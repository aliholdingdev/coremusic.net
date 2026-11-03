<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Cache;

use CoreMusic\Cache\FileCacheAdapter;
use PHPUnit\Framework\TestCase;

final class FileCacheAdapterTest extends TestCase
{
    private string $tempDir;
    private FileCacheAdapter $adapter;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cm_cache_test_' . bin2hex(random_bytes(6));
        $this->adapter = new FileCacheAdapter($this->tempDir, 3600);
    }

    protected function tearDown(): void
    {
        $this->adapter->clear();
        @rmdir($this->tempDir);
    }

    public function testBasicGetSetHasDelete(): void
    {
        $this->assertTrue($this->adapter->set('test_key', ['foo' => 'bar']));
        $this->assertTrue($this->adapter->has('test_key'));
        $this->assertSame(['foo' => 'bar'], $this->adapter->get('test_key'));

        $this->assertTrue($this->adapter->delete('test_key'));
        $this->assertFalse($this->adapter->has('test_key'));
        $this->assertFalse($this->adapter->get('test_key'));
    }

    public function testMissReturnsFalse(): void
    {
        $this->assertFalse($this->adapter->get('non_existent_key_12345'));
    }

    public function testTtlExpiration(): void
    {
        // 1 saniyelik TTL
        $this->adapter->set('short_lived', 'alive', 1);
        $this->assertSame('alive', $this->adapter->get('short_lived'));

        // 1.5 saniye bekle (tam saniye sınırını geçmesi için)
        usleep(1500000);

        $this->assertFalse($this->adapter->get('short_lived'), 'TTL dolduktan sonra false dönmeli');
        $this->assertFalse($this->adapter->has('short_lived'));
    }

    public function testAtomicIncrement(): void
    {
        $this->assertSame(1, $this->adapter->increment('counter', 1));
        $this->assertSame(6, $this->adapter->increment('counter', 5));
        $this->assertSame(6, $this->adapter->get('counter'));
    }

    public function testClearRemovesAllEntries(): void
    {
        $this->adapter->set('key1', 'val1');
        $this->adapter->set('key2', 'val2');

        $this->assertTrue($this->adapter->clear());
        $this->assertFalse($this->adapter->has('key1'));
        $this->assertFalse($this->adapter->has('key2'));
    }

    public function testCorruptedFileHandledGracefully(): void
    {
        $this->adapter->set('corrupt_key', 'valid_data');
        $hash = hash('xxh64', 'corrupt_key');
        $sub1 = substr($hash, 0, 2);
        $filePath = $this->tempDir . DIRECTORY_SEPARATOR . $sub1 . DIRECTORY_SEPARATOR . $hash . '.cache';

        $this->assertFileExists($filePath);

        // Dosya içeriğini boz
        file_put_contents($filePath, '{not valid json');

        // Graceful fallback: false dönmeli ve bozuk dosya temizlenmeli
        $this->assertFalse($this->adapter->get('corrupt_key'));
        $this->assertFileDoesNotExist($filePath);
    }

    public function testDirectoryPartitioningCreated(): void
    {
        $this->adapter->set('partitioned_key', 'data');
        $hash = hash('xxh64', 'partitioned_key');
        $sub1 = substr($hash, 0, 2);
        $subDir = $this->tempDir . DIRECTORY_SEPARATOR . $sub1;

        $this->assertDirectoryExists($subDir, '2 karakterlik alt dizin bölümlemesi oluşmalı');
    }
}
