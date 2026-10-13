---
title: "K018-devops — Doğrulama"
node_id: K018-devops/devops-dogrulama
node_type: domain-validation
status: draft
parent_id: K018-devops
children: []
depends_on:
  - K018-devops/devops-master
  - K019-testing
  - K009-security
evidence:
  - ".github/workflows/ci.yml · Satır 25 — php-lint: (6 job'ın ilki, 203 satır, okundu 2026-10-13)"
  - ".github/workflows/secret-scan.yml · Satır 4 — \"diskte .gitleaks.toml YOK\" yorumu (31 satır)"
  - ".gitleaks.toml · Satır 1 — # CoreMusic - gitleaks config (18 satır — yorumu tersliyor)"
  - ".git/HEAD · Satır 1 — ref: refs/heads/main (depo VAR, 2026-10-13 ölçümü)"
  - ".ai/architecture/index.md · Satır 382 — v2 §4.2 frontmatter alanları (8 + node_id + group)"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde ReadAllLines / satır indeksi ile birebir okunarak doğrulandı"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç (§G kontrol 7)"
  - "§1-§6 gövdesi aynen korundu; yalnız FM ve Authority satırı revize edildi"
open_questions:
  - "CI gerçekten geçti mi? GitHub run kanıtı diskte değil (§2/8)"
  - "Git deposu 2026-10-13'te VAR; §2/9 2026-10-12 \"depo yok\" kaydı bayat mı? (§D.3/1)"
  - ".gitleaks.toml diskte mevcut; secret-scan.yml:4 yorumu neden hâlâ YOK diyor? (§D.3/2)"
  - "k13-cicd plan dizini diskte yok; yerine pipeline/ mi geldi? (§D.3/4)"
authority: "Derived — SSOT: v2 spec §4.2 + ADR-097 (draft)"
updated: 2026-10-13
version: 1.1.0
group: kanit
---


# K018-devops — devops-dogrulama.md

## §1 Amaç

K018 iddialarını dosya okuma + glob ölçümüyle doğrular. Kanıt yoksa UNKNOWN / ⚠️ VERIFICATION REQUIRED.

## §2 Ölçütler → Durum

| # | Ölçüt | Kanıt (gerçek yol / satır) | Durum |
|---|---|---|---|
| 1 | Workflow dosyaları mevcut mu? | .github/workflows/ = ci.yml + secret-scan.yml (glob 2026-10-12) | ✅ verified |
| 2 | CI lint kapısı var mı? | ci.yml php-lint job (PHP 8.4, composer stan: phpstan --level=5) | ✅ verified (dosya) |
| 3 | CI test kapısı var mı? | ci.yml php-test job (matrix: shared · auth --testsuite Unit) + js-test (vitest) | ✅ verified (dosya) |
| 4 | Audit kapısı var mı? | ci.yml composer-audit job (shared · auth · home) | ✅ verified (dosya) |
| 5 | E2E kapısı var mı? | ci.yml js-e2e job (Playwright chromium, continue-on-error, A6) | ✅ verified (dosya) |
| 6 | Secret taraması var mı? | secret-scan.yml gitleaks job (gitleaks-action@v2, fetch-depth: 0) | ✅ verified (dosya) |
| 7 | Şablon zinciri tutarlı mı? | ci.yml satır 2 → .ai/.templates/infrastructure/github-actions-template.md §3.2 (varlık doğrulandı) | ✅ verified |
| 8 | CI GERÇEKTEN geçiyor mu? | GitHub run kanıtı YOK (diskte değil); vault §25.2: "geçtiği doğrulanmadı" | ⚠️ verify-required |
| 9 | Git deposu var mı? | .git/HEAD glob = 0 isabet (2026-10-12) + env "Is directory a git repo: no" | ❌ verified NEGATIF (depo yok) |
| 10 | cd/deploy job'ı var mı? | 2 workflow dosyasının tamamı okundu; deploy job'ı YOK | ❌ verified NEGATIF |
| 11 | environment: bloğu var mı? | on: push/pull_request only — environment tanımı YOK | ❌ verified NEGATIF |
| 12 | upload-artifact adımı var mı? | ci.yml'de upload-artifact YOK (cache var) | ❌ verified NEGATIF |
| 13 | rollback/release/infra-test kanıtı? | ölçüm YAPILMADI (dosya yok) | ⚠️ verify-required |

## §3 Doğrulanmayan Yüzeyler (kanıt bekliyor — sonraki tur)

