---
title: "CoreMusic — Genel Dokümantasyon .md Şablonu"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
---

# CoreMusic — Genel Dokümantasyon .md Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

Bu şablon, CoreMusic vault'unda ve proje ağacında üretilecek **genel amaçlı dokümantasyon .md dosyaları** için standart dış iskeleti tanımlar: `README.md`, `AGENTS.md` (alt dizin kopyaları), rehber/kılavuz sayfaları, el kitabı bölümleri, runbook özeti ve "docs/" tipi bilgi dosyaları bu şablondan başlar. Şablonun taşıdığı bağlayıcı ilke, AI'ın her dosya yazımından önce `.ai/.templates/` dizinindeki ilgili şablonları okuması gerektiğidir (Guardrail #16 — Template Mandatory).

| Alan | Değer |
|------|-------|
| Template Name | `docs-md-template.md` |
| Template Path | `.ai/.templates/documentation/docs-md-template.md` |
| Hedef Dosya Tipi | Genel dokümantasyon Markdown (`README.md`, `AGENTS.md`, rehber, runbook, el kitabı) |
| Hedef Konum | Proje ağacı ve `.ai/` vault altı herhangi bir alt dizin |
| Guardrail | #16 (Template Mandatory) — şablonsuz doküman üretilemez |
| Birincil Yazar | Master Orchestrator (vault-updater) |
| İkincil Yazarlar | Domain sahibi agent'lar (backend, ui, security, data, qa, devops ...) |
| Routing | `[[../../AGENTS.md]]` §6: vault, documentation, wiki-link, index → MO (vault-updater) |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter) |
| Zorunlu Blok | §3.3 Şablon-Önce Kural Bloğu (üretilen dosyada SİLİNEMEZ) |
| Zorunlu Tablolar | §2 Kapsam/Kapsam Dışı + §6.1 Doğrulama listesi + §7.2 Değişiklik Geçmişi |
| Kayıt Kuralı | Değişiklik Geçmişi append-only — mevcut satıra dokunulmaz |
| Kanıt — Vault | `.ai/CLAUDE.md` (anayasa), `.ai/AGENTS.md` (agent routing), `.ai/.templates/index.md` (registry) |
| Kanıt — Format | 7 alanlı frontmatter, `[[wiki-link]]`, 8-bölüm iskeleti |
| Korunan Bilgi | Dil (TR), mojibake yasak, REDACTED, In-Place, append-only log |
| Değişken Formatı | `{{VARIABLE}}` (TITLE, PROJECT_NAME, DATE, AUTHOR, DOC_TYPE, SCOPE_DESCRIPTION, SECTION_TABLE_ROWS, RELATED_DOCS) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı İngilizce |
| Versiyon | 2.0.0 (ilk üretim — 2026-09-24) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-24 |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

Genel doküman **bilgi taşır**: mevcut olanı anlatır, yeni karar üretmez. Karar üretiyorsan bu şablonu kullanma; karar ADR'dir.

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| `README.md`, el kitabı, rehber, runbook özeti | ✅ Bu şablon | — |
| Alt dizin `AGENTS.md` (kısa yönerge kopyası) | ✅ Bu şablon | — |
| Vault wiki sayfası (bağlayıcı navigasyon) | `[[./WikiPage-Template]]` | ❌ |
| Proje AI anayasası (`CLAUDE.md`) | `[[./claude-md-template]]` | ❌ |
| Mimari karar kaydı (ADR) | `[[../adr/adr-template]]` | ❌ |
| API dokümanı | `[[./api-doc-template]]` | ❌ |
| Güvenlik denetim raporu | `[[./security-audit-template]]` | ❌ |
| Kod dosyası (PHP/JS/CSS/SQL/C++) | İlgili kod şablonu (`../backend/*`, `../frontend/*`) | ❌ |

**Ayırıcı test:** "Dosya bir karar mı kaydediyor?" → Evet ise `adr-template`. "Bir API'yi mi tanımlıyor?" → Evet ise `api-doc-template`. "Sadece anlatıyor mu?" → Evet ise bu şablon.

### §1.2 Şablon-Önce Kuralı (Temel İlke)

Bu şablonu kullanırken uygulanan kural, üretilen dosyanın içine de gömülür: **AI her dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları okur.** Kural metni §3.3'te birebir verilmiştir; şablon ve insan geliştirici aynı metne uyar.

| Boyut | Açıklama |
|-------|----------|
| Ne zaman | Her dosya yazımından ÖNCE (okuma adımından hemen sonra) |
| Nereden | `.ai/.templates/index.md` → kategori → şablon |
| Ne yapılacak | Şablon varsa ona göre yaz; yoksa bu belgedeki standart formata göre yaz |
| İhlal sonucu | Dosya geçersiz sayılır, revert edilir, `log.md`'ye ERROR girilir (Guardrail #16) |
| İstisna | Yok — tek istisna `session-log-template.md`'nin 500+ derinlikten muaf olmasıdır |

---

## §2 Kapsam

### §2.1 Kullanım Anları

| # | Dosya Tipi | Kullanım Anı | Sorumlu Agent |
|---|-----------|--------------|---------------|
| 1 | `README.md` (dizin kökü) | Dizin ilk kez belgelenirken | Domain sahibi |
| 2 | Alt dizin `AGENTS.md` | Dizinde AI yönergesi gerekiyor | Domain sahibi + MO onayı |
| 3 | `docs/*.md` el kitabı | Süreç/adım anlatımı yazılır | Domain sahibi |
| 4 | Runbook özeti | Operasyon adımı belgelenir | DevOps |
| 5 | Değişiklik/ürün notu | Sürüm notu yayımlanır | MO |

### §2.2 Kapsam

- **Kapsam:** Genel `.md` dokümanlarının dış iskeleti (frontmatter + H1 + §1-§7), zorunlu Şablon-Önce bloğu, biçim/dil kuralları, doğrulama listesi, append-only değişiklik geçmişi.
- **Kapsam dışı:** Karar kayıtları (ADR — `../adr/*`), API kontratı (`./api-doc-template`), güvenlik denetimi (`./security-audit-template`), AI anayasası (`./claude-md-template`), wiki navigasyon sayfaları (`./WikiPage-Template`), kod iskeletleri.

### §2.3 Hedef Kitle

| Kitle | Bu Şablonu Nasıl Kullanır |
|-------|---------------------------|
| AI ajanları | Görevde kopyala → doldur → §6.1 doğrula |
| İnsan geliştiriciler | Bölüm tablolarını doldurur, §7.2'yi tarihçe olarak sürdürür |
| Vault Steward | Denetimde §6.2 metrikleriyle karşılaştırır |
| Yeni ekip üyesi | Üretilen dosyayı okuyarak dizini öğrenir |

### §2.4 Kapsam Dışı İstisnalar

| Durum | Ne Yapılır |
|-------|-----------|
| Dosya hem wiki hem rehber niteliğinde | Önce MO'ya sor (Contradiction Gate); sonra tek şablon seçilir |
| Mevcut dosya bu şablonun dışında yazılmış | In-Place düzeltme; dosya adı değişmez, içerik iskelete çekilir |
| Şablonla vault çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 öncelik sırası uygulanır |

---

## §3 Mimari — Üretilen Dosyanın İskeleti

### §3.1 Dış İskelet (Frontmatter + H1 + Bağlantılar)

````markdown
---
title: "{{PROJECT_NAME}} — {{TITLE}}"
type: guide
category: documentation
version: 1.0.0
status: active
authority: reference
updated: {{DATE}}
---

# {{PROJECT_NAME}} — {{TITLE}}

**Zorunlu Bağlantılar:** [[../index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç
## §2 Kapsam
## §3 Mimari
## §4 Kurallar
## §5 Workflow
## §6 Doğrulama
## §7 Referanslar

---

**Authority:** {{AUTHOR}}
**Last Updated:** {{DATE}}
**Mode:** Red Team · Human Mode · Truth Mode
````

### §3.2 8-Bölüm Eşlemesi (İskelet → İçerik)

| İskelet Bölümü | Docs Dosyası Karşılığı | Zorunlu İçerik |
|-----------------|------------------------|----------------|
| Başlık | H1 + frontmatter | 7 zorunlu alan + tek H1 |
| §1 Amaç | Bu doküman neden var | 2-5 cümle + hedef kitle |
| §2 Kapsam | Ne kapsar / ne kapsamaz | 2 sütunlu tablo ≥ 3 satır |
| §3 Mimari | Yapının kendisi (dizin, akış, varlık) | Tablo veya ağaç bloğu |
| §4 Kurallar | Uygulanabilir kural/yasaklar | Numaralı kurallar + Şablon-Önce bloğu |
| §5 Workflow | Adım adım süreç | Numaralı/adımlı akış + tablo |
| §6 Doğrulama | Nasıl kontrol edilir | `- [ ]` listesi + metrik |
| §7 Referanslar | Bağlantılar + tarihçe | Wiki-link tablosu + Değişiklik Geçmişi |

### §3.3 ZORUNLU — Şablon-Önce Kural Bloğu (Kopyalanacak Metin)

> **Bu blok üretilen her genel dokümanda §4'ün ilk maddesi olarak birebir bulunmak ZORUNDADır.** Kısaltılamaz, yorumlanamaz, yerine başka cümle konulamaz.

````markdown
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
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |
````

### §3.4 Değişken Tablosu

| Değişken | Açıklama | Örnek |
|----------|----------|-------|
| `{{PROJECT_NAME}}` | Proje adı | CoreMusic |
| `{{TITLE}}` | Dokümanın adı | Dağıtım Runbook |
| `{{DOC_TYPE}}` | docs / README / AGENTS / runbook | README |
| `{{AUTHOR}}` | Yazar | Bayram Ali / Vault Steward |
| `{{VERSION}}` | Semver | 1.0.0 |
| `{{DATE}}` | YYYY-MM-DD | 2026-09-24 |
| `{{SCOPE_DESCRIPTION}}` | Tek cümlelik kapsam | Bu dizindeki servislerin kurulumu |
| `{{SECTION_TABLE_ROWS}}` | Bölüm tablosu satırları | Adım 1-5 satırları |
| `{{RELATED_DOCS}}` | İlgili dosya linkleri | `[[../index]]`, `[[../../WORKFLOW]]` |

### §3.5 Bölüm İçerik Rehberi

#### §3.5.1 §1 Amaç (asgari 5 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Dokümanın tek cümlelik tanımı | ✅ | Ne anlatıyor, kim için |
| Hedef kitle | ✅ | Kim okumalı |
| Neden şimdi yazıldı | Tercihen | Tetikleyici olay |
| Vault bağlantısı | ✅ | İlgili kök dosyaya wiki-link |

#### §3.5.2 §2 Kapsam (asgari 8 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Kapsam / Kapsam Dışı tablosu | ✅ | ≥ 3 satır |
| Alt konu listesi | ✅ | 3-7 madde |
| Kapsam dışı için "neye gitmeli" yönlendirmesi | ✅ | Yanlış şablonu engeller |
| Bağımlılıklar | Tercihen | Hangi dosya okunmalı |

#### §3.5.3 §3 Mimari (asgari 6 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Dizin/akış ağacı (kod bloğu) | ✅ | ASCII ağaç veya tablo |
| Varlık/rol tablosu | ✅ | ≥ 3 satır |
| Diyagram | Tercihen | Mermaid varsa eklenir |
| Kod yazımı | ❌ | Bu şablon PHP/JS/CSS/SQL/C++ içermez |

#### §3.5.4 §4 Kurallar (asgari 12 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Şablon-Önce bloğu (§3.3) | ✅ ASLA SİLİNEMEZ | İlk madde |
| Numaralı kurallar | ✅ | ≥ 4 kural |
| Yasak/Doğru tablosu | ✅ | En az 3 çift |
| İhlal sonucu | ✅ | Her kuralın sonucu |

#### §3.5.5 §5 Workflow (asgari 8 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Adım listesi (numaralı) | ✅ | 4-8 adım |
| Adım tablosu (aksiyon/çıktı) | ✅ | ≥ 4 satır |
| Geri dönüş/rollback notu | Tercihen | Operasyonel dokümanlarda zorunlu |
| Tahmini süre | Tercihen | Dakika cinsinden |

#### §3.5.6 §6 Doğrulama (asgari 8 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| `- [ ]` kontrol listesi | ✅ | ≥ 8 madde |
| Ölçülebilir metrik | ✅ | Satır/test/coverage/süre |
| Kanıt komutu | Tercihen | Salt-okunur komut örneği |
| Hata modları tablosu | Tercihen | Belirti → düzeltme |

#### §3.5.7 §7 Referanslar (asgari 6 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Wiki-link tablosu | ✅ | ≥ 4 hedef |
| Değişiklik Geçmişi | ✅ | Append-only |
| Authority footer | ✅ | Authority + Last Updated + Mode |

### §3.6 Anti-Pattern Tablosu (Üretim Sırası Sık Yapılan Hatalar)

| # | Anti-Pattern | Neden Kötü | Doğrusu |
|---|--------------|-----------|---------|
| 1 | Şablon-Önce bloğunu "gereksiz" bulup çıkarmak | Guardrail #16 metni zayıflar | §3.3 birebir kalır |
| 2 | Dokümana kod dosyası gömmek (PHP/JS/CSS/SQL/C++) | Şablon ihlali + kod duplication | Kod dosyası ayrı şablondan üretilir, buraya link verilir |
| 3 | Mutlak yol / Windows yolu linklemek | Kırılgan link | `[[relative/path]]` |
| 4 | Değişiklik Geçmişi'nde eski satırı düzenlemek | Append-only ihlali | Yeni satır eklenir |
| 5 | Frontmatter'siz dosya | Standart ihlali | 7 alan zorunlu |
| 6 | "yaklaşık/belki/duyduğuma göre" ifadeler | Hallüsinasyon | Doğrula veya `VERIFICATION REQUIRED` |
| 7 | Mojibake bırakma | Okunamaz | `vault-utf8-writer verify` |
| 8 | 500 altı satır | Derinlik standardı ihlali | §3.5'e göre derinleştir |

---

## §4 Kurallar

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Genel `.md` bu şablondan üretilir | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | SSOT | Bilgi vault'tan; dış kaynak vault altındadır | İçerik silinir |
| 4 | Zero Hallucination | Doğrulanamayan iddia etiketlenir | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED | Secret/key asla yazılmaz | Sızıntı sayılır |
| 8 | Append-only log | Değişikliklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Dosya domain sahibi agent tarafından yazılır | Layer violation → revert |

### §4.2 Frontmatter — 7 Zorunlu Alan

| Alan | Zorunlu | Kural | Örnek |
|------|---------|-------|-------|
| `title` | ✅ | Tırnak içinde, `Proje — Başlık` | `"CoreMusic — Dağıtım Runbook"` |
| `type` | ✅ | `guide` / `howto` / `reference` | `guide` |
| `category` | ✅ | Kategori | `documentation` |
| `version` | ✅ | Semver | `1.0.0` |
| `status` | ✅ | `active` / `draft` / `deprecated` | `active` |
| `authority` | ✅ | Genel doküman `reference` | `reference` |
| `updated` | ✅ | `YYYY-MM-DD` | `2026-09-24` |

**Not:** Vault kök anayasası (`CLAUDE.md`) `authority: SSOT` taşır; bu şablonla üretilen genel dokümanlar `reference` değerini kullanır (SSOT self-claim yasak).

### §4.3 Dil, Kod Adı ve Mojibake

| Kural | Detay |
|-------|-------|
| Ana dil | Türkçe — ç ğ ı İ ö ş ü doğru yazılır |
| Dosya/klasör adı | İngilizce: `README.md`, `runbook-deploy.md` |
| Teknik terim | Gerektiği yerde İngilizce kalır (repository, middleware) — zorla çevrilmez |
| Mojibake yasak | Bozuk UTF-8 ikilileri (Ã- ile başlayan diziler) ve U+FFFD yer tutucuları `verify` ile tespit edilir |
| Yazım aracı | `node .ai/scripts/vault-utf8-writer.mjs` |
| PowerShell yazım yasağı | `Set-Content`, `Out-File`, `Add-Content`, `echo >` kullanılmaz |

### §4.4 Wiki-Link ve Bağlantı Formatı

| ✅ Doğru | ✅ Yanlış |
|----------|-----------|
| `[[../index]]` | `[index](../index.md)` |
| `[[../../WORKFLOW.md]]` | `[WF](C:\www\coremusic.net\.ai\WORKFLOW.md)` |
| `[[.templates/index]]` | `https://iç-sistem/wiki/index` |
| Harici URL düz metin | `https://...` (wiki-link yapılmaz) |

### §4.5 SSOT ve Authority Hiyerarşisi

| Sıra | Kaynak | Çelişki Durumunda |
|------|--------|-------------------|
| 1 | `.ai/CLAUDE.md` | Kazanır |
| 2 | `.ai/AGENTS.md` | — |
| 3 | `.ai/WORKFLOW.md` | — |
| 4 | `.ai/brain.md` | — |
| 5 | `.ai/index.md` | — |
| 6 | `.ai/.templates/` | En altta |

**Bu şablonla üretilen dosya** kendini SSOT ilan edemez; `authority: reference` taşır ve çelişkide kök vault dosyasına bağlanır.

### §4.6 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Yeni şablon dosyası | ≥ 500 satır (ham ölçüm — `vault-utf8-writer verify` → `lines`) |
| Üretilen doküman | Zorunlu değil; ama §3.5 asgari satırları karşılamalı |
| Kapsam dışı | `session-log-template.md` (144 satır) |
| İhlal | Şablon 500 altındaysa tamamlanmış sayılmaz |

### §4.7 REDACTED ve Sır Politikası

| Durum | Aksiyon |
|-------|---------|
| API key, token, parola | Vault'a asla yazılmaz; `[REDACTED]` |
| `.env` içeriği | Örnek bile olsa yapıştırılmaz |
| Kişisel veri (KVKK) | Maskelenir |
| Log'a sızan secret | `log.md` girişinde `[REDACTED]` |

### §4.8 In-Place, Frozen ADR ve Dosya Adı Koruması

| Kural | Uygulama |
|-------|----------|
| Dosya adı değişmez | `README.md` → `README-v2.md` üretilmez |
| Frozen ADR 001-037 | Okunur, referans edilir; değiştirilmez |
| Yeni ADR gerekirse | `ADR-090+` numarasıyla ayrı dosyada açılır |
| Silinmezlik | Eski içerik düzeltilir, `log.md`'ye kaydedilir |

---

## §5 Workflow

### §5.1 Üretim Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Şablonu seç | `documentation/docs-md-template.md` | 1 dk |
| 2 | `[[.templates/index]]` §4 kurallarını oku | Format bilgisi | 1 dk |
| 3 | Hedef dizini incele (mevcut dosya, komşu dosyalar) | In-Place mi üretim mi | 2 dk |
| 4 | Kopyala → `{{VARIABLE}}` doldur | Taslak | 5 dk |
| 5 | §3.3 Şablon-Önce bloğunu birebir göm | Zorunlu blok | 1 dk |
| 6 | §3.5 bölüm asgari satırlarını doldur | Dolu §1-§7 | 10 dk |
| 7 | §6.1 kontrol listesini çalıştır | İşaretli liste | 3 dk |
| 8 | `vault-utf8-writer verify` | UTF-8 + satır raporu | <1 dk |
| 9 | `log.md` append + gerekirse registry senkronu | Audit trail | 2 dk |

```text
ŞABLONU SEÇ → OKU → KOPYALA → {{PLACEHOLDER}} DOLDUR → ŞABLON-ÖNCE BLOĞUNU GÖM → BÖLÜM SATIRLARINI DOLDUR → DOĞRULA → UTF-8 VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Şablon okunmadan yazım | §6.1'de 3+ madde düşer | Şablondan yeniden üret |
| Eksik frontmatter | FM BAD | 7 alanı tamamla |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Kırık wiki-link | Hedef diskte yok | Linki düzelt veya `VERIFICATION REQUIRED` |
| Kapsam belirsiz | §2 tablosu boş/geçersiz | Hedef kitleyi netleştir, sonra yaz |
| Domain ihlali | Başka agent'ın dosyasına yazdın | Handover protokolü (`[[../../AGENTS.md]]` §9) |

### §5.3 Bitiş Koşulları

- [ ] Dosya hedef yolda, adı İngilizce, `.md` uzantılı
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0)
- [ ] `log.md` append edildi
- [ ] Wiki-link'ler hedef buluyor

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] Tek H1 var, `# ` ile başlıyor
- [ ] §3.3 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] §1-§7 başlıkları eksiksiz
- [ ] Kapsam / Kapsam Dışı tablosu ≥ 3 satır
- [ ] Zorunlu Bağlantılar satırı (en az 1 wiki-link) var
- [ ] Tüm iç linkler `[[relative/path]]` formatında
- [ ] §4'te numaralı kurallar + Yasak/Doğru tablosu var
- [ ] §5'te adım listesi ≥ 4 adım
- [ ] §6'da `- [ ]` maddeleri ≥ 8
- [ ] §7.2 Değişiklik Geçmişi append-only tablo var
- [ ] Authority footer (Authority + Last Updated + Mode)
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] Kod bloğu içinde PHP/JS/CSS/SQL/C++ yok (yalnız ASCII ağaç/şema)

### §6.2 Quality Report (Şablon Dosyasının Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 2.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Zorunlu blok | 1 (§3.3 Şablon-Önce — silinemez) |
| Guardrail kapsamı | #16 merkezli; #2, #3, #4, #5, #12 bağlayıcı |
| Değişken | 9 (`{{VARIABLE}}`) |
| Anti-pattern | 8 (§3.6) |
| Frontmatter alan | 7 zorunlu |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (verify `lines`) |
| Kayıt | `log.md` append + registry senkron |

### §6.3 Örnek — Dolu İskelet (İlk 2 Bölüm)

````markdown
## §1 Amaç

Bu doküman, **{{PROJECT_NAME}}** içindeki {{SCOPE_DESCRIPTION}} sürecini adım adım anlatır.
Hedef kitle: bu dizin üzerinde çalışan geliştiriciler ve AI ajanları.
Vault bağlantısı: kurallar → `[[../CLAUDE.md]]`, süreçler → `[[../WORKFLOW]]`.

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Kurulum adımları | Uygulama içi iş mantığı |
| Doğrulama komutları (salt-okunur) | Kod değişikliği prosedürü |
| Hata modları ve düzeltmeler | Güvenlik politikası tasarımı |

*Alt konular:* ön hazırlık → kurulum → doğrulama → sorun giderme → geri dönüş.
Kapsam dışı için: kod iskeleti → `[[../backend/php-template]]`, karar → `[[../adr/adr-template]]`.
````

````markdown
## §3 Mimari

```text
shared/src/
├── Middleware/     → PSR-15 middleware zinciri
├── Api/            → Gateway + BFF katmanı
└── Database/       → PDO bağlantı yönetimi
```

| Varlık | Rol | Sorumlu |
|--------|-----|---------|
| Gateway | Tek giriş noktası | Backend |
| Middleware | Güvenlik zinciri | Security |
| Repository | Veri erişimi | Data |

## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First)
*(§3.3 bloğu birebir buraya yapıştırılır — 5 madde + Durum/Aksiyon tablosu)*

1. Bu dizindeki dosyalar `strict_types` ile yazılır.
2. Doğrudan `echo`/`var_dump` ile çıktı alınmaz; logger kullanılır.
3. Mutlak yol referansı yapılmaz; göreli `../` linki kullanılır.

| ✅ Yasak | ✅ Doğru |
|----------|----------|
| `SELECT *` | Açık sütun listesi |
| Hardcoded secret | `.env` |
| `console.log` prod'da | Logger |

## §5 Workflow

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Ön hazırlık: bağımlılık kontrolü | Kontrol listesi | 5 dk |
| 2 | Kurulum adımı | Çalışan dizin | 10 dk |
| 3 | Doğrulama (salt-okunur komut) | Çıktı | 3 dk |
| 4 | Kayıt: `log.md` append | Audit satırı | 1 dk |

## §6 Doğrulama

- [ ] Adımlar sırayla uygulanabildi
- [ ] Doğrulama komutu temiz çıktı verdi
- [ ] `log.md` append edildi
- [ ] Wiki-link'ler hedef buluyor

## §7 Referanslar

| Dosya | Amaç |
|-------|------|
| `[[../index]]` | Vault kataloğu |
| `[[../WORKFLOW]]` | Süreçler |

| Tarih | Versiyon | Değişiklik |
|-------|----------|------------|
| {{DATE}} | 1.0.0 | İlk üretim |
````

*(§3-§7 örnekleri kasıtlı kısaltılmıştır; üretimde §3.5 asgari satırları doldurulur.)*

---

## §7 Referanslar

### 7.1 Wiki-link Referansları

| Hedef | İlişki |
|-------|--------|
| `[[.templates/index]]` | Şablon registry'si — bu şablonun kayıt defteri |
| `[[../CLAUDE.md]]` | Vault anayasası (Guardrail #2, #16 kaynağı) |
| `[[../../AGENTS.md]]` | Agent routing §6 + handover §9 |
| `[[../../WORKFLOW.md]]` | Süreçler, boot protokolü |
| `[[./claude-md-template]]` | CLAUDE.md üretim şablonu (kardeş) |
| `[[./WikiPage-Template]]` | Wiki sayfası şablonu (kardeş) |
| `[[../../log.md]]` | Audit trail (append-only) |
| `[[../../keys.md]]` | Keyword haritası |

### 7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-24 | 2.0.0 | İlk üretim — registry'ye eklendi; §3.3 Şablon-Önce bloğu zorunlu | vault-updater |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
