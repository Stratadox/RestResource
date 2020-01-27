<?php declare(strict_types=1);

namespace Stratadox\RestResource;

use ICanBoogie\Inflector;
use function strtolower;

final class BoogieSingularizer implements Singularizer
{
    /** @var Inflector[] */
    private $inflectors;

    private const LANGUAGES = [
        'english' => 'en',
        'french' => 'fr',
        'norwegian bokmal' => 'nb',
        'bokmal' => 'nb',
        'portuguese' => 'pt',
        'spanish' => 'es',
        'turkish' => 'tr',
    ];

    public function __construct(Inflector ...$inflectors)
    {
        $this->inflectors = $inflectors;
    }

    public static function default(): Singularizer
    {
        return new self(Inflector::get('en'));
    }

    public static function in(string $locale): Singularizer
    {
        return new self(
            Inflector::get(
                self::LANGUAGES[strtolower($locale)] ?? strtolower($locale)
            ),
            Inflector::get('en')
        );
    }

    public function convert(string $word): string
    {
        foreach ($this->inflectors as $inflector) {
            $singular = $inflector->singularize($word);
            if ($singular !== $word) {
                return $singular;
            }
        }
        return $word;
    }
}
