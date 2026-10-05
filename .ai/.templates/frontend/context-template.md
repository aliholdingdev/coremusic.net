---
title: "CoreMusic — Context Documentation Template"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CoreMusic — Context Documentation Template

**Teknoloji:** Markdown · 7 bölümlü iskelet (§1–§7) · `{{VARIABLE}}` placeholder · **Katman:** dokümantasyon · **Sorumlu Agent:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[css-template]] · [[../../ui-design/01-mockup-index]]

---

## 1. Amaç

Bu şablon, CoreMusic klasör-bazlı/context-bazlı doküman iskeletini tanımlar. **Guardrail #16:** `.ai/` veya katman kökünde yeni context dokümanı (CONTEXT / CLAUDE / AGENTS / WORKFLOW tipi) bu şablondan üretilmek ZORUNLUDUR.

**4用途 notu (ortak iskelet):** Bu şablon **4 dokümanda ortak iskelettir**; hangi doküman olduğu `docType:` frontmatter alanı ile ayrılır:

| docType | Doküman | Ağırlıklı içerik | Sorumlu |
|---------|---------|------------------|---------|
| `context` | `CONTEXT.md` | **Bilgi** — bu klasör ne işe yarar, envanter, disk kanıtı | MO / vault-updater |
| `claude` | `CLAUDE.md` | **Kural** — guardrail özeti, yasaklar, ADR referansları | Security + UI Designer |
| `agents` | `AGENTS.md` | **Rol** — kim ne yapar, routing, sorumluluk tablosu | MO (registry) |
| `workflow` | `WORKFLOW.MD` | **Süreç** — adım adım akış, kapılar, commit kuralı | MO (workflow) |

| Karar | ADR | Şablona gömülü karşılığı |
|-------|-----|--------------------------|
| Vault SSOT + çelişkide disk kazanır | ADR-083 (ilgili) | §4 — çelişki kuralı |
| Guardrail #16 (şablon zorunlu) | ADR-042/C4 | §1 + §6 #1 |
| Vanilla JS + ITCSS, framework yasak | ADR-001 | §4 — hard guardrail referansı |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Klasör kökünde `CONTEXT.md` · `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.MD` üretimi | CSS/JS kod dosyaları → [[css-template]] · `js-template` |
| `{{VARIABLE}}` placeholder doldurma kuralı | Mockup görselleri → `.ai/ui-design/` (okunur, üretilmez) |
| Frontmatter 7 zorunlu alan + `docType` | Frozen ADR metinleri (dokunulmaz, yalnız referans verilir) |
| Wiki-link formatı `[[...]]` | İçerik verisi (envanter sayıları → disk ölçümü, uydurulmaz) |

- **Kullananlar:** MO (birincil), ilgili katman agent'ı (içerik), vault-updater (index kaydı).
- **Ön koşul:** Hedef klasör diskte MEVCUT ve envanter sayımları yapılmış olmalı; sayı yoksa `UNKNOWN` yazılır.
- **Not:** Bu şablon 4 doküman ortak iskeletidir; §3–§7 her docType'ta aynı sıra ile korunur.

---

## 3. Mimari

### 3.1 7 Bölüm İskeleti (§ sırası — `css-template.md` ile aynı düzen)

| Bölüm | Ad | Her docType'ta ne yazılır |
|-------|----|---------------------------|
| §1 | Amaç | Bu dokümanın ne olduğu + `docType` 4用途 tablosu (yalnız şablon kendisinde) veya 1 paragraf özet + ADR/decision tablosu |
| §2 | Kapsam | Kapsam / Kapsam Dışı tablosu + kullanıcılar + ön koşul |
| §3 | Mimari | Disk kanıtlı yapı (katman/dosya tablosu); sayılar `UNKNOWN` olamaz — ölçüm zorunlu |
| §4 | Kurallar | Guardrail özeti; numara ile `css-template.md` §4.1'e referans; çelişki kuralı |
| §5 | Workflow | Adım akışı (tabloda veya fenced code zincirde) + kapılar |
| §6 | Doğrulama | Numaralı kontrol tablosu (kriter sütunu zorunlu) |
| §7 | Referanslar | Wiki-link tablosu: Kaynak · Yol · Amaç |

