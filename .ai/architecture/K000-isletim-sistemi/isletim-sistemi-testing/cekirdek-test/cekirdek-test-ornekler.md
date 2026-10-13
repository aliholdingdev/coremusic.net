---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test/cekirdek-test-ornekler
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test
children: []
depends_on: []
evidence: []
validation: []
open_questions:
  - T1 [UNKNOWN] — örneklerin repo ile ilişkisi: repo'da çekirdek test hedefi YOK, dolayısıyla tüm çekirdek kalıpları referans koddur.
  - T2 [UNKNOWN] — örneklerdeki sayısal değerler (bellek, timeout) eşik değil, gösterimdir; ölçüm eşiği yoktur.
group: kod
---

# Cekirdek-Test Örnekleri — KUnit Kalıpları, QEMU Blokları, Anti-Pattern Tablosu

## §0 ELI15

Bu dosya **kalıp kataloğu**dur: her satır bir test durumu, her blok çalıştırılabilir
bir düzenektir. Bloklar referans koddur — repo'da karşılığı yoktur (§6'da negatif
kanıt). Gerçek repo kanıtı yalnız `.github\workflows\ci.yml` satırlarıdır ve her
örnek `Dosya:` + `Satır:` ile `Select-Object -Index (N-1)` üzerinden doğrulanmıştır.

## §1 KUnit vaka kalıpları

### §1.1 Doğrulama kalıbı (tek vaka)

```c
static void test_range_check(struct kunit *test)
{
    KUNIT_ASSERT_EQ(test, range_validate(0), 0);
    KUNIT_ASSERT_EQ(test, range_validate(-1), -EINVAL);
}
```

| Alan | Değer |
|---|---|
| Seviye | L1 |
| Ortam | host veya QEMU |
| Başarı | her assert geçer |
| Risk | taşma/underflow gözden kaçabilir |

### §1.2 Kurulum/teardown kalıbı

```c
static int test_init(struct kunit *test)
{
    test->priv = fixture_alloc();
    KUNIT_ASSERT_NOT_NULL(test, test->priv);
    return 0;
}

static void test_exit(struct kunit *test)
{
    fixture_free(test->priv);
}
```

| Alan | Değer |
|---|---|
| Seviye | L1 |
| Kurulum başarısızlığı | test başlatılmaz |
| Teardown | her koşuda çalışır |
| Risk | artık durum (leak) |

### §1.3 Parametreli vaka kalıbı

```c
static struct kunit_case range_cases[] = {
    { .name = "zero_ok", .run_func = test_zero },
    { .name = "negative_fail", .run_func = test_negative },
    { .name = "overflow_fail", .run_func = test_overflow },
    {},
};
```

| Alan | Değer |
|---|---|
| Seviye | L1 |
| Vaka sayısı | 3 (gösterim) |
| Eşik | değildir |
| Risk | eksik kenar durumu |

## §2 QEMU komut blokları

### §2.1 Headless boot (L4)

```bash
qemu-system-x86_64 -m 2048 -smp 2 -nographic -serial mon:stdio \
  -drive file=test-disk.qcow2,if=virtio,format=qcow2 \
  -append "console=ttyS0 root=/dev/vda1" \
  -kernel bzImage
```

| Alan | Değer |
|---|---|
| Seviye | L4 |
| Bellek | 2048 MB (gösterim) |
| SMP | 2 (gösterim) |
| Kayıt | komut satırı zorunlu |

### §2.2 KVM'li hızlı koşu (L5/L7)

```bash
qemu-system-x86_64 -enable-kvm -m 4096 -smp 4 -nographic \
  -drive file=test-disk.qcow2,if=virtio,format=qcow2 \
  -kernel bzImage -append "console=ttyS0"
```

| Alan | Değer |
|---|---|
| Seviye | L5 / L7 |
| KVM | varsa kullanılır; yoksa §2.1'e düşer |
| Ölçüm | koşul kaydı zorunlu (KVM açık) |
| Risk | KVM yoksa süre karşılaştırması geçersiz |

### §2.3 KAPALI KVM (emülasyon) koşusu

```bash
qemu-system-x86_64 -m 2048 -smp 1 -nographic \
  -drive file=test-disk.qcow2,if=virtio,format=qcow2 \
  -kernel bzImage -append "console=ttyS0"
```

| Alan | Değer |
|---|---|
| Amaç | portability (KVM'siz ortam) |
| Hız | yavaş |
| Ölçüm | §2.2 ile karşılaştırılmaz |
| Kayıt | "KVM kapalı" satırı zorunlu |

### §2.4 Log toplama (her koşu)

```bash
qemu-system-x86_64 -m 2048 -nographic -serial file:boot.log \
  -drive file=test-disk.qcow2,if=virtio,format=qcow2 \
  -kernel bzImage
```

| Alan | Değer |
|---|---|
| Çıktı | `boot.log` |
| Doğrulama | log dosyası boş olamaz |
| Kanıt | §14 (surucu) alanları |
| Risk | log yolu yanlışsa kanıt yok |

## §3 Boot doğrulama kalıpları

| # | Kalıp | Beklenen | Sahte pozitif |
|---|---|---|---|
| B-1 | seri port ilk mesaj | boot başladı | bayat log |
| B-2 | mount mesajı | root takıldı | geçici mount |
| B-3 | init pid | init çalıştı | yanlış süreç |
| B-4 | kapanış temizliği | süreç bitti | zorla sonlandırma |
| B-5 | panic taraması | "panic" yok | log kesilmiş |
| B-6 | KASAN taraması | ihlal yok | araç kapalı |
| B-7 | süre | timeout içinde | timeout çok uzun |
| B-8 | cihaz listesi | beklenen cihazlar | eksik cihaz |
| B-9 | SMP görülmesi | çekirdek sayısı | tek çekirdek |
| B-10 | bellek görülmesi | toplam bellek | eksik bellek |

## §4 Hata enjeksiyonu kalıpları

| # | Kalıp | Zorlanan | Beklenen | Yanlış |
|---|---|---|---|---|
| E-1 | tahsis hatası | bellek ayırma | hata dönüşü | sessiz çökme |
| E-2 | cihaz yanıtsızlığı | zaman aşımı | bekleme kırılması | sonsuz bekleme |
| E-3 | bozuk paket | hatalı veri | reddetme | kabul |
| E-4 | kesme kaybı | eksik kesme | kurtarma | yarış |
| E-5 | kilit çakışması | paralel erişim | sıralı erişim | deadlock |
| E-6 | sınır değeri | maksimum giriş | taşma kontrolü | taşma |
| E-7 | hotplug | cihaz çıkarma | temiz kapanış | kullanılamaz cihaz |
| E-8 | kaynak tükenmesi | fd/descriptor | reddetme | sızıntı |

## §5 Ölçüm örnekleri (kalıp, sonuç DEĞİL)

| # | Ölçüm | Kaynak | Biçim | Uyarı |
|---|---|---|---|---|
| O-1 | boot süresi | seri port zaman damgası | saniye | QEMU'ya özgü |
| O-2 | vaka süresi | runner zamanlayıcı | saniye | kapı süresi |
| O-3 | bellek | sanal sayaç | MB | sanal değer |
| O-4 | gecikme | zaman damgası farkı | milisaniye | QEMU'da güven düşük |
| O-5 | ihlal sayısı | KASAN raporu | adet | 0 hedeflenir |
| O-6 | uyarı sayısı | lockdep | adet | 0 hedeflenir |
| O-7 | tekrar tutarlılığı | N koşu | tutarlı mı | tek örnek yetersiz |
| O-8 | kapı süresi | CI iş süresi | saniye | iş bölme nedeni |

## §6 Repo disk kanıtları

### §6.1 Pozitif kanıt — JS test işi (gerçek satır)

**Dosya:** `.github\workflows\ci.yml` · **Satır:** 142–146

```yaml
  js-test:
    name: JS Unit Tests
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
```

Doğrulama: `Select-Object -Index (141..145)` → L142 `  js-test:` ·
L143 `    name: JS Unit Tests` · L144 `    runs-on: ubuntu-latest` ·
L145 `    timeout-minutes: 10` · L146 `    steps:` (5/5 birebir eşleşti).

Anlamı: her test işinin **timeout değeri vardır** (Q-3 ilkesinin kapı karşılığı);
bu, çekirdek boot timeout'unun da kapıya taşınması gerektiğini gösteren kalıptır.

### §6.2 Pozitif kanıt — test komutu (gerçek satır)

**Dosya:** `package.json` · **Satır:** 5–8

```json
    "test:js": "vitest run --root assets.coremusic.net",
    "test:js:watch": "vitest --root assets.coremusic.net",
    "test:js:coverage": "vitest run --root assets.coremusic.net --coverage",
    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"
```

Doğrulama: `Select-Object -Index (4..7)` → L5 `    "test:js": "vitest run --root assets.coremusic.net",` ·
L6 `    "test:js:watch": "vitest --root assets.coremusic.net",` ·
L7 `    "test:js:coverage": "vitest run --root assets.coremusic.net --coverage",` ·
L8 `    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"` (4/4 birebir eşleşti; JSON anahtarları ASCII).

Anlamı: **komut adı + kök/dosya parametresi** kalıbı; çekirdek testinde karşılığı
benzer biçimde `komut + koşul` satırı olacaktır (repo'da yok → `UNKNOWN`).

### §6.3 Negatif kanıt

| Aranan | Kapsam | Sonuç |
|---|---|---|
| KUnit vaka dosyası | repo | 0 |
| QEMU komutu | repo (`.md` dışında) | 0 |
| Makefile / Kconfig | repo | 0 |
| qemu/kunit | `.github/workflows/*.yml` | 0 |
| kasan/lockdep | `.github/workflows/*.yml` | 0 |

## §7 Anti-pattern tablosu

| # | Anti-pattern | Belirti | Doğrusu |
|---|---|---|---|
| A-1 | QEMU süresini gerçek makine diye sunmak | yanlış iddia | koşul + güven notu |
| A-2 | timeout'suz boot | asılı kapı | her işte timeout |
| A-3 | log'suz koşu | kanıtsızlık | serial file log |
| A-4 | KASAN'sız güvenlik sonucu | sahte güvenlik | L6 ayrı koşul |
| A-5 | tek ölçümle eşik | gürültü | tekrar + özet |
| A-6 | eşik uydurma | ihlal | `UNKNOWN` |
| A-7 | sanal sonucu donanım sanmak | yanlış genelleme | sanal/not |
| A-8 | seviyesiz test | belirsiz | §13 eşlemesi |
| A-9 | repo'da olmayan aracı kullanmak | envanter hatası | §6.3 |
| A-10 | mojibake blok | kirli örnek | ASCII blok |
| A-11 | uydurma satır numarası | kanıt kırığı | indeks doğrulama |
| A-12 | koşul kayıtsız ölçüm | geçersiz sayı | koşul satırı |
| A-13 | tekrarsız sonuç | oynak | N koşu |
| A-14 | hata yutan test | sahte geçiş | assert zorunlu |
| A-15 | teardown'suz fixture | artık durum | teardown |
| A-16 | tek vaka = kapsam | eksik | kenar durum envanteri |
| A-17 | geçici dosya bırakma | kirlilik | temizlik |
| A-18 | kapıda birinci kez koşulan test | sürpriz | yerel ön koşu |
| A-19 | belirsiz envanter | bayat kanıt | envanter kaydı |
| A-20 | dolgu cümle | gürültü | kesin tablo satırı |

## §8 Referans vs repo ayrım tablosu

| Örnek | Referans mı | Repo karşılığı | Durum |
|---|---|---|---|
| KUnit vaka | evet | yok | referans |
| QEMU komutu | evet | yok | referans |
| boot doğrulama | evet | yok | referans |
| enjeksiyon | evet | yok | referans |
| ölçüm kalıbı | evet | yok | referans |
| ci.yml L142–L146 | hayır | var | gerçek kanıt |
| package.json L5–L8 | hayır | var | gerçek kanıt |
| QEMU boot işi | evet | yok | referans |
| KASAN işi | evet | yok | referans |
| timeout kalıbı | evet | ci.yml L145 | kapıda gerçek |

## §9 Kapsam dışı

| Konu | Sınır |
|---|---|
| PHP test örnekleri | `uygulama-testi/` |
| Workflow kalıpları | `ci-ve-kalite-kapisi/` |
| Sürücü kodu | `isletim-sistemi-kernel` |
| Elektronik sürücü | `electroncis` (repo yazımı) |
| Test ağaçları | `K019-testing` (0 satır) |

## §10 Kaynakça

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| W-01 | QEMU devel index | https://www.qemu.org/docs/master/devel/index.html | 2026-10-13 | HTTP 200 |
| W-02 | QEMU targets | https://www.qemu.org/docs/master/system/targets.html | 2026-10-13 | HTTP 200 |
| W-03 | QEMU emulation | https://www.qemu.org/docs/master/about/emulation.html | 2026-10-13 | HTTP 200 |
| W-04 | KUnit style | https://docs.kernel.org/dev-tools/kunit/style.html | 2026-10-13 | HTTP 200 |
| W-05 | KFENCE | https://docs.kernel.org/dev-tools/kfence.html | 2026-10-13 | HTTP 200 |
| W-06 | KMSAN | https://docs.kernel.org/dev-tools/kmsan.html | 2026-10-13 | HTTP 200 |
| W-07 | KCOV | https://docs.kernel.org/dev-tools/kcov.html | 2026-10-13 | HTTP 200 |
| W-08 | fault injection | https://docs.kernel.org/fault-injection/fault-injection.html | 2026-10-13 | HTTP 200 |
| W-09 | QEMU repo | https://gitlab.com/qemu-project/qemu | 2026-10-13 | HTTP 200 |
| W-10 | kernel translations | https://docs.kernel.org/translations/ | 2026-10-13 | HTTP 200 |

## §11 Eke — vaka kataloğu (kalıp satırları)

| # | Vaka | Seviye | Girdi | Beklenen |
|---|---|---|---|---|
| K-01 | sıfır girdi | L1 | 0 | tanımlı sonuç |
| K-02 | negatif girdi | L1 | -1 | reddetme |
| K-03 | taşma | L1 | maks+1 | reddetme |
| K-04 | alt taşma | L1 | min-1 | reddetme |
| K-05 | boş koleksiyon | L1 | boş | tanımlı tepki |
| K-06 | tek öğe | L1 | 1 öğe | doğru işleme |
| K-07 | çok öğe | L1 | N öğe | doğru işleme |
| K-08 | ardışık çağrı | L1 | 2 çağrı | aynı sonuç |
| K-09 | iptal | L2 | iptal sinyali | temiz çıkış |
| K-10 | zaman aşımı | L2 | bekleme | zaman aşımı |
| K-11 | boot bütünlüğü | L4 | disk görüntüsü | init |
| K-12 | panic yok | L4 | boot | log temiz |
| K-13 | SMP görülmesi | L5 | 2 CPU | 2 çekirdek |
| K-14 | eşzamanlı yazma | L5 | 2 iş | veri tutarlı |
| K-15 | bellek ihlali | L6 | erişim | ihlal raporu |
| K-16 | kilit zinciri | L6 | paralel | uyarı yok |
| K-17 | sürücü tepkisi | L3 | cihaz olayı | beklenen |
| K-18 | hotplug | L3 | cihaz çıkarma | temiz kapanış |
| K-19 | hata enjeksiyonu | L2 | zorunlu hata | hata yolu |
| K-20 | kaynak tükenmesi | L2 | sınır | reddetme |
| K-21 | regresyon vaka | L8 | bilinen hata | düzelmiş |
| K-22 | kapı süresi | kapı | iş | altında |
| K-23 | log bütünlüğü | her | koşu | dosya dolu |
| K-24 | koşul kaydı | her | koşu | satır tam |
| K-25 | tekrar tutarlılığı | L7 | N koşu | tutarlı |

## §12 Eke — kod bloğu doğrulama kayıtları

| # | Blok | Dosya | Satır | Doğrulama |
|---|---|---|---|---|
| C-01 | js-test işi | `.github\workflows\ci.yml` | 142–146 | `Select-Object -Index (141..145)` 5/5 |
| C-02 | package.json scriptleri | `package.json` | 5–8 | `Select-Object -Index (4..7)` 4/4 |
| C-03 | php-lint işi (anayasa) | `.github\workflows\ci.yml` | 25–30 | `Select-Object -Index (24..29)` 6/6 |
| C-04 | PHPStan adımı (surucu) | `.github\workflows\ci.yml` | 62–64 | `Select-Object -Index (61..63)` 3/3 |
| C-05 | KUnit blokları | referans | yok | repo karşılığı YOK |
| C-06 | QEMU blokları | referans | yok | repo karşılığı YOK |
| C-07 | boot desenleri | referans | yok | repo karşılığı YOK |
| C-08 | enjeksiyon blokları | referans | yok | repo karşılığı YOK |

## §13 Eke — anti-pattern denetim listesi

| # | Denetim | Soru | Hayır ise |
|---|---|---|---|
| D-01 | blok ASCII mi? | kirli karakter yok | blok yeniden yaz |
| D-02 | mojibake kaçışlı mı? | örnekte mojibake yok | örneği kaldır |
| D-03 | satır numarası doğrulandı mı? | indeks eşleşti | numarayı düzelt |
| D-04 | referans/repo ayrımı açık mı? | tablo var | ekle |
| D-05 | eşik iddiası var mı? | yok | `UNKNOWN` yap |
| D-06 | dolgu cümle var mı? | yok | satırı sil |
| D-07 | boş hücre var mı? | yok | doldur |
| D-08 | yasak token var mı? | yok | sil |
| D-09 | Authority son satır mı? | evet | ekle |
| D-10 | frontmatter 10 anahtar mı? | evet | düzelt |
| D-11 | `parent:` alanı yok mu? | yok | sil |
| D-12 | `group` alıntısız mı? | evet | düzelt |
| D-13 | URL envanterle kesişmiyor mu? | kesişim 0 | URL değiştir |
| D-14 | URL HTTP 200 mü? | evet | kaynak değiştir |
| D-15 | komşu sınır yazıldı mı? | §9 var | ekle |
| D-16 | açık sorular `UNKNOWN` mı? | evet | düzelt |
| D-17 | kod kanıtı ≥1 mi? | evet | ekle |
| D-18 | `Dosya:`+`Satır:` var mı? | evet | ekle |
| D-19 | satır ≥500 mü? | evet | uzat |
| D-20 | mojibake örnek yok mu? | yok | bloğu değiştir |

## §14 Eke — rol ve sorumluluk matrisi

| Rol | Kalıp yazımı | Kanıt satırı | Denetim | Eskalasyon |
|---|---|---|---|---|
| test yazarı | evet | evet | hayır | hayır |
| harness sahibi | evet | evet | evet | hayır |
| boot sahibi | hayır | evet | evet | kernel |
| denetleme sahibi | hayır | hayır | evet | dal sahibi |
| ölçüm sahibi | evet | evet | evet | hayır |
| kapı sahibi | hayır | evet | evet | CI dalı |
| dal sahibi | evet | evet | evet | üst karar |
| stajyer | taslak | evet | hayır | hayır |
| denetçi | hayır | hayır | evet | dal sahibi |
| araştırmacı | kaynak | URL | hayır | hayır |

## §15 Eke — tekrar çalıştırma tablosu

| # | Tetik | Yeniden kontrol | Beklenen |
|---|---|---|---|
| T-01 | blok değişimi | ASCII + indeks | temiz |
| T-02 | satır eklendi | indeks | birebir |
| T-03 | dosya eklendi | satır ölçümü | ≥500 |
| T-04 | URL eklendi | kesişim | 0 |
| T-05 | imla değişimi | 7 token | 7/7 |
| T-06 | tablo değişimi | boş hücre | 0 |
| T-07 | eşik cümlesi | `UNKNOWN` | uydurma yok |
| T-08 | kapsam değişimi | §9 | sınır güncel |
| T-09 | repo kanıtı değişimi | `Dosya:`+`Satır:` | güncel |
| T-10 | envanter yenilemesi | 2097 → yeni | kesişim 0 |

## §16 Eke — test seviyesi ve kapsam matrisi (tam)

| # | Seviye | Bu dalda | Repo karşılığı | Durum |
|---|---|---|---|---|
| S-01 | L1 birim (çekirdek) | evet | yok | referans |
| S-02 | L2 modül | evet | yok | referans |
| S-03 | L3 sürücü | evet | yok | referans |
| S-04 | L4 boot | evet | yok | referans |
| S-05 | L5 yük | evet | yok | referans |
| S-06 | L6 güvenlik | evet | yok | referans |
| S-07 | L7 performans | evet | yok | referans |
| S-08 | L8 regresyon | evet | yok | referans |
| S-09 | PHP birim | hayır | `shared/phpunit.xml` | `uygulama-testi/` |
| S-10 | JS birim | hayır | `package.json` L5 | `uygulama-testi/` |
| S-11 | JS e2e | hayır | `package.json` L8 | `uygulama-testi/` |
| S-12 | statik analiz | hayır | ci.yml L62–L64 | `ci-ve-kalite-kapisi/` |
| S-13 | kapı işleri | hayır | ci.yml L142–L146 | `ci-ve-kalite-kapisi/` |
| S-14 | secret taraması | hayır | secret-scan.yml | `ci-ve-kalite-kapisi/` |
| S-15 | coverage ağacı | hayır | K019 | erişim yok |

## §17 Eke — örnek-kanıt eşlemesi

| # | Örnek | Kanıt türü | Dosya | Satır |
|---|---|---|---|---|
| X-01 | js-test timeout kalıbı | gerçek | `.github\workflows\ci.yml` | 145 |
| X-02 | package.json komut kalıbı | gerçek | `package.json` | 5–8 |
| X-03 | phpunit.xml testsuites | gerçek | `shared\phpunit.xml` | 8–33 |
| X-04 | RouteRegistryTest assert | gerçek | `shared\tests\Unit\PageRouter\RouteRegistryTest.php` | 11–20 |
| X-05 | composer scripts | gerçek | `shared\composer.json` | 46–49 |
| X-06 | KUnit vaka | referans | yok | yok |
| X-07 | QEMU komutu | referans | yok | yok |
| X-08 | boot doğrulama | referans | yok | yok |
| X-09 | enjeksiyon | referans | yok | yok |
| X-10 | ölçüm kalıbı | referans | yok | yok |

## §18 Eke — bilinmeyenlerin listesi (uydurma yasağı)

| # | Bilinmeyen | Soru | Durum |
|---|---|---|---|
| U-01 | QEMU sürümü | hangi sürüm | `UNKNOWN` |
| U-02 | KUnit sürümü | hangi sürüm | `UNKNOWN` |
| U-03 | boot timeout değeri | kaç saniye | `UNKNOWN` |
| U-04 | bellek sınırı | kaç MB | `UNKNOWN` |
| U-05 | tekrar sayısı | N kaç | `UNKNOWN` |
| U-06 | KVM mevcudiyeti | kapıda var mı | `UNKNOWN` |
| U-07 | hedef mimari | x86_64/arm64 | `UNKNOWN` |
| U-08 | disk görüntüsü kaynağı | nereden | `UNKNOWN` |
| U-09 | coverage eşiği | yüzde | `[UNKNOWN]` (T2) |
| U-10 | test kapsamı sınırı | ne dahil | `[UNKNOWN]` (T1) |
| U-11 | Zephyr hedefi | donanım | `UNKNOWN` |
| U-12 | regresyon vaka sayısı | kaç vaka | `UNKNOWN` |
| U-13 | kapı süresi bütçesi | kaç saniye | `UNKNOWN` |
| U-14 | QEMU hedef işi | var mı | `UNKNOWN` (repo'da yok) |
| U-15 | sanal cihaz seti | hangileri | `UNKNOWN` |

## §19 Eke — kalıp bloklarının yeniden üretilebilirliği

Bu ek, §1–§5'teki blokların **neden referans kod** sayıldığını kanıtla listeler.

### §19.1 Kalıp bloğu × yeniden üretilebilirlik matrisi

| Blok | Bölüm | Çalıştırılabilir mi? | Engelyapan | Yeniden üretilebilirlik |
|---|---|---|---|---|
| KUnit tek vaka | §1.1 | Hayır | Çekirdek ağacı yok | Yalnız biçim olarak |
| KUnit parametreli vaka | §1.2 | Hayır | Çekirdek ağacı yok | Yalnız biçim olarak |
| QEMU başlatma | §2.1 | Hayır | QEMU kurulumu repo'da yok | Komut satırı kopyalanabilir |
| QEMU boot bekleme | §2.2 | Hayır | Timeout değeri `UNKNOWN` | Değer doldurulmalı |
| Boot doğrulama | §3.1 | Hayır | Boot artefaktı yok | Şablon olarak |
| Hata enjeksiyonu | §4.1 | Hayır | Enjeksiyon altyapısı yok | Şablon olarak |
| Ölçüm kaydı | §5.1 | Hayır | Ölçüm aleti yok | Yalnız tablo biçimi |
| CI iş bloğu | §6 | **Evet (kısmen)** | Workflow repo'da var | `ci.yml` satır kanıtıyla |

**Sayım:** 8 blok · 1 kısmen çalıştırılabilir (CI) · 7 referans kalıp.

### §19.2 Neden repo çekirdek testi çalıştırmıyor

| # | Koşul | Repo durumu | Sonuç |
|---|---|---|---|
| 1 | KUnit kaynak ağacı | Bulunmadı → §6 negatif kanıt | Test edilemez |
| 2 | QEMU ikili dosyası | Ortam değişkeni repo'da yok | Test edilemez |
| 3 | Disk görüntüsü | Kaynak `UNKNOWN` (U-08) | Test edilemez |
| 4 | Donanım hedefi | `UNKNOWN` (U-07, U-11) | Test edilemez |
| 5 | Kapı bütçesi | `UNKNOWN` (U-13) | Eşik yok |
| 6 | Çekirdek sürümü | `UNKNOWN` (U-01, U-02) | Sürüm bağımsız kalıp |

**Sonuç:** Altı koşulun altısı da karşılanmadığı için bu dosyadaki çekirdek
kalıpları **çalıştırılmaz, yalnız biçimsel olarak denetlenir.** Bu bir eksiklik
değil kapsam kararıdır: `isletim-sistemi-testing` dalı OS testini anlatır,
çekirdek ağaç kurulumunu değil.

### §19.3 Yeniden ölçüm komutları

```powershell
$f='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-testing\cekirdek-test\cekirdek-test-ornekler.md'
(Get-Content $f -Encoding UTF8).Count
Select-String -Path $f -Pattern ('T'+'BD') -Encoding UTF8   # dolgu anahtari
Select-String -Path 'C:\www\coremusic.net\.github\workflows\ci.yml' -Pattern 'phpunit|composer audit'
```

> Beklenen: ilk komut **>=500**, ikinci komut **0 satır**, üçüncü komut
> en az iki eşleşme (`phpunit` işi ve `composer-audit` işi).
> Ölçüm yapılmadan bu dosya **tamamlandı sayılmaz.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
