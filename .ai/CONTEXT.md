---
title: "CoreMusic — .ai/ Vault Klasör Context"
type: docs
category: vault
date: 2026-10-03
updated: 2026-10-07
version: 1.2.0
status: active
authority: "SSOT (klasör kökü) — çelişkide disk kazanır"
docType: context
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: [".ai/CLAUDE.md", ".ai/AGENTS.md", ".ai/SYNC.md"]
---

# CoreMusic — .ai/ Vault Klasör Context

**docType:** context · **Klasör:** `.ai/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[../AGENTS.md]] · [[.templates/index]] · [[architecture/00-master-index]] · [[SYNC]]

**Hot Memory (2026-10-10):** Mimari context = [[architecture/00-master-index]] (21 katman K000-K020 · `katman-baglilik-matrisi` · `coremusic-mimari-plani`). Genel context = bu dosya. Sürekli güncelleme döngüsü = [[SYNC]] (hedef set 26).

---

## 1. Amaç

`.ai/`, CoreMusic'in **ikinci beyin (vault)** klasörüdür: anayasa, agent registry, süreç, mimari karar, şablon, ekran görseli ve audit trail burada yaşar ve tüm alt alan adlarının tek doğruluk kaynağıdır. Bu doküman klasörün **ne işe yaradığını** ve diskteki **gerçek** envanterini tanımlar; mevcut [[CLAUDE]] · [[AGENTS]] · [[WORKFLOW]] dosyalarını **değiştirmez**, onlara bağlar. Tüm sayılar 2026-10-03 disk ölçümüdür.

| Karar | Karşılığı (kaynak) |
|-------|--------------------|
| Vault SSOT — çelişkide disk kazanır | Kök [[../AGENTS]] §3 (Zero-Hallucination) |
| Yeni context dokümanı şablonla üretilir | Guardrail #16 → [[.templates/frontend/context-template]] §1 |
| Yeni ADR numarası 088+ | [[../AGENTS]] (Frozen 001-037 değişmez) |
| log.md append-only | Kök [[../AGENTS]] §9 · [[log]] |
| **Control Plane v2**: FM standardı 13 alan · validator `scripts/validate.mjs --check` · planner spec [[PLANNER]] · HIGH-risk kapısı ai-risk-gate + ai-validate-post hook'ları | interview Q1-Q7 (2026-10-07, onaylı) + `.claude/settings.json` |
| Boot'ta toplu vault okuma yasak | Kök [[../AGENTS]] §9 (yalnızca ihtiyaç anında `@`) |

> **eli10 (basit):** Bu klasör, projenin hafızası: kurallar, kararlar, çizimler ve geçmiş burada durur; kimse kod yazmadan önce buraya bakar.
> **eli15 (detay):** Ayrı klasör tutuldu çünkü kural ve karar koddan farklı hızda değişir; kod içine gömülse her revizyonda kaybolur. İçine anayasa, agent rolleri, süreç, ADR, şablon ve ekran görselleri yazılır. Okunur çünkü tek doğruluk kaynağıdır; yazılır çünkü değişiklik geçmişine imza atılır. Burası bozulursa tüm projede hangi kuralın geçerli olduğu bilinemez.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/**` kök dosyalar + 20 alt dizin envanteri (dosya/alt dizin sayısı) | Uygulama kodu → `shared/`, `assets.coremusic.net/`, alt alan adları |
| Boot okuma sırası ve kök dokümanlarla ilişki (wiki-link) | OpenCode çalışma ortamı → `.opencode/` (kendi CONTEXT/AGENTS/WORKFLOW dosyaları) |
| Envanter ↔ vault iddiası çelişki kaydı | ADR metinlerinin içeriği (frozen — yalnız wiki-link verilir) |
| `.ai/` içinde ne yazılır / ne yazılmaz (yazım protokolü) | `.env.figma` içeriği (REDACTED — hiçbir yere yazılmaz) |

- **Kullananlar:** MO (birincil, vault-updater), 11 agent'ın tamamı (okur), Security (REDACTED denetimi), QA (wiki-link/link kontrolü).
- **Ön koşul:** Hedef dizin diskte MEVCUT ve sayımlar yapılmış olmalı; okunmayan içeriğin rolü `VERIFICATION REQUIRED` olarak yazılır.
- **Not:** Bu doküman `docType: context` = bilgi ağırlıklıdır; kural koymaz, mevcut [[CLAUDE]]/[[AGENTS]]/[[WORKFLOW]] dosyalarındaki kurallara referans verir.

> **eli10 (basit):** Bu dosya klasörün haritası: içinde ne var, kim okur, hangi dosya ne işe yarar; kuralları burada yeniden yazmaz.
> **eli15 (detay):** Kapsam ayrı çizildi çünkü envanter ile kural birbirine karışırsa eski sayı yeni kural sanılır. İçine yalnız ölçülmüş dosya/dizin sayıları ve ilişkiler girer. Kural okumak isteyen [[CLAUDE]]'ye, süreç isteyen [[WORKFLOW]]'ye yönlendirilir. Kapsam yazılmazsa bu dosya kısa sürede ikinci bir anayasaya dönüşür.

---

## 3. Mimari

