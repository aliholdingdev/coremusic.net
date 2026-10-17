---
node_id: K014-media/offline
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
  - "Sahiplik: K015 mi K014 mü K012 mi → ⚠️ VERIFICATION REQUIRED (§7/2)"
group: mimari
---


# offline — K014-media (kademe-2 · subdomain)

**ELI15:** Çevrimdışı kullanılabilirlik: ne hazır, ne bekliyor, ne silinmeli.

**Sınır (ne DEĞİL):** Bağlantı kopması/kuyruk K014/offline-sync ile sınır belirsiz.

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
| 2 | Sahiplik: K015 mi K014 mü K012 mi → ⚠️ VERIFICATION REQUIRED (§7/2) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

## §ROL (ELI15)

Offline, bir parçanın çevrimdışı kullanılabilirliğinin işaretlenmesi ve korunması işidir: ne hazır, ne bekliyor, ne silinmeli (master §9.8 — v2 §2.15 S2). Her parça için `is_offline_ready` kontrolü yapılır: dosya yerelde var mı ve içerik hash'i (ETag/SHA-256) kayıtlı değerle eşleşiyor mu (master §22.5). Depolama kotası aşıldığında sırayla parça silinir (eviction); dayanak LRU/TTL/ARC stratejileridir (master §22.5 — strateji adları doğrulanmış, eşikler ⚠️ UNKNOWN, Vault Steward §16/3 kararı bekliyor). Parça çevrimdışı hazır değilse sistem akışa nazikçe düşer — dinleme kesintiye uğramaz (master §9.6 fallback TASARIM İDDİASI). Bu çocuğun sahiplik sınırı belirsizdir: bağlantı kopması/kuyruk K014/offline-sync ve K012-services ile paylaşımı §7/2'de ⚠️ VERIFICATION REQUIRED olarak açıktır.

## §KAPSAM

### Kapsam içinde (bu çocuğun malı)

| # | Konu | Kaynak |
|---|---|---|
| 1 | Çevrimdışı kullanılabilirlik: indirilen/önbelleğe alınan parçalar | master §9.8 · v2 §2.15 (S2) |
| 2 | Parça bazında `is_offline_ready` kontrolü (dosya + hash) | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) · dayanak master §22.5 |
| 3 | Depolama kotası + eviction (LRU / TTL / ARC) | master §22.5 (stratejiler doğrulanmış) · eşik ⚠️ UNKNOWN (§16/3) |
| 4 | Durum işaretleme: hazır / bekliyor / eksik | TASARIM ÖNERİSİ (`ornekler.md` §2-§16) · master §9.8 ELI15 |
| 5 | Çevrimdışı yoksa streaming'e nazik fallback | master §9.6 (TASARIM İDDİASI) |
| 6 | `Cache-Control: immutable` + ETag ile yerel içerik doğrulama | master §22.5 (doğrulanmış) |
| 7 | Süresi dolan içerik temizliği (TTL) | master §22.5 (doğrulanmış strateji) · süre kararı §16/3 |

### Kapsam dışında (kimin?)

| # | Konu | Ait olduğu düğüm | Kaynak |
|---|---|---|---|
| 1 | İndirme kendisi (dosyanın alınması) | K014-media/downloads | master §9.7 · §14.3 |
| 2 | Bağlantı kopması / çevrimdışı kuyruk | K014/offline-sync · K012-services | master §7/2 → ⚠️ §A/2 |
| 3 | Medya önbelleği (boyut/TTL sahibi) | K014-media/media-cache | master §9.9 · §12/3 |
| 4 | Ağ taşıması ayrıntısı | K010-network | master §2.2 |
| 5 | Kalıcı veri şeması/ORM (durum kayıtları) | K007-data | master §2.2 |
| 6 | Uygulama akışı/arayüz (çevrimdışı rozeti) | K011-application | master §2.2 |
| 7 | Medya verisi eşitlemesi (cihazlar arası) | K014-media/synchronization | master §9.10 · §7/3 |
| 8 | Sahiplik: bu çocuk kimin? (K015 / K014 / K012) | Vault Steward (§7/2) | master §9.8 → ⚠️ VERIFICATION REQUIRED |

## §BİLEŞENLER

| # | Bileşen | Sorumluluk | Etiket |
|---|---|---|---|
| 1 | Offline readiness işaretleyici | parça başına `is_offline_ready` = dosya var + hash eşleşiyor | TASARIM ÖNERİSİ (`ornekler.md`) |
| 2 | Kota denetleyici | toplam çevrimdışı bayt → kota karşılaştırması | TASARIM ÖNERİSİ — eşik ⚠️ UNKNOWN (master §16/3) |
| 3 | Eviction politikası | kota aşılırsa sırayla silme: LRU / TTL / ARC (scan-resistant) | TASARIM ÖNERİSİ (strateji adları doğrulanmış: master §22.5) |
| 4 | Fallback yönlendirici | parça hazır değilse streaming'e nazik düşme | TASARIM İDDİASI (master §9.6) |
| 5 | Durum listesi | hazır · bekliyor · eksik (READY / PENDING / MISSING) | TASARIM ÖNERİSİ (master §9.8 ELI15) |
| 6 | Sınır kapısı | K014/offline-sync · K012 offline kuyruğu ile iletişim | ⚠️ VERIFICATION REQUIRED (master §9.8 · §7/2) |
| 7 | Kalıcılık seam'i | durum kayıtları (path/size/hash/last_access) → K007 | TASARIM İDDİASI (master §2.2) |

## §STANDARTLAR

| # | Standart/fark | Ne | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | `Cache-Control: public, max-age=31536000, immutable` | yerel/indirilmiş içeriğin önbellek başlığı | master §22.5 | ✅ doğrulanmış |
| 2 | ETag | içerik hash — `is_offline_ready` doğrulamasının dayanağı | master §22.5 | ✅ doğrulanmış |
| 3 | ARC (scan-resistant) | taramayla kirletilmeyen önbellek — katalog/parça için tercih | master §22.5 | ✅ doğrulanmış (kullanım TASARIM ÖNERİSİ) |
| 4 | LRU | genel önbellek — audio stream'de isabet oranı ≈ 0 | master §22.5 | ✅ doğrulanmış |
| 5 | LFU | sık dinlenenler için strateji | master §22.5 | ✅ doğrulanmış |
| 6 | TTL | zamanlayıcı temizlik (süresi dolan içerik) | master §22.5 | ✅ doğrulanmış |
| 7 | HTTP Range (RFC 9110) | eksik yerel parçanın tamamlanması/devamı | master §22.2 | ✅ doğrulanmış |

### §STANDARTLAR — TASARIM ÖNERİLERİ (Vault Steward onayı ister)

| # | Öneri | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Eviction = ARC (scan-resistant) + `immutable`/ETag | tarama dayanıklı; katalog/parça önbelleği için en uygun | master §22.5 TASARIM ÖNERİSİ (eşik onayı §16/3) |
| 2 | `is_offline_ready` = dosya mevcut + ETag/SHA-256 eşitliği | bütünlük kanıtı olmadan "hazır" sayılmaz | master §22.5 · `ornekler.md` §2-§16 |
| 3 | Fallback = parça çevrimdışı yoksa streaming | dinleme kesintisiz kalır | master §9.6 (TASARIM İDDİASI) |
| 4 | Kota/TTL eşikleri sabitlenmeden eviction kalıcılaştırılmaz | §16/3 açık | master §16/3 → Vault Steward kararı |
| 5 | Sahiplik (K015 / K014 / K012) çözülmeden kuyruk entegrasyonu yapılmaz | §7/2 açık | master §7/2 → ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
