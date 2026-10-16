---
title: "K011-application / caching — Bölüm Dizini"
type: architecture-index
kodeme: kademe-2
module: K011-application
parent: caching
status: active
updated: 2026-10-15
group: mimari
---

# caching — Bölüm Dizini

> **Kapsam:** Uygulama-seviyesi cache katmanı (PSR-16 benzeri arayüz + backends) ve
> HTTP cache başlıkları. Anasayfa: `K011-application/index.md`. Ana dosya:
> `caching-uygulama-seviyesi-cache.md` (kapsam özeti — bu dizinde "kapsam" rolünü oynar).
>
> **Kaynak canavarı:** Kod kanıtı repo içinden okunur (`Dosya: <path> · Satır: <n>`).
> Standart/dokümantasyon iddiaları yalnızca §9 havuzundaki URL'lerden; her URL bu
> dizinde **tek dosyada** kullanılır (cross-folder tekrar YASAK — spec §1.6).
> Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`, eksik kod PLANNED boşluk olarak
> işaretlenir; uydurma kod/satır YASAK (zero-hallucination).

## Dosyalar

| Dosya | Rol | Grup |
|---|---|---|
| `caching-uygulama-seviyesi-cache.md` | Ana kapsam — cache mimarisi özeti | mimari |
| `caching-dogrulama.md` | Doğrulama planı, test envanteri, PASS/BLOCK kapısı, Age başlığı | kanit |
| `caching-web-arastirma.md` | Havuz URL'leri (Expires, Last-Modified), claim↔repo matrisi | kanit |
| `caching-ornekler.md` | Repo örnekleri (PHP/JS/SQL/CSS) + If-None-Match URL | kod |
| `caching-bilesenler.md` | Bileşen envanteri, akış izleri, If-Modified-Since URL, boşluklar | mimari |

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | CacheManager + adapter kontratı (`shared/src/Cache/`) | mimari | Bileşen envanteri | `caching-bilesenler.md` |
| 2 | Backend envanteri: APCu, Memory, PageCache, RateLimiter | mimari | Akış izi | `caching-bilesenler.md` |
| 3 | SPA cache katmanı: `CacheLayer.js` TTL + LRU (`assets.coremusic.net`) | mimari | Bağımlılık grafiği | `caching-bilesenler.md` |
| 4 | Prefetch kuyruğu: `PrefetchManager.js` | mimari | Boşluk listesi | `caching-bilesenler.md` |
| 5 | HTTP cache başlığı doğrulama planı (ETag / Cache-Control / Age) | kanit | Test envanteri | `caching-dogrulama.md` |
| 6 | Negatif senaryolar (stale token, TTL yarışı, cache poisoning) | kanit | PASS/BLOCK kapısı | `caching-dogrulama.md` |
| 7 | Standart kanıtı: `Expires` + `Last-Modified` (§9 havuzu) | kanit | Web kaynağı | `caching-web-arastirma.md` |
| 8 | Claim↔repo matrisi (W-C1..W-C8) | kanit | Çapraz doğrulama | `caching-web-arastirma.md` |
| 9 | Reddedilen adaylar (LABEL) + PowerShell kesişim bloğu | kanit | Süreç kanıtı | `caching-web-arastirma.md` |
| 10 | PHP örneği: `CacheRateLimiter` + `ResponseNormalizationMiddleware` | kod | Örnek↔iddia köprüsü | `caching-ornekler.md` |
| 11 | JS örneği: `CacheLayer.js` TTL_MAP/LRU + `.htaccess` TTL'leri | kod | YAPMA↔YAP | `caching-ornekler.md` |
| 12 | SQL örneği: `rate_limits` tablosu (DB-backed rate limit kaydı) | kod | Kod kanıtı | `caching-ornekler.md` |
| 13 | CSS örneği: `auth-bundled.css?v=2.1.1` versiyonlama | kod | Kullanım | `caching-ornekler.md` |
| 14 | `If-Modified-Since` başlığı — SPA yeniden doğrulama | kanit | Web kaynağı | `caching-bilesenler.md` |

## Kullanım Sırası

1. Kapsam özeti → `caching-uygulama-seviyesi-cache.md`
2. Bileşen/akış haritası → `caching-bilesenler.md`
3. Doğrulama → `caching-dogrulama.md` (PASS/BLOCK kapısı)
4. Standart kanıtı → `caching-web-arastirma.md`
5. Kod örnekleri → `caching-ornekler.md`

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
