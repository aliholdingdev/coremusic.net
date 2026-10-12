---
title: "CoreMusic — media.coremusic.net Klasör Context"
type: docs
category: media
date: 2026-10-12
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# media.coremusic.net — CONTEXT.md

**docType:** context · **Klasör:** `media.coremusic.net/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`media.coremusic.net/`, CoreMusic **medya arşivinin PHP CLI aracıdır**: `scan` (tarama + katalog), `audit` (arşiv denetimi), `ingest` (taşıma — dry-run varsayılan). Bu doküman klasörün gerçek envanterini tanımlar; **her sayı bu oturumda (2026-10-12) diskten ölçülmüştür**, ölçülemeyen bilgi `UNKNOWN` bırakılmıştır. Medya dosyası (mp3/flac/jpg vb.) bu klasörde **yoktur** — sayı **0**; klasör statik medya servisi değil, arşiv aracıdır.

| Karar | Kaynak (disk) |
|-------|---------------|
| Tek disk ekseni sanatçı → albüm → parça; tür/dönem/durum = TAG | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid.md]] (accepted, v1.1.2, 2026-09-29) |
| Servis yerleşimi `media.coremusic.net` :5000/:6000, PHP + FFmpeg | [[../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture.md]] §2.1 (satır 120) + `../.ai/index.md:154` |
| `media/` · `catalog/` · `reports/` · `vendor/` git'e girmez; `!src/Media/` negasyonu şart | `.gitignore` (11 satır, bu oturum okundu) |
| CLI komutları tek yerde (`scan`/`audit`/`ingest`/`lint`) | `composer.json` (`scripts` 4 · `bin` 3) |
| Kapalı taksonomi = 17 anahtar / 98 değer; serbest etiket yalnız `etiket[]` | `config/taxonomy.json` (`kurallar.mod` = kapalı) |

Not: K-space katman mimarisi atıfları (K000–K020, A0–A5, `.ai/architecture/`) bu sürümde bilinçli olarak bulundurulmaz — kaldırıldı, yerine yenisi üretilmedi.

> **eli10 (basit):** Bu klasör şarkı saklamaz; şarkıları tarayan, denetleyen ve taşıyan üç PHP komutu saklar.
> **eli15 (detay):** Klasörün işi ADR-092'den gelir: arşiv işlemi tek CLI kapısından yürür. Tablodaki her karar bir disk dosyasından okunmuştur, model hafızasından değil. Medya sayısının 0 olması eksiklik değil, faz durumudur — veri `ingest` ile taşınınca değişir. Bu dosya her yeniden yazımında tüm sayılar tekrar ölçülür.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `media.coremusic.net/` kök + `bin/`, `src/Media/`, `config/`, `docs/` envanteri (22 dosya · 5 dizin) | `media/` medya ağacı içeriği (git'e girmez; bu oturumda dizin diskte YOK) |
| `composer.json` / `.gitignore` yapılandırması | `.ai/.sql/mysql/media_catalog.sql` (DB şeması — `../.ai/` içindedir, orası SSOT) |
| Vault ↔ disk çelişki kaydı (§3.7) | FFmpeg/sunucu çalışma zamanı (ADR-039 PLANNED katman) |
| Dosya → sorumlu rol özeti (ayrıntı: [[AGENTS.md]]) | Faz 3 GUI uygulaması (`docs/faz3-gui-spec.md` ön spec) |

- **Kullananlar:** Backend Architect (CLI kodu), Data Engineer (`media_catalog`), QA Engineer (audit/denetim), MO (doküman), Security Engineer (hash/kopya kilidi denetimi).
- **Ön koşul:** Hedef dosya diskte mevcut; sayısal iddia ölçümle kanıtlanır, ölçülmeyen `UNKNOWN` yazılır.
- **Not:** Çekişmeli sayılarda **disk kazanır** (§3.7); bu dokümandaki her sayı 2026-10-12 ölçümüdür.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları, dış ilişkileri ve çelişki kaydı anlatılır; medya içeriği ve sunucu işi konu dışıdır.
> **eli15 (detay):** Sınır klasörün fiziksel kenarıyla çizilir: 22 dosyanın tamamı içeri, medya ağacı dışarıdadır. DB şeması da dışarıdadır çünkü `.ai/.sql/` kendi SSOT'udur. Kapsam dışı satırları okunmazsa bu dosya kendi yetkisini aşar. Ön koşul, her iddianın ölçülebilir olmasıdır.

---

## 3. Mimari

### 3.1 Kök Envanter — 5 Sütunlu Disk Kanıtı (2026-10-12 ölçümü)

**Ölçüm:** 5 dizin (`bin`, `config`, `docs`, `src`, `src\Media`) · **22 dosya** · 27 girdi · uzantı: `.php` 8 · `.md` 9 · `.json` 4 · `.gitignore` 1 · PHP satır toplamı **2356** (bin 1106 + src 1250).

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `bin/` | 3 CLI komutu çalıştırılır (`scan` · `audit` · `ingest`) | 3 dosya · 1106 satır: `scan.php` 261, `audit.php` 499, `ingest.php` 346 | ADR-092 Faz 2: arşivi okuyan/yazan tek kapıyı CLI olarak kurmak | Yeni komut/kural kimliği eklendiğinde (ADR-092 §6.1/§7.1) |
| `src/Media/` | Arşiv kütüphanesi — `Media\` PSR-4 namespace | 5 sınıf · 1250 satır: `CatalogWriter` 491 · `Validator` 431 · `Slugger` 140 · `Taxonomy` 104 · `Ulid` 84 | Aynı kural üç komutta tek yerde dursun; tekrar ve kayma önlenir | Kural (slug/ULID/sema) değiştiğinde |
| `config/` | Veri sözleşmesi — şema, taksonomi, kodlama düzeltmesi | 3 dosya: `media.schema.json` 679 satır, `taxonomy.json` 27, `mojibake-fix.json` 44 | Meta JSON'ın hangi alanları taşıdığı tek yerde sabitlensin | Yeni alan/enum/kodlama eşlemesi geldiğinde |
| `docs/` | Karar, spec ve denetim belgeleri | 5 md · 1066 satır: `faz3-gui-spec` 537 · `dizin-yapisi` 235 · `adlandirma` 193 · `checklist` 63 · `todos` 38 | Karar metni koddan ayrı hızda değişir; denetim kanıtı yazılır | Kapı/karar durumu değiştiğinde (append/checkbox) |
| `composer.json` | Paket kimliği + komut + lint listesi | 46 satır: `php ^8.4`, PSR-4 `Media\`, `bin` 3, `scripts` 4 | CLI'nin tek giriş ve tek lint kaydı olsun | Bağımlılık/komut eklendiğinde |
| `.gitignore` | Varlık ve türev dosyalarını repodan ayırır | 11 satır: `media/`, `catalog/`, `reports/`, `vendor/`, `*.log`, `!src/Media/`, `!src/Media/**` | Varlık baytı + türetilmiş indeks git'e girmez | Yeni türev dizin açıldığında |
| `CONTEXT` · `CLAUDE` · `AGENTS` · `WORKFLOW.md` | Bilgi / kural / rol / süreç | 4 doküman: `CLAUDE` 302 · `AGENTS` 305 · `WORKFLOW` 301 satır + `CONTEXT` (bu dosya) | Guardrail #16 iskeleti; okuma sırası §3.8'de | Vault senkronu (MO) |
| `media/`, `catalog/`, `reports/`, `vendor/`, `tests/`, `public/` | **Diskte YOK** (bu oturumda 6'sı ayrı ayrı ölçüldü, hepsi false) | — | İlk tarama/ingest ile kendiliğinden oluşacak iskeletler | Oluştuğunda envanter yeniden ölçülür |
| Medya dosyası (`.mp3 .flac .wav .m4a .ogg .mp4 .jpg .png` vb.) | **0 dosya** (22 dosyanın hiçbiri medya uzantısı değil) | — | Arşiv henüz boş; taşıma `ingest.php` ile yapılacak | İlk gerçek ingest'te |

**eli10/eli15 blokları (§3.1 — 9 madde):**

**`bin/`**
> **eli10 (basit):** Arşivi tarayan, denetleyen ve dosya taşıyan üç komutun durduğu klasör.
> **eli15 (detay):** Ayrı klasör çünkü çalıştırılan komutlar kod kütüphanesinden farklıdır; kullanıcı onlara dokunur. İçine `scan`, `audit`, `ingest` girdisi yazılır. Arşiv kuralı değişince (ör. hash kuralı) burası güncellenir. Olmazsa arşiv elle klasör gezilerek yönetilir ki büyük koleksiyon ölçeğinde imkânsızdır.

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
> **eli15 (detay):** Ayrı dosyadır çünkü git davranışı kodun dışında değişir. İçine `media/`, `catalog/`, `reports/`, `vendor/` ve `!src/Media/` negasyonu yazılır. Yeni türev dizin açılınca eklenir. Unutulursa medya veya bozuk kod repoya girer/çıkış yapar.

**4 klasör dokümanı (`CONTEXT` · `CLAUDE` · `AGENTS` · `WORKFLOW.md`)**
> **eli10 (basit):** Klasörün bilgisini, kuralını, rolünü ve iş akışını anlatan dört yazı.
> **eli15 (detay):** Ayrılar çünkü bilgi (envanter), kural (yasak), rol (kim ne yapar) ve süreç (adım/kapı) farklı hızda değişir. Sıraları §3.8'de sabittir. Yazmaları denetimi mümkün kılar. Hepsi tek dosyada toplansa hangi kuralın ne olduğu kaybolur.

**`media/` · `catalog/` · `reports/` · `vendor/` · `tests/` · `public/` (diskte YOK)**
> **eli10 (basit):** Henüz var olmayan, ilk çalışmayla oluşacak klasörler.
> **eli15 (detay):** Yok diye uydurulmaz; bu oturumda altısı ayrı ayrı ölçüldü ve hepsi false döndü. `media/` ilk `ingest`'te, `catalog/` ilk `scan`'de, `vendor/` `composer install`'da açılır. Varlıklarını görünce bu satır güncellenir. Önceden yazılsaydı envanter hayali olurdu.

**Medya dosyası (0 dosya)**
> **eli10 (basit):** Klasörde hiçbir şarkı, klip veya görsel dosyası yok.
> **eli15 (detay):** Sayım 22 dosyalık envantrin uzantılarıyla yapıldı ve sonuç sıfır; arşiv iskelet olarak duruyor. Bu, eksiklik değil faz 2 durumudur. Gerçek veri `ingest` ile taşınınca değişir. Yazılı olsaydı doğrulama kırılırdı.

### 3.2 Dosya Ölçüm Tablosu (18 dosya — satır / bayt; 4 klasör dokümanı §3.1'de)

**Toplam: 18 + 4 = 22 dosya · 5 dizin · PHP 2356 satır (bin 1106 + src 1250).**

| Dosya | Satır | Bayt |
|-------|------:|-----:|
| `.gitignore` | 11 | 429 |
| `composer.json` | 46 | 1370 |
| `bin/scan.php` | 261 | 11499 |
| `bin/audit.php` | 499 | 22978 |
| `bin/ingest.php` | 346 | 13953 |
| `config/media.schema.json` | 679 | 27535 |
| `config/mojibake-fix.json` | 44 | 2147 |
| `config/taxonomy.json` | 27 | 1515 |
| `docs/adlandirma.md` | 193 | 8872 |
| `docs/checklist.md` | 63 | 14147 |
| `docs/dizin-yapisi.md` | 235 | 15684 |
| `docs/faz3-gui-spec.md` | 537 | 43834 |
| `docs/todos.md` | 38 | 6982 |
| `src/Media/CatalogWriter.php` | 491 | 19053 |
| `src/Media/Slugger.php` | 140 | 4533 |
| `src/Media/Taxonomy.php` | 104 | 2770 |
| `src/Media/Ulid.php` | 84 | 2458 |
| `src/Media/Validator.php` | 431 | 15650 |

**eli10/eli15 blokları (§3.2 — 5 grup):**

**Kök (`.gitignore` + `composer.json`)**
> **eli10 (basit):** Klasörün iki künye dosyası: neyi gizlediği ve nasıl çalıştırıldığı.
> **eli15 (detay):** Ayrı dururlar çünkü git ve paket kuralları kodun dışında değişir. İkisi de okunur ki hangi komutun çalıştığı ve neyin repoya girmediği belli olsun. Bağımlılık ya da yasak genişleyince düzenlenir. Yanlış yazılırsa kaynak kod repodan düşebilir.

**CLI (`bin/*.php`)**
> **eli10 (basit):** Üç komutun kaynak kodu: tara, denetle, taşı.
> **eli15 (detay):** Dosyalar ayrıdır çünkü her komutun tek bir işi ve kendi çıkış kodu vardır (0/1/2). İçlerine okuma, kural uygulama ve raporlama yazılır. Kural kimliği değişince güncellenir. Birleşik olsaydı tek hata üç komutu birden durdururdu.

**config (`*.json`)**
> **eli10 (basit):** Verinin hangi alanları taşıdığını söyleyen üç JSON.
> **eli15 (detay):** JSON ayrıdır çünkü veri sözleşmesi koddan bağımsız okunur; `audit` de `Validator` da onu tüketir. İçine alan, enum ve düzeltme eşlemesi yazılır. Yeni alan eklenince düzenlenir. İçine kod yazılırsa hem kırılır hem okunmaz.

**docs (`*.md`)**
> **eli10 (basit):** Kural, spec ve denetim kaydı tutan beş yazı.
> **eli15 (detay):** Her biri ayrıdır çünkü karar (dizin/adlandırma), iş listesi (todos) ve kanıt (checklist) farklı sahiptir. Okunmaları işe kuraldan başlamayı sağlar. Kapı kapanınca yalnız ilgili satır değişir. Tek dosyada toplansa denetim geçmişi silinir.

**sınıf (`src/Media/*.php`)**
> **eli10 (basit):** Beş sınıfın kodu: yazıcı, denetçi, slug, taksonomi, kimlik.
> **eli15 (detay):** Dosyalar ayrıdır çünkü her sınıfın tek sorumluluğu vardır (SRP). Ölçüm tablosunda satır/bayt olarak taşınır ki büyüme izlenebilsin. Sınıf büyüdüğünde sorumluluk bölünür. Tek dosyada toplansalar bağımlılık halkası oluşur ve test edilemez hâle gelir.

### 3.3 CLI Araçları (3 komut — disk kanıtı)

| Araç | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|------|---------|--------|-----------|---------------------|
| `bin/scan.php` | `media/` → `catalog/catalog.jsonl` (+ MySQL varsa) | 261 satır: bayrak dokümanı `:14-24`, ayrıştırma `:62-64` (`--rebuild` `--dry-run` `--deep` `-v`), tek gerçek `hash_file` `:155` (yalnız `--deep` dalı) | Türetilmiş indeks yeniden üretilebilir olsun (ADR-092 §5.2) | İndeks alanı/DB yazımı değişince |
| `bin/audit.php` | Arşiv lint'i; salt okunur | 499 satır: çıkış `0 = hata yok · 1 = HATA · 2 = kullanım` (`:11`), **22 kural kimliği** (`:17-23`), hafif mod (`:51`), deep hash (`:398`) | Kabul kriterleri (ADR-092 §7.1) otomatik denetlensin | Yeni kural kimliği eklendiğinde |
| `bin/ingest.php` | Kaynak → arşiv taşıma; `reports/ingest-*.csv` | 346 satır: giriş hash `:197`, STDIN onayı `:299-301` (`evet`), tek `copy(` `:328`, kopya hash doğrulama `:336` | Taşma tek bayt bile olsa onaysız olmasın | Taşma kuralı/format değişince |

**eli10/eli15 blokları (§3.3):**

**`scan.php`**
> **eli10 (basit):** Kütüphaneyi gezip içindekilere dair bir liste (katalog) çıkaran komut.
> **eli15 (detay):** Ayrı komuttur çünkü tarama okuma ağırlıklıdır ve sık koşulur. İçine bayrak okuma, JSON doğrulama ve katalog yazma adımları girer. İndeks alanı değişince güncellenir. Çalışmazsa arşiv hiç kataloglanamaz ve arama çalışmaz.

**`audit.php`**
> **eli10 (basit):** Arşivde kurallara uymayan yeri gösteren denetçi komut.
> **eli15 (detay):** Ayrıdır çünkü denetim bağımsız olmalı; taşıma yapan `ingest` ile aynı dosyada olsaydı denetim kendi hatasını göremezdi. İçine 22 kural kimliği ve çıktı satırı formatı yazılır. Yeni kural eklenince değiştirilir. Olmazsa bozuk yol/bozuk kimlik sessizce arşive girer.

**`ingest.php`**
> **eli10 (basit):** Dışarıdan dosyaları arşive, önce prova (dry-run) sonra onayla kopyalayan komut.
> **eli15 (detay):** Ayrıdır çünkü tek yazan komuttur; yazma yetkisi tek elde toplanır. İçine adlandırma, kopya ve rapor adımları girer. Kaynak format değişince güncellenir. Onay kapısı kaldırılırsa yanlış dosya arşive girer ve master dokunulmazlık zedelenir.

### 3.4 Çekirdek Sınıflar (5 — `src/Media/`)

| Sınıf | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|-------|---------|--------|-----------|---------------------|
| `CatalogWriter.php` | `catalog.jsonl` yazar (türetilmiş indeks) | 491 satır: JSONL satır üretimi + dosya yönetimi | Yan-JSON SSOT, indeks türevdir; DB/JSON çökse arşiv yaşasın | Katalog alanı değişince |
| `Validator.php` | `media.schema.json` için bağımlılıksız doğrulayıcı | 431 satır; 6 kural (dosya başı yorum): required · pattern · enum+tax · type · aralık · additionalProperties=false | Paket çekilmeden şema denetlensin | Şema kuralı eklendiğinde |
| `Slugger.php` | kebab-case slug üretimi | 140 satır; fold 12 eşleme → `[a-z0-9-]` → MAKS 80 (`:17`) → boşsa `YEDEK = 'isimsiz'` (`:20`) | Adlandırma kuralı (`docs/adlandirma.md` 1-3) tek kodda dursun | Adlandırma kuralı değişince |
| `Taxonomy.php` | Kapalı taksonomi okuyucusu | 104 satır; `values()` / `isValid()` yalnız `config/taxonomy.json`'dan döner | DB/JSON drift'inin tek referansı olsun | `taxonomy.json` anahtar eklendiğinde |
| `Ulid.php` | ULID üretimi + doğrulama | 84 satır; Crockford base32 (`:16`), desen `^[0-9A-HJKMNP-TV-Z]{26}$` (`:19`), ilk 10 karakter 48-bit ms | Kalıcı kimlik slug'dan bağımsız olsun (ADR-092 §6.1 kural 2) | Kimlik kuralı değişirse |

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
> **eli15 (detay):** Ayrıdır çünkü kapalı liste tek dosyadan okunmalı, koda gömülmemelidir. İçine `values()` ve `isValid()` yazar; `taxonomy.json` değişince o değişir. Koda gömülseydi iki ayrı liste oluşur ve drift doğardı. `etiket[]` açık set olduğundan buradan denetlenmez.

**`Ulid.php`**
> **eli10 (basit):** Her kayda değişmez 26 karakterlik kimlik üreten sınıf.
> **eli15 (detay):** Kimlik ayrıdır çünkü slug değişince dosya taşınmamalı, indeks kırılmamalıdır. İçine alfabe, uzunluk ve doğrulama yazılır. ADR kimlik kuralını değiştirirse güncellenir. Unutulursa yeniden adlandırma indeksi bozar.

### 3.5 Config (3 dosya)

| Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|-------|---------|--------|-----------|---------------------|
| `config/media.schema.json` | Meta JSON şeması | 679 satır / 27535 bayt · `$defs` 34 · `oneOf` 4 varlık · ULID/slug/sha256/datetime kalıpları · `$id` + `version` alanı taşır | `audit` hangi alanın zorunlu olduğunu şemadan okur | Yeni varlık/alan geldiğinde (`version` artırılır) |
| `config/taxonomy.json` | Kapalı taksonomi + yaşam döngüsü | 27 satır / 1515 bayt · **17 anahtar / 98 değer** · `durum` = inbox→aktif→sakli→tekrar→arsiv · `kurallar.mod` = kapalı | Enum şemasıyla senkron tek kaynak | Yeni değer = 1 satır + audit |
| `config/mojibake-fix.json` | Bozuk → doğru ad eşlemesi | 44 satır / 2147 bayt · `toplam_eslesme` 21 · yön `kaynak -> duzeltilmis` · uygulama: `ingest normalize asamasi` | Slug'dan önce normalize edilmezse bozuk slug üretilir | Yeni bozuk kalıp görüldüğünde |

**eli10/eli15 blokları (§3.5):**

**`media.schema.json`**
> **eli10 (basit):** Bilgi dosyalarında hangi alanın bulunması gerektiğini söyleyen kural defteri.
> **eli15 (detay):** Ayrı dosyadır çünkü veri sözleşmesi kod olmadan okunmelidir; `audit` onu harici paket çekmeden uygular. İçine alan, tip ve desen tanımları yazılır. Yeni alan gelince `version` artırılarak düzenlenir. Kaybolursa denetim kör kalır.

**`taxonomy.json`**
> **eli10 (basit):** Kullanılabilecek etiketlerin ve durumların kapalı listesi.
> **eli15 (detay):** Ayrıdır çünkü enum listesi hem JSON şemasında hem MySQL'de aynı olmalıdır; tek kaynak olmasa drift doğar. İçine 17 anahtar ve 98 değer yazılır. Yeni değer eklenince tek satır eklenir. Serbest `etiket[]` dışında liste burada tutulur.

**`mojibake-fix.json`**
> **eli10 (basit):** Bozuk yazılmış Türkçe adları doğrusuna çeviren eşleme listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü düzeltme veriye özeldir, koda gömülmez. İçine bozuk → doğru eşlemeleri ve uygulama sırası yazılır. Yeni bozuk kalıp görünce eklenir. Uygulanmazsa bozuk karakterli adlar yanlış slug üretir.

### 3.6 Docs (5 dosya)

| Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|-------|---------|--------|-----------|---------------------|
| `docs/dizin-yapisi.md` | Fiziksel disk yerleşimi + ölçek kuralları | 235 satır: tek eksen, ağac, format, master, yol hesabı | Yerleşim ADR-092'den okunur, tek yerde dursun | Yerleşim/ölçek eşiği değişince |
| `docs/adlandirma.md` | 6 adlandırma kuralı | 193 satır: ASCII fold · ön ekler · çakışma · ULID · kodlama · mojibake | `Slugger` kuralının insan-okur kaynağı olsun | Kural değişince (Slugger ile birlikte) |
| `docs/checklist.md` | Kabul denetim listesi (kanıt + `VR` işaretli açık maddeler) | 63 satır; kanıt türü: `dosya:satır` · `grep` · `commit` · `kayıt` | Kanıtsız `- [x]` yok kuralı | Kapı koşulu değişince |
| `docs/todos.md` | Sıralı iş listesi (P0→P1→P2) | 38 satır; sütun: öncelik · görev · sahip · tahmin · kapı koşulu | P0 kapanmadan P2 başlamaz | Görev kapanınca `- [x]` |
| `docs/faz3-gui-spec.md` | Faz 3 web arayüzü ön spec'i | 537 satır: 8 ekran + ASCII wireframe + BEM eşlemesi | `.ai/ui-design/` Kalıp D üretimi öncesi hammadde | Ekran/karar değişince (ön spec statüsü) |

**eli10/eli15 blokları (§3.6):**

**`dizin-yapisi.md`**
> **eli10 (basit):** Medyanın hangi klasörde duracağını ve neden tek sırayla olduğunu anlatan yazı.
> **eli15 (detay):** Ayrı dosyadır çünkü yerleşim kararı kodun dışında alınır ve ADR'den okunur. İçine dizin ağacı, format ve ölçek kuralları yazılır. Yerleşim değişince güncellenir. Okunmadan klasör açılırsa eksen kırılır, duplike klasör doğar.

**`adlandirma.md`**
> **eli10 (basit):** Dosya ve klasör adlarının nasıl yazılacağını anlatan altı kural.
> **eli15 (detay):** Ayrıdır çünkü hem insan hem kod bunu okur; `Slugger` bu belgeyi işaret eder. İçine fold, önek, çakışma, kimlik, kodlama ve mojibake anlatılır. Kural değişince kodla birlikte güncellenir. Yoksa adlar tutarsızlaşır ve arama çalışamaz.

**`checklist.md`**
> **eli10 (basit):** Ne kanıtlandığını, neyin hâlâ açık olduğunu gösteren onay listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü kanıt ile iş listesi farklıdır: biri "oldu mu", diğeri "ne yapılacak". İçine kanıt türü etiketi ve `VR` satırları yazılır. Kapı kapanınca satır işaretlenir. Tek dosyada toplansa kanıt kaybolur.

**`todos.md`**
> **eli10 (basit):** Sırayla yapılacak işler ve kimin neyi ne zaman yapacağı.
> **eli15 (detay):** Ayrıdır çünkü plan, kanıttan farklı hızda değişir. İçine öncelik, sahip, tahmin ve kapı koşulu yazılır. İş bitince kapanır. Checklist ile beraber tutulur ki iş ile kanıt eşleşsin.

**`faz3-gui-spec.md`**
> **eli10 (basit):** Web ekranlarının şimdiden çizilmiş, henüz kodlanmamış taslağı.
> **eli15 (detay):** Ayrı dosyadır çünkü spec, uygulamadan önce yazılır ve `.ai/ui-design/` Kalıp D'ye dönüşecektir. İçine ekran listesi ve ASCII çizimler girer. Ekran kararı değişince güncellenir. Koda gömülse tasarım geçmişi kaybolur.

### 3.7 Vault ↔ Disk Çelişkileri (disk kazanır — uydurulmaz)

| İddia (kaynak) | Disk gerçeği (2026-10-12) | İşaret |
|----------------|---------------------------|--------|
| `docs/dizin-yapisi.md:211`/`:217` + `docs/checklist.md:13`: "62 dizin / `media\` altında 0 dosya (2026-09-29)" — ayrıca ADR-092 §5.2: `Test-Path .../media` = True | `media/` dizini bu oturumda **tamamen YOK**; klasör = **5 dizin / 22 dosya** | VERIFICATION REQUIRED |
| `docs/dizin-yapisi.md:214` + `docs/adlandirma.md:185-187`: schema 26.856 B · taxonomy 1.488 B · mojibake 2.103 B | Ölçülen: **27535** · **1515** · **2147** bayt | VERIFICATION REQUIRED |
| `docs/checklist.md:25` (+ ADR-092 §5.2): "toplam 2.060 satır" | PHP satır toplamı **2356** (bin 1106 + src 1250, bu oturum satır sayımı) | VERIFICATION REQUIRED |
| `docs/dizin-yapisi.md:62`/`:231` + `bin/audit.php:25`: "composer bu makinede YOK" · "PHP 8.4 bu makinede yok → `php -l`/çalışma testi YAPILMADI" | `vendor/` bu oturumda **YOK**; PHP/composer kurulu mu bu oturumda shell erişimi olmadığından **tekrar koşulmadı** (2026-10-03 oturumunda `php -l` 8/8 geçmişti) | VERIFICATION REQUIRED |
| `bin/audit.php:17`: kural kimlikleri "docs/audits ile birebir" | `docs/audits/` **diskte YOK** (`docs/` = 5 md) | VERIFICATION REQUIRED |
| `../.ai/glossary.md:252`: "Faz 0: `media.coremusic.net` dizini mevcut değil" | Dizin **VAR**: 22 dosya / 5 dizin | VERIFICATION REQUIRED |
| `../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture.md:120`: media satırı "Fiziksel kanıt: dizin YOK · PLANNED" | Dizin **VAR** + 2356 satır PHP kodu; :5000/:6000 servisi henüz yok | VERIFICATION REQUIRED |
| `../.ai/index.md` eski satır atıfları (eski CONTEXT: `:162`/`:194`) | Medya satırı şu an **`:154`** | VERIFICATION REQUIRED |
| Eski CONTEXT v1.0.0 (2026-10-03): "composer.json/.gitignore mojibake içeriyor" + "ADR-092 §8 m.11" | Her iki dosya da bu oturumda **temiz** Türkçe okundu; ADR-092'de **§8 yok** (§1–§7 var) | VERIFICATION REQUIRED |

**eli10/eli15 blokları (§3.7):**

> **eli10 (basit):** Bazı eski yazılar başka bir bilgisayardaki veya başka bir tarihteki durumu anlatıyor; bugünün diski ölçülünce farklı çıktı.
> **eli15 (detay):** Çelişki burada uydurmak yerine iki tarafı da yazıp "disk kazanır" demek içindir. Eski kayıt silinirse neden yanlış olduğu anlaşılıp tekrarlanmaz. Okunması hangi sayının güvenilir olduğunu gösterir. Düzeltme bu dokümanın işi değil, ilgili dosya sahibinin işidir — frozen ADR'ler ve vault dosyaları değişmez, yalnız işaretlenir.

### 3.8 Boot Sırası, Otorite ve Klasör Dışı İlişkiler

**Boot (her görev başında):** kök `AGENTS.md` + kök `CLAUDE.md` → bu klasörde işse `media.coremusic.net/CLAUDE.md` → yalnız ihtiyaç anında `.ai/` dosyaları (bkz. [[../AGENTS.md]] §14).

| Dosya | docType | authority (frontmatter) | Satır |
|-------|---------|-------------------------|------:|
| `CONTEXT.md` (bu dosya) | context | Derived — SSOT: `.ai/` + kök `AGENTS.md` | bu sürüm |
| `CLAUDE.md` | claude | Derived — SSOT: `.ai/CLAUDE.md` + ADR-092 | 302 |
| `AGENTS.md` | agents | Derived — SSOT: `.ai/AGENTS.md` (v22.x) §5/§6/§9 | 305 |
| `WORKFLOW.md` | workflow | Derived — SSOT: `.ai/WORKFLOW.md` + kök `AGENTS.md` | 301 |

| Dış dosya | İlişki | Kanıt |
|-----------|--------|-------|
| `../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid.md` | Disk ekseni + ULID + §6.1/§7.1 kuralları (accepted, v1.1.2, 2026-09-29) | frontmatter okundu |
| `../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture.md` | Servis yerleşimi :5000/:6000 (accepted, v1.0.1) | frontmatter + §2.1 satır 120 okundu |
| `../.ai/.sql/mysql/media_catalog.sql` | DB `media_catalog` | bu oturum: 9 `CREATE TABLE` + 1 `CREATE VIEW` (`v_asset_search`) |
| `../.ai/index.md` | Subdomain tablosunda medya satırı (PHP + FFmpeg) | satır 154 okundu |
| `../AGENTS.md` · `../CLAUDE.md` · `../CONTEXT.md` | Kök boot + skill registry + proje envanteri | diskte mevcut |
| `reports/ingest-*.csv` | `ingest.php` rapor çıktısı (klasör diskte YOK) | `.gitignore:5` |

**eli10/eli15 blokları (§3.8):**

> **eli10 (basit):** Bu klasörün işi bitmiyor; kural vault'tan gelir, kararı ve DB şeması da orada durur.
> **eli15 (detay):** Boot sırası ayrı tabloda çünkü hangi dosyanın ne zaman okunduğu sabittir. Otorite satırları frontmatter'dan okunmuştur, tahmin edilmemiştir. Dış ilişkiler ayrıdır çünkü o dosyalar bu klasörün içinde değildir. Tablo olmasa klasör kendi başına doğru sanılır ve yanlış üretilir.

### 3.9 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur (her şey tek dosyada olsaydı) |
|---|---------|----------|----------------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek yerde kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Sözleşme tek kaynak** | Şema/taksonomi `config/`'te tek yerde durur | Kodda ayrı, DB'de ayrı liste → drift ve tutarsız kayıt |
| 4 | **Türev izolasyonu** | `media/` ve `catalog/` repodan ayrı kalır | Medya ve üretilen indeks git'e girer, repo taşınamaz hâle gelir |
| 5 | **Araç karantinası** | CLI (`bin/`) ile kütüphane (`src/Media/`) ayrı durur | Komut ile kütüphane birbirine karışıp yeniden yazılamaz hâle gelir |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: veri sözleşmesi `config/`, araç `bin/`, kütüphane `src/`, karar `docs/` durur. Böylece bir yer değiştirince sadece o değişir. Sözleşme kodun içine girse her alan için kod revizyonu gerekir, medya repoya girse repo taşınamaz hâle gelir. Hepsi tek dosyada toplansa bakım ve denetim kaybolurdu.

---

## 4. Kurallar

Guardrail numaralarının kaynağı: [[../.ai/CLAUDE.md]] (Hard Guardrails) + ADR-092 §6.1.

| # | Kural (özet) | Ref |
|---|--------------|-----|
| 1 | `media/` içeriği asla git'e girmez; `!src/Media/` negasyonu korunur | `.gitignore:1-11` + ADR-092 §2.2 |
| 2 | Tek disk ekseni: sanatçı → albüm → parça; tür/dönem/durum = TAG, klasör adı DEĞİL | ADR-092 §2.1 |
| 3 | Master (`.flac`/`.wav`) dokunulmaz · varyant düz durur · `derived\` klasörü YOK | ADR-092 §2.1 + `docs/dizin-yapisi.md` §3.1 |
| 4 | sha256 **yalnız girişte**; tarama/denetim hafif modda yeniden hash yapmaz | ADR-092 §6.1 kural 5 · `scan.php:155` yalnız `--deep` · `ingest.php:197` |
| 5 | `ingest` `--commit` yokken tek bayt kopyalamaz; onay = STDIN `evet` | `ingest.php:299-301` + tek `copy(` `:328` + ADR-092 §6.1/§7.1 |
| 6 | Yasaklı çağrı yasak: `eval` · `exec` · `shell_exec` · `passthru` · `system` · `proc_open` | bu oturumda 8 PHP dosyasında grep = **0 eşleşme** |
| 7 | Sayı/kanıt yoksa `VERIFICATION REQUIRED` — uydurulmaz | Zero-Hallucination · [[../.ai/CLAUDE.md]] |
| 8 | Bu dokümandaki her sayı ölçümdür; yeni ölçümde eski sayı geçersizdir | Şablon doğrulama kuralı |

- **Çelişki kuralı:** vault ↔ disk → **disk kazanır** + `VERIFICATION REQUIRED` (§3.7).
- **Sembol:** dekoratif emoji yasak; yalnız `VERIFICATION REQUIRED` işareti ve `[[...]]` kullanılır.
- **REDACTED:** secret/anahtar hiçbir `.md`'ye yazılmaz.

> **eli10 (basit):** Kurallar bu klasörde neyin asla yapılmayacağını ve her iddianın nasıl kanıtlanacağını söyler.
> **eli15 (detay):** Kurallar ayrı yazıldı ki kural metni kod içine gömülmesin; değişince kod değil, tablo değişir. Okunması işe başlarken yasağı baştan bilmeyi sağlar. Yazması denetimi mümkün kılar. İhlalde disk kazanır kuralı ve işaret mekanizması devreye girer.

---

## 5. Workflow

```text
ENVANTER ÖLÇ (dosya + satır + bayt) → KOD/CONFIG OKU (1. kez, karar ver)
→ ŞABLON İSKELETİ (§1-§7) → ÇELİŞKİ KAYDI (§3.7) → DOĞRULA (§6)
→ RAPOR → COMMIT (subagent ATMAZ)
```

Adım detayı ve kapılar: [[WORKFLOW.md]] §3.1 / §3.2.

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, çelişkileri kaydet, doğrula ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Akış tek yönlüdür çünkü her adım bir öncekinin çıktısını kullanır. Ölçüm yazıdan önce gelir ki sayı uydurma imkânsız olsun. Doğrulama (§6) rapordan önce koşulur. Commit orkestratöre aittir; subagent atarsa iz sürme bozulur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 9 alan: title · type · category · date · updated · version · status · authority · `docType: context` |
| 2 | Bölüm | §1–§7 numaralı, aynı sıra, alt başlık ≤3 seviye |
| 3 | Envanter | 22 dosya · 5 dizin · 27 girdi · PHP 2356 satır (bin 1106 + src 1250) — hepsi bu oturum ölçümü |
| 4 | 5 sütun | §3.1–§3.6 envanter satırları `Katman/Dosya · Ne için · Neyden · Neden var · Ne zaman düzenlenir` — hiçbir hücre boş değil |
| 5 | eli10 + eli15 | §1, §2, §3.1 (9 madde), §3.2 (5 grup), §3.3–§3.6 her madde, §3.7–§3.9, §4, §5 maddelerinde çift blok var (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 6 | Çelişki | §3.7 = 9 satır; her satırda `VERIFICATION REQUIRED` + disk kazanır |
| 7 | Placeholder | Üretilen metinde şablon değişkeni tutamağı kalmadı |
| 8 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 9 | K-space | Tablo/bağlantı atıfı 0; tek iz §1'deki kaldırma notu |
| 10 | Uzunluk | 250–450 satır |
| 11 | Sembol | Yalnız `VERIFICATION REQUIRED` işareti; dekoratif emoji yok |
| 12 | Dokunulmaz | Yalnız `CONTEXT.md` yazıldı; başka dosya oluşturulmadı/değişmedi · commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Klasör kuralları | [[CLAUDE.md]] | Yasak + guardrail özeti |
| Klasör rolleri | [[AGENTS.md]] | Dosya → rol routing |
| Klasör süreç | [[WORKFLOW.md]] | Adım + kapı |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif · §5 anti-overthink · §7 loop |
| Kök skill registry | [[../CLAUDE.md]] | Skill Registry + vault bağlantıları |
| Kök envanter | [[../CONTEXT.md]] | Proje geneli context |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Vault agent registry | [[../.ai/AGENTS.md]] | Routing/handover SSOT |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kataloğu (hub) |
| Dizin kararı | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid.md]] | Disk ekseni + ULID (accepted, v1.1.2) |
| Servis kararı | [[../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture.md]] | :5000/:6000 yerleşim (accepted, v1.0.1) |
| Master katalog | [[../.ai/index.md]] | Subdomain tablosu — medya satırı 154 |
| Audit trail | [[../.ai/log.md]] | append-only kayıt |
| DB şeması | `../.ai/.sql/mysql/media_catalog.sql` | 9 tablo + `v_asset_search` view |
| Dizin/adlandırma belgesi | `docs/dizin-yapisi.md` · `docs/adlandirma.md` | Ağaç + 6 kural |
| Denetim + iş listesi | `docs/checklist.md` · `docs/todos.md` | Kapı kanıtı + P0/P1/P2 |
| GUI ön spec | `docs/faz3-gui-spec.md` | 8 ekran taslağı |
| Disk kanıtı | `media.coremusic.net/` | Bu dokümandaki tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12 · **docType:** context
