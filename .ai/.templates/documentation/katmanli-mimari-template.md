---
title: "CoreMusic — Katmanlı Mimari Şablonu"
type: template
category: template
date: 2026-10-10
updated: 2026-10-10
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Katmanlı Mimari Şablonu

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[AGENTS.md]] ·
[[../architecture/00-master-index]] · [[../architecture/katman-baglilik-matrisi]] ·
[[katman-readme-template]] · [[alt-katman-template]] · [[mimari-detay-template]] · [[../adr/adr-template]]

> **Kullanım:** Katmanlı mimari (layered architecture) anlatısı gerektiren her vault dosyası
> bu şablondan üretilir: `.ai/architecture/**` mimari raporları, katman tanım belgeleri,
> katman-ötesi (cross-layer) tasarım notları ve ADR-0xx gerekçe ekleri.
> **Şablonsuz katmanlı-mimari dosyası üretilmez (Guardrail #16).**
> Şablon **≥500 satır** sözleşmesindedir; üretilen dosya da ≥500 satır hedefler
> (istisna: tek düğüm README/alt-katman — bkz. ilgili şablonlar).

**Dosya:** `.ai/.templates/documentation/katmanli-mimari-template.md` · **Hedef tip:** Markdown mimari belge

---

## §1 Amaç

Bu şablon, CoreMusic katmanlı mimarisinin (K000–K020 çekirdek + bant genişletmeleri)
**tanımını, bağlamını, içerik malzemesini ve kolon (tablo) yapısını** tek biçimde sabitler.
Katmanlı mimari anlatısı dağınık yazılırsa bağımlılık denetimi imkânsızlaşır; bu yüzden
bağlam (context), tanım (definition), içerik (content) ve kolon (column) dördü tek iskelette toplanır.

| Alan | Değer |
|------|-------|
| Template Name | `katmanli-mimari-template.md` |
| Template Path | `.ai/.templates/documentation/katmanli-mimari-template.md` |
| Hedef Dosya Tipi | Katmanlı mimari belgesi (layered architecture document) |
| Adlandırma | `<konu>-katmanli-mimari.md` (slug İngilizce/Türkçe ASCII uyumlu) |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Vault Steward (yazar + onay) |
| Onaylayanlar | Vault Steward ✅ → Arch Lead ⏳ |
| Üretici Agent'lar | `architect` · `backend-architect` · `developer` · `explore` (kanıt) |
| İskelet | H1 + künye + §1 Amaç → §7 Referanslar (7 bölüm) |
| Zorunlu Bölüm | §1.3 Web araştırması raporu (gerçek sonuçlarla doldurulur) |
| Zorunlu İçerik | Context (§3.3) · Tanım (§1.1 + §3.4) · İçerik malzemesi (§3.5) · Kolonlar (§3.6) |
| Min Satır | **500** (şablon ve üretilecek belge için) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK) |
| Governance | Red Team · Human Mode · Truth Mode |

### §1.1 Tanımlar Sözlüğü (Zorunlu — şablonda serbest bırakılmaz)

| Terim | Tanım | Kaynak |
|-------|-------|--------|
| **Katman (layer)** | Tek sorumluluğa sahip yatay bölme; yalnız tanımlı arayüzle komşusuyla konuşur | UQ CSSE6400 Layered Architecture slaytları |
| **Katman (K{n})** | CoreMusic'te K000–K020 çekirdek dizin kimliği (`K000-isletim-sistemi` … `K020-uretim`) | `.ai/architecture/` disk kanıtı |
| **Tier (kademe)** | Fiziksel/dağıtık dağıtım bölmesi (client/server); katmanla **eşanlamlı DEĞİLDİR** | RUP Layering Strategies |
| **Bağımlılık kuralı (dependency rule)** | Bağımlılıklar yalnızca aşağı (veya içeri) doğru akar; üst katman alt katmanı asla geri çağırmaz | R. C. Martin, Clean Architecture |
| **Komşu iletişim** | Bir katman, yalnızca doğrudan altındaki (ve gerektiğinde üstündeki) komşuyla iletişim kurar | UQ slaytları — Neighbour Communication |
| **Yukarı bildirim (upward notification)** | Alt katman üst katmanı yalnız genel arayüz / callback / event ile bilgilendirir | UQ slaytları — Upward Notification |
| **Katman izolasyonu** | Katmanlar birbirinin uygulama detayını bilmez; yalnız sözleşmeli arayüzle bağlanır | UQ slaytları — Layer Isolation |
| **Relaxed layering** | Primitif servisler (log, msg) için komşu-atlama izni; istisna kayıtlı olmalı | PosoMAS Guideline: Layering |
| **Strateji (layering strategy)** | Sorumluluk-temelli / yeniden-kullanım-temelli / çok-boyutlu katmanlama seçimi | RUP Layering Strategies |
| **Sorumluluk-temelli katman** | Sunum / iş kuralı / veri erişim sorumluluklarının katmanlara bölünmesi | RUP · UMD CMSC389A |
| **Yeniden-kullanım-temelli katman** | Base → Business-Specific → Application-Specific yeniden kullanım sarmalayıcıları | RUP Layering Strategies |
| **arc42** | 12 bölümlük pratik mimari dokümantasyon şablonu (bölüm 5 Bina Bloğu = katman görünümü) | arc42.org · docs.arc42.org |
| **Bina bloğu görünümü (building block view)** | Statik ayrıştırma + bağımlılıklar; zorunlu mimari bölüm | arc42 §5 |
| **Boundary (sınır)** | Katman-ötesi geçişte taşınan basit veri yapısı (DTO); alt katman nesnesi/row'u taşınmaz | Clean Architecture |
| **Vokabüler sızıntısı** | Üst katmanın dilinin (HTTP, SQL) alt katmana girmesi = katman ihlali bulgusu | UMD CMSC389A |
| **Pass-through disease** | Her metot yalnız ileten katmanlar = katman ritüeli, maliyet (kural ihlali sinyali) | UMD CMSC389A |

### §1.2 Context (Bağlam) Tanımı — bu şablon neden var?

CoreMusic vault'u katmanlı mimariyi **üç ayrı biçimde** belgeliyor: (1) `.ai/architecture/`
altındaki K{n} dizin ağacı + README/alt-katman dosyaları, (2) `.ai/.decisions/` altındaki ADR
gerekçeleri, (3) `.ai/reports/` altındaki genişletilmiş raporlar. Bu üç biçim aynı **bağlam /
tanım / içerik / kolon** dörtlüsünü taşımadığında katman ihlali denetimi (matris kontrolü)
yapılamıyor. Şablon bu dörtlüyü zorunlu kılar:

| Katman | Ne taşır | Şablondaki karşılığı |
|--------|----------|----------------------|
| Context (Bağlam) | Neden katmanlı? Hangi kalite hedefi? Hangi dış kısıt? | §3.3 + §1.2 |
| Definition (Tanım) | Katman nedir, komşu nedir, bağımlılık kuralı nedir | §1.1 + §3.4 |
| Content (İçerik malzemesi) | Katmanın bileşenleri, arayüzleri, veri/güvenlik sınırı | §3.5 |
| Columns (Kolonlar) | 16-alanlı satır tablosu (matris uyumu) | §3.6 |

### §1.3 Web Araştırması Raporu & Kaynakları (Zorunlu — gerçek sonuçlarla doldurulur)

| Alan | Değer |
|------|-------|
| Web Search **Query** | `{{WEB_SEARCH_QUERY}}` |
| Web Search **Konusu** | `{{WEB_SEARCH_TOPIC}}` |
| Web Search **Bağlam** | `{{WEB_SEARCH_CONTEXT}}` |
| Web Search **Sonucu** | `{{WEB_SEARCH_RESULT}}` |
| Web Search **Alınan Karar** | `{{WEB_SEARCH_DECISION}}` |
| Web Search **Sonuç** | `{{WEB_SEARCH_CONCLUSION}}` |
| Tarih | `{{DATE}}` |
| Erişilen kaynak sayısı | `{{SOURCE_COUNT}}` |
| Durum | `{{RESEARCH_STATUS}}` (ok / kısmi / ⚠️ VERIFICATION REQUIRED) |

**Kaynak tablosu (şablon üretiminde kullanılan gerçek kaynaklar — 2026-10-10 turu):**

| # | Kaynak | URL | Şablona Katkısı |
|---|--------|-----|-----------------|
| 1 | UMD CMSC389A — 10 Layered Architecture | https://www.cs.umd.edu/class/fall2026/cmsc389A/Layered_Architecture.html | 4 kanonik katman, bağımlılık kuralı, vokabüler sızıntısı bulguları |
| 2 | R. C. Martin — The Clean Architecture Dependency Rule | https://www.informit.com/articles/article.aspx?p=2832399 | İçe-akış bağımlılık kuralı, sınırda DTO kuralı |
| 3 | arc42 Template Overview | https://arc42.org/overview/ | 12 bölüm, §5 Building Block View zorunluluğu |
| 4 | arc42 Docs — Section 5 Building Block View | https://docs.arc42.org/section-5/ | White/black box, L1-L3 kademelenme, katman/tier tanımı |
| 5 | arc42 Docs — Section 7 Deployment View | https://docs.arc42.org/section-7/ | Altyapı + blok eşlemesi (tier ile katman ayrımı) |
| 6 | UQ CSSE6400 — Layered Architecture (R. Thomas) | https://csse6400.uqcloud.net/slides/layered.pdf | 5 ilke: izolasyon, komşu iletişim, aşağı akış, yukarı bildirim, sidecar |
| 7 | PosoMAS — Guideline: Layering | https://posomas.isse.de/PosoMAS/core.tech.common.extend_supp/guidances/guidelines/layering_F169CF07.html | Katman sayısı (3 tipik, 5-7 karmaşık, >10 şüpheli), relaxed layering |
| 8 | RUP — Layering Strategies (tp199) | https://files.defcon.no/RUP/papers/pdf/tp199_layering_strategies.pdf | Sorumluluk-temelli / yeniden-kullanım-temelli / çok-boyutlu stratejiler, DAG |
| 9 | JOT 2024 — Specifying and Composing Layered Architectures | https://www.jot.fm/issues/issue_2024_01/article2.pdf | Biçimsel katman tanımı: sağlanan/gerektirilen servis, kompozisyon |
| 10 | arc42 şablon deposu | https://github.com/arc42/arc42-template/ | Şablon sürümleme/ölçeklenebilir yapının referansı |

