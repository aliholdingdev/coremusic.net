---
title: "ADR-078: CMS Database Schema"
status: active
date: 2026-08-10
tags: [database, cms, schema, active]
---

# ADR-078: CMS Database Schema

---

## 1. Executive Summary

CMS veritabanÄ±, sayfalarÄ±, blog yazÄ±larÄ±nÄ±, etiketleri, SSS'leri ve banner'larÄ± yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | cms_pages | Statik sayfalar |
| 2 | cms_blog_posts | Blog yazÄ±larÄ± |
| 3 | cms_tags | Etiketler |
| 4 | cms_post_tags | YazÄ±-etiket iliÅŸkisi |
| 5 | cms_media_assets | Medya varlÄ±klarÄ± |
| 6 | cms_faqs | SSS'ler |
| 7 | cms_banners | Banner'lar |
| 8 | cms_categories | Kategoriler |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | SEO metadata | âœ… Zorunlu |
| 4 | Draft/Published status | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-078: CMS Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*