---
title: "CoreMusic — media.coremusic.net Klasör Kuralları"
type: docs
category: media
docType: claude
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md + ADR-092"
---

# media.coremusic.net — CLAUDE.md

**docType:** claude · **Klasör:** `media.coremusic.net/` · **Sorumlu:** Security Engineer (kural), MO (vault)

**Zorunlu Bağlantılar:** [[CONTEXT]] · [[AGENTS]] · [[WORKFLOW]] · [[../AGENTS.md]] · [[../.ai/CLAUDE.md]]

---

## 1. Amaç

Bu doküman `media.coremusic.net/` üzerinde **neyin yasak olduğunu ve hangi kuralın kimi koruduğunu** tanımlar. Medya arşivi geri alınamaz varlıktır (master dosya + 38 GB+ ölçek); buradaki her yasağın bir **gerekçesi** vardır — ceza değil koruma. Çelişkide [[../.ai/CLAUDE]] (16 Hard Guardrail) kazanır.

| Kural kaynağı | Ne verir | Karşılığı |
|---------------|----------|-----------|
| ADR-092 (accepted, v1.1.2) | Disk ekseni · master dokunulmazlık · sha256 yalnız girişte | §4 #2-#5 |
| ADR-039 (accepted, v1.0.1) | Servis yerleşimi :5000/:6000 | §3.3 |
| Kök `AGENTS.md` §4-§5 | Kod öncesi keşif · anti-overthink | §5 zinciri |
| `.gitignore` | `media/` · `catalog/` · `reports/` · `vendor/` repoda değil | §4 #1 |
| Zero-Hallucination | Ölçülmeyen sayı yazılmaz | §4 #8 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Bu klasördeki yasak + zorunlu kural listesi | Rol/atama → [[AGENTS]] |
| Medya varlığı koruma kuralları (master, byte, taşıma) | Adım/kapı akışı → [[WORKFLOW]] |
| Vault ↔ disk çelişki kuralı (disk kazanır) | DB şeması kuralı → `.ai/.sql/mysql/media_catalog.sql` |
| Secret/REDACTED + log maskesi | Vault geneli 16 guardrail metni (yalnız referans) |

- **Kullananlar:** Backend Architect (CLI kodu), Data Engineer (şema), Security Engineer (hash/secret), QA Engineer (denetim), MO (doküman).
- **Ön koşul:** [[CONTEXT]] (envanter) okunmuş; hedef dosya diskte mevcut.
- **Not:** Her kuralın "Neden var" sütunu doludur — boş kural yazılmaz (şablon §3.5 `claude` ağırlığı).

---

## 3. Mimari

### 3.1 Korunan Yüzeyler — 5 Sütun (4 zorunlu soru)

| Yüzey (dosya/katman) | Neyi korur (işlev) | Kural metni + ref | Neden var (gerekçe) | Ne zaman revize edilir |
|----------------------|--------------------|-------------------|---------------------|------------------------|
| `media/**` medya ağacı | Master + varyant + yan JSON | `.gitignore:2` — `media/` repoya girmez | 38 GB+ varlık baytı git'e girerse repo taşınmaz ve her clone 38 GB çeker (ADR-092 §8) | Yeni türev/lokalizasyon açıldığında |
| Master dosyalar (`.flac`, `.wav`) | Orijinal byte | ADR-092 §5 Master: dokunulmaz · dönüştürülmez · silinmez | Arşivin değeri "orijinal"dedir; dönüştürülürse tekrar üretilemez | Yalnız ADR revizyonu |
| Yan JSON (`meta.json`, `album.json`, `artist.json`) | Meta + kimlik (ULID) | `config/media.schema.json` (v2) + `Validator` 6 kural | Gömülü etiket yazılsa master byte değişir (ADR §3 alternatif 5) | Şema `version` artışında |
| `catalog/` + MySQL `media_catalog` | Türetilmiş indeks | `catalog.jsonl` yeniden üretilebilir; DB = indeks, JSON = SSOT | DB çökse arşiv yaşar (ADR §5.2) | Katalog alanı eklendiğinde |
| `docs/checklist.md` + `todos.md` | Kapı kanıtı | "Kanıtsız `- [x]` yok" (checklist kural satırı) | Kanıtsız kapanan kapı, kapanmamış kapıdan tehlikelidir | Kapı koşulu değişince |
| `.env` / secret / anahtar | Kimlik bilgisi | REDACTED: secret hiçbir `.md`/`.json`/`.log`'a yazılmaz | Vault ve log okunur; sızan anahtar kalıcıdır | Asla (sürekli) |

