<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use InvalidArgumentException;
use Throwable;
use function sprintf;

final class CannotFormatXml extends InvalidArgumentException implements Unformattable
{
    public static function because(Resource $resource, Throwable $reason): self
    {
        return new self(sprintf(
            'Could not format the resource `%s` as xml, because: %s',
            $resource->name(),
            $reason->getMessage()
        ), (int) $reason->getCode(), $reason);
    }
}
