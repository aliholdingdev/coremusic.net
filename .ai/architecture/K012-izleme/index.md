---
title: "K012 OBSERVABILITY «AYNA» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K012-izleme/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: observability
ssot: true
risk: medium
owner: devops
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K012 OBSERVABILITY «AYNA» — Katman Index

> **Authority:** Bu dosya K012 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md §A.1 K012 kartı` > `.ai/CLAUDE.md §5/§12` > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md §2`. **Durum:** `draft` — bant onayı Kapı 10'da 👤.
> Bu dosya staging'dir; vault'a yazılmadı.

## Künye

| Alan | Değer |
|---|---|
| K-ID | K012 |
| Kanonik Ad | OBSERVABILITY |
| Teatral Epitet | «AYNA» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2) |
| Dizin deseni | `.ai/architecture/K012-izleme/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / observability |
| Owner (`.ai/AGENTS.md` §4 registry) | devops |
| Risk | medium — audit trail + log PII yüzeyi; dışa açık tehdit yüzeyi yok |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-006 · ADR-022 · ADR-040 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K012 OBSERVABILITY «AYNA», sistemin kendini göstermesidir: Structured Logging · Metrics · Tracing ·
Correlation · Health Checks · Audit Trail · Alerting · Audio Diagnostics (anayasa §A.1 K012).
Hard guardrail: **"Yalnızca K8 servislerinden okuma yapar"** (anayasa §5 K12 — 35 bileşen; araçlar:
Prometheus, Grafana, Sentry, Matomo, Audit). Repo gerçeği (ls 2026-10-08): log ALTYAPISI var
(`shared/src/Log/LoggerFactory.php` · `shared/src/PageRouter/StructuredLogger.php` · kökte
`coremusic_php_*.log` dosyaları · `coremusic_logs` DB §18 #7); ancak Prometheus/Grafana/Sentry/Matomo
altyapısı diskte GÖZLENMEDİ → merkezi izleme araç seti PLANNED (H1).
Kapsam dışı: iş mantığı/veri sahipliği (K008/K005) · dağıtım/dağıtım otomasyonu (K013) · güvenlik
kararları (K006) — bu katman yalnız **sinyal toplama, kayıt ve denetim** ile yükümlüdür.
Hard guardrail'ler ikisidir: (1) yalnız K8'den okuma, (2) append-only audit — ikisi de §5'te kilitlidir.

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K012 | OBSERVABILITY | «AYNA» | observability | PHP 8.4 (log producer) · izleme araçları hedef stack: Prometheus/Grafana/Sentry/Matomo (PLANNED — K013 dağıtımı) | Structured Logging · Metrics · Tracing · Correlation · Health Checks · Audit Trail · Alerting · Audio Diagnostics — 35 bileşen (anayasa §5 K12) | **yalnız K8 servislerinden okuma** (hard guardrail) · log/metrik/event akışı · health-check sorguları | zaman serileri · log kayıtları · alert · audit trail · dashboard · audio telemetri (hedef) | K000-K011 (alt katmanlar) · K008 servisleri (tek okuma kaynağı) · K005 `coremusic_logs` (yazım havuzu) | K012 → K008'e geri çağrı (H20) · K012'nin iş verisine doğrudan erişimi (okuma yalnız K8 servis yüzeyinden) · K013'e runtime müdahale · H19 veri paylaşımı | toplanan veri: log/metric/trace + audit kaydı; `coremusic_logs` (§18 #7) · PII redaksiyonu K006 ile (log'da secret yasağı — §23 #3) | audit trail append-only · log bütünlüğü · redaksiyon (credential/PII) · erişim K006 RBAC | izleme aracı DOWN → göz körlüğü (blind spot) · log yazımı tamponsuz → kayıp riski ⚠️ · alert gecikmesi; fail-over (EK A) | **bu katmanın kendisi** — sinyal üretimi ve toplanması; araç seti PLANNED | log şema testi PLANNED ⚠️ · `shared/tests/` (Unit/Security) dolaylı · hedef ≥80% (§17) | repo: `shared/src/Log/LoggerFactory.php` · `shared/src/PageRouter/StructuredLogger.php` · `coremusic_php_{errors,info,kernel_debug,security,warnings}.log` (kök ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K12/§18 #7/§23` + `00-kspace-anayasa.md §A.1 K012` · ADR: ADR-006/022/040 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ ✓. Hedef ≠ kanıt (H10): "35 bileşen · Prometheus/Grafana/Sentry/
Matomo" anayasa hedefidir; "LoggerFactory + 5 kök log dosyası + logs DB" repo kanıtıdır — birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K012 |
| 2 | KANONİK_AD | OBSERVABILITY |
| 3 | TEATRAL_EPİTET | «AYNA» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | observability |
| 5 | SUBDOMAIN | structured-logging · metrics · tracing · correlation · health-checks · audit-trail · alerting · audio-diagnostics |
| 6 | BOUNDED_CONTEXT | Sistem Görünürlüğü — iş kuralı üretmez; K008'den sinyal okur, K006 auditini belgeler, K013'e sinyal gönderir |
| 7 | RUNTIME | PHP 8.4 (producer/logger) · hedef araçlar: Prometheus (metrik) · Grafana (dashboard) · Sentry (hata) · Matomo (analitik) — PLANNED · taşınma/dağıtım K013 |
| 8 | SORUMLULUK | Yapısal loglama (LoggerFactory/StructuredLogger) · Metrics · Tracing · Correlation (K009'dan gelen correlation ID) · Health Checks · Audit Trail (append-only) · Alerting · Audio Diagnostics (ses zinciri telemetrisi — K003/K016 sınırı) |
| 9 | GIRDI | **yalnız K8 servislerinden okuma** · K009 correlation/access log · K007 red/olay satırları · K010/K011 UI olayları · health-check sorguları |
| 10 | CIKTI | log kayıtları (yapısal) · zaman serisi/metrik · alert · audit trail (`coremusic_logs`) · dashboard · rapor |
| 11 | IZINLI_BAGIMLILIK | K000-K011 (alt katmanlar) · **K008 servisleri (tek okuma kaynağı)** · K005 `coremusic_logs` (yazım) · K006 (audit/redaksiyon kuralları) · port/adapter |
| 12 | YASAK_BAGIMLILIK | K012 → K008'e geri çağrı (H20) · K8 dışı kaynaktan okuma (hard guardrail) · runtime müdahale (dağıtım K013) · K005 dışı DB'ye yazım (iş DB'leri K008'de) · H19 |
| 13 | DATA_BOUNDARY | yalnız gözlem verisi: log/metrik/trace/audit; PII redaksiyonu zorunlu; `coremusic_logs` (§18 #7) bu katmanın havuzudur; iş verisi kaynağı DEĞİL |
| 14 | SECURITY_BOUNDARY | audit append-only (değiştirilemez geçmiş) · log'da secret/credential yasağı (§23 #3) · log erişimi K006 RBAC · izleme araç admin yüzeyi (PLANNED) |
| 15 | FAILURE_MODE | izleme aracı DOWN → göz körlüğü (blind spot) — bildirilebilirlik kesilir · log yazım hattı tamponsuz → kayıp riski ⚠️ · alert yorgunluğu/yan alarm · fail-over (EK A) |
| 16 | OBSERVABILITY | bu katmanın kendisi sinyal üretir; kendi sağlığı (health-check) K013 kapısında |
| 17 | TEST | log şema/geçerlilik testi PLANNED ⚠️ · `shared/tests/` dolaylı kapsam · alert kural testi PLANNED · hedef ≥80% (§17) |
| 18 | KANIT | repo: `shared/src/Log/LoggerFactory.php` · `shared/src/PageRouter/StructuredLogger.php` · kök `coremusic_php_errors.log · coremusic_php_info.log · coremusic_php_kernel_debug.log · coremusic_php_security.log · coremusic_php_warnings.log` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K12 · §18 #7 (coremusic_logs) · §23 #3` + `00-kspace-anayasa.md §A.1 K012` · ADR: ADR-006/022/040 (accepted/ ls) · ADR-096 · web: ⚠️ VERIFICATION REQUIRED |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «AYNA» — yansıtan, objektif yüzey metaforu (sistem kendini K012'de görür); EK A §A.1 anahtar satırı: `K012 · OBSERVABILITY · «AYNA» · SOFTWARE` |

**R4.4 kart kapıları:** (a) 20 alan dolu ✓ · (b) İZİNLİ ∩ YASAK = ∅ ✓ (izinli okuma: yalnız K8; yasak: K8 dışı okuma + geri çağrı) ·
(c) KANIT 3'lü ✓ (web ayağı ⚠️) · (d) veri sınırı: gözlem verisi K012'de; iş verisi K008'de ✓.

### §3.1 Özet Satır ↔ EK C Tutarlılık Kontrolü (R4.4a)

| Özet alanı (§2) | EK C karşılığı (§3) | Tutarlı? |
|---|---|---|
| K-ID / KANONİK_AD / TEATRAL_EPİTET | alan 1/2/3 | ✓ |
| DOMAIN | alan 4 | ✓ |
| RUNTIME | alan 7 | ✓ |
| SORUMLULUK | alan 8 | ✓ |
| GİRDİ / ÇIKTI | alan 9/10 | ✓ |
| İZİNLİ / YASAK | alan 11/12 | ✓ (kümeler disjoint) |
| DATA / SECURITY BOUNDARY | alan 13/14 | ✓ |
| FAILURE_MODE | alan 15 | ✓ |
| OBSERVABILITY | alan 16 | ✓ |
| TEST | alan 17 | ✓ |
| KANIT | alan 18/19/20 | ✓ (3'lü + tarih + epitet notu) |

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K012 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Structured Logging · Metrics · Tracing ·
Correlation · Health Checks · Audit Trail · Alerting · Audio Diagnostics".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Structured Logging | yapısal log üretimi (JSON/satır formatı) | IMPLEMENTED (altyapı) | `shared/src/Log/LoggerFactory.php` · `shared/src/PageRouter/StructuredLogger.php` (ls 2026-10-08) |
| Metrics | metrik toplama/zaman serisi | PLANNED — Prometheus/Grafana diskte GÖZLENMEDİ (maxdepth 3 tarama 0 isabet) → ⚠️ | `.ai/CLAUDE.md §5 K12` · repo: araç YOK |
| Tracing | uçtan uca iz (span/zaman) | PLANNED ⚠️ | §5 K12 · ⚠️ |
| Correlation | correlation ID ile istek boyu korelasyon (gateway üretimi — K009 §6A.1) | PARTIAL (üretim tanımı K009'da; K012 toplama PLANNED) | `.ai/CLAUDE.md §6A.1` · K009 §6.3 |
| Health Checks | servis sağlık sorguları | PLANNED ⚠️ (uç grep edilmedi) | §5 K12 · K008 §6.3 |
| Audit Trail | append-only denetim kaydı (§18 #7 `coremusic_logs`: Application logs, audit trail, analytics, performance metrics) | PARTIAL — DB sahipliği vault'ta; uygulama grep ⚠️ | `.ai/CLAUDE.md §18 #7` · ADR-022 (accepted/ ls) · ADR-040 |
| Alerting | eşik/olay uyarıları | PLANNED ⚠️ (Prometheus Alertmanager/ekipman yok) | §5 K12 · ⚠️ |
| Audio Diagnostics | ses zinciri telemetrisi (spectrum/latency/DSP durumu — K003/K016 sınırı) | PLANNED ⚠️ | anayasa §A.1 · `.ai/CLAUDE.md §19` (audio standartları) · ⚠️ |

### §4.2 Anayasa §5 K-Matrix Satırı (K12) — 35 bileşenin açılımı

Kaynak: `.ai/CLAUDE.md §5` — **K12 İzleme & Log | Prometheus, Grafana, Sentry, Matomo, Audit | 35 |
Yalnızca K8 servislerinden okuma yapar.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Prometheus | metrik sunucusu/serileri | PLANNED ⚠️ (repo: konfigürasyon yok) | §5 K12 · maxdepth 3 tarama 0 isabet (ls 2026-10-08) |
| Grafana | dashboard/visualizasyon | PLANNED ⚠️ | §5 K12 · ⚠️ |
| Sentry | hata izleme (ön yüz + backend) | PLANNED ⚠️ | §5 K12 · ⚠️ |
| Matomo | kullanıcı analitiği | PLANNED ⚠️ | §5 K12 · ⚠️ |
| Audit | denetim kaydı (K006 ortak alanı) | PARTIAL (DB + güvenlik logları) | §18 #7 · kök `coremusic_php_security.log` (ls) · ADR-022 |
| "35 bileşen" | K12 envanter toplamı | HEDEF (H10) | `.ai/CLAUDE.md §5 K12` |
| Hard guardrail | "Yalnızca K8 servislerinden okuma yapar" | BAĞLAYICI | `.ai/CLAUDE.md §5 K12` |

### §4.3 Log Altyapısı Envanteri (repo — ls 2026-10-08)

| # | Varlık | Rol | Durum |
|---|---|---|---|
| 1 | `shared/src/Log/LoggerFactory.php` | logcu fabrikası (ortak üretim) | VAR |
| 2 | `shared/src/PageRouter/StructuredLogger.php` | yapısal logger (router katmanı) | VAR |
| 3 | `coremusic_php_errors.log` (kök) | hata logu dosyası | VAR |
| 4 | `coremusic_php_info.log` | bilgi logu | VAR |
| 5 | `coremusic_php_warnings.log` | uyarı logu | VAR |
| 6 | `coremusic_php_security.log` | güvenlik logu (K006/K012 ortak) | VAR |
| 7 | `coremusic_php_kernel_debug.log` | kernel/debug logu | VAR |
| 8 | `coremusic_logs` veritabanı (§18 #7) | uygulama logu + audit + analitik + performans metrikleri (18 BCNF parçası) | ŞEMA VAR (`.ai/.sql/mysql/coremusic_logs.sql` — §18 evi) · canlı yazım ⚠️ |
| 9 | Prometheus/Grafana/Sentry/Matomo konfigürasyonu | merkezi araç seti | YOK → PLANNED |
| 10 | Log rotasyon/retention politikası | disk/uyumluluk yönetimi | ⚠️ (politika dosyası yok) |

### §4.3.1 Log Dosya Seviyesi Haritası (repo dosya adları — ls 2026-10-08)

| Dosya | Seviye/alan | Beklenen içerik | Durum |
|---|---|---|---|
| `coremusic_php_errors.log` | ERROR | PHP/uygulama hataları | VAR |
| `coremusic_php_warnings.log` | WARNING | uyarılar | VAR |
| `coremusic_php_info.log` | INFO | bilgi/akış kayıtları | VAR |
| `coremusic_php_security.log` | SECURITY | güvenlik olayları (K006 ortak) | VAR |
| `coremusic_php_kernel_debug.log` | DEBUG | kernel/çekirdek hata ayıklama | VAR |
| (hedef) tek yapısal akış | JSON log (yapısal) | LoggerFactory ile üretim | PARTIAL — format ⚠️ |

### §4.4 Kapsam Sınırı — Bu Katmanın Okuma/Yazma Matrisi

| İşlem | Hedef | İzin | Kanıt |
|---|---|---|---|
| Okuma (log/metrik/trace) | **K008 servisleri** | İZİNli (tek kaynak — hard guardrail) | `.ai/CLAUDE.md §5 K12` |
| Okuma | K8 dışı (örn. K010 panel içi) | YASAK | §5 K12 |
| Yazım (audit/log havuzu) | `coremusic_logs` (K005) | İZİNli | §18 #7 |
| Yazım | iş veritabanları (K008 sahipliği) | YASAK | §18 · H19 |
| Geri çağrı | K008 servisleri | YASAK (H20) | `rules.md R6.1` |
| Alert → K013 | dağıtım/dağıtım tepkisi | İZİNli (yukarı sinyal) | R6.2 |

### §4.5 K012 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K012 etkisi |
|---|---|---|
| ADR-006-performance-targets | performans hedefleri | metrik eşikleri/hedefleri (içerik okunmadı → ⚠️) |
| ADR-022-database-hardened-security | DB güvenlik sertleştirme | audit/log DB güvenliği |
| ADR-040-database-authority | 18 BCNF otoritesi | `coremusic_logs` sahipliği (§18 #7) |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi | format/bağımlılık kaynağı |

### §4.6 K012 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K012'nin verdiği | Beklenen | Kanıt |
|---|---|---|---|---|
| K008 SERVICES | log/metric okuması (tek kaynak) | toplama + saklama | servis log üretimi (LoggerFactory) | §5 K12 · `shared/src/Log/` |
| K009 API | correlation/access log | korele edilmiş kayıt | correlation ID üretimi | §6A.1 |
| K007 MIDDLEWARE | red/olay satırları | güvenlik olayı akışı | — | K007 §6.3 |
| K010/K011 | UI olay/hataları | ön yüz sinyali (PLANNED) | — | K011 §6.3 |
| K006 SECURITY | audit trail | append-only denetim | redaksiyon kuralları | §18 #7 · §23 #3 |
| K013 CI/CD | health/sinyal | alarm/dashboard verisi | dağıtım tepkisi (PLANNED) | §5 K13 |

### §4.7 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K12 hedefi | 35 bileşen · Prometheus/Grafana/Sentry/Matomo/Audit (HEDEF) |
| Repo kanıtı (ls 2026-10-08) | LoggerFactory + StructuredLogger + 5 kök log dosyası + `coremusic_logs` şeması |
| Araç seti | 0/4 araç (Prometheus/Grafana/Sentry/Matomo) → PLANNED |
| Audit | PARTIAL (DB + security log) · uygulama katmanı ⚠️ |
| Test | log şema/alert testi yok → PLANNED |

### §4.8 `coremusic_logs` Alan Dağılımı (§18 #7 — dört amaç)

| # | Amaç (§18 #7 metni) | İçerik | Üreten kardeş | Durum |
|---|---|---|---|---|
| 1 | Application logs | uygulama logları | K007/K008/K009/K010 | PARTIAL (dosyalar var; DB yazımı ⚠️) |
| 2 | Audit trail | denetim izi (append-only) | K006 + K008 olayları | PARTIAL (ADR-022 · ADR-040) |
| 3 | Analytics | kullanım/analitik | K010/K011 olayları (+Matomo hedef) | PLANNED ⚠️ |
| 4 | Performance metrics | performans metrikleri | K008/K009 latency | PLANNED ⚠️ (Prometheus hedef) |

### §4.9 K012 ↔ K013 Sınırı (log okuması vs dağıtım)

| Yön | İş | İzin | Kanıt |
|---|---|---|---|
| K012 → K013 alert/health sinyali | yukarı olay yayını | İZİNli | `rules.md R6.2` |
| K013 → K012 build/test log okuması | doküman/olay düzeyi | İZİNli (okuma, geri çağrı değil) | R6.4 · `.github/workflows/ci.yml` (ls) |
| K013 → runtime müdahale | deploy/rollback | K013'ün kendi işi (K012 değil) | §5 K13 |
| K012 → deploy tetikleme | senkron | YASAK (H20 — alert olaydır, komut değil) | `rules.md R6.1` |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000-K011 (OS → UX) | aşağı | anayasa §A.1 K012 "izinli=K000-K011" | `00-kspace-anayasa.md §A.1 K012` |
| **K008 SERVICES** | aşağı (tek okuma kaynağı) | "Yalnızca K8 servislerinden okuma yapar" — hard guardrail | `.ai/CLAUDE.md §5 K12` |
| K005 `coremusic_logs` | aşağı (yazım havuzu) | §18 #7 — uygulama logu/audit/analitik/metrik DB'si | `.ai/CLAUDE.md §18 #7` · ADR-040 |
| K006 SECURITY | aşağı | redaksiyon/audit kuralları, log erişim RBAC | `.ai/CLAUDE.md §23 #3` · ADR-022 |
| K013 CI/CD | yukarı sinyal | health/alert → dağıtım tepkisi (olay yukarı serbest) | `rules.md R6.2` |
| port/adapter | yan | EK A istisnası (R6.3) | anayasa §A.1 |
| Prometheus/Sentry/… | dış araç | hedef araç seti (PLANNED) | `.ai/CLAUDE.md §5 K12` |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K8 dışı kaynaktan okuma | hard guardrail: "Yalnızca K8 servislerinden okuma yapar" | `.ai/CLAUDE.md §5 K12` |
| K012 → K008 geri çağrı (H20) | klasik yön | `rules.md R6.1` · ADR-096 §2 |
| İş DB'lerine yazım (K008 sahipliği) | veri sınırı (H19) | `.ai/CLAUDE.md §18` · `rules.md R6.1` |
| Runtime müdahale/dağıtım | dağıtım K013'ün (yalnız build/deploy otomasyonu) | `.ai/CLAUDE.md §5 K13` |
| Log'a secret/credential yazımı | §23 #3 kritik uyarı (veri sızıntısı) | `.ai/CLAUDE.md §23 #3` |
| Audit kaydını silmek/değiştirmek | append-only denetim | §18 #7 · ADR-022 (accepted/ ls) |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali | `rules.md R6.1` |

### §5.3 Boundary Matrisi

| Boundary | K012 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | gözlem verisi (log/metric/trace/audit) + PII redaksiyonu; iş verisi DEĞİL | K008 (iş) · K005 (DB) |
| SECURITY_BOUNDARY | append-only audit · log secret yasağı · log erişimi RBAC | K006 |
| FAILURE_MODE | izleme DOWN → blind spot · log yazım kaybı riski ⚠️ · yan alarm | K013 (dağıtım tepkisi) · K008 |
| RUNTIME boundary | PHP producer + (hedef) araç süreçleri; runtime K000/K013 | K000 · K013 |
| CONTRACT boundary | log şeması/olay şeması (K012 sahibi — şema dosyası PLANNED ⚠️) | K012 |
| Olay yukarı serbest | alert/olay → K013/yönetim; geri senkron çağırır yasak | K013 |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay Akışı (R6.2 — sinyal yukarı, geri çağrı yok)

```text
K013 CI/CD ──(alert/health sinyali, yukarı serbest)──→ K012 OBSERVABILITY «AYNA»
                                                          ↑        ↑
                                     [okuma: YALNIZ K8 servisleri] │
                                                          │        │
K008 SERVICES ──(log · metric · trace · event)────────────┘        │
K009 (correlation/access) ──(log)──────────────────────────────────┘
K007/K010/K011 (olay/red/UI) ──(olay)──────────────────────────────┘
        │
        └── yazım: coremusic_logs (K005) + log dosyaları (repo kökü)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K008 → K012 log/metric | aşağı→yukarı sinyal | tek okuma kaynağı | §5 K12 |
| K012 → K008 geri | — | YASAK (H20) | `rules.md R6.1` |
| K012 → K013 alert | yukarı | olay yayını serbest | `rules.md R6.2` |
| K012 → K005 logs DB | aşağı (yazım) | §18 #7 havuz | §18 |
| K012 → K006 audit | aşağı/devir | redaksiyon & denetim | §23 #3 · ADR-022 |

### §5.5 Kardeş İlişki Kuralı (R6.4)

Kardeş KNNN'lerle ilişki yalnız `refers-to`; `depends-on` yalnız alt katman yönünde. K012'nin
K008'e bağımlılığı "okuma" bağımlılığıdır ve R6.1'de izinli alt-yön içindedir; K008'in K012'ye
bağımlılığı olamaz (döngü → `dep-check` exit 1, R6.6).

### §5.6 Sinyal Kaynakları Kardeş Matrisi (hangi kardeş ne gönderir)

| Kaynak kardeş | Sinyal türü | Durum | Kanıt |
|---|---|---|---|
| K007 MIDDLEWARE | red/olay/rate-limit satırları | PARTIAL (logger altyapısı) | K007 §6.3 · `shared/src/Log/` |
| K008 SERVICES | servis log/metric/event | PARTIAL (producer var, merkez PLANNED) | K008 §6.3 |
| K009 API | access log + correlation ID | PARTIAL (tanım var, toplama PLANNED) | `.ai/CLAUDE.md §6A.1` |
| K010 APPLICATION | panel erişim/aksiyon | PARTIAL (PHP loglar) | kök `coremusic_php_*.log` (ls) |
| K011 UX | Web Vitals/UI hata | PLANNED ⚠️ | K011 §6.3 |
| K006 SECURITY | güvenlik/audit olayı | PARTIAL (`coremusic_php_security.log`) | repo ls · §18 #7 |
| K003/K016 (ses zinciri) | Audio Diagnostics telemetrisi | PLANNED ⚠️ | anayasa §A.1 K012 · §19 |
| K013 CI/CD | build/test/deploy sinyali | PARTIAL (`ci.yml` logları GitHub'da) | `.github/workflows/ci.yml` (ls) |

### §5.7 Alert/Eşik Taslağı (PLANNED — hedeflerin tamamı `⚠️ VERIFICATION REQUIRED`, R9/R14)

| # | Sinyal | Eşik kaynağı | Durum |
|---|---|---|---|
| A1 | Rate-limit eşik ihlali (60/60s) | `.ai/CLAUDE.md §6 #3` (gerçek eşik) | eşik VAR · alert PLANNED ⚠️ |
| A2 | Session idle dolumu (3600s) | `.ai/CLAUDE.md §6 #5` (gerçek eşik) | eşik VAR · alert PLANNED ⚠️ |
| A3 | Servis DOWN (health-check) | §5 K12 health-check | PLANNED ⚠️ |
| A4 | 5xx oranı | ADR-006 performans hedefleri (içerik okunmadı) | ⚠️ |
| A5 | Latency percentile | ADR-006 (okunmadı) | ⚠️ |
| A6 | Log hacmi/disk kullanımı | politika yok (§4.3 #10) | ⚠️ |
| A7 | Audit boşluğu (append-only ihlali) | ADR-022 | PLANNED ⚠️ |
| A8 | Audio Diagnostics sapması | §19 standartları (ör. <10ms ASIO gecikme hedefi) | PLANNED ⚠️ |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı | Not |
|---|---|---|---|
| Koleksiyon | K8 servis log/metric + correlation | ham sinyal | hard guardrail §5 K12 |
| Yapısal loglama | LoggerFactory girdisi | yapısal kayıt (dosya/DB) | §4.3 #1/#2 |
| Audit yazımı | güvenlik olayı | append-only kayıt (`coremusic_logs`) | §18 #7 |
| Metrics (hedef) | sayaç/istogram | zaman serisi (Prometheus) | PLANNED |
| Alert (hedef) | eşik ihlali | bildirim | PLANNED |
| Dashboard (hedef) | zaman serisi | Grafana paneli | PLANNED |
| Audio Diagnostics (hedef) | ses telemetrisi | spectrum/latency raporu | PLANNED ⚠️ |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Producer | PHP 8.4 (LoggerFactory/StructuredLogger) | repo ls · §12 |
| Log dosyaları | kök `coremusic_php_{errors,info,warnings,security,kernel_debug}.log` | repo ls 2026-10-08 |
| Log DB | `coremusic_logs` (18 BCNF #7) | `.ai/CLAUDE.md §18` · `.ai/.sql/mysql/coremusic_logs.sql` |
| Hedef araçlar | Prometheus · Grafana · Sentry · Matomo | §5 K12 · repo: YOK → PLANNED |
| Dağıtım | Docker/K8s hedefi K013'te | §5 K13 · repo: Dockerfile yok (ls) |
| Redis | metrik cache hedefi — APCu gerçek, Redis PLANNED | §12 (cache satırı) |

### §6.3 Observability (meta — katmanın kendisi)

| Sinyal | Kaynak | Durum |
|---|---|---|
| Log üretimi | LoggerFactory + 5 dosya | IMPLEMENTED (ls) |
| Audit trail | `coremusic_logs` + security log | PARTIAL |
| Metrik/trace/alert | araç seti | PLANNED ⚠️ |
| Health-check | servis uçları | PLANNED ⚠️ |
| PII redaksiyonu | kural (log'da secret yok) | BAĞLAYICI (§23 #3) · uygulama grep ⚠️ |

### §6.4 Test

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| Log şema/geçerlilik | PHPUnit (hedef) | PLANNED ⚠️ (`shared/tests/` içinde log testi gözlenmedi) | ≥80% (§17) |
| Audit bütünlüğü (append-only) | PHPUnit (hedef) | PLANNED ⚠️ | ≥80% |
| Alert kural testi | (hedef) | PLANNED — araç yok | ⚠️ |
| Metrik toplama | — | PLANNED (Prometheus yok) | ⚠️ |
| Doğrudan kapsam | `shared/tests/{Unit,Security}` | dolaylı (ls) | §17 |

### §6.4.1 Sınıflandırma & Öncelik Matrisi (log→alert zinciri)

| Sınıf | Kaynak dosya/seviye | Alert önceliği | Saklama hedefi |
|---|---|---|---|
| SECURITY | `coremusic_php_security.log` | en yüksek (K006/K012 ortak) | append-only · retention ⚠️ |
| ERROR | `coremusic_php_errors.log` | yüksek | retention ⚠️ |
| WARNING | `coremusic_php_warnings.log` | orta | retention ⚠️ |
| INFO | `coremusic_php_info.log` | düşük (yalnız korelasyon) | retention ⚠️ |
| DEBUG | `coremusic_php_kernel_debug.log` | yok (yalnız teşhis) | kısa retention ⚠️ |
| METRIC/ALERT | (hedef Prometheus/Alertmanager) | eşik tabanlı (§5.7) | PLANNED ⚠️ |

### §6.5 Failure Mode Senaryoları (failure=fail-over)

| # | Senaryo | K012 davranışı | Kanıt |
|---|---|---|---|
| 1 | İzleme aracı DOWN (hedef stack) | göz körlüğü: alert'ler üretilmez | §5 K12 · tasarım ⚠️ |
| 2 | Log yazım hattı dolu/kesintili | kayıt kaybı riski (tamponsuz politika tanımsız) | ⚠️ VERIFICATION REQUIRED (§4.3 #10) |
| 3 | Log DB (`coremusic_logs`) erişilemez | audit boşluğu → denetim riski | §18 #7 · ADR-040 |
| 4 | Secret log'a sızarsa | §23 #3 kritik uyarı — sızıntı olayı | `.ai/CLAUDE.md §23 #3` |
| 5 | Yan alarm / alert yorgunluğu | gerçek olay gözden kaçar | tasarım riski ⚠️ |
| 6 | K8 dışı kaynak okuması | hard guardrail ihlali → revert + CRITICAL | §5 K12 · anayasa §5.1 |
| 7 | Correlation ID kaybı | iz kopması (tracing kör noktası) | §6A.1 · K009 §6.3 |
| 8 | Log rotasyon yok | disk dolması → yazım hatası | ⚠️ (politika yok §4.3 #10) |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K012 durumu |
|---|---|
| KAPI 1 vault oku | Tam (anayasa §A.1 + §5/§18/§23 + rules + ADR-096 okundu) |
| KAPI 9 hallucination | ⚠️ listesi §7.1 |
| KAPI 10 onay | BEKLİYOR — `status: draft` (R10) |
| §23 #3 (secret log) | bağlayıcı — log redaksiyonu bu katmanın |
| H19/H20 | §5.2 kilitli |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | 8 EK A kalemi + §5 K12 kalemleri | her kalem Durum+Kanıt | ✓ (§4.1/§4.2) |
| K4 | Okuma kilidi | "yalnız K8 okuma" hard guardrail belgeli | ✓ (§4.4 · §5.2) |
| K5 | Araç seti gerçekliği | 0/4 araç PLANNED olarak işaretli | ✓ (§4.7) |
| K6 | Web research (R9 3'lü) | ≥%80 kaynaklı | ✗ → ⚠️ (§7.1) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K012 - OBSERVABILITY «AYNA»` (+ §A.0) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5 K12` · `§18 #7` · `§23 #3` · `§12` · `§17` | dosya yolu (vault read) | K-matrix + audit/log kuralları |
| 3 | `shared/src/Log/LoggerFactory.php` · `shared/src/PageRouter/StructuredLogger.php` · kök `coremusic_php_*.log` (5 dosya) · `.ai/.sql/mysql/coremusic_logs.sql` varlığı | repo ls (2026-10-08) | IMPLEMENTED/PARTIAL |
| 4 | `.ai/.decisions/accepted/` ls: ADR-006/022/040/096 | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2` | dosya yolu | format + yön |
| 6 | F1 EK B (37 URL) | URL (vault arşivi) | research üssü |
| 7 | Aşağıdaki boşluklar | ⚠️ VERIFICATION REQUIRED | R14 research kapısı |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | Prometheus/Grafana/Sentry/Matomo yok (0/4) | §4.2 · EK C RUNTIME | araç kurulum planı (K013) + research |
| G2 | Log şema dosyası/şema testi yok | §4.3 · EK C TEST | şema üretimi + test |
| G3 | Log rotasyon/retention politikası yok | §6.5 #8 | politika yazımı (👤) |
| G4 | Correlation toplama uygulaması | §4.1 Correlation | K009/K012 kod grep'i |
| G5 | Health-check uçları grep edilmedi | §4.1 | K008 kod taraması |
| G6 | Web kanıtı (URL+tarih) | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G7 | `coremusic_logs` canlı yazım kanıtı | §4.3 #8 | canlı DB/uygulama grep'i |
| G8 | PII redaksiyonu uygulaması | EK C SECURITY_BOUNDARY | log üretim kodu grep'i |

**Kural:** Boşluklar dosyayı geçersiz kılmaz; `ACTIVE` için R4.4c/R16.2 kapanışı gerekir.

### §7.2 Kanıt Haritası (EK C alan → birincil kanıt)

| EK C alan | Birincil kanıt | İkincil kanıt | Üçüncül |
|---|---|---|---|
| SORUMLULUK | anayasa §A.1 K012 | `.ai/CLAUDE.md §5 K12` | repo §4.3 |
| RUNTIME | §5 K12 araç listesi | kök log dosyaları (ls) | ⚠️ (araçlar yok) |
| GIRDI/CIKTI | §5 K12 okuma kilidi | LoggerFactory (ls) | ⚠️ |
| IZINLI/YASAK | anayasa §A.1 + `rules.md R6` | ADR-096 §2 | ⚠️ |
| DATA_BOUNDARY | §18 #7 + §23 #3 | `coremusic_logs.sql` varlığı | ⚠️ |
| SECURITY_BOUNDARY | ADR-022 · §18 #7 append-only | `coremusic_php_security.log` (ls) | ⚠️ |
| FAILURE_MODE | §6.5 senaryoları | — | ⚠️ (politikalar) |
| OBSERVABILITY | (bu katman) | — | — |
| TEST | §6.4 (PLANNED işaretli) | `shared/tests/` (ls) | ⚠️ |

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» (tek okuma kaynağı) · K009 API «HÜCRE» ·
K010 APPLICATION «DÜĞÜM» · K011 UX «OMURGA». Üst/yasak yön (H20): K013 CI/CD «PUSULA» (olay yukarı) ·
K014 NETWORK «ÇARK» · K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» ·
K018 THERMAL «MEZİT» · K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL». Kardeşler arası ilişki
yalnız `refers-to` (R6.4).

**Sınır notu (K013 ile):** K012 alert/health sinyalini K013'e **yukarı** yayınlar (R6.2); K013'ün
dağıtım/rütbesi K012'yi geri tetiklemez (senkron geri çağrı yasak). K013'ün K012'yi okuması
yalnız build/test logları üzerinden ve doküman düzeyindedir.

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):** b1-K007-middleware.md (K007) ·
b1-K008-servisler.md (K008) · b1-K009-api.md (K009) · b1-K010-uygulama.md (K010) ·
b1-K011-ux.md (K011) · b1-K012-izleme.md (K012 — bu dosya) · b1-K013-cicd.md (K013).

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.

**Not (Kapı 10):** Bu dosya `draft`'tır; band-1 seti (K007-K013) birlikte 👤 onayına gider. §7.1
boşlukları (özellikle G1 araç seti) research kapısında kapatılmalıdır (R4.4c · R16.2 · R11 KAPI 9).
Vault yazımı ve `git commit` bu üretim kapsamında DEĞİLDİR (H5/H6).

**Kaynak bağımlılığı:** `depends-on: .ai/architecture/00-kspace-anayasa.md` — anayasa güncellenirse
bu kart §A.1 K012 satırıyla birlikte yeniden gözden geçirilir (R8.1 zinciri).

**Çapraz okuma:** K008 (tek okuma kaynağı) · K006 (audit/redaksiyon) · K013 (dağıtım tepkisi) kartları
bu dosyanın sınır tanımlarını tamamlar; birlikte okunur.

**Öncelik defteri:** §5.7 alert eşiklerinin tamamı `⚠️` — R14 research kapısında ADR-006 içeriği
okunarak doldurulur; doldurulmadan katman `ACTIVE` olamaz (R4.4c).

**Not (staging):** Bu dosya yalnız `.superpowers/sdd/…/staging/` altındadır; `.ai/architecture/`
altındaki nihai `K012-izleme/index.md` yolu Kapı 10 onayı + R5 id-check sonrası üretilir.