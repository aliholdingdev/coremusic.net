---
type: architecture
category: layer-subindex
title: "K000 · isletim-sistemi-plan — K000 Planlanan İşler"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# isletim-sistemi-plan — PLANNED Kayıtları

> Üst indeks: `[[../index]]` · Makro: `[[../os-master]]` · Durum SSOT: `[[../00-master-index]]`

## §1 Kapsam
K000 kapsamında **henüz uygulanmamış** işler. Her satır: kayıt → neden → kanıt durumu.

## §2 Plan Kayıtları

| # | İş | Neden | Kanıt Durumu |
|---|---|---|---|
| 1 | ADR-015 env parser satır-bazlı doğrulama | `EnvParser.php` + 4 `constants.php` çağırısı bulundu (2026-10-10 grep); ADR metni ile birebir eşleşme okunmadı | `⚠️ VERIFICATION REQUIRED` |
| 2 | `assets.coremusic.net` composer kapsamı kararı | composer.json YOK; kasıt (statik varlık mı, unutulmuş mu?) belirsiz | UNKNOWN (os-master §7-2) |
| 3 | IAudioBackend implementasyonu (Win/mac/Linux) | ADR-019 accepted; repo'da native kod 0 | PLANNED (ADR-019) |
| 4 | Platform gecikme ölçüm seti (<10 ASIO / <20 WASAPI) | ADR-006 kapısı; hiçbir platformda ölçüm yok | PLANNED (ADR-006) |
| 5 | K001 donanım zemini | ADR-061/038 PLANNED; disk kanıtı YOK | PLANNED (os-master §7-3) |
| 6 | `isletim-sistemi-turu/andorid.md` dosya adı düzeltmesi | yazım hatası; wiki-link 0 → rename güvenli ama **onay gerektirir** | RAPOR (ad değişikliği yasak-onsay) |

## §3 Bağımlılık
- **Alt:** tüm K000 alt klasörleri (durumları buraya akar).
- **Üst:** `[[../00-master-index]]` (layer status SSOT — bu dosya status **yazmaz**, yalnız iş listesi tutar).

## §4 Kapanış Kriteri
Bir satır yalnız, disk kanıtı (gerçek dosya yolu + içerik) veya ADR durum değişikliği ile kapanır; iddia ile kapanmaz.
