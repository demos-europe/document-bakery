<?php

declare(strict_types=1);

namespace DemosEurope\DocumentCompiler\DependencyInjection;


use DemosEurope\DocumentCompiler\ElementFactory;
use DemosEurope\DocumentCompiler\Elements\ElementInterface;
use DemosEurope\DocumentCompiler\Exporter;
use DemosEurope\DocumentCompiler\TemporaryStuff\EntityFetcher;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DocumentCompilerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        // TODO: use configuration if there are actual things to configure
        //       see: $this->processedConfigurations

        $this->registerEdt($container);

        $this->registerDefinitions($container);
    }

    private function registerDefinitions(ContainerBuilder $containerBuilder): void
    {
        $definitions = [];

        $exporterDefinition = new Definition(Exporter::class);
        $exporterDefinition->setAutowired(true);
        $exporterDefinition->setAutoconfigured(true);

        $definitions[Exporter::class] = $exporterDefinition;

        // document_compiler.element
        $containerBuilder->registerForAutoconfiguration(ElementInterface::class)
            ->addTag('document_compiler.element');

        $elements = $containerBuilder->findTaggedServiceIds('document_compiler.element');

        $elementFactoryDefinition = new Definition(ElementFactory::class);
        $elementFactoryDefinition->setAutowired(true);
        $elementFactoryDefinition->setAutoconfigured(true);
        $elementFactoryDefinition->setArgument('$elements', $elements);

        $definitions[ElementFactory::class] = $elementFactoryDefinition;

        // FIXME: This doesn't want to stay here, it's just a house "guest"
        $entityFetcherDefinition = new Definition(EntityFetcher::class);
        $entityFetcherDefinition->setAutowired(true);
        $entityFetcherDefinition->setAutoconfigured(true);

        $definitions[EntityFetcher::class] = $entityFetcherDefinition;

        $containerBuilder->addDefinitions($definitions);
    }

    private function registerEdt(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services_edt.yml');
    }
}
