---
node_id: "K008-library-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-library-kapsam"
  - "K008-library-ornekler"
evidence:
  - ".ai/.sql/mysql/coremusic_user.sql satır 35–57 · 59–69 · 83–102 (3 tablo)"
  - "2026-10-15 grep: repo PHP/JS `favorite|user_favorites` = 0 isabet → mevcut kod bileşeni YOK"
  - "shared/src/Events/Domain/ — 9 domain event, favori/kütüphane olayı YOK (bkz [[library-dogrulama]] D-L7)"
  - "domain-master.md §3.1 satır 90 — LibraryEntry (tasarım iddiası, ⚠️ ADR-097 K4)"
validation: "2026-10-15 — §1 mevcut kademe (yalnız şema) + §2 altı PLANNED satır; uydurma sınıf/olay adı yok"
open_questions:
  - "LibraryEntry aggregate + invariant listesi → ADR-097 K4'te kilitlenmedi"
  - "Favori servisi/ucu → kanıt YOK → UNKNOWN (D-L11)"
  - "Kuyruk (queue) mülkiyeti → SINIR AÇIK (D-L12 · bkz [[library-kapsam]] §2 satır 5)"
group: mimari
---

# library — Bileşenler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Bu dalda **mevcut kod bileşeni yok** (2026-10-15 grep 0 isabet). §1 yalnız şema
> varlıklarını, §2 ise **PLANNED** kademe-3 planını taşır. Disk'te karşılığı olmayan
> sınıf/dosya/olay adı "var" gibi sunulmaz.

## §1 MEVCUT KADEME (yalnız şema — kod YOK)

| # | Varlık | Tür | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `user_favorites` | SQL tablo (polymorphic unique) | `.ai/.sql/mysql/coremusic_user.sql` satır 59–69 | ✅ DISKTE |
| 2 | `user_downloads` | SQL tablo (durum · format · expiry) | aynı dosya satır 35–57 | ✅ DISKTE |
| 3 | `user_listening_history` | SQL tablo (süre · yüzde · cihaz) | aynı dosya satır 83–102 | ✅ DISKTE |
| 4 | Favori toggle servisi / UI kodu | PHP / JS | **YOK** — grep 0 isabet ([[library-dogrulama]] D-L6) | ❌ YOK |
| 5 | Favori/kütüphane domain event'i | `shared/src/Events/Domain/` | **YOK** — 9 dosyada yok (D-L7) | ❌ YOK |
| 6 | Dinleme geçmişi / indirme uygulama kodu | PHP / JS | tarama yapılmadı | ⚠️ VERIFICATION REQUIRED |

## §2 KADEME-3 PLANI (PLANNED — ADR-097 K4 bekliyor)

| # | Bileşen (aday) | Sorumluluk | Dayanak (şema/vault) | Durum |
|---|---|---|---|---|
| 1 | Kütüphane deposu (Repository — ad ADR-097 K4) | favori · geçmiş · indirilen okuma/yazma | §1 satır 1–3 (3 tablo) | ⚠️ PLANNED |
| 2 | Favori toggle servisi | ekleme/çıkarma + tekillik | `idx_favorites_unique` (§1 satır 1 · satır 66) | ⚠️ PLANNED — uygulama kanıtı YOK (D-L11) |
| 3 | Dinleme geçmişi yazıcısı | süre · yüzde · cihaz bağlamı kaydı | `user_listening_history` satır 88–92 | ⚠️ PLANNED — kod taraması yok |
| 4 | İndirme yöneticisi (durum makinesi) | pending → downloading → completed/failed/expired | `download_status` enum satır 39 | ⚠️ PLANNED — kod taraması yok |
| 5 | Kütüphane domain event'i (adı belirlenmedi) | favori/indirme değişimini yayınlama | D-L7: 9 event'te karşılık YOK | ⚠️ PLANNED — olay adı uydurulmaz |
| 6 | Kuyruk sınırı kararı | queue mülkiyeti library ↔ playback | [[library-dogrulama]] D-L12 (SINIR AÇIK) | ⏸ BEKLEMEDE — sahiplik ADR'si |

**Yasak:** §2'deki adlar **tasarım adayıdır**; disk'te karşılığı doğrulanmadan
"mevcut bileşen" gibi raporlanmaz.

**Geçiş kuralı:** her satır, karşılık gelen kod/olay diske yazıldığı ve LibraryEntry
aggregate'inin ADR-097 K4'te kilitlendiğinde `active` durumuna geçer; kapanana kadar
bu dal `draft` kalır.

**Kardeş dosyalar:** [[library-kapsam]] · [[library-dogrulama]] · [[library-web-arastirma]] · [[library-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
