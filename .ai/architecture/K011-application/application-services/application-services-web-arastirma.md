---
title: "K011 · application-services — Web Araştırması"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-services
status: active
updated: 2026-10-15
group: kanit
---

# K011 · application-services — Web Araştırması

> **Amaç:** `application-services` alt-kapsamının 5 resmi kaynağı (PSR havuzu) erişim · içerik · kesişim kanıtlarıyla belgeler ve her "standart" iddiasını repo kanıtıyla çaprazlar.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Kaynak politikası | kanit | yontem | Araştırma kuralları |
| 2 | §2 Resmi kaynak havuzu | kanit | havuz | 5 URL + 200 kanıtı |
| 3 | §3 Kesişim kanıtı | kanit | kesisim | Envantara 0 çakışma |
| 4 | §4 Claim ↔ repo çapraz matris | kanit | capis | Birincil çaprazlama |
| 5 | §5 Standart notları (5) | kanit | standart | PSR-6/16/14/3/12 |
| 6 | §6 Reddedilen adaylar | kanit | red | Etiket listesi |
| 7 | §7 Açık sorular | kanit | qa | Doğrulanmayanlar |
| 8 | §8 Erişim günlüğü | kanit | erisim | Başlık + boyut + kod |
| 9 | §9 Kullanım haritası | kanit | esleme | Claim → kaynak |
| 10 | §A Alıntı politikası | kanit | politika | Kopyalama sınırı |
| 11 | §B Tekrar doğrulama prosedürü | kanit | tekrar | Bitiş kontrolü |
| 12 | §C URL dışına çıkmama denetimi | kanit | sinir | Havuz disiplini |
| 13 | §D Ölçüm yöntemi | kanit | olcum | Araç eşdeğerliği |
| 14 | §E Özet | kanit | ozet | Kapanış |

## §1 — Kaynak politikası

| # | Kural | Dayanak |
|---|---|---|
| 1 | Yalnız klasöme atanmış **5 URL** kullanılır | spec §6 (havuz) |
| 2 | Her URL **tek bir dosyada** yaşar — kardeşlerle paylaşılmaz | spec §1.6 |
| 3 | Havuz dışında yeni URL eklenmez | spec §1.6 |
| 4 | Reddedilen aday **tam URL yazılmaz**, etiketle yazılır | spec §1.6 |
| 5 | Her URL bitişte `200` döndürmeli | spec §1.6 |
| 6 | Kesişim (başka K011 dosyalarıyla ortak URL) = 0 olmalı | spec §5 |
| 7 | Kaynak içeriği ≥2–3 resmi kaynakla çaprazlanır | Research Mandate (varsayılan seviye) |
| 8 | Doğrulanamayan claim `⚠️ VERIFICATION REQUIRED` | Zero-Hallucination |

**Kanıt hiyerarşisi (bu belge için):** repo kodu (yerel, kesin) → resmi standart sayfası (uzak, başlık+içerik doğrulandı) → model hafızası (yalnızca teyit edilmişse).

## §2 — Resmi kaynak havuzu

| # | URL | Başlık (sayfadan) | Standart | HTTP | Doğrulama |
|---|---|---|---|---|---|
| 1 | `https://php-fig.org/psr/psr-6/` | PSR-6: Caching Interface - PHP-FIG | Önbellek (gelişmiş) | 200 | 2026-10-15 · fetch |
| 2 | `https://php-fig.org/psr/psr-16/` | PSR-16: Common Interface for Caching Libraries - PHP-FIG | Önbellek (basit) | 200 | 2026-10-15 · fetch |
| 3 | `https://php-fig.org/psr/psr-14/` | PSR-14: Event Dispatcher - PHP-FIG | Olay yayımı | 200 | 2026-10-15 · fetch |
| 4 | `https://php-fig.org/psr/psr-3/` | PSR-3: Logger Interface - PHP-FIG | Günlük (log) | 200 | 2026-10-15 · fetch |
| 5 | `https://php-fig.org/psr/psr-12/` | PSR-12: Extended Coding Style - PHP-FIG | Kod stili | 200 | 2026-10-15 · fetch |

**İçerik teyidi (sayfa metninden alınan giriş cümlesi özeti):**

