---
name: composer-sync
description: "Use when composer dependencies drift between shared/ and subdomain composer.json files, autoload breaks, or vendor junction sync is needed — Tetikleyiciler: 'composer', 'vendor', 'autoload', 'junction', 'class not found', 'bağımlılık senkron'."
license: "Proprietary — CoreMusic Internal"
metadata:
  version: 4.0.0
  format: "Claude Skill v3.0"
  author: "CoreMusic Agent System"
  category: "devops"
  tags: ["composer", "vendor", "autoload", "junction", "windows", "php-8-4", "psr-4", "dependency-management"]
  updated: 2026-10-21
  previous-version: "3.0.0"
---

# Composer Sync — Vendor Bağımlılık Senkronizasyonu

> **Amaç:** `shared/` paketinin (coremusic/shared-infrastructure) subdomain vendor
> ağaçlarına (junction) düzgün yansıdığını doğrulamak, autoload kırılmalarını
> onarmak ve Composer işlemlerini production-safe yürütmek.
>
> **Skill Usage Mandate:** Görev `composer` / `vendor` / `autoload` / `junction` /
> `class not found` / `bağımlılık senkron` tetikleyicilerinden biriyle eşleşiyorsa
> bu dosya okunmadan komut çalıştırılmaz.

---

## §0 Genel Bakış — Bu Skill Ne Yapar, Ne Yapmaz

### Yapar

1. `shared/` PSR-4 paketinin subdomain `vendor/` ağaçlarına junction ile bağlı
   olduğunu **kanıtla** (LinkType + Target sorgusu).
2. `composer install` / `composer update` arasındaki farkı **karar matrisi** ile
   ayırır — hangisinin ne zaman güvenli olduğunu söyler.
3. `dump-autoload` ailesini (`--optimize`, `--classmap-authoritative`, `--apcu`,
   `--dry-run`) doğru senaryoda uygular.
4. `Class "X" not found` hatalarını **kanıt zinciriyle** teşhis eder (autoload
   kırık mı, dosya yok mu, PSR-4 ihlali mi).
5. `composer.lock` disiplinini dayatır: lock olmadan install/update YASAK.
6. Production'da Composer çalıştırılmasını **staging gate** ile engeller.
7. Kullanılmayan (orphan) paket temizliğini yalnız lock doğrulamasıyla yapar.

### Yapmaz

1. Proje `composer.json` dosyalarını düzenLEMEZ (bozuk bulursa raporlar + düzeltme
   önerir, karar kullanıcıya ait).
2. `vendor/` ağaçlarını elle silMEZ (junction hedefine dokunmak shared/ kaynak
   kodunu silebilir — §7.4).
3. `git commit` ATMAM — commit orkestratöre aittir.
4. `.ai/` vault'una yazMAZ.
5. Tahminle komut çalıştırmaz; `php`/`composer` PATH'te yoksa §6.5'e göre raporlar.

---

## §1 Tetikleyici Senaryolar (ne zaman bu skill)

| # | Senaryo | Tetikleyici cümle | Hızlı aksiyon |
|---|---------|-------------------|---------------|
| T1 | Bağımlılık senkronu | "composer senkronla", "bağımlılık güncelle" | §4 karar matrisi → §5 |
| T2 | Class not found | `Class "CoreMusic\..." not found` | §6 teşhis zinciri |
| T3 | Junction doğrulama | "junction kırık", "vendor bağlantısı" | §3 |
| T4 | Autoload yeniden üretim | "autoload dump", "otoloader" | §5 dump-autoload |
| T5 | Lock tutarlılığı | "lock güncelle", "composer.lock" | §7 |
| T6 | Production güvenlik | "production'da composer" | §8 |
| T7 | Orphan paket temizliği | "kullanılmayan paket" | §9 |
| S-EXPLORER | Keşif (composer.json okuma) | — | §2 mimari harita |

---

## §2 Mimari Harita — CoreMusic Composer Yapısı

> **Kanıt (disk, 2026-10-21):** Aşağıdaki yapı `C:\www\coremusic.net` altında
> ölçülmüştür. Tahmin değildir.

```
C:\www\coremusic.net\
├── shared\                          ← PSR-4 kaynak paketi (source of truth)
│   ├── composer.json                (coremusic/shared-infrastructure v2.0.0)
│   ├── composer.lock
│   ├── src\                         (PSR-4: CoreMusic\ → src/)
│   └── vendor\                      (shared'ın kendi vendor'ı — subdomain'lere KULLANILMAZ)
├── admin.coremusic.net\vendor\coremusic\shared-infrastructure  → JUNCTION → ..\..\shared
├── api.coremusic.net\vendor\coremusic\shared-infrastructure    → JUNCTION → ..\..\shared
├── auth.coremusic.net\vendor\coremusic\shared-infrastructure   → JUNCTION → ..\..\shared
├── home.coremusic.net\vendor\coremusic\shared-infrastructure   → JUNCTION → ..\..\shared
└── media.coremusic.net\vendor\...                             (junction durumu §3.2'de doğrulanır)
```

### §2.1 shared/composer.json — kaynak paket kimliği (gerçek değerler)

```json
{
    "name": "coremusic/shared-infrastructure",
    "type": "project",
    "version": "2.0.0",
    "require": {
        "php": ">=8.4",
        "ext-pdo": "*",
        "ext-pdo_mysql": "*",
        "ext-mbstring": "*",
        "ext-json": "*",
        "ext-openssl": "*",
        "vlucas/phpdotenv": "^5.6"
    },
    "autoload": {
        "psr-4": {
            "CoreMusic\\": "src/"
        }
    }
}
```

**Kritik noktalar:**

1. `CoreMusic\` namespace'i `shared/src/` altındadır — subdomain'de
   `CoreMusic\Logger` aranıyorsa `shared/src/Logger.php` DOSYASI OLMALIDIR.
   Dosya yoksa autoload dump çözüm DEĞİLDİR (§6.3).
2. `php >=8.4` zorunlu — subdomain composer.json `platform.php` bu değeri
   tutmuyorsa install `platform-check` ile patlar (§10 R5).
3. `vlucas/phpdotenv ^5.6` tek üçüncü parti bağımlılıktır — lock'ta sürümü
   sabitlenmiştir.

### §2.2 Subdomain path-repo tanımı (auth.coremusic.net — kanıt)

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../shared",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "coremusic/shared-infrastructure": "@dev"
    }
}
```

**Yorum:** `type: path` + `symlink: true` isteniyor; Composer Windows'ta
junction'a düşer (§3.1). `@dev` branch-based path repo kuralıdır — lock'ta
`dist.reference` = shared commit SHA'sına sabitlenir.

---

## §3 Windows Junction Doğrulama (vendor/shared bağıntısı)

### §3.1 Junction vs Symlink vs Hardlink — neden vendor'da junction

| Özellik | Directory Symlink | Junction | Hardlink (dosya) |
|---------|-------------------|----------|------------------|
| Hedef | mutlak/göreli path | mutlak path | dosya inode |
| Oluşturma yetkisi | Developer Mode OU admin | admin gerekmez | admin gerekmez |
| Hedef offline | KIRILIR | çalışır (yerel disk) | çalışır |
| `rm -rf vendor` davranışı | hedefe YAZMAZ | hedefe YAZMAZ | hedefe dokunmaz |
| Path geçerliliği | göreli olabilir | MUTLAK path (kırılabilir) | — |
| `is_link()` PHP | true | **false** (dir olarak görünür) | — |

**Sonuç:** Composer Windows'ta path-repo `symlink: true` istendiğinde junction
kullanır — PHP `is_link()` junction'da `false` döndürür; junction doğrulaması
`LinkType` özelliğiyle yapılır, `is_link()` ile DEĞİL.

> Bu tablo §12 kaynak S3 (Microsoft Learn — Windows C#: Junctions and
> Symbolic Links) ile doğrulanmıştır; erişim 2026-10-21.

### §3.2 Zorunlu doğrulama komutu (her sync öncesi)

```powershell
# Tüm subdomain vendor junction'larını tek seferde doğrula
$subdomains = @('admin','api','auth','home','media')
foreach ($s in $subdomains) {
    $p = "C:\www\coremusic.net\$s.coremusic.net\vendor\coremusic\shared-infrastructure"
    if (Test-Path $p) {
        $item = Get-Item $p -Force -ErrorAction SilentlyContinue
        "{0,-8} LinkType={1,-8} Target={2}" -f $s, $item.LinkType, ($item.Target -join ',')
    } else {
        "{0,-8} YOK — composer install çalıştırılmamış" -f $s
    }
}
```

**Beklenen çıktı (sağlıklı ağaç):**

```
admin    LinkType=Junction Target=C:\www\coremusic.net\shared
api      LinkType=Junction Target=C:\www\coremusic.net\shared
auth     LinkType=Junction Target=C:\www\coremusic.net\shared
home     LinkType=Junction Target=C:\www\coremusic.net\shared
media    LinkType=Junction Target=C:\www\coremusic.net\shared
```

**Bozuk durum belirtileri:**

| Çıktı | Anlamı | Aksiyon |
|-------|--------|---------|
| `LinkType=` (boş) + dosya/dizin var | junction değil, gerçek kopya veya boş dizin | §6.4 |
| `YOK` | hiç kurulmamış | o subdomain'de §4 matrisine göre install |
| `Target=` başka bir yol | yanlış hedef (eski taşınma) | §6.4 junction onarımı |
| Erişim hatası | junction hedefi silinmiş/kaldırılmış | §6.4 |

### §3.3 PHP ile junction testi (alternatif)

```php
<?php
declare(strict_types=1);

$vendorShared = __DIR__ . '/vendor/coremusic/shared-infrastructure';

// Junction'da is_link() false döndürür — LinkType kontrolü şart
$real = realpath($vendorShared);
$expected = realpath(__DIR__ . '/../shared');

if ($real === false) {
    fwrite(STDERR, "BROKEN: junction hedefi erişilemiyor\n");
    exit(1);
}
if ($real !== $expected) {
    fwrite(STDERR, "WRONG TARGET: {$real} != {$expected}\n");
    exit(1);
}
echo "OK: junction → {$real}\n";
```

---

## §4 Senkron Karar Matrisi — install vs update vs dump-autoload

### §4.1 Karar tablosu (görev → komut)

