---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test/cekirdek-test-anayasa
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test
children: []
depends_on: []
evidence: []
validation: []
open_questions:
  - T1 [UNKNOWN] — çekirdek test kapsamının sınırı (yalnız QEMU misafir kerneli mi, POSIX kullanıcı uzayı dahil mi).
  - T2 [UNKNOWN] — KASAN/lockdep kapsam (coverage) eşiği yok; parantez içi değerler doküman iddiasıdır, ölçüm değildir.
group: mimari
---

# Cekirdek-Test Anayasası — Çekirdek, Sürücü ve Sanal Donanım Test İlkeleri

## §0 ELI15

Çekirdek testi, işletim sisteminin **kodunu** çalıştıran değil, **kodunu doğrulayan**
katmandır. Çekirdek kodu bozulunca uygulama çökmez — makine çöker; bu yüzden test,
kullanıcı uzayından ayrı bir Sandbox (QEMU) içinde koşar. Anayasa üç soruya cevap verir:
(1) **neyi** hangi seviyede test ederiz, (2) **hangi araçla** koşarız, (3) **başarı**
hangi ölçümle anlaşılır. Bu dosya yalnızca ilkeleri ve araç sınırlarını kurar;
sürücü/senaryo uygulaması `-surucu.md` ve `-ornekler.md` dosyalarındadır.

## §1 Test seviyeleri (çekirdek)

| Seviye | Ne test eder | Koşum ortamı | Başarı ölçütü | Sahip araç |
|---|---|---|---|---|
| L1 unit | tek fonksiyon/veri yapısı | KUnit (host veya QEMU) | case pass | KUnit runner |
| L2 modül | modül + komşu modül sözleşmesi | QEMU + yüklenen modül | dmesg temiz | QEMU boot |
| L3 sürücü | sürücü + sanal cihaz | QEMU sanal donanım | cihaz tepkisi | QEMU + if/virtio |
| L4 boot | bootloader → init | QEMU tam boot | init'e ulaşım | QEMU + timeout |
| L5 yük | eşzamanlı süreç + baskı | QEMU SMP | panik yok | stress + dmesg |
| L6 güvenlik | bellek/örtü ihlali | QEMU + KASAN | rapor temiz | KASAN/KFENCE |
| L7 performans | gecikme/throughput | QEMU (ölçüm güveni düşük) | rapor üretilir | zamanlayıcı okuma |
| L8 regresyon | bilinen hata geri gelmesin | hepsi | vaka sayısı 0 | vaka envanteri |

### §1.1 Seviye kuralı

| # | Kural | Gerekçe |
|---|---|---|
| R-1 | Her test bir seviyeye aittir; seviyesiz test yazılmaz | Karmaşıklık ölçümü |
| R-2 | L1, L4 olmadan L5'e sıçramaz | Hata izolasyonu |
| R-3 | L7 sonradan eklenir; önce doğruluk | Ölçüm yanlılığı |
| R-4 | Kullanıcı uzayı uygulama testi bu seviyelere girmez | Dal sınırı (`uygulama-testi/`) |
| R-5 | CI'da L1–L4; L5–L7 ayrı iş (job) | Kapı süresi |

### §1.2 Seviye ↔ artefakt

| Seviye | Girdi | Çıktı | Kayıt |
|---|---|---|---|
| L1 | kaynak + KUnit tanımı | pass/fail listesi | rapor |
| L2 | modül + bağımlılık | dmesg | log |
| L3 | sürücü + sanal cihaz | cihaz olayları | log |
| L4 | disk görüntüsü | boot süresi + exit | log |
| L5 | eşzamanlı yük | panic yok | rapor |
| L6 | KASAN etkin çekirdek | ihlal raporu | log |
| L7 | zaman damgaları | sayısal tablo | rapor |
| L8 | vaka listesi | regresyon sonucu | tablo |

## §2 Çerçeveler: KUnit ve Zephyr

### §2.1 KUnit

| Alan | Değer |
|---|---|
| Ne | Linux çekirdeği içi birim test çerçevesi |
| Koşum | host (hızlı) veya QEMU misafir |
| Birim | `kunit_case` grupları; assert tabanlı |
| Çıktı | KUnit raporu; başarısızlıkta kernel log |
| Kaynak | `docs.kernel.org` usage + style belgeleri |

### §2.2 Zephyr

