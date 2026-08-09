---
type: adr
category: quality
title: "ADR-005: Ultrathink Protocol — Zero Hallucination"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-005: Ultrathink Protocol — Zero Hallucination

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Quality
**İlgili Agent:** Tüm agent'lar
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki tüm AI ajanlarının ve mühendislerin uyması gereken Zero Hallucination protokolünü tanımlar. Doğrulanamayan bilginin `VERIFICATION REQUIRED` olarak işaretlenmesi, Red Team review ve Truth Mode uygulaması bu kararın temelini oluşturur.

Hallüsinasyon (yanıltıcı bilgi üretme), AI sistemlerinin en kritik sorunlarından biridir. CoreMusic'te bu sorun sistematik olarak ele alınır.

---

## 2. Bağlam

### 2.1 İş Problemi

AI ajanları zaman zaman yanlış veya doğrulanamayan bilgi üretebilir:

| Hallüsinasyon Türü | Örnek | Risk |
|--------------------|-------|------|
| Uydurma API | `GET /api/fake-endpoint` | Kod hatası |
| Yanlış versiyon | `PHP 9.0` (henüz yok) | Teknik borç |
| Olmayan kütüphane | `npm install nonexistent-lib` | Bağımlılık hatası |
| Yanlış port | `port 8080` (aslında 81) | Servis hatası |
| Uydurma ADR | `ADR-099` (henüz yok) | Mimari tutarsızlık |
| Yanlış syntax | `function() {` (PHP'de) | Kod hatası |
| Hayali özellik | `MySQL 10 BCNF` (aslında 9) | Yanlış bilgi |

### 2.2 Neden Önemli?

| Etki | Açıklama |
|------|----------|
| Güven kaybı | Yanlış bilgi, kullanıcının güvenini sarsar |
| Geliştirme gecikmesi | Yanlış kod, zaman kaybına yol açar |
| Mimari bozulma | Uydurma ADR'ler, mimari bütünlüğü bozar |
| Güvenlik açığı | Yanlış güvenlik bilgisi, açıklara yol açar |
| Performans sorunu | Yanlış optimizasyon önerisi, performansı düşürür |

### 2.3 İlişkili Kararlar

| ADR | İlişki |
|-----|--------|
| ADR-007 | Zero Code Before Plan |
| ADR-042 | MSA limit, vault standardı |
| ADR-035 | System prompt engineering |
| ADR-036 | Multi-project prompt maker |

---

## 3. Karar

CoreMusic'te **Zero Hallucination** protokolü uygulanacak. Doğrulanamayan bilgi → `VERIFICATION REQUIRED` etiketi ile işaretlenecek. Tüm çıktılar Red Team review'dan geçecek ve Truth Mode her zaman aktif olacak.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| Protokol adı | Zero Hallucination |
| Etiket | `VERIFICATION REQUIRED` |
| Review | Red Team adversarial |
| Mode | Truth Mode (her zaman aktif) |
| Kapsam | Tüm agent'lar, tüm çıktılar |
| İhlal sonucu | İçerik silinir + log CRITICAL |

### 3.2 Temel Prensipler

| Prensip | Açıklama |
|---------|----------|
| **Zero Hallucination** | Uydurma bilgi üretme yasağı |
| **Web Doğrulama** | API/teknoloji web'de doğrulanmalı |
| **VERIFICATION REQUIRED** | Doğrulanamayan bilgi için etiket |
| **Red Team Review** | Her çıktı adversarial review'dan geçmeli |
| **Truth Mode** | Her zaman gerçek mod |
| **Human Mode** | İnsan onayı olmadan üretim yapılmaz |
| **Source Citation** | Her bilgi kaynağı belirtilmeli |

---

## 4. Teknik Detaylar

### 4.1 VERIFICATION REQUIRED Etiketi

Doğrulanamayan her bilgi şu formatta işaretlenir:

```markdown
// VERIFICATION REQUIRED
// Bu bilgi henüz doğrulanmamıştır.
// Kaynak: [belirtilmeli]
// Doğrulama tarihi: [belirtilmeli]
```

### 4.2 Doğrulama Akışı

```
Bilgi ihtiyacı tespit edildi
  → [1. Kaynak Kontrolü] — Vault'ta var mı?
    → [2. Web Doğrulama] — Web'de doğrula
      → [3. Cross-Reference] — Çapraz referans
        → [4. Doğrulama] — Doğrulandı mı?
          → EVET → Bilgiyi kullan
          → HAYIR → VERIFICATION REQUIRED etiketi ekle
```

### 4.3 Doğrulama Seviyeleri

| Seviye | Yöntem | Güvenilirlik |
|--------|--------|-------------|
| L1 | Vault'ta mevcut | Yüksek |
| L2 | Web'de doğrulama (resmi kaynak) | Yüksek |
| L3 | Cross-reference (diğer ADR'ler) | Orta |
| L4 | Topluluk bilgisi (Stack Overflow) | Orta |
| L5 | Tahmin / spekülasyon | Düşük → REDDEDİLİR |

### 4.4 Red Team Review Akışı

```
Çıktı üretildi
  → [1. Adversarial Review] — Yanlış bilgi var mı?
    → [2. Fact Check] — Her bilgi doğrulandı mı?
      → [3. Security Check] — Güvenlik açığı var mı?
        → [4. Performance Check] — Performans etkisi nedir?
          → [5. Final Review] — İnsansız onay
            → Geçti → Çıktı kabul
            → Başarısız → Düzelt → Tekrar review
```

### 4.5 Truth Mode Kuralları

| Kural | Açıklama |
|-------|----------|
| Her zaman aktif | Truth Mode asla devre dışı bırakılamaz |
| Gerçeği yansıtsın | Yanlış bilgi üretme |
| Kaynak belirt | Her bilginin kaynağını göster |
| Eski bilgiyi güncelle | Eski bilgiyi güncelleme |
| Bilinmeyeni bil | Bilmediğini kabul et |

### 4.6 Human Mode Kuralları

| Kural | Açıklama |
|-------|----------|
| İnsan onayı | Kritik kararlar için insan onayı zorunlu |
| Oversight | İnsan oversight'ı her zaman mevcut |
| Escalation | Sorun çözülemezse insan müdahalesi |
| Final decision | Son karar her zaman insana ait |

### 4.7 Hallüsinasyon Tespit Kalıpları

| Kalıp | Tespit Yöntemi | Aksiyon |
|-------|---------------|---------|
| Uydurma API endpoint | API dokümanı kontrolü | `VERIFICATION REQUIRED` |
| Yanlış versiyon numarası | Resmi kaynak kontrolü | Versiyonu doğrula |
| Olmayan kütüphane | npm/packagist kontrolü | Kütüphaneyi ara |
| Yanlış port numarası | Port haritası kontrolü | ADR-042'ye bak |
| Uydurma ADR | decisions/ dizini kontrolü | ADR'yi ara |
| Hayali özellik | Teknik doküman kontrolü | Özelliği doğrula |
| Yanlış syntax | Dil dokümanı kontrolü | Syntax'ı doğrula |

### 4.8 Doğrulama Workflow'u

```
[1. Bilgi İhtiyacı] → [2. Kaynak Ara] → [3. Doğrula] → [4. Etiketle] → [5. Kullan]
```

Her adım için kontrol noktaları:

| Adım | Kontrol | Çıktı |
|------|---------|-------|
| 1. Bilgi İhtiyacı | Ne bilmeliyim? | Soru tanımı |
| 2. Kaynak Ara | Vault'ta var mı? Web'de var mı? | Kaynak listesi |
| 3. Doğrula | Bilgi doğru mu? Güncel mi? | Doğrulama sonucu |
| 4. Etiketle | Doğrulandı mı? | Etiket (doğrulanmış/VERIFICATION REQUIRED) |
| 5. Kullan | Bilgiyi kullan | Çıktı |

### 4.9 Doğrulama Matrisi Detayı

| Bilgi Türü | Doğrulama Yöntemi | Kaynak | Güvenilirlik |
|------------|-------------------|--------|-------------|
| API endpoint | Resmi doküman | API docs | Yüksek |
| Versiyon numarası | Resmi kaynak | Release notes | Yüksek |
| Kütüphane | npm/packagist | Package registry | Yüksek |
| Port numarası | ADR-042 | Vault | Yüksek |
| ADR varlığı | decisions/ dizini | Vault | Yüksek |
| Teknik özellik | Teknik doküman | Resmi kaynak | Yüksek |
| Syntax | Dil dokümanı | Resmi kaynak | Yüksek |
| Güvenlik | OWASP | OWASP Top 10 | Yüksek |
| Topluluk bilgisi | Forum, blog | Stack Overflow | Orta |
| Kişisel deneyim | Deneyim | — | Düşük |
| Spekülasyon | Tahmin | — | Çok düşük |
| Uydurma bilgi | Yok | — | SIFIR |

### 4.10 Red Team Review Prosedürü

| Adım | Aksiyon | Sorumlu | Süre |
|------|---------|---------|------|
| 1 | Çıktıyı adversarial review'a gönder | Üreten ajan | Anlık |
| 2 | Yanlış bilgi ara | Review ajanı | 5dk |
| 3 | Fact check uygula | Review ajanı | 10dk |
| 4 | Güvenlik kontrolü yap | Security ajanı | 5dk |
| 5 | Final onay ver | Vault Steward | 5dk |

### 4.11 Doğrulama Kaynakları

| Kaynak | Türü | Güvenilirlik | Kullanım |
|--------|------|-------------|----------|
| ADR dosyaları | Vault | Yüksek | Mimari kararlar |
| Resmi doküman | Web | Yüksek | Teknik bilgi |
| Package registry | npm/packagist | Yüksek | Kütüphane bilgisi |
| GitHub repos | Web | Orta | Kod örneği |
| Stack Overflow | Topluluk | Orta | Sorun çözümü |
| Blog yazıları | Web | Düşük | Genel bilgi |
| Sosyal medya | Web | Çok düşük | Spekülasyon |

### 4.12 Doğrulama Raporu Formatı

```markdown
# Doğrulama Raporu

## Bilgi: [Bilgi açıklaması]
- Kaynak: [Kaynak adı]
- Doğrulama tarihi: [YYYY-MM-DD]
- Doğrulama yöntemi: [Yöntem]
- Güvenilirlik: [Yüksek/Orta/Düşük]
- Durum: [Doğrulanmış / VERIFICATION REQUIRED]
- Notlar: [Ek açıklamalar]
```

### 4.13 Hallüsinasyon Önleme Stratejileri

| Strateji | Açıklama | Etkinlik |
|----------|----------|---------|
| Web doğrulama | API/teknoloji web'de doğrulanmalı | Yüksek |
| Cross-reference | Diğer ADR'ler ile çapraz referans | Yüksek |
| Source citation | Her bilgi kaynağı belirtilmeli | Yüksek |
| Red Team review | Adversarial review zorunlu | Yüksek |
| Confidence score | Her bilgiye güvenilirlik puanı | Orta |
| Fact check | Bilgi doğrulama | Yüksek |
| Peer review | Başka agent review | Orta |
| Version control | Eski bilgi versiyonlama | Orta |

### 4.14 Doğrulama Pipeline'u

```
[1. Bilgi İhtiyacı] → [2. Kaynak Ara] → [3. Doğrula] → [4. Confidence] → [5. Etiketle] → [6. Kullan]
```

Her aşama için detay:

| Aşama | Aksiyon | Araç | Çıktı |
|-------|---------|------|-------|
| 1 | Bilgi ihtiyacını tanımla | Manuel | Soru tanımı |
| 2 | Kaynakları ara | Web search, vault | Kaynak listesi |
| 3 | Bilgiyi doğrula | Resmi kaynak | Doğrulama sonucu |
| 4 | Güvenilirlik puanı ver | Confidence scoring | Puan (0-100) |
| 5 | Etiketle | Manuel | Etiket |
| 6 | Kullan veya reddet | Karar | Aksiyon |

### 4.15 Confidence Score Matrisi

| Puan Aralığı | Seviye | Kullanım |
|--------------|--------|----------|
| 90-100 | Yüksek | Doğrudan kullan |
| 70-89 | Orta | Kaynak belirterek kullan |
| 50-69 | Düşük | `VERIFICATION REQUIRED` |
| 0-49 | Çok düşük | Kullanma, reddet |

### 4.16 Red Team Review Prosedürü Detayı

| Adım | Aksiyon | Araç | Süre |
|------|---------|------|------|
| 1 | Çıktıyı adversarial review'a gönder | Manuel | Anlık |
| 2 | Yanlış bilgi ara | Grep, search | 5dk |
| 3 | Fact check uygula | Web search | 10dk |
| 4 | Güvenlik kontrolü yap | OWASP checklist | 5dk |
| 5 | Performance check | Benchmark | 5dk |
| 6 | Final onay ver | Vault Steward | 5dk |

### 4.17 Hallüsinasyon Türleri Detayı

| Tür | Örnek | Tespit | Önleme |
|-----|-------|--------|--------|
| Uydurma API | `GET /api/fake` | API doküman | Web doğrulama |
| Yanlış versiyon | `PHP 9.0` | Release notes | Kaynak kontrolü |
| Olmayan kütüphane | `npm install fake` | npm registry | Package kontrol |
| Yanlış port | `port 8080` | Port haritası | ADR kontrolü |
| Uydurma ADR | `ADR-099` | decisions/ dizini | Vault kontrolü |
| Hayali özellik | `MySQL 10 BCNF` | Teknik doküman | Doğrulama |
| Yanlış syntax | `function() {` | Dil dokümanı | Syntax kontrolü |
| Eski bilgi | Güncellenmemiş | Versiyon kontrolü | Periyodik güncelleme |

### 4.18 Doğrulama Kaynakları Hiyerarşisi

```
En yüksek güvenilirlik
  → Resmi doküman (API docs, release notes)
    → ADR dosyaları (vault)
      → Package registry (npm, packagist)
        → GitHub repos (kod örnekleri)
          → Topluluk (Stack Overflow)
            → Blog yazıları
              → Sosyal medya
                → Spekülasyon (REDDEDİLİR)
En düşük güvenilirlik
```

### 4.19 Truth Mode Workflow

```
[1. Bilgi Girişi] → [2. Doğrulama] → [3. Confidence] → [4. Etiketleme] → [5. Çıktı]
```

Her adım için kontrol:

| Adım | Kontrol | Başarısızlık |
|------|---------|-------------|
| 1 | Bilgi doğru mu? | Reddet |
| 2 | Kaynak güvenilir mi? | `VERIFICATION REQUIRED` |
| 3 | Confidence yeterli mi? | Düşükse reddet |
| 4 | Etiket doğru mu? | Düzelt |
| 5 | Çıktı safe mi? | Reddet |

### 4.20 Human Mode Protokolü

| Durum | Aksiyon | Sorumlu |
|-------|---------|---------|
| Kritik karar | İnsan onayı | Vault Steward |
| Acil durum | İnsan müdahalesi | En yakın yetkili |
| Çelişki | İnsan çözümü | Tech Lead |
| Belirsizlik | İnsan kararı | Vault Steward |
| Escalation | İnsan müdahalesi | Arch Lead |

### 4.21 Doğrulama Raporu Şablonu

```markdown
# Doğrulama Raporu

## Genel Bilgi
- Rapor tarihi: [YYYY-MM-DD]
- Doğrulayan: [Agent adı]
- Konu: [Bilgi açıklaması]

## Doğrulama Detayları
- Kaynak: [Kaynak adı ve URL'si]
- Doğrulama yöntemi: [Yöntem açıklaması]
- Güvenilirlik puanı: [0-100]
- Durum: [Doğrulanmış / VERIFICATION REQUIRED]

## Çapraz Referans
- İlgili ADR'ler: [ADR-NNN]
- İlgili dosyalar: [dosya yolu]

## Sonuç
- Kullanılabilirlik: [Evet/Hayır]
- Koşullar: [Varsa koşullar]
- Notlar: [Ek açıklamalar]
```

### 4.22 Hallüsinasyon Tespit Kalıpları

| Kalıp | Tespit Yöntemi | Otomatik mi? | Aksiyon |
|-------|---------------|-------------|---------|
| Uydurma API | API doküman tarama | Kısmen | Manuel |
| Yanlış versiyon | Versiyon kontrolü | Evet | Otomatik |
| Olmayan kütüphane | Registry kontrolü | Evet | Otomatik |
| Yanlış port | Port haritası | Evet | Otomatik |
| Uydurma ADR | Vault tarama | Evet | Otomatik |
| Hayali özellik | Teknik doküman | Kısmen | Manuel |
| Yanlış syntax | Lint/compile | Evet | Otomatik |
| Eski bilgi | Versiyon kontrolü | Kısmen | Manuel |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | İhlal Sonucu |
|----------|----------|-------------|
| Uydurma bilgi | Doğrulanmış bilgi | İçerik silinir |
| Kaynaksız bilgi | Kaynak belirtilmiş bilgi | `VERIFICATION REQUIRED` |
| Spekülasyon | Doğrulama | İçerik silinir |
| Eski bilgi kullanımı | Güncellenmiş bilgi | Uyarı + güncelleme |
| Tahmin | Kanıt | İçerik silinir |
| Kaynak belirtmeden alıntı | Kaynak belirtilmiş alıntı | İzlenebilirlik düşer |
| Red Team atlanması | Zorunlu review | Kalite düşüşü |
| Truth Mode devre dışı | Her zaman aktif | Güvenilirlik düşer |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Eski API versiyonu | API güncellemesi | Versiyon kontrolü |
| Çelişkili kaynaklar | Farklı kaynaklar farklı bilgi | En güvenilir kaynak |
| Yeni teknoloji | Henüz doğrulanmamış | `VERIFICATION REQUIRED` |
| Hızlı değişen bilgi | Teknoloji güncellemeleri | Periyodik doğrulama |
| Kısmi bilgi | Eksik dokümantasyon | `VERIFICATION REQUIRED` |
| Deney tabanlı bilgi | Kişisel deneyim | `VERIFICATION REQUIRED` |
| Topluluk bilgisi | Forum, blog | Doğrulama gerekli |
| Resmi olmayan kaynak | Blog, sosyal medya | Düşük güvenilirlik |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero Hallucination — uydurma bilgi yasak | İçerik silinir |
| 2 | VERIFICATION REQUIRED etiketi zorunlu | Güvenilirlik düşer |
| 3 | Red Team review zorunlu | Kalite düşüşü |
| 4 | Truth Mode her zaman aktif | Güvenilirlik düşer |
| 5 | Human Mode — insan onayı zorunlu | Yanlış karar |
| 6 | Kaynak belirtme zorunlu | İzlenebilirlik düşer |
| 7 | Eski bilgi güncellenmeli | Yanlış bilgi yayılır |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-007-cache-namespace]] | Zero Code Before Plan | Planlama zorunluluğu |
| [[ADR-035-system-prompt-engineering]] | System prompt engineering | Prompt standartları |
| [[ADR-036-multi-project-prompt-maker]] | Multi-project prompt | Prompt üretimi |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | Bilgi kaynağı |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS | Teknoloji doğrulama |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO | Teknoloji doğrulama |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[CLAUDE.md]] §3 | Terminoloji |
| § 4.1 Etiket | [[CLAUDE.md]] §7 | Hard guardrails |
| § 4.5 Truth Mode | [[ADR-005-ultrathink-protocol]] | Bu dosya |
| § 5 Yasaklar | [[CLAUDE.md]] §21 | Yasak örüntüleri |
| § 6 Edge | [[ADR-042-vault-restructuring-2026-08-03]] | Edge cases |
| § 7 Guardrails | [[AGENTS.md]] §13 | MSA limit |
| § 8 Matris | [[brain.md]] §7 | C++ audio rules |
| § 9 ADR | [[ADR-035-system-prompt-engineering]] | Prompt |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Hallüsinasyon** | AI'ın yanlış veya uydurma bilgi üretmesi |
| **Zero Hallucination** | Uydurma bilgi üretme yasağı |
| **VERIFICATION REQUIRED** | Doğrulanamayan bilgi etiketi |
| **Red Team** | Adversarial review süreci |
| **Truth Mode** | Gerçek bilgi üretme modu |
| **Human Mode** | İnsan onayı gerektiren mod |
| **Fact Check** | Bilgi doğrulama |
| **Cross-Reference** | Çapraz referans |
| **Source Citation** | Kaynak belirtme |
| **Adversarial Review** | Düşmanca inceleme |
| **Overspeculation** | Fazla spekülasyon |
| **Confidence Score** | Güvenilirlik puanı |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| Frozen | 2026-05-15 |
| Hallüsinasyon Türü | 7 |
| Doğrulama Seviyesi | 5 |
| Yasak Örüntüleri | 8 |
| Edge Cases | 8 |
| Hard Guardrails | 7 |
| ADR References | 6 |
| Cross References | 8 |
| Glossary Terms | 12 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
