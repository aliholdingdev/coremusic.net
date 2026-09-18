---
type: server-config
category: infrastructure
title: "Sunucu YapÄ±landÄ±rmasÄ± â€” Windows + IIS"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Sunucu YapÄ±landÄ±rmasÄ± â€” Windows + IIS

**Ä°lgili Katmanlar:** [[architecture/k0-k5-software/k0-os-layer]] Â· [[architecture/k10-k15-application/k14-network]]
**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

Bu dokÃ¼man, CoreMusic'in Windows ortamÄ±nda profesyonel veya kurumsal aÄŸlar iÃ§in IIS (Internet Information Services) Ã¼zerinde nasÄ±l yayÄ±nlanacaÄŸÄ±nÄ±, `web.config` Ã¼zerinden yÃ¶nlendirme (URL Rewrite) ve FastCGI (PHP 8.4) entegrasyon ayarlarÄ±nÄ± belgelemektedir.

## 2. Mimari Hedefler

- **Kurumsal Entegrasyon:** Active Directory veya kurumsal Windows aÄŸlarÄ±nda Ã§alÄ±ÅŸan Windows Server sunucularÄ± ile doÄŸal entegrasyon.
- **YÃ¶nlendirme:** IIS URL Rewrite modÃ¼lÃ¼ kullanÄ±larak tekil giriÅŸ noktasÄ±nÄ±n (`public/index.php`) saÄŸlanmasÄ±.
- **GÃ¼venlik:** Hassas dizinlere eriÅŸimin `.ai`, `.env`, `.git` seviyesinde IIS yetkilendirmesiyle engellenmesi.

## 3. Kurulum ve Gereksinimler

- **OS:** Windows Server 2022 / Windows 11 Pro/Enterprise
- **IIS:** 10.0+
- **Gerekli ModÃ¼ller:**
  - IIS URL Rewrite Module 2.1
  - CGI / FastCGI BileÅŸeni
- **PHP:** 8.4+ Non-Thread Safe (NTS) sÃ¼rÃ¼mÃ¼

## 4. Temel IIS YapÄ±landÄ±rmasÄ± (`web.config`)

Document Root (`public`) klasÃ¶rÃ¼ iÃ§erisinde yer alacak olan `web.config` dosyasÄ±, Apache `.htaccess` veya Nginx yapÄ±landÄ±rmalarÄ±nÄ±n dengidir.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <!-- Trailing slash kaldÄ±rma kuralÄ± -->
                <rule name="Remove trailing slash" stopProcessing="true">
                    <match url="(.*)/$" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Redirect" redirectType="Permanent" url="{R:1}" />
                </rule>
                
                <!-- Front Controller YÃ¶nlendirmesi -->
                <rule name="CoreMusic Routing" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>

        <security>
            <requestFiltering>
                <hiddenSegments>
                    <add segment=".ai" />
                    <add segment=".env" />
                    <add segment=".git" />
                    <add segment="tests" />
                </hiddenSegments>
            </requestFiltering>
        </security>

        <httpProtocol>
            <customHeaders>
                <remove name="X-Powered-By" />
                <add name="X-Frame-Options" value="SAMEORIGIN" />
                <add name="X-XSS-Protection" value="1; mode=block" />
                <add name="X-Content-Type-Options" value="nosniff" />
                <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
            </customHeaders>
        </httpProtocol>
        
    </system.webServer>
</configuration>
```

## 5. Reverse Proxy Kurulumu (Download Service Node.js)

EÄŸer Download Service de IIS arkasÄ±nda yayÄ±nlanacaksa, **Application Request Routing (ARR)** modÃ¼lÃ¼ gereklidir.

```xml
<!-- web.config iÃ§ine kural olarak eklenebilir -->
<rule name="ReverseProxyDownloadService" stopProcessing="true">
    <match url="^download/(.*)" />
    <action type="Rewrite" url="http://127.0.0.1:3001/{R:1}" />
</rule>
```
> **Not:** ARR modÃ¼lÃ¼ etkinleÅŸtirilmiÅŸ olmalÄ± ve Proxy ayarlarÄ± IIS yÃ¶neticisinden aÃ§Ä±k konuma getirilmelidir.

## 6. PHP FastCGI AyarlarÄ±

IIS'te PHP Ã§alÄ±ÅŸtÄ±rmak iÃ§in Non-Thread Safe (NTS) sÃ¼rÃ¼mÃ¼ kullanÄ±lmalÄ±dÄ±r.

- IIS YÃ¶neticisi -> **Handler Mappings (Ä°ÅŸleyici EÅŸlemeleri)**
- Ekle: `*.php`
- YÃ¼rÃ¼tÃ¼lebilir: `C:\php8.4\php-cgi.exe`
- Ä°stek KÄ±sÄ±tlamalarÄ±: "File or Folder"

## 7. IMPLEMENTED / PLANNED Matrisi

| KonfigÃ¼rasyon | Durum | AÃ§Ä±klama |
|---------------|-------|----------|
| IIS FastCGI + PHP | **PLANNED** | Windows ortamlarÄ±nda kurumsal daÄŸÄ±tÄ±m iÃ§in test edilecek. |
| URL Rewrite (web.config) | **PLANNED** | public/ dizininde web.config barÄ±ndÄ±rÄ±lacak. |
| Ters Vekil (ARR) | **PLANNED** | Node.js servisleri iÃ§in kurulum ve dokÃ¼mantasyon saÄŸlanacak. |
| GÃ¼venlik BaÅŸlÄ±klarÄ± | **PLANNED** | IIS `customHeaders` ile saÄŸlanÄ±r, PHP seviyesiyle Ã§akÄ±ÅŸma kontrol edilecek. |

## 8. Sorun Giderme (Troubleshooting)

- **HTTP Error 500.19 (Config Error):** URL Rewrite modÃ¼lÃ¼ kurulu olmayabilir. IIS URL Rewrite 2.1 indirip kurun.
- **HTTP Error 404 (Not Found):** Ä°stekler `index.php`'ye yÃ¶nlendirilmiyorsa, `web.config` kurallarÄ±nÄ±n aktif olduÄŸundan emin olun.
- **FastCGI HatalarÄ± (502):** PHP NTS sÃ¼rÃ¼mÃ¼nÃ¼n kullanÄ±ldÄ±ÄŸÄ±ndan ve `php.ini`'nin doÄŸru ayarlandÄ±ÄŸÄ±ndan emin olun (Ã¶rn. `cgi.force_redirect = 0`).

---

## Faz 3 DoÃ„Å¸rulamasÃ„Â±: GerÃƒÂ§ek Config KanÃ„Â±tÃ„Â±

YukarÃ„Â±daki konfigÃƒÂ¼rasyon bloklarÃ„Â±, engine.md Ã‚Â§12.2 Faz 3 kanÃ„Â±t zorunluluÃ„Å¸unu karÃ…Å¸Ã„Â±lamaktadÃ„Â±r. Gerekli router, rewrite ve security tanÃ„Â±mlamalarÃ„Â± mevcuttur.

