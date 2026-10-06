####################################################################################
###                                                                              ###
###                Core Music AI Destekli Müzik Dinleme Sistemi                  ###
###            Youtube Music or Spotify or Deezer Music clone app                ###
###                                                                              ###
###                   Core Music Kodlama planı 00 appi & auth Planlaması         ###
###                                                                              ###
###                              VERSION 1.0.0                                   ###
###                                                                              ###
###                 Designed & Coding by Bayram Ali Akşit                        ###
###                                                                              ###
####################################################################################


# COREMUSIC — AUTH SYSTEM
## LOGIN · REGISTER · GENDER · PASSWORD RESET
### PLANLI KODLAMA + ARCHITECTURE + SECURITY IMPLEMENTATION ENGINE

**VERSION:** 3.0.0  
**LANGUAGE:** Türkçe  
**PROJECT:** CoreMusic  
**DOMAIN:** Authentication / Authorization / Session Management  
**ROLE:** Senior PHP Developer + Senior Frontend Engineer + Senior Software Architect + Senior Electron Engineer + Security Engineer  
**EXPERIENCE:** 50+ yıllık Senior Engineering perspektifi

---

# 1. PRIMARY MISSION

CoreMusic Authentication sistemini:

- Login
- Register
- Logout
- Gender Selection
- Forgot Password
- Reset Password
- Session
- JWT
- CSRF
- Rate Limiting
- Authentication API
- Authentication UI

alanlarıyla birlikte **production-ready, güvenli, katmanlı ve maintainable** şekilde implement et.

Ancak implementation'a başlamadan önce mevcut project yapısını oku ve doğrula.

---

# 2. ABSOLUTE RULE

## MEVCUT SİSTEMİ BOZMA

Mevcut:

- PHP
- API
- UI
- Route
- Session
- Database
- CSS
- JavaScript
- Authentication flow
- Existing behavior

incelenmeden dosya değiştirme.

Mevcut implementation ile bu prompt arasında çelişki varsa:

```text
CURRENT PROJECT
+
EXPLICIT REQUIREMENTS
+
SECURITY
+
ARCHITECTURE
```

birlikte analiz edilir.

Çelişki sessizce çözülmez.

---

# 3. TECHNOLOGY AUTHORITY

## ANA SİSTEM

```text
PHP 8.3
PDO
PSR-4
Composer
MySQL 8
```

PHP ana business system'dir.

## API

```text
api.coremusic.net
```

PHP API olarak çalışır.

API:

- Business Logic
- Validation
- Authentication
- Authorization
- Session
- Security Guards
- Database Operations

için authoritative backend'dir.

---

# 4. FRONTEND

```text
auth.coremusic.net
```

Authentication UI:

```text
PHP View
HTML
CSS
JavaScript
```

kullanır.

Frontend validation yalnızca UX amaçlıdır.

> Frontend hiçbir security rule'un authoritative kaynağı değildir.

---

# 5. NODE.JS SINIRI

Node.js ana backend değildir.

Node.js yalnızca:

```text
Electron
Build
Packaging
Live Reload
WebSocket Bridge
IPC
```

amaçlarıyla kullanılabilir.

Node.js şunları yapamaz:

```text
Password Validation
Business Logic
Authentication Decision
Gender Guard
Database Access
SQL
Session Management
JWT Generation
Authorization Decision
```

Node.js:

```text
Electron
   ↓
PHP API
```

modelinde yalnızca communication/support layer olarak çalışmalıdır.

---

# 6. ARCHITECTURE

Authentication için dependency direction:

```text
Electron / Node Shell
        │
        ▼
UI
        │
        ▼
AuthService
        │
        ▼
Controller / API
        │
        ▼
Repository
        │
        ▼
PDO
        │
        ▼
MySQL
```

Temel sorumluluk:

```text
UI
→ UX / Presentation

Service
→ Business Rules

Controller / API
→ Request / Response / Guard

Repository
→ Persistence

PDO
→ Database Access

MySQL
→ Data Persistence
```

Dependency tersine çevrilmemelidir.

---

# 7. SECURITY PRINCIPLE

Security yalnızca frontend'de uygulanamaz.

Bütün kritik security rules API/backend tarafında enforce edilmelidir.

Özellikle:

```text
Authentication
Authorization
Gender Guard
Session
JWT
Password
CSRF
Rate Limit
Database Validation
```

server-side olarak kontrol edilmelidir.

---

# 8. CONTEXT

## AUTH DOMAIN

```text
auth.coremusic.net
```

Authentication UI ve route layer'ını barındırır.

## API DOMAIN

```text
api.coremusic.net
```

Authentication business logic ve security enforcement layer'ıdır.

## ELECTRON

Electron yalnızca desktop shell'dir.

---

# 9. AUTH FLOW

Ana authentication flow:

