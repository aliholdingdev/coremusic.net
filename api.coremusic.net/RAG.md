---
title: "api.coremusic.net — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: api.coremusic.net/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# api.coremusic.net — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `api.coremusic.net` alanında çalışan AI agent'larının veya geliştiricilerin REST API, controller, repository ve middleware hatlarına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/api.coremusic.net/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **API Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/api.coremusic.net/CONTEXT.md) | `openapi.yaml` |
| **Rotalar & Sürüm Çözümleme (V1/V2)** | `config/routes.php` | `include/Router/VersionResolver.php` |
| **DI Konteynerleri (V1 & V2 Paralel)** | `include/Container/V1Container.php` | `include/Container/V2Container.php` |
| **Controller'lar (Billing, Music, User)** | `include/Controller/` | `include/Service/` |
| **Repository Mimarisi (I* & Final Class)** | `include/Repository/` | `shared/src/Database/ConnectionFactory.php` |
| **Servis Katmanı & DTO Dönüşümü** | `include/Service/` | `shared/src/Api/Dto/` |
| **Birim ve Entegrasyon Testleri** | `tests/Unit/` | `phpunit.xml` |

---

## 🎯 Keyword & Arama Etiketleri

`api`, `rest`, `routing`, `version-resolver`, `v1`, `v2`, `v2-container`, `billing-controller`, `billing-repository`, `repository-pattern`, `adr-113`, `adr-114`, `raw-pdo`, `openapi`

---

## ⚠️ Kritik Alan Kısıtları

1. **API SÜRÜMLEME KURALI (ADR-113):** V1 ve V2 ayrık rota tablolarında değil, tek route kaydında `/api/{version}/...` ile yönetilir. Davranış farkı `V2Container` override ile yapılır.
2. **REPOSITORY STANDARDI (ADR-114):** Yeni repository'ler `I*` interface + concrete `final class` + raw prepared statement ile yazılır (`SELECT *` ve ORM kesinlikle yasaktır).
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*api.coremusic.net RAG Router v1.0.0 — SSOT: [`api.coremusic.net/CONTEXT.md`](file:///c:/www/coremusic.net/api.coremusic.net/CONTEXT.md)*
