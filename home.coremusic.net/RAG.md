---
title: "home.coremusic.net — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: home.coremusic.net/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# home.coremusic.net — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `home.coremusic.net` alanında çalışan AI agent'larının veya geliştiricilerin tüm depoyu taramadan, ana portal ve web oynatıcıyla ilgili dosyalara nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/home.coremusic.net/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Ana Portal Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/home.coremusic.net/CONTEXT.md) | `composer.json` |
| **Header, Navigasyon & CSP** | `header.php` | `config/bootstrap.php` |
| **Footer, Oynatıcı & Ses Barı** | `footer.php` | `assets.coremusic.net/js/coreplayer/` |
| **Ana Sayfa Düzeni (Home Layout)** | `pages/home.php` | `include/Class/HomeLayoutVariant.php` |
| **Müzik Göz At & Drill-Down Explorer** | `pages/browse.php` | `assets.coremusic.net/Css/05_Pages/p-browse.css` |
| **Çalma Listesi & Şarkı Tablosu** | `pages/playlist.php` | `include/Repository/PlaylistRepository.php` |
| **Şimdi Çalıyor & Disk Player** | `pages/nowplaying.php` · `pages/diskplayer.php` | `assets.coremusic.net/Css/04_Components/` |
| **Albüm & Sanatçı Detay Sayfaları** | `pages/album-detail.php` · `pages/albums.php` | `assets.coremusic.net/Css/05_Pages/p-album-detail.css` |
| **Terminal, Telemetri & Donanım Monitörü** | `pages/terminal.php` | `assets.coremusic.net/Css/04_Components/c-terminal.css` |
| **Hızlı Ayarlar & Ağ Modalleri** | `pages/components/quick-settings-modal.php` | `pages/components/quick-network-panels.php` |
| **Akış İşleyici & Ses Motoru Köprüsü** | `include/Stream/MusicStreamHandler.php` | `include/Repository/MusicRepository.php` |
| **Birim Testleri (PHPUnit)** | `tests/Unit/` | `phpunit.xml` |

---

## 🎯 Keyword & Arama Etiketleri

`home`, `browse`, `playlist`, `diskplayer`, `nowplaying`, `history`, `album-detail`, `terminal`, `telemetry`, `quick-settings`, `stream-handler`, `light-mode`, `volume-slider`, `widget-grid`, `recent-tracks`

---

## ⚠️ Kritik Alan Kısıtları

1. **YALNIZCA LIGHT MODE (`HL-01`):** Tüm CSS ve PHP şablonları yalnızca Light Mode için tasarlanır; dark mode varsayımı veya CSS dark class'ı yazmak yasaktır.
2. **HAM ORAN HASSASİYETİ (`HL-06`):** Ses eşikleri yuvarlanmış yüzde yerine ham oran (`$volRatio <= 0.02 / 0.33 / 0.66`) ile test edilir.
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi yalnızca kullanıcıya / orkestratöre aittir.

---

*home.coremusic.net RAG Router v1.0.0 — SSOT: [`home.coremusic.net/CONTEXT.md`](file:///c:/www/coremusic.net/home.coremusic.net/CONTEXT.md)*