```text
/
 ↓
Session Check
 ↓
Login?
 ├── YES → /home
 │
 └── NO
      ↓
    /login
      ↓
Gender Selected?
 ├── NO  → /select-gender
 │
 └── YES
      ↓
    Login
      ↓
Gender Guard
      ↓
Authentication
      ↓
/home
```

---

# 10. LOGIN

Route:

```text
GET /
GET /login
POST /login
```

Controller:

```text
LoginController
```

Beklenen behavior:

```text
GET /
```

Oturum varsa:

```text
/home
```

Oturum yoksa:

```text
/login
```

---

# 11. LOGIN ROUTES

| Method | Route | Controller | Expected Status |
|---|---|---|---|
| GET | `/` | `LoginController@index` | Redirect / Login |
| GET | `/login` | `LoginController@show` | 200 |
| POST | `/login` | `LoginController@login` | 302 / 401 / 422 |

---

# 12. REGISTER

Route:

```text
GET /register
POST /register
```

Controller:

```text
RegisterController
```

Input:

```text
name
email
password
gender
```

Gender seçimi zorunludur.

Register işlemi:

```text
Validation
 ↓
API
 ↓
Business Logic
 ↓
Transaction
 ↓
Password Hash
 ↓
Database
 ↓
Response
```

---

# 13. REGISTER ROUTES

| Method | Route | Controller | Expected Status |
|---|---|---|---|
| GET | `/register` | `RegisterController@show` | 200 |
| POST | `/register` | `RegisterController@register` | 302 / 409 / 422 |

---

# 14. GENDER SYSTEM

Gender values:

```text
kız
erkek
nötr
```

Gender selection authentication flow'un bir parçasıdır.

API tarafında kontrol zorunludur.

---

# 15. GENDER MATCH RULE

Asymmetric matching uygulanır:

| Selected | Allowed | Blocked |
|---|---|---|
| kız | kız | erkek / nötr |
| erkek | erkek | kız / nötr |
| nötr | nötr | kız / erkek |

Eşleşme yoksa:

```text
Authentication DENIED
HTTP 403
```

Mesaj:

```text
bu hesap giriş yapamaz
```

Gender guard hem:

```text
REGISTER
LOGIN
```

işlemlerinde API tarafında uygulanmalıdır.

---

# 16. GENDER ROUTE

```text
GET /select-gender
POST /select-gender
```

Controller:

```text
GenderController
```

Expected:

```text
302
```

---

# 17. AUTH API

API:

```text
api.coremusic.net
```

Endpoint'ler:

```text
POST /v1/auth/register
POST /v1/auth/login
POST /v1/auth/logout
POST /v1/auth/select-gender
POST /v1/auth/forgot-password
POST /v1/auth/reset-password
GET  /v1/auth/me
```

---

# 18. API CONTRACT

## REGISTER

```http
POST /v1/auth/register
```

Payload:

```json
{
  "name": "",
  "email": "",
  "password": "",
  "gender": ""
}
```

---

## LOGIN

```http
POST /v1/auth/login
```

Payload:

```json
{
  "email": "",
  "password": "",
  "gender": ""
}
```

---

## SELECT GENDER

```http
POST /v1/auth/select-gender
```

Payload:

```json
{
  "gender": ""
}
```

---

## FORGOT PASSWORD

```http
POST /v1/auth/forgot-password
```

Payload:

```json
{
  "email": ""
}
```

Expected:

```text
Always HTTP 200
```

---

## RESET PASSWORD

```http
POST /v1/auth/reset-password
```

Payload:

```json
{
  "token": "",
  "password": ""
}
```

---

## CURRENT USER

```http
GET /v1/auth/me
```

Expected:

```text
401
```

veya:

```text
200 + gender
```

---

# 19. LOGOUT

Route:

```text
POST /logout
```

Controller:

```text
AuthController@logout
```

API session/token state temizlenmelidir.

---

# 20. SECURITY REQUIREMENTS

Aşağıdaki security controls mevcut specification kapsamında zorunludur:

```text
TLS 1.3
HSTS
CSRF
Rate Limiting
Argon2id
IP Lockout
CORS Whitelist
Session Regeneration
HttpOnly
Secure Cookie
SameSite=Strict
PDO Prepared Statements
Request Validation
Audit Logging
JWT
Refresh Token Rotation
Token Blacklist
```

Security implementation sırasında mevcut project configuration ve infrastructure doğrulanmalıdır.

---

# 21. PASSWORD SECURITY

Password:

```text
Argon2id
```

ile hash edilmelidir.

Plain-text password:

- Database'e yazılmaz.
- Loglanmaz.
- Audit log'a yazılmaz.
- Response'a dahil edilmez.

---

# 22. SESSION SECURITY

Başarılı authentication sonrasında session security uygulanmalıdır.

Session regeneration:

```php
session_regenerate_id(true);
```

kullanılmalıdır.

Cookie:

```text
HttpOnly
Secure
SameSite=Strict
```

