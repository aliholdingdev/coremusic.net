---
node_id: "K008-library-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-library-web-arastirma"
evidence:
  - "satır D-L1…D-L12 — 2026-10-15 kaydı"
  - "grep kanıtı: favorite|user_favorites = 0 isabet (repo PHP/JS), context: zero-hallucination"
validation: "2026-10-15 — 12 iddia: 7 Dosya/Satır, 3 web pointer (§1), 2 negatif/UNKNOWN"
open_questions:
  - "Favori uç noktası servisi → UNKNOWN (kanıt yok)"
  - "queue mülkiyeti → SINIR AÇIK (bkz [[library-kapsam]] §2 satır 5)"
group: kanit
---

# library — Doğrulama Defteri (K008)

> **Authority: Bayram Ali / Vault Steward.** Web kanıtları **URL ile değil**
> [[library-web-arastirma]] §1 pointer'ı ile verilir (C4 — URL tekrarı yasak).
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN**.

## §1 İDDİA TABLOSU

| # | İddia | Repo kanıtı (Dosya/Satır) | Web kanıtı | Tür | Durum |
|---|-------|---------------------------|-----------|-----|-------|
| D-L1 | `user_favorites` tablosu şemada var | `.ai/.sql/mysql/coremusic_user.sql` satır 59–69 | — | T4 | ✅ DOĞRULANDI |
| D-L2 | Favori polymorphic: `item_type enum('music','album','playlist','artist','podcast')` | aynı dosya satır 62–63 | — | T4 | ✅ DOĞRULANDI |
| D-L3 | Favori tekilliği DB'de zorlanıyor (`UNIQUE user_id+item_type+item_id`) | aynı dosya satır 66 | — | T4 | ✅ DOĞRULANDI |
| D-L4 | Dinleme geçmişi tablosu var (süre · yüzde · cihaz tipi) | aynı dosya satır 83–102 | — | T4 | ✅ DOĞRULANDI |
| D-L5 | İndirilen koleksiyon tablosu var (`user_downloads`, unique user+music) | aynı dosya satır 35–51 | — | T4 | ✅ DOĞRULANDI |
| D-L6 | Repo PHP/JS'te favori kodu **YOK** (`favorite\|user_favorites` = 0 isabet) | 2026-10-15 grep (repo geneli) | — | T4 | ✅ NEGATİF KANIT |
| D-L7 | 9 domain event'te favori/kütüphane olayı **YOK** | `shared/src/Events/Domain/` dizin (2026-10-15 — 9 dosya sayıldı) | — | T4 | ✅ NEGATİF KANIT |
| D-L8 | Koleksiyon/favori kavramı web'de tanımlı | — | [[library-web-arastirma]] §1 W-3, W-4, W-6 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-L9 | Kişisel kayıt servisi modeli (saved tracks/albums) referans alınabilir | — | [[library-web-arastirma]] §1 W-1, W-2 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-L10 | `LibraryEntry` aggregate'i (master §3.1 satır 90) | `domain-master.md` satır 90 — tasarım iddiası | — | — | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |
| D-L11 | Favori ekleme uç noktası/servisi | kanıt YOK | — | — | **UNKNOWN** |
| D-L12 | Sıra (queue) mülkiyeti library ↔ playback | `domain-master.md` satır 216–217 (kuyruk event'i kodlanmadı, `playback_queue` tablosu var) | — | — | **SINIR AÇIK** |

## §2 SAYIM

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| ✅ doğrulandı (T4) | 7 (D-L1…D-L7) |
| ✅ web (T3) | 2 (D-L8 · D-L9 — URL'ler web dosyasında) |
| ⚠️ / UNKNOWN / AÇIK | 3 (D-L10 · D-L11 · D-L12) |

**Geçiş kuralı:** D-L10 ADR-097 K4 ile · D-L11 servis kanıtıyla · D-L12 sahiplik ADR'siyle kapanır; kapanana kadar bu dal `draft` kalır.

**Kardeş dosyalar:** [[library-kapsam]] · [[library-web-arastirma]] · [[library-ornekler]] · [[library-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
