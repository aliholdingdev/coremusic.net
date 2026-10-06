## LOCAL SERVER / DEVELOPMENT DOMAIN CONFIGURATION

CoreMusic development environment local server üzerinde çalıştırılmalıdır.

Windows `hosts` dosyasında aşağıdaki local domain mapping'leri kullanılmalıdır:

```text
127.0.0.1    home.coremusic.net
127.0.0.1    assets.coremusic.net
127.0.0.1    auth.coremusic.net
127.0.0.1    api.coremusic.net
```

### PHP Built-in Server

Development ortamında PHP server, PHP Built-in Server kullanılarak `php -S` ile başlatılmalıdır.

Her CoreMusic domain'i kendi document root'u ile çalıştırılmalıdır.

Örnek:

```bash
php -S home.coremusic.net:81 -t [HOME_DOCUMENT_ROOT]
```

```bash
php -S assets.coremusic.net:8001 -t [ASSETS_DOCUMENT_ROOT]
```

```bash
php -S auth.coremusic.net:8002 -t [AUTH_DOCUMENT_ROOT]
```

```bash
php -S api.coremusic.net:8003 -t [API_DOCUMENT_ROOT]
```

> Port numaraları mevcut project configuration tarafından belirlenmişse mevcut değerler korunmalıdır. Port bilgisi belirtilmemişse `[PORT]` placeholder kullanılmalıdır.

### Server Başlatma Kuralları

1. Önce local development environment kontrol edilmelidir.
2. `hosts` dosyasındaki CoreMusic domain mapping'leri doğrulanmalıdır.
3. İlgili PHP version doğrulanmalıdır.
4. Her service için doğru document root kullanılmalıdır.
5. PHP Built-in Server `php -S` ile başlatılmalıdır.
6. Domain → port → document root eşleşmesi korunmalıdır.
7. Bir service'in document root'u başka bir service ile karıştırılmamalıdır.
8. Server başlatılırken mevcut project structure değiştirilmemelidir.
9. Production server configuration taklit edilmemeli; bu yapı yalnızca local development içindir.
10. Mevcut port, PHP version veya path bilgisi project configuration'da bulunuyorsa tahmin edilmemeli, mevcut değer kullanılmalıdır.

### Beklenen Local Domain Yapısı

```text
home.coremusic.net
        ↓
PHP Built-in Server
        ↓
[HOME_DOCUMENT_ROOT]

assets.coremusic.net
        ↓
PHP Built-in Server
        ↓
[ASSETS_DOCUMENT_ROOT]

auth.coremusic.net
        ↓
PHP Built-in Server
        ↓
[AUTH_DOCUMENT_ROOT]

api.coremusic.net
        ↓
PHP Built-in Server
        ↓
[API_DOCUMENT_ROOT]
```

### Validation

Server'lar başlatıldıktan sonra aşağıdaki domain'lerin local olarak erişilebilir olduğu doğrulanmalıdır:

```text
http://home.coremusic.net:[PORT]
http://assets.coremusic.net:[PORT]
http://auth.coremusic.net:[PORT]
http://api.coremusic.net:[PORT]
```

Her domain'in doğru application/document root'a yönlendiği ve yanlış service'e düşmediği kontrol edilmelidir.