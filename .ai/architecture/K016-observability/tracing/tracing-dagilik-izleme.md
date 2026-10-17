---
title: "K016-observability/tracing — Dağıtık İzleme (Trace/Span) Modeli"
node_id: K016-observability/tracing/tracing-dagilik-izleme
node_type: module
status: draft
parent_id: K016-observability/tracing
children: []
depends_on:
  - K016-observability
  - K016-observability/tracing
evidence:
  - "shared/src/PageRouter/PageRouterKernel.php — ReadAllLines 324 satır, generateTraceId satır 317 (2026-10-12)"
  - "shared/src/PageRouter/ResponseEmitter.php — X-Trace-Id başlığı satır 97 (2026-10-12)"
  - "shared/src/Api/Gateway.php — UuidV7::generate() satır 88, ikinci trace kimliği (2026-10-12)"
  - "shared/src/Middleware/MiddlewarePipeline.php — hrtime ile span-benzeri sarma satır 42-48 (2026-10-12)"
validation:
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri: 1081 benzersiz URL ölçülüp kopyalanmadı (2026-10-12)"
  - "İki bağımsız trace kimliği üretici tespit edildi (PageRouterKernel + Gateway) (2026-10-12)"
open_questions:
  - "X-Trace-Id yanıt başlığının istemci tarafında okunup okunmadığı bilinmiyor (⚠️ VERIFICATION REQUIRED)"
  - "PageRouter trace kimliği ile Gateway trace kimliği arasında köprü kurulup kurulmadığı bilinmiyor (⚠️ VERIFICATION REQUIRED)"
  - "Örnekleme (sampling) oranı için henüz karar verilmedi (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-observability/tracing/tracing-dagilik-izleme

> **ELI10:** Log tek bir yerde olan olayı anlatır, iz (trace) ise bir isteğin sistem boyunca nasıl
> dolaştığını anlatır. Bu dosya; iz kimliğinin nerede üretildiğinin, hangi başlıkla taşındığının ve
> hangi sarmalayıcıların "span" sayılacağının tek yetkili tanımıdır.

## §1 Amaç

Dağıtık izleme üç parçadan oluşur: **iz (trace)**, **span** ve **bağlam yayılımı (context
propagation)**. CoreMusic'te iz kimliği üretilmektedir; buna karşılık span ağacı ve toplayıcı
repo'da bulunmamaktadır. Bu dosya, mevcut durumu ve hedef modeli birlikte tanımlar.

| # | Soru | Karar | Repo durumu |
|---|---|---|---|
| 1 | İz kimliği nerede üretilir? | İstek girişinde tek nokta | mevcut (`PageRouterKernel`) |
| 2 | İz kimliği hangi biçimdedir? | 8-4-4-4-12 UUID biçimi | mevcut (`generateTraceId`) |
| 3 | Kimlik hangi başlıkla taşınır? | `X-Trace-Id` yanıt başlığı | mevcut (`ResponseEmitter`) |
| 4 | Kimlik hangi log alanına yazılır? | `traceId` / `trace_id` | mevcut (iki alan adı) |
| 5 | Span kimliği var mı? | Hayır | yok |
| 6 | Üst-alt bağlam başlığı var mı? | Hayır (`traceparent` yok) | yok |
| 7 | Örnekleme oranı nedir? | ⚠️ tanımlı değil | yok |
| 8 | Toplayıcı hangisi? | ⚠️ seçili değil | yok |

**Hedefler:**

| # | Hedef | Ölçüm | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Her istekte tek iz kimliği | olsun/olmasın | 1 | mevcut |
| H2 | Aynı istekte log ve JSON kaydı aynı kimliği taşır | eşleşme | birebir | mevcut |
| H3 | Yanıt başlığı ile log kaydı eşleşir | eşleşme | birebir | mevcut |
| H4 | Tek trace kimliği üretici | adet | 1 | ⚠️ 2 tespit edildi |
| H5 | Üst-bağlam başlığı (`traceparent`) | varlık | var | yok |
| H6 | Örnekleme kararı | karar | verilmiş | ⚠️ yok |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| İz kimliği üretimi | ✅ Biçim, kaynak, tekillik | ❌ Kimlik üretimi kodu değişikliği |
| Yayılım | ✅ Yanıt başlığı + log alanları | ❌ Servisler arası header aktarımı (henüz yok) |
| Span modeli | ✅ Hedef tanım ve eşleme | ❌ Span API implementasyonu |
| Örnekleme | ✅ Kural ve seçenekler | ❌ Oran seçimi → üst karar |
| Toplayıcı | ✅ Seçim kriterleri | ❌ Kurulum → K018-devops |
| Korelasyon | ✅ `traceId` ile log bağı | `correlation/` altındaki kimlik politikası |
| Ses zinciri izleme | ❌ Değil | `audio-diagnostics/` + K015 |
| Uyarı | ❌ Değil | `alerting/` |

## §3 Terimler

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| İz | trace | Bir isteğin uçtan uca yolu | `traceId` |
| İz kimliği | trace id | İz benzersiz anahtarı | UUID biçimli dize |
| Span | span | İz içindeki tek iş bloğu | ⚠️ yok |
| Üst-bağlam | context | Üst kimlik + kendi kimliği + bayraklar | ⚠️ yok |
| Yayılım | propagation | Bağlamın başlıkla taşınması | `X-Trace-Id` |
| Örnekleme | sampling | Hangi izlerin kaydedileceği | ⚠️ karar yok |
| Toplayıcı | collector | İzleri toplayan bileşen | ⚠️ yok |
| Kuyruk | queue | Bekleyen iz önbelleği | ⚠️ yok |
| Bayrak | flags | Örnekleme kararı biti | ⚠️ yok |
| Kimlik biçimi | id format | 8-4-4-4-12 | `generateTraceId()` |
| Zamansal sıra | ordering | Olayların sırası | log zaman damgası |
| Köprü | stitching | İki sistemin izini birleştirme | ⚠️ eksik |
| Sarmalayıcı | wrapper | İşe sarılan ölçüm bloğu | `MiddlewarePipeline::wrap()` |
| Yanıt başlığı | response header | İstemciye kimlik iadesi | `X-Trace-Id` |
| Kimlik alanı adı | field name | Log anahtarı | `traceId` ve `trace_id` |

## §4 İz kimliği biçimi ve üretimi

### §4.1 Biçim kuralları

| # | Kural | Değer | Kaynak |
|---|---|---|---|
| 1 | Uzunluk | 36 karakter (tireli) | `generateTraceId()` |
| 2 | Ham uzunluk | 16 bayt (128 bit) | `random_bytes(16)` |
| 3 | Sürüm biti | 4 (UUID v4) | `& 0x0f` birleşimi `0x40` |
| 4 | Varyant biti | 8, 9, a, b | `& 0x3f` birleşimi `0x80` |
| 5 | Karakter kümesi | küçük hex + tire | `bin2hex` |
| 6 | Sıralanabilirlik | hayır (v4) | Sürüm 4 |
| 7 | Karıştırma kaydı | 16 bayt tam entropy | `random_bytes` |
| 8 | Yeniden kullanım | yasak | Her istekte yeni |

### §4.2 İki kimlik üretici — tespit

Repo'da iki bağımsız üretici vardır. Bu, §10.1'de ayrıntılı ele alınan bir mimari bulgudur.

| # | Üretici | Konum | Biçim | Kullanım alanı |
|---|---|---|---|---|
| 1 | `PageRouterKernel::generateTraceId()` | `PageRouterKernel.php` satır 317 | v4 biçimli 8-4-4-4-12 | Sayfa yönlendirme |
| 2 | `UuidV7::generate()` | `Gateway.php` satır 88 | v7 (zamana sıralı) | API Gateway yakalama |
| 3 | Fark | biçim | v4 vs v7 | Kimlik eşleşmez |
| 4 | Fark | tetikleyici | her istek vs yalnız hata | Kapsam farklı |

### §4.3 Kimlik üretimi güvenliği

| # | Kural | Gerekçe | Uygulama |
|---|---|---|---|
| 1 | Tahmin edilebilir olmamalı | İz sızıntısı | `random_bytes` |
| 2 | Zaman damgası içermemeli | Gizlilik (v4) | v4 seçimi |
| 3 | Aynı istekte tekrar edilmemeli | Tekillik | fonksiyon çağrısı |
| 4 | Log'a yazılırken maskelenmemeli | Köprü işlevi | `Redactor` UUID korur |
| 5 | Yanıt başlığıyla aynı olmalı | İstemci korelasyonu | `emitTraceAndExtras` |
| 6 | Boş bırakılmamalı | Kayıp iz | fallback yok, her zaman üretilir |

### §4.4 Biçim karşılaştırmasının pratik sonucu

| # | Özellik | PageRouter v4 | Gateway v7 | Sonuç |
|---|---|---|---|---|
| 1 | Zamana göre sıralanabilirlik | hayır | evet | Sıralama tutarsız |
| 2 | İki log arasında arama | biçim farkı | biçim farkı | Eşleşme garantisi yok |
| 3 | Depolama anahtarı | 36 karakter tireli | 36 karakter tireli | Uzunluk aynı |
| 4 | Bit düzeni (sürüm/y varyant) | 4 / 8-b | 7 / 8-b | Farklı sürüm alanı |
| 5 | Tekilleştirme | istek başına | yalnız hata yolu | Kapsam dar |
| 6 | Ortak köprü alanı | — | — | Yok (§10.1) |

**Sonuç:** iki üreticinin çıktısı **aynı istekte bile eşleşmez**; köprü kurulmadan tek iz
zinciri okunamaz (§8 satır 12: kimlik köprüsü yok).

## §5 Bağlam yayılımı (context propagation)

### §5.1 Yayılım yönleri

| # | Yön | Mekanizma | Durum | Kaynak |
|---|---|---|---|---|
| 1 | İstek → çekirdek | Fonksiyon argümanı | mevcut | `handle()` |
| 2 | Çekirdek → log | `setTraceId()` | mevcut | `PageRouterKernel` satır 88 |
| 3 | Çekirdek → dosya logu | `FileHandler::setTraceId()` | mevcut | satır 91 |
| 4 | Çekirdek → yanıt | `X-Trace-Id` başlığı | mevcut | `ResponseEmitter` satır 97 |
| 5 | Çekirdek → hata gövdesi | `ErrorResponse` `trace_id` | mevcut | `ErrorResponse.php` |
| 6 | Servis → servis | `traceparent` başlığı | yok | ⚠️ eksik |
| 7 | İstemci → servis | Gelen başlık okuması | yok | ⚠️ eksik |
| 8 | Vardiya → kuyruk | Yok | yok | ⚠️ eksik |

### §5.2 Başlık kuralları

| # | Başlık | Yön | Durum | Kural |
|---|---|---|---|---|
| 1 | `X-Trace-Id` | yanıt | mevcut | Her yanıtta varsa yazılır |
| 2 | `traceparent` | istek/yanıt | yok | Standart yayılım için gerekli |
| 3 | `tracestate` | istek/yanıt | yok | Özel sağlayıcı durumu için |
| 4 | `X-Request-Id` | istek | yok | Kullanılmıyor |
| 5 | `baggage` | istek/yanıt | yok | Kullanılmıyor |

### §5.3 Sarmalayıcı (span benzeri) yapısı

`MiddlewarePipeline::wrap()`, her ara katmanı `hrtime` ile sarar. Bu yapı, span üretimi için
doğal yerleşimdir; fakat üretilen süre yalnız loga yazılır.

| # | Sarılan birim | Başlangıç | Bitiş | Çıktı |
|---|---|---|---|---|
| 1 | Tek middleware | `hrtime(true)` | `hrtime(true)` farkı | `timing()` |
| 2 | Tüm pipeline | yok | yok | ⚠️ ölçülmedi |
| 3 | Route çözümleme | yok | yok | ⚠️ ölçülmedi |
| 4 | Handler çalıştırma | yok | yok | ⚠️ ölçülmedi |
| 5 | DB sorgusu | yok | yok | ⚠️ ölçülmedi |

## §6 Örnekleme (sampling)

Örnekleme, hangi izlerin kaydedileceğine karar verir. Yanlış seçim ya veri kaybına ya da maliyet
patlamasına yol açar.

| # | Yöntem | Ne yapar | Artı | Eksi |
|---|---|---|---|---|
| 1 | Hepsi (always-on) | Her iz kaydedilir | Tam veri | Maliyet |
| 2 | Oransal (probabilistic) | Rastgele oran | Basit | Hataları kaçırabilir |
| 3 | Kural tabanlı | Koşula göre | Hataları yakalar | Kurallar değişir |
| 4 | Kök tabanlı | Üst iz karar verir | Tutarlı | Yayılım gerektirir |
| 5 | Sayım tabanlı | Dakika başına sınırlı | Bütçe güvencesi | Gecikme |
| 6 | Hata-ağırlıklı | Hata izlerini tutar | Olay yakalama | Yanlış sinyal |

**Bu proje için durum:** ⚠️ yöntem seçilmedi. §10.3'te açık soru olarak taşınmaktadır.

### §6.1 Örnekleme karar tablosu

| # | Durum | Önerilen davranış | Gerekçe |
|---|---|---|---|
| 1 | 5xx yanıtlar | her zaman kaydet | Olay müdahalesi |
| 2 | 429 (sınır aşımı) | kural tabanlı | Kaba kuvvet izi |
| 3 | 2xx yanıtlar | oransal | Hacim kontrolü |
| 4 | Hata yolu (istisna) | her zaman kaydet | Kök neden analizi |
| 5 | Ses zinciri olayı | ayrı kanal | Gerçek zamanlı kısıt |
| 6 | Geliştirme ortamı | hepsi | Tanılama |

### §6.2 Örnekleme uygulanabilirlik zinciri

| # | Ön koşul | Mevcut mu | Kanıt | Örnekleme etkisi |
|---|---|---|---|---|
| 1 | Üretilen iz | evet | §4 | Kaynak var |
| 2 | İz başına karar noktası | hayır | Sarma yalnız süre loglar (§8 satır 6) | Uygulanamaz |
| 3 | Toplayıcı | hayır | §8 satır 10 | Kaydedecek yer yok |
| 4 | Bayrak alanı (`flags`) | hayır | §3 satır 8 | Karar taşınamaz |
| 5 | Yayılım | hayır | §10.2 | Kök kararı dağıtılamaz |
| 6 | Oran kararı | hayır | §1 H6 | Üst karar bekliyor |

**Zincir sonucu:** örnekleme, toplayıcı seçimi ve oran kararı **birlikte** verilir; tek başına
oran seçimi uygulanamaz (§10.3 satır 1).

## §7 Toplayıcı ve aktarım

| # | Seçenek | Ne sağlar | Uygunluk | Durum |
|---|---|---|---|---|
| 1 | Standart dosya çıktısı | Basit, bağımlılıksız | Prototip | Mevcut davranış |
| 2 | HTTP/METRICS benzeri uç | Toplayıcıya gönderim | Üretim | Seçilmedi |
| 3 | Binerecek biçim | Düşük başlık maliyeti | Yüksek hacim | Seçilmedi |
| 4 | Kuyruk üzerinden | Dayanıklılık | Yüksek hacim | Seçilmedi |

**Seçim kriterleri:**

| # | Kriter | Ağırlık | Not |
|---|---|---|---|
| 1 | Sıfır tahsis zorunluluğu (ses yolu) | yüksek | L0 kuralı |
| 2 | Başlatma süresi | orta | Uygulama açılışı |
| 3 | Bağımlılık sayısı | orta | Sürüm riski |
| 4 | Kaynak tüketimi | orta | Bellek/CPU |
| 5 | Sürüm kanıtı | yüksek | §12 kaynakları |

## §8 Mevcut durum envanteri

| # | Yetenek | Durum | Kanıt | Eksik parça |
|---|---|---|---|---|
| 1 | İz kimliği üretimi | ✅ mevcut | `generateTraceId()` | — |
| 2 | Loga kimlik yazma | ✅ mevcut | `setTraceId()` | — |
| 3 | Yanıt başlığı | ✅ mevcut | `X-Trace-Id` | — |
| 4 | Hata gövdesine kimlik | ✅ mevcut | `ErrorResponse` | — |
| 5 | FATAL satırına kimlik | ✅ mevcut | satır 135 | — |
| 6 | Süre ölçümü (sarılı) | ✅ mevcut | `wrap()` | Yalnız log |
| 7 | Span üretimi | ❌ yok | — | Span API |
| 8 | Üst-bağlam yayılımı | ❌ yok | — | `traceparent` |
| 9 | Örnekleme | ❌ yok | — | Karar |
| 10 | Toplayıcı | ❌ yok | — | Seçim + kurulum |
| 11 | Görselleştirme | ❌ yok | — | Arayüz |
| 12 | Kimlik köprüsü (iki üretici) | ❌ yok | — | Tekilleştirme |

## §9 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor | Doğrulama |
|---|---|---|---|---|
| 1 | `shared/src/PageRouter/PageRouterKernel.php` | 87 | `generateTraceId()` çağrısı | Numaralı döküm |
| 2 | `shared/src/PageRouter/PageRouterKernel.php` | 88 | Yapılandırılmış logger'a kimlik | Numaralı döküm |
| 3 | `shared/src/PageRouter/PageRouterKernel.php` | 91 | Dosya loguna aynı kimlik | Numaralı döküm |
| 4 | `shared/src/PageRouter/PageRouterKernel.php` | 135 | FATAL satırında kimlik | `Select-String` |
| 5 | `shared/src/PageRouter/PageRouterKernel.php` | 317 | `generateTraceId()` tanımı | Numaralı döküm |
| 6 | `shared/src/PageRouter/PageRouterKernel.php` | 319 | `random_bytes(16)` entropy | Numaralı döküm |
| 7 | `shared/src/PageRouter/PageRouterKernel.php` | 322 | UUID biçimi `vsprintf` | Numaralı döküm |
| 8 | `shared/src/PageRouter/ResponseEmitter.php` | 94 | `emitTraceAndExtras()` tanımı | `Select-String` |
| 9 | `shared/src/PageRouter/ResponseEmitter.php` | 97 | `X-Trace-Id` yanıt başlığı | `Select-String` |
| 10 | `shared/src/PageRouter/StructuredLogger.php` | 11 | `traceId` alanı | Numaralı döküm |
| 11 | `shared/src/PageRouter/StructuredLogger.php` | 66 | Kayıtta `traceId` anahtarı | Numaralı döküm |
| 12 | `shared/src/Api/Gateway.php` | 88 | İkinci kimlik üretici (`UuidV7`) | Numaralı döküm |
| 13 | `shared/src/Api/Gateway.php` | 91 | `trace_id` alan adı (alt çizgi) | Numaralı döküm |
| 14 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | Sarmalayıcı başlangıcı | Numaralı döküm |
| 15 | `shared/src/Middleware/MiddlewarePipeline.php` | 48 | Süre yayını | Numaralı döküm |
| 16 | `shared/src/Log/FileHandler.php` | 37 | Statik `traceId` alanı | `Select-String` |
| 17 | `shared/src/Exception/ErrorResponse.php` | 7 | `traceId` parametresi | `Select-String` |

**Doğrulama komutu:**

```powershell
Get-Content 'C:\www\coremusic.net\shared\src\PageRouter\PageRouterKernel.php' -Encoding UTF8 |
  Select-Object -Index 316
```

Bu komut 1-based satır 317'yi, yani `private static function generateTraceId(): string` satırını
döndürür.

## §10 Bulgular

### §10.1 Çift kimlik üretici

| # | Bulgu | Etki | Öneri |
|---|---|---|---|
| 1 | PageRouter v4 biçimli kimlik üretir | Sayfa izleri | Ana üretici olarak atanır |
| 2 | Gateway yalnız hata yolunda v7 üretir | Hata izleri farklı biçimde | Tek üreticiye taşınır |
| 3 | Alan adları farklı (`traceId` vs `trace_id`) | Sorgu zorlaşır | Alan adı tekilleştirilir |
| 4 | Köprü yok | Tek istekte iki kimlik | Tek noktaya indirgenir |

### §10.2 Yayılım eksikleri

| # | Eksik | Sonuç | Öncelik |
|---|---|---|---|
| 1 | Gelen başlık okunmuyor | Dış istemcinin izi kaybolur | yüksek |
| 2 | `traceparent` yok | Standart araçlar çalışmaz | orta |
| 3 | Servisler arası başlık yok | Uçtan uca zincir kopar | orta |
| 4 | Span yok | Yalnız toplam süre görünür | orta |

### §10.3 Açık sorular

| # | Soru | Neden önemli | Durum |
|---|---|---|---|
| 1 | Örnekleme yöntemi nedir? | Maliyet/veri dengesi | ⚠️ VERIFICATION REQUIRED |
| 2 | Toplayıcı hangisidir? | Kurulum ve sürüm | ⚠️ VERIFICATION REQUIRED |
| 3 | Kimlik ne kadar saklanır? | Gizlilik | ⚠️ VERIFICATION REQUIRED |
| 4 | Ses zinciri olayları izlenecek mi? | K015 sınırı | ⚠️ VERIFICATION REQUIRED |
| 5 | İki kimlik biçimi istemciye nasıl yansıyor? | Destek deneyimi | ⚠️ VERIFICATION REQUIRED |

## §11 Entegrasyon

| # | Yön | Muhatap | Arayüz | Not |
|---|---|---|---|---|
| 1 | Giriş | `PageRouterKernel` | `generateTraceId()` | Tek nokta |
| 2 | Çıkış | `StructuredLogger` | `traceId` alanı | Log köprüsü |
| 3 | Çıkış | `FileHandler` | `setTraceId()` | Dosya logu köprüsü |
| 4 | Çıkış | `ResponseEmitter` | `X-Trace-Id` | İstemciye iade |
| 5 | Çıkış | `ErrorResponse` | `trace_id` | Hata gövdesi |
| 6 | Dikey | `logging/` | Aynı alan | Tutarlılık |
| 7 | Dikey | `correlation/` | Kimlik politikası | Sınır |
| 8 | Dikey | `metrics/` | `route_pattern` köprüsü | Etiket |
| 9 | Dikey | K018-devops | Toplayıcı kurulumu | Ortam |

## §12 Uçtan uca iz şeması

Şema, mevcut durumu gösterir. Kesikli iş akışları henüz **yoktur** ve bu dosyada yok olarak
işaretlenir.

| # | Aşama | Kimlik kaynağı | Kimlik nereye yazılır | Durum |
|---|---|---|---|---|
| 1 | İstek kabulü | `generateTraceId()` | Değişken | ✅ |
| 2 | Yapılandırılmış log | Argüman | `traceId` alanı | ✅ |
| 3 | Dosya logu | Statik alan | `[trace=…]` | ✅ |
| 4 | Ara katman ölçümü | — | Yalnız süre | kısmi |
| 5 | Route çözümleme | — | — | ❌ |
| 6 | Handler çalıştırma | — | — | ❌ |
| 7 | Hata yakalama (Gateway) | Yeni kimlik | `trace_id` alanı | ⚠️ ikinci kimlik |
| 8 | Hata gövdesi | Fonksiyon argümanı | `trace_id` alanı | ✅ |
| 9 | Yanıt başlığı | Fonksiyon argümanı | `X-Trace-Id` | ✅ |
| 10 | Uzak servis | — | — | ❌ |

### §12.1 Durum özeti

| # | Durum | Aşama sayısı | Yüzde | Yorum |
|---|---|---|---|---|
| 1 | ✅ tam | 6 | %60 | Çekirdek yol |
| 2 | ⚠️ kısmi | 2 | %20 | Ölçüm ve hata |
| 3 | ❌ yok | 4 | %40 | Zincir kopuklukları |
| 4 | Toplam | 10 | — | Şemanın tamamı |

### §12.2 Eksik aşama ayrıntısı

| # | Eksik aşama | Ne kaybedilir | Nerede eklenir |
|---|---|---|---|
| 1 | Route çözümleme | Eşleşmeme süreleri | `RouteTable::match()` çevresi |
| 2 | Handler çalıştırma | İş mantığı süresi | `invokeHandler()` |
| 3 | Servisler arası | Uçtan uca zincir | HTTP istemcisi |
| 4 | Uzak servis yanıtı | Karşı servis süresi | İstek katmanı |

## §13 Kimlik yaşam döngüsü

| # | Aşama | Zaman | Sahip | Eylem |
|---|---|---|---|---|
| 1 | Üretim | İstek başlangıcı | `PageRouterKernel` | `random_bytes(16)` |
| 2 | Biçimleme | Hemen ardından | `generateTraceId()` | `vsprintf` |
| 3 | Dağıtım | İstek işlenmeden önce | Çekirdek | iki `setTraceId()` |
| 4 | Kullanım | İş boyunca | Log yazıcıları | `traceId` alanı |
| 5 | Yanıt | Gönderim | `ResponseEmitter` | `X-Trace-Id` başlığı |
| 6 | Saklama | Log dosyası | Dosya sistemi | Log satırı |
| 7 | Son | İstek bitimi | Statik alan | Bellekten düşer |
| 8 | Silme | — | — | Statik alan sıfırlanmaz |

### §13.1 Statik alan riski

| # | Risk | Açıklama | Etki | Öneri |
|---|---|---|---|---|
| 1 | İş parçacığı paylaşımı | `FileHandler::$traceId` statiktir | Yanlış korelasyon | İstek başına temizlik |
| 2 | Sıfırlama yok | `clearTraceId()` yalnız testte çağrılıyor | Sızıntı | İstek sonunda temizlik |
| 3 | Sunucu modeli | Paylaşılan uzun süreç | Yüksek | Ölçüm yapılmalı |
| 4 | Test kapsamı | `Psr3LoggerTest` setUp/tearDown temizliyor | Kısmi güvence | Üretim davranışına yayılmalı |

### §13.2 Biçim karşılaştırması

| # | Özellik | PageRouter kimliği | Gateway kimliği |
|---|---|---|---|
| 1 | Üretici | `random_bytes(16)` | `UuidV7::generate()` |
| 2 | Sürüm | 4 | 7 |
| 3 | Sıralanabilirlik | hayır | evet (zaman) |
| 4 | Tetikleyici | her istek | yalnız hata yolu |
| 5 | Alan adı | `traceId` | `trace_id` |
| 6 | Biçim | 8-4-4-4-12 | ⚠️ v7 biçimi doğrulanmadı |
| 7 | Başlık | `X-Trace-Id` | başlık yok |

## §14 Hata yolu izleme senaryoları

| # | Senaryo | Kimlik üretilir mi | Nerede görünür | Eksik |
|---|---|---|---|---|
| 1 | Sayfa yönlendirmesi | evet | log + yanıt başlığı | — |
| 2 | API Gateway yakalama | evet (yeni) | log kaydı | Başka kimlikle köprü |
| 3 | FATAL yakalama | evet | `error_log` satırı | — |
| 4 | Hata gövdesi | argüman | JSON gövdesi | — |
| 5 | Sınır aşımı (429) | evet | güvenlik olayı kaydı | Başlık yok |
| 6 | Yönlendirme (redirect) | evet | başlık | — |
| 7 | HTML yanıtı | evet | başlık | — |
| 8 | Ses zinciri olayı | yok | — | ayrı kanal |

### §14.1 Hata gövdesi kuralı

| # | Kural | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | İstisna detayı istemciye dönmez | OWASP API3 | `Gateway.php` satır 99 |
| 2 | Yer tutucu mesaj döner | Tutarlılık | `INTERNAL_ERROR` |
| 3 | Trace kimliği dönerse destek bağlanabilir | Destek deneyimi | `ErrorResponse` |
| 4 | Kimlik maskelenmez | Köprü işlevi | `Redactor` kuralı |

## §15 Mevcut – hedef karşılaştırması

| # | Boyut | Mevcut | Hedef | Fark |
|---|---|---|---|---|
| 1 | Kimlik üretici | 2 | 1 | Tekilleştirme |
| 2 | Alan adı | 2 biçim | 1 biçim | Standartlaşma |
| 3 | Yayılım başlığı | yanıt | istek + yanıt | `traceparent` |
| 4 | Süre ölçümü | log | toplayıcı | Köprü |
| 5 | Örnekleme | yok | kararlı | Politika |
| 6 | Toplayıcı | yok | seçili | Kurulum |
| 7 | Görselleştirme | yok | mevcut | Arayüz |
| 8 | Saklama | log dosyası | tanımlı | Politika |

## §16 Örnek kayıtlar

Aşağıdaki örnekler biçimi gösterir; değerler temsilidir.

| # | Örnek | Kanal | Ne gösterir |
|---|---|---|---|
| 1 | `[2026-10-12T21:10:00+03:00] [INFO] [GET /] [127.0.0.1] [trace=4a7f…] route_match` | dosya logu | Dosya logunda kimlik |
| 2 | `{"timestamp":"…","level":"info","module":"PageRouter","event":"middleware_timing","traceId":"4a7f…"}` | JSON kayıt | Yapılandırılmış kayıtta kimlik |
| 3 | `X-Trace-Id: 4a7f…` | yanıt başlığı | İstemciye kimlik iadesi |
| 4 | `{"error":"INTERNAL_ERROR","trace_id":"0193…"}` | hata gövdesi | Gateway kimliği |
| 5 | `[PageRouterKernel] FATAL traceId=4a7f… message` | hata akışı | FATAL satırı |

**Örnek sorgu (destek senaryosu):** istemci `X-Trace-Id` başlığını iletirse, destek kaydı bu değeri
aramayla bulabilir. Başlık ile hata gövdesindeki kimlik **farklıysa** bu sorgu sonuçsuz kalır — bu,
§10.1 bulgusunun kullanıcı etkisidir.

