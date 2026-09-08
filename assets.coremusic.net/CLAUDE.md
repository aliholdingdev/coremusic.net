---
title: "CoreMusic — assets.coremusic.net Bağlam"
type: context
folder: "assets.coremusic.net"
category: domain
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# assets.coremusic.net — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/ui-design/00-mockup-index.md]]

## 1. Bağlam

Tüm subdomainler (auth, home, music, admin...) CSS/JS'i bu servisten çeker. Değişiklikler **tüm platformu etkiler** — tek dosya değişikliği dahi çapraz etki analizinden geçer. L3 (Presentation) katmanının kod karşılığı burada ikamet eder.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| ITCSS katmanları | 01_Abstracts → 11_OAuth (10 klasör) |
| JS modül sayısı | ~50 (router 28 + features 5 + managers 5 + core 4 + auth 2 + giriş 5) |
| Device CSS | 13 (4k→phone aralığı, auth varyantları dahil) |
| ViewMode CSS | 4 (car, home, pro, studio) — ADR-045 |
| Font sayısı | 105 |
| Bilinen risk | `bluethoot-connected.png.png` çift uzantı (typo var olan dosya) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Tüketen | [[../auth.coremusic.net/CLAUDE.md]] | Login/register sayfaları CSS+JS çeker |
| Tüketen | [[../home.coremusic.net/CLAUDE.md]] | Home panel asset'leri çeker |
| Mimari | [[../.ai/architecture/l3-presentation/index.md]] | Presentation katman kuralları |
| Router karşılığı | [[../shared/src/PageRouter/CLAUDE.md]] | PHP tarafı HTML shell üretir, JS router devralır |
| Device senkron | [[../shared/src/Device/CLAUDE.md]] | DeviceCssMap ↔ devices.config.js |

## 4. Değişiklik Protokolü

1. CSS değişikliği → ilgili ITCSS katmanında → token etkisi `01_Abstracts` kontrolü → mockup karşılaştırması → PNG doğrulama
2. JS değişikliği → modül sınırına saygı (EventBus üzerinden iletişim) → CSP uyumu
3. Görsel ekleme → `res-pink` alt kategorisine yerleşim → BEM adlandırma ile eşleşen dosya adı
4. Audit `[[../.ai/log.md]]` + vault-sync (vault etkisi varsa)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
