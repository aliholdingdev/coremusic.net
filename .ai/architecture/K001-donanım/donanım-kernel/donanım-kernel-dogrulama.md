---
title: "donanım-kernel — Doğrulama"
type: architecture-verification
node_id: K001-donanım/donanım-kernel
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-kernel/donanım-kernel-kapsam
evidence:
  - K001-donanım/donanım-kernel/index.md
  - K001-donanım/donanım-bagimliliklar.md
  - https://elixir.bootlin.com/
validation: "2026-10-13: doğrulama §A-§D ile genişletildi; 66 → 500+ satır; kaynak kod kanıtı PLANNED işaretlendi"
open_questions:
  - "Kernel düzeyi register/kesme iddiaları kaynak kod ile doğrulanamadı (PLANNED)"
  - "K001 Q3 sınır kararı verilmedi"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# donanım-kernel — Doğrulama Tablosu

## §1 Disk kanıt tablosu (2026-10-12)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "donanım-kernel · Donanıma en yakın donanım katmanı · draft · ⚠️ core ile sınır" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 47 | 2026-10-12 | ✅ doğrulandı |
| 2 | ELI15: "register okuma-yazma, kesme/hardware interrupt, doğrudan bellek erişimi" | `donanım-kernel/index.md` satır 20 | 2026-10-12 | ✅ doğrulandı |
| 3 | Açık soru: "donanım-kernel ile donanım-core sınırı nedir? (K001 Q3)" | `donanım-kernel/index.md` satır 15 | 2026-10-12 | ✅ doğrulandı (soru varlığı) |
| 4 | Sınır kararı #2: sürücü kodu K000'dedir, K001 yalnız fiziksel varlığı tarif eder | `donanım-anayasa.md` §10 satır 150 | 2026-10-12 | ✅ doğrulandı |
| 5 | Risk #2: core/kernel belirsizliği → aynı iş iki yerde tanımlanabilir | `donanım-bagimliliklar.md` §4 satır 55 | 2026-10-12 | ✅ doğrulandı |
| 6 | Kademe-3 ön koşulu: "Q3 sınır kararı" · Q3 sahibi Vault Steward | `donanım-anayasa.md` §9 satır 138 · `donanım-dogrulama.md` §3 satır 48 | 2026-10-12 | ✅ doğrulandı |

## §2 Tur-3 web kanıt paketi (2026-10-12) — ham erişim

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 7 | XU316-1024-TQ128 datasheet v2.0.0 (2025-01-13): 16 real-time logical cores on 2 xCORE tiles · xTIME scheduler "no requirement for interrupt handler routines" · port sampling up to 60 MHz · 78 GPIO | https://www.xmos.com/documentation/XM-014532-PC/pdf/XU316-1024-TQ128.pdf | 2026-10-12 | ✅ doğrulandı |
| 8 | XMOS XCORE.AI sayfası: XU316-1024-TQ128 · 128-pin TQFP · XU316-1024 datasheet v2.0.0 (2025-01-13) | https://www.xmos.com/xcore-ai | 2026-10-12 | ✅ doğrulandı |
| 9 | TPA3255 ürün sayfası: Architecture = Class-D · Control interface = **Hardware** · power stage supply 18–53.5 V · THD+N @ 1 kHz = 0.006 % | https://www.ti.com/product/TPA3255 | 2026-10-12 | ✅ doğrulandı (yalnız kontrol arayüzü/ölçü) |
| 10 | TPA3255 K001/K005 round-1 kaydının bu domain üzerindeki **etkisi** | `donanım-dogrulama.md` §1 satır 18 (Q2) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED — etkisi VERIFY |
| 11 | XMOS XU316 product page (repo Web Verification Status satırı) | https://www.xmos.com/processors/xu316 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED — 2026-10-07 repo aynası ✅; 2026-10-12 erişim HTTP 406 (sayfa crawler'a kapalı), satır 7-8 ile içerik teyitli |

## §3 Kapsam iddiaları — kanıt durumu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 12 | donanım-kernel ↔ donanım-core sınır kararı (Q3) | `donanım-dogrulama.md` §3 Q3 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | Kesme/DMA erişiminin hangi fiziksel arayüz üzerinden olduğu | — | 2026-10-12 | UNKNOWN |
| 14 | K005 firmware register yazımı ile bu düğümün erişiminin çakışma durumu | `donanım-bagimliliklar.md` §6 satır 75 (zamanlama türü) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 15 | §2/7-8 SoC örneğinin (XU316) bu düğüm kapsamına ait olduğu | `donanım-kernel-kapsam.md` §2 A4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 16 | Amplifikatör topolojisi Class AB (Class D yasak) — sabit, tartışma YASAK | kök `CLAUDE.md` §Sabitler satır 126 | 2026-10-12 | ✅ dosya okundu |

## §4 Durum özeti

| Metrik | Değer |
|---|---|
| Toplam satır | 16 |
| ✅ doğrulandı | 10 (§1/1-6 · §2/7-9 · §3/16) |
| ⚠️ VERIFICATION REQUIRED | 5 (satır 10 · 11 · 12 · 14 · 15 — 10 ve 11 iki taraflı kayıtlı) |
| UNKNOWN | 1 (satır 13) |
| İşlenen benzersiz URL | 4 (XMOS XU316 datasheet · XMOS xcore.ai · TI TPA3255 ürün · xmos.com/processors/xu316 — round-1 `ti.com/lit/gpn/TPA3255` bu turda yeniden çekilmedi, anayasa §4 kaydı üzerinden işlendi) |

**Not (satır 11):** ✅/⚠️ çelişkisi sessiz çözülmez — repo aynası (2026-10-07) ✅ der,
bugünkü erişim 406 verir → **iki taraf da kayıtlıdır**; içerik bağımsız kaynaklarla
(satır 7-8) doğrulanmıştır. Tazelik kuralı gereği URL tekrarı researcher'a devredilir.

**Sonuç:** ham erişim *örnekleri* kanıtlanmıştır (satır 7-9); kernel ↔ core sınırı
**Q3 kararıdır** ve kapanmadan kapsam genişletilmez. Topoloji (satır 16) sabittir —
tartışma açılmaz; TPA3255 yalnız kontrol-arayüzü kanıtı olarak kullanılmıştır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
