---
type: adr
category: vault
title: "ADR-042: Vault Restructuring"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-042: Vault Restructuring

**Status:** Active (güncellenebilir)
**Kategorisi:** Vault Architecture
**İlgili Agent:** [[.agents/master-orchestrator]]
**İlgili Division:** Vault System

---

## 1. Amaç

Bu ADR, CoreMusic `.ai/` vault'unun yeniden yapılandırılmasını, MSA (Master System Architecture / Sparse Attention) limitini, PHP 8.4 standartlarını, port 81 zorunluluğunu ve vault yönetim kurallarını tanımlar. Karar, tüm AI ajanlarının token kullanımını, dosya erişimini ve vault ile kod arasındaki tutarlılığı doğrudan etkiler.

CoreMusic'in vault yeniden yapılandırma hedefi:
- Token ekonomisi: Görev başına max 15 dosya okuma (MSA limiti)
- Teknoloji standardı: PHP 8.4+ zorunlu, port 81 sabit
- Vault bütünlüğü: SSOT (Single Source of Truth) prensibi
- İzlenebilirlik: Audit trail ile tüm değişikliklerin loglanması
- Bakım kolaylığı: Modüler vault yapısı, kolay navigasyon

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic vault'u (`.ai/`) 404 markdown dosyasından oluşur:
- 9 SSOT core dosyası (CLAUDE, AGENTS, WORKFLOW, index, keys, brain, MEMORY, log, engine)
- 50 ADR (Architecture Decision Record)
- 9 BCNF veritabanı şeması
- 10 panel tanımı
- 7 servis tanımı
- Test stratejileri
- Donanım tasarım dosyaları
- Prompt sistemi
- Workflow tanımları

### 2.2 Token Sorunu

AI ajanları büyük vault'ta çalışırken token aşımı yaşıyor:
- Tüm vault dosyalarını okumak ~100K token gerektiriyor
- Claude context window ~200K token
- Tek bir görev için tüm vault'u okumak verimsiz
- Seçici okuma (Sparse Attention) gerekli

### 2.3 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | MSA Limit | Max 15 dosya/görev | ADR-042 |
| R2 | PHP | 8.4+ (strict_types) | ADR-042 |
| R3 | Port | 81 (music.coremusic.net) | ADR-042 |
| R4 | SSOT | Tek doğruluk kaynağı | ADR-042 |
| R5 | Audit trail | Append-only log | ADR-004 |
| R6 | Wiki-link | Çapraz referanslar | ADR-042 |
| R7 | Frontmatter | 7 zorunlu alan | ADR-042 |
| R8 | Version | Semantic versioning | ADR-042 |

### 2.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Token limiti | Context window ~200K token |
| C2 | Dosya boyutu | Max 1000 satır/dosya |
| C3 | MSA limiti | Max 15 dosya/görev |
| C4 | Frontmatter | 7 zorunlu alan |
| C5 | Wiki-link formatı | [[dosya/yolu]] formatı |

---

## 3. Karar

CoreMusic'te **MSA limiti, PHP 8.4, port 81** standartları uygulanacak ve vault yeniden yapılandırılacak.

### 3.1 MSA Limiti (Sparse Attention)

**Görev başına MAX 15 dosya okunur.**

| Öncelik | Dosya Grubu | Max | Toplam |
|---------|-------------|-----|--------|
| P0 (Kritik) | CLAUDE.md, AGENTS.md, WORKFLOW.md | 3 | 3 |
| P1 (Yüksek) | index.md, keys.md, brain.md, MEMORY.md, log.md | 5 | 8 |
| P2 (Görev) | decisions/accepted/ADR-NNN, architecture/L[0-3]/* | 5 | 13 |
| P3 (Düşük) | testing/*, ui-design/*, personas/* | 2 | 15 |

**Kurallar:**
1. P0 → P1 → P2 → P3 sırasıyla okunur
2. Fallback: `index.md`
3. Limit aşılırsa `log.md`'ye WARN yazılır
4. Token aşımı önlenir: gereksiz dosya okunmaz
5. Seçici okuma (Sparse Attention) uygulanır

### 3.2 PHP 8.4 Standartı

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | strict_types | Her PHP dosyasında `declare(strict_types=1)` |
| 2 | Constructor injection | Bağımlılık enjeksiyonu |
| 3 | Typed properties | Tip tanımlı özellikler |
| 4 | Named arguments | İsimli argümanlar |
| 5 | Match expression | Switch yerine match |
| 6 | Null safe operator | ?-> operatörü |
| 7 | Fiber | Hafif iplik (thread) |
| 8 | Enum | Sayısal ve string enum'lar |

### 3.3 Port 81 Zorunluluğu

| Port | Servis | Protokol | Değişmez mi? |
|------|--------|----------|-------------|
| 81 | music.coremusic.net (Control Service) | HTTP | ✅ Sabit |
| 80 | admin.coremusic.net | HTTP | Sabit |
| 3001 | download.coremusic.net | HTTP/WS | Sabit |
| 3306 | MySQL 9 BCNF DB | TCP | Sabit |
| 5000/6000 | media.coremusic.net | HTTP | Sabit |
| 9741/9742 | Audio Service | REST/WS | Sabit |

---

## 4. Teknik Detaylar

### 4.1 Vault Dosya Yapısı

```
.ai/
├── CLAUDE.md                    ← Ana sözleşme (SSOT)
├── AGENTS.md                    ← Agent kayıt defteri
├── WORKFLOW.md                  ← Süreçler
├── index.md                     ← Master katalog
├── keys.md                      ← Keyword haritası
├── brain.md                     ← Mimari kararlar
├── MEMORY.md                    ← Session hafızası
├── log.md                       ← Audit trail
├── engine.md                    ← Orkestrasyon motoru
├── decisions/
│   ├── accepted/
│   │   ├── ADR-001-vanilla-js-itcss.md
│   │   ├── ADR-002-pdo-mandatory-no-orm.md
│   │   └── ... (50 ADR)
│   ├── rejected/
│   │   └── README.md
│   └── draft/
│       └── ...
├── architecture/
│   ├── l0-infrastructure.md
│   ├── l1-security.md
│   ├── l2-routing.md
│   ├── l3-presentation.md
│   └── 05-data/
│       └── database_master.md
├── testing/
│   ├── strategy.md
│   └── coverage-targets.md
├── electronic/
│   ├── audio-interface-design.md
│   └── hardware-roadmap.md
├── projects/
│   ├── NevaEngine/
│   └── NevaPlayer/
├── subdomains/
│   └── README.md
├── ecosystem/
│   └── 7-service-integration.md
├── .templates/
│   └── index.md
└── prompt-system/
    └── coremusic-theme-prompt.md
```

### 4.2 Frontmatter Standardı

Her vault dosyası şu 7 zorunlu alanı içermeli:

```yaml
---
type: adr | guide | system | architecture
category: audio | architecture | database | security | vault | ui | ai | auth
title: "Dosya başlığı"
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active | draft | frozen
version: X.Y.Z
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

### 4.3 Wiki-Link Formatı

| Format | Açıklama | Örnek |
|--------|----------|-------|
| `[[dosya/yolu]]` | Doğrudan dosya referansı | [[brain.md]] |
| `[[ADR-NNN-title]]` | ADR referansı | [[ADR-010-csrf-protection-strategy]] |
| `[[architecture/l0-infrastructure]]` | Mimari dosya | [[architecture/l1-security]] |

**Doğrulama Regex:** `\[\[([^\]]+)\]\]`

### 4.4 Log Formatı

```
[YYYY-MM-DD HH:MM:SS] [LEVEL] [AGENT/MODULE] [ACTION] Açıklama
```

| Alan | Format | Örnek |
|------|--------|-------|
| Timestamp | `YYYY-MM-DD HH:MM:SS` (UTC) | `2026-08-06 14:30:00` |
| Level | INFO / WARN / ERROR / CRITICAL | `CRITICAL` |
| Agent | agent-name | `security-engineer` |
| Action | CREATE / UPDATE / DELETE / REFACTOR | `CREATE` |

### 4.5 Boot Protokolü (10 Dosya)

| # | Dosya | Amac | Öncelik | Timeout |
|---|-------|------|---------|---------|
| 1 | CLAUDE.md | Kanonik AI talimatı | P0 | 3s |
| 2 | AGENTS.md | Agent kayıt defteri | P0 | 3s |
| 3 | WORKFLOW.md | Süreçler | P0 | 3s |
| 4 | index.md | Master katalog | P1 | 4s |
| 5 | keys.md | Keyword haritası | P1 | 3s |
| 6 | brain.md | Mimari kararlar | P1 | 4s |
| 7 | MEMORY.md | Oturum hafızası | P1 | 3s |
| 8 | log.md | Aktivite günlüğü | P1 | 2s |
| 9 | .claude/rules/* | Tüm kurallar | P2 | 5s |
| 10 | engine.md | Orkestrasyon motoru | P1 | 3s |

**Toplam boot süresi:** Max 25 saniye.

### 4.6 Vault Sync Protokolü

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
| 2 | log.md'ye timestamp ekle | Format |
| 3 | MEMORY.md session state güncelle | Session index |
| 4 | Wiki-link'leri doğrula | Regex |
| 5 | MSA limit kontrolü (15 dosya) | Sayacı |
| 6 | Hallüsinasyon sweep | VERIFICATION REQUIRED |

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | MSA limiti aşımı (>15 dosya) | Görev parçalama, fallback index.md | ADR-042 |
| 2 | Frontmatter eksik | 7 zorunlu alan | ADR-042 |
| 3 | Wiki-link kirik | Doğru dosya yolu | ADR-042 |
| 4 | Hardcoded secret vault'ta | .env / credential vault | ADR-034 |
| 5 | log.md'de silme/değiştirme | Append-only | ADR-004 |
| 6 | Port 81 dışı PHP | music.coremusic.net = port 81 | ADR-042 |
| 7 | PHP 8.4 dışı | strict_types, 8.4+ | ADR-042 |
| 8 | Dosya boyutu >1000 satır | Parçalama veya arşivleme | ADR-042 |
| 9 | Hallüsinasyon | VERIFICATION REQUIRED etiketi | ADR-005 |
| 10 | Token aşımı | Sparse Attention uygula | ADR-042 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | MSA limit aşımı | >15 dosya task | Index.md fallback + görev parçalama | ADR-042 |
| 2 | Vault bozulması | Dosya silinmesi | git checkout + son commit | ADR-042 |
| 3 | Token overflow | Büyük context | Chunked read | ADR-042 |
| 4 | Kirik wiki-link | Dosya taşınması | Cross-reference update | ADR-042 |
| 5 | Frontmatter eksik | Yeni dosya | 7 zorunlu alan ekleme | ADR-042 |
| 6 | log.md boyutu >1000 | Aktif kullanım | Rotasyon (arşivleme) | ADR-004 |
| 7 | Hallüsinasyon yayılımı | Yanlış bilgi | VERIFICATION REQUIRED + sweep | ADR-005 |
| 8 | Session kaybı | Oturum kesintisi | log.md'den resume | ADR-004 |
| 9 | Concurrent vault write | Eşzamanlı erişim | Context Lock + Queue | ADR-022 |
| 10 | Eski ADR referansı | Eski dosya yolu | decisions/accepted/ tarama | ADR-042 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | MSA Limit = 15 dosya | Görev başına max 15 dosya | Görev parçalanır |
| G2 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert edilir |
| G3 | SSOT | Bilgi sadece .ai/ vault'tan | Harici bilgi reddedilir |
| G4 | Frontmatter zorunlu | 7 alan her dosyada | Dosya geçersiz |
| G5 | Wiki-link formatı | [[dosya/yolu]] formatı | Referans kırık |
| G6 | log.md Append-Only | Geçmiş satırlar silinemez | Audit trail bozulur |
| G7 | PHP 8.4+ | strict_types zorunlu | Kod reddedilir |
| G8 | Port 81 sabit | music.coremusic.net | Servis çökmesi |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Vault versiyonlama |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination | Hallüsinasyon kontrolü |
| [[ADR-007-cache-namespace]] | Cache namespace | Zero Code Before Plan |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | Security middleware |
| [[ADR-011-session-management]] | Session yönetimi | Boot protokolü |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Vault güvenliği |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Secret yönetimi |
| [[ADR-038-8.1-sound-card-chip-selection]] | Ses donanımı | Donanım kararları |
| [[ADR-040-database-authority]] | 9 BCNF DB | DB otoritesi |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[MEMORY.md]] §8 | MSA Sparse Attention |
| § 3.2 | [[brain.md]] §18 | Coding standards |
| § 3.3 | [[brain.md]] §11 | Port haritası |
| § 4.1 | [[index.md]] §2 | Quick reference |
| § 4.2 | [[WORKFLOW.md]] §7 | ADR lifecycle |
| § 4.4 | [[log.md]] §6 | Log formatı |
| § 4.5 | [[MEMORY.md]] §5 | Boot protokolü |
| § 4.6 | [[MEMORY.md]] §6-7 | Vault sync |
| § 7 | [[CLAUDE.md]] §7 | Hard guardrails |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **MSA** | Master System Architecture / Sparse Attention — Görev başına max 15 dosya okuma protokolü |
| **SSOT** | Single Source of Truth — Tek Doğruluk Kaynağı |
| **Sparse Attention** | Seçici okuma — Token tasarrufu için dosya filtreleme |
| **Frontmatter** | Dosya başlığı — 7 zorunlu alan |
| **Wiki-Link** | `[[dosya/yolu]]` formatında çapraz referans |
| **Boot Protokolü** | Oturum başlatma — 10 dosya, max 25s |
| **Vault Sync** | Vault ile kod arasındaki tutarlılığı sağlama |
| **Audit Trail** | Tüm değişikliklerin timestamp ile loglanması |
| **Append-Only** | Sadece ekleme, geçmiş satırlar silinemez |
| **Hallüsinasyon** | Doğrulanamayan bilgi üretme |
| **Token** | AI context window birimi (~4 karakter = 1 token) |
| **Context Window** | AI modelinin aynı anda görebildiği maksimum metin |
| **Chunked Read** | Dosyanın parça parça okunması |
| **Rotasyon** | Eski log kayıtlarının arşive taşınması |
| **In-Place Modification** | Dosya adı/konumu değişmeden güncelleme |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| MSA Limit | 15 dosya/görev |
| Vault Dosya Sayısı | 404 |
| ADR Sayısı | 50 |
| SSOT Core Dosya | 9 |
| Frontmatter Alanı | 7 zorunlu |
| Boot Süresi | Max 25s |
| Boot Dosyası | 10 |
| Vault Sync Başlangıç | 5 soru |
| Vault Sync Bitiş | 6 adım |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 10 |
| İlgili ADR | 9 |
| Çapraz Referans | 9 |
| Sözlük Terim | 15 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Review Status | Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Version | 2.0.0 |
| Immutability | Active (güncellenebilir) |
| Next Review | Vault boyutu >500 dosya olduğunda |
| Related Division | Vault System |
| Risk Seviyesi | Yüksek (tüm vault yapısını etkiler) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | Vault deploy | Git ile version control |
| 2 | Migration script | Eski vault'tan yeni yapına |
| 3 | Backup | Vault öncesi tam yedek |
| 4 | Validation | Wiki-link doğrulama |
| 5 | Team sync | Tüm ajanlar aynı versiyonda |
| 6 | Documentation | Değişiklik logu |
| 7 | Rollback | Git revert ile geri alma |
| 8 | Monitoring | Dosya boyutu izleme |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Link Validation | Wiki-link doğrulama | Custom script |
| Frontmatter Check | 7 zorunlu alan | Regex |
| MSA Compliance | 15 dosya limiti | Custom script |
| Size Check | 1000 satır limiti | Custom script |
| Format Check | Log formatı | Regex |
| Cross-Reference | Çapraz referans | Custom script |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Token overflow | Orta | Yüksek | MSA limit |
| R2 | Vault bozulması | Düşük | Yüksek | Git backup |
| R3 | Hallüsinasyon | Orta | Yüksek | VERIFICATION REQUIRED |
| R4 | Kirik link | Orta | Orta | Validation script |
| R5 | Frontmatter eksik | Düşük | Düşük | Template |
| R6 | Eski bilgi | Orta | Orta | Vault sync |
| R7 | Eşzamanlı write | Düşük | Orta | Context Lock |
| R8 | Boyut aşımı | Düşük | Orta | Rotasyon |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Vault integrity check | Haftalık | Master Orchestrator |
| 2 | Wiki-link validation | Aylık | Master Orchestrator |
| 3 | Frontmatter audit | Aylık | Master Orchestrator |
| 4 | Log rotation | İhtiyaca göre | Master Orchestrator |
| 5 | Backup | Haftalık | DevOps Engineer |
| 6 | Performance audit | Aylık | QA Engineer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | Vektörel search | Planlanıyor | Semantic search |
| 2 | AI-powered nav | Araştırılıyor | Otomatik yönlendirme |
| 3 | Auto-validation | Planlanıyor | Real-time kontrol |
| 4 | Version control++ | Gelecek | Branch-based vault |
| 5 | Multi-vault | Araştırılıyor | Cross-project |
| 6 | Vault analytics | Planlanıyor | Kullanım istatistikleri |

---

## 18. Vault Dosya Kategorileri

| Kategori | Dosya Sayısı | Amaç | Öncelik |
|----------|-------------|------|---------|
| SSOT Core | 9 | Ana sözleşme ve kurallar | P0 |
| ADR | 50 | Mimari kararlar | P1-P2 |
| Architecture | 8 | Katman tanımları | P2 |
| SQL Schema | 11 | Veritabanı şemaları | P2 |
| Testing | 6 | Test stratejileri | P3 |
| Electronic | 10 | Donanım tasarım | P3 |
| Projects | 17 | Proje dokümanları | P3 |
| Ecosystem | 7 | Servis entegrasyonu | P2 |
| Prompt System | 1 | AI prompt sistemi | P3 |
| UI Design | 4 | UI/UX tasarım | P3 |
| Subdomains | 4 | Panel tanımları | P2 |
| Templates | 1 | Template indeksi | P3 |
| Personas | 3 | Kullanıcı persona | P3 |
| Workflows | 8 | İş akışları | P2 |
| Other | ~256 | Çeşitli dosyalar | P3 |

---

## 19. Vault Health Metrics

| Metrik | Hedef | Uyarı Eşiği | Kritik Eşik |
|--------|-------|-------------|-------------|
| Dosya sayısı | 400-450 | 500 | 600 |
| Toplam boyut | < 10MB | 15MB | 20MB |
| Wiki-link kırık | 0 | 5 | 10 |
| Frontmatter eksik | 0 | 3 | 5 |
| log.md satırı | < 800 | 900 | 1000 |
| MSA ortalama | < 12 | 14 | 15 |
| Boot süresi | < 20s | 23s | 25s |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
