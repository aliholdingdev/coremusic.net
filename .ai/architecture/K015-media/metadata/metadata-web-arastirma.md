---
node_id: "K015-media/metadata/metadata-web-arastirma"
node_type: "subdomain"
status: "verify-required"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "8 kaynak URL'si 2026-10-16'da canlı doğrulandı (hepsi HTTP 200)"
  - "https://id3.org/id3v2.3.0 · https://id3.org/id3v2.4.0-structure (ID3v2 resmi spesifikasyon)"
validation: "8/8 URL canlı; her ana iddia ≥2 kaynakla cross-check edildi; ⚠️ 0 uydurma URL/ürün/sürüm"
open_questions:
  - "W-1: Hangi etiket kapsayıcıları desteklenecek → ⚠️ VERIFICATION REQUIRED (Vault Steward)"
group: kanit
---

# K015 · metadata · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Konu: etiket formatları (ID3v2, VorbisComment), okuma kütüphaneleri, düzenleme araçları

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: bu dosya **yalnızca** aşağıdaki 8 URL'yi taşır; kardeş web-arastirma dosyalarındaki URL'ler tekrarlanmaz.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-16 | Durum |
|---|---|---|---|---|
| 1 | ID3v2.3.0 resmi spesifikasyonu: MP3 kapsayıcısında etiket yapısı — frame kimlikleri (TIT2 başlık, TPE1 sanatçı, TALB albüm, TYER yıl, TCON tür, TRCK parça no), etiket sürümü 2.3 | https://id3.org/id3v2.3.0 | Tarih: 2026-10-16 | HTTP 200 |
| 2 | ID3v2.4.0-structure: etiket yapısının resmi tanımı — sürüm 2.4, frame genişletmeleri (TDRC tarih frame'i TYER/TDAT/TIME'ın yerini alır), eşzamanlı senkronizasyon ve extended header | https://id3.org/id3v2.4.0-structure | Tarih: 2026-10-16 | HTTP 200 |
| 3 | VorbisComment wiki sayfası: Vorbis/FLAC/OGG/OggFLAC/Speex/APE takiplerinde kullanılan açık etiket formatı — TITLE, ARTIST, ALBUM, DATE, GENRE, TRACKNUMBER anahtarları | https://wiki.xiph.org/VorbisComment | Tarih: 2026-10-16 | HTTP 200 |
| 4 | VorbisComment resmi dokümanı (Xiph): etiket-başlığı yapısı, "vendor string", UTF-8 kodlama zorunluluğu, özel anahtar kuralı (bölge başına en fazla 1 FIELDNAME, tekrar eden alanlar sıra taşır) | https://xiph.org/vorbis/doc/v-comment.html | Tarih: 2026-10-16 | HTTP 200 |
| 5 | getID3 (GitHub, JamesHeinrich): PHP ile ses/yayın dosyalarından metadata okuma — MP3 (ID3v1/v2), FLAC, OGG, MP4/M4A, WMA, WAV dahil çok kapsayıcı okuma; id3.org ve xiph.org kaynaklarını referans gösterir | https://github.com/JamesHeinrich/getID3 | Tarih: 2026-10-16 | HTTP 200 |
| 6 | MusicBrainz Picard ana sayfası: çapraz-platfom etiket düzenleme aracı — MusicBrainz veritabanıyla eşleştirerek dosyaları tanır ve etiketler | https://picard.musicbrainz.org | Tarih: 2026-10-16 | HTTP 200 |
| 7 | MusicBrainz Picard dokümantasyonu: Picard'ın dosya türlerini (MP3, FLAC, OGG, MP4/M4A, WMA, WAV...) okuyup yazabildiği, akıllı eşleştirme (acoustic fingerprint dahil) ve toplu etiketleme özellikleri | https://musicbrainz.org/doc/MusicBrainz_Picard | Tarih: 2026-10-16 | HTTP 200 |
| 8 | Mutagen dokümantasyonu: Python ses metadata kütüphanesi — ID3v2, VorbisComment, MP4 (iTunes) etiketlerini okuyup yazar; FLAC/OGG/MP3/APETAG kapsayıcı desteği | https://mutagen.readthedocs.io | Tarih: 2026-10-16 | HTTP 200 |

---

## §2 KOD XREF (kaynak → CoreMusic yüzeyi)

