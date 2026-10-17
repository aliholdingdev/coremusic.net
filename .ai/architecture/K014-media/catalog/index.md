---
node_id: K014-media/catalog
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
  - "catalog ↔ K012/music-service sınırı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---


# catalog — K014-media (kademe-2 · subdomain)

**ELI15:** Listeleme/dizin verisi: hangi görünümde ne listelenir, arama kaynağı.

**Sınır (ne DEĞİL):** Arama motoru/katalog sorgusu K012/music-service ile sınır belirsiz.

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
| 2 | catalog ↔ K012/music-service sınırı → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Catalog (dizin/veri), koleksiyonun **indekslenmiş halidir** ve arama kaynağını besler (v2 §2.15 · master §9.4 — TASARIM İDDİASI). Çocuk "hangi görünümde ne listelenir" sorusunun veri tarafını taşır; arama motoru/katalog sorgusu servisi K012/music-service ile sınır **belirsizdir** (master §7/1 · FM open_questions). Arama yöntemi (tam metin/etiket) henüz ⚠️ UNKNOWN'dur (master §9.4). Önbellek tarafında **ARC** önerilir: scan-resistant olduğu için tarama ile kirletilmez (master §22.5 — TASARIM ÖNERİSİ). Katalog kimliğinin birincil kaynağı **MusicBrainz**'dir: 1 istek/sn/IP + zorunlu User-Agent (master §22.3).

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Dayanak |
|---|---|---|
| 1 | Tüm koleksiyonun indekslenmiş hali | master §9.4 (TASARIM İDDİASI) |
| 2 | Arama kaynağı olma — tam metin/etiket araması (yöntem ⚠️ UNKNOWN) | master §9.4 · §3/4 |
| 3 | Sıralı gezinme (sorted browse) verisi | TASARIM ÖNERİSİ (bkz. ornekler.md) |
| 4 | Mükerrer kayıt önleme — içerik hash (ETag kavramı) | master §22.5 (TASARIM ÖNERİSİ) |
| 5 | Katalog önbelleği — ARC (scan-resistant) + immutable/ETag | master §22.5 (TASARIM ÖNERİSİ) |
| 6 | Katalog kimliği — MusicBrainz birincil (1 istek/sn/IP + User-Agent) | master §22.3 |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Dayanak |
|---|---|---|---|
| 1 | Arama motoru/katalog sorgusu servisi | K012/music-service (sınır belirsiz ⚠️) | master §7/1 · §9.4 |
| 2 | Kalıcı veri şeması | K007-data | master §2.2 |
| 3 | Dış katalog eşitlemesi | K013-integraion | master §2.2 |
| 4 | Parça bilgileri (sanatçı/albüm/süre/tür) | K014-media/metadata | master §9.2 |
| 5 | Önbellek boyut/TTL eşikleri | Vault Steward §16/3 (+ media-cache §9.9) | master §16/3 · §9.9 |
| 6 | Kapak görselleri (kart görseli) | K014-media/artwork | master §9.3 |
| 7 | Ağ taşımasının ayrıntısı | K010-network | master §2.2 |

## §BİLEŞENLER

> Bu tablodaki bileşen adları **TASARIM İDDİASI**'dır (kademe-2 design node — repo'da henüz kodu YOK); sorumluluk satırları master §9.4 + §22'e dayanır.

| # | Bileşen | Sorumluluk | Dayanak |
|---|---|---|---|
| 1 | Catalog Indexer | koleksiyonu indeksler; kimlik birincil kaynağı MusicBrainz (1 istek/sn/IP + zorunlu User-Agent) | master §9.4 · §22.3 |
| 2 | Search Provider | tam metin/etiket araması (yöntem ⚠️ UNKNOWN — §9.4) | master §9.4 |
| 3 | Browse/Sort Engine | sıralı gezinme (sanatçı/albüm/yıl/başlık) | TASARIM ÖNERİSİ |
| 4 | Dedup (content hash) | içerik hash ile mükerrer kayıt/indirme önleme (ETag kavramı) | master §22.5 — TASARIM ÖNERİSİ |
| 5 | Catalog Cache (ARC) | scan-resistant önbellek + `immutable`/ETag HTTP sunumu | master §22.5 (TASARIM ÖNERİSİ) |

## §STANDARTLAR

> Bu bölüm yalnız `media-master.md` §22'de **doğrulanmış** standartları taşır (zero-hallucination); öneri satırları **TASARIM ÖNERİSİ** etiketlidir ve Vault Steward onayı ister.

| # | Standart / kural | Ne söyler | Dayanak |
|---|---|---|---|
| 1 | ARC | **scan-resistant** — tarama ile kirletilmez; katalog için tercih | master §22.5 |
| 2 | LRU / LFU / TTL | LRU genel önbellek (audio stream isabet ≈ 0 — tekil dinleme) · LFU sık dinlenenler · TTL zamanlayıcı temizlik | master §22.5 |
| 3 | HTTP cache başlıkları | `Cache-Control: public, max-age=31536000, immutable` + **ETag** (içerik hash) | master §22.5 |
| 4 | MusicBrainz API politikası | **1 istek/sn/IP** · **User-Agent zorunlu** · CC0 çekirdek — katalog kimliği | master §22.3 |
| 5 | Cover Art Archive kademeleri | 250 / 500 / 1200 px — katalog kartı görsel referansı | master §22.3 · §22.4 |
| 6 | Etiket şeması (indeks alanlarının kaynağı) | ID3v2.3/v2.4 (TYER→TDRC) · Vorbis Comment · MP4 `ilst` (`©nam`·`©ART`·`covr`) · WAV LIST-INFO | master §22.3 |
| 7 | Seviye normalizasyonu (katalog gösterim alanı) | EBU R128 (−23 LUFS yayın) · ReplayGain 2.0 (−18 LUFS) | master §22.3 |

**TASARIM ÖNERİSİ (Vault Steward onayı ister):** katalog önbelleği → **ARC** (scan-resistant) + `immutable`/ETag (master §22.5); boyut/TTL eşikleri §16/3 kararıdır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
