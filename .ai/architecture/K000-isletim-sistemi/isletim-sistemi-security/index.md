---
type: architecture
category: layer-subindex
title: "K000 · isletim-sistemi-security — OS/Zemin Güvenliği"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# isletim-sistemi-security — OS/Zemin Güvenliği

> Üst indeks: `[[../index]]` · Makro: `[[../os-master]]` · **Politika sahibi: K006 (Güvenlik)** — bu dosya yalnız K000 zemin yüzeyini listeler.

## §1 Kapsam
İşletim sistemi / sunucu zeminindeki güvenlik yüzeyi: erişim kuralları, header zemini, env sızdırma riski.
Uygulama seviyeli auth/CSRF/rate-limit **K006**'dadır; çakışma olursa K006 kazanır (matris §1).

## §2 Disk Kanıtları

| Yüzey | Kanıt | Not |
|---|---|---|
| Erişim kuralları | 4 subdomain × `.{htaccess,web.config}` = 8 dosya | sunucu-seviye rewrite/deny — içerikleri okunmadı → `UNKNOWN` |
| Header middleware | `shared/src/Middleware/SecurityHeadersMiddleware.php` | dosya VAR; satır içeriği okunmadı → `⚠️ VERIFICATION REQUIRED` |
| CORS/Origin | `shared/src/Api/Middleware/CorsMiddleware.php` · `OriginCheck.php` | dosya VAR |
| Env sızıntısı | `shared/src/Config/EnvParser.php` | `.env` doğrudan sunulmaz; env dosyasının web-root dışında olduğu **doğrulanmadı** → UNKNOWN |
| Statik asset | `assets.coremusic.net/.htaccess` | statik servis — execute yasağı var mı bilinmiyor → UNKNOWN |

## §3 Bağımlılık
- **Alt:** `isletim-sistemi-core`.
- **Üst:** `isletim-sistemi-security` → K006 (tam politika), K014 (ağ/deploy).

## §4 Durum
**PARTIAL** — dosyalar diskte var; davranış doğrulaması (header değerleri, deny kuralları) bu görevde yapılmadı.

## §5 Risk
- Zemin-güvenlik davranışının K006 ile mükerrer yazılması riski → bu dosya **envanter** tutar, kural **yazmaz**.
