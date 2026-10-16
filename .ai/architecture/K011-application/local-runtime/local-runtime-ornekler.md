---
title: "K011 · local-runtime — Örnekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: local-runtime
status: active
updated: 2026-10-15
group: kod
---

# K011 · local-runtime — Örnekler

> **Amaç:** local-runtime'ın **gerçek repo kodundan** örnekler sunar — PHP
> (guard · getenv · argv · exit · dry-run), SQL (CLI'nın yazdığı sözleşmeler),
> CSS (terminal token'ı) ve Vanilla JS (CLI yüzeyi durumu). Her blokta
> `Dosya: … · Satır: …` kanıtı vardır; konu uydurulmaz, yokluk **PLANNED**
> olarak yazılır.

## §GRUPLAR

> İki-eksenli gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın
> frontmatter'ında **son alan `group`**.

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Kanıt envanteri | kod | envanter | 4 dil · sayaçlar · yöntem |
| 2 | §2 PHP — doğrudan-çalıştırma guard'ı | kod | koruma | `$_SERVER` + `realpath` |
| 3 | §3 PHP — `getenv` ve `.env` yasağı | kod | config | env sarmalayıcı + DB_* |
| 4 | §4 PHP — argüman ayrıştırma (3 desen) | kod | giriş | `match` · `if` · `for` |
| 5 | §5 PHP — exit kodu sözleşmesi | kod | sözleşme | 0/1/2 sabitleri + final |
| 6 | §6 PHP — dry-run / onay / yol güvenliği | kod | güvenlik | ingest doc + `kokIcindeMi()` |
| 7 | §7 PHP — STDOUT/STDERR yazım deseni | kod | çıktı | `fwrite` sayımları |
| 8 | §8 Vanilla JS — CLI yüzeyi durumu | kod | boşluk | **PLANNED** + Node teması |
| 9 | §9 SQL — CLI sözleşmelerinin şeması | kod | kalıcılık | `api_keys` · `ingest_batch` |
| 10 | §10 CSS — terminal token'ları | kod | yüzey | `--bg-terminal` / `--text-terminal` |
| 11 | §11 Anti-pattern: YAPMA ↔ YAP | kod | kural | 8 satır karşıtlık |
| 12 | §12 Özet matris + boşluk listesi | kod | gate | dil↔konu + ⚠️/PLANNED |

---

## §1 — Kanıt envanteri (4 dil · ölçüm)

| # | Dil | Kaynak yüzey | Ölçülen | local-runtime konusu ile ilişki |
|---|---|---|---|---|
| 1 | PHP | `bin/*.php` (1) + `media.coremusic.net/bin/*.php` (3) | 4 CLI · 1.600 satır | **doğrudan** (bu katmanın kendisi) |
| 2 | Vanilla JS | `assets.coremusic.net/js/**` | 95 dosya · 7 spec | **YOK** → §8 PLANNED |
| 3 | SQL | `.ai/.sql/mysql/*.sql` | 20 dosya | **dolaylı** (CLI'nın yazdığı tablolar) |
| 4 | CSS | `assets.coremusic.net/Css/01_Abstracts/` | 19 dosya | **dolaylı** (terminal token'ı) |
| 5 | PowerShell | `.ai/scripts/*.ps1` | 6 dosya | **dışında** (vault gate araçları — okunmadı, çalıştırılmadı) |

**Sayım yöntemı:** `Get-ChildItem` + `[System.IO.File]::ReadAllLines` +
`[regex]::Matches` (repo kökünden, salt-okunur). Betik metni §12'dedir;
**çalıştırma orkestratöre aittir**.

**Kanıt bloğu (dil sayaçları):**

```
Dosya: bin/ + media.coremusic.net/bin/ · Satır: — · 4 CLI PHP | exit( = 21 | getenv( = 1 | $_SERVER = 3 | $argv = 3 | fwrite( = 77
```

**Kod bloğu sayısı (bu dosya):** PHP 6 · SQL 2 · CSS 2 · JS 1 = **11 gerçek
repo bloğu** · anti-pattern bölümü 1 (§11).

## §2 — PHP: doğrudan-çalıştırma guard'ı

**Ne korur:** Betik `require`/`include` edildiğinde **koşmaz**; yalnız
`php bin/api-key-create.php` doğrudan çağrıldığında `main()` çalışır. Böylece
dosya hem araç hem güvenli biçimde kütüphane olarak incelenebilir.

```php
namespace {
    // Yalnızca dosya doğrudan çalıştırıldığında (require edildiğinde DEĞİL).
    if (isset($_SERVER['SCRIPT_FILENAME'])
        && realpath((string) $_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)
    ) {
        exit(\CoreMusic\Console\ApiKeyCreateCommand::main($_SERVER['argv'] ?? []));
    }
}
```

```
Dosya: bin/api-key-create.php · Satır: 487–494 · $_SERVER['SCRIPT_FILENAME'] guard + argv → main() → exit()
```

| Parça | Satır | Görevi | Havuz bağlantısı |
|---|---|---|---|
| `isset($_SERVER['SCRIPT_FILENAME'])` | 489 | Anahtar yoksa sessizce atla | `reserved.variables.server` |
| `realpath(...) === realpath(__FILE__)` | 490 | Göreli yol farkını normalize et | `reserved.variables.server` (CLI notu) |
| `$_SERVER['argv'] ?? []` | 492 | Argüman dizisi (+ null güvenliği) | `reserved.variables.argv` |
| `exit(main(...))` | 492 | `int` dönüş kodu → süreç kodu | `function.exit` |

**Gözlem (farklı mekanizma):** php.net CLI tespiti için `php_sapi_name()`
önerir; repo `realpath(__FILE__)` karşılaştırmasını kullanıyor. İkisi de
geçerli, ama **aynı değildir** → `⚠️ VERIFICATION REQUIRED` (§12/4).

## §3 — PHP: `getenv` ve `.env` yasağı

**Ne gösterir:** Yapılandırma **yalnız ortam değişkeninden** okunur;
`.env` dosyası `file_get_contents` ile açılmaz (Security Engineer kuralı).

```php
        /**
         * `.env` OKUNMAZ — yalnız ortam değişkenleri.
         */
        private static function env(string $key, string $default = ''): string
        {
            $value = getenv($key);

            return ($value === false || $value === '') ? $default : $value;
        }
```

```
Dosya: bin/api-key-create.php · Satır: 419–427 · env() → getenv($key) · doc: ".env OKUNMAZ"
```

**Çağrı yüzeyi (DB bağlantı parmak izi):**

```php
            $registry = new DatabaseRegistry();
            $registry->registerMySql(
                ApiKeyRepository::DB_KEY,
                self::env('DB_HOST', 'localhost'),
                self::env('DB_NAME', 'coremusic_auth'),
                self::env('DB_USER', ''),
                self::env('DB_PASSWORD', ''),
                (int) self::env('DB_PORT', '3306'),
                self::env('DB_CHARSET', 'utf8mb4'),
            );
```

```
Dosya: bin/api-key-create.php · Satır: 438–447 · 6 ortam değişkeni (DB_*) · varsayılanlar kodda
```

| # | Değişken | Varsayılan | Neden varsayılan var? |
|---|---|---|---|
| 1 | `DB_HOST` | `localhost` | Yerel geliştirme makinesi varsayılanı |
| 2 | `DB_NAME` | `coremusic_auth` | `api_keys` şemasının sahibi DB |
| 3 | `DB_USER` / `DB_PASSWORD` | `''` | **Secret asla kodda değil** (boş = ortamdan gelmek zorunda) |
| 4 | `DB_PORT` | `3306` | `(int)` ile tip güvenliği |
| 5 | `DB_CHARSET` | `utf8mb4` | Şema collation ile uyum |

**Ölçüm:** `getenv(` repo geneli **1** çağrı (bu dosyada) · `putenv(` **0** →
betik env'i **yalnız okur** (`-web-arastirma.md` §5/10–11 ile aynı sonuç).

## §4 — PHP: argüman ayrıştırma (3 desen)

Aynı sözleşmenin üç farklı sözdizimi (kod tekrarı değil, üç betiğin kendi
akışı):

### §4.1 — `match` deseni (scan)

```php
foreach (array_slice($argv, 1) as $arg) {
    match ($arg) {
        '--rebuild' => $rebuild = true,
        '--dry-run' => $dryRun = true,
        '--deep' => $derin = true,
        '-v', '--verbose' => $verbose = true,
        '-h', '--help' => (function (): void {
            fwrite(STDOUT, "Kullanim: php bin/scan.php [--rebuild] [--dry-run] [--deep] [-v]\n");
            fwrite(STDOUT, "  --deep  medya dosyasinin sha256'sini yeniden hesaplar ve ayni dizindeki\n");
            fwrite(STDOUT, "          yan-JSON'daki teknik.sha256 ile karsilastirir (fark → HASH-FARK, HATA).\n");
            fwrite(STDOUT, "          Varsayilan (hafif) modda yeniden hash YOKTUR (ADR-092 §6.1 madde 5).\n");
            exit(0);
        })(),
        default => (function (string $a): void {
            fwrite(STDERR, "HATA | arguman | bilinmeyen arguman: {$a}\n");
            fwrite(STDERR, "Kullanim: php bin/scan.php [--rebuild] [--dry-run] [--deep] [-v]\n");
            exit(2);
        })($arg),
    };
}
```

```
Dosya: media.coremusic.net/bin/scan.php · Satır: 60–79 · array_slice($argv,1) + match → exit(0) help / exit(2) bilinmeyen
```

### §4.2 — `if` deseni (audit)

| Satır | Davranış |
|---|---|
| 53 | `foreach (array_slice($argv, 1) as $arg)` |
| 54–60 | `-h/--help` → yardım + `exit(0)` |
| 61–64 | `--deep` → `$derin = true` |
| 65–66 | bilinmeyen → `STDERR` + `exit(2)` |

```
Dosya: media.coremusic.net/bin/audit.php · Satır: 53–67 · if + continue + exit(0)/exit(2)
```

### §4.3 — `for` deseni (ingest · değerli argüman)

```php
    if ($arg === '--source') {
        $source = $args[++$i] ?? '';
        if ($source === '') {
            fwrite(STDERR, "HATA | arguman | --source degeri bos\n");
            fwrite(STDERR, "Kullanim: php bin/ingest.php --source \"DIZIN\" [--commit] [-v]\n");
            exit(2);
        }
    } elseif ($arg === '--commit') {
        $commit = true;
    }
```

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 52–62 · indeksli döngü (++$i) → değer + exit(2)
```

**Zorunlu argüman kontrolü (ingest):**

| Satır | Koşul | Sonuç |
|---|---|---|
| 75–79 | `$source === null` | `STDERR` + `exit(2)` |
| 80–85 | `realpath($source)` false / `is_dir` false | `STDERR` + "hicbir dosya okunmadi/yazilmadi" + `exit(2)` |

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 75–85 · --source yoksa/geçmezse exit(2) (crash yok)
```

## §5 — PHP: exit kodu sözleşmesi (0/1/2)

### §5.1 — Sabitler (tek kaynak)

```php
    final class ApiKeyCreateCommand
    {
        public const EXIT_OK    = 0;
        public const EXIT_ERROR = 1;
        public const EXIT_USAGE = 2;
```

```
Dosya: bin/api-key-create.php · Satır: 178–182 · EXIT_OK=0 · EXIT_ERROR=1 · EXIT_USAGE=2
```

### §5.2 — Kodun nereden döndüğü

| Betik | Mekanizma | Kanıt |
|---|---|---|
| api-key-create | `main(): int` döndürür → tek `exit(...)` | L201 (`main`), L492 (`exit`) |
| scan | doğrudan `exit(0/2)` + final `exit($hata>0?1:0)` | L71, L76, L261 |
| audit | doğrudan `exit(0/2)` + final `exit($hata>0?1:0)` | L59, L66, L499 |
| ingest | doğrudan `exit(0/2)` (11 çağrı) + doc final | L68, L60/72/78/84, L19 |

### §5.3 — Yardım ve hata akışı (audit finali)

```php
fwrite(STDOUT, 'MOD: ' . ($derin ? 'deep (yeniden hash + JSON karsilastirmasi)' : 'hafif (yeniden hash yok)') . "\n");
fwrite(STDOUT, "TOPLAM | {$hata} HATA | {$uyari} UYARI | {$kayit} dosya tarandi\n");
exit($hata > 0 ? 1 : 0);
```

```
Dosya: media.coremusic.net/bin/audit.php · Satır: 497–499 · özet + exit($hata > 0 ? 1 : 0)
```

### §5.4 — Sözleşme özeti (kod ↔ havuz)

| Exit | Anlam | Kod tarafı | php.net claim'i |
|---|---|---|---|
| `0` | temiz / yardım / başarı | `EXIT_OK` · `exit(0)` help | "exit code of 0 = succeeded" |
| `1` | hata bulundu | `EXIT_ERROR` · `exit($hata>0?1:0)` | "Any other value = error" |
| `2` | kullanım/kok/kaynak hatası | `EXIT_USAGE` · `exit(2)` | 0–254 aralığında geçerli |
| `255` | **kullanılmaz** | rastlanmadı (ölçüm 0) | PHP tarafından rezerve |

**Sayım (4 betik toplamı):** `exit(` **21** çağrı · sabit **3** ·
kullanılan benzersiz kod **3** (0/1/2) · `255` **0**.

## §6 — PHP: dry-run / onay / yol güvenliği

### §6.1 — Sözleşmenin kendisi (doc bloğu)

```php
 * GUVENLIK (ADR-092 §8 madde 11 — dry-run zorunlu):
 *   --commit YOKKEN medya agacina tek bayt bile kopyalama/yazma/silme YOKTUR.
 *   Dry-run'da yalnizca rapor dosyasi yazilir:
 *   reports/ingest-YYYYMMDD-HHMM.csv  (UTF-8 BOM, virgul, tum alanlar tirnakli)
 *
 * Kullanim:
 *   php bin/ingest.php --source "C:\Users\Bayram Ali\Music" [--commit] [-v]
 *   --source yoksa/gecmezse exit 2 (temiz hata cikisi, crash yok).
 *   --commit verilirse kopyalama onceden STDIN'den "evet" onayi ister.
 *
 * Cikis: 0 = temiz, 1 = hata, 2 = kullanim/kaynak/kok hatasi.
```

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 9–19 · dry-run zorunlu + STDIN onay + exit 0/1/2
```

### §6.2 — Yol güvenliği (path-traversal koruması)

```php
/** Yol sinirlari: hedef her zaman proje koku icinde (ADR-092 §8 madde 2). */
function kokIcindeMi(string $kok, string $goreliYol): bool
{
    $gercekKok = realpath($kok);
    if ($gercekKok === false) {
        return false;
    }
    $aday = $gercekKok . '/' . str_replace('\\', '/', $goreliYol);
    $dizin = dirname($aday);
    /* henuz olusturulmamis dizinlerde dirname uzerinden kontrol. */
    $gercekDizin = realpath($dizin);
    if ($gercekDizin === false) {
        /* kokun kendisine klasor acmak uzereyse kok kontrolu yeter. */
        return str_starts_with($dizin, $gercekKok);
    }

    return str_starts_with($gercekDizin, $gercekKok) || $gercekDizin === $gercekKok;
}
```

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 101–118 · kokIcindeMi() — hedef her zaman proje kökü içinde
```

| Kural | Kod karşılığı | Koruduğu şey |
|---|---|---|
| Bağıl yol normalize | `str_replace('\\','/')` (L108) | Windows/Linux ayrımı |
| Kök dışı hedef reddi | `str_starts_with($gercekDizin, $gercekKok)` (L117) | `../../` kaçışı |
| Henüz yoksa kök kontrolü | `str_starts_with($dizin, $gercekKok)` (L114) | yeni dizin açma |
| Kural kaynağı | doc: ADR-092 §8 madde 2 (L101) | izlenebilirlik |

## §7 — PHP: STDOUT/STDERR yazım deseni

| Betik | `fwrite(` | STDOUT kullanımı | STDERR kullanımı | Çıktı biçimi |
|---|---|---|---|---|
| api-key-create | 19 | yardım + ham anahtar (1 kez) | hata | düz metin |
| scan | 23 | katalog/MySQL özeti | argüman hatası | `ANAHTAR \| değer` |
| audit | 10 | `SEVIYE \| yol \| kural \| neden` + TOPLAM | kök/argüman hatası | pipe-ayraçlı |
| ingest | 25 | rapor yolu / özet | kaynak/argüman hatası | pipe-ayraçlı |
| **Toplam** | **77** | — | — | — |

```
Dosya: media.coremusic.net/bin/scan.php · Satır: 67–70, 74–75 · STDOUT=yardım, STDERR=hata (ayrım zorunlu)
```

**Neden önemli:** CI/CD gate (workflows) stdout'u ayrıştırır; hata akışının
STDERR'e gitmesi boru hatlarının (pipe) doğru çalışmasını sağlar. `-kapsam.md`
§15'teki "STDOUT/STDERR + exit" satırının kod karşılığı budur.

## §8 — Vanilla JS: CLI yüzeyi durumu (PLANNED)

**Ölçüm (2026-10-15):** `assets.coremusic.net/js/**` = **95 dosya**
(şartname "98" demişti → ölçülen 95 · `⚠️ VERIFICATION REQUIRED`),
7 tanesi `*.spec.js`.

| Aranan desen | İsabet | Yorum |
|---|---|---|
| `\bCLI\b` (kelime) | **0** | JS içinde CLI sözü yok |
| `argv` / `argc` / `getenv` / `putenv` | **0** | Komut satırı köprüsü yok |
| `exit` / `exitCode` / `stdout` / `STDERR` | **0** | Süreç köprüsü yok |
| `terminal` | **0** | JS'de terminal teması yok |
| `cli-server` / `php_sapi_name` | **0** | SAPI köprüsü yok |

**Sonuç:** local-runtime konusuna giren bir Vanilla JS yüzeyi **repo'da yok**.
Konu uydurmak yasak olduğu için bu satır **boşluk kaydıdır**:

> **PLANNED** — local-runtime ↔ JS köprüsü (ör. CLI raporunu tarayıcıda
> gösteren panel) **henüz tasarlanmamıştır**. `⚠️ VERIFICATION REQUIRED`:
> böyle bir yüzeyin ihtiyacı/kararı vault'ta kayıtlı DEĞİLDİR.

**Repo'da bulunan tek çalışma-zamanı teması (JS ↔ PHP — drift kilidi):**

```js
    beforeAll(() => {
      // jsdom'da import.meta.url file:// degildir → process.cwd() (repo koku,
      // npm script repo kokunden calisir) uzerinden resolve edilir.
      const path = resolve(
        process.cwd(),
        'shared/src/Device/DeviceDetector.php'
      );
      const src = readFileSync(path, 'utf8');
      phpConsts = {};
      const re = /const\s+([A-Z_0-9]+)\s*=\s*(\d+);/g;
      let m;
      while ((m = re.exec(src)) !== null) {
        phpConsts[m[1]] = Number(m[2]);
      }
    });
```

```
Dosya: assets.coremusic.net/js/core/breakpoints.spec.js · Satır: 110–124 · process.cwd() → shared/src/Device/DeviceDetector.php (JS↔PHP parite testi)
```

| Öğe | Değer | Yorum |
|---|---|---|
| Ortam | Vitest + jsdom | Tarayıcı simülasyonu, **gerçek CLI değil** |
| Node API | `node:fs` · `node:path` · `process.cwd()` | Test dosyasının kendi bağımlılığı |
| Hedef | `shared/src/Device/DeviceDetector.php` | local-runtime betiği **DEĞİL** (Device katmanı) |
| İlişki | **konu dışı** → yalnız "JS'in PHP kaynağına erişimi" kanıtı | uydurma CLI-JS bağlantısı YOK |

**Test yapılandırması (tam, 33 satır):**

```
Dosya: assets.coremusic.net/vitest.config.js · Satır: 1–33 · environment: jsdom · include: tests/** + js/**/*.spec.js · eşik %80
```

## §9 — SQL: CLI'nın yazdığı sözleşmeler

### §9.1 — `api_keys` (api-key-create.php'nin hedefi)

```sql
CREATE TABLE `api_keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `api_key_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allowed_ips` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ak_hash` (`api_key_hash`),
  ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

```
Dosya: .ai/.sql/mysql/coremusic_api.sql · Satır: 55–76 · api_keys (hash + prefix + unique) — CLI yalnız hash+prefix yazar
```

| Sütun | CLI ile yazılan | Kanıt |
|---|---|---|
| `api_key_hash` (char 64) | evet — ham anahtar **asla** | `-kapsam.md` §13/7 (api-key L194 doc) |
| `api_key_prefix` (varchar 20) | evet — ön ek | aynı |
| `expires_at` | `--ttl` seçeneğinden | api-key L192 (`--ttl=900s\|30m\|12h\|30d\|7w`) |
| `user_id` | `--user=<32-hex>` | api-key L191 |

### §9.2 — `ingest_batch` (ingest/scan parti izi)

```sql
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Parti kimligi (sayac)',
  kaynak         VARCHAR(500)   NOT NULL COMMENT 'Tarama kaynagi (repo-goreli dizin/yol)',
  mod            ENUM('dry-run','commit') NOT NULL COMMENT 'dry-run = yalniz rapor, commit = yaz',
  dosya_sayisi   INT UNSIGNED   NOT NULL DEFAULT 0 COMMENT 'Islenen dosya sayisi',
  byte           BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Islenen toplam bayt',
  baslangic      DATETIME       NOT NULL COMMENT 'Parti baslangici (+03:00)',
  bitis          DATETIME       NULL COMMENT 'Parti bitisi (NULL = suruyor)',
  durum          ENUM('planlandi','calisiyor','basarili','hatali') NOT NULL DEFAULT 'planlandi',
  rapor_yolu     VARCHAR(500)   NULL COMMENT 'Uretilen rapor dosyasi yolu (repo-goreli)',
  ...
) ENGINE=InnoDB ... COMMENT='Ingest/tarama parti kaydi - ADR-092 §5.2 adimlarinin denetlenebilir izi';
```

```
Dosya: .ai/.sql/mysql/media_catalog.sql · Satır: 406–421 · ingest_batch (mod ENUM dry-run|commit) — ADR-092 §5.2 izi
```

> **BOŞLUK (kritik):** `mod ENUM('dry-run','commit')` sözleşmesi şemada **var**,
> ancak `ingest_batch` yazan PHP kodu repo'da **0 isabet** (grep, vendor hariç
> 323 `.php`) → **PLANNED**: tablo betikler henüz bu tabloya yazmıyor.
> `⚠️ VERIFICATION REQUIRED`: yazım tasarımı henüz yapılmamış olabilir.

### §9.3 — `scan.php` ↔ MySQL (koşullu yazım)

| Satır | Davranış |
|---|---|
| 12 | "MySQL media_catalog (opsiyonel; yoksa tek uyari + JSONL ile devam)" |
| 15 | `--dry-run` → stdout'a yalnız yazılacakları basar |
| 249–251 | dry-run ise MySQL yazımı **atlanır** (`uyari: ['dry-run: MySQL yazimi atladi']`) |

```
Dosya: media.coremusic.net/bin/scan.php · Satır: 249–251 · $dryRun ? atla : CatalogWriter::mysqlYaz(...)
```

## §10 — CSS: terminal token'ları

**Gerçek token (01_Abstracts — sabit değer yalnız burada):**

```css
  --bg-overlay:     var(--color-dark-600);   /* #231535 - modal, dropdown */
  --bg-subtle:      rgba(255, 255, 255, 0.04);
  --bg-muted:       rgba(255, 255, 255, 0.07);
  --bg-player:      var(--color-dark-900);   /* Muzik calar arka plani */
  --bg-admin:       var(--color-dark-700);   /* Admin panel arka plani */
  --bg-terminal:    var(--color-dark-900);   /* Terminal arka plani */
```

```css
  --text-success:   var(--color-green-500);
  --text-warning:   var(--color-yellow-500);
  --text-info:      var(--color-info-500);
  --text-terminal:  var(--color-green-500);          /* Terminal yesili */
```

```
Dosya: assets.coremusic.net/Css/01_Abstracts/a-semantic-token.css · Satır: 24, 40 · --bg-terminal / --text-terminal
```

**Koyu tema karşılığı (primitive referanslı):**

```
Dosya: assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css · Satır: 49, 153, 319 · --bg-terminal (dark/aydınlık mod eşlemesi)
```

| Token | Değer | Görevi |
|---|---|---|
| `--bg-terminal` | `var(--color-dark-900)` / `#1a1025` | Terminal konsol arka planı |
| `--text-terminal` | `var(--color-green-500)` | Terminal yeşili çıktı metni |
| `--color-green-500` | `#51CF66` | "Success / Terminal" (a-colors-token L158) |

> **BOŞLUK (ölçüm):** `bg-terminal`/`text-terminal` isabetlerinin **tamamı**
> `01_Abstracts/` içindedir (5/5) → component/page CSS'te **tüketim 0**.
> Yani token **tanımlı ama kullanılmıyor**: CLI çıktısını gösteren bir web
> yüzeyi henüz yok → **PLANNED** (JS tarafı §8 ile aynı boşluk).

```
Dosya: assets.coremusic.net/Css/01_Abstracts/ · Satır: — · 19 token dosyası · terminal token tüketimi (04_Components vb.) = 0
```

## §11 — Anti-pattern: YAPMA ↔ YAP

| # | YAPMA (anti-pattern) | YAP (kural) | Repo kanıtı |
|---|---|---|---|
| 1 | `.env` dosyasını `file_get_contents` ile okumak | `getenv()` üzerinden oku, yoksa varsayılana düş | api-key L419–427 |
| 2 | `exit("mesaj")` (dizge ile çıkış) | int kod döndür, mesajı `STDERR`'a yaz | 4 betikte `exit("…")` = 0 |
| 3 | Betik doğrudan `require` edilince de çalışsın | `realpath(__FILE__)` guard'ı | api-key L487–494 |
| 4 | `--commit` yokken yazma (dry-run'ı atla) | Önce rapor, sonra onay, sonra yaz | ingest L9–17 |
| 5 | `$_SERVER['HTTP_*']` okumak (CLI'da) | Yalnız `argv` + `SCRIPT_FILENAME` | 4 betikte `HTTP_*` = 0 |
| 6 | `putenv()` ile ortamı kirletmek | Yalnız **oku** (`getenv`) | `putenv(` = 0 · `getenv(` = 1 |
| 7 | Hata çıktısını `STDOUT`'a yazmak | Hata → `STDERR`, veri → `STDOUT` | scan L67–70 vs L74–75 |
| 8 | `.ai/scripts/*.ps1` koduna PHP gibi davranmak | PS1 = vault aracı; PHP CLI = bu katman | `.ai/scripts` 6 dosya (dış sınır) |

**Doğrulama durumu:** 8 satırın 7'si **kod kanıtlı** · 8. satır **sınır
kuralı** (okuma/yorum). Çalıştırma kanıtı (betiklerin gerçekten exit 0
döndürmesi) **YOK** → `⚠️ VERIFICATION REQUIRED` (PHP çalışma zamanı bu
ortamda yok).

## §12 — Özet matris + boşluk listesi + kapı

### §12.1 — Dil ↔ konu matrisi

| Dil | Blok | Konu tutarlı mı? | Durum |
|---|---|---|---|
| PHP | 6 (§2–§7) | ✅ doğrudan local-runtime | KANITLI |
| SQL | 2 (§9) | ✅ CLI'nın yazdığı tablolar | KANITLI (+1 boşluk) |
| CSS | 2 (§10) | ⚠️ yalnız token tanımı | KANITLI (+tüketim 0) |
| Vanilla JS | 1 (§8) | ❌ CLI yüzeyi yok | **PLANNED** |
| **Toplam** | **11** | ≥4 dil | ✅ |

### §12.2 — Boşluk / ⚠️ listesi

| # | Boşluk | Sınıf | Sahip |
|---|---|---|---|
| 1 | local-runtime ↔ JS köprüsü (rapor paneli) | **PLANNED** | UI Designer |
| 2 | `--bg-terminal` token'ı hiçbir bileşende kullanılmıyor | **PLANNED** | UI Designer |
| 3 | `ingest_batch` tablosuna PHP'den yazım yok | **PLANNED** | Backend Architect |
| 4 | Guard `php_sapi_name()` yerine `realpath` kullanıyor | ⚠️ VR | Backend Architect |
| 5 | Betikler PHP 8.4'te koşulmadı (exit 0 kanıtı yok) | ⚠️ VR | QA Engineer |
| 6 | Şartname "98 JS dosyası" → ölçülen **95** | ⚠️ VR | MO (vault) |
| 7 | `putenv` kullanılmaması kasıtlı politika mı? | ⚠️ VR | Security Engineer |

### §12.3 — Sayım betiği (metin — repo kökünden çalıştır)

```powershell
# local-runtime örnek sayım betiği (metin)
$root = 'C:\www\coremusic.net'
$cli = @()
$cli += Get-ChildItem "$root\bin" -Filter *.php -EA SilentlyContinue
$cli += Get-ChildItem "$root\media.coremusic.net\bin" -Filter *.php -EA SilentlyContinue
foreach ($f in $cli) {
  $t = [System.IO.File]::ReadAllText($f.FullName)
  "{0} | exit(={1} | getenv(={2} | argv={3} | fwrite(={4} | satir={5}" -f $f.Name,
    ([regex]::Matches($t, [regex]::Escape('exit('))).Count,
    ([regex]::Matches($t, [regex]::Escape('getenv('))).Count,
    ([regex]::Matches($t, [regex]::Escape('$argv'))).Count,
    ([regex]::Matches($t, [regex]::Escape('fwrite('))).Count,
    ([System.IO.File]::ReadAllLines($f.FullName).Count)

# JS CLI yüzeyi
$js = Get-ChildItem "$root\assets.coremusic.net\js" -Recurse -Filter *.js -EA SilentlyContinue
"JS={0} | CLI kelime={1}" -f $js.Count,
  ($js | Select-String -Pattern '\bCLI\b' -EA SilentlyContinue).Count

# SQL / CSS
"SQL={0}" -f (@(Get-ChildItem "$root\.ai\.sql\mysql" -Filter *.sql)).Count
"CSS_01={0}" -f (@(Get-ChildItem "$root\assets.coremusic.net\Css\01_Abstracts" -Filter *.css)).Count
```

**Beklenen çıktı (elle ölçümle birebir):** `exit(` 1/5/4/11 → **21** ·
`getenv(` **1** · `$argv` 6/1/1/1 → **9** (api-key'teki 6, doc satırları dahil) ·
`fwrite(` **77** · `JS=95` · `CLI kelime=0` · `SQL=20` · `CSS_01=19`.
`⚠️ VERIFICATION REQUIRED`: betik çalıştırılmadı (çalıştırma orkestratöre ait).

### §12.4 — Kapı özeti

- **Dosya:** `local-runtime-ornekler.md` · **Ölçüm:** 2026-10-15
- **Frontmatter:** 8 alan · `group: kod` (son alan) ✅
- **Dil sayısı:** 4 (PHP · SQL · CSS · JS) ✅ · **Kanıt bloğu:** 15 (≥1) ✅
- **Her blokta `Dosya: … · Satır: …`** ✅ · **Anti-pattern YAPMA↔YAP:** §11 (8 satır) ✅
- **Konu uydurulmadı:** JS yüzeyi yok → PLANNED yazıldı (§8) ✅
- **Kaynak kod değişikliği:** 0 · **commit:** YOK

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
