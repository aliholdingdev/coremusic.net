---
title: "shared — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: shared/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# shared — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `shared` çekirdek kütüphanesinde çalışan AI agent'larının veya geliştiricilerin veritabanı bağlantı fabrikasına, middleware hattına, SPA sayfa router'ına ve ortak servis katmanına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/shared/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Çekirdek Alan Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/shared/CONTEXT.md) | `composer.json` |
| **PDO Veritabanı Bağlantı Fabrikası** | `src/Database/ConnectionFactory.php` | `config/domain.php` |
| **CSRF Koruması & Middleware** | `src/Middleware/CsrfMiddleware.php` | `src/Middleware/SecurityHeadersMiddleware.php` |
| **Kimlik Doğrulama Middleware Hattı** | `src/Api/Middleware/AuthenticationMiddleware.php` | `src/Middleware/` |
| **Ortak Rota Tablosu & Gateway Yönlendirme** | `src/Api/Routing/RouteTable.php` | `config/routes.php` |
| **SPA Sayfa Render & Shell Yayımı** | `src/PageRouter/HtmlShellRenderer.php` | `src/PageRouter/ResponseEmitter.php` |
| **Domain & Cihaz Yönetimi** | `src/Config/DomainConfig.php` | `src/Device/DeviceManager.php` |
| **Birim ve Entegrasyon Testleri (PHPUnit)** | `tests/` | `phpunit.xml` |

---

## 🎯 Keyword & Arama Etiketleri

`shared`, `connection-factory`, `raw-pdo`, `csrf-middleware`, `security-headers`, `route-table`, `html-shell-renderer`, `domain-config`, `device-manager`, `middleware-pipeline`

---

## ⚠️ Kritik Alan Kısıtları

1. **RAW PDO & ORM YASAĞI (ADR-002):** Parametreli raw PDO kullanılır; Eloquent, Doctrine gibi ORM'ler ve `SELECT *` sorguları kesinlikle yasaktır.
2. **TOKEN VE GÜVENLİK (`ADR-010`):** CSRF token adı daima `csrf_token`dır. Güvenlik başlıkları (CSP, HSTS) zorunludur.
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*shared RAG Router v1.0.0 — SSOT: [`shared/CONTEXT.md`](file:///c:/www/coremusic.net/shared/CONTEXT.md)*
