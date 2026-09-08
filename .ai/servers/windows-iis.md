---
title: "windows-iis"
type: reference
folder: ".ai/servers"
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team - Human Mode - Truth Mode
---

# Windows IIS Yapılandırması

**Ortam:** Geliştirme / Production (Windows Server)
**Port:** 80 (HTTP), 443 (HTTPS)
**PHP:** PHP-FPM via FastCGI

## 1. IIS Kurulumu

### Windows Features
```
Internet Information Services
├── Web Management Tools
│   └── IIS Management Console
├── World Wide Web Services
│   ├── Application Development Features
│   │   ├── CGI
│   │   ├── ISAPI Extensions
│   │   └── ISAPI Filters
│   ├── Common HTTP Features
│   │   ├── Default Document
│   │   ├── Directory Browsing
│   │   ├── HTTP Errors
│   │   └── Static Content
│   ├── Health and Diagnostics
│   │   ├── HTTP Logging
│   │   └── Request Monitor
│   ├── Performance Features
│   │   └── Static Content Compression
│   └── Security
│       ├── Request Filtering
│       └── Windows Authentication
```

## 2. PHP-FPM Yapılandırması

### php.ini (Temel Ayarlar)
```ini
; Error Handling
display_errors = Off
log_errors = On
error_log = "C:\php\logs\php_errors.log"

; Memory
memory_limit = 256M

; Execution
max_execution_time = 30
max_input_time = 60

; Upload
upload_max_filesize = 50M
post_max_size = 50M

; Session
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379"

; OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 0
opcache.validate_timestamps = 0
```

### PHP-FPM Pool
```ini
[coremusic]
user = IUSR
group = IIS_IUSRS
listen = 127.0.0.1:9001
pm = dynamic
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
pm.max_requests = 500
```

## 3. IIS Application Host Config

### applicationHost.config
```xml
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <!-- Ana domain yönlendirmesi -->
                <rule name="CoreMusic Main" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{HTTP_HOST}" pattern="^(www\.)?coremusic\.net$" />
                    </conditions>
                    <action type="Rewrite" url="/index.php" appendQueryString="true" />
                </rule>

                <!-- Auth subdomain -->
                <rule name="Auth Subdomain" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{HTTP_HOST}" pattern="^auth\.coremusic\.net$" />
                    </conditions>
                    <action type="Rewrite" url="/index.php" appendQueryString="true" />
                </rule>

                <!-- Home subdomain -->
                <rule name="Home Subdomain" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{HTTP_HOST}" pattern="^home\.coremusic\.net$" />
                    </conditions>
                    <action type="Rewrite" url="/index.php" appendQueryString="true" />
                </rule>

                <!-- API subdomain -->
                <rule name="API Subdomain" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{HTTP_HOST}" pattern="^api\.coremusic\.net$" />
                    </conditions>
                    <action type="Rewrite" url="/index.php" appendQueryString="true" />
                </rule>

                <!-- Tüm subdomain'ler için varsayılan -->
                <rule name="Subdomain Rewrite" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{HTTP_HOST}" pattern="^(www\.)?coremusic\.net$" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="/index.php" appendQueryString="true" />
                </rule>
            </rules>
        </rewrite>

        <!-- Güvenlik Başlıkları -->
        <httpProtocol>
            <customHeaders>
                <add name="X-Content-Type-Options" value="nosniff" />
                <add name="X-Frame-Options" value="SAMEORIGIN" />
                <add name="X-XSS-Protection" value="1; mode=block" />
                <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
                <remove name="X-Powered-By" />
            </customHeaders>
        </httpProtocol>

        <!-- Request Filtering -->
        <security>
            <requestFiltering>
                <hiddenSegments>
                    <add segment=".env" />
                    <add segment=".git" />
                    <add segment="vendor" />
                    <add segment="packages" />
                </hiddenSegments>
                <fileExtensions>
                    <add fileExtension=".sql" allowed="false" />
                    <add fileExtension=".log" allowed="false" />
                </fileExtensions>
            </requestFiltering>
        </security>

        <!-- Static Dosyalar -->
        <staticContent>
            <remove fileExtension=".woff" />
            <mimeMap fileExtension=".woff" mimeType="font/woff" />
            <remove fileExtension=".woff2" />
            <mimeMap fileExtension=".woff2" mimeType="font/woff2" />
            <remove fileExtension=".json" />
            <mimeMap fileExtension=".json" mimeType="application/json" />
        </staticContent>
    </system.webServer>
</configuration>
```

## 4. Dizin Yapısı

```
C:\www\coremusic.net\
├── public\                 # IIS DocumentRoot
│   ├── index.php           # Ana giriş noktası
│   ├── .htaccess           # (IIS için gerekmez)
│   └── assets\             # Statik dosyalar
├── src\                    # PHP kaynak kodu
├── packages\               # Composer paketleri
├── vendor\                 # Composer dependencies
└── .env                    # Ortam değişkenleri
```

## 5. Hosts Dosyası

```
# C:\Windows\System32\drivers\etc\hosts
127.0.0.1    coremusic.net
127.0.0.1    www.coremusic.net
127.0.0.1    auth.coremusic.net
127.0.0.1    home.coremusic.net
127.0.0.1    pro.coremusic.net
127.0.0.1    studio.coremusic.net
127.0.0.1    car.coremusic.net
127.0.0.1    admin.coremusic.net
127.0.0.1    api.coremusic.net
127.0.0.1    media.coremusic.net
127.0.0.1    download.coremusic.net
127.0.0.1    music.coremusic.net
```

## 6. SSL Yapılandırması (Production)

```powershell
# Let's Encrypt sertifikası alma
# certbot ile IIS entegrasyonu
certbot certonly --webroot -w C:\www\coremusic.net\public -d coremusic.net -d *.coremusic.net
```

## 7. Hata Ayıklama

### IIS Logları
```
C:\inetpub\logs\LogFiles\
```

### PHP Logları
```
C:\php\logs\php_errors.log
```

### HTTP Hataları
```xml
<httpErrors errorMode="Detailed" />
```
