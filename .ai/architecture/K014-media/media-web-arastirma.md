---
node_id: K014-media/media-web-arastirma.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K013-integraion
evidence:
  - ".ai/architecture/K014-media/media-master.md §22 (web araştırması — RFC/ISO doğrulaması, 2026-10-17), §22.7 (kaynaklar), §4 (Jellyfin GitHub kanıtı) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-web-arastirma.md §3.8 (MusicBrainz 1 sn/kullanıcı — 200 yeniden doğrulandı 2026-10-18) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-dogrulama.md §3a/§3b (satır kanıtları) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 16 kaynak: 12 §22'den (erişim 2026-10-17) + 1 yeniden doğrulama (2026-10-18) + 3 ⚠️ URL'siz beyan; erişim tarihleri korundu (uydurma tarih yok)"
open_questions:
  - "⚠️ Last.fm sayısal limit — §22.3: UNKNOWN (VERIFICATION REQUIRED)."
  - "⚠️ OpenAudio deposunun tam adresi — §7/7: UNKNOWN (~2800★ beyanı URL'siz)."
  - "⚠️ Plex/Emby/Plexamp/Finamp/Navidrome URL'leri — §4/2-§4: 'görev beyanında yok' (S3 beyan, URL'siz)."
  - "⚠️ ISO/IEC 23009-1:2026 (DASH) + ISO/IEC 14496-3 erişimi doğrulanmadı (§22.7 liste — URL erişimi 2026-10-17'de ayrıca doğrulanmadı)."
  - "⚠️ RFC 8825 (WebRTC) yalnız §22.2'de geçiyor — gerçek zamanlı senaryoya özel (kullanım önerilmez notuyla)."
group: kanit
---

# K014 MEDIA — Web Araştırması

> Kaynak: `media-master.md` §22 (2026-10-17 web araştırması — §12/§16'daki 5 UNKNOWN'u kapatan) · §22.7 (kaynak listesi) · §4 (Jellyfin GitHub) · `integraion-web-arastirma.md` §3.8 (MusicBrainz 2026-10-18 yeniden doğrulama).
> **Kural**: URL erişilemezse `⚠️ DEAD?` + tarih işaretlenir (zero-hallucination). Erişim tarihleri korunur — 2026-10-17 doğrulamaları 2026-18 olarak yazılmaz.

## §1 Doğrulama Yöntemi

1. §22 araştırması (2026-10-17): RFC/ISO/EBU standartları + MusicBrainz/CAA/AcoustID limitleri doğrulandı — 5 UNKNOWN kapandı (§22.6).
2. 2026-10-18 (bu oturum): MusicBrainz URL'leri yeniden doğrulandı (HTTP 200 — `integraion-web-arastirma.md` §3.8).
3. §22.7'deki ISO/EBU kaynakları listelenmiştir; erişim 2026-10-17 raporlanır (ayrı HTTP kontrolü bu oturumda YAPILMADI — honest).

## §2 Doğrulanmış Standartlar (§22.1-§22.2 — erişim 2026-10-17)

| # | Kaynak (URL) | Konu | Erişim | Kullanım |
|---|---|---|---|---|
| 1 | `https://www.rfc-editor.org/rfc/rfc9639.html` | **RFC 9639** — FLAC · royalty-free · Xiph BSD | 2026-10-17 (§22.1) | Codec seti: kayıpsız temel |
| 2 | `https://www.rfc-editor.org/rfc/rfc6716.html` | **RFC 6716** — Opus · IETF · royalty-free | 2026-10-17 (§22.1) | Opus: öneri (ağ/düşük gecikme) |
| 3 | `https://www.rfc-editor.org/rfc/rfc9110.html` | **RFC 9110 (STD 97)** — HTTP semantics · Range | 2026-10-17 (§22.2) | Streaming Range 206 + seek (§14) |
| 4 | `https://www.rfc-editor.org/rfc/rfc8216.html` | **RFC 8216** — HLS (Informational) | 2026-10-17 (§22.2) | Adaptif akış (Apple/CDN uyumu) |
| 5 | `https://www.rfc-editor.org/rfc/rfc8825.html` | **RFC 8825** — WebRTC | 2026-10-17 (§22.2) | ⚠️ yalnız gerçek zamanlı senaryoya özel |
| 6 | ISO/IEC 23009-1:2026 — MPEG-DASH | Bağımsız standard | 2026-10-17 (§22.2 · §22.7) | Adaptif akış alternatifi — URL erişimi ayrıca doğrulanmadı ⚠️ |
| 7 | ISO/IEC 14496-3 | ID3v2.4 / MP4 ilst | 2026-10-17 (§22.3 · §22.7) | Metadata etiket şeması — URL erişimi ayrıca doğrulanmadı ⚠️ |

**MP3 patent notu (§22.1)**: Fraunhofer patentleri 2017'de doldu (royalty-free) — §22.1 doğrulaması. **AAC**: Via LA — encoder/decoder **satışına** uygulanır (⚠️ lisans maliyeti). **WMA**: Microsoft proprietary — ❌ önerilmez.

## §3 Doğrulanmış Metadata Kaynakları (§22.3 — erişim 2026-10-17 · MusicBrainz 2026-10-18)

| # | Kaynak (URL) | Konu | Erişim | Limit (doğrulanmış) |
|---|---|---|---|---|
| 8 | `https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting` | Rate limiting | **2026-10-18 (yeniden — HTTP 200)** | **1 istek/sn/IP** · User-Agent zorunlu · 503 |
| 9 | `https://musicbrainz.org/doc/MusicBrainz_API` | API politikası | **2026-10-18 (yeniden — HTTP 200)** | CC0 çekirdek · application name/version zorunlu |
| 10 | `https://musicbrainz.org/doc/Developer/Resource_Authorization` | OAuth + API key | **2026-10-18 (yeniden — HTTP 200)** | Kimlik doğrulama |
| 11 | Cover Art Archive (CAA) — resmi kademeler | Kapak resmi | 2026-10-17 (§22.3-§22.4) | **250 / 500 / 1200 px** (1200 resmi) · görsel lisansı ayrı |
| 12 | AcoustID API | Parmak izi | 2026-10-17 (§22.3) | **≤3 istek/sn** |
| 13 | Last.fm | Dış metadata | 2026-10-17 (§22.3) | ⚠️ sayısal limit **UNKNOWN** — VERIFICATION REQUIRED |

## §4 Referans Ürün Kanıtı (§4 — erişim 2026-10-12)

| # | Kaynak (URL) | Konu | Erişim | Kanıt |
|---|---|---|---|---|
| 14 | `https://github.com/jellyfin/jellyfin` | Jellyfin — açık kaynak medya sunucusu | 2026-10-12 (§4/1) | **55732★ · GPL-2.0 · .NET · Emby 3.5.2 fork'u (2018-12-09)** |

**§4/2-§4 beyanları (URL'siz — S3)**: Plex/Emby proprietary (§4/2) · Plexamp/Finamp/Navidrome (§4/3) · OpenAudio 2.599★ (§4/4 — §7/7: tam adres UNKNOWN) · Phonograph_Plus 729★ · Picard 5273★ · Strawberry 3622★ (§11 tablosu) — bunlar **URL'siz beyandır**, kanıt bekliyor (§6/5: "⚠️ kayıtlı, kanıt bekliyor").

## §5 Loudness Standartları (§22.3 — erişim 2026-10-17)

| # | Standart | Konu | Erişim | Değer |
|---|---|---|---|---|
| 15 | **EBU R128** | Yayın loudness | 2026-10-17 (§22.3) | −23 LUFS |
| 16 | **ReplayGain 2.0** | Dosya loudness etiketi | 2026-10-17 (§22.3) | −18 LUFS |

## §6 Kapanan / Kalan UNKNOWN (§22.6 — 2026-10-17)

| §12/# | Bilinmeyen | Durum |
|---|---|---|
| 1 | codec/format seti | ✅ **KAPANDI** (§22.1 — RFC 9639/6716 + patent notları) |
| 4 | akış protokolü | ✅ **KAPANDI** (§22.2 — RFC 9110/8216 + ISO DASH) |
| 6 | metadata/artwork kaynağı | ✅ **KAPANDI** (§22.3 — MusicBrainz/CAA/AcoustID) — Last.fm limiti hariç ⚠️ |
| 7 | artwork boyut varyantları | ✅ **KAPANDI** (§22.4 — CAA 250/500/1200) |
| 8 | cache stratejisi | ✅ **KAPANDI** (§22.5 — ARC + immutable/ETag) — eşikler §16/3 onayı ister |
| 2 | şema biçimi | ⏳ AÇIK (K007-data) |
| 3 | boyut/TTL eşikleri | ⏳ AÇIK (Vault Steward §16/3) |
| 5 | OpenAudio adresi | ⏳ AÇIK (§7/7) |
| 9 | synchronization yönü | ⏳ AÇIK (§12/9) |
| 10 | media-testing araç seti | ⏳ AÇIK (K19 — §12/10) |

## §7 Kullanılmayan / Doğrulanmayan İddialar

| İddia | Durum | Not |
|---|---|---|
| Last.fm rate limit | **⚠️ VERIFICATION REQUIRED** | §22.3: sayısal limit UNKNOWN |
| OpenAudio deposu tam adresi | **⚠️ UNKNOWN** | §7/7: ~2800★ beyanı URL'siz |
| Plex/Emby/Plexamp/Finamp/Navidrome URL'leri | **⚠️ URL'siz beyan (S3)** | §4/2-§4: "görev beyanında yok" |
| ISO/IEC 23009-1 / 14496-3 erişimi | **⚠️ ayrıca doğrulanmadı** | §22.7 listesi — URL HTTP kontrolü 2026-10-17'de ayrıca yapılmadı |
| WebRTC (RFC 8825) kullanımı | **⚠️ yalnız gerçek zamanlı** | §22.2: "gerçek zamanlı senaryoya özel" — temel akışta önerilmez |

## §8 Kaynak → Kullanım Tablosu

| Konu (dosya) | Doğrulanmış kaynak | Durum |
|---|---|---|
| Codec seti — `media-kararlar.md` §7/1 (TASARIM ÖNERİSİ) | §2 (#1-#2) + MP3 patent notu | 2026-10-17 ✓ |
| Akış protokolü — `media-bilesenler.md` §2.6 | §2 (#3-#4) | 2026-10-17 ✓ |
| Range 206 — `media-kod-ornekleri.md` §7.1 | §2 (#3) RFC 9110 | 2026-10-17 ✓ |
| HLS M3U8 — `media-kod-ornekleri.md` §15.1 | §2 (#4) RFC 8216 | 2026-10-17 ✓ |
| Metadata kaynağı — `media-kararlar.md` §7/3 | §3 (#8-#10 MusicBrainz · 2026-10-18) + #11-#12 | ✓ |
| Artwork CAA — `media-kod-ornekleri.md` §4.1 | §3 (#11) 1200 px resmi | 2026-10-17 ✓ |
| EBU R128 — `media-kod-ornekleri.md` §12.1 | §5 (#15-#16) | 2026-10-17 ✓ |
| Anti-ban (rate limit) — `media-kararlar.md` §4 (ADR-028) | §3 (#8) 1 sn · User-Agent | 2026-10-18 ✓ |
| Jellyfin referans — `media-kapsam.md` §8 | §4 (#14) GitHub | 2026-10-12 ✓ |
| Codec seti (patent notu) — `media-kararlar.md` §7/1 | §2 MP3 patent dolumu (2017) · Via LA (AAC) | 2026-10-17 ✓ |
| Offline/indirme (resume) — `media-kod-ornekleri.md` §8.1 | §2 (#3) RFC 9110 Range | 2026-10-17 ✓ |
| Artwork varyantları — `media-kod-ornekleri.md` §4.1 | §3 (#11) CAA kademeleri | 2026-10-17 ✓ |

## §9 Yeniden Doğrulama Prosedürü

| Ne | Nasıl | Ne zaman |
|---|---|---|
| §2-§5 URL'leri | websearch + erişim tarihi yenile; 404 → `⚠️ DEAD?` işareti + satır silinmez | 90 günde bir |
| §6-§7 URL'siz beyanlar | URL bulunana kadar beyan kullanılamaz (kod-ornekleri'ne kaynak yapılmaz) | her incelemede |
| §8 eşleşme tablosu | İlgili K dosyası değişince eş satırı güncelle | K dosyası değişiminde |
| Erişim tarihi | Yalnız gerçek erişim günü yazılır — eski tarih silinmez, yenisi eklenir | her doğrulamada |
