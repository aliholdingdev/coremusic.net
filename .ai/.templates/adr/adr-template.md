---
title: "CoreMusic — Architecture Decision Record Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — Architecture Decision Record Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[CLAUDE.md]] · [[brain.md]] · [[WORKFLOW.md]]

---

## §1 Amaç

Bu şablon, CoreMusic mimari kararlarının (Architecture Decision Record — ADR) standart biçimde kaydedilmesi için zorunludur. **Guardrail #16:** yeni bir ADR dosyası oluşturulurken bu şablondan başlamak ZORUNLUDUR. Şablon; Bağlam (Context), Karar (Decision), Alternatifler, Sonuçlar, Uygulama ve Onay bölümlerini sabitleyerek kararların izlenebilir, gerekçeli ve denetlenebilir kalmasını sağlar. **ADR-042 hibrit kuralı:** `.templates/*` dosyaları tam yeniden yazıma açıktır (bu dosya v2.0.0 ile yeniden yazıldı).

| Alan | Değer |
|------|-------|
| Template Name | `adr-template.md` |
| Template Path | `.ai/.templates/adr/adr-template.md` |
| Hedef Dosya Tipi | Architecture Decision Record (ADR) |
| Adlandırma | `ADR-NNN-<slug>.md` (NNN = sıradaki numara, slug = karar özeti) |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Vault Steward (yazar + onay) |
| Onaylayanlar | Vault Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ |
| Üretici Agent'lar | Backend / Security / Data / Embedded vb. karar domaini sahipleri |
| Durum Döngüsü | Draft → Review → Active → Frozen |
| İskelet | H1 + künye + §1 Bağlam → §7 Onay (7 bölüm) |
| Zorunlu Bölüm | §1.3 Web araştırması raporu (gerçek sonuçlarla doldurulur) |
| Disk Kanıtı — Dizin | `.ai/.decisions/index.md` + `.ai/.decisions/CLAUDE.md` MEVCUT |
| Disk Kanıtı — Alt Klasör | `.ai/.decisions/accepted/`, `draft/`, `rejected/` (her birinde `CLAUDE.md`) |
| ⚠️ Disk Kanıtı — ADR Dosyası | `**/ADR-0*.md` glob'u BOŞ — tekil ADR-NNN dosyası diskte YOK |
| Wiki-link Kanıtı | Vault kök dosyalarında `[[decisions/accepted/ADR-001-...]]` biçimli referanslar |
| Korunan Bilgi | §1-§7 iskeleti, REFACTOR REPORT satırı, web araştırması §1.3, onay akışı |
| Değişken Formatı | `{{VARIABLE}}` (TITLE, STATUS, DATE, AUTHOR, CONTEXT_DESCRIPTION, CURRENT_STATE, PROBLEM_STATEMENT, DECISION_DESCRIPTION, RATIONALE, TECHNICAL_DETAILS, ALT_*, POSITIVE_*, NEGATIVE_*, RISK_*, STEP_*, ROLLBACK_PLAN, TECH_LEAD, ARCH_LEAD, RELATED_ADRS, Web Search alanları) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); ADR adı/slug İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); üretilen ADR'nin authority değeri karar metninin kendisidir |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-23 |

### §1.1 ADR Neden 7 Bölümle Sınırlandırılır?

Yedi bölüm, bir kararın **tam izlenebilirliğini** sağlar: neden (§1), ne (§2), neden başka değil (§3), bedeli (§4), nasıl (§5), nereye bağlı (§6), kim onayladı (§7). Daha az bölüm gerekçe boşluğu, daha fazlası gereksiz karmaşa üretir. Bu yüzden iskelet silinemez — yalnız alanlar doldurulur.

| Bölüm | Yaptığı İş | Boş Bırakılırsa |
|-------|-----------|------------------|
| §1 Bağlam | Sorunu ve kısıtları belgeler | Karar "niye?" sorusuyla kalır |
| §1.3 Web araştırması | Güncel ekosistem kanıtı | Karar eski bilgiye dayanır |
| §2 Karar | Kesin ifadeyi sabitler | Yorum payı kalır |
| §3 Alternatifler | Reddedilen seçenekleri gerekçeler | "Bunu neden yapmadık?" sorusu kalır |
| §4 Sonuçlar | Kazanç/kayıp/risk dengesi | Sürpriz maliyetler |
| §5 Uygulama | Adım + geri dönüş planı | Geri alınamaz karar |
| §6 İlgili | Bağlantı ağı | İzole karar |
| §7 Onay | Yetki zinciri | Onaysız karar |

### §1.2 Disk Kanıtları ve Boşluk (YAGNI)

Şablon, `.ai/.decisions/` altındaki **gerçek** yapıya sadıktır. Tekil ADR dosyaları diskte bulunamadı; bu bilgi saklanmaz — açıkça işaretlenir.

| İddia | Durum | Kanıt |
|-------|-------|-------|
| `.ai/.decisions/index.md` var | ✅ DOĞRULANDI | glob → dizin dosyası |
| `.ai/.decisions/CLAUDE.md` var | ✅ DOĞRULANDI | glob → dizin kuralı |
| `.ai/.decisions/accepted/` var | ✅ DOĞRULANDI | klasör + `CLAUDE.md` |
| `.ai/.decisions/draft/` var | ✅ DOĞRULANDI | klasör + `CLAUDE.md` |
| `.ai/.decisions/rejected/` var | ✅ DOĞRULANDI | klasör + `CLAUDE.md` |
| `**/ADR-0*.md` tekil dosyalar | ❌ DOĞRULANAMADI | glob BOŞ → `⚠️ VERIFICATION REQUIRED` |
| Wiki-link kanıtları | ✅ MEVCUT | kök dosyalarda `[[decisions/accepted/ADR-001-...]]` biçimleri |

**Yorum yasağı:** Yukarıdaki boşluk için "ADR'ler şurada", "dosyalar şöyle" gibi açıklamalar uydurulmaz; yalnız `⚠️ VERIFICATION REQUIRED` yazılır ve üst göreve sorulur.

### §1.3 Karar Türleri

| Tür | Tipik Konu | Örnek Alan | Onay Kademesi |
|-----|-----------|-----------|---------------|
| Teknoloji seçimi | Kütüphane / dil / sürücü | §2.2 Teknik Detaylar | Tech Lead + Arch Lead |
| Mimari sınır | Katman / servis / modül sınırı | §1.4 Kısıtlamalar | Arch Lead |
| Güvenlik kararı | Kripto / auth / oturum | §4.3 Riskler | Security Engineer → Arch Lead |
| Veri kararı | Şema / BCNF / indeks | §5.1 Adımlar | Data Engineer → Arch Lead |
| Süreç kararı | Workflow / guardrail | §6 İlgili Dokümanlar | Vault Steward + Tech Lead |

---

## §2 Kapsam

ADR'nin kapsadığı ve kapsam dışında bıraktığı alanlar. ADR bir **karar kaydıdır**: kod üretmez, yalnız neden-sonuç zincirini belgeler.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Mimari kararların standart biçimde kaydı (`ADR-NNN-<slug>.md`) | Frozen ADR metinlerinin düzenlenmesi (okunur + referanslanır — AGENTS.md §25.3 kural 2) |
| Durum döngüsü: Draft → Review → Active → Frozen | Kod implementasyonu (→ `[[../backend/php-template]]`, `[[../backend/nodejs-template]]`) |
| Bağlam / Karar / Alternatifler / Sonuçlar / Uygulama / Onay | Agent routing/handover kuralları (→ `[[../../AGENTS.md]]`) |
| §1.3 web araştırması raporu (gerçek sonuçlar) | Envanter listesi (→ `[[.templates/index]]`, SRP) |
| Risk tablosu + geri dönüş planı | API/denetim/wiki dokümanları (→ `[[../documentation/*]]`) |
| 3 kademeli onay akışı (Steward → Tech Lead → Arch Lead) | `.ai/log.md` append-only kayıt (üst görevin işi) |
| `{{PLACEHOLDER}}` doldurma + Guardrail #16 doğrulaması | `.ai/.decisions/index.md` kayıt satırı (kayıt — ayrı işlem) |
| Türkçe doğruluk + mojibake denetimi | Secret/credential yazımı (REDACTED — yasak) |

**Dosya tipi:** Markdown ADR · **Uzantı:** `.md` · **Konum:** `.ai/.decisions/` · **Guardrail:** #16 · **Kural:** Frozen = dokunulmaz

### §2.1 Dosya Tipi → Sorumlu Eşlemesi

