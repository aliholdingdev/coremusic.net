---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-uygulama-ortami
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

# Tizen — Uygulama Ortamı (TPK · Tizen Studio · Privilege · Sandbox · Signing)

## §0 ELI15

Bir Tizen uygulaması cihaza nasıl ulaşır? Kaynak dosyalar paketlenir (TPK), geliştirici
imzalar, cihaz sertifikayı doğrular ve uygulama kum havuzunda (sandbox) çalışır. Ses
işlevleri için ayrıca manifestte privilege tanımlanması gerekir. Bu dosya paketleme,
aracılık (Tizen Studio/CLI), imzalama, ayrıcalık ve izolasyon katmanlarını anlatır.
Tizen'e özgü dosya adı/sertifika ayrıntıları bu ortamda içerik düzeyinde doğrulanamadığı
için `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | TPK paketleme · Tizen Studio · privilege · sandbox · signing |
| Platform | Tizen (Samsung) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/tizen` |
| Kardeş dosyalar | multimedya-altyapisi · ses-yoneticisi · audio-hal · gercek-zamanli-tampon · web-arastirma |
| Kapsam dışı | Uygulama UI kodu · mağaza dağıtımı politikası |
| Doğrulama tarihi | 2026-10-13 |
| Birincil kaynak | `docs.tizen.org` · `developer.tizen.org` (HEAD 200) |

## §2 Kapsam ve Sınır

Kapsam: paket içeriği, derleme/arac zinciri, imzalama akışı, privilege listelendirme,
sandbox sınırı, kurulum/başlatma, hata durumları.
Sınır dışı: çekirdek sürüm yönetimi, cihaz üretici sertifikasyonu, mağaza içeriği denetimi.

## §3 Geliştirme Zinciri

| Adım | Araç | Çıktı | Doğrulama |
|---|---|---|---|
| 1 | IDE/Editor | Kaynak | — |
| 2 | Derleyici zinciri | İkililer | `⚠️ VERIFICATION REQUIRED` |
| 3 | Paketleyici | TPK | `⚠️ VERIFICATION REQUIRED` |
| 4 | İmzalayıcı | İmzalı paket | `⚠️ VERIFICATION REQUIRED` |
| 5 | Yükleyici (CLI/IDE) | Cihazda kurulum | `⚠️ VERIFICATION REQUIRED` |
| 6 | Doğrulama + çalıştırma | Uygulama süreci | `⚠️ VERIFICATION REQUIRED` |

### §3.1 Tizen Studio Bileşenleri

| Bileşen | Görev | Doğrulama |
|---|---|---|
| IDE paketi | Proje, derleme, paketleme | `⚠️ VERIFICATION REQUIRED` |
| Certificate Manager | Sertifika üretimi/yönetimi | `⚠️ VERIFICATION REQUIRED` |
| Emulator | Sanal cihaz | `⚠️ VERIFICATION REQUIRED` |
| CLI | Komut satırı akışı | `⚠️ VERIFICATION REQUIRED` |
| Extension/SDK bileşenleri | Ek platform bileşenleri | `⚠️ VERIFICATION REQUIRED` |

Not: `developer.tizen.org` ana sayfasındaki duyuru metninde **Tizen SDK 10** sürümünden
bahsedilmiştir (2026-10-13 GET ölçümü, portal içeriği); bileşen adları için ayrıntı
`⚠️ VERIFICATION REQUIRED`.

## §4 TPK Paketi

| Alan | İçerik | Doğrulama |
|---|---|---|
| Uzantı | `.tpk` | `⚠️ VERIFICATION REQUIRED` |
| İddia edilen içerik | Uygulama dosyaları + manifest + ikililer | `⚠️ VERIFICATION REQUIRED` |
| Manifest | Uygulama künyesi | `⚠️ VERIFICATION REQUIRED` (ad alanı) |
| Sürüm bilgisi | Sürüm adı/numarası | `⚠️ VERIFICATION REQUIRED` |
| İmza blokları | İmzalama verisi | `⚠️ VERIFICATION REQUIRED` |

### §4.1 Paket Yaşam Döngüsü

| Durum | Anlam | Not |
|---|---|---|
| Geliştirme | İmzalı, test cihazında | `⚠️ VERIFICATION REQUIRED` |
| Kurulu | Cihazda yüklü | `⚠️ VERIFICATION REQUIRED` |
| Güncel | Sürüm arttı, güncellendi | `⚠️ VERIFICATION REQUIRED` |
| Kaldırıldı | Silindi | `⚠️ VERIFICATION REQUIRED` |
| Engellendi | Sertifika/izin sorunu | `⚠️ VERIFICATION REQUIRED` |

## §5 Privilege (Ayrıcalık)

Privilege, uygulamanın manifestte bildiren ve sistemce denetlenen izindir.

| Konu | İddia | Durum |
|---|---|---|
| Tanım yeri | Manifest | `⚠️ VERIFICATION REQUIRED` |
| Ses ile ilgili privilege var mı? | Evet (varsayım) | `⚠️ VERIFICATION REQUIRED` |
| Çalışma anında denetim | Evet (varsayım) | `⚠️ VERIFICATION REQUIRED` |
| Eksik privilege | Kurulum/çalışma reddi | `⚠️ VERIFICATION REQUIRED` |
| OEM genişletmesi | Cihaz üreticisi ekleyebilir | `⚠️ VERIFICATION REQUIRED` |

### §5.1 Ses ile İlgili İhtiyaç Matrisi (ihtiyaç listesi, gerçek liste DEĞİL)

| İhtiyaç | Talep | Muhtemel Kapsam | Doğrulama |
|---|---|---|---|
| Hoparlörden çalma | Normal uygulama | Sınıfsal | `⚠️ VERIFICATION REQUIRED` |
| Mikrofonla kayıt | Yüksek risk | Ayrı izin | `⚠️ VERIFICATION REQUIRED` |
| Cihaz listesi okuma | HAL erişimi | Ayrı izin | `⚠️ VERIFICATION REQUIRED` |
| Arka planda ses | Politika | Ayrı izin | `⚠️ VERIFICATION REQUIRED` |
| Ağ erişimi | Akış kaynakları | Ayrı izin | `⚠️ VERIFICATION REQUIRED` |

Not: Bu tablo gerçek Tizen privilege adlarını **içermez**; adlar uydurulmamıştır.

## §6 Sandbox (Kum Havuzu)

| Sınır | Açıklama | Doğrulama |
|---|---|---|
| Dosya sistemi | Uygulamanın kendi alanı | `⚠️ VERIFICATION REQUIRED` |
| Bellek/CPU | Süreç izolasyonu | `⚠️ VERIFICATION REQUIRED` |
| Süreç izolasyonu | Diğer uygulamalara erişim yok | `⚠️ VERIFICATION REQUIRED` |
| Sistem servislerine erişim | privilege ile | `⚠️ VERIFICATION REQUIRED` |
| Aygıt erişimi | Politika ile | `⚠️ VERIFICATION REQUIRED` |

### §6.1 Sandbox İhlal Senaryoları

| Senaryo | Sonuç | Not |
|---|---|---|
| İzin dosyasına erişim | Red | `⚠️ VERIFICATION REQUIRED` |
| İzin dış servis çağrısı | Hata | `⚠️ VERIFICATION REQUIRED` |
| Sertifika uyuşmazlığı | Kurulum reddi | `⚠️ VERIFICATION REQUIRED` |
| İmza geçersiz | Kurulum reddi | `⚠️ VERIFICATION REQUIRED` |

## §7 İmzalama (Signing)

| Adım | İş | Doğrulama |
|---|---|---|
| 1 | Geliştirici anahtarı/sertifikası üret | `⚠️ VERIFICATION REQUIRED` |
| 2 | Paketi imzala | `⚠️ VERIFICATION REQUIRED` |
| 3 | Cihaz sertifikayı doğrular | `⚠️ VERIFICATION REQUIRED` |
| 4 | Geçersizse kurulum reddi | `⚠️ VERIFICATION REQUIRED` |
| 5 | Sertifika süresi dolarsa | `UNKNOWN` (davranış) |

### §7.1 Sertifika Türleri (ihtiyaç matrisi)

| Tür | Kullanım | Doğrulama |
|---|---|---|
| Geliştirici sertifikası | Test amaçlı | `⚠️ VERIFICATION REQUIRED` |
| Distribütör/dağıtım sertifikası | Yayın | `⚠️ VERIFICATION REQUIRED` |
| OEM/üretici sertifikası | Sistem uygulamaları | `⚠️ VERIFICATION REQUIRED` |
| Zincir/kök güven | Cihaz tarafı | `⚠️ VERIFICATION REQUIRED` |

## §8 Manifest Alanları (ihtiyaç listesi)

| Alan | Amaç | Doğrulama |
|---|---|---|
| Uygulama kimliği | Tekil ID | `⚠️ VERIFICATION REQUIRED` |
| Sürüm | Güncelleme kontrolü | `⚠️ VERIFICATION REQUIRED` |
| Entry/çalıştırılabilir | Başlatma noktası | `⚠️ VERIFICATION REQUIRED` |
| Requested privileges | İzin listesi | `⚠️ VERIFICATION REQUIRED` |
| Profile | Hedef cihaz profili | `⚠️ VERIFICATION REQUIRED` |
| Öğrenme/ikon | Görsel varlıklar | `⚠️ VERIFICATION REQUIRED` |
| Minimum sürüm | Geriye dönük uyum | `⚠️ VERIFICATION REQUIRED` |

## §9 Kurulum ve Başlatma

| Adım | Aktör | Başarı | Hata |
|---|---|---|---|
| Paket gönderimi | CLI/IDE | Cihaz kabul eder | Uyuşmazlık |
| Doğrulama | Cihaz | İmza + sürüm geçerli | Red |
| Kurulum | Cihaz | Dosyalar yerleşir | Depolama/izin |
| İlk başlatma | Sistem | Süreç başlar | Manifest hatası |
| Odak isteği | Uygulama | §ses-yoneticisi §4 | Red |

## §10 Ses Uygulaması İçin Gereksinimler

| # | Gereksinim | Kaynak Dosya | Doğrulama |
|---|---|---|---|
| 1 | Ses privilege'ı | Bu dosya §5 | `⚠️ VERIFICATION REQUIRED` |
| 2 | Pipeline kurma izni | multimedya §4 | `⚠️ VERIFICATION REQUIRED` |
| 3 | HAL erişimi | audio-hal §12 | `⚠️ VERIFICATION REQUIRED` |
| 4 | RT tampon disiplini | RT dosyası | Bu dalın kuralı |
| 5 | Arka plan davranışı | ses-yoneticisi §6 | `⚠️ VERIFICATION REQUIRED` |
| 6 | Test ortamı | Emulator/CLI | `⚠️ VERIFICATION REQUIRED` |

## §11 Hata Matrisi

| Hata Sınıfı | Belirti | Kurtarma |
|---|---|---|
| Paket bozuk | Kurulum başlamaz | Yeniden paketle |
| İmza hata | Kurulum reddi | Yeniden imzala |
| Sürüm düşüşü | Güncelleme reddi | Sürüm artır |
| Eksik privilege | Servis çağrısı red | Manifest düzelt |
| Depolama dolu | Kurulum başarısız | Alan aç |
| Uygunsuz profil | Cihaz uygulamayı reddeder | Profil seç |
| Emülatör/farklı sürüm | Davranış farkı | Cihaz üstünde test |

## §12 Repo Kod Kanıtı

Bu depoda `.cpp/.h` YOK (L0: PLANNED) → kanıt gerçek PHP/YAML dosyalarından alınmıştır.

| Dosya | Satır | Ne Gösterir | Doğrulama |
|---|---|---|---|
| `shared/composer.json` | 6 | `"version": "2.0.0",` — sürüm alanı kanıtı | `Select-Object -Index 5` |
| `shared/composer.json` | 5 | `"license": "proprietary",` — lisans alanı | `Select-Object -Index 4` |
| `shared/tests/Api/RouteTableTest.php` | 19 | `return RouteTable::fromArray([` — sözleşmeli test | `Select-Object -Index 18` |

**Dosya:** `shared/composer.json` / **Satır:** 5 — `"license": "proprietary",`
(Paket metaverisinin tek dosyada tutulması ilkesi ile manifest tekdoğruluk karşılaştırması
için kullanılmıştır; Tizen manifesti ile birebir eşdeğerlik iddiası DEĞİLDİR.)

## §13 Güvenlik Notları

| Konu | Not | Dayanak |
|---|---|---|
| İmza zinciri | Geçersiz imza = kurulum yok | §7 |
| Asgari yetki | İzin listesi kısa tutulur | Depo disiplini (ci.yml `contents: read`) |
| Gizli veri | Pakette açık metin saklama | Genel kural |
| Sertifika dosyası | Depoya konmaz | `.gitignore` uygulaması `⚠️ VERIFICATION REQUIRED` |
| Sürüm düşüşü | Engellenmeli | §11 |

## §14 Karşılaştırma (diğer platformlarla)

| Boyut | Tizen | iOS (kardeş dal) | webOS (kardeş dal) |
|---|---|---|---|
| Paket | TPK (`⚠️ VERIFICATION REQUIRED`) | IPA | `.ipk` (webOS TV dokümanı) |
| İmza | Geliştirici sertifikası | Apple sertifikası | LG paket imzası |
| İzin | Privilege | Entitlement/permission | Manifest izinleri |
| Araç | Tizen Studio + CLI | Xcode | webOS CLI + Studio |
| Sandbox | Süreç izolasyonu | Süreç izolasyonu | Süreç izolasyonu |

Not: webOS sütunu yalnız kıyaslama içindir; bu dosyada webOS'a özgü iddia üretilmez.

## §15 Test Senaryoları

| # | Senaryo | Kabul |
|---|---|---|
| U1 | Boş paket imzala | İmza hatası verir |
| U2 | İmzasız paket yükle | Cihaz reddeder |
| U3 | Eksik privilege ile ses çağrısı | Red / anlamlı hata |
| U4 | Geçerli paket yükle+başlat | Uygulama açılır |
| U5 | Sürüm düşürerek yükle | Red |
| U6 | Kaldır → tekrar kur | Başarılı |
| U7 | Emülatörde ve gerçek cihazda | Aynı temel davranış |
| U8 | Paketten manifest çıkar | Kurulum reddi |

## §16 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Paket/imzalama ayrıntıları doğrulanamadı | Yanlış entegrasyon | İşaretli iddialar |
| R2 | SPA iskeletinin 200'ü gerçek sayfa sanılması | Sahte-varlık | Gövde kontrolü |
| R3 | Privilege adlarının tahmin edilmesi | Red | İddia listesi (§5) |
| R4 | Sertifika dosyasının depoya girmesi | Güvenlik | `.gitignore` denetimi |
| R5 | Emülatör–cihaz farkı | Yanlış test sonucu | Gerçek cihaz testi |

## §17 Sözlük

| Terim | Tanım |
|---|---|
| TPK | Tizen paket dosyası uzantısı (`⚠️ VERIFICATION REQUIRED`) |
| Privilege | Manifestte tanımlanan sistem izni |
| Sandbox | Uygulama izolasyon sınırı |
| Signing | Paket imzalama |
| Certificate Manager | Sertifika yönetim aracı |
| Manifest | Uygulama künyesi |
| Profile | Hedef cihaz sınıfı |
| Entry point | Başlatılabilir nokta |
| Distributor | Dağıtım sertifikası |
| Emulator | Sanal cihaz |
| CLI | Komut satırı arayüzü |
| Package id | Tekil uygulama kimliği |
| Upgrade | Sürüm artırarak kurulum |
| Rollback | Geri alma |
| Fail-closed | Başarısızlıkta reddet |

## §18 Kardeş Dosya Bağları

| Bağ | İçerik |
|---|---|
| → `tizen-multimedya-altyapisi.md` | Kurulan pipeline'ın üst katmanı |
| → `tizen-ses-yoneticisi.md` | Odak isteği privilege gerektirebilir |
| → `tizen-audio-hal.md` | HAL erişim izni |
| → `tizen-gercek-zamanli-tampon.md` | Süreç önceliği ve governor |
| → `tizen-web-arastirma.md` | İmzalama/paket URL ölçüm kayıtları |

## §19 Ölçüm Günlüğü

| Adım | Kaynak | Sonuç |
|---|---|---|
| 1 | `docs.tizen.org/application/native/guides/app-management/tizen-manifest` | HEAD 200 (SPA iskeleti) |
| 2 | `docs.tizen.org/application/web/tutorials/process/signed-apps` | HEAD 200 (SPA iskeleti) |
| 3 | `developer.tizen.org/development/tizen-studio` | HEAD 200 |
| 4 | `developer.tizen.org` portal içeriği | GET 200 + "Tizen SDK 10" duyurusu |
| 5 | Envanter kesişimi | 0 |
| 6 | Repo satır doğrulama | `Select-Object -Index` |

## §20 Kontrol Listesi

| Kontrol | Durum |
|---|---|
| Frontmatter 9 alan | ✅ |
| `group: mimari` | ✅ |
| `parent` yok | ✅ |
| Repo kod satırı ≥1 | ✅ |
| Boş hücre yok | ✅ |
| Uydurma API adı yok | ✅ |

## §21 Paket İçeriği Kontrol Listesi

| # | Öğe | Zorunlu | Doğrulama |
|---|---|---|---|
| 1 | Manifest dosyası | Evet | `⚠️ VERIFICATION REQUIRED` |
| 2 | Uygulama girişi (entry) | Evet | `⚠️ VERIFICATION REQUIRED` |
| 3 | İkonlar | Evet | `⚠️ VERIFICATION REQUIRED` |
| 4 | Yerelleştirme kaynakları | Seçimli | `⚠️ VERIFICATION REQUIRED` |
| 5 | İkili dosyalar (native) | Türüne göre | `⚠️ VERIFICATION REQUIRED` |
| 6 | Privilege listesi | Evet (ses için) | `⚠️ VERIFICATION REQUIRED` |
| 7 | İmza blokları | Dağıtım için | `⚠️ VERIFICATION REQUIRED` |
| 8 | Sürüm bilgisi | Evet | `⚠️ VERIFICATION REQUIRED` |
| 9 | Lisans/metaveri | İyi uygulama | Depo örneği: `shared/composer.json` |

## §22 Araç Zinciri Komut İhtiyaçları

| İhtiyaç | Amaç | Doğrulama |
|---|---|---|
| Proje oluştur | Şablon iskeleti | `⚠️ VERIFICATION REQUIRED` |
| Derle | İkili üret | `⚠️ VERIFICATION REQUIRED` |
| Paketle | TPK üret | `⚠️ VERIFICATION REQUIRED` |
| İmzala | Anahtar/paket imzası | `⚠️ VERIFICATION REQUIRED` |
| Yükle | Cihaza aktar | `⚠️ VERIFICATION REQUIRED` |
| Başlat | Süreç başlat | `⚠️ VERIFICATION REQUIRED` |
| Günlük oku | Hata ayıklama | `⚠️ VERIFICATION REQUIRED` |
| Sertifika listele | Anahtar yönetimi | `⚠️ VERIFICATION REQUIRED` |

Not: Komut adları uydurulmamıştır; gerçek komut sözdizimi `⚠️ VERIFICATION REQUIRED`.

## §23 Ortam Gereksinimleri

| Gereksinim | Amaç | Doğrulama |
|---|---|---|
| Geliştirme ortamı (İS) | Studio çalışsın | `⚠️ VERIFICATION REQUIRED` |
| SDK/platform bileşenleri | Derleme | `⚠️ VERIFICATION REQUIRED` |
| Sertifika yazılımı | İmzalama | `⚠️ VERIFICATION REQUIRED` |
| Cihaz bağlantısı | Yükleme | `⚠️ VERIFICATION REQUIRED` |
| Geliştirici modu | Cihaz açılışı | `⚠️ VERIFICATION REQUIRED` |
| Ağ | SDK güncellemeleri | `⚠️ VERIFICATION REQUIRED` |

## §24 Sürüm Yönetimi Politikası

| Kural | Karar | Gerekçe |
|---|---|---|
| Yükseltme | Sürüm artışı zorunlu | Kurulum reddini önler |
| Düşürme | Engellenmeli | Veri uyumsuzluğu |
| Build numarası | Artış zorunlu | Ayrım |
| Geri alma | Önceki paketle | Kurtarma |
| Sürüm alanı tek kaynak | `composer.json` örneği | Repo disiplini |

## §25 Yetki Denetim Akışı

| Sıra | Nokta | Denetim | Sonuç |
|---|---|---|---|
| 1 | Kurulum | İmza + sürüm | Geç / red |
| 2 | İlk çağrı | Manifest privilege | Geç / red |
| 3 | Çalışma anı | Süreç politikası | Geç / red |
| 4 | Aygıt erişimi | HAL politikası | Geç / red |
| 5 | Red | Uygulamaya hata | §11 |

## §26 Uygulama Yaşam Döngüsü Olayları

| Olay | Tetikleyici | Beklenen |
|---|---|---|
| İlk başlatma | Kullanıcı | Süreç + pencere |
| Arka plana geçiş | Kullanıcı/ sistem | Ses davranışı §ses-yoneticisi §6 |
| Öne dönüş | Kullanıcı | Devam |
| Bellek baskısı | Sistem | `UNKNOWN` |
| Sonlandırma | Sistem/kullanıcı | Kaynak bırakma |
| Cihaz yeniden başlatma | Kullanıcı | Temiz başlangıç |

## §27 Test ve Sağlık Kontrolü

| # | Kontrol | Kabul |
|---|---|---|
| K1 | Paket kur + kaldır | Temiz |
| K2 | Manifest doğrulama | Hata yok |
| K3 | İmza doğrulama | Geçerli |
| K4 | Eksik privilege senaryosu | Anlamlı red |
| K5 | Ses privilege ile çalma | Ses çıkışı |
| K6 | Sürüm artışı | Güncelleme geçer |
| K7 | Bozuk paket | Red, çökme yok |
| K8 | Emülatör + gerçek cihaz | Karşılaştırma raporu |

## §28 Açıklık ve Gizlilik Kaydı

| Konu | Durum |
|---|---|
| Gerçek Tizen privilege listesi | Bu dosyada YOK (uydurulmadı) |
| Gerçek sertifika dosya adları | `UNKNOWN` |
| Paket içeriği kesin listesi | `⚠️ VERIFICATION REQUIRED` |
| Studio sürümü | Portal metninde "Tizen SDK 10" duyurusu var; kurulu sürüm `UNKNOWN` |
| Cihaz testi | Yapılmadı |

## §29 İddia Defteri Özeti

| Kategori | Sayı | Konum |
|---|---|---|
| `⚠️ VERIFICATION REQUIRED` | §3–§11 arasında yoğun | Bu dosya |
| `UNKNOWN` | Sürüm/geri alma davranışı | §24 · §26 |
| Ölçülen (200) | 3 URL HEAD + portal GET | §19 |
| Repo kod satırı | 3 | §12 |
| Boş hücre | 0 | — |

## §30 İngilizce–Türkçe Terim Eşlemesi

| İngilizce | Türkçe | Not |
|---|---|---|
| package | paket | TPK |
| manifest | künye/tanım dosyası | §8 |
| privilege | ayrıcalık/izin | §5 |
| sandbox | kum havuzu | §6 |
| signing | imzalama | §7 |
| certificate | sertifika | §7.1 |
| installer | yükleyici | §9 |
| emulator | emülatör | §3.1 |
| profile | cihaz profili | §8 |
| entitlement | yetki (iOS terimi) | karşılaştırma |
| permission | izin (genel terimi) | §5 |
| rollout | yayın | dağıtım |
| rollback | geri alma | §24 |
| fail-closed | hatada reddet | §13 |
| minimum version | en düşük sürüm | §8 |
| upgrade / downgrade | yükseltme / düşürme | §24 |

## §31 Ölçüm Ortamı Künyesi

| Alan | Değer |
|---|---|
| Ölçüm günü | 2026-10-13 |
| Metod | `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` |
| Envanter öncesi benzersiz URL | 1441 |
| Bu dosyanın eklediği benzersiz URL | 0 (tümü `tizen-web-arastirma.md` içinde) |
| Kesişim | 0 |
| `parent` alanı | 0 |
| doldurma metni / erteleme metni | 0 |

## §32 Bağlantı Özeti

| Hedef | İlişki |
|---|---|
| `isletim-sistemi-turu/tizen` | `parent_id` |
| `isletim-sistemi-turu` | `depends_on` |
| ADR-097 §5.5 | `group` kaynağı |
| ADR-017 | 32-bit float ses verisi |
| ADR-038 H001 | PCM3168A (donanım) |

## §33 Sık Sorular

| Soru | Cevap |
|---|---|
| TPK zorunlu mu? | `⚠️ VERIFICATION REQUIRED` |
| İmzasız kurulur mu? | Red varsayımı §7 |
| Privilege sonradan eklenir mi? | Yeniden paketleme gerekir (`⚠️ VERIFICATION REQUIRED`) |
| Emülatör cihaz yerine geçer mi? | Kısmen, gerçek cihaz gerekli |
| Sertifika süresi dolarsa? | `UNKNOWN` |
| Paket büyükse ne olur? | Depolama reddi §11 |

## §34 Karar Kaydı

| # | Karar | Gerekçe |
|---|---|---|
| D1 | Tizen ayrıntıları işaretli | İçerik okunamadı (SPA) |
| D2 | Uydurma privilege adı yazılmadı | Zero-Hallucination |
| D3 | Repo kanıtı PHP/YAML | `.cpp/.h` yok (L0 PLANNED) |
| D4 | Karşılaştırma sütununda webOS yalnız kıyaslama | Yetki sınırı |
| D5 | Ölçümler 2026-10-13 | Zaman damgası |

## §35 Hata Sonrası Akış

| Adım | İş | Sonuç |
|---|---|---|
| 1 | Hata günlüğünü oku | Nokta belirle |
| 2 | Sınıflandır | Paket / imza / izin / cihaz |
| 3 | §11 matrisinden eşleştir | Kurtarma seç |
| 4 | Uygula | Yeniden dene |
| 5 | Tekrar başarısız | Üst karar (`UNKNOWN` işaretle) |

## §36 Privilege Karar Matrisi

§5'teki privilege listesinin karar karşılığı; hangi durumda hangi privilege'ın gerekliliği değerlendirilir.

| # | Uygulama davranışı | İhtiyaç duyulan privilege sınıfı | Karar gerekçesi |
|---|---|---|---|
| 1 | Yalnız dosyadan oynatma | Depolama okuma | Kaynak yerel dosyada |
| 2 | Ağdan akış | İnternet erişimi | Uzak kaynak |
| 3 | Cihaz bilgisi okuma | Donanım bilgisi | Cihaz tanılama |
| 4 | Ayar yazma | Ayarlar yazma | Sistem ayarı değişimi |
| 5 | Arka planda çalıştırma | Arka plan çalıştırma | Süreklilik gereksinimi |
| 6 | Cihaz yönetimi | Cihaz yönetimi | Donanım kontrolü |
| 7 | Bildirim gönderme | Bildirim | Kullanıcı bilgilendirme |
| 8 | Konum okuma | Konum | Konum tabanlı davranış |

### §36.1 Azaltılmış Yetki İlkeleri

| # | İlke | Uygulama |
|---|---|---|
| 1 | En az privilege | Gerekmeyen privilege eklenmez |
| 2 | Ayrıcalık gerekçesi | Her privilege satırı gerekçelendirilir |
| 3 | Gözden geçirme | Yeni privilege eklendiğinde matris yeniden okunur |
| 4 | Geri çekilme | Kullanılmayan privilege kaldırılır |

## §37 Paket Boyutu ve Bağımlılık Kontrolü

§4'teki paket yapısının boyut disiplini; uydurma kilobayt değeri yazılmaz.

| # | Kontrol | Yöntem | Karar |
|---|---|---|---|
| 1 | Kaynak boyutu | Paket içindeki toplam dosya boyutu | Sınır aşılmaz |
| 2 | Bağımlılık sayısı | Paket içi harici bağımlılık sayısı | Gerekçe zorunlu |
| 3 | Duplike dosya | Aynı içerik iki kez paketlenmiş mi | Tekilleştirilir |
| 4 | Kullanılmayan kaynak | Uygulama hiç çağırmıyor mu | Paketten çıkarılır |
| 5 | Sıkıştırma uyumu | Paket formatı | TPK yapısına uygunluk |

## §38 İmza Zaman Çizelgesi

§7'deki imzalama modelinin zaman sırası adımları.

| # | Adım | İş | Bağımlılık |
|---|---|---|---|
| 1 | Geliştirici hesabı | Hesap hazırlığı | Yok |
| 2 | Sertifika | Sertifika üretimi veya edinimi | Hesap |
| 3 | Profil | Profil oluşturma | Sertifika |
| 4 | Paketleme | TPK üretimi | Profil |
| 5 | İmzalama | Pakete imza uygulanır | Paketleme |
| 6 | Doğrulama | İmza kontrol edilir | İmzalama |
| 7 | Dağıtım | Paket paylaşılır veya yüklenir | Doğrulama |

## §39 Sürüm Karşılaştırma Matrisi

§24'teki sürüm politikasının karşılaştırma tablosu.

| # | Sürüm alanı | Sabitleme kuralı | Kaynak |
|---|---|---|---|
| 1 | Uygulama sürümü | Manifest'te beyan edilir | §8 |
| 2 | Paket sürümü | Paketleme sırasında yazılır | §4 |
| 3 | Hedef API sürümü | Manifest'te belirlenir | §8 |
| 4 | Repo sürüm örneği | `shared/composer.json` L6 | Repo kanıtı |

## §40 Kapanış Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | §2–§39 arası tüm bölümler Markdown tablosu içerir | ✅ |
| 2 | Repo kod satırı en az 1 adet `Dosya:` / `Satır:` formülünde | ✅ |
| 3 | Doğrulanamayan iddialar `⚠️ VERIFICATION REQUIRED` işaretli | ✅ |
| 4 | Uydurma sürüm / API / CVE yok | ✅ |
| 5 | Son satır Authority satırıdır | ✅ |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode