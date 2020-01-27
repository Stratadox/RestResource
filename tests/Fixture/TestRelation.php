<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test\Fixture;

use Stratadox\RestResource\RelationshipType;

final class TestRelation implements RelationshipType
{
    private $name;
    private $read;

    public function __construct(string $name, bool $read = true)
    {
        $this->name = $name;
        $this->read = $read;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function isReadOperation(): bool
    {
        return $this->read;
    }
}
