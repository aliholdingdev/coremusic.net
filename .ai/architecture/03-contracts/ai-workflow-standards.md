---
title: "AI Workflow Standards for CoreMusic ELECTRONICS"
type: architecture
category: ai-workflow-standards
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# AI Workflow Standards for CoreMusic ELECTRONICS

**Zorunlu Bağlantılar:** [[ai/index]] · [[ai/ai-engine]] · [[ai/ai-orchestrator]] · [[ai/ai-workflow]] · [[ai/knowledge-base]] · [[ai/memory-system]] · [[ai/prompt-engine]] · [[ai/tool-calling]] · [[ai/mcp-integration]] · [[decisions/accepted/ADR-005-ultrathink-protocol]]

---

## 1. Amaç

CoreMusic ELECTRONICS geliştirme süreçlerinde AI araçlarının kullanımını standartlaştıran protokoldür. Zero Hallucination politikası ve doğrulama zinciri tanımlanmıştır.

---

## 2. Desteklenen AI Platformları

| Platform | Kullanım Alanı | Öncelik |
|----------|---------------|---------|
| Claude | Mimari, kod incelemesi, dokümantasyon | Birincil |
| ChatGPT | Araştırma, beyin fırtınası | İkincil |
| Gemini | Çoklu mod analizi | İkincil |
| Codex | Kod üretimi | Destekleyici |
| Cursor | IDE entegrasyonu | Destekleyici |
| RooCode | Kod asistanı | Destekleyici |

---

## 3. MCP Entegrasyonu

| Özellik | Tanım |
|---------|-------|
| Tool Calling | Dış araç çağrısı |
| Context Injection | Bağlam enjeksiyonu |
| Knowledge Retrieval | Bilgi geri çağırma |

---

## 4. Bilgi Bankası Yapısı

| Kategori | Tanım | Güven Skoru |
|----------|-------|-------------|
| Doğrulanmış | Vault ile doğrulanmış bilgi | %95+ |
| Doğrulanmamış | Henüz doğrulanmamış bilgi | %50-94 |
| Reddedilmiş | Yanlış veya geçersiz bilgi | %0-49 |

---

## 5. Doğrulama Pipeline'ı

```mermaid
graph LR
    A[AI Çıktısı] --> B[Fakt Kontrolü]
    B --> C[ADR Uyumluluğu]
    C --> D[Güvenlik İncelemesi]
    D --> E[İnsan Onayı]
    E ->|Geçti| F[Kabul]
    E ->|Geçmedi| G[Red]

    style A fill:#e1f5fe
    style F fill:#c8e6c9
    style G fill:#ef9a9a
```

### 5.1 Doğrulama Adımları

| Adım | İşlem | Kaynak |
|------|-------|--------|
| 1 | AI çıktısını al | AI platformu |
| 2 | Vault ile fakt kontrolü | [[ai/knowledge-base]] |
| 3 | ADR uyumluluğu kontrolü | [[decisions/accepted/]] |
| 4 | Güvenlik incelemesi | [[architecture/07-security/]] |
| 5 | İnsan onayı | Vault Steward |

---

## 6. AI Kuralları

### 6.1 Zero Hallucination (ADR-005)

| Kural | Değer |
|-------|-------|
| ASLA uydurma | API endpoint, sınıf veya veritabanı tablosu uydurulmaz |
| Doğrulanamayan bilgi | `⚠️ VERIFICATION REQUIRED` olarak işaretlenir |
| Vault doğrulama | Kod yazmadan önce vault dokümanları kontrol edilir |
| Belirsizlik durumu | Kullanıcıya sorulur |
| Web araması | Doğrulama için Yasak |

### 6.2 Pre-Commit Kontrol Listesi

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | ✅ Gerçek kaynak kodu okundu | — |
| 2 | ✅ ADR dokümanlarıyla doğrulandı | — |
| 3 | ✅ `.ai/` vault ile kontrol edildi | — |
| 4 | ✅ Fonksiyon imzaları doğrulandı | — |
| 5 | ✅ Veritabanı şeması `.sql/` dosyalarıyla doğrulandı | — |

### 6.3 Yaygın Hallüsinasyon Örüntüleri

| Örüntü | Önlem |
|--------|-------|
| Uydurulan API endpoint'leri | Route dosyalarını doğrudan kontrol et |
| Yanlış fonksiyon imzaları | Sınıf tanımlarını oku |
| Güncel olmayan örüntüler | Son dokümanları kontrol et |
| Varsayılan davranış | Gerçek uygulamayı oku |
| Uydurulan ADR numaraları | `decisions/accepted/` dizinini kontrol et |

---

## 7. AI Oturum Protokolü

| Adım | İşlem | Süre |
|------|-------|------|
| 1 | Boot dosyalarını oku (10 adım) | Max 25s |
| 2 | Vault bağlamını yükle | Max 10s |
| 3 | Görev bağlamını anla | Değişken |
| 4 | Plana göre kod yaz | Değişken |
| 5 | Doğrulama ile çalıştır | Değişken |
| 6 | Audit trail'a log yaz | Anlık |
| 7 | Gerekirse vault-sync yap | Değişken |

---

## 8. Zorunlu 5 Skills

| # | Skill | Amaç |
|---|-------|------|
| 1 | `/prompt-maker` | Prompt üretim motoru |
| 2 | `/brainstorming` | Fikir üretimi ve keşif |
| 3 | `/vault-sync` | Vault senkronizasyonu |
| 4 | `/hallucination-control` | Halüsinasyon doğrulama |
| 5 | `Red Team · Truth Mode · Human Mode` | Her zaman aktif |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Platformlar | [[ai/ai-engine]] | AI motoru |
| § 4 Bilgi Bankası | [[ai/knowledge-base]] | Bilgi yönetimi |
| § 5 Doğrulama | [[ai/memory-system]] | Bellek sistemi |
| § 6.1 Zero Hallucination | [[ADR-005-ultrathink-protocol]] | Hallüsinasyon politikası |
| § 7 Oturum Protokolü | [[MEMORY.md]] §5 | Boot protokolü |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 10 |
| AI Platforms | 6 |
| Knowledge Categories | 3 |
| Verification Steps | 5 |
| Mandatory Skills | 5 |
| ADR References | 1 |
| Cross References | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
