<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;

class InstructionFactory
{
    /**
     * @var array<string, AbstractInstruction>
     */
    private $elements = [];

    /**
     * InstructionFactory constructor.
     * @param \Traversable|iterable  $elements !tagged_iterator document_compiler.element
     *
     */
    public function __construct(iterable $elements)
    {
        $this->elements = iterator_to_array($elements);
    }

    /**
     * @throws DocumentGenerationException
     */
    public function lookupForName(string $name): AbstractInstruction
    {
        if (array_key_exists($name, $this->elements)) {
            return $this->elements[$name];
        }

        throw DocumentGenerationException::elementNotFound($name);
    }
}
