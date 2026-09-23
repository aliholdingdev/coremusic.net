# 🌐 skill-maker — Mimari & Agentic Orchestration (Overview)

## 1. Agentic Progressive Disclosure
Kiro IDE skill'leri, otonom agent'ların belleğini (context window) korumak için 3 katmanlı yükleme sistemi kullanır:

### Katman 1 — Discovery (Kesif)
Sadece `name` + `description` yüklenir. AI, kullanıcının isteğiyle skill'in yeteneklerini eşleştirir. Otonom karar burada başlar.

### Katman 2 — Activation & Orchestration
`SKILL.md` tam yüklenir. Bu dosya bir "kod" değil, bir "Agentic Orchestration" dosyasıdır. Zorunlu protokoller, Truth Mode kuralları ve web research adımları burada deklare edilir. AI bu kuralların dışına çıkamaz.

### Katman 3 — Execution & Deep Knowledge
`references/` ve `scripts/` sadece AI derin bağlama ihtiyaç duyduğunda yüklenir (örneğin spesifik domain kuralları). Bu katman, "Zero Hallucination" ilkesini destekler.

---

## 2. Skill Klasör Yapısı (Canonical)
```text
.claude/skills/{skill-adi}/
├── SKILL.md                    ← ZORUNLU — Ana orchestration dosyası
├── references/                 ← Katman 3: Deep Knowledge 
│   ├── overview.md             ← (Bu dosya) Mimari bakış
│   └── rules.md                ← Güvenlik, doğrulama ve format kuralları
├── scripts/                    ← Otonom yürütülecek scriptler (opsiyonel)
└── templates/                  ← Kod üretim şablonları (opsiyonel)
```

## 3. Otonom Yaşam Döngüsü (Agentic Lifecycle)
1. **Trigger:** Kullanıcı tetikler.
2. **Analysis:** AI, webden domain-spesifik arama yapar (Zorunlu Web Research).
3. **Validation:** Kaynaklar 2-3 kez doğrulanır (Truth Mode). Yanlış/eski bilgi reddedilir.
4. **Generation:** `SKILL.md` ve ilgili yapı 2000 satır kuralına sadık kalarak oluşturulur.
5. **Report:** Kullanıcıya "Zero Hallucination" ve "Agentic Readiness" raporu sunulur.
