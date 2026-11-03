---
title: "admin.coremusic.net — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: admin.coremusic.net/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# admin.coremusic.net — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `admin.coremusic.net` alanında çalışan AI agent'larının veya geliştiricilerin yönetim paneli, yetkilendirme geçitleri, sistem denetimi ve yönetim rotalarına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/admin.coremusic.net/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Yönetim Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/admin.coremusic.net/CONTEXT.md) | `composer.json` |
| **Geliştirici Giriş Geçidi (DevLoginGate)** | `include/Routing/DevLoginGate.php` | `config/bootstrap.php` |
| **Yönetim Paneli Şablon & Arayüzleri** | `pages/` | `assets.coremusic.net/Css/` |
| **Yönetim Yetkilendirme & RBAC Hatları** | `include/` | `auth.coremusic.net/include/` |
| **Birim ve Geçit Testleri (PHPUnit)** | `tests/Unit/DevLoginGateTest.php` | `phpunit.xml` |

---

## 🎯 Keyword & Arama Etiketleri

`admin`, `management`, `dev-login-gate`, `rbac`, `admin-panel`, `superadmin`, `system-audit`, `light-mode`, `php-admin`

---

## ⚠️ Kritik Alan Kısıtları

1. **YALNIZCA LIGHT MODE (`HL-01`):** Yönetim paneli arayüzleri istisnasız Light Mode'dur; dark mode varsayımı veya CSS dark class'ı yasaktır.
2. **DEV GİRİŞ GÜVENLİK KORUMASI:** Canlı ortamda `DevLoginGate` bypass edilemez; production ortamında sert kilitli kalır.
3. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*admin.coremusic.net RAG Router v1.0.0 — SSOT: [`admin.coremusic.net/CONTEXT.md`](file:///c:/www/coremusic.net/admin.coremusic.net/CONTEXT.md)*
