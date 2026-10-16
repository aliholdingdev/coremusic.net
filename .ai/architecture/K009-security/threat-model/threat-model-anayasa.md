---
node_id: K009-security/threat-model/threat-model-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/threat-model
  - K009-security/security-master
  - K009-security/security-riskler
evidence:
  - "threat-model/index.md §2 (STRIDE → K009 çocuk eşlemesi) · §1 (6 adımlı süreç)"
  - ".ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md (accepted — 3 katmanlı CSRF)"
  - "security-riskler.md §1-§2 (R-01..R-14 STRIDE risk defteri)"
validation:
  - "2026-10-15 — skorlama matrisi 5×5 + 4 bant; tehdit→kontrol→kanıt zinciri 5 adım; modele-alma tetikleyicisi 8 kalem"
open_questions:
  - "DFD (veri akışı diyagramı) diskte yok ⚠️ VERIFICATION REQUIRED (threat-model/index.md §1)"
  - "Tehdit kataloğu periyodik gözden geçirme tarihi belirsiz ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · threat-model · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · rol: mimari (tehdit sınıflandırma, skorlama, kabul kapısı)

## §1 AMAÇ VE KAPSAM

| Alan | Değer |
|---|---|
| node_id | K009-security/threat-model/threat-model-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | tehdit sınıflandırma · etki × olasılık skorlama · tehdit→kontrol→kanıt zinciri · modele-alma sıklığı · kabul öncesi ADR kapısı |
| Kapsam dışı | kontrol UYGULAMASI (K009 çocukları) · risk kaydı (security-riskler) · kimlik modeli (K010) · OS güvenliği (K000) · ağ tehditleri (K014) |
| Sürücü | threat-model-surucu.md (akış) · threat-model-ornekler.md (gerçek yüzey) |

> **Soru:** neyi koruyoruz, kim tehdit ediyor, hangi yüzeyden girer, hangi kontrol durdurur, kanıtı nerede?

## §2 TERİMLER (TR / EN)

| Türkçe | English | Tanım |
|---|---|---|
| Tehdit | Threat | Bir varlığı bozabilecek niyet veya olay |
| Tehdit yüzeyi | Attack surface | Saldırganın erişebileceği kod/konfigürasyon/veri kenarı |
| Varlık | Asset | Korunması gereken şey (oturum, parola hash, API key, log) |
| Güven sınırı | Trust boundary | Güven düzeyinin değiştiği sınır (tarayıcı ↔ sunucu ↔ DB) |
| Zafiyet | Vulnerability | Tehditin gerçekleşmesine izin veren durum |
| Risk | Risk | Olasılık × etki (NIST 800-30 r1 puanı) |
| Kontrol | Control | Tehditin olasılığını veya etkisini düşüren mekanizma |
| Kanıt | Evidence | Kontrolün varlığını gösteren Dosya·Satır |
| Sızıntı | Leak | Yetkisiz bilgi açığa çıkması |
| Fail-closed | Fail-closed | Belirsizlikte ERİŞİMİ REDDEDEN davranış |
| Fail-open | Fail-open | Belirsizlikte erişimi GEÇİREN davranış |
| Kabullenme | Risk acceptance | Kalan riskin yazılı olarak onaylanması |

## §3 TEHDİT SINIFLANDIRMASI (STRIDE benzeri)

> Kaynak: `threat-model/index.md` §2 — STRIDE + attack.mitre.org / CWE eşlemesi (v2 §2.9 satır 327, ad).

| # | Sınıf | Kısaltma | Soru | Bu sınıftaki tipik tehdit |
|---|---|---|---|---|
| 1 | Sahtecilik | S — Spoofing | Kimlik taklidi yapıldı mı? | bypass credential sızıntısı, sahte JWT, tahmin edilebilir API key |
| 2 | Bozulma | T — Tampering | Veri/istek değiştirildi mi? | CSRF, açık redirect, girdi enjeksiyonu, log/şema bozulması |
| 3 | İspatsızlık | R — Repudiation | İşlem sahibi inkâr edebilir mi? | güvenlik olayının loglanmaması |
| 4 | Bilgi sızıntısı | I — Information Disclosure | Sır/gizli veri açığa çıktı mı? | log sızıntısı, cookie hırsızlığı, header/referrer sızıntısı |
| 5 | Hizmet reddi | D — Denial of Service | hizmet erişilemez oldu mu? | brute-force, cache çöküşü → fail-open |
| 6 | Yetki artışı | E — Elevation of Privilege | Yetkisiz güç kazanıldı mı? | CSRF bypass, rol karşılaştırma zaafı, auth atlatma |

### §3.1 Sınıf → K009 Yüzey Eşlemesi

| Sınıf | CoreMusic yüzeyi (dosya) | Kanıt durumu |
|---|---|---|
| S | `shared/src/Middleware/BypassAuthMiddleware.php` · `Security/JwtService.php` | ✅ Dosya·Satır (ornekler §2 Ör-04) |
| T | `shared/src/Middleware/CsrfMiddleware.php` · `Security/ReturnUrlPolicy.php` | ✅ Dosya·Satır (ornekler §2 Ör-03, Ör-06) |
| R | Logger securityEvent (`csrf_invalid` · `BACC 006` · `RATE 005`) | ✅ Dosya·Satır (ornekler §2 Ör-03/Ör-04; log kanıtı dogrulama AD-09) |
| I | `SecurityHeadersMiddleware.php:30` Referrer-Policy (R-10) · session cookie bayrakları `Session/CLAUDE.md:49` (R-09, SameSite=Lax) | ✅ Dosya·Satır — 2026-10-15 düzeltmesi (bkz dogrulama AD-07/AD-08) |
| D | `shared/src/Middleware/RateLimiterMiddleware.php` | ✅ Dosya·Satır (ornekler §2 Ör-05) |
| E | `shared/src/Middleware/PermissionMiddleware.php` | ⚠️ bu dosyada okunmadı → authorization çocuğuna devir (U-5) |

### §3.2 STRIDE DIŞI SINIFLAR (bu anayasada zorunlu)

| # | Sınıf | Neden ayrı | Sınıf karşılığı |
|---|---|---|---|
| 7 | Güvensiz serileştirme (insecure deserialization) | OWASP ayrı tehdit sınıfı — kod çalıştırma yüzeyi | T + E |
| 8 | Açık redirect (open redirect) | phishing zinciri; STRIDE'da daralmaz | T |
| 9 | Fail-open / fail-closed hatası | "yok sayılan güvenlik kontrolü" — tasarım zaafı | D + E |
| 10 | Yanlış yapılandırma (misconfiguration) | kod hatası değil, config zaafı (bypass whitelist, allowlist) | S + E |
| 11 | Bilgi sızdıran hata mesajı (verbose error) | hata yönetimi üzerinden keşif | I |
| 12 | Bağımlılık/zafiyetli bileşen | üçüncü parti CVE yüzeyi | T + E |

## §4 SKORLAMA — ETKİ × OLASILIK

> Yöntem: NIST 800-30 r1 puanlaması (kaynak: `threat-model/index.md` §1 satır 37).
> Ölçüm kuralı: **kanıt yok → olasılık puanı asla 1'in altına indirilemez**; kanıtsız iddia `⚠️ VERIFICATION REQUIRED` kalır.

### §4.1 Etki Ölçeği (Impact — 1..5)

| Puan | Etki | Tanım | CoreMusic karşılığı |
|---|---|---|---|
| 1 | Çok düşük | Tek kullanıcı, geçici | kozmetik hata |
| 2 | Düşük | sınırlı kullanıcı, kurtarılabilir | tek uçta reddedilen istek |
| 3 | Orta | veri/özellik kaybı, geri döndürülebilir | açık redirect, log sızıntısı |
| 4 | Yüksek | kimlik/oturum veya hassas veri etkisi | CSRF ile state-changing, cookie sızıntısı |
| 5 | Kritik | tam hesap ele geçirme, sır ifşası, veri ihlali | bypass auth, parola/anahtar sızıntısı |

### §4.2 Olasılık Ölçeği (Likelihood — 1..5)

| Puan | Olasılık | Tanım | Kanıt karşılığı |
|---|---|---|---|
| 1 | Nadir | saldırgan özel çaba + düşük geri dönüş | savunma kanıtlanmış, test var |
| 2 | Düşük | koşul gerektirir | kısmi kanıt |
| 3 | Olası | standart araçla denenebilir | kanıt eksik veya yama PLANNED |
| 4 | Yüksek | varsayılan davranışta açık | kodda fail-open / kontrol YOK |
| 5 | Neredeyse kesin | her istekte tetiklenir | kontrol hiç yok |

### §4.3 Skor Matrisi (Etki × Olasılık = 1..25)

| Etki \ Olasılık | 1 Nadir | 2 Düşük | 3 Olası | 4 Yüksek | 5 Kesin |
|---|---|---|---|---|---|
| **5 Kritik** | 5 | 10 | 15 | 20 | 25 |
| **4 Yüksek** | 4 | 8 | 12 | 16 | 20 |
| **3 Orta** | 3 | 6 | 9 | 12 | 15 |
| **2 Düşük** | 2 | 4 | 6 | 8 | 10 |
| **1 Çok düşük** | 1 | 2 | 3 | 4 | 5 |

### §4.4 Bant → Öncelik → Aksiyon

| Bant | Skor | Öncelik | Zorunlu aksiyon | Karar mercii |
|---|---|---|---|---|
| Kritik | 15–25 | P0 | kontrol hemen uygulanır veya **üst onayla** kapatılır | Vault Steward + security-engineer |
| Yüksek | 10–14 | P1 | kontrol planlanır, tarih yazılır | security-engineer |
| Orta | 5–9 | P2 | kontrol veya gözlem (log) | backend-architect |
| Düşük | 1–4 | İzle | yalnız kayıt | bu çocuk (tehdit kataloğu) |

| Bağ (risk defteri ile) | |
|---|---|
| `security-riskler.md` §7 | Etki sütunu Low/Medium/High — bu anayasa 1..5 ölçeğine karşılık gelir: 1-2 = Low, 3 = Medium, 4-5 = High |
| `security-riskler.md` §6 | P0/P1/P2 bantları bu §4.4 ile aynı öncelik adlarını taşır |

## §5 TEHDİT → KONTROL → KANIT ZİNCİRİ (zorunlu 5 adım)

| Adım | İşlem | Zorunlu çıktı | Yapılamazsa |
|---|---|---|---|
| Z-1 | Tehdit kaydı aç | tehdit no (T-xx) + sınıf (§3) + yüzey (Dosya) | kayıt yoksa tehdit yok sayılamaz → DUR |
| Z-2 | Varlık + etki belirle | etki puanı (§4.1) + varlık (§11) | etki yoksa skor uydurulur → yasak |
| Z-3 | Olasılığı kanıta bağla | olasılık puanı (§4.2) + kanıt durumu | kanıtsız → olasılık ≥3 (§4 kuralı) |
| Z-4 | Kontrol ata | K009 çocuk adı (§13) + kontrol mekanizması | kontrol yoksa → §8 (risk kaydı / kabul / ADR) |
| Z-5 | Kanıtı yaz | **Dosya·Satır** | kanıt yoksa durum `⚠️ KANIT YOK` — AZALTILMIŞ yazılmaz |

### §5.1 Zincir Örneği (kalıp — adım adım)

```text
T-01 (S)  BypassAuth credential'ı yanlış URL ile auth atlatır
  Z-1 yüzey : shared/src/Middleware/BypassAuthMiddleware.php
  Z-2 etki  : 5 (kimlik tamamen atlanır) · varlık: tüm oturumlar
  Z-3 olas. : 4 (config hatası varsayılan) — kanıt: security-riskler R-01 AÇIK
  Z-4 kontrol: authentication (fail-closed credential) + kod inceleme
  Z-5 kanıt : BypassAuthMiddleware.php·29-31 (3 sabitten biri boşsa [])
  skor      : 5 × 4 = 20 → Kritik / P0
```

| Kural | |
|---|---|
| Zincir kırılırsa | tehdit `⚠️ VERIFICATION REQUIRED` olarak kalır; "kontrol var" DEĞİL |
| Kanıt `Dosya·Satır` değilse | Z-5 sayılmaz (yorum, doküman veya test iddiası tek başına yeterli değildir) |
| Test kanıtı | test DOSYASI varlığı ek kanıttır; davranışın kendisi kod satırıyla gösterilir |

## §6 BAĞLAYICI KURALLAR (anayasa maddeleri)

| Madde | Kural | Yaptırım |
|---|---|---|
| T-01 | Yeni uç, yeni şema veya yeni middleware **modele alınmadan** üretemez (§7) | kod incelemesinde RED |
| T-02 | Her tehdit kaydı §5 zincirinin 5 adımını da taşır | vault review BLOCK |
| T-03 | Skor = etki × olasılık; tek başına "düşük risk" yorumu yazılmaz | revizyon RED |
| T-04 | Kanıtsız tehdit asla `AZALTILMIŞ` yapılmaz | security-riskler §7 kuralıyla ortak |
| T-05 | Kontrol yoksa sessiz geçilmez → §8'e göre risk kaydı veya ADR açılır | BLOCK |
| T-06 | Kabul edilen risk gerekçesiz yazılmaz (ADR numarası zorunlu) | BLOCK |
| T-07 | Bant 15–25 (P0) üst onaysız kapatılamaz | BLOCK |
| T-08 | K009 dışında bir domeine (K010/K000/K014) karar yazılmaz | Layer violation → revert |
| T-09 | Tehdit kataloğu §7 tetikleyicilerinden biri gerçekleşince yeniden taranır | gözden geçirme zorunlu |
| T-10 | STRIDE dışı sınıflar (§3.2) en az aynı ciddiyetle değerlendirilir | review |
| T-11 | Uydurma Dosya·Satır, uydurma CVE, uydurma URL yasak (Zero-Hallucination) | vault kalite kapısı |
| T-12 | Kabullenme bir **durumdur**, kapanış değildir — yeniden tetikleyicide (§7) geri açılır | kapanış yok |

## §7 MODELE-ALMA SIKLIĞI (ne zaman zorunlu)

| # | Tetikleyici | Kapsam | Süre |
|---|---|---|---|
| 1 | Yeni HTTP ucu (route/endpoint) eklendi | o uç + zincirdeki middleware'ler | kod merge'ünden ÖNCE |
| 2 | Yeni DB şeması / tablo / migration | varlıklar + veri akışı | migration merge'ünden ÖNCE |
| 3 | Yeni middleware veya pipeline sırası değişikliği | sıralama etkisi (ADR-010 §1.1 sıra kilidi) | kod merge'ünden ÖNCE |
| 4 | ADR açılışı (güvenlik veya mimari karar) | kararın tehdit yüzeyi | ADR taslağı sırasında |
| 5 | Yeni bağımlılık (composer paketi) | §3.2 sınıf 12 | ekleme öncesi |
| 6 | Auth/yetki/oturum kodu değişikliği | S + E sınıfları | kod merge'ünden ÖNCE |
| 7 | Config değişikliği (bypass, allowlist, proxy) | §3.2 sınıf 10 | deploy öncesi |
| 8 | Olay / pentest bulgusu | ilgili tehdit + komşu sınıflar | bulgu sonrası 1 iş günü |

| Sıklık Kuralı | |
|---|---|
| Tetikleyici yoksa | periyodik gözden geçirme tarihi **⚠️ VERIFICATION REQUIRED** (threat-model/index.md §5 açık soru) |
| Modelleme çıktısı | tehdit kaydı + skor + kontrol ataması + kanıt (§5) |
| Modelleme sahibi | security-engineer · onay: Vault Steward |

## §8 KABUL ÖNCESİ KARAR (ADR kapısı)

> Kural: **kontrol uygulanmadan önce** kalan risk ya azaltılır, ya devredilir, ya da **yazılı olarak kabul edilir**. Kabul = ADR.

| Seçenek | Koşul | Belge | Gerekçe zorunlu mu |
|---|---|---|---|
| Azalt (mitigate) | kontrol uygulanabilir ve skor düşer | kontrol + kanıt | hayır |
| Devret (transfer) | üçüncü taraf/altyapı yükümlü | sözleşme/sınır kaydı | evet |
| Kabul (accept) | skor P2 veya bilinçli tasarım | **ADR (status: accepted)** | **evet — zorunlu** |
| Yok et (avoid) | yüzey kapatılır | tasarım değişikliği kaydı | evet |

### §8.1 ADR Zorunluluk Matrisi

| Durum | ADR gerekli mi? | Kaynak |
|---|---|---|
| Skor 15–25 (P0) kabul edilecekse | **EVET — üst onay + ADR** | §4.4, T-07 |
| Fail-open davranışı bilinçliyse | **EVET** (ör. R-13 → ADR-013, `security-riskler.md` §2) | risk defteri |
| Bypass/whitelist kapsamı değişiyorsa | **EVET** (ADR-008 kapsam kuralı — `shared/AGENTS.md` Yasak-2) | shared/AGENTS.md §4 |
| Kontrol PLANNED ve tarihsizse | Hayır — ama `⚠️ VERIFICATION REQUIRED` + P1 kaydı | §5 Z-5 |
| SKOR ile oynama (olasılığı düşürme) | **EVET — skor değişikliği kararı** | T-03 |

### §8.2 Referans Karar — ADR-010 (CSRF)

| Öğe | Değer |
|---|---|
| Durum | accepted (kullanılabilir — frozen YOK) |
| Tehdit sınıfı | T (Tampering) — CSRF |
| Skor kalıbı | etki 4 × olasılık 4 = 16 → Kritik/P0 (kontrol öncesi) |
| Kontrol | 3 katman: synchronizer token + SameSite + Origin/Referer fail-closed |
| Kanıt | `CsrfMiddleware.php·42` (hash_equals) · `CsrfMiddleware.php·24` (safe method dışı her şey) |
| Kalan boşluk | OriginCheck fail-open → ADR-010 §2.2a katman 3 **PLANNED** |
| Bu anayasadaki rolü | §5 zincirinin kabul edilmiş örneği: tehdit → ADR → kontrol → kanıt |

## §9 YASAKLAR

| # | Yasak | Sınıf |
|---|---|---|
| Y-01 | Kontrol olmadan "risk düşük" yazmak | skorlama |
| Y-02 | Kanıtsız `AZALTILMIŞ` durumu | kanıt |
| Y-03 | Dosya·Satır uydurmak | Zero-Hallucination |
| Y-04 | P0 riski onaysız kapatmak | kabul kapısı |
| Y-05 | Fail-open davranışını "geçici" diye kayıt dışı bırakmak | §8.1 |
| Y-06 | K009 dışında domeine karar/kontrol yazmak | sınır |
| Y-07 | Tehdit modelini "bir kez yaz, unut" şeklinde sürdürmek | §7 |
| Y-08 | Kabul edilmiş riski "kapandı" olarak işaretlemek | T-12 |
| Y-09 | Test iddiasını kod kanıtı yerine koymak | Z-5 |
| Y-10 | ADR metnini değiştirmek (frozen okunur, dokunulmaz) | `.ai/AGENTS.md` §25.3 |

## §10 DOĞRULAMA SORUSU SETİ (her modelleme turunda)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Yeni uç/şema var mı? (§7) | modelleme zorunlu | mevcut katalog yeterli |
| 2 | Her tehdidin sınıfı yazıldı mı? (§3) | devam | sınıf seç → DUR |
| 3 | Etki ve olasılık ayrı puanlandı mı? (§4) | devam | DUR |
| 4 | Skor 15+ mı? | §4.4 P0 + §8 ADR kapısı | bant kontrolü |
| 5 | Kontrol atandı mı? (§13) | Z-5 kanıta geç | §8 seçenekleri |
| 6 | Kanıt Dosya·Satır mı? | durum yazılır | `⚠️ KANIT YOK` |
| 7 | Kabul varsa ADR numarası yazıldı mı? | T-06 sağlanır | BLOCK |
| 8 | Sınır ihlali var mı? (K010/K000/K014) | T-08 → devir | devam |
| 9 | Açık soru/UNKNOWN işaretlendi mi? | defter güncellenir | işaretle |
| 10 | Test bağlantısı kuruldu mu? (security-testing) | Z-5 ek kanıt | plana yaz |

## §11 VARLIK ENVANTERİ (ne korunuyor)

| # | Varlık | Sınıf | Sahip çocuk | Not |
|---|---|---|---|---|
| V-1 | Oturum cookie'si / session ID | I | session-security | `domain=.coremusic.net` geniş yayılım |
| V-2 | Parola hash + pepper (APP_PEPPER) | I | authentication · secrets | pepper sızarsa tüm parolalar risk altında |
| V-3 | JWT private key (private.pem) | I · S | encryption · secrets | RS256 imza |
| V-4 | API key (ham) | I · S | secrets | hash + prefix saklama |
| V-5 | CSRF token (session) | T | session-security | `CsrfMiddleware` bağlamı |
| V-6 | Bypass credential sabitleri | S · E | authentication | fail-closed (§14) |
| V-7 | Yetki tablosu / permissions | E | authorization | `_auth` üzerinden |
| V-8 | Güvenlik logları (securityEvent) | R | audit | redaction zorunlu |
| V-9 | Origin/CORS allowlist config | T · E | threat-model (bu çocuk) | yanlış liste = yetkisiz origin |
| V-10 | Return URL (redirect hedefi) | T | session-security | açık redirect → phishing |

## §12 GÜVEN SINIRLARI (DFD yok — çizim olmadan tablo)

> `threat-model/index.md` §1: "Yüzeyler ve güven sınırları çizilir → DFD (yok ⚠️)"
> **DFD diskte yok → ⚠️ VERIFICATION REQUIRED.** Yerine tablo kullanılır; diyagram üretilmeden "tamamlandı" denmez.

| # | Sınır | Güvenilir taraf | Güvensiz taraf | Kontrol |
|---|---|---|---|---|
| S-1 | Tarayıcı ↔ Uygulama | session + CSRF | istemci girdisi | Csrf · Validation |
| S-2 | Uygulama ↔ Harici origin | allowlist | üçüncü taraf sayfa | OriginCheck · Cors |
| S-3 | Uygulama ↔ DB | parametreli PDO | ham SQL | Validation · repositories |
| S-4 | Hizmet ↔ Hizmet (subdomain) | ortak session cookie | alt alan adı | CORS allowlist · ReturnUrlPolicy |
| S-5 | Kod ↔ Config | güvenilir config | yanlış/yazılı config | fail-closed (bypass, rate-limit) |
| S-6 | Uygulama ↔ Log | redacted log | sır/parola | redaction testi |

## §13 TEHDİT → K009 ÇOCUK KONTROL EŞLEMESİ

| Sınıf | Tehdit tipi | Kontrol çocuğu (K009) | Kanıt kaynağı |
|---|---|---|---|
| S | sahte kimlik / bypass | `authentication` | ornekler §2 Ör-04 · dogrulama AD-04 |
| S | sahte anahtar/JWT | `secrets` · `encryption` | security-riskler R-02/R-03 |
| T | CSRF | `session-security` (+ ADR-010) | ornekler §2 Ör-03 |
| T | açık redirect | `session-security` | ornekler §2 Ör-06 |
| T | enjeksiyon | `input-validation` | security-riskler R-06 |
| R | loglanmayan olay | `audit` | security-riskler R-07 |
| I | sır/jeton sızıntısı | `secrets` · `session-security` | security-riskler R-08/R-11 |
| D | brute-force | `rate-limiting` | ornekler §2 Ör-05 · dogrulama AD-05 |
| D | fail-open | `rate-limiting` | security-riskler R-13 (KABUL) |
| E | yetki aşımı | `authorization` | security-riskler R-14 |
| §3.2-7 | güvensiz deser. | `input-validation` | web-arastirma §1 (OWASP Deserialization) |
| §3.2-10 | yanlış config | bu çocuk (tehdit kataloğu) | ornekler §2 Ör-01, Ör-02 |

## §14 TEHDİT KAYDI ŞABLONU (zorunlu alanlar)

| Alan | Zorunlu | Örnek |
|---|---|---|
| tehdit_no | evet | T-01 |
| sınıf | evet | S (Spoofing) |
| yüzey (Dosya) | evet | `shared/src/Middleware/BypassAuthMiddleware.php` |
| varlık | evet | V-6 bypass credential |
| etki (1-5) | evet | 5 |
| olasılık (1-5) + gerekçe | evet | 4 (config hatası varsayılanı) |
| skor | evet | 20 → P0 |
| kontrol (K009 çocuk) | evet | authentication |
| kanıt (Dosya·Satır) | evet | `BypassAuthMiddleware.php·29-31` |
| durum | evet | AÇIK / AZALTILMIŞ / KABUL / ⚠️ |
| karar (kabul ise) | koşullu | ADR-xxx |

## §15 BİLMEMEYENLER DEFTERİ

| # | Soru | Etiket |
|---|---|---|
| U-1 | DFD (veri akışı diyagramı) diskte yok | ⚠️ VERIFICATION REQUIRED |
| U-2 | Periyodik gözden geçirme tarihi belirsiz | ⚠️ VERIFICATION REQUIRED |
| U-3 | Etki/olasılık puanları bireysel mi, ekip onaylı mı? | UNKNOWN |
| U-4 | Tehdit kataloğu hangi depoda tutuluyor (dosya listesi)? | UNKNOWN |
| U-5 | PermissionMiddleware yüzeyi bu dosyada okunmadı | ⚠️ VERIFICATION REQUIRED |
| U-6 | Güvensiz serileştirme yüzeyi repo'da var mı? | UNKNOWN (web-arastirma §2) |
| U-7 | PermissionMiddleware 2026-10-15'te sibling dosyalarda okundu (ornekler §2 Ör-07 · dogrulama §1 AD-12) — U-5'teki "bu dosyada okunmadı" iddiası bu anayasa için geçerliliğini korur | not (U-5 kapanmaz) |

## §16 İLİŞKİLİ DÜĞÜMLER

| Düğüm | İlişki |
|---|---|
| `threat-model/index.md` | süreç, STRIDE eşlemesi, açık sorular |
| `threat-model-surucu.md` | tehdit → risk → kontrol akışı |
| `threat-model-ornekler.md` | gerçek yüzey kanıtları (Ör-01..Ör-12) |
| `threat-model-kapsam.md` | kapsam sınırı — içeri/dışarı matrisi (S-1..S-10) |
| `threat-model-dogrulama.md` | doğrulama kayıtları AD-01..AD-17 + bulgular B-1..B-3 |
| `threat-model-bilesenler.md` | kapı/karar/kanıt bileşen envanteri (B-01..B-14) |
| `threat-model-web-arastirma.md` | kaynak × iddia × durum matrisi + erişim kaydı |
| `security-riskler.md` | risk kaydı (tekrar değil **bağ**) |
| `security-master.md` | domen anayasası |
| ADR-010 (CSRF) | kabul edilmiş tehdit kararı |

## §17 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — sınıflandırma, skorlama, zincir, tetikleyiciler, ADR kapısı | Vault Steward (subagent) |

## §18 SÜRÜM VE ERİŞİM

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Otorite | Bayram Ali / Vault Steward |
| Mod | Red Team · Human Mode · Truth Mode |
| Durum | draft (U-1..U-6 açık) |
| Erişim | `.ai/architecture/K009-security/threat-model/threat-model-anayasa.md` |

## § BAĞLANTILAR

| Bağ | Ne verir |
|---|---|
| [[threat-model/index]] | süreç · STRIDE eşlemesi · açık sorular |
| [[threat-model-surucu]] | tehdit → risk → kontrol sürüşü |
| [[threat-model-ornekler]] | gerçek yüzey tehdit örnekleri (Dosya·Satır) |
| [[security-master]] | domen anayasası |
| [[security-riskler]] | R-01..R-14 risk defteri (bağ — tekrar değil) |
| [[security-kapsam]] | domen kapsam sınırı |
| [[authentication-kapsam]] | auth yüzeyi kapsamı |
| [[authorization-kapsam]] | yetki tehditlerinin sahibi |
| [[session-security-kapsam]] | oturum/CSRF tehditlerinin sahibi |
| [[rate-limiting-kapsam]] | DoS / fail-open tehditlerinin sahibi |
| [[encryption-kapsam]] · [[secrets-kapsam]] | sır ve kripto tehditlerinin sahibi |
| ADR-010-csrf-protection-strategy | kabul edilmiş CSRF kararı (§8.2) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
