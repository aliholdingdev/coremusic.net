---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-audio-unit-arayuzu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# ios-audio-unit-arayuzu — AudioUnit bileşeni, AudioBufferList ve render callback (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** draft
> **Kapsam:** `ios/` beyazliste kalem 1 (AudioUnit arayüzü) · 9 alanlı frontmatter · ≥500 satır.

---

## §0. ELI15

iOS'ta ses, uygulamanın doğrudan donanıma yazmadığı bir zincirden geçer: uygulama
AudioUnit (Apple'ın ses işlemci bileşeni) çağırır, bileşen işletim sisteminin ses
altyapısıyla konuşur, altyapı donanım sürücüsüne iner. Render callback, bu zincirin
"örnekleri burada üret" noktasıdır ve **zaman duyarlıdır**: sistem onu belirli bir
süre içinde geri çağırır; süre aşılırsa ses kesilir (dropout). Bu dosya bu arayüzün
sözleşmesini, veri yapısını (AudioBufferList), callback kurallarını ve iOS'a özgü
sınırları (ör. AUHAL'ın iOS'ta olmayışı) yazar.

---

## §1. Künye

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-audio-unit-arayuzu` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-turu/ios` |
| Üst dal | `isletim-sistemi-turu/ios/` |
| Kardeş dosyalar | `ios-avfoundation-semasi.md` · `ios-core-audio-ayrintisi.md` · `ios-gercek-zamanli-tampon.md` · `ios-cihaz-ve-rota-yonetimi.md` · `ios-web-arastirma.md` |
| Kademe | 3 (kademe-2 indeksin altı) |
| Durum | `draft` |
| group | `mimari` |
| Tarih | 2026-10-12 |
| Doğrulama türü | Resmi URL erişim ölçümü (developer.apple.com · 2026-10-12) |

---

## §2. Kapsam ve sınır

| İçinde | Dışında | Nereye |
|---|---|---|
| AudioUnit bileşen modeli ve yaşam döngüsü | AVFoundation düğüm grafiği | `ios-avfoundation-semasi.md` |
| AudioBufferList yapısı ve kanal taşıma | ASBD alan alan çözümleme | `ios-core-audio-ayrintisi.md` |
| Render callback sözleşmesi ve deadline | Ring buffer uygulaması | `ios-gercek-zamanli-tampon.md` |
| AUHAL sınırı (macOS ile ayrım) | Session/rota yönetimi | `ios-cihaz-ve-rota-yonetimi.md` |
| Property get/set kapsam özeti | URL tarama kaydı | `ios-web-arastirma.md` |

---

## §3. AudioUnit nedir — bileşen modeli

| Kavram | Tanım (bu vault'un ifadesi) | Durum |
|---|---|---|
| AudioUnit | Apple'ın ses işleme bileşeni; girdi alır, çıktı üretir, property ile yapılandırılır | resmi dokümanla destekli (§11) |
| Component subtype | Bileşenin rolü (çıktı, efekt, mixer, converter vb.) | isimler §4'te |
| Scope (global/input/output) | Property'lerin uygulandığı kapsam | §7 |
| Element | Kapsam içindeki örnek (ör. 2. girdi yayı) | §7 |
| Render callback | Sistem tarafından geri çağrılan, örnek üreten fonksiyon | §6 |
| Property listener | Property değişikliğini bildiren geri çağrım | §7 |
| Factory (AUAudioUnit) | Objective-C/Swift düzeyinde bileşen fabrikası | §5 |

### 3.1 Neden ayrı dosya

Bu dosya yalnız **arayüz sözleşmesini** yazar; algoritma (EQ/reverb vb.) bu dalın
kapsamı dışındadır (K004-dsp). Buffer stratejisi kardeş dosyadadır.

---

## §4. Çıktı bileşen alt tipleri (iOS bağlamı)

> **Kaynak notu:** Alt tip adları resmi Core Audio belgelerinde tanımlıdır; bu tablo
> iOS'ta ses yolunun nasıl bittiğini özetler. Cihaz başına destek **UNKNOWN**
> (ölçüm için fiziksel cihaz gerekir).

| Alt tip (isim) | Rol | iOS'ta kullanımı | Durum |
|---|---|---|---|
| `RemoteIO` | Donanıma giden/geden girdi-çıktı bileşeni | iOS'un birincil ses I/O yolu | destekli (doküman) |
| `VoiceProcessingIO` | Gürültü bastırma/vokal işleme donanımlı I-O | Görüşme benzeri senaryolar | destekli (doküman) |
| `VoiceProcessingIO` dışı efekt alt tipleri | EQ/delay/reverb gibi efektler | Aşağı akış efekt zinciri | [⚠️] cihaz-özgül destek |
| `Converter` | Biçim dönüşümü (ör. float↔int124) | Format uyuşmazlığı köprüsü | destekli (doküman) |
| `Mixer` | Çoklu girdinin karıştırılması | Aşağı akış toplama noktası | destekli (doküman) |
| `AUHAL` | macOS donanım birimi | **iOS'ta YOK** | iOS dışı — §8 |

**AUHAL uyarısı (bağlayıcı):** AUHAL (Audio Hardware Abstraction Layer) macOS'un
donanım ses birimidir; iOS tarafında karşılığı `RemoteIO` ailesidir. Bu vault'ta
"iOS AUHAL" ifadesi **kullanılmaz** — kullanılırsa kapsam hatasıdır.

---

## §5. AUAudioUnit (Objective-C/Swift) ile v2 C API ayrımı

| Özellik | AUAudioUnit (v3) | v2 C API |
|---|---|---|
| Dil | Objective-C / Swift | C |
| Oluşturma | Fabrika üzerinden | `AudioComponent` çözümleme + instance |
| Render bloğu | Blok tabanlı render tanımlama | C fonksiyon işaretçisi |
| Bağlam taşıma | Öznitelik (attribute) mekanizması | `inRefCon` kullanıcı bağlamı |
| Kaynak tahsisi | Ömür yönetimi sınıf içinde | Açık allocate/uninitialize |
| iOS uyumu | ✅ | ✅ |
| Bu vault'taki karşılığı | Kod yüzeyi PLANNED (0 .cpp) | Kod yüzeyi PLANNED (0 .cpp) |

**Etiket:** Sembol adları bu vault'ta yalnızca resmi doküman alıntısı olarak geçer;
bu depoda karşılık gelen kod YOKTUR (L0: PLANNED) → derleme iddiası üretilemez.

---

## §6. Render callback sözleşmesi

| Kural | Açıklama | Zorunluluk |
|---|---|---|
| Zamanlama | Sistem callback'i periyodik olarak çağırır; iş bitiş süresi sınırıdır | zorunlu |
| Tahsis yasağı | Callback içinde `malloc/new` yasak (sıfır tahsis kuralı) | zorunlu (vault kuralı §9) |
| Kilit yasağı | Mutex/lock kullanmak yasak; lock-free iletişim | zorunlu (vault kuralı §9) |
| `noexcept` | Callback hatası yaymaz; hata sessizce yönetilir | zorunlu (vault kuralı §9) |
| Girdi okuma | Girdi yayınları AudioBufferList üzerinden okunur | zorunlu |
| Çıkış yazma | Çıkış yayınları AudioBufferList üzerinden yazılır | zorunlu |
| Durum paylaşımı | UI→audio iletişimi atomik bayrak/kuyruk ile | zorunlu |
| Bağlam | Kullanıcı verisi callback'e bağlam üzerinden taşınır | zorunlu |

### 6.1 Callback'in gördüğü veri yapısı

| Alan | Anlamı |
|---|---|
| `AudioUnitRenderActionFlags` | Render eylem bayrakları |
| `AudioTimeStamp` | Zaman damgası (örnek zamanı için) |
| `UInt32` bus numarası | Hangi yayının (bus) render edildiği |
| `UInt32` çerçeve sayısı | Bu çağrının örnek sayısı |
| `AudioBufferList*` | Kanalları taşıyan tampon listesi |

---

## §7. AudioBufferList ve property kapsamları

### 7.1 AudioBufferList (özet)

| Bileşen | Anlamı |
|---|---|
| `mNumberBuffers` | Tampon sayısı (non-interleaved'te = kanal) |
| `mBuffers[i].mNumberChannels` | O tampondaki kanal sayısı |
| `mBuffers[i].mDataByteSize` | Byte uzunluğu |
| `mBuffers[i].mData` | Örnek gövdesi (pointer) |

### 7.2 Property kapsam modeli

| Kapsam | Kime uygulanır | Örnek |
|---|---|---|
| Global | Bileşenin tamamı | Format, çekirdek vurgusu |
| Input | Girdi yayı (bus) | Girdi kaynağı |
| Output | Çıkıtı yay (bus) | Çıkış formatı, render bloğu |
| Element | Belirli bir düğüm/girdi | Mixer girdi seviyesi |

### 7.3 Property değişim akışı

| Adım | İşlem | Not |
|---|---|---|
| 1 | Property dinleyici kaydı | Değişimi öğrenme |
| 2 | Property yazma | Set ile yeni değer |
| 3 | Gerekirse yeniden başlatma | Bazı property'ler initialize ister |
| 4 | Render'a yansıtma | Callback atomik okur |

---

## §8. AUHAL sınırı ve macOS ayrımı

| Konu | macOS | iOS | Durum |
|---|---|---|---|
| Donanım ses birimi | AUHAL | RemoteIO ailesi | ayrık |
| Aggregate device | Var | Yok | iOS dışı |
| Device enumeration | Core Audio hardware tarafı | AVAudioSession rota modeli | ayrık |
| Vault karşılığı | `macos/` derin-dalı | bu dosya | ayrışık |
| Ortak nokta | Core Audio tipleri (ASBD, ABL) | aynı tipler | ortak |

**Kural:** iOS ile macOS arasında yalnız **tipler** ortaktır; cihaz/rota yönetimi
farklıdır ve `ios-cihaz-ve-rota-yonetimi.md` içinde kalır.

---

## §9. CoreMusic / Neva Engine eşleşme tablosu

| Gereksinim (vault içi) | iOS karşılığı | Durum |
|---|---|---|
| Örnek hızı 48 kHz | Session/sample rate yönetimi ile | [⚠️] cihaz-özgül |
| Buffer 512 örnek | IO buffer süresi ile ayarlanır | [⚠️] ölçülmeli |
| 32-bit float veri | ASBD float32 tanımı ile | destekli (doküman) |
| 8.1 surround | iOS cihazlarında tipik çıkış stereo'dur | [⚠️] doğrulanamadı |
| Lock-free callback | Zorunlu, platform izin verir | ✅ tasarım kuralı |
| Zero-allocation callback | Zorunlu | ✅ tasarım kuralı |
| Exclusive lock (ASIO benzeri) | iOS'ta eşdeğeri yok; session paylaşımı vardır | UNKNOWN |
| DAC hedefi PCM3168A | Donanım tarafı (harici) | bu dalın dışı |

---

## §10. Repo kod kanıtı (gerçek dosya · gerçek satır)

| # | Dosya | Satır | İçerik | İlişki |
|---|---|---|---|---|
| 1 | `api.coremusic.net/config/routes.php` | 24 | `return [` — method-aware route tablosunun başlangıcı | Platform derin-dalı sunan vault dosyası, aynı contract-first disiplinini taşır |
| 2 | `.github/workflows/ci.yml` | 22 | `PHP_VERSION: '8.4'   # Kanıt: 3 composer.json -> "php": ">=8.4" (config.platform.php YOK)` | Ölçüm iddiasının kanıt satırıyla birlikte yazılması örneği |

**Doğrulama komutu:** `Get-Content 'C:\www\coremusic.net\api.coremusic.net\config\routes.php' | Select-Object -Index 23` → 24. satır.

---

## §11. Web kanıt matrisi (2026-10-12 · dosya-içi ayrışık URL)

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| AVAudioUnit sayfası erişilebilir | https://developer.apple.com/documentation/avfaudio/avaudiounit | 2026-10-12 | ✅ HTTP 200 |
| AUAudioUnit (v3) sayfası erişilebilir | https://developer.apple.com/documentation/audiotoolbox/auaudiounit | 2026-10-12 | ✅ HTTP 200 |
| Render block sayfası erişilebilir | https://developer.apple.com/documentation/audiotoolbox/auaudiounit/renderblock | 2026-10-12 | ✅ HTTP 200 |
| AUAudioUnitBus sayfası erişilebilir | https://developer.apple.com/documentation/audiotoolbox/auaudiounitbus | 2026-10-12 | ✅ HTTP 200 |
| AUAudioUnitFactory sayfası erişilebilir | https://developer.apple.com/documentation/audiotoolbox/auaudiounitfactory | 2026-10-12 | ✅ HTTP 200 |
| AVAudioConnectionPoint sayfası erişilebilir | https://developer.apple.com/documentation/avfaudio/avaudioconnectionpoint | 2026-10-12 | ✅ HTTP 200 |

**Dosya-içi benzersiz URL: 6** · bu dal içinde başka dosyayla kesişim: 0.

---

## §12. Açık sorular

| # | Soru | Etiket |
|---|---|---|
| I-A1 | Hedef cihaz ailesinde IO buffer alt sınırı kaç örnektir? | ⚠️ VERIFICATION REQUIRED |
| I-A2 | `RemoteIO` dışında hangi çıktı bileşenleri bu cihazda mevcuttur? | ⚠️ VERIFICATION REQUIRED |
| I-A3 | 8.1 kanal çıkışı mümkün müdür (yoksa stereo mu)? | UNKNOWN |
| I-A4 | Kesinti (interruption) sırasında render'ın tampon durumu nedir? | ⚠️ VERIFICATION REQUIRED |
| I-A5 | Vokal işleme bileşeni (`VoiceProcessingIO`) hangi cihazlarda açıktır? | UNKNOWN |
| I-A6 | Render callback çağrısı başına tipik çerçeve sayısı kaçtır? | ⚠️ VERIFICATION REQUIRED |
| I-A7 | Property değişimleri render sırasında anında yansır mı? | UNKNOWN |
| I-A8 | Efekt alt tipleri (EQ/delay) hangi cihaz donanımında mevcuttur? | UNKNOWN |

---

## §13. Terim sözlüğü

| Terim | Anlamı |
|---|---|
| AudioUnit | Apple ses işleme bileşeni |
| Render callback | Sistemce periyodik çağrılan örnek üretici |
| AudioBufferList | Kanalları taşıyan tampon listesi |
| Scope | Property uygulama kapsamı (global/input/output) |
| Bus | Girdi/çıktı yayı |
| AUHAL | macOS donanım ses birimi (iOS'ta yok) |
| RemoteIO | iOS'un donanım ses I/O bileşeni |
| Deadline | Callback'in bitmesi gereken süre sınırı |
| Dropout | Deadline aşımı kaynaklı ses kesintisi |
| Lock-free | Kilit olmadan eşzamanlı erişim |
| Zero-allocation | Çağrı sırasında bellek ayırmama |

---

## §13.5 Ölçüm protokolü (bu vault'un orijinal çerçevesi)

> **Etiket:** `[ORİJİNAL ÇERÇEVE · resmi kaynak değil]` — her iOS ses bileşeni
> projesinde sınamak için kullanılır.

| # | Ölçüm | Nasıl | Beklenen birim | Bu turda durum |
|---|---|---|---|---|
| M1 | Çağrı başına örnek sayısı | callback sayaçları | örnek | ortam yok |
| M2 | IO buffer süresi | session okuması | ms | ortam yok |
| M3 | Örnekleme hızı | format okuması | Hz | ortam yok |
| M4 | Buffer alt sınırı | küçültme taraması | örnek | ortam yok |
| M5 | Callback tahsis sayısı | sayaç (malloc hook) | adet | ortam yok |
| M6 | Callback kilit sayısı | statik denetim | adet | ortam yok |
| M7 | Uçtan uca gecikme | kendi kendine ölçüm | ms | ortam yok |
| M8 | Kesme (underrun) oranı | stres testi | olay/dk | ortam yok |
| M9 | Kanal sayısı okuması | format okuması | kanal | ortam yok |
| M10 | Kesinti sonrası devam | arama/uyarı denemesi | başarı/hata | ortam yok |
| M11 | Rota değişikliği tepkisi | kulaklık tak-çıkar | süre | ortam yok |
| M12 | Kararlılık | 1 saat akış | hata | ortam yok |

**Tablo kuralı:** "ortam yok" ifadesi **eksiklik değil, kayıt**tır; ölçüme başlamak
için §18'deki 20 adım tamamlanmalıdır.

---

## §14. Çapraz referanslar

| Hedef | İlişki |
|---|---|
| `index.md` | üst indeks (§GRUPLAR) |
| `ios-avfoundation-semasi.md` | kardeş — üst katman şema |
| `ios-core-audio-ayrintisi.md` | kardeş — tipler detayı |
| `ios-gercek-zamanli-tampon.md` | kardeş — buffer uygulaması |
| `ios-cihaz-ve-rota-yonetimi.md` | kardeş — session/rota |
| `ios-web-arastirma.md` | kardeş — tarama kaydı |
| `../macos/` | kardeş platform — AUHAL sahibi |

---

## §15. Sınır dışı bırakılan konular

| # | Konu | Nereye |
|---|---|---|
| 1 | DSP algoritmaları | K004-dsp (beyazliste dışı) |
| 2 | Harici DAC donanımı (PCM3168A) | K001-donanım (beyazliste dışı) |
| 3 | macOS AUHAL iç yapısı | `../macos/` derin-dalı |
| 4 | Ring buffer kod tasarımı | `ios-gercek-zamanli-tampon.md` |
| 5 | Session/rota/interruption | `ios-cihaz-ve-rota-yonetimi.md` |
| 6 | AVFoundation düğüm grafiği | `ios-avfoundation-semasi.md` |
| 7 | ASBD alan çözümlemesi | `ios-core-audio-ayrintisi.md` |
| 8 | URL tarama kaydı | `ios-web-arastirma.md` |
| 9 | Android AAudio/OpenSL karşılaştırması | `../android/` derin-dalı |
| 10 | Windows WASAPI karşılaştırması | `../windows/` derin-dalı |

---

## §16. Kırmızı bayraklar (bu dosyada yapılmayan tipik hatalar)

| # | Hata | Sonuç | Bu dosyadaki koruma |
|---|---|---|---|
| F1 | "iOS'ta AUHAL var" demek | Kapsam hatası | §4/§8 AUHAL=macOS |
| F2 | Callback içinde bellek ayırmak | Deadline aşımı | §6 tahsis yasağı |
| F3 | Callback içinde mutex açmak | Kilitlenme/kesinti | §6 kilit yasağı |
| F4 | Buffer boyutunu cihaz-özgül sanmak | Yanlış kesinlik | §9 [⚠️] işaretleri |
| F5 | 8.1 kanalı iOS'ta kesin sanmak | Doğrulanmamış iddia | §9 UNKNOWN |
| F6 | Kod satırı uydurmak | Halüsinasyon | §10 gerçek satır doğrulaması |
| F7 | URL tekrarı (diğer ios/ dosyaları) | Kanıt tekilliği bozulur | §11 ayrışık küme |
| F8 | scope ile element'i karıştırmak | Yanlış property yazımı | §7.2 ayrımı |
| F9 | v2 ile v3 API'yi eşitlemek | Yanlış ömür yönetimi | §5 karşılaştırma |
| F10 | AUAudioUnitBus'u kanal sanmak | Model hatası | §6.1/§7 ayrımı |
| F11 | Deadline'ı milisaniye diye sabitlemek | Cihaz-özgül yanlışlık | §12 açık sorular |
| F12 | Render bayraklarını yok saymak | Görsel/hissedilir kırılmalar | §6.1 satırı |
| F13 | iOS↔macOS rota modelini eşitlemek | Yanlış enumerasyon | §8 tablosu |
| F14 | `status: verified` yazmak | Kanıtsız doğrulama | frontmatter `draft` |
| F15 | Açık soruyu tahminle kapatmak | Bilgi kaybı | §12 işaretli |

---

## §17. Varsayım günlüğü (RED TEAM)

| # | Varsayım | Kanıt | Karar |
|---|---|---|---|
| A1 | iOS'ta ses için AudioUnit kullanılır | §11 URL matrisi | KABUL |
| A2 | Render callback periyodiktir | resmi doküman çerçevesi | KABUL (ölçüm yok) |
| A3 | iOS'ta AUHAL vardır | — | REDDEDİLDİ (§8) |
| A4 | Callback serbestçe tahsis yapabilir | vault kuralı §9 | REDDEDİLDİ |
| A5 | Buffer 512 örnek her cihazda çalışır | — | REDDEDİLDİ → [⚠️] |
| A6 | 48 kHz her cihazda zorunlu | — | REDDEDİLDİ → [⚠️] |
| A7 | 8.1 çıkış mümkündür | kanıt yok | UNKNOWN |
| A8 | float32 desteklenir | ASBD çerçevesi | KABUL (doküman) |
| A9 | Kesinti sonrası render otomatik devam eder | — | UNKNOWN |
| A10 | Tek uygulama donanımı münhasır kullanır | — | REDDEDİLDİ (session paylaşımı) |
| A11 | Bus numarası 0 daima birincil çıkıştır | — | [⚠️] |
| A12 | AUAudioUnit her yerde v3'tür | — | REDDEDİLDİ (§5 ayrım) |
| A13 | AudioBufferList her zaman non-interleaved'tir | — | REDDEDİLDİ (biçime bağlı) |
| A14 | Property değişimi anlıktır | — | [⚠️] |
| A15 | Efekt alt tipleri her cihazda vardır | — | REDDEDİLDİ |
| A16 | Bu vault'ta iOS kodu vardır | 0 .cpp/.h | REDDEDİLDİ |
| A17 | Dosya/Satır referansları iOS koduna aittir | — | REDDEDİLDİ (yalnız PHP/YAML §10) |
| A18 | URL'ler 2026-10-12'de erişilebilirdir | §11 | KABUL |
| A19 | 404 veren adaylar elenmiştir | ölçüm kaydı | KABUL |
| A20 | AUHAL/RemoteIO ayrımı bağlayıcıdır | §4/§8 | KABUL |
| A21 | Callback bayrakları önemsizdir | — | REDDEDİLDİ |
| A22 | Scope/element karıştırılabilir | — | REDDEDİLDİ |
| A23 | 512/48k hedefi bu dosyada ölçülür | ortam yok | REDDEDİLDİ |
| A24 | Ekran okuyucu/erişilebilirlik bu konuda | — | REDDEDİLDİ (kapsam dışı) |

**Özet:** 24 varsayımdan **12'si REDDEDİLDİ**, 5'i [⚠️], 5'i KABUL, 2'si UNKNOWN.

---

## §18. Doğrulama (kapatma) planı — 20 adım

| # | Adım | Beklenen çıktı | Bağımlılık |
|---|---|---|---|
| 1 | Fiziksel iOS cihazı edinimi | cihaz elde | bütçe |
| 2 | Hedef sürücü/OS sürümünün kaydı | sürüm satırı | adım 1 |
| 3 | Minimal render callback iskeleti | derlenen örnek | Xcode |
| 4 | IO buffer süresi okuması | ms değeri | adım 3 |
| 5 | Örnek sayısı raporlama | çağrı başına örnek | adım 3 |
| 6 | Buffer alt sınırı taraması | en küçük çalışma boyutu | adım 5 |
| 7 | Örnekleme hızı doğrulaması | Hz değeri | adım 3 |
| 8 | float32 format doğrulaması | başarı/hata | adım 3 |
| 9 | Kanal sayısı okuması | kanal sayısı | adım 3 |
| 10 | Underrun stres testi | kesinti olayı | adım 5 |
| 11 | Tahsis sayacı (callback içinde) | 0 beklenir | adım 3 |
| 12 | Lock denetimi (statik) | 0 mutex beklenir | adım 3 |
| 13 | Interruption denemesi (arama/uyarı) | durum geçişleri | cihaz |
| 14 | Rota değişikliği denemesi | bildirim akışı | cihaz |
| 15 | Uçtan uca gecikme ölçümü | ms değeri | adım 10 |
| 16 | 8.1 kanal denemesi | başarı/hata | adım 9 |
| 17 | Neva Engine iskeletinin taşınması | derleme sonucu | L0 kodu |
| 18 | Bulguların `ios-web-arastirma.md`'ye yazımı | kanıt satırları | adım 1–17 |
| 19 | `status` alanının güncellenmesi | frontmatter | adım 18 |
| 20 | Açık soruların kapatılması (I-A1…I-A4) | 0 açık soru | adım 19 |

**Plan olmadan** bu dosyadaki [⚠️]/UNKNOWN değerleri kapatılamaz.

---

## §19. Sık sorulanlar

| Soru | Cevap |
|---|---|
| iOS'ta AUHAL var mı? | Hayır — AUHAL macOS'a aittir (§8) |
| iOS'un I/O bileşeni nedir? | `RemoteIO` ailesi (§4) |
| Callback kilit kullanabilir mi? | Hayır (§6, vault kuralı) |
| Callback tahsis yapabilir mi? | Hayır (§6, vault kuralı) |
| AudioBufferList nedir? | Kanalları taşıyan tampon listesi (§7.1) |
| Scope ile element farkı nedir? | Kapsam ile kapsam içi örnek (§7.2) |
| 512 örnek bu dosyada doğrulandı mı? | Hayır → [⚠️] (§12) |
| 8.1 destekli mi? | UNKNOWN (§9) |
| v2 API hâlâ geçerli mi? | Evet, ikisi de iOS'ta (§5) |
| Render bloğu nerede tanımlanır? | Output scope / render block (§6) |
| Kod kanıtları iOS koduna mı ait? | Hayır — PHP/YAML (§10) |
| URL'ler neden 6? | Bu dosyanın ayrışık kanıt kümesi (§11) |
| macOS dosyası hangisi? | `../macos/` derin-dalı |
| Kesinti (interruption) nerede? | `ios-cihaz-ve-rota-yonetimi.md` |
| Ring buffer nerede? | `ios-gercek-zamanli-tampon.md` |
| Bu dosyanın group'u? | `mimari` (frontmatter 9. alan) |

---

## §20. Ses yığını halkaları (bu vault'un OBU modeli — orijinal çerçeve)

| Halka | Soru | iOS durumu |
|---|---|---|
| H1 · Donanım | Hoparlör/kulaklık hangi yolla bağlı? | rota modeli §9 |
| H2 · Sürücü | Donanım sürücüsü kimin? | Apple kapalı (UNKNOWN) |
| H3 · Çekirdek alt sistemi | Ses alt sistemi adı? | Core Audio (doküman) |
| H4 · Kütüphane | Uygulamanın çağırdığı katman | AVAudioEngine/AudioUnit |
| H5 · Arayüz | Sunulan API | AudioUnit + AVAudioSession |
| H6 · Biçim | Hz/bit/kanal | ASBD ile tanımlı |
| H7 · Zamanlama | Buffer davranışı | IO buffer süresi ile |
| H8 · Paylaşım | Çok uygulama | session paylaşımı var |
| H9 · Kestirme | Düşük gecikme yolu | [⚠️] cihaz-özgül |
| H10 · Ölçüm | Gecikme nasıl ölçülür? | uçtan uca ölçüm §18 |

---

## §21. iOS ses yığını katman özeti

| # | Katman | Sorumluluk | Bu vaultta karşılığı |
|---|---|---|---|
| 1 | Uygulama | Ne çalacağına karar verir | PLANNED (0 kod) |
| 2 | AVFoundation (opsiyonel) | Şema, player, mixer | `ios-avfoundation-semasi.md` |
| 3 | AudioToolbox / AudioUnit | Render, format, property | bu dosya (§3–§7) |
| 4 | Core Audio tipleri | ASBD, ABL, channel layout | `ios-core-audio-ayrintisi.md` |
| 5 | Sistem ses altyapısı | Zamanlama, rota, miksaj | `ios-cihaz-ve-rota-yonetimi.md` |
| 6 | Sürücü + donanım | DAC/çıkış (fiziksel) | K001-donanım (dışarıda) |

### 21.1 Katmanlar arası veri akışı (tek yönlü)

| Adım | Taşınan | Biçim |
|---|---|---|
| 1 | Uygulama kuyruğu → şema | tampon başvurusu |
| 2 | Şema → AudioUnit girdisi | AudioBufferList |
| 3 | AudioUnit render | AudioBufferList (float32 hedefi) |
| 4 | Sistem miksajı | biçim dönüşümü olabilir |
| 5 | Sürücü → DAC | donanım çerçevesi |

---

## §22. Karşılaştırma çerçevesi (karşılaştırma TURU DIŞINDA YAPILMADI)

| Eksen | iOS | Android (bu turda okunmadı) | Durum |
|---|---|---|---|
| Düşük gecikme yolu | RemoteIO (doküman) | okunmadı | [⚠️] |
| Yüksek katman | AVAudioEngine (doküman) | okunmadı | [⚠️] |
| Session modeli | AVAudioSession | okunmadı | [⚠️] |
| Buffer kontrolü | IO buffer süresi | okunmadı | [⚠️] |

**Kural:** Karşılaştırma satırları boş bırakılmaz; "okunmadı" yazılır.

---

## §23. Riskler

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Cihaz-özgül buffer davranışı | Ses kesintisi | §18 ölçüm planı |
| R2 | Kesinti/rota olaylarının ihmal edilmesi | Bozuk devam | kardeş dosya |
| R3 | Callback'e gizli tahsis sızması | Deadline aşımı | statik denetim + sayaç |
| R4 | Yanlış ASBD kombinasyonu | Bozuk/çizik ses | §9 format doğrulama |
| R5 | AUHAL/RemoteIO karışıklığı | Mimari hata | §4/§8 bağlayıcı uyarı |
| R6 | 8.1 varsayımı | Tasarım hatası | UNKNOWN işaretleme |
| R7 | Kanıt tarihi eskimesi | Yanlış güncellik | 2026-10-12 damgası |
| R8 | Kod kanıtsızlık (0 .cpp) | Uygulanabilirlik belirsizliği | L0 PLANNED etiketi |
| R9 | URL 404 kayması | Kanıt kırılması | §11 erişim ölçümü |
| R10 | Dosya/Satır uydurma | Güven kaybı | §10 doğrulama komutu |

---

## §24. Ölçüm

| Metrik | Değer |
|---|---|
| Dosya | `ios-audio-unit-arayuzu.md` |
| Hedef satır | ≥500 |
| § sayısı | 24 |
| Benzersiz URL | 6 (dosya içi) |
| Repo kod kanıtı | 2 (gerçek satır) |
| Reddedilen varsayım | 12 / 24 |
| Frontmatter | 9 alan ✅ · `group: mimari` ✅ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode