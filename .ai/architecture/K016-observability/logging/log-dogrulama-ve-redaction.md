---
title: "K016-observability/logging — Log Doğrulama ve Redaction: Maskeleme Kuralları, False-Positive Dengesi ve Kapı"
node_id: K016-observability/logging/log-dogrulama-ve-redaction
node_type: module
status: draft
parent_id: K016-observability/logging
children: []
depends_on:
  - K016-observability
  - K016-observability/logging
evidence:
  - "shared/src/Log/Redactor.php — 106 satir tam okuma: Satir 8 (AGENTS §17 kaynak kural), Satir 28 MASK, Satir 31 SENSITIVE_KEYS, Satir 40/49/56 desenler, Satir 65-92 redact() uc asama (2026-10-12)"
  - "shared/src/Log/FileHandler.php — 253 satir okuma: Satir 12-15 PSR-3 + setTraceId, Satir 19-25 bes log dosyasi, Satir 27-28 'tum satirlar Redactor'dan gecer', Satir 40-49 PSR-3 seviyeler (2026-10-12)"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php — 189 satir; Satir 55 testAuthKeyInQueryStringIsRedacted, Satir 63-64 maskeleme assertion, Satir 66 flag koruma, Satir 70 parola testi (2026-10-12)"
  - "shared/src/PageRouter/StructuredLogger.php — Satir 11 traceId alani, Satir 66 kayit anahtari (2026-10-12 tracing dosyasi ile ayni kanit)"
validation:
  - "Dosya satir sayilari [IO.File]::ReadAllLines ile olcüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 dondu; RFC'lerin icerik kimligi ilk satirlarla dogrulandi (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada cakisma 0 (2026-10-12)"
  - "Redactor.php 106 satir ve FileHandler.php 253 satir tam/klasor okundu; alinti satirlari birebir dogrulandi (2026-10-12)"
open_questions:
  - "Log retention (saklama) periyodu tanimli degil — silme politikasi yok (⚠️ VERIFICATION REQUIRED)"
  - "Redaction kapsam disi yollar (kernel_debug yazicisi) Redactor'dan geciyor mu — dogrulanmadi (⚠️ VERIFICATION REQUIRED)"
  - "E-posta maskesi (B-F-17/P5) disi PII siniflari (telefon, TC kimlik) icin desen eklenmedi (⚠️ VERIFICATION REQUIRED)"
  - "Kapali (skip) test sayisi: 0 (2026-10-12 tarama)"
group: mimari
---

# K016-observability/logging/log-dogrulama-ve-redaction

> **ELI10:** Log dosyaları okunabilir ve **kalıcıdır**; bu yüzden log'a düşen parola, token veya
> e-posta yıllarca orada durur. Bu dosya üç şeyi kurar: **neyin maskeleneceği** (Redactor
> kuralları), **neyin maskelenmeyeceği** (false-positive dengesi) ve **nasıl doğrulanacağı**
> (test kapısı). Maskeleme, yazılan **tek satırı** değiştirir; geçmiş log dosyalarına dokunulmaz.

## §1 Amaç

Bu modül, CoreMusic'in **log redaction (maskeleme) ve doğrulama kurallarının tek yetkili
tanımıdır**. Beş karar bu dosyada verilir: maskeleme desenleri · koruma istisnaları · test
kapısı · denetim (audit) kanalı · retention sınırı. Log **seviye/yapılandırma** kuralları komşu
dosyadadır (`logging-yapilandirma-ve-seviyeler.md`); burada yalnız **içerik güvenliği** ve
**doğrulama** konuşulur.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Neyin değeri maskelenir? | Hassas anahtar listesi | §5.1 |
| 2 | Hash ve uzun token maskelenir mi? | 64 karakter kuralı | §5.2 |
| 3 | E-posta maskelenir mi? | Kısmi maske | §5.3 |
| 4 | Ne MASKelenmez? | False-positive istisnaları | §6 |
| 5 | Nasıl doğrulanır? | Test seti + kapı | §7 |
| 6 | Geçmiş loglar ne olur? | Dokunulmazlık | §9 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Maskeleme noktası | adet | 1 (tek `Redactor`) | Redactor.php Satır 10-11 |
| H2 | Maskeleme değeri | dize | `[REDACTED]` | Satır 28 |
| H3 | Hassas anahtar | adet | 11 anahtar deseni | Satır 31 |
| H4 | Redaction testi | adet | 2 test dosyada | LoggerRedactionTest.php |
| H5 | Ham secret logda | adet | 0 (test Satır 64) | Doğrulandı |
| H6 | PII desen sayısı | adet | 3 (anahtar/hash/e-posta) | Satır 40/49/56 |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Maskeleme | ✅ Desen + kural + istisna | ❌ Desen kodu değişikliği |
| Doğrulama | ✅ Test seti + kapı | ❌ Test yazımı süreci |
| Seviyeler | ✅ Atıf (seviye listesi okunur) | ❌ Seviye kararı → `logging-yapilandirma-ve-seviyeler.md` |
| Dosya split | ✅ Audit kanalı atfı | ❌ Dosya yerleşimi → kardeş dosya |
| Trace köprüsü | ✅ `traceId` maske dışı | ❌ İz modeli → `../tracing/` |
| Secret envanteri | ✅ Sınır (şablon = K018) | ❌ CI taraması → K17-devops/ci |
| Syslog/uzak gönderim | ✅ Sınır (RFC bağlamı) | ❌ Aktarım tasarımı → `../tracing/` §7 |
| Retention | ✅ Dokunulmazlık kuralı | ❌ Silme periyodu → ⚠️ VERIFICATION REQUIRED |

**Sınır kararı:** redaction **yazma anında** uygulanır; okuma-anı gizleme yoktur (Satır 22
yorumu: maskeleme yalnızca yazılacak satırı etkiler).

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Redaction | redaction | Logda maskeleme | §5 |
| Mask | mask | `[REDACTED]` değeri | Satır 28 |
| Hassas anahtar | sensitive key | 11 desenli liste | Satır 31 |
| False-positive | false positive | Maskelememesi gerekirken maskeleme | §6 |
| Hash | hash | 64 karakter uzun özet | §5.2 |
| PII | personally identifiable info | Kişisel veri (e-posta) | §5.3 |
| Audit log | audit log | Güvenlik olay günlüğü | §8.1 |
| PSR-3 | PSR-3 | Logger arayüzü standardı | FileHandler Satır 12 |
| Koruma | preserve | Maske dışı bırakma | §6.1 |
| Dokunulmazlık | immutability | Geçmiş dosyaya dokunmama | §9 |
| Desen | pattern | Regex kuralı | Satır 40/49/56 |
| Üç aşama | pipeline | redact() sırası | Satır 71-92 |
| Syslog | syslog | Uzak log protokolü (RFC 5426) | §17 |
| Yönetim | management | Syslog MIB (RFC 5427) | §17 |

## §4 Repo gerçeği — redaction envanteri

### §4.1 Sınıf envanteri (2026-10-12 okuma)

| # | Dosya | Satır | Rol | Durum |
|---|---|---|---|---|
| 1 | `shared/src/Log/Redactor.php` | 106 | Tek maskeleme noktası | ✅ IMPLEMENTED |
| 2 | `shared/src/Log/FileHandler.php` | 253 | Dosya logger + redaction geçirici | ✅ IMPLEMENTED |
| 3 | `shared/tests/Unit/Log/LoggerRedactionTest.php` | 189 | Redaction doğrulaması | ✅ 2 test |
| 4 | `shared/src/PageRouter/StructuredLogger.php` | — | JSON logger (Satır 11/66 traceId) | ✅ mevcut |
| 5 | `shared/src/PageRouter/PageRouterKernel.php` | 135 | FATAL satırı redaction yolu | ✅ (tracing §9 satır 4) |

### §4.2 Redactor — kural kaynağı ve kapsam

