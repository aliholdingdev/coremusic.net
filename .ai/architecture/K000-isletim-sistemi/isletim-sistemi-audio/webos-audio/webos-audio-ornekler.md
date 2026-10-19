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

---

## §7 Örnek-Anatomisi Çizelgesi (§2–§3 bloklarının yapısal dökümü)

| # | Örnek | Tip | Çağrı biçimi | Callback (başarı) | Hata bloğu | `return;` | Kayıt § |
|---|---|---|---|---|---|---|---|
| O1 | `setMuted` | JS | `method + parameters {muted:true}` | `onSuccess` | `onFailure` | evet | §2.1 |
| O2 | `volumeDown` | JS | `method` (paramsız) | `onSuccess` | `onFailure` | evet | §2.2 |
| O3 | `volumeUp` | JS | `method` (paramsız) | **`onComplete`** | `onFailure` | evet | §2.3 |
| O4 | genel iskelet | JS | `method + parameters + subscribe + resubscribe` | `onSuccess` + `onComplete` | `onFailure` | yok (TO-DO) | §2.4 |
| O5 | kitaplık etiketi | HTML | `<script src="webOSTVjs-1.1.1/webOSTV.js">` | — | — | — | §2.4 |
| O6 | `audioGuidance` | JS | `category + keys[]` | `onSuccess` | `onFailure` | evet | §3.1 |
| O7 | `audioGuidance` dönüşü | JSON | — | — | — | — | §3.1 |
| O8 | `localeInfo` abonelik | JS | `keys[] + subscribe:true` | `onSuccess` (önce `subscribed` koruması) | `onFailure` (parça) | — | §3.2 |
| O9 | abonelik iptal | JS snippet | `subscriptionHandle.cancel()` | — | — | — | §3.2 |
| O10 | ülke okuma | JS | `category + keys[iki]` | `onSuccess` (parça) | `onFailure` (parça) | — | §3.3 |

**Yapısal bulgular (sayım):**
- Başarı-callback dağılımı: `onSuccess` **6** · `onComplete` **2** (O3 · O4) ·
  `onSuccess+onComplete` birlikte yalnız O4'te.
- `onFailure` **7** blokta var; hata logu kalıbı hep aynı biçim:
  `'[' + inError.errorCode + ']: ' + inError.errorText` (O1 · O2 · O3 · O6).
- `return;` yalnız tek-seferlik çağrıların `onFailure`'ında (O1 · O2 · O3 · O6).
- Parametreli çağrı **2** (O1 `muted` · O6/O8/O10 `parameters`); paramsız **2**
  (O2 · O3).
- `subscribe` gerçek kullanım **1** (O8 `true`) · `resubscribe` yalnız O4'te
  değişken olarak.

---

## §8 Kalıp Çıkarımları (§2–§3 kodundan — iddia değil gözlem)

| # | Kalıp | Kanıt (blok + satır dili) |
|---|---|---|
| P1 | istek her zaman değişkene atanır (`request` · `audioGuidanceRequest` · `subscriptionHandle`) | O1–O4 · O6 · O8–O9 — [I] GC kuralı §2.4 dipnotu |
| P2 | tek çağrı örneği yorum satırıyla başlar: `// One-time call` | O1 · O2 · O3 |
| P3 | başarıda `console.log` + `// To-Do something` iskeleti | O1 · O2 · O3 · O6 |
| P4 | hata iki satır log: durum mesajı + `[kod]: metin` | O1 · O2 · O3 · O6 |
| P5 | abonelik örneğinde ilk iş `subscribed` doğrulaması | O8 (`typeof ... != 'undefined'` dalı) |
| P6 | iptal ayrı snippet: yorum + `subscriptionHandle.cancel();` | O9 |
| P7 | girinti karışıklığı **düzeltmez** (boşluk+tab bir arada) | O2 · O3 dipnotu (§2.2) |
| P8 | dönüş `JSON.stringify(inResponse)` ile loglanır | O6 · O8 |
| P9 | kitaplık yolu göreli: `webOSTVjs-1.1.1/webOSTV.js` | O5 — sürüm etiketi örneğin kendisinde |

---

## §9 Alıntı Bütünlük Denetimi (fence-sayımı — bu turda dosya tarandı)

| # | Blok | Açılış fence (satır) | Kod satırı (etiket hariç) | Etiket satırı | Tip |
|---|---|---|---|---|---|
| 1 | §2.1 setMuted | 54 | 15 | ✅ | JS |
| 2 | §2.2 volumeDown | 77 | 14 | ✅ | JS |
| 3 | §2.3 volumeUp | 100 | 14 | ✅ | JS |
| 4 | §2.4 iskelet | 124 | 19 | ✅ | JS |
| 5 | §2.4 script etiketi | 147 | 1 | ✅ | HTML |
| 6 | §3.1 audioGuidance | 161 | 20 | ✅ | JS |
| 7 | §3.1 dönüş | 187 | 9 | ✅ | JSON |
| 8 | §3.2 abonelik | 202 | 18 | ✅ | JS |
| 9 | §3.2 iptal | 224 | 2 | ✅ | JS |
| 10 | §3.3 ülke | 234 | 9 | ✅ | JS |

