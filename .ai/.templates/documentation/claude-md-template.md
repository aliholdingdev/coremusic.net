---
title: "CoreMusic — CLAUDE.md Üretim Şablonu"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
---

# CoreMusic — CLAUDE.md Üretim Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

Bu şablon, CoreMusic ekosisteminde bir **CLAUDE.md anayasa/kılavuz dosyası** üretmek isteyen AI ajanının ve insan geliştiricinin uyması gereken dış iskeleti, zorunlu kural bloklarını ve doğrulama listesini tanımlar. CLAUDE.md, bir dizinde çalışan tüm AI ajanlarının ilk okuduğu yönerge dosyasıdır; yanlış yazılan bir CLAUDE.md tüm oturumları yanlış yönlendirir. Bu yüzden dosya, `[[.templates/index]]` registry'sine kayıtlı bu şablondan üretilir (Guardrail #16 — Template Mandatory).

| Alan | Değer |
|------|-------|
| Template Name | `claude-md-template.md` |
| Template Path | `.ai/.templates/documentation/claude-md-template.md` |
| Hedef Dosya Tipi | `CLAUDE.md` (AI yönerge / anayasa dosyası) |
| Hedef Konum | Proje kökü veya herhangi bir alt dizin (`./CLAUDE.md`) |
| Guardrail | #16 (Template Mandatory) — şablonsuz CLAUDE.md üretilemez |
| Birincil Yazar | Master Orchestrator (vault-updater) |
| İkincil Yazarlar | Tüm agent'lar (okuma serbest; yazma domain sahibine ait) |
| Routing | `[[../../AGENTS.md]]` §6: vault, documentation, CLAUDE.md → MO (vault-updater) |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter) |
| Zorunlu Blok | §3.3 Şablon-Önce Kural Bloğu (üretilen dosyada SİLİNEMEZ) |
| Zorunlu Tablolar | §6.1 Doğrulama listesi (en az 10 satır) + §7.2 Değişiklik Geçmişi (append-only) |
| Kayıt Kuralı | Değişiklik Geçmişi append-only — mevcut satıra dokunulmaz |
| Kanıt — Vault | `.ai/CLAUDE.md` (anayasa), `.ai/AGENTS.md` (agent routing), `.ai/.templates/index.md` (registry) |
| Kanıt — Kural | Guardrail #16 (`.ai/CLAUDE.md` §7), Şablon-Önce ilkesi (bu şablon §3.3) |
| Korunan Bilgi | 7 alanlı frontmatter, wiki-link formatı, 500+ derinlik standardı, REDACTED |
| Değişken Formatı | `{{VARIABLE}}` (TITLE, PROJECT_NAME, DATE, AUTHOR, SCOPE_DESCRIPTION, RULE_TABLE_ROWS, READING_LIST, VIOLATION_CONSEQUENCE) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya adı `CLAUDE.md` (İngilizce) |
| Versiyon | 2.0.0 (ilk üretim — 2026-09-24) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-24 |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

CLAUDE.md, **yönerge dosyasıdır**: kuralları bildirir, kod üretmez. Görev sırasında hangi dosyanın üretileceğine karar verirken aşağıdaki ayrım zorunludur.

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| Bir dizin için AI yönerge/anasayfa dosyası | ✅ Bu şablon | — |
| Proje kökü AI anayasası (kurallar + guardrail) | ✅ Bu şablon | — |
| Genel dokümantasyon .md (README, AGENTS, rehber) | `[[./docs-md-template]]` | ❌ |
| Vault wiki sayfası | `[[./WikiPage-Template]]` | ❌ |
| Mimari karar kaydı (ADR) | `[[../adr/adr-template]]` | ❌ |
| API dokümanı | `[[./api-doc-template]]` | ❌ |
| Güvenlik denetim raporu | `[[./security-audit-template]]` | ❌ |
| PHP/JS/CSS kod iskeleti | `[[../backend/*]]`, `[[../frontend/*]]` | ❌ |

**Kural:** Dosya adı `CLAUDE.md` ise bu şablon; dosya adı başka bir `.md` ise `docs-md-template` veya ilgili kategori şablonu geçerlidir.

### §1.2 Şablon-Önce Kuralı (Temel İlke)

Bu şablonun taşıdığı en kritik ilke, **üretilen her CLAUDE.md'nin de kendi içine gömdüğü zorunlu kuraldır**: AI, herhangi bir dosyayı yazmadan önce `.ai/.templates/` dizinindeki ilgili şablonları okumak zorundadır. Kural, Guardrail #16'nın operasyonel karşılığıdır ve şablonun §3.3'ünde birebir kopyalanacak metin olarak verilmiştir.

| Boyut | Açıklama |
|-------|----------|
| Ne zaman | Her dosya yazımından ÖNCE (plan adımından hemen sonra) |
| Nereden | `.ai/.templates/index.md` → ilgili kategori klasörü → şablon dosyası |
| Ne yapılacak | Şablon varsa ona göre yaz; yoksa standart 7- §/8-bölüm formatına göre yaz |
| İhlal sonucu | Dosya geçersiz sayılır, revert edilir, `log.md`'ye ERROR girilir (Guardrail #16) |
| İstisna | `session-log-template.md` gibi kısa şablonlarda 500+ derinlik aranmaz; iskelet yine korunur |

---

## §2 Kapsam

### §2.1 Kullanım Anları

| # | Kullanım Anı | Sorumlu | Not |
|---|--------------|---------|-----|
| 1 | Yeni proje/dizin ilk kez AI'a açılırken | MO (vault-updater) | Proje köküne CLAUDE.md |
| 2 | Mevcut CLAUDE.md bayatlamış, çelişkili olmuşsa | MO (vault-updater) | In-Place güncelleme, dosya adı değişmez |
| 3 | Alt dizin (ör. `shared/`, `assets.coremusic.net/`) özgün kural ister | Domain sahibi agent | Üst CLAUDE.md'yi tekrarlamaz, sadece farkı yazar |
| 4 | Vault kök `.ai/CLAUDE.md` revizyonu | Vault Steward onayı zorunlu | Human Approval Gate (Guardrail #14) |
| 5 | Şablon sisteminde yeni kural eklenmesi | MO | Önce `[[.templates/index]]` sonra CLAUDE.md |

### §2.2 Kapsam

- **Kapsam:** Üretilecek `CLAUDE.md` dosyasının dış iskeleti (frontmatter + H1 + §1-§7), zorunlu kural blokları, yasaklar, doğrulama listesi.
- **Kapsam dışı:** Şablon envanteri ve sayım metrikleri (→ `[[.templates/index]]`); agent yetki/handover kuralları (→ `[[../../AGENTS.md]]`); proje teknik mimarisi (→ `[[../../brain.md]]`, `[[../../WORKFLOW.md]]`).

### §2.3 Hedef Kitle

| Kitle | Bu Şablonu Nasıl Kullanır |
|-------|---------------------------|
| AI ajanları (Claude, OpenCode, Cursor) | Üretim görevinde kopyala-doldur |
| İnsan geliştiriciler | Elle doldururken §6.1 listesini işaretler |
| Vault Steward | Denetimde §6.2 Quality Report ile karşılaştırır |

---

## §3 Mimari — Üretilen CLAUDE.md'nin İskeleti

### §3.1 Dış İskelet (Frontmatter + H1 + Bağlantılar)

Üretilen dosya aşağıda blok halinde verilen dış iskeletle başlar; `{{VARIABLE}}` alanları doldurulur, gerekmeyen § içeriği azaltılabilir ama § başlıkları silinemez.

````markdown
---
title: "{{PROJECT_NAME}} — AI Kuralları"
type: guide
category: ai-mandate
version: 1.0.0
status: active
authority: SSOT
updated: {{DATE}}
---

# {{PROJECT_NAME}} — AI Kuralları

**Zorunlu Bağlantılar:** [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]]

**Skills:** `.opencode/skills/` (Guardrail #16 zorunlu)

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

| İskelet Bölümü | CLAUDE.md Karşılığı | Zorunlu İçerik |
|-----------------|---------------------|----------------|
| Başlık | H1 + frontmatter | 7 zorunlu alan + tek H1 |
| §1 Amaç | Purpose | Projenin tek cümlelik tanımı + bu dosyanın neden okunması gerektiği |
| §2 Kapsam | Scope | Kapsam / kapsam dışı tablosu + SSOT öncelik sırası |
| §3 Mimari | Architecture | Katmanlar veya dizin yapısı — kısa tablo |
| §4 Kurallar | Rules | Guardrail listesi + §3.3 Şablon-Önce bloğu + yasaklılar |
| §5 Workflow | Workflow | Okuma sırası (boot protokolü) + görev akışı |
| §6 Doğrulama | Validation | Kontrol listesi + ihlal sonucu tablosu |
| §7 Referanslar | References | Wiki-linkler + Değişiklik Geçmişi (append-only) |

### §3.3 ZORUNLU — Şablon-Önce Kural Bloğu (Kopyalanacak Metin)

> **Bu blok üretilen CLAUDE.md içinde birebir bulunmak ZORUNDADIR.** Başka kelimelerle ifade edilemez, kısaltılamaz, yoruma açılamaz. Blok, §4 Kurallar bölümünün en üstüne, ilk madde olarak yerleştirilir.

````markdown
### ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. İlgili şablonun hangisi olduğunu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından bul.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** bu belgedeki standart formata göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablonu okumadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir ve `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası uygulanır |
| Erişilemedi (vault yok) | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |
````

### §3.4 Değişken Tablosu

| Değişken | Açıklama | Örnek |
|----------|----------|-------|
| `{{PROJECT_NAME}}` | Proje adı | CoreMusic |
| `{{TITLE}}` | Dosyanın görünen başlığı | AI Kuralları |
| `{{AUTHOR}}` | Yetkili yazar | Bayram Ali / Vault Steward |
| `{{VERSION}}` | Semver (x.y.z) | 1.0.0 |
| `{{DATE}}` | YYYY-MM-DD | 2026-09-24 |
| `{{SCOPE_DESCRIPTION}}` | Kapsam cümlesi | Tüm AI ajanları ve mühendisler |
| `{{READING_LIST}}` | Boot okuma listesi satırları | CLAUDE.md → AGENTS.md → WORKFLOW.md |
| `{{RULE_TABLE_ROWS}}` | Guardrail tablosu satırları | 16 guardrail satırı |
| `{{VIOLATION_CONSEQUENCE}}` | İhlal sonucu | Dosya revert edilir |

### §3.5 Bölüm İçerik Rehberi

Her § için asgari içerik aşağıdaki gibidir; eksik § varsa dosya §6.1'de düşer.

#### §3.5.1 §1 Amaç (asgari 5 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Tek cümlelik proje tanımı | ✅ | Pazar/konumlandırma SSOT'una link ver |
| "Bu dosya neden okunmalı" cümlesi | ✅ | Guardrail #2 (Vault First) gerekçesi |
| Felsefe/ilke maddesi | Tercihen | 1-3 madde |
| İlgili vizyon dosyası linki | ✅ | `[[VISION]]` veya eşdeğeri |

#### §3.5.2 §2 Kapsam (asgari 8 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Kapsam / Kapsam Dışı 2 sütunlu tablo | ✅ | En az 3 satır |
| SSOT öncelik sırası | ✅ | `CLAUDE.md > AGENTS.md > WORKFLOW.md > ...` tek satır |
| Çelişki kuralı | ✅ | "Vault lehine" veya "DUR + kullanıcıya sor" |

#### §3.5.3 §3 Mimari (asgari 6 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Katman/dizin tablosu | ✅ | 3+ satır; her satırda kısıtlama sütunu |
| Katman bağımlılık kuralı | ✅ | Hangi katman hangisine bağımlı |
| Port/servis listesi | İsteğe | Varsa eklenir |

#### §3.5.4 §4 Kurallar (asgari 15 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Şablon-Önce bloğu (§3.3) | ✅ ASLA SİLİNEMEZ | İlk madde |
| Guardrail tablosu (# nolu) | ✅ | Kural + uygulama + ihlal sonucu sütunları |
| Yasaklılar tablosu | ✅ | ✅ Yasak / ✅ Doğru çift sütunu |
| Onay kapıları | ✅ | Human Approval Gate, Zero Code Before Plan |

#### §3.5.5 §5 Workflow (asgari 6 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Okuma sırası (boot protokolü) | ✅ | Numaralı liste, dosya yollarıyla |
| Görev akışı şeması | ✅ | 5-7 adım, oklarla |
| Prompt/şablon eşlemesi | Tercihen | Varsa tablo |

#### §3.5.6 §6 Doğrulama (asgari 8 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| `- [ ]` kontrol listesi | ✅ | En az 8 madde |
| İhlal sonucu tablosu | ✅ | Her kural için sonuç |
| Ölçülebilir metrik | Tercihen | Coverage, test, satır sayısı |

#### §3.5.7 §7 Referanslar (asgari 6 satır)

| Öğe | Zorunlu mu? | Not |
|-----|-------------|-----|
| Wiki-link tablosu | ✅ | En az 4 hedef, `[[relative/path]]` formatı |
| Değişiklik Geçmişi | ✅ | Append-only tablo; eski satır silinmez |
| Authority/Footer | ✅ | Authority + Last Updated + Mode |

### §3.6 Anti-Pattern Tablosu (Üretim Sırası Sık Yapılan Hatalar)

| # | Anti-Pattern | Neden Kötü | Doğrusu |
|---|--------------|-----------|---------|
| 1 | Şablon-Önce bloğunun "kısaldırılması" | Guardrail #16 metni zayıflar | §3.3 birebir kopyalanır |
| 2 | `../` yerine mutlak yol yazmak (`C:\...`) | Taşınabilirlik kırılır | `[[relative/path]]` wiki-link |
| 3 | Frontmatter'e 8. alan eklemek | Standart kırılır | 7 zorunlu alan + varsa `date` gibi ekstra |
| 4 | Guardrail numarasını uydurmak | Hallüsinasyon | Vault'taki numaralar okunur, yoksa `VERIFICATION REQUIRED` |
| 5 | Değişiklik Geçmişi'nde eski satırı silme | Append-only ihlali | Yeni satır eklenir |
| 6 | Mojibake (bozuk UTF-8 ikilileri, U+FFFD) bırakma | Okunamaz dosya | `vault-utf8-writer verify` ile doğrula |
| 7 | 500+ derinliğin altında teslim | Vault standardı ihlali | §6.2 metriği doldur |

---

## §4 Kurallar

### §4.1 Guardrail #16 ve Şablon-Önce Uygulaması

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Yeni `CLAUDE.md` bu şablondan üretilir | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | SSOT | Kurallar vault'tan; kafadan kural uydurulmaz | İçerik silinir |
| 4 | Zero Hallucination | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`CLAUDE.md` kalır) | Dosya geri yüklenir |
| 6 | Human Approval Gate | Vault kök `.ai/CLAUDE.md` değişikliği onay ister | Onaysız değişiklik revert |
| 7 | REDACTED | Secret/key/log'a asla yazılmaz | Sızıntı sayılır, silinir |
| 8 | Frozen ADR dokunulmaz | 001-037 arası ADR metni değiştirilmez | Değişiklik revert + ERROR |
| 9 | Append-only log | Tüm değişikliklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 10 | UTF-8 yazım | Vault yazımları `vault-utf8-writer` ile | Bozuk dosya → repair |

### §4.2 Frontmatter — 7 Zorunlu Alan

| Alan | Zorunlu | Kural | Örnek |
|------|---------|-------|-------|
| `title` | ✅ | Tırnak içinde, `Proje — Dosya` düzeni | `"CoreMusic — AI Kuralları"` |
| `type` | ✅ | Dosya türü | `guide` |
| `category` | ✅ | Kategori | `ai-mandate` |
| `version` | ✅ | Semver `x.y.z` | `1.0.0` |
| `status` | ✅ | `active` / `draft` / `deprecated` | `active` |
| `authority` | ✅ | Otorite; anayasa dosyası `SSOT` | `SSOT` |
| `updated` | ✅ | `YYYY-MM-DD` | `2026-09-24` |

**Ek alanlar (isteğe bağlı):** `date` (ilk üretim tarihi), `governance` (Red Team · Human Mode · Truth Mode). Ek alanlar 7 zorunlu alanın yerine geçemez.

### §4.3 Dil ve Mojibake Kuralı

| Kural | Detay |
|-------|-------|
| Ana dil | Türkçe — ç ğ ı İ ö ş ü doğru yazılır |
| Dosya/kod adı | İngilizce: `CLAUDE.md`, `AGENTS.md` |
| Mojibake yasak | Bozuk UTF-8 ikilileri (Ã- ile başlayan diziler) ve U+FFFD yer tutucuları `vault-utf8-writer verify` ile tespit edilir |
| Yazım aracı | Vault yazımları yalnızca `node .ai/scripts/vault-utf8-writer.mjs` |
| PowerShell yazım yasağı | `Set-Content`, `Out-File`, `Add-Content`, `echo >` kullanılmaz |

### §4.4 Wiki-Link Formatı

| ✅ Doğru | ✅ Yanlış |
|----------|-----------|
| `[[AGENTS.md]]` | `[AGENTS.md](AGENTS.md)` |
| `[[../CLAUDE.md]]` | `[../CLAUDE.md](../CLAUDE.md)` |
| `[[.templates/index]]` | `C:\www\coremusic.net\.ai\index.md` |
| `[[../../AGENTS.md]]` | `/var/www/AGENTS.md` |

### §4.5 SSOT ve Authority Hiyerarşisi

| Sıra | Kaynak | Çelişki Durumunda |
|------|--------|-------------------|
| 1 | `.ai/CLAUDE.md` (vault anayasası) | Kazanır |
| 2 | `.ai/AGENTS.md` | — |
| 3 | `.ai/WORKFLOW.md` | — |
| 4 | `.ai/brain.md` | — |
| 5 | `.ai/index.md` | — |
| 6 | `.ai/.templates/` | En altta |

**Üretilen dosyanın `authority` alanı:** Alt dizin kılavuzu → `reference`; proje kökü anayasası → `SSOT`; şablon kendisi → `Template (Guardrail #16) — Registry: .ai/.templates/index.md`.

### §4.6 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Yeni şablon dosyası | ≥ 500 satır (ham ölçüm; `vault-utf8-writer verify` çıktısı) |
| Amaç | Yüzeysel şablon yerine gerçekten yönlendiren içerik |
| Kapsam dışı | `session-log-template.md` (144 satır, bilinçli kısa şablon) |
| Ölçüm | `vault-utf8-writer.mjs verify --file <path>` → `lines` alanı |
| İhlal | 500 altındaysa dosya tamamlanmış sayılmaz (geçmişte 7 şablon bu nedenle reddedildi) |

### §4.7 REDACTED ve Sır Politikası

| Durum | Aksiyon |
|-------|---------|
| API key, token, parola | Vault'a asla yazılmaz; `[REDACTED]` |
| `.env` içeriği | CLAUDE.md'ye örnek olarak bile yapıştırılmaz |
| Kişisel veri (KVKK) | Maskelenir |
| Log'a sızan secret | `log.md` girişinde `[REDACTED]` ile maskelenir |

### §4.8 In-Place ve Frozen ADR Koruması

| Kural | Uygulama |
|-------|----------|
| Dosya adı değişmez | `CLAUDE.md` yerine `CLAUDE-NEW.md` üretilmez |
| Frozen ADR 001-037 | Metinleri okunur, referans edilir; değiştirilmez |
| Yeni ADR | `ADR-088+` numarasıyla açılır (bu şablon ADR üretmez) |
| Silme yok | Çelişkiler silinmez; düzeltilir ve `log.md`'ye kaydedilir |

---

## §5 Workflow

### §5.1 Üretim Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Şablonu seç | `documentation/claude-md-template.md` | 1 dk |
| 2 | `.ai/.templates/index.md` §4 kurallarını oku | 7 alan + 8 bölüm bilgisi | 1 dk |
| 3 | Hedef dizini incele (mevcut CLAUDE.md var mı?) | In-Place mi üretim mi kararı | 2 dk |
| 4 | Kopyala → `{{VARIABLE}}` doldur | Taslak dosya | 5 dk |
| 5 | §3.3 Şablon-Önce bloğunu birebir göm | Zorunlu blok yerinde | 1 dk |
| 6 | §6.1 doğrulama listesini çalıştır | İşaretli kontrol listesi | 3 dk |
| 7 | `vault-utf8-writer verify` | UTF-8 + satır sayısı raporu | <1 dk |
| 8 | `log.md`'ye append + registry senkronu | Audit trail | 2 dk |

```text
ŞABLONU SEÇ → OKU → KOPYALA → {{PLACEHOLDER}} DOLDUR → ŞABLON-ÖNCE BLOĞUNU GÖM → DOĞRULA → UTF-8 VERIFY → LOG + REGISTRY SENKRON
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Şablon okunmadan yazım | Dosya §6.1'de 3+ madde düşer | Dosyayı şablondan yeniden üret |
| Eksik frontmatter alanı | FM BAD raporu | 7 alanı tamamla |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| 500 altı satır | `verify.lines < 500` | §3.5 rehberine göre derinleştir |
| Kırık wiki-link | Hedef dosya diskte yok | Linki düzelt veya `VERIFICATION REQUIRED` |
| Sayı çelişkisi (registry vs disk) | index.md total_* tutmuyor | Registry'yi disk sayımıyla senkronla |

### §5.3 Bitiş Koşulları

- [ ] Dosya hedef yolda, adı `CLAUDE.md`
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0)
- [ ] `log.md` append edildi (append-only)
- [ ] `.ai/.templates/index.md` sayıları güncel (eğer şablon kendisi değiştiyse)

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] Tek H1 var, `# ` ile başlıyor
- [ ] §3.3 Şablon-Önce bloğu birebir mevcut ve ilk madde
- [ ] §1-§7 başlıkları eksiksiz (silinmiş § yok)
- [ ] Zorunlu Bağlantılar satırı var (en az 1 wiki-link)
- [ ] Tüm linkler `[[relative/path]]` formatında
- [ ] Kapsam / Kapsam Dışı tablosu ≥ 3 satır
- [ ] Guardrail tablosu numaralı ve ihlal sonucu sütunlu
- [ ] Yasaklılar tablosu (Yasak/Doğru) mevcut
- [ ] Boot okuma sırası numaralı liste olarak mevcut
- [ ] Doğrulama listesi ≥ 8 `- [ ]` madde
- [ ] Değişiklik Geçmişi tablosu append-only
- [ ] Authority footer (Authority + Last Updated + Mode)
- [ ] Mojibake yok (`vault-utf8-writer verify` → mojibake 0, BOM false)
- [ ] Ham satır sayısı ≥ 500 (şablon dosyası için)

### §6.2 Quality Report (Şablon Dosyasının Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 2.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Zorunlu blok | 1 (§3.3 Şablon-Önce — silinemez) |
| Guardrail kapsamı | #16 merkezli; #2, #3, #4, #13, #14 bağlayıcı |
| Değişken | 9 (`{{VARIABLE}}`) |
| Anti-pattern | 7 (§3.6) |
| Frontmatter alan | 7 zorunlu + 0 opsiyonel zorunlu |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (verify `lines`) |
| Kayıt | `log.md` append + registry senkron |

### §6.3 Örnek — Dolu İskelet (İlk 3 Bölüm)

Aşağıdaki örnek, değişkenler doldurulmuş halde §1-§3'ün nasıl görüneceğini gösterir; üretimde tamamı §3.5 rehberine göre doldurulur.

````markdown
## §1 Amaç

**{{PROJECT_NAME}}** — kurumsal seviyede {{SCOPE_DESCRIPTION}} için tasarlanmış platformdur.
Bu dosya, bu dizinde çalışan tüm AI ajanlarının uyması zorunlu kuralları tek başına tanımlar;
okunmadan plan/kod/faaliyet başlatılamaz (Guardrail #2 — Vault First).

* **Felsefe:** "..."
* **Temel Belgeler:** Vizyon → `[[VISION]]`, süreç → `[[WORKFLOW]]`

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tüm AI ajanları | Üçüncü taraf servislerin iç kuralları |
| Tüm mühendisler | Kaynak kodu iç detayları |
| Dokümantasyon | Donanım üretimi süreçleri |

**SSOT öncelik sırası:** `CLAUDE.md > AGENTS.md > WORKFLOW.md > brain.md > index.md > .templates/`
Harici kaynaklar (web, model hafızası) her zaman vault'un altındadır.

## §3 Mimari

| Katman | Kapsam | Kısıtlama |
|--------|--------|-----------|
| L3 Uygulama | Paneller, SPA | Yalnızca API üzerinden |
| L2 Servis | Servisler, Event Bus | Doğrudan çağrı yasak |
| L1 Güvenlik | Auth, CSRF, RateLimit | Bypass edilemez |
| L0 Altyapı | DB, Cache, Docker | BCNF, raw PDO |
````

````markdown
## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First)
*(§3.3 bloğu birebir buraya yapıştırılır — 5 madde + Durum/Aksiyon tablosu)*

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Vault First | Kod/plan öncesi CLAUDE → AGENTS → WORKFLOW okunur | İşlem durdurulur |
| 2 | Zero Hallucination | Doğrulanamayan iddia `VERIFICATION REQUIRED` | İçerik silinir |
| 3 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert edilir |
| 4 | Human Approval Gate | Mimari karar öncesi onay | Karar uygulanmaz |

| ✅ Yasak | ✅ Doğru |
|----------|----------|
| `SELECT *` | Açık sütun listesi |
| ORM | Raw PDO |
| Hardcoded secret | `.env` / credential vault |

## §5 Workflow

1. Boot protokolü: `.ai/CLAUDE.md` → `AGENTS.md` → `WORKFLOW.md` → `brain.md` → `index.md`
2. Görev analizi: keyword → agent routing (`AGENTS.md` §6)
3. Şablon seçimi: `.ai/.templates/index.md` §7.1
4. Üretim → doğrulama → `log.md` append

## §6 Doğrulama

- [ ] Frontmatter 7 alan
- [ ] Şablon-Önce bloğu yerinde
- [ ] Wiki-link'ler hedef buluyor
- [ ] Mojibake 0 (verify)
- [ ] `log.md` append edildi

## §7 Referanslar

| Dosya | Amaç |
|-------|------|
| `[[../index]]` | Vault kataloğu |
| `[[../WORKFLOW]]` | Süreçler |
| `[[../log]]` | Audit trail |

| Tarih | Versiyon | Değişiklik |
|-------|----------|------------|
| {{DATE}} | 1.0.0 | İlk üretim |
````

*(§4-§7 örnekleri kasıtlı kısaltılmıştır; üretimde §3.5 asgari satırları doldurulur.)*

---

## §7 Referanslar

### 7.1 Wiki-link Referansları

| Hedef | İlişki |
|-------|--------|
| `[[.templates/index]]` | Şablon registry'si — bu şablonun kayıt defteri |
| `[[../CLAUDE.md]]` | Vault anayasası (Guardrail #2, #16 kaynağı) |
| `[[../../AGENTS.md]]` | Agent routing, §6 keyword tablosu |
| `[[../../WORKFLOW.md]]` | Süreçler, boot protokolü |
| `[[./docs-md-template]]` | Genel dokümantasyon .md şablonu (kardeş şablon) |
| `[[./WikiPage-Template]]` | Wiki sayfası şablonu (kardeş şablon) |
| `[[../../keys.md]]` | Keyword haritası |
| `[[../../log.md]]` | Audit trail (append-only) |

### 7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-24 | 2.0.0 | İlk üretim — registry'ye eklendi; §3.3 Şablon-Önce bloğu zorunlu | vault-updater |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
