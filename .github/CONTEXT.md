---
title: "CoreMusic — .github Klasör Context"
type: docs
category: infrastructure
docType: context
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# .github — CONTEXT.md

**docType:** context · **Klasör:** `.github/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.github/`, GitHub platformunun repo düzeyi davranışlarını barındırır: CI/CD workflow tanımları (`workflows/`) ve issue şablonları (`ISSUE_TEMPLATE/`). Kapsam notu: **.github = CI/CD + issue şablonları (DevOps domain)** — kod üretmez, depo üzerinde çalışan tanımlar (yml/md) içerir; sorumlu agent DevOps Engineer'dır. Bu doküman klasörün disk envanterini, CI işlerinin yapısını ve mevcut `CLAUDE.md` ile disk arasındaki çelişkileri kanıtlarıyla tanımlar.

| Karar | Kaynak (disk) |
|-------|---------------|
| CI/CD 2 workflow dosyası | `.github/workflows/ci.yml` (139 satır) · `.github/workflows/secret-scan.yml` (32 satır) — ölçüm 2026-10-03 |
| Issue şablonu 1 adet | `.github/ISSUE_TEMPLATE/01-bug-report.md` (38 satır) |
| Workflow şablon kaynağı (Guardrail #16) | `ci.yml` satır 2 yorumu → `.ai/.templates/infrastructure/github-actions-template.md` §3.2 |
| CI plan kaynağı (K13) | `ci.yml` satır 3 yorumu → `.ai/architecture/k13-cicd/README.md` + `.ai/architecture/k13-cicd/ci-pipeline.md` |
| `*.yml` sahibi | [[../.ai/AGENTS.md]] §5 Domain Boundaries → DevOps Engineer |
| Secret tarama sahibi | ci.yml/secret-scan.yml içinde GitLeaks adımı → Security Engineer (denetim) + DevOps (pipeline) |

> **eli10 (basit):** Bu klasör, GitHub'ın depo ayarlarını ve otomatik kontrollerini (test + gizli anahtar taraması) tutar; sorumlusu DevOps ekibidir.
> **eli15 (detay):** Ayrı yazıldı çünkü GitHub tanımları sunucuda çalışır, PHP kodunun içinde duramaz. Okunması, "depoya kod girerken hangi kapıların geçildiğini" gösterir. Yazılması, plan ile uygulamanın hizasını kanıtlar. Bu dosyalar olmazsa test ve secret taraması çalışmaz, hatalı değişiklik doğrudan depoya girer.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.github/` kök + `workflows/` + `ISSUE_TEMPLATE/` envanteri (depth 2, 5 dosya) | PHP/JS/CSS kaynak kod (ilgili katmanın `CONTEXT.md`'si) |
| `ci.yml` job yapısı ve kapıları | `.ai/` vault içeriği (SSOT, okunur ama burada üretilmez) |
| `secret-scan.yml` GitLeaks adımı ve config durumu | GitHub repo/org seviyesi ayarları (arayüzde, diskte değil) |
| Mevcut `CLAUDE.md` ↔ disk çelişki kaydı | Vault `.ai/.rules/`, `.ai/scripts/` envanteri |

- **Kullananlar:** DevOps Engineer (`*.yml`), Security Engineer (secret-scan kapsamı), MO (bu doküman + index kaydı), Claude Code (`.github/CLAUDE.md` boot okuması).
- **Ön koşul:** Klasör diskte mevcut ve tüm girdiler `vault-utf8-writer verify` ile ölçülmüş olmalı; sayısal iddiası olmayan satır `UNKNOWN` taşır.
- **Dokunulmaz:** `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) bu görevde değiştirilmez; yalnız wiki-link ile bağlanır.

---

## 3. Mimari

### 3.1 Kök Envanter (depth 2 — disk ölçümü 2026-10-03: 5 dosya)

