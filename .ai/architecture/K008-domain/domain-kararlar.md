---
node_id: K008-domain/kararlar
node_type: root
status: verify-required
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
  - K008-domain/domain-kapsam.md
evidence:
  - .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md
  - shared/src/Events/EventDispatcher.php
  - shared/composer.json
validation: 2026-10-12 — her karar: bağlam, karar, alternatif, sonuç + kaynak
open_questions:
  - "K1–K6 statüsü draft'tır; kesinleşmesi orkestratör onayı ister"
group: mimari
---

# K008-domain — Kararlar (Domain ADRs)

> **Authority: Bayram Ali / Vault Steward.** K008-domain'e özgü mimari kararlar.
> Format: Bağlam → Karar → Alternatif(ler) → Sonuç(lar). Kaynaksız karar yok.
> Üst otorite: ADR-097 (K-space v3) — bu kararlar onunla çelişemez.

## §1 KARAR DİZİNİ

| # | Başlık | Durum | Tarih |
|---|---|---|---|
| K1 | K008 = domain katmanı (servisler değil) | verify-required | 2026-10-12 |
| K2 | 10 Bounded Context = 10 çocuk | verify-required | 2026-10-12 |
| K3 | Domain katmanı persistence/HTTP bilmeyecek (purist sınır) | verify-required | 2026-10-12 |
| K4 | Domain event PSR-14 ile senkron yayılır (önce) | verify-required | 2026-10-12 |
| K5 | Öneri/keşif domain gereksinimi K008'de, uygulaması K016'da | verify-required | 2026-10-12 |
| K6 | İki-eksen gruplama (mimari/kod/kanit) zorunlu | verify-required | 2026-10-12 |

## §2 K1 — K008 = DOMAIN KATMANI

| Alan | İçerik |
|---|---|
| Bağlam | Eski disk notlarında `K008-servisler` adı vardı; v2 spec §2.8 `K008-domain` (satır 298) |
| Karar | K008 = `domain` katmanı (DDD). "Servisler" adı kullanılmaz; v2 spec adı korunur |
| Alternatif A | Eski `K008-servisler` adında ısrar — reddedildi: v2 spec bağlayıcı (§3 KURAL 1) |
| Alternatif B | K008'i servis+domain karma — reddedildi: K006 zaten servis/topoloji sahibi |
| Sonuç | CONFLICT C1 açık bırakıldı (`⚠️ VERIFY REQUIRED`), eski kayıt silinmedi |
| Kaynak | v2 spec §2.8 · ADR-097 §1 |

## §3 K2 — 10 BOUNDED CONTEXT = 10 ÇOCUK

| Alan | İçerik |
|---|---|
| Bağlam | Her çocuk bir Bounded Context midir, yoksa birer alt alan mı? |
| Karar | Her çocuk = bir Bounded Context. Her BC kendi `-kapsam.md` + 5 dosyası setini alır |
| Alternatif A | Bazı children'ı alt domain yap (örn. `library` → `user` altı) — reddedildi: v2 spec 10 çocuğu eşit seviyede sayar |
| Alternatif B | Tek dev domain + dosya bazlı ayırma — reddedildi: ADR-097 §5.5 gruplama buna göre |
| Sonuç | Dalga 1–2'de 6 çocuk tam set (36 MD); dalga 3'te 4 çocuk yol listesi |
| Kaynak | v2 spec §2.8 satır 298–307 · ADR-097 §5.5 |

## §4 K3 — DOMAIN PURITY SINIRI

| Alan | İçerik |
|---|---|
| Bağlam | `shared/src/` altında domain event, repository, DTO iç içe; domain katmanı sınırı belirsiz |
| Karar | Domain katmanı PDO/HTTP/UI/dosya-sistemi **bilemez** (master §3.2, bagimliliklar §4) |
| Alternatif A | Pragmatik "her şey shared/" — reddedildi: BC sınırı çöker, test edilemezlik artar |
| Alternatif B | Tam purist katman (fiziksel dizin ayırma) — ertelendi: repo mevcut yapısı korunur, karar yalnızca **kural** olarak bağlanır |
| Sonuç | İhlal görülürse risk kaydı (R-satırı) + rapor; kod değişikliği K008 agent'ının yetkisinde değil |
| Kaynak | DDD purizm — `domain-dogrulama.md` §8 (web kanıtı) · `domain-bagimliliklar.md` §4 |

## §5 K4 — DOMAIN EVENT YAYIN STRATEJİSİ

| Alan | İçerik |
|---|---|
| Bağlam | 9 domain event sınıfı var; yayım mekanizması netleşmeli |
| Karar | Domain event'ler PSR-14 `EventDispatcherInterface` ile **senkron** yayınlanır; async/queue iş katmanının kararıdır |
| Alternatif A | Event sourcing tamamen — reddedildi: mevcut yapıda store yok, YAGNI |
| Alternatif B | Kuyruk (async) ilk seferden — ertelendi: senkron dispatcher kanıtlı; async gereksinimi `playback`/`media` ölçeğinde ayrıca ölçülecek |
| Sonuç | Sözleşme: base class (eventId + occurredAt) + dispatcher (s.20/s.28); consumer idempotent olmalı |
| Kaynak | `shared/src/Events/EventDispatcher.php` s.20/s.28 · `php-fig.org/psr/psr-14/` |

## §6 K5 — ÖNERİ/KEŞİF SAHİPLİĞİ SINIRI

| Alan | İçerik |
|---|---|
| Bağlam | `recommendation`/`discovery` v2 spec'te K008'de; kod `shared/src/AI/AIEngine.php` (s.28 sınıf, s.30–32 eşik sabitleri, s.49 akış) |
| Karar | **Domain gereksinimi** (öneri kuralları, eşiklerin anlamı, çeşitlilik hedefi) K008-domain'de; **uygulama** (algoritma, kod) K016/teknoloji katmanında |
| Alternatif A | K008'i kod sahibi yap — reddedildi: ADR-097 K008'i domain olarak konumlandırır |
| Alternatif B | Konuyu tamamen K008'den çıkar (K006'ya taşı) — ertelendi: CONFLICT C2 orkestratör kararı |
| Sonuç | `AIEngine` eşik sabitleri K008'de **kanıt** olarak gösterilir, sahiplik iddiası yapılmaz |
| Kaynak | `shared/src/AI/AIEngine.php` · v2 spec §2.8 satır 304–305 · CONFLICT C2 |

## §7 K6 — İKİ EKSEN GRUPLAMA ZORUNLULUĞU

| Alan | İçerik |
|---|---|
| Bağlam | ADR-097 §5.5: her dosya hem içerik ekseninde hem güvenilirlik ekseninde |
| Karar | Her K008 dosyası `group: mimari|kod|kanit` taşır; `index.md`'de §GRUPLAR tablosu zorunlu |
| Alternatif A | Tek eksen (dosya türü) — reddedildi: ADR-097 bağlayıcı |
| Alternatif B | Gönüllü gruplama — reddedildi: frontmatter doğrulaması (master §9-C1) bunu kontrol eder |
| Sonuç | Kök 12 MD'de grup dağılımı: mimari 7 · kod 3 · kanit 3 (test-stratejisi kod sayıldı) |
| Kaynak | ADR-097 §5.5 · `K008-domain/index.md` §GRUPLAR |

## §8 KARAR GÜNCELLEME PROTOKOLÜ

| Durum | Eylem |
|---|---|
| Yeni karar | Dizine satır + tam bölüm (bağlam/karar/alternatif/sonuç/kaynak) |
| Revizyon | Eski satır silinmez; `Durum` = `revize (tarih, no)` |
| İptal | `Durum` = `iptal (gerekçe)` — kayıt arşivde kalır |
| Statü yükseltmesi (`verify-required` → `verified`) | Yalnız kanıt eklendikçe; `domain-dogrulama.md` referansı zorunlu |

---

*Authority: Bayram Ali / Vault Steward · K008-domain kararlar · 2026-10-12 · K1–K6 (hepsi draft)*
