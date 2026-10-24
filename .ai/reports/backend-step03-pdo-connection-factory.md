---
title: "Backend Rebuild —" Adım 03: PDO Connection Factory (Merkezi DB Bağlantısı)"
type: report
category: backend
docType: report
date: 2026-10-24
version: 1.0.0
status: completed
authority: "SSOT: shared/src/Database/ + ADR-002-pdo-mandatory-no-orm"
---

# Adım 03 — PDO Connection Factory Raporu

**Tarih:** 2026-10-24 · **Yürütücü:** MO (ana oturum) · **Kapı:** step04'te "yakında" olarak işaretlenen bu raporla kapanır

---

## 1. Kapsam ve Karar

Prompt arşivi `coremusic-backend-restructure-master-prompt` Adım 3 = "Veritabanı bağlantısı"
(Adım 1-2 şema/BCNF → Adım 3 bağlantı katmanı → Adım 4 API). step01/step02 şema ve
BCNF denetimini kapattı; bu rapor bağlantı katmanının disk kanıtını yazar.

**Kod zaten mevcuttu** — `shared/src/Database/DatabaseManager.php:18` docblock'unda
açık not: *"NOT (Adım 03): execute()/write() mevcut (repository sözleşmesi)"*. Yani
Adım 03 kodu yazma işi değil, **denetim + kanıt** işidir; 2026-10-24'te tamamlandı.

**Karar (YAGNI + surgical change):** sıfırdan bağlantı katmanı yazılmadı; mevcut
`ConnectionFactory` + `DatabaseRegistry` + `DatabaseManager` üçlüsü denetlendi,
1 gap (home repo'larının kendi bağlantısı) kapatıldı, guard ile kapıcıya bağlandı.

---

## 2. Disk Kanıtı — Bağlantı Katmanı Envanteri

| Dosya | Rol | Belleyici kanıt |
|---|---|---|
| `shared/src/Database/ConnectionFactory.php` (7180 B) | **DB başına TEK PDO** — singleton | `getInstance()` L58 · `reset()` L66 (test) · `get($db): \PDO` L76 · `has()` L91 · `registry()` L97 |
| `shared/src/Database/DatabaseRegistry.php` (1054 B) | key → manager haritası | `registerMySql()` L14 · `get()` L21 (yoksa `RuntimeException`) · `has()` L29 |
| `shared/src/Database/DatabaseManager.php` (3737 B) | lazy PDO + prepared-only API | `execute()` L50 / `write()` L58 ikisi de `prepare()+execute()` · transaction L69-81 |
| `shared/src/Database/DatabaseConfig.php` (438 B) | bağlantı config değeri nesnesi | constructor L25 üzerinden `DatabaseManager`'a |
| `shared/src/Database/DatabaseException.php` (2580 B) | hata sarmalayıcı | — |
| `shared/src/Contracts/Database/IDatabaseManager.php` | DI sözleşmesi | `implements IDatabaseManager` (DatabaseManager L21) |

**PDO ayarları (ADR-002/022 — DatabaseManager L41-43):**
`ERRMODE_EXCEPTION` · `FETCH_ASSOC` · **`ATTR_EMULATE_PREPARES => false`** · `utf8mb4`.

**Kayıt/şema uyuşmazlığı (küçük drift):** ADR-002 §1.1 (2026-09-24) "`shared/src/Database/`
**3 PHP dosyası**" yazar; diskte bugün **5 dosya** (ConnectionFactory + DatabaseException
sonradan eklenmiş). ADR metni frozen değil (accepted, yazılabilir) → düzeltme adayı,
bu raporda tespit edildi.

---

## 3. Bağlantı Mimarisi (ugulanan kural)

```text
.env  →  ConnectionFactory::getInstance()   (süreç başına tek factory)
              └─ DatabaseRegistry  (get-or-register key map)
                    └─ DatabaseManager  (lazy PDO, prepared-only, EMULATE=false)
                          └─ IDatabaseManager  ← DI sözleşmesi (repository buradan okur)
```

- **Tek kaynak:** herhangi bir kod `new PDO` açmaz; `ConnectionFactory` üzerinden DB başına
  tek bağlantı alır. `.env` yüklemesi `EnvParser::loadIntoEnv` → mevcut `$_ENV` değerlerini
  ezmez (mevcut zarf korunur).
- **Repository sözleşmesi:** yeni bağlantı **kuramaz**; DI `IDatabaseManager` tercih edilir.
  İstisna (onaylı, 2026-10-24): home repo'ları DI altyapısı olmadığı için
  `ConnectionFactory::getInstance()->registry()` **get-or-register** (`home_{$db}` key) —
  mevcut kaydı okur, yeni bağlantı kurmaz. Kural bu genişletmeyle guard docblock'ına yazıldı
  (`bin/db-access-guard.php` §2 "serbest (tek kaynak kuralı)").