**eli10/eli15 blokları (§3.1 — 6 yüzey):**

**`media/**` medya ağacı**
> **eli10 (basit):** Şarkı, klip ve görsellerin durduğu klasör — repoya hiç girmez.
> **eli15 (detay):** Ayrı kural çünkü dosyalar tek tek değil, klasörün tamamı git dışındadır; bir tanesi bile girse 38 GB depo şişer. Kural `.gitignore` metnindedir, kod içine gömülmez. İlkece gözden geçirilir, güncel değildir. Kalkarsa arşiv kazaen commit edilir ve geri almak zorlaşır.

**Master dosyalar**
> **eli10 (basit):** Kayıpsız orijinal dosyalar; hiçbir program onlara dokunamaz.
> **eli15 (detay):** Ayrı yasak çünkü kayıpsız olan bir kez gider, yenisi üretilemez. Kural ADR-092'de yazılıdır ve kod bunu yalnızca okur. Yalnız ADR revize edilirse değişir. İhlalde arşivin değeri kalmaz.

**Yan JSON**
> **eli10 (basit):** Dosyanın bilgisi ayrı bir metin dosyasında tutulur, şarkıya işlenmez.
> **eli15 (detay):** Ayrı korunur çünkü şarkıya etiket yazılsaydı orijinal bayt değişirdi. Kural şemadadır (`media.schema.json`) ve `Validator` uygular. Şema sürümü artınca gözden geçirilir. Yoksa her güncelleme master'ı bozar.

**`catalog/` + MySQL**
> **eli10 (basit):** Arama için üretilen ikincil liste — asıl bilgi onda değil.
> **eli15 (detay):** Ayrı korunur çünkü indeks her zaman yeniden üretilebilir olmalıdır; kalıcı bilgi sanılırsa yanlış güncelleme kaynak veriyi ezilir. Kural dosya başı yorumlarda (`CatalogWriter.php`). Katalog alanı eklenince güncellenir. İhlalde arama yanıltıcı hâle gelir.

**checklist + todos**
> **eli10 (basit):** "Ne kanıtlandı, ne kaldı" yazan dosyalar; kanıtsız iş yapılmaz işaretlenmez.
> **eli15 (detay):** Ayrı korunur çünkü kapı kuralı süreç içindedir, kodda değil. Metin dosyanın başındadır. Kapı tanımı değişince değişir. İşaretlenirse denetim değeri kalmaz, yanlış "tamam" görünür.

**Secret / REDACTED**
> **eli10 (basit):** Şifre ve anahtarlar hiçbir yazıya kopyalanmaz.
> **eli15 (detay):** Ayrı ve mutlak kuraldır çünkü yazılar okunur, örneklenir, çoğaltılır. Kaynak vault anayasasındadır (`.ai/CLAUDE.md`). Sürekli geçerlidir, revize edilmez. Sızarsa tüm kapılar yeniden kapatılmalıdır.

### 3.2 Hard Guardrail Özeti (bu klasöre uyarlanmış)

| # | Kural | İhlal sonucu | Neden var |
|---|-------|--------------|-----------|
| 1 | `media/` · `catalog/` · `reports/` · `vendor/` git'e girmez; `!src/Media/` negasyonu silinmez | Repo kirlenir / kod repodan düşer | `.gitignore:2,4,5,7,10-11` — `media/` çapasızdı, `src/Media/`'yı yutuyordu |
| 2 | Master `.flac`/`.wav` dönüştürülmez, silinmez, byte'ı değişmez | Arşiv değeri kaybedilir | ADR-092 §5 |
| 3 | `derived\` klasörü açılmaz; varyant düz durur (`audio.mp3`) | "Orijinal mi?" belirsizleşir | ADR-092 §2.1 m.5, §2.3 |
| 4 | sha256 **yalnız girişte**; `scan`/`audit` hafif modda yeniden hash yapmaz | Gereksiz I/O + ADR §6.1 m.5 ihlali | `scan.php:145,155` · `audit.php:391,398` |
| 5 | `ingest --commit` yokken tek bayt kopyalanmaz (dry-run) | Kaynaksız/yanlış taşıma geri alınamaz | `ingest.php:291-293` dry-run çıkışı + `:299-303` `evet` onayı + `:328` tek `copy(` |
| 6 | Yasaklı çalıştırma çağrıları: `eval`·`exec`·`shell_exec`·`passthru`·`system`·`proc_open` | Kod enjeksiyon yüzeyi açılır | Ölçüm: **0 eşleşme** (2026-10-03) |
| 7 | Kapalı taksonomi: `taxonomy.json` dışına değer eklenmez (`etiket[]` hariç) | Enum drift, DB/JSON ayrışması | `taxonomy.json` `kurallar.mod: kapali` |
| 8 | Dosya/klasör adı ASCII + slug ≤80; Windows-reserved yasak; boş slug → `isimsiz` | Kırık yol / çakışma / MAX_PATH | `docs/adlandirma.md` 1-6 · `Slugger.php:19-20` |
| 9 | Boş slug **uydurulmaz** — `Slugger::YEDEK = 'isimsiz'` | Kod ile kural ayrışır | `adlandirma.md` §8.7 |
| 10 | Ölçülmemiş sayı/kanıt yazılmaz → `⚠️ VERIFICATION REQUIRED` | Hallüsinasyon | Zero-Hallucination · şablon §4.1 #2 |
| 11 | Frozen ADR metinleri kopyalanmaz, yalnız wiki-link | revert | Şablon §4.1 #4 |
| 12 | `git commit` subagent tarafından **ATILMAZ** | Dağınık tarih, iz sürme bozulur | Kök `AGENTS.md` §7/§8 |

**eli10/eli15 blokları (§3.2 — 12 guardrail, özet blok):**

> **eli10 (basit):** Bu klasörde en çok dikkat edilen üç şey: medya repoya girmesin, orijinal dosyalar değişmesin, taşıma onaysız olmasın.
> **eli15 (detay):** Kurallar ayrı tabloda çünkü her birinin gerekçesi farklıdır ve kod içine gömülse revizyonda kaybolur. Okunmaları işe başlamadan sınırı gösterir (hangi dosyaya dokunulamaz). Yazmaları denetimin tekrarlanmasını sağlar. İhlalde kod revert edilir + `log.md`'ye ERROR yazılır (kök `AGENTS.md` §18).

### 3.3 Yasaklı İşlemler (bu klasörde)

| # | Yasak | Gerekçe (neden var) | Ref |
|---|-------|---------------------|-----|
| 1 | `media/` içine kod, config, doküman, log, cache koymak | `media\` yalnız medya ağacıdır; karışırsa tarama kuralları bozulur | `docs/dizin-yapisi.md` §3 |
| 2 | Explorer ile toplu taşıma/yeniden adlandırma | 1M dosya ölçeğinde elle işlem kayıp üretir | `dizin-yapisi.md` §6 (CLI: robocopy/PHP) |
| 3 | `media.schema.json` içinde `additionalProperties` gevşetmek | Bilinmeyen anahtar sessizce geçer, veri bozulur | `media.schema.json` `$comment` ORTAK KURAL 1 |
| 4 | `mojibake-fix.json` eşlemesini tersine/çift yönlü yapmak | Kısa eşleme yanlış pozitif üretir; yön `kaynak -> duzeltilmis` sabit | `mojibake-fix.json` `yon` + `siralama` |
| 5 | `.gitignore` negasyonunu (`!src/Media/`) silmek | `media/` çapası PHP kodunu yutar, kod repoya girmez | `.gitignore:9-11` yorumu |
| 6 | `coremusic_media` şemasını medya arşivi sanmak | Ana uygulamanın cihaz senkron şemasıdır, arşiv DB'si `media_catalog`'tur | `dizin-yapisi.md` §3.2 uyarı |
| 7 | `php -l`/CLI sonucunu okumadan "çalışıyor" demek | Kapı kanıtsız kapanamaz | `checklist.md` kural satırı |
| 8 | `{{VARIABLE}}` veya emoji ile doküman teslim etmek | Şablon §4.1 #6-#8 | context-template §4.1 |

**eli10/eli15 blokları (§3.3 — 8 yasak, özet blok):**

> **eli10 (basit):** Klasörde yapılmaması gereken sekiz iş var; en kritiği medyayı elle taşımak ve kural dosyalarını gevşetmek.
> **eli15 (detay):** Yasaklar ayrı yazıldı çünkü gerekçeleri tek bir cümleye sığmaz; her biri ayrı bir kayıp türünü (veri, kod, itibar) engeller. Okunmaları hatayı işlemden önce görmeyi sağlar. Birleştirilirse hangi yasağın hangi dosyayı koruduğu kaybolur. Kural değişirse (ADR revizyonu) yalnız bu tablo güncellenir.

### 3.4 Ölçülmüş Kanıt Tablosu (kural ↔ disk)

| Kural | Beklenen | Ölçüm (2026-10-03) | Sonuç |
|-------|----------|---------------------|-------|
| Lint 8 dosya temiz | 8/8 | `php -l` → 8/8 "No syntax errors" (PHP 8.5.8, `C:\Php858\php.exe`) | GEÇTİ |
| Yasaklı çağrı yok | 0 | `eval|exec|shell_exec|passthru|system|proc_open(` → 0 eşleşme | GEÇTİ |
| `hash_file` yalnız ilgili yerlerde | yorum + deep/giriş | `scan.php:145(yorum),155` · `audit.php:391(yorum),398` · `ingest.php:197(giriş),336(kopya doğrulama)` | UYUMLU |
| `copy(` tek nokta | 1 gerçek | `ingest.php:327(yorum),328(gerçek)` | GEÇTİ |
| `md5_file` / `sha1_file` yok | 0 | yok | GEÇTİ |
| DB 9 tablo | 9 | `.ai/.sql/mysql/media_catalog.sql` → 9 `CREATE TABLE` + 1 `CREATE OR REPLACE VIEW` | GEÇTİ (DDL yüklenmedi, `⚠️ VR-3`) |
| Taksonomi 17/98 | 17 anahtar / 98 değer | `taxonomy.json` → 17 / 98 | GEÇTİ |
| Mojibake eşleşme | 21 | `mojibake-fix.json` `toplam_eslesme: 21` | GEÇTİ |
| Şema sürümü | v2 | `media.schema.json` `$defs.schema_v2` const 2 · 34 `$defs` | GEÇTİ |
| `tests/` var mı | — | `Test-Path tests/` = **false** (test dosyası yok) | **AÇIK** ⚠️ |
| `vendor/` var mı | — | `Test-Path vendor/` = **false** (composer install çalıştırılmadı) | **AÇIK** ⚠️ |
| Medya dosyası | — | uzantı taraması → **0 dosya** | BEKLENEN (faz 2 öncesi) |

**eli10/eli15 blokları (§3.4):**

> **eli10 (basit):** Kuralların gerçekten tutup tutmadığı tek tek sayılarla ölçülmüş; ikisi hâlâ açık.
> **eli15 (detay):** Kanıt tablosu ayrıdır çünkü "uygun" demek için sayı gerekir; kural metni tek başına yeterli değildir. Her satır komut veya sayım ile üretildi. Kapı (checklist VR-2/VR-3) bu tabloya dayanır. Ölçüm tekrarlandıkça eski sayı geçersiz olur.

### 3.5 Kod İçi Yorum ve Dosya-Başı Sözleşmeler

| Dosya | Sözleşme (dosya başı yorum) | Kuralı dayatan satır |
|-------|----------------------------|----------------------|
| `bin/audit.php` | "Salt okunur: medya ağacında hiçbir yazma işlemi yapmaz" · exit 0/1/2 | `:5-11` |
| `bin/audit.php` | Kural kimlikleri `docs/audits` ile birebir (json·sema·slug·ulid·sha256·mojibake·yol-260…) | `:17-23` |
| `bin/audit.php` | SSOT zinciri: `media.schema.json` → `taxonomy.json` → `mojibake-fix.json` → `adlandirma.md` → `dizin-yapisi.md` → ADR-092 | `:13-15` |
| `bin/ingest.php` | "`--commit` YOKKEN medya ağacına tek bayt bile kopyalama/yazma/silme YOKTUR" | `:10` |
| `bin/ingest.php` | Dry-run'da yalnız rapor: `reports/ingest-YYYYMMDD-HHMM.csv` (UTF-8 BOM, virgül, tırnaklı alan) | `:12-14` |
| `bin/scan.php` | Bayraklar `--rebuild` `--dry-run` `--deep` `-v` + bilgi metni | `:60-76` |
| `src/Media/CatalogWriter.php` | "SSOT yan JSON dosyalarıdır; bu indeks her zaman `scan.php` ile baştan üretilebilir" | başlık yorumu |
| `src/Media/Slugger.php` | `YEDEK = 'isimsiz'` (boş slug kararı) | `:19-20` |
| `src/Media/Taxonomy.php` | "DB/JSON drift'inin tek referansı burasıdır" | başlık yorumu |

**eli10/eli15 blokları (§3.5):**

> **eli10 (basit):** Her komutun başında kendi sözleşmesi yazıyor: ne yapar, neyi yapmaz.
> **eli15 (detay):** Sözleşme dosya başındadır çünkü okuyan kişinin kuralı aramaya gitmemesi gerekir. `audit` salt-okunur, `ingest` onaysız yazmaz, `scan` hafif modda hash'lemez. Yorum satırları kodla birlikte değişir. Silinirse kural kod içinden okunamaz hâle gelir.

### 3.6 Format ve Master Kuralı Özeti (dosya tipi → muamele)

| Tip | Kabul edilen uzantılar | Dosya adı | Muamele (neden var) |
|-----|------------------------|-----------|---------------------|
| Ses | `.mp3 .flac .wav .m4a .ogg .wma` | `audio.{orijinal-uzantı}` | Uzantı **ASLA değişmez** — kaynak format arşivin kanıtıdır |
| Video | `.mp4 .mkv .avi .webm` | `video.{uzantı}` | Aynı: orijinal korunur |
| Görsel | `.jpg .png .webp` | `cover.jpg` · `poster.jpg` · `avatar.jpg` | Master kapak klasör düzeyinde tekeldir (albümde `cover.jpg`) |
| Playlist | `.m3u .pls` | dosya adına dönüşmez | Parse edilip **koleksiyon tanımı**na dönüşür; kendisi rapora girer |
| Kabul edilmeyen | diğer her şey | — | `_hurda\` (ses hariç) veya rapor — ses asla çöpe atılmaz |

**eli10/eli15 blokları (§3.6):**

> **eli10 (basit):** Hangi dosyanın ne adla duracağı ve hangilerinin kabul edildiği burada yazılı.
> **eli15 (detay):** Tablo ayrıdır çünkü hem `audit` hem insan bunu kontrol eder; kod içine gömülse format eklemek kod revizyonu ister. Her satırda kabul listesi ile birlikte gerekçesi de vardır. Format değişince (yeni uzantı kararı) bu tablo + `audit` kural kimliği birlikte güncellenir. Yoksa yeni format sessizce çöpe düşer.

---

## 4. Kurallar

### 4.1 Zorunlu

| # | Kural | Ref |
|---|-------|-----|
| 1 | Yeni `.md` Guardrail #16 şablonundan üretilir (`context-template` §3.4) | [[../.ai/.templates/frontend/context-template]] |
| 2 | Yeni PHP dosyası `declare(strict_types=1)` + `namespace Media;` + `php -l` geçer | `composer.json` `scripts.lint` (8 dosya) |
| 3 | Yeni config alanı → `media.schema.json` + `taxonomy.json` (gerekirse) + `checklist` kanıtı | Şablon §3.5 `claude` ağırlığı |
| 4 | Değişiklik sonrası kapı: `php -l` 8/8 + ilgili CLI `--dry-run` | `docs/todos.md` P0-1 |
| 5 | Hata → `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol (3 başarısız → DUR) | Kök `AGENTS.md` §7 |
| 6 | Vault ↔ disk çelişkisi → disk kazanır + `⚠️ VERIFICATION REQUIRED` + rapor | Şablon §4.1 #7 |

### 4.2 Ek Kurallar

- **docType yükü:** bu dosya **kural ağırlıklıdır** — §1-§7 sırası korunur, ağırlık §3-§4'tedir.
- **Uzunluk:** hedef 300–500 satır; § sayısı 7 sabit, alt başlık ≤3 seviye.
- **Tarih:** `date` = ilk üretim, `updated` = her revizyonda.
- **Çelişkide:** `.ai/CLAUDE.md` (16 Hard Guardrail) > bu dosya > `docs/*` belgeleri; disk ölçümü ise her yazının üstündedir.

### 4.3 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | "PHP yok" diye eski kaydı tekrarlamak | 2026-10-03 ölçümü: PHP 8.5.8 var, `php -l` 8/8 geçti → §3.7 |
| 2 | `media/` klasörünü var saymak | `Test-Path` = false; yok olarak yazılır |
| 3 | `vendor/` elle doldurmak | Yalnız `composer install` üretir |
| 4 | Checklist'e kanıtsız `- [x]` | Kanıt etiketi: `dosya:satır` · `grep` · `commit` |
| 5 | Guardrail metnini ADR'den kopyalamak | Yalnız wiki-link verilir |

### 4.4 Hata → Kurtarma (error-recovery özeti)

| # | Hata | Ne yapılır | Kapı |
|---|------|-----------|------|
| 1 | `php -l` hatası | `.ai/.rules/error-recovery.md` → dosyayı yeniden yaz → `composer lint` tekrar | G3 |
| 2 | `audit` exit 1 (HATA) | Kural kimliğine göre ilgili dosyayı bul → düzelt → `audit` tekrar | G3 |
| 3 | `scan` kırık JSON/şema | `media.schema.json` mı yoksa veri mi yanlış ayrımı → Data + Backend | G3 |
| 4 | Aynı hata 3. kez | **DUR** + şüpheli varsayım + 1 kısa soru → MO | Kök §5.5 |
| 5 | `media/`'ya yanlışlıkla yazma | revert + `.gitignore` denetimi + MO (CRITICAL) | G2 |

> **eli10 (basit):** Hata çıkınca kör kör düzeltmek yok; adımlar önceden yazılı.
> **eli15 (detay):** Kurtarma tablosu ayrıdır çünkü panik anında karar verilmez. Her satırda hangi kapıya dönüleceği bellidir. Kök `AGENTS.md` §7 zinciriyle uyumludur. Uygulanmazsa aynı hata üç kez denenir ve iş durur.

### 4.5 Kural Revizyon Tetikleyicileri (bu dosya ne zaman değişir)

| Tetikleyici | Etkilenen madde | Sahip | Sonra |
|-------------|-----------------|-------|-------|
| ADR-092 revizyonu (yeni sürüm) | §3.2 · §3.6 · §4.1 | MO (vault-updater) | `updated` + checklist kanıtı |
| `media.schema.json` `version` artışı | §3.1 yan JSON · §4.1 #4 | Data Engineer | `Validator` + CLAUDE §3.5 birlikte |
| Yeni CLI komutu (`bin/`) | §3.3 · §4.1 #2 | Backend Architect | CONTEXT §3.3 + WORKFLOW §3.5 |
| `media/`/`catalog/` gitignore değişikliği | §3.1 #1 · §3.3 #5 | MO + Security | `.gitignore` + CONTEXT §3.7 |
| Kök `AGENTS.md` §6 routing revizyonu | AGENTS §4.1 → bu dosya §4.1 | MO | çelişkide kök kazanır |
| PHP/ortam değişimi (sürüm, composer) | §3.4 kanıt satırları | QA Engineer | ölçüm tekrarlanır, eski sayı geçersiz |

> **eli10 (basit):** Kuralların kendisi de güncellenir; hangi olay olunca hangi maddenin değişeceği baştan yazılı.
> **eli15 (detay):** Tetikleyici tablosu ayrıdır çünkü kural metni tek başına yetmez, ne zaman güncelleneceği de bilinmelidir. Her satırda madde, sahibi ve sonraki adım vardır. Okunması eskimiş kuralın fark edilmesini sağlar. Güncellenmezse eski kural yeni kodu durdurur ya da yanlış yönlendirir.

---

## 5. Workflow

```text
KURAL OKU (bu dosya) → ENVANTER ÖLÇ (CONTEXT §3) → DEĞİŞİKLİK (hedef dosya)
→ php -l 8/8 → ilgili CLI --dry-run → CHECKLIST kanıtı → RAPOR
→ COMMIT: subagent ATMAZ
```

Kapılar ve adım tablosu: [[WORKFLOW]] §3.1-§3.2.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: claude` |
| 2 | Bölüm | §1–§7, ≤3 başlık seviyesi |
| 3 | Kural yükü | §3-§4 kural/yasak ağırlıklı; her maddede "Neden var" dolu |
| 4 | Guardrail | §3.2 12/12 + §3.3 8/8 |
| 5 | Kanıt | `php -l` 8/8 · yasaklı çağrı 0 · `copy(` tek (`ingest.php:328`) · 9 `CREATE TABLE` |
| 6 | Placeholder | `{{` kalmadı · emoji yok (yalnız `⚠️ VERIFICATION REQUIRED`) |
| 7 | Wiki-link | `[[...]]` formatı, hedefler diskte var |
| 8 | eli10 + eli15 | §3.1-§3.6 + §4.1/§4.4 + §7 maddelerinde `> **eli10 (basit):**` / `> **eli15 (detay):**` bloğu var mı (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 9 | Dokunulmaz | Frozen ADR (001-037) metni kopyalanmadı; mevcut dosyalar değiştirilmedi |
| 10 | Uzunluk | 300–500 satır |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Vault anayasası | [[../.ai/CLAUDE]] | 16 Hard Guardrail (üst otorite) |
| Kök master kurallar | [[../AGENTS]] | §4 keşif · §5 anti-overthink · §7 loop · §8 prompt-maker |
| Klasör envanteri | [[CONTEXT]] | Ölçülmüş sayılar |
| Klasör rolleri | [[AGENTS]] | Dosya → rol |
| Klasör süreç | [[WORKFLOW]] | Adım + kapı |
| Dizin/adlandırma kararı | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Master · ULID · sha256 · gitignore |
| Servis yerleşimi | [[../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture]] | :5000/:6000 |
| Şablon | [[../.ai/.templates/frontend/context-template]] | 4 doküman ortak iskeleti + §4.1 guardrail |
| Şema | `config/media.schema.json` | 6 kural + `additionalProperties=false` |
| Taksonomi | `config/taxonomy.json` | 17 anahtar / 98 değer (kapalı) |
| Denetim kuralı | `docs/checklist.md` | "Kanıtsız `- [x]` yok" |
| Hata kurtarma | `../.ai/.rules/error-recovery.md` | §4.4 kapısı |
| Audit trail | `../.ai/log.md` | append-only kayıt |

**eli10/eli15 blokları (§7):**

> **eli10 (basit):** Kuralın kaynağına giden bağlantılar — kuralı aramaya gerek kalmasın diye.
> **eli15 (detay):** Referans tablosu ayrıdır çünkü her bağlantı ayrı bir otoriteyi gösterir (anayasa · karar · şema · denetim). Okunması üst kuralın nerede olduğunu gösterir; çelişkide `.ai/CLAUDE.md` kazanır. Güncellenmezse kırık link doğar ve sonraki okuyucu yanlış kaynağa gider.
| DB şeması | `../.ai/.sql/mysql/media_catalog.sql` | 9 tablo (BCNF, `utf8mb4_tr_0900_ai_ci`) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** claude
