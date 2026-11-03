/* icy_parser.h — ICY/Shoutcast v1 metadata parser + sanitizasyon (ADR-106 §2.2a kural 2, 7, 8).
 *
 * KAPSAM: yalnizca saf (pure) parser/islevler — network (libcurl) ve decoder AYRI modul.
 * Bu dosya tahsisat YOK (sabit buffer'lar), RT-DISI kullanilir (metadata lock-free kuyruk
 * ornegi ADR-106 §2.2a şemasi — parser non-RT producer'da calisir).
 *
 * ADR-106 §2.2a kural 7 (metadata sanitizasyonu — XSS kapi):
 *   1. UTF-8 DOGRULA — hatali bayt dizilimi varsa metin DROP (bos string).
 *   2. Kontrol karakterleri strip (0x00-0x1F, 0x7F; '\t' haric — istisna yok, tam strip).
 *   3. 256 BAYT CAP — asarsa kirma (truncation), NUL-sonlandir.
 *   4. HTML-escape render'da yapilir (native/web UI) — burada html_escape() islevi saglanir;
 *      parser metni DÜZ METIN olarak birakir (escape UI katmaninda, cunku cikti yeri farkli olabilir).
 *   5. StreamUrl HYPERLINK olarak render edilmez — parser bunu isaretlemez, UI kurali zorlar.
 *
 * ADR-106 §2.2a kural 8 (limitler): icy-metaint 32 B - 1 MiB araligi DISINDA -> stream RED
 * (validate_metaint() false dondurur). ICY text blogu <= 1 MB (parse icin: tek blogu isleriz,
 * cap 1 MB + kontrol blogu prefix'i 0-255 * 16 byte = max 4080 byte zaten sinirli).
 *
 * Format bilgisi (Shoutcast v1 ICY metadata — encoder/decoder sozlesmesi):
 *   icy-metaint: N byte ses (audio data) sonrasi 1 bayt uzunluk L, onu L*16 byte metadata blogu izler.
 *   Blog icerigi: "StreamTitle='...';StreamUrl='...';" (tek tirnakli alanlar, NUL-dolgulu).
 *   Header alani: "icy-name:", "icy-genre:", "icy-br:", "icy-url:", "content-type:" satirlari.
 */
#ifndef NEVA_ICY_PARSER_H
#define NEVA_ICY_PARSER_H

#include <cstddef>
#include <cstdint>
#include <cstring>

namespace neva::radio {

inline constexpr std::size_t kMetaCap = 256;          /* kural 7: 256 B cap */
inline constexpr std::uint32_t kMetaintMin = 32;      /* kural 8: RED araligi alt */
inline constexpr std::uint32_t kMetaintMax = 1u << 20; /* kural 8: RED araligi ust (1 MiB) */
inline constexpr std::size_t kIcyHeaderCap = 4096;    /* header satir cap'i (defansif) */

/* --- UTF-8 dogrulama (kural 7.1) ---------------------------------------------- */
/* Strict UTF-8: overlong, surrogate (U+D800-DFFF), > U+10FFFF RED. Return: bayt sayisi
 * veya 0 (hatali). Valide ise uzunluk, hatali ise 0. */
inline std::size_t utf8_validate(const char* p, std::size_t n) noexcept {
  std::size_t i = 0;
  while (i < n) {
    const unsigned char c = static_cast<unsigned char>(p[i]);
    std::size_t extra = 0;
    unsigned char lo = 0x80, hi = 0xBF;   /* devam baytlari araligi */
    std::uint32_t cp = 0;
    if (c <= 0x7F) { ++i; continue; }                       /* ASCII */
    else if (c >= 0xC2 && c <= 0xDF) { extra = 1; cp = c & 0x1Fu; }
    else if (c == 0xE0) { extra = 2; cp = 0; lo = 0xA0; }   /* overlong engeli */
    else if (c >= 0xE1 && c <= 0xEC) { extra = 2; cp = c & 0x0Fu; }
    else if (c == 0xED) { extra = 2; cp = 0x0D; hi = 0x9F; } /* surrogate engeli (U+D000-D7FF) */
    else if (c >= 0xEE && c <= 0xEF) { extra = 2; cp = c & 0x0Fu; }
    else if (c == 0xF0) { extra = 3; cp = 0; lo = 0x90; }   /* overlong + >U+10FFFF engeli */
    else if (c >= 0xF1 && c <= 0xF3) { extra = 3; cp = c & 0x07u; }
    else if (c == 0xF4) { extra = 3; cp = 0x04; hi = 0x8F; }
    else return 0;                                          /* 0x80-0xC1, 0xF5-0xFF hatali */
    if (i + extra >= n + 0 && i + extra > n - 1) return 0;  /* tasirma kontrolu */
    for (std::size_t k = 1; k <= extra; ++k) {
      const unsigned char cc = static_cast<unsigned char>(p[i + k]);
      const unsigned char l = (k == 1) ? lo : 0x80;
      const unsigned char h = (k == 1) ? hi : 0xBF;
      if (cc < l || cc > h) return 0;
      cp = (cp << 6) | (cc & 0x3Fu);
    }
    /* U+FFFE/U+FFFF (non-characters) birakilir — renderer zararsiz; sinir degerler kontrol edildi. */
    (void)cp;
    i += extra + 1;
  }
  return n;
}

/* --- Kontrol karakteri strip + cap (kural 7.2-7.3) ---------------------------- */
/* in'den out'a kopyala: kontrol karakterlerini (0x00-0x1F, 0x7F) at, cap'te kirp,
 * her zaman NUL-sonlandir. Return: yazilan uzunluk (NUL haric). */
inline std::size_t sanitize_copy(const char* in, std::size_t in_len, char* out,
                                 std::size_t out_cap) noexcept {
  if (out_cap == 0) return 0;
  std::size_t o = 0;
  for (std::size_t i = 0; i < in_len && o + 1 < out_cap; ++i) {
    const unsigned char c = static_cast<unsigned char>(in[i]);
    if (c < 0x20 || c == 0x7F) continue;   /* kontrol karakteri strip */
    out[o++] = static_cast<char>(c);
  }
  out[o] = '\0';
  return o;
}

/* --- Metavalidasyon giris noktasi (kural 7.1-7.3) ------------------------------ */
/* UTF-8 hatali → out bos (drop, donus 0); degilse strip + cap. Return: uzunluk. */
inline std::size_t sanitize_metadata(const char* in, std::size_t in_len, char* out,
                                     std::size_t out_cap) noexcept {
  if (out_cap == 0) return 0;
  out[0] = '\0';
  if (in == nullptr || in_len == 0) return 0;
  if (utf8_validate(in, in_len) == 0 && in_len > 0) return 0;   /* hatali UTF-8 → DROP */
  /* Kural 7: 256 bayt DOMAIN cap — buffer buyuk olsa bile asilamaz (NUL icin +1);
   * buffer kucukse fizik sinir korunur (title[kMetaCap] guvenli). */
  const std::size_t cap = (out_cap < kMetaCap + 1) ? out_cap : kMetaCap + 1;
  return sanitize_copy(in, in_len, out, cap);
}

/* --- icy-metaint limit (kural 8) ---------------------------------------------- */
/* 32 B - 1 MiB araliginda ise true (kabul); disinda false (stream RED). */
inline bool validate_metaint(std::uint32_t metaint) noexcept {
  return metaint >= kMetaintMin && metaint <= kMetaintMax;
}

/* --- StreamTitle/StreamUrl ayristirma (kural 2, 7) ----------------------------- */
/* Blog: "StreamTitle='...';StreamUrl='...';" — alanlar opsiyonel, sirasi sabit degil.
 * out_title/out_url sanitize edilmis (UTF-8 valide, strip, cap'li) NUL-sonlandirilmis.
 * Return: en az bir alan bulundu mu (true). Bulunamazsa out'lar bos. */
struct IcyMetadata {
  char title[kMetaCap] = {};
  char url[kMetaCap] = {};
  bool has_title = false;
  bool has_url = false;
};

inline bool parse_icy_metadata_block(const char* block, std::size_t block_len,
                                     IcyMetadata& out) noexcept {
  out.has_title = false;
  out.has_url = false;
  out.title[0] = '\0';
  out.url[0] = '\0';
  if (block == nullptr || block_len == 0) return false;

  auto find_field = [&](const char* key, std::size_t key_len, const char*& val,
                        std::size_t& val_len) noexcept -> bool {
    for (std::size_t i = 0; i + key_len <= block_len; ++i) {
      if (std::memcmp(block + i, key, key_len) != 0) continue;
      /* "key='" deseni — ac tirnak bul */
      std::size_t j = i + key_len;
      if (j >= block_len || block[j] != '\'') continue;
      ++j;
      const char* close = static_cast<const char*>(std::memchr(block + j, '\'', block_len - j));
      if (close == nullptr) return false;             /* kapanis tirnak yok → bozuk blog */
      val = block + j;
      val_len = static_cast<std::size_t>(close - (block + j));
      return true;
    }
    return false;
  };

  const char* v = nullptr;
  std::size_t vl = 0;
  if (find_field("StreamTitle=", 12, v, vl)) {   /* "StreamTitle=" = 12 karakter (eski 13 -> '\0' == ''' karsilastirmasi, hic eslesmezdi) */
    sanitize_metadata(v, vl, out.title, sizeof(out.title));
    out.has_title = out.title[0] != '\0';             /* drop edildiyse false say */
  }
  if (find_field("StreamUrl=", 10, v, vl)) {
    sanitize_metadata(v, vl, out.url, sizeof(out.url));
    out.has_url = out.url[0] != '\0';
  }
  return out.has_title || out.has_url;
}

/* --- HTML escape (kural 7.4 — UI katmani icin yardimci) ------------------------ */
/* & < > " ' → &amp; &lt; &gt; &quot; &#39;. Cap'li, NUL-sonlandirilmis. Return: uzunluk. */
inline std::size_t html_escape(const char* in, std::size_t in_len, char* out,
                               std::size_t out_cap) noexcept {
  if (out_cap == 0) return 0;
  std::size_t o = 0;
  auto put = [&](const char* s, std::size_t n) noexcept {
    for (std::size_t k = 0; k < n && o + 1 < out_cap; ++k) out[o++] = s[k];
  };
  for (std::size_t i = 0; i < in_len; ++i) {
    switch (in[i]) {
      case '&': put("&amp;", 5); break;
      case '<': put("&lt;", 4); break;
      case '>': put("&gt;", 4); break;
      case '"': put("&quot;", 6); break;
      case '\'': put("&#39;", 5); break;
      default:
        if (o + 1 < out_cap) out[o++] = in[i];
        break;
    }
  }
  out[o] = '\0';
  return o;
}

/* --- Header satir degeri cikarma (icy-name, icy-genre, icy-br, ...) ------------ */
/* "key: value" satirinda value'yu sanitize edip out'a yazar. Case-insensitive key eslesme.
 * Return: key bulundu mu. */
inline bool header_value(const char* headers, std::size_t headers_len, const char* key,
                         std::size_t key_len, char* out, std::size_t out_cap) noexcept {
  if (out_cap > 0) out[0] = '\0';
  if (headers == nullptr || headers_len == 0) return false;
  std::size_t i = 0;
  while (i < headers_len) {
    /* satir sonu bul (\n) */
    std::size_t eol = i;
    while (eol < headers_len && headers[eol] != '\n') ++eol;
    std::size_t line_end = eol;
    if (line_end > i && headers[line_end - 1] == '\r') --line_end;   /* \r\n */
    /* key eslesme (case-insensitive, ':' oncesi, basindaki bosluklar sayilmaz) */
    std::size_t s = i;
    while (s < line_end && (headers[s] == ' ' || headers[s] == '\t')) ++s;
    if (s + key_len <= line_end) {
      bool match = true;
      for (std::size_t k = 0; k < key_len; ++k) {
        char a = headers[s + k];
        char b = key[k];
        if (a >= 'A' && a <= 'Z') a = static_cast<char>(a - 'A' + 'a');
        if (b >= 'A' && b <= 'Z') b = static_cast<char>(b - 'A' + 'a');
        if (a != b) { match = false; break; }
      }
      if (match && s + key_len < line_end && headers[s + key_len] == ':') {
        std::size_t v = s + key_len + 1;
        while (v < line_end && (headers[v] == ' ' || headers[v] == '\t')) ++v;
        sanitize_metadata(headers + v, line_end - v, out, out_cap);
        return true;
      }
    }
    i = (eol < headers_len) ? eol + 1 : headers_len;   /* \n atl */
  }
  return false;
}

/* --- icy-metaint header okuma (kural 8) ---------------------------------------- */
/* "icy-metaint: N" → N; bulunamazsa false. Validate edilmis (kural 8) donus. */
inline bool parse_metaint(const char* headers, std::size_t headers_len,
                          std::uint32_t& out_metaint) noexcept {
  char buf[32];
  if (!header_value(headers, headers_len, "icy-metaint", 11, buf, sizeof(buf))) return false;
  std::uint32_t v = 0;
  if (buf[0] == '\0') return false;
  for (const char* p = buf; *p; ++p) {
    if (*p < '0' || *p > '9') return false;              /* rakam disi → bozuk header */
    if (v > (0xFFFFFFFFu - static_cast<std::uint32_t>(*p - '0')) / 10u) return false; /* tasirma */
    v = v * 10u + static_cast<std::uint32_t>(*p - '0');
  }
  if (!validate_metaint(v)) return false;                /* kural 8: aralik disi → RED */
  out_metaint = v;
  return true;
}

} // namespace neva::radio
#endif /* NEVA_ICY_PARSER_H */
