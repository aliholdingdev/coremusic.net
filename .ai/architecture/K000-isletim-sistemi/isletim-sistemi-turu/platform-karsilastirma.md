---
title: "K000-isletim-sistemi-turu/platform-karsilastirma"
type: architecture
category: layer-detail
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Platform Karşılaştırması — 8 Platform (Destek Hedefi / Runtime Notu)

## §1 Kimlik

Bu dosya klasördeki 8 platform dosyasını **tek tabloda** karşılaştırır; platform bazlı derinlik
kardeş dosyalardadır. Üst: `[[index]]` · Kardeş: `[[windows]]` · `[[linux]]` · `[[macos]]` ·
`[[ios]]` · `[[andorid]]` · `[[tizen]]` · `[[raspberry-pi-6]]` · `[[web]]`.

## §2 Kapsam & Sorumluluk

| Kapsam (İÇİN) | Kanıt | Kapsam Dışı (DIŞI) |
|---|---|---|
| 8 platformun yığın (tier) · ses yolu · ADR kapsamı · durum özeti | `.ai/CLAUDE.md` §13 (satır 352-358) · ADR-019 §1 (dosya satır 25) | Platform başına ölçüm/ eşik değeri (→ 4 stub, sayı yasak) |
| Web yüzeyinin IMPLEMENTED durumu | 3 subdomain `index.php` glob + `shared/src/PageRouter/` (14 dosya) | Sürücü/native kod (kod = 0 — PLANNED) |
| Native kapsam dışı 4 platformun karar durumu | ADR-019 §1 yalnız 3 masaüstü platform yazar | Native iOS/Android/Tizen kararı (vault'ta YOK → UNKNOWN) |

## §3 Bağımlılık Kuralları

- **Alt katman:** YOK — bu dosya en dip zemin notudur; hiçbir dosyayı çağırmaz.
- **Üst katman:** `[[index]]` (klasör dizini) bu tabloyu manifestine sayar.
- **Yasak:** bu tabloya disk kanıtı olmayan **sürüm/eşik/ölçüm** satırı eklenmez; eklenirse
  halüsinasyondur → revert + log ERROR (şablon §3.3).

## §4 Arayüz — Karşılaştırma Tablosu

**Tablo A — Yığın (tier) + durum (kanıt: `.ai/CLAUDE.md` §13, satır 352-358):**

| # | Platform | Yığın | CLAUDE §13 durumu | Ses yolu (§13 sütunu) | Dosya |
|---|---|---|---|---|---|
| 1 | Windows | Tier 1 (Primary) | ✅ Ana geliştirme | ASIO, WASAPI | `[[windows]]` |
| 2 | Linux | Tier 2 | ✅ Destekli | ALSA, PipeWire | `[[linux]]` |
| 3 | macOS | Tier 3 | ✅ Destekli | CoreAudio | `[[macos]]` |
| 4 | Raspberry Pi | Tier 4 | ✅ Destekli | I2S | `[[raspberry-pi-6]]` |
| 5 | ReactOS | Tier 5 | ⚠️ Experimental | Sınırlı | **dosya YOK — manifest dışı (index §7-5)** |
| 6 | iOS | yığın tablosunda YOK | — | — | `[[ios]]` |
| 7 | Android | yığın tablosunda YOK | — | — | `[[andorid]]` |
| 8 | Tizen | yığın tablosunda YOK | — | — | `[[tizen]]` |
| 9 | Web | yığın tablosunda YOK (tarayıcı yüzeyi) | — | Web Audio / HTMLAudioElement | `[[web]]` |

**Tablo B — ADR kapsamı + CoreMusic durumu (kanıt sütunlu):**

| Platform | ADR-019 kapsamı (3 masaüstü) | Durum | Kanıt |
|---|---|---|---|
| Windows | VAR (ASIO + WASAPI Exclusive/Shared) | **PLANNED** (kod 0) | ADR-019 §1 · `../isletim-sistemi-plan/kapsam.md:48` (P3) |
| Linux | VAR (ALSA + PipeWire) | **PLANNED** (kod 0) | ADR-019 §1 · `../isletim-sistemi-plan/senaryolar.md:73` |
| macOS | VAR (CoreAudio) | **PLANNED** (kod 0) | ADR-019 §1 · kod glob 0 |
| Raspberry Pi | YOK (Linux adapter'ının altına girer — dosya notu) | **PLANNED** (vault hedefi) | `[[linux]]` · `brain.md:361` (Tier 4) |
| Web | YOK (ADR-004 sınırları) | **IMPLEMENTED** (yüzey) | `home.coremusic.net/index.php` · `shared/src/PageRouter/` (glob) |
| iOS | YOK | **UNKNOWN** (native karar yok) | `[[ios]]` §2 · ADR-019 yalnız 3 masaüstü |
| Android | YOK | **UNKNOWN** (native karar yok) | `[[andorid]]` §2 · ADR-019 §1 |
| Tizen | YOK | **PLANNED (web) / UNKNOWN (native)** | `brain.md:594` (UA → `4k-tv`) · native karar YOK |

**Tablo C — Deployment modes (kanıt: `.ai/CLAUDE.md` §14, satır 366-370):**

| Mod | Platform | Donanım |
|---|---|---|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

## §5 Senaryolar

- **S1 — Platform seçimi (okuma akışı):** kullanıcı → `[[index]]` §5 okuma sırası →
  `[[platform-karsilastirma]]` (bu dosya) → hedef platform dosyası. Kanıt: `[[index]]` §5.
- **S2 — Destek hedefi sorgusu:** "Windows destekli mi?" → Tablo A satır 1 → CLAUDE §13 "✅ Ana
  geliştirme". Kanıt: `.ai/CLAUDE.md:354`.
- **S3 — Native kapsam sorgusu:** "iOS native oynatıcı var mı?" → Tablo B "UNKNOWN" → `[[ios]]` §8
  (karar yok). Kanıt: ADR-019 §1 (3 masaüstü).
- **S4 — Karar kapısı:** native iOS/Android/Tizen kararı → L3 architect (onaysız kod/ADR yazılmaz).
  Kanıt: `../isletim-sistemi-plan/yasam-dongusu.md` K4 kapısı.
- **S5 — Web doğrulaması:** "ürün neyle çalışıyor?" → Tablo B Web satırı → 3 subdomain
  `index.php` + 14 PageRouter dosyası (glob 2026-10-10).
- **S6 — Çelişki raporu:** Tablo A'da ReactOS satırı var ama dosyası yok → `[[index]]` §7-5'e
  rapor edilir, dosya üretilmez (manifest dışı üretim yasak).

## §6 Risk & Failure Modes

| # | Risk | Tetik | Etki | Sahip |
|---|---|---|---|---|
| R1 | Tabloya kanıtsız sayı/eşik eklenmesi | şablon dışı yazım | halüsinasyon, denetim sahteliği | bu dosya sahibi |
| R2 | Tier tablosu eskiyorsa (CLAUDE §13 güncellenirse) | üst SSOT değişikliği | klasör yanıltıcı olur | `[[../index]]` sahibi |
| R3 | ReactOS (Tier 5) unutulursa | manifest dışı ekleme | sayım 14'ü aşar | manifest sahibi |
| R4 | Native 4 platforma "destekli" denmesi | kanıtsız iddia | yanlış ürün vaadi | architect |

## §7 ADR Bağlantıları

| ADR | Başlık | Durum |
|---|---|---|
| ADR-019 | Per-OS Neva Player | accepted (dosya frontmatter'ı — `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md:8`) |
| ADR-006 | Performance Targets | dosya VAR → durum okuması `⚠️ VERIFICATION REQUIRED` (`[[../isletim-sistemi-plan/adr]]`) |
| ADR-004 | Multi-domain SPA | dosya VAR → durum okuması `⚠️ VERIFICATION REQUIRED` |
| ADR-017 | DSP Hardware Mode | dosya VAR → durum okuması `⚠️ VERIFICATION REQUIRED` |

## §8 Bilinmeyenler

- **K1:** iOS/Android/Tizen native kararı vault'ta yok → `UNKNOWN` (kapanış: yeni ADR).
- **K2:** ReactOS Tier 5 — bu klasörde dosya yok → `PLANNED` değil, **eksik rapor** (manifest dışı).
- **K3:** Yığın tablosu ile ADR-019 kapsamı farklı şeyler taşır (tier ≠ native karar) →
  okuyucu karıştırabilir → bu tablo iki ayrı bölümde ayırır (Tablo A / Tablo B).
- **K4:** Tablo C'deki deployment donanım iddiaları bu görevde doğrulanmadı → `⚠️ VERIFICATION REQUIRED`.