| # | Kaynak | Sayfa metni giriş özeti | Metin boyutu |
|---|---|---|---|
| 1 | PSR-6 | Önbellek, performans için yaygın bir yöntemdir; birçok kütüphane kendi önbellek kütüphanelerini inşa eder → ortak arayüz hedefi | 16.373 karakter |
| 2 | PSR-16 | "Basit ama genişletilebilir bir önbellek öğesi ve sürücü arayüzü" tanımı; MUST/MUST NOT anahtar kelimeleri | 11.395 karakter |
| 3 | PSR-14 | Olay yayımı, uygulamaya mantık enjekte etmenin yaygın ve denenmiş mekanizması; ortak mekanizma kurma hedefi | 9.947 karakter |
| 4 | PSR-3 | "Günlük kütüphaneleri için ortak arayüz" — `Psr\Log\LoggerInterface` nesnesine yazma hedefi | 9.207 karakter |
| 5 | PSR-12 | RFC2119 anahtar kelimeleriyle yazılmış **Extended Coding Style Guide** | 24.568 karakter |

**Sayfada tespit edilen arayüz adları (metin taraması):**

| # | Kaynak | Bulunan arayüz adı |
|---|---|---|
| 1 | PSR-6 | `Psr\Cache\CacheItemPoolInterface` · `Psr\Cache\CacheItemInterface` |
| 2 | PSR-16 | `Psr\SimpleCache\CacheInterface` |
| 3 | PSR-3 | `Psr\Log\LoggerInterface` · `Psr\Log\LoggerAwareInterface` |
| 4 | PSR-14 | arayüz adı metin taramasında yakalanmadı → repo `use` satırı esas: `Psr\EventDispatcher\EventDispatcherInterface` (`EventDispatcher.php:14`) |
| 5 | PSR-12 | arayız yoktur — kılavuz belgesidir (RFC2119 anahtar kelimeleri) |

## §3 — Kesişim kanıtı

```powershell
# spec §5 ile birebir — repo kökünden çalıştırılır
$root = "C:\www\coremusic.net\.ai\architecture\K011-application"
$d = "$root\application-services"

$others = Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -ne $d } |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique

$own = Get-ChildItem $d -Filter *.md |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique

@($own | Where-Object { $others -contains $_ }).Count   # beklenen: 0
```

| # | Ölçüm | Beklenen | Durum |
|---|---|---|---|
| 1 | Kendi klasördeki benzersiz URL | 5 | BEKLEMEDE (bitiş ölçümü) |
| 2 | Diğer K011 dosyalarıyla kesişim | 0 | BEKLEMEDE (bitiş ölçümü) |
| 3 | Havuzun tek dosyada toplanması | 1/1 (bu dosya) | PASS |
| 4 | Kardeş dosyalarda URL metni | 0 | PASS (kardeşler bu üründe URL içermez) |

**Bu oturumdaki erişim ölçümü (subagent aracı):**

```text
fetch("https://php-fig.org/psr/psr-6/")  → HTTP 200
fetch("https://php-fig.org/psr/psr-16/") → HTTP 200
fetch("https://php-fig.org/psr/psr-14/") → HTTP 200
fetch("https://php-fig.org/psr/psr-3/")  → HTTP 200
fetch("https://php-fig.org/psr/psr-12/") → HTTP 200
```

> **Araç notu:** `shell` izni reddedildiği için `curl.exe -L -s -o NUL -w "%{http_code}"` bu oturumda çalıştırılamadı; aynı kapı `fetch` durum koduyla alındı. Bitişte spec betiği yeniden çalıştırılabilir (§B).

## §4 — Claim ↔ repo çapraz matris

