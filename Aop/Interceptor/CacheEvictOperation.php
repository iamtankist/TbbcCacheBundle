<?php
/**
 * This file is part of TbbcCacheBundle
 *
 * (c) TheBigBrainsCompany <contact@thebigbrainscompany.com>
 *
 */

namespace Tbbc\CacheBundle\Aop\Interceptor;

use CG\Proxy\MethodInvocation;
use Tbbc\CacheBundle\Exception\InvalidArgumentException;
use Tbbc\CacheBundle\Metadata\CacheEvictMethodMetadata;
use Tbbc\CacheBundle\Metadata\CacheMethodMetadataInterface;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class CacheEvictOperation extends AbstractCacheOperation
{
    public function handle(CacheMethodMetadataInterface $methodMetadata, MethodInvocation $methodInvocation)
    {
        if (!$methodMetadata instanceof CacheEvictMethodMetadata) {
            throw new InvalidArgumentException(sprintf('%s does only support "CacheEvictMethodMetadata" objects', __CLASS__ ));
        }

        $this->cacheOperationContext->setTargetClass($methodMetadata->class);
        $this->cacheOperationContext->setTargetMethod($methodMetadata->name);
        $this->cacheOperationContext->setCaches($methodMetadata->caches);
        // TODO: Set before invocation on operation context
        // $this->cacheOperationContext->setBeforeInvocation($methodMetadata->beforeInvocation)

        $returnValue = $methodInvocation->proceed();

        if ($methodMetadata->allEntries) {
            $this->cacheOperationContext->setFlush(true);
            foreach($methodMetadata->caches as $cacheName) {
                $this->getCacheManager()->getCache($cacheName)->flush();
            }
        } else {
            $cacheKey = $this->generateCacheKey($methodMetadata, $methodInvocation);
            $this->cacheOperationContext->setKey($cacheKey);

            foreach($methodMetadata->caches as $cacheName) {
                $this->getCacheManager()->getCache($cacheName)->delete($cacheKey);
            }
        }

        return $returnValue;
    }

    public function getOperationName()
    {
        return 'cache_evict';
    }
}
