<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Instructions;

use DemosEurope\DocumentBakery\Exceptions\DocumentGenerationException;

class InstructionFactory
{
    /**
     * @var array<string, AbstractInstruction>
     */
    private $instructions;

    /**
     * InstructionFactory constructor.
     * @param \Traversable|iterable  $instructions !tagged_iterator document_compiler.instruction
     *
     */
    public function __construct(iterable $instructions)
    {
        $this->instructions = iterator_to_array($instructions);
    }

    /**
     * @throws DocumentGenerationException
     */
    public function lookupForName(string $name): AbstractInstruction
    {
        if (array_key_exists($name, $this->instructions)) {
            return $this->instructions[$name];
        }

        throw DocumentGenerationException::instructionNotFound($name);
    }
}
