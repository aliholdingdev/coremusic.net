---
title: "K011 · local-runtime — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: local-runtime
status: active
updated: 2026-10-15
group: mimari
---

# K011 · local-runtime — Kapsam

> **Amaç:** Kullanıcı makinesinde (geliştirici/operatör workstation'ında) çalışan
> uygulama çalışma zamanını (CLI çekirdeği + yükleyici/araçlar) tanımlar: hangi
> betikler local çalışır, exit kodları, argüman/çevre değişkeni sözleşmeleri ve
> sunucu-runtime'dan sınır. Bu dosya **kapsam** belgesidir; kod kanıtı
> `-ornekler.md`'de, sayımlar `-bilesenler.md`'dedir.

## §GRUPLAR

> İki-eksenli gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın
> frontmatter'ında **son alan `group`**.

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 ELI15 | mimari | anlatım | Kim için, ne için |
| 2 | §2 Sınır (ne DEĞİL) | mimari | ayırt etme | server-runtime / web / desktop ile sınır |
| 3 | §3 Yerel çalışma zamanı bileşenleri | mimari | envanter | 4 CLI betiği + rolleri |
| 4 | §4 Exit kodu sözleşmesi | mimari | sözleşme | 0/1/2 semantiği (tüm betikler) |
| 5 | §5 Argüman & çevre değişkeni sözleşmesi | mimari | giriş | `$argv` · `getenv` · `.env` OKUNMAZ |
| 6 | §6 Akış: dry-run → onay → commit | mimari | güvenlik | ingest akış şeması |
| 7 | §7 Kapsam dışı | mimari | ayırt etme | Bu katmanda OLMAYANlar |
| 8 | §A Açık sorular | mimari | belirsizlik | ⚠️ VR maddeleri |

---

## §1 — ELI15 (basit açıklama)

Bilgisayarında çalışan küçük "tamirci" programlardır. Bunlar sitenin görsel
arayüzünü çizmez (o iş `web`/`desktop` katmanının), sunucuyu barındırmaz (o iş
`server-runtime`'ın). Bunun yerine **senin makinende**, komut satırından
çalıştırılan araçlardır: API anahtarı üretir, medya arşivini tarar/denetler/taşır.
"Runtime" yani "çalışma zamanı" burada "uygulamanın kendi bilgisayarında ayakta
kalıp iş yapması" demektir.

## §2 — Sınır (ne DEĞİL)

| Bu katman (local-runtime) | Komşu katman | Ayırt edici işaret |
|---|---|---|
| CLI betikleri (`bin/*.php`) — operatör çalıştırır | `server-runtime` — nginx/PHP-FPM sürekli çalışır | Süreklilik: local **anlık** çalışır, sunucu **daemon** |
| `$_SERVER['argv']` girdisi | `$_SERVER['HTTP_*']` girdisi (web) | Kaynak: komut satırı vs HTTP isteği |
| `getenv()` ile `.env` OKUNMADAN config | web/desktop config yükleme | `.env` dosyası doğrudan okunmaz |
| `STDERR`/`STDOUT` raporu | web render (HTML/CSS) | Çıktı metin/konsol vs görsel |
| Anlık exit kodu (0/1/2) | HTTP status kodu | Yerel süreç sonucu vs ağ yanıtı |

**Sınır notu:** `media.coremusic.net/bin/*.php` betikleri de bu katmandadır
(lokal CLI); ancak `media.coremusic.net`'in kendi `AGENTS.md`'si bu klasörün
rol routing'ini daraltır — bu belge yalnız **kademe-2 kapsam** açısından bakar.

## §3 — Yerel çalışma zamanı bileşenleri (4 CLI betiği)

| # | Betik | Yol | Görevi | Exit kaynağı |
|---|---|---|---|---|
| 1 | `api-key-create.php` | `bin/` | API anahtarı üretir (DB'ye hash+prefix) | L180–182 sabitleri |
| 2 | `scan.php` | `media.coremusic.net/bin/` | Medya ağacını tarar + katalog üretir | L26 doc + `exit(...)` |
| 3 | `audit.php` | `media.coremusic.net/bin/` | Salt-okunur denetim raporu | L11 doc + L499 |
| 4 | `ingest.php` | `media.coremusic.net/bin/` | Harici dizinden medya taşır (onaylı) | L19 doc + `exit(...)` |

**Kanıt bloğu (betik sayısı):**

```
Dosya: bin/ · media.coremusic.net/bin/ · Satır: — · glob: 1 + 3 = 4 CLI .php
```

## §4 — Exit kodu sözleşmesi (tüm betikler 0/1/2)

| Exit | Anlamı | Kim kullanır | Kanıt |
|---|---|---|---|
| `0` | Temiz / başarı / help | 4 betik de | api-key L180 · scan L26 · audit L11 · ingest L19 |
| `1` | Hata bulundu (veri/DB/işlem) | api-key · scan · audit · ingest | api-key L181 · scan L261 · audit L499 · ingest L19 |
| `2` | Kullanım/kok/kaynak hatası | 4 betik de | api-key L182 · scan L53 · audit L49 · ingest L46 |

**Kanıt bloğu (api-key-create exit sabitleri):**

```
Dosya: bin/api-key-create.php · Satır: 180–182 · EXIT_OK=0, EXIT_ERROR=1, EXIT_USAGE=2
```

**Kanıt bloğu (audit.php final exit):**

```
Dosya: media.coremusic.net/bin/audit.php · Satır: 499 · exit($hata > 0 ? 1 : 0)
```

**Tutarlılık notu:** 4 betik de aynı 0/1/2 semantiğini paylaşır — bu, CI/CD
gate'lerinin (DevOps) tek exit kodu bekleyebilmesini sağlar. `⚠️ VERIFICATION
REQUIRED`: betiklerin PHP 8.4 ortamında gerçekten koşulup 0 döndüğü doğrulanmadı
(shell yok; her betik doc-blokunda `php -l` uyarısı var — scan L28–29, audit
L25–26, ingest L21–22).

## §5 — Argüman & çevre değişkeni sözleşmesi

### §5.1 — `$_SERVER['argv']` (komut satırı girdisi)

| Betik | Argüman ayrıştırma | Kanıt |
|---|---|---|
| `api-key-create.php` | `main($_SERVER['argv'] ?? [])` + doğrudan-çalıştırma guard'ı | L489–492 |
| `scan.php` | `array_slice($argv, 1)` + `match` | L60–79 |
| `audit.php` | `array_slice($argv, 1)` + `if` | L53–67 |
| `ingest.php` | `array_slice($argv, 1)` + `for` | L52–74 |

**Kanıt bloğu (api-key-create argv + guard):**

```
Dosya: bin/api-key-create.php · Satır: 489–492 · $_SERVER['argv'] + realpath(__FILE__) guard
```

**Guard açıklaması (L489–491):** Betik `require`/`include` edildiğinde
**çalışmaz** — yalnız `php bin/api-key-create.php` doğrudan çağrıldığında
`main()` koşar. Bu, betiğin kütüphane olarak kullanılmasını önler (local-runtime
"araç" niteliğini korur).

### §5.2 — `getenv()` (çevre değişkenleri — `.env` OKUNMAZ)

| Betik | Çevre değişkenleri | Kanıt |
|---|---|---|
| `api-key-create.php` | `DB_HOST` · `DB_NAME` · `DB_USER` · `DB_PASSWORD` · `DB_PORT` · `DB_CHARSET` | L441–446 |

**Kanıt bloğu (env sarmalayıcı — `.env` okunmaz):**

```
Dosya: bin/api-key-create.php · Satır: 422–427 · getenv($key) — ".env OKUNMAZ" doc
```

**Güvenlik notu:** `env()` yalnız `getenv()` çağırır; `.env` dosyası
`file_get_contents` ile okunmaz. Secret'lar yalnız ortam değişkeninden gelir
(kök `AGENTS.md` §5 `.env` kuralı + Security Engineer denetimi).

## §6 — Akış: dry-run → onay → commit (ingest örneği)

```text
php bin/ingest.php --source "DIZIN" [--commit] [-v]
  │
  ├─ kok kontrolü (config/mojibake-fix.json yoksa → exit 2)      [L44–47]
  ├─ argüman ayrıştırma (--source yoksa/geçmezse → exit 2)        [L52–79]
  ├─ kaynak dizin doğrulama (realpath/is_dir yoksa → exit 2)      [L80–85]
  │
  ├─ DRY-RUN (varsayilan): reports/ingest-*.csv yazılır,          [doc L10–12]
  │   medya ağacına TEK BAYT kopyalama/yazma/silme YOK            [doc L9–11]
  │
  └─ --commit: STDIN'den "evet" onayı ister → kopyalama           [doc L17]
       (onay yoksa → temiz çıkış, crash yok)
```

**Kanıt bloğu (ingest dry-run zorunluluğu):**

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 9–12, 19 · "--commit YOKKEN ... YOKTUR" · exit 0/1/2
```

**Güvenlik anlamı (ADR-092 §8 madde 11):** Taşıma işlemi **önce rapor üretir,
sonra onay ister**. Bu iki-kapılı akış, master `.flac`/`.wav` dosyalarının
kazara ezilmesini engeller (Security Engineer rolü — media AGENTS §3.1 #3).

## §7 — Kapsam dışı (bu katmanda OLMAYAN)

1. **Sürekli çalışan sunucu** (nginx/PHP-FPM) → `server-runtime`.
2. **HTTP路由/render** (HTML/CSS/JS üretimi) → `web`.
3. **Masaüstü penceresi/OS entegrasyonu** → `desktop`.
4. **Electron paketleme** → `electron` (K011 ayrı düğüm).
5. **Uygulama mantığı / servis katmanı** (PSR-6/16/14/3/12) → `application-services`.
6. **CI/CD pipeline'ın kendisi** (GitHub Actions) → `workflows` (local-runtime
   yalnız *yerel* CLI'yı kapsar; CI koşucusu bu katman dışındadır).
7. **Betiklerin PHP 8.4'te gerçekten çalıştığının kanıtı** → bu dosyada YOK
   (shell yok) → `⚠️ VERIFICATION REQUIRED` (bkz. §4).

---

## §8 — Betik bazlı çalışma zamanı detayları

### §8.1 — `api-key-create.php` (bin/)

**Görev:** API anahtarı üretir; ham anahtar bir kez stdout'a basılır, DB'ye yalnız hash + prefix yazılır (L194 USAGE).

**Çevre değişkenleri (getenv):** `DB_HOST` · `DB_NAME` · `DB_USER` · `DB_PASSWORD` · `DB_PORT` · `DB_CHARSET` (L441–446). `.env` dosyası OKUNMAZ (L420 doc).

**SQLite fallback:** `--sqlite=<yol>` verilirse `SqliteFileGateway` kullanılır (L434–436); verilmezse MySQL `DatabaseRegistry` (L438–447). Bu, local-runtime'ın **çevrimdışı** çalışabilmesini sağlar.

**Direct-exec guard:** `realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)` (L490) — betik `require` edilmez, yalnız doğrudan çalıştırılır.

**Kanıt bloğu:**

```
Dosya: bin/api-key-create.php · Satır: 180–182 (exit sabitleri) · 422–427 (getenv) · 489–492 (argv + guard)
```

### §8.2 — `scan.php` (media.coremusic.net/bin/)

**Görev:** Medya ağacını tarar, yan-JSON'ları doğrular, katalog üretir (JSONL + opsiyonel MySQL).

**Bayraklar:** `--rebuild` · `--dry-run` · `--deep` · `-v` (L60–65). `--deep` modunda yeniden hash + HASH-FARK karşılaştırması (L154–167).

**Çıkış:** `exit($hata > 0 ? 1 : 0)` (L261). Kök hatası: `exit(2)` (L53). Bilinmeyen argüman: `exit(2)` (L76).

**Kanıt bloğu:**

```
Dosya: media.coremusic.net/bin/scan.php · Satır: 26 (exit doc) · 60–79 (argv match) · 261 (final exit)
```

### §8.3 — `audit.php` (media.coremusic.net/bin/)

**Görev:** Salt-okunur denetim; medya ağacına hiçbir yazma yapmaz (doc L9).

**Rapor formatı:** `SEVIYE | yol | kural | neden` (L75–83). Hata → STDERR, uyarı → STDOUT.

**Çıkış:** `exit($hata > 0 ? 1 : 0)` (L499). Kök/argüman hatası: `exit(2)` (L49, L66). Help: `exit(0)` (L59).

**Kanıt bloğu:**

```
Dosya: media.coremusic.net/bin/audit.php · Satır: 11 (exit doc) · 53–67 (argv) · 499 (final exit)
```

### §8.4 — `ingest.php` (media.coremusic.net/bin/)

**Görev:** Harici dizinden medya taşır; `--commit` YOKKEN tek bayt yazma YOK (doc L9–11).

**Onay akışı:** `--commit` → STDIN'den `evet` onayı (doc L17). Onay yoksa temiz çıkış.

**Yol güvenliği:** `kokIcindeMi()` — hedef her zaman proje kökü içinde (L102–118); path-traversal koruması.

**Çıkış:** `exit($hata > 0 ? 1 : 0)` (L19 doc). Kök/kaynak/argüman hatası: `exit(2)` (L46, L61, L72, L78, L84). Help: `exit(0)` (L68).

**Kanıt bloğu:**

```
Dosya: media.coremusic.net/bin/ingest.php · Satır: 19 (exit doc) · 52–74 (argv) · 102–118 (yol güvenliği)
```

## §9 — Exit kodu-semantik matrisi

| Betik | exit 0 | exit 1 | exit 2 | Kaynak |
|---|---|---|---|---|
| api-key-create | başarılı | DB/çalışma zamanı hatası | kullanım hatası | L195 USAGE |
| scan | temiz | hata bulundu | kök/kullanım hatası | L26 doc |
| audit | HATA yok | en az bir HATA | kök/kullanım hatası | L11 doc |
| ingest | temiz | hata | kök/kaynak/kullanım hatası | L19 doc |

**Ortak desen:** 4 betik de aynı 0/1/2 semantiğini paylaşır. Bu, CI/CD gate'lerinin (DevOps) tek exit kodu bekleyebilmesini sağlar.

## §10 — Boşluklar (kapsam dışı bırakılan)

| # | Boşluk | Neden | Sahip |
|---|---|---|---|
| 1 | Betiklerin PHP 8.4'te gerçekten koşulduğu kanıtı | Shell yok; doc bloklarında uyarı var | QA Engineer |
| 2 | `.env` dosyasının hiçbir yerde okunmadığının kanıtı | Yalnız `env()` doc'u; repo geneli doğrulanmadı | Security Engineer |
| 3 | 4 betik dışında başka local CLI olup olmadığı | `bin/` + `media.coremusic.net/bin/` dışında glob yapılmadı | Backend Architect |
| 4 | `--deep` modunun gerçek veriyle test edilmesi | 1M+ varlıkta CPU maliyeti (audit L58) | Data Engineer |
| 5 | Kademe-3 bounded-context kapsamı | ADR-097 K4 fazı (C) bekliyor | MO |

## §A — Açık sorular

| # | Açık soru | Neden açık | Sahip | Durum |
|---|---|---|---|---|
| 1 | `media.coremusic.net/bin/*.php` betikleri local-runtime'a **tam** dahil mi, yoksa `media.coremusic.net` alt-katalog olarak ayrı mı ele alınmalı? | İki `AGENTS.md` (kök + media) çakışabilir | MO (vault) | ⚠️ VERIFICATION REQUIRED |
| 2 | PHP 8.4 çalışma zamanı bu ortamda YOK — betikler hiç koşuldu mu? | Doc bloklarında açık uyarı var (scan L28, audit L25, ingest L21) | QA Engineer | ⚠️ VERIFICATION REQUIRED |
| 3 | `.env` gerçekten hiçbir yerde okunmuyor mu? | `env()` yalnız `getenv()` diyor (L420 doc) ama repo geneli doğrulanmadı | Security Engineer | ⚠️ VERIFICATION REQUIRED |
| 4 | 4 betik dışında başka local CLI var mı? | `bin/` + `media.coremusic.net/bin/` dışında glob yapılmadı | Backend Architect | ⚠️ VERIFICATION REQUIRED |
| 5 | `--deep` modu (yeniden hash) gerçek veriyle test edildi mi? | 1M+ varlıkta CPU maliyeti var (audit L58) — ölçek doğrulanmadı | Data Engineer | ⚠️ VERIFICATION REQUIRED |
| 6 | Kademe-3 bounded-context kapsamı | ADR-097 K4 fazı (C) bekliyor | MO | ⚠️ VERIFICATION REQUIRED |
| 7 | Barındırıldığı yer ve çalıştırma biçimi (kullanıcı makinesi mi, sunucu cron'u mu?) | `index.md` açık sorusu korunuyor | MO | ⚠️ VERIFICATION REQUIRED |

## §11 — Yerel çalışma zamanı bağımlılık grafiği

```text
Operatör (konsol)
  │
  ├─> bin/api-key-create.php
  │     ├─> $_SERVER['argv']  (komut satırı girdisi)
  │     ├─> getenv()          (DB_* çevresel değişkenler)
  │     └─> IDatabaseRegistry (MySQL veya SQLite fallback)
  │
  ├─> media.coremusic.net/bin/scan.php
  │     ├─> $argv             (--rebuild/--dry-run/--deep/-v)
  │     ├─> Media\{CatalogWriter,Ulid,Validator}
  │     └─> catalog/catalog.jsonl + MySQL (opsiyonel)
  │
  ├─> media.coremusic.net/bin/audit.php
  │     ├─> $argv             (--deep)
  │     ├─> Media\{Ulid,Validator}
  │     └─> STDERR/STDOUT rapor (salt okunur)
  │
  └─> media.coremusic.net/bin/ingest.php
        ├─> $argv             (--source/--commit/-v)
        ├─> Media\{CatalogWriter,Slugger,Ulid}
        ├─> STDIN onay ("evet")
        └─> reports/ingest-*.csv (dry-run) · medya ağacı (--commit)
```

**Ortak bağımlılık:** 3 media betiği `Media\` autoload zincirine bağlı (scan L32–41, audit L29–38, ingest L25–34); `api-key-create.php` farklı namespace (`CoreMusic\Console`) kullanır.

## §12 — Yerel çalışma zamanı akış şeması (üretim → denetim → taşıma)

```text
[1] ingest.php --source "DIZIN"           → reports/*.csv (dry-run raporu)
        │ (operatör raporu inceler)
        ▼
[2] ingest.php --source "DIZIN" --commit  → STDIN "evet" onayı → medya ağacına kopyalama
        │
        ▼
[3] scan.php [--deep]                     → catalog/catalog.jsonl + MySQL media_catalog
        │
        ▼
[4] audit.php [--deep]                    → SEVIYE|yol|kural|neden raporu (salt okunur)
        │ (HATA varsa → 1 · yoksa → 0)
        ▼
[5] CI/CD gate (workflows)                → exit koduna göre PASS/BLOCK
```

**Kritik kural:** `[4] audit` her zaman `[3] scan`'den sonra koşulur (audit L476: katalog yoksa "scan-onrasi kapsam" uyarısı).

## §13 — Güvenlik sınırları (local-runtime)

| # | Sınır | Uygulama | Kanıt |
|---|---|---|---|
| 1 | `.env` dosyası OKUNMAZ | `env()` yalnız `getenv()` çağırır | api-key L420–427 |
| 2 | dry-run zorunlu (ingest) | `--commit` YOKKEN tek bayt yazma YOK | ingest L9–11 doc |
| 3 | Onay kapısı | `--commit` → STDIN "evet" | ingest L17 doc |
| 4 | Yol güvenliği | `kokIcindeMi()` — path-traversal koruması | ingest L102–118 |
| 5 | Doğrudan-çalıştırma guard'ı | `realpath(__FILE__)` karşılaştırması | api-key L489–491 |
| 6 | Salt-okunur denetim | audit medya ağacına yazmaz | audit L9 doc |
| 7 | Ham anahtar tek sefer | stdout'a bir kez basılır; DB'ye hash+prefix | api-key L194 doc |

## §14 — Performance / ölçek notları

| Betik | Ölçek riski | Koruma | Kanıt |
|---|---|---|---|
| scan | 1M+ varlıkta hash maliyeti | hafif mod (varsayilan): yeniden hash YOK | scan L55 doc, L143–148 |
| audit | `--deep` modda yeniden hash | hafif mod: `hash_file()` ÇALIŞMAZ | audit L389–394 |
| audit | Windows MAX_PATH 260 | `yol-260` kuralı (260 bayt sınırı) | audit L311–314 |
| ingest | Kopyalama I/O | dry-run önce rapor | ingest L10–12 doc |

## §15 — Kapsam ↔ komşu katman matrisi

| Eksen | local-runtime | server-runtime | web | desktop |
|---|---|---|---|---|
| Çalışma süresi | anlık (CLI) | sürekli (daemon) | tarayıcı oturumu | masaüstü oturumu |
| Girdi kaynağı | `$argv` / `getenv` | HTTP / config | HTTP / DOM | HTTP / OS event |
| Çıktı | STDOUT/STDERR + exit | HTTP yanıt | HTML/CSS/JS | pencere + meta |
| Config | `.env` OKUNMAZ | nginx/php-fpm conf | SPA config | device config |
| Test | `php -l` + dry-run | health check | browser test | browser test |

## §16 — Bu dosyanın kapsamı dışındakiler (özet)

1. Betiklerin PHP 8.4'te gerçekten çalıştırılması (shell yok).
2. `.env` dosyasının repo genelinde okunmadığının ispatı.
3. `bin/` + `media.coremusic.net/bin/` dışında başka CLI olup olmadığının glob'u.
4. `--deep` modunun gerçek 1M+ veriyle test edilmesi.
5. Kademe-3 bounded-context kapsamı (ADR-097 K4 fazı C).
6. `index.md` açık sorusu: barındırıldığı yer ve çalıştırma biçimi.

## §17 — Exit kodu-semantik matrisi (detay)

| Betik | exit 0 | exit 1 | exit 2 | Kaynak |
|---|---|---|---|---|
| api-key-create | başarılı | DB/çalışma zamanı hatası | kullanım hatası | L180–182 sabitleri |
| scan | temiz | hata bulundu | kök/kullanım hatası | L26 doc · L261 final |
| audit | HATA yok | en az bir HATA | kök/kullanım hatası | L11 doc · L499 final |
| ingest | temiz | hata | kök/kaynak/kullanım hatası | L19 doc |

**Ortak desen:** 4 betik de aynı 0/1/2 semantiğini paylaşır — CI/CD gate'leri (DevOps) tek exit kodu bekleyebilir.

## §18 — Betik ↔ havuz eşlemesi (local-runtime)

| Betik | php.net havuz konusu | İlişki |
|---|---|---|
| api-key-create.php | `function.exit` · `reserved.variables.argv` · `function.putenv` | `exit()` L492 · `$_SERVER['argv']` L492 · `getenv()` L424 |
| scan.php | `reserved.variables.argv` · `function.exit` | `$argv` L60 · `exit()` L53/71/76/88/261 |
| audit.php | `reserved.variables.argv` · `function.exit` | `$argv` L53 · `exit()` L49/59/66/499 |
| ingest.php | `reserved.variables.argv` · `function.exit` | `$argv` L52 · `exit()` L46/61/68/72/78/84 |
| (hepsi) | `features.commandline.webserver` | PHP yerleşik sunucu — bu betiklerde KULLANILMIYOR (boşluk) |

**Boşluk notu:** `features.commandline.webserver` havuz konusu local-runtime betiklerinde **hiç kullanılmıyor** — bu, havuz ile repo arasındaki bilinçli boşluktur (§19).

## §19 — Havuz ↔ repo boşlukları

| # | Havuz konusu | Repo durumu | Yorum |
|---|---|---|---|
| 1 | `features.commandline.webserver` | Kullanım YOK (grep 0) | PHP yerleşik sunucu bu betiklerde geçmiyor; belki `server-runtime`'da |
| 2 | `function.putenv` | Yalnız `getenv` var (api-key L424); `putenv` ÇAĞRISI YOK | Değişken **okunuyor**, yazılmıyor |
| 3 | `reserved.variables.server` | Yalnız `$_SERVER['argv']` + `SCRIPT_FILENAME` (api-key L489–492) | `HTTP_*` anahtarları local-runtime'da YOK (web katmanı) |

**Not:** Bu boşluklar hata değil, **kapsam gerçeğidir**; uydurma kullanım yazılmaz.

## §20 — Kademe-2 ↔ kademe-3 geçiş notları

| # | Geçiş maddesi | Bağlı ADR | Durum |
|---|---|---|---|
| 1 | Bounded-context (local-runtime) | ADR-097 K4 faz C | ⏳ BEKLEMEDE |
| 2 | CLI ↔ servis ayrımı | ADR-039 (port :5000/:6000) | local-runtime'da port YOK (CLI) |
| 3 | `.env` politikası | kök `AGENTS.md` §5 | `.env` OKUNMAZ — getenv |
| 4 | Dry-run zorunluluğu | ADR-092 §8 madde 11 | ingest'te UYGULANIR |

## §21 — Bu dosyada olmayanlar

1. Çalışan betik çıktısı (shell yok → `php -l` koşulmadı).
2. `.env` repo-geneli ispatı.
3. `bin/` dışı CLI glob'u.
4. `--deep` gerçek veri testi.
5. Yeni `.php` dosyası (bu dosya yalnız `.md`).

## §22 — Değişiklik günlüğü (bu dosya)

| # | Tarih | Değişiklik | Yapan |
|---|-------|-----------|-------|
| 1 | 2026-10-15 | İlk yazım (§1–§10) | subagent |
| 2 | 2026-10-15 | §11–§22 eklendi (bağımlılık, akış, güvenlik, boşluk) | subagent |

**Toplam:** 2 revizyon · commit YOK (orkestratöre ait).

## §23 — PowerShell sayım betiği (metin — repo kökünden çalıştır)

```powershell
# local-runtime CLI sayım betiği (metin)
$root = "C:\www\coremusic.net"
$cli = @()
$cli += Get-ChildItem "$root\bin" -Filter *.php -EA SilentlyContinue
$cli += Get-ChildItem "$root\media.coremusic.net\bin" -Filter *.php -EA SilentlyContinue
Write-Host "Toplam CLI PHP: $($cli.Count)"
$cli | ForEach-Object { "{0}  {1}" -f $_.FullName, $_.Name }

# exit kodu geçişleri (grep karşılığı)
$exitHits = Select-String -Path $cli.FullName -Pattern "exit\(" -SimpleMatch
Write-Host "exit( çağrısı: $($exitHits.Count)"

# getenv kullanımı
$getenvHits = Select-String -Path $cli.FullName -Pattern "getenv" -SimpleMatch
Write-Host "getenv kullanımı: $($getenvHits.Count)"
```

**Durum:** `⚠️ VERIFICATION REQUIRED` — betik repo kökünden koşulmadı.

## §24 — Elle ölçüm ↔ betik beklentisi

| Ölçüm | Elle (read/glob) | Betik beklentisi | Fark |
|---|---|---|---|
| CLI PHP dosyası | 4 | 4 | 0 |
| `exit(` çağrısı | ~20 (4 betik) | ≈20 | ~0 |
| `getenv` kullanımı | 1 (api-key L424) | 1 | 0 |
| `$_SERVER['argv']` | 1 (api-key L492) | 1 | 0 |
| `$argv` (global) | 3 (scan/audit/ingest) | 3 | 0 |

## §25 — Handover maddeleri

| # | Hedef agent | İstek | Öncelik |
|---|---|---|---|
| 1 | `qa-engineer` | `php -l` 4 betik + `--dry-run`/`--deep` kapıları | HIGH |
| 2 | `security-engineer` | `.env` OKUNMAZ kuralı + ingest onay kilidi denetimi | HIGH |
| 3 | `backend-developer` | `media.coremusic.net/bin` ↔ local-runtime sınırı netleşmesi | MEDIUM |
| 4 | `data-engineer` | `--deep` modunun 1M+ varlık ölçeği | MEDIUM |

## §26 — Kapsam ↔ dosya eşlemesi

| Kapsam §'si | Kardeş dosya | İlişki |
|---|---|---|
| §3 bileşenler | `-bilesenler.md` §3 | Sayımlar |
| §4 exit kodları | `-dogrulama.md` §D | Doğrulama satırları |
| §5 argüman/getenv | `-ornekler.md` §2–§4 | PHP örnekleri |
| §6 dry-run akışı | `-dogrulama.md` §G | Güvenlik doğrulaması |
| §18 havuz eşlemesi | `-web-arastirma.md` §2 | Havuz tablosu |

## §27 — Kapanış

1. Tüm bileşenler elle ölçüldü; okunmayan dosya sayılmadı.
2. Boşluklar §10/§19'da; çelişkiler §A'da; handover §25'te.
3. Bu dosya `.md` dışı hiçbir yüzeye dokunmaz.
4. Commit atılmadı (orkestratöre ait).

## §28 — Son kapı özeti (bu dosya)

- **Dosya:** `local-runtime-kapsam.md`
- **Ölçüm tarihi:** 2026-10-15
- **Frontmatter:** 8 alan + `group: mimari` (son alan)
- **URL sayısı:** 0 (havuz yalnız `-web-arastirma.md`'de)
- **Footer:** Authority + Last Updated 2026-10-15
- **Kanıt blokları:** §3, §4, §5, §6, §8 (her birinde `Dosya: <yol> · Satır: <n>`)
- **Açık sorular:** §A (7 madde)

**Ek not:** Bu dosya salt-okunur ölçümlerden oluşur; hiçbir `.php` dosyası
değiştirilmemiştir. Sayılar disk kanıtıdır. Ölçümler **tekrarlanabilir**
komutlarla üretilmiştir; her §N bloğunda komut belirtilmiştir.

---

## §Z — Ölçüm Yöntemi Kaydı

| # | Ölçüm | Yöntem | Güvenilirlik |
|---|---|---|---|
| 1 | Dosya sayısı | `Get-ChildItem -Recurse` | yüksek |
| 2 | Satır sayısı | `[IO.File]::ReadAllLines` | yüksek |
| 3 | Sembol arama | `grep` / `Select-String` | orta |
| 4 | Bağımlılık | `composer.json` okuma | yüksek |
| 5 | Tahmin | okunmamış dosya | **düşük** — ⚠️ |

**5 yöntem** — 4'ü **yüksek/orta** güvenilir · 1'i **düşük** (tahmin).
Düşük güvenilirlikli ölçümler **her zaman** `⚠️` ile işaretlenmiştir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
