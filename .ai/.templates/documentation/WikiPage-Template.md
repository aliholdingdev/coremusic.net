---
title: "CoreMusic — Wiki Page Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# CoreMusic — Wiki Page Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

CoreMusic vault wiki sayfaları için standart şablonu sağlamaktır: başlık/kategori/durum/meta bilgisini; genel bakış ve detay bölümlerini; ilgili sayfalar tablosunu ve değişiklik geçmişini tek iskelette toplar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`. **Guardrail #16:** yeni wiki sayfası bu şablondan başlar; şablonsuz sayfa vault'a eklenemez.

| Alan | Değer |
|------|-------|
| Template Name | `WikiPage-Template.md` |
| Template Path | `.ai/.templates/documentation/WikiPage-Template.md` |
| Hedef Dosya Tipi | Markdown wiki sayfası |
| Hedef Konum | `.ai/` vault altı (herhangi bir alt dizin) |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Master Orchestrator / vault-updater |
| İkincil Yazarlar | Tüm agent'lar (okuma serbest; yazma domain sahibine ait) |
| Routing | `[[../../AGENTS.md]]` §6: vault, documentation, wiki-link, index → MO (vault-updater) |
| İskelet | H1 + §1 Genel Bakış → §5 İlişkiler (5 bölüm) |
| Zorunlu Tablolar | §4 İlgili Sayfalar (en az 2 satır) + §5 Değişiklik Geçmişi (append-only) |
| Kayıt Kuralı | Değişiklik Geçmişi append-only — mevcut satıra dokunulmaz |
| Kanıt — Vault | `.ai/*.md` kök dosyaları (CLAUDE.md, AGENTS.md, WORKFLOW.md, brain.md, glossary.md, index.md) |
| Kanıt — Alt Dizinler | `.ai/.agents/`, `.ai/.decisions/`, `.ai/.templates/`, `.ai/.workflows/`, `.ai/.sql/` |
| Korunan Bilgi | Wiki-link formatı, append-only kuralı, 7 alanlı frontmatter |
| Değişken Formatı | `{{VARIABLE}}` (TITLE, CATEGORY, STATUS, DATE, AUTHOR, OVERVIEW_DESCRIPTION, DETAIL_CONTENT, RELATED_PAGE_*) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/kod adı İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); üretilen sayfa kendi authority değerini taşır |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-23 |

### §1.1 Wiki Sayfası ile Diğer Dosya Tipleri Arasındaki Fark

Wiki sayfası **taşıyıcıdır**: bilgi üretmez, bilgiyi düzenler ve bağlar. Bu yüzden ADR, rapor veya kod üretmek isteyen bir görev yanlış şablonu seçmemelidir.

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| Mimari karar kaydı | `[[../adr/adr-template]]` | ❌ |
| Güvenlik denetim raporu | `[[./security-audit-template]]` | ❌ |
| API dokümanı | `[[./api-doc-template]]` | ❌ |
| Agent profili | `[[../agents/agents-template]]` | ❌ |
| PHP/Node.js kod iskeleti | `[[../backend/*]]` | ❌ |
| Vault içinde genel bilgi sayfası | ✅ Bu şablon | — |
| Mimari/ekosistem/tanım/kılavuz sayfası | ✅ Bu şablon | — |

### §1.2 Sayfa Yaşam Döngüsü

| Durum | Anlamı | Yazma Yetkisi |
|-------|--------|---------------|
| Taslak | İçerik doluyor | Yazar (domain agent / MO) |
| Aktif | Güncel ve teyitli | Domain sahibi + MO |
| Eski | Yenisiyle değiştirildi | Yalnız üst geçiş notu |
| Frozen referans | ADR/ karar özeti | Okunur, düzenlenmez |

### §1.3 Çapraz Referans Ağı

Wiki-link'ler rastgele değil, **aşağıdan yukarı** bağlanır: sayfa → kategori indeksi → master katalog → boot dosyaları. Kopuk halka tespit edildiğinde §6.2'deki red sinyalleri devreye girer.

| Katman | Örnek Hedef | Rol |
|--------|-------------|-----|
| 0 Boot | `[[../CLAUDE.md]]`, `[[../../AGENTS.md]]` | Ana otoriteler |
| 1 Kategori | `[[.templates/index]]`, `[[../../index.md]]` | İndeks/registry |
| 2 Kardeş | `[[./api-doc-template]]`, `[[../adr/adr-template]]` | Aynı seviye şablonlar |
| 3 İçerik | Sayfanın kendi konu bağlantıları | Bilgi ağı |
| 4 Kanıt | `glob` ile doğrulanmış yollar | YAGNI zemini |

---

## §2 Kapsam

Wiki sayfasının kapsadığı ve kapsam dışında bıraktığı alanlar. Wiki sayfası **dokümandır**: kod, ADR veya rapor üretmez, yalnız bilgi taşır ve çapraz referans kurar.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/` vault içindeki genel wiki sayfaları | ADR metinleri (frozen — okunur, referanslanır; bkz. `[[../adr/adr-template]]`) |
| Genel bakış + detay + ilişkiler + değişiklik geçmişi | Kaynak kod dosyaları (`.php`, `.js`, `.ts`, `.cpp`) |
| Wiki-link ile çapraz referans (`[[relative/path]]`) | Denetim/rapor şablonları (→ `[[./security-audit-template]]`, `[[./api-doc-template]]`) |
| 7 alanlı frontmatter + künye | Agent profilleri (→ `[[../agents/agents-template]]`) |
| Append-only Değişiklik Geçmişi | Backend/frontend kod iskeletleri (→ `[[../backend/*]]`, `[[../frontend/*]]`) |
| Türkçe doğruluk + mojibake denetimi | Envanter listesi (→ `[[.templates/index]]`, SRP) |
| `{{PLACEHOLDER}}` doldurma + Guardrail #16 doğrulaması | `.ai/log.md` append-only kayıt (üst görevin işi) |
| Yeni sayfa eklendiğinde registry + indeks güncellemesi | Skill tanımları (`.opencode/skills/*/SKILL.md`) |

**Dosya tipi:** Markdown wiki sayfası · **Uzantı:** `.md` · **Konum:** `.ai/` · **Guardrail:** #16

### §2.1 Vault İçi Konum Kararı

Yeni bir sayfa hangi dizine konur? Karar verilmez, aşağıdaki tablodan okunur.

| İçerik Türü | Hedef Konum | Şablon |
|-------------|-------------|--------|
| Genel bilgi / kılavuz / tanım | `.ai/<sayfa>.md` | ✅ Bu şablon |
| Alt alan dokümanı | `.ai/<alt-dizin>/<sayfa>.md` | ✅ Bu şablon |
| Agent profili | `.ai/.agents/<agent>.md` | ❌ `[[../agents/agents-template]]` |
| ADR kararı | `.ai/.decisions/ADR-NNN-<slug>.md` | ❌ `[[../adr/adr-template]]` |
| Şablon dosyası | `.ai/.templates/<kategori>/<şablon>.md` | ❌ Guardrail #16 ailesi |
| Workflow akışı | `.workflows/<akış>.md` | ❌ Akış dosyası |
| Skill tanımı | `.opencode/skills/<skill>/SKILL.md` | ❌ Skill dosyası |

### §2.2 Disk Kanıtları (YAGNI)

| Kanıt | Yol | Durum |
|-------|-----|-------|
| Vault kök dosyaları | `.ai/*.md` (CLAUDE, AGENTS, WORKFLOW, brain, glossary, index) | ✅ MEVCUT |
| Alt dizinler | `.ai/.agents/`, `.ai/.decisions/`, `.ai/.templates/`, `.ai/.sql/` | ✅ MEVCUT |
| Workflow'lar | `.workflows/*.md` | ✅ MEVCUT (8 dosya) |
| Şablonlar | `.ai/.templates/**/*.md` | ✅ MEVCUT (28 dosya) |
| `vault-cmd.mjs` scripti | `.ai/scripts/vault-cmd.mjs` | ⚠️ DOĞRULANACAK — yoksa manuel denetim · ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel |
| Uydurma dizin/yol | — | ❌ YAZILMAZ |

### §2.3 Sayfa Boyutu ve Parçalama

| Eşik | Değer | Aksiyon |
|------|-------|---------|
| Hedef boyut | 300-600 satır | Tek sayfada kal |
| Üst sınır | 900 satır | `####` alt bölümlere böl veya konuyu iki sayfaya ayır |
| Tablo genişliği | ≤5 sütun | Daha fazlaysa iki tabloya böl |
| Paralel sayfa | Aynı konu 2+ sayfa | Birleştir; eskisine üst geçiş notu (§1.2 "Eski") |
| Bölme sonrası | Yeni sayfa oluştu | §5.3 adımları (indeks + log) zorunlu |

---

## §3 Mimari

Şablonun tam gövdesi. Gömme nedeniyle başlıklar iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}` ve tablolar birebir korunmuştur.

### {{TITLE}}

**Kategori:** {{CATEGORY}}
**Durum:** {{STATUS}}
**Son Güncelleme:** {{DATE}}
**Yazar:** {{AUTHOR}}

---

#### §3.1 Genel Bakış

{{OVERVIEW_DESCRIPTION}}

Bu bölüm tek paragraf (2-4 cümle) olur: sayfanın neyi, kimin için ve hangi kapsamda ele aldığını söyler. Ayrıntı §4'tedir.

---

#### §3.2 Detay

{{DETAIL_CONTENT}}

Bu bölüm konuya göre serbesttir; ancak en az bir tablo veya bir kod bloğu içerir (tablo ağırlıklı anlayış). Alt başlıklar en fazla iki seviye derindir (H1 → `###`, H2 → `####`, H3 → `#####`).

---

#### §3.3 Kullanım Notları

| Not | Açıklama |
|-----|----------|
| Bağlam | Bu sayfayı kim, hangi görevde okur? |
| Kapsam Sınırı | Sayfa hangi konuyu içermez? |
| Güncellik | Bilgi hangi tarihe ait, teyit edildi mi? |
| Belirsizlik | Doğrulanamayan iddia var mı → `⚠️ VERIFICATION REQUIRED` |
| İlişki | Komşu sayfalara wiki-link ile mi bağlı? |

### §3.4 Başlık Hiyerarşisi Kuralı

Sayfa içi başlık seviyesi 3'ü geçmez; gömme nedeniyle H1 → `###`, H2 → `####` biçimindedir.

| Seviye | Markdown | Kullanım | Azami Derinlik |
|--------|----------|----------|----------------|
| Sayfa başlığı | `### {{TITLE}}` | Tek sayfa H1'i | 1 |
| Ana bölüm | `#### §3.x` | Genel bakış / detay / notlar | 1 |
| Alt bölüm | `##### Konu` | Detay içi konular | ≤2 |

### §3.5 Künye Alanları

| Alan | Zorunlu | Format | Not |
|------|---------|--------|-----|
| `{{TITLE}}` | Evet | Serbest metin | Türkçe başlık |
| `{{CATEGORY}}` | Evet | Küme değeri | Kategori adı |
| `{{STATUS}}` | Evet | Taslak/Aktif/Eski | §1.2 durum kümesi |
| `{{DATE}}` | Evet | `YYYY-MM-DD` | Son güncelleme |
| `{{AUTHOR}}` | Evet | Agent adı | Sorumlu |

### §3.6 Doldurulmuş Örnek Sayfa (İskelet Görünümü)

Aşağıdaki örnek, şablonun gerçek çıktı biçimidir; `{{...}}` alanları doldurulmuş hâli gösterir.

```markdown
---
title: "CoreMusic — Vault Yazım Protokolü"
type: guide
category: documentation
date: 2026-09-06
updated: 2026-09-23
version: 1.2.0
status: active
authority: SSOT
---

# CoreMusic — Vault Yazım Protokolü

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[.templates/index]] · [[log.md]]

### Vault Yazım Protokolü

**Kategori:** documentation
**Durum:** Aktif
**Son Güncelleme:** 2026-09-23
**Yazar:** MO (vault-updater)

---

#### §3.1 Genel Bakış

Bu sayfa, `.ai/` vault dosyalarının UTF-8 yazım ve onarım protokolünü tanımlar.
Yazma işlemleri yalnızca onaylı script ile yapılır; PowerShell yazım cmdlet'leri yasaktır.

---

#### §3.2 Detay

| İşlem | Komut | Not |
|-------|-------|-----|
| Ekleme | `node .ai/scripts/vault-utf8-writer.mjs append` | log.md yalnız append |
| Onarım | `node .ai/scripts/vault-utf8-writer.mjs repair` | Yedek alır |
| Doğrulama | `node .ai/scripts/vault-utf8-writer.mjs verify` | Yazım sonrası |

---

#### §3.3 Kullanım Notları

| Not | Açıklama |
|-----|----------|
| Bağlam | Yazma hatası alan her görev |
| Kapsam Sınırı | Kod dosyaları değil, vault `.md` dosyaları |
| Güncellik | 2026-09-23 teyitli |
| Belirsizlik | Script yolu `⚠️ VERIFICATION REQUIRED` (script yoksa) |
| İlişki | `[[CLAUDE.md]]` Guardrail kaynağı |

---

### Vault Yazım Protokolü — İlgili Sayfalar

| Sayfa | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Guardrail kaynağı |
| [[.templates/index]] | Şablon kaydı |

---

### Vault Yazım Protokolü — Değişiklik Geçmişi

| Tarih | Değişiklik | Sorumlu |
|-------|-----------|---------|
| 2026-09-06 | İlk oluşturma | MO (vault-updater) |
```

**Korunan öğeler:** 7 alanlı frontmatter · `**Zorunlu Bağlantılar:**` · `{{VARIABLE}}` → gerçek değer · append-only geçmiş ilk satırı · wiki-link biçimli iç bağlantılar.

---

## §4 Kurallar

Wiki sayfası yazımında uygulanan zorunlu/yasak kurallar. Çapraz referans bütünlüğü bu kurallarla korunur.

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | İç bağlantılar wiki-link formatında yazılır: `[[hedef]]`; `.ai/` dışı kaynaklar için `[[../...]]` göreli biçim | Zorunlu | Kırık çapraz referans |
| 2 | §5 İlgili Sayfalar tablosu en az 2 satır içerir; her satırda ilişki açıklanır | Zorunlu | İzole sayfa |
| 3 | §6 Değişiklik Geçmişi append-only'dir — mevcut satıra dokunulmaz, yeni satır eklenir | Zorunlu | Audit trail bozulması |
| 4 | Frontmatter 7 alan (title, type, category, date, updated, version, status, authority) eksiksiz yazılır | Zorunlu | Frontmatter hatası |
| 5 | `{{TITLE}}`, `{{CATEGORY}}`, `{{STATUS}}`, `{{DATE}}`, `{{OVERVIEW_DESCRIPTION}}`, `{{DETAIL_CONTENT}}`, `[[{{RELATED_PAGE_*}}]]`, `{{AUTHOR}}` doldurulmadan commit edilemez | Yasak | Yarım sayfa |
| 6 | Kırık/wiki-link hedefi olmayan bağlantı bırakılamaz; hedef bilinmiyorsa `⚠️ VERIFICATION REQUIRED` yazılır | Yasak | Hallucination |
| 7 | ADR metinleri frozen'dır — bu şablonla ADR içeriği düzenlenmez, yalnız referanslanır | Yasak | Frozen ihlali → revert |
| 8 | `.ai/log.md` yalnız append; geçmiş satıra dokunulmaz | Yasak | Audit trail bozulması |
| 9 | Yeni sayfa eklendiğinde registry (`[[.templates/index]]`) + vault indeks + log güncellenir | Zorunlu | Kayıt düşüklüğü |
| 10 | Dosya adı onay olmadan değiştirilemez (In-Place Refactoring) | Yasak | Kırık wiki-link ağı |
| 11 | Envanter listesi bu şablonda tekrarlanmaz (SRP) → `[[.templates/index]]` | Yasak | İkincil kaynak çelişkisi |
| 12 | Routing/vault değerleri `[[../../AGENTS.md]]` dosyasından okunur (DIP) | Zorunlu | SSOT çelişkisi |
| 13 | Aynı kategori § başlıkları birebir aynıdır (DRY): §1 Amaç → §7 Referanslar | Zorunlu | Şablon tutarsızlığı |
| 14 | Türkçe karakterler (ç ğ ı İ ö ş ü) doğru; mojibake YASAK; dosya adı İngilizce | Zorunlu | Mojibake → onarım |
| 15 | Secret / credential yazılmaz (REDACTED politikası) | Yasak | Güvenlik ihlali |
| 16 | Uydurma dosya/yol adı yazılmaz; iddia yalnız glob kanıtıyla (YAGNI) | Zorunlu | Doğrulanamayan referans |

