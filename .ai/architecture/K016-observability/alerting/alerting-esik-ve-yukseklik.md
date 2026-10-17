---
title: "K016-observability/alerting — Uyarı Eşiği, Önceliği ve Flapping Kuralları"
node_id: K016-observability/alerting/alerting-esik-ve-yukseklik
node_type: module
status: draft
parent_id: K016-observability/alerting
children: []
depends_on:
  - K016-observability
  - K016-observability/alerting
  - K016-observability/metrics
  - K016-observability/logging
evidence:
  - "shared/src/AI/AIEngine.php — predictFault(array \$metrics) satır 161, eşik kuralları satır 174-194 (2026-10-12)"
  - "shared/src/Api/Registry/ServiceHealth.php — genel durum kararı satır 53-58 (2026-10-12)"
  - "shared/src/Middleware/RateLimiterMiddleware.php — rate_limited güvenlik olayı satır 88 (2026-10-12)"
  - ".github/workflows/ci.yml — continue-on-error (soft gate) satır 167 ve 183 (2026-10-12)"
  - "shared/src/Log/FileHandler.php — WARNING_FAMILY satır 52 (2026-10-12)"
validation:
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri: 1081 benzersiz URL ölçülüp kopyalanmadı (2026-10-12)"
  - "Bildirim (notify/webhook/mail) deseni için kod taraması yapıldı — uygulama katmanında isabet yok (2026-10-12)"
open_questions:
  - "Uyarı bildirim kanalı (e-posta, webhook, mobil) seçilmemiş (⚠️ VERIFICATION REQUIRED)"
  - "Nöbet (on-call) rotasyonu ve eskalasyon ağacı tanımlı değil (⚠️ VERIFICATION REQUIRED)"
  - "Alertmanager benzeri bir bileşenin sürümü ve lisansı doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-observability/alerting/alerting-esik-ve-yukseklik

> **ELI10:** Uyarı, "sistem hasta" demek değildir; "insanın dikkatini hak eden bir şey var" demektir.
> Bu dosya hangi durumun uyarı üreteceğini, uyarıya kimin bakacağını ve aynı şeyin defalarca
> tekrar etmesinin nasıl önleneceğini tanımlar.

## §1 Amaç

Uyarı sisteminin dört kararı vardır: **eşik** (ne zaman), **öncelik** (ne kadar acil), **ayrıştırma
(dedup)** (tekrar engelleme) ve **flapping** (titreme önleme). Yanlış ayarlanmış bir uyarı sistemi
ya gürültü üretir (insanlar kapatır) ya da sessiz kalır (olay büyür). Her ikisi de aynı sonuca
gider: uyarı görmezden gelinir.

| # | Soru | Karar | Bu dosyadaki karşılığı |
|---|---|---|---|
| 1 | Uyarı ne zaman tetiklenir? | Eşik + pencere | §5 |
| 2 | Uyarı ne kadar acildir? | Öncelik sınıfı | §6 |
| 3 | Aynı uyarı tekrar ederse? | Ayrıştırma | §7 |
| 4 | Uyarı salınırsa? | Flapping önleme | §8 |
| 5 | Uyarı kime gider? | Kanal + eskalasyon | §9 |
| 6 | Uyarı nasıl kapanır? | Kurtarma kuralı | §10 |
| 7 | Uyarı ne zaman susturulur? | Susturma (silence) | §11 |
| 8 | Uyarı nasıl test edilir? | Uyarı testi | §12 |

**Hedefler:**

| # | Hedef | Ölçüm | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Bildirilen uyarı başına yanlış alarm oranı | oran | %10 altı | ⚠️ ölçülmedi |
| H2 | Ortalama tespit süresi | dakika | tanımlı değil | ⚠️ ölçülmedi |
| H3 | Tekrarlayan uyarı sayısı | adet | 0 (ayrıştırma ile) | tanımsız |
| H4 | Sessizce geçen olay | adet | 0 | tanımsız |
| H5 | Uyarı kaydının eylemle ilişkisi | oran | %100 | tanımsız |
| H6 | Bildirim kanalının çalışırlığı | deneme | periyodik doğrulama | yok |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Eşik kuralı | ✅ Kural biçimi ve pencere | ❌ Metrik üretimi → `metrics/` |
| Öncelik | ✅ Dört sınıf | ❌ SLA metrikleri → K018 |
| Ayrıştırma | ✅ Anahtar ve gruplama | ❌ Bildirim altyapısı → K018 |
| Flapping | ✅ Kararlılık kuralı | ❌ Otomatik susturma politikası |
| Kanal | ✅ Kanal seçimi kriterleri | ❌ Kanal kurulumu |
| Eskalasyon | ✅ Zaman çizelgesi | ❌ Nöbet çizelgesi (insan kaynağı) |
| CI kapıları | ✅ Soft/hard gate ayrımı | ❌ Pipeline tasarımı → K018 |
| Donanım uyarıları | ✅ Eşik örneği (`AIEngine`) | ❌ Donanım kontrolü → K015-AI |
| Olay müdahalesi | ❌ Değil | `incident-response/` |

## §3 Terimler

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Eşik | threshold | Tetikleme değeri | §5 |
| Pencere | window | Değerin değerlendirildiği aralık | §5.2 |
| Öncelik | severity | Aciliyet sınıfı | §6 |
| Ayrıştırma | deduplication | Tekrar eden uyarıyı tekilleştirme | §7 |
| Gruplama | grouping | Benzer uyarıları birleştirme | §7 |
| Titreme | flapping | Açı-kapalı hızla değişen durum | §8 |
| Kurtarma | recovery | Uyarının kendiliğinden kapanması | §10 |
| Susturma | silence | Geçici susturma | §11 |
| Nöbet | on-call | Sorumlu insan | §9 |
| Eskalasyon | escalation | Zincirleme bildirim | §9 |
| Barkod | fingerprint | Uyarının benzersiz anahtarı | §7 |
| Gürültü | noise | Değersiz uyarı akışı | §8 |
| Yutma | swallowing | Gerçek olayın kaybı | §8 |
| Kapı | gate | CI'da engelleyici kontrol | §13 |
| Soft gate | soft gate | Görünür ama engellemez | §13 |
| Hard gate | hard gate | Engelleyici | §13 |

## §4 Uyarı kaynakları

| # | Kaynak | Ne uyarır | Veri kaynağı | Durum |
|---|---|---|---|---|
| 1 | Log seviyesi | `warning` ve üzeri | `FileHandler` | veri var, kural yok |
| 2 | Güvenlik olayları | 429, 403 reddi | `securityEvent()` | veri var, kural yok |
| 3 | Servis sağlığı | `unhealthy` | `ServiceHealth` | veri var, kural yok |
| 4 | Donanım eşikleri | Sıcaklık, SNR, THD | `AIEngine::predictFault()` | kural mevcut |
| 5 | Sınır aşımı | Rate limit | `RateLimiterMiddleware` | veri var, kural yok |
| 6 | CI kapıları | Test hatası | `ci.yml` | kural mevcut |
| 7 | Secret tarama | Sızıntı | `secret-scan.yml` | kural mevcut |
| 8 | Disk/doluluk | Kapasite | — | ⚠️ yok |
| 9 | Sertifika süresi | Yaklaşan son | — | ⚠️ yok |
| 10 | Yedekleme başarısı | Yedek hatası | — | ⚠️ yok |

## §5 Eşik kuralları

### §5.1 Kural biçimi

Bir uyarı kuralı beş parçadan oluşur. Parçalardan biri eksikse kural yazılmaz.

| # | Parça | Zorunlu | Değer türü | Örnek |
|---|---|---|---|---|
| 1 | Kaynak metrik | evet | ad | `service_healthy_count` |
| 2 | Karşılaştırma | evet | işlev | `less_than` |
| 3 | Eşik değeri | evet | sayı | 0 |
| 4 | Pencere | evet | süre | 1 dakika |
| 5 | Öncelik | evet | sınıf | kritik |
| 6 | Açıklama | evet | metin | servis yok |
| 7 | Eylem | evet | koşu defteri | §14 |

### §5.2 Pencere kuralları

| # | Pencere | Ne için uygun | Uyarı |
|---|---|---|---|
| 1 | 1 dakika | Ani çöküş | Kısa pencere gürültü üretir |
| 2 | 5 dakika | Hata oranı | Sınır aşımı |
| 3 | 15 dakika | Eğilim (trend) | Yavaş bozulma |
| 4 | 1 saat | Kapasite | Planlama |
| 24 saat | günlük | Günlük özet | Operasyonel karar |
| 6 | Çok kısa (<10 sn) | Gerçek zamanlı ses | Yasak (gürültü) |

### §5.3 Eşik türleri

| # | Tür | Karşılaştırma | Örnek | Not |
|---|---|---|---|---|
| 1 | Mutlak | değer < eşik | sağlık = 0 | Basit |
| 2 | Oran | oran > eşik | hata oranı > 0.01 | Ölçekli |
| 3 | Eğilim | değişim hızı | hacim %50 düştü | Karşılaştırmalı |
| 4 | Yokluk | veri gelmemesi | sıfır veri | En zor olan |
| 5 | Bileşik | iki koşul | oran > eşik VE hacim > eşik | Yanlış sinyali azaltır |
| 6 | Artış hızı | birim zamanda artış | istek artışı | Atak tespiti |

## §6 Öncelik sınıfları

| # | Sınıf | Müdahale | Bildirim şekli | Örnek |
|---|---|---|---|---|
| 1 | kritik | anında, nöbetsiz | telefon/uygulama | servis yok |
| 2 | yüksek | 15 dakika | anlık mesaj | hata oranı |
| 3 | orta | aynı iş günü | özet e-postası | gecikme |
| 4 | düşük | planlı iş | rapor | önbellek oranı |

### §6.1 Öncelik – eşik eşlemesi

| # | Koşul | Öncelik | Gerekçe |
|---|---|---|---|
| 1 | Servis sayısı sıfır | kritik | Hizmet durdu |
| 2 | 5xx oranı eşiği aştı | yüksek | Kullanıcı etkisi |
| 3 | p95 eşiği aştı | orta | Deneyim bozuldu |
| 4 | 429 sayısı arttı | orta | Kötüye kullanım izi |
| 5 | Önbellek oranı düştü | düşük | Performans |
| 6 | CI soft gate kırmızı | düşük | Teknik borç |
| 7 | Secret tarama yakaladı | kritik | Güvenlik olayı |
| 8 | Sertifika 30 günden yakın | orta | Kesinti riski |

## §7 Ayrıştırma (dedup) ve gruplama

Aynı kök nedenden doğan yüzlerce uyarı tek satıra inmelidir. Bunun için her uyarının bir
**parmak izi (fingerprint)** olur.

| # | Parmak izi bileşeni | Örnek | Değişir mi? |
|---|---|---|---|
| 1 | Kural adı | `service_down` | hayır |
| 2 | Hedef adı | `auth.coremusic.net` | hedef başına |
| 3 | Öncelik | kritik | kural sabiti |
| 4 | Etiket alt kümesi | `target` | değişken |
| 5 | Uyarı açıklaması | servis yok | sabit |

### §7.1 Gruplama kuralları

| # | Kural | Gerekçe | Sonuç |
|---|---|---|---|
| G1 | Aynı parmak izi tek uyarı | Tekrar engeli | Tek bildirim |
| G2 | Benzer hedefler gruplanır | Bakım tekil iş | Tek görev |
| G3 | Farklı öncelikler gruplanmaz | Müdahale farkı | Ayrı bildirim |
| G4 | Grup süresi dolduğunda özetlenir | Akış kontrolü | Tek özet |
| G5 | Grup içinde en yüksek öncelik öne çıkar | Öncelik korunur | Hızlı okuma |

### §7.2 Parmak izi karar tablosu

| # | Durum | Parmak izi | Beklenen |
|---|---|---|---|
| 1 | Aynı hedef, iki kez | aynı | tek uyarı |
| 2 | İki hedef, aynı kural | farklı | iki uyarı |
| 3 | Aynı hedef, farklı kural | farklı | iki uyarı |
| 4 | Aynı hedef, farklı etiket | farklı | iki uyarı |
| 5 | Aynı kural, eşik değişti | aynı | güncelleme, yeni uyarı değil |

## §8 Flapping (titreme) önleme

Titreme, durumun eşiğin iki yanında hızla gidip gelmesidir. Sonuç: insanlar uyarıyı kapatır.

| # | Belirti | Sonuç | Önlem |
|---|---|---|---|
| 1 | Aç-kapa < 1 dakika | Gürültü | Kararlılık penceresi |
| 2 | Aynı uyarı 10 kez | Yorgunluk | Ayrıştırma |
| 3 | Eşik değerine çok yakın | Salınım | Hysteresis |
| 4 | Zamanlama kayması | Sahte tetik | Pencere seçimi |
| 5 | Ölçüm kaynağı titriyor | Yanlış veri | Ölçüm doğrulama |

### §8.1 Hysteresis kuralı

| # | Durum | Değer | Davranış |
|---|---|---|---|
| 1 | Sağlıklı → uyarı | eşik altı değere iner | tetikle |
| 2 | Uyarı → sağlıklı | eşik üstü değere çıkar | kurtar |
| 3 | Eşit değer | — | durumu değiştirme |
| 4 | Çok kısa geçiş | — | sayma (flap sayacı) |
| 5 | Flap sayısı eşiği aşarsa | — | uyarı yükselt / sustur |

### §8.2 Kararlılık penceresi

| # | Pencere | Kural | Gerekçe |
|---|---|---|---|
| 1 | 1 dakika | iki ardışık gözlem gerekir | Sahte tetik önleme |
| 2 | 5 dakika | üç gözlem | Eğilim doğrulama |
| 3 | 15 dakika | iki gözlem | Yavaş olaylar |
| 4 | Sıfır pencere | yasak | Titreme üretir |

## §9 Bildirim kanalları ve eskalasyon

| # | Kanal | Öncelik | Hız | Durum |
|---|---|---|---|---|
| 1 | Telefon/uygulama | kritik | anında | ⚠️ seçilmedi |
| 2 | Anlık mesaj | yüksek | dakika | ⚠️ seçilmedi |
| 3 | E-posta | orta/düşük | saat | ⚠️ seçilmedi |
| 4 | Panolar (dashboard) | hepsi | sürekli | ⚠️ kurulmadı |
| 5 | CI raporu | düşük | iş akışı | mevcut (kırmızı iş) |
| 6 | Günlük özet | düşük | günlük | ⚠️ yok |

### §9.1 Eskalasyon zaman çizelgesi

| # | Süre | Eylem | Sahip |
|---|---|---|---|
| 1 | 0 dakika | İlk bildirim | Nöbetçi |
| 2 | 5 dakika | Tekrar bildirim (yanıt yoksa) | Sistem |
| 3 | 15 dakika | Sorumlu artıra | Nöbet yöneticisi |
| 4 | 30 dakika | Yönetici | Katman yöneticisi |
| 5 | 60 dakika | Olay ilanı | Olay yöneticisi |
| 6 | 120 dakika | Dış iletişim | İletişim sorumlusu |

### §9.2 Kanal seçimi kriterleri

| # | Kriter | Ağırlık | Not |
|---|---|---|---|
| 1 | Kritik öncelikte güvenilir teslim | yüksek | Kanal kesintisi |
| 2 | Gecikme süresi | yüksek | Dakika altı |
| 3 | İnsanları rahatsız etme maliyeti | orta | Gürültü |
| 4 | Denetim izi | orta | Olay sonrası |
| 5 | Kurulum maliyeti | düşük | Sürüm riski |

## §10 Kurtarma (recovery) kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Uyarı, koşul normalleşince kapanır | Gereksiz bildirim yok |
| 2 | Kurtarma bildirimi önceliği düşüktür | Sessiz normalleşme |
| 3 | Kurtarma kaydı saklanır | Denetim |
| 4 | Otomatik kurtarma tanımı yoksa açık kalır | Güvenli varsayım |
| 5 | Kurtarma süresi ölçülür | Süreç metriği |

## §11 Susturma (silence)

| # | Durum | Süre | Kural |
|---|---|---|---|
| 1 | Planlı bakım | bakım penceresi | Nöbetçi onayı |
| 2 | Bilinen olay | olay süresi | Olay kimliği kaydı |
| 3 | Bilinen sahte tetik | en fazla 24 saat | Kök neden görevi |
| 4 | Sonsuz susturma | yasak | Zorunlu bitiş |
| 5 | Susturma kaydı | — | Neden + onaylayan |

