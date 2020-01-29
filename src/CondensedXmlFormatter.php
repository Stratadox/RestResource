<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use SimpleXMLElement;
use function is_array;
use function is_numeric;
use function str_replace;

final class CondensedXmlFormatter extends XmlFormatter
{
    protected function toSimpleXML(
        array $input,
        SimpleXMLElement $parent,
        bool $alreadySingularized = false
    ): void {
        foreach ($input as $key => $value) {

            if (is_numeric($key)) {
                $name = $alreadySingularized ?
                    'item' : $this->singularizer->convert($parent->getName());
                $singularized = true;
            } else {
                $name = str_replace(['<', '>'], '', (string) $key);
                $singularized = false;
            }

            if (is_array($value)) {
                $node = $parent->addChild($name);
                $this->toSimpleXML($value, $node, $singularized);
            } elseif ($singularized) {
                $child = $parent->addChild($name);
                $child->addAttribute('value', (string) $value);
            } else {
                $parent->addAttribute($name, (string) $value);
            }

        }
    }
}
