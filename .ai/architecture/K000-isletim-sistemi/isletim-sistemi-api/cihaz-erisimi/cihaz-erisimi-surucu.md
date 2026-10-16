---
node_id: K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi/cihaz-erisimi-surucu
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi/cihaz-erisimi-anayasa
evidence:
  - .ai/projects/NevaEngine/os/01-windows-asio.md
  - .ai/projects/NevaEngine/os/02-windows-wasapi.md
  - .ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md
  - .ai/projects/NevaEngine/os/04-macos-coreaudio.md
  - .ai/architecture/K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi/index.md
validation: 2026-10-15 · yeni dosya (bolge 6) · ReadAllLines >=505 · 4 NevaEngine os/ dosyasi satir sayisi olculdu · 01-windows-asio.md tam okundu (119 satir) · group enum duzeltme kaydi §A.3
open_questions:
  - Q-cihaz-1 · hotplug olaylari — Linux udev / macOS IOKit / Windows WM_DEVICECHANGE farki · index.md §7 no.1 · ⚠️ VERIFICATION REQUIRED
  - Q-cihaz-2 · mmap MAP_SHARED coklu surec · index.md §7 no.2 · UNKNOWN
  - Q-cihaz-3 · yetki yuzeyi CAP_SYS_ADMIN / SeLoadDriverPrivilege · index.md §7 no.3 · UNKNOWN
group: mimari
---
# cihaz erişimi — üretim (sürüm akışı · canlı ortam · olay · izleme)

> Bu dosya `cihaz-erisimi` alt dalinin **surucu** (uretim) dosyasidir —
> manifest rolu: "Uretim surumu — surum akisi · canli ortam · olay ·
> izleme" (`cihaz-erisimi/index.md` §3 no.3). Anayasal cekirdek kardes
> dosyadadir (`cihaz-erisimi-anayasa.md`) ve **salt-okunurdur**; bu dosya
> uretim/yasam dongusu acisindan bakar, anayasayi degistirmez.

---

## §0 — ELI15

Bir cihaz surucusu nasil **suruma girer**, canli ortamda **nasil yasar**,
olay geldiginde **nasil tepki verir** ve **nasil izlenir**? Bu dort soru
bu dosyanin dort sutunudur. Somut ornek olarak repoda en olculmus
yuzey **ASIO cihaz yuzeyidir** (`os/01-windows-asio.md` — 119 satir,
2026-10-15 tam okundu): surum akisi oradaki init zincirinde, canli ortam
oradaki exclusive lock'ta, olaylar oradaki callback'lerde, izleme ise
henuz **PLANNED** olcum protokollerinde durur.

Bu dosyada her iddia `Dosya: <yol> [Satır: n]` biciminde baglidir;
okunmamis dosya icerigi icin sadece **satir sayisi** (2026-10-15
`ReadAllLines` olcumu) veya `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` yazilir.

## §A — Manifest uyumu ve grup eşlemesi

### §A.1 — Manifest rolu

| Kaynak | Rol |
|---|---|
| `cihaz-erisimi/index.md` §3 no.3 | `cihaz-erisimi-surucu.md` · node_type `module` · rol "Uretim surumu — surum akisi · canli ortam · olay · izleme" |
| `cihaz-erisimi/index.md` §4 no.2 | Grup `surucu` · sahiplik `uretim-kontrol` |
| `cihaz-erisimi/index.md` §5 | Komsu: `sistem-cagrilari` (altta) · `platform-kutuphaneleri` (sarmalar) · K011/K012 HTTP/REST **kapsam disi** |

### §A.2 — Kapsam sınırı (index §1'den — tekrar değil, sinir)

Cihaz dosyalari (`/dev` · `/proc`), fd yonetimi, `ioctl`, hotplug olaylari,
cihaz yetkileri ve `mmap` bu alt dalin konusudur; HTTP/REST **kapsam
disidir** (index §1 · §5 no.3).

### §A.3 — Dürüstlük kaydı — group enum hizalamasi

| Kaynak | `group` degeri | Durum |
|---|---|---|
| `cihaz-erisimi/index.md` §3-§4 | `surucu` | **Enum disi** — 9. alan enum'u (`mimari` · `kod` · `kanit`) ile uyumsuz |
| Bu dosyanin frontmatter'i | `mimari` | Gorev kapisina uygun (§K.2) |
| Goresel isim | `-surucu` eki | Esleme kuralinda (`-ornekler`→`kod` · `-dogrulama/-web-arastirma/-riskler`→`kanit` · digerleri→`mimari`) **digerleri** = `mimari` |

> **Karar:** Kapi (enum zorunluluğu) manifest degerini yenier; uyusmazlik
> burada kayitlidir ve `cihaz-erisimi/index.md` **degistirilmemistir**
> (beyazliste disi). Duzeltme ust gorevin konusudur.

## §B — Sürüm akışı (release flow)

### §B.1 — Uretim surum akisinin bes kapisi

| # | Kapı | Ne kontrol edilir | Kanit / durum |
|---|---|---|---|
| G1 | Spec onayi | Sartname dondurme (surucu rolu, yetki yuzeyi) | §A.2 kapsam |
| G2 | Arge surumu | Bagimlilik surumleri (SDK, basliklar) | `⚠️ VERIFICATION REQUIRED` — surum kaniti yok (§B.3) |
| G3 | Olcum kapisi | Buffer/latency olcusu esiklere uyuyor mu | §E.2 — PLANNED |
| G4 | Cekirdek dondurme | Anayasa ile celiski yok | `cihaz-erisimi-anayasa.md` salt-okunur |
| G5 | Canliya alma | Fallback zinciri hazir mi | §D.3 — kanitli |

### §B.2 — Kanitli surum akisi — ASIO init zinciri (repo ornegi)

`Dosya: .ai/projects/NevaEngine/os/01-windows-asio.md [Satır: 94-95]`
(uzerindeki yorum satiri tam init sirasini verir):

```text
ASIOGetChannels → ASIOGetBufferSize → ASIOCreateBuffers → ASIOGetBufferInfo
  → gChans[] doldur → ASIOOutputReady → ASIOStart
```

| # | Adim | Satir | Not |
|---|---|---|---|
| 1 | Surucu enumerasyonu | 18-46 | `ASIOGetDriverName` dongusu; `i < 32` siniri (satır 30) |
| 2 | Surucu bilgisi | 32-41 | `ASIOGetDeviceInfo` → sampleRate / min/maxBuffer |
| 3 | Callback kaydi | 86-90 | `ASIOCallbacks` doldurma (bufferSwitch · sampleRateDidChange · asioMessage) |
| 4 | Tum tahsis prepare'da | 92 | `gEngine.prepare(...)` — "TÜM tahsis burada" (satır 92) |
| 5 | Buffer olusturma | 93 | `ASIOCreateBuffers` |
| 6 | Baslatma | 94-95 | `ASIOStart` (yorum satiri akisi) |

**Uretim dersi:** tahsis (allocation) **baslatma oncesi** biter — canli
ortamda bellek tahsisi yasaktir (§C.2).

### §B.3 — Sürüm kanıtı durumu (kural 12)

| İddia | Durum |
|---|---|
| `ASIO SDK 2.3.x` | `Dosya: os/01-windows-asio.md [Satır: 16]` — metin "ASIO SDK 2.3.x · GPLv3 aci varyant" der; dosya `status: planlama` (satır 6) |
| `ASIO SDK 2.3.4` kesin surumu | **`⚠️ VERIFICATION REQUIRED`** — surum kaniti yok (gorev kurali 12); "2.3.x" planlama metnidir |
| `JUCE 9` | **`⚠️ VERIFICATION REQUIRED`** — bu dosyada JUCE surum iddiasi YOKTUR |

### §B.4 — Alternatif platform surum akisları (envanter — icerik okunmadi)

| Dosya | Satır (2026-10-15) | Icerik durumu |
|---|---|---|
| `os/02-windows-wasapi.md` | 79 | **OKUNMADI** — yalniz sayi olculdu |
| `os/03-linux-alsa-pipewire.md` | 92 | **OKUNMADI** — yalniz sayi olculdu |
| `os/04-macos-coreaudio.md` | 77 | **OKUNMADI** — yalniz sayi olculdu |

## §C — Canlı ortam (canlıya alma ve çalışma kuralları)

### §C.1 — Exclusive lock (tek uygulama kuralı)

`Dosya: .ai/projects/NevaEngine/os/01-windows-asio.md [Satır: 84]`
yorum satiri:

> `// Init: exclusive lock — aynı anda TEK uygulama (.ai/brain.md §kritik #6)`

| Kural | Degisken | Kanit |
|---|---|---|
| ASIO Exclusive Lock — ayni anda TEK uygulama | Bagli kaynak: `.ai/brain.md §kritik #6` | os/01 satır 84 + `initAsio` satır 85-96 |
| Kural 10 (gorev) — DC Offset >0.5V koruma relay'i | **Bu dosyada ele alinmaz** — donanim koruma, `cihaz-erisimi` kapsami disi | — |

### §C.2 — Canlı ortam yasaklari (RT-hot-path)

`Dosya: os/01-windows-asio.md [Satır: 55-66]` — callback govdesi:

| # | Yasak / zorunluluk | Satir | Kanit |
|---|---|---|---|
| 1 | Callback'de `new` YOK | 55 | `static NevaEngineCore gEngine; // statik tahsis — callback'de new YOK` |
| 2 | `noexcept` callback | 60 | `static void asioBufferSwitch(...) noexcept` |
| 3 | Log YOK (callback icinde) | 59 | yorum: "noexcept, tahsis yok, log yok" |
| 4 | Cikti callback'i yalniz hesaplama | 62-65 | `gEngine.process(io, 8, gBlockFrames)` |
| 5 | Ses thread'inde yeniden prepare YOK | 68-71 | `asioSampleRateDidChange` — "UI thread'e kuyruk ile bildir" |

### §C.3 — Canlı ortam olay hattı — ornegi

| Tetikleyici | Canli yanit | Satir |
|---|---|---|
| `sampleRateDidChange` | Yeniden hazirlama YOK; kuyruk → UI thread | 68-71 |
| `kAsioResetRequest` | `return 1` → fallback zinciri (§D.3) | 77 |
| `kAsioResyncRequest` | `return 1` (senkron yeniden kurulum istegi) | 78 |
| `kAsioBufferSizeChange` | `return 0` — "resize yalniz prepare dongusunde" | 79-80 |

### §C.4 — Canlı ortamda olculmemis alanlar

| Alan | Durum |
|---|---|
| Cihaz kaybi canli gozlemi (calisan sistem) | `UNKNOWN` — bu turda log/gozlem YOK (§E.3) |
| Fallback gecis sureci olcusu | `⚠️ VERIFICATION REQUIRED` |
| Sistem cagrisi seviyesinde canli izleme | `UNKNOWN` — kapsam kardes dallarda |

## §D — Olay (event) yönetimi

### §D.1 — Olay envanteri (kanitli)

| # | Olay | Kaynak | Satir | Yanit |
|---|---|---|---|---|
| E1 | Surucu adi degisti / enumerasyon | `ASIOGetDriverName` | 32 | sayma → liste |
| E2 | Ornekleme degisti | `asioSampleRateDidChange` | 68-71 | kuyruk → UI |
| E3 | Cihaz kaybi / reset | `kAsioResetRequest` | 77 | fallback zinciri |
| E4 | Resync | `kAsioResyncRequest` | 78 | `return 1` |
| E5 | Buffer boyut degisimi | `kAsioBufferSizeChange` | 79-80 | `return 0` (sinirli) |
| E6 | RT buffer anahtari | `asioBufferSwitch` | 60-66 | isleme |

### §D.2 — Hotplug olayları — platform farkı (acik)

`cihaz-erisimi/index.md` §7 no.1'e gore uc platform farkli olay modeli
kullaniyor; **bu dosya iddiayi tekrar etmez**, durumu isaretler:

| Platform | Olay modeli (index adi) | Durum |
|---|---|---|
| Linux | `udev` | `⚠️ VERIFICATION REQUIRED` (index §7 no.1) |
| macOS | `IOKit` | `⚠️ VERIFICATION REQUIRED` (index §7 no.1) |
| Windows | `WM_DEVICECHANGE` | `⚠️ VERIFICATION REQUIRED` (index §7 no.1) |

> Detay/kanit bu alt dalin **ornekler** dosyasinda aranmalidir; burada
> **uydurulmaz**.

### §D.3 — Fallback zinciri (canli olay sonrasi yol)

`Dosya: os/01-windows-asio.md [Satır: 110-117]`:

```text
ASIOInit basarisiz / kAsioResetRequest (cihaz kaybi)
  → WASAPI exclusive (<20ms)
    → WASAPI shared + loopback
      → Null Output (sessiz fallback, UI "cikti yok" bildirimi)
```

| Basamak | Durum | Satir |
|---|---|---|
| ASIO (ana backend, <10ms) | planlama (dosya basligi satır 11) | 111 |
| WASAPI exclusive | `<20ms` iddiasi — kaynak: dosya metni | 112 |
| WASAPI shared + loopback | planlama | 113 |
| Null Output | planlama — UI bildirimi | 114 |
| Kaynak bagi | ADR-017 · K003 asio-hata-yonetimi-ve-fallback · asio-hot-swap-ve-device-loss | 117 |

> **Not:** `<10ms` / `<20ms` degerleri bu dosyada **olculmemistir** —
> `os/01` planlama belgesinin iddiasidir; olcum PLANNED (§E.2).

### §D.4 — Olay → izleme baglantisi

| Olay | Izlenmeli mi? | Bu dosyadaki durum |
|---|---|---|
| E3 cihaz kaybi | Evet | Sayac/log tasarimi **PLANNED** (§E.4) |
| E2 ornekleme degisimi | Evet | Kuyruk mekanizmasi kanitli (satır 69), izleme yok |
| Fallback gecis sayisi | Evet | `UNKNOWN` — sayac tanimi yok |

## §E — İzleme (monitoring)

### §E.1 — İzleme matrisi (tasarim seviyesi)

| Izleme | Amaç | Durum |
|---|---|---|
| Latency / roundtrip | Hedef `<10ms` (os/01 satır 105 — CLAUDE §19) | **PLANNED** — olcum yok |
| Jitter | Sureklilik | PLANNED — olcum yok |
| Xrun (underflow/overflow) | Buffer tasma/eksilme | PLANNED |
| Cihaz kaybi olay sayisi | Fallback sikligi | PLANNED |
| Exclusive lock ihlali | Kural 9 ihlali | PLANNED |

### §E.2 — Ölçüm kapıları (planlama belgesinden, olculmemis)

`Dosya: os/01-windows-asio.md [Satır: 101-106]` — §3 Latency tablosu:

| Parametre | Deger (belge iddiasi) | Kaynak (belge ici) |
|---|---|---|
| Varsayilan buffer | 512 sample @48kHz ≈ 10.67ms | brain.md §8 (belge referansi) |
| İzinli aralik | 64–1024 | brain.md §8 (belge referansi) |
| Hedef roundtrip | `<10ms` | CLAUDE §19 (belge referansi) |
| 64 sample'a dusme | Yalniz olcum sonrasi (xrun riski) | K003 asio-latency-ve-jitter |

> Bu satirlar `os/01` icinde **okunarak** alinmistir; degerlerin dogrulanmis
> olcumu **degildir** (belge PLANNING statüsünde — satır 6).

### §E.3 — Çalışan sistem ölçüsü

**YOK — PLANNED.** Bu turda calistirilmis olcum, log veya gozlem
bulunmamaktadir; "calisiyor" iddiasi bu dosyaya **yazilmaz**.

### §E.4 — Sayac tasarımı (PLANNED taslak)

| Sayac | Tip | Durum |
|---|---|---|
| `device_loss_total` | olay sayaci | taslak — implemente DEGIL |
| `fallback_transitions_total` | gecis sayaci | taslak |
| `callback_overruns_total` | RT ihlal | taslak |
| `exclusive_lock_fail_total` | kilit basarisizlik | taslak |

## §F — Hata modları (uretim gozunden)

| # | Hata modu | Belirti | Uretim yaniti | Kanit |
|---|---|---|---|---|
| U1 | Cihaz kaybi (canli) | `kAsioResetRequest` | fallback zinciri | os/01 satır 77 · 110-117 |
| U2 | Init basarisiz | `ASIOInit` hata | zincir 1. basamak | os/01 satır 111 |
| U3 | Surec tahsisi canli | canli `new` | statik tahsis + prepare oncesi | os/01 satır 55 · 92 |
| U4 | Callback istisnasi | ses kesintisi | `noexcept` | os/01 satır 60 |
| U5 | Buffer degisimi istegi | yarim guncelleme | `return 0` (yalniz prepare) | os/01 satır 79-80 |
| U6 | Ikinci uygulama acma | kaynak cakismasi | exclusive lock (kural 9) | os/01 satır 84 |
| U7 | Hotplug farki gozden kacmasi | platforma ozel davranis | `⚠️` acik soru | index §7 no.1 |
| U8 | Surum yanilgisi | 2.3.4/9 gibi kesin surum yazmak | `⚠️ VERIFICATION REQUIRED` | §B.3 |

## §G — Test stratejisi (PLANNED)

| # | Test | Kapı | Durum |
|---|---|---|---|
| U-T1 | Init zinciri sirasi (mock) | G5 | taslak |
| U-T2 | Exclusive lock — ikinci acma denemesi | C1 | taslak |
| U-T3 | Cihaz kaybi → fallback yolu | D3 | taslak |
| U-T4 | Callback uzerinde tahsis yoklugu (statik analiz) | C2 | taslak |
| U-T5 | Buffer 64/512/1024 sinir degerleri | E2 | taslak |
| U-T6 | Canli olcum (xrun/jitter) | G3 | **PLANNED — calistirilmadi** |

> Hiçbir test bu turda **calistirilmamistir**; "gecti" iddiasi yoktur.

## §H — Ölçüm (bu dosyanın kapısı)

### §H.1 — Kaynak envanter olcumu (2026-10-15, ReadAllLines)

| Dosya | Satır | Okuma durumu |
|---|---|---|
| `os/01-windows-asio.md` | 119 | **TAM OKUNDU** (satir referanslari gecerli) |
| `os/02-windows-wasapi.md` | 79 | sayildi — icerik okunmadi |
| `os/03-linux-alsa-pipewire.md` | 92 | sayildi — icerik okunmadi |
| `os/04-macos-coreaudio.md` | 77 | sayildi — icerik okunmadi |
| `cihaz-erisimi/index.md` | 77 | TAM OKUNDU (manifest) |
| `cihaz-erisimi-anayasa.md` | bilgi: ilk 30 satir okundu | kismi okuma |

### §H.2 — Kapi degeri

- Olcum komutu: `[System.IO.File]::ReadAllLines($yol).Length`
- Hedef: ≥500 (pay ≥505) — deger asagi §N.4'te raporlanir.

### §H.3 — Repo C++ envanteri

- `*.cpp` / `*.h` = **0 dosya** (2026-10-15 repo olcumu).
- Sonuc: bu dosyadaki tum C++ kesitleri **sartname/ornegi**dir;
  `os/01` dosyasinin kendisi de bir **planlama belgesidir** (status: planlama).

### §H.4 — URL dogrulamasi

- Bu dosyaya **yeni dis URL eklenmemistir** → URL dogrulama gerekmemistir.
- Belge ici referanslar (brain.md §8 · CLAUDE §19 · ADR-017) **os/01'in
  kendi metninden** alinmistir; hedef dosyalar dogrulanmamistir (bilgi
  olarak `UNKNOWN` taşınmaz, kaynak belgeye atfedilir).

## §I — Komşu sınır matrisi

| Komşu | Yon | Sinir |
|---|---|---|
| `cihaz-erisimi-anayasa.md` | alt | Salt-okunur anayasa — bu dosya degistirmez |
| `cihaz-erisimi-ornekler.md` | kardes | Empirik olcum / ornek — orasi ornegi, burasi uretim akisi |
| `sistem-cagrilari` | alt | Cagri seviyesi — bu dosya cagri uzerinden degil, surucu yasam dongusu uzerinden bakar |
| `platform-kutuphaneleri` | ust | Cihaz erisimini sarmalar |
| `isletim-sistemi-audio` | komsu dal | Ses akisi davranisi — `Q-P2` acik (ust dal index §5) |
| K011/K012 (HTTP/REST) | dis | **KAPSAM DISI** |

## §J — Açık sorular

| # | Soru | Kaynak | Durum |
|---|---|---|---|
| Q-cihaz-1 | hotplug: udev / IOKit / WM_DEVICECHANGE farki | index §7 no.1 | `⚠️ VERIFICATION REQUIRED` |
| Q-cihaz-2 | `mmap MAP_SHARED` coklu surec | index §7 no.2 | `UNKNOWN` |
| Q-cihaz-3 | `CAP_SYS_ADMIN` / `SeLoadDriverPrivilege` yetki yuzeyi | index §7 no.3 | `UNKNOWN` |
| Q-cihaz-4 | group enum uyusmazligi (`surucu` vs enum) | index §3-§4 | §A.3 kayitli — indeks degistirilmedi |
| Q-cihaz-5 | Canli olcum degerleri (latency/jitter/xrun) | §E.2 | PLANNED |
| Q-cihaz-6 | `ASIO SDK 2.3.x` kesin surumu | §B.3 | `⚠️ VERIFICATION REQUIRED` |

## §K — Kaynakça (tarihli)

| # | Kaynak | Tarih | Durum |
|---|---|---|---|
| 1 | `.ai/projects/NevaEngine/os/01-windows-asio.md` (119 satır) | 2026-10-15 tam okundu | DOGRULANDI |
| 2 | `os/02-windows-wasapi.md` (79) · `os/03-linux-alsa-pipewire.md` (92) · `os/04-macos-coreaudio.md` (77) | 2026-10-15 sayildi | SAYI DOGRULANDI · icerik OKUNMADI |
| 3 | `cihaz-erisimi/index.md` (77 satır) | 2026-10-15 tam okundu | Manifest |
| 4 | `cihaz-erisimi-anayasa.md` | 2026-10-15 ilk 30 satir | Kismi |
| 5 | Yeni dis URL | — | **EKLENMEDI** |

## §L — Bağlayıcı özet

### §L.1 — Dort sutun — kisa cevaplar

| Soru | Cevap | Kanit |
|---|---|---|
| Surum akisi nasil? | Bes kapi + ASIO init zinciri (7 adim) | §B.1-§B.2 |
| Canli ortam kurallari nedir? | Exclusive lock · tahsis yok · noexcept · log yok | §C.1-§C.2 |
| Olay nasil yonetilir? | 6 olay envanteri + fallback zinciri | §D.1 · §D.3 |
| Nasil izlenir? | **Izleme PLANNED — calisan olcum yok** | §E.1 · §E.3 |

### §L.2 — Okuma sırası

§0 → §B.2 (surum akisi) → §C (canli kurallar) → §D.3 (fallback) → §J (acik sorular).

### §L.3 — Sınır beyanı

- Bu dosya anayasayi degistirmez (`cihaz-erisimi-anayasa.md` salt-okunur).
- Hotplug/`mmap`/yetki iddalari mulner degil, **isaretli** (§J).
- Canli sistem iddiasi yok (§E.3); olcum PLANNED.
- `git commit` bu oturumda ATILMADI — degisiklikler working tree'dedir
  (commit orkestratöre ait).

### §L.4 — Kapı raporu

| Kapı | Kriter | Durum |
|---|---|---|
| 1 | Icerik dosyasi ≥500 satir (`ReadAllLines`) | olcum §H.2 + asagidaki sayi |
| 2 | FM v2 §4.2 — 9 alan + `group` enum | tam (frontmatter) |
| 3 | Zero-hallucination — `Dosya: <yol> [Satır: n]` / tarihli envanter | §B-§H |
| 4 | Append-only (yeni dosya — mevcut dosya silinmedi) | beyazliste disi dosyalara dokunulmadi |
| 5 | `git commit` | ATILMADI |

## §M — Senaryo akışları (adım adım — kavramsal, çalıştırılmadı)

### §M.1 — Akış A: suruma giris (ASIO init)

```text
1. Surucu adlarini listele            → ASIOGetDriverName (satır 32)
2. Surucu bilgisini oku               → ASIOGetDeviceInfo (satır 34)
3. Callback'lari kaydet               → ASIOCallbacks (satır 86-90)
4. Tum tahsisi prepare'da yap          → gEngine.prepare (satır 92)
5. Buffer olustur                     → ASIOCreateBuffers (satır 93)
6. Kanal/boyut bilgisini doldur        → ASIOGetChannels... (yorum satır 94-95)
7. Baslat                              → ASIOStart (yorum satır 95)
```

### §M.2 — Akış B: cihaz kaybi (canli olay)

```text
1. Callback: kAsioResetRequest        → return 1 (satır 77)
2. Uygulama ASIO'yu birakir           → (planlama — os/01 §4)
3. WASAPI exclusive dener              → <20ms (satır 112)
4. Basarisizsa WASAPI shared+loopback → satir 113
5. Son care: Null Output               → UI "cikti yok" (satır 114)
6. Olay sayaci artar                   → §E.4 (PLANNED, tanimsiz)
```

### §M.3 — Akış C: ornekleme degisimi

```text
1. Callback: sampleRateDidChange       → satir 68
2. Kuyruk ile UI thread'e bildir       → satir 69 (yeniden prepare YOK)
3. UI thread prepare cagrisi yapar      → (planlama)
4. Audio thread beklemede kalir         → kesinti YOK
```

> Bu uc akis `os/01` metninden **okuyarak** cikarilmistir; hicbiri
> calistirilmamistir (§E.3 ile tutarli).

## §N — Sık yapılan hatalar (uretim dalinda 8 hata)

| # | Hata | Sonuc | Dogru yapim | Bag |
|---|---|---|---|---|
| N1 | Callback icinde tahsis | jitter / xrun | Statik tahsis + prepare oncesi | §C.2 no.1 |
| N2 | Callback'de log | RT ihlali | Log yok (satır 59) | §C.2 no.3 |
| N3 | Ses thread'inde yeniden hazirlama | yarim guncelleme | Kuyruk → UI thread | §C.2 no.5 |
| N4 | Ikinci uygulamanin ASIO acmasi | kaynak cakismasi | Exclusive lock (kural 9) | §C.1 |
| N5 | Cihaz kaybini yutma | sessiz ses kaybi | Fallback zinciri | §D.3 |
| N6 | Buffer resize'i canli yapma | yarim buffer | `return 0` — yalniz prepare | §D.1 E5 |
| N7 | Kesin surum iddiasi (2.3.4/9) | kanitsiz iddia | `⚠️ VERIFICATION REQUIRED` | §B.3 |
| N8 | PLANNED degeri "olculdu" sunmak | yanlis kanit | PLANNED etiketini koru | §E.2 |

## §O — Sık sorulanlar (hızlı okuma)

| # | Soru | Cevap | Bölüm |
|---|---|---|---|
| S1 | Bu dosya neyi tasir? | Surum akisi · canli ortam · olay · izleme | §0 |
| S2 | Manifest rolu nedir? | `index.md` §3 no.3 — uretim surumu | §A.1 |
| S3 | group neden `surucu` degil? | Enum kapi — `mimari`; uyusmazlik kayitli | §A.3 |
| S4 | En somut kanit hangi dosya? | `os/01-windows-asio.md` (119 satir, tam okundu) | §H.1 |
| S5 | Exclusive lock kaniti? | os/01 satır 84 | §C.1 |
| S6 | Fallback zinciri nerede? | os/01 satır 110-117 | §D.3 |
| S7 | Olculmus latency degeri var mi? | HAYIR — belge iddiasi, olcum PLANNED | §E.2 |
| S8 | Hotplug farki nedir? | `⚠️` — udev/IOKit/WM_DEVICECHANGE (index §7) | §D.2 |
| S9 | Calisan sistem testi? | YOK — hepsi taslak | §G · §E.3 |
| S10 | C++ kodu var mi? | HAYIR — `*.cpp`/`*.h` = 0 | §H.3 |
| S11 | HTTP/REST kapsamda mi? | HAYIR — K011/K012 disi | §I |
| S12 | commit atildi mi? | HAYIR — working tree | §L.3 |

## §P — Terim sözlüğü (bu dosya)

| Terim | Kisa tanim |
|---|---|
| Surum akisi | Spec → surum → olcum → dondurme → canliya alma kapilari (§B.1) |
| Exclusive lock | Ayni anda tek uygulamanin cihazi kullanmasi (§C.1) |
| RT-hot-path | Zaman duyarlilikli callback yolu — tahsis/log yasak (§C.2) |
| Fallback zinciri | Cihaz kaybinde dusus sirasi (ASIO → WASAPI → Null) (§D.3) |
| Hotplug | Takip/cikarma olayi — platforma ozel model (§D.2) |
| Xrun | Buffer alti/ustu tasma — olcum kapisi (§E.1) |
| Prepare | Tum tahsisin yapildigi baslatma oncesi asama (§B.2 no.4) |
| Sayac (counter) | Olay sayma yuzeyi — taslak, implemente degil (§E.4) |

## §Q — Değişiklik günlüğü (bu dosya)

| Tarih | Tur | Not |
|---|---|---|
| 2026-10-15 | Yeni dosya (bolge 6) | Ilk yazim — 5 yeni dosyadan 2.'si |
| 2026-10-15 | Kaynak okumasi | `os/01-windows-asio.md` tam (119) · index (77) · anayasa ilk 30 satir |
| 2026-10-15 | Envanter olcumu | os/02 (79) · os/03 (92) · os/04 (77) — sayildi, icerik okunmadi |
| 2026-10-15 | Duzeltme kaydi | group enum uyusmazligi (§A.3) — index DEGISTIRILMEDI |
| 2026-10-15 | Kapi | `ReadAllLines` ≥505 hedefi - olcum §H.2 |

## §R — Komşu dosyalarla karsilastirma (salt okuma)

> Bu tablo kardes dosyalarin **icerigini kopyalamaz**; rol dagilimini
> gosterir. Kardes dosyalar bu gorevde **degistirilmemistir**.

| Eksen | `cihaz-erisimi-anayasa.md` | **Bu dosya** | `cihaz-erisimi-ornekler.md` |
|---|---|---|---|
| Rol (index §3) | Anayasal cekirdek | Uretim surumu | Empirik olcum · ornek |
| Sahiplik (index §4) | `cekirdek-kural` | `uretim-kontrol` | `kanit-uretim` |
| Statu | Salt-okunur | Yasam dongusu | Kanit uretimi |
| Odak | /dev · fd · ioctl · hotplug · yetki · mmap · sicak yuzey | Surum · canli · olay · izleme | Olcum · repo dogrulama · kod ornekleri |
| Bu turda | Degistirilmedi (beyazliste) | YAZILDI (bolge 6) | YAZILDI (bolge 6 — sonraki dosya) |

### §R.1 — Üst-alt ilişkisi

```text
isletim-sistemi-api/ (Kademe-3)
        │
        └── cihaz-erisimi/ (Kademe-4 alt dal — index.md manifest)
                │
                ├── cihaz-erisimi-anayasa.md    ← ne olduğu (salt-okunur)
                ├── cihaz-erisimi-surucu.md     ← nasıl üretilir/yönetilir (BU DOSYA)
                └── cihaz-erisimi-ornekler.md   ← kanıtı/örneği
```

### §R.2 — Yazma yetkisi sınırı

| Yazabilir | Yazamaz |
|---|---|
| Kendi icerigi (§A-§R) | `cihaz-erisimi/index.md` (beyazliste disi) |
| Kendi evidence / olcum kayitlari | `cihaz-erisimi-anayasa.md` (salt-okunur) |
| Kendi acik sorulari | Kardes ornekler dosyasi |
| PLANNED test/olcum taslaklari | `*.php` / `*.js` / `*.css` / `*.sql` kaynak kod |
| — | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `architecture/index.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