### §4.1 Wiki-Link Formatı

| Durum | Format | Örnek |
|-------|--------|-------|
| Aynı dizin | `[[dosya]]` | `[[CLAUDE.md]]` |
| Üst dizin | `[[../dosya]]` | `[[../CLAUDE.md]]` |
| Alt dizin | `[[alt/dosya]]` | `[[.templates/index]]` |
| İki üst | `[[../../dosya]]` | `[[../../AGENTS.md]]` |
| Uzantısız yol | `[[relative/path/to/file]]` | `[[decisions/index]]` |
| Fragment yok | Vault içi standart | `[[#section]]` kullanılmaz |
| Bilinmeyen hedef | İşaretli | `⚠️ VERIFICATION REQUIRED` |

### §4.2 Sık Yapılan Hatalar

| Hata | Yanlış | Doğru |
|------|--------|-------|
| Mutlak yol | `[[../../CLAUDE.md]]` | `[[../CLAUDE.md]]` (göreli) |
| Eklenti tekrarı | `[[file.md]]` vs `[[file]]` | Vault içi tutarlı biçim |
| Hedefsiz link | `[[olmayan-dosya]]` | `⚠️ VERIFICATION REQUIRED` veya sil |
| ADR düzenleme | frozen metni değiştirmek | Yalnız `[[ADR-NNN-...]]` referansı |
| Envanter tekrarı | sayfada dosya listesi | `[[.templates/index]]`'e yönlendir |

### §4.3 Kural Grupları

| Grup | Kurallar | Ağırlık | İhlalde |
|------|----------|---------|---------|
| Bağlantı bütünlüğü | 1, 6, 12 | HIGH | DUR — kırık link / DIP ihlali |
| Yapı & format | 4, 10, 13, 14 | HIGH | DUR — frontmatter/DRY/mojibake |
| Protokol | 3, 8, 9, 15 | CRITICAL | DUR — audit trail / REDACTED |
| Envanter & kanıt | 11, 16 | MEDIUM | DUR — SRP / YAGNI |
| Zorunlu içerik | 2, 5, 7 | HIGH | DUR — eksik tablo / placeholder / frozen |

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/documentation/WikiPage-Template.md` (Guardrail #16) | Şablon kopyası |
| 2 | KOPYALA | Dosyayı hedef wiki konumuna (`.ai/` altı) kopyala | Yeni sayfa iskeleti |
| 3 | DOLDUR | `{{TITLE}}`, `{{CATEGORY}}`, `{{STATUS}}`, `{{DATE}}`, `{{AUTHOR}}`; §3.1 `{{OVERVIEW_DESCRIPTION}}`; §3.2 `{{DETAIL_CONTENT}}`; §5 `[[{{RELATED_PAGE_*}}]]` + ilişkiler; §6 `{{DATE}}`/`{{AUTHOR}}` | Dolu wiki sayfası |
| 4 | DOĞRULA | §7 kontrol listesi: 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + tüm wiki-link'ler hedefe ulaşıyor | 8/8 gate |
| 5 | COMMIT | Sayfayı commit et; yeni sayfa ise `[[.templates/index]]`/vault indeksine ekle ve `log.md`'ye giriş yaz | Vault senkronu |

**Adım 3 detayı — doldurma sırası:** (a) künye (`{{TITLE}}`, `{{CATEGORY}}`, `{{STATUS}}`, `{{DATE}}`, `{{AUTHOR}}`), (b) §3.1 genel bakış (2-4 cümle), (c) §3.2 detay (en az 1 tablo/kod bloğu), (d) §3.3 kullanım notları, (e) §5 ilgili sayfalar (en az 2 satır, gerçek wiki-link'ler), (f) §6 değişiklik geçmişi ilk satır (`{{DATE}} | İlk oluşturma | {{AUTHOR}}`), (g) §7 referanslar.

**Adım 4 doğrulama komutu (read-only):**

```bash
node .ai/scripts/vault-cmd.mjs chk
# ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel
```

*(Script yoksa `⚠️ VERIFICATION REQUIRED` — manuel link denetimi yapılır.)*

### §5.1 Append-Only Değişiklik Geçmişi Örneği

Yeni sürüm eklendiğinde **eski satırlar aynen kalır**, yalnız yeni satır eklenir.

| Tarih | Değişiklik | Sorumlu |
|-------|-----------|---------|
| 2026-09-06 | İlk oluşturma | {{AUTHOR}} |
| 2026-09-23 | İçerik genişletildi (§3.3 notlar) | vault-updater |
| {{DATE}} | {{CHANGE_DESCRIPTION}} | {{AUTHOR}} |

**Yasak:** yukarıdaki tabloda bir satırın "Değişiklik" hücresini değiştirmek. **Doğru:** yeni satır ekle.

### §5.2 İlgili Sayfa Tablosu Kuralları

| Kural | Değer |
|-------|-------|
| Minimum satır | 2 |
| Hedef formatı | `[[relative/path]]` |
| İlişki sütunu | Her satırda açıklayıcı (ne anlam taşır) |
| Boş ilişki | `{{RELATION_*}}` placeholder'ı bırakılamaz |
| Hedefsiz link | `⚠️ VERIFICATION REQUIRED` veya sil |

### §5.3 Yeni Sayfa Ekleme Adımları

| # | Adım | Dosya | Not |
|---|------|-------|-----|
| 1 | Şablonu kopyala | `.ai/.templates/documentation/WikiPage-Template.md` | Guardrail #16 |
| 2 | Hedefe yaz | `.ai/<sayfa>.md` (§2.1 tablosu) | Dosya adı onaylı |
| 3 | Placeholder doldur | Sayfa içi `{{...}}` | §7 kontrolü |
| 4 | Bağlantıları kur | İlgili sayfalara karşılıklı `[[...]]` | Kopuk halka yok |
| 5 | İndekse ekle | `[[../../index.md]]` / `[[.templates/index]]` | SRP |
| 6 | Append log | `.ai/log.md` | Append-only |

### §5.4 Handover Tetikleyicileri

| Tetikleyici | Kaynak | Hedef | Öncelik |
|-------------|--------|-------|---------|
| Sayfa başka domain'in konusunu kapsıyor | Yazar | Domain sahibi agent | MEDIUM |
| Kırık wiki-link hedefi | Yazar | MO (vault-updater) | MEDIUM |
| Frozen ADR düzenleme isteği | Yazar | Vault Steward (§4.7) | HIGH |
| Mojibake / encoding | Yazar | MO (`vault-utf8-writer.mjs`) | LOW |
| Yeni sayfa + registry kararı | Yazar | Vault Steward | MEDIUM |

### §5.5 Commit Öncesi Son Adım Özeti

| # | Adım | Beklenen kanıt | Dur |
|---|------|----------------|-----|
| 1 | Placeholder taraması | `grep '{{' <dosya>` → 0 | ⛔ |
| 2 | Link denetimi | Her `[[...]]` hedefi diskte var | ⛔ |
| 3 | Frontmatter sayımı | 7 alan eksiksiz | ⛔ |
| 4 | Geçmiş diff'i | Eski satır değişmedi (append-only) | ⛔ |
| 5 | Envanter kontrolü | Yeni şablon ise `[[.templates/index]]`'te | ⛔ |
| 6 | log append | Yeni satır eklendi, eski korundu | ⛔ |

---

## §6 Doğrulama

Sayfa commit edilmeden önce kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse commit yapılmaz.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + §1-§7 iskeleti eksiksiz mi? | 7 bölüm silinmemiş | ✅/❌ |
| 3 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | TITLE, CATEGORY, STATUS, DATE, AUTHOR, OVERVIEW, DETAIL | ✅/❌ |
| 4 | §5 İlgili Sayfalar en az 2 satır mı? | Her satırda ilişki açıklaması | ✅/❌ |
| 5 | §6 Değişiklik Geçmişi append-only mi? | İlk satır + yeni satırlar; eski satır değişmedi | ✅/❌ |
| 6 | Tüm wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]`, kırık link yok | ✅/❌ |
| 7 | En az 1 tablo/kod bloğu var mı? | §3.2 tablo ağırlıklı | ✅/❌ |
| 8 | Başlık seviyesi ≤3 mü? | H1 + § + alt başlık | ✅/❌ |
| 9 | ADR metni düzenlenmedi mi? | Frozen — yalnız referans | ✅/❌ |
| 10 | Secret/credential yazılmadı mı? | REDACTED politikası | ✅/❌ |
| 11 | Uydurma dosya/yol adı yok mu? | YAGNI — glob kanıtlı | ✅/❌ |
| 12 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru | ✅/❌ |
| 13 | Doğrulanamayan alan işaretlendi mi? | `⚠️ VERIFICATION REQUIRED` | ✅/❌ |
| 14 | Yeni sayfa ise registry+indeks+log güncellendi mi? | Değişiklik Protokolü | ✅/❌ |
| 15 | Dosya adı değişmedi mi? | In-Place Refactoring | ✅/❌ |
| 16 | Envanter SRP ihlali yok mu? | Envanter `[[.templates/index]]`'de | ✅/❌ |
| 17 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

### §6.1 Doğrulama Aşamaları

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2, 8 | Frontmatter + iskelet + başlık seviyesi |
| B | İçerik | 3, 7 | Placeholder doldu + en az 1 tablo/kod |
| C | İlişki | 4, 6 | ≥2 satır + link'ler hedefe ulaşıyor |
| D | Protokol | 5, 9, 14, 15 | Append-only, frozen, kayıt, dosya adı |
| E | Kanıt & Dil | 10, 11, 12, 13 | REDACTED, YAGNI, dil, VERIFY |
| F | Tutarlılık | 16, 17 | SRP + DRY |

### §6.1.1 Aşama Kanıtları

| Aşama | Kanıt | Alınan Çıktı |
|-------|-------|--------------|
| A | Frontmatter okuma | 7 alan + §1-§7 |
| B | `grep '{{'` + tablo sayımı | 0 placeholder + ≥1 tablo |
| C | Link denetimi | ≥2 `[[...]]`, hedefsiz 0 |
| D | Git/append farkı | Eski satır değişmedi |
| E | Dil + REDACTED taraması | mojibake 0, secret 0 |
| F | Kategori § eşlemesi | §1 Amaç → §7 Referanslar |

### §6.2 Red Sinyalleri (Otomatik DUR)

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| §5 İlgili Sayfalar < 2 satır | İzole sayfa | DUR — en az 2 gerçek `[[...]]` ekle |
| §6 geçmişte satır düzenlendi | Append-only ihlali | DUR — orijinali geri yükle, yeni satır ekle |
| Hedefi olmayan `[[link]]` | Kırık referans | DUR — hedefi bul veya `⚠️ VERIFICATION REQUIRED` |
| Placeholder `{{...}}` kalmış | Yarım sayfa | DUR — doldur |
| ADR metni düzenlenmeye çalışıldı | Frozen | Derhal revert + log ERROR |
| Başlık seviyesi > 3 | Format | DUR — hiyerarşiyi düzelt |
| Uydurma dosya/yol adı | YAGNI | DUR — glob kanıtı yoksa sil |
| Secret/credential içerdiği | REDACTED | DUR — maskele |
| Dosya adı değişti | In-Place Refactoring | DUR — eski ada dön (onaysız yasak) |
| Mojibake tespiti | Encoding | `vault-utf8-writer.mjs repair` |
| Yeni sayfa ama indeks/log yok | Değişiklik Protokolü | DUR — §5.3 adım 5-6 |

### §6.3 Sayfa Kalite Ölçütleri

| Ölçüt | Hedef | Ölçüm |
|-------|-------|-------|
| Bağlantı yoğunluğu | ≥3 wiki-link | §5 + §7 sayımı |
| Tablo ağırlığı | Her bölüm ≥1 tablo/kod | §3 + §6 gözlem |
| Başlık derinliği | ≤3 seviye | Markdown gezinme |
| Türkçe doğruluk | %100 (ç ğ ı İ ö ş ü) | mojibake taraması |
| Tazelik | `{{DATE}}` = son değişiklik | Frontmatter |
| Doğrulanabilirlik | Uydurma iddia 0 | YAGNI denetimi |

### §6.4 Read-Only Doğrulama Komutları

Yazma yasaklı kontroller (salt okunur); biri bile beklenen çıktıyı vermezse commit durur.

```bash
# 1) Placeholder kontrolü — 0 olmalı
grep -c '{{' <WIKI-PAGE-FILE>

# 2) Wiki-link sayısı — §5 + §7 ≥3 beklenir
grep -o '\[\[[^]]*\]\]' <WIKI-PAGE-FILE>

# 3) Frontmatter alanı — 7 satır görünmeli
grep -E '^(title|type|category|date|updated|version|status|authority):' <WIKI-PAGE-FILE>

# 4) Mojibake taraması — sonuç 0 olmalı
grep -aP 'Ã|Â|\x{FFFD}' <WIKI-PAGE-FILE>
```

*(`<WIKI-PAGE-FILE>` gerçek dosya yolu ile değiştirilir; script varsa `node .ai/scripts/vault-cmd.mjs chk` tercih edilir. ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel.)*

---

**REFACTOR REPORT:** FILE: WikiPage-Template.md · PURPOSE: Wiki Page Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (wiki-link formatı, append-only, 17 doğrulama) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Routing §6 (vault → MO), append-only kuralı §25.3 |
| Ana sözleşme (vault) | `.ai/CLAUDE.md` | frontmatter `reference` |
| Agent registry (vault) | `.ai/AGENTS.md` | frontmatter `reference` |
| Mimari kararlar (vault) | `.ai/brain.md` | frontmatter `reference` |
| Master katalog | [[../../index.md]] | Vault indeks dizini |
| ADR şablonu | [[../adr/adr-template]] | Frozen ADR referans formatı |
| Denetim şablonu | [[./security-audit-template]] | Rapor tipi sayfalar |
| API şablonu | [[./api-doc-template]] | Doküman tipi sayfalar |
| Kaynak dokümanı | `reference_doc: Freelancer Technical Documentation v1.0` | Format kaynağı |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
