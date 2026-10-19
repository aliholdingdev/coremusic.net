---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
evidence:
  - .github/workflows/ci.yml:14
  - .github/workflows/ci.yml:17
  - .github/workflows/secret-scan.yml:11
validation:
  - "2026-10-13: 4 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 12 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - T5 [UNKNOWN] — `secret-scan.yml` tetikleyicisi yalnız `push`/`pull_request` mi: L7–L9 okundu, ek event yok; yine de `UNKNOWN` bırakılmaz, L7–L9 kanıttır.
  - T2 [UNKNOWN] — kalite kapılarındaki eşikler (coverage yüzdesi, lint seviyesi) dosyalarda tanımlı değil.
group: mimari
---

# CI-Ve-Kalite-Kapisi Anayasası — Workflow Yapısı, Kalite Kapıları, Artifact, Determinizm

## §0 ELI15

Bu dosya CI'nın **ilkelerini** yazar: neyin kapı olduğunu, neyin yalnız
gösterildiğini, artifact/caching kurallarını, secret taramasının yerini ve
determinizm zorunluluğunu. Somut iş/matris satırları `-surucu.md` içindedir;
gerçek workflow blokları `-ornekler.md` içindedir. Burada **kural** vardır,
kod alıntısı sınırlıdır.

## §1 Workflow yapısı ilkeleri

### §1.1 Temel ilkeler

| # | İlke | Neden | Disk kanıtı |
|---|---|---|---|
| P-1 | En az yetki: `permissions: contents: read` | yazma yetkisi sızıntıyı büyütür | ci.yml L14–L15 |
| P-2 | Aynı ref için eşzamanlılık grubu | kısmi koşu çakışması | ci.yml L17–L19 |
| P-3 | İlerleyen iptal: `cancel-in-progress` | eski koşu kaynağı yakar | ci.yml L19 |
| P-4 | Sert kapı ile yumuşak kapı ayrık | gerçeği gizlemeden ilerleme | ci.yml L167, L183 |
| P-5 | Bağımlılık sırası açık yazılır | lint başarısızsa test anlamsız | ci.yml L68, L162 |
| P-6 | Zaman aşımı her işte tanımlı | sonsuz koşu maliyeti | ci.yml L28, L70, L113, L145, L164, L181 |
| P-7 | Ortam sürümü tek noktada | sürüm kayması | ci.yml L21–L22 |
| P-8 | Template'ten türetilir (Guardrail #16) | tutarlı iskelet | ci.yml L2 |

### §1.2 Kural dosyası ≠ workflow dosyası

| Kavram | Nerede | Kim değiştirir | Onay |
|---|---|---|---|
| Kapı ilkesi | bu dosya (vault) | MO / vault-updater | vault-sync |
| İş tanımı | `.github/workflows/*.yml` | DevOps Engineer | **kullanıcı onayı** |
| Rol/sahiplik | `.github/AGENTS.md` §3.1 | MO | vault-sync |
| Eşik değeri | iş içi parametre | DevOps + QA | kullanıcı onayı |

> `.github/AGENTS.md` §3.3-4: `.yml`'e kullanıcı onayı olmadan iş eklenmez/çıkarılmaz.
> Bu dosya **hiçbir işi değiştirmez**; yalnız denetler.

### §1.3 Sert kapı ↔ yumuşak kapı ayrımı

| Tür | Tanım | Bu repo'daki sayı | Davranış |
|---|---|---|---|
| Sert kapı | başarısızlık PR'ı durdurur | 4 (php-lint, php-test, composer-audit, js-test) | iş kırmızı → birleşme engel |
| Yumuşak kapı | başarısızlık görünür ama durdurmaz | 2 (js-coverage A7, js-e2e A6) | iş kırmızı → akış sürer |
| Tarama kapısı | ayrı workflow | 1 (gitleaks) | bulgu → iş düşer |

**Sayım:** 6 CI işi + 1 ayrı tarama işi = **7 kapı**. Bu sayı `ci.yml` ve
`secret-scan.yml` iş listelerinden sayıldı (bkz. `-surucu.md` §1).

## §2 Kalite kapıları

### §2.1 Kapı türleri

| # | Kapı | Aracı | Ne ölçer | Eşik | Sahip |
|---|---|---|---|---|---|
| K-1 | Statik analiz | PHPStan (`composer stan`) | tip/hata | level 5 (yorum, L60) | backend-architect |
| K-2 | Birim + suite testi | PHPUnit | davranış | `UNKNOWN` (eşik yok) | qa-engineer |
| K-3 | Bağımlılık denetimi | `composer audit` | bilinen CVE | `UNKNOWN` (süreç) | security-engineer |
| K-4 | JS birim testi | Vitest | JS davranışı | `UNKNOWN` | qa-engineer |
| K-5 | JS kapsamı | Vitest coverage | yüzdesel | `%80` — **yumuşak** (L165–L167) | qa-engineer |
| K-6 | E2E | Playwright | uçtan uca | `UNKNOWN` — **yumuşak** (L182–L183) | devops-engineer |
| K-7 | Secret tarama | GitLeaks | sızan anahtar | `UNKNOWN` (config) | security-engineer |

**K-1 eşik notu:** `ci.yml` L60 yorumu `scripts.stan = "phpstan analyse src --level=5"`
der; `phpstan.neon` dosyası diskte **YOK** (2026-10-13 ölçümü: ENOENT) → eşik
kaynağı `shared/composer.json` scriptidir. Level 8 iddiası **yoktur**.

**K-5 eşik notu:** `%80` değeri **iş yorumunda** yazılıdır (L165) ve iş
`continue-on-error: true` olduğu için bu eşik şu an **bloklayıcı değildir**.

### §2.2 Kapı türleri arası bağımlılık

```text
php-lint ──needs──> php-test ──(paralel)──> (iş sonu)
js-test  ──needs──> js-coverage (yumuşak)
composer-audit (bağımsız)
js-e2e (bağımsız, yumuşak)
secret-scan (ayrı workflow, push + pull_request)
```

**Gözlem:** `php-test` yalnız `php-lint`'e bağlıdır (L68); `composer-audit` ve
`js-*` işleri `needs` **taşımez** → bunlar birbirinden bağımsız paralel koşar.

### §2.3 Kapı sonuç sınıfları

| Sınıf | Belirti | Birleşmeye etkisi | Kayıt |
|---|---|---|---|
| PASS | iş yeşil | serbest | CI geçmişi |
| FAIL (sert) | iş kırmızı | **engel** | CI geçmişi |
| FAIL (yumuşak) | iş kırmızı + akış sürer | serbest ama görünür | uyarı |
| SKIP | koşu iptal (L19) | serbest | iptal kaydı |
| TIMEOUT | `timeout-minutes` aşıldı | iş düşer | iş logu |

> CI geçmişi **bu repoda okunmadı** → hangi sınıfın gerçekleştiği `UNKNOWN`dir.

## §3 Artifact ve caching

### §3.1 Caching kuralları

| # | Cache | Anahtar | Kapsam | Satır |
|---|---|---|---|---|
| C-1 | Composer | `${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` | php-lint | L44 |
| C-2 | Composer | aynı desen + `-${{ matrix.project }}` | php-test | L96 |
| C-3 | npm | `cache: npm` (setup-node) | js-* işleri | L152, L173, L195 |

**İlke C-1/C-2:** anahtar `composer.lock` içeriğine bağlıdır → lock değişince
cache yenilenir; **eski bağımlılık yayılmaz.** C-2'ye proje adı eklenir →
proje arası cache **karışmaz.**

**İlke C-3:** npm cache anahtarı `setup-node` tarafından üretilir; bu dosyada
görünmez → değeri `UNKNOWN`.

### §3.2 Artifact kuralları

| # | Kural | Bu repo durumu | Kanıt |
|---|---|---|---|
| A-1 | Test raporu artifact'i yüklenmeli | **tanım yok** | `ci.yml` içinde `upload-artifact` yok |
| A-2 | Kapsam raporu artifact'i | **tanım yok** | js-coverage işi raporu dışarı yüklemez (L168–L176) | 
| A-3 | Cache ≠ artifact | cache yeniden kullanım, artifact kalıcı çıktı | — |
| A-4 | Artifact sahipliği | DevOps Engineer | `.github/AGENTS.md` §3.1 |

