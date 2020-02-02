<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use function array_map;
use function count;

trait LinkRetrieval
{
    private function linksOf(Resource $resource, string $baseUri): array
    {
        if (!count($resource->links())) {
            return [];
        }
        return [
            'links' => array_map(
                static function (Link $link) use ($baseUri): array {
                    return [
                        'href' => $baseUri . $link->path(),
                        'rel' => (string) $link->type(),
                        'type' => $link->type()->isReadOperation() ?
                            'GET' :
                            'POST',
                    ];
                },
                $resource->links()->items()
            )
        ];
    }
}
