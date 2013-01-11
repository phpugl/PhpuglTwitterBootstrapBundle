<?php

namespace Phpugl\TwitterBootstrapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PhpuglTwitterBootstrapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (isset($config['config'])) {
            $container->setParameter('phpugl_twitter_bootstrap.config', $config['config']);
        }

        if (isset($config['less'])) {
            $container->setParameter('phpugl_twitter_bootstrap.less', $config['less']);
        }

        if (isset($config['images'])) {
            $container->setParameter('phpugl_twitter_bootstrap.images', $config['images']);
        }

        if (isset($config['javascript'])) {
            $container->setParameter('phpugl_twitter_bootstrap.javascript', $config['javascript']);
        }
    }
}
