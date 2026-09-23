---
title: "CoreMusic — Config Analiz Raporu"
type: report
category: code-analysis
date: 2026-09-23
status: active
version: 1.0.0
---

# CoreMusic — Config Analiz Raporu

**Kapsam:** `home.coremusic.net/config/` dizinindeki 5 dosya  
**Tarih:** 2026-09-23

---

## 1. Config Dosya Zinciri (Bootstrap Chain)

```
index.php
  ├── autoload.php          (PSR-4)
  ├── config/constants.php  ← .env okur, define() ile sabitleri oluşturur
  ├── config/app.php        ← Sabitlerden config array'i döndürür
  ├── config/config.php     ← ConfigManager + DomainConfig objeleri üretir
  └── config/bootstrap.php  ← DI container + routing + auth callback
```

**Sonuç:** 5 dosyanın tamamı kullanılıyor. Hiçbiri "kullanılmıyor" değil.

---

## 2. Sabit (Constant) Kullanım Analizi

### ✅ Kullanılan Sabitler (12/18)

| Sabit | Tanımlandığı Yer | Kullanıldığı Yer | Durum |
|-------|-------------------|------------------|-------|
| `APP_ENV_MODE` | constants.php:21 | constants.php (kendi kontrolü) | ✅ |
| `DEBUG_MODE` | constants.php:28 | RuntimeBootstrap::boot() | ✅ |
| `APP_NAME` | constants.php:29 | app.php → ConfigManager | ✅ |
| `APP_VERSION` | constants.php:30 | app.php, bootstrap.php, health.php | ✅ |
| `APP_TIMEZONE` | constants.php:31 | app.php → ConfigManager | ✅ |
| `SESSION_NAME` | constants.php:51 | app.php, HomeContainer.php | ✅ |
| `CSRF_TOKEN_LENGTH` | constants.php:52 | app.php → Security config | ✅ |
| `RATE_LIMIT_MAX` | constants.php:53 | app.php → Security config | ✅ |
| `RATE_LIMIT_WINDOW` | constants.php:54 | app.php → Security config | ✅ |
| `AUTH_URL` | constants.php:72 | HomeAuthBridge.php, HomeContainer.php | ✅ |
| `ASSETS_URL` | constants.php:74 | header.php, footer.php, AbstractComponent.php, HomeSongButton.php | ✅ |
| `APP_PEPPER` | constants.php:89 | HomeAuthBridge.php (auth_key validate) | ✅ |

### ❌ Kullanılmayan Sabitler (6/18)

| Sabit | Tanımlandığı Yer | Kullanıldığı Yer | Durum |
|-------|-------------------|------------------|-------|
| `TEST_MODE` | constants.php:36 | Sadece app.php'de config array'e yazılıyor, hiçbir yerde okunmuyor | ❌ Kullanılmıyor |
| `FORCE_AUTH_BYPASS` | constants.php:37 | Sadece app.php'de config array'e yazılıyor, hiçbir yerde okunmuyor | ❌ Kullanılmıyor |
| `DEFAULT_PAGE` | constants.php:46 | Hiçbir yerde Referans yok | ❌ Kullanılmıyor |
| `TRUSTED_PROXIES` | constants.php:67 | Hiçbir yerde Referans yok | ❌ Kullanılmıyor |
| `MUSIC_URL` | constants.php:73 | Hiçbir yerde Referans yok | ❌ Kullanılmıyor |
| `ROOT_PATH` | constants.php:42 | Sadece constants.php içinde (PAGES_PATH, HEADER_PATH, FOOTER_PATH için) | ⚠️ Dolaylı |

### ⚠️ Veritabanı Sabitleri (Tanımlı Ama Doğrudan Kullanılmayan)

| Sabit | Tanımlandığı Yer | Durum |
|-------|-------------------|-------|
| `DB_HOST` | constants.php:79 | ⚠️ HomeContainer'da kullanılmıyor (muhtemelen shared'de kullanılıyor) |
| `DB_HOME_NAME` | constants.php:80 | ⚠️ Aynı |
| `DB_USER` | constants.php:81 | ⚠️ Aynı |
| `DB_PASSWORD` | constants.php:82 | ⚠️ Aynı |
| `DB_PORT` | constants.php:83 | ⚠️ Aynı |
| `DB_CHARSET` | constants.php:84 | ⚠️ Aynı |

**Not:** DB sabitleri `shared/src/Database/` tarafında kullanılabilir — home.coremusic.net tarafında doğrudan Referans yok.

---

## 3. .env Kullanım Analizi

| .env Anahtarı | Sabit Karşılığı | Durum |
|---------------|-----------------|-------|
| `APP_ENV_MODE` | `APP_ENV_MODE` | ✅ |
| `APP_NAME` | `APP_NAME` | ✅ |
| `APP_VERSION` | `APP_VERSION` | ✅ |
| `APP_TIMEZONE` | `APP_TIMEZONE` | ✅ |
| `TEST_MODE` | `TEST_MODE` | ❌ Sabit kullanılmıyor |
| `FORCE_AUTH_BYPASS` | `FORCE_AUTH_BYPASS` | ❌ Sabit kullanılmıyor |
| `SESSION_NAME` | `SESSION_NAME` | ✅ |
| `CSRF_TOKEN_LENGTH` | `CSRF_TOKEN_LENGTH` | ✅ |
| `RATE_LIMIT_MAX` | `RATE_LIMIT_MAX` | ✅ |
| `RATE_LIMIT_WINDOW` | `RATE_LIMIT_WINDOW` | ✅ |
| `DEFAULT_PAGE` | `DEFAULT_PAGE` | ❌ Sabit kullanılmıyor |
| `AUTH_URL` | `AUTH_URL` | ✅ |
| `MUSIC_URL` | `MUSIC_URL` | ❌ Sabit kullanılmıyor |
| `DB_HOST` | `DB_HOST` | ⚠️ home tarafında doğrudan kullanılmıyor |
| `DB_HOME_NAME` | `DB_HOME_NAME` | ⚠️ Aynı |
| `DB_USER` | `DB_USER` | ⚠️ Aynı |
| `DB_PASSWORD` | `DB_PASSWORD` | ⚠️ Aynı |
| `DB_PORT` | `DB_PORT` | ⚠️ Aynı |
| `DB_CHARSET` | `DB_CHARSET` | ⚠️ Aynı |
| `APP_PEPPER` | `APP_PEPPER` | ✅ |

---

## 4. Öneri

### Silinebilir (constants.php + .env)

| # | Sabit | .env Satırı | Gerekçe |
|---|-------|-------------|---------|
| 1 | `DEFAULT_PAGE` | Satır 13 | Hiçbir yerde kullanılmıyor |
| 2 | `MUSIC_URL` | Satır 16 | Hiçbir yerde kullanılmıyor |
| 3 | `TRUSTED_PROXIES` | Yok (hardcoded) | Hiçbir yerde kullanılmıyor |

### Korunabilir (Ama İzlenmeli)

| # | Sabit | Gerekçe |
|---|-------|---------|
| 1 | `TEST_MODE` | Gelecekte test ortamı için gerekli olabilir |
| 2 | `FORCE_AUTH_BYPASS` | Geliştirme ortamında auth bypass için kullanılabilir |
| 3 | `DB_*` (6 sabit) | shared/src/Database/ tarafında kullanılabilir |

---

## 5. Sonuç

| Kategori | Sayı |
|----------|------|
| Config dosyası | 5 (tamamı kullanılıyor) |
| Kullanılan sabit | 12 |
| Kullanılmayan sabit | 3 (DEFAULT_PAGE, MUSIC_URL, TRUSTED_PROXIES) |
| Dolaylı/izlenmeli | 6 (TEST_MODE, FORCE_AUTH_BYPASS, DB_*) |
| .env satırı | 20 |

**Config yapısı sağlam.** Kullanılmayan 3 sabit var ama bunlar zararsız — sadece gereksiz kod kalabalığı yaratıyor.

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-09-23
