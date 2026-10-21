# php-backend-standards — Kalite Kapısı (Quality Gate)

> **Kullanım:** PHP kodu/tasarımı teslim edilmeden önce bu dosyayı çalıştır.
> Her madde `PASS` / `BLOCK` / `N/A`. **Tek `BLOCK` = teslim YOK.**
> Çıktı biçimi: `GATE: PASS|BLOCK` + maddeler + kanıt (dosya:satır).
> Kalıp: `skill-maker/references/checklist.md` (N1-N10) + bu skill'in kapıları.

**Skill:** php-backend-standards · **Format:** Claude Skill v3.0 (N1-N10) · **Updated:** 2026-10-21

---

## A. SKILL.md Biçim Kapıları (N1-N10 — teslimde bir kez)

| # | Madde | Kanıt |
|---|-------|-------|
| A1 (N1) | Frontmatter yalnız `name/description/license/metadata` | head |
| A2 (N2) | description "Use when…" · ≤1024 · tetikleyiciler ('PHP kodla', 'middleware ekle', 'endpoint yaz', 'PDO sorgusu') | head |
| A3 (N3) | SKILL.md 500–2000 · dizin ≥1000 (`(Get-Content).Count`) | ölçüm |
| A4 (N4) | Her `references/` + `examples/` SKILL.md'den linkli — **orphan=0** | link taraması |
| A5 (N5) | `examples/add-endpoint.md` tam döngü (contract → route → DTO → test) | dosya |
| A6 (N6) | `name` = klasör 1:1 (`php-backend-standards`) | head |
| A7 (N8) | `CLAUDE.md` sidecar YOK | dosya yok |
| A8 (N9) | Footer = `metadata.version` + `Updated: 2026-10-21` | tail |
| A9 (N10) | Kök `changelog[]` yok · sürüm `references/changelog.md` (3.0.0 → 4.0.0) | head |
| A10 | UTF-8 BOM yok | bayt okuma |

## B — Dil & Dosya Standartları (§3-§4)

```text
[ ] `declare(strict_types=1)` her dosyanın ilk satırı (namespace'den önce)
[ ] `var`/mixin/hack yok · null pointer'a karşı explicit null kontrolü
[ ] Dosya ≤ 1000 satır / sınıf ≤ 10 kararlı bağımlılık (aşımına gerekçe)
[ ] Sınıf adı dosya adıyla birebir (PSR-4) · namespace dizin yapısıyla uyumlu
[ ] Kod yorumda değil kodda; kod bloğu canonical template ile (`.ai/.templates/frontend/`)
```

## C — Middleware & Akış (`references/middleware-pipeline.md`)

```text
[ ] Pipeline sırası donmuş: Security headers → CORS → RequestId → Log → Auth → CSRF → RateLimit → Route
[ ] Controller yalnız yetki/kontrol + servis çağrısı; iş mantığı serviste
[ ] Auth kontrolü route'a ÖNCE — controller'da tek başına yetki kontrolüne güvenilmiyor
[ ] API pipeline GERÇEĞİ (disk kanıtı): çekirdek `RequestHandlerInterface` uygulamıyor
      → PSR-15 varsayımı yok, §5 + middleware-pipeline.md §2 uyarısı geçerli
```

## D — Route & API Sözleşmesi (`references/api-contract-flow.md`)

```text
[ ] Contract-first: route adı/method/yanıt şeması önce sözleşme dosyasında
[ ] DTO ile veri aktarımı — ham `$_GET`/`$_POST` doğrudan servise gitmiyor
[ ] HTTP semantiği doğru: 405 (yanlış method) ≠ 404 (yol yok) ≠ 400 (şema ihlali)
[ ] Contract Drift: kod ↔ sözleşme farkı varsa raporlandı ve güncellendi
```

## E — PDO / SQL (`references/pdo-rules.md` — bağlayıcı)

```text
[ ] Parametreli prepare/bindValue — string concat ile SQL YOK (CI gate'i: 0 isabet)
[ ] `SELECT *` YOK (yalnızca gerekli sütunlar)
[ ] `ATTR_EMULATE_PREPARES => true` YOK (gerçek prepared statement)
[ ] Transaction: çok-yazmalı işlemde beginTransaction/commit/rollback eksiksiz
[ ] Dynamic SQL: yalnız whitelist sütun eşleme + parametre bağlama (kullanıcı değeri asla doğrudan)
[ ] N+1 yok: liste ekranlarında tek sorgu / JOIN veya batch IN
[ ] Connection hata zinciri: SQLSTATE 2002/2006/2013 → tek retry (backoff) → 503 + ERROR log
```

## F — Hata & Güvenlik (§§ gerekli kapılar)

```text
[ ] `try/catch` yutma yok — yakalanan her istisna log + uygun HTTP kodu döner
[ ] `unserialize(` kullanıcı verisinde YOK · `eval`/`shell_exec` kapalı
[ ] Hata gövdesinde stack trace/SQL/d sürümü YOK (kullanıcıya genel mesaj, detay log'a)
[ ] CSRF token state-changing her istekte doğrulanıyor
[ ] Çıkışta escape (HTML/attr/JS/URL bağlamına göre)
```

## G — Performans & Test

```text
[ ] Uzun iş async/görev kuyruğuna devredildi (request timeout aşımı düşünüldü)
[ ] Cache: okuma-çoğunda veri cache'leniyor + TTL/invalidasyon tanımlı
[ ] Uç birim testi yazıldı/en az bir senaryo çalıştırıldı (kanıt: komut + çıktı)
[ ] `php -l` temiz (lint hatası yok) — yeni/değişen her dosyada
```

## H — Yasaklar (bu skill'in kendisi)

```text
[ ] `git commit` ATILMADI (orkestratöre ait)
[ ] `.ai/` vault'a YAZMA YOK (salt okunur; hidden: true)
[ ] Secret değerleri `.md`/`.json`/`.log`'a yazılmadı (`.env`)
```

---

## Çıktı şablonu

```
GATE: PASS|BLOCK
- A: 10/10 (N3: SKILL=652, toplam=1747)
- E: BLOCK — E.3: ATTR_EMULATE_PREPARES => true, src/Db.php:44
- diğerleri: PASS
Kanıt: dosya:satır · doğrulama: php -l / test komutu
```
