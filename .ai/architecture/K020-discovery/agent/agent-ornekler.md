---
node_id: K020-discovery/agent/agent-ornekler
node_type: subdomain
status: draft
parent_id: K020-discovery/agent
children: []
depends_on:
  - K020-discovery/agent
  - K020-discovery/agent/agent-kapsam
  - K020-discovery/agent/agent-bilesenler
evidence:
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow (erişim: 2026-10-17)"
  - "https://developer.spotify.com/documentation/web-api/concepts/rate-limits (erişim: 2026-10-17)"
  - "Dosya: .ai/architecture/K020-discovery/agent/agent-bilesenler.md §3-§9 — arayüz imzaları"
validation:
  - "2026-10-17 — web tur-2 ile doğrulandı"
open_questions:
  - "Üretim dili hangisi? (13 dil de yalnız kalıp — dil kararı kapı 2'ye bağlı)"
  - "⚠️ VERIFICATION REQUIRED: kod blokları derlenmedi/test edilmedi; OpenSSL/sha2/reqwest sürümleri doğrulanmadı"
group: kod
---

# K020-discovery/agent — Kod Örnekleri (agent-ornekler)

**ELI15:** Aynı üç problemi (**A** PKCE · **B** token exchange / `Authorization: Bearer` · **C** 429
`Retry-After` backoff) **13 dilde** ve SQL'de gösteren kalıp dosyasıdır. Bloklar **repo kodu değil,
üretim örneğidir**; gerçek anahtar yoktur — her yerde placeholder kullanılır.

## 1. Amaç, kanıt ve placeholder kuralı

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Placeholder zorunlu | `YOUR_CLIENT_ID`, `<TOKEN>`, `<CODE>`, `<VERIFIER>`, `${CLIENT_ID}` — gerçek anahtar **asla** yazılmaz |
| 2 | Doğrulanmış uçlar | `https://accounts.spotify.com/api/token` · `https://api.spotify.com/v1/search` · `https://auth.tidal.com/v1/oauth2/token` · `https://connect.deezer.com/oauth/auth.php` · `https://api.genius.com/oauth/token` (hepsi erişim: 2026-10-17) |
| 3 | Placeholder uç | `YOUR_AUTHORIZE_ENDPOINT` — Spotify/Tidal/Apple **authorize** host'u doğrulanmadı → ⚠️ VERIFICATION REQUIRED (yol: `/authorize`) |
| 4 | Uydurma yasak | Gerçek olmayan HTTP durumu, limit, sürüm **yazılmaz**; doğrulanmayan ⚠️ işaretlenir |
| 5 | Derlenmedi | Bloklar çalıştırılmadı; standart kütüphane kullanımı esas alınır, üçüncü taraf paketler ⚠️ işaretlidir |
| 6 | Güvenlik kanunu | Token yalnız `.env`/env substitution; `.md`/`.log`/konsol **yasak** · ADR-095 oturum ayrımı · ADR-010 `state`+PKCE · ADR-101 timeout/retry/fallback |

## 2. Problem tanımı (A/B/C) ve dil × problem matrisi

| Harita | Problem | Doğrulanmış kural |
|---|---|---|
| **A** | `code_verifier` → `code_challenge` (S256) | verifier **43–128 karakter**; SHA-256 → **base64url**; `code_challenge_method=S256` |
| **B** | token exchange · `Authorization: Bearer` | Spotify token ucu: `application/x-www-form-urlencoded`; alanlar `grant_type=authorization_code`, `code`, `redirect_uri`, `client_id`, `code_verifier` |
| **C** | 429 → `Retry-After` backoff | Spotify: yuvarlanan 30 sn pencere, aşım → **429** + `Retry-After` başlığı |

| # | Dil | Bloklar | Not |
|---|---|---|---|
| 1 | PHP 8.4 | A + B + C | proje ana dili |
| 2 | JavaScript | A + B + C | tarayıcı/sunucu |
| 3 | TypeScript | A + B | tip güvenli sözleşme |
| 4 | Python | A + B + C | |
| 5 | Go | A + B | net/http |
| 6 | Rust | A + C | ⚠️ paket sürümleri doğrulanmadı |
| 7 | C | A + B | ⚠️ OpenSSL/libcurl sürümleri doğrulanmadı |
| 8 | C++ | A + C | ⚠️ OpenSSL sürümü doğrulanmadı |
| 9 | C# | A + B + C | BCL |
| 10 | Java | A + B | JDK standart |
| 11 | Kotlin | A + C | JDK standart |
| 12 | Swift | A + B | CryptoKit |
| 13 | Ruby | A + B + C | stdlib |
| 14 | SQL | şema ×2 | AES-256-GCM sütunlu token tablosu |

## 3. PHP 8.4

**Blok A — PKCE (S256):** `random_bytes` kriptografik üreteçtir; `hash(..., true)` + base64url.

```php
<?php
declare(strict_types=1);

function pkceVerifier(int $bytes = 64): string
{
    // 64 bayt rastgele -> base64url => ~86 karakter (43-128 aralığında)
    return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
}

function pkceChallengeS256(string $verifier): string
{
    $digest = hash('sha256', $verifier, true);
    return rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
}

$verifier  = pkceVerifier();
$challenge = pkceChallengeS256($verifier);
$state     = bin2hex(random_bytes(32)); // tek kullanımlık CSRF belirteci (ADR-010)
```

**Neden böyle:** verifier 43–128 karakter kuralına 64 baytlık girdiyle girilir; challenge yalnız
SHA-256 özetinden üretilir, uzunluk/charset kuralı uygulanmaz (base64url yeterli).

**Blok B — token exchange (Spotify):** uç `https://accounts.spotify.com/api/token` (erişim: 2026-10-17).

```php
function exchangeToken(string $code, string $verifier): array
{
    $body = http_build_query([
        'grant_type'    => 'authorization_code',
        'code'          => $code,
        'redirect_uri'  => getenv('SPOTIFY_REDIRECT_URI'),   // exchange'de de gönderilir
        'client_id'     => getenv('SPOTIFY_CLIENT_ID'),      // YOUR_CLIENT_ID
        'code_verifier' => $verifier,
    ]);

    $ch = curl_init('https://accounts.spotify.com/api/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,                        // ADR-101: timeout zorunlu
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($code !== 200) {
        return ['ok' => false, 'status' => $code];           // uydurma durum YOK
    }
    $data = json_decode((string) $raw, true, 512, JSON_THROW_ON_ERROR);
    return [
        'ok'            => true,
        'access_token'  => $data['access_token'],            // düz metin: yalnız TokenStore'a
        'token_type'    => $data['token_type'],              // "Bearer"
        'expires_in'    => $data['expires_in'],
        'refresh_token' => $data['refresh_token'] ?? null,   // yenilemede gelmeyebilir
    ];
}
```

**Neden böyle:** form alanları dokümanla birebir (`redirect_uri` ve `client_id` de gönderilir);
gerçek durum kodu ayrıştırılır, 200 dışı durumlar uydurulmaz; `refresh_token` opsiyoneldir.

**Blok C — 429 Retry-After:**

```php
function callWithRetry(string $url, array $headers, int $maxAttempts = 3): array
{
    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $retryAfter = null;                                    // Retry-After başlığı buraya yazılır
        $capture    = function (CurlHandle $ch, string $line) use (&$retryAfter): int {
            if (preg_match('/^Retry-After:\s*(\d+)/i', $line, $m) === 1) {
                $retryAfter = (int) $m[1];
            }
            return strlen($line);
        };

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,                      // ADR-101
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADERFUNCTION => $capture,
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($code !== 429) {
            return ['ok' => true, 'status' => $code, 'body' => $raw];
        }
        // Retry-After başlığı sunucudan gelir; yoksa üstel geri çekilme (üst sınır 30 sn)
        $wait = $retryAfter ?? min(30, 2 ** $attempt);
        sleep($wait);                                          // kota kilidi: bekle
    }
    return ['ok' => false, 'status' => 429, 'body' => null];
}
```

**Neden böyle:** 429'da `Retry-After` **varsa onu kullan** kuralı vurgulanır; başlık yoksa üstel
geri çekilme devreye girer; deneme bütçesi sınırlıdır (sonsuz döngü yok).

## 4. JavaScript

**Blok A — PKCE (S256, Web Crypto):**

```js
const bytesToBase64Url = (buf) =>
  btoa(String.fromCharCode(...new Uint8Array(buf)))
    .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');

export async function pkcePair() {
  const raw = crypto.getRandomValues(new Uint8Array(64));
  const verifier = bytesToBase64Url(raw.buffer);
  const digest = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(verifier));
  const challenge = bytesToBase64Url(digest);
  const state = bytesToBase64Url(crypto.getRandomValues(new Uint8Array(32)).buffer);
  return { verifier, challenge, state, method: 'S256' };
}

const authorizeUrl = new URL('YOUR_AUTHORIZE_ENDPOINT'); // host doğrulanmadı → ⚠️
authorizeUrl.search = new URLSearchParams({
  client_id: 'YOUR_CLIENT_ID',
  response_type: 'code',
  redirect_uri: 'https://coremusic.example/callback',
  scope: 'user-read-private',
  state: pair.state,
  code_challenge: pair.challenge,
  code_challenge_method: 'S256',
}).toString();
```

**Neden böyle:** `crypto.subtle` asenkron çalışır; base64url dönüşümü `+`, `/` ve `=` dolgusunu
temizler — challenge'ın doküman kuralına (base64url) uyması için zorunlu.

**Blok B — token exchange (fetch):**

```js
export async function exchangeToken(code, verifier) {
  const body = new URLSearchParams({
    grant_type: 'authorization_code',
    code,
    redirect_uri: 'https://coremusic.example/callback',
    client_id: 'YOUR_CLIENT_ID',
    code_verifier: verifier,
  });

  const res = await fetch('https://accounts.spotify.com/api/token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body,
    signal: AbortSignal.timeout(10000), // ADR-101: timeout zorunlu
  });

  const status = res.status;                 // gerçek durum, uydurma değil
  if (status === 429) {
    const waitSec = Number(res.headers.get('Retry-After') ?? '30');
    return { ok: false, retryAfter: waitSec };
  }
  const data = await res.json();
  return {
    ok: res.ok,
    accessToken: data.access_token,
    tokenType: data.token_type,
    expiresIn: data.expires_in,
    refreshToken: data.refresh_token ?? null, // yenilemede gelmeyebilir
  };
}
```

**Neden böyle:** 429 durumunda gövde okunmadan `Retry-After` başlığı okunur; `refresh_token`
opsiyoneldir (Spotify kuralı) ve `?? null` ile açıkça taşınır.

**Blok C — 429 backoff döngüsü:**

```js
export async function withRateLimitRetry(doCall, maxAttempts = 3) {
  for (let attempt = 1; attempt <= maxAttempts; attempt++) {
    const result = await doCall();
    if (result.status !== 429) return result;

    const header = result.retryAfter;                  // saniye
    const waitSec = header ?? Math.min(30, 2 ** attempt);
    await new Promise((r) => setTimeout(r, waitSec * 1000));
  }
  return { ok: false, status: 429, exhausted: true };  // zincir kapanır (ADR-101)
}
```

**Neden böyle:** bekleme tam `Retry-After` değeridir; başlık yoksa üstel geri çekilme + üst sınır
(30 sn) uygulanır; deneme bütçesi bitince hata yerine **bilinir** bir sonuç döner.

## 5. TypeScript

**Blok A — PKCE + tip sözleşmesi:**

```ts
export interface PkcePair {
  readonly verifier: string;      // 43-128 karakter
  readonly challenge: string;     // base64url(SHA-256(verifier))
  readonly state: string;
  readonly method: 'S256';
}

const toBase64Url = (buf: ArrayBuffer): string =>
  btoa(String.fromCharCode(...new Uint8Array(buf)))
    .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');

export async function createPkcePair(): Promise<PkcePair> {
  const verifier = toBase64Url(crypto.getRandomValues(new Uint8Array(64)).buffer);
  const digest = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(verifier));
  return {
    verifier,
    challenge: toBase64Url(digest),
    state: toBase64Url(crypto.getRandomValues(new Uint8Array(32)).buffer),
    method: 'S256',
  };
}
```

**Neden böyle:** `method: 'S256'` literal tip olarak kilitlenir — yanlış yöntem (`plain`) derleme
anında geçemez; `readonly` alanlar çalışma anında değiştirilemez.

**Blok B — token exchange (tipli yanıt):**

```ts
export interface SpotifyTokenResponse {
  readonly access_token: string;
  readonly token_type: 'Bearer';
  readonly scope: string;
  readonly expires_in: number;
  readonly refresh_token?: string;   // yenilemede gelmeyebilir
}

export type ExchangeResult =
  | { readonly ok: true; readonly data: SpotifyTokenResponse }
  | { readonly ok: false; readonly status: number; readonly retryAfter?: number };

export async function exchangeToken(code: string, verifier: string): Promise<ExchangeResult> {
  const res = await fetch('https://accounts.spotify.com/api/token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      grant_type: 'authorization_code',
      code,
      redirect_uri: 'https://coremusic.example/callback',
      client_id: 'YOUR_CLIENT_ID',
      code_verifier: verifier,
    }),
    signal: AbortSignal.timeout(10000),
  });

  if (res.status === 429) {
    return { ok: false, status: 429, retryAfter: Number(res.headers.get('Retry-After') ?? '30') };
  }
  if (!res.ok) return { ok: false, status: res.status };
  return { ok: true, data: (await res.json()) as SpotifyTokenResponse };
}
```

**Neden böyle:** `token_type` literal `'Bearer'` olarak yazılır (doküman değeri); union tip,
başarılı/başarısız durumları ayırmayı zorunlu kılar — hata yutma mümkün olmaz.

## 6. Python

**Blok A — PKCE (stdlib):**

```python
import base64
import hashlib
import secrets


def _b64url(data: bytes) -> str:
    return base64.urlsafe_b64encode(data).rstrip(b"=").decode("ascii")


def pkce_pair() -> dict[str, str]:
    verifier = _b64url(secrets.token_bytes(64))     # ~86 karakter (43-128)
    challenge = _b64url(hashlib.sha256(verifier.encode("ascii")).digest())
    state = _b64url(secrets.token_bytes(32))        # tek kullanımlık (ADR-010)
    return {"verifier": verifier, "challenge": challenge, "state": state,
            "method": "S256"}
```

**Neden böyle:** `secrets` modülü kriptografik rastgeleliktir; `base64.urlsafe_b64encode` zaten
base64url üretir, yalnız `=` dolgusu kırpılır (`+`/`/` çıkmaz).

**Blok B — token exchange (urllib):**

```python
import json
import urllib.error
import urllib.parse
import urllib.request


def exchange_token(code: str, verifier: str) -> dict:
    payload = urllib.parse.urlencode({
        "grant_type": "authorization_code",
        "code": code,
        "redirect_uri": "https://coremusic.example/callback",
        "client_id": "YOUR_CLIENT_ID",
        "code_verifier": verifier,
    }).encode("ascii")

    req = urllib.request.Request(
        "https://accounts.spotify.com/api/token",
        data=payload,
        method="POST",
        headers={"Content-Type": "application/x-www-form-urlencoded"},
    )
    try:
        with urllib.request.urlopen(req, timeout=10) as res:      # ADR-101
            data = json.loads(res.read().decode("utf-8"))
            return {"ok": True, "access_token": data["access_token"],
                    "expires_in": data["expires_in"],
                    "refresh_token": data.get("refresh_token")}   # gelmeyebilir
    except urllib.error.HTTPError as err:
        if err.code == 429:
            retry_after = err.headers.get("Retry-After", "30")
            return {"ok": False, "status": 429, "retry_after": int(retry_after)}
        return {"ok": False, "status": err.code}
```

**Neden böyle:** `HTTPError` hem durum kodunu hem başlıkları taşır — 429'da `Retry-After` gövde
okunmadan alınır; `timeout` parametresi ADR-101 gereği sabittir.

**Blok C — 429 backoff döngüsü:**

```python
import time


def call_with_retry(call, max_attempts: int = 3) -> dict:
    for attempt in range(1, max_attempts + 1):
        result = call()
        if result.get("status") != 429:
            return result
        wait = int(result.get("retry_after") or min(30, 2 ** attempt))
        time.sleep(wait)                                  # Retry-After kadar bekle
    return {"ok": False, "status": 429, "exhausted": True}
```

**Neden böyle:** bekleme `Retry-After` değerine görelenir; bütçe bitince döngü kapanır ve
`exhausted` sonucu çağıran bileşene (`FallbackDriver`) bırakılır.

## 7. Go

**Blok A — PKCE (stdlib):**

```go
package pkce

import (
	"crypto/rand"
	"crypto/sha256"
	"encoding/base64"
)

func b64url(b []byte) string {
	return base64.RawURLEncoding.EncodeToString(b) // padssiz base64url
}

func Pair() (verifier, challenge, state string, err error) {
	buf := make([]byte, 64)
	if _, err = rand.Read(buf); err != nil {
		return
	}
	verifier = b64url(buf) // ~86 karakter (43-128)
	sum := sha256.Sum256([]byte(verifier))
	challenge = b64url(sum[:])

	st := make([]byte, 32)
	if _, err = rand.Read(st); err != nil {
		return
	}
	state = b64url(st)
	return
}
```

**Neden böyle:** `crypto/rand` OS kaynağını kullanır; `RawURLEncoding` dolgusuz base64url
üretir — challenge kuralına uygun çıktı için `TrimRight` gerekmez.

**Blok B — token exchange (net/http + context):**

