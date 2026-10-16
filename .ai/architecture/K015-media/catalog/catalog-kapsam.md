---
node_id: "K015-media/catalog/catalog-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir, S2)"
  - ".ai/architecture/K015-media/media-master.md §9.4 (catalog derinleştirmesi)"
validation: "Draft — her satır TASARIM İDDİASI etiketli; yalnız v2 §2.15 çocuk adı S2 kanıtlıdır; arama yöntemi media-master UNKNOWN listesiyle korunur (2026-10-16)"
open_questions:
  - "catalog ↔ K012/music-service sınırı → ⚠️ VERIFICATION REQUIRED (media-master §7/1)"
  - "Arama yöntemi (tam metin motoru seçimi) → ⚠️ UNKNOWN"
group: mimari
---

# K015 · catalog · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Kapsıyor, Ne Hariç

---

## §1 KAPSAM İÇİ (modellenen)

> Bu bölümdeki her satır **TASARIM İDDİASI**'dır; tek istisna §1a/1'deki çocuk adı (v2 §2.15 — S2 kanıtlıdır).

### §1a Varlıklar

| # | Varlık | Tanım (tasarım iddiası) | Kanıt |
|---|---|---|---|
| 1 | **catalog (çocuk adı)** | Dizin/liste verisi — tüm koleksiyonun indekslenmiş hali | v2 §2.15 (S2) · media-master §9.4 |
| 2 | **Arama indeksi** | Parça/albüm/sanatçı alanlarından oluşturulan sorgulanabilir yapı | TASARIM İDDİASI |
| 3 | **Sorgu (query)** | Kullanıcının arama ifadesi + filtreleri + sıralaması | TASARIM İDDİASI |
| 4 | **Sonuç kümesi** | Sayfalanmış arama sonucu (puan/sıra dahil) | TASARIM İDDİASI |
| 5 | **İndeks güncelleme olayı** | library/metadata değişikliğinin indekse bildirimi | TASARIM İDDİASI |
| 6 | **Sonuç istatistiği** | Toplam eşleşme sayısı (sayfalama için) | TASARIM İDDİASI |

### §1b Yüzeyler

| # | Yüzey | Ne sunar | Sınır |
|---|---|---|---|
| 1 | Tam metin arama | Serbest metin sorgusu (parça/albüm/sanatçı) | TASARIM İDDİASI |
| 2 | Alan bazlı arama | Belirli alanda filtre (sanatçı = X) | TASARIM İDDİASI |
| 3 | Öneri (type-ahead) | Kısmi ifadeyle hızlı sonuç (isteğe bağlı) | TASARIM İDDİASI |
| 4 | İndeks yönetimi | İndeks oluşturma/yenileme (library taraması tetikler) | K015-içi · TASARIM İDDİASI |
| 5 | Sıralama | Puan / alfababetik / sonuç kalitesi | TASARIM İDDİASI |

### §1c Zaman penceresi

| Dönem | Kapsam (tasarım iddiası) |
|---|---|
| İlk tarama | İndeks oluşturma (tüm koleksiyon) |
| Sürekli | Eklenen/çıkan parça + metadata değişikliği → kısmi güncelleme |
| İstek anında | Sorgu çalıştırma + sayfalama |

---

## §2 KAPSAM DIŞI (bu çocukta DEĞİL)

| # | Konu | Nerede | Kanıt |
|---|---|---|---|
| 1 | Kalıcı veri şeması / ORM / migration | K007-data | media-master §2.2 |
| 2 | Dış servis konuşması (dış katalog eşitlemesi) | K013-integraion | media-master §2.2 |
| 3 | Ağ taşıması | K014-network | media-master §2.2 |
| 4 | Müzik servisi mantığı / çalma orkestrasyonu | K012-services (music-service) | media-master §7/1 (sınır belirsiz) |
| 5 | Arayüz / uygulama akışı | K011-application | media-master §2.2 |
| 6 | Öneri/kişiselleştirme sıralaması | K006-yapay-zeka / K020-recommendation | media-master §2.2 |
| 7 | Koleksiyonun kendisi (hangi parça var) | K015/media (library) | v2 §2.15 · media-master §9.1 |
| 8 | Etiket içeriği okuma | K015/media (metadata) | v2 §2.15 · media-master §9.2 |

---

## §3 DURUM MAKİNESİ (tasarım iddiası)

| # | Durum | Tetik | Hedef durum | Sınır |
|---|---|---|---|---|
| 1 | NO_INDEX | İndeks yok (ilk kurulum) | BUILDING | TASARIM İDDİASI |
| 2 | BUILDING | İlk indeksleme (tüm koleksiyon) | READY / DEGRADED (hata) | TASARIM İDDİASI |
| 3 | READY | İndeks güncel ve sorgulanabilir | SYNC_PENDING (değişiklik bildirimi) | TASARIM İDDİASI |
| 4 | SYNC_PENDING | library/metadata değişikliği geldi | READY (kısmi güncelleme sonrası) | TASARIM İDDİASI |
| 5 | DEGRADED | İndeks bozuldu / disk hatası | BUILDING (tam yeniden) | TASARIM İDDİASI |
| 6 | QUERYING | Sorgu açık | READY (yanıt döndü) | TASARIM İDDİASI |

> Durum adları ⚠️ UNKNOWN (Kademe-3/ADR bekliyor); bu tablo tasarım iskeletidir.

---

## §4 VERİ AKIŞI (tasarım iddiası)

| # | Akış | Yön | Katıldığı sınır |
|---|---|---|---|
| 1 | İndeks besleme | library/metadata → catalog | K015-içi · TASARIM İDDİASI |
| 2 | İndeks depolama | catalog → K007-data (veya ayrı indeks yapısı — ⚠️ UNKNOWN) | Şema K007'de · TASARIM İDDİASI |
| 3 | Sorgu | K011/K012 → catalog | Dış oku · TASARIM İDDİASI |
| 4 | Sonuç | catalog → K011/K012 (sayfalı) | TASARIM İDDİASI |
| 5 | Değişiklik bildirimi | library → catalog (ekleme/silme olayı) | K015-içi · TASARIM İDDİASI |
| 6 | Dış katalog eşitleme | (varsa) K013 → catalog | K013 sınırı · TASARIM İDDİASI |

---

## §5 HATA DURUMLARI

| # | Durum | Koşul | Tepki (tasarım iddiası) |
|---|---|---|---|
| 1 | **EMPTY_INDEX** | İndeks boş (koleksiyon boş) | Boş sonuç + "koleksiyon boş" bilgisi (library EMPTY ile el sıkışır) |
| 2 | **NO_MATCH** | Sorgu eşleşme bulamadı | Boş sonuç listesi (hata değil) |
| 3 | **INDEKS_BOZUK** | İndeks okunamıyor | DEGRADED → tam yeniden indeksleme |
| 4 | **SORGU_HATASI** | Geçersiz sorgu ifadesi (syntax) | Hata döner, indeks bozulmaz |
| 5 | **GUNCELLEME_GECIKMESI** | SYNC_PENDING çok uzun süre bekliyor | Sorgu eskimiş veriyle çalışır; uyarı kaydı |

> Hata kodlarının kesin adları ⚠️ UNKNOWN (Kademe-3/ADR bekliyor).

---

## §6 AÇIK SORULAR

| # | Soru | Etiket |
|---|---|---|
| 1 | catalog ↔ K012/music-service sınırı: arama motoru kimin? | ⚠️ VERIFICATION REQUIRED (media-master §7/1) |
| 2 | Arama yöntemi: yerel tam metin mi, harici arama motoru mu? | ⚠️ UNKNOWN (media-master §12 — "Arama yöntemi ⚠️ UNKNOWN") |
| 3 | İndeks nerede tutulur: K007 şeması içinde mi, ayrı yapı mı? | ⚠️ UNKNOWN |
| 4 | Öneri (type-ahead) özelliği kapsamda mı, K020'ye mi ait? | ⚠️ VERIFICATION REQUIRED |
| 5 | Dış katalog eşitlemesi catalog'u etkiliyor mu (K013 kanalı)? | ⚠️ VERIFICATION REQUIRED |

---

## §7 KARAR BAĞILIKLARI (bu çocukta Vault Steward kapısı olanlar)

| # | Karar | İlişkili açık soru | Kapı |
|---|---|---|---|
| 1 | catalog ↔ K012/music-service sınırı | §6/1 | Vault Steward (media-master §7/1) |
| 2 | Arama yöntemi (yerel FULLTEXT vs harici motor) | §6/2 | Vault Steward (media-master §12) |
| 3 | İndeks depolama yeri | §6/3 | Vault Steward + K007 |
| 4 | Öneri kapsam sınırı (K020) | §6/4 | Vault Steward |
| 5 | Dış katalog eşitleme etkisi | §6/5 | Vault Steward + K013 |

> Bu tablo yalnız mevcut açık soruları derler; yeni iddia eklemez.

---

## §8 REVİZYON

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-12 | Çocuk adı kaydı (v2 §2.15 birebir) | Vault Steward |
| 2 | 2026-10-16 | Kapsam dosyası ilk yazım (§1-§7) — Red Team | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
