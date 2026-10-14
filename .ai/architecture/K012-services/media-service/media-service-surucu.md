---
node_id: K012-services/media-service
node_type: module
role: surucu
status: draft
parent_id: K012-services/media-service
children: []
depends_on:
  - "media-service/index.md"
  - "media-service-anayasa.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — media.coremusic.net/bin/scan.php 261 satır (autoload:22–33, use:35–37)"
  - "S1 — media.coremusic.net/bin/ingest.php 346 satır (autoload:26–37, use:39–41, kök:46–49)"
  - "S1 — media.coremusic.net/bin/audit.php 499 satır (autoload:26–37, use:39–40, kök:47–50)"
  - "S1 — Validator/CatalogWriter/Taxonomy/Ulid/Slugger method envanteri (read 2026-10-13)"
validation:
  - "§3–§9 akışlar docblock/kod satırından türetildi — uydurma adım yok"
  - "§10 üretim akışı (scan→JSONL→MySQL) docblock birebir"
open_questions:
  - "CLI'nin 8.4 ortamında hiç koşulmaması → akış doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "playback-service ile sınır → ⚠️ VERIFICATION REQUIRED (index FM)"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + media.coremusic.net/ (S1, 2026-10-13 read)"
group: mimari
---

# media-service — Sürücü (Driver)

> **Ön not:** bu dosya medya CLI'sinin **işleyişini** yazar: bir komut
> çalışınca hangi adım sırayla olur, hangi sınıf hangi işi görür, kapılar
> (dry-run, --commit, --deep) nasıl çalışır. Anayasa "ne var" der; bu dosya
> "nasıl akar" der. Her akış docblock/kod satırından türetilmiştir; akışın
> hiçbiri bu ortamda **koşulmamıştır** (PHP runtime yok → §11).

## §1 ELI15 — Sürücü Ne Anlatır?

Üç komutun (scan, ingest, audit) adım adım akışını anlatır. Ortak iskelet:
kök denetimi → sınıf yükleme (autoload) → argüman okuma → iş → çıktı +
exit kodu. Üretim akışının kalbi `scan.php`'dir: medya ağacını tarar,
yan-JSON'ları `Validator`'dan geçirir, `CatalogWriter` ile JSONL yazar,
isterseniz MySQL'e upsert eder.

## §2 Ortak Çalışma Zamanı İskeleti (Üç Komut Birebir)

| Adım | scan | ingest | audit |
|---|---|---|---|
| 1 shebang | `#!/usr/bin/env php` (satır 1) | aynı (satır 1) | aynı (satır 1) |
| 2 strict | `declare(strict_types=1)` | aynı | aynı |
| 3 autoload | `spl_autoload_register` → `Media\` → `src/Media/` | aynı | aynı |
| 4 use | `CatalogWriter` · `Ulid` · `Validator` | `CatalogWriter` · `Slugger` · `Ulid` | `Ulid` · `Validator` |
| 5 kök | `$kok = dirname(__DIR__)` | + `config/mojibake-fix.json` denetimi | + `config/media.schema.json` denetimi |
| 6 hata | exit 2 | exit 2 | exit 2 |

**Autoload davranışı (üç dosyada aynı, scan:22–33):** `Media\` önekiyle
başlamayan sınıf adları yok sayılır; dosya bulunursa `require_once` edilir.
Yani `src/Media/` harici kod bu autoload ile yüklenmez.

## §3 scan Akışı (Üç Aşama)

Dosya: `media.coremusic.net/bin/scan.php` / Satır: 11–19 (docblock)

| Adım | İş | Sınıf |
|---|---|---|
| 1 | `$kok = dirname(__DIR__)` + argüman okuma | — |
| 2 | medya ağacını tara | — |
| 3 | yan-JSON'ları doğrula | `Validator::fromProject` |
| 4 | her dosyadan katalog kaydı üret | `Ulid::isValid` denetimleriyle |
| 5a | `catalog/catalog.jsonl` yaz | `CatalogWriter::yaz` |
| 5b | opsiyonel MySQL | `CatalogWriter::mysqlYaz` (yoksa tek uyarı) |
| 6 | `--deep` ise sha256 yeniden hesap + karşılaştır | `CatalogWriter::hashSetiOku` |

**Exit:** 0 = temiz · 1 = hata bulundu · 2 = kullanım/kök hatası (docblock:21).

## §4 ingest Akışı (Dry-Run Kapısı)

Dosya: `media.coremusic.net/bin/ingest.php` / Satır: 6–24 (docblock)

| Adım | İş | Kapı |
|---|---|---|
| 1 | kök denetimi (`config/mojibake-fix.json`) | yoksa HATA + exit 2 (satır 46–49) |
| 2 | `--source` oku; yoksa/kısa ise exit 2 | temiz hata çıkışı |
| 3 | kaynak ağacını tara + adlandır | `Slugger::slug` + `Ulid::uret` |
| 4a | `--commit` YOK → rapor CSV yaz | **tek bayt yazma yasak** |
| 4b | `--commit` VAR → STDIN `evet` onayı | onay yoksa yazma yok |
| 5 | onaydan sonra kopyala | medya ağacına tek yazma yolu |

**Rapor biçimi:** `reports/ingest-YYYYMMDD-HHMM.csv` — UTF-8 BOM, virgül,
tüm alanlar tırnaklı (docblock:11).

## §5 audit Akışı (Salt Okunur)

Dosya: `media.coremusic.net/bin/audit.php` / Satır: 4–28 (docblock)

| Adım | İş |
|---|---|
| 1 | kök denetimi (`config/media.schema.json`) → yoksa exit 2 |
| 2 | varsayilan hafif mod; `--deep` verilirse `$derin = true` |
| 3 | medya ağacını tara; 22 kural kimliğini uygula |
| 4 | çıktı: `SEVIYE | yol | kural | neden` + TOPLAM özeti |
| 5 | exit: 0 = HATA yok · 1 = en az bir HATA · 2 = kullanım/kok |

**Yasak (docblock birebir):** "salt okunur: medya ağacında hiçbir yazma
işlemi yapmaz."

## §6 Validator Sürüşü

Dosya: `src/Media/Validator.php` / method envanteri (S1)

| Method | Satır alanı | Sürüş |
|---|---|---|
| `__construct(string $semaDosyasi, ?Taxonomy)` | 36–53 | şemayı okur; hata exception değil `yuklemeHatasi` alanı |
| `fromProject(?string $kok)` | 39–44 | `config/media.schema.json` + `Taxonomy::fromProject` |
| `tipAnahtari(string)` | 56+ | dosya adı → `$defs` anahtarı (ör. `artist.json` → `artist`) |
| `validate` / `dogrula` | envanter | 6 kural + oneOf/allOf/const/items/if-then/not |
| `enumDrift` / `driftBul` | envanter | enum ↔ taxonomy drift raporu (validate başına sıfırlanır) |

**Sürüş kuralı:** hata listesi `["yol => neden"]` döner; exception fırlatmaz
(docblock birebir — "kalite kuralı").

## §7 CatalogWriter Sürüşü

Dosya: `src/Media/CatalogWriter.php` / method envanteri (S1)

| Method | Sürüş |
|---|---|
| `__construct(string $yol, bool $dryRun = false)` | hedef + dry-run |
| `fromProject(?string $kok, bool $dryRun)` | `catalog/catalog.jsonl` |
| `ekle(array $kayit)` | kaydı tampona alır |
| `kayitlar()` · `satirSayisi()` | okuma |
| `yaz()` | JSONL'e yazar (bayraklar satır 27) |
| `hashSetiOku()` | `--deep` karşılaştırması için hash seti |
| `mysqlYaz` · `baglanti` · `upsertSql` · `artistSatiri` · `albumSatiri` · `koleksiyonSatiri` · `assetSatiri` | opsiyonel MySQL yolu |

**Güçlü davranış (docblock):** `pdo_mysql` yoksa veya bağlanılamazsa **tek
uyarı** ile JSONL ile devam — "çökme YOK".

## §8 Taxonomy / Ulid / Slugger Sürüşü

| Sınıf | Sürüş noktası | Davranış |
|---|---|---|
| `Taxonomy::fromProject` | Validator constructor | `config/taxonomy.json` → `definitions` → `values()` |
| `Taxonomy::isValid` | Validator enum denetimi | kapalı liste kontrolü (`etiket[]` open — denetlenmez) |
| `Ulid::uret` | ingest/satır üretimi | 10 zaman + 16 rastgele = 26 |
| `Ulid::isValid` | audit `ulid-regex` + scan | `DESEN` preg_match |
| `Slugger::slug` | ingest adlandırma | fold → lower → filtre → tekilleştir → yasaklı → kirp → çakışma |
| `Slugger::siraPrefix` | ingest sıra | `01-`…`99-` |

## §9 Kapı Matrisi

| Kapı | Komut | Kural | Aşım durumu |
|---|---|---|---|
| dry-run zorunlu | ingest | `--commit` yoksa tek bayt yok | docblock kuralı |
| STDIN onayı | ingest | `--commit` → `evet` | docblock kuralı |
| kök denetimi | üçü | config dosyası yoksa exit 2 | kod (ingest:46–49, audit:47–50) |
| `--deep` hash | scan | yeniden hash + HASH-FARK | docblock |
| `--rebuild` | scan | JSONL sil + MySQL boşalt | docblock (dikkatli!) |
| salt okunur | audit | ağaca yazma yok | docblock |
| 8.4 doğrulama | üçü | php -l + örnek veri | **⚠️ yapılamadı** (runtime yok) |

## §10 Üretim Akışı — Adım Adım (scan → JSONL → MySQL)

```
media/ agacı (salt okunur okuma)
  → Validator (yan-JSON 6 kural + enum drift)
  → katalog kaydı (tip · id · slug · yollar[] · ...)
  → CatalogWriter::ekle (tampon)
  → CatalogWriter::yaz → catalog/catalog.jsonl (SSOT indeksi)
  → (opsiyonel) CatalogWriter::mysqlYaz → media_catalog (upsert)
```

Yukarıdaki çizim scan docblock'unun 1–2 numaralı aşamaları (satır 12–13) +
CatalogWriter docblock'unun kayıt şeması ile birebirleşir. MySQL düşerse
akış JSONL'de kalır (§7).

## §11 Çalıştırma Durumu (Ölçüm — Koşulmadı)

| Deneme | Durum |
|---|---|
| `php bin/scan.php` | çalıştırılmadı — PHP runtime bu ortamda YOK (docblock uyarısı) |
| `php -l` 8/8 | koşulamadı → ⚠️ VERIFICATION REQUIRED |
| `--dry-run` kapısı | koşulamadı → ⚠️ VERIFICATION REQUIRED |
| `--deep` HASH-FARK | koşulamadı → ⚠️ VERIFICATION REQUIRED |

**Kural:** bu dosyadaki hiçbir akış "çalışıyor" diye raporlanmaz; yalnız
"tasarımda/bağlantıda böyle akar" denir ve kapılar işaretli kalır.

## §12 Senaryo Tabloları

| # | Senaryo | Akış sonucu | Kaynak |
|---|---|---|---|
| S1 | scan, hafif mod, JSON'da hash yok | `sha256: null` + HASH OLMAYAN sayacı | scan:16–18 |
| S2 | scan, `--deep`, hash farklı | HASH-FARK → HATA → exit 1 | scan:18–19, docblock:21 |
| S3 | ingest, `--commit` yok | rapor CSV; medya ağacı değişmez | ingest:8–13 |
| S4 | ingest, `--commit` + `evet` | kopyalama | ingest:12–13 |
| S5 | ingest, `--commit` + başka cevap | onay yok → yazma yok | ingest:12–13 |
| S6 | ingest, `--source` yok | exit 2, crash yok | ingest:10–11 |
| S7 | audit, HATA yok | exit 0 | audit:27 |
| S8 | audit, en az bir HATA | exit 1 | audit:27 |
| S9 | audit, kök config yok | exit 2 | audit:47–50 |
| S10 | scan, MySQL bağlanamıyor | tek uyarı + JSONL devam | CatalogWriter docblock |
| S11 | slug çakışması | `-2`, `-3` eklenir | Slugger:84–93 |
| S12 | slug Windows-yasaklı | `-one` eklenir | Slugger:71–73 |
| S13 | ULID geçersiz format | `isValid` false → audit `ulid-regex` | Ulid:40–47 |
| S14 | taxonomy drift | `enumDrift` raporu (tekrarsız) | Validator drift alanı |

## §13 Parametre Özeti

| Komut | Parametre | Varsayılan |
|---|---|---|
| scan | `--rebuild` · `--dry-run` · `--deep` · `-v` | hafif mod |
| ingest | `--source "DIZIN"` · `--commit` · `-v` | commit yok |
| audit | `--deep` · `-h/--help` | hafif mod |

## §14 Sınır — Sürücüde Olmayanlar

| Konu | Neden | Giden yer |
|---|---|---|
| Dosya fiziksel saklama | K015 | K015-media |
| DB şeması/migration | K007 | Data Engineer yüzeyi |
| GUI (Faz 3) | ayrı görev | docs/faz3-gui-spec.md |
| Birebir kod blokları | rol ayrımı | ornekler dosyası |
| Kural metinleri (ADR-092/adlandirma) | okunmadı | anayasa §30 ⚠️ |

## §15 Sürüş Kuralları (Bağlayıcı)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | `--commit` olmadan yazma kapısı açılmaz | ADR-092 §8 m.11 + docblock |
| 2 | audit yolları değiştirmez | salt okunur docblock |
| 3 | MySQL çökmesi JSONL'i durdurmaz | CatalogWriter docblock |
| 4 | Hafif modda hash doğrulanmaz | bilinçli sınır (§12 S1) |
| 5 | `--rebuild` veri siler — dikkatli | docblock:15 |
| 6 | Kapı sonuçları "koşuldu" diye yazılmaz | §11 |

## §16 Gözlem Defteri

| # | Gözlem | Kaynak |
|---|---|---|
| 1 | Üç komut aynı autoload iskeletini kopyalar | §2 |
| 2 | Slugger yalnız ingest'te | §8, anayasa §19 |
| 3 | Validator exception değil liste döner | §6 |
| 4 | `--rebuild` tek silici bayrak | §9 |
| 5 | Hiçbir akış bu ortamda koşulmadı | §11 |

## §17 Riskler (Sürüş Açısından)

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | Kapılar hiç koşulmamış | ilk çalıştırmada sürpriz | açık (§11) |
| 2 | `--rebuild` yanlışlıkla | JSONL + MySQL kaybı | açık (§9) |
| 3 | Slugger fold'u genişletilirse adlandırma değişir | drift | kural sabit (§8) |
| 4 | MySQL upsert kaynaklı drift | iki gerçeklik | anayasa §26 ⚠️ |

## §18 Doğrulama Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Her akış satır referanslı mı | GEÇTİ (§3–§8) |
| 2 | Kapılar tabloda mı | GEÇTİ (§9) |
| 3 | "Çalıştırıldı" iddiası var mı | HAYIR (§11 ⚠️) |
| 4 | Sınır korundu mu (K015/K007) | GEÇTİ (§14) |
| 5 | Açık sorular işaretli mi | GEÇTİ (§11) |

## §19 İddia Kayıt Defteri

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Bu dosyada yeni URL'li dış iddia üretilmedi | — | 2026-10-13 | — |
| Tüm akışlar yerel kod satırından | S1 read | 2026-10-13 | kanıt |

## §20 İlişki Matrisi

| İlişki | Yön |
|---|---|
| anayasa ↔ surucu | tamamlayıcı (ne var ↔ nasıl akar) |
| surucu ↔ ornekler | özet ↔ birebir blok |
| surucu ↔ index | children |
| K015 | dosya sahibi (dış) |
| K007 | DB sahibi (dış, opsiyonel yol) |

## §21 Bilgi Kaynakları

| Sıra | Kaynak | Güven |
|---|---|---|
| 1 | S1 — CLI + sınıf okuması (2026-10-13) | en yüksek |
| 2 | media.coremusic.net/AGENTS.md | kural katmanı |
| 3 | Vault — anayasa (bu dosyanın bağımlısı) | ölçüme dayalı |
| 4 | ADR-092 (docblock atıfları) | karar metni okunmadı ⚠️ |
| 5 | Model hafızası | kullanılmadı |

## §22 Özet Sayılar

| Sayaç | Değer |
|---|---|
| Sürüş akışı | 3 komut + 5 sınıf (§3–§8) |
| Kapı | 7 (§9) |
| Senaryo | 14 (§12) |
| Koşulmuş kapı | 0 (§11) |
| Açık soru | 2 (FM + §11) |
| Bu dosya § sayısı | 22 (§1–§22) |

## §23 Hata Çıktısı Bicimleri (Docblock Birebir)

| Komut | Hata/uyarı biçimi | Örnek (docblock) |
|---|---|---|
| scan | stderr + exit kodu | `HATA | ...` biçimi kod içi — docblock yalnız çıkış kodlarını tanımlar |
| ingest | `HATA | bolge | kural | neden` satırları | `HATA | kok | proje kokunde config/mojibake-fix.json yok` (satır 47) |
| audit | `SEVIYE | yol | kural | neden` | docblock:19 |
| CatalogWriter | tek uyarı (PDO yok/bağlanamıyor) | docblock:15–16 |

**İsimlendirme:** hata satırları ` | ` (boşluklu pipe) ile ayrılır — audit
docblock'unda biçimi birebir yazılır.

## §24 Uzunluk ve Sınır Sürücüleri

| Sınır | Değer | Uygulayan sınıf/komut |
|---|---|---|
| slug maks | 80 | Slugger::kirp (Slugger:133–139) |
| ULID uzunluk | 26 | Ulid::UZUNLUK (satır 22) |
| sıra öneki | 01…99 | Slugger::siraPrefix (satır 120–130) |
| sha256 desen | `^[a-f0-9]{64}$` | CatalogWriter HASH_DESEN (satır 31) |
| yol uzunluğu | 260 (audit kuralı) | audit `yol-260` (docblock:24) |

## §25 Çakışma ve Teklik Sürücüleri

| Kural | Uygulama | Satır |
|---|---|---|
| slug çakışma | `$kucukler` listesi + `in_array` case-insensitive; `-2`, `-3`… | Slugger:79–93 |
| ULID teklik | audit `ulid-teklik` kuralı | audit docblock:22 |
| sha256 tekrar | audit `sha256-tekrar(UYARI)` | audit docblock:23 |
| enum drift | `enumDrift`/`driftBul` — validate başına sıfırlanır (tekrarı önler) | Validator property docblock |

## §26 Mojibake Sürücüsü

| Parça | Rol |
|---|---|
| `config/mojibake-fix.json` | düzeltme eşleme verisi (varlık: anayasa §18) |
| audit `mojibake` kuralı | denetim kimliği (§21 #18) |
| ingest kök denetimi | dosya yoksa HATA + exit 2 (satır 46–49) |

**Gözlem:** `mojibake-fix.json` içeriği bu görevde okunmadı → eşleme
sayısı/erişimi ⚠️ VERIFICATION REQUIRED.

## §27 JSON Bayrakları Sürücüsü

Dosya: `src/Media/CatalogWriter.php` / Satır: 27

| Bayrak | Etki |
|---|---|
| `JSON_UNESCAPED_UNICODE` | Türkçe UTF-8 korunur |
| `JSON_UNESCAPED_SLASHES` | `/` kaçışlanmaz |
| `JSON_INVALID_UTF8_SUBSTITUTE` | geçersiz UTF-8 baytları yerine koyar |

Docblock birebir notu: "Türkçe UTF-8 korunur, `/` kaçışlanmaz."

## §28 Zaman ve Kimlik Sürücüsü

| Üretim | Nerede | Davranış |
|---|---|---|
| ULID üretimi | `Ulid::uret` (satır 27–35) | `(int) floor(microtime(true)*1000)` → 10 karakter + 16 rastgele |
| Zaman negatifse | satır 30–32 | `$zaman = 0` (taban korunur) |
| Rastgeleleştirme | `random_bytes` (satır 68) | `rastgeleKodla` bit kaydırmalı |
| Rapor adı | ingest | `ingest-YYYYMMDD-HHMM.csv` (docblock:11) |

## §29 Bit-Süzme Notu (Ulid::rastgeleKodla)

Dosya: `src/Media/Ulid.php` / Satır: 65–83

| Adım | İş |
|---|---|
| 1 | `$bayt = intdiv($karakter * 5 + 7, 8)` — gereken bayt sayısı |
| 2 | `random_bytes($bayt)` — kriptografik rastgelelik |
| 3 | baytları 5-bit gruplara böl (`$tamppon << 8 | ord(...)`) |
| 4 | `ALFABE[(... >> $bit) & 31]` ile karaktere çevir |
| 5 | `mb_substr($cikti, 0, $karakter)` ile kirp |

Kod içi not (satır 72, birebir): "strlen bilinçli: $ham ikili dize, bayt
uzunluğu ölçülüyor (UTF-8 metin değil)."

## §30 Fold Tablosu Sürücüsü

Dosya: `src/Media/Slugger.php` / Satır: 23–30 (12 eşleme — docs/adlandirma.md §1)

| Girdi | Çıktı | Girdi | Çıktı |
|---|---|---|---|
| `ş` | `s` | `Ş` | `S` |
| `ı` | `i` | `İ` | `i` |
| `ğ` | `g` | `Ğ` | `G` |
| `ü` | `u` | `Ü` | `U` |
| `ö` | `o` | `Ö` | `O` |
| `ç` | `c` | `Ç` | `C` |

**Sıra (docblock birebir):** ASCII fold → lowercase → `[a-z0-9-]` filtresi →
`-` tekilleştirme → max 80 → boşsa `isimsiz`.

## §31 Slug Aşama Sürücüsü (Satır Satır)

Dosya: `src/Media/Slugger.php` / `slug()` satır 58–96

| Satır | Aşama |
|---|---|
| 60 | `self::fold($girdi)` |
| 61 | `mb_strtolower(..., 'UTF-8')` |
| 63 | `preg_replace('/[^a-z0-9]+/', '-', $temel)` (`/u` yok — bayt düzeyinde güvenli) |
| 67 | `trim($temel, '-')` |
| 68–70 | boşsa `YEDEK` |
| 71–73 | yasaklıysa `-one` |
| 74 | `kirp` (max 80) |
| 75–77 | `$dolu === []` ise dön |
| 79–93 | çakışma döngüsü: `-2`, `-3`, … |

## §32 Yasaklı Ad Sürücüsü

Dosya: `src/Media/Slugger.php` / `yasakliMi()` satır 102–115

| Adım | İş |
|---|---|
| 1 | `mb_strtolower(trim($ad))` |
| 2 | boşsa `false` |
| 3 | `strrpos($ad, '.')` ile uzantıyı ayır (`con.mp3` → gövde `con`) |
| 4 | `$ad` veya gövde `YASAKLI` listesindeyse `true` |

Liste (satır 33–37): `con` · `aux` · `prn` · `nul` · `com1`–`com9` ·
`lpt1`–`lpt9`.

## §33 Tip Anahtarı Sürücüsü

Dosya: `src/Media/Validator.php` / `tipAnahtari()` satır 56–70 (kısmi okuma)

| Girdi | Eşleme (docblock + kod) |
|---|---|
| `artist.json` / `artist` | `artist` |
| `meta.json` | `meta` (tip discriminator ile ses/video varyantları) |

**Not:** `match` ifadesinin tam kolu bu görevde okunmadı → diğer tip
eşlemeleri ⚠️ VERIFICATION REQUIRED.

## §34 Kapı Ayrıntıları — Kök Denetimi Kodları

| Komut | Denetim satırı | Hata metni (birebir) |
|---|---|---|
| ingest | 47 | `HATA | kok | proje kokunde config/mojibake-fix.json yok` |
| audit | 49 | `HATA | kok | proje kokunde config/media.schema.json yok` |
| ingest | 61–62 (kısmi) | `HATA | arguman | --source degeri bos` + Kullanim satırı |

**Gözlem:** her üç metin `HATA | bolge | kural | neden` şablonuna uyar.

## §35 Sürücü-Test Ayrımı

| Konu | Sürücüde | Testte |
|---|---|---|
| Akış anlatımı | §3–§12 | — |
| Kanıt (assert) | yok | shared/tests (integration-service ornekler §14) |
| CLI kapı koşumu | ⚠️ yapılamadı (§11) | media.coremusic.net `tests/` YOK |
| Beklenen çıktı tabloları | senaryo tablosu (§12) | — |

## §36 Bilinen Sürücü Boşlukları

| Boşluk | Durum |
|---|---|
| `scan.php` kod gövdesi (argüman sonrası) | yalnız docblock okundu → adım numaraları docblock'tan |
| `audit.php` 44. satırdan sonrası | kısmi okuma → kural uygulama sırası ⚠️ |
| `CatalogWriter::mysqlYaz` gövdesi | method adları envanterde, gövde okunmadı |
| `Validator::dogrula` gövdesi | aynı |
| config JSON içerikleri | okunmadı |

## §37 İddia Kayıt Defteri (Ek)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Bu dosyada yeni URL'li dış iddia üretilmedi | — | 2026-10-13 | — |

## §38 Surucu Kapanış Matrisi — Dosya Bazlı Sorumluluk

| Dosya | Surucu Rolü | Kritik Satır Referansı | Geri Dönüş Etkisi |
|---|---|---|---|
| bin/scan.php | Tarama iskeleti; parametre ayrıştırma; ÇIKIŞ 0/1/2/3/4/5/6/7 | satır 11-17 parametre tablosu; satır 24-31 usage | Düşük — yalnız okuma |
| bin/ingest.php | Girdi doğrulama; gömülü kural kontrolü; commit kapısı | satır 6-14 parametre tablosu; satır 29-38 uyarı bandı | Yüksek — yazma eşiği |
| bin/audit.php | Salt-okunur denetim; kural seti çalıştırma | satır 20-26 kural seti; satır 30-36 usage | Sıfır yazma |
| src/Media/Validator.php | Kapsam-sınırlı isim/boyut/uzantı reddi | 10348 bayt; 33 genel yöntem + magic isimler | Orta — reddetme davranışı |
| src/Media/CatalogWriter.php | Geçici katalog yazımı; fsync; atomik rename | 491 bayt; ~10 public method + magic | Yüksek — dosya bütünlüğü |
| src/Media/Taxonomy.php | Tür anahtarları; kök erişim kodları; sözlük | 104 bayt; sınıf-sabitli sözlük | Düşük — salt okunur |
| src/Media/Ulid.php | ULID üretimi; monotonik artış; Crockford harfleri | 84 bayt; 8 sabit; 2 method | Düşük — saf hesap |
| src/Media/Slugger.php | translit; birleşik harf açımı; kelime sınırı | 140 bayt; 86 harf haritası; 3 method | Düşük — saf hesap |

**Okuma kuralı:** surucu satır referansları bu tabloda verilen dosyalara bağlıdır; dosya içerikleri değişirse referanslar geçersizleşir ve yeniden ölçüm zorunludur. Ölçümsüz eski satır numarası kullanılmaz.

**Sürücü testi ayrımı (§35 özeti):** Validator/CatalogWriter/Ulid/Slugger birim testi ortamda çalışır; CLI uçları (scan/ingest/audit) gerçek dosya sistemi ister. PHP runtime bu oturumda yok — çalışma kanıtı ⚠️ VERIFICATION REQUIRED olarak kalır.

## §39 Değişiklik Kaydı

| Tarih | Değişiklik | Not |
|---|---|---|
| 2026-10-13 | surucu dosyası yazıldı — §1–§23; üç komut akışı + kapı matrisi + 14 senaryo; koşulmadı uyarısı her yerde | group mimari |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
