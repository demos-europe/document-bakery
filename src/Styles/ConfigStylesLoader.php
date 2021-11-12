<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Styles;

/**
 * Load styles provided in the `styles` configuration key if this is
 * used as a Symfony Bundle.
 */
class ConfigStylesLoader implements StylesLoaderInterface
{
    /**
     * @var array<string, array>
     */
    private $styles;

    public function __construct(array $styles)
    {
        $this->styles = $styles;
    }

    public static function getName(): string
    {
        return self::class;
    }

    public function load(string $recipeName): array
    {
        return $this->styles[$recipeName];
    }

    public function availableStyles(): array
    {
        return $this->styles;
    }
}