- **Ayrık servis (ADR-039):** `media.coremusic.net` CLI servisi kendi bağlantısını kurar
  (guard kapsamı dışı, gerekçe guard satır 28'de) — `EMULATE_PREPARES=false` + prepared-only
  korunur, `TABLO_ALLOWLIST` guard'ı `CatalogWriter::upsertSql()` eklendi (2026-10-24).
- **Kapsam dışı (bilinen):** `admin.coremusic.net/pages/admin-dashboard.php:28`
  `new DatabaseManager(...)` — kullanıcı kapsam kararıyla hariç (reddedilen teklif, raporlandı).

---

## 4. Bu Adımda Kapanan Gap'ler (2026-10-24)

| # | Gap | Kapatma | Kanıt |
|---|---|---|---|
| 1 | home repo'ları `new DatabaseManager(new DatabaseConfig(...))` ile **kendi bağlantılarını** kuruyordu (çifte bağlantı, havuz drift'i) | `ConnectionFactory` get-or-register'a taşındı | `home/.../MusicRepository.php:108` · `PlaylistRepository.php:72` · commit `2513de8` |
| 2 | `media/CatalogWriter` ham `$pdo->query()` + string-interpolated upsert çalıştırıyordu | prepared `execute()` + `TABLO_ALLOWLIST` + `upsertSql()` guard | `media.coremusic.net/src/Media/CatalogWriter.php` (L161 doc, L307 PDO opts) |
| 3 | Bağlantı kuralına **kapıcı yoktu** (kural kağıtta, kodda denetimsiz) | `bin/db-access-guard.php` statik gate → CI job | 67 dosya PASS · self-test BLOCK/exit1 → PASS/exit0 · commits `2513de8`, `d48416f` |

---

## 5. Doğrulamalar (2026-10-24, bu oturum)

| Kontrol | Sonuç |
|---|---|
| `php bin/db-access-guard.php` (prezentasyon + repository yüzeyi) | **PASS** — 67 dosya, exit 0 |
| Guard self-test (geçici ihlal: `v2/index.php` + `->execute(`) | **BLOCK — 2 ihlal, exit 1** → geri alındı → PASS |
| PHPUnit: home 67 · api 110 · auth Unit 46 | **223 test OK** |
| `php -l` (kapsam, vendor hariç) | **265 dosya / 0 hata** |
| `ATTR_EMULATE_PREPARES` (kod taraması) | yalnız `false` ×2 (DatabaseManager:43, CatalogWriter:307) |
| `SELECT *` production kodu / `unserialize(user)` | **0 / 0** (tek gerçek kullanım test fixture'ı) |
| Boot smoke (php -S, curl) | api `/health` **200** · auth `/health` **200** · home `/` **302** |
| `.env` git tracked | **0 dosya** |

---

## 6. Kabul Kriteri

| # | Kriter | Durum |
|---|---|---|
| 1 | DB başına tek bağlantı; her kod `ConnectionFactory`'dan geçer | PASS (guard kuralı 2) |
| 2 | Repository yeni bağlantı kuramaz (yalnız DI / get-or-register) | PASS |
| 3 | Prezentasyon (index/Controller/pages/config/boot/route) DB'ye dokunamaz | PASS (67 dosya gate) |
| 4 | Prepared-only, `EMULATE_PREPARES=false`, ORM/`SELECT *` yasak (ADR-002) | PASS (tarama §5) |
| 5 | Test + boot yeşil | PASS (223 test + 200/200/302) |

---

## 7. Açık Maddeler

| # | Açık | Sahip | Not |
|---|---|---|---|
| 1 | **Canlı doğrulama (server PC, MySQL):** `backend-step09/10/11-curl-pc.ps1` + `composer test` + KAPI-E browser | kullanıcı | `.ai/TODO.md` satır 58 açık — dev makinede MySQL YOK → `⚠️ VERIFICATION REQUIRED` |
| 2 | ADR-002 §1.1 "3 dosya" → diskte 5 dosya drift'i | MO | ADR yazılabilir; düzeltme onayı bekliyor |
| 3 | `auth /health` session cookie üretiyor (health stateless olmalı) | backend | düşük öncelik; monitor/bot DB'ye session satırı yazar |

---

**İlgili:** `backend-step01-sema-dogrulama.md` → `backend-step02-bcnf-denetim.md` → **bu dosya**
→ `backend-step04-api-v2.md` (L178 bu raporu bekliyordu) · ADR: `ADR-002-pdo-mandatory-no-orm`
· guard: `bin/db-access-guard.php`

**Sonraki adım:** Adım 04-11 raporları mevcut; sıradaki vault işi açık maddeler §7.

---

*Adım 03 — PDO Connection Factory denetimi · 2026-10-24 · MO*
