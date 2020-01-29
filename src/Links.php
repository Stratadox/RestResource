<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use Stratadox\ImmutableCollection\ImmutableCollection;

final class Links extends ImmutableCollection
{
    private function __construct(Link ...$links)
    {
        parent::__construct(...$links);
    }

    public static function none(): Links
    {
        return new self();
    }

    public static function provide(Link ...$links): Links
    {
        return new self(...$links);
    }

    /** @codeCoverageIgnore used for type safety and IDE auto completion */
    public function current(): Link
    {
        return parent::current();
    }
}
