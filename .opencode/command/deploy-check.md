---
description: "Deployment oncesi kontrol listesi,环境 dogrulama, production hazirlik"
agent: build
---

# Deploy Check Komutu

Deployment oncesi kontrol listesini calistirir.

## Nasil Calisir?

1. Environment dogrulama
2. Config kontrolu
3. Database migration kontrolu
4. Security scan
5. Performance baseline
6. Rollback plani hazirla

## Kullanim

```
/deploy-check [environment]
```

## Environment Tipleri

| Environment | URL | Amaç |
|-------------|-----|-------|
| development | localhost | Gelistirme |
| staging | staging.coremusic.net | Test |
| production | music.coremusic.net | Canli |

## Kontrol Listesi

```
ON HAZIRLIK:
- [ ] Tum testler gecti
- [ ] Coverage %80 uzerinde
- [ ] Security audit temiz
- [ ] Code review tamam
- [ ] ADR olusturuldu (mimari karar varsa)

ENVIRONMENT:
- [ ] PHP 8.4 versiyonu dogru
- [ ] MySQL 8 calisiyor
- [ ] Extensions mevcut (mbstring, openssl, pdo_mysql)
- [ ] OPcache aktif
- [ ] APCu aktif

CONFIG:
- [ ] .env dosyasi guncel
- [ ] Database baglantisi dogru
- [ ] Cache driver dogru
- [ ] Session ayarlari dogru
- [ ] CSP nonce uretimi calisiyor

DATABASE:
- [ ] Migration'lar uygulandi
- [ ] Seed'ler calisti
- [ ] Index'ler olusturuldu
- [ ] Foreign key'ler tanimli

SECURITY:
- [ ] CSRF token aktif
- [ ] Rate limiting aktif
- [ ] Security headers mevcut
- [ ] HTTPS aktif
- [ ] HSTS aktif

PERFORMANCE:
- [ ] Page load < 3s
- [ ] API response < 200ms
- [ ] Cache hit rate > 80%
- [ ] Memory usage < 256MB

ROLLBACK:
- [ ] Rollback plani hazir
- [ ] Database backup alindi
- [ ] File backup alindi
- [ ] Rollback test edildi
```

## Vault Context

- `.ai/architecture/01-overview/startup-strategy.md` — Deployment stratejisi
- `.ai/workflows/deployment.md` — Deployment workflow
- `.claude/rules/core-rules.md`

## Red Team Kontrolu

- All tests passing?
- Security scan clean?
- Performance baseline met?
- Rollback plan tested?