| Alan | Değer |
|---|---|
| Ne | Gömülü RTOS için test altyapısı |
| Birim | test suit + assertion |
| Koşum | hedef pano / simülatör |
| Bu dalda rolü | Referans çerçeve; CoreMusic repo'sunda Zephyr hedefi YOK |
| Durum | `UNKNOWN` — hedef donanım belirsiz |

### §2.3 Çerçeve karşılaştırması

| Kriter | KUnit | Zephyr | Karar |
|---|---|---|---|
| Hedef | Linux çekirdeği | gömülü RTOS | KUnit (Linux odaklı) |
| Hız | host'ta çok hızlı | simülasyon yavaş | KUnit |
| Sanal donanım | QEMU | QEMU/Zephyr runner | eşit |
| Repo karşılığı | yok | yok | ikisi de referans |
| Öğrenme eğrisi | orta | orta | belirsiz → `UNKNOWN` |

### §2.4 Çerçeve seçim ilkeleri

| # | İlke |
|---|---|
| P-1 | Çerçeve, hedef çekirdeğe göre seçilir (Linux → KUnit) |
| P-2 | Çerçeve değişimi test vakalarını silmez, taşır |
| P-3 | Repo'da karşılığı olmayan çerçeve "kullanılıyor" diye yazılmaz |
| P-4 | Çerçeve sürümü uydurulmaz → `UNKNOWN` |

## §3 QEMU ve KVM

### §3.1 QEMU rolleri

| Roll | Ne yapar | Testte kullanımı |
|---|---|---|
| Sistem emülasyonu | tam makine taklidi | L2–L7 |
| Hedef makine | mimari taklidi | platform matrisi |
| Disk görüntüsü | hazır görüntü boot | L4 |
| Davranış | emülasyon/davranış modu | referans |

### §3.2 KVM

| Alan | Değer |
|---|---|
| Ne | çekirdekte donanım sanallaştırma uzantısı |
| Yarar | emülasyon yerine doğrudan çalıştırma → hızlı |
| Kısıt | ev sahibi (host) desteği gerekir |
| Test rolü | L5/L7 gibi uzun koşular için hız |
| Risk | KVM yoksa sonuç farklılaşır → koşul kaydı şart |

### §3.3 Boot zinciri testi

| # | Adım | Doğrulama | Başarısızlık |
|---|---|---|---|
| B-1 | firmware/BIOS | ekran çıktısı | boot yok |
| B-2 | bootloader | geçiş mesajı | timeout |
| B-3 | kernel decompress | dmesg | panic |
| B-4 | kernel init | ilk printk | panic |
| B-5 | donanım probe | cihaz listesi | eksik cihaz |
| B-6 | root mount | mount mesajı | mount hatası |
| B-7 | init çalıştırma | init pid | boot fail |

### §3.4 QEMU ilkeleri

| # | İlke | Gerekçe |
|---|---|---|
| Q-1 | Komut satırı sürüm sabitlemeli | tekrarlanabilirlik |
| Q-2 | Headless koşulur | CI uyumu |
| Q-3 | timeout zorunlu | sonsuz bekleme |
| Q-4 | log dosyaya yazılır | kanıt |
| Q-5 | SMP sayısı açıkça verilir | eşzamanlılık testi |

## §4 Emülatör ve Sandbox

| Ortam | Ne | İzolasyon | Hız | Kullanım |
|---|---|---|---|---|
| Host KUnit | doğrudan host | düşük | en hızlı | L1 |
| QEMU tam emülasyon | sanal makine | yüksek | yavaş | L2–L6 |
| QEMU + KVM | sanal makine + hızlandırma | yüksek | hızlı | L5, L7 |
| Container | süreç izolasyonu | orta | hızlı | uygulama testi (bu dal değil) |
| Sandbox test | kapalı çevre | yüksek | orta | güvenlik testleri |

### §4.1 Sandbox ilkeleri

| # | İlke |
|---|---|
| S-1 | Tehlikeli test (boot, modül yükleme) yalnız sandbox'ta |
| S-2 | Host'ta L1 dışı test koşmaz |
| S-3 | Sandbox kaynakları sınırlıdır (bellek/disk/timeout) |
| S-4 | Sandbox log'ları kanıttır, silinmez |

## §5 Sanal Donanım

