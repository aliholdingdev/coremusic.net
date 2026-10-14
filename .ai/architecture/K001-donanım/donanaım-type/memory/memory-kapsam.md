---
title: "memory — Kapsam (Bellek Donanımı Tipi)"
type: branch-kapsam
status: proposed
node_id: K001-donanım/donanaım-type/memory/memory-kapsam
node_type: donanaım-type-kapsam
parent_id: "K001-donanım/donanaım-type"
branch_authority: ADR-102
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

> **Yazım notu**: `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak.
> **PROPOSED (ADR-102 taslak)** — anayasa/üst kapsam revize edilmedi; bu dosya
> bir **taslak**tır ve yalnızca ADR-102'nin §4 kapsamıyla sınırlıdır.
>
> **Dal açma yetkisi**: `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` §4
> — "memory = bellek donanımı (RAM DDR/SO-DIMM, EEPROM/flash, önbellek depolama
> donanımı); **bellek yönetim algoritması + sanal bellek → K000-isletim-sistemi**".

---

## 1. Eli15 — Bu dal ne işe yarar?

Bilgisayar "düşünür" ama düşündüğü yeri bir yerde tutmak zorundadır: elektrik
kaybolunca **silinen** yer (RAM), **kalıcı** kalan yer (flash/EEPROM) ve
**hızlı** tutulan yer (önbellek). Bu dal **belleğin fiziksel gövdesini** tutar:
RAM modülü (DDR/DIMM/SO-DIMM), kalıcı depolama çipleri (EEPROM, gömülü flash) ve
depolama tabanlı önbellek donanımı. Bu dalda **yazılım** (bellek yönetimi,
sanal bellek/swap, sayfa tabloları, `malloc`) **yoktur** — onlar ayrı bir dalın
(K000-isletim-sistemi) işidir. Kısaca: **burada "çip ve modül" oturur, "algoritma
ve yönetim" değil.**

---

## 2. Kapsam İÇİNDE (memory)

### 2.1 Ana bellek donanımı

| # | Öğe | Kapsam | Kanıt | Not |
|---|---|---|---|---|
| 1 | **DRAM modülü** — DDR4/DDR5 DIMM, SO-DIMM (dizüstü), RDIMM | ✅ | — | Fiziksel modül + çip dizilimi |
| 2 | **Yongada bellek (SoC/µC SRAM/ROM)** | ✅ | — | Donanımın **kendi** gömülü belleği; **yönetimi** değil |
| 3 | **ECC / düzeltme donanımı** | ✅ | — | ECC bit üretimi/denetimi **donanım**; algoritma ayrı |
| 4 | **Bellek denetleyicisi (memory controller) donanım bloğu** | ✅ (temas) | — | **Donanım bloğu** olarak; **denetleyici algoritması** → K000 |
| 5 | **NUMA / bellek yolu donanımı** | ✅ (temas) | — | Fiziksel topoloji; **dağıtım politikası** → K000 |

### 2.2 Kalıcı / yarı-kalıcı bellek donanımı

| # | Öğe | Kapsam | Kanıt | Not |
|---|---|---|---|---|
| 6 | **EEPROM** — konfigürasyon/ajanda saklama çipi | ✅ | ölçüm: 1/1 (arşiv) | Donanım çipi; **erişim kodu** → K000 |
| 7 | **NVRAM / battery-backed SRAM** | ✅ | ölçüm: 0 | Envanter **UNKNOWN (Q-M1)** |
| 8 | **Gömülü flash** — SPI NOR/NAND, kod flash'ı | ✅ | ölçüm: donanım 0 | **Firmware saklama donanımı**; firmware **sürümü** değil |
| 9 | **Depolama aygıtı flash'ı** (SSD/e.MMC/UFS) | ⚠️ sınır | — | **Donanım olarak** `ssd/` dalı sınırı (§4.3) |

### 2.3 Önbellek depolama donanımı

| # | Öğe | Kapsam | Kanıt | Not |
|---|---|---|---|---|
| 10 | **Depolama tabanlı önbellek donanımı** (önbellek diskini kuran donanım) | ✅ | — | **Donanım varlığı**; **önbellek algoritması/JS-PHP** → K000 |
| 11 | **CPU L2/L3 önbelleği** | ⚠️ sınır | — | **CPU'nun kendi** donanımı → büyük ölçüde `cpu/` dalı (§4.3) |
| 12 | **`CoreMusic\Cache\` (PHP Apcu/PageCache)** | ❌ **DIŞINDA** | ölçüm: 857 `cache` | **Yazılım** → K000 (`shared/src/Cache/` 6 sınıf) |

### 2.4 Bahsedilen ama envanteri doğrulanmayan

| # | Öğe | Durum | Kanıt | Gerekçe |
|---|---|---|---|---|
| 13 | Proje envanterindeki RAM (kapasite/DDR tipi) | **UNKNOWN (Q-M1)** | — | `ddr` 0 · `dimm` 0 (ölçüm) |
| 14 | Proje envanterindeki EEPROM/flash çip modeli | **UNKNOWN (Q-M1)** | — | Donanım `flash` 0 · `eeprom` 1 (arşiv) |
| 15 | Önbellek **donanımı** (cache diski) | **UNKNOWN (Q-M2)** | — | 857 `cache` = **yazılım** (§6) |
| 16 | Ölçülmüş bellek gecikmesi/bant | **UNKNOWN (Q-M3)** | — | Ölçüm **yok** → kapı M6 |

**Kural**: 13–16 satırları envanter/ölçüm gelene kadar **UNKNOWN** kalır;
envanter çıkarıldığında §2.1/2.2'ye taşınır ve **M2/M3** kapıları tekrar çalışır.

---

## 3. Kapsam DIŞINDA (memory) — komşu sınırlar

| # | Dışarıda olan | Nereye gider | Sınır gerekçesi |
|---|---|---|---|
| 1 | **Bellek yönetim algoritması** (tahsis, sayfa atama) | K000-isletim-sistemi | ADR-102 §4: algoritma ≠ donanım |
| 2 | **Sanal bellek / swap** | K000-isletim-sistemi | OS çekirdeği |
| 3 | **Sayfa tablosu / TLB / MMU yönetimi** | K000-isletim-sistemi | OS + donanım arayüzü — **yönetim** OS |
| 4 | **OS önbellek yazılımı** (PageCache, buffer cache) | K000-isletim-sistemi | Yazılım katmanı |
| 5 | **Uygulama cache'i** (`CoreMusic\Cache\`, Apcu, HTTP) | K000-isletim-sistemi / uygulama | Ölçüm: 857 = yazılım (§6) |
| 6 | **`malloc`/garbage collector/heap** | K000-isletim-sistemi | Çalışma zamanı belleği |
| 7 | **Depolama aygıtı iç yapısı** (SSD firmware, FTL) | `ssd/` + K000 | Depolama dalı sınırı (§4.3) |
| 8 | **`font-display: swap` / CSS** | UI (frontend) | Ölçüm gürültüsü (§6.1) |
| 9 | **Bellek güç/PCB/sinyal tasarımı** | K002-electroncis | ADR-102 §4.2 (§5) |

### 3.1 Sınır kasası — "donanım mı, yazılım mı?" (3 test)

1. **Fiziksel var mı?** çip/modül/pin → bu dal; kavram/algoritma → K000.
2. **Değiştirilebilir mi sadece kodla?** evet → K000; hayır (donanım
   değişmeli) → bu dal. *Örn: RAM kapasitesi donanım; swap boyutu K000.*
3. **Firmware sürümü mü, donanımın kendisi mi?** sürüm/parametre → K000;
   **çipin kendisi** → bu dal.

**Uygulama**: `CoreMusic\Cache\MemoryAdapter` → **test 1 fails** (kod) → K000.
`DDR5 DIMM` → **test 1 pass** (modül) → bu dal. `SPI flash` → bu dal; flash'a
**yazan sürücü** → K000.

### 3.2 Sınır kasası — ECC istisnası

ECC **donanımın** özelliğidir (fazla bit + düzeltme devresi) → bu dal.
Ancak **hatayı yöneten politika** (cevre/tekrar) → K000. **Kural**: ECC
**donanım yeteneği** olarak yazılır; **hata istatistikleri** ölçüme (M6) girer.

### 3.3 Ölçüm gürültüsü ve sınır (bu dalın ölçümle imtihanı)

Depo ölçümü bu dala **özel** tuzaklar taşıyor — sınırlar burada yazılı:

| Gürültü | Ne | Sınır kararı |
|---|---|---|
| `swap` 64'ün 57'si | `font-display: swap` (CSS) | **OS swap ölçümü = 0**; CSS → UI, dal dışında |
| `cache` 857 | PHP + HTTP + CSS | **Yazılım** → K000 (§6.1) |
| `ram` ham 2575 | alt-dize gürültüsü | `\bram\b` 8/6 → **yine envanter değil** |
| `flash` 13 | FOUT / flash loan / linker | **Donanım flash = 0** → Q-M1 |
| `bellek` 19 | OS bellek yönetimi dokümanı | **K000** → yalnız sınır işareti |

**Kural**: bu beş gürültü **her yeniden ölçümde** tekrar sınıflandırılır;
sınıflandırma yapılmadan **hiçbir sayı donanım kanıtı sayılmaz**.

---

## 4. Derinlik hedefi (bu dalın ne kadar ineceği)

| Katman | Hedef derinlik | Örnek |
|---|---|---|
| Fiziksel | **Tam** — modül formu, pin, konnektör | DIMM 288-pin (DDR5) |
| Çip | **Kapsam** — DRAM çipi, EEPROM/flash çipi | "DDR5 UDIMM", "SPI NOR" |
| Arayüz | **Kapsam** — bellek denetleyicisi arayüzü | "Dual-channel" |
| Yönetim | **Yok** → K000 | sayfa atama, swap |
| Uygulama | **Yok** → K000/K015 | PHP cache |
| **Toplam** | **≤ 2 katman** (fiziksel + çip/arayüz) | K001-donanım dalı |

**Kural (özet)**: bu dal **en alt iki katmanda** durur; **"bellek yönetimi
şeması değildir"**.

### 4.1 Örnek derinlik kararları (ne iner, ne inmez)

| Soru | Derinlik kararı | Gerekçe |
|---|---|---|
| "RAM kaç GB?" | ✅ **Yazılır** (donanım miktarı) | Fiziksel envanter — §2.1 |
| "Swap alanı kaç GB?" | ❌ **Yazılmaz** → K000 | OS ayarı (§3/2) |
| "DDR5-5600 mü, DDR4-3200 mü?" | ✅ **Yazılır** (JEDEC nesli, §8.1) | Donanım + standart |
| "Kanal sayısı (dual-channel) nasıl açılır?" | ⚠️ **Sınır** | Donanım **yetenek** yazılır; BIOS ayarı K000 |
| "Apcu belleği kaç MB?" | ❌ → K000 | Yazılım (§6.1) |
| "PageCache nerede saklanır?" | ❌ → K000 | Yazılım (§6.1) |
| "ECC hatası istatistiği?" | ⚠️ **Ölçüm (M6)** | Donanım **yeteneği** bu dalda; istatistik ölçüm kaydı |
| "SPI flash'a hangi sürücü yazar?" | ❌ → K000 | Sürücü (§3/1) |
| "Önbellek diski fiziksel var mı?" | ✅ **Aranır** (Q-M2, M3) | Donanım envanteri — kapı M3 |

**Özet**: her soru **§3.1'deki 3 testten** geçirilir; karar **tabloya** işlenir,
**sınır her seferinde görünür** yazılır (tahmin yok).

---

## 5. Atıf ve yetki

| Kaynak | § | Ne verir | Erişim |
|---|---|---|---|
| `ADR-102-tur-taksonomi-genislemesi-5-dal.md` | §4 | Dal kapsamı: "memory = bellek donanımı (RAM DDR/SO-DIMM, EEPROM/flash, önbellek depolama donanımı); bellek yönetim algoritması + sanal bellek → K000-isletim-sistemi" | yerel |
| `ADR-102-tur-taksonomi-genislemesi-5-dal.md` | §4.2 | Kısıt: **ADR-038 (XMOS XU316) değişmez · PCM5122 yasak · ADR-089 Class AB geçersiz kılınamaz** · güç/PCB → K002-electroncis çapraz referans | yerel |
| `https://www.jedec.org/` | ana sayfa | **JEDEC** standartları: DDR SDRAM/HBM · LPDDR · Flash UFS/e.MMC/SSD/XFMD · JESD21-C · JC-45 DRAM modülleri · JC-42 | 2026-10-13 ✅ |
| `.ai/architecture/K001-donanım/donanaım-type/` | dizin | Kardeşler: audio · cables · cpu · display · hdd · power · ssd · **network** | yerel |

