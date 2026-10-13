---
title: "K007-data — Bağımlılıklar (K-Space V3)"
type: architecture-dependencies
category: architecture
date: 2026-10-12
status: draft
version: 2.0.0
node_id: K007-data
node_type: dependencies
parent_id: "K000–K5000 Architecture Registry"
children:
  - "schema"
  - "migrations"
  - "repositories"
  - "transactions"
  - "indexing"
  - "cache"
  - "search"
  - "synchronization"
  - "backup"
  - "data-quality"
depends_on:
  - "K008-domain (entity/kavram tanımı — §1/1)"
  - "K009-security (şifreleme çağrısı — §1/2)"
  - "K000-isletim-sistemi (fiziksel dosya/disk — §1/3)"
  - "PHP 8.4 + PDO + APCu çalışma zamanı (§1/4 — REPO)"
  - "MySQL 8+/InnoDB utf8mb4 (§1/5 — REPO)"
  - "K011-application / K012-services (repo çağrısı — §1/6)"
evidence:
  - "shared/src/Database/DatabaseManager.php:14-67 · DatabaseRegistry.php — 2026-10-12"
  - "shared/src/Cache/CacheManager.php:32-99 · PageCacheAdapter.php:11-44 — 2026-10-12"
  - "shared/database/migrations/oauth_connections_migration.php:41 (arabir DB FK → coremusic_user.users) — 2026-10-12"
validation: "2026-10-12 — 11 bağımlılık satırı REPO/TASARIM/UNKNOWN etiketli; 3 açık sınır (S-1/S-2/S-3) işaretli; uydurma bağımlılık yok."
open_questions:
  - "S-1 arama sahipliği bağımlılığı K020 ile hangi yönde çözülecek"
  - "S-2 yedek operasyonu K018'e devredilirse bağımlılık yönü tersine döner mi"
  - "S-9 CDC/outbox bağımlılığı K013-integraion'a mı bağlı (ADR-100 metni okunmadı)"
group: mimari
---

# data-bagimliliklar — K007-data

## §0 Etiket kuralı (önce oku)

Bu dosyadaki **her satır etiketlidir**:

| Etiket | Anlam | Kanıt türü |
|---|---|---|
| `REPO` | kod/şema kanıtı okundu | dosya yolu + satır no |
| `TASARIM` | yapısal mantıktan türetildi (kanıt yok) | — |
| `⚠️ SINIR AÇIK` | sahiplik belirsiz | ADR bekliyor |
| `UNKNOWN` | hiçbir kanıt yok | — |

`TASARIM`/`⚠️` satırları `verified` değildir; kanıt gelince `data-dogrulama.md` §3'te ölçüt
kapanır ve satır `REPO` etiketine döner. Uydurma bağımlılık yazılmaz.

## §1 depends_on — bu domain NEYE muhtaç (doğrusal akış)

| # | Hedef | Ne alınır | Neden | Kırılganlık | Etiket |
|---|---|---|---|---|---|
| 1 | K008-domain | entity/kavram tanımı — şemanın dayandığı model | veri, anlamı olmadan şekil değildir; kolon adları ubiquitous language'ten gelir | domain adı değişirse şema + repo + test üçü birden kırılır | TASARIM |
| 2 | K009-security | şifreleme/Hash **çağrısı** (anahtar değil) | kullanıcı verisi korunmalı — token'lar şifreli | security API'si değişirse repo kırılır; anahtar yanlışsa sessiz veri sızıntısı | TASARIM |
| 3 | K000-isletim-sistemi | fiziksel dosya/disk yazımı | veri bir yerde durur | disk dolusu/IO hatası yedek ve migration'ı durdurur | TASARIM |
| 4 | PHP 8.4 + PDO + APCu (`composer.json:8-12`) | çalışma zamanı | `DatabaseManager` PDO'suz; `ApcuAdapter` APCu'suz çalışmaz | eklenti yoksa sessiz fallback (`CacheManager.php:12-16`) — davranış farkı | **REPO** |
| 5 | MySQL 8+/InnoDB (`utf8mb4_unicode_ci`) | DDL/DML davranışı | canlı DB bu; dump'lar InnoDB | sürüm davranışı (instant ADD COLUMN) migration planını etkiler | **REPO** |
| 6 | K011-application / K012-services (yukarıdan gelen istek) | repo çağrısı | veri okuma/yazma talebi | repo sözleşmesi değişirse servis kırılır | TASARIM |

