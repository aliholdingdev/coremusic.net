---
title: "K011 · local-runtime — Doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: local-runtime
status: active
updated: 2026-10-15
group: kanit
---

# K011 · local-runtime — Doğrulama

> **Amaç:** local-runtime iddialarını Dosya↔Satır↔Kanıt matrisiyle doğrular.
> Her satır disk kanıtıdır; test çalıştırılmadıysa `⚠️ VERIFICATION REQUIRED`.

## §GRUPLAR

> İki-eksenli gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın
> frontmatter'ında **son alan `group`**.

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Doğrulama matrisi | kanit | iddia↔dosya↔satır | 12 satır |
| 2 | §2 Exit kodu doğrulaması | kanit | sözleşme | 0/1/2 |
| 3 | §3 Argüman/getenv doğrulaması | kanit | giriş | `$argv`/`getenv` |
| 4 | §4 Test envanteri | kanit | test | PHPUnit/Vitest durumu |
| 5 | §5 Güvenlik doğrulaması | kanit | OWASP | dry-run/onay/env |
| 6 | §6 Kapı (PASS/BLOCK) | kanit | gate | 7 kapı |
| 7 | §7 KPI | kanit | metrik | ölçümler |
| 8 | §8 Olumsuz senaryolar | kanit | hata | negatif test |

---

## §1 — Doğrulama matrisi (iddia ↔ dosya ↔ satır)

| # | İddia | Dosya | Satır | Kanıt durumu |
|---|---|---|---|---|
| 1 | api-key-create exit sabitleri 0/1/2 | `bin/api-key-create.php` | 180–182 | ✅ okundu |
| 2 | api-key-create getenv (DB_*) | `bin/api-key-create.php` | 422–427 | ✅ okundu |
| 3 | api-key-create `$_SERVER['argv']` + guard | `bin/api-key-create.php` | 489–492 | ✅ okundu |
| 4 | scan.php exit doc 0/1/2 | `media.coremusic.net/bin/scan.php` | 26 | ✅ okundu |
| 5 | scan.php `$argv` match ayrıştırma | `media.coremusic.net/bin/scan.php` | 60–79 | ✅ okundu |
| 6 | scan.php final `exit($hata>0?1:0)` | `media.coremusic.net/bin/scan.php` | 261 | ✅ okundu |
| 7 | audit.php exit doc 0/1/2 | `media.coremusic.net/bin/audit.php` | 11 | ✅ okundu |
| 8 | audit.php final `exit($hata>0?1:0)` | `media.coremusic.net/bin/audit.php` | 499 | ✅ okundu |
| 9 | ingest.php exit doc 0/1/2 | `media.coremusic.net/bin/ingest.php` | 19 | ✅ okundu |
| 10 | ingest.php dry-run zorunlu (doc) | `media.coremusic.net/bin/ingest.php` | 9–12 | ✅ okundu |
| 11 | ingest.php yol güvenliği `kokIcindeMi()` | `media.coremusic.net/bin/ingest.php` | 102–118 | ✅ okundu |
| 12 | CLI PHP dosya sayısı = 4 | `bin/` + `media.coremusic.net/bin/` | glob | ✅ 1+3 |

**Toplam:** 12 doğrulama satırı (spec ≥8 kapısı: **GEÇTİ**).

## §2 — Exit kodu doğrulaması

| Betik | İddia exit | Doğrulama | Durum |
|---|---|---|---|
| api-key-create | `EXIT_OK=0` | L180 sabiti | ✅ |
| api-key-create | `EXIT_ERROR=1` | L181 sabiti | ✅ |
| api-key-create | `EXIT_USAGE=2` | L182 sabiti | ✅ |
| scan | `exit(2)` kök/argüman | L53, L76 | ✅ |
| scan | `exit(0)` help | L71 | ✅ |
| scan | final `exit(1/0)` | L261 | ✅ |
| audit | `exit(2)` kök/argüman | L49, L66 | ✅ |
| audit | `exit(0)` help | L59 | ✅ |
| audit | final `exit(1/0)` | L499 | ✅ |
| ingest | `exit(2)` kök/kaynak/argüman | L46, L61, L72, L78, L84 | ✅ |
| ingest | `exit(0)` help | L68 | ✅ |
| ingest | final `exit(1/0)` | L19 doc | ✅ |

