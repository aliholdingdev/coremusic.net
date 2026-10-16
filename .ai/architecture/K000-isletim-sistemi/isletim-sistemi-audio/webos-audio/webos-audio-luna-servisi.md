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

---

## §9 Üç Metot Karşılaştırma Matrisi (§2–§4 birleşimi — sayım kanıtlı)

| Ölçü | `setMuted` | `volumeDown` | `volumeUp` |
|---|---|---|---|
| Açıklama (birebir) | "Mutes or unmutes the volume." | "Decreases the volume by 1." | "Increases the volume by 1." |
| Parametre | `muted` (Boolean, **Required**) | **None** | **None** |
| Dönüş | `returnValue` (+ `errorCode`/`errorText` opsiyonel) | aynı | aynı |
| Hata referansı dili | Number `1` — "Not a valid JSON message" | String `ERROR_3` — "Parameters should be empty" | String `ERROR_3` — aynı |
| Emulator | Yes | Yes | Yes |
| See also | (sayfada yazılmış değil) | `setMuted` · `volumeUp` | `setMuted` · `volumeDown` |
| Örnek callback'i | `onSuccess` (§4 gözlemi) | `onSuccess` (§4 gözlemi) | **`onComplete`** (§4 gözlemi) |
| Abonelik parametresi | sayfada geçmiyor (§5 notu) | sayfada geçmiyor | sayfada geçmiyor |

**Sayım:** metot **3** (§2) · parametreli **1** (`setMuted`) · paramsız **2** ·
Emulator ✅ **3/3** · hata dili **iki farklı yazım** (→ §19 · W-Q4).

---

## §10 Çağrı İmza Envanteri (dosyada imzası kayıtlı çağrılar — 8 kayıt)

| # | Çağrı | İmza/şema (dosyadaki kayıt) | Kayıt § |
|---|---|---|---|
| 1 | `setMuted` | `{ muted: Boolean /*Required*/ }` | §3.1 |
| 2 | `volumeDown` | parametre yok (None) | §4 |
| 3 | `volumeUp` | parametre yok (None) | §4 |
| 4 | `getSystemSettings` | `{ category?: String, keys?: String[], key?: String, subscribe?: Boolean }` | §6.1 |
| 5 | Luna genel istek | `webOS.service.request` + URI `luna://service name` | ELI15 · §5 (anayasa bağı) |
| 6 | `subscribe: true` | rezerv özellik — periyodik/olay yanıtı | §5 |
| 7 | `resubscribe: true` | hatadan sonra yeniden abonelik | §5 |
| 8 | `Request.cancel()` | istek+abonelik iptali — `[S]` örneği `subscriptionHandle.cancel()` | §5 |

---

## §11 Dönüş Alanı Birleşik Envanteri (§3.2 · §4 · §6.4 — alan × tip × kayıt)

| # | Alan | Tip | Zorunluluk (dosyadaki) | Hangi sözleşmede | Kayıt |
|---|---|---|---|---|---|
| 1 | `returnValue` | Boolean | **Required** (audio) | 3 ses metodu | §3.2 · §2 |
| 2 | `errorCode` | Number | Optional — "returns errorCode only if it fails" | ses metotları | §2 · §3.2 |
| 3 | `errorText` | String | Optional | ses metotları | §2 · §3.2 |
| 4 | `subscribed` | Boolean | abonelikte görünür (`false` örnekte) | getSystemSettings | §5 · §6.4 |
| 5 | `category` | String | dönüşte | getSystemSettings | §6.4 |
| 6 | `method` | String | dönüşte (`"getSystemSettings"`) | getSystemSettings | §6.4 |
| 7 | `settings` | Object | dönüşte — anahtar/value çifti | getSystemSettings | §6.4 |
| 8 | `settings.audioGuidance` | String (`on`/`off`) | anahtar tanımlıysa | getSystemSettings | §6.2 · §6.4 |
| 9 | `settings.locales.AUD` | String (dil etiketi) | örnekte `fr-FR` | getSystemSettings | §6.2 · §6.4 |
| 10 | `settings.locales.AUD2` | String | örnekte `en-GB` | getSystemSettings | §6.2 · §6.4 |

**Birleşik şema kuralı:** bu 10 alan **dosyanın birleşik tümkümesidir**; dışarıda
kalan alan adı bu turda yazılmaz (anayasa §8-W3 ile aynı disiplin).

---

## §12 Hata Yüzeyi Envanteri (§3.3 · §4 · §6.4)

