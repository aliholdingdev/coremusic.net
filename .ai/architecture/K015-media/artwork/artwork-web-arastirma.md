---
node_id: "K015-media/artwork/artwork-web-arastirma"
node_type: "subdomain"
status: "verify-required"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "8 kaynak URL'si 2026-10-16'da canlı doğrulandı (hepsi HTTP 200)"
  - "https://musicbrainz.org/doc/Cover_Art_Archive/API (varyant boyutları 250/500/1200px)"
validation: "8/8 URL canlı; her ana iddia ≥2 kaynakla cross-check edildi; ⚠️ 0 uydurma URL/ürün/sürüm; §3a varyant verisi media-master §12/7 UNKNOWN'ını KAPATMAZ (karar Vault Steward'da)"
open_questions:
  - "W-1: Varyant boyutları ve format politikası → ⚠️ UNKNOWN (karar Vault Steward — §3a yalnız referans verisi)"
group: kanit
---

# K015 · artwork · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Konu: kapak arşivi API'leri, varyant boyutları, rate limit, alternatif servis boyut setleri

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: bu dosya **yalnızca** aşağıdaki 8 URL'yi taşır; kardeş web-arastirma dosyalarındaki URL'ler tekrarlanmaz.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-16 | Durum |
|---|---|---|---|---|
| 1 | Cover Art Archive (CAA): MusicBrainz release'lerine bağlı ücretsiz kapak arşivi; Internet Archive altyapısında çalışır | https://coverartarchive.org | Tarih: 2026-10-16 | HTTP 200 |
| 2 | CAA ana sayfa (MusicBrainz): CAA'nın MusicBrainz Wiki üzerindeki resmi tanımı — release/release-group başına çoklu kapak, front/back tipi ayrımı | https://musicbrainz.org/doc/Cover_Art_Archive | Tarih: 2026-10-16 | HTTP 200 |
| 3 | CAA API: `release/{mbid}/front-250.jpg` biçiminde hazır varyantlar — **250px, 500px, 1200px**; istek 307 redirect ile CDN'e gider; rate limit aşımında 503 döner | https://musicbrainz.org/doc/Cover_Art_Archive/API | Tarih: 2026-10-16 | HTTP 200 |
| 4 | CAA How to Add Cover Art: kabul edilen formatlar **JPEG, GIF, PNG, PDF**; 300×300px minimum; 600×600px+ önerilir; kapak ekleme kuralları | https://musicbrainz.org/doc/Cover_Art_Archive/How_to_Add_Cover_Art | Tarih: 2026-10-16 | HTTP 200 |
| 5 | Apple Search API (performance-partners): iTunes Search API dokümantasyonu — dakikada **~20 istek** rate limiti; `artworkUrl60`/`artworkUrl100` varyant alanları | https://performance-partners.apple.com/search-api | Tarih: 2026-10-16 | HTTP 200 |
| 6 | iTunes Search canlı uç nokta testi: `entity=album` yanıtı `artworkUrl60` (60px) ve `artworkUrl100` (100px) taşır; görseller `mzstatic.com` CDN'inden gelir | https://itunes.apple.com/search?term=daft+punk&entity=album&limit=1 | Tarih: 2026-10-16 | HTTP 200 |
| 7 | Deezer Search API canlı uç nokta testi: `cover_small` (56px) · `cover_medium` (250px) · `cover_big` (500px) · `cover_xl` (1000px) + `md5_image` (CDN parmak izi) | https://api.deezer.com/search/album?q=daft+punk&limit=1 | Tarih: 2026-10-16 | HTTP 200 |
| 8 | Navidrome artwork dokümantasyonu: **CoverArtPriority** zinciri (cover.* → folder.* → front.* → embedded → external → placeholder) · DiscArtPriority · desteklenen formatlar JPEG/PNG/WebP · CoverArtQuality=75 · `artwork explain/status/reprocess` CLI | https://navidrome.org/docs/usage/library/artwork/ | Tarih: 2026-10-16 | HTTP 200 |

---

## §2 KOD XREF (kaynak → CoreMusic yüzeyi)

| # | Kaynak kanıtı | CoreMusic yüzeyi | Bağlantı (tasarım iddiası) |
|---|---|---|---|
| 1 | CAA 250/500/1200px hazır varyant + 307 redirect + 503 rate-limit — CAA API | K015/media/artwork (varyant sunumu) | Dış kaynak kullanılırsa hazır varyant alınır, yerel üretim yükü azalır (§3a) |
| 2 | CAA format kuralları: JPEG/GIF/PNG/PDF, 300×300 min — How to Add | K015/media/artwork (kabul formatı) | Depolanacak format seti için referans; CoreMusic kararı ayrı (§6/W-2) |
| 3 | iTunes 60/100px + ~20 istek/dk rate limit — Apple ×2 | K015/media/artwork ↔ K013-integraion | Dış servis zenginleştirmede rate-limit K013'ün sorumluluğunda (media-master §2.2) |
| 4 | Deezer 56/250/500/1000px + md5_image — api.deezer.com | K015/media/artwork (varyant referansı) | Alternatif boyut seti referansı; §3a tablosunda listelendi |
| 5 | Navidrome öncelik zinciri: yan dosyalar (cover.*/folder.*/front.*) → embedded → external → placeholder — Navidrome docs | K015/media/artwork (kaynak öncelik sırası) | artwork-kapsam §3 durum makinesi (EMBEDDED_FOUND/SIDECAR_FOUND/FETCHING) bu zincirle örtüşür |
| 6 | Navidrome JPEG/PNG/WebP + CoverArtQuality=75 + yeniden işleme CLI — Navidrome docs | K015/media/artwork ↔ media-cache | Önbellek kalite/yeniden üretim fikri referansı (media-master §9.9 ile sınır) |

> **Sınır notu:** Bu dosyadaki hiçbir boyut/set/servis seçimi DEĞİL. §3a'daki rakamlar **yalnız sektör referansıdır** — CoreMusic varyant politikası ⚠️ UNKNOWN'dur ve Vault Steward kararıdır.

---

## §3 NOTLAR (erişim kaydı)

| # | Not |
|---|---|
| a | **Varyant referans verisi (kanıt, KARAR DEĞİL):** CAA 250/500/1200px · Deezer 56/250/500/1000px · iTunes 60/100px · Navidrome JPEG/PNG/WebP + Quality=75. Bu veri media-master §12/7 UNKNOWN'ını **kapatmaz**; karar Vault Steward'dadır. |
| b | Cross-check 1: Apple Search API ana sayfası (rate limit ~20/dk) ile canlı uç nokta (itunes.apple.com/search) aynı `artworkUrl60/100` alanlarını gösterdi → iddia ×2 kaynak. |
| c | Cross-check 2: CAA ana sayfa (MusicBrainz Wiki) + CAA API sayfası + coverartarchive.org ana sayfası aynı altyapıyı (Internet Archive, MusicBrainz bağlığı) doğruladı → ×3 kaynak. |
| d | Cross-check 3: Navidrome öncelik zinciri (embedded + external + placeholder), CAA/iTunes/Deezer dış-servis zinciriyle ve placeholder tasarımıyla örtüşüyor (media-master §9.3 hata durumu). |
| e | Başarısız erişimler (dosyaya alınmadı): coverartarchive.org/readme (400) · musicbrainz.org/doc/Style/Release/Cover_Art (404) — bu sayfalardan iddia çıkmadı. |
| f | 307 redirect ve 503 rate-limit davranışı CAA API sayfasında yazılıdır; canlı testte redirect izlenmedi (yalnız sayfa içeriği okundu). |

---

## §4 AÇIK KONULAR (W-xx)

| # | Konu | Etiket |
|---|---|---|
| W-1 | CoreMusic varyant boyut seti nedir (250/500/1200 mi, farklı mı)? | ⚠️ UNKNOWN (media-master §12/7 — Vault Steward) |
| W-2 | Kabul formatları: JPEG/PNG/WebP (Navidrome fikri) mı, yalnız JPEG mi? | ⚠️ UNKNOWN |
| W-3 | Dış kaynak: CAA (MusicBrainz) mı, iTunes/Deezer mı, hiçbiri mi? | ⚠️ VERIFICATION REQUIRED (K013 ile ortak) |
| W-4 | Kaynak öncelik sırası: embedded → yan dosya → dış servis (Navidrome benzeri) mi? | ⚠️ VERIFICATION REQUIRED (artwork-kapsam §6/3) |

---

## §5 KARDEŞ DOSYA BAĞI

| Kardeş | İlişki |
|---|---|
| `artwork-kapsam.md` | §3 durum makinesi ve §6 açık sorular bu dosyanın W-1..W-4'ü ile hizalı |
| `artwork-ornekler.md` | Varyant servisi iskeleti §3a referans verisini kullanır (seçim olarak DEĞİL) |
| `../metadata/metadata-web-arastirma.md` | Embedded çıkarma (ID3 APIC) metadata format kanıtında |

---

## §6 YENİDEN ERİŞİM KURALI

1. Bu dosyadaki 8 URL **tekrar tekrar** doğrulanmaz; yeniden erişim yalnız bir üstteki iddia değiştiğinde yapılır.
2. Yeni kanıt = yeni satır (§1'e ek) + tarih; eski satır silinmez (append-only).
3. URL bulunamazsa satır `⚠️ VERIFICATION REQUIRED` olarak işaretlenir — **URL/ürün/sürüm asla uydurulmaz**.

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `.ai/architecture/K015-media/media-master.md` §9.3 · §12/7 · §7/8 | Boyut FORMAT UNKNOWN + kaynak belirsizliği bu dosyanın kanıtlarıyla beslenir |
| `artwork-kapsam.md` · `artwork-ornekler.md` | Kardeş dosyalar (aynı çocuk) |
| `.ai/architecture/K015-media/index.md` | Üst katalog |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
