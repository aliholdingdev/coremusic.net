---
title: "K000-isletim-sistemi-turu/index"
type: architecture
category: layer-detail
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# K000 · İşletim Sistemi Türü — Dizin İskeleti (14 dosya)

> **Klasör dizini + manifest.** Üst katman: `[[../index]]` (K000 kök) · Makro: `[[../os-master]]` ·
> Bağlantı matrisi (kanonik): `[[../../katman-baglilik-matrisi]]` ·
> Şablon (Guardrail #16): `.ai/.templates/documentation/mimari-detay-template.md` §7.6 (K000 yerel istisna — turu = 14 dosya).
> Bu dosya sıfırdan yazılmıştır (2026-10-10); tüm sayılar yazım günü disk ölçümüdür.

## §1 Kimlik

- **Klasör:** `isletim-sistemi-turu/` · **Üst katman:** K000 — `[[../index]]` §2-8.
- **Sorumluluk:** OS türü bazında **platform notları** — her platform dosyası o platformun
  CoreMusic açısından durumunu (destek hedefi mi, runtime notu mu) sahiplenir.
- **Konum:** K000'ın en dip zemin dokümanıdır; bu klasörde **kod yoktur**, yalnız vault notu vardır.

## §2 Sorumluluk

1. **8 platform dosyası** — Windows · Linux · macOS · iOS · Android (`andorid.md`) · Tizen ·
   Raspberry Pi (`raspberry-pi-6.md`) · Web — her biri kendi platformunun ses yolu + durumunu anlatır.
2. **Karşılaştırma** — `[[platform-karsilastirma]]` 8 platformu tek tabloda yığın (tier) ·
   ses yolu · ADR kapsamı · durum ekseninde karşılaştırır.
3. **4 planlanan dal** — `[[ses-yollari]]` · `[[donanim-uyumluluk]]` ·
   `[[oturum-ses-yollari]]` · `[[kurulum-ortamlari]]` — `status: planned` stub (şablon §7.4).
4. **Kapsam dışı:** platform başına kurulum betiği, sürücü kodu, ölçülmüş gecikme değeri —
   bunlar bu klasörde **yazılmaz** (eşik/sayı uydurma yasağı).

## §3 Bağlantılar (Dependency Rule)

- **Alt katman (çağıran):** **YOK** — bu klasör en dip zemin dokümanıdır, hiçbir katmanı çağırmaz.
- **Üst katman (çağıran):** K000 kök dizini (`[[../index]]` §2-8 bu klasörü sahiplenir) ·
  K001 — `../../K001-donanim/donanım-kernel/index.md:37` windows/linux dosyalarına link verir (kanıt (ii)).
- **Kenarlar (yalnız matris §2'den — ikinci matris üretilmez):**
  `K001 → K000` (sayısal) · `K005 → K000` · `K006 → K000` · `K007 → K000` · `K012 → K000` · `K013 → K000`.
- **Yasak:** bu klasörde bir üst katman (K001-K020) içeriği **üretilmez**; platform notu
  donanım/ürün kararının yerine geçmez. İhlal → revert + log ERROR (matris §3).

## §4 Alt Klasör Dizini (manifest — 14 dosya, 2026-10-10 glob)

| # | Dosya | Tür | Band | Sorumluluk (1 satır) |
|---|---|---|---|---|
| — | `index.md` | dizin | 90-140 | Bu dosya — klasör haritası + manifest |
| 1 | `windows.md` | içerik | 80-140 | Windows: WASAPI/ASIO zemini + Tier 1 durumu |
| 2 | `linux.md` | içerik | 80-140 | Linux: ALSA/PipeWire zemini + Tier 2 durumu |
| 3 | `macos.md` | içerik | 80-140 | macOS: CoreAudio/HAL zemini + Tier 3 durumu |
| 4 | `ios.md` | içerik | 80-140 | iOS: Core Audio kısıtları + native karar durumu |
| 5 | `andorid.md`* | içerik | 80-140 | Android: AAudio/Oboe notu + native karar durumu |
| 6 | `tizen.md` | içerik | 80-140 | Tizen: TV web hedefi + native karar durumu |
| 7 | `raspberry-pi-6.md`** | içerik | 80-140 | Raspberry Pi: Tier 4 / EMBEDDED hedefi |
| 8 | `web.md` | içerik | 80-140 | Web: tarayıcı ses yolu — tek IMPLEMENTED yüzey |
| 9 | `platform-karsilastirma.md` | içerik | 80-140 | 8 platformun karşılaştırmalı özeti |
| 10 | `ses-yollari.md` | stub | 30-50 | PLANNED — platform ses yolu özeti |
| 11 | `donanim-uyumluluk.md` | stub | 30-50 | PLANNED — donanım uyumluluk matrisi |
| 12 | `oturum-ses-yollari.md` | stub | 30-50 | PLANNED — oturum + ses yolu kesişimi |
| 13 | `kurulum-ortamlari.md` | stub | 30-50 | PLANNED — kurulum/ortam notları |
| | **Toplam** | | | **14 dosya** — manifest dışı üretim **0** |

\* `andorid.md` yazım hatası **bilinçli korunur** (ad DEĞİŞMEZ — şablon §7.6; rapor edilir, düzeltilmez).
\*\* `raspberry-pi-6.md` adı "Pi 6" ima eder; içerik **mevcut Pi 5 + Tier 4** içindir (ad yasağı —
şablonda `rasberry-pi-6` → `raspberry-pi-6` dönüşümü ilanlı; dosya adı değiştirilmez, uyuşmazlık rapor edilir).

## §5 Okuma Sırası

```text
[[../index]] (K000 kök) → index (bu dosya) → platform-karsilastirma (genel bakış)
→ tercih edilen platform (windows/linux/macos/web) → diğer platformlar → 4 stub (yalnız ihtiyaç anında)
```

Her dosyada önce §1 Kimlik, sonra §2-§8; stub dosyalar en son.

## §6 ADR Bağlantıları

Başlık/durum SSOT'u bu dosya değildir — güncel okuma: `[[../isletim-sistemi-plan/adr]]`
(ADR dosyalarının frontmatter'ından). Bu klasörün bağlandığı ADR'lar (dosya varlıkları
`.ai/.decisions/` glob ile doğrulanmıştır — 2026-10-10):

| ADR | Konu | Dosya (disk kanıtı) | Kapsam |
|---|---|---|---|
| ADR-019 | Per-OS Neva Player (IAudioBackend) | `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` — frontmatter `status: accepted` (satır 8) | Windows · macOS · Linux (3 masaüstü — ADR-019 §1) |
| ADR-006 | Performance Targets (`<10ms ASIO / <20ms WASAPI`) | `.ai/.decisions/accepted/ADR-006-performance-targets.md` (dosya VAR) | Windows ölçüm kapısı |
| ADR-017 | DSP Hardware Mode (hard-RT kısıtları) | `.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md` (dosya VAR) | callback yasağı — tüm native platformlar |
| ADR-004 | Multi-domain SPA | `.ai/.decisions/accepted/ADR-004-multi-domain-spa.md` (dosya VAR) | web + mobil web yüzeyi |

> ⚠️ ADR durum metinleri bu turda tek tek okunmadı → klasör geneli **⚠️ VERIFICATION REQUIRED**;
> kanıt `[[../isletim-sistemi-plan/adr]]` dosyasındadır.

## §7 Risk / Not

1. **`andorid.md` yazım hatası** bilinçli korunur — ad değişimi yasak (şablon §7.6 dipnotu;
   `../isletim-sistemi-plan/bilinmeyenler` B10/B16 onay bekliyor).
2. **`raspberry-pi-6.md` ad-içerik uyuşmazlığı** — dosya "Pi 6" der, içerik Pi 5'tir
   (onaysız rename yasak; rapor edilir).
3. **ADR-019 kapsamı 3 masaüstadır** (Windows/macOS/Linux — ADR-019 §1); iOS · Android ·
   Tizen · Raspberry Pi **bu kapsamın dışındadır** → bu 4 platformda native karar `UNKNOWN`tır.
4. **Üst dizin çelişkisi:** `00-master-index.md:44` "turu/ 8 md" yazar — bu dosyanın yazımıyla (14)
   çelişir → `⚠️ VERIFICATION REQUIRED`, düzeltme üst dizin sahibine aittir.
5. **Tier 5 ReactOS** (`.ai/CLAUDE.md:358`) bu klasörde dosyası olmayan tek yığıttır → eksiklik
   rapor edilir, dosya üretilmez (manifest dışı üretim yasak).

## §8 Bilinmeyenler (özet)

Tam envanter: `[[../isletim-sistemi-plan/bilinmeyenler]]`. Klasör bazlı:

- **B-T1** iOS/Android/Tizen native hedefi → vault'ta ADR **YOK** → `UNKNOWN` (karar kapısı: architect).
- **B-T2** `IAudioBackend` kodda 0 → platform adapter PLANNED (`../isletim-sistemi-plan/senaryolar.md:73`).
- **B-T3** platform başına ölçülmüş round-trip gecikme → ölçüm **YOK** → hiçbir platform dosyasında sayı yok.
- **B-T4** `isletim-sistemi-api/kapsam.md:38` "klasörün dizini `index.md` YOK" → bu dosya ile
  **kapanmıştır** (eski kayıt — üst dosya sahibine rapor).
- **B-T5** `isletim-sistemi-cross/bilinmeyenler.md:86` (B21: "14 ↔ glob 8") → bu dosya ile
  **kapanmıştır** (glob artık 14).

## §9 Manifest

Bu dosyanın üretim kümesi §4 tablosudur (**14 dosya** — şablon §7.6 K000 yerel istisnası);
manifest dışı üretim **0** · kaynak kod (`*.php *.js *.css *.sql`) bu görevde **yazılmaz** ·
dosya silme **yok** · `git commit` **atılmaz** (orkestratöre aittir).

---

**İlişki:** üst `[[../index]]` · makro `[[../os-master]]` · matris `[[../../katman-baglilik-matrisi]]` ·
karşılaştırma `[[platform-karsilastirma]]` · ADR oku `[[../isletim-sistemi-plan/adr]]`
