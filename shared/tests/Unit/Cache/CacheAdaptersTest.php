<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Cache;

use PHPUnit\Framework\TestCase;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Cache\PageCacheAdapter;
use CoreMusic\Cache\ApcuAdapter;

final class CacheAdaptersTest extends TestCase
{
    public function testApcuTtlZeroOrNegativeFallsBackToDefault(): void
    {
        $this->assertSame(ApcuAdapter::DEFAULT_TTL, ApcuAdapter::normalizeTtl(0));
        $this->assertSame(ApcuAdapter::DEFAULT_TTL, ApcuAdapter::normalizeTtl(-5));
        $this->assertSame(3600, ApcuAdapter::DEFAULT_TTL);
    }

    public function testApcuPositiveTtlIsPreserved(): void
    {
        $this->assertSame(600, ApcuAdapter::normalizeTtl(600));
        $this->assertSame(1, ApcuAdapter::normalizeTtl(1));
    }

    public function testMemoryAdapterSetGetHasDelete(): void
    {
        $cache = new MemoryAdapter();

        $this->assertTrue($cache->set('k', 'v'));
        $this->assertSame('v', $cache->get('k'));
        $this->assertTrue($cache->has('k'));

        $cache->delete('k');
        $this->assertFalse($cache->has('k'));
        $this->assertFalse($cache->get('k'));
    }

    public function testMemoryAdapterMissReturnsFalse(): void
    {
        $this->assertFalse((new MemoryAdapter())->get('absent'));
    }

    public function testMemoryAdapterIncrementStartsFromStoredInteger(): void
    {
        $cache = new MemoryAdapter();
        $cache->set('counter', 5);

        $this->assertSame(7, $cache->increment('counter', 2));
    }

    public function testMemoryAdapterClearRemovesEverything(): void
    {
        $cache = new MemoryAdapter();
        $cache->set('a', 1);
        $cache->set('b', 2);

        $cache->clear();

        $this->assertFalse($cache->has('a'));
        $this->assertFalse($cache->has('b'));
    }

    public function testPageCacheKeyDiffersByAuthentication(): void
    {
        $page = new PageCacheAdapter(new MemoryAdapter());

        $this->assertNotSame(
            $page->pageKey('/home', false),
            $page->pageKey('/home', true),
            'Kimlikli ve anonim sayfa aynı cache anahtarını paylaşmamalı (A01).'
        );
    }

    public function testPageCacheRememberCallsCallbackOnlyOnMiss(): void
    {
        $page = new PageCacheAdapter(new MemoryAdapter(), true, 60);
        $calls = 0;
        $callback = static function () use (&$calls): string {
            $calls++;
            return 'html';
        };

        $key = $page->pageKey('/home', false);
        $this->assertSame('html', $page->remember($key, 60, $callback));
        $this->assertSame('html', $page->remember($key, 60, $callback));
        $this->assertSame(1, $calls);
    }

    public function testPageCacheExposesConfiguration(): void
    {
        $page = new PageCacheAdapter(new MemoryAdapter(), false, 120);

        $this->assertFalse($page->isPageCacheEnabled());
        $this->assertSame(120, $page->ttlPage());
    }
}