> **Kaynak politikası:** Web'den gelen hiçbir teknik iddia proje dosyasına **olgu** olarak
> yazılmaz; yalnız yöntem/iskelet olarak kullanılır. Proje olgusu = disk kanıtı (glob/grep/read).

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Guardrail #16 | Şablonsuz katmanlı-mimari dosyası üretilmez |
| Disk kanıtı | K kimliği/id/ADR numarası yalnız glob/grep ile doğrulanır; uydurma K adı YASAK |
| Matris uyumu | Katman satırı §3.6 16 kolonunu aynen taşır (`.ai/architecture/katman-baglilik-matrisi`) |
| Uzunluk | Üretilen belge ≥500 satır; uzun içerik EK'e taşınır |
| Dil | Türkçe; mojibake YASAK; slug/id ASCII |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Katmanlı mimari belgesinin iskeleti (§1-§7) | Tek katman README'si (→ `[[katman-readme-template]]`) |
| Context / Tanım / İçerik / Kolon dörtlüsünün zorunluluğu | Tek alt katman md'si (→ `[[alt-katman-template]]`) |
| §1.3 web araştırması raporu (gerçek kaynaklarla) | ADR metni (→ `[[../adr/adr-template]]`) |
| 16 kolonlu katman satır formatı (matris uyumu) | Rapor genişletmesi ≥5.000 satır (→ `.ai/reports/` kontratı) |
| Bağımlılık kuralı denetim listesi | CSS/HTML katman sırası (→ frontend CSS şablonları) |
| Katman sayısı politikası (3 / 5-7 / >10 şüpheli) | UI mockup / ekran akışı (→ ui-design şablonları) |
| Türkçe doğruluk + mojibake denetimi | `git commit` (orkestratöre aittir) |

### §2.1 Dosya Tipi → Sorumlu Eşlemesi

| Dosya Tipi | Sorumlu | Bu Şablondan mı? |
|------------|---------|-------------------|
| `.ai/architecture/**/*katmanli*.md` | Vault Steward + `architect` | ✅ (bu şablon) |
| `.ai/architecture/k{n}-<slug>/README.md` | `architect` | ❌ → `[[katman-readme-template]]` |
| `.ai/architecture/k{n}-<slug>/<konu>.md` | `architect` | ❌ → `[[alt-katman-template]]` |
| `.ai/architecture/**/*.md` (README + detail) | `architect` | ❌ → `[[mimari-detay-template]]` |
| `.ai/.decisions/ADR-NNN-*.md` | Vault Steward | ❌ → `[[../adr/adr-template]]` |
| `.ai/reports/*.md` | Vault Steward | ❌ rapor kontratı (§10-§11) |
| `.ai/.templates/index.md` | Vault Steward | ❌ kayıt satırı (envanter SRP) |

### §2.2 Kapsam Sınırı Testi — bu şablon kullanılır mı?

| # | Soru | Evet ise |
|---|------|----------|
| 1 | Dosya bir **veya daha fazla katmanı** tanım/hak gerekçesiyle anlatıyor mu? | BU ŞABLON |
| 2 | Bağımlılık okları / matris satırı üretiliyor mu? | BU ŞABLON |
| 3 | Katman-ötesi (cross-cutting) konu işleniyor mu (log, security, events)? | BU ŞABLON |
| 4 | Tek katman README'si mi? | `[[katman-readme-template]]` |
| 5 | Tek düğüm (K{n}.a.b) mi? | `[[alt-katman-template]]` |
| 6 | Geri döndürülmesi pahalı teknoloji kararı mı? | `[[../adr/adr-template]]` |

---

## §3 Mimari (İskelet)

### §3.1 Frontmatter + Başlık (birebir korunur)

````markdown
---
title: "CoreMusic — <Konu> Katmanlı Mimari"
type: architecture-layered
category: architecture
date: {{DATE}}
updated: {{DATE}}
version: 1.0.0
status: draft
authority: "SSOT: bu dosyanın konusu — registry: .ai/.templates/index.md"
governance: Red Team · Human Mode · Truth Mode
depends-on: ["<gerçek disk kanıtı dosyaları>"]
---

# <Konu> — Katmanlı Mimari

**Künye:** <K aralığı / bant> · Durum: draft · Yazar: {{AUTHOR}} · Tarih: {{DATE}}
**İlgili ADR:** [[../decisions/...]] (varsa; yoksa "YOK — ⚠️ VERIFICATION REQUIRED")
**İlgili matris:** [[../architecture/katman-baglilik-matrisi]]

---

