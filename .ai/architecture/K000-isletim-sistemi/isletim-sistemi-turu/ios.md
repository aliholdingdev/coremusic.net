---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — iOS (Core Audio / AVAudioSession)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# iOS — Ses Yolu ve Zemin

> Kaynak: Apple Developer — Core Audio (iOS mimarisi) · Core Audio Overview (archive) — 2026-10-10 erişimli.
> Vault: `[[../os-master]]` · `[[ADR-004-multi-domain-spa]]` (mobil yüzey)

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Mimari fark | iOS'ta **HAL ve I/O Kit'e doğrudan erişim API'si YOK** — sistem tarafından sıkı yönetilen katman gizlenir | Apple — What Is Core Audio? (iOS §) |
| Donanım köprüsü | **AURemoteIO** birimi donanım ile uygulama grafiği arasında geçiş yapar (macOS'taki AUHAL karşılığı) | Apple — Core Audio Overview §HAL |
| Oturum yönetimi | **Audio Session Services** — cihaz hem telefon/iPod olduğunda uygulama ses davranışını yönetir (kategori, kesinti) | Apple — Core Audio Services |
| Yüksek seviye | AVAudioPlayer (basit) · Audio Queue Services (kayıt/oynatma, kod çözücü) · AVAudioEngine (graf, real-time) | Apple — Playback/Recording sections |
| Sınırlama | iOS'ta geliştirilen özel Audio Unit'ler **statik bağlanır**, diğer uygulamalar kullanamaz | Apple — What Is Core Audio? |

## §2 CoreMusic Karşılığı
- ADR-004 (multi-domain SPA) mobil web yüzeyini tanımlar; **native iOS oynatıcı** vault'ta kararlı değil → ADR-019 yalnız 3 masaüstü platformu (Win/macOS/Linux) taşır.
- iOS ses yolu CoreMusic için **web tarayıcı üzerinden** (AVAudioEngine'i sarmalayan Safari/WebKit) mi yoksa native mi kullanılacağı → **UNKNOWN** (karar yok).

## §3 Durum
**PLANNED / UNKNOWN** — repo'da Swift/ObjC 0 · vault'ta iOS-native oynatıcı ADR'si **YOK**.

## §4 Bilinmeyenler
- Native iOS hedefi var mı → vault'ta kanıt yok → `⚠️ VERIFICATION REQUIRED`.
- AVAudioSession kategori seçimi → karar yok → UNKNOWN.
