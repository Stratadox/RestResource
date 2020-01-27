<?php declare(strict_types=1);

namespace Stratadox\RestResource;

final class Link
{
    /** @var string */
    private $path;
    /** @var RelationshipType */
    private $type;

    private function __construct(string $path, RelationshipType $type)
    {
        $this->path = $path;
        $this->type = $type;
    }

    public static function to(string $path, RelationshipType $type): self
    {
        return new self($path, $type);
    }

    /**
     * Retrieves the path this links to.
     *
     * @return string The path this link points to.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Retrieves the type of relationship this link represents.
     *
     * @return RelationshipType The relationship type.
     */
    public function type(): RelationshipType
    {
        return $this->type;
    }
}
