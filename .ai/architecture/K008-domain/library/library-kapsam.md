---
node_id: "K008-library-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master.md"
evidence:
  - "domain-master.md §3.1 satır 90 — library = 'Kütüphane', ana aggregate LibraryEntry (favori/geçmiş/sıra)"
  - ".ai/.sql/mysql/coremusic_user.sql satır 35–102 (user_downloads · user_favorites · user_listening_history)"
  - "2026-10-15 grep: repo PHP/JS `favorite|user_favorites` = 0 isabet — kod kanıtı YOK"
validation: "2026-10-15 — kapsam sınırı SQL varlıkları + negatif kod kanıtı ile çizildi"
open_questions:
  - "LibraryEntry aggregate'i ADR-097 K4'te kilitlenmedi → taslak"
  - "Favori ekleme uç noktası/servisi → repo'da kanıt YOK → UNKNOWN"
  - "Sıra (queue) mülkiyeti: library mi playback mi → master §7 notu (kuyruk event'i yok) → SINIR AÇIK"
group: mimari
---

# library — Kademe-2 Kapsam Kaydı (K008)

> **Authority: Bayram Ali / Vault Steward.** `library`, kullanıcının **kendi
> koleksiyonu**nun (favori · geçmiş · indirilen) kavramıdır. SQL varlıkları vardır;
> **kod karşılığı 2026-10-15 taramasında YOK** — tahminle dolgu yapılmaz.

## §1 ROL (ELI15)

`domain-master.md` §3.1 (satır 90) bu dali **"Kütüphane — LibraryEntry (favori/geçmiş/sıra)"**
yazar. Basitçe: "kullanıcının kendi şeylerinin listesi" — bir şarkının *kullanıcının
kütüphanesinde olup olmadığı* CoreMusic genelinde **tek** anlamda burada doğar.

## §2 KAPSAM SINIRI (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Favori kavramı (`user_favorites` dili: item_type · item_id) | Şarkının kendisi (tanım) | music |
| 2 | Dinleme geçmişi (`user_listening_history` dili) | Oynatma anlık durumu (play/pause) | playback |
| 3 | İndirilen koleksiyon (`user_downloads` dili) | Dosya kodlaması/transcode | media |
| 4 | Koleksiyon/архив terimleri (web: [[library-web-arastirma]] W-1…W-8) | Şemanın normalizasyonu | K007-data |
| 5 | — | Sıra (queue) — **mülkiyet açık** (master §7: kuyruk event'i yok) | playback (SINIR AÇIK) |

## §3 DDD MODEL NOTU (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Kütüphane modeli: favori · geçmiş · indirilen | ⚠️ ADR-097 K4 |
| 2 | Aggregate (aday) | `LibraryEntry` (master §3.1) | ⚠️ TASARIM İDDİASI |
| 3 | Entity | `user_id + item_type + item_id` üçlüsü (unique) | ✅ `coremusic_user.sql` satır 66 |
| 4 | Value Object (aday) | `item_type` enum · `listen_percentage` decimal | ✅ satır 62, 89 |
| 5 | Ubiquitous language | favori = `favorite` · geçmiş = `listening_history` · indirilen = `download` | ✅ şema COMMENT'leri |
| 6 | Domain event | **YOK** — 9 event'te favori/kütüphane olayı yok | ✅ negatif (2026-10-15 dizin) |
| 7 | Invariant | tanımsız (ör. "favori tekildir" → unique key kanıtı var ama invariant listesi yok) | ⚠️ ADR-097 K4 |

**Yasak:** aggregate sınırı ve invariant listesi bu dosyada **uydurulmaz**.

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/.sql/mysql/coremusic_user.sql` | 59–69 | `user_favorites` (polymorphic, unique) |
| `.ai/.sql/mysql/coremusic_user.sql` | 83–102 | `user_listening_history` |
| `.ai/.sql/mysql/coremusic_user.sql` | 35–57 | `user_downloads` |
| `shared/src` + repo JS/PHP | — | `favorite` = **0 isabet** (2026-10-15) |

**Kardeş dosyalar:** [[library-dogrulama]] · [[library-web-arastirma]] · [[library-ornekler]] · [[library-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
