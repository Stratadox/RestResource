<?php declare(strict_types=1);

namespace Stratadox\RestResource;

interface ResourceFormatter
{
    /**
     * Formats a response based on a rest resource.
     *
     * @param RestResource $resource The resource to format.
     * @return string                The presentable output.
     * @throws Unformattable         When the resource cannot be formatted.
     */
    public function from(RestResource $resource): string;
}
