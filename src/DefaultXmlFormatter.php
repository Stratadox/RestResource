<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use SimpleXMLElement;
use Throwable;
use function htmlspecialchars;
use function is_array;
use function is_numeric;
use function sprintf;
use function str_replace;
use const ENT_XML1;

final class DefaultXmlFormatter implements ResourceFormatter
{
    use LinkRetrieval;

    /** @var string */
    private $baseUri;
    /** @var Singularizer */
    private $singularizer;

    public function __construct(string $baseUri, Singularizer $singularizer = null)
    {
        $this->baseUri = $baseUri;
        $this->singularizer = $singularizer ?: BoogieSingularizer::default();
    }

    public static function in(
        string $locale,
        string $baseUri
    ): ResourceFormatter {
        return new self($baseUri, BoogieSingularizer::in($locale));
    }

    public function from(RestResource $resource): string
    {
        $xml = new SimpleXMLElement(sprintf(
            '<?xml version="1.0"?><%s />',
            $resource->name()
        ));
        try {
            $this->toSimpleXML(
                $resource->body() + $this->linksOf($resource, $this->baseUri),
                $xml
            );
            return (string) $xml->asXML();
        } catch (Throwable $exception) {
            throw CannotFormatXml::because($resource, $exception);
        }
    }

    private function toSimpleXML(
        array $input,
        SimpleXMLElement $parent,
        bool $alreadySingularized = false
    ): void {
        foreach ($input as $key => $value) {

            if (is_numeric($key)) {
                $name = $alreadySingularized ?
                    'item' : $this->singularizer->convert($parent->getName());
                $singularized = true;
            } else {
                $name = str_replace(['<', '>'], '', (string) $key);
                $singularized = false;
            }

            if (is_array($value)) {
                $node = $parent->addChild($name);
                $this->toSimpleXML($value, $node, $singularized);
            } else {
                $parent->addChild(
                    $name,
                    htmlspecialchars((string) $value, ENT_XML1)
                );
            }

        }
    }
}
