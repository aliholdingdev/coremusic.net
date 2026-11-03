---
title: "CoreMusic — bin/ Context"
type: docs
category: domain
version: 1.0.0
status: active
authority: "SSOT: bin/CONTEXT.md · companion: bin/RAG.md · bin/durum.md"
updated: 2026-11-01
---

# CoreMusic — bin/ Context

**docType:** context · **Klasör:** `bin/` · **Sorumlu:** DevOps / Tooling Engineer

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[RAG.md]] · [[durum.md]] · [[../CONTEXT.md]]

---

## 1. Amaç

`bin/` dizini, CoreMusic ekosistemi için komut satırı (CLI) bakım, anahtar üretimi ve veritabanı koruma scriptlerini barındırır. Üretim ve bakım araçlarının proje köküne saçılmasını önler.

| Dosya | Amaç & İşlev | Kaynak |
|---|---|---|
| `api-key-create.php` | Sistemler arası veya harici API erişimleri için güvenli API anahtarı (key + secret hash) üretir. | `bin/api-key-create.php` |
| `db-access-guard.php` | Canlı veritabanı erişimlerini ve yetkilerini denetler, yetkisiz DDL veya tablo erişimlerini engeller. | `bin/db-access-guard.php` |
| `duration-backfill.php` | Medya dosyalarının süre (duration) metadata eksikliklerini katalog veritabanında toplu doldurur. | `bin/duration-backfill.php` |
| `durum.md` | Tek sayfalık CLI araçları çalışma ve oturum devir hafızası. | `bin/durum.md` |
| `RAG.md` | CLI araçları akıllı bilgi erişim yönlendiricisi. | `bin/RAG.md` |

---

## 2. Kapsam

- **Kapsam:** `bin/` altındaki PHP CLI scriptleri, parametreleri ve çalışma logları.
- **Kapsam Dışı:** Web sunucusu üzerinden doğrudan HTTP ile çalıştırılacak sayfalar (buradaki scriptler salt CLI / terminal içindir).
- **Kullananlar:** DevOps Engineer, Database Engineer, Backend Architect.

---

*bin/ Context v1.0.0 — CoreMusic Engineering Standard — 2026-11-01*
