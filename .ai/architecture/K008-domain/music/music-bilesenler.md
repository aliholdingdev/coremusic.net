---
node_id: "K008-music-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/music-kapsam.md"
evidence:
  - "music-kapsam.md §2 (sınır tablosu) + §3 (DDD model notu) — 2026-10-15 okundu"
  - "shared/src/Events/Domain/MusicAddedEvent.php satır 20, 27–29 · MusicPlayedEvent.php satır 20, 26–39"
  - "shared/src/Api/Dto/Response/MusicResponse.php satır 20–26 (7 alan — music-dogrulama.md §1 satır 27)"
validation: "2026-10-15 — kademe-3 planı; her satır kapsam/sınır kanıtına bağlı"
open_questions:
  - "Kademe-3 alt düğüm adları ADR-097 K4 onayı BEKLİYOR — burada taslak, bağlayıcı değil"
group: mimari
---

# music — Bileşenler (K008 · kademe-3 planı)

> **Authority: Bayram Ali / Vault Steward.** Bu dosya `music` Bounded Context'inin
> **kademe-3 (alt düğüm) plan taslağıdır**; adlar ADR-097 K4 onayına kadar
> **bağlayıcı değildir**. Kanıt iddiası yapmaz — iddialar
> [[music-kapsam]] ve [[music-dogrulama]] içindedir.

## §1 KADeme-3 ALT DÜĞÜM ADAYLARI

| # | Alt düğüm (aday) | Kapsamı (kapsam dosyasından) | Öncelik | Durum |
|---|---|---|---|---|
| 1 | `music-catalog` | track · albüm · sanatçı kavramının tanımı (kapsam §2 satır 1) | P0 | ⏳ ADR-097 K4 |
| 2 | `music-identity` | ISRC/ISWC gibi kimlik sözlüğü — web-arastirma §1 (W-1…W-8) | P2 | ⏳ ADR-097 K4 · şemada sütun **UNKNOWN** |
| 3 | `music-event` | `MusicAddedEvent` · `MusicPlayedEvent` sözleşmeleri | P1 | ✅ dosya kanıtlı (Events/Domain/) |
| 4 | `music-dto-siniri` | `MusicResponse` 7 alan ↔ domain'in DTO bilmezliği (master §8 C3 riski) | P1 | ⚠️ sınır ihlali riski — `domain-bagimliliklar.md` §3 |
| 5 | `music-invariant` | Tanımsız — kapsam §3 satır 57 (invariant listesi yasak doldurma) | P0 | ⚠️ VERIFICATION REQUIRED |

## §2 SINIR KOMŞULARI (kapsam §2'den — bağımlılık yönü)

| Komşu | Yön | Ne geçer | Kanıt |
|---|---|---|---|
| `media` | music → media | dosya kodlaması **geçmez** (yalnız kavram) | kapsam §2 satır 4 |
| `library` | music → library | favori/geçmiş durumu katalogda **tutulmaz** | kapsam §2 satır 5 |
| `playlist` | music → playlist | sıra/snapshot playlist'te | kapsam §2 satır 6 |
| K007-data | music → persistence | şema K007'nin (kavramlar paylaşılır) | kapsam §2 satır 41 |

## §3 BİLEŞEN OLGUNLUK MATRİSİ

| Bileşen | Kod kanıtı | Şema kanıtı | Test | Olgunluk |
|---|---|---|---|---|
| Event sınıfları | ✅ `Events/Domain/Music*.php` | — | ⚠️ test kanıtı yok (`shared/tests/Events/` 2026-10-15 görülmedi → VERIFICATION REQUIRED) | Orta |
| DTO (`MusicResponse`) | ✅ satır 20–26 | — | — | Orta |
| Katalog şeması | — | ✅ `.ai/.sql/mysql/coremusic_musics.sql` satır 305 (`musics`) | — | Orta |
| Kimlik (ISRC/ISWC) | ❌ yok | ❌ sütun görülmedi | — | Yok (UNKNOWN) |

**Sıradaki adım:** ADR-097 K4 onayı → bu tablo `domain-kararlar.md`'ye ADR satırı olarak taşınır.

**Kardeş dosyalar:** [[music-kapsam]] · [[music-dogrulama]] · [[music-web-arastirma]] · [[music-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
