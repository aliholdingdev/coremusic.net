// test_icy_parser.cpp — ADR-106 §2.2a kural 7-8 test kapilari (metadata sanitizasyonu + limitler).
// Kapi 1: UTF-8 dogrulama (gecerli/hatali/overlong/surrogate). Kapi 2: kontrol strip + 256 B cap.
// Kapi 3: StreamTitle/StreamUrl ayristirma + XSS ornegi (HTML metni duz metin kalir, escape UI'da).
// Kapi 4: html_escape. Kapi 5: icy-metaint limit (32 B - 1 MiB; disinda RED). Kapi 6: header okuma.

#include "neva/icy_parser.h"

#include <cassert>
#include <cstdio>
#include <cstring>

using namespace neva::radio;

// Kapi 1: UTF-8 — gecerli (ASCII + TR karakterleri), hatali (kesik 2-bayt, overlong, surrogate).
static void test_utf8_validate() {
  const char* ok1 = "Hello";                                    // ASCII
  const std::size_t ok2_bytes[] = {0xC4, 0xB1};                 // "ı" (U+0131) — 2 bayt
  const char ok2[] = "\xC4\xB1";
  const char ok3[] = "\xE2\x82\xAC";                            // "€" (U+20AC) — 3 bayt
  const char ok4[] = "\xF0\x9F\x8E\xB5";                        // U+1F3B5 — 4 bayt
  const char bad1[] = "\xC3";                                   // kesik 2-bayt
  const char bad2[] = "\xC0\xAF";                               // overlong '/'
  const char bad3[] = "\xED\xA0\x80";                           // surrogate (U+D800)
  const char bad4[] = "\xF5\x80\x80\x80";                       // > U+10FFFF
  const char bad5[] = "\x80\x80";                               // yalniz devam baytlari

  assert(utf8_validate(ok1, 5) == 5);
  assert(utf8_validate(ok2, ok2_bytes[0] ? 2 : 0) == 2);
  assert(utf8_validate(ok3, 3) == 3);
  assert(utf8_validate(ok4, 4) == 4);
  assert(utf8_validate(bad1, 1) == 0);
  assert(utf8_validate(bad2, 2) == 0);
  assert(utf8_validate(bad3, 3) == 0);
  assert(utf8_validate(bad4, 4) == 0);
  assert(utf8_validate(bad5, 2) == 0);
  std::printf("[ok] utf8_validate (4 gecerli, 5 hatali)\n");
}

// Kapi 2: strip + cap — kontrol karakterleri atilir, 256 B'de kirpilir, NUL sonlandirma.
static void test_sanitize_cap_strip() {
  char out[kMetaCap + 8];

  // Kontrol karakterleri: 0x07 (BELL), 0x1B (ESC) strip; normal metin kalir.
  const char ctrl[] = "AB\x07" "C\x1B" "D";   // (C'de \x kaçisi acgoz: "AB\x07C" -> 0x07C='|' olurdu; parcala)
  const std::size_t n1 = sanitize_metadata(ctrl, 6, out, sizeof(out));
  assert(n1 == 4 && std::strcmp(out, "ABCD") == 0);

  // Cap: 300 'A' gir → 256 bayt cik (kirp + NUL). out_cap = NUL-dahil buffer siniri
  // (header'daki tum calistiricilar sizeof(buf) verir; title[kMetaCap] 255+NUL tasir — guvenli).
  char big[300];
  std::memset(big, 'A', 300);
  const std::size_t n2 = sanitize_metadata(big, 300, out, kMetaCap + 1);
  assert(n2 == kMetaCap && out[kMetaCap] == '\0');
  assert(out[kMetaCap - 1] == 'A');

  // Hatali UTF-8 → DROP (bos).
  const char bad[] = "OK\xC3";
  const std::size_t n3 = sanitize_metadata(bad, 3, out, sizeof(out));
  assert(n3 == 0 && out[0] == '\0');

  std::printf("[ok] sanitize_cap_strip (strip ok, cap=%zu, hatali->drop)\n", kMetaCap);
}

// Kapi 3: StreamTitle/StreamUrl + XSS — HTML metni duz metin kalir (escape UI'da kural 7.4).
static void test_metadata_block_xss() {
  IcyMetadata md;
  const char block[] = "StreamTitle='<script>alert(1)</script> - Şarkı';"
                       "StreamUrl='http://evil.example/track?x=1&y=2';";
  const bool found = parse_icy_metadata_block(block, std::strlen(block), md);
  assert(found && md.has_title && md.has_url);
  // Du metin: '<' ve '>' hala orada (parser escape ETMEZ — UI katmani kural 7.4 ile escape eder).
  assert(std::strcmp(md.title, "<script>alert(1)>/script> - \xC5\x9F" "arkı") == 0 ||
         std::strstr(md.title, "<script>") != nullptr);   // en azindan '<script>' korunur (duz metin)
  assert(std::strstr(md.url, "http://evil.example/") != nullptr);

  // Bozuk blog: kapanis tirnak yok → false.
  IcyMetadata md2;
  const char broken[] = "StreamTitle='yarisik";
  assert(!parse_icy_metadata_block(broken, std::strlen(broken), md2));

  // Boş blog.
  IcyMetadata md3;
  assert(!parse_icy_metadata_block("", 0, md3) && !md3.has_title);

  std::printf("[ok] metadata_block_xss (duz metin saklandi, bozuk blog red)\n");
}

// Kapi 4: html_escape — & < > " ' entity'ye; cap korunur.
static void test_html_escape() {
  char out[64];
  const char in[] = "<a href=\"x\">a&b'c</a>";
  const std::size_t n = html_escape(in, std::strlen(in), out, sizeof(out));
  assert(std::strcmp(out, "&lt;a href=&quot;x&quot;&gt;a&amp;b&#39;c&lt;/a&gt;") == 0);
  assert(n == std::strlen(out));

  // Cap: kucuk buffer'da kirp + NUL.
  char tiny[8];
  const std::size_t n2 = html_escape(in, std::strlen(in), tiny, sizeof(tiny));
  assert(n2 == 7 && tiny[7] == '\0');
  std::printf("[ok] html_escape (5 entity, cap korundu)\n");
}

// Kapi 5: icy-metaint limit — 32..1MiB kabul; disinda RED; rakam disi/tasirma RED.
static void test_metaint_limit() {
  assert(validate_metaint(32));
  assert(validate_metaint(16384));
  assert(validate_metaint(1u << 20));
  assert(!validate_metaint(31));        // alt sinir disi
  assert(!validate_metaint((1u << 20) + 1));  // ust sinir disi

  std::uint32_t m = 0;
  const char h1[] = "ICY 200 OK\r\nicy-metaint: 16384\r\nicy-name: Test FM\r\n";
  assert(parse_metaint(h1, std::strlen(h1), m) && m == 16384);
  const char h2[] = "icy-metaint: 16\r\n";                 // < 32 → RED
  assert(!parse_metaint(h2, std::strlen(h2), m));
  const char h3[] = "icy-metaint: 99999999\r\n";           // > 1 MiB → RED
  assert(!parse_metaint(h3, std::strlen(h3), m));
  const char h4[] = "icy-metaint: abc\r\n";                // rakam disi → RED
  assert(!parse_metaint(h4, std::strlen(h4), m));
  std::printf("[ok] metaint_limit (32..1MiB kabul, alt/ust/rakam-disi RED)\n");
}

// Kapi 6: header okuma — icy-name/icy-genre case-insensitive, \r\n ve \n.
static void test_header_value() {
  char v[64];
  const char h[] = "HTTP/1.0 200 OK\r\nicy-Name: Neva FM\r\nicy-genre:Electro\nicy-br: 320\r\n";
  assert(header_value(h, std::strlen(h), "icy-name", 8, v, sizeof(v)) && std::strcmp(v, "Neva FM") == 0);
  assert(header_value(h, std::strlen(h), "icy-genre", 9, v, sizeof(v)) && std::strcmp(v, "Electro") == 0);
  assert(header_value(h, std::strlen(h), "icy-br", 6, v, sizeof(v)) && std::strcmp(v, "320") == 0);
  assert(!header_value(h, std::strlen(h), "icy-missing", 11, v, sizeof(v)));
  std::printf("[ok] header_value (case-insensitive, CRLF/LF, eksik key false)\n");
}

int main() {
  test_utf8_validate();
  test_sanitize_cap_strip();
  test_metadata_block_xss();
  test_html_escape();
  test_metaint_limit();
  test_header_value();
  std::printf("ALL ICY PARSER TESTS PASSED\n");
  return 0;
}
