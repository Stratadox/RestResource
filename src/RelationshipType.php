<?php declare(strict_types=1);

namespace Stratadox\RestResource;

interface RelationshipType
{
    /**
     * Retrieves the name of the type of relationship the link represents.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Checks whether this is a read operation. Might potentially be used to
     * determine whether `get` or `post` should be used.
     *
     * @return bool
     */
    public function isReadOperation(): bool;
}