> **§4.2 uygulaması (bu dal için)**: bellek **güç/PCB/sinyal** kararları K002'ye
> devredilir; **ADR-038 / PCM5122 / ADR-089** ses zinciri kısıtları bu dalda
> **dokunulmaz**.

### 5.1 Kısıt uygulama kontrolü (bu dalın ADR-102 §4.2 sınavı)

| # | Kısıt (ADR-102 §4.2) | Bu dalda uygulaması | Durum |
|---|---|---|---|
| 1 | **ADR-038 (XMOS XU316) değişmez** | XU316'nın **bellek donanımı** bu dala girse bile ADR-038 **revize edilmez**; dal yalnız **envanter** satar | ✅ dokunulmadı |
| 2 | **PCM5122 yasak** | DAC donanımı — bu dalda **hiç geçmez**; öneri/alternatif **yazılmaz** | ✅ yok |
| 3 | **ADR-089 Class AB geçersiz kılınamaz** | Güç amplifikatörü — bellek dalının **konusu değil**; **tek satır bile** geçersiz kılmaz | ✅ dokunulmadı |
| 4 | **Güç/PCB → K002 çapraz referans** | Bellek **güç/PCB/sinyal** kararları §3/9'da K002'ye devredildi | ✅ işaretli |
| 5 | **`donanaım-type` yazım yasağı** | Dosya/başlık yollarında typo **korundu** — düzeltme yok | ✅ korundu |

**Kural**: bu tablo **her revizyonda** tekrar denetlenir; bir kısıt **ihlal
edilmişse** dal `status: proposed`'da **durdurulur** ve üst karar (ADR-102)
güncellenmeden **ilerlenmez**.

---

## 6. Ölçüm özeti (kapsamın kanıt dayanağı)

Tam tablolar + yöntem: `memory-dogrulama.md` §3–5 (aynı oturum ölçümü:
2026-10-13 · 1338 dosya / 25.752.595 bayt · directory_tree + read_multiple_files).

| Terim grubu | Ölçüm | Kapsam etkisi |
|---|---|---|
| `ram` (sınırlı `\bram\b`) | 8 / 6 dosya | Agent/prompt metni → **envanter yok** |
| `ram` (ham) | 2575 / 687 | **Gürültü** (prog**ram**/di**ram**a) → yorum **sınırlı** sayıya (§6.1) |
| `ddr` · `dimm` · `nvram` | 0 · 0 · 0 | **Donanım envanteri yok** → Q-M1 + M2 |
| `eeprom` | 1 / 1 | Arşiv soru dosyası → envanter **değil** |
| `flash` (ham) | 13 / 8 | CSS FOUT · flash loan · linker script → **donanım 0** |
| `cache` (sınırlı) | **857 / 166** | **PHP 146/30 + non-PHP 711/136 = YAZILIM** → K000 (Q-M2) |
| `bellek` | 19 / 10 | K000 bellek yönetimi + güvenlik → **DIŞINDA** |
| `swap` (ham) | 64 / 9 | **57'si `font-display: swap`** → OS swap ölçümü **0** |

