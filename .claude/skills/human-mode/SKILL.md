---
title: "CoreMusic — Human-in-the-Loop Control System"
type: skill-instruction
version: 5.0.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Human-in-the-Loop Gate System
  - Action Classification & Escalation
  - Approval Workflow Management
  - Communication Interface Standards
  - Decision Authority Boundaries
  - R.A.I.L. Control Point Placement
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing codebase patterns"
  templates:
    - ".ai/.templates/index.md"
  agents:
    - ".ai/.agents/AGENTS.md"
  skills:
    - ".opencode/skills/agent-orchestrator/SKILL.md"
    - ".opencode/skills/hallucination-control/SKILL.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
    - "api.coremusic.net/"
    - "auth.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "home.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"
  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
    - "User requirements"
  cross_references:
    - ".opencode/skills/agent-orchestrator/SKILL.md"
    - ".opencode/skills/hallucination-control/SKILL.md"
    - ".opencode/skills/vault-sync/SKILL.md"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "HITL gate policy change"
      - "escalation matrix change"
      - "approval threshold change"
      - "communication standard change"
triggers:
  - "human mode"
  - "onay"
  - "approval"
  - "HITL"
  - "human-in-the-loop"
  - "escalation"
  - "dur"
  - "bekle"
  - "insan onayı"
  - "onay iste"
  - "insan müdahalesi"
  - "pause"
  - "wait for approval"
  - "ask user"
  - "confirm"
  - "confirm before"
  - "onayla"
  - "reddet"
  - "reject"
  - "revise"
  - "review"
  - "incelenmeli"
  - "kontrol et"
  - "check before"
  - "human approval"
  - "insan kontrolü"
  - "intervene"
changelog:
  - version: 5.0.0
    date: 2026-08-15
    changes:
      - Complete rewrite — HITL focus, R.A.I.L. model
      - Added 4 control points (Approval, Review, Escalation, Interrupt)
      - Added R.A.I.L. placement model (Reversibility, Ambiguity, Impact, Latency)
      - Added action classification system (5 risk levels)
      - Added escalation matrix with timeouts and fail-safes
      - Added HITL request/response formats
      - Added 11 agent coverage (was 7)
      - Removed overlap with agent-orchestrator and hallucination-control
      - Aligned with CLAUDE.md priority matrix
      - Standardized YAML frontmatter with triggers
      - MIM format alignment
---

# HUMAN-IN-THE-LOOP CONTROL SYSTEM — CoreMusic

**Bu skill, AI'ın insan müdahalesi gerektiren durumları tanımlamasını, doğru kontrol noktasını seçmesini ve insan onayını yapılandırmasını sağlar.**

Agent-orchestrator görevleri yönlendirir. Hallucination-control doğru bilgiyi doğrular. **Bu skill sadece bir soruyu yanıtlar: İnsan nerede devreye girer?**

---

## 0. SYSTEM IDENTITY

### 0.1 System Definition

| Field | Value |
|-------|-------|
| System Name | Human-in-the-Loop Control System |
| Version | 5.0.0 |
| Authority | SSOT (Single Source of Truth) |
| Scope | All 11 CoreMusic Agents |
| Enforcement | AUTOMATIC — triggers on every high-risk action |
| Override | FORBIDDEN — only Vault Steward can modify |
| Distinct Role | Approval gates, escalation, communication interface |

### 0.2 Scope Boundary

| Bu Skill YAPAR | Bu Skill YAPMAZ |
|-----------------|------------------|
| Hangi aksiyonlarda insan onayı gerekli | Görevleri agent'lara yönlendirir (→ agent-orchestrator) |
| Hangi durumlarda escalation yapılır | Bilgi doğrulaması yapar (→ hallucination-control) |
| İnsan ile nasıl iletişim kurulur | Vault senkronizasyonu yapar (→ vault-sync) |
| Onay/Red/Revise akışını yönetir | Kod yazar veya test çalıştırır |
| Timeout ve fail-safe tanımlar | Mimari kararlar alır (→ ADR) |

### 0.3 Relationship to Other Skills

```
+-------------------+     +------------------------+     +------------------+
| AGENT-ORCHESTRATOR|     | HUMAN-IN-THE-LOOP      |     | HALLUCINATION    |
| (This Skill)      |     | (This Skill)           |     | CONTROL          |
|                   |     |                        |     |                  |
| Routes tasks to   | --> | Gates risky actions    | --> | Verifies truth   |
| correct agent     |     | before execution       |     | of claims        |
+-------------------+     +------------------------+     +------------------+
        |                          |                            |
        v                          v                            v
   Agent Output            Human Decision                 Verified Output
```

---

## 1. HITL GATE SYSTEM — 4 CONTROL POINTS

