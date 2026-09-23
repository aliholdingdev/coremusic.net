---
reference_doc: Freelancer Technical Documentation v1.0
type: protocol
category: ai
title: "AI Düşünme Protokolü — Ultra Thinking Protocol"
date: 2026-09-19
updated: 2026-09-23
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# AI Düşünme Protokolü — Ultra Thinking Protocol

> *"Düşünmeden kod yazma, kod yazmadan plan yapma, plan yapmadan vault okuma."*

---

## Zorunlu Bağlantılar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Anayasa, 17 Hard Guardrail, kurallar |
| [[VISION.md]] | Ekosistem vizyonu, mülkiyet felsefesi, 6 sorun-çözüm matrisi |
| [[PROJECTS.md]] | CoreMusic nedir, 10 temel yetenek, 6 hedef kitle, kullanım senaryoları |
| [[AGENTS.md]] | §24 Ultra Thinking Protocol detayı |
| [[brain.md]] | ADR kararları, mimari kısıtlamalar |
| [[WORKFLOW.md]] | Süreçler, hard gate'ler, workflow'lar |
| [[glossary]] | Teknik terim tanımları |
| [[engine.md]] | Orkestrasyon, task dispatch, escalation |

---

## 1. Amaç

CoreMusic projesinde çalışan tüm AI agentlarının kullanacağı **düşünme ve karar verme protokolü**. Bu protokol, her kod satırı yazılmadan önce, her mimari karar alınmadan önce ve her plan yapılmadan önce uygulanması **zorunlu** olan düşünme disiplinini tanımlar. Tüm kararlar projenin temel vizyonu (mülkiyet odaklı felsefe, stüdyo ses kalitesi, kesintisiz yaşam deneyimi) ve hedeflenen 6 temel problem çözümü doğrultusunda verilir.

**Guardrail Bağlantısı:** [[CLAUDE.md]] §7 Guardrail #1 (Zero Code Before Plan), #3 (Zero Hallucination), #5 (SSOT), #14 (Human Approval Gate).

---

## 2. Düşünme Katmanları (4 Katman)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    ULTRA THINKING PROTOKOLÜ                          │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 4: STRATEJİK DÜŞÜNME                               │   │
│  │    Uzun vadeli etki, mimari bütünlük, teknik borç          │   │
│  │    Tetikleyici: Yeni özellik, mimari değişiklik, ADR       │   │
│  │    Araçlar: ADR-087 Master Plan, architecture/index.md     │   │
│  │    Süre: 5-15 dakika düşünme süresi                        │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 3: TAKTİKSEL DÜŞÜNME                               │   │
│  │    Kısa vadeli çözüm, optimizasyon, performans             │   │
│  │    Tetikleyici: Bug fix, refactor, optimizasyon            │   │
│  │    Araçlar: brain.md, ADR kararları, glossary              │   │
│  │    Süre: 3-8 dakika düşünme süresi                         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 2: OPERASYONEL DÜŞÜNME                              │   │
│  │    Günlük görevler, bug düzeltmeleri, test                 │   │
│  │    Tetikleyici: Debug, test yazma, küçük düzeltme          │   │
│  │    Araçlar: log.md, MEMORY.md, session context             │   │
│  │    Süre: 1-3 dakika düşünme süresi                         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 1: TEMEL DÜŞÜNME                                    │   │
│  │    Kod okuma, dosya arama, basit sorular                   │   │
│  │    Tetikleyici: Dosya okuma, arama, açıklama isteği        │   │
│  │    Araçlar: Read, Grep, Glob, smart_outline                │   │
│  │    Süre: 0-1 dakika düşünme süresi                         │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.1 Katman Detayları

#### Katman 4: Stratejik Düşünme
- **Kapsam:** Sistem genelinde etkisi olan kararlar
- **Tetikleyiciler:** Yeni servis ekleme, veritabanı şema değişikliği, API sözleşmesi değişikliği, donanım seçimi, güvenlik politikası değişikliği
- **Zorunlu Kontroller:** ADR kontrolü, Layer Violation kontrolü, Guardrail kontrolü, kullanıcı onayı
- **Çıktı:** ADR draft'ı veya plan dokümanı
- **Örnek:** "8.1 Surround desteği ekleyelim" → NevaEngine kanal sayısını artırma, PCM3168A ADC seçimi, Class AB kanal genişletme, test protokolü oluşturma

#### Katman 3: Taktiksel Düşünme
- **Kapsam:** Belirli bir modül veya bileşen üzerinde etkisi olan kararlar
- **Tetikleyiciler:** Performans optimizasyonu, Refactoring, Güvenlik düzeltmesi, Test iyileştirme
- **Zorunlu Kontroller:** Mevcut kod okuma,aternatif karşılaştırma, trade-off analizi
- **Çıktı:** Kod değişikliği planı
- **Örnek:** "NevaEngine gecikmesini azaltalım" → Ring buffer boyutu, thread önceliği, cache stratejisi analizi

#### Katman 2: Operasyonel Düşünme
- **Kapsam:** Tek bir dosya veya fonksiyon üzerinde etkisi olan kararlar
- **Tetikleyiciler:** Bug fix, test yazma, küçük refactor, dokümantasyon
- **Zorunlu Kontroller:** Mevcut kod okuma, test yazma, doğrulama
- **Çıktı:** Kod değişikliği
- **Örnek:** "CSRF token hatası var" → csrf_token key kontrolü, middleware sırası kontrolü

#### Katman 1: Temel Düşünme
- **Kapsam:** Tek bir satır kod veya dosya okuma
- **Tetikleyiciler:** Dosya okuma, arama, açıklama
- **Zorunlu Kontroller:** Doğru dosya okundu mu?
- **Çıktı:** Bilgi yanıtı
- **Örnek:** "brain.md'de ADR-089 ne diyor?" → Dosyayı oku, ilgili bölümü bul

---

## 3. Karar Verme Matrisi

| Öncelik | Kaynak | Açıklama | Çatışma Durumunda |
|---------|--------|----------|-------------------|
| 1 | ADR Decisions | Daha önce alınmış mimari kararlar | EN YÜKSEK — geri alınamaz |
| 2 | CoreMusic Architecture | Projenin temel prensipleri (CLAUDE.md) | ADR ile çelişiyorsa kullanıcıya sor |
| 3 | Security Requirements | Güvenlik zorunlulukları (OWASP, RBAC) | Performans ile çelişiyorsa güvenlik kazanır |
| 4 | Performance Requirements | Hız ve kaynak kullanımı | Güvenlik ile çelişiyorsa güvenlik kazanır |
| 5 | Maintainability | Uzun vadeli bakım | Kısa vadeli kolaylık ile çelişiyorsa bakım kazanır |
| 6 | User Request | Kullanıcı ihtiyacı | Diğer tüm katmanlar ile çelişiyorsa kullanıcıya açıkla |

### 3.1 Çatışma Çözüm Protokolü

**Durum 1: Güvenlik vs Performans**
```
Güvenlik her zaman kazanır.
Örnek: Rate limiting gecikme ekler ama brute-force saldırıları engeller.
Çözüm: Rate limiting korunur, optimizasyon cache stratejisi ile yapılır.
```

**Durum 2: ADR vs Kullanıcı İsteği**
```
ADR önceliklidir. Kullanıcıya ADR'nin neden bu şekilde tasarlandığı açıklanır.
Örnek: Kullanıcı "React kullanalım" der → ADR-001 Vanilla JS yasağı.
Çözüm: Kullanıcıya ADR-001 gösterilir, alternatif Vanilla JS çözümleri sunulur.
```

**Durum 3: Kısa Vadeli Kolaylık vs Uzun Vadeli Bakım**
```
Uzun vadeli bakım her zaman kazanır.
Örnek: "Bu kodu quick-fix yapalım" → Teknik borç yaratır.
Çözüm: Kısa vadeli fix uygulanır ama TODO + ticket açılır.
```

**Durum 4: Mimari Bütünlük vs Hızlı Teslimat**
```
Mimari bütünlük her zaman kazanır.
Örnek: "Bu hafta bitirmemiz lazım, middleware sırasını atlayalım."
Çözüm: Hızlı teslimat için Diğer yollar aranır, Guardrail #7 asla ihlal edilmez.
```

---

## 4. Zorunlu Doğrulama İş Akışı (10 Adım)

Her karar öncesi bu 10 adım **sırayla** uygulanır:

```
┌─────────────────────────────────────────────────────────────────┐
│              10 ADIMLI DOĞRULAMA İŞ AKIŞI                       │
├─────────────────────────────────────────────────────────────────┤
│  ADIM 1:  Vault Okundu mu?                                    │
│           → CLAUDE.md, AGENTS.md, WORKFLOW.md, brain.md        │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve oku                │
│                                                                 │
│  ADIM 2:  ADR Kontrol Edildi mi?                               │
│           → decisions/accepted/ + decisions/draft/              │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve karar ara           │
│                                                                 │
│  ADIM 3:  Güvenlik Etkisi Değerlendirildi mi?                  │
│           → OWASP kontrolü, RBAC, CSRF, CSP                    │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve değerlendir          │
│                                                                 │
│  ADIM 4:  Performans Etkisi Değerlendirildi mi?                │
│           → Gecikme, bellek, CPU, disk kullanımı                │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve ölç                 │
│                                                                 │
│  ADIM 5:  Alternatifler Karşılaştırıldı mi?                    │
│           → En az 2 alternatif, trade-off tablosu               │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve düşün               │
│                                                                 │
│  ADIM 6:  Trade-off Açıklandı mı?                              │
│           → Avantaj/dezavantaj, risk, maliyet                   │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve yaz                 │
│                                                                 │
│  ADIM 7:  Test Planı Hazırlandı mı?                            │
│           → Unit test, integration test, edge cases             │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve planla               │
│                                                                 │
│  ADIM 8:  Rollback Planı Hazırlandı mı?                        │
│           → Geri alma adımları, veri koruma                     │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve planla               │
│                                                                 │
│  ADIM 9:  Kullanıcı Onayı Alındı mı?                           │
│           → Mimari karar, büyük değişiklik                       │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve onay iste            │
│                                                                 │
│  ADIM 10: Vault Güncellendi mi?                                │
│           → ADR, log.md, MEMORY.md güncellendi mi?              │
│           → Geçti: ✅ / Geçmedi: ❌ DUR ve güncelle             │
└─────────────────────────────────────────────────────────────────┘
```

### 4.1 Adım Detayları

| Adım | Geçme Kriteri | Red Sebepleri |
|------|---------------|---------------|
| 1 | Vault'un ilgili bölümleri okundu ve anlaşıldı | Vault okunmadan kod yazma denemesi |
| 2 | İlgili ADR bulundu veya "yeni ADR gerekli" kararı verildi | Mevcut ADR'ye aykırı karar |
| 3 | Güvenlik riski değerlendirildi ve önlem alındı | Güvenlik açığı yaratan değişiklik |
| 4 | Performans etkisi ölçüldü veya tahmin edildi | Kritik performans düşüklüğü |
| 5 | En az 2 alternatif karşılaştırıldı | Tek alternatifli karar |
| 6 | Trade-off tablosu yazıldı | Belirsiz avantaj/dezavantaj |
| 7 | Test senaryoları yazıldı | Testsiz değişiklik |
| 8 | Rollback adımları tanımlandı | Geri alınamaz değişiklik |
| 9 | Kullanıcı onayı alındı (gerekli durumlarda) | Onaysız mimari değişiklik |
| 10 | Vault dosyaları güncellendi | Güncellenmemis dokümantasyon |

---

## 5. Halüsinasyon Kontrol Protokolü (ADR-005)

### 5.1 Halüsinasyon Türleri

| Tür | Tanım | Örnek | Tespit Yöntemi |
|-----|-------|-------|----------------|
| **Bilgi Halüsinasyonu** | Doğrulanamayan gerçek iddialar | "NevaEngine %96 verimle çalışıyor" (ölçülmediyse) | Kaynak kodu okuma, ölçüm |
| **Kaynak Halüsinasyonu** | Olmayan dosya/fonksiyon referansları | "ADR-090 şunu diyor" (ADR-090 yoksa) | Dosya kontrolü |
| **Tutarlılık Halüsinasyonu** | Çelişkili bilgi iddiaları | "Rate limit 60/dk" ve "Rate limit 100/dk" aynı dosyada | Cross-reference kontrolü |
| **Geçmiş Halüsinasyonu** | Eski/yanlış versiyon bilgisi | "Redis IMPLEMENTED" (aslında PLANNED ise) | Kod okuma |
| **Ölçüm Halüsinasyonu** | Gerçek olmayan rakamlar | "SNR 120dB" (aslında 105dB ise) | Teknik specs kontrolü |

### 5.2 Kontrol Seviyeleri

```
SEVİYE 5: KRİTİK — Doğrulanamayan bilgi → DERHAL VERIFICATION REQUIRED yaz
SEVİYE 4: YÜKSEK — Eski/yanlış bilgi → Düzelt veya sil
SEVİYE 3: ORTA — Belirsiz bilgi → Kaynak göster, şüphe belirt
SEVİYE 2: DÜŞÜK — Eksik bilgi → Eksikliği işaretle
SEVİYE 1: BİLGİ — Tamamen doğrulanmış bilgi → Devam et
```

### 5.3 Aksiyon Matrisi

| Durum | Aksiyon | Guardrail |
|-------|---------|-----------|
| Doğrulanamayan bilgi | `VERIFICATION REQUIRED` yaz | #3 Zero Hallucination |
| Eski/yanlış bilgi | Düzelt veya sil | #3 Zero Hallucination |
| Kırık wiki-link | Doğru dosya yolunu bul | #5 SSOT |
| Eksik frontmatter | 7 zorunlu alanı ekle | #16 Template Mandatory |
| Çelişkili bilgi | Vault Steward'a sor, onay bekle | #12 Contradiction Gate |

---

## 6. Güven Skorlama Sistemi

Her karar için 0-100 arası güven skoru hesaplanır:

| Skor Aralığı | Seviye | Aksiyon |
|---------------|--------|---------|
| 90-100 | 🟢 YÜKSEK | Devam et, yüksek güvenle uygula |
| 70-89 | 🟡 ORTA | Devam et, dikkatli ol, ek doğrulama yap |
| 50-69 | 🟠 DÜŞÜK | DUR, ek araştırma yap, alternatifleri değerlendir |
| 0-49 | 🔴 ÇOK DÜŞÜK | DUR, kullanıcıya sor, ADR kontrol et, plan yap |

### 6.1 Güven Skoru Hesaplama Formülü

```
Güven Skoru = Temel (50)
             + ADR Uyumu (+20 veya -30)
             + Vault Doğrulama (+15 veya -20)
             + Test Kapsama (+10 veya -15)
             + Kaynak Güvenilirliği (+5 veya -10)
```

**Örnek Hesaplama:**
- ADR-001 uyumlu: +20
- Vault okundu ve doğrulandı: +15
- Test yazıldı: +10
- Kaynak kodu okundu: +5
- **Toplam:** 50 + 20 + 15 + 10 + 5 = **100** 🟢

---

## 7. Kod Öncesi Kontrol Listesi (15 Madde)

Her kod yazımından **önce** bu 15 madde kontrol edilir:

| # | Kontrol | Geçme Kriteri | İlgili Guardrail |
|---|---------|---------------|-----------------|
| 1 | Vault okundu mu? | CLAUDE.md + AGENTS.md + WORKFLOW.md + brain.md okundu | #2 Vault First, #15 Vault-First Mandatory |
| 2 | Plan yapıldı mı? | Plan dokümanı yazıldı ve onaylandı | #1 Zero Code Before Plan |
| 3 | ADR kontrol edildi mi? | İlgili ADR bulundu veya yeni ADR draft'ı yazıldı | #5 SSOT |
| 4 | Template seçildi mi? | .ai/.templates/index.md'den uygun template seçildi | #16 Template Mandatory |
| 5 | Dosya yolu doğru mu? | Mevcut dosya yapısına uygun, yeni dosya açılmadı | #4 In-Place Refactoring |
| 6 | Frontmatter hazır mı? | 7 zorunlu alan mevcut ve doğru | #16 Template Mandatory |
| 7 | CSRF token doğru mu? | `csrf_token` kullanılıyor (`_csrf_token` değil) | #6 CSRF Token |
| 8 | Middleware sırası korundu mu? | 10 adım sırası değişmedi | #7 Middleware Order |
| 9 | Layer Violation yok mu? | Katman bağımlılık kurallarına uygun | §5 Katman Bağımlılık Matrisi |
| 10 | ORM kullanılmadı mı? | Raw PDO kullanılıyor | #9 No ORM |
| 11 | Framework kullanılmadı mı? | Vanilla JS kullanılıyor | #10 No Frameworks |
| 12 | `SELECT *` kullanılmadı mı? | Explicit columns kullanılıyor | Yasak Örüntüler |
| 13 | `innerHTML` kullanılmadı mı? | DOMParser + TrustedTypes kullanılıyor | Yasak Örüntüler |
| 14 | Hardcoded secret yok mu? | .env veya credential vault kullanılıyor | Yasak Örüntüler |
| 15 | `eval()` / `Function()` kullanılmadı mı? | Safe alternatives kullanılıyor | Yasak Örüntüler |

---

## 8. Domain-Spezifische Düşünme Kuralları

### 8.1 Backend (PHP) İçin

| Kural | Açıklama |
|-------|----------|
| strict_types | Her PHP dosyasının başında `declare(strict_types=1)` |
| Prepared statement | Tüm SQL sorgularında prepared statement zorunlu |
| BCNF uyumu | 18 veritabanında BCNF normalizasyonu zorunlu |
| Error handling | Try-catch ile tüm hatalar yakalanır, loglanır |
| Type hint | Tüm fonksiyonlarda parametre ve dönüş tipi belirtilir |

### 8.2 Frontend (JavaScript) İçin

| Kural | Açıklama |
|-------|----------|
| const/let | `var` kullanımı kesinlikle yasak |
| DOMParser | innerHTML yerine DOMParser + TrustedTypes |
| Event delegation | Tek event listener, bubbling ile yönetimi |
| Async/await | Promise zincirleri yerine async/await |
| ITCSS katmanı | CSS yazılırken önce hangi ITCSS katmanına ait olduğunu belirle |

### 8.3 Hardware (C++) İçin

| Kural | Açıklama |
|-------|----------|
| Zero-allocation | Real-time ses işlemede bellek tahsisi yasak |
| Lock-free | Multithread işlemede kilit kullanma |
| noexcept | Tüm fonksiyonlarda noexcept belirtisi |
| Cache-line alignment | 64-byte hizalama zorunlu |
| RAII | Kaynak yönetimi için RAII pattern |

### 8.4 Security İçin

| Kural | Açıklama |
|-------|----------|
| OWASP Top 10 | Tüm OWASP risklerini değerlendir |
| Input validation | Tüm girdiler validate edilir |
| Output encoding | Tüm çıktılar encode edilir |
| Least privilege | Minimum yetki ilkesi |
| Defense in depth | Çok katmanlı güvenlik |

---

## 9. Hata Türü Sınıflandırması

| Tür | Tanım | Örnek | Önleme |
|-----|-------|-------|--------|
| **Sistemik Hata** | Mimari kökenli, tüm sistemi etkiler | Layer Violation, middleware sırası değişikliği | ADR kontrolü, Guardrail |
| **Rastlantısal Hata** | Tek bir dosya/ fonksiyonda lokal hata | Null pointer, type mismatch | Test, code review |
| **Yapısal Hata** | Kod yapısından kaynaklanan hata | God class, tight coupling | Refactoring, SOLID |
| **Güvenlik Hata**** | Güvenlik açığı yaratan hata | SQL injection, XSS | OWASP, pentest |
| **Performans Hata**** | Performans düşüklüğü yaratan hata | N+1 query, memory leak | Profiling, benchmark |

---

## 10. Meta-Düşünme (Düşünme Hakkında Düşünme)

Her 100 kod satırında bir veya her önemli karar öncesi, aşağıdaki soruları kendine sor:

### 10.1 Self-Monitoring Kontrolleri

| # | Soru | Amaç |
|---|------|------|
| 1 | "Şu an hangi düşünme katmanındayım?" | Katman uygunluğunu doğrula |
| 2 | "Bu kararın uzun vadeli etkisi ne?" | Teknik borç yaratma |
| 3 | "Bu değişiklik başka bileşenleri etkiler mi?" | Yan etki analizi |
| 4 | "Bu kodu 6 ay sonra anlayabilir miyim?" | Sürdürülebilirlik |
| 5 | "Bu değişikliği geri alabilir miyim?" | Rollback planı |
| 6 | "Bu konuda halüsinasyon yapıyor olabilir miyim?" | Zero Hallucination |
| 7 | "Vault'ta bu konuda ne diyor?" | SSOT kontrolü |
| 8 | "Bu değişiklik test edilebilir mi?" | Test coverage |
| 9 | "Bu değişiklik güvenli mi?" | Güvenlik kontrolü |
| 10 | "Bu değişiklik performsız mı?" | Performans kontrolü |

### 10.2 Düşünme Kalitesi Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Vault okuma oranı | %100 | Her karar öncesi vault okundu mu? |
| ADR kontrol oranı | %100 | Her karar öncesi ADR kontrol edildi mi? |
| Alternatif değerlendirme | ≥2 | Her karar için en az 2 alternatif |
| Test yazma oranı | %100 | Her kod değişikliği için test |
| Halüsinasyon tespit oranı | %100 | Doğrulanamayan bilgi işaretlendi mi? |
| Rollback planı | %100 | Her değişiklik için geri alma planı |

---

## 11. Örnek Senaryolar

### Senaryo 1: "NevaEngine'e reverb efekti ekle"
```
KATMAN 4 (Stratejik):
1. Vault oku: brain.md §7 C++ Audio kuralları
2. ADR kontrol: Yeni ADR gerekli mi? → Mevcut ADR-017 (DSP Hardware Mode) yeterli
3. Güvenlik etki: Yok (sadece ses işleme)
4. Performans etki: Real-time.processing'de bellek tahsisi yasak
5. Alternatifler: FFT tabanlı vs convolution reverb
6. Trade-off: FFT daha hızlı, convolution daha gerçekçi
7. Test: Unit test + listening test
8. Rollback: Reverb modülü devre dışı bırakma
9. Kullanıcı onayı: Evet (yeni özellik)
10. Vault güncelle: brain.md §7 güncelle

GÜVEN SKORU: 50 + 20(ADR) + 15(vault) + 10(test) = 95 🟢
```

### Senaryo 2: "CSRF token'ı değiştir"
```
KATMAN 2 (Operasyonel):
1. Vault oku: CLAUDE.md §6 CSRF token = csrf_token
2. ADR kontrol: ADR-010 CSRF Protection Strategy (Frozen)
3. Güvenlik etki: YÜKSEK — CSRF koruması değişiyor
4. Performans etki: Minimal
5. Alternatifler: Yok — ADR-010 bağlayıcı
6. Trade-off: Yok — güvenlik öncelikli
7. Test: Tüm formlarda test
8. Rollback: Eski token formatına dön
9. Kullanıcı onayı: Evet (güvenlik değişikliği)
10. Vault güncelle: log.md'ye CRITICAL ekle

GÜVEN SKORU: 50 + 20(ADR) + 15(vault) = 85 🟡 (güvenlik yüksek risk)
```

### Senaryo 3: "Yeni bir API endpoint ekle"
```
KATMAN 3 (Taktiksel):
1. Vault oku: brain.md §6A API-First mimari
2. ADR kontrol: ADR-084 API-First (Active)
3. Güvenlik etki: Auth + Rate limiting gerekli
4. Performans etki: DB sorgusu optimizasyonu gerekli
5. Alternatifler: REST vs GraphQL
6. Trade-off: REST daha basit, GraphQL daha esnek
7. Test: Unit + integration + API contract test
8. Rollback: Endpoint'i devre dışı bırakma
9. Kullanıcı onayı: Evet (yeni özellik)
10. Vault güncelle: API spec güncelle

GÜVEN SKORU: 50 + 20(ADR) + 15(vault) + 10(test) = 95 🟢
```

---

## 12. AGENTS.md §24 Entegrasyonu

Bu protokol, [[AGENTS.md]] §24 (Ultra Düşünme Protokolü) ile tam uyumludur. Ek farklılıklar:

| Konu | ULTRA-THINKING.md | AGENTS.md §24 |
|------|-------------------|---------------|
| Kapsam | Tüm AI agentları | OpenCode entegrasyonu |
| Katman | 4 katman (stratejik-operasyonel) | 5 adımlı pre-code checklist |
| Güven Skoru | 0-100 arası | Yok |
| Domain Kuralları | Backend/Frontend/Hardware/Security | Genel |
| Halüsinasyon | 5 tür, 5 seviye | 4 satır tablo |

**Uyumluluk:** Her iki protokol de aynı Guardrail'lere dayanır. ULTRA-THINKING.md daha detaylı ve kapsamlıdır; AGENTS.md §24 daha öz ve uygulamaya odaklıdır.

---

## 13. Uygulama Protokolü

### 13.1 Otomatik Tetikleme Durumları

| Durum | Otomatik Uygulanacak Katman |
|-------|----------------------------|
| Yeni dosya oluşturma | Katman 4 (Stratejik) |
| Mevcut dosyada değişiklik | Katman 2 veya 3 (değişikliğin boyutuna göre) |
| Bug düzeltme | Katman 2 (Operasyonel) |
| Yeni ADR taslağı | Katman 4 (Stratejik) |
| Test yazma | Katman 2 (Operasyonel) |
| Güvenlik düzeltmesi | Katman 4 (Stratejik) |
| Donanım değişikliği | Katman 4 (Stratejik) |

### 13.2 Durdurulması Gereken Durumlar

| Durum | Aksiyon |
|-------|---------|
| Vault okunmadı | DUR, vault oku |
| ADR bulunamadı | DUR, yeni ADR taslağı yaz veya Vault Steward'a sor |
| Çelişki tespit edildi | DUR, kullanıcıya sor (#12 Contradiction Gate) |
| Güven skoru <50 | DUR, ek araştırma yap |
| Layer Violation riski | DUR, katman bağımlılık matrisini kontrol et |

---

## 14. Düşünme Journal'ı (Örnek Kayıtlar)

Her önemli karar için kısa bir düşünce kaydı tutulur. Bu kayıtlar gelecekte benzer durumlarda referans olarak kullanılır.

### 14.1 Düşünme Kaydı Formatı

```
TARİH: YYYY-MM-DD HH:MM
KATMAN: [4/3/2/1]
KONU: Kısa açıklama
VAULT OKUMA: Hangi dosyalar okundu?
ADR KONTROL: Hangi ADR incelendi?
GÜVEN SKORU: [0-100]
KARAR: Alınan karar
SONUÇ: Uygulama sonucu
```

### 14.2 Örnek Kayıtlar

**Kayıt #1:**
```
TARİH: 2026-09-19 10:30
KATMAN: 4 (Stratejik)
KONU: Class AB Amplifikatör topoloji seçimi
VAULT OKUMA: brain.md §8, architecture/k16-k20-electronics/amfii/
ADR KONTROL: ADR-089 (Draft)
GÜVEN SKORU: 75 🟡
KARAR: Darlington topolojisi seçildi (MBB150TH-1000 vs CFP vs Diamond)
SONUÇ: ADR-089 draft'ı güncellendi, LTSpice simülasyonu başlatıldı
```

**Kayıt #2:**
```
TARİH: 2026-09-19 11:15
KATMAN: 3 (Taktiksel)
KONU: NevaEngine ring buffer boyutu optimizasyonu
VAULT OKUMA: brain.md §7, glossary.md (zero-allocation tanımı)
ADR KONTROL: ADR-017 (DSP Hardware Mode)
GÜVEN SKORU: 85 🟢
KARAR: Ring buffer boyutu 4096'dan 8192'ye çıkarıldı
SONUÇ: Gecikme 8.2ms'ye düştü (hedef: <10ms) ✅
```

**Kayıt #3:**
```
TARİH: 2026-09-19 14:00
KATMAN: 2 (Operasyonel)
KONU: CSRF token format hatası düzeltmesi
VAULT OKUMA: CLAUDE.md §6, ADR-010
ADR KONTROL: ADR-010 (Frozen — csrf_token)
GÜVEN SKORU: 95 🟢
KARAR: _csrf_token → csrf_token düzeltmesi
SONUÇ: 3 formda düzeltme yapıldı, test geçti ✅
```

---

## 15. Düşünme Kalitesı Sürekli İyileştirme

### 15.1 Geri Bildirim Döngüsü

```
[Düşünme] → [Karar] → [Uygulama] → [Sonuç] → [Değerlendirme] → [İyileştirme]
     ↑                                                              │
     └──────────────────────────────────────────────────────────────┘
```

Her düşünce döngüsünden sonra:
1. Karar doğru muydu? (Evet/Hayır/Nisel)
2. Vault yeterli miydi? (Evet/Hayır — eksik parça var mı?)
3. Güven skoru gerçekçi miydi? (Skor ile sonuç uyumlu mu?)
4. Süre yeterli miydi? (Çok mu uzun/sürekli?)
5. Halüsinasyon oldu mu? (Doğrulanamayan bilgi var mıydı?)

### 15.2 İyileştirme Metrikleri

| Metrik | Hedef | Ölçüm Yöntemi |
|--------|-------|---------------|
| Doğru karar oranı | ≥90% | Karar sonrası değerlendirme |
| Vault okuma oranı | %100 | Her karar öncesi vault |
| ADR uyumu | %100 | ADR çelişkisi yok |
| Halüsinasyon oranı | 0% | Zero Hallucination |
| Düşünme süresi | Uygun | Ne çok kısa ne çok uzun |
| Geri bildirim döngüsü | Her 10 kararda bir | Self-evaluation |

---

## 16. Acil Durum Protokolü

### 16.1 Acil Durum Türleri

| Tür | Tanım | Aksiyon |
|-----|-------|---------|
| **Güvenlik İhlali** | Aktif saldırı veya veri sızıntısı | DUR → İsole et → Logla → Vault Steward'a bildir |
| **Veri Kaybı** | Veritabanı bozulması veya silinme | DUR → Backup'tan geri yükle → Doğrula |
| **Sistem Çökmesi** | Tüm servislerin durması | DUR → Restart → Log analizi → Kök neden |
| **Mimari İhlal** | Layer Violation veya Guardrail ihlali | DUR → Revert → Log CRITICAL → Kullanıcıya bildir |

### 16.2 Acil Durum Adımları

```
1. DUR — Tüm işlemleri durdur
2. GÜVENLİK — Veri kaybını önle
3. LOG — Olayı detaylı logla
4. BİLDİR — Vault Steward'a/ kullanıcıya bildir
5. ÇÖZ — Kök nedeni araştır ve çöz
6. DÖNÜŞ — Normal devama dön
7. ÖĞREN — Lesson learned'ı vault'a ekle
```

---

## 17. İleriye Dönük Geliştirmeler

### 17.1 Planlanan İyileştirmeler

| İyileştirme | Amaç | Öncelik | Tahmini |
|-------------|------|---------|---------|
| Otomatik düşünme journal'ı | Her karar otomatik kaydedilsin | Yüksek | Q4 2026 |
| AI-based güven skoru | Makine öğrenmesi ile skor hesaplama | Orta | Q1 2027 |
| Düşünme kalitesi dashboard'u | Metriklerin görselleştirilmesi | Düşük | Q2 2027 |
| Entegre halüsinasyon tespiti | Gerçek zamanlı halüsinasyon uyarıları | Yüksek | Q4 2026 |
| Düşünme kalıpları kütüphanesi | Sık kullanılan düşünce şablonları | Orta | Q1 2027 |

### 17.2 Uzun Vadeli Vizyon

Ultra Thinking Protocol, zamanla **otomatik bir düşünme asistanına** dönüşmeyi hedefler. Her AI agent'ı bu protokolü uygularken, sistem düşünme kalıplarını öğrenir ve gelecek kararlarda daha hızlı ve doğru öneriler sunar.

```
[GÜNÜMÜZ] Manuel düşünme + kontrol listesi
    ↓
[YAKIN GELECEK] Otomatik journal + metrikler
    ↓
[ORTA VADE] AI-based güven skoru + kalıp tanıma
    ↓
[UZAK GELECEK] Otonom düşünme asistanı + sürekli öğrenme
```

---

## 18. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 18 |
| Total Lines | 500+ |
| Cross References | 6 (Zorunlu Bağlantılar) |
| Thinking Layers | 4 (detaylı) |
| Decision Matrix | 6 öncelik + 4 çatışma senaryosu |
| Verification Steps | 10 (adım adım) |
| Hallucination Types | 5 tür, 5 seviye |
| Confidence Score | 0-100 formülü |
| Pre-Code Checklist | 15 madde |
| Domain Rules | 4 domain (backend/frontend/hardware/security) |
| Error Types | 5 tür |
| Example Scenarios | 3 detaylı senaryo |
| Journal Entries | 3 örnek kayıt |
| Emergency Protocol | 4 acil durum türü |
| Future Roadmap | 5 iyileştirme + 4 aşamalı vizyon |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 2.0.0
**Mode:** Red Team · Human Mode · Truth Mode
