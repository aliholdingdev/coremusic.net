# API-First Sözleşme Akışı

> Kaynak: `.ai/CLAUDE.md` §6A (ADR-084) — tekil ADR-084/ADR-086 dosyaları diskte YOK
> (`⚠️ VERIFICATION REQUIRED`); bağlayıcı içerik §6A metnidir.

## 1. Sözleşme Zinciri (hiçbir endpoint doğrudan kodlanmaz)

```text
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

1. **OpenAPI Spec** — önce sözleşme yazılır (K9 katmanı: "API sözleşmesi ihlal edilemez").
2. **DTO** — spec'ten türetilen veri transfer nesnesi.
3. **Contract** — istemci-sunucu sözleşmesi sabitlenir.
4. **Validation** — pipeline #10 Validation middleware'inde DTO doğrulaması.
5. **Use Case** — iş mantığı.
6. **Kod** — repository/PDO implementasyonu (bkz. [pdo-rules.md](pdo-rules.md)).

## 2. API Gateway

- Tek giriş noktası: **`api.coremusic.net`**.
- Gateway görevleri: routing, auth, rate limit, validation, logging, **correlation ID**.

## 3. BFF × 6 (§6A.2)

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

## 4. CQRS (§6A.3)

```text
Write: Command → Use Case → Repository → MySQL Master
Read:  Query  → Read Model → Cache → Response
```

Yazma ve okuma tamamen ayrılır; okuma yolu read model + cache üzerinden döner.

## 5. Event Driven — Event Bus PSR-14 (§6A.4, ADR-086)

```text
Service A → Event Bus (PSR-14) → Service B, C, D
```

**Servisler birbirini doğrudan çağırmaz** (K8 hard guardrail) — yalnızca event yayınlar.

## 6. SPA → ApiClient Kuralı (§6A.5)

```text
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure
```

SPA **asla** PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis,
Cache veya **SQL görmez.** Frontend kodunda bu isimlerin geçmesi ihlaldir (→ `ui-*` değil,
backend'e raporlanır).

## 7. Portlar (§11 — çakışma yasak)

| Port | Servis |
|------|--------|
| 80 | admin.coremusic.net |
| 81 | music.coremusic.net (Control) |
| 3001 | download.coremusic.net (HTTP/WS) |
| 5000/6000 | media.coremusic.net |
| 3306 | MySQL (TCP) |