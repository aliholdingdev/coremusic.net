---
node_id: K014-media/media-bilesenler.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K003-audio
  - K007-data
  - K010-network
  - K011-application
  - K012-services
  - K013-integraion
evidence:
  - ".ai/architecture/K014-media/media-master.md §9.1-§9.11 (11 çocuk derinleştirmesi), §10 (yaşam döngüsü), §14 (bağımlılık zinciri), §23 (ornekler.md) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-kod-ornekleri.md §2-§16 (15 dil bloğu — söylemler) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 11 bileşen §9.x'ten; her biri TASARIM İDDİASI etiketli; §22 standartları web-arastirma.md'ye taşındı"
open_questions:
  - "⚠️ playback sahipliği (§7/1) — K012/playback-service ile belirsiz."
  - "⚠️ offline sahipliği (§7/2) — K014/offline-sync ↔ K012/offline kuyruğu sınırı."
  - "⚠️ synchronization yönü (§12/9) — çift/tek yönlü UNKNOWN."
  - "⚠️ metadata/artwork kaynağı (§7/8) — yerel etiket vs dış servis UNKNOWN."
  - "⚠️ media-cache boyut/TTL eşikleri (§16/3) — Vault Steward onayı ister."
  - "⚠️ media-testing araç seti (§12/10) — K019 incelemesi bekliyor."
  - "⚠️ catalog arama yöntemi (§9.4) — tam metin/etiket UNKNOWN."
group: mimari
---

# K014 MEDIA — Bileşenler

> Kaynak: `media-master.md` §9.1-§9.11 (çocuk derinleştirmesi) · §10 (yaşam döngüsü) · §14 (bağımlılık zinciri) · §23 (ornekler.md).
> **Tüm bileşenler TASARIM İDDİASI** — medya düğümü kademe-2 design node'dur, repo'da henüz kodu YOK (§23).
> Kod söylemleri: `media-kod-ornekleri.md` §2-§16 (15 dil × 11 çocuk = ornekler.md'de; bu dosyada çekirdek).

## §1 Mimarî Yapı (§14 zincirleri)

```text
§14.1 Çalma:  K011 → K012/playback-service (sahiplik §7/1) → K014/playback → K003-audio → çıkış
§14.2 Akış:   K011 → K014/streaming → K010-network → K013-integraion → dış servis
§14.3 İndirme: K011 → K014/downloads → K010-network → dosya/yerel depo → K014/offline
```

## §2 Bileşenler (11 — §9.x)

### 2.1 library — Kitaplık

- **Rol**: koleksiyonun kendisi (şarkı/album kümesi) — v2 §2.15 (S2).
- **İçerik**: kullanıcı sahipli parçalar · playlist'ler · sıralama (TASARIM İDDİASI).
- **Kalıcılık**: şema K007-data'da (§2.2).
- **Hata**: koleksiyon boşsa EMPTY durumu → K011 gösterir.
- **Kod**: ornekler.md §2-§16 (koleksiyon/track modeli · filtreli sorgu · content-hash dedup · playlist/shelf).

### 2.2 metadata — Şarkı Bilgileri

- **Rol**: sanatçı, albüm, süre, tür (v2 §2.15 · S2).
- **Kaynak**: yerel dosya etiketleri mi dış servis mi — **⚠️ UNKNOWN (§7/8)**.
- **Kalıcılık**: K007-data · **dış servis**: K013-integraion üzerinden (§2.2).
- **Hata**: etiket eksikse fallback (dosya adı) — TASARIM İDDİASI.
- **Standart**: §22.3 — MP3 ID3v2.3/2.4 (TYER→TDRC kırılması) · FLAC Vorbis Comment · MP4 `ilst` · WAV LIST-INFO; EBU R128 (−23 LUFS) / ReplayGain 2.0 (−18 LUFS).
- **Kod**: ornekler.md (ID3 TYER→TDRC · Vorbis Comment · MP4 `ilst` · LIST-INFO · R128/RG2).

### 2.3 artwork — Kapak Resimleri

- **Rol**: kapak resmi indirme/önbellek (v2 §2.15 · S2).
- **Boyut**: thumbnail/medium/large — §22.4 kapattı: ~64-128 px · ~250-500 px (CAA) · ~640-1200 px (CAA 1200 resmi).
- **Önbellek**: media-cache ile kesişim (§9.9) · `immutable`/ETag (§22.5).
- **Kaynak**: yerel/embedded mi dış servis mi — **⚠️ UNKNOWN (§7/8)**.
- **Hata**: kapak yoksa placeholder — TASARIM İDDİASI.
- **Kod**: ornekler.md (CAA 250/500/1200 fetch · varyant üretimi · `immutable`+ETag · gömülü kapak çıkarma).

### 2.4 catalog — Dizin/Veri

- **Rol**: arama kaynağı olabilir (v2 §2.15 · S2).
- **İlişki**: K012/music-service ile sınır — **⚠️ §7/1**.
- **İçerik**: koleksiyonun indekslenmiş hali (TASARIM İDDİASI).
- **Arama**: tam metin/etiket — **⚠️ UNKNOWN (yöntem)**.
- **Kod**: ornekler.md (indeks/arama · content-hash dedup · sorted browse · ARC gerekçesi).

### 2.5 playback — Çalma İşlemi

- **Rol**: dosyanın/sinyalin çalınması (v2 §2.15 · S2).
- **Sahiplik**: K012/playback-service ile belirsiz — **⚠️ §7/1**.
- **Motor**: K003-audio motoru çağrısı (§2.2).
- **Durum**: oynat/duraklat/ileri/geri (TASARIM İDDİASI).
- **Konum**: pozisyon takibi (saniye) — TASARIM İDDİASI.
- **Standart**: §22.2 — gapless/seek: Xing/LAME tag (MP3) + FLAC `SEEKTABLE` + HTTP Range (RFC 9110 §14).
- **Kod**: ornekler.md (durum makinesi IDLE/PLAYING/PAUSED/SEEKING/ENDED · Xing/LAME + SEEKTABLE · pozisyon kalıcılığı).

### 2.6 streaming — Akışla Dinleme

- **Rol**: akışla dinleme (v2 §2.15 · S2).
- **Taşıma**: K010-network · **dış kaynak**: K013-integraion (§2.2).
- **Codec**: içerik biçimi — §22.1/§22.2 kapattı (MP3/FLAC/WAV temel · HLS/DASH adaptif).
- **Hata**: bağlantı koparsa offline fallback — TASARIM İDDİASI.
- **Kod**: ornekler.md (HTTP Range 206 + `Content-Range` · M3U8 manifest · HLS/DASH segment · ETag/`immutable`).

### 2.7 downloads — İndirme

- **Rol**: indirme (v2 §2.15 · S2).
- **Taşıma**: K010-network · **politika**: saklama süresi/boyut sınırı **⚠️ UNKNOWN** (§16/3).
- **Hata**: yarıda kesilirse resume — TASARIM İDDİASI.
- **Karar**: ADR-026 (Download Service Architecture — accepted).
- **Kod**: ornekler.md (`Range: bytes=N-` resume · `.part` + atomik rename · SHA-256/ETag checksum · exponential backoff).

### 2.8 offline — Çevrimdışı Kullanılabilirlik

- **Rol**: çevrimdışı kullanılabilirlik (v2 §2.15 · S2).
- **Sınır**: K014/offline-sync ile — **⚠️ §7/2** (K012/offline kuyruğu).
- **İçerik**: indirilen/önbelleğe alınan parçalar — TASARIM İDDİASI.
- **Karar**: ADR-027 (Dual-Mode Storage Strategy — accepted).
- **Kod**: ornekler.md (`is_offline_ready` · READY/PENDING/MISSING · kota/LRU-TTL eviction · streaming fallback).

### 2.9 media-cache — Medya Önbelleği

- **Rol**: medya önbelleği (v2 §2.15 · S2).
- **Boyut/TTL**: politika **⚠️ UNKNOWN** (§16/3 — Vault Steward).
- **İçerik**: artwork · metadata · parça önbelleği — TASARIM İDDİASI.
- **Temizlik**: §22.5 kapattı: katalog/artwork → **ARC** (scan-resistant); audio stream'de LRU isabet ≈ 0 (tekil dinleme); TTL zamanlayıcı.
- **Kod**: ornekler.md (ARC T1/T2/B1/B2 · LRU karşılaştırma · TTL sweep · ETag revalidation 304).

### 2.10 synchronization — Medya Verisi Eşitlemesi

- **Rol**: cihaz/dış sistemle medya verisi eşitleme (v2 §2.15 · S2).
- **Ad çakışması**: K013-integraion'da da "synchronization" — **⚠️ §7/3**.
- **Yön**: çift/tek yönlü — **⚠️ UNKNOWN (§12/9)**.
- **Çakışma**: son-yazan-kazanır mı — **⚠️ UNKNOWN**.
- **Kod**: ornekler.md (vector clock/LWW · sync queue · delta exchange — yön AÇIK).

### 2.11 media-testing — Medya Testleri

- **Rol**: medya testleri (v2 §2.15 · S2).
- **Sahiplik**: K18-testing ile belirsiz — **⚠️ §7/4** (master §7/4: K019-testing).
- **Kapsam**: decode/çalma/akış kalitesi — TASARIM İDDİASI.
- **Araç**: **⚠️ UNKNOWN** (§12/10 — K019 incelemesi).
- **Kod**: ornekler.md (decode PCM SHA-256 golden fixture · EBU R128 −23 LUFS · Range 206 sözleşmesi · etiket round-trip).

## §3 Yaşam Döngüsü (§10 — TASARIM İDDİASI)

| Aşama | Durum | Geçiş |
|---|---|---|
| 1 · keşif | dış servisten bulundu (K013) | → indirilebilir |
| 2 · indirme | downloads aktif | → yerelde |
| 3 · katalog | metadata + artwork eklendi | → kitaplıkta |
| 4 · çalınabilir | playback hazır | → çalma |
| 5 · çevrimdışı | offline kullanılabilir | → offline mod |
| 6 · önbellek | media-cache'te | → hızlandırılmış erişim |
| 7 · silme | kullanıcı sildi | → arşiv/silindi |

Durum makinesi (§10.2): `discovered → downloading → local → playing/offline/cached/deleted` — geçişler §10.2 tablosunda.

## §4 Bileşen → K Kapsamı

| Bileşen | Kapsam Sahibi | Komşu | Not |
|---|---|---|---|
| library | `K014-media` | `K007-data` (şema) | §9.1 |
| metadata | `K014-media` | `K013` (dış servis) · `K007` | §9.2 · kaynak UNKNOWN |
| artwork | `K014-media` | `K013` (CAA) · `K014/media-cache` | §9.3 |
| catalog | `K014-media` | `K012/music-service` | §9.4 · sınır §7/1 |
| playback | **KARAR YOK** | `K012/playback-service` · `K003-audio` | §9.5 · §7/1 |
| streaming | `K014-media` | `K010-network` · `K013` | §9.6 |
| downloads | `K014-media` | `K010-network` | §9.7 · ADR-026 |
| offline | **KARAR YOK** | `K012/offline` | §9.8 · §7/2 |
| media-cache | `K014-media` | `K007-data` | §9.9 |
| synchronization | **ÇELİŞKİ** | `K013/synchronization` | §9.10 · §7/3 |
| media-testing | **KARAR YOK** | `K18-testing` | §9.11 · §7/4 |
