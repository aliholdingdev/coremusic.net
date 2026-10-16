---
node_id: "K015-media/artwork/artwork-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir, S2)"
  - ".ai/architecture/K015-media/media-master.md §9.3 (artwork derinleştirmesi)"
validation: "Draft — her satır TASARIM İDDİASI etiketli; yalnız v2 §2.15 çocuk adı S2 kanıtlıdır; boyut/format politikası media-master §12/7 UNKNOWN ile korunur (2026-10-16)"
open_questions:
  - "Boyut/format politikası (varyant ölçüleri, kabul formatları) → ⚠️ UNKNOWN (media-master §12/7)"
  - "Kaynak: yerel/embedded mi dış servis mi → ⚠️ VERIFICATION REQUIRED (§7/8)"
group: mimari
---

# K015 · artwork · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Kapsıyor, Ne Hariç

---

## §1 KAPSAM İÇİ (modellenen)

> Bu bölümdeki her satır **TASARIM İDDİASI**'dır; tek istisna §1a/1'deki çocuk adı (v2 §2.15 — S2 kanıtlıdır).

### §1a Varlıklar

| # | Varlık | Tanım (tasarım iddiası) | Kanıt |
|---|---|---|---|
| 1 | **artwork (çocuk adı)** | Kapak resimleri — indirme/önbellek ilişkili | v2 §2.15 (S2) · media-master §9.3 |
| 2 | **Kapak görseli** | Albüm/parça düzeyinde tek görsel; orijinal + varyantları | TASARIM İDDİASI |
| 3 | **Varyant** | Önceden tanımlanmış boyutlarda türetilmiş kopya (thumbnail/medium/large — ölçüler ⚠️ UNKNOWN) | TASARIM İDDİASI · media-master §9.3 |
| 4 | **Görsel önbelleği** | Varyant dosyalarının diske/önbelleğe alınması (media-cache ile kesişim) | media-master §9.9 |
| 5 | **Placeholder** | Kapak yoksa gösterilen varsayılan görsel | TASARIM İDDİASI |
| 6 | **Kaynak bildirimi** | Görselin nereden geldiği kaydı: embedded · dosya (cover.jpg/folder.jpg) · dış servis · placeholder | TASARIM İDDİASI |

### §1b Yüzeyler

| # | Yüzey | Ne sunar | Sınır |
|---|---|---|---|
| 1 | Görsel sunum | Parça/albüm için kapak URL'si (varyant seçimiyle) | K011 kullanır · TASARIM İDDİASI |
| 2 | Varyant üretme | Orijinalden boyutlu türetme | TASARIM İDDİASI |
| 3 | Dış kaynak getirme | Eksik kapağın dış servisten indirilmesi | K013 üzerinden · TASARIM İDDİASI |
| 4 | Önbellek yönetimi | Varyantların saklanması, geçerlilik | media-cache (§9.9) · TASARIM İDDİASI |
| 5 | Yenileme | Kapsayıcıda kapak değişince yeniden üretme | TASARIM İDDİASI |

### §1c Zaman penceresi

| Dönem | Kapsam (tasarım iddiası) |
|---|---|
| Tarama anında | Embedded/kapsayıcı yanındaki görselleri tespit |
| İstek anında | İlk görsel talebi — varyant yoksa üretme |
| Sürekli | Dış servis zenginleştirme (varsa) + önbellek geçerlilik |

---

## §2 KAPSAM DIŞI (bu çocukta DEĞİL)

| # | Konu | Nerede | Kanıt |
|---|---|---|---|
| 1 | Kalıcı veri şeması / ORM | K007-data | media-master §2.2 |
| 2 | Dış servis konuşmasının taşıması (rate-limit, retry, auth) | K013-integraion | media-master §2.2 · §7/8 |
| 3 | Ağ taşıması (indirme paketleri) | K014-network | media-master §2.2 |
| 4 | Önbellek boyut/TTL/temizlik politikası | K015/media (media-cache) | media-master §9.9 · §12 |
| 5 | Parça metadata içeriği (etiket okuma) | K015/media (metadata) | v2 §2.15 · media-master §9.2 |
| 6 | Koleksiyonun kümesi | K015/media (library) | v2 §2.15 · media-master §9.1 |
| 7 | Görsel işleme/DSP | K003-audio / K004-dsp | media-master §2.2 |

---

## §3 DURUM MAKİNESİ (tasarım iddiası)

