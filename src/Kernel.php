<?php

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader as RoutingYamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
class Kernel
{
    private ContainerBuilder $container;

    public function __construct()
    {
        $this->container = new ContainerBuilder();
        $this->initializeContainer();
    }

    private function initializeContainer(): void
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');
        $this->container->compile();
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    public function loadRoutes(): RouteCollection
    {
        $loader = new RoutingYamlFileLoader(new FileLocator(__DIR__ . '/../config'));
        return $loader->load('routes.yaml');
    }
}