gereksinimlerine uygun olmalıdır.

---

# 23. CSRF

CSRF protection:

```text
Per-session token
Double-submit strategy
```

gereksinimleriyle uygulanmalıdır.

CSRF yalnızca frontend tarafından kontrol edilmemelidir.

API/server tarafında doğrulanmalıdır.

---

# 24. RATE LIMITING

Authentication endpoint'leri rate limiting'e tabi olmalıdır.

Mevcut specification:

```text
5 dakika / IP
```

olarak tanımlanmıştır.

Login abuse ve password reset abuse ayrıca değerlendirilmelidir.

Mevcut implementation farklıysa değiştirmeden önce analiz et.

---

# 25. IP LOCKOUT

Mevcut requirement:

```text
5 failed attempts
```

sonrasında IP lockout uygulanmasıdır.

Lockout implementation:

```text
Tracking
Threshold
Duration
Reset
Audit
```

boyutlarıyla analiz edilmelidir.

---

# 26. CORS

CORS whitelist:

```text
auth.coremusic.net
```

ile sınırlandırılmalıdır.

Wildcard:

```text
*
```

authentication API için kullanılmamalıdır.

---

# 27. FORGOT PASSWORD

Route:

```text
GET /forgot-password
POST /forgot-password
```

Controller:

```text
ForgotPasswordController
```

---

# 28. FORGOT PASSWORD ENUMERATION PROTECTION

Kullanıcı email'i database'de bulunmasa bile response aynı olmalıdır.

Expected:

```text
HTTP 200
```

Aynı kullanıcı mesajı kullanılmalıdır.

Amaç:

> Account Enumeration saldırısını önlemek.

---

# 29. RESET TOKEN

Token:

```text
32 bytes
random_bytes()
```

ile oluşturulmalıdır.

Token URL-safe formatta kullanılmalıdır.

Database'e:

> Raw token yazılmaz.

Hash saklanır.

Mevcut specification:

```text
SHA-256
```

olarak tanımlanmıştır.

---

# 30. TOKEN LIFETIME

Reset token:

```text
15 dakika
```

geçerlidir.

Süre dolduğunda:

```text
HTTP 400
```

dönmelidir.

---

# 31. SINGLE USE TOKEN

Reset token:

```text
Single Use
```

olmalıdır.

Başarılı kullanım sonrasında token geçersiz hale getirilmelidir.

---

# 32. RESET PASSWORD FLOW

```text
/forgot-password
      ↓
Email
      ↓
API
      ↓
Enumeration-safe lookup
      ↓
Generate Token
      ↓
Hash Token
      ↓
Store Hash
      ↓
SMTP
      ↓
/reset-password?token=...
      ↓
Validate Token
      ↓
Validate Expiration
      ↓
New Password
      ↓
Argon2id
      ↓
Update Password
      ↓
Invalidate Sessions
      ↓
Invalidate Refresh Tokens
      ↓
/login
```

---

# 33. PASSWORD RESET SESSION INVALIDATION

Password başarıyla değiştirildiğinde:

```text
All Refresh Tokens
All Sessions
```

iptal edilmelidir.

Amaç:

> Eski authentication state'lerinin kullanılmasını engellemek.

---

# 34. SMTP

Password reset mail'i SMTP üzerinden gönderilir.

Link:

```text
auth.coremusic.net/reset-password?token=...
```

olmalıdır.

Token loglanmamalıdır.

---

# 35. HTTP ERROR CONTRACT

| Condition | HTTP |
|---|---:|
| Invalid token | 400 |
| Gender mismatch | 403 |
| Duplicate email | 409 |
| Validation error | 422 |
| Database error | 500 |
| API / upstream problem | 502 |
| Connection problem | 503 |

Forgot password:

```text
Always 200
```

---

# 36. ERROR HANDLING

Frontend yalnızca kullanıcıya uygun presentation üretir.

Backend:

```text
Validation
Business Error
Authentication Error
Authorization Error
Persistence Error
Infrastructure Error
```

durumlarını belirler.

Sensitive internal error details kullanıcıya gönderilmemelidir.

---

# 37. DATABASE

Database:

```text
MySQL 8
```

Data access:

```text
PDO
Prepared Statements
```

üzerinden gerçekleştirilmelidir.

SQL string interpolation kullanılmamalıdır.

---

# 38. TRANSACTION

Register gibi birden fazla persistence operation gerektiren işlemlerde transaction kullanılmalıdır.

Örnek:

```text
BEGIN
 ↓
Validate
 ↓
Create User
 ↓
Write Audit
 ↓
COMMIT
```

Hata:

```text
ROLLBACK
```

---

# 39. AUDIT LOG

Authentication olayları audit edilmelidir.

Örneğin:

```text
Login Success
Login Failure
Register
Logout
Password Reset Requested
Password Reset Completed
Gender Guard Failure
Lockout
Token Revocation
```