| # | Durum | Tetik | Hedef durum | Sınır |
|---|---|---|---|---|
| 1 | NO_COVER | Kapsayıcıda/embedded görsel yok + dış kaynak yok | PLACEHOLDER | media-master §9.3 ("kapak yoksa placeholder") |
| 2 | EMBEDDED_FOUND | Embedded görsel bulundu | EXTRACT → VARYANT (isteğe bağlı) | TASARIM İDDİASI |
| 3 | SIDECAR_FOUND | Klasörde yan görsel bulundu (ör. cover.jpg — ad listesi ⚠️ UNKNOWN) | EXTRACT → VARYANT | TASARIM İDDİASI |
| 4 | FETCHING | Dış servisten getirme açık (K013 üzerinden) | EXTRACT / NO_COVER (hata) | TASARIM İDDİASI |
| 5 | VARYANT_READY | İstenen boyut mevcut | SUN (TTL dahil) | TASARIM İDDİASI |
| 6 | STALE | Orijinal değişti / önbellek TTL doldu | VARYANT (yeniden üretim) | TASARIM İDDİASI |

> Öncelik zinciri (embedded mi önce, yan dosya mı önce, dış servis mi önce) ⚠️ UNKNOWN — karar Vault Steward'da.

---

## §4 VERİ AKIŞI (tasarım iddiası)

| # | Akış | Yön | Katıldığı sınır |
|---|---|---|---|
| 1 | Embedded okuma | dosya → artwork | Yerel I/O · TASARIM İDDİASI |
| 2 | Yan görsel tarama | klasör → artwork | Yerel I/O · TASARIM İDDİASI |
| 3 | Varyant üretimi | orijinal → varyantlar → önbellek | media-cache · TASARIM İDDİASI |
| 4 | Dış getirme | artwork → K013 → dış servis → artwork | K013 sınırı · TASARIM İDDİASI |
| 5 | Görsel sunum | K011 ← artwork (varyant URL) | TASARIM İDDİASI |
| 6 | Kaynak kaydı | artwork → K007-data (görsel kaydı) | Şema K007'de · TASARIM İDDİASI |

---

## §5 HATA DURUMLARI

| # | Durum | Koşul | Tepki (tasarım iddiası) |
|---|---|---|---|
| 1 | **PLACEHOLDER** | Hiçbir kaynakta kapak yok | Varsayılan görsel sunulur; istek PLACEHOLDER olarak işaretlenir |
| 2 | **BOZUK_GORSEL** | Görsel dosyası okunamıyor / geçersiz format | Görsel atlanır, sıradaki kaynak denenir, yoksa PLACEHOLDER |
| 3 | **VARYANT_URETILEMEDI** | Boyutlandırma başarısız | Orijinal sunulur (varsa) veya PLACEHOLDER |
| 4 | **SERVIS_HATASI** | Dış servis hatası / rate-limit (429/503) | Getirme ertelenir; PLACEHOLDER korunur |
| 5 | **OZ_KAYNAK_YOK** | İstenen varyant üretilemez ve disk dolu / yazma hatası | Orijinal/PLACEHOLDER'a düşülür |

> Varyant ölçüleri ve kabul formatları ⚠️ UNKNOWN (media-master §12/7 — karar Vault Steward'da).

---

## §6 AÇIK SORULAR

| # | Soru | Etiket |
|---|---|---|
| 1 | Varyant boyutları (thumbnail/medium/large piksel ölçüleri) nedir? | ⚠️ UNKNOWN (§12/7) |
| 2 | Kabul formatları neler (JPEG/PNG/WebP)? | ⚠️ UNKNOWN |
| 3 | Kaynak öncelik sırası: embedded → yan dosya → dış servis mi, tersi mi? | ⚠️ VERIFICATION REQUIRED (§7/8) |
| 4 | Dış servis hangisi (MusicBrainz Cover Art Archive, iTunes, Deezer, diğer)? | ⚠️ VERIFICATION REQUIRED (K013) |
| 5 | Önbellek TTL ve maksimum görsel depolama bütçesi nedir? | ⚠️ UNKNOWN (media-cache §9.9) |

---

## §7 KARAR BAĞILIKLARI (bu çocukta Vault Steward kapısı olanlar)

| # | Karar | İlişkili açık soru | Kapı |
|---|---|---|---|
| 1 | Varyant boyut seti | §6/1 | Vault Steward (media-master §12/7) |
| 2 | Kabul formatları (JPEG/PNG/WebP) | §6/2 | Vault Steward |
| 3 | Kaynak öncelik sırası (embedded/sidecar/external) | §6/3 | Vault Steward |
| 4 | Dış servis seçimi | §6/4 | Vault Steward + K013 |
| 5 | Önbellek TTL/bütçe | §6/5 | Vault Steward + media-cache |

> Bu tablo yalnız mevcut açık soruları derler; yeni iddia eklemez. Web-arastirma §3a varyant referans verisi **girdi**tir, karar DEĞİL.

---

## §8 REVİZYON

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-12 | Çocuk adı kaydı (v2 §2.15 birebir) | Vault Steward |
| 2 | 2026-10-16 | Kapsam dosyası ilk yazım (§1-§7) — Red Team | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
