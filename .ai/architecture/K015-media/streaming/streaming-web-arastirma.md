---
node_id: K015-MEDIA-STREAMING-WEB-ARASTIRMA
node_type: kanit
status: active
parent_id: K015-MEDIA
children: []
depends_on: [K015-MEDIA-STREAMING-KAPSAM]
evidence: [K015-media/streaming/streaming-kapsam.md, K015-media/media-master.md]
validation: partial
open_questions: 3
group: kanit
---

# Streaming — Web Araştırması (K015-media/children/streaming)

> **Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
>
> Bu dosya `streaming` iddialarının **canlı web kaynaklarıyla
> doğrulamasını** içerir. Format referansı:
> `.ai/architecture/K009-security/threat-model/threat-model-web-arastirma.md`.
>
> **Her satır TASARIM İDDİASI'dır** (yalnız URL'ler kesin kanıttır);
> doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
> **ASLA URL / API / sürüm uydurulmaz.**

## §1 — Doğrulama Tablosu

> **Tarih: 2026-10-16** · Tüm URL'ler bu tarihte **canlı (HTTP 200)** doğrulanmıştır.

| # | İddia | Doğrulayan URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | MediaSource Extensions API (MSE), `MediaSource` / `SourceBuffer` ile istemcide akış tamponlaması ve parça eklemeyi tanımlar — akışın JS ile orkestre edilmesi bu API'ye dayanır | https://developer.mozilla.org/en-US/docs/Web/API/Media_Source_Extensions_API | 2026-10-16 | ✅ CANLI |
| 2 | WebRTC API, tarayıcıda gerçek zamanlı (düşük gecikmeli) medya akışı için P2P/RTC taşır — canlı akış protokolü adaylarından biri | https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API | 2026-10-16 | ✅ CANLI |
| 3 | HLS (HTTP Live Streaming) IETF standardıdır; segmentli akış, playlist ve codec taşıma düzeni RFC 8216 ile tanımlanır | https://datatracker.ietf.org/doc/html/rfc8216 | 2026-10-16 | ✅ CANLI |
| 4 | FLAC codec'inin resmi spesifikasyonu IETF CELLAR çalışma grubınca RFC 9639 olarak yayımlanmıştır (akışta taşınabilecek codec'lerden biri) | https://github.com/ietf-wg-cellar/flac-specification | 2026-10-16 | ✅ CANLI |
| 5 | `MediaSource` (MSE) tarayıcı desteği yaygın ama tam değildir (caniuse: mediasource) — kapsam içi iddia: MSE'ye dayalı tasarım evrensel değildir | https://caniuse.com/mediasource | 2026-10-16 | ✅ CANLI |
| 6 | hls.js, MSE kullanan açık kaynak bir HLS istemcisidir — "tarayıcıda HLS akışı" pratikte MSE üstünde kurulur | https://github.com/video-dev/hls.js | 2026-10-16 | ✅ CANLI |
| 7 | Canlı yayın gecikmesi (live latency) ölçümleri ve gecikme kaynakları — rebuffering/geride kalma iddiasının pratik zemini | https://ceeblue.net/latency-benchmarks/ | 2026-10-16 | ✅ CANLI |
| 8 | Shoutcast (ICY protokolü) ile Icecast arasındaki farklar; ICY tabanlı radyo akışının taşıma düzeni | https://hostcodelab.com/shoutcast-vs-icecast-streaming-server | 2026-10-16 | ✅ CANLI |
| 9 | Icecast canlı yayın sunucusu (sürüm sayfası 2.5.0); Opus/FLAC/WebM/MP3 akış desteği — dış servis/protokol adayının kendisi | https://icecast.org/ | 2026-10-16 | ✅ CANLI |
| 10 | MPEG-DASH istemcisi dash.js; DASH akışı da MSE üstünde kurulur — protokol adayı #2 | https://github.com/Dash-Industry-Forum/dash.js | 2026-10-16 | ✅ CANLI |

**Doğrulanan benzersiz URL sayısı: 10** (kural: ≥8 — ✅).

### §1a — Kaynak Havuzu (özet)

| # | URL | Tür | İddia # |
|---|---|---|---|
| 1 | https://developer.mozilla.org/en-US/docs/Web/API/Media_Source_Extensions_API | MDN (dokümantasyon) | 1 |
| 2 | https://developer.mozilla.org/en-US/docs/Web/API/WebRTC_API | MDN (dokümantasyon) | 2 |
| 3 | https://datatracker.ietf.org/doc/html/rfc8216 | IETF RFC (standart) | 3 |
| 4 | https://github.com/ietf-wg-cellar/flac-specification | GitHub (standart repo) | 4 |
| 5 | https://caniuse.com/mediasource | caniuse (tarayıcı desteği) | 5 |
| 6 | https://github.com/video-dev/hls.js | GitHub (uygulama) | 6 |
| 7 | https://ceeblue.net/latency-benchmarks/ | Bağımsız ölçüm (benchmark) | 7 |
| 8 | https://hostcodelab.com/shoutcast-vs-icecast-streaming-server | Teknoloji karşılaştırması | 8 |
| 9 | https://icecast.org/ | Resmi ürün sitesi | 9 |
| 10 | https://github.com/Dash-Industry-Forum/dash.js | GitHub (uygulama) | 10 |

