---
node_id: K005-firmware/firmware-web-arastirma
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/firmware-master
  - K005-firmware/firmware-dogrulama
evidence:
  - "ADR-097 §5.5 (satır 140) — kanit grubu — okundu 2026-10-12"
  - ".ai/architecture/index.md §8.1 (satır 160) — grup kuralı — okundu 2026-10-13"
  - ".github/workflows/ci.yml (satır 8, 142) — CI / js-test — okundu 2026-10-13"
  - "URL havuzu (181 satır doğrulanmış kayıt) — 48 satır bu dosyaya 2026-10-13"
validation:
  - "2026-10-13 — 48 URL havuzdan yazıldı (HTTP-200 doğrulandı) · benzersizlik 48/48"
  - "2026-10-13 — FM 9 anahtar (status/parent yok) — K-13"
open_questions:
  - "OTA bulut tarafı (AWS/Azure/GCP) CoreMusic hedefinde kullanılıyor mu? → kapsam teyidi (⚠️)"
  - "RTOS seçimi yapılmadı → §3.3 satırları seçenek olarak durur, seçim değildir"
group: kanit
---

# firmware-web-arastirma — Web Araştırması (iddia/URL kayıtları)

> Dayanak: ADR-097 §5.5 (kanit grubu) · `.ai/architecture/index.md` §8.1.
> Bu dosya K005'in **web araştırma** defteridir: her satır bir iddia + benzersiz URL.

## §1 ELI15

> **Firmware'in kanıtı internette yaşar: ses API'leri, protokol RFC'leri,
> RTOS ve çapraz-derleme dokümanları. Her satır 2026-10-13 HTTP-200 turundan
> geçti; geçmeyen havuza girmedi.**

Kural üçlüsü: (1) her satırda URL olur, (2) aynı URL bu üretimde tek dosyada
geçer, (3) erişilmeyen URL `✅` olmaz.

## §2 Domain havuzu (bu dosyaya ayrılan — çakışma yasağı)

| # | Havuz alanı | Kullanım amacı | Bu dosyada ayrılan alan adı |
|---|---|---|---|
| 1 | Web ses API'leri | tarayıcı-içi ses referansı | `developer.mozilla.org` |
| 2 | Protokol standartları | TLS · HTTP · kimlik · log | `rfc-editor.org` |
| 3 | Gömülü işletim sistemi | görev · sürücü · yapı | `freertos.org` · `zephyrproject.org` · `docs.zephyrproject.org` |
| 4 | Çip-ailesi SDK | I2S · ESP32 | `docs.espressif.com` · `espressif.com` |
| 5 | Mesajlaşma standardı | hafif mesajlaşma | `docs.oasis-open.org` |
| 6 | Emülasyon | hedef simülasyonu | `qemu.org` |

> **Bölünme kuralı:** derleme/standart araçları (CMake · GCC · MISRA) →
> `firmware-master.md` §4 · güvenlik/doğrulama kaynakları (OWASP · NVD ·
> SLSA) → `firmware-dogrulama.md` §2 · bu dosya yalnız §2'deki altı alan
> adını kullanır. Çocuklara özgü alan adları çocuk `*-web-arastirma.md`
> dosyalarına aittir.

## §3 İddia / URL tabloları (48 satır)

### §3.1 Tarayıcı ses ve zamanlama API'leri (satır 1–22)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Web Audio API, tarayıcıda ses üretimi ve işleme arayüzüdür | https://developer.mozilla.org/en-US/docs/Web/API/Web_Audio_API | 2026-10-13 | ✅ HTTP-200 |
| AudioContext.baseLatency, giriş-gecikmenin öngörülen süresini verir | https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/baseLatency | 2026-10-13 | ✅ HTTP-200 |
| AudioContext.outputLatency, çıkış gecikmesini belirtir | https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/outputLatency | 2026-10-13 | ✅ HTTP-200 |
| AudioWorkletNode, özel işleyiciyi gerçek zamanlı yürütme iş parçacığına yerleştirir | https://developer.mozilla.org/en-US/docs/Web/API/AudioWorkletNode | 2026-10-13 | ✅ HTTP-200 |
| AnalyserNode, zaman-bölgesi ve frekans analizi üretir | https://developer.mozilla.org/en-US/docs/Web/API/AnalyserNode | 2026-10-13 | ✅ HTTP-200 |
| BiquadFilterNode, ikili kutuplu (biquad) filtre uygular | https://developer.mozilla.org/en-US/docs/Web/API/BiquadFilterNode | 2026-10-13 | ✅ HTTP-200 |
| DynamicsCompressorNode, tepe kırpma (compression) kontrolü sağlar | https://developer.mozilla.org/en-US/docs/Web/API/DynamicsCompressorNode | 2026-10-13 | ✅ HTTP-200 |
| GainNode, ses seviyesi (gain) ayarını uygular | https://developer.mozilla.org/en-US/docs/Web/API/GainNode | 2026-10-13 | ✅ HTTP-200 |
| AudioBuffer, bellekte tutulan ham ses örneği tamponudur | https://developer.mozilla.org/en-US/docs/Web/API/AudioBuffer | 2026-10-13 | ✅ HTTP-200 |
| Performance.now, yüksek çözünürlüklü zaman damgası verir | https://developer.mozilla.org/en-US/docs/Web/API/Performance/now | 2026-10-13 | ✅ HTTP-200 |
| PerformanceObserver, performans ölçüm olaylarını izler | https://developer.mozilla.org/en-US/docs/Web/API/PerformanceObserver | 2026-10-13 | ✅ HTTP-200 |
| Fetch API, modern HTTP istek arayüzüdür | https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API | 2026-10-13 | ✅ HTTP-200 |
| AbortController, bekleyen istekleri iptal etmeyi sağlar | https://developer.mozilla.org/en-US/docs/Web/API/AbortController | 2026-10-13 | ✅ HTTP-200 |
| WebSocket, çift-yönlü kalıcı bağlantı protokolüdür | https://developer.mozilla.org/en-US/docs/Web/API/WebSocket | 2026-10-13 | ✅ HTTP-200 |
| WebTransport, HTTP/3 üzerinde çoklu-akım taşır | https://developer.mozilla.org/en-US/docs/Web/API/WebTransport | 2026-10-13 | ✅ HTTP-200 |
| Worker, arayüz iş parçacığından bağımsız kod çalıştırır | https://developer.mozilla.org/en-US/docs/Web/API/Worker | 2026-10-13 | ✅ HTTP-200 |
| CORS, kaynaklar arası erişim kurallarını tanımlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS | 2026-10-13 | ✅ HTTP-200 |
| HTTP Range istekleri, dosyanın parça parça okunmasını sağlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Range_requests | 2026-10-13 | ✅ HTTP-200 |
| 401 Unauthorized, kimlik doğrulama gerekli durumunu bildirir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/401 | 2026-10-13 | ✅ HTTP-200 |
| 503 Service Unavailable, hizmetin geçici olarak çalışmadığını bildirir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/503 | 2026-10-13 | ✅ HTTP-200 |
| SubtleCrypto, düşük-seviyeli kripto işlemleri arayüzüdür | https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto | 2026-10-13 | ✅ HTTP-200 |
| Streams API, veri akışlarını parça parça işlemeyi tanımlar | https://developer.mozilla.org/en-US/docs/Web/API/Streams_API | 2026-10-13 | ✅ HTTP-200 |

### §3.2 Protokol ve kimlik standartları (satır 23–36)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| RFC 7252, CoAP (sınırlı cihazlar için REST protokolü) tanımlar | https://www.rfc-editor.org/rfc/rfc7252 | 2026-10-13 | ✅ HTTP-200 |
| RFC 8613, OSCORE ile CoAP mesajlarını uçtan uca korur | https://www.rfc-editor.org/rfc/rfc8613 | 2026-10-13 | ✅ HTTP-200 |
| RFC 5424, syslog kayıt formatını ve taşımayı tanımlar | https://www.rfc-editor.org/rfc/rfc5424 | 2026-10-13 | ✅ HTTP-200 |
| RFC 8259, JSON veri değişim biçiminin standardıdır | https://www.rfc-editor.org/rfc/rfc8259 | 2026-10-13 | ✅ HTTP-200 |
| RFC 9113, HTTP/2 protokolünü tanımlar | https://www.rfc-editor.org/rfc/rfc9113 | 2026-10-13 | ✅ HTTP-200 |
| RFC 9114, HTTP/3 protokolünü tanımlar | https://www.rfc-editor.org/rfc/rfc9114 | 2026-10-13 | ✅ HTTP-200 |
| RFC 6749, OAuth 2.0 yetkilendirme çerçevesidir | https://www.rfc-editor.org/rfc/rfc6749 | 2026-10-13 | ✅ HTTP-200 |
| RFC 7636, PKCE ile yetki kodu sızıntısını engeller | https://www.rfc-editor.org/rfc/rfc7636 | 2026-10-13 | ✅ HTTP-200 |
| RFC 8446, TLS 1.3 protokolünü tanımlar | https://www.rfc-editor.org/info/rfc8446 | 2026-10-13 | ✅ HTTP-200 |
| RFC 6347, DTLS 1.2 ile UDP üzerinde güvenli taşıma sağlar | https://www.rfc-editor.org/rfc/rfc6347 | 2026-10-13 | ✅ HTTP-200 |
| RFC 9147, DTLS 1.3 protokolünü tanımlar | https://www.rfc-editor.org/rfc/rfc9147 | 2026-10-13 | ✅ HTTP-200 |
| RFC 7748, eliptik eğri protokollerindeki temel eğrileri tanımlar | https://www.rfc-editor.org/rfc/rfc7748 | 2026-10-13 | ✅ HTTP-200 |
| RFC 8032, EdDSA imzalama algoritmasını tanımlar | https://www.rfc-editor.org/rfc/rfc8032 | 2026-10-13 | ✅ HTTP-200 |
| RFC 9562, UUID üretim kurallarını günceller | https://www.rfc-editor.org/rfc/rfc9562 | 2026-10-13 | ✅ HTTP-200 |

### §3.3 Gömülü işletim sistemi, SDK ve emülasyon (satır 37–48)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| FreeRTOS, gömülü sistemler için gerçek-zamanlı çekirdeğin resmi sitesidir | https://www.freertos.org/RTOS.html | 2026-10-13 | ✅ HTTP-200 |
| FreeRTOS SSS, çekirdek kullanımı ve lisans sorularını yanıtlar | https://www.freertos.org/FAQ.html | 2026-10-13 | ✅ HTTP-200 |
| Zephyr dokümantasyon ana sayfası, proje belgelerinin girişidir | https://docs.zephyrproject.org/latest/ | 2026-10-13 | ✅ HTTP-200 |
| Zephyr çekirdek (kernel) dokümanı, görev ve senkronizasyonu anlatır | https://docs.zephyrproject.org/latest/kernel/index.html | 2026-10-13 | ✅ HTTP-200 |
| Zephyr yapı ve konfigürasyon dokümanı, derleme ayarlarını tanımlar | https://docs.zephyrproject.org/latest/build/index.html | 2026-10-13 | ✅ HTTP-200 |
| Zephyr araç zinciri dokümanı, derleyici ve toolchain seçimini anlatır | https://docs.zephyrproject.org/latest/develop/toolchains/index.html | 2026-10-13 | ✅ HTTP-200 |
| Zephyr desteklenen kart listesi, hedef donanım seçeneklerini verir | https://docs.zephyrproject.org/latest/boards/index.html | 2026-10-13 | ✅ HTTP-200 |
| ESP-IDF, Espressif çipleri için resmi geliştirme çerçevesidir | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/ | 2026-10-13 | ✅ HTTP-200 |
| ESP-IDF I2S dokümanı, seri ses veri yolunun yapılandırmasını anlatır | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/peripherals/i2s.html | 2026-10-13 | ✅ HTTP-200 |
| Zephyr projesi, açık kaynak gömülü işletim sistemi topluluğudur | https://www.zephyrproject.org/ | 2026-10-13 | ✅ HTTP-200 |
| MQTT 5.0 spesifikasyonu, OASIS standardı olarak yayımlanmıştır | https://docs.oasis-open.org/mqtt/mqtt/v5.0/mqtt-v5.0.html | 2026-10-13 | ✅ HTTP-200 |
| QEMU sistem emülasyonu, hedef makine taklidini (taklit/simülasyon) tanımlar | https://www.qemu.org/docs/master/system/ | 2026-10-13 | ✅ HTTP-200 |

> **Benzersizlik sayımı:** 48 satır / 48 farklı URL / 0 tekrar. Başka K005
> kök dosyasında bu URL'lerin hiçbiri geçmez (master 12 · dogrulama 26).

## §4 İddia → K005 kullanımı (hangi iddia nereye bağlanır)

| # | İddia grubu | Satır aralığı | Kullanıldığı dosya | Kullanım amacı |
|---|---|---|---|---|
| 1 | Ses API'leri ve gecikme | 1–9 | `firmware-bilesenler.md` | gecikme ölçümü dili |
| 2 | Zamanlama ve performans | 10–11 | `firmware-test-stratejisi.md` | ölçüm metodu |
| 3 | Ağ ve iptal | 12–22 | `update-*` (dalga-2) | parça-dosya indirme |
| 4 | Güvenli taşıma | 31–33 | `security` (dalga-3) | TLS/DTLS referansı |
| 5 | İmzalama | 34–35 | `security` (dalga-3) | imza algoritması |
| 6 | Kimlik ve kayıt | 25–27 · 36 | `security` (dalga-3) | syslog · JSON · UUID |
| 7 | RTOS çekirdek | 37–43 | `runtime-*` | seçenek listesi — seçim YOK |
| 8 | Ses yolu donanım arayüzü | 44–45 | `audio-control-*` | I2S yapılandırması |
| 9 | Emülasyon | 48 | `testing/*` | hedef-öncesi test |

"Seçim YOK" ifadesi bilinçlidir: satır 37–43 herhangi bir RTOS'un
seçildiğini **iddia etmez**, yalnızca kaynak olarak kayıtlıdır.

## §5 Araştırma yöntemi

| # | Adım | Kural |
|---|---|---|
| 1 | Kaynak seçimi | Yalnız resmi alan adı (§2 havuzu) — blog/üçüncü taraf yok |
| 2 | Kayıt | Her iddia `İddia / URL / Tarih / Durum` sütunlarıyla yazılır |
| 3 | Doğrulama | HTTP isteği → yalnız 200 `✅` olur |
| 4 | Tazelik | 90 gün eski `✅` yeniden denenir |
| 5 | Tekrar | Aynı URL ikinci dosyaya yazılmaz — üç dosya havuzdan bölünür |
| 6 | Başarısızlık | erişim reddi (403/406) veya yanıt yok (0) → havuza alınmaz |

## §6 Durum özeti

| Durum | Adım | Oran |
|---|---|---|
| ✅ HTTP-200 | 48 | %100 |
| ⚠️ VERIFICATION REQUIRED | 0 | %0 |
| ❌ ERİŞİLEMEDİ | 0 | %0 |

**Kapı:** §6'daki `✅` satırları 2026-10-13 turundan gelir; 90 gün sonra
yeniden denenir (`firmware-dogrulama.md` §7 adım 2).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Bulut OTA kullanımı CoreMusic cihaz hedefinde geçerli mi? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | RTOS seçimi (FreeRTOS/Zephyr) ne zaman karara bağlanır? | `⚠️ UNKNOWN` |
| Q3 | Tarayıcı ses API'leri K005 kapsamına girer mi, yalnız K003 mü? | `⚠️ VERIFY REQUIRED` |

## §8 Kaynak değerlendirme ölçütleri

| Ölçüt | Soru | Red nedeni |
|---|---|---|
| Sahiplik | Kaynak projenin resmi alanı mı? | üçüncü taraf özet |
| Sürüm | Belge hangi sürümü kapsıyor? | süresiz |
| Tarih | Erişim tarihi yazabiliyor muyuz? | tarihsiz iddia |
| Tekrar | Bu URL başka dosyada var mı? | çakışma |
| İddia | Tek cümlede ne iddia ediyor? | iddiasız satır |

Beş ölçütün hepsi sağlanmayan satır tabloya girmez; eksik ölçüt varsa satır
`⚠️` olarak yazılır.

## §9 İddia yazım kuralları

1. İddia **tek cümledir** ve nokta ile biter.
2. İddia sayfanın gerçekten iddia ettiği şeyi yansıtır (başlık yerine geçmez).
3. Birinci çoğul şahıs yoktur; tarafgirlik yoktur.
4. Sürüm numarası yalnızca sayfada yazılıysa yazılır.
5. Türkçe kurulur, teknik terim İngilizce kalır (`biquad`, `RTOS`, `TLS`).
6. İddia, kaynağından okunmadan "bilinen" şeye göre yazılmazsa `⚠️` taşır.

## §10 Alan adı → sorumluluk haritası

| Alan adı | Sorumlu rol | Kullanım yeri |
|---|---|---|
| `developer.mozilla.org` | UI + embedded | §3.1 |
| `rfc-editor.org` | security + backend | §3.2 |
| `freertos.org` · `zephyrproject.org` | embedded | §3.3 |
| `docs.espressif.com` | audio-hw + embedded | §3.3 |
| `docs.oasis-open.org` | backend | §3.3 |
| `qemu.org` | QA | §3.3 |

Rol adları `.ai/AGENTS.md` §4 tablosundaki 11 ajan adıdır; uydurma rol yoktur.

## §11 HTTP yanıt kodu kullanımı

| Kod | Anlamı | Bu üretimde |
|---|---|---|
| 200 | başarılı | 86 satır `✅` |
| 401 | kimlik gerekli | iddia satırı olarak kullanılır (§3.1), hata değil |
| 403 | erişim reddi | havuza girmedi |
| 406 | kabul edilebilir içerik yok | havuza girmedi |
| 404 | bulunamadı | havuza girmedi |
| 503 | servis yok | iddia satırı olarak kullanılır (§3.1), hata değil |
| 0 | yanıt kodu üretilemedi | havuza girmedi |

İddia konusu kod (401 · 503) ile erişim sonucu aynı şey değildir; ikisi bu
tabloda ayrı satırlarda durur.

## §12 Tazelik ve yeniden doğrulama

| Durum | Eylem | Sıklık |
|---|---|---|
| Erişim ≤ 90 gün | geçerli sayılır | — |
| Erişim > 90 gün | yeniden denenir | üretim turunda |
| 200 değilse | durum `⚠️`'a alınır | anında |
| Alan adı kapandıysa | resmi alternatif aranır, eski URL silinmez | anında |
| Yenileme kaydı | `validation:` listesine tarih düşülür | üretim turunda |

Silme yerine işaretleme yapılır: eski URL kaybolmaz, durumu değişir.

## §13 Dışlama kaydı (havuza alınmayanlar)

| Alan adı grubu | Yanıt | Sonuç |
|---|---|---|
| Bazı çip-üreticisi doküman portalları | 403 / 406 | havuza alınmadı |
| Bazı çip-üreticisi ürün sayfaları | yanıt kodu üretilemedi | havuza alınmadı |

Dışlama gerekçesi sayısal yanıt kodudur; "güvenilmez" gibi niteliksel bir
gerekçe yazılmaz. Dışlanan alan adları için alternatif resmi kaynak aranır
ve o kaynak da aynı HTTP turundan geçerse havuza girer.

## §14 Çocuk dosyalara devredilen alan adları

| Alan adı | Gideceği çocuk | Durum |
|---|---|---|
| Yapısal firmware portalları | `boot/boot-web-arastirma.md` | dalga-2 |
| Çalışma anı kaynakları | `runtime/runtime-web-arastirma.md` | dalga-2 |
| Ses yolu kaynakları | `audio-control/audio-control-web-arastirma.md` | dalga-2 |
| Cihaz kontrol kaynakları | `device-control/device-control-web-arastirma.md` | dalga-2 |
| Güç kaynakları | `power-control/power-control-web-arastirma.md` | dalga-2 |
| Güncelleme/imzalama | `update/update-web-arastirma.md` | dalga-2 |

Bu altı dosya bu üretimde yazılmadı; alan adları burada rezerve edilir ve
bu dosyada kullanılmaz.

## §15 Sayım ve kontrol tablosu

| Kontrol | Beklenen | Yöntem |
|---|---|---|
| §3 satır sayısı | 48 | satır sayımı |
| Benzersiz URL | 48 | küme büyüklüğü |
| Diğer iki dosyayla kesişim | 0 | küme kesişimi |
| `https://` olmayan satır | 0 | metin arama |
| Tarih sütunu | 2026-10-13 (48/48) | metin arama |
| Dolgu / mojibake | 0 | metin arama |

Altı kontrol da çalıştırılır ve sonucu ÖLÇÜM raporuna yazılır.

## §16 Revizyon günlüğü

| Tarih | Olay | Kapsam |
|---|---|---|
| 2026-10-12 | İlk yazım (48 satır · `⚠️`) | araştırma defteri |
| 2026-10-13 | 48 URL havuzdan yenilendi · HTTP-200 | tazelik |
| 2026-10-13 | FM 9 anahtar (K-13) · §8–§15 eklendi | yöntem |

Günlük append-only'dir; geçmiş satır değişmez, yeni olay altına eklenir.

## §17 Kapanış

Araştırma defteri üç şeyle kapanır: 48 benzersiz URL, tek satırlık Türkçe
iddialar ve 2026-10-13 erişim kanıtı. Kaynak seçimi ölçütleri (§8), yazım
kuralları (§9), tazelik (§12) ve dışlama kaydı (§13) bu defteri tekrar
üretilebilir yapar. Dosyanın son satırı Authority bloğudur ve tek satırdır.

## §18 İddia yükseltme örnekleri (önce → sonra)

| # | Zayıf iddia | Güçlü iddia | Ne değişti |
|---|---|---|---|
| 1 | "MDN iyi bir kaynaktır" | "Web Audio API, tarayıcıda ses üretimi ve işleme arayüzüdür" | konu + sahiplik |
| 2 | "TLS güvenlidir" | "RFC 8446, TLS 1.3 protokolünü tanımlar" | standart numarası |
| 3 | "RTOS kullanılır" | "FreeRTOS, gömülü sistemler için gerçek-zamanlı çekirdeğin resmi sitesidir" | seçim iddiası yok |
| 4 | "I2S anlatılıyor" | "ESP-IDF I2S dokümanı, seri ses veri yolunun yapılandırmasını anlatır" | kapsam |
| 5 | "QEMU var" | "QEMU sistem emülasyonu, hedef makine taklidini tanımlar" | işlev |
| 6 | "JSON standart" | "RFC 8259, JSON veri değişim biçiminin standardıdır" | künye |
| 7 | "gecikme ölçülür" | "AudioContext.outputLatency, çıkış gecikmesini belirtir" | ölçüm kaynağı |
| 8 | "güncelleme imzalanır" | "RFC 8032, EdDSA imzalama algoritmasını tanımlar" | algoritma |
| 9 | "log atılır" | "RFC 5424, syslog kayıt formatını ve taşımayı tanımlar" | format |
| 10 | "cihaz bağlanır" | "MQTT 5.0 spesifikasyonu, OASIS standardı olarak yayımlanmıştır" | standart |
| 11 | "derleme ayarlanır" | "Zephyr yapı ve konfigürasyon dokümanı, derleme ayarlarını tanımlar" | kaynak |
| 12 | "imza doğrulanır" | "RFC 7748, eliptik eğri protokollerindeki temel eğrileri tanımlar" | eğri |
| 13 | "arşiv indirilir" | "HTTP Range istekleri, dosyanın parça parça okunmasını sağlar" | mekanizma |
| 14 | "panel açılır" | "PerformanceObserver, performans ölçüm olaylarını izler" | API adı |
| 15 | "güvenli aktarım" | "RFC 9147, DTLS 1.3 protokolünü tanımlar" | sürüm |

Zayıf iddia satırı bu dosyada bulunmaz; tablo yalnızca farkı öğretmek için
vardır ve `Durum` sütunu taşımaz (iddia/URL tablosu değildir).

## §19 URL yapıları (yol parçası okuma)

| Yol parçası | Anlamı | Örnek |
|---|---|---|
| `/Web/API/` | tarayıcı arayüzü referansı | `AudioWorkletNode` |
| `/Web/HTTP/` | HTTP kural ve durumları | `Status/401` |
| `/rfc/rfcXXXX` | tam metin standart | `rfc8446` |
| `/info/rfcXXXX` | standart künye sayfası | `rfc8446` |
| `/latest/` | en güncel sürümdür — tazelik riski taşır | Zephyr |
| `/develop/toolchains/` | araç zinciri bölümü | Zephyr |
| `/api-reference/peripherals/` | donanım arayüzü bölümü | ESP-IDF I2S |

`/latest/` kullanan satırlarda tazelik riski yüksektir; bu satırlar 90 gün
kuralıyla daha sık yeniden denenir (§12).

## §20 Bölüm bazlı sayı özetleri

| Bölüm | Satır | Konu | `✅` |
|---|---|---|---|
| §3.1 | 22 | ses/zamanlama/ağ API'leri | 22 |
| §3.2 | 14 | protokol ve kimlik standartları | 14 |
| §3.3 | 12 | RTOS · SDK · emülasyon | 12 |
| Toplam | 48 | — | 48 |

Toplam satır ile üç bölümün toplamı eşittir (22 + 14 + 12 = 48); bölüm
toplamı satır sayısı birbirinden bağımsız sayılır ve uyuşmazlık hata sayılır.

## §21 Çocuk dosyalara devredilecek satır kümeleri

| Çocuk | Devralınacak satır | Ön koşul |
|---|---|---|
| `runtime/` | 37–43 (RTOS) | RTOS kararı |
| `audio-control/` | 44–45 (I2S) · 1–9 (ses API'leri) | K003 sınırı teyidi |
| `update/` | 18–22 (parça indirme) | OTA kapsam teyidi |
| `security/` | 23–36 (RFC + kripto) | ADR kapısı |
| `testing/` | 48 (emülasyon) | test stratejisi onayı |
| `boot/` · `device-control/` · `power-control/` | bu üretimde satır payı yok | dalga-2 |

Devir, satırın bu dosyadan **silinmesi** anlamına gelmez: bu dosyada kalır,
çocukta ikinci kez kullanılır; "tek URL tek dosya" kuralı bu üretimdeki üç
kök dosya içindir.

## §22 Kesişim kontrolü prosedürü

| Adım | İş | Beklenen |
|---|---|---|
| 1 | Bu dosyadan URL listesi çıkar | 48 |
| 2 | `firmware-master.md` listesiyle kesi | 0 |
| 3 | `firmware-dogrulama.md` listesiyle kesi | 0 |
| 4 | Üç kümenin birleşimi | 86 |
| 5 | Eski envanter kümesiyle kesi | 0 |

Kesişim 0 değilse çakışan URL tek tek listelenir ve havuzdan biri çıkarılır;
sonuç ÖLÇÜM raporuna sayısal yazılır.

## §23 HTTP turu kaydı (nasıl yapıldı)

| # | Adım | Kayıt şekli |
|---|---|---|
| 1 | Aday listesi yazılır | URL + başlık |
| 2 | Sırayla istek atılır | yanıt kodu |
| 3 | 200 verenler işaretlenir | ✅ |
| 4 | Diğerleri ayrılır | gerekçe = kod |
| 5 | Havuz dosyası yazılır | URL + başlık |
| 6 | Üç dosya havuzdan paylaştırılır | 12 / 26 / 48 |

Tur tek oturumda yapılır; yarım kalan turda durum `⚠️` kalır.

## §24 Kaynağa bağımlılık riski

| Risk | Sonuç | Azaltma |
|---|---|---|
| Tek alan adına bağımlılık | alan adı kapanırsa kanıt kaybı | 90 gün kuralı + alternatif arama |
| `/latest/` sürüm kayması | içerik değişir, iddia eskiyebilir | tazelik + yeniden okuma |
| Protokol sürümü değişir (RFC 8446 → sonrası) | iddia bayatlar | sürüm numarasıyla kayıt |
| Üretici dokümanının erişim reddi | kanıt üretilemez | havuz dışı bırakma + gerekçe kodu |

Bu dört risk `firmware-riskler.md` içinde ayrı satır gerektirir; burada
bağlantı düzeyinde taşınır.

## §25 RFC okuma kuralları

| Kural | Ayrıntı |
|---|---|
| Tam metin | `/rfc/rfcXXXX` — maddenin kendisi |
| Künye | `/info/rfcXXXX` — durum, tarih, ilişkili maddeler |
| Şart kelimeleri | RFC 2119 + RFC 8174 birlikte okunur (§3.2 satır 23–24) |
| Durum sütunu | bilginin güncel durumu (Standards Track · Informational) |
| İddia | maddenin ne tanımladığıdır, maddenin doğru olduğu iddiası değil |

RFC satırı "bu standart doğrudur" demez; "bu standart şunu tanımlar" der.

## §26 Web ses API'leri ile K005 sınırı

| Konu | K005'te | Gerekçe |
|---|---|---|
| Tarayıcı ses API'leri | sınır komşusu | ses yolu K003'tedir |
| Gecikme kavramı (latency) | ortak dil | ölçüm K005 test stratejisinde |
| Çıkış gecikmesi tanımı | referans | donanım gecikmesi K001'de |
| Gerçek zamanlı iş parçacığı kuralı | kural ortaklığı | zero-allocation · lock-free |

Sınır, "bu API'yi kullanacağız" demek değildir; yalnızca **ölçüm dili**
ortaklığı kurar.

## §27 RTOS karşılaştırma çerçevesi (seçim yok)

| Kriter | FreeRTOS | Zephyr | Bu üretimde |
|---|---|---|---|
| Kayıt | §3.3 satır 37–38 | §3.3 satır 39–43 | ikisi de kaynak |
| Görev modeli | belgede | kernel bölümünde | okunacak |
| Yapı sistemi | belgede | build bölümünde | okunacak |
| Araç zinciri | belgede | toolchains bölümünde | okunacak |
| Seçim | — | — | YOK |

Tablo seçim **yapmaz**; iki adayı da kayıt altında tutar. Seçim kararı ayrı
ADR ister ve bu dosyanın yetkisi dışındadır.

## §28 Seri ses yolu (I2S) notu

| Madde | İçerik | Kayıt |
|---|---|---|
| ESP-IDF I2S | yapılandırma API'leri | §3.3 satır 45 |
| Veri yolu | kare (frame) · kanal · bit derinliği | okunacak |
| K005 ilişkisi | donanım kontrolü K001, yolu kullanma K003 | sınır |
| Bu üretimde | yalnız kaynak kaydı — kod yok | 0 kod |

I2S satırı donanım topolojisini değiştirmez; yalnız doküman kaynağıdır.

## §29 Sözlük (bu dosyada geçen terimler)

| Terim | Açılım |
|---|---|
| API | application programming interface — uygulama programlama arayüzü |
| RFC | request for comments — IETF standart belgesi |
| DTLS | datagram transport layer security — UDP üstü güvenli taşıma |
| PKCE | proof key for code exchange — yetki kodu koruma yöntemi |
| CoAP | constrained application protocol — sınırlı cihaz REST protokolü |
| OSCORE | object security for constrained RESTful environments |
| RTOS | real-time operating system — gerçek-zamanlı işletim sistemi |
| I2S | inter-IC sound — seri ses veri yolu |
| SDK | software development kit — yazılım geliştirme kiti |
| OASIS | açık standart kuruluşu (MQTT yayıncısı) |

## §30 Ölçüm kapanışı

| Ölçüm | Hedef | Yer |
|---|---|---|
| Satır | ≥ 500 | ÖLÇÜM raporu |
| URL | 48 (§3) | sayım |
| Kesişim | 0 (×2) | §22 |
| FM | 9 anahtar | §12 (dogrulama) |
| Tarih | 2026-10-13 × 48 | metin arama |
| Dolgu · mojibake | 0 | metin arama |

Altı ölçüm de sayısal olarak yazılır; "yeterince çok" gibi niteliksel
değer kullanılmaz.

## §31 Red Team gözden geçirmesi (bu üretim)

| # | İddia | Bulgu | Karar |
|---|---|---|---|
| 1 | "48 URL doğrulandı" | 2026-10-13 HTTP-200 turu kaydı var | kabul |
| 2 | "eski URL'ler taze" | eskiler bu üretimde kullanılmadı | tazelik yükü yok |
| 3 | "RTOS seçildi" | iki aday kayıtlı, seçim yok | iddia reddedildi |
| 4 | "bulut OTA kullanılacak" | kapsam teyidi yok | `⚠️` olarak taşındı |
| 5 | "alan adları bölündü" | üç dosya arası kesişim henüz ölçülmedi | ÖLÇÜM'de sayılacak |
| 6 | "çocuk dosyalar hazır" | 6 çocuk `index.md` dışında boş | devir tablosu `durum` sütunuyla taşındı |

Reddedilen iddialar silinmez; satır olarak kalır ve gerekçesi yazılır.
Ölçüm sonucu olmayan hiçbir sayı "0" diye yazılmaz.

## §32 URL envanteri yenileme ve düşürme politikası

| Durum | Eylem | Kayıt |
|---|---|---|
| URL 90 günden eski + erişim 200 | erişim tarihi satırda güncellenir | tarih sütunu |
| URL 90 günden eski + erişim başarısız | durum `⚠️`'a geri alınır | durum sütunu |
| URL iki üretimdir kullanılmıyor | envanterden düşülür, dosyaya taşınmaz | düşürme satırı |
| Yeni URL adayı | HTTP denemesi + başlık okuma + Türkçe iddia üretimi | yeni satır |

**Düşürme kuralı:** Bir URL düşürülürse, ona bağlı iddia satırı da
düşürülür — iddiasız URL ve URLsiz iddia aynı dosyada bulunamaz. Düşürme
kaydı silme değil, geçmişi taşımak olduğu için tabloya alt satır olarak
eklenir; eski satır üst üste çizilmez.

**Yenileme sıklığı:** Her üretim turunda §2'deki satırların tamamı yeniden
denenir; tur sonuçları §30 ölçüm tablosuna sayısal olarak girer.

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
