# CoreMusic Shared Package

CoreMusic Shared, tum servisler tarafindan kullanilan ortak sozlesmeleri, veri yapilarini ve yardimci siniflari icerir.

## Gereksinimler

- PHP >= 8.3
- ramsey/uuid ^4.7
- paragonie/sodium_compat ^1.20

## Kurulum

```bash
cd packages/shared
composer install
```

## Dizin Mimarisi

```
packages/shared/
├── composer.json
├── phpunit.xml
├── README.md
├── src/
│   ├── Contract/           # Arayuzler
│   │   ├── Http/           # RequestInterface, ResponseInterface, MiddlewareInterface
│   │   ├── Repository/     # UserRepositoryInterface, MediaRepositoryInterface, PlaylistRepositoryInterface
│   │   └── Service/        # AuthServiceInterface, MediaServiceInterface
│   ├── DTO/                # Veri Transfer Nesneleri
│   │   ├── Auth/           # LoginRequest, LoginResponse, TokenPair
│   │   ├── User/           # UserDTO, UserProfileDTO
│   │   ├── Media/          # MediaFileDTO, MediaUploadDTO
│   │   └── Playlist/       # PlaylistDTO, PlaylistItemDTO
│   ├── Enum/               # Numaralandirmalar
│   │   ├── UserRole
│   │   ├── MediaType
│   │   ├── DeviceType
│   │   └── PermissionAction
│   ├── ValueObject/        # Deger Nesneleri
│   │   ├── Email
│   │   ├── UserId
│   │   ├── MediaId
│   │   └── PlaylistId
│   ├── Exception/          # Ozel Hata Siniflari
│   │   ├── AuthenticationException
│   │   ├── AuthorizationException
│   │   ├── ValidationException
│   │   └── NotFoundException
│   ├── Event/              # Olay Tanimlari
│   │   ├── UserLoggedIn
│   │   ├── UserLoggedOut
│   │   ├── MediaUploaded
│   │   └── PlaylistCreated
│   ├── Validation/         # Dogrulama Katmani
│   │   ├── EmailRule
│   │   ├── PasswordRule
│   │   └── ValidationResult
│   ├── Security/           # Guvenlik Bilesenleri
│   │   ├── TokenGenerator
│   │   ├── Hasher
│   │   └── Cipher
│   ├── Http/               # HTTP Katmani
│   │   ├── StatusCode
│   │   └── Headers
│   └── Helper/             # Yardimci Siniflar
│       ├── ArrayHelper
│       ├── StringHelper
│       └── DateTimeHelper
└── tests/
    └── Unit/
        ├── ValueObject/
        │   └── EmailTest.php
        └── Helper/
            └── StringHelperTest.php
```

## Kullanim Ornekleri

### Value Object Kullanimi

```php
use CoreMusic\Shared\ValueObject\Email;
use CoreMusic\Shared\ValueObject\UserId;

$email = new Email('user@example.com');
$userId = UserId::generate();
```

### Enum Kullanimi

```php
use CoreMusic\Shared\Enum\UserRole;

$role = UserRole::ADMIN;
echo $role->label(); // "Admin"
```

### Validation Kullanimi

```php
use CoreMusic\Shared\Validation\EmailRule;
use CoreMusic\Shared\Validation\PasswordRule;

$emailRule = new EmailRule();
$error = $emailRule->validate('invalid-email');
// $error = "Gecersiz e-posta formati."

$passwordRule = new PasswordRule(minLength: 8);
$error = $passwordRule->validate('weak');
// $error = "Sifre en az 8 karakter olmalidir."
```

### Helper Kullanimi

```php
use CoreMusic\Shared\Helper\StringHelper;
use CoreMusic\Shared\Helper\ArrayHelper;

$slug = StringHelper::slugify('Merhaba Dunya'); // "merhaba-dunya"
$truncated = StringHelper::truncate('Uzun metin...', 10); // "Uzun metin..."

$users = [['name' => 'Ali', 'age' => 25], ['name' => 'Veli', 'age' => 30]];
$names = ArrayHelper::pluck($users, 'name'); // ['Ali', 'Veli']
```

## Testler

```bash
cd packages/shared
composer test
# veya
vendor/bin/phpunit
```

## Notlar

- Veritabani implementasyonlari kapsam disidir; sadece PHP duzeyindeki tip ve sozlesme yapilari olusturulmustur.
- Tum PHP dosyalarinda `declare(strict_types=1)` zorunludur.
- Paket, temiz mimari (Clean Architecture) ilkelerine uygun olarak tasarlanmistir.
