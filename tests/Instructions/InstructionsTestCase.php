<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\Instructions;

use DemosEurope\DocumentBakery\Data\RecipeDataBag;
use DemosEurope\DocumentBakery\Instructions\InstructionInterface;
use DemosEurope\DocumentBakery\Instructions\PhpWordInstructionInterface;
use DemosEurope\DocumentBakery\TwigRenderer;
use PHPUnit\Framework\TestCase;

abstract class InstructionsTestCase extends TestCase
{
    /**
     * @var InstructionInterface
     */
    protected $instructionUnderTest;

    /** @var array<class-string,callable> */
    private $dependencySetupMap = [];

    /** @var RecipeDataBag */
    private static $recipeDataBag;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->addSetupDependency(PhpWordInstructionInterface::class, static function (): array {
            return [
                new TwigRenderer(),
            ];
        });
    }

    /**
     * @param class-string $instructionInterface
     * @param callable $setupMethod
     * @return $this
     */
    public function addSetupDependency(string $instructionInterface, callable $setupMethod): self
    {
        $this->dependencySetupMap[$instructionInterface] = $setupMethod;

        return $this;
    }

    /**
     * @param class-string|InstructionInterface $instructionUnderTest
     */
    public function setInstructionUnderTest($instructionUnderTest): void
    {
        if ($instructionUnderTest instanceof InstructionInterface) {
            $this->instructionUnderTest = $instructionUnderTest;

            return;
        }

        $this->instructionUnderTest = $this->createInstructionInstance($instructionUnderTest);
    }

    public function prepareInstruction(array $instruction, $recipeDataBag, $mappedStyles): void {
        //$this->resetRecipeDataBag();

        //$mappedStyles = [];
        $this->instructionUnderTest->initializeInstruction($instruction, $recipeDataBag, $mappedStyles);
    }

    /**
     * @param class-string $instructionUnderTest
     * @return InstructionInterface
     */
    private function createInstructionInstance(string $instructionUnderTest): InstructionInterface
    {
        $classReflection = new \ReflectionClass($instructionUnderTest);
        $dependencies = [];

        $instructionInterface = $this->canSetupDependencies($classReflection->getInterfaceNames());
        if (null !== $instructionInterface) {
            $dependencies = $this->setupDependencies($instructionInterface);
        }

        /** @var InstructionInterface $instance */
        $instance = $classReflection->newInstanceArgs($dependencies);

        return $instance;
    }

    private function canSetupDependencies(array $interfaces): ?string
    {
        foreach ($interfaces as $interface) {
            if (array_key_exists($interface, $this->dependencySetupMap)) {
                return $interface;
            }
        }

        return null;
    }

    private function setupDependencies(string $instructionInterface): array
    {
        return $this->dependencySetupMap[$instructionInterface]();
    }

    private function resetRecipeDataBag(): void {
        self::$recipeDataBag = new RecipeDataBag();
    }

    //public static function assertCurrentRepositoryPathEquals($expected): void
    //{
    //    self::assertEquals($expected, self::$recipeDataBag->getCurrentPath());
    //}
}
