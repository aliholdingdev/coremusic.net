---
title: CoreMusic — Standard 60/40 Layout Pattern
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
---

# CoreMusic — Standard 60/40 Layout Pattern

## Kullanım

Albümler, Sanatçılar, Dosya Yöneticisi, Playlist, Göz At (3 sütun varyantı)

## ASCII Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — 60px]                                                             │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ← Başlık / Alt başlık                        [Arama] [Sıralama]          │
│  [Tab'lar — opsiyonel]                                                     │
│                                                                              │
│  ┌── SOL (%60, ~614px) ─────────────┐  ┌── SAĞ (%40, ~390px) ──────────┐ │
│  │                                    │  │                                  │ │
│  │  İçerik (kart grid veya tablo)     │  │  Det Panel (seçili öğe)         │ │
│  │  3 sütun × N satır                 │  │  Art + Başlık + Metadata        │ │
│  │  Gap: 8px                          │  │  Butonlar                       │ │
│  │  Scroll: dikey                     │  │  Glass efekti                   │ │
│  │                                    │  │                                  │ │
│  └────────────────────────────────────┘  └──────────────────────────────────┘ │
│                                                                              │
├──────────────────────────────────────────────────────────────────────────────┤
│ [FOOTER — 90px]                                                             │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Ölçüler

| Bölge | Genişlik | Yükseklik | Padding |
|-------|----------|-----------|---------|
| Header | 100% | 60px | — |
| Sol panel | ~60% (614px) | 450px (scroll) | 12px |
| Sağ panel | ~40% (390px) | 450px | 16px |
| Gap | 12px | — | — |
| Footer | 100% | 90px | — |

## Kurallar

1. Sol panel scroll edilebilir, sağ panel sabit
2. Sağ panel glass efekti: `backdrop-filter: blur(8px)`
3. Gap: 12px (sol ile sağ arası)
4. Kart grid: 3 sütun, gap 8px
5. Tab'lar varsa, sol panelin üstünde

## Kullanıldığı Ekranlar

| Ekran | Varyant |
|-------|---------|
| Albums | Kart grid (C09) |
| Artists | Dairesel kart grid (C09, r:50%) |
| File Manager | Kategori listesi + disk listesi |
| Playlist | Tablo (C13) |
| Göz At (3 sütun) | Sidebar + liste + bilgi paneli |

---

*Standard 60/40 Layout v2.0.0 — CoreMusic UI Design System*
