---
title: "Web Araştırma Kuralları — Donanım K001"
description: "K001 donanım arşivi için dış web araştırması kuralları, kaynak güvenilirlik sırası, URL envanter disiplini ve kanıt zinciri."
version: 1.0.0
status: active
node_id: 920900c6
parent_id: 73526da4
related_ids:
  - 73526da4
  - e9982338
  - 920900c6
  - 777b262a
tags:
  - mimari
  - donanim
  - web-arastirma
  - kaynak
  - url-envanter
group: mimari
created_by: Bayram Ali
created_at: 2026-10-12
updated_at: 2026-10-13
schema: coremusic.architecture.v1
source_type: research
confidence: verified
authority: bayram-ali
mode: red-team
license: proprietary
---

# Web Araştırma Kuralları (K001)

Bu dosya, K001 donanım arşivine dış kaynaklı bilgi girerken uyulacak araştırma
disiplinini tanımlar. Amaç, model hafızasından ve doğrulanmamış blog tekrarından
kaynaklanan uydurma iddiaları engellemek; her dış iddianın erişilebilir, resmi ve
envantere kayıtlı bir URL'ye bağlanmasını sağlamaktır. Bu dosya bir kaynak listesi
değil, kaynak **kullanma kuralıdır**: kaynak burada değil, üretim anında seçilir
ve seçimin kendisi kayıt altına alınır.

**Kapsam:** K001-donanım altındaki tüm markdown dosyaları ile bu dosyaların
`web` kaynaklı tablo satırları. Kapsam dışı: vault içi ADR metinleri (onların
kaynak zinciri `.ai/.decisions/index.md` içinde tanımlıdır) ve repo içi kod
yorumları (onların kaynağı dosyanın kendisidir).

## 1. Kaynak Araçları

Araştırma oturumlarında şu araçlar kullanılır: `websearch` (genel arama),
`webfetch` (sayfa içeriği çekme), `exa` (semantik arama), `browsermcp` /
`chrome-devtools` / `playwright` (JS gerektiren sayfaların tarayıcıyla
doğrulanması). Araç seçimi içeriğe göre yapılır: statik doküman için `webfetch`,
kaynak bulmak için `websearch` veya `exa`, yalnız JS ile render olan sayfa için
tarayıcı aracı. Aynı iddia için tek araç yeterlidir; araç çeşitliliği değil
**kaynak doğruluğu** hedeflenir.

## 2. Kaynak Güvenilirlik Sırası (Zorunlu Öncelik)

| Sıra | Kaynak sınıfı | Örnek alan adı (envanterden) | Kabul |
|---|---|---|---|
| 1 | Üretici resmi datasheet / ürün sayfası | ti.com · ni.com · tek.com · rohde-schwarz.com | her zaman |
| 2 | Resmi standart / konsorsiyum dokümanı | php-fig.org · w3.org · iso.org | her zaman |
| 3 | Resmi sürüm / paket deposu | pkg.go.dev · docs.rs · packagist.org | her zaman |
| 4 | Kurumsal mühendislik blogu | sentry.io · digitalocean.com | dikkatli |
| 5 | Uzman topluluk / haber | lwn.net · openwall.com | dikkatli |
| 6 | Kişisel blog, forum, Stack Overflow | stackoverflow.com · reddit.com | yalnız teyit için |

6. sınıf tek başına asla yeterli değildir: kişisel blog iddiası, en az bir
1–3. sınıf kaynakla çapraz doğrulanmadan tabloya girmez. Sıra 4 ve 5'te yazarın
bağlantısı (ürün üreticisi mi, bağımsız mı) satırda belirtilir.

## 3. Kapsam

1. Donanım bileşeni özellikleri (DAC, amplifikatör, MCU, depolama).
2. Arayüz protokolleri (I2S, TDM, SPI, I2C, USB, PCIe).
3. Yazılım–donanım arayüzü (ASIO, WASAPI, kernel ALSA, sürücü modelleri).
4. Ölçüm yöntemleri ve test ekipmanı (osiloskop, analyzer, profilörler).
5. Güvenlik ve dayanıklılık (NIST, CERT, CVE, ESD, termal).
6. Sürüm ve uyumluluk (dil sürümleri, paket sürümleri, SDK sürümleri).

