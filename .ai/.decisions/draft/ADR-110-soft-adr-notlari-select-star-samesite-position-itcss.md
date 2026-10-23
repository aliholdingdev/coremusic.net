---
id: ADR-110
title: "Soft-ADR Notları: SELECT * İstisnası, SameSite Lax, Position Mekanizması Serbestliği ve ITCSS 11-Katman SSOT"
status: draft
date: 2026-10-23
deciders: "Bayram Ali (onay) · MO (tespit)"
tags: [soft-adr, security, css, database, session]
related:
  - "[[accepted/ADR-002-database-pdo]]"
  - "[[accepted/ADR-047-frontend-architecture-mimari-design]]"
  - "[[accepted/ADR-100-frontend-security-owasp-hardening-2026]]"
  - "[[accepted/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu]]"
---

# ADR-110: Soft-ADR Notları

## Bağlam

2026-10-23 Prompt Arşivi Taraması (3 paralel keşif ajanı · 37 dosya) sırasında,
prompt'larda "hard gibi taşıyan" ama aslında müzakere edilebilir 6 kural
tespit edildi (Rapor S1–S6). Kullanıcı onayı ile 4'ü SOFT, 2'si HARD kaldı.
Bu ADR bu kararların tek kayıt noktasıdır.

## Kararlar

### S1 — `SELECT *` yasağı: SOFT istisna

- **Kural:** `SELECT *` genel yasak (ADR-002 kapsamı) **hard** kalır.
- **İstisna (SOFT):** Admin/konsol uçları (tek kullanıcı, düşük trafik,
  denetim amaçlı) `SELECT *` kullanabilir. Asıl tehlike liste uçlarındadır
  (band genişliği + N+1).
- **Kural:** İstisna yalnız `app/routes/admin/` altında; public/api
  uçlarında yasak pazarlık dışı.

### S2 — `SameSite=Strict` yerine `Lax` (SOFT)

- **Önceki iddia:** Bazı prompt'larda session cookie `SameSite=Strict`.
- **Karar:** Default **`Lax`**. Gerekçe: ADR-047 auth-bridge akışları
  (redirect ile gelen cross-site navigasyon) Strict'i kırar; Lax +
  CSRF token (ADR-100 OWASP hardening — ⚠️ VERIFICATION REQUIRED:
  CSRF-specific ADR dosyası diskte YOK) kombinasyonu OWASP yeterliliğini korur.
- **Strict kalacağı yer:** ADR-047 §2 "X-Frame-Options: DENY" ile
  birlikte frame-based CSRF yüzeyi; orada ek katman olarak `Strict`
  düşünülebilir (gelecek ADR).

### S3 — Playlist hard DELETE yasağı: HARD KALDI

- Prompt'lardaki `DELETE FROM playlist` yasağı aynen korunur.
- KVKK purge senaryosu ayrı bir ADR ile ele alınacak; bu ADR kapsamında değil.

### S4 — `<a href>` full-reload yasağı: HARD KALDI

- Vanilla JS SPA (ADR-001) içinde `<a href>` ile tam sayfa reload yasağı
  aynen korunur. OAuth redirect/logout istisnaları (ADR-043/047) mevcut
  akış zaten `window.location` ile; prompt'larda bu istisna açıkça yazılır.

### S5 — `position = MAX + 1` yasağı: mekanizma SERBEST (SOFT)

- **Önceki iddia:** Bazı prompt'larda pozisyon için tek mekanizma
  (ör. `MAX+1`) zorunlu.
- **Karar:** Mekanizma **serbest** (fractional ordering, offset, gap
  array — seçimi uygulamanın). **Hard kalan:** yarış koşulu (race
  condition) reddi — concurrent ekleme/taşımada veri bozulması kabul
  edilmez; optimistic locking veya transaction zorunlu.

### S6 — ITCSS katman sayısı: SSOT = css-template v4.0.0 / 11 katman

- **Çelişki:** `.ai/AGENTS.md` §4/§6'da "ITCSS 9-layer", SSOT
  şablonda (`.ai/.templates/frontend/css-template.md` v4.0.0) **11 katman**
  (01_Abstracts … 11_OAuth).
- **Karar:** SSOT **11-katman** (css-template v4.0.0 + AGENTS.md §10
  sıra kuralı). `.ai/AGENTS.md` §4/§6 satırları 2026-10-23'te
  "11-layer" olarak düzeltildi (bu ADR'nin uygulaması).
- **İhlal raporu:** 9-katman iddiası taşıyan prompt/vault dosyaları
  11-katman ile hizalanır.

## Sonuç

- Prompt arşivi yeniden yazımlarında bu 6 karar içerik olarak uygulanır.
- Hard kalanlar (S3, S4 + ADR-001/002/047/100/105) pazarlık dışıdır.
- `SELECT *` istisnası (S1) yalnız admin/konsol; genişletilmesi yeni ADR ister.

## Durum

**Draft** — accepted terfisi: sonraki vault-sync oturumunda, prompt arşivi
yeniden yazımı tamamlandıktan sonra değerlendirilecek.
