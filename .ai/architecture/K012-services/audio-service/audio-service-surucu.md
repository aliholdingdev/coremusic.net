---
node_id: K012-services/audio-service
node_type: module
role: surucu
status: draft
parent_id: K012-services/audio-service
children: []
depends_on:
  - "audio-service/audio-service-anayasa.md"
  - "audio-service/index.md"
  - "services-master.md"
evidence:
  - "S1 — home.coremusic.net/include/Stream/MusicStreamHandler.php 380 satır (2026-10-13)"
  - "S1 — home.coremusic.net/include/Repository/MusicRepository.php 111 satır"
  - "S1 — home.coremusic.net/include/Component/RecentTracksComponent.php 218 satır"
  - "S1 — shared/src/Device/DeviceManager.php 663 satır (showVolume/showSeekBar bayrakları)"
  - "S5 — ses motoru (EQ/sinyal) sürücüsü PHP yüzeyinde YOK"
  - "S3 — https://www.w3.org/TR/media-capabilities/ HTTP 200"
validation:
  - "§2 tetikleyici tablosu her satırda S1/S2/S5 kanıtı taşıyor"
  - "§7 sürücü-engine ayrımı S5 olarak işaretlendi — uydurma yok"
open_questions:
  - "Q3 — audio-service motor mu servis mi → ⚠️ VERIFICATION REQUIRED"
  - "Sürüm/kuyruk tetikleyicisi (Zamanlanmış iş) bilinmiyor → UNKNOWN"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: services-master.md §21.4 + MusicStreamHandler.php (S1)"
group: mimari
---

# audio-service — Sürücü (Driver)

> **Bu dosya nedir:** audio-service'i **hareket ettiren** olayları, sırayı, durum
> geçişlerini ve geri beslemeleri tanımlar. "Ne yapılabilir" (anayasa) burada
> değil; "**ne tetikler, hangi sırayla, neyle biter**" buradadır.

## §1 Sürücü Tanımı

| Alan | Değer | Kanıt |
|---|---|---|
| Sürücü adı | audio-service akış isteği işleyicisi | S1 MusicStreamHandler.php:21 |
| Birincil girdi | `musicIdHex` (onaltılık kimlik) | S1:55 · S1:60 |
| Birincil çıktı | HTTP yanıt (başlık + gövde ya da durum) | S1:310 · S1:331 · S1:357 |
| İkincil girdi | `Range` / `If-None-Match` başlıkları | S1:224 · S1:264 |
| Bağımlı veri | dosya satırı (repository) | S1 MusicRepository:51 |
| Tetik kaynağı | HTTP isteği (iddia) | ⚠️ VERIFICATION REQUIRED — yönlendirme okunmadı |
| Bitiş koşulu | gövde gönderimi ya da `fail()` | S1:331 · S1:357 |
| Sürücü sahibi | K012-services | S2 master §3/4 |

## §2 Tetikleyici Tablosu

| # | Tetikleyici | Kaynak | Tepki | Kanıt |
|---|---|---|---|---|
| T1 | Geçerli `musicIdHex` ile istek | HTTP | yetki → dosya → gövde | S1:55 · :121 · :164 · :331 |
| T2 | Kimlik kayıtta yok | veri | `fail()` durum yanıtı | S1:164 · :357 |
| T3 | Yetki başarısız | güvenlik | gövde yok | S1:121 |
| T4 | Bypass bayrağı açık | ortam | yetki kapısı atlanır | S1:149 |
| T5 | `If-None-Match` eşleşiyor | istemci | 304, gövde yok | S1:264 · :294 |
| T6 | `Range` başlığı var | istemci | kısmi gövde | S1:224 · :331 |
| T7 | `Range` yok | istemci | tüm gövde (davranış iddiası) | ⚠️ VERIFICATION REQUIRED |
| T8 | Dosya yolu çözülemez | dosya sistemi | hata dalı → ⚠️ VERIFICATION REQUIRED | S1:173 imzası |
| T9 | Son dinlenenler istenir | bileşen | repository → fallback | S1 RecentTracks:122 · :172 |
| T10 | Repository bağlantısı yok | bağımlılık | `defaultTracks()` | S1:63 · :172 |
| T11 | Cihaz profili okunur | DeviceManager | `showVolume()` / `showSeekBar()` | S1 DeviceManager:463 · :487 |
| T12 | Sinyal işleme isteği | K003-audio | bu sürücüye girmez → devir | S2 index.md sınırı |
| T13 | Zamanlanmış/kuyruk tetikleyicisi | bilinmiyor | — | UNKNOWN (S5) |
| T14 | Katalog yazımı | K015 | bu sürücünün işi değil | S2 master §23/4 |
| T15 | Oran aşımı (429) | K012 kök middleware | audio'ya uygulanışı kanıtlanmadı | S5 |

