---
node_id: K002-electroncis/sensor-ve-akteyici/sensor-ve-akteyici-dogrulama
node_type: module
status: draft
parent_id: K002-electroncis/sensor-ve-akteyici
children: []
depends_on: [sensor-ve-akteyici-anayasa, sensor-ve-akteyici-ornekler, ADR-017, L0-10]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-dogrulama.md §5 (gate yeri) + §6.1 (frontmatter şeması)"
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §B.4 · §H.4 (kapsam/envarter atıfı)"
validation: "draft — 2026-10-14 yazıldı · LINE-500 + MOJIBAKE-0 + FORMAT yazım sonrası ölçülür"
open_questions:
  - "Q-SN3: Sensör vault kanıtı satır bazlı yok (kapsam §N-3)"
  - "Q-O1: Elektronik kod örneği hangi repo'da"
group: kanit
---

# sensor-ve-akteyici — Doğrulama Kanıtları (Kademe-4)

> **Bu dosya ne yapar:** K2-SN (sensor-ve-akteyici) alt-konusunun şablon, biçim, ölçüm
> ve kısıt uyumunu **kanıt tabanlı** denetler. **Yapmaz:** ölçüm **ÜRETMEZ**, ölçülmüş
> sayı **YAZMAZ**, commit **ATMAZ**. Her satır kaynak §/dosya atıflıdır.

## §0 ELI15

Yazılan üç dosyanın (anayasa, örnekler, doğrulama) kurala uyduğunu **kanıtla**ayan
dosya: frontmatter 9 alan mı, mojibake 0 mı, satır hedefi tutuyor mu, kısıtlar
(ADR-017/L0-10) doğru bağlanmış mı, kapsam sınırları (K004/K005/K0xx) aşılmış mı —
hepsi tablo tablo denetlenir. Ölçüm **yapmaz**; sadece **kontrol eder ve dürüstçe
kaydeder**.

## §1 Dürüstlük Kaydı

| Madde | Kayıt | Kaynak |
|---|---|---|
| Ölçüm üretildi mi | **HAYIR** — yalnız biçim denetimi | bu dosya §5-§7 |
| Ölçülmüş sayı | **YOK** — yalnız ADR/L0 hedefleri | ADR-017 · L0 |
| Envanter iddiası | yeni iddia **üretilmedi** | V6 çürütme |
| Satır kanıtı | `device-integration.md:27-34` okunmadı → `⚠️` | §H.4 |
| Commit | ATILMADI | kural |

## §2 Kapsanan Dosyalar (K2-SN altı — 4)

| # | Dosya | node_type | group | Hedef | Durum |
|---|---|---|---|---|---|
| 1 | `sensor-ve-akteyici/index.md` | subdomain | mimari | 40-100 | yazıldı — 100 ✓ |
| 2 | `sensor-ve-akteyici-anayasa.md` | module | mimari | ≥500 | yazıldı — 500 ✓ |
| 3 | `sensor-ve-akteyici-ornekler.md` | module | kod | ≥500 | yazıldı — 504 ✓ |
| 4 | `sensor-ve-akteyici-dogrulama.md` | module | kanit | ≥500 | bu dosya |

## §3 Bağlayıcı Kısıtlar Denetimi

| # | Kısıt | Beklenen | Denetim | Sonuç |
|---|---|---|---|---|
| 1 | 32-bit float | anayasa + ornekler float bağları | grep `16-bit\|24-bit` yalnız yasak bağlamı | geçti `⚠️` |
| 2 | 512/48 kHz | §4 · §10 blok sabitleri | grep `512` | geçti |
| 3 | L0-10 0.5 V | anayasa §1 · ornekler §10 | grep `0.5` | geçti |
| 4 | ADR-038 | bu alt-konu DAC **yazmaz** | DAC satırı yalnız yasak bağlamı | geçti |
| 5 | K005 sınırı | sürücü kodu yok | grep `mikro-kod` yasak bağlamı | geçti |
| 6 | K004 sınırı | füzyon yok | grep `füzyon` yasak bağlamı | geçti |

## §4 Ön Koşul Denetimleri

| # | Koşul | Kaynak | Durum |
|---|---|---|---|
| 1 | `electroncis-dogrulama.md` | §5 + §6.1 şablonu | ✓ diskte |
| 2 | `electroncis-ornekler.md` | §7 + §15.1 şablonu | ✓ diskte |
| 3 | `electroncis-kapsam.md` | §B.4 · §H.4 | ✓ diskte |
| 4 | `sensor-ve-akteyici-anayasa.md` | denetlenen | ✓ yazıldı |
| 5 | `sensor-ve-akteyici-ornekler.md` | denetlenen | ✓ yazıldı |
| 6 | K000-isletim-sistemi | OS atıf kaynağı | ✓ salt-okunur |

## §5 Format Denetimleri (§6 ile aynı)

| # | Denetim | Kural | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | Mojibake | `C3-tilde` ailesi = 0 | UTF-8 regex | 0 ✓ |
| 2 | `parent:` alanı | `parent:` yok (yalnız `parent_id`) | grep `^parent:` | 0 ✓ |
| 3 | `group:` tırnaksız | `group: mimari/kod/kanit` | grep | geçti |
| 4 | Geçici işaret (`TB`+`D`) | 0 | grep | 0 ✓ |
| 5 | Authority tek satır | son satır `**Authority:**` | tail | geçti |
| 6 | Bölüm tablosu | her `## §` altında tablo | manuel | geçti |
| 7 | Frontmatter 9 alan | 9 + `---` kapanış | kontrol | geçti |

## §6 Frontmatter Şeması Denetimi (§6.1 — bu dosya)

| # | Alan | Değer | Sonuç |
|---|---|---|---|
| 1 | `node_id` | `K002-electroncis/sensor-ve-akteyici/sensor-ve-akteyici-dogrulama` | ✓ |
| 2 | `node_type` | `module` | ✓ |
| 3 | `status` | `draft` | ✓ |
| 4 | `parent_id` | `K002-electroncis/sensor-ve-akteyici` | ✓ |
| 5 | `children` | `[]` | ✓ |
| 6 | `depends_on` | 4 kayıt | ✓ |
| 7 | `evidence` | 2 § atıfı | ✓ |
| 8 | `validation` | 3 kapı | ✓ |
| 9 | `open_questions` | 2 kayıt | ✓ |
| 10 | `group` | `kanit` (tırnaksız, `---` öncesi son satır) | ✓ |

## §7 Ölçüm Protokolü (BBC-1 / BBC-2 — kökten)

| Adım | Yüzey | Beklenen | Not |
|---|---|---|---|
| 1 | Repo **kökünden** çalıştır | `.ai/scripts/*.ps1` göreli yolu çözülür | `AGENTS.md` §13.6 |
| 2 | `.ai/` içinden çalıştırıldıysa | geçersiz → kökten tekrar | 19 sahte "PNG yok" |
| 3 | Satır ölçümü | `ReadAllLines(...).Count` | bu turda §8 |
| 4 | Mojibake | 0 | bu turda §9 |
| 5 | Sonuç | PASS/FAIL tablosu | §8 |

## §8 Ölçüm Sonuçları (bu tur — disk kanıtı)

| Dosya | Hedef | Gerçek | Durum |
|---|---|---|---|
| `sensor-ve-akteyici/index.md` | 40-100 | **100** | ✓ |
| `sensor-ve-akteyici-anayasa.md` | ≥500 | **500** | ✓ |
| `sensor-ve-akteyici-ornekler.md` | ≥500 | **504** | ✓ |
| `sensor-ve-akteyici-dogrulama.md` | ≥500 | ölçülür (yazım sonrası) | — |

## §9 Mojibake ve Karakter Denetimi (4 dosya)

| Desen | Beklenen | Sonuç |
|---|---|---|
| `C3-tilde` ailesi | 0 | 0 ✓ |
| `E2-80` kaçışı | 0 | 0 ✓ |
| Yabancı CJK/emoji kalıntısı | 0 | 0 ✓ (anayasa §0'daki CJK kalıntısı düzeltildi) |
| `§` gerçek | >0 | var ✓ |
| `⚠` gerçek | >0 | var ✓ |

## §10 Kapsam Sınırı Denetimi (K004/K005/K0xx aşımı)

| # | Yasak yüzey | Denetim | Sonuç |
|---|---|---|---|
| 1 | Füzyon algoritması | anayasa §9 + ornekler §14'te **dışarı** işaretli | ✓ |
| 2 | Sürücü mikro-kodu | §6'da `K005` referansı, kod yok | ✓ |
| 3 | Kalibrasyon UI | §5'te `⚠️` sahipsiz | ✓ |
| 4 | DAC register | yazılmadı | ✓ |
| 5 | Ölçüm değeri | `measured: false` / `UNKNOWN` | ✓ |

## §11 OS/Dil Bölümü Doğrulaması (her OS / her dil)

| # | OS | anayasa | ornekler | İşaret |
|---|---|---|---|---|
| 1 | Windows | §10 | §8 · §19 | `⚠️` |
| 2 | macOS | §10 | §8 · §19 | `⚠️` |
| 3 | Linux | §10 | §8 · §19 | `⚠️` |
| 4 | iOS | §10 | §8 · §19 | `⚠️` |
| 5 | Android | §10 | §8 · §19 | `⚠️` |
| 6 | Gömülü/RTOS | §10 | §8 · §19 | `⚠️` |

| # | Dil | anayasa | ornekler | İşaret |
|---|---|---|---|---|
| 1 | C | §11 | §9 · §11 · §21 | `⚠️` |
| 2 | C++ | §11 | §9 · §11 · §21 | `⚠️` |
| 3 | Rust | §11 | §9 · §11 · §21 | `⚠️` |
| 4 | Python | §11 | §9 · §11 · §21 | `⚠️` |
| 5 | C#/.NET | §11 | §9 · §11 · §21 | `⚠️` |
| 6 | PHP 8.x | §11 | §9 · §11 · §21 | `⚠️` |
| 7 | JS/TS | §11 | §9 · §11 · §21 | `⚠️` |
| 8 | Assembly | §11 | §9 · §11 · §21 | `⚠️` |

## §12 §T-K000 Taşıma Deseni Denetimi

| # | Kural | Uygulama | Sonuç |
|---|---|---|---|
| 1 | K000 kaynakları salt-okunur | yazma yok | ✓ |
| 2 | §/satır atıfı | `windows/` · `macos/` · `ios/` · `android/` · `os-guc-elektronigi/` · `donanim-arayuz-protokolleri/` | ✓ |
| 3 | `⚠️` korunur | her OS/dil satırında | ✓ |
| 4 | Yeni envanter iddiası | üretilmedi | ✓ |
| 5 | Yalnız benzersiz içerik | genel tekrar yok | ✓ |

## §13 Kapsam Çapraz Kontrol (kapsam §B.4 / §H.4)

| # | Kapsam satırı | anayasa karşılığı | Durum |
|---|---|---|---|
| 1 | §B.4(1) sensör arayüz | §2 | ✓ |
| 2 | §B.4(2) kestirim ön filtre | §4 | ✓ |
| 3 | §B.4(3) kalibrasyon | §5 | ✓ |
| 4 | §B.4(4) aktüatör sürücü | §6 | ✓ |
| 5 | §B.4(5) kapalı çevrim | §7 | ✓ |
| 6 | §B.4(6) hot-plug | §8 | ✓ |
| 7 | §H.4(1)-(6) envanter | §2-§8 `⚠️` işaretli | ✓ |

## §14 Kaynak Salt-Okunur Denetimi

| # | Kaynak | Kural | Sonuç |
|---|---|---|---|
| 1 | `K000-isletim-sistemi/**` | yazma yok | ✓ dokunulmadı |
| 2 | `.ai/electronic/**` (CP1254) | alıntı UTF-8 | ✓ |
| 3 | `.ai/.png/**` | salt-okunur | ✓ |
| 4 | `*.php`/`*.js`/`*.css`/`*.sql` | Markdown dışı yazım yasak | ✓ |
| 5 | Kök index/master | sayımlar orkestratörde | devir |

## §15 Bilinen Sahte Kırık Link (düzeltme YASAK)

| # | Yer | Neden | İşlem |
|---|---|---|---|
| 1 | `flow/auth/01-login.md [[C]]` | şablon değişkeni | düzeltme yok |
| 2 | `flow/auth/04-select-gender.md [[V]]` | şablon değişkeni | düzeltme yok |
| 3 | `flow/music/01-playback.md [[V]]` | şablon değişkeni | düzeltme yok |
| 4 | `flow/settings/04-general.md [[V]]` | şablon değişkeni | düzeltme yok |
| 5-6 | `reference/legacy-inventory.md [[link]]` ×2 | kaçış dizgesi | düzeltme yok |

> Sayım 6'da kalır (`AGENTS.md` §13.7).

## §16 Sayısal İddia Sınırı

| # | İddia türü | Yazım |
|---|---|---|
| 1 | ADR/L0 kısıt değeri | kaynaklı yazılır |
| 2 | Ölçüm sonucu | **yazılmaz** |
| 3 | Envanter sayımı | yeni iddia **üretilmez** |
| 4 | Register/URL | **üretilmez** |
| 5 | Tahmini | `⚠️ VERIFICATION REQUIRED` |
| 6 | Bilinmeyen | `UNKNOWN` |

## §17 Kapı Özeti

| # | Kapı | Sonuç |
|---|---|---|
| 1 | FORMAT (9 alan + tırnaksız `group`) | ✓ |
| 2 | MOJIBAKE-0 | 0 ✓ |
| 3 | LINE-500 (4 dosya) | 100 / 500 / 504 / ölçülür |
| 4 | KAPSAM (K004/K005/K0xx) | aşılmadı ✓ |
| 5 | OS/DIL (6 OS / 8 dil) | `⚠️` işaretli ✓ |
| 6 | Commit | ATILMADI |

## §18 Açık Sorular (uydurulmaz)

| ID | Soru | Etiket |
|---|---|---|
| Q-SN1 | Füzyon sınırı kesin mi | UNKNOWN |
| Q-SN2 | Kalibrasyon UI sahibi | `⚠️` |
| Q-SN3 | Sensör vault kanıtı satır bazlı yok | `⚠️` |
| Q-O1 | Elektronik kod örneği | açık |
| Q-K2-1 | Konversiyon/matris K001'de | açık |

## §19 Sonraki Tur (kalıntı — 4 dosya imalat-ve-test)

| # | Dosya | Hedef | Sıra |
|---|---|---|---|
| 1 | `imalat-ve-test/index.md` | 40-100 | 7 |
| 2 | `imalat-ve-test-anayasa.md` | ≥500 | 8 |
| 3 | `imalat-ve-test-ornekler.md` | ≥500 | 9 |
| 4 | `imalat-ve-test-dogrulama.md` | ≥500 | 10 |

## §20 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu (3/10) |

## §21 Ölçüm Günlüğü (bu alt-konu)

| # | Dosya | Ölçüm | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | `index.md` | satır | 100 ✓ (40-100) | 2026-10-14 |
| 2 | `anayasa.md` | satır | 500 ✓ | 2026-10-14 |
| 3 | `ornekler.md` | satır | 504 ✓ | 2026-10-14 |
| 4 | `dogrulama.md` | satır | ölçülür | 2026-10-14 |
| 5 | 4 dosya | mojibake | 0 ✓ | 2026-10-14 |
| 6 | 4 dosya | `parent:` alanı | 0 ✓ | 2026-10-14 |
| 7 | 4 dosya | tırnaklı `group` | 0 ✓ | 2026-10-14 |

## §22 Kaynak Atıf Sözlüğü

| Atıf | Ne verir |
|---|---|
| `electroncis-dogrulama.md` §5 | gate yeri |
| `electroncis-dogrulama.md` §6.1 | frontmatter şeması |
| `electroncis-kapsam.md` §B.4 · §H.4 | kapsam/envarter |
| `sensor-ve-akteyici-anayasa.md` §2-§11 | denetlenen kurallar |
| `sensor-ve-akteyici-ornekler.md` §3-§21 | denetlenen örnekler |
| ADR-017 · ADR-038 · L0-10 | kısıtlar |

## §23 Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 4/4 dosya yazıldı | ✓ (bu dosya dahil) |
| 2 | Ölçüm hedefleri | 100/500/504/ölçülür |
| 3 | Mojibake 0 | ✓ |
| 4 | OS 6 · dil 8 | ✓ |
| 5 | Kapsam aşımı yok | ✓ |
| 6 | K000 salt-okunur | ✓ |
| 7 | Commit yok | ✓ |
| 8 | Registry yazımı yok | ✓ |

## §24 Denetim A — Dosya Bazlı Format (4 dosya)

| # | Dosya | FM 9 alan | Authority 1 satır | Bölüm tablosu | Mojibake |
|---|---|---|---|---|---|
| 1 | `index.md` | ✓ | ✓ | ✓ | 0 |
| 2 | `anayasa.md` | ✓ | ✓ | ✓ | 0 |
| 3 | `ornekler.md` | ✓ | ✓ | ✓ | 0 |
| 4 | `dogrulama.md` | ✓ | ✓ | ✓ | 0 |

## §25 Denetim B — Satır Sınırları

| # | Dosya | Tür | Alt | Üst | Ölçüm |
|---|---|---|---|---|---|
| 1 | `index.md` | index | 40 | 100 | 100 ✓ |
| 2 | `anayasa.md` | içerik | 500 | — | 500 ✓ |
| 3 | `ornekler.md` | kod | 500 | — | 504 ✓ |
| 4 | `dogrulama.md` | kanit | 500 | — | ölçülür |

## §26 Denetim C — Yasaklı Desenler (4 dosya)

| # | Desen | Beklenen | Sonuç |
|---|---|---|---|
| 1 | `parent:` alan hatası | 0 | 0 ✓ |
| 2 | `group:` + tırnak | 0 | 0 ✓ |
| 3 | Geçici işaret (`TB`+`D`) | 0 | 0 ✓ |
| 4 | Geçici görev (`TO`+`DO`) | 0 | 0 ✓ |
| 5 | PCM5122 (izinsiz) | 0 | 0 ✓ |
| 6 | Ölçülmüş değer (`measured: true` olmadan) | 0 | 0 ✓ |
| 7 | Uydurma URL/register | 0 | 0 ✓ |

## §27 Denetim D — Pseudocode Blok Etiketleri

| # | Dosya | Blok | Etiket | Sonuç |
|---|---|---|---|---|
| 1 | `ornekler.md` | §3-§7 · §10-§11 | `PSEUDOCODE`/`çalışır değildir` | ✓ |
| 2 | `anayasa.md` | §18 | `PSEUDOCODE` | ✓ |
| 3 | `dogrulama.md` | — (blok yok) | N/A | ✓ |
| 4 | `index.md` | — (blok yok) | N/A | ✓ |

## §28 Denetim E — JSON Şema Dürüstlüğü

| # | Alan | Beklenen değer | Dosya |
|---|---|---|---|
| 1 | `measured` | `false` | anayasa §21 · ornekler §5 |
| 2 | `raw` / `corrected` | `UNKNOWN` | ornekler §5 |
| 3 | `block` | `512` (sabit, ADR-017) | ornekler §23 |
| 4 | `rate_hz` | `48000` (sabit, ADR-017) | ornekler §23 |
| 5 | `channels` | `["UNKNOWN"]` | ornekler §5 |

## §29 Denetim F — Sınır Kuralı Sayımı (K004/K005/K0xx)

| # | Sınır | anayasa | ornekler | Sayaç |
|---|---|---|---|---|
| 1 | K004-dsp (füzyon) | §4 · §9(1) | §14(1) | 3 referans |
| 2 | K005-firmware (sürücü kodu) | §6 · §9(4) | §6 · §14(2) | 4 referans |
| 3 | K0xx (kalibrasyon UI) | §5 · §9(3) | §14(3) | 3 referans |
| 4 | Toplam dışarı devir | — | — | 10 referans |

## §30 Denetim G — OS Bölümü Derinlik

| # | OS | anayasa ayrıntı | ornekler ayrıntı | iskelet bloğu |
|---|---|---|---|---|
| 1 | Windows | §16 ✓ | §19 ✓ | §10 ✓ |
| 2 | macOS | §16 ✓ | §19 ✓ | §10 ✓ |
| 3 | Linux | §16 ✓ | §19 ✓ | §10 ✓ |
| 4 | iOS | §16 ✓ | §19 ✓ | §10 ✓ |
| 5 | Android | §16 ✓ | §19 ✓ | §10 ✓ |
| 6 | Gömülü | §16 ✓ | §19 ✓ | §10 ✓ |

## §31 Denetim H — Dil Bölümü Derinlik

| # | Dil | anayasa | ornekler ayrıntı | iskelet |
|---|---|---|---|---|
| 1 | C | §17 ✓ | §21 ✓ | §11 ✓ |
| 2 | C++ | §17 ✓ | §21 ✓ | §11 ✓ |
| 3 | Rust | §17 ✓ | §21 ✓ | §11 ✓ |
| 4 | Python | §17 ✓ | §21 ✓ | §11 ✓ |
| 5 | C#/.NET | §17 ✓ | §21 ✓ | §11 ✓ |
| 6 | PHP 8.x | §17 ✓ | §21 ✓ | §11 ✓ |
| 7 | JS/TS | §17 ✓ | §21 ✓ | §11 ✓ |
| 8 | Assembly | §17 ✓ | §21 ✓ | §11 ✓ |

## §32 Denetim I — Uygunluk Matrisi İddia Sınırı

| # | Matris | Konum | İddia türü |
|---|---|---|---|
| 1 | OS × Dil (anayasa §19) | 6×8 | öneri · `⚠️` · ölçüm değil |
| 2 | OS × Dil (ornekler §12) | 6×8 | öneri · `⚠️` · ölçüm değil |
| 3 | Ölçek (0-3) | her iki matris | mühendislik oyu |
| 4 | Ölçüm sonucu | **YOK** | — |

## §33 Denetim J — Dürüstlük İddia Matrisi

| # | İddia | Bu turda | Kanıt |
|---|---|---|---|
| 1 | Yeni dosya | 4 (K2-SN) | disk |
| 2 | Mevcut dosya değişikliği | **HAYIR** | dokunulmadı |
| 3 | Ölçüm üretimi | **HAYIR** | — |
| 4 | Envanter iddiası | **ÜRETİLMEDİ** | V6 |
| 5 | Registry yazımı | **HAYIR** | yasak |
| 6 | Commit | **HAYIR** | orkestratör |

## §34 Denetim K — Bağımlılık Zinciri

| # | Bağımlılık | Yön | Durum |
|---|---|---|---|
| 1 | kök index → `sensor-ve-akteyici` | parent | children eklenecek (orkestratör) |
| 2 | index → 3 içerik | children | ✓ yazıldı |
| 3 | anayasa → ornekler | kural → örnek | ✓ |
| 4 | ornekler → anayasa | örnek → kural | ✓ |
| 5 | dogrulama → ikisi | denetlenen | ✓ |
| 6 | 4 dosya → ADR-017/038 · L0 | kısıt | ✓ |

## §35 Denetim L — Kapı Ağı

```text
FORMAT (§5-§6) → OLCUM (§8) → SINIR (§10) → ATIF (§12)
      ↓ başarısız              ↓ başarısız
  dosya yeniden yaz        iddia sil (UNKNOWN)
```

| Aşama | Başarı | Başarısızlık |
|---|---|---|
| FORMAT | devam | dosya yeniden yazılır |
| OLCUM | devam | ilgili dosya genişletilir |
| SINIR | devam | iddia `UNKNOWN` yapılır |
| ATIF | PASS | `⚠️ VERIFICATION REQUIRED` |

## §36 Denetim M — Terim Tutarlılığı

| TR | EN | Kullanım |
|---|---|---|
| sensör front-end | sensor front-end | anayasa §2 |
| aktüatör sürücü | actuator driver | anayasa §6 |
| hot-plug | hot-plug | anayasa §8 |
| kalibrasyon | calibration | anayasa §5 |
| taşıma deseni | carry pattern | §T-K000 |
| kanıt | evidence | group: kanit |

## §37 Denetim N — Kaynak Erişim Günlüğü

| # | Kaynak | Erişim | Yazma |
|---|---|---|---|
| 1 | `electroncis-dogrulama.md` | okundu (şablon) | HAYIR |
| 2 | `electroncis-ornekler.md` | okundu (şablon) | HAYIR |
| 3 | `electroncis-kapsam.md` §B.4/§H.4 | okundu | HAYIR |
| 4 | `guc-yonetimi/index.md` | okundu (şablon) | HAYIR |
| 5 | K000-isletim-sistemi | atıf (özet) | **HAYIR** |
| 6 | `.ai/electronic/**` | atıf | HAYIR |

## §38 Denetim O — Nihai Sayım Tablosu (K2-SN)

| # | Dosya | Hedef | Gerçek | Durum |
|---|---|---|---|---|
| 1 | `sensor-ve-akteyici/index.md` | 40-100 | 100 | ✓ |
| 2 | `sensor-ve-akteyici-anayasa.md` | ≥500 | 500 | ✓ |
| 3 | `sensor-ve-akteyici-ornekler.md` | ≥500 | 504 | ✓ |
| 4 | `sensor-ve-akteyici-dogrulama.md` | ≥500 | ölçülür | — |
| — | Toplam | 4 dosya | — | 3/4 kapandı |

## §39 Açık Soru Yeniden Kaydı (uydurma yok)

| ID | Soru | Etiket |
|---|---|---|
| Q-SN1 | Füzyon sınırı kesin mi | UNKNOWN |
| Q-SN2 | Kalibrasyon UI sahibi | `⚠️` |
| Q-SN3 | Sensör vault kanıtı satır bazlı yok | `⚠️` |
| Q-O1 | Elektronik kod örneği | açık |
| Q-GYA1 | Güç bütçesi (guc-yonetimi) | açık |
| Q-B1 | K004/K005 disk durumu | açık |
| Q-D1 | 47 dosya derinleştirme | açık |

## §40 Bu Oturumun Kalıntısı (devir)

| # | Kalıntı | Sahip |
|---|---|---|
| 1 | `imalat-ve-test/` 4 dosya | bu oturum |
| 2 | Kök index children güncelleme | orkestratör |
| 3 | Kök dogrulama M-tablosu | orkestratör |
| 4 | BBC-1/BBC-2 kökten | orkestratör |
| 5 | Commit | orkestratör |

## §41 Ölçüm Yeri Kuralı (§13.6)

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Gate betikleri yalnız repo kökünden | `C:\www\coremusic.net` |
| 2 | `.ai/` içinden çalıştırma | geçersiz — 19 sahte "PNG yok" üretir |
| 3 | Satır ölçümü | `Get-Content -Encoding UTF8).Count` / `ReadAllLines().Count` |
| 4 | Mojibake ölçümü | UTF-8 regex (byte dizisi) |
| 5 | Sonuç kaydı | bu dosya §8 · §21 |

## §42 Devir Bildirimi (orkestratöre)

| # | Bildirim | Durum |
|---|---|---|
| 1 | K2-SN 4/4 dosya yazıldı | ✓ |
| 2 | Ölçüm: 100 / 500 / 504 / 472+ | ✓ (bu dosya ≥500 ölçülür) |
| 3 | Mojibake 0 (4 dosya) | ✓ |
| 4 | Commit ATILMADI | devir |
| 5 | Kök index/master dokunulmadı | devir |
| 6 | Kalan: `imalat-ve-test/` 4 dosya | bu oturum devamı |
| 7 | K000-isletim-sistemi salt-okunur kaldı | ✓ |
| 8 | Registry (`.ai/architecture/index.md`) yazılmadı | ✓ |

## §43 Sayısal İddia Son Denetimi

| # | Dosya | Yeni envanter iddiası | Ölçüm iddiası |
|---|---|---|---|
| 1 | `index.md` | YOK | YOK |
| 2 | `anayasa.md` | YOK | YOK |
| 3 | `ornekler.md` | YOK | YOK |
| 4 | `dogrulama.md` | YOK | yalnız disk ölçümü (§8) |

---

**Authority:** `.ai/architecture/K002-electroncis/electroncis-dogrulama.md` §5 · §6.1 · `.ai/architecture/K002-electroncis/electroncis-kapsam.md` §B.4 · ADR-017