| Durum | Komut | Neden | Risk |
|-------|-------|-------|------|
| lock var, vendor eksik/taze | `composer install` | lock'taki tam sürümü kurar, deterministik | DÜŞÜK |
| lock var, vendor var, sadece sınıf bulunamıyor | `composer dump-autoload` | dosyalar yerinde, yalnız otoloader bayat | YOK |
| shared/src'a YENİ sınıf eklendi | `composer dump-autoload --optimize` | classmap'lenmemiş yeni dosya | YOK |
| bağımlılık sürümü bilinçli yükseltilecek | `composer update --with-all-dependencies <paket>` | lock bilinçli değişir | ORTA |
| composer.json DEĞİŞTİ (require eklendi) | `composer update <paket>` | lock artık eşleşmiyor | ORTA |
| lock yok (ya da bozuk) | **DUR** → §7.3 | deterministik kurulum imkânsız | YÜKSEK |
| production | **DUR** → §8 | canlıda asla | YASAK |

### §4.2 YASAK komutlar (CoreMusic üretim ortamı)

```bash
# ASLA ÇALIŞTIRMA:
composer update                    # tüm ağaç yükseltir — kaza yüzeyi
composer update --no-lock          # lock devre dışı — determinizm gider
composer install --no-dev          # production'da dev zaten yok; local'de test kırar
composer update --prefer-source    # git clone gerekir, PATH'te git yok
rm -rf vendor && composer install  # §7.4 — junction hedefine zarar riski
```

### §4.3 Güvenli sync akışı (adım adım)

```
1. Doğrula: composer.lock var mı?  → yoksa §7.3, varsa devam
2. Doğrula: junction LinkType       → §3.2 komutu, Junction değilse §6.4
3. Değişiklik var mı composer.json'da?
   - HAYIR + sadece sınıf hatası  → §5.1 dump-autoload --optimize
   - HAYIR + vendor eksik         → composer install
   - EVET                         → composer update <paket> (lock diff bak §7.2)
4. lock diff incele (§7.2) — beklenmeyen sürüm sıçraması varsa geri al §7.4
5. Son sınıf-hatası testi (§6.1 smoke testi)
```

---

## §4-b Senkron Öncesi / Sonrası Kontrol Listesi

### §4.4.1 Senkron ÖNCESİ (30 saniye)

1. `git status` → working tree temiz mi? Kiralık ağaçta lock diff'i karışır.
2. `composer.lock` commit edilmiş mi? (`git log -1 --oneline composer.lock`)
3. §3.2 junction audit → tüm subdomain'ler `Junction + ..\shared`?
4. `php`/`composer` erişilebilir mi? (`Get-Command`) → yoksa §6.5.
5. Ne DEĞİŞTİ? (`git diff composer.json`) → §4.1 matrisine göre komut seç.

### §4.4.2 Senkron SONRASI (zorunlu)

