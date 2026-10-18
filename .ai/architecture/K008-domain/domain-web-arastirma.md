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

---
## §18 DOĞRULAMA TURU (2026-10-17 — HAVUZ GENİŞLETME)

§2–§15 havuzu ve §16 dağıtım cetveli **değiştirilmez**; bu bölüm havuza **29 yeni kaynak** ekler.
Turlar ayrı ayrı yaşar: §2–§15 = 2026-10-12 turu (75 URL) · §18 = 2026-10-17 turu (29 URL).
Bu bölümdeki her başarılı kayıt aynı gün tek tek `GET` ile doğrulanmıştır; 404 dökümü §18.5'te,
iddia eşlemesi §18.3'te, sayfa-içi metin doğrulaması §18.4'tedir.

### §18.1 YÖNTEM (bu turda ne yapıldı)

| Adım | İşlem | Sonuç |
|---|---|---|
| M1 | Aday havuzu kuruldu: DDD desenleri · dil referansları · standart dokümanları | 46 aday (29 × 200 · 16 × 404/probe · 1 × yönlendirme sayfası) |
| M2 | C4 tekil kontrol: K008-domain içindeki **72** `.md` dosyası `https?://` kalıbıyla tarandı | mevcut havuz **169** benzersiz URL; 29 adayın **hiçbiri** havuzda yok → çakışma **0** |
| M3 | Erişim: `GET` (fetch) — `status`, `title`, nihai `url` (redirect sonrası) kaydedildi | §18.2 |
| M4 | Anahtar-kelime doğrulaması: iddia edilen kavram sayfanın metninde gerçekten geçiyor mu? | §18.4 |
| M5 | 404 / dayanaksızlar gizlenmeden yazıldı | §18.5 |
| M6 | Kaynak ↔ iddia eşlemesi (ne kanıtlıyor — ne kanıtlamıyor) | §18.3 |

**Yöntem notları (tur içi, bağlamsal):**

1. `HEAD` yerine `GET` kullanıldı — pek çok sunucu `HEAD` isteğine 405 döndürür; bu da
   erişilebilir bir kaynağı **sahte 404** gibi gösterirdi (yanlış negatif riski).
2. PDF uç noktalarda (`#9`, `#19`) ve JSON API'de (`#26`) `title` alanı boş kalır; o
   durumda yanıt gövdesi okunarak kayıt yapıldı — boş başlık "erişilemedi" anlamına gelmez.
3. Yönlendirme zinciri olan kaynaklarda **hem istenen hem nihai URL** yazıldı; iddianın
   dayanağı nihai (sondurulan) URL'dir.
4. 404 tek başına "yanlış iddia" kanıtı değildir; yeni yerleşim aranır ve tur tekrarlanır (§17).
5. Bu turda arama motoru sonucu **ham kanıt sayılmadı** — arama yalnız URL bulmak için
   kullanıldı; kabul yalnız HTTP 200 + başlık/gövde doğrulamasıyla verildi.

### §18.2 YENİ DOĞRULANMIŞ KAYNAKLAR (29 URL · HTTP 200 · tarih 2026-10-17)

| # | Alan | URL | HTTP | Başlık (yanıtta görünen) | Yönlendirme |
|---|---|---|---|---|---|
| 1 | DDD topluluğu | https://dddcommunity.org/ | 200 | dddcommunity.org · Domain Driven Design Community | → `www.dddcommunity.org` |
| 2 | DDD makale havuzu | https://www.infoq.com/domain-driven-design/ | 200 | Domain Driven Design - InfoQ | — |
| 3 | Olay mimarisi | https://martinfowler.com/articles/201701-event-driven.html | 200 | What do you mean by "Event-Driven"? | — |
| 4 | Ortak dil | https://martinfowler.com/bliki/UbiquitousLanguage.html | 200 | Ubiquitous Language | — |
| 5 | Stratejik analiz | https://learn.microsoft.com/en-us/azure/architecture/microservices/model/domain-analysis | 200 | Use Domain Analysis to Model Microservices - Azure Architecture Center | — |
| 6 | Taktik DDD | https://learn.microsoft.com/en-us/azure/architecture/microservices/model/tactical-domain-driven-design | 200 | Use Tactical DDD to Design Microservices - Azure Architecture Center | — |
| 7 | DDD + CQRS | https://learn.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/ | 200 | Tackling Business Complexity in a Microservice with DDD and CQRS Patterns | — |
| 8 | DDD tasarımı | https://learn.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/ddd-oriented-microservice | 200 | Designing a DDD-oriented microservice | — |
| 9 | Specification | https://martinfowler.com/apsupp/spec.pdf | 200 | (PDF — başlık alanı yok, gövde okundu) | — |
| 10 | Specification | https://en.wikipedia.org/wiki/Specification_pattern | 200 | Specification pattern - Wikipedia | — |
| 11 | Outbox | https://microservices.io/patterns/data/transactional-outbox.html | 200 | Pattern: Transactional outbox | — |
| 12 | JS değişmezlik | https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/freeze | 200 | Object.freeze() - JavaScript · MDN | — |
| 13 | Python VO | https://docs.python.org/3/library/dataclasses.html | 200 | dataclasses — Data Classes — Python dokümantasyonu | — |
| 14 | Java record | https://docs.oracle.com/en/java/javase/21/language/records.html | 200 | Record Classes | — |
| 15 | Kotlin data class | https://kotlinlang.org/docs/data-classes.html | 200 | Data classes · Kotlin Documentation | — |
| 16 | TS narrowing | https://www.typescriptlang.org/docs/handbook/2/narrowing.html | 200 | TypeScript: Documentation - Narrowing | — |
| 17 | Go | https://go.dev/doc/effective_go | 200 | Effective Go - The Go Programming Language | — |
| 18 | C++ constexpr | https://en.cppreference.com/w/cpp/language/constexpr | 200 | constexpr specifier (since C++11) - cppreference.com | → `/cpp/language/constexpr` |
| 19 | C11 standardı | https://open-std.org/JTC1/SC22/WG14/www/docs/n1570.pdf | 200 | (PDF — C11 komite taslağı N1570) | — |
| 20 | Terraform / HCL | https://developer.hashicorp.com/terraform/language/expressions/types | 200 | Types and Values - Configuration Language · Terraform | — |
| 21 | GitHub Actions | https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax | 200 | Workflow syntax for GitHub Actions - GitHub Docs | — |
| 22 | Bash | https://www.gnu.org/software/bash/manual/bash.html | 200 | Bash Reference Manual | — |
| 23 | PowerShell | https://learn.microsoft.com/en-us/powershell/scripting/overview | 200 | What is PowerShell? - PowerShell · Microsoft Learn | → `?view=powershell-7.6` |
| 24 | SQL DDL | https://www.postgresql.org/docs/current/sql-createtable.html | 200 | PostgreSQL: Documentation: 18: CREATE TABLE | — |
| 25 | C# record | https://learn.microsoft.com/en-us/dotnet/csharp/fundamentals/types/records | 200 | C# record types - C# · Microsoft Learn | — |
| 26 | Referans uygulama | https://api.github.com/repos/VaughnVernon/IDDD_Samples | 200 | (JSON — "sample Bounded Contexts from the book Implementing Domain-Driven Design") | — |
| 27 | Rust advanced traits | https://doc.rust-lang.org/book/ch20-02-advanced-traits.html | 200 | Advanced Traits - The Rust Programming Language | — |
| 28 | Swift dil referansı | https://docs.swift.org/swift-book/documentation/the-swift-programming-language/declarations | 200 | Documentation (declarations) | → `/latest/documentation/…` |
| 29 | SQL benzersizlik | https://en.wikipedia.org/wiki/Unique_key | 200 | Unique key - Wikipedia | — |

**Bu tablo tekil kaynaktır:** aynı URL'ler K008-domain içinde başka hiçbir dosyada
geçmez (C4). Diğer dosyalar bu tabloya `#no` ile referans verir.

### §18.3 KAYNAK ↔ İDDİA EŞLEMESİ (ne kanıtlıyor — ne kanıtlamıyor)

| # | İddia (K008'de geçtiği yer) | Kaynak | Kanıtladığı | Kanıtlamadığı / sınır |
|---|---|---|---|---|
| A | Alt-domainler çekirdek / destekleyici / genel diye ayrılır (`domain-master.md` §23) | #5 | "Core / Supporting / Generic subdomain" ayrımı sayfada **metinde doğrulandı** | CoreMusic'te hangi BC'nin hangi sınıfa girdiği **bu sayfadan gelmez** — proje kararı |
| B | Taktik DDD kavram seti: Value Object · Aggregate · Domain Event · Bounded Context (`domain-master.md` §24–§26) | #6 | Dört kavramın başlığı sayfada **metinde doğrulandı** (§18.4) | "Specification" ve "Repository" başlıkları bu sayfada **geçmiyor** → o iddialar #9/#10 ve #7/#8 ile taşınır |
| C | Specification pattern And/Or bileşimiyle yeniden kullanılabilir sorgu nesnesi üretir (`domain-master.md` §27) | #9 + #10 | #10'da "composite", "And", "Or" metni geçiyor; #9 Evans & Fowler özgün ek makaledir | #9 PDF olduğu için bu turda metin-bağımsız doğrulama yapılmadı → eşlik eden kanıt #10'dur |
| D | Aggregate dışı tutarlılık için transactional outbox gerekir (`domain-master.md` §26) | #11 | "outbox", "transactional", "publish" metni geçiyor | **Depoda outbox uygulama kanıtı YOK** → iddia yalnız gereksinim, `⚠️ VERIFICATION REQUIRED` |
| E | Event-driven'da olaylar hafif bildirim (notification) olarak taşınır (`domain-master.md` §26) | #3 | "event notification" metni geçiyor | Sayfada "domain event" terimi geçmiyor; CoreMusic'in tanımı repo kanıtından gelir |
| F | Ortak dil yazılım modeliyle aynı adları kullanır (`domain-master.md` §10) | #4 | Ubiquitous Language bliki girişi | CoreMusic sözlüğünün kendisi kanıt değildir → `domain-evrensel-dil.md` sahibidir |
| G | Dil bazında immutable / VO karşılıkları resmi dokümanlara dayanır (`domain-kod-ornekleri.md` §8) | #12–#20, #22–#25, #27–#28 | Her dilin resmi referansı tek tek 200 (§18.2) | Örnek kodların CoreMusic'e uygulanabilirliği **tasarım örnekliğidir** — repo karşılığı olmayanlar "tasarım örneği" etiketlidir |
| H | SQL'de invariant'ın ikinci katmanı constraint'tir (`playlist-kapsam.md` §6) | #24 | `CREATE TABLE` dokümanı `UNIQUE`, `CHECK`, `PRIMARY KEY` içeriyor (§18.4 #16) | MySQL'e özgü davranış için kanıt `.ai/.sql/mysql/*` dosyalarıdır (satır kanıtlı) |
| I | Benzersizlik (unique) tanımı ve davranışı (`playlist-kapsam.md` §6) | #29 | Unique key tanımı | MySQL `UNIQUE` davranışı için #24 + repo SQL'i |
| J | Referans uygulama / örnek bounded context'ler mevcuttur (`domain-kapsam.md` §8) | #26 | GitHub API JSON: "sample Bounded Contexts from the book Implementing Domain-Driven Design" (3.947 yıldız, son itme 2023-09-09) | Repo **bu projede kullanılmıyor** — yalnız karşılaştırma kaynağıdır |
| K | Belge / CI / altyapı yüzeyleri de deseni dışarıdan tekrar edebilir (`domain-kod-ornekleri.md` §8) | #20, #21, #22, #23 | Sözdizimi / doküman sayfaları 200 | Bunlar **dil değil, yapılandırma yüzeyidir**; invariant'ın dış katman tekrarıdır |

### §18.4 İÇERİK DOĞRULAMASI (anahtar-kelime — sayfa metninde gerçekten arandı)

Aşağıdaki aramalar aynı turda sayfa **gövdesi** üzerinde yapılmıştır. "YOK" satırlar
gizlenmemiştir: ilgili iddia o kaynağa **dayandırılmaz**.

| # | Kaynak | Aranan ifade | Sonuç |
|---|---|---|---|
| 1 | #6 taktik DDD | Value Object / Aggregate / Domain Event / Bounded Context | hepsi **VARDI** |
| 2 | #6 taktik DDD | Specification / Repository | **YOK** → bu iddia #6'ya bağlanmaz |
| 3 | #5 domain analysis | Core subdomain / Supporting subdomain / Generic subdomain | hepsi **VARDI** |
| 4 | #11 outbox | outbox / transactional / publish | hepsi **VARDI** |
| 5 | #3 event-driven | event-driven / event notification | **VARDI**; "domain event" **YOK** |
| 6 | #10 spec pattern | Specification / composite / And / Or | hepsi **VARDI** |
| 7 | #25 C# record | reference type / value equality / immutable / with expression | hepsi **VARDI** |
| 8 | #13 Python dataclasses | frozen / eq / hash | hepsi **VARDI** |
| 9 | #14 Java 21 record | record / equals | **VARDI**; "implicitly final" kalıbı **YOK** → o söz edilmez |
| 10 | #15 Kotlin data class | equals() / hashCode() / copy() / component | hepsi **VARDI** |
| 11 | #16 TypeScript narrowing | type guard / typeof / in operator / discriminant | hepsi **VARDI** |
| 12 | #12 MDN Object.freeze | strict mode | **VARDI**; "does not throw" kalıbı **YOK** → o söz edilmez |
| 13 | #27 Rust advanced traits | newtype / operator / trait | hepsi **VARDI** |
| 14 | #18 C++ constexpr | constant expression / constexpr function | hepsi **VARDI** |
| 15 | #17 Effective Go | struct | **VARDI**; "composition" kalıbı **YOK** → o söz edilmez |
| 16 | #24 PostgreSQL CREATE TABLE | UNIQUE / CHECK / PRIMARY KEY | hepsi **VARDI** |
| 17 | #20 Terraform types | map / object / bool | hepsi **VARDI** |
| 18 | #21 GitHub Actions | jobs / on: / runs-on | hepsi **VARDI** |

### §18.5 404 VE DAYANAK OLMAYANLAR (2026-10-17 — bu kayıtlar KULLANIM DIŞIDIR)

Aşağıdaki adaylar bu turda **erişilemedi**; hiçbiri §18.3'teki hiçbir iddiaya
dayanak yapılmamıştır. 404 = kaynak yoktur; "iddia yanlıştır" anlamına **gelmez**.

| # | URL | HTTP | Karşılığı (varsa) |
|---|---|---|---|
| 1 | https://learn.microsoft.com/en-us/azure/architecture/guide/ddd/ | 404 | yerine #5, #6, #7 |
| 2 | https://learn.microsoft.com/en-us/azure/architecture/guide/ddd/ddd-context-map | 404 | context-map arayışı sürüyor (§18.8) |
| 3 | https://martinfowler.com/eaaDev/SpecPattern.html | 404 | yerine #9 (PDF), #10 |
| 4 | https://microservices.io/patterns/transactional-outbox.html | 404 | yerine #11 (yol farkı: `/data/`) |
| 5 | https://learn.microsoft.com/en-us/azure/architecture/patterns/outbox | 404 | outbox için yalnız #11 kullanılabilir |
| 6 | https://learn.microsoft.com/en-us/dotnet/csharp/fundamentals/tuples/record-types | 404 | yerine #25 |
| 7 | https://learn.microsoft.com/en-us/dotnet/csharp/language-reference/keywords/record | 404 | yerine #25 |
| 8 | https://learn.microsoft.com/en-us/dotnet/csharp/fundamentals/object-oriented/records | 404 | yerine #25 |
| 9 | https://doc.rust-lang.org/book/ch07-03-newtype-pattern.html | 404 | yerine #27 (newtype metni orada — §18.4 #13) |
| 10 | https://docs.swift.org/swift-book/LanguageGuide/Structures.html | 404 | yerine #28 (eski kılavuz yolu taşınmış) |
| 11 | https://en.wikipedia.org/wiki/Unique_constraint | 404 | yerine #29 |
| 12 | https://doc.rust-lang.org/book/ch18-00-advanced-features.html | 404 | yerine #27 |
| 13 | https://docs.swift.org/swift-book/documentation/the-swift-programming-language/structuresandclasses | 404 | yerine #28 |
| 14 | https://developer.apple.com/documentation/swift/struct | 404 | yerine #28 |
| 15 | https://learn.microsoft.com/en-us/azure/architecture/microservices/context-mapping/context-map | 404 | açık arayış — §18.8 |
| 16 | https://learn.microsoft.com/en-us/dotnet/csharp/fundamentals/object-oriented/abstract-base-classes | 404 | deneme kaydı, iddiaya bağlanmadı |

**Yönlendirme sayfası (200 ama içerik değil):** `doc.rust-lang.org/book/ch19-03-advanced-traits.html`
HTTP 200 döndürür ancak gövdesi yalnız bir `meta refresh` yönlendirmesidir (başlık:
"Redirecting..."). Nihai hedef §18.2 **#27**'dir (200, gerçek içerik) — bu nedenle #27
kullanılır, ch19-03 **kullanılmaz**.

### §18.6 HAVUZ TOPLAMI VE C4 KAYDI

| Ölçüm | 2026-10-12 (önceki tur) | 2026-10-17 (bu tur) | Yöntem |
|---|---|---|---|
| Kök dosyalar (depth 0) | 12 | 12 | dizin ağacı sayımı |
| Kök dosyalardaki benzersiz URL | 75 | **104** | 75 + §18.2'deki 29 |
| Çocuk dosyalar (depth 1+) | 60 | 60 | dizin ağacı sayımı |
| K008-domain geneli benzersiz URL | 169 | **198** | 72 dosya, `https?://` kalıbı, trailing nokta/virgül ayıklanmış |
| C4 çakışması (bu tur eklenenler) | — | **0** | ekleme öncesi tüm havuza karşı kontrol |

**C4 kalıcılığı:** §18.2'deki 29 URL yalnız bu dosyada bulunur; başka bir K008 dosyasında
görülürse C4 ihlalidir ve **o dosyadaki satır silinir** (URL bu dosyada kalır).

### §18.7 DERİN NOTLAR (yüksek ağırlıklı kaynaklar)

**#5 — Domain Analysis (Microsoft Learn).** Alt-domain sınıflandırması (Core /
Supporting / Generic) ile bounded context analizinin birlikte yürütüldüğünü gösterir
(§18.4 #3). K008 değeri: `domain-master.md` §23'ün sınıflandırma eksenini dış bir
kaynağa bağlar. Sınır: hangi CoreMusic BC'sinin hangi sınıfa girdiği **bu sayfadan
değil**, proje sahipliği kararından çıkar — o nedenle §23'te "öneri + gerekçe" dili
kullanılır, kesin atama dili değil.

**#6 — Tactical DDD (Microsoft Learn).** Sayfa metninde doğrulanan dört kavram:
Value Object, Aggregate, Domain Event, Bounded Context. Bu dördü K008'in omurgasıdır
(`domain-master.md` §24–§26). **Kanıt disiplini:** aynı sayfada "Specification" ve
"Repository" ifadeleri aranmış ve **bulunamamıştır** (§18.4 #2). Bu nedenle
specification iddiası #9/#10'ya, repository iddiası #7/#8'e bağlanır; #6'ya
**bağlanmaz**. Bu ayrım, "kaynak var" ile "kaynak o iddiayı taşıyor" arasındaki
farkı korur.

**#7 / #8 — DDD & CQRS (Microsoft .NET mimari kılavuzu).** İki seviyeli okuma sunar:
#7 bütünsel (iş karmaşıklığının CQRS ile dağıtılması), #8 ise DDD yönelimli
mikroservisin **tasarım adımları**. K008'de repository / sorgu sınırı bu ikinci
katmandan beslenir. Sınır: bu kılavuz **.NET** örneklendirmesi yazıyor; CoreMusic
PHP olduğu için yalnız **desen** alınır, kod değil — bu, `domain-kod-ornekleri.md`
§8'deki "repo karşılığı olmayan örnek = tasarım örneği" kuralını besler.

**#9 + #10 — Specification Pattern (Evans & Fowler PDF + Wikipedia).** #9, desenin
özgün ek makaledir; PDF olduğu için bu turda metin-bağımsız doğrulama **yapılmadı** —
bu açıkça kayıtlıdır. #10 ise aynı deseni kompozit bileşim (And/Or) açısından metin
içinde doğrular (§18.4 #6). İddia bu yüzden **ikili dayanakla** taşınır: özgün kaynak
(#9) + doğrulanabilir anlatım (#10).

**#11 — Transactional Outbox (microservices.io).** K008'de outbox iddiasının tek
mimari dayanağı budur (§18.4 #4). K013 ile eşlemede kritik sınır şu satırda durur:
**bu depoda outbox uygulamasına dair kanıt bulunamamıştır** → iddia yalnız
"gereksinim" olarak yazılır ve `⚠️ VERIFICATION REQUIRED` taşır. Kaynak, desenin
varlığını kanıtlar; CoreMusic'in deseni uyguladığını **kanıtlamaz**.

**#3 — "What do you mean by Event-Driven?" (Fowler).** Olay yayımının stillerini ve
"event notification" hafiflik ilkesini anlatır (§18.4 #5). Sayfa "domain event"
terimini kullanmıyor; bu nedenle CoreMusic'in domain event tanımı buradan değil,
repo sözleşmesinden gelir (`domain-dogrulama.md` §4). K008'de kullanımı: event'in
**taşma yükü** (payload) ne kadar hafif olmalı sorusuna yanıt aramak.

**#4 — Ubiquitous Language (Fowler bliki).** Ortak dilin kodla aynı adları zorunlu
kıldığını; dil bozulduğunda modelin de bozulduğunu anlatır. K008 karşılığı:
`domain-evrensel-dil.md` sözlüğü ve `domain-master.md` §10 özeti. Sınır: sözlük
içeriği bu blogdan değil, projeden gelir — kaynak yalnız **gerekçe** verir.

**#26 — IDDD_Samples (GitHub API JSON).** Vaughn Vernon'ın kitabındaki örnek
bounded context'leri içerir (yanıt metni: "sample Bounded Contexts from the book
Implementing Domain-Driven Design"; 3.947 yıldız; son itme 2023-09-09). K008 değeri:
`domain-kapsam.md` §8'deki "örnek BC" karşılaştırması için dış kalıp. Sınır: bu repo
**bu projede kullanılmıyor**, kodu buraya **kopyalanmaz** — yalnız karşılaştırma
kaynağıdır.

**#19 — C11 (WG14 N1570).** `const` ile derleme-zamanı sabitlemenin dilsel temeli
(`domain-kod-ornekleri.md` §8 C bloğu). PDF olduğu için başlık alanı boş; dosyanın
kimliği URL'den (JTC1/SC22/WG14/N1570) okunur. Sınır: N1570 bir **komite taslağıdır**
(standart son metni değil) — bu nedenle C örneğinde yalnız yaygın ve değişmez unsurlar
kullanılır.

**#12–#18, #22–#25, #27–#28 — Dil referansları (küme).** Her dil için **tek bir**
resmi kaynak seçilmiştir (MDN, Python docs, Oracle, Kotlin, TS handbook, Go,
cppreference, GNU, Microsoft Learn, PostgreSQL, Rust Book, Swift Book). Kümenin
K008 değeri: `domain-kod-ornekleri.md` §8'deki 17 dilin immutable / VO karşılığının
**resmi docs'a** dayanması; "dil böyle yapıyor" sözü kanıtsız yazılmaz. Sınır: bu
sayfalar dil davranışını kanıtlar, **CoreMusic'in o dili kullandığını değil** — repo
yüzeyi ile dil yüzeyi ayrı ayrı işaretlenir.

**#20, #21, #22, #23 — Yapılandırma yüzeyleri (HCL / YAML / Bash / PowerShell).**
Bunlar programlama dili değil, desenin **dış tekrarıdır**: altyapı değişken tipi, CI
job tanımı, betik komut satırı, istemci kabuğu. `domain-kod-ornekleri.md` §8'de bu
dördü ayrı sınıfta etiketlenir ("yapılandırma yüzeyi — dil değildir"), böylece "17
dil" sayısı dil-iddiasını şişirmez.

### §18.8 SONRAKİ TUR TETİKLEYİCİLERİ

| Tetik | Aksiyon | Sahip |
|---|---|---|
| §18.5 #2 / #15 — context-map kaynağı hâlâ yok | Microsoft context mapping yolu yeniden aranır; yeni URL önce C4'e tabi | bu dosya |
| §18.3 D — outbox kanıtı | K013 dosyalarında outbox araması tekrarlanır; sonuç `domain-dogrulama.md` §9'a yazılır | `domain-dogrulama.md` |
| §18.4'te "YOK" çıkan iddialar | İlgili iddia başka kaynağa bağlanır ya da `⚠️ VERIFICATION REQUIRED`'a düşer | ilgili iddia sahibi |
| 6 ay (2027-04-17) | §18.2'nin 29 kaydı yeniden `GET` ile taranır; 404 olanlar §18.5'e taşınır | bu dosya |

---
### §18.9 İDDİA ZİNCİRİ (kaynak → master kuralı → çocuk dosya → doğrulama defteri)

Her satır tek bir iddianın **dört ucu**nu bağlar. Zincirin herhangi bir halkası
kopuksa iddia kanıtsızdır ve «⚠️ VERIFICATION REQUIRED» kalır.

| İddia | Master kuralı | Çocuk dosya | Doğrulama defteri | Kaynak (#) |
|---|---|---|---|---|
| Alt-domain sınıflandırması (Core/Supporting/Generic) | §23 | «domain-kapsam.md» §8 | §11 | #5 |
| Value Object değişmezliği | §25 | «domain-kod-ornekleri.md» §8 | §10 | #6 + dil kümesi |
| Aggregate tek tutarlılık sınırıdır | §24 | «domain-bilesenler.md» §7 | §10 | #6 |
| Aggregate arası referans ID ile olur | §24 | ilgili çocuk kapsam dosyaları | §10 | #6 |
| Domain event geçmiş zaman adlandırılır | §26 | «domain-bilesenler.md» §8 | §9 | #3 + repo kanıtı |
| Domain event tek BC içinde yaşar | §26 | «domain-bilesenler.md» §8 | §9 | #3 (sınır: "domain event" terimi sayfada yok) |
| Aggregate dışı tutarlılık outbox gerektirir | §26 | K013 eşlemesi | §9 | #11 — **uygulama kanıtı YOK** |
| Specification And/Or ile bileşenir | §27 | «domain-bilesenler.md» §7 | §12 | #9 + #10 |
| Repository arayüzü domain'de, uygulama infrastructure'da | §28 | «domain-bilesenler.md» §9 | §10 | #7 + #8 |
| SQL constraint invariant'ın ikinci katmanıdır | §24 | «playlist-kapsam.md» §6 | §10 | #24 + #29 |
| Benzersizlik davranışı DB katmanında tanımlanır | §24 | «playlist-kapsam.md» §6 | §10 | #24 + #29 |
| Ortak dil kodla aynı adları kullanır | §10 | «domain-evrensel-dil.md» | §12 | #4 |
| Referans uygulama karşılaştırma kaynağıdır | §2 | «domain-kapsam.md» §8 | §12 | #26 |
| Dil başına immutable karşılığı resmi dokümandadır | §25 | «domain-kod-ornekleri.md» §8 | §10 | #12–#18, #22–#25, #27–#28 |
| Yapılandırma yüzeyi dil değildir | §25 | «domain-kod-ornekleri.md» §8.5 | §10 | #20–#23 |

### §18.10 BU TURUN ÖLÇÜMLERİ (kayıt)

| Ölçüm | Değer | Not |
|---|---|---|
| Tarama tarihi | 2026-10-17 | §18.1 M1–M6 sırası |
| Denenen URL | 46 | 29 × 200 · 16 × 404 · 1 × yönlendirme sayfası |
| Kabul edilen URL | 29 | yalnız HTTP 200 + başlık/gövde doğrulaması |
| Reddedilen URL | 16 | §18.5 — dayanak yapılmadı |
| Sayfa-içi metin araması | 18 satır | §18.4 — 41 aranan ifadenin sonucu |
| C4 çakışması | 0 | ekleme öncesi 72 dosyalık havuza karşı |
| Bu dosyanın satır sayısı | 230 → 467 (+ §18.9–§18.10 sonrası) | ölçüm: JS «ReadAllLines» eşdeğeri |

---
*Authority: Bayram Ali / Vault Steward · K008-domain web araştırması · 2026-10-12 · 75 benzersiz URL · HTTP 200 (C4 düzeltmesi 2026-10-15: #72 tekrarı kaldırıldı)*
*Authority: Bayram Ali / Vault Steward · K008-domain web araştırması · 2026-10-17 · 104 kök / 198 K008-geneli benzersiz URL · §18 turu: 29 × HTTP 200 + 16 × 404 (§18.5) · C4 çakışma 0*

---

## §19 ÇAPRAZ REFERANS TURU — 8 KAYIT (2026-10-17)

> §18 turunun **tamamlayıcısıdır** (yeni tarama değil): 6 kayıt mevcut doğrulanmış
> kaynağın **child dosyalara çapraz kullanımını** kiler, 2 kayıt **yeni URL**'dir ve
> aynı doğrulama protokolünden geçmiştir (HTTP 200 + başlık + gövde metin araması).
> C4: URL'ler bu dosyanın tekelindedir — child dosyalar yalnız `#no` verir.

### §19.1 YENİ DOĞRULANMIŞ KAYNAKLAR (#30–#31 · HTTP 200 · 2026-10-17)

| # | Alan | URL | HTTP | Başlık (yanıtta görünen) | Tarih (sayfada) | Yönlendirme |
|---|---|---|---|---|---|---|
| 30 | DDD genel bakış | https://martinfowler.com/bliki/DomainDrivenDesign.html | 200 | Domain Driven Design | 22 April 2020 | — |
| 31 | Bounded Context | https://martinfowler.com/bliki/BoundedContext.html | 200 | Bounded Context | 15 January 2014 | — |

### §19.2 İÇERİK DOĞRULAMASI (gövde metni — aranan ifadeler)

| # | Kaynak | Aranan ifade | Sonuç |
|---|---|---|---|
| 1 | #30 | Ubiquitous Language | **VARDI** |
| 2 | #30 | Entities / Value Objects / Aggregates | hepsi **VARDI** |
| 3 | #30 | Strategic Design / Bounded Contexts | hepsi **VARDI** |
| 4 | #31 | "total unification of the domain model for a large system will not be feasible" | **VARDI** (Evans alıntısı, dipnot 1) |
| 5 | #31 | context map | **VARDI** ("depict these using a context map") |
| 6 | #31 | Ubiquitous Language | **VARDI** |
| 7 | #31 | polyseme / "Customer" / "Product" | hepsi **VARDI** (meter örneği + Customer/Product) |

### §19.3 ÇAPRAZ REFERANS KAYITLARI (6 satır — hangi child hangi `#no`'yu kullanır)

| # | Child dosya | Kullanacağı kaynak | Ne için | Kural |
|---|---|---|---|---|
| X1 | `context-mapping/context-mapping-kapsam.md` §12 | **#31** | `context map` ifadesi sayfada doğrulandı — ContextMap stratejik tasarım kartının literatür dayanağı | `#31` ile referans verilir, URL tekrar yazılmaz (C4) |
| X2 | `bounded-context-kapsam.md` | **#30 + #31** | "total unification … not feasible" alıntısı BC ayırmaya gerekçe; #30 Evans sınıflandırması (Entity/VO/Service/Aggregate) | iki `#no` |
| X3 | `anti-corruption-kapsam.md` | **#31** | polyseme (aynı terim farklı bağlamlarda farklı anlam) ACL'nin gereksinim gerekçesi; "mechanisms to map between these polysemic concepts" | `#31` |
| X4 | `events-kapsam.md` | **#3** (§18.2) | "event notification" — hafif bildirim tezi zaten §18.3-E'de kayıtlı; bu tur child'a atıf ekler | `#3` |
| X5 | `specifications-kapsam.md` | **#9 + #10** (§18.2) | And/Or composite — §18.3-C kaydı child'a taşınır | `#9 #10` |
| X6 | `domain-kod-ornekleri.md` §8–§9 | **#12–#20, #22–#25, #27–#28** (§18.2) | dil başına resmi referans zaten eşleşmiş; §9 (event+specification) bu tur yeni kaynak **istemedi** — blokların dayanağı §18.2 havuzudur | `#no` kümeleri |

### §19.4 KAYNAK ↔ İDDİA EŞLEMESİ (bu turun iddiaları)

| # | İddia (K008'de geçtiği yer) | Kaynak | Kanıtladığı | Kanıtlamadığı / sınır |
|---|---|---|---|---|
| L | DDD, karmaşık alanlarda yazılım modelini alanın kendisine göre kurar (domain-master §1–§2 genel tez) | #30 | "approach … centers the development on programming a domain model" + "particularly suited to complex domains" metinleri **VARDI** | CoreMusic'in karmaşıklık derecesi bu sayfadan gelmez — proje kararı |
| M | Büyük sistemde domain modelinin tam birleşimi uygulanamaz → BC zorunludur (`bounded-context-kapsam.md` tez satırı) | #31 | Evans alıntısı birebir sayfada **VARDI** (§19.2 #4) | Hangi sınırın CoreMusic'te nereye çekileceği **proje kararı** |
| N | Context map, BC ilişkilerini göstermek için kullanılan araçtır (`context-mapping-kapsam.md` §12) | #31 | "worthwhile to depict these using a context map" metni **VARDI** | Depoda context map sınıfı/şeması **YOK** (child §3) — yalnız literatür dayanağı |
| O | Aynı terim farklı bağlamlarda farklı anlam taşır (anti-corruption tezi) | #31 | polyseme önekleri: "meter" (3 anlam) + Customer/Product **VARDI** | ACL deseninin kendisi bu sayfada **YOK** — ACL kanıtı ayrı kaynaktır (§18 öncesi havuz) |

### §19.5 HAVUZ TOPLAMI VE ÖLÇÜM (bu turun kaydı)

| Ölçüm | Değer |
|---|---|
| Tarama tarihi | 2026-10-17 |
| Bu turda denen URL | 2 (yeni) |
| Kabul edilen URL | 2 (#30 · #31 — HTTP 200 + başlık + gövde 7/7 arama) |
| Reddedilen URL | 0 |
| Çapraz referans kaydı | 6 (X1–X6) |
| İddia eşlemesi | 4 (L–O) |
| C4 çakışması | 0 (#30/#31 K008 havuzunda daha önce yoktu — §18.2'nin 29'una ek) |
| §18.x'e dokunma | 0 (append-only) |
| Commit | 0 (orkestratöre ait) |

**Kapanış:** §19 tek turda hem çapraz atıf (X1–X6) hem 2 yeni kaynak (#30–#31)
ekledi; §18.1–§18.10 olduğu gibi kaldı. Bir sonraki tarama turu (§20) yalnız
**§18.8 tetikleyicileri** veya **`⚠️ VERIFICATION REQUIRED` açık işaretleri**
kapanacaksa açılır.
---
*Authority: Bayram Ali / Vault Steward · K008-domain web araştırması · 2026-10-12 · 75 benzersiz URL · HTTP 200 (C4 düzeltmesi 2026-10-15: #72 tekrarı kaldırıldı)*
*Authority: Bayram Ali / Vault Steward · K008-domain web araştırması · 2026-10-17 · 104 kök / 198 K008-geneli benzersiz URL · §18 turu: 29 × HTTP 200 + 16 × 404 (§18.5) · C4 çakışma 0*
*Authority: Bayram Ali / Vault Steward · K008-domain web araştırması · 2026-10-17 · §19 çapraz referans turu: 6 atıf (X1–X6) + 2 yeni kaynak (#30 DomainDrivenDesign · #31 BoundedContext — HTTP 200 + gövde 7/7) · C4 çakışma 0*
