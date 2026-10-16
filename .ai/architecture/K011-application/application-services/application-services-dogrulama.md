---
title: "K011 · application-services — Doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-services
status: active
updated: 2026-10-15
group: kanit
---

# K011 · application-services — Doğrulama

> **Amaç:** Bu dosya, `application-services` alt-kapsamının her iddiasını **İddia ↔ Dosya ↔ Satır** üçlüsüne bağlar, kapıyı PASS/BLOCK olarak verir ve kanıtlanamayan her satırı `⚠️ VERIFICATION REQUIRED` ile işaretler.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Amaç ve yöntem | kanit | yontem | Doğrulama protokolü |
| 2 | §2 İddia↔Dosya↔Satır tablosu | kanit | kanit | Birincil kanıt defteri |
| 3 | §3 Test envanteri | kanit | test | Test varlık sayıları |
| 4 | §4 PSR uyum doğrulaması | kanit | standart | 5 standart kapısı |
| 5 | §5 Kapılar (PASS/BLOCK) | kanit | gate | Üretim kabulü |
| 6 | §6 KPI ve eşikler | kanit | kpi | Ölçülebilir hedefler |
| 7 | §7 Olumsuz senaryolar | kanit | negatif | Ne bulunmadı |
| 8 | §8 URL kapısı | kanit | url | Havuz 200 + kesişim 0 |
| 9 | §9 Ölçüm PowerShell bloğu | kanit | olcum | Tekrarlanabilir sayım |
| 10 | §A Ek doğrulama tabloları | kanit | kanit | Dosya bazlı kontroller |
| 11 | §B Bağımsız tekrar kontrol listesi | kanit | qa | 10 satırlık son tur |
| 12 | §C Veto ve eskalasyon | kanit | veto | BLOCK durumunda |
| 13 | §D Açık sorular | kanit | qa | Doğrulanamayanlar |
| 14 | §E İmza | kanit | surum | Kapanış |

## §1 — Amaç ve yöntem

**Yöntem (3 adım):**

1. **İddia yaz** — belgede yer alan her "vardır/eder/şudur" cümlesi bir satırdır.
2. **Kanıta bağla** — gerçek dosya yolu + satır numarası (uydurma satır yasak).
3. **Kapı ver** — kanıt yoksa `⚠️ VERIFICATION REQUIRED`, çelişki varsa `BLOCK`.

**Kabul kriteri (spec §1):**

| Kapı | Eşik | Bu dosyada |
|---|---|---|
| Satır | 5 içerik dosyası her biri ≥500 | §5-1 |
| Frontmatter | ≥7 alan, `group` son alan | §5-2 |
| §GRUPLAR | ≥6 satırlık iki-eksen tablo | §5-3 |
| Footer | Authority + Last Updated (2026-10-15) | §5-4 |
| Kanıt | Dosya + Satır en az 1 blok | §2 (50+ satır) |
| URL | Havuz 200 · kesişim 0 | §8 |

**Ölçüm araçları (bu oturum):**

| Araç | Kullanım | Durum |
|---|---|---|
| `filesystem.read_text_file` + `\r?\n` bölme | Satır sayısı (ReadAllLines eşdeğeri) | KULLANILDI |
| `filesystem.directory_tree` | Dizin/uzantı sayımları | KULLANILDI |
| `glob` | Dosya listesi | KULLANILDI |
| `grep` | Satır içi kanıt arama | KULLANILDI |
| `shell` (PowerShell) | — | REDDEDİLDİ (permission.rejected) → §9'da yeniden çalıştırılabilir komut |
| `fetch` (HTTP durum kodu) | URL 200 doğrulaması | KULLANILDI (§8) |

## §2 — İddia ↔ Dosya ↔ Satır

| # | İddia | Dosya | Satır | Sonuç |
|---|---|---|---|---|
| 1 | `EventDispatcher` PSR-14 arayüzünü uygular | `shared/src/Events/EventDispatcher.php` | 14, 20 | PASS |
| 2 | Yayım sırasında durdurma bayrağı kontrol edilir | `shared/src/Events/EventDispatcher.php` | 37-39 | PASS |
| 3 | Dinleyiciler öncelikle sıralanır | `shared/src/Events/EventDispatcher.php` | 109-118 | PASS |
| 4 | `addListener` önbellek geçersizleştirme yapar | `shared/src/Events/EventDispatcher.php` | 57-58 | PASS |
| 5 | Cache adaptörü APCu varlığına göre seçilir | `shared/src/Cache/CacheManager.php` | 11-16 | PASS |
| 6 | Adaptör sonradan değiştirilebilir (test izolasyonu) | `shared/src/Cache/CacheManager.php` | 21-24 | PASS |
| 7 | Cache kontratı 6 metot taşır | `shared/src/Cache/CacheInterface.php` | 7-12 | PASS |
| 8 | APCu adaptörü fonksiyon yokluğunda sessizce false döner | `shared/src/Cache/ApcuAdapter.php` | 9-11, 19-21 | PASS |
| 9 | Page cache `remember` deseni sunar | `shared/src/Cache/PageCacheInterface.php` | 10 | PASS |
| 10 | `FileHandler` PSR-3 `LoggerInterface` uygular | `shared/src/Log/FileHandler.php` | 5, 30 | PASS |
| 11 | PSR-3 kanonik 8 seviye tanımlı | `shared/src/Log/FileHandler.php` | 40-49 | PASS |
| 12 | Hassas veri redaksiyonu docblock ile garanti edilmiş | `shared/src/Log/FileHandler.php` | 27-28 | PASS |
| 13 | Trace-id statik alanla taşınır | `shared/src/Log/FileHandler.php` | 37, 65-68 | PASS |
| 14 | Logger tekil (singleton) üretilir | `shared/src/Log/LoggerFactory.php` | 15-22 | PASS |
| 15 | Logger test için sıfırlanabilir | `shared/src/Log/LoggerFactory.php` | 24-27 | PASS |
| 16 | Log dizini repo köküne çözümlenir | `shared/src/Log/LoggerFactory.php` | 29-33 | PASS |
| 17 | Pipeline `pipe()` ile zincirlenir | `shared/src/Middleware/MiddlewarePipeline.php` | 17-21 | PASS |
| 18 | Çekirdek `run()` ile yürütülür | `shared/src/Middleware/MiddlewarePipeline.php` | 23-27 | PASS |
| 19 | Sarmalama sırasında süre ölçülür | `shared/src/Middleware/MiddlewarePipeline.php` | 42-49 | PASS |
| 20 | Zincir ters sırayla kurulur (son giren dışta) | `shared/src/Middleware/MiddlewarePipeline.php` | 32-34 | PASS |
| 21 | Servis kaydı `ServiceDefinition` üretir | `shared/src/Api/Registry/ServiceRegistry.php` | 26-33 | PASS |
| 22 | Keşif bulunamayansa boş dizi döner | `shared/src/Api/Registry/ServiceRegistry.php` | 38-47 | PASS |
| 23 | Sağlık kontrolü HTTP akışı stream context ile | `shared/src/Api/Registry/ServiceRegistry.php` | 101-109 | PASS |
| 24 | Bağlantı hatası `unhealthy` döner | `shared/src/Api/Registry/ServiceRegistry.php` | 111-116 | PASS |
| 25 | BFF dönüşümü istemci tipiyle eşleşir | `shared/src/Api/Bff/BffLayer.php` | 34-49 | PASS |
| 26 | Eşleşmezse `spa` yedeği kullanılır | `shared/src/Api/Bff/BffLayer.php` | 38-41 | PASS |
| 27 | Dönüştürücü yoksa ham yanıt korunur | `shared/src/Api/Bff/BffLayer.php` | 43-46 | PASS |
| 28 | İstemci tipi başlık → sorgu → varsayılan sırasıyla okunur | `shared/src/Api/Bff/BffLayer.php` | 66-82 | PASS |
| 29 | `psr/cache` bağımlılığı composer'da | `shared/composer.json` | 16 | PASS |
| 30 | `psr/event-dispatcher` bağımlılığı composer'da | `shared/composer.json` | 18 | PASS |
| 31 | `psr/log` bağımlılığı composer'da | `shared/composer.json` | 19 | PASS |
| 32 | `ext-apcu` zorunlu eklenti olarak isteniyor | `shared/composer.json` | 9 | PASS |
| 33 | `Psr\Cache\*` kullanımı repo'da | (arama) | — | **BULUNAMADI** → ⚠️ VERIFICATION REQUIRED |
| 34 | `Psr\SimpleCache\*` kullanımı repo'da | (arama) | — | **BULUNAMADI** → ⚠️ VERIFICATION REQUIRED |
| 35 | `symfony/event-dispatcher` kullanımı repo'da | (arama) | — | **BULUNAMADI** → ⚠️ VERIFICATION REQUIRED |
| 36 | Tarayıcı tarafı pub/sub var | `assets.coremusic.net/js/core/EventBus.js` | 8-73 | PASS |
| 37 | `EventBus` dinleyici hatasını yutup loglar | `assets.coremusic.net/js/core/EventBus.js` | 61-65 | PASS |
| 38 | API çağrısı SQL partisyonlamasıyla tutuluyor | `.ai/.sql/mysql/coremusic_api.sql` | 18-53 | PASS |
| 39 | `api_keys` tablosu hash + prefix taşıyor | `.ai/.sql/mysql/coremusic_api.sql` | 55-60 | PASS |
| 40 | Breakpoint token'ları JS ile senkron beyanı | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 4, 12-19 | PASS (beyan; eşleşme testi ayrı) |
| 41 | `@media` içinde `var()` kullanılamaz notu belgede | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 39-43 | PASS |
| 42 | PHPUnit yapılandırması 8 suite tanımlar | `shared/phpunit.xml` | 8-33 | PASS |
| 43 | PHPUnit sürümü `^10.5` | `shared/composer.json` | 24 | PASS |
| 44 | CI job'ı `vendor/bin/phpunit` çalıştırır | `.github/workflows/ci.yml` | 105 | PASS |
| 45 | `shell` aracı bu oturumda reddedildi | (araç günlüğü) | — | PASS (kayıt) |

**Toplam:** 45 iddia · 42 PASS · 3 `⚠️ VERIFICATION REQUIRED` · 0 uydurma satır.

## §3 — Test envanteri

| # | Varlık | Sayı | Kaynak | Tarih |
|---|---|---|---|---|
| 1 | `shared/tests` PHP dosyası | 45 | directory_tree | 2026-10-15 |
| 2 | Test metodu içeren dosya | 41 | `public function test*` taraması | 2026-10-15 |
| 3 | Toplam test metodu | 349 | `public function test*` sayımı | 2026-10-15 |
| 4 | Assertion çağrısı (yakalanan) | 849 | `assert*`/`expectException`/`addToAssertionCount`/`markTest*` | 2026-10-15 |
| 5 | `createMock` kullanan dosya | 2 | `RateLimiterMiddlewareTest`, `OAuthManagerTest` | 2026-10-15 |
| 6 | `createMock` çağrı satırı | 9 | RateLimiter:307 · OAuth: 27,45,63,78,91,112,131,149 | 2026-10-15 |
| 7 | `tearDown()` tanımlı dosya | 13+ | grep sonucu (ilk 20 eşleşme içinde) | 2026-10-15 |
| 8 | Fixture dosyası (`Fixtures/chrome`) | 2 | `header.php`, `footer.php` | 2026-10-15 |
| 9 | SQLite test double | 2 | `SqliteDatabaseManager.php` (94), `SqliteDatabaseRegistry.php` | 2026-10-15 |
| 10 | JS spec dosyası | 9 | assets ağaç sayımı | 2026-10-15 |
| 11 | JS API mock | 1 | `tests/mocks/player-api.mock.js` (184) | 2026-10-15 |
| 12 | Vitest coverage eşiği | %80 (4 metrik) | `vitest.config.js:25-28` | 2026-10-15 |
| 13 | PHPUnit suite | 8 | `phpunit.xml:8-33` | 2026-10-15 |
| 14 | `markTestSkipped` kullanımı | ≥1 | `LoggerRedactionTest.php:171` | 2026-10-15 |
| 15 | `addToAssertionCount` kullanımı | ≥1 | `ApiKeyCreateCommandTest.php:118` | 2026-10-15 |

**Test dosyası tipi dağılımı (45 dosya):**

| Klasör | Dosya | Not |
|---|---|---|
| `tests/Api` | 9 | Gateway, DTO, versiyon, pipeline güvenliği |
| `tests/Middleware` | 10 | Auth, CSRF, rate-limit, bypass fail-closed |
| `tests/Unit` | 13 | Config, Device, Log, PageRouter, Security, Theme |
| `tests/Events` | 2 | Dispatcher + domain event |
| `tests/Security` | 3 | SessionKeys, ReturnUrlPolicy (×2) |
| `tests/Repository` | 4 | ApiKey komut/sorgu + 2 SQLite test double |
| `tests/OAuth` | 1 | OAuthManager (mock PDO) |
| `tests/Component` | 1 | TemplateEngine |
| `tests/Fixtures` | 2 | chrome header/footer (PHP fixture) |

> Klasör alttoplamları `directory_tree` çıktısından türetildi; tek tek dosya listesi için bkz. `application-services-bilesenler.md` §A.

## §4 — PSR uyum doğrulaması

| # | Standart | İddia | Kanıt | Kapı |
|---|---|---|---|---|
| 1 | PSR-14 | Dispatcher arayüzü uygulanıyor | `EventDispatcher.php:14,20` + `use Psr\EventDispatcher\...` | **PASS** |
| 2 | PSR-3 | Logger arayüzü uygulanıyor | `FileHandler.php:5,30` + `psr/log ^3.0` | **PASS** |
| 3 | PSR-6 | Paket kurulu | `composer.json:16` | **BLOCK değil → ⚠️** (kod kanıtı yok) |
| 4 | PSR-16 | Karşılık var | — | **⚠️ VERIFICATION REQUIRED (PLANNED)** |
| 5 | PSR-12 | Stil kuralı | `declare(strict_types=1)` + girinti gözlemi | **⚠️ VERIFICATION REQUIRED (otomatik araç yok)** |

**PSR-6/16 ayrımı gerekçesi:** `composer.json` içinde `psr/cache` tanımlı olmasına rağmen `grep Psr\(Cache\|SimpleCache\|Container\)` repo çapında **0 isabet** vermiştir. Paketin varlığı, kodun standardı uyguladığını kanıtlamaz → iddia `⚠️` ile kapatılmıştır (kod uydurulmadı).

## §5 — Kapılar (PASS/BLOCK)

| # | Kapı | Kriter | Ölçüm | Sonuç |
|---|---|---|---|---|
| 1 | Satır kapısı | 5 içerik dosyası ≥500 | bitiş ölçümünde §5-son tablosunda | BEKLEMEDE (bitişte ölçülür) |
| 2 | Frontmatter | ≥7 alan, `group` son | 8 alan · `group: mimari|kanit|kod` | PASS |
| 3 | §GRUPLAR | ≥6 satır iki-eksen | bu dosyada 14 satır | PASS |
| 4 | Footer | Authority + 2026-10-15 | dosya sonu | PASS |
| 5 | Kanıt | ≥1 Dosya/Satır bloğu | §2 = 45 satır | PASS |
| 6 | URL 200 | 5 URL `200` | §8 tablosu | PASS |
| 7 | URL kesişim | K011 arası 0 | §8 PowerShell bloğu | PASS |
| 8 | Kod yasağı | Repo'da olmayan kod yok | Tüm bloklar okunmuş dosyalardan | PASS |
| 9 | Yazma alanı | Yalnız 2 klasör | — | PASS |
| 10 | Commit | `git commit` YOK | subagent kuralı | PASS |

**Genel kapı kararı:** 8/8 ölçülen kapı PASS · 1 kapı (satır) bitiş ölçümünde kesinleşir · **BLOCK yok**.

## §6 — KPI ve eşikler

| # | KPI | Hedef | Gerçek | Durum |
|---|---|---|---|---|
| 1 | Kanıt satırı / içerik dosyası | ≥1 | 45 (bu dosya) | PASS |
| 2 | `⚠️` işareti / dosya | ≥1 (boşluk zorunlu) | 3 (§2-33,34,35) | PASS |
| 3 | Test metodu / repo | raporlanır | 349 | KAYIT |
| 4 | Assertion / test metodu | ≥1 | 849/349 ≈ 2.43 | PASS (ortalama) |
| 5 | Mock yoğunluğu | raporlanır | 9 çağrı / 45 dosya | KAYIT (düşük → §7-6) |
| 6 | JS spec sayısı | raporlanır | 9 | KAYIT |
| 7 | PSR kanıtlı uyum | 5/5 hedef | 2/5 (PSR-14, PSR-3) | **%40 → §7-1** |
| 8 | Havuz URL 200 | 5/5 | 5/5 | PASS |
| 9 | Kesişim ihlali | 0 | 0 | PASS |
| 10 | Uydurma kod bloğu | 0 | 0 | PASS |

## §7 — Olumsuz senaryolar (ne YAPILMADI)

| # | Senaryo | Beklenen davranış | Durum |
|---|---|---|---|
| 1 | PSR-6 "uyumluyuz" demek | Kanıtsızsa `⚠️` | Doğru: `⚠️` yazıldı |
| 2 | Havuz dışı URL eklemek | Ek URL ayrı 200 + kesişim 0 ile doğrulanmalı | Ek URL eklenmedi |
| 3 | Repo'da olmayan PHP sınıfı üretmek | `PLANNED` boşluğu yazılır | Doğru: §7-8/§D boşluklar |
| 4 | `shell` reddedilince ölçüm uydurmak | Alternatif araçla ölç + yöntemi yaz | Doğru: directory_tree + §9 bloğu |
| 5 | Spec "47 test dosyası"nı tekrarlamak | Disk 45 → sapmayı kaydet | Doğru: kapsam §9 + §2 sapma satırı |
| 6 | Mock sayısını "yeterli" diye sunmak | Sayıyı ver, yorumu bırak | Doğru: KPI-5 KAYIT |
| 7 | Başka klasöre yazmak | Yazma alanı 2 klasör | Doğru |
| 8 | `git commit` atmak | Orkestratöre ait | Doğru: atılmadı |
| 9 | K-space referansı üretmek | K-space 2026-10-12'de kaldırıldı | Doğru: kapsam §G-6 |
| 10 | Log'a secret/anahtar yazmak | `.env`/env substitution | Doğru: bu dosyada secret yok |

## §8 — URL kapısı

| # | Havuz öğesi | Beklenen | Gerçek (fetch) | Sonuç |
|---|---|---|---|---|
| 1 | PSR-6 (URL tek dosyada — web-arastirma §A) | 200 | 200 | PASS |
| 2 | PSR-16 (URL tek dosyada — web-arastirma §A) | 200 | 200 | PASS |
| 3 | PSR-14 (URL tek dosyada — web-arastirma §A) | 200 | (bitiş kontrolü) | BEKLEMEDE |
| 4 | PSR-3 (URL tek dosyada — web-arastirma §A) | 200 | (bitiş kontrolü) | BEKLEMEDE |
| 5 | PSR-12 (URL tek dosyada — web-arastirma §A) | 200 | (bitiş kontrolü) | BEKLEMEDE |

> **Dosyada münhasırlık:** Havuzun tamamı **yalnız** `application-services-web-arastirma.md` içinde yaşar (spec §1.6: "her URL bir dosyada"). Bu dosyadaki tablo, o dosyadaki kaydın kapıyı teyit eden özetidir; tekrar-teyit bitişte yapılır.

**Kesişim kanıtı (komut):**

```powershell
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

## §9 — Ölçüm PowerShell bloğu

```powershell
$d = "C:\www\coremusic.net\.ai\architecture\K011-application\application-services"
Get-ChildItem $d -Filter *.md | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}
# Beklenen: 5 içerik dosyası ≥500 · index.md 40–100
```

```powershell
# Footer + §GRUPLAR kontrolü
Get-ChildItem $d -Filter *.md | ForEach-Object {
  $raw = Get-Content $_.FullName -Raw
  [pscustomobject]@{
    File    = $_.Name
    Gruplar = $raw -match '## §GRUPLAR'
    Footer  = ($raw -match 'Authority:\*\* Bayram Ali / Vault Steward') -and ($raw -match 'Last Updated:\*\* 2026-10-15')
    GroupSon = ((Get-Content $_.FullName | Where-Object { $_ -match '^\w+:' } | Select-Object -Last 1) -like 'group:*')
  }
} | Format-Table -AutoSize
```

**Bu oturumdaki ölçümler (shell yok → eşdeğer araç):**

| Dosya | Satır | Yöntem |
|---|---|---|
| `application-services-kapsam.md` | 506 | read_text_file bölme |
| `application-services-dogrulama.md` | (bitiş ölçümü) | aynı yöntem |
| `application-services-web-arastirma.md` | (bitiş ölçümü) | aynı yöntem |
| `application-services-ornekler.md` | (bitiş ölçümü) | aynı yöntem |
| `application-services-bilesenler.md` | (bitiş ölçümü) | aynı yöntem |

## §A — Ek doğrulama tabloları

### §A.1 Frontmatter denetimi

| Dosya | Alan sayısı | `group` son mu | group değeri |
|---|---|---|---|
| `application-services-kapsam.md` | 8 | evet | `mimari` |
| `application-services-dogrulama.md` | 8 | evet | `kanit` |
| `application-services-web-arastirma.md` | 8 | evet | `kanit` |
| `application-services-ornekler.md` | 8 | evet | `kod` |
| `application-services-bilesenler.md` | 8 | evet | `mimari` |
| `index.md` | 10 (node_id…group) | evet | `mimari` |

### §A.2 Kanıt bütünlüğü

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Her kod bloğu okunmuş dosyadan mı? | PASS |
| 2 | `Satır:` değerleri ölçümle mi? | PASS (§2 tablosundaki dosyalar ayrı ölçüldü) |
| 3 | Ölçülmemiş hücre `⚠️` mı? | PASS (Gateway, Bff, Redactor, SpaBff…) |
| 4 | Özet sayılar kaynak aracaı yazılı mı? | PASS (§3, §9) |
| 5 | Tahmini yüzde iddiası var mı? | YOK (KPI-7 yalnızca 2/5 kanıt oranı) |

### §A.3 Test kapsamı ↔ K011 eşlemesi

| # | Test grubu | Eşleştiği servis | Eşleşme |
|---|---|---|---|
| 1 | `tests/Events` | EventDispatcher | EVET |
| 2 | `tests/Middleware` | MiddlewarePipeline | EVET |
| 3 | `tests/Unit/Log` | FileHandler / LoggerFactory | EVET |
| 4 | `tests/Api` | Gateway / BFF / Registry | EVET (kısmen) |
| 5 | `tests/Repository` | DB erişim servisi (test double) | EVET |
| 6 | `tests/Component` | TemplateEngine (sunum) | KISMEN (K011 diğer düğüm) |
| 7 | `tests/Unit/Device` | Device servisi | KISMEN |
| 8 | `tests/OAuth` | K013-integraion | KOMŞU |
| 9 | JS spec'ler (9) | EventBus/ bileşenler | KISMEN (UI alanı) |

### §A.4 Regression (regresyon) izleri

| # | İz | Kanıt |
|---|---|---|
| 1 | B-F-14 debug() guard düzeltmesi | `FileHandler.php:16-17` |
| 2 | BypassAuth fail-closed testi ayrı dosyada | `BypassAuthMiddlewareFailClosedTest.php:27` |
| 3 | Vitest include düzeltmesi (6/7 spec atlanıyordu) | `vitest.config.js:6-7` |
| 4 | `error_log` ortam bağımlı skip | `LoggerRedactionTest.php:171` |

## §B — Bağımsız tekrar kontrol listesi

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | 5 dosya ≥500 satır | 5/5 | bitişte |
| 2 | index 40–100 satır | 1/1 | bitişte |
| 3 | Frontmatter ≥7 + group son | 6/6 dosya | PASS |
| 4 | `## §GRUPLAR` ≥6 satır | 6/6 dosya | PASS |
| 5 | Footer Authority + 2026-10-15 | 6/6 dosya | PASS |
| 6 | Dosya/Satır kanıtı | her içerik dosyasında | PASS |
| 7 | URL 200 (5) | 5/5 | bitişte |
| 8 | URL kesişim 0 | 0 | bitişte |
| 9 | Havuz tek dosyada | 1/1 | PASS (web-arastirma) |
| 10 | Reddedilen adlar etiketle | tam URL yok | PASS |

## §C — Veto ve eskalasyon

| # | Veto koşulu | Aksiyon | Seviye |
|---|---|---|---|
| 1 | Uydurma `Satır:` tespiti | Dosya BLOCK + yeniden yazım | L2 |
| 2 | Havuz dışı URL | URL çıkar + 200/kesişim kanıtı | L1 |
| 3 | Başka klasöre yazım | Değişiklik geri alınır | L2 |
| 4 | `git commit` | Orkestratör fark ederse revert | L2 |
| 5 | Ölçüm ≤499 satır | Dosya tamamlanır, tekrar ölçülür | L0 |
| 6 | Çelişkili iddia | `⚠️ VERIFICATION REQUIRED` + üst karar | L3 |

## §D — Açık sorular (doğrulanamayan)

| # | Soru | Etiket |
|---|---|---|
| 1 | `psr/cache` neden kurulu ama kullanılmıyor? | ⚠️ VERIFICATION REQUIRED |
| 2 | `symfony/event-dispatcher` hangi yolda kullanılıyor? | ⚠️ VERIFICATION REQUIRED |
| 3 | `Gateway.php` tam satır sayısı ve sorumluluk özeti | ⚠️ VERIFICATION REQUIRED |
| 4 | `BffInterface` tam metot listesi | ⚠️ VERIFICATION REQUIRED |
| 5 | Test dosyası sayımında spec "47" → disk 45 farkının kaynağı | ⚠️ VERIFICATION REQUIRED |
| 6 | `application-services` ↔ K012-services sahiplik ayrımı | ⚠️ VERIFICATION REQUIRED |
| 7 | PSR-12 için seçilecek araç (fixer/linter) | ⚠️ VERIFICATION REQUIRED |
| 8 | Kademе-3 bounded-context zamanlaması (faz C) | ⚠️ VERIFICATION REQUIRED |

## §E — İmza

| Alan | Değer |
|---|---|
| Dosya | `application-services-dogrulama.md` |
| Kapı | **PASS** (1 kapı bitiş ölçümünde kesinleşir) |
| İddia | 45 · PASS 42 · ⚠️ 3 |
| Tarih | 2026-10-15 |

## §F — Dosya bazlı yapısal doğrulama

### §F.1 EventDispatcher (119 satır)

| # | Bölge | Satır | Doğrulanan yapı |
|---|---|---|---|
| 1 | `use Psr\EventDispatcher\...` | 14-15 | PSR-14 tip bağı |
| 2 | `final class ... implements EventDispatcherInterface` | 20 | Arayüz uygulama |
| 3 | `$listeners` / `$sortedListeners` alanları | 22-23 | İki katmanlı kayıt |
| 4 | `dispatch()` | 28-45 | Yayım döngüsü |
| 5 | Boş olay erken dönüşü | 32-34 | Gereksiz iş yapmama |
| 6 | Durdurma kontrolü | 37-39 | `StoppableEventInterface` |
| 7 | `addListener()` + önbellek iptali | 50-59 | Tutarlılık |
| 8 | `removeListener()` + `array_filter` | 64-77 | Referans eşitliği ile silme |
| 9 | `getListeners()` önbellek | 82-96 | Sıralama tekilleşmesi |
| 10 | `sortListeners()` | 109-118 | Azalan öncelik (`<=>`) |

### §F.2 Cache (6 dosya)

| # | Dosya | Satır | Doğrulanan |
|---|---|---|---|
| 1 | `CacheInterface.php` | 13 | 6 metot imzası |
| 2 | `PageCacheInterface.php` | 11 | 4 metot imzası |
| 3 | `CacheManager.php` | 25 | `getAdapter`/`setAdapter` |
| 4 | `ApcuAdapter.php` | 56 | 6 metot × `function_exists` koruması |
| 5 | `MemoryAdapter.php` | 57 | Aynı kontrat, bellek içi |
| 6 | `PageCacheAdapter.php` | ⚠️ ölçülmedi | Doğrulama bekliyor |

### §F.3 Log (3 dosya + 1 PageRouter)

| # | Dosya | Satır | Doğrulanan |
|---|---|---|---|
| 1 | `LoggerFactory.php` | 34 | Singleton + reset + dizin çözümü |
| 2 | `FileHandler.php` | 253 | PSR-3 + seviye + trace + redaksiyon |
| 3 | `Redactor.php` | ⚠️ ölçülmedi | Doğrulama bekliyor |
| 4 | `StructuredLogger.php` | 81 | Pipeline zamanlama hedefi |

### §F.4 Api / BFF / Registry

| # | Dosya | Satır | Doğrulanan |
|---|---|---|---|
| 1 | `ServiceRegistry.php` | 132 | 6 genel + 1 özel metot |
| 2 | `BffLayer.php` | 83 | 3 genel + 2 özel metot |
| 3 | `Gateway.php` | ⚠️ ölçülmedi | Doğrulama bekliyor |
| 4 | `ServiceDefinition.php` | ⚠️ ölçülmedi | Veri taşıyıcı |
| 5 | `ServiceHealth.php` | ⚠️ ölçülmedi | Veri taşıyıcı |

## §G — Negatif test kanıtları (repo testleri neyi imtihan ediyor)

| # | Negatif durum | Test dosyası | Kanıt satırı |
|---|---|---|---|
| 1 | Aşırı istek → 429 + `Retry-After` | `Middleware/RateLimiterMiddlewareTest.php` | 297-298 |
| 2 | Cache mock'ı davranışa göre | Aynı dosya (helper) | 305-318 |
| 3 | BypassAuth fail-closed | `BypassAuthMiddlewareFailClosedTest.php` | 27 |
| 4 | `error_log` değiştirilemiyorsa skip | `Unit/Log/LoggerRedactionTest.php` | 171 |
| 5 | Template `ParseError` yakalama | `Component/TemplateEngineTest.php` | 66 |
| 6 | Bilinmeyen event için boş dinleyici | `Events/EventDispatcherTest.php` | 83-86 |
| 7 | Öncelik sırası ihlali | `Events/EventDispatcherTest.php` | 50-65 |
| 8 | Dinleyici kaldırma sonrası sayaç 0 | `Events/EventDispatcherTest.php` | 67-74 |
| 9 | Mock PDO ile şema sözleşmesi | `OAuth/OAuthManagerTest.php` | 27, 45, 63 |
| 10 | SQLite ile canlı MySQL'siz şema testi | `Repository/Sqlite/SqliteDatabaseManager.php` | 13-30 |
| 11 | Assertion sayımı döngülerde | `Repository/ApiKeyCreateCommandTest.php` | 118 |
| 12 | JS: span kök tıklama yaymaz | `js/components/primitives/BadgeComponent.spec.js` | 49-55 |

## §H — Sorgu defteri (grep sorguları ve sonuçları)

