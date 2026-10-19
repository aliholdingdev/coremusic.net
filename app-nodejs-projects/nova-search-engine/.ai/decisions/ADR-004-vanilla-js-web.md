# ADR-004 — Web: Vanilla JS + ITCSS/BEM

- **Durum:** Kabul
- **Tarih:** 2026-10-16
- **Alan:** Arayüz

## Bağlam

Web arayüzü teknolojisi: framework (React/Vue) mi, Vanilla mı? Kullanıcı kararı
net: **Vanilla JS + TypeScript**, framework yok.

## Karar

- React, Vue, Angular **kullanılmaz**
- ITCSS katmanları: `01-abstracts` (token) → `02-base` → `03-layout` → `04-components`
- BEM adlandırma (`cm-block__element--modifier`)
- Sabit değer yalnız `01-abstracts.css` içinde `--cm-*` token'larıyla
- Sunum: `@fastify/static` üzerinden API ile tek süreç

## Gerekçe

- Framework bağımlılığı yok, bundle küçük, bakım ucuz
- Yerel/kişisel ürün için SPA altyapısı gereksiz karmaşıklık (YAGNI)

## Sonuçlar

- Ekranlar: arama + filtre + sonuç kartları (detay V2)
- WCAG 2.2: 3px odak halkası, aria-live sonuç bölgesi, mobil-öncelikli grid
- Yeni ekran = mevcut katman/BEM kurallarına uyar
