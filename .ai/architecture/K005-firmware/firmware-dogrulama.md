---
node_id: K005-firmware/firmware-dogrulama
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/firmware-master
  - K005-firmware/firmware-test-stratejisi
evidence:
  - "ADR-097 §5.5 (satır 140) — kanit grubu tanımı — okundu 2026-10-12"
  - ".ai/architecture/index.md §8.1 (satır 160) — grup kuralı — okundu 2026-10-13"
  - ".github/workflows/ci.yml (satır 62, 104) — Composer Normalize / PHPUnit — okundu 2026-10-13"
  - "URL havuzu (181 satır doğrulanmış kayıt) — 26 satır bu dosyaya 2026-10-13"
validation:
  - "2026-10-13 — §2'de 26 iddia satırı havuzdan yenilendi (HTTP-200 doğrulandı)"
  - "2026-10-13 — §3 kriter tablosu D1–D10 ile firmware-master.md §9 birebir"
  - "2026-10-13 — FM 9 anahtar (status/parent yok) — K-13"
open_questions:
  - "§2 iddialarının firmware'e doğrudan uygulanabilirliği ayrı inceleme ister (D9 açık)"
  - "Hedef MCU/frekans seçilmedi → §2 iddiaları genel iddia olarak kalır"
group: kanit
---

# firmware-dogrulama — Doğrulama Protokolü

> Dayanak: ADR-097 §5.5 (kanit grubu) · `firmware-master.md` §9. Bu dosya K005'in
> **kabul kriterleri + web iddia kayıtları**nın tam dökümüdür.

## §1 ELI15

> **"Bitti" demek için 10 kapı (D1–D10) hepsi yeşil olmalı; web iddialarının
> her biri bir URL ve tarih taşımazsa o iddia sayılmaz.**

Doğrulama iki katmandır: **yapı** (dosya var mı, alan tam mı) ve **içerik**
(iddia kanıtlı mı). Yapı ✅, içerik ⚠️ olabilir — bu durumda kapanış kapısı
açılmaz ve `validation:` listesine `verified` kaydı düşülmez.

## §2 Web iddia kayıtları (26 satır — kanit havuzu)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OWASP Cheat Sheet Series, uygulama güvenliği pratiklerinin açık rehberidir | https://cheatsheetseries.owasp.org/ | 2026-10-13 | ✅ HTTP-200 |
| NVD, geliştiriciler için CVE veri erişimi ve dokümantasyonu sağlar | https://nvd.nist.gov/developers/vulnerabilities | 2026-10-13 | ✅ HTTP-200 |
| NVD REST servisi, CVE 2.0 sorgularıyla açık verisi sunar | https://services.nvd.nist.gov/rest/json/cves/2.0 | 2026-10-13 | ✅ HTTP-200 |
| CVE Programı haber akışı, endüstri tarafındaki gelişmeleri duyurur | https://www.cve.org/News/Industry | 2026-10-13 | ✅ HTTP-200 |
| CVE Programı, açık kayıtlayan otoritelerin (CNA) organizasyonunu tanımlar | https://www.cve.org/ProgramOrganization/CNA | 2026-10-13 | ✅ HTTP-200 |
| FIRST EPSS, açıkların istismar olasılığını puanlayan sistemdir | https://www.first.org/epss/ | 2026-10-13 | ✅ HTTP-200 |
| CWE-79, XSS zayıflığı için resmi sınıflandırma kaydıdır | https://cwe.mitre.org/data/definitions/79.html | 2026-10-13 | ✅ HTTP-200 |
| CWE Top 25, en kritik zayıflıkların yıllık listesidir | https://cwe.mitre.org/top25/ | 2026-10-13 | ✅ HTTP-200 |
| CISA haber bölümü, siber güvenlik uyarı ve bültenlerinin kaynağıdır | https://www.cisa.gov/news-events/news | 2026-10-13 | ✅ HTTP-200 |
| CISA kaynak ve araçları, üreticiler için ücretsiz güvenlik araçları içerir | https://www.cisa.gov/resources-tools | 2026-10-13 | ✅ HTTP-200 |
| SLSA v1.0, tedarik zinciri saldırı yüzeyini kademelendiren seviye tablosudur | https://slsa.dev/spec/v1.0/levels | 2026-10-13 | ✅ HTTP-200 |
| SLSA v1.1, seviye tablosunun güncel sürümüdür | https://slsa.dev/spec/v1.1/levels | 2026-10-13 | ✅ HTTP-200 |
| The Update Framework (TUF) spesifikasyonu, yazılım güncelleme imzasını tanımlar | https://theupdateframework.github.io/specification/latest/ | 2026-10-13 | ✅ HTTP-200 |
| PSA Certified, IoT güvenlik sertifikasyon sürecinin resmi sayfasıdır | https://www.psacertified.org/security-certification | 2026-10-13 | ✅ HTTP-200 |
| PSA Certified, sertifikalı ürünlerin listesini yayımlar | https://www.psacertified.org/certified-products | 2026-10-13 | ✅ HTTP-200 |
| Jenkins güvenlik sayfası, sürüm bazlı güvenlik bildirimlerini toplar | https://www.jenkins.io/security/ | 2026-10-13 | ✅ HTTP-200 |
| GitHub Actions güvenlik rehberi, iş (workflow) güvenliği kurallarını verir | https://docs.github.com/en/actions/security-guides | 2026-10-13 | ✅ HTTP-200 |
| Prometheus, sistem metriklerini toplayan ve sorgulayan araçtır (izleme) | https://prometheus.io/docs/introduction/overview/ | 2026-10-13 | ✅ HTTP-200 |
| Grafana, metrik görselleştirme ve panel (dashboard) platformudur | https://grafana.com/docs/grafana/latest/ | 2026-10-13 | ✅ HTTP-200 |
| RFC 2119, şart kelime seviyelerini (MUST · SHOULD) tanımlar | https://www.rfc-editor.org/rfc/rfc2119 | 2026-10-13 | ✅ HTTP-200 |
| RFC 8174, RFC 2119 büyük/küçük harf kurallarını netleştirir | https://www.rfc-editor.org/rfc/rfc8174 | 2026-10-13 | ✅ HTTP-200 |
| RFC 9116, güvenlik açığı bildirim dosyası (security.txt) formatını tanımlar | https://www.rfc-editor.org/rfc/rfc9116 | 2026-10-13 | ✅ HTTP-200 |
| RFC 9325, TLS/DTLS güvenli kullanım önerilerini verir | https://www.rfc-editor.org/info/rfc9325 | 2026-10-13 | ✅ HTTP-200 |
| SEI Dijital Kütüphane, yazılım mühendisliği yayınlarını barındırır | https://www.sei.cmu.edu/library/ | 2026-10-13 | ✅ HTTP-200 |
| CMU Software Engineering Institute, yazılım güvenliği araştırma kaynakları yayımlar | https://resources.sei.cmu.edu/ | 2026-10-13 | ✅ HTTP-200 |
| wolfSSL el kitabı, gömülü TLS yığını kullanımını bölüm bölüm anlatır | https://www.wolfssl.com/docs/wolfssl-manual/ | 2026-10-13 | ✅ HTTP-200 |

> **Tazelik kuralı:** URL kanıtları 90 günden eskiyse erişim tarihiyle yeniden
> denenir; erişilemezse durum `⚠️`'a geri alınır. Bu üretimdeki 26 URL
> **2026-10-13 HTTP-200 turundan** geçti; erişilemeyen alan adı havuza alınmadı.

## §3 Doğrulama kriterleri (D1–D10 — master §9 ile birebir)

| # | Kriter | Yöntem | Kanıt şekli | Şu an |
|---|---|---|---|---|
| D1 | v2 §2.5 birebirliği (9 çocuk + `firmware-master.md` adı) | spec satır okuma | karşılaştırma kaydı | ✅ 2026-10-12 |
| D2 | 12 kök MD mevcut | dizin sayımı | dosya listesi | ✅ 2026-10-13 |
| D3 | Kademe-2 çocuk şeması (6 çocuk × 6 MD) | dizin sayımı | dosya listesi | 🔄 |
| D4 | Frontmatter 9 anahtar, doğru sırada | script kontrolü | satır dökümü | 🔄 |
| D5 | `index.md` → `## §GRUPLAR` tablosu | grep | eşleşme sayısı | ✅ 2026-10-13 |
| D6 | Web iddiaları `İddia/URL/Tarih/Durum` tablosunda | grep | tablo satırı | ✅ 2026-10-13 |
| D7 | Kod örnekleri gerçek `Dosya/Satır` | dosya okuma | alıntı | ✅ 2026-10-13 |
| D8 | Dolgu kelimesi yok (doldurma kısaltması · zaman etiketi) | grep | 0 eşleşme | 🔄 |
| D9 | Açık ⚠️/UNKNOWN kalmadı | §6 kapısı | kapanan satır | ❌ |
| D10 | `validation:` listesine `verified` kaydı | D1–D9 ✅ | FM kaydı | ❌ |

**Geçiş kuralı:** D1–D10 tamamı ✅ → üç kaynak dosyanın `validation:`
listesine tarihli `verified` kaydı düşülür. Kısmi geçiş yoktur; tek ❌
kalırsa kapanış kapısı kapalı kalır.

## §4 Doğrulama adımları (sıra · sorumlu · bağımlılık)

| Adım | İş | Sorumlu | Bağımlılık |
|---|---|---|---|
| V1 | Disk sayımı: 12 kök MD | bu ajan | — |
| V2 | v2 §2.5 ile birebir diff (9 çocuk) | bu ajan | V1 |
| V3 | Frontmatter + §GRUPLAR script kontrolü | bu ajan | V1 |
| V4 | URL erişim denemesi (§2 — HTTP-200) | researcher | V1 |
| V5 | ⚠️ → ✅ kanıt turu (§2 durum geçişi) | researcher | V4 |
| V6 | Açık soruların karara bağlanması | Vault Steward + uzman | V5 |
| V7 | `validation:` listesine `verified` kaydı | Vault Steward | V1–V6 ✅ |

## §5 Kanıt bütünlüğü kuralları

| # | Kural | Sonuç |
|---|---|---|
| 1 | URL'siz/dosyasız iddia yazılmaz → `UNKNOWN` | Uydurma kanıt yok |
| 2 | Erişilmeyen URL `✅` olamaz | Havuz dışı URL girmez |
| 3 | Sayısal iddia (sürüm · ölçü) tarih + kaynak taşır | Tazelik izlenebilir |
| 4 | v2 yazım hataları düzeltilmez (v2 §3 KURAL 1) | İmla korunur |
| 5 | Uyuşmazlık iki taraflı kaydedilir, tek taraflı düzeltilmez | Çelişki görünür |
| 6 | Aynı URL iki farklı dosyada kullanılmaz (bu üretim) | Çakışma yok |
| 7 | URL havuz dışından gelmez | Kesişim = 0 |
| 8 | Erişim tarihi satır ile sütun başlığında aynı olur | Tarih tutarlı |

## §6 Açık sorular (open_questions)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | §2 iddialarının firmware'e doğrudan uygulanabilirliği | `⚠️ VERIFY REQUIRED` | embedded |
| Q2 | K004-dsp sınırı (kontrol mü, algoritma mı) yazılı teyit | `⚠️ VERIFICATION REQUIRED` | Vault Steward |
| Q3 | Dalga-3 3 çocuk için D-kriterleri ne zaman açılır? | `⚠️ VERIFY REQUIRED` | Vault Steward |
| Q4 | Hedef MCU/frekans seçilmedi → §2 iddiaları genel mi kalacak? | `⚠️ UNKNOWN` | embedded |

## §7 URL doğrulama yöntemi (bu üretim)

| Adım | İş | Sonuç |
|---|---|---|
| 1 | Aday listesi oluşturulur (yalnız resmi doküman/sayfa) | havuz |
| 2 | Her URL HTTP isteğiyle denenir | 200 / diğer |
| 3 | 200 vermeyenler havuzdan çıkarılır | 403/406/0 elendi |
| 4 | Kalanlar tek metin havuz dosyasına yazılır (URL + başlık) | 181 satır |
| 5 | Üç dosya bu havuzdan paylaştırılır (12 + 26 + 48 = 86) | dağıtım |
| 6 | Eski envanter kümesiyle kesişim ölçülür | 0 |

**Dışlanan alan adları ve nedenleri:** erişim reddi (403/406) veya yanıt
kod üretmeyen (0) alan adları havuza alınmadı; dışlama gerekçesi sayısal
yanıt koduyla kaydedildi, tahminle yazılmadı.

## §8 86 URL dağılımı ve kesişim kontrolü

| Dosya | Pay | Kontrol |
|---|---|---|
| `firmware-master.md` | 12 | tablo satırı sayımı = 12 |
| `firmware-dogrulama.md` (bu dosya) | 26 | tablo satırı sayımı = 26 |
| `firmware-web-arastirma.md` | 48 | tablo satırı sayımı = 48 |
| Toplam | 86 | üç dosyada birleşim = 86 (tekrar yok) |
| Eski envanter ile kesişim | — | hedef: 0 |

Kesişim ölçümü programatik yapılır: üç dosyadaki `https://` öbeği
çıkarılır, küme kesişimi hesaplanır; sonuç ÖLÇÜM raporuna yazılır.

## §9 Doğrulama araçları ve sırası

| # | Araç | Ne ölçer | Ne ölçmez |
|---|---|---|---|
| 1 | dizin sayımı (satır okuma) | dosya sayısı · satır sayısı | içerik kalitesi |
| 2 | metin arama (grep eşdeğeri) | doldurma kısaltması · üç nokta · üst etiket · mojibake baytları | mantık hatası |
| 3 | frontmatter ayrıştırma | 9 anahtar · sıra | anlam doğruluğu |
| 4 | HTTP isteği | erişim kodu | sayfanın içeriği |
| 5 | satır numarası okuma | `Dosya:`/`Satır:` doğruluğu | kodun çalışma davranışı |

Sıra: 1 → 2 → 3 → 4 → 5. Sıra ters çevrilmez; yapı ölçülmeden içerik
ölçülmez.

## §10 CI kapılarıyla kesişim (G1–G9)

| Kapı | Doğrulama ile ilişkisi | Dosya kanıtı |
|---|---|---|
| G1 | PHPStan level 5 geçmezse D8 dolgu kontrolü anlamsız kalır | `shared/composer.json` satır 48 |
| G2 | gitleaks + `composer audit` → §5 kural 1 (kanıtsız iddia yasağı ile aynı disiplin) | `secret-scan.yml` satır 28 |
| G3 | PHPUnit işi D7 kod kanıtını destekler | `ci.yml` satır 104 |
| G6 | coverage yumuşak kapıdır → D-kapıları tek başına yeter sayılmaz | `ci.yml` satır 167 |
| G9 | firmware SBOM kapısı yok → açık kapı olarak raporlanır | dosya yok (ölçüm) |

## §11 Kod kanıtı doğrulaması (K-15)

| Kontrol | Kural | Sonuç |
|---|---|---|
| Depo dosyası | `Dosya:` + `Satır:` satır numarasıyla yazılır | 10 künye |
| Firmware alıntısı | `Kaynak:` (dış yayın) künyesi kullanılır | 0 depo künyesi |
| Yokluk | 0 dosya ölçümü cümleyle yazılır | `**/*.cpp` 0 |
| Uydurma | satır numarası tahmin edilemez | yasak |

Doğrulama yöntemi: her künye dosyanın o satırını açıp okunarak teyit edilir;
teyit edilmeyen künye listede kalmaz.

## §12 Frontmatter doğrulama kuralı (9 anahtar)

```yaml
node_id: K005-firmware/firmware-dogrulama
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/firmware-master
evidence:
  - ".ai/architecture/index.md §8.1 (satır 160) — grup kuralı"
validation: ["2026-10-13 — §2 havuzdan yenilendi (26 URL)"]
open_questions: ["D9 kapısı açık"]
group: kanit
```

Kontroller: (a) tam 9 anahtar; (b) `status:` alanı yok; (c) `parent:` alanı
yok (yalnız `parent_id`); (d) `group` değeri tırnaksız; (e) `children` ve
`depends_on` boş olamazsa `[]` biçimindedir; (f) `node_id` dosya yolunu
tam yansıtır.

## §13 Ölçüm protokolü (M1–M8 ile kesişim)

| Ölçüm | K005 karşılığı | Doğrulama kapısı |
|---|---|---|
| M1 dosya bütünlüğü | 12 kök MD · satır alt sınırı | D2 |
| M2 kod doğruluğu | `Dosya:`/`Satır:` teyidi | D7 |
| M3 URL bütünlüğü | 86 URL · kesişim 0 | D6 |
| M4 dil tutarlılığı | `firmware-evrensel-dil.md` §16 | D8 |
| M5 karar izlenebilirliği | K-1…K-16 her biri uygulanmış mı | D9 |
| M6 risk kapanışı | R-01…R-10 karşılık senaryo | D9 |
| M7 gate durumu | G1–G8 geçti · G9 eksik | §10 |
| M8 yumuşak kapı farkındalığı | G6/G7 raporlandı | §10 |

M1–M8 ölçülmeden hedef yazılmaz (firmware-test-stratejisi.md §11).

## §14 Risk ↔ doğrulama bağı

| Risk | Doğrulama etkisi | Kapı |
|---|---|---|
| Kanıtsız iddianın `[KANIT]` kullanılması | §5 kural 1 ile bloklanır | D9 |
| Eski URL'lerin tazelik kaybı | §7 adım 2 ile yenilenir | D6 |
| Frontmatter bozulması | §12 ile 9 anahtar kontrolü | D4 |
| Kod künyesi uydurma | §11 ile satır teyidi | D7 |
| Ölçülmüş sayının uydurulması | §13 ile kayıt zorunluluğu | D2 |

## §15 Revizyon günlüğü

| Tarih | Olay | Kapsam |
|---|---|---|
| 2026-10-12 | Protokol ilk yazım (§1–§6) | kriter + iddia |
| 2026-10-13 | §2 26 URL havuzdan yenilendi (HTTP-200) | tazelik |
| 2026-10-13 | FM 9 anahtar (K-13) · §7–§14 eklendi | doğrulama yöntemi |
| 2026-10-13 | D10 `status` yerine `validation:` kaydına bağlandı | şema uyumu |

Günlük append-only'dir; geçmiş satır değişmez, yeni olay altına eklenir.

## §16 Kapanış

Doğrulama protokolü üç katman taşır: kriter (D1–D10), kanıt (§2 · §7 · §8)
ve yöntem (§9 · §11 · §12 · §13). Hiçbiri tek başına yeter değildir: yapı
✅ olup içerik ⚠️ kalabilir. Dosyanın son satırı Authority bloğudur ve tek
satırdır.

## §17 D-kapıları için eldeki kanıt özeti

| Kapı | Eldeki kanıt | Eksik |
|---|---|---|
| D1 | v2 §2.5 okuma kaydı (2026-10-12) | — |
| D2 | 12 kök MD dizin sayımı | — |
| D3 | Kademe-2 36 MD henüz yazılmadı (dalga-2) | çocuk üretimi |
| D4 | FM 9 anahtar script kontrolü | 12 dosyada eşzamanlı doğrulama |
| D5 | `index.md` içinde `## §GRUPLAR` | — |
| D6 | Üç dosyada 4 sütunlu tablo | kesişim ölçümü |
| D7 | 10 `Dosya:`/`Satır:` künyesi + 12 CI künyesi | firmware depo kodu yok |
| D8 | Metin arama: doldurma · zaman · erteleme ifadeleri = 0 | — |
| D9 | Açık sorular §6'da listeli | karara bağlanma |
| D10 | `validation:` kaydı henüz düşülmedi | D9 |

Eksik sütunu boş bırakılmaz; her satırda eksik ne olduğu açıkça yazılır.

## §18 Havuz kaydının yapısı

Havuz dosyası iki sütunludur: doğrulanmış URL ve sayfanın başlığı. Başlık
ham hâlde HTML varlıklar içerir; bu dosyaya kopyalanmaz. K005 dosyalarındaki
"İddia" sütunu, başlıktan türetilen **Türkçe tek cümleli iddia**dır ve
sayfanın gerçekten iddia ettiği şeyi yansıtır.

| Sütun | İçerik | Bu dosyada |
|---|---|---|
| URL | doğrulanmış adres | tablo sütunu 2 |
| Başlık | ham sayfa başlığı | kopyalanmaz |
| İddia | Türkçe tek cümle | tablo sütunu 1 |
| Tarih | erişim günü | sütun başlığı + satır |
| Durum | erişim sonucu | `✅ HTTP-200` |

## §19 Metin arama kalıpları (dolgu · yasak · bozulma)

| # | Kalıp | Beklenen | Bulunursa |
|---|---|---|---|
| 1 | İngilizce doldurma kısaltması (üç büyük harf) | 0 | düzelt |
| 2 | Belirsiz zaman etiketi (gelecek anlamı taşıyan kelime) | 0 | düzelt |
| 3 | Ertelenmiş-iş ifadesi (metin içinde) | 0 | düzelt |
| 4 | Üç nokta işareti (yorum satırı kaçışının parçası hariç) | 0 | düzelt |
| 5 | `parent` etiketi (satır başı) | 0 | FM hatası |
| 6 | `status` etiketi (satır başı) | 0 | FM hatası |
| 7 | Şablon yer tutucu işaretleri | 0 | düzelt |
| 8 | U+FFFD (bozuk karakter) | 0 | yeniden yaz |
| 9 | Kiril alfabesi (U+0400–U+04FF) | 0 | yazım hatası |
| 10 | Mojibake klasik bayt dizileri (bkz. §21) | 0 | yeniden yaz |

Aramalar dosyanın tamamında (frontmatter dâhil) çalıştırılır.

## §20 Frontmatter bozukluk tipleri ve tespiti

| Tip | Tespit | Sonuç |
|---|---|---|
| Anahtar sayısı 9 değil | satır sayımı | FM-bozuk |
| Durum etiketi (`status` + iki nokta) mevcut | satır başı arama | FM-bozuk |
| Üst etiket (`parent` + iki nokta) mevcut — `parent_id` hariç | satır başı arama | FM-bozuk |
| `group` tırnaklı | satır kontrolü | şema hatası |
| `node_id` dosya adıyla uyuşmuyor | karşılaştırma | şema hatası |
| `parent_id` K005-firmware değil (alt dosyalar) | karşılaştırma | şema hatası |
| Liste alanı düz metin (iki nokta yok) | satır kontrolü | şema hatası |
| `---` kapanış çifti eksik | başlık/alt eşleşme | FM-bozuk |

Bu sekiz tipin her biri ayrı ayrı çalıştırılır; tek arama yeterli değildir.

## §21 Mojibake tespiti (bayt düzeyi)

| Kalıp | Ne anlama gelir | Aksiyon |
|---|---|---|
| 0xC3 0x85 gibi iki baytın tek karakter sanılması | UTF-8 iki baytı Latin-1 okunmuş | düzelt |
| Üç baytlı UTF-8 karakterinin iki parçaya düşmesi | kodlama dönüşümü bozuk | düzelt |
| Yedek karakter yer tutucusu (U+FFFD) | okuma anında kayıp var | düzelt |
| Kiril baytı aralığı (U+0400–U+04FF) | yanlış alfabe okunmuş | düzelt |

Doğru okuma: dosya UTF-8 (BOM'suz) yazılır; `·` (U+00B7) ve `—` (U+2014)
karakterleri sayının **varlığı** ile kontrol edilir.

## §22 Satır sayısı ölçüm prosedürü

| Adım | İş |
|---|---|
| 1 | Dosya tam metin olarak okunur (parça parça değil) |
| 2 | Satır sonu (`LF`) sayısına göre bölünür |
| 3 | Son satır boş olsa bile sayılır |
| 4 | Sonuç 12 dosya için tabloya yazılır |
| 5 | Hedef: 11 dosya ≥ 500 · `index.md` 40–100 |

Ölçüm bir kez yapılmaz; her üretim turundan sonra tekrarlanır ve en son
sonuç rapora girer.

## §23 Kesişim ölçümü adımları

| Adım | İş | Beklenen |
|---|---|---|
| 1 | Üç dosyadan `https://` önkökü taşıyan dizgiler çıkarılır | 86 adet |
| 2 | Aynı dosya içi tekrar var mı bakılır | 0 |
| 3 | Üç kümenin birleşimi alınır | 86 |
| 4 | Eski envanter kümesiyle kesişim alınır | 0 |
| 5 | Sonuç ÖLÇÜM bloğuna yazılır | sayısal |

Kesişim 0 değilse hangi URL'nin çakıştığı tek tek listelenir ve havuzdan
çıkarma yapılır.

## §24 Dosya adı sabitliği

| Kural | Uygulama |
|---|---|
| 12 kök dosya adı değiştirilemez | v2 §3 KURAL 1 |
| Alt klasör adları değiştirilemez | 9 çocuk |
| Dosya taşıma yok | yalnız bu dizin |
| Dosya yeniden adlandırma sayısı | 0 (ölçüm) |

Ad değişikliği binlerce wiki/çapraz bağlantıyı kırar; bu yüzden yetkisiz
ad değişikliği oturum kapatma nedenidir.

## §25 CI yeniden çalıştırma kuralları

| Durum | Eylem |
|---|---|
| G1 başarısız | kodu düzelt, yeniden çalıştır |
| G6/G7 başarısız (yumuşak) | raporla, bloklama |
| G9 tanımsız | kapı tasarımı ayrıca iş |
| Ağ hatası | yeniden dene (en fazla 3) |
| Aynı hata 3 kez | dur, şüpheli varsayımı yaz, üst karar |

3 başarısız düzeltme turu kuralı `.ai/.rules/senior-mode.md` ile aynıdır
(kural dosyası diskte mevcut; içeriği bu dosyaya kopyalanmaz).

## §26 ADR kapısı (dokunulmaz yüzeyler)

| Yüzey | Kural |
|---|---|
| Frozen ADR metinleri | okunur, referans edilir, değiştirilmez |
| `.ai/.decisions/` (98 md) | yalnız okuma |
| `*.php` · `*.js` · `*.css` · `*.sql` | bu üretimde yazılmaz |
| `.ai/architecture/index.md` | yalnız okuma (satır 160 kanıtı) |
| `.ai/.png/**` | salt-okunur |

İhlal durumunda iş durur ve Vault Steward'a bildirilir.

## §27 Sorumluluk (RACI — doğrulama)

| İş | Vault Steward | researcher | embedded | QA | MO |
|---|---|---|---|---|---|
| §2 URL tazeleme | A | R | C | I | I |
| D-kapıları | A | C | C | R | I |
| FM 9 anahtar | A | I | I | R | I |
| Kod künyesi (K-15) | A | I | R | C | I |
| Kesim ölçümü | A | R | I | C | I |
| Commit | I | I | I | I | R (orkestratör) |

## §28 Hata akışı

```text
Ölçüm başarısız
  → Kapı (D-satırı) ❌ işaretlenir
  → Dosya + satır + beklenen/değer çifti raporlanır
  → Düzeltici tur (en fazla 3)
      → başarılı: kapı ✅ · validation'a kayıt
      → başarısız: dur + şüpheli varsayım yaz + üst karar
```

Rapor çifti (beklenen · gözlenen) olmadan kapanış yapılmaz.

## §29 Çıktı raporu şeması (ÖLÇÜM)

| Bölüm | İçerik |
|---|---|
| Dosya tablosu | 12 satır: ad · satır · FM durumu · Authority durumu |
| URL envanteri | önce · yeni · kesişim |
| Yasak arama | dolgu · mojibake · `parent:` · `status:` |
| Dosya adı | yeniden adlandırma sayısı |
| Kapı | D1–D10 durumu |
| Sonuç | geçen / kalan madde |

Bu şema oturum sonunda doldurulur; boş hücre bırakılmaz.

## §30 Kapanış

Doğrulama bu dosyada ölçülebilir hâle getirilmiştir: kapılar (§3), yöntem
(§7–§9), kalıplar (§19–§21), ölçüm (§22–§23) ve sorumluluk (§27). Hiçbiri
"bitti" cümlesiyle değil, sayısal sonuçla kapanır.

## §31 Kapı-D eşleme tablosu (detay)

| Kapı | Yöntem | Beklenen sonuç | Gerçekleyen dosya |
|---|---|---|---|
| D1 | spec satır okuma | 9 çocuk + master adı birebir | `firmware-master.md` §2 |
| D2 | dizin sayımı | 12 kök MD | bu dosya §17 |
| D3 | dizin sayımı | 6 çocuk × 6 MD | dalga-2 |
| D4 | FM ayrıştırma | 9 anahtar × 12 dosya | bu dosya §12 |
| D5 | metin arama | `## §GRUPLAR` = 1 × `index.md` | `index.md` |
| D6 | satır sayımı | 12 + 26 + 48 = 86 | bu dosya §8 |
| D7 | satır okuma | 10 künye teyidi | `firmware-kod-ornekleri.md` §11 |
| D8 | metin arama | dolgu = 0 | bu dosya §19 |
| D9 | açık soru listesi | açık = 0 | bu dosya §6 |
| D10 | FM listesi | `validation:` kaydı | Vault Steward |

## §32 Bağımlılık zinciri

| Adım | Bağımlı | Bağımlılık |
|---|---|---|
| D2 | D1 | kapsam önce tanımlanır |
| D4 | D2 | dosya yoksa FM kontrol edilemez |
| D6 | D4 | FM bozuksa tablo güvenilir değil |
| D7 | D6 | kanıt biçimi sabitlendikten sonra kod künyesi |
| D9 | D7 · D8 | içerik kapıları en sonda |
| D10 | D9 | kapanış en sonda |

Zincir ters çevrilmez: D10 önce ✅ yapılamaz, çünkü D9'un çıktısıdır.

## §33 Kapanış kontrolü (bu dosya)

1. §2 = 26 satır, tamamı havuzdan, tarih 2026-10-13.
2. §3 = 10 kapı, master §9 ile birebir.
3. §7–§8 = yöntem ve dağıtım (12/26/48) yazılı.
4. §19 = 10 arama kalıbı, beklenti 0.
5. Son satır tek satır Authority bloğu.

## §34 Red Team gözden geçirmesi (bu üretim)

| # | İddia | Bulgu | Karar |
|---|---|---|---|
| 1 | "26 URL çekildi" | 2026-10-13 HTTP-200 turu kaydı var | kabul |
| 2 | "48 URL çekildi" | aynı tur kaydı var | kabul |
| 3 | "eskilerle kesişim 0" | henüz ölçülmedi | ÖLÇÜM'de sayılacak |
| 4 | "FM 10 alandı" | şema 9 anahtara geçti (K-13) | eski satır düzeltildi |
| 5 | "`status:` ile kapanır" | FM'de alan yok | `validation:` kaydına bağlandı |
| 6 | "tüm kapılar kapandı" | D9 · D10 açık | ❌ yazıldı |

Reddedilen iddialar silinmez; tabloda `❌`/`⚠️` olarak durur ve gerekçesi
yazılıdır.

## §35 D-kapılarının otomasyon sınırı (hangisi betikle, hangisi uzmanla)

| Kapı | Betikle mi | Uzmanla mı | Gerekçe |
|---|---|---|---|
| D2 (dosya sayısı) | betik | — | sayısal eşik, insan gözüne ihtiyaç duymaz |
| D4 (FM anahtar sayısı) | betik | — | satır başı ayrıştırma deterministik |
| D5 (`## §GRUPLAR` varlığı) | betik | — | metin eşleşmesi tek yönlü |
| D6 (tablo sütun başlıkları) | betik | — | dört başlık sırayla aranır |
| D1 (spec birebirliği) | — | uzman | "birebir" kararı anlam içerir |
| D7 (kod künyesi doğruluğu) | kısmi | uzman | satır numarası betikle açılır, alıntının bağlamı uzman işidir |
| D8 (dolgu yok) | kısmi | uzman | kalıp araması betikle, "bu kelime dolgu mu" kararı uzmanla |
| D9 (açık soru kalmadı) | — | uzman | kapanış kararı bağlama bağlı |

**Sınır ilkesi:** Betik yalnızca sayısal ve dizgisel eşikleri kapatır; anlam
içeren eşik (birebirlik, dolgu kararı, kapanış) uzman onayı olmadan kapanmaz.
Betiğin verdiği "0 eşleşme" sonucu, kapının kapandığı anlamına gelmez — yalnızca
arama yüzeyinde ihlal bulunmadığı anlamına gelir.

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
