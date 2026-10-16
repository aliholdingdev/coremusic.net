---
node_id: K002-electroncis
node_type: domain
status: draft
parent_id: null
children:
  - devre-ve-analog
  - dijital-ve-mikroislemci
  - guc-yonetimi
  - sensor-ve-akteyici
  - imalat-ve-test
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - ".ai/architecture/index.md §8.1 (Band-1 | K000-K020 | 19 tekil domain — K002 GAP)"
  - "Kullanıcı onayı 2026-10-13 (K002-electroncis kurulumu)"
  - ".ai/electronic/** (60 dosya — ham içerik kaynağı, 3 ajan derinleştiriyor)"
validation: "draft — bu turda 26 dosya yazıldı (kök 6 + 5 alt-konu × 4); A1 tur-1"
open_questions:
  - "Q-K2-1: K002 ile K001-donanım sınırı — kart/PCB tasarımı kimde? (KAPANDI 2026-10-15: PCB tasarımı+üretimi K002; K001 = envanter + erişim/soyutlama — kanıt: donanım-anayasa.md L65·L305)"
  - "Q-K2-2: K002 ile K005-firmware sınırı — gömülü kod mu, donanım tasarımı mı?"
  - "Q-K2-3: K002 ile K004-dsp sınırı — DSP zinciri analog ön zincirin parçası mı?"
group: mimari
---

# K002-electroncis — Kademe-1 Domain İndeksi

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1 — `electronics` DEĞİL, düzeltilmez).
> Band-1 tekil domaini (K000–K020). ADR-098 kapısı AÇILMAZ (Band-2 = K021+).

## §1.4 İlişkili Proje Kaydı (2026-10-14)

