---
node_id: "K015-media/synchronization/synchronization-web-arastirma"
node_type: "module"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "9 kaynak URL'si 2026-10-16'da webfetch ile canlı doğrulandı — 9/9 HTTP 200"
  - "Ana iddialar 2+ kaynak cross-check (§2) — çapraz doğrulama 4/4"
validation: "9/9 URL canlı (2026-10-16); her iddia §2'de CoreMusic yüzeyine bağlandı; bulamayan iddia ⚠️ VERIFICATION REQUIRED (§4)"
open_questions:
  - "CoreMusic'in işaretçi/mekanizması seçimi → Q-4 (kapsam §6) — bu dosya yalnız seçenek kanıtı taşır"
  - "Jellyfin'de kuyruk eşitlemesi 'not working' iddiası tek kaynak → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K015 · synchronization · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Konu: WebDAV/CalDAV eşitleme mekanizmaları,
> çakışma stratejileri, medya sunucusu kuyruk/ilerleme eşitlemesi

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: her satır **iddia × URL × tarih × durum** taşır; iddialar kaynak metninden çıkarılmıştır (uydurma yok).

| # | İddia (kaynak ne diyor) | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | WebDAV Sync (RFC 6578): koleksiyon eşitlemesi için standart — değişiklikler sync-token ile izlenir | https://www.rfc-editor.org/info/rfc6578/ | Tarih: 2026-10-16 | HTTP 200 |
| 2 | CalDAV (RFC 4791): takvim nesneleri ETag ile değişiklik takibine dayanır | https://www.rfc-editor.org/info/rfc4791/ | Tarih: 2026-10-16 | HTTP 200 |
| 3 | RFC 4791 §8.2.1.3 (synchronization process): istemci URI/ETag çiftini önbellekler; ETag değişen kaynak yenilenir; calendar-query + calendar-multiget REPORT ile fark alınır | https://icalendar.org/CalDAV-Access-RFC-4791/8-2-1-3-synchronization-process.html | Tarih: 2026-10-16 | HTTP 200 |
| 4 | DAVx⁵ teknik dokümanı: iki algoritma (PROPFIND/REPORT vs Collection Synchronization); çakışmada **"sunucu her zaman kazanır"** — yerel değişiklikler atılır, kullanıcıya sorulmaz | https://manual.davx5.com/technical_information | Tarih: 2026-10-16 | HTTP 200 |
| 5 | CRDT (Wikipedia): LWW-Element-Set son-yazan-kazanır küme tipidir; state-based CRDT merge işlevi komütatif/assosiyatif/idepotent olmalıdır; endüstride Redis, Riak, Apple Notes, Figma (LWW benzeri) kullanır | https://en.wikipedia.org/wiki/Conflict-free_replicated_data_type | Tarih: 2026-10-16 | HTTP 200 |
| 6 | Navidrome Subsonic API dokümanı: OpenSubsonic uzantıları (playbackReport vb.) sunucu tarafında oynatma durumu/ilerleme raporlaması tanımlar | https://www.navidrome.org/docs/developers/subsonic-api/ | Tarih: 2026-10-16 | HTTP 200 |
| 7 | Navidrome PR #5442 (merged 2026-05-01): playbackReport uzantısı — starting/playing/paused/stopped durum makinesi, positionMs (milisaniye hassasiyeti), otomatik scrobble eşiği (%50 süre veya 4 dk) | https://github.com/navidrome/navidrome/pull/5442 | Tarih: 2026-10-16 | HTTP 200 |
| 8 | Feishin PR #1335 (merged 2025-12-13): Navidrome/Subsonic için çalma kuyruğu eşitlemesi ("sync play queue") — commit notu: Navidrome'da çalışıyor, Jellyfin'de "not working" | https://github.com/jeffvli/feishin/pull/1335 | Tarih: 2026-10-16 | HTTP 200 |
| 9 | Radicale v3: açık kaynak CalDAV/CardDAV sunucu; depolama seçeneği `max_sync_token_age` (varsayılan 2592000 sn) ile sync-token'lar yaşlandırılır | https://radicale.org/v3.html | Tarih: 2026-10-16 | HTTP 200 |

**Sayım:** 9 URL · 9/9 canlı (2026-10-16) · ölü URL: 0 · `⚠️ VERIFICATION REQUIRED`: §4'te kayıtlı 2 kalem.

---

## §2 ÇAPRAZ DOĞRULAMA (ana iddialar ≥2 kaynak)

| # | Ana iddia | Kaynak 1 | Kaynak 2 (+) | Sonuç |
|---|---|---|---|---|
| C-1 | Değişiklik takibi işaretçi (ETag/sync-token) ile yapılır — endüstri standardı | RFC 4791 (kaynak 2) + §8.2.1.3 süreç (kaynak 3) | RFC 6578 (kaynak 1) · DAVx⁵ (kaynak 4) · Radicale sync-token (kaynak 9) | ✅ 5 kaynak — çapraz doğrulandı |
| C-2 | Çakışma stratejisi tek değildir: "sunucu kazanır" ile "son-yazan-kazanır" farklıdır | DAVx⁵ "server always wins" (kaynak 4) | CRDT LWW-Element-Set (kaynak 5) | ✅ 2 kaynak — **ikisi rakip strateji; CoreMusic seçimi UNKNOWN (kapsam Q-2)** |
| C-3 | Oynatma kuyruğu/ilerleme eşitlemesi gerçek müzik sunucu-istemcilerinde vardır | Navidrome playbackReport (kaynak 7) | Feishin sync play queue (kaynak 8) · Navidrome API docs (kaynak 6) | ✅ 3 kaynak |
| C-4 | Oynatma durumu bir durum makinesidir (starting/playing/paused/stopped) + konum raporlaması | Navidrome PR #5442 (kaynak 7) | Navidrome Subsonic API docs (kaynak 6) — getNowPlaying/uzantı listesi | ✅ 2 kaynak (docs, PR'daki alan adlarını birebir taşımaz → alan adları için tek kaynak: PR) |

---

## §3 KOD XREF (kaynak → K015 yüzeyi)

| # | Kaynak iddiası | K015 karşılığı | Dosya·Bölüm / durum |
|---|---|---|---|
| 1 | sync-token tabanlı koleksiyon eşitleme | fark tespit adımı (kapsam §4 adım 2) | `synchronization-kapsam.md` §4 — işaretçi biçimi Q-4 (seçim yapılmadı) |
| 2+3 | ETag önbellek karşılaştırması + multiget ile fark indirme | tespit katmanı tasarımı | kapsam §4 adım 2-3 — mekanizma **TASARIM İDDİASI**, seçim Q-4 |
| 4 | Sunucu kazanır çakışma politikası + gizli arka plan eşitlemesi | E-3 hata akışı | kapsam §5 E-3 — **strateji UNKNOWN (Q-2)**; DAVx⁵ yaklaşımı bir **adaydır**, karar değil |
| 5 | LWW / CRDT merge özellikleri | fusion (birleştirme) tasarımının teorik zemini | kapsam §1-4 — "son-yazan-kazanır mı" media-master §9.10'da UNKNOWN |
| 6+7 | Durum makinesi + positionMs + otomatik scrobble eşiği | eşitlenecek alan kümesi (kapsam §1-2b) | alan kümesi TASARIM İDDİASI — Navidrome eşiği (%50 / 4 dk) **referans veri, CoreMusic eşiği değil** |
| 8 | Çalma kuyruğu eşitlemesi Navidrome'da çalışıyor | kuyruk durumu (kapsam §1-2c) | Jellyfin "not working" notu tek kaynak → §4-W2 |
| 9 | sync-token yaşlandırma (varsayılan 30 gün) | E-1/E-6 arka planı: bayat token → yeniden tespit | kapsam §3 `error → detecting` — CoreMusic üst sınırı Q-6 (UNKNOWN) |

---

## §4 NOTLAR VE ⚠️ KAYITLAR

| # | Not / eksik kanıt | Etiket |
|---|---|---|
| W-1 | CoreMusic'in kendi eşitleme protokolü/mechanizması vault'ta **tanımlı değil** — bu dosya yalnız harici seçenek kanıtı taşır; seçim Q-4 (kapsam §6) | ⚠️ VERIFICATION REQUIRED |
| W-2 | "Jellyfin'de kuyruk eşitlemesi çalışmıyor" iddiası **tek kaynaklıdır** (Feishin PR commit notu, kaynak 8) — ikinci kaynak bulunamadı | ⚠️ VERIFICATION REQUIRED |
| W-3 | Radicale `max_sync_token_age` varsayılanı (2592000 sn = 30 gün) tek kaynak (kaynak 9) — ikinci bağımsız kaynak aranmadı | tek kaynak — cross-check yok |
| W-4 | K013-integraion tarafındaki "synchronization" ne içeriyor bu dosyada **araştırılmadı** (kapsam §2-1 · §6-Q3) — ad çakışması §7/3 açık kalır | ⚠️ VERIFICATION REQUIRED |
| W-5 | PESQ/POLQA benzeri kalite ölçümü bu konu dışıdır (media-testing dosyasında işlenir) | kapsam dışı — bkz media-testing |

---

## §5 AÇIK KONULAR

| # | Konu | Etiket |
|---|---|---|
| SW-1 | İşaretçi mekanizması seçimi (ETag / sync-token / damga) | Q-4 — bu dosya karar **vermez**, yalnız kanıt taşır |
| SW-2 | Çakışma stratejisi kararı | Q-2 / media-master §9.10 UNKNOWN — Vault Steward |
| SW-3 | §7/3 ad çakışması (K013 vs K015) | ⚠️ VERIFICATION REQUIRED — bu dosyada **kapatılmaz** |

---

## §6 YENİDEN ERİŞİM / YENİLEME KURALI

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Yenileme tetiği | §1'deki bir durum/tarih eskiyorsa (kanıt tazeleme turu, Q-2/Q-4 kararı) URL yeniden fetch edilir |
| 2 | Tarih güncelleme | Fetch edilen satırın tarihi gerçek erişim gününe çekilir; fetch edilmeyen eskisinde kalır |
| 3 | Yeni URL ekleme | Yalnız webfetch ile o gün doğrulanırsa §1 tablosuna satır eklenir; uydurma URL yasak |
| 4 | Ölü kaynak | 404/410 dönerse satır silinmez — durum `DEAD (erişim: YYYY-MM-DD)` olur ve §5'e kayıt açılır |
| 5 | Kapsam sınırı | Yenileme yalnız §1'deki 9 URL'yi kapsar; media-testing dosyasının URL'leri burada yenilenmez |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