## §1 Amaç
## §2 Kapsam
## §3 Bağlam, Tanım, İçerik, Kolonlar
## §4 Kurallar
## §5 Workflow
## §6 Doğrulama
## §7 Referanslar
````

### §3.2 § Başlık Şeması (DRY — her üretilen dosyada birebir aynı)

| Bölüm | Zorunlu Alt Bölüm | İçerik Türü |
|-------|-------------------|-------------|
| §1 Amaç | §1.1 Tanımlar · §1.2 Context · §1.3 Web araştırması · §1.4 Kısıtlamalar | Tanım + kaynak |
| §2 Kapsam | §2.1 Sorumlu eşlemesi · §2.2 Sınır testi | Kolon |
| §3 Bağlam/Tanım/İçerik/Kolon | §3.1 Model · §3.2 Bağım. kuralı · §3.3 Context · §3.4 Katman tanımı · §3.5 İçerik malzemesi · §3.6 16 kolon | Kolon + içerik |
| §4 Kurallar | §4.1 Bağımlılık · §4.2 Tier≠Katman · §4.3 Red sinyalleri | Kural |
| §5 Workflow | Adım tablosu + ASCII akış | Süreç |
| §6 Doğrulama | 19 kontrol + aşama + read-only komutlar | Kapı |
| §7 Referanslar | Wiki-link tablosu | Bağlantı |

### §3.3 Context (Bağlam) Bölümü — doldurma kuralı

Context üç soruyu cevaplar; biri boşsa dosya yayımlanmaz:

| # | Soru | Placeholder |
|---|------|-------------|
| 1 | Bu katman modeli **neden** var? Hangi kalite hedefini karşılıyor? | `{{CONTEXT_WHY}}` |
| 2 | **Mevcut durum** nedir (disk kanıtıyla)? | `{{CURRENT_STATE}}` |
| 3 | **Sorun tanımı** nedir (çözülmezse ne olur)? | `{{PROBLEM_STATEMENT}}` |

**Context yazım biçimi (örnek — üretilen dosyada bu ton):**

> Sistem, `.ai/architecture/` altında K000–K020 çekirdek katmanlarına ayrılmıştır; her katman
> tek sorumluluk taşır ve yalnız izinli bağımlılık oklarını kullanır. Mevcut durum glob ile
> doğrulanır; sayı/id uydurulmaz. Sorun: bağımlılık oku matristen sapan bir dosya, katman
> ihlali denetimini kırar — bu yüzden her katman belgesi aynı kolon setini taşır.

### §3.4 Katman Tanımı Tablosu (İçerik malzemesi — 1. parça)

| Katman | Kanonik ad | Sorumluluk (1 cümle) | İzinli bağımlılık | Yasak bağımlılık |
|--------|-----------|----------------------|-------------------|------------------|
| `K000` | `isletim-sistemi` | `{{K000_RESP}}` | `{{K000_ALLOW}}` | `{{K000_DENY}}` |
| `K001` | `donanim` | `{{K001_RESP}}` | `{{K001_ALLOW}}` | `{{K001_DENY}}` |
| `K002` | `surucu` | `{{K002_RESP}}` | `{{K002_ALLOW}}` | `{{K002_DENY}}` |
| `K003` | `ses-motoru` | `{{K003_RESP}}` | `{{K003_ALLOW}}` | `{{K003_DENY}}` |
| `K004` | `yapay-zeka` | `{{K004_RESP}}` | `{{K004_ALLOW}}` | `{{K004_DENY}}` |
| `K005` | `veri-yonetimi` | `{{K005_RESP}}` | `{{K005_ALLOW}}` | `{{K005_DENY}}` |
| `K006` | `guvenlik` | `{{K006_RESP}}` | `{{K006_ALLOW}}` | `{{K006_DENY}}` |
| `K007` | `middleware` | `{{K007_RESP}}` | `{{K007_ALLOW}}` | `{{K007_DENY}}` |
| `K008` | `servisler` | `{{K008_RESP}}` | `{{K008_ALLOW}}` | `{{K008_DENY}}` |
| `K009` | `api` | `{{K009_RESP}}` | `{{K009_ALLOW}}` | `{{K009_DENY}}` |
| `K010` | `uygulama` | `{{K010_RESP}}` | `{{K010_ALLOW}}` | `{{K010_DENY}}` |
| `K011` | `ux` | `{{K011_RESP}}` | `{{K011_ALLOW}}` | `{{K011_DENY}}` |
| `K012` | `izleme` | `{{K012_RESP}}` | `{{K012_ALLOW}}` | `{{K012_DENY}}` |
| `K013` | `cicd` | `{{K013_RESP}}` | `{{K013_ALLOW}}` | `{{K013_DENY}}` |
| `K014` | `ag` | `{{K014_RESP}}` | `{{K014_ALLOW}}` | `{{K014_DENY}}` |
| `K015` | `medya` | `{{K015_RESP}}` | `{{K015_ALLOW}}` | `{{K015_DENY}}` |
| `K016` | `amplifikator` | `{{K016_RESP}}` | `{{K016_ALLOW}}` | `{{K016_DENY}}` |
| `K017` | `guc-kaynagi` | `{{K017_RESP}}` | `{{K017_ALLOW}}` | `{{K017_DENY}}` |
| `K018` | `termal` | `{{K018_RESP}}` | `{{K018_ALLOW}}` | `{{K018_DENY}}` |
| `K019` | `pcb` | `{{K019_RESP}}` | `{{K019_ALLOW}}` | `{{K019_DENY}}` |
| `K020` | `uretim` | `{{K020_RESP}}` | `{{K020_ALLOW}}` | `{{K020_DENY}}` |

> **Kanal kuralı:** K adları **yalnız diskten** okunur (`.ai/architecture/K*` listesi);
> tablodaki `{{...}}` alanları glob çıktısıyla doldurulur. Eksik K → satır `UNKNOWN` yazılır.

### §3.5 İçerik Malzemesi (2. parça) — katman başına zorunlu alanlar

| Alan | Açıklama | Placeholder |
|------|----------|-------------|
| Sorumluluk | Katmanın tek cümlelik sahipliği | `{{LAYER_RESPONSIBILITY}}` |
| Girdi | Hangi tetikleyici/servis gelir | `{{LAYER_INPUT}}` |
| Çıktı | Hangi servis/olay çıkışı üretilir | `{{LAYER_OUTPUT}}` |
| İzinli bağımlılık | Matristeki oklar | `{{LAYER_ALLOW}}` |
| Yasak bağımlılık | Matristeki yasaklar (ihlal = red) | `{{LAYER_DENY}}` |
| Veri sınırı | Hangi veri bu katmandan dışarı çıkmaz | `{{DATA_BOUNDARY}}` |
| Güvenlik sınırı | Auth/CSRF/CSP sorumluluğu var mı | `{{SECURITY_BOUNDARY}}` |
| Hata kipi | Çökerse davranış (failure mode) | `{{FAILURE_MODE}}` |
| Gözlemlenebilirlik | Log/metrik/iz alanı | `{{OBSERVABILITY}}` |
| Test | Hangi test katmanı bu katmanı kapsar | `{{TEST_LAYER}}` |
| Kanıt | Dosya yolu + glob çıktısı | `{{EVIDENCE}}` |

### §3.6 16 Kolonlu Katman Satırı (Matris uyumu — silinemez)

```
K-ID | KANONİK AD | TEATRAL EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI |
İZİNLİ BAĞIMLILIK | YASAK BAĞIMLILIK | DATA BOUNDARY | SECURITY BOUNDARY |
FAILURE MODE | OBSERVABILITY | TEST | KANIT
```

| # | Kolon | Kural |
|---|-------|-------|
| 1 | `K-ID` | `K{nnn}` — diskte mevcut olmalı |
| 2 | `KANONİK AD` | dizin slug'ı birebir |
| 3 | `TEATRAL EPİTET` | varsa vault'taki; yoksa `—` |
| 4 | `DOMAIN` | alan etiketi (diskte yoksa `UNKNOWN`) |
| 5 | `RUNTIME` | çalıştığı ortam/süreç |
| 6 | `SORUMLULUK` | tek cümle |
| 7 | `GİRDİ` | tetikleyici |
| 8 | `ÇIKTI` | servis/olay |
| 9 | `İZİNLİ BAĞIMLILIK` | matristeki ok |
| 10 | `YASAK BAĞIMLILIK` | matristeki yasak |
| 11 | `DATA BOUNDARY` | veri dışarı çıkmaz kuralı |
| 12 | `SECURITY BOUNDARY` | auth/CSRF/CSP sorumluluğu |
| 13 | `FAILURE MODE` | çökme davranışı |
| 14 | `OBSERVABILITY` | log/metric/trace |
| 15 | `TEST` | kapsayan test katmanı |
| 16 | `KANIT` | gerçek dosya yolu + ölçümdür |

### §3.7 Bağımlılık Kuralı Matrisi Şablonu (§4.1'in görseli)

```
        ↓ yalnız aşağı (veya içeri) akar
  +--------------------------------------------------+
  | K011 ux / K010 uygulama   (sunum)                |
  +--------------------------------------------------+
  | K009 api / K008 servisler (iş kuralı)            |
  +--------------------------------------------------+
  | K005 veri / K007 middleware (erişim)             |
  +--------------------------------------------------+
  | K000 os / K001 donanim / K002 surucu  (altyapı)  |
  +--------------------------------------------------+
   ↑ yalnız genel arayüz / callback / event ile bildirim
   × komşu-atlama (relaxed layering) → yalnız kayıtlı istisna
```

---

## §4 Kurallar

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Yeni katmanlı-mimari dosyası bu şablondan üretilir; §1-§7 iskeleti silinemez | Zorunlu | Guardrail #16 ihlali |
| 2 | §1.1 tanımları ve §3.6 16 kolonu aynen korunur; kolon eklenip çıkarılmaz | Zorunlu | Matris uyumsuzluğu |
| 3 | §1.3 web araştırması tablosu gerçek sonuçlarla doldurulur; kaynak URL'siz bölüm yayımlanamaz | Zorunlu | Gerekçesiz belge |
| 4 | Context (§3.3) üç soruyu da cevaplar (neden / mevcut durum / sorun) | Zorunlu | Bağlam boşluğu |
| 5 | K kimlikleri, ADR numaraları, dosya yolları yalnız disk kanıtıyla yazılır | Zorunlu | Hallucination |
| 6 | Bilinmeyen bilgi `UNKNOWN`; doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` | Zorunlu | Uydurma olgu |
| 7 | Bağımlılık okları `.ai/architecture/katman-baglilik-matrisi` kanoniktir; bu şablonda yeniden tanımlanmaz (SRP) | Zorunlu | İkincil kaynak çelişkisi |
| 8 | Katman ≠ tier; dağıtım/kademe anlatısı ayrı bölümde etiketlenir | Zorunlu | Kavram karışıklığı |
| 9 | Üretilen dosya ≥500 satır; uzun tablo/liste EK'e taşınır | Zorunlu | Yarım belge |
| 10 | Web'den gelen teknik iddia proje olgusu olarak yazılmaz (yöntem/iskelet olarak kullanılır) | Yasak | Kaynak karışımı |
| 11 | Envanter kaydı `.ai/.templates/index.md` (SRP) — bu şablon envanter listelemez | Yasak | İkincil kaynak çelişkisi |
| 12 | Routing/onay değerleri `.ai/AGENTS.md` (SSOT) dosyasından okunur (DIP) | Zorunlu | SSOT çelişkisi |
| 13 | § başlıkları kategori standardıdır: §1 Amaç → §7 Referanslar (DRY) | Zorunlu | Şablon tutarsızlığı |
| 14 | Türkçe karakterler doğru; mojibake YASAK | Zorunlu | Mojibake → onarım |
| 15 | Secret/credential hiçbir koşulda yazılmaz (REDACTED) | Yasak | Güvenlik ihlali |
| 16 | `git commit` bu şablonu uygulayan subagent tarafından ATILMAZ (orkestratöre aittir) | Yasak | Erken commit |
| 17 | Uydurma wiki-link/kırık link yazılmaz; link yalnız diskte varsa eklenir | Zorunlu | Kırık link |
| 18 | Frozen ADR/vault metni okunur, referanslanır; düzenlenmez | Yasak | Frozen ihlali |
| 19 | **Hardcoded dosya seti yasak:** klasöre `adr/kapsam/riskler/senaryolar/performans/test-plani/yasam-dongusu/entegrasyon/olcum-kriterleri/bilinmeyenler/arayuz` sabit 11 dosyası toplu üretilmez — dosya yalnız **ihtiyaç anında** ve tek tek, içerik varsa üretilir; yoksa dosya açılmaz | Yasak | Kütlesel (mass) üretim ihlali → sil + log |
| 20 | Dizin/dosya adı yazım denetimi: `electroncis` (elektronik), `andorid` (android), `donanim-circuits` gibi bozuk/typo ad üretilmez; ad diskteki kanonik slug'la birebir olur | Yasak | Bozuk ad → yeniden adlandır (onayla) |

### §4.1 Bağımlılık Kuralı Denetimi (§6'daki kapının gerekçesi)

| # | Denetlenecek | İhlal bulgusu |
|---|--------------|---------------|
| 1 | Üst katman alt katmanı import ediyor mu? (izinli ok) | Matriste olmayan ok |
| 2 | Alt katman üst katmanın dilini taşıyor mu? (HTTP/SQL sızıntısı) | Vokabüler sızıntısı |
| 3 | Geçişte basit veri yapısı mı taşınıyor (DTO)? | Alt katman nesnesi/row'u |
| 4 | Katmanlar klasörle mi "var", bağımlılık hâlâ düğümüş mü? | Klasör = mimari yanılgısı |
| 5 | Her metot yalnız mı iletiyor? | Pass-through disease |
| 6 | Katman sayısı makul mü (3 / 5-7; >10 şüpheli)? | Aşırı katmanlama |

### §4.2 Tier ≠ Katman Ayrım Çizgisi

| Soru | Cevap ise | Bölüm |
|------|-----------|-------|
| Fiziksel dağıtım / süreç / makine mi? | tier (kademe) | Deployment notu |
| Kod sorumluluğu / bağımlılık yönü mü? | katman | §3.4-§3.7 |
| İkisi birden mi? | iki ayrı tablo | İkisi de yazılır, birleştirilmez |

### §4.3 Red Sinyalleri (Otomatik DUR)

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| §1.3 web araştırması boş | Gerekçesiz belge | DUR — kaynak tablosunu doldur |
| Context üç sorudan biri boş | Bağlam eksik | DUR — §3.3'ü tamamla |
| §3.6 16 kolon eksik | Matris kırılır | DUR — kolonları tamamla |
| Disk kanıtsız K/ADR iddiası | Hallucination | DUR — glob ile doğrula |
| Placeholder `{{...}}` kalmış | Yarım belge | DUR — doldur |
| Matristen farklı bağımlılık oku | SSOT çelişkisi | DUR — matristen oku |

---

## §5 Workflow

```
KANONİK ŞABLONU SEÇ → KOPYALA → CONTEXT+TANIM DOLDUR → İÇERİK+KOLON DOLDUR
→ WEB ARAŞTIRMASI (kaynak URL) → GUARDRAIL #16 DOĞRULA → REGISTRY KAYDI → COMMIT (orkestratör)
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/documentation/katmanli-mimari-template.md` | Şablon kopyası |
| 2 | KOPYALA | `<konu>-katmanli-mimari.md` adıyla hedefe kopyala | Dosya iskeleti |
| 3 | CONTEXT | §3.3 üç soru + §1.2 bağlam tablosu dolu | Bağlam bölümü |
| 4 | TANIM | §1.1 sözlük + §3.4 katman tablosu (diskten K listesi) | Tanım bölümü |
| 5 | İÇERİK | §3.5 11 alan + §3.6 16 kolon (matrisle hizalı) | İçerik + kolonlar |
| 6 | ARAŞTIRMA | §1.3 iki tablo (sorgu + kaynak URL) gerçek sonuçlarla | Web raporu |
| 7 | DOĞRULA | §6 19 kontrol + §4.3 red sinyalleri | 19/19 gate |
| 8 | REGISTRY | `.ai/.templates/index.md` + ilgili sayfaya kayıt satırı | Vault senkronu |
| 9 | COMMIT | Orkestratör yapar (subagent ATMAZ) | Git geçmişi |

**Adım 6 detayı — araştırma sırası:** (a) sorgu yaz → (b) ≥3 bağımsız kaynak getir →
(c) her kaynağa URL + tek cümle katkısını yaz → (d) yöntemi şablona, olguyu diske ayır →
(e) doğrulanamayan iddiayı `⚠️ VERIFICATION REQUIRED` bırak.

---

## §6 Doğrulama

Dosya commit edilmeden önce 19 kontrol sırayla yapılır; tek madde bile ❌ ise commit durur.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | §1-§7 iskeleti eksiksiz mi? | 7 bölüm silinmemiş | ✅/❌ |
| 3 | §1.1 tanımlar sözlüğü dolu mu? | ≥12 terim + kaynak | ✅/❌ |
| 4 | §1.3 web araştırması iki tablo dolu mu? | Query tablosu + ≥3 kaynak URL | ✅/❌ |
| 5 | §1.4 kısıtlamalar ≥2 satır mı? | Kısıt + Açıklama | ✅/❌ |
| 6 | §2 kapsam/kapsam dışı tablosu var mı? | İki sütun dolu | ✅/❌ |
| 7 | §2.2 sınır testi ≥4 satır mı? | Soru + Yön | ✅/❌ |
| 8 | §3.3 Context üç soruyu cevaplıyor mu? | Neden / Mevcut durum / Sorun | ✅/❌ |
| 9 | §3.4 katman tablosu diskten mi? | K listesi glob ile | ✅/❌ |
| 10 | §3.5 içerik malzemesi 11 alan mı? | Sorumluluk…Kanıt | ✅/❌ |
| 11 | §3.6 16 kolon aynen var mı? | K-ID…KANIT | ✅/❌ |
| 12 | §3.7 bağımlılık matrisi ASCII var mı? | Aşağı ok + yukarı bildirim + yasak | ✅/❌ |
| 13 | §4 kural tablosu ≥15 satır mı? | Kural + Tür + İhlal | ✅/❌ |
| 14 | §4.3 red sinyalleri ≥5 satır mı? | Red + Neden + Aksiyon | ✅/❌ |
| 15 | §5 workflow ≥8 adım mı? | Adım + Eylem + Çıktı | ✅/❌ |
| 16 | §6 kontrol listesi 19 satır mı? | 19 madde | ✅/❌ |
| 17 | Satır sayısı ≥500 mü? | Ölçüm | ✅/❌ |
| 18 | Placeholder kalmadı mı (`grep '{{'`)? | 0 | ✅/❌ |
| 19 | Türkçe + mojibake + secret taraması temiz mi? | 0/0/0 | ✅/❌ |

### §6.1 Doğrulama Aşamaları

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2, 16, 17 | Frontmatter + iskelet + satır |
| B | Bağlam & Tanım | 3, 8, 9 | Context + sözlük + K tablosu |
| C | İçerik & Kolon | 10, 11, 12 | 11 alan + 16 kolon + matris |
| D | Kaynak | 4, 18, 19 | Web kaynakları + placeholder + UTF-8 |
| E | Kural & Süreç | 5, 6, 7, 13, 14, 15 | Kapsam + kural + workflow |

### §6.2 Read-Only Doğrulama Komutları (repo kökünden)

```bash
# 1) Satır sayısı — ≥500 olmalı
wc -l <DOSYA>