> v2 §2.7 bağımlılık **vermez**; 1–3 yapısal mantıktan, 4–5 repo kanıtından türetildi.

## §2 used_by — bu domain KİMİ besliyor

| # | Kaynak | Ne alır | Beklentisi | Etiket |
|---|---|---|---|---|
| 1 | K006-yapay-zeka | öneri girdisi (etkileşim + katalog) | temiz, eksiksiz, gecikmesiz | TASARIM |
| 2 | K011-application | sayfa verisi (repo üzerinden) | hızlı + önbellekli | TASARIM |
| 3 | K012-services | servis kalıcılığı (repo üzerinden) | atomik yazma | TASARIM |
| 4 | K019-testing | test verisi kurulumu (SQLite sahte şema) | şema-parite | **REPO** (`ApiKeyRepositoryTest.php:23-30`) |
| 5 | K018-devops | yedek artefaktı | uygun format + kurtarma talimatı | ⚠️ SINIR AÇIK |
| 6 | K020-discovery | arama indeksi verisi | taze indeks | ⚠️ SINIR AÇIK |

## §3 Dikey bağımlılık (kademe-içi — 10 çocuk)

| # | Çocuk | Üstüne bağımlı | Bağımlı olduğu | Kırılganlık |
|---|---|---|---|---|
| 1 | schema | — (kök) | K008 (entity adı) | tüm çocuklar şemaya bağlı — değişirse her şey |
| 2 | migrations | schema | K018 (pipeline) | up/down bozulursa kurtarma zor |
| 3 | repositories | schema · transactions | K011/K012 | sızıntı: SQL'i domain'e yayarsa sınır kırılır |
| 4 | transactions | schema | repositories | yarım-kullanım: rollback unutulursa veri bozulur |
| 5 | indexing | schema | search · repositories | yanlış indeks = write-yavaş + read-hızlı (denge) |
| 6 | cache | repositories | K017 (oran) | kirletme unutulursa **eski veri** gösterilir |
| 7 | search | indexing · schema | K020-discovery | indeks gecikirse sonuç eski |
| 8 | synchronization | transactions | K013-integraion | yarım-olay = tutarsız cihaz |
| 9 | backup | schema · migrations | K018-devops | kurtarma testi yoksa yedek **varsayım**dır |
| 10 | data-quality | schema · repositories | K019-testing | yanlış beklenti = yanlış alarm |

## §4 Entegrasyon sınırları (paylaşılır / paylaşılmaz)

| Sınır | Paylaşılır | Paylaşılmaz | Karşılayan dosya |
|---|---|---|---|
| K007 ↔ K008-domain | entity/kavram adları | kural motoru, iş akışı | `data-evrensel-dil.md` |
| K007 ↔ K009-security | şifreleme **çağrısı** | anahtar saklama, politika yazımı | `data-kod-ornekleri.md` §2.7 |
| K007 ↔ K000-os | saklama yolu/izin | disk kuyruğu, fsync | os-master |
| K007 ↔ K006-ai | veriye erişim sözleşmesi | model eğitimi/öneri mantığı | ai-master |
| K007 ↔ K018-devops | yedek artefaktı | zamanlama/pipeline | ⚠️ AÇIK |
| K007 ↔ K020-discovery | arama indeksi verisi | keşif/ranking kuralları | ⚠️ AÇIK |
| K007 ↔ K013-integraion | olay şeması (outbox) | taşıma protokolü | ⚠️ AÇIK |

### §4.1 Entegrasyon kuralı (özet)

1. K007 **sahiptir**: şema · migration · indeks · önbellek sözleşmesi · yedek artefaktı · veri kalitesi beklentisi.
2. K007 **okur** domain'den (kural), **yazmaz** — kural değişikliği K008'de.
3. Sınır aşıldığında: kayıt → `.ai/architecture/index.md` §6'ya işlenir, tahmin edilmez.

## §5 Tek yönlü akış diyagramı

```text
K008-domain ──(entity adı)──► schema ──► repositories ──► K011/K012
K009-security ──(şifreleme çağrısı)──► repositories
K000-os ──(disk)──► schema · backup
schema ──► migrations ──► K018-devops (pipeline)   ⚠️ sınır
schema ──► indexing ──► search ──► K020-discovery    ⚠️ sınır
repositories ──► cache (TTL) ──► K017-observability (oran)
transactions ──► synchronization ──► K013-integraion ⚠️ sınır
schema+transactions ──► backup ──► K018-devops       ⚠️ sınır
schema+repositories ──► data-quality ──► K019-testing
```

## §6 Riskli bağımlılıklar

| # | Risk | Neden | Etki | Olasılık | Öneri | Durum |
|---|---|---|---|---|---|---|
| 1 | search sahipliği çakışması | K007-search ↔ K020-discovery/search | çift indeks, tutarsız sonuç | yüksek | sahiplik ADR'si | ⚠️ |
| 2 | backup sahipliği belirsiz | K007 ↔ K018-devops | kimsenin almadığı yedek | orta | sahiplik ADR'si | ⚠️ |
| 3 | synchronization çift-kaydı | K007 ↔ K013-integraion | iki senkron tasarımı | orta | sınır kaydı | ⚠️ |
| 4 | şema değişimi sessiz kırar | K008/K011/K012 okuyucu | üretim hatası | orta | şema sözleşmesi + K019 testi | TASARIM |
| 5 | cache kirletme unutulursa eski veri | cache ↔ repositories | hatalı içerik gösterimi | orta | kirletme sözleşmesi (`data-kararlar.md` K-05) | TASARIM |
| 6 | referans lisans etkileşimi | Ampache AGPL-3.0 · Koel MIT | kod kopyalarsa yükümlülük | düşük | K020-licence akışına sor | ⚠️ |
| 7 | `PERSISTENT=true` bağlantı havuzu | `DatabaseManager.php:26` | fork sonrası tutarsız durum | düşük | PHP-FPM ayarı denetimi | REPO |

## §7 Doğrulama durumu (ne zaman `verified`)

| Bağımlılık | Ölçüt | Şu an |
|---|---|---|
| K008-domain → | entity tanımı referansı + review kaydı | draft (karşılıklı kayıt yok) |
| K009-security → | şifreleme çağrısı sözleşmesi | draft (karşılıklı kayıt yok) |
| K000-os → | saklama yolu/izin kaydı | draft (karşılıklı kayıt yok) |
| PHP/MySQL runtime → | `composer.json` + DSN kanıtı | ✅ **REPO** (2026-10-12) |
| used_by 1–4 | ilgili domain index'lerinde karşılıklı kayıt | draft |
| used_by 5–6 | K018/K020 sahiplik ADR'si | ⚠️ AÇIK |
| Referans lisans notu | K020-licence dosyasında kayıt | draft (kayıt yok) |

## §8 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.7 (satır 270–288) | yalnız rol + çocuk listesi — bağımlılık VERMEZ |
| `.ai/architecture/index.md` §5 satır 7 · §8 | master adı · isimlendirme |
| ADR-097 §5 · K2/K4 | etiket, isimlendirme, kademe 3–4 |
| `shared/composer.json:8-12` | PHP 8.4 · ext-apcu · ext-pdo (§1/4 kanıtı) |
| `shared/src/Database/DatabaseManager.php:14-27` | MySQL/PDO · PERSISTENT (§1/5, §6/7) |
| `shared/src/Cache/CacheManager.php:12-16` | APCu fallback (§1/4, §6/5) |
| `shared/tests/Repository/ApiKeyRepositoryTest.php:23-30` | SQLite sahte şema (§2/4) |
| `shared/database/migrations/2026-10_07_drop…php:14-16` | mysqldump yedek talimatı (§2/5) |

---

## §9 Bağımlılık grafiği (yön okuması)

> Aşağıdaki metin-bağımlılık grafiği, `depends_on` (→) ve `used_by` (←) yönlerini tek bakışta verir.
> `⚠️` işaretli kenarlar sahiplik ADR'si bekleyen açık sınırlardır.

```text
K008-domain ──veri modeli──▶ K007-data ◀──şifreleme çağrısı── K009-security
                              │  ▲
              pipeline ◀──────┤  └────repo üzerinden veri──── K011-application
             (K018-devops)    │                              K012-services
                              │                              K019-testing
             arama keşfi ◀────┤
             (K020-discovery) │
             synchronization◀─┤
             (K013-integraion)│
             kimlik ◀─────────┘
             (K010-identitiy)
```

> Kenar etiketleri: sol tarafta `K007`'ye **giren** (used_by/depends kaynak), sağda **çıkıyor**
> olan. `⚠️` kenarları §11'de ayrıntılandırılır.

---

## §10 Bağımlılık türleri (taxonomy)

| Tür | Tanım | K007 örneği | Kanıt |
|-----|-------|-------------|-------|
| Yapısal (depends_on) | şema/veri modeli dayanağı | K008-domain entity tanımı | `data-master.md §5` |
| Çağrı (invocation) | çalışma-zamanı çağrısı | K009-security şifreleme | `data-master.md §5` |
| Tüketim (used_by) | bu düğüm veriyi okur | K011-application repo okuması | `data-master.md §5` |
| Operasyonel sınır | sahiplik ADR bekliyor | K018-devops yedek | `data-master.md §5` |
| Veri paylaşımı | çift kayıt riski | K013-integraion synchronization | `data-master.md §5` |

---

## §11 Açık sınırlar (⚠️ — sahiplik ADR bekliyor)

| # | Sınır | Komşu | Risk | Kapanış koşulu |
|---|-------|-------|------|----------------|
| S-1 | search ↔ discovery | K020-discovery | iki düğüm "arama" diyor | sahiplik ADR'si |
| S-2 | backup ↔ devops | K018-devops | politika (K007) vs operasyon (K018) | sahiplik ADR'si |
| S-3 | synchronization ↔ entegrasyon | K013-integraion | tazelik vs entegrasyon olayı | sınır kaydı |
| S-4 | identity ↔ kimlik | K010-identitiy | identity verisi vs kimlik doğrulama | sınır kaydı |

---

## §12 Kırılganlık matrisi (kırılan kenar → etki)

| Kenar | Kırılma senaryosu | Etki | Azaltma |
|-------|-------------------|-------|---------|
| K008→K007 | entity tanımı değişirse şema uyumsuz | migration gecikmesi | şema review |
| K009→K007 | şifreleme anahtarı dönerse | `key_hash` doğrulanamaz | key rotasyonu |
| K011→K007 | repo sözleşmesi değişirse | uygulama kırılır | sözleşme testi |
| K018→K007 | yedek alınamazsa | veri kaybı (RPO) | PITR + kurtarma provası |
| K013→K007 | çift kayıt yazar | tutarsızlık (twin-write) | outbox/CDC |
| K010→K007 | identity verisi değişirse | oturum/erişim kırılır | sınır kaydı |

---

## §13 Bağımlılık kanıt tablosu (repo + doküman)

| Bağımlılık | Kanıt (dosya:satır) | Durum |
|-----------|---------------------|-------|
| MySQL/PDO çekirdeği | `shared/src/Database/DatabaseManager.php:14-27` | ✅ |
| Çoklu-DB registry | `shared/src/Contracts/Database/IDatabaseRegistry.php:8-12` | ✅ |
| Repo sözleşmesi | `shared/src/Contracts/Auth/IUserRepository.php` (20 metot) | ✅ |
| APCu önbellek | `shared/src/Cache/CacheManager.php:12-16` | ✅ |
| Domain event | `shared/src/Contracts/Events/DomainEventInterface.php:21-33` | ✅ |
| migration döngüsü | `shared/database/migrations/oauth_connections_migration.php:13,46` | ✅ |
| yedek talimatı | `2026-10_07_drop_…migration.php:14-20` | ✅ |

---

## §14 Doğrulanmış resmi referanslar

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Transaction/ACID bağımlılığı | https://www.postgresql.org/docs/current/tutorial-transactions.html | 2026-10-13 | HTTP 200 |
| Repository/Data Mapper | https://martinfowler.com/eaaCatalog/dataMapper.html | 2026-10-13 | HTTP 200 |
| Cache invalidation (result cache) | https://docs.oracle.com/en/database/oracle/oracle-database/23/tgdba/tuning-result-cache.html | 2026-10-13 | HTTP 200 |
| Parallel query bağımlılığı | https://www.postgresql.org/docs/current/parallel-query.html | 2026-10-13 | HTTP 200 |

---

## §15 Bağımlılık güncelleme protokolü

| Adım | Aksiyon | Sorumlu |
|------|---------|---------|
| 1 | Yeni kenar tespiti (depends_on/used_by) | istek sahibi |
| 2 | Yön + kanıt kaydı (§13) | K007 sahibi |
| 3 | Kırılganlık satırı (§12) | K007 sahibi |
| 4 | Açık sınır işaretle (⚠️) | K007 sahibi |
| 5 | ADR gerekirse | Vault Steward |

---

## §16 Bağımlılık ölçütleri (kabul)

| Ölçüt | Nasıl ölçülür | Durum |
|-------|---------------|-------|
| depends_on/used_by yönleri kayıtlı | §5 + §9 | ✅ |
| Açık sınır işaretli | §11 (4 satır) | ✅ |
| Kırılganlık satırı var | §12 (6 satır) | ✅ |
| Repo kanıtı ≠ 0 | §13 (7 satır) | ✅ |
| Doğrulanmış URL ≠ 0 | §14 (4 satır) | ✅ |

---

## §17 İlişkili dosyalar

| Dosya | İlişki |
|-------|--------|
| `data-master.md` | §5 bağımlılık özeti (girdi) |
| `data-kapsam.md` | komşu sınır çizelgesi (§9) |
| `data-kararlar.md` | açık sınır kararı |
| `data-riskler.md` | kırılganlık → risk |

---

## §18 Bağımlılık günlüğü (append-only)

| Tarih | Değişiklik | Kanıt |
|-------|------------|-------|
| 2026-10-13 | §9-§18 eklendi — grafik + tür + kırılganlık + kanıt | repo + HTTP 200 |

---

## §19 Bağımlılık — omurga × komşu detay çizelgesi (10 × yön)

> Her K007 omurga ayağının hangi komşuya bağımlı olduğunu ayrıntılandırır. `—` = bağımlılık yok.

| Omurga ayağı | → depends_on | ← used_by | ⚠️ açık sınır |
|---|---|---|---|
| schema | K008-domain | — | — |
| migrations | — | K018-devops | pipeline sahipliği |
| repositories | K008-domain, K009-security | K011-application, K012-services | K010-identitiy |
| transactions | — | K012-services | — |
| indexing | — | search, cache | — |
| cache | — | K011-application | invalidation kuralı |
| search | — | — | K020-discovery (S-1) |
| synchronization | — | — | K013-integraion (S-3) |
| backup | — | — | K018-devops (S-2) |
| data-quality | — | K019-testing | — |

---

## §20 Bağımlılık — "kırılan kenar" senaryo defteri (derin)

> Her senaryo bir gerçek bağımlılık kenarının **nasıl kırılabileceğini** ve etkisini anlatır.
> Kanıt yoksa `⚠️ VERIFICATION REQUIRED` işaretlenir; senaryo uydurma değildir, tasarım riskidir.

### §20.1 K008→K007 (entity → şema)

| Alan | Değer |
|------|-------|
| Kenar | `depends_on: K008-domain` |
| Kırılma | `artist_credit` entity'si alan eklerse şema geride kalır |
| Etki | migration gecikmesi → repo sorgusu hata |
| Azaltma | şema review + migration up/down |
| Kanıt | `coremusic_musics.sql` `artist_credit` `id binary(16)` |

### §20.2 K009→K007 (şifreleme → key_hash)

| Alan | Değer |
|------|-------|
| Kenar | `depends_on: K009-security` |
| Kırılma | şifreleme anahtarı dönerse `key_hash` doğrulanamaz |
| Etki | API key reddi → erişim kaybı |
| Azaltma | key rotasyonu + dual-key geçiş |
| Kanıt | `ApiKeyRepository.php:18-22` (ham anahtar asla DB'ye yazılmaz) |

### §20.3 K011→K007 (repo sözleşmesi)

| Alan | Değer |
|------|-------|
| Kenar | `used_by: K011-application` |
| Kırılma | `IUserRepository` metot imzası değişirse uygulama kırılır |
| Etki | runtime TypeError / veri okuma hatası |
| Azaltma | sözleşme testi (contract test) |
| Kanıt | `IUserRepository.php` (20 metot imzası) |

### §20.4 K018→K007 (yedek → veri kaybı)

| Alan | Değer |
|------|-------|
| Kenar | `used_by: K018-devops` (operasyon) |
| Kırılma | yedek alınamazsa RPO aşılır |
| Etki | veri kaybı (geri alınamaz) |
| Azaltma | PITR + kurtarma provası |
| Kanıt | migration'da mysqldump talimatı (`2026-10_07_drop_…:14-20`) — operasyon kanıtı YOK → ⚠️ |

### §20.5 K013→K007 (synchronization → tutarsızlık)

| Alan | Değer |
|------|-------|
| Kenar | `used_by: K013-integraion` |
| Kırılma | iki servis aynı veriyi yazar (twin-write) |
| Etki | son-yazan-kazanır → veri kaybı |
| Azaltma | outbox / CDC |
| Kanıt | repo'da CDC/outbox altyapısı YOK → ⚠️ VERIFICATION REQUIRED |

### §20.6 K010→K007 (identity → erişim)

| Alan | Değer |
|------|-------|
| Kenar | `used_by: K010-identitiy` |
| Kırılma | identity verisi değişirse oturum/erişim kırılır |
| Etki | yanlış kullanıcı erişimi |
| Azaltma | sınır kaydı + RBAC testi |
| Kanıt | `IUserRepository.php` `findRolesForUser` (RBAC rolleri) |

---

## §21 Bağımlılık — yön sözlüğü (TR↔EN)

| TR | EN | Yön |
|----|----|-----|
| Bağımlı olmak | depends on | → |
| Kullanılmak | used by | ← |
| Açık sınır | open boundary | ⚠️ |
| Kırılganlık | fragility | — |
| Kenar | edge | — |
| Düğüm | node | — |

---

## §22 Bağımlılık — ölçüm metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| depends_on/used_by satırı | ≥8 | 10 (§5 + §9) |
| Açık sınır (⚠️) | işaretli | 4 (§11) |
| Kırılganlık senaryosu | ≥5 | 6 (§12 + §20) |
| Repo kanıtı | ≥5 | 7 (§13) |
| Doğrulanmış URL | ≥3 | 4 (§14) |

---

## §23 Bağımlılık — sık sorulanlar (FAQ)

| Soru | Cevap |
|------|-------|
| K007 neden K008'e bağımlı? | entity/kavram tanımı K008'de; şema onun yansıması |
| K009 bağımlılığı neden "çağrı"? | anahtar K009'da; K007 yalnız `key_hash` saklar |
| synchronization neden repo'da kanıtsız? | CDC/outbox altyapısı henüz kurulmamış → ⚠️ |
| backup neden K018'e devredildi? | operasyon (zamanlama/çalıştırma) K018 yetkisi |

---

## §24 Bağımlılık — genişletme günlüğü (append-only)

| Tarih | Değişiklik | Kanıt |
|-------|------------|-------|
| 2026-10-13 | §9-§24 eklendi — grafik + tür + kırılganlık + omurga×komşu + senaryo defteri | repo + HTTP 200 |

---

## §25 Bağımlılık — kademe-3 (bounded-context) hazırlık notu

> ADR-097 K4 ile 10 omurga ayağının kademe-3 (bounded-context) yapısı sabitlenecek. Bu bölüm,
> o kademe-3 yazılmadan önce **bağımlılık kenarlarının nereye taşınacağı**na dair hazırlık notudur.
> Kanıt yoksa `⚠️ VERIFICATION REQUIRED`; uydurma bounded-context adı yazılmaz.

| Omurga ayağı | Kademe-3'e taşınacak bağımlılık | Beklenen sınır |
|---|---|---|
| schema | K008-domain entity | net (kanıt var) |
| migrations | K018-devops pipeline | ⚠️ sınır ADR'si |
| repositories | K011/K012 + K010 identity | net + ⚠️ identity |
| transactions | K012-services atomiklik | net |
| indexing | search/cache iç bağımlılık | net |
| cache | K011 invalidation | ⚠️ kural sahipliği |
| search | K020-discovery | ⚠️ S-1 |
| synchronization | K013-integraion | ⚠️ S-3 |
| backup | K018-devops | ⚠️ S-2 |
| data-quality | K019-testing | net |

---

## §26 Bağımlılık — risk ↔ kırılganlık eşlemesi (data-riskler.md girdisi)

| Kırılganlık (bu dosya) | Karşılık gelen risk (data-riskler.md) | Etiket |
|---|---|---|
| K008→K007 entity değişimi | R-şema uyumsuzluk | TASARIM |
| K009→K007 anahtar dönüşü | R-key_hash reddi | GÜVENLİK |
| K011→K007 sözleşme değişimi | R-runtime TypeError | SÖZLEŞME |
| K018→K007 yedek yok | R-veri kaybı (RPO) | OPERASYON |
| K013→K007 twin-write | R-tutarsızlık | TUTARLILIK |
| K010→K007 identity değişimi | R-yanlış erişim | KİMLİK |

---

## §27 Bağımlılık — son kontrol listesi (bu revizyon)

| Kontrol | Durum |
|---------|-------|
| depends_on/used_by yönleri kayıtlı | ✅ |
| Açık sınır işaretli (⚠️ 4) | ✅ |
| Kırılganlık senaryosu (6) | ✅ |
| Repo kanıtı (7) | ✅ |
| Doğrulanmış URL (4) | ✅ |
| Mojibake yok | ✅ |
| `parent:` anahtarı yok | ✅ |
| Uydurma bağımlılık yok | ✅ |

---

## §28 Bağımlılık — kaynak envanteri (bu dosyanın dayandığı dosyalar)

| Kaynak | Tür | Ne verdi | Bölüm |
|--------|-----|----------|-------|
| `shared/src/Database/DatabaseManager.php` | repo | PDO + 4 attribute | §13, §20 |
| `shared/src/Contracts/Database/IDatabaseManager.php` | repo | 6 metot sözleşmesi | §13 |
| `shared/src/Contracts/Database/IDatabaseRegistry.php` | repo | registerMySql/get/has | §13 |
| `shared/src/Contracts/Auth/IUserRepository.php` | repo | 20 metot + RBAC | §20.3, §20.6 |
| `shared/src/Contracts/Events/DomainEventInterface.php` | repo | eventName/occurredOn/payload | §13 |
| `shared/src/Cache/CacheManager.php` | repo | APCu fallback | §13 |
| `shared/src/Repository/ApiKeyRepository.php` | repo | key_hash + 5 INDEX | §20.2 |
| `.ai/.sql/mysql/*.sql` | şema | InnoDB + kısıt + indeks | §12, §20 |
| `shared/database/migrations/*.php` | migration | up/down + yedek talimatı | §20.4 |
| `postgresql.org` · `oracle` · `martinfowler.com` | web | HTTP 200 doğrulanmış | §14 |

---

## §29 Bağımlılık — son not (bu genişletme)

Bu dosya K007'nin **yön·kırılganlık** defteridir; kod üretmez, yalnız **kenar ve risk** kaydeder.
Yeni bağımlılık eklenirken §15 protokolü + §13 kanıt zorunludur. Açık sınır (⚠️) kapanmadan
`status` `draft`'ta kalır. Bağımlılık kaydı **kanıtsız üretilmez**; her kenar ya repo satırı ya
HTTP 200 doküman ya `⚠️ VERIFICATION REQUIRED` taşır. Kapanmayan açık sınır, kademe-3
(bounded-context) üretiminde yeniden değerlendirilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