Sensitive data audit log'a yazılmamalıdır.

Özellikle:

```text
Password
Raw Reset Token
Session Secret
JWT Secret
```

loglanamaz.

---

# 40. FRONTEND RESPONSIBILITY

Frontend:

```text
Form
UX Validation
Loading State
Error Display
Success Display
Navigation
CSRF Token Handling
API Request
```

yapabilir.

Frontend:

```text
Authentication Decision
Authorization Decision
Gender Guard Authority
Password Security Authority
```

olamaz.

---

# 41. NODE.JS RESPONSIBILITY

Node.js:

```text
Electron BrowserWindow
ipcMain
ipcRenderer
Build
Packaging
Live Reload
Optional WebSocket Bridge
```

ile sınırlandırılmıştır.

Node.js:

```text
MySQL
SQL
Password
Authentication
Session
JWT
Gender Guard
```

işlemlerini yapamaz.

Node.js gerektiğinde:

```text
HTTP → PHP API
```

kullanmalıdır.

---

# 42. IMPLEMENTATION WORKFLOW

Kod yazmadan önce:

```text
READ
 ↓
UNDERSTAND
 ↓
CHECK CURRENT FILES
 ↓
CHECK ROUTES
 ↓
CHECK API
 ↓
CHECK DATABASE
 ↓
CHECK SECURITY
 ↓
PLAN
 ↓
IMPLEMENT
 ↓
TEST
 ↓
VALIDATE
```

uygulanmalıdır.

---

# 43. EXISTING PROJECT ANALYSIS

Implementation öncesinde kontrol et:

```text
routes
controllers
services
repositories
models
database
migrations
middleware
session
authentication
frontend
CSS
JavaScript
Composer
PHP configuration
API configuration
environment configuration
```

Mevcut dosya yapısı doğrulanmadan yeni path uydurma.

---

# 44. ARCHITECTURE RULE

Minimum responsibility separation:

```text
Controller
    ↓
Service
    ↓
Repository
    ↓
PDO
    ↓
MySQL
```

Controller içerisinde:

- SQL
- Database query
- Password hashing
- Complex business logic

bulunmamalıdır.

Repository içerisinde:

- HTTP redirect
- HTML rendering
- Authentication UI

bulunmamalıdır.

Service içerisinde:

- View rendering

bulunmamalıdır.

---

# 45. SOLID

Implementation:

```text
SRP
OCP
LSP
ISP
DIP
```

prensiplerine uygun olmalıdır.

Özellikle:

```text
Controller
Service
Repository
```

responsibility'leri birbirine karıştırılmamalıdır.

---

# 46. CLEAN CODE

Kod:

- Açık
- Küçük
- Test edilebilir
- Type-safe
- Explicit
- Maintainable

olmalıdır.

PHP:

```php
declare(strict_types=1);
```

kullanmalıdır.

---

# 47. VALIDATION

Her implementation sonrasında:

```text
[ ] Route works
[ ] API works
[ ] Authentication works
[ ] Gender guard works
[ ] Session works
[ ] CSRF works
[ ] Rate limit works
[ ] Password hashing works
[ ] Reset token works
[ ] Enumeration protection works
[ ] Session invalidation works
[ ] Database transaction works
[ ] SQL injection protection works
[ ] Error contract works
[ ] Node.js does not contain business logic
```

kontrol edilmelidir.

---

# 48. TESTING

Test seviyeleri:

```text
Unit
Integration
API
Security
Regression
```

gerektiği yerde kullanılmalıdır.

Özellikle:

```text
Login
Register
Gender mismatch
Forgot password
Reset password
Expired token
Used token
Session invalidation
CSRF
Rate limit
```

test edilmelidir.

---

# 49. AUTH FLOW VALIDATION

## LOGIN

```text
/
 ↓
Session?
 ├─ YES → /home
 └─ NO → /login
          ↓
       Gender?
          ↓
       API Login
          ↓
       Gender Guard
          ↓
       Success
          ↓
       /home
```

---

# 50. REGISTER

```text
/register
 ↓
Validation
 ↓
API
 ↓
Transaction
 ↓
Create User
 ↓
Gender
 ↓
Success
 ↓
/login
```

---

# 51. FORGOT PASSWORD

```text
/forgot-password
 ↓
Email
 ↓
API
 ↓
Always 200
 ↓
If account exists:
    token
    hash
    DB
    SMTP
```

---

# 52. RESET PASSWORD

```text
/reset-password
 ↓
Token Validation
 ↓
Expiration
 ↓
Password Validation
 ↓
Argon2id
 ↓
Update
 ↓
Invalidate Sessions
 ↓
Invalidate Refresh Tokens
 ↓
/login
```

---

# 53. DO NOT DO

Aşağıdakiler yasaktır:

```text
Node.js'te Authentication
Node.js'te SQL
Node.js'te Password Validation
Frontend-only Security
Plain Password Storage
Raw Reset Token Storage
SQL Injection
Hardcoded Secrets
Controller'da SQL
Repository'de HTML
Service'de View Rendering
Duplicate Business Logic
Silent Errors
Sensitive Logging
Wildcard CORS
```

---

# 54. OUTPUT FORMAT

Her implementation adımı için:

```text
FILE:
PATH:

PURPOSE:

CURRENT STATE:

CHANGE:

ARCHITECTURE:

SECURITY:

DEPENDENCIES:

IMPLEMENTATION:

TEST:

VALIDATION:

RISKS:

RELATED FILES:
```

formatını kullan.

---

# 55. CODE GENERATION RULE

Kod yazarken:

1. Önce mevcut dosyayı oku.
2. Caller'ları bul.
3. Consumer'ları bul.
4. Dependency'leri kontrol et.
5. Security etkisini kontrol et.
6. En küçük güvenli değişikliği planla.
7. Kodla.
8. Test et.
9. Validate et.

Kod yazmadan önce varsayım yapma.

---

# 56. FINAL DELIVERY CRITERIA

Aşağıdakilerin tamamı sağlanmalıdır:

```text
[ ] / → /login → /select-gender → /home çalışıyor
[ ] Existing session → /home
[ ] Register çalışıyor
[ ] Gender zorunlu
[ ] Gender mismatch → 403
[ ] Login API çalışıyor
[ ] Logout çalışıyor
[ ] Forgot password → always 200
[ ] Reset token → 15 dakika
[ ] Reset token → single-use
[ ] Raw token DB'de yok
[ ] Password → Argon2id
[ ] Password reset → all sessions revoked
[ ] Refresh tokens revoked
[ ] CSRF enabled
[ ] Rate limit enabled
[ ] IP lockout enabled
[ ] CORS restricted
[ ] Secure cookies
[ ] Audit logging
[ ] PDO prepared statements
[ ] SQL injection protection
[ ] XSS protection
[ ] Node.js yalnızca destek layer
[ ] Business Logic PHP
[ ] Database PHP
[ ] Session PHP
[ ] Authentication PHP
[ ] Layer dependency tek yönlü
```

---

# 57. FINAL ARCHITECTURE

Nihai model:

```text
                    COREMUSIC AUTH
                         │
              ┌──────────┴──────────┐
              │                     │
          WEB CLIENT          ELECTRON SHELL
              │                     │
              │                 IPC / HTTP
              │                     │
              └──────────┬──────────┘
                         │
                         ▼
                  AUTH CONTROLLER
                         │
                         ▼
                    AUTH SERVICE
                         │
              ┌──────────┼──────────┐
              │          │          │
              ▼          ▼          ▼
          Session     Security    Validation
              │          │          │
              └──────────┼──────────┘
                         ▼
                    REPOSITORY
                         │
                         ▼
                       PDO
                         │
                         ▼
                      MYSQL
```

---

# 58. ANA PRENSİP

**Önce mevcut sistemi oku.**

**Sonra architecture'ı anla.**

**Sonra security boundary'lerini kontrol et.**

**Sonra dependency'leri kontrol et.**

**Sonra plan yap.**

**Sonra kodla.**

**Sonra test et.**

**Sonra validate et.**

---

# 59. SON KURAL

CoreMusic Authentication sistemi:

> **PHP merkezli, güvenlik odaklı, katmanlı, SOLID/Clean Code prensiplerine uygun, test edilebilir ve Node.js'ten bağımsız business logic'e sahip olmalıdır.**

Node.js yalnızca:

```text
Electron
IPC
Build
Packaging
Live Reload
Optional WebSocket Bridge
```

içindir.

Tüm kritik authentication ve security kararları:

```text
PHP API
```

tarafında verilmelidir.

**Mevcut project behavior'ını doğrulamadan bozma.**

**Eksik bilgiyi tahmin etme.**

**Dosya yolu uydurma.**

**Kod yazmadan önce mevcut implementation'ı oku.**

**Test edilmemiş sistemi verified olarak işaretleme.**


# COREMUSIC — Planlı Kodlama Prompt'u · Adım 1: Auth (Login / Register / Şifremi Unuttum)

> Sürüm: 2.0 · Dil: Türkçe · Rol: **Senior PHP Developer** + Frontend Uzmanı + Senior Electron Uzmanı
> Deneyim: **50 yıllık senior** · Mimari: **Katmanlı + SOLID + Clean Code**
> ⚠️ **ANA SİSTEM PHP'DİR.** Node.js yalnızca destek amaçlıdır (Electron kabuğu, build, WebSocket köprüsü).

---

## 1. ROL & TEKNOLOJİ KARARI

| Alan | Değer |
|---|---|
| **Ana sistem** | **PHP 8.3** (strict_types, PDO, PSR-4 Composer) |
| API | `api.coremusic.net` → PHP 8.3 + PDO + JSON |
| UI | `auth.coremusic.net` → PHP 8.3 (view/controller) + HTML/CSS/JS |
| **Node.js (destek)** | Yalnızca: Electron penceresi, derleme/paketleme, canlı reload, WebSocket köprüsü |
| Node.js yasağı | İş mantığı, doğrulama, DB erişimi, oturum **asla** Node.js'te olmaz |
| Veritabanı | MySQL 8 + PDO prepared statement |
| Dil | Türkçe (kod anahtar kelimeleri hariç) |
| Kural | Planı **birebir** uygula; adımı atla, keyfi genişletme |

### 1.1 Katmanlar (zorunlu, tek yönlü bağımlılık)

```
[Electron/Node kabuğu]  →  yalnızca pencere + kanal
        │
        ▼
UI (PHP view + JS)  →  AuthService (PHP)  →  Controller/API (PHP)  →  Repository (PDO)  →  MySQL
   sadece UX            iş kuralları           guard + status            SQL + tx
```

- **Frontend kontrolü = UX içindir.** Güvenlik asla frontend'de sağlanmaz.
- **Tüm guard'lar API tarafında** (`api.coremusic.net`) zorla uygulanır.
- Node.js katmanı **hiçbir** güvenlik kararı vermez.
---

## 2. CONTEXT (BAĞLAM)

```
auth.coremusic.net   →  PHP view + controller, session/JWT cookie, yönlendirme
api.coremusic.net    →  PHP API, TÜM iş mantığı, ULTRA GÜVENLİ, tek doğruluk kaynağı
Node.js / Electron   →  sadece masaüstü kabuğu ve build araçları
```

---

## 3. ADIM 1 — LOGIN / SIGN-IN / REGISTER / ŞİFREMİ UNUTTUM

### 3.1 Yönlendirme kuralları (auth.coremusic.net)

Kullanıcı `http://auth.coremusic.net` adresine girer:

1. **Login olmuş ise** → `**{redicest to back refer url}**/home`
2. **Login olmamış ise** → `/` veya `/login`
3. `/login` sonrasında **hemen** kontrol et:
   - **cinsiyet seçilmemişse** → `/select-gender`
   - **cinsiyet seçiliyse** → `/login` akışına devam et

### 3.2 Cinsiyet guard kuralı (asimetrik eşleşme)

| Seçilen | İzin verilen | Engellenen |
|---|---|---|
| **kız** | kız hesabı geçer | erkek ✗ · nötr ✗ |
| **erkek** | erkek hesabı geçer | kız ✗ · nötr ✗ |
| **nötr** | nötr hesabı geçer | kız ✗ · erkek ✗ |

Eşleşme yoksa: **giriş yapılmaz**, kullanıcıya mesaj gösterilir.
Kontrol hem **register** hem **login** çağrısında API'de çalışır.

### 3.3 Route tablosu (PHP)

| Metod | Route | Controller | Durum |
|---|---|---|---|
| GET | `/` | `LoginController@index` | login ise → `/home` |
| GET | `/login` | `LoginController@show` | 200 |
| POST | `/login` | `LoginController@login` | 302 / 401 / 422 |
| GET | `/register` | `RegisterController@show` | 200 |
| POST | `/register` | `RegisterController@register` | 302 / 409 / 422 |
| GET/POST | `/select-gender` | `GenderController@form` | 302 |
| GET | `/forgot-password` | `ForgotPasswordController@show` | 200 |
| POST | `/forgot-password` | `ForgotPasswordController@request` | **her zaman 200** |
| GET | `/reset-password?token=` | `ForgotPasswordController@reset` | 200 / 400 |
| POST | `/reset-password` | `ForgotPasswordController@update` | 302 / 400 |
| POST | `/logout` | `AuthController@logout` | 302 |

### 3.4 API endpointleri (api.coremusic.net)

```
POST /v1/auth/register          { name, email, password, gender }
POST /v1/auth/login             { email, password, gender }
POST /v1/auth/logout
POST /v1/auth/select-gender     { gender }
POST /v1/auth/forgot-password   { email }        → her zaman 200
POST /v1/auth/reset-password    { token, password }
GET  /v1/auth/me                                   → 401 / 200 + gender
```

### 3.5 Güvenlik katmanı (zorunlu — PHP specific)

```
TLS 1.3 + HSTS                 CSRF token (double submit + per-session)
rate-limit 5 dk / IP            argon2id parola hash (memory_cost 64MB+)
IP lockout (5 hatalı deneme)    CORS whitelist (sadece auth.coremusic.net)
session_regenerate_id(true)     cookie: HttpOnly + Secure + SameSite=Strict
PDO prepared statement          body validation (her field)
audit log (tüm auth olayları)   imzalı JWT + refresh rotation + blacklist
```

### 3.6 Şifremi Unuttum — güvenlik kuralları

| Kural | Değer |
|---|---|
| Token | 32 bayt `random_bytes()`, URL-safe base64 |
| Ömür | 15 dakika |
| Saklama | DB'de **hash** (sha256), ham asla |
| Kullanım | Tek kullanımlık; kullanınca sil |
| Enumeration | Email kayıtlı olmasa da **aynı 200 mesajı** dön |
| Sorgu süresi | Sabit gecikmeli (timing koruması) |
| Sonrası | Yeni hash yaz → **tüm** refresh token ve session iptal |
| Mail | SMTP, sadece `auth.coremusic.net/reset-password?token=` linki |

### 3.7 Hata kodları ve mesajlar

| Kaynak | HTTP | Mesaj |
|---|---|---|
| Yanlış cinsiyet eşleşmesi | 403 | "bu hesap giriş yapamaz" |
| Alan doğrulama hatası | 422 | "alan hatası" |
| Email zaten kayıtlı | 409 | "bu email kullanılıyor" |
| Token geçersiz / süresi dolmuş | 400 | "link geçersiz veya süresi dolmuş" |
| Bağlantı hatası | 503 | "bağlantı hatası, tekrar dene" |
| Veritabanı sorunu | 500 | "sistem hatası" |
| API sorunu / timeout | 502 | "servis yok, tekrar dene" |

Kullanıcıya gösterilecek durum mesajları: `Kız` · `Erkek` · `Nötr`

---

## 4. FLOW DİYAGRAMLARI (ASCII / Unicode Box Art)

### D1 — LOGIN

```
┌──────────────────────────────────────────────────────┐
│★★  D1 · LOGIN AKIŞI  ★★                              │
│auth.coremusic.net  →  api.coremusic.net              │
│yığın: PHP 8.3 · PDO · PSR-4 Composer                 │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│★ [ START ]  /                                        │
│tarayıcı isteği                                       │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│M1 · OTURUM KONTROLÜ                                  │
│session_start() + JWT cookie var mı?                  │
└───────────┬──────────────┬───────────────────────────┘
            │ VAR          │ YOK
            ▼              │
┌────────────────────────┐ │
│302 → /home             │ │
│zaten giriş             │ │
│yapılmış                │ │
│● TERMİNAL              │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│L1 · /login  SAYFASI                                  │
│mail + şifre + CSRF token                             │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│G1 · CİNSİYET SEÇİM KONTROLÜ                          │
│users.gender seçili mi?                               │
└───────────┬──────────────┬───────────────────────────┘
            │ HAYIR        │ EVET
            ▼              │
┌────────────────────────┐ │
│/select-gender          │ │
│kız / erkek / nötr      │ │
│seçim sonrası → /login  │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│A1 · POST  /v1/auth/login                             │
│api.coremusic.net  (PHP 8.3 + PDO)                    │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│A2 · ULTRA GÜVENLİ KATMAN                             │
│TLS1.3 + HSTS · CSRF · rate-limit 5/dk                │
│argon2id · IP lockout · CORS whitelist                │
│session_regenerate_id · SameSite=Strict               │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│A3 · API YANITI                                       │
│başarılı (200) mı?                                    │
└───────────┬──────────────┬───────────────────────────┘
            │ HATA         │ OK
            ▼              │
┌────────────────────────┐ │
│⛔ HATA EKRANI           │ │
│422 alan hatası         │ │
│503 bağlantı            │ │
│500 veritabanı          │ │
│502 api hatası          │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│G2 · CİNSİYET GUARD  (API tarafında)                  │
│seçili cinsiyet = hesap cinsiyeti mi?                 │
│                                                      │
│kız    → erkek ✗ nötr  ✗  engellenir                  │
│erkek  → kız   ✗ nötr  ✗  engellenir                  │
│nötr   → kız   ✗ erkek ✗  engellenir                  │
└───────────┬──────────────┬───────────────────────────┘
            │ UYUŞMAZ      │ EŞLEŞTİ
            ▼              │
┌────────────────────────┐ │
│⛔ 403 REDDEDİLDİ        │ │
│mesaj: bu hesap         │ │
│giriş yapamaz           │ │
│→ /login                │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│[ OK ]  302 → /home                                   │
│kullanıcı paneli                                      │
│● TERMİNAL                                            │
└──────────────────────────────────────────────────────┘
```

### D2 — REGISTER