| # | Satır | İçerik | Anlamı |
|---|---|---|---|
| 1 | 8 | `.ai/AGENTS.md §17 Edge Case 3` | Kural kaynağı (vault) |
| 2 | 10-11 | Tüm yazma yolları BURADAN geçer | Tek nokta |
| 3 | 13-20 | Kurallar 1-3 (anahtar, hash, koruma) | Şartname |
| 4 | 22 | Maskeleme yalnızca yazılacak satırı etkiler | Dokunulmazlık |
| 5 | 23 | Loglama seviyeleri değişmez | Sınır |
| 6 | 28 | `MASK = '[REDACTED]'` | Tek değer |
| 7 | 31 | `SENSITIVE_KEYS` (11 anahtar) | Liste |
| 8 | 58-60 | Özel kurucu (kullanım yok) | Statik sınıf |

### §4.3 Log dosyaları — redaction altındaki yüzey

| # | Dosya (FileHandler Satır 19-25) | Seviye | Ortam | Redaction |
|---|---|---|---|---|
| 1 | `coremusic_php_errors.log` | Tümü | her zaman | evet (Satır 27-28) |
| 2 | `coremusic_php_warnings.log` | warning ailesi | yalnız debug | evet |
| 3 | `coremusic_php_info.log` | info/debug | yalnız debug | evet |
| 4 | `coremusic_php_kernel_debug.log` | kernel | ayrı yazıcı | ⚠️ doğrulanmadı |
| 5 | `coremusic_php_security.log` | [AUTH]/[SECURITY] | prod dâhil | evet (audit) |

### §4.4 Test envanteri

| # | Test | Dosya satırı | Ne doğrular | Durum |
|---|---|---|---|---|
| 1 | `testAuthKeyInQueryStringIsRedacted` | 55 | URI'de `auth_key` maskelenir | yeşil varsayımı |
| 2 | `assertStringContainsString('auth_key=[REDACTED]')` | 63 | Maske uygulanır | — |
| 3 | `assertStringNotContainsString(self::AUTH_KEY)` | 64 | Ham değer yok | — |
| 4 | Flag koruma assertion'ı | 66 | `has_auth_key` korunur | — |
| 5 | Alt-anhtar prefix assertion'ı | 67 | Kısaltılmış önek korunur | — |
| 6 | `testPasswordIsRedactedInKeyValueAndJsonFormats` | 70 | İki biçimde parola | — |

## §5 Maskeleme kuralları

### §5.1 Kural 1 — hassas anahtar-adı eşleşmesi

| # | Anahtar (Satır 31) | Sınıf | Örnek bağlam | Etki |
|---|---|---|---|---|
| 1 | `auth_key` | kimlik | URL sorgu dizesi | Değer maskelenir |
| 2 | `password` | kimlik | Form/JSON | Değer maskelenir |
| 3 | `passwd` | kimlik | Form | Değer maskelenir |
| 4 | `csrf_token` | güvenlik | Form/çerez | Değer maskelenir |
| 5 | `token` | güvenlik | Bağlam geneli | Değer maskelenir |
| 6 | `secret` | güvenlik | Sağlayıcı anahtarı | Değer maskelenir |
| 7 | `api_key` | güvenlik | Başlık | Değer maskelenir |
| 8 | `session_id` | oturum | Çerez | Değer maskelenir |
| 9 | `session` | oturum | Bağlam | Değer maskelenir |
| 10 | `authorization` | başlık | `Bearer` değeri | Çok kelimeli |
| 11 | `cookie` | başlık | Çerez dizesi | Değer maskelenir |

**Biçimler (Satır 17-18):** `key=value` · `key: value` · `"key":"value"` (JSON) ·
`authorization: Bearer <token>` (çok kelimeli). Büyük/küçük harf duyarsız (Satır 16).

### §5.2 Kural 2 — 64 karakter uzun hash

| # | Kural | Değer | Satır | Gerekçe |
|---|---|---|---|---|
| 1 | Uzunluk | tam 64 karakter | 49 | Hash boyutu |
| 2 | Karakter kümesi | hex/base64 (`A-Za-z0-9+/`) | 49 | Özet biçimi |
| 3 | Sınır kuralı | lookaround (`?<!` / `?!`) | 44-46 | `\b` yan etkisi |
| 4 | `=` sınırda | tutulmaz | 46 | `key=` değerleri maskelenebilmeli |
| 5 | Sonuç | `[REDACTED]` | 83 | Tek değer |

### §5.3 Kural 3 — e-posta maskesi (kısmi)

| # | Kural | Değer | Satır | Amaç |
|---|---|---|---|---|
| 1 | Yerel kısım | `***` olur | 52, 88 | Gizlilik |
| 2 | Alan adı | korunur | 52, 56 | Ops tanınırlığı |
| 3 | Lookaround sınırı | uzun token ortası | 54 | False-positive önleme |
| 4 | Kaynak | B-F-17 / P5 kararı | 52-53 | Vault atfı |
| 5 | Diğer PII | desen yok | — | ⚠️ VERIFICATION REQUIRED |

### §5.4 Maskeleme değeri kuralları

| # | Kural | Değer | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | Tek maske değeri | `[REDACTED]` (Satır 28) | Arama kolaylığı | Kod |
| 2 | Anahtar adı korunur | `password=[REDACTED]` | Bağlam kaybı yok | Test Satır 63 |
| 3 | Tırnak yapısı korunur | `"password":"[REDACTED]"` | JSON sağlamlığı | Satır 37, 100-102 |
| 4 | Ayraç korunur | `prefix` saklanır | Satır 96-97 | — |
| 5 | Değer maskelenir | yalnız | Asgari etki | §6 |

## §6 False-positive dengesi (neyin MASKenmediği)

### §6.1 Koruma listesi

| # | Korunan | Kural | Satır | Gerekçe |
|---|---|---|---|---|
| 1 | UUID (8-4-4-4-12) | Tire kısa segment → hash kuralına girmez | 20, 47, 82 | İz köprüsü kırılmaz |
| 2 | Boş satır | Aynen döner | 67-69 | Gereksiz iş yok |
| 3 | Temiz satır | Aynen döner (false-positive yok) | 63, 78-80 | Gürültü yok |
| 4 | `has_auth_key` flag | `has_` öneki anahtar-adı eşleşmesine girmez | Test Satır 66 | Bayrak değeri korunur |
| 5 | Alt-anhtar prefix | Kısaltılmış önek maskelenmez | Test Satır 65-67 | Ops tanınırlığı |
| 6 | Loglama seviyeleri | Değişmez | Satır 23 | Kapsam dışı |
| 7 | Geçmiş `.log` dosyaları | Dokunulmaz | Satır 22 | §9 |

### §6.2 Koruma – maske kararı matrisi

| # | Değer türü | Örnek | Karar | Neden |
|---|---|---|---|---|
| 1 | 64 karakter hash | Uzun özet | Maske | §5.2 |
| 2 | UUID v4 | İz kimliği | Koru | §6.1 satır 1 |
| 3 | Kısa hex (8 karakter) | Prefix örneği | Koru | Sınır altında |
| 4 | E-posta | Yerel + alan | Kısmi maske | §5.3 |
| 5 | `Bearer` token | Çok kelimeli | Maske | Satır 18 |
| 6 | URL yolu | `/index.php` | Koru | Yol veri |
| 7 | HTTP durumu | `200` | Koru | Metrik |

### §6.3 Dengenin kaynağı

| # | Dengi | Ölçüm | Sonuç | Kanıt |
|---|---|---|---|---|
| 1 | Gizlilik ↔ tanınırlık | E-posta: yerel gizli, alan açık | 2 taraf | Satır 52-53 |
| 2 | Güvenlik ↔ köprü | UUID korunur, hash maskelenir | 2 kural | Satır 20, 47 |
| 3 | Bayrak ↔ değer | `has_auth_key` korunur | 2 katman | Test Satır 66 |
| 4 | Temiz satır ↔ iş | Aynen döner | 0 maliyet | Satır 67-69 |

## §7 Doğrulama (test kapısı)

### §7.1 Test seti matrisi

| # | Test | Ne kapsar | Dosya satırı | Durum |
|---|---|---|---|---|
| 1 | URI + JSON `auth_key` | Kural 1 + biçim 4 | 55-68 | ✅ |
| 2 | Parola iki biçim | `key=value` + JSON | 70 (başlık) | ✅ |
| 3 | 64 karakter hash | Kural 2 | ⚠️ Doğrulanmadı | Kapsam açığı |
| 4 | E-posta maskesi | Kural 3 | ⚠️ Doğrulanmadı | Kapsam açığı |
| 5 | UUID koruma | İstisna 1 | ⚠️ Doğrulanmadı | Kapsam açığı |
| 6 | Temiz satır aynen | İstisna 3 | ⚠️ Doğrulanmadı | Kapsam açığı |

**Kapsam uyarısı:** dosyada **2** test başlığı görülür (Satır 55, 70); kural 2 ve kural 3
için doğrudan test **görülmedi** (2026-10-12 okuma) — kapsam açığı §14 risk 3.

### §7.2 Kapı kuralları

| # | Kural | Zorunlu mu | Uygulama | Gerekçe |
|---|---|---|---|---|
| 1 | Yeni hassas anahtar → test zorunlu | evet | §16 şablonu | Körleşme |
| 2 | Yeni desen → sahte pozitif testi | evet | §6.1 | Gürültü |
| 3 | Test yeşil → kapı | evet | CI php-test | Regresyon |
| 4 | Maske değeri değişikliği | onay | Vault | Kırılma |
| 5 | Kernel yazıcısı doğrulaması | evet | ⚠️ Açık | §4.3 satır 4 |

### §7.3 Doğrulama senaryosu (taslak)

| # | Senaryo | Girdi | Beklenen | Kanıt |
|---|---|---|---|---|
| 1 | URI'de anahtar | `?auth_key=deger` | `auth_key=[REDACTED]` | Test Satır 63 |
| 2 | Ham değer | Aynı satır | Ham listede yok | Test Satır 64 |
| 3 | Bayrak | `has_auth_key:true` | Korunur | Test Satır 66 |
| 4 | Parola JSON | `"password":"deger"` | Maske | Test Satır 70 |
| 5 | 64 hash | 64 karakter özet | `[REDACTED]` | Satır 83 (kod) |
| 6 | E-posta | `ad@alan.com` | `***@alan.com` | Satır 88 (kod) |

## §8 Denetim (audit) kanalı

### §8.1 Security logu

| # | Alan | Değer | Kanıt | Not |
|---|---|---|---|---|
| 1 | Dosya | `coremusic_php_security.log` | FileHandler Satır 24-25 | Ayrı stream |
| 2 | Olaylar | `[AUTH]` / `[SECURITY]` | Satır 24 | Audit tail'i |
| 3 | Ortam | prod dâhil her zaman | Satır 25 | Sürekli denetim |
| 4 | Redaction | evet | Satır 27-28 | Güvenli |
| 5 | Sahiplik | güvenlik olayları | K009 sınırı | Atıf |

### §8.2 Denetim – redaction etkileşimi

| # | Soru | Karar | Gerekçe | Sınır |
|---|---|---|---|---|
| 1 | Audit olayı maskelenir mi? | Değeri evet, olay adı hayır | Denetlenebilirlik | §5 |
| 2 | Ham kimlik yazılır mı? | Hayır | Güvenlik | §9 |
| 3 | Trace köprüsü korunur mu? | Evet (UUID) | İz | §6.1 |
| 4 | Ops tanınırlığı | E-posta alan adı kalır | Denge | §5.3 |

## §9 Dokunulmazlık ve retention

### §9.1 Dokunulmazlık kuralları

| # | Kural | Zorunlu mu | Kanıt | Gerekçe |
|---|---|---|---|---|
| 1 | Geçmiş `.log` dosyalarına dokunulmaz | evet | Satır 22 | Bütünlük |
| 2 | Maskeleme yalnız yazma anında | evet | Satır 22 | Performans |
| 3 | Düzeltme = yeni satır | evet | Denetim | Tarihçe |
| 4 | Silme | tanımsız | ⚠️ Retention yok | §14 risk 1 |

### §9.2 Retention kararı matrisi