### 3.2 Frontmatter Kalıbı (7 zorunlu alan + docType)

```yaml
---
title: "CoreMusic — {{KONU}}"
type: docs            # docs | guide
category: {{category}}
date: {{YYYY-MM-DD}}
updated: {{YYYY-MM-DD}}
version: 1.0.0
status: active
authority: "{{AUTHORITY}}"
docType: {{docType}}  # context | claude | agents | workflow
---
```

### 3.3 Placeholder Sözlüğü

| Placeholder | Açıklama | Örnek |
|-------------|----------|-------|
| `{{docType}}` | 4用途 ayrımı (§1 tablosu) | `context` |
| `{{KONU}}` | Doküman konusu | `Css — Katman Envanteri` |
| `{{category}}` | Frontmatter kategorisi | `frontend` |
| `{{AUTHORITY}}` | Otorite satırı | `SSOT (klasör kökü)` |
| `{{layer}}` | İlgili ITCSS katmanı | `04_Components` |
| `{{device}}` | İlgili cihaz | `RPi5 1024×600` |
| `{{agent}}` | Sorumlu agent adı | `ui` |
| `{{YYYY-MM-DD}}` | Tarih | `2026-10-03` |

### 3.4 Kalıp Metin (boş iskelet)

```markdown
# CoreMusic — {{KONU}}

**docType:** {{docType}} · **Katman:** {{layer}} · **Sorumlu:** {{agent}}

**Zorunlu Bağlantılar:** [[.templates/index]] · [[css-template]]

## 1. Amaç
{{1 paragraf özet + ilgili karar/ADR tablosu}}

## 2. Kapsam
{{Kapsam | Kapsam Dışı tablosu + kullanıcılar + ön koşul}}

## 3. Mimari
{{Disk kanıtlı tablo — sayılar ölçülür, uydurulmaz}}

## 4. Kurallar
{{Guardrail listesi — numara ile [[css-template]] §4.1 referansı}}

## 5. Workflow
{{Adım akışı + kapılar}}

## 6. Doğrulama
{{# | Kontrol | Kriter tablosu}}

## 7. Referanslar
{{Kaynak | Yol | Amaç wiki-link tablosu}}
```

### 3.5 Amaç Alanları — Katman/Dosya Satırı 5 Sütunlu (zorunlu 4 soru)

**Guardrail:** Bu şablonla üretilen her context dokümanında katman/dosya satırı **5 sütunlu** olmak ZORUNLUDUR; 4 sorudan biri boş bırakılamaz, "ceza" değil **gerekçe** yazılır:

| Katman/Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 (basit açıklama) | eli15 (neden ayrı / neden okunur / neden yazılır) |
|--------------|--------------------|---------------|-----------|---------------------|--------------------------|-----------------------------------------------------|
| `{{katman/dosya}}` | 1 — işlev | 2 — içerik bileşimi (dosya/kural) | 3 — hangi ihtiyacı doğurdu | 4 — tetikleyici olay | 1-2 cümle, jargon yok | 3-4 cümle: neden ayrı, neden okunur, neden yazılır, ne olur olmazsa |

**eli10 / eli15 zorunlu bloğu — HER katman/dosya/rol/adım maddesine tablonun (veya maddenin) HEMEN ALTINA şu block quote formatıyla gömülür (tüm dosyalarda aynı yer ve aynı etiket):**

```
> **eli10 (basit):** <bu dosya ne işe yarar — 1-2 cümle, günlük dil, jargon yok; varsa terim parantezde sadeleştir>
> **eli15 (detay):** <neden ayrı dosya, neden bu katmanda, neden okunur, neden yazılır, ne olur olmazsa — 3-4 cümle>
```

| Format kuralı | Kriter |
|---------------|--------|
| eli10 | En fazla **2 cümle**; günlük dil; teknik terim yok (zorunluysa parantezde sadeleştir: "adres defteri (token)") |
| eli15 | **3-4 cümle** — neden ayrı dosya · neden bu katmanda · neden okunur · neden yazılır · ne olur olmazsa |
| Yerleşim | Maddenin **hemen altında**, `> **eli10 (basit):**` / `> **eli15 (detay):**` etiketleriyle — tablo hücresinde ÖZET, maddede blok tam metin |
| Tutarlılık | Tüm `.md` dosyalarında **aynı etiket, aynı sıra** (eli10 önce, eli15 sonra) |

| # | Soru | Yazılacak şey | Kötü örnek |
|---|------|---------------|------------|
| 1 | **Ne için kullanılır?** | işlev — o katman/dosya hangi problemi çözer | "token katmanı" (işlev yok) |
| 2 | **Neyden oluşur / ne yazılır içine?** | içerik bileşimi — dosya listesi, kural tipi, import grubu | "çeşitli dosyalar" |
| 3 | **Neden var / neden yazıldı?** | gerekçe — hangi ihtiyaç, ADR veya guardrail doğurdu | "kural gereği" (hangi kural?) |
| 4 | **Ne zaman düzenlenir?** | tetikleyici — ne olunca bu dosyaya dokunulur | "gerekince" (somut olay yok) |

**docType'a göre 4 alanın ağırlığı (aynı 4 soru, farklı odak):**

| docType | 1 (ne için) | 2 (neyden) | 3 (neden var) | 4 (ne zaman) |
|---------|-------------|------------|----------------|---------------|
| `context` | katman işlevi | dosya/ envanter | mimari gerekçe (ADR/guardrail) | envanter güncelleme ölçümü |
| `claude` | kuralın koruduğu yüzey | kural metni + ref | **"neden var" sütunu — ceza değil gerekçe** | guardrail/ADR revizyonu |
| `agents` | rolün işlevi | yetki sınırları | **rol hangi ihtiyacı karşılar** | **ne zaman devreye girer** |
| `workflow` | adım çıktısı | girdi/kapı | **neden bu adım** | **atlanırsa ne olur** |

