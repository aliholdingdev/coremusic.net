---
title: "CoreMusic — media.coremusic.net Rolleri & Routing"
type: docs
category: media
docType: agents
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/AGENTS.md (v22.x) §5/§6/§9"
---

# media.coremusic.net — AGENTS.md

**docType:** agents · **Klasör:** `media.coremusic.net/` · **Sorumlu:** MO (registry)

**Zorunlu Bağlantılar:** [[CONTEXT]] · [[CLAUDE]] · [[WORKFLOW]] · [[../AGENTS.md]] · [[../.ai/AGENTS.md]]

---

## 1. Amaç

Bu doküman `media.coremusic.net/` klasöründe **kimin hangi dosyaya dokunacağını** ve hangi durumda kime handover yapılacağını tanımlar. Klasör PHP CLI arşiv aracı olduğundan (statik servis değil) routing, kök registry §6 keyword satırlarının **bu klasöre daraltılmış** hâlidir; çelişkide kök registry kazanır.

| Karar | Kaynak (disk) |
|-------|---------------|
| `*.php` → Backend Architect (birincil) | [[../AGENTS.md]] §5 Domain Boundaries |
| `*.sql` / `media_catalog` → Data Engineer | [[../AGENTS.md]] §5 · §6 database satırı |
| `tests/**` → QA Engineer (bu klasörde `tests/` **YOK**) | [[../AGENTS.md]] §5 — diskte `tests/` = false |
| vault / doküman / ADR → MO (vault-updater) | [[../AGENTS.md]] §6 "vault, documentation, ADR" |
| secret / hash / kopya kilidi denetimi → Security Engineer | [[../AGENTS.md]] §6 security satırı + [[CLAUDE]] §3 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `media.coremusic.net/` içi dosya → rol eşlemesi | `shared/src/` ortak katman (bağımlı olunan) |
| Handover tetikleyicileri + öncelik | ADR metinleri (dokunulmaz, yalnız referans) |
| Bu klasördeki domain okuması (§3.5) | Faz 3 GUI uygulaması → `docs/faz3-gui-spec.md` sahibi (⚠️ Ayrı görev) |
| Kök §6 keyword daraltması | Genel vault routing (SSOT = kök registry) |

- **Kullananlar:** MO (routing), atanan agent (yürütme).
- **Ön koşul:** [[CONTEXT]] (envanter) okunmuş; hedef dosya diskte mevcut.

---

## 3. Mimari

### 3.1 Rol Tablosu — bu klasörde kim ne yapar

| # | Rol / Agent | Kod | Bu klasördeki işi (ne için) | Yetki sınırı (neye dokunamaz) | Ne zaman devreye girer (tetikleyici) |
|---|-------------|-----|------------------------------|--------------------------------|--------------------------------------|
| 1 | **Backend Architect** | `backend` | `bin/*.php` + `src/Media/*.php` CLI kodu — tarama/denetim/taşma akışının sahibi | `config/*` yalnız veri sözleşmesi ise birlikte; `.gitignore` yalnız MO+Security onayı; ADR metni | Yeni komut, bayrak, kural kimliği veya sınıf gerektiğinde |
| 2 | **Data Engineer** | `data` | `media_catalog` DB: `.ai/.sql/mysql/media_catalog.sql` (9 tablo + `v_asset_search`), `scan.php` DB yazımı, `taxonomy` senkronu | PHP CLI kodu (Backend); `media/` ağacı (dokunulmaz) | Şema/indeks/enum değişikliği; `SELECT *`/drift şüphesi |
| 3 | **Security Engineer** | `security` | Hash kuralı (sha256 yalnız girişte), `ingest` onay/kilit denetimi, yasaklı çağrı taraması, secret/REDACTED | İş mantığı kodu (düzeltmez, denetler); `media/` içeriği | Kopya/taşma kuralı, secret şüphesi, güvenlik denetimi talebi |
| 4 | **QA Engineer** | `qa` | `php -l` 8/8 · CLI `--dry-run`/`--deep` kapıları · `checklist.md` kanıt koşma | Üretim kodu düzeltmez (bulguyu handover eder); şema | Kod değişti; kapı kanıtı istendi; `⚠️ VR` maddesi kapanacak |
| 5 | **DevOps Engineer** | `devops` | CI/lint pipeline'ı (`composer lint`), deploy/port :5000/:6000 (ADR-039) | `bin/`·`src/` iş mantığı; `media/` | Pipeline/lint kapısı; sunucu/port kuralı |
| 6 | **MO (Master Orchestrator)** | `mo` | Routing, 4 klasör dokümanı, `log.md` append, context lock, **commit** | Üretim kodu + config (kod yazmaz) | Görev başında (routing) · kapanışta (commit) · lock çatışmasında |

