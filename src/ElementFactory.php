<?php

declare(strict_types=1);

namespace DemosInternational\DocumentCompiler;

use DemosInternational\DocumentCompiler\Elements\AbstractElement;
use DemosInternational\DocumentCompiler\Exceptions\ExportGenerationException;

class ElementFactory
{
    /**
     * @var array<string, AbstractElement>
     */
    private $elements;

    /**
     * ElementFactory constructor.
     * @param iterable  $elements !tagged_iterator document_compiler.element
     *
     */
    public function __construct(iterable $elements)
    {
        foreach ($elements as $element) {
            $this->elements[$element->getName()] = $element;
        }
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
