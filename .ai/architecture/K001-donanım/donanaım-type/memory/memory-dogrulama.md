---
title: "memory — Doğrulama (Kanıt & Kapılar)"
type: branch-dogrulama
status: proposed
node_id: K001-donanım/donanaım-type/memory/memory-dogrulama
node_type: donanaım-type-dogrulama
parent_id: "K001-donanım/donanaım-type"
branch_authority: ADR-102
group: kanit
date: 2026-10-13
updated: 2026-10-13
---

> **Yazım notu**: `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak.
> **PROPOSED (ADR-102 taslak)** — anayasa/üst kapsam revize edilmedi.
>
> **Dal açma yetkisi**: `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` §4
> (memory = bellek donanımı; bellek yönetim algoritması + sanal bellek → K000-isletim-sistemi).
>
> **Bu dosyanın işi**: iddiaları **kanıt seviyesine** bağlamak, **repo ölçümünü**
> (tarih + yöntemle) kaydetmek — **PHP cache ayrımı dahil** — ve **kapıları**
> (M1–M6) tanımlamak. Kanıtsız iddia = **UNKNOWN**, uydurma = **yasak**.

---

## 1. Kanıt seviyesi şeması (S1–S5)

| Seviye | Ad | Ne sayılır | Bu dalda örneği |
|---|---|---|---|
| **S5** | Üst | Resmî standart/kurum sayfası, bu oturumda **erişilerek** doğrulandı | JEDEC (DDR/Flash/JC-45/JC-42) — §6.1 |
| **S4** | Güçlü | Depoda **bulunan gerçek dosya** (kod/konfigürasyon) | `shared/src/Cache/` 6 sınıf (§5.1) — **yazılım** kanıtı |
| **S3** | Orta | Ölçüm/çıktı (tarih + yöntem + sayı) | §4–5 repo ölçüm tabloları (2026-10-13) |
| **S2** | Zayıf | Mantıksal çıkarım / standart atıfı (erişilmemiş) | "`ddr` = 0 → DDR envanteri depoda yok" çıkarımı |
| **S1** | Kanıtsız | Tahmin / doğrulanmayan iddia | — (bu dosyada barınamaz) |

**Kural (QUANTIFIED)**: iddia **en az S2** olmadan `memory-kapsam.md` §2'ye
giremez; **S4/S5** olmadan **"kanıtlandı"** denemez. **S1** → **UNKNOWN**
(§2.4'e / Q-M1–Q-M3).

---

## 2. İddia → kanıt matrisi (memory)

| # | İddia (kapsam dosyası) | Seviye | Kanıt | Erişim |
|---|---|---|---|---|
| C-M01 | "DDR/EEPROM/flash endüstrisi JEDEC tarafından standartlaşır" | **S5** | `https://www.jedec.org/` — DDR SDRAM/HBM · LPDDR · Flash UFS/e.MMC/SSD/XFMD · JESD21-C · JC-45 · JC-42 | 2026-10-13 ✅ |
| C-M02 | "Depoda bellek donanımı (RAM/EEPROM/flash modeli) envanteri vardır" | **REDDEDİLDİ** | §4: `ddr` 0 · `dimm` 0 · `nvram` 0 · donanım `flash` 0 → **S4 yok** | 2026-10-13 |
| C-M03 | "`ram` ölçümü donanım envanterini gösteriyor" | **DÜZELTİLDİ** | §4: sınırlı `\bram\b` = **8/6 dosya** (agent/prompt metni); ham 2575 = gürültü → **donanım 0** | 2026-10-13 |
| C-M04 | "`cache` ölçümü önbellek **donanımını** gösteriyor" | **REDDEDİLDİ** | §5: 857/166 = **PHP 146/30 + non-PHP 711/136**; `shared/src/Cache/` **6 sınıf** → **yazılım** (K000) | 2026-10-13 |
| C-M05 | "`swap` ölçümü OS swap'ini gösteriyor" | **REDDEDİLDİ** | §5: 64/9 — **57'si `font-display: swap`** (3 CSS token dosyası) → OS swap ölçümü **0** | 2026-10-13 |
| C-M06 | "Bellek yönetim algoritması bu dalda" | **REDDEDİLDİ** | ADR-102 §4: algoritma + sanal bellek → K000-isletim-sistemi | yerel §4 |
| C-M07 | "ECC bu dalda" | **S2 (kapsam içi)** | Donanım **yetenek** (bit + devre) → bu dal; **politika/istatistik** → K000 (`kapsam` §3.2) | yerel §3.2 |
| C-M08 | "Depoda önbellek **donanımı** vardır (cache diski)" | **UNKNOWN (Q-M2)** | S4 kanıt yok (C-M04) → kapı **M3** kapalı | — |
| C-M09 | "Bellek gecikmesi ölçülmüştür" | **UNKNOWN (Q-M3)** | Ölçüm **yok** → kapı **M6** ⚠️ | — |
| C-M10 | "Güç/PCB kararları bu dalda" | **REDDEDİLDİ** | ADR-102 §4.2: güç/PCB → K002-electroncis çapraz referans | yerel §4.2 |
| C-M11 | "`bellek` terimi bu dala ait" | **DÜZELTİLDİ** | §5: 19/10 = **K000 bellek yönetimi + güvenlik** → sınır işareti | 2026-10-13 |