| Dosya/Katman | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir | eli10 | eli15 |
|--------------|--------------------|---------------|-----------|---------------------|-------|-------|
| `workflows/ci.yml` | Push/PR'da lint + test + audit kapısı | 3 job: `php-lint`, `php-test`, `composer-audit`; PHP 8.4; checkout/cache/setup-php aksiyonları | Hatalı kod depoya sessizce girmesin | CI davranışı değişince (DevOps + onay) | Depoya her girişte çalışan otomatik sınav: lint, test ve güvenlik denetimi. | Ayrı yml'dir çünkü GitHub onu okur; PHP kodu içine gömülse çalışmaz. Okunması, hangi kapıların zorunlu olduğunu gösterir. CI planı (K13) değişince düzenlenir. Atlarsan test aşlanan değişiklik üretime sızmaz ama kapı da çalışmaz. |
| `workflows/secret-scan.yml` | Push/PR'da gizli anahtar (secret) taraması | tek job `gitleaks`; `fetch-depth: 0`; `gitleaks/gitleaks-action@v2` | Anahtar sızıntısı yakalansın | Tarama politikası/ayar değişince | Gizli anahtar kaçak avcısı; depoya sızan şifreleri yakalar. | Ayrı dosyadır çünkü tek işi var: güvenlik taraması. CI testinden ayrılınca test kapısı kırılsa bile tarama çalışır kalır. Okunması, hangi aksiyon ve hangi depth ile tarandığını gösterir. `.gitleaks.toml` yokluğu dosya yorumunda yazılıdır; config eklenince düzenlenir. |
| `ISSUE_TEMPLATE/01-bug-report.md` | Standart hata bildirim formu | frontmatter (name/about/title/labels) + 7 bölüm (özet, adımlar, beklenen, gerçekleşen, görüntü, ortam, öneri) | Hata raporu tek biçimde toplansın | Rapor alanları değişince | Hata bildiriminde doldurulan hazır form. | Ayrı durur çünkü GitHub onu issue oluştururken sunar; başka dosyaya gömülse kullanılmaz. Okunması, hangi bilgilerin istendiğini gösterir. Yeni alan (ör. log) eklenince düzenlenir. Form bozulursa eksik rapor gelir, hata tekrarlanamaz. |
| `CLAUDE.md` (kök) | Bu klasörün boot kuralı (MEVCUT — dokunulmaz) | 47 satır; frontmatter + bağlam + mevcut durum + komşu ilişkiler + değişiklik protokolü | Klasör kuralları tek yerde dursun | Yalnız vault-sync (MO) | Bu klasörde çalışırken uyulacak kuralların yazılı olduğu defter. | Kural ile tanım farklı hızda değişir; kod içine gömülse her revizyonda kural aranır. Okunması, klasörün kimin sorumlu olduğunu ve protokolü gösterir. Bu oturumda overwrite yasak; düzeltme talebi vault-sync'e rapor edilir. |
| `ISSUE_TEMPLATE/CLAUDE.md` | Alt dizin boot kuralı (MEVCUT — dokunulmaz) | 39 satır; bağlam + mevcut durum + komşu ilişkiler | Issue şablonu kuralı üst klasörden ayrılır | Yalnız vault-sync (MO) | Şablon klasörüne ait kısa kural defteri. | Alt dizin kuralı ayrı durur ki üst klasör değişince şablon kuralı sessizce değişmesin. Okunması, şablon ekleme protokolünü gösterir. Silinmez ve bu görevde düzenlenmez. |

> **eli10 (basit):** Beş dosya var: iki otomatik kontrol tanımı, bir hata formu ve iki kural defteri; ikisi kural defteri olduğu için dokunulmaz.
> **eli15 (detay):** Envanter ayrı yazıldı çünkü ölçüm yazılmadan iddia edilmez (ZERO-HALLUCINATION). Okunması, klasörün gerçekten ne içerdiğini ve hangi dosyanın kime ait olduğunu gösterir. Yazılması, CI planı (`.ai/architecture/k13-cicd/`) ile disk gerçeğinin hizalandığını kanıtlar. Ölçüm yoksa "kaç workflow var" sorusu tahminle cevaplanır ve plan ile uygulama ayrışır.

### 3.2 CI Pipeline Yapısı (`ci.yml` — disk ölçümü: 139 satır, 3 job)

| Job | Ne yapar | Disk kanıtı (ci.yml) |
|-----|----------|---------------------|
| `php-lint` | 3 dizine `composer install` + `composer stan` (PHPStan level 5) | `working-directory: shared`, `auth.coremusic.net`, `home.coremusic.net`; `phpstan.neon` YOK yorumu (satır ~59) |
| `php-test` | `needs: php-lint` → matrix: `shared` (tümü), `auth.coremusic.net` (`--testsuite Unit`) | matrix `include` yorumu: `tests/Integration` dizini diskte YOK → yalnız Unit koşulur |
| `composer-audit` | 3 dizinde `composer audit` (bağımlılık güvenlik denetimi) | 3 ayrı `working-directory` adımı (satır ~122-138) |

Ortak ayar: `on: push` + `pull_request` · `permissions: contents: read` · `concurrency` ile aynı ref'te eski koşu iptali · `PHP_VERSION: '8.4'` (kanıt: 3 composer.json `"php": ">=8.4"`).

> **eli10 (basit):** CI, üç adımda çalışır: önce kod denetlenir, sonra testler koşulur, en son güvenlik açığı kontrolü yapılır.
> **eli15 (detay):** Job'lar ayrı ayrı tanımlanmıştır çünkü bağımlıdırlar: test, lint geçmeden çalışmaz (`needs`). Okunması, hangi dizinlerin gerçekten test edildiğini gösterir (auth yalnız Unit). Yazılması, disk kanıtıyla (composer.json/phpunit konumu) hizalanır. Job kaldırılırsa o kapı tamamen kalkar ve hatalı kod depoya girer.

### 3.3 Secret Scan (`secret-scan.yml` — disk ölçümü: 32 satır, 1 job)

| Öğe | Değer | Kanıt |
|-----|-------|-------|
| Job | `gitleaks` (timeout 10 dk) | `jobs.gitleaks` |
| Checkout | `fetch-depth: 0` (tüm geçmiş taranır) | `with.fetch-depth` + `github-actions-template.md` §10 yorumu |
| Aksiyon | `gitleaks/gitleaks-action@v2` + `GITHUB_TOKEN` | `env` bloğu |
| Config | `.gitleaks.toml` **diskte YOK** → varsayılan config | dosya satır 4 yorumu |

> **eli10 (basit):** Bu dosya, depoya yanlışlıkla yazılmış şifre ve anahtarları her girişte tarar.
> **eli15 (detay):** CI testinden ayrıldı çünkü güvenlik taraması testten bağımsız çalışmalı; test kapısı kapalıyken bile sızıntı avlanır. Okunması, tüm geçmişin tarandığını (`fetch-depth: 0`) ve özel config kullanılmadığını gösterir. Config eklenince (`security-scanning.md` örneği) bu satır güncellenir. Atlarsan anahtar sızıntısı fark edilmez.

### 3.4 ISSUE_TEMPLATE Envanteri (depth 2: 2 dosya)

| Dosya | Ne için | Ne zaman düzenlenir | eli10 | eli15 |
|-------|---------|---------------------|-------|-------|
| `01-bug-report.md` | `[BUG]` önekli, `hata`/`onay bekliyor` etiketli hata formu (7 bölüm) | Rapor alanları değişince | Hata girilirken doldurulan tek form. | GitHub formu olduğu için adı/etiketleri frontmatter'dadır; alan değişince tek dosya düzenlenir. Form eksik olursa hata tekrarlanamaz, çözüm gecikir. İçeriği ASCII karakterle yazılmıştır (ör. `Adimlar` — ölçümdür, düzeltilmesi vault-sync kapsamındadır). |
| `CLAUDE.md` | Alt dizin kuralı (MEVCUT — dokunulmaz) | Yalnız vault-sync | Şablon klasörünün kısa kural defteri. | Ayrı durur ki üst klasör kuralı değişince şablon kuralı sessizce değişmesin. Okunması protokolü gösterir; bu görevde değiştirilmez. |

> **eli10 (basit):** Hata bildirimleri tek formdan toplanır; formun kural defteri de ayrıca durur.
> **eli15 (detay):** Şablon ayrı yazılır çünkü GitHub onu issue akışında ayrı sunar. Okunması, hangi bilgilerin istendiğini ve etiketleri gösterir. Yazılması, rapor alanları değişince yapılır. Olmazsa herkes farklı biçimde hata açar, tekrar üretimi imkânsızlaşır.

### 3.5 Komşu İlişkiler

| Komşu | Yön | Ne taşır (disk kanıtı) |
|-------|-----|------------------------|
| `../AGENTS.md` (kök) | .github → kök | Master kurallar; `.yml` sahipliği ve commit kuralı |
| `../.ai/AGENTS.md` | .github → vault | §5 Domain Boundaries: `*.yml` → DevOps Engineer |
| `../.ai/architecture/k13-cicd/` | .github → plan | `README.md`, `ci-pipeline.md`, `security-scanning.md` (ci.yml yorumları) |
| `../.ai/.templates/infrastructure/github-actions-template.md` | .github → şablon | Guardrail #16 kaynağı (ci.yml/secret-scan.yml yorumları) |
| `../.workflows/deployment.md` | .github → süreç | Dağıtım akışı CI kapısını tamamlar |
| `../.claude/settings.json` | .github → Claude Code | `instructions` listesi vault boot'unu tetikler |

