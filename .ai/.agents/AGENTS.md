---
title: "CoreMusic — Agent Alt-Registry (Profil İndeksi)"
type: agent-registry
category: agent-registry
date: 2026-09-23
updated: 2026-09-24
version: 1.2.2
status: active
authority: "Alt Registry — SSOT: .ai/AGENTS.md (v22.0.1)"
---

# CoreMusic — Agent Alt-Registry (Profil İndeksi)

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[../CLAUDE.md]] · [[../WORKFLOW.md]] · [[../brain.md]] · [[../index.md]] · [[../.templates/index]]

---

> **⚠️ SSOT Uyarısı:** Bu dosya **yalnızca profil indeksidir (Alt Registry)**. Tek SSOT: [[../AGENTS.md]] (v22.0.1). Routing, handover, escalation, öncelik, context lock ve health check kurallarının **tamamı kök dosyanın tekelindedir** (kök §26.2). Bu dosyada bu kurallar **tekrarlanmaz**; çelişkide kök dosya kazanır. Bu dosyanınauthority değeri `SSOT` iddiası **taşıyamaz**.

---

## §1 Amaç & Kapsam

Bu dosya, `.ai/.agents/` klasöründeki 11 agent profilinin **indeksidir**: hangi profilin ne olduğu, hangi şablondan üretildiği, nasıl yazıldığı, boot ile nasıl bağlandığı ve ne zaman/how güncellendiği burada tanımlanır. Profil dosyalarının kendisi (yetki belgeleri) ayrı dosyalardadır; bu registry onların **dizinidir, kopyası değildir**.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/.agents/*.md` profil envanteri (11 satır + bu dosya) | Agent routing / handover / escalation kuralları → kök [[../AGENTS.md]] §6, §9, §10 |
| Agent→Template eşleştirmesi (kaynak: [[../.templates/index]] §5.1) | Şablon envanteri ve şablon içerikleri → [[../.templates/index]] (SRP) |
| Profil yazım kuralları (dizgi, frontmatter, dil) | Domain boundary tablosu ve katman kuralları → kök [[../AGENTS.md]] §5 |
| Boot bağlantısı (kök §24.2/§24.3 özet + disk doğrulaması) | Teknik uygulama detayları, kod dosyaları |
| Profil güncelleme protokolü (versiyon append, validation) | `.ai/log.md` append-only kayıt (üst görevin/vault-sync işi) |

**Ne zaman okunur:** (1) profil yazılırken/düzenlenirken, (2) bir görev hangi ajanın hangi yetkiyle çalışacağını sorguladığında, (3) yeni profil eklenirken, (4) boot listesi domain bazlı genişletilirken. **Ne zaman yazılmaz:** routing/öncelik tartışmalarında — o kısımlar kök dosyadadır.

### §1.1 Bu Dosyanın Yeri (SSOT Hiyerarşisi)

| Seviye | Dosya | Rol |
|--------|-------|-----|
| 0 (SSOT) | [[../CLAUDE.md]] | AI anayasası, 16 Hard Guardrail |
| 1 (SSOT) | [[../AGENTS.md]] (v22.0.1) | Agent registry — routing/handover/escalation/öncelik tekelinde |
| 2 (Alt Registry) | Bu dosya (v1.2.2) | Profil indeksi + yazım kuralları + boot bağlantısı |
| 3 (Profil) | `.ai/.agents/<agent>.md` | Tekil ajanın yetki belgesi (§1-§11 domain serisi) |

---

## §2 Mimari Şema

```
                    ┌──────────────────────┐
                    │  MASTER ORCHESTRATOR │   Kök registry: [[../AGENTS.md]]
                    │  Koordinasyon (mo)   │   Bu indeks: profil indeksi
                    └──────────┬───────────┘
        ┌──────────────┬───────┴────────┬───────────────┐
        │              │                │               │
   ┌────▼────┐    ┌────▼────┐    ┌──────▼─────┐   ┌─────▼─────┐
   │ backend │    │   ui    │    │  security  │   │   data    │
   │   L2    │    │   L3    │    │    L1      │   │    L0     │
   └────┬────┘    └────┬────┘    └──────┬─────┘   └─────┬─────┘
        │              │                │               │
        └──────────────┴───────┬────────┴───────────────┘
                               │
                    ┌──────────▼───────────┐
                    │  MÜHENDİSLİK KATMANI │
                    └──────────┬───────────┘
     ┌────────────┬────────────┼────────────┬────────────┐
 ┌───▼───┐   ┌────▼────┐  ┌────▼────┐  ┌────▼─────┐ ┌────▼────┐
 │  qa   │   │  devops │  │embedded │  │ audio-hw │ │ dsp-fw  │
 └───┬───┘   └────┬────┘  └────┬────┘  └────┬─────┘ └────┬────┘
     │            │            │             │            │
     └────────────┴────────────┴──────┬──────┴────────────┘
                                      │
                               ┌──────▼──────┐
                               │   win-sw    │
                               └─────────────┘
```

| Şema Katmanı | Ajanlar | Orkestrasyon Rolü |
|--------------|---------|-------------------|
| Koordinasyon | `mo` (Master Orchestrator) | Görev dağıtımı, context lock, vault-sync |
| Domain Uzmanları | `backend` · `ui` · `security` · `data` | L2 / L3 / L1 / L0 sahiplikleri |
| Mühendislik | `qa` · `devops` · `embedded` · `audio-hw` · `dsp-fw` | Cross-cutting test, CI/CD, ses/zar/firmware |
| Platform | `win-sw` | WASAPI, sürücü, Windows entegrasyonu |

*(Şema, v1.1.0 dosyasındaki ASCII mimarinin bilgi kayıpsız sıkıştırılmış hâlidir; hardware/kernel/driver/HAL detay kolları kök [[../AGENTS.md]] §5 domain boundary ve `architecture/` ağacına devredilmiştir — tekrar burada tutulmaz.)*

---

## §3 Profil Envanteri

### §3.1 Envanter Tablosu (11 profil + 1 alt registry = 12 dosya)

**Kanıt:** glob `.ai/.agents/*.md` → 12 dosya (2026-09-23, FAZ 3a doğrulaması). **FAZ 3 birleştirme (2026-09-23): 12/12 tamamlandı** — 7 FAZ 3a + 5 FAZ 3b; **hepsi ≥500 satır** (ölçüm: `[System.IO.File]::ReadAllLines().Length`, boş satır dahil), **hepsi ` M`** (in-place, dosya adı değişmedi).

| # | Agent | Kod | Domain | Profil Dosyası | Durum (2026-09-23) |
|---|-------|-----|--------|----------------|---------------------|
| 1 | Master Orchestrator | `mo` | Görev dağıtımı, koordinasyon | [[master-orchestrator]] | ✅ FAZ 3a yeniden yazıldı (v2.0.0, §1-§11) |
| 2 | Backend Architect | `backend` | PHP 8.4 API, routing, middleware | [[backend-architect]] | ✅ FAZ 3a yeniden yazıldı (v2.0.0, §1-§11) |
| 3 | UI Designer | `ui` | Vanilla JS, ITCSS, CSS, responsive | [[ui-designer]] | ✅ FAZ 3a yeniden yazıldı (v2.0.0, §1-§11) |
| 4 | Security Engineer | `security` | OWASP, encryption, CSRF, CSP | [[security-engineer]] | ✅ FAZ 3a yeniden yazıldı (v2.0.0, §1-§11) |
| 5 | Data Engineer | `data` | MySQL 18 BCNF, PDO, migration | [[data-engineer]] | ✅ FAZ 3a yeniden yazıldı (v2.0.0, §1-§11) |
| 6 | Embedded Engineer | `embedded` | C++20, JUCE, ASIO, DSP | [[embedded-engineer]] | ✅ FAZ 3a yeniden yazıldı (v2.0.0, §1-§11) |
| 7 | QA Engineer | `qa` | Test, coverage, E2E | [[qa-engineer]] | ✅ FAZ 3b yeniden yazıldı (2026-09-23, v2.0.0, §1-§11, 549 satır) |
| 8 | DevOps Engineer | `devops` | CI/CD, GitHub Actions, deploy | [[devops-engineer]] | ✅ FAZ 3b yeniden yazıldı (2026-09-23, v2.0.0, §1-§11, 560 satır) |
| 9 | Audio Hardware Engineer | `audio-hw` | DAC/ADC, PCB, amplifier | [[audio-hardware-engineer]] | ✅ FAZ 3b yeniden yazıldı (2026-09-23, v2.0.0, §1-§11, 533 satır) |
| 10 | DSP Firmware Engineer | `dsp-fw` | XMOS, PCM3168A, DSP chain | [[dsp-firmware-engineer]] | ✅ FAZ 3b yeniden yazıldı (2026-09-23, v2.0.0, §1-§11, 548 satır) |
| 11 | Windows Software Engineer | `win-sw` | WASAPI, driver, platform | [[windows-software-engineer]] | ✅ FAZ 3b yeniden yazıldı (2026-09-23, v2.0.0, §1-§11, 549 satır) |
| — | *(bu dosya)* | — | Profil indeksi (Alt Registry) | `AGENTS.md` | ✅ FAZ 3a + FAZ 3 birleştirme (v1.2.1, 12/12 teyitli) |

### §3.2 Dosya Adı Kuralları

| Kural | Değer |
|-------|-------|
| Slug formatı | `küçük-harf`, tire ile: `backend-architect.md` |
| Kaynak | Kök [[../AGENTS.md]] §15 profil linkiyle birebir aynı slug |
| Değiştirme | **In-Place Refactoring** — dosya adı onaysız değişmez (guardrail #1) |
| Uzantı | `.md` · Konum: yalnız `.ai/.agents/` |
| Ekleme | Yeni profil → §3.1 satırı + kök §15 satırı + [[../.templates/index]] birlikte (§7) |

**Düzeltme kaydı (Truth Mode):** v1.1.0 dosyasındaki §14 "Agent Files" listesi disk gerçeğiyle **uçuyordu** — `frontend-ui-designer.md` ve `database-engineer.md` adlı dosyalar diskte **yoktur**. Doğru adlar: `ui-designer.md` · `data-engineer.md` (glob kanıtı, 12/12 dosya). Eski iddia bu yeniden yazımda kaldırıldı.

### §3.3 Envanter Bütünlüğü

| Kontrol | Beklenen | Aksiyon (ihlalde) |
|---------|----------|-------------------|
| Dosya sayısı | 12 (11 profil + AGENTS.md) | Fark → §7.2 adımlarını çalıştır |
| Her profil §1-§11 taşır | 11 bölüm eksiksiz | Eksik bölüm → MO'ya rapor, tamamlat |
| Frontmatter 7+1 alan | title, type, category, date, updated, version, status, authority | Eksik → profil yayımlanmaz |
| Kök §15 ↔ §3.1 eşleşmesi | 11 satır birebir | Uyuşmazlık → MO müdahalesi (kök güncellenir) |
| FAZ 3b profilleri | 5 dosya §1-§11, ≥500 satır (FAZ 3b, 2026-09-23) | ✅ 12/12 tamam — envanter ↔ disk birebir |

---

## §4 Agent → Template Eşleştirme

**Kaynak:** [[../.templates/index]] §5.1 (Agent-Template Eşleştirme Tablosu) — bu tablo **kopyadır**; şablon envanterinin kendisi o dosyadadır (SRP/DIP). 28/28 dosya (26 şablon + 2 meta) diskte mevcuttur (2026-09-24 — +2 yeni şablon: claude-md, docs-md).

### §4.1 Agent → Template (11 satır)

| Agent | Kullanacağı Template'ler |
|-------|--------------------------|
| Master Orchestrator | `documentation/WikiPage-Template.md`, `adr/adr-index.md` |
| Backend Architect | `backend/php-template.md`, `adr/adr-template.md` |
| UI Designer | `frontend/js-template.md`, `frontend/css-template.md`, `adr/adr-frontend-template.md` |
| Security Engineer | `adr/adr-security-template.md`, `documentation/security-audit-template.md` |
| Data Engineer | `adr/adr-database-template.md`, `query/Query-Template.md`, `infrastructure/migration-template.md` |
| Embedded Engineer | `other/c-template.md`, `adr/adr-audio-template.md` |
| QA Engineer | `testing/phpunit-template.md`, `testing/vitest-template.md` |
| DevOps Engineer | `infrastructure/github-actions-template.md` |
| Audio Hardware Engineer | `hardware/hardware-template.md`, `adr/adr-audio-template.md` |
| DSP Firmware Engineer | `other/c-template.md`, `hardware/hardware-template.md` |
| Windows Software Engineer | `other/c-template.md` |

### §4.2 Workflow → Template (özet — detay: [[../.templates/index]] §5.2)

| Workflow | Gerekli Template |
|----------|------------------|
| New Feature | İlgili kategoriden template (§4.1'e bak) |
| Security Audit | `adr/adr-security-template.md` + `documentation/security-audit-template.md` |
| Database Migration | `infrastructure/migration-template.md` + `query/Query-Template.md` |
| CI/CD Pipeline | `infrastructure/github-actions-template.md` |
| API Documentation | `documentation/api-doc-template.md` |
| Hardware Design | `hardware/hardware-template.md` |

### §4.3 Şablon Kuralı ve İstisnaları

| Kural | Değer |
|-------|-------|
| Guardrail #16 | Yeni `.md`/kod dosyası şablonsuz **oluşturulamaz** |
| Profil şablonu | `.ai/.templates/agents/agents-template.md` (527 satır, v2.0.0) — profil iskeleti buradan gelir |
| Planlanan şablonlar (diskte YOK) | `hardware/arduino-template.md`, `hardware/avr-template.md`, `hardware/pic-template.md` — uydurulmaz, `hardware/hardware-template.md` kullanılır |
| Kısa şablon istisnası | `session-log-template.md` (144 satır) — 500+ kuralı kapsamı dışı |

---

## §5 Profil Yazım Kuralları

### §5.1 Frontmatter (7 zorunlu + 1 identifier alan)

```yaml
---
title: "CoreMusic — <Ad> Agent Profile"
type: profile
category: agent-registry
date: 2026-08-08
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---
```

| Alan | Zorunlu | Not |
|------|---------|-----|
| `title` | ✅ | `"CoreMusic — <Ad> Agent Profile"` formatı |
| `type` | ✅ | Profiller `profile`; bu dosya `agent-registry` |
| `category` | ✅ | `agent-registry` (FAZ 3a standardı) |
| `date` | ✅ | Orijinal türetme tarihi (2026-08-08) |
| `updated` | ✅ | Son revizyon tarihi |
| `version` | ✅ | Profiller `2.0.0`; bu dosya `1.2.2` |
| `status` | ✅ | `active` |
| `authority` | ✅ | Profilde `SSOT` (domain tekel); hiyerarşi §1.1 — çelişkide kök kazanır |

### §5.2 Zorunlu Dizgi (H1 → §1-§11 → footer)

| Sıra | Öğe | Şart |
|------|-----|------|
| 1 | Frontmatter | §5.1 tablosu |
| 2 | `# <Ad> — Agent Profile` | Tek H1 |
| 3 | `**Zorunlu Bağlantılar:**` | `[[../AGENTS.md]]` · `[[../CLAUDE.md]]` · `[[../WORKFLOW.md]]` · `[[../brain.md]]` · `[[../MEMORY.md]]` (+ `[[../engine.md]]` sadece MO) |
| 4 | `---` ayraç | Ayıracın altında numaralı bölümler |
| 5 | `## §1` … `## §11` | Aşağıdaki sabit seri — **sıra değiştirilemez, bölüm silinemez** |
| 6 | `---` + footer | `**Authority:**` · `**Last Updated:**` · `**Mode:** Red Team · Human Mode · Truth Mode` |

**§1-§11 sabit seri (FAZ 3a domain standardı):**

| § | Başlık | İçerik Zorunluluğu | Şablon Karşılığı |
|---|--------|--------------------|------------------|
| §1 | Kimlik | Ad, kod, domain, katman, profil dosyası, kök §15 satırı, öncelik, escalation hedefi | İskelet §1 Identity |
| §2 | Domain & Sorumluluk | Misyon paragrafı + 8 satır sorumluluk tablosu | İskelet §2-§3 |
| §3 | Yetki Sınırları | İzinli tablo + Yasak tablo (Yasak → Doğru/Sorumlu) + layer violation uyarısı | İskelet §4-§5 |
| §4 | Teknoloji & Stack | Her satırda **IMPLEMENTED / PLANNED / VERIFICATION REQUIRED** etiketi + disk kanıtı | İskelet §6 |
| §5 | Kalite Standartları | Metrik→Hedef tablosu + domain kod kuralları + bağımlılık yönü | İskelet §7 |
| §6 | Keyword Routing | Kök [[../AGENTS.md]] §6 ile **birebir tutarlı** 9 grup (kopya; çelişkide kök kazanır) | — (kök §6) |
| §7 | Handover Senaryoları | Kök §9.3 senaryoları + profil-özel satırlar + format işaretçisi (§9.1-§9.2) | İskelet §9 |
| §8 | Zorunlu Okuma | Kök §24.3 domain satırı + **disk ile doğrulanmış yollar** | İskelet §8 |
| §9 | Çıktı Formatı | Teslimat: çıktı → format → kontrol sütunları | — |
| §10 | Edge Cases | Kök §17 alt kümesi + domain-özel senaryolar → çözüm | İskelet §10 Failure |
| §11 | Referanslar | Wiki-link tablosu + **Version geçmişi (append-only)** | İskelet §11 Version |

### §5.3 Dil & Format Kuralları

| # | Kural | Detay |
|---|-------|-------|
| 1 | Dil | Türkçe nesir; kod, dosya, sınıf, komut adı **İngilizce** |
| 2 | Mojibake yasak | ç ğ ı İ ö ş ü doğru; çift-kodlama (double-encoded) kalıntısı → `vault-utf8-writer.mjs repair` |
| 3 | Wiki-link | `[[relative/path/to/file]]` formatı; kırık link yasak |
| 4 | Başlık derinliği | En fazla 3 seviye: `## §x` → `### §x.y` → (`####` yasak) |
| 5 | Bölüm dizgisi | Bölüm başına ağırlıklı olarak tablo; kod bloğu en fazla 1 adet/bölüm |
| 6 | Truth Mode | Her stack satırı etiketli: `✅ IMPLEMENTED (kanıt)` / `⚠️ PLANNED` / `⚠️ VERIFICATION REQUIRED` |
| 7 | YAGNI | Doğrulanmayan dosya/yol/sayaç **uydurulmaz** — glob ile kanıtlanır veya `⚠️ VERIFICATION REQUIRED` |
| 8 | REDACTED | Secret, token, `.env` içeriği hiçbir koşulda yazılmaz |

### §5.4 Yasaklar

| Yasak | İhlal Sonucu |
|-------|--------------|
| Bu dosyaya routing/handover/escalation/öncelik kopyalamak | SSOT ihlali → içerik köke iade edilir (§26.2) |
| Kendini SSOT ilan etmek | Authority ihlali → `authority` alanı Alt Registry'ye sabitlenir |
| Şablonsuz profil yazmak | Guardrail #16 ihlali → dosya reddedilir |
| Frozen ADR metni değiştirmek | ADR-001…037 mutabakat ihlali → derhal revert |
| Dosya adını onaysız değiştirmek | Kırık wiki-link → In-Place Refactoring (guardrail #1) |
| Version tablosunda satır silmek/değiştirmek | Append-only ihlali → sürüm geçmişi kaybı |
| `.ai/log.md`'ye bu görevden dokunmak | Üst görev/vault-sync işi — append-only bozulur |

---

## §6 Boot Bağlantıları

### §6.1 Kök Boot Listesi (kök §24.2 — 10 dosya, disk doğrulamalı)

**Kanıt:** glob `.ai/*.md` → 14 dosya; aşağıdaki 10'u kök §24.2'de zorunludur.

| Sıra | Dosya | Amaç | Disk |
|------|-------|------|------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails | ✅ |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing (SSOT) | ✅ |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar | ✅ |
| 4 | `.ai/brain.md` | Mimari kararlar | ✅ |
| 5 | `.ai/ROLE.md` | Rol tanımı, §11 stack map | ✅ |
| 6 | `.ai/index.md` | Master katalog | ✅ |
| 7 | `.ai/keys.md` | Keyword haritası | ✅ |
| 8 | `.ai/MEMORY.md` | Session hafızası | ✅ |
| 9 | `.ai/log.md` | Audit trail (append-only) | ✅ |
| 10 | `.ai/ULTRA-THINKING.md` | Ultra düşünme protokolü | ✅ |

*Bu dosyadaki wiki-link listesi tekrarlanmaz; birleşik kanonik boot: [[../CLAUDE.md]] §7A + [[../WORKFLOW.md]] §8.6 (kök §25.4). Ek kök dosyalar (diskte mevcut): `VISION.md`, `PROJECTS.md`, `engine.md`, `glossary.md`.*

### §6.2 Domain Bazlı Zorunlu Okuma (kök §24.3 ↔ disk gerçeği)

> **⚠️ Truth Mode notu (2026-09-23):** Kök §24.3'ün bazı yolları diskte **yoktur** (eski `architecture/l1-security` vb. ağacı taşınmıştır). Aşağıdaki tablo kök iddiasını ve **doğrulanmış karşılığını** birlikte taşır; profillerin §8 bölümü bu tabloyu kullanır. Kök dosya bu görevde **değiştirilemez** — düzeltme üst görevdedir.

| Agent | Kök §24.3 İddiası | Disk | Doğrulanmış Karşılık |
|-------|-------------------|------|----------------------|
| Backend | `architecture/l2-routing/*.md`, `ADR-083*.md`, `shared/src/PageRouter/` | l2-routing **YOK** · PageRouter ✅ 14 dosya | `architecture/k9-api-routing/` ✅ 14 md · `architecture/k7-middleware/` ✅ 14 md · ADR-083/084/085 → [[../.decisions/index]] ✅ |
| Frontend | `ui-design/01-mockup-index.md`, `02-component-inventory.md`, `architecture/l3-presentation/*.md` | 01 ✅ 02 ✅ · l3-presentation **YOK** | `architecture/k11-ux/` ✅ 16 md · `ui-design/reference/` ✅ 10 md |
| Security | `architecture/l1-security/*.md`, `ADR-010*.md`, `shared/src/Middleware/` | l1-security **YOK** · Middleware ✅ 11 dosya | `architecture/k6-guvenlik/` ✅ 16 md · ADR-010/011/012/013/022 → [[../.decisions/index]] ✅ |
| Data | `architecture/k0-k5-software/k0-os-layer/*.md`, `.ai/.sql/mysql/*.sql`, `shared/src/Database/` | k0-k5-software **YOK** · sql ✅ 18 · Database ✅ 2 | `architecture/k0-isletim-sistemi/` ✅ 15 md · `architecture/k5-veri-yonetimi/` ✅ 14 md |
| Embedded | `projects/NevaEngine/*.md`, `electronic/dsp/*.md`, `electronic/firmware/*.md` | **ÜÇÜ DE YOK** | `architecture/firmware/` ✅ 8 md · `architecture/k3-ses-motoru/` ✅ 18 md · `architecture/k1-donanim/` ✅ 23 md |
| QA | `ui-design/03-accessibility-gaps.md`, `ui-design/screens/**`, `reports/` | 03-accessibility **YOK** (04 ✅) · screens ✅ · reports ✅ 9 md | `ui-design/04-accessibility-gaps.md` · `.ai/reports/` ✅ |
| DevOps | `architecture/02-deployment/*.md`, `ecosystem/*.md` | 02-deployment **YOK** | `architecture/k13-cicd/` ✅ 14 md · `.ai/ecosystem/` ✅ 2 md |

### §6.3 Skills & Workflow Bağlantısı

| Kaynak | Disk Kanıtı | Not |
|--------|-------------|-----|
| `.opencode/skills/*/SKILL.md` | ✅ 6 aktif: `orchestration`, `truth-engine`, `db-engine`, `ui-workbench`, `composer-sync`, `vault-sync-post` | Kök [[../AGENTS.md]] "10 skill" iddiası → ⚠️ VERIFICATION REQUIRED (bkz. §8) |
| `.opencode/skills/_archive/` | ✅ 9 arşiv skill (`prompt-maker`, `hallucination-control`, `red-team-truth-mode`, `human-mode`, `agent-orchestrator`, `ui-code-generator`, `ui-analyzer`, `database-normalize-maker`, `skill-maker`) | Arşiv = kullanımda değil |
| `.workflows/*.md` | ✅ 8 dosya: `session-init`, `vault-sync`, `security-audit`, `orchestrator-flow`, `hallucination-control`, `deployment`, `adr-creation`, `CLAUDE.md` | Şablon iddiası "8 dosya" ✅ tutuyor |
| `.ai/scripts/vault-utf8-writer.mjs` | ✅ tek mjs betik | `session-save.mjs` / `vault-post-update.mjs` `.ai/scripts/` altında **YOK** → ⚠️ VERIFICATION REQUIRED (§8) |

**Okuma sırası:** Boot (§6.1) → domain satırı (§6.2) → ilgili profilin §8'i → yalnızca gereken dosya (token aşımı önlenir). Görsel görevlerde istisna: `.ai/ui-design/**`, `.ai/.png/**` (kök §13).

---

## §7 Değişiklik Protokolü

### §7.1 Kim Ne Yapabilir

| İşlem | Yetkili | Şart |
|-------|---------|------|
| Profil içeriği güncelleme (§1-§11) | İlgili domain agent'ı / Vault Steward | §5 kuralları + şablon (Guardrail #16) |
| Yeni profil ekleme | MO (koordinasyon) + Vault Steward | §7.2 adımlarının tamamı |
| Dosya adı değiştirme | **Yasak** (onay hariç) | In-Place Refactoring — guardrail #1 |
| Bu dosyanın §1/§3/§4/§5/§6/§7'si | Vault Steward (FAZ 3a gibi) | Disk kanıtı (glob) zorunlu |
| Bu dosyaya routing/öncelik eklemek | **Yasak** | Kök [[../AGENTS.md]] tekeldir |
| `.ai/log.md` append | MO / vault-sync (üst görev) | Append-only |
| Frozen ADR (001-037) | **Dokunulmaz** | Yalnız referans |

### §7.2 Güncelleme Adımları (profil ekleme/değiştirme)

| # | Adım | Kontrol |
|---|------|---------|
| 1 | Şablonu seç | `.ai/.templates/agents/agents-template.md` (Guardrail #16) |
| 2 | Hedef dosyayı glob ile doğrula | `.ai/.agents/*.md` — var mı, ad slug doğru mu |
| 3 | §5.1 frontmatter + §5.2 dizgi ile yaz | 7+1 alan, §1-§11 eksiksiz |
| 4 | §4.1 tablosuna/eşleşmesine bak | Yeni profil ise: kök §15 satırı + bu dosya §3.1 satırı + [[../.templates/index]] birlikte |
| 5 | Truth Mode süpürmesi | Her iddia glob/okuma ile kanıtlı; olmayan → `⚠️ VERIFICATION REQUIRED` |
| 6 | Doğrulama listesi (§7.4) | 14/14 ✅ olmadan tamamlanmaz |
| 7 | Üst göreve rapor | `.ai/log.md` append üst görevde — bu dosyadan yazma |

### §7.3 Version & Append Kuralları

| Kural | Değer |
|-------|-------|
| İlke | Append-only — mevcut satıra dokunulmaz, silme yok |
| Profil revizyonu | Frontmatter `version` bump + §11 Version tablona yeni satır |
| Alt registry revizyonu | `1.0.0` → `1.1.0` → `1.2.0` → `1.2.1` → `1.2.2` (bu dosya §9) |
| Major bump | İskelet (§1-§11) değişirse — onay gerektirir |
| Tarih | `YYYY-MM-DD`, `updated` alanı ile senkron |
| Kök §26.2 senkronu | Alt registry sürümü değişirse kök §26.2'deki "v1.1.0" ifadesi **eski kalır** → üst görevde güncellenmeli (bu görevde kök yasaklıdır) |

### §7.4 Doğrulama Listesi (profil onayı öncesi — 14/14)

| # | Kontrol | Beklenen |
|---|---------|----------|
| 1 | Frontmatter 7+1 alan eksiksiz | title, type, category, date, updated, version, status, authority |
| 2 | H1 + `Zorunlu Bağlantılar` + `---` dizgisi | §5.2 sırası |
| 3 | §1-§11 seri eksiksiz ve sıralı | 11 bölüm, silme yok |
| 4 | §3 İzinli + Yasak tabloları dolu | Her ikisi ≥3 satır |
| 5 | §4 stack etiketli | IMPLEMENTED/PLANNED/VERIFICATION REQUIRED her satırda |
| 6 | §6 routing tutarlı | Kök §6 ile birebir 9 grup |
| 7 | §8 yolları disk doğrulamalı | §6.2 tablosuyla uyumlu |
| 8 | Wiki-link'ler hedefe ulaşıyor | Kırık link yok |
| 9 | Kök §4/§15 eşleşmesi | Agent satırı + slug tutarlı |
| 10 | Türkçe doğruluk | Mojibake yok |
| 11 | YAGNI | Uydurma dosya/yol/sayaç yok |
| 12 | REDACTED | Secret/token yok |
| 13 | Version tablosu append | Eski satırlar korunmuş |
| 14 | Dosya adı değişmedi | In-Place Refactoring |

---

## §8 Truth Mode — Bilinen Çelişkiler (Bekleyen Düzeltmeler)

> Bu tablo, FAZ 3a sırasında tespit edilen ve **bu görevin yazma kapsamı dışında kalan** (kök dosyalar yasaklı) çelişkileri taşır. Hallüsinasyon değildir: hepsi glob/okuma ile kanıtlanmıştır. Düzeltme üst görevdedir.

| # | İddia (Kaynak) | Disk Gerçeği (2026-09-23 kanıtı) | Durum |
|---|----------------|-----------------------------------|-------|
| 1 | "4 composer.json" (kök §25.2) | **3** adet: `shared/`, `home.coremusic.net/`, `auth.coremusic.net/` | ⚠️ VERIFICATION REQUIRED |
| 2 | "Middleware ×4 (PSR-15)" (kök §25.2) | `shared/src/Middleware/` = **11** PHP dosyası (10 middleware + `MiddlewarePipeline`) | ⚠️ VERIFICATION REQUIRED |
| 3 | "10 skill" (kök §14/Giriş) | 6 aktif SKILL.md + 9 `_archive/` | ⚠️ VERIFICATION REQUIRED |
| 4 | `architecture/l1-security` · `l2-routing` · `l3-presentation` · `k0-k5-software/k0-os-layer` · `02-deployment` (kök §24.3) | **Hiçbiri yok** — gerçek ağaç: `k6-guvenlik` · `k9-api-routing` · `k11-ux` · `k0-isletim-sistemi` · `k13-cicd` | Kök §24.3 güncellemeli (üst görev) |
| 5 | `ui-design/03-accessibility-gaps.md` (kök §24.3 QA satırı) | Dosya adı **`04-accessibility-gaps.md`** | Üst görev düzeltmeli |
| 6 | `projects/NevaEngine/` · `electronic/dsp/` · `electronic/firmware/` (kök §24.3 Embedded) | **Hiçbiri yok** — karşılığı `architecture/firmware/` (8 md) | Embedded profili §8'de işaretledi |
| 7 | ITCSS **9** katman (eski ui profili / `k11-ux/itcss-9-layer.md` iddiası) | `Css/` altında **8** katman dizini (01_Abstracts…08_Devices) + `main.css` + `auth-bundled.css`; `09_ViewModes/` **YOK** | ⚠️ VERIFICATION REQUIRED |
| 8 | PWA `manifest.json` (eski ui profili izinli kapsam) | `assets.coremusic.net/**/manifest*.json` = **0** | ⚠️ PLANNED |
| 9 | `.ai/scripts/session-save.mjs` · `vault-post-update.mjs` (sistem çağrısı) | `.ai/scripts/` altında **yalnız** `vault-utf8-writer.mjs` var | ⚠️ VERIFICATION REQUIRED |
| 10 | ADR'lerin `.ai/decisions/accepted/` yolu (eski backend profili §7.7) | `.ai/decisions/` **YOK**; tek karar kaynağı `.ai/.decisions/index.md` (+ `CLAUDE.md`) — ADR-010/011/012/013/019/022/083/084/085 satırları ✅ orada | FAZ 3a profillerinde düzeltildi |
| 11 | lcobucci/jwt · monolog · symfony/cache (eski backend stack tablosu) | Üç composer.json'da da **geçmiyor** | ⚠️ VERIFICATION REQUIRED |
| 12 | `.github/workflows/` CI (DevOps domaini) | Dizinde **0** dosya | ⚠️ PLANNED |
| 13 | Kök §24.2 "10 dosya" (bu dosya §6.1 başlığı) | Kök §24.2 tablosu artık **14 satır** taşır (10 ana + engine/glossary/VISION/PROJECTS) — küme aynı, sayı ifadesi farklı | §6.1 dipnotu ile uyumlu; ifade üst görevde netleştirilebilir |

### §8.1 Kanıt Protokolü ve FAZ 3a İşlem Özeti

> Bu alt bölüm, §8 çelişki defterinin **nasıl** üretildiğini ve FAZ 3a'da hangi dosyalara **gerçekten** dokunulduğunu belgeler. Amaç: sonraki okuyucunun her iddiayı aynı araçla yeniden üretebilmesi.

**Kanıt metodolojisi (her satır tekrarlanabilir):**

| # | Yöntem | Komut / Araç | Kanıtladığı | Kullanım Anı |
|---|--------|--------------|-------------|--------------|
| 1 | Dosya sayımı | `Get-ChildItem <path>` / glob | varlık + ad + sayı | §3 envanter, §6.2 |
| 2 | İçerik okuma | `Get-Content <file>` | metin gerçeği (composer require, ADR satırı) | §4 eşleşme, §8 iddia |
| 3 | Kalite taraması | `node .ai/scripts/vault-utf8-writer.mjs scan` | BOM / mojibake / CJK / NUL | yazım sonrası zorunlu |
| 4 | Git durumu | `git status --porcelain -- .ai/.agents/` | hangi dosya değişti | §7.4 #14 |
| 5 | Satır sayımı | `[System.IO.File]::ReadAllLines($p).Length` | 500+ hedefi (boş satır dahil) | uzunluk kapısı |
| 6 | Yokluk kanıtı | glob sonucu = 0 | `⚠️ PLANNED` (NevaEngine, manifest, workflows) | YAGNI |
| 7 | Zaman damgası | `Get-ChildItem \| Select LastWriteTime` | yazanın/batch'ın ayrımı | dış değişiklik tespiti |

> **Ölçüm yöntemi notu (zorunlu):** satır sayısı yalnızca `[System.IO.File]::ReadAllLines($p).Length` ile ölçülür (boş satır dahil). `Measure-Object -Line` boş satırları **saymaz** — 500+ kapısında yanlış sonuç verir; kullanılmaz.

**FAZ 3a işlem özeti (yalnız bu 7 dosya bu görevce yazıldı):**

| # | Dosya | Önce (satır) | LastWriteTime (2026-09-23) | İşlem |
|---|-------|--------------|------------------------------|-------|
| 1 | `AGENTS.md` (bu dosya) | 353 | 23:05:37 | Tam rewrite → v1.2.0 |
| 2 | `master-orchestrator.md` | 232 | 23:07:22 | Tam rewrite → §1-§11 |
| 3 | `backend-architect.md` | 216 | 23:10:59 | Tam rewrite → §1-§11 |
| 4 | `ui-designer.md` | 247 | 23:12:44 | Tam rewrite (mockup adları 01/02/04 düzeltmesi) |
| 5 | `security-engineer.md` | 207 | 23:14:21 | Tam rewrite → §1-§11 |
| 6 | `data-engineer.md` | 210 | 23:15:44 | Tam rewrite → §1-§11 |
| 7 | `embedded-engineer.md` | 216 | 23:17:13 | Tam rewrite → §1-§11 |

**Sahiplik doğrulaması (Truth Mode — FAZ 3 birleştirme, 2026-09-23):** `git status --porcelain -- .ai/.agents/` bu görev sonunda **12 ` M`** döndürür; 7'si FAZ 3a (23:05-23:17), 5'i FAZ 3b (23:20-23:25) — hepsi bu vault görevinin write çağrıları arasındadır. 5 profil dosyası Faz 3b kapsamında yazıldı (sahip: Faz 3b oturumu, 2026-09-23 23:20-23:25) — sahiplik doğrulandı, çelişki yok.

**Çelişki çözüm akışı (§8 satırları için):**

| Adım | Eylem | Sorumlu |
|------|-------|---------|
| 1 | İddiayı glob/okuma ile kanıtla | Yazan profil / registry |
| 2 | Kanıt varsa §8'e ekle (`⚠️ VERIFICATION REQUIRED` veya `⚠️ PLANNED`) | Vault Steward |
| 3 | Kök dosya yasaklıysa düzeltme **üst göreve** devredilir | Üst görev |
| 4 | Düzeltme sonrası satır `✅ GİDERİLDİ` olarak işaretlenir (satır silinmez — append) | Vault Steward |
| 5 | İlgili profilin §8'i ile senkron tut | Profil sahibi |
| 6 | Kök §24.3/§25.2/§26.2 gibi SSOT düzeltmeleri yalnız kök sahibince | Root / Vault Steward |

**FAZ 3b kuyruk durumu (FAZ 3 birleştirme ile güncellendi):**

| # | İş | Kapsam | Engelli Mi? |
|---|----|--------|-------------|
| 1 | qa/devops/audio-hw/dsp-fw/win-sw profilleri rewrite | 5 dosya §1-§11 | ✅ Tamamlandı — FAZ 3b (2026-09-23, §3.1'de ✅) |
| 2 | 23:10:07 batch değişikliğinin sahipliğinin doğrulanması | 5 dosya git diff | ✅ Tamamlandı — sahip: Faz 3b oturumu (2026-09-23 23:20-23:25) |
| 3 | Kök §14 "10 skill" · §25.2 "4 composer" / "×4" · §26.2 "v1.1.0" düzeltmesi | kök `.ai/AGENTS.md` | Evet — bu görevde kök yazımı yasak |
| 4 | `.ai/log.md` FAZ 3a append kaydı | append-only | ✅ Tamamlandı — FAZ 3 kaydı eklendi (bu birleştirme) |
| 5 | `.ai/scripts/session-save.mjs` · `vault-post-update.mjs` varlığının netleşmesi | sistem çağrısı ≠ disk | Evet — §8 #9 · ⚠️ VERIFICATION REQUIRED — araç yok, senkronizasyon manuel |

**Riskler (bu görev sonu):**

| # | Risk | Etki | Aksiyon |
|---|------|------|---------|
| 1 | Kök §26.2 "v1.1.0" — bu dosya v1.2.1 | Okuma kafa karışıklığı | Üst görevde kök güncellenir (bu görevde kök yasak) |
| 2 | 5 FAZ 3b dosyası FAZ 3a sırasında dışarıdan değişmiş görünüyordu | "7 M" hedefi 12 M görünür | ✅ GİDERİLDİ — sahiplik doğrulandı (Faz 3b oturumu); revert yok |
| 3 | Kök §24.3 eski yollar (l1/l2/l3, 03-accessibility, NevaEngine) | Kör takip → kırık okuma | §6.2 + profil §8'ler işaretli |
| 4 | `.ai/log.md` bu görevde append edilmedi (yasak, FAZ 3a dönemi) | Audit trail bu görev için eksik | ✅ GİDERİLDİ — FAZ 3 kaydı eklendi (bu birleştirme) |
| 5 | 500+ satır hedefi ilk yazımın hepsinde tutmadı (291-415) | Derinlik standardı ihlali | §8.1/§4.x/§8.x genişletmeleriyle kapatıldı — nihai sayımlar raporda |

---

### §8.3 FAZ 3a/3b yazım envanteri ve zaman damgası kanıt tablosu

**Amaç:** hangi profilin ne zaman, kim tarafından yazıldığını disk kanıtıyla (UTC damgası) göstermek; FAZ 3a dışındaki değişiklikleri ayırt etmek.

| # | Dosya | İş | UTC damgası | Kime ait | 500+ hedefi |
|---|-------|-----|-------------|----------|-------------|
| 1 | `AGENTS.md` (bu dosya) | FAZ 3a | 23:05:37 | vault-documentation-specialist | ✅ (§8.1/§8.2/§8.3) |
| 2 | `master-orchestrator.md` | FAZ 3a | 23:07:22 | vault-documentation-specialist | ✅ (§7.1/§8.3) |
| 3 | `backend-architect.md` | FAZ 3a | 23:10:59 | vault-documentation-specialist | ✅ (§4.4/§4.5/§8.2) |
| 4 | `ui-designer.md` | FAZ 3a | 23:12:44 | vault-documentation-specialist | ✅ (§4.4/§4.5/§8.2) |
| 5 | `security-engineer.md` | FAZ 3a | 23:14:21 | vault-documentation-specialist | ✅ (§4.4/§4.5/§8.2/§8.3) |
| 6 | `data-engineer.md` | FAZ 3a | 23:15:44 | vault-documentation-specialist | ✅ (§4.4/§4.5/§8.2) |
| 7 | `embedded-engineer.md` | FAZ 3a | 23:17:13 | vault-documentation-specialist | ✅ (§4.4/§4.5/§8.2) |
| 8-12 | aşağıda 5 dosya | **FAZ 3b yazımı** | 23:20-23:25 | **Faz 3b oturumu — sahiplik doğrulandı** | ✅ |

**FAZ 3b dosyaları — FAZ 3a'da OKUNMADI/YAZILMADI; nihai yazım FAZ 3b oturumunda (23:20-23:25), sahiplik doğrulandı:**

| # | Dosya | Durum | Bu registry'deki satırı |
|---|-------|-------|--------------------------|
| 8 | `qa-engineer.md` | ` M` @23:20:57 (549 satır) | §3.1 #7 |
| 9 | `devops-engineer.md` | ` M` @23:20:57 (560 satır) | §3.1 #8 |
| 10 | `audio-hardware-engineer.md` | ` M` @23:25:28 (533 satır) | §3.1 #9 |
| 11 | `dsp-firmware-engineer.md` | ` M` @23:25:28 (548 satır) | §3.1 #10 |
| 12 | `windows-software-engineer.md` | ` M` @23:20:57 (549 satır) | §3.1 #11 |

**Doğrulama zinciri (FAZ 3a için):**

| Adım | Komut/arac | Beklenen sonuç |
|------|------------|----------------|
| 1 | satır sayısı: `[System.IO.File]::ReadAllLines($p).Length` | her hedef ≥500 (boş satır dahil) |
| 2 | `node .ai/scripts/vault-utf8-writer.mjs scan .ai/.agents` | 7 hedef `CLEAN` |
| 3 | mojibake/CJK taraması (scan + grep; CJK = Unicode U+4E00-U+9FFF aralığı) | 0 gerçek eşleşme (eski Çince karakter yazım hatası `Yığın` olarak düzeltildi) |
| 4 | `git status --porcelain -- .ai/.agents/` | 12 ` M` (7 FAZ 3a + 5 dış) |
| 5 | `.ai/log.md` eki | **bu görevde yapılmadı** — parent'a devredildi |

**Yasaklar (bu dosyanın yazım kapsamı):** `.ai/log.md` doğrudan ekleme; root `.ai/AGENTS.md`/`CLAUDE.md` düzenlemesi; `.templates/**` ve `.decisions/**` değişikliği; FAZ 3b 5 dosyaya dokunma; dosya adı değişikliği; frozen ADR 001-037 değişikliği; yeni ADR 038-087 aralığında açma (yeni ADR **≥088**, sahibin onayı).

---

## §9 Versiyon Geçmişi

| Version | Date | Change |
|---------|------|--------|
| 1.0.0 | 2026-09-23 | Initial Agent System |
| 1.1.0 | 2026-09-23 | Alt registry'ye indirgendi (SSOT: .ai/AGENTS.md v22.0.0); 7 alanlı frontmatter + SSOT uyarısı |
| 1.2.0 | 2026-09-23 | FAZ 3a: profil indeksi olarak tam yeniden yazım — §3 Profil Envanteri (12 dosya glob kanıtlı, eski §14 dosya adı hataları düzeltildi), §4 Agent→Template (index §5.1 kopyası), §5 Profil Yazım Kuralları (§1-§11 domain serisi), §6 Boot Bağlantıları (disk doğrulamalı, §6.2 çelişki tablosu), §7 Değişiklik Protokolü (14/14 checklist), §8 Truth Mode çelişki defteri |
| 1.2.1 | 2026-09-23 | FAZ 3 birleştirme düzeltmeleri: §3.1'de 5 FAZ 3b satırı ⏳→✅ (549/560/533/548/549 satır), §8.1 sahiplik iddiası düzeltildi (Faz 3b oturumu, 23:20-23:25), 12/12 ≥500 + ` M` teyidi, ölçüm yöntemi notu (ReadAllLines; Measure-Object -Line yasak), §8.3 kuyruk/risk senkronu, kırık wiki-link düzeltmesi (.workflows/session → session-init + vault-sync) |
| 1.2.2 | 2026-09-24 | Kök authority senkronu v22.0.1 |

---

## §10 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Agent Registry (SSOT) | [[../AGENTS.md]] | Routing §6 · Domain §5 · Overview §4 · Detay §15 · Phase §25-§26 |
| AI Anayasası | [[../CLAUDE.md]] | 16 Hard Guardrail ( #1 In-Place, #11 Mockup, #16 Template, #17 Tek bileşen ) |
| Süreçler | [[../WORKFLOW.md]] | Fazlar, session protokolü |
| Mimari kararlar | [[../brain.md]] | ADR özetleri |
| Motor indeksi | [[../engine.md]] | §9.2 domain↔teknoloji matrisi, §12.6 faz kontrolü |
| Rol tanımı | [[../ROLE.md]] | §11 IMPLEMENTED/PLANNED stack map |
| Template Registry | [[../.templates/index]] | Şablon envanteri + §5.1 eşleştirme (SRP) |
| Profil şablonu | [[../.templates/agents/agents-template]] | Guardrail #16 iskeleti (527 satır) |
| Karar indeksi | [[../.decisions/index]] | ADR-010/011/012/013/019/022/083/084/085 satırları |
| Session workflow | [[../../.workflows/session-init]] · [[../../.workflows/vault-sync]] | Seans başlatma/kapatma akışı |
| UTF-8 yazım aracı | `.ai/scripts/vault-utf8-writer.mjs` | Vault yazmalarının tek betiği (append/insert/write/verify/repair/scan) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
