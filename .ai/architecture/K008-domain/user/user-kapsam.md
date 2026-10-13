---
title: "user — Kapsam"
type: architecture-scope
node_id: K008-domain/user
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# user — Kademe-2 Kapsam Kaydı

> **DDD notu:** bu kayıt bu dalın **Bounded Context tanımı adayıdır** (ADR-097 K4).
> Kapsam *aggregate · entity · ubiquitous language* ile kurulur; v2 bu dala **yalnızca
> ad verir** — tahminle dolgu **yapılmaz**.

## §1 Rol (ELI15)

`domain-master.md` §1 satır 41 bu dali **"Kullanıcı kavramları"** sorumluluğuna
`user · playlist` ikilisiyle yazar. Basitçe: "kim" sorusunun cevabı — hangi kullanıcı
beğendi, hangi kullanıcı ekledi, hangi kullanıcı çaldı. Yetki/rol sözlüğü **burada
değil** (bkz. §2 satır 4).

`user/index.md` satır 13: v2'de rol tanımı **yok** → §1 bir tasarım yorumudur.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Kullanıcı **kavramı**: id · username · displayName · avatar · gender | Parola, oturum, JWT, kimlik doğrulama | K010-identitiy (master §2.1 satır 55) |
| 2 | Olaylardaki `userId` kimliğinin anlamı (kim ekledi/beğendi/çaldı) | Yetki/rol **tanımı** (`roles` alanı burada taşınır, anlamı K010'un) | K010-identitiy |
| 3 | Kullanıcı kavramının playlist'ten ayrı tanımı | Liste sahipliği/ortaklık kuralı | playlist (master §1 satır 41) |
| 4 | Kullanıcı kavramının kütüphaneden ayrı tanımı | Koleksiyon içeriği | library (master §1 satır 41) |
| 5 | Cinsiyet/görünen ad gibi profil alanlarının **kavramı** | Profilin şemada saklanması | K007-data (bagimliliklar §3/1) |
| — | *(dışında)* auth akışı (kayıt/giriş/çıkış süreçleri) | — | K010 · K012-services (master §2.1) |

**Sınır notu:** satır 1'deki ayrım `-bagimliliklar` §4/1 ile uyumludur: K008 **kavram
adlarını** (ubiquitous language) sahiplenir, tabloyu değil.

## §3 DDD model notu (bu dal için)

| # | Kavram | Bu dalda değeri | Kanıt durumu |
|---|---|---|---|
| 1 | Bounded Context (aday) | Kullanıcı profili modeli (kimlik doğrulama **dışı**) | ⚠️ ADR-097 K4 bekliyor |
| 2 | Aggregate (aday) | `User` — profile alanları | ⚠️ TASARIM İDDİASI (aggregate sınırı çizilmedi) |
| 3 | Entity | `userId` (id eşitliği) | ✅ repo: `UserResponse.php` satır 20 |
| 4 | Value Object (aday) | `username` · `displayName` · `gender` (varsayılan `neutral`) | ✅ repo: satır 21–25, 96 |
| 5 | Ubiquitous language | kullanıcı = `user` · görünen ad = `displayName` · rol = `roles` | ✅ repo: satır 20–26 |
| 6 | Domain event | `user.registered` · `user.logged_in` · `user.logged_out` · `gender.set` | ✅ repo: Events/Domain/ (4 dosya) |
| 7 | Invariant | **tanımsız** (ör. username benzersizliği kimin kuralı?) | ⚠️ VERIFICATION REQUIRED |

**Yasak:** yetki/rol sözlüğü, kimlik doğrulama kuralı ve parola politikası bu dosyada
**uydurulmaz** — bunlar `master §2.1` gereği K010-identitiy'nindir.

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: `user` = v2 §2.8 satır 300 | `domain-master.md` §3 satır 76 | ✅ dosya okundu |
| 2 | Sorumluluk #2 = kullanıcı kavramları → user · playlist | `domain-master.md` §1 satır 41 | ✅ dosya okundu |
| 3 | "Rol tanımı v2'de yok → ⚠️" | `user/index.md` satır 13 | ✅ dosya okundu |
| 4 | Kullanıcı DTO'su 7 alanlı (`id·username·email·displayName·avatar·gender·roles`) | `shared/src/Api/Dto/Response/UserResponse.php` satır 20–26 | ✅ shell ile okundu |
| 5 | `gender` varsayılanı `neutral` (fromArray) | aynı dosya satır 96 | ✅ shell ile okundu |
| 6 | 4 kullanıcı domain event'i gerçek: UserRegistered · UserLoggedIn · UserLoggedOut · GenderSet | `shared/src/Events/Domain/` (4 dosya, shell sayımı 2026-10-12) | ✅ shell ile okundu |
| 7 | Yetki/rol tanımları kapsam DIŞI → K010-identitiy | `domain-master.md` §2.1 satır 55 | ✅ dosya okundu |
| 8 | user aggregate'sinin resmi kapsam tanımı | — | **UNKNOWN** (ADR-097 K4) |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | user (K008) ile kimlik doğrulama (K010) sınırı: `email` hangisinde? | ⚠️ VERIFICATION REQUIRED — bu kayıtta açıldı |
| R2 | `roles` alanı taşınıyor ama rol **tanımı** K010'da — kavram burada mı? | ⚠️ VERIFICATION REQUIRED |
| R3 | `user.registered` olayının sahibi user mı, K010 mu? | ⚠️ VERIFICATION REQUIRED |
| R4 | Kullanıcı aggregate'si ile `auth.coremusic.net` içindeki `Domain\Entity\User` ilişkisi | **UNKNOWN** — karşılıklı kayıt yok |
| R5 | `email`/`password` için K008 ↔ K007 şema sınırı | **UNKNOWN** (bagimliliklar §3/1 TASARIM) |

**Kapanış:** status: draft kalır. §3/7 ve §4/8 kapanmadan bu dosyada kapsam iddiası
**kesin** sayılmaz (`domain-dogrulama.md` §5 satır 67).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
