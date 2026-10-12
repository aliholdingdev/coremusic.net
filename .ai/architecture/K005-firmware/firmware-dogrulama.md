---
title: "K005-firmware — Doğrulama Protokolü"
type: architecture-verification
node_id: K005-firmware
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# firmware-dogrulama — K005 Doğrulama Protokolü

## §1 Web-kanıt kayıtları (tur-1)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Steinberg ASIO SDK açık kaynak ilanı (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı |
| 2 | ASIO SDK lisansının firmware entegrasyonuna etkisi | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 3 | TPA3255 PVDD 18 V–53.5 V (TI datasheet) | https://www.ti.com/lit/gpn/TPA3255 | 2026-10-12 | ✅ doğrulandı |
| 4 | TPA3255 PVDD 24–52 V (görev manifesti iddiası) | görev manifesti | 2026-10-12 | ⚠️ 3 ile uyuşmuyor |
| 5 | TPA3255 PBTL KiCad referans projesi (şematik + PCB) | https://github.com/EliMattingly22/TPA3255_ClassD_PBTL | 2026-10-12 | ✅ doğrulandı |
| 6 | TPA3255 Class-D / PBTL sınıf tanımı | https://www.ti.com/lit/gpn/TPA3255 | 2026-10-12 | ✅ doğrulandı |
| 7 | XMOS XU316 · I2S · TDM hedef yığını | .ai/AGENTS.md §4 (agent 10) | 2026-10-12 | ✅ dosya okundu (hedef) |
| 8 | XMOS araç zinciri/SDK sürümü | — | 2026-10-12 | **UNKNOWN** |
| 9 | Güvenlik hedefi: Argon2id · AES-256-GCM | .ai/AGENTS.md §5 | 2026-10-12 | ✅ dosya okundu (hedef) |
| 10 | ASIO→WASAPI fallback (host tarafı, K005 dışı) | .ai/AGENTS.md §17 #6 | 2026-10-12 | ✅ dosya okundu |

> **Yasak:** 2, 4 ve 8 için değer **uydurulmaz**. 3 ve 4 birlikte yazılır —
> uyuşmazlık tek taraflı düzeltmez, iki taraflı kaydedilir (§5 kural 5).

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Kanıt şekli | Şu an |
|---|---|---|---|
| D1 | v2 §2.5 mini-ağaç birebirliği (9 çocuk) | karşılaştırma kaydı | ✅ 2026-10-12 |
| D2 | 9 çocuk klasöründe index.md (8 zorunlu alan) | disk sayımı | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama üçlüsü | disk sayımı | ✅ 2026-10-12 |
| D4 | §1'de açık ⚠️ / UNKNOWN satırı kalmamış | kanıt turu | ❌ 3 açık (2, 4, 8) |
| D5 | Açık sorular kapanmış (§3) | üst karar | ❌ 5 açık |
| D6 | [TASARIM] satırlarından en az biri [KANIT] olmuş | dosya/URL | ❌ 0 |
| D7 | status: verified (index.md) | D1–D7 ✅ | ❌ bekliyor |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified. Kısmi geçiş yoktur.

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | ASIO SDK GPLv3 firmware/SDK entegrasyonunu nasıl etkiler? | ⚠️ VERIFY REQUIRED | legal-advisor + embedded |
| Q2 | TPA3255 PVDD: 24–52 V (manifest) ↔ 18–53.5 V (TI) — hangisi? | ⚠️ VERIFY REQUIRED | audio-hw |
| Q3 | boot ↔ runtime ↔ device-control sınırı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | update ↔ security (imzalı güncelleme) kime ait? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | Donanım hedefi (XMOS XU316) + araç zinciri kesin mi? | ⚠️ VERIFY REQUIRED | embedded + dsp-fw |

## §4 Adım adım doğrulama sırası

| Adım | İş | Sorumlu | Bağımlılık |
|---|---|---|---|
| V1 | Disk sayımı: 4 MD + 9 çocuk index | bu ajan | — |
| V2 | v2 §2.5 ile birebir diff | bu ajan | V1 |
| V3 | URL erişilebilirlik tekrarı (§1: 1, 3, 5, 6) | researcher | — |
| V4 | ⚠️/UNKNOWN kanıt turu-2 (§1: 2, 4, 8) | researcher | V3 |
| V5 | Q1–Q5 kararları | Vault Steward + ilgili uzman | V4 |
| V6 | [TASARIM] → [KANIT] geçişi | bu ajan | V4 |
| V7 | status: verified güncellemesi | Vault Steward | V1–V6 ✅ |

## §5 Kanıt bütünlüğü kuralları

1. URL'siz veya dosya yolsuz iddia yazılmaz → **UNKNOWN**.
2. Datasheet varlığı ≠ sistem kararına uygunluk (etki ayrı satırdır).
3. Sayısal iddialar (V, sürüm) erişim tarihiyle ve kaynağıyla yazılır.
4. v2 yazım hataları düzeltilmez — v2 §3 KURAL 1.
5. Uyuşmazlık tek taraflı düzeltilmez, **iki taraflı kaydedilir** (§1/3 ↔ §1/4).
6. 'Hedef yığın' ile 'kurulu/kanıtlanmış sürüm' ayrımı korunur (§1/7, §1/9).

## §6 Kanıt turu planı (tur-2 ne zaman açılır)

| Tur | Kapsam | Tetikleyici | Beklenen çıktı |
|---|---|---|---|
| tur-1 | §1 satır 1–10 | bu dosyanın yazımı (2026-10-12) | tamamlandı — 5 ✅ + 2 ⚠️ + 1 UNKNOWN + 2 ✅ iç |
| tur-2 | §1 satır 2 | hukuki lisans değerlendirmesi | Q1 kapanış kaydı |
| tur-3 | §1 satır 4 | audio-hw PVDD kararı | Q2 kapanış kaydı — 3/4 satırları korunur |
| tur-4 | §1 satır 8 | XMOS araç zinciri kanıtı | sürüm + araç adları |
| tur-5 | D6 | ilk [KANIT] bağımlılığı | bagimliliklar §2/§5 etiket güncellemesi |

**Tazelik kuralı:** URL kanıtları 90 günden eskiyse erişim tarihiyla yeniden denenir;
erişilemezse ✅ → ⚠️ VERIFICATION REQUIRED olarak geri alınır ve §6'ya satır eklenir.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
