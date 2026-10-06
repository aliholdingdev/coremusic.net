# CoreMusic — CLAUDE.md (Pointer)

> **Kanonik kök dosya: [`AGENTS.md`](AGENTS.md)** — tüm ana kurallar (akış, zero-hallucination,
> anti-overthink, execution loop, prompt-maker) oradadır; bu dosya V2 tarafından yok sayılır.
> Vault (`.ai/` — anayasa, ADR, kurallar) **yalnız ihtiyaç anında** `@` ile okunur:
> `@.ai/CLAUDE.md` · `@.ai/brain.md` · `@.ai/.rules/*` — boot'ta toplu okuma yok.

## Master Engineering System (Özet — 2026-10-01)

**Yaşam döngüsü (15 aşama):**

```text
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document
```

Kural: **analiz tamamlanmadan implementation başlatılmaz.** Kod yazmadan önce repo incele: dosya yapısı, mimari katmanlar, dependency graph, pattern'ler, build/test/config/logging/security/database/API/frontend yüzeyleri, conventions, duplicate implementation, teknik borç (16 madde).

**YASAK:** dosya/klasör keyfi açma · mevcut dosyayı incelemeden yeniden yazma · mimariyi bozma · duplicate abstraction · doğrulanmamış API · olmayan class/dependency/dosya varsayma · test/build çalıştırmadan "tamam" deme.

**Zero-Hallucination:** repository, URL, API, class, method, property, package, dependency, version, license, configuration, MCP, skill, agent, framework behavior, file, directory, architecture rule, benchmark, security claim **ASLA uydurulmaz** → doğrulanamayan = `⚠️ VERIFICATION REQUIRED` · bilinmeyen = `UNKNOWN`.

**Final Rule (otonom akış):**

```text
Understand → Research → Resolve → Decide → Implement → Track → Verify
```

Görev kullanıcıyı mikro-yönetmek değildir: riskli işlemede dur · yanlış dosyaya gitme · gereksiz dosya/skill/agent/context/plan üretme · aynı hatayı tekrarlama · mevcut repository'nin gerçek yapısı esas · kullanıcı onayı olmadan büyük source-code değişikliği yok.

**MAX THINKING — Anti-Overthink (7 madde):**

1. Varsayılan reasoning = LOW (opencode.json: reasoningEffort "low"). "high/deep" SADECE kullanıcı açıkça isterse.
2. Uzun analiz paragrafı, promptu geri anlatma, plan kompozisyonu YASAK. Nokta atışı cevap → hemen uygula.
3. Her dosya 1 kez okunur; ilk okumadan sonra KARAR VER. Aynı veriyi "emin olmak" için ikinci kez analiz etme.
4. Session başlangıcı = anında boot (okuma listesi) → sonra işlem. Keşif önsözü yok.
5. Arka arkaya 3 başarısız düzeltme → DUR, şüpheli varsayımı söyle, 1 kısa soru sor.
6. Bilinmeyen = UNKNOWN; tahmin yok. Gereksiz dosya/klasör/skill/agent/context/plan üretimi yasak.
7. Output kısa ve aksiyon odaklı: ne değişti → hangi dosya → sonraki adım. Maks 5 madde.

*SSOT detayları: [[AGENTS.md]] §5 · süreç: [[.ai/WORKFLOW.md]] §8.9-§8.10 · tam metin bütçesi: [[.ai/ULTRA-THINKING.md]] § MAX THINKING*

**Mimari referans (2026-10-06 · v4.0.0):** Enterprise Layered Architecture **500 katman** (`K000`–`K499`),
10 domain, **hibrit multi-MD** (MD sayısı değişken, sabit değil). Giriş: `@.ai/architecture/00-master-index.md`
→ domain tabloları `10-domain-d01-…md` … `19-domain-d10-…md`. Sayım birimi = **KATMAN** (mantıksal doküman
katmanı); toplam yalnız betik çıktısıyla yazılır. Eski K0–K20 / 344 MD yapısı backup'tadır
(`_backup/arch-2026-10-06_1057.zip`, salt-okunur referans, esas değil). Amplifikatör topolojisi **Class AB**
(Class D yasak). Agent hiyerarşisi: **Expert 5 / Senior 5 / Junior 10** → [[AGENTS.md]] §6.1.

**CSS görevleri:** kod öncesi kanonik `.ai/.templates/frontend/` seti — `css-template.md` · `css-abstracts-token-template.md` · `css-component-template.md` · `css-page-template.md` · `css-device-template.md` · `css-auth-device-template.md` · `css-utility-template.md` · `css-helper-template.md` okunur (01→11 sıra · token yalnız 01 · taşıma yok) → [[AGENTS.md]] §10.
⚠️ **Ölü atıf düzeltildi (2026-10-06):** `+ özet (kök): .ai/.templates/css-structure.md · css-token.md · css-component.md · css-page.md · css-imports.md` — beş dosya **çalışma ağacında diskte YOK (0 glob isabeti)** → referans kaldırıldı; özet içerik `css-template.md` içindedir (HEAD'de varlar, silme commit edilmedi — `git status: D`).

*SSOT: .ai/ · Pointer v2.1 — Last Updated: 2026-10-06*
