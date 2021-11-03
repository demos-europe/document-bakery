<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery;

use DemosEurope\DocumentBakery\Elements\AbstractElement;
use DemosEurope\DocumentBakery\Exceptions\ExportGenerationException;

class ElementFactory
{
    /**
     * @var array<string, AbstractElement>
     */
    private $elements = [];

    /**
     * ElementFactory constructor.
     * @param \Traversable|iterable  $elements !tagged_iterator document_compiler.element
     *
     */
    public function __construct(iterable $elements)
    {
        $this->elements = iterator_to_array($elements);
    }

    /**
     * @throws ExportGenerationException
     */
    public function lookupForName(string $name): AbstractElement
    {
        if (array_key_exists($name, $this->elements)) {
            return $this->elements[$name];
        }

        throw ExportGenerationException::elementNotFound($name);
    }
}
