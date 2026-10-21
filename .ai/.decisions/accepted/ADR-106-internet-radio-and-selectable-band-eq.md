---
title: "CoreMusic — ADR-106: Neva Player Internet Radio (ICY/Shoutcast + HLS Özel Tuner Motoru) + Kullanıcı-Ayarlanabilir Bant EQ (Maks 31 Band)"
type: adr
category: audio
date: 2026-10-20
updated: 2026-10-20
version: 1.1.1
status: accepted
authority: "SSOT: bu dosya"
governance: "Red Team · Human Mode · Truth Mode — 4 persona debate TAMAMLANDI (4/4 KOŞULLU-KABUL → 27 şart v1.1'e entegre edildi → KABUL)"
debate: "2026-10-20 — Tur 1: 4 persona (architect · embedded-engineer · security-engineer · qa-engineer) kendi raporu · Tur 2: çapraz eleştiri (grid çelişkisi, ring invariant, HLS SSRF bypass, backoff SSOT çelişkisi, sahte-PASS) · Tur 3: 4/4 KOŞULLU-KABUL → şartlar bu v1.1'e işlendi → KABUL"
related:
  - ".decisions/accepted/ADR-0105-neva-player-media-stack.md"
  - ".decisions/accepted/ADR-019-per-os-neva-player.md"
  - ".decisions/accepted/ADR-017-dsp-hardware-mode.md"
  - ".decisions/accepted/ADR-006-performance-targets.md"
  - ".decisions/accepted/ADR-074-radio-database-schema.md"
  - "architecture/neva-player/index.md"
  - "projects/NevaPlayer/research/radio-and-hires.md"
  - "projects/NevaPlayer/research/lib-matrix.md"
  - "projects/NevaPlayer/reverse/aimp-dump.md"
---

# CoreMusic — ADR-106: Neva Player Internet Radio + Kullanıcı-Ayarlanabilir Bant EQ

**Durum:** accepted (v1.1 — debate şartları işlenmiş; **frozen YOK**)
**Tarih:** 2026-10-20 (v1.0 taslak) → 2026-10-20 (v1.1 accepted)
**Karar Veren:** Vault Steward (kullanıcı talimatı: "internet radyosu desteklesin; desteklemiyorsa özel motor yaz ekle" + "max 31 band, kullanıcı band sayısını seçecek" + "başla hepsini yap eksiksiz")
**İlgili ADR'ler:** [[ADR-0105-neva-player-media-stack]] (medya stack'i — bu ADR onu **tamamlar ve iki maddesini genişletir**) · [[ADR-019-per-os-neva-player]] (IAudioBackend + fallback zinciri) · [[ADR-017-dsp-hardware-mode]] (hard-RT kısıtları) · [[ADR-006-performance-targets]] (latency kapısı)

---

## 1. Bağlam (Context)

ADR-0105 (accepted v1.1) Neva Player'ın yerel dosya (FLAC/DSD/hi-res) ve video yollarını kilitledi; ancak iki kullanıcı gereksinimini **kapsam dışı bıraktı**:

1. **İnternet radyosu zorunlu özellik** — kullanıcı açıkça "internet radyosu desteklesin; desteklemiyorsa özel radyo motoru yaz, ekle" dedi. Mevcut kapsam dosyası (`projects/NevaPlayer/01-kapsam-ve-kabul-kriterleri.md`) radyoyu hiç anmıyor; ADR-0105'in karar tablosunda ağ/tuner satırı yok. CoreMusic platformunun kendisi de radyo domainine sahiptir (`.decisions/accepted/ADR-074-radio-database-schema.md` — DB şeması mevcut) → ürün tarafı hazır, oynatıcı tarafı boş.
2. **EQ bant sayısı kullanıcı tarafından seçilecek, maksimum 31** — ADR-0105 §2.2 sabit 31-band parametrik EQ tanımlar. Kullanıcı; 10/15/20/31 gibi farklı bant sayılarının **seçilebilir** olmasını, üst sınırın 31 olmasını istedi.

RE dump'dan gelen emsaller: **AIMP 20-band EQ** kullanır (`EQPresetsLibrary.ini` Band0..19 kanıtı — `projects/NevaPlayer/reverse/aimp-dump.md` §3), **VLC 10-band EQ** (`libequalizer_plugin.dll`, bant frekans listesi dump ile doğrulanacak — ⚠️ VERIFICATION REQUIRED) + **AIMP radyo kataloğu** (`System\Catalogs-Radio.ini`) ve **AIMP HLS desteği** (`bass_hls` plugin) kanıtları radyonun AIMP'te ayrı bir katalog/eklenti katmanı olarak yaşandığını gösteriyor.

### 1.1 Mevcut Durum

**Vault katmanı (IMPLEMENTED doküman):**
- `architecture/neva-player/index.md` (v1.0.0) — ses/yönetim diyagramında **radyo satırı yok**; §6 açık maddelerinde "AIMP EQ band sayısı dump'da doğrulanmadı" → bu ADR'nin yeni dump'ı (20-band kanıtı) o boşluğu kapatır.
- `projects/NevaPlayer/research/radio-and-hires.md` (53.7 KB, 84 kaynak) — ICY/Shoutcast v1+v2, HLS/LL-HLS, DASH, Radio Browser API, libcurl ICY durum makinesi pseudokodu, reconnect/backoff, WASAPI Exclusive/ASIO implementasyon rehberleri, 31-band ISO frekans tablosu, RBJ katsayıları, gapless tekniği (bu ADR'nin §1.3 araştırma tablosunun kaynağı).
- `projects/NevaPlayer/research/lib-matrix.md` (28 kaynak) — libcurl 8.23.0 (2026-10-14), mbedTLS (Apache-2.0 OR GPL-2.0), FFmpeg 9.0.2 HLS demux, BASS KULLANMA kararı.
- `projects/NevaPlayer/reverse/aimp-dump.md` — AIMP'de `Catalogs-Radio.ini` (radyo katalog), `bass_hls` (HLS decode), 20-band EQ, tek export `AIMPPluginGetHeader` plugin sözleşmesi.
- `.decisions/accepted/ADR-074-radio-database-schema.md` — CoreMusic radio DB şeması (ürün tarafı).

**Kod katmanı — YOK → PLANNED:** `**/*.{cpp,h,hpp}` = 0 (ADR-019 §1.1-B ile aynı durum); tuner/radio/EQ-band-seçimi kodu henüz yazılmamıştır.

### 1.2 Sorun Tanımı

1. **Radyo protokolü seçimi yapılmamış:** ICY (Shoutcast v1), Shoutcast v2 XML metadata, HLS, DASH — hangisi birincil, hangisi aşamalı? Özel motor mu yazılacak, hazır kütüphane mi kullanılacak? Kullanıcı talimatı: gerekirse **özel motor**.
2. **Radyo nereye bağlanacak:** Radyo akışı decode sonrası **aynı DSP zincirinden** (EQ/LUFS/limiter) mi geçer, yoksa bit-perfect yolu bozmamak için bypass mı edilir? (Radyo zaten lossy → bit-perfect iddiası taşımaz; kapı ayrımı yazılmalı.)
3. **Reconnect/gapless semantiği:** Ağ kopması, sunucu timeout, stream değişimi durumunda durum makinesi (ADR-004 ile aynı STOPPED/PLAYING/PAUSED/LOADING/ERROR semantiği) tanımsız.
4. **EQ band sayısı sözleşmesi:** ADR-0105 §2.2 "31-band sabit" der; kullanıcı seçilebilirlik istedi → bant grid'i, izin verilen sayılar, biquad bütçesi (62/124 tablosu) yeniden ifade edilmeli; ADR-0105 ile **çelişmeden** genişletilmeli.
5. **Güvenlik:** Radyo URL'leri kullanıcı/stream kaynaklı güvensiz girdidir (playlist/M3U redirect, SSRF riski, yanıltıcı ICY header'ları) → OWASP uyumlu kısıt gerekli.
6. **Band geçişinde RT güvenliği:** Bant sayısı/preset değişimi callback'te olmaz (ADR-0105 §2.3 ramp smoothing zaten var) — bant *sayısı* değişimi (biquad zinciri boyutu değişir) için ek kural gerekli.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Kaynak: `projects/NevaPlayer/research/radio-and-hires.md` (84 kaynak, güven seviyeli) + `research/lib-matrix.md` (28 kaynak). Ayrıntı bu iki dosyadadır; bu tablo ADR şablonu §1.3 için 9 alanlık özetidir.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "ICY Shoutcast protocol icy-metaint StreamTitle metadata format" · (2) "HLS HTTP Live Streaming audio radio FFmpeg demux low-latency" · (3) "WASAPI exclusive mode bit-perfect 32-bit 192kHz implementation" · (4) "RBJ audio EQ cookbook biquad 31-band ISO 1/3 octave graphic equalizer" · (5) "internet radio C++ libcurl reconnect backoff gapless streaming" |
| Web Search **Konusu** | ICY/Shoutcast v1+v2 metadata protokolü · HLS/LL-HLS + DASH yayını · Radio Browser API + SomaFM/BBC stream kaynakları · WASAPI Exclusive (AUDCLNT_STREAMFLAGS_EXCLUSIVE, format negosyasyonu, event-driven gapless) + ASIO (buffer/latency, registry enumeration) · 31-band 1/3-oktav ISO frekans grid'i + RBJ biquad katsayıları (A=10^(dB/40)) + true-peak (BS.1770-4) · gapless (Xing/LAME 12-bit padding, FLAC SEEKTABLE) |
| Web Search **Bağlam** | 84 adlandırılmış kaynak (radio-and-hires §7) + 28 kaynak (lib-matrix §5); resmi spec (mcaster1 icy2-spec, SHOUTcast XML Metadata Spec), FFmpeg dokümanları, learn.microsoft.com WASAPI, Radio Browser API, SomaFM, forum/ölçüm yazıları |
| Web Search **Kısa Açıklama** | ICY = HTTP GET + `icy-*` header'ları + `icy-metaint` ile metin bloğu enjeksiyonu; Shoutcast v2 `icy-metadata-version: 2` + XML metadata. HLS'de FFmpeg demux eder (LL-HLS'in demux değil mux olduğu ayırt edildi). WASAPI Exclusive bit-perfect için format cihazla birebir eşleşmeli (32f→32i→24-in-32 negosyasyon sırası); cihazlar float reddedebilir. 31-band grid ISO 1/3 oktav (20 Hz–20 kHz). |
| Web Search **Uzun Açıklama** | ICY parser'ı elle yazılabilir (durum makinesi: header → icy-metaint → N byte veri → metin bloğu → tekrar); hazır kütüphane şart değil. HLS için FFmpeg'in `hls` demuxer'ı olgundur; LL-HLS edge'leri (PART/RENDITION-REPORT) demux katmanında opsiyoneldir. DASH radyoda daha az yaygındır (BBC HLS/DASH'a geçti, ICY'yi bıraktı — kaynak). WASAPI Exclusive'de `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` kurtarma yolu ve periyot ayarı yazıldı; exclusive↔shared otomatik geçiş durum makinesi tarif edildi. RBJ biquad IIR olduğu için gecikme ≈ 0 (grup gecikmesi hariç), CPU maliyeti 192 kHz'te ~59.5 M MAC/s (31×2 biquad, kaynak hesabı) → tek çekirdek ≤%5 benchmark kapısı (ADR-0105 §5.6) ile uyumlu. |
| Web Search **Paragraf Veri Uzun** | Radio mimarisi: tuner → curl/demux → decode → DSP → SPSC ring → IAudioBackend (ADR-019). libcurl 8.23.0 + mbedTLS (Apache-2.0) + özel ICY parser = açık kaynak, ticari serbest. Reconnect: üstel backoff (1s→30s cap, ±%20 jitter — research §1.6; **v1.0'daki "0.5s→8s" bu v1.1'de düzeltildi**), 3 başarısız sonra durum=ERROR. Codec matrisi: AAC-LC/HE-AAC v1-v2 (44.1/48k) + Opus (48k) radyoda yaygın. Gapless yerel dosya içindir; radyoda sürekli akış + reconnect sonrası konum koruma. AIMP emsal: `Catalogs-Radio.ini` (katalog ayrı katmanda), `bass_hls`. VLC emsal: `libequalizer` ayrı plugin, `libaccess_wasapi`, http/https/srt access. |
| Web Search **Sonucu** | (1) Radyo protokolü: **ICY birincil (özel parser)** + **HLS ikincil (FFmpeg demux)** + DASH Aşama 2 — destek ≥2 kaynak. (2) Bit-perfect kapı ayrımı: yerel dosya = ADR-0105 §5.1 null-test; radyo = **kapsam dışı** (lossy ağ akışı) — yazıldı. (3) WASAPI/ASIO rehberi ADR-0105/019 ile uyumlu (kaynak: radio-and-hires §3-§4). (4) 31-band grid + RBJ destekli; bant sayısı seçimi bu ADR ile yeni. (5) Güvenlik: URL allow/deny + redirect limiti + TLS (mbedTLS) — OWASP. ⚠️ VERIFICATION REQUIRED: icy metadata üst sınırı, LL-HLS demux detayları, "Opus 48k" resmi API, ASIO sample-type enum header'ı, MP4 gapless tag spesifikasyonu (radio-and-hires §7'de 6 madde işaretli). |
| Web Search **Alınan Karar** | **Özel Radio Tuner modülü** yazılır (libcurl tabanlı ICY/Shoutcast v1+v2 parser + M3U/M3U8 playlist çözümleyici + HLS için FFmpeg gateway demuxer'ı; BASS'a bağımlılık YOK — ADR-0105/lisans kararı korunur). Tuner → decode → soxr → SPSC ring → **RT render callback (EQ + limiter)** → IAudioBackend (ADR-019). EQ: sabit **31-band ISO 1/3-oktav grid**; kullanıcı grid'ten **bant sayısı seçer (izinli: 10 / 15 / 20 / 31)**, kalan bantlar bypass; biquad bütçesi = maks 62 (cascade 124) **sabit allocate**, bant değişimi bypass maskesi + ramp ile. |
| Web Search **Sonuç** | Karar 112 kaynaklı (84+28) araştırma ve RE dump emsalleriyle (AIMP 20-band + `Catalogs-Radio.ini` + `bass_hls`; VLC 10-band `libequalizer` + http/https access) desteklendi; ADR-0105/019/017/006 ile çelişki yok, iki madde genişletiliyor (radyo satırı + seçilebilir bant). 6 madde ⚠️ VERIFICATION REQUIRED olarak imzalandı — kodda doğrulanacak. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-0105 (Medya Stack) | Decode/DSP/thread topolojisi/zero-copy/C ABI bağlayıcı; bu ADR yalnızca **radyo kaynağı** ve **EQ bant sayısı** ekler, ADR-0105'le çelişen satır içermez (çelişki olsaydı yeni ADR ile supersede edilirdi — değil) |
| ADR-019 (IAudioBackend) | Tuner/radyo asla doğrudan OS/çıkış API'sine dokunmaz; ses yalnız `IAudioBackend` üzerinden akar |
| ADR-017 (Hard-RT) | Callback içinde ağ I/O, decode, JSON/XML parse, tahsis **yasak**; tuner tamamen non-RT producer thread |
| ADR-006 (Latency) | Radyo DSP dahil `<10ms ASIO / <20ms WASAPI` bütçesi aşılmaz; band sayısı değişimi bu bütçeyi callback'te harcamaz |
| Lisans (ADR-0105 §5, lib-matrix) | BASS KULLANMA (HLS çözümü `bass_hls` **kopyalanmaz**); FFmpeg LGPL build (`--enable-gpl` yasak); libcurl/mbedTLS Apache-2.0/MIT-tarzı |
| Güvenlik (OWASP Top 10:2025) | Radyo URL'leri güvensiz girdi: SSRF/redirect kısıtı, zararlı playlist koruması, credential log'lanmaz (REDACTED), metadata sanitizasyonu (XSS) |
| Scope (`projects/NevaPlayer/01-kapsam`) | "Streaming server" kapsam dışı kalır (sunucu değil **istemci** tuner); kapsam dosyasına radyo satırı bu ADR ile eklenir |
| Zero-Hallucination | 6 ⚠️ işaretli iddia kod/test ile doğrulanana kadar kesin sayılmaz; VLC EQ frekansları VLC dump bitiminde doldurulacak; 15/20-band alt küme frekansları §2.2b kural 3'te ⚠️ |

---

## 2. Karar (Decision)

**Neva Player'a (A) Özel Radio Tuner modülü — libcurl + ICY/Shoutcast v1+v2 özel parser + M3U/M3U8 çözümleyici + HLS için FFmpeg demux gateway — ve (B) sabit 31-band ISO 1/3-oktav grid üzerinde kullanıcı-seçimli bant sayısı (izinli: 10/15/20/31, maks 31) eklenir. Radyo akışı decode + soxr sonrası aynı SPSC ring'e girer ve EQ/limiter RT render callback'inde uygulanır; bit-perfect null-test kapısı (ADR-0105 §5.1) yalnız yerel dosyalara uygulanır.**

### 2.1 Neden Bu Seçenek?

- **Kullanıcı talimatı doğrudan:** "internet radyosu desteklesin, desteklemiyorsa özel radyo motoru yaz ekle" + "max 31 band, kullanıcı band sayısını seçecek".
- **Özel parser ucuz ve lisans-temiz:** ICY protokolü bir HTTP header + periyodik metin bloğu (~200 satır durum makinesi); hazır kütüphane ek bağımlılık, BASS ise yasaklı (ADR-0105). libcurl (8.23.0, MIT-tarzı) yalnız transport — TLS mbedTLS (Apache-2.0).
- **HLS elle yazılmaz:** FFmpeg'in `hls` demuxer'ı olgundur; ADR-0105 zaten FFmpeg'i "opsiyonel gateway" olarak konumlandırmış — radyo HLS'i bu gateway'in doğal müşterisidir.
- **Radyo DSP'den geçer:** AIMP `Catalogs-Radio.ini` katalog ayrı katman ama ses yolu aynı DSP'den geçer (EQ kullanıcı radyoya da EQ ister); lossy akışta bit-perfect iddiası **yoktur** → kapı ayrımı yazmak sahtekârlığı önler (null-test radyoda çalıştırılmaz — §5.1 Ş4'te CI guard'ı ile **kanıtlanır**).
- **Grid sabit, bant sayısı seçilebilir:** Frekanslar her zaman 31-band ISO 1/3-oktav grid'inden seçilir → preset taşınabilir, kasa (UI) tek grafik; izinli sayılar (10=VLC/Winamp geleneği ISO alt kümesi, 20=AIMP emsali, 15/31 ara/ tam ölçek) emsallere dayanır. Biquad bütçesi **en yüksek bant için sabit allocate** (62/124); daha düşük bantlar bypass maskesi ile çalışır — benchmark yine 31-band'de ölçülür (en kötü durum).
- **Bant sayısı değişimi non-RT + tahsissiz:** Zincir boyutu değişmez (sabit allocate + per-band bypass maskesi) → callback'te tahsis yok (ADR-017); maske geçişi 5–20 ms ramp ile (click yasağı, ADR-0105 §2.3). v1.0'daki "atomik zincir takası + deferred free" önerisi debate'de **maske yaklaşımıyla değiştirildi** (yaşam-süresi/kargaşa riski kalktı — §5.3).

### 2.2 Teknik Detaylar

**a) Radio Tuner modülü (yeni — `neva::radio`):**

```
[TunerThread (non-RT producer — ADR-0105 §2.5)]           [RT render callback]
  URL → URL Validator (tek kapı §2.2a kural 6)               ring → biquad EQ zinciri
    → libcurl HTTP GET (redirect ≤5, timeout 10s,              (aktif bant maskesi + ramp)
       TLS mbedTLS, HTTP09Allowed=0)                         → true-peak limiter
      ├─ icy-* header / SHOUTcast v2 XML                      → IAudioBackend cihazına yaz
      ├─ HLS (m3u8): FFmpeg gateway demux (protocol           (master clock = CİHAZ)
      │    whitelist http/https + aynı URL Validator)        [/RT]
      └─ ICY: icy-metaint × N byte → text blok
  → decoder (native aac/flac/mp3/opus — §2.2a kural 9)
  → soxr (producer-side, cihaz formatına — ADR-0105 §2.5)
  → SPSC ring (PCM)
  [non-RT yan thread] R128 loudness sidechain (ring'den okur)
  [non-RT] metadata sanitizasyon → lock-free kuyruk → C ABI event
  Reconnect: backoff 1s→30s cap, ±%20 jitter; 3 başarısız → state=ERROR
  Ring STARVE → fade-to-silence (≤50ms ramp); RING OVERFLOW → drop (akış sürekli)
  Reconnect kutusu: prebuffer 2-4s (LOADING) → decoder reset → sessizlik → fade-in 20ms
```

> **Debate düzeltmesi (v1.0 → v1.1):** v1.0 şeması DSP'yi producer'a koymuştu (EQ ring'den önce) — architect "ring invariant" ve embedded "İ1" itirazı ADR-0105 §2.5'e (biquad/limiter RT callback'de, R128 non-RT) dayanarak düzeltti. Bu şema bağlayıcıdır.

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | **Protokol sırası** | Birincil: ICY/Shoutcast v1+v2 (özel parser) · İkincil: HLS (FFmpeg gateway) · Aşama 2: DASH |
| 2 | **Metadata olayları** | `StreamTitle/StreamUrl` (v1) ve XML (v2) → **sanitizasyon sonrası** (kural 7) lock-free kuyruk → non-RT thread çağırır → C ABI `neva_session_*` metadata event'i (ADR-0105 §2.6 sürüm sözleşmesi içinde — **minor ek**, ABI kırmaz) |
| 3 | **Katalog** | Yerel kütüphane (SQLite, ADR-074 şemasıyla uyumlu) + Radio Browser API (açık kaynak dizin, **yalnız https mirror + `url_resolved` validator**) opsiyonel keşif; AIMP `Catalogs-Radio.ini` emsali |
| 4 | **DSP/EQ radyoda** | Zorunlu aynı zincir (kullanıcı EQ radyoya da uygulanır) — ama topoloji §2.2a: EQ/limiter **RT callback'de** · **bit-perfect null-test radyoda ÇALIŞTIRILMAZ** (lossy ağ akışı) — kapı yalnız yerel dosya; CI'da inverted guard (§5.1 Ş4) |
| 5 | **Resample** | Radyo 44.1k → cihaz 48k mismatch tipik → soxr producer-side (ADR-0105 §2.5); master clock = cihaz; hız farkı ring sayacı ile dengelenir (overflow→drop / starve→fade) |
| 6 | **URL Validator (tek kapı)** | Tüm URL'ler (tuner + FFmpeg gateway) **tek** validator'dan geçer: her redirect hop'ta yeniden + **post-connect gerçek IP** kontrolü (DNS rebinding); **HTTPS zorunlu, http yalnız kullanıcı açık onayıyla** (domain başına); private/loopback/link-local aralıklar default RED (SSRF); `userinfo` (user:pass) log'a/URL'ye yazılmaz → `[REDACTED]`; curl `HTTP09Allowed=0`; redirect zinciri ≤5; playlist boyutu ≤1 MB |
| 7 | **Metadata sanitizasyonu (XSS)** | UTF-8 doğrula (hatalı → drop), kontrol karakterleri strip, **256 bayt cap** (aşarsa kırp), render'da HTML-escape (native ve web UI); `StreamUrl` **hyperlink olarak render edilmez** (yalnız düz metin) |
| 8 | **Parser limit tablosu** | M3U8 entry ≤1000 · playlist derinlik ≤1 (EXT-X ile iç içe yasak) · segment ≤50 MB · XML'de DTD/external entity **kapalı** (billion-laughs) · `icy-metaint` 32 B–1 MiB aralığı dışında → stream RED · ICY text blok ≤1 MB |
| 9 | **Decode (radyo)** | **FFmpeg native `aac` decoder** (LGPL, ADR-0105 gateway kapsamı) + flac/mp3/opus (radyo FLAC nadir ama desteklenir) · **FDK-AAC opsiyonel ve ⚠️ VERIFICATION REQUIRED** (lisans teyidi lib-matrix'te yok → R7 SPDX doğrulamasına eklendi; teyide kadar kullanılmaz — architect S4 kararı) |
| 10 | **Durum makinesi** | STOPPED/PLAYING/PAUSED/LOADING/ERROR — ADR-004/018 semantiğiyle aynı (native + web footer player uyumu); reconnect sırasında LOADING, 3 başarısız → ERROR |
| 11 | **A/V sync** | Video + radyo eşzamanlı senaryoda **master clock = cihaz** (embedded şartı — v1.0'daki "radyo stream'i master" ifadesi düzeltildi); video A/V sync ADR-0105 §2.1'e göre audio-üstüne hizalanır |
| 12 | **Güvenlik review kapısı** | Merge öncesi security-engineer review: SSRF-bypass (redirect + DNS rebinding + HLS yolu) / metadata XSS / playlist-bomb testleri zorunlu (§5.1 Şart 9) |

**b) EQ bant sözleşmesi (ADR-0105 §2.2'yi genişleten — bağlayıcı):**

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | **Grid sabit** | 31-band ISO 1/3-oktav: 20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160, 200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600, 2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000 (Hz) |
| 2 | **İzinli bant sayıları** | **10 · 15 · 20 · 31** (seçim UI'da; preset'ler grid'e göre saklanır — 10-band preset'i 31-band kasa içinde geri yüklenebilir, eksik bantlar default flat) |
| 3 | **Grid alt küme mantığı** | **10-band = ISO 1/3-oktav alt kümesi:** 31.5, **63**, 125, 250, 500, 1000, 2000, 4000, 8000, 16000 Hz — v1.0'daki "62.5" **grid'de olmadığı için düzeltildi** (Winamp/VLC geleneğindeki 62.5 Hz yerine ISO 63 Hz kullanılır; fark ~14 sent, duyulamaz — **VLC dump teyidi v1.1.1**: VLC 3.0.23 equalizer 62.5/`63`=0 isabet kullanır, Neva ISO 63'te kalır; VLC preset'leri kendi frekans setiyle okunur, zorla grid'e map edilmez) · **20-band = AIMP emsal aralığı (Band0..19) — gerçek frekans listesi AIMP dump'da kanıtlanmadı → ⚠️ VERIFICATION REQUIRED** (kodda `eq_grids` tanım dosyasında sabitlenir, dump/web kaynağı ile teyit edilir) · **15-band = eşit log-aralık alt küme → ⚠️ VERIFICATION REQUIRED** (2/3-oktav standart listesi web kaynağı ile teyit edilmeden kesin sayılmaz) |
| 4 | **Biquad bütçesi** | **Sabit allocate: 31 band × 2 kanal = 62 biquad (cascade modunda 124)** — baştan tahsis (ADR-0105 §2.3 state bütçesi); aktif bant yoksa o biquad **bypass maskesiyle** pasif (yeni zincir tahsisi/yok edilmesi YOK). 10-band → 20 biquad aktif, 42 bypass. Benchmark kapısı **her zaman 31-band (en kötü durum)** ölçer — ADR-0105 §5.6 değişmez |
| 5 | **Band sayısı değişimi** | RT thread **dışı** maske hesabı → atomik maske yayını (acquire/release) → callback **maskeyi ramp ile** uygular (5–20 ms, click yasağı ADR-0105 §2.3; tahsis yok, zincir takası yok — debate uzlaşması). Cascade (1↔2 biquad/bant) mod değişimi = nadir durum → fade-out/re-init/fade-in (maske değil, yapılandırma değişimi) |
| 6 | **Preset biçimi** | Preset = `{grid_version, band_count, per-band gain dB, preamp}` — band sayısı preset'in parçası; preset yüklenirken band_count değişirse kural 5 işletilir |
| 7 | **Parametrik serbestlik** | Kullanıcı tek tek band frekansı ayarlayamaz (grid sabit); **parametrik mod** ayrıdır (ADR-0105 §2.1 31-band parametrik korunur) — grafik EQ = grid, parametrik = RBJ serbest mod |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **BASS ile radyo (`bass_hls`/BASS_StreamCreateURL)** — AIMP yolu | En az kod, HLS+ICY hazır | Kapalı kaynak + ticari €950–3.450 (lib-matrix KULLANMA) | ADR-0105 yasağı + kullanıcı "açık kaynak kullan" talimatı |
| 2 | **Hazır radyo kütüphanesi (libshout, libvx-tuna v.b.)** | Az kod | Ek bağımlılık, bakımı ölü/şüpheli, ICY v2 metadata kapsamı doğrulanmadı | Özel parser ~200 satır + libcurl mevcut; YAGNI |
| 3 | **Radyoyu DSP bypass et (bit-perfect yolu temiz kalsın)** | Null-test iddiası radyoya da yayılır | Kullanıcı radyoda EQ ister; lossy akışta "bit-perfect" sahte iddia olur | Sahte-PASS riski (ADR-0105 §5.1 canary ruhu); kapı ayrımı daha dürüst |
| 4 | **Bant sayısı serbest (1–31 arası her değer)** | Tam esneklik | Grid dışı frekanslar preset taşınabilirliğini bozar, UI/kasa testi patlar, biquad bütçesi öngörülemez | Sabit grid + 4 izinli sayı emsallerle (VLC 10 / AIMP 20) ve test edilebilirlikle uyumlu |
| 5 | **HLS'i de elle yaz (FFmpeg'siz)** | FFmpeg bağımlılığı tamamen kalkar | LL-HLS/segment/encryption elle = aylarca iş | FFmpeg zaten opsiyonel gateway (ADR-0105) — HLS doğal müşterisi |
| 6 | **Band değişimi için atomik zincir takası + deferred free** (v1.0 önerisi) | Maske kadar esnek | Bellek-sıra + geç serbestleştirme + cascade transient kavgası (architect S3, embedded İ2) | **Debate'de reddedildi:** sabit allocate + bypass maskesi tahsissiz ve RT-güvenli (§2.2b kural 4-5) |
| 7 | **FDK-AAC ile radyo AAC decode** | Muhtemel daha iyi AAC kalitesi | Lisans teyidi lib-matrix'te yok (R7 boşluğu) | ⚠️ VERIFICATION REQUIRED çözülene kadar FFmpeg native aac (kural 9) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Radyo zorunlu özelliği kapsama girer; ICY özel parser lisans-temiz ve bağımlılıksız (libcurl + mbedTLS Apache-2.0/MIT-tarzı).
- EQ seçilebilirliği kullanıcı isteğini karşılar; sabit grid preset taşınabilirliği ve test edilebilirliği korur (10-band preset → 31-band geri yükleme).
- ADR-0105/019/017 ile çelişki yok; biquad bütçesi ve benchmark kapısı aynen korunur (31-band en kötü durum).
- CoreMusic radio DB (ADR-074) ile ürün seviyesinde bütünleşir (katalog/favori/son dinlenen).
- Bit-perfect kapı ayrımı (yerel dosya test edilir, radyo edilmez) sahte-PASS riskini sıfırlar; CI inverted guard ile kanıtlanır.
- Bant değişimi tahsissiz (maske+ramp) → RT güvenliği debate ile pekişti.

### 4.2 Olumsuz Sonuçlar

- Yeni non-RT thread (TunerThread) + URL Validator + sanitizasyon katmanı → durum makinesi karmaşıklığı artar.
- İzinli 4 bant sayısı = UI + preset test matrisi ×4 (31-band testleri en kötü durumu kapsar, diğerleri alt kümelerdir; QA Ş6: 4×4 round-trip).
- HLS radyo FFmpeg gateway'e bağımlı → FFmpeg sürümü HLS demux golden vektörleriyle kilitlenir (QA Ş5: CI'da skip yasak).
- DASH radyoda Aşama 2'ye ertelendi (kapsam darlığı — BBC emsalinde HLS/DASH var).
- HTTPS zorunluluuğu → yalnız http yayın yapan eski istasyonlar açık onay gerektirir (kullanıcı tarafında tek tıklama).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| ICY v2 XML metadata şema varyantları (sunucuya göre) | 3 (olası) | 2 (düşük) | Parser tolerant (bilinmeyen alanlar yutulur); ⚠️ 6 madde kodda doğrulanır |
| SSRF / zararlı playlist (redirect → iç ağ) — **HLS FFmpeg bypass dahil** | 3 (olası) | 4 (yüksek) | **Tek URL Validator** (her redirect hop + post-connect IP; tuner + FFmpeg protocol whitelist) + private-range default RED + parser limit tablosu (§2.2a kural 6/8/12); security review kapısı |
| Metadata XSS (StreamTitle → UI render) | 3 (olası) | 4 (yüksek) | Sanitizasyon + HTML-escape + 256 bayt cap + StreamUrl hyperlink değil (kural 7) |
| Band sayısı değişimi sırasında xrun/click | 2 (mümkün) | 3 (orta) | Maske + 5–20 ms ramp + ADR-0105 §5.3 click testi "band-count-change" senaryosuyla (§5.1 Ş3) |
| Radyo + 4K eşzamanlı DPC/xrun (ADR-0105 risk 4 ile aynı aile) | 3 (olası) | 3 (orta) | Aynı nightly stres kapısı (ADR-0105 §5.7) radyo senaryosuyla genişletilir |
| FFmpeg HLS demux davranışı sürüm değişimi | 2 (mümkün) | 3 (orta) | FFmpeg 9.0.2 pin + HLS golden vektör testleri (merge gate, skip yasak — Ş5) |
| 10-band alt küme frekansı VLC'den farklı (63 vs 62.5) | 3 (olası) | 1 (düşük) | Kasıtlı karar: ISO grid tutarlılığı (§2.2b kural 3); fark ~14 sent, belgeledik |
| Reconnect/thundering-herd (birden fazla istasyon) | 2 (mümkün) | 2 (düşük) | ±%20 jitter backoff (kural + Ş2 fake-clock testi) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar & Test Kapıları (debate şartları — bağlayıcı)

| # | Adım / Şart | Detay (kaynak persona) | Sorumlu |
|---|------|---------|---------|
| 1 | `neva::radio` iskeleti: TunerThread + libcurl fetch + ICY header parser + backoff durum makinesi | **Ş1 chunk-partition invariance:** aynı akış farklı `icy-metaint` bölmeleriyle sunulduğunda byte-birebir aynı metadata çıktısı (QA) · **Ş2 fake-clock backoff:** gerçek zaman beklenmez, sahte saatle 1s→30s±%20 dizisi assert edilir (QA) | Embedded + Backend |
| 2 | Metadata: sanitizasyon (kural 7) + lock-free kuyruk + C ABI `neva_session_*` minor eklenti (ADR-0105 §2.6) + Python ctypes smoke genişletmesi | XSS testleri: kontrol char/UTF-8 cap/HTML-escape (security #7) | Embedded + QA |
| 3 | **Band-count özelliği:** grid tanımları (10 ISO subset · 15/20 ⚠️ teyit · 31) + sabit 62/124 allocate + bypass maskesi + ramp | **Ş3 click eşikleri:** band-count-change senaryosu ADR-0105 §5.3 metriğiyle ölçülür (kayıp ≤0.5 dB; ek rapor: geçiş bandında peak step **≤ -60 dBFS** — ⚠️ ölçümle kalibre edilebilir) (QA) · **Ş6 4×4 preset matrisi:** 4 band sayısı × preset round-trip (QA) | Embedded + QA |
| 4 | HLS yolu (FFmpeg gateway) + protocol whitelist + aynı URL Validator | **Ş5 FFmpeg 9.0.2 pin + HLS golden vektör (2 stream, mock HTTP); CI'da skip yasak** (QA) · **Ş8 offline CI:** gerçek ağ yok, mock sunucu (QA) | Embedded + DevOps |
| 5 | Güvenlik kapısı | **Şart 9 (security review):** SSRF-bypass (redirect + DNS rebinding + HLS yolu) / metadata XSS / playlist-bomb testleri merge gate; parser limit tablosu (kural 8) doğrulaması | Security Engineer |
| 6 | Katalog: SQLite tablo (ADR-074 uyum) + Radio Browser API (https mirror + url_resolved) | Çevrimdışı çalışır (Ş8) | Data + Backend |
| 7 | Ölçüm | **ADR-0105 §5.6** (31-band biquad CPU, boşta, bit-perfect) + **radyo+soxr senaryosu eklentisi** (embedded) + **§5.7 nightly stres** radyo+4K senaryosu · **Ş7 radio modülü line coverage ≥%80** (QA) | QA + DevOps |
| 8 | **Şart 4 — null-test kaynak-guard:** radyo senaryosunda null-test **SKIP kanıtı** zorunlu (çalışırsa test FAIL — inverted guard; kapı yalnız yerel dosya) (QA) | QA |
| 9 | Vault senkronu: `architecture/neva-player/index.md` v1.1.0 (radyo/tuner katmanı + §1/§6) + `01-kapsam` radyo satırı + `.decisions/index.md` kayıt + `log.md` append (architect S7) | Vault Steward | Vault Steward |

### 5.2 Geri Dönüş Planı

1. **Tuner:** `neva_session_*` radio fonksiyonları feature-flag (`NEVA_RADIO=0`) ile kapatılır; çekirdek/çıkış etkilenmez (tuner yalnız producer). Kaldırma = tek modül silinir, ADR-0105'e geri dönülür.
2. **HLS/FFmpeg gateway:** HLS akışları desteklenmez → ICY yolu tek başına çalışır (radyo kısmen çalışır kalır).
3. **EQ band-count:** `band_count` ayarı 31'e sabitlenir → maske tamamen set (hepsi aktif) → davranış ADR-0105 v1.1'e birebir döner.
4. **Crossfade ramp:** ramp devre dışı bırakılırsa ADR-0105 §2.3 click yasağı ihlal edilir → yalnız yeni ADR ile geri alınabilir (acil: band-count değişimi pasif).
5. **Katalog/Radio Browser:** çevrimdışı kısım ayrık kalır; API keşfi kapatılabilir (tek satır config).
6. **Vault bozulması:** `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `git checkout` (geçmiş satıra dokunulmaz).

### 5.3 Debate Kaydı (3 tur / 4 persona — 2026-10-20)

| Persona | Tur 1 Konum (özet) | Oy | v1.1'e işlenen şartlar |
|---------|-------------------|----|------------------------|
| **architect** (Expert) | Katman/lisans/rollback olgun; grid çelişkisi + ring invariant kritik | **KOŞULLU-KABUL** | S1 grid düzeltmesi (62.5→63) + 15/20 ⚠️ · S2 topoloji şeması §2.5'e göre yazım · S3 takas bellek-sıra yerine **maske yaklaşımı** (embedded ile uzlaşı) + cascade transient §5.6 · S4 AAC kararı (native aac; FDK ⚠️) · S5 metadata non-RT lock-free kuyruk · S6 ring-starve fade-to-silence · S7 vault senkron |
| **embedded-engineer** (Expert) | İ1 kritik: v1.0 şeması DSP'yi producer'a taşımış; İ2 takas mekaniği tanımsız | **KOŞULLU-KABUL** | §2.2a şeması (producer=decode+soxr+ring, EQ=callback) · **maske/protokol + 62×2 aritmetiği** (takas yok) · reconnect kutusu (prebuffer 2-4s, decoder reset, sessizlik, fade-in) · backoff **1s→30s ±%20 jitter** (SSOT düzeltmesi) · §5.6'ya radyo+soxr · **master clock=cihaz** · metadata lock-free kuyruk · 62.5→63 |
| **security-engineer** (Expert) | İskelet doğru ama HLS SSRF bypass (CRITICAL) + metadata XSS kilit | **KOŞULLU-KABUL** | (1) HTTPS zorunlu/http açık onay · (2) tek URL Validator (her hop + post-connect IP; tuner+FFmpeg) · (3) metadata sanitizasyon (UTF-8/control/256 bayt/escape/hyperlink değil) · (4) parser limit tablosu (M3U8 ≤1000, derinlik ≤1, segment ≤50 MB, XML DTD kapalı, icy-metaint 32 B–1 MiB) · (5) Radio Browser https+url_resolved · (6) userinfo RED + HTTP09Allowed=0 · (7) security review testleri · (8) jitter |
| **qa-engineer** (Expert) | §5'te sayısal eşik/metot yok; backoff çelişkisi; FFmpeg CI pin | **KOŞULLU-KABUL** | Ş1 chunk-partition invariance · Ş2 fake-clock backoff · Ş3 click eşikleri · Ş4 null-test inverted guard · Ş5 FFmpeg pinli CI (skip yasak) · Ş6 4×4 preset matrisi · Ş7 radio line ≥%80 · Ş8 offline CI |

**Uzlaşma (Tur 3):** 4/4 KOŞULLU-KABUL → **27 şart** bu v1.1'e entegre edildi → **KABUL**. Öne çıkan iki uzlaşma: (a) **takas yerine sabit-allocate + bypass maskesi** (architect S3 ↔ embedded İ2 — yaşam-süresi riski kalktı), (b) **backoff SSOT = 1s→30s ±%20 jitter** (QA çelişki itirazı — ADR v1.0'daki "0.5s→8s" research §1.6 ile değiştirildi). **SSRF bypass (security CRITICAL)** tek-URL-Validator + FFmpeg protocol whitelist ile kapatıldı.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-0105-neva-player-media-stack]] | Medya stack SSOT — bu ADR radyo satırı + bant-seçimi ile **tamamlar**; §2.2/§2.3/§2.5/§5.6/§5.7 değişmez (topoloji bu ADR §2.2a'da birebir uyumlu tekrarlanır) |
| [[ADR-019-per-os-neva-player]] | Tuner sesi yalnız `IAudioBackend` üzerinden akar |
| [[ADR-017-dsp-hardware-mode]] | TunerThread non-RT; callback'te ağ/parse/tahsis yasağı |
| [[ADR-006-performance-targets]] | Radyo DSP dahil latency bütçesi |
| [[ADR-074-radio-database-schema]] | CoreMusic radio DB şeması — katalog uyumu |
| [[ADR-004-multi-domain-spa]] | Durum makinesi semantiği (native ↔ web footer) |
| `architecture/neva-player/index.md` | Mimari — radyo/tuner katmanı bu ADR ile eklenir (v1.1.0) |
| `projects/NevaPlayer/research/radio-and-hires.md` | §1.3 kaynağı — 84 kaynak, protokol/WASAPI/ASIO/EQ rehberi + backoff SSOT |
| `projects/NevaPlayer/research/lib-matrix.md` | Lisans matrisi (libcurl/mbedTLS/BASS) |
| `projects/NevaPlayer/reverse/aimp-dump.md` | AIMP 20-band EQ + `Catalogs-Radio.ini` + `bass_hls` emsalleri |
| `projects/nevaplayer/reverse/vlc-dump.md` | VLC dump (✓ 2026-10-20: 10-band + 62.5 teyidi, §2.2b kural 3'e işlendi) |
| `projects/NevaPlayer/01-kapsam-ve-kabul-kriterleri.md` | Kapsam — radyo satırı eklenir |
| `[[CLAUDE.md]]` · `[[brain.md]]` · `[[WORKFLOW.md]]` | Ana sözleşme · özet · süreç |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Master Orchestrator (kullanıcı direktifi: "başla hepsini yap eksiksiz") | 2026-10-20 | ✅ |
| Tech Lead | 4 persona debate uzlaşması (embedded · security · qa raporları) | 2026-10-20 | ✅ (4/4 KOŞULLU-KABUL → şartlar v1.1'e işlendi) |
| Arch Lead | architect (Expert) raporu + topoloji/grid düzeltmeleri | 2026-10-20 | ✅ (KOŞULLU-KABUL şartları S1–S7 işlendi) |

---

*1.1.0 | 2026-10-20 | Debate entegrasyonu (4/4 koşullu-kabul → 27 şart) → accepted — CoreMusic ADR-106*
*1.0.0 | 2026-10-20 | Created — CoreMusic ADR-106 (draft)*
*Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
