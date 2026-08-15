---
type: decision
id: "078"
title: "ADR-078: CMS Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, cms, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-078: CMS Database Schema

---

## 1. Executive Summary

CMS veritabanı, sayfaları, blog yazılarını, etiketleri, SSS'leri ve banner'ları yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | cms_pages | Statik sayfalar |
| 2 | cms_blog_posts | Blog yazıları |
| 3 | cms_tags | Etiketler |
| 4 | cms_post_tags | Yazı-etiket ilişkisi |
| 5 | cms_media_assets | Medya varlıkları |
| 6 | cms_faqs | SSS'ler |
| 7 | cms_banners | Banner'lar |
| 8 | cms_categories | Kategoriler |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | SEO metadata | ✅ Zorunlu |
| 4 | Draft/Published status | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-078: CMS Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
