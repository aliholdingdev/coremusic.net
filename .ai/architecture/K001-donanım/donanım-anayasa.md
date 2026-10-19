---
node_id: K001-donanım
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children:
  - donanım-kernel
  - donanım-core
  - donanım-api
  - donanaım-type
depends_on:
  - K000-isletim-sistemi
  - K003-audio
  - K005-firmware
  - K001-donanım/donanım-core
  - K001-donanım/donanım-api
evidence:
  - "v2 §2.2 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 170-186"
  - "v2 §3 KURAL 1 (imla korunumu) — aynı dosya satır 595-610"
  - ".ai/architecture/index.md §8.1 iki-ekeni gruplama"
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §5.5"
  - "shared/src/Device/DeviceManager.php L24 (662 satır) — repo içi cihaz soyutlama kanıtı"
validation:
  - "Bu dosya 10 § + 9 tablo içerir; satır sayımı 2026-10-12'de yapıldı"
  - "Frontmatter 9 alan (8 zorunlu + group) — 2026-10-12 kontrolü"
open_questions:
  - "K001 ile K003 arasında K002 yok (ID GAP) → ⚠️ VERIFICATION REQUIRED"
  - "donanım-kernel ↔ donanım-core sınırı v2'de tanımsız → ⚠️ VERIFICATION REQUIRED"
  - "donanaım-type yazımı kaynaktan birebir korunur (düzeltme YASAK) — bilgi notu"
group: mimari
---

# K001-donanım — Donanım Anayasası

**ELI15 (v2 §2.2):** Kasadaki fiziksel parçalar — disk, ses kartı, ekran, kablolar.
Bu belge K001 düğümünün bağlayıcı kural setidir; çelişkide bu dosya kazanır.

## §1 Kimlik ve Yetki

| Alan | Değer | Dayanak |
|---|---|---|
| node_id | K001-donanım | v2 §2.2 |
| node_type | domain (kademe 1) | v2 §4.1 |
| parent | K000–K5000 Architecture Registry | v2 §4.1 |
| K slotu | K001 (Band-1: K000–K020 TANIMLI) | v2 §4.7 |
| Çocuk sayısı | 4 (kademe-2) | v2 mini-ağaç |
| Kademe-3 sayısı | 3 (audio · hdd · ssd) | v2 mini-ağaç |
| Yazım | donanaım-type → KORUNUR | v2 §3 KURAL 1 |
| Komsu | K000-isletim-sistemi (üst), K003-audio (alt) | v2 §1 |
| Komşu | K005-firmware (aşırı) | ağaç sırası |
| Durum | verify-required (açık sorular §10) | bu dosya §10 |

## §2 Kapsam Yetki Sınırı

| Yüzey | K001'de mi? | Karar mercii | Not |
|---|---|---|---|
| Sürücü seviyesi donanım soyutlama | EVET | K001 | ana iş |
| Cihaz tipi sınıflandırma | EVET | K001/donanaım-type | audio · hdd · ssd |
| Donanım erişim API'si | EVET | K001/donanım-api | keşif · komut · durum · olay |
| Kernel-üstü arayüz | EVET | K001/donanım-kernel | çekirdeğe en yakın rol |
| Çekirdek sürücü kodu (ring-0) | HAYIR | K000-isletim-sistemi | yalnız sınır |
| Cihaz firmware'i / MCU yüklemesi | HAYIR | K005-firmware | XMOS · DSP zinciri |
| Ses işleme zinciri | HAYIR | K003-audio | K001 yalnız cihazı sunar |
| DSP algoritması | HAYIR | K004-dsp | komşu |
| Fiziksel PCB / BOM | HAYIR | hardware vault | elektronik tarafı |
| Depolama cihazı envanteri | EVET | K001/donanaım-type/hdd · ssd | kademe-3 |

## §3 İlkeler

1. **Tek yönlü bağımlılık:** alt katman üst katmanı asla import etmez; yön yalnız yukarıdır.
2. **Fail-safe:** doğrulanamayan donanım durumu `verify-required` etiketiyle işaretlenir, tahmin edilmez.
3. **Realtime yol sıfır-atama:** ses yolundaki buffer'lar baştan allocate edilir; callback içinde tahsis yasak.
4. **Sürücü-üstü soyutlama:** K001 ham register/IO yazımını sarmalar, uygulamaya açmaz.
5. **Test edilebilirlik:** gerçek cihaz olmayan ortamda soyutlama taklit edilebilir (simüle cihaz).
6. **Sonuç tipi:** hata yayılımı Result/Either ile yapılır; ham exception yığını yukarı taşınmaz.
7. **Kanıt zorunluluğu:** her iddia gerçek dosya yolu veya resmi URL ile desteklenir.
8. **İmla sadakati:** v2 spec yazım hataları düzeltilmez (§3 KURAL 1).
9. **Bant disiplini:** Band-2+ klasörü açılmaz; ADR onayı bekler (v2 §4.7).
10. **Sahiplik:** donanım verisi tek yazıcıdan girer (registry sahibi tek düğüm).
11. **Idempotency:** cihaz keşif/çıkarım işlemleri tekrar çalıştırılabilir.
12. **Bileşim > kalıtım:** ortak davranış trait/mixin ile paylaşılır, taban sınıf genişletilmez.
13. **Yapılandırma veri değildir:** cihaz fiziksel özelliği kod içinde sabitlenmez, envanterden okunur.
14. **Kapsam disiplini:** K000/K005 yüzeyine girilirse dur + escalation (§6 kapısı).
15. **Gözlemlenebilirlik:** donanım olayı (eklendi/çıktı/hata) log'a yazılır, sessiz yutulmaz.
16. **Tersinirlik:** her cihaz ekleme işleminin karşılığı olan çıkarma tanımlıdır.

## §4 Yasaklar

1. `donanaım-type` yazımını düzeltmek (v2 §3 KURAL 1 — DOKUNULMAZ).
2. Band-2..7 klasörü açmak (yalnız ADR onayıyla).
3. Kod içine sabit donanım register adresi gömmek.
4. Ham kullanıcı girdisini doğrudan donanım komutuna çevirmek.
5. Donanım bulunamadığında sessizce boş liste döndürmek (fail-open).
6. Boş bırakılmış yer tutucu / ertelenmiş iş bildirimi / noktalama dolgusu ile doldurulmuş satır üretmek.
7. Doğrulanmamış URL veya uydurma `Dosya:/Satır:` referansı yazmak.
8. K000 (işletim sistemi çekirdeği) veya K005 (firmware) içine kod taşımak.
9. `as`, `!` (bang) veya `any` ile tip güvenliğini atlatmak.
10. Yeni harici bağımlılığı domain/soyutlama katmanına eklemek.
11. Sürücü kodunu uygulama katmanına doğrudan çağırmak (katman ihlali).
12. Donanım envanterini iki ayrı tabloda tutmak (tek yazıcı kuralı ihlali).
13. `SELECT *` ile cihaz envanteri okumak.
14. Cihaz serisi/seri numarasını log'a açık yazmak (KVKK/PII riski).
15. Eşzamanlı yazım kilidi olmadan paylaşılan cihaz durumunu güncellemek.
16. Kanıtsız `status: verified` atamak.

## §5 Katman ve Sorumluluk Matrisi

| Düğüm | Rol | Sahip olduğu | Sahip olmadığı | Kademe |
|---|---|---|---|---|
| donanım-kernel | donanıma en yakın soyutlama | interrupt/DMA sarmalayıcı rolü | çekirdek sürücü kodu | 2 |
| donanım-core | merkez bileşenler | cihaz kayıt, durum modeli, kaynak havuzu | API sözleşmesi | 2 |
| donanım-api | erişim arayüzü | keşif, komut, durum, olay | iş kuralı | 2 |
| donanaım-type | tip sınıflandırma | audio · hdd · ssd envanteri | firmware yükleme | 2 |
| donanaım-type/audio | ses donanımı | DAC/codec/amplifier envanteri | DSP zinciri | 3 |
| donanaım-type/hdd | döner disk | disk/RAID envanteri | dosya sistemi | 3 |
| donanaım-type/ssd | katı hal | SSD/NVMe envanteri | kalıcı depolama şeması | 3 |
| donanım-anayasa | kural seti | bu belge | uygulama kodu | — |
| donanım-kapsam | sınır tanımı | kapsam içi/dışı | kod | — |
| donanım-bagimliliklar | bağımlılık matrisi | yön belirleme | dağıtım | — |

## §6 Bileşen Sözleşmeleri

| Sözleşme | Girdi | Çıktı | Hata | Sahip |
|---|---|---|---|---|
| cihaz keşfi | tip filtresi | envanter listesi | keşif-hatasi | donanım-api |
| cihaz aç | cihaz kimliği | durum | erisim-red | donanım-api |
| durum sorgu | cihaz kimliği | durum nesnesi | bulunamadi | donanım-core |
| olay yayını | olay | abone listesi | — | donanım-core |
| tip sınıflandırma | ham cihaz kaydı | tip etiketi | belirsiz-tip | donanaım-type |
| kaynak ayır | talep | kaynak tutamağı | kaynak-yok | donanım-core |
| kaynak bırak | tutamak | onay | gecersiz-tutamak | donanım-core |
| soyutlama bağla | arayüz | bağlama | uyumsuz-arayuz | donanım-kernel |
| kapasite oku | cihaz | kapasite | okuma-hatasi | donanaım-type |
| sağlık kontrolü | — | sağlık | — | donanım-core |
| envanter eşitle | harici kaynak | fark raporu | kaynak-erişimi | donanım-core |
| yetki denetimi | istek | karar | yetkisiz | donanım-api |

## §7 Hata Modeli ve Güvenli Çalışma

| Durum | Davranış | Üst kata etiket | Örnek |
|---|---|---|---|
| Cihaz bulunamadı | boş-özel sonuç | NOT_FOUND | takılı olmayan disk |
| Cihaz yetkisiz | reddet | FORBIDDEN | salt-okunur medya |
| Zaman aşımı | kısmi sonuç + etiket | TIMEOUT | disk yanıt vermiyor |
| Sürücü çakışması | dur + rapor | CONFLICT | iki uygulama aynı cihazda |
| Kapasite aşıldı | sınır koy | RESOURCE_EXHAUSTED | buffer taşması |
| Bilinmeyen tip | tahmin etme | VERIFY_REQUIRED | yeni cihaz modeli |
| Firmware sürüm uyuşmazlığı | K005'e havale | ESCALATE | MCU sürüm farkı |
| İşletim sistemi desteği yok | K000'e havale | ESCALATE | çekirdek API eksik |
| Bozuk envanter kaydı | izole et + log | DATA_INVALID | eksik seri no |
| Eşzamanlı yazım | kuyruğa al | LOCKED | çift ekleme |
| Kanıt eksikliği | status: verify-required | OPEN_QUESTION | v2 tanımı yok |
| Kapsam ihlali | dur + escalation | LAYER_VIOLATION | K000 koduna erişim |

## §8 Kalite Kapıları

| Kapı | Ölçüt | Yöntem | Aşarsa |
|---|---|---|---|
| G1 frontmatter | 9 alan eksiksiz | metin kontrolü | dosya reddedilir |
| G2 grup | group: mimari/kod/kanit | metin kontrolü | dosya reddedilir |
| G3 §GRUPLAR | index.md tablosu 6/6 | sayım | index reddedilir |
| G4 satır aralığı | hedef aralıkta | satır sayımı | içerik genişletilir |
| G5 dolgu yasağı | yasaklı dolgu terimi yok | grep | satır yeniden yazılır |
| G6 URL benzersizliği | dosyalar arası tekrar yok | URL çıkarımı | URL havuzundan değişir |
| G7 kod referansı | Dosya/Satır gerçek | dosya açılışı | referans silinir |
| G8 kapsam | K000/K005'e taşma yok | inceleme | katman geri alınır |
| G9 imla | donanaım-type korunur | diff | yazım geri alınır |
| G10 kanıt | doğrulanamayan iddia etiketli | inceleme | iddia silinir |

## §9 Kaynaklar ve Kanıt

| # | Kaynak | Tür | Ne verir |
|---|---|---|---|
| 1 | coremusic-katmanli-mimari-agaci-v2.md §2.2 | spec | mini-ağaç, çocuk listesi |
| 2 | coremusic-katmanli-mimari-agaci-v2.md §3 | spec | imla/koruma kuralları |
| 3 | coremusic-katmanli-mimari-agaci-v2.md §4.2 | spec | 8 zorunlu alan |
| 4 | coremusic-katmanli-mimari-agaci-v2.md §4.7 | spec | band şeması |
| 5 | .ai/architecture/index.md §8.1 | vault | iki-ekeni gruplama |
| 6 | ADR-097 §5.5 (draft) | ADR | group + §GRUPLAR |
| 7 | shared/src/Device/DeviceManager.php L24 | repo | 662 satır cihaz yönetimi |
| 8 | shared/src/Device/DeviceDetector.php L19 | repo | cihaz tespiti |
| 9 | shared/src/Device/DeviceRenderer.php L19 | repo | cihaz bazlı render |
| 10 | shared/src/Device/DeviceCssMap.php L5 | repo | cihaz–CSS haritası |
| 11 | .ai/.sql/mysql/coremusic_system.sql L432 | repo | sistem envanteri şeması |
| 12 | .ai/.sql/mysql/coremusic_media.sql L172 | repo | medya/donanım şeması |
| 13 | .ai/.decisions/index.md | vault | 80 kabul edilmiş ADR |
| 14 | shared/composer.json L24 | repo | phpunit ^10.5 test altyapısı |

## §10 Açık Sorular

| # | Soru | Etki | Etiket |
|---|---|---|---|
| 1 | K002 neden yok? (K001→K003 atlama) | ID GAP | ⚠️ VERIFICATION REQUIRED — düzeltme (2026-10-13): `K002-electroncis/` diskte VAR (16 md) ama `architecture/index.md` §6 hâlâ "K002 yok" der → çelişki C1, Vault Steward kararı bekliyor (ADR-102 §6) |
| 2 | donanım-kernel mi core mu donanıma daha yakın? | sınır | ⚠️ VERIFICATION REQUIRED |
| 3 | donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ✅ CEVAPLANDI (2026-10-13): disk gerçeği **10 bağlam** — 9'u `PROPOSED — APPROVAL REQUIRED` (ADR-102 taslağı: display·power önceki oturum + cables·network·memory·cpu bu oturum) + gpu (paralel Vault Steward oturumu, ADR-102 DIŞI) |
| 4 | hdd/ssd ayrımı teknoloji mi kullanım mı? | sınıflandırma | ⚠️ VERIFICATION REQUIRED |
| 5 | Ses donanımı envanteri K001 mi K003 mü? | sınır | ⚠️ VERIFICATION REQUIRED |
| 6 | firmware-sürüm uyuşmazlığı kimin işi (K005 sınırı)? | escalation | ⚠️ VERIFICATION REQUIRED |
| 7 | K001 için gerçek repo karşılığı hangi modül? | kanıt | ⚠️ VERIFICATION REQUIRED |
| 8 | Simüle cihaz katmanı hangi dosyada yaşayacak? | tasarım | ⚠️ VERIFICATION REQUIRED |
| 9 | Band-2 genişlemesi ne zaman ADR'ye girer? | süreç | ⚠️ VERIFICATION REQUIRED |
| 10 | Çocuk tanımları v2'de yok — kim onaylayacak? | içerik | ⚠️ VERIFICATION REQUIRED |

