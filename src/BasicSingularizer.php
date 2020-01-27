<?php declare(strict_types=1);

namespace Stratadox\RestResource;

final class BasicSingularizer implements Singularizer
{
    public function convert(string $word): string
    {
        return $word === 'links' ? 'link' : 'item';
    }
}