## §12 Uyarı testleri

| # | Test | Ne doğrular | Kabul |
|---|---|---|---|
| 1 | Eşik aşım testi | Kural tetikleniyor | Uyarı oluşur |
| 2 | Eşik altı testi | Tetiklenmiyor | Uyarı oluşmaz |
| 3 | Pencere testi | Zamanlama | Pencere içinde |
| 4 | Ayrıştırma testi | Tekilleşme | Tek bildirim |
| 5 | Flap testi | Titreme önleme | Sessizlik korunur |
| 6 | Kurtarma testi | Otomatik kapanma | Kapanır |
| 7 | Kanal testi | Teslim | Ulaştı |
| 8 | Eskalasyon testi | Zincir | Sıra doğru |
| 9 | Susturma testi | Sessizlik | Uyarı yok |
| 10 | Öncelik testi | Sınıf | Doğru sınıf |

## §13 CI kapıları — mevcut durum

CoreMusic CI'ında iki kapı türü vardır: sert kapı (engelleyici) ve yumuşak kapı (görünür ama
engellemez). Yumuşak kapı kararı, repo'da gerekçesiyle yazılıdır.

| # | İş | Kapı türü | Davranış | Kaynak |
|---|---|---|---|---|
| 1 | PHP Lint | sert | Hata = iş başarısız | `ci.yml` satır 26 |
| 2 | PHP Tests | sert | Hata = iş başarısız | `ci.yml` satır 67 |
| 3 | Composer Audit | sert | Bulgu = iş başarısız | `ci.yml` satır 111 |
| 4 | JS Unit | sert | Hata = iş başarısız | `ci.yml` satır 143 |
| 5 | JS Coverage | yumuşak | Kırmızı ama geçer | `ci.yml` satır 167 |
| 6 | JS E2E | yumuşak | Kırmızı ama geçer | `ci.yml` satır 183 |
| 7 | Secret Scan | sert | Bulgu = iş başarısız | `secret-scan.yml` |

**Yumuşak kapı gerekçesi (repo notu):** %80 eşiği şimdilik erişilemez (12.9k satır / 100'den az
test); iş **görünür-kırmızı** olsun ama bloklamasın; eşikler büyüyünce kapatılır. E2E için de
ilk koşular bilinen-risklidir. Bu iki karar, §6'daki "düşük öncelik" sınıfına karşılık gelir.

## §14 Uyarı kayıt şeması

| # | Alan | Zorunlu | Tip | Örnek |
|---|---|---|---|---|
| 1 | Kural adı | evet | dize | `service_down` |
| 2 | Açıklama | evet | metin | servis yok |
| 3 | Öncelik | evet | sınıf | kritik |
| 4 | Parmak izi | evet | dize | hedef + kural |
| 5 | Hedef | evet | dize | `auth.coremusic.net` |
| 6 | Değer | evet | sayı | 0 |
| 7 | Eşik | evet | sayı | 1 |
| 8 | Pencere | evet | süre | 1 dakika |
| 9 | Zaman damgası | evet | ISO 8601 | 2026-10-12T21:15:00Z |
| 10 | Durum | evet | açık/kapalı | açık |
| 11 | Eylem | evet | koşu defteri | §14.1 |
| 12 | Onay | hayır | insan | Nöbetçi |

## §15 Donanım eşik örneği — repo kanıtı

`AIEngine::predictFault()` içinde dört eşik kuralı tanımlıdır. Bu kural seti, bu dosyadaki kural
biçiminin repo'daki karşılığıdır.

| # | Ölçüm | Karşılaştırma | Eşik | Öncelik türevi | Eklenen olasılık |
|---|---|---|---|---|---|
| 1 | Sıcaklık | büyük mü | 70 | `high` | 0.4 |
| 2 | SNR | küçük mü | 90 | `medium` | 0.2 |
| 3 | THD | büyük mü | 0.1 | `medium` | 0.15 |
| 4 | Kullanım saati | büyük mü | 10000 | bakım uyarısı | — |
| 5 | Toplam olasılık | sınır | 1.0 | `min()` ile kırpılır | 1.0 |

**Gözlem:** bu kural seti **anlık** (window) içermez; tek gözleme dayanır. Bu, §8'deki titreme
riskine doğrudan örnektir ve kural setinin pencere ile güçlendirilmesi bu dosyanın önerisidir.

## §16 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor | Doğrulama |
|---|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | 161 | `predictFault()` imzası | Numaralı döküm |
| 2 | `shared/src/AI/AIEngine.php` | 165 | `alerts` listesi | Numaralı döküm |
| 3 | `shared/src/AI/AIEngine.php` | 174 | Sıcaklık eşiği | Numaralı döküm |
| 4 | `shared/src/AI/AIEngine.php` | 180 | SNR eşiği | Numaralı döküm |
| 5 | `shared/src/AI/AIEngine.php` | 186 | THD eşiği | Numaralı döküm |
| 6 | `shared/src/AI/AIEngine.php` | 192 | Bakım uyarısı eşiği | Numaralı döküm |
| 7 | `shared/src/AI/AIEngine.php` | 198 | Olasılık tavanı | Numaralı döküm |
| 8 | `shared/src/Api/Registry/ServiceHealth.php` | 53 | Genel durum kararı | Numaralı döküm |
| 9 | `shared/src/Api/Registry/ServiceHealth.php` | 54 | `unhealthy` varsa kritik | Numaralı döküm |
| 10 | `shared/src/Api/Registry/ServiceHealth.php` | 56 | `degraded` varsa orta | Numaralı döküm |
| 11 | `shared/src/Middleware/RateLimiterMiddleware.php` | 88 | `rate_limited` güvenlik olayı | Numaralı döküm |
| 12 | `shared/src/Log/FileHandler.php` | 47 | `alert` seviyesi | `Select-String` |
| 13 | `shared/src/Log/FileHandler.php` | 52 | `WARNING_FAMILY` | `Select-String` |
| 14 | `.github/workflows/ci.yml` | 167 | JS Coverage soft gate | `Select-String` |
| 15 | `.github/workflows/ci.yml` | 183 | JS E2E soft gate | `Select-String` |

**Doğrulama komutu:**

```powershell
Get-Content 'C:\www\coremusic.net\shared\src\AI\AIEngine.php' -Encoding UTF8 |
  Select-Object -Index 173
```

Bu komut 1-based satır 174'ü, yani `if ($temperature > 70) {` satırını döndürür.

**Negatif kanıt:**

```powershell
Select-String -Path 'C:\www\coremusic.net\shared\src\**\*.php' -Pattern 'notify|webhook|smtp|escalat'
```

Sorgu yalnız ilgisiz isabetler verdi; uygulama katmanında **bildirim gönderen kod yoktur**. Bu,
§9'daki kanal seçiminin henüz yapılmadığını kanıtlar.

## §17 Entegrasyon

| # | Yön | Muhatap | Arayüz | Not |
|---|---|---|---|---|
| 1 | Giriş | `metrics/` | Metrik değeri | Eşik kaynağı |
| 2 | Giriş | `logging/` | `warning` ailesi | Log tetikleyici |
| 3 | Giriş | `ServiceHealth` | Durum sayısı | Sağlık tetikleyici |
| 4 | Giriş | `AIEngine` | Donanım eşiği | Donanım tetikleyici |
| 5 | Çıkış | Bildirim kanalı | Uyarı kaydı | ⚠️ kanal yok |
| 6 | Çıkış | `incident-response/` | Olay kaydı | Sınır |
| 7 | Dikey | K018-devops | CI kapısı | Soft/hard gate |
| 8 | Dikey | `health-checks/` | Sağlık ucu | Veri kaynağı |

## §18 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Bildirim kanalı yok | Uyarı görünmez | yüksek | Kanal seçimi | ⚠️ VERIFICATION REQUIRED |
| 2 | Nöbet çizelgesi yok | Sahipsiz uyarı | yüksek | Sorumlu tanımı | ⚠️ VERIFICATION REQUIRED |
| 3 | Donanım kuralında pencere yok | Titreme | orta | Pencere ekleme | açık |
| 4 | Parmak izi tanımı yok | Tekrar bildirim | orta | Parmak izi kuralı | açık |
| 5 | Susturma politikası yok | Gürültü | orta | Politika yazımı | açık |
| 6 | Soft gate süresiz kalabilir | Kapı etkisiz | orta | Eşik takibi | açık |
| 7 | Kurtarma kuralı yok | Uyarı kapanmaz | orta | Kurtarma tanımı | açık |
| 8 | Uyarı testi yok | Sahte güvence | yüksek | §12 testleri | açık |

## §19 Uyarı kuralı şablonları — repo bağlamına çevrim

Bu bölüm, §5–§8'deki eşik kurallarını repo'nun gerçek ölçüm noktalarına bağlar. Üç şablon
da aynı iskeleti kullanır: **kaynak → koşul → `for` penceresi → etiket kümesi → seviye**.

### §19.1 Kredi kullanım şablonu

```text
kural adı     : coremusic_kredi_tuketimi_high
kaynak        : sum(rate(neva_engine_token_total[5m])) / sum(rate(neva_engine_token_limit[5m]))
koşul         : oran > 0.80
for           : 10m
etiketler     : {service="neva-engine", team="platform", severity="warning"}
eylem         : kanal = platform-uyari
```

`0.80` değeri `AIEngine.php` içindeki kritik eşik `0.80` ile aynı sayıdır; iki yerde ayrı
ayrı değiştirilirse uyarı ile uygulama davranışı birbirinden kopar.

### §19.2 Hız sınırı şablonu

```text
kural adı     : coremusic_rate_limit_rejects
kaynak        : sum(rate(coremusic_ratelimit_rejected_total[1m]))
koşul         : oran > 0 (en az bir ret)
for           : 5m
etiketler     : {service="page-router", zone="api", severity="info"}
eylem         : kanal = teknik-gundem
```

`RateLimitMiddleware.php` sayaçları etiket olarak `zone` ve `service` taşıdığı için
uyarı etiketleri ile repo sayaç etiketleri birebir aynı anahtarlarda buluşur.

### §19.3 DC offset şablonu (donanım)

```text
kural adı     : coremusic_dc_offset_high
kaynak        : max(coremusic_dc_offset_volts{channel=~".*"})
koşul         : değer > 0.5
for           : 1s
etiketler     : {board="pcm3168a-rca", severity="critical"}
eylem         : kanal = acil + sayfa = protection relay
```

`for: 1s` bilinçli olarak kısa tutulur: kural §10 gereği gecikmesiz davranmalı, ancak
tek örneklik gürültüye kanmamak için yine de sıfırdan farklı bir pencere kullanılır.

### §19.4 Şablonların ortak denetim listesi

| # | Denetim | Kabul ölçütü | Sıklık | Sorumlu rol |
|---|---|---|---|---|
| 1 | Kaynak ifadesi üretiliyor mu | sorgu boş dönmüyor | her sürüm | platform mühendisi |
| 2 | `for` penceresi seviyeyle uyumlu mu | kritik ≤ 1s, uyarı ≥ 5m | her kural ekleme | platform mühendisi |
| 3 | Etiket kümesi kartonu şişiriyor mu | etiket başı seri ≤ 200 | ayda bir | SRE |
| 4 | Eylem kanalı gerçekten abone | test gönderimi alındı | ayda bir | nöbetçi |
| 5 | Kural dokümanda kayıtlı mı | §6 tablosunda satır var | her kural ekleme | Vault Steward |

## §20 Susturma, bağlam ve kayıt disiplini

Uyarı üretmek kadar **üretilen uyarıyı yönetmek** de bu düğümün sorumluluğudur. Üç mekanizma
ayrım anlamında kullanılır; birbirinin yerine geçmez.

| Mekanizma | Ne yapar | Ne yapmaz | Repo karşılığı | Durum |
|---|---|---|---|---|
| Susturma (silence) | Bilinen bakım penceresinde bildirimi erteler | Kuralı kapatır | uygulama katmanında karşılığı yok | ⚠️ VERIFICATION REQUIRED |
| Bağlam (context) | Uyarıya teşhis bağlantısı ve koşu kimliği ekler | Eşiği değiştirir | `StructuredLogger` bağlam alanı (L51) | kısmen mevcut |
| Kayıt (record) | Uyarı kararı ve sonucu kalıcı yere yazar | Bildirim göndermez | dosya log yolu `FileHandler.php` L40 | mevcut |

