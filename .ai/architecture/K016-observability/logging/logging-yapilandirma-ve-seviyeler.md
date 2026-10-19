---
title: "K016-observability/logging — PSR-3 Loglama Yapılandırması ve Seviye Hiyerarşisi"
node_id: K016-observability/logging/logging-yapilandirma-ve-seviyeler
node_type: module
status: draft
parent_id: K016-observability/logging
children: []
depends_on:
  - K016-observability
  - K016-observability/logging
evidence:
  - "shared/src/Log/FileHandler.php — ReadAllLines 214 satır, LEVELS sabiti satır 40 (2026-10-12)"
  - "shared/src/Log/Redactor.php — ReadAllLines 106 satır, KEY_VALUE_PATTERN satır 40 (2026-10-12)"
  - "shared/src/PageRouter/StructuredLogger.php — ReadAllLines 64 satır, LEVELS satır 9 (2026-10-12)"
  - "shared/composer.json — satır 19: psr/log ^3.0 (2026-10-12)"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php — ReadAllLines 163 satır (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri ölçümü: .ai/architecture/** genelinde 1081 benzersiz URL, hiçbir kopyalanmadı (2026-10-12)"
open_questions:
  - "FileHandler'ın warning/ailesi ayrımının PSR-3 compliance testinde kapsamı doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
  - "StructuredLogger ile FileHandler arasında seviye adı eşlemesi (warn vs warning) standardize edilmedi (⚠️ VERIFICATION REQUIRED)"
  - "prod ortamında minLevel'ın hangi env değişkeninden okunduğu repo'da tespit edilmedi (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-observability/logging/logging-yapilandirma-ve-seviyeler

> **ELI10:** Loglama, sistemin söylediklerini yazıp söylemediğini susturma işidir. Bu dosya üç soruyu
> cevaplar: hangi ciddiyet seviyesi ne zaman kullanılır, bir log satırında hangi alanlar bulunur ve
> hangi değerler yazılmadan önce maskelenir. Karar tek noktada verilir; her sınıf kendi loglama
> politikasını uyduramaz.

## §1 Amaç

Bu modül, CoreMusic'in loglama yapılandırmasının **tek yetkili tanımıdır**. Loglama üç katmanlıdır:
(seviye politikası) · bağlam (context alanı) · redaction (maskeleme). Üçü birbirinden ayrılmaz;
yalnız seviye tanımlayıp redaction'ı bırakmak, KVKK/OWASP açısından açık üretir.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Bir olayın ciddiyeti nedir? | Seviye hiyerarşisi (level hierarchy) | `FileHandler::LEVELS` sabiti |
| 2 | Hangi seviyeden aşağısı yazılır? | `minLevel` eşiği (threshold) | `FileHandler::__construct` |
| 3 | Satırda hangi alanlar zorunlu? | Context şeması (context schema) | `StructuredLogger::log()` |
| 4 | Hassas değer nasıl gizlenir? | Redaction (masking) | `Redactor::redact()` |
| 5 | İstekler nasıl birbirine bağlanır? | Correlation (traceId) | `FileHandler::setTraceId()` |
| 6 | Ayrık dosyalara nasıl bölünür? | Stream split (info vs warning) | `FileHandler::write()` |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Tek maskeleme noktası | sayısı | 1 (`Redactor`) | kodda mevcut, test ile kanıtlanmış |
| H2 | PSR-3 sekiz zorunlu seviye adı | tam liste | 8/8 | `FileHandler` kodunda mevcut |
| H3 | Log satırında zorunlu alan | sayısı | 6 (timestamp, level, method+uri, ip, trace, message) | format satırında mevcut |
| H4 | Redaction false-positive oranı | oran | UUID korunur, hex hash maskelenir | `Redactor` yorum satırında tanımlı |
| H5 | Log yazımında tahsis (allocation) | istek başına | loglama düzeyinde ölçülmüş değer yok | ⚠️ VERIFICATION REQUIRED |
| H6 | Log dosyası günlük rotasyonu | mekanizma | repo'da tespit edilmedi | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Seviye adları ve sırası | ✅ PSR-3 sekiz seviye + eşik kuralı | ❌ Uyarı kanalları (Slack, e-posta) → `alerts/` |
| Context alanları | ✅ Zorunlu altı alan + opsiyonel alanlar | ❌ Metrik üretimi → `metrics/` |
| Redaction kuralları | ✅ Anahtar adı, hash, e-posta maskeleri | ❌ Sızıntı taraması (secret scan) → K17-devops/secrets |
| Correlation | ✅ traceId'nin log satırına yazımı | ❌ Uçtan uca span ağacı → `tracing/` |
| Dosya düzeni | ✅ info/warning ayrımı, format satırı | ❌ Saklama süresi (retention) → K17-devops |
| Test kapsamı | ✅ Mevcut testlerin neyi kanıtladığı | ❌ Yeni test yazımı → K18-testing |
| PSR-3 arayüzü | ✅ `LoggerInterface` uygulaması | ❌ Olay yayımı (event dispatch) → `Events/` |

**Komşu domainlerle sınır:**

| Komşu | Geçiş noktası | Bu dosyanın rolü |
|---|---|---|
| K17-devops | CI log çıktısı | Aynı seviye adları kullanılır; içerik bu dosyada tanımlı |
| K18-testing | Testlerde log assertion | `Psr3LoggerTest` bu dosyanın kurallarını doğrular |
| K009-security | Hassas veri tespiti | Redaction anahtar listesi buradan beslenir |
| K016-alerting | Uyarı üreten loglar | `warning` ailesi uyarı adaydır |
| K016-metrics | Log tabanlı sayaçlar | Log hacmi metrik olarak ayrıca üretilir |
| K016-correlation | İstek kimliği | `traceId` alanı birebir aynı değeri taşır |
| K016-health-checks | Sağlık uçlarının logu | `info` seviyesi kullanılır |
| K016-incident-response | Olay kayıtları | `critical` ve üzeri olaylar müdahale girdisidir |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılık | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Seviye | level | Olayın ciddiyet basamağı | `debug` … `emergency` |
| Eşik | threshold | Yazılacak en düşük seviye | `minLevel` |
| Bağlam | context | Mesaja eklenen anahtar-değer çifti | `$context` dizisi |
| Maskeleme | redaction | Hassas değerin yerine koyucu yazılması | `[REDACTED]` |
| Korelasyon | correlation | İsteğe bağlı tekil kimlik | `traceId` |
| Akış ayrımı | stream split | Seviyeye göre farklı dosyaya yazma | `info` / `error` dosyaları |
| Uyarı ailesi | warning family | Uyarı üreten seviye kümesi | `WARNING_FAMILY` |
| PSR-3 | PSR-3 | PHP fig loglama arayüzü standardı | `Psr\Log\LoggerInterface` |
| Sözleşme | contract | Arayüzün bağlayıcı davranışı | sekiz zorunlu metot |
| Kanal | channel | Ayrık log hedefi | `securityStream` |
| Yapılandırma | configuration | Eşiğin kaynağı | constructor + sabitler |
| Maskelenemez değer | unmaskable | Desene uymayan hassas veri | ⚠️ bilinen boşluk |

## §4 PSR-3 seviye hiyerarşisi

### §4.1 Kanonik sekiz seviye

PSR-3, `debug` ile `emergency` arasında sekiz seviye tanımlar ve sıranın önem artışı olduğunu
belirtir. CoreMusic bu adları `FileHandler` içinde birebir korur.

| # | Seviye adı | Önem sırası | Ne zaman kullanılır | Repo karşılığı | Dosya |
|---|---|---|---|---|---|
| 1 | `debug` | 0 | Geliştirici ayıklama izi, üretimde kapalı | `LEVELS['debug']` | `FileHandler.php` |
| 2 | `info` | 1 | Normal iş akışı olayı, istek başarıları | `LEVELS['info']` | `FileHandler.php` |
| 3 | `notice` | 2 | Önemli ama olağan durum, konfigürasyon değişimi | `LEVELS['notice']` | `FileHandler.php` |
| 4 | `warning` | 3 | Olağandışı ama devam edebilen durum | `LEVELS['warning']` | `FileHandler.php` |
| 5 | `error` | 4 | Tekil hata, istek başarısız oldu | `LEVELS['error']` | `FileHandler.php` |
| 6 | `critical` | 5 | Hizmetin çekirdek işi aksadı | `LEVELS['critical']` | `FileHandler.php` |
| 7 | `alert` | 6 | Sistem kullanılabilir değil, anında müdahale | `LEVELS['alert']` | `FileHandler.php` |
| 8 | `emergency` | 7 | Sistem tamamen kullanılamaz | `LEVELS['emergency']` | `FileHandler.php` |

### §4.2 Önem karşılaştırması (eşik kuralı)

Eşik kuralı basittir: aday seviyenin sayısal sırası, `minLevel` sırasına **eşit veya büyükse** satır
yazılır. Eşitlikte yazılır; bu, "en az bu ciddiyette" okumasıdır.

| minLevel | Yazılan seviyeler | Yazılmayan seviyeler | Tipik kullanım |
|---|---|---|---|
| `debug` | 0-7 (sekizi de) | yok | Yerel geliştirme, ünite testi |
| `info` | 1-7 | `debug` | Üretim varsayılanı |
| `notice` | 2-7 | `debug`, `info` | Yüksek trafikli üretim |
| `warning` | 3-7 | `debug`, `info`, `notice` | Yalnız uyarı günlüğü |
| `error` | 4-7 | 0-3 | Yalnız hata günlüğü |
| `critical` | 5-7 | 0-4 | Acil durum izleme |
| `alert` | 6-7 | 0-5 | ⚠️ önerilmez, nadir |
| `emergency` | 7 | 0-6 | ⚠️ önerilmez, nadir |

### §4.3 Uyarı ailesi ve dosya ayrımı

Uyarı ailesi, seviye hiyerarşisinden türetilen ikinci bir kümeydir. Amaç, hata satırının info
kalabalığında kaybolmamasıdır.

| Küme | Seviyeler | Hedef dosya | Ayrım mantığı |
|---|---|---|---|
| Uyarı ailesi | `warning`, `error`, `critical`, `alert`, `emergency` | `warnings` hattı | Önem sırası ≥ 3 |
| Normal aile | `debug`, `info`, `notice` | `info` hattı | Önem sırası ≤ 2 |
| Güvenlik kanalı | Olay tipine göre seçilen seviye | `securityStream` | `authEvent` / `securityEvent` |
| Hata akışı | `error` ve üzeri | `errors` hattı | PHP hata yakalama |
| Geliştirme | `debug` | Aynı hat, ayrı mod | `debugMode` true iken |

`FileHandler`, uyarı ailesini sabit olarak tutar: `WARNING_FAMILY` listesi beş seviyeyi içerir ve
`logAt()` içinde bu listede olup olmadığına bakılarak hedef dosya `error` mı `info` mı seçilir.
Seviye adı değişirse küme de değişmelidir — bu, bu dosyanın bağlayıcı kuralıdır.

### §4.4 Yapılandırma kaynakları

| Parametre | Kaynak | Varsayılan | Değiştirme yolu | Repo kanıtı |
|---|---|---|---|---|
| `logDir` | Çağrıncı sınıf | `resolveLogDir()` | `LoggerFactory::getInstance()` | `LoggerFactory.php` satır 15 |
| `minLevel` | Çağrıncı sınıf | `info` | constructor ikinci argüman | `FileHandler.php` satır 54 |
| `debugMode` | Ortam sabiti | `DEBUG_MODE` sabiti | Sabit tanımı | `FileHandler.php` satır 60 |
| `traceId` | İstek katmanı | boş dize | `setTraceId()` / `clearTraceId()` | `FileHandler.php` satır 65-73 |
| `minLevel` (yapılandırılmış) | StructuredLogger | `info` | constructor | `StructuredLogger.php` satır 14 |

## §5 Yapılandırma kuralları

| # | Kural | Gerekçe | Uygulayıcı | İhlâl sonucu |
|---|---|---|---|---|
| R1 | Seviye adı PSR-3 sekizlisinden biri olmak zorundadır | Uyumsuz ad, eşiği sessizce atlar | `FileHandler::log()` | `isset` kontrolünde reddedilir |
| R2 | Seviye adı büyük harfle yazılmaz | `strtolower()` ile normalize edilir | `log()` gövdesi | Yine de doğru çözümlenir |
| R3 | Varsayılan `minLevel` `info`'dur | Üretimde `debug` gürültü üretir | constructor varsayılanı | Gürültü |
| R4 | `debug` seviyesi üretimde yazılmaz | Performans ve gizlilik | eşiğe bağlı | Kanıt boşluğu |
| R5 | Mesaj tek satır olmalıdır | Satır-tabanlı ayrıştırma bozulur | yazan kod | Kayıt okunmaz |
| R6 | Context, düz dize yerine dizi verilir | PSR-3 değiştirilebilir context kuralı | `log($level, $msg, $ctx)` | Değiştirme yapılmaz |
| R7 | Hassas anahtar `Redactor`'dan geçmeden yazılmaz | Sızıntı önleme | tüm yazma yolları | `[REDACTED]` uygulanmaz |
| R8 | Log satırında `traceId` varsa boş bırakılmaz | İstek korelasyonu | `setTraceId()` | İstek izi kopar |
| R9 | Loglama `error_log()` üzerine yazılır | Paylaşılan barındırma uyumu | `StructuredLogger::log()` | Standart akışa düşer |
| R10 | Uyarı ailesi ayrı dosyaya yazılır | Alarm hızlandırması | `logAt()` | Hata kaybolur |
| R11 | Güvenlik olayları ayrı kanala yazılır | Denetim izi | `authEvent` / `securityEvent` | Denetim izi kaybı |
| R12 | Yeni seviye adı eklenemez | Eşik tablosu ve testler buna bağlı | herkes | Sözleşme kırılır |
| R13 | Context anahtar adı `snake_case` olmalıdır | Uyumluluk | yazan kod | Sorgu kırılır |
| R14 | Maskelenen değer geri alınamaz | Tasarım gereği | `Redactor` | — |
| R15 | `StructuredLogger` yalnız dört seviye adı bilir (`debug`, `info`, `warn`, `error`) | İki sınıfta seviye kümesi farklı | `StructuredLogger::LEVELS` | Sekiz seviyeye erişilemez |
| R16 | Güvenlik olayları `securityEvent()` üzerinden yazılır | Denetim izinin ayrıştırılabilmesi | `FileHandler` | Ayrık akışa gitmez |
| R17 | Kimlik olayları `authEvent()` üzerinden yazılır | Olay tipine göre seviye seçilir | `FileHandler` | Seviye elle seçilmek zorunda kalır |
| R18 | Hata hattı adı sabittir (`coremusic_php_errors.log`) | Testler bu adı dosya sisteminde arar | `Psr3LoggerTest` | Testler okuyamaz |
| R19 | `securityStream` bayrağı yalnız iki metottan set edilir | Yetkisiz kanal açılmasın | `authEvent`, `securityEvent` | Güvenlik kaydı normal hatta düşer |
| R20 | Seviye karşılaştırması sayısal yapılır | Metinsel karşılaştırma ASCII sırasına güvenir | `shouldLog()` | Eşik yanlış çalışır |

### §5.1 Yasaklar

| # | Yasak | Neden | Karşılığı |
|---|---|---|---|
| Y1 | Sıradan metin olarak ham token yazmak | Sızıntı | `Redactor` maskesi |
| Y2 | Parola / API anahtarı / çerezi context'e koymak | Sızıntı | `[REDACTED]` |
| Y3 | Log içinde kaynak kod gömmek | Gizlilik + boyut | Tanımlayıcı ad yeterli |
| Y4 | Kayıt için `sleep` / `usleep` çağırmak | Gecikme | Yok |
| Y5 | Log yazımında ağ isteği yapmak | Blokaj | Yerel dosya |
| Y6 | Seviye yerine tam metin arama ile filtrelemek | Yanlış eşleşme | Sayısal eşik |
| Y7 | TraceId'yi istek dışında yeniden kullanmak | Yanlış korelasyon | Her istekte yeni kimlik |
| Y8 | Maskelenmiş satırı sonra geri açmaya çalışmak | Kayıt bozulur | Yok |

## §6 Context ve redaction

### §6.1 Zorunlu context alanları

`StructuredLogger::log()` çağrısında her kayıt şu alanlarla başlar; fazladan alanlar `array_merge`
ile eklenir.

| # | Alan | Tip | Kaynak | Zorunlu | Örnek |
|---|---|---|---|---|---|
| 1 | `timestamp` | ISO 8601 dize | `DateTimeImmutable` | evet | `2026-10-12T21:04:00+03:00` |
| 2 | `level` | dize | çağrı argümanı | evet | `warning` |
| 3 | `module` | dize | çağrı argümanı | evet | `PageRouter` |
| 4 | `event` | dize | çağrı argümanı | evet | `middleware_timing` |
| 5 | `traceId` | dize | `setTraceId()` | evet (boş olabilir) | `4f3a…` |
| 6 | `file` | dize | `write()` formatı | evsatır bazında | `coremusic_php_errors.log` |
| 7 | `method` | dize | HTTP isteği | evsatır bazında | `GET` |
| 8 | `uri` | dize | HTTP isteği | evsatır bazında | `/api/v1/auth/me` |
| 9 | `ip` | dize | `REMOTE_ADDR` | evsatır bazında | `203.0.113.7` |

### §6.2 Redaction maskeleri

`Redactor`, tek noktada üç maske uygular. Sıra önemlidir: önce anahtar-adı, sonra uzun hash, sonra
e-posta.

| # | Desen | Kapsam | Maskelenen kısım | Korunan kısım | Repo karşılığı |
|---|---|---|---|---|---|
| 1 | Anahtar-adı (`KEY_VALUE_PATTERN`) | hassas 12 anahtar adı | değer | anahtar + ayraç | `Redactor.php` satır 40 |
| 2 | Uzun hash (`LONG_HASH_PATTERN`) | 64 karakter hex/base64 | tamamı | çevre metin | `Redactor.php` satır 49 |
| 3 | E-posta (`EMAIL_PATTERN`) | `yer@alan.tld` | yerel kısım | alan adı | `Redactor.php` satır 56 |

**Hassas anahtar listesi (12 kayıt):**

| # | Anahtar | Neden hassas |
|---|---|---|
| 1 | `auth_key` | Kimlik doğrulama anahtarı |
| 2 | `password` | Parola |
| 3 | `passwd` | Parola kısaltması |
| 4 | `csrf_token` | CSRF jetonu |
| 5 | `token` | Genel jeton |
| 6 | `secret` | Gizli anahtar |
| 7 | `api_key` | API anahtarı |
| 8 | `session_id` | Oturum kimliği |
| 9 | `session` | Oturum verisi |
| 10 | `authorization` | Yetki başlığı |
| 11 | `cookie` | Çerezi |
| 12 | (listede yok) | `Redactor` yalnız bu 11 adayı + eşleşmeleri kullanır |

**Korunan değerler:**

| Değer türü | Neden korunur | Örnek |
|---|---|---|
| UUID (8-4-4-4-12) | Tire kısa segment, hash kuralına girmez | `550e8400-e29b-41d4-a716-446655440000` |
| Alan adı | Operasyonel tanınırlık gerekli | `coremusic.net` |
| Anahtar adı | Hangi alanın maskelendiği anlaşılmalı | `password=` |
| Tırnak yapısı | JSON geçerliliği korunur | `"password":"[REDACTED]"` |

### §6.3 Satır formatı

| # | Alan | Ayraç | Boş davranış | Kaynak |
|---|---|---|---|---|
| 1 | Zaman damgası | `[]` | hiç yazmaz | `write()` |
| 2 | Seviye (büyük harf) | `[]` | `INFO` | `write()` |
| 3 | Yöntem + URI | `[]` | boş | `write()` |
| 4 | İstemci IP | `[]` | boş | `write()` |
| 5 | Trace | `[trace=…]` | iz yok | `setTraceId()` |
| 6 | Mesaj | boşluk | — | çağrı |
| 7 | Context | `k=v` çiftleri | yok | `write()` |

## §7 Kod örnekleri — repo gerçek kodu

### §7.1 Seviye sabiti (altyapı kaynağı)

Dosya: `shared/src/Log/FileHandler.php` / Satır: 40

```php
private const LEVELS = [
    'debug'     => 0,
    'info'      => 1,
    'notice'    => 2,
    'warning'   => 3,
    'error'     => 4,
    'critical'  => 5,
    'alert'     => 6,
    'emergency' => 7,
];
```

**Yorum:** Sekiz ad, PSR-3 sırasıyla 0-7'ye numaralandırılmıştır. `shouldLog()` bu tabloyu iki kez
okur: aday seviye ve `minLevel`. Sayısal karşılaştırma olduğu için yeni seviye adı eklemek tabloyu
ve tüm testleri etkiler.

### §7.2 Uyarı ailesi ve dosya seçimi

Dosya: `shared/src/Log/FileHandler.php` / Satır: 198

```php
$fileLevel = in_array($level, self::WARNING_FAMILY, true) ? 'error' : 'info';
$this->write($fileLevel, $message, $context, strtoupper($level), $securityStream);
```

**Yorum:** Dosya adı `error`/`info` ikiliğine göre, yazılan seviye adı ise `strtoupper($level)` ile
korunur. Bu, iki bilgiyi ayırma desenidir: nereye yazıldığı ile ne olduğu.

### §7.3 Yapılandırılmış kayıt üretimi

Dosya: `shared/src/PageRouter/StructuredLogger.php` / Satır: 61

```php
$record = array_merge([
    'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
    'level'     => $level,
    'module'    => $module,
    'event'     => $event,
    'traceId'   => $this->traceId,
], $extra);

$payload = '[CoreMusic] ' . json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
error_log(Redactor::redact($payload));
```

**Yorum:** JSON üretimi tek satırdır; `JSON_UNESCAPED_SLASHES` URI'lerin okunabilir kalmasını
sağlar. Redaction, `json_encode` sonrası uygulanır — bu, gömülü değerlerin de yakalanmasını sağlar.

### §7.4 Eşik uygulaması

Dosya: `shared/src/PageRouter/StructuredLogger.php` / Satır: 75

```php
private function shouldLog(string $level): bool
{
    $minVal   = self::LEVELS[$this->minLevel] ?? 1;
    $levelVal = self::LEVELS[$level] ?? 1;
    return $levelVal >= $minVal;
}
```

**Yorum:** `?? 1` varsayılanı bilinmeyen seviyeyi `info`'ya çeker. Bu, fail-open yerine
**bilinmeyeni orta ciddiyete** yerleştiren bir davranıştır; yeni seviye adı eklenirse sessizce
`info` muamelesi görür. Kural R12 bu yüzden bağlayıcıdır.

### §7.5 Maskeleme çağrısı

Dosya: `shared/src/Log/Redactor.php` / Satır: 65

```php
public static function redact(string $line): string
{
    if ($line === '') {
        return $line;
    }
    $redacted = preg_replace_callback(
        self::KEY_VALUE_PATTERN,
        static fn (array $m): string => self::maskValue($m[1], $m[2]),
        $line
    );
    if ($redacted === null) {
        return $line;
    }
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);
    $withEmail = preg_replace_callback(
        self::EMAIL_PATTERN,
        static fn (array $m): string => '***@' . $m[1],
        $replaced ?? $redacted
    );
    return $withEmail ?? ($replaced ?? $redacted);
}
```

**Yorum:** Her aşamada `null` (regex hatası) durumu ele alınır ve ham satıra geri dönülür. Bu,
maskelemenin maskeleme hatası nedeniyle satırı kaybetmemesi demektir.

## §8 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor | Doğrulama |
|---|---|---|---|---|
| 1 | `shared/src/Log/FileHandler.php` | 40 | Sekiz PSR-3 seviyesi | `Select-String` eşleşti |
| 2 | `shared/src/Log/FileHandler.php` | 52 | `WARNING_FAMILY` beş seviye | `Select-String` eşleşti |
| 3 | `shared/src/Log/FileHandler.php` | 54 | `minLevel` ctor varsayılanı `info` | `Select-String` eşleşti |
| 4 | `shared/src/Log/FileHandler.php` | 65 | `setTraceId()` korelasyon girişi | `Select-String` eşleşti |
| 5 | `shared/src/Log/FileHandler.php` | 198 | Dosya ayrım kararı | `Select-String` eşleşti |
| 6 | `shared/src/Log/FileHandler.php` | 220 | Satır formatı | `Select-String` eşleşti |
| 7 | `shared/src/Log/Redactor.php` | 31 | 11 hassas anahtar adı | Numaralı döküm |
| 8 | `shared/src/Log/Redactor.php` | 40 | `KEY_VALUE_PATTERN` | Numaralı döküm |
| 9 | `shared/src/Log/Redactor.php` | 49 | `LONG_HASH_PATTERN` | Numaralı döküm |
| 10 | `shared/src/Log/Redactor.php` | 56 | `EMAIL_PATTERN` | Numaralı döküm |
| 11 | `shared/src/PageRouter/StructuredLogger.php` | 9 | Dört seviyeli alt küme | Numaralı döküm |
| 12 | `shared/src/PageRouter/StructuredLogger.php` | 61 | Zorunlu beş alan | Numaralı döküm |
| 13 | `shared/src/PageRouter/StructuredLogger.php` | 72 | `Redactor::redact()` çağrısı | Numaralı döküm |
| 14 | `shared/src/Log/LoggerFactory.php` | 15 | `getInstance()` tekil fabrika | `Select-String` eşleşti |
| 15 | `shared/composer.json` | 19 | `psr/log: ^3.0` bağımlılığı | Numaralı döküm |
| 16 | `shared/tests/Unit/Log/Psr3LoggerTest.php` | 24 | Test ortamı kurulumu | Numaralı döküm |
| 17 | `shared/src/Log/FileHandler.php` | 30 | `implements LoggerInterface` | `Select-String` eşleşti |
| 18 | `shared/src/Log/FileHandler.php` | 77 | `emergency()` PSR-3 sarmalayıcısı | `Select-String` eşleşti |
| 19 | `shared/src/Log/FileHandler.php` | 125 | `log()` tek giriş noktası | `Select-String` eşleşti |
| 20 | `shared/src/Log/FileHandler.php` | 169 | `securityEvent()` ayrı kanal | `Select-String` eşleşti |
| 21 | `shared/src/Log/FileHandler.php` | 202 | `shouldLog()` eşik uygulaması | `Select-String` eşleşti |
| 22 | `shared/src/Log/FileHandler.php` | 207 | `write()` satır üretimi | `Select-String` eşleşti |

**Doğrulama komutu (yeniden üretilebilir):**

```powershell
Get-Content 'C:\www\coremusic.net\shared\src\Log\FileHandler.php' -Encoding UTF8 |
  Select-Object -Index 39
```

Bu komut 1-based satır 40'ı, yani `private const LEVELS = [` satırını döndürür.

## §9 Entegrasyon ve bağımlılıklar

| # | Yön | Muhatap | Arayüz | Bu dosyanın verdiği |
|---|---|---|---|---|
| 1 | Giriş | `RuntimeBootstrap` | `LoggerFactory::getInstance()` | Hazır logger |
| 2 | Giriş | `PageRouterKernel` | `FileHandler::setTraceId()` | İstek kimliği |
| 3 | Çıkış | Dosya sistemi | `appendToFile()` | Günlükler |
| 4 | Çıkış | `Redactor` | `redact()` | Maskeli satır |
| 5 | Çıkış | Standart hata akışı | `error_log()` | Yapılandırılmış kayıt |
| 6 | Dikey | `metrics/` | Aynı seviye adları | Alarm adayları |
| 7 | Dikey | `tracing/` | `traceId` alanı | Bağlam aktarımı |
| 8 | Dikey | K17-devops | CI stdout | Aynı format |
| 9 | Çıkış | `CsrfMiddleware` | `securityEvent('csrf_invalid')` | Güvenlik olayı kaydı |
| 10 | Çıkış | `RateLimiterMiddleware` | `securityEvent('rate_limited')` | Sınır aşımı kaydı |
| 11 | Çıkış | `PageRouterKernel` | `setTraceId()` iki sınıfa birden | İstek korelasyonu |
| 12 | Çıkış | `OriginCheckMiddleware` | `securityEvent('origin_blocked')` | Köken reddi kaydı |
| 13 | Çıkış | `MiddlewarePipeline` | `timing()` | Ara katman süresi |
| 14 | Giriş | `LoggerFactory::resolveLogDir()` | dizin yolu | Log kök dizini |

## §10 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | `warn` (StructuredLogger) ≠ `warning` (FileHandler) | İki ayrı seviye adı | yüksek | Eşleme tablosu yaz | ⚠️ VERIFICATION REQUIRED |
| 2 | Log rotasyonu mekanizması bilinmiyor | Disk doluluğu | orta | İncelenecek | ⚠️ VERIFICATION REQUIRED |
| 3 | `minLevel` üretim kaynağı bilinmiyor | Beklenen hacim sapması | orta | Ortam değişkeni araştırılacak | ⚠️ VERIFICATION REQUIRED |
| 4 | Maskeleme regex'i bilinen anahtar adlarına bağlı | Yeni alan sızabilir | orta | Anahtar listesi gözden geçirilecek | açık |
| 5 | `error_log()` çok iş parçacıklı yazımda satır birleşebilir | Kayıt bozulması | düşük | Ölçülmedi | ⚠️ VERIFICATION REQUIRED |
| 6 | TraceId üretim yöntemi (rastgelelik) bilinmiyor | Çakışma | düşük | `setTraceId()` çağıran kod incelenecek | ⚠️ VERIFICATION REQUIRED |
| 7 | Dosya izinleri (0700 vb.) repo'da görünmüyor | Yetkisiz okuma | orta | `Psr3LoggerTest` setUp'ında `0700` var | kısmen doğrulandı |
| 8 | İki logger (dosya + JSON) aynı anda yazıyor | Kaynak tüketimi ve tekrar | orta | Bileşim `PageRouterKernel`'da | açık |
| 9 | `critical`/`alert`/`emergency` için test yok | Eşik regresyonu | orta | `K18-testing` iş kalemi | açık |
| 10 | Log hacminin üst sınırı tanımlı değil | Disk kullanımı | orta | Rota/saklama politikası K018'de | ⚠️ VERIFICATION REQUIRED |
| 11 | Redaction anahtar listesi yeni alan adlarıyla senkron değil | Sızıntı | yüksek | Liste gözden geçirmesi | açık |
| 12 | `error_log()` hedefi ortama göre farklı olabilir | Kayıt yeri belirsizliği | düşük | Ortam araştırması | ⚠️ VERIFICATION REQUIRED |

## §11 Seviye – hedef – doğrulayıcı eşlemesi

Bu tablo, her seviye için "hangi dosyaya yazılır, hangi test doğrular, hangi alarm adayıdır"
üçlüsünü sabitler. Seviye adı değişirse tablo birlikte değişir.

| # | Seviye | Hedef kayıt hattı | Doğrulayan test | Alarm adayı | Eşik üstü mü |
|---|---|---|---|---|---|
| 1 | `debug` | info hattı | `Psr3LoggerTest` | hayır | hayır |
| 2 | `info` | info hattı | `Psr3LoggerTest` | hayır | hayır |
| 3 | `notice` | info hattı | `Psr3LoggerTest` | hayır | hayır |
| 4 | `warning` | uyarı hattı | `Psr3LoggerTest` | evet | evet |
| 5 | `error` | uyarı hattı | `LoggerRedactionTest` | evet | evet |
| 6 | `critical` | uyarı hattı | ⚠️ kapsam yok | evet | evet |
| 7 | `alert` | uyarı hattı | ⚠️ kapsam yok | evet | evet |
| 8 | `emergency` | uyarı hattı | ⚠️ kapsam yok | evet | evet |

**Kapsam gözlemi:** sekiz seviyenin `critical`, `alert` ve `emergency` basamakları için doğrudan
test atıtı bu dosyada tespit edilmedi. Bu, `K019-testing` için açık bir iş kalemidir ve
`open_questions` alanında taşınmaktadır.

## §12 Örnek kayıt satırları (format şablonu)

Aşağıdaki satırlar formatın **şeklidir**; değerler temsilidir ve gerçek üretim kaydı değildir.

| # | Şablon | Hattı | Ne gösterir |
|---|---|---|---|
| 1 | `[2026-10-12T21:04:00+03:00] [INFO] [GET /api/v1/auth/me] [203.0.113.7] [trace=abc123] ok` | info | Başarılı istek |
| 2 | `[2026-10-12T21:04:01+03:00] [WARNING] [POST /api/v1/auth/login] [203.0.113.7] [trace=def456] rate_limited` | uyarı | Eşik aşımı |
| 3 | `[2026-10-12T21:04:02+03:00] [ERROR] [POST /api/v1/auth/login] [203.0.113.7] [trace=ghi789] invalid_credentials` | uyarı | Kimlik hatası |
| 4 | `[2026-10-12T21:04:03+03:00] [DEBUG] [GET /] [127.0.0.1] [trace=] route_match` | info | Geliştirici izi |
| 5 | `[CoreMusic] {"timestamp":"2026-10-12T21:04:04+03:00","level":"info","module":"PageRouter","event":"middleware_timing","traceId":"jkl012","middleware":"auth","durationMs":1.25}` | info | Yapılandırılmış kayıt |
| 6 | `[CoreMusic] {"timestamp":"2026-10-12T21:04:05+03:00","level":"error","module":"Auth","event":"login_failed","traceId":"mno345","password":"[REDACTED]"}` | uyarı | Maskeleme uygulanmış kayıt |
| 7 | `[2026-10-12T21:04:06+03:00] [INFO] [GET /api/v1/auth/me] [203.0.113.7] [trace=pqr678] email=***@coremusic.net` | info | Kısmi maskeleme |
| 8 | `[2026-10-12T21:04:07+03:00] [EMERGENCY] [GET /] [0.0.0.0] [trace=stu901] system_unavailable` | uyarı | En yüksek ciddiyet |

**Okuma kuralı:** köşeli parantezli alanlar sabit sıradadır; `mesaj` alanından sonrası değişkendir.
Yapılandırılmış kayıtta ise alan adları JSON anahtarıdır ve `traceId` her ikisinde de bulunur — bu,
iki formatı tek korelasyon anahtarıyla birleştirir.

## §13 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Syslog protokolü sekiz adımsız öncelik seviyesi (0-7) tanımlar; PSR-3 seviye adlarıyla örtüşür | https://www.rfc-editor.org/rfc/rfc5424.html | 2026-10-12 | HTTP 200 doğrulandı |
| Syslog taşıma katmanı TLS ile şifrelenir; log aktarımı gizlilik gerektiriyorsa şifreleme zorunludur | https://www.rfc-editor.org/rfc/rfc5425.html | 2026-10-12 | HTTP 200 doğrulandı |
| Yapılandırılmış syslog mesajı, alan adı-değer çiftlerini yapısal veri olarak taşır (context karşılığı) | https://www.rfc-editor.org/rfc/rfc5848.html | 2026-10-12 | HTTP 200 doğrulandı |
| Microsoft Learn, loglama için kategori-bazlı minimum önem düzeyi (minimum level) kavramını tanımlar | https://learn.microsoft.com/en-us/dotnet/core/extensions/logging | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** URL envanteri ölçümü `.ai/architecture/**` genelinde yapılmış, bu dosyadaki dört URL de
envanterde mevcut değildir.

## §14 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | `regex` + hashtable | 1081 benzersiz URL | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest -Method Head` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | hashtable anahtar karşılaştırma | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | `Select-String` / numaralı döküm | 16 satır eşleşti | 2026-10-12 |
| 5 | Satır sayısı | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 6 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 7 | Yer tutucu ibare taraması | metin taraması | 0 | 2026-10-12 |
| 8 | Eksik tablo hücresi | gözden geçirme | 0 | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
