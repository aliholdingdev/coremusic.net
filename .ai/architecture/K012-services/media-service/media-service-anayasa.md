---
node_id: K012-services/media-service
node_type: module
role: anayasa
status: draft
parent_id: K012-services/media-service
children: []
depends_on:
  - "media-service/index.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — media.coremusic.net/bin/scan.php 261 satır (docblock:4–30)"
  - "S1 — media.coremusic.net/bin/ingest.php 346 satır (docblock:4–24, exit:2)"
  - "S1 — media.coremusic.net/bin/audit.php 499 satır (docblock:4–28, salt okunur)"
  - "S1 — media.coremusic.net/src/Media/Validator.php 431 satır (class:22)"
  - "S1 — media.coremusic.net/src/Media/CatalogWriter.php 491 satır (class:24)"
  - "S1 — media.coremusic.net/src/Media/Taxonomy.php 104 satır (class:11)"
  - "S1 — media.coremusic.net/src/Media/Ulid.php 84 satır (class:13)"
  - "S1 — media.coremusic.net/src/Media/Slugger.php 140 satır (class:14)"
  - "media.coremusic.net/AGENTS.md §4 kuralları (salt okunur, dry-run)"
validation:
  - "§3–§10 kod kanıtı 2026-10-13 read — birebir"
  - "§12 sınır services-master §21.2 ile hizalı"
  - "§11 sınır index FM open_questions ile hizalı"
open_questions:
  - "playback-service ile sınırı → ⚠️ VERIFICATION REQUIRED (index FM)"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "media.coremusic.net CLI kodu ile K012/media-service eşlemesi → ⚠️ VERIFICATION REQUIRED (servis kartında repo kanıtı ⚠️ idi)"
  - "PHP çalışma zamanı bu ortamda YOK → php -l/8.4 doğrulaması yapılamadı (kod docblock'u, §9)"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + media.coremusic.net/ (S1, 2026-10-13 read)"
group: mimari
---

# media-service — Anayasa (Constitution)

> **Ön not:** media-service, **medya varlığına dair işleri** yürüten K012
> servisidir (master §21.2: "Medya varlığı servisi — dosya/kaynak varlığına
> dair işler"). Dosyanın sahibi K015'tir; servis yalnız iş yapar (master §21.2
> sınır satırı). Repo'daki en zengin kanıt `media.coremusic.net/` altındaki
> PHP CLI arşiv aracıdır (3 komut + 5 sınıf). Bu CLI'nin K012/media-service'e
> birebir karşılığı ayrı bir sorudur ve açık soru olarak taşınır (§11 Q3).

## §1 ELI15 — Bu Servis Ne Yapar?

Medya ağacıyla ilgili **işleri** yapar: tarama (scan), taşıma (ingest),
denetleme (audit), katalog yazımı, şema doğrulama, taksonomi denetimi,
ULID/slug üretimi. Dosyanın kendisini saklamaz (K015), kuralı kendisi
yazmaz (K008-domain iddiası), arayüzü çizmez (K011). Çıktıları: yeniden
üretilebilir `catalog/catalog.jsonl` indeksi + opsiyonel MySQL
`media_catalog` + rapor CSV + "SEVIYE | yol | kural | neden" denetim çıktısı.

## §2 Sınır Tablosu

| Konu | Kapsam | Sahibi | Kanıt |
|---|---|---|---|
| Medya dosyası saklama/akış | DIŞINDA | K015-media | index sınır satırı |
| İş kuralı (domain) | DIŞINDA (iddia) | K008-domain | index sınır satırı |
| Tarama/taşıma/denetim iş akışı | İÇİNDE | K012/media-service (CLI kanıtı) | S1 bin/scan·ingest·audit |
| Şema doğrulama (sınırlı) | İÇİNDE (repo) | media.coremusic.net Validator | S1 Validator:22 |
| Katalog indeksi (JSONL) | İÇİNDE (repo) | CatalogWriter | S1 CatalogWriter:24 |
| Kapalı taksonomi okuma | İÇİNDE (repo) | Taxonomy | S1 Taxonomy:11 |
| ULID / slug üretimi | İÇİNDE (repo) | Ulid, Slugger | S1 |
| DB şeması (media_catalog) | DIŞINDA | K007-data | media AGENTS.md §3.1 #2 |
| medya ağacı yazma | YASAK | kimsede değil (salt okunur) | media AGENTS.md §4 kural 2 |
| playback sahipliği | SINIR → açık soru | — | §11 Q1 ⚠️ |