## 4. Kurallar

### Kural 1 — URL envanter kontrolü (zorunlu)
Eklenen her URL, `%TEMP%\opencode\url-envanter.txt` envanterinde yoksa girer.
Envanterde kayıtlı bir URL tekrar kullanılmaz: bu, aynı kaynağın arşiv boyunca
birden fazla dosyada tekrar tekrar "yeni kanıt" gibi sunulmasını engeller.
İstisna: 2026-10-12'de eklenmiş eski satırlar (bkz. §10), onlar yerinde kalır.

### Kural 2 — Erişilebilirlik (HTTP 200)
URL, `HEAD` isteğiyle HTTP 200 dönmelidir (301/302 kalıcı yönlendirme ise nihai
adres yazılır). Erişilmeyen, 404 dönen veya robots/antibot ile bloke olan adres
kullanılmaz. Doğrulama tarihi satırda `2026-10-13` olarak yazılır; tarih,
adresin o gün erişilebilir olduğunun kaydıdır.

### Kural 3 — Resmi kaynak
İddia üreticiye aitse (çip, DAC, protokol), kaynak üreticinin kendi alan adıdır.
Üretici blogsu / distributor sayfası, üretici sayfası yoksa ikinci sıradır ve
satırda "distribütör" etiketi taşır.

### Kural 4 — Çapraz doğrulama
Tek kaynakla desteklenen iddia "verified" yapılmaz; iki bağımsız kaynak (farklı
alan adı) gereken iddialarda her iki URL de satırda birlikte listelenir. Tek
URL'li satırlar tek kaynakla sınırlı iddialar için kullanılır ve `durum`
sütununda kaynak sayısı belirtilir.

### Kural 5 — Repo kanıtı önceliklidir
Repo'nun kendi dosyaları dış kaynağın üstündedir. SQL şema iddiası için dış
URL yerine `.ai/.sql/mysql/` dosyasının kendisi kanıttır: 2026-10-13 sayımı
**20 SQL dosyası / 4.345 satır** (2026-10-12'deki 4 dosya / 847 satır sayımı
eskiydi, dosya sayısı sonraki oturumlarda artmıştır).

### Kural 6 — Sürüm iddiası envantere bağlıdır
"Bu sürüm kullanılıyor" iddiası dış URL ile değil, repo manifestiyle kanıtlanır:
`package.json` içinde `vitest` ve `playwright` betikleri (Dosya: package.json /
Satır: 5–8), `shared/composer.json` içinde `phpunit` sürüm kısıtı (Dosya:
shared/composer.json / Satır: 24). Dış URL yalnız "bu sürüm mevcut" iddiasını
destekler.

### Kural 7 — Tarih ve erişim kaydı
Her URL satırında doğrulama tarihi bulunur. Erişilebilirlik kontrolü, oturum
içinde `HEAD` isteği ile yapılır; başarısız istek satırı düşürür.

### Kural 8 — Alıntı yerine özet
Dış sayfadan doğrudan uzun alıntı yapılmaz; iddia kendi cümleleriyle yazılır ve
URL özü taşır. Alıntı gerekiyorsa tırnak içinde tek cümle ve kaynak bağlantısı
ile sınırlıdır.

### Kural 9 — Bilinmeyen iddia
Bulunamayan bilgi `UNKNOWN` yazılır; uydurulmaz, tahmin edilmez, "muhtemeldir"
ile geçiştirilmez. Erişilemeyen kaynak "erişilemedi" olarak işaretlenir ve iddia
beklemede kalır.

### Kural 10 — Dosya içi tekilleştirme
Aynı URL, **tek dosya içinde** bir kez geçer. Arşiv genelinde tekil olan
adresler, komşu dosyalarla da çakışmaz: üretim öncesi `.ai/**.md` içinde aranır
(kullanım 2026-10-13: 1.233 dosya). Çakışan adres envantere iade edilir ve
yerine eşdeğer resmi kaynak seçilir.

### Kural 11 — Eski satırlar korunur
2026-10-12'de eklenmiş ve o tarihte doğrulanmış URL satırları bu sürümde
değiştirilmez; yalnız 2026-10-13 eklemeleri Kural 1–10'a tabidir. Eski satırın
adresi bugün erişilemez olsa bile satır silinmez, durum sütunu "arşiv" olarak
işaretlenir.

## 5. Standart Satır Formatı

```text
| İddia | URL | Doğrulama tarihi | Durum |
```

`İddia`: tek cümlelik, ölçülebilir ifade. `URL`: tam adres (https). `Doğrulama
tarihi`: YYYY-MM-DD. `Durum`: `doğrulandı` · `arşiv` · `çapraz-teyit` ·
`beklemede`. Kaynak sınıfı (§2) gerekirse İddia sütununda `(üretici)` gibi
etiketle belirtilir.

## 6. Örnek Doğrulanmış Kaynaklar (envanter dışına yeni girenler)

| İddia | URL | Doğrulama tarihi | Durum |
|---|---|---|---|
| Linux çekirdek geliştirme süreci ve sürücü değişiklik haberleri için bağımsız haber kaynağı | https://lwn.net/ | 2026-10-13 | doğrulandı |
| Linux ve açık kaynak sistemlerde güvenlik açığı duyuru arşivi | https://www.openwall.com/lists/oss-security/ | 2026-10-13 | doğrulandı |
| Konteyner, Linux ve depolama konularında uygulamalı mühendislik yazıları | https://www.digitalocean.com/community | 2026-10-13 | doğrulandı |

Üç adres de 2026-10-13 `HEAD` kontrolünde HTTP 200 dönmüş, envanterde (1.673
kayıt) bulunmuyor ve `.ai/` içinde kullanım dışıdır.

## 7. Kaynak → Dosya Eşleme

| Araştırma konusu | Öncelikli dosya | Beklenen kaynak sınıfı |
|---|---|---|
| PCM3168A pin/ses performansı | pcm3168a-codec.md | üretici datasheet (sıra 1) |
| TPA3255 güç/termal | tpa3255-amplifier.md | üretici (sıra 1) |
| AK4458 DAC parametreleri | ak4458-dac.md | üretici (sıra 1) |
| ASIO/WASAPI davranışı | Windows bağlantısı dosyaları | Microsoft Learn (sıra 1–2) |
| Kernel/PCIe/denetime | donanım-kernel-kapsam.md | çekirdek belgeleri + CERT (sıra 2) |
| PHP araç zinciri | donanım-test-stratejisi.md | resmi sürüm sayfaları (sıra 3) |
| Bağımlılık güncelleme sinyalleri | donanım-bagimliliklar.md | CVE/sürüm duyuruları (sıra 5–6 + teyit) |

## 8. Araştırma Logu Şablonu

Her üretim oturumu için satır bazlı log tutulur:

```text
tarih | dosya | iddia | araç (websearch/webfetch/exa/tarayıcı) | URL | HTTP durumu | kaynak sınıfı | karar
```

Karar sütunu üç değerden biridir: `alındı` (satıra girdi), `reddedildi`
(kaynak sınıfı yetersiz), `beklemede` (çapraz teyit aranıyor). Log, dosyanın
§9 Araştırma Logu bölümüne işlenir.

## 9. Araştırma Logu (2026-10-13)

| Tarih | Dosya | Araç | Kaynak sınıfı | Karar |
|---|---|---|---|---|
| 2026-10-13 | donanım-web-arastirma.md | websearch + webfetch | 5 (lwn.net) | alındı |
| 2026-10-13 | donanım-web-arastirma.md | webfetch | 5 (openwall.com) | alındı |
| 2026-10-13 | donanım-web-arastirma.md | webfetch | 4 (digitalocean.com) | alındı |
| 2026-10-13 | donanım-kararlar.md | webfetch | 2 (spdx.org) | alındı |
| 2026-10-13 | donanım-kararlar.md | webfetch | 2 (opensource.org) | alındı |
| 2026-10-13 | donanım-test-stratejisi.md | webfetch | 3 (jestjs.io, playwright.dev) | alındı |
| 2026-10-13 | donanım-evrensel-dil.md | webfetch | 6 (sourcemaking.com, refactoring.guru) | alındı |

## 10. Eski URL Satırları (2026-10-12 — korunur)

Aşağıdaki satırlar Kural 11 gereği yerinde bırakılmıştır; durumları o gün
doğrulanmıştır ve bu sürümde yeniden test edilmez:

| İddia | URL | Doğrulama tarihi | Durum |
|---|---|---|---|
| PHP arayüz çözümleme标准 standardının resmi dokümanı | https://www.php-fig.org/psr/ | 2026-10-12 | doğrulandı |
| Konteyner imajı oluşturma ve çalışma zamanı resmi dokümanları | https://docs.docker.com/ | 2026-10-12 | doğrulandı |
| Composer paket yöneticisi resmi belgelendirmesi | https://getcomposer.org/ | 2026-10-12 | doğrulandı |
| PHPStan statik analiz aracı resmi kullanım kılavuzu | https://phpstan.org/user-guide | 2026-10-12 | doğrulandı |

Not: `标准` ibaresi 2026-10-12 kaydının kendi yazımıdır; bu sürümde düzeltilmez
(Kural 11 — eski satır değişmez), düzeltme ayrı bir revizyon işidir.

## 11. Red Team Kontrol Listesi

Üretim öncesi sorulan sorular:

1. Bu URL envanterde miydi? (Kural 1 — hayır ise devam)
2. `HEAD` 200 dönüyor mu ve tarih satırda var mı? (Kural 2)
3. Kaynak sınıfı iddia için yeterli mi? (§2 tablosu)
4. Üretici iddiası üreticinin alan adından mı geliyor? (Kural 3)
5. "verified" iddiası iki bağımsız kaynak taşıyor mu? (Kural 4)
6. Repo kanıtı var mı, dış URL yalnız destekleyici mi? (Kural 5)
7. Sürüm iddiası manifest satırına bağlı mı? (Kural 6)
8. Alıntı değil özet mi? (Kural 8)
9. Bulunamayan bilgi `UNKNOWN` mı? (Kural 9)
10. Aynı URL bu dosyada başka yerde geçiyor mu? (Kural 10)
11. 2026-10-12 satırlarına dokunuldu mu? (Kural 11 — dokunulmayacak)

## 12. Sık Yapılan Hatalar

| Hata | Sonuç | Düzeltme |
|---|---|---|
| Model hafızasından URL yazma | 404 / uydurma kaynak | her URL `HEAD` ile test edilir |
| Envanter kontrolünü atlama | tekrar eden kanıt | §4 Kural 1 zorunlu |
| Wiki/özet sitesini birincil kaynak yapma | hatalı parametre | üretici datasheet'e dönülür |
| Blog iddiasını teyitsiz yayınlama | doğrulanamayan sayı | ikinci bağımsız kaynak aranır |
| Tarih yazmamak | kanıt tazeliği bilinmez | doğrulama tarihi zorunlu |
| Tek URL ile "verified" demek | tek kaynak riski | §4 Kural 4 uygulanır |
| Eski satırı güncellemeye çalışmak | arşiv tutarsızlığı | §4 Kural 11 uygulanır |

## 13. İlişkili Dosyalar

| Dosya | İlişki |
|---|---|
| `.ai/architecture/K001-donanım/donanım-dogrulama.md` | URL ve kanıt doğrulamasının kontrol adımları |
| `.ai/architecture/K001-donanım/donanım-test-stratejisi.md` | araç sürümü iddialarının manifest kanıtı |
| `.ai/architecture/K001-donanım/donanım-kararlar.md` | kaynak seçimi kararlarının gerekçesi |
| `.ai/architecture/K001-donanım/donanım-bagimliliklar.md` | dış bağımlılık sürüm takibi |
| `.ai/architecture/K001-donanım/donanım-web-arastirma.md` | bu dosya |

## 14. Hızlı Kontrol Listesi (özet)

1. Kaynak sınıfı ≥ 4 mü, yoksa çapraz teyit var mı?
2. URL envanter dışı ve HTTP 200 mü (tarih 2026-10-13)?
3. Tekil mi (bu dosya + `.ai/` genelinde)?
4. İddia özet mi, alıntı değil mi?
5. Repo kanıtı var mı (Dosya / Satır)?
6. `UNKNOWN` olan `UNKNOWN` mı?

## 15. Uygulama Notları

- Oturum başında envanter dosyası yeniden okunur; sayfadan varsaymak yasaktır.
- `HEAD` başarısızsa `GET` ile ilk 200 bayt denenir; o da başarısızsa adres
  satıra girmez.
- Tarayıcı aracı yalnız JS render sayfalarda kullanılır; statik HTML için
  `webfetch` yeterlidir.
- Araç hataları (MCP bağlantı koptu gibi) iddianın doğruluğunu etkilemez; o
  durumda satır "beklemede" kalır ve başka araçla tekrar denenir.

## §A — Özet

Web araştırması bu arşivde kanıt üretme yöntemi değil, **kanıt tamamlama**
yöntemidir: birincil kanıt her zaman repo dosyasıdır (§4 Kural 5). Dış kaynak,
repo'nun söylemediği şeyleri — üretici parametreleri, standart metinleri, sürüm
varlığı — ekler ve satır formatıyla (§5) izlenebilir kalır. Envanter disiplini
(§4 Kural 1) ve tekilleştirme (Kural 10) sayesinde aynı URL arşivde ikinci kez
"kanıt" olmaz; tarih kaydı (Kural 2) ise üç ay sonra hangi iddianın ne zaman
doğrulandığını bilinir kılar. Red Team kontrol listesi (§11) üretimden hemen
önce çalıştırılır ve on bir sorunun tamamının "evet" olması beklenir.

## §REVİZYON

| Tarih | Sürüm | Değişiklik | Yazar |
|---|---|---|---|
| 2026-10-12 | 1.0.0 | İlk sürüm: 11 kural, satır formatı, 4 eski URL satırı | Bayram Ali |
| 2026-10-13 | 1.1.0 | §2 kaynak hiyerarşisi, §6 envanter dışı 3 yeni URL (HTTP 200), §9 araştırma logu, §11 kontrol listesi, §12 hatalar; Kural 5 SQL sayımı 20 dosya / 4.345 satır olarak güncellendi, Kural 6 manifest kanıtı eklendi | Bayram Ali |

## §16 Araç Kullanım Kuralları — Detaylı (derinleştirme eki 1)

### §16.1 Araç ↔ iddia türü eşlemesi

| İddia türü | Birincil araç | İkincil araç | Kullanım anı |
|---|---|---|---|
| Üretici parametresi (DAC/amplifikatör) | webfetch (datasheet) | exa (semantik arama) | §7 eşleme |
| Standart metni (protokol) | webfetch | websearch | §3 kapsam 2 |
| Sürüm varlığı | webfetch (resmi sürüm sayfası) | — | Kural 6 |
| Güvenlik açığı | webfetch (CVE duyurusu) | websearch | §3 kapsam 5 |
| JS render sayfa | chrome-devtools | playwright | §15 notu |
| Semantik arama (ilgili kaynak bulma) | exa | websearch | §1 kaynak seçimi |

### §16.2 Araç hata davranışları

| Araç | Yaygın hata | Davranış | Satır sonucu |
|---|---|---|---|
| webfetch | 404 / timeout | GET + ilk 200 bayt denenir | başarısızsa satır girmez |
| websearch | Sonuç yok | exa ile tekrar denenir | beklemede |
| exa | İlgisiz sonuç | websearch ile tekrar denenir | beklemede |
| chrome-devtools | Bağlantı koptu | playwright ile tekrar denenir | beklemede |
| playwright | Timeout | chrome-devtools ile tekrar denenir | beklemede |
| HEAD isteği | 403 / antibot | GET ile denenir | başarısızsa satır girmez |