1. `git diff composer.lock` → yalnız beklenen paketler mi? (§7.2)
2. §6.2 smoke test → `bool(true)`.
3. §3.2 junction audit → tekrar (install junction'ı yeniden yazmış olabilir).
4. Yeni eklenen sınıflar için autoload_classmap kontrolü (§6.1 ADIM 4).
5. Rapor formatı: ne değişti / hangi komut / kanıt çıktısı / kalan BLOCK.

### §4.4.3 Senkron raporu şablonu

```markdown
## Composer Sync Raporu — <subdomain>
- Komut: composer install (lock: <commit-sha>)
- lock diff: yalnız vlucas/phpdotenv 5.6.x (beklenen)
- Junction: LinkType=Junction Target=..\shared  PASS
- Smoke: CoreMusic\Logger class_exists=true     PASS
- Kalan: (yok | BLOCK: php PATH'te yok)
```

---

## §5 Autoload İşlemleri

> **Kaynak:** S2 — getcomposer.org/doc/03-cli.md#dump-autoload (erişim 2026-10-21).

### §5.1 dump-autoload komut ailesi

```bash
# PSR-4 dizin taraması — her sınıf yüklemesinde filesystem bakar (yavaş ama esnek)
composer dump-autoload

# ÖNERİLEN (CoreMusic standardı): classmap oluşturur, tek dosya = tek bakış
composer dump-autoload --optimize

# --classmap-authoritative: classmap'te olmayan hiçbir sınıf yüklenmez.
# Hızlı + yanlış kullanılırsa "not found" üretir — §5.3'te kural.
composer dump-autoload --optimize --classmap-authoritative

# --apcu: classmap'i APCu önbelleğine yazar (üretimde opcache/APCu varsa)
composer dump-autoload --optimize --apcu

# --no-scripts: install/update tetikleyen script'leri atlar (teşhis için)
composer dump-autoload --optimize --no-scripts

# --dry-run: ne üretileceğini gösterir, DOSYA YAZMAZ (güvenli keşif)
composer dump-autoload --dry-run
```

### §5.2 Ortaya çıkan dosyalar (vendor/composer/)

| Dosya | Rol | Kırık belirtisi |
|-------|-----|-----------------|
| `autoload_psr4.php` | namespace → dizin eşlemesi | boş dizi = composer.json autoload bloğu bozuk |
| `autoload_classmap.php` | sınıf → dosya (optimize'te) | yeni sınıf yok = dump alınmamış |
| `autoload_static.php` | PHP 5.6+ statik ön-yükleme | bayat = dump alınmamış |
| `autoload_real.php` | loader örneği | — |
| `platform_check.php` | PHP/ext sürüm kontrolü | install/update sırasında §10 R5 |

### §5.3 --classmap-authoritative kuralı (dikkat)

`--classmap-authoritative` açıkken Composer yalnız `autoload_classmap.php`'ye
yazılmış sınıfları yükler; PSR-4 fallback YOKTUR. Bu yüzden:

1. Yeni sınıf eklendikten SONRA her zaman yeniden dump alınmazsa
   `Class not found` üretir (en sık yanlış).
2. **CoreMusic standardı:** local development'ta `--optimize` YETERLİ;
   `--classmap-authoritative` yalnız üretim imajı/staging'de kullanılır ve
   orada da her deploy sonrası dump alınır.

### §5.4 Optimized autoloader yapılandırması

```json
{
    "config": {
        "optimize-autoloader": true
    }
}
```

> Kaynak: S1 — getcomposer.org/doc/06-config.md#optimize-autoloader (erişim 2026-10-21).
> `true` yapıldığında `install`/`update` bitiminde otomatik optimize dump alınır —
> §5.1 komutunu elle çalıştırma ihtiyacını azaltır.

### §5.5 dump-autoload hata kodları ve strict-psr-autoloader

> Kaynak: S1 — 06-config.md#strict-psr-autoloader (erişim 2026-10-21).

| Komut | `strict-psr-autoloader: true` iken uyumsuz paket | Hata kodu |
|-------|--------------------------------------------------|-----------|
| `composer install` | uyumsuzsa BASKILAR, kurmaz | exit 6 |
| `composer update` | uyumsuzsa BASKILAR, güncellemez | exit 6 |
| `composer dump-autoload` | uyumsuzsa YAZAR ama uyarır | exit 1 |

**CoreMusic kararı:** `strict-psr-autoloader` subdomain composer.json'larında
`true` TUTULUR — PSR-4 ihlalini CI'da erken yakalar. Local makinede exit 1
görülürse §6.1 ADIM 2 (namespace beyanı) kontrol edilir, composer.json
"esnetilmez".

### §5.6 subdomain bazlı senkron sırası (bağımlılık zinciri)

`shared/` değiştikten sonra subdomain'ler SIRALI senkronlanır:

```
1. shared\              → composer update (lock güncellenir; §7.2 diff incele)
2. admin.api.auth.home   → tek tek: cd <sub> && composer install
3. media (varsa)         → cd media.coremusic.net && composer install
4. §3.2 junction audit   → tüm subdomain'lerde LinkType=Junction
5. §6.2 smoke test       → en az 1 subdomain'de class_exists
```

**Neden sıra:** install lock'tan okur; shared'de lock GÜNCELLENMEDEN
subdomain install edilirse eski sürüm kurulur ve junction üzerinden eski
namespace'ler görünür — "bende çalışıyor" tuzağı.

### §5.7 cache kullanımı (tekrarlı install'lar)

```bash
# Composer paketleri önbellekten kurar; cache temizliği nadiren gerekir
composer clear-cache
# cache konumu/env:
# COMPOSER_CACHE_DIR — yalnız CI secret store'dan enjekte edilir (§11.4)
```

`cache-files-maxsize` (§9.1) aşıldığında Composer eski sürümleri kendi
temizler; elle `clear-cache` yalnız "corrupted cache" şüphesinde çalıştırılır.

### §5.9 --dry-run çıktısı örneği (doc-tabanlı, NOT TESTED)

```bash
cd C:\www\coremusic.net\auth.coremusic.net
composer dump-autoload --dry-run --optimize
```

**Beklenen çıktı formu (S2 — 03-cli.md):**

```
Generating optimized autoload files (dry-run)
No files were written (dry-run)
```

> `--dry-run` yazım yapmaz; yalnız ne üretileceğini bildirir. CI ön kontrolünde
> veya "dump bayat mı?" şüphesinde güvenli keşif aracıdır (§5.1).

---

## §6 "Class not found" Teşhis Zinciri (kanıt sırası)

### §6.1 Zincir (her adım KANITLA yapılır, tahmin yok)

```
HATA: PHP Fatal error: Uncaught Error: Class "CoreMusic\Xxx" not found

ADIM 1 — Dosya var mı? (namespace→dosya eşlemesi)
  CoreMusic\Xxx  ⇒  shared/src/Xxx.php
  powershell>    Test-Path C:\www\coremusic.net\shared\src\Xxx.php
  - DOSYA YOK   → sınıf yazılmamış; autoload çözüm değildir. §6.3.
  - DOSYA VAR   → ADIM 2

ADIM 2 — PSR-4 uyumlu mu? (dosya içi namespace beyanı)
  powershell>    Select-String -Path shared\src\Xxx.php -Pattern 'namespace'
  - beyan yok / farklıysa → PSR-4 ihlali: düzelt composer.json değil, DOSYAYI düzelt (namespace)

ADIM 3 — vendor bağlantısı sağlıklı mı?
  §3.2 junction doğrulaması — BROKEN/WRONG → §6.4

ADIM 4 — Otoloader güncel mi?
  powershell>    Select-String -Path <sub>\vendor\composer\autoload_classmap.php -Pattern 'Xxx'
  - yoksa        → composer dump-autoload --optimize   (§5.1)
  - varsa        → ADIM 5

ADIM 5 — classmap-authoritative bayat mı? (§5.3)
  dump alınmış ama AFTER yeni sınıf eklenmişse → yeniden dump
```

### §6.2 Smoke testi (sync sonrası zorunlu)

```powershell
php -r "require 'C:/www/coremusic.net/auth.coremusic.net/vendor/autoload.php'; var_dump(class_exists('CoreMusic\Logger'));"
# bool(true) beklenir — false ise §6.1 zincirine dön
```

> **VERIFICATION REQUIRED:** `php`/`composer` binary'leri 2026-10-21 itibarıyla
> bu oturumun shell PATH'inde GÖRÜLMEMİŞTİR. Gerçek çıktı üretmek için
> ortamda `composer --version` doğrulanmalıdır; doğrulanamazsa komutlar
> `VERIFICATION REQUIRED` etiketiyle raporlanır, "çalıştı" denmez.

### §6.3 Sınıf dosyası YOK ise

Sınıf yazılmamışsa yapılacak tek doğru şey: sınıfı PSR-4 kuralına göre
`shared/src/` altına yazmak. `composer.json`'a `files`/`classmap` ekleyerek
"çözme" — ITCSS katmanlarını bozan anti-pattern'dir, yasak.

### §6.4 Junction onarımı (bozuk/wrong-target)

```powershell
# 1) Bozuk junction'ı temizle (HEDEF DOKUNMA — vendor altındaki junction'ı sil)
Remove-Item "C:\www\coremusic.net\auth.coremusic.net\vendor\coremusic\shared-infrastructure" -Force -Recurse -ErrorAction SilentlyContinue

# 2) Tekrar kur — Composer path-repo symlink/junction'ı yeniden üretir
cd C:\www\coremusic.net\auth.coremusic.net
composer install

# 3) Yeniden doğrula — §3.2
```

**UYARI:** `Remove-Item -Recurse` junction üzerinde POSIX `rm -rf` gibi hedefe
yazabilir/erişebilir (junction = gerçek dizin girişi). Silmeden önce
`Get-Item -Force` ile `LinkType=Junction` TEYİT EDİLMEDEN silme yapılmaz.
Şüphe varsa §7.4 yedeği al.

### §6.6 Vaka Analizleri (kanıt zinciri uygulaması)

**Vaka A — "Bende çalışıyor, production'da Class not found"**

```
BELİRTİ: localde sınıf yükleniyor, production paketinde fatal error.
KANIT 1: production vendor/ artefaktında autoload_classmap.php → sınıf YOK.
KANIT 2: CI job log'unda dump adımı YOK.
KÖK NEDEN: §8.3 uyarlanmamış — deploy artefaktı dump alınmadan paketlenmiş.
ONARIM: CI'a --classmap-authoritative dump ekle (§11-b), artefaktı yeniden üret.
YASAK: production sunucuda elle composer dump çalıştırmak (§8).
```

**Vaka B — `shared/` taşındı, tüm junction'lar kör**

```
BELİRTİ: her subdomain'de "Failed to open stream" / class not found.
KANIT 1: §3.2 çıktısı → Target= ESKİ YOL (C:\www\shared-eski) veya erişim hatası.
KANIT 2: Test-Path C:\www\coremusic.net\shared\src → True (kaynak duruyor).
KÖK NEDEN: junction mutlak path'e bağlı (§3.1), kaynak taşınınca kör oldu.
ONARIM: her subdomain'de §6.4 — junction'ı sil + composer install (lock'tan yeniden üret).
ÖNLEME: shared'ı proje kökü içinde TUT; junction mutlak path taşımaya düşman.
```

**Vaka C — elle kopya junction'ı istila etti**

```
BELİRTİ: shared'e eklenen sınıf subdomain'de görünmüyor (ama eski sınıflar çalışıyor).
KANIT 1: §3.2 → LinkType= (boş), dizin GERÇEK.
KANIT 2: Get-ChildItem → dosyalar junction'da olması gerekenden ESKİ/eksik.
KÖK NEDEN: bir ara adımda vendor/shared elle kopyalanmış (anti-pattern A7).
ONARIM: gerçek dizini sil (LinkType teyidi KOLAY — zaten junction değil) → composer install.
ÖNLEME: §14 A7 — vendor'a elle dosya koyma; yalnız Composer yazar.
```

---

## §7 composer.lock Disiplini

### §7.1 Kurallar (bağlayıcı)

1. `vendor/` + `composer.lock` birlikte commit edilir; lock OLMADAN install
   deterministik değildir.
2. `composer install` lock'tan okur; lock'ta OLMAYAN hiçbir sürüm kurulmaz.
3. Sadece `composer install` çalıştırılan yerde (CI, staging, production
   deploy) lock asla elle düzenlenmez.
4. `shared/composer.json` değiştiyse → shared'de `composer update` çalıştır →
   lock diff'ini §7.2 ile incele → SONRA subdomain'lerde install.

### §7.2 lock diff inceleme

```bash
# Sürüm sıçramalarını göster (lock JSON'u büyük — diff ile)
git diff composer.lock
# veya yalnız package satırları:
git diff composer.lock | Select-String -Pattern '"name"|"version"'
```

**Kabul kriteri:** diff'te yalnız BEKLENEN paketler görünür. `symfony/*`,
`psr/*` gibi transit bağımlılıklarda major sıçrama varsa
`--with-all-dependencies` istenmeyen yükseltme yapmış olabilir → geri al §7.4.

### §7.3 Lock yoksa / bozuksa

```bash
composer validate --no-check-publish --no-check-all
# "lock file is not up to date" veya "composer.json is valid but lock ..." hatası
```

1. `git status` → lock commit edilmemiş olabilir; `git checkout -- composer.lock`.
2. Lock hiç yoksa (ilk kurulum): yalnız `composer update` kilidi üretir — bu
   bilinçli bir "ilk kurulum"dur, senkron DEĞİLDİR. İlk kurulum sonrası lock
   hemen commit edilir.
3. `composer validate` → `composer install --dry-run` sırasıyla çalıştırılır.

### §7.4 Geri alma (rollback)

```bash
# 1) lock'u git'den geri getir
git checkout -- composer.lock

# 2) vendor'ı lock'a göre yeniden kur — §6.4 UYARISI: junction'ları önce doğrula
composer install

# 3) doğrula: §6.2 smoke testi
```

---

## §8 Production Güvenliği (staging gate)

### §8.1 Kural

**Production sunucuda `composer install/update` YASAKTIR** — istisnası
`STAGING=true` environment flag'i ile kapalı staging ortamıdır.

### §8.2 Doğrulama komutu (deploy öncesi)

```powershell
# Environment flag kontrolü — staging değilse composer çalıştırılmaz
if ($env:STAGING -ne 'true') {
    Write-Host 'PRODUCTION — composer çalıştırılmaz; vendor deploy artefaktıdır'
} else {
    Write-Host 'STAGING — composer install limektedir'
}
```

### §8.3 Üretim deseni

1. Vendor artefaktı: CI'da `composer install --no-dev --classmap-authoritative`
   ile üretilir, deploy paketine `vendor/` + `composer.lock` dahil edilir.
2. Sunucuda yalnız `php artisan` benzeri uygulama komutları çalışır —
   Composer ağ erişimi/disk yazarma istemez.
3. `composer.lock` deploy paketinde aynı SHA'da bulunur; mismatch = deploy durur.

---

## §9 Önbellek ve Platform Yapılandırması

### §9.1 Composer cache (CLI)

| Ayar | Değer | Not |
|------|-------|-----|
| `cache-files-maxsize` | `500M` | varsayılan `1G`; disk tasarrufu için düşürülebilir |
| `cache-files-dir` | platform cache dizini | env `COMPOSER_CACHE_DIR` ile ezilebilir |

```json
{
    "config": {
        "cache-files-maxsize": "500M"
    }
}
```

> Kaynak: S1 — 06-config.md#cache-files-maxsize (erişim 2026-10-21).

### §9.2 htaccess-protect (web'den vendor erişimi)

```json
{ "config": { "htaccess-protect": true } }
```

> Varsayılan `true` — Composer cache dizinlerine web'den erişimi `.htaccess`
> ile engeller. Apache kullanılmayan ortamlarda (nginx) bu koruma DOSYA
> düzeyinde DEĞİLDİR; nginx `location` bloğu ile `vendor/` ve cache dizinleri
> için `deny all` ayrıca KONULMALIDIR. Bu nginx kuralı bu skill'in dışındadır —
> `security-hardening` skill'ine yönlendirilir.

### §9.3 config.platform — hedef PHP'yi sabitleme

```json
{
    "config": {
        "platform": {
            "php": "8.4.14"
        }
    }
}
```

> Composer, `platform.php` değeri üzerinden bağımlılık çözümlemesini gerçek
> yerel PHP yerine bu sürüme göre yapar — deploy sunucusu farklı sürümdeyse bile
> lock deterministik kalır. `shared/composer.json` `php >=8.4` istediği için
> platform değeri `8.4.*` bandında tutulmalıdır.

### §9.4 platform-check

```json
{
    "config": {
        "platform-check": true
    }
}
```

> `true` → `vendor/composer/platform_check.php` üretilir; autoload başında PHP
> sürümü + gerekli ext'ler kontrol edilir. `false` → dosya üretilmez (PHP
> sürümü düşükse sınıf-bulunamadı gibi belirsiz hatalar çıkar). CoreMusic'te
> `true` KALIR; ext eksikliğinde install aşamasında erken patlar.

---

## §10 Sorun Giderme Tablosu (semptom → kök neden → aksiyon)

| # | Semptom | Kök neden | Aksiyon |
|---|---------|-----------|---------|
| R1 | `Class "CoreMusic\X" not found` (dosya mevcut) | dump alınmamış | `composer dump-autoload --optimize` (§5.1) |
| R2 | Aynı hata + `--classmap-authoritative` açık | yeni sınıf classmap'te yok | yeniden dump; local'de §5.3 kuralı |
| R3 | `Package operations: N installs` her install'da | vendor kırık/junction yok | §3.2 doğrula → §6.4 onar |
| R4 | `Your lock file does not contain a compatible set...` | lock/composer.json uyumsuz | `composer validate` → §7.3 |
| R5 | `platform_check` fatal: requires PHP 8.4.x | yerel PHP < 8.4 veya platform.php yanlış | §9.3 platform.php güncelle |
| R6 | install her seferinde `symfony/*` güncelliyor | lock commit edilmiyor | lock'u commit et (§7.1) |
| R7 | `Class ... not found` sadece production'da | production'da dump unutulmuş | §8.3 CI artefaktına dump ekle |
| R8 | junction `LinkType=` boş | path-repo `symlink:false` veya elle kopya | composer.json'ı §2.2'ye göre kontrol |
| R9 | `composer` komutu bulunamadı | PATH'te yok | §6.5 — NOT TESTED raporla |
| R10 | `Could not read ... credentials` | auth.json / COMPOSER_AUTH | credential sızdırmadan env'den ver (§11.4) |

---

## §11 Güvenlik Kuralları (bağlayıcı)

### §11.1 Secret sızıntısı

`auth.json`, `.env`, `COMPOSER_AUTH` değerleri **asla** `.md`/`.json`/`.log`
dosyalarına, commit'lere veya raporlara yazılmaz.

### §11.2 vendor web erişimi

`vendor/` web kökünde (docroot) OLMALIDIR çünkü autoload.php include edilir;
bu yüzden `.htaccess`/nginx `deny all` (§9.2) şarttır.

### §11.3 Script güvenliği

`composer install` `scripts.post-install-cmd` çalıştırır. Yabancı paket
`scripts` blokları bilinmeden `--no-scripts` ile sınırlı tutulur; CoreMusic
kendi `scripts`'ini tanımlıyorsa script'ler tanınır ve çalıştırılır.

### §11.4 Ortam değişkeni kuralı

`COMPOSER_AUTH` yalnız CI secret store'dan enjekte edilir; shell geçmişine,
log'a, ekran çıktısına yazılmaz.

### §11.5 Bilinen env değişkenleri (env substitution)

| Değişken | Rol | Güvenlik |
|----------|-----|----------|
| `COMPOSER_CACHE_DIR` | cache dizini override | değer log'a yazılmaz |
| `COMPOSER_AUTH` | auth.json karşılığı (HTTP basic bearer) | secret — commit/log YASAK |
| `COMPOSER_HOME` | global composer home | sunucuda sabitlenir |
| `COMPOSER_NO_INTERACTION=1` | CI'da non-interactive | güvenli |

---

## §11-b CI Entegrasyon Örneği (deploy artefaktı)

```powershell
# CI job: vendor'ı deterministik üret, sunucuya artefakt olarak taşı
cd shared
composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction

cd ..\auth.coremusic.net
composer install --no-dev --optimize-autoloader --no-interaction

# Artefakt paketi: composer.json + composer.lock + vendor/ + shared/src
# Sunucuda composer ÇALIŞTIRILMAZ (§8) — yalnız dosyalar kopyalanır.
```

**Kabul kriteri:**

1. Job `composer validate` ile başlar; lock/composer.json uyumsuzsa job patlar.
2. `--classmap-authoritative` yalnız CI'da (§5.3 kuralı — local'de yok).
3. Artefakt SHA'sı deploy kaydına yazılır (lock SHA ile eşleşmeli).

---

## §11-c Sürüm Yükseltme Prosedürü (bilinçli update)

1. `composer outdated --direct` → hangi paketler arkada (NOT TESTED gate §6.5).
2. Hedef paketi tekil güncelle: `composer update vendor/package --with-all-dependencies`.
3. lock diff incele (§7.2) → major sıçrama varsa CHANGELOG oku, karar ver.
4. Smoke test + uygulama testleri.
5. subdomain'lere install (§5.6 sırası) → junction audit → rapor (§4.4.3).

---

## §12 Kaynak Doğrulama Tablosu (Truth Mode)

> Tam metin: [references/sources.md](references/sources.md)

| # | Kaynak | URL | Erişim | Kullanıldığı bölüm |
|---|--------|-----|--------|--------------------|
| S1 | Composer Configuration Reference | https://getcomposer.org/doc/06-config.md | 2026-10-21 | §5.4, §9.1–§9.4 |
| S2 | Composer CLI Reference (dump-autoload) | https://getcomposer.org/doc/03-cli.md | 2026-10-21 | §5.1, §7.3 |
| S3 | Microsoft Learn — Junctions & Symbolic Links | https://learn.microsoft.com/en-us/windows/win32/fileio/symbolic-links | 2026-10-21 | §3.1 |

**Doğrulanamayan iddialar:** §6.2 (php PATH), §8.2 (STAGING flag) — ortam
kanıtı yoksa `VERIFICATION REQUIRED` / `NOT TESTED` etiketi taşır.

---

## §13 Kontrol Listesi ve Bağlantılar

| Dosya | İçerik |
|-------|--------|
| [references/sources.md](references/sources.md) | Truth Mode kaynakları (2–3 resmi, URL + erişim tarihi) |
| [references/checklist.md](references/checklist.md) | skill-maker checklist PASS/BLOCK sonuçları |
| [references/windows-junction.md](references/windows-junction.md) | Junction derinlemesine: CreateSymbolicLinkW, yetki, PHP davranışı |
| [references/diagnostics.md](references/diagnostics.md) | Teşhis komutları — kanıt çıktılarıyla (docktane, junction, autoload) |
| [examples/vendor-sync-loop.md](examples/vendor-sync-loop.md) | Tam giriş→çıkış döngüsü: doğrula → install → dump → smoke test |
| [references/changelog.md](references/changelog.md) | Sürüm geçmişi: 3.0.0 (v3.0 format) → 4.0.0 (tam yeniden yazım, 2026-10-21) |

---

## §12-b Hızlı Referans Kartı (buzdolabı özeti)

```
SENKRON ÖNCESİ        git status temiz?  → lock commit edilmiş?  → junction audit (LinkType=Junction)
SADECE "not found"    composer dump-autoload --optimize            (update DEĞİL)
composer.json DEĞİSTİ composer update <paket>  →  git diff composer.lock incele
YENİ SINIF EKLENDİ    dump --optimize  →  smoke test class_exists
LOCK YOK              DUR  →  §7.3 (ilk kurulum ise update + hemen commit)
PRODUCTION            composer YASAK  →  vendor + lock deploy artefaktıdır (§8)
JUNCTION BOZUK        LinkType teyidi → junction sil → composer install (§6.4)
php/composer YOK      NOT TESTED raporla, "çalıştırdım DEME" (§6.5)
```

