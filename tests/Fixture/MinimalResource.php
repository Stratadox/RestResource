<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test\Fixture;

use Stratadox\RestResource\Links;
use Stratadox\RestResource\Resource;

final class MinimalResource implements Resource
{
    /** @var array */
    private $body;

    public function __construct(array $body)
    {
        $this->body = $body;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function links(): Links
    {
        return Links::none();
    }

    public function name(): string
    {
        return 'minimal-resource';
    }
}