**Önemli ayrım (§6.1)**: `shared/src/Cache/` **6 sınıf** (`ApcuAdapter`,
`CacheInterface`, `CacheManager`, `MemoryAdapter`, `PageCacheAdapter`,
`PageCacheInterface`) — **kanıt**: bunlar **PHP namespace'idir**, donanım
**değil**. Bu yüzden 857 sayısı **asla** "857 önbellek donanımı" diye
**yorumlanmaz** (network dalındaki C-N05 mantığının eşdeğeri: C-M05).

### 6.1 Ölçüm gürültüsü sınıflandırması (ham sayı → doğru anlam)

| Ham sayı | Gürültü kaynağı (dosya içeriği) | Doğru yorum |
|---|---|---|
| `ram` 2575 | `prog**ram**` · `di**ram**a` · `p**ram**` alt-dizeleri | **Gürültü** → `\bram\b` = 8/6 kullanılır |
| `cache` 857 | `CoreMusic\Cache\` (146/30 PHP) + HTTP/CSS/doküman (711/136) | **%100 yazılım** → K000 |
| `swap` 64 | **57 / 3 dosya** = `font-display: swap` (CSS token) | **CSS** → OS swap ölçümü **0** |
| `flash` 13 | CSS FOUT · flash loan (fintech) · linker script · soru arşivi | **Donanım flash = 0** |
| `bellek` 19 | K000 bellek yönetimi + güvenlik dokümanı | **DIŞINDA** (K000) |
| `eeprom` 1 | `.claude/skills/prompt-maker/.archive/...` (soru arşivi) | **Envanter değil** → Q-M1 |

**Kural (sınıflandırma)**: her ham sayı **dosya-içeriğiyle** sınıflandırılır;
sınıflandırma **yapılmadan** yorum **yasaktır**. Sınıf → kapsam eşlemesi:
`donanım` bu dal · `yazılım` K000 · `UI/CSS` yok say · `doküman` atıf.

---

## 7. Terim sözlüğü (kısa)

| Terim | Anlamı | Kapsam |
|---|---|---|
| **DIMM / SO-DIMM** | Bellek modülü formu (masaüstü/dizüstü) | ✅ |
| **DDR4/DDR5** | DRAM nesli (JEDEC standardı) | ✅ |
| **DRAM/SRAM/ROM** | Çip türleri (dinamik/statik/kalıcı) | ✅ |
| **ECC** | Hata düzeltme donanımı | ✅ |
| **EEPROM** | Elektrikle silinen kalıcı bellek çipi | ✅ |
| **NVRAM** | Güç kesilse de kalan SRAM | ✅ |
| **SPI NOR/NAND flash** | Gömülü kod/depolama flash'ı | ✅ |
| **Bellek denetleyicisi** | Donanım bloğu (algoritma → K000) | ✅ (blok) |
| **Önbellek (donanım)** | Depolama tabanlı hızlı katman **donanımı** | ✅ (donanım) |
| **Sanal bellek / swap** | OS sayfalama | ❌ → K000 |
| **PageCache / Apcu / malloc** | Yazılım belleği | ❌ → K000 |
| **`font-display: swap`** | CSS font metni | ❌ → UI |

---

## 8. Kaynakça (erişilen web + yerel)

### 8.1 Web — bu oturumda gerçekten erişildi (erişim: 2026-10-13)

| # | URL | Ne doğrulandı | Erişim |
|---|---|---|---|
| 1 | `https://www.jedec.org/` | **JEDEC** — DDR SDRAM/HBM · LPDDR · Flash UFS/e.MMC/SSD/XFMD · JESD21-C (standartlar kataloğu) · **JC-45** DRAM modülleri · **JC-42** | 2026-10-13 ✅ |

