<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use SimpleXMLElement;
use Throwable;
use function sprintf;

abstract class XmlFormatter implements ResourceFormatter
{
    use LinkRetrieval;

    /** @var string */
    private $baseUri;
    /** @var Singularizer */
    protected $singularizer;

    public function __construct(string $baseUri, Singularizer $singularizer = null)
    {
        $this->baseUri = $baseUri;
        $this->singularizer = $singularizer ?: BoogieSingularizer::default();
    }

    public static function in(
        string $locale,
        string $baseUri
    ): ResourceFormatter {
        return new static($baseUri, BoogieSingularizer::in($locale));
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

    // @todo inject serializer instead
    abstract protected function toSimpleXML(
        array $input,
        SimpleXMLElement $parent,
        bool $alreadySingularized = false
    ): void;
}
