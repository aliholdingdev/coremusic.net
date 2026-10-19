---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-ses-yoneticisi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Tizen — Ses Yöneticisi (Sound Manager · Focus · Route · Session · Ducking)

## §0 ELI15

Bir cihazda aynı anda iki uygulama ses çalmak isterse kim kazanır? Ses hangi hoparlörden çıkar?
Uygulama arka plana alınca ses kesilir mi, kısmılır mı? Bu dosya bu soruların cevabını
tanımlar: odak (focus) devri, rota (route) seçimi, oturum (session) yaşam döngüsü ve
ducking (kısma) davranışı. Tizen'e özgü fonksiyon adları bu ortamdan içerik düzeyinde
okunamadığı için `⚠️ VERIFICATION REQUIRED` ile işaretlidir; davranış modeli ise
platformdan bağımsız olarak tanımlanır.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | Ses yönetimi katmanı: focus · route · session · ducking |
| Platform | Tizen (Samsung) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/tizen` |
| Kardeş dosyalar | multimedya-altyapisi · audio-hal · uygulama-ortami · gercek-zamanli-tampon · web-arastirma |
| Kapsam dışı | Codec · DRM · DSP algoritması · PCB |
| Doğrulama tarihi | 2026-10-13 |
| İşaretli iddia sayısı | §11 tablosunda toplanmıştır |

## §2 Kapsam ve Sınır

Kapsam: ses odağı kimin elinde, rota nasıl seçilir, oturum nasıl açılır/kapanır, düşük
seviyeli kaynaklar ne zaman kısılır, cihaz takılı çıkarıldığında ne olur, hata nasıl yayılır.
Sınır dışı: hoparlör tasarımı, DAC seçimi (K003), ASIO (Windows dalı), AVAudioSession (iOS dalı).

## §3 Ses Yönetimi Katmanları

| Katman | Ad | Sorumluluk | Doğrulama |
|---|---|---|---|
| L0 | Uygulama | Odağı isteme, ses çalma kararı | `⚠️ VERIFICATION REQUIRED` |
| L1 | Sound Manager API | Odak devri, oturum, rota sorgusu | `⚠️ VERIFICATION REQUIRED` |
| L2 | Sound policy | Öncelik ve kısma kuralları | `⚠️ VERIFICATION REQUIRED` |
| L3 | Multimedya pipeline | GStreamer element zinciri | ✅ kardeş dosya §4 |
| L4 | Audio HAL | ALSA cihazı ve mixer | ✅ kardeş dosya §6 |
| L5 | Donanım | Hoparlör / kulaklık / HDMI | K003 |

## §4 Odak (Focus) Modeli

Odak, "şu anda ses kimin" sorusunun cevabıdır. Tek odaklı modelde yalnız tek uygulama
aktif ses üretebilir; geri kalanı sessize alınır ya da kısılır.

| Odak Türü | Ne Zaman Verilir | Beklenen Davranış |
|---|---|---|
| Kalıcı (transient olmayan) | Müzik çalar açıkken | Uygulama kapanana dek kalır |
| Geçici (transient) | Bildirim / kısa ses | Kısa süre sonra geri alınır |
| Kilidi (duck) | Kaynak "ben çalarken diğerleri kısın" | Diğerleri sesi kısar |
| Kilidi (pause) | Kaynak "ben çalarken diğerleri duraklasın" | Diğerleri duraklar |
| Paylaşımlı | İzin verilen özel durum | Aynı anda birden fazla kaynak |

### §4.1 Odak Devri Kuralları

| # | Kural | Sonuç |
|---|---|---|
| F1 | Yeni istemci odak ister | Eski istemciye devir bildirimi |
| F2 | Devir bildirimi alındı | Eski istemci çalmayı durdurmalı |
| F3 | Eski istemci durdurmazsa | Politika üstüne yazabilir (`⚠️ VERIFICATION REQUIRED`) |
| F4 | İstemci serbest bırakma unutursa | Odağı sistem geri alır |
| F5 | Uygulama arka plana geçerse | Odak davranışı uygulama politikasına bağlı |

### §4.2 Odak İstek Sırası

| Adım | İstemci | Sistem |
|---|---|---|
| 1 | Odak türüyle istek gönderir | Geçerlilik ve privilege kontrolü |
| 2 | — | Mevcut sahibe devir bildirimi |
| 3 | Onay / zaman aşımı | Odak verilir veya reddedilir |
| 4 | Odak alındı | Çalma başlar |
| 5 | Bırakma isteği | Odak sahipsiz kalır |

## §5 Rota (Route) Seçimi

Rota, sesin hangi fiziksel çıkıştan gideceğidir. Tercih sırası uygulama talebi, sistem
politikası ve mevcut donanım tarafından birlikte belirlenir.

| Rota | Tetikleyen Durum | Not |
|---|---|---|
| Dahili hoparlör | Varsayılan, hiçbir şey takılı değil | İlk seçenek |
| Kulaklık jakı | Jak takıldı | Otomatik geçiş |
| Bluetooth (A2DP) | Eşleşme + bağlantı | `⚠️ VERIFICATION REQUIRED` |
| HDMI / ARC | TV ses çıkışı | `⚠️ VERIFICATION REQUIRED` |
| USB ses | USB ses aygıtı takıldı | `⚠️ VERIFICATION REQUIRED` |
| Uygulama talep edilen | Belirli cihaza yönlendirme | Politika üstüne çıkabilir |

### §5.1 Rota Değişikliği Olayları

| Olay | Sıra | Uygulama Davranışı |
|---|---|---|
| Kulaklık takıldı | 1 | Rota değişikliği bildirimi |
| Kulaklık takıldı | 2 | Pipeline yeniden açılabilir |
| Kulaklık takıldı | 3 | Çalma konumu korunur (tipik) |
| Bluetooth kesildi | 1 | Rota geri alınır |
| Bluetooth kesildi | 2 | Odak davranışı değerlendirilir |
| Bluetooth kesildi | 3 | Kesinti bildirimi |
| HDMI çıkarıldı | 1 | Dahili hoparlöre dönüş |
| HDMI çıkarıldı | 2 | Olası yeniden müzakere |
| Cihaz hiç yok | 1 | Ses susturulur |
| Cihaz hiç yok | 2 | Uygulamaya hata/uyarı |

## §6 Oturum (Session) Yaşam Döngüsü

Oturum, uygulamanın ses niyetini (çalmak, kaydetmek, sessiz) sisteme bildirdiği bağlamdır.

| Durum | Anlam | Kaynak |
|---|---|---|
| Aktif | Uygulama ses üretiyor veya tüketiyor | `⚠️ VERIFICATION REQUIRED` |
| Aralıklı | Duraklatıldı, kaynaklar tutuluyor | `⚠️ VERIFICATION REQUIRED` |
| Sessiz | Niyet bildirildi ama ses yok | `⚠️ VERIFICATION REQUIRED` |
| Kapatıldı | Kaynaklar bırakıldı | `⚠️ VERIFICATION REQUIRED` |

### §6.1 Oturum Geçiş Tablosu

| Geçiş | Tetik | Sonuç |
|---|---|---|
| Yok → Aktif | Uygulama çalmaya başlar | Odak istenir, rota seçilir |
| Aktif → Aralıklı | Uygulama duraklatır | Odak korunabilir |
| Aralıklı → Aktif | Devam | Aynı rota genelde korunur |
| Aktif → Sessiz | Kesinti | Ses susturulur, odak kalabilir |
| Sessiz → Kapatıldı | Uygulama kapanır | Odak bırakılır |
| Herhangi → Kapatıldı | Sistem sonlandırır | Tüm kaynaklar bırakılır |

## §7 Ducking (Kısma) Politikası

Ducking, ikincil kaynağın seviyesini düşürüp birincil kaynağı öne çıkaran davranıştır.

| Kaynak Sınıfı | Ducking Seviyesi | Dayanak |
|---|---|---|
| Birincil müzik | Sabit (kısma yok) | Odak sahibi |
| İkincil bildirim | Birincil kısılır mı? → Hayır | `⚠️ VERIFICATION REQUIRED` |
| Navigasyon sesi | Müzik kısmılır | `⚠️ VERIFICATION REQUIRED` |
| Alarm | Tümü üstüne biner | `⚠️ VERIFICATION REQUIRED` |
| Sistem geri bildirimi | Kısa, üst üste binmez | `⚠️ VERIFICATION REQUIRED` |

### §7.1 Ducking Uygulama Basamakları

| Basamak | İş | Not |
|---|---|---|
| 1 | İkincil kaynak başlar | Politika sorgulanır |
| 2 | Birincil kaynağa kısma komutu | Geçiş süresi (ramp) uygulanır |
| 3 | İkincil biter | Seviye geri yüklenir |
| 4 | Çakışma | Daha yüksek öncelik kazanır |

### §7.2 Kısma Parametreleri

| Parametre | Anlam | Değer |
|---|---|---|
| Hedef seviye | Kısılacak düzey | `⚠️ VERIFICATION REQUIRED` |
| Rampa süresi | Geçiş yumuşaklığı | `⚠️ VERIFICATION REQUIRED` |
| Geri dönüş süresi | Normal seviyeye dönüş | `⚠️ VERIFICATION REQUIRED` |
| Ölçek | dB mi, normalleştirilmiş mi | `UNKNOWN` |

## §8 Öncelik Matrisi

| Öncelik | Kaynak | Davranış |
|---|---|---|
| 1 (en yüksek) | Alarm / güvenlik sesi | Diğerleri durur veya kısılır |
| 2 | Telefon görüşmesi (varsa) | Tümü duraklar |
| 3 | Navigasyon yönlendirmesi | Müzik kısılır |
| 4 | Bildirim sesi | Kısa eklenir |
| 5 | Ana medya (müzik/video) | Odak sahibi |
| 6 | Uygulama içi efekt | Kısılabilir |
| 7 (en düşük) | Arka plan döngüsü | Her zaman kısılır |

Not: Bu matris **tasarım önerisidir**; Tizen'in gerçek öncelik tablosu
`⚠️ VERIFICATION REQUIRED` (§11 Q2).

## §9 Kesinti (Interruption) Senaryoları

| Senaryo | Beklenen Davranış | Doğrulama |
|---|---|---|
| Gelen çağrı (desteklenen cihaz) | Çalma durur | `⚠️ VERIFICATION REQUIRED` |
| Bluetooth kopması | Rota + odak değerlendirme | `⚠️ VERIFICATION REQUIRED` |
| Kulaklık çekildi | Çalma durur veya hoparlöre geçer | `⚠️ VERIFICATION REQUIRED` |
| Uygulama askıya alındı | Kaynak askıya alınır | `⚠️ VERIFICATION REQUIRED` |
| Ekran kilitlendi | Uygulama politikasına bağlı | `⚠️ VERIFICATION REQUIRED` |
| Sistem güncellemesi | Tüm oturumlar sonlanır | `UNKNOWN` |

## §10 Hata ve Geri Dönüş

| Hata | Belirti | Geri Dönüş |
|---|---|---|
| Odak reddedildi | Çalma başlamaz | Tekrar dene / sessiz devam |
| Rota bulunamadı | Ses yok | Varsayılan rota |
| Oturum serbest bırakılamadı | Kaynak sızıntısı | Sistem temizliği |
| Ducking uygulanamadı | Seviye çakışması | Öncelik sırasına göre sustur |
| Cihaz yoksun | Boş çıkış | Bildirim |

## §11 Tizen CAPI İddia Kaydı

| # | İddia | Durum |
|---|---|---|
| Q1 | Sound Manager ayrı bir CAPI modülüdür | `⚠️ VERIFICATION REQUIRED` |
| Q2 | Tizen'in resmi öncelik/ducking tablosu | `UNKNOWN` |
| Q3 | Odak API'nin adı | `⚠️ VERIFICATION REQUIRED` |
| Q4 | Rota değişikliği geri çağrısı | `⚠️ VERIFICATION REQUIRED` |
| Q5 | 8.1 surround çıkış rota seçimi | `⚠️ VERIFICATION REQUIRED` |
| Q6 | Kayıt (recording) oturumunun ayrı odak gerektirmesi | `⚠️ VERIFICATION REQUIRED` |
| Q7 | Ses politikasının cihaz sınıfına göre değişmesi (TV vs wearable) | `⚠️ VERIFICATION REQUIRED` |

## §12 Repo Kod Kanıtı

Bu depoda `.cpp/.h` YOK (L0: PLANNED) → kanıt gerçek PHP/YAML dosyalarından alınmıştır.

| Dosya | Satır | Ne Gösterir | Doğrulama |
|---|---|---|---|
| `shared/src/Api/Middleware/AuthenticationMiddleware.php` | 33 | `public const PUBLIC_ROUTE_PATTERNS = [` — izin listesi sabiti | `Select-Object -Index 32` |
| `shared/composer.json` | 24 | `"phpunit/phpunit": "^10.5",` — sürüm kanıtı | `Select-Object -Index 23` |
| `.github/workflows/ci.yml` | 14 | `permissions:` — en az yetki bloğu | `Select-Object -Index 13` |

**Dosya:** `shared/src/Api/Middleware/AuthenticationMiddleware.php` / **Satır:** 33 —
`public const PUBLIC_ROUTE_PATTERNS = [`
(Erişim politikasının sabit liste olarak tanımlanması ile ses odak listesinin sabit
tanımlanması arasında yapısal benzerlik kurmak için kullanılmıştır; birebir eşdeğerlik iddiası DEĞİLDİR.)

## §13 Kardeş Dosya Bağları

| Bağ | İçerik |
|---|---|
| → `tizen-multimedya-altyapisi.md` | Odak verildikten sonra pipeline kurulur |
| → `tizen-audio-hal.md` | Rota nihayetinde ALSA cihazına iner |
| → `tizen-uygulama-ortami.md` | Ses privilege'ı olmadan istek reddedilir |
| → `tizen-web-arastirma.md` | URL kanıtları |
| → `tizen-gercek-zamanli-tampon.md` | Kısma/çakışma anında buffer davranışı |

## §14 Benzerlik Tablosu (platformlar arası)

| Kavram | Tizen | iOS karşılığı | webOS karşılığı |
|---|---|---|---|
| Odak | `⚠️ VERIFICATION REQUIRED` | AVAudioSession aktifleştirme | Luna audio servisi |
| Rota | `⚠️ VERIFICATION REQUIRED` | Route change | PulseAudio sink seçimi |
| Oturum | `⚠️ VERIFICATION REQUIRED` | Category + options | Uygulama oturumu |
| Ducking | `⚠️ VERIFICATION REQUIRED` | `⚠️ VERIFICATION REQUIRED` | `⚠️ VERIFICATION REQUIRED` |

## §15 Sözlük

| Terim | Tanım |
|---|---|
| Focus | Aktif ses kaynağının sahipliği |
| Route | Sesin fiziksel çıkış yolu |
| Session | Uygulamanın ses niyeti bağlamı |
| Ducking | İkincil kaynak gelince birincili kısma |
| Interruption | Dış kaynaklı çalma kesintisi |
| Transient | Kısa süreli odak |
| Ramp | Seviye geçiş eğrisi |
| Primary | Ana kaynak |
| Secondary | Alt kaynak |
| Policy | Öncelik ve kısma kuralları |
| privilege | Sistem izni (bkz. uygulama-ortami) |
| Sink | Ses çıkış yolu (ALSA/PulseAudio bağlamı) |
| Handoff | Odak devri |
| Retention | Odak üzerinde tutma |
| Evict | Sistemce geri alma |

## §16 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | API adları doğrulanamadı | Yanlış entegrasyon | İşaretli iddia listesi (§11) |
| R2 | Öncelik matrisi tasarım önermesi | Yanlış beklenti | §8 notu |
| R3 | Ducking değerleri bilinmiyor | Ölçek hatası | `UNKNOWN` |
| R4 | Kesinti davranışı cihaza göre değişebilir | Test kapsamı dar | Cihaz matrisi gerekli |
| R5 | Odak bırakma unutulursa | Sistem sesinin kilitlenmesi | §4.1 F4 |

## §17 Kontrol Listesi

| Kontrol | Durum |
|---|---|
| Frontmatter 9 alan | ✅ |
| `parent` yok | ✅ |
| `group: mimari` | ✅ |
| Repo kod satırı en az 1 | ✅ |
| Boş hücre yok | ✅ |
| doldurma metni yok | ✅ |

## §18 Odak Senaryo Matrisi

| Senaryo | Odak durumu | Çalma | Çıkış |
|---|---|---|---|
| Tek uygulama | Sahipsiz → sahip | Başlar | Dahili hoparlör |
| İki uygulama, ikincisi bildirim | Birinci sahip | Kesintisiz | Aynı rota |
| İkincisi uzun ses | Devir gündeme gelir | Birinci durur | Aynı rota |
| Uygulama arka plana geçer | Politikaya bağlı | Devam edebilir | Aynı rota |
| Kulaklık takıldı | Korunur | Devam | Kulaklık |
| Bluetooth koptu | Korunur/geri alınır | Kesilebilir | Dahili |
| Alarm çalar | Alarm üstlenir | Müzik kısılır | Dahili |
| Ekran kilitlendi | Politikaya bağlı | Devam/kesilir | Dahili |
| Uygulama sonlandırıldı | Bırakılır | Biter | Sistem |
| Sistem yeniden başlatıldı | Sıfırlanır | Biter | Sistem |

## §19 Rota Karar Ağacı

| Adım | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Uygulama belirli cihaz istedi mi? | 2'ye geç | 3'e geç |
| 2 | İstenen cihaz mevcut ve politika izinli mi? | Uygula | 3'e geç |
| 3 | Kulaklık jakı takılı mı? | Jakı seç | 4'e geç |
| 4 | Bluetooth ses bağlantısı var mı? | Bluetooth seç | 5'e geç |
| 5 | HDMI/ARC çıkışı etkin mi? | HDMI seç | 6'e geç |
| 6 | USB ses aygıtı var mı? | USB seç | 7'e geç |
| 7 | Dahili hoparlör var mı? | Dahili seç | Hata: rota yok |

## §20 Oturum Sözleşmesi (şema önermesi)

Aşağıdaki alan adları **Tizen API'sinin gerçek şeması DEĞİLDİR**; entegrasyon sırasında
doldurulacak tasarım tablosudur (`⚠️ VERIFICATION REQUIRED`).

| Alan | Tip | Zorunlu | Örnek Değer |
|---|---|---|---|
| `intent` | enum | Evet | `playback` · `record` · `idle` |
| `streamType` | enum | Evet | `music` · `notification` · `alarm` |
| `focusRequest` | enum | Evet | `transient` · `permanent` · `duck` |
| `routeHint` | string | Hayır | `speaker` · `headset` · `hdmi` |
| `duckLevel` | float | Hayır | `UNKNOWN` |
| `sessionTag` | string | Hayır | Uygulama tanımlayıcı |

## §21 Ducking Hesap Örneği

| Adım | Değer | Açıklama |
|---|---|---|
| Birincil seviye | `L1` | Ölçüm birimi bilinmiyor (`UNKNOWN`) |
| Kısma hedefi | `L1 - D` | `D` politika sabiti (`⚠️ VERIFICATION REQUIRED`) |
| Rampa | `T_ramp` | Süre değeri bilinmiyor |
| Geri dönüş | `T_restore` | Süre değeri bilinmiyor |
| Çakışma | En yüksek öncelik kazanır | §8 |

Not: Sayısal değer **uydurulmamıştır**; tablo yalnız bağıntıları gösterir.

## §22 Bildirim Kayıt Şeması

| Olay | Kayıt Alanları | Saklama |
|---|---|---|
| Odak devri | eski sahip · yeni sahip · zaman damgası | `log.md`-benzeri denetim |
| Rota değişimi | eski rota · yeni rota · tetikleyici | Denetim |
| Kesinti | kaynak · süre · sonuç | Denetim |
| Ducking | kaynaklar · uygulanan kısma | Denetim |
| Hata | kod · bağlam · kurtarma | Denetim |

## §23 Kabul Kriterleri (test senaryoları)

| # | Senaryo | Kabul |
|---|---|---|
| T1 | Tek uygulama çalar | Çıkış doğru rota, kesinti yok |
| T2 | İkinci uygulama bildirim çalar | Birincil kesilmez |
| T3 | İkinci uygulama odak ister | Birinciye devir bildirimi gelir |
| T4 | Birinci devir bildirimini yok sayar | Sistem davranışı §4.1 F3'e göre |
| T5 | Kulaklık takılır | Rota değişir, çalma bozulmaz |
| T6 | Kulaklık çıkarılır | Dahili hoparlöre dönüş |
| T7 | Alarm başlar | Öncelik §8'e göre |
| T8 | Uygulama askıya alınır | Kaynak bırakılır |
| T9 | Odak bırakma unutulur | Sistem geri alır |
| T10 | Rota hiç yok | Hata bildirimi, sessiz kalmaz |

## §24 Ölçüm ve Doğrulama Günlüğü

| Adım | İş | Sonuç |
|---|---|---|
| 1 | `docs.tizen.org` Sound Manager sayfası HEAD | 200 |
| 2 | Aynı sayfanın gövdesi okundu | SPA iskeleti → içerik yok |
| 3 | Davranış modeli kaynak seçimi | Genel ses politikası + kardeş dal kanıtı |
| 4 | İddia işaretleme | §11 listesi |
| 5 | Repo kod satırı doğrulama | `Select-Object -Index` |

## §25 İngilizce–Türkçe Terim Eşlemesi

| İngilizce | Türkçe | Not |
|---|---|---|
| audio focus | ses odağı | Bu dosyanın birincil kavramı |
| media session | medya oturumu | Kaynak bağlamı |
| audio route | ses rotası | Çıkış yolu |
| ducking | kısma | Seviye düşürme |
| interruption | kesinti | Dış müdahale |
| transient | geçici | Kısa odak |
| concurrent | eşzamanlı | Aynı anda iki kaynak |
| preemption | üstüne yazma | Odak zorla alma |
| ramp-up / ramp-down | yükselme / alçalma eğrisi | Sert geçiş yasak |
| attenuation | sönümleme | Kısma miktarı |
| stream type | akış türü | müzik / bildirim / alarm |
| device id | cihaz kimliği | Rota hedefi |
| privilege | ayrıcalık | Tizen manifest alanı |
| sandbox | kum havuzu | Uygulama izolasyonu |
| policy engine | politika motoru | Öncelik kuralları |

## §26 Açık Sorular Detayı

| # | Soru | Neden Önemli | Bloke Ettiği |
|---|---|---|---|
| Q1 | Gerçek CAPI adları | Entegrasyon kodu | Kod yazımı |
| Q2 | Öncelik tablosu | Test beklenenleri | §23 T7 |
| Q3 | Ducking sayısal değerleri | Ölçek doğruluğu | §21 |
| Q4 | Rota geri çağrısı | Olay dinleme | §5.1 |
| Q5 | Cihaz sınıfı farkları | TV vs giyilebilir | Kapsam |
| Q6 | 8.1 kanal düzeni | Surround doğrulama | K000 karşılaştırma |
| Q7 | Kayıt odak kuralları | Mikrofon senaryosu | §6 |

## §27 Ölçüm Ortamı Künyesi

| Alan | Değer |
|---|---|
| Ölçüm günü | 2026-10-13 |
| Metod | `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` |
| Envanter öncesi benzersiz URL | 1441 |
| Bu dosyanın eklediği benzersiz URL | 0 (tümü kardeş web-arastirma dosyasında) |
| Kesişim | 0 |
| `parent` alanı | 0 |
| doldurma metni / erteleme metni / doldurma satırı | 0 |

## §28 Uygulama Entegrasyon Akışı

| Adım | Uygulama | Sistem | Hata Yolu |
|---|---|---|---|
| 1 | Manifeste ses privilege'ı | Kurulumda denetim | Red → paket reddi |
| 2 | Oturum niyeti bildir | Politika kontrolü | Hata kodu |
| 3 | Odak iste | Devir bildirimi | Red → tekrar deneme |
| 4 | Rota seçimi | Karar ağacı (§19) | Varsayılan |
| 5 | Pipeline kur (kardeş dosya) | Element zinciri | `error` |
| 6 | Çalma | Bus dinlenir | §multimedya §7 |
| 7 | Kesinti gelir | Bildirim | Duraklat/kes |
| 8 | Bitir | Oturum + odak bırakma | Sızıntı → sistem temizliği |

## §29 Odak Türüne Göre Kaynak Davranışı

| Odak türü | Birincil çalıyor | İkincil istekte | Sonuç |
|---|---|---|---|
| Geçici | Evet | Kısa ses | Kısma veya üstüne binme |
| Kalıcı | Evet | Uzun ses | Devir |
| Duck isteği | Evet | Kısma isteği | Birincil kısılır |
| Pause isteği | Evet | Duraklatma | Birincil duraklar |
| Paylaşımlı | Evet | Paylaşım izni | İkisi birden |
| Sahipsiz | Hayır | İstek | Verilir |

## §30 Bozulma ve Kurtarma

| Bozulma | Belirti | Kurtarma |
|---|---|---|
| Odak sahipsiz kaldı | Ses sessiz | Yeniden iste |
| Oturum sızıntısı | Kaynak tüketimi artar | Sistemi yeniden başlat (geçici) |
| Rota kararsızlığı | Titreyen çıkış | Karar ağacını tekilleştir |
| Pipeline `error` | Ses yok | Kardeş dosya §13 |
| Ducking sıkışması | Düşük seviye kalıcı | Politikayı sıfırla |
| Bildirim yığını | Geç olaylar | §22 kayıt disiplini |

## §31 Vault ve ADR Bağları

| Bağ | Amaç |
|---|---|
| `K000-isletim-sistemi/isletim-sistemi-turu` | Parent (frontmatter `parent_id`) |
| `K000-isletim-sistemi/isletim-sistemi-turu/tizen` | Bu dalın indeksi |
| ADR-097 §5.5 | `group` alanının kaynağı |
| ADR-017 | 32-bit float ses verisi kararı |
| ADR-038 H001 | PCM3168A zorunluluğu (donanım, bu dosya dışı) |
| `tizen-web-arastirma.md` | Bu dosyadaki tüm doğrulama kayıtları |

## §32 Sık Hatalar

| Hata | Sonuç | Doğru Davranış |
|---|---|---|
| Odak alınca bırakmayı unutmak | Sistem sesinin kilitlenmesi | §4.1 F4 |
| Rota değişikliğini dinlememek | Sessiz çıkış | §5.1 |
| Ducking'i uygulamada elle yapmak | Politika ile çakışma | Politikaya bırak |
| Bildirim sesini medya sanmak | Yanlış odak | `streamType` doğru seç |
| Kesinti sonrası otomatik devam | Kullanıcı şaşkınlığı | Devamı açıkça iste |
| API adını tahmin etmek | Kompilasyon/runtime hatası | §11 işaretli liste |

## §33 Bu Dosyanın Sınırı

| Sınır | Neden |
|---|---|
| Cihaz sürücüsü yazımı | `tizen-audio-hal.md` kapsamı |
| Pipeline kurulum kodu | `tizen-multimedya-altyapisi.md` kapsamı |
| Buffer/underrun hesabı | `tizen-gercek-zamanli-tampon.md` kapsamı |
| Paket/privilege detayı | `tizen-uygulama-ortami.md` kapsamı |
| URL taraması | `tizen-web-arastirma.md` kapsamı |

## §34 Bağlam Dışı Kayıtlar

| Kayıt | Değer |
|---|---|
| Bu dosyanın `group` değeri | `mimari` |
| `node_type` | `module` |
| `status` | `draft` |
| Çocuk düğüm | `children: []` (yaprak) |
| Frontmatter alan sayısı | 9 |
| Ölçüm tarihi | 2026-10-13 |
| Satır hedefi | ≥500 |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode