<?php declare(strict_types=1);

namespace Stratadox\RestResource;

final class BasicResource implements Resource
{
    /** @var string */
    private $name;
    /** @var array */
    private $body;
    /** @var Links */
    private $links;

    public function __construct(string $name, array $body, Links $links)
    {
        $this->name = $name;
        $this->body = $body;
        $this->links = $links;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function links(): Links
    {
        return $this->links;
    }
}