**Araç kuralı:** araç hatası iddianın doğruluğunu etkilemez — iddia
"beklemede" kalır ve farklı araçla tekrar denenir (§15 notu 4).

## §17 URL Envanteri İşleyişi (derinleştirme eki 1)

### §17.1 Envanter yaşam döngüsü

| Adım | İşlem | Araç | Kayıt |
|---|---|---|---|
| 1 | Envanter okunur (oturum başında) | read (`%TEMP%\opencode\url-envanter.txt`) | — |
| 2 | Aday URL envanterde aranır | grep | — |
| 3 | Envanterde YOK → HTTP 200 doğrulanır | shell (Invoke-WebRequest HEAD) | tarih satırda |
| 4 | HTTP 200 → `.ai/` içinde tekillik aranır | grep (1.233 dosya) | — |
| 5 | Tekil → satır formatıyla dosyaya yazılır | edit | §5 formatı |
| 6 | Envantere eklenir | append | — |

### §17.2 Envanter çakışma tipleri

| Çakışma | Tespit | tepki |
|---|---|---|
| Aynı URL bu dosyada | Kural 10 (dosya içi tekilleştirme) | satır eklenmez |
| Aynı URL komşu dosyada | `.ai/**.md` grep | eşdeğer resmi kaynak seçilir |
| Aynı URL envanterde | Kural 1 (envanter kontrolü) | satır eklenmez |
| Aynı iddia farklı URL | §4 Kural 4 (çapraz doğrulama) | iki URL birlikte listelenir |

## §18 Çapraz Doğrulama Yöntemi (derinleştirme eki 1)

### §18.1 Çapraz doğrulama adımları

1. İddia için birincil kaynak seçilir (§2 sıra 1–3).
2. Birincil kaynakta iddia doğrulanır (webfetch).
3. İkinci bağımsız kaynak aranır (exa veya websearch) — farklı alan adı.
4. İkinci kaynakta aynı iddia doğrulanır (webfetch).
5. İki kaynak iddia ile uyumluysa satır `çapraz-teyit` olur.
6. Uyumsuzluk varsa satır `beklemede` kalır ve üst karar istenir.

### §18.2 Çapraz doğrulama senaryoları

| Senaryo | Birincil | İkincil | Uyum | Sonuç |
|---|---|---|---|---|
| DAC parametresi | üretici datasheet | üretici ürün sayfası | evet | çapraz-teyit |
| Protokol davranışı | standart doküman | ikinci standart kaynağı | evet | çapraz-teyit |
| Sürüm varlığı | resmi sürüm sayfası | paket deposu | evet | çapraz-teyit |
| CVE | NVD/CERT | üretici duyurusu | evet | çapraz-teyit |
| Parametre çelişkisi | üretici datasheet | distribütör sayfası | HAYIR | üretici kazanır (Kural 3) |

## §19 Red Team Denetim Turu (derinleştirme eki 1)

### §19.1 Denetim soruları (üretim sonrası)

| # | Soru | Beklenen | İhlal |
|---|---|---|---|
| 1 | Her URL satırında HTTP 200 kaydı var mı? | evet | satır düşer |
| 2 | Her URL satırında doğrulama tarihi var mı? | evet | satır düşer |
| 3 | "verified" iddialar iki bağımsız kaynak taşıyor mu? | evet | "tek kaynak" etiketi |
| 4 | Üretici iddiası üreticinin alan adından mı? | evet | Kural 3 ihlali |
| 5 | Alıntı yerine özet mi kullanılmış? | özet | Kural 8 ihlali |
| 6 | `UNKNOWN` olan `UNKNOWN` mı, tahmin mi? | UNKNOWN | zero-hallucination ihlali |
| 7 | 2026-10-12 satırlarına dokunulmuş mu? | hayır | Kural 11 ihlali |
| 8 | Envanter güncel mi? | evet | Kural 1 ihlali |

### §19.2 Denetim sonuç matrisi

| Sonuç | Sayı | Aksiyon |
|---|---|---|
| İhlal yok | 8/8 geçti | dosya yayınlanır |
| 1 ihlal | 7/8 | ihlal düzeltılır, tekrar denetim |
| 2+ ihlal | ≤6/8 | dosya REDDEDİLİR, yeniden üretim |

## §20 Kaynak → Bölüm Eşlemesi (derinleştirme eki 1)

### §20.1 K001 bölümleri ↔ kaynak sınıfı

| Bölüm (dosya) | Kaynak sınıfı | Öncelik |
|---|---|---|
| donanım-kapsam.md | 1 (üretici) + 2 (standart) | yüksek |
| donanım-dogrulama.md | 1 (üretici) + 3 (sürüm) | yüksek |
| donanım-kararlar.md | 1–3 (üretici + standart + sürüm) | yüksek |
| donanım-web-arastirma.md | 1–6 (tümü) | — (bu dosya) |
| donanım-bagimliliklar.md | 3 (sürüm) + 5–6 (duyuru + teyit) | orta |
| donanım-test-stratejisi.md | 3 (sürüm) | orta |
| donanım-kod-ornekleri.md | 1 (üretici) + 3 (sürüm) | orta |
| donanım-riskler.md | 2 (standart) + 5 (güvenlik) | yüksek |
| donanım-anayasa.md | 1–2 (üretici + standart) | yüksek |
| donanım-kernel-kapsam.md | 2 (çekirdek belgeleri) + CERT | orta |
| donanım-evrensel-dil.md | 6 (şablon + topluluk) | düşük |

### §20.2 Bölüm ↔ kaynak çakışma kuralları

| Çakışma | Kural | Sonuç |
|---|---|---|
| Üretici + standart aynı konu | Üretici kazanır (Kural 3) | üretici satırı esas |
| Üretici + distribütör | Üretici kazanır (Kural 3) | distribütör etiketli |
| Standart + blog | Standart kazanır (§2 sıra) | blog yalnız teyit |
| Repo + dış URL | Repo kazanır (Kural 5) | dış URL destekleyici |

## §21 Dürüstlük bloğu (derinleştirme eki 1)

Bu ek (§16-§20) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 ölçüm sonucu eklenmiştir; yalnız araç kullanım
kuralları (§16), envanter işleyişi (§17), çapraz doğrulama yöntemi
(§18), red team denetim turu (§19) ve kaynak↔bölüm eşlemesi (§20)
eklenmiştir. §1-§15 ve §A'daki mevcut kurallar, tablolar ve notlar
aynen korunur. §6'daki 3 URL ve §10'daki 4 eski URL satırı aynen
korunur; Kural 11 (eski satır değişmez) bu ekle ihlal edilmez. §9'daki
araştırma logu aynen korunur — bu ek yeni log satırı eklemez (log
yalnız üretim oturumunda doldurulur).

## §22 Hata Sınıflandırması — Araştırma Hataları (derinleştirme eki 1)

### §22.1 Araştırma hata taksonomisi

| # | Hata sınıfı | Tetik | Sonuç | Önleme |
|---|---|---|---|---|
| R1 | Uydurma URL | model hafızası | 404 / kaynak yok | her URL HEAD ile test |
| R2 | Eski URL tekrarı | envanter okunmadı | tekrar eden kanıt | Kural 1 |
| R3 | Alıntı aşırılığı | doğrudan kopya | telif + bağlam kaybı | Kural 8 (özet) |
| R4 | Tek kaynak "verified" | çapraz teyit atlandı | tek kaynak riski | Kural 4 |
| R5 | Tarih eksikliği | kayıt atlandı | kanıt tazeliği bilinmez | Kural 2 |
| R6 | Tahmin "muhtemeldir" | bilinmeyen geçiştirme | zero-hallucination ihlali | Kural 9 |
| R7 | Eski satır değişikliği | revizyon karışıklığı | arşiv tutarsızlığı | Kural 11 |
| R8 | Wiki birincil kaynak | kaynak sınıfı karışıklığı | hatalı parametre | §2 sıra 6 |
| R9 | Üretici dışı parametre | distribütör sayfası | yanlış değer | Kural 3 |

### §22.2 Hata ↔ kural eşlemesi

| Hata | İhlal edilen kural | Düzeltme adımı |
|---|---|---|
| R1 | Kural 2 | URL silinir, yeniden aranır |
| R2 | Kural 1 | URL envantere iade, eşdeğer kaynak |
| R3 | Kural 8 | Alıntı özetle değiştirilir |
| R4 | Kural 4 | İkinci bağımsız kaynak aranır |
| R5 | Kural 2 | Doğrulama tarihi eklenir |
| R6 | Kural 9 | `UNKNOWN` yazılır |
| R7 | Kural 11 | Eski satır geri alınır |
| R8 | §2 sıra 6 | Üretici datasheet'e dönülür |
| R9 | Kural 3 | Üretici sayfası kullanılır |

## §23 Kapı Özeti ve Kapanış Koşulları (derinleştirme eki 1)

### §23.1 Araştırma kapıları

| Kapı | Kriter | Durum |
|---|---|---|
| Kapı A (URL doğrulama) | her URL HTTP 200 + tarih | AÇIK |
| Kapı B (kaynak sınıfı) | sınıf ≥ 4 veya çapraz teyit | AÇIK |
| Kapı C (tekilleştirme) | `.ai/` genelinde tekil | AÇIK |
| Kapı D (repo kanıtı) | Dosya + Satır referansı | AÇIK |
| Kapı E (bilinmeyen) | `UNKNOWN` işaretli | AÇIK |
| Kapı F (eski satır) | 2026-10-12 satırları korunur | AÇIK |
| Kapı G (denetim) | §19.1 8/8 geçti | AÇIK |

### §23.2 Kapı kapanış koşulları

| Kapı | Kapanış koşulu | Sahip |
|---|---|---|
| A | her URL HEAD 200 + tarih satırda | research-analyst |
| B | sınıf ≥ 4 veya 2 bağımsız kaynak | research-analyst |
| C | grep 1.233 dosyada 0 isabet | research-analyst |
| D | Dosya yolu + satır numarası mevcut | Vault Steward |
| E | `UNKNOWN` işareti mevcut | Vault Steward |
| F | 2026-10-12 satırları değişmedi | Vault Steward |
| G | 8 sorunun 8'i "evet" | Red Team |

**Kapanış kuralı:** yedi kapı da AÇIK'tır; bu ek kapıları yalnız tarif
eder — kapıların kapanışı üretim oturumunda §19.1 denetim turu ile
olur ve sonuç §9 araştırma loguna yazılır (iki-kayıt kuralı: tarif
burada, denetim logda).

### §23.3 Ek ↔ dış dosya referansları

| Ek bölüm | Dış dosya | İlişki |
|---|---|---|
| §16.1 araç eşlemesi | bu dosyanın §1 kaynak araçları | kendi araç kullanımı |
| §17.1 envanter | `%TEMP%\opencode\url-envanter.txt` | envanter dosyası (dış) |
| §20.1 bölüm eşlemesi | `donanım-*.md` (11 dosya) | K001 kök dosyaları |
| §22.1 hata taksonomisi | §4 Kural 1–11 | kendi kuralları |
| §23.1 kapılar | §19.1 denetim turu | kendi denetimi |

**Referans kuralı:** beş referans da okuma ilişkisidir; envanter dosyası
dıştır ve bu dosya onu değiştirmez — yalnız okur. §17.1 envanter
okuma adımı oturum başında bir kez yapılır; oturum ortasında tekrar
okuma yasaktır (envanter tutarlılığı kuralı — yarım oturumda envanter
değişirse çakışma riski doğar).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
