---
title: "media — Kapsam"
type: architecture-scope
node_id: K008-domain/media
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# media — Kademe-2 Kapsam Kaydı

> **DDD notu:** bu kayıt bu dalın **Bounded Context tanımı adayıdır** (ADR-097 K4'te
> kilitlenir). Kapsam *aggregate · entity · ubiquitous language* ile kurulur;
> v2 bu dala **yalnızca ad verir** — tahminle dolgu **yapılmaz**.

## §1 Rol (ELI15)

`domain-master.md` §1 satır 40 bu dali **"Katalog kavramları (şarkı · albüm · medya)"**
sorumluluğuna yazar (`music · media · library` üçlüsü). Basitçe: bir içeriğe **nasıl
erişildiğinin** anlamı — `stream` mi, `download` mı, hangi kullanıcı, ne zaman —
"erişim" kelimesinin tek anlamı burada doğar.

`media/index.md` satır 13: v2'de rol tanımı **yok** → §1 bir tasarım yorumudur.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Erişim türü kavramı (`accessType`: stream · download · preview) | Dosyanın kodlanması, bitrate, codec | K015-media · K004-dsp (master §2.1) |
| 2 | Erişimin kiminle ilgili olduğu (`mediaId` + `userId`) | Kimlik/rol tanımı | K010-identitiy (master §2.1) |
| 3 | "erişildi" olayının kurala dönüşü | Olmanın fiziksel üretilmesi/ses yolu | K003-audio (master §2.1 — ⚠️ SINIR AÇIK) |
| 4 | Medya kavramının katalogdan **ayrı** tanımı | Şarkı/albüm kimliği | music (master §1/1) |
| 5 | Yerel dosya ↔ katalog içeriği ayrımı kavramı | Yerel dosyanın taranması/indexlenmesi | K007-data (bagimliliklar §4/1) |
| — | *(dışında)* medya kitaplığı/saklama şeması | — | K007-data (master §2.1) |

**Sınır notu:** satır 3 `domain-bagimliliklar.md` §4 satır 49'da `"ne çalınır" listesi
paylaşılır · "nasıl çalınır" motoru → ⚠️ SINIR AÇIK` diye yazılıdır; bu tablo o açık
sınırı **tekrarlar**, kapatmaz.

## §3 DDD model notu (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Erişim/medya modeli: media · accessType · userId | ⚠️ ADR-097 K4 bekliyor |
| 2 | Aggregate (aday) | `MediaAccess` (bir erişim kaydı) — **sınır çizilmedi** | ⚠️ TASARIM İDDİASI |
| 3 | Entity | `mediaId` + olayın kimliği; erişim kaydı tektir | ✅ repo: `MediaAccessedEvent.php` satır 27 |
| 4 | Value Object (aday) | `accessType` — **sabit küme**: `stream` · `download` (dokümante) | ✅ repo: satır 29, 80 (yorum: "stream, download, etc.") |
| 5 | Ubiquitous language | erişim = `access` · akış = `stream` · indirme = `download` · yerel = `is_local` | ✅ repo satır 39 · Spotify `is_local` |
| 6 | Domain event | `media.accessed` (`mediaId · userId · accessType`) | ✅ repo: satır 27–29, 39 |
| 7 | Invariant | **tanımsız** (ör. "download kuralı kimin izni?") | ⚠️ VERIFICATION REQUIRED |

**Yasak:** `accessType` kümesinin tamamı ve izin invariant'ı bu dosyada **uydurulmaz** —
`etc.` ifadesi (satır 80) kapanmamış bir açıklık işaretidir.

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: `media` = v2 §2.8 satır 299 | `domain-master.md` §3 satır 75 | ✅ dosya okundu |
| 2 | Sorumluluk #1 = katalog kavramları → music · media · library | `domain-master.md` §1 satır 40 | ✅ dosya okundu |
| 3 | "Rol tanımı v2'de yok → ⚠️" | `media/index.md` satır 13 | ✅ dosya okundu |
| 4 | Domain event `media.accessed` gerçek (`mediaId · userId · accessType`) | `shared/src/Events/Domain/MediaAccessedEvent.php` satır 27–29, 39 | ✅ shell ile okundu |
| 5 | `accessType` dokümanı "stream, download, etc." der — küme açık | aynı dosya satır 80 (getter yorumu) | ✅ shell ile okundu |
| 6 | `payload()` `accessType` anahtarını yayınlar | aynı dosya satır 58 | ✅ shell ile okundu |
| 7 | K008 ↔ K003/K015 sınırı "ne çalınır" / "nasıl çalınır" ayrımı — **AÇIK** | `domain-bagimliliklar.md` §4 satır 49 | ⚠️ SINIR AÇIK |
| 8 | Medya kavramının resmi kapsam tanımı | — | **UNKNOWN** (ADR-097 K4) |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | `accessType` kümesinin tamamı nedir? ("etc." kapanmamış) | ⚠️ VERIFICATION REQUIRED — bu kayıtta açıldı |
| R2 | media ↔ music sınırı: erişim, şarkının özelliği mi ayrı varlık mı? | ⚠️ VERIFICATION REQUIRED |
| R3 | Download izni kuralı (lisans/DRM) kime ait? K013 mü, K020-licence mı? | ⚠️ VERIFICATION REQUIRED |
| R4 | media erişim kaydının saklanması (tablo/olay deposu) | **UNKNOWN** — K007/K011 kanıtı yok |
| R5 | Kodlama/codec kuralının sahibi (K015 mi K004 mü?) | **UNKNOWN** (master §2.1 ⚠️ SINIR AÇIK) |

**Kapanış:** status: draft kalır. §3/7 ve §4/8 kapanmadan bu dosyada kapsam iddiası
**kesin** sayılmaz (`domain-dogrulama.md` §5 satır 67).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
