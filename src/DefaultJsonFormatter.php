<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use Throwable;
use function array_walk_recursive;
use function is_string;
use function json_encode;
use function json_last_error_msg;

final class DefaultJsonFormatter implements ResourceFormatter
{
    use LinkRetrieval;

    /** @var string */
    private $baseUri;

    private function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public static function fromBaseUri(string $baseUri): ResourceFormatter
    {
        return new self($baseUri);
    }

    public function from(RestResource $resource): string
    {
        try {
            $result = json_encode($this->prepare($resource));
        } catch (Throwable $exception) {
            throw CannotFormatJson::because($resource, $exception);
        }
        if (!is_string($result)) {
            throw CannotFormatJson::jsonError($resource, json_last_error_msg());
        }
        return $result;
    }

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
