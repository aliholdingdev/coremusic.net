---
# Control Plane FM standardı v2 (13 alan — 2026-10-07)
title: "CoreMusic — Context Planner Spec (Control Plane)"
type: guide
category: documentation
version: 1.0.0
status: approved
authority: reference
updated: 2026-10-07
tier: 3
domain: architecture
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md", ".ai/AGENTS.md", ".ai/index.md"]
---

# CoreMusic — Context Planner Spec (Control Plane)

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE]] · [[AGENTS]] · [[WORKFLOW]] · [[keys]] · [[CONTEXT]]

---

## §1 Amaç

Bu doküman, `.ai` Control Plane'in **otomatik context planner** mekanizmasının
**konseptüel mimarisini ve sözleşmelerini** tanımlar (interview kararı **Q4-B** —
kullanıcı onaylı: deterministik context loading tercih edilmiştir).

Amaç: her AI Agent görevinin başında, görev tipi + agent rolü + dosya metadata'sı
(13 alanlı frontmatter) kullanılarak **deterministik ve tekrarlanabilir** bir context
seti üretilmesidir — agent'ın "hangi dosyayı okuyayım" kararını disipline eder,
gereksiz okumayı (context pollution) ve otorite kaybını (§27) önler.

Hedef kitle: CoreMusic AI Agent'ları (12 proje + 5 global), Vault Steward (MO),
insan geliştirici (spec değişiklik onayları). Vault bağlantısı: `.ai/CLAUDE.md` §16 boot
protokolü, `.ai/AGENTS.md` §24 reference tracking, `.ai/WORKFLOW.md` §8.9-§8.10 akış.

Neden şimdi yazıldı (2026-10-07): Control Plane mimari keşfi (interview Aşama 1-2)
tamamlandı; Q1-Q7 kararları onaylandı; bu spec o kararların **planner** ayağının
tek kaynaklı tanımıdır. Kod implementasyonu **bu spec'in kapsamı DIŞINDADır**
(onaylı restate Out of Scope) — önce spec, sonra (ayrı onayla) implementasyon.

> **eli10 (basit):** Bu dosya, "AI ne okuyacak?" sorusunun cevabını önceden
> kurallara bağlar; böylece her seferinde farklı dosyalar okunmaz.
> **eli15 (detay):** Context yükleme agent kararına bırakılırsa davranış
> deterministik olmaz (§4.2); bu spec metadata'dan tekrarlanabilir çıktı üretir.
> Okunması, planner davranışını gösterir. Yazılması, token ekonomisini ve
> otorite hiyerarşisini korur. Spec olmadan planner kodu kural tanımsız çalışır
> — hangi tier'ın her zaman zorunlu olduğu belirsizleşir.

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Planner tetikleyici tanımı (ne zaman çalışır) | **Implementasyon kodu** (planner motoru — ayrı faz, ayrı onay) |
| Girdi sözleşmesi: görev metni + agent rolü + FM metadata (13 alan) + index/keys indeksi | `.claude/hooks/` mevcut hook'larının değişimi (skill-mandate, ai-risk-gate, ai-validate-post) |
| Çıktı sözleşmesi: sıralı context seti (§28 hiyerarşisi) + token bütçesi | Dashboard/görselleştirme (yok) |
| Fallback ve hata davranışları (metadata eksik → ne olur) | Planner'ın LLM ile mi rule-engine ile mi çalışacağı karar implementasyonda |
| Tier çekirdek seti (her görevde zorunlu dosyalar) | Domain klasör izolasyonu (yalnız gerçek domain büyümesinde — Q kararı) |
| Domain ↔ dosya eşleme tablosu (metadata registry) | `.claude/skills` / `.claude/agent` dosyalarının planner girdisi olması (onlar ayrı sistem — Q5 ayrışım) |
| Dry-run örnekleri ve kabul kriterleri | Token sayımının gerçek ölçümü (ölçüm implementasyonda) |

**Alt konular:**
1. Tetikleyici ve yaşam döngüsü (§5.1)
2. Girdi sözleşmesi — 13 alanın planner kullanımı (§3.2)
3. Tier çekirdek seti — her zaman zorunlu (§3.3)
4. Domain registry — hangi domain hangi dosyaları çeker (§3.4)
5. Çıktı şablonu — sıralı context paketi (§3.5)
6. Token bütçesi politikası (§3.6)
7. Fallback / hata / güven (§4.3, §4.4)
8. Dry-run kabul kriterleri (§5.3, §6.2)

---

## §3 Mimari

### §3.1 Genel Akış (pipeline)

```text
[TRIGGER] görev başlangıcı (session-init / Skill mandate hook noktası)
    │
    ▼
[1] GOAL PARSE        görev metni → task type + domain adayları (anahtar kelime)
    │
    ▼
[2] ROLE RESOLVE      çağıran agent rolü → ilgili .agents/ tanımı (Q5: 11 domain agent)
    │
    ▼
[3] METADATA READ     .ai/**/*.md frontmatter (tier, domain, ssot, risk, owner, depends-on)
    │
    ▼
[4] PLAN BUILD        T1 çekirdek (HER ZAMAN)
    → T2 global rules (HER ZAMAN)
    → T3 domain eşleşmesi (domain registry §3.4)
    → T4 agent tanımı (rol eşleşmesi)
    → T5 task-state (varsa: CHECKLIST/TODO/MEMORY ilgili satırlar)
    │
    ▼
[5] BUDGET GATE       token bütçesi kontrolü (§3.6) — aşımda öncelik sırasıyla kırp
    │
    ▼
[6] OUTPUT            sıralı context seti + gerekçe satırları (§3.5 şablonu)
    │
    ▼
[7] VERIFY            eksik metadata → FALLBACK (§4.3) · doğrulama: validator temiz mi?
```

**Önemli sınır:** Planner **okuma listesi üretir, içerik ÖZETLEMEZ.** İçerik okuması
veyerine listeyi verir; dosyalar ilgili agent/oturum tarafından okunur. Planner
kusurlu çıktı üretirse bile **otorite kaybı olmaz** (T1/T2 seti her koşulda sabittir).

### §3.2 Girdi Sözleşmesi — 13 alanın planner kullanımı

Frontmatter standardı: `.ai/.templates/coremusic-vault-template.md` (v2, 13 alan).

| Alan | Planner'daki rolü | Zorunlu mu? | Not |
|------|-------------------|:-----------:|-----|
| `tier` | Çekirdek set + öncelik sırası (T1 her zaman; T2 her zaman; T3-5 filtreli) | ✅ | enum 1-5; asset dosyalarda yok (`type: asset`) |
| `domain` | Dosyanın bağlanacağı görev domaini (anahtar filtre) | ✅ | §3.4 registry'deki değerlerle eşleşmeli |
| `ssot` | `true` = konunun canonical owner'ı → **planner tercih eder**; `false` = derived (son çare, navigasyon) | ✅ | ssot:false dosya T3 kaynak olarak yalnız derived ise çekilir |
| `risk` | Çıktıya "bu dosyaya yazma HIGH-risktir" etiketi ekler (Q3 matrisi) | ✅ | planner salt-okur; risk yalnız uyarı üretir |
| `owner` | Eksik metadata'da kime sorulacağı (fallback sahibi) | ✅ | §4.3 |
| `depends-on` | Çekirdek sete bağımlı dosyaları çeker (graf genişletme, 1 kademe) | ✅ | 1 kademe YASAK (sonsuz derinlik) — 2. kademede dur |
| `title` | Çıktı listesinde insan-okunur etiket | ✅ | — |
| `type` | Kategori ayrımı: `guide/template/playbook/spec/asset` | ✅ | `asset` → planner'a girmez (tier yok) |
| `category` | İnce filtre (aynı domain içinde konu ayrımı) | ✅ | §3.4 ikinci seviye anahtar |
| `version` | Çıktıda "hangi sürüm bağlandı" kaydı (audit) | ✅ | — |
| `status` | `deprecated`/`rejected` dosyalar planner'a girmez | ✅ | enum: active/draft/proposed/approved/rejected/deprecated |
| `authority` | Sahip bilgisi (planner'da yalnız raporlama) | ✅ | öncelik DEĞİL — öncelik `tier`'dır (Q7 ayrımı) |
| `updated` | Tazelik uyarısı (> eski eşik → "stale olabilir" notu) | ✅ | eşik: implementasyonda ayarlanır |

**Yasak:** Planner, metadata alanı olmayan/eksik dosyayı "sessizce" güvenilir kabul
etmez → FALLBACK (§4.3). Metadata okunamazsa plan **üretilmez**, on-demand okuma moduna
düşülür (`⚠️ VERIFICATION REQUIRED`).

### §3.3 Tier Çekirdek Seti (her görevde ZORUNLU — §27 koruması)

| Tier | Dosyalar | Neden her zaman |
|------|----------|-----------------|
| **T1** | `.ai/CLAUDE.md` | Governance + 16 guardrail + §2.1 öncelik sırası — hiçbir task üstünde değildir |
| **T2** | `.ai/AGENTS.md` · `.ai/WORKFLOW.md` · `.ai/ULTRA-THINKING.md` · `.rules/senior-mode.md` | Global engineering rules: anti-overthink, execution loop, süreç kapıları, mod kuralları |
| **T1/T2 güvenlik özeti** | (vault güvenlik kurallarının özet satırları — CLAUDE §6/§21/§23) | Security, T3-5 tarafından override EDİLEMEZ (§35); token optimizasyonu ile ATILAMAZ (§27) |

- **Kırpma yasağı:** T1/T2 çekirdeği **asla** token bütçesi kırpmasıyla çıkarılamaz.
  Bütçe aşılırsa kırpma sırası: T5 → T3 ilgisiz domain → T4 ilgisi olmayan rol → T2
  (asla) → T1 (asla).
- **Çekirdek set fiziksel olarak okunmaz** — boot'ta zaten `instructions[]` +
  Skill-mandate hook bunları bağlama getirir; planner çekirdeği **listeler ve
  "yüklenmeli" diye işaretler** (ikinci kez okuma israfına yol açmaz — double-load
  koruması: mevcut context'te varsa işaret "zaten yüklü" olur).

### §3.4 Domain Registry (metadata ↔ dosya eşleme)

Planner'ın filtre anahtarı = `domain` alanı. Güncel registry (FM taramasıyla türetilir —
kaynak: `.ai/**/*.md` `domain:` alanları):

| Domain | Tipik T3+ dosyalar | Tetikleyen görev anahtarları (örnek) |
|--------|--------------------|--------------------------------------|
| `governance` | `CLAUDE.md` (T1) | guardrail, anayasa, kural sorusu, otorite |
| `rules` | `ULTRA-THINKING.md`, `.rules/senior-mode.md` | anti-overthink, mod, kural bütçesi |
| `workflow` | `WORKFLOW.md`, `CHECKLIST.md` | süreç, kapı, faz, session, checklist |
| `decisions` | `brain.md`, `.decisions/**` | ADR, karar geçmişi, "neden böyle" |
| `agents` | `AGENTS.md`(T2 dominant), `.agents/**`, `ROLE.md`, `engine.md` | agent rolü, routing, handover, persona |
| `knowledge` | `glossary.md`, `ecosystem/**`, `servers/**` | terim, ekosistem, dış kaynak |
| `product` | `VISION.md`, `PROJECTS.md` | vizyon, yetenek, kapsam, hedef kitle |
| `task-state` | `MEMORY.md`, `PLAN.md`, `TODO.md` | oturum durumu, todo, plan |
| `navigation` | `index.md`, `keys.md`, `CONTEXT.md` | dizin, arama, "nerede" |
| `templates` | `.templates/**` | şablon seçimi (Guardrail #16) |
| `ui` | `ui-design/**` | mockup, tasarım, CSS, token |
| `data` | `.sql/**` | şema, BCNF, migration |
| `hardware` | (donanım bilgi dosyaları) | Class AB, termal, PCB, BOM |

**Registry güncelleme kuralı:** yeni `domain` değeri FM'e girerse registry'ye SATIR
eklenir (In-Place); registry'de olmayan domain → validator `fm-check`-e benzer uyarı
üretir (spec bile kendini güncellemez — değişiklik = Medium risk, §4.3'e değil
değişiklik yaşam döngüsüne tabi).

### §3.5 Çıktı Şablonu (planner çıktısının sözleşmesi)

```markdown
## CONTEXT PLAN — {görev başlığı} · {tarih}
| # | Dosya | Tier | Domain | ssot | Neden (gerekçe) | Durum |
|---|-------|:----:|--------|:----:|-----------------|-------|
| 1 | .ai/CLAUDE.md | 1 | governance | ✓ | çekirdek — her zaman zorunlu | zaten yüklü / oku |
| 2 | .ai/AGENTS.md | 2 | rules | ✓ | çekirdek — global kurallar | ... |
| …| (görev-domain T3 dosyaları) | 3 | … | … | görev anahtarı: "…" | oku |
| n | (rol T4 tanımı) | 4 | agents | ✓ | rol: {agent} | oku |
Bütçe: {tahmini token} / {limit} · Kırpılan: {liste veya —} · Uyarılar: {stale/eksik metadata}
```

- Her satırda **gerekçe zorunlu** (plansız okuma yasağı — §27 ihlali sayılmaz).
- `Durum` sütunu: `zaten yüklü` (boot/hooks getirmiş) | `oku` | `atla (deprecated)`.
- Çıktı, **audit için saklanabilir** (oturum loguna satır) ama zorunlu değildir.

### §3.6 Token Bütçesi Politikası

| Kalem | Değer | Kaynak |
|-------|-------|--------|
| Çekirdek set (T1+T2) | Bütçe DIŞI (zorunlu, kırpılamaz) | §27, Q4-B |
| Görev context (T3+T4+T5) | **50K token Instruction bütçesi** (mevcut kural korunur) | `.ai/WORKFLOW.md` §8.9 prompt-maker kuralı |
| Tercih sırası (aşımda) | ssot:true → düşük `category` genişliği → en yeni `updated` | §3.2 |
| Ölçüm | Tahmin yerine gerçek sayım implementasyonda (`[VERIFY REQUIRED]` bu spec'te) | Zero-Hallucination |

**Kural:** Bütçe dolgu ile değil, **ilgisizlikle** kırpılır. `deprecated`/`rejected`
status her zaman elenir. `ssot:false` yalnız derived gerekiyorsa girer.

---

### §3.7 Metadata Cache ve Tazelik (implementasyon notları)

| Sorun | Çözüm | Tetikleyici |
|-------|-------|-------------|
| Her görevde tüm `.ai/**/*.md` FM taraması maliyetli | Oturum içi **metadata cache** (FM alanları → bellek) | session boot |
| Cache bayatlar | Cache invalidation = her `.md` Write/Edit sonrası `ai-validate-post.cjs` başarılıysa | mevcut hook (canlı) |
| Oturumlar arası tutarlılık | Cache **yalnız oturum içi**; kalıcı değil (boot'ta yeniden tarama) | §5.1 adım 1 |
| Gerçek maliyet ölçümü | `find .ai -name "*.md"` + FM regex — sayı implementasyonda | `[VERIFY REQUIRED]` (bu spec'te tahmin yazılmaz) |

**Cache güven kuralı:** Plana cache tarihi EKLENİR (`plan age: Ndk`). Cache yaşlıysa
(kritik dosya T1/T2 değişmişse) cache ATLANIR, FM doğrudan okunur. Cache asla disk'in
yerine geçmez; validator çıkışı cache'in doğruluğunun kanıtıdır.

### §3.8 Domain ↔ Skill Çapraz Bağlantısı (Q5 ayrışımı ile uyum)

Planner ile Skill Mandate **aynı anda** çalışır; işleri ayrıdır (çakışma yok):

```text
Skill Mandate (hook)   → "BU GÖREVİN PROSEDÜRÜ hangi skill?" → skill SKILL.md yükle
Context Planner (spec) → "BU GÖREVİN OKUNACAK DOSYALARI ne?"  → .ai dosya listesi
```

| Görev | Skill (prosedür) | Planner context (dosya) |
|-------|------------------|--------------------------|
| CSS bileşen kodu | `ui-code-generator` | T1+T2 + `ui-design/*` + `.templates/frontend/*` |
| Güvenlik değişikliği | `security-hardening` | T1+T2 + `.decisions/` (ADR-010/012/022) + güvenlik kuralları |
| PHP endpoint | `php-backend-standards` | T1+T2 + `.decisions/` + `.sql/` + `.agents/backend-architect` |
| Vault düzeltme | `truth-engine` (doğrulama) + `vault-sync-post` (kapanış) | T1+T2 + hedef dosya + `CONTEXT` |
| Agent tartışma | `agent-debate` (global) | T1+T2 + `AGENTS` §6.1 + `.personas/` |

**Çakışma kuralı:** İkisi de aynı dosyayı isterse okuma **TEK KEZ** yapılır (double-load
koruması §3.3). Skill içeriği planner'ın **girdisi DEĞİLDİR** — skill ≠ `.ai`
authority (Q5 ayrışımı); planner yalnız `.ai/**/*.md` FM'ini tarar.

---

## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[CLAUDE]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

### §4.1 Planner Kuralları (bağlayıcı)

1. **T1/T2 çekirdeği her zaman** — kırpma yasağı (§3.3).
2. **Plansız okuma yasağı:** planner listesi dışındaki dosya, görevle ilgili gerekçe
   yazılmadan okunmaz (context pollution); gerekçesiz satır planner çıktısında yoktur.
3. **Metadata önce, tahmin sonra:** dosya seçimi yalnız FM alanlarından; FM yoksa
   FALLBACK (§4.3) — "bence şudur" diye seçme.
4. **Determinism:** aynı görev metni + aynı rol + aynı FM → **aynı context seti**
   (§4.2 determinism hedefi; sıralama tabloda sabittir: T1→T2→T3→T4→T5).
5. **Salt-okuma:** planner hiçbir dosyaya YAZMAZ, hiçbir dosyayı DEĞİŞTİRMEZ.
6. **Deprecated elenir:** `status: deprecated|rejected` hiçbir plana girmez.
7. **Güvenlik üstünlüğü:** security/governance domaini (T1) başka domain lehine
   ÇIKARILAMAZ; domain çelişkesinde T1 kazanır.
8. **1 kademe depends-on:** `depends-on` genişletmesi yalnız 1 kademede yapılır
   (döngü riski → validator dep-check zaten circular tespit eder).

### §4.2 Determinism Gerekçesi

Q4-B kararının gerekçesi: manuel `@`-on-demand okuma agent disiplinine bağlıdır ve
aynı görev iki farklı oturumda farklı okuma listeleri üretir. Planner, metadata'dan
aynı listeyi üretir → tekrarlanabilir, denetlenebilir, token ekonomisi ölçülebilir.
Bu gerekçe, Q4-A (on-demand) tercih edilmemesinin **kayıtlı sebebidir**; tersine
dönüş = mimari karar değişikliği (High risk — insan onayı).

### §4.3 FALLBACK (metadata eksik / planner başarısız)

```text
FM okunamadı veya alan eksik
  → planner planı ÜRETEMEZ
  → mod: ON-DEMAND okuma (eski davranış) + ⚠️ VERIFICATION REQUIRED
  → eksik alan listesi owner'a bildirilir (FM: owner alanı, §3.2)
  → validator fm-check ihlali giderilmeden plan "temiz" sayılmaz
```

Yasak: eksik metadata'yı varsayım ile tamamlamak (Zero-Hallucination ihlali).

### §4.4 Güven Sınırı (agent output trust — §60)

Planner çıktısı `Generated`'dır; `Authoritative` DEĞİLDİR. Güven kaynağı:
(1) FM alanlarının validator ile doğrulanmış olması (validate --check exit 0),
(2) çekirdek setin sabitliği, (3) gerekçe satırlarının denetlenebilirliği.
Planner çıktısına dayanarak T1/T2 dosyası değiştirilemez (ayrı High-risk kapısı —
ai-risk-gate hook).

---

### §4.5 Çakışma Senaryoları ve Çözüm Tablosu

Plan üretiminde karşılaşılan çelişkiler otomatik "karar" ile DEĞİL, aşağıdaki
tabloyla çözülür (Conflict modeli: Source → Impact → Authority Analysis → Decision):

| # | Senaryo | Örnek | Çözüm (authority analizi) | Çıktı davranışı |
|---|---------|-------|---------------------------|-----------------|
| 1 | Görev 2 domain'e giriyor | "DB şeması + API endpoint" → `data` + `workflow` | İkisi de T3 → **ikisi de planda** (T3 bütçe içi, sıralama category'ye göre) | Çift T3 satırı; gerekçe ayrı ayrı |
| 2 | Domain T3, T1/T2 lehine çelişiyor | "CLAUDE'daki guardrail'i esnet" | T1 kazanır (§4.1-7) | Plan üretimi DUR → High-risk kapısı (ai-risk-gate ask) |
| 3 | Aynı konu için 2 `ssot:true` | (validator ssot gözden geçirme uyarısı) | İnsan kararı beklenir; planner tekini seçmez | Her ikisi de listelenir + "duplicate: insan kontrolü" uyarısı |
| 4 | Rol belirsiz | Görev 2 `.agents/` tanımına sığıyor | En dar rol (dominant domain) alınır; belirsizse T4 ATLANMAZ, ikisi de listelenir + uyarı | Çift T4 satırı + `[AI TO DETERMINE]` notu |
| 5 | `status: draft` hedef | Planlanan ADR draft'ı | Girer ama `Durum: draft — kanıt değildir` etiketiyle (§50 historical ayrımı) | Uyarılı satır |
| 6 | `updated` bayat | > eşik (stale) | Girer + `stale olabilir` notu; `stale knowledge` işareti (§34) | Uyarılı satır |
| 7 | Planner determinizmi ihlal eden girdi | Aynı görev → farklı iki planda | Son plan değil, **ilk plan** esas; fark kaynağı bulunana kadar `⚠️ VERIFICATION REQUIRED` | Determinism ihlali = validator benzeri rapor |

**Yasak:** Senaryo 2 ve 7'de planner'ın "sessizce bir tarafı seçmesi" (§23 conflict
modeli ihlali). Çözüm üretilemiyorsa plan `INCOMPLETE` olarak işaretlenir ve görev
High-risk onayına düşer.

---

## §5 Workflow

### §5.1 Tetikleyici Yaşam Döngüsü

| # | An | Ne olur | Mekanizma |
|---|----|---------|-----------|
| 1 | Session boot | Çekirdek set bağlama gelir (T1+T2) | `settings.json` `instructions[]` + UserPromptSubmit skill-mandate hook |
| 2 | Görev başlangıcı | Planner çalışır → CONTEXT PLAN üretilir | (implementasyon fazı: session-init / agent dispatch noktası) |
| 3 | Görev ortası | Yeni domain girerse plan genişletilir (T3 ek satır) | Aynı sözleşme; incremental |
| 4 | Görev kapanışı | Plan audit satırı (opsiyonel) + vault-sync | `.ai/WORKFLOW.md` §9 |
| 5 | Metadata değişimi | Validator PostToolUse → plan geçerliliği bozulursa yeniden üretim | `ai-validate-post.cjs` + `validate.mjs` |

### §5.2 Uygulama Adımları (agent gözüyle)

```text
1. trigger algıla (yeni görev / rol değişimi / domain değişimi)
2. FM taraması: find .ai -name "*.md" → tier/domain/ssot/status oku (cache'lenebilir)
3. görev anahtarları çıkar (goal parse) — §3.4 registry ile eşleştir
4. plan kur: çekirdek (§3.3) + domain T3 + rol T4 + task-state T5
5. bütçe filtresi (§3.6) — T5→ilgisiz T3→ilgisiz T4 sırasıyla kırp; T1/T2 asla
6. çıktıyı §3.5 şablonuyla ver (gerekçe sütunu ZORUNLU)
7. oku: durumu "zaten yüklü" OLMAYAN satırları oku
8. doğrula: validate --check hâlâ temiz mi? (okuma yazma yapmaz → çoğunlukla evet)
```

### §5.3 Dry-Run Örnekleri (kabul kriteri — §6.2 için)

**Örnek A — PHP endpoint görevi** ("music paneline stream endpoint'i ekle"):

```text
goal anahtarları: php, endpoint, api, stream → domains: [governance, rules, workflow, agents] + data(.sql şema)
PLAN: T1 CLAUDE · T2 AGENTS+WORKFLOW+ULTRA · T3 .decisions (ADR-026/028 download) + .sql
      · T4 .agents/backend-architect · T5 CHECKLIST(§A)
BÜTÇE: çekirdek zorunlu + ~task · kırpılan: —  · Uyarı: —
```

**Örnek B — CSS görevi** ("footer bar token'a bağla"):

```text
goal: css, token, ui → domains: [governance, rules, ui, templates]
PLAN: T1 CLAUDE · T2 AGENTS(§10 CSS kuralları)+WORKFLOW+ULTRA · T3 ui-design (01-mockup-index,
      02-component-inventory, tokens/design-tokens-master) + .templates/frontend/css-template
      · T4 .agents/ui-designer · T5 — (todo yoksa)
KURAL ÇAĞRISI: bu görev ayrıca Skill Mandate'e takılır → ui-code-generator SKILL.md
      (planner + skill birlikte: skill=prosedür, planner=context listesi — çakışma yok)
```

**Örnek C — Vault düzeltme** ("index.md bayat linkleri"):

```text
goal: vault, düzeltme, link → domains: [governance, navigation, workflow]
PLAN: T1 CLAUDE · T2 AGENTS+WORKFLOW · T3 CONTEXT(envanter)+index/keys (hedef dosya) + PLANNER(bu dosya, ilgiliyse)
      · T4 — (rol gerekmiyor) · T5 MEMORY (oturum notu)
RISK: hedef T3 → risk: medium → Low/Med matrisi (Q3): düzeltilir + log append
NOT: T1/T2 dosyasına dokunulacaksa ai-risk-gate → ask (HIGH → insan)
```

---

**Örnek D — Çok-domainli görev** ("download servisine anti-ban kuralları + test ekle"):

```text
goal: download, güvenlik, test → domains: [governance, rules, decisions, agents] + (security altı: .decisions)
PLAN: T1 CLAUDE · T2 AGENTS+WORKFLOW+ULTRA
      · T3 .decisions (ADR-026 download + ADR-028 anti-ban)  ← çakışma #1: iki T3 kaynak, ikisi de girer
      · T3 .decisions (ADR-010/013 rate-limit) — kategori security
      · T4 .agents/security-engineer + .agents/backend-architect ← çakışma #4: çift T4, ikisi de + uyarı
      · T5 CHECKLIST §A
BÜTÇE: T3 kaynakları category genişliğine göre sıralanır (önce download'a yakın ADR-026)
KURAL: skill eşleşmesi → security-hardening + php-backend-standards (prosedür); planner context'ten bağımsız
NOT: Q3 risk = T3 değişikliği YOKSA yalnız okuma → Low; T1/T2 değişikliği → ai-risk-gate ask
```

**Örnek E — Tartışma/debate görevi** ("3 tur/20 persona ile sayım kuralı tartışması"):

```text
goal: tartışma, persona, karar → domains: [agents, workflow, governance]
PLAN: T1 CLAUDE · T2 AGENTS (§6.1 persona hiyerarşisi — bağlayıcı) + WORKFLOW (§6 tur protokolü)
      · T3 — (tartışma konusu sayım ise: doğrulama → truth-engine skill + hedef sayı dosyası)
      · T4 AGENTS çekirdekten T2 olarak zaten içerilir; .agents/ ek T4 GEREKMEZ (persona ≠ agent — Q5)
      · T5 MEMORY (tartışma sonucu kaydı) + log.md (append)
ÖNEMLİ: .personas/ bu planda GİRER (tartışma sesleri — debate çıktısı), ama
        agent AUTHORITY DEĞİL (Q5 ayrışımı): plan notunda "ses, otorite değil" etiketi
TARTIŞMA ÇIKTISI: uzlaşma yoksa → 🔴 VERIFICATION REQUIRED + üst karar (AGENTS §6.2)
```

---

## §6 Doğrulama

### §6.1 Spec Sağlığı (bu doküman kendisi)

- [ ] FM 13 alan dolu + enum geçerli → `node .ai/scripts/validate.mjs --check` exit 0
- [ ] `status: approved` (mimari kararlar interview'de onaylandı)
- [ ] Wiki-link'ler çözülür (validator link-check temiz)
- [ ] `depends-on` hedefleri mevcut (validator dep-check)
- [ ] Şablon-Önce bloğu §4'te birebir mevcut (Guardrail #16)

### §6.2 Planner Dry-Run Kabul Kriterleri (implementasyon fazında)

- [ ] 3 örnek görev (§5.3 A/B/C) için plan üretimi **aynı girdide deterministik**
      (2 çalıştırma → aynı satır listesi)
- [ ] T1/T2 çekirdeği her 3 planda da mevcut ve ilk satırlarda (kırpılamaz)
- [ ] `deprecated` status'lü dosya hiçbir plana girmiyor (negatif test)
- [ ] FM'siz dosya → FALLBACK tetikleniyor (negatif test, §4.3)
- [ ] Bütçe aşım simülasyonu → kırpma sırası §3.6 (T5→T3→T4; T2/T1 KALIR)
- [ ] Aynı görev iki oturumda → aynı context seti (determinism)
- [ ] Planner çıktısından sonra `validate --check` hâlâ exit 0
- [ ] Çıktı örneği §3.5 şablonuna uyuyor (gerekçe sütunu dolu)

### §6.3 Completion Gate Bağlantısı

Bu spec, `.ai` mimarisinin §64 Architecture Completion Gate'inin
**"Context strategy defined"** maddesini karşılar. Kalan gate maddeleri ayrı
dokümanlarda (layer model = bu spec + FM standardı; validation = validate.mjs;
agent model = Q5 kararı) toplanır; hepsi kapanmadan mimari `FINAL` sayılmaz.

---

### §6.4 Bilinen Sınırlamalar ve Açık Kalemler (dürüstlük listesi)

| # | Sınırlama | Durum | Kapanış yolu |
|---|-----------|-------|--------------|
| 1 | Plan üretimi henüz **kodlanmadı** (bu spec konseptüeldir) | Açık — kasıtlı (restate Out of Scope) | Ayrı implementasyon fazı + onay |
| 2 | Domain registry elle senkron — yeni domain FM'e girerse registry unutulabilir | Açık | `validate.mjs`'e domain-registry check'i (sonraki sprint) |
| 3 | Token ölçümü tahmin (`[VERIFY REQUIRED]`) — gerçek sayılmadı | Açık | Implementasyonda tokenizer ile ölç + §3.6'yı sayıya bağla |
| 4 | `ecosystem/`, `ui-design/`, `.sql/`, `.templates/` alt dosyaları henüz 13-alan FM'e uplift EDİLMEDİ (31 dosyalık ilk kapsam) | Açık — kademeli | Uplift Adım B (plan §2); o zamana kadar bu dizinler **tier'siz girer → FALLBACK uyarısı** |
| 5 | `.personas/` 20 dosya FM uplift kapsamı dışında | Açık | Adım B genişletmesi (Q5'e göre tartışma girdisi) |
| 6 | Cache invalidation hook'u yalnız `.md` Write/Edit'leri kapsar (CLI/Script ile değişiklik kapsam dışı) | Açık | `validate` her boot'ta bir kez çalıştırma seçeneği (faz 2) |
| 7 | Ölçüm/performans kanıtı yok (§3.7 maliyet tahmini) | Açık | Dry-run fazında ölçüm (§6.2) |

**Kural:** Bu liste **kapsam dışı değil, izlenen borçtur** — her item Medium risk
(§3 matrisi): kapanışı proposal/review ile; T1/T2'yi etkileyen madde çıkarsa High'a
yükselir (insan onayı).

---

## §7 Referanslar

| Kaynak | İlişki | Ne için |
|--------|--------|---------|
| [[CLAUDE]] | T1 anayasa · §2.1 precedence · §16 boot | Çekirdek setin kaynağı (Q2 beş katman) |
| [[AGENTS]] | T2 global rules · §6.1 20 persona · §24 reference tracking | Rol/agent kuralları (Q5 11 agent) |
| [[WORKFLOW]] | T2 süreç · §8.9-§8.10 boot/PromptMaker akışı | Tetikleyici noktalar (§5.1) |
| [[index]] · [[keys]] | navigation (ssot:false) | Planner indeks kaynakları |
| [[CONTEXT]] | vault envanter | FM tarama kapsamı |
| `[[.templates/coremusic-vault-template]]` | 13 alanlı FM standardı | Girdi sözleşmesinin şablonu |
| `node .ai/scripts/validate.mjs --check` | Q6 validator | Metadata sağlığı (fm/check exit 0) |
| `.claude/hooks/skill-mandate.cjs` | Skill zorunluluğu hook'u | Tetikleyici nokta 1 (mevcut, canlı) |
| `.claude/hooks/ai-validate-post.cjs` | PostToolUse validator | Metadata bozulursa block (mevcut, canlı) |

**Karar geçmişi:** Q4-B (otomatik planner) interview'de onaylandı; Q1 hibrit
metadata + Q7 geniş şema bu spec'in girdisidir; Q3 onay matrisi §3.2 `risk`
alanıyla bağlanır. Tersine dönüş her biri **High risk** (insan onayı).

### §7.1 Karar İzlenebilirlik Tablosu (interview Q1-Q7 → bu spec)

| Karar | Onaylı seçim | Bu spec'teki karşılığı | Tersine dönüş riski |
|-------|--------------|------------------------|---------------------|
| Q1 Katman ekseni | Hibrit: modül (dizin) + geniş metadata | §3.2 (13 alan) + §3.4 (domain registry) | High (mimari eksen) |
| Q2 Otorite hiyerarşisi | 5 katman (Güv+Gov zirve) | §3.3 çekirdek set (T1/T2) + FM `tier` | High |
| Q3 Onay matrisi | 3 katman (Low/Med/High-İNSAN) | §3.2 `risk` + §4.4 + ai-risk-gate hook | High |
| Q4 Context loading | **B: otomatik planner** | BU DOSYANIN TAMAMI | High (§4.2 gerekçesi) |
| Q5 Agent modeli | 4-yolu ayrışım (.ai = 11 agent) | §3.8 + §5.3-E (persona ≠ agent) | High |
| Q6 Validation | Dağıık check + tek giriş + hook | §6.1 (validate.mjs --check) | Medium |
| Q7 Metadata şeması | Geniş 13 alan | §3.2 tablosu | High |

**Traceability zinciri:** Requirement (interview sorusu) → Decision (onay) →
Spec bölümü (bu tablo) → Implementation (validate.mjs / hook / planner fazı) →
Audit (log.md append). Her satır bu zinciri kanıtlar (§4.6 traceability hedefi).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-07
**Mode:** Red Team · Human Mode · Truth Mode