**Negatif kanıt:** 203 satırlık `ci.yml` içinde `actions/upload-artifact`
**geçmez** (satır listesi okundu). Bu, A-1/A-2 kuralına **aykırı bir boşluktur**;
düzeltme `devops-engineer` işidir, bu dosyanın değil.

### §3.3 Cache güvenlik kuralları

| # | Kural | Gerekçe | Durum |
|---|---|---|---|
| X-1 | Cache anahtarı girdiye bağlı olsun | zehirli cache | uygulanmış (C-1, C-2) |
| X-2 | Cache yazma yetkisi sınırlı | cache üzerinden yazma | `permissions: contents: read` (L14–L15) |
| X-3 | Cache'e secret konmaz | sızıntı | `UNKNOWN` — cache içeriği okunmadı |
| X-4 | Harici cache kullanılmaz | üçüncü taraf | yerel `actions/cache@v4` (L41, L93) |

## §4 Secret tarama anayasası

### §4.1 Yerleşim kuralları

| # | Kural | Neden | Disk kanıtı |
|---|---|---|---|
| S-1 | Tarama **ayrı workflow** olmalı | test pipeline'ını bloklamadan koşabilmek | `secret-scan.yml` ayrı dosya |
| S-2 | `push` + `pull_request` tetikleyici | her iki giriş kapısı da taranır | secret-scan.yml L7–L9 |
| S-3 | `permissions: contents: read` | taramanın yazmaya ihtiyacı yok | secret-scan.yml L11–L12 |
| S-4 | tam geçmiş: `fetch-depth: 0` | tüm commitler taranır | secret-scan.yml L26 |
| S-5 | süre sınırı 10 dk | sonsuz tarama maliyeti | secret-scan.yml L22 |
| S-6 | token yalnız `${{ secrets.GITHUB_TOKEN }}` | sabit secret yasak | secret-scan.yml L31 |

### §4.2 Config dosyası durumu (çelişki kaydı)

| İddia | Kaynak | Ölçüm | Sonuç |
|---|---|---|---|
| “diskte `.gitleaks.toml` YOK” | `secret-scan.yml` L4 yorumu | dosya bilgisi okundu | **ÇELİŞİYOR — dosya VAR** (821 bayt, 2026-10-12) |
| Repo kökünde `.gitleaks.toml` var | disk ölçümü | `get_file_info` | **EVET** |
| Action config'i otomatik bulur mu? | — | okunmadı | `UNKNOWN` |
| Tarama hangi kuralla çalışıyor? | — | yordam içeriği okunmadı | `UNKNOWN` |

> **Çelişki kaydı:** workflow yorumu (L4) günceldir; disk ölçümü kazanır
> (`.ai/CLAUDE.md` kuralı: çelişkide disk ölçümü kazanır). Düzeltme yorumu
> `devops-engineer` günceller — bu dosya **dokunmaz**.

### §4.3 Secret yasakları (bu vault için)

| # | Yasak | Kaynak | İhlalde |
|---|---|---|---|
| 1 | secret değeri `.md` içine yazılmaz | kök AGENTS.md §16.3 | değeri iptal et + rapor |
| 2 | `${{ secrets.* }}` dışında token yok | secret-scan.yml L31 | tarama bulgusu |
| 3 | `.env` değeri workflow'a gömülmez | genel kural | kapıda sızıntı |
| 4 | Cache'e secret konmaz | §3.3 X-3 | `UNKNOWN` — denetlenmedi |
| 5 | Log'a token basılmaz | genel kural | log temizliği |

> Yasakların **hiçbiri bu dosyada uygulanmaz**; bu dosya yalnız kuralı yazar.

## §5 Determinizm

### §5.1 Determinizm kuralları

