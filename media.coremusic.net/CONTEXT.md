---
title: "CoreMusic — media.coremusic.net Klasör Context"
type: docs
category: media
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# media.coremusic.net — CONTEXT.md

**docType:** context · **Klasör:** `media.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`media.coremusic.net/`, CoreMusic **medya arşivinin PHP CLI aracıdır**: `scan` (tarama + katalog), `audit` (arşiv denetimi), `ingest` (dry-run taşma). Bu doküman klasörün **gerçek envanterini** disk ölçümüyle tanımlar. Medya dosyası (mp3/flac/mp3/jpg) bu klasörde **yoktur** — sayı **0**'dır; klasör bir statik medya servisi değil, arşiv aracıdır.

| Karar | Kaynak (disk) |
|-------|---------------|
| Tek disk ekseni sanatçı → albüm → parça; tür/dönem/durum = TAG | `../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid` (accepted, v1.1.2) |
| Servis yerleşimi `media.coremusic.net` :5000/:6000, PHP + FFmpeg | `../.ai/index.md:162` + `../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture` |
| `media/` · `catalog/` · `reports/` · `vendor/` git'e girmez; `!src/Media/` negasyonu şart | `.gitignore` (11 satır) |
| CLI komutları tek yerde (`scan`/`audit`/`ingest`/`lint`) | `composer.json` (`scripts`, `bin`) |
| Kapalı taksonomi = 17 anahtar / 98 değer; serbest etiket yalnız `etiket[]` | `config/taxonomy.json` (`kurallar.mod: "kapali"`) |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `media.coremusic.net/` kök + `bin/`, `src/Media/`, `config/`, `docs/` envanteri | `media/` medya ağacı içeriği (git'e girmez, bu görevde **oluşturulmadı**) |
| `composer.json` / `.gitignore` yapılandırması | `.ai/.sql/mysql/media_catalog.sql` (DB şeması — `../.ai/` içindedir, orası SSOT) |
| Vault ↔ disk çelişki kaydı (§3.7) | FFmpeg/sunucu çalışma zamanı (ADR-039 PLANNED katman) |
| Dosya → sorumlu rol özeti (ayrıntı: [[AGENTS.md]]) | Faz 3 GUI uygulaması (`docs/faz3-gui-spec.md` ön spec) |

- **Kullananlar:** Backend Architect (CLI kodu), Data Engineer (`media_catalog`), QA Engineer (audit/denetim), MO (doküman), Security Engineer (hash/kopya kilidi denetimi).
- **Ön koşul:** Hedef dosya diskte mevcut; sayısal iddia ölçümle kanıtlanır, ölçülmeyen `UNKNOWN` yazılır.
- **Not:** Çekişmeli sayılarda **disk kazanır** (§3.7); bu dokümandaki her sayı 2026-10-03 ölçümüdür.

---

## 3. Mimari

### 3.1 Kök Envanter — Disk Kanıtı (2026-10-03 ölçümü)

**Ölçüm (bu 4 doküman yazılmadan ÖNCE):** 5 dizin (`bin`, `config`, `docs`, `src`, `src\Media`) · **18 dosya** · uzantı: `.php` 8 · `.json` 4 · `.md` 5 · `.gitignore` 1 · PHP satır toplamı **2356** (bin 1106 + src 1250).
**Bu doküman yazıldıktan SONRA:** **22 dosya** (`+CONTEXT` `+CLAUDE` `+AGENTS` `+WORKFLOW` — `.md` 9). §3.2 tablosu 18 "öncesi" dosyayı taşır; 4 klasör dokümanı §3.1 satırındadır.

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|--------------|--------------------|---------------|-----------|---------------------|
| `bin/` | 3 CLI komutu çalıştırılır (`scan`·`audit`·`ingest`) | 3 dosya: `scan.php` 261 satır, `audit.php` 499 satır, `ingest.php` 346 satır | ADR-092 Faz 2: arşivi okuyan/yazan tek kapıyı CLI olarak kurmak | Yeni komut/kural kimliği eklendiğinde (ADR §6.1/§7.1) |
| `src/Media/` | Arşiv kütüphanesi — `Media\` PSR-4 namespace | 5 sınıf: `CatalogWriter` 491 · `Validator` 431 · `Slugger` 140 · `Taxonomy` 104 · `Ulid` 84 satır | `bin/` içindeki işin tekrarını önleyip test edilebilir katman bırakmak | Kural (slug/ULID/sema) değiştiğinde |
| `config/` | Veri sözleşmesi — şema, taksonomi, kodlama düzeltmesi | 3 dosya: `media.schema.json` (v2), `taxonomy.json` (17/98), `mojibake-fix.json` (21 eşleşme) | Meta JSON'ın hangi alanları taşıyacağı tek yerde sabitlensin | Yeni alan/enum/kodlama eşlemesi geldiğinde |
| `docs/` | Karar ve denetim belgeleri | 5 md: `dizin-yapisi` 235 · `faz3-gui-spec` 537 · `adlandirma` 193 · `checklist` 63 · `todos` 38 satır | Dizin/adbilgisi koddan ayrı hızda değişir; denetim kanıtı yazılır | Karar/kapı durumu değiştiğinde (append/checkbox) |
| `composer.json` | Paket kimliği + komut + lint listesi | 46 satır: `coremusic/media`, `php ^8.4`, PSR-4 `Media\`, `bin` 3, `scripts` 4 | CLI'nin tek giriş ve tek lint kaydı olsun | Bağımlılık/komut eklendiğinde |
| `.gitignore` | Varlık ve türev dosyalarını repodan ayırır | 11 satır: `media/`, `catalog/`, `reports/`, `vendor/`, `*.log`, `!src/Media/`, `!src/Media/**` | 38 GB+ medya + türetilmiş indeks git'e girmez | Yeni türev dizin açıldığında |
| `media/`, `catalog/`, `reports/`, `vendor/`, `tests/`, `public/` | **Diskte YOK** (`Test-Path` = false, 2026-10-03) | — | İlk tarama/ingest ile kendiliğinden oluşacak iskeletler | Oluştuğunda envanter yeniden ölçülür |
| Medya dosyası (`.mp3 .flac .wav .m4a .ogg .wma .mp4 .mkv .avi .webm .jpg .png .webp`) | **0 dosya** | — | Arşiv henüz boş; taşma `ingest.php` ile yapılacak | İlk gerçek ingest'te |
| 4 klasör dokümanı (`CONTEXT`·`CLAUDE`·`AGENTS`·`WORKFLOW`) | Bilgi/kural/rol/süreç | Bu seri | Guardrail #16 iskeleti | Vault senkronu (MO) |

**eli10/eli15 blokları (§3.1'deki 9 madde):**

**`bin/`**
> **eli10 (basit):** Arşivi tarayan, denetleyen ve dosya taşıyan üç komutun durduğu klasör.
> **eli15 (detay):** Ayrı klasör çünkü çalıştırılan komutlar kod kütüphanesinden farklıdır; kullanıcı onlara dokunur. İçine `scan`, `audit`, `ingest` girdisi yazılır. Arşiv kuralı değişince (ör. hash kuralı) burası güncellenir. Olmazsa arşiv elle klasör gezilerek yönetilir ki 1M dosya ölçeğinde imkânsızdır.

**`src/Media/`**
> **eli10 (basit):** Ortak işlerin toplandığı PHP sınıf kutusu (slug, kimlik, doğrulama, katalog yazımı).
> **eli15 (detay):** Ayrıldı çünkü aynı kural üç komutta da çalışmalı; tek yerde olunca düzeltmenin etkisi üçüne birden yansır. İçine sınıf dosyaları yazılır, komut değil. Kural değişince (slug/ULID/sema) düzenlenir. Olmazsa her komut kuralı kendi içinde tekrar yazar ve kural kayar.

**`config/`**
> **eli10 (basit):** Hangi alanların geçerli olduğunu ve doğru yazımları söyleyen üç ayar dosyası.
> **eli15 (detay):** Ayrı klasör çünkü veri sözleşmesi koddan farklı değişir; kod içine gömülse her alan için kod revizyonu gerekir. İçine JSON dosyaları girer. Yeni alan/enum/tümleme eklenince değiştirilir. Olmazsa `audit` hangi değerin geçerli olduğunu bilemez.

**`docs/`**
> **eli10 (basit):** Klasörün kurallarını, yapılacakları ve denetim kanıtını anlatan yazılar.
> **eli15 (detay):** Ayrı klasör çünkü karar metni kodla aynı anda değişmez; kod içine yazılırsa revizyonda kaybolur. İçine karar (dizin/adlandırma), spec, checklist ve todos yazılır. Kapı durumu değişince güncellenir. Olmazsa "neden bu klasör böyle" sorusu cevapsız kalır.

**`composer.json`**
> **eli10 (basit):** Projenin kimlik kartı: hangi PHP sürümü, hangi komutlar, hangi dosyalar lintlenir.
> **eli15 (detay):** Ayrı dosyadır çünkü paket bilgisi okunur, kod değildir. İçine ad, `php ^8.4`, PSR-4, `bin` listesi ve `scripts` yazar. Bağımlılık ya da komut eklenince düzenlenir. Olmazsa tek komutluk denetim kapısı olmaz.

**`.gitignore`**
> **eli10 (basit):** Repoya girmemesi gereken büyük ve türev dosyaları listeleyen kural dosyası.
> **eli15 (detay):** Ayrı dosyadır çünkü git davranışı kodun dışında değişir. İçine `media/`, `catalog/`, `reports/`, `vendor/` ve `!src/Media/` negasyonu yazılır. Yeni türev dizin açılınca eklenir. Unutulursa 38 GB medya veya bozuk kod repoya girer/çıkış yapar.

**`media/` · `catalog/` · `reports/` · `vendor/` · `tests/` · `public/` (diskte YOK)**
> **eli10 (basit):** Henüz var olmayan, ilk çalışmayla oluşacak klasörler.
> **eli15 (detay):** Yok diye uydurulmaz; `Test-Path` ile ölçüldü ve false döndü. `media/` ilk `ingest`'te, `catalog/` ilk `scan`'de, `vendor/` `composer install`'da açılır. Varlıklarını görünce bu satır güncellenir. Önceden yazılsaydı envanter hayali olurdu.

**Medya dosyası (0 dosya)**
> **eli10 (basit):** Klasörde hiçbir şarkı, klip veya görsel dosyası yok.
> **eli15 (detay):** Sayım uzantı listesiyle yapıldı ve sonuç sıfır; arşiv iskelet olarak duruyor. Bu, eksiklik değil faz 2 durumudur. Gerçek veri `ingest.php --commit` ile taşınınca değişir. Yazılı olsaydı doğrulama kırılırdı.

**4 klasör dokümanı**
> **eli10 (basit):** Klasörün bilgisini, kuralını, rolünü ve iş akışını anlatan dört yazı.
> **eli15 (detay):** Ayrılar çünkü bilgi (envanter), kural (yasak), rol (kim ne yapar) ve süreç (adım/kapı) farklı hızda değişir. Okunmaları işe sırayla başlamayı sağlar. Yazmaları denetimi mümkün kılar. Hepsi tek dosyada toplansa hangi kuralın ne olduğu kaybolur.

### 3.2 Dosya Ölçüm Tablosu (önceden var 18 dosya — satır / bayt)

| Dosya | Satır | Bayt | Grup |
|-------|------:|-----:|------|
| `.gitignore` | 11 | 429 | kök |
| `composer.json` | 46 | 1370 | kök |
| `bin/scan.php` | 261 | 11499 | CLI |
| `bin/audit.php` | 499 | 22978 | CLI |
| `bin/ingest.php` | 346 | 13953 | CLI |
| `config/media.schema.json` | 679 | 27535 | config |
| `config/mojibake-fix.json` | 44 | 2147 | config |
| `config/taxonomy.json` | 27 | 1515 | config |
| `docs/adlandirma.md` | 193 | 8872 | docs |
| `docs/checklist.md` | 63 | 14147 | docs |
| `docs/dizin-yapisi.md` | 235 | 15684 | docs |
| `docs/faz3-gui-spec.md` | 537 | 43834 | docs |
| `docs/todos.md` | 38 | 6982 | docs |
| `src/Media/CatalogWriter.php` | 491 | 19053 | sınıf |
| `src/Media/Slugger.php` | 140 | 4533 | sınıf |
| `src/Media/Taxonomy.php` | 104 | 2770 | sınıf |
| `src/Media/Ulid.php` | 84 | 2458 | sınıf |
| `src/Media/Validator.php` | 431 | 15650 | sınıf |

**Grup eli10/eli15 blokları (ölçüm tablosu 4 grup):**

**Kök (`.gitignore` + `composer.json`)**
> **eli10 (basit):** Klasörün iki künye dosyası: neyi gizlediği ve nasıl çalıştırıldığı.
> **eli15 (detay):** Ayrı dururlar çünkü git ve paket kuralları kodun dışında değişir. İkisi de okunur ki hangi komutun çalıştığı ve neyin repoya girmediği belli olsun. Bağımlılık ya da yasak genişleyince düzenlenir. Yanlış yazılırsa kaynak kod repodan düşebilir.

**CLI (`bin/*.php`)**
> **eli10 (basit):** Üç komutun kaynak kodu: tara, denetle, taşı.
> **eli15 (detay):** Dosyalar ayrıdır çünkü her komutun tek bir işi ve çıkış kodu vardır (audit: 0/1/2). İçlerine okuma, kural uygulama ve raporlama yazar. Kural kimliği değişince güncellenir. Birleşik olsaydı tek hata üç komutu birden durdururdu.

**config (`*.json`)**
> **eli10 (basit):** Verinin hangi alanları taşıdığını söyleyen üç JSON.
> **eli15 (detay):** JSON ayrıdır çünkü veri sözleşmesi koddan bağımsız okunur; `audit` de `Validator` da onu tüketir. İçine alan, enum ve düzeltme eşlemesi yazılır. Yeni alan eklenince düzenlenir. İçine kod yazılırsa hem kırılır hem okunmaz.

**docs (`*.md`)**
> **eli10 (basit):** Kural, spec ve denetim kaydı tutan beş yazı.
> **eli15 (detay):** Her biri ayrıdır çünkü karar (dizin/adlandırma), iş listesi (todos) ve kanıt (checklist) farklı sahiptir. Okunmaları işe kuraldan başlamayı sağlar. Kapı kapanınca yalnız ilgili satır değişir. Tek dosyada toplansa denetim geçmişi silinir gider.

### 3.3 CLI Araçları (3 komut — disk kanıtı)

| Araç | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|------|--------------------|---------------|-----------|---------------------|
| `bin/scan.php` | `media/` → `catalog/catalog.jsonl` (+ MySQL varsa) | 261 satır: bayraklar `--rebuild` `--dry-run` `--deep` `-v` (`:60-76`), tek gerçek `hash_file` `:155` (deep dalı) | Türetilmiş indeks yeniden üretilebilir olsun (ADR-092 §5.2) | İndeks alanı/DB yazımı değişince |
| `bin/audit.php` | Arşiv lint'i; salt okunur | 499 satır; çıkış 0 = hata yok · 1 = HATA · 2 = kullanım (`:11`); **22 kural kimliği** (`:17-23`); deep hash `:398` | Kabul kriterleri (ADR §7.1) otomatik denetlensin | Yeni kural kimliği eklendiğinde |
| `bin/ingest.php` | Kaynak → arşiv taşma; `reports/ingest-*.csv` | 346 satır: dry-run kilidi, `copy(` tek gerçek çağrı `:328`, STDIN `evet` onayı, giriş hash `:197` + kopya doğrulama `:336` | Taşma tek bayt bile olsa onaysız olmasın (ADR §8 madde 11) | Taşma kuralı/format değişince |

**eli10/eli15 blokları (§3.3):**

**`scan.php`**
> **eli10 (basit):** Kütüphaneyi gezip içindekilere dair bir liste (katalog) çıkaran komut.
> **eli15 (detay):** Ayrı komuttur çünkü tarama okuma ağırlıklıdır ve sık koşulur. İçine bayrak okuma, JSON doğrulama ve katalog yazma adımları girer. İndeks alanı değişince güncellenir. Çalışmazsa arşiv hiç kataloglanamaz ve arama çalışmaz.

**`audit.php`**
> **eli10 (basit):** Arşivde kurallara uymayan yeri gösteren denetçi komut.
> **eli15 (detay):** Ayrıdır çünkü denetim bağımsız olmalı; taşıma yapan `ingest` ile aynı dosyada olsaydı denetim kendi hatasını göremezdi. İçine kural kimlikleri ve çıktı satırı formatı yazılır. Yeni kural eklenince değiştirilir. Olmazsa bozuk yol/bozuk kimlik sessizce arşive girer.

**`ingest.php`**
> **eli10 (basit):** Dışarıdan dosyaları arşive, önce prova (dry-run) sonra onayla kopyalayan komut.
> **eli15 (detay):** Ayrıdır çünkü tek yazan komuttur; yazma yetkisi tek elde toplanır. İçine adlandırma, kopya ve rapor adımları girer. Kaynak format değişince güncellenir. Onay kapısı kaldırılırsa yanlış dosya arşive girer ve master dokunulmazlık zedelenir.

### 3.4 Çekirdek Sınıflar (5 — `src/Media/`)

| Sınıf | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-------|--------------------|---------------|-----------|---------------------|
| `CatalogWriter.php` | `catalog.jsonl` yazar (türetilmiş indeks) | 491 satır; dosya başı: SSOT yan JSON'dur, indeks yeniden üretilir | DB/JSON çökse arşiv yaşasın | Katalog alanı değişince |
| `Validator.php` | `media.schema.json` için bağımlılıksız doğrulayıcı | 431 satır; 6 kural: required · pattern · enum+tax · type · aralık · additionalProperties=false | Paket çekilmeden şema denetlensin | Şema kuralı eklendiğinde |
| `Slugger.php` | kebab-case slug üretimi | 140 satır; fold → lowercase → `[a-z0-9-]` → max 80 → boşsa `YEDEK = 'isimsiz'` (`:19-20`) | Adlandırma kuralı (docs/adlandirma 1-3) tek kodda dursun | Adlandırma kuralı değişince |
| `Taxonomy.php` | Kapalı taksonomi okuyucusu | 104 satır; `values()` / `isValid()` yalnız `config/taxonomy.json`'dan döner | DB/JSON drift'inin tek referansı olsun | `taxonomy.json` anahtar eklendiğinde |
| `Ulid.php` | ULID üretimi + doğrulama | 84 satır; Crockford base32, 26 karakter, ilk 10 = 48-bit ms | Kalıcı kimlik slug'dan bağımsız olsun (ADR §2.1 m.3) | Kimlik kuralı değişirse (ADR revizyonu) |

**eli10/eli15 blokları (§3.4):**

**`CatalogWriter.php`**
> **eli10 (basit):** Tarama sonuçlarını tek satır tek satır liste dosyasına yazan sınıf.
> **eli15 (detay):** Ayrı sınıftır çünkü yazma biçimi (JSONL) tek yerde sabitlenmelidir; parçalı olsaydı iki sürüm katalog oluşurdu. İçine satır üretimi ve dosya yönetimi yazılır. Katalog alanı değişince düzenlenir. Silinirse arama indeksi üretilemez.

**`Validator.php`**
> **eli10 (basit):** Oluşturulan bilgi dosyasının kurala uyup uymadığını kontrol eden sınıf.
> **eli15 (detay):** Ayrıldı çünkü dış pakete bağımlı olmadan çalışmalı; bağımlılık olsaydı `vendor/` kurulmadan denetim yapılamazdı. İçine altı kural tipi yazılır. Şema genişleyince eklenir. Olmazsa hatalı veri arşive sızar ve sonradan temizlenmesi zorlaşır.

**`Slugger.php`**
> **eli10 (basit):** Türkçe adları klasör adı olabilecek kısa ASCII şekle çeviren sınıf.
> **eli15 (detay):** Ayrı sınıftır çünkü adlandırma hem ingest hem audit hem scan'de aynı olmalıdır. İçine fold tablosu, filtre, uzunluk sınırı ve boş slug yedeği yazılır. Adlandırma kuralı değişince güncellenir. Tekrarlanırsa klasör adları tutarsızlaşır.

**`Taxonomy.php`**
> **eli10 (basit):** Hangi etiket değerlerinin geçerli olduğunu söyleyen okuyucu.
> **eli15 (detay):** Ayrıdır çünkü kapalı liste tek dosyadan okunmalı, koda gömülmemelidir. İçine `values()` ve `isValid()` yazar. `taxonomy.json` değişince o değişir. Koda gömülsüyordu iki ayrı liste oluşur ve drift doğardı.

**`Ulid.php`**
> **eli10 (basit):** Her kayda değişmez 26 karakterlik kimlik üreten sınıf.
> **eli15 (detay):** Kimlik ayrıdır çünkü slug değişince dosya taşınmamalı, indeks kırılmamalıdır. İçine alfabe, uzunluk ve doğrulama yazılır. ADR kimlik kuralını değiştirirse güncellenir. Unutulursa yeniden adlandırma indeksi bozar.

### 3.5 Config (3 dosya)

| Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-------|--------------------|---------------|-----------|---------------------|
| `config/media.schema.json` | Meta JSON şeması (v2) | 679 satır / 27535 bayt · `$defs` 34 · `oneOf` 4 varlık: `artist`·`album`·`meta`·`koleksiyon`; ULID/slug/sha256/datetime (+03:00) kalıpları | `audit` hangi alanın zorunlu olduğunu şemadan okur | Yeni varlık/alan geldiğinde (`version` artırılır) |
| `config/taxonomy.json` | Kapalı taksonomi + yaşam döngüsü | 27 satır / 1515 bayt · **17 anahtar / 98 değer** · `durum` = inbox→aktif→sakli→tekrar→arsiv | Enum şemasıyla senkron tek kaynak (mod: kapalı) | Yeni değer = 1 satır + audit |
| `config/mojibake-fix.json` | Bozuk → doğru ad eşlemesi | 44 satır / 2147 bayt · `toplam_eslesme: 21` · yön `kaynak -> duzeltilmis` · uygulama: ingest normalize | Slug'dan önce normalize edilmezse bozuk slug üretilir (ADR §4.2) | Yeni bozuk kalıp görüldüğünde |

**eli10/eli15 blokları (§3.5):**

**`media.schema.json`**
> **eli10 (basit):** Bilgi dosyalarında hangi alanın bulunması gerektiğini söyleyen kural defteri.
> **eli15 (detay):** Ayrı dosyadır çünkü veri sözleşmesi kod olmadan okunabilir olmalıdır; `audit` onu harici paket çekmeden uygular. İçine alan, tip ve desen tanımları yazılır. Yeni alan gelince `version` artırılarak düzenlenir. Kaybolursa denetim kör kalır.

**`taxonomy.json`**
> **eli10 (basit):** Kullanılabilecek etiketlerin ve durumların kapalı listesi.
> **eli15 (detay):** Ayrıdır çünkü enum listesi hem JSON şemasında hem MySQL'de aynı olmalıdır; tek kaynak olmasa drift doğar. İçine 17 anahtar ve 98 değer yazılır. Yeni değer eklenince tek satır eklenir. Serbest `etiket[]` dışında liste burada tutulur.

**`mojibake-fix.json`**
> **eli10 (basit):** Bozuk yazılmış Türkçe adları doğrusına çeviren eşleme listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü düzeltme veriye özeldir, koda gömülmez. İçine bozuk → doğru eşlemeleri ve uygulama sırası yazılır. Yeni bozuk kalıp görünce eklenir. Uygulanmazsa `M\u00C3\u00BCzik` gibi adlar yanlış slug üretir.

### 3.6 Docs (5 dosya)

| Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-------|--------------------|---------------|-----------|---------------------|
| `docs/dizin-yapisi.md` | Fiziksel disk yerleşimi + ölçek kuralları | 235 satır: tek eksen, ağac, format, master, yol hesabı (53+13 taban, 194 pay) | Yerleşim ADR-092'den okunur tek yerde dursun | Yerleşim/ölçek eşiği değişince |
| `docs/adlandirma.md` | 6 adlandırma kuralı | 193 satır: ASCII fold · ön ekler · çakışma · ULID · kodlama · mojibake | `Slugger` kuralının insan-okur kaynağı olsun | Kural değişince (Slugger ile birlikte) |
| `docs/checklist.md` | Kabul denetim listesi (kanıt + `⚠️ VR` işaretli açık maddeler) | 63 satır; kanıt türü: `dosya:satır` · `grep` · `commit` · `kayıt` | Kanıtsız `- [x]` yok kuralı | Kapı koşulu değişince |
| `docs/todos.md` | Sıralı iş listesi (P0→P1→P2) | 38 satır; sütun: öncelik · görev · sahip · tahmin · bağımlılık · kapı koşulu | P0 kapanmadan P2 başlamaz | Görev kapanınca `- [x]` |
| `docs/faz3-gui-spec.md` | Faz 3 web arayüzü ön spec'i | 537 satır; §3 8 ekran + 8 ASCII wireframe + §5.4 BEM eşlemesi | `.ai/ui-design/` Kalıp D üretimi öncesi hammadde | Ekran/karar değişince (ön spec statüsü) |

**eli10/eli15 blokları (§3.6):**

**`dizin-yapisi.md`**
> **eli10 (basit):** Medyanın hangi klasörde duracağını ve neden tek sırayla olduğunu anlatan yazı.
> **eli15 (detay):** Ayrı dosyadır çünkü yerleşim kararı kodun dışında alınır ve ADR'den okunur. İçine dizin ağacı, format ve ölçek kuralları yazılır. Yerleşim değişince güncellenir. Okunmadan klasör açılırsa eksen kırılır, duplike klasör doğar.

**`adlandirma.md`**
> **eli10 (basit):** Dosya ve klasör adlarının nasıl yazılacağını anlatan altı kural.
> **eli15 (detay):** Ayrıdır çünkü hem insan hem kod bunu okur; `Slugger` bu belgeyi işaret eder. İçine fold, önek, çakışma, kimlik, kodlama ve mojibake anlatılır. Kural değişince kodla birlikte güncellenir. Yoksa adlar tutarsızlaşır ve arama çalışamaz.

**`checklist.md`**
> **eli10 (basit):** Ne kanıtlandığını, neyin hâlâ açık olduğunu gösteren onay listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü kanıt ile iş listesi farklıdır: biri "oldu mu", diğeri "ne yapılacak". İçine kanıt türü etiketi ve `⚠️ VR` satırları yazılır. Kapı kapanınca satır işaretlenir. Tek dosyada toplansa kanıt kaybolur.

**`todos.md`**
> **eli10 (basit):** Sırayla yapılacak işler ve kimin neyi ne zaman yapacağı.
> **eli15 (detay):** Ayrıdır çünkü plan, kanıttan farklı hızda değişir. İçine öncelik, sahip, tahmin ve kapı koşulu yazılır. İş bitince kapanır. Checklist ile beraber tutulur ki iş ile kanıt eşleşsin.

**`faz3-gui-spec.md`**
> **eli10 (basit):** Web ekranlarının şimdiden çizilmiş, henüz kodlanmamış taslağı.
> **eli15 (detay):** Ayrı dosyadır çünkü spec, uygulamadan önce yazılır ve `.ai/ui-design/` Kalıp D'ye dönüşecektir. İçine ekran listesi ve çizimler (ASCII) girer. Ekran kararı değişince güncellenir. Koda gömülse tasarım geçmişi kaybolur.

### 3.7 Vault ↔ Disk Çelişkileri (disk kazanır — uydurulmaz)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `docs/dizin-yapisi.md` §8: "62 dizin / 0 dosya" (2026-09-29) | **5 dizin** (`bin`,`config`,`docs`,`src`,`src\Media`); `media/` ağacı diskte **YOK** | ⚠️ VERIFICATION REQUIRED |
| `docs/dizin-yapisi.md` §9 + `bin/audit.php:25`: "PHP 8.4 bu makinede YOK → `php -l` yapılamadı" | **PHP 8.5.8** (`C:\Php858\php.exe`) kurulu; `php -l` **8/8 "No syntax errors"** geçti | ⚠️ VERIFICATION REQUIRED |
| `docs/dizin-yapisi.md` §9: "composer bu makinede YOK" | `composer` PATH'te var (`C:\composer\composer.bat`); **`vendor/` yine YOK** (kurulum çalıştırılmadı) | ⚠️ VERIFICATION REQUIRED |
| `docs/checklist.md` Faz 2: "toplam 2.060 satır" | PHP satır toplamı **2356** (bin 1106 + src 1250) | ⚠️ VERIFICATION REQUIRED |
| `docs/checklist.md` VR-2: "PHP ortamı yok" | Yarı kapandı: `php -l` 8/8 geçti · **`php bin/audit.php` / `scan --dry-run` bu görevde KOŞULMADI** | ⚠️ VERIFICATION REQUIRED |
| `docs/checklist.md` VR-3: "MySQL yok, DDL çalıştırılamadı" | Bu görevde de **çalıştırılmadı**; `media_catalog.sql` diskte 9 `CREATE TABLE` + `v_asset_search` | ⚠️ VERIFICATION REQUIRED |
| `docs/adlandirma.md` §9 + `dizin-yapisi.md` §8: schema 26.856 B · taxonomy 1.488 B · mojibake 2.103 B | **27535** · **1515** · **2147** bayt | ⚠️ VERIFICATION REQUIRED |
| `../.ai/glossary.md:245`: "`media.coremusic.net` dizini mevcut değil (Faz 0)" | Dizin **VAR** (önceden 18 + bu 4 doküman = **22 dosya** / 5 dizin) | ⚠️ VERIFICATION REQUIRED |
| `composer.json` description + `.gitignore` yorumları: mojibake (`CLI ?`, `i??eri?i`) | Dosyalar bozuk bayt içeriyor — **düzeltilmedi** (yalnız rapor) | ⚠️ VERIFICATION REQUIRED |

**eli10/eli15 blokları (§3.7 çelişki listesi):**

> **eli10 (basit):** Bazı eski yazılar başka bir bilgisayardaki durumu anlatıyor; bugünün bilgisayarı ölçülünce farklı çıktı.
> **eli15 (detay):** Çelişki burada uydurmak yerine iki tarafı da yazıp "disk kazanır" demek içindir. Çünkü eski kayıt silinirse neden yanlış olduğu anlaşılıp tekrarlanmaz. Okunması, hangi sayının güvenilir olduğunu gösterir. Yazılması, sonraki denetçinin aynı hataya düşmesini engeller. Düzeltme bu dokümanın işi değil, ilgili dosya sahibinin işidir.

### 3.8 Klasör Dışı İlişkiler

| Sistem dosyası | İlişki | Kanıt |
|----------------|--------|-------|
| `../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid` | Disk ekseni + ULID + §6.1 kuralları (accepted, v1.1.2, 2026-09-29) | dosya frontmatter'i okundu |
| `../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture` | Servis yerleşimi :5000/:6000 (accepted, v1.0.1) | dosya frontmatter'i okundu |
| `../.ai/.sql/mysql/media_catalog.sql` | DB `media_catalog`: 9 tablo + `v_asset_search` view | `CREATE TABLE` sayımı = 9 |
| `../.ai/index.md:162` / `:194` | `media.coremusic.net` 5000/6000 · PHP + FFmpeg | satır okundu |
| `../.ai/CLAUDE.md:282` / `:315` | Servis listesi + port tablosu medya satırı | satır okundu |
| `../AGENTS.md` §5 / §6 | Dosya tipi → rol; PHP → Backend Architect | kök registry |
| `reports/ingest-*.csv` | `ingest.php` rapor çıktısı (klasör diskte YOK) | `.gitignore:5` |

**eli10/eli15 blokları (§3.8):**

> **eli10 (basit):** Bu klasörün işi bitmiyor; kararı vault'ta, veritabanı şeması da orada duruyor.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü dış dosyalar bu klasörün içinde değil. Hangi ADR'nin ne verdiği okununca belli olur. Şema değişince (veya ADR revize edilince) bu satır güncellenir. Tablo olmasa klasör kendi başına doğru sanılır ve yanlış üretilir.

### 3.9 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur (her şey tek dosyada olsaydı) |
|---|---------|----------|----------------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Sözleşme tek kaynak** | Şema/taksonomi `config/`'te tek yerde durur | Kodda ayrı, DB'de ayrı liste → drift ve tutarsız kayıt |
| 4 | **Türev izolasyonu** | `media/` ve `catalog/` repodan ayrı kalır | 38 GB medya ve üretilen indeks git'e girer, repo şişer |
| 5 | **Araç karantinası** | CLI (`bin/`) ile kütüphane (`src/Media/`) ayrı durur | Komut ile kütüphane birbirine karışıp yeniden yazılamaz hâle gelir |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: veri sözleşmesi `config/`, araç `bin/`, kütüphane `src/`, karar `docs/` durur. Böylece bir yer değiştirince sadece o değişir. Sözleşme kodun içine girse her alan için kod revizyonu gerekir, medya repoya girse repo taşınamaz hâle gelir. Hepsi tek dosyada toplansa bakım ve denetim kaybolurdu.

---

## 4. Kurallar

Guardrail numaralarının kaynağı: [[../.ai/CLAUDE]] (16 Hard Guardrail) + ADR-092 §6.1.

| # | Kural (özet) | Ref |
|---|--------------|-----|
| 1 | `media/` içeriği asla git'e girmez; `!src/Media/` negasyonu korunur | `.gitignore` + ADR-092 |
| 2 | Tek disk ekseni: sanatçı → albüm → parça; tür/dönem/durum = TAG, klasör adı DEĞİL | ADR-092 §1.1 (bkz. [[CLAUDE]]) |
| 3 | Master (`.flac`/`.wav`) dokunulmaz · varyant düz durur · `derived\` klasörü YOK | ADR-092 §2.1 m.5 (bkz. [[CLAUDE]]) |
| 4 | sha256 **yalnız girişte**; tarama/denetim hafif modda yeniden hash yapmaz | ADR-092 §6.1 m.5 · `scan.php:155` yalnız `--deep` |
| 5 | `ingest` `--commit` yokken tek bayt kopyalamaz; onay = STDIN `evet` | `ingest.php:328` + ADR §8 m.11 |
| 6 | Yasaklı çağrı yasak: `eval`·`exec`·`shell_exec`·`passthru`·`system`·`proc_open` → ölçülen **0** | checklist Faz 2 (bu görevde grep = 0) |
| 7 | Sayı/kanıt yoksa `⚠️ VERIFICATION REQUIRED` — uydurulmaz | Zero-Hallucination · [[CLAUDE]] |
| 8 | Bu dokümandaki her sayı ölçümdür; yeni ölçümde eski sayı geçersizdir | Şablon §4.1 #2-#3 |

- **Çelişki kuralı:** vault ↔ disk → **disk kazanır** + `⚠️ VERIFICATION REQUIRED` (§3.7).
- **Emoji yasak**; yalnız `⚠️ VERIFICATION REQUIRED` işareti ve `[[...]]` kullanılır.
- **REDACTED:** secret/anahtar hiçbir `.md`'ye yazılmaz.

---

## 5. Workflow

```text
ENVANTER ÖLÇ (dosya + satır + bayt) → KOD/CONFIG OKU (1. kez, karar ver)
→ ŞABLON İSKELETİ (§1-§7) → ÇELİŞKİ KAYDI (§3.7) → DOĞRULA (§6)
→ RAPOR → COMMIT (subagent ATMAZ)
```

Adım detayı ve kapılar: [[WORKFLOW]] §3.1 / §3.2.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm | §1–§7 numaralı, aynı sıra, ≤3 başlık seviyesi |
| 3 | Envanter | 18 (öncesi) + 4 (bu seri) = 22 dosya · 5 dizin · 2356 PHP satırı ölçümle eşit |
| 4 | Çelişki | §3.7 9 satır; her biri `⚠️ VERIFICATION REQUIRED` işaretli |
| 5 | Placeholder | Dosyada `{{` kalmadı |
| 6 | Wiki-link | `[[...]]` formatı; hedefler diskte var |
| 7 | Halüsinasyon | Diskte olmayan dosya/sayı/varlık iddia edilmedi (`media/` = YOK olarak yazıldı) |
| 8 | Uzunluk | 300–500 satır |
| 9 | eli10 + eli15 | §3.1-§3.9 her maddesinde `> **eli10 (basit):**` + `> **eli15 (detay):**` bloğu var mı (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 10 | İçerik neden ayrı | §3.9 tablosu 5 satır (bakım · tek sorumluluk · sözleşme tek kaynak · türev izolasyonu · araç karantinası) |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Klasör kuralları | [[CLAUDE]] | Yasak + guardrail özeti |
| Klasör rolleri | [[AGENTS]] | Dosya → rol routing |
| Klasör süreç | [[WORKFLOW]] | Adım + kapı |
| Kök master kurallar | [[../AGENTS]] | §4 keşif · §5 anti-overthink · §7 loop |
| Vault anayasası | [[../.ai/CLAUDE]] | 16 Hard Guardrail |
| Şablon kaynağı | [[../.ai/.templates/frontend/context-template]] | İskelet (Guardrail #16) |
| Dizin kararı | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Disk ekseni + ULID |
| Servis kararı | [[../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture]] | :5000/:6000 yerleşim |
| DB şeması | `../.ai/.sql/mysql/media_catalog.sql` | 9 tablo + `v_asset_search` |
| Dizin belgesi | `docs/dizin-yapisi.md` | Ağaç + ölçek kuralları |
| Adlandırma belgesi | `docs/adlandirma.md` | 6 kural |
| Denetim + iş listesi | `docs/checklist.md` · `docs/todos.md` | Kapı kanıtı + P0/P1/P2 |
| Disk kanıtı | `media.coremusic.net/` | Bu dokümandaki tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** context
