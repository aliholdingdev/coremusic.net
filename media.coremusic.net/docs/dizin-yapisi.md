# Medya Arşivi — Dizin Yapısı

> **Kapsam:** `C:\www\coremusic.net\media.coremusic.net\` · **Tarih:** 2026-09-29 · **Görev:** F1.3
> **Yetki (SSOT):** ADR-092 `media-dizin-ekseni-ve-ulid` · **Yerleşim:** ADR-039 · **Şart:** VISION §6
> **Kardeş dosya:** [adlandirma.md](./adlandirma.md)

## İçindekiler

1. [Amaç ve yetki](#1-amaç-ve-yetki)
2. [Dizin ağacı](#2-dizin-ağacı)
3. [Dizin sorumlulukları](#3-dizin-sorumlulukları)
4. [Format tablosu](#4-format-tablosu)
5. [Master kuralı](#5-master-kuralı)
6. [Ölçek ve büyüme kuralları](#6-ölçek-ve-büyüme-kuralları)
7. [Yaşam döngüsü](#7-yaşam-döngüsü)
8. [Mevcut durum](#8-mevcut-durum)
9. [Araçlar (faz 2)](#9-araçlar-faz-2)

---

## 1. Amaç ve yetki

Bu doküman medya arşivinin **fiziksel disk yerleşimini** tanımlar. Üç kaynaktan güç alır:

| Kaynak | Ne verir | Durum |
|---|---|---|
| **ADR-092** — `.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid.md` | **Karar kaynağı:** disk ekseni, iki kök, ULID, yan-JSON meta, `derived\` yok | ✅ ACCEPTED (2026-09-29) — diskte okunarak doğrulandı |
| **ADR-039** — servis yerleşimi | `media.coremusic.net` **:5000 / :6000**, **PHP 8.4 + FFmpeg**, durum **PLANNED** | ADR-092 §1.1/§5.1 üzerinden atıfla doğrulandı |
| **VISION §6** (madde 6) | Şart: merkezi ev/ofis medya arşivi + streaming | ADR-092 §1.1 üzerinden doğrulandı |

### 1.1 Tek disk ekseni

```
sanatçı  →  albüm  →  parça
```

- **Diskte tek eksen budur.** Başka hiçbir şey disk klasörü olamaz.
- **Tür, kullanım, dönem, ruh hâli = TAG.** Bunlar `meta.json` içindeki alanlardır (`tur[]`, `etiket[]`), klasör adı değildir.
- **Çok-eksen disk = duplike + kaçak.** Aynı parça 3 türe aitse disk bunu fiziksel kopyayla çözer; bu 1M+ ölçekte depolama ve tutarsızlık patlaması demektir (ADR-092 §2.1 madde 1, §2.3).
- Sorgu tarafı (tür/dönem/ruh araması) **tag + indeks** ile karşılanır — disk kopyası üretilmez.

### 1.2 İkincil kurallar (ADR-092 §6.1)

| # | Kural | Değer |
|---|---|---|
| 1 | Slug | **≤ 80 karakter, ASCII** |
| 2 | ULID | `^[0-9A-HJKMNP-TV-Z]{26}$` |
| 3 | `_cesitli` klasör adı | **a-z**, Türkçe karakter YOK |
| 4 | Sanatçı klasörü sayısı **> 50.000** | **baş-harf katmanı** (`a/`, `b/` …) — mevcut ağaca toplu taşıma YOK |
| 5 | sha256 | **yalnız girişte** (inbox) |
| 6 | C: boş alan < 31 GB | disk genişletme incelemesi |

---

## 2. Dizin ağacı

Aşağıdaki ağaç **birebir** hedef yerleşimdir (`media.coremusic.net` kökü):

```
C:\www\coremusic.net\media.coremusic.net\
├─ .gitignore                      ← genişletildi (ağaç altındaki not)
├─ composer.json                   (php ^8.4 · PSR-4 `Media\` → `src/Media\` · composer bu makinede YOK)
├─ bin\                            ← faz 2 CLI: scan.php · audit.php · ingest.php
├─ src\Media\                      ← faz 2 kod: Slugger · Ulid · Taxonomy · Validator · CatalogWriter (.php)
├─ media\                          ← SADECE MEDYA
│  ├─ audio\
│  │  ├─ _cesitli\{a-z}\{koleksiyon-slug}\{01-parca-slug}\
│  │  └─ {sanatci-slug}\
│  │     ├─ artist.json  avatar.jpg
│  │     ├─ _tekli\{1998-parca-slug}\          (self-contained meta)
│  │     └─ {1975-album-slug}\
│  │        ├─ album.json  cover.jpg           ← MASTER kimlik+kapak
│  │        └─ {01-parca-slug}\
│  │           ├─ audio.flac                   ← master (uzantı = orijinal format)
│  │           ├─ audio.mp3                    ← varyant (VARSA, düz durur)
│  │           ├─ cover.jpg
│  │           └─ meta.json
│  ├─ video\
│  │  ├─ _cesitli\{a-z}\{koleksiyon-slug}\{2012-klip-slug}\
│  │  └─ {sanatci-slug}\{2012-klip-slug}\
│  │        video.mp4  poster.jpg  meta.json
│  ├─ _inbox\{YYYY-AA-GG}\                     ← tarih partisyonlu, işlenmemiş
│  └─ _hurda\                                  ← çöp (audio hariç)
├─ config\   (media.schema.json · taxonomy.json · mojibake-fix.json)
├─ catalog\  ← `scan.php` ilk çalıştırmada üretir: catalog.jsonl (türetilmiş, git'e girmez → §3.2)
└─ docs\     (bu dosya + adlandirma.md)
```

> **`.gitignore` (faz 2 — genişletildi):** `media/` · `catalog/` · `reports/` · `vendor/` · `*.log` + **`!src/Media/` negasyonu** (ve `!src/Media/**`). Gerekçe: `media/` **çapasız** idi → `src/Media/` PHP kodunu yutuyordu; **negasyon şart** (aksi hâlde kod repoya girmez).

**Ağaç dışı not:** ADR-092 §2.2 yerleşiminde kod `src\` klasöründedir ve **media dışındadır**. Kod, config, docs ile `bin\`, `src\`, `.gitignore`, `composer.json` `media\` ağacına **girmez** (ADR-092 §2.2, §7.1 kriter 1). Bunlar **proje kök seviyesindedir** (faz 2) — `media\` altı değil; `media\` ağaç bölümü değişmemiştir.

---

## 3. Dizin sorumlulukları

| Dizin | Ne taşır | Ne girmez |
|---|---|---|
| `media\` | **Yalnız medya ağacı** (audio + video + yan meta JSON) | Kod, config, doküman, log, cache, geçici dosya |
| `media\audio\` | Ses kökü: `{sanatci-slug}` ve `_cesitli` | Video, işlenmemiş gelen |
| `media\audio\_cesitli\` | Sanatçıya bağlanamayan derlemeler (koleksiyonlar), **HER ZAMAN** `a-z` bölmeli | Sanatçıya atanabilir dosya |
| `media\audio\{sanatci}\` | `artist.json` + `avatar.jpg` + `_tekli\` + albüm klasörleri | Albüm düzeyinde olmayan parça (o `_tekli\`ye girer) |
| `media\audio\{sanatci}\_tekli\` | Single/dağıtık parça — **self-contained meta** (dış bağımlılık yok) | Albüm içeriği |
| `media\audio\{sanatci}\{album}\` | `album.json` + `cover.jpg` + parça klasörleri | Kodek/transcode çıktısı |
| `media\audio\{sanatci}\{album}\{parca}\` | `audio.{uzantı}` (master) + `audio.mp3` (varyant) + `cover.jpg` + `meta.json` | `derived\`, çözünürlük klasörleri, dönüştürülmüş dosya |
| `media\video\` | Video kökü: klip klasörleri (`video.mp4`, `poster.jpg`, `meta.json`) | Ses dosyası |
| `media\_inbox\{YYYY-AA-GG}\` | Tarih partisyonlu, **işlenmemiş** gelen dosya; sha256 **burada** hesaplanır | Sürekli olarak kalan veri (işlenip çıkarılır) |
| `media\_hurda\` | Çöp / reddedilen — **audio hariç** (ses asla buraya atılmaz) | Sağlam ses ve video |
| `config\` | `media.schema.json`, `taxonomy.json`, `mojibake-fix.json` | Medya dosyası |
| `docs\` | Bu dosya + `adlandirma.md` | Medya, config |
| `bin\` | Faz 2 CLI: `scan.php` (tarama + katalog), `audit.php` (arşiv denetimi), `ingest.php` (dry-run taşma) | Medya, config, docs |
| `src\Media\` | Faz 2 PHP kodu: `Slugger`, `Ulid`, `Taxonomy`, `Validator`, `CatalogWriter` (PSR-4 `Media\`) | Medya, config, docs |
| `catalog\` | `catalog.jsonl` — `scan.php` ile üretilen **türetilmiş** indeks (yeniden üretilebilir, git'e girmez) → §3.2 | Medya, kalıcı SSOT (JSON'dur) |

### 3.1 `derived\` YOKTUR

- ADR-092 §2.1 madde 5 + §3 alternatif 4: **`derived\` klasörü yoktur.**
- FFmpeg çıktısı (transcode, HLS parçası, önizleme) **uygulama `cache\`**'indedir ve bu **medyanın dışındadır**; faz 2'de kurulur, silinip yeniden üretilebilir.
- **`derived` yerine ne?** Varyant dosyalar **düz durur**: `audio.mp3` (ses varyantı), `video-720p.mp4` (video varyantı) — hepsi parça klasörünün içinde, alt klasör yok.
- Gerekçe: arşiv = **orijinal + kapak + meta**; üretim sonucu arşivde tutulursa "orijinal mi?" belirsizleşir (ADR-092 §2.3).

### 3.2 `catalog\` ve SQL şeması (faz 2)

- **`catalog\catalog.jsonl`** — `bin\scan.php` üretir (klasör yoksa ilk yazımda açılır). **Yeniden üretilebilir türetilmiş indeks**; git'e girmez.
- **Kalıcı sorgu katmanı:** `.ai/.sql/mysql/media_catalog.sql` → DB **`media_catalog`**, **BCNF** (ADR-040), **`utf8mb4_tr_0900_ai_ci`** (Türkçe sıralama zorunlu), **9 tablo** (`artist`, `album`, `koleksiyon`, `asset`, `variant`, `taxonomy`, `asset_tag`, `path_history`, `ingest_batch`) + **`v_asset_search` FULLTEXT view**.
- **DB türetilmiş indekstir — JSON hâlâ SSOT** (`meta.json` + yan JSON); `scan.php --rebuild` ile yeniden üretilir. DB çökse arşiv yaşar.
- **Taxonomy:** 98 seed (17 anahtar) + `asset_tag` → `taxonomy` **FK** ile drift **imkânsız**.
- **⚠️ KARIŞTIRMA YOK:** `coremusic_media` ana uygulamanın (cihaz senkron) şemasıdır, **arşive ait değildir**.

---

## 4. Format tablosu

| Tip | Kabul edilen uzantılar | Dosya adı | Not |
|---|---|---|---|
| **Ses** | `.mp3` `.flac` `.wav` `.m4a` `.ogg` `.wma` | `audio.{orijinal-uzantı}` | **Uzantı ASLA değişmez** — kaynak neyse o kalır |
| **Video** | `.mp4` `.mkv` `.avi` `.webm` | `video.{uzantı}` | Aynı kural: uzantı korunur |
| **Görsel** | `.jpg` `.png` `.webp` | `cover.jpg` (albüm/parça), `poster.jpg` (klip), `avatar.jpg` (sanatçı) | **Master kapak** — albüm klasöründe `cover.jpg` |
| **Playlist** | `.m3u` `.pls` | dosya adına dönüşmez | **Parse edilir** → meta içinde **koleksiyon tanımı** olur; kendisi **rapora** girer |

Kabul edilmeyen uzantılar ve tanınmayan tipler → `_hurda\` (ses hariç) veya rapor.

---

## 5. Master kuralı

**Kayıpsız = master = dokunulmaz.**

| Sınıf | Formatlar | Muamele |
|---|---|---|
| **Master (kayıpsız)** | `.flac`, `.wav` | Dokunulmaz · dönüştürülmez · silinmez |
| **Varyant (kayıplı)** | `.mp3`, `.wma`, `.ogg` (aynı parça için) | Aynı parça klasöründe düz durur; meta'da `varyantlar[]` içinde listelenir |

- Master dosya adı: `audio.flac` (ör.). Yanına varyant `audio.mp3` aynı klasörde **düz** durur.
- Master dosyasının **byte'ı değişmez**; gömülü (embedded) etiket yazımı yapılmaz — meta **yan JSON**'da yaşar (ADR-092 §3 alternatif 5).
- **Meta alanları:** `format` · `bitrate` · `kanal` · `orneklem` · `cozunurluk` · `fps`
  - Ses için: `format`, `bitrate`, `kanal`, `orneklem`
  - Görsel/video için: `cozunurluk`, `fps`
  - Alanların kesin şeması `config\media.schema.json` ile sabitlenir → **diskte mevcut ✓ (2026-09-29)**.

---

## 6. Ölçek ve büyüme kuralları

| Kural | Eşik |
|---|---|
| Sanatçı klasörü | **> 50.000** → baş-harf katmanı (`a/`, `b/` …) eklenir; mevcut ağaca toplu taşıma YOK |
| `_cesitli` | **HER ZAMAN** `a-z` bölmeli |
| Albüm | **> 60 parça** → `-cd1` / `-cd2` eki |
| Tarama | **günlük incremental** (mtime, size); **tam reconcile haftalık gece** |
| sha256 | **YALNIZ girişte** (inbox); sonraki aşamada yeniden hash YOK |
| Scrub | **aylık %5 örneklem**, **yıllık tam** |
| Toplu iş | **CLI** (robocopy / PHP) — **Explorer YASAK** (1M dosya ölçeği) |
| Yedek | **3-2-1** + **günlük `catalog.jsonl` export** |
| Yol | slug **≤ 80** → tipik yol ≈ **166 karakter** < `MAX_PATH` **260** |

> **Yol hesabı (F1.5 — 2026-09-29, gerçek yol uzunluklarıyla yeniden türetildi):**
> **Taban = 53 krk:** `C:\www\coremusic.net\media.coremusic.net\` = `C:\www\` **7** + `coremusic.net\` **14** + `media.coremusic.net\` **20** = **41 krk** · `media\` **6** · `audio\` **6** → **41 + 6 + 6 = 53 krk**. (eski kök `C:\www\` + `media.coremusic.net\` = **27 krk** → taban **14 krk** uzadı: **~40 → ~53**)
> **Sabit gider = 13 krk:** 3 ayraç `\` = **3** + en uzun dosya adı `audio.flac` = **10**.
> **260 payı:** 260 − 53 − 13 = **194 krk** → **sanatçı + albüm + parça slug'larının toplamı ≤ 194** ise yol 260 altında kalır.
> **Doğrulama (tipik profil 30/30/40):** 53 + 30 + 1 + 30 + 1 + 40 + 1 + 10 = **166 krk < 260 ✓** · `_tekli` (en kötü 80/80): 53 + 80 + 1 + 7 + 80 + 1 + 10 = **232 ✓** · `_cesitli` (en kötü): 53 + 9 + 1 + 1 + 80 + 1 + 80 + 1 + 10 = **236 ✓**.
> **Sınır:** slug'ın **tek tek** 80'de tutulması tek başına yeterli değildir — üç slug da 80 ise 53 + 80 + 1 + 80 + 1 + 80 + 1 + 10 = **306 krk > 260 ✗**. Bağlayıcı kural **üç slug'ın toplamıdır (≤ 194)**; ADR-092 §6.1 kural 1 (slug ≤ 80) bunun ön koşuludur, tek başına garantisi değildir.

---

## 7. Yaşam döngüsü

Her varlığın `meta.json` içindeki **`durum`** alanı `config\taxonomy.json` tanımlı değerlerden birini alır:

```
inbox  →  aktif  →  sakli  →  tekrar  →  arsiv
```

| Durum | Anlamı |
|---|---|
| `inbox` | `media\_inbox\{YYYY-AA-GG}\` içinde, işlenmedi |
| `aktif` | Yerleşik, arşivin etkin parçası |
| `sakli` | Etkin değil ama silinmedi (arşivde kalır) |
| `tekrar` | Yeniden değerlendirme bekliyor |
| `arsiv` | Soğuk katman; erişim nadir |

- Durum **tag'tir, disk konumu değil** — dosya taşınmaz; durum JSON'da değişir.
- Geçiş kuralları ve kapalı set: `../config/taxonomy.json` → **diskte mevcut ✓**.

---

## 8. Mevcut durum

**2026-09-29 — F1.1 iskeleti:**

- **62 dizin / 0 dosya.** Dizin iskeleti kuruldu (F1.1).
- `docs\` içine bu dosya ve `adlandirma.md` yazıldı (F1.3).
- `media\audio\{sanatci}`, `media\audio\{albüm}`, `media\audio\{parça}` **seviyeleri oluşmadı** — bunlar **faz 2 (ingest)** ile dosya gelince oluşur.
- `config\` içindeki `media.schema.json` (26.856 B), `taxonomy.json` (1.488 B), `mojibake-fix.json` (2.103 B) — **üçü de diskte ✓ (2026-09-29, F1.2)**.
- Kaynak koleksiyon (`C:\Users\...\Music`, 7.551 dosya / 38,33 GB) **taşınmadı** — bu faz yalnız iskelet + config + docs (ADR-092 §1.4 "TAŞIMA YOK").

**Sayaç notu:** **62 dizin / 0 dosya — 2026-09-29 bağımsız denetim (F1.5) ile doğrulandı** (ilk sayım: F1.1 raporu · kontrol komutu: `Get-ChildItem -Directory -Recurse | Measure-Object`).

> **Faz 2 eklentisi (2026-09-29):** proje köküne `bin\`, `src\Media\`, `composer.json`, `.gitignore` eklendi — **62/0 sayımı bu eklemeden önceki denetime aittir, yeniden sayım YAPILMADI.**

---

## 9. Araçlar (faz 2)

| Araç | Ne yapar |
|---|---|
| `bin\scan.php` | `media\` → **`catalog\catalog.jsonl`** (+ MySQL `media_catalog`). `--rebuild` = JSONL'i ve MySQL varlık tablolarını boşaltıp yeniden kurar · `--dry-run` = yalnızca yazılacakları stdout'a basar. **Mod (ADR-092 §6.1 k.5):** varsayılan **hafif** — `teknik.sha256` yan-JSON'dan okunur, **yeniden hash yok**; JSON'da yoksa `sha256: null` + özet `HASH OLMAYAN: N`. `--deep` = gerçek `hash_file()` + JSON değeriyle karşılaştırma, fark → **HASH-FARK** (HATA); özet `MOD: hafif` / `MOD: deep`. MySQL **varsa** PDO ile `media_catalog`'e de yazar, **yoksa** tek uyarı + JSONL ile devam eder. |
| `bin\audit.php` | Arşiv lint'i (ADR-092 **§6.1/§7.1**). **Varsayılan hafif mod: yeniden hash YOK** (ADR-092 §6.1 kural 5 — sha256 yalnız girişte). `--deep` = gerçek sha256 karşılaştırması; JSON `teknik.sha256` ile fark → **HASH-FARK** (HATA). Salt okunur, `media\`'ya hiçbir şey yazmaz. |
| `bin\ingest.php` | Kaynak → arşiv **taşma**; rapor `reports\ingest-YYYYMMDD-HHMM.csv`. Varsayılan **dry-run**: `--commit` **yokken tek bayt kopyalamaz** (`ingest.php:291` kilidi) · tek kopya noktası `ingest.php:328` (`copy()`). `--commit` ayrıca STDIN'den `evet` onayı ister. |

> **⚠️ PHP 8.4 bu makinede yok → `php -l` / çalışma testi YAPILMADI (faz 2 ortamı).** Üçü de bu uyarıyı taşır; `composer` da bu makinede YOK (`composer.json` yazıldı, `vendor\` hiç oluşmadı).

---

**Authority:** ADR-092 Karar Metni (SSOT) · **Son güncelleme:** 2026-09-29
