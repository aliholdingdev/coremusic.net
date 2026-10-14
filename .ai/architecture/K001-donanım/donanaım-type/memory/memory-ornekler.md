---
title: "memory — Örnekler (Repo Kanıtı + Adım Şablonları)"
type: branch-ornekler
status: proposed
node_id: K001-donanım/donanaım-type/memory/memory-ornekler
node_type: donanaım-type-ornekler
parent_id: "K001-donanım/donanaım-type"
branch_authority: ADR-102
group: kod
date: 2026-10-13
updated: 2026-10-13
---

> **Yazım notu**: `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak.
> **PROPOSED (ADR-102 taslak)** — anayasa/üst kapsam revize edilmedi.
>
> **Dal açma yetkisi**: `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` §4
> (memory = bellek donanımı; bellek yönetim algoritması + sanal bellek → K000-isletim-sistemi).
>
> **Bu dosyanın işi**: (1) depodaki **gerçek** bellek kod/kanıtını göstermek —
> **donanım ise "yok" der**, yazılımsa **sınıra işaret eder**; (2) envanter/ölçüm
> için **adım şablonları**. **UYDURMA dosya/sürüm/çıktı ÜRETİLMEZ.**

---

## 1. §1 — Depodaki gerçek bellek kodu

### 1.1 Donanım kanıtı araması (ölçüm: 2026-10-13)

| Aranan terim | Eşleşme | Sonuç |
|---|---|---|
| `\bddr\b` · `ddr[45]` | 0 · 0 | **DDR modülü yok** |
| `\bdimm\b` · `so-?dimm` | 0 · 0 | **Modül kaydı yok** |
| `\bnvram\b` | 0 | NVRAM yok |
| `spi.?flash` · `\bnor\b/\bnand\b` | 0 · 0 | Gömülü flash çipi yok |
| `eeprom` | 1 / 1 dosya | `.claude/skills/prompt-maker/.archive/questions-block-h.md:57` — **soru arşivi** |
| `flash` (ham) | 13 / 8 dosya | CSS FOUT · flash loan · linker script → **donanım 0** |
| `\bram\b` (sınırlı) | 8 / 6 dosya | Agent/prompt metni → **donanım envanteri 0** |

**§1.1 SONUÇ (QUANTIFIED)**: **Depoda bellek donanımı kod/kanıtı = YOK.**
(Q-M1 · kapı M2 kapalı.)

### 1.2 Gerçekte var olan kod: `shared/src/Cache/` (SINIR KANITI)

Depoda **gerçek** 6 cache sınıfı var — ama bunlar **yazılımdır** ve bu dalın
**DIŞINDA** olduğunu **kanıtlar** (ölçüm: 857 `cache` → PHP 146/30):

| # | Dosya/sınıf | Rol | Kapsam kararı |
|---|---|---|---|
| 1 | `CacheInterface` | Cache sözleşmesi | ❌ → K000 (yazılım) |
| 2 | `CacheManager` | Kayıt/yaşam döngüsü | ❌ → K000 |
| 3 | `ApcuAdapter` | PHP APCu (RAM cache'i) | ❌ → K000 |
| 4 | `MemoryAdapter` | Bellek içi cache | ❌ → K000 |
| 5 | `PageCacheAdapter` | Sayfa cache'i | ❌ → K000 |
| 6 | `PageCacheInterface` | Sayfa cache sözleşmesi | ❌ → K000 |

> **Neden §1'de?** Bu sınıflar **"cache" teriminin depoda donanım değil,
> `CoreMusic\Cache\` namespace'i olduğunu** **dosya kanıtıyla** ispatlar
> (dürüstlük: `memory-dogrulama.md` C-M04/§5.2). **Yazılım kodunu bu dala
> dahil etmiyoruz** — sınır **işareti** olarak kullanıyoruz.

### 1.3 Özet tablo

| İstenen kanıt | Durum | Nereye bakıldı |
|---|---|---|
| RAM/DDR/DIMM envanteri | **YOK** (Q-M1) | 1338 metin dosyası (ölçüm §1.1) |
| EEPROM/flash çip modeli | **YOK** (Q-M1) | — |
| Önbellek **donanımı** | **YOK** (Q-M2) | `cache` 857 = yazılım |
| Ölçülmüş bellek metriği | **YOK** (Q-M3) | — |
| **Yazılım cache kodu** | **VAR** (6 sınıf) | `shared/src/Cache/` → **K000 sınırı** |

---

## 2. §2 — Adım şablonu: bellek donanımı envanteri çıkarma (kapı M2)

> **Kural**: her adım **gerçek komut/gözlem** ile çalışır; çıktılar ölçümle
> doldurulur. `MEASURE`/`UNKNOWN` alanları **yer tutucudur** — **uydurulmaz**.

### 2.1 Adım adım

1. **Fiziksel sayım** — kasayı aç/gözle say: RAM modülü slot/dolu · M.2 SSD ·
   anakart üzerindeki EEPROM/flash çip(dizileri) → `envanter-fiziksel.md`
   (tarih + gözlemci).
2. **RAM okuma** — OS bellek bilgisi: Windows `Get-CimInstance Win32_PhysicalMemory`
   (Speed · Capacity · DeviceLocator) · Linux `dmidecode -t memory`
   → **DDR nesli + kapasite + hız** (S4 kanıt).
3. **Form faktörü** — DIMM vs SO-DIMM, slot doluluk, kanal (single/dual) → §2.2.
4. **Kalıcı bellek** — EEPROM/flash çip modeli (etiket/datasheet) → §2.2 satır 4/5.
5. **Önbellek donanımı** — varsa önbellek diski/HBM → §2.2 satır 6 (yoksa `YOK`).
6. **Kaydet** — sonuçları `memory-kapsam.md` §2.4'teki **UNKNOWN** alanlarının
   yerine doldur; **kapı M2/M3** tekrar çalıştırılır.

### 2.2 Boş envanter formu (doldurulacak)

| # | Alan | Değer (ölçümle) | Kanıt (S4) | Tarih |
|---|---|---|---|---|
| 1 | RAM tipi (DDR4/DDR5) | `MEASURE` | `dmidecode` çıktısı | `YYYY-MM-DD` |
| 2 | Kapasite / slot doluluk | `MEASURE` | gözlem + okuma | `YYYY-MM-DD` |
| 3 | Hız (MT/s) / kanal | `MEASURE` | okuma | `YYYY-MM-DD` |
| 4 | EEPROM modeli | `MEASURE` | çip etiketi | `YYYY-MM-DD` |
| 5 | Gömülü flash çipi | `MEASURE` | çip etiketi | `YYYY-MM-DD` |
| 6 | Önbellek donanımı | `MEASURE`/`YOK` | — | `YYYY-MM-DD` |

### 2.3 Envanter çıktısı — standart şema (JSON)

Çıktı tek bir `envanter-memory.json` olur (N2/M2 kapısı **makinece** denetlenir):

```json
{
  "measured_at": "YYYY-MM-DD",
  "method": "<komut/aracı + sürüm — gerçek>",
  "ram": [
    {
      "type": "<DDR4|DDR5 | UNKNOWN>",
      "capacity_gb": null,
      "speed_mts": null,
      "form_factor": "<DIMM|SO-DIMM | UNKNOWN>",
      "ecc": null
    }
  ],
  "persistent": [
    { "kind": "<EEPROM|SPI-NOR|SPI-NAND>", "part_number": "<etiket — tahmin YASAK>", "location": "<yer>" }
  ],
  "cache_hardware": null,
  "open_questions": ["Q-M1", "Q-M2"]
}
```

**Doğrulama kuralları**: `part_number` **yalnız çip etiketinden**/komut
çıktısından gelir — okunamıyorsa `UNKNOWN` · `ram.type` tahmin edilmez ·
`cache_hardware` donanım **yoksa** `null` (boş bırakılmaz, `null` yazılır).
**Şemaya uymayan çıktı M2'de sayılmaz.**

---

## 3. §3 — Adım şablonu: bellek ölçüm formu (kapı M6)

### 3.1 Ölçüm formu (boş)

| # | Metrik | Yöntem | Değer | Tarih |
|---|---|---|---|---|
| 1 | Okuma gecikmesi (ns) | ölçüm aracı | `MEASURE` | `YYYY-MM-DD` |
| 2 | Yazma gecikmesi (ns) | ölçüm aracı | `MEASURE` | `YYYY-MM-DD` |
| 3 | Bant genişliği (GB/s) | ölçüm aracı | `MEASURE` | `YYYY-MM-DD` |
| 4 | Kanal durumu (single/dual) | sistem okuması | `MEASURE` | `YYYY-MM-DD` |

### 3.2 Adımlar

1. **Ortam** — makine + OS + ölçüm aracı (**gerçekten** kuruluysa sürümü yaz).
2. **3× tekrar** — medyan yaz (tek ölçüm yeterli değil).
3. **Sonuç** — 3.1'e gerçek sayı + tarih; **yorum yok** (karar üst dalın).

### 3.3 Ölçüm aracına dair dürüstlük kuralları (araç adı uydurulmaz)

| # | Kural | Açıklama |
|---|---|---|
| 1 | **Araç gerçekten kuruluysa adı + sürümü yazılır** | Kurulu değilse araç **adı yazılmaz** — "ölçüm aracı" (belirsiz) kalır |
| 2 | **Çıktı = ham okuma** | Ekran görüntüsü/komut çıktısı dosyaya iliştirilir (S4); elle yazılmış sayı **S3** |
| 3 | **Tek ölçüm geçersiz** | 3× tekrar + medyan zorunlu (§3.2/2) |
| 4 | **Ortam yazılır** | OS/sürücü/diğer süreçler ölçümü etkiler → ortam **zorunlu alan** |
| 5 | **Kanal/eksen notu** | Dual-channel ölçümü ile tek-kanal **karıştırılmaz** (§4.2 ile eşleştirme) |

**Durum**: şu an **araç kurulu değil / ölçüm yapılmadı** → 3.1 tamamen
`MEASURE` (Q-M3 · kapı M6 ⚠️).

---

## 4. §4 — Adım şablonu: bellek hesabı (formül → tablo)

### 4.1 Formüller

```
GB/sn = MT/s × 64 bit (kanal) / 8 / 1.000.000.000   (kaba bant genişliği)
bayt  = kanal × örnek × bit/8 × süre                (tampon bellek ihtiyacı)
```

### 4.2 Kaba bant genişliği tablosu (formül uygulandı)

| Bellek hızı | Kanal | Bit | **GB/sn (kaba)** |
|---|---|---|---|
| DDR4-3200 | 2 (dual) | 64 | **51,2** |
| DDR5-4800 | 2 (dual) | 64 | **76,8** |
| DDR5-5600 | 2 (dual) | 64 | **89,6** |

> **Not**: sayılar **formül çıktısıdır (S2)** — **ölçüm değil** (kapı M6 ⚠️).
> Kesin değer için §3 ölçümü gerekir.

### 4.3 Ses tamponu hesabı (gerçek hesap örneği)

**Soru**: 2 kanal × 32 bit × 192 kHz sesi **1 saniyelik** tamponsuz akıtmak
için bellek?

```
adım 1: bayt/sn = 2 × (32/8) × 192.000 = 1.536.000 bayt/sn
adım 2: 1 sn tampon = 1.536.000 bayt ≈ 1,46 MiB
adım 3: 100 ms gecikme bütçesi için = 153.600 bayt ≈ 150 KiB
sonuç : gerçek-time audio tamponu bellekte ihmal edilebilir düzeyde küçük
```

**Yorum**: ses akışı **bellek darboğazı değildir**; darboğaz **ölçümle**
(§3) doğrulanır — **hesap ölçüm yerine geçmez**.

### 4.4 Tampon çarpanı tablosu (formülün sistematik uygulaması — S2)

Formül: `bayt = 2 × (32/8) × 192.000 × süre` (192 kHz / 32 bit / 2 kanal):

| Süre | bayt | ≈ KiB/MiB |
|---|---|---|
| 1 ms | 1.536 | 1,5 KiB |
| 10 ms | 15.360 | 15 KiB |
| 50 ms | 76.800 | 75 KiB |
| 100 ms | 153.600 | 150 KiB |
| 1 s | 1.536.000 | 1,46 MiB |
| 10 s | 15.360.000 | 14,65 MiB |

**Okuma kuralı**: **gerçek-time** tamponlar (1–100 ms) **KiB** mertebesinde
→ bellekte **ihmal**; yalnızca **on-dakika ölçeği** MiB'ye çıkar. Bu tablo
**hesaptır (S2)** — **ölçüm değildir** (kapı M6 ⚠️); kesinlik için §3.3
kuralları uygulanır.

---

## 5. §5 — Adım şablonu: sınır testi (donanım mı, yazılım mı?)

`memory-kapsam.md` §3.1'deki **3 test** uygulanır:

| # | Öğe | Test 1 (fiziksel?) | Karar |
|---|---|---|---|
| 1 | DDR5 DIMM modülü | ✅ modül/pin | **bu dal** |
| 2 | `CoreMusic\Cache\MemoryAdapter` | ❌ kod | **K000** |
| 3 | `font-display: swap` | ❌ CSS | **UI** (ölçüm gürültüsü) |
| 4 | SPI NOR flash çipi | ✅ çip | **bu dal** |
| 5 | Flash'a yazan sürücü | ❌ kod | **K000** |
| 6 | Sayfa tablosu/TLB politikası | ❌ algoritma | **K000** |

**Adım**: her yeni kayıt §5 tablosundan geçirilir; **karar + test sonucu**
`memory-kapsam.md` §2/§3'e işlenir.

### 5.1 Sınır-karar kaydı şablonu (her kayıt için satır)

| Alan | Zorunlu içerik |
|---|---|
| `item` | Kaydın adı (dosya/sınıf/çip) |
| `test_1_physical` | `evet`/`hayır` (+ kanıt: gözlem/komut) |
| `test_2_code_only` | `evet`/`hayır` (yalnız kodla değişir mi?) |
| `test_3_firmware` | `evet`/`hayır` (sürüm/parametre mi, çipin kendisi mi?) |
| `decision` | `memory` · `K000` · `K002` · `ssd/` · `cpu/` · `UI` |
| `evidence` | S4/S3/`UNKNOWN` |
| `date` | `YYYY-MM-DD` |

**Kural**: karar **gerekçesiz** (test sonucu olmadan) **yazılmaz**; `UNKNOWN`
bir **karar** değil, **bekleme**dir → kapıya (M2/M3) bağlanır.

---

## 6. §6 — Kaçınma listesi (bu dalda yapılmayanlar)

| # | Kaçınma | Gerekçe |
|---|---|---|
| 1 | 857 `cache` sayısını **donanım önbellek** saymak | C-M04: PHP 146/30 + non-PHP 711/136 = yazılım |
| 2 | `swap` 64'ü **OS swap** saymak | C-M05: 57'si `font-display: swap` |
| 3 | `ram` ham 2575'i **envanter** saymak | C-M03: gürültü → `\bram\b` 8/6 |
| 4 | Bellek yönetim/swap kodu **bu dala almak** | ADR-102 §4: → K000 |
| 5 | Uydurma RAM/EEPROM/flash modeli | ZERO-HALLUCINATION (S1 yasak) |
| 6 | 404/JEDEC alt sayfasını **kaynakça** koymak | `memory-dogrulama.md` §6.2 (W-X2) |
| 7 | Hız **hesabını ölçüm** diye sunmak | §4 = hesap; §3 = ölçüm |
| 8 | ADR-038 / PCM5122 / ADR-089'a **dokunmak** | ADR-102 §4.2 (dokunulmaz) |

---

## 7. Dürüstlük bloğu (QUANTIFIED)

- **§1 sonucu**: Depoda **bellek donanımı kod kanıtı YOK** — `ddr` `dimm` `nvram`
  `spi flash` `nor/nand` = **0**; donanım `flash` **0**; `eeprom` **1** (arşiv) —
  ölçüm 2026-10-13, 1338 dosya. **Uydurma kod üretilmedi.**
- **Sınır kanıtı**: `shared/src/Cache/` **6 sınıf gerçek dosya** (§1.2) →
  **%100 yazılım** → K000; donanım **değil**, **sınır işareti** olarak kullanıldı.
- **Ölçüm gürültüsü**: `ram` 2575 (ham) · `cache` 857 · `swap` 64 — **üçü de**
  donanım sayımı olarak **yorumlanmadı** (sınırlı sayı + dosya sınıflandırması).
- **Şablonlar**: §2/§3 formları **`MEASURE`/`UNKNOWN` ile boş** —
  **uydurma değer yazılmadı**.
- **Hesap (§4)**: DDR bant + ses tamponu formülleri **doğru** (DDR5-5600 dual
  89,6 GB/sn; 192k/32b/2ch = 1,46 MiB/sn) — **kaynak = formül (S2)**,
  **ölçüm değil** (kapı M6 ⚠️).
- **Kısıt**: ADR-038 · PCM5122 · ADR-089 **dokunulmaz**; güç/PCB → K002;
  JEDEC **1 URL erişildi**, 404 URL **elendi**.
- **Belirsizlik**: Q-M1 · Q-M2 · Q-M3 **açık**; kapılar **M2 ❌ · M3 ❌ · M6 ⚠️**
  → dal **proposed**.
- **Kaynak yeniden ölçümü (2026-10-13 — §10)**: jedec.org HEAD **403** →
  sayısal JEDEC iddiaları **⚠️ VERIFICATION REQUIRED**; docs.kernel.org ·
  kernel.org/doc/html/latest **200** (S5); uefi.org **403** (kullanılmadı).
  **Bu dosyada yeni URL uydurulmadı** — §10.1 yalnız HEAD sonucu taşır.
- **Bu dosyadaki tüm adımlar (§8–§11) çalıştırılmadı** — şablondur;
  çıktı yok · ölçüm 0 · `MEASURE`/`UNKNOWN` alanları **işaretlendi**
  (uydurma değer yazılmadı).

---

## 8. §8 — Adım şablonu: DDR nesli / modül formu tespiti (kapsam §10.4 karşılığı)

> **Aynı eksen** (`memory-kapsam.md` §10): DDR4/DDR5 · DIMM/SO-DIMM · JEDEC.
> Bu bölüm **adım/şablon** verir — **hiçbir adım bu oturumda çalıştırılmadı**.

### 8.1 Adımlar (doldurulacak)

1. **Ortam kaydı** — OS · tarih · araç (**gerçekten** kuruluysa adı + sürüm).
2. **Nesil okuma** — OS bellek bilgisi (K000 aracı — §7 sınır testi/6):
   DDR4 mü DDR5 mi → `§2.2/1` satırı · **tahmin yasak**.
3. **Modül formu** — DIMM / SO-DIMM / lehimli: **gözlem** (kasayı aç) veya okuma.
4. **Hız/kanal** — okunan MT/s + single/dual → §3.1/3-4 satırları.
5. **ECC bayrağı** — okuma/gözlem → §9.2 formu.
6. **Kaynak disiplini** — JEDEC **403** → sayısal değer **⚠️ VERIFICATION
   REQUIRED** olarak yazılır (§10).
7. **Kaydet** — sonuçları `envanter-memory.json` şemasına (§2.3) işle; kapı M2.

### 8.2 Boş tespit formu (kapsam §10.4 karşılığı)

| # | Alan | Değer | Yöntem | Tarih |
|---|---|---|---|---|
| 1 | Nesil (DDR4/DDR5) | `MEASURE` | okuma (K000 aracı) | `YYYY-MM-DD` |
| 2 | Modül formu | `MEASURE` | gözlem/okuma | `YYYY-MM-DD` |
| 3 | Hız (MT/s) | `MEASURE` | okuma | `YYYY-MM-DD` |
| 4 | Kanal (single/dual) | `MEASURE` | okuma | `YYYY-MM-DD` |
| 5 | ECC | `MEASURE` | okuma + gözlem | `YYYY-MM-DD` |
| 6 | Kaynak seviyesi | `S4` (komut çıktısı) | çıktı iliştirilir | `YYYY-MM-DD` |

### 8.3 Yanlış/doğru yazım örnekleri (JEDEC 403 sonrası)

```text
YANLIŞ (uydurma/yanlış kaynak):
  "DDR5-5600 · 288-pin — kaynak: jedec.org ✅"
  neden : jedec.org bu oturumda HEAD 403 → kaynak DEĞİL (dogrulama §6.4.1).
