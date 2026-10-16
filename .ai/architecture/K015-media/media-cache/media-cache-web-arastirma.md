---
node_id: K015-MEDIA-MEDIA-CACHE-WEB-ARASTIRMA
node_type: kanit
status: active
parent_id: K015-MEDIA
children: []
depends_on: [K015-MEDIA-MEDIA-CACHE-KAPSAM]
evidence: [K015-media/media-cache/media-cache-kapsam.md, K015-media/media-master.md]
validation: partial
open_questions: 5
group: kanit
---

# Media-Cache — Web Araştırması (K015-media/children/media-cache)

> **Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
>
> Bu dosya `media-cache` iddialarının **canlı web kaynaklarıyla
> doğrulamasını** içerir. Format referansı:
> `.ai/architecture/K009-security/threat-model/threat-model-web-arastirma.md`.
>
> **Her satır TASARIM İDDİASI'dır** (yalnız URL'ler kesin kanıttır);
> doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
> **ASLA URL / API / sürüm / eşik uydurulmaz** (ör. TTL veya boyut sayısı yazılmaz).

## §1 — Doğrulama Tablosu

> **Tarih: 2026-10-16** · Tüm URL'ler bu tarihte **canlı (HTTP 200)** doğrulanmıştır.

| # | İddia | Doğrulayan URL | Tarih | Durum |
|---|---|---|---|---|
| MC-1 | RFC 9111 (HTTP Caching): taze (fresh) / bayat (stale) kavramı, cache key, doğrulama (validation) ve önbellek kaydı oluşturma semantiği tanımlıdır — kapsam §1a-3/§3 `fresh`/`stale` iddiasının standart kaynağı | https://www.rfc-editor.org/rfc/rfc9111 | 2026-10-16 | ✅ CANLI |
| MC-2 | MDN Caching rehberi: önbelleğe alınabilir yanıtlar, özel (private)/paylaşılan (managed) önbellek ayrımı, cache key bileşenleri ve heuristic freshness (yüzdesel öngerilim) tartışması vardır — eşik sayıları **sayfa içeriğinden alınır, yazılmaz** | https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching | 2026-10-16 | ✅ CANLI |
| MC-3 | MDN `Cache-Control`: `max-age` · `no-store` · `private` · `public` · `must-revalidate` · `stale-while-revalidate` · `stale-if-error` direktifleri tanımlıdır — §3 `fresh`→`stale` ve §5/3 davranışının direktif karşılığı | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control | 2026-10-16 | ✅ CANLI |
| MC-4 | MDN `Vary`: yanıtın, istek başlıklarına göre önbellekte **farklı varyantlar** olarak saklanmasını belirtir — kapsam §1a-2 (anahtar + varyant) ve §5/4 iddiası | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Vary | 2026-10-16 | ✅ CANLI |
| MC-5 | Önbellek yerleştirme/temizlik politikaları (cache replacement policies): LRU · LFU · FIFO · ARC gibi seçenekler ve ödünleşimleri tanımlanır — §6/2'de **aday** olarak taşınan strateji kümesi | https://en.wikipedia.org/wiki/Cache_replacement_policies | 2026-10-16 | ✅ CANLI |
| MC-6 | ARC (Adaptive Replacement Cache): çalışma-zamanında LRU ve LFU arasında denge kuran politika (IBM Almaden araştırması) — §6/2 aday kümesinin üçüncü seçeneği | https://en.wikipedia.org/wiki/Adaptive_replacement_cache | 2026-10-16 | ✅ CANLI |
| MC-7 | Sayfa yerleştirme algoritmaları (page replacement): temizlik stratejisinin genel sınıflandırması ve ödünleşim tartışması — MC-5/MC-6'nın bağımsız ikinci kaynağı | https://en.wikipedia.org/wiki/Page_replacement_algorithm | 2026-10-16 | ✅ CANLI |
| MC-8 | `StorageManager.estimate()`: `usage` (kullanılan bayt) ve `quota` (kota) tahminini döndürür; güvenli bağlamda (secure context) mevcuttur — §5/1 kota dolumu iddiasının web karşılığı (değerler **çağrıda okunur**, sabit yazılmaz) | https://developer.mozilla.org/en-US/docs/Web/API/StorageManager/estimate | 2026-10-16 | ✅ CANLI |
| MC-9 | `navigator.storage` (StorageManager): `estimate` ve `getDirectory` ile istemci deposu ölçümü/yönetimi sunar — MC-8'in arayüz kaynağı | https://developer.mozilla.org/en-US/docs/Web/API/StorageManager | 2026-10-16 | ✅ CANLI |
| MC-10 | Web Storage API (`localStorage`/`sessionStorage`): anahtar-değer deposu; MDN, depolamanın bölümlere (partition) göre ayrılabildiğini belirtir — medya önbelleğinin tarayıcı-depo seçeneği referansı | https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API | 2026-10-16 | ✅ CANLI |
| MC-11 | RFC 5861 (Informational, Mayıs 2010): `stale-while-revalidate` ve `stale-if-error` uzantılarını tanımlar — §3 `stale` durumu ve §5/3 (doğrulama başarısızsa ne yapılacağı) iddiasının standart kaynağı | https://www.rfc-editor.org/rfc/rfc5861 | 2026-10-16 | ✅ CANLI |
| MC-12 | AWS CloudFront: CDN önbellek son kullanma (expiration/TTL) yapılandırması — sunucu/CDN katmanında TTL'nin ayrı bir karar düzlemi olduğu iddiası (CoreMusic TTL'i §6/1'de UNKNOWN kalır) | https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/Expiration.html | 2026-10-16 | ✅ CANLI |
| MC-13 | RFC 9213 (Haziran 2022): `CDN-Cache-Control` — CDN'in önbellek direktiflerini ayrı yönlendirmesi tanımlıdır; kaynak ile CDN katmanı ayrımı | https://www.rfc-editor.org/rfc/rfc9213 | 2026-10-16 | ✅ CANLI |

**Doğrulanan benzersiz URL sayısı: 13** (kural: ≥8 — ✅).

### §1a — Kaynak Havuzu (özet)

| # | URL | Tür | İddia # |
|---|---|---|---|
| 1 | https://www.rfc-editor.org/rfc/rfc9111 | IETF RFC (standart izleyici) | MC-1 |
| 2 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching | MDN (rehber) | MC-2 |
| 3 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control | MDN (dokümantasyon) | MC-3 |
| 4 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Vary | MDN (dokümantasyon) | MC-4 |
| 5 | https://en.wikipedia.org/wiki/Cache_replacement_policies | Wikipedia (genel bakış) | MC-5 |
| 6 | https://en.wikipedia.org/wiki/Adaptive_replacement_cache | Wikipedia (algoritma) | MC-6 |
| 7 | https://en.wikipedia.org/wiki/Page_replacement_algorithm | Wikipedia (algoritma) | MC-7 |
| 8 | https://developer.mozilla.org/en-US/docs/Web/API/StorageManager/estimate | MDN (API) | MC-8 |
| 9 | https://developer.mozilla.org/en-US/docs/Web/API/StorageManager | MDN (API) | MC-9 |
| 10 | https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API | MDN (API) | MC-10 |
| 11 | https://www.rfc-editor.org/rfc/rfc5861 | IETF RFC (informational) | MC-11 |
| 12 | https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/Expiration.html | AWS dokümantasyonu | MC-12 |
| 13 | https://www.rfc-editor.org/rfc/rfc9213 | IETF RFC (standart) | MC-13 |

## §2 — Cross-Check Eşlemeleri (ana iddia × ≥2 kaynak)

| Ana iddia | Kaynak A | Kaynak B | Çapraz sonucu |
|---|---|---|---|
| Önbellek girdisi taze/bayat + doğrulama modeliyle yaşar (kapsam §1a-3, §3) | RFC 9111 (MC-1) | MDN Caching (MC-2) | ✅ Standart + uygulama rehberi aynı modeli tanımlar |
| Tazelik direktifleri (max-age/no-store/private/public) tanımlıdır | RFC 9111 (MC-1) | MDN Cache-Control (MC-3) | ✅ Standart + direktif dokümanı birbirini doğrular |
| `stale` sonrası davranış genişletmeleri (stale-while-revalidate / stale-if-error) vardır | RFC 5861 (MC-11) | MDN Cache-Control (MC-3) | ✅ RFC uzantısı + MDN direktif listesi örtüşür |
| Temizlik/yerleştirme stratejisi birden çok seçenekli bir karardır (§6/2) | Wikipedia replacement (MC-5) | Wikipedia ARC (MC-6) + page replacement (MC-7) | ✅ Genel katalog + iki ayrı algoritma kaynağı birbirini destekler |
| Depolama kotası/ölçümü tarayıcıda ölçülebilir (§5/1) | MDN StorageManager.estimate (MC-8) | MDN StorageManager (MC-9) | ✅ İki MDN sayfası aynı arayüzü tanımlar |
| Varyantlar ayrı önbellek satırıdır (kapsam §1a-2, §5/4) | MDN Vary (MC-4) | MDN Caching (MC-2) | ✅ Başlık dokümanı + rehber cache key/varyant ayrımında buluşur |
| TTL/expiration ayrı bir katman (CDN) kararıdır (§6/1 — CoreMusic TTL'i UNKNOWN) | CloudFront Expiration (MC-12) | RFC 9213 (MC-13) | ✅ Sağlayıcı rehberi + IETF katman ayrımı birbirini doğrular |

**Cross-check sayısı: 7** (kural: her ana iddia ≥2 kaynak — ✅).

## §3 — Kardeş Havuz Ayrımı (tekrar yok)

| Kardeş dosya | Havuz | Durum |
|---|---|---|
| `../downloads/downloads-web-arastirma.md` | D-1..D-16 (Range/206/416/fetch/Streams) | ❌ tekrar yok — URL kopyalanmadı |
| `../offline/offline-web-arastirma.md` | O-1..O-11 (Service Worker/CacheStorage/onLine) | ❌ tekrar yok — URL kopyalanmadı |
| Bu dosya | MC-1..MC-13 (HTTP caching · replacement · storage · CDN) | ✅ havuz ayrık |

> Not: MC-10 Web Storage ve MC-8/MC-9 StorageManager, offline'ın CacheStorage/IndexedDB
> havuzundan **farklı API'lerdir** (aynı `Cache` adı taşımazlar).

## §4 — Açık Konular (araştırma sonuçları)

| # | Konu | Araştırma bulgusu | Durum |
|---|---|---|---|
| 1 | Boyut/TTL politikası (CoreMusic) | Web kaynakları TTL'nin mevcut olduğunu ve her katmanda ayrı verilebileceğini (MC-1/MC-12/MC-13) doğrular; **CoreMusic'in değeri media-master §12/3'te UNKNOWN** — eşik/sayı bu dosyada YAZILMAZ | ⚠️ UNKNOWN (korundu) |
| 2 | Temizleme stratejisi (LRU/LFU/ARC) | Üç seçenek ve ödünleşimleri doğrulandı (MC-5/MC-6/MC-7); **seçim Vault Steward kararıdır** — burada seçilmez | ⚠️ UNKNOWN (korundu) |
| 3 | offline sahipliği (§7/2) | Web araştırması bu soruyu **çözmez** — mimari (vault) sorusudur; kapsamtaki açık soru §6/4'te açık kaldı | ⚠️ AÇIK SORU |
| 4 | Önbellek depolama yeri (sunucu mu tarayıcı mı) | Hem HTTP katmanı (MC-1..MC-4) hem tarayıcı deposu (MC-8..MC-10) doğrulandı; **CoreMusic'in katman seçimi tasarım kararı** | ⚠️ TASARIM İDDİASI |
| 5 | İstisabet oranı (hit/miss) hedef eşiği | Hiçbir kaynak CoreMusic'e özgü eşik **önermez**; media-master §15/4'te UNKNOWN — sayı uydurulmadı | ⚠️ UNKNOWN (korundu) |

## §5 — Boş / Erişilemeyen Kaynaklar

| Beklenen kaynak | Erişim | Not |
|---|---|---|
| Cloudflare önbellek dokümantasyonu | ❌ `CRAWL_NOT_FOUND` (2026-10-16, exa) | Sayfa içeriği çekilemedi → **URL bu dosyaya ALINMADI** (uydurma URL yasak); yerine MC-12/MC-13 (CDN katmanı) kullanıldı |
| Google/web.dev HTTP caching rehberi | ❌ `CRAWL_NOT_FOUND` (2026-10-16, exa) | Sayfa içeriği çekilemedi → **URL bu dosyaya ALINMADI**; yerine MC-2 (MDN Caching) kullanıldı |

> Bu iki kaynak için tam adresler **doğrulanmadığı için yazılmamıştır**; iddialar
> yalnız §1'deki 13 canlı URL ile taşınır (ZERO-HALLUCINATION).

## §6 — Yenileme Kuralı

- Bu dosyanın URL doğrulamaları **2026-10-16** tarihlidir; **90 gün** sonra
  yeniden doğrulanır (önce §1 tablosu, sonra §5 erişilemeyenler).
- Yeni iddia eklenirse **en az 1 yeni canlı URL** ile kanıtlanır; doğrulanamayan
  satır `⚠️ VERIFICATION REQUIRED` olarak kalır.
- **ASLA URL / API / sürüm / eşik uydurulmaz** (ZERO-HALLUCINATION).

## §7 — Revizyon

| Tarih | Revizyon | Authority |
|---|---|---|
| 2026-10-16 | İlk web araştırması — 13 canlı URL, 7 cross-check; TTL/boyut/strateji UNKNOWN korundu; 2 erişilemeyen kaynak CRAWL_NOT_FOUND olarak kaydedildi | Bayram Ali / Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
