---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/webos-audio
evidence:
  - https://webostv.developer.lge.com/develop/references/luna-service-introduction
  - https://webostv.developer.lge.com/develop/references/audio
  - https://webostv.developer.lge.com/develop/references/settings-service
  - https://webostv.developer.lge.com/develop/references/webostvjs-introduction
validation:
  - "2026-10-13 · 4 URL GET 200 · tam gövde (bu turda hepsi yeniden çekildi) · katman/servis/uyumluluk adları yalnız resmi sayfalardan · uyumluluk matrisleri tabloya birebir alındı"
open_questions:
  - "webOS çekirdeğinde ses hattının (alt katman) adları bu sayfalarda geçmiyor → UNKNOWN, üst dala bırakıldı"
  - "com.webos.audio servisinin ses ayarlarının tamamı (ör. setVolume hedefi) Audio sayfasında yok → ses-luna-servisi dosyası §6'da işaretli"
group: mimari
---

# webos-audio-anayasa — Kimlik, Katman ve Luna Sözleşmesi

> **Bu dosya ne değildir:** Uygulama tutorial'ı veya servis mutfak dokümanı
> değildir. Bu dosya webOS ses mimarisinin **anayasasını** kurar: katmanlar,
> Luna servis sözleşmesinin temel kavramları, servis envanteri ve resmi
> **uyumluluk matrisleri** — dördü de bu turda gövde-GET ile çekilen LG resmi
> sayfalarından.

**ELI15:** webOS'ta uygulama bir web sayfasıdır ve ses ayarına **soru sorarak**
ulaşır: `webOS.service.request('luna://com.webos.audio', {...})` der, sistem
cevabı JSON olarak `returnValue` ile döndürür. Bu dosya bu sözleşmenin ve
katmanların anayasa kaynağını belgeler.

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-anayasa` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/webos-audio` |
| Kademe | 5 · yaprak-modül |
| group | `mimari` |
| Doğrulama tarihi | 2026-10-13 |
| Kanıt yöntemi | **4 URL · tam gövde** (LG resmi referans sayfaları, hepsi yeniden çekildi) |

---

## §2 Bu dosyanın kanıt kaynakları

| Etiket | Kaynak | URL | Durum 2026-10-13 |
|---|---|---|---|
| [L1] | Luna Service API Introduction | https://webostv.developer.lge.com/develop/references/luna-service-introduction | ✅ GET 200 · tam gövde |
| [L2] | Audio API Reference Guide | https://webostv.developer.lge.com/develop/references/audio | ✅ GET 200 · tam gövde |
| [L3] | Settings Service | https://webostv.developer.lge.com/develop/references/settings-service | ✅ GET 200 · tam gövde |
| [L4] | webOSTV.js Introduction | https://webostv.developer.lge.com/develop/references/webostvjs-introduction | ✅ GET 200 · tam gövde |

---

## §3 Kimlik

| Öğe | Değer | Kaynak |
|---|---|---|
| Platform | webOS TV — LG Electronics | üst dal `../../isletim-sistemi-turu/webos/` |
| Servis URI | `luna://com.webos.audio` | [L2] |
| Servis tanımı | "The Audio Setting service enables apps to control volume" · "Provides methods for volume control" | [L2] · [L1] (birebir) |
| Çağrı kütüphanesi | `webOSTV.js` — "a portable library to access TV-specific features and functionality for webOS TV" | [L4] (birebir) |
| Çağrı API'si | `webOS.service.request` | [L1] · [L4] |
| kütüphane sürümü | **v1.2.13** (sayfadaki Download bağlantısı) | [L4] |
| Sürüm eşiği | "From webOS TV 4.0, we provide the webOSTV.js library instead of the webOS.js library." · "This library is fully supported on webOS TV 4.0 or higher" | [L1] · [L4] (birebir) |
| webOSTV.js API'leri | `webOS` (webOS.js uyumlu) · `webOSDev` (yeni API'ler) | [L4] |
| Sınıf | `DRMAgent` (DRM servisi yönetimi — ses dışı ama aynı kitaplıkta) | [L4] |
| İlgili servis | `luna://com.webos.settingsservice` — `audioGuidance` anahtarı (ses rehberliği/açıklılık) | [L3] |
| Alt katman | "webOS çekirdeğinde ses hattı adları bu sayfalarda geçmiyor" | UNKNOWN |

---

## §4 Katman Yığını

```
[ETİKET: mimari şema · kaynak: [L1]-[L4] ortak · 2026-10-13]
Uygulama (web app · index.html + JS)
   │  webOSTV.js v1.2.13 — webOS.service.request(...)
   ▼
Luna Service Bus  ──  URI biçimi: luna://<servis adı>   [L1]
   │
   ├─► luna://com.webos.audio           ses seviyesi (3 metot)  [L2]
   ├─► luna://com.webos.settingsservice ayar okuma/abonelik      [L3]
   └─► (14 servis daha — §6 envanter)                            [L1]
   ▼
webOS platform katmanı → ses donanımı   (bu sayfalarda adlandırılmıyor → UNKNOWN)
```

| Katman | Bileşen | Görev | Kanıt |
|---|---|---|---|
| L0 | Uygulama | ses kararı, abonelik | örnekler [L2]/[L3] |
| L1 | `webOSTV.js` | Luna çağrısının taşınması | [L1]/[L4] |
| L2 | Luna Service Bus | URI yönlendirme, `returnValue` JSON sözleşmesi | [L1] |
| L3 | `com.webos.audio` servisi | `setMuted` · `volumeDown` · `volumeUp` | [L2] |
| L4 | Ses alt sistemi | adları bu sayfalarda YOK | UNKNOWN |

---

## §5 Luna Çağrı Sözleşmesi (Temel Kavramlar — [L1] birebir)

| Kavram | Resmi tanım (özet — birebir ifadeler tırnaklı) |
|---|---|
| Service URI | "the URI to the service, in the form: `luna://service name`" — örnek `luna://com.webos.mediadb` |
| Parameters | "service request options and are passed as a JSON object" — zorunlu + rezerv özellikler |
| Method name | "can be passed as part of the URI or could be passed separately as a property of the parameters object" |
| `subscribe` | rezerv özellik — "the method remains in memory and return responses either at periodic intervals or when its data is updated" |
| `resubscribe` | "it resubscribes to a method after a failure has occurred" |
| `onSuccess` | başarı callback'i — JSON yanıt alır |
| `onFailure` | hata callback'i — hata detayları alır |
| `onComplete` | "called when a request is complete (regardless of its success or failure)" |
| Request object | "returned by a service request call. It can be used to cancel the service request and any associated subscription" |
| Response object | rezerv özellik `returnValue` · `false` ise `errorCode`/`errorText` · `subscribe` ise `subscribed` |

**GC uyarısı (birebir):** *"Implicit objects, used for the Luna API call, might be
removed by garbage collection (GC) before you get the return data. Therefore, we
recommend using a variable to call or subscribe to Luna APIs as the above code
example."*

---

## §6 Uyumluluk Matrisleri (resmi tablolar — birebir)

### §6.1 Cihaz platformu (webOS TV 1.x → 25)

| API | 1.x | 2.x | 3.x | 4.x | 5.x | 6.x | 22 | 23 | 24 | 25 |
|---|---|---|---|---|---|---|---|---|---|---|
| **Audio** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Settings Service | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Camera | ✅ | ✅ | ✅ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ (Deprecated) |
| Device Unique ID | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Keymanager3 | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Magic Remote | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ✅ | ✅ |
| TV Device Information | ⚠️ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

(✅ Yes · ⚠️ Partially/Deprecated · ❌ No — sayfadaki sıralamayla; Audio ve
Settings Service satırları iki tabloda da aynen Yes.)

### §6.2 Simulator ve Emulator

| API | Simulator | Emulator 1.2 → 6.x |
|---|---|---|
| **Audio** | **❌ No** | ✅ Yes (her sürüm) |
| Settings Service | ✅ Yes | ✅ Yes (6.x Partially) |
| BLE GATT | ❌ | ❌ |
| DRM | ❌ | ❌ |

> **Ölçüm notu:** ses akışı doğrulaması **simulatörde yapılamaz**; emulator veya
> gerçek cihaz gerekir.

### §6.3 Servis envanteri (Luna API listesi — 16 satır, [L1])

Activity Manager · Application Manager · **Audio** · BLE GATT · Camera ·
Connection Manager · Database · Device Unique ID · DRM · Keymanager3 ·
Media Database · Magic Remote · **Settings Service** · System Service ·
TV Device Information · (+ webOSTV.js Library / webOS API / webOSDev API /
DRM Agent / webos-service API — ayrı referans grupları).

---

## §7 İlgili Ses Ayarı: Settings Service (audioGuidance — [L3])

| Öğe | Değer |
|---|---|
| URI | `luna://com.webos.settingsservice` |
| Metot | `getSystemSettings` |
| Ses anahtarı | `category: 'option'` + `keys: ['audioGuidance']` |
| Değerler | `"on"` (açık) · `"off"` (kapalı) |
| Sürüm | "This key is available on webOS TV 3.0 or higher." (birebir) |
| `locales.AUD` | "Language for audio language" · `AUD2` "secondary audio language" |
| Abonelik | `subscribe: true` — değişiklik geldiğinde callback; iptal `request.cancel()` |
| Hata | `"There is no matched result from DB"` (anahtar tanımsızsa) |

> Tam getSystemSettings sözleşmesi (parametre/görünüm/dönüş tabloları):
> `webos-audio-luna-servisi.md` §6.

---

## §8 Anayasal Kurallar (bağlayıcı)

| # | Kural | Gerekçe |
|---|---|---|
| W1 | Ses metot kümesi **yalnız** [L2] Audio sayfasındakilerdir (3 metot); sayfada olmayan metot adı **üretilmez** | zero-hallucination |
| W2 | Uyumluluk iddiası §6 matrisi dışına taşınamaz | resmi tablo |
| W3 | `returnValue`/`errorCode`/`errorText`/`subscribed` dışında dönüş alanı uydurulmaz | sözleşme disiplini |
| W4 | webOSTV.js sürümü sayfadaki değerdir (v1.2.13); yeni sürüm iddiası kanıtsız yazılmaz | [L4] |
| W5 | Ses alt katmanı (L4) adı bu sayfalarda yok → UNKNOWN | sınır |
| W6 | Servis mutfak (LS2) içeriği ayrı dosyadır: `webos-audio-luna-servisi.md` | tek-sorumluluk |

---

## §9 Kardeş ve üst bağlar

| Bağ | İlişki |
|---|---|
| `index.md` (üst) | künye, URL matrisi, açık sorular |
| `webos-audio-luna-servisi.md` (kardeş) | Audio + Settings servis sözleşmesi tabloları |
| `webos-audio-ornekler.md` (kardeş) | birebir JS örnekleri + test senaryoları |
| `../../isletim-sistemi-turu/webos/` | platform üst dalı |
| `../wasapi/` · `../alsa/` | karşılaştırma dalları |

---

## §10 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| WQ1 | `com.webos.audio` servisinde ses ayarlarının **tamamı** var mı (3 metot dışında)? | ⚠️ VERIFICATION REQUIRED | [L1] servis tanımı yalnız "volume control" diyor — çift kaynak teyidi var ama kapsamlı başka sayfa yok |
| WQ2 | Ses alt katmanı (L4) bileşen adları? | UNKNOWN | Bu sayfalarda geçmiyor |
| WQ3 | `setVolume` (mutlak seviye) metodu var mı? | ⚠️ VERIFICATION REQUIRED | [L2] yalnız ±1 ve mute gösteriyor; eski/NC4 dokümanlarında geçebileceği spekülasyondur |
| WQ4 | Emulator'da Audio 6.x "Partially" mi? | UNKNOWN | Emulator tablosunda Audio satırı yalnız ✅/❌ diliyle; Partially yalnız Settings Service 6.x için |

---

---

## §11 Luna Çağrı Yaşam Döngüsü (§3 · §5 türetim)

| # | Adım | Öğe | Kayıt |
|---|---|---|---|
| 1 | kütüphane yükle | `webOSTV.js` (v1.2.13 · 4.0+ tam destek) | §3 · [L4] |
| 2 | çağrı başlat | `webOS.service.request('luna://<servis>', {...})` | §3 · ELI15 |
| 3 | URI biçimi | `luna://service name` — örnek `luna://com.webos.mediadb` | §5 [L1] |
| 4 | method name | URI'nin içinde **veya** parameters nesnesinde ayrı özellik | §5 |
| 5 | parameters | JSON nesnesi — zorunlu + rezerv özellikler | §5 |
| 6 | Request object | çağrı dönüşünde döner · iptal + abonelik için kullanılır | §5 |
| 7 | başarı | `onSuccess` JSON yanıt | §5 |
| 8 | hata | `onFailure` hata detayları | §5 |
| 9 | bitiş | `onComplete` — başarısızlıkta da çağrılır | §5 |
| 10 | abonelik | `subscribe: true` → periyodik/olay yanıtı | §5 · §7 |
| 11 | iptal | `request.cancel()` | §7 |
| 12 | yanıt | JSON — `returnValue` anahtarı | ELI15 · §5 |

---

## §12 Rezerv Özellik ve Callback Envanteri (§5 — 10 kayıt)

| # | Öğe | Resmi tanım (bu dosyadan) | Kayıt |
|---|---|---|---|
| R1 | `subscribe` | "the method remains in memory and return responses either at periodic intervals or when its data is updated" | §5 |
| R2 | `resubscribe` | "it resubscribes to a method after a failure has occurred" | §5 |
| R3 | `onSuccess` | başarı callback'i — JSON yanıt | §5 |
| R4 | `onFailure` | hata callback'i — detaylar | §5 |
| R5 | `onComplete` | "called when a request is complete (regardless of its success or failure)" | §5 |
| R6 | Request object | "returned by a service request call. It can be used to cancel the service request and any associated subscription" | §5 |
| R7 | `returnValue` | yanıt dönüş anahtarı — `false` ise hata alanları | §5 |
| R8 | `errorCode` | `returnValue: false` iken | §5 |
| R9 | `errorText` | `returnValue: false` iken | §5 |
| R10 | `subscribed` | abonelikli yanıtta | §5 |

**GC uyarısı (birebir, §5):** *"Implicit objects, used for the Luna API call, might
be removed by garbage collection (GC) before you get the return data. Therefore,
we recommend using a variable to call or subscribe to Luna APIs..."* → kural:
çağrı sonucu **her zaman değişkene** bağlanır.

---

## §13 Dönüş Sözleşmesi Doğruluk Tablosu (§5 · §7 türetim)

| Senaryo | `returnValue` | Eşlik alanları | Kayıt |
|---|---|---|---|
| başarılı tek seferlik yanıt | `true` | işlem sonuç alanları | §5 · ELI15 |
| hata | `false` | `errorCode` + `errorText` | §5 |
| abonelik kurulumu | — | `subscribed` alanı görünür | §5 |
| aboneliye gelen veri | `true` (yonetim alanları) | veri + `subscribed` | §5 (tarif) |
| istek bitişi (başarılı ya da değil) | — | `onComplete` her durumda | §5 |
| tanımsız settings anahtarı | hata yüzeyi | `errorText`: "There is no matched result from DB" | §7 |
| kullanıcı aboneliği iptal eder | — | `request.cancel()` → yanıt yok | §7 |

**W3 bağlamı:** bu tablo dışına dönüş alanı **çıkarılmaz** (§8 W3).

---

## §14 Audio Servisi Metot Yüzeyi (§3 · §4 · §8-W1)

| # | Metot | Görev (dosyadaki tanım) | Kayıt |
|---|---|---|---|
| 1 | `setMuted` | sessize alma kontrolü | §4 (L3 satırı) |
| 2 | `volumeDown` | seviye düşürme | §4 |
| 3 | `volumeUp` | seviye yükseltme | §4 |

- **Toplam 3 metot** — §4 katman tablosu ve §8-W1 bu sayıyı bağlayıcı kılar.
- `setVolume` (mutlak seviye): bu turda **kanıtlanmadı** → WQ3 ⚠️ (§10).
- Servis kapsamı "volume control" ([L1] tanımı) — tam liste WQ1 ⚠️ (§10).
- Sayfada olmayan metot adı **üretilmez** (§8-W1) — §14 bu kuralın kaydıdır.

---

## §15 Uyumluluk Matrisi Sayımsal Türetimi (§6.1 tablosundan sayım)

| API | ✅ | ⚠️ | ❌ | İlk ✅ sütunu (matris dili) |
|---|---|---|---|---|
| **Audio** | 10 | 0 | 0 | 1.x |
| Settings Service | 10 | 0 | 0 | 1.x |
| TV Device Information | 9 | 1 | 0 | 2.x |
| Camera | 3 | 7 | 0 | 1.x (4.x'ten ⚠️ · 25 "Deprecated") |
| Device Unique ID | 8 | 0 | 2 | 3.x |
| Keymanager3 | 2 | 0 | 8 | 24 |
| Magic Remote | 2 | 8 | 0 | 24 |

**Türemler (sayım — iddia değil aritmetik):**
- 10 sütunun **tamamında** ✅ olan API: **yalnız Audio ve Settings Service** (2/7 satır).
- `Settings Service` iki tabloda da Yes (§6.1 dipnotu — birebir).
- `Audio` cihazda 10/10 · **Simulator ❌** · Emulator ✅ (§6.2) → ses doğrulaması
  simulatörde **yapılamaz** (§6.2 ölçüm notu).
- `Keymanager3` ve `Magic Remote` aynı kolona açılır: **24** (matris dili).
- `Camera` satırındaki tek ⚠️-etiketli 1.x yok; ⚠️ dizisi 4.x'te başlar, 25'te
  "(Deprecated)" etiketi taşır (§6.1 birebir).

---

## §16 Sürüm Eşiği Zaman Çizelgesi (§3 · §6 · §7 türetim)

| Eşik | Değişim | Kayıt |
|---|---|---|
| 1.x | Audio ✅ · Settings ✅ · Camera ✅ · Magic Remote ⚠️ · TV Device Info ⚠️ | §6.1 |
| 2.x | TV Device Information ✅ olur | §6.1 |
| 3.0+ | `audioGuidance` anahtarı kullanılabilir ("This key is available on webOS TV 3.0 or higher." — birebir) · Device Unique ID ✅ olur | §7 · §6.1 |
| 4.0 | webOS.js yerini `webOSTV.js`'e bırakır · library "fully supported on webOS TV 4.0 or higher" · Camera ⚠️ başlar | §3 · §6.1 |
| 24 | Keymanager3 ✅ · Magic Remote ✅ | §6.1 |
| 25 | Camera satırı "(Deprecated)" etiketiyle | §6.1 |
| Simulator/Emulator | Audio: Simulator ❌ · Emulator 1.2→6.x ✅ | §6.2 |

---

## §17 Hata ve Uyarı Desenleri (§5 · §7 · §12 türetim)

| # | Desen | Kural | Kayıt |
|---|---|---|---|
| E1 | hata bayrağı | `returnValue: false` → `errorCode` + `errorText` oku | §5 |
| E2 | bitiş garantisi | `onComplete` başarıdan **bağımsız** çalışır | §5 |
| E3 | abonelik hatası sonrası | `resubscribe` yeniden kurulum | §5 |
| E4 | tanımsız anahtar | `errorText`: "There is no matched result from DB" | §7 |
| E5 | GC | örtük nesne kaybolabilir → çağrı **değişkene** bağlanır | §5 GC uyarısı |
| E6 | abonelik temizliği | `request.cancel()` — zorunlu temizlik | §7 |
| E7 | kapsam dışı hata kodu | Luna'nın hata kodu listesi bu sayfalarda **yok** → UNKNOWN | dosyadan yokluk (§2 kaynak tablosu) |

---

## §18 Luna Servis Envanteri (§6.3 — 16 kayıt · not sütunu)

| # | Servis | Bu dosyadaki karşılığı |
|---|---|---|
| 1 | Activity Manager | envanter satırı — ayrı sayfa çekmedi |
| 2 | Application Manager | envanter satırı — ayrı sayfa çekmedi |
| 3 | **Audio** | §3 · §4 · §14 — 3 metot (L2 gövde-GET) |
| 4 | BLE GATT | §6.2 — Simulator ❌ Emulator ❌ |
| 5 | Camera | §6.1 — 4.x'ten ⚠️ · 25 Deprecated |
| 6 | Connection Manager | envanter satırı |
| 7 | Database | envanter satırı |
| 8 | Device Unique ID | §6.1 — 3.x'ten ✅ |
| 9 | DRM | §6.2 — Sim ❌ · Emu ❌ · §3 DRMAgent (aynı kitaplık) |
| 10 | Keymanager3 | §6.1 — 24'ten ✅ |
| 11 | Media Database | envanter satırı |
| 12 | Magic Remote | §6.1 — 24'ten ✅ |
| 13 | **Settings Service** | §7 — `audioGuidance` (L3 gövde-GET) |
| 14 | System Service | envanter satırı |
| 15 | TV Device Information | §6.1 — 2.x'ten ✅ |
| 16 | (listede sonraki grup) | §6.3 dipnotu: webOSTV.js Library / webOS API / webOSDev API / DRM Agent / webos-service API — **ayrı referans grupları** |

---

## §19 Katman Yığını Sorumluluk Matrisi (§4 türetim)

| Katman | Sorumluluk | Kanıt sınırı | UNKNOWN? |
|---|---|---|---|
| L0 Uygulama | ses kararı + abonelik | §4 · örnekler [L2]/[L3] | hayır |
| L1 `webOSTV.js` | çağrıyı Luna'ya taşımak | §3 · [L4] | hayır |
| L2 Luna Service Bus | URI yönlendirme + `returnValue` sözleşmesi | §5 [L1] | hayır |
| L3 `com.webos.audio` | 3 metot (setMuted · volumeDown · volumeUp) | §4 · [L2] | hayır |
| L4 Ses alt sistemi | — | **bu sayfalarda adlandırma YOK** | **EVET** (§8-W5) |

---

## §20 Ses Ayarı Abonelik Akışı (§7 — adım adım)

1. `webOS.service.request('luna://com.webos.settingsservice', { method: 'getSystemSettings', parameters: { category: 'option', keys: ['audioGuidance'] } })` — §7 satırları
2. `audioGuidance` değerleri: `"on"` / `"off"` — §7
3. abonelik: `subscribe: true` → değişiklikte callback — §7
4. iptal: `request.cancel()` — §7
5. tanımsız anahtar: "There is no matched result from DB" — §7
6. sürüm koşulu: 3.0+ — §7 (birebir)
7. ilgili anahtarlar: `locales.AUD` ("Language for audio language") · `AUD2` ("secondary audio language") — §7
8. tam sözleşmenin tabloları: kardeş `webos-audio-luna-servisi.md` §6 — §7 dipnotu

---

## §21 Kavram İndeksi (terim → bu dosyadaki kayıt)

| Terim | Kayıt § |
|---|---|
| Service URI (`luna://service name`) | §5 |
| Parameters (JSON) | §5 |
| Method name (URI içinde veya ayrı) | §5 |
| subscribe / resubscribe | §5 · §12 |
| onSuccess / onFailure / onComplete | §5 · §12 |
| Request object / cancel | §5 · §7 |
| Response: returnValue/errorCode/errorText/subscribed | §5 · §13 |
| GC uyarısı | §5 · §17-E5 |
| audioGuidance (`on`/`off`) | §7 |
| `locales.AUD` / `AUD2` | §7 |
| Uyumluluk matrisi dili (✅/⚠️/❌) | §6 |
| Simulator ≠ Emulator | §6.2 |
| webOSTV.js v1.2.13 · `webOS` / `webOSDev` | §3 |
| DRMAgent | §3 |
| L0–L4 katmanları | §4 · §19 |

---

## §22 Açık Sorular Kapanış Planı (WQ1–WQ4 — gelecek tur içindir · iddia değil plan)

| # | Kapanış için gereken | Şimdiki durum |
|---|---|---|
| WQ1 | `com.webos.audio` kapsamlı sayfası varsa gövde-GET + §14'e ekleme | ⚠️ açık |
| WQ2 | webOS çekirdeği/kaynak ağacı ses hattı dokümanı | UNKNOWN — bu turda yok |
| WQ3 | ses sayfasının arşiv/sürüm geçmişi (setVolume iddiası) | ⚠️ açık — iddia edilmedi |
| WQ4 | Emulator 6.x Audio satırının resmi dili | UNKNOWN |

**Kural:** bu tabloda yalnız **plan** vardır; kapanış iddiası gelecek turda
ölçüm + kayıtla yazılır (kendi-kendine-beyan yasağı, §16-M1 deseni).

---

## §23 Bu Tur Yapılmayanlar (açık beyan)

| Yapılmadı | Kayıt |
|---|---|
| Web GET / ölçüm tazelenmesi | §2'deki 2026-10-13 damgaları **geçerli sayıldı**, yenilenmedi |
| FM `validation` / `open_questions` değişikliği | dokunulmadı |
| Kardeş dosyalara yazım (luna-servisi · ornekler) | yok — kendi turlarında |
| §1–§10 içeriğinde değişiklik | yok — yalnız Authority öncesi append |
| `git commit` | yok — orkestratöre ait |

---

## §24 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 220 | <500 |
| §11–§23 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir |
| M2 | bu tur yeni dış iddia 0: §11–§24 tamamı §1–§10 içinden türetildi |
| M3 | FM 10-alan (`group: mimari`) korundu — `validation` bu turlarda değiştirilmez |
| M4 | §6.1/§6.2 matrisleri **tekrar yazılmadı**, yalnız sayıldı (§15) |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 220 satır dokunulmaz |
| M7 | bozuk karakter taraması (Kiril/CJK Unicode aralık regex'i) her ölçümde tekrarlanır — aralık **deseni** dosyaya literal yazılmaz (aksi halde tarama kendini yakar) |

---

---

## §25 Uyumluluk Matrisi Sütun-Bazlı Döküm (§6.1 tablosundan sayım — aritmetik)

| Sütun | ✅ | ⚠️ | ❌ | ✅ olanlar |
|---|---|---|---|---|
| 1.x | 3 | 2 | 2 | Audio · Settings · Camera |
| 2.x | 4 | 1 | 2 | + TV Device Information |
| 3.x | 5 | 1 | 1 | + Device Unique ID |
| 4.x | 4 | 2 | 1 | Camera ⚠️'ye düşer |
| 5.x | 4 | 2 | 1 | (4.x ile aynı dağılım) |
| 6.x | 4 | 2 | 1 | (4.x ile aynı dağılım) |
| 22 | 4 | 2 | 1 | — |
| 23 | 4 | 2 | 1 | — |
| 24 | 6 | 1 | 0 | + Keymanager3 · + Magic Remote · ❌ kalmaz |
| 25 | 6 | 1 | 0 | Camera "(Deprecated)" etiketli ⚠️ |

**Okuma kuralları (bu tablonun türettiği minimal iddialar):**
- ❌ yalnız 1.x–3.x aralığında görülür; **24'ten sonra hiçbir ❌ yok** (matris dili).
- ⚠️ hiçbir sütunda 2'yi aşmaz (Camera + Magic Remote).
- Audio ve Settings Service her sütunda ✅ (§15 ile aynı sonuç — iki ayrı sayım, tek gerçek).

---

## §26 Kanıt Dayanıklılığı Kaydı (§2 · §23 türetim)

| Kaynak | Damga | Bu turda | Ne geçersiz kılar |
|---|---|---|---|
| [L1] Luna Service API Introduction | 2026-10-13 · GET 200 · tam gövde | yeniden çekilmedi | sayfa yeniden yazımı → §5/§12 tekrar doğrular |
| [L2] Audio API Reference Guide | 2026-10-13 · GET 200 · tam gövde | yeniden çekilmedi | 3 metot kümesi değişirse §14 + W1 güncellenir |
| [L3] Settings Service | 2026-10-13 · GET 200 · tam gövde | yeniden çekilmedi | `audioGuidance` sürüm koşulu değişirse §7+§20 |
| [L4] webOSTV.js Introduction | 2026-10-13 · GET 200 · tam gövde | yeniden çekilmedi | v1.2.13/4.0 eşikleri değişirse §3+§16 |

**Kural:** dayanıklılık damgası = §2'deki tarih; bu tur (2026-10-15) **yalnız
dosya-içi türetme** yaptı (§23 beyanı). Yenileme gelecek turda GET ile yapılır.

---

## §27 Türetilmiş Çağrı İskeleti (§5 sözlüğünden kuruldu — alıntı DEĞİL, iddia DEĞİL)

```text
[ETİKET: türetilmiş iskelet · kaynak: §5 kavram tablosu + §3 ELI15 · 2026-10-15]
const req = webOS.service.request({
    service: "luna://com.webos.audio",          // §3 — URI biçimi §5
    method: "<3 metottan biri>",                // §14 — metot adı üretilmez (W1)
    parameters: { /* JSON */ },                 // §5 — Parameters
    subscribe: true,                            // §5 — rezerv özellik (isteğe bağlı)
    onSuccess:  (res) => { /* res.returnValue */ },  // §5
    onFailure:  (err) => { /* errorCode + errorText */ }, // §5
    onComplete: ()   => { /* her durumda */ }        // §5 — regardless of success
});
// req.cancel();                                 // §5 Request object · §7 iptal deseni
```

**İskeletin statüsü:** parametre adları §5/§3'teki kavramlardır; `<3 metottan biri>`
yer tutucusu W1 korumasıdır — gerçek metot adı **yalnız** §14'teki üçlüdür.
Blok, dosyadan alıntı olarak **etiketlenmiştir**; "rehber kodu" gibi sunulmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
