<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test\Fixture;

use BadMethodCallException;
use JsonSerializable;

final class NoJson implements JsonSerializable
{
    public function jsonSerialize()
    {
        throw new BadMethodCallException('No JSON!');
    }
}
