---
title: "AIMP v5.40.2722 — Ghidra Full Decompile Dump (AIMP.Shared.dll)"
type: reverse-engineering
target: "C:\Program Files\AIMP\AIMP.Shared.dll (AIMP v5.40.2722)"
method: "Ghidra 12.1.4 headless auto-analysis via REA MCP — pseudocode decompile (FULL DECOMPILE kapsamı, private/local)"
date: 2026-10-20
status: evidence-backed
evidence:
  - "ev_beb248a9 (auto-analysis overview — önceki oturum)"
  - "ev_230d10be (SafeProcessData ilk çekim — önceki oturum)"
  - "ev_9baaa8f3887001c23cab13aca7c89dbd05bed7270e8dfd15d55654e8ee96ce13 (DoSwitchBuffer + SOXR Process)"
  - "ev_58b49482b8050f9d6a79aa67efd0181c9e308cf5f075b5c93e8325664bde3f0a (EQ çekirdeği 7 prosedür)"
  - "ev_c5d76fae4a569db18ab4afc79754bd1d3afedde8acdb3f628e643e310eeb13e6 (WASAPI liste + GetBandValue)"
  - "ev_8a671c7d648d4a0251bb5286ce11e970a1ad09d84d9851503831a0ffc63e0f68 (SafeProcessData + ResetBands)"
---

# AIMP.Shared.dll — Full Decompile Dump

> **Kapsam notu:** Önceki `aimp-dump.md` (string-seviyesi, non-decompile) DOSYASI
> DEĞİŞTİRİLMEMİŞTİR — bu dosya onun yerine geçen **decompile katmanıdır**.
> Kullanıcı talimatı: private kullanım, internette yayınlama yok, TAM FULL DECOMPILE.
> Bu dosya AIMP.Shared.dll'e ait çözülmüş prosedürleri taşır; AIMP.exe (Delphi, 7.1MB)
> henüz decompile EDİLMEDİ → §6 açık maddeler.

## §1 Hedef ve analiz künyesi

| Alan | Değer |
|---|---|
| Hedef | `C:\Program Files\AIMP\AIMP.Shared.dll` |
| SHA-256 | `e9622014e251a5aa2deb1616187032c7388ea686448a1ebc304f711c8bc05ce7` |
| Format / Arch | PE / x86_64 (image base 0x400000) |
| Analiz | Ghidra 12.1.4 headless auto-analysis — **16.723 prosedür, 18.960 string** |
| Oturum | REA MCP `open_binary(provider_id: "ghidra")` + `batch_decompile` |

**Bilinen sınır:** Ghidra Delphi/C++Builder mangled sembollerini tam çözer
(`Aso::*` = ses motoru, `Aimp::*` = uygulama çekirdeği, `Acl::*` = RTL yardımcıları);
vtable-based dispatch'te hedef fonksiyon adı bilinemez → o satırlar `(**(code**)(...+off))(x)`
olarak raporlanır ve sahiplik VERIFICATION REQUIRED işaretlenir.

## §2 EQ çekirdeği — `Aso::Processors::Equalizer` (KRİTİK BULGU)

### §2.1 Bant sayısı = 20 (kanıt: iki bağımsız prosedür)

**`TASOCustomEqualizer::SetEQBandsArr(StaticArray<double,20> const&)`** @ 0x5cc3c0
(evidence ev_58b49482…):

```cpp
void SetEQBandsArr(longlong param_1, undefined8 *param_2) {
  puVar2 = (undefined8 *)(param_1 + 0x60);
  for (lVar1 = 0x14; lVar1 != 0; lVar1--) {        // 0x14 = 20 eleman × 8 byte
    *puVar2 = *param_2; param_2++; puVar2++;       // double kopyası (160 byte)
  }
  *(undefined1 *)(param_1 + 0x58) = 1;             // dirty flag
  TASOCustomProcessor::DoChanged(param_1);         // zinciri yeniden kur
}
```

**`TASOCustomEqualizer::ResetBands()`** @ 0x5cbf40 (evidence ev_8a671c7d…):
`SetPreamp(0)` + `FastZeroMem(param_1 + 0x60, 0xa0)` — **0xa0 = 160 byte = 20 × double**.
Çıkarım: AIMP EQ **20 bant**, bant dizisi `this+0x60`'ta **double** hassasiyetle saklanır,
`this+0x58` dirty flag. **Neva 31 band (ADR-105/106) ile karşılaştırılabilir mimari —
depolama stratejisini (double dizi + dirty + rebuild) alıntılamak ücretsizdir.**

### §2.2 Bant ve preamp aralığı = ±15 dB (kanıt: IEEE754 sabitleri)

**`SetEqBand(int, float)`** @ 0x5cc340:
```cpp
fVar1 = (float)Acl::Math::MinMax(param_3, 0xc1700000, 0x41700000);
// 0x41700000f = +15.0f · 0xc1700000f = -15.0f  → clamp [-15, +15] dB
if (GetEqBand(i) != fVar1) {
  *(double*)(this + 0x60 + i*8) = (double)fVar1;   // double'a genişletilerek saklanır
  this+0x58 = 1; DoChanged();
}
```

**`SetPreamp(float)`** @ 0x5cc3f0:
```cpp
uVar2 = MinMax(param_2, -15.0f, +15.0f);           // preamp de ±15 dB
if (!SameValue(uVar2, *(float*)(this+0x100), 0)) {
  *(float*)(this+0x100) = uVar2;                   // dB değeri
  *(float*)(this+0x104) = Abl::Audiotypes::DBToLevel(uVar2);  // lineer gain
  *(bool*)(this+0x108) = !Math::IsZero(level, 0);  // bypass bayrağı (gain==0 → bypass)
  DoChanged();
}
```

**Neva etkisi:** ADR-105'teki ±12 dB RBJ aralığı AIMP ile uyumlu ölçek; ±15 dB kullanıcı
kutusuna genişletilebilir (aynı MinMax deseni).

### §2.3 Ses işleme yolu — `SafeProcessData` @ 0x5cbe80 (evidence ev_8a671c7d…):

```cpp
void SafeProcessData(longlong *param_1 /*this*/, longlong param_2 /*TASOSamples**/) {
  if ((char)param_1[0xb] != '\0') {           // +0x58 dirty → 8-byte read
    *(undefined1*)(param_1 + 0xb) = 0;
    (**(code **)(*param_1 + 0xa8))(param_1);  // vtable+0xA8: band→süzgeç yeniden hesabı
  }
  if ((char)param_1[0x21] != '\0') {          // +0x108 preamp-bypass flag
    fVar1 = *(float *)((longlong)param_1 + 0x104);   // lineer preamp gain
    if (!SameValue(fVar1, 0x3f800000 /*1.0f*/, 0)) {
      pfVar4 = *(float**)(param_2 + 0x10);           // sample buffer
      for (iVar3 = samples(+0x2c) * channels(+0xc); iVar3 > 0; iVar3--)
        *pfVar4++ *= fVar1;                          // interleaved çarpım (Tüm seritler)
    }
  }
}
```

**Çıkarım:** (1) bant süzgeç hesabını **vtable+0xA8** tetikler (SOP: dirty → lazy rebuild,
gerçek işleme çağrısı içinde değil); (2) preamp **interleaved tüm-serit lineer çarpım**;
(3) actual bant işleme bu fonksiyonda DEĞİL — alt sınıf/çekirdek zincirindedir (aşağıda FFT).

### §2.4 FFT EQ çekirdeği — `TASOFFTEqualizerCore` @ 0x5cc4a0 ailesi

**`Setup(StaticArray<double,1>*, StaticArray<double,1>*, int, int)`** @ 0x5ccba0 (ev_58b49482…):
```cpp
TFastFourierTransform::Flush();
iVar3 = *(int*)(param_1 + 0x30);              // FFT boyutu (N)
fVar5 = (float)iVar3;
for (iVar2 = 0; iVar2 < iVar3; iVar2++) {
  dVar7 = ((float)in_stack_00000028 /*sample rate*/ * 0.5) / fVar5;   // bin genişliği
  fVar4 = FUN_005cca00(auStack_58, (float)((double)iVar2 * dVar7));  // frekans yanıtı çizgisi ( interpolate )
  dVar7 = Math::Power(0x4024000000000000 /*10.0*/, (double)fVar4 / 20.0); // 10^(dB/20)
  *(float*)(... + iVar2*8) = (float)(dVar7 * 2.0);                    // lineer gain × 2 (kompleks eşlenik)
}
// sonra: dizi vtable+8 ile pub edilir; circular shift: (i + offset) % N  (faz kaydırma)
```

**`Process(StaticArray<float,1024>*, int, int)`** @ 0x5cc6b0 — overlap-add FFT işleme:
```cpp
// her blok: Min(+0x3c, kalan) → Flush → giriş orneği kompleks tampona (adım 2 = re/im)
// → vtable+8 (forward FFT) → kompleks çarpım (re/im çiftleri, +0x20 süzgeç tablosu)
// (gövde 1.8KB+ — tam metin evidence ev_58b49482'de; IFFT + overlap-add devamı)
```

**`TASOFFTEqualizer::CalculateBands()`** @ 0x5ccf20:
```cpp
TASOFFTEqualizerCore::Setup(*(this+0x110), PTR_Aso::Types::EQ_BANDS /*0x668390*/,
                            this + 0x60, 0x14 /*20*/, *(int*)(this+0x25) /*rate*/);
```
→ **Frekans seti `Aso::Types::EQ_BANDS` sabit tablosunda** (adres 0x668390; içerik string
aramasında `31.25/62.5/...` ISO seti beklentisi ile doğrulanmalı → §6 açık #2).

**Neva etkisi:** AIMP bant EQ'yu **FFT domain'de** uygular (1024 örnek blok, overlap-add);
ADR-105'teki **RBJ biquad zinciri (62/124Hz)** buna alternatiftir — ikisi birlikte
"EQ mode" anahtarı olarak sunulabilir (FFT = düz kırmızı çizgi, biquad = sıfır-gecikme).
Kullanıcı 1-31 band seçebilir (ADR-106) — FFT modunda band sayısı süzgeç tablosu
boyutuna bağlıdır, 31 bant FFT ile sorunsuz.

### §2.5 Plugin mesaj API'si — bant get/set tek mesajda

**`Aimp::Core::Messages::CorePropertyEqualizerSetBandValue(int, float)`** @ 0x415020:
```cpp
uVar1 = FUN_00403850(1, band_index);
iVar2 = CoreSend(0x100f /*msg*/, uVar1, &float_value);
```
**`CorePropertyEqualizerGetBandValue(int)`** @ 0x414fe0: aynı mesaj `0x100f`, read modu.
→ **Tek mesaj ID 0x100F**, parametre ile get/set ayrıştırılıyor. Neva C ABI
(`neva_session_set_eq_gain`, tur7b dumpbin kanıtlı 9 export) birebir aynı deseni izliyor —
doğrulama: **Neva tasarımı AIMP plugin mimarisiyle tutarlı**.

## §3 ASIO cihaz katmanı — `TASOASIODevice::DoSwitchBuffer(int)` @ 0x6052d0 (ev_9baaa8f3…)

```cpp
void DoSwitchBuffer(longlong *param_1 /*this*/, int param_2 /*buffer index 0|1*/) {
  if (*(char*)(this + 0x14d) /*active*/ && Math::InRange(param_2, 0, 1)) {   // çift tampon
    if (TACLCriticalSection::TryEnter(param_1[7] /*lock @+0x38*/, this + 0x14d)) {
      if (*(char*)(this + 0x8c) == 0)
        (**(code **)(*param_1 + 0x50))(param_1);        // vtable+0x50: buffer hazırlık
      iVar3 = (int)param_1[0x11];                      // kanal sayısı @+0x88
      for (local_1c = 0; local_1c < iVar3; local_1c++) {
        (*(code *)param_1[0x2f])                       // callback @+0x170
           (param_1[0x30] /*context*/,
            *(this + param_2*8 + local_1c*0x18 + 0x95 /*Ghidra offset çözümlemesi — taban muhtemel 0x950, VR*/),
            param_1[0x32] + local_1c * *(int*)(this + 0x16) /*stride*/);
      }
      *(char*)(this + 0x8c) = 0;
      TACLCriticalSection::Leave(param_1[7]);
    }
    if (*(char*)(this + 0x14d)) {                       // kilit serbestken driver'a bildir
      TASOASIODevice::GetDriver(this, &local_38);
      (**(code **)(*local_38 + 0x40))(local_38);        // vtable+0x40: trigger/start
    }
  }
  System::_IntfClear(&local_38);
}
```

**Çıkarımlar:** (1) **ASIO çift-tampon (double buffering)** — index 0/1, `InRange(0,1)`
kanıtı; (2) **TryEnter** — render thread'i lock'u beklemiyor (fail-safe drop);
(3) **kanal-başına ayrı callback + stride** (deinterleave hazır); (4) hazırlık
(vtable+0x50) yalnız `+0x8c==0` iken — yani **buffer zaten hazırken tekrarlanmıyor**.
Neva WASAPI exclusive backend'i (tur7b `wasapi_backend.cpp`) aynı çift-tampon +
TryEnter desenini uygulamalı.

## §4 SOXR resampler — `TASOSOXRResampler::Process(TASOSamples*, TASOSamples*)` @ 0x541100 (ev_9baaa8f3…)

```cpp
void Process(longlong param_1 /*this*/, longlong param_2 /*input*/, longlong param_3 /*output*/) {
  *(char*)(param_1 + 0x14) = 0;                          // running flag
  if (*(char*)(param_1 + 0x38)) FreeHandle();            // format değiştiyse serbest bırak
  if (*(longlong*)(param_1 + 0x20) == 0) CreateHandle(); // lazy (re)create
  if (handle != 0 && *(int*)(param_2 + 0x2c) > 0) {      // input sample count > 0
    lVar2 = (output_capacity(+0x28) - written(+0x2c));   // boş yer
    uVar1 = System::_Trunc((double)lVar2 * *(double*)(param_1 + 0x30));  // ratio (double)
    uVar1 = Math::Max(uVar1, 1);                          // en az 1 ornek
    uVar1 = Math::Min(uVar1, input_count);                // input'tan fazla isteme
    uVar1 = (*DAT_00669488)                              // soxr_process (IAT fn ptr)
        (handle, input_buf(+0x10), uVar1, &in_used,
         output_buf(+0x10) + written * channels(+0xc) * 4 /*float32*/,
         lVar2, &out_written);
    CheckError(uVar1);
    // gecikme defteri: in_used / ratio − out_written  (delay compensation)
    *(double*)(param_1 + 0x18) += (double)in_used / ratio - (double)out_written;
    *(int*)(param_3 + 0x2c) += out_written;              // output written sayacı
    TASOSamples::Remove(param_2, in_used);               // tüketileni girişten sil
    *(char*)(param_1 + 0x14) = 1;
  }
}
```

**Çıkarımlar:** (1) **libsoxr doğrudan `soxr_process` ile** — `aimp-dump.md` §1.2'deki
string kanıtı (`soxr_create/delete/process/quality_spec`) artık **IAT-fn-ptr pseudocode
kanıtıyla doğrulandı**; (2) **oran (ratio) double** — 32-bit float değil, hi-res
dostu; (3) **gecikme defteri** (`+0x18`) soxr'ın fazla/ornek farkını track eder —
**Neva'da ADR-105 "libsoxr AIMP yolu" kararı bu defterle desteklenmeli** (A/V senkron
ve gapless için gerekli); (4) output **float32 interleaved** (stride `channels × 4 byte`).

## §5 WASAPI — bu DLL'de yalnız cihaz tescili (ev_c5d76fae…)

| Prosedür @ | İşlev |
|---|---|
| 0x607d30 | `Wasapi::Types::GetDeviceID(IMMDevice)` |
| 0x607db0 | `Wasapi::Types::GetDeviceName(IMMDevice)` |
| 0x607f00 | `Wasapi::Types::GetDeviceNameByID(IMMDevice)` |
| 0x607fa0 | `Wasapi::Types::IsDefaultDevice(IMMDevice, enum)` |
| 0x60c650 | `Wasapi::initialization()` → `acOSCheckVersion(6,0,…)` (Vista+) → `TASODeviceProvider::Register` |

**Bulgu:** `IAudioClient`/render döngüsü AIMP.Shared.dll'de **YOK** — gerçek WASAPI
render sınıfı muhtemel `AIMP.Runtime.dll` içinde (15.7MB, §1.1 aimp-dump.md).
Decompile kapsamı genişletilecekse hedef orası → §6 açık #3.

## §6 Açık maddeler (bu dump'ın sınırları)

| # | Açık | Durum |
|---|---|---|
| 1 | `AIMP.exe` (7.1MB, Delphi) full decompile — VST/Winamp-plugin yükleyici, UI | DECOMPİLE YOK — kullanıcı hedefi |
| 2 | `Aso::Types::EQ_BANDS` tablosu (0x668390) içerik doğrulaması (ISO 31.25…16k vs AIMP'e özgü set) | VERIFICATION REQUIRED |
| 3 | WASAPI render döngüsü (`AIMP.Runtime.dll` içinde beklenir) | DECOMPİLE YOK |
| 4 | `TASOFFTEqualizerCore::Process` IFFT + overlap-add devamı (gövde kesiği 1.8KB'da) | Kanıt ev_58b49482'de tam metin var — okunacak |
| 5 | FFT boyutu N değeri (`+0x30` runtime) ve `+0x2c` blok uzunluğu | UNKNOWN (runtime veri) |
| 6 | `DoSwitchBuffer` offset 0x95 taban çözümlemesi (Ghidra decoded) | VERIFICATION REQUIRED |

## §7 Neva Player'a aktarım özeti

| AIMP bulgusu | Neva karşılığı (ADR-105/106) | Aksiyon |
|---|---|---|
| 20 bant × double + dirty + lazy rebuild | 31 bant (kullanıcı 1-31 seçer) | Ayni strateji (double dizi + dirty flag + rebuild) |
| ±15 dB clamp (MinMax deseni) | ±12 dB RBJ | ±15'e genişlet — aynı MinMax |
| FFT EQ modu (1024 blok, overlap-add) + biquad yok | RBJ biquad zinciri | EQ mode anahtarı: FFT (düz çizgi) / biquad (sıfır-gecikme) |
| preamp: dB→DBToLevel→lineer çarpım + bypass flag | preamp mevcut (ADR-105) | bypass flag deseni alınır (gain==1.0 → skip) |
| mesaj 0x100F tek-ID get/set bant | C ABI `neva_session_set_eq_gain` | Tutarlı — API tasarımı doğrulandı |
| ASIO: çift tampon + TryEnter + kanal stride callback | WASAPI exclusive backend | Aynı desen uygulanır |
| soxr: double ratio + gecikme defteri | libsoxr (ADR-105) | Gecikme defteri zorunlu (gapless/A-V senkron) |

---
*Ghidra 12.1.4 · REA MCP · 2026-10-20 · private/local kullanım · yayın YOK*
