---
title: "Protokol Dönüşümü — Kod Örnekleri (13 Dil)"
node_id: "K013-integraion/protocol-adapters/proto-kod-ornekleri"
type: architecture
category: protocol-adapters
group: kod
status: active
date: 2026-10-16
authority: "SSOT: K013-integraion/protocol-adapters"
upstream:
  - "K013-integraion/protocol-adapters/proto-anayasa.md (sınır ve çift envanteri)"
  - "K013-integraion/protocol-adapters/proto-sozlesme.md (sürüm ve hata kodları)"
  - "K013-integraion/api-contracts/openapi/openapi-kod-ornekleri.md (kalıp)"
related:
  - "K013-integraion/adapters/adapters-anayasa.md (ikinci kardeş sınırı)"
---

# Protokol Dönüşümü — Kod Örnekleri (13 Dil)

> **Tarih:** 2026-10-16 · **Grup:** kod · **Erişim:** K013 → kademe-3
>
> Bu dosya, `proto-anayasa.md` §3'te envanteri verilen 5 protokol çiftinin
> dil-bağımsız dönüşüm mantığını **13 dilde** somutlaştırır. Amaç
> "çalışan üretim kodu" değil; **dönüşüm algoritmasının dil-kalıbı**nı
> göstermektir (her blokta rol ve statü açıkça yazılır).

> **⚠️ TASARIM İDDİASI (ortak):** Bu dosyadaki 13 bloğun tamamı
> **açıklayıcı / örnekleyici** tiptir — hiçbiri bu repo'da çalıştırılmamış
> veya derlenmemiştir (kanıt: `proto-dogrulama.md` §8 repo disk taraması;
> protokol dönüşümü sınıfı repo'da YOK). Kod blokları yalnızca her dilde
> aynı olan **dönüşüm kalıbını** ifade eder; üretim kullanımı için
> `proto-sozlesme.md` §6-§7 (hata + ortak kurallar) ve `proto-dogrulama.md`
> (L1-L6 doğrulama) ile birlikte okunmalıdır. Blok bazlı ek kaynak
> yoktur; ortak dayanak `proto-web-arastirma.md` S1-S15'tir (P1/P3/P5
> referans belgeleri o dosyada kanıtlanmıştır).

> **⚠️ VERIFICATION REQUIRED (repo):** Bu örneklerde adı geçen hiçbir
> sınıf/yardımcı (`ProtoJson`, `RouteTable` hariç — o diskte var)
> repo'da varmış gibi KULLANILMAZ; `RouteTable` referansı dahi
> "protokol dönüşümü" iddiası TAŞIMAZ (bkz. `proto-dogrulama.md` §8).

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi, çocuklar, kapılar (bu dosya) | 2 |
| mimari | `proto-anayasa.md` | Kimlik, sınır (adapters + K014), 5 çift envanteri, 10 protokol kimlik kartı | 3 |
| mimari | `proto-sozlesme.md` | Çift bazında tanım + sürüm + pazarlık + hata kodları | 3 |
| kod | `proto-dogrulama.md` | L1-L6 doğrulama katmanları, CI kapısı, repo disk kanıtı | 3 |
| kod | `proto-kod-ornekleri.md` | **Bu dosya — 13 dilde dönüşüm kalıbı** | 3 |
| kanit | `proto-web-arastirma.md` | 15 resmi URL doğrulama + elenenler | 3 |

---

## 1. Ortak Kurallar (tüm bloklar)

| Kural | İfade |
|---|---|
| Tek yön kuralı | Protokol dönüşümü **düzleminde** tek yönlü değildir; her iki yön de tanımlıdır (P1: `encode`/`decode` çifti gibi). |
| Hata → durum eşlemesi | `proto-sozlesme.md` §7'deki ortak tablo bağlayıcıdır; bloklar `400/401/403` gibi **yalnız** o tablodaki kodları fırlatır. |
| Pazarlık | P1 (Accept), P4 (Upgrade), P5 (scope) pazarlık adımları blok içinde **yorum satırıyla** işaretlenmiştir. |
| Boyut sınırı | P3: 2 GiB protobuf serileştirilmiş mesaj sınırı (`proto-sozlesme.md` S12 / `proto-dogrulama.md` §2.3); P5 Bearer başlık boyutu sunucu politikası. |
| `wire` kodu | P2'nin `4/5/6` wire tipleri `proto-sozlesme.md` §3.2'deki protobuf tanımıyla aynıdır (RFC değil, protobuf şeması — kaynak `proto-web-arastirma.md` S15). |

---

## 2. Dil Blokları (13)

# Dil: PHP 8.4 — Rol: JSON ↔ XML dönüşümü (P3 yönü, sunucu tarafı)

```php
<?php
declare(strict_types=1);

/**
 * K013 / protocol-adapters — JSON → XML yönlü kalıp (örnekleyici).
 * Statü: TASARIM İDDİASI — repo'da karşılığı YOK (bkz. proto-dogrulama.md §8).
 */
final class JsonToXmlConverter
{
    /** RFC 7301 Content-Type uyumu: application/xml (+ Encoding profili). */
    public function convert(array $json, string $root = 'response'): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $rootEl = $doc->createElement($root);
        $this->attach($doc, $rootEl, $json);
        $doc->appendChild($rootEl);

        // Pazarlık (P3): istemci "Accept: application/xml" gönderdiyse
        // yanıt Content-Type: application/xml; charset=utf-8 olmalı.
        header('Content-Type: application/xml; charset=utf-8');
        return $doc->saveXML();
    }

    private function attach(DOMDocument $doc, DOMElement $parent, array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $child = $doc->createElement((string) $key);
                $this->attach($doc, $child, $value);
                $parent->appendChild($child);
            } else {
                // Hata eşlemesi (§1): JSON null/boole/sayı/XML metni ayrımı
                // kontrolsüz kopyalanırsa bozulur — tip ayrımı zorunlu.
                $parent->appendChild($doc->createElement(
                    (string) $key,
                    is_bool($value) ? ($value ? 'true' : 'false') : (string) $value
                ));
            }
        }
    }
}
```

# Dil: JavaScript (ES2024) — Rol: SSE → olay akışı yorumlayıcı (P4 uc-1)

```javascript
// K013 / protocol-adapters — SSE ayrıştırma kalıbı (örnekleyici).
// Statü: TASARIM İDDİASI — WHATWG SSE alan adları S9 ile kanıtlanmıştır
// (proto-web-arastirma.md S9); bu yorumlayıcı repo'da YOK.
//
// Pazarlık (P4): yanıt başlığı text/event-stream; charset yalnız UTF-8
// (S9). 204 alınırsa yeniden bağlanma KAPANIR (S9: kapanışta 204).

const SSE_FIELDS = new Set(['event', 'data', 'id', 'retry']); // S9

export function createSseParser(onEvent) {
  let buffer = '';
  let eventType = 'message';
  let dataLines = [];
  let lastId = null;
  let retryMs = null;

  return {
    push(chunk) {
      buffer += chunk;
      // Her satır sonu: \n, \r\n veya \r (S9: ayrı satırlarda event/data/id/retry)
      const lines = buffer.split(/\r\n|\r|\n/);
      buffer = lines.pop(); // yarım satır tamponda kalır
      for (const line of lines) {
        if (line === '') { // boş satır = olay sonu
          if (dataLines.length > 0) {
            onEvent({ type: eventType, data: dataLines.join('\n'), lastId, retryMs });
          }
          eventType = 'message';
          dataLines = [];
          continue;
        }
        if (line.startsWith(':')) continue; // yorum satırı (S9)
        const idx = line.indexOf(':');
        const field = idx === -1 ? line : line.slice(0, idx);
        let value = idx === -1 ? '' : line.slice(idx + 1);
        if (value.startsWith(' ')) value = value.slice(1);
        if (!SSE_FIELDS.has(field)) continue; // bilinmeyen alan atlanır (S9)
        if (field === 'event') eventType = value;
        else if (field === 'data') dataLines.push(value);
        else if (field === 'id') lastId = value;   // NUL yoksa geçerli (S9)
        else if (field === 'retry') retryMs = Number(value);
      }
    },
    getLastId: () => lastId,
    getRetry: () => retryMs,
  };
}
```

# Dil: TypeScript — Rol: HTTP JSON → gRPC-Web çağrısı (P1 uc-2, tip güvenli)

```typescript
// K013 / protocol-adapters — P1 istemci kalıbı (örnekleyici).
// Statü: TASARIM İDDİASI — gRPC dört yöntem türü taşır (unary/server-stream/
// client-stream/bidirectional; proto-web-arastirma.md S12-S13). Bu dosya
// yalnız unary gösterir; repo'da karşılığı YOK.
//
// Pazarlık (P1 uc-2): wire `application/grpc+proto`;
// yanıt Content-Type da `application/grpc+proto` olmalı (S13).
// Trailer: grpc-status / grpc-message (S13) — HTTP 200 + trailer hata taşır.

export type GrpcStatus = { code: number; message?: string };

export interface UnaryCall<I, O> {
  path: string;            // "/pkg.Service/Method"
  request: I;
  metadata?: Record<string, string>; // "grpc-" öneki reserved (S13)
  encode: (input: I) => Uint8Array;  // protobuf varint/length-delimited (S15)
  decode: (bytes: Uint8Array) => O;
}

export async function grpcUnary<I, O>(
  base: string,
  call: UnaryCall<I, O>,
  signal?: AbortSignal,
): Promise<{ response: O; status: GrpcStatus }> {
  const body = call.encode(call.request);
  const headers: Record<string, string> = {
    'content-type': 'application/grpc+proto',      // S13
    te: 'trailers',                                 // trailer beklenir (S13)
    ...call.metadata,
  };
  // Pazarlık: 15 (HEALING / deadline) → DEADLINE_EXCEEDED (S13).
  const res = await fetch(`${base}${call.path}`, {
    method: 'POST',
    headers,
    body: frameGrpc(body),
    signal,
  });
  const status = Number(res.headers.get('grpc-status') ?? '0');
  if (status !== 0) {
    return { response: undefined as never, status: { code: status, message: res.headers.get('grpc-message') ?? '' } };
  }
  return { response: call.decode(await res.arrayBuffer()).valueOf() as O, status: { code: 0 } };
}

/** gRPC-JSON değil, gRPC-HTTP2 çerçevelesi: 1 bayrak + 4 bayrak uzunluk + payload (S13). */
function frameGrpc(payload: Uint8Array): Uint8Array {
  const framed = new Uint8Array(5 + payload.length);
  framed[0] = 0; // compression: uncompressed (S13)
  new DataView(framed.buffer).setUint32(1, payload.length, false);
  framed.set(payload, 5);
  return framed;
}
```

# Dil: Python 3.12 — Rol: JSON → XML yönlü çıktı + UTF-8 kısıtı (P3)

```python
# K013 / protocol-adapters — JSON → XML kalıbı (örnekleyici).
# Statü: TASARIM İDDİASI — UTF-8 kısıtı S9-S10 (XML Encoding prosedürü
# S10; SSE yalnız UTF-8 S9) ile ilişkilidir; repo'da karşılığı YOK.
from __future__ import annotations
import json
import xml.etree.ElementTree as ET

def json_to_xml(payload: dict, root: str = "response") -> bytes:
    """JSON sözlüğünü tek kök XML'e çevirir; UTF-8 bayt döndürür.

    Pazarlık (P3): istemci `Accept: application/xml` gönderdiyse yanıt
    `Content-Type: application/xml; charset=utf-8` olmalıdır.
    Hata eşlemesi (§1): list/tuple tek element altında toplanır —
    XML'in listesi yoktur, yapı kaybı burada bilinçlidir.
    """
    node = ET.Element(root)

    def attach(parent, value):
        if isinstance(value, dict):
            for k, v in value.items():
                child = ET.SubElement(parent, str(k))
                attach(child, v)
        elif isinstance(value, list):
            for item in value:
                attach(parent, item)
        else:
            parent.text = "" if value is None else str(value)

    attach(node, payload)
    return ET.tostring(node, encoding="utf-8", xml_declaration=True)

# Kullanım öncesi doğrulama zinciri: proto-dogrulama.md L2 (gelen gövde
# şema kontrolü) → dönüşüm → L4 (Content-Type ve charset eşlemesi).
```

# Dil: Go 1.22 — Rol: HTTP SSE → WebSocket yükseltme el sıkışması (P4 uc-2)

```go
// K013 / protocol-adapters — WebSocket Accept üretimi (örnekleyici).
// Statü: AÇIKLAYICI / KAYNAK-BEZELİ — RFC 6455 §4.2.2 formülü
// (proto-web-arastirma.md S8): Accept = base64(SHA-1(key + GUID)).
// GUID ve sürüm 13 S8'de kanıtlanmıştır. Repo'da karşılığı YOK.
package protoadapter

import (
	"crypto/sha1"
	"encoding/base64"
	"fmt"
	"net/http"
)

// wsGUID — RFC 6455 §1.3 sabiti (S8).
const wsGUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"

// Handshake, P4 el sıkışmasını doğrular ve Accept başlığını üretir.
// Kabul koşulları (S8): Upgrade: websocket + Connection: Upgrade +
// Sec-WebSocket-Key + Sec-WebSocket-Version: 13.
// HTTP 400 (S8): desteklenmeyen sürüm (version != 13).
// HTTP 426 (S8): Upgrade Required (Upgrade başlığı yoksa).
func Handshake(w http.ResponseWriter, r *http.Request) error {
	if r.Header.Get("Upgrade") == "" || r.Header.Get("Sec-WebSocket-Key") == "" {
		w.WriteHeader(http.StatusUpgradeRequired) // 426 (S8)
		return fmt.Errorf("upgrade required")
	}
	if v := r.Header.Get("Sec-WebSocket-Version"); v != "13" { // S8
		w.WriteHeader(http.StatusBadRequest) // 400 (S8)
		return fmt.Errorf("unsupported version %q", v)
	}
	sum := sha1.Sum([]byte(r.Header.Get("Sec-WebSocket-Key") + wsGUID))
	accept := base64.StdEncoding.EncodeToString(sum[:]) // S8
	w.Header().Set("Upgrade", "websocket")
	w.Header().Set("Connection", "Upgrade")
	w.Header().Set("Sec-WebSocket-Accept", accept)
	w.WriteHeader(http.StatusSwitchingProtocols) // 101
	return nil
}
```

# Dil: Java 21 — Rol: SOAP → REST yanıt zarfı (P2 uc-2 / HTTPP)

```java
// K013 / protocol-adapters — SOAP 1.2 yanıt zarfı kalıbı (örnekleyici).
// Statü: TASARIM İDDİASI — SOAP 1.2 MIME (application/soap+xml) ve
// XML Media Type yakalama kuralı proto-sozlesme.md §2 / §6'da
// kanıtlanmıştır (S1-S2); bu sınıf repo'da YOK.
//
// Pazarlık (P2): istemci `Accept: application/soap+xml` → yanıt
// `Content-Type: application/soap+xml; action=...`.
// Hata (§7): SOAP katmanı 400/500 + Fault kodu = SOAP 1.2 faultcode.
public final class SoapEnvelopeWriter {

    public String okEnvelope(String payloadXml) {
        // SOAP 1.2 yapı taşları: Envelope → Header(opsiyonel) → Body
        // (proto-sozlesme.md §2: Envelope/Body zorunlu, Header opsiyonel).
        return """
            <?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
              <soap:Body>%s</soap:Body>
            </soap:Envelope>
            """.formatted(payloadXml);
    }

    public String faultEnvelope(String code, String reason) {
        // Fault, HTTP durumuyla AYRI taşınır: SOAP 1.2 faultcode/severity
        // (proto-sozlesme.md §2/§7) — HTTP 200 + Fault geçerli olan
        // "iyimser" profildir; katı profilde HTTP hata durumu kullanılır.
        return """
            <?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
              <soap:Body>
                <soap:Fault>
                  <soap:Code><soap:Value>soap:%s</soap:Value></soap:Code>
                  <soap:Reason><soap:Text xml:lang="tr">%s</soap:Text></soap:Reason>
                </soap:Fault>
              </soap:Body>
            </soap:Envelope>
            """.formatted(code, reason);
    }
}
```

# Dil: C# (.NET 9) — Rol: OAuth Bearer doğrulama → API key'e dönüşüm kapısı (P5)

```csharp
// K013 / protocol-adapters — P5 iki yüzü (örnekleyici).
// Statü: AÇIKLAYICI — RFC 6750 hata HTTP 400/401/403 eşlemesi ve
// "tek yöntem" kuralı proto-sozlesme.md §4 / proto-web-arastirma.md S7
// ile kanıtlanmıştır. API key yüzünün evrensel standardı YOKTUR →
// ⚠️ VERIFICATION REQUIRED (proto-sozlesme.md §6 — API key yüzü şartnamesiz).
//
// Pazarlık (P5): 401 + WWW-Authenticate: Bearer realm=..., error=...
// (S7). Eksik kapsam → 403 insufficient_scope (S7).
using System.Net;
using System.Net.Http.Headers;

namespace K013.ProtocolAdapters;

public sealed class BearerToApiKeyGateway(HttpClient http, ApiKeySink sink)
{
    // RFC 6750: üç yöntem (Authorization header / form / URI) TEK olmalı (S7).
    public async Task<GatewayResult> ForwardAsync(HttpRequestMessage inbound)
    {
        var token = ExtractSingleMethod(inbound);
        if (token is null)
            return GatewayResult.Fail(HttpStatusCode.BadRequest, "invalid_request"); // 400 (S7)

        // Yüz-1 (Bearer): token'ı doğrula; geçersizse 401 invalid_token (S7).
        if (!await ValidateBearerAsync(token))
            return GatewayResult.Fail(HttpStatusCode.Unauthorized, "invalid_token"); // 401 (S7)

        // Yüz-2 (API key): token yerine kendi anahtarımızla ikinci/atlayıcı çağrı.
        // ⚠: Bu eşleme K013-özel bir karardır, evrensel spec'ten türetilmez.
        var outbound = new HttpRequestMessage(inbound.Method, sink.RouteFor(inbound.RequestUri!));
        outbound.Headers.Add("X-Api-Key", sink.CurrentKey); // ⚠️ TASARIM İDDİASI
        foreach (var (k, v) in inbound.Headers)
            if (k.StartsWith("X-Proto-", StringComparison.Ordinal))
                outbound.Headers.TryAddWithoutValidation(k, v);

        var res = await http.SendAsync(outbound);
        // Yetersiz kapsam: 403 insufficient_scope (S7) — burada 403 pass-through.
        return GatewayResult.From(res);
    }

    private static string? ExtractSingleMethod(HttpRequestMessage req)
    {
        if (req.Headers.TryGetValues("Authorization", out var vals))
        {
            var list = vals.ToList();
            if (list.Count != 1) return null;           // tek yöntem kuralı (S7)
            AuthenticationHeaderValue.TryParse(list[0], out var h);
            return h?.Scheme == "Bearer" ? h.Parameter : null;
        }
        // Form (application/x-www-form-urlencoded) ve URI yüzleri bu
        // örnekte bilinçli eksiktir — üç yöntemden FAZLASI tek çağrıda
        // kullanılamaz (S7).
        return null;
    }

    private Task<bool> ValidateBearerAsync(string token) => Task.FromResult(token.Length > 0);
    public sealed record GatewayResult(HttpStatusCode Status, string? Error) { public static GatewayResult Fail(HttpStatusCode s, string e) => new(s, e); public static GatewayResult From(HttpResponseMessage r) => new(r.StatusCode, null); }
    public interface ApiKeySink { Uri RouteFor(Uri u); string CurrentKey { get; } }
}
```

# Dil: Rust 1.83 — Rol: protobuf varint kod çözme (P2 alt katmanı)

```rust
// K013 / protocol-adapters — protobuf varint okuyucu (örnekleyici).
// Statü: AÇIKLAYICI / KAYNAK-BEZELİ — tag = (field << 3) | wire_type ve
// 6 wire tipi (0 varint, 1 64-bit, 2 length-delimited, 3/4 grup başlangıç/
// bitiş, 5 32-bit), ayrıca 2 GiB şema sınırı: proto-sozlesme.md §3.2
// (proto-web-arastirma.md S15). Repo'da karşılığı YOK.
//
// P2 bağlamı: XML/SOAP ile dönüşümde binary taşıma katmanı değil, yalnız
// P1'in binary/pas-through yüzü devreye girer (P2 = metin çifti).

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum WireType {
    Varint = 0,       // S15
    Fixed64 = 1,      // S15
    LengthDelimited = 2, // S15
    StartGroup = 3,   // S15 (deprecated)
    EndGroup = 4,     // S15 (deprecated)
    Fixed32 = 5,      // S15
}

pub fn read_varint(buf: &[u8], pos: &mut usize) -> Result<u64, ProtoErr> {
    let mut result: u64 = 0;
    let mut shift = 0u32;
    loop {
        let byte = *buf.get(*pos).ok_or(ProtoErr::Truncated)?; // 2 GiB sınırı (S15)
        *pos += 1;
        result |= u64::from(byte & 0x7F) << shift;
        if byte & 0x80 == 0 {
            return Ok(result);
        }
        shift += 7;
        if shift >= 64 {
            return Err(ProtoErr::VarintOverflow);
        }
    }
}

pub fn split_tag(tag: u64) -> (u32, WireType) {
    let field = (tag >> 3) as u32;                       // S15
    let wire = match tag & 0x07 {
        0 => WireType::Varint,
        1 => WireType::Fixed64,
        2 => WireType::LengthDelimited,
        3 => WireType::StartGroup,
        4 => WireType::EndGroup,
        5 => WireType::Fixed32,
        _ => return (field, WireType::Varint), // geçersiz: çağrıcı reddetmeli
    };
    (field, wire)
}

#[derive(Debug)]
pub enum ProtoErr { Truncated, VarintOverflow }
```

# Dil: C++20 — Rol: P3 ALPN uzunluk alanı + içerik tipi eşlemesi

```cpp
// K013 / protocol-adapters — protokol seçimi (ALPN) ve uzunluk alanı (örnekleyici).
// Statü: AÇIKLAYICI/KISMI — ALPN etiketleri ("http/1.1"·"h2"·"h3") ve
// 2^31-1 uzunluk alanı RFC 7301'den gelir; **o belge bu turda
// ÇEKİLMEDİ** → ⚠️ VERIFICATION REQUIRED (proto-web-arastirma.md §4 ve
// §6 madde 1). Bu blok yalnız kendi iddiasını gösterir; dayanaksız
// satırlar üretimde kullanılmaz. Repo'da karşılığı YOK.
#include <array>
#include <cstdint>
#include <cstring>
#include <span>
#include <stdexcept>
#include <string_view>

namespace k013::proto {

// ALPN: OPENSSL_ENABLE_NEXTPROTONEG + SSL_set_alpn_protos benzeri seçim
// (kütüphane somutu ⚠️ TASARIM İDDİASI). Bilinen etiketler S11:
inline constexpr std::array<std::string_view, 3> kAlpnKnown{
    "http/1.1", "h2", "h3"};

enum class ProtoPair { JsonXml, GrpcHttp, SoapRest, SseWs, BearerApiKey };

// Eşleme tablosu proto-anayasa.md §3 çift envanteriyle birebir aynıdır.
inline ProtoPair select(std::string_view alpn) {
    if (alpn == "h2" || alpn == "h3") return ProtoPair::GrpcHttp;
    if (alpn == "http/1.1")           return ProtoPair::JsonXml;
    throw std::invalid_argument("unsupported ALPN protocol");
}

// ALPN uzunluk alanı: 1..2^31-1 (RFC 7301 — ⚠️ bu turda çekilmemişti).
// Sınır aşımı yine de reddedilir (savunmacı davranış, şartname atfı yok).
constexpr std::uint32_t kMaxAlpnLen = 0x7FFFFFFFu; // S11

inline std::uint32_t checked_length(std::span<const std::uint8_t> frame) {
    if (frame.size() < 4) throw std::length_error("short ALPN length");
    std::uint32_t n = 0;
    for (int i = 0; i < 4; ++i) n = (n << 8) | frame[i];
    if (n == 0 || n > kMaxAlpnLen) throw std::length_error("ALPN length out of range"); // ⚠️ RFC 7301 çekilmemişti
    return n;
}

} // namespace k013::proto
```

# Dil: Ruby 3.3 — Rol: HTTP başlık/pazarlık doğrulayıcı (P1 + P4)

```ruby
# K013 / protocol-adapters — başlık pazarlık doğrulayıcı (örnekleyici).
# Statü: TASARIM İDDİASI — Content-Type tanımı ve 415 (Unsupported Media
# Type) / 406 (Not Acceptable) HTTP durumları RFC 9110'dan gelir
# (proto-web-arastirma.md S6); 415/406 eşlemesi proto-dogrulama.md L4'tedir.
# Repo'da karşılığı YOK.
module K013
  module Proto
    PAIRS = {
      json_xml:  'application/json|application/xml',   # P3
      grpc_http: 'application/grpc+proto',            # P1 (wire)
      soap_rest: 'application/soap+xml',              # P2
      sse_ws:    'text/event-stream|websocket',       # P4
      bearer_key: 'authorization|x-api-key',          # P5
    }.freeze

    # Accept pazarlığı (P1/P3): istemci en az bir çift kabul ediyorsa geç.
    def self.negotiate!(headers)
      accept = headers['accept'].to_s
      return :passthrough if accept.empty? || accept.include?('*/*')

      wanted = accept.split(',').map { |p| p.split(';').first.strip.downcase }
      supported = PAIRS.values.flat_map { |v| v.split('|') }
      unless (wanted & supported).any?
        # 406 Not Acceptable (S6 + proto-dogrulama.md L4)
        raise ProtocolError.new(406, 'not_acceptable')
      end
      :converted
    end

    # İstek gövdesi tipi (L2 şema kapısı): bilinmeyen tip → 415 (S6).
    def self.media_type_ok!(headers)
      ct = headers['content-type'].to_s.split(';').first.strip.downcase
      return if ct.empty?
      known = PAIRS.values.flat_map { |v| v.split('|') }
      raise ProtocolError.new(415, 'unsupported_media_type') unless known.include?(ct)
    end

    class ProtocolError < StandardError
      attr_reader :status, :error
      def initialize(status, error)
        @status = status
        @error  = error
        super("#{status} #{error}") # §7 ortak hata tablosuyla eşleşir
      end
    end
  end
end
```

# Dil: Kotlin 2.0 — Rol: WebSocket Accept + sürüm kontrolü (P4 uc-2)

```kotlin
// K013 / protocol-adapters — WS el sıkışması (örnekleyici).
// Statü: AÇIKLAYICI / KAYNAK-BEZELİ — GUID, sürüm 13 ve 400/426/101
// durumları RFC 6455'ten (proto-web-arastirma.md S8). Kotlin ipek/dokuma
// bağımlılığı belirsizdir → yalnız JDK API kullanılır. Repo'da YOK.
package k013.proto

import java.security.MessageDigest
import java.util.Base64

object WebSocketHandshake {
    // RFC 6455 §1.3 sabiti (S8).
    const val GUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"
    const val VERSION = "13" // S8: desteklenen tek sürüm

    sealed class Result {
        data class Accept(val headerValue: String) : Result() // → 101
        data class Reject(val status: Int, val reason: String) : Result()
    }

    fun evaluate(
        upgrade: String?,
        connection: String?,
        key: String?,
        version: String?,
    ): Result = when {
        // 426 Upgrade Required (S8) — Upgrade/Connection yoksa.
        upgrade.isNullOrBlank() || connection?.contains("Upgrade") != true ->
            Result.Reject(426, "upgrade required")
        key.isNullOrBlank() ->
            Result.Reject(400, "missing Sec-WebSocket-Key") // §7: invalid_request
        // 400 Bad Request (S8) — sürüm != 13.
        version != VERSION ->
            Result.Reject(400, "unsupported Sec-WebSocket-Version")
        else -> Result.Accept(acceptValue(key))
    }

    private fun acceptValue(key: String): String {
        val sha1 = MessageDigest.getInstance("SHA-1")
        val digest = sha1.digest((key + GUID).toByteArray(Charsets.US_ASCII))
        return Base64.getEncoder().encodeToString(digest) // S8
    }
}
```

# Dil: Swift 5.10 — Rol: SSE → actor ile olay durumu (P4 uc-1, eşzamanlılık)

```swift
// K013 / protocol-adapters — SSE durum makinesi (örnekleyici).
// Statü: TASARIM İDDİASI — SSE alanları ve "boş satır = olay sonu",
// "204 = yeniden bağlanma kapanır" kuralları S9 (proto-web-arastirma.md
// S9) ile sabittir; Swift karşılığı repo'da YOK.
import Foundation

public actor SseProcessor {
    public enum Field: String { case event, data, id, retry } // S9: 4 alan

    private var eventType = "message"
    private var dataLines: [String] = []
    private(set) public var lastId: String?
    public private(set) var retryMs: Int?

    // Bir satır işler; boş satır olayı teslim eder (S9).
    public func ingest(_ rawLine: String) -> (type: String, data: String)? {
        if rawLine.isEmpty {
            defer { eventType = "message"; dataLines.removeAll() }
            guard !dataLines.isEmpty else { return nil }
            return (eventType, dataLines.joined(separator: "\n"))
        }
        if rawLine.hasPrefix(":") { return nil }            // yorum (S9)
        let parts = rawLine.split(separator: ":", maxSplits: 1, omittingEmptySubsequences: false)
        let name = String(parts[0])
        guard let field = Field(rawValue: name) else { return nil } // bilinmeyen alan (S9)
        var value = parts.count > 1 ? String(parts[1]) : ""
        if value.hasPrefix(" ") { value.removeFirst() }     // tek boşluk (S9)
        switch field {
        case .event: eventType = value
        case .data:  dataLines.append(value)
        case .id:    if !value.contains("\0") { lastId = value } // NUL (S9)
        case .retry: retryMs = Int(value)
        }
        return nil
    }

    // 204 alındığında çağrılır: yeniden bağlanma KAPANIR (S9).
    public func stopReconnect() -> Bool { false }
}
```

# Dil: Elixir 1.17 — Rol: CloudEvents JSON → olay yayını (P6 deseni)

```elixir
# K013 / protocol-adapters — CloudEvents-zarflı olay yayını (örnekleyici).
# Statü: TASARIM İDDİASI — CloudEvents v1.0 (2019-10-24) ve CNCF Graduated
# (2024-01-25) künyesi proto-web-arastirma.md S14 ile kanıtlanmıştır;
# yapı (specversion/type/source/id) S14 türetilmiştir. Repo'da YOK.
#
# Not (proto-anayasa.md §4): CloudEvents tek başına bir protokol ÇİFTİ
# değildir — P1 (HTTP↔gRPC) ve P4 (SSE↔WS) taşıyıcısındaki olay
# zarfıdır.
defmodule K013.Proto.CloudEvent do
  @moduledoc """
  CloudEvents v1.0 JSON zarfı. Zorunlu alanlar: specversion, type, source, id (S14).
  """
  @enforce_keys [:specversion, :type, :source, :id]
  defstruct [:specversion, :type, :source, :id, :datacontenttype, :data, :time, :subject]

  @spec to_json(t()) :: {:ok, map()} | {:error, :missing_required}
  def to_json(%__MODULE__{} = ev) do
    required = [:specversion, :type, :source, :id]
    if Enum.all?(required, &Map.get(ev, &1)) do
      ev
      |> Map.from_struct()
      |> Enum.reject(fn {_k, v} -> is_nil(v) end)
      |> Map.new()
      |> then(&{:ok, &1})
    else
      {:error, :missing_required} # §7: invalid_request (400) ile eşleşir
    end
  end

  @spec publish(map(), module()) :: :ok | {:error, atom()}
  def publish(payload, broker) when is_map(payload) do
    # Pazarlık (P1/P4): taşıyıcı ne olursa olsun zarf aynı kalır —
    # dönüşüm katmanı yalnız JSON ↔ hedef şemayı çevirir.
    case to_json(struct(__MODULE__, Map.take(payload, [:specversion, :type, :source, :id]))) do
      {:ok, json} -> broker.send(json)
      err -> err
    end
  end
end
```

---

## §3. Blok Haritası (çift → bloklar)

| Çift | Hangi bloklar işliyor | Rol |
|---|---|---|
| P1 HTTP↔gRPC | TypeScript · C# · Elixir | wire `application/grpc+proto`, frame, metadata, CloudEvents taşıyıcı |
| P2 REST↔SOAP · JSON↔XML | PHP · Python · Java | Envelope, root, Content-Type `application/xml`/`application/soap+xml` |
| P3 JSON↔XML (P2 ile ortak aile) | PHP · Python · C++ | Accept pazarlığı, 2 GiB/uzunluk alanı, UTF-8 |
| P4 SSE↔WebSocket | JavaScript · Go · Kotlin · Swift | SSE alanları, Accept=base64(SHA-1(key+GUID)), 101/400/426, 204 |
| P5 OAuth bearer↔API key | C# · Ruby | 400/401/403 eşlemesi, tek yöntem, API key yüzü ⚠️ |

> **⚠️ VERIFICATION REQUIRED (harita):** P6 (CloudEvents) bilinçli olarak
> "5 çift" listesine EKLENMEMİŞTİR — `proto-anayasa.md` §4'te
> **sarmalayıcı desen** olarak tanımlanmıştır. Haritadaki P3 satırı,
> `proto-anayasa.md` §3'te P2 ile birlikte anılan JSON↔XML ailesini
> yansıtır; ayrı bir taşıyıcı DEĞİLDİR.

---

## § Kaynak Tablosu

| # | Kaynak | Tür | Tarih |
|---|--------|-----|-------|
| S1 | `proto-anayasa.md` (bu klasör) | mimari envanter + sınır | 2026-10-16 |
| S2 | `proto-sozlesme.md` (bu klasör) | sürüm + hata + pazarlık | 2026-10-16 |
| S3 | `proto-dogrulama.md` (bu klasör) | L1-L6 + repo disk kanıtı | 2026-10-16 |
| S4 | `proto-web-arastirma.md` (bu klasör) | S1-S15 URL doğrulaması | 2026-10-16 |
| S5 | `adapters-anayasa.md` (kardeş klasör) | sınır çapraz kontrolü | 2026-10-16 |
| S6 | `openapi-kod-ornekleri.md` (kalıp) | kalıp yapısı + stil | 2026-10-16 |

> **Kaynak notu:** Dil bloklarındaki olgusal iddialar (GUID, sürüm 13,
> wire tipleri, ALPN, SSE alanları, CloudEvents künyesi) tek tek
> `proto-web-arastirma.md` S6-S15'e bağlanmıştır; o dosyada her iddianın
> URL + erişim tarihi + durumu vardır. Blokların kendisi bu dosyanın
> **tasarım çıktısıdır** (⚠️ TASARIM İDDİASI, §Giriş).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