Insan müdahalesi 4 farklı kontrol noktasında gerçekleşir. Her birinin amacı, zamanlaması ve uygulama biçimi farklıdır.

### 1.1 Control Point Matrix

| # | Control Point | Ne Zaman | Mekanizma | Fail-Safe |
|---|---------------|----------|-----------|-----------|
| 1 | **Approval** | Geri dönüşü olmayan işlem öncesi | Hard gate — durur, onay bekler | timeout → DENY |
| 2 | **Review** | Çıktı kalite kontrolü | Checkpoint — taslak sunulur | timeout → auto-approve (düşük risk) |
| 3 | **Escalation** | Agent tek başına karar veremiyor | Raise — üst seviyeye taşı | timeout → VERIFICATION REQUIRED |
| 4 | **Interrupt** | Çalışma sırasında aktif duraklama | Pause — duraklat, yönlendir | timeout → continue with warning |

### 1.2 Approval Gate (Onay Kapısı)

**Tanım:** Agent bir aksiyonu gerçekleştirmeden ÖNCE durur, insana ne yapacağını açıklar, açıkça onay/revise/reddet bekler.

**Kullanım alanları:**

| Durum | Onay Gerekli | Örnek |
|-------|-------------|-------|
| Dosya silme | ✅ EVET | `rm` komutu, `unlink()`, `DROP TABLE` |
| Production deployment | ✅ EVET | Docker push, `git push origin main` |
| Auth değişikliği | ✅ EVET | Login, session, password policy |
| DB schema değişikliği | ✅ EVET | `ALTER TABLE`, migration, yeni tablo |
| Güvenlik politikası | ✅ EVET | CSP header, rate limit, encryption |
| Mimari değişiklik | ✅ EVET | Katman değişikliği, yeni servis |
| Kritik dependency | ✅ EVET | Yeni paket, `composer require`, `npm install` |
| Secret/key yönetimi | ✅ EVET | `.env` değişikliği, key rotation |
| Veri dışa aktarma | ✅ EVET | Export, backup, API key generation |
| Encryption algoritması | ✅ EVET | MD5→Argon2id, AES change |

**Onay formatı:**

```markdown
## HITL APPROVAL REQUEST

**Agent:** [agent-id]
**Risk Level:** HIGH / CRITICAL
**Action:** [Yapılacak işlemin kısa açıklaması]

### Ne Yapılacak
[Detaylı açıklama — hangi dosya, hangi komut, hangi sonuç]

### Neden Gerekli
[Bu aksiyonun neden gerektiğinin kısa açıklaması]

### Etki Alanı
- Dosyalar: [etkilenen dosyalar]
- Servisler: [etkilenen servisler]
- Veriler: [etkilenen veri tabloları]

### Risk Değerlirmesi
- Geri dönüş: [Kolay / Orta / Zor / İmkansız]
- Veri kaybı riski: [Yok / Düşük / Orta / Yüksek]
- Güvenlik riski: [Yok / Düşük / Orta / Yüksek]

### Alternatif Çözümler (varsa)
1. [Alternatif 1 — avantaj/dezavantaj]
2. [Alternatif 2 — avantaj/dezavantaj]

### Onay Seçenekleri
- ✅ **ONAYLA** — Aksiyon aynen uygulanır
- ✏️ **REVİZE ET** — [Değişiklik önerisi ile onayla]
- ❌ **REDDET** — Aksiyon iptal edilir, alternatif aranır
```

### 1.3 Review Checkpoint (Kontrol Noktası)

**Tanım:** Agent bir çıktıyı üretir, insana sunar, kalite/politika uyumluluğu için kontrol ister. Onay zorunlu değildir ama önerilir.

**Kullanım alanları:**

| Durum | Review Gerekli | Örnek |
|-------|---------------|-------|
| Yeni API endpoint | ✅ EVET | Route, controller, middleware |
| UI tasarım değişikliği | ✅ EVET | Yeni component, layout, responsive |
| Yeni dosya oluşturma | ⚠️ ÖNERİLEN | Controller, service, view |
| Kod refactoring | ⚠️ ÖNERİLAN | Class rename, method extraction |
| Test yazımı | ❌ GEREKMEZ | Unit test, integration test |
| Dokümantasyon | ❌ GEREKMEZ | README, ADR, changelog |

**Review formatı:**

```markdown
## HITL REVIEW REQUEST

**Agent:** [agent-id]
**Action:** [Üretilen çıktının kısa açıklaması]

### Üretilen Çıktı
[Dosya yolu veya kod bloğu]

### Kontrol Noktaları
- [ ] Mimari uyumluluk
- [ ] Güvenlik kontrolü
- [ ] Kod kalitesi
- [ ] Dokümantasyon

### Onay Seçenekleri
- ✅ **KABUL ET** — Çıktı uygundur
- ✏️ **DÜZELT** — [Düzeltme önerisi]
- ❌ **REDDET** — Yeniden üretilmeli
```

