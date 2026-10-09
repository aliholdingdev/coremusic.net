---
title: "Doğrulama"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 28.4.4
updated: 2026-10-06
total_files: 720
total_adr: 80
total_adr_disk: 60
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---# Doğrulama## Validation

### §11 Test & Ekosistem

### §11.1 Test

> **Durum (Faz 0, 2026-09-08):** `.ai/testing/` dizini vault ağacında MEVCUT DEĞİLDİR — aşağıdaki referanslar tarihsel plan kaydıdır; kullanılabilir karşılıklar: `.ai/reports/` (3 rapor), `ui-design/04-accessibility-gaps.md` (WCAG denetimi — eski `03-accessibility-gaps.md` adı geçersiz), `ui-design/screens/` (ekran spec'leri).

| Dosya | Kapsam |
|-------|--------|
| [[testing/strategy]] | Test stratejisi *(dizin yok — plan kaydı)* |
| [[testing/coverage-targets]] | Kapsama hedefleri (≥80% min, ≥90% target) |
| [[testing/e2e-template]] | E2E test şablonu |
| [[.personas/methodology]] | Persona test protokolü *(yol düzeltildi 2026-09-26 — eski `testing/persona-test-protocol` hedefi yok)* |
| [[testing/test-plan]] | Test planı |
| [[.personas/test-scenarios-mapping]] | Test senaryoları eşleme |

---

### §11.2 Ekosistem

| Dosya | Kapsam |
|-------|--------|
| [[ecosystem/service-integration]] | 7-servis entegrasyonu *(yol düzeltildi 2026-10-07 — eski `7-service-integration` hedefi yok)* |
| [[ecosystem/index]] | Health check endpoint'leri *(hedef dosya yok — modül indeksi 2026-10-07)* |
| [[ecosystem/index]] | Servis iletişim kalıpları *(hedef dosya yok — modül indeksi 2026-10-07)* |
| [[ecosystem/index]] | 10-panel entegrasyonu *(hedef dosya yok — modül indeksi 2026-10-07)* |
| [[ecosystem/index]] | Hata kurtarma stratejileri *(hedef dosya yok — modül indeksi 2026-10-07)* |
| [[ecosystem/index]] | Ağ mimarisi *(hedef dosya yok — modül indeksi 2026-10-07)* |
| [[ecosystem/index]] | State machine'ler *(hedef dosya yok — modül indeksi 2026-10-07)* |

---

### §16 Test Coverage Hedefleri

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

---

### §19 Faz 1 Doğrulama Anlık Görüntüsü (2026-09-08)

### §19.1 Envanter Metrikleri (Faz 0 ölçümü)

| Metrik | Değer | Yöntem |
|--------|-------|--------|
| Vault MD | 741 dosya / 136.261 satır (boş-hariç) | Get-ChildItem + Measure-Object |
| Ortalama satır | 184/dosya | Toplam ÷ dosya |
| Hedef (Faz 1) | ≥500 satır/dosya | Kullanıcı direktifi |
| Kırık referans kümesi | 60+ (5 kategori) | Test-Path taraması |
| Sayım çelişkisi | 8 (hepsi bu revizyonda düzeltildi) | Cross-check |

---

### §19.2 Sayım Düzeltme Kaydı

| İddia | Eski | Yeni | Kanıt |
|--------|------|------|-------|
| PNG mockup | 18 | **19** (12+1+6) | .ai/.png/ sayımı |
| Vault dosya | 726 | **787** | stub'lar dahil sayım |
| ADR kapsamı | 001-087 | **001-089** (80) | decisions sayımı |
| Active ADR | 50 | **31** | §5.2 satır sayımı |
| Kök MD | 11 | **12** | glossary.md dahil |

---

### §19.3 Domain Uygulama Durumu (IMPLEMENTED/PLANNED)

| # | Domain | Teknoloji | Durum | Kanıt |
|---|--------|-----------|-------|-------|
| 1 | shared/ | PHP 8.4 | **IMPLEMENTED** | composer.json (v2.0.0) |
| 2 | packages/shared/ (ADR-085 ile shared/ altına alındı — fiziksel dizin yok) | PHP 8.3 | **FİZİKSEL DEĞİL** | Test-Path: kökte packages/ yok |
| 3 | auth.coremusic.net | PHP 8.4 | **IMPLEMENTED** | composer.json + include/ |
| 4 | home.coremusic.net | PHP 8.4 | **IMPLEMENTED** | composer.json + include/ |
| 5 | assets.coremusic.net | Statik | **IMPLEMENTED** | Css/Fonts/Image/js |
| 6-14 | api, music, admin, car, studio, pro, media, download, landing | PHP/Node.js/C++20 | PLANNED | Test-Path: dizin yok |

---

### §19.4 Kırık Referans Çözüm Durumu

| Küme | Hedef | Çözüm |
|------|-------|-------|
| Arşiv tarihi | prompt*-2026-08-13 (12 link, 5 dosya) | ✅ 2026-09-01'e hizalandı |
| .ai/testing/ | index.md §11, keys.md | ✅ "dizin yok" notu + gerçek karşılıklar |
| .ai/workflows/ | index.md §12 | ✅ uyarı notu mevcut; kök `.workflows/` gerçek konum |
| Electronic kök 8 dosya | keys.md §7, brain.md §21 | ✅ DOĞRULAMA GEREKLİ etiketi + alt klasör yönlendirme |
| models/issues/scripts index | CLAUDE.md §17 | Bekliyor — dizinler yok; oluşturma kararı kullanıcıda |

---

### §19.5 UI-Design Disk Gerçekliği (Faz 7 ölçümü — 2026-09-29)

> Bu tablo `.ai/ui-design/` sayımının **tek güncel kaynağıdır**; eski sayaç ifadeleri bu ölçümle geçersizdir. Faz/commit kanıtı: [[AGENTS.md]] §13.8 · kapı betikleri: [[WORKFLOW.md]] §8.7A.

| Yüzey | Sayım |
|-------|-------|
| Kök `0*.md` | **6** — `00-device-matrix` · `01-mockup-index` · `02-component-inventory` · `03-implementation-plan` · `04-accessibility-gaps` · `05-responsive-architecture` |
| `screens/` | **21 md** = 1 indeks + **20 spec** (T07-embedded **12** · T17-monitor-22fhd **2** · shared **6**) |
| `flow/` | **21 md** = `00-flow-index` + 20 (auth 5 · music 5 · settings 4 · navigation 3 · automotive 2 · watch 1) |
| `prompt/` | **51 md** = **48 içerik** (component **16** · page **12** · layout **10** · screen **10**) + `prompt/00-prompt-index` + `screen/00-prompt-index` + `web-research` |
| `reference/` · `tokens/` | **17 md** (11 üst düzey + `figma/` 6) · **4 md + 7 json** |
| Görsel / ham | `.ai/.png` **19 PNG** (12 · 1 · 6) · `reference/figma/png` **151 hedef · 136 indirilen · 15 gizli node (`visible: false`) → API NULL · 13 legacy · dizin 149** (Faz 8b — `ui-design/reference/04-verification` §7.1) · `reference/figma/raw` **19 JSON / 79.7 MB** (15 sayfanın **4'ü boş**) |

---

### §19.5 Boot Dosyası Revizyon Durumu

| Dosya | Satır (boş-hariç) | Durum |
|-------|-------------------|-------|
| engine.md | 503 | ✅ |
| glossary.md | ~500 | ✅ (v2.0.0 tam revizyon) |
| ROLE.md | 500 | ✅ (v6.0.0 tam revizyon) |
| ULTRA-THINKING.md | ~510 | ✅ (v2.0.0 tam revizyon) |
| MEMORY.md | ~490 | ✅ (v25.0.0) |
| CLAUDE.md | ~575 | ✅ (düzeltmeler) |
| brain.md | ~745 | ✅ (düzeltmeler) |
| keys.md | ~530 | ✅ (düzeltmeler) |
| WORKFLOW.md | ~590 | ✅ (düzeltmeler) |
| index.md | bu bölümle | ✅ |
| AGENTS.md | ~500 | ✅ (düzeltmeler + §25) |
| log.md | append-only | Faz kapanışında append (✅ 2026-09-08 kaydı düştü) |

---

### §19.6 Yöntem Notu

1. Hedef metrik "boş-hariç satır"tır (`Measure-Object -Line`) — Faz 0 envanteriyle aynı ölçüt.
2. Her genişletme satırı bilgi taşır: kanıt yolu, tablo, ASCII şema veya doğrulama kaydı; doldurma/fluff yasaktır.
3. Frozen ADR metinlerine ve arşiv dosyalarına dokunulmadı; yalnız referanslar doğrulandı.
4. **2026-09-24 taze ölçüm:** `.ai` recursive = 587 `.md` / 650 toplam dosya; `.ai/.templates` = 36 `.md` (`Get-ChildItem -Recurse -Force -Filter *.md`). §18'deki 518 / template 19 değerleri bu disk ölçümüyle düzeltildi (587 / 36 — ölçüm 2026-09-24 21:37); sahip yeniden doğrulaması gerekirse §18 satırı üzerinden yapılır. Not: eşzamanlı yazım nedeniyle `.md` ve toplam dosya sayıları gün içinde artabilir.

---

### §21 Kırık Referans Kataloğu (Faz 0 taraması — çözüm durumu)

Bu bölüm, vault genelinde tespit edilen kırık referans kümelerini ve çözüm durumlarını izler. Kural: her çözüm `log.md`'ye kaydedilir; yenisi bulundukça buraya eklenir.

| # | Küme | Etkilenen Dosyalar | Çözüm Durumu |
|---|------|--------------------|--------------|
| 1 | `archives/prompt*-2026-08-13` (12 link) | brain.md §22, WORKFLOW §8.6, ROLE §10, MEMORY §5, keys §3B | ✅ 2026-09-01'e hizalandı |
| 2 | `.ai/testing/` dizini (6 dosya) | index.md §11.1, keys.md §11/§14, ROLE.md, ULTRA-THINKING §3.2 | ✅ "dizin yok" notu + gerçek karşılıklar |
| 3 | `.ai/workflows/` dizini (8 dosya) | index.md §12 | ⚠️ uyarı notu mevcut; gerçek konum kök `.workflows/` |
| 4 | Electronic kök 8 dosya | keys.md §7, brain.md §21, index.md §10 | ✅ DOĞRULAMA GEREKLİ + alt klasör yönlendirme |
| 5 | `electronic/frequency-response` ve `snr-thd` yanlış yol | keys.md §7 | ✅ `electronic/hardware/` altına yönlendirildi |
| 6 | `models/issues/scripts` index yok | CLAUDE.md §17 | ⚠️ Kullanıcı kararı bekliyor (oluştur / referans kaldır) |
| 7 | `subdomains/README` + download index | keys.md §8, index.md §12 | ⚠️ Oluşturma kararı bekliyor |
| 8 | `projects/NevaEngine/eq-dsp-chain` | keys.md §6 | ⚠️ stub kapsamı dışı — index.md §9 notuyla tutarlı |
| 9 | `research/`, `personas/` → **`.ai/.personas/` KURULDU (2026-09-26)**, `registry/`, `scaffold/`, `knowledge/`, `confidence/`, `sessions/` | index.md §12 | ✅ personas yeniden kuruldu (`.ai/.personas/` — 5 kök + 6 senaryo + 6 grup); `research/registry/scaffold/knowledge/confidence/sessions` ⚠️ §12 güncellik notu mevcut; yeniden kurma kararı kullanıcıda |
| 10 | `ui-design/reference/02-design-tokens` | keys.md §3A | ✅ `ui-design/tokens/design-tokens-master.md` |
| 11 | l4/l5 flat vs index | (Obsidian fallback) | ✅ flat dosyalar mevcut — sorun değil |
| 12 | `[[reference/yaml-formatter]]` | WORKFLOW §8.8 | ⚠️ DOĞRULANAMADI — test edilmedi |

**Özet:** 7 çözüldü (personas dahil — `.ai/.personas/` 2026-09-26) · 5 kullanıcı kararı bekliyor. Bekleyenler kod üretimi gerektirmez; doküman/dizin kararıdır.

---

### §21.1 Metodoloji

1. Referans çıkarma: boot + index dosyalarındaki `[[...]]` ve düz yol pattern'leri toplandı.
2. Doğrulama: her hedef Test-Path ile sınandı (PowerShell 5.1, Faz 0).
3. Kümeler: aynı kök nedenli kırıklar tek küme olarak gruplandı.
4. Çözüm: kanıtlı karşılık varsa yönlendirme; yoksa DOĞRULAMA GEREKLİ/kullanıcı kararı.
5. Bakım: bu katalog her faz kapanışında güncellenir — yeni kırık → yeni satır.

---

### §21.2 Kullanıcı Kararı Bekleyen Özet

| Karar | Seçenekler | Etki |
|-------|------------|------|
| models/issues/scripts index | Oluştur / referans kaldır | CLAUDE.md §17 boot iddiası |
| subdomains README + download index | Oluştur / keys.md satırını sil | §8 keyword yönlendirme |
| research/personas/registry vb. dizinler | personas → **KURULDU** (`.ai/.personas/`, 2026-09-26); kalan research/registry vb. → Yeniden kur / §12'den sil | 30+ satır katalog temizliği |
| electronic kök 8 dosya | Yeniden üret / DOĞRULAMA GEREKLİ kalıcı | keys.md §7, brain §21 |

---

### §23 Doküman İskeleti (8-Bölüm Uyumu — Vault Refactor Engine 2026-09-23)

> **Not:** v28.0.0 → v28.1.0; eksik frontmatter alanları tamamlandı (category, status, updated). Satır-edit + ekleme (ADR-042), §1-§22 korundu.

### §23.1 İskelet Eşlemesi

| İskelet Bölümü | Karşılık Gelen § |
|----------------|------------------|
| Başlık | H1 + frontmatter (7 zorunlu alan — bu turda category/status/updated eklendi) |
| Amaç | §1 Amaç |
| Kapsam | §2 Quick Reference + §3 SSOT Core Dosyaları (14 Kök Boot + 1 Ek Doküman) |
| Mimari | §4-§4A (L0-L6 + UI Design) + §6-§10 (Panel/Servis/Agent/DB/Projeler/Donanım) + §11A AI Architecture + §15 Audio Org |
| Kurallar | §11B Skills (Guardrail #16) + §12 Vault Altyapısı + [[CLAUDE.md]] §16 |
| Workflow | §13-§14 (Deployment + Tiers) + §20 (stack özeti) |
| Doğrulama | §11 Test & Ekosistem + §16 Test Coverage + §19 Faz 1 Anlık Görüntü + §21 Kırık Referans Kataloğu + bu bölüm §23 |
| Referanslar | §5 ADR Kataloğu + §17 Cross References + §18 Metadata + §22 PDF |

---

### §23.2 Faz 3 Doğrulama (2026-09-23)

- [x] Frontmatter 7 alan TAMAMLANDI (category/status/updated eksikti); version 28.2.0
- [x] `total_files: 850` + `total_adr: 79` → **yeniden sayım Faz 6'ya ertelendi** (ADR aralığı CLAUDE'da 001-088 ile çelişiyor — SSOT conflict defterine işlendi)
- [x] §1-§22 korundu, silme yok; yeni bölüm §23 eklendi

---

### §23.3 İlgili Dosyalar

[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[keys.md]] · [[glossary.md]] · [[MEMORY.md]] · [[log.md]]

---

### §23.4 Faz 2-3 İskelet Yeniden Düzenleme (2026-09-23)

- [x] 7 İngilizce H2 iskeleti uygulandı: `## Purpose / ## Scope / ## Architecture / ## Rules / ## Workflow / ## Validation / ## References`
- [x] Eski numaralı H2 bölümleri `### §N` H3 başlığına dönüştürüldü; § numaraları harfiyen korundu (dış cross-ref'ler: §4A, §5, §8, §11B, §17)
- [x] İçerik taşındı, silinmedi; §23.1 eşlemesi güncel grubu gösterir
- [x] §4 bağımlılık satırında yasak yönler `✅` → `❌` düzeltildi (L0→L2/L3, L1→L3, L3→L0)
- [x] version 28.1.0 → 28.2.0; updated 2026-09-23; §18 Metadata versiyon satırı güncellendi

---

