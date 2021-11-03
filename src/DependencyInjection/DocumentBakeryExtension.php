<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\DependencyInjection;


use DemosEurope\DocumentBakery\ElementFactory;
use DemosEurope\DocumentBakery\Elements\ElementInterface;
use DemosEurope\DocumentBakery\Exporter;
use DemosEurope\DocumentBakery\TemporaryStuff\EntityFetcher;
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
        $this->processConfiguration($configuration, $configs);

        // TODO: use configuration if there are actual things to configure
        //       see: $this->processedConfigurations

        $this->yamlFileLoader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $this->registerEdt($container);

        $this->registerDefinitions($container);
    }

    private function registerDefinitions(ContainerBuilder $containerBuilder): void
    {
        $exporterDefinition = new Definition(Exporter::class);
        $exporterDefinition->setAutowired(true);
        $exporterDefinition->setAutoconfigured(true);

        $containerBuilder->addDefinitions([Exporter::class => $exporterDefinition]);

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

        $elementFactoryDefinition = new Definition(ElementFactory::class);
        $elementFactoryDefinition->setAutowired(true);
        $elementFactoryDefinition->setAutoconfigured(true);
        $elementFactoryDefinition->setArgument(
            '$elements',
            new TaggedIteratorArgument(
                'document_compiler.element',
            null,
            'getName')
        );

        $containerBuilder->addDefinitions([ElementFactory::class => $elementFactoryDefinition]);

        // FIXME: This doesn't want to stay here, it's just a house "guest"
        $entityFetcherDefinition = new Definition(EntityFetcher::class);
        $entityFetcherDefinition->setAutowired(true);
        $entityFetcherDefinition->setAutoconfigured(true);

        $containerBuilder->addDefinitions([EntityFetcher::class => $entityFetcherDefinition]);

        $twigRendererDefinition = new Definition(TwigRenderer::class);
        $twigRendererDefinition->setAutowired(true);
        $twigRendererDefinition->setAutoconfigured(true);

        $containerBuilder->addDefinitions([TwigRenderer::class => $twigRendererDefinition]);
    }

    private function registerEdt(ContainerBuilder $container)
    {
        $this->yamlFileLoader->load('services_edt.yml');
    }
}
