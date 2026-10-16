---
node_id: "K015-media/offline/offline-web-arastirma"
node_type: "module"
status: "verify-required"
parent_id: "K015-media/offline"
children: []
depends_on: ["K015-media/media-master"]
evidence:
  - "11 URL 2026-10-16'da canlı doğrulandı (hepsi HTTP 200)"
  - "web.dev/the-offline-cookbook 404 idi → doğru adres web.dev/articles/offline-cookbook (düzeltildi, 2026-10-16 — bu kardeş dosyaya tahsis edildi)"
validation: "11/11 URL canlı (2026-10-16); §2a'daki her ana iddia ≥2 kaynakla çaprazlandı; uydurma eşik/URL YOK — bulunamayanlar §5'te ⚠️"
open_questions:
  - "Çevrimdışı sahipliği (K015/K014/K012) → ⚠️ VERIFICATION REQUIRED (media-master §7/2) — web kanıtıyla KAPATILMAZ"
  - "Tazelik (stale) eşiği / TTL → ⚠️ UNKNOWN (kanıt yok — §5)"
group: kanit
---

# K015-media · offline · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam: çevrimdışı kullanılabilirlik
> (Service Worker · Cache Storage · bağlantı durumu) kaynakları

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: her satır **iddia (kaynak ne diyor) × URL × tarih × durum** taşır. Bu dosya yalnız aşağıdaki
> 11 URL'yi taşır; kardeş dosyaların (downloads · media-cache) URL'leri buraya kopyalanmaz.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-16 | Durum |
|---|---|---|---|---|
| O-1 | Offline Cookbook: çevrimdışı çalışan uygulamalar için servis çalışanı + önbellek strateji kataloğu | https://web.dev/articles/offline-cookbook | Tarih: 2026-10-16 | HTTP 200 |
| O-2 | Service workers ve Cache Storage ile çevrimdışı deneyim kurulumu | https://web.dev/articles/service-workers-cache-storage | Tarih: 2026-10-16 | HTTP 200 |
| O-3 | Service Worker API: arka planda çalışan, ağdan bağımsız davranışı etkinleştiren betik | https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API | Tarih: 2026-10-16 | HTTP 200 |
| O-4 | ServiceWorkerContainer: sayfanın servis çalışanıyla ilişki kuran arayüz (register · getRegistration · ready) | https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerContainer | Tarih: 2026-10-16 | HTTP 200 |
| O-5 | CacheStorage: servis çalışanları için tasarlanmış, istek/yanıt çiftlerini saklayan depo (open · has · delete) | https://developer.mozilla.org/en-US/docs/Web/API/CacheStorage | Tarih: 2026-10-16 | HTTP 200 |
| O-6 | Cache.match: önbellekteki kayıtlar arasında isteğe uyan ilk yanıtı bulur | https://developer.mozilla.org/en-US/docs/Web/API/Cache/match | Tarih: 2026-10-16 | HTTP 200 |
| O-7 | navigator.onLine: çevrimiçi olma durumunu bildirir (true/false) | https://developer.mozilla.org/en-US/docs/Web/API/Navigator/onLine | Tarih: 2026-10-16 | HTTP 200 |
| O-8 | online olayı: tarayıcı çevrimiçi hâle geldiğinde pencerede tetiklenir | https://developer.mozilla.org/en-US/docs/Web/API/Window/online_event | Tarih: 2026-10-16 | HTTP 200 |
| O-9 | Network Information API: cihazın bağlantı türü/hızı hakkında bilgi sunar | https://developer.mozilla.org/en-US/docs/Web/API/Network_Information_API | Tarih: 2026-10-16 | HTTP 200 |
| O-10 | Background Synchronization: çevrimdışıyken alınan isteklerin bağlantı dönünce sunucuya gönderilmesine izin verir | https://developer.mozilla.org/en-US/docs/Web/API/Background_synchronization | Tarih: 2026-10-16 | HTTP 200 |
| O-11 | IndexedDB: büyük miktarda veriyi yapılandırılmış olarak saklayan düşük seviyeli API | https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API | Tarih: 2026-10-16 | HTTP 200 |

---

## §2 ÇAPRAZ DOĞRULAMA (her ana iddia ≥2 kaynak)

### §2a Ana iddialar (hepsi ≥2 kaynak)

| # | Ana iddia (CoreMusic çevrimdışı tasarımı için) | Kaynak 1 | Kaynak 2 (+) | Sonuç |
|---|---|---|---|---|
| A-1 | **Çevrimdışı kabuk = Service Worker + Cache Storage:** servis çalışanı, ağı yakalar; gerekli yanıtlar önbelleğe alınır → ağ yokken sunulur | O-1 (Offline Cookbook) | O-2 (SW + Cache Storage kurulumu) + O-3 (Service Worker API) | ✅ 3 kaynak — çapraz yeterli |
| A-2 | **Bağlantı durumu gözlemi:** `navigator.onLine` çevrimiçilik bilgisini verir; bağlantı döndüğünde `online` olayı tetiklenir → kapsam §3 `offline → offline-synced` geçişinin dış olayı | O-7 (navigator.onLine) | O-8 (online olayı) | ✅ 2 kaynak |
| A-3 | **Hazır yanıtı bulma:** `Cache.match` önbellekte isteğe uyan yanıtı döndürür; depo `CacheStorage` üzerinden açılır/silinir → kapsam §1a-1 "hazır varlık" sorgusu | O-6 (Cache.match) | O-5 (CacheStorage) + O-2 (kurulum) | ✅ 3 kaynak |
| A-4 | **Kayıt/yaşam döngüsü:** sayfa servis çalışanısını `ServiceWorkerContainer` üzerinden kaydeder; container hazır kaydı sunar → çevrimdışı kabuğun kurulum adımı | O-4 (ServiceWorkerContainer) | O-3 (Service Worker API) | ✅ 2 kaynak |

### §2b Destekleyici iddialar (tek kaynak — ⚠️ ana iddia sayılmaz)

| # | İddia | Kaynak | Not |
|---|---|---|---|
| A-5 | Bekleyen senkron istekleri bağlantı dönünce gönderme | O-10 (Background Synchronization) | ⚠️ tek kaynak — bu dosyada ana iddia yapılmaz; §5 W-1 |
| A-6 | Bağlantı türü/hızı bilgisi (degrade kararına girdi) | O-9 (Network Information API) | ⚠️ tek kaynak — §5 W-2; `online-degraded` eşiği UYDURULMAZ |
| A-7 | Yapılandırılmış yerel depo (envanter kaydı) | O-11 (IndexedDB) | ⚠️ tek kaynak — §5 W-3; kalıcılık K007 sınırı (kapsam §2/4) |

---

## §3 K015/offline KARŞILIĞI (iddia → yüzey → dosya·satır)

| # | İddia | CoreMusic yüzeyi | Bağ (Dosya·Satır / durum) |
|---|---|---|---|
| 1 | A-1 çevrimdışı kabuk (SW + cache) | kapsam §1a-1 hazır varlık · §3 S-3 `offline` | `offline-kapsam.md` §1a-1 · §3/§3a — **uygulama yok, tasarım iddiası** |
| 2 | A-2 bağlantı durumu gözlemi | kapsam §3 `online/online-degraded/offline` geçişleri · §5 H-1/H-2 | `offline-kapsam.md` §3 · §5 H-1, H-2 |
| 3 | A-3 hazır yanıtı bulma | kapsam §1a-1 · §5 H-3 (`stale` işareti) | `offline-kapsam.md` §1a-1 · §3a · §5 H-3 |
| 4 | A-4 kayıt/yaşam döngüsü | kapsam §1a-4 (hazırlık el sıkışması) | `offline-kapsam.md` §1a-4 — kurulum tasarımı **TASARIM İDDİASI** |

> **Sahiplik sınırı (kapsam §2/2 ile aynı):** bu dosya çevrimdışı **sahipliğini** çözmez —
> media-master §7/2 ⚠️ durur; web kanıtı (O-1..O-11) yalnız **teknik mekanizmayı** belgeler,
> düğüm sınırını **kapatmaz**.

---

## §4 ERİŞİM KAYDI (hangi URL ne zaman doğrulandı)

| Erişim olayı | Tarih | Kapsam | Sonuç |
|---|---|---|---|
| Tur-1 — 404 düzeltmesi | 2026-10-16 | aday | `web.dev/the-offline-cookbook` **404** → `web.dev/articles/offline-cookbook` (O-1) — bu dosyaya tahsis edildi |
| Tur-1 — doğrulama | 2026-10-16 | §1 tablosu (O-1..O-11) | **11/11 HTTP 200** |
| Tur-1 — 404 eleme | 2026-10-16 | adaylar | `developer.chrome.com/docs/workbox/background-sync`, `w3c.github.io/BackgroundSynchronization/`, `MDN Periodic_Background_Sync_API` (404/yetersiz) → havuza alınmadı |

| Erişim Kuralı | |
|---|---|
| Yenileme | bir satırın durumu eskiyse URL yeniden fetch edilir; edilmeyenin tarihi **eskisinde kalır** |
| Ölü kaynak | 404/410 → satır silinmez, durum `DEAD (erişim: YYYY-MM-DD)` olur + §5'e W-kaydı açılır |
| Yeni URL | §1'e yalnız o gün doğrulanarak eklenir; kardeş dosya URL'leri tekrarlanmaz |

---

## §5 AÇIK KONULAR (⚠️ — bulunamayan / kanıt bekleyen)

| # | Konu | Etiket |
|---|---|---|
| W-1 | Background Sync'in CoreMusic zincirine girişi + **ikinci kaynak** | ⚠️ VERIFICATION REQUIRED — O-10 tek kaynak; eşik **uydurulmaz** |
| W-2 | `online-degraded` tanımı/hız eşiği için ikinci kaynak | ⚠️ BULUNAMADI — O-9 tek kaynak; hiçbir hız/latansı sayısı bu dosyada yazılmadı |
| W-3 | Yerel depo aracının (IndexedDB?) seçimi + K007 kalıcılık sınırı | ⚠️ VERIFICATION REQUIRED — O-11 tek kaynak; araç seçimi tasarım kararı |
| W-4 | Tazelik (stale) eşiği / TTL | ⚠️ UNKNOWN — kapsam §6/3 ile aynı kayıt; kanıt yok |
| W-5 | Çevrimdışı sahipliği (K015/K014/K012) | ⚠️ VERIFICATION REQUIRED (media-master §7/2) — **web ile kapatılmaz**, üst karar bekler |

---

## §6 KARDEŞ DOSYA İLİŞKİSİ (bu dosyanın yeri)

| Kardeş | İlişki | Kanıt yönü |
|---|---|---|
| `offline-kapsam.md` | §2a iddiaları kapsam §1a/§3/§5'teki yüzeylere bağlandı | kanıt → kapsam |
| `offline-ornekler.md` | A-1/A-2/A-3 kodda nasıl görünür (SW+cache sarmalayıcı, online olayı) | kanıt → örnek |
| `offline/index.md` | §GRUPLAR + FM açık sorular bu dosyada derinleştirilir (W-5) | kanıt → index |
| `../downloads/*` · `../media-cache/*` | URL havuzları **ayırt edilmiştir** (tekrar yok; O-1 downloads'ın 404 düzeltmesinden devralındı) | havuz → kardeş |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
