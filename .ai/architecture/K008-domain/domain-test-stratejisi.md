---
node_id: K008-domain/test-stratejisi
node_type: root
status: verified
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
  - K008-domain/domain-kod-ornekleri.md
evidence:
  - shared/tests/Events/DomainEventTest.php
  - shared/tests/Events/EventDispatcherTest.php
  - shared/tests/Repository/ApiKeyRepositoryTest.php
  - shared/composer.json
validation: 2026-10-12 — test dosyaları diskte var; phpunit ^10.5 + phpstan level 5 (composer.json)
open_questions:
  - "Her çocuk için ayrı test dosyası henüz yok — dalga 1–2'de çocukların `-dogrulama.md`'si test gap'leri listeler"
group: kod
---

# K008-domain — Test Stratejisi

> **Authority: Bayram Ali / Vault Steward.** Domain katmanının nasıl test
> edildiği ve çocukların nasıl doğrulanacağı. Bu dosya stratejiyi bağlar;
> somut test senaryoları çocuk `-dogrulama.md`'sindedir.

## §1 TEST PİRAMİDİ (domain katmanı)

| Katman | Kapsam | Sahip | Durum (2026-10-12) |
|---|---|---|---|
| Birim (unit) | Aggregate kuralları, VO eşitliği, event payload | çocuk `-dogrulama.md` | Kısmi — 2 event testi var |
| Sözleşme (contract) | Event base class + dispatcher davranışı | kök (`Events/`) | Kanıtlı (§3) |
| Entegrasyon | Repository + SQLite/MySQL (test double) | `shared/tests/Repository/` | Kanıtlı (§4) |
| Statik analiz | phpstan level 5 | `composer.json` scripts.stan | Kanıtlı |
| Mutation | Kötü niyetli test kırma | infection (araç envanterinde) | `⚠️ VERIFICATION REQUIRED` (§6) |

## §2 SÖZLEŞME TESTLERİ (kök kanıtı)

| Test dosyası | Ne doğruluyor | K008 bağlantısı |
|---|---|---|
| `shared/tests/Events/DomainEventTest.php` | Domain event sözleşmesi (sonuç) | master §3.3 |
| `shared/tests/Events/EventDispatcherTest.php` | PSR-14 dispatcher (öncelik, durdurma) | kod-ornekleri §4 |

**Kural:** Yeni domain event ekleyen bir çocuk, bu iki testin **kapsadığı**
sözleşme alanını (eventId/occurredOn/payload/eventName) ihlal edemez; ihlal
`domain-dogrulama.md` §8'e işlenir.

## §3 ENTEGRASYON TESTLERİ (repository kanıtı)

| Test dosyası | Ne doğruluyor | Kanıt |
|---|---|---|
| `shared/tests/Repository/ApiKeyRepositoryTest.php` | Repository akışı (hash eşleşmesi, aktiflik) | kod-ornekleri §5 |
| `shared/tests/Repository/ApiKeyCreateCommandTest.php` | Anahtar oluşturma komutu | `shared/tests/Repository/` |
| `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | Test altyapısı: SQLite üstünde gerçek şema | Dış bağımlılık (yalnız test) |

**Gözlem:** Testler repository'i **arayüz üzerinden** (SQLite manager) sürer —
domain'in persistence bilmediği kuralı (K3) test tarafında da korunuyor.

## §4 ÇOCUK TEST KAPSAMI (dalga 1–2 beklentisi)

Her çocuk `-dogrulama.md` dosyasında en az şu test başlıklarını taşır:

| # | Başlık | Beklenen |
|---|---|---|
| T1 | Aggregate invariant (kural ihlali reddi) | ≥2 senaryo |
| T2 | Event payload alanı doğruluğu | her event için 1 senaryo |
| T3 | VO eşitliği (immutable) | ≥1 senaryo |
| T4 | Repository sözleşmesi (kapsayan çocuk varsa) | Dosya/Satır kanıtı |
| T5 | Sınır ihlali denemesi (Y1–Y6) | ≥1 negatif senaryo |

## §5 KALİTE KAPILARI (composer kanıtı)

| Kapı | Komut (composer.json) | Eşik |
|---|---|---|
| Birim test | `scripts.test` = `phpunit` | Tümü geçmeli |
| Statik analiz | `scripts.stan` = `phpstan analyse src --level=5` | Seviye 5; yükseltme K016 |
| Mutation (opsiyonel) | infection envanterinde | K016 sahipliğinde |

## §6 DOĞRULAMA BOŞLUKLARI (`⚠️ VERIFICATION REQUIRED`)

| # | Boşluk | Kapatma yolu |
|---|---|---|
| B1 | `discovery`/`recommendation`/`device-management`/`context-mapping` için test dosyası yok | Dalga 3 çocuğun `-dogrulama.md`'si |
| B2 | Infection mutation testi gerçekten çalışıyor mu? | `infection` çalıştırma kanıtı |
| B3 | `phpunit.xml` kapsam ayarları (hangi dizinler) | Dosya okuma |
| B4 | Event listener'ların entegrasyon testi (dinleyici sırası) | `EventDispatcherTest` içeriğinin okunması |

## §7 TEST EKLEME PROTOKOLÜ

| Adım | Kural |
|---|---|
| 1 | Test önce senaryo (`-{çocuk}-dogrulama.md` T1–T5), sonra kod |
| 2 | Test adı `{Beceri}_{Senaryo}_{Beklenen}` biçiminde |
| 3 | Test persistence'i **gerçek MySQL değil** test double/SQLite kullanır |
| 4 | Yeni event için: `DomainEventTest` kapsamı genişletilmeden çocuk tamamlanmaz |
| 5 | Mutation skoru K016'ya raporlanır (K008 yetkisi dışı) |

---

*Authority: Bayram Ali / Vault Steward · K008-domain test stratejisi · 2026-10-12 · phpunit ^10.5 · phpstan level 5*
