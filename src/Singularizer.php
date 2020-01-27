<?php declare(strict_types=1);

namespace Stratadox\RestResource;

interface Singularizer
{
    public function convert(string $word): string;
}
