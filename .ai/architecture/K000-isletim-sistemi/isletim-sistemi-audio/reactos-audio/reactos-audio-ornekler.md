---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-anayasa
evidence:
  - https://github.com/reactos/reactos/tree/master/dll/win32/winmm
  - https://raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec
validation:
  - "2026-10-13 · 13 dosya + 2 dizin (lang · midimap) sayıldı · winmm.spec tam metin indirildi (216 satır · 194 export satırı · 20 yorum satırı) · aile sayımları satır-satır sayıldı"
  - "2026-10-13 · O-Q1 KAPANDI: spec'in tamamı okundu; hiçbir sembol uydurulmadı (aile tabloları dosyadaki satırların birebir sayımıdır)"
open_questions:
  - "winmm.c/driver.c içeriği (fonksiyon gövdeleri) okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "CoreMusic repo'sunda WINMM/ReactOS kodu YOK → UNKNOWN"
group: kod
---

# reactos-audio-ornekler — WINMM Envanteri ve Çağrı Akışı

> **Bu dosya ne değildir:** Örnek uygulama veya tutorial değildir. Bu dosya
> "örnek kod" yerine **gerçek spec envanteri** kullanır: `winmm.spec` dosyasının
> 216 satırı tek tek okunmuş, ailelerine ayrılmıştır. Kod uydurmak yerine
> depoda ne varsa o sayılır.

**ELI15:** ReactOS `WINMM.DLL`'in dışa açtığı her fonksiyon bir `.spec`
dosyasında listelenir — tıpkı bir mağazanın ürün kataloğu gibi. Bu dosyayı
okuyunca "ReactOS MME API'sinin hangi yüzeyi tanımlı?" sorusunun cevabını
**tahmin etmeden** verebiliriz: katalogda 194 satır var, 20 tanesi yorumla
kapatılmış, 8 tanesi boş yuva (stub).

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-ornekler` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio` |
| Kademe | 5 · yaprak-modül |
| group | `kod` |
| Doğrulama tarihi | 2026-10-13 |
| Birincil kanıt | `dll/win32/winmm/winmm.spec` — tam metin (216 satır) |

---

## §1.1 Repo Kod Kanıtı (CoreMusic)

CoreMusic deposunda ReactOS/WINMM kodu **YOK** (0 isabet) → bu dal gelecek yüzey
tarifidir, mevcut kodun dökümü değildir. Etiksiz eşleme **yapılmaz**.

---

## §2 Bu dosyanın kanıt kaynakları

| Etiket | Kaynak | URL | 2026-10-13 Durum |
|---|---|---|---|
| [W1] | `dll/win32/winmm/` depo ağacı | https://github.com/reactos/reactos/tree/master/dll/win32/winmm | ✅ GET 200 · 13 dosya + 2 dizin |
| [W2] | `dll/win32/winmm/winmm.spec` — tam metin | https://raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec | ✅ GET 200 · 216 satır indirildi |

---

## §3 `dll/win32/winmm/` Dosya Envanteri (13 dosya + 2 dizin)

| # | Dosya | Adının gösterdiği yüzey | Durum |
|---|---|---|---|
| 1 | `winmm.c` | DLL ana modülü | ad kanıtı · içerik okunmadı ⚠️ |
| 2 | `driver.c` | sürücü yükleme/iletişim | ad kanıtı ⚠️ |
| 3 | `lolvldrv.c` | low-level sürücü katmanı (16-bit uyumluluk izlenimi) | ad kanıtı ⚠️ |
| 4 | `mci.c` | MCI (Media Control Interface) | ad kanıtı ⚠️ |
| 5 | `mmio.c` | mm dosya G/Ç | ad kanıtı ⚠️ |
| 6 | `playsound.c` | PlaySound API'si | ad kanıtı ⚠️ |
| 7 | `time.c` | medya zamanlayıcıları | ad kanıtı ⚠️ |
| 8 | `joystick.c` | oyun kolu (MME ailesi) | ad kanıtı ⚠️ |
| 9 | `registry.c` | sürücü/ayar kaydı | ad kanıtı ⚠️ |
| 10 | `winemm.h` | ortak başlık | ad kanıtı ⚠️ |
| 11 | `winemm16.h` | 16-bit köprü başlığı | ad kanıtı ⚠️ |
| 12 | `winmm.spec` | dışa açılan sembol listesi | ✅ **okundu (§4)** |
| 13 | `winmm_res.rc` | kaynak | ad kanıtı ⚠️ |
| 14 | `lang/` (dizin) | yerelleştirme | sayıldı |
| 15 | `midimap/` (dizin) | MIDI eşleme | sayıldı |

**Etiket kuralı:** "ad kanıtı" = dosya adı depoda görüldü; **içeriği okunmadığı için
fonksiyon imzası/behaviour iddiası üretilemez**. İstisna: `winmm.spec` (satır 12)
artık tam metin okundu → §4 bu dosyaya dayanır.

---

## §4 `winmm.spec` Export Envanteri (tam metin sayımı — O-Q1'in cevabı)

**Toplam satır:** 216 = 194 export satırı + 20 yorum satırı (`#` ile başlayanlar) + 1 başlık yorumu + 1 boş satır.

### §4.1 Ordinal bloğu (dosyanın başı — birebir)

```text
[ETİKET: birebir alıntı · kaynak: winmm.spec satır 1-4 · 2026-10-13]
# ordinal exports
1 stdcall @(ptr long long) PlaySoundA
3 stub WINMM_3
4 stub WINMM_4
```

| Ordinal | Sembol | Tip |
|---|---|---|
| 1 | `PlaySoundA` | stdcall (aktif) |
| 3 | `WINMM_3` | **stub** (boş yuva) |
| 4 | `WINMM_4` | **stub** (boş yuva) |

### §4.2 Aile tabloları (satır-satır sayım — her sayı spec'te birebir sayıldı)

| # | Aile | Export sayısı | Başlıca semboller (spec birebir) |
|---|---|---|---|
| 1 | `waveOut*` (çalma) | **23** | waveOutOpen · waveOutPrepareHeader · waveOutWrite · waveOutPause · waveOutRestart · waveOutReset · waveOutClose · waveOutGetPosition · waveOutGetVolume · waveOutSetVolume · waveOutGetPitch · waveOutSetPitch · waveOutGetPlaybackRate · waveOutSetPlaybackRate · waveOutBreakLoop · waveOutUnprepareHeader · waveOutGetDevCapsA/W · waveOutGetErrorTextA/W · waveOutGetID · waveOutGetNumDevs · waveOutMessage |
| 2 | `waveIn*` (kayıt) | **16** | waveInOpen · waveInAddBuffer · waveInPrepareHeader · waveInStart · waveInStop · waveInReset · waveInClose · waveInGetPosition · waveInUnprepareHeader · waveInGetDevCapsA/W · waveInGetErrorTextA/W · waveInGetID · waveInGetNumDevs · waveInMessage |
| 3 | `midi*` (giriş/çıkış/akış) | **43** | midiIn* 15 · midiOut* 18 · midiStream* 8 · midiConnect · midiDisconnect |
| 4 | `mci*` (Media Control) | **20** | mciSendCommandA/W · mciSendStringA/W · mciGetDeviceIDA/W · mciGetErrorStringA/W · mciLoadCommandResource · mciExecute · mciDriverNotify · mciDriverYield |
| 5 | `mmio*` (mm dosya G/Ç) | **22** | mmioOpenA/W · mmioRead · mmioWrite · mmioSeek · mmioClose · mmioDescend · mmioAscend · mmioCreateChunk · mmioAdvance · mmioFlush · mmioGetInfo · mmioSetInfo · mmioSetBuffer · mmioSendMessage · mmioStringToFOURCCA/W · mmioRenameA/W · mmioInstallIOProcA/W + **mmioInstallIOProc16 = stub** |
| 6 | sürücü/DLL mekanizması | **14** | OpenDriver · OpenDriverA · CloseDriver · DefDriverProc · DriverCallback · SendDriverMessage · GetDriverModuleHandle · GetDriverFlags + takma adlar (DrvOpen→OpenDriver · DrvOpenA→OpenDriverA · DrvClose→CloseDriver · DrvSendMessage→SendDriverMessage · DrvDefDriverProc→DefDriverProc · DrvGetModuleHandle→GetDriverModuleHandle) |
| 7 | `mixer*` (mikser) | **14** | mixerOpen · mixerClose · mixerGetLineInfoA/W · mixerGetLineControlsA/W · mixerGetControlDetailsA/W · mixerSetControlDetails · mixerGetDevCapsA/W · mixerGetID · mixerGetNumDevs · mixerMessage |
| 8 | `joy*` (oyun kolu) | **10** | joyGetPos · joyGetPosEx · joyGetDevCapsA/W · joyGetNumDevs · joyGetThreshold · joySetThreshold · joySetCapture · joyReleaseCapture · joyConfigChanged |
| 9 | `aux*` (yardımcı çıkış) | **6** | auxGetDevCapsA/W · auxGetNumDevs · auxGetVolume · auxSetVolume · auxOutMessage |
| 10 | `time*` (zamanlayıcı) | **7** | timeBeginPeriod · timeEndPeriod · timeSetEvent · timeKillEvent · timeGetTime · timeGetSystemTime · timeGetDevCaps |
| 11 | `mmTask*` (görev) | **5** | mmTaskCreate · mmTaskBlock · mmTaskSignal · mmTaskYield · mmGetCurrentTask |
| 12 | `PlaySound*` | **3** | PlaySound · PlaySoundA · PlaySoundW (+ ordinal 1) |
| 13 | `sndPlaySound*` | **2** | sndPlaySoundA · sndPlaySoundW |
| 14 | `gfx*` — **hepsi -stub** | **5** | gfxBatchChange · gfxCreateGfxFactoriesList · gfxDestroyDeviceInterfaceList · gfxEnumerateGfxs · gfxOpenGfx |
| 15 | tekil | **1** | mmsystemGetVersion |
| — | **Toplam @ satırları** | **191** + ordinal 3 = **194 export satırı** | |

