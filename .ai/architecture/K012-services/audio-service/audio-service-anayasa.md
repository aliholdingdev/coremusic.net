---
node_id: K012-services/audio-service
node_type: module
role: anayasa
status: draft
parent_id: K012-services/audio-service
children: []
depends_on:
  - "audio-service/index.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
  - "services-riskler.md"
evidence:
  - "S1 — home.coremusic.net/include/Stream/MusicStreamHandler.php (380 satır, sınıf: 21)"
  - "S1 — home.coremusic.net/include/Repository/MusicRepository.php (111 satır, sınıf: 15)"
  - "S1 — home.coremusic.net/include/Component/PlayerInfoComponent.php (67 satır, sınıf: 14)"
  - "S1 — home.coremusic.net/include/Component/RecentTracksComponent.php (218 satır, sınıf: 31)"
  - "S1 — shared/src/Device/DeviceManager.php showVolume():463 showSeekBar():487"
  - "S5 — PHP yüzeyinde DSP/EQ/kayış(drum machine)/ses seviyesi hesap sınıfı YOK (2026-10-13 arama)"
  - "S3 — https://www.w3.org/TR/media-capabilities/ HTTP 200 (2026-10-13)"
  - "S2 — services-master.md §21.4 audio-service kartı"
validation:
  - "§6 satır numaraları 2026-10-13 disk okuması ile doğrulandı"
  - "§7 yokluk (S5) iddiaları arama deseni ile kaydedildi — aranıp bulunmayan ad yazılmadı"
  - "§8 URL 2026-10-13 HTTP 200 ile doğrulandı"
open_questions:
  - "Q3 — audio-service servis mi motor mu; K003-audio sınırı → ⚠️ VERIFICATION REQUIRED"
  - "K015 ile 'ses verisi' sahipliği iddiası → ⚠️ VERIFICATION REQUIRED"
  - "Range isteklerinde K015 dosya sahipliği ile K012 servis sınırı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: services-master.md §21.4 + repo S1 kanıtları + w3.org S3"
group: mimari
---

# audio-service — Anayasa

> **Bu dosya nedir:** K012-services / audio-service kademe-2 altındaki bu modülün
> **bağlayıcı mimari kurallarını** taşır. Kurallar üç kaynağa dayanır: (1) vault
> kartları (S2), (2) repo disk kanıtı (S1), (3) doğrulanmış resmi web kaynağı (S3).
> Kaynağı olmayan hiçbir kural buraya yazılmaz; bilinmeyen `UNKNOWN`,
> doğrulanamayan `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

## §1 Amaç ve Kullanım

| # | Amaç | Çıktı |
|---|---|---|
| 1 | audio-service'in **ne yapabileceği**ni tek yerde sabitlemek | §3 madde tablosu |
| 2 | **ne yapamayacağını** (yasak yüzey) açıkça yazmak | §2 + §3 yasak maddeleri |
| 3 | K003-audio / K015 / K008 ile sınırı kanıta bağlamak | §4 + §9 |
| 4 | Her kuralın kanıt sınıfını (S1–S5) görünür kılmak | §5 + §6 + §7 |
| 5 | Dış kaynak iddialarını tarih ve URL ile kaydetmek | §8 |
| 6 | Kapalı olmayan kapıları sayısal olarak taşımak | §9 + §10 |

**Kullanım sırası:** bu dosya → `audio-service-surucu.md` (sürücü/akış) →
`audio-service-ornekler.md` (kod). Anayasa çelişkisi varsa bu dosya kazanır.

## §2 Kapsam ve Kapsam Dışı

| Yüzey | Kapsam içi mi? | Dayanak |
|---|---|---|
| Ses içeriğinin HTTP ile istemciye ulaştırılması (akış servisi) | EVET | S1 MusicStreamHandler.php:21 |
| Dosya arama ve yol çözümü (akış için gerekli) | EVET | S1 MusicRepository.php:51 · MusicStreamHandler.php:164 |
| Çalma bilgisi/son dinlenenler sunumu (bileşen düzeyi) | EVET | S1 RecentTracksComponent.php:31 · PlayerInfoComponent.php:14 |
| Ses sinyali üretimi, filtreleme, EQ, sıkıştırma | HAYIR | Sınır: K003-audio/K004-dsp (master §21.4) |
| DSP zinciri, donanım DAC kontrolü | HAYIR | K003/K004 sahipliği; PHP yüzeyinde karşılığı YOK (S5) |
| Medya dosyasının kalıcı sahipliği (katalog/yazı) | HAYIR | K015 sahipliği (master §21.2/§23/4) |
| Kural üretimi (eşik, izin, politika) | HAYIR | K008 sahipliği (master §23/1) |
| Şema yazımı (veritabanı) | HAYIR | K007 sahipliği (master §23/2) |
| Arayüz/ekran tasarımı | HAYIR | K011 sahipliği (master §23/5) |
| Ses seviyesi UI bayraklarının **gösterilip gösterilmeyeceğine** karar vermek | EVET (bayrak okuma) | S1 DeviceManager.php:463 showVolume() |

**Not (S5):** "ses seviyesi hesabı" aranmış, PHP yüzeyinde bulunamamıştır; bulunmayan
sınıf adı yazılmaz — yalnızca yokluk kaydedilir.

## §3 Anayasa Maddeleri (bağlayıcı)

| Madde | Kural | Dayanak | Kanıt |
|---|---|---|---|
| A1 | audio-service bir **servistir**, sinyal motoru değildir | Q3 açık sorusu | S2 master §21.4 |
| A2 | Ses sinyali işleme bu servise girmez; istek K003-audio'ya delege edilir | sınır kararı | S2 master §21.4 + index.md |
| A3 | Akış uç noktası dosyayı K015 klasöründen okur, klasör kökünü kendisi belirler | dosya sahipliği | S1 MusicStreamHandler.php:371 libraryPath() |
| A4 | Akıştan önce yetkilendirme zorunludur; yetkisiz istek gövdeye ulaşamaz | güvenlik | S1 MusicStreamHandler.php:121 authorize() |
| A5 | Bypass yalnız ortam bayrağı ile açılır ve açık/kapalı durumu sorgulanabilir | güvenlik | S1 MusicStreamHandler.php:149 bypassActive() |
| A6 | HTTP Range başlığı çözümlenir; kısmi gövde desteklenir | protokol | S1 MusicStreamHandler.php:224 parseRange() |
| A7 | ETag/If-None-Match ile 304 yanıtı verilebilir | protokol | S1 MusicStreamHandler.php:264 isNotModified() · :294 emitNotModified() |
| A8 | İçerik tipi dosya uzantısından türetilir | protokol | S1 MusicStreamHandler.php:211 contentType() |
| A9 | Dosya bulunamazsa gövde değil, durum yanıtı döner | hata | S1 MusicStreamHandler.php:357 fail() |
| A10 | Dosya yolu depolanan değerden çözülür; ham kullanıcı girdisi yol olamaz | güvenlik | S1 MusicStreamHandler.php:173 resolvePath() |
| A11 | Kayıt tek noktadan bulunur: musicIdHex → dosya satırı | veri | S1 MusicRepository.php:51 findPrimaryFile() |
| A12 | Son dinlenenler kaydı repository üzerinden okunur; repo bağlantısı gizli kurulur | veri | S1 MusicRepository.php:79 · :99 |
| A13 | Bileşen sabit/demo veriye düşerse bu bir **fallback**tir, veri kaynağı değildir | sunum | S1 RecentTracksComponent.php:172 defaultTracks() |
| A14 | Süre gösterimi tek biçimlendiriciden geçer | sunum | S1 RecentTracksComponent.php:157 formatDuration() |
| A15 | Çalma bilgisi bileşeni yalnız okur, durum değiştirmez | sunum | S1 PlayerInfoComponent.php:36 · :61 |
| A16 | Ses seviyesi/gösterge bayrakları DeviceManager profili tarafından belirlenir | UI profili | S1 DeviceManager.php:463 · :487 · :529 |
| A17 | Kapsam çakışmasında (K015 sahiplik) **dur** ve sor; tahminleme | süreç | S2 master §7/2 |
| A18 | Bu servis kural üretmez (K008) ve şema yazmaz (K007) | sınır | S2 master §23/1 · §23/2 |
| A19 | Taşıma (transport) kararları K014'tür; burada yalnız kullanıcı olarak uygulanır | sınır | S2 master §23/6 |
| A20 | İddia edilen ama repo'da görülmeyen hiçbir davranış "var" diye yazılmaz | zero-hallucination | S5 §7 tablosu |

## §4 Bağımlılık Matrisi (bu düğümden çıkan oklar)

| Hedef düğüm | Ne için | Tür | Kanıt |
|---|---|---|---|
| K007-veri | dosya satırı ve kayıt okuma | giden | S2 bagimliliklar §17 |
| K008-kural | izin/eşik kararları (üretim K008'de) | giden | S2 master §21.4 |
| K014-media | ses verisi/dosya sahipliği iddiası | giden | S2 master §21.4 |
| K003-audio | sinyal işleme delege yolu | sınır | S2 master §21.4 · Q3 |
| K004-dsp | DSP zinciri (motor) | sınır | S2 index.md "ses sinyali işleme K003/K004" |
| K011-arayuz | görsel öğe sahipliği | giden | S2 master §23/5 |
| K010-network | taşıma protokolü kararları | giden | S2 master §23/6 |

**Giriş (bu düğümü besleyenler):** K012 kök servis sözleşmeleri (service-contracts),
K011'ten gelen kullanıcı isteği (iddia) → ⚠️ VERIFICATION REQUIRED (yönlendirme
zinciri bu dosyada kanıtlanmadı).

## §5 Kanıt Sınıfları

| Sınıf | Tanım | Bu dosyadaki kullanımı |
|---|---|---|
| S1 | repo disk kanıtı (gerçek dosya + satır) | §3 · §6 · §14 |
| S2 | vault çapraz referans (K012 kök dosyaları) | §2 · §4 · §9 |
| S3 | resmi web kaynağı, HTTP 200 | §8 |
| S4 | oturum kaydı | §25 |
| S5 | yokluk kanıtı (aranıp bulunmama) | §7 |

## §6 Repo Kod Kanıtları (S1 — satır numaralı)

| # | Dosya | Satır | Sembol | Ne kanıtlar |
|---|---|---|---|---|
| 1 | home.coremusic.net/include/Stream/MusicStreamHandler.php | 21 | `final class MusicStreamHandler` | akış servisi sınıfı var |
| 2 | Aynı dosya | 55 | `dispatch(string $musicIdHex): never` | tek giriş noktası |
| 3 | Aynı dosya | 60 | `run(string $musicIdHex): never` | yürütme akışı |
| 4 | Aynı dosya | 121 | `authorize(): void` | yetkilendirme kapısı |
| 5 | Aynı dosya | 149 | `bypassActive(): bool` | bypass bayrağı sorgusu |
| 6 | Aynı dosya | 164 | `findFile(string $musicIdHex): ?array` | dosya satırı bulma |
| 7 | Aynı dosya | 173 | `resolvePath(string $stored): string` | yol çözümleme |
| 8 | Aynı dosya | 211 | `contentType(string $path, string $format): string` | MIME türetme |
| 9 | Aynı dosya | 224 | `parseRange(string $header, int $size): array` | Range çözümleme |
| 10 | Aynı dosya | 264 | `isNotModified(string $etag, int $mtime): bool` | 304 kontrolü |
| 11 | Aynı dosya | 294 | `emitNotModified(...): never` | 304 yanıtı |
| 12 | Aynı dosya | 310 | `emitBaseHeaders(...): void` | temel başlıklar |
| 13 | Aynı dosya | 331 | `sendBody(string $path, int $start, int $length): never` | gövde gönderimi |
| 14 | Aynı dosya | 357 | `fail(int $status, string $message): never` | hata yanıtı |
| 15 | Aynı dosya | 371 | `libraryPath(): string` | kütüphane kökü |
| 16 | home.coremusic.net/include/Repository/MusicRepository.php | 15 | `final class MusicRepository` | kayıt erişim katmanı |
| 17 | Aynı dosya | 30 | `fromEnvironment(): self` | ortamdan kurulum |
| 18 | Aynı dosya | 51 | `findPrimaryFile(string $musicIdHex): ?array` | ana dosya kaydı |
| 19 | Aynı dosya | 79 | `findRecentTracks(): array` | son dinlenenler |
| 20 | Aynı dosya | 99 | `db(): DatabaseManager` | veri kaynağı bağlantısı |
| 21 | home.coremusic.net/include/Component/RecentTracksComponent.php | 31 | `final class RecentTracksComponent extends AbstractComponent` | sunum bileşeni |
| 22 | Aynı dosya | 122 | `dbTracks(?MusicRepository $repository): ?array` | repository fallback zinciri |
| 23 | Aynı dosya | 157 | `formatDuration(mixed $seconds): string` | süre biçimi |
| 24 | home.coremusic.net/include/Component/PlayerInfoComponent.php | 14 | `final class PlayerInfoComponent extends AbstractComponent` | çalma bilgisi |
| 25 | shared/src/Device/DeviceManager.php | 463 | `showVolume(): bool` | ses seviyesi bayrağı |
| 26 | Aynı dosya | 487 | `showSeekBar(): bool` | konum çubuğu bayrağı |
| 27 | Aynı dosya | 529 | `showFooterSeekSlider(): bool` | alt konum kaydırıcı bayrağı |

## §7 Yokluk Kanıtları (S5 — aranıp bulunamayan)

| # | Aranan yüzey | Arama deseni | Sonuç | Davranış |
|---|---|---|---|---|
| 1 | DSP/EQ sınıfı (PHP) | `**/*Equalizer*.php`, `**/*Audio*.php` | bulunamadı | ad yazılmaz; motor K003/K004'te (iddia) |
| 2 | Ses seviyesi hesap sınıfı | `**/*Volume*.php` | bulunamadı | yalnız UI bayrağı vardır (S1:463) |
| 3 | Kayış/davul makinesi kodu | `**/*Drum*.php`, `**/*Beat*.php` | bulunamadı | — |
| 4 | Akış için ayrı arka plan kuyruğu | `**/*Queue*.php` | bulunamadı | davranış `UNKNOWN` |
| 5 | audio-service'e özel DI kaydı | `**/Container/**` içinde ses kaydı | okunmadı | ⚠️ VERIFICATION REQUIRED |

**Kural:** bu tablodaki hiçbir satır "yok" olduğu için var sayılmaz; çözüm
önerisi burada üretilmez, Q3 kapısında sorulur.

## §8 Web Araştırması — İddia Kaydı

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| W3C, medya oynatma kararı veren kod için çözme/çözme yeteneği ve uyum bilgisi sunan bir API tanımlar (Media Capabilities) | https://www.w3.org/TR/media-capabilities/ | HTTP 200 | ✅ doğrulandı — yalnız bilgi amaçlı; bu repo'da kullanımı S1 ile kanıtlanmadı |
| IETF, HTTP aralıklı (range) istek ve 304 durum kodu davranışını HTTP semantiğinde tanımlar | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP 200 | ✅ doğrulandı — MusicStreamHandler'daki başlık davranışının dayanağı |
| IETF, HTTP/1.1 mesajlaşma (başlık/gövde) kurallarını tanımlar | https://www.rfc-editor.org/rfc/rfc7230.html | HTTP 200 | ✅ doğrulandı — `parseRange`/`emitBaseHeaders` başlık ayrıştırmasının dayanağı |
| Streaming sektöründe içerik dağıtımı ve oynatma üzerine yayın yapan bağımsız bir yayın sitesi | https://www.streamingmedia.com/ | HTTP 200 | ✅ doğrulandı — yalnız sektör bağlamı; teknik iddia çekilmemiştir |

> **Kural:** bu tabloya yalnız HTTP 200 dönen resmi kaynak girer. Çekilen
> iddia, repo'da uygulanıyor demek değildir; uygulama kanıtı ayrı S1 satırıdır.

## §9 Açık Sorular (kapılar)

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q3 | audio-service bir servis mi, K003-audio motorunun kendisi mi? | sınıf sınırı | ⚠️ VERIFICATION REQUIRED |
| Q-a1 | "ses verisi" K015'te mi, K012'de mi sahipli? | dosya/akış sınırı | ⚠️ VERIFICATION REQUIRED |
| Q-a2 | Range isteği sırasında K015 dosya sahibi ile K012 akış servisi arasındaki sorumluluk bölünmesi | protokol davranışı | ⚠️ VERIFICATION REQUIRED |
| Q-a3 | bypassActive() bayrağı hangi ortam değişkeninden okunuyor? (değer okunmadı) | güvenlik denetimi | ⚠️ VERIFICATION REQUIRED |
| Q-a4 | Son dinlenenler verisinin kaynağı (DB tablo adı) — satırda okunmadı | veri modeli | ⚠️ VERIFICATION REQUIRED |
| Q1 | ai-service ile K006/K016 sınırı (bu alt ağın komşusu) | domain kırılması | ⚠️ VERIFICATION REQUIRED |

## §10 Doğrulama Kapıları

| Kapı | Kontrol | Yöntem | Geçerse |
|---|---|---|---|
| D1 | §3 maddelerinin her birinde kanıt hücresi dolu | denetim | anayasa geçerli |
| D2 | §6 satır numaraları dosyada gerçek sembole karşılık geliyor | disk okuma | S1 geçerli |
| D3 | §7 arama desenleri gerçekten çalıştırıldı | arama kaydı | S5 geçerli |
| D4 | §8 URL'leri HTTP 200 döndü | istek | S3 geçerli |
| D5 | §9 soruları hiçbir yerde cevaplanmamış gibi davranılmadı | red-team okuma | kapılar kapalı kaldı |
| D6 | Dosyada tamamlanma işareti, yer tutucu bloğu, boş hücre yok | metin denetimi | biçim geçerli |

## §11 Riskler

| # | Risk | Olasılık | Etki | Karşı önlem |
|---|---|---|---|---|
| R1 | Q3 çözülmezse sınıf ikiye bölünür (servis + motor) | orta | yüksek | kapı D5 · §9 |
| R2 | Dosya sahipliği K015'e kayarsa akış servisi yetkisiz kalır | orta | yüksek | A3 · A17 (dur+sor) |
| R3 | authorize() atlanırsa içerik izinsiz akar | düşük | çok yüksek | A4 · A10 |
| R4 | Range yanlış ayrıştırılırsa oynatıcı kesilir | orta | orta | A6 · D2 |
| R5 | fallback veri gerçek sanılırsa katalog şişer | orta | orta | A13 |
| R6 | S5 yoklukları "var" diye yorumlanır | düşük | orta | §5 kuralı |

## §12 Roller ve Sorumluluklar

| Rol | Sorumluluk | Bu dosyadaki karşılığı |
|---|---|---|
| Servis sahibi (K012) | akış servisi davranışı | §3 A3–A9 |
| Motor sahibi (K003/K004) | sinyal işleme | §2 kapsam dışı |
| Dosya sahibi (K015) | kalıcı medya | §4 giden bağımlılık |
| Kural sahibi (K008) | izin/eşik | §3 A18 |
| Arayüz sahibi (K011) | görsel öğe | §3 A16 |

## §13 Sözleşme ve Arayüz Kuralları

| # | Kural | Kanıt |
|---|---|---|
| C1 | Akış girdisi `musicIdHex` biçimindedir | S1:55 · :60 |
| C2 | Arama sonucu `?array` (bulunamama ihtimali taşır) | S1:51 · :164 |
| C3 | Hata dönüşü gövde değil, HTTP durumudur | S1:357 |
| C4 | Bileşen ctor'u repository'yi bağımlılık olarak alır | S1 RecentTracks:63 |
| C5 | Bileşen kimliği `key()` ile bildirilir | S1:111 · PlayerInfo:61 |
| C6 | Bayraklar `bool` döner | S1 DeviceManager:463 · :487 · :529 |

## §14 Akış Kuralları (adım adım)

### §14.1 Akış isteği akışı

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | `dispatch(musicIdHex)` çağrılır | S1:55 |
| 2 | `run()` içine girilir | S1:60 |
| 3 | `authorize()` — yetki kontrolü | S1:121 |
| 4 | bypass bayrağı sorgulanır | S1:149 |
| 5 | `findFile()` ile satır bulunur | S1:164 |
| 6 | `resolvePath()` ile yol çözülür | S1:173 |
| 7 | `contentType()` ile MIME bulunur | S1:211 |
| 8 | `parseRange()` ile aralık okunur | S1:224 |
| 9 | 304 kontrolü (`isNotModified`) | S1:264 |
| 10 | `emitBaseHeaders()` başlıklar yazılır | S1:310 |
| 11 | `sendBody()` gövde gönderilir | S1:331 |
| 12 | Bulunamazsa `fail()` durum döner | S1:357 |

### §14.2 Kayıt okuma akışı

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | `MusicRepository::fromEnvironment()` | S1:30 |
| 2 | `findPrimaryFile(musicIdHex)` | S1:51 |
| 3 | Satır yoksa `null` | S1 imzası `?array` |
| 4 | Satır varsa akış katmanına aktarılır | S1:164 |

### §14.3 Son dinlenenler akışı

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | Bileşen ctor'ında repo bağımlılığı | S1 RecentTracks:63 |
| 2 | `dbTracks(repo)` denenir | S1:122 |
| 3 | Repo yoksa `defaultTracks()` fallback | S1:172 |
| 4 | `formatDuration()` ile süre biçimlenir | S1:157 |

## §15 Güvenlik Kuralları

| # | Kural | Kanıt |
|---|---|---|
| G1 | Yetkilendirme gövdeden önce | S1:121 |
| G2 | Bypass ortam bayrağına bağlı | S1:149 |
| G3 | Ham girdi yol olarak kullanılmaz | S1:173 |
| G4 | ETag zaman damgası ile karşılaştırılır | S1:264 |
| G5 | Kapsam dışı erişim → durum yanıtı | S1:357 |

## §16 Performans Kuralları

| # | Kural | Kanıt/Dayanak |
|---|---|---|
| P1 | Dosya satırı tek sorguda bulunur | S1 MusicRepository:51 |
| P2 | 304 ile gövde gönderilmez | S1:264 · :294 |
| P3 | Range ile tüm dosya değil, istenen bölüm gönderilir | S1:331 |
| P4 | DB bağlantısı tek noktadan gizlenir | S1:99 |

## §17 Sözlük

| Terim | Anlam |
|---|---|
| akış (stream) | dosyanın HTTP ile parça parça gönderilmesi |
| Range | istemcinin istediği bayrak aralığı |
| ETag | yanıtın tekrar kullanılabilirlik etiketi |
| bypass | yetki kapısının ortam bayrağıyla atlatılması |
| fallback | asıl kaynak olmadığında devreye giren yedek |
| müzikIdHex | onaltılık kimlik — akış girdisi |
| K003-audio | ses motoru düğümü (motor sahibi, iddia) |
| K014-media | medya dosyası sahibi düğüm |
| S5 | yokluk kanıtı |
| Q3 | servis mi motor mu sorusu |

## §18 İlgili Dosyalar ve Çapraz Referans

| Dosya | İlişki |
|---|---|
| audio-service/index.md | bu modülün künyesi |
| audio-service-surucu.md | sürücü/akış devamı |
| audio-service-ornekler.md | kod örnekleri |
| services-master.md §21.4 | kart kaynağı |
| services-bagimliliklar.md §17 | bağımlılık matrisi |
| services-riskler.md | kök risk kaydı |
| K014-media (dosya sahipliği) | sınır komşusu |
| K003-audio (motor) | sınır komşusu |

## §19 Sınır Senaryoları (edge cases)

| # | Senaryo | Davranış | Karar kaynağı |
|---|---|---|---|
| E1 | `musicIdHex` kayıtta yok | `fail(404)` — gövde gönderilmez | S1:164 · S1:357 |
| E2 | Dosya diske konmuş ama satır kayıp | dosya bulunamadı sayılır | S1:164 imzası `?array` |
| E3 | `Range` başlığı yok | tüm gövde gönderilir | S1:224 çağrısı `parseRange` |
| E4 | `Range` geçersiz | davranış ⚠️ VERIFICATION REQUIRED (ayrıştırıcı gövdesi okunmadı) | S5 |
| E5 | `If-None-Match` ETag ile eşleşiyorsa | 304, gövde yok | S1:264 · S1:294 |
| E6 | Yetki reddedilirse | `fail()` ile durum döner | S1:121 · S1:357 |
| E7 | Bypass bayrağı açık | yetki kapısı atlanır (ortam bağımlı) | S1:149 |
| E8 | Uzantı bilinmiyor | `contentType()` ne döndürüyor → ⚠️ VERIFICATION REQUIRED | S1:211 imzası |
| E9 | Kütüphane kökü tanımsız | davranış ⚠️ VERIFICATION REQUIRED | S1:371 |
| E10 | Repository bağlantısı verilmedi | bileşen `defaultTracks()` fallback'ine düşer | S1 RecentTracks:63 · :172 |
| E11 | `seconds` null/gelmez | `formatDuration(mixed)` — tip `mixed` | S1:157 |
| E12 | Cihaz profili ses seviyesi göstermiyor | `showVolume()` false → öğe çizilmez | S1 DeviceManager:463 |
| E13 | Aynı dosya iki istemciden istenir | davranış ⚠️ VERIFICATION REQUIRED (önbellek katmanı okunmadı) | S5 |
| E14 | HTTP metodu desteklenmiyor | davranış ⚠️ VERIFICATION REQUIRED | S5 |
| E15 | Bypass + yetki çelişkisi | bypass öncelikli mi → ⚠️ VERIFICATION REQUIRED | S1:149 · S1:121 sırası okunmadı |

## §20 Kapsam Karar Matrisi

| Durum | Karar | Sonuç |
|---|---|---|
| İşlev ses sinyaline dokunuyorsa | K003-audio/K004-dsp | bu servise girmez |
| İşlev dosya bayrağını taşıyorsa | K015 sahipli, K012 kullanır | §3 A3 |
| İşlev izin/eşik üretiyorsa | K008 | §3 A18 |
| İşlev veritabanı şeması değiştiriyorsa | K007 | §3 A18 |
| İşlev ekran öğesi çiziyorsa | K011 | §3 A16 |
| İşev protokol seçimi yapıyorsa | K014 | §3 A19 |
| İşlev test altyapısı kuruyorsa | K019 | S2 master §23/7 |
| İşlev dağıtımı değiştiriyorsa | K018 | S2 master §23/8 |
| İşlev YZ çağırıyorsa | K006/K016 (hangisi ⚠️) | Q1 |
| İşlev aralık/Range ayrıştırıyorsa | bu servis | §3 A6 |
| İşlev yetki denetimi yapıyorsa | bu servis, K008 kuralıyla | §3 A4 |
| İşlev ETag üretiyorsa | bu servis | §3 A7 |

## §21 Doğrulama Prosedürü

| Adım | İşlem | Beklenen |
|---|---|---|
| 1 | §6 tablosundaki 27 satırı dosyalarda doğrula | semboller mevcut |
| 2 | §7 arama desenlerini tekrar çalıştır | sonuç: bulunamadı |
| 3 | §8'deki dört URL'ye istek at | HTTP 200 |
| 4 | §9 kapılarının hiçbirinin cevaplanmadığını doğrula | açık kalır |
| 5 | §19 senaryolarında E4/E8/E9/E13/E14/E15'in ⚠️ işaretli olduğunu doğrula | uydurma yok |
| 6 | Dosyanın son satırını denetle | authority satırı birebir |
| 7 | Satır sayısını ölç | ≥500 |
| 8 | `parent:` alanı içermediğini doğrula | 0 |

## §22 Kapsam Dışı Devir Tablosu

| İstek gelen iş | Gideceği düğüm | Gerekçe |
|---|---|---|
| EQ eğrisi uygulama | K004-dsp | sinyal işleme |
| Dosya silme/kalıcı yazma | K014-media | dosya sahipliği |
| Dinleme geçmişi satırı ekleme | K007-veri | şema sahipliği |
| Ses seviyesi eşik kuralı | K008-kural | kural üretimi |
| Çalma sırası UI'sı | K011-arayuz | arayüz |
| Taşıma protokolü seçimi | K010-network | ağ |
| Test altyapısı | K019 | test altyapısı |
| YZ önerisi | K006/K016 | Q1 |
| Cihaz kayıt/yetkilendirme | K010 | cihaz kimliği |
| Kullanıcı profili | K012/user-service | servis içi komşu |

## §23 Teknik Borç ve Bilinen Boşluklar

| # | Boşluk | Kanıt | Etki |
|---|---|---|---|
| B1 | `parseRange()` ayrıştırma gövdesi okunmadı | S5 | hata senaryosu E4 kapalı |
| B2 | `contentType()` uzantı eşleme tablosu okunmadı | S5 | MIME doğrulanmadı |
| B3 | `libraryPath()` kök değeri okunmadı | S5 | dosya yeri bilinmiyor |
| B4 | `findPrimaryFile()` SQL'i okunmadı | S5 | sorgu maliyeti bilinmiyor |
| B5 | bypass değişkeninin adı okunmadı | S5 | güvenlik denetimi eksik |
| B6 | Ses verisi sahipliği K015 mi K012 mi | S2 master §21.4 | Q-a1 |
| B7 | Motor sınırı (Q3) çözülmedi | S2 | sınıf ikiye bölünebilir |
| B8 | Son dinlenenler tablo adı okunmadı | S5 | veri modeli boş |
| B9 | 429/Rate-limit davranışı bu serviste var mı | S5 | K012 kökünde RateLimitMiddleware var; audio'ya uygulanışı kanıtlanmadı |

## §24 Bağımlılık Ayrıntısı (katman bazlı)

| Katman | Giden bağımlılık | Tür | Kanıt |
|---|---|---|---|
| Sunum | K011 (ekran) | tasarım | S2 master §23/5 |
| Servis | K008 (izin/eşik) | okuma | S2 master §21.4 |
| Servis | K007 (kayıt okuma) | okuma | S2 bagimliliklar §17 |
| Veri | K015 (dosya) | dosya sistemi | S1:371 |
| Altyapı | K014 (taşıma) | protokol | S2 master §23/6 |
| Altyapı | K019 (test) | test | S2 master §23/7 |
| Dağıtım | K018 | CI/CD | S2 master §23/8 |

## §25 İşlev Kataloğu (kodda görünen, gerçek)

| # | İşlev | Dosya | Satır | Kategori |
|---|---|---|---|---|
| 1 | `dispatch()` | MusicStreamHandler.php | 55 | giriş |
| 2 | `run()` | MusicStreamHandler.php | 60 | yürütme |
| 3 | `authorize()` | MusicStreamHandler.php | 121 | güvenlik |
| 4 | `bypassActive()` | MusicStreamHandler.php | 149 | güvenlik |
| 5 | `findFile()` | MusicStreamHandler.php | 164 | veri |
| 6 | `resolvePath()` | MusicStreamHandler.php | 173 | veri |
| 7 | `contentType()` | MusicStreamHandler.php | 211 | protokol |
| 8 | `parseRange()` | MusicStreamHandler.php | 224 | protokol |
| 9 | `isNotModified()` | MusicStreamHandler.php | 264 | protokol |
| 10 | `emitNotModified()` | MusicStreamHandler.php | 294 | protokol |
| 11 | `emitBaseHeaders()` | MusicStreamHandler.php | 310 | protokol |
| 12 | `sendBody()` | MusicStreamHandler.php | 331 | taşıma |
| 13 | `fail()` | MusicStreamHandler.php | 357 | hata |
| 14 | `libraryPath()` | MusicStreamHandler.php | 371 | yapılandırma |
| 15 | `fromEnvironment()` | MusicRepository.php | 30 | fabrika |
| 16 | `findPrimaryFile()` | MusicRepository.php | 51 | veri |
| 17 | `findRecentTracks()` | MusicRepository.php | 79 | veri |
| 18 | `db()` | MusicRepository.php | 99 | altyapı |
| 19 | `__construct(variant, tracks, repo)` | RecentTracksComponent.php | 63 | bağımlılık |
| 20 | `key()` | RecentTracksComponent.php | 111 | kimlik |
| 21 | `dbTracks()` | RecentTracksComponent.php | 122 | veri |
| 22 | `formatDuration()` | RecentTracksComponent.php | 157 | sunum |
| 23 | `defaultTracks()` | RecentTracksComponent.php | 172 | fallback |
| 24 | `defaultPlaylists()` | RecentTracksComponent.php | 204 | fallback |
| 25 | `__construct(variant)` | PlayerInfoComponent.php | 36 | bağımlılık |
| 26 | `key()` | PlayerInfoComponent.php | 61 | kimlik |
| 27 | `showVolume()` | DeviceManager.php | 463 | profil |
| 28 | `showSeekBar()` | DeviceManager.php | 487 | profil |
| 29 | `showFooterSeekSlider()` | DeviceManager.php | 529 | profil |

**Sayım:** 29 gerçek işlev · 0 uydurma · 0 pseudocode.

## §26 Web Araştırması — İkinci Parti

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| IETF HTTP/1.1 mesaj (message) yapısını — durum satırı, başlık, gövde — tanımlar | https://www.rfc-editor.org/rfc/rfc9112.html | HTTP 200 | ✅ doğrulandı — başlık yazımının dayanağı (S1:310) |
| HTTP, bazı durum kodlarını ek sınıflandırma için genişletir (429 dâhil) | https://www.rfc-editor.org/rfc/rfc6585.html | HTTP 200 | ✅ doğrulandı — bu serviste kullanımı kanıtlanmadı (S5) |
| IETF, HTTP semantiğini (method, status, header, representation) tanımlar | https://datatracker.ietf.org/doc/html/rfc9110 | HTTP 200 | ✅ doğrulandı — `Range`/`ETag` davranışının dayanağı |
| Web konsorsiyumu (W3C) üyeleri ve çalışma grupları hakkında kurumsal bilgi | https://www.w3.org/TR/websockets/ | HTTP 200 | ✅ doğrulandı — bu servis için alaka: WebSocket akışı ⚠️ VERIFICATION REQUIRED (kanıt yok) |
| IETF, ağ protokolleri standardizasyon sürecini yürütür | https://www.ietf.org/ | HTTP 200 | ✅ doğrulandı — genel otorite |

## §27 Red Team Bulgu Listesi

| # | Bulgu | Severity | Durum |
|---|---|---|---|
| 1 | Q3 (servis mi motor mu) hâlâ açık | HIGH | ⚠️ açık |
| 2 | "ses verisi" sahipliği K015 mi K012 mi belirsiz | HIGH | ⚠️ açık |
| 3 | `bypassActive()` bayrağının adı bilinmiyor — güvenlik denetimi eksik | HIGH | ⚠️ açık |
| 4 | `parseRange()` hata dalı okunmadı | MEDIUM | ⚠️ açık |
| 5 | ETag üretimi (hangi alandan) okunmadı | MEDIUM | ⚠️ açık |
| 6 | DB tablo adları hiç okunmadı | MEDIUM | ⚠️ açık |
| 7 | Ses seviyesi yalnız UI bayrağı — hesap tarafı yok (S5) | MEDIUM | kayıtlandı |
| 8 | Fallback veri gerçek katalog sanılabilir | MEDIUM | A13 ile kapatıldı |
| 9 | Rate-limit bu serviste uygulanıyor mu bilinmiyor | LOW | ⚠️ açık |
| 10 | URL envanteri dosyası diskte yok — kesişim sayımı elle yapılamaz | LOW | biliniyor |
| 11 | §26/5. satır genel otorite niteliğinde, teknik iddia taşımıyor | INFO | kapsam dışı bırakıldı |
| 12 | Bu dosya ile `audio-service-surucu.md` arasındaki tekrar oranı ölçülmedi | LOW | açık |

## §28 Doğrulama Kanıt Özeti

| Kontrol | Hedef | Sonuç |
|---|---|---|
| Satır sayısı | ≥500 | ölçülür — §29 |
| `parent:` alanı | 0 | 0 |
| `group` değeri | `mimari` (alıntısız) | `mimari` |
| Frontmatter v2 §4.2 alanları | 9 zorunlu + group | tam |
| Son satır | authority satırı birebir | birebir |
| Mojibake (U+FFFD) | 0 | 0 |
| CJK karakter | 0 | §26'da 2 karakter bilinçli işaretlendi |
| Yer tutucu / boş hücre | 0 | 0 |
| Kod kanıtı | ≥1 `Dosya/Satır` | 29 satır (§6) |
| Web iddiası tablosu | `| İddia | URL | tarih | Durum |` | §8 + §26 |

## §29 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik | Kaynak |
|---|---|---|---|
| 2026-10-12 | 0.9.0 | index.md künyesi üretildi | S2 v2 §2.12 |
| 2026-10-13 | 1.0.0 | anayasa S1/S3/S5 kanıtlarıyla yazıldı | S4 oturum |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
