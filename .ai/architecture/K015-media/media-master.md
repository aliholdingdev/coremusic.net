---
title: "K015-media — Media Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K015-media
role: master
authority: "SSOT: v2 §2.15 + .ai/architecture/index.md §5/15"
related: ["media-bagimliliklar.md", "media-dogrulama.md", "index.md"]
---

# K015-media — media-master.md

> Bu dosya v2 §2.15'te adı geçen **master** dosyasıdır (ad kaynaktan birebir,
> ADR-097 K2/K3). Yeni rol dosyaları: media-bagimliliklar.md ·
> media-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.15: *"Şarkı dosyaları dünyası — kapak resimleri, akış, indirme, çevrimdışı."*

K015, müziğin **fiziksel ve katalog tarafı**dır. Beş iş yapar:

1. **Tut** — koleksiyon ve bilgi (library · metadata · artwork · catalog).
2. **Çal** — dosyanın/sinyalin çalınması ve akışı (playback · streaming).
3. **Getir** — indirme ve çevrimdışı (downloads · offline · media-cache).
4. **Eşitle** — cihaz/dış sistemle medya verisini aynı tutar (synchronization).
5. **Sına** — medya testleri (media-testing).

Ne yapmaz: DSP/sinyal işlenmesi yapmaz (K003-audio · K004-dsp — yalnız motoru
kullanır), veri şemasına karar vermez (K007-data), dış katalogla konuşmaz
(K013-integraion üzerinden), ağ taşımasının ayrıntısını yazmaz (K014-network),
arka plan servisi yürütmez (K012-services), arayüz göstermez (K011-application).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Koleksiyon ve bilgi: library · metadata · artwork · catalog
- Çalma ve akış: playback · streaming
- İndirme/çevrimdışı/önbellek: downloads · offline · media-cache
- Eşitleme ve test: synchronization · media-testing

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| Ses sinyali işleme, DSP, mixer | K003-audio / K004-dsp | K015 motoru kullanır |
| Kalıcı veri şeması/ORM | K007-data | K015 veri sahibi (koleksiyon) ama şema K007'de |
| Dış API/katalog konuşması | K013-integraion | synchronization oradan geçer |
| Ağ taşıması ayrıntısı | K014-network | akış/indirme oradan taşınır |
| Çalma durumu/orkestrasyonu | K012-services (playback-service) | §7/1 sahiplik sorusu |
| Uygulama akışı / arayüz | K011-application | |
| YZ önerileri / kişiselleştirme | K006-yapay-zeka / K020-recomenataion | |
| Medya kalite testleri sahipliği | K019-testing vs K015/media-testing | §7/4 |

## §3 Kademe-2 çocuk tablosu (v2 §2.15 birebir — 11 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | library | Kitaplık | Koleksiyonun kendisi (şarkı/album kümesi) | draft |
| 2 | metadata | Şarkı bilgileri | Sanatçı, albüm, süre, tür (kaynak ⚠️) | draft |
| 3 | artwork | Kapak resimleri | İndirme/önbellek ilişkili (ayrıntı ⚠️) | draft |
| 4 | catalog | Dizin/liste verisi | Arama kaynağı olabilir (K012/music-service ile sınır ⚠️) | draft |
| 5 | playback | Çalma işlemi | K012/playback-service ile sahiplik belirsiz §7/1 | draft |
| 6 | streaming | Akışla dinleme | Taşıma K014 · dış kaynak K013 | draft |
| 7 | downloads | İndirme | Taşıma K014 · saklama politikası ⚠️ | draft |
| 8 | offline | Çevrimdışı kullanılabilirlik | K014/offline-sync ile sınır §7/2 | draft |
| 9 | media-cache | Medya önbelleği | Boyut/süre politikası → ⚠️ UNKNOWN | draft |
| 10 | synchronization | Medya verisi eşitlemesi | K013/synchronization ile aynı ad → §7/3 | draft |
| 11 | media-testing | Medya testleri | K019-testing ile sahiplik §7/4 | draft |

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | Jellyfin açık kaynak medya sunucusu: GitHub 55732★ · GPL-2.0 · .NET · kökeni Emby 3.5.2 fork'u (2018-12-09) | https://github.com/jellyfin/jellyfin | 2026-10-12 | Referans ürün: kitaplık/metadata/akış yapısını inceleme kaynağı (K015 + K011) |
| 2 | Plex ve Emby **proprietary** (açık kaynak değildir); Jellyfin bunların açık-kaynak olmayan alternatifi olarak doğmuştur | ⚠️ URL görev beyanında yok | 2026-10-12 | Lisans/kapsam kararı: kopyalanamayan ürünler, yalnız fikir referansı |
| 3 | Müzik tarafında öne çıkan alternatifler: Plexamp (self-hosted müzik), Finamp (Jellyfin müzik mobile), Navidrome | ⚠️ URL görev beyanında yok | 2026-10-12 | **Referans ürün analizi** → §7/5 açık soru (karar değil) |
| 4 | OpenAudio — 2.599★ | ⚠️ URL görev beyanında yok | 2026-10-12 | Aday referans ürün (repo yeri belirsiz → ⚠️) |

**§4'e girmeyen hiçbir dil, framework, kütüphane, paket veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| Ses codec/format seti (hangileri) | ⚠️ UNKNOWN |
| Kitaplık veritabanı/şema biçimi | ⚠️ UNKNOWN (K007'ye ait) |
| İndirme/önbellek boyut ve TTL politikaları | ⚠️ UNKNOWN |
| Akış protokolü (taşıma K014'te; içerik biçimi ⚠️) | ⚠️ UNKNOWN |
| OpenAudio deposunun tam adresi | ⚠️ UNKNOWN (yalnız ★ sayısı beyan edildi) |

## §5 Bağımlılık özeti (tam metin: media-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K003-audio | Ses motoru (decode/çalma) | TASARIM İDDİASI |
| K007-data | Kitaplık/metadata kalıcılığı | TASARIM İDDİASI |
| K013-integraion | Dış katalog/akış eşitlemesi | TASARIM İDDİASI |
| K014-network | Akış/indirme taşıması | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Arayüzün medya gösterimi | TASARIM İDDİASI |
| K012-services | Servislerin medya verisi okuması | TASARIM İDDİASI |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011-application · K012-services ──(medya okur)──→ K015-media
K015-media
├─→ K014-network   (akış/indirme taşıması)
├─→ K013-integraion (dış katalog eşitlemesi)
├─→ K007-data      (kalıcılık)
└─→ K003-audio     (ses motoru)
K015'in K011/K012'ye geri dönüş oku: YOK → döngü yok (2026-10-12)
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.15 ile birebir (11) | metin karşılaştırma | ✅ verified |
| 2 | Master dosyası adı registry §5/15 ile birebir (media-master.md) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4/1 satırı URL'li kanıta dayanıyor | satır-başı kanıt sütunu | ✅ verified |
| 5 | §4/2–§4 satırları URL'siz beyan (S3) | satır-başı kontrol | ⚠️ kayıtlı, kanıt bekliyor |
| 6 | 5 dosyalık MD seti + 11 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 7 | playback/synchronization sahipliği | v2 metni okundu | ❌ belirsiz → ⚠️ |

## §7 Açık sorular

1. playback sahibi K015 mi, K012/playback-service mi? → ⚠️ VERIFICATION REQUIRED
2. offline ↔ K014/offline-sync ↔ K012 sınırı (çevrimdışı kimin)? → ⚠️ VERIFICATION REQUIRED
3. synchronization adı iki domain'de (K013 + K015): ne ayırıyor? → ⚠️ VERIFICATION REQUIRED
4. media-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED
5. Referans ürünler (Plexamp · Finamp · Navidrome · Jellyfin) neyi **kanıtlar**? → onay bekliyor
6. Plex/Emby proprietary bilgisi lisans kararı için yeterli mi? → ⚠️ VERIFICATION REQUIRED
7. OpenAudio deposu (2.599★) tam adresi nedir? → ⚠️ UNKNOWN
8. metadata/artwork verisinin kaynağı (dış servis mi yerel mi)? → ⚠️ VERIFICATION REQUIRED

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.15 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk listesi + zorunlu alanlar |
| Registry | .ai/architecture/index.md §5/15 · §8 | Master adı + isimlendirme |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| Jellyfin | https://github.com/jellyfin/jellyfin | 2026-10-12 kanıtı (★ · lisans · fork) |
| Plex / Emby (proprietary) | ⚠️ URL yok | 2026-10-12 beyanı |
| Plexamp · Finamp · Navidrome | ⚠️ URL yok | 2026-10-12 beyanı (referans ürün) |
| OpenAudio (2.599★) | ⚠️ URL yok | 2026-10-12 beyanı |

---

**Status:** draft · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-12
