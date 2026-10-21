# Neva Player — Açık Kaynak Kütüphane Matrisi (Zero-Hallucination)

> Tarih: 2026-10-19 · Amaç: lisans uyumu + bit-perfect (32bit/192kHz WASAPI Exclusive + ASIO) + kapsam (4K/HDR video, 31-band EQ, tracker/chipset, internet radio) karar matrisi.
> Doğrulama notu: her sürüm/lisans değeri web aramasıyla teyit edildi; edilemeyen hücreler VERIFICATION REQUIRED işaretlendi (uydurma yok).

## §1 — Karar Matrisi

| # | Kütüphane | Lisans | Sürüm (2026-10-19) | Rol | Bit-perfect uygun? | Neva kararı |
|---|---|---|---|---|---|---|
| 1 | libmpv / mpv | LGPL-2.1+ (çekirdek; GPL build'i de var) | v0.41.0 (2026-12-21 release; master v0.41.0-1224-g6a262d11f @2026-10-18) | 4K/HDR video (libmpv embed) | N/A (video) | KULLAN — LGPL lisanslı build ile; HDR tone-mapping gpu-next/libplacebo |
| 2 | FFmpeg | LGPL-2.1+ çekirdek; --enable-gpl --enable-version3 ile GPL parçalar | 9.0 Lei (2026-08-04); son: 9.0.2 (2026-09-18); libavcodec 63.1.100 | Codec altyapısı + demux (HLS dahil) | Sadece decode-side; ÇIKTI CİHAZI YOK (WASAPI yok, ticket #9408 new) | KULLAN — sadece decode/demux; ses çıkışı kendi WASAPI/ASIO backend'ine |
| 3 | libsoxr | LGPL-2.0-or-later | 0.1.3 (2018-02-24; SF son commit 2019-11) | Kaliteli sample-rate conversion | Sadece SRC açıkken; bit-perfect'te bypass | KULLAN — faz preset + SSE/AVX/NEON SIMD; bit-perfect'te devre dışı |
| 4 | miniaudio | Public Domain veya MIT-0 (dual) | v0.11.25 (2026-03-04); 0.12 yolda | Taşınabilir ses backend'i (WASAPI dahil 15+ backend) | KISMI — WASAPI Exclusive var; AYIRT EDİCİ bulgu: ASIO backend'i YOK (issue #133, 2020'den açık; kullanıcı raporunda RME'de bit-d şeffaf olmadığı iddia edildi) | KULLAN (fallback / CoreAudio / Linux); ASIO ve bit-perfect için tek başına YETMEZ |
| 5 | JUCE | Dual: JUCE EULA veya GPLv3; Starter (yıllık ≤$20K gelir) ücretsiz, Indie $40/ay, Pro $175/ay veya $3,500 perpetual | JUCE 8 EULA (7 Mayıs 2024; Jun/Jul 2025 rev.) | Uygulama kabuğu / DSP / cross-platform | Evet — WASAPI Exclusive + ASIO destekli | KULLAN (gerekçeli) — EULA yolu ya da GPLv3; kapalı-kaynak hedefle AGPL çelişir (bkz. §2, §4-R5) |
| 6 | ASIO SDK (Steinberg) | Dual: royalty-free proprietary (SDK Licensing Agreement v2.2 §4; §2.5 bildirim şartı) veya GPLv3 | v2.3 spec (audiosdk/asio, Dual Licensing) | ASIO Exclusive ses çıkışı | Evet — ASIO Exclusive bit-perfect | KULLAN — nevaplayer ASIO wrapper (ROLE §4.3) ile; COM base + AsioDrivers enumerasyon + ASIOConvertSamples |
| 7 | BASS | Kapalı kaynak; ücretsiz non-commercial; ticari: shareware €125 (≤€40/product), Single €950, Unlimited €3.450 | un4seen.com (canlı) | Alternatif ses motoru (radyo destekli) | Evet (ASIO+WASAPI var) | KULLANMA — kapalı kaynak + commercial ücret; Neva'nın hybrid modeliyle çelişir |
| 8 | RtAudio / RtMidi | RtAudio: MIT-tarzı (similar to MIT); RtMidi: MIT | RtAudio 6.0.1 (2023-08-01, soversion 7); RtMidi sürümü VERIFICATION REQUIRED | Hafif C++ audio/MIDI wrapper (DirectSound, ASIO, WASAPI) | Hayır — Exclusive/bit-perfect garanti etmez | ARAŞTIR — kendi backend ile çakışma riski, kapsam fazlalığı |
| 9 | libopenmpt | BSD-3-Clause | v0.8.9 (2026-08-19) | Tracker modül decode (MOD/XM/IT/S3M) | Evet | KULLAN — tracker için temiz lisanslı tek seçenek |
| 10 | libgme | LGPL-2.1 çekirdek; AMA MAME YM2612 varyantı GPL 2.0+; GENS/Nuked OPN2 varyantları LGPL | son etiket VERIFICATION REQUIRED | Oyun müziği (NES/SMS/GameBoy/Genesis) | Evet | ARAŞTIR/KOŞULLU — Nuked/GENS (LGPL) ile build et; MAME varyantı GPL bulaştırır |
| 11 | opusfile | BSD-3-Clause | VERIFICATION REQUIRED | Opus decode + HTTP(S) streaming (opusurl) | Evet | KULLAN — opusurl openssl isteyebilir |
| 12 | libFLAC | New BSD License (referans kütüphane); FLAC CLI araçları GPL | VERIFICATION REQUIRED | FLAC decode/encode | Evet | KULLAN — BSD |
| 13 | mpg123 | LGPL-2.1-or-later | VERIFICATION REQUIRED | MP3 decode | Evet | KULLAN — LGPL dynamic link |
| 14 | libvorbis | Xiph.Org — BSD-tarzı (SPDX doğrulanacak) | VERIFICATION REQUIRED | Vorbis/OGG decode | Evet | KULLAN (lisans commit öncesi doğrulanacak) |
| 15 | TagLib | LGPL-2.1 veya MPL-1.1 (dual) | v2.0 (2024-01-24) | Metadata (MP3/FLAC/Ogg/Opus/Matroska/ASF + DSF/DFF) | N/A | KULLAN — DSF/DFF desteği DSD hedefiyle uyumlu; MPL seçeneği kapalı kaynakla daha uyumlu |
| 16 | SQLite | Public Domain | 3.53.4 (2026-08-01) | Yerel müzik kütüphanesi DB | N/A | KULLAN — sıfır risk |
| 17 | libcurl | curl license (MIT-tarzı) | 8.23.0 (2026-10-14) | HTTP/HTTPS istemcisi | N/A | KULLAN |
| 18 | mbedTLS | Dual: Apache-2.0 veya GPL-2.0-or-later | VERIFICATION REQUIRED | TLS (curl backend) | N/A | KULLAN (Apache-2.0 seçimi) |
| 19 | OpenSSL | 3.0+: Apache-2.0 (öncesi dual OpenSSL/SSLeay) | VERIFICATION REQUIRED | Alternatif TLS | N/A | ARAŞTIR — aksi halde mbedTLS tercih |
| 20 | ICY / Shoutcast v2 | Protokol spec (kütüphane değil) | ICY 1.x + Shoutcast v2 XML Metadata Spec; ICY-META v2.2 (Şub 2026) | Internet radio metadata (icy-name/icy-genre/icy-br/icy-metaint; icy-metadata-version: 2) | N/A | KULLAN (özel implementasyon) — libcurl + ICY header parsing; hazır kütüphane gerekmiyor |

## §2 — Lisans Stratejisi

**1) LGPL dinamik linking (FFmpeg/FLAC/vorbis/mpg123/gme):**
- FFmpeg LGPL sürümü (gpl kapalı) ile build; GPL-only codec'ler asla eklenmez.
- libgme YM2612 seçimi kritik: MAME varyantı GPL 2.0+ → tüm uygulama GPL. Nuked OPN2 veya GENS (LGPL 2.1+) şart.
- LGPL kütüphaneler dynamic link ile; static ise LGPL'ün değiştirilebilirlik şartları karşılanmalı.

**2) GPL stratejisi:**
- mpv'nin GPL build'inden kaçın; LGPL FFmpeg ile LGPL libmpv build kullan (aksi halde uygulama GPL olur).
- ASIO SDK'yi GPLv3 yerine royalty-free proprietary lisansla kullan (§2.5 Steinberg'e signed agreement ile bildirim + ASIO logo/copyright notice/about-box şartları). GPLv3 + Apache-2.0 mbedTLS kombinasyonu uyumsuzdur.

**3) MIT/Apache/PD/MPL (miniaudio, TagLib-MPL, curl, mbedTLS, SQLite):**
- Hepsi kapalı-kaynak/hybrid ile uyumlu; proprietary kapanma senaryosu sorun çıkarmaz.
- JUCE'de JUCE EULA (Starter/Indie/Pro) kapalı-kaynak yolu açar; AGPLv3 yolu derived work'ü AGPL yapar ve hedefle çelişir.

## §3 — Önerilen Nihai Yığın

| Katman | Seçim | Gerekçe |
|---|---|---|
| Ses çıkışı (bit-perfect yol 1) | ASIO SDK (proprietary lisans) + kendi wrapper | ASIO Exclusive = bit-perfect; ROLE §4.3 ASIO wrapper (audiosdk/asio) ile birebir |
| Ses çıkışı (bit-perfect yol 2) | miniaudio WASAPI Exclusive (veya elle WASAPI backend) | FFmpeg'de WASAPI yok (#9408); miniaudio'da var ama bit-d şeffaflık doğrulanmalı (#133 iddiası) |
| Fallback / çok-platform | miniaudio (CoreAudio, ALSA, PulseAudio, JACK) | Tek header, PD/MIT-0, 15+ backend |
| SRC | libsoxr (bit-perfect'te bypass) | Filtre kalitesi + SIMD |
| Video (4K/HDR) | libmpv (LGPL build) — wid HWND embed | Maintainer onaylı Windows embed yolu; HDR tone-mapping gpu-next/libplacebo; mpv.net emülasyonu referans |
| Codec altyapısı | FFmpeg 9.x LGPL (decode/demux; HLS demuxer dahil) | Çıktı cihazı yok → ses çıkışı kendi backend'ine |
| Format desteği | libFLAC (BSD), opusfile (BSD), mpg123 (LGPL), libvorbis (BSD-tarzı), libopenmpt (BSD), libgme (Nuked/GENS LGPL) | Temiz lisans enzimi |
| Metadata | TagLib (MPL-1.1 seçimi) — DSF/DFF | DSD hedefi uyumu |
| Ağ / radio | libcurl + mbedTLS (Apache-2.0) + özel ICY/Shoutcast v2 parser | ICY v2.2 metadata + XML/DNS-SD discovery |
| Uygulama kabuğu / DSP | JUCE 8 (EULA yolu) veya kendi C++20 framework'ü | AGPLv3 yolu kapalı-kaynakla çelişir |
| DB | SQLite (public domain) | Sıfır risk |

## §4 — Riskler ve Açıklar

| ID | Risk | Etki | Önlem |
|---|---|---|---|
| R1 | mpv GPL build'i (LGPL FFmpeg'siz) → tüm uygulama GPL | Kritik | LGPL FFmpeg ile LGPL libmpv; dynamic link şartları |
| R2 | libgme MAME YM2612 → GPL 2.0+ | Yüksek | Nuked OPN2 / GENS (LGPL) build flag; MAME varyantını ekleme |
| R3 | ASIO SDK proprietary §2.5 — Steinberg'e signed agreement ile bildirim zorunlu | Yüksek | Bildirim akışı + about-box ASIO logo/copyright notice; GPLv3 yolundan kaçın |
| R4 | miniaudio'da ASIO backend'i yok (#133); WASAPI bit-d şeffaflığı doğrulanmamış | Yüksek | ASIO için ayrı SDK; WASAPI Exclusive'i gerçek donanımda (RME/ESI) bit-d doğrula |
| R5 | JUCE AGPLv3 yolu kapalı-kaynakla çelişir; Starter EULA gelir sınırı (≤$20K/yıl) | Orta | JUCE EULA (Starter/Indie) ya da kabuğu kendi framework'ünle yaz |
| R6 | FFmpeg'de WASAPI output yok (#9408 new) | Orta | Ses çıkışı asla FFmpeg'e emanet edilmez |
| R7 | libvorbis/opusfile/libFLAC/mpg123/RtMidi/OpenSSL/mbedTLS sürüm+lisans detayları doğrulanmadı | Düşük-Orta | Commit öncesi SPDX + resmi LICENSE sayfasından tek tek teyit |
| R8 | libsoxr bakımı ölü (2019 son commit) | Düşük | Yalnız SRC'de; alternatif (libsamplerate) izole tut |

## §5 — Kaynak URL'leri

- mpv sürümler: https://github.com/mpv-player/mpv/releases (v0.41.0 Dec 21; master v0.41.0-1224-g6a262d11f @2026-10-18)
- mpv embed (win32 wid + HDR/libplacebo): https://github.com/mpv-player/mpv/discussions/16075 (maintainer Dudemanguy yanıtı); https://github.com/mpv-player/mpv/pull/12627 (D3D11 render API PR, closed)
- mpv LGPL lisans kaynak kodu: https://github.com/mpv-player/mpv/blob/master/video/out/w32_common.c ; https://github.com/mpv-player/mpv/blob/master/video/out/d3d.c
- FFmpeg 9.0/9.0.2: https://ffmpeg.org/download.html ; https://ffmpeg.org/releases/ ; https://github.com/FFmpeg/FFmpeg/releases (9.0.2 @2026-09-18)
- FFmpeg WASAPI eksikliği: https://trac.ffmpeg.org/ticket/9408 ; https://trac.ffmpeg.org/ticket/11270
- FFmpeg HLS + output device list: https://ffmpeg.org/ffmpeg-formats.html ; https://ffmpeg.org/ffmpeg-devices.html
- miniaudio v0.11.25: https://github.com/mackron/miniaudio/releases (2026-03-04)
- miniaudio lisans: https://github.com/mackron/miniaudio/blob/master/LICENSE (Public Domain veya MIT-0)
- miniaudio backend/docs: https://miniaud.io/docs/manual/
- miniaudio ASIO yok: https://github.com/mackron/miniaudio/issues/133
- JUCE 8 EULA: https://www.juce.com/legal/juce-8-licence/ (7 May 2024; Jun/Jul 2025 rev.)
- JUCE fiyatlandırma: https://www.juce.com/get-juce/
- ASIO SDK repo (Dual Licensing): https://github.com/audiosdk/asio
- ASIO SDK Licensing Agreement v2.2 (§2.5, §4): https://www.amanero.com/downloads/ASIO_SDK_Licensing_Agreement_v2.2-Oct_2005.pdf
- BASS lisans/tier: https://un4seen.com/bass.html
- RtAudio: https://github.com/thestk/rtaudio (v6.0.1 2023-08-01; LICENSE similar to MIT; doc/release.txt)
- RtMidi: https://github.com/thestk/rtmidi (LICENSE; Windows Multimedia/UWP)
- libopenmpt: https://lib.openmpt.org/libopenmpt/download/ (v0.8.9 2026-08-19); https://lib.openmpt.org/libopenmpt/license/ (BSD-3-Clause)
- libgme: https://github.com/libgme/game-music-emu (LGPL 2.1; YM2612 MAME→GPL 2.0+, Nuked/GENS→LGPL)
- opusfile: https://github.com/xiph/opusfile (BSD-3-Clause; opusurl/ogg/https notları)
- libFLAC lisans: https://xiph.org/flac/license.html (New BSD Library; CLI GPL)
- mpg123: https://www.mpg123.de/ ; https://www.mpg123.de/download.shtml (LGPL 2.1)
- TagLib: https://taglib.org/ ; https://taglib.org/api/ (DSF/DFF + tracker API); https://github.com/taglib/taglib/releases (v2.0 2024-01-24)
- SQLite: https://www.sqlite.org/chronology.html (3.53.4 @2026-08-01); https://www.sqlite.org/copyright.html (public domain)
- libcurl: https://curl.se/download.html (8.23.0 @2026-10-14); https://curl.se/docs/copyright.html
- mbedTLS: https://github.com/Mbed-TLS/mbedtls (Apache-2.0 OR GPL-2.0-or-later)
- OpenSSL lisans: https://www.openssl.org/source/license.html (3.0+ Apache-2.0)
- libsoxr: https://sourceforge.net/projects/soxr/ (0.1.3 2018-02-24; LGPL 2.0; SSE/AVX/NEON; faz presetleri)
- ICY/Shoutcast v2: http://www.mcaster1.com/icy2-spec/ ; https://wiki.shoutcast.com/wiki/SHOUTcast_XML_Metadata_Specification

## Özet (10 satır)

1. Video için libmpv v0.41.0 (LGPL build) — Windows'ta wid HWND embed, HDR/libplacebo gpu-next ile.
2. FFmpeg 9.0.2 sadece decode/demux (LGPL); WASAPI output YOK — ses çıkışı asla FFmpeg'e emanet edilmez.
3. Bit-perfect ses çıkışı iki yol: ASIO SDK (proprietary lisans, §2.5 bildirim şartı) + miniaudio WASAPI Exclusive.
4. miniaudio v0.11.25 ASIO backend'i taşımıyor (issue #133) — ASIO için ayrı SDK şart; WASAPI bit-d doğrulanmalı.
5. libsoxr 0.1.3 kaliteli SRC; bit-perfect modda bypass edilir.
6. BASS KULLANMA: kapalı kaynak + €125–3.450 ticari lisans.
7. libgme YM2612'de MAME varyantı GPL bulaştırır — Nuked/GENS (LGPL) varyantla build.
8. Decoder enzimi temiz: libFLAC/opusfile/libopenmpt BSD, mpg123/libvorbis LGPL/BSD-tarzı; TagLib dual LGPL/MPL (DSF/DFF için).
9. Radio: libcurl 8.23.0 + mbedTLS (Apache-2.0) + özel ICY/Shoutcast v2.2 parser.
10. Kalan doğrulama (R7): libvorbis, opusfile, libFLAC, mpg123, RtMidi, OpenSSL, mbedTLS sürüm+lisans SPDX teyidi commit öncesi.