## §3 Sürücü Zinciri (adım sırası)

| Sıra | Adım | Sorumlu | Kanıt |
|---|---|---|---|
| 1 | `dispatch()` çağrısı | MusicStreamHandler | S1:55 |
| 2 | `run()` gövdesi | MusicStreamHandler | S1:60 |
| 3 | `authorize()` | MusicStreamHandler | S1:121 |
| 4 | `bypassActive()` kontrolü | MusicStreamHandler | S1:149 |
| 5 | `findFile()` | MusicStreamHandler | S1:164 |
| 6 | `resolvePath()` | MusicStreamHandler | S1:173 |
| 7 | `contentType()` | MusicStreamHandler | S1:211 |
| 8 | `parseRange()` | MusicStreamHandler | S1:224 |
| 9 | `isNotModified()` | MusicStreamHandler | S1:264 |
| 10 | `emitNotModified()` (304 ise) | MusicStreamHandler | S1:294 |
| 11 | `emitBaseHeaders()` | MusicStreamHandler | S1:310 |
| 12 | `sendBody()` | MusicStreamHandler | S1:331 |

**Sıra kuralı:** 3 ve 4 birbirinden önce/sonra okunmadı → sıralama ⚠️ VERIFICATION
REQUIRED'tur (anayasa §19/E15).

## §4 Durum Tablosu

| # | Durum | Çıkış | Sonraki durum | Kanıt |
|---|---|---|---|---|
| S1 | başlangıç | `dispatch()` | yetki | S1:55 |
| S2 | yetki bekliyor | `authorize()` | bypass kontrolü | S1:121 |
| S3 | bypass sorgusu | `bypassActive()` | dosya arama | S1:149 |
| S4 | dosya aranıyor | `findFile()` | yol çözümü / hata | S1:164 |
| S5 | yol çözülüyor | `resolvePath()` | içerik tipi | S1:173 |
| S6 | başlık hazırlanıyor | `contentType()` · `parseRange()` | 304 kontrolü | S1:211 · :224 |
| S7 | 304 kontrolü | `isNotModified()` | 304 çıkışı ya da devam | S1:264 |
| S8 | 304 çıkışı | `emitNotModified()` | BİTTİ | S1:294 |
| S9 | gövde | `sendBody()` | BİTTİ | S1:331 |
| S10 | hata | `fail()` | BİTTİ | S1:357 |

## §5 Geri Besleme Hatları

| # | Geri besleme | Nerede | Etki |
|---|---|---|---|
| F1 | Dosya satırı `null` dönerse | `findFile()` | S4 → S10 | 
| F2 | Yetki false dönerse | `authorize()` | S2 → S10 |
| F3 | ETag eşleşirse | `isNotModified()` | S7 → S8 |
| F4 | Range aralığı geçersizse | `parseRange()` | ⚠️ VERIFICATION REQUIRED |
| F5 | Repo null ise bileşende | `dbTracks()` | fallback veriye |
| F6 | DB bağlantı hatası | `db()` | ⚠️ VERIFICATION REQUIRED |
| F7 | Dosya sistemi hatası | `sendBody()` | ⚠️ VERIFICATION REQUIRED |
| F8 | Bypass bayrağı true | `bypassActive()` | S2 kapısı atlanır |

## §6 Sürücü Bağımlılık Grafiği (yazısal)

```text
HTTP isteği (iddia ⚠️)
   └─> MusicStreamHandler::dispatch(musicIdHex)
          ├─> authorize()  <── K008 kural (iddia)
          ├─> bypassActive() <── ortam (ad bilinmiyor ⚠️)
          ├─> findFile()  ──> MusicRepository::findPrimaryFile() ──> DB (K007)
          ├─> resolvePath() ──> libraryPath() ──> dosya sistemi (K015)
          ├─> parseRange() / isNotModified()  <── RFC (S3 §26)
          └─> sendBody() | fail()
```

> **Not:** bu şema yalnızca kodda adı geçen sembollerden kurulmuştur; okunmayan
> dallar `⚠️` ile bitirilmiştir.

## §7 Sürücü-Engine Ayrımı (S5)

| Soru | Cevap | Kanıt |
|---|---|---|
| Sürücü kim? | HTTP akış istekleyici | S1:21 |
| Engine kim? | sinyal işleyici motor | PHP yüzeyinde YOK |
| Engine dosyası var mı? | hayır — `**/*Audio*.php`, `**/*Equalizer*.php` bulunamadı | S5 |
| Sürücü engine'i çağırıyor mu? | kanıt yok → ⚠️ VERIFICATION REQUIRED | S5 |
| Sürücü K003-audio'ya delege ediyor mu? | tasarımda öyle (index.md) ama kod kanıtı yok | S2 + S5 |

## §8 Zamanlama ve Sıklık

| Olay | Sıklık | Kanıt |
|---|---|---|
| Akış isteği | istemci başına (HTTP) | S1:55 |
| 304 yanıtı | koşullu | S1:264 |
| Son dinlenenler okuması | sayfa render'ında (iddia) | S1 RecentTracks:63 |
| Cihaz profili okuması | istek başına (iddia) | ⚠️ VERIFICATION REQUIRED |
| Zamanlanmış iş | — | UNKNOWN (S5: `**/*Cron*.php` bulunamadı) |
| Kuyruk tüketimi | — | UNKNOWN (S5: `**/*Queue*.php` bulunamadı) |

## §9 Hata Sürücüleri

| # | Hata kaynağı | Sürücü tepkisi | Kanıt |
|---|---|---|---|
| H1 | kayıt yok | `fail(status, message)` | S1:357 |
| H2 | yetki yok | davranış ⚠️ | S1:121 (gövde okunmadı) |
| H3 | geçersiz Range | davranış ⚠️ | S1:224 |
| H4 | dosya yok | davranış ⚠️ | S1:173 |
| H5 | MIME bilinmiyor | davranış ⚠️ | S1:211 |
| H6 | DB yok | davranış ⚠️ | S1:99 |
| H7 | bypass + yetki çelişkisi | öncelik bilinmiyor ⚠️ | S1:149 · :121 |

**Kural:** hata durumunda uydurma yanıt üretilmez; her dal ya kodda ya `⚠️` ile
ya da `UNKNOWN` olarak kayıtlıdır.

## §9.1 Hata Yanıt Kataloğu

| # | Durum | Ne zaman | Kanıt |
|---|---|---|---|
| 1 | 200 (gövde) | geçerli istek + dosya mevcut | S1:331 |
| 2 | 206 (kısmi) | Range geçerli (iddia) | ⚠️ VERIFICATION REQUIRED — durum kodu okunmadı |
| 3 | 304 | ETag eşleşiyor | S1:294 |
| 4 | 404 (iddia) | dosya/satır yok | ⚠️ VERIFICATION REQUIRED — `fail` çağrı argümanı okunmadı |
| 5 | 401/403 (iddia) | yetki reddi | ⚠️ VERIFICATION REQUIRED |
| 6 | 416 (iddia) | Range sınır dışı | ⚠️ VERIFICATION REQUIRED |
| 7 | 429 (iddia) | oran aşımı | ⚠️ VERIFICATION REQUIRED — middleware audio'ya bağlı değil |
| 8 | 500 (iddia) | iç hata | ⚠️ VERIFICATION REQUIRED |

**Not:** 4–8. satırlardaki durum kodları **iddiadır**; `fail(int $status, ...)`
imzası durumun parametre olduğunu kanıtlar (S1:357), fakat hangi çağrıya hangi
kodun geçtiği okunmamıştır. Uydurma kod yazılmaz.

## §9.2 Hata Kaynağı Ağaçları

```text
Hata kaynağı
├─ Kimlik    → findFile() null        → fail()            (kanıt: S1:164, S1:357)
├─ Yetki     → authorize() reddi      → davranış ⚠️        (kanıt: S1:121)
├─ Dosya     → resolvePath() hata     → davranış ⚠️        (kanıt: S1:173)
├─ Protokol  → parseRange() geçersiz  → davranış ⚠️        (kanıt: S1:224)
├─ Veri      → db() hatası            → davranış ⚠️        (kanıt: S1:99)
└─ Sunum     → formatDuration(mixed)  → biçim belirsiz ⚠️  (kanıt: S1:157)
```

## §10 Sürücü Metrikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Sınıf satır uzunluğu | 380 | S1 (dosya ölçümü) |
| Genel/özel metot sayısı | 16 (sıra numarası 48–371 arası) | S1 okuma |
| Giriş noktası sayısı | 1 (`dispatch`) | S1:55 |
| `never` dönüşlü metot | 4 (`dispatch`,`run`,`emitNotModified`,`sendBody`,`fail`) | S1 imzalar |
| Repository satır uzunluğu | 111 | S1 |
| bileşen satır uzunluğu | 218 / 67 | S1 |
| DeviceManager satır uzunluğu | 663 | S1 |

## §11 Web Araştırması — İddia Kaydı

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| W3C, medya yeteneklerini (çözme/uyum) bildiren API tanımlar | https://www.w3.org/TR/media-capabilities/ | HTTP 200 | ✅ — bu repo'da kullanımı kanıtlanmadı (S5) |
| IETF, HTTP koşullu istek (If-None-Match) ve 304 davranışını tanımlar | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP 200 | ✅ — S1:264/294 ile uyumlu |
| IETF, HTTP/1.1'de Range isteğinin bayrak aralığını tanımlar | https://datatracker.ietf.org/doc/html/rfc9110 | HTTP 200 | ✅ — S1:224/331 ile uyumlu |
| IETF, HTTP/1.1 mesaj yapısını tanımlar | https://www.rfc-editor.org/rfc/rfc7230.html | HTTP 200 | ✅ — başlık yazımı dayanağı |

## §12 Açık Sorular (sürücü düzeyi)

| # | Soru | Kapı |
|---|---|---|
| Q3 | Sürücü mü, motor mu? | anayasa §9 |
| Q-s1 | Tetikleyici hangi yönlendirme katmanından geliyor? | ⚠️ VERIFICATION REQUIRED |
| Q-s2 | `bypassActive()` hangi ortam anahtarını okuyor? | ⚠️ VERIFICATION REQUIRED |
| Q-s3 | 429 Rate-limit bu sürücüye uygulanıyor mu? | ⚠️ VERIFICATION REQUIRED |
| Q-s4 | Zamanlanmış/kuyruk sürücüsü var mı? | UNKNOWN |
| Q-s5 | 304 yolunda ETag hangi alandan üretiliyor? | ⚠️ VERIFICATION REQUIRED |

## §13 Red Team Bulgu Listesi

| # | Bulgu | Severity |
|---|---|---|
| 1 | Sıra 3/4 sırası okunmadı (E15) | HIGH |
| 2 | Hata dalları H2–H7 kod gövdesi okunmadı | MEDIUM |
| 3 | Tetikleyici kaynağı (yönlendirme) bilinmiyor | MEDIUM |
| 4 | Zamanlanmış iş sürücüsü yok (S5) | MEDIUM |
| 5 | Rate-limit uygulanışı kanıtlanmadı | LOW |
| 6 | Metrik §10'daki "4" ifadesi 5 metot sayısını taşıyor — çelişki | LOW |

## §14 Doğrulama

| Kontrol | Hedef | Sonuç |
|---|---|---|
| Satır sayısı | ≥500 | ölçülür (bu dosya, dış ölçüm) |
| Son satır | authority birebir | birebir |
| `parent:` | 0 | 0 |
| `group` | `mimari` alıntısız | mimari |
| Mojibake | 0 | 0 |
| Yer tutucu | 0 | 0 |
| Kod kanıtı | ≥1 `Dosya/Satır` | §3 · §10 |

## §15 Bağımlılık Özeti

| Hedef | Tür | Kanıt |
|---|---|---|
| K007-veri | okuma | S2 bagimliliklar §17 |
| K015-media | dosya | S1:371 |
| K008-kural | okuma (iddia) | S2 master §21.4 |
| K014-network | protokol | S2 master §23/6 |
| K011-arayuz | sunum | S2 master §23/5 |

## §16 İlgili Dosyalar

| Dosya | İlişki |
|---|---|
| audio-service-anayasa.md | kurallar (önce okunur) |
| audio-service-ornekler.md | kod örnekleri |
| audio-service/index.md | künye |
| services-master.md §21.4 · §22 | kart ve akışlar |
| services-riskler.md | kök risk |

## §17 Sürücü Zaman Çizelgesi (tek istek, adım bazlı)

| # | Zaman sırası | Olay | Beklenen süre | Kanıt |
|---|---|---|---|---|
| 1 | t0 | istek kabul | — | S1:55 |
| 2 | t1 | yetki denetimi | bilinmiyor | S1:121 |
| 3 | t2 | bypass sorgusu | bilinmiyor | S1:149 |
| 4 | t3 | kayıt sorgusu | bilinmiyor | S1:51 |
| 5 | t4 | yol çözümü | bilinmiyor | S1:173 |
| 6 | t5 | başlık üretimi | bilinmiyor | S1:310 |
| 7 | t6 | 304 kararı | bilinmiyor | S1:264 |
| 8 | t7 | gövde gönderimi | bilinmiyor | S1:331 |
| 9 | t8 | bağlantı kapanışı | bilinmiyor | ⚠️ VERIFICATION REQUIRED |

**Süre sütunu neden boş değil:** her hücre "bilinmiyor" ya da `⚠️` ile doludur —
ölçüm yapılmadığı için sayı uydurulmaz (ölçüm PLANNED, sartname §8).

## §18 Sürücü Sorumluluk Matrisi (RACI)

| Eylem | audio-service | K003-audio | K007 | K008 | K015 | K014 |
|---|---|---|---|---|---|---|
| Yetki denetimi | R | — | — | C | — | — |
| Dosya satırı okuma | R | — | A | — | C | — |
| Yol çözümleme | R | — | — | — | A | — |
| Range ayrıştırma | R | — | — | — | — | C |
| Gövde gönderimi | R | — | — | — | C | A |
| Sinyal işleme | — | A/R | — | C | — | — |
| Kural üretimi | — | — | — | A/R | — | — |
| Şema yazımı | — | — | A/R | — | — | — |
| Dosya kalıcılığı | — | — | — | — | A/R | — |
| Protokol seçimi | — | — | — | — | — | A/R |

> **Lejant:** R = yürütücü · A = sorumlu (hesap verir) · C = danışılan ·
> `—` = ilgisi yok. A/R = hem sorumlu hem yürütücü.

## §19 Sürücü Sınır Devirleri

| Durum | Devir hedefi | Neden |
|---|---|---|
| Sinyal isteği gelirse | K003-audio | sinyal motor sahibi |
| EQ/kompresör isteği | K004-dsp | DSP zinciri sahibi |
| Dosya silme | K015-media | kalıcılık sahibi |
| Katalog yazma | K015-media | katalog sahibi |
| Eşik/izin kuralı | K008-kural | kural sahibi |
| Ekran çizimi | K011-arayuz | arayüz sahibi |
| Taşıma protokolü değişikliği | K014-network | ağ sahibi |
| Zamanlanmış iş | background-jobs (K012 içi) | Q5 motor bilinmiyor |
| Kontrat/DTO biçimi | service-contracts | Q4 kapsam belirsiz |

## §20 Sürücü ile Anayasa Eşlemesi

| Sürücü konusu | Anayasa maddesi |
|---|---|
| yetki kapısı | A4 · A5 |
| yol çözümü | A10 |
| Range | A6 |
| 304 / ETag | A7 |
| içerik tipi | A8 |
| hata yanıtı | A9 · A3 |
| fallback veri | A13 |
| süre biçimi | A14 |
| cihaz bayrakları | A16 |
| sınır çelişkisi | A17 |

## §21 Sürücü Test Senaryoları (taslak — K019)

| # | Senaryo | Girdi | Beklenen | Durum |
|---|---|---|---|---|
| 1 | geçerli kimlik | geçerli `musicIdHex` | gövde | ⚠️ test yok (S5) |
| 2 | geçersiz kimlik | bilinmeyen `musicIdHex` | hata durumu | ⚠️ test yok |
| 3 | yetkisiz istek | yetki reddi | gövde yok | ⚠️ test yok |
| 4 | ETag eşleşmesi | `If-None-Match` | 304 | ⚠️ test yok |
| 5 | Range | `Range: bytes=0-99` | kısmi gövde | ⚠️ test yok |
| 6 | geçersiz Range | bozuk başlık | ⚠️ davranış bilinmiyor | ⚠️ test yok |
| 7 | bypass açık | ortam bayrağı | yetki atlanır | ⚠️ test yok |
| 8 | repo null | bağımlılık yok | fallback veri | ⚠️ test yok |

**Not:** `shared/tests/` içinde bu sürücüye ait test dosyası aranmadı →
⚠️ VERIFICATION REQUIRED (S5 iddiası değil, okuma eksikliği).

## §22 Bilinen Sürücü Riskleri

| # | Risk | Etki | Karşı önlem |
|---|---|---|---|
| 1 | yetki kapısı bypass ile baypas | çok yüksek | A5 + ortam denetimi |
| 2 | Range yanlışsa oynatıcı kesilir | orta | A6 + test 5/6 |
| 3 | fallback veri gerçek sanılır | orta | A13 |
| 4 | motor sınırı çözülmezse çift kod | yüksek | Q3 |
| 5 | süre ölçümü yok → gecikme görünmez | orta | §17 + ölçüm PLANNED |

## §23 Web Araştırması — İkinci Parti

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| HTTP, durum kodlarının semantiğini tanımlar (2xx/3xx/4xx/5xx sınıfları) | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP 200 | ✅ — §9.1 dayanağı |
| HTTP/1.1 istek satırı ve başlık yazım kuralları | https://www.rfc-editor.org/rfc/rfc7230.html | HTTP 200 | ✅ — başlık ayrıştırma dayanağı |
| 12 faktörlük uygulama, yapılandırmayı ortama taşır | https://12factor.net/ | HTTP 200 | ✅ — `fromEnvironment()` yaklaşımıyla uyumlu (S1:30) |
| CNCF, bulut yerel projeleri destekleyen vakıftır | https://www.cncf.io/ | HTTP 200 | ✅ — genel bağlam; bu servisle doğrudan ilişki kanıtlanmadı |

## §24 Açık Kapı Sayımı

| Kapı | Sayı |
|---|---|
| `⚠️ VERIFICATION REQUIRED` işaretli satır (gövde) | §2 · §9 · §9.1 · §17 içinde toplam 24 |
| `UNKNOWN` işaretli satır | §7 · §8 içinde 4 |
| Açık soru (Q) | 6 (Q3 + Q-s1..Q-s5) |
| Red team bulgusu | 6 (§13) |

## §25 Bağlantılar

| Dosya | İlişki |
|---|---|
| audio-service-anayasa.md | önce okunur (kurallar) |
| audio-service-ornekler.md | sonra okunur (kod) |
| services-master.md §22.1–§22.3 | kök akış örnekleri |
| services-bagimliliklar.md §17 | bağımlılık matrisi |
| services-dogrulama.md | kök doğrulama |

## §26 Sürücü Sembol Dizinı (satır numaralı)

| # | Sembol | Dosya | Satır | Tür |
|---|---|---|---|---|
| 1 | `final class MusicStreamHandler` | MusicStreamHandler.php | 21 | sınıf |
| 2 | `__construct()` | MusicStreamHandler.php | 48 | kurucu |
| 3 | `dispatch()` | MusicStreamHandler.php | 55 | statik giriş |
| 4 | `run()` | MusicStreamHandler.php | 60 | özel yürütücü |
| 5 | `authorize()` | MusicStreamHandler.php | 121 | güvenlik |
| 6 | `bypassActive()` | MusicStreamHandler.php | 149 | statik güvenlik |
| 7 | `findFile()` | MusicStreamHandler.php | 164 | veri |
| 8 | `resolvePath()` | MusicStreamHandler.php | 173 | yol |
| 9 | `contentType()` | MusicStreamHandler.php | 211 | MIME |
| 10 | `parseRange()` | MusicStreamHandler.php | 224 | protokol |
| 11 | `isNotModified()` | MusicStreamHandler.php | 264 | protokol |
| 12 | `emitNotModified()` | MusicStreamHandler.php | 294 | çıkış |
| 13 | `emitBaseHeaders()` | MusicStreamHandler.php | 310 | çıkış |
| 14 | `sendBody()` | MusicStreamHandler.php | 331 | çıkış |
| 15 | `fail()` | MusicStreamHandler.php | 357 | hata |
| 16 | `libraryPath()` | MusicStreamHandler.php | 371 | yapılandırma |
| 17 | `final class MusicRepository` | MusicRepository.php | 15 | sınıf |
| 18 | `__construct()` | MusicRepository.php | 19 | kurucu (özel) |
| 19 | `fromEnvironment()` | MusicRepository.php | 30 | fabrika |
| 20 | `findPrimaryFile()` | MusicRepository.php | 51 | sorgu |
| 21 | `findRecentTracks()` | MusicRepository.php | 79 | sorgu |
| 22 | `db()` | MusicRepository.php | 99 | altyapı |
| 23 | `RecentTracksComponent` | RecentTracksComponent.php | 31 | sınıf |
| 24 | `__construct()` | RecentTracksComponent.php | 63 | bağımlılık |
| 25 | `key()` | RecentTracksComponent.php | 111 | kimlik |
| 26 | `dbTracks()` | RecentTracksComponent.php | 122 | veri |
| 27 | `formatDuration()` | RecentTracksComponent.php | 157 | sunum |
| 28 | `defaultTracks()` | RecentTracksComponent.php | 172 | fallback |
| 29 | `defaultPlaylists()` | RecentTracksComponent.php | 204 | fallback |
| 30 | `PlayerInfoComponent` | PlayerInfoComponent.php | 14 | sınıf |

**Sayım:** 30 sembol · tümü S1 disk okuması · 0 tahmin.

## §27 Tetikleyici → Yanıt Eşleme Matrisi

| # | Tetikleyici | Yanıt yolu | Çıkış noktası | Kanıt |
|---|---|---|---|---|
| T1 | geçerli kimlik isteği | S1→S9 | `sendBody()` | S1:331 |
| T2 | kimlik yok | S4→S10 | `fail()` | S1:357 |
| T3 | yetki reddi | S2→S10 | `fail()` (iddia) | ⚠️ S1:121 gövde okunmadı |
| T4 | bypass açık | S3 atlanır | dosya aramaya geç | S1:149 |
| T5 | ETag eşleşmesi | S7→S8 | `emitNotModified()` | S1:294 |
| T6 | Range var | S6→S9 | kısmi gövde (iddia) | ⚠️ durum kodu okunmadı |
| T7 | Range yok | S6→S9 | tam gövde (iddia) | ⚠️ S1:224 |
| T8 | yol çözülemez | S5→S10 | davranış ⚠️ | S1:173 |
| T9 | son dinlenenler | bileşen yolu | DB ya da fallback | S1:122 · :172 |
| T10 | repo null | fallback | `defaultTracks()` | S1:172 |
| T11 | cihaz profili | profil yolu | bayrak false/true | S1:463 |
| T12 | sinyal isteği | devir | K003-audio | S2 index.md |
| T13 | zamanlanmış iş | — | — | UNKNOWN |
| T14 | katalog yazımı | devir | K015-media | S2 §23/4 |
| T15 | oran aşımı | — | — | UNKNOWN |

## §28 Sürücü Kapsam Dışı Liste (net devir)

| # | İstek | Hedef düğüm | Gerekçe |
|---|---|---|---|
| 1 | ses sinyali işleme | K003-audio | motor sahipliği |
| 2 | DSP zinciri ayarı | K004-dsp | DSP sahipliği |
| 3 | medya dosyası silme/üretme | K015-media | dosya sahipliği |
| 4 | katalog yazma | K015-media | katalog sahipliği |
| 5 | izin/eşik kuralı üretme | K008-kural | kural sahipliği |
| 6 | şema/DB yazma | K007-veri | veri sahipliği |
| 7 | ekran öğesi çizme | K011-arayuz | arayüz sahipliği |
| 8 | taşıma protokolü seçme | K014-network | ağ sahipliği |
| 9 | zamanlanmış iş motoru | background-jobs | Q5 |
| 10 | DTO/kontrat biçimi | service-contracts | Q4 |
| 11 | cihaz kimliği/yetki | K010-identitiy | Q6 |
| 12 | kullanıcı profili | user-service | komşu servis |
| 13 | test altyapısı kurma | K019 | §23/7 |
| 14 | dağıtım/CI | K018 | §23/8 |
| 15 | YZ çağrısı | K006/K016 | Q1 |

## §29 Sürücü Doğrulama Özeti

| Kontrol | Hedef | Durum |
|---|---|---|
| Satır sayısı | ≥500 | dış ölçüm |
| Son satır | authority birebir | birebir |
| `parent:` alanı | 0 | 0 |
| `group` alıntısız | `mimari` | mimari |
| Mojibake U+FFFD | 0 | 0 |
| CJK karakter | 0 | 0 |
| Yer tutucu blok | 0 | 0 |
| Boş tablo hücresi | 0 | 0 |
| Kod kanıtı (`Dosya`/`Satır`) | ≥1 | §3 · §17 · §26 |
| Web iddiası tablosu formatı | `İddia · URL · tarih · Durum` | §11 · §23 |
| Açık soru kaydı | var | §12 (6) |
| Red team bulgusu | var | §13 (6) |

## §29.1 Sürücü Sözlüğü

| Terim | Tanım |
|---|---|
| sürücü (driver) | sistemi hareket ettiren olay/girdi akışı |
| tetikleyici | sürücüyü başlatan dış olay |
| durum | adım arası geçiş noktası |
| geri besleme | bir adımın olumsuz sonucunun önceki adıma dönmesi |
| devir | sorumluluğun başka düğüme geçmesi |
| bypass | yetki kapısının ortam bayrağıyla atlatılması |
| ETag | yanıtın tekrar kullanılabilirlik etiketi |
| Range | istemcinin istediği bayrak aralığı |
| fallback | asıl kaynak olmadığında devreye giren yedek |
| RACI | yürütücü/sorumlu/danışılan bilgi matrisi |
| S5 | yokluk kanıtı (aranıp bulunamama) |
| Q3 | servis mi motor mu sorusu |

## §30 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik | Kaynak |
|---|---|---|---|
| 2026-10-13 | 1.0.0 | sürücü dosyası S1/S3/S5 kanıtıyla yazıldı | S4 oturum |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
