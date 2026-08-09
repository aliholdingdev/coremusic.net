# CoreMusic — PHP Extension Reference Guide

**PHP Version:** 8.5.8 NTS x64 VC17
**Extension Dir:** `C:\Php858\ext`
**php.ini:** `C:\Php858\php.ini`
**Last Updated:** 2026-08-08

---

## 1. Aktif Kurulum Durumu

| Extension | Versiyon | Durum | DLL |
|-----------|----------|-------|-----|
| redis | 6.3.0 | YUKLU | `php_redis.dll` |
| sqlsrv | 5.13.1 | YUKLU | `php_sqlsrv_85_nts.dll` |
| pdo_sqlsrv | 5.13.1 | YUKLU | `php_pdo_sqlsrv_85_nts.dll` |
| apcu | 5.1.28 | YUKLU | `php_apcu.dll` |

### Dogrulama

```powershell
php -m | Select-String "redis|sqlsrv|pdo_sqlsrv|apcu"
# apcu
# pdo_sqlsrv
# redis
# sqlsrv
```

### php.ini Satirlari

```ini
; === CoreMusic PHP Extensions ===
extension=php_redis.dll
extension=php_sqlsrv_85_nts.dll
extension=php_pdo_sqlsrv_85_nts.dll
extension=php_apcu.dll
```

---

## 2. phpredis Extension

### Download Links

#### phpredis 6.3.0 (Latest)

| PHP | Thread | Arch | PECL | GitHub |
|-----|--------|------|------|--------|
| 8.5 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0-php-8.5.0) |
| 8.5 | TS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0-php-8.5.0) |
| 8.4 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0rc1-php-8.4.14) |
| 8.3 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0rc1-php-8.3.27) |
| 8.2 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0rc1-php-8.2.29) |
| 8.1 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0rc1-php-8.1.33) |
| 8.0 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.3.0/windows) | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.3.0rc1-php-8.0.30) |

#### phpredis 6.2.0

| PHP | Thread | Arch | PECL | GitHub |
|-----|--------|------|------|--------|
| 8.5 | NTS | x64 | — | [GitHub](https://github.com/dk-sirk/phpredis-windows-release/releases/tag/php_redis-6.2.0-php-8.5.0) |
| 8.4 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.2.0/windows) | — |
| 8.3 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.2.0/windows) | — |
| 8.2 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.2.0/windows) | — |
| 8.1 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.2.0/windows) | — |
| 8.0 | NTS | x64 | [PECL](https://pecl.php.net/package/redis/6.2.0/windows) | — |

### Kurulum

```powershell
Invoke-WebRequest -Uri "https://github.com/dk-sirk/phpredis-windows-release/releases/download/php_redis-6.3.0-php-8.5.0/php_redis-6.3.0-php-8.5.0.7z" -OutFile "C:\temp\php_redis.7z"
& "C:\Program Files\7-Zip\7z.exe" x "C:\temp\php_redis.7z" -o"C:\temp\phpredis" -y
Copy-Item "C:\temp\phpredis\php_redis_nts.dll" "C:\Php858\ext\php_redis.dll"
```

---

## 3. MSSQL Server PHP Extensions (sqlsrv + pdo_sqlsrv)

### Download Links

#### Microsoft Drivers 5.13.1 (Latest)

| PHP | Thread | Arch | DLL Dosyalari | PECL |
|-----|--------|------|--------------|------|
| 8.5 | NTS | x64 | `php_sqlsrv_85_nts_x64.dll` / `php_pdo_sqlsrv_85_nts_x64.dll` | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.13.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.13.0/windows) |
| 8.5 | TS | x64 | `php_sqlsrv_85_ts_x64.dll` / `php_pdo_sqlsrv_85_ts_x64.dll` | [PECL](https://pecl.php.net/package/sqlsrv/5.13.0/windows) |
| 8.4 | NTS | x64 | `php_sqlsrv_84_nts_x64.dll` / `php_pdo_sqlsrv_84_nts_x64.dll` | [PECL](https://pecl.php.net/package/sqlsrv/5.13.0/windows) |
| 8.3 | NTS | x64 | `php_sqlsrv_83_nts_x64.dll` / `php_pdo_sqlsrv_83_nts_x64.dll` | [PECL](https://pecl.php.net/package/sqlsrv/5.13.0/windows) |

**Windows Indirme:**
- [Microsoft Resmi](https://go.microsoft.com/fwlink/?linkid=2362088)
- [GitHub v5.13.1](https://github.com/Microsoft/msphpsql/releases/tag/v5.13.1)

#### Microsoft Drivers 5.12

| PHP | Thread | Arch | PECL |
|-----|--------|------|------|
| 8.4 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.12.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.12.0/windows) |
| 8.3 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.12.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.12.0/windows) |
| 8.2 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.12.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.12.0/windows) |
| 8.1 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.12.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.12.0/windows) |

#### Microsoft Drivers 5.11

| PHP | Thread | Arch | PECL |
|-----|--------|------|------|
| 8.3 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.11.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.11.0/windows) |
| 8.2 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.11.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.11.0/windows) |
| 8.1 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.11.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.11.0/windows) |
| 8.0 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.11.0/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.11.0/windows) |

#### Microsoft Drivers 5.10

| PHP | Thread | Arch | PECL |
|-----|--------|------|------|
| 8.1 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.10.1/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.10.1/windows) |
| 8.0 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.10.1/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.10.1/windows) |
| 7.4 | NTS | x64 | [sqlsrv](https://pecl.php.net/package/sqlsrv/5.10.1/windows) / [pdo_sqlsrv](https://pecl.php.net/package/pdo_sqlsrv/5.10.1/windows) |

### Kurulum

```powershell
Invoke-WebRequest -Uri "https://github.com/microsoft/msphpsql/releases/download/v5.13.1/Windows_5.13.1RTW.zip" -OutFile "C:\temp\php_sqlsrv.zip"
Expand-Archive "C:\temp\php_sqlsrv.zip" -DestinationPath "C:\temp\mssql" -Force
Copy-Item "C:\temp\mssql\Windows\php_sqlsrv_85_nts_x64.dll" "C:\Php858\ext\php_sqlsrv_85_nts.dll"
Copy-Item "C:\temp\mssql\Windows\php_pdo_sqlsrv_85_nts_x64.dll" "C:\Php858\ext\php_pdo_sqlsrv_85_nts.dll"
```

### ODBC Driver Zorunluluğu

```powershell
Get-ItemProperty "HKLM:\SOFTWARE\ODBC\ODBCINST.INI\ODBC Drivers"
# ODBC Driver 17 for SQL Server : Installed
# ODBC Driver 18 for SQL Server : Installed
```

Indirme: [ODBC Driver](https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server)

---

## 4. APCu Extension (APC User Cache)

### Download Links

#### APCu 5.1.28 (Latest)

| PHP | Thread | Arch | PECL | Direct |
|-----|--------|------|------|--------|
| 8.5 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.5-nts-vs17-x64.zip) |
| 8.5 | TS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.5-ts-vs17-x64.zip) |
| 8.5 | NTS | x86 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.5-nts-vs17-x86.zip) |
| 8.5 | TS | x86 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.5-ts-vs17-x86.zip) |
| 8.4 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.4-nts-vs17-x64.zip) |
| 8.4 | TS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.4-ts-vs17-x64.zip) |
| 8.3 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.3-nts-vs16-x64.zip) |
| 8.3 | TS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.3-ts-vs16-x64.zip) |
| 8.2 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.2-nts-vs16-x64.zip) |
| 8.1 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.1-nts-vs16-x64.zip) |
| 8.0 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.28/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.0-nts-vs16-x64.zip) |

#### APCu 5.1.27

| PHP | Thread | Arch | PECL | Direct |
|-----|--------|------|------|--------|
| 8.5 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.27/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.27/php_apcu-5.1.27-8.5-nts-vs17-x64.zip) |
| 8.4 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.27/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.27/php_apcu-5.1.27-8.4-nts-vs17-x64.zip) |
| 8.3 | NTS | x64 | [PECL](https://pecl.php.net/package/APCu/5.1.27/windows) | [Download](https://downloads.php.net/~windows/pecl/releases/apcu/5.1.27/php_apcu-5.1.27-8.3-nts-vs16-x64.zip) |

### Kurulum

```powershell
Invoke-WebRequest -Uri "https://downloads.php.net/~windows/pecl/releases/apcu/5.1.28/php_apcu-5.1.28-8.5-nts-vs17-x64.zip" -OutFile "C:\temp\php_apcu.zip"
Expand-Archive "C:\temp\php_apcu.zip" -DestinationPath "C:\temp\php_apcu" -Force
Copy-Item "C:\temp\php_apcu\php_apcu.dll" "C:\Php858\ext\php_apcu.dll"
```

### php.ini

```ini
extension=php_apcu.dll

; OPCache + APCu entegrasyonu (opsiyonel)
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
```

### Dogrulama

```powershell
php -m | Select-String "apcu"
php -r "echo 'apcu: ' . (extension_loaded('apcu') ? 'OK v' . phpversion('apcu') : 'FAIL') . PHP_EOL;"
php --ri apcu
```

---

## 5. Redis Server

| Ozellik | Deger |
|---------|-------|
| Version | 8.8.0 (redis-windows-fork) |
| Port | 6379 |
| Maxmemory | 256 MB |
| Persistence | RDB |
| Baslangic | Otomatik (Startup VBS) |

### Kurulum

```powershell
winget install taizod1024.redis-windows-fork --accept-package-agreements --accept-source-agreements
```

### Baslatma

```powershell
redis-server --port 6379 --maxmemory 256mb --maxmemory-policy allkeys-lru
```

### PHP Redis Test

```php
<?php
$r = new Redis();
$r->connect('127.0.0.1', 6379);
echo $r->ping();  // 1
$r->set('key', 'value');
echo $r->get('key');  // value
```

---

## 6. Kaynak Linkleri

| Kaynak | URL |
|--------|-----|
| PECL | https://pecl.php.net |
| phpredis | https://github.com/phpredis/phpredis |
| phpredis Windows | https://github.com/dk-sirk/phpredis-windows-release |
| MSSQL PHP | https://github.com/Microsoft/msphpsql |
| MSSQL Download | https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server |
| APCu | https://pecl.php.net/package/APCu |
| APCu Windows | https://downloads.php.net/~windows/pecl/releases/apcu/ |
| Redis Windows | https://github.com/tporadowski/redis |
| ODBC Driver | https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server |
| PHP Windows | https://windows.php.net |
| VC++ Redist | https://aka.ms/vs/17/release/vc_redist.x64.exe |

---

## 7. Troubleshooting

| Sorun | Cozum |
|-------|-------|
| "Unable to load dynamic library" | NTS/TS uyumsuz — dogru thread modelini kullan |
| "The specified module could not be found" | VC++ Redistributable kurulu degil |
| sqlsrv connection fail | ODBC Driver kurulu degil |
| APCu cache calismadiyorsa | `apc.enabled=1` php.ini'de olmali |
| Redis connection refused | `redis-server` calismiyor |
| php.ini degismiyor | `php --ini` ile dogru dosyayi bul |
