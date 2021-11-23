<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Styles;

use DemosEurope\DocumentBakery\Exceptions\StyleException;
use Traversable;

class StylesRepository
{
    /**
     * @var iterable|array<string,StylesLoaderInterface>
     */
    private iterable $loaders;

    /**
     * @var array<string, array>
     */
    private $styleNameCache;

    /**
     * @param iterable|Traversable|StylesLoaderInterface[] $loaders
     */
    public function __construct(iterable $loaders)
    {
        $this->loaders = iterator_to_array($loaders);
        $this->styleNameCache = [];
    }

    public function has(string $styleName): bool
    {
        $this->buildStyleNameCacheIfRequired();

        return array_key_exists($styleName, $this->styleNameCache);
    }

    /**
     * @throws StyleException
     */
    public function get(string $styleName): array
    {
        if (!$this->has($styleName)) {
            throw StyleException::styleNotFound($styleName);
        }

        return $this->styleNameCache[$styleName];
    }

    public function mergeStyles(array $recipeStyles): void
    {
        $this->buildStyleNameCacheIfRequired();

        $this->styleNameCache = array_replace_recursive($this->styleNameCache, $recipeStyles);
    }

    private function buildStyleNameCacheIfRequired(): void
    {
        if (0 === count($this->styleNameCache)) {
            $this->buildStyleNameCache();
        }
    }

    private function buildStyleNameCache(): void
    {
        foreach ($this->loaders as $loader) {
            $availableStyles = $loader->availableStyles();

            foreach ($availableStyles as $styleName => $style) {
                if (array_key_exists($styleName, $this->styleNameCache)) {
                    StyleException::duplicateStyleFound($styleName);
                }

                $this->styleNameCache[$styleName] = $style;
            }
        }
    }
}