### 3.1 Kök Dosya Envanteri (depth 0 — **2026-10-10 ölçümü: 20 `.md` = 20 dosya** · `.env.figma` **YOK**; önceki: 2026-10-07 20 `.md` + `.env.figma` = 21 · 2026-10-03 19 dosya)

> ⚠️ **DÜZELTME (2026-10-10):** Eski kayıt `.env.figma`'yı kök dosya sayıyordu — `Test-Path .ai/.env.figma = False`. Figma anahtarı artık `.ai/.env.figma`'da **bulunmuyor**; `.gitignore:69` kuralı ve `figma-extract.ps1`/`figma-tokens.ps1` davranışı değişmedi, ancak **anahtarın nerede olduğu UNKNOWN** (REDACTED kuralı gereği araştırılmaz, kopyalanmaz). Eski sayım 21 → yeni 20.

| Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|-------|--------------------|---------------|-----------|---------------------|
| `CLAUDE.md` (989 satır) | AI anayasası — 16 Hard Guardrails, boot protokolü, forbidden patterns | §1-§34 bölüm yapısı, guardrail tabloları | Her ajanın uyması gereken tek kural metni olsun (docType: claude) | Guardrail/ADR revizyonu (MO + Security) |
| `AGENTS.md` (798 satır) | Agent registry SSOT — 11 agent, routing §6, handover §9, escalation §10 | Rol tabloları, keyword routing, domain boundary | Görev kime gideceği tek yerde belli olsun | Yeni agent/rol/kural değişince (MO) |
| `WORKFLOW.md` (892 satır) | Vault süreçleri — 12/20 faz, ADR lifecycle, hard gates §9 | Adım/kapı tabloları, faz kayıtları | Süreç tekrarlanabilir ve denetlenebilir olsun | Faz/kapı değişince (MO) |
| `index.md` (824 satır) | Master katalog — hızlı referans, ADR listesi, envanter metrikleri | §1-§24 indeks tabloları | Kayıp dosya/düzey farkı için tek katalog | Yeni dosya/ADR eklenince (vault-updater) |
| `keys.md` | Keyword haritası — routing anahtarları | Keyword → konu eşlemesi | Arama/eko eşlemesi tek yerde dursun | Keyword eklenince |
| `RAG.md` (yeni 2026-10-07) | Retrieval indeksi + pipeline — "hangi soru → hangi dosya" eşlemesi (§3 20 satır) + embedding/arama tasarımı (§4, ÇOĞU PLANNED — ADR-030) | Konu→dosya→keyword tablosu, durum sütunu | AI analizi doğru dosyaya hızlı gitsin; boot'ta toplu okuma yerine tek tablo okunsun | Vault'a dosya eklendiğinde/taşındığında (`vault-sync-post`) |
| `brain.md` | Mimari karar özeti (ADR türevi) | Karar satırları | Kod öncesi karar okunsun | Yeni ADR'de |
| `ROLE.md` | Rol tanımı — agent teknoloji/yüzey eşlemesi | Rol satırları | Rol ile stack eşleşsin | Stack değişince |
| `engine.md` | Orkestrasyon motoru — §2 orkestrasyon, §9 stack etiketleri, §12 faz kapanışı | Orkestrasyon tabloları | Multi-agent akış tek yerden yönetsin | Orkestrasyon kuralı değişince |
| `MEMORY.md` | Session hafızası — boot listesi ve oturum durumu | Adım listesi, oturum notları | Oturumlar arası bağlam kaybolmasın | Her oturum kapanışında |
| `log.md` | Audit trail | Yalnız ekleme (append-only) | Kim-ne-ne-zaman tek yönlü kayıt | Yalnız SONA ekleme — geçmişe dokunulmaz |
| `glossary.md` | Terim sözlüğü (kod-referanslı) | Terim satırları | Terim tek anlamda kalsın | Yeni terimde |
| `ULTRA-THINKING.md` | Ultra düşünme protokolü + §3.2 kanıt dosyaları | Protokol adımları | Derin görevlerde tek akış zorunlu olsun | Protokol revizyonunda |
| `VISION.md` | Vizyon, pazar, konumlandırma metni | Vizyon paragrafları | Ürün yönü tek kaynakta dursun | Vizyon değişince |
| `PROJECTS.md` | Proje envanteri, 10 yetenek, hedef kitle | Proje/kitle tabloları | Kapsam tanımı tek yerde dursun | Kapsam değişince |
| `CHECKLIST.md` · `PLAN.md` · `TODO.md` | Plan/kontrol listesi görünümü (içerik OKUNMADI — VERIFICATION REQUIRED) | UNKNOWN | — | — |
| `broken-links-report.md` | Kırık link raporu görünümü (içerik OKUNMADI — VERIFICATION REQUIRED) | UNKNOWN | — | — |
| `.env.figma` | ⚠️ **2026-10-10: diskte YOK** (`Test-Path = False`) — eski kayıt "Figma erişim anahtarı, gitignore'da" idi. Kural hâlâ geçerli: anahtar hiçbir `.md`/`.json`/`.log`a yazılmaz (REDACTED); nerede olduğu UNKNOWN | **YOK (2026-10-10)** | — | — |

**Kökdosya eli10/eli15 blokları (başlıca 6 madde):**

**`CLAUDE.md`**
> **eli10 (basit):** Projede yapmayacağımız şeylerin ve uyacağımız temel kuralların yazılı olduğu anayasa.
> **eli15 (detay):** Ayrı dosyadır çünkü kural, koddan daha uzun yaşar ve tüm ajanlar aynı metni okumalıdır. İçine guardrail listesi ve yasaklar yazılır. Okunur çünkü ilk bakışta sınırlar görünür. Yeni ADR ya da guardrail kararı çıktığında düzenlenir; sessizce değişirse denetim imkânsızlaşır.

**`AGENTS.md`**
> **eli10 (basit):** Kimin hangi işi yaptığını ve görevin kime gideceğini gösteren listedir.
> **eli15 (detay):** Ayrı dosya çünkü rol ile kural farklı sorumluluk taşır; roller tek yerde olmazsa görev yanlış ajana gider. İçine 11 agent'ın yetkisi, routing ve handover kuralları yazılır. Okunması, işe başlamadan doğru kapıyı bulmayı sağlar. Yeni agent eklendiğinde ya da routing değişince düzenlenir.

**`WORKFLOW.md`**
> **eli10 (basit):** İşlerin hangi sırayla, hangi kontrollerden geçerek yürütüldüğünü anlatan yol haritası.
> **eli15 (detay):** Ayrı dosya çünkü adım ve kapılar zamanla değişir, sabit kural ile karışmamalıdır. İçine faz tabloları, kapılar ve rapor adımları yazılır. Okunması, atlanan bir kontrolün (test/onay) fark edilmesini sağlar. Yeni faz ya da kapı eklendiğinde güncellenir.

**`index.md`**
> **eli10 (basit):** Vault içindeki her şeyi tek sayfada bulmamızı sağlayan fihrist.
> **eli15 (detay):** Ayrı dosyadır çünkü içerik ile fihrist farklı hızda büyür. İçine dosya/ADR/envanter listeleri yazılır. Okunması, aranan dokümanın yerini kaybetmemeyi sağlar. Yeni dosya veya karar eklendiğinde bir satır eklenir; mevcut satırlara dokunulmaz.

**`log.md`**
> **eli10 (basit):** Ne yaptığımızın tarihinin tutulduğu defter — sadece üstüne yazılır.
> **eli15 (detay):** Ayrı ve yalnız eklenebilir (append-only) tutulur ki geçmiş değiştirilemesin; aksi halde denetim değeri kalmaz. İçine tarih, yapılan iş ve sonuç yazılır. Okunması, hangi oturumda ne olduğunun kanıtını verir. Kapıdan sonra eklenir; eski satır silinemez.

**`.env.figma`**
> **eli10 (basit):** Dış servise giriş anahtarını taşıyan, kimseye gösterilmeyen dosya.
> **eli15 (detay):** Ayrı ve gizli dosyadır çünkü anahtar metin olarak yayılırsa erişim ele geçirilir. İçeriği hiçbir belgeye kopyalanmaz (REDACTED). Okunması yalnız betikler içindir. Anahtar süresi dolunca yenilenir; kural ihlalinde derhal döndürülür.

### 3.2 Alt Dizin Envanteri (depth 1 — **2026-10-10 ölçümü: 17 dizin**; önceki: 2026-10-07 13 dizin · 2026-10-03 20 dizin)

**17 dizin (2026-10-10):** `.agents` · `.decisions` · `.obsidian` · `.personas` · `.png` · `.rules` · `.sql` · `.templates` · `architecture` · `ecosystem` · `index` · `prompts` · `reports` · `scripts` · `servers` · `SESSIONS` · `ui-design`

> ⚠️ **DÜZELTME (2026-10-10):** Eski tablo 13 dizin sayıyordu ve `archives/` · `checklists/` · `.subdomains/` · `projects/` · `.diagram/` satırlarını taşıyordu — **bu beş dizin diskte YOK** (`Test-Path = False`). Yeni/eksiksiz eklenen dizinler: `index/` (5 alt sayfa) · `prompts/` (3 dosya) · `SESSIONS/` (2 dosya). Aşağıdaki tabloda silinen satırlar `❌ YOK` olarak işaretlendi, sayılmıyor.

| Dizin | Alt dizin | Dosya | Ne için kullanılır | Neden ayrı | Ne zaman düzenlenir |
|-------|-----------|-------|--------------------|-----------|---------------------|
| `architecture/` | 37 | **125 md** | 21 katman (K000-K020) mimarisi — `00-master-index.md` · `katman-baglilik-matrisi.md` · `coremusic-mimari-plani.md` + 21 K-dizini | Katman başına ayrı klasör | Mimari değişince |
| `ui-design/` | 23 | **121 md** | Mockup indeksi, screens, flow, prompt, reference, tokens | Görsel/şablon kendi hiyerarşisinde | Mockup/tokens değişince |
| `.decisions/` | 3 | **97 md** | ADR arşivi: `accepted/` **84** · `draft/` **1** · `rejected/` **12** + `index.md` | Karar yaşam döngüsü ayrı dosyalanır | Yeni ADR yazımında |
| `.personas/` | 7 | **84** | Kullanıcı persona dosyaları | Persona ile kural karışmasın | Persona revizyonunda |
| `.templates/` | 13 | **69** | Guardrail #16 şablonları (13 kategori) + `index.md` | Şablon ayrı olunca yeni dosya tutarlı üretilir | Yeni şablon/kategori eklenince |
| `.sql/` | 4 | **20 .sql** | SQL şema/sorgu dosyaları (mssql, mysql, postgresql, sqlite) | DBMS başına ayrılır | Şema değişince |
| `.png/` | 3 | **19 PNG** | Görsel SSOT — salt okunur | Görsel salt okunur ayrı tutulur | Onaylı görsel ekleme/çıkarmada |
| `index/` | 0 | **5** | Master katalog hub alt sayfaları (`01-mimari` · `02-kurallar` · `03-workflow` · `04-dogrulama` · `05-referanslar`) | Çok-sayfalı hub yapısı | Bölüm değişince |
| `reports/` | 0 | **7** | Rapor çıktıları | Rapor kuralı kirletmez | Yeni raporda |
| `scripts/` | 0 | **10** | Doğrulama/vault betikleri (validate.mjs, wiki-link-check, kalip-abc-check, screens-frontmatter-check, figma-extract, figma-tokens, device-matrix-catid, fix-mojibake, vault-utf8-writer, index.md) | Betik ile doküman ayrı hızda değişir | Betik revizyonunda |
| `ecosystem/` | 0 | **13** | Ekosistem dersleri/kaynakları | Harici bilgi vault'tan ayrılır | Yeni kaynak eklenince |
| `.agents/` | 0 | **12** | 11 agent profili + alt registry `AGENTS.md` | Profil ile kök registry ayrı (çelişkide kök kazanır) | Profil/stack değişince |
| `.obsidian/` | 0 | 5 | Obsidian/görünüm yapılandırması | Araç ayarı ayrı durur | Araç ayarı değişince |
| `servers/` | 0 | **3** | Sunucu/kurulum notları | Ortam bilgisi ayrı | Sunucu değişince |
| `prompts/` | 2 | **3** | Prompt arşivi (`2026-10-08-master-prompt-v2.2.0-f1.md` vb.) | Prompt ayrı güncellenir | Prompt revizyonunda |
| `.rules/` | 0 | **1** | Kural dosyası — diskte **yalnız `senior-mode.md`** | Kural dosyası tek konu taşır | Kural ekleme/revizyonunda |
| `SESSIONS/` | 0 | **2** | Session kayıtları (`2026-10-08-kspace-kesif` · `2026-10-09-katmanli-mimari-rapor-kesif`) | Keşif çıktısı ayrı durur | Yeni session kapanışında |
| `archives/` | — | — | **❌ YOK (2026-10-10)** — eski arşiv klasörü artık mevcut değil | — | — |
| `checklists/` | — | — | **❌ YOK (2026-10-10)** | — | — |
| `.subdomains/` | — | — | **❌ YOK (2026-10-10)** | — | — |
| `projects/` | — | — | **❌ YOK (2026-10-10)** — eski kayıt "4 alt dizin / 0 dosya" idi; dizin artık hiç yok | — | İçerik gelince yeniden açılır |
| `.diagram/` | — | — | **❌ YOK (2026-10-10)** — eski kayıt "0 dosya (boş)" idi; dizin artık hiç yok | — | — |

**Toplam:** `.ai/` altında recursive **804 dosya** (gizli dahil) · **572 `.md`** · **107 dizin** (2026-10-10 ölçümü) · kök **20 `.md`**. *(önceki ölçüm: 796 dosya / 567 md — 2026-10-10 sabah · 678 dosya / 20 kök — 2026-10-07 · 984 dosya / 19 kök — 2026-10-03)*

**Dizin eli10/eli15 blokları (başlıca 5 madde):**

**`architecture/` (341 dosya)**
> **eli10 (basit):** Sistemin katman katman nasıl kurulduğunun anlatıldığı dosya dolabı.
> **eli15 (detay):** Ayrı klasör çünkü 21 katmanın (K0-K20) her birinin kendi dosyası var; tek dosyada toplansa okunmaz hâle gelir. İçine katman sorumlulukları, arayüz ve bağımlılık anlatımı yazılır. Mimari bir soru geldiğinde önce burası okunur. Katman değişince o katmanın dosyası güncellenir.

**`ui-design/` (297 dosya)**
> **eli10 (basit):** Ekranların resimleri, çizimleri ve ekran kurallarının durduğu klasör.
> **eli15 (detay):** Ayrı klasördür çünkü görsel ve kod farklı üreticiden gelir; kod yazmadan önce resme bakma kuralı buraya bağlıdır. İçine indeks, ekran tanımları, akışlar ve tasarım değerleri (token) yazılır. CSS/JS işine başlarken okunur. Mockup değişince ilgili dosya güncellenir; okunmadan kod yazmak durdurucu hatadır.

**`.decisions/` (84 dosya)**
> **eli10 (basit):** "Neyi, neden böyle yaptık" sorusunun cevabının saklandığı karar defteri.
> **eli15 (detay):** Ayrı klasördür çünkü kararlar dondurulur (frozen) ve kodla birlikte değişmez. İçine kabul edilen, reddedilen ve taslak kararlar ayrı alt dizinlerde durur. Kod öncesi okunur ki aynı tartışma tekrarlanmasın. Yeni karar yazılır; dondurulmuş karar metni değiştirilemez.

**`.templates/` (54 dosya)**
> **eli10 (basit):** Yeni dosya yazarken dolduracağımız boş formaların kutusu.
> **eli15 (detay):** Ayrı klasör, şablon zorunluluğu (Guardrail #16) için şarttır; şablon olmazsa her dosya farklı görünür. İçine konu kategorisi başına boş iskelet ve kural tabloları yazılır. Yeni doküman üretmeden önce okunur. Yeni kategori/kural eklendiğinde şablon revize edilir.

**`scripts/` (9 dosya)**
> **eli10 (basit):** Otomatik kontrol yapan küçük programlar — bağlantıları ve formatı tarar.
> **eli15 (detay):** Ayrı klasördür çünkü çalışan kod, okunan dokümandan farklıdır ve elle karıştırılmamalıdır. İçine doğrulama betikleri (wiki-link, frontmatter, şablon kontrolü, UTF-8 yazım aracı) girer. Kök dizinden çalıştırılırlar; iç dizinden çalıştırıldıklarında yanlış sonuç verirler. Betik davranışı değişince güncellenir.

### 3.3 Boot Okuma Sırası (kök ↔ .ai ↔ .opencode)

```text
kök AGENTS.md (master kurallar)
  → görevin gerektirdiği .ai/ dosyaları (yalnız ihtiyaç anında @ ile)
      CLAUDE.md → AGENTS.md → WORKFLOW.md → index.md → brain.md → ROLE.md → ...
  → .opencode/ (çalışma ortamı: CLAUDE.md + skills/ + command/ + .workflows/)
  → hedef dosya → kod/doküman → doğrulama → log.md (append)
```

- **Kural:** boot'ta toplu vault okuma yasak (kök [[../AGENTS]] §9); uzun referanslar yalnız görev anında okunur.
- **Kanıt:** kök `AGENTS.md` §9 tablosu; `.ai/CLAUDE.md` §16 Boot Protocol; `.ai/AGENTS.md` §24.2 (14 kök dosya).

### 3.4 `.ai/` ↔ `.opencode/` ↔ Kök İlişkisi

| Yön | Ne taşır | Kanıt |
|-----|----------|-------|
| `.ai/` → `.opencode/` | Kural ve şablon (skills/command orada uygulanır) | `.opencode/CLAUDE.md` frontmatter + `command/prompt-maker.md` varlığı |
| `.opencode/` → `.ai/` | Skill çıktıları, log kaydı, vault senkronu | `skills/vault-sync-post/` (2 dosya), `.workflows/vault-sync.md` |
| kök → `.ai/` | Master kuralların vault'a uzantısı | Kök `AGENTS.md` §9 tablosu |
| `.ai/` → kod | ADR/mimari kısıt kodu yönetir | `architecture/`, `.decisions/` |

### 3.5 Vault İddiası ↔ Disk Çelişkileri (disk kazanır — **2026-10-10 yeniden ölçümü**)

| İddia (kaynak) | Disk gerçeği (2026-10-10) | İşaret |
|----------------|---------------------------|--------|
| Kök `CLAUDE.md` §Skill Registry: "17 skill (12 proje + 5 global)" | `.claude/skills/` **13 dizin** · `C:\.claude\skills` **YOK** · `.opencode/skills` **YOK** | ✅ DÜZELTİLDİ (2026-10-10 — 13 proje / 0 global) |
| Kök `AGENTS.md` §7 #2: "`62 .ai/wiki/` sayfası" | `.ai/wiki/` **YOK** (0 dizin) | ✅ DÜZELTİLDİ (2026-10-10) |
| Kök `AGENTS.md` §7 #7 / §9: `error-recovery.md` | `.ai/.rules/` içinde **yalnız `senior-mode.md`** | ✅ DÜZELTİLDİ (2026-10-10) |
| Kök `AGENTS.md` §9: `@.ai/wiki/vault-agents.md` · `@.ai/raw/AGENTS.md` · `@.ai/raw/WORKFLOW.md` · `@.ai/raw/brain.md` | `.ai/wiki/` ve `.ai/raw/` **YOK** | ✅ DÜZELTİLDİ (2026-10-10 — `.ai/AGENTS.md` + `.ai/.agents/` işaretlendi) |
| Kök `README.md` badge: `.ai/architecture/index.md` | **YOK** — gerçek master `.ai/architecture/00-master-index.md` | ✅ DÜZELTİLDİ (2026-10-10) |
| Kök `CLAUDE.md`: "500 katman K000-K499, 10 domain" · `10-domain-d01-…md` … `19-domain-d10-…md` | **21 katman K000-K020** (`00-master-index.md` §1) · domain tabloları **YOK** | ✅ DÜZELTİLDİ (2026-10-10 — çelişki kaydı eklendi) |
| ADR-096/097: "6.000 katman K000-K5999 · 5.041 dizin · 5.089 md" | `architecture/` = **21 K-dizini · 125 md** | ⚠️ ADR frozen, değiştirilmez; **hedef model**, uygulama kanıtı taşımaz |
| `.ai/.decisions/index.md` §2: accepted 78 / rejected 7 / draft 0 / toplam 87 | **accepted 84 · rejected 12 · draft 1** (dosya sayısı) | ⚠️ index.md §2 sayımı güncellenmeli |
| `.ai/index.md` frontmatter: `total_files: 720` · `total_adr: 80` · `total_adr_disk: 60` | `.ai/` recursive **804 dosya / 572 md** · ADR dosyası **97** (84+12+1) | ⚠️ index.md sayımı güncellenmeli |
| `.ai/index.md` §2: `TECHNICAL_DOCUMENTATION.md` · `broken-links-report.md` | İkisi de **YOK** | ⚠️ VERIFICATION REQUIRED — linkler kırık |
| `.ai/CONTEXT.md` (önceki sürüm): 21 kök dosya (+`.env.figma`) · 13 dizin · 678 dosya | **20 kök `.md`** · `.env.figma` **YOK** · **17 dizin** · **804 dosya / 572 md / 107 dizin** | ✅ DÜZELTİLDİ (2026-10-10 — bu sürüm) |
| Kök `AGENTS.md` §24.3: `.ai/projects/NevaEngine/*.md` | `.ai/projects/` **YOK** (dizin hiç yok) | ⚠️ VERIFICATION REQUIRED |
| Kök `AGENTS.md` §24.3: `electronic/dsp/*.md` · `electronic/firmware/*.md` | `.ai/electronic/` **YOK** | ⚠️ Doğrulandı: gerçek `architecture/` altındaki K001/K003 |
| `.ai/architecture/context.md` · `.ai/architecture/rules.md` | **YOK** (opsiyonel; `.opencode/.ai` altında olabilir — VERIFICATION REQUIRED) | ⚠️ VERIFICATION REQUIRED |
| `.ai/.png` 19 PNG | `.png` altında **19 `.png`** | ✅ doğrulandı |
| `.ai/.sql` | **20 `.sql`** (4 DBMS alt dizini) | ✅ doğrulandı |

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Değer/tanım tek yerde durur (`ui-design/tokens/`, `.templates/`) | Tanım her dosyada tekrar eder → değişince hepsinde tarama gerekir |
| 4 | **Cihaz izolasyonu** | Cihaz/ekran farkı başka katmana yayılmaz | Fark tüm dosyalara sıçrar, uyumsuzluk (drift) doğar |
| 5 | **Vendor karantinası** | 3. taraf ve arşiv içeriği ayrı durur | Dış içerik içimize karışıp güncellemede kaybolur |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: kural tek yerde (`CLAUDE`), rol tek yerde (`AGENTS`), süreç tek yerde (`WORKFLOW`), karar tek yerde (`.decisions/`), şablon tek yerde (`.templates/`) durur. Böylece bir yer değişince sadece o değişir. Karar ayrı olunca frozen metin korunur, şablon ayrı olunca yeni dosyalar tutarlı üretilir. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

### 3.7 Kalan Kök Dosya Blokları (11 madde — eli10 / eli15)

> Bu bloklar §3.1 kök dosya tablosunun tamamlayıcısıdır; numara sırasını korumak için §3.6 sonrasına konmuştur.

**`keys.md`**
> **eli10 (basit):** Hangi kelimenin hangi konuya gittiğini gösteren anahtar listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü anahtar (keyword) tablosu kural metninden daha sık değişir ve ayrıca taranır. İçine kelime → konu/agent eşlemesi yazılır. Routing tartışmasında okunur. Yeni konu açıldığında bir satır eklenir.

**`brain.md`**
> **eli10 (basit):** Mimari kararların kısa özetinin durduğu defter.
> **eli15 (detay):** Ayrı dosyadır çünkü karar özeti ile karar metni (ADR) farklı uzunluktadır; ikisi birleşirse okunmaz. İçine hangi kararın hangi konuda alındığı yazılır. Kod öncesi okunur. Yeni ADR çıktığında özet satır eklenir; karar metni değiştirilmez.

**`ROLE.md`**
> **eli10 (basit):** Her ajanda hangi teknoloji ve hangi sorumluluk düştüğünün listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü rol bilgisi, görev kurallarından farklıdır ve stack ile birlikte değişir. İçine rol → teknoloji → sorumluluk satırları yazılır. Görev atarken okunur. Stack değişince ilgili satır güncellenir.

**`engine.md`**
> **eli10 (basit):** Birden çok ajanda işin nasıl paylaştırılacağını anlatan motor tarifi.
> **eli15 (detay):** Ayrı dosyadır çünkü orkestrasyon kuralları ayrı bir uzmanlık alanıdır ve ölçülebilir kapanış kriterleri taşır. İçine orkestrasyon bölümleri, stack etiketleri ve faz kapanışı listesi yazılır. Çok-agent işte okunur. Faz kapandığında kapanış kriteri güncellenir.

**`MEMORY.md`**
> **eli10 (basit):** Daha önceki oturumlardan aklımızda kalması gerekenlerin tutulduğu defter.
> **eli15 (detay):** Ayrı dosyadır çünkü oturum hafızası günlük, kural ise kalıcıdır; birleşirse eski oturum notu kural sanılır. İçine oturum listesi ve son durum yazılır. Oturum başında okunur, kapanışında güncellenir.

**`glossary.md`**
> **eli10 (basit):** Projede kullanılan özel kelimelerin tek anlamlı karşılıkları.
> **eli15 (detay):** Ayrı dosyadır çünkü terim, kuraldan bağımsızdır ve her belgede aynı anlamda kullanılmalıdır. İçine terim → tanım + kod referansı yazılır. Belge yazarken okunur. Yeni terim girince eklenir; eski tanım sessizce silinmez.

**`ULTRA-THINKING.md`**
> **eli10 (basit):** Zor işlerde izlenecek derin düşünme sırası ve kanıt dosyalarının listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü akış, sabit kuraldan farklıdır ve adım adım uygulanır. İçine protokol adımları ve kanıt dosyaları (§3.2) yazılır. Derin görev başında okunur. Akış değişince yeniden yazılır.

**`VISION.md`**
> **eli10 (basit):** Ürünün nereye gittiğini ve kime hitap ettiğini anlatan vizyon metni.
> **eli15 (detay):** Ayrı dosyadır çünkü yön metni teknik belgeden farklı hızda ve dille değişir. İçine vizyon, pazar ve konumlandırma yazılır. Kapsam tartışmasında okunur. Yön değişince güncellenir.

**`PROJECTS.md`**
> **eli10 (basit):** Projede neler yapıldığının, yeteneklerin ve hedef kitlelerin listesi.
> **eli15 (detay):** Ayrı dosyadır çünkü kapsam tanımı ayrı güncellenir; teknik dosyaya gömülürse ürün konuşması kaybolur. İçine yetenek, kitle ve sektörel çözüm tabloları yazılır. Kapsam sorusunda okunur. Yeni yetenek/kitle eklenince düzenlenir.

**`CHECKLIST.md` · `PLAN.md` · `TODO.md`**
> **eli10 (basit):** Yapılacakların, planın ve kontrol listelerinin durduğu not dosyaları (içerikleri okunmadı).
> **eli15 (detay):** Rolleri bu görevde okunmadığı için bilinmiyor; dosya adından işlev varsayımı YAZILMAZ. Ayrı durmaları, iş listesinin kural dosyasını kirletmemesi içindir. İçerik doğrulanmadan bu satır güncellenmez.

**`broken-links-report.md`**
> **eli10 (basit):** Bozuk bağlantıların listelendiği rapor dosyası (içeriği okunmadı).
> **eli15 (detay):** İddia yalnızca dosya adıyla sınırlıdır; içerik okunmadığı için kapsam ve tarih bilinmiyor. Rapor ayrı tutulur ki kural dosyasına kirletmesin. İçerik okunup ölçüldüğünde bu satır güncellenir.

---

## 4. Kurallar

Guardrail numaralarının tam kaynağı: [[CLAUDE]] §7 (16 Hard Guardrails) · bu dokümanın iskeleti için [[.templates/frontend/context-template]] §4.1 (8 madde).

| # | Kural (özet) | Neden var | Ref |
|---|--------------|-----------|-----|
| 1 | Vault tek doğruluk kaynağıdır; uygulama kodu vault'a tabidir | Karar ile kod ayrı yerde dursun | [[../AGENTS]] §2 |
| 2 | Disk kanıtı olmayan dosya/sayı/versiyon adı YAZILMAZ — `UNKNOWN` / `VERIFICATION REQUIRED` | Halüsinasyonu kapatır | context-template §4.1 #2 |
| 3 | Sayı iddiası ölçümle kanıtlanır | Uydurma envanteri önler | context-template §4.1 #3 |
| 4 | Frozen ADR (001-037) metni kopyalanmaz, yalnız wiki-link | Karar metni bozulmaz | context-template §4.1 #4 |
| 5 | Wiki-link formatı (çift köşeli bağlantı) — markdown path linki değil | Link denetimi çalışır | context-template §4.1 #5 |
| 6 | Üretilen dokümanda doldurulmamış placeholder kalmaz | Yarı dolu teslim engellenir | context-template §4.1 #6 |
| 7 | Çelişki: vault ↔ disk → **disk kazanır** + işaretlenir | SSOT korunur | context-template §4.1 #7 |
| 8 | Emoji/dekoratif işaret yasak; yalnız wiki-link referansı | Metin taşınabilir kalır | context-template §4.1 #8 |
| 9 | `.ai/` yazımı yalnız `vault-utf8-writer.mjs` ile; `log.md` yalnız `append` | UTF-8/bozulma önlenir | `.ai/scripts/vault-utf8-writer.mjs` başlığı |
| 10 | `.env.figma` anahtarı hiçbir `.md`/`.json`/`.log`a yazılmaz | Sızıntı engellenir | REDACTED — `.ai/AGENTS.md` §13.9 #3 |
| 11 | Dosya adı değişikliği (taşıma/silme) onaysız yapılmaz | Yüzlerce wiki-link kırılır | Kök [[../AGENTS]] §4 (Faz 1) |

> **eli10 (basit):** Bu kurallar, vault'a yazarken yanlış yapmayı engeller: bilmediğimiz şeyi uydurmayız, sayıyı ölçeriz, kararı bozmayız.
> **eli15 (detay):** Kurallar ayrı tabloda tutulur çünkü kural ile içerik farklı hızda değişir; içeriğe gömülse revizyonda kaybolur. Okunmaları, yazmadan önce sınırları görmeyi sağlar. Yazılmaları, denetimin tekrarlanabilir olmasını garantiler. Kural değişince (yeni guardrail/ADR) yalnız bu tablo ve ilgili şablon güncellenir; envanter sayıları yeni ölçümle değişir.

---

## 5. Workflow

```text
GÖREV → KURAL OKU (kök AGENTS + ilgili .ai dosyası)
  → DISK ÖLÇ (envanter / hedef dosya 1. kez oku) → KARAR (1. okumadan)
  → YAZ (yalnız vault-utf8-writer ile; log.md append)
  → DOĞRULA (verify / wiki-link-check / eli10+eli15 / placeholder)
  → RAPOR (subagent COMMIT ATMAZ — orkestratöre aittir)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | İlgili kural dosyasını oku | Kural listesi | Vault kuralları orada | Guardrail ihlali |
| 2 | Hedef dizini/dosyayı ölç | Sayı + envanter | Sayısız iddia yazılır | Halüsinasyonlu envanter |
| 3 | Şablonu seç (Guardrail #16) | Doğru iskelet | 4 docType ortak iskelet | Tutarsız, denetlenemez doküman |
| 4 | `vault-utf8-writer.mjs` ile yaz | UTF-8, BOM'suz dosya | Tek yazma arayüzü | Mojibake/bozuk encoding |
| 5 | `verify` + link kontrolü | Temiz rapor | Bozuk link/sayfa sessiz kalır | Kırık zincir |
| 6 | `log.md`'ye append | Audit satırı | Geçmiş tek yönlü | Denetim izi kaybolur |
| 7 | Rapor + commit yok | Teslim raporu | Yetki sınırı | Yetkisiz commit, revert riski |

> **eli10 (basit):** İş sırası basit: önce kural, sonra ölç, sonra yaz, sonra kontrol et ve kaydettiğin yerde bırak.
> **eli15 (detay):** Adımlar ayrı tutuldu çünkü sıralama hatası doğrudan veri kaybı üretir; ölçmeden yazılan envanter ileride yanlış karar besler. Kapılar (ölçüm, doğrulama, kayıt) atlanırsa hata ancak sonradan fark edilir. Commit yetkisi orkestratörde bırakılmıştır ki tek kalemli tarih oluşsun. Adım değişirse bu tablo güncellenir, kod değil.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada doldurulmamış placeholder kalmadı |
| 4 | Envanter | Kök **20 `.md`** · **17 dizin** · **804 dosya / 572 md / 107 dizin** (2026-10-10) — ölçümle eşit |
| 5 | Çelişki | §3.5 tablosu eksiksiz; disk kazanır işareti var |
| 6 | Wiki-link | Wiki-link formatı (çift köşeli); hedefler diskte mevcut |
| 7 | eli10 + eli15 | §1–§5 maddelerinin altında `> **eli10 (basit):**` + `> **eli15 (detay):**` bloğu var; eli10 ≤2 cümle, eli15 3-4 cümle |
| 8 | İçerik neden ayrı | §3.6 tablosu 5 satır (bakım · tek sorumluluk · token tek kaynak · cihaz izolasyonu · vendor karantinası) |
| 9 | Halüsinasyon | Diskte olmayan dosya/sayı iddia edilmedi; okunmayan içerik `VERIFICATION REQUIRED` |
| 10 | Dokunulmaz | `CLAUDE.md` / `AGENTS.md` / `WORKFLOW.md` değiştirilmedi; commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Vault anayasası | [[CLAUDE]] | 16 Hard Guardrails, boot protokolü |
| Agent registry | [[AGENTS]] | Routing, handover, escalation (SSOT) |
| Vault süreç | [[WORKFLOW]] | Fazlar, kapılar, ADR lifecycle |
| Master katalog | [[index]] | Dosya/ADR/envanter indeksi |
| Retrieval indeksi | [[RAG]] | Konu→dosya eşlemesi + pipeline (PLANNED — ADR-030) |
| Sürekli güncelleme döngüsü | [[SYNC]] | Hedef set 26 · üç kapı · tazelik ölçütü (2026-10-10) |
| Kök master kurallar | [[../AGENTS]] | §3 Zero-Hallucination · §4 keşif · §7 loop |
| Context şablonu | [[.templates/frontend/context-template]] | Bu dokümanın iskeleti (Guardrail #16) |
| Şablon registry | [[.templates/index]] | Şablon envanteri |
| Alt registry | [[.agents/AGENTS]] | 11 agent profili indeksi |
| Karar arşivi | [[.decisions/index]] | ADR listesi (frozen dahil) |
| Mockup indeksi | [[ui-design/01-mockup-index]] | Mockup Before Frontend kapısı |
| Katman mimarisi | [[architecture/00-master-index]] | K000-K020 dokümanları (2026-10-10: 21 K-dizini · 125 md) |
| Yazım aracı | `.ai/scripts/vault-utf8-writer.mjs` | UTF-8 tek yazma arayüzü |
| Link denetimi | `.ai/scripts/wiki-link-check.ps1` | Wiki-link doğrulama (kökten çalıştırılır) |
| Disk kanıtı | `.ai/` gerçek envanteri | §3 tüm sayıları |

---

**Template Version:** 1.2.0 · **Şablon:** `.ai/.templates/frontend/context-template.md` (+ `documentation/context-md-template.md`, 2026-10-07)
**Last Updated:** 2026-10-10 · **docType:** context
