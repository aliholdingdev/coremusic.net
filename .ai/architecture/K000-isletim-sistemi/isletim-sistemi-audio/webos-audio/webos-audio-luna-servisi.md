---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-luna-servisi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-anayasa
evidence:
  - https://webostv.developer.lge.com/develop/references/audio
  - https://webostv.developer.lge.com/develop/references/settings-service
  - https://webostv.developer.lge.com/develop/references/luna-service-introduction
validation:
  - "2026-10-13 · 3 URL GET 200 · tam gövde · metot/parametre/dönüş/hata tabloları sayfadan birebir alındı · taşma/ekleme yok · Audio metot kümesi iki bağımsız sayfada (Audio + Luna Intro) aynı: setMuted/volumeDown/volumeUp"
open_questions:
  - "com.webos.audio servisinde 3 metot dışında ayar metodu var mı (ör. mutlak setVolume) → ⚠️ VERIFICATION REQUIRED (iki sayfa da yalnız 'volume control' diyor)"
  - "Hata kodu 1/ERROR_3 dışındaki kodların tam listesi → UNKNOWN (sayfada yalnız metot-bazlı referanslar var)"
group: mimari
---

# webos-audio-luna-servisi — Luna Ses ve Ayar Servis Sözleşmeleri

**ELI15:** Bu dosya LG'nin resmi sayfalarının **birebir masa kopyasıdır**: hangi
servis, hangi metot, parametre ne, dönüş ne, hata kodu nedir — hepsi kaynak
sayfadan aynen alınmıştır. `luna://com.webos.audio` sesi yönetir;
`luna://com.webos.settingsservice` ses rehberliği (`audioGuidance`) gibi ayarları
okur.

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-luna-servisi` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/webos-audio` |
| Kademe | 5 · yaprak-modül |
| group | `mimari` |
| Doğrulama tarihi | 2026-10-13 |

## §1.1 Kaynaklar

| Etiket | Kaynak | URL | Durum |
|---|---|---|---|
| [A] | Audio API Reference Guide | https://webostv.developer.lge.com/develop/references/audio | ✅ GET 200 · tam gövde |
| [S] | Settings Service | https://webostv.developer.lge.com/develop/references/settings-service | ✅ GET 200 · tam gövde |
| [I] | Luna Service API Introduction | https://webostv.developer.lge.com/develop/references/luna-service-introduction | ✅ GET 200 · tam gövde |

---

## §2 `luna://com.webos.audio` — Servis Genel Görünümü

| Alan | Değer (birebir) |
|---|---|
| URI | `luna://com.webos.audio` |
| Tanım | "Provides methods for volume control. The Audio Setting service enables apps to control volume, with the following methods." |
| Metot sayısı | **3** |
| Emulator desteği | üç metotta da "Supported in Emulator: **Yes**" |
| Ortak başarı dönüşü | `{'returnValue': true}` |
| Ortak hata alanları | `errorCode` (Optional Number) · `errorText` (Optional String) — "returns errorCode only if it fails" |

| Metot | Açıklama (birebir) | Parametre |
|---|---|---|
| `setMuted` | "Mutes or unmutes the volume." | `muted` (Boolean, Required) |
| `volumeDown` | "Decreases the volume by 1." | None |
| `volumeUp` | "Increases the volume by 1." | None |

> Çift kaynak teyidi: [I] Luna Intro servis listesinde Audio tanımı da aynı:
> "Provides methods for volume control." — **metot kümesi iki sayfada birleşiyor**.

---

## §3 `setMuted` Sözleşmesi ([A] birebir)

### §3.1 Parametre

| Alan | Zorunlu | Tip | Açıklama |
|---|---|---|---|
| `muted` | Required | Boolean | "The flag that indicates whether to mute the volume or unmute the volume." · `true`: mute · `false`: unmute |

### §3.2 Dönüş

| Alan | Zorunlu | Tip | Açıklama |
|---|---|---|---|
| `returnValue` | Required | Boolean | true: Success · false: Failure |
| `errorCode` | Optional | Number | yalnız hatada |
| `errorText` | Optional | String | yalnız hatada |

### §3.3 Hata referansı

| Kod | Mesaj |
|---|---|
| `1` | **"Not a valid JSON message"** |

### §3.4 Dönüş örneği

```json
[ETİKET: birebir alıntı · kaynak: [A] Return example]
{
  'returnValue': true
}
```

---

## §4 `volumeDown` / `volumeUp` Sözleşmesi ([A] birebir)

| Alan | `volumeDown` | `volumeUp` |
|---|---|---|
| Açıklama | "Decreases the volume by 1." | "Increases the volume by 1." |
| Parametre | **None** | **None** |
| Dönüş | `returnValue` Required Boolean (+ opsiyonel `errorCode`/`errorText`) | aynı |
| Hata referansı | `ERROR_3` — **"Parameters should be empty"** | `ERROR_3` — aynı |
| Emulator | Yes | Yes |
| See also | `setMuted` · `volumeUp` | `setMuted` · `volumeDown` |

> **Örnek kod gözlemi (resmi sayfanın kendisi):** `setMuted` ve `volumeDown`
> örneklerinde başarı bloğu `onSuccess` ile, `volumeUp` örneğinde ise
> **`onComplete`** ile yazılmıştır — LG'nin kendi örnekleri arasında tutarsızlık
> vardır; iki callback de [I]'ya göre geçerlidir (success/failure bağımsız
> tamamlanma). Birebir örnekler: `webos-audio-ornekler.md` §2.

---

## §5 Abonelik ve Çağrı Mekanizması ([I] birebir)

| Öğe | Davranış |
|---|---|
| URI biçimi | `luna://service name` |
| `subscribe: true` | "the method remains in memory and return responses either at periodic intervals or when its data is updated" · yanıtta `subscribed: true` |
| `resubscribe: true` | hatadan sonra otomatik yeniden abonelik |
| `onComplete` | başarıdan bağımsız tetiklenir |
| Request object | `cancel()` — abonelik dahil isteği iptal eder ([S] örneği: `subscriptionHandle.cancel()`) |
| GC kuralı | "we recommend using a variable to call or subscribe to Luna APIs" |

> Not: `volumeUp`/`volumeDown`/`setMuted` sayfalarında `subscribe` parametresi
> **geçmiyor** — abonelik sözleşmesi [I] genel çerçevesidir; ses metotlarına
> uygulanıp uygulanmadığı **UNKNOWN**.

---

## §6 `luna://com.webos.settingsservice` — Ses İle Kesişen Ayarlar ([S] birebir)

### §6.1 `getSystemSettings` parametreleri

| Alan | Zorunlu | Tip | Açıklama |
|---|---|---|---|
| `category` | Optional | String | kategori — "If the specified keys (key) has a category, you must specify this property." |
| `keys` | Optional | String array | "One of the keys and key property should be specified." |
| `key` | Optional | String | tek anahtar |
| `subscribe` | Optional | Boolean | true: değişim olayı gelir · false (Default): tek çağrı |

### §6.2 Ses ile ilgili anahtarlar

| Kategori | Anahtar | Tip | Değerler | Not |
|---|---|---|---|---|
| `option` | `audioGuidance` | String | `on` · `off` | "Indicates whether to enable the audio guidance for the accessibility menu or not. This key is available on **webOS TV 3.0 or higher**." |
| `localeInfo` | `locales.AUD` | String | dil etiketi (ör. `fr-FR`) | "Language for audio language" |
| `localeInfo` | `locales.AUD2` | String | dil etiketi | "Language for secondary audio language" |

### §6.3 Kullanım durumu kuralları (Remarks tablosu — özet)

| case | category | keys | Sonuç |
|---|---|---|---|
| kategorisiz anahtar | — | `["localeInfo"]` | ✅ locale değerleri döner |
| yalnız category | `option` | — | ❌ hata |
| category + doğru key | `option` | `["country"]` | ✅ ülke değeri |
| yanlış kategori-key | `option` | `["localeInfo"]` | ❌ hata |
| farklı kategoriler | — | `["localeInfo","country"]` | ❌ hata |
| hiçbir şey | — | — | ❌ hata |

### §6.4 Dönüş ve hata

| Öğe | Değer (birebir) |
|---|---|
| Başarı | `returnValue: true` · `category` · `method` · `settings` (· abonelikte `subscribed`) |
| Hata metni | `"There is no matched result from DB"` — "If the requested key is undefined, API returns an error." |
| Çoklu abonelik | "you will receive the result data for whole keys at the first return. After the first return, you will receive the result data for each key when the corresponding system information is changed." |
| audioGuidance dönüşü | `{"subscribed": false, "category": "option", "method": "getSystemSettings", "settings": {"audioGuidance": "off"}, "returnValue": true}` |
| localeInfo AUD örneği | `"AUD": "fr-FR"`, `"AUD2": "en-GB"` (sayfadaki dönüş örneği) |
| Emulator | Yes (6.x Partially) |

> Tam `settings`/`localeInfo` obje şeması ve örnek kodlar:
> `webos-audio-ornekler.md` §3–§4.

---

## §7 Sınır

| Dışında olan | Nereye |
|---|---|
| Uygulama tarafı JS kodu (birebir örnekler + test senaryoları) | kardeş `webos-audio-ornekler.md` |
| Luna genel çerçeve, GC, servis envanteri, uyumluluk matrisi | `webos-audio-anayasa.md` §5–§6 |
| Caption anahtarlarının tamamı (ses dışı erişilebilirlik) | yalnız §6.2'de geçiş düzeyinde — tam küme [S] sayfasındadır |
| Ses alt katmanı | UNKNOWN (üst dal) |

---

## §8 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| W-Q1 | Audio servisinde 3 metot dışında metot var mı? | ⚠️ VERIFICATION REQUIRED | Çift kaynak (Audio + Intro) aynı kümyeyi veriyor; yine de kapsamlı üçüncü bir doküman kanıtı yok |
| W-Q2 | Hata kodu tam listesi? | UNKNOWN | Sayfa yalnız metot-bazlı: `1` ve `ERROR_3` |
| W-Q3 | Ses metotlarında `subscribe` geçerli mi? | UNKNOWN | §5 notu |
| W-Q4 | `setMuted` ile `volumeUp/Down` hata kodu dili neden farklı (Number `1` vs String `ERROR_3`)? | ⚠️ VERIFICATION REQUIRED | Resmi sayfada iki farklı yazım — dökümantedir, açıklama yok |
| W-Q5 | `getSystemSettings` dışında yazma (set) metodu? | UNKNOWN | [S] sayfasında yalnız `getSystemSettings` var |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
