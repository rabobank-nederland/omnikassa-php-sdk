<?php

declare(strict_types=1);

namespace OmniKassa\ExampleIntegration\Service\Provider;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\TokenProvider;
use OmniKassa\ExampleIntegration\Service\Provider\Contract\TokenProviderInterface;
use Psr\Cache\CacheItemPoolInterface;

final class CacheTokenProvider extends TokenProvider implements TokenProviderInterface
{
    public function __construct(private CacheItemPoolInterface $cacheItemPool, string $refreshToken)
    {
        $this->setValue(self::REFRESH_TOKEN, $refreshToken);
    }

    protected function getValue($key): mixed
    {
        $item = $this->cacheItemPool->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        return null;
    }

    protected function setValue($key, $value)
    {
        $item = $this->cacheItemPool->getItem($key);

        $item->set($value);
        $item->expiresAfter(3600);

        $this->cacheItemPool->save($item);
    }

    protected function flush()
    {
        // not needed for this cache implementation.
    }
}