**eli10/eli15 blokları (§3.1 — 6 rol):**

**Backend Architect (`backend`)**
> **eli10 (basit):** Üç komutu ve arkalarındaki kütüphaneyi yazan, bozan, geliştiren ana geliştirici.
> **eli15 (detay):** Bu rol tutuldu çünkü klasörün tamamı PHP CLI; kod sahibi tek olmalı. Yeni komut ya da kural kimliği gerektiğinde devreye girer. Kod dosyalarını okur ve yazar, ADR metnine dokunmaz. Aksi hâlde kimin hangi komutu değiştirdiği bilinmez.

**Data Engineer (`data`)**
> **eli10 (basit):** Arşivin arama listesini (veritabanını) kuran ve senkron tutan kişi.
> **eli15 (detay):** Ayrı rol çünkü JSON ile MySQL iki ayrı hızda değişir; ikisini de tek elde tutmak veri kaybı riski doğurur. `media_catalog.sql` ve `scan.php` DB yazımı gerektiğinde girer. Şemayı okur, CLI koduna dokunmaz. Drift (liste kayması) ancak böyle yakalanır.

**Security Engineer (`security`)**
> **eli10 (basit):** Taşıma ve hash kurallarının ihlal edilmediğini denetleyen uzman.
> **eli15 (detay):** Ayrı rol çünkü denetim bağımsız olmalı; yapan kişi kendi hatasını denetlemez. Hash kuralı, onay kilidi ya da secret şüphesinde devreye girer. Kodu okur ama düzeltmez, bulguyu handover eder. Sınır bozulursa denetim değeri kalmaz.

**QA Engineer (`qa`)**
> **eli10 (basit):** Kapıları (kontrolleri) koşup kanıt üreten testçi.
> **eli15 (detay):** Ayrı tutuldu çünkü kanıt ile üretim aynı elde olursa "geçti" anlamı kaybolur. Kod değişince ya da `⚠️ VR` kapanacakken girer. `php -l` ve dry-run çıktısını okur, koda dokunmaz. Kanıtsız kapanan kapı tekrar açılmalıdır.

**DevOps Engineer (`devops`)**
> **eli10 (basit):** Lint'in CI'da da koşmasını ve sunucu ayarlarını sağlayan kişi.
> **eli15 (detay):** Ayrı rol çünkü pipeline ve port kuralı (ADR-039 :5000/:6000) sunucu hızında değişir. Pipeline ya da port kararı gerektikçe girer. Kod dosyalarını okur, iş mantığını değiştirmez. Kodsuz kalırsa lint yalnız yerelde koşar, kapı kaçar.

**Master Orchestrator (`mo`)**
> **eli10 (basit):** Kimin ne yapacağını ayarlayan, dört dokümanı yazan ve işi tek elden commit eden koordinatör.
> **eli15 (detay):** Routing ve commit tek elde olmalı ki dağınık değişiklik bir araya gelmesin. Görev başında ve kapanışta devreye girer, lock çatışmasında arabulucudur. Dokümanları yazar, kod yazmaz. Aksi hâlde tarih ve iz sürme bozulur.

### 3.2 Dosya → Birincil Rol Eşlemesi (hızlı routing)

| Dosya / Desen | Birincil | İkincil / denetim |
|---------------|----------|-------------------|
| `bin/scan.php` | Backend Architect | Data Engineer (DB yazımı) + QA (kapı) |
| `bin/audit.php` | Backend Architect | QA Engineer (kanıt) + Security (hash kuralı) |
| `bin/ingest.php` | Backend Architect | **Security Engineer (onay kilidi, zorunlu)** + Data (`path_history`) |
| `src/Media/*.php` | Backend Architect | QA (lint/test) |
| `config/media.schema.json` | Data Engineer | Backend Architect (`Validator` eşlemesi) |
| `config/taxonomy.json` | Data Engineer | Backend (`Taxonomy.php` senkronu) + QA (enum senkron) |
| `config/mojibake-fix.json` | Backend Architect | QA (eşleşme sayısı kanıtı) |
| `composer.json` / `.gitignore` | Backend Architect + MO | Security (negasyon + secret yok) |
| `docs/checklist.md`, `docs/todos.md` | MO (vault-updater) | QA (kanıt) + kapı sahipleri |
| `docs/dizin-yapisi.md`, `docs/adlandirma.md` | MO (vault-updater) | Backend (kod↔kural eşlemesi) |
| `docs/faz3-gui-spec.md` | UI Designer / frontend-developer | MO (kapı kaydı) — ⚠️ uygulama ayrı görev |
| `media/` (medya ağacı) | **KİMSE — salt okunur** | Security (master dokunulmazlık) |
| `CONTEXT`·`CLAUDE`·`AGENTS`·`WORKFLOW.md` | MO (vault-updater) | — |