- Kaynak: disk kanıtı; tahmin/uydurma yasak (§4 #2). Uzunluk 300–500 satır korunur.

### 3.6 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçeleri — her gerekçe 1 satır)

> Bu bölüm her context dokümanında tekrarlanır (özellikle `docType: context`); içeriğin dosyalara/katmanlara ayrılmasının **gerekçesi** buradadır.

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ölçü/renk `01_Abstracts`'te tek yerde durur | Her dosyada ayrı `16px` → tutarsız görünüm, ölçü değişince tarama |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz | Her cihaz farkı tüm dosyalara yayılır → 8+ yerde tekrar, drift |
| 5 | **Vendor karantinası** | 3. taraf kod `07_Vendors`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

**eli10 / eli15 (bu bölümün kendi bloğu):**

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: değerler tek sözlükte, düz tek evde, parça tek kutuda durur. Böylece bir yeri değiştirince sadece o değişir, diğerleri bozulmaz. Cihaz ayarları ayrı olunca telefon ayarı masaüstünü bozmaz, hazır kütüphane ayrı olunca güncelleme bizim kodumuzu bozmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Frontmatter 7 zorunlu alan + `docType` eksik olamaz | Doküman reddedilir |
| 2 | Disk kanıtı olmayan dosya/sayı/versiyon adı YAZILMAZ — `UNKNOWN` | `⚠️ VERIFICATION REQUIRED` |
| 3 | Sayı iddiası (envanter, satır, dosya sayısı) ölçümle kanıtlanır | Id dia edilen sayi |
| 4 | Frozen ADR metni kopyalanmaz, yalnız wiki-link verilir | revert |
| 5 | Wiki-link formatı `[[...]]` — markdown path linki değil | Link check kırmızı |
| 6 | Placeholder `{{VARIABLE}}` üretilen dokümanda kalamaz | Doğrulama #3 ihlali |
| 7 | Çelişki kuralı: vault ↔ disk → **disk kazanır** + ⚠️ işaretlenir | SSOT ihlali |
| 8 | Emoji yasak — yalnızca PNG/`[[...]]` referansı | revert |

### 4.2 Ek Kurallar

- **docType yükü:** `context` = bilgi ağırlıklı (envanter tabloları) · `claude` = kural ağırlıklı (yasak listesi) · `agents` = rol/routing ağırlıklı · `workflow` = süreç/adım ağırlıklı. Bölüm sırası yine §1–§7'de kalır.
- **Uzunluk:** hedef 300–500 satır; § sayısı 7 sabit, alt başlık ≤3 seviye.
- **Tarih:** `date` = ilk üretim, `updated =` her revizyonda güncellenir.
- **İndeks:** üretilen şablon `.ai/.templates/index.md`'e 1 satır kaydedilir (mevcut satırlara dokunulmaz).

### 4.3 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | `docType` alanı olmadan 4用途 iskeleti kullanmak | `docType:` frontmatter'da zorunlu |
| 2 | Envanter sayısını şablondan kopyalayıp diskte doğrulamamak | Ölç → yaz; yoksa `UNKNOWN` |
| 3 | § sırasını değiştirmek (ör. Workflow'u §2'ye almak) | §1–§7 sabit |
| 4 | Wiki-link yerine göreli dosya yolu linki | `[[css-template]]` formatı |
| 5 | `{{VARIABLE}}` kalmış şekilde teslim | §6 #3 kontrolü |

---

## 5. Workflow

```
KATEGORİYİ SEÇ → {{docType}} BELİRLE → ŞABLONU KOPYALA (§3.4)
→ FRONTMATTER DOLDUR (7+1 alan) → §1–§7 İÇERİĞİ DOLDUR
→ DISK ÖLÇÜMÜ (§3 sayıları) → GUARDRAIL #16 DOĞRULA → INDEX'E 1 SATIR EKLE
```

1. **KATEGORİ/docType:** hedef 4 dokümandan hangisi? (`context`/`claude`/`agents`/`workflow`).
2. **ŞABLON KOPYALA:** §3.4 boş iskelet + `css-template.md` §1–§7 bölüm başlıkları.
3. **FRONTMATTER:** §3.2 kalıbı — 7 alan + `docType`.
4. **İÇERİK:** §1–§7; her §'de ne yazılacağı §3.1 tablosundan.
5. **DISK ÖLÇÜMÜ:** §3 tablolarındaki sayılar diskten ölçülür; ulaşılamayan `UNKNOWN`.
6. **GUARDRAIL #16 DOĞRULA:** §6 tablosu 7/7.
7. **INDEX:** `.ai/.templates/index.md` — yalnız 1 yeni satır (mevcut satırlara dokunma; format emin değilse kayıt atlanır ve raporlanır).
8. **COMMIT:** subagent atmaz — orkestratöre aittir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType` |
| 2 | Bölüm yapısı | §1–§7 numaralı, aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı (şablonun kendisi hariç) |
| 4 | Guardrails | §4.1 8/8 |
| 5 | Disk kanıtı | Her sayı/sayısal iddia ölçümle destekli veya `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; hedefler var |
| 7 | Uzunluk | 300–500 satır (istisna gerekçesi yorumda) |
| 8 | Halüsinasyon | Diskte olmayan dosya/katman/ADR iddia edilmedi |
| 9 | **eli10 + eli15 bloğu** | Her katman/dosya/rol/adım maddesinde `> **eli10 (basit):**` + `> **eli15 (detay):**` bloğu var mı? Etiket, sıra ve format §3.5 ile birebir aynı mı? (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 10 | **İçerik neden ayrı** | `docType: context` dokümanında §3.6 "İçerik Neden Dosyalara Ayrıldı?" tablosu 5 satır mı? |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter + kayıt satırı |
| CSS ana şablon | [[css-template]] | § düzeni (§1–§7) ve frontmatter kalıbının kaynağı |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails |
| Agent registry | [[../../AGENTS.md]] | Routing: dokümantasyon → MO |
| Mockup indeksi | [[../../ui-design/01-mockup-index]] | İçerik için görsel kanıt |
| Disk kanıtı | Hedef klasörün gerçek envanteri | §3 sayılarının kaynağı |
| Frontmatter şablonu | [[../documentation/docs-md-template]] | Genel docs md formatı |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-10-03
