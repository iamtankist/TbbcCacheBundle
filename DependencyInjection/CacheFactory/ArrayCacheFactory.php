<?php
/**
 * This file is part of TbbcCacheBundle
 *
 * (c) TheBigBrainsCompany <contact@thebigbrainscompany.com>
 *
 */

namespace Tbbc\CacheBundle\DependencyInjection\CacheFactory;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Factory for Array Cache
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class ArrayCacheFactory implements CacheFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {

        $doctrineArrayId = sprintf('tbbc_cache.doctrine_cache.%s_array_instance', $config['name']);
        $container
            ->setDefinition($doctrineArrayId, new DefinitionDecorator('tbbc_cache.doctrine_cache.array'))
            ->setPublic(false)
        ;

        $container
            ->setDefinition($id, new DefinitionDecorator('tbbc_cache.cache.doctrine_proxy'))
            ->addArgument($config['name'])
            ->addArgument(new Reference($doctrineArrayId))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'array';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeBuilder $node)
    {
        // Array cache doesn't require any configuration to be set
    }
}
