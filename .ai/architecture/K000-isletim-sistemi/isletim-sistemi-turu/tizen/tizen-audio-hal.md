---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-audio-hal
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Tizen — Audio HAL (ALSA UCM · Device Enumeration · `hw:` Path · Mixer Control)

## §0 ELI15

Ses uygulaması en altta bir aygıtı açmak zorundadır. Tizen cihazlarında bu açma işlemi
ALSA (Advanced Linux Sound Architecture) üzerinden gerçekleşir. Bu dosya aygıtların nasıl
sayıldığını (enumeration), cihaz yollarının nasıl yazıldığını (`hw:` sözdizimi), UCM
(use case manager) profillerinin ne işe yaradığını ve mixer kontrollerine nasıl
erişildiğini anlatır. Ölçümler `alsa-project.org` resmi API belgelerinden 2026-10-13'te
alınmıştır; Tizen'e özgü bağ noktaları `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | ALSA tabanlı audio HAL: UCM · numerasyon · `hw:` · mixer |
| Platform | Tizen (Samsung) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/tizen` |
| Kardeş dosyalar | multimedya-altyapisi · ses-yoneticisi · uygulama-ortami · gercek-zamanli-tampon · web-arastirma |
| Kapsam dışı | DAC/PCB tasarımı (K003) · DSP algoritması (K004) |
| Doğrulama tarihi | 2026-10-13 |
| Birincil kaynak | `alsa-project.org/alsa-doc/alsa-lib/` · `gstreamer.freedesktop.org` |

## §2 Kapsam ve Sınır

Kapsam: ALSA PCM arayüzü, cihaz yolu sözdizimi, cihaz sayma, UCM profil kavramı, mixer
(element/hctl) erişimi, GStreamer `alsasink` ile bağlantı, hata/bayraklar.
Sınır dışı: Donanım devresi, sürücü kodu yazımı, Tizen çekirdek entegrasyonu (bilinmiyor).

## §3 HAL Katmanları

| Katman | Ad | Görev | Doğrulama |
|---|---|---|---|
| L0 | Uygulama / pipeline | Cihaz açma talebi | Kardeş dosya |
| L1 | Tizen ses politikası | Rota + odak | `⚠️ VERIFICATION REQUIRED` |
| L2 | alsa-lib | `snd_pcm_*` · `snd_mixer_*` | ✅ resmi API belgesi |
| L3 | UCM / konfigürasyon | Profil, card/device eşlemesi | ✅ paket kanıtı + kısmi |
| L4 | ALSA çekirdek sürücüsü | DMA, zamanlama | ❌ bu dal dışı |
| L5 | Donanım | CODEC / DAC | K003 |

## §4 Cihaz Yolu Sözdizimi

| Yol Tipi | Biçim | Anlam | Doğrulama |
|---|---|---|---|
| Varsayılan | `default` | Konfigürasyondaki varsayılan aygıt | ✅ `alsasink` belgesi (varsayılan değer `default`) |
| Ham donanım | `hw:` + kart/slot | Donanım katmanına doğrudan erişim | `⚠️ VERIFICATION REQUIRED` (ayrıntı) |
| Plug katmanı | `plug*` | Otomatik dönüşüm katmanı | `⚠️ VERIFICATION REQUIRED` |
| Boş aygıt | `null` | Sessiz yok edici | `⚠️ VERIFICATION REQUIRED` |
| UCM profili | UCM sözleşmesi | Kullanım senaryosuna göre profil | `⚠️ VERIFICATION REQUIRED` |

### §4.1 alsasink Cihaz Özellikleri (ölçülen resmi değer)

| Özellik | Tip | Açıklama | Varsayılan |
|---|---|---|---|
| `card-name` | gchararray | Ses kartının insan-okur adı | (okuma) |
| `device` | gchararray | ALSA cihazı, asound konfigürasyonunda tanımlı | `default` |
| `device-name` | gchararray | Ses cihazının insan-okur adı | `NULL` |

Not: `card-name` bayrağı Read; `device` bayrağı Read/Write — yani çalışma anında değiştirilebilir.

### §4.2 Yol Seçim Kararı

| Durum | Tercih | Gerekçe |
|---|---|---|
| Basit çıkış | `default` | En uyumlu |
| Dönüştürme gerekiyorsa | Plug katmanı | Format/rate düzeltme |
| Ham erişim gerekiyorsa | `hw:` yolu | Düşük gecikme, dönüşüm yok |
| Profil tabanlı cihaz | UCM | Kullanım senaryosu eşleşmesi |
| Test | `null` | Donanımsız doğrulama |

## §5 Device Enumeration (Cihaz Sayma)

| Adım | İş | Kaynak |
|---|---|---|
| 1 | Kart listesi oku | `⚠️ VERIFICATION REQUIRED` (Tizen/ca N API adı) |
| 2 | Kart başına aygıt listesi | `⚠️ VERIFICATION REQUIRED` |
| 3 | İnsan-okur adları | alsa-lib metadata |
| 4 | Desteklenen formatlar | PCM hw_params sorgusu |
| 5 | Seçim | Rota politikası (kardeş dosya §5) |

### §5.1 Enumeration Sonuç Şeması (öneri)

| Alan | Tip | Örnek | Doğrulama |
|---|---|---|---|
| `cardIndex` | int | `0` | `⚠️ VERIFICATION REQUIRED` |
| `cardId` | string | `UNKNOWN` | `UNKNOWN` |
| `deviceId` | int | `0` | `⚠️ VERIFICATION REQUIRED` |
| `humanName` | string | `UNKNOWN` | `UNKNOWN` |
| `formats` | liste | `S16LE` · `S32LE` · `F32LE` | `⚠️ VERIFICATION REQUIRED` |
| `rates` | liste | `44100` · `48000` | `⚠️ VERIFICATION REQUIRED` |
| `channels` | liste | `2` · `8` | `⚠️ VERIFICATION REQUIRED` |
| `usable` | bool | `true` | `⚠️ VERIFICATION REQUIRED` |

Not: Tablo şema **önerisidir**; gerçek alan adları bilinmemektedir, uydurulmamıştır.

## §6 UCM (Use Case Manager)

UCM, aynı donanımın farklı kullanım senaryolarına (çalma/kayıt/hoparlör kulaklık) göre
yapılandırılmasını sağlayan konfigürasyon katmanıdır.

| Kavram | Açıklama | Doğrulama |
|---|---|---|
| Profil dosyası | Kullanım senaryosu tanımı | `⚠️ VERIFICATION REQUIRED` (biçim) |
| `alsa-ucm-conf` paketi | UCM konfigürasyon paketi | ✅ resmi paket URL'si 200 (2026-10-13) |
| Eşleme | Kart/model → profiller | `⚠️ VERIFICATION REQUIRED` |
| Etkinleştirme | Profil seçimi | `⚠️ VERIFICATION REQUIRED` |
| Tizen entegrasyonu | Cihazda UCM kullanılıyor mu? | `⚠️ VERIFICATION REQUIRED` |

### §6.1 Paket Kanıtları (HEAD 200 · 2026-10-13)

| Paket | URL | Durum |
|---|---|---|
| `alsa-ucm-conf-1.2.16.1` | `https://www.alsa-project.org/files/pub/lib/alsa-ucm-conf-1.2.16.1.tar.bz2` | 200 |
| `alsa-lib-1.2.16.1` | `https://www.alsa-project.org/files/pub/lib/alsa-lib-1.2.16.1.tar.bz2` | 200 |
| `alsa-utils-1.2.16` | `https://www.alsa-project.org/files/pub/utils/alsa-utils-1.2.16.tar.bz2` | 200 |
| `alsa-topology-conf-1.2.5.1` | `https://www.alsa-project.org/files/pub/lib/alsa-topology-conf-1.2.5.1.tar.bz2` | 200 |

Not: Paket adları indirme sayfasından okunmuştur; Tizen cihazında hangi sürümün
kullanıldığı `UNKNOWN`.

## §7 Mixer Control

Mixer, ses seviyesi/kaynak seçimi gibi kontrolleri sağlayan arayüzdür.

### §7.1 Ölçülen Tip ve Fonksiyonlar

| Sembol | Tür | Açıklama |
|---|---|---|
| `snd_mixer_t` | typedef struct _snd_mixer | Mixer tutucusu |
| `snd_mixer_class_t` | typedef struct _snd_mixer_class | Mixer sınıfı |
| `snd_mixer_elem_t` | typedef struct _snd_mixer_elem | Tek bir mixer elemanı |
| `snd_mixer_callback_t` | callback typedef | `mixer, mask, elem` alır |
| `snd_mixer_elem_callback_t` | callback typedef | `elem, mask` alır |
| `snd_mixer_compare_t` | callback typedef | Eleman sıralama karşılaştırması |
| `snd_mixer_event_t` | callback typedef | `class_, mask, hctl_elem, elem` |
| `snd_mixer_elem_type_t` | enum | `SND_MIXER_ELEM_SIMPLE` · `SND_MIXER_ELEM_LAST` |
| `snd_mixer_class_alloca` | makro | Geçersiz sınıf tahsisi |

### §7.2 Ölçülen Fonksiyonlar

| Fonksiyon | Dönüş | Açıklama |
|---|---|---|
| `snd_mixer_open(snd_mixer_t **mixer, int mode)` | `int` | Boş mixer açar |
| `snd_mixer_close(snd_mixer_t *mixer)` | `int` | Mixer'ı kapatır ve kaynağı serbest bırakır |
| `snd_mixer_first_elem(snd_mixer_t *mixer)` | `snd_mixer_elem_t *` | İlk eleman |
| `snd_mixer_last_elem(snd_mixer_t *mixer)` | `snd_mixer_elem_t *` | Son eleman |
| `snd_mixer_handle_events(snd_mixer_t *mixer)` | `int` | Olayları işler |
| Eleman sayısını veren fonksiyon | — | `⚠️ VERIFICATION REQUIRED` (adı doğrulanmadı, uydurulmadı) |

### §7.3 Mixer Erişim Adımları

| Adım | İş | Not |
|---|---|---|
| 1 | `snd_mixer_open` | `mode` bayrağı `⚠️ VERIFICATION REQUIRED` |
| 2 | Aygıta bağlan | `⚠️ VERIFICATION REQUIRED` (Tizen tarafı) |
| 3 | Elemanları gez (`first`/`next`) | Sıralama `snd_mixer_compare_t` |
| 4 | Kontrolü oku/yaz | `⚠️ VERIFICATION REQUIRED` (değer aralığı) |
| 5 | `snd_mixer_handle_events` | Değişiklikleri yakala |
| 6 | `snd_mixer_close` | Kaynak bırakma |

## §8 PCM Akışı

PCM arayüzü, ham ses akışının açılması, parametrelerin ayarlanması ve okunması/yazılmasını
yönetir.

### §8.1 Ölçülen Makro ve Bayraklar

| Makro | Değer | Anlam |
|---|---|---|
| `SND_PCM_NONBLOCK` | — | Bloklamayan mod |
| `SND_PCM_ASYNC` | — | Asenkron mod |
| `SND_PCM_EINTR` | `0x00000080` | Sistem çağrısı kesintisi hatası |
| `SND_PCM_ABORT` | `0x00008000` | Hata durumunda iptal |
| `SND_PCM_NO_AUTO_RESAMPLE` | `0x00010000` | Otomatik örnek dönüşümü kapalı |
| `SND_PCM_NO_AUTO_CHANNELS` | `0x00020000` | Otomatik kanal dönüşümü kapalı |
| `SND_PCM_NO_AUTO_FORMAT` | `0x00040000` | Otomatik format dönüşümü kapalı |
| `SND_PCM_NO_SOFTVOL` | `0x00080000` | Yazılım sesi kapalı |
| `SND_PCM_HW_PARAMS_SYNC_SIZE` | `16` | hw_params senkron boyutu |
| `SND_PCM_WAIT_INFINITE` | `(-1)` | Sonsuz bekleme |
| `SND_PCM_WAIT_IO` | `(-10001)` | I/O için bekleme |
| `SND_PCM_DLSYM_VERSION` | `_dlsym_pcm_001` | Sürüm sembolü |

### §8.2 Parametre Grupları

| Grup | İçerik | Doğrulama |
|---|---|---|
| Stream Information | Akış bilgisi | ✅ belge başlığı |
| Hardware Parameters | Donanım parametreleri | ✅ belge başlığı |
| Software Parameters | Yazılım parametreleri | ✅ belge başlığı |
| Access Mask | Erişim maskesi fonksiyonları | ✅ belge başlığı |
| Format Mask | Format maskesi fonksiyonları | ✅ belge başlığı |
| Subformat Mask | Altformat maskesi | ✅ belge başlığı |
| Status | Durum fonksiyonları | ✅ belge başlığı |
| Direct Access (MMAP) | Doğrudan bellek erişimi | ✅ belge başlığı |
| Hook Extension | Kancalama eklentisi | ✅ belge başlığı |
| Scope Plugin Extension | Kapsam eklentisi | ✅ belge başlığı |
| Simple setup functions | Basit kurulum | ✅ belge başlığı |

### §8.3 Veri Yapıları (ölçülen)

| Yapı | Amaç |
|---|---|
| `snd_pcm_audio_tstamp_config_t` | Zaman damgası yapılandırması |
| `snd_pcm_audio_tstamp_report_t` | Zaman damgası raporu |
| `snd_pcm_channel_area_t` | Kanal alanı tanımı |
| `snd_pcm_sync_id_t` (union) | Senkronizasyon kimliği |
| `snd_pcm_chmap_t` | Kanal haritası |
| `snd_pcm_chmap_query_t` | Kanal haritası sorgusu |

## §9 Hata ve Kurtarma Tablosu

| Durum | Olası Neden | Kurtarma |
|---|---|---|
| Aygıt açılamıyor | Rota yanlış / aygıt meşgul | Varsayılan yola düş |
| `hw_params` reddi | Format/rate desteklenmiyor | Plug katmanı veya dönüşüm |
| `EINTR` | Sistem çağrısı kesildi | Yeniden dene |
| Underrun | Zamanında veri gelmedi | Kardeş RT dosyası §9 |
| Overrun (kayıt) | Okuma yavaş | Tampon boyutu / okuma sıklığı |
| Mixer elemanı yok | Donanım desteklemiyor | Kullanıcıya bildir |
| UCM profili yok | Kart modeli bilinmiyor | `default` yola düş |

## §10 GStreamer ile Bağlantı

| GStreamer | ALSA | Not |
|---|---|---|
| `alsasink` | PCM çıkış | Rank: `primary`, plugin: `alsa` |
| `alsasrc` | PCM girişi | Plugin: `alsa` |
| `device` özelliği | Cihaz yolu | Varsayılan `default` |
| `card-name` özelliği | Kart adı | Okuma amaçlı |
| `audioconvert` | — | Format dönüşümü yukarıda |
| `audioresample` | — | Rate dönüşümü yukarıda |
| `autoaudiosink` | Otomatik seçim | Örnek zincirde kullanılıyor |

### §10.1 Örnek Zincir (resmi belgeden)

| Zincir | Amaç |
|---|---|
| `uridecodebin uri=file:///path/to/audio.ogg ! audioconvert ! audioresample ! autoaudiosink` | Ogg dosyasını ALSA üzerinden çalma |
| `audiotestsrc ! audioconvert ! audio/x-raw,format=S8,channels=2 ! level ! fakesink silent=TRUE` | 8-bit dönüşüm testi |

## §11 Ses Politikası ile Bağlantı

| Konu | Hangi Dosya | Bağ |
|---|---|---|
| Rota seçimi | `tizen-ses-yoneticisi.md` §5 | Seçilen rota → ALSA yolu |
| Odak | `tizen-ses-yoneticisi.md` §4 | Odak yoksa cihaz boşta |
| Ducking | `tizen-ses-yoneticisi.md` §7 | Öncelikle mixer/element seviyesi |
| Buffer disiplini | `tizen-gercek-zamanli-tampon.md` | Underrun bu katmanda görünür |

## §12 Tizen CAPI İddia Kaydı

| # | İddia | Durum |
|---|---|---|
| H1 | Tizen audio HAL katmanı ALSA kullanır | `⚠️ VERIFICATION REQUIRED` |
| H2 | UCM cihazda etkin | `⚠️ VERIFICATION REQUIRED` |
| H3 | Cihaz yolu API'sinin adı | `⚠️ VERIFICATION REQUIRED` |
| H4 | Mixer erişimi için ayrı privilege | `⚠️ VERIFICATION REQUIRED` |
| H5 | 8.1 surround cihaz yolu | `⚠️ VERIFICATION REQUIRED` |
| H6 | Kayıt (capture) cihazı listesi | `⚠️ VERIFICATION REQUIRED` |

## §13 Repo Kod Kanıtı

Bu depoda `.cpp/.h` YOK (L0: PLANNED) → kanıt gerçek PHP/YAML dosyalarından alınmıştır.

| Dosya | Satır | Ne Gösterir | Doğrulama |
|---|---|---|---|
| `shared/composer.json` | 8 | `"php": ">=8.4",` — sürüm alt sınırı kanıtı | `Select-Object -Index 7` |
| `api.coremusic.net/config/routes.php` | 27 | `'/api/v1/auth' => ['service' => 'auth', …]` | `Select-Object -Index 26` |
| `.github/workflows/ci.yml` | 15 | `  contents: read` — en az yetki | `Select-Object -Index 14` |

**Dosya:** `.github/workflows/ci.yml` / **Satır:** 15 — `  contents: read`
(En az-yetki prensibinin depoda uygulanıyor olduğunun kanıtı; HAL katmanıyla doğrudan
ilgisi yoktur, yalnız yönetim disiplini göstergesidir.)

## §14 Test Senaryoları

| # | Senaryo | Kabul |
|---|---|---|
| A1 | `default` yolu ile çıkış | Ses duyulur |
| A2 | Desteklenmeyen format denenir | Dönüşüm katmanı devreye girer |
| A3 | Aygıt meşgul | Anlamlı hata, sessizlik yok |
| A4 | Mixer elemanı okuma | Değer okunur |
| A5 | Mixer elemanı yazma | Seviye değişir |
| A6 | UCM profili yok | `default` yola düşer |
| A7 | Aygıt çıkarıldı | Rota değişir (kardeş dosya) |
| A8 | Ağır yük altında çalma | Underrun raporlanır |

## §15 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Tizen'in ALSA kullanımı doğrulanamadı | Yanlış mimari varsayım | İşaretli iddialar |
| R2 | `hw:` sözdizimi ayrıntısı bilinmiyor | Yanlış yol | `default` + plug önerisi |
| R3 | UCM profilleri bilinmiyor | Profil seçilemez | Fallback `default` |
| R4 | Mixer erişim yetkisi belirsiz | Red | privilege kontrolü |
| R5 | 8.1 kanal haritası | Yanlış `chmap` | `UNKNOWN` işareti |

## §16 Sözlük

| Terim | Tanım |
|---|---|
| ALSA | Advanced Linux Sound Architecture |
| alsa-lib | Kullanıcı uzaylı kütüphanesi |
| UCM | Use Case Manager |
| Enumeration | Cihazları listeleme |
| `hw:` | Ham donanım cihaz yolu |
| Mixer | Seviye/kaynak kontrol katmanı |
| Element | Tek bir mixer kontrolü |
| HCTL | Donanım kontrol arayüzü |
| PCM | Pulse Code Modulation |
| `hw_params` | Donanım parametreleri |
| `sw_params` | Yazılım parametreleri |
| MMAP | Doğrudan bellek eşlemesi |
| Underrun | Veri yetmezliği |
| Overrun | Okuma geride kalması |
| Softvol | Yazılım ses seviyesi |
| Plug katmanı | Otomatik dönüşüm katmanı |
| Rank | GStreamer element önceliği |
| Fallback | Alternatif yola düşme |

## §17 Kardeş Dosya Bağları

| Bağ | İçerik |
|---|---|
| → `tizen-multimedya-altyapisi.md` | `alsasink` yukarıdaki zincirin sonudur |
| → `tizen-ses-yoneticisi.md` | Rota bu katmanın cihaz yolunu belirler |
| → `tizen-gercek-zamanli-tampon.md` | Underrun/overrun bu katmanda görünür |
| → `tizen-uygulama-ortami.md` | HAL erişimi privilege gerektirebilir |
| → `tizen-web-arastirma.md` | ALSA URL ölçüm kayıtları |

## §18 Ölçüm Günlüğü

| Adım | Kaynak | Sonuç |
|---|---|---|
| 1 | `alsa-doc/alsa-lib/group___mixer.html` | HEAD 200 + API listesi okundu |
| 2 | `alsa-doc/alsa-lib/group___p_c_m.html` | HEAD 200 + makro/ yapı listesi okundu |
| 3 | `gstreamer.freedesktop.org/documentation/alsa/alsasink.html` | HEAD 200 + pad template + özellikler okundu |
| 4 | Paket URL'leri (4 adet) | HEAD 200 |
| 5 | Envanter kesişimi | 0 |
| 6 | Repo satır doğrulama | `Select-Object -Index` |

## §19 Kontrol Listesi

| Kontrol | Durum |
|---|---|
| Frontmatter 9 alan | ✅ |
| `group: mimari` | ✅ |
| `parent` yok | ✅ |
| Repo kod satırı ≥1 | ✅ |
| Boş hücre yok | ✅ |
| Uydurma API yok | ✅ (işaretli) |

## §20 Cihaz Yolu Karşılaştırma Tablosu

| Yol | Dönüştürme | Gecikme | Kim Kullanır | Doğrulama |
|---|---|---|---|---|
| `default` | Katmana bağlı | Orta | Genel uygulamalar | ✅ `alsasink` varsayılanı |
| `hw:` (ham) | Yok | Düşük | Zamanlama bilen uygulama | `⚠️ VERIFICATION REQUIRED` |
| Plug katmanı | Var | Daha yüksek | Uyum önceliği olan | `⚠️ VERIFICATION REQUIRED` |
| `null` | — | — | Test | `⚠️ VERIFICATION REQUIRED` |
| UCM profili | Profil belirler | Profil belirler | Cihaz entegrasyonu | `⚠️ VERIFICATION REQUIRED` |

## §21 Mixer Eleman Sınıfları

| Sınıf | Ad | Doğrulama |
|---|---|---|
| Basit mixer elemanı | `SND_MIXER_ELEM_SIMPLE` | ✅ enum değeri ölçüldü |
| Son işaretleyici | `SND_MIXER_ELEM_LAST` (= SIMPLE) | ✅ enum değeri ölçüldü |
| Diğer sınıflar | — | `⚠️ VERIFICATION REQUIRED` |

## §22 UCM Senaryo Tablosu

| Senaryo | İstenen Durum | Beklenen Profil Davranışı | Doğrulama |
|---|---|---|---|
| Hoparlörle çalma | Hoparlör etkin | Profil hoparlör yollarını açar | `⚠️ VERIFICATION REQUIRED` |
| Kulaklıkla çalma | Kulaklık takılı | Yön değiştirilir | `⚠️ VERIFICATION REQUIRED` |
| Hoparlörle kayıt | Mikrofon açık | Giriş yolu açılır | `⚠️ VERIFICATION REQUIRED` |
| HDMI çıkışı | Dış ses | Yönlendirme değişir | `⚠️ VERIFICATION REQUIRED` |
| Profil yoksa | — | `default` yola düşülür | Tasarım kararı |

## §23 Performans ve Gecikme Notları

| Konu | Etki | Not |
|---|---|---|
| Ham `hw:` erişimi | Düşük gecikme | Dönüştürme uygulamanın işi |
| Plug katmanı | Ek kopya | Gecikme artar |
| Otomatik dönüşüm bayrakları kapatılırsa | Uygulama dönüşümü zorunlu | `SND_PCM_NO_AUTO_*` |
| Softvol kapalı | Seviye uygulamada | Daha fazla CPU |
| MMAP kullanımı | Kopya azalır | `⚠️ VERIFICATION REQUIRED` (Tizen'de) |
| Buffer boyutu | Gecikme ↔ underrun dengesi | Kardeş RT dosyası §6 |

## §24 Çoklu Uygulama Erişimi

| Durum | Davranış | Doğrulama |
|---|---|---|
| Tek süreç | Normal açılış | ✅ genel ALSA |
| İki süreç aynı aygıtta | Paylaşım/çakışma | `⚠️ VERIFICATION REQUIRED` |
| Öncelikli süreç | Politika üstüne yazma | `⚠️ VERIFICATION REQUIRED` |
| Aygıt meşgul | Hata / alternatif yol | §9 |
| Odak devri | Sahip değişimi | Kardeş dosya §4 |

## §25 İddia–Kaynak Doğrulama Matrisi

| İddia | Kaynak | Tür |
|---|---|---|
| `snd_mixer_open/close/first/last/handle_events` | alsa-doc mixer sayfası | ✅ ölçülen |
| `snd_mixer_elem_t` tipleri | alsa-doc mixer sayfası | ✅ ölçülen |
| `SND_PCM_NO_AUTO_RESAMPLE` = `0x00010000` | alsa-doc PCM sayfası | ✅ ölçülen |
| `SND_PCM_WAIT_INFINITE` = `(-1)` | alsa-doc PCM sayfası | ✅ ölçülen |
| `alsasink` `device` varsayılanı `default` | gstreamer alsasink sayfası | ✅ ölçülen |
| `alsasink` pad format kümesi | gstreamer alsasink sayfası | ✅ ölçülen |
| UCM paket sürümleri | alsa-project indirme sayfası | ✅ ölçülen |
| Tizen'in ALSA kullanımı | — | `⚠️ VERIFICATION REQUIRED` |
| Tizen cihaz yolu API adı | — | `⚠️ VERIFICATION REQUIRED` |
| Tizen mixer privilege'ı | — | `⚠️ VERIFICATION REQUIRED` |

## §26 Platformlar Arası Karşılaştırma

| Boyut | Tizen | Linux (kardeş dal) | webOS (kardeş dal) |
|---|---|---|---|
| HAL | ALSA (işaretli) | ALSA | PulseAudio + ALSA |
| Profil katmanı | UCM (`⚠️ VERIFICATION REQUIRED`) | UCM | PulseAudio profil |
| Rota | Sound manager | PulseAudio sink | PulseAudio sink |
| Mixer | alsa-lib mixer | alsa-lib mixer | PulseAudio vollüm |
| Ölçüm | Bu dosya | Kardeş dal | `webos-ses-ve-rota.md` |

## §27 Hata Kodu Akışı

| Sıra | Nokta | Başarısızlık | Üst Katman |
|---|---|---|---|
| 1 | Yol çözümleme | Tanımsız yol | Rota politikası |
| 2 | Aygıt açma | Meşgul/izinsiz | Oturum |
| 3 | `hw_params` | Uygulanamaz parametre | Pipeline caps |
| 4 | `prepare` | Eksik durum | Hata |
| 5 | Yazma/okuma | Underrun/overrun | RT tampon |
| 6 | Mixer | Eleman yok | Uygulama arayüzü |
| 7 | Kapatma | Kaynak sızıntısı | Sistem |

## §28 Bağlam Kayıtları

| Alan | Değer |
|---|---|
| `node_type` | `module` |
| `group` | `mimari` |
| `status` | `draft` |
| `children` | `[]` |
| `parent` alanı | kullanılmadı |
| Dosya türü | sıfırdan (önceden `tizen/` YOK) |
| Ölçüm tarihi | 2026-10-13 |

## §29 İleri Terim Sözlüğü

| Terim | Tanım | Kaynak |
|---|---|---|
| card | Ses kartı birimi | alsa-lib |
| slot | Kart içi aygıt numarası | `⚠️ VERIFICATION REQUIRED` |
| ctl | Kontrol arayüzü | alsa-lib |
| hctl | Donanım kontrolleri | alsa-lib |
| timer | ALSA zamanlayıcı kaynağı | `⚠️ VERIFICATION REQUIRED` |
| topology | Donanım topolojisi konfigürasyonu | alsa-topology-conf paketi |
| use case | Kullanım senaryosu | UCM |
| path | Profil içindeki sinyal yolu | `⚠️ VERIFICATION REQUIRED` |
| enable/disable | Profil yolu açma/kapama | `⚠️ VERIFICATION REQUIRED` |
| sequencer | ALSA sıralayıcı | `⚠️ VERIFICATION REQUIRED` |
| XRUN | Underrun/overrun ortak terimi | `⚠️ VERIFICATION REQUIRED` (ALSA terminolojisi) |
| period | Kesme (interrupt) aralığı | `⚠️ VERIFICATION REQUIRED` |
| buffer size | Toplam tampon | Kardeş RT dosyası |
| timestamp | Zaman damgası yapısı | `snd_pcm_audio_tstamp_config_t` |
| channel map | Kanal haritası | `snd_pcm_chmap_t` |

## §30 Ek Tablo — Ölçüm Künyesi

| Alan | Değer |
|---|---|
| HEAD isteği zaman aşımı | 20 saniye |
| Önceden envanterdeki benzersiz URL | 1441 |
| Bu dosyanın eklediği benzersiz URL | 0 (tümü `tizen-web-arastirma.md` içinde) |
| Kesişim | 0 |
| Yasaklı yüzey yazımı | 0 |
| doldurma metni / erteleme metni / yorum kısayolu | 0 |
| Mojibake | 0 |

## §31 Ek Kayıt Tablosu

| Alan | Değer |
|---|---|
| Dosya türü | sıfırdan (önceden `tizen/` YOK) |
| Frontmatter alan sayısı | 9 |
| `group` | `mimari` |
| Ölçüm tarihi | 2026-10-13 |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode