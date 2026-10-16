---
node_id: K013-integraion/integration-testing/ittest-kod-ornekleri
node_type: technology-deepdive
title: "Entegrasyon Test Kod Örnekleri — 12 Dilde Dar Entegrasyon / Stub / Contract Kalıpları"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/integration-testing/ittest-anayasa
  - K013-integraion/integration-testing/ittest-sozlesme
  - K013-integraion/integration-testing/ittest-dogrulama
evidence:
  - "https://docs.phpunit.de/en/10.5/ (2026-10-16)"
  - "https://jestjs.io/docs/mock-functions (2026-10-16)"
  - "https://vitest.dev/guide/features/ (2026-10-16)"
  - "https://docs.pytest.org/en/stable/ (2026-10-16)"
  - "https://go.dev/testing/ (2026-10-16)"
  - "https://pkg.go.dev/net/http/httptest (2026-10-16)"
  - "https://junit.org/junit5/docs/current/user-guide/ (2026-10-16)"
  - "https://xunit.net/ (2026-10-16)"
  - "https://docs.rs/wiremock/latest/wiremock/ (2026-10-16)"
  - "https://google.github.io/googletest/ (2026-10-16)"
  - "https://rspec.info/documentation/ (2026-10-16)"
  - "https://kotest.io/docs/ (2026-10-16)"
validation:
  - "12 dil başlığı `# Dil: X — Rol: …` biçimindedir"
  - "Swift ve Elixir resmi doküman URL'leri bu turda doğrulanAMADI (404/boş) → o iki örnek ⚠️ işaretle taşınmıştır"
  - "Örneklerde gerçek CoreMusic uç noktası/anahtar ADI YOK; tüm URL'ler kasıtlı geçersiz alandır"
open_questions:
  - "Swift/Elixir resmi doküman kaynakları → ⚠️ VERIFICATION REQUIRED"
  - "CoreMusic hangi dillerde entegrasyon testi yazacak → UNKNOWN (QA stratejisi)"
group: kod
---

# Entegrasyon Test Kod Örnekleri — 12 Dil

> **Bu dosya ne değildir:** Repoda çalışan test dosyaları değildir; hepsi
> **kalıp**tır ve her örnek kendi dilinin resmi dokümanıyla eşleştirilmiştir
> (§Kaynak Tablosu). Doğrulanamayan iki dil açıkça `⚠️` ile işaretlidir.
>
> **Durum etiketi:** Kalıp IMPLEMENTED (belge) · Repo uygulaması ⚠️ PLANNED
> (`tests/Integration/` dizini 2026-10-16'da diskte YOK).

---

## §0 ELI15 — Bu dosya bir cümlede

Oniki dilde aynı entegrasyon testi kalıbını — "sahte uç hazırla → çağır →
sınır davranışını doğrula → periyodik sözleşmeyi ayrıca test et" —
her dilin kendi test çerçevesiyle gösterir.
(Kaynak: §Kaynak Tablosu [S1]–[S12] · sınır tanımı `ittest-anayasa.md`)

---

## §1 Kalıp Şeması (tüm diller ortak)

```text
Arrange : ortam sözleşmesi + stub uç hazırla (ittest-sozlesme §2-§3)
Act     : sınır kodunu çağır (gerçek HTTP/DB/fs değil, stub uç)
Assert  : durum kodu + gövde biçimi (sözleşmeye göre)
Teardown: stub'ı kapat, canlı temas yoksa doğrula (live_endpoints = [])
```

> **Ortak kural:** Örneklerdeki alan `example.invalid` **kasıtlı olarak
> geçersizdir** — gerçek bir uç nokta **uydurulmaz** (openapi serisi ile aynı
> kural).

---

## §2 12 Dil — Kalıplar

### # Dil: PHP (PHPUnit 10.5) — Rol: Sağlayıcı-bazlı dar entegrasyon (CoreMusic birincil dili)

```php
<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PaymentCallbackContractTest extends TestCase
{
    public function testCallbackReturnsDocumentedShape(): void
    {
        // Arrange — sahte uç (ortam sözleşmesi: stub_endpoints)
        $stub = self::createStub(PaymentGatewayInterface::class);
        $stub->method('charge')->willReturn(['status' => 'ok', 'ref' => 'REF000']);

        // Act
        $result = (new PaymentService($stub))->charge(1000);

        // Assert — sözleşmedeki biçim
        self::assertSame('ok', $result['status']);
        self::assertArrayHasKey('ref', $result);
    }
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | PHPUnit 10.5 (repo `composer.json` kanıtı) | [S1] · `.ai/AGENTS.md` §4 QA |
| Stub | `createStub` (nesne seviyesi taklit) | [S1] |
| Repo durumu | `tests/Integration/` **YOK** → bu kalıp diskte uygulanmadı | [R1] |

---

### # Dil: JavaScript (Jest) — Rol: Tüketici tarafı stub + sözleşme örneği

```js
// Dil: JavaScript — Rol: consumer stub (Jest mock fonksiyonu)
import { fetchTracks } from './tracksClient.js';

test('fetchTracks parses the documented shape', async () => {
  // Arrange — sahte fetch
  global.fetch = jest.fn().mockResolvedValue({
    ok: true,
    json: async () => ({ items: [{ id: '1', title: 'Song' }] }),
  });

  // Act
  const out = await fetchTracks();

  // Assert — sözleşme biçimi
  expect(out.items[0]).toHaveProperty('id');
  expect(global.fetch).toHaveBeenCalledWith(
    expect.stringContaining('/v1/tracks'),
    expect.anything(),
  );
});
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | Jest mock fonksiyonları (resmi kılavuz) | [S2] |
| Repo durumu | JS testleri `Vitest` ile koşulur (ci.yml satır 157) → Jest kalıbı **öneri** | [R2] |

---

### # Dil: JavaScript (Vitest) — Rol: Repodaki gerçek JS test çatısı

```js
// Dil: JavaScript — Rol: consumer stub (Vitest vi.fn)
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { fetchPlayerState } from './playerClient.js';

describe('playerClient contract', () => {
  beforeEach(() => { vi.restoreAllMocks(); });

  it('parses success scenario', async () => {
    vi.stubGlobal('fetch', vi.fn().mockResolvedValue({
      status: 200,
      json: async () => ({ playing: true, trackId: 't1' }),
    }));

    const state = await fetchPlayerState();

    expect(state.playing).toBe(true);
    expect(fetch).toHaveBeenCalledTimes(1);
  });
});
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | Vitest — resmi "mocking" özellikleri | [S3] |
| Repo durumu | `ci.yml` satır 151-157 "Vitest (unit)" | [R2] |

---

### # Dil: Python (pytest) — Rol: HTTP sınırı için stub uç (test yönlendirmeli)

```python
# Dil: Python — Rol: dar entegrasyon (pytest + monkeypatch stub uç)
import pytest
from myapp.client import fetch_tracks


def test_tracks_shape(monkeypatch):
    # Arrange — sahte HTTP cevabı
    class FakeResp:
        status_code = 200
        def json(self):
            return {"items": [{"id": "1", "title": "Song"}]}

    monkeypatch.setattr("myapp.client.requests.get",
                        lambda url, **kw: FakeResp())

    # Act
    out = fetch_tracks("https://api.example.invalid/v1/tracks")

    # Assert
    assert "items" in out
    assert out["items"][0]["id"] == "1"
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | pytest (resmi kılavuz; fixture/monkeypatch kültürü) | [S4] |
| Repo durumu | CoreMusic Python testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Go — Rol: `httptest` ile yerel sahte sunucu (dar entegrasyonun klasik şekli)

```go
// Dil: Go — Rol: dar entegrasyon (net/http/httptest sahte sunucu)
package client_test

import (
    "encoding/json"
    "net/http"
    "net/http/httptest"
    "testing"

    "example.invalid/myapp/client"
)

func TestFetchTracksShape(t *testing.T) {
    // Arrange — yerel sahte uç
    srv := httptest.NewServer(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Content-Type", "application/json")
        _ = json.NewEncoder(w).Encode(map[string]any{
            "items": []map[string]string{{"id": "1", "title": "Song"}},
        })
    }))
    defer srv.Close()

    // Act
    out, err := client.FetchTracks(srv.URL)

    // Assert
    if err != nil {
        t.Fatalf("unexpected error: %v", err)
    }
    if len(out.Items) == 0 {
        t.Fatal("expected at least one item")
    }
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | `net/http/httptest` — resmi paket dokümanı (`MockServer` benzeri `NewServer`) | [S6] |
| Çalıştırma | `go test` (resmi test paketi kılavuzu) | [S5] |
| Repo durumu | Go testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Java (JUnit 5/6) — Rol: Sağlayıcı testi + sahte servis

```java
// Dil: Java — Rol: dar entegrasyon (JUnit + yerel stub uç)
import org.junit.jupiter.api.Test;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

import static org.junit.jupiter.api.Assertions.*;

class WeatherClientIntegrationTest {

    @Test
    void parsesDocumentedShape() throws Exception {
        // Arrange — sahte uç (test ortamı)
        var stub = new StubHttpServer();   // kendi yerel stub'ınız
        stub.onGet("/v1/weather").respond(200, "{\"condition\":\"rain\"}");

        // Act
        var client = HttpClient.newHttpClient();
        var resp = client.send(
            HttpRequest.newBuilder(URI.create(stub.url() + "/v1/weather")).GET().build(),
            HttpResponse.BodyHandlers.ofString());

        // Assert
        assertEquals(200, resp.statusCode());
        assertTrue(resp.body().contains("condition"));
        stub.close();
    }
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | JUnit User Guide (güncel sürüm sayfası) | [S7] |
| Not | Makale örneğinde Java için WireMock **kural bloğu** ile uç sabitleme yaygın kalıptır | [S9] |
| Repo durumu | JVM testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: C# (xUnit) — Rol: Tüketici stub + sınır doğrulama

```csharp
// Dil: C# — Rol: consumer stub (xUnit)
using Xunit;

public class TrackClientTests
{
    [Fact]
    public async Task ParsesDocumentedShape()
    {
        // Arrange — sahte handler (stub uç)
        var handler = new StubHandler(status: 200,
            body: """{"items":[{"id":"1","title":"Song"}]}""");
        var client = new HttpClient(handler);

        // Act
        var out_ = await TrackClient.FetchAsync(client, "https://api.example.invalid/v1/tracks");

        // Assert
        Assert.NotEmpty(out_.Items);
        Assert.Equal("1", out_.Items[0].Id);
    }
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | xUnit.net v3 (resmi ana sayfa: `dotnet new xunit3`, `[Fact]`, `Assert.Equal`) | [S8] |
| Repo durumu | .NET testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Rust — Rol: Ayrı process'te koşan HTTP mock sunucusu

```rust
// Dil: Rust — Rol: dar entegrasyon (wiremock MockServer)
use wiremock::{MockServer, Mock, ResponseTemplate};
use wiremock::matchers::{method, path};

#[tokio::test]
async fn parses_documented_shape() {
    // Arrange — arka planda rastgele portta sahte sunucu
    let server = MockServer::start().await;
    Mock::given(method("GET"))
        .and(path("/v1/tracks"))
        .respond_with(ResponseTemplate::new(200)
            .set_body_json(serde_json::json!({"items": [{"id":"1"}]})))
        .mount(&server)
        .await;

    // Act
    let status = reqwest::get(format!("{}/v1/tracks", server.uri()))
        .await.unwrap().status();

    // Assert
    assert_eq!(status.as_u16(), 200);
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | wiremock 0.6.5 — `MockServer`, `Mock`, `ResponseTemplate`, rastgele port + izolasyon | [S10] |
| Repo durumu | Rust testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: C++ (GoogleTest) — Rol: Mock nesne ile sınır sözleşmesi

```cpp
// Dil: C++ — Rol: dar entegrasyon (GoogleTest + gmock)
#include <gtest/gtest.h>
#include "track_client.h"

using ::testing::Return;

class StubGateway : public Gateway {
 public:
  MOCK_METHOD(Response, Get, (const std::string& path), (override));
};

TEST(TrackClientTest, ParsesDocumentedShape) {
  // Arrange
  StubGateway gw;
  EXPECT_CALL(gw, Get("/v1/tracks"))
      .WillOnce(Return(Response{200, R"({"items":[{"id":"1"}]})"}));

  // Act
  TrackClient client(&gw);
  auto out = client.FetchTracks();

  // Assert
  ASSERT_FALSE(out.items.empty());
  EXPECT_EQ(out.items[0].id, "1");
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | GoogleTest User's Guide + gmock bölümleri (Mocking Cheat Sheet / for Dummies) | [S11] |
| Repo durumu | C++ testi (Neva Engine) **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Ruby (RSpec) — Rol: Tüketici stub (test double framework)

```ruby
# Dil: Ruby — Rol: consumer stub (rspec-mocks double)
require "rspec"

RSpec.describe TrackClient do
  it "parses the documented shape" do
    # Arrange — sahte servis yanıtı
    fake = double("track_api")
    allow(fake).to receive(:get).with("/v1/tracks")
      .and_return({ "items" => [{ "id" => "1" }] })

    # Act
    out = described_class.new(fake).tracks

    # Assert
    expect(out["items"].first["id"]).to eq("1")
  end
end
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | RSpec parçaları: `rspec-mocks` = "Test double framework" (resmi doküman indeksi) | [S12] |
| Repo durumu | Ruby testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Kotlin (Kotest) — Rol: FunSpec ile dar entegrasyon + matcher

```kotlin
// Dil: Kotlin — Rol: dar entegrasyon (Kotest FunSpec)
import io.kotest.core.spec.style.FunSpec
import io.kotest.matchers.shouldBe
import io.kotest.matchers.string.shouldContain

class TrackClientTest : FunSpec({
    test("parses the documented shape") {
        // Arrange — sahte uç
        val stub = StubServer().onGet("/v1/tracks").respond(200, """{"items":[{"id":"1"}]}""")

        // Act
        val out = TrackClient(stub.url).fetchTracks()

        // Assert
        out.items.isNotEmpty() shouldBe true
        stub.close()
    }
})
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | Kotest 6.2 — `FunSpec`, matcher'lar, `should` sözdizimi | [S13] |
| Repo durumu | Kotlin testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Swift — Rol: XCTest ile sınır testi

```swift
// Dil: Swift — Rol: dar entegrasyon (XCTest)
// ⚠️ VERIFICATION REQUIRED — XCTest resmi doküman URL'si bu turda
// doğrulanamadı (developer.apple.com sayfası fetch edilemedi).
import XCTest

final class TrackClientTests: XCTestCase {
    func testParsesDocumentedShape() throws {
        // Arrange — URLProtocol tabanlı stub uç
        URLProtocolStub.startStub(status: 200,
                                   body: #"{"items":[{"id":"1"}]}"#)

        // Act
        let out = try TrackClient.fetchTracks()

        // Assert
        XCTAssertFalse(out.items.isEmpty)
        XCTAssertEqual(out.items[0].id, "1")
    }
}
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | XCTest — **⚠️ resmi URL bu turda doğrulanamadı** (boş yanıt/404) | ⚠️ [X1] |
| Repo durumu | Swift testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

### # Dil: Elixir (ExUnit) — Rol: HTTP sınırı için sahte cevap

```elixir
# Dil: Elixir — Rol: dar entegrasyon (ExUnit + sahte HTTP modülü)
# ⚠️ VERIFICATION REQUIRED — ExUnit resmi doküman URL'si bu turda
# doğrulanamadı (hexdocs.pm 404).
defmodule TrackClientTest do
  use ExUnit.Case, async: true

  test "parses the documented shape" do
    # Arrange — sahte uç cevabı
    stub = fn _url -> {:ok, %{"items" => [%{"id" => "1"}]}} end

    # Act
    out = TrackClient.fetch_tracks(stub)

    # Assert
    assert [%{"id" => "1"} | _] = out["items"]
  end
end
```

| Alan | Değer | Kanıt |
|---|---|---|
| Çerçeve | ExUnit — **⚠️ resmi URL bu turda doğrulanamadı** (hexdocs 404) | ⚠️ [X2] |
| Repo durumu | Elixir testi **tespit edilmedi** → ⚠️ PLANNED | [R1] |

---

## §3 Dil → Çerçeve → Kaynak Eşlemesi (özet)

| # | Dil | Çerçeve/aracı | Kaynak | Doğrulama |
|---|---|---|---|---|
| 1 | PHP | PHPUnit 10.5 | [S1] | ✅ |
| 2 | JavaScript | Jest (mock) | [S2] | ✅ |
| 3 | JavaScript | Vitest | [S3] | ✅ |
| 4 | Python | pytest | [S4] | ✅ |
| 5 | Go | `testing` + `httptest` | [S5], [S6] | ✅ |
| 6 | Java | JUnit | [S7] | ✅ |
| 7 | C# | xUnit.net v3 | [S8] | ✅ |
| 8 | Rust | wiremock | [S10] | ✅ |
| 9 | C++ | GoogleTest/gmock | [S11] | ✅ |
| 10 | Ruby | RSpec (rspec-mocks) | [S12] | ✅ |
| 11 | Kotlin | Kotest | [S13] | ✅ |
| 12 | Swift | XCTest | [X1] | ⚠️ doğrulanamadı |
| 13 | Elixir | ExUnit | [X2] | ⚠️ doğrulanamadı |

> **Sayım notu:** 12 dil (JS iki çatıyla sayıldığı için 13 satır).

---

## §4 Ortak Kalıp Uyarıları

| # | Uyarı |
|---|---|
| 1 | Her örnekte Arrange → Act → Assert sırası korunur (Fowler makalesi bu yapıyı tüm test türleri için önerir) [S9] |
| 2 | Canlı uç **hiçbir** örnekte kullanılmaz — canlı temas `ittest-sozlesme.md` §4 kapısına bağlıdır |
| 3 | `example.invalid` alanları kasıtlıdır; gerçek uç uydurulmaz |
| 4 | Swift/Elixir örnekleri **⚠️ taşır**: çerçeve API'si bu turda resmi kaynakla doğrulanmadı |
| 5 | Repo'da karşılığı olmayan hiçbir örnek "mevcut" diye sunulmaz (§Repo durumu sütunu) |

---

## §5 Kapsam Dışı

1. Testlerin nasıl **koşturulacağı** → `ittest-dogrulama.md` §5 (CI)
2. Ortam/isolasyon → **K019-testing** (⚠️ sınır)
3. Gerçek repo test dosyaları → kod yüzeyi (bu seride üretilmez)
4. Araç kurulumu (package/composer ayrıntıları) → ⚠️ UNKNOWN (envanter çekilmedi)

---

## §6 Açık Sorular (özet)

| # | Soru | İşaret |
|---|---|---|
| Q1 | XCTest resmi doküman URL'si | ⚠️ VERIFICATION REQUIRED (fetch boş) |
| Q2 | ExUnit resmi doküman URL'si | ⚠️ VERIFICATION REQUIRED (hexdocs 404) |
| Q3 | CoreMusic hangi dillerde entegrasyon testi yazacak? | UNKNOWN (QA stratejisi) |
| Q4 | Repo'da Swift/Elixir/YAML vs. hangi dillerin kodu var? | ⚠️ VERIFICATION REQUIRED (bu turda envanter çekilmedi) |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | PHPUnit 10.5 resmi kılavuz — test yazma/koşum API'si | https://docs.phpunit.de/en/10.5/ | 2026-10-16 | ✅ çekildi |
| S2 | Jest mock fonksiyonları (`jest.fn`, `mockResolvedValue`) | https://jestjs.io/docs/mock-functions | 2026-10-16 | ✅ çekildi |
| S3 | Vitest mock özellikleri (`vi.fn`, `stubGlobal`) | https://vitest.dev/guide/features/ | 2026-10-16 | ✅ çekildi |
| S4 | pytest resmi kılavuz (fixture/monkeypatch kültürü) | https://docs.pytest.org/en/stable/ | 2026-10-16 | ✅ çekildi |
| S5 | Go testing paketi (`go test`) | https://go.dev/testing/ | 2026-10-16 | ✅ çekildi |
| S6 | `net/http/httptest` — sahte sunucu (`NewServer`) | https://pkg.go.dev/net/http/httptest | 2026-10-16 | ✅ çekildi |
| S7 | JUnit User Guide (güncel) | https://junit.org/junit5/docs/current/user-guide/ | 2026-10-16 | ✅ çekildi |
| S8 | xUnit.net v3 — `dotnet new xunit3`, `[Fact]`, `Assert` | https://xunit.net/ | 2026-10-16 | ✅ çekildi |
| S9 | Arrange/Act/Assert + WireMock'lu dar entegrasyon Java örneği | https://martinfowler.com/articles/practical-test-pyramid.html | 2026-10-16 | ✅ çekildi |
| S10 | wiremock 0.6.5 — `MockServer`, matcher, izolasyon | https://docs.rs/wiremock/latest/wiremock/ | 2026-10-16 | ✅ çekildi |
| S11 | GoogleTest User's Guide + gmock bölümleri | https://google.github.io/googletest/ | 2026-10-16 | ✅ çekildi |
| S12 | RSpec doküman indeksi — `rspec-mocks` test double framework | https://rspec.info/documentation/ | 2026-10-16 | ✅ çekildi |
| S13 | Kotest 6.2 ana sayfa — FunSpec + matcher | https://kotest.io/docs/ | 2026-10-16 | ✅ çekildi |
| X1 | XCTest resmi doküman | https://developer.apple.com/documentation/xctest | 2026-10-16 | ❌ çekilemedi (boş yanıt) → reddedildi |
| X2 | ExUnit resmi doküman | https://hexdocs.pm/exunit/ExUnit.html | 2026-10-16 | ❌ 404 → reddedildi (bkz. `ittest-web-arastirma.md` §Reddedilenler) |
| R1 | `tests/Integration/` yokluğu (tüm diller için) | `auth.coremusic.net/phpunit.xml` satır 10-12 | 2026-10-16 | ✅ disk |
| R2 | JS testleri Vitest ile | `.github/workflows/ci.yml` satır 151-157 | 2026-10-16 | ✅ disk |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `⚠️` işaretli iki dil için **kaynak yoktur** — o örnekler yalnız kalıp
> olarak taşınır, "resmi yöntem" diye kullanılamaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · 13 resmi test çerçeve dokümanı · **Tarih: 2026-10-16**
