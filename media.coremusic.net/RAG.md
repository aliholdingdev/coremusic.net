---
title: "media.coremusic.net — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: media.coremusic.net/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# media.coremusic.net — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `media.coremusic.net` alanında çalışan AI agent'larının veya geliştiricilerin medya kataloğu, ses dosyası ayrıştırma (ingest/scan), ULID üretimi, path guard ve akış erişim denetimine nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/media.coremusic.net/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Medya Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/media.coremusic.net/CONTEXT.md) | `composer.json` |
| **Katalog Yazıcı & İçe Aktarma (Ingest)** | `src/Media/CatalogWriter.php` | `bin/ingest.php` |
| **Medya Dosya Tarama & Denetim (Scan/Audit)** | `bin/scan.php` · `bin/audit.php` | `src/Media/CatalogValidator.php` |
| **ULID Üretimi & Medya Kimliği** | `src/Media/Ulid.php` | `src/Media/TypeConverter.php` |
| **Dosya Yolu Güvenliği (PathGuard)** | `src/Media/PathGuard.php` | `src/Media/MediaEnv.php` |
| **Medya Erişim Deposu (ADR-112 Hizalama)** | `src/Media/MySqlMediaAccessRepository.php` | `src/Media/MediaAccessRepositoryInterface.php` |
| **JSONL Katalog Formatı** | `src/Media/JsonlWriter.php` | `src/Media/CatalogTransformer.php` |
| **Birim ve Katalog Testleri (PHPUnit)** | `tests/` | `phpunit.xml` |

---

## 🎯 Keyword & Arama Etiketleri

`media`, `ingest`, `scan`, `audit`, `catalog-writer`, `ulid`, `path-guard`, `jsonl`, `media-access-repository`, `adr-112`, `streaming-security`

---

## ⚠️ Kritik Alan Kısıtları

1. **DB ŞEMA SÖZLEŞME KURALI (ADR-112):** Canlı şema drift bulunduğunda kod esnetilmez, fail-closed davranış korunur; `is_deleted = 0` filtresi sql dump referansıyla yazılır.
2. **DOSYA GÜVENLİĞİ (PATH GUARD):** Directory traversal saldırılarına karşı `PathGuard` zorunludur; ham path parametreleri asla doğrudan dosya sistemine verilmez.
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*media.coremusic.net RAG Router v1.0.0 — SSOT: [`media.coremusic.net/CONTEXT.md`](file:///c:/www/coremusic.net/media.coremusic.net/CONTEXT.md)*