GitHub arayüz kanıtları (run geçmişi · branch protection · environments/secrets) · rollback/ · release-management/ · infrastructure-testing/ · deployment/ (doküman okuması).

## §4 Web-Kanıt Kayıtları

> **Bu turda web araştırması YAPILMADI** (A4 payı disk-öncelikli kanıt planı). Tablo iskelettir.

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| — | (boş — kanıt bekliyor) | — | — | ⏳ |

**Bekleyen web-iddiaları (sonraki tur):**

1. gitleaks-action@v2 güncel sürümü ve config best-practice (artifact-management/secret-scan revizyonu için).
2. GitHub Actions sürüm pinleme güvenlik önerileri (major-pin vs SHA-pin).
3. Node 26 / PHP 8.4 LTS destek durumu (toolchain kararları için).

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru |
|---|---|
| 1 | Son CI run'ı geçti mi? (arayüz kanıtı gerekli) |
| 2 | Git deposu yokluğu bilinçli karar mı? |
| 3 | .gitleaks.toml Security onayıyla eklenecek mi? |
| 4 | k13-cicd planı arşiv mi, taşınacak mı? |
| 5 | Deployment onayı .workflows/deployment.md ile mi yürüyor? (okunacak) |

## §6 Kapanış Kuralı

Domain verified olmak için §2'deki ⚠️ satırları kapatmalı; ❌ satırlar (depo yok, cd yok) ise
**karar gerektirir** — ADR ile ya tescillenir ya kapatılır.

## §A Kapsamlı doğrulama senaryoları

> §4 iskeletinin devamıdır: §4'te bu turda web araştırması yapılmadığı kayıtlıdır; ilk web-iddia satırı §F'dedir.

### §A.1 CI kapısı senaryoları (dosya-yüzeyli)

| # | Senaryo | Adım | Beklenen gözlem | Kapı |
|---|---|---|---|---|
| 1 | Push'ta lint koşar mı? | ci.yml `on: push` satır 10-11 → jobs satır 24 → php-lint satır 25 | php-lint job tanımı 203 satırlık dosyada mevcut | ✅ dosya |
| 2 | Lint testi besler mi? | php-test satır 66 → `needs: php-lint` satır 68 | Bağımlılık zinciri dosyada yazılı | ✅ dosya |
| 3 | PHPUnit koşuyor mu? | php-test job → satır 105 `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` | Test komutu adımı mevcut | ✅ dosya |
| 4 | Audit kapısı koşuyor mu? | composer-audit satır 110 → satır 126 `composer audit` | Audit adımı mevcut | ✅ dosya |
| 5 | JS testi koşuyor mu? | js-test satır 142 → satır 158 `npm run test:js` | Vitest adımı mevcut | ✅ dosya |
| 6 | Coverage ayrık mı? | js-coverage satır 160 → satır 176 `npm run test:js:coverage` | Coverage job'ı ayrı | ✅ dosya |
| 7 | E2E yumuşak mı? | js-e2e satır 178 → satır 183 `continue-on-error: true` → satır 203 | E2E kırıcı değil, raporlayan | ✅ dosya (A6) |
| 8 | CI gerçekten yeşil mi? | GitHub run geçmişi diskte değil | Kanıt yok → UNKNOWN | ⚠️ arayüz |

### §A.2 Secret tarama senaryoları

| # | Senaryo | Adım | Beklenen gözlem | Kapı |
|---|---|---|---|---|
| 1 | Tarama workflow'u var mı? | secret-scan.yml satır 19 `gitleaks:` | Job tanımı mevcut (31 satır) | ✅ dosya |
| 2 | Aksiyon sürümü | satır 29 `gitleaks/gitleaks-action@v2` | Major-pin mevcut; SHA-pin yok | ✅ dosya |
| 3 | Geçmiş taraması | satır 26 `fetch-depth: 0` | Tüm commit geçmişi taranır | ✅ dosya |
| 4 | Config dosyası | .gitleaks.toml satır 1 `# CoreMusic - gitleaks config` | Diskte VAR (18 satır) | ✅ dosya |
| 5 | Yorum-gerçek uyumu | secret-scan.yml satır 4 "diskte .gitleaks.toml YOK" | Yorum yanlış → §D.3/2 | ❌ çelişki |
| 6 | Sahiplik onayı | .github/AGENTS.md satır 60 | Config eklemek ADR/Security onayı ister | ✅ dosya |

### §A.3 Dağıtım ve geri alma senaryoları

| # | Senaryo | Adım | Beklenen gözlem | Kapı |
|---|---|---|---|---|
| 1 | Deploy job'ı workflow'da mı? | ci.yml + secret-scan.yml tamamı okundu | cd/deploy job'ı YOK | ❌ NEGATIF |
| 2 | Onay akışı var mı? | .workflows/deployment.md satır 156 `Aşama 4: Onay` | 8 aşamalı dağıtım akışı dokümanı mevcut | ✅ dosya |
| 3 | Dağıtım aşaması | deployment.md satır 165 `Aşama 5: Dağıtım` | Aşama tanımı mevcut | ✅ dosya |
| 4 | Sağlık kontrolü | deployment.md satır 182 `Aşama 6: Sağlık Kontrolü` | Doğrulama aşaması mevcut | ✅ dosya |
| 5 | Geri dönüş tanımı | deployment.md satır 212 `## 4. Geri Dönüş (Rollback)` | Rollback bölümü mevcut (workflow'a bağlı değil) | ⚠️ kısmi |
| 6 | Dağıtım yasakları | deployment.md satır 257 `## 6. Yasaklar` | Yasak listesi mevcut | ✅ dosya |
| 7 | Rollback işi çalışan workflow | rollback/ dizini + workflow adımı | İkisi de diskte yok | ❌ NEGATIF |

### §A.4 Vault–disk çelişki senaryoları

| # | Senaryo | Vault/Temel iddia | Disk ölçümü (2026-10-13) | Karar |
|---|---|---|---|---|
| 1 | Git deposu | §2/9: `.git/HEAD` 0 isabet (2026-10-12) | `.git/HEAD` VAR → `ref: refs/heads/main` | Disk kazanır |
| 2 | gitleaks config | secret-scan.yml satır 4: YOK | `.gitleaks.toml` VAR, 18 satır | Disk kazanır |
| 3 | CI iş kanıtı | .github/AGENTS.md satır 191: "3 job" | ci.yml 6 job (25 · 66 · 110 · 142 · 160 · 178) | Disk kazanır |
| 4 | K-space kaldırıldı | shared/AGENTS.md §5: K-space 2026-10-12'de kaldırıldı | `.ai/architecture/` altında 26 K* dizini | Disk kazanır |
| 5 | Plan dizini | .github/AGENTS.md §3.4: `k13-cicd/` planı | Test-Path = False | Disk kazanır |
| 6 | K018 envanteri | index.md satır 462: 17 dosya · 2.819 satır | 19 dosya · 4.731 satır (revizyon sonrası) | Disk kazanır |
| 7 | CI envanteri | .github/CONTEXT.md satır 116: "ci.yml - 203 satır, 6 job" | 203 satır · 6 job | Tutarlı |
| 8 | Şablon zinciri | ci.yml satır 2 → github-actions-template.md §3.2 | Şablon 545 satır, diskte var | Tutarlı |

### §A.5 Kapanış senaryoları (§2 ⚠️ ve ❌ satırları için)

| # | Hedef satır | Kapanış şartı | Kanıt türü |
|---|---|---|---|
| 1 | §2/8 (CI geçti mi?) | GitHub Actions run ekranı veya run API yanıtı | arayüz |
| 2 | §2/9 (depo yok) | 2026-10-12 kaydının revizyonu: depo VAR yazıldı | disk (tamamlandı §D.3/1) |
| 3 | §2/10 (deploy job yok) | cd job'ı eklenmesi ya da ADR ile "doküman akışı" kararı | karar |
| 4 | §2/11 (environment yok) | environment: bloğu eklenmesi veya gerekçeli yoksayma | karar |
| 5 | §2/12 (artifact yok) | upload-artifact adımı eklenmesi veya cache'in yeterli kararı | karar |
| 6 | §2/13 (rollback kanıtı) | rollback/ dizininin üretilmesi + ölçüm | dosya |
| 7 | §2/3 (deployment.md okunacak) | Dosyanın 281 satırının okunup §A.3'e taşınması | dosya (kısmen yapıldı) |

### §A.6 Toolchain sürüm senaryoları (disk kanıtı)

| # | Yüzey | Kanıt | Değer | Durum |
|---|---|---|---|---|
| 1 | PHP sürümü | ci.yml satır 22 `PHP_VERSION` | `8.4` | ✅ dosya |
| 2 | PHP kısıtı | shared/composer.json satır 8 | `>=8.4` | ✅ dosya |
| 3 | Node sürümü | ci.yml satır 151 `node-version` | `26` | ✅ dosya |
| 4 | Test scripti (JS) | package.json satır 5 | `vitest run --root assets.coremusic.net` | ✅ dosya |
| 5 | E2E scripti | package.json satır 8 | `playwright test -c assets.coremusic.net/playwright.config.ts` | ✅ dosya |
| 6 | Playwright sürümü | package.json satır 11 · 15 | `^1.63.0` | ✅ dosya |
| 7 | Vitest sürümü | package.json satır 16 | `^5.0.3` | ✅ dosya |
| 8 | PHPUnit sürümü | shared/composer.json satır 24 | `^10.5` | ✅ dosya |
| 9 | PHPStan sürümü | shared/composer.json satır 25 | `^1.10` | ✅ dosya |
| 10 | Composer içeriği | shared/composer.json satır 6 | `2.0.0` | ✅ dosya |
| 11 | Cache anahtarı | ci.yml satır 44 | `hashFiles('**/composer.lock')` | ✅ dosya |
| 12 | İzinler | ci.yml satır 14-15 | `contents: read` | ✅ dosya |
| 13 | Eşzamanlılık | ci.yml satır 17-19 | `cancel-in-progress: true` | ✅ dosya |
| 14 | Aksiyon pinleri | ci.yml satır 30 · 33 · 41 | `@v4` · `@v2` · `@v4` (major-pin, SHA-pin yok) | ✅ dosya |

## §B Ölçüm zinciri

### §B.1 Dosya ölçüm zinciri

| # | Dosya | Yöntem | Sonuç (2026-10-13) |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | `ReadAllLines` | 203 satır · 6 job |
| 2 | `.github/workflows/secret-scan.yml` | `ReadAllLines` | 31 satır · 1 job |
| 3 | `.github/AGENTS.md` | `ReadAllLines` | 200 satır |
| 4 | `.github/CONTEXT.md` | `ReadAllLines` | 276 satır |
| 5 | `.github/WORKFLOW.md` | `ReadAllLines` | 201 satır |
| 6 | `.workflows/deployment.md` | `ReadAllLines` | 281 satır |
| 7 | `.gitleaks.toml` | `ReadAllLines` | 18 satır |
| 8 | `.ai/.templates/infrastructure/github-actions-template.md` | `ReadAllLines` | 545 satır |
| 9 | `package.json` | `ReadAllLines` | 18 satır |
| 10 | `shared/composer.json` | `ReadAllLines` | 50 satır |
| 11 | `.ai/architecture/index.md` | `ReadAllLines` | 562 satır |
| 12 | `C:\www\coremusic.net\.ai\architecture\K018-devops\devops-dogrulama.md` | `ReadAllLines` | §G/1 hücresinde |

### §B.2 Satır indeksi zinciri (kanıt doğrulama yöntemi)

| # | Adım | Çıktı |
|---|---|---|
| 1 | `$L = [IO.File]::ReadAllLines($yol)` | 0 tabanlı satır dizisi |
| 2 | `$L[$n-1]` | n. satırın tam metni (1 tabanlı okuma) |
| 3 | Ekran çıktısındaki bozuk harfler | Konsol kodlama kayması — dosya içeriği UTF-8, bozulma yoktur |
| 4 | Bozuk harf doğrulaması | `[regex]::Matches($t, [char]0xFFFD)` → 0 |
| 5 | Kanıt satırı yazımı | `Dosya: \`yol\` · Satır: n — metin` biçiminde §E'de |

### §B.3 Tekrarlanabilir ölçüm adımları

| # | Adım | Komut özeti | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı | `ReadAllLines($p).Count` | §G/1 hücresindeki değer |
| 2 | Son satır uzunluğu | `ReadAllLines($p)[-1].Length` | 88 |
| 3 | FM alanı sayısı | `^(node_type\|status\|parent_id\|children\|depends_on\|evidence\|validation\|open_questions\|node_id\|group):` | 10 |
| 4 | Yasaklı ibare taraması | yer tutucu ibare · üç nokta dizgesi · U+2026 kodu · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu | 0 |
| 5 | Boş hücre taraması | `(?m)\|[^\S\r\n]*\|` | 0 |
| 6 | URL sayımı | `(?m)https?://` | 1 |
| 7 | Kanıt satırı | `Dosya:` geçen satır sayısı | en az 1 |

### §B.4 Dizin ölçüm zinciri (K018-devops)

| # | Nesne | Yöntem | Sonuç (2026-10-13) |
|---|---|---|---|
| 1 | Alt dizin | `Get-ChildItem -Directory -Recurse` | 10 (kök + 10 = 11) |
| 2 | Dosya | `Get-ChildItem -File -Recurse -Filter *.md` | 19 |
| 3 | Satır toplamı | dosya dosya `ReadAllLines` | 4.731 |
| 4 | Revizyon öncesi | devops-master FM evidence kaydı | 3.866 |
| 5 | Fark | 528−101 (master) + 511−73 (bağımlılıklar) | +865 · 3.866+865 = 4.731 ✓ |

## §C Regresyon eşiği ve kapılar

### §C.1 Eşikler

| # | Eşik | Değer | Aşımında |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | DUR — revizyon geçersiz |
| 2 | Son satır | 88 karakter, birebir Authority satırı | DUR |
| 3 | FM alanı | v2 §4.2 8 alan + `node_id` + `group` | DUR |
| 4 | Zorunlu bölüm | §A · §B · §C · §D (5/5 başlık) | DUR |
| 5 | Repo kanıtı | en az 1 `Dosya: <ayrac> · Satır: n` | DUR |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı · HTTP 200 | DUR |
| 7 | Yasaklı ibare | 0 | DUR |
| 8 | Çelişki kaydı | §D.3'e yazılır, gövde değiştirilmez | Uyarı |

### §C.2 Kapı sırası (bu dosyanın revizyon kapısı)

| # | Kapı | Sorumlu | Çıktı |
|---|---|---|---|
| 1 | Keşif: hedef dosya + komşu dosyalar okunur | embedded-engineer | kanıt listesi |
| 2 | FM revizyonu (8 + node_id + group) | embedded-engineer | yeni FM |
| 3 | Gövde §1-§6 aynen korunur | embedded-engineer | değişmez metin |
| 4 | §A-§D üretilir | embedded-engineer | yeni bölümler |
| 5 | §E kanıt zinciri satır numaraları tek tek doğrulanır | embedded-engineer | Dosya/Satır satırları |
| 6 | §F web tablosu + envanter farkı | embedded-engineer | 1 URL |
| 7 | §G tarama | embedded-engineer | geç/blok |
| 8 | Commit | orkestratör | subagent ATMAZ |

### §C.3 Regresyon kataloğu

| # | Regresyon | Tetikleyici | Koruma |
|---|---|---|---|
| 1 | Gövdenin sessizce değişmesi | yeniden yazım | §G/8 kontrolü |
| 2 | Satır numarasının uydurulması | dosya değişimi | §B.2 tek tek okuma |
| 3 | URL envantere çakışması | tekrarlı iddia | §G/6 envanter farkı |
| 4 | FM alanının düşmesi | şablon değişimi | §G/3 |
| 5 | Boş hücre / yer tutucu | hızlı yazım | §G/7 |
| 6 | §2 satırlarının eskimesi | disk değişimi | §D.3 çelişki kaydı |

## §D Referans değerler

### §D.1 Sayısal referanslar (2026-10-13 ölçümü)

| # | Nesne | Değer |
|---|---|---|
| 1 | ci.yml | 203 satır · 6 job (25 · 66 · 110 · 142 · 160 · 178) |
| 2 | secret-scan.yml | 31 satır · 1 job (19) |
| 3 | workflow toplamı | 234 satır · 2 dosya |
| 4 | .gitleaks.toml | 18 satır |
| 5 | .workflows/deployment.md | 281 satır · 8 aşama |
| 6 | github-actions-template.md | 545 satır |
| 7 | package.json | 18 satır · 4 script (5 · 6 · 7 · 8) |
| 8 | shared/composer.json | 50 satır · test (47) + stan (48) |
| 9 | K018-devops | 11 dizin · 19 dosya · 4.731 satır |
| 10 | index.md K018 satırı | 17 dosya · 2.819 satır (bayat — §D.3/5) |
| 11 | envanter URL sayısı | 1.441 |
| 12 | K-space dizin sayısı | 26 |

### §D.2 Durum kodu referansı

| Kod | Anlam | §2 karşılığı |
|---|---|---|
| ✅ verified | dosyada okunarak doğrulandı | 1-7 |
| ⚠️ verify-required | kanıt diskte değil, tur bekliyor | 8 · 13 |
| ❌ verified NEGATIF | ölçülüp yokluğu doğrulandı | 9-12 |
| UNKNOWN | hiç ölçülmedi | — |
| Disk kazanır | vault ile disk çelişirse disk yazılır | §A.4 |

### §D.3 Bilinen çelişkiler ve bayat kayıtlar (gövde değiştirilmez, burada kayıt)

| # | Çelişki | Kaynak | Disk gerçeği (2026-10-13) |
|---|---|---|---|
| 1 | "depo yok" | §2/9 (2026-10-12 ölçümü) | `.git/HEAD` VAR → `ref: refs/heads/main` |
| 2 | "gitleaks config YOK" | secret-scan.yml satır 4 | `.gitleaks.toml` VAR · 18 satır |
| 3 | "3 job" | .github/AGENTS.md satır 191 | 6 job |
| 4 | "k13-cicd planı" | .github/AGENTS.md §3.4 | dizin Test-Path = False |
| 5 | "17 dosya · 2.819 satır" | index.md satır 462 | 19 dosya · 4.731 satır (index.md salt-okunur, düzeltilmedi) |
| 6 | "K-space kaldırıldı" | shared/AGENTS.md §5 | 26 K* dizini diskte |

### §D.4 Kapsam dışı referanslar (bu turda okunmadı)

| # | Yüzey | Neden okunmadı |
|---|---|---|
| 1 | GitHub Actions run geçmişi | arayüz kanıtı, diskte değil |
| 2 | Branch protection ayarları | arayüz kanıtı |
| 3 | `rollback/` `release-management/` `infrastructure-testing/` | dizinler diskte yok |
| 4 | `.github/ISSUE_TEMPLATE/` | K018 doğrulama kapsamı dışında |
| 5 | Üçüncü parti aksiyon iç kaynakları | vendor karantinası |

### §D.5 Sonraki tur referans planı

| # | İddia | Kanıt türü |
|---|---|---|
| 1 | Son CI run'ı yeşil | GitHub run sayfası |
| 2 | gitleaks-action@v2 sürümü | resmi sürüm sayfası |
| 3 | Node 26 / PHP 8.4 LTS durumu | resmi yayın takvimi |
| 4 | rollback/ planı | vault kararı + ADR |

## §E Repo kanıtları (devops-dogrulama)

1. Dosya: `.github/workflows/ci.yml` · Satır: 2 — `# Şablon (Guardrail #16): .ai/.templates/infrastructure/github-actions-template.md §3.2`

2. Dosya: `.github/workflows/ci.yml` · Satır: 10 — `on:`

3. Dosya: `.github/workflows/ci.yml` · Satır: 17 — `concurrency:`

4. Dosya: `.github/workflows/ci.yml` · Satır: 21 — `env:`

5. Dosya: `.github/workflows/ci.yml` · Satır: 25 — `  php-lint:`

6. Dosya: `.github/workflows/ci.yml` · Satır: 41 — `        uses: actions/cache@v4`

7. Dosya: `.github/workflows/ci.yml` · Satır: 66 — `  php-test:`

8. Dosya: `.github/workflows/ci.yml` · Satır: 68 — `    needs: php-lint`

9. Dosya: `.github/workflows/ci.yml` · Satır: 93 — `        uses: actions/cache@v4`

10. Dosya: `.github/workflows/ci.yml` · Satır: 105 — `        run: vendor/bin/phpunit "$PHPUNIT_ARGS"`

11. Dosya: `.github/workflows/ci.yml` · Satır: 110 — `  composer-audit:`

12. Dosya: `.github/workflows/ci.yml` · Satır: 126 — `          composer audit`

13. Dosya: `.github/workflows/ci.yml` · Satır: 142 — `  js-test:`

14. Dosya: `.github/workflows/ci.yml` · Satır: 151 — `          node-version: '26'`

15. Dosya: `.github/workflows/ci.yml` · Satır: 158 — `        run: npm run test:js`

16. Dosya: `.github/workflows/ci.yml` · Satır: 160 — `  js-coverage:`

17. Dosya: `.github/workflows/ci.yml` · Satır: 167 — `    continue-on-error: true`

18. Dosya: `.github/workflows/ci.yml` · Satır: 178 — `  js-e2e:`

19. Dosya: `.github/workflows/ci.yml` · Satır: 183 — `    continue-on-error: true`

20. Dosya: `.github/workflows/ci.yml` · Satır: 203 — `        run: npm run test:e2e`

21. Dosya: `.github/workflows/secret-scan.yml` · Satır: 4 — `# Not: diskte .gitleaks.toml YOK (yalnizca security-scanning.md dokuman ornegi) -> varsayilan gitleaks config.`

22. Dosya: `.github/workflows/secret-scan.yml` · Satır: 19 — `  gitleaks:`

23. Dosya: `.github/workflows/secret-scan.yml` · Satır: 26 — `          fetch-depth: 0`

24. Dosya: `.github/workflows/secret-scan.yml` · Satır: 29 — `          uses: gitleaks/gitleaks-action@v2`

25. Dosya: `.gitleaks.toml` · Satır: 1 — `# CoreMusic - gitleaks config`

26. Dosya: `.github/AGENTS.md` · Satır: 59 — `| workflows/ci.yml | DevOps Engineer (birincil) · QA Engineer (test kapsamı) | Düzenleme yalnız onayla; job silme/ekleme yasak |`

27. Dosya: `.github/AGENTS.md` · Satır: 60 — `| workflows/secret-scan.yml | Security Engineer (kapsam) · DevOps Engineer (yml) | Config (.gitleaks.toml) eklemek ADR/Security onayı ister |`

28. Dosya: `.github/AGENTS.md` · Satır: 63 — `| Yeni .yml (gelecek) | DevOps Engineer | Taslak → kullanıcı onayı → uygulama | CI domaini |`

29. Dosya: `.github/AGENTS.md` · Satır: 85 — `| 1 | Subagent git commit ATMAZ (kök AGENTS.md §7/§8) | Yetkisiz commit → revert + log |`

30. Dosya: `.github/AGENTS.md` · Satır: 191 — `| CI iş kanıtı | .github/workflows/ci.yml | 3 job - sahiplik dayanağı |`

31. Dosya: `.github/CONTEXT.md` · Satır: 116 — `### 3.2 CI Pipeline Yapısı (ci.yml - 203 satır, 6 job)`

32. Dosya: `.github/CONTEXT.md` · Satır: 166 — `Disk kazanır - CLAUDE.md dokunulmaz; düzeltme vault-sync'e rapor edilir`

33. Dosya: `.github/WORKFLOW.md` · Satır: 91 — `### 3.3 CI Fail Geri Dönüş Yolu (özel durum)`

34. Dosya: `.workflows/deployment.md` · Satır: 156 — `### Aşama 4: Onay`

35. Dosya: `.workflows/deployment.md` · Satır: 165 — `### Aşama 5: Dağıtım`

36. Dosya: `.workflows/deployment.md` · Satır: 182 — `### Aşama 6: Sağlık Kontrolü`

37. Dosya: `.workflows/deployment.md` · Satır: 212 — `## 4. Geri Dönüş (Rollback)`

38. Dosya: `.workflows/deployment.md` · Satır: 257 — `## 6. Yasaklar`

39. Dosya: `.workflows/deployment.md` · Satır: 273 — `## 8. Aktivasyon`

40. Dosya: `package.json` · Satır: 5 — `    "test:js": "vitest run --root assets.coremusic.net",`

41. Dosya: `package.json` · Satır: 8 — `    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"`

42. Dosya: `shared/composer.json` · Satır: 47 — `        "test": "phpunit",`

43. Dosya: `shared/composer.json` · Satır: 48 — `        "stan": "phpstan analyse src --level=5"`

44. Dosya: `.ai/.templates/infrastructure/github-actions-template.md` · Satır: 30 — `| Test komutları | \`phpunit-template\` / \`vitest-template\` | §3.2 job adları |`

45. Dosya: `.ai/architecture/index.md` · Satır: 382 — `node_type · status · parent_id · children · depends_on · evidence · validation · open_questions (+ node_id + group)`

46. Dosya: `.ai/architecture/index.md` · Satır: 462 — `| K018-devops | 17 | 2.819 | 4 | 2 | +2 |`

47. Dosya: `.git/HEAD` · Satır: 1 — `ref: refs/heads/main`

48. Dosya: `C:\www\coremusic.net\.ai\architecture\K018-devops\devops-dogrulama.md` · Satır: 500 — `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode`

49. Dosya: `.github/workflows/ci.yml` · Satır: 14 — `permissions:`

50. Dosya: `.github/workflows/ci.yml` · Satır: 22 — `  PHP_VERSION: '8.4'`

51. Dosya: `.github/workflows/ci.yml` · Satır: 28 — `        timeout-minutes: 15`

52. Dosya: `.github/workflows/ci.yml` · Satır: 44 — `          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}`

53. Dosya: `shared/composer.json` · Satır: 8 — `        "php": ">=8.4",`

54. Dosya: `shared/composer.json` · Satır: 24 — `        "phpunit/phpunit": "^10.5",`

55. Dosya: `package.json` · Satır: 11 — `    "@playwright/test": "^1.63.0",`

56. Dosya: `package.json` · Satır: 16 — `    "vitest": "^5.0.3"`

57. Dosya: `.github/CONTEXT.md` · Satır: 132 — `### 3.3 Secret Scan (secret-scan.yml - 31 satır, 1 job)`

58. Dosya: `.github/CONTEXT.md` · Satır: 167 — `Disk kazanır - bu dosya yeniden yazıldı`

59. Dosya: `.github/AGENTS.md` · Satır: 55 — `### 3.1 Dosya Sahipliği Tablosu (disk ölçümü: 5 dosya - 2026-10-03)`

60. Dosya: `.github/AGENTS.md` · Satır: 81 — `### 3.3 Yetki Sınırları (yasaklar)`

61. Dosya: `.workflows/deployment.md` · Satır: 281 — `*Mode: Red Team · Truth Mode · Human Mode*`

62. Dosya: `.gitleaks.toml` · Satır: 18 — `]`

### §E.1 — Kanıt zinciri (iddia → dosya → satır)

| # | İddia | Dosya | Satır | Sonuç |
|---|---|---|---|---|
| 1 | 6 CI job'ı var | ci.yml | 25 · 66 · 110 · 142 · 160 · 178 | doğrulandı |
| 2 | E2E yumuşak kapıdır | ci.yml | 183 | doğrulandı |
| 3 | Tarama tüm geçmişi tarar | secret-scan.yml | 26 | doğrulandı |
| 4 | Config diskte var | .gitleaks.toml | 1 | doğrulandı (yorumu tersliyor) |
| 5 | yml sahibi DevOps | .github/AGENTS.md | 59 | doğrulandı |
| 6 | Deploy onayı dokümanda | deployment.md | 156 | doğrulandı |
| 7 | Rollback bölümü var | deployment.md | 212 | doğrulandı (workflow'a bağlı değil) |
| 8 | JS scriptleri package.json'da | package.json | 5 · 8 | doğrulandı |
| 9 | FM şeması index.md §382 | index.md | 382 | doğrulandı |
| 10 | Depo VAR | .git/HEAD | 1 | doğrulandı (§2/9'u tersliyor) |

### §E.2 — Revizyon ölçüm günlüğü

| # | Adım | Sonuç |
|---|---|---|
| 1 | Başlangıç uzunluğu | 71 satır |
| 2 | FM revizyonu | 8 alan + node_id + group (10 anahtar) |
| 3 | Gövde §1-§6 | aynen korundu |
| 4 | §A-§E eklendi | senaryo + zincir + kanıt |
| 5 | §F web satırı | 1 URL · envanter dışı |
| 6 | §G tarama | §G tablosunda |

## §F Web tablosu (devops-dogrulama)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Azure Pipelines'da job'lar aşama (phase) tanımlanır ve her aşama koşu, gate ve artifact kurallarını taşır — ci.yml'deki job/needs düzeninin endüstri karşılığı | https://learn.microsoft.com/en-us/azure/devops/pipelines/process/phases?view=azure-devops | 2026-10-13 | HTTP 200 · doğrulandı · envanter (1441) dışı |

## §G Revizyon doğrulama kaydı (devops-dogrulama)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 500 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir, 88 karakter | ✓ 88 |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan, tırnaksız) | ✓ 10 anahtar |
| 4 | Zorunlu bölümler | `## §A Kapsamlı doğrulama senaryoları` · `## §B Ölçüm zinciri` · `## §C Regresyon eşiği ve kapılar` · `## §D Referans değerler` | ✓ 4/4 |
| 5 | Repo kanıtları | en az 1 `Dosya:` + `Satır:` kanıt satırı | ✓ (§E) |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı · HTTP 200 | ✓ (§F) |
| 7 | Yasaklı yer-tutucu | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 8 | §1-§6 gövdesi | aynen korundu (yalnız FM ve Authority satırı revize) | ✓ |
| 9 | Çelişki kaydı | depo · gitleaks config · K-space "kaldırıldı" iddiası | ✓ §D.3 |
| 10 | Yazılan dosya | bu dosya dahil 8 (P0 2 + P1 6) | ✓ 8 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode