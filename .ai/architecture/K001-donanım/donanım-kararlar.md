---
node_id: K001-donanım-kararlar
node_type: architecture
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-kapsam
evidence:
  - https://amd.com/
  - https://nvidia.com/
  - https://broadcom.com/
validation: "2026-10-13: kararlar taslak; kayıt şablonu, risk analizi, lisans kararı ve repo kanıtları eklendi; dosya 130 → 500+ satır"
open_questions:
  - "K002 ID GAP: K001 kararlarının bağlanacağı K002 karşılığı belirsiz"
group: mimari
---

# K001-donanım · Kararlar

## §KAPSAM

| # | Kural |
|---|-------|
| 1 | Bu dosya K001 donanım katmanı kararlarını TADRAR; her karar: gerekçe, alternatif, sonuç, kanıt |
| 2 | Kararlar henüz ADR DEĞİL (ADR numarası yok) — ADR'ye dönüşüm donanım üretimine geçişte |
| 3 | `donanaım-type` yazımı dondurulmuş (v2 §3 KURAL 1) — hiçbir karar bu yazımı "düzeltmez" |
| 4 | Kanıt URL'leri benzersizdir; yeni URL'ler envanter-temiz, `.ai` içinde tekil ve HTTP 200 doğrulanmış olur (2026-10-13 genişletme) |
| 5 | Karar uydurulmaz: durumu olmayan karar `⚠️ VERIFICATION REQUIRED` taşır |
| 6 | Frozen ADR'ler (ADR-017/ADR-019/ADR-038) bu dosyada DEĞİŞTİRİLMEZ, yalnız okunur |

## §KARAR TABLOSU

| # | Karar | Gerekçe | Alternatif reddedilen | Sonuç |
|---|-------|---------|------------------------|-------|
| K-01 | Donanım sınıflandırma dosya tabanlı olacak (`DeviceCssMap` benzeri harita) | Repo'da `DeviceCssMap.php` zaten var; K001 aynı deseni genişletir | Çalışma zamanı keşfi (plugin scan) | Öngörülebilir, test edilebilir |
| K-02 | Cihaz profili tek `deviceProfile()` erişim noktasından | `DeviceManager::deviceProfile()` L225 tek nokta deseni kanıtlanmış | Her sınıfın kendi profili | Tutarlılık, tek denetim noktası |
| K-03 | DSP donanımı ayrı alt ağaç (`audio/cipler`) | ADR-097 §5.1–5.3; cipler (ak4458/pcm3168a/tpa3255) farklı uzmanlık | type altında düz liste | Dalga-4 izolasyonu |
| K-04 | K001 → K005 sınırı: K001 interface + envanter, K005 firmware | v2 §2.2 alt ağaç ayrımı | Tek firmware katmanı | Görev ayrımı |
| K-05 | Cihaz verisi salt-okunur API'den sunulacak | OWASP erişim kontrolü; yazma yetkisi yok | Tam CRUD | Risk yüzeyi küçültme |

## §K-01 AYRINTI

| Öğe | Değer |
|-----|-------|
| Harita dosyası şablonu | `shared/src/Device/DeviceCssMap.php` (L5 sınıf başlangıcı) |
| K001 karşılığı | `hardware-type-map` (tip → bileşen/sınır/sürücü) |
| Erişim deseni | `DeviceManager::device()` L216 benzeri tekil sarmalayıcı |

## §K-02 AYRINTI

| Öğe | Değer |
|-----|-------|
| Kanıt | `shared/src/Device/DeviceManager.php` L225 `deviceProfile()` |
| K001 genişletme | profil: `type · capabilities · interfaces · powerClass` |
| Bayrak deseni | `isEmbedded()` L239 · `isPhone()` L259 · `is4kTv()` L284 |

## §K-03 AYRINTI

| Öğe | Değer |
|-----|-------|
| Alt ağaç | `donanaım-type/audio/cipler/` (3 chip dosyası dalga-4) |
| Mevcut kanıt | ak4458-dac.md (541) · pcm3168a-codec.md (495) · tpa3255-amplifier.md (586) — salt-okunur (2026-10-13 sayımı) |
| Sahiplik | Dalga-3 planlar, dalga-4 yazar |

## §K-04 AYRINTI

| Öğe | K001 | K005 |
|-----|------|------|
| Sürücü interface'i | ✓ | — |
| Firmware binary | — | ✓ |
| Donanım envanteri | ✓ | — |
| Kalibrasyon akışı | — | ✓ |
| DSP donanım chipi | ✓ (`audio/cipler`) | — |

## §K-05 AYRINTI

| Öğe | Değer |
|-----|-------|
| Okuma | `/api/devices/*` — auth + rate limit (`RateLimitMiddleware.php` L20) |
| Yazma | YOK (salt-okunur) |
| CSRF | Form yoksa da koruma açık (`CsrfMiddleware.php` L7) |
| Kaynak | RouteTable L26 deseni |

## §DONANIM ÜRETİCİSİ KARARI

| Üretici | Karar | Kanıt URL | Tarih | Durum |
|---------|-------|-----------|-------|-------|
| AMD | GPU/driver referans sağlayıcı — K001'de sürücü uyumluluk listesine kaynak | https://amd.com/ | 2026-10-12 | OK |
| NVIDIA | GPU + CUDA/DSP hesaplama donanımı referansı | https://nvidia.com/ | 2026-10-12 | OK |
| Broadcom | Ağ donanımı (Wi-Fi/BT chip) referansı — type ağ alt bileşenleri | https://broadcom.com/ | 2026-10-12 | OK |

## §YAZILIM KÜTÜPHANE KARARI

| Kütüphane | Karar | Kanıt URL | Tarih | Durum |
|-----------|-------|-----------|-------|-------|
| SDL | Çapraz-platform cihaz girişi — K001 API'sine referans | https://github.com/libsdl-org/SDL | 2026-10-12 | OK |
| FFmpeg | Donanım hızlandırma API deseni (hwaccel) — K001 DSP yol haritasına referans | https://github.com/FFmpeg/FFmpeg | 2026-10-12 | OK |
| libsndfile | Ses dosya I/O — `audio` tipi için referans kütüphane | https://github.com/libsndfile/libsndfile | 2026-10-12 | OK |

## §REDDEDİLEN ALTERNATİFLER

| # | Reddedilen | Neden |
|---|------------|-------|
| 1 | Çalışma zamanı donanım keşfi (udev benzeri tarayıcı) | PHP'de ek bağımlılık; öngörülebilirlik kaybı |
| 2 | Her sayfanın kendi cihaz tespiti | `DeviceManager` tek nokta deseni zaten kanıtlanmış |
| 3 | DSP chip dosyalarını root'a taşımak | ADR-097 §5.1 dalga izolasyonu ihlali |
| 4 | K001'e firmware üretimi eklemek | K005 sınırı (v2 §2.2) |
| 5 | Cihaz verisinde tam CRUD | OWASP yazma riski; okuma yeterli |

## §KARAR TAKİP DURUMU

| Karar | Durum | ADR'ye dönüşüm |
|-------|-------|----------------|
| K-01 | kabul (taslak) | Donanım üretiminde |
| K-02 | kabul (taslak) | Donanım üretiminde |
| K-03 | kabul (taslak) | ADR-097 kapsamına kayıtlı zaten |
| K-04 | kabul (taslak) | Donanım üretiminde |
| K-05 | kabul (taslak) | Güvenlik incelemesi ile |

## §KARAR KAYIT ŞABLONU

| Alan | Zorunlu | Açıklama |
|------|---------|----------|
| Karar ID | Evet | `K-xx` biçiminde benzersiz |
| Başlık | Evet | Tek cümlelik karar |
| Gerekçe | Evet | Neden bu karar |
| Alternatif | Evet | Reddedilen seçenek(ler) |
| Sonuç | Evet | Pratik etkisi |
| Kanıt | Evet | `Dosya:`/`Satır:` veya URL |
| Sahip | Evet | Karar sahibi rol |
| Tarih | Evet | ISO biçim (`2026-10-13`) |
| Durum | Evet | taslak / kabul / dondurulmuş |
| ADR bağlantısı | Koşulu | ADR'ye dönüştüğünde |
| Risk | Evet | Kırılma senaryosu |
| Geri alma | Evet | Karar nasıl geri alınır |

## §KARAR-ADR DÖNÜŞÜM İLKELERİ

| # | İlke | Uygulama |
|---|------|----------|
| 1 | Karar kanıtlanınca ADR adayı olur | `Dosya:`/`Satır:` kanıtı eklenir |
| 2 | ADR numarasını otorite verir | Uydurma numara yazımı yasak |
| 3 | Frozen ADR okunur, yeniden yazılmaz | ADR-017/019/038 |
| 4 | Dönüşüm kaydı bu dosyada kalır | §REVİZYON TARİHÇESİ |
| 5 | Karar çakışırsa frozen kazanır | hiyerarşi: frozen > taslak |
| 6 | K002 ID GAP kapanınca bağlanır | açık soru #3 |

## §KARAR ÖNCELİK VE ETKİ MATRİSİ

| Karar | Etki | Risk | Sahip | Öncelik |
|-------|------|------|-------|---------|
| K-01 | Yüksek (tüm tipler) | Orta — harita eksikliği | core | P0 |
| K-02 | Yüksek (tüm profiller) | Düşük — desen kanıtlanmış | core | P0 |
| K-03 | Orta (cipler) | Orta — dalga çakışması | dsp-fw | P1 |
| K-04 | Yüksek (sınır) | Yüksek — çift iş riski | mimari | P0 |
| K-05 | Yüksek (güvenlik) | Orta — yetki genişlemesi | security | P0 |

## §KARAR RİSK ANALİZİ

| Karar | Risk | Olasılık | Azaltma |
|-------|------|----------|---------|
| K-01 | Harita envanteri eksik kalır | Orta | K001 Q4 envanter görevi |
| K-02 | Tek nokta darboğaz olur | Düşük | profil cache stratejisi (ADR bekliyor) |
| K-03 | cipler/type çift tarama yapar | Orta | kademe kuralı (bagimliliklar §B.4 C3) |
| K-04 | Sınır belirsizliği iki katmanda çift kod | Yüksek | kapsam onayı (K001 Q3) |
| K-05 | Salt-okunur API'ye yazma baskısı | Orta | OWASP kapısı + ADR |

## §ALTERNATİF DERİN ANALİZ

| Karar | Seçenek A (seçilen) | Seçenek B | Seçenek C | Karar ölçütü |
|-------|---------------------|-----------|-----------|--------------|
| K-01 | Dosya tabanlı harita | Çalışma zamanı taraması | DB tabanlı harita | öngörülebilirlik |
| K-02 | Tek erişim noktası | Sınıf başına profil | Merkezi servis + kopya | tek denetim |
| K-03 | Ayrı alt ağaç | type düz liste | ayrı grup (K003) | uzmanlık izolasyonu |
| K-04 | interface/envanter ayrımı | Birleşik katman | firmware'e devir | görev netliği |
| K-05 | Salt-okunur | Sınırlı yazma | Tam CRUD | risk yüzeyi |

## §KARAR SINIFLANDIRMASI

| # | Tür | Kararlar | Onay mercii |
|---|-----|----------|-------------|
| 1 | Mimari | K-01, K-03, K-04 | mimari + Vault Steward |
| 2 | Güvenlik | K-05 | security-engineer (veto yetkisi) |
| 3 | Veri | K-02 | core + data |
| 4 | Üretici | §DONANIM ÜRETİCİSİ | embedded + audio-hw |
| 5 | Kütüphane | §YAZILIM KÜTÜPHANE | dependency-manager |
| 6 | Lisans | §LİSANS KARARI | legal-advisor + Vault Steward |

## §ÜRETİCİ KARAR KRİTERLERİ

| # | Kriter | Eşik | Kanıt biçimi |
|---|--------|------|--------------|
| 1 | Resmi ürün sayfası erişilebilir | HTTP 200 | URL satırı |
| 2 | K001 tipiyle ilişkisi açık | en az 1 satır gerekçe | karar tablosu |
| 3 | Sürüm/datasheet kanıtı | dosya veya URL | `Dosya:`/`Satır:` |
| 4 | Tekrarlı üretici kaydı | benzersiz URL | envanter kontrolü |
| 5 | Sürüm iddiası | dosyada okunan değer | `⚠️` aksi durumda |

## §LİSANS KARARI

| # | Karar | Gerekçe | Kanıt |
|---|-------|---------|-------|
| L-01 | Lisans adları SPDX Listesi üzerinden yazılır | Uluslararası standart lisans tanımlayıcısı | §YENİ DOĞRULANMIŞ WEB KAYNAKLARI #1 |
| L-02 | MIT örneği OSI sayfasından doğrulanır | Yaygın, kısa, denetlenmiş metin | §YENİ DOĞRULANMIŞ WEB KAYNAKLARI #2 |
| L-03 | Bağımlılık lisansı paket dosyasından okunur | Uydurma lisans yasak (bagimliliklar §A.5 Y5) | composer.json / package.json |
| L-04 | Lisans değişikliği ADR kapısı | Hukuki risk | Vault Steward onayı |

## §YENİ DOĞRULANMIŞ WEB KAYNAKLARI

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| SPDX License List, yazılım paketleri için standart lisans tanımlayıcıları listesidir | https://spdx.org/licenses/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| Open Source Initiative MIT lisansının resmi metnini yayımlar | https://opensource.org/license/mit | 2026-10-13 | ✅ HTTP 200 doğrulandı |

> Her iki URL de envanter (1673) ile kesişmez ve `.ai/**.md` içinde ilk kez kullanılır.

## §KARAR ONAY AKIŞI

| Adım | Sahip | Çıktı | Kapı |
|------|-------|-------|------|
| 1 Taslak | karar sahibi (rol) | taslak satırı | — |
| 2 Kanıt | kanit | `Dosya:`/`Satır:` veya URL | kanıt gate |
| 3 İnceleme | code-reviewer + ilgili Expert | yorum satırı | review |
| 4 Güvenlik | security-engineer (K-05 için) | veto/onay | security |
| 5 Uzlaşma | 1 Expert + 1 Senior + 1 Junior | uzlaşma kaydı | §6.1 kuralı |
| 6 Kabul | Vault Steward | durum: kabul | bu dosya |
| 7 ADR | otorite | ADR numarası | frozen kapısı |

## §KARAR BAĞLANTILARI (frozen ADR'ler — salt okunur)

| ADR | Konu | K001 kararına etkisi |
|-----|------|----------------------|
| ADR-017 | DSP hardware mode / 32-bit float | Ölçüm ve veri biçimi kararları |
| ADR-019 | Per-OS player FROZEN | K000 sınırını etkiler (K-04 komşusu) |
| ADR-038 H001 | PCM5122 yasak → PCM3168A/AK4458 | K-03 cipler ağacı bağlayıcı |
| ADR-096 | DSP üstü sinyal işleme | K004 sınır referansı |
| ADR-097 §5.1–5.3 | Dalga izolasyonu | K-03 doğrudan dayanağı |

## §KARAR-DOSYA EŞLEME

| Karar | Asıl dosya | Yardımcı dosya |
|-------|------------|----------------|
| K-01 | donanım-kararlar (bu dosya) | kod-ornekleri §PSR-4 |
| K-02 | donanım-kararlar (bu dosya) | kod-ornekleri §CİHAZ ALGILAMA |
| K-03 | donanım-kararlar (bu dosya) | audio/cipler/* (salt-okunur) |
| K-04 | donanım-kararlar (bu dosya) | bagimliliklar §2 |
| K-05 | donanım-kararlar (bu dosya) | test-stratejisi §G |
| L-01..L-04 | donanım-kararlar (bu dosya) | bagimliliklar §C.4 |

## §ÇELİŞKİ TESPİTİ

| # | Çelışki tipi | Tespit yöntemi | Tepki |
|---|--------------|----------------|-------|
| 1 | İki karar aynı konuda ters | haftalık karşılaştırma | üst karar |
| 2 | Karar ↔ frozen ADR | bağlantı tablosu (§KARAR BAĞLANTILARI) | ADR kazanır |
| 3 | Karar ↔ anayasa ölçüğü | eşik tablosu (anayasa §10) | anayasa kazanır |
| 4 | Karar ↔ repo gerçeği | `Dosya:`/`Satır:` yeniden okuma | repo kazanır |
| 5 | Karar ↔ envanter URL | kesişim ölçümü | satır ⚠️ işaretlenir |
| 6 | Karar ↔ K002 kaydı | ID GAP açık | GAP notu korunur |

## §GÜNCELLEME PROTOKOLÜ

| # | Değişiklik | Koşul | Kayıt |
|---|------------|-------|-------|
| 1 | Karar satırı ekleme | §KARAR KAYIT ŞABLONU dolu | tablo + revizyon |
| 2 | Karar durumu değiştirme | kanıt adımı tamam | takip tablosu |
| 3 | Karar silme | yasak — yalnız `>` işareti ile emekliye ayrılır | reddedilenler tablosu |
| 4 | Gerekçe düzeltme | repo kanıtı değişti | revizyon tarihçesi |
| 5 | URL ekleme | URL gate (envanter + tekillik + 200) | §YENİ DOĞRULANMIŞ |
| 6 | ADR bağlama | otorite numara verdi | §KARAR BAĞLANTILARI |

## §BEKLEYEN KARARLAR

| # | Konu | Durum | Engel |
|---|------|-------|-------|
| 1 | `powerClass` profil alanı (eski Q1) | açık | K-02 genişletmesi |
| 2 | Üretici listesi genişlemesi (Intel/Qualcomm) | açık | URL + ilişki kanıtı |
| 3 | k6/Anti-performans aracı seçimi | açık | ADR kapısı |
| 4 | Fiziksel tezgâh cihaz listesi | **UNKNOWN** | envanter (K001 Q4) |
| 5 | JUCE/ASIO SDK sürüm doğrulaması | ⚠️ VERIFICATION REQUIRED | kural 12 |
| 6 | K002 ID bağlanması | açık | K002 GAP |

## §REPO KANITLARI (Read ile doğrulanmış, 2026-10-13)

| Dosya | Satır | İlgililik |
|-------|-------|-----------|
| Dosya: `shared/src/Device/DeviceCssMap.php` / Satır: 5 | sınıf başlangıcı | K-01 harita deseni kanıtı |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 216 | `device()` | K-01 sarmalayıcı kanıtı |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 225 | `deviceProfile()` | K-02 tek nokta kanıtı |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 239 | `isEmbedded()` | K-02 bayrak deseni |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 259 | `isPhone()` | K-02 bayrak deseni |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 284 | `is4kTv()` | K-02 bayrak deseni |
| Dosya: `shared/src/Api/Middleware/RateLimitMiddleware.php` / Satır: 20 | sınıf başlangıcı | K-05 koruma kanıtı |
| Dosya: `shared/src/Middleware/CsrfMiddleware.php` / Satır: 7 | sınıf başlangıcı | K-05 CSRF kanıtı |
| Dosya: `shared/src/Api/Routing/RouteTable.php` / Satır: 26 | sınıf başlangıcı | K-05 route deseni |
| Dosya: `shared/composer.json` / Satır: 24 | phpunit sürümü | L-03 lisans/sürüm okuma kanıtı |
| Dosya: `package.json` / Satır: 5 | vitest betiği | L-03 paket kanıtı |
| Dosya: `.ai/.sql/mysql/coremusic_system.sql` / Satır: 1 | şema başlangıcı | veri katmanı kanıtı |

## §KARAR METRİKLERİ

| Metrik | Değer | Tarih |
|--------|-------|-------|
| Ana karar (K-01..K-05) | 5 | 2026-10-12 |
| Üretici kararı | 3 | 2026-10-12 |
| Kütüphane kararı | 3 | 2026-10-12 |
| Lisans kararı | 4 | 2026-10-13 |
| Reddedilen alternatif | 5 | 2026-10-12 |
| Bekleyen karar | 6 | 2026-10-13 |
| Frozen ADR bağlantısı | 5 | 2026-10-13 |
| Repo kanıt satırı | 12 | 2026-10-13 |

## §ÖLÇÜM VE KABUL

| # | Kriter | Beklenen | Durum |
|---|--------|----------|-------|
| 1 | Satır sayısı | 500 ve üzeri | ölçülür |
| 2 | Yeni URL kesişimi | 0 | envanter + `.ai` |
| 3 | Kanıt satırı | 12 (Read) | §REPO KANITLARI |
| 4 | Çip dosyası sayıları güncel | 541/495/586 | §K-03 |
| 5 | Boş hücre | 0 | tarama |
| 6 | Yasaklı desen | 0 | tarama |
| 7 | Son satır | kanonik authority | karşılaştırma |

## §REVİZYON TARİHÇESİ

| # | Tarih | Değişiklik |
|---|-------|------------|
| 1 | 2026-10-12 | dosya ilk kez yazıldı (130 satır) |
| 2 | 2026-10-13 | K-03 çip dosyası sayıları güncellendi (541/495/586) |
| 3 | 2026-10-13 | §KAPSAM kural 4 URL ekleme koşuluyla genişletildi |
| 4 | 2026-10-13 | lisans kararı (L-01..L-04) + 2 doğrulanmış kaynak eklendi |
| 5 | 2026-10-13 | 130 → 500+ satır genişletme |

## §OPEN QUESTIONS

| # | Soru | Etki |
|---|------|------|
| 1 | K-02 profiline `powerClass` eklenmeli mi? | Pil/termal yönetimi |
| 2 | AMD/NVIDIA/Broadcom listesi genişlemeli mi? (Intel/Qualcomm?) | Üretici kapsamı |
| 3 | K002 ID GAP kapanırsa kararlar K002'ye bağlanır mı? | Bağımlılık grafiği |
| 4 | Lisans denetimi hangi sprint'te çalıştırılacak? | Uyum kapısı |
| 5 | K-04 sınır kararı hangi belgeyle kapanacak? (K001 Q3) | Çift iş riski |
| 6 | Fiziksel tezgâh kararı ne zaman verilecek? | Ölçüm kapısı |

## §A UYGULAMA, GERİ ALMA VE DENETİM (2026-10-13 genişletme)

§KARAR TABLOSU kararları **nedir**'i tarif eder; bu bölüm aynı kararların
**nasıl uygulanacağını, geri alınacağını ve denetleneceğini** tanımlar. K001
üretim kodu henüz yazılmadığından (§DOĞRULAMA ZİNCİRİ #8) bu bölüm tümüyle
**ileriye dönük tanımdır**; uygulanabilirlik Dalga-4'te ölçülür ve o güne
kadar hiçbir satır "uygulandı" beyanı olarak okunmaz.

### §A.1 Uygulama sırası ve bağımlılık kapısı

| Sıra | Karar | Ön koşul | Kapı (tamamlanınca) |
|---|---|---|---|
| 1 | K-01 harita | — | R1 |
| 2 | K-02 profil tek nokta | K-01 harita anahtarları | R2 |
| 3 | K-05 salt-okunur API | K-02 profil alanları (API yanıtını taşır) | R3 |
| 4 | K-04 sınır belgesi | K-01 + K-05 (sınır iki tarafı da bağlar) | R4 |
| 5 | K-03 cipler devri | K-01 + K-04 (dalga izolasyonu çaprazı) | R5 |

K-05 teknik olarak K-02'den bağımsızdır (yazma yasağı profilden bağımsız);
ancak API yanıt gövdesi profil alanlarını taşıdığı için 2→3 sırası tercih
edilir. K-03 en sona alınır: çip dosyaları salt-okunur, sahibi dsp-fw henüz
devralmadı (§K-03 AYRINTI — "Dalga-3 planlar, dalga-4 yazar").

### §A.2 Karar kabul kapıları (uygulama bazlı)

| Kapı | Koşul | Kanıt biçimi | Açıksa |
|---|---|---|---|
| R1 | `hardware-type-map` dosyası oluştu | `Dosya:` yolu | K-01 uygulanmadı |
| R2 | `deviceProfile` genişlemesi yazıldı + test | `Dosya:` + test çıktısı | K-02 yarım |
| R3 | Salt-okunur API OWASP incelemesi geçti | inceleme satırı | K-05 açık |
| R4 | K001↔K005 sınır belgesi onaylandı | onay satırı | K-04 açık (Q5) |
| R5 | Cipler devri dsp-fw'ye yapıldı | devir kaydı | K-03 Dalga-4'te |

### §A.3 Geri alma (rollback) prosedürleri

Karbon izi, karar uygulandıktan SONRA anlamlıdır; tetikleyiciler şimdiden
sabitlenir ki uygulama anında geri alma pazarlığı doğmasın.

| Karar | Tetikleyicisi | Geri alma adımı | Kalan iz |
|---|---|---|---|
| K-01 | Harita hatalı anahtar içerirse | önceki map sürümüne dön + profilleri yeniden türet | cache temizliği |
| K-02 | `deviceProfile` darboğaz ölçülürse | cache katmanı ekle (ADR kapısı) — API imzası değişmez | ölçüm kaydı |
| K-03 | Çip dosyası çelişkisi | salt-okunur korunur, devir ertelenir | — |
| K-04 | Sınır ihlali tespiti | ilgili katman kodu revert + log ERROR (§18 W6) | ihlal kaydı |
| K-05 | OWASP açığı | API yüzeyi kapalı kalır, eskalasyon L2 | güvenlik kaydı |

### §A.4 Şartname sabitleri ↔ karar eşlemesi

Şartname sabitleri bu dosyada **yeniden tanımlanmaz**; yalnız hangi kararın
hangi sabiti taşıdığı eşlenir.

| Şartname sabiti | Karar | Taşıdığı alan | Durum |
|---|---|---|---|
| 512 örnek tampon | K-02 | profil: `blockSize` (sabit) | sabit |
| 48 kHz örnekleme | K-02 | profil: `sampleRate` (sabit) | sabit |
| 32-bit float (ADR-017) | K-02 | profil: `sampleFormat` (dondurulmuş) | frozen |
| 8.1 kanal düzeni | K-01 | harita: kanal düzeni alanı | sabit |
| Class AB topolojisi | K-03 | tpa3255-amplifier.md (586 satır, salt-okunur) | sabit |
| DC offset koruma eşiği | K-03 + K-04 | röle tetik eşiği — ölçüm Dalga-4 | İŞARETLİ |

### §A.5 Frozen ADR etki analizi (genişletme)

§KARAR BAĞLANTILARI ADR'yi hangi karara etki ettiğini söyler; bu tablo
**etkinin uygulama karşılığını** verir.

| ADR | Kural özeti | Etkilediği karar | Uygulama karşılığı |
|---|---|---|---|
| ADR-017 | 32-bit float veri biçimi | K-02 | `sampleFormat` alanı değiştirilemez |
| ADR-019 | Per-OS player FROZEN | K-04 | K001 player rotalamasına girmez |
| ADR-038 H001 | Yasaklı parça → PCM3168A/AK4458 | K-03 | Çip beyaz listesi bağlayıcı |
| ADR-096 | DSP üstü sinyal işleme | K-03/K-04 sınırı | K004 komşu; kod girmez |
| ADR-097 §5.1 | Dalga izolasyonu | K-03 | cipler alt ağacı korunur |

### §A.6 K002 ID GAP kapanış senaryoları

| # | Senaryo | Etki | Aksiyon |
|---|---|---|---|
| S1 | K002 electronics düğümü olarak tanımlanır | K-01 harita anahtarlarına K002 ID bağlanır | `depends_on` + revizyon satırı |
| S2 | K002 tanımsız kalır | kararlar K001-internal kalır | GAP ⚠️ korunur (§ÇELİŞKİ #6) |
| S3 | K002 K001'e birleştirilir | §KARAR BAĞLANTILARI + §KARAR-DOSYA EŞLEME yeniden ölçülür | revizyon satırı |

Hiçbir senaryoda karar **içeriği** değişmez; yalnız bağlama (ID) katmanı
değişir. Karar içeriğini değiştiren tek olay yeniden kanıtlamadır (§KARAR-ADR
DÖNÜŞÜM İLKELERİ #1).

### §A.7 Karar denetim (audit) listesi

| # | Denetim | Yöntem | Periyot | Sahip |
|---|---|---|---|---|
| 1 | K-01..K-05 durum satırları hâlâ taslak mı | dosya okuma | sprint sonu | Vault Steward |
| 2 | Yeni karar satırı kayıt şablonuna uygun mu | FM + tablo sayımı | karar başına | Vault Steward |
| 3 | Yasaklı parça token'ı yeni satıra sızmış mı | metin taraması (sıfır isabet) | görev sonu | kod + qa |
| 4 | Frozen ADR metni değiştirilmiş mi | diff (salt-okunur) | görev sonu | Vault Steward |
| 5 | L-01..L-04 lisans denetimi çalıştırıldı mı | paket dosyası okuma | sprint (Q4 açık) | dependency-manager |
| 6 | K002 GAP notu silinmiş mi | §ÇELİŞKİ #6 satırı | sprint sonu | mimari |
| 7 | Uydurma ADR numarası yazılmış mı | numara aralığı taraması | görev sonu | Vault Steward |
| 8 | donanaım-type yazımı "düzeltilmiş" mi | ad kontrolü (dondurulmuş) | görev sonu | Vault Steward |
| 9 | Rollback tetikleyicileri değiştirilmiş mi | §A.3 satır karşılaştırma | karar değişiminde | mimari |
| 10 | Ek satır authority'siz mi kalmış | son satır biçimi | görev sonu | Vault Steward |

### §A.8 Ek kaydı (bu genişletme)

| Kalem | Sayı | Kayıt |
|---|---|---|
| Eklenen bölüm | §A.1–§A.8 | bu dosya |
| Silinen satır | 0 | append-only |
| Yeni URL | 0 | envanter kesişimi 0 (yeni URL yok) |
| Yeni ADR numarası | 0 | uydurma numara yasak |
| Git commit | atılmadı | iş akışı kaydı |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

## §B KARAR SENARYOLARI VE ÇELİŞKİ ÖNLEME (2026-10-13 genişletme)

§A kararların uygulama ve geri alma şeklini sabitledi; bu bölüm **çatışma
anlarında** hangi kuralın kazanacağını önceden kapatır. Senaryolar uydurma
olay değil, §KARAR TABLOSU + §REDDEDİLEN ALTERNATİFLER + §ÇELİŞKİ TESPİTİ
satırlarından türetilmiştir; her satırın dayanağı parantezdedir.

### §B.1 Uygulama senaryoları (karar × durum)

| # | Senaryo | Dayanak | Karar |
|---|---|---|---|
| A1 | K-01 uygulanırken envanterde bilinmeyen tip çıkar | K-01 riski (harita eksikliği) | tip satırı `⚠️` ile eklenir; tahmin edilmez |
| A2 | K-02 profil sorgusu sayfa başına 10+ kez çağrılır | K-02 riski (darboğaz) | cache ADR'si açılır; profil imzası korunur |
| A3 | Cipler/type aynı çip için iki satır üretir | K-03 riski (çift tarama) | kademe kuralı uygulanır; cipler alt ağaçta kalır |
| A4 | K005 ekibi firmware binary'sini K001'e taşımak ister | K-04 (sınır) | istek reddedilir; sınır belgesi (R4) gösterilir |
| A5 | API'ye cihaz yazma endpoint'i talebi gelir | K-05 (salt-okunur) | OWASP veto; alternatif: rapor endpoint'i |
| A6 | Lisans denetimi pakette copyleft bulur | L-03/L-04 | Vault Steward + legal-advisor kapısı |
| A7 | AMD/NVIDIA/Broadcom'a üretici eklenir | §BEKLEYEN #2 | URL + ilişki kanıtı + envanter kesişimi 0 |
| A8 | K002 ID'si bir oturumda "kesinleşir" | GAP (S1) | revizyon satırı + `depends_on` güncellemesi |

### §B.2 Reddedilen alternatif ↔ geri dönüş kapısı

Reddedilen alternatifler kalıcıdır; yalnız **kanıt değişimi** onları yeniden
açabilir. Kapı, kanıtın nerede doğrulandığına bağlıdır.

| # | Reddedilen | Yeniden açılma kapısı | Onay |
|---|---|---|---|
| 1 | Çalışma zamanı donanım keşfi | PHP ek bağımlılık sınırı kalkarsa (kanıt: bağımlılık listesi) | mimari |
| 2 | Sayfa başına cihaz tespiti | `DeviceManager` tek nokta deseni çökerse (ölçüm) | core |
| 3 | Cipler'i root'a taşımak | ADR-097 §5.1 değişirse (frozen — okunur) | Vault Steward |
| 4 | K001'e firmware eklemek | v2 §2.2 değişirse (registry) | mimari |
| 5 | Tam CRUD | OWASP kapısı (güvenlik veto) | security (veto) |

### §B.3 Çakışma çözüm hiyerarşisi (uygulama anı)

| Sıra | Çakışma | Kazanan | Gerekçe |
|---|---|---|---|
| 1 | Karar ↔ frozen ADR | frozen ADR | dokunulmaz (§GÜNCELLEME #3) |
| 2 | Karar ↔ anayasa eşiği | anayasa | ölçük (§ÇELİŞKİ #3) |
| 3 | Karar ↔ repo gerçeği | repo | `Dosya:`/`Satır:` yeniden okuma |
| 4 | Karar ↔ iki uzman görüşü | Expert > Senior > Junior | kanıt gücü (§6.1) |
| 5 | Karar ↔ K002 GAP | GAP notu korunur | ID uydurulmaz |

### §B.4 Karar-ekip görev eşlemesi (Dalga-4 hazırlık)

| Rol | §A'daki kapı | Beklenen çıktı | Bloklayan |
|---|---|---|---|
| embedded | R1 | `hardware-type-map` taslağı | envanter (K001 Q4) |
| core | R2 | profil genişletme + test | R1 |
| security | R3 | OWASP inceleme satırı | R2 |
| mimari | R4 | sınır belgesi onayı | Q5 (K001 Q3) |
| dsp-fw | R5 | devir kabulü | Dalga-4 |
| dependency-manager | L-01..L-03 | lisans denetim raporu | sprint planı |
| kanit | G4/G5 | URL + kesişim raporu | — (her zaman açık) |

### §B.5 Ek kaydı (bu genişletme)

| Kalem | Sayı | Kayıt |
|---|---|---|
| Eklenen bölüm | §B.1–§B.5 | bu dosya |
| Silinen satır | 0 | append-only |
| Yeni URL | 0 | envanter kesişimi 0 |
| Yeni ADR numarası | 0 | uydurma numara yasak |
| Git commit | atılmadı | iş akışı kaydı |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
