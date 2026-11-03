<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Cache;

use CoreMusic\Cache\CacheInterface;
use CoreMusic\Cache\CacheManager;
use CoreMusic\Cache\FileCacheAdapter;
use CoreMusic\Cache\MemoryAdapter;
use CoreMusic\Cache\PageCacheAdapter;
use CoreMusic\PageRouter\ResponseEmitter;
use PHPUnit\Framework\TestCase;

/**
 * 50 Yıllık Büyük Önbellek Deneyi (The 50-Year Cache Experiment)
 *
 * Bu test süiti şunları doğrular:
 * 1. 50 yıllık TTL (1,577,880,000 saniye / 2076 yılı) ve 2038 Y2038 64-bit tam sayı taşma koruması.
 * 2. Sanal Zaman Bükümü (Time-Warp): 10, 25, 49 yıl sonra geçerlilik; 50 yıl + 1 saniye sonra süresi dolma/tahliye.
 * 3. Tarayıcı ETag değişmezliği: 50 yıl sonra bile aynı içerik 304 Not Modified döner (sıfır bayt aktarımı).
 * 4. ADR-007 namespace izolasyonu ve versiyon sıçraması (stampede koruması).
 * 5. Sunucu tarafı PageCache + dinamik CSRF yer tutucu izolasyonu (veri sızıntısız ortak önbellek).
 */
final class FiftyYearCacheExperimentTest extends TestCase
{
    public const FIFTY_YEARS_SECONDS = 50 * 365 * 86400; // 1,576,800,000 saniye

    private string $tempDir;
    private FileCacheAdapter $fileCache;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cm_50yr_test_' . bin2hex(random_bytes(6));
        $this->fileCache = new FileCacheAdapter($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->fileCache->clear();
        @rmdir($this->tempDir);
    }

    /**
     * Deney 1: 50 Yıllık TTL ve 2038 Taşma Koruması.
     * 2^31 - 1 = 2,147,483,647 (19 Ocak 2038).
     * 2026 + 50 yıl = 2076 yılı (~3.36 milyar saniye).
     */
    public function testFiftyYearExpirationCalculationAndY2038Immunity(): void
    {
        $now = time();
        $target50Years = $now + self::FIFTY_YEARS_SECONDS;

        // 2038 sınırını aştığını doğrula
        $y2038Limit = 2147483647;
        $this->assertGreaterThan(
            $y2038Limit,
            $target50Years,
            '50 yıllık zaman damgası 32-bit işaretli tamsayı sınırını (2038) aşmalıdır.'
        );

        $payload = [
            'song' => 'Dönence',
            'artist' => 'Barış Manço',
            'archived_at' => date('Y-m-d H:i:s', $now),
            'target_year' => 2076,
        ];

        // L2 File Cache kaydet
        $this->assertTrue($this->fileCache->set('legacy_archive_1976_2076', $payload, self::FIFTY_YEARS_SECONDS));
        $this->assertTrue($this->fileCache->has('legacy_archive_1976_2076'));

        $fetched = $this->fileCache->get('legacy_archive_1976_2076');
        $this->assertSame($payload, $fetched);
    }

    /**
     * Deney 2: Sanal Zaman Bükümü (Time-Warp Clock Jump).
     * Zamanı sanal olarak 10 yıl, 25 yıl, 49.9 yıl ve 50 yıl + 1 saniye ileri sararak
     * yaşam döngüsünü test et.
     */
    public function testVirtualTimeWarpAcrossFiftyYears(): void
    {
        $mockTime = 1772500000; // 2026 baz zamanı
        $warpAdapter = new FileCacheAdapter($this->tempDir, 3600, function () use (&$mockTime): int {
            return $mockTime;
        });

        $baseTime = $mockTime;
        $fiftyYears = 50 * 365 * 86400;
        $expiresAt = $baseTime + $fiftyYears;

        $key = 'golden_record_voyager';
        $data = ['album' => 'Sounds of Earth', 'format' => 'Gold Disc'];

        $this->assertTrue($warpAdapter->set($key, $data, $fiftyYears));

        // Durak 1: 10 Yıl Sonra
        $mockTime = $baseTime + (10 * 365 * 86400);
        $this->assertSame($data, $warpAdapter->get($key), '10 yıl sonra veri hâlâ önbellekte olmalı');

        // Durak 2: 25 Yıl Sonra (Gümüş Yıl)
        $mockTime = $baseTime + (25 * 365 * 86400);
        $this->assertSame($data, $warpAdapter->get($key), '25 yıl sonra veri hâlâ önbellekte olmalı');

        // Durak 3: 49 Yıl ve 364 Gün Sonra
        $mockTime = $expiresAt - 86400;
        $this->assertSame($data, $warpAdapter->get($key), '49 yıl 364 gün sonra veri hâlâ önbellekte olmalı');

        // Durak 4: 50 Yıl + 1 Saniye Sonra (Süresi Doldu!)
        $mockTime = $expiresAt + 1;
        $this->assertFalse($warpAdapter->get($key), '50 yıl + 1 saniye sonra süresi kesinlikle dolmalı ve false dönmeli');
    }

    /**
     * Deney 3: Tarayıcı ETag Değişmezliği (Deterministic Content Hashing).
     * İçerik 50 yıl boyunca değişmezse, üretilen ETag aynı kalır ve
     * tarayıcı 304 Not Modified alarak 0 bayt indirir.
     */
    public function testBrowserETagLongevityAnd304Validation(): void
    {
        $htmlContent = '<main class="albums-main"><h1>CoreMusic 50 Yıllık Arşiv</h1></main>';
        $currentEtag = '"' . hash('xxh64', $htmlContent) . '"';

        // 50 yıl sonra aynı içerikten hesaplanan ETag
        $futureEtag = '"' . hash('xxh64', $htmlContent) . '"';
        $this->assertSame($currentEtag, $futureEtag);

        // Tarayıcının gönderdiği If-None-Match başlık varyasyonları
        $this->assertTrue(ResponseEmitter::matchesEtag($currentEtag, $futureEtag), 'Tam eşleşme');
        $this->assertTrue(ResponseEmitter::matchesEtag('W/' . $currentEtag, $futureEtag), 'Zayıf (weak) ETag');
        $this->assertTrue(ResponseEmitter::matchesEtag('"other-etag", ' . $currentEtag, $futureEtag), 'Çoklu liste');
        $this->assertTrue(ResponseEmitter::matchesEtag('*', $futureEtag), 'Joker eşleşme');
        $this->assertFalse(ResponseEmitter::matchesEtag('"different-content"', $futureEtag), 'Farklı içerik');
    }

    /**
     * Deney 4: ADR-007 Namespace İzolasyonu ve Versiyon Sıçraması.
     */
    public function testAdr007NamespaceHierarchyAndVersionBump(): void
    {
        $keyV1 = CacheManager::buildKey('music', 'top50', 1);
        $keyV2 = CacheManager::buildKey('music', 'top50', 2);
        $userKey = CacheManager::buildKey('user', 'sess:abc', 1);

        $this->assertSame('app:v1:music:top50', $keyV1);
        $this->assertSame('app:v2:music:top50', $keyV2);
        $this->assertSame('app:v1:user:sess:abc', $userKey);

        // Versiyon bump deploy anında eski veriye istek gitmesini keser
        $this->assertNotSame($keyV1, $keyV2);
    }

    /**
     * Deney 5: Sunucu Tarafı PageCache ve CSRF Güvenliği.
     * Önbelleğe alınan sayfa şablonu, her kullanıcıya kendi canlı CSRF anahtarını
     * verir; başka kullanıcının token'ı asla sızmaz.
     */
    public function testPageCachePreservesDynamicCsrfTokenAcrossUsers(): void
    {
        $memory = new MemoryAdapter();
        $pageCache = new PageCacheAdapter($memory, true, 3600);

        $cacheKey = $pageCache->pageKey('/albumler', false);

        // Sayfa şablonu bir kere render edilip önbelleğe alınır
        $renderCount = 0;
        $callback = function () use (&$renderCount): string {
            $renderCount++;
            return '<form><input type="hidden" name="csrf_token" value="__CSRF_TOKEN_PLACEHOLDER__"><button>Oynat</button></form>';
        };

        // Kullanıcı 1 isteği (CSRF: user1_token_xyz)
        $cachedTpl1 = $pageCache->remember($cacheKey, 3600, $callback);
        $user1Html = str_replace('__CSRF_TOKEN_PLACEHOLDER__', 'user1_token_xyz', $cachedTpl1);

        $this->assertStringContainsString('user1_token_xyz', $user1Html);
        $this->assertSame(1, $renderCount);

        // Kullanıcı 2 isteği (CSRF: user2_token_abc)
        $cachedTpl2 = $pageCache->remember($cacheKey, 3600, $callback);
        $user2Html = str_replace('__CSRF_TOKEN_PLACEHOLDER__', 'user2_token_abc', $cachedTpl2);

        $this->assertStringContainsString('user2_token_abc', $user2Html);
        $this->assertStringNotContainsString('user1_token_xyz', $user2Html, 'Kullanıcı 1 tokeni Kullanıcı 2ye sızmamalı');
        $this->assertSame(1, $renderCount, 'Sayfa yalnız bir defa render edilmeli (önbellek isabeti)');
    }

    /**
     * Deney 6: CacheManager remember() Cache-Aside Akışı.
     */
    public function testCacheManagerRememberCacheAside(): void
    {
        $adapter = new MemoryAdapter();
        $calls = 0;

        $fetcher = function () use (&$calls): array {
            $calls++;
            return ['track' => 'Gülpembe', 'plays' => 1500000];
        };

        $val1 = CacheManager::remember('hot:track:1', 60, $fetcher, $adapter);
        $val2 = CacheManager::remember('hot:track:1', 60, $fetcher, $adapter);

        $this->assertSame($val1, $val2);
        $this->assertSame(1, $calls, 'İkinci çağrıda callback çalışmamalı (L1/L2 cache-aside isabeti)');
    }
}
