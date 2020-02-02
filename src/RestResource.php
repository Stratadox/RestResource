<?php declare(strict_types=1);

namespace Stratadox\RestResource;

interface RestResource
{
    /**
     * Retrieves the body or the resource.
     *
     * Retrieves a map of primitives and/or (arrays of) arrays (of arrays etc)
     * primitives. For example:
     * <code>
     * [
     *   'type' => 'card',
     *   'template_id' => '...some uuid...',
     *   'offset' => 3,
     *   'is_attacking' => false,
     *   'is_defending' => true,
     * ]
     * </code>
     * Or:
     * <code>
     * [
     *   'type' => 'errors',
     *   'messages' => ['Something went wrong...', 'Terribly wrong.'],
     * ]
     * </code>
     *
     * @return array Map of easily encodable (non-object, non-resource) values.
     */
    public function body(): array;

    /**
     * Retrieves the links to related resources.
     *
     * @return Links
     */
    public function links(): Links;

    /**
     * Retrieves the name of the resource type.
     *
     * @return string
     */
    public function name(): string;
}