| Cihaz | QEMU taklidi | Test değeri | Risk |
|---|---|---|---|
| Disk | virtio-blk / scsi | mount + fs testi | veri kaybı yok (sanal) |
| Ağ | virtio-net | sürücü + stack testi | izolasyon şart |
| Ekran | virtio-gpu | display testi | pointer tuzağı (cross dalı) |
| Ses | virtio-ses / HDA | ses yolu testi | audio dalı sınırı |
| USB | qemu USB | hotplug testi | sürücü yükleme |
| Türe | seri port | konsol kanıtı | log kaynağı |
| Zamanlayıcı | PIT/HPET/rtc | zaman testi | plan dalı sınırı |
| SMP | çoklu CPU | eşzamanlılık | yarış durumu |

### §5.1 Sanal donanım ilkeleri

| # | İlke |
|---|---|
| V-1 | Sanal cihaz sonucu gerçek donanımın yerine geçmez; yalnız yaklaşıktır |
| V-2 | Her test, hangi sanal cihazla koştuğunu kaydeder |
| V-3 | Sanal cihaz hatası ile çekirdek hatası ayrıştırılır |
| V-4 | Donanım topolojisi değişince test koşulu değişir, kayıt gerekir |

## §6 Ölçüm

### §6.1 Ölçüm türleri

| Tür | Soru | Kaynak | Güven |
|---|---|---|---|
| Doğruluk | test doğru mu? | vaka envanteri | yüksek |
| Kapsam | neyi test ettik? | seviye matrisi | orta |
| Hız | ne kadar sürüyor? | koşum zamanı | orta |
| Gecikme | tepki süresi | zaman damgası | QEMU'da düşük |
| Bellek | kullanım | sanal donanım sayacı | orta |
| Hata | ihlal sayısı | KASAN/KFENCE | yüksek |

### §6.2 Ölçüm ilkeleri

| # | İlke | Yasa |
|---|---|---|
| O-1 | Ölçüm değeri olmadan iddia yazılmaz | uydurma sayı yasak |
| O-2 | QEMU üzerindeki süre gerçek makine süresi değildir | karşılaştırma yasağı |
| O-3 | Ölçüm koşulu (SMP, bellek, KVM) kaydedilir | koşulsuz sayı yasak |
| O-4 | Tek ölçüm sonuç değildir; tekrar gerekir | tek örnek yasak |
| O-5 | Eşik yoksa eşik uydurulmaz → `UNKNOWN` | T2 |

### §6.3 Ölçüm kayıt şeması

| Alan | Açıklama | Zorunlu |
|---|---|---|
| vaka | test vakası adı | evet |
| seviye | L1–L8 | evet |
| ortam | host / QEMU / KVM | evet |
| koşul | SMP, bellek, görüntü | evet |
| sonuç | pass/fail/skip | evet |
| süre | saniye | evet |
| tekrar | kaç kez koşulduğu | evet |

## §7 Repo disk kanıtları

### §7.1 Negatif kanıt (çekirdek altyapısı repo'da YOK)

| Aranan | Yol | Sonuç |
|---|---|---|
| Makefile | repo geneli | 0 isabet |
| Kconfig | repo geneli | 0 isabet |
| kernel/ dizini | repo geneli | 0 isabet |
| arch/ dizini | repo geneli | 0 isabet |
| drivers/ dizini | repo geneli | yalnız `.ai\electronic\drivers` (elektronik, çekirdek değil) |
| QEMU/KUnit hedefi | `.ai/architecture/**` (bu dal dışında) | 7 markdown anım (bilgi metni, kod değil) |

> Sonuç: repo'da **çalıştırılabilir çekirdek test altyapısı yoktur**. Bu dosyadaki
> KUnit/QEMU tanımları referans stratejidir; repo kanıtı olarak sunulamaz.

### §7.2 Pozitif kanıt (testin CI yüzü — gerçek satır)

**Dosya:** `.github\workflows\ci.yml` · **Satır:** 25–30

```yaml
  php-lint:
    name: PHP Lint
    runs-on: ubuntu-latest
    timeout-minutes: 15
    steps:
      - uses: actions/checkout@v4
```

Doğrulama: `Select-Object -Index (24..29)` → L25 `  php-lint:` · L26 `    name: PHP Lint` ·
L27 `    runs-on: ubuntu-latest` · L28 `    timeout-minutes: 15` · L29 `    steps:` ·
L30 `      - uses: actions/checkout@v4` (6/6 birebir eşleşti).

Anlamı: test koşum ortamı Linux runner'dır (`ubuntu-latest`) — çekirdek testi için
referans OS satırı budur; QEMU hedefi bu dosyada YOKTUR.

### §7.3 Kanıt özeti

| Kanıt | Tür | Durum |
|---|---|---|
| Makefile/Kconfig yokluğu | negatif | okundu (repo geneli arama) |
| 7 markdown anımı | dolaylı | yalnız metin |
| ci.yml L25–L30 | pozitif | 6/6 birebir |
| KUnit/QEMU çalıştırma | yok | `⚠️ VERIFICATION REQUIRED` |

## §8 Riskler

| # | Risk | Etki | Azaltma | Durum |
|---|---|---|---|---|
| K-1 | referans stratejinin repo gerçeği sanılması | yanlış envanter | §7.1 negatif kanıt | izleniyor |
| K-2 | QEMU süresinin gerçek sürenin yerine konması | yanlış performans | O-2 | izleniyor |
| K-3 | KASAN'ın test hızını düşürmesi | kapı süresi | L6 ayrı job | izleniyor |
| K-4 | KVM yokluğu | sonuç değişimi | koşul kaydı | izleniyor |
| K-5 | eşik uydurma | zero-hallucination ihlali | T2 `UNKNOWN` | azaltıldı |
| K-6 | sanal cihaz sonucunun donanım sanılması | yanlış güvenlik | V-1 | azaltıldı |
| K-7 | boot timeout'suz koşu | kilitlenme | Q-3 | azaltıldı |
| K-8 | log kaybı | kanıtsızlık | Q-4 | azaltıldı |
| K-9 | Zephyr hedefinin uydurulması | yanlış çerçeve | §2.2 `UNKNOWN` | azaltıldı |
| K-10 | kullanıcı uzayının bu dala sızması | kapsam kayması | §1 R-4 | izleniyor |

## §9 Kapsam dışı

| Konu | Sınır | Gerekçe |
|---|---|---|
| PHP/JS testi | `uygulama-testi/` | kullanıcı uzayı |
| CI kapısı işleri | `ci-ve-kalite-kapisi/` | orkestrasyon |
| Sürücü kodu | `isletim-sistemi-kernel` | bu dal yalnız test |
| Zamanlama modeli | `isletim-sistemi-plan` | ölçüm sahipliği |
| Güvenlik politikası | `isletim-sistemi-security` | denetim |
| Telemetri altyapısı | `isletim-sistemi-observability` | canlı sistem |
| Platform kurulumu | `isletim-sistemi-turu` | dağıtım |
| Test ağaçları | `K18-testing` | 0 satır yazım |
| Donanım devresi | `electroncis` (repo yazımı) | elektronik dalı |

## §10 Kaynakça

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| W-01 | KUnit usage | https://docs.kernel.org/dev-tools/kunit/usage.html | 2026-10-13 | HTTP 200 |
| W-02 | KUnit style | https://docs.kernel.org/dev-tools/kunit/style.html | 2026-10-13 | HTTP 200 |
| W-03 | KCOV | https://docs.kernel.org/dev-tools/kcov.html | 2026-10-13 | HTTP 200 |
| W-04 | KFENCE | https://docs.kernel.org/dev-tools/kfence.html | 2026-10-13 | HTTP 200 |
| W-05 | KMSAN | https://docs.kernel.org/dev-tools/kmsan.html | 2026-10-13 | HTTP 200 |
| W-06 | fault injection | https://docs.kernel.org/fault-injection/fault-injection.html | 2026-10-13 | HTTP 200 |
| W-07 | checkpatch | https://docs.kernel.org/dev-tools/checkpatch.html | 2026-10-13 | HTTP 200 |
| W-08 | dev-tools index | https://docs.kernel.org/dev-tools/index.html | 2026-10-13 | HTTP 200 |
| W-09 | QEMU images | https://www.qemu.org/docs/master/system/images.html | 2026-10-13 | HTTP 200 |
| W-10 | QEMU invocation | https://www.qemu.org/docs/master/system/invocation.html | 2026-10-13 | HTTP 200 |
| W-11 | QEMU targets | https://www.qemu.org/docs/master/system/targets.html | 2026-10-13 | HTTP 200 |
| W-12 | QEMU devel | https://www.qemu.org/docs/master/devel/index.html | 2026-10-13 | HTTP 200 |
| W-13 | QEMU emulation | https://www.qemu.org/docs/master/about/emulation.html | 2026-10-13 | HTTP 200 |
| W-14 | QEMU testing.rst | https://gitlab.com/qemu-project/qemu/-/blob/master/docs/devel/testing.rst | 2026-10-13 | HTTP 200 |
| W-15 | QEMU repo | https://gitlab.com/qemu-project/qemu | 2026-10-13 | HTTP 200 |
| W-16 | kernel admin index | https://docs.kernel.org/admin-guide/index.html | 2026-10-13 | HTTP 200 |
| W-17 | kernel parameters | https://www.kernel.org/doc/html/latest/admin-guide/kernel-parameters.html | 2026-10-13 | HTTP 200 |
| W-18 | kernel howto | https://www.kernel.org/doc/html/latest/process/howto.html | 2026-10-13 | HTTP 200 |
| W-19 | kernel deprecated | https://www.kernel.org/doc/html/latest/process/deprecated.html | 2026-10-13 | HTTP 200 |
| W-20 | kexec_file_load | https://man7.org/linux/man-pages/man2/kexec_file_load.2.html | 2026-10-13 | HTTP 200 |
| W-21 | numa(7) | https://man7.org/linux/man-pages/man7/numa.7.html | 2026-10-13 | HTTP 200 |
| W-22 | Austin Group | https://www.opengroup.org/austin/ | 2026-10-13 | HTTP 200 |
| W-23 | Open Group testing | https://www.opengroup.org/testing | 2026-10-13 | HTTP 200 |
| W-24 | kernel.org translations | https://docs.kernel.org/translations/ | 2026-10-13 | HTTP 200 |

## §11 L1 birim test vakası kalıpları

| # | Kalıp | Girdi | Beklenen | Tuzağa dikkat |
|---|---|---|---|---|
| U-01 | sınır değeri | 0 / -1 / max | tanım gereği sonuç | taşma |
| U-02 | geçersiz giriş | null benzeri | reddetme | sessiz kabul |
| U-03 | boş koleksiyon | boş liste | tanımlı tepki | null dönüş |
| U-04 | ardışık çağrı | 2 kez çalıştırma | ikinci de aynı | durum sızıntısı |
| U-05 | paralel çağrı | 2 iş | yarış yok | veri yarışı |
| U-06 | geri alma | kısmi iş | tam geri dönüş | yarım durum |
| U-07 | zaman bağımlılığı | sabit zaman | deterministik | zaman sızıntısı |
| U-08 | rastgelelik | sabit tohum | tekrarlanabilir | tohumsuz test |
| U-09 | dosya sistemi | geçici dosya | temizlik | artık dosya |
| U-10 | ağ | sahte sunucu | izole sonuç | gerçek ağ |
| U-11 | bellek sınırı | az bellek | kontrollü hata | OOM çökme |
| U-12 | hata enjeksiyonu | zorlanan hata | geri dönüş yolu | hata yutma |
| U-13 | kayıt doğrulama | log çıktısı | beklenen kayıt | aşırı log |
| U-14 | konfigürasyon | farklı bayrak | beklenen davranış | gizli varsayılan |
| U-15 | geri uyumluluk | eski giriş | korunan davranış | sessiz kırılma |
| U-16 | uzunluk sınırı | çok uzun giriş | reddetme | tampon taşması |
| U-17 | kodlama | ASCII dışı giriş | korunmuş eşdeğerlik | kodlama kaybı |
| U-18 | birim tutarlılık | kuruş değeri | kayıpsız toplam | kayan nokta |
| U-19 | idempotent tekrar | aynı komut 2 kez | tek etki | çift etki |
| U-20 | iptal | iptal edilen iş | temiz çıkış | yetim iş |
| U-21 | kilit | 2 erişim | sıralı erişim | kilitlenme |
| U-22 | önbellek | ısıtma/invalidasyon | güncel okuma | bayat okuma |
| U-23 | şema doğrulama | eksik alan | reddetme | kısmi kayıt |
| U-24 | yetki | yetkisiz erişim | reddetme | sızıntı |
| U-25 | regresyon vaka | bilinen hata | düzelmiş kalır | geri dönüş |

## §12 QEMU koşum parametre matrisi

| Parametre | Seçenek | Test etkisi | Kayıt zorunlu |
|---|---|---|---|
| hedef mimari | x86_64 | referans | evet |
| hedef mimari | arm64 | alternatif satır | evet |
| CPU | tek çekirdek | basit davranış | evet |
| CPU | SMP | yarış testi | evet |
| bellek | küçük | sınır testi | evet |
| bellek | yeterli | normal akış | evet |
| disk | hazır görüntü | boot testi | evet |
| disk | geçici | izolasyon | evet |
| ağ | yok | izolasyon | evet |
| ağ | user mode | bağlantı testi | evet |
| konsol | seri port | log kanıtı | evet |
| display | headless | CI uyumu | evet |
| hızlandırma | yok (emülasyon) | taşınabilir | evet |
| hızlandırma | KVM | hız | evet |
| timeout | kısa | hızlı başarısızlık | evet |
| timeout | uzun | boot toleransı | evet |
| SMP sayısı | 1 | deterministik | evet |
| SMP sayısı | 2+ | eşzamanlılık | evet |
| görüntü | salt-okunur | tekrarlanabilirlik | evet |
| log | dosya | kanıt | evet |

## §13 Koşul kayıt matrisi (ölçüm geçersizliğini önler)

| Koşul | Neden kayıt | Kayıt edilmezse |
|---|---|---|
| QEMU sürümü | davranış farkı | karşılaştırma geçersiz |
| hedef mimari | performans farkı | yanlış genelleme |
| KVM açık/kapalı | hız farkı | yanlış süre |
| SMP sayısı | yarış varyasyonu | eksik kapsam |
| bellek | OOM davranışı | yanlış sonuç |
| KASAN etkinliği | hız + duyarlılık | yanlış karşılaştırma |
| KUnit çalışma yeri | hız farkı | yanlış beklenti |
| disk görüntüsü | boot davranışı | tekrarlanamazlık |
| ağ erişimi | bağımlılık | kopuk test |
| zaman kaynağı | determinizm | oynak test |
| log seviyesi | kanıt derinliği | eksik kanıt |
| envanter sürümü | kod değişimi | bayat vaka |
| runner OS | davranış farkı | portability hatası |
| PHP sürümü | uygulama testi | kapı farkı |
| Node sürümü | JS testi | kapı farkı |
| bağımlılık kilidi | sürüm farkı | bayat paket |
| timeout | başarısızlık biçimi | asılı kapı |
| tekrar sayısı | güven aralığı | tek örnek |
| çalışma saati | makine yükü | gürültülü süre |
| süreç önceliği | zamanlama | oynak süre |

## §14 Hata sınıflandırması

| Sınıf | Belirti | Sahip | Aksiyon |
|---|---|---|---|
| H-1 | KUnit assert başarısız | cekirdek-test | vakayı düzelt |
| H-2 | boot panic | cekirdek-test | log analizi |
| H-3 | timeout | cekirdek-test | koşul/timeout ayrımı |
| H-4 | KASAN ihlali | cekirdek-test → kernel | eskalasyon |
| H-5 | lockdep uyarısı | cekirdek-test → kernel | eskalasyon |
| H-6 | sürücü hatası | cekirdek-test → kernel | eskalasyon |
| H-7 | sanal cihaz hatası | cekirdek-test | QEMU koşulu |
| H-8 | kapı süresi aşımı | ci-ve-kalite-kapisi | iş bölme |
| H-9 | test sahteliği | uygulama-testi | vaka düzeltmesi |
| H-10 | eşik uydurma | bu dal | T2 `UNKNOWN` |

## §15 Test yaşam döngüsü

| # | Adım | Girdi | Çıktı | Kapı |
|---|---|---|---|---|
| Y-01 | vaka tasarımı | gereksinim | vaka listesi | evet |
| Y-02 | seviye atama | vaka | L1–L8 | evet |
| Y-03 | ortam seçimi | seviye | QEMU/host | hayır |
| Y-04 | implementasyon | vaka | test kodu | evet |
| Y-05 | yerel koşum | test | ilk sonuç | hayır |
| Y-06 | kapıya ekleme | test | CI işi | evet |
| Y-07 | regresyon | vaka envanteri | otomatik tekrar | evet |
| Y-08 | hata triage | başarısızlık | sınıf (§14) | hayır |
| Y-09 | eskalasyon | H-4..H-6 | komşu dal | hayır |
| Y-10 | kapanış | düzeltme | kanıt | evet |

## §16 Çekirdek test anti-pattern'leri

| # | Anti-pattern | Sonuç | Doğrusu |
|---|---|---|---|
| A-1 | QEMU süresini gerçek makine diye sunmak | yanlış iddia | O-2 |
| A-2 | timeout'suz boot koşusu | asılı kapı | Q-3 |
| A-3 | log tutmamak | kanıtsızlık | Q-4 |
| A-4 | KASAN kapalıyken "güvenli" demek | yanlış güvenlik | L6 ayrı koşul |
| A-5 | tek ölçümle eşik koymak | gürültü | O-4 |
| A-6 | eşik uydurmak | ihlal | T2 `UNKNOWN` |
| A-7 | sanal cihaz sonucunu donanım sanmak | yanlış genelleme | V-1 |
| A-8 | seviyesiz test | belirsiz kapsam | R-1 |
| A-9 | kullanıcı uzayını bu dala koymak | kapsam kayması | R-4 |
| A-10 | repo'da olmayan aracı "kullanıyoruz" demek | envanter hatası | §7.1 |
| A-11 | Zephyr hedefi uydurmak | uydurma donanım | §2.2 `UNKNOWN` |
| A-12 | KVM koşulunu kaydetmemek | oynak süre | §13 |
| A-13 | boot logunu silmek | kanıt kaybı | S-4 |
| A-14 | paralel testte kilitlenmeyi yok saymak | yarış | U-21 |
| A-15 | hata yutan test | sahte geçiş | U-12 |

## §17 Açık soruların bu dosyadaki karşılığı

| Soru | Bu dosyadaki karşılığı | Durum |
|---|---|---|
| T1 kapsam | §1 seviye tablosunun uygulama testiyle kesişimi | `[UNKNOWN]` |
| T2 eşik | §6.2 O-5 (eşik yok) | `[UNKNOWN]` |
| KVM mevcudiyeti | §3.2 ev sahibi desteği | `UNKNOWN` |
| Zephyr hedefi | §2.2 | `UNKNOWN` |
| QEMU sürümü | §13 koşul kaydı | `UNKNOWN` |
| Mimari hedefi | §12 matrisi | `UNKNOWN` |
| Disk görüntüsü kaynağı | §12 | `UNKNOWN` |
| Runner KVM desteği | §7.2 (`ubuntu-latest`) | `UNKNOWN` |
| L7 güven sınırı | §6.1 güven sütunu | orta/LOW belirtildi |
| K019 teslim şekli | §9 kapsam dışı | `UNKNOWN` |
| L6 kapıyı geçip geçmeyeceği | §14 H-4 | `UNKNOWN` |
| Boot timeout değeri | §3.4 Q-3 | `UNKNOWN` |
| Tekrar sayısı | §6.3 şema | `UNKNOWN` |
| Bellek eşiği | §12 | `UNKNOWN` |
| SMP test sayısı | §12 | `UNKNOWN` |

## §18 Ölçüm ve raporlama sorumlulukları

| Rol | Sorumluluk | Çıktı | Frekans |
|---|---|---|---|
| test yazarı | vaka + seviye | vaka listesi | her vaka |
| test yazarı | koşul kaydı | koşul satırı | her koşu |
| kapı sahibi | CI entegrasyonu | iş tanımı | değişiklikte |
| kapı sahibi | timeout değeri | kapı süresi | haftalık |
| dal sahibi | risk kaydı | risk tablosu | haftalık |
| dal sahibi | açık soru | `[UNKNOWN]` listesi | haftalık |
| triage | hata sınıfı | §14 sınıfı | her başarısızlık |
| eskalasyon | komşu dal bildirimi | kayıt | her H-4..H-6 |
| gözlemci | tekrar ölçüm | karşılaştırma | aylık |
| gözlemci | envanter yenileme | URL listesi | aylık |
| stajyer | vaka taslağı | taslak | görevde |
| stajyer | yerel koşum | sonuç | görevde |
| stajyer | kanıt satırı | Dosya+Satır | görevde |
| denetçi | anti-pattern tarama | §16 | sürümde |
| denetçi | uydurma sayı tarama | `UNKNOWN` | sürümde |
| denetçi | sınır matrisi kontrolü | §9 | sürümde |
| denetçi | Authority satırı | son satır | sürümde |
| denetçi | frontmatter anahtar | 10 anahtar | sürümde |
| denetçi | grup eşlemesi | 3 değer | sürümde |
| denetçi | URL kesişimi | 0 | sürümde |
| denetçi | satır uzunluğu | ≥500 | sürümde |
| denetçi | mojibake | 0 | sürümde |
| denetçi | yasak token | 0 | sürümde |
| denetçi | kod kanıtı | Dosya+Satır | sürümde |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
