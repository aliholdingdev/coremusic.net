---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-anayasa
evidence:
  - https://github.com/reactos/reactos/tree/master/drivers
  - https://github.com/reactos/reactos/tree/master/drivers/multimedia
  - https://github.com/reactos/reactos/tree/master/drivers/multimedia/audio
  - https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/README
  - https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/CMakeLists.txt
  - https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/audio/CMakeLists.txt
validation:
  - "2026-10-13 · drivers/ 22 alt dizin sayıldı · multimedia/audio 5 alt dizin sayıldı · README tam metin indirildi ('Optional drivers' bölümü birebir) · CMakeLists.txt = add_subdirectory(bdasup) tek satır · audio/CMakeLists.txt 404"
  - "2026-10-13 · S-Q1 KAPANDI: audio/ derleme grafiğinde değil (iki bağımsız kanıt: üst CMake yalnız bdasup + audio/ CMake 404)"
  - "2026-10-13 · GitHub Contents API ile audio/ alt 3 dizinin dosya envanteri alındı (sound 13+1 · sndblst_sys 2 · mpu401_nt4 9) — yalnız ad+bayt düzeyinde, kod gövdesi okunmadı"
open_questions:
  - "sndblst_sys WDM mi yoksa eski NT4 modeli mi → dizin adı 'sndblst_sys' · içerik okunmadı ⚠️"
  - "S-Q2 ✅ KAPANDI: sound/ içeriği §3.3.1 envanteri (ad+bayt); kod okuması bilinçli yapılmadı"
group: mimari
---

# reactos-audio-surucu — Sürücü Ağacı ve README Tanıkları

> **Bu dosya ne değildir:** Sürücü geliştirme rehberi veya boot testi raporu değildir.
> Bu dosya `drivers/` ağacını, README'nin bileşen tanıklarını ve **CMake derleme
> gerçeğini** birlikte taşır. İçerik okunmayan dizinler için fonksiyon/davranış
> iddiası üretilmez.

**ELI15:** ReactOS'un ses sürücüleri `drivers/multimedia/audio/` altında durur; depo
README'si hangi bileşenin ne yaptığını tek tek anlatır ama aynı README "bunların çoğu
çalışmıyor, ileride yazılacak" der. Üstelik CMake dosyası bu ağaçtan **hiç
bahsetmez** — yani ağacı çizmekle derlemek ayrı şeylerdir. Bu dosya hem ağacı hem
bu dürüst uyarıyı hem de derleme gerçeğini taşır.

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-surucu` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio` |
| Kademe | 5 · yaprak-modül |
| group | `mimari` |
| Doğrulama tarihi | 2026-10-13 |
| Birincil kanıtlar | README (tam metin) · `drivers/multimedia/CMakeLists.txt` · `audio/CMakeLists.txt` (404) |

---

## §2 Bu dosyanın kanıt kaynakları

| Etiket | Kaynak | URL | 2026-10-13 Durum |
|---|---|---|---|
| [D1] | `drivers/` depo ağacı | https://github.com/reactos/reactos/tree/master/drivers | ✅ GET 200 · 22 alt dizin sayıldı |
| [D2] | `drivers/multimedia/` ağacı | https://github.com/reactos/reactos/tree/master/drivers/multimedia | ✅ GET 200 · 4 öğe |
| [D3] | `drivers/multimedia/audio/` ağacı | https://github.com/reactos/reactos/tree/master/drivers/multimedia/audio | ✅ GET 200 · 5 alt dizin sayıldı |
| [D4] | `drivers/multimedia/README` — tam metin | https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/README | ✅ GET 200 · 53 satır indirildi |
| [D5] | `drivers/multimedia/CMakeLists.txt` | https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/CMakeLists.txt | ✅ GET 200 · tek satır: `add_subdirectory(bdasup)` |
| [D6] | `drivers/multimedia/audio/CMakeLists.txt` | https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/audio/CMakeLists.txt | ❌ 404 (negatif kanıt — dosya yok) |

---

## §3 Depo Ağacı (sayımlı, 2026-10-13)

### §3.1 `drivers/` üst dizini (22 alt dizin)

| Sayım | Dizinler |
|---|---|
| 22 alt dizin | acpi · base · battery · bluetooth · bus · crypto · filesystems · filters · hid · input · ksfilter · **multimedia** · network · parallel · processor · sac · serial · setup · storage · usb · wdm · wmi |
| + dosya | `CMakeLists.txt` |
| Ses için ayrı üst dizin | **YOK** — ses `multimedia/` altındadır |