## §17 Kabul kriterleri ve test planı

Bu modül kabul edilirken aşağıdaki kriterlerin **kanıtı** istenir; yalnızca kural yazmak yeterli
değildir.

| # | Kriter | Kanıt türü | Kabul değeri | Durum |
|---|---|---|---|---|
| 1 | Her istekte kimlik üretiliyor mu? | log örneği | kesintisiz | kısmen doğrulandı |
| 2 | Log kaydı ile JSON kaydı eşleşiyor mu? | iki kayıt karşılaştırması | birebir | kod ile destekli |
| 3 | Yanıt başlığı yazılıyor mu? | HTTP yanıtı | varsa yazılır | kod ile destekli |
| 4 | Hata gövdesinde kimlik var mı? | JSON gövdesi | var | kod ile destekli |
| 5 | FATAL satırında kimlik var mı? | hata akışı | var | kod ile destekli |
| 6 | Tek kimlik üretici mi? | kod taraması | 1 | ⚠️ 2 bulundu |
| 7 | Statik alan istek sonunda temizleniyor mu? | kod incelemesi | evet | ⚠️ doğrulanmadı |
| 8 | Alan adı tek mi? | kod taraması | 1 biçim | ⚠️ 2 biçim |
| 9 | Yayılım başlığı var mı? | HTTP başlığı | `traceparent` | ❌ yok |
| 10 | Örnekleme kararı var mı? | karar belgesi | var | ❌ yok |
| 11 | Toplayıcı çalışıyor mu? | servis durumu | çalışıyor | ❌ yok |
| 12 | Ses yolu etkileniyor mu? | ölçüm | 0 etki | ⚠️ ölçülmedi |

### §17.1 Test senaryoları (taslak)

| # | Senaryo | Girdi | Beklenen | Katman |
|---|---|---|---|---|
| 1 | Kimlik biçim kontrolü | `generateTraceId()` | 8-4-4-4-12 | birim |
| 2 | Kimlik tekilliği | 1000 çağrı | 1000 farklı değer | birim |
| 3 | Kimlik maskelenmesi | `Redactor` | UUID korunur | birim |
| 4 | Log-kimlik eşleşmesi | tek istek | iki kayıtta aynı | birleşik |
| 5 | Başlık yazımı | yanıt | `X-Trace-Id` var | birleşik |
| 6 | Hata gövdesi | istisna | `trace_id` alanı | birleşik |
| 7 | Statik alan temizliği | iki ardışık istek | kimlikler farklı | birleşik |
| 8 | Eşzamanlılık | iki istek | korelasyon karışmaz | yük |
| 9 | Uzun çalışma | 1 saat | alan taşması yok | yük |
| 10 | Yayılım | dış başlık | kimlik korunur | uçtan uca |

## §18 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| OpenTelemetry iz (trace), bir iş talebinin yolunu temsil eden span ağacı olarak tanımlanır | https://opentelemetry.io/docs/concepts/signals/traces/ | 2026-10-12 | HTTP 200 doğrulandı |
| OpenTelemetry bağlam yayılımı, üst-bağlam bilgisinin servisler arasında başlıkla taşınmasını tanımlar | https://opentelemetry.io/docs/concepts/context-propagation/ | 2026-10-12 | HTTP 200 doğrulandı |
| OpenTelemetry örnekleme kavramı, hangi izlerin kaydedileceğini belirleyen politikaları tanımlar | https://opentelemetry.io/docs/concepts/sampling/ | 2026-10-12 | HTTP 200 doğrulandı |
| OpenTelemetry veri kaynakları, iz/metrik/günlük sinyallerinin taşındığı taşıyıcıları listeler | https://opentelemetry.io/docs/concepts/data-sources/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Bu dört URL `.ai/architecture/**` envanterinde bulunmaz; ölçüm envanter ölçümünden sonra
yapılmıştır.

## §19 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1081 benzersiz URL | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest -Method Head` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | anahtar karşılaştırma | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | `Select-String` + numaralı döküm | 17 satır eşleşti | 2026-10-12 |
| 5 | Çift kimlik üretici | çoklu desen araması | 2 üretici bulundu | 2026-10-12 |
| 6 | Satır sayısı | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | metin taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | gözden geçirme | 0 | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
