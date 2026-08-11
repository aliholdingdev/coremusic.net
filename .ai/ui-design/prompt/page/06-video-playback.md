---
title: "Sayfa Prompt — Video Playback"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/playlist/:id (video)"
layout: "fullscreen"
---

# Video Playback

## Route: `/playlist/:id` (video modu)
## Layout Pattern: Fullscreen

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Back Arrow | 1 | Sol üst | Geri oku butonu |
| Video Area | 1 | Sol (70%) | Video oynatıcı |
| Song List | 1 | Sağ (30%) | Parça listesi |
| Mini Player | 1 | Sol alt | Küçük oynatıcı |

## Sol Alan (70% = 716px)

1. **Video Area** — Tam genişlikte video
   - `width: 100%; height: 100%`
   - `object-fit: contain`
   - Siyah arka plan

## Sağ Alan (30% = 292px)

2. **Song List** — Parça listesi
   - Scrollable
   - Her satır: Küçük kapak, başlık, süre
   - Seçili: vurgulu
   - h=44px satır yüksekliği

## Mini Player (Sol Alt)

3. **Mini Player** — Sol alt köşede sabit
   - `position: fixed; bottom: 16px; left: 16px`
   - Albüm kapağı (40×40px)
   - Şarkı adı, sanatçı
   - Oynat/duraklat butonu
   - Glass efekti
   - `width: fit-content`

## Back Arrow

- Sol üst köşede
- Minimum 44×44px dokunma alanı
- Beyaz renk, hafif gölge

## ASCII Art Referansı

`00-mockup-index.md` §4.7 — Video Playback ASCII Art
