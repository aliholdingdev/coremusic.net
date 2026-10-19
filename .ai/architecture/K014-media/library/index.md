---
node_id: K014-media/library
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Koleksiyon verisinin kalıcılık sahibi (K007 mi K015 mi) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---


# library — K014-media (kademe-2 · subdomain)

**ELI15:** Şarkılardan oluşan kitaplığın kendisi: ne var, ne çalınabilir.

**Sınır (ne DEĞİL):** Bilgi (metadata) ayrı çocukta · kalıcı şema K007-data · DSP K003-audio.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Koleksiyon verisinin kalıcılık sahibi (K007 mi K015 mi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

## §ROL (ELI15)

library, K014-media'nın "kitaplık" çocuğudur: kullanıcının sahip olduğu parça ve albüm kümesinin kendisidir — "ne var, ne çalınabilir" sorusunun cevabı burada durur. Koleksiyonda playlist'ler, sıralama, listeleme ve filtreleme işleri bu çocuğun içindedir. Şarkının *bilgisi* (sanatçı, albüm, süre, tür) ayrı bir çocukta (metadata), kapak resmi ayrı bir çocukta (artwork) durur. Koleksiyonun kalıcı şeması K007-data'dadır; bu çocuk verinin sahibi ama şemayı kendisi kurmaz. Koleksiyon boşsa üretilen EMPTY durumu K011-application tarafından gösterilir.

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Koleksiyonun kendisi (şarkı/album kümesi) | master §3/1 |
| 2 | Kullanıcı sahipli parçalar · playlist'ler · sıralama | master §9.1 |
| 3 | Listeleme · filtreleme · sıralama | master §9.1 |
| 4 | Boş koleksiyon (EMPTY) durumunun üretilmesi | master §9.1 |
| 5 | Parça format alanı (temel set: MP3 · FLAC · WAV — TASARIM ÖNERİSİ) | master §22.1 |
| 6 | Koleksiyon verisi sahipliği (şema K007'de kalır) | master §2.2 · §9.1 |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | Şarkı bilgisi (sanatçı · albüm · süre · tür) | K014-media/metadata | master §3/2 |
| 2 | Kapak resimleri | K014-media/artwork | master §3/3 |
| 3 | Dizin/liste verisi (arama kaynağı) | K014-media/catalog | master §3/4 |
| 4 | Kalıcı veri şeması/ORM | K007-data | master §2.2 |
| 5 | Ses sinyali işleme, DSP, mixer | K003-audio / K004-dsp | master §2.2 |
| 6 | Dış API/katalog konuşması | K013-integraion | master §2.2 |
| 7 | Çalma durumu/orkestrasyonu | K012-services | master §2.2 |
| 8 | Uygulama akışı / arayüz (EMPTY gösterimi dahil) | K011-application | master §9.1 |
| 9 | Ağ taşıması ayrıntısı | K010-network | master §2.2 |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Koleksiyon modeli (parça/album kümesi) | kullanıcının sahip olduğu kayıtları tutmak | TASARIM İDDİASI (master §9.1) |
| 2 | Playlist / shelf yapısı | sıralı parça listelerini tutmak | TASARIM İDDİASI (master §9.1) |
| 3 | Kitaplık sorgusu | listeleme · filtreleme · sıralama | TASARIM İDDİASI (master §9.1) |
| 4 | Yineleme denetimi (dedup) | aynı parçanın iki kez girmesini engellemek (içerik hash / kimlik eşleşmesi) | TASARIM ÖNERİSİ (master §22.5 ETag deseni + §22.3 kimlik) |
| 5 | Boş koleksiyon (EMPTY) durumu | K011'e gösterilecek durumu döndürmek | TASARIM İDDİASI (master §9.1) |
| 6 | Kalıcılık arayüzü | koleksiyonu K007-data'ya okumak/yazmak | TASARIM İDDİASI (master §2.2) |

## §STANDARTLAR

| # | Standart | Ne işe yarar | Kaynak |
|---|---|---|---|
| 1 | MP3 — Fraunhofer patentleri 2017'de doldu (royalty-free) | parça formatı (temel) | master §22.1 |
| 2 | FLAC — RFC 9639 · royalty-free · Xiph.org BSD | kayıpsız parça formatı | master §22.1 |
| 3 | WAV/PCM — ham PCM, tescilsiz | ham/kayıpsız parça | master §22.1 |
| 4 | Opus — RFC 6716 · IETF · royalty-free | ağ/düşük gecikme formatı (ileride eklenir) | master §22.1 — **TASARIM ÖNERİSİ** |
| 5 | AAC (Via LA patentli) · WMA (proprietary) | kaçınılır | master §22.1 — **TASARIM ÖNERİSİ** |
| 6 | HTTP Range — RFC 9110 (STD 97) | parça dosyasına erişim/seek (varlık düzeyi) | master §22.2 |
| 7 | ARC (scan-resistant) · LRU · LFU · TTL | koleksiyon/önbellek temizleme stratejisi | master §22.5 — **TASARIM ÖNERİSİ** (katalog için ARC) |
| 8 | `Cache-Control: public, max-age=31536000, immutable` + ETag (içerik hash) | içerik hash → dedup deseni | master §22.5 |
| 9 | MusicBrainz (CC0 çekirdek · User-Agent zorunlu · 1 istek/sn/IP) + AcoustID (≤3 istek/sn) | kimlik eşleşmesi → dedup | master §22.3 — **TASARIM ÖNERİSİ** (birincil kimlik) |
| 10 | Koleksiyon şema biçimi | ⚠️ UNKNOWN — K007-data'ya ait | master §12/2 · §22.6 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