**eli10/eli15 blokları (§3.2 — eşleme özeti):**

> **eli10 (basit):** Hangi dosyayı açacaksan önce bu tabloya bak; hangi masanın işi olduğu orada yazılı.
> **eli15 (detay):** Eşleme ayrı tablodur çünkü dosya adı tek bakışta rolü söyler, uzun rol tablosunu okumaya gerek kalmaz. Okunması, layer violation'ı baştan engeller (yanlış masaya giren dosya revert edilir). Yazılması, yeni dosya eklenince rolün netleşmesini sağlar. Tablo olmasa herkes her dosyaya dokunur.

### 3.3 Handover Tetikleyicileri (bu klasörden çıkarken)

| Tetikleyici | Kaynak rol | Hedef rol | Öncelik |
|-------------|-----------|-----------|---------|
| `ingest` kopya kilidi zayıflığı / onay akışı şüphesi | Backend | **Security** | HIGH |
| Secret/anahtar bir `.md`/`.json`/`.log` içinde bulundu | İlk farkeden | Security → MO (REDACTED) | CRITICAL |
| `media_catalog` ↔ `taxonomy.json` enum drift'i | Data | Backend (`Taxonomy.php`) | HIGH |
| `php -l` veya CLI kapısı kırmızı | QA | Backend | MEDIUM |
| `scan.php` DB yazımı `SELECT *` / indekssız sorgu | Data | Data (kendisi düzeltir) | MEDIUM |
| Master `.flac`/`.wav` byte değişikliği tespiti | İlk farkeden | Security + MO | CRITICAL |
| Pipeline/lint `composer lint` kırmızı | DevOps | Backend | MEDIUM |
| Vault ↔ disk çelişkisi (§3.5) | İlk farkeden | MO (disk kazanır + `⚠️ VR`) | LOW |

**eli10/eli15 blokları (§3.3 — handover özeti):**

> **eli10 (basit):** İşin kendi masasını aştığı an, doğru masaya yazılı teslim yapılır.
> **eli15 (detay):** Handover tablosu ayrıdır çünkü yetki sınırı anlık durumlarda belirlenemez. Öncelik (HIGH/CRITICAL) ne kadar çabuk gidileceğini söyler. Onaysız tamamlanmaz (kök `AGENTS.md` §9.2). Tablo olmasa güvenlik bulgusu kaybolur.

### 3.4 Senaryo → Agent → Sonuç Matrisi

| # | Senaryo (tetikleyen) | Birincil | Sonuç / not |
|---|----------------------|----------|-------------|
| 1 | Yeni PHP dosyası/komut | Backend Architect | `php -l` 8/8 + `composer lint` yeşil |
| 2 | Yeni meta alanı / enum | Data Engineer | `media.schema.json` + `taxonomy.json` + checklist kanıtı |
| 3 | Taşma (`ingest`) çalıştırma | Backend + **Security** | dry-run CSV → onay → `--commit` |
| 4 | `⚠️ VR` maddesi kapanacak | QA Engineer | tek komutluk `Denet:` satırı koşulur |
| 5 | `media/` veya `catalog/` git'e girdi | MO + Security | `.gitignore` negasyonu denetlenir, gerekirse revert |
| 6 | Faz 3 GUI ekranı | UI Designer | `.ai/ui-design/` Kalıp D (Guardrail #16) — spec `docs/`'ta kalır |
| 7 | Port :5000/:6000 netleştirme | MO (vault-updater) | ADR-039'a not → `checklist` VR-7 |
| 8 | Vault ↔ disk çelişkisi | İlk farkeden | `⚠️ VERIFICATION REQUIRED` + disk kazanır + MO |

**eli10/eli15 blokları (§3.4 — senaryo özeti):**

> **eli10 (basit):** Sık karşılaşılan sekiz durum ve kimin çözeceği burada hazır yazılı.
> **eli15 (detay):** Senaryo tablosu ayrıdır çünkü anlık karar gerektiren durumlar rol tablosundan daha hızlı okunmalıdır. Her satırda tetikleyici ile birlikte beklenen sonuç da vardır. Uygulanınca iş dağılmaz. Tablo olmasa her olay için yeniden routing yapmak gerekir.

### 3.5 §24.3 Domain Okuması — bu klasörün satırı

| # | Zorunlu okuma | Ne için |
|---|---------------|---------|
| 1 | `docs/dizin-yapisi.md` + `docs/adlandirma.md` | Dizin ekseni + 6 adlandırma kuralı (kod öncesi) |
| 2 | `config/media.schema.json` + `config/taxonomy.json` | Veri sözleşmesi + kapalı enum |
| 3 | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Karar kaynağı (§6.1/§7.1 kabul kriterleri) |
| 4 | [[CONTEXT]] | Ölçülmüş envanter + çelişki kaydı |

> Ek: DB işi → `.ai/.sql/mysql/media_catalog.sql` · kapı işi → `docs/checklist.md` + `docs/todos.md` · şablon → `../.ai/.templates/frontend/context-template.md`.

**eli10/eli15 blokları (§3.5):**

> **eli10 (basit):** Bu klasörde işe başlamadan önce dört yazının okunması gerekiyor.
> **eli15 (detay):** Okuma listesi ayrıdır çünkü hangi belgenin ne verdiği bellidir ve toplu okuma token israfıdır (kök `AGENTS.md` §9 on-demand kuralı). Kod öncesi okunmazsa kural yeniden keşfedilir. Liste değişince bu satır güncellenir. Atlanırsa hallüsinasyon riski doğar.

### 3.6 Escalasyon (bu klasöre özgü — kök `AGENTS.md` §10)

| Durum | Seviye 1 | Seviye 2 | Seviye 3 |
|-------|----------|----------|----------|
| `php -l` 3 kez kırmızı (aynı dosya) | Backend düzeltir | MO: DUR + 1 soru | — |
| Master `.flac`/`.wav` byte değişikliği | Security | MO → İnsan | İnsan |
| `taxonomy.json` ↔ `media.schema.json` drift | Data | Backend (`Taxonomy.php`) | MO |
| `media/` veya `catalog/` git'e girdi | İlk farkeden → MO | revert + `.gitignore` denetimi | — |
| Secret `.md` içinde bulundu | Security | MO (REDACTED) | İnsan |
| Faz 3 breakpoint/port kararı (checklist VR-5/VR-7) | MO | ui-designer / vault-updater | İnsan (onay) |

### 3.7 Context Lock — Eşzamanlı Erişim

| Kural | Değer |
|-------|-------|
| Kilitleme süresi | max 30 sn |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Deadlock | MO en eski kilidi kırar |
| Logging | acquire/release → `../.ai/log.md` |

**eli10/eli15 blokları (§3.6-§3.7):**

**Escalasyon (§3.6)**
> **eli10 (basit):** Aynı hata üç kez düzeltilemezse durulur ve soru sorulur.
> **eli15 (detay):** Seviyeler ayrıdır çünkü kör deneme döngüsü hem zaman hem güven kaybıdır. L1 domen sahibi, L2 MO, L3/İnsan son çaredir. Süreler kök registry §10.2'dedir. Uygulanmazsa hata karanlıkta çoğalır.

**Context Lock (§3.7)**
> **eli10 (basit):** İki oturum aynı dosyaya birden yazamasın diye kilit.
> **eli15 (detay):** Kilit ayrı bir mekanizmadır çünkü eşzamanlı oturumlar gerçektir (figma/ui-design oturumu repoda). Süre ve öncelik tabloda sabittir. Çatışmada MO arabulucudur. Kilit olmasa iki yazma birbirini ezer.

### 3.8 İçerik Neden Dosyalara Ayrıldı? (süreç belgesi gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur (her şey tek dosyada olsaydı) |
|---|---------|----------|----------------------------------------------|
| 1 | **Bakım kolaylığı** | Adım değişimi tek satırda kalır, diff küçük | Süreç kod içine gömülür, revizyonda kaybolur |
| 2 | **Tek sorumluluk** | Her kapının tek sahibi olur | "Kapıyı kim kapatacak" cevapsız kalır |
| 3 | **Sözleşme tek kaynak** | Kapı koşulu checklist ile tek yerde | İki ayrı kapı listesi → çelişkili kapanış |
| 4 | **Türev izolasyonu** | `media/` yazma kapısı (G5) ayrı durur | Sıradan adım ile geri alınamaz adım karışır |
| 5 | **Araç karantinası** | Lint/CLI kapısı üretim kodundan ayrı | Yapan kendi kapısını kapatır, denetim değeri düşer |

> **eli10 (basit):** Adımlar küçük kutulara bölünmüş; çünkü bir kutuyu değiştirmek diğerini bozmaz.
> **eli15 (detay):** Süreç ayrı dosyada tutulur ki her klasör kendi kapısını bilsin; `media/` kapısı (taşıma onayı) farklı, `shared/` kapısı farklıdır. Aynı yerde toplansa her iş için tüm süreç okunur, odak kaybolur. Okunması işe sırayla başlamayı sağlar. Bölünmezse "adım atlandı mı" sorusu cevapsız kalır.

| # | Gerekçe | Ne korur | Boşsa ne olur (her şey tek dosyada olsaydı) |
|---|---------|----------|----------------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek satırda kalır, diff küçük | Rol değişimi tüm envanteri kirletir |
| 2 | **Tek sorumluluk** | Her rolün tek sahibi ve tek yetkisi | "Kim yazdı" sorusu cevapsız kalır |
| 3 | **Sözleşme tek kaynak** | Routing kök `AGENTS.md` §6'da tek yerde | İki ayrı routing listesi → çelişki |
| 4 | **Türev izolasyonu** | `media/` erişimi salt-okunur ayrılır | Rol genişliği medyaya sıçrar, master riski |
| 5 | **Araç karantinası** | Denetim (QA/Security) üretimden (Backend) ayrılır | Yapan kendi hatasını denetler, kapı değersizleşir |

> **eli10 (basit):** Roller ayrı yazıldı ki kimin neye dokunacağı baştan belli olsun; karışınca herkes her yere girer.
> **eli15 (detay):** Yetki metni kodla aynı anda değişmez; görev dağılımı ayrı hızda revize edilir. Okunması layer violation'ı önler. Yazması denetimi ve eskalasyonu mümkün kılar. Tek dosyada toplansaydı "bunu kim yazdı, kim denetledi" sorusu cevapsız kalırdı.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Domain boundary: rol yalnız dosya tipine dokunur; ihlal → revert + log ERROR | Layer violation (kök `AGENTS.md` §18) |
| 2 | `media/` ağacı **herkes için salt okunur** — yazma yalnız `ingest.php` üzerinden ve onayla | Master dokunulmazlık (ADR-092 §5) |
| 3 | Denetim yapan rol kodu kendisi düzeltmez, handover eder | Bağımsız denetim (§3.1 #3-#4) |
| 4 | Secret/anahtar hiçbir dokümana yazılmaz → `[REDACTED]` | REDACTED politikası |
| 5 | Vault ↔ disk çelişkisi → disk kazanır + `⚠️ VERIFICATION REQUIRED` + MO | Zero-Hallucination |
| 6 | **Commit subagent ATMAZ** — orkestratörde | Kök `AGENTS.md` §7/§8 |
| 7 | Routing değişikliği yalnız kök `AGENTS.md` §6'da; bu dosya daraltmadır | SSOT hiyerarşisi |

> **eli10 (basit):** Kurallar herkesin kendi masasında kalmasını, denetimin bağımsız olmasını ve commit'in tek elden olmasını söylüyor.
> **eli15 (detay):** Kurallar ayrı yazıldı ki yetki metni kod içine gömülmesin; rol değişince kod değil, bu tablo değişir. Okunması görev başlarken sınır ihlalini engeller. Yazması MO'nun routing'i uygulayabilmesini sağlar. İhlalde revert + `log.md` ERROR işletilir.

### 4.1 Keyword Routing — kök `AGENTS.md` §6 satırının bu klasöre daraltması

| Keyword'ler | Birincil | İkincil |
|-------------|----------|---------|
| PHP, CLI, scan, audit, ingest, composer, sınıf, PSR-4 | **Backend Architect** | Security Engineer |
| database, SQL, BCNF, schema, index, `media_catalog`, taxonomy, enum | **Data Engineer** | Backend Architect |
| hash, sha256, dry-run, onay kilidi, secret, REDACTED, master | **Security Engineer** | Backend Architect |
| test, lint, `php -l`, kanıt, checklist, `⚠️ VR`, coverage | **QA Engineer** | Backend Architect |
| CI/CD, pipeline, port :5000/:6000, deploy, `composer lint` | **DevOps Engineer** | QA Engineer |
| vault, documentation, ADR, wiki-link, CONTEXT/CLAUDE/AGENTS/WORKFLOW | **MO (vault-updater)** | — |

> **eli10 (basit):** Hangi kelime geçtiyse iş o masaya gider; tablo kök registry'den süzülmüş klasör hâlidir.
> **eli15 (detay):** Routing tablosu ayrıdır çünkü iş başlarken kimin çağrılacağı anında belli olmalıdır. Okunması yanlış masaya giden işi baştan engeller. Kök §6 tek SSOT'tur; bu tablo ikinci kaynak değil, süzgeçtir. Yeni keyword doğarsa önce kök registry'ye eklenir, sonra buraya yansıtılır.

---

## 5. Workflow

```text
MO ROUTING (§3.2 eşleme) → ROL ATANIR → CONTEXT + CLAUDE + ilgili docs OKU
→ UYGULA (yalnız kendi dosyası) → KAPI: php -l / CLI --dry-run (QA)
→ HANDOVER varsa §3.3 → RAPOR → MO COMMIT (subagent ATMAZ)
```

Adım ve kapı detayı: [[WORKFLOW]] §3.1 / §3.2.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: agents` |
| 2 | Bölüm | §1–§7, ≤3 başlık seviyesi |
| 3 | Rol tablosu | §3.1 = 6 satır; her satırda "ne için" · "yetki sınırı" · "ne zaman" dolu |
| 4 | Dosya eşlemesi | §3.2 her satırda birincil rol var; `media/` = KİMSE (salt okunur) |
| 5 | Handover | §3.3 8 satır, öncelik sütunu dolu |
| 6 | Routing kaynağı | Kök `AGENTS.md` §6 ile uyumlu (daraltma, ikinci kaynak değil) — §4.1 6 satır |
| 7 | Placeholder | `{{` kalmadı · emoji yok |
| 8 | Wiki-link | `[[...]]` formatı; hedefler diskte var |
| 9 | eli10 + eli15 | §3.1 6 rol + §3.2-§3.8 + §4.1 maddelerinde blok var mı (eli10 ≤2 cümle, eli15 3-4 cümle) |
| 10 | Dokunulmaz | Mevcut dosyalar değişmedi; commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Klasör envanteri | [[CONTEXT]] | Ölçülmüş sayılar |
| Klasör kuralları | [[CLAUDE]] | Yasak + guardrail |
| Klasör süreç | [[WORKFLOW]] | Adım + kapı |
| Agent registry (SSOT) | [[../AGENTS]] | §5 domain · §6 routing · §9 handover · §24.3 okuma |
| Vault agent registry | [[../.ai/AGENTS]] | Agent profilleri + routing |
| Dizin/adlandırma kararı | [[../.ai/.decisions/accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Kural kaynakları |
| Servis yerleşimi | [[../.ai/.decisions/accepted/ADR-039-7-service-platform-architecture]] | Port/servis |
| DB şeması | `../.ai/.sql/mysql/media_catalog.sql` | Data Engineer yüzeyi |
| Denetim kapısı | `docs/checklist.md` · `docs/todos.md` | QA kanıtı + kapı sahipleri |
| Şablon | [[../.ai/.templates/frontend/context-template]] | İskelet (Guardrail #16) |
| Kural dosyaları | `../.ai/.rules/*` | error-recovery (G3) |
| Audit trail | `../.ai/log.md` | append-only kayıt |
| Domain okuma listesi | [[../.ai/AGENTS]] §24.3 | Klasörün satırı (§3.5) |

**eli10/eli15 blokları (§7):**

> **eli10 (basit):** İşe nereden başlanacağını gösteren bağlantı listesi.
> **eli15 (detay):** Referans tablosu ayrıdır çünkü her belge farklı bir soruyu yanıtlar (envanter · kural · rol · süreç · karar). Okunması, işe doğru dosyayla başlanmasını sağlar. Yanlış kaynaktan başlanırsa eski sayı tekrarlanır. Tablo güncel tutulmazsa kırık bağlantı doğar.

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** agents