DOĞRU:
  "Nesil: MEASURE (okuma YOK) · hız: MEASURE · kaynak: komut çıktısı (S4, tarih)"
  veya  : "DDR5 hız değeri ⚠️ VERIFICATION REQUIRED — JEDEC erişimi 403 (2026-10-13)."
```

---

## 9. §9 — Adım şablonu: ECC tespiti ve hata tespit/düzeltme kaydı

### 9.1 Adımlar

1. **Fiziksel/gözlem** — modül etiketinde ECC işareti var mı? (gözlem + tarih).
2. **Okuma** — OS bellek okumasında ECC durumu (K000 aracı) → §9.2/1.
3. **Ayrım** — ECC **donanım yeteneği** bu dalda; hata **politikası** K000
   (`kapsam` §11.2/5) → kayıt ikiye bölünür.
4. **Hata tespit kaydı** — tespit/düzeltme **donanım** katmanları (`kapsam`
   §11.2/1-3) bu dalda; **istatistik/log** kapı M6'ya (ölçüm).
5. **Kaydet** — ECC=YES/NO/MEASURE + kaynak; "ECC kullanılıyor" cümlesi
   **yalnız** 1-2 doluysa.

### 9.2 Boş ECC formu

| # | Alan | Değer | Kaynak (S4) | Tarih |
|---|---|---|---|---|
| 1 | ECC durumu (var/yok) | `MEASURE` | — | `YYYY-MM-DD` |
| 2 | Görsel kanıt (etiket) | `MEASURE` | — | `YYYY-MM-DD` |
| 3 | Düzeltme devresi kaydı | `MEASURE` | — | `YYYY-MM-DD` |
| 4 | Hata istatistiği | **K000** (bu dalda değil) | — | — |

### 9.3 Sınır provası (3 test uygulaması — `kapsam` §3.1)

| # | Öğe | T1 fiziksel? | T2 yalnız kod? | T3 sürüm/çip? | Karar |
|---|---|---|---|---|---|
| 1 | ECC DIMM modülü | ✅ | — | — | **bu dal** |
| 2 | ECC denetim devresi | ✅ | — | — | **bu dal** |
| 3 | ECC hata **logu** | ❌ | ✅ | sürüm/log | **K000** |
| 4 | ECC politikası (retry/crash) | ❌ | ✅ | — | **K000** |
| 5 | `ecc` 3 eşleşme (repo) | ❌ | — | doküman | **envanter değil** (dogrulama §4) |

---

## 10. §10 — Adım şablonu: kaynak kullanımı (jedec.org 403 sonrası)

### 10.1 URL kullanım matrisi (bu dal — 2026-10-13)

| URL | HEAD | Kullanım | İşaret |
|---|---|---|---|
| `https://www.jedec.org/` | **403** | yalnız "standart kuruluşu" düzeyi | ⚠️ VERIFICATION REQUIRED (sayısal) |
| `https://docs.kernel.org/` | 200 | K000 sınırı / sürücü dokümanı | S5 |
| `https://kernel.org/doc/html/latest/` | 200 | çekirdek doküman kökü | S5 |
| `https://uefi.org/` | **403** | **kullanılmaz** | KULLANILMADI |
| `jedec.org/.../main-memory-ddr-sdram-hbm` | 404 | **kullanılmaz** | W-X2 elendi |