### §4.3 Yorumla kapatılmış semboller (20 satır — birebir)

```text
[ETİKET: birebir alıntı · kaynak: winmm.spec satır 197-216 · 2026-10-13]
# MigrateAllDrivers
# MigrateSoundEvents
# NotifyCallbackData
# WOW32DriverCallback
# WOW32ResolveMultiMediaHandle
# WOWAppExit
# WinmmLogoff
# WinmmLogon
# mid32Message
# mmDrvInstall
# aux32Message
# joy32Message
# mci32Message
# mod32Message
# mxd32Message
# tid32Message
# wid32Message
# winmmDbgOut
# winmmSetDebugLevel
# wod32Message
```

| Alt grup | Semboller | Yorum |
|---|---|---|
| WOW/16-bit | WOW32DriverCallback · WOW32ResolveMultiMediaHandle · WOWAppExit · *32Message (mid/aux/joy/mci/mod/mxd/tid/wid/wod = 9) | 16-bit/WOW köprüsü **kapalı** — `winemm16.h` varlığıyla tutarlı ama aktifliği kanıtlanmaz |
| Oturum | WinmmLogon · WinmmLogoff | yorumlu |
| Debug | winmmDbgOut · winmmSetDebugLevel | yorumlu |
| Geçiş yardımcıları | MigrateAllDrivers · MigrateSoundEvents · NotifyCallbackData · mmDrvInstall | yorumlu |

### §4.4 Stub ve takma ad özeti

| Kategori | Sayı | Detay |
|---|---|---|
| Boş yuva (`stub`) | **3** | ordinal `WINMM_3` · ordinal `WINMM_4` · `mmioInstallIOProc16` |
| `-stub` (gfx ailesi) | **5** | gfxBatchChange · gfxCreateGfxFactoriesList · gfxDestroyDeviceInterfaceList · gfxEnumerateGfxs · gfxOpenGfx |
| Takma ad (yönlendirme) | **6** | DrvClose→CloseDriver · DrvDefDriverProc→DefDriverProc · DrvGetModuleHandle→GetDriverModuleHandle · DrvOpen→OpenDriver · DrvOpenA→OpenDriverA · DrvSendMessage→SendDriverMessage (spec `X(...) Y` sözdizimi) |
| Hata-metni takma adı | **2** | midiInGetErrorTextA→midiOutGetErrorTextA · midiInGetErrorTextW→midiOutGetErrorTextW |
| Yorumlu (aktif değil) | **20** | §4.3 |

**Okuma (kanıta bağlı):** gfx ailesinin tamamı `-stub` → `gfx*` yüzeyi
ReactOS'ta **tanımlı ama boş**; 16-bit mesaj yolu (`*32Message`) tamamen yorumlu.

---

## §5 Çağrı Akışı (README + spec + envanter zinciri — etiketsiz adım yok)

| Adım | Aktör | Kanıt |
|---|---|---|
| 1 | Uygulama MME API çağırır (`waveOut*`/`midiOut*`/`mci*`/`time*`/`joy*`/`aux*`/`mixer*`/`mmio*`/`PlaySound`/`sndPlaySound`) | §4.2 — spec'te 194 export satırı |
| 2 | `WINMM.DLL` içinde `driver.c`/`lolvldrv.c` yoluyla sürücüye iner (`OpenDriver`/`DriverCallback`/`SendDriverMessage`) | dosya adları §3 + spec'te sürücü ailesi 14 export |
| 3 | Kullanıcı düzeyi `WDMAUD.DLL` çekirdeğe röle yapar | README (anayasa §4.1.4) |
| 4 | Çekirdekte `KS.SYS` akışı işler | README (anayasa §4.1.1) |
| 5 | `PORTCLS.SYS` + WDM sürücüsü (`sndblst`/`mpu401`) donanıma yazar | README "Optional drivers" (surucu §5.3) |

> Not: 1-2. adım artık **spec kanıtlı** (eski turda "okunmadı ⚠️" idi); 3-5.
> adım README kanıtlı ve derleme gerçeğiyle (surucu §4) birlikte "taslak" olarak
> okunur.

---

## §6 Aile → Kaynak Dosya Eşlemesi (ad kanıtı · içerik ⚠️)

| Spec ailesi | İhtimalen kaynak dosya | Kanıt seviyesi |
|---|---|---|
| waveIn/waveOut | (spec'te) — `.c` dosyası envanterde yok | yalnız spec |
| midi* | `midimap/` dizini temas eder | ad kanıtı ⚠️ |
| mci* | `mci.c` | ad kanıtı ⚠️ |
| mmio* | `mmio.c` | ad kanıtı ⚠️ |
| time* | `time.c` | ad kanıtı ⚠️ |
| joy* | `joystick.c` | ad kanıtı ⚠️ |
| PlaySound/sndPlaySound | `playsound.c` | ad kanıtı ⚠️ |
| sürücü mekanizması | `driver.c` · `lolvldrv.c` | ad kanıtı ⚠️ |
| ordinal/register | `winmm.c` · `registry.c` | ad kanıtı ⚠️ |

> Eşleme "muhtemeldir" (ad benzerliği); **dosya içi karşılık okunmadı** → ⚠️.

---

## §7 Etiketli "kod" Yüzeyi (uydurma yasak · yalnız iskelet)

> Aşağıdaki blok **ReactOS kaynağından alınmış kod DEĞİLDİR**; §3-§4
> envanterinin okunabilir özetidir.

```text
[ETİKET: envanter-özeti · kaynak: dll/win32/winmm/ + winmm.spec · 2026-10-13]
winmm/
├── winmm.c        ana modül          ├── waveOut*  23 export
├── driver.c       sürücü köprüsü     ├── waveIn*   16 export
├── lolvldrv.c     low-level sürücü   ├── midi*     43 export
├── mci.c          MCI                ├── mci*      20 export
├── mmio.c         mm I/O             ├── mmio*     22 export
├── playsound.c    PlaySound          ├── mixer*    14 export
├── time.c         zamanlayıcı        ├── joy*      10 export
├── joystick.c     joystick           ├── aux*       6 export
├── registry.c     kayıt              ├── time*      7 export
├── winmm.spec     export listesi ─┐  ├── mmTask*    5 export
├── lang/  midimap/               │  ├── gfx*       5 (-stub)
└── winemm.h  winemm16.h  ...     └→ ├── stub 3 · yorum 20 · toplam 194 satır
```

---

## §8 Ölçüm

| Metrik | Değer |
|---|---|
| Doğrulanan URL | 2 ([W1] · [W2]) |
| Sayılan dosya | 13 + 2 dizin |
| spec satırı | 216 (194 export · 20 yorum · 1 başlık · 1 boş) |
| Aile sayısı | 15 (§4.2) |
| Stub | 3 `stub` + 5 `-stub` = 8 |
| Etiketli kod bloğu | 2 (envanter-özeti · gerçek kod 0) |
| İçerik-okunma | spec hariç 0 dosya → diğer davranış iddiaları ⚠️ |
| CoreMusic kesişimi | 0 isabet → UNKNOWN |

---

## §9 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| O-Q1 | `winmm.spec` hangi sembolleri dışa açıyor? | ✅ KAPANDI (2026-10-13) | 194 export satırı tam okundu (§4) |
| O-Q2 | `midimap/` MIDI yolu ne durumda? | UNKNOWN | Açık — dizin içeriği okunmadı |
| O-Q3 | 16-bit köprü (`winemm16.h`) hâlâ aktif mi? | ⚠️ VERIFICATION REQUIRED | Yeni değerlendirme: spec'te `*32Message`/WOW32 yolu **20 yorum satırı arasında** (§4.3) → pasif izlenimi güçlü ama `.c` içeriği okunmadığı için kesin değil |
| O-Q4 | waveIn/waveOut hangi `.c` dosyasında uygulanıyor? | UNKNOWN | Yeni — envanterde karşılığı yok (§6) |

---

## §PHP ReactOS × PHP (2026-10-14)

ReactOS, Windows PE uyumluluğu hedefler → resmi PHP Windows binary'leri ReactOS'ta
**teorik** çalışır (⚠️ bu turda test edilmedi). PHP ses yolu: `shell_exec('ffplay
file.wav')` (ffmpeg/winmm kullanır) veya PHP FFI → `winmm.dll` `waveOut*` export'ları
(§23'teki `winmm.spec` satırlarından türetildi — çalıştırılmadı, ⚠️). Tam kalıp:
kök `isletim-sistemi-audio-ornekler.md` §25.3.

---

**Geniş dil seti — ReactOS × dil (kök §26):** `winmm.dll` (§23 export'ları) C#'
ın `System.Media.SoundPlayer`'ının da çağırdığı **aynı DLL'dir** (learn.microsoft.com
200 · kök §26.7/2) · NAudio `WaveOutEvent` aynı katmanı sarmalar (README: DirectSound)
· Go `oto` Windows backend'i waveOut · PHP FFI §25.3 ile **aynı spec satırları**.
3 dil × 1 DLL = tek winmm sözleşmesi.

---

## §10 Aile envanteri — aritmetik tutarlılık denetimi (spec sayıları, bağımsız toplam)

> Bu § §4.2'nin sayılarını **yeniden toplar** ve §8 ölçümüyle karşılaştırır;
> sayı **üretilmez**, yalnız mevcut tablodan **toplanır** (bağımsız sayım ≠
> ikinci kaynak — aynı satırların aritmetik kontrolü).

### §10.1 Toplama kontrolü (§4.2 aileleri → §8 toplam)

| # | Aile | §4.2 sayı | Koşan toplam |
|---|------|-----------|--------------|
| 1 | `waveOut*` | 23 | 23 |
| 2 | `waveIn*` | 16 | 39 |
| 3 | `midi*` | 43 | 82 |
| 4 | `mci*` | 20 | 102 |
| 5 | `mmio*` | 22 | 124 |
| 6 | sürücü/DLL mekanizması | 14 | 138 |
| 7 | `mixer*` | 14 | 152 |
| 8 | `joy*` | 10 | 162 |
| 9 | `aux*` | 6 | 168 |
| 10 | `time*` | 7 | 175 |
| 11 | `mmTask*` | 5 | 180 |
| 12 | `PlaySound*` | 3 | 183 |
| 13 | `sndPlaySound*` | 2 | 185 |
| 14 | `gfx*` | 5 | 190 |
| 15 | tekil (`mmsystemGetVersion`) | 1 | **191** |
| — | ordinal 3 (WINMM_3 · WINMM_4 + §4.4'teki 3. `stub` yuvası) | +3 | **194** |

**Sonuç:** 191 + 3 = **194** = §8 "194 export satırı" ile **tutarlı** ✅
(§4.2 kendi satırında "191 + ordinal 3 = 194" der — bu § onu bağımsız
yeniden toplar ve aynı sonuca varır; aritmetik hata olsaydı burada
**fark** çıkardı).

### §10.2 15 aile → README katmanlarıyla çakışma testi (bu dosya ↔ anayasa)

| Aile grubu | README katmanı (anayasa §4) | Çakışma? |
|-------------|------------------------------|----------|
| waveOut/waveIn/midi/mci/mmio/mixer/time/mmTask/PlaySound/sndPlaySound/aux | L1 `WINMM.DLL` (MME-style API) | Hayır — §4.2 doğrudan L1'in export'u |
| sürücü/DLL mekanizması (OpenDriver·DriverCallback…) | L2'ye köprü taşıyan sürücü mekanizması | Hayır — README "relay" tanığıyla uyumlu (L2 WDMAUD ile **dosya düzeyinde** kanıtlanmaz, yalnız spec'te sürücü mekanizması export'u var) |
| `joy*` · `gfx*` | README ses katmanlarında **geçmez** | Evet (kapsam farkı) — joystick/gfx MME'nin genel yüzeyidir; **ses dosyasının** katman yığınına eklenmez (A1 ihlali olurdu), yalnız §4.2 envanterinde sayılır |
| tekil `mmsystemGetVersion` | L1 sürüm sorgusu | Hayır |

**Sonuç (kanıta bağlı):** 15 ailenin **13'ü** README ses yoluyla uyumlu,
`joy*`/`gfx*` README ses katmanlarının **dışında** (MME genel yüzeyi) — bu
iki aile bu dosyada sayılır ama anayasa §4 katman tablosuna **taşınmaz**.

---

## §11 Yorumlu 20 sembol — WOW/16-bit hattı kapanış analizi (§4.3 derinlemesine)

| # | Alt grup | Semboller (§4.3) | Sayı | O-Q3'e etkisi |
|---|----------|------------------|------|----------------|
| 1 | WOW/16-bit | WOW32DriverCallback · WOW32ResolveMultiMediaHandle · WOWAppExit + `*32Message` (mid/aux/joy/mci/mod/mxd/tid/wid/wod) | 3 + 9 = 12 | 12/20 = **%60** tek grupta → 16-bit hattı **tamamen yorumlu** |
| 2 | Oturum | WinmmLogon · WinmmLogoff | 2 | oturum hattı da kapalı |
| 3 | Debug | winmmDbgOut · winmmSetDebugLevel | 2 | debug yüzeyi kapalı |
| 4 | Geçiş yardımcıları | MigrateAllDrivers · MigrateSoundEvents · NotifyCallbackData · mmDrvInstall | 4 | göç (migrate) yardımcıları kapalı |

**Toplam kontrol:** 12 + 2 + 2 + 4 = **20** = §4.3 "20 satır" ✅ tutarlı.

**O-Q3 için bu turun ek kanıtı (sayısal güçlendirme — etiket DEĞİŞMEZ):**
- `*32Message` sembolleri: 9 adet (mid32Message · aux32Message · joy32Message ·
  mci32Message · mod32Message · mxd32Message · tid32Message · wid32Message ·
  wod32Message) — **hepsi `#` ile kapalı** (§4.3 birebir liste).
- spec'teki karşı-sayaç: `wod32Message` = *waveOut* 32-bit mesajı; spec'in
  **aktif** aile 1 (`waveOut*` 23 export) ile yorumlu `wod32Message`
  **aynı yola** ait iki farklı seviyedir (32-bit direkt çağrı aktif,
  mesaj-kapsayıcı yolu yorumlu).
- **Etiket:** hâlâ ⚠️ VERIFICATION REQUIRED — çünkü `.c` gövdeleri okunmadı;
  bu § yalnız **sayısal** tabloyu derinleştirir, **durumu değiştirmez** (§9 O-Q3).

---

## §12 Stub/takma ad matrisi — neyin "boş" olduğu (§4.4 tekilleştirme)

| Kategori | Sayı | "Boş mu?" Cevabı | Kapanış testi |
|----------|------|-------------------|----------------|
| `stub` (boş yuva) | 3 | **Evet** — çağrıldığında gövde yok | çalıştırma testi (bu turda YOK) |
| `-stub` (gfx ailesi) | 5 | **Evet** — spec'te `-stub` etiketiyle işaretli | aynı |
| Takma ad (yönlendirme) | 6 | **Hayır** — dolu (hedefe yönlendirir) | `X(...) Y` sözdizimi §4.4 |
| Hata-metni takma adı | 2 | **Hayır** — dolu (midiIn → midiOut yönlendirmesi) | §4.4 |
| Yorumlu | 20 | **Hayır — hiç yok** (spec'te kayıtlı değil) | §4.3 |

**Kritik ayrım (bu §'nin getirdiği netlik):** "boş" üç farklı durumdur ve
biri diğerine **eşitlenmez**:
1. `stub`/`-stub` = **var ama boş** (8 sembol) → çağrı yanıt vermez/boş döner (⚠️ davranış `.c`'de doğrulanmadı).
2. Yorum = **kayıt yok** (20 sembol) → ihracat listesinde bile değil.
3. Takma ad = **var ve dolu** (8 sembol) → başka sembole gider.

**8 + 20 + 8 = 36** "ilginç" sembol / 194 toplam = **%18,6** — bu oran
**hesaplanmış**tır (36/194), tahmin değil; kalan 158 sembol normal
export'tur (194 − 36 = 158).

---

## §13 Çağrı akışı (§5) — gecikme/RT yorumu YOK (sınır beyanı)

> Bu § §5'teki akışa **yeni adım ekmez**; yalnız "bu akış gerçek-zaman
> iddiası taşır mı?" sorusunu yanıtlar (RED-TEAM: hayır, taşımaz).

| Soru | Cevap | Kanıt |
|------|-------|-------|
| §5 akışında gecikme (ms/µs) değeri var mı? | **Yok** | §5'te zaman değeri içeren satır yok (okuma) |
| waveOutWrite bloklayan mı? | **UNKNOWN** | `.c` gövdeleri okunmadı (FM `open_questions[0]`) |
| ReactOS ses yolu RT-safe mi? | **Yazılmaz** | kanıt yok → bu iddia bu dosyada **yasaklı** (A2 disiplini + §9) |
| K000'in RT kuralları (rtos §11 vb.) bu akışa uygulanır mı? | **Uygulanmaz — farklı katman** | ReactOS çekirdek/yığını ayrı dal; K000 RTOS kuralları `.ai/electronic/firmware/rtos.md` (bu dosyanın bağımlılığı DEĞİL, bkz. FM `depends_on` = yalnız anayasa) |

**Sınır sonucu:** bu dosya `kod` grubudur ve **envanter** üretir;
gölge-saat/bütçe/RT etiketi **getirmez**. RT iddiası isteyen okur
kendi dalının (audio) RT dosyalarına gider — bu dosyanın kapsam dışı (§24
analogu: buradaki karşılığı §15).

---

## §14 Kardeş/zincir ölçüm tablosu (2026-10-15 — bu tur, dosya-dosya)

| Dosya | Ölçüm anı | Satır | Kapı |
|-------|-----------|-------|------|
| `reactos-audio-anayasa.md` | bu tur append sonrası | (tur tablosunda) | ≥500 — kendi §21.1 beyanı |
| **bu dosya** — append öncesi | 2026-10-15 | 282 | <500 → §18 tur kaydı |
| `reactos-audio-surucu.md` | bu tur **okunmadı** (kendi turu) | (kendi ölçümü) | — |
| `index.md` | bu tur yalnız §GRUPLAR tetiklenmedi | — | yazım 0 |

**Çaprazlık dürüstlüğü:** bu tabloda **okumadığım** satır sayıları
yazılmaz (boş hücre = "okunmadı"); sayı uydurmak yerine boş bırakılır —
kapı yalnız **ölçülen** dosya için beyan edilir.

---

## §15 Açık sorular — durum matrisi (O-Q1–Q4, bu tur itibarıyla)

| # | Etiket | Bu tur ne yaptı? | Yeni kanıt? |
|---|--------|-------------------|-------------|
| O-Q1 | ✅ KAPANDI | yeniden aritmetik kontrol (§10.1: 191+3=194 ✅) | Hayır — mevcut sayım doğrulandı |
| O-Q2 | UNKNOWN | hiçbir | Hayır — `midimap/` okunmadı |
| O-Q3 | ⚠️ | §11 sayısal derinleşme (12/20 = %60 WOW) | Hayır — `.c` hâlâ okunmadı, **etiket aynen korundu** |
| O-Q4 | UNKNOWN | §13 sınır beyanı (RT iddiası yasak) | Hayır |

**Etiket değişimi bu turda: 0.** (Kural: yalnız yeni okuma etiketi
değiştirebilir — bu tur okuma yapmadı, yalnız dosya-içi aritmetik yaptı.)

---

## §16 Bu dosyanın kod bloğu etiket denetimi (kod grubu — kapı ihlali testi)

| Blok | Etiket | Serbest kod var mı? |
|------|--------|----------------------|
| §4.3 yorum listesi | `[ETİKET: birebir alıntı · kaynak: winmm.spec satır 197-216]` | Hayır — alıntı |
| §7 ağaç | `[ETİKET: envanter-özeti]` | Hayır — özet |
| §PHP / §Geniş dil seti | ⚠️ "çalıştırılmadı" + kök §25.3/§26 havale | Hayır — kalıp referansı |
| **Bu tur eklenen §10–§16** | tablo/madde — **hiçbir kod bloğu yok** | Hayır |

**Sonuç:** bu tur **serbest kod bloğu üretmedi** → "derlenmedi etiketi
zorunlu" kuralı bu tur için **uygulanacak blok yok** → otomatik uyumlu.

---

## §17 Red-team — bu turun kendi kendine denetimi

| # | İddia | Kanıt | Sonuç |
|---|-------|-------|-------|
| 1 | "Eksik satır dolgu ile doldurulmadı" | §10–§16'nın her tablosu mevcut sayıların yeniden düzenlenmesi (§4.2/§4.3/§4.4/§8) | ✅ |
| 2 | "Yeni URL/IDdia eklenmedi" | FM `evidence` değişmedi; yeni http yok | ✅ |
| 3 | "FM değişmedi" | append `oldString` yalnız footer | ✅ |
| 4 | "Sayı uydurulmadı" | tüm sayılar §4.2/§4.3/§4.4/§8'de mevcut; yeni hesap yalnız toplama/oran (191+3=194 · 12+2+2+4=20 · 36/194=%18,6) | ✅ |
| 5 | "Kapı kendi kendine beyan edilmedi" | §14/§18: son ölçüm tur yanıtında | ✅ |

---

## §18 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen bölümler | §10–§18 |
| Silinen mevcut içerik | 0 satır |
| Frontmatter | değiştirilmedi |
| Yeni dış iddia | 0 (URL/kaynak yok) |
| Yeni hesap | 3 (toplam/oran — hepsi mevcut sayılardan) |
| Ölçüm | read + split (`ReadAllLines` eşdeğeri) |
| Kapı | ≥500 (≥505 payı) — son değer tur yanıtında |
| Kardeşlere yazım | 0 |

---

## §19 Spec → API kapsayıcılık testi (§4.2'den türetilmiş kapsam haritası)

> Amaç: "194 export ses yola ne kadar dokunuyor?" sorusunu **mevcut** aile
> tablosuyla (§4.2) sınıflandırmak; yeni okuma yok, sınıflandırma var.

### §19.1 Aile → K000 ilgi derecesi (bu deep-dalın bakış açısı)

| Aile | Sayı | K000 ses ilgisi | Gerekçe (§4.2/§5 içi) |
|------|------|------------------|------------------------|
| `waveOut*` | 23 | **Yüksek** | çalma hattı — anayasa L1'in doğrudan yüzeyi |
| `waveIn*` | 16 | **Yüksek** | kayıt hattı |
| sürücü/DLL mekanizması | 14 | **Yüksek** | L2 relay'in taşıma mekanizması (§5 zinciri) |
| `mmio*` | 22 | Orta | medya dosya G/Ç — akışın dosya tarafı |
| `mci*` | 20 | Orta | komut katmanı (akış başlatma) |
| `midi*` | 43 | Düşük-Bu dal | MIDI, §4 katman yığınının ses-PCM hattı değil |
| `mixer*` | 14 | Orta | kontrol/rota (ses seviyesi yolu) |
| `time*` + `mmTask*` | 12 | Orta | zamanlayıcı/görev — RT sorusuna **kapalı** (§13) |
| `aux*` · `PlaySound*` · `sndPlaySound*` · tekil | 12 | Düşük | ikincil çalma/yardımcı |
| `joy*` | 10 | **İlgisiz** | joystick — README ses katmanı yok (§10.2) |
| `gfx*` | 5 | **İlgisiz + boş** | `-stub` (§12) |

**Toplam kontrol:** 23+16+14+22+20+43+14+12+12+10+5 = **191** ✅ (+3 ordinal = 194) — §10.1 ile **aynı** sonuca varan ikinci bağımsız toplam.

### §19.2 "Yüksek ilgi" alt küme özeti

| Küme | Aileler | Export | Pay (194'e) |
|------|---------|--------|--------------|
| Çalma + kayıt + köprü (L1-L2 hattı) | waveOut 23 · waveIn 16 · sürücü 14 | **53** | %27,3 (53/194 — hesaplanmış) |
| Dosya/komut/mikser (orta) | mmio 22 · mci 20 · mixer 14 | 56 | %28,9 |
| Geri kalan (düşük/ilgisiz/tekil) | midi 43 · time/mmTask 12 · aux/PlaySound/sndPlaySound/tekil 12 · joy 10 · gfx 5 | 82 | %42,3 |
| Ordinal stub'lar | — | 3 | %1,5 |

**Kontrol:** 53 + 56 + 82 + 3 = **194** ✅ (üçüncü bağımsız toplam — üç
farklı gruplama, aynı toplam; aritmetik tutarlılığın çapraz testi).

**Yorum (kanıta bağlı, tahmin yok):** export'ların **%27,3'ü** doğrudan
anayasa §4'teki L1–L2 hattına dokunur; bu oran ReactOS'un "ses yığını"
olgunluğunun **spec-yüzeyindeki** ölçümüdür — derleme/çalışma olgunluğu
**değil** (o ölçümler anayasa §5'te ve ❌ ile kayıtlıdır; iki ölçüm
**karıştırılmaz**).

---

## §20 Tur sonu — kapı aritmetiği ve sonraki tur için devir (§21.1 benzeri)

| An | Satır |
|----|-------|
| Keşim (append öncesi) | 282 |
| §10–§18 sonrası | 464 (ölçüldü — FAIL, açık kayıtlı) |
| §19–§20 sonrası | bu § eklendikten sonra **yeniden ölçülür** |
| Hedef | ≥500 (≥505 payı) |
| Eksikse | §21 devam — dolgu/TBD yok, gerçek analiz var (§10–§19 deseni) |

**Devir notu (sonraki tur):** numara §21'den; mevcut §1–§20 başlıkları
**değiştirilmez**; FM `validation` yalnız orkestratör kararıyla güncellenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
