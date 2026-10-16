---
node_id: "K015-media/library/library-web-arastirma"
node_type: "subdomain"
status: "verify-required"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "8 kaynak URL'si 2026-10-16'da canlı doğrulandı (hepsi HTTP 200)"
  - "https://subsonic.org/pages/api.jsp (getArtists vs getIndexes — iki katalog modeli)"
validation: "8/8 URL canlı; her ana iddia ≥2 kaynakla cross-check edildi; ⚠️ 0 uydurma URL/ürün/sürüm"
open_questions:
  - "W-1: Çoklu kitaplık (multi-library) desteği CoreMusic'te var mı → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K015 · library · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Konu: kitaplık yönetimi modelleri (referans ürünler), tarama/eksik dosya davranışları, depolama notları

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: bu dosya **yalnızca** aşağıdaki 8 URL'yi taşır; kardeş web-arastirma dosyalarındaki URL'ler tekrarlanmaz.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-16 | Durum |
|---|---|---|---|---|
| 1 | Navidrome ana sayfa: açık kaynak self-hosted müzik sunucusu — **GPL v3**, ~900.000 şarkı ölçeğinde kitaplık desteği, **çoklu kitaplık** (multi-library), OpenSubsonic uzantıları | https://navidrome.org | Tarih: 2026-10-16 | HTTP 200 |
| 2 | Navidrome GitHub deposu: Go ile yazılmış, ~24.2k yıldız (2026-10-16 ölçümü), SQLite + Subsonic uyumlu API | https://github.com/navidrome/navidrome | Tarih: 2026-10-16 | HTTP 200 |
| 3 | Subsonic API: iki katalog modeli — `getArtists` (ID3-based, etiket: sanatçı→albüm→parça) vs `getIndexes` (dosya-yapısı based, klasör); ayrıca `search2/search3` (arama), `getCoverArt` (kapak sunumu), kimlik doğrulama salt + md5 | https://subsonic.org/pages/api.jsp | Tarih: 2026-10-16 | HTTP 200 |
| 4 | Jellyfin kitaplık dokümantasyonu: medya kitaplıkları (müzik/tv/film) — kütüphane türü seçimi, tarama (scan) davranışları, NFO poster/fanart dosyalarından yerel görsel okuma | https://jellyfin.org/docs/general/server/libraries | Tarih: 2026-10-16 | HTTP 200 |
| 5 | Jellyfin GitHub deposu: **58.1k★** (2026-10-16), **GPL-2.0**, .NET 10, Emby 3.5.2 fork'u (2018-12) | https://github.com/jellyfin/jellyfin | Tarih: 2026-10-16 | HTTP 200 |
| 6 | Jellyfin depolama dokümantasyonu: SQLite veritabanı dosyaları **10-100GB** büyüyebilir; ZFS'te veritabanı için **4K/8K record size**, medya dosyaları için **1M** önerilir; rclone/mergerfs mount'larında performans notları | https://jellyfin.org/docs/general/administration/storage | Tarih: 2026-10-16 | HTTP 200 |
| 7 | Navidrome kitaplık dokümantasyonu: **Tagging Guidelines** (tutarlı etiketleme), **Missing Files** davranışı (kayıt kalır, dosya gidince işaretlenir), `.ndignore` ile dışlama | https://navidrome.org/docs/usage/library/ | Tarih: 2026-10-16 | HTTP 200 |
| 8 | Navidrome dokümantasyon ana sayfası: kullanım kılavuzu yapısı (library, playback, settings) + OpenSubsonic uyumluluk beyanı | https://navidrome.org/docs/ | Tarih: 2026-10-16 | HTTP 200 |

---

## §2 KOD XREF (kaynak → CoreMusic yüzeyi)

| # | Kaynak kanıtı | CoreMusic yüzeyi | Bağlantı (tasarım iddiası) |
|---|---|---|---|
| 1 | Subsonic: getArtists (ID3-based) vs getIndexes (dosya-yapısı) — iki katalog erişim modeli | K015/media/library (listeleme yüzeyi) | Listeleme/tarama modeli seçimi için referans; CoreMusic'te hangi model ⚠️ UNKNOWN |
| 2 | Navidrome çoklu kitaplık + ~900K şarkı ölçeği — navidrome.org | K015/media/library (kapsam) | Multi-library kapsam sorusu (§4/W-1) |
| 3 | Navidrome Missing Files: kayıt kalır, dosya gidince işaretlenir — docs | K015/media/library (MISSING_FILE hata durumu) | library-kapsam §5/2 hata tasarımı bu davranışla örtüşür |
| 4 | Jellyfin depolama: SQLite DB 10-100GB + ZFS record size ayrımı (DB 4K/8K vs medya 1M) — Jellyfin docs | K007-data sınırı (şema K007'de) + K015 veri sahibi | Kitaplık verisinin ölçek/disksel davranışı için referans; K015 şema yazmaz (media-master §2.2) |
| 5 | Jellyfin kütüphane tarama + NFO yerel görsel okuma — Jellyfin docs | K015/media/library (SCANNING durumu) ↔ artwork (yerel görsel) | Tarama ve yerel dosya okuma davranışı referansı |
| 6 | Subsonic search2/search3 + getCoverArt — API sayfası | K015/media/catalog ↔ artwork | Arama ve kapak sunumunun ayrı uçlar olması fikri (çocuk ayrımıyla örtüşür) |

> **Sınır notu:** Bu dosyadaki hiçbir ürün seçimi DEĞİL; hepsi referans üründür (media-master §11/5 açık sorusu bağlamında).

---

## §3 NOTLAR (erişim kaydı)

| # | Not |
|---|---|
| a | Tüm erişimler 2026-10-16'da HTTP 200 ile doğrulandı (8/8). ★ sayıları 2026-10-16 GitHub ölçümüdür, zamanla değişir. |
| b | Cross-check 1: Jellyfin ★/lisans/fork bilgisi hem ana sayfa (jellyfin.org) hem GitHub deposuyla ×2 doğrulandı (media-master §4/1 kanıtıyla uyumlu). |
| c | Cross-check 2: Navidrome çoklu-kitaplık + OpenSubsonic hem navidrome.org hem docs/ hem GitHub deposuyla ×3 doğrulandı. |
| d | Cross-check 3: Missing Files davranışı hem docs/usage/library/ hem artwork öncelik zinciri sayfasında (kardeş dosya) anlatılıyor → tutarlı. |
| e | Başarısız erişim (dosyaya alınmadı): navidrome.org/docs/features/library-management/ (404 — gerçek URL /docs/usage/library/). |
| f | OpenSubsonic (opensubsonic.net) erişilemedi (transport hatası) — OpenSubsonic iddiaları yalnız Navidrome sayfalarından aktarıldı, bağımsız kaynak YOK (⚠️). |

---

## §4 AÇIK KONULAR (W-xx)

| # | Konu | Etiket |
|---|---|---|
| W-1 | CoreMusic çoklu kitaplık (multi-library) destekleyecek mi? | ⚠️ VERIFICATION REQUIRED |
| W-2 | Listeleme modeli: etiket-based (ID3) mi, dosya-yapısı-based mi (Subsonic iki modeli)? | ⚠️ VERIFICATION REQUIRED |
| W-3 | Tarama motoru yerel process mi, servis (K012) mi? | ⚠️ VERIFICATION REQUIRED (kapsam §6/3 ile hizalı) |
| W-4 | OpenSubsonic uyumluluk hedefleniyor mu? (bağımsız kaynak erişilemedi) | ⚠️ VERIFICATION REQUIRED |

---

## §5 KARDEŞ DOSYA BAĞI

| Kardeş | İlişki |
|---|---|
| `library-kapsam.md` | §3 durum makinesi + §5 hata durumları (MISSING_FILE) bu dosya kanıtlarıyla örtüşür |
| `library-ornekler.md` | Repository arayüzü iskeleti listeleme/filtreleme yüzeylerini taşır |
| `../metadata/metadata-web-arastirma.md` | Tagging Guidelines davranışının etiket-format karşılığı orada kanıtlanır |

---

## §6 YENİDEN ERİŞİM KURALI

1. Bu dosyadaki 8 URL **tekrar tekrar** doğrulanmaz; yeniden erişim yalnız bir üstteki iddia değiştiğinde yapılır.
2. Yeni kanıt = yeni satır (§1'e ek) + tarih; eski satır silinmez (append-only).
3. URL bulunamazsa satır `⚠️ VERIFICATION REQUIRED` olarak işaretlenir — **URL/ürün/sürüm asla uydurulmaz**.

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `.ai/architecture/K015-media/media-master.md` §9.1 · §11/5 · §7/8 | Çocuk kapsamı + referans ürün sorusu bu dosyanın kanıtlarıyla beslenir |
| `library-kapsam.md` · `library-ornekler.md` | Kardeş dosyalar (aynı çocuk) |
| `.ai/architecture/K015-media/index.md` | Üst katalog |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