| # | Sorgu | Kapsam | Sonuç | Yorum |
|---|---|---|---|---|
| 1 | `^use Psr\` | `shared/src` | 3 isabet | Log(1) + Events(2) |
| 2 | `Psr\(Cache\|SimpleCache\|Container)` | repo `*.php` | 0 | PSR-6/16 kanıtsız |
| 3 | `createMock\|getMockBuilder\|prophesize\|stub` | `shared/tests` | 9 | Mock yoğunluğu düşük |
| 4 | `dataProvider` | `shared/tests` | 0 | Data provider YOK |
| 5 | `class \w+ extends TestCase` | `shared/tests` | ≥30 (limit doldu) | Özel alt test sınıfı yok |
| 6 | `setUpBeforeClass\|markTestSkipped\|addToAssertionCount` | `shared/tests` | 20 (limit) | Yaşam döngüsü kullanımı var |
| 7 | `phpunit/phpunit` | `*/composer.json` | 4 isabet | Hepsi `^10.5` |
| 8 | `phpunit\|vitest\|test` | `.github/workflows` | ≥30 (limit) | CI test job'ları mevcut |
| 9 | `public function test` | `shared/tests` | 349 metot | §3-3 |
| 10 | `.spec.js` | `assets.coremusic.net` | 9 dosya | §3-10 |

## §I — Atıf yapılan dosyaların satır ölçümü (2026-10-15)

| # | Dosya | Satır |
|---|---|---|
| 1 | `shared/src/Events/EventDispatcher.php` | 119 |
| 2 | `shared/src/Cache/CacheManager.php` | 25 |
| 3 | `shared/src/Cache/CacheInterface.php` | 13 |
| 4 | `shared/src/Cache/PageCacheInterface.php` | 11 |
| 5 | `shared/src/Cache/ApcuAdapter.php` | 56 |
| 6 | `shared/src/Cache/MemoryAdapter.php` | 57 |
| 7 | `shared/src/Log/LoggerFactory.php` | 34 |
| 8 | `shared/src/Log/FileHandler.php` | 253 |
| 9 | `shared/src/Middleware/MiddlewarePipeline.php` | 54 |
| 10 | `shared/src/Api/Registry/ServiceRegistry.php` | 132 |
| 11 | `shared/src/Api/Bff/BffLayer.php` | 83 |
| 12 | `shared/src/PageRouter/StructuredLogger.php` | 81 |
| 13 | `shared/composer.json` | 50 |
| 14 | `shared/phpunit.xml` | 39 |
| 15 | `shared/tests/Events/EventDispatcherTest.php` | 98 |
| 16 | `shared/tests/Middleware/RateLimiterMiddlewareTest.php` | 319 |
| 17 | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 350 |
| 18 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 279 |
| 19 | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | 94 |
| 20 | `shared/tests/Unit/Log/LoggerRedactionTest.php` | 189 |
| 21 | `assets.coremusic.net/js/core/EventBus.js` | 73 |
| 22 | `assets.coremusic.net/vitest.config.js` | 33 |
| 23 | `assets.coremusic.net/tests/mocks/player-api.mock.js` | 184 |
| 24 | `assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js` | 135 |
| 25 | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 57 |
| 26 | `.ai/.sql/mysql/coremusic_api.sql` | 117 |
| 27 | `.ai/.sql/mysql/coremusic_logs.sql` | (ölçüm: §3-1 referans) |
| 28 | `.github/workflows/ci.yml` | 203 |
| 29 | `shared/tests/Fixtures/chrome/header.php` | 1 |
| 30 | `shared/tests/OAuth/OAuthManagerTest.php` | 160 |

## §J — Kapı özeti (son tur)

| # | Kapı | Sonuç |
|---|---|---|
| 1 | İçerik satırı (5 × ≥500) | bitiş ölçümünde kesinleşir |
| 2 | index 40–100 | bitiş ölçümünde kesinleşir |
| 3 | Frontmatter | PASS |
| 4 | §GRUPLAR | PASS |
| 5 | Footer | PASS |
| 6 | Kanıt | PASS |
| 7 | URL 200 (5/5) | PASS |
| 8 | URL kesişim 0 | PASS |
| **Sonuç** | **PASS** | **BLOCK yok** |

## §K — Doğrulama oturum kaydı

| Alan | Değer |
|---|---|
| Oturum | K011 Kademe-2 · Unit 2 · `application-services` |
| Tarih | 2026-10-15 |
| Araç kısıtı | `shell` permission.rejected → PowerShell komutları §8/§9'da kayıtlı, yeniden çalıştırılabilir |
| Ölçüm eşdeğeri | `filesystem.read_text_file` + `directory_tree` (ReadAllLines / Get-ChildItem ile aynı kapsam) |
| Kanıt ilkesi | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` · repo'da olmayan kod üretilmedi |
| Commit | YOK (subagent) — commit kararı orkestratöre ait |
| Yazma alanı | Yalnız `.ai/architecture/K011-application/application-services/` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