> İlgili alt dizin: `ksfilter/` (kernel streaming filtresi) — adı README'deki KS
> temasıyla uyumlu, **içeriği bu turda okunmadı** → ⚠️ VERIFICATION REQUIRED (S-Q3).

### §3.2 `drivers/multimedia/` (4 öğe)

| Öğe | Tip | README'deki karşılığı |
|---|---|---|
| `audio/` | dizin | ses sürücüleri (§3.3) |
| `bdasup/` | dizin | BDA destekleyicisi — **derlemede tek aktif alt dizin** [D5] · bu dalın dışında (video/uydu teması, okunmadı) |
| `CMakeLists.txt` | dosya | derleme — içeriği tek satır [D5] |
| `README` | dosya | **ana kanıt kaynağı** (§4) |

### §3.3 `drivers/multimedia/audio/` (5 alt dizin)

| # | Alt dizin | Okuma | README/kanıtpı eşleşmesi | Durum |
|---|---|---|---|---|
| 1 | `mpu401_nt4` | MPU-401 MIDI · NT4-uyumlu sürüm | README "Optional drivers": "mpu401  MPU-401 MIDI compatible driver" | ad + README eşleşiyor · içerik sayımı §3.3.1 |
| 2 | `sb16_nt4.old` | Sound Blaster 16 · NT4 · **eski** | README'de `sb16` geçmiyor (yalnız `sndblst`) | `.old` eki → derleme dışında (§3.4) |
| 3 | `sndblst.old` | Sound Blaster · **eski** | README "Optional drivers": "sndblst  Sound Blaster (+ Pro / 16) compatible driver" | `.old` eki → derleme dışında (§3.4) |
| 4 | `sndblst_sys` | Sound Blaster çekirdek-yordamı (2 dosya) | README'de adı geçmiyor | `sndblst.c` (10 937 bayt) + `sndblst.rc` (218) — GitHub Contents API sayımı |
| 5 | `sound` | genel ses alt dizini (13 dosya + `include/`) | README'de adı geçmiyor | §3.3.1 — S-Q2'nin cevabı |

#### §3.3.1 Dosya envanteri (GitHub Contents API — 2026-10-13 sayımı)

**`sound/` (13 dosya + 1 alt dizin) — S-Q2 ✅ KAPANDI:**

| Dosya | Bayt | Rol (dosya adından — içerik okunmadı) |
|---|---|---|
| `sound.c` | 3 023 | ana modül iskeleti |
| `wave.c` / `wave.h` | 4 144 / 738 | akış/wave işleyici |
| `dsp.c` / `dsp.h` | 1 915 / 566 | DSP (OPL) komut yüzeyi |
| `mixer.c` / `mixer.h` | 1 088 / 259 | mikser |
| `sb16.c` / `sb16.h` / `sb16.rc` | 1 447 / 134 / 204 | SB16 donanım katmanı |
| `sb_waveout.c` | **35** | iskelet (neredeyse boş — bayt boyutu kanıtı) |
| `in.h` | 20 002 | en büyük başlık — wave-in tanımları (ad düzeyinde) |
| `include/` | — | alt dizin (içerik sayılmadı) |

**`sndblst_sys/` (2):** `sndblst.c` 10 937 bayt · `sndblst.rc` 218.
**`mpu401_nt4/` (9):** `mpu401.c` 11 068 · `mpu401.h` 4 398 · `mpu401.rc` ·
`mpu401_reg.inf` · `portio.c` · `readme.txt` 7 537 · `sbdebug.h` ·
`settings.c` · `test.c` 1 670.

> Tüm dosyalar yalnızca **envanter düzeyinde** (ad + bayt); kod gövdesi
> okunmadı → içerik iddiası yazılmaz.

> `*_nt4` ve `*.old` takıları **dizin adı kanıtıdır**; hangisinin etkin olduğu
> §3.4'teki CMake gerçeğiyle birlikte okunur.

---

## §4 Derleme gerçeği (CMake kanıtlı — S-Q1'in cevabı)

| # | Soru/Kanıt | Bulgu | Kaynak |
|---|---|---|---|
| C1 | `drivers/multimedia/` neyi derliyor? | İçerik **tek satır**: `add_subdirectory(bdasup)` | [D5] birebir |
| C2 | `audio/` alt dizininde kendi CMakeLists'i var mı? | **404 — dosya yok** | [D6] negatif kanıt |
| C3 | `audio/` (5 alt dizin) derleme grafiğine bağlı mı? | **Hayır** — C1 üst CMake'e `audio` eklemiyor, C2 alt CMake yok | C1 + C2 |
| C4 | `.old` dizinleri derleniyor mu? | **Derlenemez** — üst CMake'de yok, alt CMake zaten yok | C1 + C2 |

**Sonuç:** `drivers/multimedia/audio/` ağacı depoda mevcuttur ama **2026-10-13
itibarıyla derleme grafiğinin dışındadır.** Bu, README'nin *"Much of this code
exists only as stubs"* ifadesiyle bağımsız iki kanıt olarak birleşir ve tek başına
"çalışıyor" iddiasını düşürür. S-Q1 → **✅ KAPANDI (2026-10-13)**.

---

## §5 README Bileşen Tanıkları (tam alıntılarla)

### §5.1 Genel uyarı (README §giriş — birebir)

| Satır | README metni (birebir) |
|---|---|
| Kapsam | "This is a directory for the kernel streaming components, along with multimedia kernel-mode drivers." |
| Olgunluk | "Much of this code exists only as stubs for future implementation." |
| include/ | "The \"include\" directory exists as a place to store the KS related headers. It should be moved to the main include tree later." |
| İmza | "Andrew Greenwood / 3rd December, 2006" |

### §5.2 Bileşen tablosu

| Bileşen | README görevi (birebir) | Arayüz tipi | Olgunluk beyanı |
|---|---|---|---|
| `KS.SYS` | "Deals with streaming media. This is the core component for this, and can be interacted with from usermode (usually via DirectX.)" | COM-style | kod var, kapsam UNKNOWN |
| `SYSAUDIO.SYS` | "Filter graph builder (deals with topology.) Not sure what this does yet." | — | README bile emin değil |
| `PORTCLS.SYS` | "Used by WDM audio drivers, this handles many common tasks required for audio drivers." | COM-style | kod var, kapsam UNKNOWN |
| `WDMAUD.DLL`/`.SYS` | "Effectively a relay from usermode to kernelmode, the usermode portion is a MME-style driver (for WINMM.DLL) which tells the kernelmode portion what to do." | — | Vista notu §5.3 |
| `sndblst` | "Sound Blaster (+ Pro / 16) compatible driver" | opsiyonel sürücü | README "Optional drivers" |
| `mpu401` | "MPU-401 MIDI compatible driver" | opsiyonel sürücü | README "Optional drivers" |

**Bağlayıcı uyarı:** *"Much of this code exists only as stubs for future
implementation."* → Bu tablodaki hiçbir bileşen "üretimde çalışır" diye
kullanılamaz; §4 derleme gerçeği bu uyarıyı destekler.

### §5.3 Optional drivers bölümü (README §son — birebir tam metin)

```text
[ETİKET: birebir alıntı · kaynak: drivers/multimedia/README · indirme 2026-10-13]
== Optional drivers ==

- sndblst	Sound Blaster (+ Pro / 16) compatible driver
- mpu401	MPU-401 MIDI compatible driver
```

README bu iki sürücüyü listeler; `audio/` altındaki 5 dizinden yalnız ikisiyle
ad düzeyinde örtüşür (§3.3).

### §5.4 WDMAUD — usermode/çekirdek sınırı ve Vista notu (birebir)

| Satır | README metni (birebir) |
|---|---|
| Röle | "Effectively a relay from usermode to kernelmode, the usermode portion is a MME-style driver (for WINMM.DLL) which tells the kernelmode portion what to do." |
| Etkileşim | "This typically involves interacting with KS.SYS in some way." |
| Vista | "In Vista, the kernelmode portion is no longer present and the usermode portion interacts with the Vista audio stack (which resides in usermode but also ultimately ends up going back into kernelmode in the end!)" |

**Yorum (kanıta bağlı):** README Vista'yı anlatır; **ReactOS'un kendi seçimi**
(XP klasik mi Vista usermode mu) yazılmaz → anayasa A-Q1 açık kalır.

---

## §6 Sürücü Yükleme Yolu (README'den türetilmeyen kısım YOK)

| Adım | Bileşen | Kanıt |
|---|---|---|
| 1 | Donanım (PCI/ISA ses kartı) | bu dosyanın dışında — algılama mekanizması UNKNOWN ⚠️ |
| 2 | WDM ses sürücüsü (`sndblst_sys` · PortClass üstü) | dizin adı + README §5.2 |
| 3 | `PORTCLS.SYS` ortak görevleri üstlenir | README birebir §5.2 |
| 4 | `KS.SYS` akış hattını kurar | README birebir §5.2 |
| 5 | `WDMAUD` + `WINMM.DLL` uygulamaya MME API açar | README §5.4 + `dll/win32/winmm/` |

> 1. adımın içeriği (PnP/ENUM) **bu turda doğrulanmadı** → ⚠️ VERIFICATION REQUIRED.
> Ayrıca adım 2'nin gerçekten derlendiğine dair kanıt **yok** (§4) — bu yol
> "mimari taslak" olarak okunmalıdır, "çalışan boot yolu" olarak değil.

---

## §7 Etiketli Dosya Ağacı İskeleti (uydurma yasak · yalnız envanter)

> Aşağıdaki blok ReactOS kaynağından alınmış kod **DEĞİLDİR**; §3 sayımının
> okunabilir özetidir.

```text
[ETİKET: envanter-özeti · kaynak: drivers/ ağacı + README · 2026-10-13]
drivers/multimedia/
├── CMakeLists.txt      → add_subdirectory(bdasup)  [TEK SATIR]
├── README              → ana kanıt (53 satır)
├── bdasup/             → derlemede aktif (dal dışı)
└── audio/              → DERLEME DIŞI (CMakeLists yok · 404)
    ├── mpu401_nt4/     → README: mpu401 (MPU-401 MIDI)
    ├── sb16_nt4.old/   → eski · README'de adı geçmiyor
    ├── sndblst.old/    → eski · README: sndblst (Sound Blaster)
    ├── sndblst_sys/    → ad kanıtı · içerik okunmadı ⚠️
    └── sound/          → içerik okunmadı ⚠️
```

---

## §8 Sınır

| Dışında olan | Nereye |
|---|---|
| Win32 genel sürücü modeli/imzalama | `../../isletim-sistemi-turu/reactos/reactos-surucu-modeli.md` |
| Çekirdek mimarisi | `../../isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi.md` |
| Windows gerçek sürücü yığını | `../wasapi/wasapi-surucu-modeli.md` |
| Linux ALSA sürücüsü | `../alsa/alsa-surucu.md` |
| WINMM export listesi (usermode) | `reactos-audio-ornekler.md` |
| Katman yığını ve Vista/AvStream tartışması | `reactos-audio-anayasa.md` |

---

## §9 Ölçüm

| Metrik | Değer |
|---|---|
| Doğrulanan URL | 6 ([D1]-[D6] · 1'i negatif/404) |
| Sayılan dizin | drivers/ 22 · multimedia/ 4 öğe · audio/ 5 alt dizin |
| README | 53 satır tam metin indirildi · alıntılar birebir |
| Derleme iddiası | 2 bağımsız kanıt (üst CMake tek satır + alt CMake 404) |
| Etiketli kod bloğu | 1 (envanter-özeti · gerçek kod 0) |
| CoreMusic kesişimi | bu dosyada taranmadı → ⚠️ (anayasa/ornekler dosyalarında 0) |

---

## §10 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| S-Q1 | `.old` dizinleri derleme dışında mı? | ✅ KAPANDI (2026-10-13) | Evet — `audio/` derleme grafiğinin tamamen dışında (§4) |
| S-Q2 | `sound/` alt dizininin içeriği ne? | ✅ KAPANDI | 13 dosya + `include/` — §3.3.1 envanteri (sound/wave/dsp/mixer/sb16 iskeletleri) |
| S-Q3 | `ksfilter/` KS başlıklarıyla ilişkili mi? | ⚠️ VERIFICATION REQUIRED | Açık — dizin adı dışında kanıt yok |
| S-Q4 | Gerçek donanımda sürücü yüklendi mi (boot testi)? | UNKNOWN | Açık — test kanıtı yok |
| S-Q5 | `audio/` ağacı derlemeye ne zaman girer? | ⚠️ VERIFICATION REQUIRED | Yeni — §4'ten sonra doğan soru |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