---

## 3. Ölçüm yöntemi (tekrarlanabilir)

| Öğe | Değer |
|---|---|
| **Tarih** | 2026-10-13 |
| **Yöntem** | `tools.filesystem.directory_tree` + `tools.filesystem.read_multiple_files` (shell izni yok) + JS tarafında regex sayımı (içerik bağlama alınmadı) |
| **Kapsam** | Repo kökü (`C:\www\coremusic.net`), `.ai/` hariç; `.git` `node_modules` `vendor` `dist` `build` `playwright-report` `test-results` hariç |
| **Dosya filtresi** | `md txt php js ts css scss json yml yaml xml sh ps1 env ini sql html htm mjs cjs` |
| **Ölçülen** | 1338 metin dosyası / 25.752.595 bayt |
| **Regex kuralı** | Kısa/belirsiz terimlerde **kelime sınırı** `\b` — §3.1 |

### 3.1 Neden kelime sınırı? (dürüstlük notu — network §3.1 ile aynı)

Ham alt-dize `ram` = **2575** (687 dosya) — `prog**ram**`, `di**ram**a`,
`p**ram**` gürültüsü. Kelime sınırı `\bram\b` = **8** (6 dosya). **İki sayı da
raporlandı**; **yorum** yalnız sınır sayısına göre yapıldı. `swap` için de aynı:
ham 64 → **57'si CSS `font-display: swap`** (dosya içeriği sınıflandırması §5).

---

## 4. Ölçüm tablosu — ana bellek terimleri (2026-10-13)

| Terim | Regex | Eşleşme | Dosya | Yorum (kapsam etkisi) |
|---|---|---|---|---|
| ram (sınırlı) | `\bram\b` | **8** | 6 | Agent/prompt metinleri (postgres-pro · embedded-systems · arşiv) → **donanım envanteri DEĞİL** |
| ram (ham) | `ram` | **2575** | 687 | **Gürültü** — yorumlanmadı (§3.1) |
| ddr | `\bddr\b` | **0** | 0 | **DDR modeli yok** → Q-M1 · kapı M2 |
| dimm | `\bdimm\b` | **0** | 0 | **Modül kaydı yok** → Q-M1 · kapı M2 |
| nvram | `\bnvram\b` | **0** | 0 | NVRAM yok → Q-M1 |
| dram | `\bdram\b` | 1 | 1 | Tek metin (doküman) → envanter **değil** |
| ecc | `\becc\b` | 3 | 3 | Doküman/agent metni → donanım **değil** |
| so-dimm | `so-?dimm` | 0 | 0 | Dizüstü modülü kaydı yok |
| ddr5/ddr4 | `ddr[45]` | 0 | 0 | Nesne/nesil envanteri yok |

### 4.1 Yorum notları (bu tablo nasıl okunur?)

| # | Not | Açıklama |
|---|---|---|
| 1 | **8 ≠ envanter** | `\bram\b` 8 eşleşmenin **tamamı** agent/prompt **metnidir** (postgres-pro · embedded-systems · arşiv) — donanım **değil** |
| 2 | **2575 asla envanter sayılmaz** | Ham sayı **yalnız gürültü kanıtıdır**; yorum **hep** `\b` sınırıyla (§3.1) |
| 3 | **0 = kapı gerekçesi** | `ddr`/`dimm`/`nvram` 0 → **eksiklik değil, kanıt yokluğu** → Q-M1 + M2 ❌ |
| 4 | **ddr[45] ayrıca sayıldı** | `\bddr\b` (kelime) ile `ddr4`/`ddr5` (önek) **farklı regex'tir** — birleştirilmedi, ikisi de 0 |
| 5 | **`eeprom` 1 tekil** | Arşiv soru dosyası → **envanter değil**; tek dosya olduğu için sınıflandırma **dosya içeriğiyle** yapıldı |

**Yeniden-produksiyon**: §3 yöntemi + bu regex'ler aynen uygulanırsa sayılar
**birebir** tekrarlanır (dosya kümesi aynı kaldığı sürece).

---

## 5. Ölçüm tablosu — kalıcı/önbellek terimleri (2026-10-13)

### 5.1 Kalıcı bellek donanımı

| Terim | Regex | Eşleşme | Dosya | Yorum |
|---|---|---|---|---|
| eeprom | `eeprom` | **1** | 1 | `.claude/skills/prompt-maker/.archive/questions-block-h.md:57` — **soru arşivi**, envanter **değil** |
| flash (ham) | `flash` | 13 | 8 | **CSS FOUT flash · flash loan (fintech) · embedded linker script · hardware questionnaire** → **donanım flash envanteri 0** |
| spi flash | `spi.?flash` | 0 | 0 | Gömülü flash çipi kaydı yok |
| nor/nand | `\bnor\b|\bnand\b` | 0 | 0 | Çip türü envanteri yok |

### 5.2 Önbellek — **kritik PHP ayrımı**

| Terim | Regex | Eşleşme | Dosya | Sınıflandırma |
|---|---|---|---|---|
| cache (sınırlı) | `\bcache\b` | **857** | **166** | **YAZILIM** (aşağıda kırılım) |
| ↳ PHP dosyaları | (`.php`) | **146** | **30** | `CoreMusic\Cache\` **kütüphanesi** |
| ↳ non-PHP | — | **711** | **136** | HTTP dokümanı · CSS · skill/prompt |
| bellek | `bellek` | **19** | **10** | **K000 bellek yönetimi** + güvenlik → DIŞINDA |
| swap (ham) | `swap` | **64** | **9** | **57 eşleşme / 3 dosya = `font-display: swap`** (CSS token) → OS swap **0** |

**`shared/src/Cache/` kanıt zinciri (S4)** — depoda **gerçek** 6 dosya/sınıf:

| # | Sınıf | Ne yapar | Kapsam |
|---|---|---|---|
| 1 | `CacheInterface` | Cache sözleşmesi | ❌ Yazılım → K000 |
| 2 | `CacheManager` | Kayıt/yaşam döngüsü | ❌ → K000 |
| 3 | `ApcuAdapter` | PHP APCu (RAM cache) | ❌ → K000 |
| 4 | `MemoryAdapter` | Bellek içi cache (yazılım) | ❌ → K000 |
| 5 | `PageCacheAdapter` | Sayfa cache'i (HTTP/dosya) | ❌ → K000 |
| 6 | `PageCacheInterface` | Sayfa cache sözleşmesi | ❌ → K000 |

> **Sonuç (C-M04)**: 857 `cache` = **%100 yazılım** → **"donanım önbellek
> envanteri = 0"** · Q-M2 · kapı **M3 kapalı**.

### 5.3 `swap` ve `flash` dosya-içeriği kırılımı (sınıflandırma kanıtı)

| Terim | Eşleşme/Dosya | Kırılım | Sınıf |
|---|---|---|---|
| `swap` | 64 / 9 | **57 / 3 dosya** = `font-display: swap` (CSS token dosyaları) | **UI/CSS** |
| ↳ kalan | 7 / ~6 | OS/mem (metin) · doküman | **K000 / doküman** |
| `flash` | 13 / 8 | CSS FOUT flash · flash loan (fintech doküman) · embedded linker script · hardware questionnaire | **UI + doküman + şablon** |
| ↳ donanım flash | **0** | Çip modeli/kaydı **hiçbiri** | **ENVANTER YOK → Q-M1** |

**Kural**: `swap`/`flash` için **ham sayı sınıflandırılmadan** kullanılamaz
(her ikisi de **kendi adı donanım/OS çağrıştırır** ama depoda **değillerdir**) —
sınıflandırma **dosya-içeriği** ile yapılır, **terim anlamıyla** değil.

---

## 6. URL doğrulama defteri (erişim tarihi: 2026-10-13)

### 6.1 Erişilen / doğrulanan (S5)

| # | URL | Sonuç | Kanıt |
|---|---|---|---|
| W-M1 | `https://www.jedec.org/` | ✅ erişildi | **JEDEC** — DDR SDRAM/HBM · LPDDR · Flash UFS/e.MMC/SSD/XFMD · JESD21-C · **JC-45** DRAM Modülleri · **JC-42** |

### 6.2 Elenen / erişilemeyen (kullanılmadı — dürüstlük kaydı)

| # | URL | Sonuç | Karar |
|---|---|---|---|
| W-X2 | `https://www.jedec.org/technology-focus-areas/main-memory-ddr-sdram-hbm` | ❌ **HTTP 404** | **ELİNDİ** — JEDEC ana sayfası kullanıldı |
| W-X1 | `https://standards.ieee.org/ieee/802.11/7033/` | ⚠️ yanlış sayfa (IEEE 382-2019) | **ELİNDİ** (network dalı ortak kayıt) |

**Kural**: 6.2'deki URL'ler **hiçbir dosyada atıf olarak kullanılamaz**;
kullanılırsa **kapı M5 (bütünlük)** kapalı sayılır.

### 6.3 S5 kaynağının kullanım sınırı (JEDEC neyi ispatlar, neyi ispatlamaz)

| # | İspatlar (S5 kullanılabilir) | İspatlamaz (UNKNOWN kalır) |
|---|---|---|
| 1 | Bellek/flash endüstrisinin **standart kuruluşu** JEDEC'dir | Projenin **hangi** DDR neslini kullandığı (→ Q-M1) |
| 2 | DDR SDRAM/HBM · LPDDR · UFS/e.MMC/SSD/XFMD **JEDEC kapsamındadır** | Spesifik **part numarası / hız / zamanlama** (JC-40/42/45 dokümanları **bu oturumda çekilmedi** → UNKNOWN) |
| 3 | **JC-45** DRAM modülleri, **JC-42** (sıcaklık/ajanda) komiteleri **vardır** | Projenin **modül formu** (DIMM/SO-DIMM) — envanter M2'ye bağlı |
| 4 | JESD21-C = katalog anahtarı (standart listesi) | Tekil bir standardın **metni/içeriği** (erişilmedi) |

**Kural**: ana sayfa erişimi **kurum + kapsam** ispatlar; **parça/türetilmiş
iddialar** S5'e **bağlanamaz** — onlar M2 envanteri (S4) veya doküman
(erişilirse S5) ile gelir. **"JEDEC dedi → proje DDR5"** gibi zincir
**yasaktır** (0 atıf uydurmaya eşdeğerdir).

### 6.4 Yeniden ölçüm (HEAD — 2026-10-13) — JEDEC 403 çelişki kaydı

> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

| # | URL | İlk kayıt (§6.1) | Yeniden HEAD (2026-10-13) | Son karar |
|---|---|---|---|---|
| 1 | `https://www.jedec.org/` | ✅ erişildi (eski oturum) | **HTTP 403** | **KRİTİK** → aşağıda §6.4.1 |
| 2 | `https://docs.kernel.org/` | — (bu dosyada yok) | **HTTP 200** | **S5 eklendi** (W-M2) |
| 3 | `https://kernel.org/doc/html/latest/` | — | **HTTP 200** | **S5 eklendi** (W-M3) |
| 4 | `https://uefi.org/` | — | **HTTP 403** | **KULLANILMADI** (W-X3) |

#### 6.4.1 JEDEC çelişkisinin çözümü (satır silinmeden — tamamlanarak)

| # | Kayıt | Durum |
|---|---|---|
| 1 | §6.1'deki W-M1 satırı | **korunur** (tarihsel ilk oturum kaydı — silinmez) |
| 2 | Bu oturumun HEAD sonucu | **403** → erişim **tartışmalı** |
| 3 | Sonraki kullanımda JEDEC | yalnızca "endüstri standart kuruluşu" düzeyi — **S5 sınırlı** |
| 4 | JEDEC sayısal iddiası (pin/gerilim/hız/JESD numarası) | **⚠️ VERIFICATION REQUIRED** (kaynak erişilemedi) |
| 5 | Kapı M5 (web bütünlüğü) | **yeniden değerlendirilir** — §7'deki "1 erişildi" satırı bu § ile **tamamlanır**: 2 yeni 200 eklendi, 1×403 elendi (W-X3) |
| 6 | "jedec.org ✅ erişildi" cümleleri (kapsam §5/§8.1) | **tarihsel** — bu § okunmadan **yönlendirici sayılmaz** |

**Kural (append-only)**: 403/404/timeout **hiçbir yeni satırda kaynak
olamaz**; çelişki **silinmez**, **§6.4.1 ile işaretlenir**. Değişiklik
olduğunda §6 tablolarına **yeni satır** eklenir, eski satır **üzerine yazılmaz**.

#### 6.4.2 W-X3 (yeni elenen)

| # | URL | Sonuç | Karar |
|---|---|---|---|
| W-X3 | `https://uefi.org/` | ❌ **HTTP 403** | **ELİNDİ** — bu dalda atıf yapılmaz |

**Kural**: §6.2 ve §6.4.2'deki elenen URL'ler **hiçbir dosyada atıf olarak
kullanılamaz**; kullanılırsa **kapı M5 kapalı** sayılır (mevcut kural genişletildi).

---

## 7. Kapılar (M1–M6) — geç/et

| Kapı | Soru | Kriter (ölçülebilir) | Durum (2026-10-13) |
|---|---|---|---|
| **M1 — Kapsam bütünlüğü** | Kapsam §2/§3 ADR-102 §4 ile uyumlu mu? | Algoritma/sanal bellek → K000 · güç/PCB → K002 · `ssd/`·`cpu/` sınırı işaretli | ✅ **GEÇ** |
| **M2 — Envanter kapısı** | Bellek donanımı envanteri var mı? | RAM/EEPROM/flash modeli kaydı (S4) | ❌ **KAPALI** (Q-M1: `ddr`/`dimm` = 0) |
| **M3 — Önbellek donanımı** | Önbellek **donanımı** kanıtı var mı? | Donanım cache kaydı (disk/L2-L3 envanteri) | ❌ **KAPALI** (857 = yazılım; Q-M2) |
| **M4 — Ölçüm bütünlüğü** | Ölçüm §3 yöntemine uygun mu? | Tarih + yöntem + dosya sayısı + `\b` kuralı + gürültü notu | ✅ **GEÇ** |
| **M5 — Web bütünlüğü** | Kullanılan URL erişildi mi? | §6.1 ✓ / §6.2 ✗ | ✅ **GEÇ** (1 erişildi · 2 elendi) |
| **M6 — Ölçülebilirlik** | Bellek metrikleri ölçüldü mü? | Gecikme/bant genişliği ölçümü (§8.2 formu) | ⚠️ **KISMİ** — ölçüm **yok** (Q-M3) |

**Kapı özeti**: **M1 ✅ · M2 ❌ · M3 ❌ · M4 ✅ · M5 ✅ · M6 ⚠️**
→ Dal **proposed** kalır; **M2/M3** envanterle, **M6** ölçümle açılır.

---

## 8. Kayıt formları (boş — envanter/ölçüm gelince doldurulur)

### 8.1 Bellek donanımı envanter formu (kapı M2)

| # | Alan | Değer | Kanıt (S4) | Tarih |
|---|---|---|---|---|
| 1 | RAM tipi (DDR4/DDR5) | `UNKNOWN` | — | — |
| 2 | Kapasite / modül formu | `UNKNOWN` | — | — |
| 3 | EEPROM modeli | `UNKNOWN` | — | — |
| 4 | Gömülü flash çipi | `UNKNOWN` | — | — |
| 5 | ECC varlığı | `UNKNOWN` | — | — |
| 6 | Önbellek donanımı (varsa) | `UNKNOWN` | — | — |

### 8.2 Bellek ölçüm formu (kapı M6) — şablon

| # | Metrik | Yöntem | Değer | Tarih |
|---|---|---|---|---|
| M6.1 | Bellek okuma gecikmesi (ns) | ölçüm aracı | `MEASURE` | `YYYY-MM-DD` |
| M6.2 | Bellek bant genişliği (GB/s) | ölçüm aracı | `MEASURE` | `YYYY-MM-DD` |
| M6.3 | Kanal/dual-channel durumu | NIC benzeri donanım okuma | `MEASURE` | `YYYY-MM-DD` |

> **Dürüstlük**: 8.1/8.2 **boş bırakıldı**; **uydurma değer yazılmadı**.
> Değer gelince **kaynak (S4/S3) + tarih** zorunlu.

### 8.3 Kapı yeniden değerlendirme prosedürü (M2/M3/M6 nasıl açılır?)

| Kapı | Aşama 1 | Aşama 2 | Aşama 3 | Açılış kanıtı |
|---|---|---|---|---|
| **M2** | `ornekler` §2.1 envanter şablonu doldurulur (`dmidecode`/`Get-CimInstance`/gözlem) | Her satıra **S4** kaynak (komut çıktısı) | `memory-kapsam.md` §2.4 → §2.1/2.2'ye **taşınır** | Envanter + tarih |
| **M3** | Önbellek **donanımı** aranır (cache diski/HBM — donanım sorgusu) | Donanım varsa S4; yoksa **"YOK"** kaydı (negatif de sonuçtur) | §2.3/15 satırı güncellenir | Donanım kaydı **veya** "YOK" + tarih |
| **M6** | §8.2 formu doldurulur (3× tekrar, medyan) | Araç + ortam + tarih | `ornekler` §4 hesabı ölçümle eşleştirilir | Ölçüm çıktısı + tarih |

**Kural (append-only)**: kapı **kapalıyken** dal "proposed" kalır; kapı
açıldığında §7 tablosundaki **durum hücresi** güncellenir, **satır silinmez**.
**M4 (ölçüm bütünlüğü)** her yeni ölçümde **tekrar** denetlenir (yöntem §3
bozulmuşsa ölçüm geçersiz sayılır).

**Durdurma kuralı**: 3 başarısız değerlendirmeden sonra **dur**; şüpheli
varsayımı açıkça yaz ve üst karara (orchestrator) sor (küresel §2.5).

---

## 9. Komşu kapılar (çapraz referans)

| Kapı | Komşu dal | Ne devredildi | Kayıt |
|---|---|---|---|
| **K000** | isletim-sistemi | Bellek yönetimi · sanal bellek/swap · PageCache/Apcu · malloc | `kapsam` §3 |
| **K002** | electroncis | Bellek güç/PCB · sinyal bütünlüğü | ADR-102 §4.2 |
| **ssd/** | kardeş dal | Depolama aygıtı flash'ı (SSD/e.MMC iç yapısı) | `kapsam` §2.2/9 |
| **cpu/** | kardeş dal | L2/L3 önbelleği (CPU donanımı) | `kapsam` §2.3 |

---

## 10. Dürüstlük bloğu (QUANTIFIED)

- **Ölçüm**: 2026-10-13 · 1338 dosya / 25.752.595 bayt · yöntem §3 ·
  `ddr` `dimm` `nvram` `spi flash` `nor/nand` = **0** → **envanter kapısı M2 kapalı** (Q-M1).
- **`ram` = 8/6 (sınırlı)**: agent/prompt metni; **ham 2575 = gürültü** (§3.1) —
  **iki sayı raporlandı**, yorum sınır sayısına.
- **`cache` = 857/166**: **PHP 146/30 + non-PHP 711/136**; `shared/src/Cache/`
  **6 sınıf** (§5.2) → **%100 yazılım**; **donanım önbellek = 0 kanıt** (Q-M2 · M3).
- **`swap` = 64/9**: **57'si `font-display: swap`** → OS swap ölçümü **0**.
- **`eeprom` = 1 · `flash` = 13**: arşiv/CSS/flash-loan/linker → **donanım 0**.
- **Web**: **1 URL erişildi** (JEDEC, S5); **2 URL elendi** (§6.2) — elenenler
  **hiçbir dosyada kullanılmadı**.
- **Web (yeniden ölçüm 2026-10-13 — §6.4)**: JEDEC HEAD **403** → sayısal
  JEDEC iddiaları **⚠️ VERIFICATION REQUIRED**; **2 yeni S5** eklendi
  (docs.kernel.org 200 · kernel.org/doc/html/latest 200); **1 yeni elendi**
  (uefi.org 403) → M5 **yeniden değerlendirildi** (§12.2).
- **Kısıt**: ADR-038 · PCM5122 · ADR-089 **dokunulmaz** (ADR-102 §4.2);
  güç/PCB → K002 (C-M10).
- **Belirsizlik**: **Q-M1–Q-M3 açık** · kapılar **M2 ❌ · M3 ❌ · M6 ⚠️**
  → dal **proposed**; envanter + ölçüm gelmeden **"kanıtlandı" denemez**.

---

## 11. Yeniden Ölçüm Protokolleri (tekrarlanabilirlik — bu dosyanın kalıcılık kuralı)

### 11.1 HEAD URL protokolü (10 adım — her dal revizyonunda)

```text
[1] Hedef URL listesini yaz (§6.1 + adaylar)
  → [2] Yöntem: Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20
  → [3] Sonucu kaydet: HTTP kodu + tarih (2026-10-13 standardı)
  → [4] 200/202? → KULLAN · §6.1'e/§6.4'e YENİ satır ekle
  → [5] 403/406? → KULLANMA · §6.4.2'ye yeni satır · ⚠️ VERIFICATION REQUIRED
  → [6] 404?     → KULLANMA · §6.2'ye satır
  → [7] timeout/DNS → KULLANMA · §6.2'ye satır
  → [8] Eski satırın üstüne YAZMA (append-only — §6.4.1 kuralı)
  → [9] Kapı M5'i yeniden değerlendir (§12.2)
  → [10] Erişim tarihini satırda güncelle; kalıcılık garantisi YOK (§3.5 mantığı)
```

### 11.2 Bu dalın URL tekrar beklenti tablosu (2026-10-13 tabanı)

| # | URL | Beklenen (bu oturum) | Kullanım alanı | Kırılırsa |
|---|---|---|---|---|
| 1 | `https://www.jedec.org/` | **403** | yalnız "kuruluş" düzeyi | §6.4.1 kuralı |
| 2 | `https://docs.kernel.org/` | 200 | K000 sınırı / sürücü dokümanı | yeni satır |
| 3 | `https://kernel.org/doc/html/latest/` | 200 | çekirdek doküman kökü | yeni satır |
| 4 | `https://uefi.org/` | **403** | **kullanılmaz** | — |
| 5 | `jedec.org/.../main-memory-ddr-sdram-hbm` | 404 | **kullanılmaz** (W-X2) | — |

### 11.3 Repo ölçüm tekrar protokolü (R1–R10)

| Adım | İşlem | Beklenen (2026-10-13 seti) |
|---|---|---|
| R1 | `\bram\b` → tüm metin dosyası | 8 / 6 |
| R2 | `ram` (ham) → aynı set | 2575 / 687 (gürültü) |
| R3 | `\bddr\b` · `\bdimm\b` · `so-?dimm` | 0 · 0 · 0 |
| R4 | `\bnvram\b` · `eeprom` | 0 · 1 |
| R5 | `flash` (ham) · `spi.?flash` · `\bnor\b/\bnand\b` | 13 · 0 · 0 |
| R6 | `\bcache\b` → kırılım PHP/non-PHP | 857/166 → 146/30 + 711/136 |
| R7 | `bellek` | 19 / 10 |
| R8 | `swap` (ham) + `font-display: swap` | 64/9 → 57/3 CSS |
| R9 | Dosya/bayt seti | 1338 / 25.752.595 |
| R10 | Fark varsa | **yeni satır ekle** (eskiyi silme) + §14.1'e fark kaydı |

### 11.4 Fark kaydı şablonu (yeniden ölçümde çelişki çıktığında)

| Alan | İçerik |
|---|---|
| `metric` | ölçüm adı (ör. `jedec.org HEAD`) |
| `old` | eski kayıt (satır + tarih) |
| `new` | yeni sonuç (HTTP/sayı + tarih) |
| `decision` | hangisi geçerli — **gerekçeyle** |
| `gate_effect` | etkilenen kapı (M4/M5/M6) |
| `date` | `YYYY-MM-DD` |

---

## 12. Kapı Derinleştirme (M1–M6 — tam kayıt)

### 12.1 Kapı × soru × form × soru-no × sahip çizelgesi

| Kapı | Soru | Form | İlgili açık soru | Sahip | Durum |
|---|---|---|---|---|---|
| M1 | Kapsam ADR-102 §4 ile uyumlu mu? | — (metin denetimi) | Q-M0 (süreç) | Vault Steward | ✅ GEÇ |
| M2 | Bellek donanımı envanteri var mı? | `memory-ornekler.md` §2.2 · `kapsam` §10.4 | **Q-M1** | Vault Steward | ❌ KAPALI |
| M3 | Önbellek **donanımı** kanıtı var mı? | `memory-ornekler.md` §2.2/6 | **Q-M2** | Vault Steward | ❌ KAPALI |
| M4 | Ölçüm yöntemi (§3) bozulmadı mı? | §3 yöntem + §11.3 tekrar | — | dosya sahibi | ✅ GEÇ |
| M5 | Kullanılan URL erişildi mi? | §6.1 ✓ / §6.2 ✗ / §6.4 ✗ | — | dosya sahibi | ⚠️ **YENİDEN** (JEDEC 403 — §12.2) |
| M6 | Bellek metrikleri ölçüldü mü? | `memory-ornekler.md` §3.1 | **Q-M3** | Vault Steward | ⚠️ KISMİ (ölçüm 0) |

### 12.2 M5 yeniden değerlendirme (JEDEC 403 sonrası — adım adım)

```text
[1] §6.4 yeniden ölçüm kaydını oku (HEAD 2026-10-13: jedec 403 · kernel 200 ×2 · uefi 403)
  → [2] Kullanılan kaynakları say:
      = 2 × S5 sağlam (docs.kernel.org · kernel.org/doc/html/latest)
      + 1 × tartışmalı (jedec.org — §6.4.1)
  → [3] Elenenleri say: 404 (W-X2) · yanlış sayfa (W-X1) · 403 (W-X3)
  → [4] JEDEC sayısal iddialarını ⚠️ VERIFICATION REQUIRED'a çevir
  → [5] M5 hücreini GÜNCELLE (satır silme): "GEÇ (2 sağlam + 1 tartışmalı · 3 elendi)"
  → [6] `memory-kapsam.md` §10.3/§5 atıf satırlarını §6.4.1'e göre işaretle
  → [7] 3 turda çözülmezse DUR + üst karar (küresel kural)
```

### 12.3 Kapı geçiş kanıtı formatı (her kapı için zorunlu alan)

| Alan | Zorunlu içerik |
|---|---|
| `gate` | M1…M6 |
| `claim` | kapanan iddia (C-Mxx) |
| `evidence_level` | S2/S3/S4/S5 |
| `evidence_ref` | dosya + § + satır/komut çıktısı |
| `date` | `YYYY-MM-DD` |
| `result` | GEÇ / KAPALI / KISMİ |
| `next_condition` | kapalıysa açılış koşulu |

**Kural**: `result: GEÇ` **yalnız** `evidence_ref` + `date` doluysa yazılır;
**kanıtsız GEÇ = kapı kapalıdır** (S1 yasağı §1 ile aynı).

---

## 13. Sınıflandırma ve Hata Akışları (Red Team — bu dosyanın imtihanı)

### 13.1 Ham sayı → sınıf karar ağacı

```text
[1] Ölçüm/arama sonucu geldi (ham sayı + dosya sayısı)
  → [2] Kelime sınırı uygula (\b) — §3.1
      → sınırlı sayı CİDDİYE ALINIR, ham sayı RAPORLANIR
  → [3] Dosya içeriği sınıflandırması (her eşleşme için):
      = donanım (çip/modül/pin) → bu dal · §2.1/2.2
      = yazılım (PHP/JS/OS)     → K000 · §3
      = UI/CSS                  → yok say · §6.1
      = doküman/metin           → atıf · envanter DEĞİL
  → [4] Sınıflandırma YOKSA → sayı yorumlanmaz (UNKNOWN)
  → [5] Sınıflandırma VARSA → kapıya bağla (M2/M3/M6)
```

### 13.2 Sınıflandırma örnek tablosu (2026-10-13 seti — 12 satır)

| # | Ham bulgu | Sınıf | Kapı | Gerekçe |
|---|---|---|---|---|
| 1 | `ram` 2575 | gürültü | — | alt-dize (program/drama) |
| 2 | `\bram\b` 8/6 | doküman | — | agent/prompt metni |
| 3 | `ddr` 0 | yok | M2 | envanter kanıtı yok |
| 4 | `dimm` 0 | yok | M2 | modül kaydı yok |
| 5 | `cache` 857/166 | **yazılım** | M3 | PHP 146/30 + 711/136 |
| 6 | `shared/src/Cache/` 6 sınıf | **yazılım** | M3 | gerçek dosya kanıtı (S4) |
| 7 | `swap` 64 → 57 CSS | UI/CSS | — | `font-display: swap` |
| 8 | `flash` 13 | UI+doküman | M2 | donanım 0 |
| 9 | `eeprom` 1 | doküman | M2 | soru arşivi |
| 10 | `bellek` 19 | K000 | — | yönetim dokümanı |
| 11 | `ecc` 3 | doküman | M1 | donanım kanıtı 0 |
| 12 | `m2`/`nvme`/`hbm` | **aranmadı** | — | sayı uydurulmaz (kapsam §12.3) |

### 13.3 Kanıt uyuşmazlığı akışı (vault vs ölçüm)

```text
[1] Vault metni bir iddia taşıyor (ör. "jedec.org ✅")
  → [2] Yeni ölçüm onunla çelişiyor (HEAD 403)
      → [3] Eski satır silinmez (append-only)
      → [4] Çelişki §6.4.1 ile işaretlenir · iddia ⚠️ VERIFICATION REQUIRED
      → [5] Kapı M5 yeniden değerlendirilir (§12.2)
  → [6] 3 turda çözülmezse: DUR + üst karar (Vault Steward)
```

### 13.4 Ölçüm çelişkisi akışı (aynı metrik, iki sayı)

```text
[1] İki farklı sayı çıktı (ör. ram 2575 vs 8)
  → [2] Farkın kaynağı: regex sınırı mı, dosya seti mi? (§3.1 · §11.3)
      → KAYNAK regex  → iki sayı da raporlanır · yorum sınırlı sayıya
      → KAYNAK set    → §11.4 fark kaydı · eski satır korunur
  → [3] Yorumu değiştirme yetkisi: dosya sahibi + §11.4 kaydı
```

---

## 14. UNKNOWN / VERIFICATION REQUIRED İzleme Kaydı

### 14.1 U-M kayıtları (kapanış koşulu ile)

| # | Belirsizlik | İşaret | Kapanış koşulu | Kapı |
|---|---|---|---|---|
| U-M1 | RAM/EEPROM/flash envanteri | UNKNOWN | §8.1 formu + S4 kaynak | M2 |
| U-M2 | Önbellek donanımı (cache diski/L2-L3) | UNKNOWN | donanım sorgusu + kayıt | M3 |
| U-M3 | Bellek gecikme/bant ölçümü | UNKNOWN | §8.2 formu (3× medyan) | M6 |
| U-M4 | JEDEC sayısal spesifikasyonları | ⚠️ VERIFICATION REQUIRED | erişilebilir kaynak (HEAD 200) | M5 |
| U-M5 | ECC varlığı (projede) | UNKNOWN | envanter satırı | M2 |
| U-M6 | Modül formu (DIMM/SO-DIMM) | UNKNOWN | gözlem/okuma | M2 |
| U-M7 | `m2`/`nvme`/`hbm` ölçümü | UNKNOWN (aranmadı) | §11.3 R-adımı eklenerek ölç | M4 |
| U-M8 | ADR-102 onayı | APPROVAL REQUIRED | Vault Steward kararı | M1 |

### 14.2 Revizyon tetikleyicileri

| Tetikleyici | Etkilenen bölüm |
|---|---|
| §8.1 envanter doldu | `kapsam` §2.4 → §2.1/2.2 · §7 (M2) · U-M1 |
| §8.2 ölçüm girildi | §7 (M6) · U-M3 · `ornekler` §4 eşleşmesi |
| URL HEAD değişimi | §6 (yeni satır) · §6.4 · §7 (M5) · U-M4 |
| Yeni repo ölçümü | §4/§5 (yeni satır) · §11.3 · U-M7 |
| ADR-102 onayı | U-M8 · tüm dal `status` alanları |

---

## 15. İlgili dosyalar

| Dosya | İçerik |
|---|---|
| [`index.md`](index.md) | Dal ana sayfası · açık sorular |
| [`memory-kapsam.md`](memory-kapsam.md) | Kapsam İÇİN/DIŞINDA · sınırlar (ECC/3 test) · ölçüme dayalı §6 |
| [`memory-ornekler.md`](memory-ornekler.md) | `shared/src/Cache/` gerçek kodu + envanter/ölçüm şablonları |
| `../network/network-dogrulama.md` §5 | **Ortak ölçüm defteri** (aynı oturum ölçümünün tamamı) |
| `../hdd/hdd-dogrulama.md` | Kardeş dal — S1–S5 şablon modeli |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