**Neva Engine elektronik bağımlılıkları** — `.ai/projects/NevaEngine/`: DAC/ADC çipleri (PCM3168A, AK4458 — K001'de detaylı) bu projenin analog hedefidir; yazılım DSP zinciri (`.ai/projects/NevaEngine/02-dsp-zincir-mimari.md`) analog ön zincirin yerine geçmez/çakışmaz: K002 devre (analog), K004/Neva DSP (dijital). Sınır: Q-K2-3. **Q5 KAPANDI 2026-10-15 (ADR-017):** DSP zinciri host'ta (NevaEngine/JUCE), XMOS yalnız zamanlama — K002 analog dal olarak kalır (Q-K2-3 için kanıt).

**ELI15:** Elektronik mühendisliği alanının CoreMusic vault'undaki ASIL evi. Devreden üretime kadar tüm elektronik tasarım katmanlarını kapsar; ham içerik `.ai/electronic/**` (60 dosya) ve OS perspektifi `K000-isletim-sistemi/electroncis/` (2 alt-dal) buradan FARKLI rollerdir — tekrar değil, tamamlanmadır.

| # | Bilgi | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis` |
| 2 | Kademe | 1 — domain |
| 3 | parent_id | `null` (Band-1 tekil domain) |
| 4 | Alt-konu sayısı | 5 (Kademe-2 subdomain) |
| 5 | Dosya sayısı (bu tur) | 26 (kök 6 + 5×4) |
| 6 | Durum | `draft` (A1 tur-1) |
| 7 | Yazım | `electroncis` — dokunulmaz |

## §1 ELI15 — Bu domain neyi kapsar?

Elektronik, fiziksel dünyadaki sinyalin (akım/gerilim) hesaplanabilir, tasarlanabilir ve üretilebilir hali. Bu domain beş katmanı sırayla anlatır: **devre** (bileşenler ve topoloji) → **dijital** (mikroislemci ve saat) → **güç** (besleme ve koruma) →
**sensör/aktüatör** (dünya ile arayüz) → **imalat** (üretim ve test). Her katmanın ölçüm eşiği, komşu sınırı ve açık sorusu vault'ta kayıtlıdır.

## §2 Kademe-2 Alt-Konu Envanteri (5)

| # | Alt-konu | node_id | Kapsam özeti | group |
|---|---|---|---|---|
| 1 | Devre ve analog | `K002-electroncis/devre-ve-analog` | Pasif/aktif bileşenler, op-amp, osilatör, filtre, ADC/DAC ön zincir | mimari |
| 2 | Dijital ve mikroislemci | `K002-electroncis/dijital-ve-mikroislemci` | MCU/DSP mimarisi, GPIO/PLL, saat/kararlılık, bellek haritası, I2S/SPI/I2C | mimari |
| 3 | Güç yönetimi | `K002-electroncis/guc-yonetimi` | AC-DC, DC-DC topolojileri, LDO, termal, koruma | mimari |
| 4 | Sensör ve aktüatör | `K002-electroncis/sensor-ve-akteyici` | Sensör arayüzleri, kestirim, kalibrasyon, aktüatör sürücü, kapalı çevrim | mimari |
| 5 | İmalat ve test | `K002-electroncis/imalat-ve-test` | PCB üretimi, DFT, AOI/X-ray, kalibrasyon testi, EMC ön hazırlık | mimari |

## §3 §GRUPLAR

| # | Dosya grubu | Dosyalar | group |
|---|---|---|---|
| 1 | Kök kayıt | `index` · `electroncis-master` · `electroncis-kapsam` · `electroncis-bagimliliklar` | mimari |
| 2 | Kök kanıt | `electroncis-dogrulama` | kanit |
| 3 | Kök kod | `electroncis-ornekler` | kod |
| 4 | Alt-konu anayasa | 5 × `{alt}-anayasa` · 5 × `index` | mimari |
| 5 | Alt-konu kanıt/kod | 5 × `{alt}-ornekler` (kod) · 5 × `{alt}-dogrulama` (kanit) | kod · kanit |

## §4 Bağımlılıklar (dışa)

| # | Hedef | İlişki | Durum |
|---|---|---|---|
| 1 | `K001-donanım` | Fiziksel parça envanteri (disk, ses kartı, kablo) — kart/PCB sınırı **KAPANDI 2026-10-15: PCB K002'de** | ✅ |
| 2 | `K004-dsp` | DSP algoritma zinciri — analog ön zincir sonrası | açık |
| 3 | `K005-firmware` | Gömülü kod yüzeyi — donanım üstünde çalışan yazılım | açık |
| 4 | `.ai/electronic/**` | Ham içerik kaynağı (60 dosya) — 3 ajan derinleştiriyor | aktif |
| 5 | `K000-isletim-sistemi/electroncis` | OS perspektifinden elektronik sınırı (2 alt-dal) | aktif |

## §5 Komşu sınırı — detay: `electroncis-kapsam.md` §B (5 komşu: K000/electroncis · K001 · K004 · K005 · `.ai/electronic/**`)

## §6 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-K2-1 | K001 ile sınır: kart/PCB tasarımı K002'de mi, K001'de mi? | ✅ **KAPANDI 2026-10-15** — PCB tasarımı+üretimi K002; K001 = envanter + erişim/soyutlama API'si (kanıt: `donanım-anayasa.md` L65·L305 "Fiziksel PCB/BOM → HAYIR" · `devre-ve-analog-anayasa.md` L222-223) |
| Q-K2-2 | K005 ile sınır: firmware kodu K005'te, donanım şartnamesi K002'de mi? | ⚠️ VERIFICATION REQUIRED |
| Q-K2-3 | K004 ile sınır: DSP zinciri hangi dalın parçası? | ⚠️ VERIFICATION REQUIRED |
| Q-K2-4 | `.ai/electronic/**` 60 dosyanın hangi bölümü hangi alt-konuya taşınacak? (3 ajan bekleniyor) | UNKNOWN |
| Q-K2-5 | 8.1 surround topolojisi bu domainin hangi alt-konu sınırında? | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