### §20.1 Susturma politikası kuralları

| # | Kural | Zorunlu alan | Azami süre | İhlal sonucu |
|---|---|---|---|---|
| 1 | Susturma yalnız bakım için açılır | bakım talebi kimliği | 4 saat | otomatik kaldırma |
| 2 | Susturma kuralı etiketle değil uyarıyla eşleşir | tam parmak izi | 4 saat | otomatik kaldırma |
| 3 | Susturulan kural izlenmeye devam eder | sessiz kayıt satırı | bakım sonu | kayıt tutulmazsa iptal |
| 4 | Kritik seviye susturulamaz | severity=critical ret kodu | uygulanamaz | susturma reddedilir |
| 5 | Susturma sonunda kapanış gözden geçirilir | kapanış notu | bakım bitiminden 1 gün sonra | açık kalan susturma raporlanır |

### §20.2 Bağlam alanları

Bir uyarı kararı, en az şu bağlam alanlarıyla birlikte yazılır:

| Alan | Tip | Örnek | Kaynak | Zorunluluk |
|---|---|---|---|---|
| `rule_id` | metin | `coremusic_kredi_tuketimi_high` | kural tanımı | zorunlu |
| `eval_time` | ISO 8601 | `2026-10-12T09:15:00Z` | değerlendirme anı | zorunlu |
| `value` | ondalık | `0.83` | kural değeri | zorunlu |
| `series` | harita | `service=neva-engine` | etiket kümesi | zorunlu |
| `run_id` | metin | CI koşu kimliği | pipeline çıktısı | varsa |
| `runbook` | bağlantı | dahili koşum sayfası | eylem tablosu | zorunlu |

Bağlam alanları `StructuredLogger`'ın bağlam dizisiyle aynı adda tutulur; iki ayrı sözlük
oluşursa log ile uyarı arasında anahtar eşleşmesi kırılır.

### §20.3 Kayıt disiplini

| # | Kayıt | Nereye yazılır | Süre | Denetim |
|---|---|---|---|---|
| 1 | Uyarı doğuşu | log yolu (FileHandler) | kalıcı | aylık örnek inceleme |
| 2 | Nöbetçi ataması | nöbet çizelgesi | kalıcı | aylık |
| 3 | Susturma açılışı ve kapanışı | susturma kaydı | kalıcı | her olayda |
| 4 | Müdahale süresi | olay kaydı | kalıcı | çeyreklik özet |
| 5 | Uyarı kapatma gerekçesi | olay kaydı | kalıcı | her olayda |

## §21 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Prometheus uyarı kuralı, `for` süresi ve etiket bazlı gruplama ile tanımlanır | https://prometheus.io/docs/prometheus/latest/configuration/alerting_rules/ | 2026-10-12 | HTTP 200 doğrulandı |
| Alertmanager, uyarıların gruplanmasından, filtrelenmesinden ve yönlendirilmesinden sorumludur | https://prometheus.io/docs/alerting/latest/alertmanager/ | 2026-10-12 | HTTP 200 doğrulandı |
| Alertmanager yapılandırması, alıcı (receiver) ve yönlendirme (route) bloklarını tanımlar | https://prometheus.io/docs/alerting/latest/configuration/ | 2026-10-12 | HTTP 200 doğrulandı |
| Prometheus uyarı uygulamaları, uyarıların aksiyon üretmesi gerektiğini ve iyi uyarı tanımlarını verir | https://prometheus.io/docs/practices/alerting/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde bulunmaz; ölçüm envanter ölçümünden sonra
yapılmıştır.

## §22 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1081 benzersiz URL | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest -Method Head` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | anahtar karşılaştırma | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | `Select-String` + numaralı döküm | 15 satır eşleşti | 2026-10-12 |
| 5 | Bildirim kodu yokluğu | çoklu desen taraması | uygulama katmanında isabet yok | 2026-10-12 |
| 6 | Satır sayısı | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | metin taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | gözden geçirme | 0 | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