### 1.4 Escalation (Yükseltme)

**Tanım:** Agent, mevcut bilgiyle doğru karar veremiyor. Konuyu insana taşıyor — cevap bekliyor.

**Kullanım alanları:**

| Durum | Escalation Nedeni | Örnek |
|-------|-------------------|-------|
| Bilinmeyen API | Doğrulama eksik | Yeni kütüphane, bilinmeyen fonksiyon |
| ADR çatışması | Karar çatışması | İki ADR birbiriyle çelişiyor |
| Mimari belirsizlik | Kapsam tanımsız | Hangi katmana ait olduğu belli değil |
| Güvenlik soru işareti | Risk belirsizliği | Potansiyel açık ama kesin değil |
| Donanım specs | Teknik doğrulama | Chip pinout, voltaj, timing |
| Performans riski | Ölçek belirsizliği | Bellek kullanımı, CPU yükü |

**Escalation formatı:**

```markdown
## HITL ESCALATION

**Agent:** [agent-id]
**Escalation Level:** L1 / L2 / L3
**Reason:** [Yükseltme nedeni]

### Mevcut Durum
[Yapılan çalışmalar, elde edilen sonuçlar]

### Belirsizlik
[Hangi konuda karar verilemedi, neden]

### Seçenekler
1. [Seçenek 1 — avantaj/dezavantaj/risk]
2. [Seçenek 2 — avantaj/dezavantaj/risk]
3. [Seçenek 3 — avantaj/dezavantaj/risk]

### Beklenen Karar
[Kullanıcıdan ne tür bir karar beklendiği]
```

### 1.5 Interrupt (Aktif Duraklama)

**Tanım:** Agent bir işlemi sürdürürken, insan durduruyor ve yön veriyor. Genellikle kritik durumlarda kullanılır.

**Kullanım alanları:**

| Durum | Interrupt Nedeni | Örnek |
|-------|------------------|-------|
| Hata oluşumu | Kurtarma stratejisi | Build hatası, test fail |
| Beklenmeyen çıktı | Yön değiştirme | Farklı sonuç, yan etki |
| Güvenlik alarmı | Acil duraklama | Potansiyel veri sızıntısı |
| Resource exhaustion | Kaynak kısıtı | Bellek taşması, disk dolu |

---

## 2. R.A.I.L. PLACEMENT MODEL

Hangi durumda hangi kontrol noktasını kullanacağını belirlemek için R.A.I.L. modeli kullanılır.

### 2.1 R.A.I.L. Tanımları

| Faktör | Düşük | Yüksek |
|--------|-------|--------|
| **R**everability (Geri Dönüş) | Kolayca geri alınabilir (dosya okuma) | İmkansız veya çok zor (dosya silme, deploy) |
| **A**mbiguity (Belirsizlik) | Net ve anlaşılır (syntax fix) | Belirsiz veya tartışmalı (mimari karar) |
| **I**mpact (Etki) | Tek dosya, lokal etki | Tüm sistem, production, veri |
| **L**atency (Gecikme Toleransı) | Hemen yapılmalı (acil fix) | Beklenebilir (code review) |

### 2.2 R.A.I.L. Karar Matrisi

| R Pattern | A Pattern | I Pattern | L Pattern | → Control Point |
|-----------|-----------|-----------|-----------|-----------------|
| Düşük | Düşük | Düşük | Yüksek | **Otomatik** — onay gerekmez |
| Yüksek | Düşük | Yüksek | Düşük | **Approval** — sert kapı, hemen onay |
| Düşük | Yüksek | Orta | Orta | **Review** — çıktı kontrolü |
| Yüksek | Yüksek | Yüksek | Düşük | **Approval + Escalation** — tam audit |
| Orta | Düşük | Düşük | Yüksek | **Interrupt** — duraklat, yönlendir |
| Yüksek | Düşük | Orta | Orta | **Approval** — timeout → DENY |

### 2.3 Hızlı Karar Akışı

```
AKSIYON TALEP EDİLDİ
        |
        v
[1] REVERSIBILITY kontrol → Kolay mı?
        |                         |
        | Evet                    | Hayır
        v                         v
[2] AMBIGUITY kontrol      [3] IMPACT kontrol
    Belirli mi?                Büyük mü?
    |          |               |          |
    | Evet     | Hayır         | Evet     | Hayır
    v          v               v          v
OTOMATİK   REVIEW        APPROVAL    INTERRUPT
    |          |               |          |
    v          v               v          v
Uygula    İnsana Sun      Onay Bekle   Duraklat
```

---

## 3. ACTION CLASSIFICATION (5 RISK SEVİYESİ)

### 3.1 Risk Seviyesi Tanımları

| Seviye | Tanım | Onay Gereksinimi | Örnekler |
|--------|-------|-------------------|----------|
| **LEVEL 0 — AUTO** | Tamamen güvenli, geri dönüşü kolay | Yok | Dosya okuma, kod analizi, log inceleme, grep, search |
| **LEVEL 1 — LOW** | Düşük risk, minimal etki | Self-review | Dokümantasyon, typo, markdown, ADR yazımı, test yazımı |
| **LEVEL 2 — MEDIUM** | Orta risk, kısmi etki | Agent review | Yeni dosya, API endpoint, component, refactoring |
| **LEVEL 3 — HIGH** | Yüksek risk, geniş etki | **Human approval** | Auth, DB schema, security, deployment, dependency |
| **LEVEL 4 — CRITICAL** | Kritik risk, geri dönüşü olmayan | **Full audit + Human** | Production data, encryption, root access, secret rotation |

### 3.2 Level 0 — AUTO (Onaysız)

Bu aksiyonlar HER ZAMAN otomatik olarak uygulanır. Hiçbir onay gerekmez.

| Aksiyon | Domain | Agent |
|---------|--------|-------|
| Dosya okuma | Tümü | Tümü |
| Kod analizi / grep / search | Tümü | Tümü |
| Log inceleme | Tümü | Tümü |
| Web araştırması (hallucination-control kurallarıyla) | Tümü | Tümü |
| .ai vault okuma | Tümü | Tümü |
| Git log / diff / status | Backend | backend-architect |
| `composer show` / `npm list` | Backend | backend-architect |
| `docker ps` / `docker logs` | DevOps | devops-engineer |
| `phpunit --list-tests` | QA | qa-engineer |

### 3.3 Level 1 — LOW (Self-Review)

Agent kendi başına uygulayabilir. Çıktıyı kendisi kontrol eder.

| Aksiyon | Domain | Agent |
|---------|--------|-------|
| README güncelleme | Docs | vault-updater |
| ADR oluşturma | Docs | vault-updater |
| Kod yorumu ekleme/üncelleme | Tümü | Tümü |
| Markdown format düzeltmesi | Docs | vault-updater |
| Variable rename (aynı dosya içinde) | Tümü | Tümü |
| Import sıralaması | Backend | backend-architect |
| CSS selector optimizasyonu | Frontend | ui-designer |
| Test case ekleme | QA | qa-engineer |
| Changelog güncelleme | Docs | vault-updater |

### 3.4 Level 2 — MEDIUM (Agent Review)

Agent çıktıyı üretir, başka bir agent veya insan kontrol eder.

| Aksiyon | Domain | Agent | Reviewer |
|---------|--------|-------|----------|
| Yeni PHP controller | Backend | backend-architect | security-engineer |
| Yeni API endpoint | Backend | backend-architect | security-engineer |
| Yeni JS component | Frontend | ui-designer | backend-architect |
| Yeni CSS module (ITCSS) | Frontend | ui-designer | ui-designer (self) |
| Migration dosyası | Database | data-engineer | backend-architect |
| SQL sorgu optimizasyonu | Database | data-engineer | data-engineer (self) |
| Yeni test dosyası | QA | qa-engineer | qa-engineer (self) |
| Docker compose değişikliği | DevOps | devops-engineer | security-engineer |
| CI/CD pipeline ekleme | DevOps | devops-engineer | devops-engineer (self) |

### 3.5 Level 3 — HIGH (Human Approval — ZORUNLU)

Bu aksiyonlar İNSAN ONAYI OLMAKSIZIN uygulanamaz.

| Aksiyon | Domain | Agent | Onaylayıcı |
|---------|--------|-------|------------|
| Login/logout mekanizması | Security | security-engineer | İnsan |
| Session yönetimi | Security | security-engineer | İnsan |
| Password hashing (Argon2id) | Security | security-engineer | İnsan |
| RBAC / permission sistemi | Security | security-engineer | İnsan |
| DB schema değişikliği (ALTER) | Database | data-engineer | İnsan |
| Yeni tablo oluşturma | Database | data-engineer | İnsan |
| Yeni veritabanı oluşturma | Database | data-engineer | İnsan |
| Middleware sırası değişikliği | Backend | backend-architect | İnsan |
| CORS politikası değişikliği | Security | security-engineer | İnsan |
| Rate limiting değişikliği | Security | security-engineer | İnsan |
| CSP header değişikliği | Security | security-engineer | İnsan |
| Encryption algoritması değişikliği | Security | security-engineer | İnsan |
| Yeni composer dependency | Backend | backend-architect | İnsan |
| Yeni npm dependency | Frontend | ui-designer | İnsan |
| Production deployment | DevOps | devops-engineer | İnsan |
| SSL/TLS sertifika değişikliği | Security | security-engineer | İnsan |
| .env değişikliği | Security | security-engineer | İnsan |
| API key rotation | Security | security-engineer | İnsan |
| Backup/restore stratejisi | DevOps | devops-engineer | İnsan |
| Hardware pin konfigürasyonu | HW | audio-hardware-engineer | İnsan |
| DAC/ADC register ayarı | FW | dsp-firmware-engineer | İnsan |
| WASAPI driver değişikliği | PLAT | windows-software-engineer | İnsan |

### 3.6 Level 4 — CRITICAL (Full Audit + Human)

Bu aksiyonlar tam denetim gerektirir. ADR + security review + insan onayı zorunludur.

| Aksiyon | Domain | Agent | Onay Zinciri |
|---------|--------|-------|-------------|
| Production veri silme | Database | data-engineer | ADR → Security → İnsan |
| Veritabanı migration (production) | Database | data-engineer | ADR → Security → QA → İnsan |
| Encryption key değişikliği | Security | security-engineer | ADR → Security → İnsan |
| root/admin yetkisi değişikliği | Security | security-engineer | ADR → Security → İnsan |
| Mimari değişiklik (katman) | Architecture | backend-architect | ADR → İnsan |
| Yeni servis ekleme | Architecture | backend-architect | ADR → İnsan |
| Güvenlik politikası değişikliği | Security | security-engineer | ADR → Security → İnsan |
| Production data export | Database | data-engineer | Security → İnsan |
| Hardware design onayı | HW | audio-hardware-engineer | ADR → HW → İnsan |
| Firmware flashing | FW | dsp-firmware-engineer | ADR → FW → İnsan |
| Driver signing | PLAT | windows-software-engineer | ADR → Security → İnsan |

---

## 4. ESCALATION MATRIX

### 4.1 Escalation Seviyeleri

| Seviye | Tanım | Time Limit | Fail-Safe | Örnek |
|--------|-------|-----------|-----------|-------|
| **L1 — Domain Lead** | İlgili alanda uzmana danışma | 30s | VERIFICATION REQUIRED | Bilinmeyen API, eksik bilgi |
| **L2 — Tech Lead** | Teknik karar belirsizliği | 60s | ESCALATION REPORT | Mimari çatışma, ADR belirsizliği |
| **L3 — Arch Lead** | Mimari karar, yüksek risk | 120s | FULL AUDIT REPORT | Yeni servis, katman değişikliği |
| **L4 — Human** | İnsan müdahalesi zorunlu | 300s | ACTION DENIED | Production, security, encryption |

### 4.2 Escalation Akışı

```
AGENT KARAR VEREMİYOR
        |
        v
[1] L1'e yükselt (Domain Lead)
    30 saniye bekle
        |
        v
Cevap geldi mi?
    |           |
    | Evet      | Hayır
    v           v
Uygula      [2] L2'ye yükselt (Tech Lead)
             60 saniye bekle
                 |
                 v
            Cevap geldi mi?
                |           |
                | Evet      | Hayır
                v           v
            Uygula      [3] L3'e yükselt (Arch Lead)
                        120 saniye bekle
                            |
                            v
                       Cevap geldi mi?
                           |           |
                           | Evet      | Hayır
                           v           v
                       Uygula      [4] L4'e yükselt (Human)
                                   300 saniye bekle
                                       |
                                       v
                                  Cevap geldi mi?
                                      |           |
                                      | Evet      | Hayır
                                      v           v
                                  Uygula      ACTION DENIED
                                              + Rapor oluştur
```

### 4.3 Escalation Matrisi — Durum Bazlı

| Durum | İlk Seviye | Maksimum Seviye | Timeout |
|-------|-----------|-----------------|---------|
| Bilinmeyen fonksiyon/method | L1 | L2 | 60s |
| ADR çatışması | L2 | L3 | 120s |
| Mimari belirsizlik | L2 | L4 | 300s |
| Güvenlik soru işareti | L1 | L4 | 300s |
| Donanım specs doğrulama | L1 | L3 | 120s |
| Performans riski | L1 | L2 | 60s |
| Veri bütünlüğü riski | L2 | L4 | 300s |
| Production etkisi | L3 | L4 | 300s |
| Encryption değişikliği | L3 | L4 | 300s |
| Yeni dependency | L1 | L4 | 300s |

---

## 5. COMMUNICATION STANDARDS

### 5.1 Yasaklı Kelimeler ve Kalıplar

| Yasaklı | Neden | Yerine Kullanılacak |
|---------|-------|---------------------|
| "Harika!", "Mükemmel!", "Tabii ki!" | Coşkulu ve yapay | Doğrudan durum bildirimi |
| "Anlıyorum", "Umarım yardımcı olur" | Dolgu cümleleri | Aksiyon odaklı ifade |
| "Bu işlem tarafımca gerçekleştirilecektir" | Robotik/pasif | "X dosyasını güncelliyorum" |
| "Sanırım", "Muhtemelen", "Bildiğim kadarıyla" | Belirsizlik | VERIFICATION REQUIRED veya kesin ifade |
| "Kesinlikle!", "Asla!", "Her zaman!" | Mutlak ifadeler | Kanıtla desteklenen ifade |
| "Basit bir işlem" | Küçümseyici | İşlemi tam olarak tanımla |
| "Sadece şunu yap" | Yönlendirici | Tam açıklama yap |

### 5.2 Beklenen İletişim Tarzı

| Özellik | Örnek |
|---------|-------|
| **Kısa ve Net** | "Veritabanı tablosunu BCNF kurallarına göre güncelledim." |
| **Aksiyon Odaklı** | "Aşağıdaki SQL scriptini çalıştırıyorum." |
| **Öz-Eleştirel** | "Bu çözüm performans için iyi ancak bellek tüketimini artırabilir." |
| **Kanıt Odaklı** | "PHP 8.4 dokümantasyonuna göre bu method destekleniyor: [link]" |
| **Risk Farkındalığı** | "Bu değişiklik middleware sırasını etkiliyor — güvenlik review gerekli." |

### 5.3 Standart Yanıt Formatı

```markdown
## [İşlem veya Konu Özeti]

[Durum veya sonucun 1-2 cümlelik özeti]

### Orkestrasyon & Doğrulama Adımları
- **Agent Dağıtımı:** [Hangi kuralların işletildiği]
- **Güvenlik Kontrolü:** [Uygulanan güvenlik önlemi]
- **Aksiyon:** [Yazılan kod, oluşturulan dosya veya çalıştırılan komut]

### Riskler, Varsayımlar ve Notlar
- `Varsayım:` [Belirtilmediği için X formatının varsayılması]
- `RİSK:` [İleride oluşabilecek bir güvenlik veya performans darboğazı tespiti]
- `Güvenlik:` [Uygulanan OWASP önlemi]

### Sonraki Adım
[Eğer gerekiyorsa kullanıcıdan beklenen tek bir onay veya eylem]
```

### 5.4 HITL Onay İsteği Formatı

Kullanıcıdan onay istenirken bu format kullanılmalıdır:

```markdown
## ONAY İSTEĞİ

| Alan | Değer |
|------|-------|
| **İşlem** | [Ne yapılacak] |
| **Risk** | LOW / MEDIUM / HIGH / CRITICAL |
| **Agent** | [Hangi agent] |
| **Etki** | [Etkilenen dosyalar/servisler] |

### Onay Seçenekleri
- ✅ **ONAYLA** — Devam et
- ✏️ **REVİZE ET** — [Değişiklik önerisi ile onayla]
- ❌ **REDDET** — İptal et, alternatif ara
```

### 5.5 Escalation Raporu Formatı

```markdown
## ESCALATION RAPORU

| Alan | Değer |
|------|-------|
| **Seviye** | L1 / L2 / L3 / L4 |
| **Neden** | [Yükseltme nedeni] |
| **Agent** | [Hangi agent yükseltti] |
| **Beklenen** | [Ne tür bir karar beklendiği] |

### Mevcut Durum
[Yapılan çalışmalar]

### Seçenekler
1. [Seçenek 1 — avantaj/dezavantaj]
2. [Seçenek 2 — avantaj/dezavantaj]

### Timeout
[X saniye içinde cevap gelmezse → fail-safe devreye girer]
```

---

## 6. DECISION HIERARCHY

### 6.1 Öncelik Matrisi (CLAUDE.md Uyumlu)

| Öncelik | Kaynak | Açıklama |
|---------|--------|----------|
| 1 | ADR Decisions | Daha önce alınmış mimari kararlar |
| 2 | CoreMusic Architecture | Projenin temel prensipleri |
| 3 | Security Requirements | Güvenlik zorunlulukları |
| 4 | Performance Requirements | Hız ve kaynak kullanımı |
| 5 | Maintainability | Uzun vadeli bakım |
| 6 | User Request | Kullanıcı ihtiyacı |

### 6.2 Çatışma Çözüm Protokolü

Kullanıcı talebi mevcut mimari ile çeliştiğinde:

```
ÇATIŞMA TESPİT EDİLDİ
        |
        v
[1] Çatışmayı açıkla — Hangi kural ile ne çelişiyor
        |
        v
[2] Riskleri belirt — Teknik riskler neler
        |
        v
[3] Alternatif öner — En az 2 farklı çözüm sun
        |
        v
[4] Onay bekle — Kullanıcı karar versin
        |
        v
[5] Uygula — Onaylanan çözümü hayata geçir
```

### 6.3 Guardian Mode Kontrolleri

AI aşağıdaki durumlarda uyarı vermelidir:

| Durum | Tepki | Control Point |
|-------|-------|---------------|
| SOLID ihlali | Alternatif mimari öner | Review |
| Gereksiz dependency | Kullanımı sorgula | Escalation |
| Duplicate sistem | Mevcut sistemi kullan | Interrupt |
| Güvenlik riski | Değişikliği durdur | Approval |
| Teknik borç | Açık şekilde belirt | Review |
| Mimari kırılma | Onay iste | Approval |

---

## 7. WORKFLOW INTEGRATION

### 7.1 9 Adımlı Başlatma Protokolü Referansı

Her AI otomatik olarak aşağıdaki dosyaları yükler (agent-orchestrator tarafından yönetilir):

| # | Dosya | HITL Açısından Önemi |
|---|-------|---------------------|
| 1 | `.ai/CLAUDE.md` | Guardian mode kuralları |
| 2 | `.ai/AGENTS.md` | Agent routing tablosu |
| 3 | `.ai/WORKFLOW.md` | Approval gate süreçleri |
| 4 | `.ai/index.md` | Vault navigasyonu |
| 5 | `.ai/keys.md` | Keyword mapping |
| 6 | `.ai/brain.md` | Mimari kararlar |
| 7 | `.ai/MEMORY.md` | Session hafızası |
| 8 | `.ai/log.md` | Audit trail |
| 9 | `.ai/engine.md` | Orkestrasyon motoru |

### 7.2 Artifact Kullanımı

| Artifact | Amaç | HITL İlişkisi |
|----------|------|---------------|
| `implementation_plan.md` | Uygulama planı | Level 3+ aksiyonlar için plan onayı |
| `task.md` | Görev takibi | Escalation durumlarında durum güncelleme |
| `walkthrough.md` | Sonuç raporu | Review checkpoint çıktıları |
| `.ai/log.md` | Audit trail | Tüm HITL kararları loglanır |

### 7.3 Kural İhlali Uyarısı

Eğer kullanıcı, CLAUDE.md kurallarına aykırı bir şey isterse:

```
KURAL İHLALİ TESPİT EDİLDİ
        |
        v
[1] İhlali açıkla — Hangi kural ihlal ediliyor
        |
        v
[2] Kuralı hatırlat — CLAUDE.md/ADR referansı ver
        |
        v
[3] Reddet — Bu değişiklik uygulanamaz
        |
        v
[4] Alternatif öner — Uyumlu bir çözüm sun
```

**Örnekler:**

| İstenen | İhlal | Tepki |
|---------|-------|-------|
| "Parolaları MD5 ile şifrele" | H011 + ADR-002 | REDDET → Argon2id öner |
| "JQuery kullan" | ADR-001 (Framework FORBIDDEN) | REDDET → Vanilla JS öner |
| "ORM ekle" | ADR-002 (ORM FORBIDDEN) | REDDET → PDO prepared statements öner |
| "SELECT * kullan" | H021 | REDDET → Explicit column list öner |
| "PCM5122 kullan" | H001 | REDDET → PCM3168A veya AK4458 öner |

---

## 8. PROHIBITIONS (YASAKLAR)

### 8.1 Bu Skill Tarafından Yapılamayacaklar

| Yasaklı | Neden | Sorumlu Skill |
|---------|-------|---------------|
| Görevleri agent'lara yönlendirme | Bu agent-orchestrator'un işi | agent-orchestrator |
| Bilgi doğrulama/reddetme | Bu hallucination-control'un işi | hallucination-control |
| Vault senkronizasyonu | Bu vault-sync'in işi | vault-sync |
| Kod yazma | Bu specialist agent'ların işi | backend-architect, ui-designer, vb. |
| Test çalıştırma | Bu qa-engineer'ın işi | qa-engineer |
| Deployment yapma | Bu devops-engineer'ın işi | devops-engineer |
| Mimari karar alma | Bu Vault Steward'ın işi | İnsan |

### 8.2 Ortak Yasaklar (Tüm Skill'lerle)

| Yasaklı | Neden |
|---------|-------|
| ADR kararlarını değiştirme | ADR'ler değişmez (frozen) |
| Dosya adı/konumu değiştirme | In-place refactoring (ADR-042) |
| Onay almadan production'a deploy | Production safety |
| Güvenlik mekanizmasını devre dışı bırakma | Security first |
| Hallüsinasyon üretme | Zero hallucination |

