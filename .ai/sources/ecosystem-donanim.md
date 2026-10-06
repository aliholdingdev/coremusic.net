---
title: "Kaynak: Donanım / Devre Referansları"
type: kaynak
raw_path: raw/ecosystem/
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-donanim]
tags: [ekosistem, donanım, devre, dac, amplifikator, guk, bom, adr-089]
---

# Kaynak Özeti — Donanım / Devre Referansları Ekosistemi

## Genel Özet

Tek ham dosya: `donanım-devre-referanslari.md` (seri 4/6, 540 satır, exa 2026-09-24) — TPA3255, ddabidov Headphone-DAC-AMP (XMOS XU316 + ESS ES9039), BoostCore MT3608, modular-amplituner, PBA MK1, DA15, spin-dac, OpAmp-Headphone ve rp2040-dac-amp referanslarını K1/K16/K17/K19/K20 (+K18 termal) katmanlarına yerleştirir; ADR-089 (Class AB amplifikatör) kararını bağlar ve PCM5122 yasağını her bölümde tekrarlar. Dosya adı Türkçe karakterli gelir, yeniden adlandırılmaz (slug ASCII `ecosystem-donanim`).

## Ana Fikirler

1. **Dosyanın başındaki DÜZELTME bloğu (2026-09-24, bağlayıcı kaynak `.ai/CLAUDE.md`):** DAC dizisi (PCM3168A + AK4458) **K17 → K1** · güç amplifikatörü/Class AB×8 → **K16** · boost/güç kaynağı ±35V (LM5122) **K19 → K17** · PCB tasarımı **K1 → K19** · BOM & üretim **K20 ✓** · termal **(eksik) → K18**.
2. **⚠ Belge içi çelişki (DÜZELTME ile gövde):** düzeltme bloğu "belge genelinde bağlayıcı (§1, §3.1, §3.2 dahil)" der; ancak gövde eski eşlemeyi sürdürür — §2.1 "DAC dizisi → K17", §2.1/§3.3/§3.6.2/§3.7.3 "MT3608 boost → K19", §3.7.4 başlığı "K17 — DAC Ayrışım Dersi" → `⚠ VERIFICATION REQUIRED`. `index.md` §3.4 ise düzeltme bloğuyla tutarlıdır (K17 = güç kaynağı, K19 = PCB).
3. **TPA3255 (K16):** TI 315W/315W/160W Class-D, filter-less full-bridge; **ADR-089 ile bu topoloji REDDEDİLMİŞTİR** (Class DC / Class D ret) — dosyada yalnızca topoloji dersi olarak kalır, K16'ya entegre edilmez.
4. **ddabidov Headphone-DAC-AMP (K1):** XU316 USB→I2S köprüsü CoreMusic'inki ile **birebir aynı çip** → firmware/sözleşme şekli (USB descriptor, I2S master, saat ağacı) referans alınabilir; DAC çipi farkı (ES9039 vs mevcut PCM3168A + AK4458) ayrı bir DAC geçiş ADR'si gerektirir (§5.3 şablonu: (a) ES9039'e geç · (b) mevcut çipleri koru · (c) hibrit).
5. **ADR-089 kapsamı:** Class AB (Darlington MJL21194/MJL21193) · 12-24V DC giriş → ±35V boost (LM5122 ×2, %96) · 6S LiPo 22.2V / 19-24V adaptör, DC-only · 1/2/4/6/8 modüler kanal (her kanal bağımsız PCB, enable pinli) · 120dB+ gürültü hedefi (vault ölçüm referansı: SNR >105dB / THD+N <0.005%) · hibrit MCU (XMOS XU316 + STM32H7·RP2040 + RPi5).
6. **Güç kaynağı exa sonuçları (19 aramanın 3'ü):** hifisonix Ripple Eater (±20-63V, 5A/20A, 40dB @20Hz-300kHz / 50dB @200Hz-20kHz) · prydin lateral MOSFET Class-AB (2SK1058/2SJ162, ±30V) · nathanpc mini12 (12V, TDA2030).
7. **rp2040-dac-amp = olumsuz referans:** tek çip USB+MCU+basit DAC, K17 çözünürlük hedefine yetersiz → "neden kullanılmıyor" red gerekçesi olarak §5.3 ADR'sinde listelenir; §4.4 tablosunda K17 eşleşmesi ❌ RED.
8. **MT3608 boost topolojisi (güç katmanı):** indüktör + diyot + feedback dirençleri; ham metin "(2V+) giriş … (kadar 28V) çıkış" der (kelime sırası bozuk) → değerler CoreMusic güç bütçesine göre yeniden hesaplanır, kopya yok.
9. **Lisans durumu:** ddabidov, BoostCore, modular-amplituner/PBA MK1/DA15, OpAmp-Headphone, spin-dac/rp2040-dac-amp → hepsi `⚠ VERIFICATION REQUIRED` (exa proje varlığını doğruladı, LICENSE gelmedi; Guardrail #14: LICENSE okunmadan kopya yok). TPA3255 = TI veri sayfası (kamuya açık referans şema). ❌ YASAK: PCM5122 · AGPL/uyumsuz lisanslı şema dosyası.

## Önemli Alıntılar/Veriler

- Kapsanan K katmanları (dosya başlığı): K1 donanım · K16 Class AB güç amplifikatörü · K17 güç kaynağı ±35V boost · K18 termal · K19 PCB · K20 BOM & üretim.
- §3.6.2 ders→K matrisi 9 satır: XU316 köprü sözleşmesi (K1) · ES9039 benchmark (K17) · Class-D filter-less (K16) · boost topolojisi (K19 — düzeltme bloğuna göre K17, ⚠) · modüler amfi kanal tekrarı (K16/K20) · Op-amp çıkış stage (K20) · prototip döngüsü (PBA MK1, DA15) · tek-çip red gerekçesi (K1/K17) · DAC yerleşim dersi (spin-dac).
- §3.6.5 entegrasyon öncelikleri: Yüksek = XU316 köprü, ES9039 benchmark, TPA3255 topoloji, OpAmp-Headphone çıkış stage · Orta = MT3608, modüler amfi · Düşük = spin-dac, rp2040 red referansı.
- §3.11 ADR-089 referansı: `[[.decisions/accepted/ADR-089-classab-24v]]` — accepted, 2026-09-24.
- §5.3 DAC ADR şablonu ve §5.1 güç ağacı şema dili "referans — kopya değil"; sonuc `.ai/DECISIONS.md`'ye yazılacak (bu ingest'te yazılmadı).
- §3.8 açık sorular (5): TPA3255 hibrit mi alternatif mi · MT3608 lisansı · ES9039 pin/sinyal haritası kapsamı · rp2040 red kapsamı · K19 rail değerleri (güç bütçesi henüz yazılmamış).
- Devirler: DSP/algoritma → `ses-dsp-acik-kaynak.md` (K3) · sürücü → `asio-wasapi-rehber.md` (K2/K0) · güç telemetri yazılımı → `ekosistem-mimarileri.md` (K14) · codec → `muzik-streaming-sunuculari.md` (K9/K15).
- Yıldız/lisans bilgisi gelmeyen hiçbir değer uydurulmadı; ham dosyada "→ doğrulandı" yazan tek doğrulama exa 2026-09-24'tür.

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[ecosystem-donanim]] (yeni)
