---
title: "Neva Engine — RE Tur 6: Boşluk Kapanışı, Dil Envanteri & Runtime (VoiceMeeter Potato + FxSound)"
type: doc
category: audio-engine
version: 1.1.0
status: active
authority: "REA shipped-artifact evidence · zero-hallucination · SSOT: 04-reverse (turlar 1-5)"
updated: 2026-10-20
---

# RE Tur 6 — Boşluk Kapanışı · Dil Envanteri · Runtime · Kod Örnekleri

**Zorunlu Bağlantılar:** [[04-reverse-voicemeeter-fxsound]] (turlar 1-5 SSOT) · [[11-cok-dilli-kod-ornekleri]] (dil örnek arşivi) · [[00-sdk-lib-envanter]] · [[02-dsp-zincir-mimari]]

> **Bu tur ne yaptı (4 satır):**
> 1. Doc 04 §14.11'de açık kalan **3 ince boşluk kapandı** (§2-§4) + dB formülü çözüldü.
> 2. **"20+ dil" palet iddiası sınaması = RET** (§7) — saf native C/C++ kanıtı.
> 3. **Runtime 3 faz** çalıştırıldı (statik + pasif + aktif/PrintWindow) — §8.
> 4. **4 kod örneği** decompile kanıtından temiz-kod karşılığına çevrildi (§9) — öğret + linkle.

> ## ⚠️ DÜZELTME BANDI (2026-10-20 · doc 04 v1.5.0 §14.12 — bu dokümanın §0/§2/§3'ünü supercede eder)
>
> Bu tur **0x400000 tabanlı 32-bit `FUN_*` adresleriyle** çalışmıştır. REA taze oturumunda FxSound.exe
> **image base `0x140000000`** olarak bulunmuş ve eski üç RVA (`0x48cf20`, `0x487230`, `0x473de0`) hiçbir
> düzlemde **fiziksel olarak mevcut değildir** (byte tarama 4.182.372 bayt, `matches: []`).
>
> | Bu dokümandaki iddia | Durum | Doğrusu |
> |---|---|---|
> | §0/§2: `FUN_0048cf20` = "8-slot mix" | ❌ **GEÇERSİZ (bu sürümde)** | x64 karşılığı `FUN_1401c7000` = **`sosProcessBuffer()`** (31-band DF2T SOS) — doc 04 §14.12 |
> | §0/§3: `FUN_00473de0` = "2 SPSC ring drain" | ⚠️ **VR — doğrulanamadı** | bu düzlemde eşleşmedi; işlevsel karşılığı uydurulmadı |
> | §0/§3: `FUN_0048e990` tracker + K=6.0206 | ✅ **geçerli** (doc 04 §14.11 kanıtı) | korunur |
> | §7: "20+ dil" RET (native C/C++) | ✅ **geçerli** | korunur |
> | §8 runtime 3 faz | ✅ **geçerli** | korunur |
>
> **Bu doküman silinmez** (kanıt-zaman damgası olarak kalır); §0'daki #1/#2 satırları yalnız
> **sürüm-tarihsel** okunur. Güncel/aktif model: [[04-reverse-voicemeeter-fxsound]] §14.12.

---

## §0 Özet Tablosu

| Konu | Sonuç | Kanıt |
|---|---|---|
| Boşluk #1 `FUN_0048cf20` | 8-slot mix uygulayıcı: `out[k] += in · strip_gain · slot_gain[k]`, lineer ramp, abs-peak, blok sonu snap | ev_97a40176 |
| Boşluk #2 `FUN_00473de0` | Komut/olay kuyruğu drain: 2 SPSC ring, 8 event, blok-hizalı crossfade `−(değer/uzunluk)`, stride 0x918 → `FUN_004729e0` dispatch | ev_1430a39c |
| Boşluk #3 `FUN_0048e990` | Tracker → clamp → **hızlı 20·log10 (K = 6.0206)** → eşik → 4-yol jump table | assembly + sabit okumalar §4 |
| Dil paleti | **"20+ dil" RET** — her iki binary saf native C/C++ | §7 |
| Runtime | 3 faz tamam; odak çalma YOK → `PrintWindow(flag=2)` | §8 PNG kanıtları |
| Kapanmayan | JustVerb/C5 render çekirdeği (stripped) · `.fptable` işlevi · durum makine adları | §10 VR/UNKNOWN |

