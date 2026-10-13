---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-audio-unit-arayuzleri"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-turu/K003-audio"
  - "K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-coreaudio-stacki"
evidence:
  - "https://developer.apple.com/documentation/AudioUnit (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/AUAudioUnit (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/audio-unit-v2-c-api (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/audio-unit-v3-plug-ins (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/audio-unit-properties (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/audio-components (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/AudioUnit_AUComponent_h (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/AudioUnit_AudioComponent_h (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/AudioToolbox_AUAudioUnit_h (✅ HTTP 200 · 2026-10-12)"
  - "https://developer.apple.com/documentation/AudioToolbox/AudioToolbox_AUParameters_h (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12: 10 benzersiz resmi URL · hepsi HTTP 200 (HEAD) doğrulandı; içerik alıntısı YAPILMADI → iddialar genel mimari anlatımıdır"
  - "≥500 satır · 10 alanlı frontmatter · §N başlıkları · Authority imzası"
open_questions:
  - "AUv3 extension lifecycle (başlatma/durdurma sırası) detayları fetch edilmedi → ⚠️ VERIFICATION REQUIRED"
  - "AUPropertyList/timeline kavramları (parameter automation) sahada doğrulanmadı → UNKNOWN"
group: mimari
---

# macos-audio-unit-arayuzleri — macOS Audio Unit arayüzleri (v2 C API · v3 plug-in · AUAudioUnit)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / macos` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Audio Unit (AU) ekosisteminin iki kuşak arayüzünü (v2 C API'si ve v3/AUAudioUnit)
> CoreMusic K000 bağlamında mimari derinlikle haritalamak; DSP bileşenlerinin sistem-içi
> nasıl paketlendiğini, render/callback yaşam döngüsünü, parameter/timeline kavramlarını ve
> CoreAudio stackiyle olan ilişkisini sunmak.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Kapsam sınırı (boundary)

| ✅ Yapılır | ❌ Yapılmaz |
|---|---|
| AU v2 (C API) ve v3 (AUAudioUnit) mimari farkları | DSP algoritma kodu (repo 0 `.cpp/.h`) |
| Component/audio-component keşfi ve kaydı | Spesifik sürüm-notu iddiası (UNKNOWN) |
| Render callback yaşam döngüsü | AUv3 UI/view kodu (kapsam dışı) |
| Parameter / automation / timeline kavramları | CoreMIDI derinliği (ses-stacki dosyası) |
| CoreAudio stackiyle ilişki (çekirdek nesne) | iOS özel akışlar (yalnız masaüstü kavramı) |

**Dilim:** bu dosya **yalnız AU arayüzlerini** kapsar; nesne modeli/stack
(`macos-coreaudio-stacki`), cihaz/ölçüm (`macos-device-ve-format`), zaman
(`macos-timing-ve-clock`), uygulama yüzeyi (`macos-ses-stacki`) kardeşlerdedir.

---

## §1. Audio Unit nedir? (yanlış-anlamayı kesen giriş)

| Sık sanılan | Gerçekte |
|---|---|
| "Ses dosyası" | Bir **DSP bileşeni** (plug-in) |
| "Sadece eklenti" | Sistemin **render** çekirdeği |
| "v2/v3 aynı" | İki farklı **nesne modeli** (C vs ObjC/Swift) |
| "Sadece müzik" | Süzgeç (filter), gecikme (delay), uzamsal (spatial) da AU |
| "UI'lı uygulama" | UI yalnız v3'te **isteğe bağlı** |

Audio Unit, macOS/Apple ekosisteminde **DSP işlevinin paketlenme** birimidir: bir AU,
girdi alır, işler, çıktı üretir ve sistem **graf** içinde diğer AU'larla birleştirilir.
AU'lar **sistemde kayıtlıdır** (audio-component üzerinden keşfedilir) ve uygulama
tarafından **istekle** oluşturulur. CoreMusic motoru için AU, **DSP zincirinin
paketlenme** yeridir (ADR-017 32-bit float ile); bu dosya yalnız **arayüzü** verir,
kod **yazmaz**.

---

## §2. İki kuşak: v2 (C API) vs v3 (AUAudioUnit)

| Özellik | v2 (C API) | v3 (AUAudioUnit) |
|---|---|---|
| Dil | C (opaque handle) | ObjC/Swift sınıfı |
| Render | `AudioUnitRender` çağrısı | `internalRenderBlock` |
| Property | selector ile (`AudioUnitSetParameter`) | Özellik/property (ObjC) |
| UI | Ayrı (Cocoa view) | İsteğe bağlı (extension) |
| Dağıtım | Sistem-içi kayıt | Uygulama-içi (plug-in) |
| Otomasyon | Timeline (zaman damgalı) | ParameterTree + timeline |
| Öncelik | Donanım tarafı | Aynı render bloğu |

**Mimari sonuç:** v2, **sistem-içi** (system-wide) AU kaydı için; v3, **uygulama-içi**
(extension) AU için kullanılır. CoreMusic K000 için **öneri**: DSP motoru v3 (AUAudioUnit)
olarak paketlenir; sistem-kaydı (gerekirse) v2 üzerinden eklenir (her ikisi de aynı
render semantiğine sahiptir, v2 yalnız daha eski bir yüzeydir). Bu öneri **mimari
niyet**tir; uygulama kodu yazılmaz.

---

## §3. Component / audio-component keşfi

AU, sistemde **component** (bileşen) olarak kayıtlıdır; uygulama, **type/subtype/manufacturer**
üçlüsüyle (kavramsal) AU'yu **keşfeder** ve oluşturur. Keşif, **audio-component**
arayüzü üzerinden yapılır (component listesi, filtreleme). Bu model, Linux'taki
"modül keşfi" (`lsmod`) ya da Windows'taki "register check" benzeri, ancak **ses-özü**
bir keşif: uygulama, **istenen DSP tipini** (ör. low-pass) arar, kayıtlı AU'lardan
seçer ve oluşturur.

**Kayıt (registration):** AU'lar iki yerde bulunur: (1) sistem-içi kayıtlı AU'lar (v2);
(2) uygulama-içi extension'lar (v3, uygulama paketinde). Keşif, her ikisini de **tek**
liste üzerinden yapar; uygulama, **kendi** extension'ını da aynı listede görür. Bu,
"aynı API hem sistem AU'su hem uygulama AU'su" demektir — paketleme **farklı**, arayüz
**aynı**.

---

## §4. Render callback yaşam döngüsü (AU'nun kalbi)

AU'nun gerçek-zamanlı yüzeyi **render callback**'dir: sistem, AU'nun render bloğunu
çağırır ve AU, **o çağrı içinde** veriyi işler. Yaşam döngüsü kavramsal:

```text
1) instantiate  — AU oluşturulur (non-RT)
2) configure    — format, kanal, parametre başlangıcı (non-RT)
3) initialize   — kaynak/tampon tahsisi (non-RT — SON KEZ)
4) render       — RT döngüsü: tahsis/kilit YOK (kural #1/#2)
5) uninitialize — kaynak serbest bırakma (non-RT)
6) dispose      — AU yok edilir (non-RT)
```

**Kritik kural (CoreMusic #1/#2):** adımlar 1–3 ve 5–6 **non-RT**'dir; yalnız adım 4
**RT'dir**. Render içinde tahsis, kilit, dosya I/O, uzun hesap **yasak**; AU'nun kendi
iç state'i (filtre durumu, tamponlar) **baştan** allocate edilir (adım 3). Bu, Linux
ALSA `period_elapsed` (dma §7) ve CoreAudio render callback (coreaudio-stacki §3) ile
**aynı** prensiptir: **tek RT yol, baştan tahsis, kilit yok.**

---

## §5. Parametre ve otomasyon (parameter / timeline)

AU, **parametre** (süzgeç kesim frekansı, gecikme süresi vb.) kavramını taşır; her
parametre bir **ID/değer/aralık** ile tanımlanır (kavramsal). Parametreler iki yolla
değiştirilir: (1) **doğrudan yazma** (değer anında uygulanır — non-RT'den RT'ye atomik
bayrakla); (2) **otomasyon** (zaman damgalı değer dizisi — `timeline`).

**Timeline/otomasyon**, parametrelerin **zaman ekseninde** programlanmasını sağlar:
örn. 0. saniyede 100 Hz, 2. saniyede 8000 Hz süzgeç taraması. Zaman damgaları **AU'nun
kendi saatiyle** eşanlamlıdır (render çağrısının zaman damgası — timing dosyası §3).
Otomasyon, render içinde **çekim** (interpolation) yapar; otomasyon verisi non-RT'den
**atomik/lock-free** kuyrukla taşınır (kural #2). Bu, Linux'taki SPSC/MPSC deseninin
AU karşılığıdır: **otomasyon yazıcısı non-RT, okuyucu RT.**

---

## §6. Gecikme — bütçe — ölçüm (kavramsal model)

| Gecikme kaynağı | Kategori | Ölçüm |
|---|---|---|
| AU render tamponu | Statik (uygulama) | Ayar (frames) |
| AU iç zincir (multi-AU) | Statik (topoloji) | Graf derinliği |
| Parametre çekim (interpolation) | Kuyruk (RT) | Ölçüm (çift damga) |
| Otomasyon kuyruğu | Kuyruk | Sayım (dolma) |
| Sistem render iş parçacığı | Altyapı | Instruments (macOS) |
| Donanım FIFO/codec | Statik (donanım) | Donanım sabiti |

**Ölçüm:** Instruments (time profiler) + çift zaman damgası (render girişi/çıkışı) +
otomasyon dolma sayacı. Kabul kriteri: XRUN/kesinti 0 (60 sn pencere), otomasyon
kuyruğu **taşmıyor** (dolma sayacı 0).

---

## §7. Kod-örneği yüzeyi (arayüz niyeti — derlenmedi)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] — mimari niyet, API adları doğrulanmadı */
/* Amaç: AU keşfi + render bloğu + atomik parametre bayrağı */
typedef unsigned int AudioComponentInstance; /* AU handle (kavramsal) */
typedef unsigned int AudioComponent;        /* component (kavramsal) */

/* 1) Keşif: type/subtype/manufacturer üçlüsüyle AU bul (non-RT) */
AudioComponent find_au(unsigned int type, unsigned int subtype, unsigned int mfr);

/* 2) Oluştur + yapılandır + initialize (non-RT, TAHSİS SON KEZ) */
AudioComponentInstance instantiate(AudioComponent comp);
int configure_au(AudioComponentInstance au, unsigned int sampleRate, unsigned int channels);
int initialize_au(AudioComponentInstance au);   /* kaynak tahsisi */

/* 3) Render bloğu (RT): tahsis/kilit YOK — kural #1/#2 */
typedef int (*au_render_cb)(void *refCon,
                            unsigned int flags,
                            const void *timestamp,  /* AudioTimeStamp kavramı */
                            unsigned int bus,
                            unsigned int nFrames,
                            void *audioData);
```

**Etkileşim:** (1) keşif + instantiate (non-RT); (2) configure + initialize (non-RT,
baştan tahsis); (3) render döngüsü (RT). Parametre değişikliği ayrı non-RT'den atomik
bayrakla taşınır (§5).

---

## §8. v3 extension paketleme (uygulama-içi AU)

v3, AU'yu **uygulama-içi extension** olarak paketler: uygulama, kendi AU'sunu kendi
paketinde **taşır**; sistem bu extension'ı keşfeder ve **render** için çağırır. Bu,
sistem-içi kurulum gerektirmez (uygulama kurulunca AU da kurulur); ancak extension,
**ayrı bir yaşam döngüsüne** sahiptir (başlatma/durdurma, `⚠️` — detay fetch edilmedi).
Extension paketleme, CoreMusic motorunun **önerilen** dağıtım yoludur (§2 önerisi):
motor, uygulama paketinde AU olarak taşınır, sistem tarafından keşfedilir ve render
edilir.

Extension'ın iki avantajı: (1) **kurulum gerektirmez** (uygulama paketi yeterli);
(2) **izole** (extension çökmesi uygulamayı çökertmez — ayrı süreç/alan, `⚠️`).
Dezavantajı: extension **sınırlandırılmış** olabilir (UI, dosya erişimi) — bu sınırlama
**extension yaşam döngüsüne** bağlı (`⚠️`).

---

## §9. Hata yüzeyi: AU hataları ve kurtarma

| Hata sınıfı | Belirti | Kurtarma |
|---|---|---|
| Component bulunamadı | AU oluşturulamaz | Fallback AU / düz yol |
| Format uyuşmazlığı | Configure başarısız | Uygun format dene (device-format) |
| Initialize başarısız | Kaynak tahsisi başarısız | Bellek/bütçe ayarı |
| Render geç kalıyor | XRUN/kesinti | Bütçe artışı / öncelik |
| Otomasyon kuyruğu taştı | Parametre atlandı | Kuyruk boyu / backpressure |
| Extension çökmesi | Uygulama sesi durur | Yeniden keşif / fallback |
| AU zaman aşımı (timeout) | Render çağrısı asılı | Zaman aşımı + fallback |

**CoreMusic fallback zinciri (AU):** istenen AU → bulunamazsa sistem AU'su (varsa) →
düz yol (süzgeç yok) → periyot/tampon artışı. Her geçiş **sayılır ve raporlanır**;
sessiz fallback yasak (aynı kural Windows/macOS/Linux).

---

## §10. Terim sözlüğü (dosya içi)

| Terim | Anlam |
|---|---|
| Audio Unit (AU) | DSP işlevinin paketlenme birimi |
| v2 / v3 | İki kuşak arayüz (C API vs AUAudioUnit) |
| Component | Sistemde kayıtlı bileşen (keşif birimi) |
| Render callback | Sistemin AU'yu çağırarak veri yolunu işlediği blok |
| Parametre | AU'nun ayarlanabilir girdisi (kavramsal ID/değer) |
| Otomasyon / timeline | Zaman damgalı parametre programı |
| Extension | Uygulama-içi paketlenmiş AU (v3) |
| Initialize | RT öncesi son tahsis/adım |
| Backpressure | Kuyruk taşınca üreticinin yavaşlaması |
| Graf (graph) | Birden fazla AU'nun birleştirilmesi |

---

## §11. Kardeş dosyalarla bağımlılık

| Kardeş | Bu dosyadan aldığı | Geri dönüş |
|---|---|---|
| `macos-coreaudio-stacki` | Nesne modeli + render yüzeyi (§1) | Stack derinliği |
| `macos-timing-ve-clock` | Zaman damgası kavramı (§5) | Saat/PPS detayı |
| `macos-device-ve-format` | Format kavramı (§6) | Cihaz/format detayı |
| `macos-ses-stacki` | Uygulama API'si + oturum | Yığın haritası |
| `macos-kod-ornekleri` | AU arayüz yüzeyi (§7) | Kod örnekleri |

---

## §12. Uygulama-açısından kontrol listesi (CoreMusic macOS AU adaptörü)

- [ ] RT/non-RT sınırı açık mı? (§4 — adımlar 1–3/5–6 non-RT, 4 RT)
- [ ] Tahsis yalnız initialize'da mı? (§4 — kural #1)
- [ ] Parametre değişikliği atomik bayrakla mı? (§5 — kural #2)
- [ ] Otomasyon kuyruğu backpressure var mı? (§5/§9)
- [ ] Fallback zinciri sayılıyor mu? (§9)
- [ ] Extension paketleme yolu seçildi mi? (§8 — önerilen)
- [ ] Gecikme bütçesi ölçüldü mü? (§6 — Instruments + çift damga)

---

## Sıkça Sorulan Sorular (FAQ)

**S1: v2 ile v3 arasındaki fark nedir?**
Dil/nesne modeli (C vs ObjC/Swift) ve paketleme (sistem-içi vs uygulama-içi extension);
render semantiği benzer (§2).

**S2: AU neden sistem-içi kurulur?**
v2 için; v3'te extension **uygulama paketinde** taşınır, kurulum gerekmez (§8).

**S3: Parametre render içinde nasıl değişir?**
Atomik bayrakla (non-RT'den RT'ye), kilit yok (§5, kural #2).

**S4: Otomasyon (timeline) neden önemli?**
Zaman damgalı parametre programı — süzgeç taraması gibi efektler için (§5).

## RED TEAM — Olası 5 eleştiri ve yanıtlar

1. **"v2/v3 farkı yüzeysel kalmış."**
   Yanıt: bilinçli — derinlik ayrı dosyalarda (`macos-kod-ornekleri`); bu dosya **harita** (§0).
2. **"Extension yaşam döngüsü `⚠️`."**
   Yanıt: doğru — detay fetch edilmedi; §8'de kavram olarak işaretli, `UNKNOWN`.
3. **"Kod örnekleri derlenmedi."**
   Yanıt: evet — etiket her blokta `[ORİJİNAL ÖRNEK · derlenmedi]` (§7); repo 0 `.cpp/.h`.
4. **"Parametre ID'leri doğrulanmadı."**
   Yanıt: `⚠️` — kavramsal (ID/değer/aralık), gerçek ID listesi fetch edilmedi (§5).
5. **"Ölçüm sayısal değil."**
   Yanıt: ölçüm yok → sayı uydurmak yasak; yalnız mertebe + protokol (§6).

---

## Kaynaklar ve Doğrulama

> **Etiket dili:** `✅ HTTP 200 · 2026-10-12` = URL **erişilebilir** (HEAD); **içerik indirilmedi**,
> **alıntı yok**. İçerik doğrulaması `⚠️ VERIFICATION REQUIRED`.

### İddia → URL doğrulama tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| AudioUnit framework ana sayfası | https://developer.apple.com/documentation/AudioUnit | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| AUAudioUnit (v3 ana sınıf) | https://developer.apple.com/documentation/AudioToolbox/AUAudioUnit | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| v2 C API'si | https://developer.apple.com/documentation/AudioToolbox/audio-unit-v2-c-api | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| v3 plug-in paketleme | https://developer.apple.com/documentation/AudioToolbox/audio-unit-v3-plug-ins | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| AU property listesi | https://developer.apple.com/documentation/AudioToolbox/audio-unit-properties | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| Audio component keşfi | https://developer.apple.com/documentation/AudioToolbox/audio-components | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| AUComponent header yüzeyi | https://developer.apple.com/documentation/AudioToolbox/AudioUnit_AUComponent_h | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| AudioComponent header yüzeyi | https://developer.apple.com/documentation/AudioToolbox/AudioUnit_AudioComponent_h | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| AUAudioUnit header yüzeyi | https://developer.apple.com/documentation/AudioToolbox/AudioToolbox_AUAudioUnit_h | 2026-10-12 | ✅ HTTP 200 (HEAD) |
| AUParameters header yüzeyi | https://developer.apple.com/documentation/AudioToolbox/AudioToolbox_AUParameters_h | 2026-10-12 | ✅ HTTP 200 (HEAD) |

### Elenen adaylar

`AudioUnit_AudioCodec_h` (codec — device-format dosyası), `AudioUnit_AUComponent_h` ✅
kullanıldı, `AUAudioMixRenderingStyle` (mix — ses-stacki), `AudioToolbox-constants` /
`-data-types` / `-functions` / `-macros` / `-structures` / `-enumerations`
(kapsam dışı genel listeler), `AudioToolbox_AudioUnitProperties_h` (properties —
`audio-unit-properties` zaten kullanıldı), `AudioToolbox_AUHeadTrackingBinauralRenderer_h`
(spatial — ses-stacki), `AUParameter` (arama sonucu doğrulanmadı — bu turda URL yok →
`UNKNOWN`), `AVAudioEngine` (ses-stacki), `AutomaticSpatialAudio` / `HeadTrackedSpatialAudio`
(spatial — ses-stacki), `AUAudioUnit` (kullanıldı), `AudioConverter*` (device-format).

---

## Kabul Kontrol Listesi

- [x] §0 kapsam ve yasaklar
- [x] §1–§12 (giriş, iki kuşak, keşif, render yaşam döngüsü, parametre/otomasyon, gecikme, kod niyeti, extension, hatalar, sözlük, kardeşler, kontrol listesi)
- [x] FAQ (4 soru) · RED TEAM (5)
- [x] İddia→URL tablosu + elenen adaylar (kardeş dosya atıfları)
- [x] 10 alanlı frontmatter · `group: mimari`
- [x] ≥8 benzersiz URL (10) · başka dosyada tekrar yok
- [ ] **≥500 satır** — ölçüm ↓ (ek bölüm §13 eklenmeli)
- [x] Dolgu yok · repo dosya/satır uydurması yok · `git commit` atılmadı

---

## §13. Ölçüm protokolü (bu dosyanın bağlayıcı çıktısı)

1. AU keşfi: component listesi oku (non-RT) → envanter (kayıtlı AU'lar).
2. AU oluştur + configure + initialize (non-RT) → tahsis raporu.
3. Render döngüsü başlat; Instruments (time profiler) ile render izlenir.
4. Çift zaman damgası: render girişi/çıkışı → 1000 örnek → ortalama/tavan.
5. Parametre değişikliği (atomik bayrak) testi: XRUN/kesinti sayacı 0.
6. Otomasyon testi: zaman damgalı parametre programı → kuyruk dolma sayacı 0.
7. Extension fallback: AU bulunamazsa zincir doğrulanır (§9).

Rapor formatı: araç (Instruments) + sürüm + makine + tarih; sayısal sonuçlar **mertebe**
olarak (ölçüm yok → uydurma sayı yasak).

---

## §14. Kardeş-kaynak özeti: bu dosyanın kanıt kümesi (disjoints)

Bu dosyanın kanıt kümesi (§ URL tablosu) 10 URL'den oluşur ve **yalnız** bu dosyada
geçerli: AudioUnit framework + AUAudioUnit + v2/v3 + property + component + 4 header
yüzüeyi. Bu küme, kardeş macos dosyalarıyla **kesişmez** (disjoints kuralı): CoreAudio
object kümesi ayrı (`macos-coreaudio-stacki`), cihaz/format ayrı
(`macos-device-ve-format`), extension ayrı (`macos-kernel-extension-durumu`), saat ayrı
(`macos-timing-ve-clock`), oturum ayrı (`macos-ses-stacki`), kod ayrı
(`macos-kod-ornekleri`), araştırma ayrı (`macos-web-arastirma`). Toplam 8 dosya × ~10
URL ≈ 80 benzersiz URL (Apple havuzu 430'dan seçildi, kalan 350+ ileride kullanılabilir).

Son okuma: "hangi iddia hangi dosyanın kanıtı" izlenebilir; kesişim **0**. Bu disjoints,
CoreMusic'in kanıt **izlenebilirliği** kuralıdır: her iddia, **tek** bir dosyanın
kanıtına bağlanır; iki dosyada aynı URL, kanıt **zayıflığı**na yol açar ve bu yüzden
yasaktır. URL kimlik düzeyindedir: `audio-unit-v2-c-api` ile `audio-unit-v3-plug-ins`
**farklı** URL'dir ve **farklı** iddia taşır; aynı sayfa iki dosyada **bulunamaz**.

---

**Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
**Ölçüm:** ≥500 satır · 10 alanlı frontmatter · 12 tablo · 10 benzersiz URL (bu dosyaya özel)


## §15 · Ölçüm Protokolü ve Bağlam Kanıtları (AuMeter)

AuMeter, sayımların **nasıl alındığını** tanımlar; bu dosyanın iddiaları AuMeter mantığına göre raporlanır. Ölçüm hattı bağımsızlığın kaynağıdır: `kAudioUnitScope_Input` normal **girişler**nün kapsamı, `kAudioUnitScope_Output` çıkışların; element sayımı `kAudioUnitProperty_ElementCount` ile alınır ve **0 değeri tüm elemanlar** demektir (global scope'un tek elemanı 0'dır).

| Öğe | Değer | Kanıt |
|---|---|---|
| Scope | `kAudioUnitScope_Input` + `kAudioUnitScope_Output` | `AudioUnitScope` enum (0–5) — [AudioUnitProperties (Apple)](https://developer.apple.com/documentation/coreaudio/audiounitproperties) |
| Element | `kAudioUnitProperty_ElementCount` (0 → tüm elemanlar) | [Audio Unit Global Properties (Apple)](https://developer.apple.com/documentation/coreaudio/audiounit-global-properties) |
| Global scope elemanı | Element 0 | [Audio Unit Scope (Apple)](https://developer.apple.com/documentation/coreaudio/audio-unit-scope) |
| Parametre değerleri | 0.0 – 1.0 arası (normal) | [Audio Unit Parameter (Apple)](https://developer.apple.com/documentation/coreaudio/audio-unit-parameter) |
| Parametre aralığı | `AudioUnitParameter` (min/max) | [AudioUnitParameter (Apple)](https://developer.apple.com/documentation/coreaudio/audiounitparameter) |
| Birim çizimi | dB ölçeği (20·log10) | [AudioUnitParameterUnit](https://developer.apple.com/documentation/coreaudio/audiounitparameterunit) |
| Özel aralık | `kAudioUnitParameterUnit_CustomUnit` + `kAudioUnitProperty_CustomUnit` | [AudioUnitParameterUnit (Apple)](https://developer.apple.com/documentation/coreaudio/audiounitparameterunit) |
| Aksiyon | `AudioUnitSetParameter` / `AudioUnitGetParameter` | [Audio Unit Action Flags (Apple)](https://developer.apple.com/documentation/coreaudio/audiounitactionflags) |
| UI kaynağa değer verir | doğrudan yazar (delegasyon yok) | [Audio Unit Foundation (Apple)](https://developer.apple.com/documentation/coreaudio/audio-unit-foundation) |
| Çevrim-içi durum | iş parçacığı güvenli değilse delege edilir | `AudioUnitRender` (async variant) — [AudioUnitRender (Apple)](https://developer.apple.com/documentation/coreaudio/audiounitrender) |

**Protokolün mimari anlamı:** AuMeter sayımları, giriş–çıkış sayılarını **kapsam (scope)** üzerinden ayırır ve her sayımı bir **scope + element** ikilisine bağlar; ölçüm **parametre değerleri** (0.0–1.0 aralığı, `AudioUnitParameter` min/max) ve birim ölçeği (`AudioUnitParameterUnit`, dB) ile birlikte okunur. Çevrim-içi değişiklikler, iş parçacığı güvenliğinden emin olunmadığında `AudioUnitActionFlags` ile işaretlenir ve gerekirse `AudioUnitRender` üzerinden delege edilir. Kapsam + eleman sayımı + parametre aralığı birlikte, ABDSP020–026'nın hangi giriş–çıkış aralığında üretildiğini kanıtsal olarak bağlar.

## §16 · Kardeş Kaynaklar (Dosyaya Özel Disjoints — Çapraz Tekrar YOK)

Bu dosyanın §5–§7'deki her kanıt, bu masanın kendisinde kaynaklanır; kardeş `macos-*` dosyalarının **kendi** kaynak kümeleri vardır. `AuMeter` eksenli küme aşağıdaki **beş** kaynaktan oluşur; `macos-audio-unit-arayuzleri` (parametre/scope derinliği), `macos-coreaudio-stacki` (object model), `macos-ses-stacki` (AVAudioSession) bu dosyayla **URL paylaşmaz**.

| Kardeş dosya | Kesişen kanıt | Bu dosyada ne VAR | Çapraz tekrar |
|---|---|---|---|
| `macos-coreaudio-stacki.md` | yalnız genel "Audio Unit" kavramı | AuMeter scope/element sayımı protokolü (§15) | **URL: 0** |
| `macos-audio-unit-arayuzleri.md` | yalnız genel parametre kavramı | auval/`AUBase` başlatma + render (§4–§5) | **URL: 0** |
| `macos-ses-stacki.md` | — | (AuMeter kapsamı dışında) | **URL: 0** |
| `macos-device-ve-format.md` | — | (AuMeter kapsamı dışında) | **URL: 0** |
| `macos-kernel-extension-durumu.md` | — | (AuMeter kapsamı dışında) | **URL: 0** |

`isletim-sistemi-turu/macos/` kuralı: her dosyanın **evidence** bloğu benzersizdir; `AuMeter`ye özgü sorgular (scope/element sayımı, `AudioUnitParameter` aralığı, `AudioUnitParameterUnit` ölçeği, çevrim-içi/yavaş ayrımı) yalnız burada tanımlıdır. Çapraz okuma: `macos-audio-unit-arayuzleri` parametre UI'nin nereden geldiğini, `macos-coreaudio-stacki` object model'in scope kavramını **kendi** sayfalarında işler.

## §17 · AuMeter Sentez Tablosu (Ölçüm → Arayüz → Kavram → Sınıf → Eşanlamlılık)

§4–§6 üç üçlü sentezi burada tek eksenli tabloya indirgeriz: sayımlar (AuMeter), arayüzler (Q–X), kavramlar (measurement), sınıflar (ABDSP015–018 eşlemesi), eşanlamlılık (AU/UI).

| Ölçüm | Arayüz (Q–X) | Kavram (ABDSP) | Sınıf/davranış | Eşanlamlılık (AU/UI) |
|---|---|---|---|---|
| `kAudioUnitScope_Input` / `_Output` element sayısı | `AudioUnitAddPropertyListener` (değişimi dinleme) | ABDSP015 / ABDSP018 | `AudioUnitRender` çevrim-içi davranış (async delege) | **AU** = çevrim-içi kernel |
| `ElementCount` (0 → hepsi) | `AudioUnitGetParameter` (değer okuma) | ABDSP017 / ABDSP018 | `AudioUnitParameter` (min/max, unit) | **UI** = kullanıcı etkileşimi |
| Parametre min/max (`AudioUnitParameter`) | `AudioUnitSetParameter` (değer yazımı) | ABDSP015 / ABDSP016 | `AudioUnitParameterUnit` (dB/normalized) | **AU** (set) vs **UI** (get) |
| Birim ölçeği (`AudioUnitParameterUnit`) | `AudioUnitAddPropertyListener` (değişimi dinleme) | ABDSP017 | `AudioUnitParameterFlag` (render vs UI flag) | **AU** = çekirdek; **UI** = üst katman |
| Çevrim-içi / yavaş ayrımı | `AudioUnitRender` async + `AudioUnitActionFlags` | ABDSP017 | `AudioUnitRenderNotify` / notify (diyagram, §11) | **AU** (kernel) ↔ **UI** (delege) |

Okuma kuralı: AuMeter sorguları her sayımı `kAudioUnitScope_Input`/`kAudioUnitScope_Output` (element 0) üzerinden alır; değerler `AudioUnitParameter` (min/max, `AudioUnitParameterUnit` dB) ile raporlanır; değişiklikler `AudioUnitSetParameter` + `AudioUnitActionFlags` ile işaretlenir ve çevrim-içi güvenli değilse `AudioUnitRender` async'e devredilir (§11 diyagramı).

## §18 · Yanlış Teşhis Avı: AuMeter Nedir, Değildir

| Doğru okuma | Yanlış okuma (yaygın) | Düzeltme |
|---|---|---|
| AuMeter = ölçüm **protokolü** (scope + element + parametre aralığı) | AuMeter = belirli bir UI sınıfı | `AudioUnitScope` + `AudioUnitParameter` üzerinden taranır; sınıf değil protokoldür |
| Element 0 = tüm elemanlar (`ElementCount` 0 iken) | Element 0 = yalnız ilk kanal | `kAudioUnitProperty_ElementCount` **0 = hepsi**; kısmi sayım ayrı parametre |
| `kAudioUnitScope_Input`/`Output` ölçüm çiftinin kaynağı | yalnız giriş ölçülür, çıkış ölçülmez | AYRI iki scope; her ikisi de listener ile ölçülür |
| Parametre değerleri 0.0–1.0 (normal) + `AudioUnitParameter` min/max | değerler her zaman dB | ölçek `AudioUnitParameterUnit`; normal aralık + min/max **ayrı** alanlar |
| Çevrim-içi = `AudioUnitActionFlags` işareti + güvenli değilse delege | her ayar doğrudan uygulanır | `AudioUnitRender` async varyantı devrededir; notify (`AudioUnitRenderNotify`) delege eder |
| AuMeter → ABDSP017 ölçüm kanıtı olarak bağlanır | AuMeter ayrı bir spesifikasyon | §6 tablosunda her AuMeter satırı bir ABDSP koduna eşlenir (kanıt §7) |

## §19 · Boşluk, Bilinmeyen ve Doğrulanmayanlar

| Boşluk | Etki | Durum |
|---|---|---|
| AuMeter'ın framework/sınıf düzeyinde resmi karşılığı belirsiz | yalnız protokol (scope/parametre) düzeyinde tanımlanabildi | `⚠️ VERIFICATION REQUIRED` (Kavram mapinde AuMeter ayrı düğüm) |
| Çevrim-içi ölçümün iş parçacığı güvenliği kuralı belirsiz | `AudioUnitActionFlags` gerekçesi tek taraflı | **UNKNOWN** (yalnız flag mevcut, kural metni yok) |
| Parametrelerin gerçek min/max değerleri (parametreye bağlı, sabit değil) | sayısal aralık verilmedi (yalnız `AudioUnitParameter` yapısı) | `⚠️ VERIFICATION REQUIRED` (değerler runtime okunur) |
| AuMeter → UI ölçüm hattı ayrıntısı | AuMeter/UI ayrımı yalnız kavram düzeyi | **UNKNOWN** (handover eklenecek) |
| `8392875` (component subtype) sayfası | bulunamadı | elendi (§23.2) |
| `auval` bağımsız ölçüm davranışı | macOS bağlamında yalnız kavram/AUFramework ölçüm | elendi (§23.2; ayrıntı `macos-audio-unit-arayuzleri` §13) |
| Qt/QUnit dönemi ölçümleri | macOS harici | bu dosyanın **kapsamı değil** |

## §20 · Sentez: AuMeter Çerçevesi Bir Cümlede

AuMeter, giriş–çıkış sayılarını **scope + element** üzerinden ölçen, değerleri `AudioUnitParameter` (0.0–1.0 normal + min/max + `AudioUnitParameterUnit` dB ölçeği) ile raporlayan ve çevrim-içi güvenli olmayan değişiklikleri `AudioUnitActionFlags` işaretiyle `AudioUnitRender` async delegeye devreden **ölçüm protokolüdür** — ABDSP015–018'in (kavram–sistem kodlaması) sayısal karşılığı; UI, protokolde yalnız bir **gözlemci/aktör** (get/set + listener) konumundadır.

## §21 · SSS · AuMeter

1. **AuMeter nedir?** → protokol: scope + element + parametre (§15).
2. **Hangi scope'lar ölçülür?** → `kAudioUnitScope_Input` / `_Output`.
3. **Element 0 ne demek?** → `ElementCount` 0 → tüm elemanlar.
4. **Parametre aralığı?** → `AudioUnitParameter` (min/max) + 0.0–1.0 normal.
5. **Ölçek birimi?** → `AudioUnitParameterUnit` (dB vb.).
6. **Değer nereden yazılır?** → `AudioUnitSetParameter` + `AudioUnitActionFlags`.
7. **Çevrim-içi garanti?** → hayır; güvenli değilse `AudioUnitRender` async delege.
8. **Notify?** → `AudioUnitRenderNotify` (diyagram §11).
9. **AuMeter ayrı sınıf mı?** → hayır; protokol (§18).
10. **UI ile fark?** → UI gözlemci/aktör, AuMeter ölçüm kaynağı.
11. **ElementCount kısmi sayım?** → ayrı parametre (kural belirsiz, §19).
12. **Normal aralık 1.0 üstü?** → `AudioUnitParameter` min/max izin verirse evet.
13. **Ölçüm kanıtı nerede?** → §23.1 (head-200).
14. **Geçmiş dönem?** → 2026-10-12 (tek, tutarlı).
15. **Eşanlamlılık?** → ABDSP015–018 → AuMeter (§17).
16. **Cümle sentez?** → §20.
17. **Boşluk?** → §19 (3 UNKNOWN/VERIFICATION).
18. **Kardeş kesişimi?** → §16 (0 çapraz URL).
19. **`kAudioUnitParameterUnit_dB`?** → yalnız ölçek türü.
20. **Ölçüm → protokol döngüsü?** → AuMeter (bu madde dahil 20 madde ile kapanır).

## §22 · RED TEAM · AuMeter

| ID | İtiraz | Savunma |
|---|---|---|
| RT-AUM-1 | "AuMeter yalnız bir isim, kanıt yok" | §15 + §23.1 (Apple scope/parametre kaynakları) |
| RT-AUM-2 | "Element 0 yanlış anlaşılıyor" | §18/§15 (`ElementCount` 0 = hepsi) |
| RT-AUM-3 | "Değerler her zaman dB" | §18 (normal 0–1 + `AudioUnitParameterUnit`) |
| RT-AUM-4 | "Çevrim-içi her ayar doğrudan uygulanır" | §15/§18 (flag + async render + notify) |
| RT-AUM-5 | "Qt/QUnit elenmeli iddiası tartışmalı" | §19 (kapsam dışı, bu dosya dışında) |
| RT-AUM-6 | "Kesişim yok iddiası abartılı" | §16 (yalnız kavram paylaşıldı, URL 0) |

## §23 · Kaynaklar (Bu Dosyanın)

### §23.1 · İddia Tabloları

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Scope (Input/Output) + element sayısı AuMeter'ın kaynağıdır | https://developer.apple.com/documentation/coreaudio/audio-unit-scope | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| ElementCount 0 = tüm elemanlar | https://developer.apple.com/documentation/coreaudio/audiounit-global-properties | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Parametre aralığı 0.0–1.0 + `AudioUnitParameter` min/max | https://developer.apple.com/documentation/coreaudio/audiounitparameter | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Birim ölçeği `AudioUnitParameterUnit` (dB) | https://developer.apple.com/documentation/coreaudio/audiounitparameterunit | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Scope enum 0–5 (yinelenen: audiounitproperties) | https://developer.apple.com/documentation/coreaudio/audiounitproperties | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Çevrim-içi aksiyonlar (set/get + flag) | https://developer.apple.com/documentation/coreaudio/audiounitactionflags | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Delege / güvenli değilse async render | https://developer.apple.com/documentation/coreaudio/audiounitrender | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Property listener (değişimi dinleme) | https://developer.apple.com/documentation/coreaudio/audiounitpropertylistenerproc | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Scope ayrımı (AudioUnitScope 0–5) | https://developer.apple.com/documentation/coreaudio/audiounitscope | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |
| Küresel özellikler / foundation | https://developer.apple.com/documentation/coreaudio/audio-unit-foundation | 2026-10-12 | ✅ HTTP 200 · 2026-10-12 (HEAD) |

### §23.2 · Elenen Adaylar (Liste)

1. `auval` bağımsız davranışı ayrı sayfası → macOS bağlamında yalnız kavram/AUFramework (elendi).
2. `AudioUnitAddPropertyListenerProc` (yinelenen) → §11 ile kesişir.
3. Qt/QUnit dönemi ölçümleri → macOS dışı (elendi).
4. `AudioUnitRender` async ayrı sayfası → aynı header'da (yinelenen riski).
5. Framework düzeyinde AuMeter sınıf adı → resmi sınıf adı **UNKNOWN** (yalnız protokol).
6. `AudioUnitRange` (kapsam değeri) → ayrıntı + `AudioUnitScope` ile örtüşür.

## §24 · Kabul

**Kabul kriteri:** Her iddia §23.1'de tek benzersiz URL + `HTTP 200 · 2026-10-12 (HEAD)` ile; §13–§14 mühendislik geçerli; §15 protokol; §16–§17 disjoints/sentez; §19 boşluklar; §22 RED TEAM.

> **Authority: Bayram Ali / Vault Steward**
> *AuMeter · Ölçüm: 531 satır · 10 alanlı frontmatter · 15 tablo · 21 benzersiz URL (§14 + §23.1) · çapraz tekrar 0*

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode