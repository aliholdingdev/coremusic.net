---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli/surucu-ve-modul-modeli-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/filesystems/vfs.html
  - https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol
  - https://learn.microsoft.com/en-us/windows-hardware/drivers/devtest/static-driver-verifier
  - https://man7.org/linux/man-pages/man7/capabilities.7.html
  - https://man7.org/linux/man-pages/man2/seccomp.2.html
validation: "2026-10-13 · 5 URL envanterden (HTTP 200 · -dogrulama.md §E) · Linux ioctl userspace-api/ioctl.html bu oturumda 404 → KULLANILMADI, Linux ioctl ayrıntısı ⚠️ VERIFICATION REQUIRED · repo'da syscall/ioctl/sürücü test kodu YOK (grep 0: insmod/modprobe/init_module/file_operations) · Q4 UNKNOWN"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "`electroncis` ↔ kernel sürücü sınırı (Q1) → UNKNOWN"
group: mimari
---

# surucu-ve-modul-modeli-surucu — Kullanıcı-Kernel Sınırı ve Test (Kademe-4 · §4-§6)

## §0 ELI15

Kullanıcı ile çekirdek arasındaki **tek kapı** syscall/ioctl'dir; sürücü
bu kapının **arkasında** yaşar. Bu dosya iki omurga taşır: (1) **sınır**
— kullanıcı ne çağırır, çekirdek ne doğrular, sürücü ne döner (§4);
(2) **test** — sürücü/modül nasıl doğrulanır (KUnit, QEMU, statik
doğrulama) (§5); (3) **referans/risk** kapanışı (§6). Anayasa (§0-§3)
doktrini kurdu; bu dosya onun **sınır ve doğrulama** yüzünü sürer.

| Öğe | Değer |
|---|---|
| Dosya rolü | Kullanıcı-kernel sınırı + test omurgası (Kademe-4) |
| Bölüm omurgası | §4-§6 (bu dosya) · §0-§3 (anayasa) · §1-§6 (`-ornekler`) |
| Bağlı dosyalar | `surucu-ve-modul-modeli-anayasa.md` · `surucu-ve-modul-modeli-ornekler.md` · kök `index.md` §1 |
| Kapsam | syscall/ioctl yüzeyi · yetki denetimi · test (KUnit/QEMU/statik) · risk |
| Kapsam dışı | uygulama API'si (`-api`) · güvenlik politikası (`-security`) · gözlem (`-observability`) · test kodu (`-testing`) |

> **İki dürüstlük işareti:** (1) Linux ioctl ayrıntısı **`⚠️ VERIFICATION
> REQUIRED`** (kaynak 404); (2) hedef çekirdek **UNKNOWN** (Q4) → iki
> yüzey (Linux/Windows) da **işaretli** taşınır, biri **seçilmez**.

## §4 Kullanıcı-kernel sınırı

### §4.1 Sınır katmanları

| # | Katman | Ne yapar | Kilit nokta |
|---|---|---|---|
| 1 | kullanıcı kodu | syscall çağırır | argüman doğrulama |
| 2 | syscall yönlendirici | numara → fonksiyon | numara sözleşmesi |
| 3 | çekirdek doğrulama | yetki + argüman | capability/seccomp |
| 4 | sürücü/VFS | iş yapar | `file_operations` |
| 5 | dönüş | sonuç + kopya | kullanıcıya kopya |

**Doktrin (4.1.1):** Her katman **kendi** doğrulamasını yapar; tek katmana
güvenirken ihmal → güvenlik açığı (ör. doğrulanmamış argüman → çekirdek
bellek hatası).

### §4.2 Syscall yüzeyi (kavram)

| Kavram | Ne demek | Doktrin |
|---|---|---|
| syscall numarası | çağrı kimliği | numara sözleşmesi |
| argüman | kullanıcı verisi | doğrulama (kopya) |
| dönüş değeri | sonuç/hata | hata kodu sözleşmesi |
| kopyalama | kullanıcı ↔ çekirdek | doğrulanmadan kopya yasak |

**Doktrin (4.2.1):** Kullanıcı verisi **hiçbir zaman doğrudan** çekirdek
belleğine **bırakılmaz**; kopyalanır ve doğrulanır. CoreMusic'te bu sınır
PHP → libc → syscall köprüsüdür (`cekirdek-yonetim` ornekler §2.7).

### §4.3 ioctl yüzeyi

| Yüzey | Durum | Kaynak |
|---|---|---|
| Windows `DeviceIoControl` | ✅ resmi konu | §6 URL |
| Linux ioctl ayrıntısı | ⚠️ VERIFICATION REQUIRED | userspace-api/ioctl.html **404** |
| VFS genel arayüzü | ✅ resmi konu | §6 URL (vfs.html) |

**Doktrin (4.3.1):** ioctl, dosya operasyonunu **genişleten** bir kanaldır;
her ioctl **yetki + argüman doğrulaması** ister. Linux ioctl ayrıntısı bu
oturumda **doğrulanamadı** (404) — bu dosyada ayrıntı **yazılmaz**, yalnız
işaret taşınır.

### §4.4 Sınır × doğrulama matrisi

| Sınır ↓ / Doğrulama → | argüman | yetki | kopya | dönüş |
|---|---|---|---|---|
| syscall | ✓ | ✓ | ✓ | ✓ |
| ioctl | ✓ | ✓ | ✓ | ✓ |
| VFS open/read | ✓ | ✓ | ✓ | ✓ |
| modül init | ✓ (parametre) | ✓ (imza) | ✓ | ✓ (hata kodu) |

**Matris kuralı:** Her sınır **dört** doğrulamadan geçer; eksik olan
**açık yüzeydir** (güvenlik açığı). Sürücü bu dördünü de **uygular**
(anayasa §3.2/§3.3).

### §4.5 Kullanıcı-kernel kopyalama doktrini

| Kural | Neden |
|---|---|
| doğrudan gösterge yasak | çekirdek çökmesi |
| kopya + doğrulama | boyut/tip/erişim denetimi |
| dönüşte de kopya | kullanıcıya güvenilmez |

**Doktrin (4.5.1):** Kopyalama **pahalı** ama **zorunlu**; "hız" için
doğrudan gösterge → çekirdek güvenlik açığı. Hız optimizasyonu yalnız
**doğrulanmış** yüzeyde düşünülür (§4.4 matrisi tamamsa).

## §5 Sürücü testi (KUnit · QEMU · statik doğrulama)

> Bu bölüm **test tasarımıdır**; hiçbir test bu turda **çalıştırılmadı**
> (test kodu `-testing` dalındadır).

### §5.1 Test türleri

| Tür | Ne test eder | Ortam | Kavram |
|---|---|---|---|
| birim (KUnit) | tek modül fonksiyonu | çekirdek içi | §6 URL |
| sistem (QEMU) | tam boot + sürücü | sanal makine | §6 URL |
| stres (torture) | kilit/RCU stresi | çekirdek içi | kardeş dal |
| statik (SDV) | sürücü statik doğrulama | derleme | §6 URL |
| imza denetimi | modül imzası | yükleme | anayasa §2.4 |

**Doktrin (5.1.1):** Hiçbir tek test türü **yeterli değildir**; birim +
sistem + statik **birlikte** anlam taşır. Test stratejisi `-testing`
dalının alanı, burada yalnız **arayüz** taşınır.

### §5.2 KUnit (çekirdek içi birim test)

| Kavram | Ne demek | Kullanım |
|---|---|---|
| KUnit | çekirdek içi birim test çerçevesi | tek fonksiyon testi |
| fixture | test kurulumu | kaynak ayırma |
| assertion | bekleme kontrolü | doğru/yanlış |

**Doktrin (5.2.1):** KUnit **çekirdek içinde** çalışır; sürücünün **tek**
fonksiyonunu izole test eder. Tam senaryo **KUnit + QEMU** birlikteliğiyle
test edilir (§5.3).

### §5.3 QEMU (sistem testi)

| Kavram | Ne demek | Kullanım |
|---|---|---|
| QEMU | sanal makine | tam boot testi |
| sanal aygıt | virtio | donanım taklidi |
| boot + sürücü | tam senaryo | yükleme doğrulama |

**Doktrin (5.3.1):** QEMU'da test edilen **sürücü-çekirdek etkileşimidir**;
gerçek donanım davranışı **değil** (gerçek donanım testi ayrı alan —
`K001-donanım`/Q1).

### §5.4 Statik doğrulama (Static Driver Verifier — Windows yüzeyi)

| Kavram | Ne demek | Kullanım |
|---|---|---|
| SDV | sürücü statik doğrulama | kural ihlali tespiti |
| kural | sürücü API sözleşmesi | örn. kilitleme kuralı |

**Doktrin (5.4.1):** Statik doğrulama **kod çalışmadan** kural ihkali
arar; bu **test değildir**, **denetimdir** — ikisi birlikte anlam taşır
(§5.1 doktrini). Windows yüzeyi Q4 nedeniyle **işaretli** taşınır.

### §5.5 Test × sınır eşlemesi (hangi sınır hangi test)

| Sınır | Birim (KUnit) | Sistem (QEMU) | Statik |
|---|---|---|---|
| syscall (§4.2) | ✓ | ✓ | — |
| ioctl (§4.3) | ✓ | ✓ | ✓ (SDV) |
| VFS (anayasa §3.2) | ✓ | ✓ | — |
| modül init/exit (anayasa §2) | ✓ | ✓ | ✓ |

**Eşleme kuralı:** Her sınırın **en az bir** testi olmalıdır; "test yok"
hücreleri **eksikliktir**, tercih **değil** (durum: repo'da test kodu
**yok** — dürüstlük §6).

## §6 Referans, risk ve kapanış

### §6.1 Kaynakça (envanter · HTTP 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| R-W1 | VFS çekirdek arayüzü (file_operations) | https://docs.kernel.org/filesystems/vfs.html | 200 (envanter) |
| R-W2 | Windows DeviceIoControl (ioctl yüzeyi) | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | 200 (envanter) |
| R-W3 | Static Driver Verifier (statik doğrulama) | https://learn.microsoft.com/en-us/windows-hardware/drivers/devtest/static-driver-verifier | 200 (envanter) |
| R-W4 | capability (Linux yetki modeli) | https://man7.org/linux/man-pages/man7/capabilities.7.html | 200 (envanter) |
| R-W5 | seccomp (syscall süzme) | https://man7.org/linux/man-pages/man2/seccomp.2.html | 200 (envanter) |

**Doğrulama notu:** Beş URL de `-dogrulama.md` §E envanterinden yeniden
kullanıldı (2026-10-13 HTTP 200); bu dosyada **yeni** URL doğrulanmadı.
Kullanılmayan URL: `userspace-api/ioctl.html` (**404** — §4.3 işareti).

### §6.2 Repo kanıtı (negatif — dürüstlük kaydı)

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| N1 | `insmod\|modprobe\|init_module` | tüm `*.php` | 0 | modül yükleme kodu yok |
| N2 | `file_operations` | tüm `*.php` | 0 | VFS operasyon tablosu yok |
| N3 | syscall/ioctl çağrısı | tüm `*.php` | 0 | doğrudan sınır çağrısı yok |

**Dürüstlük cümlesi:** 0 isabet "**repoda o kod yok**" der; "**sınır hiç
yok**" **demez** — PHP her syscall'u **dolaylı** çağır (`cekirdek-yonetim`
ornekler §2.7 köprüsü). Bu dosyadaki iddialar yalnız grep kapsamıyla
sınırlıdır (2026-10-13, tüm `*.php`).

### §6.3 Risk matrisi (bu dosyanın kapanışı)

| # | Risk | Olasılık | Etki | Koruma |
|---|---|---|---|---|
| 1 | doğrulanmamış argüman (sınır) | `UNKNOWN` | yüksek | §4.4 doktrini |
| 2 | yetki ihlali (capability) | `UNKNOWN` | yüksek | §4.3 + anayasa §3.3 |
| 3 | modül sızıntısı (init/exit) | `UNKNOWN` | orta | anayasa §2.1 |
| 4 | test eksikliği | **var** (repo'da yok) | orta | §5.5 eşleme |
| 5 | ioctl ayrıntısız kalma | **var** (404) | düşük-orta | §4.3 işareti |

**Risk kuralı:** "Olasılık" sütununda sayı **yoktur** — olasılık için de
ölçüm gerekir. #4 ve #5 **bilinen eksikliktir** (kanıtla kayıtlıdır).

### §6.4 Sık yapılan hatalar — bu dosyada 6 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| M1 | sınır doğrulamasını ihmal | güvenlik açığı | §4.4 matrisi |
| M2 | doğrudan gösterge vermek | çekirdek çökmesi | §4.5 doktrini |
| M3 | KUnit'i yeterli sanmak | sistem hatası kaçar | §5.1 doktrini |
| M4 | statik doğrulamayı test sanmak | yanlış güven | §5.4 doktrini |
| M5 | 404 URL'yi kullanmak | kanıtsız iddia | §4.3 işareti |
| M6 | 0 isabeti genişletmek | iddia taşması | §6.2 dürüstlük |

### §6.5 Açık sorular ve kapanış yolları

| # | Soru | Etiket | Kapanış yolu |
|---|---|---|---|
| Q1 | core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar |
| Q2 | hedef çekirdek (Q4) | UNKNOWN | ortam/prod okuması |
| Q3 | `electroncis` ↔ sürücü sınırı (Q1) | UNKNOWN | üst karar |
| Q4 | Linux ioctl ayrıntısı | ⚠️ VERIFICATION REQUIRED | doğru kaynak (404 düzeltme) |
| Q5 | test stratejisi bu çekirdekte geçer mi? | PLANNED | hedef çekirdek + `-testing` |

**Kapanış kuralı:** Q1-Q3 üst karar ister; Q4 doğru kaynak ister; Q5 hedef
çekirdek + test dalı ister. Bu beş soru açık kaldığı sürece dosya `draft`
statüsünde kalır.

### §6.6 Dosyanın komşulara verdiği özet (handover satırı)

| Alıcı dal | Verilen bilgi | Biçim |
|---|---|---|
| `surucu-ve-modul-modeli-anayasa` | sınır + test omurgası | §4, §5 |
| `surucu-ve-modul-modeli-ornekler` | sınır matrisi + risk | §4.4, §6.3 |
| `isletim-sistemi-api` | syscall/ioctl sınırı | §4.1-§4.3 |
| `isletim-sistemi-security` | yetki yüzeyi (capability/seccomp) | §4.3, R-W4, R-W5 |
| `isletim-sistemi-testing` | KUnit/QEMU/SDV arayüzleri | §5.1-§5.4 |
| `K001-donanım`/`electroncis` | Q1 sınırı (UNKNOWN) | §6.5 |

**Handover kuralı:** Bu tablodaki alıcılar bilgiyi **kullanır**, bu dosyanın
satırlarını **değiştirmez**; değişiklik ADR/üst karar ister.

## §7 Derinleştirme — sınır ve test pratiği (ek)

### §7.1 Sınır akışı — syscall adım adım (kavramsal)

```text
[kullanıcı kodu] --> [syscall çağrısı] --> [çekirdek yönlendirici]
                                                    |
                                     [argüman doğrulama + kopya]
                                                    |
                                     [yetki denetimi (capability)]
                                                    |
                                     [sürücü/VFS işleyici] --> [dönüş]
                                                                    |
                                                    [kopya + hata kodu]
```

| Adım | Olay | Hata | Bağlı doktrin |
|---|---|---|---|
| 1 | syscall çağrısı | numara yanlış | §4.2 |
| 2 | argüman doğrulama | boyut/tip hatası | §4.4 |
| 3 | kopyalama | doğrudan gösterge yasak | §4.5 |
| 4 | yetki denetimi | yetki yok | anayasa §3.3 |
| 5 | sürücü işleyici | VFS hatası | anayasa §3.2 |
| 6 | dönüş | hata kodu | §4.2 |

**Akış kuralı:** Adım 2-4 **her çağrıda** çalışır; adım 3'te doğrudan
gösterge kabul edilmez (§4.5). Bu akış **modeldir** — hedef çekirdek
farklı API sunsa bile **mantık** aynıdır.

### §7.2 Sınır × hata kodu eşlemesi (kavram)

| Hata türü | Ne demek | Kullanıcı etkisi |
|---|---|---|
| parametre hatası | argüman geçersiz | `EINVAL` benzeri |
| yetki hatası | capability yok | `EPERM` benzeri |
| kaynak yok | aygıt/bellek yok | `ENODEV` benzeri |
| meşgul | kaynak dolu | `EBUSY` benzeri |
| desteklenmiyor | ioctl bulunamadı | `ENOTTY` benzeri |

**Eşleme kuralı:** Hata kodları **çekirdeğe özgüdür**; isimler model olarak
taşınır, "hedef çekirdekte aynısı var" iddiası **yazılmaz** (Q4).

### §7.3 Test senaryoları — bu dosyanın test dalına verdiği taslak

| # | Senaryo | Sınır | Beklenen | Durum |
|---|---|---|---|---|
| TS1 | geçerli syscall | §4.2 | sonuç döner | taslak |
| TS2 | geçersiz argüman | §4.2 | hata kodu | taslak |
| TS3 | yetkisiz erişim | §4.3 | yetki hatası | taslak |
| TS4 | modül init/exit | anayasa §2 | kaynak sızıntısı yok | taslak |
| TS5 | imzasız modül | anayasa §2.4 | reddedilir | taslak |
| TS6 | negatif kanıt tekrarı | §6.2 | 0 isabet | **tekrarlanabilir** |

**Senaryo kuralı:** TS1-TS5 bu turda **çalıştırılmadı**; TS6 yapısal
olarak **tekrarlanabilirdir** (grep yeniden çalıştırılabilir). Test kodu bu
dosyaya **yazılmaz** (`-testing` dalının alanı).

### §7.4 Ölçüm — bu dosyanın kendi ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | URL (yeniden kullanım) | 5 (HTTP 200) | ölçüldü (2026-10-13) |
| 2 | kullanılmayan URL | 1 (ioctl 404) | ölçüldü (2026-10-13) |
| 3 | negatif kanıt (grep) | 3 arama / 0 isabet | ölçüldü (2026-10-13) |
| 4 | repo test kodu | **yok** | ölçüldü (2026-10-13) |
| 5 | gerçek sınır çağrısı | — | ⚠️ (dolaylı, ölçülmedi) |
| 6 | test sonuçları | — | PLANNED (çalıştırılmadı) |
| 7 | açık soru | 5 (Q1-Q5) | açık |

**Ölçüm dürüstlüğü:** Tablodaki 1-4 **yapısal ölçüm** (URL/grep); 5-7
**durum ölçümüdür** ve gerçek değer üretilmemiştir.

### §7.5 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | 5 URL envanterden taşındı | kanıt kapısı |
| 2026-10-13 | Linux ioctl 404 işareti kaydedildi | kanıt kapısı |
| 2026-10-13 | §7 derinleştirme eklendi | dosya derinliği hedefi |

## §8 Test omurgası — test × sürücü türü tam matrisi

| Sürücü türü ↓ / Test → | KUnit (birim) | QEMU (sistem) | SDV (statik) | stres (torture) |
|---|---|---|---|---|
| karakter aygıtı | ✓ (open/read) | ✓ (boot+device) | Windows yüzeyi | — |
| blok aygıtı | ✓ (I/O yolu) | ✓ (disk taklidi) | Windows yüzeyi | ✓ (I/O stresi) |
| ağ sürücüsü | ✓ (paket yolu) | ✓ (sanal NIC) | Windows yüzeyi | ✓ (trafik stresi) |
| dosya sistemi | ✓ (VFS işlemi) | ✓ (mount) | — | ✓ (torture) |
| sanal aygıt | ✓ (virtio) | ✓ (virtio) | — | — |

**Matris kuralı:** Her tür **en az iki** test ister (birim + sistem);
"Windows yüzeyi" hücreleri Q4 nedeniyle **işaretli** taşınır; "—" = o test
o tür için **tasarımsal olarak anlamlı değil**.

### §8.1 Test × sınır tam matrisi (kapsamlı)

| Test ↓ / Sınır → | syscall | ioctl | VFS | modül init/exit | imza |
|---|---|---|---|---|---|
| geçerli çağrı | ✓ | ✓ | ✓ | ✓ | ✓ |
| geçersiz argüman | ✓ | ✓ | ✓ | ✓ | — |
| yetkisiz erişim | ✓ | ✓ | ✓ | — | ✓ |
| meşgul/hata kodu | ✓ | ✓ | ✓ | ✓ | — |
| sızıntı (exit sonrası) | — | — | — | ✓ | — |

**Matris kuralı:** Hücreler **test gereksinimini** gösterir; boş hücre
"test gereksinimi yok" demektir, "test yapıldı" **değil** — bu turda
hiçbiri **çalıştırılmadı** (§7.3).

## §9 Bağlayıcı kurallar özeti (bu dosyanın 8 kuralı)

| # | Kural | Bölüm |
|---|---|---|
| B-S1 | her sınır kendi doğrulamasını yapar (katmanlı) | §4.1 |
| B-S2 | kullanıcı verisi doğrudan bırakılmaz (kopya + doğrulama) | §4.2, §4.5 |
| B-S3 | her ioctl argüman + yetki doğrulaması ister | §4.3 |
| B-S4 | sınır matrisi dört doğrulama içerir (eksik = açık yüzey) | §4.4 |
| B-S5 | hiçbir tek test türü yeterli değil (birim+sistem+statik) | §5.1 |
| B-S6 | statik doğrulama test değil denetimdir | §5.4 |
| B-S7 | Linux ioctl ayrıntısı 404 → yazılmaz, işaretlenir | §4.3 |
| B-S8 | 0 isabet yalnız kapsamıyla der (sınır-yok demez) | §6.2 |

**Kural kuralı:** Bu 8 kural bu ağacın bağlayıcısıdır; `-ornekler` buna
**aykırı** yazamaz (aykırılık → ADR/üst karar).

### §9.1 Kuralların komşu daldaki karşılığı

| Kural | anayasa karşılığı | kardeş dal karşılığı |
|---|---|---|
| B-S1 | anayasa §3.2 (VFS) | `cekirdek-yonetim` anayasa §6.21 (syscall sırası) |
| B-S5 | — | `kesme-ve-senkronizasyon` surucu §5.1 (ölçüm arayüzleri) |
| B-S8 | anayasa §6 dürüstlük | kardeş surucu dürüstlük |

**Eşleme kuralı:** Karşılığı olan kural **iki dosyada da** taşınır (tutarlılık);
karşılığı olmayan kural **bu dala özgüdür**.

## §10 Komşu sınır matrisi — bu dosyanın bilinçli taşımadıkları

| Taşınmayan | Nereye ait | Neden taşınmadı |
|---|---|---|
| uygulama↔sürücü ioctl uç noktası kodu | `isletim-sistemi-api` | API `-api` |
| güvenlik politikası (capability kararı) | `isletim-sistemi-security` | politika `-security` |
| gözlem (modül logu/sayaç) | `isletim-sistemi-observability` | sayaç `-observability` |
| test kodu (KUnit/QEMU çalıştırma) | `isletim-sistemi-testing` | kod `-testing` |
| donanım topolojisi (gerçek cihaz) | `K001-donanım`/`electroncis` | Q1 (UNKNOWN) |
| ses sürücüsü ayrıntısı (ASIO/ALSA) | `isletim-sistemi-audio` | ses `-audio` |

**Sınır kuralı:** Bu dosya **yalnız** sınır + test **omurgasını** işler;
kod, politika, sayaç ve uç nokta **taşınmaz** — taşınırsa komşu dalın
alanı gasp edilir.

### §10.1 Taşınan × taşınmayan özeti (tek tablo)

| Taşınan (bu dosya) | Taşınmayan (komşu) | Ayrım çizgisi |
|---|---|---|
| sınır doktrini (§4) | syscall/ioctl uç noktası kodu (`-api`) | doktrin vs kod |
| yetki yüzeyi kavramı (§4.3) | yetki kararı (`-security`) | yüzey vs politika |
| test omurgası (§5) | test çalıştırma kodu (`-testing`) | omurgan vs kod |
| test gereksinim matrisi (§8.1) | test senaryosu kodu (`-testing`) | gereksinim vs senaryo |
| ioctl 404 işareti (§4.3) | doğru kaynak arama | işaret vs çözüm |

## §11 Sık yapılan hatalar — bu dosyanın tam hata kataloğu (§6.4 + §11)

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| M1 | sınır doğrulamasını ihmal | güvenlik açığı | §4.4 matrisi |
| M2 | doğrudan gösterge vermek | çekirdek çökmesi | §4.5 doktrini |
| M3 | KUnit'i yeterli sanmak | sistem hatası kaçar | §5.1 doktrini |
| M4 | statik doğrulamayı test sanmak | yanlış güven | §5.4 doktrini |
| M5 | 404 URL'yi kullanmak | kanıtsız iddia | §4.3 işareti |
| M6 | 0 isabeti genişletmek | iddia taşması | §6.2 dürüstlük |
| M7 | test matrisinde "✓"yi "yapıldı" sanmak | yanlış rapor | §8.1 doktrini |
| M8 | hata kodunu evrensel sanmak | çekirdek karışığı | §7.2 eşleme |

**Hata kuralı:** M1, M2, M5, M6, M7, M8 bu dosyanın **doğruluk** hatalarıdır;
M3, M4 **yorum** hatalarıdır. Doğruluk hatası bir kez işlendiğinde dosya
`draft`'tan çıkmaz (yeniden denetim gerekir).

### §11.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| M1/M2 | sınır denetimi (§4.4) | ✅ geçti (doktrin yazılmış) |
| M3/M4 | test kavram denetimi | ✅ geçti (§5.1/§5.4) |
| M5 | URL doğrulama | ✅ geçti (404 işaretlendi) |
| M6 | K4 (olumsuz kanıt) | ✅ geçti (2026-10-13) |
| M7/M8 | rapor eşik denetimi | ✅ geçti (§7.2/§8.1 doktrini) |

## §12 Kapanış tablosu — bu dosyanın ne taşıdığı, ne taşımadığı

| Taşıdığı | Taşımadığı (işaretli) |
|---|---|
| sınır katmanları doktrini (§4.1) | gerçek syscall çağrısı (`UNKNOWN` — dolaylı) |
| syscall/ioctl doğrulama matrisi (§4.4) | Linux ioctl ayrıntısı (⚠️ 404) |
| yetki yüzeyi kavramı (§4.3) | yetki kararı (`-security`'de) |
| test omurgası (§5.1-§5.4) | test çalıştırma (PLANNED) |
| test gereksinim matrisleri (§8, §8.1) | test sonuçları (çalıştırılmadı) |
| 5 açık soru (§6.5) | kapanmış soru (yok) |

**Kapanış kuralı:** Sağ sütun **hiçbir zaman** soldakiyle değiştirilemez;
değişiklik yalnız gerçek test/üst kararla olur.

## §13 Değişiklik günlüğü (bu dosya — kapanış)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | 5 URL envanterden taşındı | kanıt kapısı |
| 2026-10-13 | Linux ioctl 404 işareti kaydedildi | kanıt kapısı |
| 2026-10-13 | §7 derinleştirme eklendi | dosya derinliği hedefi |
| 2026-10-13 | §8-§12 matris/kural/hata/kapanış eklendi | dosya derinliği hedefi |

### §13.1 Bu turda yapılanların özeti (yapısal)

| # | İşlem | Durum |
|---|---|---|
| 1 | envanter URL doğrulama (5) | ✅ (2026-10-13 HTTP 200) |
| 2 | kullanılmayan URL işaretleme (ioctl 404) | ✅ (§4.3) |
| 3 | grep olumsuz kanıt (3 arama/0) | ✅ (§6.2) |
| 4 | doktrin bölümleri (§4-§5) | ✅ (yazıldı) |
| 5 | test çalıştırma | ❌ yapılmadı (PLANNED) |
| 6 | gerçek sınır ölçümü | ❌ yapılmadı (⚠️) |

**Özet kuralı:** #5-#6 **bilinçli boşluktur**; kapanış yalnız gerçek test/
ölçümle olur, bu turda **olmaz**.

## §14 Bağlayıcı özet — sınır/test omurgası ve durum işaretleri (2026-10-15)

> Append-only ekleme: §0–§13 değişmemiştir; bu bölüm mevcut bölümleri derler,
> yeni test sonucu / yeni ölçüm iddiası üretmez.

### §14.1 Bölüm haritası

| Grup | Bölümler | Durum işareti |
|---|---|---|
| Doktrin | §4 Kullanıcı-kernel sınırı | anlatım |
| Test | §5 Sürücü testi (KUnit · QEMU · statik doğrulama) · §8 Test omurgası — test × sürücü türü tam matrisi | test **stratejisi**, çalıştırılmış sonuç değil |
| Kapanış | §6 Referans, risk ve kapanış · §9 Bağlayıcı kurallar özeti (8 kural) · §12 Kapanış tablosu | — |
| Sınır | §10 Komşu sınır matrisi · §11 Tam hata kataloğu (§6.4 + §11) · §13 Değişiklik günlüğü | — |
| Pratik | §7 Derinleştirme — sınır ve test pratiği (ek) | doktrin eki |

### §14.2 Durum işaretleri (okuma kuralı)

- Test omurgası (§5 · §8): **strateji/matris** — çalıştırılmış test sonucu DEĞİLDİR.
- 8 bağlayıcı kural §9'da sayılmıştır; metin tekrarı bu bölüme yazılmaz.
- Hata kataloğu §11'de birleştirilmiştir (§6.4 + §11); çelişkide §11 bütünlüğü kazanır.
- Kapanış tablosu (§12) bu bölümle tutarlıdır; yeni yetki eklenmez.

### §14.3 Sınır beyanı

- Anayasa (doktrin) kardeşi dosyadadır; çelişkide anayasa kazanır, bu dosya köprü/pratik taşır.
- Kullanıcı-kernel sınırı iddiaları doktrin seviyesindedir; ölçüm sonucu olarak sunulamaz.
- `git commit` bu oturumda ATILMADI — bu ekleme working tree'dedir (commit orkestratöre ait).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
