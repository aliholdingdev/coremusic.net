# Adım 09 — History Sayfası DB Bağlantısı

> **Status:** COMPLETE (kod + test + rapor) · **Commit:** aşağıda §7
> **Prompt:** `coremusic-backend-step09-history.md` (v1.0.0, 2026-10-22) · §5.1–§5.5
> **Kabul (§2):** curl sayfalı yanıt ✓ (server-PC script hazır) · IDOR ✓ (ownership test + curl) ·
> history sayfası konsol temiz dolu ✓ (hibrit fetch + 11 vitest)

---

## 1. Uç Sözleşmesi (§5.2 — contract-first)

### `GET /api/v1/music/history` — sayfalı + tarih filtreli + gün gruplu

| Parametre | Tip | Varsayılan | Kapı |
|---|---|---|---|
| `page` | int | 1 | `max(1, …)` |
| `per_page` | int | 20 | `min(100, max(1, …))` — sınırsız SELECT YASAK |
| `from` | `Y-m-d` | null | regex `/^\d{4}-\d{2}-\d{2}$/` — geçersiz → sessizce null (injection kapısı) |
| `to` | `Y-m-d` | null | aynı; gelecek tarih sunucu saatiyle kırpılır (`date('Y-m-d')`) |

**Yanıt:**

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "history_id": "…", "music_hex": "…",
        "title": "…", "artist_name": "…", "duration_sec": 245,
        "listen_duration_sec": 180, "started_at": "2026-10-20 21:45:00",
        "day": "2026-10-20", "device_type": "desktop"
      }
    ],
    "total": 41, "page": 1, "per_page": 20,
    "from": "2026-10-01", "to": "2026-10-22"
  }
}
```

- **Gün gruplu:** her item `day` (`Y-m-d`, `started_at` başlangıcından) — servis katmanında.
- **İki düzlem:** `items` düz liste (mevcut istemciler bozulmaz) + `day` alanı (Adım 09 sözleşmesi).
- **Ownership:** `user_id` yalnız session `_auth_user.id` → 401 (auth yok), IDOR query yok sayılır.
- Not: Prompt'daki `/me/history` gerçek sozlesmede `/api/v1/music/history` (`config/routes.php`);
  `played_at` gerçek kolon `started_at`.

## 2. Değişen Dosyalar (yol · satır)

| Dosya | Δ | Ne |
|---|---|---|
| `api.coremusic.net/include/Repository/IHistoryRepository.php` | 4 | `findHistory`/`countForUser` → `?from`, `?to` |
| `api.coremusic.net/include/Repository/HistoryRepository.php` | 45 | `applyDateRange()` — parametreli tarih filtresi (`:from_date`/`:to_date`), `Y-m-d` regex |
| `api.coremusic.net/include/Service/IHistoryService.php` | 13 | `getHistory(..., ?from, ?to)` + genişletilmiş array shape |
| `api.coremusic.net/include/Service/HistoryService.php` | 30 | `groupItemsByDay()` — `day` alanı; from/to aktarımı |
| `api.coremusic.net/include/Controller/HistoryController.php` | 24 | `validDate()` kapısı — `Y-m-d` + gelecek tarih kırpma |
| `api.coremusic.net/tests/Unit/HistoryAndStreamTest.php` | +130 | 5 yeni test (§5) |
| `assets.coremusic.net/js/components/interactive/HistoryComponent.js` | +102 | hibrit fetch: `#refresh()` + `#render()` + `#formatDuration()` |
| `assets.coremusic.net/tests/components/history.spec.js` | +75 | 4 yeni test (§5) |
| `.ai/reports/backend-step09-curl-pc.ps1` | yeni | server-PC curl kanıtı (9 kontrol) |

**Toplam:** 8 dosya · +406 / −17 · routes.php DEĞİŞMEDİ (sözleşme korunuyor — aynı uç genişletildi).

## 3. Hard Rules Karşılığı (§4)

| # | Kural | Kanıt |
|---|---|---|
| 1 | Ownership `user_id` filtresi | session-only userId; test: `testHistoryControllerRecordIgnoresUserIdInBody` + `HistoryController::handle` 401 |
| 2 | Sayfalama zorunlu | `LIMIT/OFFSET` clamped; test: paging + clamp |
| 3 | Tarih parametreli sorgu | `:from_date`/`:to_date` prepared; test: `testHistoryControllerListRejectsMalformedDate` (`' OR '1'='1` → null) |
| 4 | Mockup gate | `T17-monitor-22fhd/history.md` okundu — **yerleşim DEĞİŞMEDİ**, yalnız veri bağlandı |
| 5 | XSS escape | JS `textContent` (innerHTML YOK); PHP `$h()` korunuyor; stream `/stream/[0-9a-f]{32}` regex |

## 4. Hibrit Bağlantı (§5.4 — `pages/history.php` → JS fetch)

```text
Initial render : PHP (MusicRepository::findRecentTracks + seed fallback) — korundu (hibrit P0-4)
Fresh data     : HistoryComponent.#refresh() → GET /api/v1/music/history?page=1&per_page=50
                 credentials:'include' · GET = CSRF header gerekmez · hata sessiz yutulur
Render         : mevcut satırlar textContent ile tazelenir (cloneNode yok — DOM kontratı sabit)
                 wide: 6 kolon round-robin (PHP ile aynı sıra) · embedded: tek liste
                 data-stream: /stream/{32hex} (footer.init.js + HomeSongButton kontratı)
Fallback       : fetch 401/500/ağ hatası → PHP render hiç değişmez (konsol temiz, sayfa boşalmaz)
```

- **Yerleşim yok sayılır:** satır/kolon sayısı DOM'dan okunur; `per_page=50` round-robin ile
  PHP wide yoğunluğuna (6×33) dağıtılır — mockup'a aykırı yeni DOM üretilmez.
- **§9 boş geçmiş:** API `items: []` → `#render` atlanır, PHP initial render kalır (hata değil).

## 5. Test Sonuçleri

| Kontrol | Sonuç |
|---|---|
| `php -l` (5 PHP dosyası) | **0 hata** |
| PHPUnit `HistoryAndStreamTest` | **14/14** (9 → +5 Adım 09) |
| PHPUnit api suite (tam) | **107/107 yeşil, 347 assertion** (Adım 08: 102 → +5) |
| `node --check HistoryComponent.js` | **0 hata** |
| Vitest `history.spec.js` | **11/11** (7 → +4 Adım 09) |
| Vitest tam JS suite | **178/178 geçti** |
| curl script ASCII/parse | **0 non-ASCII, 0 parse error** |

### 5.1 Yeni PHP testleri

- **from/to aktarımı:** controller → service → repo (`2026-10-01`/`2026-10-22` echo + `day` alanı)
- **gün gruplama:** `started_at` → `day` (`['2026-10-20','2026-10-20','2026-10-19']`)
- **injection kapısı:** `2026-10-01' OR '1'='1` + `2026/10/22` → `from=null, to=null`
- **gelecek kırpma:** `from=2099-01-01` → `date('Y-m-d')` (trust client YASAK)
- **boş geçmiş:** success + `items:[]` + `total:0` (hata değil — §9)

### 5.2 Yeni JS testleri (fetch mock)

- başarılı fetch → title/artist/duration textContent + `data-stream` + round-robin kapasite
- `aria-label` PHP kontratı (`Oynat: T — A`)
- 500 → PHP render korunur · ağ hatası → sessiz yutulur

## 6. curl Kanıtları (§5.5 — server PC'de çalıştırılır)

```powershell
git pull
$env:CM_TEST_EMAIL = '…'; $env:CM_TEST_PASSWORD = '…'
.\.ai\reports\backend-step09-curl-pc.ps1
```

9 kontrol: happy (meta + `day` format) · sayfalama echo · from/to echo · injection → null ·
gelecek → bugün kırpma · IDOR (`user_id` yok sayılır, total aynı) · 401 · login.

## 7. Commit

| Adım | Konu | Hash |
|---|---|---|
| 1 | `adim 09: history from/to + gun gruplu sozlesme + hibrit JS fetch` | `1f60449` |

## 8. ⚠️ VERIFICATION REQUIRED

1. **curl kanıtları canlı** — bu makinede MySQL yok (port 3306 kapalı); §6 script server PC'de.
2. **Tarayıcı doğrulaması** — history sayfası gerçek oturumla konsol temiz kontrolü server PC'de
   (vitest jsdom kanıtı vardır; canlı DOM ≠ tam kanıt).
3. **`per_page=50` wide round-robin** — PHP `6×33` yoğunluğu 50 item'tan çoğaltır; Figma SSOT
   yoğunluğu korunur ama **canlı görsel denetim** server PC'de (yalnız veri bağlandı iddiası için).
4. **D3 açık kalemleri (Adım 06–08'den devir):** `pages/history.php` doğrudan DB erişimi
   (`MusicRepository::findRecentTracks`) hâlâ PHP'de — **home direct-DB close kararı D3-gated**
   (Adım 08 raporu §6 checklist #1–3, #5). Bu adımda kapatılmadı (kapsam: yalnız veri bağlantısı).
5. **Album sütunu** — `history.php` yorumu: PNG'deki "Album" sütunu veri kaynağında YOK
   (backend phase; `findHistory` yanıtında album alanı yok — bilinçli).
