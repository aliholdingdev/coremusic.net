---
title: API DESIGN PATTERNS — REST, GraphQL, gRPC, WebSocket
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# API DESIGN PATTERNS
# Prompt Maker v7.2.0 | 2026-06-11

## REST API Design

**HTTP Methods:**
- GET: Retrieve resource (safe, idempotent)
- POST: Create resource
- PUT/PATCH: Update resource (PUT = full, PATCH = partial)
- DELETE: Remove resource

**Status Codes:**
- 200: OK (success)
- 201: Created (POST success)
- 204: No Content (DELETE success)
- 400: Bad Request (client error)
- 401: Unauthorized (not authenticated)
- 403: Forbidden (authenticated but no permission)
- 404: Not Found (resource doesn't exist)
- 429: Too Many Requests (rate limited)
- 500: Internal Server Error

**Resource Naming:**
```
/api/v1/users          (collection)
/api/v1/users/123      (specific resource)
/api/v1/users/123/playlists  (nested)
/api/v1/users/123/playlists/456/tracks  (deep nesting OK if meaningful)
```

**Versioning:**
- URL path: `/api/v1/`, `/api/v2/`
- Header: `Accept: application/vnd.company.v1+json`
- Query: `/api/users?version=v1`
- **Recommended:** URL path (explicit, backward-compat easy)

**Pagination:**
```
/api/v1/tracks?limit=50&offset=0
/api/v1/tracks?limit=50&page=1

Response:
{
  "data": [...],
  "pagination": {
    "total": 5000,
    "limit": 50,
    "offset": 0
  }
}
```

**Filtering & Sorting:**
```
/api/v1/tracks?genre=rock&year=2020&sort=popularity,-release_date
```

## GraphQL API Design

**Single Endpoint:**
```graphql
POST /graphql

query GetUser {
  user(id: 123) {
    id
    name
    email
    playlists {
      id
      title
      tracks(limit: 10) {
        id
        title
        artist
      }
    }
  }
}
```

**Benefits:** No over-fetching (request exact fields), no N+1 (nested query resolved server-side)

**Authentication:** Bearer token in Authorization header (same as REST)

## gRPC Design

**Protocol Buffer IDL:**
```protobuf
service UserService {
  rpc GetUser(GetUserRequest) returns (User);
  rpc ListTracks(ListTracksRequest) returns (stream Track);
}
```

**Benefits:** Binary protocol (small payload), streaming (bidirectional), strong typing

**Trade-off:** Requires gRPC client library (not browser-native)

## WebSocket API (Real-Time)

**Connection & Handshake:**
```
GET /ws HTTP/1.1
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Key: [random base64]
Sec-WebSocket-Version: 13
```

**Message Format (JSON):**
```json
// Client → Server (command)
{
  "type": "subscribe",
  "channel": "spectrum",
  "apiVersion": 2
}

// Server → Client (event)
{
  "type": "spectrum_update",
  "data": [base64_float32_array],
  "timestamp": "2026-06-11T10:30:00Z"
}
```

**CoreMusic Ecosystem Uses:**
- Neva Engine 9742: EQ control, spectrum streaming
- Neva Player 9743: video, RTC signaling
- Download Service 3001: queue events

## Authentication Patterns

**JWT (JSON Web Token):**
```
Header: {
  "alg": "EdDSA",
  "typ": "JWT"
}
Payload: {
  "sub": 123,
  "email": "user@example.com",
  "iat": 1234567890,
  "exp": 1234567890 + 3600
}
Signature: EdDSA(header + payload, privateKey)
```

**OAuth2 (Third-Party):**
```
1. User clicks "Login with Google"
2. Redirect to https://accounts.google.com/o/oauth2/v2/auth?...
3. User logs in, grants permission
4. Google redirects to callback_url?code=...
5. App exchanges code for access_token (backend, secure)
6. App uses access_token to call Google API on behalf of user
```

**API Key (Simple):**
```
X-API-Key: sk_live_...

Better: Use with rate limiting and IP whitelist
```

**Recommended:** JWT for user sessions, OAuth2 for third-party integrations

## Rate Limiting

**Per IP:** 60 requests / 60 seconds
**Per User:** 100 requests / hour
**Per Endpoint:** Custom limits (login: 5/min, search: 30/min)

**Response Header:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1234567890
```

**Exceeding:** Return 429 Too Many Requests with Retry-After header

## Error Response Format

**Consistent Structure:**
```json
{
  "error": {
    "code": "INVALID_REQUEST",
    "message": "Missing required field: email",
    "details": [
      {
        "field": "email",
        "reason": "REQUIRED"
      }
    ]
  }
}
```

**Never:**
- Expose stack traces to client
- Include sensitive data in errors
- Use vague messages ("error occurred")

## Caching

**HTTP Headers:**
```
Cache-Control: public, max-age=3600      (1 hour)
Cache-Control: private, max-age=60       (user-specific)
Cache-Control: no-cache, no-store        (never cache)
ETag: "abc123"                           (unique version)
Last-Modified: Wed, 11 Jun 2026 10:00:00 GMT
```

**Conditional Requests:**
```
GET /api/v1/tracks/123
If-None-Match: "abc123"       (if ETag matches, 304 Not Modified)
If-Modified-Since: ...        (if not modified, 304)
```

## Documentation

**OpenAPI/Swagger (Standard):**
```yaml
openapi: 3.0.0
info:
  title: CoreMusic API
  version: 1.0.0
paths:
  /api/v1/users/{userId}:
    get:
      summary: Get user by ID
      parameters:
        - name: userId
          in: path
          required: true
          schema:
            type: integer
      responses:
        200:
          description: User found
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/User'
        404:
          description: User not found
```

**Benefits:** Auto-generates client SDKs, interactive docs (Swagger UI)

## API Versioning Strategy

**Do:**
- Version by URL path `/v1/`, `/v2/`
- Publish deprecation timeline (v1 EOL: 2027-12-31)
- Support multiple versions simultaneously (3-6 months overlap)

**Don't:**
- Deprecate without warning
- Break contracts within version
- Add required fields without migration

---

Quality Score (2026-06-11): 98.7%
