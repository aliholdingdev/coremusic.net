---
title: "CoreMusic — Boot Instruction (Pointer)"
type: pointer
category: boot
version: 3.2.0
status: active
authority: "SSOT: .ai/CLAUDE.md (tam anayasa) · bu dosya §Skill Registry (Skill Registry SSOT)"
updated: 2026-10-10
---

# CoreMusic — CLAUDE.md (Boot Instruction Pointer)

> **Kanonik kök dosya: [`AGENTS.md`](AGENTS.md)** — tüm ana kurallar (akış, zero-hallucination,
> anti-overthink, execution loop, prompt-maker) oradadır; bu dosya V2 tarafından okunur.
> Vault (`.ai/` — anayasa, ADR, kurallar) **yalnız ihtiyaç anında** `@` ile okunur:
> `@.ai/CLAUDE.md` · `@.ai/AGENTS.md` · `@.ai/WORKFLOW.md` · `@.ai/CONTEXT.md` · `@.ai/RAG.md` — boot'ta toplu okuma yok.
> **Skill Registry SSOT: bu dosyanın §Skill Registry bölümüdür** — başka dosyada liste kopyalanmaz.

⚠️ **VERIFICATION REQUIRED**: `.ai/CLAUDE.md` v27.3.9 (2026-10-06, 34 bölüm, 991 satır) diskte MEVCUTTİR — bu dosyayı okumadan kod yazılmaz (Guardrail #2).

---

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

---

## Skill Kullanım Zorunluluğu (Skill Usage Mandate)

1. Her görev başlangıcında aşağıdaki Skill Registry + available-skills listesi
   taranır ve eşleşme KONTROL EDİLİR.
2. Görev bir skill'in "Use when..." tanımıyla eşleşiyorsa: İLK işlem Skill tool ile
   o skill'i yüklemektir. Eşleşme varken skill'siz işlem başlatmak YASAKTIR.
3. Birden fazla eşleşen varsa en dar (domain-specific) olan önce yüklenir.
4. İstisna: (a) kullanıcı açıkça "skill kullanma" derse; (b) görev skill'lerle
   ilgisizse (sohbet, aritmetik). Belirsizse 1 kısa soru sorulur.
5. Yetenek yoksa kopyala-yapıştır YAPILMAZ; skill-maker ile skill ÜRETİLİR
   (üretim kuralı: Guardrail #16 + v3.0 şeması).
6. Bu kural hook (.claude/settings.json → UserPromptSubmit) tarafından her prompt'ta
   hatırlatılır. Metnin kaynağı bu bölümdür (SSOT); hook yalnızca bu metnin
   kısa salt-sürümünü + proje skill isimlerini enjekte eder.

Çatışma: Anti-overthink "gereksiz skill" ibaresi = gereksiz skill ÜRETME yasağıdır;
eşleşen skill'i KULLANMAK bu mandate ile çelişmez, aksine zorunludur.

---

## Skill Registry (SSOT)

**Tek kayıt defteri bu bölümdür** — başka dosya (AGENTS/WORKFLOW/README) yalnız tek satırlık işaretçi taşır, liste kopyalamaz. Format otoritesi: `.claude/skills/skill-maker/` (şablon + kurallar). State sözlüğü: `upgraded` (v3.0'a yükseltildi) · `restored/merged` (geri yüklü/birleştirildi) · `new` (v3.0'da eklendi); global: `moved` · `merged` · `ported`.

| # | Skill | Scope | Path | Use when (1 satır, TR) | State |
|---|-------|-------|------|------------------------|-------|
| 1 | `prompt-maker` | project | `.claude/skills/prompt-maker/SKILL.md` | Ham prompt'u yapısal prompt'a çevirir | upgraded |
| 2 | `skill-maker` | project | `.claude/skills/skill-maker/SKILL.md` | Skill üretir / günceller (format otoritesi) | upgraded |
| 3 | `ui-analyzer` | project | `.claude/skills/ui-analyzer/SKILL.md` | UI denetimi / tasarım incelemesi | upgraded |
| 4 | `ui-code-generator` | project | `.claude/skills/ui-code-generator/SKILL.md` | Frontend (HTML/CSS/JS) kod üretimi | upgraded |
| 5 | `agent-orchestrator` | project | `.claude/skills/agent-orchestrator/SKILL.md` | Çok-ajan görev dağıtımı | restored/merged |
| 6 | `database-normalize-maker` | project | `.claude/skills/database-normalize-maker/SKILL.md` | BCNF / şema tasarımı | restored/merged |
| 7 | `composer-sync` | project | `.claude/skills/composer-sync/SKILL.md` | Composer / vendor senkronu | restored/merged |
| 8 | `vault-sync-post` | project | `.claude/skills/vault-sync-post/SKILL.md` | İşlem sonrası vault senkronu | restored/merged |
| 9 | `php-backend-standards` | project | `.claude/skills/php-backend-standards/SKILL.md` | PHP 8.4 / middleware / PDO | new |
| 10 | `audio-engine-cpp` | project | `.claude/skills/audio-engine-cpp/SKILL.md` | C++20 / JUCE / ASIO / DSP | new |
| 11 | `hardware-electronics` | project | `.claude/skills/hardware-electronics/SKILL.md` | Class AB / PSU / termal / PCB / BOM | new |
| 12 | `security-hardening` | project | `.claude/skills/security-hardening/SKILL.md` | Auth / CSRF / CSP / şifreleme | new |
| 13 | `learning-prompts` | project | `.claude/skills/learning-prompts/SKILL.md` | Sokratik / Feynman / aktif hatırlama | new (2026-10-10 disk kanıtıyla eklendi) |

✅ **DISK ÖLÇÜMÜ (2026-10-10):** `.claude/skills/` = **13 dizin** (yukarıdaki 13 satır birebir eşleşir).
⚠️ **DÜZELTİLDİ:** Eski kayıt "12 proje + 5 global = 17" idi — **5 global skill (`human-mode` · `truth-engine` · `verify-loop` · `agent-debate` · `context-report`) `C:\.claude\skills\` altında Diskte YOK** (Test-Path = False). `.opencode/skills/` de kapatılmıştır (Test-Path = False). Kayıt, disk gerçeğiyle hizalandı: **toplam 13 proje skill'i, 0 global skill**. Eski "moved/merged/ported" satırları geçersizleşti.

Proje dışı **~340 üçüncü-parti global skill** grubu (dotnet, dx-blazor, superpowers, claude-mem, chrome-devtools) harness **available-skills** listesiyle gelir; bu tabloya kopyalanmaz. Eşleşme varsa Skill tool ile yüklenir (§Skill Usage Mandate).

---

## MAX THINKING — Anti-Overthink (7 madde)

1. Varsayılan reasoning = LOW (opencode.json: reasoningEffort "low"). "high/deep" SADECE kullanıcı açıkça isterse.
2. Uzun analiz paragrafı, promptu geri anlatma, plan kompozisyonu YASAK. Nokta atışı cevap → hemen uygula; bu, eşleşen skill'i yüklemeyi engellemez.
3. Her dosya 1 kez okunur; ilk okumadan sonra KARAR VER. Aynı veriyi "emin olmak" için ikinci kez analiz etme.
4. Session başlangıcı = anında boot (okuma listesi) → sonra işlem. Keşif önsözü yok.
5. Arka arkaya 3 başarısız düzeltme → DUR, şüpheli varsayımı söyle, 1 kısa soru sor.
6. Bilinmeyen = UNKNOWN; tahmin yok. Görev manifestinde listelenmeyen dosya/klasör/agent/context/plan üretimi yasak; manifestteki dosyalar görevin kendisidir ve zorunludur. Gereksiz skill ÜRETME yasaktır (kullanma zorunlu — bkz. §Skill Usage Mandate).
7. Output kısa ve aksiyon odaklı: ne değişti → hangi dosya → sonraki adım. Maks 5 madde.

*SSOT detayları: [[AGENTS.md]] §5 · süreç: [[.ai/WORKFLOW.md]] §8.9-§8.10 · tam metin bütçesi: [[.ai/ULTRA-THINKING.md]] § MAX THINKING · FULL anayasa: [[.ai/CLAUDE.md]]*

---

## 🔗 Bağlantılar (Bu dosyanın ilişkileri)

| Bu Dosya (Root) | .ai/ SSOT | Açıklama |
|:---|:---|:---|
| `CLAUDE.md` (bu dosya) | `@.ai/CLAUDE.md` | Boot pointer — tam anayasası burada · **§Skill Registry (SSOT)** |
| `AGENTS.md` | `@.ai/AGENTS.md` | Master rules — tam agent registry burada |
| `WORKFLOW.md` | `.ai/WORKFLOW.md` | Workflow pointer — tam süreçler burada |
| `README.md` | `.ai/VISION.md` · `.ai/PROJECTS.md` | Giriş — vizyon ve proje tanımı |
| `CONTEXT.md` | `.ai/CONTEXT.md` | Vault klasör yapısı ve envanter (kök pointer — 2026-10-07) |
| `RAG.md` | `.ai/RAG.md` | Retrieval indeksi + pipeline (kök pointer — 2026-10-07) |
| — | `.ai/brain.md` | Mimari kararlar (ADR'ler) |
| — | `.ai/ROLE.md` | Rol → teknoloji eşlemesi |

> **Boot okuma sırası**: Bu dosya → [[AGENTS.md]] → [[README.md]] → [[WORKFLOW.md]] → ihtiyaç anında `@.ai/CLAUDE.md` + `@.ai/AGENTS.md` + `@.ai/WORKFLOW.md` + `@.ai/CONTEXT.md` + `@.ai/RAG.md`.

**Mimari referans (2026-10-10 · disk hizası):** Enterprise Layered Architecture **21 katman** (`K000`–`K020`),
6 alan etiketi A0–A5. Giriş: @.ai/architecture/00-master-index.md (v2.0.0, 2026-10-09) → katman tablosu §1 ·
topoloji §2 · durum §3 · makro eşleme §8. Bağımlılık kenarları: `.ai/architecture/katman-baglilik-matrisi.md`.
Plan metni: `.ai/architecture/coremusic-mimari-plani.md` (bağlayıcı değil, ADR'ye dönüşüm Vault Steward onayı ister).
Sayım birimi = **KATMAN** (mantıksal doküman katmanı); toplam yalnız betik çıktısıyla yazılır.

⚠️ **ÇELİŞKİ KAYDI (2026-10-10 — 3 iddia, hiçbiri hizalı değil):**
1. Eski kök not: "500 katman K000–K499, 10 domain, hibrit multi-MD" → **diskte `10-domain-d01-…md` … `19-domain-d10-…md` YOK** (0 glob isabeti). Bu iddia geçersizleşti.
2. ADR-096/097: "6.000 katman K000–K5999 · 5.041 dizin · 5.089 md" → **dizin yapısı diskte YOK** (architecture/ altında 21 K-dizini, 125 md). ADR metni frozen'dır, değiştirilmez; ancak **uygulama kanıtı taşımaz**.
3. `.ai/architecture/00-master-index.md` §1: **K000–K020 = 21 katman** → **bu, disk gerçeğidir** ve tek geçerli sayım budur.
Çözüm (Vault kuralı: disk kazanır): sayım = 21 katman. ADR-096/097 hedef model olarak kalır, "mevcut durum" sayılmaz. Amplifikatör topolojisi **Class AB** (Class D yasak). Agent hiyerarşisi: **Expert 5 / Senior 5 / Junior 10** → [[AGENTS.md]] §6.1.

✅ **GÜNCELLENDİ (2026-10-10):** `.ai/architecture/` diskte MEVCUT — `00-master-index.md` · `katman-baglilik-matrisi.md` · `coremusic-mimari-plani.md` + **21 K-dizini (K000–K020) + 125 md** (kök ölçüm 2026-10-10).
⚠️ **Kırık bağlantı:** `.ai/architecture/index.md` **diskte YOK** — README badge'i bu adrese gider; gerçek master `00-master-index.md`'dir (README §6 badge'i düzeltildi).

**CSS görevleri:** kod öncesi kanonik `.ai/.templates/frontend/` seti — `css-template.md` · `css-abstracts-token-template.md` · `css-component-template.md` · `css-page-template.md` · `css-device-template.md` · `css-auth-device-template.md` · `css-utility-template.md` · `css-helper-template.md` okunur (01→11 sıra · token yalnız 01 · taşıma yok) → [[AGENTS.md]] §10.

⚠️ **Ölü atıf düzeltildi (2026-10-06):** `+ özet (kök): .ai/.templates/css-structure.md · css-token.md · css-component.md · css-page.md · css-imports.md` — beş dosya **çalışma ağacında diskte YOK (0 glob isabeti)** → referans kaldırıldı; özet içerik `css-template.md` içindedir (HEAD'de varlar, silme commit edilmedi — `git status: D`).

---

## 🔍 Web Verification Status (2026-10-07)

| Teknoloji | Versiyon | Web Doğrulama | Kaynak |
|-----------|----------|:-----------:|--------|
| PHP | 8.4.22 | ✅ | [php.net](https://php.net) |
| C++ | C++20 (ISO/IEC 14882:2020) | ✅ | [cppreference](https://en.cppreference.com/w/cpp/20) |
| MySQL | 9.0.1 | ✅ | [Oracle docs](https://dev.mysql.com/doc/relnotes/mysql/9.7/en/news-9-0-0.html) |
| JUCE | 9.0.0 | ✅ | [forum.juce.com](https://forum.juce.com/t/juce-9-is-available-now/69175) |
| ASIO SDK | 2.3.4 | ✅ | [steinberg.net](https://www.steinberg.net/developers/asiosdk-open/) |
| LM5122 | Boost Converter | ✅ | [ti.com](https://www.ti.com/product/LM5122) |
| AK4458 | 32-bit 8ch DAC | ✅ | [akm.com](https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/) |
| PCM3168A | Codec (ADC+DAC) | ✅ | [ti.com](https://www.ti.com/product/PCM3168A) |
| MJL21194/93 | Power Audio Trans. | ✅ | [onsemi.com](https://www.onsemi.com) |
| XMOS XU316 | USB Audio SoC | ✅ | [xmos.com](https://www.xmos.com/processors/xu316) |

*SSOT: .ai/ · Pointer v3.2.0 — Last Updated: 2026-10-10 (disk hizası: 13 proje skill · 21 katman K000-K020)*
*Mode: Red Team · Human Mode · Truth Mode*