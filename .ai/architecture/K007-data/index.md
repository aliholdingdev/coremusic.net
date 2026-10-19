---
node_id: K007-data
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - schema
  - migrations
  - repositories
  - transactions
  - indexing
  - cache
  - search
  - synchronization
  - backup
  - data-quality
depends_on:
  - K008-domain
  - K009-security
evidence:
  - "shared/src/Database/DatabaseManager.php:14-27 — PDO MySQL DSN + 4 attribute (2026-10-13 okundu)"
  - ".ai/.sql/mysql/ — 18 BCNF coremusic_* veritabanı / 20 SQL dump (2026-10-07)"
validation:
  - "2026-10-13 — children listesi Get-ChildItem ile diskten enumerate edildi: 10/10"
open_questions:
  - "search (K007) ile K020-discovery/search/ sahipliği sınırı → VERIFICATION REQUIRED"
group: mimari
updated: 2026-10-18
---

# K007-data — Domain Künyesi

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K007-data |
| **K Adı** | Veri / Veritabanı (Data) |
| **Kapsam** | CoreMusic'in uzun süreli hafızası: şarkı künyesi, kullanıcı kaydı, playlist, cihaz eşlemesi burada şekillenir, indekslenir, önbelleklenir, aranır, yedeklenir ve kalite denetiminden geçirilir. Kod içermez — vault plan/sınır/kayıt tutar; gerçek kod `shared/` ve `.ai/.sql/` altındadır (18 BCNF coremusic_* veritabanı). 10 kademe-2 çocuk. |
| **Kritik Bağımlılıklar** | K008-domain · K009-security (kanıt: FM depends_on · DatabaseManager.php:14-27 PDO MySQL DSN) |
| **Durum** | draft (10 kademe-2 çocuk) |
| **Son Güncelleme** | 2026-10-13 (children listesi disk enumerasyonu 10/10) |

> **ELI15:** Veritabanı — şarkılar, kullanıcılar, listeler, yedekler.

Bu oda CoreMusic'in **uzun süreli hafızasıdır**: yazılan her şey (şarkı künyesi, kullanıcı
kaydı, playlist, cihaz eşlemesi) burada şekillenir, indekslenir, önbelleklenir, aranır,
yedeklenir ve kalite denetiminden geçirilir. Kod içermez — bu vault **plan, sınır ve kayıt**
tutar; gerçek kod `shared/` ve `.ai/.sql/` altındadır.

## §1 Alt-Konu Haritası (kademe-2 → kademe-3)

| # | Alt konu | Rol | Odak | Dosya |
|---|----------|-----|------|-------|
| 1 | [schema](schema/index.md) | Şema tasarımı | ER/ilişkisel, BCNF, PK/FK/UNIQUE | 6 MD |
| 2 | [migrations](migrations/index.md) | Şema sürümleme | İleri-tek-geri-yok (ADR-014), online DDL | 6 MD |
| 3 | [repositories](repositories/index.md) | Erişim katmanı | PDO/prepared, repository sözleşmesi | 6 MD |
| 4 | [transactions](transactions/index.md) | İşlem bütünlüğü | ACID, MVCC, izolasyon, deadlock | 6 MD |
| 5 | [indexing](indexing/index.md) | İndeks stratejisi | B-Tree, prefix, composite | 6 MD |
| 6 | [cache](cache/index.md) | Önbellek | APCu, page cache, invalidation | 6 MD |
| 7 | [search](search/index.md) | Arama | Full-text, inverted index, novasearch | 6 MD |
| 8 | [synchronization](synchronization/index.md) | Eşitleme | Çift kayıt sınırı (K013 overlap) | 6 MD |
| 9 | [backup](backup/index.md) | Yedekleme | Tam/artık (binlog), PITR, 3-2-1 | 6 MD |
| 10 | [data-quality](data-quality/index.md) | Veri kalitesi | Bütünlük/geçerlilik/tekrar, K-05 | 6 MD |

**Doğrulama (2026-10-15):** 10 çocuk × 6 MD = **60 dosya** disk enumerate edildi
(her çocuk: index + kapsam + bilesenler/veya anayasa + dogrulama/surucu + ornekler +
web-arastirma). ADR-097 §5.2 rol sözlüğü uygulandı.

## §2 Sınır (kapsam dışı komşular)

| Komşu | İlişki | Not |
|-------|--------|-----|
| K008-domain | Veri modelinin sahibi | Entity/ValueObject/domain event → K008 |
| K009-security | Şifreleme/anahtar | key_hash, cipher sınırı |
| K013-integraion | synchronization çift kayıt | Sınır → VERIFICATION REQUIRED |
| K020-recomenataon | Arama keşfi | search sahipliği sınırı → VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. `Kademe` ataması şeffaf rol kuralıdır (index/master/kapsam → Kademe-1; destek → Kademe-2) — diskte `kademe` alanı yok, uydurma yok.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Domain künyesi | Kademe-1 |
| mimari | `data-master.md` | K007 master | Kademe-1 |
| mimari | `data-kapsam.md` | Kapsam sınırı | Kademe-1 |
| mimari | `data-bilesenler.md` | Bileşen envanteri + sahiplik | Kademe-2 |
| mimari | `data-bagimliliklar.md` | Bağımlılık ekseni | Kademe-2 |
| mimari | `data-kararlar.md` | Karar defteri | Kademe-2 |
| kod | `data-kod-ornekleri.md` | Gerçek depo kod parçaları | Kademe-2 |
| kanit | `data-riskler.md` | Risk defteri | Kademe-2 |
| kod | `data-test-stratejisi.md` | Test stratejisi | Kademe-2 |
| kanit | `data-web-arastirma.md` | Sistemati̇k web araştırması | Kademe-2 |
| mimari | `data-evrensel-dil.md` | TR↔EN terim sözlüğü | Kademe-2 |
| kanit | `data-dogrulama.md` | Doğrulama | Kademe-2 |


## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K007-data | [[K007-data]] | ./K007-data/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | iki yönlü atıf | M→8 · M←1 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ↔ | iki yönlü atıf | M→2 · M←1 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ↔ | iki yönlü atıf | M→5 · M←7 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | → | bağımlı (depends_on) | M→23 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ↔ | bağımlı (depends_on) | M→20 · M←5 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | → | bu K referanslıyor | M→1 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ↔ | iki yönlü güçlü referans | M→17 · M←10 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ↔ | iki yönlü güçlü referans | M→14 · M←17 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ↔ | iki yönlü güçlü referans | M→31 · M←16 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←18 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ← | karşı taraf güçlü referanslıyor | M→0 · M←10 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ↔ | iki yönlü güçlü referans | M→35 · M←24 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ← | karşı taraf referanslıyor | M→0 · M←5 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ← | karşı taraf referanslıyor | M→0 · M←2 (kök .md, 2026-10-18) |


## §BAĞLANTILAR

> **Okuma kuralı:** `←` işareti **hedef K'nın kendi dizinini kasteder** (`K00X-*/index.md` veya o dizindeki bir belge) — yani "K00X burada K007'den bahsediyor" anlamına gelir; K007'nin kendi alt klasör belgeleri değil.
> **Yön semantiği:** `↔` = iki yönlü referans kanıtı var · `→` = yalnız bu K'nın belgeleri hedefi anıyor · `←` = yalnız hedef K'nın dizini bu K'yı anıyor · `⚠️` = arama yapıldı, iki yönde de kanıt yok.
> **Kanıt standardı:** Gerçek dosya adı + satır numarası; uydurma referans yok.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K000-isletim-sistemi | `[[K000-isletim-sistemi/index]]` | `../K000-isletim-sistemi/index.md` | ↔ | Dosya sistemi ve bellek — K007 K000'i anıyor + K000 K007'yi anıyor | `data-kapsam.md` 82 · `K000 index.md` 136 |
| K001-donanım | `[[K001-donanım/index]]` | `../K001-donanım/index.md` | ↔ | Depolama donanımı (SSD) — K007 K001'i anıyor + K001 K007'yi anıyor | `data-kapsam.md` 83 · `K001 index.md` 110 |
| K002-electroncis | `[[K002-electroncis/index]]` | `../K002-electroncis/index.md` | → | Depolama donanımı — sınır beyanı | `data-web-arastirma.md` 395 |
| K003-audio | `[[K003-audio/index]]` | `../K003-audio/index.md` | → | Ses dosya formatları — sınır beyanı | `data-web-arastirma.md` 395 |
| K004-dsp | `[[K004-dsp/index]]` | `../K004-dsp/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K005-firmware | `[[K005-firmware/index]]` | `../K005-firmware/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K006-yapay-zeka | `[[K006-yapay-zeka/index]]` | `../K006-yapay-zeka/index.md` | ↔ | ML veri hattı — K006 bağımlı + K007 K006'ya referans verir | `ai-master.md` 102,213 · `K007-data/index.md` 23 · `data-bagimliliklar.md` 74 |
| K008-domain | `[[K008-domain/index]]` | `../K008-domain/index.md` | ↔ | Domain entity persistence — K007 depends_on K008 + K008 K007'yi anıyor | `data-bagimliliklar.md` 23,61 · `K008 index.md` 31,93 · `domain-master.md` 188 |
| K009-security | `[[K009-security/index]]` | `../K009-security/index.md` | ↔ | Şifreleme ve erişim kontrolü — K007 depends_on K009 + K009 K007'yi anıyor | `data-bagimliliklar.md` 24,62 · `security-bagimliliklar.md` 24 · `K009 index.md` 18,68 |
| K010-network | `[[K010-network/index]]` | `../K010-network/index.md` | ↔ | Replication ve ağ aktarımı — K007 K010'u anıyor + K010 K007'yi anıyor | `data-kapsam.md` 83 · `K010 index.md` 16,68 |
| K011-application | `[[K011-application/index]]` | `../K011-application/index.md` | ↔ | Repository katmanı — K007 K011'i anıyor + K011 K007'yi anıyor | `K011 application-master.md` 24 · `data-bagimliliklar.md` 25 |
| K012-services | `[[K012-services/index]]` | `../K012-services/index.md` | ↔ | Servis veri erişimi — K007 K012'yi anıyor + K012 K007'yi anıyor | `data-bagimliliklar.md` 26 · `K012 device-service-anayasa.md` 57,115 |
| K013-integraion | `[[K013-integraion/index]]` | `../K013-integraion/index.md` | ↔ | Sync ve entegrasyon verisi — K007 K013'ü anıyor + K013 K007'yi anıyor | `K013 index.md` 16,66 · `data-bagimliliklar.md` 27 |
| K014-media | `[[K014-media/index]]` | `../K014-media/index.md` | ← | Yalnız K014 K007'yi anıyor | `K014 downloads/ornekler.md` 337 |
| K015-AI | `[[K015-AI/index]]` | `../K015-AI/index.md` | ← | Yalnız K015 K007'yi anıyor | `K015 ai-bagimliliklar.md` 29 |
| K016-observability | `[[K016-observability/index]]` | `../K016-observability/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K17-devops | `[[K17-devops/index]]` | `../K17-devops/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K18-testing | `[[K18-testing/index]]` | `../K18-testing/index.md` | ← | Yalnız K18 K007'yi anıyor | `K18 index.md` 22 · `testing-master.md` 121 |
| K019-comilance | `[[K019-comilance/index]]` | `../K019-comilance/index.md` | ← | Yalnız K019 K007'yi anıyor | `controls/index.md` 94 |
| K020-discovery | `[[K020-discovery/index]]` | `../K020-discovery/index.md` | ↔ | Arama keşfi / search sahipliği sınırı — K020 K007'yi anıyor | `K020 discovery-bagimliliklar.md` 10,51 · `data-bagimliliklar.md` 28 |
| K021-recomenataion | `[[K021-recomenataion/index]]` | `../K021-recomenataion/index.md` | ← | Yalnız K021 K007'yi anıyor | `K021 recommendation-master.md` 110,475 |
| **K007-data (kendi)** | `[[K007-data/index]]` | `index.md` | ○ | Kendi kendine bağımlılık yok. | Bu belge (bu satır) |

**Authority:** `data-master.md` (Master authority belgesi — bu index authority'si tamamlar).
