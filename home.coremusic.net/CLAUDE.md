---
title: "CoreMusic — home.coremusic.net Bağlam"
type: context
folder: "home.coremusic.net"
category: domain
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# home.coremusic.net — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/subdomains/home.coremusic.net/index.md]]

## 1. Bağlam

Ana kullanıcı yüzü. RPi5 embedded hedefli olduğundan device CSS zinciri (`d-embedded.css` + `d-auth-*`) ve `ScaleManager`/`DeviceManager` ile ölçeklenir. Son değişiklikler: header/footer güncellemesi (2026-09-06 öncesi oturum, log'da).

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Sayfa sayısı | 6 |
| Auth entegrasyonu | Bridge (redirect → auth → callback) |
| Konfig yedek | `.env.bak-20260906` (manuel, otomatik senkron yok) |
| Mockup uyumu | home-1024 seti 12 ekran — home.php/player.php ana referanslar |
| Bilinen risk | Test klasörü yok (auth servisiyle kıyasla) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Auth sağlayıcı | [[../auth.coremusic.net/CLAUDE.md]] | Bridge protokolü |
| Asset sağlayıcı | [[../assets.coremusic.net/CLAUDE.md]] | CSS/JS/font/görsel |
| Paylaşılan altyapı | [[../shared/CLAUDE.md]] | RuntimeBootstrap, Session, Config |
| UI mockup | [[../.ai/ui-design/screens/B-home/dashboard.md]] | ASCII art referans |

## 4. Değişiklik Protokolü

1. Görünüm değişikliği → PNG mockup okuma → PHP view + gerekirse CSS katmanı → PNG karşılaştırma → doğrulama
2. Auth akışı değişikliği → auth-flow.md okuma → bridge kontratına saygı
3. `health.php` davranışı bozulmaz (deployment doğrulama bağımlılığı)
4. Audit `[[../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
