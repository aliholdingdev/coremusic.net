---
title: "K003-audio — Doğrulama Protokolü"
type: architecture-verification
node_id: K003-audio
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# audio-dogrulama — K003 Doğrulama Protokolü

## §1 Web-kanıt kayıtları (tur-1)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Steinberg ASIO SDK açık kaynak ilanı (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı |
| 2 | ASIO SDK lisans etkisi CoreMusic'e (derived work / yayım) | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 3 | JUCE deposu mevcut | https://github.com/juce-framework/JUCE | 2026-10-12 | ✅ doğrulandı |
| 4 | JUCE 8811★ (görev iddiası) | bağımsız sayım gerekir | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 5 | NAudio ASIO tartışma kategorisi mevcut | https://github.com/naudio/NAudio/discussions/categories/general | 2026-10-12 | ✅ doğrulandı |
| 6 | NAudio + ASIO tartışması (2026-09-28) kesin thread | aday: /discussions/1091 · /discussions/1250 · /issues/1249 · Docs/AsioPlayback.md | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 7 | WASAPI belgeleri (ana + exclusive mode) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi · .../exclusive-mode-streams | 2026-10-12 | ✅ doğrulandı |
| 8 | ASIO→WASAPI fallback kuralı | .ai/AGENTS.md §17 #6 | 2026-10-12 | ✅ dosya okundu |
| 9 | lock-free callback + ön-allocate buffer | ~/.config/opencode/AGENTS.md §5 | 2026-10-12 | ✅ dosya okundu |
| 10 | JUCE 9 / ASIO SDK 2.3.4 hedef sürümler | .ai/AGENTS.md §4 | 2026-10-12 | ✅ dosya okundu (hedef) |
| 11 | ASIO SDK 2.3.4 bağımsız web teyidi | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| 12 | NAudio sürüm/paket bilgisi | — | 2026-10-12 | **UNKNOWN** |

> **Yasak:** 2, 4, 6, 11 ve 12 için değer **uydurulmaz**; kanıt gelene kadar
> ⚠️ veya UNKNOWN olarak kalır. Sayısal iddialar (yıldız, sürüm) erişim tarihiyle yazılır.

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Kanıt şekli | Şu an |
|---|---|---|---|
| D1 | v2 §2.3 mini-ağaç birebirliği (12 çocuk) | karşılaştırma kaydı | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md (8 zorunlu alan) | disk sayımı | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama üçlüsü | disk sayımı | ✅ 2026-10-12 |
| D4 | §1'de açık ⚠️ / UNKNOWN satırı kalmamış | kanıt turu | ❌ 5 açık (2, 4, 6, 11, 12) |
| D5 | Açık sorular kapanmış (§3) | üst karar | ❌ 6 açık |
| D6 | [TASARIM] satırlarından en az biri [KANIT] olmuş | dosya/URL | ❌ 0 |
| D7 | status: verified (index.md) | D1–D7 ✅ | ❌ bekliyor |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified. Kısmi geçiş yoktur.

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | ASIO SDK GPLv3 lisansının CoreMusic'e etkisi? | ⚠️ VERIFY REQUIRED | legal-advisor + embedded |
| Q2 | NAudio + ASIO (2026-09-28) tam hangi thread? | ⚠️ VERIFY REQUIRED | researcher |
| Q3 | JUCE 8811★ bağımsız sayımı? | ⚠️ VERIFY REQUIRED | researcher |
| Q4 | ASIO SDK 2.3.4 hedefi güncel mi? | ⚠️ VERIFY REQUIRED | embedded-engineer |
| Q5 | engine ↔ backend sınırı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q6 | input/output ↔ device/driver ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |

## §4 Adım adım doğrulama sırası

| Adım | İş | Sorumlu | Bağımlılık |
|---|---|---|---|
| V1 | Disk sayımı: 4 MD + 12 çocuk index | bu ajan | — |
| V2 | v2 §2.3 ile birebir diff | bu ajan | V1 |
| V3 | URL erişilebilirlik tekrarı (§1: 1, 3, 5, 7) | researcher | — |
| V4 | ⚠️/UNKNOWN kanıt turu-2 (§1: 2, 4, 6, 11, 12) | researcher | V3 |
| V5 | Q1–Q6 kararları | Vault Steward + ilgili uzman | V4 |
| V6 | [TASARIM] → [KANIT] geçişi | bu ajan | V4 |
| V7 | status: verified güncellemesi | Vault Steward | V1–V6 ✅ |

## §5 Kanıt bütünlüğü kuralları

1. URL'siz veya dosya yolsuz iddia yazılmaz → **UNKNOWN**.
2. Repo varlığı ≠ içerik doğruluğu (yıldız/sürüm ayrı satırdır).
3. Sayısal iddialar (yıldız, sürüm, tarih) erişim tarihiyle yazılır.
4. v2 yazım hataları düzeltilmez — v2 §3 KURAL 1.
5. Uyuşmazlık tek taraflı düzeltilmez, **iki taraflı kaydedilir**.
6. Görev manifesti ile web ölçümü çelişirse (ör. 8811★) ikisi de yazılır, fark ⚠️ işaretlenir.

## §6 Kanıt turu planı (tur-2 ne zaman açılır)

| Tur | Kapsam | Tetikleyici | Beklenen çıktı |
|---|---|---|---|
| tur-1 | §1 satır 1–12 | bu dosyanın yazımı (2026-10-12) | tamamlandı — 5 ✅/iç + 4 ⚠️ + 1 UNKNOWN + 2 ✅ |
| tur-2 | §1 satır 2 (lisans etkisi) | hukuki değerlendirme istendiğinde | Q1 kapanış kaydı |
| tur-3 | §1 satır 4, 11, 12 | researcher sayım/sürüm turu | yıldız + sürüm + paket kanıtı |
| tur-4 | §1 satır 6 | NAudio thread başlığı bulunduğunda | kesin URL + tarih |
| tur-5 | D6 | ilk [KANIT] bağımlılığı | bagimliliklar §2/§5 etiket güncellemesi |

**Tazelik kuralı:** URL kanıtları 90 günden eskiyse erişim tarihiyle yeniden denenir;
erişilemezse ✅ → ⚠️ VERIFICATION REQUIRED olarak geri alınır ve §6'ya satır eklenir.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
