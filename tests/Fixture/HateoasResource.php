<?php declare(strict_types=1);

namespace Stratadox\RestResource\Test\Fixture;

use Stratadox\RestResource\Links;
use Stratadox\RestResource\Resource;

final class HateoasResource implements Resource
{
    /** @var array */
    private $body;
    /** @var Links */
    private $links;

    public function __construct(array $body, Links $links)
    {
        $this->body = $body;
        $this->links = $links;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function links(): Links
    {
        return $this->links;
    }

    public function name(): string
    {
        return 'hateoas-resource';
    }
}
