---
type: guide
category: ai-thinking
title: "CoreMusic — Ultra Thinking Protocol"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Ultra Thinking Protocol

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

Bu dosya, OpenCode AI'ın her kod yazma işleminden önce uyması gereken **ultra düşünme protokolünü** tanımlar. AI'ın hatalı kod yazmasını, .ai referanslarını takip etmemesini ve eksik dosya oluşturmasını önler.

---

## 2. Ultra Düşünme Protokolü (5 Adım)

Her kod yazma işleminden ÖNCE bu 5 adımı uygula:

### Adım 1: Vault Oku (ZORUNLU)
```
 Kod yazmadan önce bu dosyaları OKU:
 → .ai/CLAUDE.md (AI anayasası, guardrails)
 → .ai/AGENTS.md (Agent sınırları, domain boundary)
 → .ai/brain.md (Mimari kararlar)
 → .ai/ROLE.md (Rol tanımı)
 → İlgili ADR'ler (decisions/accepted/)
 → İlgili template'ler (.ai/.templates/)
```

### Adım 2: Bağlamı Anla
```
 Kendine sor:
 → Bu görev hangi domain'de? (Backend, Frontend, Security, DB, HW, FW)
 → Hangi katmanda çalışıyorum? (L0-L3)
 → Hangi dosyaları etkileyeceğim?
 → Mevcut kod yapısını anladım mı?
 → Bağımlılıklar neler?
```

### Adım 3: Hata Kontrolü
```
 Kod yazmadan önce kontrol et:
 → Syntax doğru mu? (brackets, semicolons, types)
 → Import'lar mevcut mu? (doğru paketlerden)
 → TypeScript uyumlu mu? (any kullanımı yasak)
 → Mevcut kod stilini takip ediyor mu?
 → Security riski var mı? (hardcoded secret, injection)
```

### Adım 4: Sonuç Tahmini
```
 Düşün:
 → Bu değişiklik başka dosyaları etkiler mi?
 → Dependency flow'a uygun mu?
 → Edge case'ler var mı?
 → Test yazılabilir mi?
 → Performance etkisi var mı?
```

### Adım 5: Doğrulama
```
 Kodu yazdıktan sonra kontrol et:
 → LSP hataları var mı? (otomatik kontrol)
 → TypeScript compile ediyor mu?
 → Mevcut testleri bozar mı?
 → Template'e uygun mu?
 → Cross-reference'lar geçerli mi?
```

---

## 3. .ai Referans Takibi

### 3.1 Zorunlu Okuma Sırası

Her görev başında bu sırayla oku:

| Sıra | Dosya | Amaç | Timeout |
|------|-------|------|---------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, guardrails | 3s |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing | 3s |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar | 3s |
| 4 | `.ai/brain.md` | Mimari kararlar | 4s |
| 5 | `.ai/ROLE.md` | Rol tanımı | 3s |
| 6 | `.ai/index.md` | Master katalog | 4s |
| 7 | `.ai/keys.md` | Keyword haritası | 3s |
| 8 | `.ai/MEMORY.md` | Session hafızası | 3s |
| 9 | `.ai/log.md` | Audit trail (son 20 satır) | 2s |
| 10 | İlgili ADR'ler | Karar referansları | Değişken |
| 11 | İlgili template'ler | Dosya şablonları | Değişken |

### 3.2 Domain-Based Okuma

Her agent kendi domain'indeki dosyaları okur:

| Agent | Zorunlu Okuma |
|-------|---------------|
| Backend | `architecture/l2-routing/*.md`, `decisions/accepted/ADR-083*.md` |
| Frontend | `ui-design/**/*.md`, `architecture/l3-presentation/*.md` |
| Security | `architecture/l1-security/*.md`, `decisions/accepted/ADR-010*.md` |
| Data | `architecture/l0-infrastructure/*.md`, `.sql/*.sql` |
| Embedded | `projects/NevaEngine/*.md`, `electronic/*.md` |
| QA | `testing/*.md`, `ui-design/screens/**/*.md` |
| DevOps | `architecture/02-deployment/*.md`, `ecosystem/*.md` |

---

## 4. Otomatik Temizlik Protokolü

### 4.1 Hata Tespiti
```
 LSP hata tespit edildiğinde:
 → HATA: "LSP ERRORS DETECTED IN THIS FILE - FIX IMMEDIATELY"
 → Kritik: "CRITICAL: The file you just wrote has errors"
 → Zorunlu: "You MUST fix them before continuing"
 → Yasak: "Do NOT proceed to other tasks until this file has no errors"
```

### 4.2 Otomatik Düzeltme
```
 Hatalı dosya tespit edildiğinde:
 → 1. Hemen düzelt veya sil
 → 2. Gerekirse revert et
 → 3. Kullanıcıya ne olduğunu açıkla
 → 4. Düzeltmeyi doğrula (typecheck, test)
```

### 4.3 Kalite Kontrolü
```
 Her dosya için kontrol et:
 → Syntax doğru mu?
 → Import'lar mevcut mu?
 → Types uyumlu mu?
 → Style tutarlı mı?
 → Security riski yok mu?
 → Template'e uygun mu?
```

---

## 5. Dosya Oluşturma Kuralları

### 5.1 Template Zorunlu (Guardrail #16)
```
 Yeni dosya oluştururken:
 → 1. .ai/.templates/index.md'den uygun template seç
 → 2. Template'i kopyala
 → 3. Değişkenleri doldur ({{VARIABLE}})
 → 4. Gereksiz bölümleri kaldır
 → 5. Frontmatter'i tamamla (7 zorunlu alan)
```

### 5.2 Frontmatter Zorunlu Alanları
```yaml
---
type: guide|system|template|adr
category: kategori-adı
title: "Dosya Başlığı"
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active|draft|archived
version: X.Y.Z
---
```

### 5.3 Wiki-Link Formatı
```
 Doğru: [[dosya/yolu]] veya [[dosya/yolu|gösterim adı]]
 Yanlış: [dosya](dosya/yolu) veya düz metin link
```

---

## 6. Hata Durumları

### 6.1 Hallüsinasyon Kontrolü
```
 Doğrulanamayan bilgi tespit edildiğinde:
 → Etiketle: "VERIFICATION REQUIRED"
 → Kaynak belirt: Hangi dosyadan geldiğini göster
 → Kullanıcıya sor: "Bu bilgiyi doğrulayabilir misin?"
```

### 6.2 Çelişki Durumu
```
 Vault'ta çelişki varsa:
 → 1. DUR
 → 2. Hangi dosyalarda çelişki olduğunu belirle
 → 3. Kullanıcıya sor: "Vault'ta çelişki var, nasıl devam edeyim?"
 → 4. Onay bekle
```

### 6.3 Eksik Bilgi
```
 Eksik bilgi tespit edildiğinde:
 → 1. Hangi bilginin eksik olduğunu belirle
 → 2. Kullanıcıya sor: "Bu bilgi eksik, ne yapayım?"
 → 3. Alternatif sun (eğer mümkünse)
```

---

## 7. Takip Mekanizması

### 7.1 Dosya Takibi
```
 Her görev için:
 → Oluşturulan dosyaları listele
 → Düzeltilen dosyaları listele
 → Silinen dosyaları listele
 → Değişiklik özetini çıkar
```

### 7.2 Todo Takibi
```
 Her adımda:
 → Başlangıç: "Starting task: [görev adı]"
 → İlerleme: "Step X of Y completed"
 → Tamamlanma: "Task completed: [özet]"
 → Hata: "Error at step X: [hata açıklaması]"
```

### 7.3 Audit Trail
```
 Her değişiklik için:
 → Dosya yolu
 → Değişiklik türü (create/edit/delete)
 → Timestamp
 → Sorumlu agent
 → ADR referansı (varsa)
```

---

## 8. Kalite Standartları

### 8.1 Kod Kalitesi
| Standart | Değer |
|----------|-------|
| TypeScript | `any` kullanımı yasak |
| Comments | Sadece gerekli yerlerde |
| Naming | Descriptive, project conventions |
| Security | Hardcoded secret yasak |
| Testing | Her public function test edilmeli |

### 8.2 Dosya Kalitesi
| Standart | Değer |
|----------|-------|
| Frontmatter | 7 zorunlu alan |
| Wiki-link | Geçerli referanslar |
| Cross-reference | Tutarlı linkler |
| Template | Uygun template kullanımı |
| Boyut | Max 1000 satır/dosya |

---

## 9. Uyarılar

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Vault okumadan kod yazma | Kod geçersiz, revert |
| 2 | Template kullanmadan dosya oluşturma | Dosya geçersiz |
| 3 | Hallüsinasyonwithout verification | İçerik silinir |
| 4 | Domain boundary ihlali | Sistem durur |
| 5 | Security riski olan kod | Kod revert edilir |
| 6 | Eksik dosya oluşturma | Görev başarısız |

---

## 10. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | AI anayasası, guardrails |
| [[AGENTS.md]] | Agent sınırları, routing |
| [[WORKFLOW.md]] | Süreçler, fazlar |
| [[brain.md]] | Mimari kararlar |
| [[ROLE.md]] | Rol tanımı |
| [[index.md]] | Master katalog |
| [[keys.md]] | Keyword haritası |
| [[MEMORY.md]] | Session hafızası |
| [[log.md]] | Audit trail |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
