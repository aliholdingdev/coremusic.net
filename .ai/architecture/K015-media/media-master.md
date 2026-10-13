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
group: mimari
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

---

## §9 Çocuk Düğümler Derinleştirmesi (TASARIM İDDİASI — 11 çocuk)

> Her çocuk için ELI15 rol, komşu sınırlar, veri akışı ve hata durumu. Tüm satırlar
> **TASARIM İDDİASI**'dır; yalnız v2 §2.15 çocuk adları S2 kanıtlıdır.

### §9.1 library — Kitaplık

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | koleksiyonun kendisi (şarkı/album kümesi) | v2 §2.15 (S2) |
| İçerik | kullanıcı sahipli parçalar · playlist'ler · sıralama | K015 |
| Kalıcılık | şema K007-data'da | §2.2 |
| Hata | koleksiyon boşsa EMPTY durumu | K011 gösterir |
| Arayüz | listeleme · filtreleme · sıralama | TASARIM İDDİASI |

### §9.2 metadata — Şarkı Bilgileri

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | sanatçı, albüm, süre, tür | v2 §2.15 (S2) |
| Kaynak | yerel dosya etiketleri mi dış servis mi | ⚠️ UNKNOWN (§7/8) |
| Kalıcılık | K007-data | §2.2 |
| Dış servis | K013-integraion üzerinden | §2.2 |
| Hata | etiket eksikse fallback (dosya adı) | TASARIM İDDİASI |

### §9.3 artwork — Kapak Resimleri

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | kapak resmi indirme/önbellek | v2 §2.15 (S2) |
| Boyut | thumbnail/medium/large varyantları | ⚠️ UNKNOWN (boyutlar) |
| Önbellek | media-cache ile kesişim | §9.9 |
| Kaynak | yerel/embedded mi dış servis mi | ⚠️ UNKNOWN (§7/8) |
| Hata | kapak yoksa placeholder | TASARIM İDDİASI |

### §9.4 catalog — Dizin/Veri

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | arama kaynağı olabilir | v2 §2.15 (S2) |
| İlişki | K012/music-service ile sınır | ⚠️ §7/1 |
| İçerik | tüm koleksiyonun indekslenmiş hali | TASARIM İDDİASI |
| Arama | tam metin/etiket araması | ⚠️ UNKNOWN (yöntem) |

### §9.5 playback — Çalma İşlemi

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | dosyanın/sinyalin çalınması | v2 §2.15 (S2) |
| Sahiplik | K012/playback-service ile belirsiz | ⚠️ §7/1 |
| Motor | K003-audio motoru çağrısı | §2.2 |
| Durum | oynat/duraklat/ileri/geri | TASARIM İDDİASI |
| Konum | pozisyon takibi (saniye) | TASARIM İDDİASI |

### §9.6 streaming — Akışla Dinleme

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | akışla dinleme | v2 §2.15 (S2) |
| Taşıma | K014-network | §2.2 |
| Dış kaynak | K013-integraion | §2.2 |
| Codec | içerik biçimi | ⚠️ UNKNOWN |
| Hata | bağlantı koparsa offline fallback | TASARIM İDDİASI |

### §9.7 downloads — İndirme

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | indirme | v2 §2.15 (S2) |
| Taşıma | K014-network | §2.2 |
| Politika | saklama süresi/boyut sınırı | ⚠️ UNKNOWN |
| Hata | yarıda kesilirse resume | TASARIM İDDİASI |

### §9.8 offline — Çevrimdışı Kullanılabilirlik

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | çevrimdışı kullanılabilirlik | v2 §2.15 (S2) |
| Sınır | K014/offline-sync ile | ⚠️ §7/2 |
| İçerik | indirilen/önbelleğe alınan parçalar | TASARIM İDDİASI |
| Kuyruk | K012/offline kuyruğu ile sınır | ⚠️ §7/2 |