## §3 Üç Komut — Envanter

| # | Komut | Satır | Çıktı / Dönüş | Docblock kuralı |
|---|---|---|---|---|
| 1 | `bin/scan.php` | 261 | `catalog/catalog.jsonl` + opsiyonel MySQL | exit 0/1/2; `--dry-run` `--rebuild` `--deep` `-v` |
| 2 | `bin/ingest.php` | 346 | rapor CSV (`reports/ingest-YYYYMMDD-HHMM.csv`) | `--commit` yokken **tek bayt yazma yok** |
| 3 | `bin/audit.php` | 499 | `SEVIYE | yol | kural | neden` + TOPLAM | **salt okunur**; exit 0/1/2 |

**Ortak iskelet (üç dosya birebir):** `#!/usr/bin/env php` (satır 1) ·
`declare(strict_types=1)` · `spl_autoload_register` ile `Media\` öneki →
`src/Media/*.php` (scan:22–33, ingest:26–37, audit:26–37) · kök denetimi
(`config/mojibake-fix.json` ingest:46–49, `config/media.schema.json`
audit:47–50) → `exit(2)` temiz hata çıkışı.

**Önemli kod içi uyarı (üç komutun docblock'unda birebir):** "PHP çalışma
zamani bu ortamda YOKTUR — php -l + örnek veri ile 8.4 ortamında
doğrulanmalıdır (VERIFICATION REQUIRED)."

## §4 Beş Sınıf — Envanter

Dosya: `media.coremusic.net/src/Media/` (glob 2026-10-13)

| # | Sınıf | Satır | Public/static üye özeti |
|---|---|---|---|
| 1 | `Validator` | 431 | `fromProject` · `tipAnahtari` · `validate` · `dogrula` · `enumDrift` · `driftBul` + private yardımcılar |
| 2 | `CatalogWriter` | 491 | `fromProject` · `yol` · `dryRun` · `ekle` · `kayitlar` · `satirSayisi` · `yaz` · `hashSetiOku` · `mysqlYaz` · `upsertSql` + DB yardımcıları |
| 3 | `Taxonomy` | 104 | `fromProject` · `values` · `isValid` · `has` · `keys` · `yuklemeHatasi` |
| 4 | `Ulid` | 84 | `ALFABE` · `DESEN` · `UZUNLUK` · `uret` · `isValid` + private `zamanKodla`/`rastgeleKodla` |
| 5 | `Slugger` | 140 | `MAKS` · `YEDEK` · `fold` · `slug` · `yasakliMi` · `siraPrefix` + private `kirp` |

**Bağımlılık kuralı (Validator docblock birebir):** "sınırlı doğrulayıcı
(bağımlılık YOK, paket kullanılmaz)" — Composer paketi yok, `fromProject`
proje kökünden okur (Validator:39–44).

## §5 Validator — 6 Kural (Docblock Birebir)

Dosya: `media.coremusic.net/src/Media/Validator.php` / Satır: 6–17

| # | Kural | Kapsam |
|---|---|---|
| 1 | `required` | zorunlu alan |
| 2 | `pattern` (id/slug/sha256/tarih) | biçim |
| 3 | `enum` + taxonomy senkronu | kapalı liste |
| 4 | `type` | tip |
| 5 | `minimum`/`maximum`/`exclusiveMinimum` (+ `minLength`/`maxLength`) | sayısal/metin sınırı |
| 6 | `additionalProperties:false` ihlali (`ek` hariç) | fazla alan |

**Ek (birebir):** şemada fiilen kullanılan `oneOf`/`allOf`/`const`/`items`/
`if-then`/`not` küçük ölçekli işlenir; "Tam JSON-Schema motoru YAZILMAZ
(spec)"; çıktı hata listesi `["yol => neden"]` — "exception yerine liste
(kalite kuralı)". `REF_ONEK = '#/$defs/'` (satır 24).

## §6 CatalogWriter — SSOT ve Opsiyonel DB (Docblock Birebir)

Dosya: `media.coremusic.net/src/Media/CatalogWriter.php` / Satır: 6–22

| İddia (birebir) | Ayrıntı |
|---|---|
| JSONL = "yeniden üretilebilir türetilmiş indeks" | SSOT yan JSON'lardır; her zaman `bin\scan.php` ile baştan üretilir |
| "DB çökse arşiv yaşar" | MySQL opsiyonel — `pdo_mysql` yoksa/bağlanılamazsa tek uyarı + JSONL devam |
| Kayıt şeması | tip · id · slug · yollar[] · baslik · sanatci · album · yil · tag · durum · sha256 |
| Bayraklar | `JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE` (satır 27) |
| Hash deseni | `^[a-f0-9]{64}$` (satır 31) |

## §7 sha256 Kuralı — Girişte Üretim (Scan Docblock Birebir)

Dosya: `media.coremusic.net/bin/scan.php` / Satır: 14–19

| Mod | Davranış |
|---|---|
| hafif (varsayılan) | yeniden hash YOK — satır değeri JSON'dan okunur; yoksa `sha256: null` + HASH OLMAYAN sayacı |
| `--deep` | yeniden hash + `HASH-FARK` karşılaştırması (fark → HATA) |

Kural kimliği: ADR-092 §6.1 madde 5 — "sha256 yalnız GİRİŞTE üretilir"
(scan:14). İlgili denetim kimliği: `sha256-format`, `sha256-tekrar(UYARI)`,
`HASH-FARK(--deep)` (audit:20–21).

## §8 ULID ve Slug Kuralları (Kod Birebir)

Dosya: `Ulid.php` / Satır: 15–22 · `Slugger.php` / Satır: 16–42

| Kural | Değer | Satır |
|---|---|---|
| ULID alfabe | `0123456789ABCDEFGHJKMNPQRSTVWXYZ` (I, L, O, U yok) | Ulid:16 |
| ULID desen | `^[0-9A-HJKMNP-TV-Z]{26}$` | Ulid:19 |
| ULID uzunluk | 26 (ilk 10 = 48-bit ms zaman, son 16 = rastgele) | Ulid:11, 22 |
| Slug maks | 80 | Slugger:17 |
| Slug yedek | `isimsiz` | Slugger:20 |
| Fold | 12 eşleme (ş/ı/ğ/ü/ö/ç + büyükleri) | Slugger:23–30 |
| Windows-yasaklı | con, aux, prn, nul, com1–9, lpt1–9 (+ `-one`) | Slugger:33–37, 71–73 |
| Sıra öneki | `01`…`99` (`%02d-`) | Slugger:40–42, 120–130 |
| Çakışma | `-2`, `-3` (case-insensitive) | Slugger:84–93 |

## §9 Güvenlik ve Kapı Kuralları (media AGENTS.md §4 — Vault Kural Satırı)

| # | Kural | Kaynak |
|---|---|---|
| 1 | `media/` ağacı herkes için salt okunur; yazma yalnız `ingest.php` üzerinden ve onayla | media AGENTS.md kural 2 |
| 2 | `--commit` yokken tek bayt yazma yok; dry-run rapor CSV | ingest docblock:8–13 |
| 3 | Hash kuralı sha256 yalnız girişte | scan docblock + ADR-092 §6.1 m.5 |
| 4 | Vault ↔ disk çelişkisi → disk kazanır + ⚠️ | media AGENTS.md kural 5 |
| 5 | PHP runtime bu ortamda YOK → 8.4 doğrulaması | üç komut docblock'u ⚠️ |

**Kural düzeyi vs kod düzeyi ayrımı:** bu bölümdeki maddelerin çoğu
**kural/vault** kaynağındandır; kod tarafı §3–§8'de ayrıca kanıtlanır.
Kural maddeleri kod kanıtı olarak sunulmaz.

## §10 Test/Doğrulama Durumu (Ölçüm)

| Yüzey | Ölçüm (2026-10-13) |
|---|---|
| `media.coremusic.net/tests/` | YOK (media AGENTS.md §1: "diskte `tests/` = false") |
| PHP runtime | YOK (docblock uyarısı) → `php -l` 8/8 iddiası bu ortamda koşulamaz |
| Kapılar | `docs/checklist.md` + `--dry-run`/`--deep` CLI kapıları (media AGENTS.md §3.1 #4) |

## §11 Açık Sorular

| # | Soru | Kaynak | Durum |
|---|---|---|---|
| Q1 | playback-service ile sınır (kime ait?) | index FM | ⚠️ VERIFICATION REQUIRED |
| Q2 | Kademe-3 bounded-context kapsamı | index FM (faz C) | ⚠️ VERIFICATION REQUIRED |
| Q3 | `media.coremusic.net/` CLI = K012/media-service mi? | master §21.2 repo kanıtı ⚠️ | ⚠️ VERIFICATION REQUIRED |
| Q4 | K015'e görünüm sözleşmesi biçimi | bagimliliklar §8 satır 82 | ⚠️ UNKNOWN |

Hiçbiri bu dosyada uydurularak kapatılmaz.

## §12 Sınır Kararları (Bağlayıcı)

| # | Karar | Gerekçe |
|---|---|---|
| 1 | Dosya saklama/akış K015'te kalır | master §21.2, index sınır satırı |
| 2 | `media/` yazma yasağı bozulmaz | ADR-092 (kural) + ingest onay kapısı |
| 3 | DB işi K007 veri sahipliğinde; katalog onun **türetilmiş** indeksidir | CatalogWriter docblock |
| 4 | JSON-Schema tam motoru yazılmaz | Validator docblock (spec) |
| 5 | CLI ↔ servis eşlemesi kanıtlanmadan "bu servis" denmez | §11 Q3 |

## §13 Riskler

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | PHP runtime yok — kod hiç koşulmadı | sürpriz hat | açık (§10) |
| 2 | DB çökmesi | JSONL hayatta kalır (tasarım) | mitigasyon kodda (§6) |
| 3 | Hash drift (`--deep` dışındaki modlar) | tespit edilemez | hafif modun bilinçli sınırı (§7) |
| 4 | playback sahipliği çakışması (Q1) | iki gerçeklik riski | ⚠️ (K012 master §21.3 Q2 ile ortak) |
| 5 | CLI ↔ servis eşlemesi belirsiz (Q3) | yanlış envanter | ⚠️ |

## §14 Doğrulama Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 3 komut satır/exit/docblock birebir | GEÇTİ (§3) |
| 2 | 5 sınıf satır ölçümü | GEÇTİ (§4) |
| 3 | Validator 6 kural | GEÇTİ (§5) |
| 4 | ULID/slug sabitleri | GEÇTİ (§8) |
| 5 | Açık sorular işaretsiz kalmadı | GEÇTİ (§11 ⚠️) |
| 6 | K015 sınırı korundu | GEÇTİ (§2, §12) |

## §15 İddia Kayıt Defteri

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Bu dosyada yeni URL'li dış iddia üretilmedi | — | 2026-10-13 | — |
| Tüm kod iddiaları yerel dosya satır referanslıdır | S1 read | 2026-10-13 | kanıt |

## §16 Örnek/Referans Ayrımı

| Öğe | Nerede |
|---|---|
| Birebir kod blokları (Validator/CatalogWriter/Ulid/Slugger/CLI) | ornekler dosyası |
| Çağrı/üretim akışları (scan → JSONL → MySQL) | surucu dosyası |
| Klasör künyesi + çocuklar | index.md |

## §17 Bağımlılık Özeti

| Bağımlılık | Tür | Kanıt |
|---|---|---|
| PHP 8.x çalışma zamanı | ortam | ⚠️ yok (§10) |
| `pdo_mysql` | opsiyonel eklenti | CatalogWriter docblock |
| config/media.schema.json · taxonomy.json · mojibake-fix.json | veri sözleşmesi | üç komut kök denetimi |
| docs/adlandirma.md · dizin-yapisi.md | kural kaynağı | audit docblock:17–19 |
| ADR-092 | karar | kod docblock'ları |
| K007 (DB) · K015 (medya) · K008 (kural) | K-düğümü | master §21.2 |

## §18 Özet Sayılar

| Sayaç | Değer |
|---|---|
| CLI komutu | 3 (scan 261 · ingest 346 · audit 499) |
| Sınıf | 5 (431+491+104+84+140 = 1250 satır) |
| config dosyası | 3 (taxonomy, media.schema, mojibake-fix) |
| docs dosyası | 5 (checklist, todos, dizin-yapisi, adlandirma, faz3-gui-spec) |
| Test dizini | 0 |
| Açık soru | 4 (§11) |

## §19 Komut-Bağımızılık Matrisi

Her komut hangi sınıfları `use` eder (docblock altındaki `use` satırları, S1 2026-10-13):

| Komut | `use` edilen sınıflar | Açıklama |
|---|---|---|
| `bin/scan.php` | `Media\CatalogWriter` · `Media\Ulid` · `Media\Validator` | tarama + doğrulama + katalog |
| `bin/ingest.php` | `Media\CatalogWriter` · `Media\Slugger` · `Media\Ulid` | taşıma + adlandırma + kimlik |
| `bin/audit.php` | `Media\Ulid` · `Media\Validator` | salt okunur denetim |
| `Validator.php` | `Media\Taxonomy` | enum senkronu (constructor:36, `fromProject:42`) |
| `CatalogWriter.php` | — (PDO yalnız `mysqlYaz` içinde) | bağımlılık opsiyonel |
| `Taxonomy.php` · `Ulid.php` · `Slugger.php` | — | yaprak sınıflar |

**Gözlem:** `Slugger` yalnız ingest'te kullanılır — üretim (tarama) yolunda
slug zaten yan-JSON'lardan okunur. `Taxonomy` tek tüketiciyi `Validator`'dır.

## §20 Kök Denetimi ve Çıkış Kodları

| Komut | Kök denetimi | Dosya | Hata çıkışı |
|---|---|---|---|
| scan | `realpath` + kök kontrolü | config (scan docblock kullanım satırı) | exit 2 |
| ingest | `config/mojibake-fix.json` yoksa HATA | ingest:46–49 | exit 2 |
| audit | `config/media.schema.json` yoksa HATA | audit:47–50 | exit 2 |

| Çıkış kodu | Anlamı (üç komut ortak, docblock birebir) |
|---|---|
| 0 | temiz |
| 1 | hata bulundu |
| 2 | kullanım/kök/kaynak hatası |

## §21 audit Kural Kimlikleri (Docblock Birebir — 22 kimlik)

Dosya: `media.coremusic.net/bin/audit.php` / Satır: 20–26

| # | Kimlik | # | Kimlik |
|---|---|---|---|
| 1 | `json` | 12 | `dosya-desen` |
| 2 | `sema` | 13 | `dosya-var` |
| 3 | `addl-props` | 14 | `varyant-var(UYARI)` |
| 4 | `slug-regex` | 15 | `tag-enum` |
| 5 | `slug-max80` | 16 | `gorsel-var` |
| 6 | `slug-klasor` | 17 | `gorsel-yok(UYARI)` |
| 7 | `sira-on-ek(UYARI)` | 18 | `mojibake` |
| 8 | `ulid-regex` | 19 | `hurda-aday(UYARI)` |
| 9 | `ulid-teklik` | 20 | `yol-260` |
| 10 | `sha256-format` | 21 | `cesitli-az(UYARI)` |
| 11 | `sha256-tekrar(UYARI)` | 22 | `HASH-FARK(--deep)` |

**Çıktı biçimi (birebir):** `"SEVIYE | yol | kural | neden"` + `TOPLAM` özeti.
**SSOT listesi (birebir):** `config/media.schema.json` (v2) ·
`config/taxonomy.json` · `config/mojibake-fix.json` · `docs/adlandirma.md`
(6 kural) · `docs/dizin-yapisi.md` (ağaç + format §4) · ADR-092 §6.1/§7.1.

## §22 scan Bayrakları ve Üç Aşama

Dosya: `media.coremusic.net/bin/scan.php` / Satır: 11–19 (docblock)

| Bayrak | Davranış |
|---|---|
| `--dry-run` | JSONL'e ve MySQL'e yazar; yalnızca yazılacakları stdout basar |
| `--rebuild` | mevcut `catalog.jsonl` siler + MySQL varlık tablolarını boşaltır |
| `--deep` | medya dosyasının sha256'sını yeniden hesaplar + `teknik.sha256` ile karşılaştırır (fark → HASH-FARK, HATA) |
| `-v` | ayrıntılı çıktı |

**Üç aşama (docblock birebir numaralandırma):**
1) `catalog/catalog.jsonl` (yeniden üretilebilir SSOT indeksi) ·
2) MySQL `media_catalog` (opsiyonel; yoksa tek uyarı + JSONL ile devam) ·
3) yan-JSON doğrulama (`Validator`).

## §23 ingest Güvenlik Akışı (Docblock Birebir)

Dosya: `media.coremusic.net/bin/ingest.php` / Satır: 6–24

| Adım | Kural |
|---|---|
| 1 | `--commit` YOKKEN medya ağacına **tek bayt bile** kopyalama/yazma/silme YOK |
| 2 | Dry-run'da yalnızca rapor yazılır: `reports/ingest-YYYYMMDD-HHMM.csv` (UTF-8 BOM, virgül, tüm alanlar tırnaklı) |
| 3 | `--source` yoksa/geçmezse exit 2 (temiz hata çıkışı, crash yok) |
| 4 | `--commit` verilirse kopyalama önceden STDIN'den `evet` onayı ister |
| 5 | Kaynak kök denetimi: `config/mojibake-fix.json` yoksa HATA + exit 2 |

ADR-092 §8 madde 11: "dry-run zorunlu" (docblock:7).

## §24 Dosya → Kural Eşlemesi

| Dosya | Kural kaynağı |
|---|---|
| `docs/adlandirma.md` §1–§3 | Slugger FOLD (12 eşleme), sıra öneki (01…99), Windows-yasaklı |
| `docs/dizin-yapisi.md` | audit `dosya-desen`, ağacın format §4 |
| `config/media.schema.json` | Validator 6 kural + `$defs` referansları |
| `config/taxonomy.json` | Taxonomy (17 anahtar) + Validator enum senkronu |
| `config/mojibake-fix.json` | audit `mojibake` kuralı + ingest kök denetimi |
| ADR-092 | ULID (§6.1 m.2), sha256 (§6.1 m.5), dry-run (§8 m.11), JSONL (§5.2 adım 6) |

## §25 Servis Kartı Uyumu (master §21.2)

| Alan | master değeri | Bu dosyadaki karşılığı |
|---|---|---|
| Rol (ELI15) | Medya varlığı servisi | §1 |
| Kapsam | dosya/kaynak varlığına dair işler (ayrıntı ⚠️) | §3–§8 (ayrıntı ölçüldü) |
| Giden bağımlılık | K007, K015 | §17 |
| Sınır | dosyanın sahibi K015'tir — servis yalnız iş yapar | §2, §12 |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED | §11 Q3 — CLI eşlemesi hâlâ ⚠️ |

Bu dosya kartın "ayrıntı ⚠️" boşluğunu **kod ölçümüyle** doldurur; kartın
kendisi değiştirilmez (append-only).

## §26 K015 İlişkisi (İki Gerçeklik Riski)

| İddia | Kaynak | Durum |
|---|---|---|
| Dosyanın sahibi K015 | master §21.2 | kural |
| "Medya → servis ↔ kitaplık/akış → K015-media → görünüm sözleşmesi UNKNOWN" | bagimliliklar §8 satır 82 | ⚠️ UNKNOWN |
| "Kitaplık/metadata/akış verisi → veri okuma → iki gerçeklik riski (playback sahipliği)" | bagimliliklar satır 63 | risk kaydı |
| JSONL/DB vs K015 kitaplık verisi — hangisi gerçek? | — | ⚠️ VERIFICATION REQUIRED (Q4) |

## §27 playback Sınırı (Q1 — Taşıma)

| Kaynak | İfade |
|---|---|
| index FM | "playback-service ile sınırı → ⚠️ VERIFICATION REQUIRED" |
| master §21.3 | playback-service "sıra, konum, oynatma komutu durumu"; Q2 sahiplik çakışması |
| Bu dosya | sınır **çözülmez** — Q1 aynen taşınır (§11) |

## §28 Gözlem Defteri

| # | Gözlem | Kaynak |
|---|---|---|
| 1 | Üç komut docblock'unda aynı PHP-runtime uyarısı | §3 |
| 2 | `Validator` istisna değil liste döner (kalite kuralı) | §5 |
| 3 | MySQL opsiyonel — tek uyarı, çökme yok | §6 |
| 4 | Hafif modda hash drift tespit edilemez (bilinçli sınır) | §7 |
| 5 | `tests/` dizini yok | §10 |
| 6 | Slugger tek kullanımlı — ingest | §19 |
| 7 | `audit` 22 kural kimliği dokümante, kod içi | §21 |

## §29 Güven Sırası

| Sıra | Kaynak | Güven |
|---|---|---|
| 1 | S1 — media.coremusic.net kod okuması (2026-10-13) | en yüksek |
| 2 | media.coremusic.net/AGENTS.md (klasör kuralı) | yüksek — kural katmanı |
| 3 | Vault — master §21.2, bagimliliklar, index FM | tasarım |
| 4 | ADR-092 (kod docblock'larında @see/paragraf) | karar — metni okunmadı → ⚠️ |
| 5 | Model hafızası | kullanılmadı |

## §30 Bilinen Eksikler

| Eksik | Etki |
|---|---|
| ADR-092 metni bu görevde okunmadı | §7/§8/§23 kural atıfları kod docblock'undan — karar metni ⚠️ |
| `docs/*.md` içeriği okunmadı | kural eşlemeleri yalnız başlık düzeyi (§24) |
| PHP koşulumu yok | `php -l` kanıtı yok (§10) |
| `catalog/catalog.jsonl` örneği okunmadı | kayıt şeması docblock'tan (§6) |
| config JSON içerikleri okunmadı | yalnız varlıkları sayıldı (§17) |

## §31 Sık Sorulanlar

| # | Soru | Yanıt | Kaynak |
|---|---|---|---|
| 1 | Bu servis dosya saklar mı? | Hayır — sahibi K015 | master §21.2 |
| 2 | MySQL zorunlu mu? | Hayır — opsiyonel, tek uyarı | §6 |
| 3 | sha256 neden yeniden hesaplanmıyor (hafif mod)? | ADR-092 §6.1 m.5 — yalnız girişte | §7 |
| 4 | `--commit`sız yazma olur mu? | Hayır — tek bayt yok | §23 |
| 5 | Slug Türkçe karakterlerde ne olur? | 12 eşleme fold → ASCII | §8 |
| 6 | ULID'de I/L/O/U var mı? | Hayır — alfabe hariç tutar | §8 |
| 7 | JSON-Schema motoru neden tam değil? | spec: tam motor YAZILMAZ | §5 |
| 8 | Test var mı? | `tests/` dizini YOK | §10 |
| 9 | Çıktı biçimi nedir (audit)? | `SEVIYE | yol | kural | neden` | §21 |
| 10 | PHP 8.4 doğrulaması yapıldı mı? | Hayır — runtime yok → ⚠️ | §10 |
| 11 | CLI bu servis mi? | Kanıtlanmadı → Q3 ⚠️ | §11 |
| 12 | Kapsam dışı hangi katmanlar? | K015 (dosya), K007 (DB), K008 (kural), K011 (arayüz) | §2 |

## §32 Kural-Kod Eşlemeleri (Doğrulanmış 10 Madde)

| # | Kural (kaynak) | Kod karşılığı | Satır |
|---|---|---|---|
| 1 | sha256 yalnız girişte (ADR-092 §6.1 m.5) | scan `--deep` bayrağı + hafif varsayılan | scan:14–19 |
| 2 | dry-run zorunlu (ADR-092 §8 m.11) | ingest `--commit` + STDIN `evet` | ingest:7–13, 67–68 |
| 3 | salt okunur denetim (ADR-092 §6.1/§7.1) | audit docblock:6 + yazma yok | audit:6 |
| 4 | ULID Crockford base32 (ADR-092 §6.1 m.2) | `ALFABE` + `DESEN` sabitleri | Ulid:16, 19 |
| 5 | slug max 80 (adlandirma.md kural 1) | `MAKS = 80` + `kirp` | Slugger:17, 133–139 |
| 6 | slug boşsa yedek | `YEDEK = 'isimsiz'` | Slugger:20, 68–70 |
| 7 | 12 fold eşlemesi (adlandirma.md §1) | `FOLD` dizisi | Slugger:23–30 |
| 8 | Windows-yasaklı adlar (§3) | `YASAKLI` + `yasakliMi` + `-one` | Slugger:33–37, 102–115 |
| 9 | `additionalProperties:false` (spec) | Validator kural 6 (`ek` hariç) | Validator docblock:14 |
| 10 | enum senkronu (spec) | `enumDrift`/`driftBul` + Taxonomy | Validator method listesi |

**Not:** kural metinleri okunmadan yalnız kod docblock'larındaki atıflar
kullanılır (§30 eksik listesi ile birlikte okunmalı).

## §33 Kapsam Dışı Yüzeyler

| Yüzey | Sahibi | Neden bu dosyada yok |
|---|---|---|
| Medya dosyasının fiziksel saklanması | K015-media | master §21.2 sınır satırı |
| `media_catalog` şeması/migration | K007-data (Data Engineer) | media AGENTS.md §3.1 #2 |
| İş kuralı tanımı | K008-domain (iddia) | index sınır satırı |
| Arayüz / GUI (Faz 3) | `docs/faz3-gui-spec.md` sahibi — ayrı görev | media AGENTS.md §2 |
| Web servisi / port :5000/:6000 | ADR-039 (deploy rolü: DevOps) | bu servis CLI — port iddiası taşımaz (kaynak: media AGENTS.md §3.1 #5) |
| Adaptör/protokol (dış API) | K013-integraion | anayasa sınırları |
| Event yayını (K012 event altyapısı) | shared/src/Events + integration-service | §18 sayıları |

## §34 Ölçüm Defteri (S1 — 2026-10-13)

| Dosya | Ölçülen satır | Ölçüm yöntemi |
|---|---|---|
| bin/scan.php | 261 | read + effective count |
| bin/ingest.php | 346 | read + effective count |
| bin/audit.php | 499 | read + effective count |
| src/Media/Validator.php | 431 | read + effective count |
| src/Media/CatalogWriter.php | 491 | read + effective count |
| src/Media/Taxonomy.php | 104 | read + effective count |
| src/Media/Ulid.php | 84 | read (tam) |
| src/Media/Slugger.php | 140 | read (tam) |
| Toplam CLI kodu | 2356 | 3 komut + 5 sınıf toplamı |
| config/ dosyası | 3 | glob |
| docs/ dosyası | 5 | glob |
| tests/ | 0 | glob — dizin yok |

**Not:** sayılar 2026-10-13 ölçümüdür; dosya değişirse bu tablo
güncellenir (son ölçüm esastır).

**Ek ölçüm (CLI dışı):** `media.coremusic.net` kökünde `composer.json`
vardır (media AGENTS.md §3.1 #6 routing satırı) — içeriği bu görevde
okunmadı → ⚠️ VERIFICATION REQUIRED.

## §35 Değişiklik Kaydı

| Tarih | Değişiklik | Not |
|---|---|---|
| 2026-10-12 | index.md iskeleti oluşturuldu (v2 §2.12) | ilk kayıt |
| 2026-10-13 | anayasa dosyası yazıldı — §1–§19; 3 komut + 5 sınıf S1 read; Q3 (CLI↔servis eşlemesi) açık bırakıldı | group mimari |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
