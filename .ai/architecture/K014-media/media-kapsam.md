---
node_id: K014-media/media-kapsam.md
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
  - ".ai/architecture/K014-media/media-master.md §1 (rol), §2 (kapsam/sınır), §3 (11 çocuk), §22 (web araştırması), §23 (ornekler.md) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-bagimliliklar.md bağımlılıklar (giden/gelen) — okundu 2026-10-18"
  - ".ai/.decisions/index.md ADR-026 · ADR-027 · ADR-028 · ADR-092 · ADR-097 (draft) · ADR-103 (draft) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integration-master.md §5.1-§5.3 (medya sunucu entegrasyonları — sınır) — okundu 2026-10-18"
validation:
  - "2026-10-18 — Bu dosya yalnız mevcut K014 kaynaklarını özetliyor; yeni iddia yok; §22'deki doğrulanmış standartlar web-arastirma.md'ye taşındı"
open_questions:
  - "⚠️ playback sahipliği (K014 vs K012/playback-service) — §7/1: VERIFICATION REQUIRED."
  - "⚠️ offline sahipliği (K014/offline-sync vs K012/offline kuyruğu) — §7/2: VERIFICATION REQUIRED."
  - "⚠️ synchronization adı iki domain'de (K013 + K014) — §7/3: VERIFICATION REQUIRED."
  - "⚠️ media-testing sahipliği (K014 vs K18-testing — master'da K019-testing) — §7/4: VERIFICATION REQUIRED."
  - "⚠️ Referans ürünler (Plexamp/Finamp/Navidrome/Jellyfin) neyi kanıtlar — §7/5: onay bekliyor."
  - "⚠️ Plex/Emby proprietary bilgisi lisans kararı için yeterli mi — §7/6: VERIFICATION REQUIRED."
  - "⚠️ OpenAudio deposu (2.599★) tam adresi — §7/7: UNKNOWN."
  - "⚠️ metadata/artwork verisi kaynağı (dış servis vs yerel) — §7/8: VERIFICATION REQUIRED."
  - "⚠️ §22.1-§22.5 TASARIM ÖNERİLERİ Vault Steward onayı ister (codec seti · akış protokolü · metadata · artwork · cache)."
  - "⚠️ §12'de kalan 5 UNKNOWN: şema biçimi (K007) · boyut/TTL eşikleri · OpenAudio adresi · sync yönü · test aracı (K019)."
  - "⚠️ ADR-097 (K-Space V3) DRAFT — onay bekliyor; ADR-103 (medya-sağlayıcı auth gate) DRAFT."
group: mimari
---

# K014 MEDIA — Kapsam

> Kaynak: `media-master.md` §1-§23 (1.3.0 · 2026-10-17) · `media-bagimliliklar.md` · `.ai/.decisions/index.md` (ADR-026/027/028/092 accepted · 097/103 draft).
> **Not**: media-master.md §1/§9/§18'de kendini "K015" olarak adlandırır (eski ad) — güncel ad **K014-media** (registry §5/15). Bu dosya güncel adı kullanır; master'daki "K015" ibareleri korunmuştur.

## §1 Kapsam — K014 Ne, Değil

**K014 (media)**, şarkının **fiziksel dünyası**dır: koleksiyon tutma (library · metadata · artwork · catalog), çalma (playback · streaming), getirme (downloads · offline · media-cache), eşitleme (synchronization) ve sına (media-testing) — 11 çocuk (§3). **Kapsam dışı**: DSP/sinyal işleme (K003-audio · K004-dsp — yalnız motoru kullanır) · veri şeması/ORM (K007-data) · dış API/katalog konuşması (K013-integraion üzerinden) · ağ taşıması ayrıntısı (K010-network) · arka plan servis yürütme (K012-services) · arayüz gösterimi (K011-application) · YZ önerileri (K006-yapay-zeka / K021-recomenataion).

## §2 Amaçlar

1. **Tut**: koleksiyon + bilgi (library/metadata/artwork/catalog) — kullanıcı sahipli parçalar.
2. **Çal**: dosyanın/sinyalin çalınması ve akışı (playback/streaming) — K003-audio motoru çağrılır.
3. **Getir**: indirme + çevrimdışı (downloads/offline/media-cache) — resume, kota, TTL.
4. **Eşitle**: cihaz/dış sistemle medya verisi aynı tutar (synchronization) — §7/3 çelişkisi.
5. **Sına**: medya testleri (media-testing) — §7/4 sahiplik belirsiz.

## §3 Kapsam Dışı (Bu Alanda Değil)

| Konu | Sahip | Not |
|---|---|---|
| Ses sinyali işleme, DSP, mixer | `K003-audio` / `K004-dsp` | §2.2: "K015 motoru kullanır" |
| Kalıcı veri şeması/ORM | `K007-data` | §2.2: "şema K007'de" |
| Dış API/katalog konuşması | `K013-integraion` | §2.2: "synchronization oradan geçer" |
| Ağ taşıması ayrıntısı | `K010-network` | §2.2: "akış/indirme oradan taşınır" |
| Çalma durumu/orkestrasyonu | `K012-services` (playback-service) | §2.2/§7/1: **sahiplik sorusu** |
| Uygulama akışı/arayüz | `K011-application` | §2.2 |
| YZ önerileri/kişiselleştirme | `K006-yapay-zeka` / `K021-recomenataion` | §2.2 |
| Medya kalite testleri sahipliği | `K18-testing` vs K014/media-testing | §2.2/§7/4: **sahiplik belirsiz** |
| Medya sunucu entegrasyonu (Jellyfin/Plex/Navidrome) | `K013-integraion` §5.1-§5.3 | sınır: entegrasyon K013, medya K014 |

## §4 Çocuk Envanteri (11 — v2 §2.15 birebir · §3)

| # | Çocuk | Rol (TASARIM İDDİASI) | Durum |
|---|---|---|---|
| 1 | library | Kitaplık — koleksiyonun kendisi | draft |
| 2 | metadata | Şarkı bilgileri (sanatçı/albüm/süre/tür) | draft |
| 3 | artwork | Kapak resimleri (indirme/önbellek) | draft |
| 4 | catalog | Dizin/liste verisi (arama kaynağı olabilir) | draft |
| 5 | playback | Çalma işlemi (K012 ile sahiplik belirsiz §7/1) | draft |
| 6 | streaming | Akışla dinleme (taşıma K014 · dış kaynak K013) | draft |
| 7 | downloads | İndirme (saklama politikası ⚠️) | draft |
| 8 | offline | Çevrimdışı kullanılabilirlik (K012 sınır §7/2) | draft |
| 9 | media-cache | Medya önbelleği (boyut/süre ⚠️ UNKNOWN) | draft |
| 10 | synchronization | Medya verisi eşitlemesi (K013 ile aynı ad §7/3) | draft |
| 11 | media-testing | Medya testleri (K18-testing ile §7/4) | draft |

Her çocuk detayı: `media-master.md` §9.1-§9.11 · derinleştirme: çocuk `index.md`'leri (§23.3).

## §5 Proje Kısıtları (§22-§23 — bağlayıcı)

1. **§22 (web araştırması — 2026-10-17)**: 5 UNKNOWN kapandı (codec seti §22.1 · akış protokolü §22.2 · metadata/artwork kaynağı §22.3 · artwork boyutları §22.4 · cache stratejisi §22.5) — hepsi **TASARIM ÖNERİSİ** etiketli, Vault Steward onayı ister. Ayrıntı: `media-web-arastirma.md`.
2. **§22.6 (kalan 5 UNKNOWN)**: şema biçimi (K007) · boyut/TTL eşikleri (Vault Steward §16/3) · OpenAudio adresi · sync yönü (§12/9) · test aracı (K019).
3. **§23 (11 çocuk kod örneği seti)**: 11 `ornekler.md` · 165 dil-bölümü (15 × 11) · ~11.200 satır — **TASARIM ÖNERİSİ**; medya düğümü kademe-2 design node'dur, repo'da kodu YOK.
4. **§23.4**: Zero-Hallucination (uydurma paket/sürüm/API adı yok) · TASARIM ÖNERİSİ etiketi zorunlu · yalnız `K014-media/**` yazıldı · `git commit` atılmadı.

## §6 Bağımlılıklar (§5 — bagimliliklar.md)

### §6.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| `K003-audio` | Ses motoru (decode/çalma) | TASARIM İDDİASI · HIGH (§18) |
| `K007-data` | Kitaplık/metadata kalıcılığı | TASARIM İDDİASI · HIGH (§18) |
| `K013-integraion` | Dış katalog/akış eşitlemesi | TASARIM İDDİASI · MEDIUM (§18) |
| `K010-network` | Akış/indirme taşıması | TASARIM İDDİASI · HIGH (§18) |

### §6.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| `K011-application` | Arayüzün medya gösterimi | TASARIM İDDİASI · LOW (§18) |
| `K012-services` | Servislerin medya verisi okuması | TASARIM İDDİASI · LOW (§18) |

Graf özeti (`media-master.md` §5.3): K014 → K010/K013/K007/K003; K011/K012 → K014; **döngü yok** (2026-10-12 DAG denetimi).

## §7 Doğrulama Geçmişi (§6)

| # | Ölçüt | Sonuç |
|---|---|---|
| 1 | Çocuk listesi v2 §2.15 birebir (11) | ✅ verified |
| 2 | Master adı registry §5/15 birebir (media-master.md) | ✅ verified |
| 3 | §4/1 URL'li kanıt (Jellyfin GitHub) | ✅ verified |
| 4 | §4/2-§4 URL'siz beyan (S3) | ⚠️ kayıtlı, kanıt bekliyor |
| 5 | playback/synchronization sahipliği | ❌ belirsiz → §7/1-§7/3 |
| 6 | §19 kapılar (7/7) | ✅ (satır ≥500 · authority · FM · group · append-only · dolgu yok · imla) |

## §8 Sınır Dışı Bağımlılıklar (Çevresel)

| Dış | Sahip | Not |
|---|---|---|
| MusicBrainz / Cover Art Archive / AcoustID | 3. taraf (dış) | §22.3: limitler doğrulandı (1 sn · 250/500/1200 px · ≤3/sn) |
| RFC 9639 (FLAC) · RFC 6716 (Opus) · RFC 9110 (HTTP Range) · RFC 8216 (HLS) · ISO/IEC 23009-1 (DASH) | Standart | §22.1-§22.2: doğrulandı (2026-10-17) |
| Jellyfin (GPL-2.0 · 55732★) · Navidrome (GPL-3.0 · 24109★) | Referans ürün (açık kaynak) | §4/1 · §11: kod/fikir incelemesi |
| Plex · Emby · Plexamp | Proprietary | §11.1: yalnız fikir (kopya yasak) |
| OpenAudio (~2800★) | Aday referans | §7/7: kimlik teyidi bekliyor ⚠️ |
