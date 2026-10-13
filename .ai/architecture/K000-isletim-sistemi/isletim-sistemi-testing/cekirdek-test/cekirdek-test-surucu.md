---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test/cekirdek-test-surucu
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test
children: []
depends_on: []
evidence: []
validation: []
open_questions:
  - T1 [UNKNOWN] — sürücü testinin sınırı: repo'da sürücü kodu yok, dolayısıyla sürücü testi yalnız referans stratejidir.
  - T2 [UNKNOWN] — KASAN/lockdep kapısının geçme eşiği belirsiz (ihlal sayısında eşik yok; uydurmak yasak).
group: mimari
---

# Cekirdek-Test Sürücüsü — KUnit Harness, QEMU Zinciri, Hata Enjeksiyonu

## §0 ELI15

Bu dosya **nasıl koşturulur** sorusunu yanıtlar: test vakaları hangi çerçevenin
içinde yazılır (KUnit), hangi makinede ayağa kalkar (QEMU zinciri), hangi güvenlik
aletleri açık durur (KASAN/lockdep/KFENCE), sürücü hatası nasıl zorlanır ve sonuç
**nasıl ölçülür**. İlkeler `cekirdek-test-anayasa.md` dosyasındadır; burada zincirin
her halkası ve her başarısızlık biçimi tanımlıdır.

## §1 KUnit harness

### §1.1 Harness bileşenleri

| # | Bileşen | Görev | Çıktı |
|---|---|---|---|
| H-1 | test tanımı | vakaların listesi | KUnit suite |
| H-2 | kurulum/teardown | test öncesi/sonrası | temiz durum |
| H-3 | assert katmanı | beklenti kontrolü | pass/fail |
| H-4 | runner | vakaları çalıştırır | rapor |
| H-5 | log toplayıcı | kernel log akışı | dmesg |
| H-6 | sonuç çözümleyici | raporu sadeleştirir | özet |
| H-7 | zamanlayıcı | vaka süresi | süre satırı |
| H-8 | hata enjeksiyonu | zorunlu hata | hata yolu doğrulaması |

### §1.2 Harness çalışma akışı

| # | Adım | Girdi | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| A-1 | vaka listesi okuma | suite | vaka sayısı | kayıt hatası |
| A-2 | ortam hazırlığı | koşul | hazır çevre | hazırlık hatası |
| A-3 | kurulum | fixture | durum | kurulum hatası |
| A-4 | çalıştırma | vaka | assert sonucu | fail kaydı |
| A-5 | teardown | fixture | temiz durum | artık durum uyarısı |
| A-6 | log toplama | dmesg | log dosyası | kanıt kaybı |
| A-7 | sonuç yazma | tüm vakalar | rapor | yarım rapor |
| A-8 | kapı kararı | rapor | exit code | kapı reddi |

### §1.3 KUnit çalışma yeri kararı

| Seçenek | Hız | Sadakat | CI uyumu | Karar |
|---|---|---|---|---|
| host (doğrudan) | çok yüksek | düşük | yüksek | L1 için |
| QEMU (emülasyon) | düşük | yüksek | orta | L2–L6 için |
| QEMU + KVM | yüksek | yüksek | koşullu | L5/L7 için |
| gerçek makine | değişken | en yüksek | düşük | bu dalda yok |

