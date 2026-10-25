---
type: roadmap
category: amplifier-architecture
title: "CoreMusic Electronics — Amfi/PA Gelecek Yol Haritası & Yenilikler"
date: 2026-10-25
updated: 2026-10-25
status: active
version: 1.0.0
authority: "Derived — product-line-requirements.md v1.0.0 · ADR-111 draft · VISION §6/§11/§16 · PROJECTS §7/§9"
governance: Red Team · Human Mode · Truth Mode
---

# Amfi/PA Gelecek Yol Haritası & Yenilikler

**Zorunlu Bağlantılar:** [[product-line-requirements]] · [[coding-panel]] · [[power-governor-screen-spec]] · [[index]] · [[../VISION]] · [[../PROJECTS]] · [[../.decisions/index]]

**Etiketler:** `PLANNED` = tasarım/karar yok, yalnız yön · `⚠️ VERIFICATION REQUIRED` = hesap/ölçüm/pazar verisi bekliyor · `KULLANICI KARARI` = bağlayıcı. Bu dosya gelecek **niyetini** taşır; bağlayıcı karar ADR-111'dir.

---

## §1 Bu dosya neden var

`product-line-requirements.md` bugünü (9 SKU), ADR-111 kararı bağlar; bu dosya **gelecek genişlemeleri ve yenilik fikirlerini** tek yerde tutar ki kodlama paneli (T-11..T-15) ve PRD hattı büyüdükçe kaybolmasın. Kaynak: VISION §11 fazları + §16.2 gelecek tahminleri + PROJECTS §9 fazlama + amplifier/index §4.

## §2 Gelecek Ürün Hattı Genişlemeleri (sıra → dayanak)

| # | Genişleme | Ne | Dayanak | Bağlı görev |
|---|---|---|---|---|
| G1 | **8+1 topoloji kararı** | Class D mi Class AB mi (500 W/kanal) | amplifier/index §4 verim/ısı · **P0** | T-07 |
| G2 | **Aktif hoparlör SKU'su** | Amfi + DSP + koruma tek kutuda; Power Governor yerleşik, ayrı amfi gerekmez | VISION §10.2 donanım geliri hattı · PROJECTS §8 bağımlılık haritası | T-11 |
| G3 | **Zone güç bütçesi (ağ amfisi)** | Bölge bazlı watt tavanı — multi-room senkronuyla uyumlu | PROJECTS §7.1.4 NevaConnect (PLANNED, <5ms, 16 oda) | T-12 |
| G4 | **PA saha zinciri** | Açık alan presetleri (Düğün Salonu / Konser-Alani-Arena / Canli Stadyo / Kulup) + saha besleme (18650+adaptör) | NevaEngine 06/07 dokümanları · VISION §6.7 (PA) | T-14 |
| G5 | **64-bit float çekirdek** | Çift duyarlılıklı DSP çekirdek AR-GE | PROJECTS §2.3 yol haritası (PLANNED) | ADR-111 sonrası ayrı ADR |
| G6 | **Atmos genişlemesi** | 8.1 → nesnel ses geçişi | VISION §16.2 (2027-2030 tahmini) | PLANNED — kapsam dışı şimdilik |

## §3 Yenilik Kartları (geleceğe dönük — hepsi PLANNED)

| # | Yenilik | Kazanç | Bağımlılık |
|---|---|---|---|
| Y1 | **Aktif hoparlör** (amfi+DSP+koruma bütünleşik) | Ürün sayısı azalır, marj artar, saha kurulumu basitleşir | G1 topoloji + `pro` panel |
| Y2 | **Zone watt tavanı** | Oda/bölge başına ayrı güç bütçesi → tek ağ amfisi çok odayı besler | NevaConnect + Governor API (T-04) |
| Y3 | **Speaker fingerprint (otomatik keşif)** | Empedans+profil ölçümüyle hoparlör otomatik tanınır → hatalı kilidi imkânsız | Governor otomatik mod + ADC/ölçüm (`hardware/snr-thd-measurement`) |
| Y4 | **18650 SoH / şarj IQ** | Kullanım deseninden pil sağlığı (SoH) + otomatik bypass önerisi; telemetri `pro` panelde | 18650 politikası (product-line §4) + Power Governor audit |
| Y5 | **Üretim test hattı** | AES17 / THD+N / SNR otomatik fixture → "üretim kalitesi" hedefi ölçümle kanıtlanır | hardware-electronics §14 kapıları (3 koşul, ihlal 0) |
| Y6 | **Class D verimlilik serisi** | 500 W/kanalda ~%85-92 verim vs Class AB ~%45-55 → ısısı az, PSU küçük, ucuz satış hedefiyle uyumlu | amplifier/index §4 · ADR-111 P0 sonucu |

## §4 Zaman Sırası (bağlantılı fazlar)

| Pencere | Faz kaynağı | Bu dosyadan çıkan iş |
|---|---|---|
| Hemen (Faz 1 içinde) | — | T-07 P0 (G1) · T-03/T-04/T-05 kod paketi (T-10) · `pro` PRD satırı (T-08) |
| Faz 2 (Ay 13-24) | PROJECTS §9 | Class AB prototip (Ay 14-16) · 8.1 (Ay 14) · DSP Engine v1 (Ay 20) → **G1 sonucu ile Class D serisi buraya girer** |
| Faz 3 (Ay 25-36) | PROJECTS §9 | OEM otomotiv + global donanım satışı → **G2/G3 (aktif hoparlör + zone) ürün hattı olgunlaşır** |
| 2027-2030 | VISION §16.2 | G5 (64-bit) · G6 (Atmos) · AI müzik asistanı entegrasyyonu |

## §5 Riskler / Açıklar

| # | Madde | Etiket |
|---|---|---|
| 1 | Pazar/fiyat verisi yok (PA segmenti, aktif hoparlör fiyatı) | ⚠️ VERIFICATION REQUIRED |
| 2 | 2+1 SKU okuması belirsiz | KULLANICI NETLEŞTİRİLECEK |
| 3 | `pro.coremusic.net` PRD satırı yok (T-08) → G2/G3 panel kapsamı belgesiz | AÇIK |
| 4 | NevaConnect PLANNED (0 kod) → G3 bağımlılığı | PLANNED |
| 5 | 18650 MPN/akım/SoH hesabı yok (Y4) | ⚠️ VERIFICATION REQUIRED |

## §6 Değişiklik Günlüğü

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-25 | 1.0.0 | İlk kayıt — 6 genişleme (G1-G6) + 6 yenilik (Y1-Y6) + faz bağlantısı (oturum: gelecek düşünme turu) |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-25
**Mode:** Red Team · Human Mode · Truth Mode