| # | Yüzey | Değer | Kapsadığı çağrı | Kayıt |
|---|---|---|---|---|
| 1 | Hata kodu | `1` → "Not a valid JSON message" | `setMuted` | §3.3 |
| 2 | Hata kodu | `ERROR_3` → "Parameters should be empty" | `volumeDown` · `volumeUp` | §4 |
| 3 | Hata metni | "There is no matched result from DB" | `getSystemSettings` (anahtar tanımsız) | §6.4 |
| 4 | Hata koşulu | "If the requested key is undefined, API returns an error." | `getSystemSettings` | §6.4 |
| 5 | Kombinasyon hataları | §6.3'teki 4 ❌ satırı | `getSystemSettings` | §6.3 |
| 6 | Tam kod listesi | **yok** → UNKNOWN | — | W-Q2 (§8) |

---

## §13 `getSystemSettings` Kombinasyon Kuralları (§6.1–§6.3 çözümlemesi)

| # | Kural | Çözümleme | Kayıt |
|---|---|---|---|
| K1 | `keys` **veya** `key` zorunlu | "One of the keys and key property should be specified." — ikisi birden yalnız birlikte anlamlı okunur | §6.1 |
| K2 | kategori zorunluysa `category` yazılmalı | "If the specified keys (key) has a category, you must specify this property." | §6.1 |
| K3 | yalnız `category` = hata | `["country"]` gibi anahtarsız çağrı ❌ (§6.3 satır 2) | §6.3 |
| K4 | yanlış kategori-anahtar eşleşmesi = hata | `option` + `localeInfo` ❌ — eşleşme kategoriye göre zorunlu | §6.3 |
| K5 | farklı kategoriler tek çağrıda ❌ | `["localeInfo","country"]` ❌ — kategoriler ayrılmaz | §6.3 |
| K6 | kategorisiz anahtar çalışır | `["localeInfo"]` tek başına ✅ | §6.3 |
| K7 | tamamen boş çağrı ❌ | hiçbir özellik yok → ❌ | §6.3 |

**Çıkarım (aritmetik değil mantık):** §6.3'ün 6 satırındaki ✅/❌ dağılımı
**2 ✅ / 4 ❌** — geçerli çağrı yalnız "kategorisiz tek-kategori anahtar" ve
"eşleşen category+key" biçimleridir.

---

## §14 Ses Karar Akışları (adım zincirleri — §2–§6'dan türetildi)

### §14.1 Sessize alma (§3)

1. istek: `luna://com.webos.audio` · metot `setMuted` · `{ muted: true }` — §3.1
2. başarı: `{'returnValue': true}` — §3.4 (birebir örnektir)
3. çözme: `{ muted: false }` → unmute — §3.1 tanımı
4. hata: `errorCode: 1` — "Not a valid JSON message" — §3.3

### §14.2 Adımlı seviye (§4)

1. `volumeDown` — parametre **None** — "Decreases the volume by 1." — §4
2. `volumeUp` — parametre **None** — "Increases the volume by 1." — §4
3. taşma/alt sınır davranışı: bu sayfada **yok** → UNKNOWN (dosya yokluğu)
4. hata: `ERROR_3` "Parameters should be empty" (boş olmayan parametre gönderilirse) — §4

### §14.3 `audioGuidance` okuma (§6)

1. `getSystemSettings` · `category: 'option'` · `keys: ['audioGuidance']` — §6.2
2. dönüş: `settings.audioGuidance` = `"on"`/`"off"` — §6.2 · §6.4 örneği (`"off"`)
3. sürüm koşulu: 3.0+ — §6.2 (birebir)
4. tanımsızsa: "There is no matched result from DB" — §6.4

### §14.4 `audioGuidance` aboneliği (§6.4 · §5)

1. aynı çağrıya `subscribe: true` — §6.1
2. ilk yanıt: **tüm** anahtarların verisi — "you will receive the result data for whole keys at the first return..." — §6.4
3. sonrası: yalnız değişen anahtar gelir — §6.4 (aynı alıntı)
4. temizlik: `subscriptionHandle.cancel()` — §5 ([S] örneği)

### §14.5 Dil (locale) okuma (§6.3 · §6.4)

1. `keys: ["localeInfo"]` — **kategorisiz** — §6.3 satır 1 ✅
2. dönüş: `locales.AUD` (ör. `fr-FR`) · `AUD2` (ör. `en-GB`) — §6.4
3. anlamlar: AUD "Language for audio language" · AUD2 "secondary audio language" — §6.2
4. yanlış eşleştirme (`option` + `localeInfo`) → ❌ hata — §6.3

### §14.6 Geçersiz çağrı yolu (§6.3)

1. hangi kombinasyonlar ❌ → §6.3'teki 4 satır (K3–K7)
2. hata yüzeyi: returnValue false + §6.4 hata metni
3. tanınmayan anahtar: "If the requested key is undefined, API returns an error." — §6.4

---

