---
node_id: K008-domain/web-arastirma
node_type: root
status: verified
parent_id: K008-domain
children: []
depends_on:
  - K008-domain/domain-master.md
  - K008-domain/domain-kapsam.md
evidence:
  - martinfowler.com/bliki/DomainDrivenDesign.html
  - domainlanguage.com/ddd/
  - musicbrainz.org/doc/Recording
  - php-fig.org/psr/psr-14/
validation: 2026-10-12 — tüm satırlar HTTP 200 ile doğrulandı (keşif turu); aynı URL bu dosya dışında tekil dağıtıldı
open_questions:
  - "Doğrulama 2026-10-12'ye aittir; URL'lerin içeriği 6 ayda değişebilir → yeniden doğrulama döngüsü orkestratöre bağlı"
group: kanit
---

# K008-domain — Web Araştırması (Kök)

> **Authority: Bayram Ali / Vault Steward.** K008-domain kök iddialarının web
> kaynakları. **Dağıtım kuralı (C4):** her URL **bir kez** kullanılır; çocuklar
> kendi web-arastirma dosyalarında farklı URL'ler kullanır (§7 dağıtım cetveli).
> Doğrulanamayan iddia → `⚠️ VERIFICATION REQUIRED`.

## §1 ARAŞTIRMA METODOLOJİSİ

| Adım | Uygulama |
|---|---|
| 1 | İddia listesi master/kararlar/kapsam dosyalarından çıkarıldı |
| 2 | 2026-10-12'de ~80 URL webfetch/HEAD ile sorgulandı |
| 3 | HTTP 200 = `Doğrulandı`; 404/401 = listeden çıkarıldı |
| 4 | Her satır: iddia · URL · tarih · durum |
| 5 | Tarih biçimi: `2026-10-12` (RFC 3339 günü) |

## §2 DDD VE BOUNDED CONTEXT KAYNAKLARI

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | DDD = yazılım tasarımı yaklaşımı; model odaklı | `https://martinfowler.com/bliki/DomainDrivenDesign.html` | 2026-10-12 | Doğrulandı |
| 2 | Ubiquitous Language, DDD'nin temel direği | `https://domainlanguage.com/ddd/` | 2026-10-12 | Doğrulandı |
| 3 | Bounded Context, her BC'nin kendi dili/sınırı | `https://en.wikipedia.org/wiki/Domain-driven_design` | 2026-10-12 | Doğrulandı |
| 4 | Bounded Context pratik tanımı | `https://www.deviq.com/domain-driven-design/bounded-context/` | 2026-10-12 | Doğrulandı |
| 5 | Bounded Context'i kod içindeki uygulaması | `https://martinfowler.com/bliki/BoundedContext.html` | 2026-10-12 | Doğrulandı |

## §3 OLAY MİMARİSİ (EVENT) KAYNAKLARI

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 6 | Event sourcing = olayları değişmez kayıt olarak saklama | `https://martinfowler.com/eaaDev/EventSourcing.html` | 2026-10-12 | Doğrulandı |
| 7 | Event-driven architecture: olay yayımı/abonelik | `https://en.wikipedia.org/wiki/Event-driven_architecture` | 2026-10-12 | Doğrulandı |
| 8 | PSR-14: Event Dispatcher arayüzü (dispatch sözleşmesi) | `https://www.php-fig.org/psr/psr-14/` | 2026-10-12 | Doğrulandı |
| 9 | PSR-12: Extended Coding Style (strict_types deseni) | `https://www.php-fig.org/psr/psr-12/` | 2026-10-12 | Doğrulandı |
| 10 | PSR-4: Otomatik yükleme (namespace → dizin) | `https://www.php-fig.org/psr/psr-4/` | 2026-10-12 | Doğrulandı |
| 11 | PSR-3: Log kaydı arayüzü | `https://www.php-fig.org/psr/psr-3/` | 2026-10-12 | Doğrulandı |

## §4 MÜZİK METAVERİSİ (MusicBrainz) KAYNAKLARI

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 12 | Recording = ses kaydı (Work'ten bağımsız) | `https://musicbrainz.org/doc/Recording` | 2026-10-12 | Doğrulandı |
| 13 | Work = soyut eser (söz/müzik) | `https://musicbrainz.org/doc/Work` | 2026-10-12 | Doğrulandı |
| 14 | Release = yayımlanmış sürüm | `https://musicbrainz.org/doc/Release` | 2026-10-12 | Doğrulandı |
| 15 | MusicBrainz veritabanı şeması | `https://musicbrainz.org/doc/MusicBrainz_Database/Schema` | 2026-10-12 | Doğrulandı |
| 16 | MusicBrainz API kimlik/kullanım | `https://musicbrainz.org/doc/MusicBrainz_API` | 2026-10-12 | Doğrulandı |
| 17 | MusicBrainz stil/etiket verisi | `https://musicbrainz.org/doc/Style` | 2026-10-12 | Doğrulandı |
| 18 | Picard (CLI/desktop etiketleyici) | `https://picard.musicbrainz.org/` | 2026-10-12 | Doğrulandı |
| 19 | AcoustID = akustik parmak izi kimliği | `https://acoustid.org/` | 2026-10-12 | Doğrulandı |

## §5 SES BİÇİMLERİ VE METAVERİ ALTYAPISI

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 20 | ID3v2.4 çerçeve yapısı (MP3 etiket) | `https://id3.org/id3v2.4.0-structure` | 2026-10-12 | Doğrulandı |
| 21 | ID3v2.4 çerçeve listesi | `https://id3.org/id3v2.4.0-frames` | 2026-10-12 | Doğrulandı |
| 22 | FLAC format spesifikasyonu | `https://xiph.org/flac/format.html` | 2026-10-12 | Doğrulandı |
| 23 | FLAC (RFC 9639) | `https://www.rfc-editor.org/info/rfc9639/` | 2026-10-12 | Doğrulandı |
| 24 | Ogg container (RFC 3533) | `https://www.ietf.org/rfc/rfc3533.txt` | 2026-10-12 | Doğrulandı |
| 25 | Opus ses codec (RFC 7845) | `https://www.rfc-editor.org/rfc/rfc7845.txt` | 2026-10-12 | Doğrulandı |
| 26 | Opus codec (RFC 6716) | `https://datatracker.ietf.org/doc/html/rfc6716` | 2026-10-12 | Doğrulandı |
| 27 | ISO Base Media File Format (MP4/M4A) | `https://en.wikipedia.org/wiki/ISO_base_media_file_format` | 2026-10-12 | Doğrulandı |
| 28 | HTML Media element spec (audio/video) | `https://html.spec.whatwg.org/multipage/media.html` | 2026-10-12 | Doğrulandı |
| 29 | IANA media types (application/octet-stream vb.) | `https://www.iana.org/assignments/media-types/media-types.xhtml` | 2026-10-12 | Doğrulandı |

## §6 KİMLİK, ZAMAN VE KODLAMA STANDARTLARI

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 30 | RFC 3339 tarih/saat biçimi (ISO 8601 profil) | `https://www.rfc-editor.org/rfc/rfc3339.txt` | 2026-10-12 | Doğrulandı |
| 31 | RFC 3629 UTF-8 | `https://www.rfc-editor.org/rfc/rfc3629.txt` | 2026-10-12 | Doğrulandı |
| 32 | RFC 2046 multipart MIME | `https://www.rfc-editor.org/rfc/rfc2046.txt` | 2026-10-12 | Doğrulandı |
| 33 | RFC 9562 UUID (v7 dahil) | `https://datatracker.ietf.org/doc/html/rfc9562` | 2026-10-12 | Doğrulandı |
| 34 | Universally unique identifier (UUID kavramı) | `https://en.wikipedia.org/wiki/Universally_unique_identifier` | 2026-10-12 | Doğrulandı |
| 35 | PHP enum (BackedEnum/UnitEnum) | `https://www.php.net/manual/en/language.types.enumerations.php` | 2026-10-12 | Doğrulandı |
| 36 | PHP PDO (veritabanı erişimi) | `https://www.php.net/manual/en/book.pdo.php` | 2026-10-12 | Doğrulandı |

## §7 GÜVENLİK VE KİMLİK (domain sınırı: yalnız gereksinim)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 37 | OpenID Connect Core 1.0 (kimlik doğrulama) | `https://openid.net/specs/openid-connect-core-1_0.html` | 2026-10-12 | Doğrulandı |
| 38 | RFC 6749 OAuth 2.0 | `https://datatracker.ietf.org/doc/html/rfc6749` | 2026-10-12 | Doğrulandı |
| 39 | OWASP Password Storage Cheat Sheet | `https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html` | 2026-10-12 | Doğrulandı |
| 40 | OWASP Session Management Cheat Sheet | `https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html` | 2026-10-12 | Doğrulandı |
| 41 | Argon2id (RFC 9106) — parola türetme | `https://datatracker.ietf.org/doc/html/rfc9106` | 2026-10-12 | Doğrulandı |

## §8 API ŞEMASI (domain dışı — yalnız sınır referansı)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 42 | OpenAPI Specification (en son) | `https://spec.openapis.org/oas/latest.html` | 2026-10-12 | Doğrulandı |
| 43 | JSON Schema (veri doğrulama) | `https://json-schema.org/` | 2026-10-12 | Doğrulandı |
| 44 | JSON.org (format tanımı) | `https://www.json.org/` | 2026-10-12 | Doğrulandı |

## §9 REFERANS UYGULAMALAR (karşılaştırma kaynakları)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 45 | Koel — MIT, Laravel tabanlı müzik akış sunucusu | `https://github.com/koel/koel` | 2026-10-12 | Doğrulandı |
| 46 | Ampache — AGPL, PHP müzik sunucusu | `https://github.com/ampache/ampache` | 2026-10-12 | Doğrulandı |
| 47 | Navidrome — Go, Subsonic API | `https://github.com/navidrome/navidrome` | 2026-10-12 | Doğrulandı |
| 48 | Navidrome Subsonic API notları | `https://www.navidrome.org/docs/developers/subsonic-api/` | 2026-10-12 | Doğrulandı |
| 49 | Beets — Python katalog/etiketleyici | `https://github.com/beetbox/beets` | 2026-10-12 | Doğrulandı |
| 50 | Mutagen — Python metadata kütüphanesi | `https://github.com/quodlibet/mutagen` | 2026-10-12 | Doğrulandı |
| 51 | TagLib — C++ metadata kütüphanesi | `https://taglib.org/` | 2026-10-12 | Doğrulandı |
| 52 | XSPF playlist formatı | `https://www.xspf.org/` | 2026-10-12 | Doğrulandı |

## §10 MÜZİK DAVRANIŞI VE OYNATMA KAVRAMLARI

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 53 | M3U playlist biçimi | `https://en.wikipedia.org/wiki/M3U` | 2026-10-12 | Doğrulandı |
| 54 | Playlist (müzik listesi kavramı) | `https://en.wikipedia.org/wiki/Playlist` | 2026-10-12 | Doğrulandı |
| 55 | Shuffle (rastgele sıralama) | `https://en.wikipedia.org/wiki/Shuffle_(music)` | 2026-10-12 | Doğrulandı |
| 56 | Gapless playback (boşluksuz oynatma) | `https://en.wikipedia.org/wiki/Gapless_playback` | 2026-10-12 | Doğrulandı |
| 57 | Crossfading (parça geçişi solması) | `https://en.wikipedia.org/wiki/Crossfading_(audio)` | 2026-10-12 | Doğrulandı |
| 58 | ReplayGain (ses seviyesi eşitleme) | `https://en.wikipedia.org/wiki/ReplayGain` | 2026-10-12 | Doğrulandı |
| 59 | Compilation (derleme albüm) | `https://en.wikipedia.org/wiki/Compilation` | 2026-10-12 | Doğrulandı |
| 60 | Acoustic fingerprint (akış içeriği kimliği) | `https://en.wikipedia.org/wiki/Acoustic_fingerprint` | 2026-10-12 | Doğrulandı |
| 61 | Beat detection (ritim tespiti) | `https://en.wikipedia.org/wiki/Beat_detection` | 2026-10-12 | Doğrulandı |
| 62 | Chroma feature (harmonik/tonal özellik) | `https://en.wikipedia.org/wiki/Chroma_feature` | 2026-10-12 | Doğrulandı |
| 63 | Mood (müzik duygusu) | `https://en.wikipedia.org/wiki/Mood_(music)` | 2026-10-12 | Doğrulandı |

## §11 SES İŞLEMESİ TERİMLERİ (domain dili doğrulaması)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 64 | Sample rate (örnekleme oranı) | `https://en.wikipedia.org/wiki/Sample_rate` | 2026-10-12 | Doğrulandı |
| 65 | Bit rate (bit oranı) | `https://en.wikipedia.org/wiki/Bit_rate` | 2026-10-12 | Doğrulandı |
| 66 | Audio bit depth (bit derinliği) | `https://en.wikipedia.org/wiki/Audio_bit_depth` | 2026-10-12 | Doğrulandı |
| 67 | Pulse-code modulation (PCM) | `https://en.wikipedia.org/wiki/Pulse-code_modulation` | 2026-10-12 | Doğrulandı |
| 68 | Dithering (bit derinliği düşürme tekniği) | `https://en.wikipedia.org/wiki/Dithering` | 2026-10-12 | Doğrulandı |

## §12 API REFERANSLARI (harici müzik API'leri — sınır karşılaştırması)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 69 | Spotify Web API (reference) | `https://developer.spotify.com/documentation/web-api` | 2026-10-12 | Doğrulandı |
| 70 | Last.fm API | `https://www.last.fm/api` | 2026-10-12 | Doğrulandı |
| 71 | Apple MusicKit (Apple Music API) | `https://developer.apple.com/documentation/musickit` | 2026-10-12 | Doğrulandı |

> **C4 düzeltmesi (2026-10-15):** Eski #72 satırı (#48 `https://www.navidrome.org/docs/developers/subsonic-api/`
> ile **aynı URL**) kaldırıldı — kök havuzda tekillik (76 kayıt → **75 benzersiz URL**;
> `k008-url-audit2.ps1` sonucu: kök-çiçocuk kesişimi 0).

## §13 TEST/STATİK ARAÇLAR

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 73 | PHPUnit 11.5 dokümantasyonu | `https://docs.phpunit.de/en/11.5/` | 2026-10-12 | Doğrulandı |
| 74 | PHPStan (statik analiz) | `https://phpstan.org/` | 2026-10-12 | Doğrulandı |
| 75 | Infection (mutation testing) | `https://infection.github.io/` | 2026-10-12 | Doğrulandı |

## §14 NORMALİZASYON (data katmanı referansı)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 76 | Boyce-Codd normal formu (BCNF) | `https://en.wikipedia.org/wiki/Boyce%E2%80%93Codd_normal_form` | 2026-10-12 | Doğrulandı |

## §15 KULLANILMAYAN/ERİŞİLMEYEN URL'LER (kayıt)

Aşağıdakiler keşifte 404/erişilemez sonuç verdi ve **kullanılmadı**:

| URL | Neden |
|---|---|
| `en.wikipedia.org/wiki/Ubiquitous_language` | 404 — DDD sayfası içinde karşılandı |
| `php.net/.../language.oop5.readonly.php` | 404 — readonly properties başka sayfada |
| `xspf.org/xspf-v1.html` | 404 — xspf.org kökü kullanılabilir (§9-52) |
| `opensubsonic.net` | 404 — Subsonic notları Navidrome'da (§9-48) |
| `dev.mysql.com/refman/9.0` | 404/erişilemez — MySQL detayı kapsam dışı |
| `w3.org/TR/json-schema-validation` | 404 — json-schema.org (§8-43) kullanılabilir |

> **2026-10-15 doğrulaması:** Altı ölü URL'nin hiçbiri çocuk havuzunda
> (89 satır `k008-child-urls-ok.txt`) yer almıyor — bu kayıt günceldir, silinmez.

## §16 DAĞITIM CETVELİ (C4 kuralı — hangi URL hangi dosyada)

| Dosya | Ayrılan URL (2026-10-15 sayımı) |
|---|---|
| `domain-web-arastirma.md` (bu dosya) | 75 URL — §2–§15 (root havuz) |
| çocuk `music-web-arastirma.md` | 8 (W-1…W-8 — §1/§2'ye bak) |
| çocuk `media-web-arastirma.md` | 11 (M-1…M-11 — §1/§2'ye bak) |
| çocuk `user-web-arastirma.md` | 8 (U-1…U-8 — §1/§2'ye bak) |
| çocuk `library-web-arastirma.md` | 8 (L-1…L-8 — §1/§2'ye bak) |
| çocuk `playlist-web-arastirma.md` | 9 — havuz satır: 11, 13, 14, 83, 42, 52, 60, 87, 89 |
| çocuk `playback-web-arastirma.md` | 10 — havuz satır: 15, 16, 17, 26, 27, 28, 29, 45, 57, 84 |
| çocuk `discovery-web-arastirma.md` | 9 — havuz satır: 19, 20, 35, 37, 44, 47, 48, 61, 68 |
| çocuk `recommendation-web-arastirma.md` | 9 — havuz satır: 18, 25, 30, 31, 46, 49, 53, 65, 81 |
| çocuk `device-management-web-arastirma.md` | 9 — havuz satır: 24, 34, 58, 64, 69, 70, 71, 72, 78 |
| çocuk `context-mapping-web-arastirma.md` | 8 — havuz satır: 1, 9, 10, 56, 66, 74, 75, 76 |

> **Not (2026-10-15):** Çocuk havuzu toplamı **89 URL / 10 dosya — 0 çakışma**
> (`k008-url-audit2.ps1` · `k008-c4-scan.ps1`: `POOL-COLLISION: 0`).
> Havuz satırları `k008-child-urls-ok.txt` içindir. Root (75) + çocuk (89)
> = 164 kayıt; aynı URL iki dosyada **görünemez** (C4 — `domain-dogrulama.md` §6).

## §17 YENİDEN DOĞRULAMA DÖNGÜSÜ

| Tetik | Aksiyon |
|---|---|
| 6 ay doldu (2027-04-12) | §2–§15 yeniden HEAD/GET ile taranır; 404 olanlar §15'e taşınır |
| Yeni iddia eklendi | Yeni satır + URL; önce tekil kontrol (bu dosyada var mı?) |
| Çocuk web dosyası eklendi | §16 cetveli güncellenir |

---

*Authority: Bayram Ali / Vault Steward · K008-domain web araştırması · 2026-10-12 · 75 benzersiz URL · HTTP 200 (C4 düzeltmesi 2026-10-15: #72 tekrarı kaldırıldı)*
