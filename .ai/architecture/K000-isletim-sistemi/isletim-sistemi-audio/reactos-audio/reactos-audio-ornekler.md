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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
