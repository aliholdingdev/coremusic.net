---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/webos-audio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children:
  - K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-luna-servisi
  - K000-isletim-sistemi/isletim-sistemi-audio/webos-audio/webos-audio-ornekler
depends_on: []
evidence:
  - https://webostv.developer.lge.com/develop/references/luna-service-introduction
  - https://webostv.developer.lge.com/develop/references/audio
  - https://webostv.developer.lge.com/develop/references/settings-service
validation:
  - "2026-10-13 · 3 URL GET 200 · tam gövde alındı (Luna çağrı sözleşmesi · Audio metot/hata tabloları · Settings) · `references/audio` kardeş klasörde 4 dosyada geçiyor (isletim-sistemi-turu/webos/*) → klasör-içi ayrışık 3 URL, çakışma bilinçli ve kayıt altında · isletim-sistemi-audio/ içi kesişimi 0"
open_questions:
  - "Audio servisinde ses seviyesi okuma/yazma metotları (`getVolume`/`setVolume` vb.) referans sayfasında GÖRÜNMÜYOR — yalnız setMuted/volumeUp/volumeDown var → eksik metotlar başka sürüm sayfasında mı? ⚠️ VERIFICATION REQUIRED"
  - "CoreMusic reposunda webOS/Luna ses kodu YOK → UNKNOWN"
  - "Ses ayarı `settings-service` üzerinden ne ölçüde yönlendirilebilir (audioGuidance haricinde) → gövde teyidi ⚠️"
group: mimari
---

# webos-audio — Deep-Dal İndeksi (Kademe-4)

**ELI15:** webOS TV'de ses kartına kimse doğrudan dokunmaz: uygulama `luna://com.webos.audio`
servisine **istek atar**, sistem sesi ayarlar. Bu dal üç şeyi kanıtlar: Luna çağrı
sözleşmesi (nasıl sorulur), Audio servisinin metot/hata tablosu (neye sorulur) ve
gerçek örnek kod (nasıl görünür) — hepsi LG'nin resmi sayfasından kelimesi kelimesine.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | webOS TV (LG Electronics) |
| Repo durumu | CoreMusic'te webOS kodu YOK → UNKNOWN (§4 Q3) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Audio servisinin URI'si `luna://com.webos.audio`'dır; 3 metot içerir: `setMuted` · `volumeDown` · `volumeUp` ("Provides methods for volume control") | https://webostv.developer.lge.com/develop/references/audio | 2026-10-13 | ✅ GET 200 · tam metot tablosu alındı |
| `setMuted` parametresi `muted: Boolean` (true=mute) · dönüş `returnValue: Boolean` + opsiyonel `errorCode`/`errorText` · hata kodu `1 = "Not a valid JSON message"` | https://webostv.developer.lge.com/develop/references/audio | 2026-10-13 | ✅ GET 200 · tablolar birebir |
| `volumeUp`/`volumeDown` parametresizdir; yanlış parametre verilirse `ERROR_3 = "Parameters should be empty"` döner | https://webostv.developer.lge.com/develop/references/audio | 2026-10-13 | ✅ GET 200 · hata referansı |
| Üç metot emülatörde desteklenir (Supported in Emulator: Yes) | https://webostv.developer.lge.com/develop/references/audio | 2026-10-13 | ✅ metot tablosu sütunu |
| Luna servis çağrısı `luna://<servis adı>` URI formundadır · parametreler JSON nesnesidir · `subscribe`/`resubscribe`/`onSuccess`/`onFailure`/`onComplete` ayrılmış niteliklerdir · yanıt `returnValue` taşır | https://webostv.developer.lge.com/develop/references/luna-service-introduction | 2026-10-13 | ✅ GET 200 · "Essentials of an API Service Call" bölümü |
| Audio API'si cihaz uyum tablosunda webOS TV 1.x → 25 arası **Yes**; **simülatörde No** | https://webostv.developer.lge.com/develop/references/luna-service-introduction | 2026-10-13 | ✅ uyum tabloları |
| Ses kaynağı: `webOS.service.request` (webOSTV.js, 4.0'tan beri webOS.js yerine) · değişkene atanmazsa istek GC'ye yenilebilir | https://webostv.developer.lge.com/develop/references/luna-service-introduction | 2026-10-13 | ✅ "Calling webOS services" + Important notu |
| `settings-service` (`luna://com.webos.settingsservice`) sistem ayarı okumak içindir; Audio satırı "Provides methods for volume control" | https://webostv.developer.lge.com/develop/references/settings-service | 2026-10-13 | ✅ GET 200 · Luna API tablosu |

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-13)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |
| 2 | `webos-audio-anayasa.md` | Kimlik · Luna servis mimarisi · katman yığını | **oluşturuldu** |
| 3 | `webos-audio-luna-servisi.md` | `com.webos.audio` metot/parametre/hata tabloları · sözleşme | **oluşturuldu** |
| 4 | `webos-audio-ornekler.md` | Resmi örnek JS blokları (kaynaklı) · hata kodu matrisi · test senaryoları | **oluşturuldu** |

> Kural: bu 4 dosyanın yüzeyi bu deep-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-audio/*.md` ve diğer 6 deep-dala bu turda **dokunulmadı**.

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| Ses/rota politikası, Media DB, DRM, cihaz bilgisi | `../../isletim-sistemi-turu/webos/webos-ses-ve-rota.md` |
| Multimedya çatısı (video/oyun) | `../../isletim-sistemi-turu/webos/webos-multimedya-cati.md` |
| Gerçek zamanlı tampon/underrun | `../../isletim-sistemi-turu/webos/webos-gercek-zamanli-tampon.md` |
| Luna servis altyapısı geneli (aktivite/uygulama yöneticisi) | `../../isletim-sistemi-turu/webos/` diğer dosyalar |
| Çekirdek ses sürücüsü / ALSA | `../alsa/` · `../wasapi/` |
| DSP zinciri, Neva Engine | K004-dsp · K003-audio |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| W1 | `com.webos.audio`'da mutlak ses seviyesi okuma/yazma metotları yok mu (yalnız ±1)? | ⚠️ VERIFICATION REQUIRED |
| W2 | `settings-service` üzerinden hangi ses ayarları yazılabilir? | ⚠️ VERIFICATION REQUIRED |
| W3 | Simülatörde Audio = No → test stratejisi gerçek TV mi? | ✅ kayıt altında · strateji UNKNOWN |
| W4 | CoreMusic/Neva Engine hedefinde webOS var mı? (repo 0 isabet) | UNKNOWN |
| W5 | 512 örnek · 48 kHz ölçüm hedefi webOS'ta ölçülebilir mi? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `webos-audio-anayasa.md` | Kimlik, Luna mimarisi, katman yığını | Kademe-4 |
| mimari | `webos-audio-luna-servisi.md` | Servis metot/parametre/hata tabloları | Kademe-4 |
| kod | `webos-audio-ornekler.md` | Resmi örnek kod, hata matrisi, test senaryoları | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
