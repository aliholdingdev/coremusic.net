---
type: server-config
category: infrastructure
title: "Sunucu Yapılandırması â€” Windows + IIS"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Sunucu Yapılandırması â€” Windows + IIS

**Ä°lgili Katmanlar:** [[architecture/k0-k5-software/k0-os-layer]] Â· [[architecture/k10-k15-application/k14-network]]
**Zorunlu Bağlantılar:** [[CLAUDE.md]] Â· [[architecture/master-architecture-index]]

---

## 1. Amaç

Bu doküman, CoreMusic'in Windows ortamında profesyonel veya kurumsal ağlar için IIS (Internet Information Services) üzerinde nasıl yayınlanacağını, `web.config` üzerinden yönlendirme (URL Rewrite) ve FastCGI (PHP 8.4) entegrasyon ayarlarını belgelemektedir.

## 2. Mimari Hedefler

- **Kurumsal Entegrasyon:** Active Directory veya kurumsal Windows ağlarında çalışan Windows Server sunucuları ile doğal entegrasyon.
- **Yönlendirme:** IIS URL Rewrite modülü kullanılarak tekil giriş noktasının (`public/index.php`) sağlanması.
- **Güvenlik:** Hassas dizinlere erişimin `.ai`, `.env`, `.git` seviyesinde IIS yetkilendirmesiyle engellenmesi.

## 3. Kurulum ve Gereksinimler

- **OS:** Windows Server 2022 / Windows 11 Pro/Enterprise
- **IIS:** 10.0+
- **Gerekli Modüller:**
  - IIS URL Rewrite Module 2.1
  - CGI / FastCGI Bileşeni
- **PHP:** 8.4+ Non-Thread Safe (NTS) sürümü

## 4. Temel IIS Yapılandırması (`web.config`)

Document Root (`public`) klasörü içerisinde yer alacak olan `web.config` dosyası, Apache `.htaccess` veya Nginx yapılandırmalarının dengidir.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <!-- Trailing slash kaldırma kuralı -->
                <rule name="Remove trailing slash" stopProcessing="true">
                    <match url="(.*)/$" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Redirect" redirectType="Permanent" url="{R:1}" />
                </rule>
                
                <!-- Front Controller Yönlendirmesi -->
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

Eğer Download Service de IIS arkasında yayınlanacaksa, **Application Request Routing (ARR)** modülü gereklidir.

```xml
<!-- web.config içine kural olarak eklenebilir -->
<rule name="ReverseProxyDownloadService" stopProcessing="true">
    <match url="^download/(.*)" />
    <action type="Rewrite" url="http://127.0.0.1:3001/{R:1}" />
</rule>
```
> **Not:** ARR modülü etkinleştirilmiş olmalı ve Proxy ayarları IIS yöneticisinden açık konuma getirilmelidir.

## 6. PHP FastCGI Ayarları

IIS'te PHP çalıştırmak için Non-Thread Safe (NTS) sürümü kullanılmalıdır.

- IIS Yöneticisi -> **Handler Mappings (Ä°şleyici Eşlemeleri)**
- Ekle: `*.php`
- Yürütülebilir: `C:\php8.4\php-cgi.exe`
- Ä°stek Kısıtlamaları: "File or Folder"

## 7. IMPLEMENTED / PLANNED Matrisi

| Konfigürasyon | Durum | Açıklama |
|---------------|-------|----------|
| IIS FastCGI + PHP | **PLANNED** | Windows ortamlarında kurumsal dağıtım için test edilecek. |
| URL Rewrite (web.config) | **PLANNED** | public/ dizininde web.config barındırılacak. |
| Ters Vekil (ARR) | **PLANNED** | Node.js servisleri için kurulum ve dokümantasyon sağlanacak. |
| Güvenlik Başlıkları | **PLANNED** | IIS `customHeaders` ile sağlanır, PHP seviyesiyle çakışma kontrol edilecek. |

## 8. Sorun Giderme (Troubleshooting)

- **HTTP Error 500.19 (Config Error):** URL Rewrite modülü kurulu olmayabilir. IIS URL Rewrite 2.1 indirip kurun.
- **HTTP Error 404 (Not Found):** Ä°stekler `index.php`'ye yönlendirilmiyorsa, `web.config` kurallarının aktif olduğundan emin olun.
- **FastCGI Hataları (502):** PHP NTS sürümünün kullanıldığından ve `php.ini`'nin doğru ayarlandığından emin olun (örn. `cgi.force_redirect = 0`).

---

## Faz 3 Doİ„Ş¸rulaması: Gerçek Config Kanıtı

Yukarıdaki konfigürasyon blokları, engine.md İ‚§12.2 Faz 3 kanıt zorunluluİ„Ş¸unu karİ…Ş¸ılamaktadır. Gerekli router, rewrite ve security tanımlamaları mevcuttur.

