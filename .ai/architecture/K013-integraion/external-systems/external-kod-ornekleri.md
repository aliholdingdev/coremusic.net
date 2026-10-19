---
node_id: K013-integraion/external-systems/external-kod-ornekleri
node_type: architecture-code-examples
title: "Dış Sistemler — Dil Bazlı Kod Örnekleri"
type: reference
status: active
date: 2026-10-16
parent_id: K013-integraion/external-systems
children: []
depends_on:
  - K013-integraion/external-systems/external-anayasa
  - K013-integraion/external-systems/external-dogrulama
evidence:
  - "https://www.rfc-editor.org/rfc/rfc7636"
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow"
  - "https://developers.soundcloud.com/docs"
  - "https://musicbrainz.org/doc/MusicBrainz_API"
validation:
  - "Tüm örneklerde secret/anahtar yalnız PLACEHOLDER (anayasa P5, doğrulama GATE-EXT-4)"
  - "Akış adımları external-web-arastirma.md §2 kaynaklarıyla birebir"
open_questions:
  - "Hangi dillerin 1. dalga adaptörü olacağı (13 dil envanterde, kod child'da)"
group: kod
---

# Dış Sistemler — Dil Bazlı Kod Örnekleri

> **Tarih:** 2026-10-16 · **Parent:** `K013-integraion/external-systems` ·
> **Mode:** Red Team · Human Mode · Truth Mode
> **Amaç:** Her dil için **minimal, akış-doğru** bir örnek — adaptör kodunun
> nasıl görüneceğini gösterir. Bunlar referans taslaktır; üretim adaptörü
> child seviyede (`adapters/`) yazılır.
>
> **GÜVENLİK (P5):** Bütün örneklerde anahtar/secret/token **yalnız
> `PLACEHOLDER`** biçimindedir. Gerçek değer hiçbir `.md`'ye girmez.

## 0. Ortak PLACEHOLDER Sözleşmesi

```text
SPOTIFY_CLIENT_ID=PLACEHOLDER_CLIENT_ID
SPOTIFY_CLIENT_SECRET=PLACEHOLDER_CLIENT_SECRET
APPLE_MUSIC_KEY_ID=PLACEHOLDER_KEY_ID          # 10 karakter, secret store
APPLE_MUSIC_TEAM_ID=PLACEHOLDER_TEAM_ID
APPLE_MUSIC_PRIVATE_KEY_PATH=/secrets/AuthKey_PLACEHOLDER.p8   # dosya md'ye GİRMEZ
SOUNDCLOUD_CLIENT_ID=PLACEHOLDER_CLIENT_ID
SOUNDCLOUD_CLIENT_SECRET=PLACEHOLDER_CLIENT_SECRET
LASTFM_API_KEY=PLACEHOLDER_API_KEY
LASTFM_API_SECRET=PLACEHOLDER_API_SECRET
DISCOGS_TOKEN=PLACEHOLDER_TOKEN
```

---

## 1. PHP

```php
<?php
// Dil: PHP — Rol: Spotify Auth Code + PKCE (S256) — kaynak: developer.spotify.com PKCE flow
declare(strict_types=1);

final class SpotifyPkce
{
    public function __construct(
        private string $clientId = 'PLACEHOLDER_CLIENT_ID',
        private string $redirectUri = 'https://coremusic.net/callback/spotify',
    ) {}

    /** RFC 7636 §4.1 — code_verifier 43-128 karakter */
    public function makeVerifier(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
    }

    /** RFC 7636 §4.2 — S256 = BASE64URL(SHA256(verifier)) */
    public function challengeS256(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    public function authorizeUrl(string $verifier, string $state): string
    {
        $q = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'code_challenge_method' => 'S256',
            'code_challenge' => $this->challengeS256($verifier),
            'state' => $state,
        ]);
        return 'https://accounts.spotify.com/authorize?' . $q;
    }

    /** Token takası — POST accounts.spotify.com/api/token, grant_type=authorization_code */
    public function exchange(string $code, string $verifier): array
    {
        $body = http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->clientId,
            'code_verifier' => $verifier,
            // client_secret: PKCE public client'ta gerekmez; confidential'da secret store'dan
        ]);
        $ctx = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body,
        ]]);
        $raw = file_get_contents('https://accounts.spotify.com/api/token', false, $ctx);
        return json_decode($raw ?: '[]', true) ?: []; // access_token + refresh_token
    }
}
```

---

## 2. JavaScript

```javascript
// Dil: JavaScript — Rol: SoundCloud OAuth 2.1 + PKCE authorize URL — kaynak: developers.soundcloud.com/docs
const crypto = require("node:crypto");

const CLIENT_ID = process.env.SOUNDCLOUD_CLIENT_ID || "PLACEHOLDER_CLIENT_ID";
const REDIRECT_URI = "https://coremusic.net/callback/soundcloud";

function b64url(buf) {
  return Buffer.from(buf).toString("base64url");
}

// RFC 7636: code_verifier 43-128 karakter
function makeVerifier() {
  return b64url(crypto.randomBytes(48)); // 64 karakter
}

async function challengeS256(verifier) {
  return b64url(crypto.createHash("sha256").update(verifier).digest());
}

async function authorizeUrl(state) {
  const verifier = makeVerifier();
  const challenge = await challengeS256(verifier);
  const url = new URL("https://secure.soundcloud.com/authorize");
  url.searchParams.set("client_id", CLIENT_ID);
  url.searchParams.set("redirect_uri", REDIRECT_URI);
  url.searchParams.set("response_type", "code");
  url.searchParams.set("code_challenge", challenge);
  url.searchParams.set("code_challenge_method", "S256");
  url.searchParams.set("state", state); // CSRF nonce — token sonrası doğrula
  return { url: url.toString(), verifier };
}

// Token ucu: POST https://secure.soundcloud.com/oauth/token
// grant_type=authorization_code + code_verifier ZORUNLU (PKCE)
module.exports = { makeVerifier, challengeS256, authorizeUrl };
```

---

## 3. TypeScript

```typescript
// Dil: TypeScript — Rol: Envanter satırı tipi + durum sözlüğü — kaynak: external-anayasa.md §3
export type SystemStatus = "aktif" | "beklemede" | "kapsam-disi" | "bilinmiyor";

export interface ExternalSystemRecord {
  sistem: string;
  saglayici: string;
  kimlikTipi: string;
  apiSurumu: string;
  durum: SystemStatus;
  ucNokta: string;
  riskNotu: string;
  kanitUrl: string[];      // en az 1 resmi URL (P2)
  dogrulamaTarihi: string; // YYYY-MM-DD (P7)
  not?: string;
}

export function validateRecord(r: ExternalSystemRecord): string[] {
  const errors: string[] = [];
  if (r.kanitUrl.length === 0) errors.push("kanıt URL eksik (P2)");
  if (!/^\d{4}-\d{2}-\d{2}$/.test(r.dogrulamaTarihi)) errors.push("tarih biçimi");
  if (r.durum === "aktif") errors.push("durum 'aktif' yalnız üst otoritece işaretlenir (§6)");
  return errors; // boş dizi = geçerli
}

// Örnek kayıt (kanıt URL'leri dış web-arastirma §2'den):
const spotify: ExternalSystemRecord = {
  sistem: "Spotify",
  saglayici: "Spotify AB",
  kimlikTipi: "OAuth 2.0 Code + PKCE (S256)",
  apiSurumu: "Web API",
  durum: "beklemede",
  ucNokta: "https://api.spotify.com",
  riskNotu: "rate limit hesap/anahtar bazlı",
  kanitUrl: ["https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow"],
  dogrulamaTarihi: "2026-10-16",
};
```

---

## 4. Python

```python
# Dil: Python — Rol: Last.fm imza üretimi (api_sig) — kaynak: last.fm/api/authspec
import hashlib
import os

API_KEY = os.environ.get("LASTFM_API_KEY", "PLACEHOLDER_API_KEY")
API_SECRET = os.environ.get("LASTFM_API_SECRET", "PLACEHOLDER_API_SECRET")

def make_api_sig(params: dict) -> str:
    """Alfabetik sırala + secret ekle + MD5 → 32 karakter api_sig (authspec)."""
    items = sorted((k, v) for k, v in params.items() if v is not None)
    raw = "".join(f"{k}{v}" for k, v in items) + API_SECRET
    return hashlib.md5(raw.encode("utf-8")).hexdigest()  # 32 hex karakter

def auth_url(token: str) -> str:
    """Tarayıcı akışı: last.fm/api/auth?api_key=...&token=... → kullanıcı onayı → auth.getSession"""
    sig = make_api_sig({"api_key": API_KEY, "token": token})
    return f"https://www.last.fm/api/auth?api_key={API_KEY}&token={token}&api_sig={sig}"
```

---

## 5. Go