## §15 Ortam ve Sürüm Kayıtları (bu dosyanın kendi satırları)

| Kayıt | Değer | Kayıt § |
|---|---|---|
| 3 ses metodu — Emulator | **Yes** (hepsi) | §2 |
| `getSystemSettings` — Emulator | Yes · **6.x Partially** | §6.4 |
| `audioGuidance` eşiği | **webOS TV 3.0+** | §6.2 |
| Simulator durumu | bu dosyada **geçmiyor** → anayasa §6.2'ye bağı (Simulator ❌ oradadır) | §7 sınır satırı |
| Sayfa damgası | 2026-10-13 · GET 200 · tam gövde (3 URL) | §1.1 |

---

## §16 Operasyonel Kontrol Listesi (§5 — uygulanabilir kurallar)

| # | Kural | Gerekçe (alıntı) | Kayıt |
|---|---|---|---|
| 1 | çağrıyı **değişkene** bağla | GC: "Implicit objects ... might be removed by garbage collection (GC)" | §5 |
| 2 | abonelikte `subscriptionHandle` sakla | `cancel()` ancak referansla mümkün | §5 |
| 3 | `onComplete`'i bitiş sayacı olarak kullan | "regardless of its success or failure" | §5 |
| 4 | hatada `resubscribe` dene | "it resubscribes to a method after a failure has occurred" | §5 |
| 5 | `returnValue` kontrol etmeden başarı sanma | §3.2 true: Success · false: Failure | §3.2 |
| 6 | ses metotlarında abonelik **varsayma** | §5 notu → UNKNOWN (W-Q3) | §5 |
| 7 | her aboneliği `cancel()` ile kapat | kayıt temizliği — §14.4 | §5 · §6.4 |

---

## §17 Metot Adı Koruma Listesi (anayasa §8-W1 ile birleşik)

| İzinli (birebir kayıt) | Kayıt | Yasak/sınır |
|---|---|---|
| `setMuted` | §2 · §3 | sayfada olmayan ad **üretilmez** |
| `volumeDown` | §2 · §4 | `setVolume` (mutlak) → kanıt yok, WQ3/W-Q1 |
| `volumeUp` | §2 · §4 | `getMuted`/`getStatus` gibi adlar → dosyada kayıt yok |
| `getSystemSettings` | §6.1 | `setSystemSettings` → W-Q5: sayfada yok |

---

## §18 Örnek Dönüşlerin Satır Satır Okuması (§3.4 · §6.4 — birebir örnekler)

**Audio (`setMuted` başarısı):**

```json
[ETİKET: birebir alıntı · kaynak: [A] Return example · §3.4]
{ 'returnValue': true }
```

- alan sayısı **1** · zorunlu alan tek · `errorCode`/`errorText` yok → başarı
  durumunda hata alanlarının **gelmediği** görünür (§2 "only if it fails" ile tutarlı).

**Settings (`audioGuidance`):**

```json
[ETİKET: birebir alıntı · kaynak: [S] · §6.4]
{"subscribed": false, "category": "option", "method": "getSystemSettings",
 "settings": {"audioGuidance": "off"}, "returnValue": true}
```

- `subscribed: false` → tek çağrı (abonelik yok) — §6.1 default
- `category` + `method` + `settings` + `returnValue` → **4 alan** + `subscribed` = §6.4 sayımı
- `settings.audioGuidance: "off"` → §6.2 değer kümesi (`on`/`off`) içinden

---

## §19 W-Q4 — İki Hata Dili Gerilimi: Derin Kayıt (bulundu, silinmedi)

| Ölçü | `setMuted` | `volumeDown`/`volumeUp` |
|---|---|---|
| Kod biçimi | **Number** `1` | **String** `ERROR_3` |
| Mesaj | "Not a valid JSON message" | "Parameters should be empty" |
| Doğrudan okuma | şema/JSON-bozukluğu sınıfı | parametre-taşıma sınıfı |
| Kaynak | §3.3 ([A] birebir) | §4 ([A] birebir) |
| Dökümantedir? | ✅ — sayfanın kendisi | ✅ — sayfanın kendisi |
| Açıklama | LG sayfasında **açıklama yok** | aynı |

**Karar:** iki yazım da birebir korunur; hangisinin evrensel hata dili olduğu
**UNKNOWN** → W-Q4 ⚠️ (§8 kaydı değişmedi, bu § yalnız derinleştirir).

---

## §20 Açık Soru Kapanış Planı (W-Q1–W-Q5 — gelecek tur için PLAN · iddia değil)