| Dosya Tipi | Sorumlu | Bu Şablondan mı? |
|------------|---------|-------------------|
| `.ai/.decisions/ADR-NNN-*.md` | Vault Steward + domain agent | ✅ (bu şablon) |
| `.ai/.decisions/index.md` | Vault Steward | ❌ Dizin dosyası — kayıt satırı eklenir |
| `.ai/brain.md` ADR özeti | MO (vault-updater) | ❌ Özet — ADR'den türetilir |
| `.ai/.agents/*.md` | Domain agent | ❌ → `[[../agents/agents-template]]` |
| `.ai/*.md` genel sayfa | MO (vault-updater) | ❌ → `[[../documentation/WikiPage-Template]]` |
| `docs/api/*.md` | Backend Architect | ❌ → `[[../documentation/api-doc-template]]` |
| `src/**/*.php` | Backend Architect | ❌ → `[[../backend/php-template]]` |

### §2.2 Kapsam Sınırı Testi

Bir kararın ADR gerektirip gerektirmediği aşağıdaki sorularla anlaşılır; "evet" çıkıyorsa bu şablon kullanılır.

| # | Soru | Evet ise |
|---|------|----------|
| 1 | Geri döndürülmesi pahalı mı (hard-to-reverse)? | ADR yaz |
| 2 | Birden fazla ekibi/dosya tipini etkiliyor mu? | ADR yaz |
| 3 | Alternatifler gerçekten değerlendirildi mi? | ADR yaz (§3 zorunlu) |
| 4 | Gelecekteki biri "neden böyle?" diye soracak mı? | ADR yaz |
| 5 | Tek dosyalık düzeltme mi? | ADR yazma → ilgili şablon |
| 6 | Geçici workaround mı? | ADR yazma → `log.md` append |

---

## §3 Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + künye + 7 domain bölümü, eksiksiz). Dört-çit ` ```markdown ` bloğu iskeletin kendi üç-çit bloklarını korur.

````markdown
---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Architecture Decision Record Template"
type: adr-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Durum:** {{STATUS}} (Draft/Review/Active/Frozen)
**Tarih:** {{DATE}}
**Karar Veren:** {{AUTHOR}}
**İlgili ADR'ler:** {{RELATED_ADRS}}

---

## 1. Bağlam (Context)

<!-- Bu karar neden alındı? Hangi sorun çözülüyor? -->

{{CONTEXT_DESCRIPTION}}

### 1.1 Mevcut Durum

{{CURRENT_STATE}}

### 1.2 Sorun Tanımı

{{PROBLEM_STATEMENT}}

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | {{WEB_SEARCH_QUERY}} |
| Web Search **Konusu** | {{WEB_SEARCH_TOPIC}} |
| Web Search **Bağlam** | {{WEB_SEARCH_CONTEXT}} |
| Web Search **Kısa Açıklama** | {{WEB_SEARCH_SHORT_DESC}} |
| Web Search **Uzun Açıklama** | {{WEB_SEARCH_LONG_DESC}} |
| Web Search **Paragraf Veri Uzun** | {{WEB_SEARCH_PARAGRAPH}} |
| Web Search **Sonucu** | {{WEB_SEARCH_RESULT}} |
| Web Search **Alınan Karar** | {{WEB_SEARCH_DECISION}} |
| Web Search **Sonuç** | {{WEB_SEARCH_CONCLUSION}} |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| {{CONSTRAINT_1}} | {{CONSTRAINT_1_DESC}} |
| {{CONSTRAINT_2}} | {{CONSTRAINT_2_DESC}} |
| {{CONSTRAINT_3}} | {{CONSTRAINT_3_DESC}} |

---

## 2. Karar (Decision)

<!-- Ne kararı alındı? -->

{{DECISION_DESCRIPTION}}

### 2.1 Neden Bu Seçenek?

{{RATIONALE}}

### 2.2 Teknik Detaylar

{{TECHNICAL_DETAILS}}

---

## 3. Alternatifler (Alternatives)

<!-- Hangi alternatifler değerlendirildi? -->

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | {{ALT_1}} | {{ALT_1_PROS}} | {{ALT_1_CONS}} | {{ALT_1_REJECT}} |
| 2 | {{ALT_2}} | {{ALT_2_PROS}} | {{ALT_2_CONS}} | {{ALT_2_REJECT}} |
| 3 | {{ALT_3}} | {{ALT_3_PROS}} | {{ALT_3_CONS}} | {{ALT_3_REJECT}} |
| 4 | {{ALT_4}} | {{ALT_4_PROS}} | {{ALT_4_CONS}} | {{ALT_4_REJECT}} |

---

## 4. Sonuçlar (Consequences)

<!-- Bu kararın sonuçları neler? -->

### 4.1 Olumlu Sonuçlar

- {{POSITIVE_1}}
- {{POSITIVE_2}}
- {{POSITIVE_3}}

### 4.2 Olumsuz Sonuçlar

- {{NEGATIVE_1}}
- {{NEGATIVE_2}}
- {{NEGATIVE_3}}

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| {{RISK_1}} | {{RISK_1_PROB}} | {{RISK_1_IMPACT}} | {{RISK_1_MITIG}} |
| {{RISK_2}} | {{RISK_2_PROB}} | {{RISK_2_IMPACT}} | {{RISK_2_MITIG}} |
| {{RISK_3}} | {{RISK_3_PROB}} | {{RISK_3_IMPACT}} | {{RISK_3_MITIG}} |

---

## 5. Uygulama (Implementation)

<!-- Nasıl uygulanacak? -->

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | {{STEP_1}} | {{STEP_1_OWNER}} | {{STEP_1_DURATION}} |
| 2 | {{STEP_2}} | {{STEP_2_OWNER}} | {{STEP_2_DURATION}} |
| 3 | {{STEP_3}} | {{STEP_3_OWNER}} | {{STEP_3_DURATION}} |

### 5.2 Geri Dönüş Planı

{{ROLLBACK_PLAN}}

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Ana sözleşme |
| [[brain.md]] | Mimari kararlar |
| [[WORKFLOW.md]] | Süreçler |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | {{AUTHOR}} | {{DATE}} | ✅ |
| Tech Lead | {{TECH_LEAD}} | {{DATE}} | ⏳ |
| Arch Lead | {{ARCH_LEAD}} | {{DATE}} | ⏳ |

---

*ADR Template v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
````

---

## §4 Kurallar

ADR yazımı sırasında uygulanan zorunlu ve yasak kurallar. Frozen dokunulmazlık ve onay akışı bu kuralların merkezindedir.

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Yeni ADR bu şablondan üretilir; dış iskelet (künye + §1 Bağlam → §7 Onay) silinemez, yalnızca alanlar doldurulur | Zorunlu | Guardrail #16 ihlali |
| 2 | Durum akışı Draft → Review → Active → Frozen'dır; `status` alanı gerçek durumla yazılır | Zorunlu | Yanlış durum kaydı |
| 3 | Frozen ADR metinleri okunur ve referans edilir — değiştirilmez (AGENTS.md §25.3 kural 2) | Yasak | Frozen ihlali → derhal revert |
| 4 | §7 Onay tablosu Vault Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ akışını tamamlamadan karar Active olamaz | Zorunlu | Onaysız karar |
| 5 | Tüm `{{...}}` alanları (özellikle §1.3 web araştırması ve §5.2 geri dönüş planı) gerçek değerlerle doldurulur; boş ADR yayımlanamaz | Zorunlu | Yarım karar kaydı |
| 6 | Bilinmeyen bilgi `⚠️ VERIFICATION REQUIRED` ile işaretlenir; tahmin/uydurma yazılmaz | Zorunlu | Hallucination |
| 7 | Adlandırma `ADR-NNN-<slug>.md` biçimindedir; NNN sıradaki numaradır | Zorunlu | Numara çakışması |
| 8 | Bu şablon dosyasının kendi frontmatter'ı `SSOT`; §3 iskeletteki `authority: Single Source of Truth (SSOT)` örneği birebir korunur — üretilen ADR'nin authority değeri karar metninin kendisidir, şablon SSOT iddiası taşımaz | Zorunlu | Authority çelişkisi |
| 9 | Governance: Red Team · Human Mode · Truth Mode | Zorunlu | Mod eksikliği |
| 10 | ADR metni frozen olduktan sonra **hiçbir** düzenleme yapılmaz; yeni karar = yeni ADR + eskiyi `superseded by` bağla | Yasak | Karar geçmişi kaybı |
| 11 | Envanter listesi bu şablonda tekrarlanmaz (SRP) → `[[.templates/index]]` | Yasak | İkincil kaynak çelişkisi |
| 12 | Routing/onay değerleri `[[../../AGENTS.md]]` dosyasından okunur (DIP) | Zorunlu | SSOT çelişkisi |
| 13 | Aynı kategori § başlıkları birebir aynıdır (DRY): §1 Amaç → §7 Referanslar | Zorunlu | Şablon tutarsızliği |
| 14 | Türkçe karakterler (ç ğ ı İ ö ş ü) doğru; mojibake YASAK; ADR slug'ı İngilizce | Zorunlu | Mojibake → onarım |
| 15 | Secret / credential hiçbir koşulda ADR'ye yazılmaz (REDACTED politikası) | Yasak | Güvenlik ihlali |
| 16 | Uydurma dosya/yol adı yazılmaz; `.ai/.decisions/` yapısı glob kanıtıdır (YAGNI) | Zorunlu | Doğrulanamayan referans |
| 17 | `.ai/log.md` yalnız append; geçmiş satıra dokunulmaz | Yasak | Audit trail bozulması |
| 18 | Dosya adı onay olmadan değiştirilemez (In-Place Refactoring) | Yasak | Kırık wiki-link |

### §4.1 Durum Döngüsü

| Durum | Anlamı | Kim Değiştirir | Dokunulabilir mi? |
|-------|--------|----------------|-------------------|
| Draft | İlk taslak, alanlar doluyor | Yazar (Vault Steward / domain agent) | ✅ |
| Review | Onay bekliyor (Tech Lead) | Tech Lead notu ekler | ✅ (review notu) |
| Active | Onaylı, yürürlükte | Yalnız `superseded` geçişi | ⚠️ Sınırlı |
| Frozen | Kesin, değiştirilemez | Kimse | ❌ Yalnız okunur + referans |

### §4.2 Onay Akışı

```
Vault Steward (yazar, ✅) → Tech Lead (⏳ → ✅/❌) → Arch Lead (⏳ → ✅/❌) → Active
```

| Kademe | Rol | Timeout | Reddedilirse |
|--------|-----|---------|--------------|
| 1 | Vault Steward | — | Yazıma geri dön |
| 2 | Tech Lead | 60s | MO devreye girer (`[[../../AGENTS.md]]` §10) |
| 3 | Arch Lead | 120s | İnsana eskalasyon (L3 → İnsan) |

### §4.3 Neden Web Araştırması Zorunlu? (§1.3)

Karar, güncel ekosistem bilgisi olmadan alınırsa eski kalır. §1.3 tablosu 9 alanıyla araştırmanın **izlenebilir** olmasını sağlar: hangi sorgu çalıştı, hangi bağlam okundu, hangi sonuç karara dönüştü. Alanlardan herhangi biri `⚠️ VERIFICATION REQUIRED` kalabilir — ama tablo silinemez.

### §4.4 Risk Değerlendirme Ölçeği

| Ölçek | Olasılık | Etki | Aksiyon |
|-------|---------|------|---------|
| 1 | Nadir (<%10) | Ihmal edilebilir | Kaydet, izle |
| 2 | Mümkün (%10-40) | Düşük | Mitigasyon yaz |
| 3 | Olası (%40-70) | Orta | Mitigasyon + sahiplik ata |
| 4 | Çok olası (>%70) | Yüksek / Kritik | L2/L3 eskalasyonu |

### §4.5 Frozen'a Geçiş Kontrolü

ADR Active'ten Frozen'a geçmeden önce aşağıdaki kontrol tamamlanır; bir madde eksikse Frozen yapılmaz.

| # | Kontrol | Koşul |
|---|---------|-------|
| 1 | §7 Onay üç satırı da ✅ | Steward + Tech Lead + Arch Lead |
| 2 | §1.3 web araştırması dolu | 9 alan doldurulmuş |
| 3 | §5.2 geri dönüş planı yazılı | Boş değil |
| 4 | `.ai/.decisions/index.md` kayıt satırı eklendi | Dizin güncel |
| 5 | İlgili vault dosyalarına wiki-link eklendi | `[[ADR-NNN-...]]` bağlantıları |
| 6 | `log.md`'ye append yapıldı | Audit trail |
| 7 | `brain.md` özeti güncellendi | SSOT özeti taze |

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/adr/adr-template.md` | Şablon kopyası |
| 2 | KOPYALA | Hedefe `ADR-NNN-<slug>.md` adıyla kopyala (NNN = sıradaki numara, slug = karar özeti) | Yeni ADR iskeleti |
| 3 | DOLDUR | Durum/Tarih/Karar Veren + §1-§5 domain alanları + §7 Onay; §1.3 web araştırmasını gerçek sonuçlarla doldur | Dolu ADR |
| 4 | DOĞRULA | §6 kontrol listesi + onay akışı (Vault Steward → Tech Lead → Arch Lead) | 8/8 gate |
| 5 | COMMIT | İlgili wiki-link'leri (`[[ADR-NNN-...]]`) ilgili vault dosyalarına eklenir; `.ai/.decisions/index.md` + registry (`[[.templates/index]]`) + log güncellenir | Vault senkronu |

**Adım 3 detayı — doldurma sırası:** (a) künye (`{{TITLE}}`, `{{STATUS}}`, `{{DATE}}`, `{{AUTHOR}}`, `{{RELATED_ADRS}}`), (b) §1 Bağlam (1, 1.1, 1.2), (c) §1.3 web araştırması 9 alan, (d) §1.4 kısıtlamalar (en az 2 satır), (e) §2 Karar + 2.1 + 2.2, (f) §3 Alternatifler (en az 3 satır), (g) §4 Sonuçlar (olumlu/olumsuz/risk), (h) §5 Uygulama adımları + 5.2 geri dönüş planı, (i) §6 ilgili dokümanlar, (j) §7 onay tablosu ilk satırı.

---

## §6 Doğrulama

ADR commit edilmeden önce kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse commit yapılmaz.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + künye + §1-§7 iskeleti eksiksiz mi? | 7 bölüm silinmemiş | ✅/❌ |
| 3 | Dosya adı `ADR-NNN-<slug>.md` formatında mı? | NNN + slug | ✅/❌ |
| 4 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | Bağlam/Karar/Alternatifler/Sonuçlar/Uygulama/Onay dahil | ✅/❌ |
| 5 | §1.3 web araştırması 9 alan dolu mu? | Query…Sonuç | ✅/❌ |
| 6 | §1.4 kısıtlamalar en az 2 satır mı? | Kısıt + Açıklama | ✅/❌ |
| 7 | §3 Alternatifler en az 3 satır mı? | Artı/Eksi/Red gerekçesi | ✅/❌ |
| 8 | §4.3 Risk tablosu dolu mu? | Olasılık + Etki + Mitigasyon | ✅/❌ |
| 9 | §5.2 Geri dönüş planı yazılı mı? | Gerçek plan (boş olamaz) | ✅/❌ |
| 10 | §7 Onay akışı doğru mu? | Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ | ✅/❌ |
| 11 | `status` gerçek durumla mı? | Draft/Review/Active/Frozen | ✅/❌ |
| 12 | Frozen kuralı biliniyor mu? | Değiştirilmez, yalnız referans | ✅/❌ |
| 13 | SSOT self-claim yok mu? | Şablon `SSOT`; ADR kendi authority değeri | ✅/❌ |
| 14 | Wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]`, kırık link yok | ✅/❌ |
| 15 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru; slug İngilizce | ✅/❌ |
| 16 | Doğrulanamayan alan işaretlendi mi? | `⚠️ VERIFICATION REQUIRED` | ✅/❌ |
| 17 | Secret/credential yazılmadı mı? | REDACTED politikası | ✅/❌ |
| 18 | Envanter SRP ihlali yok mu? | Envanter `[[.templates/index]]`'de | ✅/❌ |
| 19 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

### §6.1 Doğrulama Aşamaları

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2, 3 | Frontmatter + iskelet + dosya adı |
| B | Bağlam | 4, 5, 6 | §1 + web araştırması + kısıtlar |
| C | Karar | 7, 8, 9 | Alternatifler + risk + geri dönüş |
| D | Onay | 10, 11, 12, 13 | Onay akışı + durum + frozen + authority |
| E | Kanıt & Protokol | 14-19 | Link/dil/REDACTED/YAGNI/SRP/DRY |

### §6.1.1 Her Aşamanın Kanıtı

| Aşama | Kanıt | Alınan Çıktı |
|-------|-------|--------------|
| A | Frontmatter okuma | 7 alan + iskelet §1-§7 + dosya adı `ADR-NNN-<slug>` |
| B | `grep '{{'` + §1.3 gözden geçirme | 0 placeholder + web araştırması 9 alan dolu |
| C | §3/§4/§5.2 tablo sayımı | Alternatif ≥3, sonuç üçlüsü, geri dönüş planı dolu |
| D | Onay satırı + `.ai/.decisions/index.md` | Vault Steward ✅ + registry kaydı |
| E | Link/dil/REDACTED taraması | Kırık link 0, mojibake 0, secret 0, SRP/DRY ✅ |

### §6.2 Red Sinyalleri (Otomatik DUR)

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| §7 Onay akışı eksik | Onaysız karar | DUR — Vault Steward ✅ al |
| §1.3 web araştırması boş | Gerekçesiz karar | DUR — 9 alanı doldur |
| §5.2 geri dönüş planı boş | Geri alınamaz karar | DUR — planı yaz |
| §3 Alternatifler < 3 satır | Gerekçe zayıf | DUR — alternatifleri ekle |
| Dosya adı `ADR-NNN-<slug>` değil | Numara/slug ihlali | DUR — yeniden adlandır (onayla) |
| Frozen ADR düzenlenmeye çalışıldı | §4.3 | Derhal revert + log ERROR |
| SSOT self-claim (ADR şablonu `SSOT` iddiasında) | §4.8 | DUR — authority'yi düzelt |
| Placeholder `{{...}}` kalmış | Yarım karar | DUR — doldur |
| Secret/credential içerdiği | REDACTED | DUR — maskele |
| Mojibake tespiti | Encoding | `vault-utf8-writer.mjs repair` |

### §6.3 Sürüm Geçmişi ve Supersede Kuralları

| Durum | Aksiyon |
|-------|---------|
| ADR revizyonu (Active iken, sınırlı) | `log.md` append + `.ai/.decisions/index.md` güncelle |
| Karar değişikliği | YENİ ADR yaz (`NNN+1`), eskisini `superseded by ADR-NNN+1` bağla |
| Karar geri alınırsa | Yeni ADR ile "revert of ADR-NNN" kaydet |
| Frozen metin | Hiçbir koşulda düzenlenmez |
| İlk satır (üretilen ADR'de) | `1.0.0 | {{DATE}} | Created` |

### §6.4 Read-Only Doğrulama Komutları

Yazma yasaklı kontroller (salt okunur); biri bile beklenen çıktıyı vermezse commit durur.

```bash
# 1) Placeholder kontrolü — 0 olmalı
grep -c '{{' <ADR-FILE>

# 2) Decisions dizini kayıtlı mı — index.md görünmeli
dir .ai\.decisions\

# 3) Frozen ADR diff'i — boş olmalı (frozen = immutabel)
git diff --stat -- .ai/
```

*(`<ADR-FILE>` gerçek dosya yolu ile değiştirilir. Frozen ADR'lerde beklenen diff: boş.)*

---

**REFACTOR REPORT:** FILE: adr-template.md · PURPOSE: Architecture Decision Record şablonu (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (18 kural, 19 doğrulama, §1.3 web araştırması korundu) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Onay/escalation §10, frozen kuralı §25.3, wiki-link kanıtları |
| Ana sözleşme (iskelet §6) | [[CLAUDE.md]] | İlgili Dokümanlar tablosu |
| Mimari kararlar (iskelet §6) | [[brain.md]] | ADR özetleri |
| Süreçler (iskelet §6) | [[WORKFLOW.md]] | Fazlar, session protokolü |
| Karar dizini | `[[decisions/index]]` | `.ai/.decisions/index.md` (glob kanıtı) |
| Karar alt registry | `.ai/.decisions/CLAUDE.md` | Dizin kuralı |
| Karar durumları | `.ai/.decisions/accepted/`, `draft/`, `rejected/` | Alt klasörler (her birinde `CLAUDE.md`) |
| PHP backend (ADR uygulayıcı) | [[../backend/php-template]] | ADR-002/010/011/012/013/022/040 kod iskeleti |
| Node.js backend | [[../backend/nodejs-template]] | ADR-013 rate limit uygulayıcısı |
| Güvenlik denetimi | [[../documentation/security-audit-template]] | ADR-010/011/012/022 denetimi |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