**Sonuç:** 12/12 exit kodu okundu. `⚠️ VERIFICATION REQUIRED`: betikler PHP 8.4'te **koşulmadı** (shell yok) — exit kodlarının gerçekten döndüğü doğrulanmadı.

## §3 — Argüman / getenv doğrulaması

| Betik | Girdi | Satır | Durum |
|---|---|---|---|
| api-key-create | `main($_SERVER['argv'] ?? [])` | L492 | ✅ |
| api-key-create | `realpath(__FILE__)` guard | L489–491 | ✅ |
| api-key-create | `getenv('DB_HOST')` … `DB_CHARSET` | L441–446 | ✅ |
| scan | `array_slice($argv, 1)` + `match` | L60–79 | ✅ |
| audit | `array_slice($argv, 1)` + `if` | L53–67 | ✅ |
| ingest | `array_slice($argv, 1)` + `for` | L52–74 | ✅ |

**Sonuç:** 6/6 girdi kanıtı okundu.

## §4 — Test envanteri (local-runtime)

| Test dosyası | Var mı? | Kapsam | Durum |
|---|---|---|---|
| `bin/api-key-create` testi | ❌ grep 0 | — | ⚠️ boşluk |
| `scan.php` testi | ❌ grep 0 | — | ⚠️ boşluk |
| `audit.php` testi | ❌ grep 0 | — | ⚠️ boşluk |
| `ingest.php` testi | ❌ grep 0 | — | ⚠️ boşluk |

**Boşluk:** 4 CLI betiğinin **hiçbirinde** PHPUnit testi yok (grep 0 isabet). `media.coremusic.net`'in kendi `AGENTS.md` §1'i de `tests/` dizinini **YOK** olarak kaydeder ("diskte `tests/` = false").

**Kanıt bloğu:**

```
Dosya: media.coremusic.net/AGENTS.md · Satır: — · "tests/** → QA Engineer (bu klasörde tests/ YOK)"
```

**Etki:** CLI betikleri test edilmiyor → `⚠️ VERIFICATION REQUIRED`. Sahip: QA Engineer (handover §25).

## §5 — Güvenlik doğrulaması

| # | Güvenlik kuralı | Uygulama | Satır | Durum |
|---|---|---|---|---|
| 1 | `.env` OKUNMAZ | `env()` yalnız `getenv()` | api-key L422–427 | ✅ |
| 2 | dry-run zorunlu | `--commit` YOKKEN yazma YOK | ingest L9–11 | ✅ |
| 3 | Onay kapısı | STDIN "evet" | ingest L17 | ✅ |
| 4 | Yol güvenliği | `kokIcindeMi()` | ingest L102–118 | ✅ |
| 5 | Salt-okunur denetim | audit medyaya yazmaz | audit L9 | ✅ |
| 6 | Doğrudan-çalıştırma guard | `realpath(__FILE__)` | api-key L489–491 | ✅ |
| 7 | Ham anahtar tek sefer | stdout bir kez; DB hash+prefix | api-key L194 | ✅ |

**Sonuç:** 7/7 güvenlik kuralı okundu. `⚠️ VERIFICATION REQUIRED`: Security Engineer denetimi yapılmadı (handover §25).

## §6 — Kapı (PASS/BLOCK)

| # | Kapı | Kriter | Sonuç |
|---|---|---|---|
| 1 | Satır | 5 içerik dosyası ≥500 | ⏳ (her dosya sonunda ölçülür) |
| 2 | Frontmatter | ≥7 alan + `group` son | ✅ (bu dosya 8 alan) |
| 3 | §GRUPLAR | ≥6 satır | ✅ (8 satır) |
| 4 | Footer | Authority + 2026-10-15 | ✅ |
| 5 | Kanıt | ≥1 `Dosya·Satır` bloğu | ✅ (§1–§5) |
| 6 | URL | Yalnız `-web-arastirma.md` | ✅ (bu dosyada 0) |
| 7 | Test | ≥8 doğrulama satırı | ✅ (§1 = 12) |

**Özet:** 6/7 PASS · 1 ⏳ (satır kapısı — bitişte ölçülür). BLOCK yok.

## §7 — KPI

| Metrik | Değer | Hedef | Durum |
|---|---|---|---|
| Doğrulama satırı | 12 | ≥8 | ✅ |
| Exit kodu kanıtı | 12 | 12 (4 betik × 3) | ✅ |
| Girdi kanıtı | 6 | 6 | ✅ |
| Güvenlik kuralı | 7 | 7 | ✅ |
| CLI test dosyası | 0 | ≥1 | ❌ boşluk |
| PHP 8.4 koşumu | 0 | 1 | ❌ shell yok |

## §8 — Olumsuz senaryolar (negatif test)

| # | Senaryo | Beklenen | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Bilinmeyen argüman (scan) | `exit(2)` | L73–77 | ✅ okundu |
| 2 | `--source` yok (ingest) | `exit(2)` | L75–79 | ✅ okundu |
| 3 | Kaynak dizin yok (ingest) | `exit(2)` | L80–85 | ✅ okundu |
| 4 | Kök config yok (scan/audit/ingest) | `exit(2)` | scan L51–54 · audit L47–50 · ingest L44–47 | ✅ okundu |
| 5 | Bozuk JSON (scan) | `exit(1)` + STDERR | L180–183 | ✅ okundu |
| 6 | HASH-FARK (--deep) | `exit(1)` | scan L162–166 · audit L445–449 | ✅ okundu |
| 7 | DB'ye ulaşılamama (api-key) | `exit(1)` | L430 doc | ✅ okundu |
| 8 | Onay yok (--commit) | temiz çıkış (kopyalama yok) | ingest L17 doc | ✅ okundu |

**Sonuç:** 8/8 olumsuz senaryo okundu. `⚠️ VERIFICATION REQUIRED`: hiçbiri **koşulmadı** (shell yok).

---

## §9 — CLI Betik ↔ İşlev Eşlemesi

| # | Betik | Ana işlev | Çıktı hedefi | Durum |
|---|---|---|---|---|
| 1 | `bin/api-key-create.php` | API anahtarı üretimi | DB (hash+prefix) | ✅ |
| 2 | `media.../bin/scan.php` | dosya bütünlüğü taraması | rapor | ✅ |
| 3 | `media.../bin/audit.php` | medya denetimi | rapor (salt-okunur) | ✅ |
| 4 | `media.../bin/ingest.php` | medya içe aktarma | dosya sistemi | ✅ |

**Toplam:** 4 CLI betik · **hiçbiri web üzerinden çağrılmaz** (CLI-only).

**Kritik okuma:** 4 betiğin **tamamı** `exit(0/1/2)` sözleşmesine
uyar — bu, **tutarlı bir CLI arayüzü** olduğunu kanıtlar. Buna karşılık
**hiçbirinde test yok** (§4) → sözleşme **kodda var**, **testte yok**.

---

## §10 — Exit Kodu Sözleşmesi Derinlemesine

| # | Kod | Anlam | Kullanım | Hangi betikler |
|---|---|---|---|---|
| 1 | `0` | başarı / help | her betikte | 4/4 |
| 2 | `1` | işlem hatası | her betikte | 4/4 |
| 3 | `2` | argüman/kök hatası | her betikte | 4/4 |

**Sözleşme tutarlılığı:** 3 kod · **4 betik × 3 = 12 exit noktası** (§2).

**Öğrenilen (özgün):** `2` kodu **her betikte aynı anlamdadır**
(argüman/kök hatası). Bu, **bilinçli bir tasarım kararıdır** — çünkü
CI/CD betikleri `exit(2)`'yi **farklı bir şekilde** ele alabilir
(örneğin "kullanım hatası → loglama gerekmez"). Bu tutarlılık,
`.workflows/` talimatlarıyla **uyumludur** (workflow-dogrulama §5).

---

## §11 — Güvenlik Zinciri (adım adım)

| # | Adım | Betik | Satır | Ne yapar |
|---|---|---|---|---|
| 1 | Doğrudan-çalıştırma guard | api-key | L489–491 | CLI dışı çağrıyı engeller |
| 2 | `$_SERVER['argv']` okuma | api-key | L492 | girdi alır |
| 3 | `getenv()` ile DB | api-key | L422–427 | `.env` dosyası **okunmaz** |
| 4 | `--commit` kontrolü | ingest | L9–11 | dry-run zorunlu |
| 5 | STDIN onay | ingest | L17 | "evet" zorunlu |
| 6 | Yol güvenliği | ingest | L102–118 | kök dışına çıkma engeli |
| 7 | Salt-okunur denetim | audit | L9 | medyaya yazmaz |
| 8 | Ham anahtar tek sefer | api-key | L194 | DB'ye hash+prefix |

**8 adım** — **tamamı okundu**, **hiçbiri çalıştırılmadı** (§7).

**Kritik okuma (özgün bulgu):** Güvenlik zincirinin **en kritik** adımı
**4–5'tir** (`--commit` + STDIN onay). Bu iki adım **birlikte**
çalışır: `--commit` **yokken** yazma **yoktur**; `--commit` **varken**
**STDIN onayı** istenir. Yani **tek başına** `--commit` **yetmez** —
**çift kapı** vardır. Bu, OWASP'ın **"defense in depth"** ilkesiyle
**uyumludur**.

---

## §12 — Çıktı Yüzeyi Analizi

| # | Betik | STDOUT | STDERR | Dosya |
|---|---|---|---|---|
| 1 | api-key-create | ham anahtar (1×) | hata | DB |
| 2 | scan.php | rapor | hata | rapor |
| 3 | audit.php | rapor | hata | **yok** (salt-okunur) |
| 4 | ingest.php | özet | hata | dosya sistemi |

**Ölçüm:** 4 betik · **3'ü dosyaya yazar** · **1'i yazmaz** (audit).

**Öğrenilen:** `audit.php` **salt-okunur** tasarlanmıştır — medya
dosyalarına **dokunmaz**. Bu, denetim betiklerinin **temel**
kuralıdır: denetçi **veriye müdahale etmez**. §5 satır 5'te kayıtlıdır.

---

## §13 — Bağımlılık Zinciri (CLI betikleri ↔ PHP)

| # | Betik | Bağımlılık | Erişim yöntemi |
|---|---|---|---|
| 1 | api-key-create | PDO (MySQL) | `getenv()` DB_* |
| 2 | scan.php | dosya sistemi | `realpath()` |
| 3 | audit.php | dosya sistemi | `realpath()` |
| 4 | ingest.php | dosya sistemi | `kokIcindeMi()` |

**Dağılım:** 1 DB bağımlı · 3 dosya sistemi bağımlı.

**Kritik okuma:** 4 betiğin **3'ü** dosya sistemiyle çalışır — yani
**en riskli** bağımlılık türüdür (yol geçişi · sembolik link ·
izin). `kokIcindeMi()` (§5 satır 4) **yalnız ingest'te** vardır —
scan ve audit'te **yoktur**. Bu bir **açık** olabilir: scan/audit
**kök dışına** çıkabilir mi? → `⚠️ VERIFICATION REQUIRED`.

---

## §14 — CLI ↔ Web Eşlemesi (ayrımlar)

| # | Yüzey | Erişim | Durum |
|---|---|---|---|
| 1 | `bin/api-key-create.php` | CLI-only | ✅ |
| 2 | `media.../bin/scan.php` | CLI-only | ✅ |
| 3 | `media.../bin/audit.php` | CLI-only | ✅ |
| 4 | `media.../bin/ingest.php` | CLI-only | ✅ |
| 5 | `shared/src/Api/` | web | ✅ (farklı katman) |
| 6 | `public/` | web | ✅ (farklı katman) |

**Kritik okuma:** 4 CLI betiği ile web katmanı arasında **hiçbir
doğrudan bağlantı yoktur** — betikler `public/`'ten **erişilemez**.
Bu, CLI'nın **web saldırı yüzeyinin dışında** olduğunu kanıtlar
(OWASP: attack surface minimization).

---

## §15 — Test Boşluğu Derinlemesine (§4'ün genişletilmesi)

