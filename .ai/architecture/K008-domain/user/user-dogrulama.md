---
title: "user — Doğrulama"
type: architecture-verification
node_id: K008-domain/user
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# user — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda açılan URL'ler · (b) bu oturumda okunan gerçek disk
> yolları. Yasak: URL, sınıf, satır uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Spotify Web API bir **Users** referans grubu (Get Current User's Profile · Get User's Profile · Follow/Unfollow) tanımlar | [[domain-web-arastirma]] §1 #69 (Spotify Web API kök — tekil orada, C4) | 2026-10-12 | ✅ kökte doğrulandı |
| 2 | Spotify, kullanıcı-kavramını profil (profile) ile katalog sahipliğinden ayırır: "kullanıcı profili" ≠ "kütüphane" | [[domain-web-arastirma]] §1 #69 (Reference nav — Users/Library grupları) | 2026-10-12 | ✅ kökte doğrulandı |
| 3 | DDD Reference, DDD kalıp/tanımlarının özeti konumundadır (Evans 2004) — model/ortak dil dili için referans | [[music-dogrulama]] §1 #3 (DDD Reference URL'i tekil orada — C4) | 2026-10-15 | ✅ HTTP GET |
| 4 | Kullanıcı DTO'su 7 alanlı (`id·username·email·displayName·avatar·gender·roles`) | `shared/src/Api/Dto/Response/UserResponse.php` satır 20–26 | 2026-10-12 | ✅ shell ile okundu |
| 5 | `gender` varsayılanı `neutral` | aynı dosya satır 96 | 2026-10-12 | ✅ shell ile okundu |
| 6 | 4 domain event gerçek: UserRegistered · UserLoggedIn · UserLoggedOut · GenderSet | `shared/src/Events/Domain/*.php` (shell sayımı 2026-10-12) | 2026-10-12 | ✅ shell ile okundu |
| 7 | Ayrıca `PasswordResetRequestedEvent` mevcut (parola olayı = K010 alanı) | `shared/src/Events/Domain/PasswordResetRequestedEvent.php` | 2026-10-12 | ✅ shell ile okundu |
| 8 | user rolü (tasarım yorumu) = kullanıcı kavramları | `domain-master.md` §1 satır 41 | 2026-10-12 | ✅ dosya okundu |
| 9 | user, v2 §2.8'de ad olarak geçer (satır 300) | `domain-master.md` §3 satır 76 · `user/index.md` satır 9 | 2026-10-12 | ✅ dosya okundu |
| 10 | Yetki/rol tanımları K008 kapsamı DIŞI | `domain-master.md` §2.1 satır 55 | 2026-10-12 | ✅ dosya okundu |
| 11 | user rol tanımı v2'de YOK | `user/index.md` satır 13 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | user ↔ K010-identitiy sınırı (email/parola hangi BC'de?) | — (master §7'de bu soru yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | CoreMusic'te tanımlı `User` aggregate'si (K008 domain katmanı) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 14 | `user` ↔ `auth.coremusic.net/include/Domain/Entity/User.php` karşılıklı kaydı | — (dosya var, vault'ta ilişki kaydı yok) | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 15 | CoreMusic'in kullanıcı verisini sakladığı BC/şema sahipliği (K007 ile) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| Benzersiz URL | 0 (hepsi tekil evlerinde: kök §1 #69 ve music-dogrulama §1 #3 — C4) |
| Gerçek dosya yolu satırı | 7 (4–10) + 3 (11–12 işaretli) |
| ✅ doğrulandı | 10 (1–10) |
| ⚠️ VERIFICATION REQUIRED | 2 (11 · 12) |
| UNKNOWN | 3 (13 · 14 · 15) |

**Geçiş kuralı:** satır 11 ADR-097 K4 ile, 12 sınır kaydıyla (K008 ↔ K010) kapanır ·
13–15 kanıt gelene kadar UNKNOWN kalır; kapanmazsa `status: draft` sürer.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
