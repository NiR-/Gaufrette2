<?php

namespace features\Context;

use Behat\Testwork\ServiceContainer;
use Symfony\Component\DependencyInjection\Definition;
use Behat\Testwork\Exception\ServiceContainer\ExceptionExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
use features\Context\Initializer\Filesystem;

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
        $definition = new Definition(Filesystem::class);
        $definition->addTag(ContextExtension::ARGUMENT_RESOLVER_TAG);
        $container->setDefinition('gaufrette.context.argument_resolver.filesystem', $definition);
    }
}
