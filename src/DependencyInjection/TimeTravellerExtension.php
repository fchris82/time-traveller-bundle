<?php

namespace Fchris82\TimeTravellerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class TimeTravellerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        foreach ($config as $key => $value) {
            $container->setParameter('time_traveller.' . $key, $value);
        }

        $this->makeServicePublicOnTest('fchris82.time_manager', $container);
    }

    private function makeServicePublicOnTest(string $serviceName, ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.environment') === 'test') {
            $container->getDefinition($serviceName)->setPublic(true);
        }
    }
}
