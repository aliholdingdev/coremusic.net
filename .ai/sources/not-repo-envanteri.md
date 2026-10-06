---
title: "CoreMusic Repo Envanteri (Not)"
type: kaynak
raw_path: raw/not-repo-envanteri.md
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri]
ingested: 2026-10-06 18:02
tags: [repo, php, mimari, test]
---

# Kaynak Özeti — CoreMusic Repo Envanteri

## Genel Özet

Diskten okunmuş repo keşfi notu: `C:\www\coremusic.net` altında `shared/` + 5 alt alan adı modülü, PHP 8.4+/PDO/APCu omurgası, PHPUnit/PHPStan/Playwright/Vitest test üçlüsü.

## Ana Fikirler

1. 6 modüllü tek repo: `shared/` (coremusic/shared-infrastructure v2.0.0) + api, auth, home, media, assets.
2. Zorunlu: PHP >=8.4, ext-apcu, ext-pdo, ext-json, ext-mbstring; PSR-4 `CoreMusic\` → `src/`.
3. Test: phpunit ^10.5, phpstan ^1.10 (level 5), playwright ^1.62.1 (kök package.json), vitest (assets).
4. Lisans proprietary; composer scriptleri `test` ve `stan`.
5. Kişi verisi yok → kişiler bölümü boş kaldı (bilgi boşluğu).

## Önemli Alıntılar/Veriler

- `"name": "coremusic/shared-infrastructure"`, `"version": "2.0.0"`, `"license": "proprietary"`
- `"php": ">=8.4"`, `"ext-apcu": "*"`, `"ext-pdo": "*"`
- `"phpunit/phpunit": "^10.5"`, `"phpstan/phpstan": "^1.10"`, `"playwright": "^1.62.1"`

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

Araçlar: [[php-8-4]] · [[composer]] · [[phpunit]] · [[phpstan]] · [[pdo]] · [[apcu]] · [[playwright]] · [[vitest]]
Projeler: [[coremusic-platform]] · [[shared-infrastructure]] · [[home-coremusic-net]] · [[api-coremusic-net]] · [[auth-coremusic-net]] · [[media-coremusic-net]] · [[assets-coremusic-net]]
