---
node_id: memory-ornekler
node_type: module
status: active
parent_id: memory
children: []
depends_on: [memory-anayasa, memory-surucu]
evidence:
  - "shared/src/AI/MemorySystem.php (kod kanıtı, 2026-10-17 okuma)"
  - ".ai/MEMORY.md gerçek kayıt satırları"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# memory-ornekler - Hafıza Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her PHP alıntısı 2026-10-17'de `shared/src/AI/` altındaki 7 dosyanın ve
> `shared/src/Contracts/AI/MemorySystemInterface.php` gerçek satırıdır (`Dosya:` / `Satır:`
> künyesi ile). `.ai/MEMORY.md` alıntıları non-ASCII=0 doğrulanmış gerçek kayıt satırlarıdır.
> İllüstratif çok-dilli bloklar repo kanıtı değildir; her birinin üstünde zorunlu etiket taşır.
> Kaynak kod salt-okunurdur (`shared/src/**` yazımı yasak).

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış) |
| Satır | okunan aralık (L25-L31 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| ASCII | `.ai/MEMORY.md` alıntıları `nonAscii=0` satırlardan seçilir (mojibake tuzağına girilmez) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK - kaynak: upstream docs" |
| Ölçüm | grep/glob sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Web | erişilen resmi kaynak URL'i + doğrulama tarihi; erişilemezse `⚠️ VERIFICATION REQUIRED` |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. Okunan PHP Envanteri — `shared/src/AI/` (7 dosya) + sözleşme

Dosya: `shared/src/AI/` · glob → **7 isabet** (2026-10-17) · `shared/src/Contracts/AI/` → **6 interface**

| # | Dosya | Satır | Hafıza ilgisi | Rol |
|---|---|---|---|---|
| 1 | MemorySystem.php | 242 | doğrudan | çekirdek: session/persistent/cache + HIERARCHY |
| 2 | MemorySystemInterface.php | 78 | doğrudan | sözleşme: 7 imza (L24-L77) |
| 3 | AIWorkflow.php | 271 | doğrudan | tüketici: L55 / L78 / L174 |
| 4 | AIOrchestrator.php | 210 | kısmi | context deposu: L154-L170 |
| 5 | AIEngine.php | 382 | zayıf | `session_id` okuma L53 |
| 6 | ToolCalling.php | 326 | sınır | `file_put_contents` L200 — bu bir hafıza yazımı DEĞİL |
| 7 | PromptEngine.php | 325 | YOK | hafıza kodu yok (L259 yalnız prompt metni) |
| 8 | KnowledgeBase.php | 232 | YOK | hafıza kodu yok (bellek-içi `$knowledge` ayrı konu) |

**Sözleşme:** Dosya: `shared/src/Contracts/AI/MemorySystemInterface.php` · Satır: L13, L24, L33, L41, L51, L59, L70, L77
— `interface MemorySystemInterface` · setSession · getSession · clearSession · setPersistent · getPersistent · remember · getHierarchy.

## §3. MemorySystem — Sınıf Amacı + L1/L2/L3 Seviye Yorumu

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L16-L22

```php
 * Memory System — Session state, persistent cache, memory hierarchy.
 *
 * Cache Seviyeleri:
 * - L1 (Hot): SSOT dosyaları (CLAUDE, AGENTS, WORKFLOW) — Oturum sonu
 * - L2 (Warm): Görev dosyaları (ADR, architecture) — Görev sonu
 * - L3 (Cool): Referans dosyaları (testing, ui-design) — İsteğe bağlı
```

| Gözlem | Kanıt |
|---|---|
| Üç seviye yorumu birebir vault `.ai/MEMORY.md` §10 tablosuyla aynı metni taşır | L19-L21 ↔ MEMORY.md L128-L130 |
| Seviyeler "dosya ömrü" olarak tanımlı — TTL saniye cinsinden değil | L19-L21 |
| `L1` default'lar yalnız `remember()` çağrılarında geçerli (`$level = 'l2'`) | L123 |

## §4. HIERARCHY Sabiti — Öncelik Skorları (kalıcı kayıt sırası)

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L25-L31

```php
private const HIERARCHY = [
    'brain'  => 5, // En yüksek
    'index'  => 4,
    'agents' => 4,
    'memory' => 3,
    'log'    => 1, // En düşük
];
```

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L152-L155

```php
public function getHierarchy(): array
{
    return self::HIERARCHY;
}
```

| Kavram | Karşılığı | Eşleşme |
|---|---|---|
| Vault okuma öncelik sırası (brain > index > AGENTS > MEMORY > log) | sabit skor tablosu 5→1 | **kısmi** — sabit, dinamik sıralama yok |
| `.ai/MEMORY.md` §3 öncelik satırı | `Oncelik Siralamasi...` (MEMORY.md L97) | **evet** (metin eşleşmesi) |
| Çakışmada üst seviye kazanır | kodda karşılığı YOK | **YOK** (yalnız vault kuralı) |

## §5. Session Kayıt Metotları — setSession / getSession / clearSession

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L33-L35, L45-L55

```php
/** @var array<string, array<string, array{value: mixed, expires_at: ?int}>> */
private array $sessions = [];

public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool
{
    $expiresAt = $ttl > 0 ? time() + $ttl : null;

    $this->sessions[$sessionId][$key] = [
        'value'      => $value,
        'expires_at' => $expiresAt,
    ];

    return true;
}
```

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L60-L75 (okuma + TTL kapısı)

```php
public function getSession(string $sessionId, string $key): mixed
{
    if (!isset($this->sessions[$sessionId][$key])) {
        return null;
    }

    $entry = $this->sessions[$sessionId][$key];

    // TTL kontrolü
    if ($entry['expires_at'] !== null && $entry['expires_at'] < time()) {
        unset($this->sessions[$sessionId][$key]);
        return null;
    }

    return $entry['value'];
}
```

| Gözlem | Kanıt |
|---|---|
| Depolama iki seviyeli: `sessionId → key → {value, expires_at}` | L33, L49-L52 |
| `ttl = 0` → `expires_at = null` → **süresiz** (PSR-16 ile zıt anlam — §29 #1) | L47 + interface L21 |
| Bayat kayıt okumada silinir (lazy invalidation) | L69-L72 |
| `clearSession` tüm oturumu tek `unset` ile söndürür | L80-L84 |
| **Süreç içi bellek** — array, disk/DB yazımı yok | §17 ölçüm |

## §6. Persistent Kayıt Metotları — setPersistent / getPersistent

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L36-L37, L89-L99

```php
/** @var array<string, array{value: mixed, expires_at: ?int}> */
private array $persistent = [];

public function setPersistent(string $key, mixed $value, int $ttl = 0): bool
{
    $expiresAt = $ttl > 0 ? time() + $ttl : null;

    $this->persistent[$key] = [
        'value'      => $value,
        'expires_at' => $expiresAt,
    ];

    return true;
}
```

Dosya: `shared/src/Contracts/AI/MemorySystemInterface.php` · Satır: L43-L51

```php
/**
 * Persistent state kaydeder (oturumdan bağımsız).
 *
 * @param string $key   Anahtar
 * @param mixed  $value Değer
 * @param int    $ttl   TTL (saniye, 0 = süresiz)
 * @return bool Başarı durumu
 */
public function setPersistent(string $key, mixed $value, int $ttl = 0): bool;
```

**Kavram tuzağı:** "persistent" adı vardır, mekanizma **process-içidir** — `file_put_contents`/PDO yazımı bu sınıfta **yok** (§17). Kalıcı kayıt vault tarafında dosyadır (§15).

## §7. remember() — Cache-Aside + Seviye Etiketi

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L123-L147

```php
public function remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed
{
    // Cache'de var mı kontrol et
    if (isset($this->cache[$key])) {
        $entry = $this->cache[$key];

        if ($entry['expires_at'] === null || $entry['expires_at'] >= time()) {
            return $entry['value'];
        }

        // Süresi dolmuş, temizle
        unset($this->cache[$key]);
    }

    // Yoksa yükle ve cache'le
    $value = $loader();

    $this->cache[$key] = [
        'value'      => $value,
        'level'      => $level,
        'expires_at' => $ttl > 0 ? time() + $ttl : null,
    ];

    return $value;
}
```

| Gözlem | Kanıt |
|---|---|
| Cache-aside: isabet → dön, ıskalar → `$loader()` çalıştır + yaz | L126-L146 |
| Seviye (`l1`/`l2`/`l3`) kayıtta tutulur ama okumada **karar için kullanılmaz** (yalnız istatistik) | L142, L166-L168 |
| Varsayılan `l2`, varsayılan TTL 3600 sn | L123 |
| `getCacheStats()` seviye sayaçlarını `by_level` döndürür | L162-L176 |

## §8. pruneExpired + estimateMemoryUsage — Ölçüm ve Temizlik

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L183-L194, L196-L212

```php
public function pruneExpired(): int
{
    $now = time();
    $removed = 0;

    // Cache temizliği
    foreach ($this->cache as $key => $entry) {
        if ($entry['expires_at'] !== null && $entry['expires_at'] < $now) {
            unset($this->cache[$key]);
            $removed++;
        }
    }
    // ... Persistent temizliği (L196-L202) · Session temizliği (L204-L212)
    return $removed;
}
```

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L222-L241

```php
private function estimateMemoryUsage(): int
{
    $size = 0;

    foreach ($this->cache as $entry) {
        $size += mb_strlen(serialize($entry['value']), '8bit');
    }
    // ... persistent + sessions aynı döngü (L230-L238)
    return $size;
}
```

| Gözlem | Kanıt |
|---|---|
| Üç depo temizlenir: cache → persistent → sessions; dönüş: silinen kayıt sayısı | L185-L214 |
| `serialize()` **yalnız boyut tahmininde** — diske yazım değil | L227, L231, L236 |
| Bellek üst sınırı / boyut limiti zorlaması **YOK** (yalnız raporlama) | L160-L176 |

## §9. Arayüz Sözleşmesi — 7 İmza (TTL Semantiği)

Dosya: `shared/src/Contracts/AI/MemorySystemInterface.php` · Satır: L21, L24, L33, L41, L59, L70, L77

```php
     * @param int    $ttl       TTL (saniye, 0 = süresiz)
public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool;
public function getSession(string $sessionId, string $key): mixed;
public function clearSession(string $sessionId): bool;
public function getPersistent(string $key): mixed;
/**
 * Cache stratejisi — L1/L2/L3 seviyeleri.
 */
public function remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed;
public function getHierarchy(): array;
```

| İmza | Satır | Dönüş |
|---|---|---|
| `setSession` | L24 | `bool` |
| `getSession` | L33 | `mixed` (yoksa `null`) |
| `clearSession` | L41 | `bool` |
| `setPersistent` | L51 | `bool` |
| `getPersistent` | L59 | `mixed` (yoksa `null`) |
| `remember` | L70 | `mixed` (cached veya fresh) |
| `getHierarchy` | L77 | `array{brain, index, agents, memory, log}` |

## §10. AIWorkflow — Hafızanın Gerçek Tüketimi (öneri + EQ cache)

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L52-L56, L77-L78

```php
$sessionId = $request['session_id'] ?? 'anonymous';

// 1. Context collect — session'dan tercihleri çek
$context = $this->memory->getSession($sessionId, 'preferences') ?? [];
$context = array_merge($context, $request);

// 6. Feedback — sonucu kaydet
$this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);
```

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L172-L174

```php
// 5. Cache'le
$cacheKey = "eq_{$sessionId}_" . md5(serialize($roomProfile));
$this->memory->remember($cacheKey, fn() => $eqCurve, 'l2', 3600);
```

| Akış | Hafıza kapısı | Anahtar | TTL |
|---|---|---|---|
| Recommendation: oku | `getSession('preferences')` | `session → preferences` | — |
| Recommendation: yaz | `setSession('last_recommendations', ...)` | `session → last_recommendations` | 3600 |
| AutoEQ: cache | `remember(..., 'l2', 3600)` | `eq_{sessionId}_{md5(profile)}` | 3600 |

## §11. AIOrchestrator — Context Deposu (session ↔ görev bağlamı)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L154-L170

```php
public function getContext(string $sessionId): array
{
    return $this->contexts[$sessionId] ?? [];
}

public function updateContext(string $sessionId, array $data): bool
{
    $this->contexts[$sessionId] = array_merge(
        $this->contexts[$sessionId] ?? [],
        $data
    );

    return true;
}
```

Dosya: `shared/src/AI/AIEngine.php` · Satır: L53

```php
$sessionId = $context['session_id'] ?? null;
```

| Gözlem | Kanıt |
|---|---|
| Context deposu `MemorySystem`'den **bağımsız** ikinci bellek (farklı sınıf alanı) | L154-L170 ↔ MemorySystem L34-L40 |
| `array_merge` — üstüne yazma, TTL **yok** (sonsuz ömür, process sonu) | L164-L167 |
| AIEngine yalnız `session_id` okur; kendi deposu yok | L53 |

## §12. `.ai/MEMORY.md` — Gerçek Session Kayıt Satır Formatı

Dosya: `.ai/MEMORY.md` · Satır: L646, L654, L407-L408, L410

```markdown
| Tarih | Konu | Durum | ADR | Agent |
| 2026-08-04 | Dynamic Theme Engine | Vault tamamlandi, kodlama yok | [[brain.md]] ADR-044-dynamic-user-theme-engine | UI |
| Session Date | 2026-10-15 |
| Domain | Neva Engine OSS sanal surucu arastirmasi (09 v1.1.0) + MEMORY.md kurtarma |
| Changed Files | .ai/projects/NevaEngine/09-open-source-virtual-audio-driver-karsilastirma.md (v1.1.0) + .ai/keys.md + .ai/log.md (append) + .ai/MEMORY.md (kurtarma + S20 guncel) |
```

| Format | Öğe | Kanıt |
|---|---|---|
| §18 Session History: 5 sütun | `Tarih · Konu · Durum · ADR · Agent` | L646 başlık satırı |
| §20 Current Session State: 2 sütun | `Ozellik · Deger` + `Last Action` maddesi | L405-L409 |
| Durum işareti | `✓ completed` / `Tamamlandi (...)` | L648-L650 / L654 |
| Dosya kanıtı satırı | `Changed Files` — değişen yollar tek satırda | L410 |
| Bilinen sorun satırı | `Known Issue` — `VERIFICATION REQUIRED` işaretli | L411 (UTF-8, alıntı yapılmadı) |

**Alıntı seçimi:** bu bölümdeki beş satır PowerShell ile `nonAscii=0` ölçülmüştür (2026-10-17);
dosyanın § başlıkları ve eski Türkçe satırları mojibake taşır (L79/L101/L124 `nonAscii=4`, L86 `nonAscii=16`).

## §13. `.ai/MEMORY.md` — Memory Hierarchy + Cache Strategies Alıntıları

Dosya: `.ai/MEMORY.md` · Satır: L53, L81-L85, L88-L89, L97

```markdown
| Memory Hierarchy | Bellek oncelik sirasi: brain > index > AGENTS > MEMORY > log |
| Dosya | Oncelik | Icerik | Mod | Max Boyut |
| `brain.md` | En yuksek | Mimari kararlar, ADR 001-089 (80 karar) | Read-Write | 1000 satir |
| `index.md` | Yuksek | Master katalog, tum vault indeksi | Read-Write | 1000 satir |
| `AGENTS.md` | Yuksek | Agent tanimlari, yetkiler, handover | Read-Write | 1000 satir |
| `MEMORY.md` | Orta | Session state, bu dosya | Read-Write | 1000 satir |
| `log.md` | En dusuk | Append-only audit trail | Append-Only | 1000 satir |
**Oncelik Siralamasi:** En yuksek -> en dusuk: brain -> index -> AGENTS -> MEMORY -> log. Cakisma durumunda ust seviye kazanir.
```

Dosya: `.ai/MEMORY.md` · Satır: L126, L128-L130, L132

```markdown
| Seviye | Aciklama | Omur | Gecersizlastirma |
| L1 (Hot) | SSOT dosyalari (CLAUDE, AGENTS, WORKFLOW) | Oturum sonu | Otomatik |
| L2 (Warm) | Gorev dosyalari (ADR, architecture) | Gorev sonu | Dosya degisikligi |
| L3 (Cool) | Referans dosyalari (testing, ui-design) | Istege bagli | Manuel |
**Politika:** Read-Through, Write-Through, LRU eviction. P0 dosyalari eviction'a ugramaz.
```

| Eşleşme | Karşılık | Durum |
|---|---|---|
| Vault 7 dosya öncelik tablosu | PHP `HIERARCHY` (§4) — 5 anahtar, skor 5→1 | **kısmi** (tablo 7 satır, kod 5 anahtar) |
| L1/L2/L3 "Ömür/Gecersizlastirma" | PHP yorumu (§3) — metin birebir | **evet** |
| `Politika: ... LRU eviction` | PHP'de LRU kodu | **YOK** (§17 grep 0) |
| `Read-Through` politikası | `remember()` (§7) | **evet** |

## §14. `.ai/MEMORY.md` — Persistent State Kuralları + Güncelleme Akışı

Dosya: `.ai/MEMORY.md` · Satır: L106, L113-L118, L120

```markdown
| Session Hafizasi | `MEMORY.md` | Her oturum sonunda | Read-Write |
**Kurallar:**
- Immutability: ADR 001-037 frozen, degistirilemez
- Append-Only: `log.md` gecmis satirlari silinemez
- Timestamp zorunlu: Her giris UTC timestamp icermeli
- No Secrets: Hassas veri ASLA vault'a yazilmaz
- Cross-Reference: Tum wiki-link'ler gecerli olmali
**Guncellenme Akisi:** Degisiklik tespit -> Ilgili dosya belirle -> In-place uygula -> Cross-reference guncelle -> `log.md`'ye yaz -> MEMORY.md session state guncelle -> Wiki-link'leri dogrula.
```

| Kural | Kod karşılığı | Eşleşme |
|---|---|---|
| Session Hafızası "Her oturum sonunda" güncellenir | `setSession` otomatik çağrısı **YOK** (manuel vault-sync) | **kısmi** |
| Append-Only (`log.md`) | PHP'de karşılık **YOK** | **YOK** |
| Timestamp zorunlu (UTC) | `expires_at = time()` — TTL anı, kayıt timestamp'i **değil** | **kısmi** |
| No Secrets | §25 — bu dosyada uygulanıyor | **evet** |
| Guncellenme Akisi zinciri | vault sync süreci (§15) | **evet** (süreç, kod değil) |

## §15. Kalıcı Mekanizma (Vault Tarafı) — Session Sync Bitiş 5 Adım

Dosya: `.ai/MEMORY.md` · Satır: L358, L360-L364

```markdown
| # | Adim | Kontrol | Sure |
| 1 | Degisiklikleri vault'a yaz (in-place) | Dosya boyutu | 5s |
| 2 | `log.md`'ye timestamp ekle | Format dogrulama | 2s |
| 3 | MEMORY.md session state guncelle | Session indeks | 3s |
| 4 | Wiki-link'leri dogrula | Regex pattern | 5s |
| 5 | Hallusinasyon sweep | `VERIFICATION REQUIRED` taramasi | 3s |
```

| Katman | Mekanizma | Sahip | Kanıt |
|---|---|---|---|
| Kısa vadeli (request) | PHP `$sessions/$persistent/$cache` array | MemorySystem | §5-§7 |
| Oturum sonu | dosya yazımı: MEMORY.md + log.md (append) | vault-sync | L360-L364 |
| Uzun vadeli | Git history / haftalık snapshot / aylık tam yedek | MEMORY.md §11 (L372-L387) | okuma |
| Kök neden ayrımı | PHP "persistent" = process-içî · vault "persistent" = dosya | bu dosya | §6, §17 |

## §16. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| `MemorySystem` disk/DB kalıcı yazımı | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Oturum özeti (LLM/otomatik summary üretimi) | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Triplet / graf hafızası (subject-predicate-object) | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| LRU/LFU eviction kodu (vault L132 politikası) | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Bellek boyutu üst sınırı / `SizeLimit` benzeri zorlama | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Embedding / vektör tabanlı hafıza araması | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| `Psr\SimpleCache` (PSR-16) veya `Psr\Cache` (PSR-6) implementasyonu | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Hafıza checksum / bütünlük doğrulama (bozulma tespiti) | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Process dışı paylaşım (Redis / distributed cache köprüsü) | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Otomatik `pruneExpired()` zamanlayıcısı (çağrı yeri) | Repo'da bu alan kodu YOK - kaynak: upstream docs |
| Cross-session bilgi çıkarımı (özet → sonraki oturuma taşıma) | Repo'da bu alan kodu YOK - kaynak: upstream docs |

## §17. Negatif Ölçüm — Grep Kanıtları (araç çıktısı, 2026-10-17)

| Arama (path: `shared/src/AI`, `*.php`) | Sonuç |
|---|---|
| `LRU\|lru\|eviction\|Eviction\|Apcu\|PageCache\|Cache\` | **0 isabet** |
| `summary\|Summary\|triplet\|embedding\|Embedding\|digest` | **0 isabet** |
| `Psr\` (PSR arayüz kullanımı) | **0 isabet** |
| `file_put_contents\|fwrite` | **1 isabet** — ToolCalling.php L200 (`file-write` aracı, hafıza yazımı değil) |
| `serialize` | 4 isabet — MemorySystem L227/L231/L236 (yalnız boyut tahmini) + AIWorkflow L173 (cache key) |
| `psr/simple-cache` (`shared/composer.json`) | **0 isabet** — `psr/cache ^3.0` mevcut (L16), `psr/simple-cache` YOK |
| `memory-anayasa.md` / `memory-surucu.md` (glob `.ai/architecture/K015-AI/memory/*`) | **0 isabet** — bu dosyanın `depends_on` hedefleri diskte henüz YOK |

`.ai/MEMORY.md` ölçüm: `ReadAllLines(UTF8)` → **728 satır** (kendi limiti 1000 — L68 `Max 1000 satir`).

## §18. İllüstratif — Python: Triplet Memory + Summary Buffer

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
import time
from collections import deque
from dataclasses import dataclass

@dataclass(frozen=True)
class Triplet:                      # subject - predicate - object hafıza üçlüsü
    subject: str
    predicate: str
    object: str
    confidence: float = 1.0

class TripletMemory:                # kalıcı bilgi katmanı (repo karşılığı YOK — §16)
    def __init__(self) -> None:
        self._facts: dict[tuple[str, str, str], Triplet] = {}

    def remember(self, t: Triplet) -> None:
        self._facts[(t.subject, t.predicate, t.object)] = t

    def recall(self, subject: str) -> list[Triplet]:
        return [t for k, t in self._facts.items() if k[0] == subject]

class SummaryBuffer:                # pencere içi oturum özeti tamponu
    def __init__(self, window: int = 20) -> None:
        self._events: deque[tuple[float, str]] = deque(maxlen=window)

    def add(self, event: str) -> None:
        self._events.append((time.time(), event))

    def summarize(self) -> str:     # üretimde LLM özeti buraya bağlanır (§16: kod YOK)
        return " | ".join(e for _, e in self._events)
```

**Kapsam:** bilgi üçlüsü + özet tamponu — repo karşılığı **YOK** (§16, §17 grep 0).

## §19. İllüstratif — TypeScript: localStorage/sessionStorage Session Cache

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type SessionState = { prefs: Record<string, string>; lastRoute: string; ts: number };
const KEY = "cm.session.v1";
const TTL_MS = 3_600_000;           // PHP tarafındaki 3600 sn TTL ile aynı ölçek (AIWorkflow L78)

export function loadSession(): SessionState | null {
  const raw = sessionStorage.getItem(KEY);      // sekme kapanınca silinir (MDN §29 #2)
  if (!raw) return null;
  const parsed = JSON.parse(raw) as SessionState;
  if (Date.now() - parsed.ts > TTL_MS) {
    sessionStorage.removeItem(KEY);             // lazy invalidation — PHP L69-L72 ile aynı fikir
    return null;
  }
  return parsed;
}

export function saveSession(state: SessionState): void {
  sessionStorage.setItem(KEY, JSON.stringify({ ...state, ts: Date.now() }));
}
```

**Kapsam:** istemci tarafı oturum önbelleği — repo JS yüzeyinde de bu eşleştirme **YOK**.

## §20. İllüstratif — Go: LRU Cache (eviction politikası)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package lru

import "container/list"

type entry struct{ key string; value any }

type Cache struct {
	cap int
	ll  *list.List            // en erken erişilen başta
	m   map[string]*list.Element
}

func New(capacity int) *Cache {
	return &Cache{cap: capacity, ll: list.New(), m: make(map[string]*list.Element)}
}

func (c *Cache) Get(key string) (any, bool) {
	if el, ok := c.m[key]; ok {
		c.ll.MoveToFront(el)   // erişim → en yeni
		return el.Value.(*entry).value, true
	}
	return nil, false
}

func (c *Cache) Set(key string, value any) {
	if el, ok := c.m[key]; ok {
		el.Value.(*entry).value = value
		c.ll.MoveToFront(el)
		return
	}
	c.m[key] = c.ll.PushFront(&entry{key, value})
	if c.ll.Len() > c.cap {                    // kapasite taşımı → en eskiyi at
		oldest := c.ll.Back()
		c.ll.Remove(oldest)
		delete(c.m, oldest.Value.(*entry).key)
	}
}
```

**Kapsam:** LRU eviction — vault MEMORY.md L132'de **politika olarak yazılı**, PHP kodunda **YOK** (§16, §17).

## §21. İllüstratif — Rust: Persisted KV (write-ahead log)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
use std::collections::HashMap;
use std::fs::{File, OpenOptions};
use std::io::{BufRead, BufReader, BufWriter, Write};

pub struct PersistedKv {
    mem: HashMap<String, String>,
    log: BufWriter<File>,
}

impl PersistedKv {
    pub fn open(path: &str) -> std::io::Result<Self> {
        let file = OpenOptions::new().create(true).append(true).open(path)?;
        let mut mem = HashMap::new();
        for line in BufReader::new(File::open(path)?) {          // replay: WAL → bellek
            let mut it = line.splitn(3, '\t');
            if let (Some("put"), Some(k), Some(v)) = (it.next(), it.next(), it.next()) {
                mem.insert(k.to_string(), v.to_string());
            }
        }
        Ok(Self { mem, log: BufWriter::new(file) })
    }

    pub fn put(&mut self, k: &str, v: &str) -> std::io::Result<()> {
        writeln!(self.log, "put\t{k}\t{v}")?;
        self.log.flush()?;                                       // üretimde fsync
        self.mem.insert(k.to_string(), v.to_string());
        Ok(())
    }

    pub fn get(&self, k: &str) -> Option<&str> {
        self.mem.get(k).map(String::as_str)
    }
}
```

**Kapsam:** süreç dışı kalıcı KV — PHP `MemorySystem`'de karşılığı **YOK** (§16); vault tarafı dosya yazımı §15.

## §22. İllüstratif — C++: Ring Buffer Hafıza (sabit bellek)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
#include <array>
#include <cstddef>
#include <utility>

// Sabit kapasiteli döngüsel tampon — en yeni kayıt en eskisi'nin yerine geçer.
// Neva Engine realtime yüzeyinde "buffer'lar baştan allocate" kuralı ile aynı fikir.
template <typename T, std::size_t N>
class RingMemory {
    std::array<T, N> buf_{};
    std::size_t head_ = 0;      // yazma konumu
    std::size_t count_ = 0;
public:
    void push(T v) noexcept {
        buf_[head_] = std::move(v);
        head_ = (head_ + 1) % N;
        if (count_ < N) ++count_;
    }
    template <typename F>
    void each(F&& f) const {                       // en eskiden en yeniye
        for (std::size_t i = 0; i < count_; ++i)
            f(buf_[(head_ + N - count_ + i) % N]);
    }
    std::size_t size() const noexcept { return count_; }
};
```

**Kapsam:** sabit bellekli oturum penceresi — repo AI klasöründe karşılık **YOK** (C++ yüzeyi Neva Engine / PLANNED).

## §23. İllüstratif — C#: In-Memory + Disk Snapshot

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
using System.Text.Json;

public sealed class SnapshotMemory<T> where T : class
{
    private readonly Dictionary<string, T> _mem = new();
    private readonly string _path;

    public SnapshotMemory(string path) => _path = path;

    public void Set(string key, T value) => _mem[key] = value;          // süreç içi okuma/yazma

    public void Save(CancellationToken ct = default)                    // atomik snapshot
    {
        var tmp = _path + ".tmp";
        File.WriteAllText(tmp, JsonSerializer.Serialize(_mem));
        File.Move(tmp, _path, overwrite: true);                         // write-then-replace
    }

    public bool TryLoad(out Dictionary<string, T>? state)
    {
        state = File.Exists(_path)
            ? JsonSerializer.Deserialize<Dictionary<string, T>>(File.ReadAllText(_path))
            : null;
        return state is not null;
    }
}
```

**Kapsam:** bellek-içi depo + diske anlık görüntü — PHP `MemorySystem` yalnız ilk yarıyı içerir (§6, §16).

## §24. Kavram ↔ Eşleşme Tablosu (vault kavramı → kod karşılığı)

| Vault kavramı | Repo kod karşılığı | Eşleşme |
|---|---|---|
| L1/L2/L3 cache seviyeleri | `remember($key, $loader, $level, $ttl)` L123-L147 | **evet** |
| Hierarchy öncelik skorları | `HIERARCHY` L25-L31 + `getHierarchy()` L152-L155 | **evet** |
| Session state (oturum içi) | `setSession/getSession` L45-L75 | **evet** |
| Persistent state (process-içi) | `setPersistent/getPersistent` L89-L118 | **evet** |
| Cache stats / ölçüm | `getCacheStats` + `estimateMemoryUsage` L162-L176, L222-L241 | **evet** |
| Süre dolumu temizliği | `pruneExpired` L183-L215 | **evet** (çağrı yeri yok §16) |
| Oturum kaydı satır formatı (§18/§20) | — (dosya, kod değil) | **kısmi** |
| Oturum özeti üretimi | — | **YOK** |
| Kalıcı vault yazımı | `MEMORY.md`/`log.md` (vault-sync, PHP değil) | **kısmi** |
| LRU eviction (MEMORY.md L132) | — | **YOK** |
| Append-Only audit trail | — | **YOK** |
| Context / görev bağlamı | `AIOrchestrator::getContext/updateContext` L154-L170 | **evet** |
| Triplet / graf hafızası | — | **YOK** |
| Memory size limit (10MB kuralı) | raporlama var, zorlama yok | **kısmi** |
| PSR-16 cache sözleşmesi | — (kendi `MemorySystemInterface`) | **YOK** |

## §25. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| Vault'a secret yazılmaz (`No Secrets`) | `.ai/MEMORY.md` L117: `- No Secrets: Hassas veri ASLA vault'a yazilmaz` |
| SECRET sınıfları `[REDACTED]` maskesi ile loglanır | `.ai/MEMORY.md` §12 (L255-L271) + L269 `**Dogru:** ... [REDACTED] ... **Yanlis:** ...` |
| Hafıza değeri `mixed` — secret tutulabilir; sınırlama **kodda değil** süreç kuralındadır | MemorySystem L45 (`mixed $value`) + bu dosya |
| Injection girdisi hafızaya girerse bile filtre PromptEngine'de | PromptEngine L156-L186 (rag-ornekler §9) |
| Bu dosyada ölçülen gerçek satır: "Hassas veri ASLA vault'a yazilmaz" | MEMORY.md L117 (nonAscii=0, 2026-10-17) |

## §26. Ölçüm Kalıpları (yeniden üretilebilir komutlar)

| Amaç | Komut (repo kökünden, PowerShell) |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Mojibake/ASCII denetimi | satır bazlı `[int]$_ -gt 127` sayımı — alıntı hedefi `nonAscii=0` |
| PHP kanıtı tazeliği | `Select-String -Path shared\src\AI\MemorySystem.php -Pattern 'HIERARCHY'` |
| Negatif ölçüm | `Select-String -Path shared\src\AI\*.php -Pattern 'LRU\|eviction'` → 0 |
| İllüstratif etiket | `Select-String -Path $hedef -Pattern 'İllüstratif'` → 6 |
| Section sayısı | `Select-String -Path $hedef -Pattern '^## §'` → >= 25 |
| Frontmatter + Authority | ilk 14 satır 10 alan · `$lines[-1]` birebir eşleşme |

## §27. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (2026-10-17 ölçüm: 806) | §26 ölçümü |
| 2 | Frontmatter 10 alan + Authority son satır | `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | PHP kanıtı | her alıntı gerçek `Dosya:`/`Satır:` | 2026-10-17 okuma |
| 4 | MEMORY.md alıntıları | `nonAscii=0` satırlar | §12, §17 |
| 5 | Yokluk cümleleri | 11 satır "Repo'da bu alan kodu YOK" | §16 |
| 6 | İllüstratif etiket | 6 blok / 6 dil + üstünde etiket | §18-§23 |
| 7 | Web kaynakları | 3 resmi kaynak erişildi | §29 |
| 8 | Placeholder + Mojibake | üretimde bekleyen çift süslü parantez yer tutucu yok · U+FFFD 0 | grep |

## §28. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | `depends_on: [memory-anayasa, memory-surucu]` hedefleri diskte yok (glob 0, §17) — bu bilinçli sıra mı, eksik üretim mi? | `⚠️ VERIFICATION REQUIRED` |
| 2 | `pruneExpired()` için tetikleyici (istek sonu / zamanlayıcı) nerede çağrılıyor? Çağrı yesi bulunamadı (grep 0). | `⚠️ VERIFICATION REQUIRED` |
| 3 | Vault `Politika: ... LRU eviction` satırı hangi bileşende uygulanacak (ADR bekliyor)? | `⚠️ VERIFICATION REQUIRED` |
| 4 | `getCacheStats()` çıktısını okuyan bir izleme/raporlama yüzeyi var mı? | `⚠️ VERIFICATION REQUIRED` |
| 5 | AIOrchestrator `$contexts` ile MemorySystem `$sessions` arasında yaşar/söndürme sahipliği kim? | `⚠️ VERIFICATION REQUIRED` |

## §29. Kaynaklar

| # | Kaynak | URL | Doğrulama |
|---|---|---|---|
| 1 | PSR-16: Common Interface for Caching Libraries (PHP-FIG) | https://www.php-fig.org/psr/psr-16/ | 2026-10-17 erişildi — TTL: "If a negative or zero TTL is provided, the item MUST be deleted from the cache" (repo: `ttl=0` = süresiz → **zıt semantik**, §5) · cache miss → `null` |
| 2 | MDN — Window.sessionStorage | https://developer.mozilla.org/en-US/docs/Web/API/Window/sessionStorage | 2026-10-17 erişildi — "The data in sessionStorage is only kept for the duration of the page session" · "Closing the tab/window ends the session and clears the data" (§19) |
| 3 | Microsoft Learn — Caching in .NET | https://learn.microsoft.com/en-us/dotnet/core/extensions/caching | 2026-10-17 erişildi — `IMemoryCache`: "a local in-memory cache whose values are not serialized" · `MemoryCache`: "using a dictionary to store its entries" · sliding/absolute expiration + `SizeLimit` (§23) |

| Vault kaynağı | Dosya |
|---|---|
| Hafıza indeksi (session state, hierarchy, cache strategies) | `.ai/MEMORY.md` (728 satır, 2026-10-17 ölçüm) |
| Çekirdek kod | `shared/src/AI/MemorySystem.php` (242 satır) |
| Sözleşme | `shared/src/Contracts/AI/MemorySystemInterface.php` (78 satır) |
| Kardeş örnek dosyası (şablon) | `.ai/architecture/K015-AI/rag/rag-ornekler.md` (519 satır) |

## §30. Yasaklar & Gate Özeti

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | AGENTS.md §16.5 — commit orkestratöre aittir |
| `shared/src/**` yazımı ve bu dosya dışına yazım (salt-okunur) | görev manifesti |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (AGENTS §3) · §1 |
| Uydurma URL / erişilmemiş kaynak | §29 — erişilemezse `⚠️ VERIFICATION REQUIRED` |
| Çift süslü parantez placeholder'ı · ölçülmemiş sayıya "ölçüldü" demek | RAG.md §6 #6 · §3.2 kural 4 |

§1-§30: künye formatı 11 kural · envanter 8 satır (7 PHP + 1 sözleşme) · PHP künye satırı 16 (`Dosya: shared/...`) · MEMORY.md künye satırı 5 (§12-§15, §25) · KOD YOK 11 satır · grep negatif 7 satır · illüstratif **6 blok / 6 dil** (Python, TypeScript, Go, Rust, C++, C#) · eşleme 15 satır · güvenlik 5 · ölçüm 7 · kapı 8 · açık soru 5 · web kaynağı 3 → kapılar: `>=500` (806) · FM 10 alan · Authority son satır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
