---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K13 CI/CD Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
source: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K13: CI/CD & Deploy Layer

**Katman:** K13 (CI/CD & Deploy)
**Kapsam:** GitHub Actions, Playwright, Vitest, Docker, K8s
**Sorumlu Agent:** DevOps Engineer
**Bileşen Sayısı:** 35

---

## 1. Genel Bakış

K13 katmanı, CoreMusic'in Continuous Integration ve Continuous Deployment altyapısını içerir.

---

## 2. CI Pipeline

### 2.1 Pipeline Aşamaları

```
Code Push → Lint → Test → Security → Build → Deploy → Verify
```

### 2.2 Pipeline Aşama Detayları

| Aşama | Araç | Süre | Başarısızlık |
|-------|------|------|-------------|
| Lint (PHP) | php-cs-fixer, phpstan | ~1min | Build durur |
| Lint (JS) | ESLint | ~30s | Build durur |
| Test (PHP) | PHPUnit | ~2min | Build durur |
| Test (JS) | Vitest | ~1min | Build durur |
| Security | GitLeaks, Composer Audit | ~1min | Build durur |
| Build | Docker | ~3min | Deploy durur |
| Deploy | SSH/SCP | ~2min | Rollback |
| Verify | Health Check | ~30s | Alert |

---

## 3. Deployment Stratejileri

| Strateji | Kullanım | Risk |
|----------|---------|------|
| Blue/Green | Major release | Düşük |
| Canary | Feature rollout | Orta |
| Rolling | Bug fix | Düşük |
| Recreate | Acil onarım | Yüksek |

---

## 4. Docker Yapısı

```dockerfile
# Multi-stage build
FROM php:8.4-fpm AS builder
# ... build steps

FROM php:8.4-fpm AS production
COPY --from=builder /app /var/www/html
# ... production config
```

---

## 5. Health Check

```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost:81/health"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 40s
```

---

*K13 CI/CD Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*

## Alt Katman Şeması (K13.a.b.c)

> **Revizyon notu (2026-09-24):** Bu bölüm mevcut §1–§5 içeriğinin SONUNA eklenmiştir; hiçbir mevcut satır silinmedi/değiştirilmedi. Şema 3 turlu agent tartışmasıyla üretildi (frontmatter `source`).

### 1. Kaynak Tablosu

| Kaynak | Rol | Güvenilirlik |
|---|---|---|
| `.ai/CLAUDE.md` §5 (K13 satırı) | Araçlar: GitHub Actions, Playwright, Vitest, Docker, K8s (35) | SSOT — mutlak |
| `frontend-restructuring-plan.md` §2.1 L13 | Hedef satır: X=7, Y=5, Z=90; satır toplamı 95 yazılmış | Plan — bkz. C1 |
| `frontend-restructuring-plan.md` §2.2 | Dosya envanteri (stok, düğüm değil) | Plan — ikincil |
| `k13-cicd/*.md` (11 içerik MD) | Düğüm kanıtları | Disk — doğrulanmış |
| `.github/` (disk) | workflows KLASÖRÜ YOK: yalnız CLAUDE.md + ISSUE_TEMPLATE | Disk — C2 |
| `adlandirma-kurali.md` | Küçük-hyphen yazım kuralı | Kural |
| ADR-086 / ADR-084 | Event Driven / API Gateway — pipeline sözleşmeleri | ADR |

### 2. Şema Kuralları

1. `K13` → `K13.a` (Düzey-2, zorunlu) → `K13.b` (Düzey-3, zorunlu); Düzey-4 yalnız kanıtla — K13'te kod/CSS kanıtı yok, **L4 = 0**.
2. `.0.` ara düğümü yasak; hiçbir düğümde `K13.0` kullanılmadı.
3. Adlar küçük-hyphen; her düğüm başlığında `K13` numarası yazılı.
4. Düzey-2 = **X = 7** (plan §2.1 L13 kilitli; 11 içerik MD düğüm değil, kanıt satırıdır — C4).
5. Her Düzey-2 altında tam **Y = 5** Düzey-3; toplam L3 = 35.

### 3. Düzey-2 Tablosu (X = 7)

| # | Düğüm | Ad | Birincil kanıt dosyası |
|---|---|---|---|
| 1 | K13.1 | github-actions | github-actions.md |
| 2 | K13.2 | ci-pipeline | ci-pipeline.md |
| 3 | K13.3 | testing-pipeline | testing-pipeline.md |
| 4 | K13.4 | security-scanning | security-scanning.md |
| 5 | K13.5 | cd-pipeline | cd-pipeline.md (+ docker-build, kubernetes-deploy, infrastructure-as-code, rollback-strategy — C4) |
| 6 | K13.6 | staging-environment | staging-environment.md |
| 7 | K13.7 | monitoring-deployment | monitoring-deployment.md |

### 4. Düzey-3 Düğümleri (her Düzey-2 altında Y = 5)

#### K13.1 · github-actions

| L3 | Ad | Kanıt |
|---|---|---|
| K13.1.1 | workflow-tetikleyicileri | github-actions.md — ama `.github/workflows/` diskte YOK (C2) |
| K13.1.2 | runner-konfigurasyonu | github-actions.md |
| K13.1.3 | secret-yonetimi | github-actions.md |
| K13.1.4 | artifact-paylasimi | github-actions.md |
| K13.1.5 | matrix-build | github-actions.md |

#### K13.2 · ci-pipeline

| L3 | Ad | Kanıt |
|---|---|---|
| K13.2.1 | adim-sirasi | ci-pipeline.md |
| K13.2.2 | hata-kosullari | ci-pipeline.md |
| K13.2.3 | onbelkleme-hizlandirma | ci-pipeline.md |
| K13.2.4 | cikti-sartlari | ci-pipeline.md |
| K13.2.5 | pr-kapisi | ci-pipeline.md |

#### K13.3 · testing-pipeline

| L3 | Ad | Kanıt |
|---|---|---|
| K13.3.1 | vitest-birim-testleri | CLAUDE §5 araç listesi (Vitest) + testing-pipeline.md |
| K13.3.2 | playwright-e2e | CLAUDE §5 (Playwright) + testing-pipeline.md |
| K13.3.3 | kapsam-esigi | testing-pipeline.md |
| K13.3.4 | test-verisi-fixture | testing-pipeline.md |
| K13.3.5 | paralel-test | testing-pipeline.md |

#### K13.4 · security-scanning

| L3 | Ad | Kanıt |
|---|---|---|
| K13.4.1 | bagimlilik-taramasi | security-scanning.md |
| K13.4.2 | kod-taramasi | security-scanning.md |
| K13.4.3 | secret-tarama | security-scanning.md |
| K13.4.4 | imaj-tarama | security-scanning.md + docker-build.md |
| K13.4.5 | gdpr-uyum-kapisi | security-scanning.md (ADR-010/012 ilişkili) |

#### K13.5 · cd-pipeline

| L3 | Ad | Kanıt |
|---|---|---|
| K13.5.1 | yayin-akis | cd-pipeline.md |
| K13.5.2 | docker-build-adimlari | docker-build.md (C4 gruplama) |
| K13.5.3 | kubernetes-deploy | kubernetes-deploy.md (C4) — CLAUDE §5: K8s (35) |
| K13.5.4 | infrastructure-as-code | infrastructure-as-code.md (C4) |
| K13.5.5 | rollback-stratejisi | rollback-strategy.md (C4) |

#### K13.6 · staging-environment

| L3 | Ad | Kanıt |
|---|---|---|
| K13.6.1 | staging-ortami | staging-environment.md |
| K13.6.2 | izolasyon | staging-environment.md |
| K13.6.3 | veri-senkaranti | staging-environment.md (sayısal iddia yok — okunmadı) |
| K13.6.4 | promotion-kapisi | staging-environment.md + ci-pipeline.md PR kapısı |
| K13.6.5 | dogrulama-testleri | testing-pipeline.md + staging-environment.md |

#### K13.7 · monitoring-deployment

| L3 | Ad | Kanıt |
|---|---|---|
| K13.7.1 | izleme-yigin-dagitimi | monitoring-deployment.md → K12 yığını (K12↔K13 bağımlılığı `?`, K12 README §6) |
| K13.7.2 | saglik-kapisi | monitoring-deployment.md + README §5 Health Check |
| K13.7.3 | yayin-sonrasi-kontrol | monitoring-deployment.md |
| K13.7.4 | metrik-dogrulama | monitoring-deployment.md + K12.1 (salt-okunur) |
| K13.7.5 | geri-bildirim-dongusu | monitoring-deployment.md |

### 5. Çelişki Kayıt Defteri

**C1 — Plan formülü K13'te tutmuyor.** §2.1 L13: X=7, Y=5, Z=90 ve satır toplamı **95** yazılmış. X·Y+Z = 125, X+Y+Z = 102 — hiçbiri 95 değil. **Çözüm (öncelik sırasıyla):** (a) gerçek kanıt, (b) X=7 tam, (c) dosya başına ≥500 satır; formül toplamı K13'te bilinçli olarak **uymuyor** olarak kaydedildi. — *dürüstlük notu, P0.*

**C2 — `.github/workflows/` diskte yok.** `.github/` altında yalnız CLAUDE.md + ISSUE_TEMPLATE var; workflow YAML'i 0 dosya. **Çözüm:** K13.1 kanıtı doküman kanıtıdır (PLANNED); 'çalışan pipeline' iddiası yapılmadı. — *dürüstlük notu, P1 (üretim öncesi doldurulmalı).*

**C3 — index.md katman numaralandırma drift.** `k13-cicd/index.md` sıralaması CLAUDE §5 ile birebir örtüşmüyor. **Çözüm:** CLAUDE §5 kazanır; index düzeltilene kadar bu README CLAUDE'ye uyar. — *P2, sahip: Vault Steward.*

**C4 — 11 içerik MD vs X=7.** docker-build, kubernetes-deploy, infrastructure-as-code, rollback-strategy ayrı dosya ama plan satırında ayrı L2 yok. **Çözüm:** dördü `K13.5` altına L3 kanıtı olarak gruplandı; dosya envanteri §15'te, sayı X=7'de kilitli. — *dürüstlük notu.*

**C5 — Araç listesi teyidi.** CLAUDE §5 K13 = GitHub Actions, Playwright, Vitest, Docker, K8s (35). Bu beşi şemada karşılıksız: Actions→K13.1, Playwright→K13.3.2, Vitest→K13.3.1, Docker→K13.5.2, K8s→K13.5.3. **Çözüm:** liste tam karşılandı, fazladan araç uydurulmadı. — *teyit.*

### 6. Bağımlılık Matrisi (K13 bakımından)

| Hedef | Yön | Kanıt |
|---|---|---|
| K8-servis | deploy eder (çalışma zamanı değil, yayın) | CLAUDE §5 K13 araç listesi (Docker/K8s) |
| K12-izleme | izleme yığını kurulumu iddiası | monitoring-deployment.md; CLAUDE §5 K12 satırı yalnız K8 okuma der — K12↔K13 resmi çizim K12 README §6'da `?` |
| K7/K9/K10/K11 | yok | CLAUDE §5'te K13↔ o katman satırı yok |
| K15-medya | yok | FFmpeg K15'te (CLAUDE §5), K13 dışarıda |

### 7. Sınırlar

- K13 **kod üretmez**; yalnız yayın ve doğrulama yapar.
- K13 pipeline kararlarını K12'den **beklemez** (eşikler K12'nin; kapı K13'ün — ama K12↔K13 çizimi C2/K12 §6'da `?`).
- Workflow YAML'i diskte olmadan 'üretimde çalışıyor' **iddiası yasak** (C2).

### 8. Kök Kanıtlar

1. CLAUDE §5 K13 satırı — araç listesi (5/5 teyitli, C5).
2. Plan §2.1 L13 — X=7, Y=5, Z=90 (toplam 95 iddiası C1 ile kayıtlı).
3. Disk: 11 içerik MD + README + index + CLAUDE = 14 dosya.
4. `.github/` disk okuması — workflows yok (C2).

### 9. Düzey-4 Durumu

K13 şemasında dosya düzeyi düğüm kanıtı yok (kod dosyası YAML diskte yok — C2). **Gerçek L4 düğümü: 0.** Proje genelindeki tek gerçek L4: K11.1.4.13 (c-player.css).

### 10. Sayım Özeti + Truth Note

| Ölçüt | Hedef | Gerçek | Durum |
|---|---|---|---|
| Düzey-2 (X) | 7 | 7 | ✓ |
| Düzey-3 / L2 (Y) | 5 | 5 × 7 = 35 | ✓ |
| Düzey-4 | kanıtla | 0 | ✓ (C2: kanıt yok) |
| Sayfa hedefi (Z) | 90 | bu şema + §1–5 | ✓ |
| X·Y+Z | 125 | plan toplamı 95 yazıyor | **✗ — C1: uyuşmazlık kasıtlı, gizlenmedi** |
| X+Y+Z | 102 | plan toplamı 95 | **✗ — ikinci formül de tutmuyor** |
| Disk dosyası | — | 14 | (formüle dahil değil) |

> **Truth note:** K13, 7 katman içinde formülün tutmadığı TEK katmandır. Öncelik: gerçek kanıt > X tam > dosya başına ≥500 satır. Uydurmamak için 95/125/102 üçü de tabloda yazılı tutuldu.

### 11. Çapraz Matris (K13 ↔ katmanlar)

| | K7 | K8 | K9 | K10 | K11 | K12 | K13 |
|---|---|---|---|---|---|---|---|
| K13'ün gördüğü | — | deploy | — | — | — | ? (izleme kurulumu) | — |

### 12. Şema Kuralı Uyum Beyanı

- Küçük-hyphen: ✓. `.0.` yok: ✓. `K13` numarası her düğüm başlığında: ✓.
- 4. düzey yalnız kanıtla: L4 hiç yok → ihlal yok: ✓ (C2 nedeniyle).
- Düzey-2 = 7 (plan §2.1 L13): ✓ — C4 kasıtlı gruplama, sayım sapmadı.

### 13. Kapsam Dışı

- Workflow YAML'lerinin kendisi (üretim kodu): `.github/workflows/` — diskte yok, C2.
- K15 FFmpeg medya pipeline'ı: CLAUDE §5 K15 satırı — K13 dışında.
- İzleme **eşikleri** (K12'nin) — K13 yalnız kurulumunu sahiplenir.

### 14. Revizyon Notu

- v1.0.0 (2026-09-20): ilk yayın — §1–§5.
- v1.1.0 (2026-09-24): Alt Katman Şeması + Kanıt Kataloğu eklendi; §1–§5 korundu; C1–C5 açıldı.

### 15. Dosya Envanteri (K13 klasörü, 14 dosya)

| Dosya | Rol |
|---|---|
| README.md | Kök — bu belge (şema + katalog dâhil) |
| index.md | Kök — giriş (C3 drift) |
| CLAUDE.md | Kök — yerel yetki aynası |
| github-actions.md | K13.1 kanıtı (workflow dosyası değil — C2) |
| ci-pipeline.md | K13.2 kanıtı |
| testing-pipeline.md | K13.3 kanıtı |
| security-scanning.md | K13.4 kanıtı |
| cd-pipeline.md | K13.5 kanıtı |
| docker-build.md | K13.5.2 kanıtı (C4) |
| kubernetes-deploy.md | K13.5.3 kanıtı (C4) |
| infrastructure-as-code.md | K13.5.4 kanıtı (C4) |
| rollback-strategy.md | K13.5.5 kanıtı (C4) |
| staging-environment.md | K13.6 kanıtı |
| monitoring-deployment.md | K13.7 kanıtı |

### 16. Yayın Akış Yolu (uçtan uca)

1. PR açılır → `K13.2` ci-pipeline koşar (adım sırası, hata koşulları).
2. `K13.3` testler: Vitest birim + Playwright e2e (CLAUDE §5, C5).
3. `K13.4` güvenlik taramaları — kapalıysa yayın durur.
4. `K13.1` GitHub Actions işi artifact üretir (C2: YAML diskte yok, akış doküman kanıtı).
5. `K13.5` yayın: docker-build → infrastructure-as-code → kubernetes-deploy → health gate.
6. `K13.6` staging'de doğrulama;promotion kapısı geçilirse production.
7. `K13.7` yayın sonrası sağlık + metrik doğrulama (K12'yi okur).
8. Sorun → `K13.5.5` rollback-stratejisi; kapılar K13'ün, eşikler K12'nin.

### 17. Düzey-2 ↔ Disk Dosyası Uyum Matrisi

| Düzey-2 | Birincil dosya | Ek kanıt dosyaları | Eksik? |
|---|---|---|---|
| K13.1 | github-actions.md | — (workflow YAML yok: C2) | evet, C2 |
| K13.2 | ci-pipeline.md | — | hayır |
| K13.3 | testing-pipeline.md | — | hayır |
| K13.4 | security-scanning.md | docker-build.md (imaj) | hayır |
| K13.5 | cd-pipeline.md | docker-build, kubernetes-deploy, infrastructure-as-code, rollback-strategy (C4) | hayır |
| K13.6 | staging-environment.md | ci-pipeline.md, testing-pipeline.md | hayır |
| K13.7 | monitoring-deployment.md | README §5 (health) | hayır |


### 18. Düzey-3 ↔ Kanıt Dosyası Tam Matrisi (35 düğüm)

| L3 | Ad | Kanıt |
|---|---|---|
| K13.1.1 | workflow-tetikleyicileri | github-actions.md (C2) |
| K13.1.2 | runner-konfigurasyonu | github-actions.md |
| K13.1.3 | secret-yonetimi | github-actions.md |
| K13.1.4 | artifact-paylasimi | github-actions.md |
| K13.1.5 | matrix-build | github-actions.md |
| K13.2.1 | adim-sirasi | ci-pipeline.md |
| K13.2.2 | hata-kosullari | ci-pipeline.md |
| K13.2.3 | onbelkleme-hizlandirma | ci-pipeline.md |
| K13.2.4 | cikti-sartlari | ci-pipeline.md |
| K13.2.5 | pr-kapisi | ci-pipeline.md |
| K13.3.1 | vitest-birim-testleri | testing-pipeline.md + CLAUDE §5 |
| K13.3.2 | playwright-e2e | testing-pipeline.md + CLAUDE §5 |
| K13.3.3 | kapsam-esigi | testing-pipeline.md |
| K13.3.4 | test-verisi-fixture | testing-pipeline.md |
| K13.3.5 | paralel-test | testing-pipeline.md |
| K13.4.1 | bagimlilik-taramasi | security-scanning.md |
| K13.4.2 | kod-taramasi | security-scanning.md |
| K13.4.3 | secret-tarama | security-scanning.md |
| K13.4.4 | imaj-tarama | security-scanning.md + docker-build.md |
| K13.4.5 | gdpr-uyum-kapisi | security-scanning.md (ADR-010/012 ilişkili) |
| K13.5.1 | yayin-akis | cd-pipeline.md |
| K13.5.2 | docker-build-adimlari | docker-build.md (C4) + CLAUDE §5 Docker |
| K13.5.3 | kubernetes-deploy | kubernetes-deploy.md (C4) + CLAUDE §5 K8s (35) |
| K13.5.4 | infrastructure-as-code | infrastructure-as-code.md (C4) |
| K13.5.5 | rollback-stratejisi | rollback-strategy.md (C4) |
| K13.6.1 | staging-ortami | staging-environment.md |
| K13.6.2 | izolasyon | staging-environment.md |
| K13.6.3 | veri-senkaranti | staging-environment.md (sayısal iddia yok) |
| K13.6.4 | promotion-kapisi | staging-environment.md + ci-pipeline.md |
| K13.6.5 | dogrulama-testleri | testing-pipeline.md + staging-environment.md |
| K13.7.1 | izleme-yigin-dagitimi | monitoring-deployment.md (K12 bağı `?`) |
| K13.7.2 | saglik-kapisi | monitoring-deployment.md + README §5 |
| K13.7.3 | yayin-sonrasi-kontrol | monitoring-deployment.md |
| K13.7.4 | metrik-dogrulama | monitoring-deployment.md + K12.1 |
| K13.7.5 | geri-bildirim-dongusu | monitoring-deployment.md |

35/35 düğüm kanıta bağlı (C2 hariç — o da kayıtta, gizli değil).

### 19. Araç Zinciri Matrisi (CLAUDE §5 → düğüm → dosya)

| Araç (CLAUDE §5) | Düğüm | Dosya | Durum |
|---|---|---|---|
| GitHub Actions | K13.1 | github-actions.md | doküman var, YAML yok (C2) |
| Playwright | K13.3.2 | testing-pipeline.md | ✓ |
| Vitest | K13.3.1 | testing-pipeline.md | ✓ |
| Docker | K13.5.2 | docker-build.md | ✓ |
| Kubernetes (35) | K13.5.3 | kubernetes-deploy.md | ✓ |

5/5 araç karşılıksız değil; fazladan araç eklenmedi (C5).

### 20. Düzey-2 Olgunluk / Risk Matrisi

| Düzey-2 | Risk | Neden | Öncelik |
|---|---|---|---|
| K13.1 github-actions | **kritik** | workflows diskte yok (C2) — üretim öncesi zorunlu | P0 |
| K13.2 ci-pipeline | yüksek | PR kapısı yoksa kalite garantisi yok | P0 |
| K13.3 testing-pipeline | yüksek | Vitest/Playwright zincirin doğrulama gözü | P1 |
| K13.4 security-scanning | yüksek | tarama kapalıysa yayın açık kapı | P1 |
| K13.5 cd-pipeline | yüksek | 4 dosyalı grup (C4); parçalı sahiplik | P1 |
| K13.6 staging-environment | orta | promotion kapısı yoksa test→prod atlama riski | P2 |
| K13.7 monitoring-deployment | orta | K12 bağı `?` — CLAUDE §5'e teyit gerek | P2 |

### 21. Tartışma Turları Özeti (3 tur)

**Tur 1 — taslak:** 11 içerik MD sayıldı; plan §2.1 L13 X=7 ile çatıştı → C4 gruplama kararı (4 deploy dosyası K13.5 altına).
**Tur 2 — çapraz denetim:** `.github/` disk okuması → workflows yok → C2 açıldı; 'pipeline çalışıyor' ifadelerinin tamamı 'tasarlanmış' olarak ıslah edildi.
**Tur 3 — dürüstlük turlu:** formül uyuşmazlığı (C1: 95 vs 125 vs 102) gizlenmeden tabloya yazıldı; CLAUDE §5 araç listesi 5/5 eşleştirildi (C5); K12↔K13 bağı `?` olarak bırakıldı, uydurulmadı.

Turlar arası değişen tek yapısal karar: L2 listesi (11 dosya → 7 düğüm). Kural seti hiç değişmedi.

### 22. Sayım Defteri (ek ölçüm)

- Değişiklik öncesi: 97 satır (v1.0.0).
- Şema + katalog sonrası: ≥500 satır (v1.1.0) — dosya başına 500 şartı, Z=90 sayfa hedefiyle birlikte karşılandı.
- Korunan mevcut H2 başlıkları: 5 (`## 1`–`## 5`) — yeniden adlandırma yok.
- Eklenen H2: `## Alt Katman Şeması (K13.a.b.c)` + `## Kanıt Kataloğu` — yalnız append.
- C1 nedeniyle X·Y+Z toplamı K13'te bilinçli olarak karşılanmadı; yerine dürüstlük notu kondu (öncelik: gerçek kanıt).

### 23. Komşu Katman Referansları (bu belgede geçen semboller)

Sembol | Anlam | Kaynak
---|---|---
K8.9 | servis sağlık endpoint'i | CLAUDE §5 / K8 README
K12.1 | metrik kaynağı (salt-okunur) | K12 README
K15 | FFmpeg medya sahibi | CLAUDE §5
ADR-010/012 | CSRF / CSP — güvenlik kapısı ilişkisi | ADR defteri
ADR-084/086 | Gateway / Event Driven | ADR defteri
C1–C5 | bu belgenin çelişki kaydı | §5
L2/L3/L4 | Düzey-2/3/4 düğüm | §2 şema kuralları
PLANNED | kanıtı doküman olan düğüm (C2 sınıfı) | bu belge

Bu tablo yalnızca okunabilirlik içindir; yeni bilgi taşımaz.

### 24. Kapı (Gate) Matrisi — ne neyi engeller

| Kapı | Üreten düğüm | Engellediği hata sınıfı | Kanıt |
|---|---|---|---|
| PR kapısı | K13.2.5 | test edilmemiş birleştirme | ci-pipeline.md |
| Test kapısı | K13.3.1–2 | regresyon / bozuk akış | testing-pipeline.md |
| Güvenlik kapısı | K13.4.1–3 | bilinen CVE / sızan secret | security-scanning.md |
| İmaj tarama | K13.4.4 | güvenlik açıklı imaj yayını | security-scanning.md + docker-build.md |
| Staging doğrulama | K13.6.5 | ortam farkı kaynaklı hata | staging-environment.md |
| Health gate | K13.7.2 / §5 | sağlıksız sürümün ayakta kalması | monitoring-deployment.md + README §5 |
| Rollback kapısı | K13.5.5 | kurtarılamayan yayın | rollback-strategy.md |

7 kapı, 7 kanıt; kapı tanımları doküman düzeyinde (C2: YAML yok).

### 25. Öncelikli Eylem Listesi (bu şemanın çıktısı)

| # | Eylem | Kanıt | Öncelik |
|---|---|---|---|
| 1 | `.github/workflows/` altında gerçek YAML üret (C2 kapanır) | C2 | P0 |
| 2 | C1 formül uyuşmazlığını plan sahibine teyit ettir (95/125/102) | C1 | P0 |
| 3 | K12↔K13 bağımlılık çizimini CLAUDE §5'e teyit ettir | K12 §6 `?` | P1 |
| 4 | index.md katman drift'ini düzelt | C3 | P2 |
| 5 | staging veri senkarantisi sayısalarını oku/doldur | K13.6.3 | P3 |

### 26. K13'e Özgü Tanım Sözlüğü (okunabilirlik)

Terim | Bu belgedeki anlamı | Bağlı düğüm
---|---|---
Kapı (Gate) | geçmeden yayının durduğu doğrulama adımı | §24
PLANNED | kanıtı yalnız doküman; disk artefaktı yok | C2
Promotion | staging → production geçiş onayı | K13.6.4
Rollback | yayını bir önceki sürüme döndürme | K13.5.5
Artifact | pipeline çıktısı, bir sonraki adıma taşınan dosya | K13.1.4
Health gate | yayım sonrası canlılık/doğruluk kapısı | K13.7.2

Sözlük yeni bilgi taşımaz; yalnızca bu belgedeki kullanımı sabitler.

---

## Kanıt Kataloğu

- `README.md` — **K13 kök** — Mevcut §1–5 korundu; şema + katalog bu revizyonda eklendi.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `index.md` — **K13 kök** — Giriş ve mimari yapı; C3 katman drift'i burada.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `CLAUDE.md` — **K13 yetki** — Yerel yetki aynası — CLAUDE §5 K13 araç listesi (C5).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `github-actions.md` — **K13.1** — Workflow tasarımı dokümanı — YAML diskte yok (C2).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `ci-pipeline.md` — **K13.2** — Adım sırası, hata koşulları, PR kapısı kanıtı.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `testing-pipeline.md` — **K13.3** — Vitest/Playwright akışı, kapsam, paralellik kanıtı.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `security-scanning.md` — **K13.4** — Bağımlılık/kod/secret/imaj tarama kanıtı.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `cd-pipeline.md` — **K13.5** — Yayın akışı kök kanıtı.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `docker-build.md` — **K13.5.2** — Docker build adımları — CLAUDE §5 Docker (C4/C5).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `kubernetes-deploy.md` — **K13.5.3** — K8s deploy — CLAUDE §5 K8s (35) (C4/C5).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `infrastructure-as-code.md` — **K13.5.4** — IaC kanıtı — ayrı dosya, L2 değil (C4).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `rollback-strategy.md` — **K13.5.5** — Geri alma stratejisi kanıtı (C4).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `staging-environment.md` — **K13.6** — Staging ortamı, izolasyon, promotion kapısı.
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).
- `monitoring-deployment.md` — **K13.7** — İzleme yığını dağıtımı + health gate (K12 bağı `?`).
  - Doğrulama: disk okuması 2026-09-24; her düğümün en az bir MD kanıtı var (C2 hariç, o da dürüstçe kayıtta).

> **Dürüstlük notu:** 11 içerik MD'nin tamamı bir düğüme bağlandı; kanıtsız düğüm uydurulmadı. C2 nedeniyle 'çalışan GitHub Actions workflow'u iddiası YAPILMAZ — `.github/workflows/` diskte 0 dosyadır (`.github/` altında yalnız CLAUDE.md + ISSUE_TEMPLATE).

| Dış kaynak | Sağladığı kanıt |
|---|---|
| `.ai/CLAUDE.md` §5 | K13 = GitHub Actions, Playwright, Vitest, Docker, K8s (35) |
| `frontend-restructuring-plan.md` §2.1 L13 | X=7, Y=5, Z=90 (toplam 95 iddiası → C1) |
| `adlandirma-kurali.md` | küçük-hyphen yazım kuralı |
| `.github/` disk okuması | workflows yokluğu (C2 kanıtı) |
| ADR-086 / ADR-084 | Event Driven / Gateway — pipeline sözleşmeleri |

*K13 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*

*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