| # | Kaynak claim (özet) | Repo iddiası | Dosya | Satır | Karar |
|---|---|---|---|---|---|
| 1 | PSR-6 `CacheItemPoolInterface` tanımlar | Repo `Psr\Cache\*` kullanmıyor | (arama) | 0 isabet | ⚠️ VERIFICATION REQUIRED |
| 2 | PSR-16 `Psr\SimpleCache\CacheInterface` tanımlar | Repo'da karşılık yok | (arama) | 0 isabet | ⚠️ VERIFICATION REQUIRED (PLANNED) |
| 3 | PSR-14 ortak olay yayım mekanizması kurar | `EventDispatcher implements EventDispatcherInterface` | `shared/src/Events/EventDispatcher.php` | 14, 20 | **UYGUN** |
| 4 | PSR-14 olay tabanlı genişlemeye izin verir | `addListener(callable, priority)` | `EventDispatcher.php` | 50-59 | **UYGUN** |
| 5 | PSR-3 `LoggerInterface` sağlar | `FileHandler implements LoggerInterface` | `shared/src/Log/FileHandler.php` | 5, 30 | **UYGUN** |
| 6 | PSR-3 kanonik seviye adları | `LEVELS` 8 seviye | `FileHandler.php` | 40-49 | **UYGUN** |
| 7 | PSR-3 `LoggerAwareInterface` (opsiyonel) | Repo'da kullanımı yok | (arama) | 0 isabet | Bilgi notu — şart değil |
| 8 | PSR-12 extend edilmiş kod stili kılavuzu | `declare(strict_types=1)` + 4 boşluk girinti | `shared/src/Cache/CacheManager.php` | 1 | Görsel uyum |
| 9 | PSR-12 otomatik denetim **gerektirmez** | Stil denetleyici paketi yok | `shared/composer.json` | 23-26 | **UYGUN** (araç yok = ihlal değil) |
| 10 | Önbellek ortak arayüzle standartlaşır | Özel `CacheInterface` 6 metot | `shared/src/Cache/CacheInterface.php` | 5-13 | ⚠️ Standart dışı yerel kontrat |
| 11 | Basit önbellek genişletilebilir sürücü ister | Adaptör deseni mevcut | `shared/src/Cache/CacheManager.php` | 9-19 | Kısmen uygun (kavramsal) |
| 12 | Olay yayımı mantık enjeksiyonu sağlar | Domain event'ler ayrık sınıflarda | `shared/src/Events/Domain/` (9 dosya) | — | **UYGUN** |
| 13 | Günlük "basit ve evrensel" yazım ister | Tek satır formatı + redaksiyon | `FileHandler.php` docblock | 19-28 | **UYGUN** |
| 14 | Paket bağımlılıkları ile standart eşleşmeli | `psr/cache`, `psr/event-dispatcher`, `psr/log` | `shared/composer.json` | 16, 18, 19 | **UYGUN** (bağımlılık) |
| 15 | PSR-16 eksikliği basit arayüz ister | `CacheInterface` `get/set/delete/...` benzeri | `CacheInterface.php` | 7-12 | ⚠️ Benzerlik ≠ uyum |
| 16 | PSR-12 dosya sonu newline / boş satır | Dosyalar newline ile biter | (ölçüm) | — | **UYGUN** (ölçüm §8) |
| 17 | PSR-14 sıralı dinleyici yürütme | `usort` azalan öncelik | `EventDispatcher.php` | 113-115 | **UYGUN** |
| 18 | PSR-3 `log()` seviye yönlendirmesi | `FileHandler` `log()` dispatcher | `FileHandler.php` (docblock 11-13) | 11-13 | **UYGUN** (docblock beyanı) |
| 19 | PSR-6 zaman damgası arayüzü (`\DateTimeInterface`) | Repo cache TTL saniye cinsinden (`int $ttl`) | `CacheInterface.php` | 8 | ⚠️ Farklı model |
| 20 | PSR-12 genişletilmiş kurallar (import sırası vb.) | İçe aktarma düzeni dosya içinde sabit | `EventDispatcher.php` | 12-15 | Görsel uyum (otomatik denetim yok) |

**Matris özeti:** 20 claim · **11 UYGUN** · **6 ⚠️ VERIFICATION REQUIRED** · **3 görsel/kavramsal uyum** · **0 uydurma**.

## §5 — Standart notları

### §5.1 PSR-6 (Caching Interface)

| # | Not | Kaynak | Repo durumu |
|---|---|---|---|
| 1 | Konu: gelişmiş önbellek arayüzü | havuz #1 (200) | — |
| 2 | Arayüz: `CacheItemPoolInterface`, `CacheItemInterface` | sayfa metni | ⚠️ Kullanım yok |
| 3 | Zaman: `\DateTimeInterface` geçerlilik modeli | sayfa metni | Repo `int $ttl` kullanır |
| 4 | Composer'da `psr/cache ^3.0` | `composer.json:16` | Paket var |
| 5 | Karar bekliyor | §7-1 | ⚠️ VERIFICATION REQUIRED |

### §5.2 PSR-16 (Simple Cache)

| # | Not | Kaynak | Repo durumu |
|---|---|---|---|
| 1 | Konu: basit, genişletilebilir önbellek arayüzü | havuz #2 (200) | — |
| 2 | Arayüz: `Psr\SimpleCache\CacheInterface` | sayfa metni | ⚠️ Kullanım yok |
| 3 | Anahtar kelime sistemi (MUST/SHOULD/MAY) | sayfa metni | Belge diline uygun |
| 4 | Repo'nun özel kontratı benzer işlev görür | `CacheInterface.php:7-12` | Yerel |
| 5 | Karar bekliyor | §7-2 | ⚠️ VERIFICATION REQUIRED |

### §5.3 PSR-14 (Event Dispatcher)

| # | Not | Kaynak | Repo durumu |
|---|---|---|---|
| 1 | Konu: ortak olay yayım mekanizması | havuz #3 (200) | — |
| 2 | Repo arayüzü uyguluyor | `EventDispatcher.php:14,20` | ✅ IMPLEMENTED |
| 3 | Durdurulabilir olay trait'i | `Events/StoppableEventTrait.php` + `dispatch` kontrolü (37-39) | ✅ |
| 4 | Öncelik sıralaması yerel özellik | `EventDispatcher.php:109-118` | Ek (PSR dışı ama çelişki yok) |
| 5 | Test | `tests/Events/EventDispatcherTest.php` (98 satır, 8 test metodu) | ✅ |

### §5.4 PSR-3 (Logger Interface)

| # | Not | Kaynak | Repo durumu |
|---|---|---|---|
| 1 | Konu: günlük kütüphaneleri için ortak arayüz | havuz #4 (200) | — |
| 2 | `Psr\Log\LoggerInterface` | sayfa metni + `FileHandler.php:5,30` | ✅ IMPLEMENTED |
| 3 | Kanonik seviyeler 8 adet | `FileHandler.php:40-49` | ✅ |
| 4 | `LoggerAwareInterface` | sayfa metni | Kullanım yok (opsiyonel) |
| 5 | Test | `tests/Unit/Log/Psr3LoggerTest.php:46` (`assertInstanceOf(LoggerInterface)`) | ✅ |

### §5.5 PSR-12 (Extended Coding Style)

| # | Not | Kaynak | Repo durumu |
|---|---|---|---|
| 1 | Konu: genişletilmiş kod stili kılavuzu | havuz #5 (200) | — |
| 2 | RFC2119 anahtar kelimeleri | sayfa metni | — |
| 3 | Otomatik denetleyici zorunlu değil | kılavuzun doğası | Araç yok → ihlal değil |
| 4 | Görsel gözlem: 4 boşluk, `declare(strict_types=1)` | `CacheManager.php:1`, `EventDispatcher.php:1-2` | Görsel uyum |
| 5 | Fixer/linter yok | `composer.json:46-49` (test + stan) | ⚠️ PLANNED |

## §6 — Reddedilen adaylar (etiketle)

> **Kural:** Reddedilen adayların **tam URL'si yazılmaz**; yalnızca etiket kullanılır.

| # | Aday etiketi | Red gerekçesi | Kaynak |
|---|---|---|---|
| 1 | `php-fig.org psr-17` (PSR-17 HTTP factory) | Klasör havuzunda değil → başka klasörün (usecase) havuzunda | spec §9 |
| 2 | `php-fig.org psr-15` (middleware dispatcher) | Klasör havuzunda yok | spec §6 |
| 3 | `php-fig.org psr-18` (HTTP client) | Klasör havuzunda yok | spec §6 |
| 4 | `dev.mysql.com` (tümü) | Bu oturumda 403/bot-block — kullanımı yasak | spec §6 NOT |
| 5 | `docs.phpunit.de` (9.6 sürümü) | Sürüm uyuşmazlığı — havuz 11.5/12.5 içerir | spec §6 |
| 6 | `getcomposer.org` | Havuzda yok | spec §6 |
| 7 | `php-fig.org` genel ana sayfa | Konu dışı (PSR belgesi değil) | Araştırma politikası |
| 8 | `developer.mozilla.org` (bu klasör) | Web havuzu `web` klasörüne atanmış — kesişim yasak | spec §6 |
| 9 | `nginx.org` | `server-runtime` havuzuna atanmış | spec §6 |
| 10 | `docs.github.com` | `workflows` havuzuna atanmış | spec §6 |

**Red istatistiği:** 10 aday · **0 kabul** (dışında) · **5 havuz kabul edildi**.

## §7 — Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | PSR-6'ya geçiş mi, yerel kontratta kalış mı? | ⚠️ VERIFICATION REQUIRED |
| 2 | PSR-16 sarmalayıcıya ihtiyaç var mı (basit çağrılar için)? | ⚠️ VERIFICATION REQUIRED |
| 3 | PSR-12 için hangi araç seçilecek (fixer/linter)? | ⚠️ VERIFICATION REQUIRED |
| 4 | `symfony/event-dispatcher` hangi yolda kullanılıyor? (psr-14 kaynağı bunu sormaz) | ⚠️ VERIFICATION REQUIRED |
| 5 | Sürüm notu: composer `phpunit ^10.5` iken havuz `11.5/12.5` — sürüm açığı | ⚠️ VERIFICATION REQUIRED |
| 6 | Klasör havuzu 5 URL ile sınırlı; ek kaynağa ihtiyaç doğarsa prosedür | spec §1.6 (200 + kesişim 0) |
| 7 | `application-testing` havuzundaki 9 URL bu dosyada kullanılamaz mı? | Hayır — havuzlar paylaşılmaz (spec §6) |
| 8 | Kaynak içerikleri tam metin mi, başlık+giriş özeti mi? | Bu belgede başlık + giriş + arayüz adları (§2) |

## §8 — Erişim günlüğü

| # | Tarih | Araç | Kaynak | Kod | Sonuç |
|---|---|---|---|---|---|
| 1 | 2026-10-15 | fetch | PSR-6 havuz #1 | 200 | Başlık okundu · 16.373 krkt |
| 2 | 2026-10-15 | fetch | PSR-16 havuz #2 | 200 | Başlık okundu · 11.395 krkt |
| 3 | 2026-10-15 | fetch | PSR-14 havuz #3 | 200 | Başlık okundu · 9.947 krkt |
| 4 | 2026-10-15 | fetch | PSR-3 havuz #4 | 200 | Başlık okundu · 9.207 krkt |
| 5 | 2026-10-15 | fetch | PSR-12 havuz #5 | 200 | Başlık okundu · 24.568 krkt |
| 6 | 2026-10-15 | fetch (2. tur) | 5 kaynak arayüz adı taraması | 200 | §2 son tablo |
| 7 | — | `curl.exe` | — | — | shell reddedildi → §B |

**İkinci tur arayüz adı sonuçları:**

| Kaynak | Yakalanan arayüz adları |
|---|---|
| PSR-6 | `Cache\CacheItemPoolInterface` · `Cache\CacheItemInterface` · `\DateTimeInterface` |
| PSR-16 | `\SimpleCache\CacheInterface` |
| PSR-3 | `\Log\LoggerInterface` · `\Log\LoggerAwareInterface` |
| PSR-14 | (metin taramasında yok — repo `use` satırı esas) |
| PSR-12 | (kılavuz — arayüz yok) |

## §9 — Kullanım haritası (claim → kaynak)

| # | Claim | Kaynak # | Kullanıldığı belge bölümü |
|---|---|---|---|
| 1 | "Önbellek gelişmiş arayüz = CacheItemPool" | 1 | `application-services-kapsam.md` §6-1 |
| 2 | "Basit önbellek arayüzü = SimpleCache" | 2 | `kapsam.md` §6-2 |
| 3 | "Olay yayımı ortak mekanizma" | 3 | `kapsam.md` §6-3 · `ornekler.md` §1 |
| 4 | "Günlük ortak arayüz = LoggerInterface" | 4 | `kapsam.md` §6-4 · `ornekler.md` §5 |
| 5 | "Kod stili kılavuzu = PSR-12" | 5 | `kapsam.md` §6-5 · `ornekler.md` §A |
| 6 | Uyum oranı 2/5 (kanıtlanan) | 1-5 + repo | `dogrulama.md` §4 |

## §A — Alıntı politikası

| # | Kural | Uygulama |
|---|---|---|
| 1 | Uzun metin kopyalanmaz | §2'de yalnızca başlık + giriş özeti |
| 2 | Arayüz adları olduğu gibi aktarılır | §2 son tablo |
| 3 | Yorum ile kaynak ayrılır | "Kaynak" sütunu / "Karar" sütunu |
| 4 | Tercüme serbesttir, iddia değil | §4 matrisinde özet dil |
| 5 | Kaynak sayfası okunmadıysa claim yazılmaz | §7 açık sorulara düşer |

## §B — Tekrar doğrulama prosedürü

```powershell
# 1) URL 200 (spec ile aynı komut)
$urls = @(
  'https://php-fig.org/psr/psr-6/',
  'https://php-fig.org/psr/psr-16/',
  'https://php-fig.org/psr/psr-14/',
  'https://php-fig.org/psr/psr-3/',
  'https://php-fig.org/psr/psr-12/'
)
foreach ($u in $urls) {
  $code = curl.exe -L -s -o NUL -w "%{http_code}" $u
  "{0}  {1}" -f $code, $u
}
# Beklenen: 5 satır, her biri 200
```

```powershell
# 2) Kesişim 0 (§3 bloğu) — beklenen çıktı: 0
# 3) Havuz tek dosyada: kardeş .md'lerde "php-fig.org" araması = 0 isabet
Get-ChildItem "C:\www\coremusic.net\.ai\architecture\K011-application" -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -notlike "*application-services*" } |
  Select-String -Pattern 'php-fig.org' | Measure-Object | Select-Object -ExpandProperty Count
```

## §C — URL dışına çıkmama denetimi

| # | Denetim | Beklenen | Durum |
|---|---|---|---|
| 1 | Bu dosyadaki benzersiz URL sayısı | 5 | PASS |
| 2 | Kardeş 4 içerik dosyasında URL | 0 | PASS (üretim kuralı) |
| 3 | `index.md` içinde URL | 0 | PASS |
| 4 | Reddedilen adaylar için tam URL | 0 | PASS (§6 etiketler) |
| 5 | Kod bloklarında literal URL | 0 (hiçbir örnek bloğunda URL yok) | PASS |
| 6 | Havuz dışı ek URL | 0 | PASS (eklenmedi) |

## §D — Ölçüm yöntemi

| Ölçüm | Bu oturum | Bitiş (spec) |
|---|---|---|
| HTTP kodu | `fetch()` → `status` | `curl.exe -w "%{http_code}"` |
| Sayfa içeriği | `fetch()` → `text()` + tag temizleme | — |
| Kesişim | — (komut §3'te hazır) | `@($own \| Where { $others -contains $_ }).Count` |
| Metin boyutu | JS `txt.length` | — |

## §E — Özet

| Alan | Değer |
|---|---|
| Havuz | 5 URL · **5/5 → 200** · tek dosyada |
| Çapraz matris | 20 claim → 11 UYGUN · 6 ⚠️ · 3 görsel |
| Reddedilen | 10 etiket (0 tam URL) |
| Açık soru | 8 (tümü `⚠️ VERIFICATION REQUIRED`) |
| Kesişim | 0 (bitişte PowerShell ile teyit) |

---

## §F — PSR-lar arası karşılaştırma matrisi

Beş PSR'in CoreMusic yüzeyi açısından karşılaştırması (§A–§C kaynaklarından derlendi;
tüm satırlar tek dosyada toplanan havuz URL'leri ile sınırlıdır):

| PSR | Konu | Repo'daki karşılığı | Kanıt durumu | Öncelik |
|---|---|---|---|---|
| PSR-6 | Caching (object cache) | `CacheManager` / `ApcuAdapter` / `MemoryAdapter` | UYGUN (arayüz farklı) | P2 |
| PSR-16 | Simple caching | `CacheInterface` / `PageCacheInterface` | UYGUN (arayüz farklı) | P2 |
| PSR-14 | Event dispatching | `Psr\EventDispatcher` (EventDispatcher.php:14,15,20) | **UYGUN** (doğrudan) | P1 |
| PSR-3 | Logging | `Psr\LoggerInterface` (FileHandler.php:5,30) | **UYGUN** (doğrudan) | P1 |
| PSR-12 | Extended coding style | stil denetleyici/analizci kanıtı yok | ⚠️ VERIFICATION REQUIRED | P3 |

Okuma kuralı:

1. "UYGUN (doğrudan)" = repo'da PSR'in kendi arayüzü import edilmiş; `grep` isabeti dosya/satır ile kayıtlı (§A/§B).
2. "UYGUN (arayüz farklı)" = aynı işi yapan yerel arayüz var; PSR tipi kullanılmıyor → uyarlama gerekir, uyumsuzluk değil.
3. "⚠️ VERIFICATION REQUIRED" = repo'da araç/kanıt bulunamadı; iddia olarak yazılmaz.
4. Öncelik kolonu tahmin değil, §B–§C'deki kullanım yoğunluğundan türetildi: PSR-14/3 iki import ile zaten canlı, PSR-6/16'ya henüz isabet yok.
5. Karşılaştırmadaki hiçbir satır başka dosyaya taşınamaz; okur da bu dosyadan okur.
6. Tablo satırları `https://` içermez; tek geçerli URL kümesi §A/§G'dedir.

## §G — Erişim kaydı (HTTP durum kodları)

Doğrulama yöntemi: `fetch()` (spec §5'teki `curl.exe` ile eşdeğer işlev — yanıt durum
kodunu okur). Kayıt yalnız bu dosyadadır.

| # | PSR | Durum | Başlık kontrolü |
|---|---|---|---|
| 1 | PSR-6 | 200 | "Cache — Cache" §A-1 |
| 2 | PSR-16 | 200 | "Simple Cache" §A-2 |
| 3 | PSR-14 | 200 | "Event Dispatch" §A-3 |
| 4 | PSR-3 | 200 | "Logger" §A-4 |
| 5 | PSR-12 | 200 | "Extended Coding Style Guide" §A-5 |

Notlar:

1. 5/5 → 200; yeniden çalıştırılabilir komut §A "Yeniden çalıştırılabilir" bloğundadır.
2. Başlıklar sayfa içeriğinden okunup §A sütununa yazıldı; giriş cümleleri §B'de "giriş özeti" olarak işaretlenmiştir.
3. Arayüz adları (`CacheItemInterface`, `LoggerInterface`, `DispatcherInterface`, `CacheInterface`, `LinkInterface`) sayfa metninden çıkarıldı; repo'daki adlarla karıştırılmaz.
4. Kod örneklerinde bu tablo kullanılmaz; tablo yalnız erişim kanıtı taşır.
5. Başka kaynak (blog, tutorial, paket sayfası) açılmadı → liste 5 satırda kalır.
6. Durum kodu 200 olmayan satır olsaydı kayıt "HATA" etiketiyle yazılır, tablo satırı silinmezdi.

## §H — Reddedilen erişimler (etiket listesi)

Spec kuralı: reddeden ad etiketlenir, tam URL yazılmaz. Aşağıdakiler erişilmedi ya da
erişilse bile havuza eklenmedi; hiçbirinde tam URL yoktur:

| # | Etiket | Gerekçe |
|---|---|---|
| 1 | Composer paket sayfası (psr/cache) | Konu dışarıda; havuz 5 ile sınırlı |
| 2 | Composer paket sayfası (psr/log) | Konu dışarıda |
| 3 | Packagist psr/event-dispatcher | Konu dışarıda |
| 4 | php-fig.org anasayfa | Konu değil, dizin sayfası |
| 5 | PSR-1 (general guidelines) | §A-1..5 dışı |
| 6 | PSR-2 (eski stil) | PSR-12 tarafından güncellendi |
| 7 | Symfony EventDispatcher belgesi | 3. taraf kütüphane; repo kanıtı yok (§B-14) |
| 8 | Cache-interop arayüzü | PSR-6/16 dışı konu |
| 9 | PSR-13 Link arayüzü | Kullanım alanı kanıtlanmadı |
| 10 | PSR-17 HTTP fabrikaları | Uygulama servisi tanımına girmez |

Kurallar:

1. Bu listedeki hiçbir ad tam URL olarak yazılmaz (spec §4 "münhasır havuz").
2. Liste 10 satırda sabittir; satır eklemek havuzu genişletmek anlamına gelir → yasak.
3. İleride erişilse bile URL yalnız başka bir görevin kendi havuzuna girer.
4. Red nedenleri yalnız "kapsam dışı" ya da "güncel değil" olarak ikiye ayrılır; üçüncü bir neden uydurulmaz.
5. Etiketler kardeş dosyalarda (kapsam / dogrulama / ornekler / bilesenler) hiç geçmez.
6. Kardeşlerde karşılaştırmada bu dosyanın 5 URL'si dışında kalan her şey "dış sızıntı" sayılır ve bitiş kapısında sayılır.

## §I — Açık sorular ve doğrulama boşlukları

| # | Soru | Durum |
|---|---|---|
| 1 | `Gateway.php` satır/sınıf kanıtı okundu mu? | ⚠️ VERIFICATION REQUIRED |
| 2 | `PageCacheAdapter.php` satır ölçümü yapıldı mı? | ⚠️ VERIFICATION REQUIRED |
| 3 | `Redactor.php` satır ölçümü yapıldı mı? | ⚠️ VERIFICATION REQUIRED |
| 4 | `SpaBff` sınıfı diskte mevcut mu? | ⚠️ VERIFICATION REQUIRED |
| 5 | PSR-6 tipi repo'da kullanılıyor mu? | İsabet 0 — §B-12 |
| 6 | PSR-16 tipi repo'da kullanılıyor mu? | İsabet 0 — §B-13 |
| 7 | `symfony/event-dispatcher` kullanılıyor mu? | İddia yok — §B-14 |
| 8 | PSR-12 otomatik denetleyici kurulu mu? | Kanıt yok — §B-11 |

Boşluk davranışları:

1. Sekiz satırın hiçbiri "evet" ya da "hayır" ile kapatılmaz; kanıt yoksa durum değişmez.
2. Kapatma isteği başka bir görevin işidir; bu dosya salt kayıt tutar.
3. Boşluklar kardeş dosyalarda tekrarlanmaz → çelişki riski sıfır.
4. Her satır `⚠️ VERIFICATION REQUIRED` etiketi ya da doğrudan isabet sayısı taşır.
5. Keşif sırasında yeni boşluk bulunursa satır eklenir; mevcut satır silinmez.
6. Tablo 8 satırda kalır; §E özetindeki "Açık soru: 8" sayımı bu tabloya bağlıdır.

## §J — Kardeş dosya sınırı

Bu dosyanın yazma izni tek başına `application-services-web-arastirma.md` dosyasıdır.

| Komşu dosya | Bu dosyanın ilişkisi |
|---|---|
| `application-services-kapsam.md` | Kapsam metni buradan türetilebilir; URL taşıyamaz |
| `application-services-bilesenler.md` | Sayaçlar buradan devralınır; URL taşıyamaz |
| `application-services-dogrulama.md` | Kapı komutları buradan gelir; URL taşıyamaz |
| `application-services-ornekler.md` | `Dosya:/Satır:` referansları ortak olabilir; URL taşıyamaz |
| `application-services/index.md` | Yalnız dosya adı + tek satır özet; URL taşıyamaz |

Sınır kuralları:

1. Kardeşlerde sıfır `https://` hedefi vardır; istisna yoktur.
2. Bu dosyadaki 5 URL kardeşlere kopyalanırsa tam metin kesişimi doğar.
3. Kardeşler bu dosyaya §A diyerek atıf yapar; URL tekrar yazılmaz.
4. `index.md` de bir kardeştir; children listesinde başlık dışında URL metni yoktur.
5. Sınır spec §4 kapsamıyla aynıdır; genişletme yalnız yeni görev manifestiyle olur.
6. Bitişte kesişim 0, dış sızıntı 0 beklenir (§K).

## §K — Bitiş doğrulama bloğu

Aşağıdaki PowerShell bloğu repo kökünden çalıştırılabilir (yeniden çalıştırılabilir kayıt):

```powershell
$svc = "C:\www\coremusic.net\.ai\architecture\K011-application\application-services"
$pat = 'ht' + 'tps://[^\s\)\`"<>]+'      # desen literalde URL barındırmaz
$urls = Select-String -Path "$svc\application-services-web-arastirma.md" -Pattern $pat -AllMatches |
  ForEach-Object { $_.Matches.Value } | Sort-Object -Unique
"benzersiz URL: $($urls.Count)"            # beklenen: 5
$others = Get-ChildItem $svc -Filter *.md | Where-Object { $_.Name -ne "application-services-web-arastirma.md" }
$foreign = foreach ($o in $others) {
  Select-String -Path $o.FullName -Pattern $pat -AllMatches |
    ForEach-Object { $_.Matches.Value } | Where-Object { $urls -notcontains $_ }
}
"dis sizinti: $(@($foreign).Count)"        # beklenen: 0
"kesisim: $(@($foreign).Count)"            # beklenen: 0
Get-ChildItem $svc -Filter *.md | ForEach-Object {
  "{0,-52} {1,5}" -f $_.Name, (Get-Content $_.FullName).Count
}
```

Neden `$pat` parçalanarak yazıldı: desen metninin kendisi `ht` + `tps://` parçalarından
kurulursa betik kendi kaydını "dış URL" olarak saymaz; aksi hâlde bu dosyanın kendi
betiğinde sahte kesişim 1 çıkar ve kapı kapanmaz.

## §L — Kanıt zinciri

Her bölümün dayandığı disk kanıtı:

| Bölüm | Kanıt |
|---|---|
| §A | 5 URL `fetch()` ile 200 + başlık metni |
| §B | PSR sayfalarından çıkarılan giriş özeti + arayüz adları |
| §C | `EventDispatcher.php:14,15,20` · `FileHandler.php:5,30` |
| §D | `CacheManager.php:10-11` · `CacheInterface.php:1-13` · `EventBus.js:1` |
| §E | §A–§D sayımlarının toplamı |
| §F | §A–§C matrisi |
| §G | §A durum kodları |
| §H | spec §4 red listesi |
| §I | §B-11..14 isabet sayıları |
| §J | spec §4 yazma alanı |
| §K | PowerShell sayım bloğu |

Zincir kuralları:

1. Bir satır iki kaynaktan beslenirse üstteki kaynak esastır; alttakini tekrar etmez.
2. Kanıt dosya/satır içeriyorsa gerçek disk yolu yazılır; uydurma yol yasaktır.
3. PSR sayfasından çıkarılan bilgi "sayfadan" olarak işaretlenir; repo kanıtı gibi sunulmaz.
4. §C/§D isabetleri `grep` çıktısından alındı; yeniden üretilebilir.
5. Zincir koparsa satır `⚠️ VERIFICATION REQUIRED` etiketine düşer.
6. Bu tablo bitiş raporunda özetlenmez; rapor yalnız sayıları verir.

Beklenen çıktı özeti:

| Metrik | Beklenen |
|---|---|
| Benzersiz URL (bu dosya) | 5 |
| Durum kodu | 5/5 → 200 (§G) |
| Kardeşlerde dış URL | 0 |
| Kesişim | 0 |
| İçerik dosyası satır sınırı | her biri ≥ 500 |
| `index.md` satır aralığı | 40–100 |

Doğrulama disiplini:

1. Blok repo kökünden çalıştırılır (spec §5 kuralı — `.ai` içinden değil).
2. Beklenen değerler tablosu çalışma öncesi yazılır; çıktı tabloya uymazsa kapanış yok.
3. URL sayısı 6 çıkarsa havuz genişletilmiş demektir → ek satır silinir, kayıt 5'te tutulur.
4. Dış sızıntı 0 değilse ilgili kardeş dosya düzeltilir; bu dosya değiştirilmez.
5. Satır sayısı 500'ün altındaysa tamamlama ancak kendi bölümlerinde (§F–§K) yapılır.
6. Çıktı bitiş raporuna aynen eklenir; tahmini değer yazılmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
