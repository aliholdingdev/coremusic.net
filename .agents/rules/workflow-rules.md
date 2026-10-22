---
title: "CoreMusic — Süreç & Kalite Kuralları (Workflow · QA · Commit)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/WORKFLOW.md · .ai/AGENTS.md §7-§12, §16 · kök AGENTS.md §7"
docType: workflow
---

# CoreMusic — Süreç & Kalite Kuralları (Process & Quality Rules)

**docType:** workflow · **Klasör:** `.agents/rules/` · **Sorumlu:** MO (süreç) + qa-engineer (kapı)

**Zorunlu Bağlantılar:** [[RULES]] · [[../../.ai/WORKFLOW]] · [[../../.ai/AGENTS]] · [[../../AGENTS]]

> **eli10:** İş akışı, kalite kapıları, öncelik/timeout, handover/escalasyon ve commit yetkisi bu dosyadadır.

---

## 1. Amaç

CoreMusic'te her görevin **kaçınılmaz süreç adımlarını, kalite kapılarını, öncelik/timeout değerlerini, handover-escalasyon ve commit kurallarını** vault SSOT'larından toplar. Q/A kalite hedefi: coverage ≥%80, flaky %0 (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Görev yaşam döngüsü, dispatch, kapılar | Dil/domain kuralları → ilgili `*-rules.md` |
| Priority/timeout/retry, health, context lock | Mimari kararlar → [[architecture-rules]] |
| Commit / log / vault-sync | Secret denetimi → [[security-rules]] |

---

## 3. Mimari

### 3.1 Yaşam Döngüsü (kök `AGENTS.md` §2 + `.ai/AGENTS.md` D1)

```
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document
```

**15 aşama; analiz tamamlanmadan implementation başlamaz.** Final (otonom) akış: `Understand → Research → Resolve → Decide → Implement → Track → Verify`.

**Her "ASKING QUESTIONS" kapısı onaysız sonraki aşamaya geçmez** (D1 — `.ai/AGENTS.md` §8.9).

### 3.2 Task Dispatch (`.ai/AGENTS.md` §7)

```
İstek → [1 Analiz (keyword→routing §6)] → [2 Pre-flight] → [3 Atama]
→ [4 Execution] → [5 Handover] → [6 Validation] → [7 Completion + log]
```

**Pre-flight kapıları:** domain boundary · context lock · handover ihtiyacı · **mockup uyumu** (okunmadıysa DUR) · 45-tier uyumu · responsive uyum · **şablon zorunluluğu** (Guardrail #16) · gate script sonucu (repo **kökünden**) · envanter iddiası ↔ disk ölçümü · önceki görev başarısızlığı.

### 3.3 Öncelik / Timeout / Retry (`.ai/AGENTS.md` §8)

| Öncelik | Tanım | Timeout | Max Retry |
|---------|-------|---------|-----------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 |
| HIGH | Kritik işlev kaybı | 15s | 3 |
| MEDIUM | Normal geliştirme | 30s | 3 |
| LOW | İyileştirme | 60s | 2 |

**Health states:** Healthy 200 · Degraded 301 · Retry 408 · Failed 500 (queue reset) · Dead 503 (escalation). **Context Lock:** max 30s; deadlock'da MO en eski kilidi kırar; CRITICAL diğerini kırabilir.

### 3.4 Handover & Escalasyon (`.ai/AGENTS.md` §9-§10)

- **Handover mesajı:** Konu · Kaynak · Hedef · Öncelik · Etkilenen dosyalar · İstek · Onay durumu · Timestamp (UTC).
- **Kural:** hedef onayı zorunlu (30s timeout, max 3 retry; reddedilirse MO).
- **Escalasyon:** L1 Domain Lead → L2 Tech Lead → L3 Arch Lead → **İnsan**; timeout 30/60/120s, seviye başına max 3 retry.
- Senaryolar: güvenlik açığı (Backend→Security, CRITICAL) · DB schema (Backend→Data, HIGH) · CSRF/CSP uyumsuzluğu (15s) · coverage <%80 (QA→L2, 60s) · ADR çelişkisi (L2→L3, 60s).

### 3.5 Quality Gates (`.ai/AGENTS.md` §16 · `.ai/CLAUDE.md` §8, §17)

| Agent | Standart | Hedef |
|-------|----------|-------|
| Backend | strict_types, PSR-12, prepared | %100 |
| UI | ITCSS, BEM, WCAG 2.2 AA, C01-C16 | %100 |
| Security | OWASP Top 10, `csrf_token`, Argon2id | %100 |
| Data | BCNF, no ORM, no SELECT *, prepared | %100 |
| Embedded | Zero-allocation, lock-free, noexcept | %100 |
| QA | Coverage ≥%80, flaky %0 | ≥80% |
| DevOps | CI success ≥%95, GitLeaks clean | ≥95% |

**Soft Constraints (esneme yalnız onayla):** %75 coverage geçici (Tech Lead) · batch 60s'e kadar (Tech Lead) · BypassAuth test ortamında (Security).

### 3.6 Commit & Kapanış

| Kural | Değer |
|-------|-------|
| **Commit** | **Subagent ATILMAZ** — orkestratöre ait (`.ai/AGENTS.md` §13.9 #5) |
| log.md | **Append-only** — geçmiş satıra dokunulmaz (Domain Boundaries) |
| Kapanış | `log.md` girişi + MEMORY session state + gerekirse vault-sync (`.workflows/vault-sync.md`) |
| Session Checklist | Baş: boot + git status/log-10 + TODO P0 + hedef cümlesi · Orta: yarı iş/guardrail/mockup taraması · Kapanış: kanıtla doğrulama + TODO + sonraki adım (`.ai/CLAUDE.md` §16A) |
| 3 başarısız düzeltme | **DUR** + tek kısa soru (kör döngü yasak — kök `AGENTS.md`) |

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | Analiz bitmeden implementation yok | İş geri alınır |
| 2 | Pre-flight kapıları atlanamaz (mockup/şablon/lock) | DUR |
| 3 | Yetkisiz commit | Revert + escalation |
| 4 | log.md'ye geçmişe dokunma | Append-only ihlali → CRITICAL |
| 5 | Kapı QA'yı atlamak (yapan=denetleyen) | Geçersiz teslim |
| 6 | Kanıtsız "bitti" | Zero-Hallucination ihlali |

---

## 5. Workflow

```text
Her görev:
  Session Checklist §A (boot · git status · TODO P0 · hedef cümlesi)
  → Routing (§6 keyword tablosu)
  → Pre-flight (§3.2 kapıları)
  → Execution (domain boundary içinde)
  → Öz-denetim + QA kapısı (§3.5)
  → Handover (gerekirse §3.4)
  → Completion: log.md append → COMMIT (yalnız orkestratör)
  → Session Checklist §C (kanıt · TODO · sonraki adım) + vault-sync
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType: workflow | Mevcut |
| 2 | Yaşam döngüsü 15 aşama | §3.1 |
| 3 | Öncelik tablosu 4 satır + timeout/retry | §3.3 vault §18 ile eşit |
| 4 | Commit kuralı "ATILMAZ" | §3.6 + §4 #3 (2 yer) |
| 5 | Soft constraint onay sahipleri | §3.5 |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Süreç SSOT | [[../../.ai/WORKFLOW]] | Faz kapıları |
| Registry §7-§12 | [[../../.ai/AGENTS]] | Dispatch/lock/health/handover/escalation |
| Quality §16 | [[../../.ai/AGENTS]] | Agent standartları |
| Boot §16/§16A | [[../../.ai/CLAUDE]] | Session checklist |
| Master loop | [[../../AGENTS]] §7 | Execution loop |
| Vault sync | [[../workflows/vault-sync]] | Kapanış akışı |
| Master | [[RULES]] §4.5-§4.7 | Özet |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
