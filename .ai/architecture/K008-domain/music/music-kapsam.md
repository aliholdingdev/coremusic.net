---
title: "music — Kapsam"
type: architecture-scope
node_id: K008-domain/music
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# music — Kademe-2 Kapsam Kaydı

> **DDD notu:** bu kayıt, bu dalın **Bounded Context tanımı adayıdır** (ADR-097 K4'te
> kilitlenir). Kapsam DDD terminolojisiyle — *aggregate · entity · ubiquitous language* —
> kurulur; v2 bu dala **yalnızca ad verir**, tahminle dolgu **yapılmaz**.

## §1 Rol (ELI15)

`domain-master.md` §1 sorumluluk tablosu (satır 40) bu dali **"Katalog kavramları
(şarkı · albüm · medya)"** sorumluluğuna `music · media · library` üçlüsüyle yazar.
Basitçe: bir şarkının ne olduğu, hangi albümde durduğu, hangi sanatçının yazdığı —
"şarkı" kelimesinin CoreMusic genelinde **tek** anlamı burada doğar.

`music/index.md` satır 13 bu tanımın v2'de **olmadığını** söyler; §1'deki cümle bir
**tasarım yorumudur**, kilitli sınır değildir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Şarkı (track) · albüm · sanatçı **kavramının tanımı** | Kavramın tabloda saklanması | K007-data (`-bagimliliklar` §3/1) |
| 2 | Katalog dili: `id · title · artist · album · duration` | Dosyanın fiziksel kodlanması/bitrate | K015-media (master §2.1) |
| 3 | "şarkı eklendi" kuralının olaya dönüşü | Ekleme servis süreci (kim, ne zaman) | K012-services (master §2.1) |
| 4 | Katalog kavramının medyadan **ayrı** tanımı | `stream · download` erişim türü | media (master §1/1) |
| 5 | Katalog kavramının koleksiyondan **ayrı** tanımı | "kütüphanesinde" / "beğendi" durumu | library (master §1/1) |
| 6 | Katalog kavramının listelerden **ayrı** tanımı | Sıra, `snapshot_id`, ortak sahiplik | playlist (master §1/2) |
| — | *(dışında)* dış servis katalog eşlemesi (Spotify/Apple) | — | K013-integraion (master §2.1) |

**Sınır notu:** satır 1'deki "tanım ↔ şema" ayrımı `domain-bagimliliklar.md` §4 satır 47'de
`kavram adları (ubiquitous language) paylaşılır · şema K007'nin` diye yazılıdır; bu tablo
o ayrımı yansıtır, **yeni sınır önermez**.

## §3 DDD model notu (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Katalog modeli: track · album · artist | ⚠️ ADR-097 K4 bekliyor |
| 2 | Aggregate (aday) | `Track` — album/artist **referansları** içerir | ⚠️ TASARIM İDDİASI — sınır çizilmedi |
| 3 | Entity | `musicId` taşıyan kayıt (kimlik eşitliği bu id) | ✅ repo: `MusicAddedEvent.php` satır 27 |
| 4 | Value Object (aday) | `duration` (int, saniye) · `title` (string) | ✅ repo: `MusicResponse.php` satır 24, 21 |
| 5 | Ubiquitous language | şarkı = `track` · albüm = `album` · sanatçı = `artist` | ✅ repo: `MusicResponse.php` satır 21–23 |
| 6 | Domain event | `music.added` · `music.played` | ✅ repo: Events/Domain/ (satır 39 her ikisi) |
| 7 | Invariant | **tanımsız** — invariant listesi kademe-3'te | ⚠️ VERIFICATION REQUIRED |

**Yasak:** aggregate sınırı, invariant listesi ve kural seti bu dosyada **uydurulmaz**;
§2/§3'teki ⚠️ satırlar kapanmadan bu dal `draft` kalır.

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: `music` = v2 §2.8 satır 298 | `.ai/architecture/K008-domain/domain-master.md` §3 satır 74 | ✅ dosya okundu |
| 2 | Sorumluluk #1 = katalog kavramları → music · media · library | `domain-master.md` §1 satır 40 | ✅ dosya okundu |
| 3 | "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED" | `music/index.md` satır 13 | ✅ dosya okundu |
| 4 | Katalog DTO'su 7 alanlı: `id·title·artist·album·duration·coverUrl·streamUrl` | `shared/src/Api/Dto/Response/MusicResponse.php` satır 20–26 | ✅ dosya okundu |
| 5 | Domain event `music.added` (`musicId·addedBy·title`) | `shared/src/Events/Domain/MusicAddedEvent.php` satır 27–29, 39 | ✅ dosya okundu |
| 6 | Domain event `music.played` (`musicId·userId·duration`) | `shared/src/Events/Domain/MusicPlayedEvent.php` satır 27–29, 39 | ✅ dosya okundu |
| 7 | Arama DTO'su katalog filtrelerini taşır: `query·genre·artist·page·limit` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` satır 20–24 | ✅ dosya okundu |
| 8 | music ↔ K007 sınırı = kavram adları paylaşılır | `domain-bagimliliklar.md` §4 satır 47 | ✅ dosya okundu |
| 9 | Bu dalın resmi kapsam tanımı (aggregate/invariant) | — | **UNKNOWN** (ADR-097 K4) |

> **Not:** 4–7 satırları `shared/src/` (alt domain değil, paylaşılan katman) içindir;
> bunlar katalog kavramının **repo'daki karşılığıdır**, K008 kapsamının kendisi değildir.

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | music ↔ media sınırı: "şarkı" ile "erişilebilir medya" aynı varlık mı, ayrı mı? | ⚠️ VERIFICATION REQUIRED — bu kayıtta açıldı (master §7'de yok) |
| R2 | Aggregate `Track` mi, `Recording` mi? Kimlik hangi id? | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |
| D3 | `music.played` olayının sahibi playback mi, music mi? (master §7/2 ile kesişir) | ⚠️ VERIFICATION REQUIRED |
| R4 | CoreMusic katalog modelinin kendi dili/çerçevesi (UNKNOWN — master §4 satır 97) | **UNKNOWN** |
| R5 | K013-integraion ile katalog eşlemesi hangi alan üzerinden? | **UNKNOWN** |

**Kapanış:** status: draft kalır. §3/7 ve §4/9 kapanmadan bu dosyada kapsam
iddiası **kesin** sayılmaz (`domain-dogrulama.md` §5 satır 67: ölçüt 6–8 açık).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