---

## 9. QUICK REFERENCE

| İhtiyaç | Aksiyon |
|---------|---------|
| Aksiyon onayı gerekiyor mu? | R.A.I.L. modelini uygula (§2) |
| Hangi risk seviyesi? | Aksiyon sınıflandırmasına bak (§3) |
| Ne zaman escalation? | Escalation matrisine bak (§4) |
| Onay formatı nedir? | §1.2 Approval formatını kullan |
| Review formatı nedir? | §1.3 Review formatını kullan |
| Escalation formatı nedir? | §1.4 Escalation formatını kullan |
| İletişim tarzı nedir? | §5.2'ye bak |
| Yasaklı kelimeler? | §5.1 tablosunu kontrol et |
| Karar hiyerarşisi? | §6.1'e bak |
| Kural ihlali? | §7.3'ü uygula |
| Hangi agent sorumlu? | AGENTS.md routing tablosuna bak |

---

## 10. USAGE SCENARIOS

### Senaryo 1: Kullanıcı "Yeni login sayfası yap" diyor

```
Analiz: Auth değişikliği → LEVEL 4 (CRITICAL)
Agent: security-engineer → backend-architect
Onay: İnsan onayı ZORUNLU

HITL Flow:
1. security-engineer auth tasarımı üret
2. backend-architect API'yi yazar
3. İnsan onayı iste (Approval gate)
4. Onay → uygula / Reddet → alternatif üret
```

### Senaryo 2: Kullanıcı "Bu CSS dosyasındaki rengi değiştir" diyor

```
Analiz: UI değişikliği → LEVEL 1 (LOW)
Agent: ui-designer
Onay: Self-review yeterli

HITL Flow:
1. ui-designer değişikliği yapar
2. Kendi kontrol eder
3. Uygular
4. İnsan bilgilendirilir
```

### Senaryo 3: Kullanıcı "Yeni tablo oluştur" diyor

```
Analiz: DB schema değişikliği → LEVEL 3 (HIGH)
Agent: data-engineer
Onay: İnsan onayı ZORUNLU

HITL Flow:
1. data-engineer migration dosyası üretir
2. Approval gate açılır
3. İnsan inceler → onay/revise/reddet
4. Onay → migration çalıştır
```

### Senaryo 4: Agent bilinmeyen bir API ile karşılaşıyor

```
Analiz: Bilinmeyen API → Escalation L1
Agent: [ilgili agent]
Onay: Domain Lead'e yükselt

HITL Flow:
1. Agent: "Bu API'yi doğrulayamıyorum"
2. L1 escalation: 30s timeout
3. Cevap gelmezse → L2'ye yükselt
4. Cevap gelmezse → L4 (insan)
5. Fail-safe: VERIFICATION REQUIRED
```

### Senaryo 5: Kullanıcı "Production'a deploy et" diyor

```
Analiz: Production deployment → LEVEL 4 (CRITICAL)
Agent: devops-engineer
Onay: Full audit + İnsan onayı

HITL Flow:
1. devops-engineer deploy planı üretir
2. Impact analysis yapılır
3. Security review yapılır
4. İnsan onayı istenir ( Approval gate)
5. Onay → staged rollout
6. Monitoring aktif
```

---

## 11. VERIFICATION CHECKLIST

Her HITL kararından önce bu kontrol listesi tamamlanmalıdır:

- [ ] **Risk seviyesi** belirlendi (Level 0-4)
- [ ] **R.A.I.L. modeli** uygulandı
- [ ] **Kontrol noktası** seçildi (Approval/Review/Escalation/Interrupt)
- [ ] **Onay formatı** hazırlandı
- [ ] **Timeout** ayarlandı
- [ ] **Fail-safe** tanımlandı
- [ ] **Etki alanı** belirlendi
- [ ] **Alternatif çözümler** sunuldu (gerekirse)
- [ ] **Log kaydı** oluşturulacak
- [ ] **Cross-reference** güncellenecek (gerekirse)

---

## 12. VERSION HISTORY

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-07-30 | Initial release — basic human mode |
| 2.0.0 | 2026-08-01 | Added orchestration basics |
| 3.0.0 | 2026-08-03 | Added hallucination control integration |
| 4.0.0 | 2026-08-08 | Added web search mandate, communication standards |
| 4.1.0 | 2026-08-15 | Standardized frontmatter |
| **5.0.0** | **2026-08-15** | **Complete rewrite — HITL focus, R.A.I.L. model, 4 control points, 5 risk levels, escalation matrix, 11 agent coverage** |

---

*Human-in-the-Loop Control System v5.0.0 — CoreMusic*
*Authority: Vault Steward / AI Orchestrator*
*Mandatory for all agents — Human control preserved at critical decision points*