```
┌──────────────────────────────────────────────────────┐
│★★  D2 · REGISTER AKIŞI  ★★                           │
│/register  →  api.coremusic.net                       │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│★ [ START ]  /register                                │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│K1 · KAYIT FORMU                                      │
│ad · email · şifre · cinsiyet*                        │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│K2 · İSTEM DOĞRULAMA (client + API)                   │
│şifre ≥ 12 karakter · email formatı                   │
│cinsiyet seçimi ZORUNLU                               │
└───────────┬──────────────┬───────────────────────────┘
            │ HATA         │ OK
            ▼              │
┌────────────────────────┐ │
│⛔ 422                   │ │
│alan hatası             │ │
│mesajı                  │ │
│formda göster           │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│POST /v1/auth/register                                │
│api.coremusic.net  (PHP + PDO)                        │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│K3 · KAYIT İŞLEMİ (DB transaction)                    │
│email UNIQUE · argon2id hash                          │
│audit log · IP + timestamp                            │
└───────────┬──────────────┬───────────────────────────┘
            │ HATA         │ OK
            ▼              │
┌────────────────────────┐ │
│⛔ KAYIT YOK             │ │
│409 email var           │ │
│500 veritabanı          │ │
│503 bağlantı            │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│K4 · CİNSİYET SEÇİLİ Mİ?                              │
│register sırasında seçildi mi?                        │
└───────────┬──────────────┬───────────────────────────┘
            │ HAYIR        │ EVET
            ▼              │
┌────────────────────────┐ │
│/select-gender          │ │
│kız / erkek / nötr      │ │
│seç → /login            │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│[ OK ]  302 → /login                                  │
│"hesabınız oluşturuldu"                               │
│● TERMİNAL                                            │
└──────────────────────────────────────────────────────┘
```

### D3 — ŞİFREMİ UNUTTUM / RESET

```
┌──────────────────────────────────────────────────────┐
│★★  D3 · ŞİFREMİ UNUTTUM AKIŞI  ★★                    │
│/forgot-password  →  /reset-password                  │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│★ [ START ]  /forgot-password                         │
│login ekranındaki "şifremi unuttum"                   │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│F1 · E-POSTA GİRİŞİ                                   │
│kullanıcı email yazar                                 │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│F2 · KULLANICI SORGUSU (API)                          │
│email kayıtlı mı?  enumeration koruması               │
└───────────┬──────────────┬───────────────────────────┘
            │ YOK          │ VAR
            ▼              │
┌────────────────────────┐ │
│kayıt yok               │ │
│yine de 200 dön         │ │
│aynı mesaj              │ │
│mail gitmez             │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│F3 · TOKEN ÜRET (API)                                 │
│32 bayt rastgele · ömür 15 dk                         │
│DB: hash sakla · tek kullanımlık                      │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│F4 · E-POSTA GÖNDER (SMTP)                            │
│link: /reset-password?token=xxxx                      │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│F5 · /reset-password                                  │
│yeni şifre + confirm · CSRF                           │
└───────────┬──────────────┬───────────────────────────┘
            │ GEÇERSİZ     │ GEÇERLİ
            ▼              │
┌────────────────────────┐ │
│⛔ 400                   │ │
│token süresi            │ │
│dolmuş /                │ │
│kullanılmış             │ │
└────────────────────────┘ │
                           ▼
┌──────────────────────────────────────────────────────┐
│F6 · GÜNCELLE + OTURUM KIR                            │
│argon2id hash yenile                                  │
│tüm refresh token iptal                               │
│tüm session flush                                     │
└──────────────────────────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────┐
│[ OK ]  302 → /login                                  │
│"şifreniz sıfırlandı"                                 │
│● TERMİNAL                                            │
└──────────────────────────────────────────────────────┘
```

---

## 5. NODE.JS'İN SINIRI (net kural)

| Node.js YAPAR | Node.js YAPMAZ |
|---|---|
| Electron `BrowserWindow` | Parola doğrulama |
| `ipcMain`/`ipcRenderer` kanalı | Cinsiyet guard'ı |
| Derleme, paketleme, canlı reload | Session / JWT üretimi |
| Opsiyonel: WS → PHP köprüsü | MySQL erişimi, SQL yazımı |

Node.js kanalı sadece **PHP API'ye HTTP isteği** atar; cevabı ekrana basar.

---

## 6. TESLİM KRİTERLERİ

- [ ] `/` → `/login` → `/select-gender` → `**{redicest to back refer url}**/home` zinciri çalışır
- [ ] Oturum varsa doğrudan `**{redicest to back refer url}**//home`
- [ ] `/register` → cinsiyet zorunlu → `/select-gender` → `/login`
- [ ] `/forgot-password` her durumda **aynı 200** mesajını döner
- [ ] `/reset-password` geçersiz token → 400; geçerli → 302 + tüm oturumlar kırılır
- [ ] Cinsiyet eşleşmiyorsa giriş **engellenir** ve mesaj gösterilir
- [ ] Tüm hata kodları (400/403/409/422/500/502/503) API'den döner
- [ ] İş mantığı / DB / session **%100 PHP**; Node.js yalnızca kabuk
- [ ] Frontend'de gizli güvenlik kontrolü **yoktur**
- [ ] SQL injection / XSS / CSRF koruması açık
- [ ] Katmanlar tek yönlü: UI → Service → Controller → Repository → MySQL
