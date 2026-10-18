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
| 1 | Kullanıcı **kavramı**: id · username · displayName · avatar · gender | Parola, oturum, JWT, kimlik doğrulama | K0100-identitiy (master §2.1 satır 55) |
| 2 | Olaylardaki `userId` kimliğinin anlamı (kim ekledi/beğendi/çaldı) | Yetki/rol **tanımı** (`roles` alanı burada taşınır, anlamı K010'un) | K0100-identitiy |
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
**uydurulmaz** — bunlar `master §2.1` gereği K0100-identitiy'nindir.

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: `user` = v2 §2.8 satır 300 | `domain-master.md` §3 satır 76 | ✅ dosya okundu |
| 2 | Sorumluluk #2 = kullanıcı kavramları → user · playlist | `domain-master.md` §1 satır 41 | ✅ dosya okundu |
| 3 | "Rol tanımı v2'de yok → ⚠️" | `user/index.md` satır 13 | ✅ dosya okundu |
| 4 | Kullanıcı DTO'su 7 alanlı (`id·username·email·displayName·avatar·gender·roles`) | `shared/src/Api/Dto/Response/UserResponse.php` satır 20–26 | ✅ shell ile okundu |
| 5 | `gender` varsayılanı `neutral` (fromArray) | aynı dosya satır 96 | ✅ shell ile okundu |
| 6 | 4 kullanıcı domain event'i gerçek: UserRegistered · UserLoggedIn · UserLoggedOut · GenderSet | `shared/src/Events/Domain/` (4 dosya, shell sayımı 2026-10-12) | ✅ shell ile okundu |
| 7 | Yetki/rol tanımları kapsam DIŞI → K0100-identitiy | `domain-master.md` §2.1 satır 55 | ✅ dosya okundu |
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

## §6 ŞEMA KANITLARI: KULLANICI ÇEKİRDEĞİ

> **Kaynak:** `.ai/.sql/mysql/coremusic_auth.sql` (298 satır · `users` s.270–296)
> ve `.ai/.sql/mysql/coremusic_user.sql` (150 satır · profil/tercih/tablolar).
> Bu bölüm **kilitli kapsam değildir** — §3 yasağı gereği yalnız kanıt sunar.

### §6.1 Çekirdek tablo envanteri

| Tablo | Dosya · satır | Rol | Kanıt notu |
|---|---|---|---|
| `users` | coremusic_auth.sql s.270–296 | Hesap çekirdeği | 2 UNIQUE: username (s.291) · email (s.292) |
| `user_profiles` | coremusic_user.sql s.126–148 | Profil uzantısı | `user_id` UNIQUE (s.144) + 3 enum |
| `user_preferences` | coremusic_user.sql s.104–124 | Tercihler | `user_id` UNIQUE (s.121) + 6 tercih kolonu |
| `user_follows` | coremusic_user.sql s.71–81 | Sosyal grafiği | UNIQUE (follower, following) s.77 |
| `user_roles` · `user_assigned_roles` | coremusic_auth.sql s.213–224 · s.193–204 | Rol ataması | **KAPSAM DIŞI** (§2/2 → K0100) — yalnız sınır kanıtı |

### §6.2 `users` çekirdek kolon okuması

| Kolon | Satır | Kanıt |
|---|---|---|
| `username` varchar(50) NOT NULL + UNIQUE | s.272, s.291 | Benzersiz giriş adı |
| `email` varchar(255) NOT NULL + UNIQUE | s.273, s.292 | Benzersiz e-posta |
| `password_hash` (Argon2id yorumu) | s.274 | **KAPSAM DIŞI içeriği** — kimlik doğrulamanın (§2/1) |
| `display_name` varchar(100) NULL | s.275 | Görünen ad — opsiyonel |
| `gender` enum('male','female','neutral') DEFAULT 'neutral' | s.276 | 3'lü kapalı küme |
| `avatar_url` varchar(500) NULL | s.277 | |
| `account_type` enum('free','premium','studio','admin') | s.278 | 4'lü katman |
| `is_active` · `is_banned` · `is_verified` | s.279–281 | Üç bağımsız bayrak |
| `failed_login_attempts` int DEFAULT 0 | s.282 | Auth alanıdır — içeriği K0100'ün |
| `is_deleted` + `deleted_at` | s.288–289 | Yumuşak silme ikilisi (music M-P9 deseni) |

### §6.3 Üç tablo — bir kullanıcı (profil/tercih ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| Profil 1:1 | s.144 `idx_profiles_user` UNIQUE | ✅ |
| Tercih 1:1 | s.121 `idx_preferences_user` UNIQUE | ✅ |
| Profil kolonları | bio · date_of_birth · country (ISO 2, s.131) · language (s.132) · timezone (s.133) · notification_* (s.135–137) · profile_visibility enum 3 (s.138) | ✅ |
| Tercih kolonları | device_type enum 6 (s.107) · theme (s.108) · audio_quality enum 4 (s.109) · auto_play (s.110) · crossfade_duration (s.111) · normalize_audio (s.112) · explicit_content (s.113) · autoplay_recommendations (s.114) · download_over_wifi_only (s.115) | ✅ |
| Her üç tablo da FK CASCADE | s.123 · s.147 (`users` silinirse satırlar düşer) | ✅ |

### §6.4 `gender` İKİ KATMANLI BULGUSU (bu turun en önemli bulgusu)

| Katman | Kolon | Satır | Değer |
|---|---|---|---|
| Hesap | `users.gender` | s.276 | enum('male','female','neutral') |
| Profil | `user_profiles.theme_gender` | s.134 | enum('male','female','neutral') — yorumu "Theme engine gender preference" |
| Olay | `GenderSetEvent` | Events/Domain/ 96→76 satır dosyası | `gender.set` (§9.1) |
| DTO | `UserResponse.gender` | satır 24 (§4 kanıtlı) | varsayılan `neutral` (s.96) |

**Okuma:** aynı üçlü küme iki tabloda iki kez tanımlı; olay tek `gender.set`
taşır ve **hangi katmana yazdığı dosyalanmamış** → §12 R-U8.

### §6.5 `user_follows` (sosyal — sınır kanıtı)

| İddia | Kanıt satırı | Durum |
|---|---|---|
| Takip ilişkisi tektir | s.77 UNIQUE (follower_id, following_id) | ✅ |
| Kendini takip engeli **DB'de yok** | s.73–74 iki FK; `follower ≠ following` CHECK **yok** | ✅ negatif kanıt — engel domain'de (§7 U-P8) |
| Her iki FK da CASCADE | s.79–80 | ✅ |

## §7 INVARIANT ADAYLARI (SQL KANITLI — KİLİTLENMEMİŞ ÖNERİ)

> **§3 yasağı gereği:** bu tablo **kilitli invariant listesi değildir**; her satır
> kanıtı ile **öneri**yi ayrı sütunda tutar. `status: draft` bu eklemle **kapanmaz**.

| # | SQL kanıtı (satır) | Ne garanti eder | Invariant adayı (kanıt DEĞİL) |
|---|---|---|---|
| U-P1 | s.291 UNIQUE (username) | Kullanıcı adı benzersiz | Aynı adla ikinci hesap açılamaz — çakışma ret (VO §8) |
| U-P2 | s.292 UNIQUE (email) | E-posta benzersiz | Aynı e-posta iki hesapta olamaz |
| U-P3 | s.276 + s.134 enum('male','female','neutral') | İki tabloda kapalı 3'lü | Geçersiz cinsiyet değeri yazılamaz |
| U-P4 | s.144 · s.121 UNIQUE (user_id) | Profil ve tercih 1:1 | İkinci profil/tercih satırı yaratılamaz |
| U-P5 | s.138 enum('public','private','friends') | Görünürlük kapalı 3'lü | Geçersiz görünürlük yok |
| U-P6 | s.109 enum('low','medium','high','lossless') | Ses kalitesi kapalı 4'lü | Geçersiz kalite yok |
| U-P7 | s.131 country varchar(2) | ISO 3166-1 alfa-2 | İki karakter dışındaki değer domain'de ret (öneri — DB VARCHAR kısıtı yalnız uzunluk) |
| U-P8 | s.77 UNIQUE + CHECK **YOK** (follower≠following) | DB kendini takip etmeyi **düşürmez** | Kendini takip domain'de ret edilmeli — DB güvencesi **yok** |
| U-P9 | s.288–289 yumuşak silme ikilisi | Silme iki alanlı | Silme yalnız ikisi birlikte (music M-P9 ile aynı) |
| U-P10 | s.279–281 üç bağımsız bayrak | `is_banned` bağımsız | `is_banned=1` iken giriş engeli **auth** katmanının — bu dalda değil (§2/1) |
| U-P11 | s.123 · s.147 FK CASCADE | `users` silinirse profil/tercih düşer | Hesap silmesi profil/tercihi de temizler (DB davranışı) |
| U-P12 | s.278 enum('free','premium','studio','admin') | Katman kapalı 4'lü | Geçersiz hesap tipi yok — **katman hakları K0100'ün** |

### §7.1 Uyumsuzluk / açık noktalar (kanıt + öneri ayrımı)

| İddia | Kanıt | Durum |
|---|---|---|
| `gender` iki tabloda (§6.4) | s.276 ↔ s.134 | ⚠️ SSOT YOK — hangisi gerçek? R-U8 |
| `UserResponse.email` DTO'da (§4/4) | `users.email` auth şemasında (s.273) | ⚠️ kavram K008'de, kolon K0100 şemasında — R-U6 |
| `roles` DTO'da taşınıyor (§4/4) | `user_roles`/`user_assigned_roles` s.193–224 | ⚠️ taşınan kavramın sahibi K0100 — R-U7 |
| Kendini takip engeli DB'de yok | s.73–74 CHECK yok | ⚠️ domain katmanı yükü — U-P8 |

## §8 VALUE OBJECT ADAYLARI (ÖNERİ — DEPODA VO YOK)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda kullanıcı VO sınıfı yoktur; adaylar
> yalnız şema kolonlarından türetilmiştir (`domain-master.md` §25 kuralı).

| # | VO | Dayanak kolon | Değer kümesi / biçim | Önerilen davranış |
|---|---|---|---|---|
| V-U1 | `Username` | s.272 varchar(50) NOT NULL | 1–50 karakter | Boş/whitespace ret; UNIQUE çakışması ret (U-P1) |
| V-U2 | `Email` | s.273 varchar(255) | e-posta biçimi | Biçim doğrulama — UNIQUE (U-P2) |
| V-U3 | `DisplayName` | s.275 varchar(100) NULL | 1–100 · NULL = kullanıcı adı | NULL ≠ boş string — ayrım korunur |
| V-U4 | `Gender` | s.276 (ve s.134) enum(3) | male/female/neutral | `from(string)` fabrika — geçersizde ret |
| V-U5 | `AccountType` | s.278 enum(4) | free/premium/studio/admin | `from(string)` + `isPremium()` |
| V-U6 | `ProfileVisibility` | s.138 enum(3) | public/private/friends | `from(string)` + `isViewableBy(bool $isFriend)` |
| V-U7 | `AudioQualityPreference` | s.109 enum(4) | low/medium/high/lossless | `from(string)` + `isLossless()` |
| V-U8 | `CountryCode` | s.131 varchar(2) | ISO 3166-1 alfa-2 | 2 karakter + alfabetik ret |
| V-U9 | `Timezone` | s.133 varchar(50) DEFAULT 'UTC' | IANA kimliği | Bilinen IANA biçimi doğrulaması — veri tablosu yok ⚠️ |
| V-U10 | `CrossfadeSeconds` | s.111 int DEFAULT 0 | ≥0 tam sayı | Negatif ret; 0 = kapalı |

### §8.1 VO test edilebilirlik notları

| VO | Karakteristik test fikri (öneri) |
|---|---|
| V-U1 | 51 karakterli ad ret; boş ret |
| V-U4 | 4. değer `from()` çağrısında ret |
| V-U6 | private iken arkadaş erişimi ret |
| V-U10 | `-1` ret; `0` geçerli (kapalı) |

## §9 DOMAIN OLAY ENVANTERİ

### §9.1 Var olan olaylar (dosya + satır kanıtlı — 4 dosya, 2026-10-12 shell sayımı)

| Olay | Dosya kanıtı | `eventName` | Payload / not |
|---|---|---|---|
| `UserRegisteredEvent` | `shared/src/Events/Domain/UserRegisteredEvent.php` (96 satır — §4/6) | `user.registered` ⚠️ ad satırı doğrulanmadı (R-U9) | En uzun olay dosyası (96 satır) |
| `UserLoggedInEvent` | aynı dizin (86 satır) | `user.logged_in` ⚠️ (R-U9) | Auth sınırı olayı — sahiplik R-U10 |
| `UserLoggedOutEvent` | aynı dizin (76 satır) | `user.logged_out` ⚠️ (R-U10) | |
| `GenderSetEvent` | aynı dizin (76 satır) | `gender.set` | §6.4 — hangi katmana yazdığı açık |

### §9.2 Eksik olay adayları (öneri — uydurma YASAK)

| # | Önerilen olay | Tetikleyici kanıtı | Mevcut durum |
|---|---|---|---|
| E-U1 | `user.profile_updated` | s.139–140 profil `updated_at` | ❌ DOSYA YOK (10 dosyada yok) |
| E-U2 | `user.preferences_changed` | s.117 tercih `updated_at` | ❌ DOSYA YOK |
| E-U3 | `user.banned` / `user.unbanned` | s.280 `is_banned` | ❌ DOSYA YOK |
| E-U4 | `user.email_verified` | s.281 `is_verified` | ❌ DOSYA YOK |
| E-U5 | `user.followed` / `user.unfollowed` | s.71–81 `user_follows` | ❌ DOSYA YOK |
| E-U6 | `user.deactivated` | s.288–289 yumuşak silme | ❌ DOSYA YOK |

> **Kural:** E-U1…E-U6, `domain-bilesenler.md` §12 protokolüne tabidir —
> olay ancak dosya + `class` + `eventName` satır kanıtıyla envantere girer.

## §10 REPOSITORY SÖZLEŞMESİ ÖNERİSİ

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda `IUserRepository` **vardır** (§4/4
> dönüş dili onunla hizalı); buradaki liste **bu dalın** ihtiyacıdır ve
> mevcut arayüzün yeterliğini test eder.

| # | Yöntem (öneri) | Dönüş | Bağlı invariant / kanıt |
|---|---|---|---|
| R-U1 | `findByUsername(string $username): ?array` | kayıt veya null | U-P1 (s.291) |
| R-U2 | `findByEmail(string $email): ?array` | kayıt veya null | U-P2 (s.292) |
| R-U3 | `findByIdHex(string $id): ?array` | kayıt veya null | s.271 binary(16) PK |
| R-U4 | `createProfile(string $userId, array $data): array` | profil | U-P4 (s.144) |
| R-U5 | `setPreferences(string $userId, array $prefs): void` | — | U-P4 (s.121) |
| R-U6 | `setGender(string $userId, string $gender): void` | — | U-P3, §6.4 (iki katman — hangisine? R-U8) |
| R-U7 | `follow(string $followerId, string $followingId): bool` | evet/hayır | U-P8 (kendini takip ret — DB değil) |
| R-U8 | `softDelete(string $id): void` | — | U-P9 |

### §10.1 Sözleşme tipi notu

Dönüşler `array` (§4/4 DTO dili). `roles` alanı repository'den **dönmez** —
rol ataması K0100'ün (`user_assigned_roles` s.193–204); DTO'da görünmesi bir
**görünüm birleştirme**sidir, bu dalın mülkiyeti değil (R-U7).

## §11 KOMŞU SINIR DETAYI (§2 GENİŞLETMESİ)

| Komşu | Yön | Alan/kanıt | Ayrım kuralı |
|---|---|---|---|
| K0100-identitiy | user ↔ auth | `users` (s.270) auth şemasında; `password_hash` s.274; roller s.193–224 | Bu dal kavramı sahiplenir, **şemayı/şifreyi/rolü değil** (§2/1–2) |
| playlist | user → playlist | `user_id` referansları (playlist şeması) | "kimin listesi" dili burada, sahiplik kuralı orada |
| library | user → library | `user_favorites.user_id` (coremusic_user.sql s.61) | Koleksiyon içeriği library'de |
| playback | user → playback | `playback_queue.user_id` (s.20) + tercihler s.110–114 | Anlık oynatma playback'de; **tercih dili** burada |
| user_follows (sosyal) | user içi | s.71–81 | Takip grafiğinin sahibi bu dal mı? §12 R-U11 |
| K007-data | user → data | Şemanın normalizasyonu | Bu dal şema **üretmez** |
| media/library/playback | ← user | Her olaydaki `userId` kimliği (§2/2) | Kimlik anlamının tek kaynağı burası |

## §12 AÇIK SORULAR (§5 GENİŞLETME — R-U8…R-U12)

| # | Soru | Kanıt / işaret | Kapanış için gereken |
|---|---|---|---|
| R-U8 | `gender` SSOT'u: `users.gender` (s.276) mu, `user_profiles.theme_gender` (s.134) mu? `GenderSetEvent` hangisine yazar? | §6.4 | `GenderSetEvent` + yazan servis okuması |
| R-U9 | 4 olay `eventName()` tam değerleri | §9.1 ⚠️ (dosya var, ad satırı doğrulanmadı) | 4 dosya okuması ile kapanır |
| R-U10 | `user.logged_in`/`logged_out` sahibi: user mı K010 mu? (§5 R3 ile ilişkili) | §9.1 | `domain-bilesenler.md` §14 çizelgesi |
| R-U11 | `user_follows` (sosyal graf) bu dalın mı, ayrı bir `social` bağlamının mı? | §6.5 · `coremusic_social.sql` dosyası var (envanter) | `domain-kapsam.md` §3 çapraz okuma |
| R-U12 | `account_type` katman hakları (premium/studio/admin) hangi BC'de uygulanır? | s.278 — dal yalnız kavramı taşır (U-P12) | K0100 ile çapraz okuma |

> **§5'teki R1–R5 aynen korunur**; bu tur yalnız R-U8…R-U12 ekler.
> `status: draft` ve §3 yasağı **değişmez**.

## §13 KARAKTERİSTİK TEST SENARYOLARI (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Senaryo taslağıdır; yazılmamıştır.

| # | Senaryo | Beklenen | Bağlı kural |
|---|---|---|---|
| T-U1 | Aynı username ile ikinci hesap | DB ret | U-P1 (s.291) |
| T-U2 | Aynı email ile ikinci hesap | DB ret | U-P2 (s.292) |
| T-U3 | Geçersiz `gender` değeri | VO ret / DB ret | U-P3, V-U4 |
| T-U4 | Aynı kullanıcıya ikinci profil satırı | DB ret | U-P4 (s.144) |
| T-U5 | Geçersiz `profile_visibility` | VO ret | U-P5, V-U6 |
| T-U6 | Kendini takip | **DB ret ETMEZ** — domain ret etmeli (test bu farkı gösterir) | U-P8 (s.73–74 CHECK yok) |
| T-U7 | Soft delete iki alan birlikte | `is_deleted=1` + `deleted_at` dolu | U-P9 |
| T-U8 | `users` sil → profil/tercih | CASCADE ile düşer | U-P11 (s.123, s.147) |
| T-U9 | 3'lü `gender` kümesi dışına yazma | DB enum ret | U-P3 |
| T-U10 | `gender.set` yayın → payload | §9.1 + R-U9 | `DomainEventInterface` sözleşmesi |

## §14 BC ETKİLEŞİM MATRİSİ (YÖN + NİYET)

> Yön ve niyet §2/§11'den türetilmiştir; **çağrı mekanizması** iddiası taşımaz.

| # | Çağıran → Çağrılan | Niyet | Bağlı kural |
|---|---|---|---|
| U-BC1 | K0100 → user | Kullanıcı kavramı (kimlik doğrulama dili) | §2/1 |
| U-BC2 | user → K0100 | Parola/rol dili **dışında** | §2/1–2 |
| U-BC3 | playlist → user | "kimin listesi" kimliği | §11 |
| U-BC4 | library → user | Koleksiyon sahibi kimliği | §11 |
| U-BC5 | playback → user | Çalan kullanıcı + tercih dili | §11 |
| U-BC6 | music/media → user | `userId`/`addedBy` anlamları | §2/2 |
| U-BC7 | user → K007-data | Kavram adları (ubiquitous language) | bagimliliklar §4/1 |

## §15 ROL VE SORUMLULUK MATRİSİ (ÖNERİ)

> **TASARIM ÖNERİSİ — kanıt DEĞİL.** Depoda rollerin dağılımına dair kanıt yoktur.

| Karar alanı | Önerilen sahip | Neden | Ataç |
|---|---|---|---|
| `gender` SSOT kararı | user domain sahibi + `architect` | İki tablo (§6.4) | R-U8 |
| Username/e-posta benzersizliği invariant'ı | user domain sahibi | U-P1/U-P2 | §7 |
| Kendini takip engeli | user domain sahibi | DB ret etmez (U-P8) | §6.5 |
| `roles` görünürlüğü (DTO) | K0100 + user | Sahiplik K0100'ün | R-U7 (§10.1) |
| `account_type` hakları | K0100 | Katmanlar auth katmanı | R-U12 |
| Sosyal graf sahipliği | `domain-kapsam.md` sahibi | `coremusic_social.sql` varlığı | R-U11 |
| Olay envanteri genişlemesi | `domain-bilesenler.md` protokolü | Uydurma olay yasağı | §9.2 |

## §16 ÖRNEK ANLATIM (TUTARLILIK OKUMASI)

Bir kullanıcı kaydolurken zincir şöyledir: hesap `users`'a düşer — username
benzersizdir (U-P1, s.291), e-posta benzersizdir (U-P2, s.292); parola alanı
auth katmanınıındır (s.274 — bu dal yalnız kavramı taşır, §2/1). Profil ve
tercih 1:1 uzantılardır (U-P4, s.144/s.121); cinsiyet üçlü kümededir (U-P3)
ama **iki tabloda iki kez** tanımlıdır (§6.4) — `GenderSetEvent` hangisine
yazdığı dosyalanmamış bir boşluktur (R-U8). Görünürlük, ses kalitesi, ülke
kapalı kümelerdir (U-P5–U-P7). Takip ilişkisi tektir (s.77) ama **kendini
takip** DB'de düşmez (U-P8 — CHECK yok); bu, domain katmanına düşen bir
yüktür. Hesap silinirse profil/tercih CASCADE ile düşer (U-P11) — yumuşak
silme ikiliği (U-P9) ise yalnız `users`'tadır. Zincirin en zayıf halkaları:
gender SSOT'suzluğu (R-U8) ve olay adlarının bu turda doğrulanmamış olması
(R-U9).

## §17 OKUMA YOLU VE KOMŞU DOSYA DAVET LİSTESİ

### §17.1 Bu dosyadan okuma sırası

| Amac | Sıra |
|---|---|
| Kullanıcı dilini anlamak | §1 → §2 → §6.1–§6.2 |
| İki katmanlı gender bulgusu | §6.4 → §7.1 → §12 R-U8 |
| Sosyal sınır | §6.5 → §12 R-U11 |
| Olay envanteri | §9.1 (kanıt) → §9.2 (`❌`) → `domain-bilesenler.md` §12 |
| Kapanış | §12 (R1–R12) → §24 (ölçüm) |

### §17.2 Çağrılan komşu dosyalar

| Dosya | Neden |
|---|---|
| `domain-master.md` §24/§25 | Invariant/VO üretim kuralı |
| `domain-kapsam.md` §3 | BC envanteri — R-U11 çapraz okuma |
| `domain-bilesenler.md` §7–§12 | Olay protokolü, §14 sahiplik |
| `domain-bagimliliklar.md` §4/1 | K0100 sınırı |
| `domain-web-arastirma.md` §18.2 | Tek URL kaynağı (C4) |
| `user/index.md` satır 13 | v2'de rol tanımı yok kanıtı |

## §18 OKUYUCU UYARILARI VE ÖLÇÜM YÖNTEMİ

### §18.1 Sınırlar

1. **Bu dosya kapsam + kanıt dosyasıdır, kod değildir.** §8 VO'ları ve §10
   repository sözleşmesi bu dalda **üretilmemiştir** — okuyucu "var" sanıp çağıramaz.
2. **URL aramayın.** C4 gereği web kanıtı `domain-web-arastirma.md` §18.2'dedir.
3. **§13 testleri yazılmamıştır** — taslaktır (`qa-engineer` kapsamı).
4. **`❌ DOSYA YOK` satırları kasıtlı kayıttır** — uydurma olay eklemek §9.2
   protokolünü ihlal eder.
5. **Parola/rol/katman içeriği bu dosyada yazılmaz** (§2 yasağı — K0100).

### §18.2 Ölçüm yöntemi

| Soru | Cevap |
|---|---|
| Satır nasıl sayılır? | `[System.IO.File]::ReadAllLines(yol).Length` |
| Tahmin var mı? | Hayır — §24 tablosu araç ölçümüdür |
| Mojibake nasıl taranır? | U+4E00–U+9FFF aralığı tüm dosyada; isabet 0 beklenir |
| URL ihlali nasıl taranır? | `https?://` takibi; tek başına `http` sayılmaz |
| Authority nasıl korunur? | 2026-10-12 satırı aynen kalır, 2026-10-17 satırı eklenir |

## §19 KARAR MATRİSİ (BU DOSYA ÖLÇÜSÜNDE)

| # | Karar | Seçilen | Reddedilen | Neden |
|---|---|---|---|---|
| D-U1 | Bu turda kapsam iddiası | Kanıt sunma + öneri etiketleme | Invariant kilitleme | §3 yasağı + ADR-097 K4 |
| D-U2 | `gender` iki katman | Çelişki kaydı (§6.4) + R-U8 | Tek katmanı SSOT saymak | İki satır kanıtı var, olay yazımı yok |
| D-U3 | `email` sahipliği | Kavram burada, kolon auth şemasında kaydı | Kolonu bu dala taşımak | §2/1 yasağı |
| D-U4 | `roles` görünümü | Görünüm birleştirme notu (§10.1) | Rol kuralı yazmak | K0100 tekelinde |
| D-U5 | Kendini takip | DB-engeli yok kanıtı + domain yükü (U-P8) | CHECK var varsaymak | s.73–74'te CHECK satırı yok |
| D-U6 | Sosyal graf | Açık soru R-U11 | Bu dala dahil saymak | `coremusic_social.sql` ayrı dosya |
| D-U7 | Olay envanteri | 4 dosya kanıtlı + 6 `❌ DOSYA YOK` | `eventName` uydurmak (R-U9) | Zero-hallucination |

## §20 AKIŞ SENARYOLARI (TASARIM ÖNERİSİ — kanıt DEĞİL)

> Senaryolar §7 invariant adaylarından türetilmiştir; implementasyon yoktur.

### §20.1 Kayıt

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | K0100 (kayıt akışı) | Kayıt dili user kavramından gelir | §2/1 |
| 2 | user | Username çakışması ret | U-P1 |
| 3 | user | Email çakışması ret | U-P2 |
| 4 | user | `user.registered` yayılır | §9.1 (ad R-U9) |

### §20.2 Profil güncelleme

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | user | Profil 1:1 — ikinci satır ret | U-P4 |
| 2 | user | `gender` 3'lü kümeyi aşarsa ret | U-P3, V-U4 |
| 3 | user | Görünürlük 3'lü kümeyi aşarsa ret | U-P5 |
| 4 | user | `gender.set` yayılır — **hedef katman R-U8** | §6.4 |

### §20.3 Takip

| Adım | Actor | Beklenen davranış | Bağlı kural |
|---|---|---|---|
| 1 | user | Aynı çift ikinci kez ret | s.77 UNIQUE |
| 2 | user | Kendini takip ret (**domain** — DB ret etmez) | U-P8 |

## §21 SONRAKİ TUR İŞ KALEMLERİ

| # | İş | Bağlı soru/borç | Çıktı |
|---|---|---|---|
| J-U1 | 4 olay dosyasının `eventName()` okuması | R-U9 | Adlar doğrulanır |
| J-U2 | `GenderSetEvent` + yazan servis taraması | R-U8 | gender SSOT kararı |
| J-U3 | `coremusic_social.sql` okuması | R-U11 | Sosyal graf sahipliği |
| J-U4 | `user.logged_in` sahiplik çizelgesi | R-U10 | `domain-bilesenler.md` §14 |
| J-U5 | `domain-dogrulama.md` tur sonu raporu | §24 | Ölçüm (a–d) |

> **Kapanış bu tur:** §1–§5 aynen korundu · §3 yasağı işlemede ·
> `status: draft` · hiçbir ⚠️/UNKNOWN kapatılmadı · Authority 2 satır.

## §22 OLGUNLUK BANDI (BU DOSYA ÖLÇÜSÜNDE)

| Katman | Durum | Kanıt | Not |
|---|---|---|---|
| Şema (hesap+profil+tercih+sosyal) | ✅ VAR | auth s.270–296 · user s.71–148 | 4 tablo · 4 UNIQUE · 6 enum |
| Domain olay | ✅ VAR (4) | Events/Domain/ 4 dosya | `eventName` ⚠️ R-U9 |
| DTO | ✅ VAR | UserResponse.php s.20–26, 96 | 7 alanlı |
| VO sınıfı | ❌ YOK | depo taraması | §8 yalnız şemadan türetildi |
| Invariant listesi (kilitli) | ❌ YOK | ADR-097 K4 | §7 kilitlenmemiş |
| gender SSOT | ❌ YOK | §6.4 | R-U8 |
| Test | ❌ YOK | §13 taslak | `qa-engineer` kapsamı |

**Okuma:** user dalı olay bakımından K008'in **en zengin** çocuğudur (4 olay),
ama dil katmanı (VO/kural) şemanın gerisindedir — music ile ortak profil.

## §23 RİSK VE TEKNİK BORÇ KAYDI

| # | Risk / borç | Kanıt | Ağırlık | Ataç |
|---|---|---|---|---|
| B-U1 | `gender` iki tabloda — SSOT yok | §6.4 | YÜKSEK — veri tutarsızlığı | R-U8 |
| B-U2 | 4 olay adı doğrulanmadı | §9.1 ⚠️ | ORTA — sözleşme belirsizliği | R-U9 |
| B-U3 | Kendini takip DB'de düşmüyor | U-P8 (s.73–74) | ORTA — domain yükü yazılmadı | §7 |
| B-U4 | `roles` görünümü sahiplik belirsiz | §10.1 | ORTA | R-U7 |
| B-U5 | Sosyal graf sahipliği açık | R-U11 | DÜŞÜK — sınır kararı | domain-kapsam |
| B-U6 | 6 eksik olay (E-U1…E-U6) | §9.2 `❌` | DÜŞÜK — envanter boşluğu | §9.2 |

**Kural:** bu tablodaki satırlar **silinmez**; kapanış yalnız kanıt eklenerek
yapılır (`domain-dogrulama.md` §9 defterine işlenir).

## §24 TUR SONU ÖLÇÜM (2026-10-17)

| Metrik | Değer | Yöntem |
|---|---|---|
| Ekleme öncesi satır | 85 | `[System.IO.File]::ReadAllLines().Length` |
| Eklenen bölüm | §6–§24 (19 bölüm) | append-only; §1–§5 + Authority 2026-10-12 korundu |
| Invariant adayı | 12 (U-P1…U-P12) | hepsi şema satır kanıtlı; kilitlenmemiş |
| Uyumsuzluk kaydı | 4 (§7.1) | hepsi ⚠️ — gender/email/roles/takip |
| VO adayı | 10 (V-U1…V-U10) | hepsi `TASARIM ÖNERİSİ — kanıt DEĞİL` etiketli |
| Olay kaydı | 4 dosya kanıtlı + 6 `❌ DOSYA YOK` | dosya + satır kanıtı (§9) |
| Açık soru | 5 (§5) + 5 yeni (R-U8…R-U12) = 10 | §5 kayıtları aynen korundu |
| Test senaryosu | 10 (T-U1…T-U10) | taslak — yazılmadı |
| Kapanan ⚠️ | **0** | §3 yasağı gereği hiçbir ⚠️/UNKNOWN kapatılmadı |
| Yeni URL | 0 | C4 — tek URL kaynağı `domain-web-arastirma.md` §18.2 |
| Authority | eski (2026-10-12) korundu + yeni eklendi | master §22 M-6 |

## §25 UBİQUİTOUS LANGUAGE GENİŞLETME (kanıt satırlı sözlük)

> Bu tablo §3/5'teki 4 terimin (user · displayName · roles · gender)
> **şema karşılıklarını** ekler; terim uydurulmaz, her satır kanıt satırı taşır.

| Terim (dal dili) | Şema/olay karşılığı | Kanıt satırı | Not |
|---|---|---|---|
| kullanıcı | `users` | s.270 | tablo adı kendisi |
| kullanıcı adı | `username` | s.272 | UNIQUE s.291 |
| görünen ad | `display_name` | s.275 | NULL opsiyonel |
| e-posta | `email` | s.273 | UNIQUE s.292 — **kolon auth şemasında** |
| cinsiyet (hesap) | `gender` | s.276 | enum 3'lü |
| cinsiyet (tema) | `theme_gender` | s.134 | enum 3'lü — §6.4 çelişkisi |
| avatar | `avatar_url` | s.277 | |
| hesap katmanı | `account_type` | s.278 | enum 4'lü — haklar K0100'ün |
| görünür profil | `profile_visibility` | s.138 | enum 3'lü |
| tercih | `user_preferences` | s.104 | tablo |
| takip | `user_follows` | s.71 | UNIQUE s.77 |
| kayıtlı | `user.registered` | §9.1 | olay dili — ad ⚠️ R-U9 |
| giriş yapmış | `user.logged_in` | §9.1 | olay dili — sahiplik R-U10 |

**Sınır:** `password`/`role`/`session` sözcükleri bu sözlükte **yer almaz** —
bunlar K0100'ün dilidir (§2 yasağı); `roles` yalnız **görünüm** olarak DTO'da
geçer (§10.1).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