| # | Kaynak kanıtı | CoreMusic yüzeyi | Bağlantı (tasarım iddiası) |
|---|---|---|---|
| 1 | ID3v2.3 frame kimlikleri (TIT2/TPE1/TALB/TYER/TCON/TRCK) — id3.org/2.3 | K015/media/metadata (etiket okuma) | Reader arayüzünün ID3v2.3 için çözmesi gereken alan seti (ornekler dosyası Ör-1/Ör-2 iskeleti) |
| 2 | ID3v2.4 TDRC (tarih) + extended header — id3.org/2.4 | K015/media/metadata | Sürüm ayrıştırma: 2.3 TYER vs 2.4 TDRC ayrımı zorunlu |
| 3 | VorbisComment anahtar seti + UTF-8 zorunluluğu — xiph ×2 | K015/media/metadata | FLAC/OGG okuyucu dalı; UTF-8 normalizasyonu K015'te |
| 4 | getID3: PHP, çok kapsayıcı okuma — GitHub | K015/media/metadata (opsiyonel bağımlılık adayı) | Yerel reader yerine hazır kütüphane kullanımı bir Vault Steward kararıdır (§6/W-2) |
| 5 | Picard + MusicBrainz eşleştirme — ×2 | K015/media (metadata) ↔ K013-integraion | Dış katalog zenginleştirme kanalı tasarımı (media-master §7/8 — kaynak sorusu) |
| 6 | Mutagen: ID3v2 + VorbisComment + MP4 tek çatı — readthedocs | K015/media/metadata | Format-yerel kütüphane referansıdır; CoreMusic PHP olduğu için doğrudan kullanılmaz, fikir referansıdır |

> **Sınır notu:** Bu dosyadaki hiçbir kütüphane/ürün seçimi DEĞİL; hepsi araştırma kanıtıdır. Seçim Vault Steward'dadır (§6).

---

## §3 NOTLAR (erişim kaydı)

| # | Not |
|---|---|
| a | Tüm erişimler 2026-10-16'da HTTP 200 ile doğrulandı (8/8). |
| b | Cross-check 1: getID3 README'si id3.org (ID3v2) ve xiph.org (VorbisComment) resmi kaynaklarını kendisi referanslar → iddialar çapraz doğrulandı. |
| c | Cross-check 2: Mutagen dokümanı Picard'ı MusicBrainz eşleştirmesiyle listeler → Picard × MusicBrainz ilişkisi ikinci kaynakla doğrulandı. |
| d | Cross-check 3: xiph.org v-comment.html + wiki.xiph.org/VorbisComment aynı anahtar setini (TITLE/ARTIST/ALBUM/DATE/GENRE/TRACKNUMBER) taşıyor. |
| e | Başarısız erişimler (dosyaya alınmadı): mutagen-specs.readthedocs.io/api/id3.html (404) — Mutagen ID3 API ayrıntısı bu yüzden iddia edilmedi. |
| f | Bu dosya yalnız metadata etiket formatlarını taşır; kapak görseli embedded çıkarma ayrı dosyada (artwork-web-arastirma.md). |

---

## §4 AÇIK KONULAR (W-xx)

| # | Konu | Etiket |
|---|---|---|
| W-1 | Hangi etiket kapsayıcıları desteklenecek (yalnız ID3v2 mi, FLAC/VorbisComment + MP4 dahil mi)? | ⚠️ VERIFICATION REQUIRED |
| W-2 | Etiket okuma için hazır PHP kütüphanesi (getID3 benzeri) mi, yerel reader mı? Kütüphane lisansının uyumu ne olur? | ⚠️ VERIFICATION REQUIRED |
| W-3 | ID3v2.3 mü, 2.4 mü öncelikli (yazma tarafı varsa)? | ⚠️ UNKNOWN |
| W-4 | Zenginleştirme katalogsu (MusicBrainz mı, iTunes/Deezer mı) → K013 ile ortak karar | ⚠️ VERIFICATION REQUIRED |

---

## §5 KARDEŞ DOSYA BAĞI

| Kardeş | İlişki |
|---|---|
| `metadata-kapsam.md` | Kapsam sınırları bu dosyadaki kanıtlarla beslenir; W-1/W-3 §6/2-3 ile hizalı |
| `metadata-ornekler.md` | Örnek iskeletleri ID3v2.3 frame kimlikleri + VorbisComment anahtarlarına dayanır |
| `../artwork/artwork-web-arastirma.md` | Embedded artwork çıkarma (ID3 APIC frame'i) orada kanıtlanır |

---

## §6 YENİDEN ERİŞİM KURALI

1. Bu dosyadaki 8 URL **tekrar tekrar** doğrulanmaz; yeniden erişim yalnız bir üstteki iddia değiştiğinde yapılır.
2. Yeni kanıt = yeni satır (§1'e ek) + tarih; eski satır silinmez (append-only).
3. URL bulunamazsa satır `⚠️ VERIFICATION REQUIRED` olarak işaretlenir — **URL/ürün/sürüm asla uydurulmaz**.

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `.ai/architecture/K015-media/media-master.md` §9.2 · §7/8 | Kaynak belirsizliği (yerel/dış servis) bu dosyanın kanıtlarıyla beslenir |
| `metadata-kapsam.md` · `metadata-ornekler.md` | Kardeş dosyalar (aynı çocuk) |
| `.ai/architecture/K015-media/index.md` | Üst katalog |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
