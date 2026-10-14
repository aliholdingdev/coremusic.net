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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