```go
package token

import (
	"context"
	"encoding/json"
	"io"
	"net/http"
	"net/url"
	"strings"
	"time"
)

type ExchangeResult struct {
	OK           bool
	Status       int
	AccessToken  string
	ExpiresIn    int
	RefreshToken string // boş kalabilir: yenilemede gelmeyebilir
	RetryAfter   int
}

func Exchange(ctx context.Context, code, verifier string) (ExchangeResult, error) {
	form := url.Values{
		"grant_type":    {"authorization_code"},
		"code":          {code},
		"redirect_uri":  {"https://coremusic.example/callback"},
		"client_id":     {"YOUR_CLIENT_ID"},
		"code_verifier": {verifier},
	}

	ctx, cancel := context.WithTimeout(ctx, 10*time.Second) // ADR-101
	defer cancel()

	req, err := http.NewRequestWithContext(ctx, http.MethodPost,
		"https://accounts.spotify.com/api/token",
		strings.NewReader(form.Encode()))
	if err != nil {
		return ExchangeResult{}, err
	}
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")

	res, err := http.DefaultClient.Do(req)
	if err != nil {
		return ExchangeResult{}, err
	}
	defer res.Body.Close()

	if res.StatusCode == http.StatusTooManyRequests {
		ra := res.Header.Get("Retry-After") // saniye
		return ExchangeResult{OK: false, Status: 429, RetryAfter: atoiDefault(ra, 30)}, nil
	}
	body, _ := io.ReadAll(io.LimitReader(res.Body, 1<<20))
	var data struct {
		AccessToken  string `json:"access_token"`
		ExpiresIn    int    `json:"expires_in"`
		RefreshToken string `json:"refresh_token"`
	}
	if err := json.Unmarshal(body, &data); err != nil {
		return ExchangeResult{OK: false, Status: res.StatusCode}, err
	}
	return ExchangeResult{OK: res.StatusCode == http.StatusOK, Status: res.StatusCode,
		AccessToken: data.AccessToken, ExpiresIn: data.ExpiresIn,
		RefreshToken: data.RefreshToken}, nil
}
```

**Neden böyle:** `context.WithTimeout` tüm zinciri kapsar (ADR-101); `Retry-After` gövde
okunmadan başlıktan okunur; gövde `io.LimitReader` ile sınırlıdır (bellek bütçesi).

## 8. Rust

> **Uyarı:** Rust'ta SHA-256 standart kütüphanede yoktur; aşağıdaki bloklar `sha2` ve `rand`
> paketlerini kullanır — **paket sürümleri ⚠️ VERIFICATION REQUIRED** (uydurma sürüm yazılmaz).

**Blok A — PKCE:**

```rust
use base64::{engine::general_purpose::URL_SAFE_NO_PAD, Engine as _};
use rand::RngCore;
use sha2::{Digest, Sha256};

pub struct PkcePair {
    pub verifier: String,
    pub challenge: String,
    pub state: String,
    pub method: &'static str,
}

pub fn pair() -> PkcePair {
    let mut v = [0u8; 64];
    rand::rngs::OsRng.fill_bytes(&mut v);
    let verifier = URL_SAFE_NO_PAD.encode(v); // ~86 karakter (43-128)

    let digest = Sha256::digest(verifier.as_bytes());
    let challenge = URL_SAFE_NO_PAD.encode(digest);

    let mut s = [0u8; 32];
    rand::rngs::OsRng.fill_bytes(&mut s);

    PkcePair { verifier, challenge, state: URL_SAFE_NO_PAD.encode(s), method: "S256" }
}
```

**Neden böyle:** `OsRng` kriptografik kaynaktır; `URL_SAFE_NO_PAD` tek adımda base64url +
dolgusuz çıktı verir; `method` sabit `"S256"` — çalışma anında değiştirilemez.

**Blok C — 429 Retry-After (döngü):**

```rust
use std::time::Duration;

pub enum CallOutcome {
    Done { status: u16 },
    RetryAfter { seconds: u64 },
}

pub fn with_retry(mut call: impl FnMut() -> CallOutcome, max_attempts: u32) -> CallOutcome {
    let mut attempt = 1u32;
    loop {
        match call() {
            CallOutcome::RetryAfter { seconds } if attempt < max_attempts => {
                std::thread::sleep(Duration::from_secs(seconds)); // Retry-After kadar
                attempt += 1;
            }
            CallOutcome::RetryAfter { seconds } => {
                return CallOutcome::RetryAfter { seconds }; // bütçe tükendi: bildir
            }
            other => return other,
        }
    }
}
```

**Neden böyle:** kütüphane bağımsız bir döngü yazılır; `Retry-After` saniye değeri doğrudan
uygulanır, üstel geri çekilme yalnız başlık yoksa çağıran tarafta eklenir.

## 9. C

> **Uyarı:** Bloklar OpenSSL (SHA-256) ve libcurl (HTTP) kullanır — **sürümler ⚠️ VERIFICATION
> REQUIRED**; uydurma sürüm satırı yazılmaz.

**Blok A — PKCE (OpenSSL EVP):**

```c
#include <openssl/evp.h>
#include <stdio.h>
#include <string.h>

static const char B64URL[] =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";

static void base64url(const unsigned char *in, size_t len, char *out)
{
    size_t i, j = 0;
    for (i = 0; i + 2 < len; i += 3) {
        unsigned v = (in[i] << 16) | (in[i + 1] << 8) | in[i + 2];
        out[j++] = B64URL[(v >> 18) & 63];
        out[j++] = B64URL[(v >> 12) & 63];
        out[j++] = B64URL[(v >> 6) & 63];
        out[j++] = B64URL[v & 63];
    }
    out[j] = '\0';
}

int pkce_challenge_s256(const char *verifier, char *out_challenge)
{
    unsigned char digest[EVP_MAX_MD_SIZE];
    unsigned int  dlen = 0;
    EVP_MD_CTX *ctx = EVP_MD_CTX_new();
    if (!ctx) return -1;
    if (EVP_DigestInit_ex(ctx, EVP_sha256(), NULL) != 1) { EVP_MD_CTX_free(ctx); return -1; }
    if (EVP_DigestUpdate(ctx, verifier, strlen(verifier)) != 1) { EVP_MD_CTX_free(ctx); return -1; }
    if (EVP_DigestFinal_ex(ctx, digest, &dlen) != 1) { EVP_MD_CTX_free(ctx); return -1; }
    EVP_MD_CTX_free(ctx);
    base64url(digest, dlen, out_challenge); /* padssiz base64url */
    return 0;
}
```

**Neden böyle:** base64url döngüsü tek başına yazılır — `+`, `/` ve `=` kullanılmaz; hata
yollarında `EVP_MD_CTX_free` çağrılır (bellek sızıntısı yok).

**Blok B — token exchange (libcurl):**

