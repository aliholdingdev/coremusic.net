---
title: "bin/ — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: bin/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# bin/ — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `bin/` CLI ve operasyon dizininde çalışan AI agent'larının veya geliştiricilerin bakım scriptlerine ve anahtar üretim araçlarına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/bin/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **CLI Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/bin/CONTEXT.md) | `../CONTEXT.md` |
| **Güvenli API Anahtarı Üretimi** | `api-key-create.php` | `shared/src/Repository/ApiKeyRepository.php` |
| **Veritabanı Erişim & İzin Koruması** | `db-access-guard.php` | `shared/src/Database/ConnectionFactory.php` |
| **Şarkı Süresi Metadata Doldurma** | `duration-backfill.php` | `media.coremusic.net/src/Media/` |

---

## 🎯 Keyword & Arama Etiketleri

`bin`, `cli`, `api-key-create`, `db-access-guard`, `duration-backfill`, `cron`, `terminal-script`

---

## ⚠️ Kritik Alan Kısıtları

1. **YALNIZCA CLI ÇALIŞTIRMA:** Bu scriptler doğrudan web üzerinden (HTTP) çağrılamaz; CLI modunda (`php_sapi_name() === 'cli'`) çalışır.
2. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*bin/ RAG Router v1.0.0 — SSOT: [`bin/CONTEXT.md`](file:///c:/www/coremusic.net/bin/CONTEXT.md)*
