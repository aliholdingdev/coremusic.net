---
node_id: K001-donanım/donanaım-type
node_type: subdomain
status: draft
parent_id: "K001-donanım"
children: [hdd, audio, ssd, display, power, cables, network, memory, cpu, gpu, mikrodenetleyici]
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı (yazım kaynağa sadık: donanaım-type)"
  - "donanım-anayasa.md §3 — 3 alt bağlam"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Envanter eksik: ekran, kablo, güç kaynağı bu ağaçta yok (K001 Q4) — STALE işareti (2026-10-14): display/cables/power DALLARI AÇILDI; eksik olan artık dal varlığı değil, marka/model ENVANTERİDİR (UNKNOWN, K001 Q4)"
group: mimari
---
# K001-donanım/donanaım-type — ELI15
Donanımın **türlerine** göre ayrıldığı raf: içinde hangi parçaların olduğu değil, hangi sınıftan parçaların bulunduğu taranır. **11 alt bağlam:** hdd · audio · ssd · display · power · cables · network · memory · cpu · gpu · mikrodenetleyici (display · power 2026-10-13 önceki oturum; cables · network · memory · cpu 2026-10-13 bu oturum — ADR-102 taslağı `PROPOSED — APPROVAL REQUIRED`; gpu 21:52–22:49 paralel Vault Steward oturumu — ADR-102 kapsamı DIŞI, 4/4 dosya tamam, 22:55 sayımı 46 md; mikrodenetleyici 2026-10-14 Vault Steward oturumu — ADR-102 kapsamı DIŞI, 4/4 dosya tamam, PIC (XC8) · ATmega (avr-gcc) · Arduino (PlatformIO), sayım 11 dal × 4 = 44 + audio/cipler 3 + kök 3 = **50 md**).
> yazım notu: donanaım-type yazımı v2 kaynağına sadıktır — **düzeltme yasak** (v2 §3 KURAL 1). Klasör adı bu yazımı taşır.
> ⚠️ VERIFICATION REQUIRED: v2 bu düğümü yalnız adıyla tanımlar; envanter (ekran/kablo/güç kaynağı) bilinmiyor — UNKNOWN olarak bırakıldı.
## §ÖRNEKLER — Baş katalog (her sistem · her yazılım türü)
> **Amaç:** 10 sistem (dal) + `audio/cipler/` derin-dalının örnek dosyaları tek bakışta — satırlar ilgili dalın `index.md` / `-ornekler.md` dosyalarından derlenmiştir, uydurma kod yoktur.

| # | Sistem (dal) | Örnek dosyası | Örnek türleri | Yazılım türü (bulunan) |
|---|---|---|---|---|
| 1 | audio | `audio/audio-ornekler.md` | I2S/TDM pin · init sırası · kanal eşleme · jitter şablonları | tablo / şablon |
| 2 | hdd | `hdd/hdd-ornekler.md` | kütüphane tarama · streaming-buffer · 3-2-1 rotasyon adımları | `text` akış şeması · tablo |
| 3 | ssd | `ssd/ssd-ornekler.md` | indeksleme · önbellek · sağlık izleme şablonları | şablon/form (`ssd-dogrulama.md` içinde `powershell` ölçüm bloğu) |
| 4 | display | `display/display-ornekler.md` | EDID/DDC okuma · panel↔arayüz eşleme · dokunma kalibrasyon formu | `text` şablon · boş form |
| 5 | power | `power/power-ornekler.md` | AC/DC–DC/DC–LDO · sigorta sayfası · güç ağacı çalışma sayfası | `text` şablon · form |
| 6 | cables | `cables/cables-ornekler.md` | katman ayrımı · pin/katman çalışma sayfaları · 17 tip × 4 katman provası | `text` şablon · form |
| 7 | network | `network/network-ornekler.md` | ayrım örnekleri · pin/port çalışma sayfaları · envanter formları | `text` şablon · form |
| 8 | memory | `memory/memory-ornekler.md` | envanter/ölçüm/ECC/M.2 şablonları · `shared/src/Cache/` 6 sınıf kaydı | `json` · `text` · repo kanıtı (tablo) |
| 9 | cpu | `cpu/cpu-ornekler.md` | CPU envanter · PHP yürütme bağlamı · JIT/önbellek ölçüm şablonları | `php` (3 blok) · form |
| 10 | gpu | `gpu/gpu-ornekler.md` | envanter formları · kapı adımları · PLANNED kod blokları | `text` şablon · tablo |
| — | audio/cipler | `audio/cipler/ak4458-dac.md` · `pcm3168a-codec.md` · `tpa3255-amplifier.md` | çip künyesi — kod yok (mimari derin-dal) | markdown künye |

**Yazılım türü özeti (bu ağaçta gerçekten bulunan tek formatlar):** `php` (cpu) · `json` (memory) · `powershell` (ssd-dogrulama) · `text` (hdd · display · power · cables · network · gpu) · markdown tablo (tüm dosyalar).
> ⚠️ Dürüstlük notu: çoğu daldaki "örnek" **gerçek repo kodu değildir** — `cables-ornekler` §1 (kod YOK) · `display-ornekler` §1 (repo-code YOK) · `cpu-ornekler` §3 (kod yok kaydı) · `power/index` (700 dosya / 0 eşleşme). Bunlar şablon/boş formdur; dolu örnek iddiası üretilmez. Bu ağaçta JS · C++ · SQL örneği **YOK** (UNKNOWN).
## §GRUPLAR
> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanaım-type-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `donanaım-type-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| kod | `donanaım-type-ornekler.md` | Taksonomi kayıt örüntüleri · zincir şablonları · kapı kartları | Kademe-2 |
| mimari | `hdd/hdd-kapsam.md` | Mekanik disk kapsamı (kapasite · risk · 3-2-1) | Kademe-3 |
| kanit | `hdd/hdd-dogrulama.md` | HDD kapıları: SMART · yüzey · gürültü · kurtarma | Kademe-3 |
| kod | `hdd/hdd-ornekler.md` | Tarama · streaming-buffer · rotasyon şablonları | Kademe-3 |
| mimari | `ssd/ssd-kapsam.md` | Katı hal kapsamı: flash · WAF · TRIM · TBW · boot | Kademe-3 |
| kanit | `ssd/ssd-dogrulama.md` | SSD kapıları: hız · TRIM · sağlık · sıcaklık | Kademe-3 |
| kod | `ssd/ssd-ornekler.md` | İndeksleme · önbellek · izleme şablonları | Kademe-3 |
| mimari | `audio/audio-kapsam.md` | Ses donanımı kapsamı: PCM3168A · AK4458 · TPA3255 · ADR-038 (TEYİT) | Kademe-3 |
| kanit | `audio/audio-dogrulama.md` | Ses kapıları: M1–M6 eşik · envanter · HEAD-200 kanıt matrisi | Kademe-3 |
| kod | `audio/audio-ornekler.md` | I2S/TDM pin · init sırası · kanal eşleme · jitter şablonları | Kademe-3 |
| mimari | `display/display-kapsam.md` | Display kapsamı: panel↔arayüz ayrımı · D-T01…D-T08 · §0 çatışma kaydı (2026-10-13) | Kademe-3 |
| kanit | `display/display-dogrulama.md` | Display kapıları K-D1…K-D8 · EDID/piksel/giriş-lag formları · HEAD kaydı | Kademe-3 |
| kod | `display/display-ornekler.md` | EDID/DDC şablonları · panel↔arayüz eşleme · dokunma kalibrasyon formu | Kademe-3 |
| mimari | `power/power-kapsam.md` | Güç kapsamı: P-T01…P-T13 · §0 çatışma kaydı · K002/ADR-063/064 sınırı | Kademe-3 |
| kanit | `power/power-dogrulama.md` | Güç kapıları K-P1…K-P8 · PSU/LDO/koruma ölçüm formları · HEAD kaydı | Kademe-3 |
| kod | `power/power-ornekler.md` | AC/DC–DC/DC–LDO şablonları · sigorta sayfası · güç ağacı çalışma sayfası | Kademe-3 |
| mimari | `cables/cables-kapsam.md` | Kablo/konnektör kapsamı: C-T01…C-T08 · §0 çatışma kaydı · K002 sınırı (2026-10-13) | Kademe-3 |
| kanit | `cables/cables-dogrulama.md` | Kablo kapıları K-C1…K-C8 · ölçüm formları · HEAD kaydı | Kademe-3 |
| kod | `cables/cables-ornekler.md` | Konnektör/kablo şablonları · pin-out formları | Kademe-3 |
| mimari | `network/network-kapsam.md` | Ağ arayüzü kapsamı: N-T01…N-T08 · §0 çatışma kaydı (2026-10-13) | Kademe-3 |
| kanit | `network/network-dogrulama.md` | Ağ kapıları N1–N6 · PHY/NIC ölçüm formları · HEAD kaydı | Kademe-3 |
| kod | `network/network-ornekler.md` | Ağ arayüzü şablonları · envanter formları | Kademe-3 |
| mimari | `memory/memory-kapsam.md` | Bellek kapsamı: M-T01…M-T08 · §0 çatışma kaydı (2026-10-13) | Kademe-3 |
| kanit | `memory/memory-dogrulama.md` | Bellek kapıları M1–M6 · DIMM/EEPROM ölçüm formları · HEAD kaydı | Kademe-3 |
| kod | `memory/memory-ornekler.md` | Bellek şablonları · envanter formları | Kademe-3 |
| mimari | `cpu/cpu-kapsam.md` | İşlemci kapsamı: P-Ç-T01… · §0 çatışma kaydı · ADR-038 sınırı (2026-10-13) | Kademe-3 |
| kanit | `cpu/cpu-dogrulama.md` | İşlemci kapıları · çekirdek/SoC ölçüm formları · HEAD kaydı | Kademe-3 |
| kod | `cpu/cpu-ornekler.md` | İşlemci şablonları · envanter formları | Kademe-3 |
| mimari | `gpu/index.md` | Gpu klasör künyesi ve sınır çatışma kaydı (2026-10-13, paralel oturum) | Kademe-3 |
| mimari | `gpu/gpu-kapsam.md` | GPU kapsamı: hesaplama/render · display sınırı · 26 alan · G-T01…G-T07 (2026-10-13, paralel oturum — ADR-102 kapsamı DIŞI) | Kademe-3 |
| kanit | `gpu/gpu-dogrulama.md` | GPU doğrulama: repo ölçümü (Vulkan/CUDA/OpenCL=0) · HEAD URL'ler · kapılar K-G1…K-G6 (2026-10-13, paralel oturum) | Kademe-3 |
| kod | `gpu/gpu-ornekler.md` | GPU şablonları · envanter formları · PLANNED kod blokları (2026-10-13 22:49, paralel oturum — 4/4 tamam) | Kademe-3 |
| mimari | `mikrodenetleyici/index.md` | Mikrodenetleyici bağlam künyesi — PIC (XC8) · ATmega (avr-gcc) · Arduino (PlatformIO) — 2026-10-14 Vault Steward (ADR-102 kapsamı DIŞI) | Kademe-3 |
| mimari | `mikrodenetleyici/mikrodenetleyici-kapsam.md` | Platform matrisi · 3 katmanlı mimari (uygulama→HAL→BSP) · 7 fazlı proje planı (F0–F7) · Guardrail #16 şablon bağımlılığı | Kademe-3 |
| kod | `mikrodenetleyici/mikrodenetleyici-ornekler.md` | [KALIP-PLANNED] kod blokları: HAL gpio arayüzü · ATmega BSP+blink+UART · PIC XC8 blink · Arduino sketch + platformio.ini — repo .c/.ino = 0 dürüstlük kapısı | Kademe-3 |
| kanit | `mikrodenetleyici/mikrodenetleyici-dogrulama.md` | 5 S1 repo ölçümü (glob 0 · şablon 3/3) · 8 URL HEAD 200 · kapılar K-M1…K-M5 | Kademe-3 |
| mimari | `hdd/index.md` | hdd klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `ssd/index.md` | ssd klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `audio/index.md` | audio klasör künyesi, çocuk kataloğu ve `cipler/` bağlantısı | Kademe-3 |
| mimari | `display/index.md` | display klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `power/index.md` | power klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `cables/index.md` | cables klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `network/index.md` | network klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `memory/index.md` | memory klasör künyesi, dosya linkleri ve çocuk kataloğu | Kademe-3 |
| mimari | `cpu/index.md` | cpu klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `audio/cipler/ak4458-dac.md` | AK4458 DAC çip derin-dalı künyesi (§7.4 — Kademe-4) | Kademe-4 |
| mimari | `audio/cipler/pcm3168a-codec.md` | PCM3168A codec çip derin-dalı künyesi (§7.4 — Kademe-4) | Kademe-4 |
| mimari | `audio/cipler/tpa3255-amplifier.md` | TPA3255 amplifikatör referans derin-dalı — Class D yasak (§7.4 — Kademe-4) | Kademe-4 |
> **Sayım doğrulaması (2026-10-15):** §GRUPLAR **51 satır = 51 md** (kök 4 + 11 dal × 4 + audio/cipler 3) — iki-ekseni kayıp yok; 2026-10-14'teki "34 + 12 = 46" sayımı `donanaım-type-ornekler.md` öncesi kök-3 içindi, bu oturumda kök 4 oldu.
---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12 · rev **2026-10-14** (§ÖRNEKLER baş kataloğu + §GRUPLAR 12 eksik satır + bayat açık soru notu) · **2026-10-15** (§GRUPLAR +ornekler = 51 satır · rewrap ≤100 · Durum tablosu FM ile mükerrer kaldırıldı)