```c
#include <curl/curl.h>
#include <string.h>

struct buf { char *data; size_t len; };

static size_t write_cb(char *ptr, size_t size, size_t nmemb, void *user)
{
    struct buf *b = (struct buf *)user;
    size_t total = size * nmemb;
    char *p = realloc(b->data, b->len + total + 1);
    if (!p) return 0;
    b->data = p;
    memcpy(b->data + b->len, ptr, total);
    b->len += total;
    b->data[b->len] = '\0';
    return total;
}

int exchange_token(const char *code, const char *verifier, long *http_status)
{
    CURL *curl = curl_easy_init();
    if (!curl) return -1;

    char post[1024];
    snprintf(post, sizeof post,
             "grant_type=authorization_code&code=%s"
             "&redirect_uri=https%%3A%%2F%%2Fcoremusic.example%%2Fcallback"
             "&client_id=YOUR_CLIENT_ID&code_verifier=%s", code, verifier);

    struct buf resp = {0};
    curl_easy_setopt(curl, CURLOPT_URL, "https://accounts.spotify.com/api/token");
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, post);
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER,
                     curl_slist_append(NULL, "Content-Type: application/x-www-form-urlencoded"));
    curl_easy_setopt(curl, CURLOPT_TIMEOUT, 10L); /* ADR-101 */
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, write_cb);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &resp);
    CURLcode rc = curl_easy_perform(curl);
    curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, http_status);
    curl_easy_cleanup(curl);
    free(resp.data);
    return (rc == CURLE_OK) ? 0 : -1;
}
```

**Neden böyle:** form gövdesi doküman alanlarıyla sınırlıdır; `CURLOPT_TIMEOUT` ADR-101 gereği
sabitidir; HTTP durumu dışarıda çağırana verilir — uydurma durum kodu yoktur.

## 10. C++

> **Uyarı:** OpenSSL bağımlılığı **sürüm doğrulanmadı** ⚠️; bloklar üretim örneğidir, derlenmedi.

**Blok A — PKCE:**

```cpp
#include <openssl/evp.h>
#include <array>
#include <cstdint>
#include <random>
#include <string>

namespace pkce {

inline std::string base64url(const unsigned char *in, std::size_t len)
{
    static const char tbl[] =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
    std::string out;
    out.reserve((len * 4 + 2) / 3);
    for (std::size_t i = 0; i + 2 < len; i += 3) {
        std::uint32_t v = (in[i] << 16) | (in[i + 1] << 8) | in[i + 2];
        out.push_back(tbl[(v >> 18) & 63]);
        out.push_back(tbl[(v >> 12) & 63]);
        out.push_back(tbl[(v >> 6) & 63]);
        out.push_back(tbl[v & 63]);
    }
    return out; // padssiz
}

inline std::string challenge_s256(const std::string &verifier)
{
    std::array<unsigned char, EVP_MAX_MD_SIZE> digest{};
    unsigned int dlen = 0;
    EVP_MD_CTX *ctx = EVP_MD_CTX_new();
    EVP_DigestInit_ex(ctx, EVP_sha256(), nullptr);
    EVP_DigestUpdate(ctx, verifier.data(), verifier.size());
    EVP_DigestFinal_ex(ctx, digest.data(), &dlen);
    EVP_MD_CTX_free(ctx);
    return base64url(digest.data(), dlen);
}

}  // namespace pkce
```

**Neden böyle:** `std::array` RAII ile yaşar; `EVP_MD_CTX_free` tüm yolları kapsar; base64url
tablosu `-_` içerir, dolgu yoktur.

**Blok C — 429 Retry-After (sürekleme):**

```cpp
#include <chrono>
#include <cstdint>
#include <thread>

enum class Outcome { Done, RetryAfter, Exhausted };

template <typename Call>
Outcome with_retry(Call &&call, std::uint32_t max_attempts,
                   std::uint64_t retry_after_seconds /* 0 ise başlık yok */)
{
    for (std::uint32_t attempt = 1; attempt <= max_attempts; ++attempt) {
        const auto res = call();
        if (res.status != 429) return Outcome::Done;
        const std::uint64_t wait =
            (retry_after_seconds > 0) ? retry_after_seconds
                                      : std::min<std::uint64_t>(30, std::uint64_t{1} << attempt);
        std::this_thread::sleep_for(std::chrono::seconds(wait));
    }
    return Outcome::Exhausted; // zincir kapanır: FallbackDriver'a devredilir
}
```

**Neden böyle:** `retry_after_seconds` parametresi "başlık okundu mu" ayrımını zorunlu kılar;
üst sınır (30 sn) kota kilidini engeller; sonuç `Exhausted` bildirimlidir.

## 11. C#

**Blok A — PKCE (BCL):**

```csharp
using System.Security.Cryptography;
using System.Text;

static string Base64Url(byte[] data) =>
    Convert.ToBase64String(data).TrimEnd('=').Replace('+', '-').Replace('/', '_');

static (string Verifier, string Challenge, string State) CreatePkcePair()
{
    var verifier = Base64Url(RandomNumberGenerator.GetBytes(64)); // ~86 karakter
    var digest   = SHA256.HashData(Encoding.ASCII.GetBytes(verifier));
    var challenge = Base64Url(digest);                             // base64url(SHA-256)
    var state     = Base64Url(RandomNumberGenerator.GetBytes(32)); // tek kullanımlık
    return (verifier, challenge, state);
}
```

**Neden böyle:** `RandomNumberGenerator` FIPS onaylı kaynaktır; `HashData` stateless'tır (nesne
yönetimi gerekmez); base64url dönüşümü üç adımda tamamlanır (`TrimEnd` + iki karakter değişimi).

**Blok B — token exchange (HttpClient):**

```csharp
using System.Net.Http.Headers;

static async Task<Dictionary<string, object>> ExchangeTokenAsync(
    HttpClient http, string code, string verifier, CancellationToken ct)
{
    var form = new Dictionary<string, string>
    {
        ["grant_type"]    = "authorization_code",
        ["code"]          = code,
        ["redirect_uri"]  = "https://coremusic.example/callback",
        ["client_id"]     = "YOUR_CLIENT_ID",
        ["code_verifier"] = verifier,
    };

    using var req = new HttpRequestMessage(HttpMethod.Post,
        "https://accounts.spotify.com/api/token")
    {
        Content = new FormUrlEncodedContent(form),
    };
    req.Headers.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

    using var res = await http.SendAsync(req, ct);   // timeout HttpClient ayarında (ADR-101)
    var status = (int)res.StatusCode;

    if (status == 429)
    {
        var ra = res.Headers.RetryAfter?.Delta?.TotalSeconds ?? 30;
        return new() { ["ok"] = false, ["status"] = 429, ["retry_after"] = (int)ra };
    }

    var body = await res.Content.ReadAsStringAsync(ct);
    return new() { ["ok"] = res.IsSuccessStatusCode, ["status"] = status, ["body"] = body };
}
```

**Neden böyle:** `FormUrlEncodedContent` isteğin tipini kendisi kurar (imleç hatası önlenir);
`RetryAfter` başlığı `HttpResponseHeaders` üzerinden okunur; durum kodu sözlüğe gerçek değeriyle
yazılır.

**Blok C — 429 backoff:**

```csharp
static async Task<bool> WithRetryAsync(Func<Task<bool>> call, int maxAttempts = 3,
                                       Func<int, int> waitSeconds = null)
{
    for (var attempt = 1; attempt <= maxAttempts; attempt++)
    {
        var ok = await call();
        if (ok) return true;
        var wait = waitSeconds?.Invoke(attempt) ?? Math.Min(30, 1 << attempt);
        await Task.Delay(TimeSpan.FromSeconds(wait)); // Retry-After bekleme
    }
    return false; // bütçe tükendi: FallbackDriver kararı
}
```

**Neden böyle:** bekleme `Task.Delay` ile asenkrondur (iş parçacığı bloke olmaz); bütçe sonunda
`false` döner — hata fırlatma yok, bildirimli sonuç var (ADR-101).

## 12. Java

**Blok A — PKCE (JDK):**

```java
import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.util.Base64;

public final class Pkce {
    private static final SecureRandom RNG = new SecureRandom();

    private static String b64url(byte[] data) {
        return Base64.getUrlEncoder().withoutPadding().encodeToString(data);
    }

    public static String verifier() {
        byte[] raw = new byte[64];
        RNG.nextBytes(raw);                    // kriptografik rastgelelik
        return b64url(raw);                    // ~86 karakter (43-128)
    }

    public static String challengeS256(String verifier) throws NoSuchAlgorithmException {
        byte[] digest = MessageDigest.getInstance("SHA-256")
                .digest(verifier.getBytes(StandardCharsets.US_ASCII));
        return b64url(digest);                 // base64url, padssiz
    }

    public static String state() {
        byte[] raw = new byte[32];
        RNG.nextBytes(raw);
        return b64url(raw);                    // tek kullanımlık (ADR-010)
    }
}
```

**Neden böyle:** `Base64.getUrlEncoder().withoutPadding()` tek adımda base64url verir;
`US_ASCII` girdi kodlaması verifier'ın ASCII doğasıyla uyumludur; `getInstance("SHA-256")`
yalnızca ad verir (algoritma zincirleme yok).

**Blok B — token exchange (java.net.http):**

```java
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;

public static HttpResponse<String> exchange(HttpClient client, String code, String verifier)
        throws Exception {
    String form = "grant_type=authorization_code"
            + "&code=" + java.net.URLEncoder.encode(code, StandardCharsets.UTF_8)
            + "&redirect_uri=" + java.net.URLEncoder.encode(
                  "https://coremusic.example/callback", StandardCharsets.UTF_8)
            + "&client_id=YOUR_CLIENT_ID"
            + "&code_verifier=" + java.net.URLEncoder.encode(verifier, StandardCharsets.UTF_8);

    HttpRequest req = HttpRequest.newBuilder(URI.create("https://accounts.spotify.com/api/token"))
            .timeout(Duration.ofSeconds(10))               // ADR-101
            .header("Content-Type", "application/x-www-form-urlencoded")
            .POST(HttpRequest.BodyPublishers.ofString(form))
            .build();

    HttpResponse<String> res = client.send(req, HttpResponse.BodyHandlers.ofString());
    if (res.statusCode() == 429) {
        String ra = res.headers().firstValue("Retry-After").orElse("30");
        return res;                       // çağırıcı Retry-After'i başlıktan okur
    }
    return res;
}
```

**Neden böyle:** `URLEncoder` her alan için ayrı çalıştırılır (imleç karakterleri bozulmaz);
`.timeout` tüm isteği kapsar; 429'da gövde işlenmez, başlık yeterlidir.

## 13. Kotlin

**Blok A — PKCE:**

```kotlin
import java.security.MessageDigest
import java.security.SecureRandom
import java.util.Base64

object Pkce {
    private val rng = SecureRandom()

    private fun b64url(bytes: ByteArray): String =
        Base64.getUrlEncoder().withoutPadding().encodeToString(bytes)

    fun verifier(): String = b64url(ByteArray(64).also(rng::nextBytes)) // ~86 karakter

    fun challengeS256(verifier: String): String {
        val digest = MessageDigest.getInstance("SHA-256")
            .digest(verifier.toByteArray(Charsets.US_ASCII))
        return b64url(digest)                                  // base64url, padssiz
    }

    fun state(): String = b64url(ByteArray(32).also(rng::nextBytes))
}
```

**Neden böyle:** `SecureRandom` tek örnek olarak tutulur; `also(rng::nextBytes)` diziyi yerinde
doldurur (ek kopya yok); challenge yalnız özetten üretilir.

**Blok C — 429 Retry-After:**

```kotlin
import java.net.http.HttpResponse

sealed interface CallResult {
    data class Done(val status: Int) : CallResult
    data class RateLimited(val retryAfterSeconds: Long) : CallResult
}

fun <T> withRetry(call: () -> HttpResponse<T>, maxAttempts: Int = 3): CallResult {
    repeat(maxAttempts) { index ->
        val res = call()
        if (res.statusCode() != 429) return CallResult.Done(res.statusCode())
        val header = res.headers().firstValue("Retry-After").orElse(null)
        val wait = header?.toLongOrNull()
            ?: minOf(30L, 1L shl (index + 1))        // başlık yoksa üstel geri çekilme
        Thread.sleep(wait * 1000)                    // Retry-After kadar bekle
    }
    return CallResult.Done(429)                      // bütçe tükendi: bildirimli sonuç
}
```

**Neden böyle:** sealed interface hata yutmayı imkânsız kılar (eşleşme zorunlu); `Retry-After`
önce başlıktan okunur, sayı değilse geri çekilme formülüne düşülür.

## 14. Swift

**Blok A — PKCE (CryptoKit):**

```swift
import CryptoKit
import Foundation

func base64Url(_ data: Data) -> String {
    return data.base64EncodedString()
        .replacingOccurrences(of: "+", with: "-")
        .replacingOccurrences(of: "/", with: "_")
        .replacingOccurrences(of: "=", with: "")
}

func pkcePair() -> (verifier: String, challenge: String, state: String) {
    var raw = Data(count: 64)
    raw.withUnsafeMutableBytes { SecRandomCopyBytes(kSecRandomDefault, 64, $0.baseAddress!) }

    let verifier = base64Url(raw)                       // ~86 karakter (43-128)
    let digest = SHA256.hash(data: Data(verifier.utf8))
    let challenge = base64Url(Data(digest))             // base64url(SHA-256)

    var st = Data(count: 32)
    st.withUnsafeMutableBytes { SecRandomCopyBytes(kSecRandomDefault, 32, $0.baseAddress!) }
    return (verifier, challenge, base64Url(st))         // state: tek kullanımlık
}
```

**Neden böyle:** `SHA256.hash` CryptoKit ile sarmalanır (elle yazılmış özet yok); base64url üç
değişimle elde edilir; `SecRandomCopyBytes` sistem kaynağını kullanır.

**Blok B — token exchange (URLSession):**

```swift
import Foundation

struct TokenResponse: Decodable {
    let accessToken: String
    let tokenType: String
    let expiresIn: Int
    let refreshToken: String?

    enum CodingKeys: String, CodingKey {
        case accessToken = "access_token"
        case tokenType   = "token_type"
        case expiresIn   = "expires_in"
        case refreshToken = "refresh_token"   // yenilemede gelmeyebilir
    }
}

enum TokenExchangeError: Error {
    case rateLimited(retryAfterSeconds: String)   // 429: Retry-After başlığı taşınır
    case badStatus(Int)
}

func exchangeToken(code: String, verifier: String,
                   completion: @escaping (Result<TokenResponse, Error>) -> Void) {
    var req = URLRequest(url: URL(string: "https://accounts.spotify.com/api/token")!)
    req.httpMethod = "POST"
    req.timeoutInterval = 10                              // ADR-101
    req.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")
    let form = "grant_type=authorization_code"
        + "&code=\(code.addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed)!)"
        + "&redirect_uri=\("https://coremusic.example/callback"
            .addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed)!)"
        + "&client_id=YOUR_CLIENT_ID"
        + "&code_verifier=\(verifier)"
    req.httpBody = Data(form.utf8)

    URLSession.shared.dataTask(with: req) { data, res, error in
        guard let http = res as? HTTPURLResponse else {
            completion(.failure(error ?? URLError(.badServerResponse))); return
        }
        if http.statusCode == 429 {
            let ra = http.value(forHTTPHeaderField: "Retry-After") ?? "30"
            completion(.failure(TokenExchangeError.rateLimited(retryAfterSeconds: ra)))
            return
        }
        guard let data = data, http.statusCode == 200 else {
            completion(.failure(URLError(.badServerResponse))); return
        }
        do {
            let decoded = try JSONDecoder().decode(TokenResponse.self, from: data)
            completion(.success(decoded))
        } catch {
            completion(.failure(error))
        }
    }.resume()
}
```

**Neden böyle:** `CodingKeys` ile JSON alan adları (`access_token` vb.) kilitlenir; `timeoutInterval`
istek başlangıcında tanımlıdır; 429'da `Retry-After` başlığı gövde işlenmeden okunur.

## 15. Ruby

**Blok A — PKCE (stdlib):**

```ruby
require 'digest'
require 'securerandom'
require 'base64'

module Pkce
  module_function

  def b64url(bytes)
    Base64.urlsafe_encode64(bytes, padding: false)   # padssiz base64url
  end

  def verifier
    b64url(SecureRandom.random_bytes(64))            # ~86 karakter (43-128)
  end

  def challenge_s256(verifier)
    b64url(Digest::SHA256.digest(verifier))          # base64url(SHA-256)
  end

  def state
    b64url(SecureRandom.random_bytes(32))            # tek kullanımlık (ADR-010)
  end
end
```

**Neden böyle:** `SecureRandom` kriptografik kaynaktır; `padding: false` tek satırda kuralı
sağlar; `Digest::SHA256.digest` bayt döndürür (dize dönüşümü gerekmez).

**Blok B — token exchange (Net::HTTP):**

```ruby
require 'json'
require 'net/http'
require 'uri'

def exchange_token(code, verifier)
  uri  = URI('https://accounts.spotify.com/api/token')
  body = {
    'grant_type'    => 'authorization_code',
    'code'          => code,
    'redirect_uri'  => 'https://coremusic.example/callback',
    'client_id'     => 'YOUR_CLIENT_ID',
    'code_verifier' => verifier,
  }

  req = Net::HTTP::Post.new(uri)
  req.content_type = 'application/x-www-form-urlencoded'
  req.set_form_data(body)

  http = Net::HTTP.new(uri.host, uri.port)
  http.use_ssl = true
  http.open_timeout = 5
  http.read_timeout = 10                                  # ADR-101

  res = http.request(req)
  if res.code.to_i == 429
    return { ok: false, status: 429,
             retry_after: res['Retry-After'].to_i }
  end

  data = JSON.parse(res.body)
  { ok: res.is_a?(Net::HTTPSuccess), status: res.code.to_i,
    access_token: data['access_token'],
    expires_in: data['expires_in'],
    refresh_token: data['refresh_token'] }                # gelmeyebilir (nil)
end
```

**Neden böyle:** `set_form_data` form kodlamasını üstlenir; `Retry-After` gövde dışında başlıktan
okunur; `refresh_token` nil kalabilir — çağıran taraf (TokenStore) eski değeri korur.

**Blok C — 429 Retry-After döngüsü:**

```ruby
def with_retry(max_attempts: 3)
  attempt = 0
  loop do
    attempt += 1
    result = yield
    return result unless result[:status] == 429
    wait = (result[:retry_after] || [30, 2**attempt].min).to_i
    sleep(wait)                                           # Retry-After kadar bekle
    return { ok: false, status: 429, exhausted: true } if attempt >= max_attempts
  end
end
```

**Neden böyle:** blok `yield` ile taşıyıcıya bağlanır (test edilebilir); bütçe bitince bildirimli
sonuç döner; bekleme tam `Retry-After` değerine görelenir.

## 16. SQL — token saklama şeması (AES-256-GCM sütunları)

> **Kural:** MySQL yerleşik `AES_ENCRYPT` yardımcı işlevinin GCM modunu destekleyip desteklemediği
> **⚠️ VERIFICATION REQUIRED** — bu nedenle GCM şifrelemesi **uygulama katmanında** yapılır; veritabanı
> yalnız `nonce` + `ciphertext` + `tag` sütunlarını **bayt** olarak saklar. Şema K007/data'ya aittir
> (BCNF), bu dosya yalnız taslaktır.

**Blok 1 — DDL (nonce + ciphertext + tag):**

```sql
CREATE TABLE service_token (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    service        VARCHAR(32)     NOT NULL,          -- 'spotify', 'apple_music', 'tidal', ...
    account_ref    VARCHAR(64)     NOT NULL,          -- CoreMusic hesabı kimliği (token DEĞİL)
    access_nonce   BINARY(12)      NOT NULL,          -- GCM nonce (12 bayt)
    access_cipher  VARBINARY(2048) NOT NULL,          -- AES-256-GCM şifreli erişim token'ı
    access_tag     BINARY(16)      NOT NULL,          -- GCM doğrulama etiketi
    refresh_nonce  BINARY(12)      NULL,              -- refresh yoksa NULL
    refresh_cipher VARBINARY(2048) NULL,
    refresh_tag    BINARY(16)      NULL,
    key_version    SMALLINT UNSIGNED NOT NULL DEFAULT 1,   -- anahtar döngüsü
    scope          VARCHAR(512)    NULL,              -- servisin verdiği kapsam (düz metin)
    expires_at     DATETIME        NOT NULL,          -- UTC — expires_in'den türetilir
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_service_account (service, account_ref),
    KEY ix_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
```

**Neden böyle:** şifreli alanlar tek bir VARBINARY sütunda birleştirilmez — nonce/tag ayrı
`BINARY` sütunlarda durur (GCM'in bayt uzunlukları sabittir); `expires_at` indeksi RefreshScheduler'ın
`listExpiringWithin` sorgusunu destekler; token sütunları asla `SELECT *` ile çekilmez.

**Blok 2 — vadesi gelenlerin okunması (TokenStore/ListExpiring):**

```sql
SELECT service, account_ref, scope, expires_at, key_version
FROM service_token
WHERE expires_at <= UTC_TIMESTAMP() + INTERVAL 10 MINUTE
ORDER BY expires_at ASC
LIMIT 100;
```

**Neden böyle:** sorgu yalnız **meta veriyi** seçer (token değerleri seçilmez → log sızıntısı
imkânsız); `UTC_TIMESTAMP()` ile UTC tutarlılığı korunur; `LIMIT` toplu işleme bütçesini sınırlar.

**Blok 3 — anahtar döndürme (rotation) işaretlemesi:**

```sql
UPDATE service_token
SET key_version = key_version + 1,
    expires_at  = expires_at          -- vade değiştirilmez; yenileme RefreshScheduler'da
WHERE service = 'spotify'
  AND key_version = 1;
```

**Neden böyle:** rotasyon **immediately** token'ı silmez (erişim kesintisi olmuyor); `key_version`
artışı SecretVault'taki aktif anahtarla eşleşir — eski anahtar geçersiz kılınana kadar çift okuma
mümkündür.

## 17. YAPMA ↔ YAP

| # | YAPMA (yanlış) | YAP (doğru kalıp) | Gerekçe |
|---|---|---|---|
| 1 | `random()`/`rand()` ile verifier üretmek | kriptografik üreteç (`random_bytes`, `SecureRandom`, `secrets`, `crypto.getRandomValues`) | verifier tahmin edilebilir → PKCE çöker |
| 2 | challenge'ı standart base64 ile üretmek | base64url, dolgusuz | doküman kuralı base64url'dur |
| 3 | `state` üretip doğrulamamak | geri dönünce `verifyState` — uyuşmazlıkta iptal | ADR-010/CSRF |
| 4 | token'ı log'a/`.md`'ye yazmak | yalnız TokenStore (şifreli) / `.env` | kök AGENTS.md §5 |
| 5 | 429'da anında tekrar denemek | `Retry-After` kadar bekle, bütçe koy | kota kilidi (Spotify 30 sn pencere) |
| 6 | yeni refresh gelmeyince eskisini silmek | eskisini **koru** | Spotify kuralı |
| 7 | timeout'suz istek atmak | timeout/retry/fallback (ADR-101) | askıda kalma |
| 8 | CoreMusic oturum JWT'siyle harici çağrı | ayrı kimlik/zarf (ADR-095) | oturum gaspı riski |
| 9 | uydurma HTTP durumu/limit/sürüm yazmak | gerçek durum + ⚠️ VERIFICATION REQUIRED | Zero-Hallucination |
| 10 | kodda gerçek anahtar tutmak | `YOUR_CLIENT_ID` / `getenv(...)` / SecretVault | sızıntı riski |

## 18. Açık sorular

1. Üretim dili hangisi? (PHP 8.4 proje ana dili; 13 blok yalnız kalıp çeşitliliğidir.)
2. Rust/C/C++ bloklarındaki paket sürümleri (`sha2`, `rand`, OpenSSL, libcurl) → ⚠️ VERIFICATION REQUIRED.
3. `YOUR_AUTHORIZE_ENDPOINT` host'ları (Spotify/Tidal/Apple) doğrulanacak mı? → ⚠️ VERIFICATION REQUIRED.
4. Token şemasındaki `account_ref` hangi CoreMusic alanından türetilecek? (ADR-095 ayrımı.)
5. Şifreleme anahtarı `.env`'de mi, KMS benzeri birimde mi? (K009-security kararı.)
6. 429 bütçesi (maxAttempts) ve üst sınır (30 sn) onaylandı mı? (kapı 2.)
7. SQL şeması hangi migration dosyasına bağlanacak? (K007/data sahipliğinde.)
8. Bloklar üretim diline taşınırken hangi dilin test dosyası esas alınacak? (K18/testing.)

**Authority:** Bayram Ali / Vault Steward