> **eli10 (basit):** Bu klasör tek başına çalışmaz; plan, şablon, kurallar ve dağıtım akışı hep başka klasörlerden gelir.
> **eli15 (detay):** İlişkiler ayrı tabloda çünkü çelişki çıkınca hangi kaynağa bakılacağı belli olsun. Okunması, plan-şablon-uygulama zincirini gösterir. Yazılması, klasör değişince komşu etkisi de görünür olur. Tablo boş kalırsa "bu yml nereden geldi" sorusu cevapsız kalır.

### 3.6 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak) | Disk ölçümü (2026-10-03) | Karar |
|---|----------------|--------------------------|-------|
| 1 | `.github/CLAUDE.md` §2: "Workflow (Actions) **Yok**" (2026-09-06) | `workflows/` içinde **2 dosya** (`ci.yml` 139 satır, `secret-scan.yml` 32 satır) | **Disk kazanır** — mevcut CLAUDE.md bu görevde DOKUNULMAZ; düzeltme talebi `.ai/log.md` + vault-sync'e raporlandı |
| 2 | `.github/CLAUDE.md` §3, wiki-link biçimi (`[[...]]`) ile verdiği hedef: `../.ai/architecture/02-deployment/index.md` | `Test-Path` → **False** (hedef diskte yok) | **Kırık link** — VERIFICATION REQUIRED; hedef mi taşındı mı link mi yanlış, vault-sync kapsamında araştırılır (bu dokümanda hedef kod olarak anılır, yeni kırık link üretilmez) |
| 3 | `.github/CLAUDE.md` §2: "Issue şablonu: 1" | `ISSUE_TEMPLATE/` → 1 şablon `.md` + 1 `CLAUDE.md` = **tutarlı** | Çelişki yok |

> **eli10 (basit):** Eski kural defteri "workflow yok" diyor ama diskte iki tane var; gerçeği disk söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu gösterir. Yazılması, ölçüm kanıtıyla olur. Bu tablo olmazsa eski "Yok" bilgisi yeni agent'ları yanlış yönlendirir.

