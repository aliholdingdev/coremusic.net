---
title: CoreMusic — Split Home Layout Pattern
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
---

# CoreMusic — Split Home Layout Pattern

## Kullanım

Ana Sayfa (Dashboard)

## ASCII Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — 60px]                                                             │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌── NOW PLAYING (sol %42, ~420px) ──┐  ┌── WIDGETS (sağ %58, ~540px) ──┐│
│  │ [Album Art] Şarkı Adı             │  │ [Bluetooth] [Hava]              ││
│  │               Albüm Adı           │  │ [Takvim] [Klasörlerim]          ││
│  │               Sanatçı             │  │                                  ││
│  │ 00:05 ═══════════ 00:05           │  │ (glass paneller, 2×2 grid)      ││
│  └────────────────────────────────────┘  └──────────────────────────────────┘│
│                                                                              │
│  ── EN SON DİNLENEN ŞARKILAR ──────────────────────────────────────────────│
│  [card×4]                                                                    │
│                                                                              │
│  ── OYNATMA LİSTELERİ ─────────────────────────────────────────────────────│
│  [card×5]  ☐ "Oynatma listesini göster"                                     │
│                                                                              │
│  ── SIRADAKİ ŞARKILAR ──                              [Mini Card]           │
│                                                                              │
├──────────────────────────────────────────────────────────────────────────────┤
│ [FOOTER — 90px]                                                             │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Ölçüler

| Bölge | Genişlik | Yükseklik |
|-------|----------|-----------|
| Now Playing | %42 (~420px) | ~120px |
| Widgets | %58 (~540px) | ~280px |
| En Son Dinlenen | %100 | ~200px (kartlar) |
| Oynatma Listeleri | %100 | ~200px (kartlar) |
| Sıradaki Şarkılar | %65 | ~100px |
| Mini Card | %35 | ~100px |

## Kurallar

1. Sidebar YOK (sadece Göz At'ta var)
2. Now Playing sol tarafta, sabit yükseklik
3. Widgets sağ tarafta, glass paneller
4. Kart grid'leri: 4-5 sütun, gap 8px
5. Footer player: 90px, sabit alt

---

*Split Home Layout v2.0.0 — CoreMusic UI Design System*
