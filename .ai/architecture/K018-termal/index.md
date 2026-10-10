---
type: architecture
category: layer
title: "K018 — Termal"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K018 — Termal

## §1 Kimlik
- Katman: K018 · Alan: **A5** (K16-K20).
- Kapsam: termal tasarım — ısı dağıtımı, sınırlar, soğutma.

## §2 Sorumluluk
1. Amplifikatör (K016) ve güç kaynağı (K017) için ısı hesabı kapsamı.
2. Sınırların tanımlanması (kasa/radyatör) — şartname düzeyi.
3. Termal koruma eşiği tanımı (donanım katmanları ile).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 zemini (K000–K005) — sayısal kural (plan §2-1).
- **Üst (çağıran):** K016 · K017 (ısı kaynağı bileşenler) · K020.
- A5 içi kenarlar plan §3'te yok → **UNKNOWN**.

## §4 ADR Bağlantıları
Termal başlıklı ADR: **YOK** (`.ai/.decisions/index.md` §3-§4 taramasında termal başlık bulunmadı) → **UNKNOWN**; ADR-089 içeriğinin termal kısıt içerip içermediği okunmadı.

## §5 Durum
**PLANNED** — termal hesap/ölçüm kanıtı diskte yok.

## §6 Risk / Not
- Sıcaklık/ölçüm verisi olmadan SKU (ADR-090) doğrulanamaz; termal ADR gereksinimi Vault Steward'a açık.

---

## §7 Makro Katman Karşılığı (K0–K4)

| Bu K | §6 durumu | Makro eşleme | Makro kimliği |
|---|---|---|---|
| K018 | PLANNED | **Yok** — A5 (K016–K020) makro K0–K4'e girmez | A5 (AGENTS §5) · `[[../katmanli-mimari-k0-k4/index]]` §6.1 |

- Eşleme: `[[../00-master-index]]` §8 · kenarlar: `[[../katman-baglilik-matrisi]]` §4 · makro tanımı/istisna defteri: `[[../katmanli-mimari-k0-k4/index]]` §1.1 · §6.1.
- K018, A5 grubunun **termal koludur**; K000–K005 zeminine aralık bağımlılığıyla bağlıdır (matris §2) ve makro hiyerarşisine düğüm olarak girmez.

## §8 Arayüz (Girdi/Çıktı & API)

| Kenar | Yön | Girdi → Çıktı | Kanıt |
|---|---|---|---|
| K016 → K018 | ısı kaynağı | **41W/kanal** ısı yükü → termal tasarım girdisi | ADR-089 §3.3.3-5 |
| K017 → K018 | ısı kaynağı | boost konvertör kaybı → termal yük | K018 §3 |
| K018 → K016/K017 | koruma | termal koruma eşiği → kesme/enable | K018 §2-3 |
| K018 → K020 | üretim | soğutma/ekipman listesi → üretim planı | K020 §2-3 |
| MCU → fan | kontrol | STM32/RP2040: fan/termal telemetri | ADR-089 §2.1 tablosu |

- Eşik değeri, fan eğrisi ve koruma mantığı **dokümante edilmedi** → **UNKNOWN**.

## §9 Hata Modları ve Güvenlik Sınırı

| Hata modu | Sonuç | Belirlenmiş kontrol | Kanıt durumu |
|---|---|---|---|
| Aşırı sıcaklık (çıkış stage) | hasar / performans düşüşü | KSD301 **72°C** termal kesme | ADR-089 §3.3.3-5 (kabul kriteri) — eşik **ölçülmedi** |
| Soğutma yetersiz | sürekli çalışma güvensiz | 80mm PWM fan (hava akışı) | ADR-089 §3.3.3-5 — akış ölçümü **yok** |
| Koruma eşiği tanımsız | OTP devrede çalışmaz | **Kontrol yok** — termal başlıklı ADR YOK (§4) | §4 · bu görevde teyit edildi (aşağıda §10/2) |
| Ölçüm eksikliği | SKU doğrulanamaz | **Kontrol yok** | §6 |

**Güvenlik sınırı:** termal güvenlik **donanım kesmesiyle** (KSD301) sağlanır; yazılım güvenlik katmanı (K006) bu kapıda geçerli değildir. Kesme ↔ fan koordinasyonu MCU'dadır (ADR-089 §2.1) — uygulaması **yok** (PLANNED).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

1. **UNKNOWN** — termal başlıklı ADR **YOK** (§4). Bu görevde teyit: `.ai/.decisions/**/*.md` içinde `title:` satırında "termal"/"thermal" geçen dosya **0 isabet** (grep, 2026-10-10).
2. **§4 açık sorusuna yeni kanıt (§4 SATIRI DEĞİŞTİRİLMEDİ):** ADR-089 **termal kısıt içerir** — §3.3.3-5: *"Termal: 41W/kanal → KSD301 72°C kesme + 80mm PWM fan ile sürekli çalışma güvenli"* (okundu 2026-10-10). Bu, §4'teki *"ADR-089 içeriğinin termal kısıt içerip içermediği okunmadı"* tespitini **bu §10'da kayda geçirir**; §4 satırı append-only kuralıyla korunmuştur.
3. **UNKNOWN** — sıcaklık/ölçüm verisi yok → ADR-090 SKU matrisi termal olarak doğrulanamaz (§6).
4. **UNKNOWN** — kasa/radyatör sınırları ve hava akışı şartı **ölçülmemiştir**; hesap dosyası diskte yok (§5).
5. **⚠️ VERIFICATION REQUIRED** — ADR-089 §1.1 L34/L39 alıntıları `[[../../CLAUDE.md]]` L118 ve `PROJECTS.md` L305-311'dir; **bu ikincil dosyalar bu görevde okunmadı** → "Fischer SK53-100-SA · Noctua NF-A8 · 41W/kanal" değerleri doğrulanmadı.
