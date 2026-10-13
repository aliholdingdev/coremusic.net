---
title: "K018-devops — Master (Domain Anayasa)"
node_id: K018-devops/devops-master
node_type: domain-master
status: draft
parent_id: K018-devops
children:
  - K018-devops/index
  - K018-devops/devops-bagimliliklar
  - K018-devops/devops-dogrulama
  - K018-devops/source-control
  - K018-devops/build
  - K018-devops/ci
  - K018-devops/cd
  - K018-devops/environments
  - K018-devops/deployment
  - K018-devops/rollback
  - K018-devops/release-management
  - K018-devops/artifact-management
  - K018-devops/infrastructure-testing
depends_on:
  - K019-testing
  - K009-security
  - K017-observability
evidence:
  - ".github/workflows/ci.yml · Satır 25 — php-lint: (6 job'ın ilki, 203 satır, okundu 2026-10-13)"
  - ".github/workflows/secret-scan.yml · Satır 19 — gitleaks: job (31 satır, okundu 2026-10-13)"
  - ".github/AGENTS.md · Satır 85 — Subagent git commit ATMAZ (yetki sınırı, 200 satır)"
  - ".git/HEAD · Satır 1 — ref: refs/heads/main (depo VAR — 2026-10-13 ölçümü, 2026-10-12 iddiasını tersliyor)"
  - ".gitleaks.toml · Satır 1 — # CoreMusic - gitleaks config (18 satır — secret-scan.yml:4 yorumunu tersliyor)"
  - "K018-devops dizin ölçümü (2026-10-13): 11 dizin · 19 dosya · 3.866 satır"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde ReadAllLines / satır indeksi ile birebir okunarak doğrulandı"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç (§G kontrol 7)"
  - "§1-§8 gövdesi aynen korundu; yalnız FM ve Authority satırı revize edildi"
open_questions:
  - "CI gerçekten geçti mi? GitHub run kanıtı diskte değil (§7/1)"
  - "Git deposu 2026-10-13'te bulundu; 2026-10-12 'depo YOK' kaydı bayat mı, yoksa depo o gün mü açıldı? (§G/8)"
  - ".gitleaks.toml diskte mevcut; secret-scan.yml:4 yorumu neden hâlâ 'YOK' diyor? (§G/9)"
  - "build/ v2 çocuğu diskte yok; karşılığı pipeline/ mı? (§A.1/2)"
authority: "Derived — SSOT: v2 spec §2.18 + ADR-097 (draft)"
updated: 2026-10-13
version: 1.1.0
group: mimari
---


# K018-devops — devops-master.md

## §1 Rol (ELI15)

K018-devops, kodun **üretilip yayılma yoludur**: depoda ne olur, push'ta ne çalışır, hangi testler
kapı olur, hangi ortama çıkar, nasıl geri alınır. Testleri **yazmaz** (K019), yalnız **koşar**.

## §2 Kapsam / Sınır (ne DEĞİL)

| Kapsam (bu domain) | Kapsam DIŞI |
|---|---|
| CI kapısı: lint/test/audit koşumu | Testlerin tasarımı ve içeriği → **K019-testing** (ci.yml php-test/js-test = K018'in K019'la entegrasyonu) |
| CD, ortam, deployment, rollback | Sistem logu/metrik/aler → **K017-observability** (uyarı K017, aksiyon K018) |
| Secret taraması (GitLeaks) | Auth/encryption politikası → **K009-security** (secret-scan.yml = iş birliği, sahiplik §5'te Security+DevOps) |
| Workflow dosyaları sahipliği (*.yml) | Ajan boot/hooks → **K016-AI** (hook'lar harness'da, workflow'lar GitHub'da) |

**Sahiplik kaydı (.github/AGENTS.md §3.1, 2026-10-03):** ci.yml = DevOps (birincil) + QA (test kapsamı) ·
secret-scan.yml = Security (kapsam) + DevOps (yml) · yeni .yml = taslak → kullanıcı onayı → uygulama.

## §3 Kademe-2 Çocuk Tablosu (v2 §2.18 — birebir, 10 çocuk)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu |
|---|---|---|---|
| 1 | source-control/ | Depo, branch, commit kuralları | ⚠️ depo YOK (.git/HEAD 0 isabet) |
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
| Secret taraması | secret-scan.yml: gitleaks job — gitleaks/gitleaks-action@v2, fetch-depth: 0; yorum: .gitleaks.toml YOK (varsayılan config) | ✅ IMPLEMENTED (dosya) |
| Şablon zinciri | .ai/.templates/infrastructure/github-actions-template.md §3.2 — varlık doğrulandı (2026-10-12); ci.yml satır 2 şablona atıf yapıyor | ✅ |
| Sahiplik/yetki dokümanı | .github/AGENTS.md §3.1 (5 dosya sahiplik tablosu — ölçüm 2026-10-03) · §3.3 yasaklar (subagent commit YOK, onaysız yml değişikliği YOK) | ✅ (doküman) |
| .github envanteri | 8 dosya: AGENTS.md · CONTEXT.md · CLAUDE.md · WORKFLOW.md · workflows/ci.yml · workflows/secret-scan.yml · ISSUE_TEMPLATE/CLAUDE.md · ISSUE_TEMPLATE/01-bug-report.md (glob 2026-10-12) | ✅ |
| Git deposu | .git/HEAD glob = **0 isabet** (2026-10-12) + ortam bildirimi "Is directory a git repo: no" → **depo YOK**; vault commit kayıtları (.ai/AGENTS.md §25.5) eski dönem arşividir | ⚠️ KRİTİK BULGU |
| CI geçiş durumu | .ai/AGENTS.md §25.2: "workflow dosyaları mevcut, çalışma durumu doğrulanmadı — CI'nin geçtiği doğrulanmadı" | ⚠️ doğrulanmadı |
| Eski plan referansı | ci.yml satır 3: .ai/architecture/k13-cicd/README.md — bu klasör V3 registry'de YOK (glob .ai/architecture/**) | ⚠️ tarihsel referans |

## §5 Bağımlılık Özeti (etiketli — detay: devops-bagimliliklar.md)

| Yön | Hedef | Etiket | Gerekçe |
|---|---|---|---|
| depends_on | K019-testing | kanıtlı (ci.yml) | CI test job'ları K019 yetkinliğini koşar |
| depends_on | K009-security | kanıtlı (secret-scan.yml) | GitLeaks kapısı güvenlik domaininin iş birliği |
| used_by | tüm üretim domainleri | planlanan | her kod değişimi bu yoldan geçer |
| used_by | K017-observability | planlanan | alert → pipeline aksiyonu (sınır: uyarı K017, aksiyon K018) |

## §6 Doğrulama Özeti (detay: devops-dogrulama.md)

- ✅ **Doğrulandı (dosya okuma):** ci/ (6 job) · build/ (composer/npm adımları) · secret tarama iş birliği.
- 🔶 **Kısmi/negatif:** environments (blok yok) · artifact-management (upload yok) · deployment (doküman referansı doğrulanmadı).
- ⚠️ **Uyaran:** source-control (depo yok!) · cd/rollback/release/infrastructure-testing (kanıt yok).
- **En kritik açık:** CI dosyaları var ama **CI'nin geçtiğine dair kanıt yok** (vault uyarısıyla tutarlı).

## §7 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | CI gerçekten çalışıyor mu, son run geçti mi? (GitHub arayüzü/branch koruması diskte değil) | ci/ |
| 2 | Git deposu bilinçli mi yok? (yedek/taşınma dönemi?) — K-Space V3 kararı gerekli | source-control/ |
| 3 | cd/ ve deployment/ için hedef model ne? (manual deploy mu, otomatik CD mi) | cd/ · deployment/ |
| 4 | k13-cicd planı V3 registry'sine taşınacak mı, yoksa arşiv mi? | ci/ |
| 5 | release/artifact stratejisi tanımlı mı? | release-management/ · artifact-management/ |
| 6 | infrastructure-testing kapsamı (Docker? altyapı as-code?)? | infrastructure-testing/ |

## §8 Kaynaklar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.18 |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme |
| Registry | .ai/architecture/index.md §5 satır 18 |
| Disk (2026-10-12) | .github/workflows/ci.yml · secret-scan.yml · .github/ envanteri · .git/HEAD (0 isabet) |
| Sahiplik dokümanı | .github/AGENTS.md (2026-10-03) · .ai/AGENTS.md §5/§16/§25.2 |

## §A — Modül ağacı

**Ölçüm (2026-10-13, [IO.File]::ReadAllLines):** K018-devops kökü 4 dosya / 330 satır ·
alt dizinler 15 dosya / 3.536 satır · **toplam 19 dosya / 3.866 satır** · ≥ 500 satır dosya 6 adet (bu revizyon öncesi).

```text
K018-devops/                                19 dosya · 3.866 satır
├── index.md                                85 satır · domain indeksi (group: mimari)
├── devops-master.md                        revizyon öncesi 101 satır (bu dosya)
├── devops-bagimliliklar.md                 revizyon öncesi 73 satır
├── devops-dogrulama.md                     revizyon öncesi 71 satır
├── artifact-management/                    2 dosya · 562 satır · index VAR
│   ├── index.md                            51 satır
│   └── konteyner-imaj-yonetimi.md          511 satır · ≥ 500
├── cd/                                     1 dosya · 50 satır · index VAR
│   └── index.md                            50 satır
├── ci/                                     2 dosya · 561 satır · index VAR
│   ├── index.md                            52 satır
│   └── secret-yonetimi-ve-tarama.md        509 satır · ≥ 500
├── deployment/                             3 dosya · 1.086 satır · index VAR
│   ├── index.md                            50 satır
│   ├── rollback-ve-geri-alma.md            534 satır · ≥ 500 (P0, bu tur)
│   └── yayilim-stratejileri.md             502 satır · ≥ 500
├── environments/                           1 dosya · 50 satır · index VAR
│   └── index.md                            50 satır
├── infrastructure-testing/                 1 dosya · 50 satır · index VAR
│   └── index.md                            50 satır
├── pipeline/                               2 dosya · 1.024 satır · index YOK
│   ├── cicd-asamalari.md                   511 satır · ≥ 500
│   └── pipeline-hizlandirma-ve-cache.md    513 satır · ≥ 500 (P0, bu tur)
├── release-management/                     1 dosya · 50 satır · index VAR
│   └── index.md                            50 satır
├── rollback/                               1 dosya · 51 satır · index VAR
│   └── index.md                            51 satır
└── source-control/                         1 dosya · 52 satır · index VAR
    └── index.md                            52 satır
```

### §A.1 — v2 §2.18 çocukları ile disk eşlemesi

| # | v2 §2.18 çocuğu | Diskte karşılığı | Dosya | Satır | Eşleşme |
|---|---|---|---|---|---|
| 1 | source-control/ | source-control/ | 1 | 52 | birebir (index tek dosya) |
| 2 | build/ | diskte build/ YOK → pipeline/ | 2 | 1.024 | **eşleşmiyor** (aşağıda §A.2/1) |
| 3 | ci/ | ci/ | 2 | 561 | birebir |
| 4 | cd/ | cd/ | 1 | 50 | birebir (index tek dosya) |
| 5 | environments/ | environments/ | 1 | 50 | birebir (index tek dosya) |
| 6 | deployment/ | deployment/ | 3 | 1.086 | birebir |
| 7 | rollback/ | rollback/ | 1 | 51 | birebir (index tek dosya) |
| 8 | release-management/ | release-management/ | 1 | 50 | birebir (index tek dosya) |
| 9 | artifact-management/ | artifact-management/ | 2 | 562 | birebir |
| 10 | infrastructure-testing/ | infrastructure-testing/ | 1 | 50 | birebir (index tek dosya) |
| 11 | v2'de YOK | pipeline/ | 2 | 1.024 | **fazladan dizin** (aşağıda §A.2/2) |

### §A.2 — Disk bulguları ve açıklar

| # | Bulgu | Kanıt (2026-10-13) | Sonuç |
|---|---|---|---|
| 1 | build/ dizini diskte yok | 10 dizin listesi içinde build/ yok; karşılığı olabilecek pipeline/ var | v2 çocuk listesi ile disk arasında 1 eksik |
| 2 | pipeline/ dizini v2 listesinde yok | 2 dosya / 1.024 satır, index YOK | fazladan dizin + eksik index |
| 3 | 9 alt dizinin 8'inde index.md var | pipeline/ hariç hepsi | index eksikliği tek bu dizinde |
| 4 | Kök 3 P1 dosyası < 500 satır | 101 · 73 · 71 (revizyon öncesi) | bu revizyonla 3'ü de ≥ 500 |
| 5 | ≥ 500 satır dosya 6 adet | 511 · 509 · 534 · 502 · 511 · 513 | P0 öncesi 4 idi, P0 ile 6 |
| 6 | index.md sayımı bayat | index.md satır 462: `17 · 2.819` → diskte 19 · 3.866 | index.md bu turda değiştirilmedi |
| 7 | .git/HEAD artık VAR | `ref: refs/heads/main` (Test-Path True) | §3/§4'teki "depo YOK" kaydı bayatlandı |
| 8 | .gitleaks.toml artık VAR | 18 satır, satır 1 başlık | secret-scan.yml:4 yorumu bayatlandı |
| 9 | .workflows/deployment.md VAR | 281 satır (§3/§4: Onay · §4: Geri Dönüş) | §3/6 "doğrulanmadı" kaydı kapandı |

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
| 19 | K020-comilance/k10 licence kanıtı composer-audit'ten gelir | ci.yml satır 110 |
| 20 | Deployment onayı insan kapısıdır (Aşama 4) | .workflows/deployment.md satır 156 |
| 21 | Dağıtım sonrası sağlık kontrolü zorunlu (Aşama 6) | .workflows/deployment.md satır 182 |
| 22 | Hata durumunda rollback §4'e gidilir | .workflows/deployment.md satır 212 · 247 |
| 23 | workflow yorumlarındaki ölü plan linki temizlenir | ci.yml satır 3 (k13-cicd YOK) |
| 24 | Bayat yorum ile disk çelişince disk kazanır | secret-scan.yml satır 4 · .gitleaks.toml |
| 25 | K018 dokümanları yalnız .ai/architecture/K018-devops/** yazılır | bu tur beyaz-liste |
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

Dosya: `.ai/architecture/index.md` · Satır: 462 — `| K018-devops | 17 | 2.819 | 4 | 2 | +2 |` (bayat sayım — §A.2/6).

Dosya: `.ai/architecture/K018-devops/index.md` · Satır: 34 — `group: mimari` (9. FM alanı kanıtı, 85 satır).

Dosya: `.ai/architecture/K018-devops/index.md` · Satır: 76-81 — §GRUPLAR tablosu (4 satır: mimari ×3 · kanit ×1).

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
| 14 | K018 grup kaydı | K018-devops/index.md | 34 · 76-81 | FM group |
| 15 | Build/pipeline uyuşmazlığı | dizin listesi | — | §A.1/2 · §A.2/1-2 |

### §E.2 — Ölçüm komutları (tekrarlanabilir)

| # | Komut | Ne ölçer | Beklenen sonuç (2026-10-13) |
|---|---|---|---|
| 1 | `[IO.File]::ReadAllLines($yolu).Count` | bu dosyanın uzunluğu | ≥ 500 |
| 2 | `Get-ChildItem .ai/architecture/K018-devops -Recurse -File` | domain dosya sayısı | 19 |
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

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
