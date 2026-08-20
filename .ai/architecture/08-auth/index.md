---
type: architecture
category: auth
title: "Enterprise Auth Architecture — Index"
date: 2026-08-09
updated: 2026-08-12
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth Architecture — Index

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

CoreMusic Auth sisteminin enterprise seviyedeki mimari yapısını tanımlar. Bu dizin, **Identity Provider** ve **Security Gateway** olarak görev yapan merkezi auth servisinin tüm bileşenlerini kapsar.

## 2. Auth Sistemi Nedir?

CoreMusic AUTH, sıradan bir giriş ekranı veya basit bir üyelik tablosu değil; tüm ekosistemin güvenliğini, kimlik yönetimini ve yetkilendirme süreçlerini tek noktadan kontrol eden, kurumsal (enterprise) standartlarda kurgulanmış merkezi bir **Security Gateway** ve **Identity Provider** platformudur.

**Projenin kalbi niteliğindeki bu servis, 50 yıllık bir mühendislik vizyonuyla, hataya tahammülü olmayan kritik bir altyapı olarak tasarlanmıştır.**

## 3. Merkezi Otorite İlkesi

Ekosistem içerisinde yer alan home, pro, studio, car, media gibi hiçbir subdomain, kendi başına bağımsız bir kimlik doğrulama mantığı (login sistemi) taşımaz. Sisteme giriş çıkışlar, şifre sıfırlamalar ve yetki atamaları tamamen **auth.coremusic.net** üzerinden yürütülür.

**Single Sign-On (SSO)** mimarisi kurularak, kullanıcının bir kez giriş yaptığında tüm CoreMusic platformlarında yetkileri dahilinde pürüzsüzce dolaşabilmesi sağlanır.

## 4. Dosya Yapısı

```
08-auth/
├── index.md                    ← Bu dosya (index)
├── auth-domain.md              ← Auth domain entities
├── auth-application.md         ← Auth use cases
├── auth-infrastructure.md      ← Auth persistence & security
├── auth-api.md                 ← Auth API endpoints
├── auth-flow.md                ← Authentication lifecycle
├── auth-cross-domain.md        ← Cross-subdomain authentication
├── auth-embedded.md            ← RPi5 embedded auth (YENİ)
└── auth-media-security.md      ← Media vault security
```

## 5. Hızlı Referans

| İhtiyaç | İlk Adım |
|---------|----------|
| Auth domain tasarımı | [[auth-domain]] |
| Use case'ler | [[auth-application]] |
| Persistence & Security | [[auth-infrastructure]] |
| API endpoint'leri | [[auth-api]] |
| Auth akışı | [[auth-flow]] |
| Cross-domain | [[auth-cross-domain]] |
| Embedded auth (RPi5) | [[auth-embedded]] |
| Media güvenliği | [[auth-media-security]] |

## 6. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Auth | [[architecture/00-overview/architecture-master]] | Enterprise Auth Architecture |
| § 3 Merkezi Otorite | [[architecture/07-security/middleware-security]] | Middleware Pipeline |
| § 3 SSO | [[architecture/07-security/session-management]] | Session Management |
| § 3 Otorite | [[architecture/l2-routing/subdomain-routing]] | Subdomain Routing |
| § 4 Embedded | [[auth-embedded]] | Embedded Auth Detayları |

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 7 |
| Files | 8 |
| Cross-References | 4 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
