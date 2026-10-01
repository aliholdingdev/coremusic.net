---
name: orchestration
description: "Use when coordinating multi-agent workflows, dispatching tasks to specialist agents, or running checkpoint loops across the CoreMusic team."
title: "CoreMusic — Orchestration Engine"
type: skill-instruction
version: 1.1
updated: 2026-09-29
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Task Analysis & Agent Routing
  - Multi-Agent Coordination
  - Handover Management
  - HITL Gate System
  - Risk Classification
  - Skill Creation
triggers:
  - "görev ata"
  - "agent seç"
  - "hangi agent"
  - "skill oluştur"
  - "yeni skill"
  - "onay"
  - "approval"
  - "HITL"
  - "dur"
  - "bekle"
  - "insan onayı"
  - "escalation"
  - "yükselt"
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
changelog:
  - version: 1.1
    date: 2026-09-29
    changes:
      - Faz2 içerik kalite denetimi — ölü referans YOK bulundu; "Port 81 = music.coremusic.net | ADR-042" atfı `.ai/keys.md` L201+L277 ile, "SELECT * | H021" H021 kaydı ile DOĞRULANDI
  - version: 1.0
    date: 2026-09-20
    changes:
      - Birleştirilmiş: agent-orchestrator + skill-maker + human-mode + prompt-maker
      - Gereksiz içerikler kaldırıldı (SQL referans, usage scenarios, checklist'ler)
---

# ORCHESTRATION ENGINE — CoreMusic

## MAX THINKING — Anti-Overthink (Kısa — 2026-10-01)

1. Reasoning = LOW; uzun analiz paragrafı, plan kompozisyonu, promptu geri anlatma YASAK.
2. Her dosya 1 kez okunur; ilk okumadan sonra KARAR VER — aynı veriyi ikinci kez analiz etme.
3. Arka arkaya 3 başarısız düzeltme → DUR, şüpheli varsayımı söyle, 1 kısa soru sor.
4. Bilinmeyen = UNKNOWN; gereksiz dosya/skill/agent/context/plan üretme.
5. Görev → aksiyon → sonuç (nokta atışı); aynı hatayı tekrarlama. Tam metin (7 madde): `@.ai/AGENTS.md` § MAX THINKING.



## 1. Kimlik

Bu skill, CoreMusic ekosistemindeki tüm AI ajanlarını **koordine eder, görevleri dağıtır, insan müdahalesini yönetir ve yeni skill'ler üretir.**

Tek soru: **"Bu görevi kim yapar, ne zaman dur, nasıl üretirsin?"**

---

## 2. Agent Routing (11 Agent)

| # | Agent | Domain | Keyword |
|---|-------|--------|---------|
| 1 | `mo` | Koordinasyon | vault, doc, log |
| 2 | `backend-architect` | PHP 8.4 API | API, PHP, controller, middleware |
| 3 | `ui-designer` | Vanilla JS, CSS | CSS, UI, responsive, frontend |
| 4 | `security-engineer` | OWASP, Auth | CSRF, CSP, XSS, auth, security |
| 5 | `data-engineer` | MySQL BCNF | database, SQL, migration, schema |
| 6 | `embedded-engineer` | C++20, ASIO | C++, audio, DSP, JUCE |
| 7 | `qa-engineer` | Test | test, coverage, PHPUnit, Vitest |
| 8 | `devops-engineer` | CI/CD | Docker, deploy, pipeline |
| 9 | `audio-hardware-engineer` | PCB, DAC | hardware, PCB, amplifier |
| 10 | `dsp-firmware-engineer` | XMOS, I2S | firmware, XMOS, register |
| 11 | `windows-software-engineer` | WASAPI | WASAPI, COM, driver |

### Routing Akışı

```
GELİŞ GİRİŞİ
    ↓
[1] KEYWORD ÇIKAR → routing tablosuna bak
    ↓
[2] RİSK BELİRLE → Low/Medium/High/Critical
    ↓
[3] AGENT SEÇ → birincil + ikincil
    ↓
[4] BAĞLAM YÜKLE → CLAUDE.md, AGENTS.md, ilgili ADR
    ↓
[5] GÖNDER → agent'a ata
    ↓
[6] DOĞRULA → çıktı kurallara uygun mu?
    ↓
[7] TAMAMLA → log.md'ye yaz
```

---

## 3. Risk Sınıflandırması (5 Seviye)

| Seviye | Tanım | Onay | Örnek |
|--------|-------|------|-------|
| **L0 — AUTO** | Güvenli, geri dönüşü kolay | Yok | Dosya okuma, grep, search |
| **L1 — LOW** | Minimal etki | Self-review | README, ADR, typo fix |
| **L2 — MEDIUM** | Kısmi etki | Agent review | Yeni dosya, endpoint, component |
| **L3 — HIGH** | Geniş etki | **İnsan onayı** | Auth, DB schema, security, deploy |
| **L4 — CRITICAL** | Geri dönüşü olmayan | **Tam denetim** | Production data, encryption |

### R.A.I.L. Karar Matrisi

| Geri Dönüş | Belirsizlik | Etki | Gecikme | → Kontrol Noktası |
|------------|-------------|------|---------|-------------------|
| Kolay | Düşük | Düşük | Yüksek | Otomatik |
| Zor | Düşük | Yüksek | Düşük | **Approval** |
| Kolay | Yüksek | Orta | Orta | **Review** |
| Zor | Yüksek | Yüksek | Düşük | **Approval + Escalation** |

---

## 4. HITL Kontrol Noktaları (4 Nokta)

| # | Nokta | Ne Zaman | Fail-Safe |
|---|-------|----------|-----------|
| 1 | **Approval** | Geri dönüşü olmayan işlem öncesi | timeout → DENY |
| 2 | **Review** | Çıktı kalite kontrolü | timeout → auto-approve |
| 3 | **Escalation** | Karar verilemiyor | timeout → VERIFICATION REQUIRED |
| 4 | **Interrupt** | Aktif duraklama | timeout → continue + warning |

### Onay Formatı

```markdown
## HITL ONAY İSTEĞİ
**Agent:** [agent-id]
**Risk:** HIGH / CRITICAL
**İşlem:** [Ne yapılacak]
**Etki:** [Etkilenen dosyalar/servisler]
**Seçenekler:**
- [OK] ONAYLA — Uygula
- ✏️ REVİZE ET — [Değişiklik ile onayla]
- [X] REDDET — İptal
```

---

## 5. Escalation Matrisi

| Seviye | Tanım | Timeout | Fail-Safe |
|--------|-------|---------|-----------|
| L1 | Domain Lead | 30s | VERIFICATION REQUIRED |
| L2 | Tech Lead | 60s | ESCALATION REPORT |
| L3 | Arch Lead | 120s | FULL AUDIT |
| L4 | İnsan | 300s | ACTION DENIED |

---

## 6. Skill Oluşturma Protokolü

Yeni skill oluşturulurken:

```
[1] GEREKSİNİM → ne yapacak, hangi domain
[2] ARAŞTIRMA → web'den doğrula (min 2 kaynak)
[3] YAPI → max 400 satır, sadece temel kurallar
[4] GÜVENLİK → OWASP, credential yok, hardcoded secret yok
[5] DOĞRULA → trigger'lar çalışıyor mu?
```

### Skill Yapısı (Zorunlu)

```yaml
---
title: "CoreMusic — [Skill Adı]"
type: skill-instruction
version: X.0
authority: SSOT
triggers: [...]
reference:
  authority: ".ai/CLAUDE.md"
---
# [Skill Adı]
## 1. Kimlik
## 2. Aktivasyon
## 3. Çalışma Akışı
## 4. Kurallar
## 5. Yasaklar
```

### Yasaklar

- SQL komut referansı yazma (MySQL docs'tan okunur)
- CSS/HTML kod örneği yazma (vault'ta)
- Agent-specific checklist yazma (her agent kendi domain'ini bilir)
- Anti-pattern katalogu yazma (H001-H039'da zaten var)
- 1000+ satır skill yazma

---

## 7. CoreMusic Hard Kurallar

| Kural | Kaynak |
|-------|--------|
| PHP 8.4 strict_types=1 | ADR |
| Vanilla JS (framework yasak) | ADR-001 |
| PDO only (ORM yasak) | ADR-002 |
| csrf_token (NOT _csrf_token) | ADR-010 |
| Port 81 = music.coremusic.net | ADR-042 |
| Middleware sırası değişmez | ADR-010/011/012 |
| SELECT * yasak | H021 |
| innerHTML yasak | XSS riski |
| var yasak (let/const) | ES6+ |
| Zero Code Before Plan | Guardrail #1 |

---

## 8. Quick Reference

| İhtiyaç | Aksiyon |
|---------|---------|
| Görev dağıt | §2 routing tablosu |
| Onay gerekli mi? | §3 R.A.I.L. matrisi |
| Ne zaman escalation? | §5 escalation matrisi |
| Yeni skill oluştur | §6 protokolü |
| Yasaklar neler? | §7 hard kurallar |

---

*Orchestration Engine v1.1 — CoreMusic*
*Authority: Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*

## SWARM AKIŞ DİYAGRAMI (2026-10-01)

```text
USER → ORCHESTRATOR → ARCHITECT | DEVELOPER | RESEARCHER
                          ↓ (paralel çıktilar)
                      REVIEWER → SECURITY → TEST → VERIFY
                                                            ↓
                                              USER (rapor) ←──┘
```

- Akış: `ORCHESTRATOR → ARCHITECT | DEVELOPER | RESEARCHER → REVIEWER → SECURITY → TEST → VERIFY → USER (rapor)`.
- **VERIFY başarısızsa** görev, çıktısıyla birlikte **ORCHESTRATOR üzerinden** ilgili role (ARCHITECT/DEVELOPER/RESEARCHER) geri döner; döngü VERIFY pass edene kadar sürer.
- Durum etiketleri: kanıtlanmamış fazlar **PLANNED** olarak etiketlenir — kanıtsız "IMPLEMENTED" yazılmaz.

## MAX THINKING — Anti-Overthink (2026-10-01)
- Reasoning = LOW. Bu skill yüklendiğinde uzun analiz, promptu geri anlatma, plan kompozisyonu YASAK.
- Nokta atışı: gorev -> aksiyon -> sonuc. Ayni dosya/veri 2. kez okunmaz; ilk okumadan sonra KARAR VER.
- Skill yalniz ihtiyac aninda yuklenir; boot'ta toplu skill yukleme YASAK (kural: koku AGENTS.md, on-demand vault).
- 3 basarisiz duzeltme -> DUR, supheli varsayimi soyle, 1 kisa soru sor.
- Bilinmeyen = UNKNOWN. Gereksiz dosya/klasor/skill/agent/context/plan uretme.
- Cikti: ne degisti -> hangi dosya -> sonraki adim. Maks 5 madde.
