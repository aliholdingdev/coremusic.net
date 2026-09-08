---
title: "auth-migration-plan"
type: archive
folder: ".ai/architecture"
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team - Human Mode - Truth Mode
---

# CoreMusic Auth — Migration Planı

**Tarih:** 2026-09-03
**Durum:** Aktif
**Hedef:** include/ yapısını temiz Clean Architecture'e dönüştürmek

---

## 1. Mevcut Durum

###include/ Yapısı (Çalışan)
```
include/
├── Container/AuthContainer.php     ← DI container (php-di)
├── Controller/AuthController.php   ← IAuthService, ISessionManager
├── Service/AuthService.php         ← IAuthService interface implementasyonu
├── Service/SessionManager.php      ← Session yönetimi
├── Repository/UserRepository.php   ← BINARY(16) UUID v7
├── Domain/DTO/                     ← LoginRequest, RegisterRequest, AuthResponse
├── Domain/Entity/User.php          ← fromRow() factory
├── Domain/ValueObject/Gender.php   ← Gender value object
└── Handler/AuthPostHandler.php     ← POST istek yönlendirme
```

### Ortak Bağımlılıklar (shared paket)
```
shared/
├── Interfaces/Auth/IAuthService.php
├── Interfaces/Auth/ISessionManager.php
├── Interfaces/Auth/IUserRepository.php
├── Interfaces/Database/IDatabaseRegistry.php
├── Interfaces/Security/IRateLimiter.php
├── Database/DatabaseRegistry.php
├── Security/CacheRateLimiter.php
├── Security/UuidV7.php
├── Exception/ (AuthenticationException, ValidationException, vb.)
├── Log/LoggerFactory.php
├── Config/ConfigManager.php
├── Config/DomainConfig.php
└── PageRouter/ (PageRouterKernel, AuthGuard, vb.)
```

---

## 2. Migration Hedefleri

### Kısa Vadeli (Bu Hafta)
1. **include/ yapısını temizle** — Gereksiz kodları sil
2. **Test kapsamını artır** — Mevcut kodlar için test yaz
3. **Hata ayıklama** — Mevcut hataları tespit et

### Orta Vadeli (2-4 Hafta)
1. **Interface'leri netleştir** — IAuthService, IUserRepository güncellemesi
2. **DTO'ları zenginleştir** — Validation kuralları ekle
3. **Error handling'i standartlaştır** — Exception hierarchy

### Uzun Vadeli (1-3 Ay)
1. **src/ dizinini yeniden oluştur** — Bu sefer doğru UUID formatıyla
2. **Aşamalı geçiş** — include/ → src/ migration
3. **Test coverage %80+** — Tüm katmanlar için test

---

## 3. Adım Adım Migration

### Adım 1: include/ Temizliği
- [ ] Kullanılmayan dosyaları tespit et
- [ ] Duplicate kodları birleştir
- [ ] Namespace yapısını düzelt

### Adım 2: Test Kapsamı
- [ ] GenderTest.php ✓
- [ ] LoginRequestTest.php ✓
- [ ] RegisterRequestTest.php
- [ ] AuthResponseTest.php
- [ ] AuthServiceTest.php (unit test with mocks)
- [ ] UserRepositoryTest.php (integration test)

### Adım 3: Interface Güncelleme
- [ ] IAuthService: mevcut metodları koru, yeni metodlar ekle
- [ ] IUserRepository: BINARY(16) UUID formatını koru
- [ ] ISessionManager: mevcut yapının üstüne çıkarma

### Adım 4: src/ Yeniden Oluşturma (Gelecek)
- [ ] BINARY(16) UUID formatını kullan
- [ ] Mevcut interface'leri implemente et
- [ ] include/ ile paralel çalıştır
- [ ] Aşamalı olarak include/ → src/ geçişi

---

## 4. Risk Değerlendirmesi

| Risk | Olasılık | Etki | Önlem |
|------|----------|------|-------|
| Mevcut login bozulması | Düşük | Yüksek | Her değişiklik öncesi test çalıştır |
| UUID uyumsuzluğu | Yüksek | Yüksek | BINARY(16) formatını koru |
| Session kaybı | Orta | Orta | Session manager'ı değiştirme |
| DB şeması değişikliği | Yüksek | Yüksek | Migration yapma, mevcut şemayı koru |

---

## 5. Doğrulama Kontrol Listesi

Her adım sonrası:
- [ ] `composer dump-autoload` hatasız
- [ ] `php -l` syntax kontrolü
- [ ] Mevcut testler geçer
- [ ] Yeni testler yazılır
- [ ] Manuel test (tarayıcı ile login/logout)

---

## 6. Başarı Kriterleri

| Kriter | Hedef | Mevcut |
|--------|-------|--------|
| Test coverage | %80+ | ~%20 |
| Syntax errors | 0 | 0 |
| Working login | Evet | Evet |
| Working register | Evet | Evet |
| Session management | Çalışıyor | Çalışıyor |

---

## Notlar

1. **UUID Formatı:** Mevcut sistem BINARY(16) kullanıyor. Bu format korunmalı.
2. **Pepper:** APP_PEPPER environment variable'ı kullanılmalı.
3. **Gender Gate:** Login öncesi cinsiyet seçimi zorunlu.
4. **Auth Key:** Cross-domain auth için auth_key mekanizması var.
5. **PageRouter:** Shared paketteki PageRouterKernel kullanılıyor.
