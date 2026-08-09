---
type: guide
category: workflow
title: "CoreMusic — Vault Workflows & Engineering Processes"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Vault Workflows & Engineering Processes

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic ekosistemindeki tüm süreçlerin standartlaştırıldığı **Tek Doğruluk Kaynağıdır (SSOT)**. Zero Code Before Plan ve Hard Gate prensipleri uygulanır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 12-Fazlı Vault Refactoring | Teknik uygulama detayları |
| 20-Fazlı Ürün Yaşam Döngüsü | İş mantığı |
| ADR Yaşam Döngüsü | Veritabanı işlemleri |
| 7 Workflow (Code Review, Bug Fix, Feature, Security, Deploy, Session, Vault Sync) | Güvenlik politikası |
| Session Init & Vault Sync Protokolü | — |
| Hard Gates & Kurallar | — |

---

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **Workflow** | Belirli bir amaca yönelik adımlar dizisi |
| **Hard Gate** | Kullanıcı onayı olmadan geçilemeyen kritik faz geçiş noktası |
| **Zero Code Before Plan** | Plan onayı olmadan kod yazma yasağı |
| **In-Place Modification** | Dosya adı/yolu değişmeden güncelleme |
| **Append-Only** | Geçmiş satırların silinmediği/eğitilmediği mod |
| **MSA Limit** | Görev başına max 15 dosya okuma kısıtı (ADR-042) |
| **Vault Sync** | Vault ile kod arasındaki tutarlılığı sağlama |
| **ADR Lifecycle** | Draft → Review → Active → Frozen yaşam döngüsü |
| **Pre-flight Check** | Görev başlamadan önce yapılan kontroller |
| **Context Lock** | Eşzamanlı dosya erişimini önlemek için kilitleme |

---

## 4. Core Principles

