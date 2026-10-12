# Örnek: Yeni Playlist Endpoint'i (tam döngü)

> Senaryo: kullanıcı playlist'lerini listeleyen `GET /api/playlists` endpoint'i.
> Akış: OpenAPI Spec → DTO → Validation → Use Case → PDO Repository.
> DB: `coremusic_playlist` (§18 #5 — user playlists) · Port: api.coremusic.net gateway →
> Control (81) · Middleware sırası §6'dır, DEĞİŞTİRİLMEZ.

## Adım 1 — OpenAPI Spec (önce sözleşme)

```yaml
# openapi/playlists.yaml
paths:
  /api/playlists:
    get:
      summary: Kullanıcının playlist'lerini listeler
      responses:
        "200":
          description: Playlist listesi
          content:
            application/json:
              schema:
                type: array
                items: { $ref: "#/components/schemas/PlaylistDto" }
        "401": { description: Auth gerekli }
        "429": { description: Rate limit (60 req/60s) }
components:
  schemas:
    PlaylistDto:
      type: object
      required: [id, name]
      properties:
        id:   { type: integer }
        name: { type: string }
        user_id: { type: integer }
```

## Adım 2 — DTO

```php
declare(strict_types=1);

final readonly class PlaylistDto
{
    public function __construct(
        public int $id,
        public string $name,
        public int $userId,
    ) {}
}
```

## Adım 3 — Validation (pipeline #10)

```php
// Giriş: query param yok; kimlik session'dan (pipeline #5 → #8) gelir.
// Doğrulama yalnız DTO şeması üzerinde yapılır — raw input asla sorguya gitmez.
$errors = [];
if ($userId <= 0) { $errors['user'] = 'invalid'; }
// $errors dolu → 422; boş → Adım 4
```

## Adım 4 — Use Case (CQRS: Query → Read Model)

```php
final class ListPlaylistsQueryHandler
{
    public function __construct(private PlaylistRepository $repo) {}

    /** @return list<PlaylistDto> */
    public function handle(int $userId): array
    {
        $rows = $this->repo->findForUser($userId); // Query yolu: cache/read model §6A.3
        return array_map(
            static fn (array $r): PlaylistDto => new PlaylistDto((int)$r['id'], (string)$r['name'], (int)$r['user_id']),
            $rows
        );
    }
}
```

## Adım 5 — PDO Repository (ADR-002 prepared + explicit kolonlar)

```php
declare(strict_types=1);

final class PlaylistRepository
{
    public function __construct(private \PDO $pdo) {} // bağlantı: ADR-002 §2.2 a imzası

    /** @return list<array<string, mixed>> */
    public function findForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, user_id, created_at
               FROM playlists
              WHERE user_id = :uid
              ORDER BY created_at DESC
              LIMIT 50'
        ); // ❌ yasak: SELECT * · ❌ yasak: concat · ✅ placeholder
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

## Adım 6 — Middleware sırası (doğru uygulama)

İstek `GET /api/playlists` sırasıyla şunlardan geçer (hiçbiri atlanmaz/ötelemez):

```text
OriginCheck(1) → Cors(2) → RateLimiter(3, APCu 60/60s) → SecurityHeaders(4, CSP nonce üretir)
→ SessionManager(5, nonce'u session'a kaydeder, 3600s) → [GET: Csrf(6) yalnız POST/PUT/DELETE doğrular]
→ BypassAuth(7, prod kapalı) → Auth(8, JWT+session) → Permission(9, RBAC regular/premium/…)
→ Validation(10, DTO) → Controller(ListPlaylistsQueryHandler)
```

## Çıktı kontrolü

| Kontrol | Durum |
|---------|-------|
| Sözleşme önce yazıldı (§6A) | ✅ Adım 1 |
| Prepared + explicit kolon (ADR-002/§21) | ✅ Adım 5 |
| ORM / `SELECT *` / concat yok | ✅ |
| Middleware sırası değişmedi (Guardrail #7) | ✅ Adım 6 |
| CSRF adı `csrf_token` (GET'te gerekmez; POST varyantında zorunlu) | ✅ |
| Rate limit 60/60s gateway'de (§6A.1) | ✅ |
| Frontend yalnız ApiClient üzerinden çağırır (§6A.5) | ✅ |
| Test yok → "tamam" denmez (Execution step 4) | ⚠️ PHPUnit ≥80% hedefi (§17) bekler |