### 3.7 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (yml → DevOps, şablon → GitHub) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer/ayar tek yerde durur (ör. PHP 8.4 tek `env` bloğunda) | Her job'da ayrı değer → sürüm farkı, tarama gerekir |
| 4 | **Cihaz izolasyonu** | Ortam farkı (runner/runner ayarı) yayılmaz | Ortam farkı tüm işlere sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf aksiyonlar (checkout, setup-php, gitleaks) kendi yml'lerinde ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: CI kapısı `ci.yml`'de, tarama `secret-scan.yml`'de, rapor formu `ISSUE_TEMPLATE/` altında durur. Böylece biri değişince sadece o değişir, diğer kapılar bozulmaz. Tek dosyada toplansa test kapısı ile secret kapısı birbirine karışır. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | Neden var (gerekçe) | Kaynak |
|---|-------|---------------------|--------|
| 1 | `.yml` değişikliği yalnız DevOps Engineer + kullanıcı onayı ile girer | Yanlış CI tanımı her push'ta çalışır, kaynak tüketir/yanlış geç verir | [[../.ai/AGENTS.md]] §5 |
| 2 | Yeni workflow, `.ai/.templates/infrastructure/github-actions-template.md`'den türetilir | Tutarlı gate/deploy standardı (Guardrail #16) | ci.yml satır 2 yorumu |
| 3 | Secret değerleri vault `.md` dosyalarına YAZILMAZ; GitHub repo secrets'ta yaşar | Anahtar sızıntısı (REDACTED politikası) | [[CLAUDE.md]] §4 + kök `AGENTS.md` §3 |
| 4 | Mevcut `CLAUDE.md` dosyaları bu görevde değiştirilmez | Dokunulmazlık + SSOT hiyerarşisi (düzeltme vault-sync'te) | Görev kuralı + [[../.ai/CLAUDE.md]] |
| 5 | Çelişki (vault ↔ disk) → disk kazanır + bu §3.6 tablosunda işaretlenir | SSOT ihlali önlenir | [[../.ai/CLAUDE.md]] Hard Guardrails |
| 6 | Wiki-link `[[...]]` formatı; hedefi olmayan link üretilmez | Kırık link istenmez; bulunamayan hedef `UNKNOWN`/işaretli kalır | [[../AGENTS.md]] (format kuralı) + bu doküman §3.6 #2 |
| 7 | Yeni doküman 7 alan + `docType` frontmatter ve §1–§7 sırasıyla üretilir | Şablon zorunluluğu (Guardrail #16) | `.ai/.templates/frontend/context-template.md` §3.2/§3.4 |

> **eli10 (basit):** Bu kurallar, depo ayarlarının izinsiz ve gizli bilgi sızdıracak şekilde değişmesini engeller.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile `.yml` farklı hızda değişir; yml içine gömülse her CI revizyonunda kural revize edilir. Okunmaları, bu klasörde işe başlamadan önce sınırları gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kural değişince (yeni ADR/kök kural) yalnız bu tablo güncellenir; CI dosyaları sessizce değişmez.

---

## 5. Workflow

```text
GÖREV GELİR → İLGİLİ KURAL OKU (.github/CLAUDE.md + AGENTS.md)
  → DISK ÖLÇÜMÜ (workflows/ + ISSUE_TEMPLATE/ envanteri, 1. kez)
  → KARAR VER (ilk okumadan) → DEĞİŞİKLİK (yalnız hedef dosya)
  → DOĞRULAMA (yml lint / ilgili kapı) → RAPOR (commit atmaz — orkestratöre aittir)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör yasakları orada (ör. secret yazma yasağı) | Yasak ihlali, sızıntı riski |
| 2 | Envanter ölç (5 dosya; sayı uydurulmaz) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Değişikliği ilgili `workflows/*.yml` veya `ISSUE_TEMPLATE/*.md`'ye yap | Diff | Tek sorumluluk sınırı | Yanlış dizine yazma, çakışma |
| 4 | Kapı: yml geçerliliği + ilgili `.workflows/deployment.md` kontrolü | Yeşil kapı | Hatalı yml workflow'u hiç çalışmaz | Sessiz kırılma, kapı devre dışı |
| 5 | `VERIFICATION REQUIRED` işaretli satırları raporla | Açık liste | Doğrulanmayan iddia yazılmaz | Yanlış "tamamlandı" |
| 6 | Rapor yaz; **commit ATMA** | Rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert riski |

> **eli10 (basit):** İş sırayla yürür: kuralı oku, dosyaları say, tek yeri değiştir, kapıyı geç ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve kapı) hatalı iddia git geçmişine girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: context` |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | Envanter | 5 dosya + 3 job + 1 şablon iddiası `verify` ölçümüyle uyumlu |
| 5 | Disk kanıtı | Sayı/satır iddiaları `vault-utf8-writer verify` çıktısıyla kanıtlı |
| 6 | Wiki-link | `[[...]]` formatı; olmayan hedef yazılmadı (`02-deployment/index.md` §3.6 #2'de işaretli) |
| 7 | eli10 + eli15 | §1, §3, §4, §5 bloklarında etiketli blok var; sıralama eli10 → eli15 |
| 8 | Halüsinasyon | Diskte olmayan dosya/job/ADR iddia edilmedi; bilinmeyen `UNKNOWN`/işaretli |
| 9 | Dokunulmaz | `CLAUDE.md` (kök + ISSUE_TEMPLATE) değiştirilmedi |
| 10 | Emoji | Yalnız `[[...]]` referansı; dekoratif emoji yok |
| 11 | Uzunluk | 200–350 satır bandı (küçük dizin istisnası: toplam 5 dosyalık altyapı klasörü) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasörde çalışırken uyulacaklar (MEVCUT — okunur, değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Kim hangi dosyaya dokunur |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Alt dizin kuralı | [[ISSUE_TEMPLATE/CLAUDE.md]] | Issue şablonu protokolü (MEVCUT) |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry | [[../.ai/AGENTS.md]] | §5: `*.yml` → DevOps Engineer |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| CI planı | [[../.ai/architecture/k13-cicd/README.md]] | K13 planı (ci.yml yorumu) |
| Workflow şablonu | [[../.ai/.templates/infrastructure/github-actions-template.md]] | Guardrail #16 kaynağı |
| CI iş kanıtı | `.github/workflows/ci.yml` | 3 job disk kanıtı |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | Bu dokümanın iskeleti (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
