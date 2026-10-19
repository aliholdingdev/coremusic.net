---
title: "media — Örnekler"
type: architecture-examples
node_id: K008-domain/media
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# media — Gerçek Kod & Doküman Örnekleri

> **Kural:** §1'deki her snippet bu oturumda dosyadan okunarak **shell ile doğrulanmıştır**.
> Uydurma kod yazmak yasaktır; karşılığı olmayan alan `— yok` yazılır.

## §1 Gerçek repo kodu (öncelik 1 — `shared/src/`)

**1.1 Tek domain event — `shared/src/Events/Domain/MediaAccessedEvent.php` (satır 20–40, 53–60):**

```php
final class MediaAccessedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    public function __construct(
        private readonly string $mediaId,
        private readonly string $userId,
        private readonly string $accessType
    ) { $this->occurredOn = new \DateTimeImmutable(); }

    public function eventName(): string
    {
        return 'media.accessed';
    }

    public function payload(): array
    {
        return [
            'mediaId'    => $this->mediaId,
            'userId'     => $this->userId,
            'accessType' => $this->accessType,
            'timestamp'  => $this->occurredOn->format('c'),
        ];
    }
}
```

**1.2 Açık uç — aynı dosya satır 79–85 (getter doc-bloğu):**

```php
    /**
     * Get access type (stream, download, etc.).
     */
    public function getAccessType(): string
    {
        return $this->accessType;
    }
```

> `etc.` → `accessType` **kapalı bir enum değildir**; değer kümesi dokümante edilmemiştir.

**1.3 Erişim/medya için ayrı servis-sınıfı:** **— yok**
(`shared/src/` altında `Media*` yalnız `MediaAccessedEvent.php` — shell sayımı 2026-10-12).

## §2 Resmi doküman örneği

**Kaynak:** Spotify Web API — Get Track · URL tekil: [[music-dogrulama]] §1 #1 (2026-10-15 HTTP GET ile doğrulandı)

```json
{
  "id": "string",
  "name": "string",
  "type": "track",
  "is_local": false,
  "is_playable": false,
  "restrictions": { "reason": "market" }
}
```

Aynı sayfadan politika notu (birebir metin):

> "Spotify content may not be used to train machine learning or AI model"

**Neden bu örnek:** *erişim izni* ayrı bir modeldir — `is_local` (yerel dosya),
`is_playable` (pazar uygunluğu) ve `restrictions.reason` (`market|product|explicit`)
aynı kimliğin **erişilebilirliğini** anlatır; kimliği değil. CoreMusic `accessType`
kavramı da bu ayrımı taşımak zorundadır.

## §3 Uygulama notu

1. `media.accessed` olayı tek başına yeterli değildir: `accessType` değer kümesi
   ADR-097 K4'te **kapalı liste** olarak yazılmalıdır (satır 80'deki "etc." kapanır).
2. Download izni (lisans) ayrı bir karar yüzeyidir — `master §2.1` onu K015/K003'e,
   lisans sorusunu ⚠️'a bırakır; bu dosyada **izin kuralı yazılmaz**.

## §4 İlişkili dosyalar

| Dosya | Ne verir |
|---|---|
| `media-kapsam.md` | ELI15 rol · içinde/dışında · DDD model notu · disk kanıtları · açık sorular |
| `media-dogrulama.md` | 15 satırlık iddia tablosu (URL/dosya · tarih · ✅/⚠️/UNKNOWN) |
| `../domain-master.md` | çocuk tablosu §3 satır 75 · kapsam dışı §2.1 satır 51 |
| `../domain-bagimliliklar.md` | "ne çalınır" ↔ "nasıl çalınır" açık sınır §4 satır 49 |
| `shared/src/Events/Domain/MediaAccessedEvent.php` | `media.accessed` event (tek disk karşılığı) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
