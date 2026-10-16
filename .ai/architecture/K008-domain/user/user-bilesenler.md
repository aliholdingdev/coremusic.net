---
node_id: "K008-user-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/user-kapsam.md"
evidence:
  - "shared/src/Events/Domain/ — user event'leri: UserRegisteredEvent · UserLoggedInEvent · UserLoggedOutEvent · PasswordResetRequestedEvent · GenderSetEvent (hepsi satır 20; 2026-10-15 dizin)"
  - ".ai/.sql/mysql/coremusic_user.sql satır 104–165 (user_preferences · user_profiles) + coremusic_auth.sql satır 270 (users)"
validation: "2026-10-15 — 5 user event + 2 şema dosyası kanıtlandı"
open_questions:
  - "Kademe-3 adları ADR-097 K4 bekliyor"
  - "GenderSetEvent akış seçimi (select-gender) ile UI bağının sözleşmesi → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# user — Bileşenler (K008 · kademe-3 planı)

> **Authority: Bayram Ali / Vault Steward.** `user` BC'si kademe-3 **taslak** planı;
> kanıt [[user-dogrulama]] içindedir, bu dosya yalnız planı taşır.

## §1 KADeme-3 ALT DÜĞÜM ADAYLARI

| # | Alt düğüm (aday) | Kapsamı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | `user-kimlik` | kayıt/giriş/çıkış olayları | `Events/Domain/UserRegisteredEvent.php` · `UserLoggedInEvent.php` · `UserLoggedOutEvent.php` (satır 20, 2026-10-15) | ✅ dosya kanıtlı |
| 2 | `user-sifre` | parola sıfırlama olayı | `PasswordResetRequestedEvent.php` satır 20 | ✅ dosya kanıtlı |
| 3 | `user-cinsiyet` | cinsiyet tercihi olayı | `GenderSetEvent.php` satır 20 | ✅ dosya kanıtlı |
| 4 | `user-profil` | profil + tercih aggregate adayı | `coremusic_user.sql` satır 104 (`user_preferences`), 126 (`user_profiles`) | ⏳ ADR-097 K4 |
| 5 | `user-oturum` | oturum/rol persistence | `coremusic_auth.sql` satır 227 (`user_sessions`), 213 (`user_roles`) | ⏳ data katmanı sahibi |

## §2 SINIR KOMŞULARI

| Komşu | Yön | Ne geçer | Kanıt |
|---|---|---|---|
| `library` | user → library | favori/geçmiş **kullanıcıya** bağlı, profil değil | `user-kapsam.md` §2 |
| `recommendation` | user → rec | tercih profili veri olarak (JSON) geçer | `coremusic_ai.sql` satır 117 (`user_preference_profiles`) |
| security katmanı | user ↔ auth | şifre/oturum **uygulaması** dışarıda | master §2.2 |

## §3 OLGUNLUK MATRİSİ

| Bileşen | Kod | Şema | Test | Olgunluk |
|---|---|---|---|---|
| 5 domain event | ✅ `Events/Domain/User*.php` | — | ⚠️ test → VERIFICATION REQUIRED | Orta |
| profil/tercih | — | ✅ `coremusic_user.sql` 104–165 | — | Orta |
| yetkilendirme | ⚠️ uygulama security katmanında | ✅ `coremusic_auth.sql` 193–227 | — | Sınır dışı (sahip: security) |

**Kardeş dosyalar:** [[user-kapsam]] · [[user-dogrulama]] · [[user-web-arastirma]] · [[user-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