### 10.2 Doğru atıf satırı örnekleri

```text
DOĞRU  : "docs.kernel.org — HEAD 200, erişim 2026-10-13 (K000 sürücü sınırı kaynağı)."
DOĞRU  : "JEDEC standart kuruluşu olarak anıldı; sayısal değer VERIFICATION REQUIRED."
YANLIŞ : "jedec.org ✅ erişildi 2026-10-13" (yeniden ölçüm 403 — bu cümle
         yalnız TARİHSEL kayıt olarak kalır, yeni dosyaya yazılmaz).
```

### 10.3 Kaçınma eki (§6 listesinin devamı)

| # | Kaçınma | Gerekçe |
|---|---|---|
| 9 | 403 URL'yi "eski 200" diye savunmak | §6.4.1 — append-only çelişki kaydı |
| 10 | Pin/hız/gerilim **sayısı** yazmak (kayınsız) | JEDEC 403 → VR |
| 11 | "ARA" derken sayı uydurmak (`m2`/`nvme`/`hbm`) | kapsam §12.3 — aranmadı = UNKNOWN |

---

## 11. §11 — Adım şablonu: M.2 / stacking ve self-refresh sınır testi

### 11.1 M.2 yüzeyi — 3 test uygulaması

| # | Öğe | T1 | T2 | T3 | Karar |
|---|---|---|---|---|---|
| 1 | M.2 NAND/SSD modülü | ✅ | — | — | **`ssd/`** (kapsam §2.2/9) |
| 2 | M.2 DRAM modülü (varsa) | ✅ | — | — | **bu dal** |
| 3 | M.2 SPI flash (firmware çipi) | ✅ | — | çipin kendisi | **bu dal** (sürüm → K000) |
| 4 | NVMe etiketi | ❌ | etiket | — | **K000/ssd** — katman-4 yasak |
| 5 | M.2 yuvası (konnektör) | ✅ | — | — | sınır — anakart yüzeyi |

### 11.2 self-refresh sınır testi

| # | Öğe | T1 | T2 | T3 | Karar |
|---|---|---|---|---|---|
| 1 | self-refresh (çip davranışı) | ✅ | — | — | **bu dal (özellik)** |
| 2 | self-refresh politikası | ❌ | ✅ | — | **K000** |
| 3 | VDD/güç ölçümü | ✅ | — | — | **K002** (ADR-102 §4.2) |
| 4 | Düşük güç durumu (APD/S4) | ❌ | ✅ | — | **K000** |
| 5 | Bellek güç ölçümü (bu oturum) | — | — | — | **0** → kapı M6 ⚠️ |

### 11.3 OS tespiti sınırı — bu dosyada YAZILMAYANlar

| # | Yazılmaz | Nereye bakılır |
|---|---|---|
| 1 | `dmidecode`/`Get-CimInstance` benzeri **kullanım kılavuzu** | K000 os-donanim-tespiti |
| 2 | Bellek okuma **kodu** (script/functions) | K000 (kod üretim yasağı) |
| 3 | Sayfa tablosu/swap **şeması** | K000 |
| 4 | SPD **okuma algoritması** | K000 (SPD çipi envanteri bu dalda) |
| 5 | "RAM kaç GB" **cevabı** | envanter gelince M2 (§2.2/§8.2) |

**Devir satırı (tek cümlelik sınır)**: `tespit yöntemi → K000 · tespit edilen
donanım gerçeği → memory (K001) · güç/PCB → K002`.

---

## 12. §12 — Adım şablonu: önbellek DONANIMI sorgusu (kapı M3 — Q-M2)

> `kapsam` §2.3/10 ve `dogrulama` §7 (M3) ile **aynı eksen**. Bu bölüm
> **donanım** önbelleğini arar — **yazılım cache'ini DEĞİL** (§1.2).

### 12.1 Adımlar

1. **Tanımı tazele** — "önbellek donanımı" = **fiziksel** hızlı katman
   (ör. ayrı önbellek diski / gömülü önbellek donanımı) — `CoreMusic\Cache\`
   **değil** (§1.2/6 sınıf).
2. **Donanım sorgusu** — K000 envanter aracı (donanım listesi) → fiziksel
   öğe var mı? **Kayıt**: `VAR` / `YOK` / `MEASURE` (üçü de sonuçtur).
3. **Sınıra dikkat** — CPU L2/L3 → `cpu/` sınırı (kapsam §2.3/11); SSD
   iç önbelleği → `ssd/` (kapsam §2.2/9); **hepsi burada sayılmaz**.
4. **Kaydet** — §12.2 formu; **"YOK" da bir kayıttır** (negatif sonuç M3'ü
   "kapalı ama **cevaplı**" yapar — kapı kapanmaz, **bilgi** sabitlenir).
5. **Kapı M3** — VAR ise S4 kaynak; YOK ise "YOK + tarih" (dogrulama §8.3).

### 12.2 Boş önbellek-donanımı formu

| # | Alan | Değer | Kaynak (S4) | Tarih |
|---|---|---|---|---|
| 1 | Fiziksel önbellek donanımı | `MEASURE`/`YOK` | — | `YYYY-MM-DD` |
| 2 | CPU L2/L3 envanteri | **`cpu/` dalı** (bu dalda değil) | — | — |
| 3 | Depolama aygıtı iç önbelleği | **`ssd/` dalı** | — | — |
| 4 | Yazılım cache'i | **K000** (§1.2 — 6 sınıf) | — | — |

### 12.3 Kaçınma eki (§6 listesinin ikinci devamı)

| # | Kaçınma | Gerekçe |
|---|---|---|
| 12 | 857 `cache` sayısını §12.1 sorgusuna **girdi** yapmak | C-M04 — yazılım ölçümü, donanım sorgusu değil |
| 13 | "L2/L3 envanteri bu dalda derinleştirildi" | `cpu/` sınırı (kapsam §2.3/11) |
| 14 | "SSD önbelleği bellek dalına yazıldı" | `ssd/` sınırı (kapsam §2.2/9) |
| 15 | `YOK` sonucunu **sessizce** bırakmak | negatif de kaydedilir (M3 — §12.1/4) |

### 12.4 M3 sonucu → kapı/kayıt etkisi (özet)

| §12.1 sonucu | M3 kapısı | `kapsam` satırı | Not |
|---|---|---|---|
| `VAR` (S4 kaynaklı) | M3 **açılır** | §2.3/15 → §2.3'e yeni satır | donanım envanterine geçer |
| `YOK` + tarih | M3 **cevaplanır, kapalı kalır** | §2.3/15 "YOK" | negatif sonuç kaydedilir |
| `MEASURE` (boş) | M3 **kalır** (Q-M2 açık) | değişiklik yok | ölçüm beklenir |
| Sadece yazılım kanıtı | M3 **kapanmaz** | §2.3/12 (K000) | C-M04 mantığı |

**Kural**: bu tablo `memory-dogrulama.md` §8.3 ile **eşleşir**; iki tablo
çelişirse **dogrulama** (kanıt dosyası) kazanır — bu dosya **şablondur**.

---

## 13. İlgili dosyalar

| Dosya | İçerik |
|---|---|
| [`index.md`](index.md) | Dal ana sayfası |
| [`memory-kapsam.md`](memory-kapsam.md) | Kapsam İÇİN/DIŞINDA · sınır testi |
| [`memory-dogrulama.md`](memory-dogrulama.md) | S1–S5 · ölçüm (PHP cache ayrımı) · kapılar M1–M6 |
| `../network/network-ornekler.md` | Kardeş dal — örnek/şablon modeli (aynı dal dalgası) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