```go
// Dil: Go — Rol: MusicBrainz 1 çağrı/sn gate + User-Agent — kaynak: musicbrainz.org/doc/MusicBrainz_API
package mbgate

import (
	"net/http"
	"time"
)

const minInterval = time.Second // "never make more than ONE call per second"

type Gate struct {
	last time.Time
}

func (g *Gate) Wait() {
	if d := minInterval - time.Since(g.last); d > 0 {
		time.Sleep(d)
	}
	g.last = time.Now()
}

// User-Agent "meaningful" olmalı — örn. uygulama adı-sürümü (client=... POST'larda da)
func NewClient(app, version string) *http.Client {
	return &http.Client{
		Timeout: 10 * time.Second,
		CheckRedirect: func(req *http.Request, via []*http.Request) error {
			req.Header.Set("User-Agent", app+"/"+version+" (https://coremusic.net)")
			return nil
		},
	}
}

// GET https://musicbrainz.org/ws/2/artist?query=...&fmt=json  (auth'sız okuma)
```

---

## 6. Java

```java
// Dil: Java — Rol: Apple Music ES256 JWT header/payload yapısı — kaynak: developer.apple.com generating-developer-tokens
import java.time.Instant;
import java.util.LinkedHashMap;
import java.util.Map;

public final class AppleDevTokenStructure {
    // GERÇEK DEĞERLER secret store'dan gelir; kodda yalnız placeholder adı görünür
    private final String keyId = "PLACEHOLDER_KEY_ID";   // 10 karakter kid
    private final String teamId = "PLACEHOLDER_TEAM_ID"; // iss claim'i

    public Map<String, Object> header() {
        Map<String, Object> h = new LinkedHashMap<>();
        h.put("alg", "ES256");
        h.put("kid", keyId); // 10 karakterlik key ID zorunlu
        return h;
    }

    public Map<String, Object> claims() {
        long iat = Instant.now().getEpochSecond();
        Map<String, Object> c = new LinkedHashMap<>();
        c.put("iss", teamId);
        c.put("iat", iat);
        c.put("exp", iat + 3600); // sınır: <= 15_777_000 (6 ay)
        c.put("origin", "https://coremusic.net");
        return c;
    }

    // İmzalama: ES256 (ECDSA SHA-256) — özel anahtar /secrets/AuthKey_*.p8 (md'ye GİRMEZ)
    // Başarısız/imzasız token → API 401 döner (kaynak behavior)
}
```

---

## 7. C\#

```csharp
// Dil: C# — Rol: Envanter doğrulama + kanıt yaşı kontrolü — kaynak: external-anayasa.md §3, §7
using System.Text.RegularExpressions;

namespace CoreMusic.Vault.External;

public sealed record InventoryRow(
    string Sistem, string Saglayici, string KimlikTipi, string ApiSurumu,
    string Durum, string UcNokta, string RiskNotu,
    IReadOnlyList<string> KanitUrl, string DogrulamaTarihi, string? Not);

public static class InventoryCheck
{
    private static readonly Regex Iso = new(@"^\d{4}-\d{2}-\d{2}$", RegexOptions.Compiled);
    public static readonly TimeSpan MaxAge = TimeSpan.FromDays(90); // §7: >90 gün → yeniden çek

    public static IReadOnlyList<string> Validate(InventoryRow r, DateTime today)
    {
        var errs = new List<string>();
        if (r.KanitUrl.Count == 0) errs.Add("P2: kanıt URL yok");
        if (!Iso.IsMatch(r.DogrulamaTarihi)) errs.Add("tarih biçimi");
        if (DateTime.TryParse(r.DogrulamaTarihi, out var d) && today - d > MaxAge)
            errs.Add("bayat kanıt → L1 yeniden çek");        // §7
        if (r.Durum == "aktif") errs.Add("§6: aktif kararı üst otoritede");
        return errs; // boş = geçerli → GATE-EXT-2 exit 0
    }
}
```

---

## 8. Rust

```rust
// Dil: Rust — Rol: PKCE S256 challenge (RFC 7636 §4.2) — kaynak: rfc-editor.org/rfc/rfc7636
use base64::{engine::general_purpose::URL_SAFE_NO_PAD, Engine};
use rand::RngCore;
use sha2::{Digest, Sha256};

/// code_verifier: 43-128 karakter (RFC 7636 §4.1)
pub fn code_verifier() -> String {
    let mut bytes = [0u8; 48];
    rand::rngs::OsRng.fill_bytes(&mut bytes);
    URL_SAFE_NO_PAD.encode(bytes)
}

/// code_challenge = BASE64URL(SHA256(code_verifier)) — method "S256"
pub fn code_challenge(verifier: &str) -> String {
    let digest = Sha256::digest(verifier.as_bytes());
    URL_SAFE_NO_PAD.encode(digest)
}

pub fn authorize_url(client_id: &str, redirect_uri: &str, state: &str, verifier: &str) -> String {
    format!(
        "https://accounts.spotify.com/authorize?client_id={}&response_type=code\
         &redirect_uri={}&code_challenge_method=S256&code_challenge={}&state={}",
        client_id,
        urlencoding::encode(redirect_uri),
        code_challenge(verifier),
        urlencoding::encode(state)
    )
}
// Token ucu: POST https://accounts.spotify.com/api/token (grant_type=authorization_code)
```

---

## 9. C++

```cpp
// Dil: C++ — Rol: HTTP durum kodu beklenti matrisi (L4) — kaynak: external-dogrulama.md §5
#include <cstdint>
#include <string_view>

namespace cm::l4 {

enum class Expect : std::uint8_t { Ok, AuthRejected, Forbidden, NotFound, RateLimited, ServerError };

// Beklenti matrisi: 401/403/404 = beklenen davranış; 429 = backoff ZORUNLU; 5xx = OLAY
inline Expect classify(int http_status) {
    switch (http_status) {
        case 200: case 201: return Expect::Ok;
        case 401: return Expect::AuthRejected;   // geçersiz/imzasız token → doğru reddediş
        case 403: return Expect::Forbidden;      // kapsam dışı kaynak
        case 404: return Expect::NotFound;       // var olmayan resource
        case 429: return Expect::RateLimited;    // backoff uygula
        default:  return Expect::ServerError;    // 5xx → olay kaydı
    }
}

inline bool needs_backoff(int s) { return s == 429; }

} // namespace cm::l4
// Not: istek katmanı (curl/asio) adaptör kodunda; bu dosya yalnız sözleşme.
```

---

## 10. Ruby

```ruby
# Dil: Ruby — Rol: Discogs rate-limit takibi (auth'lı 60/dk, auth'sız 25/dk)
# Kaynak: www.discogs.com/developers
require "time"

class DiscogsLimiter
  AUTHED_PER_MINUTE   = 60
  UNAUTHED_PER_MINUTE = 25

  def initialize(authenticated:)
    @limit = authenticated ? AUTHED_PER_MINUTE : UNAUTHED_PER_MINUTE
    @window_start = Time.now
    @count = 0
  end

  # User-Agent ZORUNLU — yoksa istek engellenir (kaynak)
  def headers
    {
      "User-Agent" => "CoreMusic/1.0 (PLACEHOLDER_CONTACT)",
      "Authorization" => "Discogs token=PLACEHOLDER_TOKEN" # yalnız auth'lı istekte
    }
  end

  def allow?
    now = Time.now
    if now - @window_start >= 60
      @window_start, @count = now, 0
    end
    @count < @limit.tap { @count += 1 } rescue false
  end
end
```

---

## 11. Kotlin

```kotlin
// Dil: Kotlin — Rol: SoundCloud tek kullanımlık refresh token yönetimi
// Kaynak: developers.soundcloud.com/docs (token expiry ~1 saat, single-use refresh)
data class Tokens(
    val accessToken: String,
    val refreshToken: String,   // TEK KULLANIMLIK — yenilendikten sonra eskisi geçersiz
    val expiresAtEpochSec: Long
)

class SoundCloudTokenStore {
    fun isExpired(t: Tokens, nowEpochSec: Long, skewSec: Long = 60): Boolean =
        nowEpochSec >= t.expiresAtEpochSec - skewSec

    /**
     * grant_type=refresh_token → POST https://secure.soundcloud.com/oauth/token
     * DİKKAT: yanıtta YENİ refresh_token döner; eskisini AT, yenisini sakla.
     * Tek kullanımlık kuralı ihlal edilirse 401 döner.
     */
    fun rotate(old: Tokens, response: TokenResponse): Tokens =
        Tokens(
            accessToken = response.accessToken,
            refreshToken = response.refreshToken, // eskisini saklama!
            expiresAtEpochSec = response.expiresInSec + nowEpochSec()
        )

    // Client credentials rate limit: 50 token/12 saat/app, 30 token/1 saat/IP
    // → token üretmek yerine yenileme akışını kullan (kaynak notu)
}
```

---

## 12. Swift

```swift
// Dil: Swift — Rol: Apple Music developer token taşıma (Bearer) + 401 eleme
// Kaynak: developer.apple.com generating-developer-tokens (401 = geçersiz token)
import Foundation

struct AppleMusicAuth {
    // Değerler secret store / Keychain'den gelir; burada yalnız placeholder adı
    let keyId = "PLACEHOLDER_KEY_ID"    // 10 karakter kid
    let teamId = "PLACEHOLDER_TEAM_ID"  // iss
    var signedJWT: String = "PLACEHOLDER_SIGNED_ES256_JWT"

    func request(_ path: String) -> URLRequest {
        // base: https://api.music.apple.com (dokümantasyon referansı; canlı test L4)
        var req = URLRequest(url: URL(string: "https://api.music.apple.com\(path)")!)
        req.setValue("Bearer \(signedJWT)", forHTTPHeaderField: "Authorization")
        req.setValue("application/json", forHTTPHeaderField: "Accept")
        return req
    }

    /// 401 → token geçersiz/imzasız: üretimi tazele (kid + ES256, exp <= 15777000)
    func shouldRefresh(httpStatus: Int) -> Bool { httpStatus == 401 }
}
```

---

## 13. Elixir

```elixir
# Dil: Elixir — Rol: MusicBrainz istek throttling + JSON fmt — kaynak: musicbrainz.org/doc/MusicBrainz_API
defmodule CoreMusic.MusicBrainz do
  @base "https://musicbrainz.org/ws/2"
  @min_interval_ms 1000  # 1 çağrı/sn — aşılanca IP bloğu riski

  def headers do
    # "meaningful user-agent" zorunlu
    [{"user-agent", "CoreMusic/1.0 (https://coremusic.net; PLACEHOLDER_CONTACT)"},
     {"accept", "application/json"}]
  end

  def get(path, params \\ []) do
    :ok = throttle()
    url = @base <> path <> "?" <> URI.encode_query(params ++ [fmt: "json"])
    Finch.build(:get, url, headers()) |> Finch.request(CoreMusic.Finch)
  end

  # Lookup:  /artist/<MBID>?inc=...
  # Search:  /artist?query=...&limit=...   (MBID yoksa önce search)
  defp throttle do
    case Process.get(:mb_last) do
      nil -> Process.put(:mb_last, System.monotonic_time(:millisecond)); :ok
      last ->
        wait = @min_interval_ms - (System.monotonic_time(:millisecond) - last)
        if wait > 0, do: Process.sleep(wait)
        Process.put(:mb_last, System.monotonic_time(:millisecond))
        :ok
    end
  end
end
```

---

## 14. Özet Matrisi

| # | Dil | Rol | İlgili sistem |
|---|-----|-----|---------------|
| 1 | PHP | PKCE akışı (S256) | Spotify |
| 2 | JavaScript | OAuth 2.1 authorize + challenge | SoundCloud |
| 3 | TypeScript | Envanter kayıt tipi + şema kontrolü | Tümü (şema) |
| 4 | Python | `api_sig` imzası (MD5) | Last.fm |
| 5 | Go | 1 çağrı/sn gate + User-Agent | MusicBrainz |
| 6 | Java | ES256 JWT header/payload yapısı | Apple Music |
| 7 | C# | Envanter doğrulama + kanıt yaşı | Tümü (governance) |
| 8 | Rust | PKCE S256 (RFC 7636) | Spotify/standart |
| 9 | C++ | HTTP beklenti matrisi (L4) | Tümü (doğrulama) |
| 10 | Ruby | Rate limit takibi (60/dk) | Discogs |
| 11 | Kotlin | Tek kullanımlık refresh rotation | SoundCloud |
| 12 | Swift | Bearer taşıma + 401 eleme | Apple Music |
| 13 | Elixir | Throttle + fmt=json | MusicBrainz |

**Kural (P3):** Bu örnekler **referanstır**; üretim adaptörü child seviyede
yazılır ve `external-dogrulama.md` GATE-EXT-1..4'ten geçmeden merge edilmez.

---

## §GRUPLAR

| Grup | Bu dosyadaki karşılığı |
|---|---|
| mimari | Ortak PLACEHOLDER sözleşmesi (§0), rol/şema tipleri (§3), özet matris + P3 kuralı (§14) |
| kod | 13 dil bloğu (§1–§13), hepsi `# Dil: X — Rol: ...` etiketli, akış kaynaklı |
| kanit | Her blokta kaynak atfı (URL/tarih external-web-arastirma §2 ile eşleşir) |

## §Kaynak

| Kaynak | Tarih | Erişim |
|---|---|---|
| RFC 7636 (PKCE) | 2026-10-16 | https://www.rfc-editor.org/rfc/rfc7636 |
| Spotify PKCE tutorial · SoundCloud API Guide · MusicBrainz API · Last.fm authspec · Apple developer tokens | 2026-10-16 | webfetch/exa (bkz external-web-arastirma §2) |
| `external-anayasa.md` §3/§5 · `external-dogrulama.md` §5/§6 | 2026-10-16 | disk |
| `api-contracts/openapi/openapi-kod-ornekleri.md` (kalıp etiket biçimi) | kalıp | disk |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