> **Uyarı (WWW)**: `jedec.org/technology-focus-areas/main-memory-ddr-sdram-hbm`
> **404** → **kullanılmadı**; elenme kaydı: `network-dogrulama.md` §6.2 (W-X2).

### 8.2 Yerel (depol)

| Dosya | § | Ne verir |
|---|---|---|
| `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` | §4, §4.2 | Dal kapsamı + kısıtlar |
| `shared/src/Cache/` | 6 sınıf | **Yazılım cache** kanıtı (K000 sınırı) |
| `hdd/` (kardeş dal) | index + 3 | Derinlik/şablon modeli |
| `network/` (kardeş dal) | 4 dosya | Ortak ölçüm defteri (`network-dogrulama.md` §5) |

---

## 9. Dürüstlük bloğu (QUANTIFIED)

- **Envanter**: RAM/EEPROM/flash/önbellek-donanımı **envanteri depoda
  doğrulanmadı** → §2.4 **UNKNOWN (Q-M1/Q-M2/Q-M3)** · kapı **M2/M3**.
  **Uydurma yok.**
- **Ölçüm (2026-10-13)**: `ddr` `dimm` `nvram` = **0**; `ram` sınırlı **8** (ham
  2575 = gürültü §6.1); `cache` **857 = yazılım** (PHP 146/30 + non-PHP 711/136 ·
  `shared/src/Cache/` 6 sınıf); `swap` 64'ün 57'si **CSS `font-display`**;
  `bellek` 19 = **K000 yönetimi** → **donanım kanıtı 0**.
- **Yorum kuralı**: ham sayılar (`ram` 2575 · `cache` 857 · `swap` 64)
  **ham raporlandı**, **donanım sayımı olarak yorumlanmadı**.
- **Web**: **1 URL erişildi** (JEDEC, S5); 404 URL **kullanılmadı** (§8.1 uyarısı).
- **Kısıt uygulaması**: ADR-038 · PCM5122 · ADR-089 **dokunulmaz** (§5);
  güç/PCB → K002; **ECC/algoritma ayrımı** §3.2'de yazılı.
- **Kardeş sınır**: `ssd/` (depolama flash'ı) · `cpu/` (L2/L3) · `network/`
  (ortak ölçüm defteri) — §2.3, §4.3, `index` §4.

---

## 10. Bellek nesilleri ve modül formları (DDR4/DDR5 · DIMM/SO-DIMM)

> **Kanıt durumu**: bu bölümdeki **sayısal** iddialar (pin, hız, zamanlama)
> **JEDEC 403 → doğrulanamadı** → her satır **⚠️ VERIFICATION REQUIRED**
> işaretiyle taşınır (`memory-dogrulama.md` §6.4 yeniden ölçüm kaydı).

### 10.1 Nesil karşılaştırma tablosu (kavramsal — ölçüm yok)

| # | Boyut | DDR4 | DDR5 | Kanıt |
|---|---|---|---|---|
| 1 | JEDEC nesli | ✅ kapsama (§7 sözlük) | ✅ kapsama | ⚠️ VR (jedec.org 403) |
| 2 | Pin / modül formu | ⚠️ VR | ⚠️ VR | ölçüm yok |
| 3 | Çalışma gerilimi | ⚠️ VR | ⚠️ VR | ölçüm yok (K002 sınırı) |
| 4 | Hız (MT/s) | ⚠️ VR | ⚠️ VR | yalnız **formül (S2)** — `memory-ornekler.md` §4.2 |
| 5 | Projede kullanılan nesil | **UNKNOWN (Q-M1)** | **UNKNOWN (Q-M1)** | `ddr[45]` = 0 (ölçüm) |
| 6 | Çift-kanal (dual-channel) | donanım **kabiliyeti** | donanım **kabiliyeti** | BIOS/OS tarafı → K000 (§4.1) |

**Kural**: bu tabloda **hiçbir** hücre "bu projede var" **diye kullanılamaz**;
§2.4/13 satırındaki **UNKNOWN**, envanter (kapı M2) gelene kadar **kalır**.

### 10.2 Modül form faktörü tablosu (envanter beklentisi — BOŞ)

| # | Form | Kullanım alanı | Projedeki kayıt | Kanıt |
|---|---|---|---|---|
| 1 | DIMM (masaüstü) | anakart yuvası | **UNKNOWN** | `dimm` = 0 |
| 2 | SO-DIMM (dizüstü/kompakt) | dizüstü yuvası | **UNKNOWN** | `so-?dimm` = 0 |
| 3 | RDIMM (tamponlu) | sunucu/sunucu sınıfı | **UNKNOWN** | ölçüm yok |
| 4 | Lehimli (on-package / LPDDR sınıfı) | SoC gömülü | **UNKNOWN** | ölçüm yok |

> **Sınır**: form faktörü **donanımın kendisidir** → bu dal; **yuva/BIOS
> yapılandırması** → K000. Form boşken "SO-DIMM var" cümlesi **yazılmaz**.

### 10.3 JEDEC kaynak durumu (bu oturumun yeniden ölçümü)

| # | URL | HEAD sonucu (2026-10-13) | Karar |
|---|---|---|---|
| 1 | `https://www.jedec.org/` | **HTTP 403** | **kullanılmaz** → tüm JEDEC iddiaları **⚠️ VERIFICATION REQUIRED** |
| 2 | `jedec.org/.../main-memory-ddr-sdram-hbm` | HTTP 404 (önceki oturum) | elendi (W-X2) |

**Çelişki kaydı (korunur, silinmez)**: §5/§8.1'deki "jedec.org ✅ erişildi"
satırı **eski oturum kaydıdır**; **bu oturumun HEAD 403 ölçümü** onu
**geçersiz kılmaz — tamamlar**: erişim **tartışmalıdır** → yeniden ölçümden
sonra JEDEC **yalnız** "standart kuruluşu" düzeyinde anılabilir, **sayısal
spesifikasyon kaynağı olarak kullanılamaz** (`memory-dogrulama.md` §6.4).

---

### 10.4 Bellek nesli/modül envanter kontrol listesi (BOŞ — kapı M2)

| # | Kontrol | Komut/gözlem (şablon) | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Nesil (DDR4/DDR5) | OS bellek okuması (K000 aracı) | `MEASURE` | `YYYY-MM-DD` |
| 2 | Kapasite (toplam) | gözlem + okuma | `MEASURE` | `YYYY-MM-DD` |
| 3 | Modül formu (DIMM/SO-DIMM) | gözlem | `MEASURE` | `YYYY-MM-DD` |
| 4 | Slot doluluğu | gözlem | `MEASURE` | `YYYY-MM-DD` |
| 5 | Hız (MT/s) | okuma (SPD) | `MEASURE` | `YYYY-MM-DD` |
| 6 | Kanal (single/dual) | okuma | `MEASURE` | `YYYY-MM-DD` |
| 7 | ECC varlığı | okuma + gözlem | `MEASURE` | `YYYY-MM-DD` |
| 8 | Üretici/parça no | çip/etiket | `UNKNOWN` | `YYYY-MM-DD` |

**Kural**: 1–8'den **hiçbiri** bu oturumda **doldurulmadı** (hepsi `MEASURE`/
`UNKNOWN`); doldurulunca `memory-dogrulama.md` §8.1 formu ve kapı **M2**
tekrar çalıştırılır — **satır silinmez, hücre güncellenir**.

---

## 11. ECC vs non-ECC ve hata tespit/düzeltme donanımı

### 11.1 Karşılaştırma tablosu (kavramsal — envanter yok)

| # | Boyut | non-ECC | ECC | Kapsam |
|---|---|---|---|---|
| 1 | Fazla (parity/ECC) bit | yok | **var** — donanım özelliği | ✅ bu dal (§2.1/3) |
| 2 | Düzeltme devresi | yok | **var** — çip/modül donanımı | ✅ bu dal |
| 3 | Tek-bit düzeltme | yapmaz | **yapabilir** (donanım yeteneği) | ✅ yetenek |
| 4 | Çift-bit tespiti | yapmaz | **tespit** (düzeltme yok) ⚠️ VR | ⚠️ VR — JEDEC 403 |
| 5 | Hata **istatistiği** politikası | — | OS/firmware yönetimi | ❌ → K000 (§3.2) |
| 6 | Projede ECC var mı? | **UNKNOWN (Q-M1)** | **UNKNOWN (Q-M1)** | kapı M2 |

### 11.2 Hata tespit/düzeltme — katman matrisi (donanım/algoritma ayrımı)

| # | Katman | Ne | Sahip |
|---|---|---|---|
| 1 | Ham bit (çip üstü ECC alanı) | fazla bit üretimi/denetimi | **bu dal (donanım)** |
| 2 | Modül SEEPROM/spd alanı | modül kimlik çipi (envanter yüzeyi) | **bu dal** ⚠️ VR |
| 3 | Bellek denetleyicisi (donanım bloğu) | parite/ECC denetim devresi | **bu dal (blok)** — §2.1/4 |
| 4 | Hata kaydı okuma (istatistik) | OS/firmware arayüzü | ❌ → K000 |
| 5 | Hata **yanıt politikası** (retry/crash) | çekirdek kararı | ❌ → K000 |
| 6 | Uygulama seviyesi checksum | yazılım bütünlüğü | ❌ → uygulama/K000 |

**Ölçüm durumu**: depoda `ecc` = 3 eşleşme (doküman/agent metni) →
**donanım ECC kanıtı 0** (`memory-dogrulama.md` §4).

### 11.3 ECC sınır-kayıt örneği

```text
DOĞRU KAYIT:
  [a] ECC donanım yeteneği (fiziksel kayıt — type_id envanterle atanır: `M-T__`):
        physical_component_or_device : ECC destekli DIMM + denetleyici devresi
        measurement                   : ⚠️ VERIFICATION REQUIRED (envanter yok — Q-M1)
        status                        : proposed
  [b] Hata politikası (SINIR):
        physical_component_or_device : YOK
        owner_domain                 : K000-isletim-sistemi
        status                        : proposed (sınır)

YANLIŞ KAYIT:
  "ECC bellek kullanılıyor" (envanter kanıtsız) → K-N2 benzeri RED —
  §2.4/15 mantığı: ölçüm yoksa UNKNOWN, cümle yasak.
```

---

## 12. M.2 ve katmanlı (stacked) bellek — biçim/sınır kayıtları

### 12.1 M.2 — kimin konusu? (ssd/ sınırı)

| # | M.2 yüzeyi | Karar | Gerekçe |
|---|---|---|---|
| 1 | M.2 **yuvası** (fiziksel konnektör) | sınır — anakart/çevre birimi | §3.1 test 1 |
| 2 | M.2'ye takılı **NAND/SSD modülü** | ❌ `ssd/` dalı | §2.2/9 (depolama flash'ı) |
| 3 | M.2'ye takılı **DRAM modülü** (varsa) | ✅ bu dal | §2.1/1 |
| 4 | M.2 **PCIe/NVMe etiketi** | ❌ protokol etiketi → K000/ssd | katman-4 etiket yasağı |
| 5 | M.2'de **SPI flash** (firmware) | ✅ bu dal (çip) · sürüm → K000 | §3.1 test 3 |

### 12.2 Katmanlı (stacked) bellek / HBM — durum kaydı

| # | Öğe | Kapsam | Durum |
|---|---|---|---|
| 1 | 3D stacking / katmanlı DRAM paketleme | ✅ **kavramsal** bu dal (çip biçimi) | ⚠️ VR (JEDEC 403) |
| 2 | HBM (yüksek bantlı bellek) sınıfı | ✅ kavramsal | ⚠️ VR — ölçüm/-envanter **0** |
| 3 | GPU üzerinde HBM kullanımı | sınır → `gpu/` dalı (G-T01…G-T07) | çapraz referans |
| 4 | Paket içi bellek (SoC SRAM) | ✅ §2.1/2 | envanter UNKNOWN |
| 5 | Projede stacked bellek var mı? | **UNKNOWN (Q-M1)** | kapı M2 |

> **Kural**: "HBM/stacking" **terminoloji olarak** bu dalda durur; **ürün/model
> iddiası** (kimde, kaç katman) **envanter + kaynak** gelmeden **yazılmaz**.

---

### 12.3 Repo ölçümü ile biçim kayıtlarının kesişimi (2026-10-13)

| Terim | Eşleşme/Dosya | Kırılım | Sınıf |
|---|---|---|---|
| `m2` (M.2 yuvası) | ölçüm yapılmadı — | bu oturumda **aranmadı** | **UNKNOWN** (aranmayan sayılmaz) |
| `nvme` | ölçüm yapılmadı — | bu oturumda **aranmadı** | **UNKNOWN** |
| `ddr` · `dimm` · `so-?dimm` | 0 · 0 · 0 | tüm dosya seti | **envanter yok → Q-M1** |
| `hbm` | ölçüm yapılmadı — | — | **UNKNOWN** |
| `ecc` | 3 / 3 dosya | doküman/agent metni | **donanım kanıtı 0** |

**Kural**: `m2`/`nvme`/`hbm` için **bu oturumda ölçüm YAPILMADI** → sayı
**uydurulmaz**, "aranmadı/UNKNOWN" yazılır; ölçüm istenirse §6'daki yöntem
(2026-10-13 seti) **aynen tekrarlanır** ve `memory-dogrulama.md` §4'e **yeni
satır** eklenir (eski satır silinmez).

---

## 13. OS tespit sınırı ve güç (self-refresh) — sahiplik

### 13.1 K000 os-donanim-tespiti sahipliği (bu dal neyi devreder)

| # | Soru | Sahip | Bu dalın payı |
|---|---|---|---|
| 1 | "OS belleği nasıl tespit eder?" | **K000 os-donanim-tespiti** | yok — yalnız **sonuç** envantere girer |
| 2 | "RAM kaç GB görünüyor?" | K000 okuması → **veri** bu dala gelir | veri alıcısı |
| 3 | "DDR nesli nasıl okunur?" (SPD/`dmidecode` benzeri) | K000 aracı | **SPD çipi** bu dalın envanter yüzeyi |
| 4 | "Swap/sayfa tablosu?" | K000 | yok |
| 5 | "Bellek denetleyicisi sürücüsü?" | K000 | **blok** bu dalda (§2.1/4) |
| 6 | "ECC hatası logu?" | K000 logları | **donanım yeteneği** bu dalda (§11) |

**Devir kaydı**: tespit **yöntemi** (komut/kütüphane/sürücü) K000'de **kalır**;
bulunan **donanım gerçeği** (tip · kapasite · form · ECC) bu dalın §2.1/2.2
envanterine **işlenir**. Sınır **iki yönlü tek satırla** yazılır:
`owner: tespit → K000 · envanter → K001(memory)`.

### 13.2 Güç: self-refresh ve düşük güç durumları (sınır kasası)

| # | Kavram | Fiziksel mi? | Karar |
|---|---|---|---|
| 1 | **Self-refresh** (DRAM'in kendi kendini yenilemesi) | ✅ donanım davranışı | **bu dal** — modül/çip özelliği olarak |
| 2 | Self-refresh **girişi/çıkışı kararı** | ❌ denetleyici/OS politikası | → K000 (§3/1) |
| 3 | Çalışma gerilimi / VDD | ✅ donanım | **K002'ye devir** (ADR-102 §4.2 — güç/PCB) |
| 4 | Düşük güç durumu (APD/S4) | ❌ OS politikası | → K000 |
| 5 | Bellek **güç ölçümü** | ✅ ölçüm | **0 bu oturum** → kapı M6 ⚠️ |

**Kural**: self-refresh **"var mı/çipin özelliği"** olarak yazılır;
**"ne zaman açılır"** asla bu dalda **anlatılmaz** (K000). Güç/**PCB**/**sinyal**
kararları → **K002-electroncis** (§5/§4.2).

### 13.3 Sınır-kayıt özeti (bu bölümün üç testi)

| Öğe | Test 1 (fiziksel) | Test 2 (yalnız kod) | Test 3 (sürüm/çip) | Karar |
|---|---|---|---|---|
| DDR5 DIMM | ✅ | hayır | — | **bu dal** |
| self-refresh davranışı | ✅ (çip) | hayır | — | **bu dal (özellik)** |
| self-refresh politikası | ❌ | ✅ | — | **K000** |
| `dmidecode` benzeri okuma | ❌ | ✅ | — | **K000** |
| SPD çip kimliği | ✅ | hayır | çipin kendisi | **bu dal (envanter)** |
| ECC hata istatistiği | ❌ | ✅ | sürüm/log | **K000** (ölçüm M6) |

---

## 14. İlgili dosyalar

| Dosya | İçerik |
|---|---|
| [`index.md`](index.md) | Dal ana sayfası · durum · gruplar · açık sorular |
| [`memory-dogrulama.md`](memory-dogrulama.md) | Kanıt seviyesi S1–S5 · ölçüm tabloları (RAM/DDR/cache/swap) · **kapılar M1–M6** · JEDEC URL doğrulama |
| [`memory-ornekler.md`](memory-ornekler.md) | Depodaki **gerçek** bellek kanıtı (`shared/src/Cache/`) + envanter/ölçüm **şablonları** |
| `../hdd/` | Kardeş dal — derinlik/şablon modeli |
| `../network/` | Kardeş dal — ortak ölçüm defteri |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
