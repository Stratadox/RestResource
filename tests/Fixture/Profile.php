<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test\Fixture;

use Stratadox\RestResource\RelationshipType;

final class Profile implements RelationshipType
{
    public static function view(): self
    {
        return new self();
    }

    public function __toString(): string
    {
        return 'who';
    }

    public function isReadOperation(): bool
    {
        return true;
    }
}
