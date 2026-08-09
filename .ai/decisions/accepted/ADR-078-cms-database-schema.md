---
type: adr
category: database
title: "ADR-078: CMS Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-078: CMS Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `cms_pages` | Statik sayfalar |
| 2 | `blog_posts` | Blog/duyuru yazıları |
| 3 | `blog_categories` | Blog kategorileri |
| 4 | `blog_tags` | Blog etiketleri |
| 5 | `blog_post_tags` | Yazı-etiket eşleştirme (junction) |
| 6 | `cms_media_assets` | Medya dosyaları |
| 7 | `cms_faqs` | SSS kayıtları |
| 8 | `cms_banner_slides` | Banner/slides yönetimi |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| cms_pages | id → {slug, title, content, ...} | slug UNIQUE |
| blog_posts | id → {title, slug, content, ...} | slug UNIQUE |
| blog_categories | id → {name, slug, ...} | slug UNIQUE |
| blog_tags | id → {name, slug, ...} | slug UNIQUE |
| blog_post_tags | id → {post_id, tag_id, ...} | (post_id, tag_id) UNIQUE |
| cms_media_assets | id → {file_name, file_path, ...} | file_path UNIQUE |
| cms_faqs | id → {question, answer, ...} | id |
| cms_banner_slides | id → {title, image_url, ...} | id |

---

## 3. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-040-database-authority]] | DB otoritesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
