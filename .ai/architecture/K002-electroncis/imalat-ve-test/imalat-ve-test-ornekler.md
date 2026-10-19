---
node_id: K002-electroncis/imalat-ve-test/imalat-ve-test-ornekler
node_type: module
status: draft
parent_id: K002-electroncis/imalat-ve-test
children: []
depends_on: [imalat-ve-test-anayasa, ADR-017, ADR-038, L0-10]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-ornekler.md §7 (dürüstlük) + §15.1 (iskelet)"
  - ".ai/architecture/K002-electroncis/imalat-ve-test/imalat-ve-test-anayasa.md §2-§11 (kurallar)"
validation: "draft — 2026-10-14 yazıldı · ≥500 ölçümü yazım sonrası · tüm bloklar PSEUDOCODE"
open_questions:
  - "Q-IM2: SNR/THD fiziksel ölçümü yapılmadı (PLANNED)"
  - "Q-O1: Elektronik kod örneği hangi repo'da"
group: kod
---

# imalat-ve-test — Kod/Plan Örnekleri ve OS/Dil Kapsamı (Kademe-4)

> **Kapsam:** Çalışır durumda kod **SAĞLANMAZ** (repo'da elektronik kodu YOK — L0 kaydı:
> `*.cpp/*.h` = 0). Her blok **pseudocode / plan iskeletidir**: test planı, ölçüm
> kaydı, burn-in logu, AOI kriteri, Gerber kontrol listesi. **Ölçüm sonucu, AOI limiti
> ve standart numarası uydurulmaz** — kapılar hedef olarak, sonuç `UNKNOWN` yazılır.

## §0 ELI15

Bu dosya üretim/ölçüm alt-konusunun **örnek yüzeyidir**: bir cihaz fabrikada nasıl
test edilir, ölçüm masasında SNR/THD nasıl kaydedilir, 8 saat burn-in logu nasıl
tutulur, AOI kriteri nasıl yazılır — hepsi iskelet olarak. Her örnek bir kurala
bağlıdır (anayasa §2-§7) ve her OS/dil satırı `⚠️ VERIFICATION REQUIRED` taşır:
**öneridir, ölçüm değildir**.

## §1 Dürüstlük Kaydı (şablon §7)

| Madde | Kayıt | Kaynak |
|---|---|---|
| Repo'da elektronik kodu | **YOK** (`*.cpp`/`*.h` = 0) | L0 kök turu |
| Bloklar çalışır mı | **HAYIR** — pseudocode/plan | `electroncis-ornekler.md` §7 |
| Ölçülmüş sayı | **YOK** — yalnız hedef/kapsam sabitleri | §B.5 · kısıtlar |
| AOI limiti / standart no | **YOK** | Q-IM1 `⚠️` |
| Satır kanıtları | §H.5 atıfları **okunmadı** | `⚠️` |
| Commit | ATILMADI | kural |

## §2 Bağlayıcı Kısıtlar

| # | Kısıt | Değer | Kaynak |
|---|---|---|---|
| 1 | SNR hedefi | >100 dB | §B.5(4) |
| 2 | THD+N hedefi | <0.01% @1 kHz | §B.5(4) |
| 3 | Format | 32-bit float | ADR-017 |
| 4 | Blok/örnekleme | 512 / 48 kHz | ADR-017 · L0 |
| 5 | DC koruma | >0.5 V → röle | L0-10 |
| 6 | Burn-in | 8 saat | §B.5(6) |
| 7 | Ölçüm durumu | PLANNED (`UNKNOWN`) | §N-7 |

## §3 Örnek A — ICT/FCT Test Planı İskeleti

```text
// PSEUDOCODE/PLAN — ICT + FCT adim plani (kod degil, plan)
ICT[1] := guc_rail_kontrol()      // besleme dogrulamasi
ICT[2] := kisa_acik_kontrol()     // net dogrulamasi
ICT[3] := test_noktasi_erisimi    // DFT — §3
FCT[1] := fonksiyon_akisi()       // K005/QA kodu — YAZMA
FCT[2] := olcum_kapisi()          // SNR/THD hedefleri — §5
karar := TUMU_GECTI_mi()          // tek gecen damga — §5-8
```

| # | Aşama | Kural | Kaçınılan |
|---|---|---|---|
| 1 | ICT | §3 | test noktası yok |
| 2 | FCT | §3(4) — kod K005/QA | otomasyon kodu yazmak |
| 3 | Kapı | §5 hedefleri | sonuc uydurmak |
| 4 | Karar | "geçti" = tüm kapılar | kısmi damga |

## §4 Örnek B — Ölçüm Kayıt Şablonu (JSON)

```json
{
  "record_type": "snr_thd_measurement",
  "snr_db": "UNKNOWN",
  "snr_target_db": 100,
  "thd_n_percent": "UNKNOWN",
  "thd_n_target_percent": 0.01,
  "thd_frequency_hz": 1000,
  "sample_block": 512,
  "sample_rate_hz": 48000,
  "source_format": "float32",
  "measured": false,
  "station": "UNKNOWN",
  "date": "2026-10-14"
}
```

> `measured: false` → gerçekler `UNKNOWN`, hedefler sayı olarak durur (kaynaklı).
> Uydurma ölçüm değeri **yazılmaz** (Zero-Hallucination §3).

## §5 Örnek C — Burn-in Logu İskeleti (8 saat)

```text
// PSEUDOCODE — 8 saat yaslanma logu (sure §B.5(6) sabiti)
SURE := 8 * 60 dk
her 60 sn:
    LOG := (t, guc=OLC(), dc=OLC(), sicaklik=OLC())
    eğer dc > 0.5 V ise röle_ac()     // L0-10 — cekirdek, bu dosya degil
    log_yaz(LOG)                      // degerler olcumle gelir, uydurma yok
sonunda: tekrar SNR/THD_kapisi()      // §4
```

| # | Alan | Değer | Not |
|---|---|---|---|
| 1 | `SURE` | 8 saat | §B.5(6) sabiti |
| 2 | `dc` | `UNKNOWN` | ölçülmedi |
| 3 | `sıcaklık` | `UNKNOWN` | uydurma yasak |
| 4 | `röle` | çekirdek kararı | L0-10 |

## §6 Örnek D — AOI / X-ray Kriter İskeleti

```text
// PSEUDOCODE/PLAN — muayene kriteri (SINIR YUZDESI YOK — `⚠️`)
AOI := (eksik_yayim, short, gorel_yon)      // gozle gorunur
XRAY := (void, baglanti)                    // kapali baglanti
her aday icin:
    karar := kriter_karsilastir(aday)       // limit: olcum/standart YOK
    kaydet(karar, "limit=UNKNOWN")          // uydurma limit yasak
```

## §7 Örnek E — Gerber / Üretim Kontrol Listesi (iskelet)

| # | Kontrol | Kaynak | Durum |
|---|---|---|---|
| 1 | Katman/trace bütünlüğü | `pcb-design.md:24-30,44-52` | okunmadı `⚠️` |
| 2 | I2S trace 8 mil | §N(3) | üretim kuralı |
| 3 | Decoupling yerleşimi | §N(4) | değer devrede |
| 4 | Test noktası erişimi | anayasa §3 | plan `⚠️` |
| 5 | Reflow profilisi | anayasa §2(3) | `⚠️` |
| 6 | Panel/break-away | anayasa §2(2) | `⚠️` |

## §8 OS Kapsamı — her işletim sistemi (öneri · `⚠️`)

| # | OS | Üretim/ölçüm arayüzü | Rapor | K000 atıfı |
|---|---|---|---|---|
| 1 | Windows | CAD/fabrika istemcisi + CLI (C#/Python) | ekran/HTML | `windows/` |
| 2 | macOS | CLI ölçüm (Python/C++) | CLI | `macos/` |
| 3 | Linux | Ölçüm sunucusu + shell/Python (kod K005/QA) | web (PHP) | `os-guc-elektronigi/` |
| 4 | iOS | Üretim/ölçüm **YOK** | yalnız görüntüleme | `ios/` |
| 5 | Android | Üretim/ölçüm **YOK** | yalnız görüntüleme | `android/` |
| 6 | Fabrika/RTOS | Fixture seri/USB + otomasyon | hat raporu | `donanim-arayuz-protokolleri/` |

## §9 Dil Kapsamı — her programlama dili (öneri · `⚠️`)

| # | Dil | Ölçüm/üretim bloğu | Kaçınılan |
|---|---|---|---|
| 1 | Python | ölçüm scripti + CSV/JSONL + burn-in logu | kapı kararı |
| 2 | C#/.NET | fabrika istemcisi + rapor ekranı | kapı kararı |
| 3 | C/C++ | fixture arayüzü (firmware K005 sınırı) | K005 aşımı |
| 4 | Rust | rapor/ölçüm aracı | — |
| 5 | PHP 8.x | üretim takibi (PDO parametreli) | karar/kapı |
| 6 | JS/TS | rapor okuma/gösterme | karar |
| 7 | PowerShell/shell | hattan otomasyon | sorumluluk K005/QA |
| 8 | Assembly | **önerilmez** (üretim katmanı) | genel mantık |

## §10 OS Bazlı İskelet Blokları

### Windows

```text
// PSEUDOCODE — fabrika istemcisi
SONUC := olcum_cli()                  // JSONL
eğer SONUC.measured = false ise rapor := "BILINMIYOR"
```

### macOS

```text
// PSEUDOCODE — CLI olcum
KAYIT := olcum_uret()                 // UNKNOWN degerler
kaydet_jsonl(KAYIT)                   // uydurma yok
```

### Linux

```text
// PSEUDOCODE — olcum sunucusu (kod K005/QA'da)
SONUC := api_cagir()                  // PHP kaydi
rapor_goster(SONUC)                   // KARAR YOK
```

### iOS / Android

```text
// PSEUDOCODE — yalniz rapor goruntuleme
RAPOR := api_oku()
goster(RAPOR)                         // uretim/olcum YOK
```

### Fabrika / RTOS (fixture)

```text
// PSEUDOCODE — hat akisi (L0 baglari tam)
fixture_bagla()
ICT := icer_test()                    // kod K005/QA
FCT := dis_test()
OLCUM := snr_thd_kapisi()             // hedef: >100 dB, <0.01% @1k
eğer OLCUM.gecti = false ise etiket := "KALDI"
burn_in(8 saat)                       // §B.5(6)
```

## §11 Dil Bazlı İskelet Blokları

### Python

```python
# PSEUDOCODE — olcum + burn-in logu
# kayit = {"snr_db": "UNKNOWN", "measured": False}
# sure = 8          # saat — §B.5(6) sabiti
# her_dk: log_yaz(dc=OLC(), t=OLC())
```

### C#/.NET

```csharp
// PSEUDOCODE — fabrika raporu
// record Olcum(string Snr, bool Measured = false);
// rapor_yaz(kayit);  // KARAR YOK
```

### C/C++

```c
/* PSEUDOCODE — fixture arayuzu (firmware K005'te) */
/* fixture_bagla();          seri/USB        */
/* olcum_baslat(512, 48000); ADR-017 sabit   */
/* karar: fonksiyon degil, KAPI — §5         */
```

### Rust

```rust
// PSEUDOCODE — rapor araci
// let kayit = Kayit { snr: "UNKNOWN", measured: false };
// yaz(kayit)?;   // JSONL — uydurma deger yasak
```

### PHP 8.x

```php
<?php
// PSEUDOCODE — uretim takibi (PDO parametreli)
// $st = $pdo->prepare('INSERT INTO prod_log (part, station, karar) VALUES (:p,:s,:k)');
// KARAR/KAPI PHP'de DEGIL — yalniz kayit
```

### JS/TS

```javascript
// PSEUDOCODE — rapor okuma
// const r = await fetch('/api/report');
// render(r);   // gosterme — KARAR YOK
```

## §12 OS × Dil Uygunluk Matrisi (öneridir · `⚠️`)

| OS ↓ / Dil → | Python | C# | C/C++ | Rust | PHP | JS/TS | Shell | Asm |
|---|---|---|---|---|---|---|---|---|
| Windows | **3** | **3** | 2 | 2 | 2 | 3 | 2 | 0 |
| macOS | **3** | 1 | **3** | 2 | 2 | 3 | 2 | 0 |
| Linux | **3** | 1 | **3** | 2 | **3** | 3 | **3** | 0 |
| iOS | 0 | 0 | 1 | 0 | 1 | 2 | 0 | 0 |
| Android | 1 | 0 | 1 | 0 | 1 | 2 | 0 | 0 |
| Fabrika/RTOS | **3** | 2 | **3** | 2 | 0 | 1 | **3** | 1 |

> 0 = önerilmez · 1 = sınırlı · 2 = uygun · 3 = tercih edilen · ölçüm **DEĞİLDİR**.

## §13 Örnek ↔ Kural Eşlemesi

| # | Örnek | Anayasa kuralı | Kısıt |
|---|---|---|---|
| 1 | §3 test planı | §3 DFT | kod K005/QA |
| 2 | §4 ölçüm kaydı | §5 SNR/THD | `measured:false` |
| 3 | §5 burn-in | §7 | 8 saat · L0-10 |
| 4 | §6 AOI | §4 | limit yok `⚠️` |
| 5 | §7 Gerber | §2 | pcb-design `⚠️` |
| 6 | §10 fabrika | §18 anayasa | ADR-017 |

## §14 Yasaklar

| # | Yasak | Neden |
|---|---|---|
| 1 | SNR/THD sonucu uydurma | Zero-Hallucination · §N-7 |
| 2 | AOI limit yüzdesi | ölçüm yok |
| 3 | EMC standart numarası | Q-IM1 `⚠️` |
| 4 | Test otomasyon kodu | K005/QA |
| 5 | Ölçüm UI/script | QA/K004 `⚠️` |
| 6 | Web üretim takibi kodu | PHP katmanı |
| 7 | 16/24-bit kaynak | ADR-017 |

## §15 Ölçüm Girdi Tablosu

| Girdi | Birim | Durum | Kaynak |
|---|---|---|---|
| SNR | dB | `UNKNOWN` | ölçüm yok |
| THD+N @1 kHz | % | `UNKNOWN` | ölçüm yok |
| Frekans yanıt | dB | `UNKNOWN` | okunmadı |
| AOI fail | % | `UNKNOWN` | limit yok |
| Burn-in süre | saat | 8 | §B.5(6) |
| DC offset | V | `UNKNOWN` | eşik 0.5 (L0-10) |
| Sıcaklık | °C | `UNKNOWN` | uydurma yasak |

## §16 Karar Ağacı

| Soru | Evet | Hayır |
|---|---|---|
| Ölçüm yapıldı mı | `measured:true` + değer | `UNKNOWN` yaz |
| Tüm kapılar geçti mi | "geçti" damgası | "kaldı" |
| DC >0.5 V mı | çekirdek röle açar | devam |
| Kod yüzeyi mi | K005/QA'ya devret | plan burada |
| Takip yüzeyi mi | PHP PDO | — |
| Sertifika mı | dış laboratuvar | pre-scan burada |

## §17 Açık Sorular

| ID | Soru | Etiket |
|---|---|---|
| Q-IM1 | EMC standart numaraları | `⚠️` |
| Q-IM2 | SNR/THD fiziksel ölçümü (PLANNED) | `⚠️` |
| Q-IM3 | DFT/AOI satır kanıtı | `⚠️` |
| Q-O1 | Elektronik kod örneği | açık |
| Q-B1 | K004/K005 disk durumu | açık |

## §18 Kapı ve Doğrulama

| Kapı | Aşama | Durum |
|---|---|---|
| Mockup | kod/plan görseli yok | N/A |
| LINE-500 | bu dosya ≥500 | ölçülür |
| Mojibake-0 | UTF-8 regex | 0 ✓ |
| FORMAT | frontmatter 9 alan | ✓ |
| Commit | subagent ATMAZ | ATILMADI |

## §19 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu (9/10) |

## §20 OS Ayrıntı Tabloları (her OS — öneri · `⚠️`)

### Windows

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | CAD/fabrika | istemcisi (C#/Python) | test |
| 2 | Ölçüm | CLI → JSONL | test |
| 3 | Rapor | ekran/HTML | okuma |
| 4 | Takip | PHP (kaynak Linux/PHP olabilir) | §11(5) |
| 5 | Karar | Yok — kapı ölçümde | §16 |

### macOS

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Ölçüm | CLI (Python/C++) | test |
| 2 | Log | JSONL | dürüstlük |
| 3 | Rapor | CLI | okuma |
| 4 | Takip | PHP PDO | §11(5) |
| 5 | Karar | Yok | §16 |

### Linux

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Ölçüm sunucusu | Python/shell (kod K005/QA) | devralınan |
| 2 | Log | CSV/JSONL | dürüstlük |
| 3 | Takip | PHP 8.x PDO | §7 |
| 4 | Rapor | web (JS okuma) | okuma |
| 5 | Karar | Yok | §16 |

### iOS / Android (birlikte)

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Üretim/ölçüm | **YOK** | — |
| 2 | Rapor okuma | uygulama içi `⚠️` | okuma |
| 3 | Fixture | **YOK** | — |
| 4 | Karar | Yok | §16 |

### Fabrika / RTOS (fixture)

| # | Bileşen | Öneri | Kural |
|---|---|---|---|
| 1 | Hat | seri/USB | `⚠️` |
| 2 | ICT/FCT | plan §3 | kod K005/QA |
| 3 | Ölçüm | SNR/THD kapısı | §4 |
| 4 | Burn-in | 8 saat + log | §5 |
| 5 | Röle | 0.5 V → çekirdek | L0-10 |

## §21 Dil Ayrıntı Tabloları (her dil)

| # | Dil | Ölçüm | Üretim kaydı | Rapor | Kaçınılan |
|---|---|---|---|---|---|
| 1 | Python | **EVET** | CSV/JSONL | — | kapı kararı |
| 2 | C#/.NET | **EVET** | — | ekran | kapı kararı |
| 3 | C/C++ | fixture | — | — | K005 aşımı |
| 4 | Rust | araç | JSONL | — | — |
| 5 | PHP 8.x | — | **EVET** (PDO) | — | karar/kapı |
| 6 | JS/TS | — | — | **EVET** | karar |
| 7 | PowerShell/shell | hat | — | — | sorumluluk K005/QA |
| 8 | Assembly | **HAYIR** | — | — | — |

## §22 Biçim Sözlüğü (veri yüzeyi)

| Alan | Biçim | Değer | Not |
|---|---|---|---|
| `snr_db` | dB | `UNKNOWN` | hedef `snr_target_db:100` |
| `thd_n_percent` | % | `UNKNOWN` | hedef 0.01 @1000 Hz |
| `measured` | bool | `false` | dürüstlük |
| `sample_block` | int | 512 | ADR-017 |
| `sample_rate_hz` | int | 48000 | ADR-017 |
| `source_format` | string | `float32` | ADR-017 |
| `karar` | string | `GECTI`/`KALDI` | yalnız tüm kapılar geçince |

## §23 Kısıt ↔ Örnek Çaprazı

| # | Kısıt | Örnekteki karşılığı |
|---|---|---|
| 1 | SNR >100 dB | §4 hedef alanı · §10 fabrika |
| 2 | THD+N <0.01% | §4 hedef alanı · §10 |
| 3 | 512/48k | §4 · §10 |
| 4 | float32 | §4 · §11 C |
| 5 | L0-10 0.5 V | §5 · §10 |
| 6 | 8 saat | §5 · anayasa §7 |
| 7 | §N-7 PLANNED | §1 `measured:false` |

## §24 Kaynak Atıf Sözlüğü

| Atıf | Ne verir |
|---|---|
| `electroncis-ornekler.md` §7 · §15.1 | dürüstlük + iskelet |
| anayasa §2-§11 | kural bağları |
| `electroncis-kapsam.md` §B.5 · §H.5 | kapsam + satır kanıtları `⚠️` |
| K000 isletim-sistemi | OS atıfları (salt-okunur) |
| ADR-017 · ADR-038 · L0 | kısıtlar |

## §25 Dürüstlük İddia Matrisi

| # | İddia | Bu dosyada | Kanıt |
|---|---|---|---|
| 1 | Ölçüm sonucu | **YOK** | `measured:false` |
| 2 | AOI limiti | **YOK** | `limit=UNKNOWN` |
| 3 | Standart numarası | **YOK** | Q-IM1 |
| 4 | Yeni envanter iddiası | **ÜRETİLMEDİ** | V6 |
| 5 | Register/URL | **YOK** | — |
| 6 | Tahmini | `⚠️` | Zero-Hallucination |

## §26 Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | `electroncis` yazım | ✓ |
| 2 | PSEUDOCODE/plan etiketli bloklar | ✓ |
| 3 | Ölçüm/limit/standart yok | ✓ |
| 4 | OS 6 · dil 8 | ✓ |
| 5 | Mojibake 0 | 0 ✓ |
| 6 | Frontmatter 9 alan | ✓ |
| 7 | K000 salt-okunur | ✓ |
| 8 | Commit | ATILMADI ✓ |

## §27 Ölçüm Tablosu (LINE-500 — nihai)

| # | Dosya | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 0 | Ölçüm yeri | repo kökünden (§13.6) | `ReadAllLines().Count` | zorunlu |
| 1 | `imalat-ve-test-ornekler.md` | ≥500 | ölçülür | — |
| 2 | Mojibake | 0 | 0 | ✓ |
| 3 | `parent:` alan hatası | 0 | 0 | ✓ |
| 4 | Tırnaklı `group` | 0 | 0 | ✓ |
| 5 | Commit | — | ATILMADI | ✓ |

## §29 Devir Bildirimi (orkestratöre)

| # | Bildirim | Durum |
|---|---|---|
| 1 | K2-IM 3/4 dosya yazıldı (index · anayasa · ornekler) | ✓ |
| 2 | Ölçüm: 100 / 500 / 481+ | ✓ (bu dosya ≥500 ölçülür) |
| 3 | Mojibake 0 (3 dosya) | ✓ |
| 4 | Ölçüm/limit/standart uydurması | YOK ✓ |
| 5 | Commit ATILMADI | devir |
| 6 | Kalan: `imalat-ve-test-dogrulama.md` | son dosya (10/10) |
| 7 | Kök index/master dokunulmadı | devir |
| 8 | K000-isletim-sistemi salt-okunur | ✓ |
| 9 | Registry (`.ai/architecture/index.md`) yazılmadı | ✓ |

| # | Sonraki adım | Sahip |
|---|---|---|
| 1 | `imalat-ve-test-dogrulama.md` yazımı | bu oturum |
| 2 | 10 dosya BBC-1/BBC-2 kök ölçümü | orkestratör |
| 3 | Kök index children + M-tablosu | orkestratör |

---

**Authority:** `electroncis-ornekler.md` §7 · §15.1 · `.ai/architecture/K002-electroncis/imalat-ve-test/imalat-ve-test-anayasa.md` §2-§11 · ADR-017