> Çalışma yeri **seçilmedi** → `UNKNOWN` (repo'da karşılığı yok).

## §2 QEMU boot zinciri sürücüsü

### §2.1 Zincir halkaları

| # | Halka | İş | Doğrulama | Kanıt |
|---|---|---|---|---|
| Z-1 | görüntü hazırlığı | disk/ISO | dosya var mı | dosya kaydı |
| Z-2 | komut kurma | bayraklar | parametre listesi | komut kaydı |
| Z-3 | başlatma | süreç | süreç başladı | çıkış |
| Z-4 | boot takibi | seri port | boot mesajları | log |
| Z-5 | init beklentisi | timeout | init mesajı | log |
| Z-6 | test çalıştırma | misafir komutu | exit code | rapor |
| Z-7 | kapanış | süreç sonu | temiz kapanış | çıkış |
| Z-8 | log toplama | dosya | log dosyası | kanıt |

### §2.2 Timeout matrisi

| Aşama | Kısa timeout | Uzun timeout | Aşım davranışı |
|---|---|---|---|
| görüntü yükleme | evet | hayır | başarısız say |
| boot | hayır | evet | başarısız say |
| init | hayır | evet | başarısız say |
| test koşusu | koşuya göre | koşuya göre | başarısız say |
| kapanış | evet | hayır | log'u yine de al |
| log toplama | hayır | evet | kanıt zorunlu |

### §2.3 Boot log doğrulama desenleri

| # | Desen | Beklenen | Sahte pozitif riski |
|---|---|---|---|
| D-1 | boot başladı işareti | ilk kernel mesajı | erken log |
| D-2 | donanım probe tamamı | cihaz listesi | kısmi liste |
| D-3 | root mount | mount mesajı | geçici mount |
| D-4 | init pid | init çalıştırıldı | yanlış süreç |
| D-5 | panic yok | tüm log | sessiz çökme |
| D-6 | KASAN temiz | ihlal yok | kapalı KASAN |
| D-7 | süre sınırı | timeout içinde | çok hızlı çıkış |

## §3 KASAN, lockdep ve KFENCE

| Araç | Ne yakalar | Teste etkisi | Kapı rolü |
|---|---|---|---|
| KASAN | bellek ihlali (use-after-free, taşma) | yavaşlatır | L6 reddi |
| lockdep | kilitlenme tehlikesi | yavaşlatır | L6 reddi |
| KFENCE | rastgele bellek ihlali örnekleme | düşük maliyet | destekleyici |
| KMSAN | kullanılmamış bellek okuma | çok yavaştır | planlanmadı |
| KCOV | kod kapsama haritası | orta | planlanmadı |
| fault injection | zorunlu hata yolu | kontrollü | L2 destek |

### §3.1 Etkinlik matrisi

| Senaryo | KASAN | lockdep | KFENCE | Beklenen sonuç |
|---|---|---|---|---|
| temiz koşu | açık | açık | açık | rapor temiz |
| bilinen hata | açık | açık | açık | ihlal raporu |
| hızlı koşu | kapalı | kapalı | kapalı | hız; güven yok |
| boot testi | açık | açık | kapalı | boot + denetim |
| yük testi | açık | açık | açık | yavaş ama güvenli |
| performans ölçümü | kapalı | kapalı | kapalı | süre anlamlı |
| regresyon | açık | açık | açık | tam denetim |
| saha simülasyonu | tercihe bağlı | tercihe bağlı | tercihe bağlı | koşul kaydı |

### §3.2 Yorum ilkeleri

| # | İlke |
|---|---|
| K-1 | İhlal varsa test BAŞARISIZTIR; "bilinen ihlal" listesi yoktur |
| K-2 | Araç kapalıyken alınan sonuç güvenlik kanıtı sayılmaz |
| K-3 | İhlal kaydı (sınıf + vaka) komşu dala (`isletim-sistemi-kernel`) eskalasyon edilir |
| K-4 | Araç sürümü/etkinliği koşul kaydına yazılır |

## §4 Sürücü hata enjeksiyonu

### §4.1 Enjeksiyon türleri

| # | Tür | Ne zorlar | Doğrulanan şey |
|---|---|---|---|
| E-1 | tahsis hatası | bellek ayırma başarısızlığı | geri dönüş yolu |
| E-2 | giriş/çıkış hatası | cihaz erişim hatası | hata yönetimi |
| E-3 | zaman aşımı | cihaz yanıtsızlığı | bekleme mantığı |
| E-4 | kesme kaybı | eksik kesme | eşzamanlılık |
| E-5 | kilit zorlama | kilit çakışması | lockdep temizliği |
| E-6 | kaynak tükenmesi | sınır değeri | taşma kontrolü |
| E-7 | bozuk veri | hatalı paket | doğrulama |
| E-8 | hotplug | cihaz ekleme/çıkarma | yaşam döngüsü |

### §4.2 Enjeksiyon protokolü

| # | Adım | Koşul | Kayıt |
|---|---|---|---|
| P-1 | hedef seçimi | §4.1 türü | tür |
| P-2 | ortam kurma | QEMU + araç | koşul |
| P-3 | enjeksiyon | kontrollü an | zaman damgası |
| P-4 | gözlem | log + davranış | log |
| P-5 | beklenti kontrolü | geri dönüş yolu | sonuç |
| P-6 | temizlik | durum sıfırlama | temizlik kaydı |
| P-7 | rapor | vaka + sınıf | rapor |

## §5 Ölçüm protokolü

### §5.1 Protokol adımları

| # | Adım | Ne ölçülür | Kayıt alanı |
|---|---|---|---|
| O-1 | koşul sabitleme | ortam parametreleri | §13 (anayasa) |
| O-2 | ısınma | ilk koşular | atlanır |
| O-3 | tekrar koşu | aynı vaka N kez | tekrar sayısı |
| O-4 | ham kayıt | süre/sayı | ham satır |
| O-5 | ayıklama | aykırı değerler | ayıklanan |
| O-6 | özet | ortanca/dağılım | özet |
| O-7 | karşılaştırma | önceki koşu | fark |
| O-8 | rapor | sonuç + koşul | rapor |

### §5.2 Ölçüm türü güven matrisi

| Tür | QEMU (emülatör) | QEMU+KVM | Not |
|---|---|---|---|
| doğruluk (doğru/yanlış) | yüksek | yüksek | en güvenilir |
| sıralama (A > B) | orta | orta-yüksek | koşul sabitse |
| mutlak süre | düşük | orta | gerçek makine değil |
| bellek | orta | orta | sanal sayaç |
| gecikme | düşük | orta | plan dalıyla çakışır |
| gürültü | yüksek | orta | ölçüm kaydı şart |

### §5.3 Geçersizlik koşulları

| # | Koşul | Ölçüm geçersizse |
|---|---|---|
| I-1 | koşul kaydı yok | tüm sayı |
| I-2 | tekrar sayısı yok | süre sayıları |
| I-3 | KVM durumu bilinmiyor | karşılaştırma |
| I-4 | KASAN durumu bilinmiyor | hız karşılaştırması |
| I-5 | SMP bilinmiyor | eşzamanlılık sonucu |
| I-6 | log yok | hata sonucu |
| I-7 | envanter sürümü yok | regresyon sonucu |
| I-8 | eşik uydurulmuşsa | kapı kararı |

## §6 Repo disk kanıtları

### §6.1 Pozitif kanıt — kapıda koşan PHPStan adımı (gerçek satır)

**Dosya:** `.github\workflows\ci.yml` · **Satır:** 62–64

```yaml
      - name: PHPStan (composer stan)
        run: composer stan
        working-directory: shared
```

Doğrulama: `Select-Object -Index (61..63)` → L62 `      - name: PHPStan (composer stan)` ·
L63 `        run: composer stan` · L64 `        working-directory: shared` (3/3 birebir eşleşti).

Anlamı: statik analiz adımı `shared` çalışma dizininde koşar — çekirdek testi söz
konusu olmadığında bile **adım + çalışma dizini** kalıbı budur. QEMU/KUnit adımı bu
workflow'da YOKTUR (ci.yml 203 satırında qemu/kunit/kasan geçmiyor).

### §6.2 Negatif kanıt

| Aranan | Dosya | Sonuç |
|---|---|---|
| qemu / qemu-system | `.github/workflows/*.yml` | 0 isabet |
| kunit | `.github/workflows/*.yml` | 0 isabet |
| kasan / lockdep | `.github/workflows/*.yml` | 0 isabet |
| Makefile | repo geneli | 0 isabet |
| Kconfig | repo geneli | 0 isabet |

### §6.3 Kanıt özeti

| Kanıt | Tür | Durum |
|---|---|---|
| ci.yml L62–L64 | pozitif | 3/3 birebir |
| workflow'da QEMU/KUnit yokluğu | negatif | okundu |
| Makefile/Kconfig yokluğu | negatif | okundu |
| çalıştırılabilir çekirdek harness | yok | `⚠️ VERIFICATION REQUIRED` |

## §7 Riskler

| # | Risk | Etki | Azaltma | Durum |
|---|---|---|---|---|
| S-1 | harness'ın repo'da olmaması | strateji ≠ gerçeklik | §6 negatif kanıt | izleniyor |
| S-2 | boot timeout değerinin uydurulması | kapı kilitlenmesi | `UNKNOWN` | azaltıldı |
| S-3 | KASAN kapalı "temiz" rapor | sahte güvenlik | §3.2 K-2 | izleniyor |
| S-4 | enjeksiyonun gerçek hatayı gizlemesi | yanlış sonuç | P-6 temizlik | izleniyor |
| S-5 | ölçüm koşulu kayıtsız | geçersiz sayı | §5.3 | azaltıldı |
| S-6 | log kaybı | kanıtsız hata | Z-8 | azaltıldı |
| S-7 | mojibake örneği | kirli kod bloğu | ASCII seçim | azaltıldı |
| S-8 | satır numarası uydurma | kanıt zinciri kırığı | `Select-Object -Index` | azaltıldı |
| S-9 | sürücü kodu olmadığı halde sonuç iddia etmek | uydurma | §6.2 | izleniyor |
| S-10 | eşik uydurma | ihlal | T2 `UNKNOWN` | azaltıldı |

## §8 Kapsam dışı

| Konu | Sınır |
|---|---|
| QEMU kurulum/dağıtım | `isletim-sistemi-turu` / `K018-devops` |
| Uygulama testi sürücüsü (phpunit) | `uygulama-testi/` |
| CI işi tanımı | `ci-ve-kalite-kapisi/` |
| Sürücü implementasyonu | `isletim-sistemi-kernel` |
| Zamanlayıcı ölçüm kaynağı | `isletim-sistemi-plan` |
| Telemetri toplama | `isletim-sistemi-observability` |
| Test ağaçları | `K019-testing` (0 satır) |

## §9 Kaynakça

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| W-01 | KUnit usage | https://www.kernel.org/doc/html/latest/dev-tools/kunit/usage.html | 2026-10-13 | HTTP 200 |
| W-02 | fault injection index | https://docs.kernel.org/fault-injection/ | 2026-10-13 | HTTP 200 |
| W-03 | checkpatch (kernel.org) | https://www.kernel.org/doc/html/latest/dev-tools/checkpatch.html | 2026-10-13 | HTTP 200 |
| W-04 | dev-tools index (kernel.org) | https://www.kernel.org/doc/html/latest/dev-tools/index.html | 2026-10-13 | HTTP 200 |
| W-05 | QEMU testing.rst | https://gitlab.com/qemu-project/qemu/-/blob/master/docs/devel/testing.rst | 2026-10-13 | HTTP 200 |
| W-06 | QEMU invocation | https://www.qemu.org/docs/master/system/invocation.html | 2026-10-13 | HTTP 200 |
| W-07 | QEMU images | https://www.qemu.org/docs/master/system/images.html | 2026-10-13 | HTTP 200 |

## §10 Kapı karar tablosu

| # | Kapı | Girdi | Geçme | Red |
|---|---|---|---|---|
| G-01 | KUnit L1 | rapor | tüm vaka pass | herhangi fail |
| G-02 | modül L2 | dmesg | ihlal yok | ihlal/uyarı |
| G-03 | sürücü L3 | cihaz logu | beklenen tepki | beklenen tepki yok |
| G-04 | boot L4 | boot logu | init + timeout içinde | timeout/panic |
| G-05 | yük L5 | yük logu | panik yok | panik |
| G-06 | KASAN L6 | KASAN raporu | temiz | her ihlal |
| G-07 | lockdep | lockdep raporu | temiz | her uyarı |
| G-08 | KFENCE | KFENCE raporu | temiz | her ihlal |
| G-09 | enjeksiyon | enjeksiyon sonucu | hata yolu işledi | hata yutuldu |
| G-10 | performans L7 | ölçüm raporu | rapor üretildi | rapor yok |
| G-11 | regresyon L8 | vaka envanteri | 0 regresyon | ≥1 regresyon |
| G-12 | log bütünlüğü | log dosyası | log tam | log eksik |
| G-13 | koşul kaydı | koşul satırı | kayıt tam | kayıt eksik |
| G-14 | süre sınırı | kapı süresi | timeout içinde | aşıldı |
| G-15 | tekrar tutarlılığı | N koşu | sonuçlar tutarlı | oynak sonuç |
| G-16 | envanter sürümü | envanter kaydı | kayıtlı | kayıtsız |
| G-17 | yetki | yazma yetkisi | beyazliste | beyazliste dışı |
| G-18 | Authority | son satır | eşleşiyor | eşleşmiyor |
| G-19 | frontmatter | 10 anahtar | tam | eksik |
| G-20 | yasak token | tarama | 0 isabet | isabet var |

## §11 Başarısızlık playbook'u

| # | Belirti | Muhtemel sınıf | İlk aksiyon | Eskalasyon |
|---|---|---|---|---|
| F-01 | boot hiç başlamadı | Z-1/Z-2 | görüntü/komut kontrolü | hayır |
| F-02 | boot ortada kaldı | Z-4/Z-5 | log + timeout | hayır |
| F-03 | panic | H-2 | log excerpt alma | kernel dalı |
| F-04 | KASAN ihlali | H-4 | sınıf + vaka kaydı | kernel dalı |
| F-05 | lockdep uyarısı | H-5 | kilit zinciri kaydı | kernel dalı |
| F-06 | sürücü tepkisi yok | H-6 | sanal cihaz kontrolü | kernel dalı |
| F-07 | timeout (test koşusu) | H-3 | koşul/timeout ayrımı | hayır |
| F-08 | kapı süresi aşıldı | H-8 | iş bölme | CI dalı |
| F-09 | log eksik | H-5? (kanıt) | log yolu kontrolü | hayır |
| F-10 | oynak sonuç | ölçüm | tekrar sayısı artır | hayır |
| F-11 | KVM kullanılamıyor | koşul | koşul kaydı | hayır |
| F-12 | disk görüntüsü bozuk | Z-1 | yeniden hazırlama | hayır |
| F-13 | süreç asılı kaldı | Z-7 | zorla sonlandırma + log | hayır |
| F-14 | enjeksiyon etkisiz | P-3 | hedef doğrulama | hayır |
| F-15 | teardown hatası | A-5 | artık durum temizliği | hayır |
| F-16 | eşik bulunamıyor | T2 | `UNKNOWN` bırak | evet (üst karar) |
| F-17 | mojibake örneği | kanıt | ASCII satıra düş | hayır |
| F-18 | satır uyuşmazlığı | kanıt | indeks yeniden ölçüm | hayır |
| F-19 | URL 404 | kaynak | kaynak değiştir | hayır |
| F-20 | yetkisiz yazım | süreç | geri alma | evet |

## §12 Ortam ve bağımlılık matrisi

| Öğe | Gerekli mi | Yoksa | Kayıt |
|---|---|---|---|
| QEMU | L2–L7 için evet | L1 host'a düşer | sürüm `UNKNOWN` |
| KVM | L5/L7 için tercih | emülasyon yavaş | durum `UNKNOWN` |
| disk görüntüsü | L4 için evet | boot testi yok | kaynak `UNKNOWN` |
| seri port log | her koşu için evet | kanıt yok | yol kaydı |
| KASAN etkin çekirdek | L6 için evet | güvenlik sonucu yok | durum `UNKNOWN` |
| lockdep | L6 için evet | kilit denetimi yok | durum `UNKNOWN` |
| araç zinciri | L1 için evet | test yazılamaz | sürüm `UNKNOWN` |
| ağ | isteğe bağlı | izolasyon artar | kapalı tercih |
| dosya sistemi | geçici alan | artık dosya | temizlik |
| kapı ortamı | CI runner | yerel koşu | koşul farkı |
| PHP + Node | kapı için evet | uygulama testi durur | sürüm kaydı |
| bağımlılık kilidi | tekrarlanabilirlik | sürüm kayması | kilip kaydı |
| yetki | beyazliste | yazım reddi | yetki kaydı |
| envanter | URL kanıtı | kesişim ölçümü | envanter dosyası |
| kaynak | §9 listesi | kanıtsız iddia | URL + tarih |

## §13 Vaka-ortam eşleme

| Vaka türü | Seviye | Ortam | Araç | Öncelik |
|---|---|---|---|---|
| veri yapısı birimi | L1 | host | KUnit | yüksek |
| modül sözleşmesi | L2 | QEMU | KUnit + boot | yüksek |
| sürücü tepkisi | L3 | QEMU sanal cihaz | dmesg | yüksek |
| boot bütünlüğü | L4 | QEMU | timeout | yüksek |
| eşzamanlılık | L5 | QEMU SMP | yük | orta |
| bellek güvenliği | L6 | QEMU + KASAN | KASAN | yüksek |
| gecikme | L7 | QEMU ± KVM | zamanlayıcı | düşük |
| regresyon | L8 | tümü | vaka envanteri | yüksek |
| hata enjeksiyonu | L2 | QEMU | fault injection | orta |
| hotplug | L3 | QEMU | cihaz ekleme | orta |
| dosya sistemi | L2 | QEMU | mount testi | orta |
| ağ yığını | L3 | QEMU user mode | sanal NIC | orta |
| zamanlayıcı | L2 | QEMU | sayaç testi | orta (plan dalı sınırı) |
| kesme | L2 | QEMU | kesme testi | orta |
| hafıza haritası | L2 | QEMU | sayfa testi | orta |
| çoklu CPU | L5 | QEMU SMP | yarış | orta |
| güç/bağımlılık | L4 | QEMU | sıralama | düşük |
| kaynak tükenmesi | L2 | QEMU | sınır testi | orta |
| yetkilendirme | L2 | QEMU | syscall testi | yüksek |
| geri dönüş yolu | L1 | host | KUnit | yüksek |

## §14 Log ve kanıt alanları

| Alan | İçerik | Zorunlu | Saklama |
|---|---|---|---|
| vaka adı | test vakası | evet | rapor |
| seviye | L1–L8 | evet | rapor |
| ortam | host/QEMU/KVM | evet | rapor |
| koşul satırı | SMP, bellek, araç durumu | evet | rapor |
| başlangıç | zaman damgası | evet | log |
| bitiş | zaman damgası | evet | log |
| exit code | sayısal sonuç | evet | rapor |
| stdout | test çıktısı | evet | log |
| stderr | hata akışı | hata varsa | log |
| dmesg | kernel logu | L2–L6 | log |
| boot logu | boot serisi | L4 | log |
| KASAN raporu | ihlal detayı | ihlal varsa | log |
| lockdep raporu | kilit zinciri | uyarı varsa | log |
| timeout değeri | saniye | evet | rapor |
| tekrar sayısı | N | L7/L5 | rapor |
| envanter sürümü | kayıtlı mı | evet | rapor |
| koşum sahibi | kim | evet | rapor |
| runner OS | runner etiketi | kapıda | rapor |
| komut | çalıştırılan komut | evet | log |
| dosya yolu | kanıt dosyası | evet | rapor |

## §15 Uyumluluk ve sürüm kaydı

| Öğe | Neden | Kayıt biçimi | Durum |
|---|---|---|---|
| QEMU sürümü | davranış farkı | sürüm satırı | `UNKNOWN` |
| KUnit sürümü | API farkı | sürüm satırı | `UNKNOWN` |
| KASAN etkinliği | hız/güvenlik farkı | bayrak satırı | `UNKNOWN` |
| hedef mimari | sonuç farkı | mimari satırı | `UNKNOWN` |
| runner OS | portability | runner etiketi | `ubuntu-latest` (ci.yml L27) |
| araç zinciri | L1 çalışması | sürüm satırı | `UNKNOWN` |
| kapı sürümü | iş davranışı | workflow sürümü | `@v4` (ci.yml L30) |
| kapı PHP sürümü | uygulama testi | env değeri | `'8.4'` (ci.yml L22) |
| kapı Node sürümü | JS testi | setup değeri | `'26'` (ci.yml L151) |
| envanter | URL kanıtı | 2097 satır | kayıtlı |
| vaka envanteri | regresyon | liste | `UNKNOWN` |
| risk kaydı | takip | §7 tablosu | mevcut |
| açık sorular | bilinmeyen | `[UNKNOWN]` | mevcut |
| hata sınıfları | triage | §11 | mevcut |
| kapı kararları | otomasyon | §10 | mevcut |

## §16 Roller ve görevler

| Rol | Görev | Çıktı | Sıklık |
|---|---|---|---|
| test yazarı | vaka + ortam eşleme | §13 satırı | vaka başına |
| test yazarı | koşul kaydı | §14 satırı | her koşu |
| harness sahibi | runner bütünlüğü | §1 raporu | haftalık |
| boot sahibi | zincir sağlığı | §2 raporu | haftalık |
| denetim sahibi | araç durumu | §3 raporu | haftalık |
| enjeksiyon sahibi | hata yolu envanteri | §4 raporu | sürümde |
| ölçüm sahibi | protokol uyumu | §5 raporu | sürümde |
| triage | sınıf atama | §11 kaydı | her başarısızlık |
| kapı sahibi | karar tablosu | §10 | sürümde |
| dal sahibi | risk + açık soru | §7 | haftalık |
| stajyer | kanıt satırı | Dosya+Satır | görevde |
| stajyer | yerel koşum sonucu | log | görevde |
| denetçi | mojibake taraması | 0 | sürümde |
| denetçi | yasak token | 0 | sürümde |
| denetçi | satır uzunluğu | ≥500 | sürümde |
| denetçi | Authority | son satır | sürümde |
| denetçi | URL kesişimi | 0 | sürümde |
| denetçi | frontmatter | 10 anahtar | sürümde |
| denetçi | imla 7 token | 7/7 | sürümde |
| denetçi | kod kanıtı | Dosya+Satır | sürümde |

## §17 Yeniden çalıştırma tetikleyicileri

| # | Tetik | Yeniden koşan | Beklenen |
|---|---|---|---|
| T-01 | vaka eklendi | §13 eşlemesi | satır tamam |
| T-02 | QEMU sürümü değişimi | tüm L2–L7 | koşul kaydı |
| T-03 | KASAN bayrağı değişimi | L6 | yeni rapor |
| T-04 | kapı workflow değişimi | kapı kararları | §10 güncel |
| T-05 | log yolu değişimi | §14 | kanıt yolu güncel |
| T-06 | envanter yenilemesi | URL kesişimi | 0 |
| T-07 | dosya ekleme/silme | satır ölçümü | ≥500 |
| T-08 | imla değişikliği | 7 token | 7/7 |
| T-09 | satır eklendi | indeks doğrulama | birebir |
| T-10 | hata sınıfı eklendi | §11 | tablo güncel |
| T-11 | kapı süresi değişimi | G-14 | timeout değeri |
| T-12 | tekrar sayısı değişimi | O-3 | kayıt güncel |
| T-13 | eşik sorusu kapanırsa | T2 | `UNKNOWN` kaldırılır |
| T-14 | kapsam sorusu kapanırsa | T1 | `UNKNOWN` kaldırılır |
| T-15 | komşu dal sınırı değişirse | §8 | matris güncel |

## §10 Eke — kapı ve ölçüm detay matrisleri

### §10.1 Kapı girdisi detayları

| Kapı | Girdi dosyası | Okuma biçimi | Karar değişkeni |
|---|---|---|---|
| G-01 | KUnit raporu | satır tarama | fail sayısı |
| G-02 | dmesg | desen tarama | ihlal/uyarı |
| G-03 | cihaz logu | desen tarama | beklenen tepki |
| G-04 | boot logu | desen tarama | init + süre |
| G-05 | yük logu | desen tarama | panik yok |
| G-06 | KASAN raporu | başlık tarama | kayıt yok |
| G-07 | lockdep raporu | başlık tarama | kayıt yok |
| G-08 | KFENCE raporu | başlık tarama | kayıt yok |
| G-09 | enjeksiyon sonucu | assert | hata yolu |
| G-10 | ölçüm raporu | dosya varlığı | rapor var |
| G-11 | vaka envanteri | sayımda | 0 |
| G-12 | log dosyası | boyut | boş değil |
| G-13 | koşul satırı | alan sayısı | tam |
| G-14 | kapı süresi | iş süresi | altında |
| G-15 | N koşu | karşılaştırma | tutarlı |
| G-16 | envanter kaydı | varlık | var |
| G-17 | yetki | beyazliste | içinde |
| G-18 | son satır | eşleşme | eşit |
| G-19 | frontmatter | anahtar sayısı | 10 |
| G-20 | token taraması | desen | 0 |

### §10.2 Ölçüm geçerlilik kontrol listesi

| # | Kontrol | Soru | Hayır ise |
|---|---|---|---|
| V-1 | koşul satırı var mı? | kayıtlı mı | ölçüm geçersiz |
| V-2 | KVM durumu yazılı mı? | yazılı mı | karşılaştırma geçersiz |
| V-3 | KASAN durumu yazılı mı? | yazılı mı | hız karşılaştırması geçersiz |
| V-4 | SMP yazılı mı? | yazılı mı | eşzamanlılık sonucu geçersiz |
| V-5 | tekrar sayısı var mı? | N biliniyor mu | özet geçersiz |
| V-6 | ayıklama yapıldı mı? | aykırı değer | özet geçersiz |
| V-7 | log saklandı mı? | dosya var | kanıt yok |
| V-8 | envanter sürümü var mı? | kayıtlı | regresyon geçersiz |
| V-9 | eşik kaynaklı mı? | kaynak var mı | kapı kararı geçersiz |
| V-10 | ölçüm kişisi belli mi? | sahip | sorumluluk yok |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
