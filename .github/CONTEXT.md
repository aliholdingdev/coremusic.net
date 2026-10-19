---
title: "CoreMusic — .github Context"
type: docs
category: infrastructure
date: 2026-10-03
updated: 2026-10-12
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
docType: context
---

# CoreMusic — .github Context

**docType:** context · **Klasör:** `.github/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[../AGENTS.md]]

---

## 1. Amaç

`.github/`, GitHub'ın repo düzeyinde okuduğu tanımları tutar: CI/CD workflow dosyaları (`workflows/`) ve issue şablonu (`ISSUE_TEMPLATE/`). Bu doküman klasörün disk envanterini (8 dosya, 2 alt dizin — ölçüm 2026-10-12), CI işlerinin yapısını ve vault dosyaları ile disk arasındaki çelişkileri kanıtlarıyla tanımlar; kaynak kod üretmez, yalnız okunur ve ölçümle güncellenir.

Not: Önceki sürümün mimari katman atıfları bu revizyonda kaldırıldı; yerine yenisi üretilmedi.

| Karar | Kaynak (disk) |
|-------|---------------|
| Envanter: 8 dosya, 2 alt dizin | glob ölçümü (`.github/**`, gizli dosyalar dahil, 2026-10-12) |
| CI/CD 2 workflow dosyası | `workflows/ci.yml` — 203 satır, 6 job · `workflows/secret-scan.yml` — 31 satır, 1 job (okuma ölçümü 2026-10-12) |
| Issue şablonu 1 adet | `ISSUE_TEMPLATE/01-bug-report.md` — 37 satır, 7 bölüm |
| Workflow şablon kaynağı (Guardrail #16) | `ci.yml` satır 2 yorumu → `.ai/.templates/infrastructure/github-actions-template.md` §3.2 |
| `*.yml` sahibi | [[../.ai/AGENTS.md]] §5 Domain Boundaries → DevOps Engineer |
| Dokunulmaz yüzey | `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) — bu görevde değiştirilmez |

> **eli10 (basit):** Bu klasör, GitHub'ın depo ayarlarını ve otomatik kontrollerini (test + gizli anahtar taraması) tutar; sorumlusu DevOps'tur.
> **eli15 (detay):** Ayrı yazıldı çünkü GitHub tanımları sunucuda çalışır, PHP kodunun içinde duramaz. Okunması, "depoya kod girerken hangi kapıların geçildiğini" gösterir. Yazılması, envanter ile disk gerçeğinin hizasını kanıtlar. Bu dosyalar olmazsa test ve secret taraması çalışmaz, hatalı değişiklik doğrudan depoya girer.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.github/` kök + `workflows/` + `ISSUE_TEMPLATE/` envanteri (8 dosya, 2 alt dizin) | PHP/JS/CSS kaynak kod (ilgili katmanın context dosyası) |
| `ci.yml` 6 job yapısı ve kapıları | `.ai/` vault içeriği (SSOT — okunur, burada üretilmez) |
| `secret-scan.yml` GitLeaks adımı ve config durumu | GitHub repo/org arayüz ayarları (diskte değil → UNKNOWN) |
| Vault dosyası ↔ disk çelişki kaydı | Vault alt klasör envanterleri (`.ai/` içi, ilgili vault dosyalarının konusu) |

- **Kullananlar:** DevOps Engineer (`*.yml`), Security Engineer (secret-scan kapsamı), MO (bu doküman + index kaydı), Claude Code (`.github/CLAUDE.md` boot okuması).
- **Ön koşul:** Klasör diskte mevcut ve envanter bu oturumda ölçülmüş; kanıtı olmayan sayısal iddia `UNKNOWN` taşır.
- **Dokunulmaz:** `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) bu görevde değiştirilmez; yalnız wiki-link ile bağlanır.

> **eli10 (basit):** Burada yalnız bu klasörün dosyaları ve CI kapıları anlatılır; başka kod ve sunucu ayarları konu dışıdır.
> **eli15 (detay):** Kapsam ayrı yazıldı çünkü sınır yoksa bu doküman vault ya da kaynak kod tarifine döner ve okunmaz. Okunması, hangi sorunun buradan cevaplanacağını gösterir. Yazılması, ölçüm ve çelişki kaydının nerede durduğunu sabitler. Sınır kayarsa her görev bu dosyayı yeniden keşfeder.

---

## 3. Mimari

### 3.1 Envanter (glob ölçümü 2026-10-12: 8 dosya, 2 alt dizin)

| Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir |
|--------------|---------|--------|-----------|---------------------|
| `workflows/ci.yml` | Push/PR'da lint + test + audit + JS kapıları | 6 job (`php-lint`, `php-test`, `composer-audit`, `js-test`, `js-coverage`, `js-e2e`); 203 satır | Hatalı kod depoya sessizce girmesin | CI davranışı değişince (DevOps + kullanıcı onayı) |
| `workflows/secret-scan.yml` | Push/PR'da gizli anahtar taraması | tek job `gitleaks`; 31 satır | Anahtar sızıntısı yakalansın | Tarama politikası/ayar değişince |
| `ISSUE_TEMPLATE/01-bug-report.md` | Standart hata bildirim formu | frontmatter (`name`, `about`, `title`, `labels`) + 7 bölüm; 37 satır | Hata raporu tek biçimde toplansın | Rapor alanları değişince |
| `CLAUDE.md` (kök) | Bu klasörün boot kuralı (MEVCUT — dokunulmaz) | bağlam + mevcut durum + komşu ilişkiler + değişiklik protokolü; 46 satır | Klasör kuralları tek yerde dursun | Yalnız vault-sync (MO) |
| `ISSUE_TEMPLATE/CLAUDE.md` | Alt dizin boot kuralı (MEVCUT — dokunulmaz) | bağlam + mevcut durum + komşu ilişkiler + protokol; 38 satır | Şablon kuralı üst klasörden ayrılır | Yalnız vault-sync (MO) |
| `AGENTS.md` | Dosya sahipliği, yetki sınırları ve routing | rol tabloları + yasaklar (doküman bu oturumda okundu) | Kimin hangi dosyaya dokunacağı baştan belli olsun | Agent registry revizyonunda (MO) |
| `WORKFLOW.md` | Görev akışı ve kapılar (G1-G6) | 8 adım tablosu + kapı tabloları; 201 satır | Onaysız değişiklik yapılmasın | Süreç değişince (MO) |
| `CONTEXT.md` (bu dosya) | Klasör envanteri + çelişki kaydı | 7 bölüm + 5 sütunlu envanter tablosu | Sayılar tek yerde, ölçümle dursun | Envanter ölçümü değişince |
| `workflows/` (dizin) | GitHub'ın okuduğu otomatik kontrol tanımları | 2 `.yml` dosyası | Kapılar ayrı klasörde GitHub tarafından taranır | Yeni kapı eklenince/silinince |
| `ISSUE_TEMPLATE/` (dizin) | Issue şablonları | 2 dosya (form + alt kural) | GitHub formu bu klasörden okunur | Şablon eklenince/silinince |

**`workflows/ci.yml`**
> **eli10 (basit):** Depoya her girişte çalışan otomatik sınav: kod denetimi, testler, güvenlik kontrolü ve JS testleri.
> **eli15 (detay):** Ayrı yml'dir çünkü GitHub onu doğrudan okur; PHP kodu içine gömülse çalışmaz. Okunması, hangi kapıların zorunlu ve hangilerinin bloklamayan (soft) olduğunu gösterir. CI davranışı değişince kullanıcı onayıyla düzenlenir. Job kaldırılırsa o kapı tamamen kalkar ve hatalı kod depoya girer.

**`workflows/secret-scan.yml`**
> **eli10 (basit):** Depoya yanlışlıkla yazılmış şifre ve anahtarları her girişte tarar.
> **eli15 (detay):** Ayrı dosyadır çünkü tek işi vardır: güvenlik taraması; testten ayrılınca test kapısı kırılsa bile tarama çalışır. Okunması, tüm geçmişin tarandığını (`fetch-depth: 0`) ve hangi aksiyonun kullanıldığını gösterir. Tarama politikası değişince düzenlenir. Atlarsan anahtar sızıntısı fark edilmez.

**`ISSUE_TEMPLATE/01-bug-report.md`**
> **eli10 (basit):** Hata bildiriminde doldurulan hazır form; etiketleri de frontmatter'da yazılıdır.
> **eli15 (detay):** Ayrı durur çünkü GitHub onu issue oluştururken ayrı sunar; başka dosyaya gömülse kullanılmaz. Okunması, hangi bilgilerin istendiğini ve etiketleri gösterir. Rapor alanları değişince düzenlenir. Form bozulursa eksik rapor gelir ve hata tekrarlanamaz.

**`CLAUDE.md` (kök)**
> **eli10 (basit):** Bu klasörde çalışırken uyulacak kuralların yazılı olduğu defter.
> **eli15 (detay):** Kural ile tanım farklı hızda değişir; kod içine gömülse her revizyonda kural aranır. Okunması, klasörün protokolünü ve dokunulmaz yüzeyleri gösterir. Yalnız vault-sync (MO) tarafından düzenlenir. Elle değişilirse SSOT hiyerarşisi ve git geçmişi bozulur.

**`ISSUE_TEMPLATE/CLAUDE.md`**
> **eli10 (basit):** Şablon klasörüne ait kısa kural defteri.
> **eli15 (detay):** Alt dizin kuralı ayrı durur ki üst klasör değişince şablon kuralı sessizce değişmesin. Okunması, şablon ekleme protokolünü gösterir. Yalnız vault-sync (MO) tarafından düzenlenir. Silinirse şablon kuralı güvencesiz kalır.

**`AGENTS.md`**
> **eli10 (basit):** Bu klasörde kimin hangi dosyaya dokunabileceğini yazar.
> **eli15 (detay):** Yetki ile dosya içeriği farklı hızda değişir; yml içine gömülse her CI revizyonunda rol revize edilir. Okunması, işe başlamadan sorumluyu ve yasakları gösterir. Agent registry revizyonunda MO günceller. Sahip belirsiz olunca herkes düzenler ve CI sessizce kırılır.

**`WORKFLOW.md`**
> **eli10 (basit):** Bir görevin kapı kapı nasıl yürütüleceğini anlatan adım listesi.
> **eli15 (detay):** Süreç ile dosya içeriği farklı hızda değişir; ci.yml içine gömülse her revizyonda süreç aranır. Okunması, onay ve ölçüm kapılarını (G1-G6) önceden gösterir. Süreç değişince MO günceller. Kapı atlanırsa onaysız değişiklik doğrudan depoya girer.

**`CONTEXT.md` (bu dosya)**
> **eli10 (basit):** Klasörün kaç dosya içerdiğini ve eski bilgilerle disk arasındaki farkları yazan kayıt.
> **eli15 (detay):** Ayrı yazıldı çünkü sayı iddiaları tek yerde ve ölçümle durmalıdır. Okunması, envanter ve çelişki kaydını verir; kimse tahminle "kaç workflow var" sorusunu cevaplamaz. Envanter ölçümü değişince düzenlenir. Bu dosya olmazsa eski sayılar yeni oturumları yanlış yönlendirir.

**`workflows/` (dizin)**
> **eli10 (basit):** GitHub'ın okuduğu otomatik kontrol dosyalarının klasörü.
> **eli15 (detay):** Dizin ayrıdır çünkü GitHub bu klasörü doğrudan tarar; dosyalar köke serilirse eşleşme bozulur. Okunması, kapı sayısını tek bakışta gösterir. Yeni kapı eklenince ya da kaldırılınca değişir. Klasör silinirse tüm otomatik kontroller durur.

**`ISSUE_TEMPLATE/` (dizin)**
> **eli10 (basit):** Hata bildirim formlarının durduğu klasör.
> **eli15 (detay):** Dizin ayrıdır çünkü GitHub issue ekranında bu klasördeki dosyaları form olarak sunar. Okunması, kaç şablon olduğunu gösterir. Yeni form eklenince değişir. Klasör silinirse standart hata raporu toplanamaz.

### 3.2 CI Pipeline Yapısı (`ci.yml` — 203 satır, 6 job)

| Job | Ne yapar | Disk kanıtı (ci.yml) |
|-----|----------|---------------------|
| `php-lint` | 3 dizine `composer install` + `composer stan` (PHPStan) | `working-directory`: `shared`, `auth.coremusic.net`, `home.coremusic.net`; timeout 15 dk |
| `php-test` | `needs: php-lint`; matrix 2 satır: `shared` (tümü), `auth.coremusic.net` (`--testsuite Unit`) | matrix `include` yorumu: `tests/Integration` dizini diskte yok → yalnız Unit; timeout 15 dk |
| `composer-audit` | 3 dizinde `composer audit` | 3 ayrı `working-directory` adımı; timeout 10 dk |
| `js-test` | `npm ci` + `npm run test:js` (Node 26) | `actions/setup-node@v4`; timeout 10 dk |
| `js-coverage` | `npm run test:js:coverage`; `continue-on-error: true` (soft kapı) | `needs: js-test`; yorum: %80 eşiği erişilemez → görünür-kırmızı; timeout 15 dk |
| `js-e2e` | Playwright chromium + `npm run test:e2e`; `continue-on-error: true` | yorum: e2e lock'a alındı → soft job; timeout 20 dk |

Ortak ayar: `on: push` + `pull_request` · `permissions: contents: read` · `concurrency` (cancel-in-progress) · `env: PHP_VERSION: '8.4'`. Soft job: 2 (`js-coverage`, `js-e2e`).

> **eli10 (basit):** CI altı adımda çalışır: denetim, PHP testleri, bağımlılık denetimi ve üç JS adımı.
> **eli15 (detay):** Job'lar ayrı ayrı tanımlanmıştır çünkü bağımlıdırlar: test, lint geçmeden çalışmaz (`needs`). Okunması, hangi dizinlerin gerçekten test edildiğini ve hangi kapıların bloklamadığını gösterir. Yazılması, disk kanıtıyla (composer.json/phpunit konumu) hizalanır. Job kaldırılırsa o kapı kalkar ve hatalı kod depoya girer.

### 3.3 Secret Scan (`secret-scan.yml` — 31 satır, 1 job)

| Öğe | Değer | Kanıt |
|-----|-------|-------|
| Job | `gitleaks` (timeout 10 dk) | `jobs.gitleaks` |
| Checkout | `fetch-depth: 0` (tüm geçmiş taranır) | `with.fetch-depth` + şablon §10 yorumu |
| Aksiyon | `gitleaks/gitleaks-action@v2` + `GITHUB_TOKEN` | `env` bloğu |
| Config | `.gitleaks.toml` repo kökünde MEVCUT (glob ölçümü 2026-10-12); dosya satır 4 yorumu "YOK" der → yorum eski (§3.5 #6) | `.gitleaks.toml` disk kanıtı |

> **eli10 (basit):** Bu dosya, depoya yanlışlıkla yazılmış şifre ve anahtarları her girişte tarar.
> **eli15 (detay):** CI testinden ayrıldı çünkü güvenlik taraması testten bağımsız çalışmalı; test kapısı kapalıyken bile sızıntı avlanır. Okunması, tüm geçmişin tarandığını ve hangi aksiyonun çalıştığını gösterir. Config dosyası diskte bulunduğu için yml yorumundaki "YOK" ifadesi eski sayılır. Atlarsan anahtar sızıntısı fark edilmez.

### 3.4 Boot Okuma Sırası ve Kök ↔ Vault İlişkisi

```text
kök (AGENTS.md + CLAUDE.md) → .github/ (CLAUDE.md → AGENTS.md → WORKFLOW.md → CONTEXT.md)
  → .ai/ vault yalnız ihtiyaç anında (§7'deki hedeflerle)
```

| Bu klasör dosyası | Otorite satırı (frontmatter ölçümü) | Taşıdığı bilgi |
|-------------------|--------------------------------------|----------------|
| `.github/CLAUDE.md` | `authority: Single Source of Truth (SSOT)` — kendi iddiası | Klasör protokolü + değişiklik kuralları |
| `.github/AGENTS.md` | `authority: "Derived — SSOT: .ai/AGENTS.md + kök AGENTS.md"` | Dosya sahipliği + yetki (SSOT → [[../.ai/AGENTS.md]]) |
| `.github/WORKFLOW.md` | `authority: "Derived — SSOT: .ai/WORKFLOW.md + kök AGENTS.md"` | Adım → kapı → rapor (SSOT → [[../.ai/WORKFLOW.md]]) |
| `.github/CONTEXT.md` | `authority: "Derived — SSOT: .ai/ + kök AGENTS.md"` | Envanter + çelişki kaydı (iskelet → §7 şablonlar) |
| `.github/ISSUE_TEMPLATE/CLAUDE.md` | `authority: Single Source of Truth (SSOT)` — kendi iddiası | Şablon protokolü (parent → `[[../CLAUDE.md]]`) |

> **eli10 (basit):** Her dosyanın kimi takip ettiği frontmatter'da yazılıdır; üçü vault'taki ana dosyadan türetmiştir.
> **eli15 (detay):** İlişki ayrı tabloda çünkü çelişkide hangi kaynağa bakılacağı belli olsun: türev dosyalar kazanamaz, SSOT kaynağı kazanır. Okunması, boot sırasını ve otorite zincirini gösterir. Yazılması, klasör dosyalarının ikinci SSOT sanılmasını engeller. Tablo unutulursa her dosya kendi iddiasıyla bağımsız çalışır ve bilgi ayrışır.

### 3.5 Çelişki Kaydı (vault ↔ disk — disk kazanır)

| # | İddia (kaynak, okuma ölçümü 2026-10-12) | Disk ölçümü (2026-10-12) | Karar |
|---|------------------------------------------|---------------------------|-------|
| 1 | `.github/CLAUDE.md` §2: "Workflow (Actions) — Yok" (2026-09-06) | `workflows/` içinde 2 dosya: `ci.yml` (203 satır), `secret-scan.yml` (31 satır) | **Disk kazanır** — CLAUDE.md dokunulmaz; düzeltme vault-sync'e rapor edilir |
| 2 | Eski `CONTEXT.md` (2026-10-03 nüshası): "ci.yml 139 satır, 3 job" | `ci.yml` 203 satır, 6 job (dosya yorumu: "WP2/J1 — 2026-10-07 JS test altyapısı") | **Disk kazanır** — bu dosya yeniden yazıldı |
| 3 | Eski `CONTEXT.md`: "5 dosya" envanter | 8 dosya, 2 alt dizin | **Disk kazanır** |
| 4 | Eski `CONTEXT.md`: "secret-scan.yml 32 satır" | 31 satır | **Disk kazanır** |
| 5 | `WORKFLOW.md` §7: "ci.yml — 3 job", "GitLeaks job (32 satır)" | 6 job; 31 satır | **Disk kazanır** — WORKFLOW.md bu görevde değiştirilmez, yalnız işaretlenir |
| 6 | `secret-scan.yml` satır 4 yorumu: "diskte .gitleaks.toml YOK" | `.gitleaks.toml` repo kökünde MEVCUT | **Disk kazanır** — yorum eski; yml değişikliği DevOps + onay gerektirir |

> **eli10 (basit):** Eski dosyalarda "workflow yok" ve eski satır sayıları yazıyor; gerçeği disk söyler.
> **eli15 (detay):** Çelişki tablosu ayrı tutulur çünkü dokunulmaz dosyalar düzeltilemez, yalnız işaretlenir. Okunması, hangi bilginin eski olduğunu gösterir. Yazılması, ölçüm kanıtıyla olur. Bu tablo olmazsa eski "Yok" ve "3 job" bilgisi yeni oturumları yanlış yönlendirir.

### 3.6 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur (aynı dosyada toplarsak) |
|---|---------|----------|----------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur (yml → DevOps, form → doküman) | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde durur (ör. PHP 8.4 tek `env` bloğunda) | Her job'da ayrı değer → sürüm farkı, tarama gerekir |
| 4 | **Cihaz izolasyonu** | Ortam/runner farkı başka dosyalara yayılmaz | Ortam farkı tüm işlere sıçrar, tutarsızlık (drift) doğar |
| 5 | **Vendor karantinası** | 3. taraf aksiyonlar (checkout, setup-php, gitleaks) kendi yml'lerinde ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: CI kapısı `ci.yml`'de, tarama `secret-scan.yml`'de, rapor formu `ISSUE_TEMPLATE/` altında durur. Böylece biri değişince sadece o değişir, diğer kapılar bozulmaz. Tek dosyada toplansa test kapısı ile secret kapısı birbirine karışır. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her sayısal iddia bu oturumun ölçümüyle kanıtlanır; kanıt yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 2 | Çelişki (vault ↔ disk) → **disk kazanır** + §3.5 tablosunda işaretlenir | SSOT ihlali |
| 3 | `.yml` değişikliği yalnız DevOps Engineer + kullanıcı onayı ile girer | Onaysız CI kapısı çalışır veya kapanır |
| 4 | Yeni workflow, `.ai/.templates/infrastructure/github-actions-template.md`'den türetilir (Guardrail #16) | Standart dışı pipeline doğması |
| 5 | Secret değerleri vault `.md` dosyalarına YAZILMAZ; GitHub repo secrets'ta yaşar | Anahtar sızıntısı |
| 6 | `CLAUDE.md` (kök + `ISSUE_TEMPLATE/`) bu klasörde değiştirilmez; düzeltme vault-sync'e rapor edilir | Dokunulmazlık ihlali |
| 7 | Wiki-link `[[...]]` formatı; diskte olmayan hedef üretilmez | Kırık link |
| 8 | Yeni doküman §1–§7 sırası ve şablon frontmatter'i ile üretilir (Guardrail #16) | Şablon ihlali |
| 9 | Subagent `git commit` ATMAZ — rapor orkestratöre gider | Yetkisiz commit, revert riski |

> **eli10 (basit):** Bu kurallar, depo ayarlarının izinsiz ve gizli bilgi sızdıracak şekilde değişmesini engeller.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü kural ile `.yml` farklı hızda değişir; yml içine gömülse her CI revizyonunda kural revize edilir. Okunmaları, bu klasörde işe başlamadan önce sınırları gösterir. Yazılmaları, denetimin tekrarlanabilir olmasını sağlar. Kural değişince yalnız bu tablo güncellenir; CI dosyaları sessizce değişmez.

---

## 5. Workflow

```text
ÖLÇ (envanter — ilk okumada) → YAZ (şablon iskeleti, yalnız CONTEXT.md)
  → ÇELİŞKİ KAYDI (vault ↔ disk → disk kazanır) → DOĞRULAMA (§6 tablosu)
  → İLGİLİ INDEX'E 1 SATIR → RAPOR (commit ORCHESTRATÖRDE — subagent atmaz)
```

| # | Adım | Çıktı | Neden | Atlarsan ne olur |
|---|------|-------|-------|------------------|
| 1 | Kural oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör yasakları baştan belli olsun | Yasak ihlali, gizli bilgi riski |
| 2 | Envanter ölç (glob + okuma; sayı uydurulmaz) | Ölçüm tablosu | Sayı iddiası ölçümle kanıtlanır | Halüsinasyon, yanlış "eklendi/silindi" iddiası |
| 3 | Şablondan yaz (§1–§7, 5 sütun, eli10/eli15) | Bu dosya | Tutarlı iskelet (Guardrail #16) | Şablon dışı, denetlenemez dosya |
| 4 | Çelişki tablosunu güncelle (§3.5) | Karar satırları | Eski bilgi yeni oturumu yanıltır | "Workflow yok" efsanesi sürer |
| 5 | §6 doğrulama tablosunu çalıştır | 12/12 | Teslimden önce kanıt | Hatalı dosya teslim edilir |
| 6 | İlgili index'e 1 satır + rapor; **commit ATMA** | Kayıt + rapor | Yetki sınırı (kök `AGENTS.md` §7/§8) | Yetkisiz commit, revert |

> **eli10 (basit):** İş sırayla yürür: dosyaları say, şablona göre yaz, farkları kaydet, kontrol et ve haber ver; commit'i sen atmazsın.
> **eli15 (detay):** Adımlar ayrı satırlarda çünkü her adımın çıktısı ve "atlarsan ne olur" gerekçesi ölçülür. Okunması, kapıları önceden bilmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Adım atlanırsa (özellikle ölçüm ve doğrulama) hatalı iddia dosyaya girer ve sonradan pahalıya bulunur.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | Şablon alanları + `docType: context` (frontend şablon §3.2) |
| 2 | Bölüm yapısı | §1–§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Değişken yer tutucusu (çift süslü parantezli desen) kalmadı |
| 4 | Envanter | 8 dosya, 2 alt dizin, 6+1 job iddiası bu oturum ölçümüyle uyumlu |
| 5 | Disk kanıtı | Her sayı glob/okuma ölçümünden gelir; kanıtsız iddia `UNKNOWN` |
| 6 | Wiki-link | `[[...]]` formatı; §7 hedefleri diskte mevcut |
| 7 | eli10 + eli15 | Her envanter maddesi + §1, §2, §3.2–§3.6, §4, §5 bloklarında çift var (eli10 en fazla 2 cümle, eli15 3-4 cümle) |
| 8 | Çelişki kuralı | §4'te "disk kazanır" var |
| 9 | Boot sırası | Kök ↔ vault ilişkisi tablosu §3.4'te var |
| 10 | Kaldırılan atıf | Yalnız §1'deki tek satırlık kaldırıldı notu; başka mimari atıf yok |
| 11 | Emoji | Dekoratif emoji yok |
| 12 | Uzunluk | 300–500 satır bandı (frontend şablon §4.2) |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Bu klasör boot kuralı (MEVCUT — okunur, değişmez) |
| Klasör rolleri | [[AGENTS.md]] | Dosya sahipliği + yetki sınırları |
| Klasör süreci | [[WORKFLOW.md]] | Adım → kapı → rapor |
| Alt dizin kuralı | [[ISSUE_TEMPLATE/CLAUDE.md]] | Şablon protokolü (MEVCUT) |
| Issue şablonu kanıtı | [[ISSUE_TEMPLATE/01-bug-report.md]] | Form envanteri dayanağı |
| CI iş kanıtı | [[workflows/ci.yml]] | 6 job disk kanıtı |
| Secret tarama kanıtı | [[workflows/secret-scan.yml]] | GitLeaks job |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Vault anayasası | [[../.ai/CLAUDE.md]] | Hard Guardrails |
| Agent registry (SSOT) | [[../.ai/AGENTS.md]] | §5: `*.yml` → DevOps Engineer |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Audit trail | [[../.ai/log.md]] | Değişiklik kaydı (append) |
| Workflow şablonu | [[../.ai/.templates/infrastructure/github-actions-template.md]] | Guardrail #16 kaynağı (ci.yml satır 2 yorumu) |
| Bağlayıcı iskelet | [[../.ai/.templates/frontend/context-template.md]] | §1–§7 + eli10/eli15 (çelişkide bu kazanır) |
| CONTEXT şablonu | [[../.ai/.templates/documentation/context-md-template.md]] | Envanter tablosu formatı + sayım disiplini |
| Template registry | [[../.ai/.templates/index.md]] | Şablon kayıt satırı |
| Deployment akışı | [[../.workflows/deployment.md]] | CI kapısını tamamlar |

---

**Template Version:** 1.0.0 · **Şablonlar:** `.ai/.templates/frontend/context-template.md` + `.ai/.templates/documentation/context-md-template.md`
**Last Updated:** 2026-10-12