# 2) Placeholder kontrolü — 0 olmalı
grep -c '{{' <DOSYA>

# 3) K listesi diskten mi — gerçek dizinler görünmeli
ls .ai/architecture/

# 4) Boş sekme (tab) / mojibake taraması — 0 olmalı
grep -nP '\t|\xEF\xBF\xBD' <DOSYA>
```

*(`<DOSYA>` gerçek dosya yolu ile değiştirilir; komutlar yazma yapmaz — sonuç ✅ değilse commit durur.*

### §6.3 Red Sinyalleri (Otomatik DUR — §4.3 tekrarı değildir, kapı uygulamasıdır)

| Kapı | Koşul | Aksiyon |
|------|-------|---------|
| KAPI-1 | §1.3 kaynak <3 | DUR — araştırma turu |
| KAPI-2 | §3.3 eksik soru | DUR — context yaz |
| KAPI-3 | §3.6 kolon ≠16 | DUR — kolon say |
| KAPI-4 | satır <500 | DUR — EK'e böl/depolanmış içerik getir |
| KAPI-5 | disk kanıtsız iddia | DUR — glob ile kanıtla |

---

### §6.4 Kılavuz — Dolu Örnek Katman Satırı (format referansı, üretilen dosyaya örnek)

```
K-ID: K000 | KANONİK AD: isletim-sistemi | TEATRAL EPİTET: — | DOMAIN: SYSTEM |
RUNTIME: boot/kernel | SORUMLULUK: donanim ve sürücüler için soyut çalışma ortamı sağlar |
GİRDİ: donanım olayları + sürücü çağrıları | ÇIKTI: syscall/arayüz servisleri |
İZİNLİ BAĞIMLILIK: K000 (kendi içi) | YASAK BAĞIMLILIK: K009 api, K011 ux |
DATA BOUNDARY: kullanıcı verisi bu katmandan çıkmaz | SECURITY BOUNDARY: ayrıcalık seviyesi denetimi |
FAILURE MODE: panic → güvenli kapanış | OBSERVABILITY: kernel log + dmesg |
TEST: K002 surucu entegrasyon testleri | KANIT: .ai/architecture/K000-isletim-sistemi/ (glob)
```

> Bu satır **yalnız format kılavuzudur**: `K-ID`, `KANONİK AD` ve `KANIT` alanları gerçek disk
> çıktısıyla; `DOMAIN`, `RUNTIME` gibi alanlar diskte etiket yoksa `UNKNOWN` ile doldurulur.
> `TEATRAL EPİTET` uydurulmaz — vault'ta yoksa `—` yazılır.

### §6.5 Sık Yapılan Hatalar (Red Team bulguları — tekrarlanmasın)

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Katman sayısını web'den kopyalamak (proje 21 çekirdek katman) | Proje olgusu = disk; web yalnız yöntem |
| 2 | Tier (dağıtım) anlatısını katman gibi yazmak | §4.2 ayrım tablosu zorunlu |
| 3 | Bağımlılık okunu matristen farklı yazmak | `katman-baglilik-matrisi` kanonik (SRP) |
| 4 | K adı/tahmini id uydurmak | `ls .ai/architecture/` çıktısı esas |
| 5 | §1.3'te "araştırma yaptım" deyip URL koymamak | ≥3 gerçek kaynak URL zorunlu |
| 6 | Tabloyu düz metne çevirip kolon kaybı | §3.6 16 kolon aynen kalır |
| 7 | Şablonu okuyup üretimi şablonsuz yapmak | Guardrail #16: önce şablon, sonra belge |

---

**REFACTOR REPORT:** FILE: katmanli-mimari-template.md · PURPOSE: Katmanlı mimari belgesi şablonu
(Guardrail #16) · VALIDATION: 7 bölüm + §1.1 tanımlar + §1.3 web araştırması (10 gerçek kaynak) +
§3.3 context + §3.6 16 kolon + 18 kural + 19 doğrulama · RELATED: [[.templates/index]] ·
[[katman-readme-template]] · [[alt-katman-template]] · [[mimari-detay-template]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[AGENTS.md]] | Onay/rol routing |
| Ana mimari indeks | [[../architecture/00-master-index]] | K000-K020 dizin ağacı |
| Bağımlılık matrisi | [[../architecture/katman-baglilik-matrisi]] | İzinli/yasak okların kanonik kaynağı |
| Katman README şablonu | [[katman-readme-template]] | Tek katman README'si |
| Alt katman şablonu | [[alt-katman-template]] | Tek düğüm K{n}.a.b |
| Mimari detay şablonu | [[mimari-detay-template]] | README + detail ikilisi |
| ADR şablonu | [[../adr/adr-template]] | Karar gerekçesi (bu şablonun girdisi) |
| arc42 — Building Block View | https://docs.arc42.org/section-5/ | Katman görünümü yöntemi |
| Clean Architecture — Dependency Rule | https://www.informit.com/articles/article.aspx?p=2832399 | Bağımlılık kuralı |
| UQ CSSE6400 Layered Architecture | https://csse6400.uqcloud.net/slides/layered.pdf | 5 katman ilkesi |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-10-10
**Authority:** SSOT (bu dosya) — üretilen belgenin authority değeri kendi künyesidir
**Mode:** Red Team · Human Mode · Truth Mode
