---
title: "music — Örnekler"
type: architecture-examples
node_id: K008-domain/music
node_type: subdomain-scope
status: draft
parent: K008-domain
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# music — Gerçek Kod & Doküman Örnekleri

> **Kural:** §1'deki her snippet bu oturumda dosyadan okunarak **shell ile doğrulanmıştır**
> (satır no ile). Uydurma kod yazmak yasaktır; karşılığı olmayan çocukta `— yok` yazılır.

## §1 Gerçek repo kodu (öncelik 1 — `shared/src/`)

**1.1 Katalog karşılığı — `shared/src/Api/Dto/Response/MusicResponse.php` (satır 17–27):**

```php
final class MusicResponse
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $artist,
        private readonly string $album,
        private readonly int $duration,
        private readonly string $coverUrl,
        private readonly string $streamUrl
    ) {}
```

**1.2 Domain event — `shared/src/Events/Domain/MusicAddedEvent.php` (satır 20–40):**

```php
final class MusicAddedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $musicId,
        private readonly string $addedBy,
        private readonly string $title
    ) { $this->occurredOn = new \DateTimeImmutable(); }

    public function eventName(): string
    {
        return 'music.added';
    }
```

**1.3 Arama filtresi (katalog keşfi) — `shared/src/Api/Dto/Request/MusicSearchRequest.php` (satır 17–25):**

```php
final class MusicSearchRequest
{
    public function __construct(
        private readonly string $query = '',
        private readonly string $genre = '',
        private readonly string $artist = '',
        private readonly int $page = 1,
        private readonly int $limit = 20
    ) {}
```

> **Kapsam disiplini:** bu üç sınıf `shared/` (paylaşılan altyapı) altındadır; K008 music
> **domain katmanı** için ayrı aggregate sınıfı: **— yok** (ADR-097 K4 bekleniyor).

## §2 Resmi doküman örneği

**Kaynak:** Spotify Web API — Get Track · https://developer.spotify.com/documentation/web-api/reference/get-track (2026-10-12 açıldı)

Yanıt şemasından özet (sayfadan birebir):

```json
{
  "album":     { "album_type": "compilation", "id": "2up3OPMp9Tb4dAKM2erWXQ",
                 "name": "string", "type": "album", "uri": "spotify:album:..." },
  "artists":   [ { "id": "string", "name": "string", "type": "artist" } ],
  "duration_ms": 0,
  "id":        "string",
  "name":      "string",
  "type":      "track",
  "uri":       "string",
  "is_local":  false
}
```

**Neden bu örnek:** bir *katalog kavramı* kimliği (`id`), adı (`name`) ve ilişkili
varlıklarla (`album` · `artists`) yaşar; CoreMusic `MusicResponse` DTO'su aynı üçlüyü
`id · title · artist · album` olarak taşır — **alan adları birebir değildir, eşleme K013'ün konusudur**.

## §3 Uygulama notu

1. Katalog dili tek yazılmazsa `title`/`name` ikiliği doğar; DDD Reference buna
   *ubiquitous language* der → tek sözlük K008'de tutulur (`domain-bagimliliklar.md` §4/1).
2. `music.added` olayı `shared/src/Events/Domain/` altındadır ve ADR-086'ya bağlıdır;
   olayı **yeniden** tanımlamak yerine K008 kuralı bu olayı **okur**.

## §4 İlişkili dosyalar

| Dosya | Ne verir |
|---|---|
| `music-kapsam.md` | ELI15 rol · içinde/dışında · DDD model notu · disk kanıtları · açık sorular |
| `music-dogrulama.md` | 15 satırlık iddia tablosu (URL/dosya · tarih · ✅/⚠️/UNKNOWN) |
| `../domain-master.md` | çocuk tablosu §3 satır 74 · sorumluluk §1 satır 40 |
| `../domain-bagimliliklar.md` | K008 ↔ K007 kavram adları paylaşımı §4 satır 47 |
| `shared/src/Api/Dto/Response/MusicResponse.php` | katalog DTO'su (7 alan) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
