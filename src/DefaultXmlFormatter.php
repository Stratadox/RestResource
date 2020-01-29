<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use SimpleXMLElement;
use function htmlspecialchars;
use function is_array;
use function is_numeric;
use function str_replace;
use const ENT_XML1;

final class DefaultXmlFormatter extends XmlFormatter
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
            } else {
                $parent->addChild(
                    $name,
                    htmlspecialchars((string) $value, ENT_XML1)
                );
            }

        }
    }
}