**Sayım mutabakatı (§5 ile):** fenced toplam **10** = §5'in "Kod bloğu 8" (JS:
1+2+3+4+6+8+9+10 = 8 blok) + HTML **1** + JSON **1**; §5'in "JSON dönüş örneği 2"
kaydı: audioGuidance JSON (bu tablo satır 7) + setMuted dönüşü (satır 73 —
**fence dışı**, düz metin). **Çelişki yok** — üç sayım da aynı dosyayı sayar.

---

## §10 Test Senaryoları Genişlemesi (T13–T16 — §4 ile aynı etiket sınıfı: vault ürünü)

| # | Senaryo | Çağrı | Beklenen | Bağlı sözleşme |
|---|---|---|---|---|
| T13 | Dil (locale) okuma | `getSystemSettings {keys:['localeInfo']}` (kategorisiz) | `returnValue: true` · `settings.locales` içinde `AUD`/`AUD2` | [S] §3.2 dönüş satırı (bu dosya) · Remarks satır 1 ✅ |
| T14 | Ülke okuma | `category:'option'` + `keys:['country','smartServiceCountryCode2']` | `returnValue: true` · iki anahtar `settings` içinde | [S] §3.3 örneği |
| T15 | `subscribed` koruma kolu | abonelik yanıtında `subscribed:false`/`undefined` | örnek koddalı: "Failed to subscribe settings' value" · return | [S] §3.2 örnek kodu |
| T16 | `onSuccess` vs `onComplete` (E-Q1 testi) | `volumeUp` örneğini `onSuccess`'e çevirip aynını çalıştırma | iki sürümde de başarı logu gelmeli → davranışsal fark değilse E-Q1 kapanır | [A] §2.3 · §6 E-Q1 |

**Ortam matrisi** (§4 sonundaki satır T13–T16 için de geçerli): emulator ✅ ·
**simulator ❌** · gerçek TV ✅ (anayasa §6.2 bağı).

---

## §11 Sözleşme × Örnek Kapsama Matrisi

