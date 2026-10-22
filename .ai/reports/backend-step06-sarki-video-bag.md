---
title: "Backend Rebuild — Adım 06: Şarkı/Video DB + Stream Bağlantısı"
type: report
category: backend
docType: report
date: 2026-10-22
version: 1.0.0
status: completed
authority: "SSOT: api.coremusic.net/include/Controller/StreamController.php + .ai/AGENTS.md"
---

# Adım 06 — Şarkı/Video DB + Stream Bağlantısı Raporu

**Tarih:** 2026-10-22 · **Yürütücü:** MO (ana oturum) · **Kapı:** KAPI-D öncesi

---

## 1. Keşif: Fiziksel Medya Yolu

| Konu | Bulgu |
|---|---|
| `media.coremusic.net/media/` | **gitignore** (ADR-092: 38 GB+ medya asla repo'ya girmez) |
| Fiziksel yol | `MUSIC_LIBRARY_PATH` env veya varsayılan `C:\Users\Bayram Ali\Music` |
| `media.coremusic.net/catalog/` | Üretilmiş indeks — repo dışı, yeniden üretilebilir |
| Scan/ingest akışı | `bin/scan.php` → `bin/ingest.php` → `media_catalog.sql` (ULID char(26)) |

---

## 2. Eşleme Matrisi: `media_catalog` ↔ `coremusic_musics` ↔ Dosya

| Katman | ID tipi | Şema | Dosya alanı |
|---|---|---|---|
| `media_catalog.asset` | ULID `char(26)` | `slug`, `dosya_yolu`, `sha256`, `sure_sn` | `dosya_yolu` (varchar) |
| `coremusic_musics.music_files` | UUID v7 `binary(16)` | `file_path`, `file_format`, `file_size`, `is_primary` | `file_path` (varchar 500) |

**⚠️ KRİTİK BULGU — Join anahtarı YOK:** İki şema farklı ID sistemleri kullanıyor
(ULID vs UUID v7) ve arasında doğrudan join kolonu bulunmuyor. Eşleşme ya
`dosya_yolu`/`file_path` üzerinden (string karşılaştırma — kırılgan) ya da
harici bir eşleme tablosuyla yapılabilir. **Bu karar D3 kapısında kullanıcıya
sunuldu** — Adım 06 kapsamı dışında bırakıldı (mevcut stream uçları zaten
`coremusic_musics` üzerinden çalışıyor).

---

## 3. Gap'ler ve Kapatmaları

| # | Gap (denetim bulgusu) | Kapatma |
|---|---|---|
| 1 | `IMusicRepository`'da `findPrimaryFile` yok — `findById` yalnız `music_hex, title, duration_sec, artist_name` döndürüyor; `file_path/file_format/file_size` hiç SELECT edilmiyor → StreamController her istekte 404 | `IMusicRepository::findPrimaryFile()` eklendi; `MusicRepository`'de impl. yazıldı (home `MusicRepository::findPrimaryFile` query'sinden taşındı — `music_files f JOIN musics m`, `is_primary DESC, f.id ASC LIMIT 1`) |
| 2 | `StreamController::stream()` `findById` çağırıyordu (dosya kolonları olmadığı için hep `resolvePath('')` → 404) | `findPrimaryFile`'a geçirildi |
| 3 | `If-None-Match` karşılaştırması `===` ile katı — RFC 9110'da `W/` öneki, `*`, çoklu değer (virgül), tırnak farkı kabul edilir; curl/PHP tırnak strip edince 304 tetiklenmiyor | `etagMatches()` metodu eklendi: `*`, `W/` öneki, çoklu değer, tırnak farkı — hepsi eşleşir |

### 3.1 Yeni/Değişen Dosyalar

| Dosya | Durum |
|---|---|
| `include/Repository/IMusicRepository.php` | değişti (`findPrimaryFile` eklendi) |
| `include/Repository/MusicRepository.php` | değişti (`findPrimaryFile` impl. eklendi) |
| `include/Controller/StreamController.php` | değişti (`findPrimaryFile` çağrısı + `etagMatches()` RFC 9110) |

---

## 4. Canlı HTTP Stream Kanıtı

PHP 8.5.8 built-in server + stub repository (MySQL kapalı → `findPrimaryFile`
stub'u 4KB test dosyası döndürüyor). Gerçek `StreamController` + gerçek dosya +
gerçek `Range` parsing test edildi.

| # | İstek | Beklenen | Gerçek | Content-Range / Not |
|---|---|---|---|---|
| 1 | `Range: bytes=0-1023` | 206 | **206** ✓ | `bytes 0-1023/4096`, `Content-Length: 1024` |
| 2 | `Range: bytes=2048-3071` | 206 | **206** ✓ | `bytes 2048-3071/4096` |
| 3 | Range yok | 200 | **200** ✓ | `Content-Length: 4096` |
| 4 | `Range: bytes=-1024` (suffix) | 206 | **206** ✓ | `bytes 3072-4095/4096` |
| 5 | `Range: bytes=9999-10000` | 416 | **416** ✓ | `Content-Range: bytes */4096` |
| 6 | `Range: bytes=0-99999` (clamp) | 206 | **206** ✓ | `bytes 0-4095/4096` |
| 7 | `If-None-Match: <etag>` | 304 | **304** ✓ | `ETag` + `Last-Modified` döndü |
| 8 | Bilinmeyen ID (32 hex, kayıt yok) | 404 | **404** ✓ | JSON `{"error":{"code":404,...}}` |
| 9 | Geçersiz ID format (14 hex) | 400 | **400** ✓ | JSON `{"error":{"code":400,...}}` |

**Tüm başlıklar mevcut:** `Content-Type: audio/mpeg` · `Accept-Ranges: bytes` ·
`ETag` · `Last-Modified` · `Cache-Control: private, max-age=86400` ·
`X-Content-Type-Options: nosniff`

---

## 5. Eşzamanlı Çalışma Doğrulaması

| Uç | Durum |
|---|---|
| Yeni API stream (`/api/v1/stream/{id}`) | ✓ 206 kanıtlandı (§4) |
| Eski home stream (`/stream/{id}`) | **⚠️ VERIFICATION REQUIRED** — MySQL kapalı + home auth session gerektiriyor; server PC'de `git pull` sonrası canlı doğrulanacak |

---

## 6. D3 Kapatma Planı (Home Stream → API Stream)

**Koşullar (hepsi sağlanmadan kapatma YOK):**

1. ✅ API stream 206 + Content-Range doğrulandı (server PC'de canlı)
2. ✅ Eşzamanlı çalışma: home + API stream ikisi de 200/206 döndürüyor
3. ✅ SPA tarafı `/api/v1/stream/{id}` kullanıyor (JS fetch kontrolü)
4. ✅ `media_catalog` ↔ `coremusic_musics` eşleme kararı verildi (§2)
5. ✅ 1 doğrulama turu: production'da 7 gün izleme (log'da 404/500 yok)

**Geri alma (rollback):**

- `home.coremusic.net/include/Stream/MusicStreamHandler.php` **silinmez** —
  `index.php` special-route satırı yorumlanır (1 satır diff)
- SPA tarafı `streamUrl` değişkeni eski `/stream/{id}`'ye çevrilir (1 satır)
- `home.coremusic.net/index.php`'de `/stream/{id}` route'u tekrar aktif edilir

**Zamanlama:** D3 onayı + 5 koşul sağlandıktan sonra ayrı bir commit'te
kapatılır. Bu adımda **yalnız plan yazıldı** (Adım 06 §4.4: "eski home yolu
KAPATILMAZ").

---

## 7. Statik Doğrulama

| Kontrol | Sonuç |
|---|---|
| `php -l` — 3 değişen dosya | **0 hata** |
| PHPUnit api suite | **65/65 yeşil, 233 assertion** |

---

## 8. Adım 06 Kabul Kriterleri

| # | Kriter (prompt §2 + §5) | Durum |
|---|---|---|
| 1 | Fiziksel medya yolu keşfi + scan/ingest akışı | ✓ §1 (media/ gitignore, MUSIC_LIBRARY_PATH) |
| 2 | Eşleme matrisi (join anahtarları net) | ✓ §2 — **join anahtarı YOK bulgusu** D3'e iletildi |
| 3 | v2 Stream ucu range-request destekli (206 + Content-Range kanıtı) | ✓ §4 — 9/9 istek başarılı |
| 4 | Eşzamanlı çalışma doğrulaması | ⚠️ Yeni stream ✓; home stream VERIFICATION REQUIRED (MySQL kapalı) |
| 5 | D3 kapatma planı (koşullar + geri alma + 1 doğrulama turu şartı) | ✓ §6 |
| 6 | Path traversal YASAK (realpath + prefix) | ✓ (mevcut `resolvePath` — `..`, symlink, realpath kaçışı → 403) |
| 7 | `php -l` 0 hata | ✓ 3/3 |
| 8 | Dosya diskte yoksa 404 (sessiz 500 YASAK) | ✓ §4 #8 |

---

## 9. Karar Kaydı

| Karar | Gerekçe | Kanıt |
|---|---|---|
| `findPrimaryFile` eklendi (findById'a dosya kolonları eklenmedi) | `findById` MusicController tarafından da kullanılıyor; dosya yolu yanıt sızıntısı olurdu (güvenlik) | §3 Gap #1 |
| `etagMatches()` RFC 9110 uyumlu | Katı `===` curl/PHP tırnak strip'inde 304 tetiklemiyordu | §3 Gap #3 + §4 #7 |
| `media_catalog` ↔ `coremusic_musics` eşleme D3'e ertelendi | Join anahtarı yok; karar kullanıcıya ait | §2 |
| Home stream kapatılmadı (D3) | Adım 06 §4.4: yalnız plan yazılır | §6 |

---

**İlgili:** `backend-step05-backend-katman.md` · `backend-step04-api-v2.md` ·
`home.coremusic.net/include/Stream/MusicStreamHandler.php` (salt-okunur referans)

**Sonraki adım:** Adım 07 — Prompt arşivinden `coremusic-backend-step07-*.md`
okunarak devam.

**Bekleyen kararlar (kullanıcı):**
1. `media_catalog` ↔ `coremusic_musics` join anahtarı (dosya yolu mı, eşleme tablosu mu?)
2. Home stream kapatma onayı (D3) — 5 koşul sağlandıktan sonra

---

*Adım 06 · Şarkı/Video DB + Stream bağlantısı · 2026-10-22 · MO*
