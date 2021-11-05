<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\DependencyInjection;


use DemosEurope\DocumentBakery\Elements\ElementFactory;
use DemosEurope\DocumentBakery\Elements\ElementInterface;
use DemosEurope\DocumentBakery\Exporter;
use DemosEurope\DocumentBakery\Recipes\ConfigRecipeLoader;
use DemosEurope\DocumentBakery\Recipes\RecipeConfigTreeBuilder;
use DemosEurope\DocumentBakery\Recipes\RecipeLoaderInterface;
use DemosEurope\DocumentBakery\Recipes\RecipeRepository;
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
            Exporter::class,
            RecipeConfigTreeBuilder::class,
            TwigRenderer::class
        ]);

        // document_compiler.element
        $elementsDefaults = new Definition();
        $elementsDefaults->setAutoconfigured(true);
        $elementsDefaults->setAutowired(true);
        $elementsDefaults->addTag('document_compiler.element');

        $containerBuilder->registerForAutoconfiguration(ElementInterface::class);

        $this->yamlFileLoader->registerClasses(
            $elementsDefaults,
            'DemosEurope\\DocumentBakery\\Elements\\',
            __DIR__.'/../Elements'
        );

        $elementFactoryDefinition = $this->addSimpleDefinition($containerBuilder, ElementFactory::class);
        $elementFactoryDefinition->setArgument(
            '$elements',
            new TaggedIteratorArgument(
                'document_compiler.element',
            null,
            'getName')
        );

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

    private function registerEdt(ContainerBuilder $container)
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