---

## §13-b SSS (Sık Sorulanlar)

**S1: `vendor/` git'e commit edilir mi?**
C: EVET — CoreMusic deseni. `vendor/` + `composer.lock` birlikte commit edilir;
CI/production sunucuda composer çalıştırılmaz (§8.3), vendor deploy artefaktıdır.
Alternatif desen (vendor git-dışı, CI kurar) bu projede KULLANILMIYOR.

**S2: junction + vendor commit'i birbirini kırar mı?**
C: Git junction'ı bir dizin girişi olarak commit etmez; subdomain'de junction
oluşmamışsa install ile üretilir. Fresh clone'da ilk `composer install`
junction'ları kurar (path-repo `symlink: true`, §2.2).

**S3: `composer install` her seferinde paket indiriyor — normal mi?**
C: HAYIR. Her install'da ağ trafiği varsa cache kırık demektir:
`composer clear-cache` (§5.7) veya `COMPOSER_CACHE_DIR` yanlış yazılmış (§11.5).

**S4: shared'de branch'teyim, subdomain hâlâ eski sınıfı görüyor?**
C: Junction canlıdır — shared'de kaydetiğin her şey anında görünür. Görünmüyorsa
sınıf dump edilmemiştir (§6.1) veya namespace beyanı yanlış (§6.1 ADIM 2).
"Branch değiştirince junction bayatlar" mitidir; junction bayatlamaz.

**S5: `--no-dev` neden local'de yasak?**
C: Local'de test/debug bağımlılıkları (phpunit vb.) `require-dev`'tedir;
`--no-dev` ile kurulmazsa testler kırılır. `--no-dev` yalnız CI artefaktında
ve production paketinde kullanılır (§8.3).

**S6: İki subdomain farklı `vlucas/phpdotenv` sürümünde — sorun mu?**
C: Her subdomain'in lock'u bağımsızdır; sürüm farkı bilinçliyse (§7.2 diff
kabul edildiyse) sorun DEĞİLDİR. Fark bilinçsizse shared'de lock güncellenmemiş
demektir — §5.6 sırasıyla yeniden senkronla.

**S7: `composer.json`'a `"minimum-stability": "dev"` eklemeli miyim?**
C: HAYIR. `@dev` zaten path-repo için gerekli; global minimum-stability
tüm ağaç çözümlerini gevşetir. Gerekirse paket bazında `"@dev"` pinlenir (§2.2).

**S8: media subdomain'inde junction neden LinkType boş çıkıyor?**
C: Olası üç neden: (1) hiç install edilmemiş, (2) `symlink:false` set edilmiş,
(3) elle kopya yapılmış (A7 anti-pattern). §3.2 çıktısını al, §6.4 ile onar;
elle kopyaysa `Get-Item` `LinkType=` boş + dosya/dizin var demektir.

**S9: `dump-autoload --dry-run` ne işe yarar?**
C: Hangi dosyaların üretileceğini gösterir, DOSYA YAZMAZ (§5.1). CI'da ön
kontrol olarak veya "dump bayat mı" şüphesinde kullanılır.

**S10: Composer Windows'ta neden junction'a düşüyor, symlink değil?**
C: `symlink: true` isteniyor ancak symlink oluşturma Developer Mode/Yetki
gerektirir; Composer junction'a düşer (§3.1). `symlink: false` verilirse gerçek
kopya kurulur → A7 anti-pattern (sürüklenme).

---

## §14 Kaçınılacak Anti-Pattern'ler (özet)

| # | Anti-pattern | Neden yanlış |
|---|--------------|--------------|
| A1 | `composer update` "senkronlamak" için | lock determinizmi kırılır, transit major sıçrama |
| A2 | `rm -rf vendor` + install | junction hedefine yazma riski (§6.4), yavaş yeniden kurulum |
| A3 | `classmap-authoritative` + sürekli yeni sınıf ekleme | her ekleme sonrası dump unutulunca R2 hata döngüsü |
| A4 | lock commit edilmemesi | her ortam farklı kurulum → "bende çalışıyor" |
| A5 | production'da composer | ağ/disk/script riski, determinizm yok (§8) |
| A6 | sınıf dosyası yokken composer.json'a `files:` eklemek | PSR-4 katman ihlali (§6.3) |
| A7 | junction'ı elle kopyayla değiştirmek | kopya shared değişikliklerini ALMAZ → sürüklenme |
| A8 | `php`/`composer` yokken "çalıştırdım" demek | zero-hallucination ihlali (§6.5) |

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