| İlke | Açıklama | ADR |
|------|----------|-----|
| Zero Code Before Plan | Kod yazmadan önce tam planlama zorunlu | [[ADR-007-cache-namespace]] |
| In-Place Modification | Dosya adı/konumu onay olmadan değişmez | Hard Rule #2 |
| No Hallucination | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` | [[ADR-005-ultrathink-protocol]] |
| Append-Only Log | Geçmiş kayıtlar silinemez | [[ADR-004-multi-domain-spa]] |
| MSA Limit | Görev başına max 15 dosya | [[ADR-042-vault-restructuring-2026-08-03]] |
| Hard Gate | Kullanıcı onayı olmadan sonraki faza geçilmez | [[ADR-007-cache-namespace]] |
| Domain Boundary | Her ajan kendi alanında kalır | [[ADR-008-bypass-auth-middleware]] |
| Single Source of Truth | Bilgi sadece `.ai/` vault'tan okunur | [[ADR-042-vault-restructuring-2026-08-03]] |

---

## 5. 12-Phase Vault Refactoring

| Faz | Amaç | Çıktı | Hard Gate |
|-----|------|-------|-----------|
| 1 | Repository Discovery | `repository-inventory.md` | — |
| 2 | AI Knowledge Discovery | `knowledge-gap-report.md` | — |
| 3 | Existing Markdown Analysis | `markdown-analysis.md` | — |
| 4 | Conflict Detection | `conflict-report.md` | — |
| 5 | Duplicate Detection | `duplicate-report.md` | — |
| 6 | Gap Detection | `gap-report.md` | — |
| 7 | **Improvement Proposal** | `improvement-proposal.md` | ✅ HARD GATE |
| 8 | Document Refactoring (In-Place) | Güncellenmiş vault | — |
| 9 | Cross Reference Update | Güncellenmiş referanslar | — |
| 10 | Index Update | Güncellenmiş `index.md` | — |
| 11 | Validation | `validation-report.md` | — |
| 12 | Quality Report & Vault Sync | `quality-report.md` + log | — |

### 5.1 Faz Detayları

#### Faz 1: Repository Discovery
- Tüm vault dosyaları taranır
- Dosya boyutları, satır sayıları, format analiz edilir
- Eksik dosyalar tespit edilir
- Çıktı: `repository-inventory.md` (dosya listesi + metrikler)

#### Faz 2: AI Knowledge Discovery
- AI ajanlarının bilgi ihtiyaçları analiz edilir
- Mevcut bilgi gaps tespit edilir
- Öneri listesi oluşturulur
- Çıktı: `knowledge-gap-report.md`

#### Faz 3: Existing Markdown Analysis
- Tüm markdown dosyaları format olarak analiz edilir
- Wiki-link'ler, frontmatter, yapı kontrol edilir
- Standart dışı dosyalar tespit edilir
- Çıktı: `markdown-analysis.md`

#### Faz 4: Conflict Detection
- Dosyalar arası çelişkiler tespit edilir
- Eski/yanlış bilgiler bulunur
- ADR çelişkileri kontrol edilir
- Çıktı: `conflict-report.md`

#### Faz 5: Duplicate Detection
- Yinelenen dosyalar tespit edilir
- Benzer içerikli dosyalar bulunur
- Birleştirme önerileri oluşturulur
- Çıktı: `duplicate-report.md`

#### Faz 6: Gap Detection
- Eksik dokümantasyon tespit edilir
- ADR kapsamadığı alanlar bulunur
- Eksik referanslar listelenir
- Çıktı: `gap-report.md`

#### Faz 7: Improvement Proposal → HARD GATE
- Tüm bulgular bir araya getirilir
- İyileştirme teklifi hazırlanır
- **Kullanıcı onayı zorunlu** (Hard Gate)
- Onay olmadan Faz 8'e geçilemez
- Çıktı: `improvement-proposal.md`

#### Faz 8: Document Refactoring (In-Place)
- Dosyalar yerinde güncellenir
- Dosya adı/konumu değişmez
- Format standartları uygulanır
- Wiki-link'ler korunur

#### Faz 9: Cross Reference Update
- Tüm çapraz referanslar güncellenir
- Kırık wiki-link'ler düzeltilir
- Yeni eklenen dosyalar referanslanır

#### Faz 10: Index Update
- `index.md` güncellenir
- Yeni dosyalar eklenir
- Silinen dosyalar kaldırılır
- Kategoriler güncellenir

#### Faz 11: Validation
- Tüm değişiklikler doğrulanır
- Wiki-link'ler kontrol edilir
- Format uyumluluğu test edilir
- Çıktı: `validation-report.md`

#### Faz 12: Quality Report & Vault Sync
- Kalite raporu oluşturulur
- `log.md`'ye giriş eklenir
- MEMORY.md session state güncellenir
- Çıktı: `quality-report.md`

---

## 6. 20-Phase Product Lifecycle

| Faz | Grup | Amaç | Hard Gate |
|-----|------|------|-----------|
| 1 | Vizyon & Analiz | Vizyon tanımı | — |
| 2 | Vizyon & Analiz | Domain-Driven Design | — |
| 3 | Vizyon & Analiz | Use case'ler | — |
| 4 | Vizyon & Analiz | Akış diyagramları | — |
| 5 | Vizyon & Analiz | Bilgi mimarisi | — |
| 6 | Vizyon & Analiz | Rekabet analizi | — |
| 7 | Teknik Mimari | **Mimari tasarım** | ✅ HARD GATE |
| 8 | Teknik Mimari | Diyagramlar | — |
| 9 | Teknik Mimari | Klasör yapısı | — |
| 10 | Kod & Tasarım | Kod standartları | — |
| 11 | Kod & Tasarım | UI/UX tasarımı | — |
| 12 | Kod & Tasarım | API tasarımı | — |
| 13 | Kod & Tasarım | BCNF DB tasarımı | — |
| 14 | Kod & Tasarım | OWASP güvenlik | — |
| 15 | Test & DevOps | Test stratejisi | — |
| 16 | Test & DevOps | CI/CD | — |
| 17 | Test & DevOps | Dokümantasyon | — |
| 18 | Test & DevOps | Monitoring | — |
| 19 | MVP & Yol Haritası | MVP sürümü | — |
| 20 | MVP & Yol Haritası | 5 yıllık yol haritası | — |

### 6.1 Faz Grupları Detayı

#### Vizyon & Analiz (Faz 1-6)
- Ürün vizyonu tanımlanır
- Domain-Driven Design ile sınırlandırılmış bağlam belirlenir
- Kullanıcı senaryoları (use case) yazılır
- Akış diyagramları çizilir
- Bilgi mimarisi oluşturulur
- Rekabet analizi yapılır

#### Teknik Mimari (Faz 7-9)
- **Faz 7: Hard Gate** — Mimari tasarım onayı
- Sistem diyagramları çizilir
- Klasör yapısı planlanır
- Teknoloji yığını belirlenir

#### Kod & Tasarım (Faz 10-14)
- Kod standartları yazılır (PSR-12, BEM, ITCSS)
- UI/UX tasarımı yapılır
- API tasarımı oluşturulur
- BCNF veritabanı tasarımı yapılır
- OWASP güvenlik kontrolleri uygulanır

#### Test & DevOps (Faz 15-18)
- Test stratejisi belirlenir
- CI/CD pipeline kurulur
- Dokümantasyon yazılır
- Monitoring sistemi kurulur

#### MVP & Yol Haritası (Faz 19-20)
- MVP sürümü yayınlanır
- 5 yıllık yol haritası oluşturulur

---

## 7. ADR Lifecycle

```
Draft → Review → Active → Frozen
```

| Aşama | Değişiklik | Süre | Onay |
|-------|------------|------|------|
| Draft | Tamamen düzenlenebilir | Süresiz | Gerekmez |
| Review | Kısıtlı değişiklik | 7 gün | Tech Lead |
| Active | Sadece minor güncelleme | Kalıcı | Arch Lead |
| Frozen | Hiçbir değişiklik | Sonsuz | — |

### 7.1 ADR Oluşturma Adımları

| Adım | Aksiyon | Sorumlu |
|------|---------|---------|
| 1 | `decisions/draft/ADR-NNN-*.md` oluştur | İsteyen ajan |
| 2 | Gerekçeyi ve alternatifleri yaz | İsteyen ajan |
| 3 | Tech Lead onayı iste | İsteyen ajan |
| 4 | `decisions/accepted/`'a taşı | Tech Lead |
| 5 | Cross-reference'ları güncelle | MO |
| 6 | `log.md`'ye kaydet | MO |

### 7.2 ADR Güncelleme (Active)

| Adım | Aksiyon | Sorumlu |
|------|---------|---------|
| 1 | Mevcut ADR'yi oku | Güncelleyen ajan |
| 2 | Değişiklik gerekçesini yaz | Güncelleyen ajan |
| 3 | Arch Lead onayı iste | Güncelleyen ajan |
| 4 | In-place güncelle | Arch Lead |
| 5 | Cross-reference'ları güncelle | MO |
| 6 | `log.md`'ye kaydet | MO |

### 7.3 Frozen ADR Kuralları

| Kural | Değer |
|-------|-------|
| Değişiklik yasağı | ADR 001-037 değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |
| İstisna onayı | Vault Steward + İnsan |
| Yeni karar | Yeni ADR oluşturulur (ADR-038+) |

---

## 8. Workflows

### 8.1 Code Review

| Adım | Aksiyon | Kontrol |
|------|---------|---------|
| 1 | `git diff` ile değişiklik listesini al | — |
| 2 | Etkilenen dosyaları belirle (max 15 — MSA) | MSA limit |
| 3 | İlgili ADR'leri kontrol et | ADR uyumluluğu |
| 4 | Kod standartlarını doğrula (PSR-12, BEM, ITCSS) | Format |
| 5 | Güvenlik kontrollerini yap (OWASP, CSRF, CSP) | Security |
| 6 | Test coverage'ı kontrol et (min %80) | Coverage |
| 7 | Çapraz referansları doğrula | Cross-ref |
| 8 | İnceleme raporu oluştur | — |

**Çıktı:** Code Review Raporu (geçti/başarısız + notlar)

### 8.2 Bug Fix

| Adım | Aksiyon | Öncelik |
|------|---------|---------|
| 1 | Hata tanımını analiz et (root cause) | — |
| 2 | Etkilenen dosyaları tespit et | MSA |
| 3 | İlgili ADR'leri kontrol et | ADR |
| 4 | Düzeltmeyi uygula | — |
| 5 | Testleri çalıştır | — |
| 6 | Regression testi yap | — |
| 7 | Dokümantasyonu güncelle | — |
| 8 | `log.md`'ye kaydet | — |

**Öncelik & Süre:**

| Öncelik | Süre | Örnek |
|---------|------|-------|
| CRITICAL | 1 saat | Auth bypass, veri sızıntısı |
| HIGH | 4 saat | Kritik işlev kaybı |
| MEDIUM | 1 gün | Normal hata |
| LOW | 1 hafta | Kozmetik hata |

### 8.3 New Feature

| Adım | Aksiyon | Hard Gate |
|------|---------|-----------|
| 1 | Gereksinimleri tanımla | — |
| 2 | İlgili ADR'leri kontrol et | — |
| 3 | 20-Fazlı yaşam döngüsünün ilgili fazlarını uygula | — |
| 4 | Mimari planı hazırla (Phase 7) | ✅ HARD GATE |
| 5 | Kullanıcı onayını al | — |
| 6 | Kodlamaya başla (Zero Code Before Plan) | — |
| 7 | Testleri yaz ve çalıştır | — |
| 8 | Vault-sync yap | — |

### 8.4 Security Audit

| Adım | Aksiyon | Kaynak |
|------|---------|--------|
| 1 | OWASP Top 10:2025 kontrol listesini hazırla | OWASP |
| 2 | Middleware pipeline sırasını doğrula | ADR-010/011/012/013/022 |
| 3 | Şifreleme standartlarını kontrol et | ADR-022 |
| 4 | CSRF/CSP/rate limiting'i test et | ADR-010/012/013 |
| 5 | Session yönetimini doğrula | ADR-011 |
| 6 | Credential vault'u kontrol et | ADR-034 |
| 7 | Güvenlik raporu oluştur | — |
| 8 | Tespit edilen açıkları düzelt | — |

### 8.5 Deployment

| Adım | Aksiyon | Kontrol |
|------|---------|---------|
| 1 | Pre-deployment checklist | — |
| 2 | Tüm testlerin geçtiğini doğrula | Test |
| 3 | Vault-sync çalıştır | Vault |
| 4 | Kullanıcı onayını al (Hard Gate) | ✅ HARD GATE |
| 5 | Deployment'ı başlat | — |
| 6 | Health check'leri kontrol et | Health |
| 7 | Post-deployment validation | — |
| 8 | `log.md`'ye tamamlanma kaydı | — |

### 8.6 Session Init

| Adım | Aksiyon | Süre |
|------|---------|------|
| 1 | Boot protokolünü çalıştır (10 dosya) | Max 25s |
| 2 | Session Vault Sync Protocol başlat (5 soru) | Max 10s |
| 3 | Session başlangıç kaydı oluştur | Anlık |
| 4 | MSA limit kontrolü yap | Anlık |
| 5 | Görev bağlamını anla | Değişken |

### 8.7 Vault Sync

**Başlangıç (5 Soru):**

| # | Soru | Kaynak |
|---|------|--------|
| 1 | Son session'dan bu yana ne değişti? | git log, log.md |
| 2 | Yeni ADR var mı? | decisions/accepted/ |
| 3 | Kod değişikliği oldu mu? | git diff |
| 4 | Vault'ta eski bilgi var mı? | VERIFICATION REQUIRED taraması |
| 5 | Skills durumu nedir? | .claude/skills/ |

**Bitiş (6 Adım):**

| # | Adım | Kontrol |
|---|------|---------|
| 1 | Değişiklikleri vault'a yaz (in-place) | Dosya boyutu |
| 2 | `log.md`'ye timestamp ekle | Format |
| 3 | MEMORY.md session state güncelle | Session index |
| 4 | Wiki-link'leri doğrula | Regex |
| 5 | MSA limit kontrolü (15 dosya) | Sayacı |
| 6 | Hallüsinasyon sweep | VERIFICATION REQUIRED |

---

## 9. Hard Gates

| Faz | Hard Gate | Açıklama | İhlal Sonucu |
|-----|-----------|----------|-------------|
| Phase 7 (Vault) | Improvement Proposal | İyileştirme teklifi onayı | Kod revert + CRITICAL log |
| Phase 7 (Product) | Teknik Mimari Tasarım | Mimari plan onayı | Kod revert + CRITICAL log |
| ADR Review | ADR Active'e Geçiş | ADR onayı | ADR Draft'a geri döner |
| Deployment | Production Deploy | Deploy onayı | Deploy iptal |

### 9.1 Onay Formatı

```
[Kullanıcı Onayı] — [Tarih] — [Değişiklik Özeti]
```

**Örnek:**
```
[Bayram Ali Onayı] — [2026-08-08] — [ADR-042 vault yeniden yapılandırma onayı]
```

### 9.2 İhlal Prosedürü

| Adım | Aksiyon |
|------|---------|
| 1 | Hard Gate ihlali tespit edilir |
| 2 | Derhal işlem durdurulur |
| 3 | Yapılan değişiklikler revert edilir |
| 4 | `log.md`'ye CRITICAL giriş eklenir |
| 5 | Vault Steward'a bildirim yapılır |
| 6 | Sousuz sıfırlanır |

---

## 10. Rules

### 10.1 Hard Rules

| # | Kural | İhlal Sonucu |
|---|-------|--------------|
| 1 | User Approval Gate — Onay olmadan kod/doküman yok | İşlem geri alınır |
| 2 | In-Place Modification — Dosya adı/konumu değişmez | Dosya geri yüklenir |
| 3 | No Hallucination → `VERIFICATION REQUIRED` | İçerik silinir |
| 4 | Skill Zorunluluğu — Vault değişikliği varsa vault-sync zorunlu | Oturum geçersiz |
| 5 | MSA Limit = 15 dosya | Görev parçalanır |
| 6 | Middleware Order Immutable | Sistem durdurulur |

### 10.2 Soft Constraints

| # | Kural | Esnetme Koşulu | Onay |
|---|-------|----------------|------|
| 1 | Test ortamında BypassAuth | `?_bypass=1` ile aktif | Security Engineer |
| 2 | 1000 satır dosya limiti | Master Orchestrator onayı ile | MO |
| 3 | 15 dosya MSA limiti | Parçalama stratejisi | Arch Lead |

### 10.3 Workflow'a Özel Kurallar

| Workflow | Ek Kural |
|----------|----------|
| Code Review | Max 15 dosya, 8 adım zorunlu |
| Bug Fix | Root cause analizi zorunlu, regression test |
| New Feature | 20-Fazlı lifecycle, Phase 7 Hard Gate |
| Security Audit | OWASP Top 10:2025 tam liste, ADR referanslı |
| Deployment | Pre-flight + post-flight, Hard Gate |
| Session Init | 10 dosya, 25s timeout, 5 soru |
| Vault Sync | 5 soru + 6 adım, wiki-link doğrulama |

---

## 11. Edge Cases

| Senaryo | Çözüm | ADR |
|---------|-------|-----|
| MSA limit aşımı (>15 dosya) | Index.md fallback + görev parçalama | ADR-042 |
| User rejection (Phase 7) | Phase 4'e geri dön | ADR-007 |
| Session interruption | log.md'den resume | ADR-004 |
| Vault corruption | `git checkout` + son commit | ADR-042 |
| ADR conflict | Escalation (L1→L2→L3) | ADR-008 |
| Concurrent write | Context Lock + Queue | ADR-022 |
| Token overflow | Chunked read | ADR-042 |
| Deprecated API | Otomatik replace + log warning | — |
| Hard Gate bypass | Derhal revert + CRITICAL log | ADR-007 |
| Hallüsinasyon yayılımı | `VERIFICATION REQUIRED` + sweep | ADR-005 |

---

## 12. Warnings

| # | Uyarı | ADR |
|---|-------|-----|
| 1 | Hard Gate atlanırsa mimari bütünlük bozulur | ADR-007 |
| 2 | Vault bozulursa git ile kurtarma yapılır | ADR-042 |
| 3 | Hallüsinasyon yayılırsa tüm ekosistem yanıltılır | ADR-005 |
| 4 | MSA limiti aşılırsa token aşımı oluşur | ADR-042 |
| 5 | Vault-sync yapılmazsa vault-kod tutarsızlığı oluşur | ADR-042 |
| 6 | Middleware sırası değiştirilirse CSP/CSRF bozulur | ADR-010/011/012/013/022 |
| 7 | Frozen ADR değiştirilirse karar tutarsızlığı oluşur | ADR-042 |

---

## 13. İleriye Yönelik Yol Haritası

| Versiyon | Özellik | Tahmini |
|----------|---------|---------|
| v19.0 | Enhanced Workflow Automation (mevcut) | 2026 Q3 |
| v20.0 | Cross-Project Workflow (WirelessConnect) | 2026 Q4 |
| v21.0 | Otonom Workflow (insansız validation) | 2027 Q1 |
| v22.0 | Vektörel Semantic Search | 2027 Q2 |
| v23.0 | Multi-Agent Learning | 2027 Q3 |
| v24.0 | Tam Otonom Çalışma (Zero Human Intervention) | 2027 Q4 |

---

## 14. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Ana sözleşme, AI anayasası |
| [[AGENTS.md]] | Agent yetkileri, handover |
| [[index.md]] | Master katalog |
| [[keys.md]] | Keyword haritası |
| [[brain.md]] | Mimari kararlar |
| [[MEMORY.md]] | Session hafızası |
| [[log.md]] | Audit trail |
| [[engine.md]] | Orkestrasyon motoru |

---

## 15. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Core Principles | [[ADR-007-cache-namespace]] | Zero Code Before Plan |
| § 5 Vault Refactoring | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit |
| § 7 ADR Lifecycle | [[ADR-004-multi-domain-spa]] | Vault versiyonlama |
| § 8.4 Security Audit | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 8.4 Security Audit | [[ADR-022-database-hardened-security]] | Şifreleme |
| § 9 Hard Gates | [[ADR-007-cache-namespace]] | Onay mekanizması |
| § 10 Rules | [[ADR-008-bypass-auth-middleware]] | Auth bypass |
| § 11 Edge Cases | [[ADR-044-dynamic-user-theme-engine]] | Tema engine |

---

## 16. Sözlük

| Terim | Tanım |
|-------|-------|
| **Workflow** | Belirli bir amaca yönelik adımlar dizisi |
| **Hard Gate** | Kullanıcı onayı olmadan geçilemeyen nokta |
| **Zero Code Before Plan** | Plan olmadan kod yazma yasağı |
| **In-Place** | Dosya adı/konumu değişmeden güncelleme |
| **Append-Only** | Sadece ekleme, silme/güncelleme yok |
| **MSA** | Max 15 dosya okuma limiti |
| **Vault Sync** | Vault-kod tutarlılığı |
| **ADR Lifecycle** | Draft → Review → Active → Frozen |
| **Pre-flight** | Görev öncesi kontroller |
| **Context Lock** | Eşzamanlı erişim kilidi |
| **Regression Test** | Yeni değişikliğin eski kodu bozmadığını doğrulama |
| **Root Cause** | Hatanın kök nedeni |
| **Cross-reference** | Dosyalar arası çapraz referans |
| **Hallüsinasyon** | Doğrulanamayan bilgi üretme |
| **Sparse Attention** | Seçici okuma (MSA) |

---

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 19.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 17 |
| ADR References | 8 |
| Workflows | 7 |
| Hard Rules | 6 |
| Soft Constraints | 3 |
| Edge Cases | 10 |
| Hard Gates | 4 |
| Warnings | 7 |
| Glossary Terms | 15 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode