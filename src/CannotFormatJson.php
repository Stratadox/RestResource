<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use Nette\InvalidArgumentException;
use Throwable;
use function sprintf;

final class CannotFormatJson extends InvalidArgumentException implements Unformattable
{
    public static function because(RestResource $resource, Throwable $reason): self
    {
        return new self(sprintf(
            'Could not format the resource `%s` as json, because: %s',
            $resource->name(),
            $reason->getMessage()
        ), $reason->getCode(), $reason);
    }

    public static function jsonError(RestResource $resource, string $error): self
    {
        return new self(sprintf(
            'Could not format the resource `%s` as json, because: %s',
            $resource->name(),
            $error
        ));
    }
}