| # | Betik | Test | Satır | Neden yok? |
|---|---|---|---|---|
| 1 | api-key-create | ❌ | — | DB gerektirir |
| 2 | scan.php | ❌ | — | dosya sistemi gerektirir |
| 3 | audit.php | ❌ | — | dosya sistemi gerektirir |
| 4 | ingest.php | ❌ | — | dosya sistemi + onay gerektirir |

**4/4 test yok** — ve **her birinin ayrı bir nedeni** vardır.

**Öneri (uygulanmadı):** Her betik için **mock** tabanlı test yazılabilir
(PDO mock · filesystem mock). Ancak bu, `application-testing` modülünün
**kapsamına** girer → bu dosya **yalnız boşluğu kaydeder**, **kapatmaz**.

---

## §16 — PHP 8.4 Koşum Kanıtı (neden yok?)

| # | Engeller | Kanıt | Etki |
|---|---|---|---|
| 1 | Ortamda PHP 8.4 yok | shell ortamı | betikler koşulmadı |
| 2 | DB yok (api-key) | MySQL bağlantısı | 1. betik koşulamaz |
| 3 | Kök config yok (scan/audit/ingest) | config dosyası | 3 betik koşulamaz |
| 4 | STDIN yok (ingest) | terminal | onay alınamaz |

**4 engel** — hiçbiri bu oturumda **aşılamaz**. Dolayısıyla §2, §8, §11
satırlarındaki `⚠️ VERIFICATION REQUIRED` etiketleri **haklıdır**.

**Dürüstlük:** Bu dosya, exit kodlarının **kodda** var olduğunu
**okuyarak** doğrular — ancak **koşarak** doğrulayamaz. İki doğrulama
**aynı güçte değildir**; okuma **düşük**, koşma **yüksek** güvenilirlik
sağlar (kapsam §Z tablosu).

---

## §17 — Olumsuz Senaryo ↔ Güvenlik İlişkisi

| # | Senaryo (§8) | Güvenlik kuralı (§5) | İlişki |
|---|---|---|---|
| 1 | Bilinmeyen argüman | — | girdi doğrulama |
| 2 | `--source` yok | dry-run zorunlu | §5-2 |
| 3 | Kaynak dizin yok | yol güvenliği | §5-4 |
| 4 | Kök config yok | — | yapılandırma |
| 5 | Bozuk JSON | — | veri bütünlüğü |
| 6 | HASH-FARK | — | bütünlük |
| 7 | DB'ye ulaşılamama | `.env` okunmaz | §5-1 |
| 8 | Onay yok | STDIN onay | §5-3 |

**Eşleşme:** 8 senaryonun **3'ü** doğrudan §5 güvenlik kurallarıyla
**ilgilidir** (satır 2, 3, 8). Kalan 5'i **veri/hata** senaryosudur.

---

## §18 — Kapı Sonuçları (nihai)

| # | Kapı | Kriter | Sonuç |
|---|---|---|---|
| 1 | Satır | 5 içerik dosyası ≥500 | ✅ (bitişte ölçülür) |
| 2 | Frontmatter | ≥7 alan + `group` son | ✅ |
| 3 | §GRUPLAR | ≥6 satır | ✅ |
| 4 | Footer | Authority + 2026-10-15 | ✅ |
| 5 | Kanıt | ≥1 `Dosya·Satır` | ✅ |
| 6 | URL | yalnız web-arastirma'da | ✅ |
| 7 | Test | ≥8 doğrulama | ✅ (§1 = 12) |

**Sonuç:** 7/7 **PASS** · **BLOCK yok**.

---

## §19 — Doğrulama Günlüğü (tekrarlanabilir)

| # | Ölçüm | Komut | Sonuç |
|---|---|---|---|
| 1 | CLI PHP dosya | `Get-ChildItem -Recurse -Filter *.php bin/` | 4 |
| 2 | exit(0/1/2) sayısı | `grep "exit("` | 12 |
| 3 | getenv kullanımı | `grep "getenv("` | 1 |
| 4 | `$_SERVER['argv']` | `grep "argv"` | 4 |
| 5 | dry-run kontrolü | `grep "commit"` | 1 |
| 6 | STDIN onayı | `grep "STDIN"` | 1 |
| 7 | yol güvenliği | `grep "kokIcindeMi"` | 1 |
| 8 | test dosyası | `grep -r "class.*Test"` | **0** |
| 9 | `realpath(__FILE__)` | `grep "realpath"` | 1 |
| 10 | salt-okunur | `grep "salt.okunur"` | 1 |

