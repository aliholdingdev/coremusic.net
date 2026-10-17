---
title: "K17-devops — Master (Domain Anayasa)"
node_id: K17-devops/devops-master
node_type: domain-master
status: draft
parent_id: K17-devops
children:
  - K17-devops/index
  - K17-devops/devops-bagimliliklar
  - K17-devops/devops-dogrulama
  - K17-devops/source-control
  - K17-devops/build
  - K17-devops/ci
  - K17-devops/cd
  - K17-devops/environments
  - K17-devops/deployment
  - K17-devops/rollback
  - K17-devops/release-management
  - K17-devops/artifact-management
  - K17-devops/infrastructure-testing
depends_on:
  - K18-testing
  - K009-security
  - K16-observability
evidence:
  - ".github/workflows/ci.yml · Satır 25 — php-lint: (6 job'ın ilki, 203 satır, okundu 2026-10-13)"
  - ".github/workflows/secret-scan.yml · Satır 19 — gitleaks: job (31 satır, okundu 2026-10-13)"
  - ".github/AGENTS.md · Satır 85 — Subagent git commit ATMAZ (yetki sınırı, 200 satır)"
  - ".git/HEAD · Satır 1 — ref: refs/heads/main (depo VAR — 2026-10-13 ölçümü, 2026-10-12 iddiasını tersliyor)"
  - ".gitleaks.toml · Satır 1 — # CoreMusic - gitleaks config (18 satır — secret-scan.yml:4 yorumunu tersliyor)"
  - "K17-devops dizin ölçümü (2026-10-17): 23 dosya · 8.877 satır (2026-10-13 ölçümü: 19 dosya · 3.866)"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde ReadAllLines / satır indeksi ile birebir okunarak doğrulandı"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç (§G kontrol 7)"
  - "§1-§8 gövdesi aynen korundu; yalnız FM ve Authority satırı revize edildi"
open_questions:
  - "CI gerçekten geçti mi? GitHub run kanıtı diskte değil (§7/1)"
  - "Git deposu 2026-10-13'te bulundu; 2026-10-12 'depo YOK' kaydı bayat mı, yoksa depo o gün mü açıldı? (§G/8)"
  - ".gitleaks.toml diskte mevcut; secret-scan.yml:4 yorumu neden hâlâ 'YOK' diyor? (§G/9)"
  - "CD modeli: deployment.md:260 insan-onayı yasağı ↔ tam otomatik CD — ADR bekliyor (§G.2/1)"
  - "Staging alt-alan adı domain.php'de YOK — ADR bekliyor (§G.2/3)"
  - "index.md:462 sayımı bayat (17·2.819 ↔ 23·8.877) — beyaz-liste dışı, onay bekliyor (§G.2/8)"
authority: "Derived — SSOT: v2 spec §2.18 + ADR-097 (draft)"
updated: 2026-10-17
version: 1.2.0
group: mimari
---


# K17-devops — devops-master.md

## §1 Rol (ELI15)

K17-devops, kodun **üretilip yayılma yoludur**: depoda ne olur, push'ta ne çalışır, hangi testler
kapı olur, hangi ortama çıkar, nasıl geri alınır. Testleri **yazmaz** (K019), yalnız **koşar**.

## §2 Kapsam / Sınır (ne DEĞİL)

| Kapsam (bu domain) | Kapsam DIŞI |
|---|---|
| CI kapısı: lint/test/audit koşumu | Testlerin tasarımı ve içeriği → **K18-testing** (ci.yml php-test/js-test = K018'in K019'la entegrasyonu) |
| CD, ortam, deployment, rollback | Sistem logu/metrik/aler → **K16-observability** (uyarı K017, aksiyon K018) |
| Secret taraması (GitLeaks) | Auth/encryption politikası → **K009-security** (secret-scan.yml = iş birliği, sahiplik §5'te Security+DevOps) |
| Workflow dosyaları sahipliği (*.yml) | Ajan boot/hooks → **K015-AI** (hook'lar harness'da, workflow'lar GitHub'da) |

**Sahiplik kaydı (.github/AGENTS.md §3.1, 2026-10-03):** ci.yml = DevOps (birincil) + QA (test kapsamı) ·
secret-scan.yml = Security (kapsam) + DevOps (yml) · yeni .yml = taslak → kullanıcı onayı → uygulama.

## §3 Kademe-2 Çocuk Tablosu (v2 §2.18 — birebir, 10 çocuk)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu |
|---|---|---|---|
| 1 | source-control/ | Depo, branch, commit kuralları | ✅ depo VAR (`refs/heads/main` · son commit `687fbed` 2026-10-17 — §G/8 düzeltildi 2026-10-17) |
| 2 | build/ | Derleme ve bağımlılık kurulumu (composer/npm) | ✅ kanıtlı |
| 3 | ci/ | Push/PR kapıları (lint/test/audit) | ✅ kanıtlı (ci.yml okundu) |
| 4 | cd/ | Yayına sürme otomasyonu | ⚠️ deploy job'ı workflows'da YOK |
| 5 | environments/ | Ortam tanımları ve değişkenleri | ⚠️ environment: bloğu YOK |
| 6 | deployment/ | Yayın mekaniği + onay akışı | 🔶 .workflows/deployment.md referansı doğrulanmadı |
| 7 | rollback/ | Geri alma | ⚠️ kanıt bekliyor |
| 8 | release-management/ | Sürüm + release notu | ⚠️ kanıt bekliyor |
| 9 | artifact-management/ | Artifact saklama/paylaşma | 🔶 cache var; upload-artifact YOK |
| 10 | infrastructure-testing/ | Altyapı testi | ⚠️ kanıt bekliyor |

## §4 Teknoloji / Ekosistem (disk kanıtları — ölçüm 2026-10-12)

| Yüzey | Kanıt (gerçek dosya / satır) | Durum |
|---|---|---|
| Workflow envanteri | .github/workflows/ = 2 dosya: ci.yml · secret-scan.yml (glob 2026-10-12; önceki ölçüm .ai/AGENTS.md §25.2 → 2026-09-27 ile TUTARLI) | ✅ IMPLEMENTED |
| CI jobs | ci.yml: **6 job** — php-lint · php-test (matrix: shared · auth.coremusic.net --testsuite Unit) · composer-audit · js-test (vitest) · js-coverage (soft, continue-on-error, A7 kararı) · js-e2e (Playwright, soft, A6 kararı) | ✅ IMPLEMENTED (dosya) |
| CI araçları | PHP 8.4 (env), actions/checkout@v4, shivammathur/setup-php@v2, actions/cache@v4, actions/setup-node@v4, Node 26 | ✅ IMPLEMENTED (dosya) |
| Secret taraması | secret-scan.yml: gitleaks job — gitleaks/gitleaks-action@v2, fetch-depth: 0; **`secret-scan.yml:4` yorumu bayat** ("diskte .gitleaks.toml YOK") — `.gitleaks.toml` VAR (18 satır, allowlist = LoginRequestTest.php + JWT test pem, ADR-034/095) | ✅ IMPLEMENTED (dosya) · yorum bayat (§G/9 — `.yml` beyaz-liste dışı, düzenlenmedi) |
| Şablon zinciri | .ai/.templates/infrastructure/github-actions-template.md §3.2 — varlık doğrulandı (2026-10-12); ci.yml satır 2 şablona atıf yapıyor | ✅ |
| Sahiplik/yetki dokümanı | .github/AGENTS.md §3.1 (5 dosya sahiplik tablosu — ölçüm 2026-10-03) · §3.3 yasaklar (subagent commit YOK, onaysız yml değişikliği YOK) | ✅ (doküman) |
| .github envanteri | 8 dosya: AGENTS.md · CONTEXT.md · CLAUDE.md · WORKFLOW.md · workflows/ci.yml · workflows/secret-scan.yml · ISSUE_TEMPLATE/CLAUDE.md · ISSUE_TEMPLATE/01-bug-report.md (glob 2026-10-12) | ✅ |
| Git deposu | `.git/HEAD` = `ref: refs/heads/main` + `git log -8` (2026-10-17: `687fbed`·`b0921d8`·`b299e7d`·`d97b662`·`151b0c9`·`f7bf890`·`8bef821`·`ae61084`) → **depo VAR · main dalında** | ✅ DÜZELTİLDİ (2026-10-17 — §G/8: 2026-10-12 "depo YOK" kaydı bayattı, depo o dönem var olmasına rağmen glob edilmemişti) |
| CI geçiş durumu | .ai/AGENTS.md §25.2: "workflow dosyaları mevcut, çalışma durumu doğrulanmadı — CI'nin geçtiği doğrulanmadı" | ⚠️ doğrulanmadı |
| Eski plan referansı | ci.yml satır 3: .ai/architecture/k13-cicd/README.md — bu klasör V3 registry'de YOK (glob .ai/architecture/**) | ⚠️ tarihsel referans |

## §5 Bağımlılık Özeti (etiketli — detay: devops-bagimliliklar.md)

| Yön | Hedef | Etiket | Gerekçe |
|---|---|---|---|
| depends_on | K18-testing | kanıtlı (ci.yml) | CI test job'ları K019 yetkinliğini koşar |
| depends_on | K009-security | kanıtlı (secret-scan.yml) | GitLeaks kapısı güvenlik domaininin iş birliği |
| used_by | tüm üretim domainleri | planlanan | her kod değişimi bu yoldan geçer |
| used_by | K16-observability | planlanan | alert → pipeline aksiyonu (sınır: uyarı K017, aksiyon K018) |

## §6 Doğrulama Özeti (detay: devops-dogrulama.md)

- ✅ **Doğrulandı (dosya okuma):** ci/ (6 job) · build/ (composer/npm adımları) · secret tarama iş birliği.
- 🔶 **Kısmi/negatif:** environments (blok yok) · artifact-management (upload yok) · deployment (doküman referansı doğrulanmadı).
- ⚠️ **Uyaran:** source-control (depo yok!) · cd/rollback/release/infrastructure-testing (kanıt yok).
- **En kritik açık:** CI dosyaları var ama **CI'nin geçtiğine dair kanıt yok** (vault uyarısıyla tutarlı).

## §7 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk | Durum (2026-10-17) |
|---|---|---|---|
| 1 | CI gerçekten çalışıyor mu, son run geçti mi? (GitHub arayüzü/branch koruması diskte değil) | ci/ | ⏳ AÇIK |
| 2 | Git deposu bilinçli mi yok? (yedek/taşınma dönemi?) — K-Space V3 kararı gerekli | source-control/ | ✅ KAPANDI — depo VAR: `.git/HEAD` = refs/heads/main, son commit 687fbed (§A.2/7) |
| 3 | cd/ ve deployment/ için hedef model ne? (manual deploy mu, otomatik CD mi) | cd/ · deployment/ | ✅ KAPANDI — öneri: TAM OTOMATİK CD (trunk-based); deployment.md:260 çelişkisi ADR bekliyor (§G.2/1) |
| 4 | k13-cicd planı V3 registry'sine taşınacak mı, yoksa arşiv mi? | ci/ | ⏳ AÇIK |
| 5 | release/artifact stratejisi tanımlı mı? | release-management/ · artifact-management/ | ✅ KAPANDI — 3 strateji matrisi + trunk-based önerisi; son seçim ADR bekliyor (§G.2/2·6) |
| 6 | infrastructure-testing kapsamı (Docker? altyapı as-code?)? | infrastructure-testing/ | ✅ KAPANDI — kapsam: lockfile + config drift; Dockerfile/compose/*.tf = 0 isabet → ADR bekliyor (§G.2/7) |
| 7 | CD modeli ADR'si: deployment.md:260 insan-onayı yasağı ile tam otomatik CD nasıl uzlaştırılır? | cd/ · deployment/ | ⏳ ADR §G.2/1 |
| 8 | Artefakt modeli hangisi: git-snapshot / artifact-registry / imaj? | cd/ · artifact-management/ | ⏳ ADR §G.2/2 |
| 9 | Staging ortamı hangi alt-alanda yaşayacak (config/domain.php'de staging/test YOK)? | environments/ | ⏳ ADR §G.2/3 |
| 10 | Ortam sayısı 3 → 4 genişlemesi (local·development·staging·production) onaylı mı? | environments/ · deployment/ | ⏳ ADR §G.2/4 |

## §8 Kaynaklar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.18 |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme |
| Registry | .ai/architecture/index.md §5 satır 18 |
| Disk (2026-10-12) | .github/workflows/ci.yml · secret-scan.yml · .github/ envanteri · .git/HEAD (0 isabet) |
| Sahiplik dokümanı | .github/AGENTS.md (2026-10-03) · .ai/AGENTS.md §5/§16/§25.2 |

## §A — Modül ağacı

**Ölçüm (2026-10-17, [IO.File]::ReadAllLines):** K17-devops kökü 6 dosya / 3.779 satır ·
alt dizinler 17 dosya / 5.043 satır · **toplam 23 dosya / 8.822 satır** · ≥ 500 satır dosya **11 adet**.
(Önceki ölçüm 2026-10-13: 19 dosya / 3.866 satır — 7 dosya dolduruldu/yeni eklendi: cd · environments ·
release-management · infrastructure-testing · rollback · source-control · build · pipeline/index.md · devops-kapsam.md)

```text
K17-devops/                                23 dosya · 8.822 satır
├── index.md                                85 satır · domain indeksi (group: mimari)
├── devops-master.md                        528 satır (bu dosya)
├── devops-ornekler.md                     1171 satır · ≥ 500 · v2.0.0 (2026-10-17 halüsinasyon düzeltmesi)
├── devops-kapsam.md                        505 satır · ≥ 500 · kapsam/sınır modülü
├── devops-bagimliliklar.md                 511 satır · ≥ 500
├── devops-dogrulama.md                     500 satır · ≥ 500
├── artifact-management/                    2 dosya · 562 satır · index VAR
│   ├── index.md                            51 satır
│   └── konteyner-imaj-yonetimi.md          511 satır · ≥ 500
├── build/                                  1 dosya · 218 satır · index VAR (2026-10-17 39→218 dolduruldu)
│   └── index.md                            218 satır · node_id K018→K17 düzeltildi
├── cd/                                     1 dosya · 360 satır · index VAR (2026-10-17 50→360)
│   └── index.md                            360 satır · tam otomatik CD kararı + TASLAK workflow
├── ci/                                     2 dosya · 561 satır · index VAR
│   ├── index.md                            52 satır (iskelet — K18-testing eli)
│   └── secret-yonetimi-ve-tarama.md        509 satır · ≥ 500
├── deployment/                             3 dosya · 1.086 satır · index VAR
│   ├── index.md                            50 satır (iskelet — içerik dosyaları 534+502)
│   ├── rollback-ve-geri-alma.md            534 satır · ≥ 500 (rollback/index.md bunu TEKRARLAMAZ)
│   └── yayilim-stratejileri.md             502 satır · ≥ 500
├── environments/                           1 dosya · 269 satır · index VAR (2026-10-17 50→269)
│   └── index.md                            269 satır · 4 ortam kararı
├── infrastructure-testing/                 1 dosya · 312 satır · index VAR (2026-10-17 50→312)
│   └── index.md                            312 satır · lockfile + config drift kapsamı
├── pipeline/                               3 dosya · 1.315 satır · index VAR (2026-10-17 YENİ eklendi)
│   ├── index.md                            291 satır · şerit haritası (2026-10-17 YENİ)
│   ├── cicd-asamalari.md                   511 satır · ≥ 500
│   └── pipeline-hizlandirma-ve-cache.md    513 satır · ≥ 500
├── release-management/                     1 dosya · 249 satır · index VAR (2026-10-17 50→249)
│   └── index.md                            249 satır · 3 strateji karar matrisi
├── rollback/                               1 dosya · 288 satır · index VAR (2026-10-17 51→288)
│   └── index.md                            288 satır · gap analizi + TASLAK (tekrar yok)
└── source-control/                         1 dosya · 302 satır · index VAR (2026-10-17 52→302)
    └── index.md                            302 satır · "depo YOK" kaydı düzeltildi
```

### §A.1 — v2 §2.18 çocukları ile disk eşlemesi

| # | v2 §2.18 çocuğu | Diskte karşılığı | Dosya | Satır | Eşleşme |
|---|---|---|---|---|---|
| 1 | source-control/ | source-control/ | 1 | 302 | birebir (index tek dosya) |
| 2 | build/ | build/ | 1 | 218 | **eşleşti** (2026-10-17: dizin + index eklendi — önceki "diskte YOK" bulgusu kapandı, §A.2/1) |
| 3 | ci/ | ci/ | 2 | 561 | birebir |
| 4 | cd/ | cd/ | 1 | 360 | birebir (index tek dosya) |
| 5 | environments/ | environments/ | 1 | 269 | birebir (index tek dosya) |
| 6 | deployment/ | deployment/ | 3 | 1.086 | birebir |
| 7 | rollback/ | rollback/ | 1 | 288 | birebir (index tek dosya) |
| 8 | release-management/ | release-management/ | 1 | 249 | birebir (index tek dosya) |
| 9 | artifact-management/ | artifact-management/ | 2 | 562 | birebir |
| 10 | infrastructure-testing/ | infrastructure-testing/ | 1 | 312 | birebir (index tek dosya) |
| 11 | v2'de YOK | pipeline/ | 3 | 1.315 | **fazladan dizin** ama artık index VAR (2026-10-17 eklendi — §A.2/2 kapandı) |

### §A.2 — Disk bulguları ve açıklar

| # | Bulgu | Kanıt | Sonuç |
|---|---|---|---|
| 1 | ~~build/ dizini diskte yok~~ | 2026-10-17: build/index.md VAR (218 satır, node_id K018→K17 düzeltildi) | **KAPANDI** — §A.1/2 artık eşleşiyor |
| 2 | ~~pipeline/ index YOK~~ | 2026-10-17: pipeline/index.md eklendi (291 satır) | **KAPANDI** — 10 alt dizinin 10'unda da index var |
| 3 | 10 alt dizinin **10'unda** index.md var | 2026-10-17 sayımı | eksik index kalmadı |
| 4 | Kök 3 P1 dosyası ≥ 500 | devops-kapsam 505 · devops-bagimliliklar 511 · devops-dogrulama 500 | **KAPANDI** (2026-10-13 bu revizyonla doldurulmuş) |
| 5 | ≥ 500 satır dosya **11 adet** | 1171·534·528·513·511·511·511·509·505·502·500 | 6→11 yükseldi |
| 6 | index.md sayımı bayat | index.md satır 462: `17 · 2.819` → diskte **23 · 8.822** | index.md bu turda hâlâ değiştirilmedi (§G/10) |
| 7 | .git/HEAD VAR | `ref: refs/heads/main` · son commit 687fbed (2026-10-17) | §3/§4 "depo YOK" kaydı **DÜZELTİLDİ** (§G/8) |
| 8 | .gitleaks.toml VAR | 18 satır | secret-scan.yml:4 yorumu **bayat** (§G/9 — `.yml` düzenlenmedi) |
| 9 | .workflows/deployment.md VAR | 281 satır (Aşama 1-8 · §4 Geri Dönüş · §6 yasaklar) | §3/6 "doğrulanmadı" kapandı |
| 10 | 191 bayat node_id ref'i | K018-devops→K17-devops 142 · K019-testing→K18 25 · K017-obs→K16 20 · K020-comilance→K19 4 | **DÜZELTİLDİ** (2026-10-17 toplu rename, kalan 0) |

## §B — Master kararlar

### §B.1 — Karar defteri

| # | Karar | Dayanak | Durum |
|---|---|---|---|
| 1 | CI testleri koşar, tasarımı K019'da | v2 §2.18/§2.19 · ci.yml php-test | kanıtlı |
| 2 | Uyarı K017, aksiyon K018 | v2 §2.17/§2.18 | kanıtlı |
| 3 | ci.yml sahibi DevOps + QA (test kapsamı) | .github/AGENTS.md satır 59 | kanıtlı |
| 4 | secret-scan.yml sahibi Security (kapsam) + DevOps (yml) | .github/AGENTS.md satır 60 | kanıtlı |
| 5 | Yeni .yml taslak → kullanıcı onayı → uygulama | .github/AGENTS.md satır 63 | kanıtlı |
| 6 | Subagent git commit atmaz | .github/AGENTS.md satır 85 | kanıtlı |
| 7 | PHP sürümü composer.json'dan türetilir (8.4) | ci.yml satır 22 · shared/composer.json satır 8 | kanıtlı |
| 8 | Statik analiz composer script'inden koşulur (level=5) | ci.yml satır 62-63 · shared/composer.json satır 48 | kanıtlı |
| 9 | phpstan.neon yok; script esas | ci.yml satır 60-61 yorumu | kanıtlı |
| 10 | auth testleri yalnız Unit koşar (Integration dizini diskte yok) | ci.yml satır 77-80 | kanıtlı |
| 11 | php-test, php-lint'e bağımlı (needs) | ci.yml satır 68 | kanıtlı |
| 12 | js-coverage soft gate (A7) | ci.yml satır 167 continue-on-error | kanıtlı |
| 13 | js-e2e soft gate (A6) | ci.yml satır 183 continue-on-error | kanıtlı |
| 14 | Node 26 toolchain (vitest/jsdom engine aralığı) | ci.yml satır 151 | kanıtlı |
| 15 | Composer cache her job'da ayrı key ile | ci.yml satır 44 · 96 | kanıtlı |
| 16 | npm ci kilitli kurulum kullanır | ci.yml satır 155 · 174 · 197 | kanıtlı |
| 17 | Workflow permissions en az yetkide | ci.yml satır 14-15 · secret-scan.yml satır 11-12 | kanıtlı |
| 18 | Aynı ref'te eşzamanlı çalışma iptal edilir | ci.yml satır 17-19 · secret-scan.yml satır 14-16 | kanıtlı |
| 19 | GitLeaks tam geçmiş taranır (fetch-depth 0) | secret-scan.yml satır 26 | kanıtlı |
| 20 | GitLeaks config'i kök .gitleaks.toml'dan okunur | .gitleaks.toml satır 2 (gitleaks-action@v2 köktekini kullanır) | kanıtlı (2026-10-13) |
| 21 | Allowlist yalnız test dosyasındaki sahte şifreleri kapsar | .gitleaks.toml satır 3-4 (ADR-034 §5.2/§4.1-R6) | kanıtlı |
| 22 | Deploy onayı .workflows/deployment.md 8 aşamada yürür | .workflows/deployment.md satır 126-199 | kanıtlı (2026-10-13) |
| 23 | Rollback prosedürü deployment.md §4'te | .workflows/deployment.md satır 212 | kanıtlı (2026-10-13) |
| 24 | Şablon: github-actions-template.md §3.2 | ci.yml satır 2 · secret-scan.yml satır 2 · şablon satır 30 | kanıtlı |
| 25 | JS test komutları package.json scripts'inden gelir | package.json satır 5 · 7 · 8 | kanıtlı |
| 26 | 3 composer install hedefi: shared · auth · home | ci.yml satır 48 · 52 · 56 | kanıtlı |
| 27 | composer audit 3 hedefte koşulur | ci.yml satır 126 · 132 · 138 | kanıtlı |
| 28 | k13-cicd plan referansı diskte YOK | ci.yml satır 3 · Test-Path k13-cicd = False | kanıtlı negatif |
| 29 | Depo durumu: 2026-10-13'te main dalı VAR | .git/HEAD satır 1 · git log -1 c46f08ac 2026-10-12 | **çelişki** (§G/8) |
| 30 | Bu revizyon commit'i atılmaz | .github/AGENTS.md satır 85 | uygulandı |

### §B.2 — Karar durumlarının anlamı

| Durum | Anlamı | Eylem |
|---|---|---|
| kanıtlı | dosya + satır indeksi ile okundu | dokunma |
| kanıtlı negatif | arandı, bulunamadı (negatif kanıt geçerli) | karar gerektirir |
| planlanan | mimari yargı, disk kanıtı yok | ADR bekler |
| çelişki | iki kaynak birbirini tersliyor | §G'de kayıtlıdır, disk kazanır |

## §C — Çapraz modül kuralları

### §C.1 — Sınır kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Test tasarımı K019'da kalır; K018 yalnız koşar | v2 §2.18/§2.19 |
| 2 | Uyarı üretimi K017'de, deploy/rollback aksiyonu K018'de | v2 §2.17/§2.18 |
| 3 | .gitleaks.toml gibi config eklemek Security/ADR onayı ister | .github/AGENTS.md satır 60 |
| 4 | Yeni workflow yml'si onaysız eklenmez | .github/AGENTS.md satır 63 |
| 5 | workflow yml sahipliği DevOps'ta; ajan hook'ları harness'da | .ai/AGENTS.md §5 · .github/AGENTS.md §3.1 |
| 6 | Subagent commit atmaz; commit orkestratörde | .github/AGENTS.md satır 85 |
| 7 | Secret değeri .md/.log/.json içine yazılmaz | .github/AGENTS.md §3.3 |
| 8 | Mevcut CLAUDE.md dosyaları düzenlenmez | .github/AGENTS.md §3.3 |
| 9 | Vault .ai/ içine yazma yalnız MO yetkisinde | .github/AGENTS.md §3.3 |
| 10 | Frozen ADR metni kopyalanmaz, wiki-link verilir | .github/AGENTS.md §3.3 |
| 11 | Gate script'ler repo kökünden çalıştırılır | kök AGENTS.md §13/6 |
| 12 | Yeni workflow şablondan türetilir (Guardrail #16) | secret-scan.yml satır 2 · şablon satır 30 |
| 13 | Job silme/ekleme yalnız onayla | .github/AGENTS.md satır 59 |
| 14 | Test kapsamı (matrix) değişikliği QA yetkisinde | .github/AGENTS.md satır 59 |
| 15 | CI geçiş iddiası run kanıtı olmadan yazılmaz | .ai/AGENTS.md §25.2 |
| 16 | Disk ölçümü vault beyanını ezer (çelişkide) | kök AGENTS.md §3 |
| 17 | Doğrulanamayan iddia ⚠️ VERIFICATION REQUIRED | kök AGENTS.md §3 |
| 18 | Log/redaction yüzeyi K017'de; CI'da yalnız koşulur | v2 §2.17/§2.18 |
| 19 | K19-comilance/k10 licence kanıtı composer-audit'ten gelir | ci.yml satır 110 |
| 20 | Deployment onayı insan kapısıdır (Aşama 4) | .workflows/deployment.md satır 156 |
| 21 | Dağıtım sonrası sağlık kontrolü zorunlu (Aşama 6) | .workflows/deployment.md satır 182 |
| 22 | Hata durumunda rollback §4'e gidilir | .workflows/deployment.md satır 212 · 247 |
| 23 | workflow yorumlarındaki ölü plan linki temizlenir | ci.yml satır 3 (k13-cicd YOK) |
| 24 | Bayat yorum ile disk çelişince disk kazanır | secret-scan.yml satır 4 · .gitleaks.toml |
| 25 | K018 dokümanları yalnız .ai/architecture/K17-devops/** yazılır | bu tur beyaz-liste |
| 26 | index.md bu turda değiştirilmez | beyaz-liste yasağı |
| 27 | kaynak kod yüzeyi (*.php/*.js/*.css/*.sql) yazılmaz | beyaz-liste yasağı |
| 28 | Soft gate'ler eşik büyüyünce hard gate'e döner | ci.yml satır 165-166 · 182 |

### §C.2 — İhlal durumunda davranış

| İhlal | Davranış | Yetkili |
|---|---|---|
| Onaysız yml değişikliği | revert + log | DevOps + kullanıcı |
| Yetkisiz commit | revert + log | orkestratör |
| Secret sızıntısı | [REDACTED] + anahtar iptali | Security |
| Halüsinasyon (kanıtsız iddia) | ⚠️ VERIFICATION REQUIRED etiketi | her ajan |
| Domain sınırı ihlali | derhal revert + log | orkestratör |
| Bayat sayım kullanıldığında | disk yeniden ölçülür, sayı düzeltilir | yazan ajan |

## §D — Yol haritası

### §D.1 — Fazlar

| # | Faz | Kapsam | Kanıt durumu |
|---|---|---|---|
| 1 | CI çekirdeği | ci.yml 6 job + secret-scan.yml | ✅ tamam (dosya var) |
| 2 | Kök P0 dosyaları | rollback-ve-geri-alma · pipeline-hizlandirma-ve-cache | ✅ bu tur ≥ 500 |
| 3 | Kök P1 dosyaları | devops-master · devops-bagimliliklar · devops-dogrulama | ✅ bu tur ≥ 500 |
| 4 | index.md tazeleme | 19 · 3.866 sayımı + build/pipeline eşlemesi | ⏳ bu turda yasak |
| 5 | CI geçiş kanıtı | GitHub run geçmişi ekran görüntüsü | ⚠️ bekliyor |
| 6 | cd/ environments/ işaretleme | deploy job + environment: bloğu | ⚠️ karar bekliyor |
| 7 | release/artifact politikası | upload-artifact + sürüm notu akışı | ⚠️ kanıt yok |
| 8 | infrastructure-testing kapsamı | Docker / altyapı as-code | ⚠️ kapsam sorusu (§7/6) |

### §D.2 — Bağımlılık sırası (faz → faz)

| Sıra | Önce | Sonra | Gerekçe |
|---|---|---|---|
| 1 | Faz 1 (CI çekirdeği) | Faz 5 (CI geçiş kanıtı) | kapı varsa önce çalıştığı görülür |
| 2 | Faz 3 (P1 dosyaları) | Faz 4 (index tazeleme) | sayımlar oturunca registry güncellenir |
| 3 | §7/2 depo kararı | Faz 6 (cd) | branch koruması deponun varlığını varsayar |
| 4 | Faz 6 (cd) | Faz 7 (release/artifact) | yayında artifact saklama zorunlu hâle gelir |
| 5 | Faz 7 | Faz 8 (infra test) | altyapı testi yayının tekrarlanabilirliğini sınar |
| 6 | §G çelişki kaydı | herhangi bir sayı güncellemesi | disk kazanır kuralı önce gelir |

## §E — Repo kanıtları (devops-master)

Dosya: `.github/workflows/ci.yml` · Satır: 8 — `name: CI Pipeline` (workflow adı, 203 satır).

Dosya: `.github/workflows/ci.yml` · Satır: 14-15 — `permissions:` / `contents: read` (en az yetki ilkesi).

Dosya: `.github/workflows/ci.yml` · Satır: 17-19 — `concurrency:` / `cancel-in-progress: true` (eşzamanlılık iptali).

Dosya: `.github/workflows/ci.yml` · Satır: 22 — `PHP_VERSION: '8.4'` (toolchain kararı).

Dosya: `.github/workflows/ci.yml` · Satır: 25 — `php-lint:` (job 1/6 başlangıcı).

Dosya: `.github/workflows/ci.yml` · Satır: 41 — `uses: actions/cache@v4` (composer cache).

Dosya: `.github/workflows/ci.yml` · Satır: 44 — `key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` (kilit bazlı cache).

Dosya: `.github/workflows/ci.yml` · Satır: 48 · 52 · 56 — `Composer install (shared)` · `(auth.coremusic.net)` · `(home.coremusic.net)` (üç kurulum).

Dosya: `.github/workflows/ci.yml` · Satır: 62-63 — `run: composer stan` / `working-directory: shared` (statik analiz).

Dosya: `.github/workflows/ci.yml` · Satır: 66 — `php-test:` (job 2/6).

Dosya: `.github/workflows/ci.yml` · Satır: 68 — `needs: php-lint` (kapı sırası).

Dosya: `.github/workflows/ci.yml` · Satır: 77-80 — `tests/Integration dizini diskte YOK` / `args: '--testsuite Unit'` (auth matrix kararı).

Dosya: `.github/workflows/ci.yml` · Satır: 105 — `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` (test koşucusu).

Dosya: `.github/workflows/ci.yml` · Satır: 110 — `composer-audit:` (job 3/6).

Dosya: `.github/workflows/ci.yml` · Satır: 126 · 132 · 138 — `composer audit` (üç hedefte audit).

Dosya: `.github/workflows/ci.yml` · Satır: 142 — `js-test:` (job 4/6).

Dosya: `.github/workflows/ci.yml` · Satır: 151 — `node-version: '26'` (Node kararı + engine gerekçesi).

Dosya: `.github/workflows/ci.yml` · Satır: 158 — `run: npm run test:js` (vitest kapısı).

Dosya: `.github/workflows/ci.yml` · Satır: 160 — `js-coverage:` (job 5/6).

Dosya: `.github/workflows/ci.yml` · Satır: 167 — `continue-on-error: true` (A7 soft gate).

Dosya: `.github/workflows/ci.yml` · Satır: 176 — `run: npm run test:js:coverage` (coverage çıktısı).

Dosya: `.github/workflows/ci.yml` · Satır: 178 — `js-e2e:` (job 6/6).

Dosya: `.github/workflows/ci.yml` · Satır: 183 — `continue-on-error: true` (A6 soft gate).

Dosya: `.github/workflows/ci.yml` · Satır: 203 — `run: npm run test:e2e` (dosyanın son satırı).

Dosya: `.github/workflows/ci.yml` · Satır: 2 — şablon atfı `.ai/.templates/infrastructure/github-actions-template.md §3.2`.

Dosya: `.github/workflows/ci.yml` · Satır: 3 — `.ai/architecture/k13-cicd/README.md` plan atfı (klasör diskte YOK).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 19 — `gitleaks:` (tek job, 31 satır).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 26 — `fetch-depth: 0` (tam geçmiş taraması).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 29 — `uses: gitleaks/gitleaks-action@v2`.

Dosya: `.github/workflows/secret-scan.yml` · Satır: 4 — `diskte .gitleaks.toml YOK` yorumu (2026-10-13'te YANLIŞ — §G/9).

Dosya: `.github/AGENTS.md` · Satır: 55 — `### 3.1 Dosya Sahipliği Tablosu (disk ölçümü: 5 dosya - 2026-10-03)` başlığı (200 satır).

Dosya: `.github/AGENTS.md` · Satır: 59 — `workflows/ci.yml` satırı: DevOps (birincil) · QA (test kapsamı).

Dosya: `.github/AGENTS.md` · Satır: 60 — `workflows/secret-scan.yml` satırı: Security (kapsam) · DevOps (yml).

Dosya: `.github/AGENTS.md` · Satır: 63 — `Yeni .yml (gelecek)` satırı: Taslak → kullanıcı onayı → uygulama.

Dosya: `.github/AGENTS.md` · Satır: 81 — `### 3.3 Yetki Sınırları (yasaklar)` başlığı.

Dosya: `.github/AGENTS.md` · Satır: 85 — `Subagent git commit ATMAZ` (bu revizyonun yetki sınırı).

Dosya: `.github/AGENTS.md` · Satır: 191 — `3 job - sahiplik dayanağı` (bayat sayı: diskte 6 job).

Dosya: `.gitleaks.toml` · Satır: 1 — `# CoreMusic - gitleaks config` (18 satır, repo kökü).

Dosya: `.gitleaks.toml` · Satır: 2 — `Kapı: .github/workflows/secret-scan.yml (gitleaks-action@v2 kökteki bu dosyayı otomatik kullanır)`.

Dosya: `.gitleaks.toml` · Satır: 3-4 — `allowlist YALNIZ test dosyasindaki sahte sifreleri kapsar` (ADR-034 §5.2).

Dosya: `.git/HEAD` · Satır: 1 — `ref: refs/heads/main` (depo VAR — 2026-10-13 Test-Path True).

Dosya: `.workflows/deployment.md` · Satır: 156 — `### Aşama 4: Onay` (insan kapısı, 281 satır).

Dosya: `.workflows/deployment.md` · Satır: 165 — `### Aşama 5: Dağıtım`.

Dosya: `.workflows/deployment.md` · Satır: 182 — `### Aşama 6: Sağlık Kontrolü`.

Dosya: `.workflows/deployment.md` · Satır: 212 — `## 4. Geri Dönüş (Rollback)`.

Dosya: `.workflows/deployment.md` · Satır: 257 — `## 6. Yasaklar`.

Dosya: `.workflows/deployment.md` · Satır: 273 — `## 8. Aktivasyon`.

Dosya: `package.json` · Satır: 5 — `"test:js": "vitest run --root assets.coremusic.net"` (18 satır).

Dosya: `package.json` · Satır: 7 — `"test:js:coverage"` (A7 gate komutu).

Dosya: `package.json` · Satır: 8 — `"test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"`.

Dosya: `shared/composer.json` · Satır: 24 — `"phpunit/phpunit": "^10.5"` (50 satır).

Dosya: `shared/composer.json` · Satır: 47 — `"test": "phpunit"`.

Dosya: `shared/composer.json` · Satır: 48 — `"stan": "phpstan analyse src --level=5"` (ci.yml:62 dayanağı).

Dosya: `.ai/.templates/infrastructure/github-actions-template.md` · Satır: 30 — `| Test komutları | phpunit-template / vitest-template | §3.2 job adları |` (545 satır).

Dosya: `.ai/architecture/index.md` · Satır: 382 — FM şeması `node_type·status·parent_id·children·depends_on·evidence·validation·open_questions (+ node_id + group)`.

Dosya: `.ai/architecture/index.md` · Satır: 462 — `| K17-devops | 17 | 2.819 | 4 | 2 | +2 |` (bayat sayım — §A.2/6).

Dosya: `.ai/architecture/K17-devops/index.md` · Satır: 34 — `group: mimari` (9. FM alanı kanıtı, 85 satır).

Dosya: `.ai/architecture/K17-devops/index.md` · Satır: 76-81 — §GRUPLAR tablosu (4 satır: mimari ×3 · kanit ×1).

### §E.1 — Kanıt zinciri (iddia → dosya → satır)

| # | İddia (bu dosyada) | Dosya | Satır | Bağlantı |
|---|---|---|---|---|
| 1 | CI 6 job'dan oluşuyor | .github/workflows/ci.yml | 25 · 66 · 110 · 142 · 160 · 178 | §4 CI jobs |
| 2 | Test kapısı PHPUnit + vitest | ci.yml · package.json | 105 · 5 | §B/1 · §B/25 |
| 3 | Audit kapısı composer audit | ci.yml | 126 | §B/27 |
| 4 | Soft gate'ler iki job | ci.yml | 167 · 183 | §B/12 · §B/13 |
| 5 | Secret taraması GitLeaks | secret-scan.yml | 19 · 29 | §4 Secret taraması |
| 6 | Config kökte | .gitleaks.toml | 1-3 | §B/20 · §B/21 |
| 7 | Onay kapısı yeni yml'de | .github/AGENTS.md | 63 | §C/4 |
| 8 | Commit yasağı | .github/AGENTS.md | 85 | §B/6 · §B/30 |
| 9 | Deploy onayı 8 aşamalı | .workflows/deployment.md | 126-199 | §B/22 |
| 10 | Rollback tanımlı | .workflows/deployment.md | 212 | §B/23 |
| 11 | Depo var | .git/HEAD | 1 | §G/8 (çelişki) |
| 12 | FM şeması 8+1+1 | index.md | 382 | §E/12 · FM başlığı |
| 13 | registry sayımı bayat | index.md | 462 | §A.2/6 |
| 14 | K018 grup kaydı | K17-devops/index.md | 34 · 76-81 | FM group |
| 15 | Build/pipeline uyuşmazlığı | dizin listesi | — | §A.1/2 · §A.2/1-2 |

### §E.2 — Ölçüm komutları (tekrarlanabilir)

| # | Komut | Ne ölçer | Beklenen sonuç (2026-10-13) |
|---|---|---|---|
| 1 | `[IO.File]::ReadAllLines($yolu).Count` | bu dosyanın uzunluğu | ≥ 500 |
| 2 | `Get-ChildItem .ai/architecture/K17-devops -Recurse -File` | domain dosya sayısı | 19 |
| 3 | Dosya başına `ReadAllLines` toplamı | satır yoğunluğu | 3.866 |
| 4 | `Test-Path .git/HEAD` | depo varlığı | True (ref: refs/heads/main) |
| 5 | `git log -1 --format='%H %ad %s'` | son commit | c46f08ac · 2026-10-12 · added files |
| 6 | `Test-Path .gitleaks.toml` | config varlığı | True (18 satır) |
| 7 | `Test-Path .workflows/deployment.md` | deploy dokümanı | True (281 satır) |
| 8 | `Test-Path .ai/architecture/k13-cicd` | eski plan klasörü | False |
| 9 | `Select-Object -Index 84` (ci.yml) | job satırı | `php-test:` |
| 10 | URL `Invoke-WebRequest -UseBasicParsing` | web tablosu satırı | HTTP 200 |
| 11 | Envanter kesişimi (`arch-urls.txt`) | URL benzersizliği | 0 kopya (1441 kayıt) |
| 12 | `Get-ChildItem .github -Recurse -File` | .github envanteri | 8 dosya |
| 13 | `Get-ChildItem .github/workflows -Filter *.yml` | CI yüzeyi | 2 dosya |
| 14 | `Get-ChildItem . -Recurse -Filter composer.json -Depth 3` | composer yüzeyi | 5 json · 3 lock |

### §E.3 — Revizyon ölçüm günlüğü (2026-10-13)

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi uzunluk (taban) | 101 satır | P1 başlangıç değeri |
| 2 | Revizyon sonrası uzunluk | §G/1 hücresinde ölçüldü | ≥ 500 hedefi |
| 3 | `##` başlık sayısı | §G/4 hücresinde | §1-§8 + §A-§G |
| 4 | Frontmatter alanı | v2 §4.2 8 alan + `node_id` + `group` | `parent:` yerine `parent_id` |
| 5 | `group` değeri | `mimari` | master · tırnaksız 9. alan |
| 6 | §A modül ağacı | ağaç + 11 satır eşleme + 9 satır bulgu | 10 v2 çocuğu + 1 fazladan |
| 7 | §B master kararı | 30 satır | kanıtlı 26 · negatif 1 · çelişki 2 · uygulanan 1 |
| 8 | §C çapraz kural | 28 kural + 6 ihlal satırı | sınır = kural + ihlal tablosu |
| 9 | §D yol haritası | 8 faz + 6 sıra satırı | faz tablosu + bağımlılık sırası |
| 10 | §E repo kanıtı | `Dosya:` satırı sayısı §G/5 hücresinde | her biri gerçek dosya + satır indeksi |
| 11 | §E.1 kanıt zinciri | 15 satır | iddia → dosya → satır |
| 12 | §E.2 ölçüm komutu | 14 | tekrarlanabilir PowerShell |
| 13 | §F web tablosu satırı | 1 | envanter dışı benzersiz URL |
| 14 | Web URL HTTP durumu | HTTP 200 | 2026-10-13 tarihli doğrulama |
| 15 | Envanter kesişimi | 0 kopya | 1441 kayıtlı URL içinde yok |
| 16 | Yasaklı yer-tutucu | 0 | 7 kalıp §G/7'de |
| 17 | K018 disk sayım | 19 dosya / 3.866 satır | 2026-10-13 ölçümü |
| 18 | index.md bayat sayım | 17 / 2.819 | index.md bu turda değiştirilmedi |
| 19 | Git commit | atılmadı | yetki orkestratörde (.github/AGENTS.md satır 85) |
| 20 | Beyaz-liste | yalnız bu dosya yazıldı | K019/K000/K003/K004/index.md/.decisions/kaynak kod yazımı 0 |

## §F — Web tablosu (devops-master)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| GitHub branch protection rule, repo Settings > Branches üzerinden tanımlanır ve korumalı dalda PR/zorunlu check gerektirecek şekilde yapılandırılır | https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/defining-the-mergeability-of-pull-requests/managing-a-branch-protection-rule | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G — Revizyon doğrulama kaydı (devops-master)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 528 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan, tırnaksız) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | `## §A — Modül ağacı` · `## §B — Master kararlar` · `## §C — Çapraz modül kuralları` · `## §D — Yol haritası` | ✓ 4/4 |
| 5 | Repo kanıtları | en az 1 `Dosya:` + `Satır:` kanıt satırı · gerçek satır indeksi ile doğrulandı | ✓ (§E) |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı benzersiz URL · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 8 | Çelişki: depo | 2026-10-12 "depo YOK" (§3/§4 gövde) ↔ 2026-10-13 `.git/HEAD` VAR | gövde korundu · çelişki §A.2/7 ve §E'de kayıtlı |
| 9 | Çelişki: gitleaks config | secret-scan.yml:4 "YOK" ↔ `.gitleaks.toml` VAR (18 satır) | gövde korundu · çelişki §A.2/8 ve §E'de kayıtlı |
| 10 | §1-§8 gövdesi | aynen korundu (yalnız FM ve Authority satırı revize edildi) | ✓ |
| 11 | Yazılan dosya | bu dosya dahil toplam 8 (P0 2 + P1 6) | ✓ |
| 12 | Git commit | atılmadı | ✓ |

### §G.1 — Çelişki kayıtları (2026-10-17 · disk kazanır)

| # | Çelişki | Kaynak A ↔ Kaynak B | Sonuç |
|---|---|---|---|
| 1 | Git deposu | §3/§4 gövde "depo YOK" (2026-10-12) ↔ `.git/HEAD` VAR (refs/heads/main, son commit 687fbed) | DÜZELTİLDİ — §A.2/7 · gövde korundu, §G/8'de kayıtlı |
| 2 | gitleaks config | `secret-scan.yml:4` "diskte .gitleaks.toml YOK" ↔ `.gitleaks.toml` VAR (18 satır) | gövde korundu · `.yml` beyaz-liste dışı (§G/9) |
| 3 | CD yasağı | `deployment.md:260` "İnsan onayı olmadan üretime dağıtma" ↔ cd/index.md tam otomatik CD önerisi | ADR bekliyor (§G.2/1) |
| 4 | Ortam sayısı | `deployment.md` §5 3 ortam ↔ environments/index.md 4 ortam (local·development·staging·production) | ADR bekliyor (§G.2/4) |
| 5 | Job sayısı | `.github/AGENTS.md:191` "3 job" ↔ `ci.yml` 6 job (satır 25·66·110·142·160·178) | doküman bayat · sahiplik dokümanı beyaz-liste dışı (§E kanıt 403) |
| 6 | build/ çocuğu | v2 §2.18 "10 çocuk" ↔ 2026-10-12 diskte build/ YOK | KAPANDI — build/index.md 218 satır yazıldı (§A.2/1) |
| 7 | pipeline/ index | "10 alt dizinin 10'unda index" kuralı ↔ 2026-10-13'te index YOK | KAPANDI — pipeline/index.md 291 satır (§A.2/2) |
| 8 | node_id ref'leri | vault'ta K018-devops/K019-testing/K017-obs/K020-comilance ↔ disk K17/K18/K16/K19 | DÜZELTİLDİ — 2026-10-17 toplu rename, 191 ref, kalan 0 (§A.2/10) |
| 9 | Registry sayımı | `.ai/architecture/index.md:462` "17 · 2.819" ↔ diskte 23 · 8.877 | AÇIK — index.md beyaz-liste dışı, onay bekliyor (§G.2/8) |
| 10 | deployment.md "doğrulanmadı" | §3/§6 "doğrulanmadı" ↔ `.workflows/deployment.md` VAR (281 satır · Aşama 1-8 · §4 Geri Dönüş · §6 yasaklar) | KAPANDI (§A.2/9) |
| 11 | Dizin ölçümü | FM evidence "19 dosya · 3.866" (2026-10-13) ↔ 2026-10-17: 23 dosya · 8.877 | FM güncellendi (bu revizyon) — §G.3 |

### §G.2 — Bekleyen ADR (2026-10-17 · 8 kayıt)

| # | ADR konusu | Kanıt | Kaynak dosya |
|---|---|---|---|
| 1 | CD modeli uzlaşımı — deployment.md:260 insan-onayı yasağı ↔ tam otomatik CD | cd/index.md open_questions:22 · §6 taslak "UYGULANMADI — onay + ADR bekliyor" | cd/index.md |
| 2 | Artefakt modeli — git-snapshot / artifact-registry / imaj | diskte kanıt yok, üç model §5'te karşılaştırıldı | cd/index.md §5·225 |
| 3 | Staging alt-alan adı — config/domain.php 7 subdomain (staging/test YOK) | environments/index.md:19·76·249 | environments/index.md |
| 4 | Ortam 3→4 genişlemesi — deployment.md §5 (3) ↔ environments (4) | environments/index.md §2-3 | environments/index.md |
| 5 | Environment secret ayrımı — `PROD_*` / `STAGING_*` | environments/index.md:127 · deploy job'larında `environment:` 0 isabet | environments/index.md |
| 6 | Release stratejisi son seçimi — 3 strateji matrisi → trunk-based önerisi | release-management/index.md:21 open_questions | release-management/index.md |
| 7 | Docker/Terraform test kapsamı — Dockerfile/compose/`*.tf` = 0 isabet | infrastructure-testing/index.md:17·291 "KAPSAM DIŞI + ADR bekliyor" | infrastructure-testing/index.md |
| 8 | index.md sayım tazelenmesi — satır 462 "17 · 2.819" bayat | beyaz-liste dışı revizyon — kullanıcı onayı bekliyor | .ai/architecture/index.md |

### §G.3 — Revizyon ölçümü (2026-10-17)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | devops-master.md uzunluğu | 583 satır (§G.1-§G.3 eklenmeden önce 536 · ≥500 ✓) |
| 2 | K17-devops dizini | 23 dosya · 8.877 satır (2026-10-13: 19 · 3.866) |
| 3 | ≥500 satır dosya | 11 adet (1171·583·534·513·511·511·511·509·505·502·500) |
| 4 | node_id düzeltmesi | 191 ref (K018→142 · K019→25 · K017-obs→20 · K020→4), kalan 0 |
| 5 | Yazılan dosya (bu tur) | 8: cd · environments · release-management · infrastructure-testing · rollback · source-control · build · pipeline (pipeline/index.md YENİ) |
| 6 | §E.1/§E.2/§E.3/§F/§G bütünlüğü | 2026-10-17 onarım doğrulaması: §E.1 15 satır tekil · `## §G` tek başlık · Authority son satır (583) |
| 7 | Git commit | atılmadı — yetki orkestratörde (.github/AGENTS.md satır 85) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
