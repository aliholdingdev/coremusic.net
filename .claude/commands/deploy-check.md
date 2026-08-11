# Deploy Check

Deployment öncesi kontrol listesini çalıştır.

## Kontrol Listesi

```
□ Testler geçti mi? (unit + integration + E2E)
□ Coverage ≥%80 mi?
□ Security audit temiz mi?
□ Vault-sync yapıldı mı?
□ Hard Gate onayı var mı?
□ Database migration hazır mı?
□ Rollback planı var mı?
□ Health check endpoint çalışıyor mu?
□ Monitoring aktif mi?
□ Loglama aktif mi?
```

## Environment Tipleri

| Environment | URL | Amaç |
|-------------|-----|-------|
| development | localhost | Geliştirme |
| staging | staging.coremusic.net | Test |
| production | music.coremusic.net | Üretim |

## Pre-Flight Kontroller

| Kontrol | Yöntem | Kriter |
|---------|--------|--------|
| PHP syntax | `php -l` | 0 hata |
| PHPUnit | `vendor/bin/phpunit` | 0 failure |
| Vitest | `npx vitest run` | 0 failure |
| Playwright | `npx playwright test` | 0 failure |
| GitLeaks | `gitleaks detect` | 0 secret |
| DB migration | `php bin/migrate status` | Pending yok |

## Post-Flight Kontroller

| Kontrol | Yöntem | Kriter |
|---------|--------|--------|
| Health check | `curl /health` | 200 OK |
| Auth test | Login/logout döngüsü | Başarılı |
| API test | Tüm endpoint'ler | 200/4xx |
| Performance | TTFB <200ms | Geçti |
| Error rate | Log'da ERROR | 0 yeni |
