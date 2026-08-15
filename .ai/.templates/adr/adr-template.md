---
type: template
category: adr
title: "CoreMusic — Architecture Decision Record (ADR) Master Template"
version: 1.0.0
created: 2026-08-07
updated: 2026-08-07
authority: Vault Steward
governance: Red Team • Human Mode • Truth Mode
usage: "Yeni ADR oluştururken bu dosyayı kopyalayın → NNN-numaralandırın → Doldurun"
related:
  - "[[decisions/index]]"
  - "[[ADR-042-vault-restructuring-2026-08-03]]"
  - "[[brain.md]]"
  - "[[AGENTS.md]]"
tags: [template, adr, architecture, decision-record, standard]
---

# CoreMusic — Architecture Decision Record (ADR) Master Template

**Bu dosya bir şablondur.** Yeni ADR oluştururken bu dosyayı kopyalayın, `NNN` kısmını sıradaki numara ile değiştirin ve tüm bölümleri doldurun.

**Kullanım:** `cp .ai/.templates/adr-template.md .ai/decisions/accepted/ADR-NNN-baslik.md`

---

## 📋 Şablon Kullanım Kılavuzu

### Adım 1: Numara Belirleme

Mevcut ADR listesini kontrol edin: `[[decisions/index]]`

Son ADR numarasını bulun. Yeni ADR için bir sonraki numarayı kullanın.

```
Örnek: Son ADR = ADR-050 → Yeni ADR = ADR-051
```

### Adım 2: Dosyayı Kopyalama

```bash
cp .ai/.templates/adr-template.md .ai/decisions/accepted/ADR-NNN-baslik.md
```

Dosya adı kuralları:
- Küçük harf ve tire ile yazılır
- Anlamlı bir başlık içermelidir
- Örnek: `ADR-051-multi-room-audio-sync.md`

### Adım 3: Frontmatter Doldurma

`---` arasındaki YAML alanlarını doldurun. Tüm alanlar zorunludur.

### Adım 4: Bölümleri Doldurma

Her bölümü sırasıyla doldurun. Boş bırakılan bölümler `N/A` olarak işaretlenmelidir.

### Adım 5: Cross-Reference Ekleme

İlgili ADR'leri, mimari dosyaları ve vault referanslarını `references` alanına ekleyin.

### Adım 6: Onay Süreci

1. Draft olarak kaydedin (`status: draft`)
2. Tech Lead incelemesi
3. Security Engineer incelemesi (güvenlik ile ilgili ADR'ler için)
4. Onay → `status: active` olarak güncelleyin
5. Frozen ADR'ler için `status: frozen` kullanın

---

## 📄 ADR ŞABLONU

---

```yaml
---
type: decision
id: "NNN"
title: "ADR-NNN: Kısa ve Anlamlı Başlık"
category: "frontend|backend|database|security|audio|ecosystem|vault|testing|devops"
status: "draft|active|frozen|deprecated|superseded"
date: "YYYY-MM-DD"
updated: "YYYY-MM-DD"
authority: "Sorumlu kişi/grup"
governance: "Red Team • Human Mode • Truth Mode"
supersedes: null  # Veya geçersiz kılan ADR referansı
version: 1.0.0
tags: [tag1, tag2, tag3]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[WORKFLOW.md]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
  # İlgili diğer ADR'ler buraya eklenir
---
```

---

## 1. Executive Summary (Yönetici Özeti)

**Bu bölüm kısa ve öz olmalıdır.** 2-3 paragraf ile kararın ne olduğu, neden alındığı ve temel sonuçları özetlenir.

### 1.1 Kararın Özeti

[YENİ ADR-NNN: Kararın kısa bir özeti. Ne kararlaştırıldı?]

### 1.2 Temel Gerekçe

[Bu karar neden alındı? Ana itici güçler nelerdir?]

### 1.3 Beklenen Sonuçlar

[Bu kararın en önemli sonuçları nelerdir?]

---

## 2. Status (Durum)

| Alan | Değer |
|------|-------|
| **Durum** | draft / active / frozen / deprecated / superseded |
| **Versiyon** | 1.0.0 |
| **Oluşturma Tarihi** | YYYY-MM-DD |
| **Son Güncelleme** | YYYY-MM-DD |
| **Otorite** | Sorumlu kişi veya grup |
| **Onay** | Red Team • Human Mode • Truth Mode |
| **Supersedes** | null veya ADR-XXX referansı |

### 2.1 Durum Tanımları

| Durum | Tanım | Kullanım |
|-------|-------|----------|
| **draft** | Taslak, henüz onaylanmamış | ADR yazım aşamasında |
| **active** | Onaylanmış, uygulanıyor | Güncel kararlar |
| **frozen** | Dondurulmuş, değiştirilemez | 001-037 arası ADR'ler |
| **deprecated** | Kullanımdan kaldırılmış | Eski kararlar |
| **superseded** | Başka bir ADR tarafından geçersiz kılınmış | Yeni ADR ile değişmiş |

### 2.2 Durum Geçiş Kuralları

```
draft → active → frozen
  ↓        ↓
deprecated  superseded
```

- `draft` → `active`: Tech Lead + Security onayı gerektirir
- `active` → `frozen`: Sadece kritik kararlar için, değiştirilemez
- `active` → `deprecated`: Yeni ADR ile geçersiz kılınır
- `active` → `superseded`: Başka bir ADR tarafından değiştirilir

---

## 3. Context (Bağlam)

Bu bölüm, kararın alındığı teknik ve iş bağlamını detaylı olarak açıklar.

### 3.1 Problem Tanımı

[Bu karar hangi sorunu çözüyor? Net bir şekilde tanımlayın.]

### 3.2 Mevcut Durum

[Mevcut durum nedir? Hangi teknolojiler, süreçler veya yapılar kullanılıyor?]

### 3.3 İtici Güçler (Drivers)

| # | Güç | Açıklama | Kritiklik |
|---|-----|----------|-----------|
| 1 | [Güç adı] | [Açıklama] | Yüksek/Orta/Düşük |
| 2 | [Güç adı] | [Açıklama] | Yüksek/Orta/Düşük |
| 3 | [Güç adı] | [Açıklama] | Yüksek/Orta/Düşük |

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Açıklama | İlgili ADR |
|-----------|----------|------------|
| [Kısıtlama 1] | [Açıklama] | ADR-XXX |
| [Kısıtlama 2] | [Açıklama] | ADR-XXX |

### 3.5 Ekosistem Etkileşimi

[Bu karar diğer servisleri, panelleri veya katmanları nasıl etkiliyor?]

| Etkilenen Alan | Etki Türü | Açıklama |
|---------------|-----------|----------|
| [Alan 1] | Doğrudan/Endirekt | [Açıklama] |
| [Alan 2] | Doğrudan/Endirekt | [Açıklama] |

### 3.6 İlgili ADR'ler

| ADR | Başlık | İlişki Türü |
|-----|--------|-------------|
| ADR-XXX | [Başlık] | Bağımlı / Bağımsız / Çelişen |

---

## 4. Decision (Karar)

Bu bölüm, alınan kararı net ve uygulanabilir şekilde tanımlar.

### 4.1 Karar Bildirimi

**[Net bir karar cümlesi. "CoreMusic X kullanır/kabul eder/yapar."]**

### 4.2 Kesin Kurallar

| # | Kural | Durum | İlgili ADR |
|---|-------|-------|------------|
| 1 | [Kural 1] | ✅ Zorunlu / ❌ Yasak / ⚠️ Tercih | ADR-XXX |
| 2 | [Kural 2] | ✅ Zorunlu / ❌ Yasak / ⚠️ Tercih | ADR-XXX |
| 3 | [Kural 3] | ✅ Zorunlu / ❌ Yasak / ⚠️ Tercih | ADR-XXX |

### 4.3 Kararın Gerekçesi

[Bu karar neden bu alternatifler arasından seçildi? Somut gerekçeler.]

### 4.4 Uygulama Detayları

[Teknik uygulama nasıl yapılacak? Adım adım açıklama.]

#### 4.4.1 Mimari Bileşenler

[Mimari diyagram, bileşen ilişkileri]

#### 4.4.2 Veri Akışı

[Veri nasıl akacak? Hangi formatlarda?]

#### 4.4.3 API Sözleşmesi

[Yeni API endpoint'leri, request/response formatları]

### 4.5 Kod Örnekleri

```php
// PHP Kod Örneği
// Dosya: [dosya yolu]
```

```javascript
// JavaScript Kod Örneği
// Dosya: [dosya yolu]
```

```sql
-- SQL Kod Örneği
-- Dosya: [dosya yolu]
```

### 4.6 Konfigürasyon Değişiklikleri

| Dosya | Eski Değer | Yeni Değer | Açıklama |
|-------|-----------|-----------|----------|
| [dosya] | [eski] | [yeni] | [açıklama] |

---

## 5. Architecture (Mimari)

### 5.1 Mimari Diyagram

```
[DiyagramASCII veya PlantUML]
```

### 5.2 Katman Etkileşimi

| Katman | Etki | Açıklama |
|--------|------|----------|
| **L0 Infrastructure** | [Etki] | [Açıklama] |
| **L1 Security** | [Etki] | [Açıklama] |
| **L2 Routing** | [Etki] | [Açıklama] |
| **L3 Presentation** | [Etki] | [Açıklama] |

### 5.3 Servis Etkileşimi

| Servis | Etki | Port | Açıklama |
|--------|------|------|----------|
| Control Service | [Etki] | 81 | [Açıklama] |
| Media Service | [Etki] | 5000/6000 | [Açıklama] |
| Audio Service | [Etki] | 9741/9742 | [Açıklama] |
| Download Service | [Etki] | 3001 | [Açıklama] |

---

## 6. Alternatives Considered (Düşünülen Alternatifler)

### 6.1 Alternatif 1: [Alternatif Adı]

**Açıklama:** [Bu alternatifin kısa açıklaması]

**Avantajlar:**
- [Avantaj 1]
- [Avantaj 2]

**Dezavantajlar:**
- [Dezavantaj 1]
- [Dezavantaj 2]

**Neden Reddedildi:** [Red gerekçesi]

### 6.2 Alternatif 2: [Alternatif Adı]

**Açıklama:** [Bu alternatifin kısa açıklaması]

**Avantajlar:**
- [Avantaj 1]
- [Avantaj 2]

**Dezavantajlar:**
- [Dezavantaj 1]
- [Dezavantaj 2]

**Neden Reddedildi:** [Red gerekçesi]

### 6.3 Alternatif 3: [Alternatif Adı]

**Açıklama:** [Bu alternatifin kısa açıklaması]

**Avantajlar:**
- [Avantaj 1]
- [Avantaj 2]

**Dezavantajlar:**
- [Dezavantaj 1]
- [Dezavantaj 2]

**Neden Reddedildi:** [Red gerekçesi]

### 6.4 Karar Matrisi

| Kriter | Ağırlık | Alternatif 1 | Alternatif 2 | Alternatif 3 | Seçilen |
|--------|---------|-------------|-------------|-------------|---------|
| Performans | %25 | [Puan] | [Puan] | [Puan] | [X] |
| Güvenlik | %25 | [Puan] | [Puan] | [Puan] | [X] |
| Bakım | %20 | [Puan] | [Puan] | [Puan] | [X] |
| Maliyet | %15 | [Puan] | [Puan] | [Puan] | [X] |
| Ölçeklenebilirlik | %15 | [Puan] | [Puan] | [Puan] | [X] |

---

## 7. Consequences (Sonuçlar)

### 7.1 Olumlu Sonuçlar

| # | Sonuç | Etki Seviyesi |
|---|-------|---------------|
| 1 | [Olumlu sonuç 1] | Yüksek/Orta/Düşük |
| 2 | [Olumlu sonuç 2] | Yüksek/Orta/Düşük |
| 3 | [Olumlu sonuç 3] | Yüksek/Orta/Düşük |

### 7.2 Olumsuz Sonuçlar

| # | Sonuç | Risk Seviyesi | Mitigation |
|---|-------|---------------|------------|
| 1 | [Olumsuz sonuç 1] | Yüksek/Orta/Düşük | [Çözüm] |
| 2 | [Olumsuz sonuç 2] | Yüksek/Orta/Düşük | [Çözüm] |
| 3 | [Olumsuz sonuç 3] | Yüksek/Orta/Düşük | [Çözüm] |

### 7.3 Nötr Sonuçlar

| # | Sonuç | Açıklama |
|---|-------|----------|
| 1 | [Nötr sonuç 1] | [Açıklama] |
| 2 | [Nötr sonuç 2] | [Açıklama] |

### 7.4 Risk Matrisi

| Risk | Olasılık | Etki | Skor | Mitigation |
|------|----------|------|------|------------|
| [Risk 1] | Yüksek/Orta/Düşük | Yüksek/Orta/Düşük | [1-9] | [Çözüm] |
| [Risk 2] | Yüksek/Orta/Düşük | Yüksek/Orta/Düşük | [1-9] | [Çözüm] |
| [Risk 3] | Yüksek/Orta/Düşük | Yüksek/Orta/Düşük | [1-9] | [Çözüm] |

---

## 8. Implementation Roadmap (Uygulama Yol Haritası)

### 8.1 Kısa Vadeli (0-3 Ay)

| # | Görev | Sorumlu | Süre | Durum |
|---|-------|---------|------|-------|
| 1 | [Görev 1] | [Sorumlu] | [Süre] | ⏳ |
| 2 | [Görev 2] | [Sorumlu] | [Süre] | ⏳ |
| 3 | [Görev 3] | [Sorumlu] | [Süre] | ⏳ |

### 8.2 Orta Vadeli (3-6 Ay)

| # | Görev | Sorumlu | Süre | Durum |
|---|-------|---------|------|-------|
| 1 | [Görev 1] | [Sorumlu] | [Süre] | ⏳ |
| 2 | [Görev 2] | [Sorumlu] | [Süre] | ⏳ |

### 8.3 Uzun Vadeli (6+ Ay)

| # | Görev | Sorumlu | Süre | Durum |
|---|-------|---------|------|-------|
| 1 | [Görev 1] | [Sorumlu] | [Süre] | ⏳ |
| 2 | [Görev 2] | [Sorumlu] | [Süre] | ⏳ |

---

## 9. Testing Strategy (Test Stratejisi)

### 9.1 Test Kapsamı

| Test Türü | Hedef | Mevcut Durum | Hedef |
|-----------|-------|-------------|-------|
| **Unit Test** | %80+ | [%] | %90+ |
| **Integration Test** | %70+ | [%] | %80+ |
| **E2E Test** | Kritik akışlar | [Durum] | Tüm kritik akışlar |

### 9.2 Test Senaryoları

| # | Senaryo | Türü | Beklenen Sonuç |
|---|---------|------|----------------|
| 1 | [Senaryo 1] | Unit/Integration/E2E | [Sonuç] |
| 2 | [Senaryo 2] | Unit/Integration/E2E | [Sonuç] |
| 3 | [Senaryo 3] | Unit/Integration/E2E | [Sonuç] |

### 9.3 Test Komutları

```bash
# Unit Testler
cd music.coremusic.net && vendor/bin/phpunit

# Frontend Testler
cd assets.coremusic.net && npx vitest run

# E2E Testler
npx playwright test
```

---

## 10. Security Considerations (Güvenlik Değerlendirmesi)

### 10.1 OWASP Top 10:2025 Eşleme

| OWASP Sınıfı | Durum | Açıklama |
|--------------|-------|----------|
| A01: Broken Access Control (SSRF dahil) | ✅/⚠️/❌ | [Açıklama] |
| A02: Security Misconfiguration | ✅/⚠️/❌ | [Açıklama] |
| A03: Software Supply Chain Failures | ✅/⚠️/❌ | [Açıklama] |
| A04: Cryptographic Failures | ✅/⚠️/❌ | [Açıklama] |
| A05: Injection | ✅/⚠️/❌ | [Açıklama] |
| A06: Insecure Design | ✅/⚠️/❌ | [Açıklama] |
| A07: Authentication Failures | ✅/⚠️/❌ | [Açıklama] |
| A08: Software/Data Integrity Failures | ✅/⚠️/❌ | [Açıklama] |
| A09: Security Logging & Alerting Failures | ✅/⚠️/❌ | [Açıklama] |
| A10: Mishandling of Exceptional Conditions | ✅/⚠️/❌ | [Açıklama] |

### 10.2 Tehdit Modeli

| Tehdit | Olasılık | Etki | Kontrol |
|--------|----------|------|---------|
| [Tehdit 1] | Yüksek/Orta/Düşük | Yüksek/Orta/Düşük | [Kontrol] |
| [Tehdit 2] | Yüksek/Orta/Düşük | Yüksek/Orta/Düşük | [Kontrol] |

### 10.3 Şifreleme Standardı

| Veri Türü | Algoritma | Key Length | IV | Tag |
|-----------|-----------|------------|-----|-----|
| Hassas Veri | AES-256-GCM | 256-bit | 96-bit | 16-byte |
| Şifre | Argon2id | N/A | N/A | N/A |
| Session | HMAC-SHA256 | 256-bit | N/A | N/A |

### 10.4 CSRF/Koruma

| Mekanizma | Durum | Detay |
|-----------|-------|-------|
| CSRF Token | ✅ Zorunlu | `csrf_token` key (ADR-010) |
| CSP Nonce | ✅ Zorunlu | Per-request nonce (ADR-012) |
| SameSite | ✅ Zorunlu | Lax (ADR-011) |

---

## 11. Performance Impact (Performans Etkisi)

### 11.1 Performans Bütçesi

| Metrik | Mevcut | Hedef | Fark |
|--------|--------|-------|------|
| FCP (First Contentful Paint) | [ms] | < 1.5s | [ms] |
| LCP (Largest Contentful Paint) | [ms] | < 2.5s | [ms] |
| CLS (Cumulative Layout Shift) | [ms] | < 0.1 | [ms] |
| TTI (Time to Interactive) | [ms] | < 3.0s | [ms] |

### 11.2 Benchmark Sonuçları

| Test | Ortam | Sonuç | Hedef |
|------|-------|-------|-------|
| [Test 1] | [Ortam] | [Sonuç] | [Hedef] |
| [Test 2] | [Ortam] | [Sonuç] | [Hedef] |

### 11.3 Kaynak Kullanımı

| Kaynak | Mevcut | Tahmini | Limit |
|--------|--------|---------|-------|
| CPU | [%] | [%] | [%] |
| Bellek | [MB] | [MB] | [MB] |
| Disk | [GB] | [GB] | [GB] |
| Ağ | [Mbps] | [Mbps] | [Mbps] |

---

## 12. Rollback Plan (Geri Alma Planı)

### 12.1 Geri Alma Senaryoları

| Senaryo | Tetikleyici | Geri Alma Adımları |
|---------|-------------|-------------------|
| Kritik Hata | Üretimde çökme | 1. [Adım] 2. [Adım] 3. [Adım] |
| Performans Düşüşü | %50+ yavaşlama | 1. [Adım] 2. [Adım] 3. [Adım] |
| Güvenlik İhlali | Sızma tespiti | 1. [Adım] 2. [Adım] 3. [Adım] |

### 12.2 Geri Alma Komutları

```bash
# Git rollback
git revert [commit-hash]

# Database rollback
php artisan migrate:rollback

# Config rollback
cp .env.backup .env
```

### 12.3 Geri Alma Süresi

| Senaryo | Tahmini Süre | SLA |
|---------|-------------|-----|
| Kritik Hata | [dakika] | [dakika] |
| Performans | [saat] | [saat] |
| Güvenlik | [dakika] | [dakika] |

---

## 13. Related Decisions (İlgili Kararlar)

| ADR | Başlık | İlişki Türü | Açıklama |
|-----|--------|-------------|----------|
| ADR-XXX | [Başlık] | Bağımlı / Bağımsız / Çelişen / Superseded | [Açıklama] |

---

## 14. Glossary (Sözlük)

| Terim | Tanım |
|-------|-------|
| [Terim 1] | [Tanım] |
| [Terim 2] | [Tanım] |
| [Terim 3] | [Tanım] |

---

## 15. Edge Cases (Sınır Durumlar)

| Durum | Belirti | Çözüm | İlgili ADR |
|-------|---------|-------|------------|
| [Durum 1] | [Belirti] | [Çözüm] | ADR-XXX |
| [Durum 2] | [Belirti] | [Çözüm] | ADR-XXX |
| [Durum 3] | [Belirti] | [Çözüm] | ADR-XXX |

---

## 16. Warnings (Kritik Uyarılar)

> [!WARNING]
> **Uyarı 1:** [Kritik uyarı açıklaması]

> [!WARNING]
> **Uyarı 2:** [Kritik uyarı açıklaması]

> [!DANGER]
> **Tehlike:** [Yaşamsal tehlike açıklaması]

---

## 17. Limitations (Bilinen Sınırlamalar)

| # | Sınırlama | Etki | Gelecek Çözüm |
|---|-----------|------|---------------|
| 1 | [Sınırlama 1] | [Etki] | [Gelecek çözüm] |
| 2 | [Sınırlama 2] | [Etki] | [Gelecek çözüm] |
| 3 | [Sınırlama 3] | [Etki] | [Gelecek çözüm] |

---

## 18. Dependencies (Bağımlılıklar)

### 18.1 Teknik Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım | Durum |
|------------|---------|---------|-------|
| [Bağımlılık 1] | [Versiyon] | [Kullanım] | ✅/⚠️/❌ |
| [Bağımlılık 2] | [Versiyon] | [Kullanım] | ✅/⚠️/❌ |

### 18.2 ADR Bağımlılıkları

| ADR | Bağımlılık Türü | Açıklama |
|-----|----------------|----------|
| ADR-XXX | Zorunlu/İsteğe Bağlı | [Açıklama] |

---

## 19. Future Roadmap (Gelecek Yol Haritası)

| Versiyon | Hedef | Tahmini Tarih |
|----------|-------|---------------|
| v1.1 | [Hedef] | [Tarih] |
| v1.2 | [Hedef] | [Tarih] |
| v2.0 | [Hedef] | [Tarih] |

---

## 20. Related Documents (İlgili Dokümanlar)

| Dosya | Amaç | Erişim |
|-------|------|--------|
| [[decisions/index]] | ADR indeksi | [[decisions/index]] |
| [[brain.md]] | Mühendislik kararları | [[brain.md]] |
| [[architecture/l0-infrastructure]] | L0 katmanı | [[architecture/l0-infrastructure]] |
| [[architecture/l1-security]] | L1 katmanı | [[architecture/l1-security]] |
| [[architecture/l2-routing]] | L2 katmanı | [[architecture/l2-routing]] |
| [[architecture/l3-presentation]] | L3 katmanı | [[architecture/l3-presentation]] |

---

## 21. Cross References (Çapraz Referanslar)

```
ADR-NNN
    │
    ├─► decisions/accepted/ADR-NNN.md (bu dosya)
    │
    ├─► decisions/index.md (indeks)
    │
    ├─► brain.md (mühendislik kararları)
    │
    ├─► architecture/**/ (ilgili katmanlar)
    │
    └─► ilgili kod dosyaları
```

---

## 22. Review History (İnceleme Geçmişi)

| Tarih | İnceleyen | Durum | Not |
|-------|-----------|-------|-----|
| YYYY-MM-DD | [İnceleyen] | Onaylandı/Reddedildi/Düzenlendi | [Not] |

---

## 23. Approval (Onay Matrisi

| Rol | Kişi | Onay | Tarih |
|-----|------|------|-------|
| Tech Lead | [İsim] | ✅/❌ | YYYY-MM-DD |
| Security Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Vault Steward | [İsim] | ✅/❌ | YYYY-MM-DD |

---

## 📝 Şablon Notları

### Bu Şablonu Kullanırken Dikkat Edilecekler

1. **Tüm bölümleri doldurun** — Boş bırakılan bölümler `N/A` olarak işaretlenmelidir
2. **Cross-reference ekleyin** — İlgili tüm ADR'leri ve dosyaları referans gösterin
3. **Kod örneği ekleyin** — Somut uygulama örnekleri verin
4. **Risk matrisi doldurun** — Tüm riskleri değerlendirin
5. **Test stratejisi tanımlayın** — Hangi testlerin çalıştırılacağını belirtin
6. **Güvenlik değerlendirmesi yapın** — OWASP eşleme yapın
7. **Performans etkisini ölçün** — Benchmark sonuçlarını ekleyin
8. **Geri alma planı hazırlayın** — Kötü senaryolar için plan yapın

### domain-Specific Template'ler

Farklı alanlar için özel template'ler mevcuttur:

| Template | Kullanım Alanı | Dosya |
|----------|---------------|-------|
| **ADR Frontend** | Frontend, CSS, SPA, UI | `adr-frontend-template.md` |
| **ADR Database** | Veritabanı, SQL, BCNF, Migration | `adr-database-template.md` |
| **ADR Security** | Güvenlik, OWASP, Şifreleme | `adr-security-template.md` |
| **ADR Audio** | Ses, DSP, Donanım, ASIO | `adr-audio-template.md` |

### ADR Yaşam Döngüsü

```
┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐
│  Draft  │───►│ Active  │───►│ Frozen  │───►│Archive │
└─────────┘    └─────────┘    └─────────┘    └─────────┘
     │              │              │
     │              │              │
     ▼              ▼              ▼
┌─────────┐    ┌─────────┐    ┌─────────┐
│Rejected │    │Deprecated│   │Superseded│
└─────────┘    └─────────┘    └─────────┘
```

---

*CoreMusic ADR Master Template v1.0.0 — 2026-08-07*
*Authority: Vault Steward*
*Governance: Red Team • Human Mode • Truth Mode*
