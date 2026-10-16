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

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
