---
node_id: K001-donanım/donanaım-type
node_type: subdomain
status: draft
parent_id: "K001-donanım"
children:
  - hdd
  - audio
  - ssd
  - display
  - power
  - cables
  - network
  - memory
  - cpu
  - gpu
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı (yazım kaynağa sadık: donanaım-type)"
  - "donanım-anayasa.md §3 — 3 alt bağlam"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Envanter eksik: ekran, kablo, güç kaynağı bu ağaçta yok (K001 Q4)"
group: mimari
---

# K001-donanım/donanaım-type — ELI15

Donanımın **türlerine** göre ayrıldığı raf: içinde hangi parçaların olduğu değil,
hangi sınıftan parçaların bulunduğunu tarayan düğüm. **10 alt bağlam** açıldı:
hdd · audio · ssd · display · power · cables · network · memory · cpu · gpu
(display · power 2026-10-13 önceki oturum; cables · network · memory · cpu
2026-10-13 bu oturum — ADR-102 taslağı, `PROPOSED — APPROVAL REQUIRED`;
**gpu 21:52–22:49 paralel Vault Steward oturumu — ADR-102 kapsamı DIŞI,
4/4 dosya tamam** (`gpu-ornekler.md` 22:49'da yazıldı — 22:55 sayımı: 10 dal × 4 = 40
branch + audio/cipler 3 + kök 3 = 46 md).

> yazım notu: donanaım-type yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1). Klasör adı bu yazımı taşır.

> ⚠️ VERIFICATION REQUIRED: v2 bu düğümü yalnız adıyla tanımlar; envanter
> (ekran/kablo/güç kaynağı) bilinmiyor — UNKNOWN olarak bırakıldı.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K001-donanım |
| children | hdd · audio · ssd · display · power · cables · network · memory · cpu · gpu (bounded-context) |
| status | draft |
| Kapsam | Envanter tamamlanacak — K001 Q4 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanaım-type-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `donanaım-type-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
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

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
