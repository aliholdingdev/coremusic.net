---
type: architecture
category: data
title: "Data Architecture — CoreMusic Veri Mimarisi"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Data Architecture — CoreMusic Veri Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] · [[decisions/accepted/ADR-003-multi-db-9-databases]] · [[decisions/accepted/ADR-040-database-authority]] · [[decisions/accepted/ADR-050-multi-db-sync-strategy]]

**Tek Kaynak:** [[architecture/00-overview/architecture-master]] §3

---

## 1. Amaç

CoreMusic'in 18 BCNF veritabanının detaylı şema yapısı, normalizasyon kuralları ve veri yönetimi stratejileri.

---

## 2. 18 BCNF Veritabanı

| # | Veritabanı | Amaç | Şema |
|---|------------|------|------|
| 1 | coremusic_auth | Kullanıcılar, roller, session, token, credential vault, API key | `.sql/mysql/coremusic_auth.sql` |
| 2 | coremusic_user | Profiller, tercihler, geçmiş, favoriler | `.sql/mysql/coremusic_user.sql` |
| 3 | coremusic_musics | Şarkılar, sanatçılar, türler, sözler, dosyalar, podcast, video, radyo | `.sql/mysql/coremusic_musics.sql` |
| 4 | coremusic_albums | Albüm koleksiyonları, diskler, istatistikler | `.sql/mysql/coremusic_albums.sql` |
| 5 | coremusic_playlist | Kullanıcı ve AI çalma listeleri, işbirlikçiler, takipçiler | `.sql/mysql/coremusic_playlist.sql` |
| 6 | coremusic_catalog | Referans verileri (tür listesi, sanatçı rolleri, enstrümanlar, ruh halleri) | `.sql/mysql/coremusic_catalog.sql` |
| 7 | coremusic_logs | Audit trail, analitik, hata logları, performans metrikleri | `.sql/mysql/coremusic_logs.sql` |
| 8 | coremusic_media | Cihaz senkronizasyonu, medya metadata, erişim kontrolü | `.sql/mysql/coremusic_media.sql` |
| 9 | coremusic_system | Ayarlar, config, cache, EQ, dosya yöneticisi, bildirimler, i18n | `.sql/mysql/coremusic_system.sql` |
| 10 | coremusic_social | Yorumlar, paylaşımlar, aktivite, dinleme odaları, bildirimler | `.sql/mysql/coremusic_social.sql` |
| 11 | coremusic_wireless | WiFi + Bluetooth ağları | `.sql/mysql/coremusic_wireless.sql` |
| 12 | coremusic_ai | Kullanıcı tercih profilleri, dinleme özellikleri, öneriler | `.sql/mysql/coremusic_ai.sql` |
| 13 | coremusic_api | API anahtarları, rate limit, API çağrı logları, webhook'lar | `.sql/mysql/coremusic_api.sql` |
| 14 | coremusic_cms | Sayfalar, blog, etiketler, medya varlıkları, SSS, banner'lar | `.sql/mysql/coremusic_cms.sql` |
| 15 | coremusic_download | İndirme kuyruğu, geçmiş, önbellek, kaynak API'leri | `.sql/mysql/coremusic_download.sql` |
| 16 | coremusic_neva | EQ preset'leri, DSP ayarları, yönlendirme matrisi, spektrum analizi | `.sql/mysql/coremusic_neva.sql` |
| 17 | coremusic_studio | Stüdyo oturumları, parçalar, preset'ler, ekipman | `.sql/mysql/coremusic_studio.sql` |
| 18 | coremusic_patch | Şema sürümleri, migration logları, yamalar | `.sql/mysql/coremusic_patch.sql` |
| | **TOPLAM** | | **156 tablo** |

---

## 3. BCNF Kuralları

| Kural | Açıklama |
|-------|----------|
| ORM yasak | Sadece PDO prepared (ADR-002) |
| SELECT * yasak | Açık sütun listesi |
| BCNF zorunlu | 1NF → 2NF → 3NF → BCNF |
| Soft delete | `is_deleted = 0` |
| Snake_case | Tablo/sütun adları |

---

## 4. Migration Stratejisi (ADR-014)

| Özellik | Değer |
|---------|-------|
| Yöntem | Forward-only, versioned |
| Dosya | `YYYYMMDD_HHMMSS_description.sql` |
| Geri alma | ❌ Yasak |

---

## 5. Cross References

| Dosya | Amaç |
|-------|------|
| [[architecture/l0-infrastructure]] | DB detay |
| [[decisions/accepted/ADR-040-database-authority]] | DB otoritesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode