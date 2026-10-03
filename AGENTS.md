---
title: "CoreMusic — Kök Ana Kurallar"
type: rules
category: agent-registry
version: 1.0.0
status: active
authority: "Master rules — SSOT detayları: .ai/"
updated: 2026-10-01
---

# CoreMusic — AGENTS.md (Kök Master Kurallar)

> OpenCode V2 bu dosyayı kök boot dosyası olarak okur (CLAUDE.md V2 tarafından yok sayılır).
> Uzun referanslar `.ai/` vault'tadır ve **yalnızca ihtiyaç anında** `@` ile okunur.
> "Başta tüm vault'u oku" YASAKTIR.

---

## 1. İş Akışı (Flow)

```
USER → KNOWLEDGE MODEL → ASKING QUESTIONS → .AI SECOND BRAIN → ASKING QUESTIONS
→ AGENT SYSTEM INSTRUCTIONS → DISCOVERY/ANALYSIS/VALIDATION
  (FILE INDEX / CODE INDEX / EVIDENCE)
→ REQUIREMENT ENGINE → ASKING QUESTIONS → ARCHITECTURE → ASKING QUESTIONS
→ APPROVAL → IMPLEMENTATION
```

Her "ASKING QUESTIONS" kapısı onaysız sonraki aşamaya geçmez.

## 2. Otonom Yaşam Döngüsü

```
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure
→ Verify → Document
```

⚠️ VERIFICATION REQUIRED: görev tanımı bu zinciri "13-step" diye adlandırır;
zincirde 15 adım vardır (Verify iki kez) — sayı uydurulmaz.

## 3. ZERO-HALLUCINATION POLICY

1. Repo, URL, API, class, method, dependency, version, config, skill, agent,
   dosya veya mimari kural **ASLA uydurulmaz**.
2. Bilinmeyen değer = `UNKNOWN` · doğrulanamayan iddia = `⚠️ VERIFICATION REQUIRED`.
3. Her iddia disk kanıtı olmadan yazılmaz (gerçek dosya yolu + içerik).
4. Model hafızası ve web, vault'un altındadır.

## 4. Birincil Mühendislik Kuralı (Kod Öncesi)

Kod yazmadan önce repo'yu incele: dizin yapısı, mimari katmanlar, bağımlılıklar,
pattern'ler, build/test/config/logging/security/db/api/frontend konvansiyonları,
duplicate abstraction'lar, teknik borç.

**Faz 1 (keşif fazı) — onaysız YOK:**
kod değişikliği · yeni dosya · yeni klasör · yeni bağımlılık · büyük yapısal değişiklik.

## 5. Anti-Overthink / Anti-Waste (MAX THINKING)

1. Aynı dosya bir görevde 2. kez okunmaz — ilk okumadan sonra KARAR VER.
2. İhtiyaç yoksa vault/skill/context yüklenmez.
3. Nokta-atışı talimatta plan kompozisyonu ve agent spawn YOK — doğrudan uygula.
4. Talimat açıksa mikro-soru sorulmaz.
5. Art arda 3 başarısız düzeltme → DUR, şüpheli varsayımı söyle, 1 kısa soru sor.
6. Reasoning = LOW; uzun analiz paragrafı, promptu geri anlatma, tekrarlı doğrulama yasak.
7. Gereksiz dosya/klasör/skill/agent/context/plan üretme.

## 6. Multi-Agent Swarm

