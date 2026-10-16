---
node_id: "K015-media/metadata/metadata-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir, S2)"
  - ".ai/architecture/K015-media/media-master.md §9.2 (metadata derinleştirmesi)"
validation: "Draft — her satır TASARIM İDDİASI etiketli; yalnız v2 §2.15 çocuk adı S2 kanıtlıdır; kaynak belirsizliği media-master §7/8 ile korunur (2026-10-16)"
open_questions:
  - "Kaynak: yerel dosya etiketleri mi dış servis mi → ⚠️ VERIFICATION REQUIRED (§7/8)"
  - "Etiket formatı seti (hangi kapsayıcılar) → ⚠️ UNKNOWN"
group: mimari
---

# K015 · metadata · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Kapsıyor, Ne Hariç

---

## §1 KAPSAM İÇİ (modellenen)

> Bu bölümdeki her satır **TASARIM İDDİASI**'dır; tek istisna §1a/1'deki çocuk adı (v2 §2.15 — S2 kanıtlıdır).

### §1a Varlıklar

| # | Varlık | Tanım (tasarım iddiası) | Kanıt |
|---|---|---|---|
| 1 | **metadata (çocuk adı)** | Şarkı bilgileri — sanatçı, albüm, süre, tür | v2 §2.15 (S2) · media-master §9.2 |
| 2 | **Etiket kümesi** | Parçadan okunan alanlar: başlık, sanatçı, albüm, albüm sanatçısı, yıl, parça no, tür, süre, disk no | TASARIM İDDİASI |
| 3 | **Metadata kaydı** | Parça kimliğine bağlı normalize edilmiş bilgi satırı (K007 şemasında tutulur) | TASARIM İDDİASI |
| 4 | **Okuyucu (reader)** | Dosya formatından etiket çıkaran arayüz (tasarım iskeleti: ornekler dosyasında) | TASARIM İDDİASI |
| 5 | **Dış servis zenginleştirme** | Eksik etiketleri dış katalogla tamamlama isteği (K013 üzerinden) | TASARIM İDDİASI |
| 6 | **Normalizasyon kuralı** | Sanatçı adı birleştirme ("feat.", "&", "," varyantları), boş etiket temizliği | TASARIM İDDİASI |

### §1b Yüzeyler

| # | Yüzey | Ne sunar | Sınır |
|---|---|---|---|
| 1 | Etiket okuma | Parça başına tek seferlik okuma (tarama sırasında) | Yerel I/O · TASARIM İDDİASI |
| 2 | Metadata sorgu | Parça/albüm/sanatçı için kayıtlı bilgi | K007'den okur · TASARIM İDDİASI |
| 3 | Düzeltme yüzeyi | Kullanıcının etiketi elle düzeltmesi (varsa) | K011 tetikler · TASARIM İDDİASI |
| 4 | Zenginleştirme | Eksik alanı dış servisle tamamlama (K013 kanalı) | K013 sınırı · TASARIM İDDİASI |
| 5 | Yenileme | Etiket dosyada değişince yeniden okuma | TASARIM İDDİASI |

### §1c Zaman penceresi

| Dönem | Kapsam (tasarım iddiası) |
|---|---|
| Tarama anında | Yeni parça için ilk etiket okuma |
| Sürekli | Dosya değişikliği / hash değişimi tetikleri |
| İstek anında | Dış servis zenginleştirme (K013 üzerinden, rate-limit dahil) |

---

## §2 KAPSAM DIŞI (bu çocukta DEĞİL)

| # | Konu | Nerede | Kanıt |
|---|---|---|---|
| 1 | Kalıcı veri şeması / ORM | K007-data | media-master §2.2 ("Kalıcı veri şeması/ORM → K007-data") |
| 2 | Dış servis konuşmasının taşıması (rate-limit, retry, auth) | K013-integraion | media-master §2.2 ("Dış API/katalog konuşması → K013") |
| 3 | Ağ taşıması | K014-network | media-master §2.2 |
| 4 | Dosya formatından ham bayt okuma I/O detayı | K003-audio (decode) ile sınır | TASARIM İDDİASI — etiket ≠ ses verisi |
| 5 | Kapak görseli içeriği (embedded artwork dahil) | K015/media (artwork) | v2 §2.15 · media-master §9.3 |
| 6 | Katalog indeksleme / arama sorguları | K015/media (catalog) | v2 §2.15 · media-master §9.4 |
| 7 | Koleksiyonun kümesi (hangi parça var) | K015/media (library) | v2 §2.15 · media-master §9.1 |
| 8 | Kişiselleştirme / öneri üretimi | K006-yapay-zeka / K020-recommendation | media-master §2.2 |

---

## §3 DURUM MAKİNESİ (tasarım iddiası)

| # | Durum | Tetik | Hedef durum | Sınır |
|---|---|---|---|---|
| 1 | NO_TAG | Dosyada hiç etiket yok | FALLBACK (dosya adı) | media-master §9.2 ("etiket eksikse fallback (dosya adı)") |
| 2 | PARTIAL_TAG | Bazı alanlar eksik (ör. yıl/tür) | PARTIAL → enrichment isteği (varsa) | TASARIM İDDİASI |
| 3 | FULL_TAG | Tüm zorunlu alanlar dolu | STORED (K007'ye yaz) | TASARIM İDDİASI |
| 4 | ENRICHING | Dış servis sorgusu açık (K013 üzerinden) | STORED / FALLBACK (servis hatası) | TASARIM İDDİASI |
| 5 | STALE | Dosya değişti (hash farkı) | NO_TAG/PARTIAL/FULL (yeniden okuma) | TASARIM İDDİASI |

> Zorunlu alan seti ⚠️ UNKNOWN (Karar Vault Steward'da).

---

## §4 VERİ AKIŞI (tasarım iddiası)

| # | Akış | Yön | Katıldığı sınır |
|---|---|---|---|
| 1 | Etiket okuma | dosya → reader → metadata | Yerel I/O · TASARIM İDDİASI |
| 2 | Normalizasyon | metadata → normalize (kural seti) | K015-içi · TASARIM İDDİASI |
| 3 | Kalıcılık | metadata → K007-data | Şema K007'de · TASARIM İDDİASI |
| 4 | Zenginleştirme isteği | metadata → K013-integraion → dış servis | K013 sınırı · TASARIM İDDİASI |
| 5 | Zenginleştirme yanıtı | K013 → metadata (STORED veya FALLBACK) | TASARIM İDDİASI |
| 6 | Okuma sorgusu | library/catalog/K012 → metadata | K015-içi + dış oku · TASARIM İDDİASI |

---

## §5 HATA DURUMLARI

| # | Durum | Koşul | Tepki (tasarım iddiası) |
|---|---|---|---|
| 1 | **FALLBACK (dosya adı)** | Etiket eksik/boş | Başlık = dosya adı (uzantısız); sanatçı = "Bilinmeyen" | 
| 2 | **BOZUK_ETİKET** | Etiket bloğu okunamıyor (bozuk header) | Parça yine eklenir, etiket fallback'e düşer; tarama kaydına uyarı |
| 3 | **FORMAT_DESTEKLENMIYOR** | Kapsayıcı etiketlenmemiş / bilinmeyen format | Parça eklenir, tüm alanlar fallback; format ⚠️ UNKNOWN |
| 4 | **SERVIS_HATASI** | Dış servis yanıt vermiyor / rate-limit | Zenginleştirme ertelenir; mevcut veri korunur (fallback'e düşülmez) |
| 5 | **ÇAKIŞAN_ETİKET** | Aynı parçada iki kaynak farklı değer veriyor | Yerel etiket öncelikli (tasarım); karar ⚠️ UNKNOWN |

> Hata kodlarının kesin adları ⚠️ UNKNOWN (Kademe-3/ADR bekliyor).

---

## §6 AÇIK SORULAR

| # | Soru | Etiket |
|---|---|---|
| 1 | Kaynak: metadata yerel dosya etiketlerinden mi, dış servisten mi, ikisi karışık mı? | ⚠️ VERIFICATION REQUIRED (media-master §7/8) |
| 2 | Hangi etiket kapsayıcıları desteklenecek (ID3v2, VorbisComment, MP4 atom vb.)? | ⚠️ UNKNOWN |
| 3 | Dış servis hangi katalog (MusicBrainz, iTunes, Deezer, diğer)? | ⚠️ VERIFICATION REQUIRED (K013) |
| 4 | Kullanıcı düzeltmesi var mı; varsa yerel etiketi ezer mi? | ⚠️ VERIFICATION REQUIRED |
| 5 | Zorunlu alan seti (minimum doluluk) nedir? | ⚠️ UNKNOWN |

---

## §7 KARAR BAĞILIKLARI (bu çocukta Vault Steward kapısı olanlar)

| # | Karar | İlişkili açık soru | Kapı |
|---|---|---|---|
| 1 | Kaynak: yerel etiket mi, dış servis mi, karışık mı | §6/1 | Vault Steward (media-master §7/8) |
| 2 | Kapsayıcı seti (ID3, VorbisComment, MP4…) | §6/2 | Vault Steward |
| 3 | Dış katalog seçimi (MusicBrainz/iTunes/Deezer) | §6/3 | Vault Steward + K013 |
| 4 | Kullanıcı düzeltmesi var mı / öncelik | §6/4 | Vault Steward |
| 5 | Zorunlu alan seti | §6/5 | Vault Steward |

> Bu tablo yalnız mevcut açık soruları derler; yeni iddia eklemez.

---

## §8 REVİZYON

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-12 | Çocuk adı kaydı (v2 §2.15 birebir) | Vault Steward |
| 2 | 2026-10-16 | Kapsam dosyası ilk yazım (§1-§7) — Red Team | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