| # | Kapanış yolu | Şimdiki etiket |
|---|---|---|
| W-Q1 | üçüncü bağımsız doküman (varsa) gövde-GET → §2'ye ekleme | ⚠️ |
| W-Q2 | metot sayfası dışında hata-kodu referansı (varsa) → §12'ye satır | UNKNOWN |
| W-Q3 | ses metodu sayfasında `subscribe` denemesi/örneği → §5 notunun kapatılması | UNKNOWN |
| W-Q4 | LG sürüm geçmişi/notlar → iki dilin tarihsel kaynağı | ⚠️ |
| W-Q5 | [S] sayfasında yazma metodu olup olmadığı → §17 tablosu | UNKNOWN |

---

## §21 Bu Tur Yapılmayanlar (açık beyan)

| Yapılmadı | Kayıt |
|---|---|
| Web GET / ölçüm tazelenmesi | §1.1 damgaları (2026-10-13) geçerli sayıldı |
| FM `validation`/`open_questions` değişikliği | dokunulmadı |
| Kardeş dosyalara yazım (anayasa · ornekler) | yok — kendi turlarında |
| §1–§8 içeriğinde değişiklik | yok — yalnız Authority öncesi append |
| `git commit` | yok — orkestratöre ait |

---

## §22 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 211 | <500 |
| §9–§21 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir |
| M2 | bu tur yeni dış iddia 0: §9–§22 tamamı §1–§8 içinden türetildi (yeni GET yok) |
| M3 | FM (`group: mimari`) korundu — `validation` bu turlarda değiştirilmez |
| M4 | birebir alıntı blokları (§3.4 · §6.4 örnekleri) **yeniden** yazıldıysa da içerik §'lerden kopyalandır — yeni alıntı yok |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 211 satır dokunulmaz |
| M7 | bozuk karakter taraması (Kiril/CJK aralık regex'i) her ölçümde — desen dosyaya literal yazılmaz |

---

---

## §23 Alan × Çağrı Kapsama Matrisi (§11 envanterinin çapraz okuması)

| Alan | `setMuted` | `volumeDown/Up` | `getSystemSettings` | İlk kayıt § |
|---|---|---|---|---|
| `returnValue` | ✅ (Required) | ✅ (Required) | ✅ | §3.2 · §6.4 |
| `errorCode` | ✅ (Optional, yalnız hata) | ✅ (aynı) | hata yüzeyi §6.4'te metinle | §2 · §3.2 |
| `errorText` | ✅ (Optional) | ✅ (aynı) | hata metni ile | §3.2 · §6.4 |
| `subscribed` | — | — | ✅ (abonelik/tek çağrı örnekte `false`) | §5 · §6.4 |
| `category` | — | — | ✅ (dönüşte) | §6.4 |
| `method` | — | — | ✅ (`"getSystemSettings"`) | §6.4 |
| `settings` | — | — | ✅ (obje) | §6.4 |
| `settings.audioGuidance` | — | — | ✅ (`on`/`off`) | §6.2 |
| `settings.locales.AUD` | — | — | ✅ (ör. `fr-FR`) | §6.2 |
| `settings.locales.AUD2` | — | — | ✅ (ör. `en-GB`) | §6.2 |

**Kapsama sayımı (aritmetik):** ses metotları **3 alan** (returnValue · errorCode ·
errorText) · settings **7 alan** (returnValue · subscribed · category · method ·
settings · + iç içe 3 alt anahtar) · **ortak alan: yalnız `returnValue` (+ hata
alanları)** — iki servis sözleşmesi birbirine **yalnız dönüş disiplini** üzerinden
benzer; alan kümeleri **kesişmez** (§11'deki 10 alanın dağılımı).

---

## §24 Tur Sayısal Özeti ve Ekonomi Kaydı

| Envanter | Sayı | Kayıt § |
|---|---|---|
| Ses metodu | **3** | §2 |
| Çağrı imzası | **8** | §10 |
| Dönüş alanı (birleşik) | **10** | §11 |
| Hata yüzeyi | **6** | §12 |
| Kombinasyon kuralı | **7** (2 ✅ / 4 ❌ satır çözümlemesi) | §13 |
| Ses karar akışı | **6** zincir | §14 |
| Operasyonel kural | **7** | §16 |
| Metot-adı koruma satırı | **4 izinli / 4 sınır** | §17 |
| Açık soru (değişmedi) | **5** (W-Q1…W-Q5) | §8 · §20 |
| Bu tur eklenen § | **14** (§9–§22) + 2 (§23–§24) | bu dosya |

**Ekonomi notu:** bu tur sıfır dış GET ile yapıldı; tüm sayılar §1–§8'in kendi
tablolarının toplamıdır (kaynak: her satırın "Kayıt §" sütunu). Yeni iddia 0 ·
kardeş yazım 0 · FM değişikliği 0 · commit 0.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
