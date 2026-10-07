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

## 2. Skill Klasör Yapısı (Canonical — v3.0)
```text
.claude/skills/{skill-adi}/          ← klasör adı = frontmatter name (1:1, lowercase-hyphen)
├── SKILL.md                    ← ZORUNLU — Ana orchestration dosyası (Katman 2)
├── references/                 ← Katman 3: Deep Knowledge 
│   ├── overview.md             ← (Bu dosya) Mimari bakış
│   ├── rules.md                ← Güvenlik, doğrulama ve format kuralları (v3.0 N1-N10)
│   ├── anti-patterns.md        ← Yasaklı kalıplar
│   ├── checklist.md            ← Kalite kontrol listesi
│   └── changelog.md            ← Sürüm/format geçmişi (eski frontmatter changelog[] burada)
├── examples/                   ← ZORUNLU (v3.0): ≥1 çalışmış örnek — girdi → çıktı döngüsü
│   └── …                       ← HER examples/*.md SKILL.md'den linkli olmalı (orphan = hata)
├── scripts/                    ← Otonom yürütülecek scriptler (opsiyonel)
└── templates/                  ← Kod üretim şablonları (opsiyonel)

NOT: Hiçbir katmanda CLAUDE.md sidecar YASAK (v3.0 N8); MAX THINKING bloğu kopyalanmaz (N7).
```

## 3. Otonom Yaşam Döngüsü (Agentic Lifecycle)
1. **Trigger:** Kullanıcı tetikler.
2. **Analysis:** AI, webden domain-spesifik arama yapar (Zorunlu Web Research).
3. **Validation:** Kaynaklar 2-3 kez doğrulanır (Truth Mode). Yanlış/eski bilgi reddedilir.
4. **Generation:** `SKILL.md` ve ilgili yapı 2000 satır kuralına sadık kalarak oluşturulur.
5. **Report:** Kullanıcıya "Zero Hallucination" ve "Agentic Readiness" raporu sunulur.
