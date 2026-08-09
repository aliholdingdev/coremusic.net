---
type: adr
category: testing
title: "ADR-023: Persona-Driven Testing"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-023: Persona-Driven Testing

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Testing
**İlgili Agent:** [[.agents/qa-engineer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun test stratejisini persona-driven testing yaklaşımıyla tanımlar. Kullanıcı personaları temelinde test senaryoları oluşturulur, kapsam hedefleri belirlenir ve test piramidi yapılandırılır. Tüm test süreçleri bu sözleşmeyle bağlıdır.

---

## 2. Bağlam

CoreMusic farklı kullanıcı kitlelerine hizmet veren bir platformdur:
- Bireysel kullanıcılar (günlük müzik dinleme)
- Profesyonel müzik üreticileri (stüdyo üretimi)
- DJ'ler (canlı performans)
- Sistem yöneticileri (admin paneli)
- Araç içi kullanıcılar (car infotainment)
- Ev medya merkezi kullanıcıları (home center)

Her persona farklı ihtiyaçlara, farklı cihazlara ve farklı使用 senaryolarına sahiptir. Geleneksel test yaklaşımları bu çeşitliliği karşılayamaz.

---

## 3. Karar

CoreMusic'te **persona-driven testing** kullanılacak. Tüm test senaryoları kullanıcı personalarından türetilir ve her persona için özel test akışları tanımlanır.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| Persona bazlı | Testler kullanıcı türlerinden türetilir | Bu ADR |
| Senaryo odaklı | Gerçek kullanım senaryoları test edilir | — |
| Kapsam zorunlu | Minimum %80, hedef %90 | [[ADR-040-database-authority]] |
| Piramit yapısı | Unit %70, Integration %20, E2E %10 | — |
| Cross-platform | Tüm cihazlarda test | [[ADR-019-per-os-neva-player]] |

---

## 4. Teknik Detaylar

### 4.1 Persona Tanımları

#### 4.1.1 Casual User (Günlük Kullanıcı)

| Özellik | Değer |
|---------|-------|
| Hedef | Günlük müzik dinleme, keşif |
| Cihaz | Mobil, masaüstü, araç içi |
| Teknik bilgi | Düşük |
| Hassasiyet | Kullanım kolaylığı |
| Ana akış | Giriş → Ara → Dinle → Çalma Listesi |
| Frekans | Günde 2-3 saat |

**Test Senaryoları:**
| # | Senaryo | Öncelik | Tip |
|---|---------|---------|-----|
| 1 | Kayıt olma ve giriş | CRITICAL | E2E |
| 2 | Şarkı arama | HIGH | Integration |
| 3 | Şarkı dinleme | CRITICAL | E2E |
| 4 | Çalma listesi oluşturma | MEDIUM | Integration |
| 5 | Albüm keşfetme | MEDIUM | E2E |
| 6 | Sanatçı takip etme | LOW | Unit |
| 7 | Beğenme/oylama | LOW | Unit |
| 8 | Paylaşım | LOW | Integration |
| 9 | Profil düzenleme | MEDIUM | Integration |
| 10 | Abonelik yönetimi | HIGH | E2E |

#### 4.1.2 Professional (Profesyonel Üretici)

| Özellik | Değer |
|---------|-------|
| Hedef | Stüdyo üretimi, miksaj, mastering |
| Cihaz | Masaüstü (Windows/macOS) |
| Teknik bilgi | Yüksek |
| Hassasiyet | Ses kalitesi, gecikme |
| Ana akış | EQ ayarlama → DSP zinciri → Kayıt |
| Frekans | Günde 6-8 saat |

**Test Senaryoları:**
| # | Senaryo | Öncelik | Tip |
|---|---------|---------|-----|
| 1 | 31-band EQ ayarlama | CRITICAL | Unit |
| 2 | DSP zinciri oluşturma | CRITICAL | Integration |
| 3 | ASIO driver bağlantısı | CRITICAL | Integration |
| 4 | Çoklu kanal kaydı | HIGH | E2E |
| 5 | Preset kaydetme/yükleme | MEDIUM | Unit |
| 6 | Latency ölçümü | HIGH | Performance |
| 7 | 8.1 surround konfigürasyonu | HIGH | Integration |
| 8 | Plugin_hosting testi | MEDIUM | Integration |
| 9 | MIDI kontrolü | MEDIUM | Integration |
| 10 | Export (WAV/FLAC) | HIGH | E2E |

#### 4.1.3 DJ (Canlı Performans)

| Özellik | Değer |
|---------|-------|
| Hedef | Canlı performans, miksaj |
| Cihaz | Masaüstü + DJ controller |
| Teknik bilgi | Orta-Yüksek |
| Hassasiyet | Gecikme, kararlılık |
| Ana akış | Parça seç → Cue → Mix → Perform |
| Frekans | Haftada 2-3 performans |

**Test Senaryoları:**
| # | Senaryo | Öncelik | Tip |
|---|---------|---------|-----|
| 1 | Çift çıkışlı streaming | CRITICAL | Integration |
| 2 | Hot cue noktası ekleme | HIGH | Unit |
| 3 | Tempo eşleştirme (BPM sync) | CRITICAL | Unit |
| 4 | Crossfade ayarlama | HIGH | Unit |
| 5 | Loop noktası belirleme | MEDIUM | Unit |
| 6 | FX efekt uygulama | MEDIUM | Integration |
| 7 | Performans kaydı | HIGH | E2E |
| 8 | Session kurtarma | HIGH | E2E |
| 9 | Multi-deck (4 deck) | MEDIUM | Integration |
| 10 | Timecode VINYL | LOW | Integration |

#### 4.1.4 Admin (Sistem Yöneticisi)

| Özellik | Değer |
|---------|-------|
| Hedef | Sistem yönetimi, kullanıcı yönetimi |
| Cihaz | Masaüstü |
| Teknik bilgi | Yüksek |
| Hassasiyet | Güvenlik, denetim |
| Ana akış | Dashboard → Kullanıcı → Log → Config |
| Frekans | Günde 4-6 saat |

**Test Senaryoları:**
| # | Senaryo | Öncelik | Tip |
|---|---------|---------|-----|
| 1 | Kullanıcı listeleme | CRITICAL | Integration |
| 2 | Rol değiştirme | CRITICAL | Integration |
| 3 | Hesap askıya alma | HIGH | E2E |
| 4 | Log inceleme | MEDIUM | Integration |
| 5 | Sistem konfigürasyonu | HIGH | Integration |
| 6 | Rate limit yönetimi | MEDIUM | Unit |
| 7 | API key yönetimi | HIGH | E2E |
| 8 | Backup/restore | HIGH | E2E |
| 9 | Performans monitoring | MEDIUM | Integration |
| 10 | Güvenlik audit | CRITICAL | E2E |

#### 4.1.5 Car User (Araç İçi Kullanıcı)

| Özellik | Değer |
|---------|-------|
| Hedef | Sürüş sırasında müzik dinleme |
| Cihaz | Araç içi bilgi-eğlence (Raspberry Pi 5) |
| Teknik bilgi | Düşük |
| Hassasiyet | Dikkat dağıtıcı olmama, güvenli kullanım |
| Ana akış | Başlat → Sesle kontrol → Dinle |
| Frekans | Günde 1-2 saat sürüş |

**Test Senaryoları:**
| # | Senaryo | Öncelik | Tip |
|---|---------|---------|-----|
| 1 | Otomatik başlangıç | CRITICAL | E2E |
| 2 | Sesle kontrol | CRITICAL | Integration |
| 3 | Büyük buton arayüzü | HIGH | Unit |
| 4 | Bluetooth bağlama | HIGH | Integration |
| 5 | Offline mod | MEDIUM | E2E |
| 6 | GPS tabanlı playlist | LOW | Integration |
| 7 | Güvenlik modu (hareket halinde) | CRITICAL | E2E |
| 8 | Düşük kaynak kullanımı | HIGH | Performance |
| 9 | Hızlı başlangıç (<3s) | HIGH | Performance |
| 10 | Cihaz bağlantı/kopma | MEDIUM | Integration |

#### 4.1.6 Home User (Ev Medya Merkezi)

| Özellik | Değer |
|---------|-------|
| Hedef | Evde müzik dinleme, multi-room |
| Cihaz | PC/Laptop, NAS, multi-room hoparlörler |
| Teknik bilgi | Orta |
| Hassasiyet | Ses kalitesi, çoklu oda senkronizasyonu |
| Ana akış | Oda seç → Müzik seç → Dinle → Multi-room |
| Frekans | Günde 3-4 saat |

**Test Senaryoları:**
| # | Senaryo | Öncelik | Tip |
|---|---------|---------|-----|
| 1 | Multi-room senkronizasyon | CRITICAL | E2E |
| 2 | Oda ekleme/çıkarma | HIGH | Integration |
| 3 | Farklı oda farklı müzik | MEDIUM | E2E |
| 4 | Volume grouping | MEDIUM | Unit |
| 5 | NAS medya erişimi | HIGH | Integration |
| 6 | Scheduled playback | LOW | Integration |
| 7 | Intercom (oda arası iletişim) | LOW | Integration |
| 8 | AirPlay/DLNA | MEDIUM | Integration |
| 9 | Kalite seçimi (FLAC/MP3) | MEDIUM | Unit |
| 10 | Uzaktan kontrol | HIGH | E2E |

### 4.2 Test Piramidi

```
        ┌─────────┐
        │  E2E    │ %10
        │ 10 test │
        ├─────────┤
        │Integration│ %20
        │ 20 test  │
        ├─────────┤
        │  Unit   │ %70
        │ 70 test │
        └─────────┘
```

#### 4.2.1 Test Dağılımı

| Seviye | Oran | Min Test | Framework | Süre |
|--------|------|----------|-----------|------|
| Unit | %70 | 70 test | PHPUnit 11 / Vitest | <1s/test |
| Integration | %20 | 20 test | PHPUnit 11 / Vitest | <5s/test |
| E2E | %10 | 10 test | Playwright 1.40 | <30s/test |

#### 4.2.2 Kapsam Hedefleri

| Modül | Minimum | Hedef | Kaynak |
|-------|---------|-------|--------|
| Backend (PHP) | ≥%80 | ≥%90 | PHPUnit 11 |
| Frontend (JS) | ≥%80 | ≥%90 | Vitest |
| Audio Engine (C++) | ≥%80 | ≥%90 | Google Test |
| Download Service | ≥%80 | ≥%90 | Vitest |
| Security | ≥%90 | ≥%95 | OWASP checklist |

### 4.3 Test Senaryoları

#### 4.3.1 Auth Flow Testleri

| # | Senaryo | Persona | Adımlar | Beklenen |
|---|---------|---------|---------|----------|
| 1 | Yeni kullanıcı kayıt | Casual | Email + şifre → Doğrulama → Giriş | Başarılı kayıt |
| 2 | Giriş yapma | Tümü | Email + şifre → Token | Başarılı auth |
| 3 | Şifre sıfırlama | Tümü | Email → Link → Yeni şifre | Başarılı sıfırlama |
| 4 | Session timeout | Tümü | 3600s bekleme → Otomatik auth | Yeniden auth |
| 5 | Çift faktörlü auth | Admin | Şifre + SMS/TOTP | Ek doğrulama |

#### 4.3.2 Music Flow Testleri

| # | Senaryo | Persona | Adımlar | Beklenen |
|---|---------|---------|---------|----------|
| 1 | Şarkı arama | Casual | Query → Sonuç listesi | İlgili sonuçlar |
| 2 | Şarkı dinleme | Casual | Seç → Player → Play | Ses çıkışı |
| 3 | Çalma listesi oluşturma | Casual | Yeni → İsim → Ekle | Oluşturuldu |
| 4 | Albüm keşfetme | Professional | Kategori → Filtrele | Profesyonel sonuçlar |
| 5 | İndirme | Casual | Seç → Kalite → İndir | Dosya kaydedildi |

#### 4.3.3 Audio Engine Testleri

| # | Senaryo | Persona | Adımlar | Beklenen |
|---|---------|---------|---------|----------|
| 1 | ASIO bağlantısı | Professional | Driver seç → Buffer ayarla | Bağlantı |
| 2 | EQ ayarlama | Professional | Band seç → Gain/Q ayarla | Ses değişimi |
| 3 | DSP zinciri | Professional | Efekt ekle → Sırala | Zincir uygulandı |
| 4 | Latency ölçümü | Professional | Test başlat → Ölç | <10ms |
| 5 | 8.1 surround | Professional | Kanal ata → Test | Tüm kanallar aktif |

#### 4.3.4 Security Testleri

| # | Senaryo | Persona | Adımlar | Beklenen |
|---|---------|---------|---------|----------|
| 1 | CSRF koruması | Admin | Token olmadan POST | Reddedildi |
| 2 | SQL injection | — | Şifre alanı: `' OR 1=1 --` | Reddedildi |
| 3 | XSS attack | — | `<script>alert(1)</script>` | Sanitize edildi |
| 4 | Rate limit | — | 61 istek/dakika | 429 Too Many Requests |
| 5 | Yetkisiz erişim | — | Token olmadan /admin/* | 401 Unauthorized |

#### 4.3.5 Performance Testleri

| # | Senaryo | Persona | Metrik | Hedef |
|---|---------|---------|--------|-------|
| 1 | Sayfa yüklenme | Casual | TTFB | <200ms |
| 2 | API yanıt | Casual | Response time | <100ms |
| 3 | Ses gecikmesi | Professional | Latency | <10ms (ASIO) |
| 4 | Eşzamanlı kullanıcı | — | Concurrency | 1000+ |
| 5 | Bellek kullanımı | Car | Memory | <512MB |

### 4.4 Test Verisi Yönetimi

#### 4.4.1 Fixture'lar

```php
// tests/fixtures/users.php
$users = [
    'casual' => [
        'email' => 'casual@test.com',
        'password' => 'Test1234!',
        'role' => 'user',
        'preferences' => ['theme' => 'default', 'quality' => 'mp3-320']
    ],
    'professional' => [
        'email' => 'pro@test.com',
        'password' => 'Test1234!',
        'role' => 'premium',
        'preferences' => ['theme' => 'studio', 'quality' => 'flac-24bit']
    ],
    'admin' => [
        'email' => 'admin@test.com',
        'password' => 'Test1234!',
        'role' => 'admin',
        'preferences' => ['theme' => 'admin', 'quality' => 'flac-32bit']
    ]
];
```

#### 4.4.2 Test Verisi Kuralları

| Kural | Değer | İhlal |
|-------|-------|-------|
| Production verisi yasak | Sadece test fixture'ları | Veri sızıntısı |
| Temizleme zorunlu | Her test sonrası DB temizle | Test kontaminasyonu |
| Bağımsızlık | Her test kendi verisini oluşturur | Sıralama bağımlılığı |
| Seed data | Başlangıç verisi reproducible | Test tekrarlanabilirliği |

### 4.5 Test Ortamı

#### 4.5.1 Ortam Yapısı

| Ortam | Amaç | Veritabanı | Konfigürasyon |
|-------|------|-----------|---------------|
| Local | Geliştirme | SQLite | `.env.local` |
| CI | Otomatik test | SQLite | `.env.test` |
| Staging | Entegrasyon | MySQL test DB | `.env.staging` |
| Production | Üretim | MySQL 9 BCNF | `.env.production` |

#### 4.5.2 Test Komutları

```bash
# Backend testleri
php vendor/bin/phpunit --coverage-html=coverage/

# Frontend testleri
npx vitest --coverage

# E2E testleri
npx playwright test

# C++ testleri
cmake --build build && ctest --test-dir build

# Tüm testler
composer test && npm test && ctest
```

### 4.6 Coverage Raporlama

#### 4.6.1 Coverage Metrikleri

| Metrik | Değer | Hedef |
|--------|-------|-------|
| Line coverage | ≥%80 | ≥%90 |
| Branch coverage | ≥%80 | ≥%90 |
| Function coverage | ≥%80 | ≥%90 |
| Mutation score | ≥%70 | ≥%80 |

#### 4.6.2 Coverage Kontrol Akışı

```
Test çalıştır
  → Coverage raporu oluştur
    → Eşik kontrolü (%80 min)
      → Geçti → Devam
      → Geçmedi → CI build başarısız
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Production verisi ile test | Fixture ile test | Veri sızıntısı |
| Manuel test adımları | Otomatik test | Tekrarlanamazlık |
| Eksik coverage raporu | Tam coverage | Kalite düşüşü |
| Flaky test bırakma | Test sabitleme | Güven kaybı |
| Mock aşırı kullanımı | Gerçek entegrasyon | Yanlış pozitif |
| Tek persona ile test | Tüm personalar | Kapsam eksikliği |
| Coverage alt sınırı atlatma | %80 zorunlu | Kalite düşüşü |
| Test yorumlama | Test okunabilirliği | Bakım zorluğu |
| Async test hataları | Proper await/async | Race condition |
| Environment bağımlılığı | Containerized test | Yanlış sonuç |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Flaky test | Network gecikmesi | Retry + timeout |
| Test kontaminasyonu | Paylaşımlı state | DB temizleme + isolation |
| Coverage düşük | Yeni feature | Ek test yazma zorunluluğu |
| Persona değişikliği | Yeni kullanıcı türü | Persona ekleme süreci |
| Cross-platform test | Farklı OS | Container ile test |
| Parallel test | Eşzamanlı çalışma | DB isolation + locks |
| Seed data değişikliği | DB migration | Fixture güncelleme |
| Mock/real uyumsuzluğu | API değişikliği | Contract testing |
| Performance regresyonu | Yeni kod | Benchmark testleri |
| Security exploit | Yeni attack vektörü | OWASP checklist güncelleme |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Coverage minimum %80 — Altında merge yasak | CI build başarısız |
| 2 | Persona bazlı test — Her persona için min 5 test | Kapsam eksikliği |
| 3 | Flaky test tolerance %0 — Flaky test merge edilmez | Güven kaybı |
| 4 | Production verisi yasak — Testlerde gerçek veri kullanılmaz | Veri sızıntısı |
| 5 | E2E test zorunlu — Her persona için min 1 E2E | Kullanıcı deneyimi riski |
| 6 | Security test zorunlu — OWASP Top 10 kapsamı | Güvenlik açığı |
| 7 | Performance test — TTFB <200ms, API <100ms | Performans düşüşü |
| 8 | Test bağımsızlığı — Sıralamaya bağlı test yasak | Yanlış sonuç |
| 9 | Coverage trend — Düşüş trendi varsa uyarı | Kalite düşüşü |
| 10 | Regression test — Her bug fix sonrası | Yeni hata çıkması |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend test standartları |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory | DB test stratejisi |
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Cross-domain test |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination | Test doğruluğu |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware | Audio test ortamı |
| [[ADR-019-per-os-neva-player]] | Per-OS player | Cross-platform test |
| [[ADR-040-database-authority]] | DB authority | Test coverage |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-040-database-authority]] | Coverage hedefleri |
| § 4.1 Persona | [[personas/index]] | Persona tanımları |
| § 4.2 Piramit | [[testing/strategy]] | Test stratejisi |
| § 4.3 Senaryo | [[testing/e2e-template]] | E2E şablonu |
| § 4.4 Fixture | [[ADR-002-pdo-mandatory-no-orm]] | DB test verisi |
| § 4.5 Ortam | [[architecture/l0-infrastructure]] | Test altyapısı |
| § 5 Yasak | [[ADR-022-database-hardened-security]] | Güvenlik testi |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 ADR | [[testing/coverage-targets]] | Kapsama hedefleri |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Persona** | Kullanıcı türü temsili (test odaklı) |
| **Unit Test** | Tek bir fonksiyonu/test eden test |
| **Integration Test** | Birden fazla bileşeni birlikte test eden test |
| **E2E Test** | Tüm akışı baştan sona test eden test |
| **Coverage** | Kodun test edilme oranı |
| **Flaky Test** | Bazen geçip bazen başarısız olan test |
| **Fixture** | Test verisi hazırlama mekanizması |
| **Mock** | Gerçek nesnelerin sahte versiyonları |
| **Regression Test** | Yeni değişikliğin eski kodu bozmadığını test |
| **Mutation Score** | Testlerin hata bulma gücünü ölçen metrik |
| **Contract Testing** | API sözleşme testi |
| **Performance Test** | Hız ve kaynak testi |
| **OWASP** | Open Web Application Security Project |
| **CI** | Continuous Integration — Sürekli entegrasyon |
| **PHPUnit** | PHP test framework'ü |
| **Vitest** | JavaScript test framework'ü |
| **Playwright** | E2E test framework'ü |
| **Google Test** | C++ test framework'ü |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| SSOT Authority | ADR-023 Persona-Driven Testing |
| Last Updated | 2026-08-08 |
| ADR References | 7 |
| Cross References | 9 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 18 |
| Personas | 6 (Casual, Professional, DJ, Admin, Car, Home) |
| Test Levels | 3 (Unit, Integration, E2E) |
| Test Suites | 5 (Auth, Music, Audio, Security, Performance) |
| Total Test Cases | 50+ (min 10 per persona) |
| Coverage Target | ≥80% min, ≥90% hedef |
| Frameworks | 4 (PHPUnit, Vitest, Playwright, Google Test) |
| Environments | 4 (Local, CI, Staging, Production) |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
