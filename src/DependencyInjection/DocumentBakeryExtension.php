<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\DependencyInjection;


use DemosEurope\DocumentBakery\Instructions\InstructionFactory;
use DemosEurope\DocumentBakery\Instructions\InstructionInterface;
use DemosEurope\DocumentBakery\Bakery;
use DemosEurope\DocumentBakery\Recipes\ConfigRecipeLoader;
use DemosEurope\DocumentBakery\Recipes\RecipeConfigTreeBuilder;
use DemosEurope\DocumentBakery\Recipes\RecipeLoaderInterface;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
use DemosEurope\DocumentBakery\Styles\ConfigStylesLoader;
use DemosEurope\DocumentBakery\Styles\StylesLoaderInterface;
use DemosEurope\DocumentBakery\Styles\StylesRepository;
use DemosEurope\DocumentBakery\TwigRenderer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DocumentBakeryExtension extends Extension
{
    private $yamlFileLoader;

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfiguration = $this->processConfiguration($configuration, $configs);

        $this->yamlFileLoader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $this->registerEdt($container);

        $this->registerDefinitions($container, $processedConfiguration);
    }

    private function registerDefinitions(ContainerBuilder $containerBuilder, array $configuration): void
    {
        \array_map(function (string $className) use ($containerBuilder) {
            $this->addSimpleDefinition($containerBuilder, $className);
        }, [
            Bakery::class,
            RecipeConfigTreeBuilder::class,
            TwigRenderer::class
        ]);

        // document_compiler.instruction
        $instructionsDefaults = new Definition();
        $instructionsDefaults->setAutoconfigured(true);
        $instructionsDefaults->setAutowired(true);
        $instructionsDefaults->addTag('document_compiler.instruction');

        $containerBuilder->registerForAutoconfiguration(InstructionInterface::class);

        $this->yamlFileLoader->registerClasses(
            $instructionsDefaults,
            'DemosEurope\\DocumentBakery\\Instructions\\',
            __DIR__ . '/../Instructions'
        );

        $instructionFactoryDefinition = $this->addSimpleDefinition($containerBuilder, InstructionFactory::class);
        $instructionFactoryDefinition->setArgument(
            '$instructions',
            new TaggedIteratorArgument(
                'document_compiler.instruction',
            null,
            'getName')
        );

        $this->registerRecipes($containerBuilder, $configuration);
        $this->registerStyles($containerBuilder, $configuration);
    }

    private function registerRecipes(ContainerBuilder $containerBuilder, array $configuration): void
    {
        $containerBuilder->registerForAutoconfiguration(RecipeLoaderInterface::class)
            ->addTag('document_compiler.recipe_loader');

        $recipeRepositoryConfig = $this->addSimpleDefinition($containerBuilder, RecipeRepository::class);
        $recipeRepositoryConfig->setArgument(
            '$loaders',
            new TaggedIteratorArgument(
                'document_compiler.recipe_loader',
                null,
                'getName'
            )
        );

        $configRecipeLoader = $this->addSimpleDefinition($containerBuilder, ConfigRecipeLoader::class);
        $configRecipeLoader->setArgument('$recipes', $configuration['recipes']);
    }

    private function registerStyles(ContainerBuilder $containerBuilder, array $configuration): void
    {
        $containerBuilder->registerForAutoconfiguration(StylesLoaderInterface::class)
            ->addTag('document_compiler.style_loader');

        $stylesRepositoryConfig = $this->addSimpleDefinition($containerBuilder, StylesRepository::class);
        $stylesRepositoryConfig->setArgument(
            '$loaders',
            new TaggedIteratorArgument(
                'document_compiler.style_loader',
                null,
                'getName'
            )
        );

        $configStylesLoader = $this->addSimpleDefinition($containerBuilder, ConfigStylesLoader::class);
        $configStylesLoader->setArgument('$styles', $configuration['styles']);
    }

    private function registerEdt(ContainerBuilder $container): void
    {
        $this->yamlFileLoader->load('services_edt.yml');
    }

    private function addSimpleDefinition(ContainerBuilder $containerBuilder, string $className): Definition
    {
        $definition = new Definition($className);
        $definition->setAutowired(true);
        $definition->setAutoconfigured(true);

        $containerBuilder->addDefinitions([$className => $definition]);

        return $definition;
    }
}
