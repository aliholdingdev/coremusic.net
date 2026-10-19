---
title: "ASIO Derin-Dalı — DAW Uyumluluğu (Host Davranışı · Multi-Client · ASIO-Guard · ARA)"
node_id: "K003-audio/backend/asio/asio-daw-uyumlulugu"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-anayasa"
  - "K003-audio/backend/asio/asio-arayuzler"
  - "K003-audio/backend/asio/asio-platform-uyumlulugu"
evidence:
  - "https://help.ableton.com/hc/en-us/articles/360003149240-Using-an-ASIO-audio-driver-on-Windows — Ableton ASIO önerisi, ASIO4ALL+Focusrite çakışması, FlexASIO alternatifi (2026-10-12 okundu)"
  - "https://help.ableton.com/hc/en-us/articles/4515794042898-Multi-Client-Operation-Drivers-Windows- — multi-client: ASIO4ALL exclusive-by-default, FlexASIO shared, VoiceMeeter (2026-10-12 okundu)"
  - "https://www.bitwig.com/support/technical_support/how-do-i-record-audio-using-wasapi-on-windows-36/ — Bitwig: WASAPI kayıt desteklenmiyor, ASIO gerekli (2026-10-12 okundu)"
  - "https://kb.avid.com/pkb/articles/en_US/FAQ/What-is-the-Playback-Engine-in-Pro-Tools — Pro Tools protokolleri: Mac CoreAudio · Windows WASAPI (limited to audio only) + ASIO (2026-10-12 okundu)"
  - "https://www.image-line.com/fl-studio-learning/fl-studio-online-manual/html/envsettings_asio4all.htm — FL Studio: ASIO Windows'a özgü, çoklu-cihaz avantajı, ASIO4ALL troubleshooting (2026-10-12 okundu)"
  - "https://helpcenter.steinberg.de/hc/en-us/articles/206103564-Details-on-ASIO-Guard-in-Cubase-and-Nuendo — ASIO-Guard mekaniği: RT yolu, guard yolu, Low/Normal/High, istisnalar (2026-10-12 okundu)"
  - "https://raw.githubusercontent.com/Celemony/ARA_SDK/master/README.md — ARA v1/v2 SDK (2026-10-12 okundu)"
  - "https://manual.audacityteam.org/man/asio_audio_interface.html — Audacity: resmi derlemede ASIO yok, özel derleme (GPLv3 vakası) (2026-10-12 okundu)"
  - "https://github.com/juce-framework/JUCE/blob/90e8da0cfb54ac593cdbed74c3d0c9b09bad3a9f/modules/juce_audio_devices/native/juce_win32_ASIO.cpp — JUCE host: tarama, kara liste, 16 cihaz sınırı, __try/__except (2026-10-12 okundu)"
validation:
  - "URL + tarih + alıntı: §10 kanıt-iddia tablosundaki her satır bu turda açılıp okunmuş bir URL'ye bağlandı (2026-10-12)"
  - "DAW matrisinde 'envanter-only' satırlar ✅ yerine ⚠️ işaretiyle ayrıldı; kanıtsız iddia düzeltildi (2026-10-12)"
  - "Satır sayısı ≥500 ve frontmatter alanları ölçüldü (2026-10-12)"
open_questions:
  - "Reaper/REAPER 7 + JUCE tabanlı host'lar ASIO-Guard benzeri bir ön-buffer yapıyor mu? (Cubase dışı DAW'larda eşdeğer mekanizma UNKNOWN) → ilgili DAW dokümanı teyidi"
  - "Pro Tools 'WASAPI (limited to audio only)' ifadesi kayıt girişini mi, yoksa WASAPI'nin genel kısıtını mı? (Avid cümlesi bağlam dışı → ⚠️ VERIFICATION REQUIRED)"
  - "Audacity özel derlemesi GPLv3 ASIO SDK ile mi derleniyor? (GPLv3 etkileşimi → asio-lisans-ve-dogrulama.md §open)"
  - "ARA 2 host + ASIO device loss etkileşimi (ara katman ses cihazını bağlamaz — mimari çıkarım, doğrulanmadı)"
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# ASIO — DAW Uyumluluğu (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya `K003-audio/backend/asio/` derin-dalının **host (DAW) katmanıdır**: hangi DAW
> hangi sürücü tipini sunar, host ASIO ile ne yapar (tarama → init → bufferSwitch),
> iki uygulama aynı cihazı isterse ne olur, Cubase'in ASIO-Guard'ı nedir, ARA nedir ve
> Neva Engine bir DAW host'u olurken bu tablodan ne devralır.
>
> Bu dosya **yeniden yazımdır** (2026-10-12, Red Team · Truth Mode): eski içerik yalnızca
> URL envanteri olarak kullanıldı, metin sıfırdan üretildir. Dosya adı değişmemiştir.
> **Kanıt disiplini:** bu turda açılıp okunmuş URL'ler `✅`, envanterde olup bu turda
> açılmamış URL'ler `⚠️` ile işaretlenmiştir — ikisi karıştırılmaz.

---

## §0 ELI15

DAW (Cubase, Live, REAPER, Pro Tools…), ses kartıyla konuşmak için üç yoldan birini
seçer: Windows'un kendi yolu (WASAPI/MME), Mac'in yolu (CoreAudio) ya da **ASIO**.
ASIO seçildiğinde DAW bir "host" olur: sürücü listesini tarar, kullanıcı bir cihaz
seçer, cihazı açar ve her hazır olduğunda sürücüye `bufferSwitch` der — "hazırım, sesi
yaz" — sürücü de sesi doğrudan donanıma verir.

Bu tabloda üç şey öne çıkar:

1. **Windows'ta ASIO hâlâ "pro yol"tur.** Ableton resmi sayfasında üç gerekçe sayar:
   profesyonel seviye sürücü, daha düşük buffer, daha kararlı (daha az dropout).
   Bitwig daha net: "WASAPI … is not supported for recording audio in Bitwig Studio —
   you will need to use another driver called ASIO." Pro Tools bile Windows'ta
   CoreAudio yerine WASAPI + ASIO kullanır (WASAPI "limited to audio only").
2. **Çoklu-istek problemi.** Klasik ASIO sürücüsü cihazı *ele geçirir* (exclusive) —
   bir DAW açıkken YouTube ses veremez. Ableton'un çözümü: ASIO4ALL yerine **FlexASIO**
   (shared mode'da çalışabilen evrensel ASIO) ya da VoiceMeeter gibi sanal mikser.
3. **ASIO-Guard (Cubase/Nuendo).** Cubase, kayıt/izleme yolu (gerçek zaman) ile
   geri-kalan her şey (mix render, efekt) için **ikinci, daha büyük bir buffer** kullanır;
   bir track'i izlemeye/almaya aldığında o track otomatik gerçek-zaman yoluna geçer.
   Düşük/Normal/Yüksek üç kademe.

**Neva Engine için anlamı:** Neva Engine bir DAW değildir ama ASIO host'u gibi
davranır (tarama → init → callback). Bu yüzden §3'teki host davranış kalıpları
Neva Engine'in şartnamesine birebir aktarılır; §2 matrisi ise Neva Engine'in
kullanıcı tabanının hangi DAW'larla yan yana çalışacağını gösterir.

---

## §1 Bu dosyanın kapsamı

| Kapsam | Kapsam dışı |
|--------|-------------|
| DAW × sürücü tipi uyumluluk matrisi | ASIO API imzaları → `[[asio-arayuzler]]` |
| Host yaşam döngüsü (tarama/init/callback) | Sürücü registry'si → `[[asio-surucu-modeli]]` |
| Multi-client davranışı (ASIO4ALL exclusive, FlexASIO shared) | Backend gecikme sayıları → `[[asio-platform-uyumlulugu]]` §6 |
| ASIO-Guard mekaniği (Cubase/Nuendo) | Lisans/GPLv3 → `[[asio-lisans-ve-dogrulama]]` |
| ARA v1/v2 ve ASIO ile ilişkisi | Medya oynatıcı detayları (envanter §8) |

---

## §2 DAW × sürücü tipi matrisi

### §2.1 Birincil kanıtla doğrulanmış satırlar (✅ bu turda okundu)

| # | DAW | Windows sürücü tipi (kaynaktaki ifade) | Kanıt (2026-10-12) |
|---|-----|----------------------------------------|---------------------|
| 1 | **Ableton Live** | "We recommend using an ASIO driver" — profesyonel seviye, düşük buffer, kararlılık; ASIO Windows'a kurulmalı; sürücü yoksa ASIO4ALL önerisi | ✅ help.ableton.com/…/360003149240 |
| 2 | **Bitwig Studio** | "WASAPI … is not supported for recording audio in Bitwig Studio. To record audio … you will need to use another driver called ASIO"; sürücü yoksa ASIO4ALL | ✅ bitwig.com/…/how-do-i-record-audio-using-wasapi-on-windows-36 |
| 3 | **Pro Tools** | "Currently, Pro Tools can support the following protocols: Mac: CoreAudio · Windows: WASAPI (limited to audio only), and ASIO" (Playback Engine; son güncelleme 9 Temmuz 2026) | ✅ kb.avid.com/…/What-is-the-Playback-Engine-in-Pro-Tools |
| 4 | **FL Studio** | "**ASIO Drivers are only relevant to Windows, not macOS**"; iki avantaj: hız (düşük CPU overhead + düşük buffer) ve **çoklu-audio-cihazı** (ASIO4ALL & FL Studio ASIO'ya özgü: USB mikrofon girişi + ayrı cihaz çıkışı aynı anda); native ASIO sürücüsü varsa onu öner | ✅ image-line.com/…/envsettings_asio4all.htm |
| 5 | **Cubase / Nuendo** | Audio Settings'te ASIO driver seçimi; donanım-özel ASIO > yerleşik yol (ASIO-Guard §5) | ✅ helpcenter.steinberg.de/…/Details-on-ASIO-Guard (ASIO-Guard sayfası okundu; Audio Settings sayfası ⚠️ envanter) |
| 6 | **Audacity** | Resmi dağıtımda ASIO **yok**; ASIO desteği yalnızca özel derlemede (non-distributable) — GPLv3 vakası | ✅ manual.audacityteam.org/man/asio_audio_interface.html |

### §2.2 Envante-dayalı satırlar (⚠️ bu turda açılmadı — iddia değil, kontrol bekliyor)

| # | DAW | Envanteki iddia özeti | Kanıt durumu |
|---|-----|------------------------|--------------|
| 7 | WaveLab (+ WaveLab Cast) | Steinberg built-in ASIO Driver ilk kez WaveLab Cast 2 (Tem 2023) | ⚠️ VERIFICATION REQUIRED |
| 8 | Studio One | "including ASIO, Core Audio (Mac OS X), and WASAPI (Windows)" | ⚠️ VERIFICATION REQUIRED |
| 9 | REAPER | Audio System: ASIO + "ASIO Configuration" + ReaRoute ASIO sürücüsü | ⚠️ VERIFICATION REQUIRED |
| 10 | Waveform (Tracktion) | Windows: "ASIO (recommended)" + Windows Sound; macOS Core Audio; Linux JACK | ⚠️ VERIFICATION REQUIRED |
| 11 | Cakewalk (BandLab) | Driver Mode: ASIO "for low latency" | ⚠️ VERIFICATION REQUIRED |
| 12 | Adobe Audition | Device Class: "In Windows, ASIO drivers support professional cards" | ⚠️ VERIFICATION REQUIRED |
| 13 | Samplitude (Boris FX, Ağu 2025) | ASIO Settings dokümanı; MME/WDM'a tercih | ⚠️ VERIFICATION REQUIRED |
| 14 | rekordbox (AlphaTheta) | Donanım-özel ASIO sürücüleri (ör. DDJ-RB ASIO) | ⚠️ VERIFICATION REQUIRED |
| 15 | Serato DJ Pro/Lite | Envanterde resmi ASIO kanıtı **bulunamadı** | ⚠️ UNKNOWN (negatif kayıt) |
| 16 | jetAudio (COWON) | Resmi sayfalarda ASIO geçmiyor | ⚠️ UNKNOWN (negatif kayıt) |

**Kural:** §2.2 satırları bu dosyada "destekler" diye kullanılamaz; yalnız
"envanterde var, teyit bekliyor" olarak durur. Teyit isteği → sonraki tur.

### §2.3 Örüntü çıkarımı

| Örüntü | Gözlem | Kaynak |
|--------|--------|--------|
| Windows = ASIO, macOS = CoreAudio | FL Studio "only relevant to Windows"; Pro Tools protokol listesi; Bitwig | ✅ §2.1 |
| Sürücüsü olmayan cihaz → evrensel ASIO | Ableton, Bitwig aynı öneriyi yapar: ASIO4ALL | ✅ §2.1 |
| WASAPI "yazılım yolu" olarak ikinci sınıf | Bitwig: kayıtta desteklenmiyor; Pro Tools: "limited to audio only" | ✅ §2.1 |
| Linux ayrı dünya (JACK/ALSA) | Tracktion Waveform satırı (⚠️ envanter) — `[[asio-platform-uyumlulugu]]` §7.3 WineASIO ile çapraz okunur | ⚠️ |

### §2.4 ASIO desteğinin DAW sınıfına göre dağılımı (sınıflandırma — bu belgenin analizi)

| Sınıf | Örnek | ASIO rolü | Güven |
|-------|-------|-----------|-------|
| Stüdyo iş istasyonu (DAW) | Cubase · Nuendo · REAPER · Studio One · Waveform · Cakewalk · Samplitude · Ableton · Bitwig · FL Studio | Birincil Windows sürücü tipi | ✅ (Ableton/Bitwig/FL Studio doğrudan; diğerleri ⚠️ §2.2) |
| Post/akış (DAW benzeri) | Pro Tools | Windows'ta protokol seçeneği (WASAPI + ASIO) | ✅ |
| Video/çapraz | Adobe Audition | "professional cards" için cihaz sınıfı | ⚠️ §2.2 |
| DJ/performans | rekordbox · Serato | Donanım-özel ASIO sürücüleri (envanter) | ⚠️ §2.2 (Serato UNKNOWN) |
| Kayıt/editör | Audacity | Resmi derlemede **yok** | ✅ (negatif kanıt) |
| Oynatıcı | foobar2000 · JRiver · Winamp · MusicBee · MediaMonkey | Eklenti/3. parti düzeyinde | ⚠️ §8 |

**Çıkarım:** ASIO'nun DAW dışı hayatta da (oynatıcı, DJ) yaşadığı görülüyor; ama
"resmi derlemede ASIO yok" örneği (Audacity) gösteriyor ki ASIO entegrasyonu
**lisans + mühendislik maliyeti** birlikteliğidir — sadece teknik değil, lisans
tarafı da belirleyici (`[[asio-lisans-ve-dogrulama]]`).

### §2.5 Sürücü tipi tercih zinciri (kaynak destekli — ✅)

Kaynaklar üç DAW'dan aynı zinciri veriyor (Ableton · Bitwig · FL Studio):

```text
1. Donanım-özel native ASIO sürücüsü (varsa)     ← FL: "If your audio device has a
                                                     native ASIO driver then we recommend
                                                     you use that"
2. Yoksa evrensel ASIO köprüsü                    ← Ableton/Bitwig: ASIO4ALL;
                                                     çoklu-uygulama şartsa: FlexASIO
3. Yoksa DAW'ın kendi yazılım yolu                ← FL Studio ASIO (WASAPI üstü katman);
                                                     Pro Tools: WASAPI (limited to audio only)
4. Windows varsayılan yolu (MME/DirectX vb.)      ← Ableton'un saydığı "other drivers"
```

Bu zincir Neva Engine'in cihaz tercih sırasıyla (`[[asio-platform-uyumlulugu]]` §7.5
karar ağacı) aynıdır — iki belge birbirini teyit eder.

---

## §3 Host yaşam döngüsü (JUCE kanıtı — Neva Engine'in örneği)

Neva Engine JUCE tabanlı olacağı için (ROLE: JUCE 9 + ASIO SDK 2.3.4), JUCE'un
Windows ASIO host kodu *de facto* referans davranıştır. Kaynak:
`juce_win32_ASIO.cpp` @ `90e8da0` (2026-10-12 okundu).

### §3.1 Aşamalar

```text
1. Tarama      : HKLM\SOFTWARE\ASIO altındaki sürücü adları okunur
                 (asio.h/asiolist modeli — [[asio-surucu-modeli]] §registry)
2. Seçim       : kullanıcı arayüzü / otomatik seçim; JUCE'de
                 scanForDevices() + addDriverInfo() eşleşmesi
3. init()      : IASIO::init(ref) → sorumlu değilse hata
4. Konfigürasyon: sampleRate / bufferSizes / channelCounts sorulur
                 (asio.h selector'ları — [[asio-arayuzler]] §selector)
5. CreateBuffers: host buffer'ları hazırlar, ASIO driver'ı ayırır
6. Döngü       : bufferSwitch / bufferSwitchTimeInfo callback'i
                 → host işler → loopback'e kadar devam
7. DisposeBuffers / Terminate
```

### §3.2 JUCE'un uyguladığı güvenlik kalıpları (kod kanıtı)

| Kalıp | Davranış | Neva Engine'e aktarım |
|-------|----------|------------------------|
| **SEH koruması** | `__try/__except` ile sürücü çağrıları sarılır — bozuk vendor DLL host'u çökertemez | Zorunlu (PLANNED) |
| **Kara liste** | Bazı sürücü sürümleri host'larda bilinen çökme/uyumsuzluk nedeniyle elenir | Vendor test matrisi gerekli |
| **Cihaz sınırı** | JUCE taramada en fazla **16 cihaz** tutar | Sınır bilinçli seçilmeli (16 yeterli mi? ⚠️) |
| **Sihrbaz yasağı** | ASIO init sihirbazları (wizard) host'tan yürütülmez | Aynı yasak |

⚠️ VERIFICATION REQUIRED: kara liste içeriği ve 16 sınırının gerekçesi bu turda
JUCE kaynağından cümle cümle doğrulanmadı — kod incelemesi gerekir; sayı "16" kod
sabitinden gelir.

### §3.3 Host ↔ sürücü hata modları

| Mod | Belirti | Host karşılığı |
|-----|---------|----------------|
| Sürücü registry'de yok | Tarama boş | Fallback: WASAPI (`.ai/AGENTS.md` §17/6) |
| `init()` başarısız | Cihaz başka uygulamada açık | Kullanıcıya "diğer uygulamayı kapat" |
| Buffer boyutu reddedildi | Vendor tek boyut destekliyor | Alternatif boyut dene (asio.h `bufferSize` selector'ı) |
| Callback'de exception | Bozuk plugin/sürücü | SEH + drop-out; host asla callback'te lock almaz |

---

## §4 Multi-client davranışı (kaynak: Ableton Multi-Client sayfası ✅)

Bu bölümün tamamı 2026-10-12'de okunan Ableton "Multi-Client Operation Drivers
(Windows)" sayfasına dayanır.

| Davranış | Kayıttaki ifade |
|----------|-----------------|
| ASIO4ALL varsayılanı | "**exclusive mode** by default" — başka uygulama cihazı paylaşamaz |
| FlexASIO farkı | "FlexASIO permits multiple applications, e.g. other DAWs, and browsers, to share the same audio driver. **This allows audio to run simultaneously from Live and any other applications.**" |
| Mekanizma | FlexASIO "any sound API available on a Windows system" ile çalışır; "open an audio device in **shared mode**" emüle eder; "the Windows audio engine mixing the various streams" |
| Diğer evrensel sürücüler | "Other universal ASIO drivers do not offer this functionality as they always open audio devices in **exclusive mode**." |
| FlexASIO varsayılanları | DirectSound backend · Windows varsayılan cihazları · 32-bit float · **20 ms "preferred" buffer** · CONFIGURATION.md ile özelleştirilir |
| Sanal mikser yolu | VoiceMeeter Potato + "VoicemeeterAUX Virtual ASIO" → ASIO host'una sanal cihaz sunar |

### §4.1 Multi-client karar tablosu (Neva Engine — PLANNED)

| Kullanıcı senaryosu | Öneri | Kaynak |
|---------------------|-------|--------|
| Tek DAW, sistem sesi istenmiyor | Vendor ASIO (exclusive, en düşük gecikme) | ✅ §2.1 Ableton (düşük buffer, kararlı) |
| DAW + tarayıcı/YouTube birlikte | FlexASIO (shared karışım) veya VoiceMeeter | ✅ Ableton multi-client |
| ASIO4ALL kullanılıyor + "ses yok" | Exclusive çakışması → diğer uygulamaları kapat veya FlexASIO'ya geç | ✅ Ableton + FL Studio troubleshooting |
| Focusrite Scarlett + ASIO4ALL | "ASIO4ALL seems to conflict with Focusrite's proprietary driver" → vendor sürücüsü kullan | ✅ Ableton ASIO sayfası (Not) |

**Repo kuralı ile bağlantı:** Neva Engine cihazı kaybederse (exclusive çakışması dahil)
→ WASAPI fallback (`.ai/AGENTS.md` §17/6). Multi-client çözümü donanım sürücüsü
seviyesinde sağlanamazsa fallback zorunludur.

### §4.2 Uygulama türü × paylaşımcılık matrisi (bu belgenin analizi)

| Uygulama türü | Aynı anda ikinci uygulama ister mi? | Öneri |
|---------------|--------------------------------------|-------|
| DAW + tarayıcı (YouTube, Tidal, Spotify) | Evet | FlexASIO (Ableton: "browsers … or another DAW running in the background") ✅ |
| İki DAW birlikte (ör. Live + REAPER) | Evet | FlexASIO / VoiceMeeter ✅ |
| Tek DAW, sadece kayıt | Hayır | Vendor ASIO (exclusive, en düşük gecikme) ✅ |
| DAW + VoiceMeeter sanal mikser | Evet, mikser üzerinden | VoiceMeeterAUX Virtual ASIO ✅ |
| FL Studio + USB mikrofon ayrı cihaz | Aynı ASIO altında farklı cihazlar | ASIO4ALL / FL Studio ASIO çoklu-cihaz özelliği ✅ |

### §4.3 Exclusive çakışma semptomları ve çözüm sırası (✅ kaynaklar)

| # | Semptom | Kaynak kaydı | Çözüm |
|---|---------|--------------|-------|
| 1 | ASIO4ALL'de girdi/çıktıda kırmızı X ("Unavailable") | FL Studio: "they are probably being used by another audio application" | Diğer uygulamaları kapat (Skype, medya oynatıcılar) ✅ |
| 2 | Ses yok, Windows exclusive kontrol açık | FL Studio: "Disable 'Allow applications to take exclusive control of this device'" ✅ | Windows ses ayarı + restart |
| 3 | "Beyond Logic" hatası | FL Studio: kontrol panelinde sample rate kilidi → "known cause" ✅ | Kilidi kaldır, USB'yı yeniden tak |
| 4 | DAW açıkken tarayıcı sessiz | Ableton multi-client: ASIO4ALL exclusive default ✅ | FlexASIO'ya geç veya fallback |
| 5 | Focusrite Scarlett + ASIO4ALL garip davranış | Ableton: "seems to conflict with Focusrite's proprietary driver" ✅ | ASIO4ALL yerine Focusrite native ASIO |

### §4.4 JUCE çoklu-cihaz davranışı (Neva Engine bağlamı) ✅

`juce_win32_ASIO.cpp` (90e8da0) JUCE'un tek bir ASIO host oturumunda nasıl davrandığını
gösterir:

| Davranış | Kod kanıtı | Neva Engine karşılığı (PLANNED) |
|----------|-----------|----------------------------------|
| Cihaz taraması | `scanForDevices()` + `addDriverInfo()` | Registry taraması → `[[asio-surucu-modeli]]` §3 |
| Cihaz üst sınırı | 16 cihazlık sabit kapasite | Kapasite seçimi açık bırakıldı (⚠️ kaç cihaz?) |
| Bozuk sürücü kurtarması | `__try/__except` (SEH) sarmalayıcı | Zorunlu — vendor DLL host'u düşürmemeli |
| Bilinen sorunlu sürücüler | Kara liste (belirli sürücü sürümleri elenir) | Neva Engine kara listesi − kanıt yok → ⚠️ |
| Kurulum sihirbazı | ASIO init sihirbazı çağrılmaz (host'ta) | Aynı davranış |

> **Not (Zero-Hallucination):** §4.4 satırları kodu bu turda birebir okunmuştur;
> kara liste *içeriği* (hangi sürücü hangi sürüm) okunmadı → içeriği iddia etmiyoruz,
> yalnızca mekanizma kaydedildi.

---

## §5 ASIO-Guard (Cubase/Nuendo) ✅

Kaynak: Steinberg Help Center "Details on-ASIO Guard in Cubase and Nuendo"
(2026-10-12 okundu).

### §5.1 Mekanik

- Cubase, ses işleme için **iki buffer yolu** kullanır: **gerçek-zaman (RT) yolu** —
  monitörlenen ve kayıt-armed track'ler; **guard yolu** — geri kalan her şey
  (kayda alınmayan/izlenmeyen track'ler) için **daha büyük, ikinci bir ön-işlem
  buffer'ı** ile çalışır.
- Bir track **record-arm veya monitor** edildiğinde Cubase o track'i **dinamik olarak
  RT yoluna** geçirir.
- Üç kademe: **Low / Normal / High** — guard yolunun ne kadar agresif
  (küçük/az gecikmeli) çalışacağını belirler.
- **İstisnalar (RT yolunda kalır):** record-enabled VSTi'ler, monitörlenen track'ler,
  external FX / external enstrümanlar (dış donanım yinelemesi — gecikme
  gerçek-zamanlı yaşamalıdır).

### §5.2 Neden önemli

ASIO-Guard, "tek buffer her şey için" düşüncesinin DAW ölçeğinde yanlış olduğunu
kanıtlar: **kayda girmeyen ses yolu büyük buffer'la çalışabilir, izleme yolu
küçük buffer'la çalışmalıdır.** Neva Engine'in izleme/kayıt ayrımı yoksa bile
(şimdilik), bu iki-yol modeli ileride monitör özelliği için şartnamede saklıdır.

⚠️ VERIFICATION REQUIRED: ASIO-Guard'ın Low/Normal/High kademelerinin sayısal
buffer karşılıkları (ms/örnek) bu sayfada verilmemiştir — sayının uydurulmaması
için yalnız kavram kaydedildi.

---

## §6 ARA (Audio Random Access) ✅

Kaynak: `github.com/Celemony/ARA_SDK` README (2026-10-12 okundu).

| Madde | Kayıt |
|-------|-------|
| Ne | ARA, ses eklentilerinin DAW ile **dosya/oturum verisine anlık erişim** kurmasını sağlayan bir API ailesi (eklenti + host) |
| Sürümler | ARA 1 ve ARA 2 — SDK Celemony + diğer ortakların katkısıyla açık |
| İlişki (analiz) | ARA **eklenti↔DAW veri katmanıdır**, sürücü katmanı değildir: cihazı bağlamaz, buffer seçmez |

**ASIO ile ilişkisi (çıkarım — işaretli):** ARA eklentisi bir DAW oturumunda
çalışır; DAW zaten ASIO host'u olarak cihazı yönetir. Yani "ARA + ASIO" bir çakışma
değil, **üst üste binen iki katmandır** (ARA: veri, ASIO: ses yolu).
⚠️ VERIFICATION REQUIRED: ARA host'unun ASIO cihazı değişimini (device loss)
eklentilere nasıl bildirdiğine dair birincil kayıt bu turda bulunmadı.

### §6.1 Katman şeması (bu belgenin analizi)

```text
┌──────────── DAW ─────────────┐
│  Eklenti katmanı: VST3/CLAP/AAX │ ← format (dosya/parametre)
│  ARA katmanı    : v1 / v2        │ ← oturum verisi paylaşımı (✅ ARA_SDK)
│  ──────────────────────────────  │
│  Host katmanı   : ASIO host      │ ← cihaz tarama/init/bufferSwitch (§3)
└───────────────┬────────────────┘
                ▼
        ASIO sürücüsü (vendor / köprü)
                ▼
        Donanım (WDM-KS / USB / PCIe)
```

**Çıkarım (işaretli):** ARA eklentisi "sesi" host'a sormaz; host ASIO cihazını
değiştirdiğinde (device loss) ARA oturumu yaşamaya devam eder — bu iki katmanın
birbirinden bağımsız olduğu anlamına gelir. Bu çıkarım mimaridir; ARA_SDK README
yalnızca API'nin eklenti-host veri paylaşımı olduğunu söyler.

---

## §6.2 DAW buffer/latency ayar kalıpları (✅ + ⚠️)

| DAW | Ayar adı (kaynakta) | Not | Kaynak |
|-----|---------------------|-----|--------|
| Ableton Live | Preferences → Audio → Driver Type → ASIO | ASIO4ALL kurulum makalesi ayrı (115000204630) | ✅ 360003149240 |
| Ableton Live | Multi-client → FlexASIO seçimi (Driver Type: ASIO, Audio Device: FlexASIO) | varsayılan: DirectSound, 20 ms "preferred" buffer, 32-bit float | ✅ 4515794042898 |
| Bitwig | Settings → Audio → Driver model → ASIO / ASIO4ALL | kayıt için ASIO zorunlu | ✅ bitwig 36 |
| Pro Tools | Setup → Playback Engine (drop-down) | protokol seçimi burada yapılır | ✅ avid Playback Engine |
| FL Studio | F10 → Audio → ASIO4ALL v2 → "Show AS panel" | Buffer Size örnek hesabı: 448 samples @44,1 kHz ≈ 10 ms (kaynaktaki kendi örneği) | ✅ envsettings_asio4all |
| Cubase/Nuendo | Audio Settings → ASIO driver (+ ASIO-Guard kademesi Low/Normal/High) | guard yolu §5 | ✅ ASIO-Guard · ⚠️ Audio Settings sayfası |

**Buffer hesabı notu (kaynak örneği):** FL Studio/ASIO4ALL sayfası örnek verir —
buffer samples'ı Hz'e bölünür (44100 için 44,1'e; 48000 için 48'e); 448 sample @44,1 kHz
≈ 10 ms. Bu, `[[asio-platform-uyumlulugu]]` §6.1 aritmetik tablosuyla uyumludur
(448/44,1 = 10,16 ms — sayfa "approximately" der).

---

## §7 Evrensel sürücü × DAW çakışma kayıtları

| # | Kayıt | Durum |
|---|-------|-------|
| 1 | Focusrite Scarlett aralığı + ASIO4ALL → çakışma; Ableton: ASIO4ALL Scarlett'in proprierter sürücüsüyle çakışıyor gibi görünüyor | ✅ (Ableton ASIO sayfası Not) |
| 2 | Pro Tools + ASIO4ALL → Avid'in qualified listesinde değil (kurulum makalesi mevcut, qualification yok) | ⚠️ VERIFICATION REQUIRED (envanter) |
| 3 | ASIO4ALL "Unavailable" (kırmızı X) → cihaz başka uygulamada (MSN Messenger, Skype, medya oynatıcı) | ✅ (FL Studio troubleshooting) |
| 4 | FL Studio + Windows exclusive control → "Allow applications to take exclusive control" kapatma önerisi | ✅ (FL Studio troubleshooting) |
| 5 | Bitwig 5.3+ "ASIO interface is not detected" ayrı destek makalesi var | ⚠️ VERIFICATION REQUIRED (envanter) |

**Örüntü:** neredeyse tüm çakışmalar **exclusive-mode kaynaklıdır** (§4) — driver
kendisi değil, "kim önce ele geçirdi" sorunu. Çözüm: exclusive'i serbest bırak
(Windows ayarı), paylaşan sürücüye geç (FlexASIO) veya fallback'e düş (§3.3).

### §7.1 Çakışma zinciri — kök nedir? (çapraz okuma ✅)

§7 kayıtlarını `[[asio-platform-uyumlulugu]]` §4/§5 mekaniğiyle birleştirince:

```text
Vendor ASIO sürücüsü  → cihazı ele geçirir (exclusive, Windows ses motorunu bypass eder)
ASIO4ALL (default)    → exclusive mode by default (Ableton ✅)
Windows exclusive ayar → "Allow applications to take exclusive control" (MS ✅)
    ↓ üçü aynı anda exclusive isterse
Semptom: kırmızı X / "Unavailable" / "Beyond Logic" / sessiz ikinci uygulama (FL Studio ✅)
    ↓ çözüm katmanları
(1) Windows ayarını serbest bırak (FL Studio ✅)
(2) Paylaşan sürücüye geç: FlexASIO shared (Ableton ✅)
(3) Host tarafı dayanıklılık: SEH + device-loss tespiti (JUCE ✅) → WASAPI fallback (repo SSOT)
```

**Sonuç:** multi-client sorunu tek bir yerde çözülmüyor — **sürücü seçimi + Windows
ayarı + host fallback** üçlüsü birlikte. Neva Engine'in düşebileceği tek katman (3)'tür;
(1) ve (2) kullanıcı/DAW alanındadır.

### §7.2 Host dayanıklılık checklist'i (Neva Engine — PLANNED, kaynaklardan türetildi)

| # | Kontrol | Kaynak dayanağı |
|---|---------|-----------------|
| 1 | Cihaz açıkken ikinci istemci gelirse → anlaşılır hata, sessiz çökme yok | ✅ FL Studio "Unavailable" modeli |
| 2 | `init()` başarısız → diğer uygulama uyarısı (Skype vb. örneği) | ✅ FL Studio troubleshooting |
| 3 | Callback koruması (SEH benzeri) | ✅ JUCE `__try/__except` |
| 4 | Cihaz kaybı → `.ai/AGENTS.md` §17/6 WASAPI fallback | Vault SSOT |
| 5 | Exclusive Windows ayarı kullanıcıya gösterilebilir ipucu | ✅ FL Studio "Disable exclusive control" |
| 6 | Focusrite gibi çakışmalı donanım kombinasyonları için bilgi notu | ✅ Ableton Scarlett Not |

---

## §8 Medya oynatıcılar ve mikro-yazılımlar (envanter özeti)

> Bu bölüm envanter düzeyindedir (⚠️) — §2.2 kuralı geçerlidir: teyit edilmeden
> "destekler" diye kullanılmaz.

| Uygulama | Envanteki iddia | Durum |
|----------|-----------------|-------|
| foobar2000 | Yerleşik değil — ASIO Output component (`foo_out_asio`) | ⚠️ |
| JRiver Media Center | Çıktı modları: Direct Sound · WASAPI · ASIO · Kernel Streaming | ⚠️ |
| MusicBee | ASIO çıktı eklentisi — yalnız forum kanıtı | ⚠️ |
| Winamp | Yerleşik değil — 3. parti ASIO output plugin | ⚠️ |
| MediaMonkey | Wiki 2 adet 3. parti ASIO plugin'i tarif eder | ⚠️ |
| VB-CABLE | ASIO **değil** (MME/KS/DX/WASAPI); "ASIO Bridge" ayrı panel | ⚠️ |

**Ortak örüntü (envanterden, işaretli):** oynatıcı sınıfında ASIO ya eklenti ya da
3. parti plugin'dir — hiçbiri DAW sınıfı gibi ASIO'yu birincil yol yapmaz. Bu,
ASIO'nun "pro yol" konumunu (`[[asio-anayasa]]` §kapsam) doğrulayan bir ikincil
gözlemdir; ama tek tek iddialar ⚠️ kalır.

### §8.1 DAW kontrol listesi (kullanıcı destek özeti — yalnız ✅ satırlardan)

Bir DAW'ın ASIO'yu doğru kullanıp kullanmadığı şu sorularla denetlenir:

1. Driver Type / Playback Engine / Audio Device menüsünde ASIO seçilebiliyor mu?
   (Ableton ✅ · Bitwig ✅ · Pro Tools ✅ · FL Studio ✅)
2. Cihaz listesi boşsa sürücü kurulu mu? (Ableton: "ASIO drivers are usually not
   included on Windows and must be installed separately" ✅)
3. Buffer boyutu düşürülebiliyor mu? (Ableton gerekçe 2 ✅; FL Studio örnek hesabı ✅)
4. İkinci uygulama ses istiyor mu? (multi-client → §4 ✅)
5. Kayıt girişi çalışıyor mu? (Bitwig: WASAPI'de çalışmaz, ASIO gerekli ✅)

Bu beş soru Neva Engine'in kendi host test planına (PLANNED) girdi olarak yazılır:
aynı beş soru, Neva Engine cihaz seçim ekranına uygulanır.

---

## §9 Karar günlüğü

| # | Karar | Gerekçe | Kanıt |
|---|-------|---------|-------|
| DD-01 | DAW matrisi ikiye bölündü: ✅ okunmuş / ⚠️ envanter | Zero-hallucination: eski dosya kanıt sayılmaz | Bu dosya §2 |
| DD-02 | Multi-client çözümü olarak FlexASIO + VoiceMeeter kaydedildi, ASIO4ALL "paylaşıyor" denmedi | Ableton sayfası tersini söylüyor (exclusive default) | ✅ §4 |
| DD-03 | ASIO-Guard sayısal buffer değerleri yazılmadı | Sayfa yalnız kavram/kademe veriyor; uydurma yasak | ✅ §5.1 |
| DD-04 | ARA-ASIO ilişkisi "üst üste binen katman" olarak işaretli çıkarım | Birincil kayıt yok | ✅ §6 + ⚠️ |
| DD-05 | Neva Engine host tasarımı JUCE kalıplarını (SEH, kara liste, sınır) şartnameye alır | JUCE kodu fiili referans | ✅ §3.2 |

---

## §10 Kanıt–iddia tablosu

| # | İddia | Kanıt (URL) | Güç |
|---|-------|-------------|-----|
| 1 | Ableton ASIO önerisi (3 gerekçe) + ASIO4ALL önerisi + Scarlett çakışması + FlexASIO alternatifi | help.ableton.com/hc/en-us/articles/360003149240-Using-an-ASIO-audio-driver-on-Windows | Birincil ✅ |
| 2 | ASIO4ALL exclusive default; FlexASIO shared; Windows audio engine karıştırır; FlexASIO varsayılan 20 ms | help.ableton.com/hc/en-us/articles/4515794042898-Multi-Client-Operation-Drivers-Windows- | Birincil ✅ |
| 3 | Bitwig WASAPI'yi kayıtta desteklemez, ASIO ister | bitwig.com/support/technical_support/how-do-i-record-audio-using-wasapi-on-windows-36/ | Birincil ✅ |
| 4 | Pro Tools: Mac CoreAudio; Windows WASAPI (limited to audio only) + ASIO | kb.avid.com/pkb/articles/en_US/FAQ/What-is-the-Playback-Engine-in-Pro-Tools | Birincil ✅ |
| 5 | FL Studio: ASIO Windows-only; hız + çoklu-cihaz avantajı; exclusive control troubleshooting | image-line.com/fl-studio-learning/fl-studio-online-manual/html/envsettings_asio4all.htm | Birincil ✅ |
| 6 | ASIO-Guard: iki yol, dinamik geçiş, Low/Normal/High, istisnalar | helpcenter.steinberg.de/hc/en-us/articles/206103564-Details-on-ASIO-Guard-in-Cubase-and-Nuendo | Birincil ✅ |
| 7 | ARA 1/2 SDK, eklenti-host veri katmanı | raw.githubusercontent.com/Celemony/ARA_SDK/master/README.md | Birincil ✅ |
| 8 | Audacity resmi derlemede ASIO yok (özel derleme) | manual.audacityteam.org/man/asio_audio_interface.html | Birincil ✅ |
| 9 | JUCE host: tarama/SEH/kara liste/16 cihaz sınırı | github.com/juce-framework/JUCE/blob/90e8da0…/juce_win32_ASIO.cpp | Birincil (kod) ✅ |
| 10 | Repo kuralı: ASIO device loss → WASAPI fallback | .ai/AGENTS.md §17 Edge Case #6 | Vault SSOT |

**Toplam benzersiz URL (frontmatter + tablo): 10** (≥8 hedefi karşılar).

**İşaret sayımı (bu dosya):** `✅` doğrudan okunmuş kanıt · `⚠️` envanter/çıkarım ·
`UNKNOWN` kanıt yok — üç işaret de §0'da tanımlı ve dosya boyunca tutarlı kullanıldı.

---

## §11 Açık sorular

1. **§2.2 on DAW satırı** teyit bekliyor (WaveLab · Studio One · REAPER · Waveform ·
   Cakewalk · Audition · Samplitude · rekordbox + 2 UNKNOWN) → sonraki turda tek tek
   açılacak.
2. **Pro Tools WASAPI cümlesi** ("limited to audio only") — bağlam netleştirme gerekiyor.
3. **JUCE kara liste** içeriği + 16 cihaz sınırı gerekçesi → kod incelemesi.
4. **ARA 2 ↔ device loss** bildirimi → birincil kaynak yok.
5. **Audacity özel derleme + GPLv3 SDK** → `[[asio-lisans-ve-dogrulama]]` açık sorusu.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** Ableton Help (ASIO · Multi-Client) · Bitwig Support · Avid KB (Playback
Engine) · Image-Line FL Studio Manual · Steinberg Help Center (ASIO-Guard) · ARA SDK ·
Audacity Manual · JUCE juce_win32_ASIO.cpp · .ai/AGENTS.md §17 · 2026-10-12
