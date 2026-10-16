---
node_id: "K015-media/downloads/downloads-web-arastirma"
node_type: "module"
status: "verify-required"
parent_id: "K015-media/downloads"
children: []
depends_on: ["K015-media/media-master"]
evidence:
  - "16 URL 2026-10-16'da canlı doğrulandı (hepsi HTTP 200; Wikipedia özeti REST API'den okundu)"
  - "web.dev/the-offline-cookbook 404 idi → doğru adres web.dev/articles/offline-cookbook (düzeltildi, 2026-10-16)"
validation: "16/16 URL canlı (2026-10-16); §2'deki her ana iddia ≥2 kaynakla çaprazlandı; uydurma eşik/URL YOK — bulunamayanlar §5'te ⚠️"
open_questions:
  - "Resume eşikleri (parça boyutu, deneme sayısı) → ⚠️ UNKNOWN (kanıt yok — §5)"
  - "Kapsam: indirme saklama politikası → ⚠️ UNKNOWN (media-master §12/3)"
group: kanit
---

# K015-media · downloads · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam: indirme/parça-istek (Range) semantiği,
> bütünlük, iptal ve yeniden-deneme kaynakları

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: her satır **iddia (kaynak ne diyor) × URL × tarih × durum** taşır. Bu dosya yalnız aşağıdaki
> 16 URL'yi taşır; kardeş dosyaların (offline · media-cache) URL'leri buraya kopyalanmaz.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-16 | Durum |
|---|---|---|---|---|
| D-1 | HTTP Semantics spesifikasyonu: ortak terminoloji + paylaşılan protokol yönleri (Range/If-Range/206 semantiğinin anayasası) | https://www.rfc-editor.org/rfc/rfc9110 | Tarih: 2026-10-16 | HTTP 200 |
| D-2 | Range başlığı: isteğin hangi bölümünün döndürüleceğini belirtir; tek istekte birden fazla parça istenebilir; sunucu parçaları 206 ile döndürür | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Range | Tarih: 2026-10-16 | HTTP 200 |
| D-3 | 206 Partial Content: aralık isteğine yanıt; gövde, Range ile istenen verileri içerir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/206 | Tarih: 2026-10-16 | HTTP 200 |
| D-4 | If-Range: aralık isteğini koşullu yapar; koşul sağlanırsa 206, sağlanmazsa **tam kaynak 200** döner | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Range | Tarih: 2026-10-16 | HTTP 200 |
| D-5 | ETag: kaynağın belirli bir sürümünün tanımlayıcısı; içerik değişmediyse tam yanıtın tekrar gönderilmesini engeller | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag | Tarih: 2026-10-16 | HTTP 200 |
| D-6 | Content-Range: yanıt gövdesinin tam kaynağa göre nereye ait olduğunu bildirir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Range | Tarih: 2026-10-16 | HTTP 200 |
| D-7 | Accept-Ranges: sunucunun aralık isteklerini desteklediğini duyurur; değer birim tanımlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Ranges | Tarih: 2026-10-16 | HTTP 200 |
| D-8 | 416 Range Not Satisfiable: sunucu istenen aralığı servis edemedi (aralık yok/anlamsız) | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/416 | Tarih: 2026-10-16 | HTTP 200 |
| D-9 | Byte serving: HTTP/1.1 ile mesajın yalnız bir bölümünün gönderilmesi; Accept-Ranges duyurusu → Range isteği → 206 + Content-Range; geçersiz aralık → 416 | https://en.wikipedia.org/wiki/Byte_serving | Tarih: 2026-10-16 | HTTP 200 |
| D-10 | Digest Fields: içerik/temsil bütünlüğü için HTTP alanları (Content-Digest · Repr-Digest; Want-* tercih alanları) | https://www.rfc-editor.org/rfc/rfc9530 | Tarih: 2026-10-16 | HTTP 200 |
| D-11 | Instance Digests (öncül): Content-MD5 mesaj gövdesini kapsar, Content-Range ile **kısmi** yanıtlarda tam dosyayı kapsamaz — sınırlılık gerekçesi | https://www.rfc-editor.org/rfc/rfc3230 | Tarih: 2026-10-16 | HTTP 200 |
| D-12 | fetch(): kaynağı ağdan getirmeyi başlatır; yanıt hazır olduğunda fulfilled olan bir promise döner | https://developer.mozilla.org/en-US/docs/Web/API/fetch | Tarih: 2026-10-16 | HTTP 200 |
| D-13 | AbortController: istenen Web isteklerini **olduğu zaman** iptal etmeyi sağlar | https://developer.mozilla.org/en-US/docs/Web/API/AbortController | Tarih: 2026-10-16 | HTTP 200 |
| D-14 | Retry-After: kullanıcının (istemcinin) sonraki isteği ne kadar beklemesi gerektiğini bildirir; üç ana kullanım durumu | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Retry-After | Tarih: 2026-10-16 | HTTP 200 |
| D-15 | 429 Too Many Requests: istemci belirli sürede çok fazla istek gönderdi; buna "rate limiting" denir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429 | Tarih: 2026-10-16 | HTTP 200 |
| D-16 | Streams API: ağdan gelen veri akışlarına programatik erişim ve istediğiniz gibi işleme | https://developer.mozilla.org/en-US/docs/Web/API/Streams_API | Tarih: 2026-10-16 | HTTP 200 |

---

## §2 ÇAPRAZ DOĞRULAMA (her ana iddia ≥2 kaynak)

### §2a Ana iddialar (hepsi ≥2 kaynak)

| # | Ana iddia (CoreMusic indirme tasarımı için) | Kaynak 1 | Kaynak 2 (+) | Sonuç |
|---|---|---|---|---|
| A-1 | **Resume = parça isteği:** kaldığı yerden devam, `Range` başlığı + `206` + `Content-Range` ile taşınır | D-2 (MDN Range) | D-3 (206) + D-6 (Content-Range) + D-9 (byte serving) + D-1 (RFC 9110) | ✅ 5 kaynak — çapraz yeterli |
| A-2 | **Yarıda kesilme bütünlüğü:** koşullu aralık (`If-Range` + `ETag`) parçanın aynı kaynağa ait olmasını korur; koşul sağlanmazsa tam gövde 200 gelir → kısmi dosya baştan denenmeli | D-4 (If-Range) | D-5 (ETag) + D-1 (RFC 9110) | ✅ 3 kaynak |
| A-3 | **Yetenek + ret:** sunucu `Accept-Ranges` ile destek duyurur; istenen aralık sunulamazsa 416 döner (istemci baştan dener) | D-7 (Accept-Ranges) | D-8 (416) + D-9 (byte serving) | ✅ 3 kaynak |
| A-4 | **Bütünlük doğrulaması:** ham parça bütünlüğü digest alanlarıyla doğrulanabilir; eski `Content-MD5` kısmi yanıtta tam dosyayı kapsamaz (neden yeni alan) | D-10 (RFC 9530) | D-11 (RFC 3230) | ✅ 2 kaynak |
| A-5 | **İptal:** çalışan indirme `AbortController` ile iptal edilir; `fetch` bu sinyalle sürer | D-13 (AbortController) | D-12 (fetch) | ✅ 2 kaynak |
| A-6 | **Yavaşlama/bekleme:** sunucu "az iste" dediğinde bekleme süresi `Retry-After` ile taşınır (429 = rate limiting) | D-14 (Retry-After) | D-15 (429) | ✅ 2 kaynak |

### §2b Destekleyici iddialar (tek kaynak — ⚠️ ana iddia sayılmaz)

| # | İddia | Kaynak | Not |
|---|---|---|---|
| A-7 | Parçalı veri parça parça işlenebilir (akış) | D-16 (Streams API) | ⚠️ tek kaynak — bu dosyada ana iddia yapılmaz; §5 W-1 |

---

## §3 K015/downloads KARŞILIĞI (iddia → yüzey → dosya·satır)

| # | İddia | CoreMusic yüzeyi | Bağ (Dosya·Satır / durum) |
|---|---|---|---|
| 1 | A-1 resume zinciri (Range/206/Content-Range) | kapsam §3 `resumed` durumu · §5 H-1 | `downloads-kapsam.md` §3/§5 H-1; repo'da bu semantik **akış** yüzeyinde: `MusicStreamHandler.php:91,101-106` (okundu 2026-10-16) |
| 2 | A-2 If-Range/ETag bütünlüğü | kapsam §5 H-3 (sürüm değişimi → baştan) | `downloads-kapsam.md` §5 H-3; repo ETag üretimi `MusicStreamHandler.php:80` |
| 3 | A-3 Accept-Ranges/416 | kapsam §5 H-2 | `downloads-kapsam.md` §5 H-2; repo 416 yolu `MusicStreamHandler.php:95-99` |
| 4 | A-4 digest bütünlüğü | kapsam §3 `completed` kontrolü (§1a-4) | `downloads-kapsam.md` §3 S-6 — **uygulama yok, tasarım iddiası** |
| 5 | A-5 iptal | kapsam §3 `paused`/`failed` (İptal yüzeyi) | `downloads-kapsam.md` §1b · §3 |
| 6 | A-6 Retry-After | kapsam §5 H-5 (beklemeli `queued`) | `downloads-kapsam.md` §5 H-5 |

> **Sınır notu:** repo'daki Range davranışı `GET /stream/{id}` (seek/akış) içindir (`MusicStreamHandler.php:10`);
> **diske indirme endpoint'i 0 isabet** (kapsam §2/5 ile aynı bulgu). Bu dosya "indirme uygulandı" iddiası **üretmez**.

---

## §4 ERİŞİM KAYDI (hangi URL ne zaman doğrulandı)

| Erişim olayı | Tarih | Kapsam | Sonuç |
|---|---|---|---|
| Tur-1 — ilk tarama | 2026-10-16 | aday havuz | `web.dev/the-offline-cookbook` **404** → adres `web.dev/articles/offline-cookbook` ile düzeltildi (bu dosyada kullanılmadı — offline kardeşine tahsis) |
| Tur-1 — doğrulama | 2026-10-16 | §1 tablosu (D-1..D-16) | **16/16 HTTP 200**; D-9 içeriği Wikipedia REST summary'den okundu |
| Tur-1 — 404 eleme | 2026-10-16 | adaylar | `web.dev/articles/file-system-api`, `jakearchibald.com/2016/streams-are-weird` (404) → havuza alınmadı |

| Erişim Kuralı | |
|---|---|
| Yenileme | bir satırın durumu eskiyse URL yeniden fetch edilir; edilmeyenin tarihi **eskisinde kalır** |
| Ölü kaynak | 404/410 → satır silinmez, durum `DEAD (erişim: YYYY-MM-DD)` olur + §5'e W-kaydı açılır |
| Yeni URL | §1'e yalnız o gün doğrulanarak eklenir; kardeş dosya URL'leri tekrarlanmaz |

---

## §5 AÇIK KONULAR (⚠️ — bulunamayan / kanıt bekleyen)

| # | Konu | Etiket |
|---|---|---|
| W-1 | Parça (chunk) boyutu /akış işleme stratejisi için ikinci kaynak | ⚠️ VERIFICATION REQUIRED — D-16 tek kaynak; eşik **uydurulmaz** |
| W-2 | Resume/deneme sayısına dair **bilimsel/eşik** kaynak | ⚠️ BULUNAMADI — ne "3 deneme" ne "500MB" gibi bir sayı bu dosyada yazıldı (uydurma yasak) |
| W-3 | İndirme saklama TTL/boyut politikası | ⚠️ UNKNOWN (media-master §12/3) — kapsam §6/1 ile aynı kayıt |
| W-4 | Digest alanının CoreMusic indirme zincirine nasıl gireceği (nerede doğrulanır) | ⚠️ VERIFICATION REQUIRED — tasarım kararı (kapsam §3 S-6) |

---

## §6 KARDEŞ DOSYA İLİŞKİSİ (bu dosyanın yeri)

| Kardeş | İlişki | Kanıt yönü |
|---|---|---|
| `downloads-kapsam.md` | §2a iddiaları kapsam §3/§5'teki durumlara bağlandı | kanıt → kapsam (§3) |
| `downloads-ornekler.md` | A-1/A-3 kodda nasıl görünür (Range çözümleme) | kanıt → örnek |
| `downloads/index.md` | §GRUPLAR + FM açık sorular bu dosyada derinleştirilir (W-3) | kanıt → index |
| `../offline/*` · `../media-cache/*` | URL havuzları **ayırt edilmiştir** (tekrar yok) | havuz → kardeş |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
