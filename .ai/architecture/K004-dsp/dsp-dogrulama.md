---
title: "K004-dsp — Doğrulama Protokolü"
type: architecture-verification
node_id: K004-dsp
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# dsp-dogrulama — K004 Doğrulama Protokolü

## §1 Web-kanıt kayıtları (tur-1)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Patina — C++17 analog modeling deposu mevcut | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı |
| 2 | Patina lisansı | repo LICENSE okunmadı | 2026-10-12 | **UNKNOWN** |
| 3 | Signalsmith basics deposu (116★) | https://github.com/Signalsmith-Audio/basics | 2026-10-12 | ✅ doğrulandı |
| 4 | Signalsmith basics MIT lisans | LICENSE teyidi yapılmadı | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 5 | Signalsmith dsp deposu (277★) | https://github.com/Signalsmith-Audio/dsp | 2026-10-12 | ✅ doğrulandı |
| 6 | DSPark v1 '90+' iddiası (manifest, birim belirtilmemiş) | görev manifesti | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 7 | DSPark GitHub ölçümü 2★ + eşleşen depo | depo URL'i bulunamadı | 2026-10-12 | ⚠️ VERIFY REQUIRED · URL **UNKNOWN** |
| 8 | Ölçüm eşikleri: SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | ~/.config/opencode/AGENTS.md §5 | 2026-10-12 | ✅ dosya okundu |
| 9 | Layer violation kuralı (katman ihlali → revert) | .ai/AGENTS.md §17 #7 · §18 #1 | 2026-10-12 | ✅ dosya okundu |
| 10 | DSP latency bütçesi (sayısal hedef) | — | 2026-10-12 | **UNKNOWN** |
| 11 | DSPark sürüm/sürüm geçmişi | — | 2026-10-12 | **UNKNOWN** |

> **Yasak:** 4, 6, 7, 10 ve 11 için değer **uydurulmaz**. 6 ve 7 birbirini
> tamamlar: iki ölçüm de yazılır, fark ⚠️ olarak işaretlenir, tek taraflı düzeltme yok.

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Kanıt şekli | Şu an |
|---|---|---|---|
| D1 | v2 §2.4 mini-ağaç birebirliği (12 çocuk) | karşılaştırma kaydı | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md (8 zorunlu alan) | disk sayımı | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama üçlüsü | disk sayımı | ✅ 2026-10-12 |
| D4 | §1'de açık ⚠️ / UNKNOWN satırı kalmamış | kanıt turu | ❌ 5 açık (4, 6, 7, 10, 11) + 1 UNKNOWN (2) |
| D5 | Açık sorular kapanmış (§3) | üst karar | ❌ 5 açık |
| D6 | [TASARIM] satırlarından en az biri [KANIT] olmuş | dosya/URL | ❌ 0 |
| D7 | status: verified (index.md) | D1–D7 ✅ | ❌ bekliyor |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified. Kısmi geçiş yoktur.

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | DSPark '90+' iddiası nedir, doğru depo hangisi? | ⚠️ VERIFY REQUIRED | researcher |
| Q2 | Signalsmith basics MIT lisans teyidi? | ⚠️ VERIFY REQUIRED | researcher |
| Q3 | dynamics ↔ compressor/limiter · pipeline sırası? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | measurement ↔ testing sorumluluk ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | DSP host'ta mı (K003 üstü) donanım içinde mi (K005)? | ⚠️ VERIFICATION REQUIRED | embedded + dsp-fw |

## §4 Adım adım doğrulama sırası

| Adım | İş | Sorumlu | Bağımlılık |
|---|---|---|---|
| V1 | Disk sayımı: 4 MD + 12 çocuk index | bu ajan | — |
| V2 | v2 §2.4 ile birebir diff | bu ajan | V1 |
| V3 | URL erişilebilirlik tekrarı (§1: 1, 3, 5) | researcher | — |
| V4 | ⚠️/UNKNOWN kanıt turu-2 (§1: 2, 4, 6, 7, 10, 11) | researcher | V3 |
| V5 | Q1–Q5 kararları | Vault Steward + ilgili uzman | V4 |
| V6 | [TASARIM] → [KANIT] geçişi | bu ajan | V4 |
| V7 | status: verified güncellemesi | Vault Steward | V1–V6 ✅ |

## §5 Kanıt bütünlüğü kuralları

1. URL'siz veya dosya yolsuz iddia yazılmaz → **UNKNOWN**.
2. Repo varlığı ≠ içerik doğruluğu (lisans/sürüm ayrı satırdır).
3. Sayısal iddialar (yıldız, ölçüm, birim) erişim tarihiyle ve **birimiyle** yazılır.
4. v2 yazım hataları düzeltilmez — v2 §3 KURAL 1.
5. Uyuşmazlık tek taraflı düzeltilmez, **iki taraflı kaydedilir** (§1/6 ↔ §1/7).
6. Birim belirsiz sayı ('90+') birime çevrilerek yorumlanmaz; olduğu gibi yazılır.

## §6 Kanıt turu planı (tur-2 ne zaman açılır)

| Tur | Kapsam | Tetikleyici | Beklenen çıktı |
|---|---|---|---|
| tur-1 | §1 satır 1–11 | bu dosyanın yazımı (2026-10-12) | tamamlandı — 3 ✅ + 3 ⚠️ + 4 UNKNOWN + 1 ✅ iç |
| tur-2 | §1 satır 2, 4 | LICENSE dosyaları okunduğunda | lisans kaydı (MIT / diğer) |
| tur-3 | §1 satır 6, 7, 11 | DSPark deposu bulunduğunda | kesin URL + sürüm + birim açıklaması |
| tur-4 | §1 satır 10 | latency bütçesi kararlaştırıldığında | sayısal hedef + kaynağı |
| tur-5 | D6 | ilk [KANIT] bağımlılığı | bagimliliklar §2/§5 etiket güncellemesi |

**Tazelik kuralı:** URL kanıtları 90 günden eskiyse erişim tarihiyle yeniden denenir;
erişilemezse ✅ → ⚠️ VERIFICATION REQUIRED olarak geri alınır ve §6'ya satır eklenir.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