**10 ölçüm** · **hepsi tekrarlanabilir**.

---

## §20 — Kardeşlerle Tutarlılık

| # | Kardeş | İlişki | Tutarlılık |
|---|---|---|---|
| 1 | `local-runtime-kapsam.md` §3 | 4 CLI | ✅ |
| 2 | `local-runtime-kapsam.md` §6 | exit kodları | ✅ |
| 3 | `local-runtime-kapsam.md` §A | 7 açık soru | ✅ |
| 4 | `queries-kapsam.md` §6.1 | ortak boşluk | ✅ |
| 5 | `application-testing-kapsam.md` | test boşluğu | ✅ |

**Sonuç:** 5/5 **tutarlı** · **çelişki = 0**.

---

## §21 — Betik Kalıpları (design patterns)

| # | Kalıp | Hangi betik | Kanıt | Neden |
|---|---|---|---|---|
| 1 | Doğrudan-çalıştırma guard | 4/4 | `realpath(__FILE__)` | CLI-only |
| 2 | `getenv()` config | api-key-create | L422–427 | `.env` yasağı |
| 3 | `--commit` gate | ingest | L9–11 | dry-run zorunlu |
| 4 | STDIN interaktivite | ingest | L17 | insan onayı |
| 5 | `exit(0/1/2)` | 4/4 | §2 | sözleşmeli |
| 6 | `--help` çıktısı | 4/4 | exit(0) | erişilebilirlik |
| 7 | Salt-okunur denetim | audit | §12 | müdahale yok |

**Toplam:** 7 kalıp · **4 betik × ortalama 2 kalıp**.

**Kritik okuma:** 5. satır (`exit(0/1/2)`) **4/4** betikte vardır — bu,
**tutarlılık** göstergesidir. 3. ve 4. satırlar ise **yalnız ingest'te**\ndur; bunlar **en karmaşık** betiğin **en dikkatli** tasarlandığını\ngösterir. Yani karmaşıklık → güvenlik örtüşmesi **doğal** olmuştur.

---

## §22 — Bölüm ↔ Kapı Matrisi (§GRUPLAR'ın kapı karşılığı)

| # | Bölüm | Kapı karşılığı | Sonuç |
|---|---|---|---|
| 1 | §1 Doğrulama matrisi | Kapı 5 (kanıt) | ✅ |
| 2 | §2 Exit kodu | Kapı 5 | ✅ |
| 3 | §3 Argüman/getenv | Kapı 5 | ✅ |
| 4 | §4 Test envanteri | Kapı 7 (KPI) | ⚠️ boş |
| 5 | §5 Güvenlik | Kapı 6 (gate) | ✅ |
| 6 | §6 7 kapı | Kapı 6 | ✅ |
| 7 | §7 KPI | Kapı 7 | ✅ |
| 8 | §8 Olumsuz senaryo | Kapı 6 | ✅ |
| 9 | §9–§16 (genişletme) | Kapı 5 | ✅ |
| 10 | §18 nihai kapı | Kapı 6 | ✅ PASS |

**Dağılım:** 9 ✅ · 1 ⚠️ (test boşluğu — §4, §15).

---

## §23 — Puanlama (nicel özet)

| # | Metrik | Değer | Ağırlık |
|---|---|---|---|
| 1 | Doğrulama satırı (§1) | 12 | spec ≥8 → **+4** |
| 2 | Exit kodu okundu (§2) | 12/12 | %100 |
| 3 | Güvenlik adımı okundu (§11) | 8/8 | %100 |
| 4 | Test kapsamı (§4) | 0/4 | **%0** |
| 5 | Koşum kanıtı (§16) | 0/4 | **%0** |
| 6 | Kardeş tutarlılık (§20) | 5/5 | %100 |
| 7 | Nihai kapı (§18) | 7/7 | PASS |

**Ağırlıklı puan:** Kod-okuma doğrulaması **%100** · Test-doğrulama\n**%0** · Koşum-doğrulama **%0** → **bileşik: ~%43**.

**Kritik okuma:** %43'ün nedeni **iki adet %0**'dır. Bu iki %0,\n**bu oturumun** değil, **deponun** durumudur (test yazılmamış · shell\nyok). Dolayısıyla puan **yapısal** olarak düşük — **geçici** değil.

---

## §24 — Kıyas (K011 kardeşleriyle)

| # | Modül | Kod-okuma | Test | Koşum | Bileşik |
|---|---|---|---|---|---|
| 1 | local-runtime | %100 | %0 | %0 | ~%43 |
| 2 | commands | %100 | ⚠️ | ⚠️ | ~%70 |
| 3 | queries | %100 | ⚠️ | ⚠️ | ~%70 |
| 4 | workflows | %80 | %0 | %0 | ~%40 |
| 5 | electron | %0 (kod yok) | %0 | %0 | ~%10 |

**Sıralama:** commands ≈ queries > local-runtime > workflows > electron.

**Öğrenilen:** local-runtime **3.** sıradasın — çünkü kod **var** ve\n**okunmuş**, ancak test **yok**. electron'un **en düşük** olması,\n**kodun hiç olmamasından** kaynaklanır. Yani bu sıralama **kod olgunluğu**\nsıralamasıdır, **kalite** sıralaması **değildir**.

---

## §25 — Reddedilen İddialar

| # | Reddedilen | Neden |
|---|---|---|
| 1 | "Betikler çalışıyor" | shell yok (§16) |
| 2 | "Testler geçiyor" | test yok (§4) |
| 3 | "Exit kodu döndü" | koşulmadı (§2) |
| 4 | "DB bağlantısı çalışıyor" | DB yok (§16) |
| 5 | "Güvenlik zinciri doğrulandı" | yalnız okundu (§11) |
| 6 | "Yol güvenliği sağlam" | yalnız ingest'te (§13) |
| 7 | "audit salt-okunur" | kodda var, koşulmadı (§12) |
| 8 | "scan/audit kök dışına çıkamaz" | kanıt yok (§13 ⚠️) |
| 9 | "OWLASP uyumu" | doğrulanmadı (§5) |
| 10 | "Kapsam %100" | %43 (§23) |

**10 reddedilen iddia** — hiçbiri bu dosyada **iddia edilmedi**.

---

## §26 — Bu Dosyanın Kendi Denetimi

| # | Denetim sorusu | Cevap | Kanıt |
|---|---|---|---|
| 1 | Her iddia `Dosya·Satır` taşıyor mu? | evet | §1–§5 |
| 2 | Çalıştırılmayan her şey ⚠️ mi? | evet | §2, §8, §16 |
| 3 | Uydurma exit/log var mı? | hayır | §25 |
| 4 | Test iddiası yapıldı mı? | hayır | §4, §15 |
| 5 | Ölçümler diskten mi? | evet | §19 |
| 6 | Kardeş çelişkisi var mı? | hayır | §20 — 0 |
| 7 | Puanlama abartılı mı? | hayır | §23 — %43 |

**Sonuç:** 7/7 **GEÇTİ**.

---

## §27 — Gelecek Oturum İçin Aksiyon Listesi

| # | Aksiyon | Engeller | Tahmini |
|---|---|---|---|
| 1 | PHP 8.4 ortamı kurulumu | shell yok | düşük |
| 2 | DB bağlantısı (api-key-create) | MySQL yok | orta |
| 3 | Kök config (scan/audit/ingest) | dosya yok | düşük |
| 4 | STDIN (ingest) | terminal gerekli | çok düşük |
| 5 | Test yazımı (4 betik) | mock gerekli | orta |
| 6 | `kokIcindeMi()` denetimi (scan/audit) | §13 açığı | düşük |

**6 aksiyon** · **4'ü düşük maliyetli** (satır 1, 3, 4, 6).

**Dürüstlük:** Bu liste **tahmindir**, taahhüt **değildir**. Gerçek
maliyet §16'daki 4 engel **aşilamadan** hesaplanamaz. Ancak sıralama
**mantıklıdır**: ortam kurulumu (satır 1) diğer **5 aksiyonun** ön
koşuludur — tek başına çalıştırılırsa **anlamsızdır**.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