| # | Kural | Uygulama | Kanıt |
|---|---|---|---|
| D-1 | Bağımlılıklar lock dosyasıyla sabit | `composer.lock` hash → cache anahtarı | L44, L96 |
| D-2 | Node kurulumu `npm ci` | kilitleyici kurulum | L155, L174, L197 |
| D-3 | Sürüm tek kaynaktan | `env.PHP_VERSION` | L21–L22, L35 |
| D-4 | Node sürümü sabit | `node-version: '26'` | L151, L172, L194 |
| D-5 | Paralellik `fail-fast: false` | bir hata diğerini iptal etmez | L72 |
| D-6 | Eşzamanlılık grubu ref'e bağlı | aynı ref yığılmaz | L18 |
| D-7 | İlerleyen koşu iptali | eski koşu durur | L19 |
| D-8 | İş başına zaman aşımı | sonsuz koşu yok | L28, L70, L113, L145, L164, L181 |

### §5.2 Determinizm boşlukları

| # | Boşluk | Etki | Durum |
|---|---|---|---|
| 1 | Alet sürümleri bağımsız: `setup-php@v2`, `checkout@v4`, `cache@v4` | majör sürüm kayarsa davranış değişir | tam sürüm sabitlenmemiş |
| 2 | `extensions:` listesi sürümsüz | PHP eklentisi değişebilir | `UNKNOWN` etki |
| 3 | `ubuntu-latest` runner'ı | imaj güncellenince ortam değişir | sürümsüz |
| 4 | Kapsam raporu eşiklenemiyor (yumuşak) | kapı değişkenliği | A7 kararı |
| 5 | E2E tarayıcı sürümü: `chromium` | tarayıcı güncellemesi davranışı değiştirir | sürüm yazılmamış |

> Beş boşluğun hepsi **dosyada okunarak** tespit edildi; öngörü değildir.

### §5.3 Tekrarlanabilirlik testi (masa başı)

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| 1 | Aynı lock ile aynı bağımlılık kurulur mu? | Evet | D-1 + `--prefer-dist` |
| 2 | Aynı sürüm ile aynı yorumlayıcı? | Evet | D-3 |
| 3 | Aynı Node ile aynı çalışma ortamı? | Evet | D-4 |
| 4 | Aynı runner imajı? | **Hayır** (`-latest`) | §5.2-3 |
| 5 | Aynı alet sürümü? | **Hayır** (majör etiket) | §5.2-1 |
| 6 | Aynı test verisi? | `UNKNOWN` | fixture içeriği okunmadı |

**Sonuç:** 6 sorunun 3'ü evet, 2'si hayır, 1'i `UNKNOWN`. Mutlak tekrar
olanaklığı **sınırlıdır**; koşu içi tekrarlanabilirlik ise lock+sürüme bağlı.

## §6 Kapı yönetimi süreçleri

| # | Süreç | Tetik | Onay | Kayıt |
|---|---|---|---|---|
| Y-1 | Yeni iş ekleme | yeni kapı ihtiyacı | **kullanıcı onayı** | `.github/AGENTS.md` §3.3-4 |
| Y-2 | Yumuşağı sertleştirme | eşik aşıldı | kullanıcı onayı | iş yorumu (A6/A7) |
| Y-3 | Sertesteki yumuşağa alma | geçici kırılma | güvenlik onayı | — |
| Y-4 | Cache anahtarı değişimi | bağımlılık deseni | DevOps | ci.yml |
| Y-5 | Secret kuralı değişimi | yeni ihlal deseni | Security + DevOps | `.gitleaks.toml` |
| Y-6 | Runner geçişi | `latest` → sabit sürüm | DevOps | — |

> Y-1 ve Y-5 bu dosyanın **dışındadır**: biri `yml`, diğeri config dosyası
> değiştirir; ikisi de onay kapıllıdır.
## §7 Sorumluluk ve onay matrisi

| # | Görev | DevOps | QA | Security | backend | MO |
|---|---|---|---|---|---|---|
| 1 | Workflow dosyası yazar | **R** | C | C | I | I |
| 2 | Kapı ekleme/çıkarma | **R** | C | C | I | I |
| 3 | Kapsam/suite kapsamı | C | **R** | I | C | I |
| 4 | Secret tarama kuralı | C | I | **R** | I | I |
| 5 | Artifact tanımı | **R** | C | I | I | I |
| 6 | Determinizm denetimi | **R** | C | C | I | I |
| 7 | Bu dosyanın (anayasa) güncellenmesi | C | C | C | I | **R** |
| 8 | Onay (kapı değişikliği) | toplar | — | — | — | — |

> Tablo **atamadır**, gözlemsel dağıtım değil. `R` uygular, `C` katkı verir,
> `I` bilgilendirilir. Nihai onay **kullanıcıdır** (`.github/AGENTS.md` §4-2).

## §8 Kapsam sınırı ve reddedilen yüzeyler

| Yüzey | Kapsamda mı? | Nereye gider |
|---|---|---|
| Workflow yapısı ilkeleri | **Evet** | bu dosya |
| Kalite kapıları (tür/sonuç) | **Evet** | bu dosya |
| Artifact + caching kuralları | **Evet** | bu dosya |
| Secret tarama ilkesi | **Evet** | bu dosya |
| Determinizm | **Evet** | bu dosya |
| `ci.yml` job/matris satırları | Hayır | `-surucu.md` §1 |
| Gerçek workflow blokları | Hayır | `-ornekler.md` §1 |
| Pipeline sahipliği ve deploy | Hayır | `K17-devops` |
| Uygulama test stratejisi | Hayır | K019 / `uygulama-testi/` |
| OWASP denetimi | Hayır | `isletim-sistemi-security` |
| Canlı telemetri / metrik | Hayır | `isletim-sistemi-observability` |

## §9 Referans (HTTP 200 · 2026-10-13 · envanter dışı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| GitHub Actions güvenlik sıkılaştırma rehberi | https://docs.github.com/en/actions/security-guides/security-hardening-for-github-actions | 2026-10-13 | HTTP 200 (HEAD) |
| `actions/checkout` resmi deposu | https://github.com/actions/checkout | 2026-10-13 | HTTP 200 (HEAD) |
| `actions/setup-node` resmi deposu | https://github.com/actions/setup-node | 2026-10-13 | HTTP 200 (HEAD) |
| `actions/cache` resmi deposu | https://github.com/actions/cache | 2026-10-13 | HTTP 200 (HEAD) |

**Kesişim:** 4 URL · 2633'lük envanterle **0** · tekrar eden URL **yok**.

## §10 Açık sorular (kapatılmaz)

| # | Soru | Durum | Kapanış kriteri | Sahip |
|---|---|---|---|---|
| T2 | Kalite eşikleri ne? (coverage %, lint seviyesi) | `[UNKNOWN]` | eşik kararı + işe yazma | qa-engineer |
| Q-1 | `.gitleaks.toml` kullanılıyor mu (L4 çelişkisi)? | `UNKNOWN` | action config okuması | security-engineer |
| Q-2 | Artifact yüklenmiyor — bilinçli mi? | `UNKNOWN` | karar kaydı | devops-engineer |
| Q-3 | Runner `latest` yerine sabitlenecek mi? | `UNKNOWN` | karar | devops-engineer |
| Q-4 | Alet etiketleri tam sürüme çekilecek mi? | `UNKNOWN` | karar | devops-engineer |
| Q-5 | Yumuşak kapılar ne zaman sertleşecek? | `UNKNOWN` | A6/A7 eşik kararı | qa-engineer |
| Q-6 | Secret taraması test de tarıyor mu? | `UNKNOWN` | yordam kapsamı | security-engineer |
| Q-7 | Bağımlılık denetimi ne sıklıkla? | `UNKNOWN` | iş tetikleyicisi | security-engineer |
| Q-8 | Deploy kapısı bu dalda mı, K018'de mi? | `UNKNOWN` | sınır onayı | mimari sahibi |

> Sekiz soru da **bu dosyada cevaplanmaz**; kapanış kriteri ve sahip yazılır.

## §11 Red Team bulguları (bu dosya)

| # | Bulgu | Kanıt | Şiddet | Düzeltme sahibi | Durum |
|---|---|---|---|---|---|
| B-1 | `secret-scan.yml` L4 yorumu ile disk çelişiyor | L4 + dosya bilgisi (821 bayt) | MEDIUM | devops-engineer | açık |
| B-2 | Test raporu artifact'i yok | `upload-artifact` yok (203 satır) | MEDIUM | devops-engineer | açık |
| B-3 | İki yumuşak kapı (A6/A7) | L167, L183 | MEDIUM | qa-engineer | bilinçli, süreli |
| B-4 | `ubuntu-latest` sürümsüz | L27, L69, L112, L144, L163, L180 | LOW | devops-engineer | açık |
| B-5 | Alet etiketleri majör (v2, v4) | L30, L33, L41, L149 | LOW | devops-engineer | açık |
| B-6 | `composer-audit` bağımsız, lint'e bağlı değil | `needs` yok (L110–L139) | LOW | devops-engineer | kabul edilebilir |
| B-7 | Kapı eşikleri dosyada tanımlı değil | T2 `UNKNOWN` | HIGH | qa-engineer | açık |
| B-8 | `secrets.GITHUB_TOKEN` tek token | L31 | LOW | — | uygun |

**Şiddet dağılımı:** 1 HIGH · 3 MEDIUM · 4 LOW. Bu dosya **düzeltme
uygulamaz**; bulgu `-dogrulama`/rapor akışına işlenir, uygulamayı
`devops-engineer` yapar, `security-engineer` yeniden ölçer.

### §11.1 B-1 somut ölçüm

| Adım | Komut/araç | Sonuç |
|---|---|---|
| 1 | `secret-scan.yml` L4 okundu | “diskte .gitleaks.toml YOK” |
| 2 | `.gitleaks.toml` dosya bilgisi | var, 821 bayt, 2026-10-12 |
| 3 | Karşılaştırma | **çelişki** |
| 4 | Etki | yorum yanlış yönlendirir |
| 5 | Aksiyon | yorumu güncelle (DevOps) |

> Ölçüm 2026-10-13'te **diskten** alındı; web kaynağı kullanılmadı.

## §12 Ölçüm ve doğrulama kaydı

| # | Kayıt | Değer | Tarih |
|---|---|---|---|
| 1 | Disk kanıtı satırı | 12 kanıt (L listesi §1–§5) | 2026-10-13 |
| 2 | Web URL | 4/4 HTTP 200 · kesişim 0 | 2026-10-13 |
| 3 | Red Team bulgusu | 8 (§11) | 2026-10-13 |
| 4 | Çelişki kaydı | 1 (B-1) | 2026-10-13 |
| 5 | Yasaklı token | 0 (dolgu · gövde yorumu · yer tutucu) | 2026-10-13 |
| 6 | mojibake (U+FFFD) | 0 (UTF8 okuma) | 2026-10-13 |
| 7 | `parent:` alanı | 0 | 2026-10-13 |
| 8 | `group` değeri | mimari (tırnaksız) | 2026-10-13 |
| 9 | Commit | ATILMADI (subagent yetkisi yok) | 2026-10-13 |

### §12.1 Yeniden ölçüm bloğu

```powershell
$f='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-testing\ci-ve-kalite-kapisi\ci-ve-kalite-kapisi-anayasa.md'
$t=[IO.File]::ReadAllText($f,[Text.Encoding]::UTF8)
$l=([IO.File]::ReadAllLines($f)).Count
$l                                            # >=500 olmali
([regex]::Matches($t,('T'+'BD'))).Count      # 0 olmali (dolgu)
([regex]::Matches($t,'// \.\.\.')).Count    # 0 olmali (govde yorumu)
([regex]::Matches($t,'\{\.\.\.\}')).Count # 0 olmali (yer tutucu)
([regex]::Matches($t,[char]0xFFFD)).Count     # 0 olmali (mojibake)
([regex]::Matches($t,'(?m)^parent:')).Count   # 0 olmali
([regex]::Match($t,'(?m)^group: (.+)$')).Groups[1].Value  # mimari
```

> Beklenen: satır **>=500** · sayaçlar **0** · group **mimari** · son satır Authority.
> Bu blok çalıştırılmadan dosya **tamamlandı sayılmaz.**

> **SORUMLULUK SINIRI:** Bu dosya **ilkeleri yazar**, workflow dosyasına
> dokunmaz. `.github/workflows/*.yml` değişikliği **DevOps Engineer + kullanıcı
> onayı** gerektirir (`.github/AGENTS.md` §3.3-4).
## §13 Kapı katalogu (7 kapı — sayım)

| # | Kapı | Dosya | Tür | `needs` | Süre | Satır |
|---|---|---|---|---|---|---|
| 1 | php-lint | ci.yml | sert | — | 15 dk | L25–L28 |
| 2 | php-test | ci.yml | sert | php-lint | 15 dk | L66–L70 |
| 3 | composer-audit | ci.yml | sert | — | 10 dk | L110–L113 |
| 4 | js-test | ci.yml | sert | — | 10 dk | L142–L145 |
| 5 | js-coverage | ci.yml | **yumuşak** | js-test | 15 dk | L160–L167 |
| 6 | js-e2e | ci.yml | **yumuşak** | — | 20 dk | L178–L183 |
| 7 | gitleaks | secret-scan.yml | tarama | — | 10 dk | L19–L22 |

**Toplam süre üst sınırı (paralel kabul edilirse):** en uzun zincir
`php-lint (15) → php-test (15)` = **30 dk**; ayrı tarama 10 dk.
Bu **hesaptır, ölçüm değildir** — gerçek koşu süresi `UNKNOWN`.

### §13.1 Kapı türü dağılımı

| Tür | Adet | Oran (7 içinde) |
|---|---|---|
| Sert test/lint | 4 | %57 |
| Yumuşak | 2 | %29 |
| Tarama | 1 | %14 |
| Deploy | 0 | %0 |

> Deploy kapısı **bu dalda yok** (sınırlar §8) — K018-devops kapsamındadır.

## §14 Güvenlik ilkeleri (CI yüzeyi)

| # | İlke | Uygulama | Kanıt | Durum |
|---|---|---|---|---|
| G-1 | En az yetki | `contents: read` | ci.yml L14–L15 · secret-scan L11–L12 | uygulanmış |
| G-2 | Fork PR'da secret yok | — | okunmadı | `UNKNOWN` |
| G-3 | Sabit token yok | `${{ secrets.GITHUB_TOKEN }}` | secret-scan L31 | uygulanmış |
| G-4 | Alet imzası/sabit sürüm | majör etiket (`v2`, `v4`) | L30, L33, L41, L149 | kısmi |
| G-5 | Bağımlılık CVE denetimi | `composer audit` | L126, L132, L138 | uygulanmış |
| G-6 | Secret tarama | gitleaks-action | secret-scan L29 | uygulanmış |
| G-7 | Kod yazma yetkisi kapalı | `permissions` bloğu | L14–L15 | uygulanmiş |
| G-8 | Dış repo pin (SHA) | etiket kullanılıyor | L30, L33, L41, L149, L170 | **yapılmamış** |

**G-8 bulgusu:** üçüncü taraf aletler **majör etiketle** bağlı; SHA sabitleme
yapılmamıştır. Bu, B-5 ile aynı aileden bir **LOW** bulgudur; güvenlik
sıkılaştırma rehberi referanstır (§9, URL 1).

## §15 Kapanış — kabul kriterleri

| # | Kriter | Eşik | Durum |
|---|---|---|---|
| K-1 | satır sayısı | >=500 | §12.1 ile ölçülür |
| K-2 | disk kanıtı | >=12 satır | §1–§5, §13 |
| K-3 | kapı sayımı | 7 (6 CI + 1 tarama) | §13 |
| K-4 | web URL | 4/4 HTTP 200 · kesişim 0 | §9 |
| K-5 | çelişki kaydı | 1 (B-1) | §11.1 |
| K-6 | Red Team bulgusu | 8 satır | §11 |
| K-7 | yasaklı token | 0 | §12-5 |
| K-8 | mojibake | 0 | §12-6 |
| K-9 | `parent:` alanı | 0 | §12-7 |
| K-10 | `group` | `mimari` | §12-8 |
| K-11 | son satır | Authority satırı | mevcut |
| K-12 | commit | ATILMADI | §12-9 |

**Geçme kuralı:** on iki kriterin **hepsi** yerine gelmeden dosya tamamlandı
sayılmaz; K-1 ölçülmeden diğerleri **iddia** kalır.

> **SORUMLULUK SINIRI:** Bulgu üretir, workflow **dokunmaz**. Uygulama
> `devops-engineer`, güvenlik onayı `security-engineer`, kapı kapsamı
> `qa-engineer`, metin güncellemesi `MO (vault-updater)`.
## §16 Eke — ilke ↔ kanıt eşlemesi (tam)

