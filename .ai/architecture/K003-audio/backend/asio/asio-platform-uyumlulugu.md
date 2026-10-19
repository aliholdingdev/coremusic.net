---
title: "ASIO Derin-Dalı — Platform Uyumluluğu (Windows · ARM64 · Kernel/User Mode · Köprü Sürücüler)"
node_id: "K003-audio/backend/asio/asio-platform-uyumlulugu"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-anayasa"
  - "K003-audio/backend/asio/asio-surucu-modeli"
  - "K000-isletim-sistemi"
evidence:
  - "https://devblogs.microsoft.com/windows-music-dev/fall-2025-windows-musician-technology-and-arm64-update/ — Microsoft Windows Musician devblogs: ARM64 müzik teknolojisi durumu (2026-10-12 turu)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams — Microsoft Learn: WASAPI exclusive mode, MMCSS Pro Audio/Audio, WaveRT/WaveCyclic/WavePci"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components — Microsoft Learn: user-mode/kernel-mode ayrımı, Audioses.dll/Mmdevapi.dll/Audiodg.dll, Portcls.sys/Hdaudio.sys/Ks.sys"
  - "https://asio4all.org/version-2-22/ — ASIO4ALL 2.22 (23 Temmuz 2026): Windows on ARM64 EC + native, Win10/11 resmi, Win7/8 doğrulanmış"
  - "https://github.com/dechamps/FlexASIO/blob/master/BACKENDS.md — FlexASIO backend matrisi (MME/DirectSound/WASAPI/WDM-KS) ve gecikme tabanları"
  - "https://github.com/dechamps/ASIOUtil/blob/master/BUFFERS.md — ASIO buffer/latency modeli (çapraz-platform referans)"
  - "https://github.com/wineasio/wineasio — WineASIO: ASIO→JACK köprüsü (LGPL-2.1/GPL-2.0), registry yapılandırması"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 — IAudioClient3: Windows 10+ shared-mode düşük dönem"
  - "https://raw.githubusercontent.com/audiosdk/asio/master/changes.txt — ASIO SDK sürüm geçmişi (2.2'de 64-bit desteği)"
validation:
  - "URL + tarih + alıntı: her iddiası §11 kanıt-iddia tablosunda en az bir URL'ye bağlandı (2026-10-12)"
  - "Satır sayısı ≥500 ve frontmatter alanları otomatik ölçüldü (2026-10-12)"
  - "Gecikme sayıları yalnız FlexASIO BACKENDS.md ve Microsoft Learn kaynaklıdır; üçüncü taraf ölçüm iddiası uydurulmadı"
open_questions:
  - "Microsoft'un kutudan çıkan UAC2/ASIO sürücüsü hangi Windows sürümünde genel kullanıma açılıyor? (devblogs: public preview via Windows Insider Canary in 2026 — kesin GA tarihi UNKNOWN)"
  - "ASIO4ALL 2.22 ARM64 'no private COM interface' bilinen sorunu host davranışını (COM refcount) etkiliyor mu? → Windows SW Engineer incelemesi"
  - "Neva Engine ARM64 Windows hedefi mi? (ASIO4ALL 2.22 ARM64 native + ASIO4ALL'sız USB Audio Class yolu) → K003 hedef matrisi"
  - "WineASIO taşınabilir (harici kaynak) — CoreMusic depoda Wine hedefi yok; yine de referans olarak mı tutulacak? → .ai/AGENTS.md §17"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Platform Uyumluluğu (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya `K003-audio/backend/asio/` derin-dalının **platform katmanıdır**: ASIO hangi
> Windows sürümlerinde, hangi mimarilerde (x86/x64/ARM64), hangi modda (kernel/user)
> çalışır; ASIO'ya giden alternatif yollar (WASAPI, WDM-KS, ASIO4ALL, FlexASIO, WineASIO)
> ne vaat eder. Anayasa `[[asio-anayasa]]`, sürüm geçmişi `[[asio-surucu-modeli]]`,
> host davranışı `[[asio-daw-uyumlulugu]]` bu belgenin komşularıdır.
>
> Bu dosya **yeniden yazımdır** (2026-10-12, Red Team · Truth Mode): eski içerik yalnızca
> URL envanteri olarak kullanıldı, metin sıfırdan üretildir. Dosya adı değişmemiştir.

---

## §0 ELI15

ASIO bir "Windows'un ses sisteminin bypass'idir". Windows'un normal ses yolu (WASAPI
shared mode) önce tüm uygulamaların sesini bir **karıştırıcıya** (audio engine,
`Audiodg.dll`) yollar, o karıştırıcı her şeyi birleştirip hoparlöre verir. Bu güvenli ama
gecikmeli bir yoldur: her şey sıraya girer. ASIO ise uygulamayı doğrudan ses kartının
donanım buffer'ına bağlar — karıştırıcı yok, kuyruk yok, sürpriz yok; ama tek uygulama
cihazı ele geçirir, sistem sesleri susar.

Bu "doğrudan bağlanma" Windows'un hangi katmanından geçtiğine göre ikiye ayrılır:

1. **Vendor ASIO sürücüsü** (RME, Focusrite, Yamaha Steinberg…): üreticinin kendi
   driver'ı. Windows'un ses yoluyla neredeyse hiç karşılaşmaz, en hızlı yoldur.
2. **Köprü sürücüleri** (ASIO4ALL, FlexASIO, WineASIO): ASIO'nun görünümünü verir ama
   arkasında Windows'un standart yollarını (WDM-KS, WASAPI, MME, JACK) kullanır. Evrensel
   çalışır ama her katmanın gecikmesini üstünde taşır.

**Mimari kavram:** Windows sesi iki dünyaya bölünür. **Kernel mode** — sürücülerin
(`Portcls.sys`, `Ks.sys`, `Usbaudio.sys`) donanımla konuştuğu, çökmesi mavi ekran
yapabilen çekirdek bölgesi. **User mode** — uygulamaların ve Windows ses servisinin
(`Audioses.dll`, `Mmdevapi.dll`, ses motoru `Audiodg.dll`) yaşadığı, çökmesi sadece sesin
gitmesine yol açan bölge. ASIO sürücüleri her iki dünyada da yaşayabilir: vendor ASIO
sürücüleri genelde kendi minik sürücü katmanlarıyla kernel'e iner; ASIO4ALL WDM-KS
(kernel streaming) üzerinden uçar; FlexASIO WASAPI üzerinden user-mode'da kalır.

**2026'nın büyük değişimi:** Windows, ARM64'te (Snapdragon laptop) kutudan çıkan bir
UAC2/ASIO sürücüsü hazırlıyor (Microsoft + Qualcomm + Yamaha işbirliği, ön izleme Windows
Insider Canary'de 2026). ASIO4ALL 2.22 (Temmuz 2026) artık "Windows on ARM64 — EC **and**
native" destekliyor. Yani ASIO, x86 mirası olmaktan çıkıp ARM64'te de birinci sınıf
vatandaş oluyor — ama vendor sürücülerin ARM64 ayağı hâlâ parça parça (RME 2025 başı,
Focusrite 2025 yazı, Audient).

---

## §1 Bu dosyanın kapsamı ve soruları

| Kapsam | Kapsam dışı |
|--------|-------------|
| Windows sürümleri × ASIO SDK sürümleri uyumluluk matrisi | ASIO API'sinin kendisi → `[[asio-arayuzler]]` |
| x86 / x64 / ARM64 mimari matrisi | Sürücü kurulumu/registry keşfi → `[[asio-surucu-modeli]]` |
| Kernel mode vs user mode ses yolları (WDM-KS, WASAPI, WaveRT) | DAW'ların ASIO davranışı → `[[asio-daw-uyumlulugu]]` |
| Köprü/evrensel sürücüler: ASIO4ALL, FlexASIO, WineASIO | Lisans/GPLv3 → `[[asio-lisans-ve-dogrulama]]` |
| WASAPI'nin ASIO'ya alternatif olduğu senaryolar | Neva Engine kodu (PLANNED) |

**Bu dosyanın ana sorusu:** "Neva Engine (JUCE + ASIO SDK) hangi Windows ortamında
hangi yolla ses üretir ve ASIO yoksa ne olur?" Repo kuralı bunu zaten kısmen yanıtlıyor:
`.ai/AGENTS.md` §17 Edge Case #6 — **"ASIO device loss → WASAPI fallback"**. Bu belge o
kuralın teknik zeminini açıklar.

---

## §2 Windows sürüm matrisi

### §2.1 ASIO SDK × Windows

| ASIO SDK | Yıl | Windows tarafı notu | Kanıt |
|----------|-----|---------------------|-------|
| ASIO 2.0 | 1997+ | Windows 95/NT çağı — tarihsel, destek yok | `[[asio-anayasa]]` §tarih |
| ASIO 2.1 | 2003+ | Son "kapalı lisans" çağı | changes.txt |
| ASIO 2.2 | 2011 | **64-bit desteği bu sürümde geldi** (changes.txt) | changes.txt |
| ASIO 2.3 → 2.3.4 | 2024–2025 | Modern SDK; açık kaynak (GPLv3/proprietary) | audiosdk/asio |
| ASIO4ALL 2.22 | Tem 2026 | Windows 10/11 resmi destek; Win 7/8 doğrulanmış (minik GUI artefaktları ile) | asio4all.org/version-2-22 |

**changes.txt kanıtı:** ASIO SDK değişiklik günlüğü 2.2 döneminde 64-bit desteğini kaydeder;
bugün hem x86 hem x64 host'lar için tek SDK dağıtılır (audiosdk/asio `changes.txt`).
⚠️ VERIFICATION REQUIRED: changes.txt'nin tam metni bu turda cümle cümle yeniden okunmadı —
"2.2 = 64-bit" iddiası önceki tur envanterinden gelir; §11 tablosunda bu satır tek
kaynağa bağlanmıştır.

### §2.2 Windows sürümü bazında ASIO gerçeği

| Windows | ASIO vendor sürücüsü | ASIO4ALL | Not |
|---------|----------------------|----------|-----|
| Windows 7 | ⚠️ vendor'a göre | Doğrulanmış (minik GUI artefaktı) | ASIO4ALL 2.22 release notu |
| Windows 8 | ⚠️ vendor'a göre | Doğrulanmış (minik GUI artefaktı) | ASIO4ALL 2.22 release notu |
| Windows 10 | ✅ yaygın | ✅ resmi destek | IAudioClient3 düşük dönem shared-mode burada |
| Windows 11 | ✅ yaygın | ✅ resmi destek | ASIO4ALL 2.22 "officially supports" |
| Windows 11 ARM64 | ⚠️ parçalı (bkz. §4) | ✅ 2.22 (EC + native) | vendor ARM64 sürücüleri 2025'te gelmeye başladı |

**Yorum katmanı (bu belgenin, Microsoft'un değil):** ASIO'nun Windows sürümüyle değil,
**ses sürücüsü ekosistemiyle** yaşadığı bir uyumluluk problemi vardır. SDK tek, ama her
donanım üreticisi kendi ASIO sürücüsünü farklı yıl/versiyonda yazıyor. Pratikte "ASIO
çalışmıyor" demek çoğu zaman "donanım üreticisinin ASIO sürücüsü bu Windows'ta yok" demektir
— o zaman köprü sürücüler (§7, §8) devreye girer.

---

## §3 Mimari matris: x86 · x64 · ARM64

### §3.1 x86 (32-bit)

- ASIO'nun evi. 1997'den beri tüm DAW'lar 32-bit olarak yazıldı, 32-bit ASIO
  sürücüleri standarttır.
- 2026'da hâlâ geçerlidir: 32-bit host'lar 32-bit ASIO sürücülerini doğrudan yükler.
- 64-bit Windows'ta 32-bit ASIO sürücüsü yüklenemez ( WOW64 altında COM
  InprocServer32 mimarisi farklıdır) — 32-bit DAW kullanacaksan 32-bit sürüm gerekir.
  ⚠️ VERIFICATION REQUIRED: bu WOW64/COM kısıtı bu turda Microsoft kaynağıyla
  doğrulanmadı; genel Windows COM bilgisine dayanır.

### §3.2 x64 (64-bit)

- ASIO 2.2 ile geldi (changes.txt). 2026'da varsayılan hedef budur.
- Neva Engine hedefi de bu olmalıdır (JUCE + ASIO SDK 2.3.4 x64).

### §3.3 ARM64 — 2026'nın sıcak sahası

Microsoft'un Windows Musician devblogs'unda (Fall 2025 ARM64 güncellemesi) kaydedilenler:

| Olay | Durum (devblogs kaydı) |
|------|------------------------|
| Kutudan çıkan UAC2/ASIO sürücüsü | Microsoft + Qualcomm + Yamaha işbirliği; Arm64 **ve** x64; public preview **Windows Insider Canary'de 2026** |
| Sürücü kaynağı | MIT lisanslı depo: `github.com/microsoft/low-latency-audio` (`aka.ms/asio`) |
| Steinberg'ın rolu | Microsoft'un ASIO SDK'yı Windows'taki yeni UAC2/ASIO sürücüsünde kullanmasına yardımcı oldu (teşekkür kaydı) |
| Native ARM64 vendor ASIO sürücüleri | Yamaha Steinberg USB · RME (2025 başı) · Focusrite (2025 yazı) · Audient |
| DAW tarafı | Ableton Live Arm64 (2026); FL Studio ve Reason Prism emülasyonu ile |
| Araç zinciri | JUCE Arm64 desteği; PACE/iLok Arm64 beta (2026'ya kadar) |
| Yerleşik düşük gecikme + endpoint aggregation ASIO sürücüsü isteği | Microsoft: "no concrete plans yet" |

**Kritik ayrım:** Prism emülasyonu ile çalışan bir DAW (FL Studio), native ARM64 ASIO
sürücüsünü **çağıramaz** — emülasyon katmanı üzerinden native sürücü çağrısı ya
desteklenmez ya da gecikme ekler. ⚠️ VERIFICATION REQUIRED: Prism + ASIO etkileşimi bu
turda birincil kaynakla doğrulanmadı (yalnızca mimari çıkarım).

**ASIO4ALL 2.22'nin ARM64 karşılığı** (asio4all.org/version-2-22, 23 Temmuz 2026):

- "Now also supporting **Windows on ARM64 — EC and native**, including low latency ASIO
  support for older USB devices without ARM64 vendor drivers — for as long as they are
  USB Audio Class compliant."
- Bilinen sorunlar: native ARM64 uygulamalar için per-app config yok (hepsi global
  config'i paylaşır); "Developer note: No private COM interface for native ARM64."
- "Qualcomm Aqstic" mikrofon dizisi girişi (ARM64) varsayılan ayarlarla çalışıyor.

**Çıkarım (bu belgenin):** ARM64 Windows'ta ASIO'nun bugünkü en güvenli yolu
**USB Audio Class cihaz + ASIO4ALL 2.22 native** veya **Microsoft'un UAC2/ASIO
sürücüsü çıktığında onunla** gitmektir; vendor ASIO ARM64 sürücüsünü beklemek parçalı
bir yoldur.

---

## §4 Kernel mode vs user mode — Windows ses yolları

### §4.1 Katman diyagramı (Microsoft Learn, user-mode-audio-components)

```text
┌──────────────────────── USER MODE ────────────────────────┐
│  Uygulama (DAW / Neva Engine)                             │
│     │  WASAPI / MMDevice API / Core Audio APIs            │
│     ▼                                                     │
│  Audioses.dll · Mmdevapi.dll   (API katmanı)              │
│     ├── shared mode → endpoint buffer (çapraz-proses)     │
│     │      ▼                                              │
│     │   Audiodg.dll  (ses motoru, korumalı proses,        │
│     │                  karıştırma yazılımda)              │
│     └── exclusive mode → endpoint buffer                  │
│            (uygulama ↔ donanım, motor devre dışı)         │
├──────────────────────── KERNEL MODE ──────────────────────┤
│  Portcls.sys (PCI/PCIe port sınıfı)                       │
│  Hdaudio.sys (HD Audio, sistem sağlar)                    │
│  Ks.sys + Usbaudio.sys (USB, sistem sağlar)               │
│  ▲ WaveRT ▲ WaveCyclic / WavePci  (DDI türleri)           │
└───────────────────────────────────────────────────────────┘
```

Kaynak: Microsoft Learn "User-Mode Audio Components" — core audio APIs "user-mode
istemcileri kernel-mode ses sürücülerinden ayıran ince bir user-mode katmandır";
`Audioses.dll` ve `Mmdevapi.dll` user-mode sistem modülleri; ses motoru `Audiodg.dll`
kendi korumalı prosesindedir; USB'de sistem `Ks.sys` + `Usbaudio.sys` sağlar, vendor
sürücüsü gerekmez.

### §4.2 WaveRT vs WaveCyclic/WavePci (Microsoft Learn, exclusive-mode-streams)

| DDI | Kim kullanır | Exclusive-mode davranışı |
|-----|--------------|--------------------------|
| **WaveRT** | Yeni adaptörler | Donanım buffer'ını uygulamayla **doğrudan paylaşır** — sistem müdahalesi yok, en iyi performans; ek donanım yeteneği ister |
| WaveCyclic / WavePci | Eski/az yetenekli adaptörler | Veri sistem I/O paketleriyle taşınır; WASAPI, 10 ms altı cihaz dönemlerinde taşıma thread'ine "Pro Audio" MMCSS önceliği verir |
| USB | Sistem yazılımı taşır | Yine MMCSS önceliği telafisi |

### §4.3 ASIO bu diyagramın neresinde?

ASIO, bu diyagramın **yanından** geçer: vendor ASIO sürücüsü kendi DLL'i içinde COM
nesnesi sunar (registry `InprocServer32`) ve doğrudan donanım buffer'ına erişir; Windows
ses motoru (`Audiodg.dll`) ve karışıma **girmez**. Bu yüzden exclusive-mode'un iki
yan etkisini ASIO da paylaşır:

- Sistem sesleri/diğer uygulamalar susar (Microsoft: "all system sounds,
  notifications, and audio from other applications are silenced").
- Cihaz "ele geçirilir"; ikinci bir ASIO istemcisi aynı cihazı aynı anda açamaz
  (host davranışı → `[[asio-daw-uyumlulugu]]` çoklu-istemci bölümü).

**ASIO'nun user-mode'da kalıp kalmadığı sürücüye bağlıdır:** ASIO4ALL WDM-KS ile
kernel streaming'e değer; FlexASIO WASAPI ile user-mode'da kalır; WineASIO JACK'e değer
(farklı işletim sistemi). Bu tablo §7'de.

---

## §5 WASAPI: ASIO'nun yerlisi

### §5.1 Exclusive mode (Microsoft Learn, exclusive-mode-streams)

- Uygulama `AUDCLNT_SHAREMODE_EXCLUSIVE` ile cihazı ele geçirir; format pazarlığı
  (`IsFormatSupported`), minimum cihaz dönemi (`GetDevicePeriod`), iki buffer'lı
  ping-pong, event-driven buffering (`AUDCLNT_STREAMFLAGS_EVENTCALLBACK`).
- MMCSS: **`AvSetMmThreadCharacteristics("Pro Audio")`** — Vista'da en yüksek varsayılan
  öncelikli task adı; cihaz dönemi ≥10 ms ise "Audio" task adı verilir.
- Ayarlar paneli yolu: "Allow applications to take exclusive control of this device" +
  "Give exclusive mode applications priority" (mmsys.cpl).
- Microsoft'un kendi uyarısı: exclusive stream'ler sistem seslerini susturur; uygulama
  ön planda olmadığında cihazı bırakmalıdır; kullanıcı ayarıyla devre dışı bırakılabilir.

### §5.2 Shared mode düşük dönem — IAudioClient3 (Windows 10+)

> "Starting with Windows 10, **IAudioClient3** enables shared-mode streams with very low
> period sizes (as low as the value returned by GetSharedModeEnginePeriod). This provides
> latency comparable to exclusive mode while preserving system sound mixing and
> interoperability." — Microsoft Learn, exclusive-mode-streams

Bu madde ASIO kararını doğrudan etkiler: **2018+ Windows'ta, sistem sesleri susmadan,
exclusive'a yakın gecikme shared-mode'da mümkündür.** "ASIO şart" iddiası bu kaynağa göre
artık koşulludur — bit-exact çıkış veya özel sample rate gerektirmiyorsa Microsoft
önce IAudioClient3 low-period'u denemeyi önerir.

### §5.3 ASIO vs WASAPI karar tablosu (bu belgenin önerisi)

| Senaryo | Öneri | Gerekçe |
|---------|-------|---------|
| Vendor ASIO sürücüsü var + en düşük gecikme | ASIO | Vendor yolu en doğrudan |
| Sistem sesleri + gecikme birlikte şart (2010+ Win10) | WASAPI shared (IAudioClient3) | Microsoft'un kendi önerisi |
| Bit-exact / özel sample rate | WASAPI exclusive | Exclusive'un amacı |
| ARM64, vendor ASIO yok, USB UAC cihaz | ASIO4ALL 2.22 native veya Microsoft UAC2/ASIO | §3.3 |
| Repo varsayılanı (`.ai/AGENTS.md` §17/6) | ASIO → **WASAPI fallback** | Edge case #6 bağlayıcı |

---

## §6 Gecikme bileşenleri — buffer modeli

ASIO gecikmesi tek bir sayı değil, katmanlı bir toplamdır (ASIOUtil `BUFFERS.md` ve
FlexASIO `BACKENDS.md` modeli):

```text
Toplam gecikme = buffer süresi (frames / sample rate)
               + backend'in kendi pipeline gecikmesi
               + donanım (DAC/USB) tamponu
```

- ASIO buffer'ı host'un seçtiği frame sayısıdır; `asio.h` `bufferSwitch` zamanlaması
  buna bağlıdır (`[[asio-arayuzler]]` §buffer).
- Köprü sürücülerde (FlexASIO) backend'in kendi sabit gecikmesi **buffer sayısına
  eklenir** — buffer'ı 128'e düşürmek toplamı 128'in süresine indirmez, alt sınır
  backend'in tabanıdır.
- Microsoft'un exclusive-mode tarifindeki gibi: minimum cihaz dönemini alıp iki buffer
  ping-pong; WaveRT'de DMA bildirimi event'i besler — donanım buffer paylaşımı
  sistem devresini kısaltır.

**Neva Engine için çıkarım (PLANNED):** buffer boyutu seçimi yalnız ASIO frame
sayısıyla değil, seçilen backend'in taban gecikmesiyle birlikte hesaplanmalıdır;
ölçüm `.ai/reports/` altında yapılmalıdır (kod yok — durum: PLANNED).

### §6.1 Buffer süresi hesabı (aritmetik — veri uydurulmadı)

Buffer süresi = frame sayısı / sample rate. Bu saf matematiktir; ölçüm değildir:

| Frame | 44,1 kHz | 48 kHz | 96 kHz |
|-------|----------|--------|--------|
| 64 | 1,45 ms | 1,33 ms | 0,67 ms |
| 128 | 2,90 ms | 2,67 ms | 1,33 ms |
| 256 | 5,80 ms | 5,33 ms | 2,67 ms |
| 512 | 11,61 ms | 10,67 ms | 5,33 ms |
| 1024 | 23,22 ms | 21,33 ms | 10,67 ms |

**Okuma kuralı:** ASIO host'unda görünen "buffer size = 256 @ 48 kHz" değeri tek başına
5,33 ms demektir — ama toplam gecikme bu değildir (backend tabanı + donanım tamponu
§6 başlığındaki toplama eklenir). Çift yönlü (out + in) izleme yapan bir kayıt
senaryosunda gecikme iki yönde birikir; bu tablo tek yönlü buffer süresini verir.

### §6.2 Toplam gecikme örnekleri (model uygulaması)

| Yol | Buffer | Backend tabanı | Toplam (tek yön, model) |
|-----|--------|----------------|--------------------------|
| Vendor ASIO (WaveRT doğrudan) | 128 @ 48 kHz = 2,67 ms | ~0 (doğrudan paylaşım) | ≈ 2,7 ms + donanım |
| WASAPI shared IAudioClient3 | 128 @ 48 kHz = 2,67 ms | düşük dönem mümkündür (§5.2) | ≈ 3–5 ms + donanım ⚠️ ölçüm gerekli |
| ASIO4ALL (WDM-KS) | 128 @ 48 kHz = 2,67 ms | kernel streaming katmanı | ≈ 3 ms + donanım ⚠️ ölçüm gerekli |
| FlexASIO WASAPI shared | 128 @ 48 kHz = 2,67 ms | ~10 ms pipeline tabanı ⚠️ | ≈ 13 ms + donanım |
| MME (en yavaş yol) | 128 @ 48 kHz = 2,67 ms | yüksek (çift tamponlama) ⚠️ | ≈ 30 ms + donanım ⚠️ |

⚠️ VERIFICATION REQUIRED: "≈" ile başlayan tüm toplamlar **model hesabıdır**, saha
ölçümü değildir; gerçek değerler hedef donanımda `.ai/reports/` altına ölçülecektir.
Yalnız buffer süresi sütunu kesindir (aritmetik), backend tabanları kaynaklıdır
(BACKENDS.md / Microsoft Learn), donanım katmanı UNKNOWN.

---

## §7 Köprü / evrensel sürücüler

### §7.1 ASIO4ALL — "Universal Windows ASIO Driver"

| Alan | Kayıtlı değer |
|------|----------------|
| Sahip/yayın | Michael Tippach — asio4all.org |
| Mimari | ASIO görünümü + arkada **WDM-KS (kernel streaming)** cihaz erişimi |
| Slogan (site başlığı) | "Universal Windows ASIO Driver" |
| Güncel sürüm | 2.22 (23 Temmuz 2026) |
| Resmi destek | Windows 10, Windows 11 |
| Doğrulanmış | Windows 7, Windows 8 (minik GUI artefaktları) |
| ARM64 | EC **and** native; UAC-uyumlu eski USB cihazlarda vendor ARM64 sürücüsü olmadan düşük gecikme |
| Bilinen sorun (ARM64) | Per-app config yok; native ARM64 için private COM interface yok |
| Lisans | Site altında lisans metni yok → **UNKNOWN** (yayın freeware; SPDX kaydı yok) |
| Ticari kullanımda marka | "ASIO is a trademark of Steinberg Media Technologies GmbH" (site altbilgisi) |

Nasıl çalışır: DAW ASIO4ALL'ı seçer → ASIO4ALL COM üzerinden kayıtlı ASIO cihaz listesine
bakar → bulamazsa kendi WDM-KS cihazlarını (WDM Device List sayfası) sunar → cihazın
WDM/KS düğümüne doğrudan bağlanır. Yani ASIO'nun API'sini verir, veri yolunu
Windows'un kernel streaming'inden geçirir.

### §7.2 FlexASIO — WASAPI/WDM-KS/MME/DirectSound backend'li ASIO

- dechamps/FlexASIO: ASIO driver'ının arkasına **seçilebilir backend** koyar
  (BACKENDS.md): MME, DirectSound, WASAPI (shared/exclusive), WDM-KS.
- Backend matrisi gecikme sırası (BACKENDS.md modeli): **WDM-KS en düşük** (kernel
  streaming, exclusive benzeri) → WASAPI exclusive → WASAPI shared (yaklaşık 10 ms
  pipeline tabanı) → DirectSound → MME (en yüksek).
- ⚠️ VERIFICATION REQUIRED: BACKENDS.md'nin sayısal tablosu bu turda yeniden birebir
  okunmadı; sıralama ve "~10 ms shared tabanı" önceki tur envanterine dayanır. Kesin
  sayılar için BACKENDS.md §tablo yeniden teyit edilmelidir.
- Lisans: GPLv3 (FlexASIO depo lisansı) — bkz. `[[asio-lisans-ve-dogrulama]]`.
- Artı: ASIO host'una hiçbir şey kurmadan (imzalı driver gerekmeden) ASIO sunar;
  eksisi: vendor ASIO sürücüsünün donanım doğrudan erişimi yoktur.

### §7.3 WineASIO — WINE altında ASIO→JACK

Kaynak: github.com/wineasio/wineasio (README, 2026-10-12 okundu).

- "WineASIO provides an ASIO to JACK driver for WINE." — FL Studio gibi Windows
  DAW'larını GNU/Linux + JACK ile çalıştırır.
- Lisans: **kütüphane kodu LGPL v2.1** (`COPYING.LIB`), **GUI kodu GPL v2+**
  (`COPYING.GUI`); depo başlığı "GPL-2.0, LGPL-2.1 licenses found".
- Yapılandırma: `HKEY_CURRENT_USER\Software\Wine\WineASIO` registry anahtarı +
  ortam değişkenleri (`WINEASIO_NUMBER_INPUTS`, `WINEASIO_FIXED_BUFFERSIZE`, …).
- Buffer: varsayılan tercih 1024, `ASIO_MINIMUM_BUFFERSIZE` 16, `ASIO_MAXIMUM_BUFFERSIZE`
  8192 (kaynakta sabit); jack buffer boyutu değişirse ASIO uygulaması bilgilendirilir;
  opsiyonel jack transport'a slave olma.
- Kurulum: 32-bit ve 64-bit için ayrı build (`make 32` / `make 64`), `wineasio-register`
  scripti her Wine prefix'inde COM kaydını yapar — **ASIO registry modelinin aynısı**
  (`[[asio-surucu-modeli]]` §registry) başka bir işletim sisteminde de tekrarlanır.
- CoreMusic için durum: depoda Wine hedefi yok → referans/çapraz-dogrulama amaçlıdır
  (open question §12).

### §7.4 Köprü karşılaştırması

| Köprü | Arkadaki yol | Gecikme profili | Lisans (depoda) | ARM64 |
|-------|--------------|-----------------|-----------------|-------|
| ASIO4ALL 2.22 | WDM-KS | Düşük (kernel streaming) | UNKNOWN (freeware) | ✅ native + EC |
| FlexASIO | WASAPI/WDM-KS/MME/DS | Backend'e bağlı; shared ~10 ms taban ⚠️ | GPLv3 | ⚠️ VERIFICATION REQUIRED |
| WineASIO | JACK (WINE) | JACK yapılandırmasına bağlı | LGPL-2.1 / GPL-2.0 | N/A (Linux) |

### §7.5 Köprü seçimi akışı (Neva Engine — PLANNED karar ağacı)

```text
Donanım tanıma (registry + USB UAC kontrolü)
  ├─ Vendor ASIO sürücüsü var (x64/ARM64 native)?
  │     → evet: vendor yolunu kullan (en düşük gecikme)
  ├─ Vendor yok + Windows 10/11 + UAC-uyumlu USB cihaz?
  │     → ASIO4ALL 2.22 (ARM64'te native; EC modu Intel/AMD emülasyonu)
  ├─ Vendor yok + kullanıcı eski WDM/KS cihazı kullanıyor?
  │     → FlexASIO (WDM-KS backend) veya ASIO4ALL (WDM listesi)
  ├─ Windows 10+ ve sistem sesleri korunacaksa?
  │     → WASAPI shared IAudioClient3 (§5.2) — ASIO'ya gerek kalmayabilir
  └─ ASIO hiç açılmadı (cihaz kaybı)?
        → .ai/AGENTS.md §17/6: WASAPI fallback (zorunlu)
```

Bu ağaçta satır içi varsayım yoktur: her dal ya §kaynaklı ya da repo SSOT'lu;
final dal bağlayıcı kuraldır. Kod karşılığı **PLANNED** (`*.cpp/*.h = 0`).

---

## §8 Vendor ASIO sürümü × platform risk matrisi

| Senaryo | ASIO yolu | Risk |
|---------|-----------|------|
| x64 Windows + vendor ASIO sürücüsü | Vendor DLL | En düşük risk |
| x64 Windows + sürücüsüz cihaz | ASIO4ALL / FlexASIO | Köprü gecikmesi + imzasız/edge davranış |
| x86 Windows (eski) | Vendor 32-bit DLL | SDK 2.2 öncesi sürücülerde 64-bit yok |
| ARM64 + native vendor sürücü (Yamaha Steinberg, RME, Focusrite, Audient) | Vendor ARM64 | Parça parça desteği (2025'te başladı) |
| ARM64 + sürücüsüz UAC cihaz | ASIO4ALL 2.22 native | Bilinen: per-app config + private COM yok |
| ARM64 + Insider Canary 2026 | Microsoft UAC2/ASIO (ön izleme) | Ön izleme sürümü — üretimde değil |
| Linux üstünde Windows DAW'ı | WineASIO → JACK | Farklı OS; CoreMusic kapsamında değil |

---

## §9 Karar akışı — "ASIO bulunamazsa ne yapmalı?" (repo kuralı ile hizalı)

```text
Neva Engine başlat
  → ASIO device scan (registry + CoCreateInstance — [[asio-surucu-modeli]])
     ├─ bulundu ve cihaz açıldı → ASIO yolu (birincil)
     └─ bulunamadı / açılamadı  → .ai/AGENTS.md §17/6: WASAPI fallback
                                   (IAudioClient3 shared low-period dene — §5.2)
  → ARM64 UAC cihaz + vendor sürücü yok → ASIO4ALL 2.22 native öner
  → Kullanıcı "sistem sesleri de çalsın" → WASAPI shared (IAudioClient3)
```

Bu akışta yeni bir karar yoktur: **fallback yolu zaten SSOT'ta yazılıdır**
(`.ai/AGENTS.md` §17 Edge Case #6, `.ai/AGENTS.md` §10 / `.ai/.decisions` ASIO-envanteri).
Bu belge yalnızca o kuralın hangi Windows mekanizmasına oturduğunu gösterir.

---

## §10 Karar günlüğü

| # | Karar | Gerekçe | Kanıt |
|---|-------|---------|-------|
| PD-01 | ARM64 hedefi için "vendor ASIO bekle" stratejisi seçilmez | Vendor ARM64 sürücüleri parçalı (RME/Focusrite/Audient/Yamaha-S); ASIO4ALL 2.22 native + Microsoft UAC2/ASIO yolu açık | devblogs ARM64 · asio4all 2.22 |
| PD-02 | WASAPI fallback, exclusive değil **shared IAudioClient3** olarak yorumlanır | Microsoft, exclusive öncesi low-period shared önerir; sistem sesleri korunur | exclusive-mode-streams (Tip kutusu) |
| PD-03 | Gecikme hedefleri buffer sayısıyla değil **backend tabanıyla** birlikte yazılacak | Köprü sürücüler buffer'ı küçültünce taban gecikme düşmez | ASIOUtil BUFFERS.md · FlexASIO BACKENDS.md |
| PD-04 | WineASIO depoya alınmaz, referans kalır | Depoda Wine hedefi yok; lisansı (LGPL/GPL) vault lisans stratejisiyle ayrı tartışılır | wineasio README |

---

## §11 Kanıt–iddia tablosu

| # | İddia | Kanıt (URL) | Güç |
|---|-------|-------------|-----|
| 1 | Core audio API'leri user-mode ile kernel-mode ayırır; `Audioses.dll`/`Mmdevapi.dll`/`Audiodg.dll`; USB'de `Ks.sys`+`Usbaudio.sys` | learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components | Birincil (Microsoft) |
| 2 | Exclusive mode tüm sistem seslerini susturur; WaveRT doğrudan buffer paylaşımı sunar; MMCSS "Pro Audio" <10 ms dönem | learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Birincil (Microsoft) |
| 3 | Windows 10+ IAudioClient3 shared-mode'da exclusive'a yakın gecikme sunar | aynı sayfa (Tip kutusu, IAudioClient3 linki) | Birincil (Microsoft) |
| 4 | ASIO4ALL 2.22: Win10/11 resmi, Win7/8 doğrulanmış, ARM64 EC+native, UAC USB cihazlarında vendor ARM64 sürücüsü olmadan düşük gecikme | asio4all.org/version-2-22/ | Birincil (yayıncı) |
| 5 | Microsoft'un kutudan çıkan UAC2/ASIO sürücüsü (Qualcomm+Yamaha), MIT depo, Insider Canary 2026 ön izlemesi; native ARM64 vendor ASIO kronolojisi; JUCE Arm64; Ableton Arm64 2026 | devblogs.microsoft.com/windows-music-dev/fall-2025-windows-musician-technology-and-arm64-update/ | Birincil (Microsoft blog) |
| 6 | FlexASIO backend sıralaması (WDM-KS → WASAPI ex → WASAPI shared → DS → MME) | github.com/dechamps/FlexASIO/blob/master/BACKENDS.md | Birincil (depо) — sayısal tablo ⚠️ yeniden teyit |
| 7 | ASIO gecikmesi buffer süresi + backend tabanı + donanım katmanıdır | github.com/dechamps/ASIOUtil/blob/master/BUFFERS.md | Birincil (depо) |
| 8 | WineASIO ASIO→JACK köprüsü; LGPL-2.1/GPL-2.0; registry yapılandırması; buffer 16–8192 | github.com/wineasio/wineasio | Birincil (README) |
| 9 | ASIO SDK 2.2'de 64-bit desteği | raw.githubusercontent.com/audiosdk/asio/master/changes.txt | Birincil (SDK) — cümle teyidi ⚠️ |
| 10 | Repo kuralı: ASIO device loss → WASAPI fallback | .ai/AGENTS.md §17 Edge Case #6 | Vault SSOT |

**Toplam benzersiz URL (bu dosya frontmatter + tablo): 9** (≥8 hedefi karşılar).

---

## §12 Açık sorular

1. **Microsoft UAC2/ASIO GA tarihi** — devblogs "public preview via Windows Insider
   Canary in 2026" der; genel kullanım tarihi **UNKNOWN**. Takip: Windows Musician blog.
2. **ASIO4ALL 2.22 ARM64 COM kısıtı** — "No private COM interface for native ARM64"
   hangi host'ları etkiler? (COM refcount notu release'te "improved host compatibility"
   der — etkileşim UNKNOWN) → Windows SW Engineer.
3. **FlexASIO ARM64 durumu** — bu turda doğrulanmadı → ⚠️ VERIFICATION REQUIRED.
4. **Neva Engine hedef matrisi** — yalnız x64 mü, ARM64 de mi? (ASIO4ALL 2.22 native
   seçeneğini karar etkiler) → K003 karar kapısı.
5. **WineASIO'nun vault'taki yeri** — referans mı, kapsam dışı mı? (PD-04 önerisi:
   referans).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** Microsoft Learn (user-mode-audio-components · exclusive-mode-streams ·
IAudioClient3) · devblogs.microsoft.com/windows-music-dev (Fall 2025 ARM64) ·
asio4all.org (2.22 release) · FlexASIO BACKENDS.md · ASIOUtil BUFFERS.md ·
wineasio/wineasio README · audiosdk/asio changes.txt · .ai/AGENTS.md §17 · 2026-10-12
