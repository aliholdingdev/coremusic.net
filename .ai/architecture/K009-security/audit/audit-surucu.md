---
node_id: K009-security/audit/audit-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/audit/audit-anayasa
  - K016-observability
evidence: "FileHandler.php·188-235/248-252 · StructuredLogger.php·55-73 · Redactor.php·65-93 · UserLoggedInEvent.php·26-31 · SecurityHelper.php·42-55"
validation: "2026-10-15 — sürüş akışları disk kanıtıyla; K017 tüketim kanıtı YOK → ⚠️"
open_questions: "K017 tüketim akışı ⚠️ · sırra-erişim olay sürüşü ⚠️ (tanım yok) · yazma hatası fail-safe değil ⚠️"
group: mimari
---

# K009 · audit · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · parent: K009-security/audit · rol: denetim izi sürüş akışları.
> Kurallar `audit-anayasa.md` · gerçek kod `audit-ornekler.md` · kanıt `audit-dogrulama.md`.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K009-security/audit/audit-surucu |
| node_type | module |
| Grup | mimari |
| Kapsam | olay → normalize → maskele → yaz → tüket akışı · yetki reddi · sırra erişim · hata kolları |
| Kapsam dışı | ne kaydedilmez (anayasa §3) · sınırlar (audit-kapsam) · URL kanıtı (web-arastirma) |

## §2 Sürüş Sözlüğü

| Türkçe | English | Açıklama |
|---|---|---|
| Sürüş | Drive / execution path | Bir olay kaydının adım adım üretilmesi |
| Olay üretici | Event producer | Kritik olayı tetikleyen katman |
| Normalize | Normalization | Alan adlarının standart hale getirilmesi |
| Maskeleme | Redaction | Hassas değerin `[REDACTED]` yapılması |
| Tüketim | Consumption | Kaydın K017'ye aktarılması |
| Hata kolu | Error branch | Yazma/redaction hatalarının sürülmesi |

## §3 Ana Sürüş Akışı (5 Adım — index §1)

| Adım | Eylem | Sözleşme | Kanıt |
|---|---|---|---|
| 1 | Olay üretilir (login/logout/yetki reddi/sır erişimi) | DomainEventInterface / securityEvent | `UserLoggedInEvent.php·20/39` |
| 2 | Alanlar normalize edilir | StructuredLogger JSON alan adları | `StructuredLogger.php·61-67` |
| 3 | Hassas alan maskelenir | Redactor tek nokta | `StructuredLogger.php·72`, `FileHandler.php·224` |
| 4 | Kayıt yazılır (append-only, zaman damgalı) | `FILE_APPEND \| LOCK_EX` | `FileHandler.php·251` |
| 5 | Kayıt tüketilir | K016-observability | ⚠️ KANIT YOK (U-1) |

| Durum | Sonraki |
|---|---|
| olay üretildi | adım 2 |
| normalize tamam | adım 3 |
| maskeleme tamam | adım 4 |
| yazım tamam | adım 5 |

## §4 Yapılandırılmış Kayıt Sürüşü (StructuredLogger)

| Adım | Eylem | Kanıt |
|---|---|---|
| L-1 | Seviye eşiği kontrolü | `StructuredLogger.php·57-59` |
| L-2 | Zaman damgası (ATOM) + level + module + event + traceId | `StructuredLogger.php·61-67` |
| L-3 | Extra alanlar birleştirilir | `StructuredLogger.php·61` `array_merge` |
| L-4 | JSON gövde (`[CoreMusic] ` öneki) | `StructuredLogger.php·69` |
| L-5 | `Redactor::redact` → `error_log` | `StructuredLogger.php·72` |

| Durum | Sonraki |
|---|---|
| minLevel üstü | L-2 |
| minLevel altı | dur (kayıt yok — seviye politikası) |
| preg null | ham satır gider (anayasa H-01) |

## §5 Dosya Yazıcı Sürüşü (FileHandler::write)

| Adım | Eylem | Kanıt |
|---|---|---|
| W-1 | timestamp / uri / method / ip / trace toplanır | `FileHandler.php·209-213` |
| W-2 | context JSON gövdesi | `FileHandler.php·215-218` |
| W-3 | Satır kurulur `[ts] [LEVEL] [method uri] [ip] [trace] msg ctx` | `FileHandler.php·220` |
| W-4 | `Redactor::redact($line)` | `FileHandler.php·224` |
| W-5 | errors.log'a append | `FileHandler.php·228` |
| W-6 | securityStream ise security.log'a da append | `FileHandler.php·233-235` |
| W-7 | debug mode ise warnings/info split | `FileHandler.php·238-244` |

| Akış Sınıfı | Durum |
|---|---|
| securityStream=true (authz/csrf/origin/rate-limit/bypass) | ikiz yazım — §8 |
| securityStream=false (normal) | tek hedef errors.log |

## §6 Yetki Reddi Olayı Sürüşü (authz_denied)

| Adım | Eylem | Kanıt |
|---|---|---|
| D-1 | Yetki katmanı reddeder | `authorization` (öncesi — index §5) |
| D-2 | `securityEvent('authz_denied', ctx)` çağrılır | `FileHandler.php·169` (sözleşme) |
| D-3 | context anahtarları: ip, uri, origin, role, code, reason, path | `FileHandler.php·172` |
| D-4 | Seviye `warning` (düşme yok) | `FileHandler.php·178-181` |
| D-5 | securityStream=true → security.log tail'i | `FileHandler.php·233-235` |
| D-6 | Çağrıran middleware dosyası | ⚠️ okunmadı (U-3) |

| İddia | Durum |
|---|---|
| authz_denied olay adı tanımlı | ✅ `FileHandler.php·179` |
| Üreten middleware kod kanıtı | ⚠️ KANIT YOK (PermissionMiddleware okunmadı) |
| Reddi üreten karar kaynağı | `authorization` düğümü |

## §7 Sıra Dışı Akış — Sırra Erişim Olayı (secret_access)

| Adım | Beklenen | Repo Kanıtı | Durum |
|---|---|---|---|
| S-1 | Sır okuma olayı tetiklenir | secret_access event adı TARAMADA YOK | ⚠️ KANIT YOK |
| S-2 | Erişim bağlamı normalize | — | ⚠️ |
| S-3 | Değer maskelenir | Redactor `secret` anahtarı hazır | `Redactor.php·31` |
| S-4 | security.log'a yazılır | securityEvent sözleşmesi hazır | `FileHandler.php·169` |
| S-5 | Tüketim | K017 | ⚠️ |

| Sonuç | |
|---|---|
| Altyapı (Redactor + securityEvent) SIRRA ERİŞİM olayına HAZIR | evet (§7-3/§7-4) |
| Olayın ÜRETİLDİĞİ kod | ⚠️ KANIT YOK → anayasa U-3 |

## §8 Test Bypass Aktivasyonu Sürüşü

| Adım | Eylem | Kanıt |
|---|---|---|
| B-1 | `isTestBypassActive` → production'da false | `SecurityHelper.php·25-27` |
| B-2 | Aktifse `logTestBypass(context, file, line)` | `SecurityHelper.php·42` |
| B-3 | Satır: `[tarih] [TEST_BYPASS] context=.. file=.. line=..` | `SecurityHelper.php·47-53` |
| B-4 | `FILE_APPEND \| LOCK_EX` ile errors.log'a | `SecurityHelper.php·46/54` |

| Durum | Sonraki |
|---|---|
| app.env=production | bypass yok, kayıt yok (fail-closed) |
| test mode aktif | her aktivasyonda mutlak kayıt |

## §9 Hata Kolu Sürüşü

| Tetikleyici | Sürüş | Kanıt |
|---|---|---|
| Redactor `preg` null | ham satır döner (kayıp yok) | `Redactor.php·78-80` |
| Redactor email `preg` null | önceki sonuç korunur | `Redactor.php·90-92` |
| `@file_put_contents` başarısız | sessiz ⚠️ (suppress) | `FileHandler.php·251` |
| Bilinmeyen seviye | `error`'a düşer (fail-safe) | `FileHandler.php·127-130` |
| context boş | contextStr atlanır | `FileHandler.php·216-218` |
| traceId boş | `[trace=]` eklenmez | `FileHandler.php·213` |
| JSON encode hatası | — ⚠️ okunmadı | UNKNOWN (U-5) |

| F-Adım | Hata Sürüşü |
|---|---|
| F-1 | Hata oluşur |
| F-2 | Redaction yolunda mı? → evet: ham döner (anayasa H-01) |
| F-3 | Yazım yolunda mı? → evet: sessiz suppress ⚠️ (U-4) |
| F-4 | İş akışı durmaz (log hatası istek durdurmaz) |

## §10 Durum Geçiş Sürücüsü

| Geçiş | Koşul | Kanıt |
|---|---|---|
| IDLE → PRODUCED | olay nesnesi / event çağrısı | `UserLoggedInEvent.php·31` |
| PRODUCED → NORMALIZED | array/JSON kurulumu | `StructuredLogger.php·61-69` |
| NORMALIZED → MASKED | `Redactor::redact` | `FileHandler.php·224` |
| MASKED → WRITTEN | `appendToFile` | `FileHandler.php·228` |
| WRITTEN → CONSUMED | K017 okuması | ⚠️ KANIT YOK (U-1) |
| * → DROPPED | seviye eşiği altı | `StructuredLogger.php·57-59` |

## §11 Seviye Eşiği Sürüşü

| Durum | Sürüş | Kanıt |
|---|---|---|
| StructuredLogger minLevel='info' (varsayılan) | debug/warn/elevation | `StructuredLogger.php·14/75-80` |
| FileHandler minLevel | constructor argümanı | `FileHandler.php·54` |
| debug seviyesi | yalnız debugMode AÇIK | `FileHandler.php·190-193` |
| securityEvent | eşiğe bakmaksızın warning (audit) | `FileHandler.php·178-183` — shouldLog uyar |

| Not | |
|---|---|
| securityEvent `logAt` üzerinden `shouldLog`'a girer | minLevel error ise warning düşebilir → ⚠️ (U-6) |

## §12 Hata Türü × Sürüş Matrisi

| # | Tür | Sürüş | Sonuç |
|---|---|---|---|
| 1 | regex uyumsuzluğu | null → ham satır | kayıt korunur |
| 2 | disk dolu | `@` suppress | sessiz kayıp ⚠️ |
| 3 | dosya izni yok | suppress | sessiz kayıp ⚠️ |
| 4 | JSON encode hatası | — | UNKNOWN (U-5) |
| 5 | seviye tanınmaz | error'a | fail-safe |
| 6 | traceId üretilmemiş | `-`/yok | kayıt yine yazılır |

## §13 Çağrı Zincisi (Katmanlar)

| Katman | Rol | Kanıt |
|---|---|---|
| Domain Event | olay nesnesi (userId/ip/ua/ts) | `UserLoggedInEvent.php·26-31` |
| Middleware (authz/csrf/origin/rl) | securityEvent üretir | `FileHandler.php·169` (sözleşme) |
| Security (bypass) | logTestBypass | `SecurityHelper.php·42-55` |
| FileHandler | PSR-3 + olay kayıtları + yazım | `FileHandler.php·30` |
| StructuredLogger | JSON satır + error_log | `StructuredLogger.php·7` |
| Redactor | tek maskeleme noktası | `Redactor.php·25` |
| K016-observability | tüketici | ⚠️ (U-1) |

## §14 Tüketim Sürüşü (K017) — BEKLENEN, KANITSIZ

| Adım | Beklenen | Durum |
|---|---|---|
| C-1 | security.log tail'i okunur | ⚠️ KANIT YOK |
| C-2 | Metrik/olaya dönüşür | ⚠️ KANIT YOK |
| C-3 | Uzak depoya/SIEM'e akar | UNKNOWN |

> Beklenen akış tablodur; **kod kanıtı yoktur** → anayasa U-1 ile aynı boşluk.

## §15 Test Sürüşü

| Test | Sürüş | Beklenen |
|---|---|---|
| happy path | event → logAt → dosya | satır var, timestamp var |
| redaction | `token=abc` içeren satır | `[REDACTED]` |
| seviye | debug, minLevel=info | satır yok |
| security akışı | securityEvent | 2 dosyada aynı satır |
| bypass | production env | kayıt yok |

> Test uygulaması QA kapsamı — bu dosya yalnız sürüş tanımı.

## §16 Sürüş Senaryoları

| # | Senaryo | Adımlar | Sonuç |
|---|---|---|---|
| D-1 | Başarılı login | event üret → payload → log | user.logged_in + [AUTH] satırı |
| D-2 | Yanlış parola | authEvent login_failed | warning, parola maskeli |
| D-3 | Yetkisiz rota | securityEvent authz_denied | 2 stream |
| D-4 | Bypass aktif (test) | logTestBypass | errors.log TEST_BYPASS satırı |
| D-5 | Bypass üretimde | isTestBypassActive=false | kayıt yok |
| D-6 | Token sızıntısı | Redactor | [REDACTED] |
| D-7 | Disk dolu | append başarısız | sessiz ⚠️ (U-4) |

## §17 Performans ve Zamanlama

| Faktör | Etki | Kanıt |
|---|---|---|
| İki stream'e ikiz yazım | 2× dosya I/O (satır uzunluğu kadar) | `FileHandler.php·228/234` |
| LOCK_EX | eşzamanlı yazıcı sıralar | `FileHandler.php·251` |
| regex 3'lü redaction | satır başına CPU | `Redactor.php·72-90` |
| seviye eşiği | altı seviyede I/O yok | `FileHandler.php·194` |

## §18 Denetim Sürücüsü (Sürüş Kontrolü)

| # | Soru | Kaynak |
|---|---|---|
| 1 | Her kritik olay bu zincirden geçiyor mu? | §3 |
| 2 | Maskeleme yazım öncesi mi? | §5 W-4 |
| 3 | Append + lock var mı? | §5 W-5 |
| 4 | Yetki reddi security akışında mı? | §6 |
| 5 | Tüketim kanıtı var mı? | §14 — şu an HAYIR |

## §19 Öncelikli Müdahale

| Öncelik | Durum | Müdahale |
|---|---|---|
| HIGH | yazma hatası sessiz (U-4) | fail-safe log + alarm ADR |
| HIGH | K017 tüketimi kanıtsız (U-1) | entegrasyon kanıtı topla |
| MEDIUM | secret_access olayı yok (anayasa U-3) | olay tanımı + üretici |
| MEDIUM | minLevel düşürme senaryosu (U-6) | securityEvent eşiğini sabitle |

## §20 Pseudocode Şablonları (Sürüş)

```text
ŞABLON-1 (temel kayıt)
  olay = üret(kim, ne, ne zaman)
  satır = normalize(olay)          # timestamp/level/module/event/traceId
  satır = Redactor.redact(satır)   # maskeleme YAZIM öncesi
  append(coremusic_php_errors.log, satır, LOCK_EX)
  if olay in SECURITY_SET:
      append(coremusic_php_security.log, satır, LOCK_EX)

ŞABLON-2 (yetki reddi)
  karar = Permission.check(_auth, rota)
  if karar == RED:
      logger.securityEvent('authz_denied', {ip, uri, role, code, reason})
      yanıt = 403
  return yanıt

ŞABLON-3 (bypass aktivasyonu)
  if SecurityHelper.isTestBypassActive(config):
      SecurityHelper.logTestBypass(context, file, line)   # mutlak kayıt
      if bypassConfig boş: pas geç (fail-closed)
```

> Pseudocode'tur; gerçek kod `audit-ornekler.md` §2-§12'de.

## §21 Ortam Sürücüsü (Environment)

| Ortam | Sürüş Notu | Kanıt |
|---|---|---|
| Geliştirme | debug mode → warnings/info split aktif | `FileHandler.php·60/238-244` |
| Test | LoggerFactory reset() ile izole | `LoggerFactory.php·24-27` |
| Production | security stream prod dâhil aktif | `FileHandler.php·230-232` |
| Production | test bypass kapalı (fail-closed) | `SecurityHelper.php·25-27` |
| CI | log davranışı test edilmedi | ⚠️ KANIT YOK |

## §22 Sürüş Riskleri

| # | Risk | Olasılık | Etki | Savunma |
|---|---|---|---|---|
| R-1 | sessiz yazma hatası (disk/izin) | düşük | yüksek | ⚠️ U-4 — fail-safe log ADR |
| R-2 | secret_access kaydı olmayışı | mevcut | yüksek | anayasa U-3 |
| R-3 | sessionId maskesiz sızıntısı | orta | orta | AD-16 — key listesi |
| R-4 | K017 boşluğunda kayıtlar yalnız yerel | mevcut | orta | U-1 entegrasyon |
| R-5 | minLevel düşürmeyle securityEvent kaybı | düşük | orta | U-6 sabit eşiğe |
| R-6 | retention yokluğu → dosya şişmesi | yüksek | düşük | U-1 rotation ADR |

## §23 Bilinmeyenler

| # | Soru | Etiket |
|---|---|---|
| U-1 | K017 tüketim akışı | ⚠️ VERIFICATION REQUIRED |
| U-2 | secret_access olay sürüşü | ⚠️ VERIFICATION REQUIRED |
| U-3 | authz_denied üreten middleware kanıtı | ⚠️ VERIFICATION REQUIRED |
| U-4 | sessiz yazma hatası (fail-safe değil) | UNKNOWN |
| U-5 | JSON encode hata davranışı | UNKNOWN |
| U-6 | securityEvent minLevel altı senaryosu | UNKNOWN |

## §24 Bağlantılar

| Bağ | Yön | Not |
|---|---|---|
| [[audit-anayasa]] | üst | sürüşün kuralları |
| [[audit-ornekler]] | kardeş | her adımın gerçek kodu |
| [[audit-dogrulama]] | kardeş | AD-xx kanıtları |
| [[authorization-ornekler]] | öncesi | D-1 reddi üreten katman |
| [[secrets-kapsam]] | öncesi | sırra erişim kaynağı (⚠️) |
| K016-observability | sonrası | tüketim hedefi (⚠️ kanıt yok) |

## §25 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk yazım — sürüş akışları 7 dosya kanıtı + 6 bilinmeyen |

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-15 |
| Durum | draft |
| Erişim | `.ai/architecture/K009-security/audit/audit-surucu.md` |
| git commit | ATILMADI (subagent yasağı) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
