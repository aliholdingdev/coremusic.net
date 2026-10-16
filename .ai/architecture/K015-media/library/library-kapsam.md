---
node_id: "K015-media/library/library-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir, S2)"
  - ".ai/architecture/K015-media/media-master.md §9.1 (library derinleştirmesi)"
validation: "Draft — her satır TASARIM İDDİASI etiketli; yalnız v2 §2.15 çocuk adı S2 kanıtlıdır; kapsam dışı yüzeyler media-master §2.2 ile hizalı (2026-10-16)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Koleksiyon verisinin kalıcılık sahibi (K007 mi K015 mi) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K015 · library · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Kapsıyor, Ne Hariç

---

## §1 KAPSAM İÇİ (modellenen)

> Bu bölümdeki her satır **TASARIM İDDİASI**'dır; tek istisna §1a/1'deki çocuk adı (v2 §2.15 — S2 kanıtlıdır).

### §1a Varlıklar

| # | Varlık | Tanım (tasarım iddiası) | Kanıt |
|---|---|---|---|
| 1 | **library (çocuk adı)** | Kitaplık — koleksiyonun kendisi (şarkı/album kümesi) | v2 §2.15 (S2) · media-master §9.1 |
| 2 | **Parça (track)** | Kullanıcı sahipli tek bir ses kaydı; kimliği, yolu, süresi, dosya parmak izi (tasarım) | TASARIM İDDİASI |
| 3 | **Albüm** | Parçaların albüm düzeyinde kümelenmesi; sanatçı + albüm adı anahtarı (tasarım) | TASARIM İDDİASI |
| 4 | **Sanatçı** | Albüm/parça düzeyinde referans; sanatçı adı normalizasyonu (tasarım) | TASARIM İDDİASI |
| 5 | **Playlist** | Kullanıcı oluşturduğu sıralı parça listesi; sahiplik kullanıcıdır | TASARIM İDDİASI |
| 6 | **Klasör kaynağı** | Kitaplığın beslendiği dosya sistemi kökü; çoklu kaynak (tasarım) | TASARIM İDDİASI |
| 7 | **Tarama (scan) kaydı** | Tarama oturumu, bulunan/eklenen/çıkarılan parça sayaçları (tasarım) | TASARIM İDDİASI |

### §1b Yüzeyler

| # | Yüzey | Ne sunar | Sınır |
|---|---|---|---|
| 1 | Listeleme | Parça/albüm/sanatçı listeleri (sayfalı) | K011 gösterir · K015 verir |
| 2 | Filtreleme | Sanatçı/albüm/tür/klasör süzgeçleri | TASARIM İDDİASI |
| 3 | Sıralama | Ad, süre, eklenme tarihi, sıklık anahtarları | TASARIM İDDİASI |
| 4 | Koleksiyon yönetimi | Ekleme/çıkarma, yeniden tarama, eksik dosya tespiti | TASARIM İDDİASI |
| 5 | Playlist CRUD | Oluşturma, sıralama, silme | TASARIM İDDİASI |

### §1c Zaman penceresi

| Dönem | Kapsam (tasarım iddiası) |
|---|---|
| Başlangıç | Boş koleksiyon — EMPTY durumu (§5/1) |
| Tarama sonrası | İlk katalog oluşumu |
| Sürekli | Yeni dosya ekleme, dosya silinmesi, metadata yenileme tetikleri |

---

## §2 KAPSAM DIŞI (bu çocukta DEĞİL)

| # | Konu | Nerede | Kanıt |
|---|---|---|---|
| 1 | Kalıcı veri şeması / ORM / migration | K007-data | media-master §2.2 ("Kalıcı veri şeması/ORM → K007-data") |
| 2 | Dış API / katalog konuşması | K013-integraion | media-master §2.2 ("Dış API/katalog konuşması → K013-integraion") |
| 3 | Ağ taşıması (akış/indirme paketleri) | K014-network | media-master §2.2 |
| 4 | Ses sinyali işleme, DSP, decode | K003-audio / K004-dsp | media-master §2.2 |
| 5 | Çalma durumu / orkestrasyonu | K012-services (playback-service) | media-master §7/1 (sahiplik belirsiz) |
| 6 | Şarkı bilgilerinin etiket içeriği (sanatçı/tür/süre okuma) | K015/media (metadata) | v2 §2.15 · media-master §9.2 |
| 7 | Kapak resmi indirme/önbelleği | K015/media (artwork) | v2 §2.15 · media-master §9.3 |
| 8 | Tam metin arama indeksi | K015/media (catalog) | v2 §2.15 · media-master §9.4 |
| 9 | Uygulama akışı / arayüz | K011-application | media-master §2.2 |

---

## §3 DURUM MAKİNESİ (tasarım iddiası)

| # | Durum | Tetik | Hedef durum | Sınır |
|---|---|---|---|---|
| 1 | EMPTY | Koleksiyon boş (ilk açılış / tüm parçalar silindi) | SCANNING (kullanıcı kaynağı gösterince) | K011 EMPTY gösterir · TASARIM İDDİASI |
| 2 | SCANNING | Tarama başlatma | READY (başarılı) / DEGRADED (hata) | TASARIM İDDİASI |
| 3 | READY | Katalog var ve dosyalar erişilebilir | DEGRADED (dosya kaybı tespiti) | TASARIM İDDİASI |
| 4 | DEGRADED | Bazı dosyalar eksik/okunamıyor (missing-files) | READY (yeniden tarama ile) | TASARIM İDDİASI |
| 5 | REINDEX_PENDING | metadata/catalog tarafında değişiklik bildirimi | READY (indeks yenilendi) | K015-içi el sıkışma · TASARIM İDDİASI |

> Durum makinesi media-master §10.2'deki domain geneli şemaya bağımlıdır; bu tablo library gözündür.

---

## §4 VERİ AKIŞI (tasarım iddiası)

| # | Akış | Yön | Katıldığı sınır |
|---|---|---|---|
| 1 | Dosya sistemi taraması | filesystem → library | Yerel I/O (K014 değil — tarama) · TASARIM İDDİASI |
| 2 | Tarama sonucu kaydı | library → K007-data (kalıcılık) | Şema K007'de · TASARIM İDDİASI |
| 3 | Etiket okuma tetiği | library → metadata (parça başına) | K015-içi · TASARIM İDDİASI |
| 4 | Liste/filtre sorgusu | K011/K012 → library | K015 dış oku · TASARIM İDDİASI |
| 5 | Eksik dosya raporu | library → K011 (görsel uyarı) | TASARIM İDDİASI |
| 6 | Katalog indeks besleme | library → catalog | K015-içi · TASARIM İDDİASI |

---

## §5 HATA DURUMLARI

| # | Durum | Koşul | Tepki (tasarım iddiası) |
|---|---|---|---|
| 1 | **EMPTY** | Koleksiyonda parça yok | K011 boş durum ekranı gösterir; K015 hata üretmez | 
| 2 | **MISSING_FILE** | Kayıt var ama dosya diskte yok | Kayıt "eksik" işaretlenir, çalma dışı bırakılır (media-master §9.1 + Navidrome Missing Files fikri) |
| 3 | **UNREADABLE_FILE** | Dosya okunamıyor (izin/bozuk) | Tarama kaydına hata sayacı; parça atlanır |
| 4 | **PERMISSION_DENIED** | Klasör kaynağına erişim yok | Tarama durdurulur, kullanıcıya bildirim |
| 5 | **STALE_SCAN** | Tarama sırasında dosya değişti | Yeniden tarama kuyruğa alınır |

> Hata kodlarının kesin adları ve numeric değerleri ⚠️ UNKNOWN (Kademe-3/ADR bekliyor).

---

## §6 AÇIK SORULAR

| # | Soru | Etiket |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı (library'nin altındaki module sınırı) nedir? | ⚠️ VERIFICATION REQUIRED (faz C) |
| 2 | Koleksiyon verisinin kalıcılık sahibi K007 mi, K015 mi (şema K007'de ama veri sahipliği K015 mi)? | ⚠️ VERIFICATION REQUIRED (media-master §7/8 ile ilgili) |
| 3 | Tarama motoru yerel process mi, servis (K012) mi çağırır? | ⚠️ VERIFICATION REQUIRED |
| 4 | Çoklu kitaplık (multi-library) desteği var mı? | ⚠️ VERIFICATION REQUIRED |
| 5 | Dosya parmak izi (hash/fingerprint) hangi algoritmayla alınır? | ⚠️ UNKNOWN |

---

## §7 KARAR BAĞILIKLARI (bu çocukta Vault Steward kapısı olanlar)

| # | Karar | İlişkili açık soru | Kapı |
|---|---|---|---|
| 1 | Kalıcılık sahibi (K007 vs K015 veri sahipliği) | §6/2 | Vault Steward (media-master §7/8) |
| 2 | Tarama motoru sahibi (yerel vs K012) | §6/3 | Vault Steward |
| 3 | Çoklu kitaplık desteği | §6/4 | Vault Steward |
| 4 | Hash algoritması | §6/5 | Vault Steward (K007 ile ortak) |

> Bu tablo yalnız mevcut açık soruları derler; yeni iddia eklemez.

---

## §8 REVİZYON

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-12 | Çocuk adı kaydı (v2 §2.15 birebir) | Vault Steward |
| 2 | 2026-10-16 | Kapsam dosyası ilk yazım (§1-§7) — Red Team | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