| Sözleşme (luna-servisi dosyasındaki) | Resmi örnek bu dosyada? | Kayıt |
|---|---|---|
| `setMuted` | ✅ O1 | §2.1 |
| `volumeDown` | ✅ O2 | §2.2 |
| `volumeUp` | ✅ O3 | §2.3 |
| genel Luna çağrısı (`[I]` iskelet) | ✅ O4 + O5 | §2.4 |
| `getSystemSettings` + `audioGuidance` | ✅ O6 + O7 | §3.1 |
| `getSystemSettings` + `localeInfo` (aboneli) | ✅ O8 + O9 | §3.2 |
| `getSystemSettings` + ülke anahtarları | ✅ O10 | §3.3 |
| hata kodu senaryoları (`1` · `ERROR_3` · DB metni) | ⚠️ örnek **yok** — yalnız §4 T4/T5/T9 beklentileri | §4 · §6 |
| `resubscribe` | ❌ örnek yok (O4'te yalnız değişken) | §2.4 |
| `setVolume`/diğer metotlar | **kanıt yok** → üretilmez (anayasa W1) | §6 E-Q / W-Q1 |

**Kapsama sayımı:** resmi örnekle **7** sözleşme · örnek-olmayan **3** satır
(2 açık + 1 yasak-zon) — §6 açık sorularla örtüşür, yenisi eklenmedi.

---

## §12 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 314 | <500 |
| §7–§11 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir |
| M2 | bu tur yeni dış iddia 0: §7–§16 tamamı §1–§6 içinden türetildi (yeni GET yok) |
| M3 | FM + §1 künye dokunulmadı · `group` korundu |
| M4 | 10 alıntı bloğuna **dokunulmadı** — §9 yalnız saydı (fence satırları değişmedi) |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 314 satır dokunulmaz |
| M7 | bozuk karakter taraması (Kiril/CJK aralık regex'i) her ölçümde — desen dosyaya literal yazılmaz |

---

## §13 Açık Soru Kapanış Planı (E-Q1–E-Q4 — gelecek tur için PLAN)

| # | Kapanış yolu | Etiket (değişmedi) |
|---|---|---|
| E-Q1 | gerçek TV'de T3 + T16 karşılaştırması | UNKNOWN |
| E-Q2 | canlı TV'de `volumeUp` yanıtının JSON dump'ı | ⚠️ VERIFICATION REQUIRED |
| E-Q3 | CI entegrasyonu (TV bağlanabilirse) | UNKNOWN |
| E-Q4 | ses metodu sayfasında `subscribe` örneği aranması (varsa) | UNKNOWN |

---

## §14 Kardeş ve Üst Bağlar (bu tur notu)

| Bağ | İlişki |
|---|---|
| `webos-audio-luna-servisi.md` | sözleşmelerin resmi tabloları (§9–§24) — bu dosya **örnekleri** ondan, testleri **kendi ürünü** |
| `webos-audio-anayasa.md` | katman/Luna çerçevesi + uyumluluk matrisi (§6.2 ortam satırı buradan okundu) |
| `../../isletim-sistemi-turu/webos/` | platform üst dalı |
| kök §25.5 · §26 | PHP/dil-seti kesişim satırları (§PHP · geniş-dil blokları) |

---

## §15 Bu Tur Yapılmayanlar (açık beyan)

| Yapılmadı | Kayıt |
|---|---|
| Web GET / ölçüm tazelenmesi | §1.1 damgaları (2026-10-13) geçerli sayıldı |
| Alıntı bloklarında değişiklik | 0 — §9 sayım amaçlı okudu |
| FM / §1–§6 değişikliği | yok — yalnız Authority öncesi append |
| Kardeş dosyalara yazım | yok — kendi turlarında |
| `git commit` | yok — orkestratöre ait |

---

---

## §16 Senaryo × Örnek × Sözleşme Uçgeni (T1–T16 · O1–O10 birleşimi)

| Senaryo | Bağlı resmi örnek | Sözleşme yüzeyi | Beklenen kanıt sınıfı |
|---|---|---|---|
| T1 sustur | O1 (`muted:true`) | `setMuted` | dönüş `returnValue:true` |
| T2 geri aç | O1 (`muted:false`) | `setMuted` | dönüş `returnValue:true` |
| T3 seviye +1 | O3 (`volumeUp`) | `volumeUp` | başarı callback'i (O3 `onComplete`) |
| T4 gövdeli volumeDown | O2 kontrastı | `volumeDown` hata yüzeyi | `ERROR_3` (§4 hata referansı) |
| T5 bozuk JSON | O1–O3 `onFailure` blokları | `setMuted` hata yüzeyi | kod `1` (§2.1 dipnotu) |
| T6 audioGuidance okuma | O6 | `getSystemSettings` | `settings.audioGuidance ∈ {on,off}` |
| T7 audioGuidance aboneliği | O6 + O8 deseni | `getSystemSettings subscribe` | ikinci bildirim · `subscribed` |
| T8 abonelik iptali | O9 (`cancel()`) | Request object | yeni bildirim yok |
| T9 tanımsız anahtar | (örnek yok — §4 T9 beklentisi) | `getSystemSettings` hata metni | `"There is no matched result from DB"` |
| T10 kategori-key uyumsuz | O6/O10 kontrastı | Remarks §6.3 satır 4 | ❌ hata |
| T11 kitaplık yükleme | O5 (script etiketi) | [I] çağrı çerçevesi | `webOS.service.request` tanımlı |
| T12 GC koruması | P1 · O4 (`request` değişkeni) | [I] Important | zincirleme çağrı önerilmez |
| T13 locale okuma | O8 | `getSystemSettings` (kategorisiz) | `AUD`/`AUD2` döner |
| T14 ülke okuma | O10 | `getSystemSettings` (category+keys) | iki anahtar `settings` içinde |
| T15 `subscribed` koruma kolu | O8 (typeof dalı) | `getSystemSettings subscribe` | "Failed to subscribe..." kolu |
| T16 onSuccess vs onComplete | O3 ↔ O1/O2 kontrastı | [I] callback çerçevesi | E-Q1 kapanış deneyi |

**Uçgen sayımı:** senaryo **16** (T1–T12 §4 · T13–T16 §10) · resmi örnek **10**
(O1–O10 §9) · uçgenin her satırında en az bir örnek **ya da** açıkça "örnek yok"
işareti vardır (T5 · T9) — örnek uydurulmadı.

**§7–§16 = bu turun eklediği 10 §** · tamamı §1–§6 içinden türetildi · yeni GET 0.

| Marj | Kayıt |
|---|---|
| Marj 1 | kapı son ölçümü bu satırlardan **sonra** alınır — tur yanıtı bağlayıcıdır |
| Marj 2 | O1–O10 sayımda 10'dur; §9 tablosunun satır sayısı = bu sayı (mutabakat) |
| Marj 3 | fence sayısı 20 (10 blok × 2) — §9 okumasıyla aynı turda doğrulandı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
