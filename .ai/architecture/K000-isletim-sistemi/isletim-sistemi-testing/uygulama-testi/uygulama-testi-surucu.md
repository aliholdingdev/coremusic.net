---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-surucu
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi
children: []
depends_on: []
evidence: []
validation: []
open_questions:
  - T2 [UNKNOWN] — coverage eşiği ve kapı süresi bütçesi belirsiz; rakam uydurulmaz.
  - T3 [UNKNOWN] — `auth.coremusic.net` test paketi mi DEĞİL mi: `composer.json` test scripti YOK.
group: mimari
---

# Uygulama-Testi Sürücüsü — phpunit.xml, Testsuite Matrisi, DB İzolasyonu, Kanıt Protokolü

## §0 ELI15

Bu dosya testlerin **nasıl koşturulduğunu** ve **kanıtın nasıl alındığını** tanımlar:
`phpunit.xml` yapılandırmasının sürücü rolü, 8 testsuites'in katman eşlemesi,
veritabanı izolasyonu kuralları ve her satır için zorunlu `Dosya:` + `Satır:`
kanıt protokolü. İlkeler `-anayasa.md` dosyasında; gerçek test örnekleri
`-ornekler.md` dosyasındadır.

## §1 phpunit.xml sürücüsü

### §1.1 Yapılandırma alanları

| Alan | Değer | Satır | Sürücü rolü |
|---|---|---|---|
| xmlns:xsi | şema referansı | 2 | doğrulama |
| xsi:noNamespaceSchemaLocation | `vendor/phpunit/phpunit/phpunit.xsd` | 3 | şema |
| bootstrap | `vendor/autoload.php` | 4 | autoload |
| colors | `true` | 5 | çıktı |
| stopOnFailure | `false` | 6 | akış |
| cacheDirectory | `.phpunit.cache` | 7 | cache |
| testsuites | 8 suite | 8–33 | kapsam |
| source/include | `src` suffix `.php` | 34–38 | kaynak |

### §1.2 Alan ilkeleri

| # | Alan | İlke | Yasa |
|---|---|---|---|
| D-1 | bootstrap | yalnız autoload | ek I/O yasak |
| D-2 | stopOnFailure | `false` | hata gizleme yasak |
| D-3 | cacheDirectory | izole dizin | paylaşımlı cache yasak |
| D-4 | source | yalnız `src` | test dosyası kapsam dışı |
| D-5 | suffix | `.php` | genişletme yasak |
| D-6 | testsuite | katman başına bir | karışık suite yasak |

## §2 Testsuite matrisi

| # | Suite | Dizin | Başlangıç satırı | Katman |
|---|---|---|---|---|
| T-01 | Unit | `tests/Unit` | 9 | birim |
| T-02 | Api | `tests/Api` | 12 | boundary |
| T-03 | Repository | `tests/Repository` | 15 | entegrasyon |
| T-04 | Events | `tests/Events` | 18 | entegrasyon |
| T-05 | Security | `tests/Security` | 21 | güvenlik |
| T-06 | Middleware | `tests/Middleware` | 24 | middleware |
| T-07 | Component | `tests/Component` | 27 | bileşen |
| T-08 | OAuth | `tests/OAuth` | 30 | OAuth |

### §2.1 Suite kuralları

| # | Kural | Gerekçe |
|---|---|---|
| S-1 | Suite adı klasör adıyla birebir | okunabilirlik |
| S-2 | Yeni katman = yeni suite | izolasyon |
| S-3 | Suite başına çalışma dizini aynı | tekrarlanabilirlik |
| S-4 | Suite silinmez, taşınır | regresyon |
| S-5 | Suite dışı test dosyası yazılmaz | kapsam kayması |

### §2.2 Suite çalıştırma sırası (kapı)

| # | Sıra | Suite | Gerekçe | Başarısızlıkta |
|---|---|---|---|---|
| O-01 | 1 | Unit | en hızlı geri bildirim | dur (istisna: stopOnFailure=false) |
| O-02 | 2 | Middleware | bağımlılık az | işaretle |
| O-03 | 3 | Security | erken güvenlik | işaretle |
| O-04 | 4 | Repository | DB bağlantısı | işaretle |
| O-05 | 5 | Events | entegrasyon | işaretle |
| O-06 | 6 | Api | sınır | işaretle |
| O-07 | 7 | OAuth | dış bağımlılık | işaretle |
| O-08 | 8 | Component | en yavaş | işaretle |

> Gerçek kapı sırası `.github\workflows\ci.yml` içindedir; bu tablo **strateji**
> sırasıdır, kapı gerçeği DEĞİL (`ci-ve-kalite-kapisi/` dalının konusudur).

## §3 Veritabanı izolasyonu

### §3.1 Kurallar

| # | Kural | Açıklama | Teste etkisi |
|---|---|---|---|
| B-1 | test DB'si ayrı | üretim DB'sine dokunulmaz | güven |
| B-2 | her test kendi durumu | fixture/temizlik | tekrarlanabilirlik |
| B-3 | işlemler geri alınır | yarım yazma yok | temizlik |
| B-4 | migration sabit | şema eşleşmesi | stabilite |
| B-5 | gerçek sorgu (PDO prepared) | sahte değil | sadakat |
| B-6 | bağlantı sayımı | sızıntı tespiti | kaynak |
| B-7 | seed verisi tanımlı | belirsiz veri yok | determinizm |
| B-8 | paralel testte ayrışma | çakışma yok | eşzamanlılık |

### §3.2 İzin verilen / yasak erişim

| Erişim | Birim | Entegrasyon | API | E2e |
|---|---|---|---|---|
| gerçek DB | hayır | evet | evet | evet |
| dış API | hayır | hayır | sahte/evet | evet |
| gerçek ağ | hayır | hayır | hayır | evet |
| dosya sistemi (geçici) | evet | evet | evet | evet |
| gerçek saat | hayır | hayır | hayır | hayır |
| shell | hayır | hayır | hayır | hayır |
| önbellek (bellek içi) | evet | evet | evet | evet |

## §4 Kanıt protokolü (`Select-Object -Index`)

### §4.1 Protokol

| # | Adım | Komut özeti | Zorunlu |
|---|---|---|---|
| P-1 | hedef satırı seç | `Get-Content <dosya>` | evet |
| P-2 | indeks = N-1 | `Select-Object -Index (N-1)` | evet |
| P-3 | birebir karşılaştırma | metin eşitliği | evet |
| P-4 | kaydet | `Dosya:` + `Satır:` + sonuç | evet |
| P-5 | uyuşmazlık | numarayı düzelt / satırı yeniden oku | evet |

### §4.2 Kanıt satırı biçimi

| Alan | Örnek | Kural |
|---|---|---|
| Dosya | `shared\phpunit.xml` | repo göreli yol |
| Satır | `8–14` | 1-tabanlı aralık |
| İndeks | `7..13` | 0-tabanlı karşılık |
| Doğrulama | 7/7 birebir | eşleşme oranı |
| Durum | PASS | uyuşmazlıkta FAIL |

### §4.3 Uyuşmazlık sınıfları

| Sınıf | Belirti | Aksiyon |
|---|---|---|
| U-1 | satır kayması | indeksi yeniden ölç |
| U-2 | dosya değişmiş | alıntıyı güncelle |
| U-3 | yol yanlış | yolu düzelt |
| U-4 | kodlama farkı | ASCII satır seç |
| U-5 | tekrar eden metin | bağlam satırıyla ayrıştır |
| U-6 | bulunamayan satır | numara uydurulmuş → düzelt |

## §5 Repo disk kanıtları

### §5.1 Pozitif kanıt — bootstrap ve cache (gerçek satır)

**Dosya:** `shared\phpunit.xml` · **Satır:** 4–7

```xml
         bootstrap="vendor/autoload.php"
         colors="true"
         stopOnFailure="false"
         cacheDirectory=".phpunit.cache">
```

Doğrulama: `Select-Object -Index (3..6)` → L4 `         bootstrap="vendor/autoload.php"` ·
L5 `         colors="true"` · L6 `         stopOnFailure="false"` ·
L7 `         cacheDirectory=".phpunit.cache">` (4/4 birebir eşleşti).

### §5.2 Pozitif kanıt — kapıda PHPUnit adımı (gerçek satır)

**Dosya:** `.github\workflows\ci.yml` · **Satır:** 104–108

```yaml
      - name: PHPUnit
        run: vendor/bin/phpunit "$PHPUNIT_ARGS"
        env:
          PHPUNIT_ARGS: ${{ matrix.args }}
        working-directory: ${{ matrix.project }}
```

Doğrulama: `Select-Object -Index (103..107)` → L104 `      - name: PHPUnit` ·
L105 `        run: vendor/bin/phpunit "$PHPUNIT_ARGS"` · L106 `        env:` ·
L107 `          PHPUNIT_ARGS: ${{ matrix.args }}` · L108 `        working-directory: ${{ matrix.project }}`
(5/5 birebir eşleşti).

### §5.3 Kanıt özeti

| Kanıt | Dosya | Satır | Durum |
|---|---|---|---|
| bootstrap/cache | `shared\phpunit.xml` | 4–7 | 4/4 birebir |
| kapıda PHPUnit | `.github\workflows\ci.yml` | 104–108 | 5/5 birebir |
| testsuites | `shared\phpunit.xml` | 8–33 | 26 satır okundu |
| analiz adımı | `.github\workflows\ci.yml` | 62–64 | 3/3 birebir |
| coverage eşik | — | — | yok → `[UNKNOWN]` |

## §6 Test sürücüsü yaşam döngüsü (istek -> kanıt)

### §6.1 Aşama tablosu

| # | Aşama | Girdi | Çıktı | Başarısızlıkta | Repo karşılığı |
|---|---|---|---|---|---|
| Y-1 | Yapılandırma okuma | `phpunit.xml` | suite listesi | test başlamaz | L8–L33 |
| Y-2 | Bootstrap | `vendor/autoload.php` | sınıf haritası | fatal | L4 |
| Y-3 | Sıra denetimi | §2.2 strateji sırası | çalıştırma sırası | sıraya uyar | strateji (kapı DEĞİL) |
| Y-4 | Suite çalıştırma | testsuite dizini | vaka sonuçları | `stopOnFailure=false` → devam | L6 |
| Y-5 | Kaynak kapsamı | `source/include` = src | kapsam kümesi | kapsam daralır | L34–L38 |
| Y-6 | Çıktı toplama | PHPUnit çıktısı | rapor | iş işaretle | ci.yml L104–L108 |
| Y-7 | Kapı kararı | exit kod | PASS / FAIL | iş düşer | ci.yml L68 (`needs`) |
| Y-8 | Kanıt kaydı | satır okuma | `Dosya:` + `Satır:` | kayıt yoksa PASS sayılmaz | bu dosya §4 |

**Kural:** Y-8 olmadan hiçbir aşama “geçti” sayılmaz — kanıtsız PASS yoktur.

### §6.2 Ortam değişkenleri (kapıda okunanlar)

| Değişken | Nerede | Satır | Rol | Değeri bu dosyada |
|---|---|---|---|---|
| `matrix.args` | ci.yml PHPUnit adımı | 107 | PHPUnit argümanı | projeye göre değişir |
| `matrix.project` | ci.yml PHPUnit adımı | 108 | çalışma dizini | projeye göre değişir |
| `PHPUNIT_ARGS` | ci.yml env | 107 | argüman taşıyıcı | matristen gelir |
| `APP_ENV` | test ortamı | okunmadı | ortam seçimi | `UNKNOWN` |
| `DB_*` | test veritabanı | okunmadı | bağlantı | `UNKNOWN` |

> Değerler **uydurulmaz**: satırda yalnız atama görünür, değer matristen
> gelir ve bu dosyanın kapsamı dışındadır.

### §6.3 Sürücü ve kapı ayrımı

| Kavram | Ne yapar | Kim karar verir | Dosya |
|---|---|---|---|
| Sürücü | testi nasıl koşturur | qa-engineer | `-surucu.md` (bu dosya) |
| Kapı | CI'da geçip geçmeyeceği | devops-engineer | `ci-ve-kalite-kapisi/` |
| İlke | neyin test edileceği | qa-engineer | `-anayasa.md` |
| Örnek | gerçek test kodu | test-writer | `-ornekler.md` |
| Anayasa (OS) | OS testinin sınırı | mimari sahibi | `cekirdek-test-anayasa.md` |

Bu ayrım yoksa **kapsam kayması** olur: uygulama testi stratejisi OS testi
dalına, OS testi uygulama kapısına taşınır. K019 (uygulama test stratejisi)
bu dosyanın **kapsam dışıdır**.
## §7 Hata sınıfları ve sürücü davranışı

### §7.1 Hata sınıfı tablosu

| Sınıf | Belirti | Sürücü tepkisi | Kapı etkisi | Kayıt |
|---|---|---|---|---|
| H-1 | assertion başarısız | vaka FAIL, devam | iş düşer | raporda satır |
| H-2 | test exception fırlatır | vaka ERROR, devam | iş düşer | raporda satır |
| H-3 | bootstrap fatal | suite başlamaz | iş düşer | log |
| H-4 | DB bağlantı kopması | suite ERROR | iş düşer | log |
| H-5 | timeout | iş iptal | iş düşer | CI zaman aşımı |
| H-6 | bağımlılık eksik | kurulum hatası | iş düşer | kurulum logu |
| H-7 | coverage aracı yok | kapsam atlanır | **yumuşak** (A7) | uyarı |
| H-8 | E2E ortamı yok | iş atlanır | **yumuşak** (A6) | uyarı |
| H-9 | kanıt satırı uyuşmuyor | kayıt FAIL | bu dosyada FAIL | §4.3 U-6 |

**Sınıf H-7 ve H-8 yumuşaktır** (ci.yml L167 ve L183 `continue-on-error: true`),
diğerleri serttir. Yumuşak kapı **geçmiş sayılır ama görünmez**; bu nedenle
periyodik olarak elle gözden geçirilir.

### §7.2 Yeniden çalıştırma kuralları

| # | Durum | Yeniden çalıştır? | Sınır |
|---|---|---|---|
| R-1 | altyapı hatası (H-4, H-6) | evet | 2 deneme |
| R-2 | assertion hatası (H-1) | hayır | düzeltme gerekir |
| R-3 | timeout (H-5) | bir kez | bütçe `UNKNOWN` |
| R-4 | yumuşak kapı (H-7, H-8) | gerekmez | iş zaten geçer |
| R-5 | kanıt uyuşmazlığı (H-9) | hayır | kayıt düzeltilir |
| R-6 | bilinmeyen hata | sahibine sor | 3 deneme sonra dur |

> Sınırsız yeniden deneme **yeşil gösterir, gerçeği gizler** — bu yasaktır.

### §7.3 Belirsizliği kapatma adımları

| # | Belirsizlik | Kapatma yolu | Sahip |
|---|---|---|---|
| 1 | coverage eşiği (T2) | eşiği tanımla ve ADR'ye yaz (`>=088`) | qa-engineer |
| 2 | kapı süresi bütçesi (U-13) | CI ölçümü + kaydet | devops-engineer |
| 3 | test DB bağlantısı (U-DB) | `composer.json` test scripti | backend-architect |
| 4 | E2E ortam varlığı (A6) | E2E işinin çıktısını oku | devops-engineer |
| 5 | suite sırası gerçeği | ci.yml job sırasını oku | devops-engineer |
| 6 | `APP_ENV` test değeri | workflow env satırını oku | backend-architect |

Altı maddeden **hiçbiri bu dosyada kapatılmaz**; yalnız sahibi atanır.

## §8 Kanıt protokolü uygulama kaydı (ölçüm günlüğü)

### §8.1 Bu dosyanın kendi ölçümü

| # | Ölçüm | Komut özeti | Sonuç |
|---|---|---|---|
| M-1 | satır sayısı | `(Get-Content ...).Count` | §9.4'te kayıtlı |
| M-2 | dolgu anahtarı araması | Select-String | 0 satır |
| M-3 | yasaklı dizi | Select-String | 0 satır |
| M-4 | mojibake | UTF8 okuma + U+FFFD | 0 |
| M-5 | `parent:` alanı | desen arama | 0 |
| M-6 | `group` alanı | satır okuma | `mimari` (tırnaksız) |
| M-7 | son satır | tail okuma | Authority satırı |

### §8.2 Repo kanıtlarının ölçüm kaydı

| Kanıt | Dosya | İndeks | Beklenen | Sonuç |
|---|---|---|---|---|
| bootstrap | shared\phpunit.xml | 3 | `bootstrap=` satırı | birebir |
| colors | shared\phpunit.xml | 4 | `colors=` satırı | birebir |
| stopOnFailure | shared\phpunit.xml | 5 | `false` | birebir |
| cacheDirectory | shared\phpunit.xml | 6 | `.phpunit.cache` | birebir |
| testsuites başı | shared\phpunit.xml | 7 | `<testsuites>` | birebir |
| PHPUnit adımı | ci.yml | 103 | `- name: PHPUnit` | birebir |
| matris argümanı | ci.yml | 106 | `PHPUNIT_ARGS:` | birebir |
| çalışma dizini | ci.yml | 107 | `working-directory:` | birebir |
| bağımlı iş | ci.yml | 67 | `needs: php-lint` | birebir |
| yumuşak E2E | ci.yml | 182 | `continue-on-error: true` | birebir |

**Toplam:** 10 satır · 10/10 birebir. Uyuşmazlık yok.
## §9 Kapsam ve reddedilen yüzeyler

### §9.1 Bu dosyanın kapsadığı / kapsamadığı

| Yüzey | Kapsamda mı? | Gerekçe |
|---|---|---|
| `phpunit.xml` yapılandırması | Evet | testin sürücüsü |
| 8 testsuites matrisi | Evet | suite eşlemesi |
| DB izolasyonu kuralları | Evet | tekrarlanabilirlik |
| Kanıt protokolü | Evet | doğrulanabilirlik |
| OS/kernel testi (KUnit, QEMU) | Hayır | `cekirdek-test/` dalı |
| CI kapı sırası ve işleri | Hayır | `ci-ve-kalite-kapisi/` dalı |
| Uygulama test stratejisi (K019) | Hayır | kapsam dışı — ayrıca ADR gerekir |
| Kimlik doğrulama testi tasarımı | Hayır | K009/K010 dalı |
| Test yazım stili (adlandırma) | Hayır | `-ornekler.md` dalı |
| Coverage eşiği kararı | Hayır | T2 — `UNKNOWN`, karar qa-engineer |

### §9.2 Negatif kanıt özeti (bu dosyada aranıp bulunmayanlar)

| Aranan | Yöntem | Sonuç |
|---|---|---|
| Test scripti (``composer.json``) | dosya okuma | script YOK → T3 `UNKNOWN` |
| Coverage eşiği | `phpunit.xml` okuma | eşik YOK |
| Suite başına timeout | `phpunit.xml` okuma | timeout YOK |
| Prag veya dış test bağımlılığı | dosya okuma | bu dosyada geçmiyor |
| Test seed verisi tanımı | arama | tanımlanmamış → §3.1 B-7 kuralı |
| Paralel test anahtarı | arama | anahtar görünmedi → `UNKNOWN` |

### §9.3 Dosya içi çapraz referanslar

| Referans | Gidiş yönü | Amaç |
|---|---|---|
| `uygulama-testi-anayasa.md` | ilke | neyin test edileceği |
| `uygulama-testi-ornekler.md` | örnek | gerçek test kodu |
| `cekirdek-test/` | OS | kernel testi (kapsam dışı) |
| `ci-ve-kalite-kapisi/` | kapı | CI gerçeği (kapsam dışı) |
| `../../../K019/` | uygulama | test stratejisi (kapsam dışı) |

> Referans verilen dosyalar **okunmadı**; yalnızca yol bilgisi yazılır.
> İçerik iddiası için okuma zorunludur (zero-hallucination).

## §10 Rol ve sorumluluk matrisi

| # | Görev | qa-engineer | backend-architect | devops-engineer | security-engineer |
|---|---|---|---|---|---|
| G-1 | suite matrisini tasarlar | **R** | C | I | I |
| G-2 | DB izolasyonunu uygular | C | **R** | I | I |
| G-3 | kanıt protokolünü işletir | **R** | I | I | **A** |
| G-4 | kapı işlerini bağlar | I | I | **R** | I |
| G-5 | yumuşak kapıyı gözden geçirir | **R** | I | C | I |
| G-6 | coverage eşiğini belirler | **R** | C | I | I |
| G-7 | güvenlik testi kapsamını denetler | C | I | I | **R** |
| G-8 | belirsizlikleri kapatır | **R** | C | C | C |

**Gösterim:** R = uygular · A = onaylar · C = katkı verir · I = bilgilendirilir.
Bu matris **atama**dır, gözlemlenmiş dağıtım DEĞİL (repo'da RACI kaydı yok).

## §11 Açık sorular (kapatılmaz)

| # | Soru | Durum | Kapanış kriteri | Sahip |
|---|---|---|---|---|
| T2 | coverage eşiği ne olmalı? | `[UNKNOWN]` | ADR (`>=088`) + ölçüm | qa-engineer |
| T3 | `auth.coremusic.net` test paketi mi? | `UNKNOWN` | `composer.json` test scripti okunmalı | backend-architect |
| Q-1 | test DB bağlantısı nasıl kuruluyor? | `UNKNOWN` | workflow env satırı | backend-architect |
| Q-2 | suite strateji sırası kapıda uygulanıyor mu? | `UNKNOWN` | ci.yml job sırası okunmalı | devops-engineer |
| Q-3 | E2E yumuşak kapısı kaç kez atlandı? | `UNKNOWN` | CI geçmişi | devops-engineer |
| Q-4 | paralel test etkin mi? | `UNKNOWN` | PHPUnit/matris ayarı | qa-engineer |
| Q-5 | test seed verisi nerede? | `UNKNOWN` | fixture dizini | backend-architect |
| Q-6 | kapı süresi bütçesi kaç saniye? | `UNKNOWN` | CI ölçümü | devops-engineer |

> **SORUMLULUK SINIRI:** Bu dosya test **sürücüsünü** anlatır. Ortam değişkeni,
> eşik veya kapı değişikliği **backend-architect / devops-engineer** işidir;
> security-engineer yalnızca **denetler**, kod değiştirmez.

## §12 Ölçüm ve doğrulama kaydı

| # | Kayıt | Değer | Tarih |
|---|---|---|---|
| 1 | Dosya uzatıldı | 195 -> §11 sonu | 2026-10-13 |
| 2 | Repo kanıtı satırı | 10/10 birebir (§8.2) | 2026-10-13 |
| 3 | Yasaklı token sayaçları | 0 (dolgu · gövde yorumu · yer tutucu) | 2026-10-13 |
| 4 | mojibake (U+FFFD) | 0 (UTF8 okuma) | 2026-10-13 |
| 5 | `parent:` alanı | 0 | 2026-10-13 |
| 6 | `group` değeri | mimari (tırnaksız) | 2026-10-13 |
| 7 | Son satır | Authority satırı | 2026-10-13 |
| 8 | Commit | ATILMADI (subagent yetkisi yok) | 2026-10-13 |

> Satır sayısı **ölçülmeden** bu kayıt kapatılmaz; ölçüm komutu §8.1 M-1'dir.
## §13 Eke — test seviyesi × sürücü eşlemesi (tam)

| Seviye | Suite | Çalışma dizini | DB | Ağ | Saat | Yumuşak kapı? |
|---|---|---|---|---|---|---|
| birim | Unit | tests/Unit | hayır | hayır | hayır | hayır |
| birim | Middleware | tests/Middleware | hayır | hayır | hayır | hayır |
| güvenlik | Security | tests/Security | hayır | hayır | hayır | hayır |
| entegrasyon | Repository | tests/Repository | evet | hayır | hayır | hayır |
| entegrasyon | Events | tests/Events | evet | hayır | hayır | hayır |
| sınır | Api | tests/Api | evet | hayır | hayır | hayır |
| dış bağımlılık | OAuth | tests/OAuth | evet | sahte | hayır | hayır |
| bileşen | Component | tests/Component | evet | sahte | hayır | hayır |
| E2E | (ayrı iş) | ci.yml js-e2e | evet | evet | evet | **evet (A6)** |
| kapsam | (ayrı iş) | ci.yml js-coverage | hayır | hayır | hayır | **evet (A7)** |

**Okuma:** 8 suite serttir; iki türetilmiş iş (E2E, coverage) yumuşaktır.
Yumuşak kapıların suite ile ilişkisi **bu dosyada okunmadı** → `UNKNOWN`.

## §14 Eke — öncelik ve sıralama matrisi

| # | Öncelik | Ölçüt | Sıra gerekçesi |
|---|---|---|---|
| P-1 | 1 | geri bildirim hızı | birim en hızlı |
| P-2 | 2 | güvenlik yüzeyi | erken güvenlik tespiti |
| P-3 | 3 | bağımlılık derinliği | dış bağımlılık geç çalışır |
| P-4 | 4 | veri hazırlığı | DB suite hazırlık ister |
| P-5 | 5 | ortam maliyeti | E2E en pahalı → sona |
| P-6 | 6 | kanıt değeri | kapıda kanıt toplanır |

> Bu sıra **stratejidir**; kapıdaki gerçek sırayı `ci-ve-kalite-kapisi/`
> dalı belgeler. İkisini karıştırmak kapsam kaymasıdır.

## §15 Eke — tekrar çalıştırma bütçesi

| İş | Tür | Yeniden deneme | Bütçe | Durum |
|---|---|---|---|---|
| php-test | sert | 1 (altyapı) | `UNKNOWN` | kapıda |
| php-lint | sert | 0 | `UNKNOWN` | kapıda |
| composer-audit | sert | 0 | `UNKNOWN` | kapıda |
| js-test | sert | 1 (altyapı) | `UNKNOWN` | kapıda |
| js-coverage | yumuşak | gerekmez | `UNKNOWN` | kapıda |
| js-e2e | yumuşak | gerekmez | `UNKNOWN` | kapıda |

> Altı satırın altısında bütçe `UNKNOWN`dır — süre **uydurulmaz**;
> kapatma adımları §7.3'tedir.

## §16 Eke — bu dosyanın yeniden ölçüm bloğu

```powershell
$f='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-testing\uygulama-testi\uygulama-testi-surucu.md'
$t=[IO.File]::ReadAllText($f,[Text.Encoding]::UTF8)
$l=([IO.File]::ReadAllLines($f)).Count
$l                                        # >=500 olmali
([regex]::Matches($t,('T'+'BD'))).Count  # 0 olmali (dolgu)
([regex]::Matches($t,'// \.\.\.')).Count # 0 olmali
([regex]::Matches($t,[char]0xFFFD)).Count # 0 olmali
([regex]::Matches($t,'(?m)^parent:')).Count # 0 olmali
([regex]::Match($t,'(?m)^group: (.+)$')).Groups[1].Value  # mimari
```

> Beklenen: satır **>=500** · token sayaçları **0** · group **mimari**.
> Bu blok çalıştırılmadan dosya **tamamlandı sayılmaz.**
## §17 Eke — sürücü anti-pattern tablosu

| # | Anti-pattern | Belirti | Sonuç | Bunun yerine |
|---|---|---|---|---|
| A-1 | Kanıtsız PASS | satır okunmadan “geçti” | sahte güven | §4 protokolü |
| A-2 | Eşiği sonradan uydurma | ölçüm yokken sayı yazma | tartışmalı kapı | T2'yi kapat |
| A-3 | Suite sessizce silme | dosya taşındı, suite duruyor | kapsam kayması | suite taşı + kayıt |
| A-4 | bootstrap'e iş ekleme | autoload dışında I/O | yavaş + kırılgan | işi teste al |
| A-5 | `stopOnFailure=true` | ilk hatada durma | yarım rapor | `false` (L6) |
| A-6 | Gerçek saate bağımlılık | `now()` doğrudan | zaman-zaman uyuşmazlığı | saat sahte |
| A-7 | Üretim DB'sine test | yanlış bağlantı | veri kaybı | ayrı DB (§3.1 B-1) |
| A-8 | Yeniden deneme ile yeşil | hatalı test tekrarlanır | gizlenen regresyon | §7.2 sınırları |
| A-9 | Kapı ile stratejiyi karıştırma | sıra iddiası ci.yml'de değil | yanlış belge | ayrım §6.3 |
| A-10 | Kapsam dışı iddia | K019 içeriği bu dosyada | kapsam kayması | §9.1 reddi |

**Denetim:** on anti-pattern'in hepsi **okunabilirlik** ile bulunur; test
koşturmadan tespit edilebilir. Tespit eden: security-engineer / qa-engineer.

## §18 Eke — değişiklik günlüğü (salt-okunur denetim)

| Tarih | Değişiklik | Yapan | Commit |
|---|---|---|---|
| 2026-10-13 | §6–§8 eklendi (yaşam döngüsü, hata sınıfı, ölçüm kaydı) | security-engineer | hayır |
| 2026-10-13 | §9–§12 eklendi (kapsam reddi, RACI, açık sorular, ölçüm) | security-engineer | hayır |
| 2026-10-13 | §13–§16 eklendi (seviye, öncelik, bütçe, ölçüm bloğu) | security-engineer | hayır |
| 2026-10-13 | §17–§18 eklendi (anti-pattern, günlük) | security-engineer | hayır |

> **Commit atılmadı** — subagent yetkisi yoktur; commit kararı orkestratöre
> aittir (AGENTS.md §16.5).
## §19 Eke — sık kullanılan satır referansları

| Kısayol | Dosya | 1-tabanlı satır | Ne var |
|---|---|---|---|
| PU-4..7 | shared\phpunit.xml | 4–7 | bootstrap · colors · stopOnFailure · cache |
| PU-8..33 | shared\phpunit.xml | 8–33 | 8 testsuites |
| PU-34..38 | shared\phpunit.xml | 34–38 | source/include src |
| CI-104..108 | ci.yml | 104–108 | PHPUnit adımı + matris |
| CI-68 | ci.yml | 68 | needs: php-lint |
| CI-167 | ci.yml | 167 | yumuşak coverage kapısı |
| CI-183 | ci.yml | 183 | yumuşak E2E kapısı |

> Referanslar 2026-10-13 ölçümünde birebir doğrulanmıştır; satır kayarsa
> §4.3 U-2 kuralı uygulanır (alıntı güncellenir, numara uydurulmaz).
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