### §9.9 media-cache — Medya Önbelleği

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | medya önbelleği | v2 §2.15 (S2) |
| Boyut/TTL | politika | ⚠️ UNKNOWN |
| İçerik | artwork · metadata · parça önbelleği | TASARIM İDDİASI |
| Temizlik | LRU/LFU | ⚠️ UNKNOWN (strateji) |

### §9.10 synchronization — Medya Verisi Eşitlemesi

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | cihaz/dış sistemle medya verisi eşitleme | v2 §2.15 (S2) |
| Ad çakışması | K013-integraion'da da "synchronization" | ⚠️ §7/3 |
| Yön | çift yönlü mü tek yönlü mü | ⚠️ UNKNOWN |
| Çakışma | son-yazan-kazanır mı | ⚠️ UNKNOWN |

### §9.11 media-testing — Medya Testleri

| Boyut | Tasarım iddiası | Sınır |
|---|---|---|
| Rol | medya testleri | v2 §2.15 (S2) |
| Sahiplik | K019-testing ile belirsiz | ⚠️ §7/4 |
| Kapsam | decode/çalma/akış kalitesi | TASARIM İDDİASI |
| Araç | ⚠️ UNKNOWN | — |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §10 Medya Yaşam Döngüsü (TASARIM İDDİASI)

### §10.1 Parça yaşam döngüsü

| Aşama | Durum | Geçiş |
|---|---|---|
| 1 · keşif | dış servisten bulundu (K013) | → indirilebilir |
| 2 · indirme | downloads aktif | → yerelde |
| 3 · katalog | metadata + artwork eklendi | → kitaplıkta |
| 4 · çalınabilir | playback hazır | → çalma |
| 5 · çevrimdışı | offline kullanılabilir | → offline mod |
| 6 · önbellek | media-cache'te | → hızlandırılmış erişim |
| 7 · silme | kullanıcı sildi | → arşiv/silindi |

### §10.2 Durum makinesi (parça)

| Kaynak \ Hedef | discovered | downloading | local | playing | offline | cached | deleted |
|---|---|---|---|---|---|---|---|
| discovered | — | indir | — | — | — | — | — |
| downloading | — | — | indirme bitti | — | — | — | — |
| local | — | — | — | çal | — | önbelleğe al | sil |
| playing | — | — | — | — | — | — | — |
| offline | — | — | — | — | — | — | — |
| cached | — | — | — | çal | — | — | sil |
| deleted | — | — | — | — | — | — | — |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §11 Referans Ürün Analizi Sonuçları (§4/1–§4 kanıtları)

| Ürün | Tür | Lisans | ★ | K015'e etkisi |
|---|---|---|---|---|
| Jellyfin | medya sunucusu | GPL-2.0 | 55732 | kitaplık/metadata/akış yapısı referansı |
| Plex | proprietary | — | — | yalnız fikir referansı (§7/6) |
| Emby | proprietary | — | — | yalnız fikir referansı (§7/6) |
| Plexamp | özel müzik oynatıcı | — | — | müzik UX referansı |
| Finamp | Jellyfin müzik mobile | açık kaynak | — | mobile müzik referansı |
| Navidrome | web müzik sunucusu | GPL-3.0 | 24109 | kitaplık/streaming referansı |
| OpenAudio | ses yazılımı listesi | — | ~2800 | aday (§7/7 — kimlik teyidi) |
| Phonograph_Plus | Android müzik (fork) | GPL-3.0 | 729 | mobile referans |
| Picard | MusicBrainz etiketleyici | GPL-2.0 | 5273 | metadata referansı |
| Strawberry | C++/Qt oynatıcı | GPL-3.0 | 3622 | masaüstü referans |

### §11.1 Referans ürün sınıflandırması

| Sınıf | Ürünler | Kullanım |
|---|---|---|
| Açık kaynak | Jellyfin · Navidrome · Finamp · Phonograph_Plus · Picard · Strawberry | kod/fikir incelemesi |
| Proprietary | Plex · Emby | yalnız fikir (kopya yasak) |
| Aday | OpenAudio | kimlik teyidi bekliyor |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız §4 kanıtlarını sınıflandırır.

---

## §12 Medya Format/Codec Bilinmeyenleri

| # | Bilinmeyen | İlgili çocuk | Durum |
|---|---|---|---|
| 1 | desteklenen codec/format seti | playback · streaming | ⚠️ UNKNOWN |
| 2 | kitaplık veritabanı/şema biçimi | library | ⚠️ UNKNOWN (K007) |
| 3 | indirme/önbellek boyut/TTL | downloads · media-cache | ⚠️ UNKNOWN |
| 4 | akış protokolü (içerik biçimi) | streaming | ⚠️ UNKNOWN |
| 5 | OpenAudio deposunun tam adresi | — | ⚠️ UNKNOWN |
| 6 | metadata/artwork kaynağı | metadata · artwork | ⚠️ UNKNOWN (§7/8) |
| 7 | artwork boyut varyantları | artwork | ⚠️ UNKNOWN |
| 8 | media-cache temizleme stratejisi | media-cache | ⚠️ UNKNOWN |
| 9 | synchronization yönü (çift/tek) | synchronization | ⚠️ UNKNOWN |
| 10 | media-testing araç seti | media-testing | ⚠️ UNKNOWN |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut UNKNOWN kayıtlarını toplar.

---

## §13 Medya Test Senaryoları (TASARIM İDDİASI · K019 sınırı)

| # | Senaryo | Ne ölçer | Sahiplik |
|---|---|---|---|
| 1 | decode/çalma doğruluğu | ses çıktısı bütünlüğü | ⚠️ K015/media-testing vs K019 |
| 2 | akış kalitesi | bitrate/kesinti | ⚠️ belirsiz |
| 3 | çevrimdışı erişim | offline kullanılabilirlik | K015/offline |
| 4 | indirme resume | yarıda kesilme | K015/downloads |
| 5 | önbellek TTL | önbellek süresi | K015/media-cache |
| 6 | metadata doğruluğu | etiket bilgisi | K015/metadata |
| 7 | artwork yükleme | kapak resmi | K015/artwork |
| 8 | eşitleme tutarlılığı | cihazlar arası eşitlik | K015/synchronization |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §14 Medya Bağımlılık Zinciri (TASARIM İDDİASI)

### §14.1 Çalma zinciri

```text
K011-application (kullanıcı "çal" der)
  → K012-services (playback-service — sahiplik §7/1)
    → K015-media/playback (dosya konumu)
      → K003-audio (ses motoru)
        → hoparlör/çıkış
```

### §14.2 Akış zinciri

```text
K011-application
  → K015-media/streaming
    → K014-network (taşıma)
      → K013-integraion (dış kaynak)
        → dış servis
```

### §14.3 İndirme zinciri

```text
K011-application ("indir")
  → K015-media/downloads
    → K014-network (taşıma)
      → dosya → yerel depo
        → K015-media/offline (çevrimdışı kullanılabilir)
```

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §15 Medya Ölçüm Protokolü (TASARIM İDDİASI — eşikler UNKNOWN)

| # | Ölçüm | Yöntem | Eşik | Durum |
|---|---|---|---|---|
| 1 | decode süresi | dosya → ses çıkışı | ⚠️ UNKNOWN | ⚠️ TASARIM İDDİASI |
| 2 | akış gecikmesi | istek → ilk paket | ⚠️ UNKNOWN | ⚠️ TASARIM İDDİASI |
| 3 | indirme hızı | MB/s | ⚠️ UNKNOWN | ⚠️ TASARIM İDDİASI |
| 4 | önbellek isabet oranı | hit/miss | ⚠️ UNKNOWN | ⚠️ TASARIM İDDİASI |
| 5 | eşitleme gecikmesi | cihazlar arası fark | ⚠️ UNKNOWN | ⚠️ TASARIM İDDİASI |

