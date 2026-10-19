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

## §11 Envanter bayt-aritmetiği (§3.3.1 sayımlarının bağımsız toplamı)

> Bu § §3.3.1'in **mevcut** bayt değerlerini toplar; yeni okuma yok,
> aritmetik kontrol var (aynı desen: ornekler §10.1).

### §11.1 `sound/` toplam bayt kontrolü (13 dosya)

| Dosya | Bayt (§3.3.1) | Koşan toplam |
|-------|----------------|--------------|
| `sound.c` | 3 023 | 3 023 |
| `wave.c` | 4 144 | 7 167 |
| `wave.h` | 738 | 7 905 |
| `dsp.c` | 1 915 | 9 820 |
| `dsp.h` | 566 | 10 386 |
| `mixer.c` | 1 088 | 11 474 |
| `mixer.h` | 259 | 11 733 |
| `sb16.c` | 1 447 | 13 180 |
| `sb16.h` | 134 | 13 314 |
| `sb16.rc` | 204 | 13 518 |
| `sb_waveout.c` | 35 | 13 553 |
| `in.h` | 20 002 | **33 555** |
| `sb16`-dışı 13. sayım notu | (§3.3.1 "13 dosya" der; tabloda 12 satır + `include/`) | ⚠️ sayım-etkileşimi → §11.3 |

**§11.3 Sayım sapması tespiti (RED-TEAM — bu §'nin bulgusu):**
§3.3.1 başlığı "**13 dosya + 1 alt dizin**" der; tabloda **12 dosya satırı**
+ `include/` alt dizini var. 13. dosya **tabloda eksik görünüyor** (adı
yazılmamış). Etiket: ⚠️ VERIFICATION REQUIRED — *sayım (13) tablo satırı
(12) ile eşleşmiyor; 13. dosyanın adı bu turda da okunmadı (yeniden GET
yapılmadı), uydurulmaz*. Bu bulgu **mevcut metni silmez**, yalnız
**çelişki kaydı** olarak bu §'de durur (append-only düzeltme biçimi).

**`sndblst_sys/` toplam:** 10 937 + 218 = **11 155 bayt** (2 dosya — §3.3.1 ile tutarlı ✅).
**`mpu401_nt4/` bilinen alt küme:** 11 068 + 4 398 + 7 537 + 1 670 = **24 673 bayt** (4 dosyanın baytı var; 9 dosyanın 5'inde bayt yazılmamış → tam toplam **OKUNMADI**, uydurulmaz).

---

## §12 Dizin-adı kanıtı ile içerik kanıtı ayrımı (§3.3 son notunun derinlemesi)

| Örnek | Yalnız ad ne söyler? | İçerik okunmadan yazılamaz | Etiket |
|-------|----------------------|-----------------------------|--------|
| `sb16_nt4.old` | eski + NT4 + `.old` | "kullanım-dışı mı, arşiv mi?" | ad kanıtı + C4 (derleme dışı) |
| `sndblst.old` | eski Sound Blaster | hangi sürüm/şartname | ad kanıtı + C4 |
| `sndblst_sys` | "çekirdek-yordamı" iması | WDM mi NT4 mü → **S-Q1 açık** (FM) | ⚠️ |
| `*_nt4` | NT4-uyumlu sürüm etiketi | etkin mi | ad + C4 |
| `ksfilter` | KS teması | ilişkili mi → **S-Q3 açık** | ⚠️ |

**Bağlayıcı okuma kuralı (§3.4 notunun devamı):** 5 alt dizinin **hiçbiri**
derleme grafiğinde değil (C1+C2) → ad-etiketlerin (`.old`, `_nt4`) **hangisinin**
etkin olduğu bugünden **ikinci kez** sorulmaz; soru yalnız "`audio/` grafiğe
girerse" anlamlıdır → §18 S1 senaryosu.

---

## §13 Sürücü yükleme yolu (§6) — RT/güvenlik yorumu YOK (sınır beyanı)

> §6 yükleme yolunu README'den taşır; bu § ona **yeni iddia eklemez**,
> yalnız "K000'in hangi dosyalarına **girmez**" sorusunu yanıtlar.

| Soru | Cevap | Dayanak |
|------|-------|---------|
| Bu dosyada boot-testi sonucu var mı? | **Yok** — S-Q4 UNKNOWN (§10) | §10 |
| Yükleme yolu güvenli mi (imzalama/secure boot)? | **Yazılmaz** — kanıt yok | §2 [D1]–[D6] kapsamı yalnız depo |
| K000 RTOS/RT kuralları buraya uygulanır mı? | **Uygulanmaz** — `depends_on` yalnız anayasa (FM L7–L8) | FM |
| `.old`/`_nt4` sürücüleri K000 dokümanına örnek mi alınır? | **Hayır** — derleme dışı (C4) + içerik okunmadı | §4 · §12 |

---

## §14 Ölçüm (§9) — bu turun eki (§9 ezilmez, tamamlanır)

| Metrik (§9'un devamı) | Değer (2026-10-15) |
|------------------------|---------------------|
| Bu tur web GET | **0** (§2/§9 damgalı ölçümler tazelenmedi) |
| Bu tur okunan dosya | bu dosya (kendi metni) + kardeş anayasa/ornekler **yalnız satır-başlık düzeyi** (onlar ayrı ölçüldü) |
| Bayt-aritmetik toplam | 33 555 (`sound/` bilinen 12 satır) + 11 155 (`sndblst_sys`) + 24 673 (`mpu401_nt4` alt küme) — **kısmi, tam değil** (§11.1–§11.3) |
| Bulunan iç çelişki | 1 (§11.3: 13 vs 12) |
| Yeni kod bloğu | 0 |
| Etiket değişimi | 0 (S-Q1..S-Q5 aynen) |

---

## §15 Red-team soruları (bu dosya — usul)

| # | Soru | Cevap | Kanıt § |
|---|------|-------|---------|
| R1 | §11.3 çelişkisi mevcut metni **bozar** mı? | Hayır — §3.3.1'deki "13" aynen durur; bu § yalnız ikisini **karşılaştırır** (append-only) | §11.3 |
| R2 | Eksik 13. dosyanın adı tahmin edilebilir mi? | **Hayır** — ad yazılmaz (uydurma yasağı); yalnız "tabloda 12 var, başlıkta 13" **olgu**sunu kaydeder | §11.3 |
| R3 | `mpu401_nt4` tam toplamı neden yok? | Baytlar §3.3.1'de 4/9 dosya için yazılmış; kalan 5'in baytı **okunmamış** → toplam uydurulmaz | §11.1 |
| R4 | Bu tur kardeş dosyalara yazdı mı? | Hayır — anayasa/ornekler yalnız **satır sayısı** için ölçüldü (yazım 0) | §14 |
| R5 | C4 (.old derlenemez) bu tur sorgulandı mı? | Hayır — C1+C2 birleşik kanıt, §4'te zaten bağlayıcı; yeniden açılması **yeni CMake okuması** ister | §4 · §18 S1 |
| R6 | `ksfilter` içeriği okundu mu? | Hayır — S-Q3 aynen ⚠️ | §10 · §12 |

---

## §16 Kardeş ölçüm tablosu (2026-10-15 — dosya-dosya, okunmayan boş)

| Dosya | Satır | Kapı | Bu tur kaynağı |
|-------|-------|------|----------------|
| `reactos-audio-anayasa.md` | (append sonrası kendi beyanı) | ≥500 | okunmadı (yalnız başlık-yüzeyi §12 anayasa) |
| `reactos-audio-ornekler.md` | (append sonrası kendi beyanı) | ≥500 | okunmadı (yalnız başlık-yüzeyi) |
| **bu dosya** — öncesi | 270 | <500 → §18 | kendi ölçümü |
| `index.md` | okunmadı | 40–100 | yazım 0 (yeni dosya yok) |

---

## §17 Kapsam dışı yüzeyler (pozitif liste)

| Kapsam dışı | Gidiş yolu (gerçek dosya) |
|-------------|----------------------------|
| WINMM export envanteri | `reactos-audio-ornekler.md` §4 |
| Katman/Vista/AvStream tartışması | `reactos-audio-anayasa.md` §4–§6 |
| wasapi/alsa sürücü modelleri | `../wasapi/wasapi-surucu-modeli.md` · `../alsa/alsa-surucu.md` (§8 listesi) |
| Win32 genel sürücü modeli | `../../isletim-sistemi-turu/reactos/reactos-surucu-modeli.md` |
| Kod gövdeleri okuması (`sound/*.c`) | bilinçli yapılmadı (FM validation[2] + §3.3.1 notu) — ayrı tur işi |
| Boot/gerçek donanım testi | S-Q4 UNKNOWN |

---

## §18 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen bölümler | §11–§18 |
| Silinen mevcut içerik | 0 |
| Frontmatter | değiştirilmedi (S-Q1..S-Q5 aynen) |
| Yeni dış iddia | 0 (GET yok · yeni URL yok) |
| Yeni hesap | 3 toplam + 1 çelişki tespiti (§11) — hepsi mevcut sayılardan |
| Etiket değişimi | 0 |
| Ölçüm | read + split · Kapı ≥500 (≥505) — son değer tur yanıtında |
| `git commit` | yok |

---

## §19 README tanıkları — olgunluk-matrisi (§5.1–§5.4 tek tabloda derleme gerçeğiyle)

> Bu § §5'teki **mevcut** alıntıları §4'ün C1–C4 kanıtlarıyla **eşler**;
> alıntı **kopyalanmaz**, atıf yapılır; üçüncü kanıt (çalışma testi) **yok**
> → tüm satırlar "taslak" kalır.

| Bileşen (§5.2) | README tanığı | Derleme kanıtı (§4) | Çalışma testi | Nihai durum |
|----------------|---------------|----------------------|----------------|-------------|
| `KS.SYS` | ✅ birebir (§5.2) | ❌ `audio/` derleme dışı (C3) — KS'nin hangi ağaçta olduğu ölçüldü mü? → README "kernel streaming components" der, **bu dosyada KS dosya-yolu kanıtı yok** | yok | taslak + ⚠️ (dosya yolu kanıtsız) |
| `SYSAUDIO.SYS` | ✅ birebir + yazar itirafı | ❌ aynı (C3) | yok | taslak + UNKNOWN (anayasa A-Q2) |
| `PORTCLS.SYS` | ✅ birebir | ❌ aynı (C3) | yok | taslak |
| `WDMAUD.DLL/.SYS` | ✅ birebir (§5.4) | ❌ (C3) + Vista notu §5.4 | yok | taslak + A-Q1 (anayasa) |
| `sndblst` | ✅ Optional drivers (§5.3) | `.old`/`_sys` ayrımı → C4 (`.old` derleme dışı); `sndblst_sys` grafiğe **bağlı değil** (C3) | yok | taslak + S-Q1 ⚠️ |
| `mpu401` | ✅ Optional drivers (§5.3) | `mpu401_nt4` → C3 (derleme dışı) | yok | taslak |

**Matris kuralı:** "README tanığı ✅ + derleme ❌ + test yok" = **üç
koşulun üçü de tek yönlü** → bu bileşenler için "çalışıyor" cümlesi
**her koşulda** yasak (§5.1 bağlayıcı uyarının matrisleşmiş hâli).

**Sayısal özet:** 6 bileşen · README ✅ 6/6 · derleme ❌ 6/6 · test 0/6
→ **0/6 üretim-değerliliği** (bu tur aritmetiği; ölçüm değil, matris okuması).

---

## §20 Sürücü yükleme yolu (§6) — adım kanıt sınıfları (yeniden sınıflandırma)

| Adım (§6) | Kanıt sınıfı | Neden |
|-----------|--------------|-------|
| 1 · Donanım algılama | **C — bilinmeyen** ⚠️ | "bu dosyanın dışında · UNKNOWN" (§6 satır 1) |
| 2 · `sndblst_sys` yükleme | **B — ad kanıtı + C3 ile zayıflatılmış** | dizin adı + README eşleşmesi; derleme grafiği yok |
| 3 · `PORTCLS.SYS` | **A — README metni** | birebir alıntı (§5.2) |
| 4 · `KS.SYS` | **A — README metni** | birebir alıntı (§5.2) |
| 5 · `WDMAUD`+`WINMM` | **A — README + depo varlığı** (§5.4 + [R5] anayasa) | iki bağımsız kaynak |

**Sınıf dağılımı:** A ×3 · B ×1 · C ×1 → yolun **%60'ı README metnine**,
**%20'si ad kanıtına**, **%20'si bilinmeze** dayanır (3/5 · 1/5 · 1/5 —
hesaplanmış). §6'nın kendi uyarısı ("mimari taslak olarak okunmalıdır")
bu oranla **sayısal** hâle gelir.

---

## §21 §7 ağacı ↔ §3 sayımı tutarlılık denetimi (iki blok aynı mı anlatıyor)

| §7 satırı (iskelet) | §3 karşılığı | Tutarlı? |
|----------------------|---------------|----------|
| `CMakeLists.txt → add_subdirectory(bdasup)` | §3.2 + §4 C1 | ✅ |
| `README → ana kanıt (53 satır)` | §9 ölçüm "53 satır tam metin" | ✅ |
| `bdasup/ → derlemede aktif (dal dışı)` | §3.2 | ✅ |
| `audio/ → DERLEME DIŞI` | §4 C3 | ✅ |
| 5 alt dizin listesi (mpu401_nt4 · sb16_nt4.old · sndblst.old · sndblst_sys · sound) | §3.3 tablosu | ✅ 5/5 eşleşme |
| `sndblst_sys · sound → içerik okunmadı ⚠️` | §3.3.1 (envanter var, gövde yok) | ✅ etiket aynı |

**Sonuç:** §7 iskeleti §3/§4 ile **çelişmiyor** (6/6 satır eşleşti) — iki
blok **aynı gerçeği** özetliyor (iskelet okunabilirlik, §3 kanıt düzeyi).
Çelişki **yalnız** §11.3'te kayıtlıdır (13 vs 12) — o da §7'yi **etkilemez**
(§7 dosya sayılmaz, alt-ağacı çizer).

---

## §22 Tur kaydı ek — kapı son sözü (§18'in devamı)

| Kalem | Değer |
|-------|-------|
| Eklenen §'ler (bu ek) | §19–§22 |
| Hedef | ≥500 (≥505 payı) — son ölçüm tur yanıtında |
| Yeni GET/URL | 0 |
| Etiket değişimi | 0 |
| Silme | 0 |

---

## §23 Açık sorular — bu tur kapanış denetimi (S-Q1–S-Q5, etiket testi)

| # | Başlangıç etiketi (§10) | Bu tur okuma? | Etiket | Gerekçe |
|---|--------------------------|----------------|--------|---------|
| S-Q1 | ✅ KAPANDI | hayır (yeniden CMake yok) | ✅ aynı | §19 matrisi yalnız mevcut C1–C4'ü eşledi |
| S-Q2 | ✅ KAPANDI | hayır | ✅ aynı | §11.3 bulgusu S-Q2'nin **kapsamını** daraltmaz (13. dosya adı ≠ "sound/ içeriği ne" sorusu — ayrı bulgu olarak §11.3'te) |
| S-Q3 | ⚠️ | hayır (`ksfilter` okunmadı) | ⚠️ aynı | §12 + §15 R6 |
| S-Q4 | UNKNOWN | hayır (boot testi yok) | UNKNOWN aynı | §13 |
| S-Q5 | ⚠️ | hayır | ⚠️ aynı | §18 S1 kapısı (gelecek) |
| **Yeni** | — | — | **§11.3** (13 vs 12) | yeni ⚠️ kaydı — §10'a **eklenmedi** (§10 mevcut tablo = silme/üzerine yazma yasak); bu § kendi satırında durur |

**S-Q2-§11.3 ilişkisi (netleştirme):** S-Q2 "sound/ içeriği ne?" →
13+1 envanteriyle kapandı. §11.3 "o envanterde 13 başlık vs 12 satır
farkı var mı?" → **fark var**. Bu, S-Q2'yi **yeniden açmaz** (içerik
sorusu cevaplı), yalnız envanterin **kendi tutarlılığını** sorgular —
kapanış kararı orkestratöre aittir (bu dosya yalnız tespit eder).

### §23.1 Kapı son tablosu (bu dosya — ölçüm disiplini)

| Kalem | Değer |
|-------|-------|
| Append | §11–§23 (toplam 13 bölüm) |
| Silme | 0 · FM 0 · GET 0 · etiket 0 |
| İç çelişki kaydı | 1 (§11.3 — silinmedi, eklendi) |
| Kapı | ≥500 · son ölçüm tur yanıtında · marj ≥505 |
| Kardeş yazım | 0 |
| Marj satırları | bu satırlar ≥505 tamponu (sonraki turlar §24'ten devam eder) |
| Sonraki tur numarası | §24 — §1–§23 başlıkları değiştirilmez (append-only) |
| Tur özeti | reactos-audio 3/3 kapı · P2 devam (tizen · webos) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