---

## §1 Hedefler & Yöntem

| Hedef | SHA256 (12) | Tür | Kimlik |
|---|---|---|---|
| `C:\Program Files (x86)\VB\Voicemeeter\voicemeeter8.exe` | `e1f2cdf2990f…` | PE32 x86, MSVC 2010 | PDB `Voicemeeter8_vc2010` · GUID `e9142254-19a3-4033-b53f-74650a28a26f` · image base `0x400000` |
| `C:\Program Files\FxSound LLC\FxSound\FxSound.exe` | `5a4fa19e525c…` | PE32+ x64, JUCE | image base `0x140000000` · **VA = `0x140000000 + (file_off + 0x1A00)`** |

**Yöntem notları (bu tur):**

- REA sunucusunda başka bir oturum ("tur8" AIMP) ile **paylaşımlı program yarışı** görüldü → `withT` retry-rebind helper (yanlış "Unknown Ghidra" hatasında `open_binary` + 4 deneme) ve her sonuçta `subject.digest.sha256` teyidi zorunlu tutuldu.
- `read_bytes` yanıtının baytları `normalized_result.bytes_hex` altındadır (LE hex string) — IEEE754 decode elle yapıldı (sandbox'ta `DataView` yok).
- Aktif fazda odak çalma **yapılmadı** (kullanıcı Adobe Premiere'de çalışıyordu) → `PrintWindow (flag=2)`.

---

## §2 Boşluk #1 — `FUN_0048cf20` = 8-slot mix uygulayıcı

**Kanıt:** ev_97a40176 · **Boşluk kaynağı:** doc 04 §14.11 "cf20 ana döngü matematiği VR" → **kapandı**.

| Parametre | Değer |
|---|---|
| Şema | `out[k] += in · strip_gain · slot_gain[k]` (k = 0..7) |
| Kayıt stride | `0x48` (72 B / slot) — gain, hedef, ramp durumu |
| Ramp | Lineer interpolasyon (blok içi) + **blok sonu snap** (`gain = target`) |
| İzleme | abs-peak (çıktının mutlak tepe değeri) |

**Uzman notu:** Doğrudan kazanç zıplaması hop/pop üretir; blok başında `step = (hedef − anlık)/n` hesabı + blok sonunda snap, sıfır tahsis (no-alloc) click-free volume'ün klasik desenidir. Neva Engine'de `05-neva-mixer-web-pcm-plani`daki gain basamakları aynı deseni kullanır.

---

## §3 Boşluk #2 — `FUN_00473de0` = komut/olay kuyruğu drain'i

**Kanıt:** ev_1430a39c · **Boşluk kaynağı:** doc 04 §14.11 "FUN_00473de0 kimliği VR" → **kapandı**.

| Özellik | Değer |
|---|---|
| Kuyruk | 2 × SPSC ring (tek-yönülsel, kilitssiz), turda **8 event** drain |
| Crossfade | Blok-hizalı lineer ramp: **`ramp = −(değer / uzunluk)`** (negatif değer artış verir) |
| Kayıt stride | `0x918` (event kaydı) |
| Dispatch | drain sonrası → `FUN_004729e0` |

**Uzman notu:** RT ses hattına komut doğrudan yazılmaz; kuyruktan drain + kademeli geçiş, parametre değişiminde ses hattında ani kırılma bırakmaz (SPSC = tek üretici/tek tüketici, kilit gerektirmez).

---

## §4 Boşluk #3 — `FUN_0048e990` dB/track zinciri (assembly kanıtlı)

**Zincir (assembly'den okundu):**

```text
FUN_00487230 (tracker, CALL 0x48f30a)
  → CLAMP grubu: 0.001f alt / 100.0+100.0f üst / −60.0f dB tabanı
  → HIZLI 20·log10:  K = 6.0206 · üs + LUT[mantissa]
  → Eşik karşılaştırması
  → JMP [EDX*4 + 0x498eac]  → 4-yollu durum makinesi
       case0 (0x48f41f): örnek-başına stereo kazanç ×4 unroll
       0x48f503 / 0x48f7af / 0x48f8fc → durum adları UNKNOWN
```

### §4.1 Sabit tablosu (bayt-bayt okundu)

| Adres | Bayt (LE hex) | Çözülen değer | Anlam | ev_ |
|---|---|---|---|---|
| `0x536358` (f64) | `0000002018151840` | **6.0206** | `K = 20·log10(2)` | ev_169963cd |
| `0x56e378` (f32×128) | `00000000 266f8a3d 51e6093e …` | 0 / 0.0675948 / 0.1346676 | `LUT[k] = 20·log10(1+k/128)` (±1e-8) | ev_3d1dfb36 |
| `0x536198` (f32) | `0000c842` | 100.0f | **üst clamp** (floor DEĞİL) | ev_4f1f1388 |
| `0x5361a0` (f64) | `0000000000005940` | 100.0 | üst clamp eşiği (double) | ev_8d90267d |
| `0x535ff8` (f32) | `000070c2` | −60.0f | dB tabanı (floor) | ev_a7cb6789 |
| `0x5366e0` (f32) | `6f12833a` | 0.001f | lineer alt sınır ↔ `20·log10(0.001) = −60 dB` | ev_24f7d930 |

**Jump table `0x498eac`** (bytes `1ff44800 03f54800 aff74800 fcf84800` → LE dword'lar):
`0x0048f41f` · `0x0048f503` · `0x0048f7af` · `0x0048f8fc` — 4 hedef teyitli.

### §4.2 Düzeltmeler (zero-hallucination)

1. **K = 6.0206, "6.0256" DEĞİL.** `0x536358` baytları `0000002018151840` → f64 `6.020599913279624` = `20·log10(2)` (matematik kontrolü: `20·log10(2) = 6.020599913…`, fark <1e-9). Önceki tur özetindeki "6.0256" yazım hatasıydı; vault'ta hiçbir yerde geçmiyor (grep = 0 isabet), bu rapor SSOT'tur.
2. **`[0x536198] = 100.0f` bir "floor" değil, ÜST sınırdır.** Doc 04 §14.11'deki `FLD [DAT_00536198] ; … taban (floor)` yorumu bayt okumasıyla çürütüldü: 100.0f üstte sınırlar (`FCOM` 100.0 ile karşılaştırıp üstündeyse 100.0f'ye çeker). Gerçek dB tabanı `[0x535ff8] = −60.0f`'dir. **Doc 04 §14.11 bu turda düzeltildi (v1.4.5).**
3. **LUT hassasiyeti ±0.002 değil, ±1e-8.** `LUT[1] = 0.067594811` (okunan) vs `20·log10(1+1/128) = 0.067594813` (hesap) — float32 yuvarlama düzeyinde birebir.

### §4.3 Hızlı dB matematiği (çözülen algoritma)

`x > 0` için `x = (1 + m/2²³)·2^e` olsun (`e` = üs, `m` = mantissa). O zaman:

```text
20·log10(x) = e · 20·log10(2) + 20·log10(1 + m/2²³)
            ≈ e · K            + LUT[k]      (k = mantissanın üst 7 biti, k = m≫16)
```

- `K = 6.0206` sabiti üs için, 128 girdili LUT mantissa için kullanılır.
- Kuantizasyon hatası: mantissa'nın alt 16 biti atılır → **en fazla ≈0.068 dB** (tipik ≈0.03 dB) — gerçek zamanlı meter için fazlasıyla yeterli.
- `0.001f` alt sınırı, log öncesi sıfır/near-zero guard'ıdır; `20·log10(0.001) = −60` ile dB tabanı **iç tutarlıdır** (çapraz doğrulama).

---

## §5 Dispatcher & VB efekt kancaları

| Bulgu | Detay | Durum |
|---|---|---|
| `FUN_00489230` = per-blok DSP param/yenileme dispatcher'ı | 5 şerit × stride `0x10c` + 3 × biquad `FUN_00487800` çağrısı | Sınıflandırıldı |
| Boy farkı notu | Doc 04'te gövde "2.5 KB" iken Ghidra bu oturumda **361 B** ölçtü → araç/fonksiyon sınırlandırma farkı olabilir | ⚠️ VERIFICATION REQUIRED (§10) |
| C5 serializer | `FUN_004a3cf0` = `sprintf` tabanlı param dump (konsol/serialize) | Sınıflandırıldı |
| JustVerb penceresi | WND-CLASS xref: `0x4a7c49` / `0x4a9a21` / `0x4a9b48` · WND proc `FUN_004a93b0` | Sınıflandırıldı |
| **JustVerb/C5 render çekirdeği** | Stripped — DSP iç matematiği çözülmedi | ⚠️ VR (§10) |

---

## §6 FxSound bilinmeyenleri

### §6.1 `.fptable` = PE section, işlevi UNKNOWN

| Alan | Değer |
|---|---|
| Konum | file offset `0x2E0` · VA `0x3DE000` |
| Boyut | VSize 256 · RawSize 512 · **içerik tamamı `0x00`** · SHA `076a27c7…` |
| Ad | MSVC linker standart bölüm adı (`.fptable`) |
| Çapraz kontrol | FxSound updater ve fxdiag binary'lerinde de **boş**; Go `fxmcp.exe`'de **yok** |

**Sonuç:** Üç binary'de de boş placeholder → işlevi **UNKNOWN** (doldurulmamış bölüm; okuma/analiz taraması üretmedi).

### §6.2 Modül haritası (sha-verified, VA = `0x140000000 + (file_off + 0x1A00)`)

| Kaynak izi (`__FILE__`) | Entry | Boyut | Kümeler / xref |
|---|---|---|---|
| `DfxDspEq.cpp` | `FUN_1401ac330` | 455 B | `0x1401b5870–0x1401b5eb0` · 4 xref |
| `DfxDspPreset.cpp` | `FUN_1401ab7f0` | 949 B | `0x1401b2bb0–0x1401b3fe0` |
| `ptechDsp\Play\Play32.c` | `FUN_1401c2910` | 1221 B | `0x1401c0a70–0x1401c7b20` |
| `sndDevicesInit.cpp` | — | — | 16 xref |
| Ek izler | `GraphicEq` · `SOS` · `BinauralSync` · `Qnt` · `dfxp` | — | EQ/efsane zinciri envanteri |

### §6.3 EQ çekirdeği: `FUN_1401b5870` (205 B) — dfxpEq param-set/get wrapper

Pseudocode (analyze_function):

- Null-guard → handle `*(param_1 + 0x101d88)`.
- Param aralığı `[1, DAT_140360000]` kontrolü.
- Okuma: `FUN_1401b6eb0(handle, idx, float*)`.
- Hata yolu: `MessageBoxW(L"C:\Users\vijay\...\dsp\ptutil\dfxp\dfxpEq.cpp", L"303"/"306"/"309")`.

**Sonuç:** FxSound, `__FILE__` + satır numarasını assert/diagnostik kanalı olarak **MessageBoxW ile** kullanıyor (geliştirici makinesi `vijay` — derleme zamanı `__FILE__`). Bu, .fac/.dfxp hata yollarının kullanıcıya nasıl çıktığını gösteren doğrudan kanıttır.

### §6.4 `.fac` preset formatı (disk kanıtı — bu tur)

| Alan | Değer |
|---|---|
| Fabrika presetleri | `C:\Program Files\FxSound LLC\FxSound\Factsoft\1.fac … N.fac` (944-963 B) |
| Kullanıcı presetleri | `%APPDATA%\FxSound\Presets\*.fac` + `AutoSave\*.fac` (1787 B) |
| Biçim | **ASCII metin, CRLF satırlar** — ilk satır magic: `CLASS1 : Effect Type` |
| Satır grameri | `<ondalık değer>: <anahtar>` · bare satır = bölüm etiketi (`General`, `Band n`) · girintili satır = alt alan (`   62.5: CF`, `   0: Boost/Cut`) |
| Anahtarlar (örnek) | `9: Version` · `N: Main 0..5` · `0: Element Number` · `   k: Param n` · `7: Number of Application Dependent Integers` · `10: Number of EQ Bands` · `1: On/Off Flag` · `Band n → CF + Boost/Cut` |

**Neva hedefi:** Clean-room preset import/export için doğrudan hedef format — parser örneği §9.4.

### §6.5 `FxSound.settings` (runtime yapılandırması)

- `%APPDATA%\FxSound\FxSound.settings` (7120 B): `num_bands=31`, **çıkış cihazı = Voicemeeter VAIO3**, `run_minimized=1`.
- `C:\ProgramData\FxSound\FxSound.settings` (415 B, XML): `power`, `hotkeys`, `preset=General`, `cmd_*` hotkey kodları.

---

## §7 Dil paleti envanteri — "20+ dil" iddiası **RED**

| Sınama | voicemeeter8.exe | FxSound.exe | Repo (disk) |
|---|---|---|---|
| CLR/.NET | CLR header `rva=0` · 0 isabet | 0 isabet | `.cs = 0` |
| PDB/CodeView | `Voicemeeter8_vc2010` · GUID `e9142254-…` | PE32+ x64 | — |
| JUCE imzası | **Yok** | **Var** (FxSound'e özgü) | — |
| ASIO/WASAPI | **Var** (Voicemeeter'e özgü) | — | — |
| Uzantı envanteri | — | — | **12/19 uzantı**: `.md 584 · .php 336 · .css 303 · .js 109 · .ts 61 · .cpp 25 · .h 18` = 1436/1839 (**%78**) |
| **Sıfır isabet** | — | — | `.cs .py .ps1 .go .rs .java .rb .sh` |

**Karar (gerekçeli RET):**

1. Her iki binary **saf native C/C++**: Voicemeeter = MSVC 2010 x86 (PDB kanıtı), FxSound = x64 JUCE/C++ (imza kanıtı). CLR yok, Rust/Go/Java izi yok.
2. Repo'da 8 dil **tamamen sıfır**; %78'i 7 uzantının içinde.
3. "20 dil" kaynağı: `11-examples/` altındaki **20 Markdown dosyası** (`01-c11` … `20-r`) — bunlar [[11-cok-dilli-kod-ornekleri]] arşivinin kod *örnekleri*, binary'ye gömülü dil değil (derlenmedi, doc 11 §1).
4. Go: yalnız **ayrı** `fxmcp.exe` (doc 04 §11) — bu iki hedefde değil.

---

## §8 Runtime — 3 faz (statik + pasif + aktif)

| Faz | Ne yapıldı | Sonuç / kanıt |
|---|---|---|
| **1 — Statik** | PE section/imports/module map okumaları | §2-§6 (bu rapor) |
| **2 — Pasif** | FxSound **PID 12052** (33 MB, tray, `run_minimized=1`) · voicemeeter8 **PID 13148** (WoW64 x86, başlık "Voicemeeter Potato", RECT **1645×770**) | FxSound **dış DSP DLL import etmiyor → DSP exe içinde statik gömülü** (§6.2 modül haritası ile tutarlı) |
| **3 — Aktif** | Kullanıcı Adobe Premiere'de aktif → **odak çalma YOK**; `PrintWindow (flag=2)` ile pencere yakalama | `C:\Users\MARHAN\AppData\Local\Temp\opencode\re-tur6-voicemeeter8.png` (**193390 B**) · `re-tur6-active-voicemeeter.png` (980531 B, masaüstü kanıtı) |

**PNG okuması (aktif ekran):** 5 strip + **SPECIAL FX paneli** + VAIO3'e bağlı FxSound + lisansız banner + **48 kHz / 512** arabellek.

**Ses zinciri (settings + PNG kanıtı):**

```text
uygulama → FxSound (çıkış = Voicemeeter VAIO3) → Potato strip/ bus → çıkış (48 kHz, 512)
```

*Not: Zincir settings + ekran görüntüsü kanıtına dayanır; paket düzeyinde ölçüm yapılmadı.*

---

## §9 Kod Örnekleri (clean-room, öğret + linkle)

> **Kaynak bağlantısı:** Tüm örnekler bu turun decompile/assembly kanıtlarından türetilmiştir; turlar 1-5'in SSOT'u [[04-reverse-voicemeeter-fxsound]] §14. Her örnekte kanıt adresi + `ev_*` kimliği başlıkta. `*` işaretli satırlar kanıtın **yeniden ifadesidir** (blok sonu snap / stride / dispatch kanıtlarıyla tutarlı).

### §9.1 Hızlı dB: üs + mantissa LUT (real-time meter)

```cpp
// Kanit: voicemeeter8.exe FUN_0048e990 (assembly, tur 6)
//   K   = [0x536358] = 6.0206 = 20*log10(2)        ev_169963cd
//   LUT = 0x56e378, 128 girdi = 20*log10(1+k/128)  ev_3d1dfb36
//   LUT[0]=0  LUT[1]=0.0675948  LUT[2]=0.1346676   (okundu, ±1e-8)
//   alt sinir [0x5366e0] = 0.001f                   ev_24f7d930
//   ust/dB sinirlar: 100.0f ev_4f1f1388 · 100.0 ev_8d90267d · -60.0f ev_a7cb6789
// Clamp siteleri ve sira için bakiniz: §4.1
float fast_db(float x) {
    if (x < 0.001f) x = 0.001f;              // log oncesi near-zero guard
    uint32_t b; std::memcpy(&b, &x, 4);
    int      e = int((b >> 23) & 0xFF) - 127; // 2^e carpani (uss)
    unsigned k = (b >> 16) & 0x7F;            // mantissanin ust 7 biti
    float    db = 6.0206f * float(e) + lut[k]; // 20*log10(x) yaklasik
    return db;                                  // clamp: §4.1
}
```

**Neden bu desen:** `log10` çağrısı gerçek zamanlı meter döngüsünde pahalıdır; üs alanı 2 çarpım + 7-bit indeksli LUT lookup'a iner. Maksimum hata ≈0.068 dB. 1990'lar audio kodunun klasik hilesi — VoiceMeeter de aynısını kullanıyor.

### §9.2 8-slot click-free mix (`FUN_0048cf20`)

```cpp
// Kanit: FUN_0048cf20 (ev_97a40176) — 8 slot, stride 0x48, lineer ramp,
//        abs-peak, blok sonu snap. Semasi: out[k] += in * strip_gain * slot[k]
struct Slot { float gain, target, step; };          // 0x48 baytli kayittan

void apply_slots(const float* in, float* const out[8], size_t n,
                 float strip_gain, Slot s[8]) {
    for (int k = 0; k < 8; ++k)
        s[k].step = (s[k].target - s[k].gain) / float(n);   // * blok ramp adimi
    for (size_t i = 0; i < n; ++i) {
        const float x = in[i] * strip_gain;
        for (int k = 0; k < 8; ++k) {
            s[k].gain += s[k].step;                 // lineer interpolasyon
            out[k][i] += x * s[k].gain;             // out[k] += in*strip*slot
            // peak = max(peak, fabs(out[k][i]))    // abs-peak izleme (kanitli)
        }
    }
    for (int k = 0; k < 8; ++k)
        s[k].gain = s[k].target;                    // blok sonu snap (kanitli)
}
```

**Neden bu desen:** Gain zıplaması click/pop üretir; step + snap tahsis gerektirmez ve blok hizasında biter. Neva mixer'daki slot/kazanç basamakları için bkz. [[05-neva-mixer-web-pcm-plani]].

### §9.3 SPSC kuyruk + blok-hizalı crossfade (`FUN_00473de0`)

```cpp
// Kanit: FUN_00473de0 (ev_1430a39c) — 2 SPSC ring, 8 event'e kadar drain;
//        blok-hizal crossfade ramp = -(deger/uzunluk); stride 0x918;
//        dispatch -> FUN_004729e0
while (auto ev = ring.try_pop()) {                // kilitssiz, tek U / tek T
    ev.ramp = -(ev.value / float(ev.length));     // kanitli formul
    apply_ramp(param, ev.ramp, ev.length);        // * kademeli gecis
    dispatch(ev, /*stride*/ 0x918);               // -> FUN_004729e0
}
```

**Neden bu desen:** RT audio thread'i kuyruktan okur; komut doğrudan parametreye yazılmaz. `−(değer/uzunluk)` blok sonunda tam sıfıra iner → geçişte ses hattında kırılma yok.

### §9.4 `.fac` preset parser (FxSound clean-room hedefi)

```cpp
// Kanit: disk ornegi Factsoft\1.fac (944 B, ASCII/CRLF) — §6.4
// Magic: "CLASS1 : Effect Type" · gramer: "<deger>: <anahtar>" /
//        bare satir = bolum ("General", "Band n") / girintili = alt alan
struct FacPreset {
    std::string effect;                      // 1. satir (magic sonrasi anahtar)
    double      version = 0;                 // "9: Version"
    std::string name;                        // bare satir: "General"
    std::array<double, 6> main{};            // "N: Main 0" .. "N: Main 5"
    std::vector<double>   params;            // "   k: Param n"
    struct Band { double cf, boost; };       // "Band n" -> CF + Boost/Cut
    std::vector<Band>     bands;
};

FacPreset parse_fac(std::istream& is) {
    FacPreset p; std::string line, section;
    while (std::getline(is, line)) {         // CRLF
        if (line.rfind("CLASS1", 0) == 0) continue;
        auto pos = line.find(':');
        if (pos == std::string::npos) { section = trim(line); continue; } // "Band n"
        double v = parse_num(line.substr(0, pos));
        std::string key = trim(line.substr(pos + 1));
        if (key == "Version") p.version = v;
        else if (key.rfind("Main ", 0) == 0) p.main[suffix(key)] = v;
        else if (key == "Param " + std::to_string(p.params.size())) p.params.push_back(v);
        else if (key == "CF")    p.bands.back().cf    = v;   // "Band n" section icinde
        else if (key == "Boost/Cut") p.bands.back().boost = v;
        else if (section.rfind("Band ", 0) == 0 && p.bands.empty()) p.bands.push_back({v, 0});
    }
    return p;
}
```

**Neden bu desen:** Vendor preseti binary değil metin → clean-room parser'ı ucuz ve test edilebilir. Neva mixer preset import/export bu biçimi hedefler (31-band EQ: `FxSound.settings` `num_bands=31` ile §6.5).

---

## §10 Açık Maddeler (VR / UNKNOWN)

| # | Madde | Etiket |
|---|---|---|
| 1 | JustVerb/C5 **DSP render çekirdeği** — stripped, iç matematik çözülmedi | ⚠️ VERIFICATION REQUIRED |
| 2 | `FUN_0048e990` 4-yol durum makinesinin **durum adları** (case0 = stereo kazanç ×4; 1-3 bilinmiyor) | UNKNOWN |
| 3 | **`.fptable` işlevi** — üç binary'de de tamamı `0x00` | UNKNOWN |
| 4 | `FUN_00489230` gövde boyu: doc 04 "2.5 KB" vs bu tur Ghidra "361 B" | ⚠️ VERIFICATION REQUIRED |
| 5 | §8 ses zinciri: settings + PNG kanıtı; paket/nivel ölçümü yapılmadı | ⚠️ VERIFICATION REQUIRED |

---

## §11 Değişen Dosyalar & Commit Durumu

| Dosya | Değişiklik |
|---|---|
| `12-re-tur6-bosluk-kapanis-dil-runtime.md` | **YENİ** (bu dosya) |
| `04-reverse-voicemeeter-fxsound.md` | v1.4.4 → **v1.4.5**: §13 sürüm satırı · §14.11 floor→üst clamp düzeltmesi + Tur 6 linki |
| `../MEMORY.md` | Sona tur6 satırı |
| `../keys.md` | Yeni row (tur6, .fptable, 6.0206, 0x498eac, doc 12) |
| `../brain.md` | RE paragrafına tur6 notu |
| `../RAG.md` | NevaEngine 59 → **60 md** (2026-10-20) |
| `../index.md` | `projects/` sayımı → **81 md** (2026-10-20 disk) |
| `../log.md` | Append-only tur6 girişi |

**`git commit`: ATILMADI — orkestratöre aittir.**
