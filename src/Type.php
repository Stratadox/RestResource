<?php declare(strict_types=1);

namespace Stratadox\RestResource;

final class Type implements RelationshipType
{
    /** @var string */
    private $type;
    /** @var bool */
    private $isReadOperation;

    public function __construct(string $type, bool $isReadOperation)
    {
        $this->type = $type;
        $this->isReadOperation = $isReadOperation;
    }

    public static function get(string $type): self
    {
        return new self($type, true);
    }

    public static function post(string $type): self
    {
        return new self($type, false);
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function isReadOperation(): bool
    {
        return $this->isReadOperation;
    }
}