| İlke | Bölüm | Satır kanıtı | Doğrulama biçimi |
|---|---|---|---|
| P-1 en az yetki | §1.1 | ci.yml L14–L15 | birebir okuma |
| P-2 eşzamanlılık | §1.1 | ci.yml L17–L19 | birebir okuma |
| P-3 ilerleyen iptal | §1.1 | ci.yml L19 | birebir okuma |
| P-4 sert/yumuşak ayrımı | §1.3 | ci.yml L167, L183 | birebir okuma |
| P-5 bağımlılık sırası | §1.1 | ci.yml L68, L162 | birebir okuma |
| P-6 zaman aşımı | §1.1 | ci.yml L28, L70, L113, L145, L164, L181 | 6/6 okuma |
| P-7 tek sürüm kaynağı | §1.1 | ci.yml L21–L22, L35 | birebir okuma |
| P-8 şablondan türetme | §1.1 | ci.yml L2 | birebir okuma |
| C-1/C-2 cache anahtarı | §3.1 | ci.yml L44, L96 | birebir okuma |
| D-5 fail-fast | §5.1 | ci.yml L72 | birebir okuma |
| D-2 npm ci | §5.1 | ci.yml L155, L174, L197 | 3/3 okuma |
| S-4 fetch-depth | §4.1 | secret-scan.yml L26 | birebir okuma |

**Toplam:** 12 satır grubu · 22 birebir okunan satır · uyuşmazlık **0**.

### §16.1 Doğrulama komutları

```powershell
$c=Get-Content 'C:\www\coremusic.net\.github\workflows\ci.yml' -Encoding UTF8
$c[13]; $c[14]                 # permissions: contents: read
$c[16]; $c[17]; $c[18]         # concurrency + cancel-in-progress
$c[67]                          # needs: php-lint
$c[71]                          # fail-fast: false
$c[166]; $c[182]                # continue-on-error: true (x2)
$c[27]; $c[69]; $c[112]         # timeout-minutes
$s=Get-Content 'C:\www\coremusic.net\.github\workflows\secret-scan.yml' -Encoding UTF8
$s[11]; $s[12]                 # permissions: contents: read
$s[25]                          # fetch-depth: 0
(Get-Content 'C:\www\coremusic.net\.github\workflows\ci.yml').Count   # 203
(Get-Content 'C:\www\coremusic.net\.github\workflows\secret-scan.yml').Count  # 31
```

> Beklenen: 203 ve 31 satır, okunan satırlar birebir. Komut çalıştırılmadan
> bu dosya **tamamlandı sayılmaz.**

### §16.2 Bilinçli olarak yazılmayanlar

| Yazılmayan | Neden |
|---|---|
| Gerçek iş logları / sonuçlar | CI geçmişi bu repoda okunmadı |
| Kapsam yüzdesi değeri | eşik `UNKNOWN` (T2) |
| Gerçek koşu süresi | ölçüm yok |
| Runner imaj sürümü | `latest` değeri |
| Alet SHA'sı | sabitlenmemiş (B-5) |
| Secret değeri | yasak (§4.3) |

> Altı satırın hiçbiri **uydurulmaz**; kapanış yoluna bağlanır (§10).
## §17 Eke — terim sözlüğü (bu dosyada kullanım)

| Terim | Bu dosyadaki anlamı | Yanlış anlam |
|---|---|---|
| Kapı | birleşmeyi engelleyen iş sonucu | her iş sonucu |
| Yumuşak kapı | `continue-on-error: true` işi | kapalı kapı |
| Artifact | kalıcı iş çıktısı | cache |
| Cache | yeniden kullanım için geçici dosya | artifact |
| Determinizm | aynı girdi → aynı sonuç | hız |
| Tarama | ayrı workflow'daki bulgu arama | test |
| Eşik | engelleyen sayısal değer | istek |
| İlke | bu dosyadaki kural | iş tanımı |

> Sözlük sekiz satır içerir; her terim bu dosyanın bölüm bağlamında tanımlıdır,
> genel sözlük iddiası taşımaz.
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
