<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use Throwable;
use function is_string;
use function json_encode;
use function json_last_error_msg;

final class DefaultJsonFormatter implements ResourceFormatter
{
    use LinkRetrieval;

    /** @var string */
    private $baseUri;

    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public function from(Resource $resource): string
    {
        try {
            $result = json_encode([
                $resource->name() => $resource->body() + $this->linksOf(
                    $resource,
                    $this->baseUri
                )
            ]);
        } catch (Throwable $exception) {
            throw CannotFormatJson::because($resource, $exception);
        }
        if (!is_string($result)) {
            throw CannotFormatJson::jsonError($resource, json_last_error_msg());
        }
        return $result;
    }
}