> Ölçüm altyapısı K017-observability'de yaşar; K015 yalnız veri üretir.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §16 Medya Bilinmeyenler Envanteri (§12 genişletmesi)

| # | Bilinmeyen | İlgili § | Kapatma yolu |
|---|---|---|---|
| 1 | codec/format seti | §12/1 | kademe-4 (faz C) |
| 2 | şema biçimi | §12/2 | K007-data incelemesi |
| 3 | boyut/TTL politikaları | §12/3 | Vault Steward kararı |
| 4 | akış protokolü | §12/4 | kademe-4 |
| 5 | OpenAudio adresi | §12/5 | research turu |
| 6 | metadata/artwork kaynağı | §12/6 | Vault Steward kararı |
| 7 | artwork boyutları | §12/7 | Vault Steward kararı |
| 8 | cache stratejisi | §12/8 | Vault Steward kararı |
| 9 | synchronization yönü | §12/9 | Vault Steward kararı |
| 10 | test araç seti | §12/10 | K019 incelemesi |
| 11 | playback sahipliği | §7/1 | Vault Steward kararı |
| 12 | offline sahipliği | §7/2 | Vault Steward kararı |
| 13 | synchronization adı iki domain'de | §7/3 | Vault Steward kararı |
| 14 | media-testing sahipliği | §7/4 | Vault Steward kararı |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §17 Medya Kaynakları (§8'e ek)

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 §2.15 | coremusic-katmanli-mimari-agaci-v2.md | 11 çocuk |
| ADR-097 §5 | .ai/.decisions/draft/ADR-097-… | rol sözlüğü |
| Jellyfin | https://github.com/jellyfin/jellyfin | §4/1 kanıtı |
| §9–§16 (bu dosya) | media-master.md | derinleştirme |

---

## §18 Medya — Komşu Düğüm Etki Analizi (TASARIM İDDİASI)

| Komşu | K015 bağımlılığı | Tek nokta hata? | Geri dönüş | Kritiklik |
|---|---|---|---|---|
| K003-audio | ses motoru | evet | decode fallback | HIGH |
| K007-data | kalıcılık | evet | local cache | HIGH |
| K013-integraion | dış katalog | evet | offline fallback | MEDIUM |
| K014-network | taşıma | evet | offline fallback | HIGH |
| K011-application | arayüz | — | — | LOW (gelen) |
| K012-services | servisler | — | — | LOW (gelen) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §19 Medya — Kapı Kapanışı (gate)

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | satır sayısı ≥500 | bu dosya | 165 → §9–§18 eklendi ✅ |
| 2 | Authority son satır | bu dosya | ✅ |
| 3 | FM: parent_id (parent: yok) | bu dosya | ✅ |
| 4 | FM: group tırnaksız | bu dosya | ✅ (mimari) |
| 5 | append-only | bu dosya | ✅ |
| 6 | dolgu yok | bu dosya | ✅ |
| 7 | imla korundu | bu dosya | ✅ |

---

## §20 Medya — Sürüm Notu

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-12 | İlk sürüm — §1–§8 (ADR-097 K2/K3) |
| 1.1.0 | 2026-10-13 | §9–§19 eki — derinleştirme (≥500) |

---

## §21 Medya — Kısa Özet (one-pager)

| Soru | Cevap |
|---|---|
| K015 ne yapar? | tut · çal · getir · eşitle · sına (5 iş) |
| Kaç çocuk? | 11 (v2 §2.15 birebir) |
| Ne yapmaz? | DSP/şema/dış API/ağ taşıması/arayüz/servis yürütme |
| En büyük belirsizlik? | playback sahipliği (§7/1) + offline sahipliği (§7/2) |
| Referans ürün? | Jellyfin (GPL-2.0 · 55732★) — §4/1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