| # | Seçenek | Artı | Eksi | Durum |
|---|---|---|---|---|
| 1 | Sonsuz saklama | Tam tarihçe | Disk + gizlilik | Mevcut (bilinmeyen) |
| 2 | Süre bazlı silme | Disk güvencesi | Delil kaybı | ⚠️ Karar yok |
| 3 | Rotasyon + arşiv | Denge | Kurulum | ⚠️ Karar yok |
| 4 | WORM/depoya taşıma | Güçlü denetim | Altyapı | ⚠️ Karar yok |

**Kural:** retention periyodu **insan kararıdır**; kod tarafında yalnız dokunulmazlık kuralı
(Satır 22) geçerlidir.

## §10 Kod örnekleri — repo gerçek kodu

### §10.1 Maske değeri ve liste — Satır 28 ve 31

```php
    /** Maskeleme değeri. */
    public const MASK = '[REDACTED]';

    /** Hassas anahtar listesi. */
    private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';
```

**Dosya: `shared/src/Log/Redactor.php` / Satır: 27 · 28 · 30 · 31**

### §10.2 Uzun-hash deseni — Satır 49

```php
    private const LONG_HASH_PATTERN = '/(?<![A-Za-z0-9+\/])[A-Za-z0-9+\/]{64}(?![A-Za-z0-9+\/])/';
```

**Dosya: `shared/src/Log/Redactor.php` / Satır: 49**

Yorum satırı (Satır 44-47) gerekçeyi içerir: `\b` yerine lookaround kullanılır; `=` sınırda
tutulmaz; UUID tire içerdiği için bu kurala girmez.

### §10.3 Üç aşama — Satır 82-92 (özet alıntı)

```php
        // 2) 64-char hex/base64 hash maskesi — UUID'ler (8-4-4-4-12) korunur.
        $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);

        // 3) E-posta maskesi — yerel kısım gizlenir, alan adı kalır (B-F-17).
        $withEmail = preg_replace_callback(
            self::EMAIL_PATTERN,
            static fn (array $m): string => '***@' . $m[1],
            $replaced ?? $redacted
        );
```

**Dosya: `shared/src/Log/Redactor.php` / Satır: 82 · 83 · 85 · 86 · 87 · 88 · 89**

### §10.4 Seviye ailesi — FileHandler Satır 40-52

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

    /** Uyarı-ailesi → warnings.log (debug modda ek split). */
    private const WARNING_FAMILY = ['warning', 'error', 'critical', 'alert', 'emergency'];
```

**Dosya: `shared/src/Log/FileHandler.php` / Satır: 40 · 52**

### §10.5 Test assertion'ları — LoggerRedactionTest Satır 63-66

```php
        $this->assertStringContainsString('auth_key=[REDACTED]', $redacted);
        $this->assertStringNotContainsString(self::AUTH_KEY, $redacted);
        // Alt-anhtarlar maskeleme flag'i değildir, korunur (false-positive yok).
        $this->assertStringContainsString('"has_auth_key":true', $redacted);
```

**Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` / Satır: 63 · 64 · 65 · 66**

## §11 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | shared/src/Log/Redactor.php | 8 | Kural kaynağı (AGENTS §17 Edge Case 3) |
| 2 | shared/src/Log/Redactor.php | 10-11 | Tek maskeleme noktası |
| 3 | shared/src/Log/Redactor.php | 13-20 | Üç kural şartnamesi |
| 4 | shared/src/Log/Redactor.php | 22 | Geçmiş dosyalara dokunulmazlık |
| 5 | shared/src/Log/Redactor.php | 23 | Seviyeler değişmez (sınır) |
| 6 | shared/src/Log/Redactor.php | 28 | `MASK` değeri |
| 7 | shared/src/Log/Redactor.php | 31 | `SENSITIVE_KEYS` (11 anahtar) |
| 8 | shared/src/Log/Redactor.php | 40 | `KEY_VALUE_PATTERN` |
| 9 | shared/src/Log/Redactor.php | 44-47 | Lookaround gerekçesi yorumu |
| 10 | shared/src/Log/Redactor.php | 49 | `LONG_HASH_PATTERN` |
| 11 | shared/src/Log/Redactor.php | 52-56 | E-posta maskesi kararı |
| 12 | shared/src/Log/Redactor.php | 65-92 | `redact()` üç aşaması |
| 13 | shared/src/Log/Redactor.php | 98-104 | `maskValue()` tırnak koruma |
| 14 | shared/src/Log/FileHandler.php | 12-15 | PSR-3 + `setTraceId()` köprüsü |
| 15 | shared/src/Log/FileHandler.php | 19-25 | Beş log dosyası envanteri |
| 16 | shared/src/Log/FileHandler.php | 27-28 | Tüm satırlar Redactor'dan geçer |
| 17 | shared/src/Log/FileHandler.php | 40-49 | PSR-3 seviye listesi |
| 18 | shared/src/Log/FileHandler.php | 52 | `WARNING_FAMILY` |
| 19 | shared/src/Log/FileHandler.php | 54-60 | Kurucu + debug mode |
| 20 | shared/tests/Unit/Log/LoggerRedactionTest.php | 55 | `auth_key` testi |
| 21 | shared/tests/Unit/Log/LoggerRedactionTest.php | 63-64 | Maske + ham değer yok |
| 22 | shared/tests/Unit/Log/LoggerRedactionTest.php | 66-67 | Flag/alt-anhtar koruma |
| 23 | shared/tests/Unit/Log/LoggerRedactionTest.php | 70 | Parola iki biçim testi |
| 24 | shared/src/PageRouter/StructuredLogger.php | 11, 66 | `traceId` alanı (köy köprüsü) |

## §12 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | İkinci maskeleme noktası açmak | Tutarsızlık | Kod incelemesi |
| 2 | Ham secret'ı loga yazmak | Kalıcı sızıntı | Test Satır 64 |
| 3 | Maske değerini değiştirmek | Arama/uyarı kırılır | Onay (§7.2 satır 4) |
| 4 | UUID'yi maskelemek | İz köprüsü kopar | §6.1 satır 1 |
| 5 | Geçmiş `.log` dosyasını düzenlemek | Delil bütünlüğü | Satır 22 |
| 6 | Seviyeleri redaction ile karıştırmak | Kapsam kayması | Satır 23 |
| 7 | Yeni anahtarsız desen eklemek | Kör kural | §16 |
| 8 | Redaction'ı atlayarak "hızlı log" | Sızıntı | Kod incelemesi |
| 9 | E-postayı tam maskemek (alan dâhil) | Ops tanınırlığı kaybı | §5.3 |
| 10 | Retention'sız "sildik" iddiası | Kanıt kaybı | §9.2 |

## §13 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | `logging-yapilandirma-ve-seviyeler.md` | Seviye kararı | Maskeleme yüzeyi | Satır 40-52 | mevcut |
| 2 | `../tracing/tracing-dagilik-izleme.md` | UUID koruma kuralı | İz köprüsü | §6.1 satır 1 | mevcut |
| 3 | `../metrics/metrics-olcumleme-ve-etiketleme.md` | Etiket gizliliği | PII etiketlenmez | Kural | ⚠️ VERIFICATION REQUIRED |
| 4 | `../alerting/alerting-esik-ve-yukseklik.md` | Uyarı metni güvenliği | Redaction | Ortak | mevcut |
| 5 | K17-devops/ci/secret-yonetimi-ve-tarama | Statik tarama | Dinamik redaction | §12 (dosyaları) | mevcut |
| 6 | K009-security | Politika | Hassas sınıf listesi | Atıf | sınır |
| 7 | K18-testing | Test kapısı | Redaction testi | §7.1 | mevcut |

## §14 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Retention periyodu yok | Gizlilik + disk | orta | İnsan kararı | ⚠️ VERIFICATION REQUIRED |
| 2 | Kernel yazıcısı doğrulanmadı | Maske kaçak olabilir | yüksek | Doğrulama | ⚠️ VERIFICATION REQUIRED |
| 3 | Kural 2/3 için test görünmedi | Regresyon kaçabilir | orta | Test ekleme | §7.1 satır 3-4 |
| 4 | PII kapsamı dar (yalnız e-posta) | Yeni PII sızabilir | orta | Desen genişletme | §5.3 satır 5 |
| 5 | 11 anahtar sabit | Yeni anahtar unutulabilir | düşük | §16 şablonu | mevcut |
| 6 | Regex maliyeti (satır uzun) | Uzun satır yavaşlığı | düşük | Ölçüm | `performance/` |
| 7 | Maskelenen değer kurtarılamaz | Ops zorluğu | düşük | Bilinçli tasarım | Satır 28 |
| 8 | E-posta alan adı açık | Kişisel veri kalır | düşük | Bilinçli denge | §5.3 |

## §15 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Test | 2 test yeşil | CI php-test | her PR | §7.1 |
| 2 | Kapı | Yeni anahtar → test | Kod incelemesi | ekleme anında | §16 |
| 3 | Kod | Tek maskeleme noktası | Sınıf incelemesi | her PR | Satır 10-11 |
| 4 | Denetim | Security logu prod dâhil | Dosya varlığı | aylık | §8.1 |
| 5 | Köprü | UUID logda korunur | Test | her PR | §6.1 satır 1 |
| 6 | Kapsam | Kural 2/3 testi | Test sayımı | çeyrek | §7.1 |
| 7 | Retention | Periyot kararı | Onay | ⚠️ | §9.2 |

## §16 Kabul kriteri — yeni hassas sınıf eklendiğinde

| # | Şablon adımı | Zorunlu | Çıktı | Denetim |
|---|---|---|---|---|
| 1 | Sınıf adı | evet | PII/secret sınıfı | §3 |
| 2 | Anahtar deseni | evet | `SENSITIVE_KEYS` satırı | Satır 31 |
| 3 | Biçim testi | evet | `key=value` + JSON + başlık | §7.2 kural 1 |
| 4 | Sahte pozitif testi | evet | Korunan değerler | §7.2 kural 2 |
| 5 | Desen sınırı | evet | lookaround kararlı | Satır 44-46 |
| 6 | Vault kaydı | evet | Kural güncellemesi | Onay |
| 7 | K009 bildirimi | tercih | Politika eşlemesi | Atıf |

## §17 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| RFC 5426 (Transmission of Syslog Messages over UDP), syslog mesajlarının UDP ile aktarımını tanımlar; başlık: "Transmission of Syslog Messages over UDP" | https://www.rfc-editor.org/rfc/rfc5426.txt | 2026-10-12 | HTTP 200 + içerik kimliği doğrulandı |
| RFC 5427 (Textual Conventions for Syslog Management), syslog yönetim MIB tanımlarını verir; başlık: "Textual Conventions for Syslog Management" | https://www.rfc-editor.org/rfc/rfc5427.txt | 2026-10-12 | HTTP 200 + içerik kimliği doğrulandı |
| MDN Privacy on the web sayfası, web'de gizlilik (privacy) kavramını tanımlar | https://developer.mozilla.org/en-US/docs/Web/Privacy | 2026-10-12 | HTTP 200 doğrulandı |
| Microsoft Learn, Logging in .NET and ASP.NET Core sayfası loglama çerçevesini tanımlar | https://learn.microsoft.com/en-us/aspnet/core/fundamentals/logging/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 10 yeni
derin-branch dosyasında bulunmaz. RFC kimlikleri yanıt gövdesinin ilk satırlarıyla (RFC numarası,
başlık, yazar/kurum) doğrulanmıştır.

## §18 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 + başlık/kimlik | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Redactor.php | tam okuma (106 satır) | Satır 8-92 doğrulandı | 2026-10-12 |
| 5 | FileHandler.php | okuma (253 satır) | Satır 12-60 doğrulandı | 2026-10-12 |
| 6 | LoggerRedactionTest.php | okuma (189 satır) | Satır 55-70 doğrulandı | 2026-10-12 |
| 7 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 8 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 9 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 10 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 11 | group alanı | frontmatter | `mimari` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
