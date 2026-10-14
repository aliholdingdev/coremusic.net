---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-luna-servisi
evidence:
  - https://webostv.developer.lge.com/develop/references/audio
  - https://webostv.developer.lge.com/develop/references/settings-service
  - https://webostv.developer.lge.com/develop/references/luna-service-introduction
validation:
  - "2026-10-13 · 3 URL GET 200 · tam gövde · JS blokları resmi sayfadaki örneklerden birebir (her blokta kaynak etiketi) · uydurma kod 0 · test senaryoları bu dalın ürünüdür ve ayrı etiketlidir"
open_questions:
  - "Test senaryoları bu dalın ürünüdür; gerçek TV/CI entegrasyonu UNKNOWN"
  - "volumeUp örneğinde onSuccess yerine onComplete kullanımı resmi sayfadaki tutarsızlıktır — davranışsal etkisi için cihaz testi gerekir (UNKNOWN)"
group: kod
---

# webos-audio-ornekler — Resmi Örnek Kod ve Test Senaryoları

**ELI15:** Bu dosyadaki JS blokları **LG'nin resmi sayfalarından kelimesi
kelimesine alınmıştır** (uydurma yok, etiket zorunlu); ardından bu çağrıların
nasıl doğrulanacağına dair test senaryoları verilir — senaryolar bu dalın
ürünüdür ve kaynakları ayrı işaretlenir.

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-ornekler` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/webos-audio` |
| Kademe | 5 · yaprak-modül |
| group | `kod` |
| Doğrulama tarihi | 2026-10-13 |

## §1.1 Kaynaklar

| Etiket | Kaynak | URL | Durum |
|---|---|---|---|
| [A] | Audio API Reference Guide | https://webostv.developer.lge.com/develop/references/audio | ✅ GET 200 · tam gövde |
| [S] | Settings Service | https://webostv.developer.lge.com/develop/references/settings-service | ✅ GET 200 · tam gövde |
| [I] | Luna Service API Introduction | https://webostv.developer.lge.com/develop/references/luna-service-introduction | ✅ GET 200 · tam gövde |

---

## §2 Ses Metotları — Resmi Örnekler (birebir)

### §2.1 `setMuted` ([A] Example)

```javascript
[ETİKET: birebir alıntı · kaynak: [A] setMuted Example]
// One-time call
var request = webOS.service.request('luna://com.webos.audio', {
  method: 'setMuted',
  parameters: { muted: true },
  onSuccess: function (inResponse) {
    console.log('TV is muted');
    // To-Do something
  },
  onFailure: function (inError) {
    console.log('Failed to set muted');
    console.log('[' + inError.errorCode + ']: ' + inError.errorText);
    // To-Do something
    return;
  },
});
```

Dönüş: `{"returnValue": true}` · Hata: `1 / Not a valid JSON message`.

### §2.2 `volumeDown` ([A] Example)

```javascript
[ETİKET: birebir alıntı · kaynak: [A] volumeDown Example]
// One-time call
var request = webOS.service.request('luna://com.webos.audio', {
    method: 'volumeDown',
    onSuccess: function (inResponse) {
        console.log('The volume is decreased by 1.');
        // To-Do something
    },
	onFailure: function (inError) {
		console.log('Failed to decrease volume by 1.');
		console.log('[' + inError.errorCode + ']: ' + inError.errorText);
		// To-Do something
		return;
	}
});
```

> Alıntıdaki karışık girinti (boşluk/tabs) **düzeltülmez** — birebir korunur.
> Dönüş: `{"returnValue": true}` · Hata: `ERROR_3 / Parameters should be empty`.

### §2.3 `volumeUp` ([A] Example — `onComplete` kullanan resmi sürüm)

```javascript
[ETİKET: birebir alıntı · kaynak: [A] volumeUp Example]
// One-time call
var request = webOS.service.request('luna://com.webos.audio', {
    method: 'volumeUp',
    onComplete: function (inResponse) {
        console.log('The volume is increased by 1.');
        // To-Do something
    },
	onFailure: function (inError) {
		console.log('Failed to increase volume by 1.');
		console.log('[' + inError.errorCode + ']: ' + inError.errorText);
		// To-Do something
		return;
	}
});
```

> **Tutarsızlık notu:** LG bu örnekte başarı bloğunu `onComplete` ile yazarken
> iki örnekte `onSuccess` kullanıyor. İkisi de [I] sözleşmesinde geçerlidir;
> davranışsal fark (success mi complete mi önce) cihaz testi gerektirir.

### §2.4 Genel çağrı iskeleti ([I] — birebir)

```javascript
[ETİKET: birebir alıntı · kaynak: [I] Calling webOS services from any web application]
var request = webOS.service.request("luna://com.mycom.helloworld.service", {
    method:"hello",
    parameters: {"input": inputMessage},

    onSuccess: function (args) {
        //TO-DO
    },

    onFailure: function(inError) {
        //TO-DO
    },

    onComplete: function(inResponse) {
        //....
    },

    subscribe: subscribeStatus ,
    resubscribe: resubscribeStatus
});
```

```html
[ETİKET: birebir alıntı · kaynak: [I] kütüphane ekleme]
<script type="text/javascript" src="webOSTVjs-1.1.1/webOSTV.js"></script>
```

> GC kuralı ([I]): "we recommend using a variable to call or subscribe to Luna
> APIs as the above code example" — çağrı dönüşü `request` değişkeninde tutulur.

---

## §3 Settings — Resmi Örnekler (birebir)

### §3.1 `audioGuidance` okuma ([S] Example)

```javascript
[ETİKET: birebir alıntı · kaynak: [S] Example — Get audio guidance information]
var audioGuidanceRequest = webOS.service.request(
  'luna://com.webos.settingsservice',
  {
    method: 'getSystemSettings',
    parameters: {
      category: 'option',
      keys: ['audioGuidance'],
    },
    onSuccess: function (inResponse) {
      console.log('Result: ' + JSON.stringify(inResponse));
      // To-Do something
    },
    onFailure: function (inError) {
      console.log("Failed to get settings' value");
      console.log('[' + inError.errorCode + ']: ' + inError.errorText);
      // To-Do something
      return;
    },
  }
);
```

Dönüş ([S] Return example):

```json
[ETİKET: birebir alıntı · kaynak: [S] Success return - audio guidance information]
{
    "subscribed": false,
    "category": "option",
    "method": "getSystemSettings",
    "settings": {
        "audioGuidance": "off"
    },
    "returnValue": true
}
```

### §3.2 `localeInfo` aboneliği + iptal ([S] Example)

```javascript
[ETİKET: birebir alıntı · kaynak: [S] Example — Get locale information with subscription (parça)]
subscriptionHandle = webOS.service.request('luna://com.webos.settingsservice', {
  method: 'getSystemSettings',
  parameters: {
    keys: ['localeInfo'],
    subscribe: true,
  },
  onSuccess: function (inResponse) {
    if (typeof inResponse.subscribed != 'undefined') {
      if (!inResponse.subscribed) {
        console.log("Failed to subscribe settings' value");
        return;
      }
    }
    console.log('Result: ' + JSON.stringify(inResponse));
    // To-Do something
  },
  onFailure: function (inError) { /* ... */ },
});
```

```javascript
[ETİKET: birebir alıntı · kaynak: [S] Example — iptal]
// If you need to unsubscribe the data, use cancel() method as below
subscriptionHandle.cancel();
```

Dönüşte ses lokali ([S] Return example): `"AUD": "fr-FR"` · `"AUD2": "en-GB"`.

### §3.3 Ülke okuma ([S] Example — kategori/keys kalıbı)

```javascript
[ETİKET: birebir alıntı · kaynak: [S] Example — Get country information (çekirdek)]
var request = webOS.service.request('luna://com.webos.settingsservice', {
  method: 'getSystemSettings',
  parameters: {
    category: 'option',
    keys: ['country', 'smartServiceCountryCode2'],
  },
  onSuccess: function (inResponse) { /* ... */ },
  onFailure: function (inError) { /* ... */ },
});
```

---

## §4 Test Senaryoları (bu dalın ürünü — kanıt etiketi ayrı)

> Kaynak: senaryolar bu vault dosyasının **üretimidir**; iddia düzeyi
> "önerilen doğrulama", LG dokümanı değildir.

| # | Senaryo | Çağrı | Beklenen | Bağlı sözleşme |
|---|---|---|---|---|
| T1 | Sustur | `setMuted {muted:true}` | `returnValue: true` · ses kesilir | [A] §setMuted |
| T2 | Geri aç | `setMuted {muted:false}` | `returnValue: true` · ses döner | [A] |
| T3 | Seviye +1 | `volumeUp` (parametresiz) | `returnValue: true` · OSD'de +1 | [A] |
| T4 | Boş gövdeyle volumeDown | `volumeDown {foo:1}` | `ERROR_3 / Parameters should be empty` | [A] hata referansı |
| T5 | Geçersiz JSON | bayt düzeyinde bozuk istek | `1 / Not a valid JSON message` | [A] hata referansı |
| T6 | audioGuidance okuma | `getSystemSettings(category:'option', keys:['audioGuidance'])` | `settings.audioGuidance ∈ {on,off}` | [S] |
| T7 | audioGuidance aboneliği | `subscribe:true` + ayar değiştir | ikinci bildirim gelir · `subscribed` alanı | [S] Remarks |
| T8 | Abonelik iptali | `subscriptionHandle.cancel()` | yeni bildirim gelmez | [S] |
| T9 | Tanımsız anahtar | `keys:['yokBoyleBirSey']` | `returnValue:false` · `"There is no matched result from DB"` | [S] Error reference |
| T10 | Kategori-key uyumsuzluğu | `category:'option', keys:['localeInfo']` | ❌ hata (Remarks satırı 4) | [S] Remarks |
| T11 | Kütüphane yükleme | `webOSTV.js` etiketi | `webOS.service.request` tanımlı | [I] |
| T12 | GC koruması | `request` değişkenine atamadan zincirleme | önerilmez — GC kaybı riski | [I] Important |

**Ortam matrisi (anayasa §6.2'den):** T1–T12 emulator ✅ (Audio Yes) ·
**simulator ❌ (Audio No)** · gerçek TV ✅.

---

## §5 Ölçüm

| Metrik | Değer |
|---|---|
| Doğrulanan URL | 3 (tamamı GET 200 · gövde) |
| Kod bloğu | 8 — **tamamı resmi örnek alıntısı** (etiketli) |
| Test senaryosu | 12 (bu dalın ürünü · ayrı etiketli) |
| JSON dönüş örneği | 2 (setMuted · audioGuidance) + 1 abonelik iptal |
| Uydurma kod | 0 |
| CoreMusic kesişimi | 0 isabet → UNKNOWN |

---

## §6 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| E-Q1 | `onSuccess` vs `onComplete` farkı ses metotlarında davranışsal mı? | UNKNOWN | Cihaz testi T3 ile cevaplanabilir |
| E-Q2 | `volumeDown`/`volumeUp` mevcut seviyeyi döndürüyor mu? | ⚠️ VERIFICATION REQUIRED | Dönüş örneği yalnız `returnValue` |
| E-Q3 | Gerçek TV/CI entegrasyonu | UNKNOWN | Senaryolar çalıştırılmadı |
| E-Q4 | `subscribe` ses metotlarında geçerli mi? | UNKNOWN | luna-servisi W-Q3 |

---

## §PHP webOS × PHP (2026-10-14)

webOS uygulama katmanı JS + Luna bus'tır (§22 `audio` service `volumeUp/Down`) —
cihazda PHP runtime **yoktur**. PHP'nin webOS'a erişimi: sunucu PHP → HTTP payload
→ cihaz JS'inin Luna çağrısına eklenmesi (kök §25.5 · PHP'den doğrudan Luna service
çağrısı **yapılamaz**).

---

**Geniş dil seti — webOS × dil (kök §26):** uygulama katmanı JS'tir (§22 Luna
`volumeUp/Down/setMuted`) — Node.js'te yerleşik ses API'si **yok** (404 negatif kanıt,
kök §26.2); webOS'ta ses çağıran tek dil JS'tir, Python/Go/Rust/C#/PHP/Node cihazda
**yok** (runtime doğrulaması: kök §25.5 · §26).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