## §2 — Cross-Check Eşlemeleri (ana iddia × ≥2 kaynak)

| Ana iddia | Kaynak A | Kaynak B | Çapraz sonucu |
|---|---|---|---|
| Tarayıcıda akış tamponlaması MSE'ye dayanır | MDN MSE (URL 1) | caniuse mediasource (URL 5) | ✅ Uyumlu — API MDN'de tanımlı, desteği caniuse'de ölçülü |
| HLS bir akış protokolü adayıdır | RFC 8216 (URL 3) | hls.js (URL 6) | ✅ Uyumlu — standart + pratik istemci aynı düzeni doğrular |
| DASH bir akış protokolü adayıdır | dash.js (URL 10) | MDN MSE (URL 1) | ✅ Uyumlu — DASH istemcisi MSE üstünde çalışır |
| ICY/Icecast radyo akışının taşıma düzenidir | hostcodelab (URL 8) | icecast.org (URL 9) | ✅ Uyumlu — karşılaştırma + resmi sunucu aynı ekosistemi doğrular |
| Canlı akışta gecikme/rebuffering gerçek bir sorundur | ceeblue latency-benchmarks (URL 7) | MDN WebRTC (URL 2) | ✅ Uyumlu — düşük gecikme ihtiyacı WebRTC tarafını da besler |
| FLAC akışta taşınabilir bir codec'tir | ietf-wg-cellar/flac-specification (URL 4) | icecast.org (URL 9) | ✅ Uyumlu — RFC + sunucu FLAC desteğini birlikte doğrular |

## §3 — Ölü Yollar (kullanma)

| Ölü URL | Durum | Not |
|---|---|---|
| https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Codecs | ❌ 404 | Eski MDN yolu — **kullanılmayacak** |
| https://developer.mozilla.org/en-US/docs/Web/Media/Guides/Formats/Codecs | ❌ 404 | Doğrulanamadı — **kullanılmayacak** |

> Codec listesi için bu araştırmada codec iddiası **bilinçli olarak
> uydurulmadı**: media-master §12'de codec seti **UNKNOWN'tur** (§4/2).

## §4 — Açık Konular (araştırma sonuçları)

| # | Konu | Araştırma bulgusu | Durum |
|---|---|---|---|
| 1 | Akış protokolü seçimi | 3 canlı aday doğrulandı: HLS (RFC 8216), DASH (dash.js), ICY/Icecast; 4. aday WebRTC (düşük gecikme) | ⚠️ VERIFICATION REQUIRED — **seçim yapılmadı**, media-master §12/4'te UNKNOWN |
| 2 | Codec seti | Bu araştırmada doğrulanabilir RFC: FLAC (RFC 9639 repo); **ancak CoreMusic'in kullanacağı set media-master §12'de UNKNOWN** | ⚠️ VERIFICATION REQUIRED |
| 3 | MSE evrensellik | caniuse: MSE desteği yaygın ama tam değil — tasarım MSE'siz tarayıcı için de düşünülmeli | ⚠️ TASARIM İDDİASI |
| 4 | `buffering` vs `rebuffering` | Literatür rebuffering'i ayrı bir olay (tampon tükemesi) olarak işler (URL 7 pratik zemin); §12/4 ile §14/2 çelişkisi sürer | ⚠️ AÇIK SORU |
| 5 | Protokol seçiminin sahibi | media-master §14/2 zinciri "K014-network (taşıma)" der; protokol **seçimi** K014'te mi K015'te mi belirsiz | ⚠️ AÇIK SORU |

## §5 — Boş / Erişilemeyen Kaynaklar

| Beklenen kaynak | Erişim | Not |
|---|---|---|
| W3C resmi MSE spesifikasyonu (w3c.github.io/MSE) | Bu turda **aranmadı** (kural: uydurma yok) | ⚠️ VERIFICATION REQUIRED — gelecek yenilemede doğrulanabilir |
| WHATWG Living Standard'da akış bölümü | Bu turda doğrulanmadı | ⚠️ VERIFICATION REQUIRED |

## §6 — Yenileme Kuralı

- Bu dosyanın URL doğrulamaları **2026-10-16** tarihlidir; **90 gün** sonra
  yeniden doğrulanır (önce §1 tablosu, sonra §3 ölü yollar).
- Yeni iddia eklenirse **en az 1 yeni canlı URL** ile kanıtlanır; doğrulanamayan
  satır `⚠️ VERIFICATION REQUIRED` olarak kalır.
- **ASLA URL / API / sürüm uydurulmaz** (ZERO-HALLUCINATION).

## §7 — Revizyon

| Tarih | Revizyon | Authority |
|---|---|---|
| 2026-10-16 | İlk web araştırması — 10 canlı URL, 6 cross-check; protokol/codec UNKNOWN korundu | Bayram Ali / Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
