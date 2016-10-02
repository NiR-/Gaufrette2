<?php

namespace features\Context;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Gaufrette\Filesystem\AwsS3\Behat\FeatureContextResolver as AwsS3;
use Gaufrette\Filesystem\Local\Behat\FeatureContextResolver as Local;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Extension implements ServiceContainer\Extension
{
    public function getConfigKey()
    {
        return 'gaufrette';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $classes = [
            Local::class,
            AwsS3::class,
        ];
        foreach ($classes as $fs => $class) {
            $definition = new Definition($class);
            $definition->addTag(ContextExtension::ARGUMENT_RESOLVER_TAG);
            $container->setDefinition('gaufrette.context.argument_resolver.'.$fs, $definition);
        }
    }
}
