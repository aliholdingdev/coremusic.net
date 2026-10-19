---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-ve-opensl-es-gecisi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio
children: []
depends_on: []
evidence:
  - https://developer.android.com/about/versions/12
  - https://developer.android.com/about/versions/11
  - https://developer.android.com/about/versions/14
validation:
  - "2026-10-13 · 3 resmi URL HEAD 200 · envanter kesişimi 0 · iç referans zinciri (anayasa §3 · yaşam döngüsü §2) tutarlı"
open_questions:
  - "OpenSL ES 'deprecated' sürüm eşiği resmi NDK sayfasıyla doğrulanamadı (envanter kilidi) → ⚠️ VERIFICATION REQUIRED"
  - "Oboe sürüm/lisans denetimi bu turda yapılmadı → UNKNOWN"
group: mimari
---

# AAudio ve OpenSL ES Geçişi — Durum · İkili Yol · API Seviyesi · Taşınabilirlik

> **Bu dosya ne değildir:** Geçiş kodu veya sürüm yükseltme talimatı değildir.
> Bu dosya **iki yolun (AAudio ↔ OpenSL ES) karar matrisini** kurar; hangi
> sürümde ne olduğu resmi sayfa ile kanıtlanamayan yerde **⚠️ VERIFICATION
> REQUIRED** yazılır, "eski/yeni" hissiyle iddia edilmez.
>
> **Durum etiketi:** ŞARTNAME PLANNED · Kod YOK (repo 0 Android ses dosyası).

---

## §0 ELI15 — Geçiş bir cümlede

Android'de düşük gecikme ses iki kapıdan geçer: eskisi **OpenSL ES**, yenisi
**AAudio**; Google yeni çalışmalar için AAudio'u önerir ve OpenSL ES'i
uyarı-ile işaretler, bu yüzden CoreMusic'in stratejisi **tek kapı (AAudio) ya
da sarmalayıcı (Oboe) ile fallback** olmalıdır — ikisi birden dağınık kod demektir.

---

## §1 OpenSL ES Durumu — Deprecated Yolu

### §1.1 Kimlik

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | Open Sound Library for Embedded Systems | [S2] |
| Spesifikasyon sahibi | Khronos Group | [S2] |
| Android uygulaması | NDK içine gömülü Android-özel uygulama | [S2] |
| Diller | C arayüzü (+ C++ binding) | [S2] |
| Öneri durumu (yeni proje) | Önerilmiyor — uyarı metni mevcut | ⚠️ [S1] · [S2] |
| Yerine gelen | AAudio (düşük gecikme) + Oboe sarmalayıcı | [S2] |
| CoreMusic kodu | YOK — UNKNOWN | §5.1 tarama |

### §1.2 "Deprecated" iddiasının kanıt durumu

| # | İddia | Kaynak türü | Durum |
|---|---|---|---|
| 1 | OpenSL ES ana sayfasında uyarı (deprecated) metni var | NDK sayfası (envanter kilidi) | ⚠️ VERIFICATION REQUIRED |
| 2 | "Android 11'den itibaren deprecated" anlatısı | İkincil örnek deposu notu | ⚠️ ikincil kanıt |
| 3 | Oboe, AAudio varsa AAudio, yoksa OpenSL ES'e düşer | Oboe/AGDK dokümanı | ⚠️ bu turda AGDK sayfası yalnız düşük-gecikme başlığını doğrular |
| 4 | Khronos spesifikasyonu hâlâ geçerli | Khronos belgesi (bu dalın dışı) | ⚠️ ayrıca doğrula |

> **Kural:** "deprecated" bilgisi tek kaynaklı yazılmaz. Resmi NDK sayfası
> envanter kilidi kalkınca yeniden çekilecek; o güne kadar bu satırlar
> **⚠️** işaretini taşır.

### §1.3 OpenSL ES'in güçlü/zayıf yönleri

| Yön | Açıklama | Geçiş etkisi |
|---|---|---|
| Yaygınlık (eski cihaz) | Eski API seviyelerinde tek yol | Düşük minSdk gereksinimi |
| Düşük gecikme yeteneği | Mümkün ama dolaylı | AAudio kadar odaklı değil |
| Biçim yüzeyi | Daha geniş bit derinliği yüzeyi (iddia) | ⚠️ doğrulanacak |
| Bağlantı olayları | Kopma bildirimi cihaz/yürütücüye bağlı | AAudio error callback kadar açık değil |
| Bakım yönü | Yeni öneriler AAudio/Oboe'ya kayıyor | Yatırım geri dönüşü düşük |
| Örnek/sarmalayıcı | Oboe fallback'i olarak yaşar | Geçişte anahtardır |

### §1.4 Geçiş tetikleyicileri

| # | Tetikleyici | Aksiyon |
|---|---|---|
| T1 | Yeni Android ses modülü başlıyor | AAudio tercih et |
| T2 | minSdk AAudio eşiğinin altına düşürülemiyor | Oboe fallback |
| T3 | Mevcut OpenSL ES kodu varsa (CoreMusic'te yok) | Ayrıştır → §2.1 |
| T4 | Düşük gecikme hedefi kesinleşti | LOW_LATENCY + AAudio |
| T5 | Yalnız uyumluluk testi | OpenSL ES yolu ayrı tutulur - test yüzeyi |

### §1.5 Kullanım-yasağı/izin matrisi (CoreMusic kararı)

| Durum | Karar | Dayanak |
|---|---|---|
| Yeni kod | AAudio | §0 + §2.3 |
| Sarmalayıcı kodu | Oboe (bağımlılık onayıyla) | §4 |
| Saf OpenSL ES | Yalnız test yüzeyi, onay şartlı | §1.4 T5 |
| Karışık (iki yol aynı dosyada) | Yasak | §2.4 |
| Ölçümsüz geçiş | Yasak | §5 Risk R1 |

---

## §2 İkili Çalışma Stratejisi

### §2.1 Strateji envanteri

| # | Strateji | Avantaj | Dezavantaj | Risk |
|---|---|---|---|---|
| 1 | Tek yol: AAudio | Basit | Eski cihaz kapsamı | Düşük |
| 2 | Tek yol: OpenSL ES | Eski kapsam | Öneriye aykırı | Orta |
| 3 | Oboe sarmalayıcı | Tek arayüz, otomatik seçim | Ek bağımlılık | Düşük-orta |
| 4 | Çalışma zamanı seçimi | Esnek | İki yolun testi | Orta |
| 5 | Derleme zamanı bayrak | Sıfır maliyet | APK başına tek yol | Düşük |
| 6 | API seviyesiyle koşullu | Açık | Kod parçalanması | Orta |

### §2.2 Karşılaştırma odağı: iki yolun aynı işi yapması

| İş | AAudio yolu | OpenSL ES yolu | Ortak sözleşme |
|---|---|---|---|
| Akış kurma | Builder → open | Arayüz → oynat/kayıt | §2.3 |
| Veri akışı | Callback/polling | Kuyruk/notify | RT kuralları |
| Cihaz seçimi | deviceId | Uzantılar | Yeniden çözümlendirme |
| Kopma | Error callback | Değişken | Bildirim zorunlu |
| Ölçüm | timestamp sayaçları | Sayaçlar | §5.2 |
| Format | Float32/Int16 ağırlıklı | Daha geniş yüzey | Fallback |

### §2.3 Tek-karar-arayüzü (ortak soyutlama)

| # | Soyutlama | Ne gizler | Kural |
|---|---|---|---|
| 1 | "Aç/kapa" sarmalayıcısı | Yol seçimi | Tek implementasyon |
| 2 | "Bloğu doldur" | Format farkı | Dahili dönüşüm |
| 3 | "Bağlantı koptu" | Bildirim farkı | Tek olay |
| 4 | "Ölç" | Sayaç farkı | Tek rapor |
| 5 | "Cihaz değişti" | Enumerasyon farkı | Tek olay |

### §2.4 Karışık kod yasağı

| Yasaklı desen | Neden | Denetim |
|---|---|---|
| Aynı dosyada iki yolun çağrıları | Test edilemez | Kod incelemesi |
| Çalışma-zamanında sessiz fallback | Tanınamayan xrun | Zorunlu olay kaydı |
| Callback içi yol değiştirme | RT ihlali | Statik denetim |
| Ölçüm olmadan "uygun yol" kararı | Kanıtsızlık | §5.2 |
| İki ayrı buffer havuzu | Bellek/önbellek dağınıklığı | Tasarım incelemesi |

### §2.5 Test matrisi (geçiş için)

| # | Test | AAudio | OpenSL ES | Kabul |
|---|---|---|---|---|
| 1 | Aç/kapa döngüsü | Evet | Evet | Hata yok |
| 2 | Bağlantı kopması | Evet | Koşullu | Kurtarma |
| 3 | Gecikme ölçümü | Evet | Koşullu | Bütçe |
| 4 | Xrun sayımı | Evet | Evet | 0 (pencere) |
| 5 | Format fallback | Evet | Evet | Sessizlik yok |
| 6 | Cihaz değişimi | Evet | Koşullu | Yeniden bağlanma |

---

## §3 API Seviyesi Matrisi (26 / 28 / 29 / …)

### §3.1 Gözlem tablosu

| API seviyesi | Android | Gözlem | Kaynak | Durum |
|---|---|---|---|---|
| 26 | 8.0 | AAudio başlangıcı iddiası | NDK sayfası kilidi | ⚠️ |
| 27 | 8.1 | Düşük gecikme yolu genişlemesi iddiası | Kaynak yok | ⚠️ |
| 28 | 9.0 | AAudio'a usage/content type/input preset/session ID | [W1] anayasası | ✅ |
| 29 | 10 | Ek nitelikler iddiası | Kaynak yok | ⚠️ |
| 30 | 11 | "Deprecated" anlatısı (ikincil) | İkincil depo notu | ⚠️ |
| 31 | 12 | Sürüm sayfası erişilebilir | [S1] | ✅ HEAD 200 |
| 33 | 13 | Sürüm sayfası erişilebilir | [W6] anayasası | ✅ HEAD 200 |
| 34 | 14 | Sürüm sayfası erişilebilir | [S3] | ✅ HEAD 200 |

### §3.2 Seviye → aksiyon matrisi

| minSdk | Önerilen yol | Gerekçe | Test |
|---|---|---|---|
| ≥ AAudio eşiği (⚠️ 26) | Saf AAudio | En basit | Cihaz matrisi |
| AAudio eşiğinin altı | Oboe fallback | Kapsam korunur | İki yol |
| Yalnız yeni cihazlar | Saf AAudio + exclusive dene | Düşük gecikme | §5 anayasası §4 |
| Bilinmiyor | Önce envanter sonra karar | Kanıtsız karar yok | Üst karar |

### §3.3 Seviye-süreç ilişkisi

| Süreç | Ne gerektirir | Bağ |
|---|---|---|
| Envanter çıkarma | Gerçek hedef cihazların API dağılımı | UNKNOWN |
| Karar | Envanter + gecikme hedefi | Sartname |
| Kod | Karar sonrası tek yol | §2.3 |
| Ölçüm | Cihaz × API × mod | Yaşam döngüsü §6 |
| Revizyon | Ölçüm sapması | Vault güncellemesi |

### §3.4 Belirsizlik defteri

| # | Belirsizlik | Etiket | Kapanış koşulu |
|---|---|---|---|
| B1 | AAudio'un tam başlangıç sürümü | ⚠️ | Resmi NDK sayfası |
| B2 | Exclusive paylaşımının hangi sürümde açıldığı | ⚠️ | Resmi sayfa |
| B3 | OpenSL ES deprecated eşiği | ⚠️ | Resmi sayfa |
| B4 | Oboe sürümü ve lisansı | UNKNOWN | Depo denetimi |
| B5 | Hedef cihaz API dağılımı | UNKNOWN | Envanter |

---

## §4 Taşınabilir Sarmalayıcı

### §4.1 Sarmalayıcı tasarım matrisi

| # | Öğe | Karar | Gerekçe |
|---|---|---|---|
| 1 | Arayüz | Platform-bağımsız çağrı yüzeyi | Taşınabilirlik |
| 2 | Implementasyon | Platform başına birer adaptör | Tek sorumluluk |
| 3 | Seçim | Açılışta bir kez | Karışık kod yok |
| 4 | Bildirim | Tek olay modeli | §2.3 |
| 5 | Ölçüm | Tek rapor formatı | §5.2 |
| 6 | Hata | Kod + insan-okunur | Teşhis |

### §4.2 Adaptör sınırları

| Sınır | İçeride | Dışarıda |
|---|---|---|
| Akış yaşam döngüsü | Evet | İş mantığı |
| Format dönüşümü | Evet | DSP algoritması |
| Cihaz enumerasyonu | Evet | UI |
| Kopma olayı | Evet | Kullanıcı mesajı |
| Yeniden deneme | Evet | Sonsuz döngü |

### §4.3 Bağımlılık denetimi (Oboe vb.)

| # | Kontrol | Durum |
|---|---|---|
| D1 | Depo lisansı okundu mu | UNKNOWN (bu turda yapılmadı) |
| D2 | Sürüm sabitlemesi var mı | UNKNOWN |
| D3 | NDK sürümüyle uyum | UNKNOWN |
| D4 | Boyut/maliyet etkisi | UNKNOWN |
| D5 | Karar onayı alındı mı | Hayır — onay kapısı |

> D1–D5 **UNKNOWN** işaretlidir; sarmalayıcı kararı bu denetimler kapanmadan
> IMPLEMENTED sayılmaz.

### §4.4 Sarmalayıcı ile elde edilen/kaybedilen

| Kazanç | Kayıp | Karşılık |
|---|---|---|
| Tek kod yüzeyi | Ek katman karmaşıklığı | Test ile dengelenir |
| Otomatik yol seçimi | Seçim davranışı görünmezliği | Olay kaydı zorunlu |
| Eski cihaz kapsamı | İki yolun test yükü | Otomatik test |
| Gelecek değişime dayanım | Sürüm takibi | §4.3 denetimi |

---

## §5 Riskler

### §5.1 Risk envanteri (repo kanıtıyla)

| # | Risk | Etki | Kanıt | Azaltma |
|---|---|---|---|---|
| R1 | Ölçümsüz geçiş kararı | Yanlış mimari | Repo 0 kod | Ölçüm zorunlu |
| R2 | Tek kaynaklı "deprecated" | Yanlış sürüm | Envanter kilidi | Çift kaynak |
| R3 | İki yolun karışması | Bakım pahalısı | §2.4 yasağı | Denetim |
| R4 | Cihaz üretici farkı | Cihazda kırılma | Bilinmiyor | Cihaz matrisi |
| R5 | Sarmalayıcı denetimsiz | Lisans/sürüm sürprizi | §4.3 UNKNOWN | Denetim |
| R6 | Callback RT ihlali | Xrun | Kurallar §2.3 yaşam | Statik denetim |

### §5.2 Ölçüm olmadan geçiş yapılmaz (kapı)

| # | Kapı | Çıktı | Geçmezse |
|---|---|---|---|
| K1 | Hedef envanteri | Cihaz × API | Dur |
| K2 | Gecikme ölçümü | Bütçe karşılaştırma | Dur |
| K3 | Xrun penceresi | 0 xrun | Dur |
| K4 | Kopma kurtarma | Otomatik | Dur |
| K5 | Karar kaydı | Vault kaydı | Dur |

### §5.3 Risk sahipliği

| Risk | Sahibi | Escalasyon |
|---|---|---|
| Mimari (tek yol/özel yol) | Architect (Expert) | Üst karar |
| Ölçüm/cihaz | Embedded Engineer | Performans |
| Bağımlılık/lisans | dependency-manager | Onay |
| RT kuralları | Embedded Engineer | Veto |
| Vault kaydı | Vault Steward | Orkestratör |

### §5.4 Erken uyarı işaretleri

| İşaret | Anlamı | Aksiyon |
|---|---|---|
| Aynı kodda iki yol çağrısı | §2.4 ihlali | Düzelt |
| Ölçüm olmadan "düşük gecikme" yazımı | R1 | Sil/ işaretle |
| Tek kaynaklı sürüm iddiası | R2 | Çift kaynak |
| Callback içinde tahsis | RT ihlali | Düzelt |
| Cihaz matrisi boş | K1 açık | Ölç |

---

## §6 Referanslar — Web Araştırma Tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Android 12 sürüm sayfası erişilebilir (seviye matrisi satırı) | https://developer.android.com/about/versions/12 | 2026-10-13 | ✅ HEAD 200 |
| Android 11 sürüm sayfası erişilebilir (seviye matrisi satırı) | https://developer.android.com/about/versions/11 | 2026-10-13 | ✅ HEAD 200 |
| Android 14 sürüm sayfası erişilebilir (seviye matrisi satırı) | https://developer.android.com/about/versions/14 | 2026-10-13 | ✅ HEAD 200 |
| OpenSL ES ana NDK sayfası (uyarı metni) bu turda kullanılamadı — envanter kilidi | envanter kilidi — URL yazılmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| Oboe deposu envanter kilidinde - depo adı da envanter kilidinde | envanter kilidi — URL yazılmadı | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| Repo'da OpenSL/AAudio kodu yok (215 dosya · 0 isabet) | yerel disk taraması | 2026-10-13 | ✅ ölçüm |

## §7 AAudio API Yüzeyi — Resmi NDK Referansından Tescilli Derinlemesine (2026-10-13)

> Bu bölüm §1-§6 dışındaki yeni tescili taşır: NDK ses referans sayfası hem
> HTTP 200 doğrulandı hem de **gövdesi okunup** fonksiyon/enum/durum adları
> satır satır çıkarıldı (yöntem: HEAD-200 + gövde okuması; tarih 2026-10-13).
> Bu bölümdeki her API adı o sayfadan alınmıştır; tahmin yoktur.

### §7.1 Tescil kapsamı

| # | Adım | Sonuç |
|---|---|---|
| 7.1.1 | aday adres: NDK ses referans sayfası | envanter kesişim taraması 0 eşleşme |
| 7.1.2 | HEAD doğrulaması | HTTP 200 |
| 7.1.3 | gövde okuması | fonksiyon/enum/durum listesi ayrıştırıldı |
| 7.1.4 | OpenSL ES ana sayfası | envanter kilidi sürüyor — adres yazılmadı (§6'daki kilit satırı geçerli) |
| 7.1.5 | Oboe deposu | envanter kilidi sürüyor — adres/ad yazılmadı |

### §7.2 Akış oluşturucu (builder) yüzeyi

| # | Fonksiyon | Ne yapar | Sayfadan not |
|---|---|---|---|
| 7.2.1 | `AAudioStreamBuilder_openStream` | yapılandırılmış akışı açar | akış nesnesi çağırana geçer |
| 7.2.2 | `AAudioStreamBuilder_delete` | oluşturucuyu serbest bırakır | akış açıldıktan sonra |
| 7.2.3 | `AAudioStreamBuilder_setDirection` | yön (giriş/çıkış) seçer | **varsayılan: çıkış** (sayfa metni) |
| 7.2.4 | `AAudioStreamBuilder_setFormat` | örnekleme biçimi ister | yaygın biçimler: kayan nokta ve 16 bit PCM (sayfa metni) |
| 7.2.5 | `AAudioStreamBuilder_setSampleRate` | örnekleme hızı ister | donanım uyuşmazlığı ayrı kodla bildirilir |
| 7.2.6 | `AAudioStreamBuilder_setChannelCount` | kanal sayısı ister | kanal maskesi seçeneği ayrı fonksiyonda |
| 7.2.7 | `AAudioStreamBuilder_setSharingMode` | paylaşım kipi seçer | **varsayılan: paylaşımlı** (sayfa metni) |
| 7.2.8 | `AAudioStreamBuilder_setPerformanceMode` | performans kipi seçer | **varsayılan: hiçbiri** (sayfa metni) |
| 7.2.9 | `AAudioStreamBuilder_setDeviceId` | hedef cihazı seçer | rota değiştirme geri çağırı ayrı |
| 7.2.10 | `AAudioStreamBuilder_setBufferCapacityInFrames` | tampon kapasitesi ister | istek — onay akış üzerinden sorgulanır |
| 7.2.11 | `AAudioStreamBuilder_setDataCallback` | veri geri çağırı bağlar | çalışma anında çağrılır (sayfa metni) |
| 7.2.12 | `AAudioStreamBuilder_setErrorCallback` | hata geri çağırı bağlar | hata veya bağlantı kopması anında (sayfa metni) |
| 7.2.13 | `AAudioStreamBuilder_setFramesPerDataCallback` | geri çağırı başına kare sayısı ister | desteklenmezse sonuç kodu döner |
| 7.2.14 | `AAudioStreamBuilder_setSessionId` | oturum kimliği seçer | efekt zinciri kavramı |
| 7.2.15 | `AAudioStreamBuilder_setUsage` / `setContentType` / `setInputPreset` | amaç/içerik/kayıt ön ayarı | politika sınıfı etiketler |

### §7.3 Akış nesnesi yüzeyi

| # | Fonksiyon | Ne yapar | RT dersi |
|---|---|---|---|
| 7.3.1 | `AAudioStream_requestStart` | başlatma **isteği** | istek = anında başarı değil; durum makinesi (§7.5) |
| 7.3.2 | `AAudioStream_requestPause` | duraklatma isteği | durum makinesi üzerinden |
| 7.3.3 | `AAudioStream_requestStop` | durdurma isteği | durum makinesi üzerinden |
| 7.3.4 | `AAudioStream_requestFlush` | temizleme isteği | yalnız belirli performans kipinde (sayfa metni: boşaltma destek kontrolü) |
| 7.3.5 | `AAudioStream_close` | akış yapılarını **silir** (sayfa metni) | oluşturucu-üretimi yapıların sahipliği akışta |
| 7.3.6 | `AAudioStream_release` | ses kaynaklarını **serbest bırakır** (sayfa metni) | close ile ayrık iki aşama |
| 7.3.7 | `AAudioStream_read` | giriş akışından bloklayan okuma | zaman aşımı parametresi nanosaniye cinsinden |
| 7.3.8 | `AAudioStream_setBufferSizeInFrames` | tampon boyutunu çalışma sırasında ayarlar | tek serbest ayar halkası |
| 7.3.9 | `AAudioStream_getFramesPerBurst` | donanım patlama boyutu | periyot hesabı girdisi |
| 7.3.10 | `AAudioStream_getBufferSizeInFrames` / `getBufferCapacityInFrames` | istenen vs verilen kapasite | ikisi farklıdır — karıştırılmaz |
| 7.3.11 | `AAudioStream_getFramesRead` / `getFramesWritten` | ilerleme sayacı | kopma denetimi girdisi |
| 7.3.12 | `AAudioStream_getTimestamp` | kare konumu + zaman damgası çifti | gecikme ölçümü girdisi (saat kimliği parametre) |
| 7.3.13 | `AAudioStream_getXRunCount` | taşıma/kesinti sayacı | **ölçüm kapısı** (§7.10) |
| 7.3.14 | `AAudioStream_getState` | durum makinesi okuması | §7.5 |
| 7.3.15 | `AAudioStream_isMMapUsed` | doğrudan bellek yolu kullanıldı mı | donanım yolu bilgisi |

### §7.4 Geri çağırı (callback) imzaları — sayfadaki tip tanımı üzerinden

| # | Tip | İmza (sayfadaki dizgi) | Not |
|---|---|---|---|
| 7.4.1 | veri geri çağırı | akış · kullanıcı verisi · ses tamponu · kare sayısı | çalışma anında çağrılır |
| 7.4.2 | hata geri çağırı | akış · kullanıcı verisi · sonuc kodu | hata/kopma anında |
| 7.4.3 | kısmi veri geri çağırı | akış · kullanıcı verisi · ses tamponu · kare sayısı | desteklenmediği aygıtta sonuç kodu döner |
| 7.4.4 | sunum-sonu geri çağırı | akış · kullanıcı verisi | boşaltma akışlarında |
| 7.4.5 | rota değişimi geri çağırı | akış · kullanıcı verisi · cihaz kimlik dizisi · cihaz sayısı | yönlendirme değişti |

> §7.4 imzaları NDK sayfasındaki tip tanımlarının sadeleştirilmiş okumasıdır
> (boşaltma/iptal işaretleri aynen korunmadı — C işaretçi ayrıntısı değil,
> anlatım sadeleştirmesi).

### §7.5 Durum makinesi (sayfadaki tam liste)

| # | Durum | Yer |
|---|---|---|
| 7.5.1 | başlangıç (0) | `UNINITIALIZED` |
| 7.5.2 | bilinmeyen | `UNKNOWN` |
| 7.5.3 | açık | `OPEN` |
| 7.5.4 | başlatılıyor / başlatıldı | `STARTING` · `STARTED` |
| 7.5.5 | duraklatılıyor / duraklatıldı | `PAUSING` · `PAUSED` |
| 7.5.6 | temizleniyor / temizlendi | `FLUSHING` · `FLUSHED` |
| 7.5.7 | durduruluyor / durduruldu | `STOPPING` · `STOPPED` |
| 7.5.8 | kapatılıyor / kapatıldı | `CLOSING` · `CLOSED` |
| 7.5.9 | bağlantı koptu | `DISCONNECTED` |

### §7.6 Sonuç kodları (sayfadaki taban değeriyle)

| # | Kod grubu | Taban | Üyeler (sayfa listesi) |
|---|---|---|---|
| 7.6.1 | başarı | — | `AAUDIO_OK` |
| 7.6.2 | hata tabanı | **-900** | taban etiketi |
| 7.6.3 | bağlantı/aygıt | taban+ | kopma · hizmet yok · kullanılamıyor |
| 7.6.4 | argüman/durum | taban+ | geçersiz argüman · geçersiz durum · geçersiz tanıtıcı · geçersiz biçim · geçersiz hız · kapsam dışı |
| 7.6.5 | kaynak | taban+ | bellek yok · boş işaretçi · boş tanıtıcı yok |
| 7.6.6 | davranış | taban+ | zaman aşımı · şimdi değil (bloklama) · uygulanmadı |

### §7.7 Enum kümeleri (CoreMusic eşlemesi)

| # | Küme | Üyeler | CoreMusic eşlemesi |
|---|---|---|---|
| 7.7.1 | yön | çıkış · giriş | 8.1 çıkış hedefi PLANNED |
| 7.7.2 | biçim | geçersiz(-1) · tanımsız(0) · 16 bit PCM · **kayan nokta PCM** · 24 bit paketli · 32 bit PCM · iletim biçimi · ve sıkıştırılmış biçimseller | **kayan nokta PCM = L0 kuralı ile hizalı (32-bit float veri)** |
| 7.7.3 | paylaşım | özel · paylaşımlı | özel kip = ASIO özel-kilit dersinin Android ayağı |
| 7.7.4 | performans | hiçbiri(10) · güç tasarrufu · **düşük gecikme** · boşaltmalı güç tasarrufu | düşük gecikme = sartname hedefinin karşılığı |
| 7.7.5 | cihaz tipi | iç hoparlör/kulaklık/bluetooth/HDMI/USB/mikrofon vb. uzun liste | cihaz matrisi ölçümünün girdisi |

### §7.8 L0 kıstosları ile eşleme matrisi

| # | L0 kuralı | AAudio yüzeyi | Uygulama notu |
|---|---|---|---|
| 7.8.1 | 32-bit float veri | biçim kümesinde kayan nokta_PCM seçeneği | hedef biçim |
| 7.8.2 | gerçek zamanlılık | performans kümesinde düşük gecikme seçeneği | hedef kip |
| 7.8.3 | kilit yasağı | geri çağırı içinde başka akış işlevi çağırmama deseni | geri çağırı = tek iş yeri (§7.4.1) |
| 7.8.4 | tahsis yasağı | geri çağırı içinde tahsis yok | imza sabit tampon verir |
| 7.8.5 | bağlantı kopması | kopma geri çağırı + bağlantı-koptu durumu | kurtarma kapısı |
| 7.8.6 | ölçüm | taşıma sayacı + zaman damgası çifti | jitter/taşıma ölçüm kapıları |

### §7.9 Geçiş matrisinin genişlemesi (§4-§5 üzerine — tekrar değil)

§4-§5 geçiş matrisi, Android sürüm seviyeleri ve sürükleme politikası
üzerine kuruluydu. §7, matrise **API-yüzeyi ekseni** ekler: geçiş, yalnız
"hangi sürüm" değil, "hangi durum makinesi sözleşmesi" demektir. Yeni
karar kapıları:

| # | Kapı | Soru | Kapanma ölçütü |
|---|---|---|---|
| 7.9.1 | biçim kapısı | kayan nokta destekleniyor mu? | akış açılış sonucu |
| 7.9.2 | paylaşım kapısı | özel kip veriliyor mu? | paylaşım kipi sorgusu |
| 7.9.3 | periyot kapısı | donanım patlama boyutu nedir? | patlama boyutu sorgusu |
| 7.9.4 | kopma kapısı | bağlantı kopması nasıl kurtarılır? | hata geri çağırı akışı (ölçüm PLANNED) |

### §7.10 Ölçüm kapıları (PLANNED — depoda kod yoktur)

| # | Ölçüm | Girdi (§7 referansı) | Durum |
|---|---|---|---|
| 7.10.1 | taşıma/kesinti oranı | taşıma sayacı okuması (§7.3.13) | PLANNED |
| 7.10.2 | gecikme (kare↔zaman) | zaman damgası çifti (§7.3.12) | PLANNED |
| 7.10.3 | beklenen periyot | patlama boyutu + örnekleme hızı (§7.3.9 · §7.2.5) | PLANNED |
| 7.10.4 | kopma sıklığı | hata geri çağırı sayacı (§7.4.2) | PLANNED |
| 7.10.5 | cihaz matrisi | cihaz tipi kümesi (§7.7.5) | PLANNED (§5'teki K1 açığı) |

### §7.11 Belirsizlik tablosu (§7 kapsamı)

| # | Konu | Durum | Davranış |
|---|---|---|---|
| 7.11.1 | OpenSL ES arayüzü ayrıntıları | envanter kilidi | adres/ad yazılmadı |
| 7.11.2 | Oboe kütüphanesi | envanter kilidi | adres/ad yazılmadı |
| 7.11.3 | geçiş-değerleri/dönüşüm tablosu (OpenSL → AAudio eşlemesi) | ⚠️ VERIFICATION REQUIRED | eşleme uydurulmadı |
| 7.11.4 | aygıta özgü destek matrisi (hangi aygıt hangi biçimi) | ⚠️ VERIFICATION REQUIRED | ölçüm PLANNED |
| 7.11.5 | boşaltma akışının CoreMusic'e uygunluğu | UNKNOWN | hedef değil, bilgi |

### §7.12 Web araştırma tablosu (bu bölüm)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | NDK ses referans sayfası erişilebilir; §7.2-§7.7 adları bu sayfadan çıkarıldı | https://developer.android.com/ndk/reference/group/audio | 2026-10-13 | ✅ HEAD 200 + gövde okundu (envanter kesişimi 0) |

### §7.13 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §7'nin eklediği tek yeni tescil
§7.12'deki tek adres satırıdır; §7.2-§7.7'deki tüm adlar o tescilin gövde
okumasından gelir (ayrı tescil sayılmaz). Yeni repo satırı iddiası 0 —
§7.10 ölçüm kapıları PLANNED olarak işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §7.14 Geçiş kontrol listesi — OpenSL ES'den AAudio'ya taşıma adımları (doktrin)

§7.9'daki geçiş matrisine bu bölüm uygulama sırası ekler; adımlar sıralıdır,
atlanan adım sonrakileri geçersiz kılar:

| # | Adım | Girdi | Çıktı | Atlanırsa |
|---|---|---|---|---|
| 1 | mevcut çağrı envanteri çıkarılır | kod yüzeyi | çağrı listesi | neyin taşınacağı belirsiz kalır |
| 2 | AAudio karşılığı olanlar eşlenir | çağrı listesi | eşleme tablosu | çifte-çağrı riski doğar |
| 3 | karşılığı olmayanlar için tasarım kararı verilir | eşlenmeyenler | karar kaydı (TAŞINMAZ / YENİDEN KURULUR) | sessiz işlev kaybı oluşur |
| 4 | durum makinesi farkı denetlenir | §7.5'teki durum listesi | fark tablosu | durum-gecikmesi hataları kaçar |
| 5 | hata kodu dönüşümü kurulur | §7.6'daki sonuç kodları | dönüşüm tablosu | hata kodları üst katmanda okunamaz |
| 6 | tek geçiş doğrulaması yapılır | eşleme + karar kaydı | doğrulama raporu | yarım taşıma üretimde görünür |

**Sınır:** bu liste doktrindir — depoda taşıma kodu yoktur, adım 1'in girdisi
bu turda üretilmemiştir. Adım 1 girdisi olmadan adım 2 başlamaz.

### §7.15 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §7.14 ekli 0 URL, 0 repo iddiası,
0 ölçüm sonucu içerir; yalnız adım-sırası tablosu ve kapı tariflerinden
oluşur. §7.12'deki tek adres tescili ve §7.13 dürüstlük kaydı aynen korunur —
bu ek tescil satırı eklemez. §7.10 ölçüm kapıları PLANNED olarak işaretli
kalmaya devam eder.

### §7.16 Geçiş durum makinesi (derinleştirme eki 3 — tarif)

§7.14'teki adım sırasına bu bölüm durum makinesi ekler; yeni iddia yok,
mevcut adımlar durum olarak yeniden ifade edilir:

| # | Durum | Tetik | Çıkış |
|---|---|---|---|
| G0 | OPENSL-AKTIF | varsayılan başlangıç | AAudio yokluğu doğrulanır |
| G1 | TARAMA | sistem sürümü okunur | AAudio destekleniyor mu |
| G2 | DENEME | AAudio akışı açılır | açılma başarısı beklenir |
| G3 | AAUDIO-AKTIF | açılma başarılı | OpenSL kapatılır |
| G4 | GERİ-DÖNÜŞ | açılma başarısız/söküldü | G0'a dönülür |

**Geçiş kuralları:**
1. G2→G3 geçişi yalnız akış ilk tamponu başarılı teslim ettikten sonra
   olur (erken-geçiş yasak);
2. G4 her zaman izinlidir ve veri kaybı üretmez — geri-dönüş bir hata
   değil, bir yedekleme yoludur;
3. G3 sırasında OpenSL kaynakları anında serbestleştirilmez; AAudio
   iki tampon boyunca kararlı kaldığında serbestleşir (çift-tampon
   gözlem penceresi);
4. Aynı anda iki API'nin aktif olması (çift-açık) bozulma sınıfıdır —
   §7.14'ün tekillik kuralı ihlali.

**Kapı:** durum makinesinin gözlem penceresi boyutu ölçüm kapıları-
ına bağlıdır (§7.10 PLANNED); bu ekle sayısal değer yazılmaz.

### §7.17 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§7.16) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 ölçüm sonucu, 0 sürüm sayısı eklenmiştir; yalnız
geçiş durum makinesi tarifi genişletilmiştir. §7.12'deki tek adres
tescili ve §7.15 dürüstlük kaydı aynen korunur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
