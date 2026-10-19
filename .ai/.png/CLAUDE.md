# CoreMusic — .png Bağlam

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

---

## 1. Bağlam

Orijinal PNG mockup görselleri. Frontend görünmez otorite (Guardrail #11). İndeks: `ui-design/01-mockup-index.md`.

---

## 2. PNG Envanteri

| Klasör | Görsel Sayısı | Kullanım |
|--------|---------------|----------|
| `admin-1920/` | 3 PNG | Admin panel mockup'ları (layout · Login · Register — 2026-10-16) |
| `home-1024/` | 12 PNG | Ana sayfa mockup'ları (1024×600 RPi5) |
| `home-1920/` | 4 PNG | Ana sayfa mockup'ları (1920×1080 desktop — Home · Göz At · Playlist · Geçmiş; 2026-10-18 scale=2 taze export) |
| `home-3840/` | 1 PNG | Ana sayfa mockup'ı (3840×2160 4K — Home; 2026-10-18 scale=1 export) |
| `shared-1024/` | 6 PNG | Paylaşılan ekran mockup'ları (1024×600) |
| **TOPLAM** | **26 PNG** | Frontend geliştirme referansı |

(2026-10-18 ölçümü: 5 klasör · 26 PNG — `Get-ChildItem .ai/.png -Recurse -Filter *.png`.)

---

## 3. Referans Sıralaması (Çelişki Durumunda)

```
PNG > ASCII art > Component Inventory > Tokens > Implementation Plan
```

---

## 4. Protokol

- Boot protocol uygulanır ([[../CLAUDE.md]] §16)
- Değişiklikler [[../log.md]] audit izine yazılır
- PNG okunamıyorsa DUR ve kullanıcıya bildir

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-18