## §11 Temel İlkeler (Genişletilmiş — şablon §A karşılığı)

> §3'teki 16 ilke bağlayıcıdır; bu bölüm aynı ilkelerin uygulama yüzeyini açar.
> §3 ile §11 çelişirse §3 kazanır (kanıt üstünlüğü).

| # | İlke | Uygulama yüzeyi | Kanıt |
|---|---|---|---|
| I1 | Tek yönlü bağımlılık | `shared/src/Device/` yalnız `Database/` ve `Middleware/`'i çağırır; tersi yok | `DeviceManager.php` L24 (662 satır) — import zinciri Read |
| I2 | Fail-safe | Bilinmeyen cihaz `verify-required` döner; boş liste sessiz dönmez | §7 hata modeli |
| I3 | Realtime sıfır-atama | C++20 yolunda buffer baştan allocate; PHP envanter yolu bu kuralın dışında (talep-yanıt) | kök `AGENTS.md` §5 |
| I4 | Sürücü-üstü soyutlama | `DeviceManager::device()` L216 tek sarmalayıcı — ham erişim üst katmana açılmaz | `shared/src/Device/DeviceManager.php` L216 |
| I5 | Test edilebilirlik | Simüle cihaz; test fixture `shared/tests/Fixtures/` altında | `shared/tests/` alt dizin sayımı |
| I6 | Sonuç tipi | Result/Either; ham exception yukarı taşınmaz | §7 |
| I7 | Kanıt zorunluluğu | Her satır dosya yolu + satır no veya HTTP 200 URL | bu dosya §17 |
| I8 | İmla sadakati | `donanaım-type` yazımı dondurulmuş | v2 §3 KURAL 1 |
| I9 | Bant disiplini | Band-2..7 klasörü ADR onayı olmadan açılmaz | v2 §4.7 |
| I10 | Sahiplik | Donanım envanteri tek yazıcı; iki tablo yasak | §4 yasak 12-13 |
| I11 | Idempotency | Keşif/çıkarım tekrar çalıştırılabilir; ikinci çalıştırma fark üretmez | §6 sözleşme |
| I12 | Bileşim > kalıtım | Ortak davranış trait/mixin; taban sınıf genişletilmez | PHP `final class` kanıtı §16 |
| I13 | Yapılandırma veri değildir | Cihaz fiziksel özelliği envanterden okunur, koda sabitlenmez | §4 yasak 3 |
| I14 | Kapsam disiplini | K000/K005 yüzeyine taşma → dur + §6 kapısı | §6 |
| I15 | Gözlemlenebilirlik | Donanım olayı log'a yazılır; sessiz yutma yasak | `.ai/.sql/mysql/coremusic_logs.sql` (452 satır) |
| I16 | Tersinirlik | Her ekleme işleminin karşılığı çıkarma tanımlıdır | §6 kaynak bırak |

### §11.1 İlke → Gate Eşlemesi

| İlke | Gate | Aşarsa |
|---|---|---|
| I1 | G8 kapsam | katman geri alınır |
| I7 | G7 kod referansı | referans silinir |
| I8 | G9 imla | yazım geri alınır |
| I10 | G5 dolgu yasağı | satır yeniden yazılır |

## §12 Değişmez Kurallar (Şablon §B karşılığı)

| # | Kural | Değişebilir mi? | Dayanak |
|---|---|---|---|
| C1 | `donanaım-type` yazımı korunur | HAYIR — dokunulmaz | v2 §3 KURAL 1 |
| C2 | Amplifikatör topolojisi Class AB | HAYIR — tartışma yasak | kök `CLAUDE.md` §Sabitler |
| C3 | PCM5122 kullanımı yasak; PCM3168A/AK4458 öner | HAYIR — ADR-038 H001 | `.ai/AGENTS.md` §17 |
| C4 | 32-bit float ses verisi | HAYIR — ADR-017 | ADR-017 |
| C5 | 512 sample buffer · 48 kHz | HAYIR — sartname | kök `AGENTS.md` §8 |
| C6 | ASIO Exclusive Lock: tek uygulama | HAYIR — donanım kilidi | kök `AGENTS.md` §9 |
| C7 | DC offset > 0.5 V → koruma rölesi | HAYIR — güvenlik | kök `AGENTS.md` §10 |
| C8 | SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | EVET — ADR ile değişebilir | `~/.config/opencode/AGENTS.md` §5 |
| C9 | 4 kademe-2 çocuk (kernel/core/api/type) | EVET — ADR ile | v2 §2.2 |
| C10 | 3 kademe-3 alt bağlam (audio/hdd/ssd) | EVET — envanterle | v2 §2.2 · `PROPOSED — APPROVAL REQUIRED`: 10 bağlam (audio·hdd·ssd·display·power·cables·network·memory·cpu — ADR-102 taslağı + gpu — paralel oturum, ADR-102 DIŞI) — ADR-102 onaylanana kadar eski 3 bağlam geçerli sayılır |

### §12.1 Değişmezlik Sınıfı Ayrımı

| Sınıf | Tanım | Kural |
|---|---|---|
| Mutlak | İhlali ürün güvenliğini bozar (C2-C7) | ADR bile açamaz |
| Koşullu | Ölçüm eşiği gibi (C8) | ADR + ölçüm kanıtı |
| Yapısal | Ağacın şekli (C9-C10) | ADR-097 onayı |

## §13 Yasaklar (Genişletilmiş — Şablon §C karşılığı)

> §4'teki 16 yasak bağlayıcıdır; bu bölüm yasak ihlali tespit ve geri alma yöntemini açar.

| # | Yasak | Tespit yöntemi | Geri alma |
|---|---|---|---|
| Y1 | `donanaım-type` düzeltmesi | her vault-sync'te yazım taraması | diff revert |
| Y2 | Band-2..7 klasör açma | dizin listesi | klasör silinir + ADR |
| Y3 | Sabit register adresi kod içi | grep pattern | envanterden okuma |
| Y4 | Ham girdi → donanım komutu | kod inceleme | validation middleware |
| Y5 | Fail-open (sessiz boş liste) | test senaryosu | Result::err |
| Y6 | Doldurucu satır üretimi (belirsiz yer tutucu desenleri) | grep | satır yeniden yazılır |
| Y7 | Uydurma URL / `Dosya:/Satır:` | HTTP HEAD + Read | referans silinir |
| Y8 | K000/K005 kod taşıma | katman inceleme | katman geri alınır |
| Y9 | `as`/`!`/`any` tip kaçışı | kod inceleme | tip geri eklenir |
| Y10 | Yeni harici bağımlılık domain'e | composer.json inceleme | bağımlılık kaldırılır |
| Y11 | Sürücü kodunu uygulamaya çağırma | import zinciri | katman geri alınır |
| Y12 | Envanter iki tablo | SQL şema inceleme | tek yazıcıya dönülür |
| Y13 | `SELECT *` | SQL grep | kolon listesi yazılır |
| Y14 | Seri no log'a açık | log inceleme | maskeleme |
| Y15 | Kilitsiz paylaşılan durum yazımı | kod inceleme | kilit eklenir |
| Y16 | Kanıtsız `status: verified` | frontmatter inceleme | status draft'a döner |

### §13.1 Yasak Tarama Komutları (repo kökünden)

| Yasak | Yöntem | Beklenen |
|---|---|---|
| Y6 dolgu | `Select-String` ile yasaklı dolgu terimi taraması | 0 satır |
| Y7 URL | HTTP HEAD kontrolü | 200/3xx veya ⚠️ işaretli |
| Y13 SQL | `Select-String -Path *.sql -Pattern 'SELECT \*'` | 0 satır |
| Y1 imla | `donanaım-type` literal sayımı | tüm dosyalarda birebir |

## §14 Yetki Sınırları (Şablon §D karşılığı)

| Yüzey | Yetki sahibi | K001 girebilir mi? | Karar mercii |
|---|---|---|---|
| Donanım soyutlama (sürücü-üstü) | K001 | EVET | bu dosya |
| Cihaz tipi sınıflandırma | K001/donanaım-type | EVET | bu dosya |
| Donanım erişim API'si | K001/donanım-api | EVET | bu dosya |
| Donanım merkez bileşenleri | K001/donanım-core | EVET | bu dosya |
| Donanıma en yakın arayüz | K001/donanım-kernel | EVET | bu dosya |
| Ring-0 çekirdek sürücü kodu | K000-isletim-sistemi | HAYIR | K000 anayasası |
| Cihaz firmware'i / MCU | K005-firmware | HAYIR | K005 anayasası |
| Ses işleme zinciri | K003-audio | HAYIR | K003 anayasası |
| DSP algoritması | K004-dsp | HAYIR | K004 anayasası |
| Fiziksel PCB / BOM | hardware vault | HAYIR | elektronik vault |
| Test stratejisi gate | K18-testing | HAYIR | K019 |
| Compliance denetimi | K019-comilance | HAYIR | K020 |

### §14.1 Yetki İhlali Senaryoları

| Senaryo | Sonuç | Escalasyon |
|---|---|---|
| K001 kodu K000 sürüşü | layer violation | L1 → L2 (30s) |
| K001 firmware üretimi | K005 ihlali | L1 → L2 |
| K001 DSP algoritması | K004 ihlali | L1 → L2 |
| K001 envanter yazma yetkisi | OWASP riski | L1 → security |

## §15 Uygulama Kontrol Listesi (kapı-öncesi)

| # | Kontrol | Yöntem | Kapı |
|---|---|---|---|
| K1 | Frontmatter 9 alan eksiksiz | metin kontrolü | G1 |
| K2 | `group` alanı `mimari`/`kod`/`kanit` | metin kontrolü | G2 |
| K3 | İçerik ≥500 tam satır | satır sayımı | G4 |
| K4 | Yasaklı dolgu terimleri yok | grep | G5 |
| K5 | URL benzersiz + HTTP 200/⚠️ | HEAD + envanter | G6 |
| K6 | `Dosya:`/`Satır:` Read ile doğrulanmış | Read | G7 |
| K7 | K000/K005'e taşma yok | inceleme | G8 |
| K8 | `donanaım-type` yazımı bozulmamış | diff | G9 |
| K9 | Doğrulanamayan iddia `⚠️` işaretli | inceleme | G10 |
| K10 | `parent:` alanı kullanılmamış (yalnız `parent_id`) | grep | G1 |

## §16 Kaynak Kanıt Uzantısı (2026-10-13 — yeni doğrulanan URL'ler)

> Bu bölüm §9 kaynak tablosunun genişletilmesidir; §9'daki 14 kaynak aynen kalır.

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| 15 | PHP PDO sınıf dokümantasyonu (envanter erişim katmanı referansı) | https://www.php.net/manual/en/class.pdo.php | 2026-10-13 | HTTP 200 |
| 16 | Composer dokümantasyonu (bağımlılık yönetimi) | https://getcomposer.org/doc/ | 2026-10-13 | HTTP 200 |
| 17 | PHPUnit 10.5 dokümantasyonu (test framework) | https://docs.phpunit.de/en/10.5/ | 2026-10-13 | HTTP 200 |
| 18 | ISO C++ (dil standardı — C++20 motor referansı) | https://isocpp.org/ | 2026-10-13 | HTTP 200 |
| 19 | Microsoft Win32 API (donanım erişim arayüzü referansı) | https://learn.microsoft.com/en-us/windows/win32/api/ | 2026-10-13 | HTTP 200 |
| 20 | Microsoft Windows sürücü DDI (donanım-kernel referansı) | https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/ | 2026-10-13 | HTTP 200 |

## §17 Repo Kanıt Uzantısı (2026-10-13 — Read ile doğrulandı)

| Dosya | Satır | Ne | K001 ilişkisi |
|---|---|---|---|
| `shared/src/Device/DeviceManager.php` | 662 | toplam satır | cihaz yönetimi ölçeği |
| `shared/src/Device/DeviceManager.php` | 216 | `device(): string` | tekil erişim — I4 |
| `shared/src/Device/DeviceManager.php` | 225 | `deviceProfile(): string` | profil üretimi |
| `shared/src/Device/DeviceDetector.php` | 19 | `final class DeviceDetector` | algılama servisi |
| `shared/src/Device/DeviceRenderer.php` | 19 | `final class DeviceRenderer` | render servisi |
| `shared/src/Device/DeviceCssMap.php` | 5 | `final class DeviceCssMap` | harita deseni — I12 |
| `shared/src/Api/Routing/RouteTable.php` | 26 | `final class RouteTable` | route kaydı |
| `shared/composer.json` | 24 | `phpunit/phpunit: ^10.5` | test framework |
| `shared/composer.json` | 48 | `phpstan analyse src --level=5` | statik analiz |
| `package.json` | 5 | `test:js` vitest | JS test |
| `package.json` | 8 | `test:e2e` playwright | E2E test |
| `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` | CI kapısı |
| `.ai/.sql/mysql/coremusic_logs.sql` | 452 | log şeması | I15 gözlemlenebilirlik |

## §18 Ölçüm Eşikleri ve Kabul Kapıları (2026-10-13)

| Metrik | Eşik | Birim | Kaynak | Kapı |
|---|---|---|---|---|
| SNR (ses) | > 100 | dB | `~/.config/opencode/AGENTS.md` §5 | G-ölçüm |
| THD+N | < 0.01 | % @ 1 kHz | `~/.config/opencode/AGENTS.md` §5 | G-ölçüm |
| Buffer boyutu | 512 | sample | kök `AGENTS.md` §8 | G-gerçek zaman |
| Örnekleme hızı | 48 | kHz | kök `AGENTS.md` §8 | G-gerçek zaman |
| Ses veri tipi | 32-bit | float | ADR-017 | G-veri |
| DC offset koruma eşiği | > 0.5 | V | kök `AGENTS.md` §10 | G-güvenlik |
| Test coverage (birim) | ≥ 90 | % | proje standardı | G-test |
| Statik analiz | level 5 | PHPStan | `shared/composer.json` L48 | G-LINT |

### §18.1 Eşik → Domain Eşlemesi

| Eşik | K001 sorumluluğu | K001 dışındaki |
|---|---|---|
| SNR/THD+N | envanter kaydı (ölçü saklama) | ölçüm K18-testing |
| Buffer/48kHz | envanter kaydı | uygulama K003-audio |
| DC offset | envanter kaydı + alarm | röle donanım vault |
| Coverage | test stratejisi | çalıştırma K019 |

## §19 Sürüm ve Uyumluluk Kayıtları (2026-10-13)

| Bağımlılık | Sürüm | Kanıt | K001 etkisi |
|---|---|---|---|
| PHP | 8.x (strict_types) | kök `AGENTS.md` §5 | K001 üretim kodu PHP olacak |
| PHPUnit | ^10.5 | `shared/composer.json` L24 | test uyumu |
| PHPStan | level 5 | `shared/composer.json` L48 | statik analiz |
| Vitest | `test:js` | `package.json` L5 | JS tarafı test |
| Playwright | `test:e2e` | `package.json` L8 | E2E test |
| MySQL | 18 BCNF DB | `.ai/.sql/mysql/` (20 dosya) | envanter şeması |
| C++20 | Neva Engine | kök `AGENTS.md` §1 | PLANNED — disk yok |

### §19.1 PLANNED Yüzey (disk kanıtı yok)

| Yüzey | Durum | Dayanak |
|---|---|---|
| Neva Engine C++20 kodu | PLANNED | kök `AGENTS.md` §1 — `*.cpp`/`*.h` = 0 |
| ASIO/WASAPI entegrasyonu | PLANNED | ADR-019 per-OS player FROZEN |
| K001 üretim kodu | YOK | §10 Q7 |

## §20 Kaçınılan Teknolojiler (REDDEDİLEN)

| # | Kaçınılan | Neden | Alternatif |
|---|---|---|---|
| 1 | PCM5122 DAC | ADR-038 H001 mutlak yasak | PCM3168A · AK4458 |
| 2 | Class D amplifikatör | kök `CLAUDE.md` sabit — tartışma yasak | Class AB |
| 3 | ORM | SQL injection riski (§18 Warnings) | PDO prepared |
| 4 | `SELECT *` | veri sızıntısı + kırılganlık | kolon listesi |
| 5 | Ham exception yığını | katmanlar arası yayılım | Result/Either |
| 6 | `as`/`!`/`any` tip kaçışı | tip güvenliği zayıflığı | `final class` + interface |
| 7 | Çalışma zamanı donanım keşfi | PHP'de ek bağımlılık + öngörülemezlik | dosya tabanlı harita (K-01) |
| 8 | Domain'e harici bağımlılık | katman ihlali | infrastructure |

## §21 Hata Yayılım Matrisi (katmanlar arası)

| Hata | Üst katman | Davranış | Etiket |
|---|---|---|---|
| Cihaz yok | donanım-api | boş-özel sonuç | NOT_FOUND |
| Yetki yok | donanım-api | reddet | FORBIDDEN |
| Zaman aşımı | donanım-core | kısmi sonuç + etiket | TIMEOUT |
| Sürücü çakışması | donanım-core | dur + rapor | CONFLICT |
| Kaynak yetersiz | donanım-core | sınır koy | RESOURCE_EXHAUSTED |
| Bilinmeyen tip | donanaım-type | tahmin etme | VERIFY_REQUIRED |
| Firmware farkı | K005'e havale | escalate | ESCALATE |
| OS desteği yok | K000'e havale | escalate | ESCALATE |
| Bozuk kayıt | donanım-core | izole + log | DATA_INVALID |
| Eşzamanlı yazım | donanım-core | kuyruk | LOCKED |
| Kanıt eksik | — | status draft | OPEN_QUESTION |
| Kapsam ihlali | — | dur + escalate | LAYER_VIOLATION |

## §22 Dönemsel Gözden Geçirme Tetikleyicileri

| # | Tetikleyici | Aksiyon | Sıklık |
|---|---|---|---|
| 1 | URL sağlığı | HTTP HEAD yeniden doğrulama | yıllık |
| 2 | `donanaım-type` yazım bütünlüğü | vault-sync taraması | her sync |
| 3 | Sürüm değişimi (PHPUnit/PHP/MySQL) | uyumluluk yeniden değerlendirme | sürüm başına |
| 4 | Yeni ADR (ADR-097 Band-2) | §14 yetki sınırları güncelleme | ADR başına |
| 5 | K002 ID GAP kapanışı | §9 bağımlılık düzeltmesi | bir kez — disk gerçeği: `K002-electroncis/` VAR (16 md); §9/§23 düzeltmesi Vault Steward onayı bekliyor (ADR-102 §6/C1) |
| 6 | Çekirdek eşik değişimi (SNR/THD) | §18 kapısı güncelleme | ADR başına |
| 7 | Fiziksel test ortamı kurulumu | §10 Q7 kapatma + status verified | bir kez |

## §23 Kapsamlı Karşılaştırma: K001 Alt Düğümleri (özet matris)

| Özellik | donanım-kernel | donanım-core | donanım-api | donanaım-type |
|---|---|---|---|---|
| Kademe | 2 | 2 | 2 | 2 |
| Rol | donanıma en yakın | merkez toplama | dışa çağrı yüzeyi | tür sınıflandırma |
| Sahip olduğu | register/kesme/DMA sarmalayıcı rolü | envanter/ölçü/sağlık | keşif/komut/durum/olay | audio·hdd·ssd·display·power·cables·network·memory·cpu envanteri (`PROPOSED` — ADR-102) + gpu (paralel oturum, ADR-102 DIŞI) |
| Sahip olmadığı | çekirdek sürücü kodu | API sözleşmesi | iş kuralı | firmware yükleme |
| Bloklayan soru | Q3 (kernel↔core) | Q3 + Q2 (depolama) | Q-A1 (protokol) | Q4 (envanter) |
| Durum | draft | draft | draft | draft |
| Alt kademe | `children: []` | `children: []` | `children: []` | audio·hdd·ssd·display·power·cables·network·memory·cpu (9, ADR-102) + gpu (10., paralel oturum, ADR-102 DIŞI) — `PROPOSED` |
| Kanıt durumu | XU316/TPA3255 arayüzü | LM5122/MJL ölçüm | SPI/I2C/USB Audio | PCM3168A/AK4458 |

## §24 Bağımlılık Yönü Hızlı Bakış

| Yön | Tür | Karşı taraf | Etiket |
|---|---|---|---|
| K001 → K000 | arayüz (sürücü) | OS çekirdek | [TASARIM] |
| K001 → K005 | zamanlama (boot/register) | firmware | [TASARIM] |
| K003 → K001 | fiziksel (DAC/ADC, I2S/TDM) | ses motoru | [TASARIM] |
| K004 → K001 | fiziksel (amplifikatör çıkışı) | DSP | [TASARIM] |
| K000 → K001 | arayüz (sürücü keşfi) | OS çekirdek | [TASARIM] |

> Geri bağımlılık (K001 → K003/K004) **yoktur** — layer violation sayılır (§6.2/3).

## §25 Sonraki Adım Matrisi

| # | Sonraki adım | Sahip | Ön koşul |
|---|---|---|---|
| 1 | Q3 kernel↔core sınır kararı | Vault Steward | — |
| 2 | Q4 hdd/ssd envanteri | audio-hw | fiziksel envanter |
| 3 | Q-A1 API protokol kararı | Vault Steward | Q3 |
| 4 | Fiziksel test ortamı kurulumu | Test | dalga-4 bütçe |
| 5 | K001 üretim kodu başlangıcı | embedded | Q3 + Q4 |
| 6 | ADR-099-010 kapanışı (K002 GAP) | Architect | vault-sync — disk gerçeği: `K002-electroncis/` VAR (16 md) → GAP'in kapanışı §9/§23 düzeltmesi + architecture/index.md §6 revizyonu ister |
| 7 | status: verified geçişi | Vault Steward | §15 K1-K10 tamamı |

## §26 Düğüm Dizin Yapısı (disk kanıtı — 2026-10-13 sayımı)

| Dizin/Dosya | Tür | Satır | Durum |
|---|---|---|---|
| `index.md` | klasör künyesi | 90 | dokunulmaz (40-100 bandı) |
| `donanım-anayasa.md` | anayasa (bu dosya) | 500+ hedef | genişletildi |
| `donanım-bagimliliklar.md` | bağımlılık matrisi | 100 | ≥500 hedef |
| `donanım-dogrulama.md` | doğrulama matrisi | 104 | ≥500 hedef |
| `donanım-test-stratejisi.md` | test stratejisi | 112 | ≥500 hedef |
| `donanım-evrensel-dil.md` | sözlük | 116 | ≥500 hedef |
| `donanım-kararlar.md` | karar kaydı | 130 | ≥500 hedef |
| `donanım-web-arastirma.md` | web araştırması | 134 | ≥500 hedef |
| `donanım-kod-ornekleri.md` | kod örnekleri | 135 | ≥500 hedef |
| `donanım-platformlararasi-kod-ornekleri.md` | çapraz-platform kod şablonları (Windows/macOS/Linux · 8 dil grubu) | 1018 | yeni — 2026-10-14 (G1–G8 kapı ölçümü §K1) |
| `donanım-kernel/` | 2 içerik + index | 87 · 66 · — | ≥500 hedef |
| `donanım-core/` | 2 içerik + index | 87 · 62 · — | ≥500 hedef |
| `donanım-api/` | 2 içerik + index | 85 · 61 · — | ≥500 hedef |
| `donanaım-type/` | 2 içerik + index | 87 · 63 · — | ≥500 hedef |
| `donanaım-type/display/` | index + 3 içerik | 4 dosya | proposed (ADR-102) |
| `donanaım-type/power/` | index + 3 içerik | 4 dosya | proposed — önceki oturum |
| `donanaım-type/cables/` | index + 3 içerik | 4 dosya | proposed (ADR-102) |
| `donanaım-type/network/` | index + 3 içerik | 4 dosya (1.970 satır) | proposed (ADR-102) |
| `donanaım-type/memory/` | index + 3 içerik | 4 dosya (1.290 satır) | proposed (ADR-102) |
| `donanaım-type/cpu/` | index + 3 içerik | 4 dosya (~85 KB) | proposed (ADR-102) |
| `donanaım-type/gpu/` | index + 2 içerik | 3 dosya — **gpu-ornekler.md BEKLENİYOR** | proposed (paralel oturum 2026-10-13 22:07 — ADR-102 DIŞI) |
| `donanaım-type/audio/cipler/` | 3 chip dosyası | 541 · 586 · 480 | ak4458/tpa3255 dokunulmaz |
| `donanaım-type/hdd/` | index + hedef | index 47 | hdd-kapsam YENİ ≥500 |
| `donanaım-type/ssd/` | index + hedef | index 47 | ssd-kapsam YENİ ≥500 |

> Yazım notu: `donanaım-type` yazımı (yanlış `n` + Türkçe `ı`) **dokunulmaz** — v2 §3 KURAL 1.

---

**Authority:** Bayram Ali / Vault Steward
**Mode:** Red Team · Human Mode · Truth Mode
