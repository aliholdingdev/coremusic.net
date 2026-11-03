---
title: "auth.coremusic.net — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: auth.coremusic.net/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# auth.coremusic.net — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `auth.coremusic.net` alanında çalışan AI agent'larının veya geliştiricilerin kimlik doğrulama, JWT, CSRF, oturum güvenliği ve RBAC mekanizmalarına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/auth.coremusic.net/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Kimlik Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/auth.coremusic.net/CONTEXT.md) | `composer.json` |
| **Giriş, Kayıt & Oturum Controller** | `include/Controller/AuthController.php` | `include/Container/AuthContainer.php` |
| **Kimlik Doğrulama Servis Katmanı** | `include/Service/AuthService.php` | `include/Service/` |
| **Kullanıcı Deposu (User Repository)** | `include/Repository/UserRepository.php` | `shared/src/Database/ConnectionFactory.php` |
| **Oturum Tespiti & Fixation Koruması** | `tests/Unit/Controller/AuthControllerSessionFixationTest.php` | `shared/src/Middleware/SecurityHeadersMiddleware.php` |
| **Yönlendirme İzin Listesi (Redirect Allowlist)** | `tests/Unit/Controller/AuthControllerRedirectAllowlistTest.php` | `include/Controller/AuthController.php` |
| **Birim ve Güvenlik Testleri (PHPUnit)** | `tests/Unit/` | `phpunit.xml` |

---

## 🎯 Keyword & Arama Etiketleri

`auth`, `login`, `register`, `logout`, `session`, `session-fixation`, `redirect-allowlist`, `auth-service`, `user-repository`, `csrf_token`, `security-headers`, `jwt`, `rbac`

---

## ⚠️ Kritik Alan Kısıtları

1. **STANDART RBAC ROLLERİ (`HL-09`):** Yetkilendirme sözlüğü dışı rol uydurulamaz; tüm roller standart RBAC tablosuna uygun olmalıdır.
2. **CSRF VE SESSION FIXATION (`HL-07`, `ADR-010`):** Oturum geçişlerinde session yenilenir, token ismi daima `csrf_token` olarak kullanılır (`_csrf_token` yasaktır).
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*auth.coremusic.net RAG Router v1.0.0 — SSOT: [`auth.coremusic.net/CONTEXT.md`](file:///c:/www/coremusic.net/auth.coremusic.net/CONTEXT.md)*
