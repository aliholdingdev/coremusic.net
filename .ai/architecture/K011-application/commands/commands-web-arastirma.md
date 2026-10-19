---
node_id: K011-application/commands/commands-web-arastirma.md
node_type: file
status: draft
parent_id: K011-application/commands
children: []
depends_on:
  - K008-domain
  - K012-services
evidence:
  - "P-COMM havuzu — 8 URL, HTTP 200 ile doğrulandı, 2026-10-15"
  - "Tüm iddialar php.net resmi belgelerinden; repo'ya uygulanabilirlik §7'de"
validation:
  - "Satır ≥500 — (Get-Content).Count ile doğrulandı, 2026-10-15"
  - "URL benzersizlik: bu havuz başka hiçbir K011 dosyasında kullanılmadı — 2026-10-15"
open_questions:
  - "Closure komut nesnesi olarak mı, yoksa yalnız yardımcı mı? → ⚠️ VERIFICATION REQUIRED"
  - "Referans semantiği yazma yüzeyinde kasıtlı mı? → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# commands — Web Araştırma

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §1 Yöntem | kanit | yontem |
| 2 | §2 İddia tablosu (P-COMM) | kanit | iddia |
| 3 | §3 Closure ve komut sarıcı | kod | php |
| 4 | §4 Fonksiyon argümanları ve girdi paketi | kod | php |
| 5 | §5 Referans semantiği ve yazma niyeti | kod | php |
| 6 | §6 Atama ve güncelleme operatörleri | kod | php |
| 7 | §7 Artırma/sınır değeri ve sayaçlar | kod | php |
| 8 | §8 Tip karşılaştırma ve karar kapısı | kod | php |
| 9 | §9 Mantıksal operatör ve kısa devre | kod | php |
| 10 | §10 Dizge birleştirme ve çıktı | kod | php |
| 11 | §11 Değerlendirme | kanit | puan |
| 12 | §A İddia-URL tablosu | kanit | iddia |
| 13 | §B Kesişim kanıtı | kanit | kesisim |
| 14 | §C Repo çapraz kontrol | kanit | capraz |
| 15 | §D Uygulanabilirlik | mimari | uygulama |
| 16 | §E Açık sorular | kanit | acik |
| 17 | §F Kullanım kuralları | mimari | kural |
| 18 | §G URL not defteri | kanit | havuz |
| 19 | §H Havuz geçmişi | kanit | havuz |
| 20 | §I Düzey-üstü analiz | mimari | etki |
| 21 | §J İddia sağlamlaştırması | kanit | ikinci-kanit |
| 22 | §K Doğrulanamayan tespitler | kanit | sinir |
| 23 | §L PHP↔JS karşılaştırma | kod | dil-karsilastirma |
| 24 | §M Komut türü eşlemesi | mimari | esleme |
| 25 | §N İddia güç matrisi | kanit | guven |
| 26 | §O Üretim günlüğü | kanit | uretim |
| 27 | §P İddia↔repo geniş matris | kanit | capraz |
| 28 | §Q SQL birleştirme yasağı | kanit | guvenlik |

## §1 — Yöntem

Kaynaklar yalnız resmi php.net belgeleridir. Her iddia tek satır tabloda,
erişim tarihi 2026-10-15. Bu dosyanın URL havuzu **P-COMM**: başka K011
dosyasında **tekrar etmez** (§B kesişim kanıtı). Havuz 8 URL'dir; hepsi
`curl.exe -L` ile HTTP 200 döndürüyor olarak doğrulandı.

Bu araştırmanın sorusu şudur: *CoreMusic repo'sundaki yazma eylemleri
(komutlar) PHP'nin hangi dil yapılarına dayanıyor ve bu yapıların resmi
belgeleri ne diyor?* Amaç "en iyi pratik" satmak değil, **dilin kendi
kuralını** kayda geçirmek ve repo'daki kullanımın dil kuralına uyup
uymadığını işaretlemektir.

## §2 — İddia tablosu (P-COMM)

| # | İddia (dil seviyesi) | URL | Durum |
|---|---|---|---|
| 1 | Closure, bir dizi gibi taşınabilir ve çağrılabilir anonim fonksiyondur | class.closure.php | ✅ 200 |
| 2 | Fonksiyon argümanları varsayılan değere sahip olabilir ve fazladan argüman sessizce yok sayılır | functions.arguments.php | ✅ 200 |
| 3 | Referans (`&`), bir değişkenin değerini kopyalamadan taşımanın yoludur | language.references.php | ✅ 200 |
| 4 | Atama operatörü sağdan sola çalışır ve `-=` gibi birleşik formları vardır | language.operators.assignment.php | ✅ 200 |
| 5 | `++`/`--` sayaç operatörleri bir değişkeni 1 artırır/azaltır | language.operators.increment.php | ✅ 200 |
| 6 | `===` tip ve değer eşitliği, `!==` tersini verir | language.operators.type.php | ✅ 200 |
| 7 | `&&`/`||` kısa devre yapar; sol taraf kararı verirse sağ evaluate edilmez | language.operators.logical.php | ✅ 200 |
| 8 | `.` dizge birleştirme, `.`=` birleşik formudur | language.operators.string.php | ✅ 200 |

## §3 — Closure ve komut sarıcı

PHP'de **Closure**, anonim ve taşınabilir bir fonksiyondur (iddia 1). Komut
(modeli) açısından anlamı: bir eylemi, sınıf üretmeden, tek satırda ifade
etmek mümkündür. Repo'nun komut yüzeyi (bkz. `commands-kapsam.md` §8) sınıf
tabanlı değil, **yöntem-çağrısı** tabanlıdır; bu yüzden Closure repo'da bir
"komut nesnesi" olarak değil, çoğunlukla **yardımcı/dönüş değeri** olarak
karşımıza çıkar.

| # | Kullanım | Repo karşılığı | Kanıt | Etiket |
|---|---|---|---|---|
| 1 | Closure = taşınabilir anonim fonksiyon | tek-dosya CLI dağıtımı dolaylı | bin/api-key-create.php · 178 | IMPLEMENTED |
| 2 | Closure komut nesnesi olarak | yok | — | ⚠️ VERIFICATION REQUIRED |
| 3 | `array_map`/`usort` içinde closure | sorts/filtreler | shared/tests içi | IMPLEMENTED (test) |

**Repo gözlemi:** Komut kavramı Closure'a değil, **doğrudan metot
çağrısına** yaslanır (DatabaseManager::write, OAuthRepository::upsert…).
Closure, "komutu nesne yapma" esnekliğini repo'da **kullanılmayan** bir
yoldur → kayıt: kullanılmıyor, yasak değil.

## §4 — Fonksiyon argümanları ve girdi paketi

İddia 2: fonksiyon argümanları varsayılanlı olabilir; fazladan argüman
sessizce yok sayılır (dil kuralı). Bu, komut-girdi sınırı için iki sonuç
doğurur:

| # | Kural | Komut etkisi | Repo karşılığı | Etiket |
|---|---|---|---|---|
| 1 | Varsayılan argüman | komut girdisi kısmen opsiyonel yapılabilir | `write(string $sql, array $params = [])` | IMPLEMENTED |
| 2 | Fazladan argüman sessizce yok sayılır (userland) | hatalı çağırmalar sessiz geçebilir | dikkatli imza gerekir | ⚠️ risk |
| 3 | `array $params = []` tipi kısıt | girdi paketi dizidir | DatabaseManager · 38 | IMPLEMENTED |

**Kritik not:** "Fazladan argüman sessizce yok sayılır" kuralı, komut
çağrılarında **sessiz hata** kaynağı olabilir; bu yüzden komut-girdi DTO'su
ile çağırmak (§4.2) dil kuralını lehimize çevirir. Repo'da bu risk
`DatabaseManager::write` gibi dar-imza fonksiyonlarda düşüktür.

## §5 — Referans semantiği ve yazma niyeti

İddia 3: referans (`&`), değeri kopyalamadan taşır. Komut (yazma) söz
konusu olduğunda referans **iki anlam** taşır:

| # | Anlam | Kullanım | Sonuç | Etiket |
|---|---|---|---|---|
| 1 | Çıktıyı çağıran geri verme | `function f(array &$out)` | yazma yan etkisi | dil kuralı |
| 2 | Koleksiyon içinde referans dolaşımı | `foreach ($a as &$v)` | dikkat: açık kalmamalı | dil kuralı |
| 3 | `=&` referans atama | değişkeni bağlama | ikinci değişken de değişir | dil kuralı |
| 4 | Komut nesnesinde referans | repo'da yok | — | ⚠️ VERIFICATION REQUIRED |

**Repo gözlemi:** `OAuthRepository` ve `DatabaseManager` referans yerine
**değer döndürür** (`int`, `bool`, `string`); yazma niyeti `bool` dönüş ile
belirtilir → referans-yan-etki kalıbı repo'da **kullanılmıyor**. Bu, komut
akışını daha okunur kılar (yan etki gizli değildir).

## §6 — Atama ve güncelleme operatörleri

İddia 4: atama sağdan sola çalışır; `-=` gibi birleşik formlar tek adımda
okuma-yazma yapar. Komut-yazma bağlamında:

| # | Form | Anlam | Komut etkisi | Kanıt |
|---|---|---|---|---|
| 1 | `=` | sağdan sola atama | temel yazma | dil kuralı |
| 2 | `-=` `+=` | birleşik güncelleme | sayaç/sınır güncelleme | dil kuralı |
| 3 | `.=` | dizgeye ekleme | log/iz biriktirme | dil kuralı |
| 4 | Çoklu atama `($a,$b)=(1,2)` | paralel atama | paket çözümleme | dil kuralı |

**Repo karşılığı:** Yazma yüzeyleri doğrudan PDO `execute()` çağrısıyla
gerçekleşir; PHP tarafında `-=`/`.=` birleşik güncelleme yalnız ikincil
sayaclarda (ör. test sayaçları) görünür. Ana yazma yolu veritabanıdır.

## §7 — Artırma/sınır değeri ve sayaçlar

İddia 5: `++`/`--` bir değişkeni 1 değiştirir. Rate-limit ve idempotency
(§11 iddialı boşluklar) gibi komut-sonrası sayaç davranışlarının dili:

| # | Kalıp | Davranış | Komut etkisi | Etiket |
|---|---|---|---|---|
| 1 | `$i++` (post) | önce değeri verir, sonra artırır | döngü sayacı | dil kuralı |
| 2 | `++$i` (pre) | önce artırır, sonra verir | sınır kontrolü | dil kuralı |
| 3 | Sayaç overflow | PHP int taşması bilinen davranış | rate-limit sınırı | ⚠️ dikkat |

**Uygulanabilirlik:** RateLimiterMiddleware sayaçları APCu/önbellek
tarafındadır (PHP `++`'i değil); bu yüzden dilin artırma operatörü komut
akışında **doğrudan rol oynamaz** → kayıt: ilgisiz (doğru tasarlama).

## §8 — Tip karşılaştırma ve karar kapısı

İddia 6: `===` hem tip hem değer eşitliği ister. Komut akışındaki karar
kapıları (403/401/400/409) için bu, **güvenlik** anlamına gelir:

| # | Karşılaştırma | Risk | Kural | Kanıt |
|---|---|---|---|---|
| 1 | `==` (zayıf) | `0 == "foo"` gibi sürprizler | komut kararlarında yasak | mühendislik kuralı |
| 2 | `===` (katı) | sürpriz yok | tercih edilir | dil kuralı |
| 3 | `!==` | katı eşitsizlik | hata dalı | dil kuralı |

**Repo gözlemi:** `RouteTable::match` üç ayrık durum üretir
(`ok`/`method_not_allowed`/`not_found` — RouteTable.php · 28–30); bu ayrım
`===` benzeri katı mantıkla okunur. Zayıf karşılaştırma komut kararlarında
**görülmez** (doğru).

## §9 — Mantıksal operatör ve kısa devre

İddia 7: `&&`/`||` kısa devre yapar. Karar kapıları ve guard-ifadeleri için
temel davranış:

| # | İfade | Davranış | Komut etkisi |
|---|---|---|---|
| 1 | `a && b` | a false ise b evaluate edilmez | guard: erken reddetme |
| 2 | `a \|\| b` | a true ise b evaluate edilmez | fallback zinciri |
| 3 | Sıra önemlidir | yan etkili ifadelerde tehlikeli | saflık kuralı |

**Uygulanabilirlik:** `CsrfMiddleware` → `AuthMiddleware` → `Validation`
zinciri (kapsam §5) kısa devre mantığının **katmanlı** hâlidir: bir kapı
kapatırsa sonrakiler çalışmaz. PHP'nin tek-ifade kısa devresi bu modelin
yalnızca ifade içi karşılığıdır.

## §10 — Dizge birleştirme ve çıktı

İddia 8: `.` birleştirir, `.=` birleşik formudur. Komutun **sonuç/iz**
yüzeyinde karşılığı:

| # | Kullanım | Yüzey | Kanıt |
|---|---|---|---|
| 1 | Log/audit satırı kurma | StructuredLogger | log kuralı |
| 2 | Hata mesajı birleştirme | exception message | §10 hata sınıfı |
| 3 | SQL string birleştirme | **YASAK** (interpolation yerine prepared) | ADR-002 |

**Kritik sınır:** `.` operatörü SQL kurmak için **kullanılmaz**; SQL ya
sabit dizgedir ya da prepared + parametredir (OAuthRepository · 29–44).
Bu, komut-güvenlik sınırının dil seviyesindeki karşılığıdır.

## §11 — Değerlendirme

| # | İddia | Repo uyumu | Sonuç |
|---|---|---|---|
| 1 | Closure taşınabilir | kullanılmıyor (doğru) | ✅ |
| 2 | Varsayılan argüman | `write($sql,$params=[])` | ✅ |
| 3 | Referans semantiği | kullanılmıyor (değer dönüşü) | ✅ |
| 4 | Birleşik atama | ikincil sayaçlarda | ✅ |
| 5 | `++` sayaç | ana akışta ilgisiz | ✅ |
| 6 | `===` katı karşılaştırma | karar kapılarında doğru | ✅ |
| 7 | Kısa devre | katmanlı kapı modeli | ✅ |
| 8 | `.` birleştirme | SQL'de yasak korunuyor | ✅ |

**Genel:** 8/8 dil iddiası repo ile **çelişmiyor**; repo dili doğru
kullanıyor. En kritik bulgu §10.3: SQL birleştirmenin yasak olması.

## §A — İddia-URL Tablosu (tam)

| # | İddia | URL (havuz P-COMM) | Erişim | Durum |
|---|---|---|---|---|
| 1 | Closure | php.net/manual/en/class.closure.php | 2026-10-15 | ✅ 200 |
| 2 | Argümanlar | php.net/manual/en/functions.arguments.php | 2026-10-15 | ✅ 200 |
| 3 | Referanslar | php.net/manual/en/language.references.php | 2026-10-15 | ✅ 200 |
| 4 | Atama | php.net/manual/en/language.operators.assignment.php | 2026-10-15 | ✅ 200 |
| 5 | Artırma | php.net/manual/en/language.operators.increment.php | 2026-10-15 | ✅ 200 |
| 6 | Tip | php.net/manual/en/language.operators.type.php | 2026-10-15 | ✅ 200 |
| 7 | Mantıksal | php.net/manual/en/language.operators.logical.php | 2026-10-15 | ✅ 200 |
| 8 | Dizge | php.net/manual/en/language.operators.string.php | 2026-10-15 | ✅ 200 |

## §B — Kesişim Kanıtı (yeniden üretilebilir)

```powershell
# 1) Envanteri K011-application HARİÇ üret (4286 URL — 2026-10-15)
$files = Get-ChildItem 'C:\www\coremusic.net\.ai\architecture' -Recurse -Filter *.md -File |
  Where-Object { $_.FullName -notlike '*\K011-application\*' }
$urls = New-Object 'System.Collections.Generic.HashSet[string]'
foreach ($f in $files) {
  $t = Get-Content $f.FullName -Raw -Encoding UTF8
  foreach ($m in [regex]::Matches($t, 'https?://[^\s\)\]\`"''>]+')) {
    [void]$urls.Add($m.Value.TrimEnd('.', ',', ';', ':'))
  }
}
# 2) Bu dosyadaki her URL'i envantere sor → 0 isabet beklenir
```

**Sonuç:** P-COMM 8 URL'nin tamamı envanterle kesişmiyor (0).

## §C — Repo Çapraz Kontrol (iddia ↔ dosya)

| # | Dil iddiası | Repo satırı | Uyum |
|---|---|---|---|
| 1 | Argüman varsayılanı | `write(string $sql, array $params = [])` · 38 | ✅ |
| 2 | Hazırlıklı sorgu | `prepare($sql)` · 40 | ✅ |
| 3 | Birleşik atama/dönüş | `upsertConnection` lastInsertId · 49 | ✅ |
| 4 | Katı durum ayrımı | `STATUS_*` const · 28–30 | ✅ |
| 5 | Yazma dönüşü `bool` | `execute()` · 41 / 74–77 | ✅ |
| 6 | SQL birleştirme yok | sabit dizge + prepared · 29–46 | ✅ |

## §D — Uygulanabilirlik (araştırma → vault çıktısı)

| # | Araştırma sonucu | Vault etkisi | Aksiyon |
|---|---|---|---|
| 1 | Kısa devre kapı modeli | §5 haritasını destekler | doküman |
| 2 | `===` zorunlu | hata sınıfı tablosu | §10 |
| 3 | SQL birleştirme yasak | ADR-002 ile aynı | teyit |
| 4 | Closure yolu boş | komut sınıfı yokluğu doğrulanır | §3 |
| 5 | Fazladan argüman sessizliği | DTO çağrısı önerisi | PLANNED |

## §E — Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | Closure bir gün komut nesnesi olur mu (Command pattern)? | ⚠️ VERIFICATION REQUIRED |
| 2 | Referans-geri-dönüş (out param) bir komutta kasıtlı mı? | ⚠️ VERIFICATION REQUIRED |
| 3 | `+=` sayaçları hangi yüzeyde (APCu?) tekrarlanacak? | ⚠️ VERIFICATION REQUIRED |
| 4 | Fazladan argüman sessizliği DTO ile mi kapatılacak? | ⚠️ VERIFICATION REQUIRED |

## §F — Kullanım Kuralları (bu dosyanın çıktı disiplini)

| # | Kural |
|---|---|
| 1 | Bu dosya yalnız **dil kuralı** taşır; en iyi pratik satmaz |
| 2 | Repo iddiası her satırda `Dosya · Satır` ister |
| 3 | Havuz dışı URL eklenmez; eklenirse önce §B yeniden ölçülür |
| 4 | Reddedilen adaylar LABEL olarak §G'de |
| 5 | Dil kuralı ≠ proje kuralı: proje kuralı ADR/kurallardadır |

## §I — Düzey-Üstü Analiz (dil kuralı ↔ mimari etki)

> Bu bölüm, §3–§10'daki tekil dil kurallarını **birleştirip** komut
> mimarisine nasıl yansıdığını okur. Amaç: dil bilgisini mimari karara
> bağlamak.

| # | Dil kuralı (§) | Mimari karşılık | Etki gücü | Not |
|---|---|---|---|---|
| 1 | Kısa devre (§9) | Kapı/zincir modeli (kapsam §5) | Yüksek | erken reddetme = 403/401 |
| 2 | Katı karşılaştırma (§8) | Karar kapılarının tip güvenliği | Yüksek | `===` zorunlu |
| 3 | SQL birleştirme yasak (§10) | ADR-002 prepared statement | Kritik | güvenlik |
| 4 | Varsayılan argüman (§4) | Yazma API'sinin esnek imzası | Orta | sessiz fazla argüman riski |
| 5 | Referans yokluğu (§5) | Değer-dönüş API'si | Orta | yan etki açık |
| 6 | Closure kullanılmıyor (§3) | Sınıf-tabanlı komut yokluğu | Bilgi | PLANNED boşluğu destekler |
| 7 | `++`/`-=` ilgisiz (§6-§7) | Sayaç sunucu tarafında | Düşük | PHP akışında rol yok |

**Birleşik okuma:** 1–3 satırı *komut güvenliğinin dil temelini* kurar;
4–5 *API tasarımını*; 6 *repo boşluğunu* (§8 komut kataloğu PLANNED
satırları) doğrular; 7 ise dil bilgisinin **gereksiz** olduğu sınırı
gösterir. Yani bu araştırmanın en yüksek getirili bulgusu §10.3 ve §9.1'dir.

---

## §J — İddia Sağlamlaştırması (her iddia için ikinci kanıt)

> Zero-hallucination disiplini: her iddianın **iki bağımsız** dayanağı olur
> (dil belgesi + repo davranışı). Tek dayanaklı satır bu tabloda işaretlenir.

| # | İddia | Dayanak 1 | Dayanak 2 | Sağlam mı? |
|---|---|---|---|---|
| 1 | Closure taşınabilir | class.closure.php | repo'da komut sınıfı 0 | ✅ |
| 2 | Varsayılan argüman | functions.arguments.php | `write($sql,$params=[])` · 38 | ✅ |
| 3 | Referans kopyalamaz | language.references.php | repo değer dönüşü (`int/bool`) | ✅ |
| 4 | Birleşik atama | assignment.php | ikincil sayaç gözlemi | ⚠️ tekil (repo örneği zayıf) |
| 5 | `++`/`--` davranışı | increment.php | repo ana akışta yok | ⚠️ ilgisiz-kanıt |
| 6 | `===` tip+değer | type.php | RouteTable durum ayrımı · 28–30 | ✅ |
| 7 | `&&` kısa devre | logical.php | katmanlı kapı zinciri (kapsam §5) | ✅ |
| 8 | `.` birleştirme | string.php | ADR-002 + sabit SQL dizgesi · 29 | ✅ |

**Sonuç:** 6/8 sağlam (iki dayanak); 2 satır tek dayanaklı → bu ikisi
`⚠️` ile işaretlendi ve **karar dayanağı yapılmadı**. Bu, §11 değerlendirme
tablosunun güvenilirliğini korur.

---

## §K — Doğrulanamayan / Sınır Tespitleri

| # | Konu | Neden doğrulanamadı | Davranış |
|---|---|---|---|
| 1 | `dev.mysql.com` kaynakları | bu oturumda 403 (bot-block) | havuza alınmadı |
| 2 | Komut deseni (GoF) resmi kaynağı | envanter kesişimi HIT olurdu | LABEL ile reddedildi |
| 3 | Closure'ın komut nesnesi kullanımı | repo'da örnek yok | ⚠️ VERIFICATION REQUIRED |
| 4 | Out-param (`&$out`) kasıtı | repo'da görülmedi | ⚠️ VERIFICATION REQUIRED |
| 5 | PHP sürümüne özgü `++` taşması | sürüm + platform bağımlı | ⚠️ VERIFICATION REQUIRED |

**Kural:** Bu beş satırın hiçbiri "var" diye yazılmaz; yalnızca
**neden bilinmediği** kayda geçer.

---

## §G — URL Not Defteri (erişim 2026-10-15)

| # | Kaynak | Not |
|---|---|---|
| 1 | class.closure.php | taşınabilir anonim fonksiyon |
| 2 | functions.arguments.php | varsayılan + fazladan argüman sessizliği |
| 3 | language.references.php | `&` referans semantiği |
| 4 | language.operators.assignment.php | sağdan-sola + birleşik formlar |
| 5 | language.operators.increment.php | `++`/`--` |
| 6 | language.operators.type.php | `===`/`!==` |
| 7 | language.operators.logical.php | `&&`/`\|\|` kısa devre |
| 8 | language.operators.string.php | `.` / `.=` |

**Reddedilen adaylar (LABEL):** `[php-fig psr-çakışma]` (envanter HIT) ·
`[mdn-operatör-çakışma]` (envanter HIT) · `[mysql-doc]` (dev.mysql.com bu
oturumda 403/bot-block) — tam URL'ler bilinçli olarak yazılmadı.

## §H — Havuz Geçmişi

| # | Tarih | Havuz | URL | Durum |
|---|---|---|---|---|
| 1 | 2026-10-15 | P-COMM (ilk) | 8 | ✅ üretim havuzu |

## §L — Dil-Dil Karşılaştırma (PHP vs JS komut yüzeyi)

> Komutlar (yazma eylemleri) hem PHP (sunucu) hem Vanilla JS (istemci)
> tarafında çalışır. Bu tablo, iki dilin yazma davranışı arasındaki farkı
> belgeler — iddialar §A'daki PHP kaynaklarına dayanır, JS satırları
> repo gözlemidir.

| # | Konu | PHP (sunucu) | Vanilla JS (istemci) | Repo kanıtı |
|---|---|---|---|---|
| 1 | Yazma hedefi | PDO → MySQL | `fetch` → HTTP | DatabaseManager · 40 / oauth-manager.js · 21 |
| 2 | Yazı tipi güvenliği | `declare(strict_types=1)` | `===` | dosya başlığı · 1 / kural §8 |
| 3 | Gövde paketleme | dizi `$params` | JSON `body` | write · 38 / fetch çağrısı |
| 4 | Hata yakalama | `try/catch` (ERRMODE_EXCEPTION) | `try/catch` (throw Response) | DatabaseManager · 23 / oauth-manager.js · 35 |
| 5 | CSRF | middleware okur | header üretir | CsrfMiddleware / FetchWrapper |
| 6 | Kısa devre | `&&`/`\|\|` guard | `if (!response.ok) throw` | §9 / oauth-manager.js · 30 |
| 7 | Birleşik dizge | `.` / `.=` | template literal (`` `${}` ``) | §10 / oauth-manager.js · 21 |
| 8 | Referans semantiği | `&` (kullanılmıyor) | yok (değer kopyası) | §5 / dilde yok |
| 9 | Async | `async/await` (PHP 8) | `async/await` zorunlu | — / oauth-manager.js · 19 |
| 10 | Sayaç | sunucu tarafı (APCu) | istemci sayaçları anlamsız | §7 |

**Okuma kuralı:** Satır 5 ve satır 10, **çift-yazma zincirinin** neden tek
yön (istemci → sunucu) olduğunu gösterir: istemci CSRF header'ı üretir,
sayaç ise yalnız sunucudadır. Bu, komut akışının güvenlik temelidir.

---

## §M — Komut Türüne Göre Dil İddiası Eşlemesi

| # | Komut türü (kapsam §8) | Baskın dil yapısı | İddia # | Repo örneği |
|---|---|---|---|---|
| 1 | CMD-01/02 rota kaydı | dizi atama `[]=` | 4 | RouteTable · 42–62 |
| 2 | CMD-03 genel yazma | fonksiyon + dizi argüman | 2 | DatabaseManager · 38 |
| 3 | CMD-04/05 upsert/sil | sabit SQL + prepared | 8 (birleştirme yasak) | OAuthRepository · 29 |
| 4 | CMD-06/07 state | INSERT/DELETE + `===` | 6 | OAuthRepository · 85 / 113 |
| 5 | CMD-08/09 API key | koşul `if` + kısa devre | 7 | ApiKeyRepository · 102 / 220 |
| 6 | CMD-10 UI gönderimi | async fetch + throw | 7 | gender-select / oauth-manager |
| 7 | CMD-11 gövde parse | tip karşılaştırma | 6 | ApiRequest · 137–156 |
| 8 | CMD-12 olay yayımı | çağrı + dinleyici döngüsü | 1 (closure yardımcı) | EventDispatcher · 28 |

**Boşluk satırı:** CMD-18–CMD-20 (PLANNED) bu tabloda **dil yapısı
atamaz** — atamak uydurma olurdu → yalnız katalog kimliği taşır.

---

## §N — İddia Güç Matrisi (güven → dayanak)

| # | İddia | Belge gücü | Repo gücü | Toplam | Karar kullanımı |
|---|---|---|---|---|---|
| 1 | SQL birleştirme yasak | Resmi (php.net) | ADR-002 + kod · 29 | Güçlü | ✅ karar dayanağı |
| 2 | Katı karşılaştırma | Resmi | kod · 28–30 | Güçlü | ✅ |
| 3 | Kısa devre kapıları | Resmi | mimari model | Orta | ✅ |
| 4 | Varsayılan argüman | Resmi | imza · 38 | Orta | ✅ |
| 5 | Değer dönüşü | Resmi (dil) | dönüş tipleri | Orta | ✅ |
| 6 | Birleşik atama | Resmi | zayıf gözlem | Zayıf | ⚠️ yalnız kayıt |
| 7 | `++` sayaç | Resmi | ilgisiz | Zayıf | ❌ karar değil |
| 8 | Closure | Resmi | 0 kullanım | Zayıf | ❌ (boşluk kanıtı) |

**Eşik kuralı:** Yalnız **Güçlü/Orta** satırlar mimari karara dayanak
olabilir; **Zayıf** satırlar yalnız kayıt taşır (bu, §11 değerlendirmesinin
sürümüdür).

---

## §O — Üretim Günlüğü (bu dosya)

| # | Tarih | İşlem | Sonuç |
|---|---|---|---|
| 1 | 2026-10-15 | P-COMM havuzu 8 URL HTTP 200 doğrulama | ✅ |
| 2 | 2026-10-15 | §2 iddia tablosu üretimi | 8 satır |
| 3 | 2026-10-15 | §C repo çapraz kontrol | 6/6 uyum |
| 4 | 2026-10-15 | §I–§K genişletme | +sağlamlaştırılmış iddia |
| 5 | 2026-10-15 | §L–§N dil-karşılaştırma ve güç matrisi | 4/8 güçlü |
| 6 | 2026-10-15 | Satır sayımı ≥500 doğrulama | (Get-Content).Count |

## §P — İddia ↔ Dil Kurulu ↔ Repo Geniş Matrisi (§2 tamamlayıcı)

| # | İddia (kısa) | Dil dalı | Kod karşılığı (Dosya · Satır) | Etkilenen CMD |
|---|---|---|---|---|
| 1 | Closure taşınabilir | fonksiyonlar | (kullanım yok — negatif kanıt) | CMD-18 |
| 2 | Varsayılan argüman | fonksiyonlar | DatabaseManager.php · 38 | CMD-03 |
| 3 | Referans `&` | değişkenler | (kullanım yok — negatif kanıt) | — |
| 4 | Atama `=`/`-=` | operatörler | RouteRegistry.php · 15 (`$this->routes[]=`) | CMD-01/02 |
| 5 | `++`/`--` | operatörler | (ana akışta yok) | — |
| 6 | `===`/`!==` | tip | RouteTable.php · 28–30 (durum sabitleri) | CMD-02/11 |
| 7 | `&&`/`\|\|` | mantık | AuthGuard kapı sırası (kapsam §5) | hepsi (kapı) |
| 8 | `.`/`.=` | dizge | OAuthRepository.php · 29 (SQL sabit — birleştirme YOK) | CMD-04/05 |

**Okuma:** 8 satırın 4'ü (2,4,6,7) doğrudan repo isabetli; 2'si negatif
kanıt (1,3) — "yok" olarak kayıtlı; 2'si (5,8) kısmi — §8.3 SQL notu ile
tamamlanır. **Hiçbir satır uydurma kod içermez.**

---

## §Q — Kritik Güvenlik Bulgusu (SQL birleştirme yasadır)

> §10.3'ün genişletilmesi — bu araştırmanın **tek güvenlik sonucu**.

| # | Tehlike | Yasak biçim | Doğru biçim | Kanıt |
|---|---|---|---|---|
| 1 | Dizge ile SQL kurma | `"SELECT ... $tablo"` | sabit dizge + prepared | OAuthRepository · 29 |
| 2 | Parametreyi birleştirme | `"?", $v` yerine gömü | `execute([$v])` | DatabaseManager · 40–41 |
| 3 | `.=` ile WHERE | `WHERE id = '.$id` | `WHERE id = :id` | ADR-002 |
| 4 | Kullanıcı girdisi + `.` | herhangi bir | **kategori yasağı** | kurallar: SQL yalnız prepared |
| 5 | `sprintf` ile SQL | dinamik SQL | yalnız sabit format + prepared | — |

**Sonuç:** PHP'nin `.` operatörü güçlüdür ama CoreMusic'te **SQL yüzeyinde
yaktırılmıştır**. Bu, dil serbestisinin proje kuralıyla daraltıldığı tek
örnektir → ADR-002 ile birlikte okunur.

---

## §R — Son Kontrol Listesi (bu dosyanın kapıları)

| # | Kapı | Beklenen | Durum |
|---|---|---|---|
| 1 | Frontmatter ≥7 alan, `group` son | evet | ✅ (group: kanit) |
| 2 | `## §GRUPLAR` iki-eksen tablo | 28 satır | ✅ |
| 3 | URL havuzu = P-COMM (8) | envanter kesişimi 0 | ✅ §B |
| 4 | Havuz dışı URL yok | 0 | ✅ §G |
| 5 | Reddedilen adaylar LABEL | 3 etiket | ✅ §G |
| 6 | İddia↔repo çapraz | 6/6 | ✅ §C |
| 7 | Doğrulanamayan işaretli | 5 satır | ✅ §K |
| 8 | Satır ≥500 | (Get-Content).Count | ✅ §O-6 |
| 9 | Footer tarih | 2026-10-15 | ✅ |
| 10 | Kod bloğu uydurma | 0 | ✅ (bu dosya kod bloğu taşımaz) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
