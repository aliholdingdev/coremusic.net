---
title: "user — Örnekler"
type: architecture-examples
node_id: K008-domain/user
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# user — Gerçek Kod & Doküman Örnekleri

> **Kural:** §1'deki her snippet bu oturumda dosyadan okunarak **shell ile doğrulanmıştır**.
> Uydurma kod yazmak yasaktır; karşılığı olmayan alan `— yok` yazılır.

## §1 Gerçek repo kodu (öncelik 1 — `shared/src/`)

**1.1 Kullanıcı DTO'su — `shared/src/Api/Dto/Response/UserResponse.php` (satır 17–27):**

```php
final class UserResponse
{
    public function __construct(
        private readonly string $id,
        private readonly string $username,
        private readonly string $email,
        private readonly string $displayName,
        private readonly string $avatar,
        private readonly string $gender,
        private readonly array $roles
    ) {}
```

**1.2 Domain event — `shared/src/Events/Domain/UserLoggedInEvent.php` (satır 20–40):**

```php
final class UserLoggedInEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $ipAddress,
        private readonly string $userAgent
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function eventName(): string
    {
        return 'user.logged_in';
    }
}
```

**1.3 `shared/src/Events/Domain/` altındaki kullanıcı olayları (shell sayımı):**

```
GenderSetEvent.php · PasswordResetRequestedEvent.php
UserLoggedInEvent.php · UserLoggedOutEvent.php · UserRegisteredEvent.php
```

> **Kapsam disiplini:** `roles` alanı `UserResponse` içinde **taşınır** ama rol
> **tanımı** K0100-identitiy'nindir (`domain-master.md` §2.1 satır 55) — bu DTO'yu
> yetki kaynağı gibi okumak yasaktır. K008 user domain'i için ayrı aggregate sınıfı: **— yok**.

## §2 Resmi doküman örneği

**Kaynak:** Spotify Web API (Reference nav) · URL tekil: [[domain-web-arastirma]] §1 #69 (2026-10-12 kökte doğrulandı)

```
Users        → Get Current User's Profile · Get User's Profile · Follow Playlist
Library      → Save Items to Library · Remove Items from Library · Check User's Saved Items
Playlists    → Create Playlist · Get Current User's Playlists
```

**Neden bu örnek:** resmi doküman **kullanıcı profili** ile **kütüphaneyi** ayrı referans
gruplarında tutar → "kim" (user) ile "ne biriktirdi" (library) ayrı bounded context'tir.
CoreMusic'te de `UserResponse` (profil) ile `MusicAddedEvent::addedBy` (koleksiyon sahibi)
ayrı kavramlardır ve ikisi de tek `userId` kimliğine bağlanır.

## §3 Uygulama notu

1. `userId` bu dalın **paylaşılan kimliği**dir; playback · playlist · library olayları
   onu **okur**, tanımlamaz.
2. Parola/oturum olayları (`PasswordResetRequestedEvent`) burada **kapsanmaz** —
   K0100-identitiy'ye ait kanıt olarak §4'te listelenir.

## §4 İlişkili dosyalar

| Dosya | Ne verir |
|---|---|
| `user-kapsam.md` | ELI15 rol · içinde/dışında · DDD model notu · disk kanıtları · açık sorular |
| `user-dogrulama.md` | 15 satırlık iddia tablosu (URL/dosya · tarih · ✅/⚠️/UNKNOWN) |
| `../domain-master.md` | çocuk tablosu §3 satır 76 · kapsam dışı §2.1 satır 55 |
| `shared/src/Api/Dto/Response/UserResponse.php` | kullanıcı profil DTO'su (7 alan) |
| `shared/src/Events/Domain/` | 5 kullanıcı domain event'i (shell sayımı 2026-10-12) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
