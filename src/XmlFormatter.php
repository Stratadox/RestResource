<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use SimpleXMLElement;
use Throwable;
use function array_walk_recursive;
use function current;
use function sprintf;

abstract class XmlFormatter implements ResourceFormatter
{
    use LinkRetrieval;

    /** @var string */
    private $baseUri;
    /** @var Singularizer */
    protected $singularizer;

    private function __construct(string $baseUri, Singularizer $singularizer = null)
    {
        $this->baseUri = $baseUri;
        $this->singularizer = $singularizer ?: BoogieSingularizer::default();
    }

    public static function fromBaseUri(string $baseUri): ResourceFormatter
    {
        return new static($baseUri, BoogieSingularizer::default());
    }

    public static function in(
        string $locale,
        string $baseUri
    ): ResourceFormatter {
        return new static($baseUri, BoogieSingularizer::in($locale));
    }

    public static function withSingularizer(
        string $baseUri,
        Singularizer $singularizer
    ): ResourceFormatter {
        return new static($baseUri, $singularizer);
    }

    public function from(RestResource $resource): string
    {
        $xml = new SimpleXMLElement(sprintf(
            '<?xml version="1.0"?><%s />',
            $resource->name()
        ));
        try {
            $this->toSimpleXML(
                current($this->prepare($resource)),
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

    private function prepare(RestResource $resource): array
    {
        return [$resource->name() =>
            $this->flatten($resource->body()) +
            $this->linksOf($resource, $this->baseUri)
        ];
    }

    private function flatten(array $body): array
    {
        array_walk_recursive($body, function (&$data) {
            if ($data instanceof RestResource) {
                $data = $this->prepare($data);
            }
        });
        return $body;
    }
}
