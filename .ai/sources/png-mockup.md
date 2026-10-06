---
title: "Vault Kaynağı: raw/.png (PNG Mockup Seti)"
type: kaynak
raw_path: raw/.png/CLAUDE.md
created: 2026-10-06
updated: 2026-10-06
sources: [png-mockup]
ingested: 2026-10-06 19:36
tags: [mockup, png, ui, asset]
---

# Kaynak Özeti — raw/.png (4 bağlam notu + 19 PNG)

## Genel Özet

Frontend mockup görselleri paketi: 19 PNG + 4 CLAUDE.md bağlam notu (kök envanter + 3 klasör notu). Frontend'in görünmez otoritesi (Guardrail #11); indeks `.ai/ui-design/01-mockup-index.md` (eski ağaç kopyası: ⚠️ VERIFICATION REQUIRED).

## Ana Fikirler

1. Kök `raw/.png/CLAUDE.md` (41 satır): envanter home-1024/ 12 PNG (1024×600 RPi5) · home-1920/ 1 PNG (1920×1080 desktop) · shared-1024/ 6 PNG (1024×600) = **19 PNG**; çelişki durumunda referans sıralaması bölümü var.
2. `home-1024/CLAUDE.md`: kanonik 1024×600 referans seti, UI Designer birincil doğrulama kaynağı (v1.0.1, authority: reference).
3. `home-1920/CLAUDE.md`: desktop mockup başlangıcı, tek ekran.
4. `shared-1024/CLAUDE.md`: auth ekranlarının görsel SSOT'u; auth.coremusic.net UI kodlaması buradan doğrulanır.
5. Klasör notlarında komşu bağlantılar "*(üretilecek)*" işaretli → `.png-analysis/` bağı diskte yok ⚠ VERIFICATION REQUIRED.

## Önemli Alıntılar/Veriler

- "Orijinal PNG mockup görselleri. Frontend görünmez otorite (Guardrail #11)."
- Envater sayıları klasör sayımıyla birebir doğrulandı (12+1+6 = 19 ✓, 2026-10-06 19:36)

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[png-mockup]] (yeni)

---

## Ham Veri — `.ai/raw/.png/CLAUDE.md` (tam metin, satır içine gömülü)

# CoreMusic — .png Bağlam

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

---

## 1. Bağlam

Orijinal PNG mockup görselleri. Frontend görünmez otorite (Guardrail #11). İndeks: `ui-design/01-mockup-index.md`.

---

## 2. PNG Envanteri

| Klasör | Görsel Sayısı | Kullanım |
|--------|---------------|----------|
| `home-1024/` | 12 PNG | Ana sayfa mockup'ları (1024×600 RPi5) |
| `home-1920/` | 1 PNG | Ana sayfa mockup'ı (1920×1080 desktop) |
| `shared-1024/` | 6 PNG | Paylaşılan ekran mockup'ları (1024×600) |
| **TOPLAM** | **19 PNG** | Frontend geliştirme referansı |

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
**Last Updated:** 2026-09-29
