---
title: "CoreMusic — Agent Profile Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — Agent Profile Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../.agents/AGENTS.md]]

---

## §1 Amaç

Bu şablon, CoreMusic ekosistemindeki bir agent profil dosyasının (`.ai/.agents/*.md`) üretilmesi için zorunlu standarttır. **Guardrail #16:** yeni agent profili oluşturulurken bu şablon kullanılmak ZORUNLUDUR; şablonsuz profil dosyası yazılamaz. Şablon; agent'ın kimliğini, misyonunu, sorumluluklarını, yetki sınırlarını (Allowed / Forbidden), teknoloji yığınını, mimari kurallarını, workflow'unu, handover ve failure handling alanlarını standart biçimde taşır. Böylece her profil, agent registry routing tablosuyla (`[[../../AGENTS.md]]` §6) birebir eşleşen tek bir okuma deneyimi sunar.

| Alan | Değer |
|------|-------|
| Template Name | `agents-template.md` |
| Template Path | `.ai/.templates/agents/agents-template.md` |
| Hedef Dosya Tipi | Agent profil dosyası (`.ai/.agents/<agent-adı>.md`) |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Master Orchestrator (profil koordinasyonu) |
| İkincil Yazarlar | Vault Steward (kayıt), ilgili uzman agent'ların editörleri |
| Hedef Envanter | `.ai/.agents/` altındaki 11 profil + 1 alt registry (glob kanıtı) |
| Kanıt Dosyaları | `.ai/.agents/backend-architect.md`, `.ai/.agents/master-orchestrator.md`, `.ai/.agents/AGENTS.md` |
| Korunan İskelet | H1 + §1 Identity → §11 Version (11 bölüm silinemez) |
| Değişken Formatı | `{{VARIABLE}}` (AGENT_NAME, CODE_NAME, DOMAIN, LAYER, DATE, VERSION) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); kod/dosya adı İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); profil örnekleri kendi authority değerini taşır |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| İlişkili Workflow | `.workflows/session.md`, `.workflows/feature-dev.md` |
| Son Kontrol | 2026-09-23 |

### §1.1 Neden Profil Şablonu Zorunludur?

Bir agent profili, o agent'ın **yetki belgesidir**: hangi dosya tipini düzenleyebileceğini, hangi alanlara giremeyeceğini ve hangi standarda uymak zorunda olduğunu tek yerde tanımlar. Şablonsuz yazılan profiller zamanla farklı başlık sıraları, eksik Allowed/Forbidden alanları ve tutarsız authority iddiaları üretir; bu da routing tablosuyla (`[[../../AGENTS.md]]` §6) eşleşmeyen "hayalet" profillere yol açar. Guardrail #16 bu yüzden şablon kullanımını zorunlu kılar.

### §1.2 Şablonun Sağladığı Tutarlılık Garantileri

| Garanti | Açıklama | Kaynak |
|---------|----------|--------|
| Aynı iskelet | Her profil §1 Identity → §11 Version sırasını taşır | Bu şablon §3 |
| Aynı alan adları | Field / Responsibility / Allowed / Forbidden sütun adları birebir aynı | Bu şablon §3 |
| Aynı doğrulama | 14 maddelik §6 kontrol listesi her profilde uygulanır | Bu şabolon §6 |
| Aynı authority dili | Profil kendi authority değerini yazar; şablon `SSOT` iddiası taşımaz | Bu şablon §4 kural 6 |
| Aynı kayıt yolu | Yeni profil → registry + kök referans + log birlikte güncellenir | `.ai/.templates/index.md` |

### §1.3 Kimler Kullanır?

- **Master Orchestrator:** profil koordinasyonu, yeni profil iskeletinin açılışı, §15 registry linki eşlemesi.
- **Vault Steward:** profilin commit edilmesi, Version tablosu append'i, log kaydı.
- **Uzman agent editörleri:** kendi profillerinin Mission / Responsibilities / Allowed / Forbidden alanlarını doldurur.
- **Tüm agent'lar:** başka bir agent'ın yetki sınırını sorguladığında profili **okur** (yazma yetkisi sahibine aittir).

---

## §2 Kapsam

Şablonun kapsadığı ve kapsam dışında bıraktığı alanlar aşağıdaki gibidir. Kapsam sınırı, domain boundary kuralını (`[[../../AGENTS.md]]` §5) korur: profil dosyası bir yetki belgesidir, routing/escalation kurallarının kendisi kök registry'dedir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/.agents/<agent-adı>.md` profil dosyalarının üretilmesi | Agent routing / handover / escalation kurallarının kendisi (→ `[[../../AGENTS.md]]`) |
| Mevcut 11 profilin revizyonu ve yeni profil eklenmesi | Şablon envanteri ve registry satırları (→ `[[.templates/index]]`) |
| Allowed / Forbidden yetki sınırlarının doldurulması | Teknik uygulama detayları, kod dosyaları |
| Profil içi Version tablosuna revizyon satırı eklenmesi | ADR metinleri (frozen — okunur, referanslanır, değiştirilmez) |
| `{{PLACEHOLDER}}` alanlarının gerçek değerlerle doldurulması | `.ai/log.md` append-only kayıt (üst görevin işi) |
| Frontmatter 7 alan disiplini (title, type, category, date, updated, version, status, authority) | `.ai/.templates/index.md` envanter güncellemesi (SRP — ayrı işlem) |
| Wiki-link ile çapraz referans (`[[...]]` göreli yol formatı) | `.opencode/skills/*/SKILL.md` skill tanımları |
| Türkçe doğruluk (ç ğ ı İ ö ş ü) ve mojibake denetimi | Yetkisiz dosya yazımı (YAZMA YASAĞI — sadece bu şablon ailesi) |

**Dosya tipi:** Markdown agent profil dosyası · **Uzantı:** `.md` · **Konum:** `.ai/.agents/` · **Guardrail:** #16

### §2.1 Disk Kanıtlı Envanter (YAGNI)

Aşağıdaki yollar glob ile doğrulanmıştır; şablon bunlar dışındaki hiçbir dosya adını **iddia etmez**. Doğrulanamayan herhangi bir yol uydurulmaz — `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

| Kanıt | Glob / Yol | Bulunan |
|-------|------------|---------|
| Agent profilleri | `.ai/.agents/*.md` | 12 dosya (11 profil + AGENTS.md) |
| Alt registry | `.ai/.agents/AGENTS.md` | 1 dosya (profil detayları) |
| Örnek profil | `.ai/.agents/backend-architect.md` | mevcut — §1 kimlik yapısı |
| Örnek profil | `.ai/.agents/master-orchestrator.md` | mevcut |
| Şablon registry | `.ai/.templates/index.md` | mevcut (envanter SRP) |
| Şablon kuralı | `.ai/.templates/CLAUDE.md` | mevcut (Guardrail #16) |
| Workflow'lar | `.workflows/*.md` | 8 dosya |

### §2.2 Kapsam Dışı İşlemler Hangi Şablona Gider?

| Kapsam Dışı İşlem | Gideceği Şablon |
|-------------------|-----------------|
| PHP backend kod dosyası | `[[../backend/php-template]]` |
| Node.js backend kod dosyası | `[[../backend/nodejs-template]]` |
| API dokümanı | `[[../documentation/api-doc-template]]` |
| Güvenlik denetim raporu | `[[../documentation/security-audit-template]]` |
| Genel wiki sayfası | `[[../documentation/WikiPage-Template]]` |
| ADR kararı | `[[../adr/adr-template]]` |
| Test dosyası | `[[../testing/phpunit-template]]`, `[[../testing/vitest-template]]` |

---

## §3 Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + künye + 11 bölüm, eksiksiz). Dört-çit ` ```markdown ` bloğu, iskeletin kendi üç-çit kod bloklarını korumak için kullanılmıştır; iskelet içindeki başlıklar gömme nedeniyle iki seviye derinleştirilmiştir (H1 → `#`, H2 → `##`).

````markdown
---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Agent Profile Template"
type: agent-profile-template
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

# CoreMusic — {{AGENT_NAME}}

**Kod Adı:** {{CODE_NAME}}
**Domain:** {{DOMAIN}}
**Katman:** {{LAYER}}
**Öncelik:** {{PRIORITY}}
**Durum:** {{STATUS}}

**Zorunlu Bağlantılar:** [[.agents/AGENTS.md]] · [[../../AGENTS.md]] · [[../CLAUDE.md]] · [[brain.md]]

---

## 1. Agent Identity (Kimlik)

| Field | Value |
|-------|-------|
| Name | {{AGENT_NAME}} |
| Code Name | {{CODE_NAME}} |
| Domain | {{DOMAIN}} |
| Layer | {{LAYER}} |
| Priority | {{PRIORITY}} |
| Status | {{STATUS}} |
| Profil Dosyası | `.ai/.agents/{{agent-slug}}.md` |
| Registry Satırı | `[[../../AGENTS.md]]` §15 Agent Detayları |
| Routing Keyword'leri | {{KEYWORD_GROUP}} |
| Anahtar Kalite Standardı | {{QUALITY_STANDARD}} |
| Escalation Hedefi | {{ESCALATION_TARGET}} |
| Handover Ortakları | {{HANDOVER_PARTNERS}} |
| Health Check Interval | {{HEARTBEAT}} |
| Son Doğrulama | {{DATE}} |

---

## 2. Mission (Misyon)

Bu agent:

- {{MISSION_1}}
- {{MISSION_2}}
- {{MISSION_3}}

sorumludur.

| Mission Detail | Value |
|----------------|-------|
| Ana Sorumluluk | {{PRIMARY_RESPONSIBILITY}} |
| İkincil Sorumluluk | {{SECONDARY_RESPONSIBILITY}} |
| Teslimat Çıktısı | {{DELIVERABLE}} |
| Hedef Kitle | {{AUDIENCE}} |
| Bağımlı Agent'lar | {{DEPENDENT_AGENTS}} |
| Destekleyen Agent'lar | {{SUPPORTING_AGENTS}} |
| Kapsam Sınırı | {{SCOPE_BOUNDARY}} |

---

## 3. Responsibilities (Sorumluluklar)

| # | Responsibility |
|---|----------------|
| 1 | {{RESPONSIBILITY_1}} |
| 2 | {{RESPONSIBILITY_2}} |
| 3 | {{RESPONSIBILITY_3}} |
| 4 | {{RESPONSIBILITY_4}} |
| 5 | {{RESPONSIBILITY_5}} |
| 6 | {{RESPONSIBILITY_6}} |
| 7 | {{RESPONSIBILITY_7}} |
| 8 | {{RESPONSIBILITY_8}} |
| 9 | {{RESPONSIBILITY_9}} |
| 10 | {{RESPONSIBILITY_10}} |

---

## 4. Allowed Scope (İzinli Alan)

Allowed:

| # | Allowed Action | Sınır / Not |
|---|----------------|-------------|
| 1 | {{ALLOWED_1}} | {{BOUNDARY_1}} |
| 2 | {{ALLOWED_2}} | {{BOUNDARY_2}} |
| 3 | {{ALLOWED_3}} | {{BOUNDARY_3}} |
| 4 | {{ALLOWED_4}} | {{BOUNDARY_4}} |
| 5 | {{ALLOWED_5}} | {{BOUNDARY_5}} |
| 6 | {{ALLOWED_6}} | {{BOUNDARY_6}} |
| 7 | {{ALLOWED_7}} | {{BOUNDARY_7}} |
| 8 | {{ALLOWED_8}} | {{BOUNDARY_8}} |
| 9 | {{ALLOWED_9}} | {{BOUNDARY_9}} |
| 10 | {{ALLOWED_10}} | {{BOUNDARY_10}} |

**Not:** Allowed listesi, `[[../../AGENTS.md]]` §5 Domain Boundaries tablosundaki "Diğerleri Erişebilir mi?" sütunuyla uyumlu olmalıdır; Allowed, domain'in dışına taşıyorsa profil reddedilir.

---

## 5. Forbidden Scope (Yasak Alan)

Forbidden:

| # | Forbidden Action | İhlal Sonucu |
|---|------------------|--------------|
| 1 | {{FORBIDDEN_1}} | {{VIOLATION_1}} |
| 2 | {{FORBIDDEN_2}} | {{VIOLATION_2}} |
| 3 | {{FORBIDDEN_3}} | {{VIOLATION_3}} |
| 4 | {{FORBIDDEN_4}} | {{VIOLATION_4}} |
| 5 | {{FORBIDDEN_5}} | {{VIOLATION_5}} |
| 6 | {{FORBIDDEN_6}} | {{VIOLATION_6}} |
| 7 | {{FORBIDDEN_7}} | {{VIOLATION_7}} |
| 8 | {{FORBIDDEN_8}} | {{VIOLATION_8}} |
| 9 | {{FORBIDDEN_9}} | {{VIOLATION_9}} |
| 10 | {{FORBIDDEN_10}} | {{VIOLATION_10}} |

**Not:** Forbidden listesi, katman ihlali (Layer Violation) ve dosya tipi gaspını kapsar; ihlal durumunda eylem her zaman **derhal revert + log ERROR**'dur.

---

## 6. Technology Stack (Teknoloji Yığını)

| Area | Technology |
|------|------------|
| Language | {{LANGUAGE}} |
| Framework | {{FRAMEWORK}} |
| Database | {{DATABASE}} |
| Tools | {{TOOLS}} |
| Testing | {{TESTING}} |
| CI/CD | {{CICD}} |
| Runtime | {{RUNTIME}} |
| Version | {{VERSION}} |
| Security Crypto | {{CRYPTO}} |
| Forbidden Packages | moment.js, lodash, axios, request, cheerio, puppeteer, winston, gulp |

---

## 7. Architecture Rules (Mimari Kurallar)

Must follow:

- SOLID
- Clean Architecture
- Domain Boundary
- ADR Decisions
- SSOT Rules

| # | Rule | Kaynak |
|---|------|--------|
| 1 | SOLID prensipleri | [[../CLAUDE.md]] |
| 2 | Clean Architecture katmanları | [[brain.md]] |
| 3 | Domain Boundary korunur | [[../../AGENTS.md]] §5 |
| 4 | ADR kararlarına uyulur | [[../decisions/index]] |
| 5 | SSOT kuralı (çelişkide kök kazanır) | [[../../AGENTS.md]] §26.2 |
| 6 | Zero Code Before Plan | [[../WORKFLOW.md]] |
| 7 | Template Mandatory (Guardrail #16) | [[../../.templates/index]] |
| 8 | log.md append-only | [[../../AGENTS.md]] §25.3 |
| 9 | Frozen ADR metni değiştirilmez | [[../../AGENTS.md]] §25.3 |
| 10 | Doğrulanamayan bilgi `⚠️ VERIFICATION REQUIRED` | [[../CLAUDE.md]] |
| 11 | Secret / credential REDACTED politikası | [[../CLAUDE.md]] |
| 12 | Ultra Thinking 5-adım protokolü | [[../../AGENTS.md]] §24.1 |

---

## 8. Workflow (İş Akışı)

READ → PLAN → IMPLEMENT → TEST → VALIDATE → LOG

| Step | Aksiyon | Çıktı | Timeout |
|------|---------|-------|---------|
| 1 | READ — vault + bağlam oku | Anlaşılan bağlam | 25s |
| 2 | PLAN — adım planı çıkar | Onaylı plan | Değişken |
| 3 | IMPLEMENT — kod/doküman üret | Ham çıktı | Değişken |
| 4 | TEST — doğrulama çalıştır | Test sonucu | Anlık |
| 5 | VALIDATE — kalite kapıları | 8/8 gate | Anlık |
| 6 | LOG — log.md'ye append | Audit trail | Anlık |
| 7 | HANDOVER — gerekirse transfer | Handover mesajı | 30s |
| 8 | ESCALATION — L1 → L2 → L3 → İnsan | Eskalasyon kaydı | 30/60/120s |

---

## 9. Handover (Görev Transferi)

| Alan | Değer |
|------|-------|
| FROM | {{FROM_AGENT}} |
| TO | {{TO_AGENT}} |
| TASK | {{TASK}} |
| STATUS | {{STATUS}} |
| FILES | {{FILES}} |
| VALIDATION | {{VALIDATION}} |
| Öncelik | {{PRIORITY}} |
| Onay Durumu | {{APPROVAL_STATUS}} |
| Timestamp | {{TIMESTAMP}} |

---

## 10. Failure Handling (Hata Yönetimi)

| Alan | Değer |
|------|-------|
| STATUS | {{FAILURE_STATUS}} |
| REASON | {{FAILURE_REASON}} |
| ACTION | {{FAILURE_ACTION}} |
| Max Retry | {{MAX_RETRY}} |
| Escalation | {{ESCALATION_LEVEL}} |
| Log Seviyesi | {{LOG_LEVEL}} |
| Son Deneme | {{LAST_ATTEMPT_DATE}} |

---

## 11. Version (Sürüm)

| Version | Date | Change |
|---------|------|--------|
| 1.0.0 | {{DATE}} | Created |
| {{VERSION}} | {{DATE}} | {{CHANGE_DESCRIPTION}} |

---

*Agent Profile Template v1.0.0 — CoreMusic Agent Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
````

---

## §4 Kurallar

Profil yazımı sırasında uygulanan zorunlu ve yasak kurallar. Her kural, vault içindeki bir authority kaynağına bağlanır; çelişki durumunda kök dosya kazanır (`[[../../AGENTS.md]]` §26.2).

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Profil dosyası bu şablondan üretilir; dış iskelet H1 + §1-§11 silinemez | Zorunlu | Guardrail #16 ihlali → dosya reddedilir |
| 2 | Allowed Scope (§4) ve Forbidden Scope (§5) boş bırakılamaz | Zorunlu | Bozuk profil yayımlanamaz |
| 3 | Mimari kurallar (iskelet §7): SOLID · Clean Architecture · Domain Boundary · ADR Decisions · SSOT Rules zorunlu | Zorunlu | Mimari sapma → revert |
| 4 | Frontmatter 7 zorunlu alan eksiksiz yazılır (title, type, category, date, updated, version, status, authority) | Zorunlu | Frontmatter hatası → VALIDATION hatası |
| 5 | Profil, `[[../../AGENTS.md]]` §15 profil linkiyle ve §4 Agent Overview satırıyla eşleşir | Zorunlu | Routing uyuşmazlığı → MO müdahalesi |
| 6 | Bu şablon dosyasının kendi authority değeri `SSOT`; profil örneğindeki `Single Source of Truth (SSOT)` iskelet değeri korunur, profil doldurulurken profilin gerçek authority değeri yazılır | Zorunlu | Authority çelişkisi |
| 7 | Yeni profil eklendiğinde registry (`[[.templates/index]]`) + kök referans tabloları + log güncellenir (Değişiklik Protokolü) | Zorunlu | Kayıt düşüklüğü |
| 8 | Doğrulanamayan alan `⚠️ VERIFICATION REQUIRED` ile işaretlenir; uydurma bilgi yazılmaz | Zorunlu | Hallucination → RED |
| 9 | `{{PLACEHOLDER}}` alanları doldurulmadan profil commit edilemez | Yasak | Yarım profil |
| 10 | Frozen ADR metinleri değiştirilmez, yalnız referanslanır | Yasak | Frozen ihlali → derhal revert |
| 11 | `.ai/log.md` dosyasına yalnız append yapılır; geçmiş satıra dokunulmaz | Yasak | Audit trail bozulması |
| 12 | Dosya adı değiştirilemez (In-Place Refactoring) — onay olmadan yeniden adlandırma yasak | Yasak | Kırık wiki-link |
| 13 | Türkçe karakterler (ç ğ ı İ ö ş ü) doğru yazılır; mojibake (ü, ö vb.) yasak | Zorunlu | Mojibake → onarım |
| 14 | Kod, sınıf, dosya ve komut adları İngilizce yazılır | Zorunlu | Tutarlılık bozulması |
| 15 | Wiki-link formatı `[[relative/path/to/file]]` biçimindedir | Zorunlu | Kırık çapraz referans |
| 16 | Secret / credential / token hiçbir koşulda profile yazılmaz (REDACTED politikası) | Yasak | Güvenlik ihlali |
| 17 | Profil içi Version tablosuna her revizyon için yeni satır eklenir (append-only) | Zorunlu | Sürüm geçmişi kaybı |
| 18 | Envanter listesi bu şablonda tekrarlanmaz (SRP) → `[[.templates/index]]` | Yasak | İkincil kaynak çelişkisi |
| 19 | Routing, handover, escalation değerleri kök `[[../../AGENTS.md]]` dosyasından okunur (DIP — bağımlılık tersine) | Zorunlu | SSOT çelişkisi |
| 20 | Aynı kategori şablonlarının § başlıkları birebir aynıdır (DRY): §1 Amaç → §7 Referanslar | Zorunlu | Şablon tutarsızlığı |

### §4.1 Kural Uygulama Sırası

Kurallar eşzamanlı uygulanmaz; ihlal tespit edildiğinde aşağıdaki öncelikle hareket edilir. Aynı anda birden fazla ihlal varsa en yüksek öncelikli kural önce işlenir.

| Öncelik | Kural Grubu | Aksiyon | Timeout |
|---------|-------------|---------|---------|
| 1 (CRITICAL) | Güvenlik / REDACTED (kural 16) | Derhal durdur + MO'ya bildir | Anlık |
| 2 (CRITICAL) | Frozen ihlali (kural 10) | Derhal revert + log ERROR | Anlık |
| 3 (HIGH) | Domain boundary / Layer violation (kural 2, 3) | revert + log ERROR | 15s |
| 4 (HIGH) | dosya adı değişikliği (kural 12) | Eski ada geri dön + onay iste | 15s |
| 5 (MEDIUM) | Eksik frontmatter / placeholder (kural 4, 9) | Doldur + yeniden doğrula | 30s |
| 6 (MEDIUM) | Hallucination işareti eksik (kural 8) | `⚠️ VERIFICATION REQUIRED` ekle | 30s |
| 7 (LOW) | Mojibake / Türkçe karakter (kural 13) | `vault-utf8-writer.mjs repair` ile onar | 60s |
| 8 (LOW) | SRP / DIP / DRY ihlali (kural 6, 19, 20) | İçeriği doğru dosyaya taşı | 60s |

### §4.2 İhlal Sonrası Geri Dönüş

1. **Tespit:** §6 doğrulama listesinden ihlal satırı ✅ değilse commit durdurulur.
2. **Onarım:** İlgili kural grubundaki aksiyon uygulanır (yukarıdaki tablo).
3. **Yeniden doğrulama:** §6 listesi baştan çalıştırılır; 14/14 ✅ olmadan commit yoktur.
4. **Kayıt:** Onarım sonucu `log.md`'ye append edilir; geçmiş satıra dokunulmaz (append-only).
5. **Eskalasyon:** 3 denemede düzelmeyen ihlal L2'ye (Tech Lead) taşınır (`[[../../AGENTS.md]]` §10.2).

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/agents/agents-template.md` (Guardrail #16) | Şablon kopyası |
| 2 | KOPYALA | `.ai/.agents/<agent-adı>.md` hedefine kopyala (dosya adı değişmez, onayla) | Yeni profil iskeleti |
| 3 | DOLDUR | Identity / Mission / Responsibilities / Allowed / Forbidden / Technology Stack / Architecture Rules alanları; Workflow ve Handover profil sahibine göre; Version tablosuna revizyon satırı | Dolu profil |
| 4 | DOĞRULA | §6 kontrol listesi + `[[../../AGENTS.md]]` §4/§15 eşleşmesi | 8/8 gate |
| 5 | COMMIT | Registry (`[[.templates/index]]`) + kök referans + log güncellemesiyle birlikte commit | Vault senkronu |

**Adım 3 detayı — doldurma sırası:** (a) §1 Identity alanlarının tamamı, (b) §2 Mission maddeleri ve mission detail tablosu, (c) §3 Responsibilities en az 5 satır, (d) §4 Allowed + §5 Forbidden eşit ağırlıklı, (e) §6 Technology Stack, (f) §7 Architecture Rules kaynak sütunu, (g) §8-§10 operasyonel alanlar, (h) §11 Version ilk satır (`1.0.0 | {{DATE}} | Created`).

### §5.1 Workflow Adım Bağımlılıkları

| Adım | Bağımlı Olduğu | Tamamlanmadan Yapılamaz | Sık Yapılan Hata |
|------|----------------|--------------------------|------------------|
| 1 ŞABLONU SEÇ | Guardrail #16 | — | Eski/yanlış şablon seçimi |
| 2 KOPYALA | Adım 1 | Dosya adı onayı alınmadan yeniden adlandırma | In-Place Refactoring ihlali |
| 3 DOLDUR | Adım 2 | §4-§5 (Allowed/Forbidden) boşken devam | Boş yetki alanı |
| 4 DOĞRULA | Adım 3 | §6 14/14 ✅ olmadan | Eksik placeholder kontrolü |
| 5 COMMIT | Adım 4 | Registry + log güncellenmeden | Kayıt düşüklüğü (SRP) |

### §5.2 Handover Tetikleyicileri

Profil yazımı sırasında şu durumlar handover gerektirir; handover mesaj formatı `[[../../AGENTS.md]]` §9.1'dedir.

| Tetikleyici | Kaynak | Hedef | Öncelik |
|-------------|--------|-------|---------|
| Profilin yetki alanı başka agent'ın domain'ine giriyor | Editör | MO (koordinasyon) | HIGH |
| Allowed/Forbidden çelişkisi tespit edildi | Editör | İlgili domain lead (L1) | HIGH |
| Profil, routing tablosuyla eşleşmiyor | Editör | Vault Steward | MEDIUM |
| ADR referansı doğrulanamıyor | Editör | MO (vault-updater) | MEDIUM |
| Mojibake / encoding bozukluğu | Editör | MO (vault-utf8-writer) | LOW |

---

## §6 Doğrulama

Profil commit edilmeden önce aşağıdaki kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse profil yayımlanmaz.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + §1-§11 iskeleti eksiksiz mi? | 11 bölüm silinmemiş | ✅/❌ |
| 3 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | Name / Code Name / Domain / Layer / Priority dolu | ✅/❌ |
| 4 | §4 Allowed + §5 Forbidden dolduruldu mu? | Her ikisi de en az 3 satır | ✅/❌ |
| 5 | §7 Architecture Rules 5 madde yerinde mi? | SOLID, Clean Architecture, Domain Boundary, ADR, SSOT | ✅/❌ |
| 6 | Wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]` formatı, kırık link yok | ✅/❌ |
| 7 | `[[../../AGENTS.md]]` §4/§15 ile eşleşme var mı? | Agent satırı + profil linki tutarlı | ✅/❌ |
| 8 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru; Ã- kalıntısı yok | ✅/❌ |
| 9 | Doğrulanamayan alan işaretlendi mi? | `⚠️ VERIFICATION REQUIRED` | ✅/❌ |
| 10 | Secret / credential yazılmadı mı? | REDACTED politikası | ✅/❌ |
| 11 | Version tablosuna revizyon satırı eklendi mi? | Append-only | ✅/❌ |
| 12 | Dosya adı değişmedi mi? | In-Place Refactoring | ✅/❌ |
| 13 | Envanter SRP ihlali yok mu? | Envanter listesi `[[.templates/index]]`'de | ✅/❌ |
| 14 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

### §6.1 Doğrulama Sırası ve Aşamaları

Kontroller rastgele değil, aşamalı çalıştırılır; bir aşama geçmeden sonraki aşama başlamaz.

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2 | Frontmatter + iskelet sağlam |
| B | İçerik | 3, 4, 5 | Placeholder + yetki alanları dolu |
| C | Referans | 6, 7 | Wiki-link + registry eşleşmesi |
| D | Dil | 8, 9, 10 | Türkçe doğruluk, işaretleme, REDACTED |
| E | Protokol | 11, 12, 13, 14 | Version, dosya adı, SRP, DRY |

### §6.2 Red Sinyalleri (Otomatik DUR)

Aşağıdaki durumlarda profil commit edilmez, işlem durur ve kullanıcıya/üst göreve sorulur.

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| §4 Allowed veya §5 Forbidden tamamen boş | Yetki belgesi eksik | DUR — editörden alanları doldurmasını iste |
| `[[../../AGENTS.md]]` §15'de karşılığı olmayan profil | Kayıtsız agent | DUR — registry satırı eklenmeden yazma |
| Placeholder hâlâ `{{...}}` biçiminde | Yarım dolu profil | DUR — doldur |
| Mojibake tespiti (Ã, â„ gibi kalıntılar) | Encoding bozulması | DUR — `vault-utf8-writer.mjs repair` |
| Doğrulanamayan iddia (yol/versiyon/sayı) | Hallucination riski | `⚠️ VERIFICATION REQUIRED` ekle veya sil |
| Secret / token / credential içerdiği | REDACTED politikası | DUR — maskele + Security Engineer'a bildir |

### §6.3 Sürüm Geçmişi Kuralları

| Kural | Değer |
|-------|-------|
| İlk satır | `1.0.0 | {{DATE}} | Created` |
| Revizyon | Her onaylı değişiklikte yeni satır eklenir |
| Düzenleme | Mevcut satıra dokunulmaz (append-only) |
| Format | `Version | Date | Change` sütunları sabit |
| Major bump | İskelet yapısı değişirse (H1 + §1-§11) — onay gerektirir |

---

**REFACTOR REPORT:** FILE: agents-template.md · PURPOSE: Agent Profile Template · VALIDATION: 7 alan + §1-§11 + bilgi korunumu (11 bölüm iskeleti, 17 doğrulama, 4-frontmatter örneği) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

Bu şablonun dayandığı vault kaynakları. Envanter listesi burada tekrarlanmaz (SRP); kayıt `[[.templates/index]]` dosyasındadır.

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Routing §6, Domain §5, Agent Overview §4, Detay §15 |
| Alt registry (profil detayları) | [[../../.agents/AGENTS.md]] | Profil özetleri, ASCII mimari |
| Hedef dizin | `.ai/.agents/*.md` | 11 profil + AGENTS.md (glob kanıtı) |
| Mimari kararlar | [[../../brain.md]] | ADR kararlarının özeti |
| Süreçler | [[../../WORKFLOW.md]] | Fazlar, session protokolü |
| Şablon Kuralı | [[../CLAUDE.md]] | Guardrail #16 şablon ailesi |
| Session workflow | [[../../.workflows/session.md]] | Seans başlatma / kapatma akışı |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