```
                          ┌───────────────────────┐
   USER ────────────────→ │   ORCHESTRATOR (MO)   │
                          └───────────┬───────────┘
                                      │
                 ┬────────────────────┼────────────────────┬
                 │                    │                    │
          ┌──────┬──────┐      ┌──────┬──────┐      ┌──────┬──────┐
          │  ARCHITECT  │      │  DEVELOPER  │      │  RESEARCHER │
          └──────┴──────┘      └──────┴──────┘      └──────┴──────┘
                 └────────────────────┼────────────────────┘
                                      │
                               ┌──────┬──────┐
                               │  REVIEWER   │
                               └──────┴──────┘
                                      │
                               ┌──────┬──────┐
                               │  SECURITY   │
                               └──────┴──────┘
                                      │
                               ┌──────┬──────┐
                               │    TEST     │
                               └──────┴──────┘
                                      │
                               ┌──────┬──────┐         ┌───────┐
                               │   VERIFY    │───────→ │  USER │
                               └──────┴──────┘  rapor  └───────┘
```

Her aşama bir öncekinin çıktısını doğrular; VERIFY başarısızsa ORCHESTRATOR
görevi ilgili role geri döndürür.

## 7. Execution Loop (her görevde)

1. Kök `AGENTS.md` (bu dosya) okunur.
2. **Yalnız ilgili** `.ai/.rules/*` okunur (tümü değil).
3. Hedef dosya okunur.
4. Kod yazılır.
5. `.ai/.rules/` kontrolleri çalıştırılır.
6. Hata → `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol.
7. UI değişikliği → browser testi (gerçek sayfa, eleman/layout doğrulaması).
8. Commit (subagent ATMAZ — orkestratöre aittir).

## 8. Prompt-maker Kuralı

Kullanıcı hammadde (raw) prompt gönderdiğinde **İLK** işlem `/prompt-maker`'dır:
questions modu çalışır; sağlayıcı/sabit model yok — OpenCode'un o an seçtiği model
kullanılır (free modellerde de çalışır).

Şablon çıktısı (sırasıyla):

1. eli10 paragraf açıklama
2. İstenen şey (ne isteniyor)
3. Vault referansları
4. Proje kod referansları
5. Kullanıcı kararları / cevapları
6. Orijinal prompt
7. Görev dökümü (task breakdown)
8. `✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: [n]`

Çıktı onaylanır → **sonra** session başlatılır.

## 9. On-Demand Vault Referansları (Ağır Detaylar)

| İhtiyaç | Dosya (`@` ile okunur) |
|---|---|
| Anayasa, guardrails, yasaklar | `@.ai/CLAUDE.md` |
| ADR kararları, mühendislik kısıtları | `@.ai/brain.md` |
| Kural dosyaları, error-recovery | `@.ai/.rules/*` |
| Agent registry, routing, handover | `@.ai/AGENTS.md` |
| Süreç, faz, hard gate | `@.ai/WORKFLOW.md` |

**Kural:** bu dosyalar yalnız görevin gerektirdiği anda okunur; boot'ta toplu
okuma, "tüm vault'u oku", "önce tüm .ai/ oku" talimatları geçersizdir.
Çelişkide `.ai/` kazanır (SSOT).

---

## 10. CSS Writing Rules (Templates)

Her CSS görevinden önce `.ai/.templates/frontend/` altındaki kanonik şablonlar okunur: `css-template.md` (v3.0.0, 11 katman) · `css-abstracts-token-template.md` · `css-component-template.md` · `css-page-template.md` · `css-device-template.md` · `css-auth-device-template.md` · `css-utility-template.md` · `css-helper-template.md`; ilgili/detay özet (kök): `.ai/.templates/` altındaki `css-structure.md` (01→11 klasör sırası) · `css-token.md` (token + breakpoint) · `css-component.md` · `css-page.md` · `css-imports.md` (import zinciri).
Sıra kuralı: 01_Abstracts(token) → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth.
Token kuralı: sabit değer yalnız `01_Abstracts/a-*.css` içinde `--cm-*` ile; `@media` içinde `var()` ile okuma yasak; ham hex/px katman dosyasına yazılmaz.
**Taşıma/silme YOK:** mevcut CSS dosyası adlandırması ve import zinciri değiştirilmez; yanlış yerleşim yalnız raporlanır (onay + ADR olmadan taşıma yasak).

---
