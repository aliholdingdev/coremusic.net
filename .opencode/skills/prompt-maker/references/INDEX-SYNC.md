# /REFERENCES/ SENKRONIZASYON INDEKSI
# Prompt Maker v8.0.0 — 2026-06-11
# 27 Dosya × 10 Soru Bloku (A-J) Haritası

---

## 📋 SENKRONIZASYON TABLOSU

| # | Reference Dosya | Blok(lar) | İlişkili Sorular | Status |
|----|-----------------|----------|------------------|--------|
| 01 | `01-prompt-types-deep.md` | I | I01-I20 (Prompt tipi) | ⚠️ UPDATE NEEDED |
| 02 | `02-question-bank.md` | A-J | Tüm bloklar (index) | ✅ SYNCED |
| 03 | `03-security-owasp-full.md` | D | D01-D30 (OWASP, auth, crypto) | ✅ SYNCED |
| 04 | `04-language-standards-full.md` | B | B01-B10 (Dil, framework) | ⚠️ UPDATE NEEDED |
| 05 | `05-output-templates.md` | I | I20-I30 (Çıktı formatı) | ⚠️ UPDATE NEEDED |
| 06 | `06-deep-domain-rules.md` | H | H1-H5 (Domain-spesifik) | ⚠️ UPDATE NEEDED |
| 07 | `07-architecture-patterns.md` | C | C01-C30 (Mimari, API) | ⚠️ UPDATE NEEDED |
| 08 | `08-full-example-sessions.md` | I,J | I01-I30, J01-J30 | ⚠️ UPDATE NEEDED |
| 09 | `09-quality-scoring-rubric.md` | — | Quality Score (transversal) | ✅ READY |
| 10 | `10-web-research-protocol.md` | — | Web Research (transversal) | ✅ READY |
| 11 | `11-coremusic-deep-rules.md` | A,B,C,D | CoreMusic-spesifik | ✅ READY |
| 12 | `12-performance-testing-devops.md` | E,G | E01-E30, G01-G40 | ✅ SYNCED |
| 13 | `13-uiux-accessibility.md` | F | F01-F30 (UI/UX, WCAG) | ⚠️ UPDATE NEEDED |
| 14 | `14-embedded-audio-electronics.md` | H | H1-H2 (Elektronik, Audio) | ⚠️ UPDATE NEEDED |
| 15 | `15-api-design-patterns.md` | C | C01-C20 (API, REST, GraphQL) | ⚠️ UPDATE NEEDED |
| 16 | `16-database-design-patterns.md` | B,C | B03-B04, C01-C10 | ⚠️ UPDATE NEEDED |
| 17 | `17-prompt-engineering-deep.md` | I | I01-I30 (Prompt engineering) | ⚠️ UPDATE NEEDED |
| 18 | `18-security-deep-dive.md` | D | D01-D30 (Security detay) | ⚠️ UPDATE NEEDED |
| 19 | `19-master-prompt-full-example.md` | — | MASTER PROMPT output | ✅ READY |
| 20 | `20-kiro-hooks-steering-deep.md` | I | I01-I10 (Kiro steering) | ✅ READY |
| 21 | `21-glossary-and-references.md` | — | Tüm bloklar (terim sözlüğü) | ⚠️ UPDATE NEEDED |
| 22 | `22-nodejs-typescript-patterns.md` | B | B01-B02 (Node.js, TS) | ⚠️ UPDATE NEEDED |
| 23 | `23-csharp-dotnet-patterns.md` | B | B01-B02 (C#, .NET) | ⚠️ UPDATE NEEDED |
| 24 | `24-ml-ai-patterns.md` | H | H3 (ML/AI, models) | ⚠️ UPDATE NEEDED |
| 25 | `25-fintech-payment-patterns.md` | H | H4 (Fintech, payment) | ⚠️ UPDATE NEEDED |
| — | `multi-agent-patterns.md` | — | Multi-agent orchestration | ✅ READY |
| — | `validation-engine.md` | — | Input/output validation | ✅ READY |

---

## 🔄 SYNC YAPILACAK DOSYALAR (16)

### PRIORITY 1 (CRITICAL) — 5 DOSYA
```
1. 04-language-standards-full.md
   → Bağla: questions-block-B-tech-stack.md (B01-B10)
   → Ekle: Her dil için (PHP, JS, Python, C#, C++) detaylı standartlar
   
2. 13-uiux-accessibility.md
   → Bağla: questions-block-f.md (F01-F30)
   → Ekle: WCAG 2.2 AA checklistleri, dark mode, PWA spec
   
3. 14-embedded-audio-electronics.md
   → Bağla: questions-block-h.md (H1-H2)
   → Ekle: H1 (elektronik, STM32, RTOS), H2 (audio, DSP, latency)
   
4. 18-security-deep-dive.md
   → Bağla: questions-block-d.md (D01-D30)
   → Ekle: Her OWASP maddesi için detaylı attack vectors
   
5. 24-fintech-payment-patterns.md
   → Bağla: questions-block-h.md (H4)
   → Ekle: PCI DSS, payment flows, compliance checklist
```

### PRIORITY 2 (HIGH) — 7 DOSYA
```
6. 01-prompt-types-deep.md
   → Bağla: questions-block-I-prompt-type.md (I01-I20)
   
7. 06-deep-domain-rules.md
   → Bağla: questions-block-h.md (H1-H5) tüm domain'ler
   
8. 07-architecture-patterns.md
   → Bağla: questions-block-c.md (C01-C30)
   
9. 15-api-design-patterns.md
   → Bağla: questions-block-c.md (C01-C20)
   
10. 22-nodejs-typescript-patterns.md
    → Bağla: questions-block-B-tech-stack.md (B01-B02)
    
11. 23-csharp-dotnet-patterns.md
    → Bağla: questions-block-B-tech-stack.md (B01-B02)
    
12. 25-fintech-payment-patterns.md
    → Bağla: questions-block-h.md (H4)
```

### PRIORITY 3 (MEDIUM) — 4 DOSYA
```
13. 05-output-templates.md
    → Bağla: questions-block-I-prompt-type.md (I20-I30)
    
14. 16-database-design-patterns.md
    → Bağla: questions-block-B-tech-stack.md (B03-B04)
    
15. 17-prompt-engineering-deep.md
    → Bağla: questions-block-I-prompt-type.md (I01-I30)
    
16. 21-glossary-and-references.md
    → Bağla: Tüm bloklar (A-J) — terim sözlüğü
```

---

## 📊 CROSS-REFERENCE SCHEMA

Her /references/ dosya başında:

```markdown
# [Başlık]

**Senkronizasyon:** [BLOK1, BLOK2, ...]
- Bağlantı: questions-block-[X].md (Q01-Q30 gibi)
- Güncellenme: 2026-06-11
- Soru sayısı: [N]+

---

## İlişkili Sorular (Dinamik)

**BLOK [X] İle Harita:**
- [X01](../questions-block-[X].md#x01) → Başlık
- [X02](../questions-block-[X].md#x02) → Başlık
- [X03](../questions-block-[X].md#x03) → Başlık
```

---

## ✅ COMPLETED

- ✅ 02-question-bank.md (master index)
- ✅ 03-security-owasp-full.md (D-block sync)
- ✅ 12-performance-testing-devops.md (E,G-block sync)
- ✅ INDEX.md (skill index güncellendi)

---

## ⏳ TODO

- [ ] Priority 1: 5 dosya sync (1 gün)
- [ ] Priority 2: 7 dosya sync (2 gün)
- [ ] Priority 3: 4 dosya sync (1 gün)
- [ ] brain.md + log.md append
- [ ] CHANGELOG.md güncellemesi

**Tahmini Total Süre:** 4 gün (paralel olursa 2 gün)

---

*Senkronizasyon Planı — Prompt Maker v8.0.0*
