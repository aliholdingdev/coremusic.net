---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli/surucu-ve-modul-modeli-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/surucu-ve-modul-modeli
children: []
depends_on: []
evidence:
  - https://docs.kernel.org/driver-api/index.html
  - https://docs.kernel.org/driver-api/driver-model/index.html
  - https://docs.kernel.org/filesystems/vfs.html
  - https://docs.kernel.org/admin-guide/module-signing.html
  - https://man7.org/linux/man-pages/man8/insmod.8.html
  - https://man7.org/linux/man-pages/man2/delete_module.2.html
  - https://learn.microsoft.com/en-us/windows/hardware/drivers/kernel/
validation: "2026-10-13 · 7 URL envanterden (HTTP 200 · -dogrulama.md §E) · repo'da insmod/modprobe/init_module/file_operations YOK (grep 0 isabet) · hedef çekirdek UNKNOWN (Q4) → Linux model olarak taşınır, Windows yüzeyi ayrıca işaretli"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "`electroncis` ↔ kernel sürücü sınırı (Q1) → UNKNOWN"
group: mimari
---

# surucu-ve-modul-modeli-anayasa — Modül ve Sürücü Doktrini (Kademe-4 · §0-§3)

## §0 ELI15

Sürücü, çekirdeğin donanımı **konuşan** dilidir; modül ise bu sürücünün
**çalışırken takılıp çıkarılabilen** paketidir. Bu anayasa üç doktrini
kurar: (1) modül **nerede** yaşar (çekirdek-içi vs out-of-tree), (2)
modül **nasıl** yaşar (yükle → başlat → çalış → kaldır), (3) sürücü
**ne türdür** (VFS sınıfı, aygıt türü, yetki yüzeyi). Bağlayıcıdır:
`-surucu` (kullanıcı-kernel sınırı + test) ve `-ornekler` bu doktrine
dayanır.

| Öğe | Değer |
|---|---|
| Dosya rolü | Anasözleşme (Kademe-4 · `group: mimari`) |
| Bölüm omurgası | §0-§3 (bu dosya) · §4-§6 (`-surucu`) · §1-§6 (`-ornekler`) |
| Bağlı dosyalar | `surucu-ve-modul-modeli-surucu.md` · `surucu-ve-modul-modeli-ornekler.md` · kök `index.md` §1 |
| Kapsam | modül yerleşimi · yaşam döngüsü · sürücü sınıfları · yetki yüzeyi |
| Kapsam dışı | donanım topolojisi (`K001-donanım`/Q1) · güvenlik politikası (`-security`) · gözlem (`-observability`) · test (`-testing`) · ioctl uygulama (`-api`) |

> **Hedef çekirdek bilinmiyor (Q4 = UNKNOWN).** Bu dosyadaki mekanizma
> örnekleri Linux çekirdeği üzerinden anlatılır ve **model** olarak taşınır;
> Windows yüzeyi (driver-store, DeviceIoControl, WDK) ayrı resmi konu olarak
> işaretlenir. "Hedef OS budur" iddiası **yazılmaz**.

## §1 Modül yerleşimi — çekirdek-içi vs out-of-tree

### §1.1 Yerleşim türleri

| Tür | Ne demek | Nerede | Kim korur |
|---|---|---|---|
| çekirdek-içi (in-tree) | çekirdek ağacında | ana ağ | çekirdek topluluğu |
| out-of-tree | çekirdek ağacının dışında | harici depo | sürücü sahibi |
| yüklenmiş modül (`.ko`) | derlenmiş paket | çalışma zamanı | yükleyen |
| gömülü (built-in) | derleme zamanında içinde | çekirdek görüntüsü | — |

**Doktrin (1.1.1):** "Modül" **çalışma zamanı** paketidir; "sürücü"
**işlevdir** — aynı işlev hem modül hem gömülü olabilir. İkisini karıştırmak
"modül mü sürücü mü?" sorusunu anlamsız kılar (§3.4 eşleme).

### §1.2 In-tree vs out-of-tree — karşılaştırma

| Kriter | in-tree | out-of-tree |
|---|---|---|
| sürüm uyumu | çekirdek API'sine bağlı | API değişince kırılır |
| test | ana ağa PR + review | bağımsız |
| dağıtım | çekirdek paketiyle | bağımsız paket |
| imzalama | çekirdek politikasına bağlı | kendi imzası |
| CoreMusic durumu | — | Q1/Q4 UNKNOWN |

**Doktrin (1.2.1):** Out-of-tree modül **sürüm bağımlılığı** en yüksek
yüzeydir; çekirdek API'si değişince modül yeniden derlenmezse **yüklenmez**.
Bu risk her out-of-tree modülde **varsayılan**dır, istisna değil.

### §1.3 Modül API'si (çekirdek içi)

| Kavram | Ne demek | Doktrin |
|---|---|---|
| init/exit fonksiyonları | yükleme/kaldırma giriş noktaları | §2.1 |
| sembol dışa aktarma | modüller arası fonksiyon paylaşımı | §1.4 |
| sürüm sembolü (vermagic) | çekirdek sürümüne uyum | §1.5 |
| Kbuild/Kconfig | derleme ve yapılandırma | §1.6 |

### §1.4 Sembol paylaşımı ve bağımlılık

| Durum | Ne olur | Sonuç |
|---|---|---|
| sembol dışa aktarılmış | diğer modüller kullanabilir | bağımlılık doğar |
| sembol gizli | yalnız modül içi | bağımlılık yok |
| sembol silinmiş | çekirdek sürüm değiştirdi | modül yüklenmez |

**Doktrin (1.4.1):** Sembol paylaşımı **API sözleşmesidir**; çekirdek
sembolü silerse out-of-tree modül **kırılır** (§1.2 riski). `depmod(8)`
bağımlılık haritasını üretir (kaynak §4).

### §1.5 Sürüm uyumu (vermagic)

| Kavram | Ne demek | Hata |
|---|---|---|
| vermagic | modülün derlendiği çekirdek sürümü | uyuşmazsa reddedilir |
| uyuşmazlık | çekirdek sürümü değişti | yükleme hatası |
| atlatma (force) | uyumsuzluğu zorla | **riskli** (doktrin) |

**Doktrin (1.5.1):** Uyumsuz modül **zorla yüklenmez**; atlatma (force)
yalnız test/teşhis içindir — üretimde **yasaktır** (tahmin edilemez davranış).

### §1.6 Kbuild ve Kconfig (derleme yüzeyi)

| Araç | Ne yapar | Doktrin |
|---|---|---|
| Kbuild (makefiles) | modül derleme kuralları | §4 URL |
| Kconfig | yapılandırma (hangi modül derlenecek) | §4 URL |
| modpost | sembol denetimi | bağımlılık doğrulama |

**Doktrin (1.6.1):** Derleme yüzeyi **kod değildir**, **sözleşmedir**;
Kbuild/Kconfig kuralları ihlal edilirse modül **süzgeçten geçmez**
(§3.3 yetki denetimi ile birlikte).

## §2 Modül yaşam döngüsü

### §2.1 Yaşam döngüsü — adımlar

| # | Adım | Ne olur | Hata |
|---|---|---|---|
| 1 | derleme | `.ko` üretilir | derleme hatası |
| 2 | yükleme (`insmod`) | çekirdeğe takılır | sembol/vermagic hatası |
| 3 | başlatma (init) | kaynaklar ayrılır | tahsis hatası |
| 4 | çalışma | işlev sunar | — |
| 5 | kaldırma (exit) | kaynaklar serbest | serbest hatası |
| 6 | kaldırma (`delete_module`) | çekirdeğen çıkar | meşgul hatası |

**Doktrin (2.1.1):** Adım 3 ve 5 **eşleştirilmelidir**; init'de ayrılan
her kaynak exit'te serbest bırakılır. Eşleşmezse **kaynak sızıntısı**
(sürücü kaldırılsa bile kaynak kalır).

### §2.2 init/exit doktrini

| Kural | Neden |
|---|---|
| init kısa tutulur | yükleme bloke olmasın |
| exit, init'in aynası | kaynak sızıntısı yok |
| init hata yolu temizler | kısmi tahsis geri alınır |
| exit'te kilit/kesme bırakılır | askıda kilit kilitlenmesin |

**Doktrin (2.2.1):** Init'in **hata yolu** en kritik yoldur; başarılı
init herkesin bildiği yoldur, hata yolu ihmal edilir → sızıntı. Test bunu
**ayrı** doğrular (`-testing` alanı).

### §2.3 Yükleme araçları

| Araç | Ne yapar | Kaynak |
|---|---|---|
| `insmod(8)` | modülü doğrudan yükler | §4 |
| `depmod(8)` | bağımlılık haritası üretir | §4 |
| `modinfo(8)` | modül bilgisini okur | §4 |
| `delete_module(2)` | modülü kaldırır (syscall) | §4 |

**Doktrin (2.3.1):** Araçlar **kullanıcı uzayı**dır; asıl işlem çekirdekte
olur (syscall). Bu sınır `-surucu` §4'te işlenir; burada yalnız kavram taşınır.

### §2.4 Modül imzalama (ABI güvenlik)

| Kavram | Ne demek | Sonuç |
|---|---|---|
| imzalama | modülün yayıncısı doğrulanır | imzasız reddedilebilir |
| anahtar | çekirdekte güvenilir anahtar | politika |
| zorunlu mod | imzasız yükleme yasak | yüksek güvenlik |

**Doktrin (2.4.1):** İmzalama **yetki denetimidir**, doğruluk **değildir** —
imzalı hatalı modül yine hatalıdır. Güvenlik politikası `-security`
dalındadır (komşu sınır); burada yalnız mekanizma taşınır.

### §2.5 Kaldırma (exit/delete_module) doktrini

| Kural | Neden | Hata |
|---|---|---|
| meşgul modül kaldırılmaz | kullanıcı açık | meşgul hatası |
| kilit bırakılır | kilitlenme yok | kilitlenme |
| kesme kaydı serbest | kesme gelmez | kesme hatası |

**Doktrin (2.5.1):** Kaldırma, **çalışma zamanının en tehlikeli anıdır** —
hâlâ kullanıcı varsa kaynaklar aniden kesilir. Bu nedenle "meşgul" reddi
**özellikle** vardır, eksiklik değil.

## §3 Sürücü sınıfları ve yetki yüzeyi

### §3.1 Sürücü türleri (model)

| Tür | Ne işler | Tipik |
|---|---|---|
| karakter aygıtı | byte akışı | seri port, ses |
| blok aygıtı | blok erişimi | disk |
| ağ sürücüsü | paket | ethernet |
| dosya sistemi | VFS üzerinden | ext4, nfs |
| sanal aygıt | çekirdek içi | virtio |

**Doktrin (3.1.1):** Tür, **erişim modelini** belirler (byte vs blok);
VFS bunları kullanıcıya **dosya** olarak sunar (kaynak §4 vfs.html).

### §3.2 VFS ve `file_operations` (doktrin)

| Kavram | Ne demek | Doktrin |
|---|---|---|
| VFS | çekirdeğin sanal dosya sistemi katmanı | her şey dosya |
| `file_operations` | dosya operasyon tablosu (open/read/write) | sürücü bu tabloyu doldurur |
| inode | dosya meta verisi | — |
| dentry | dizin girişi | — |

**Doktrin (3.2.1):** Sürücü **yalnız** VFS'in sunduğu operasyonları sunar;
kullanıcı `open/read/write` çağırır, çekirdek bunu `file_operations`
tablosundaki sürücü fonksiyonuna yönlendirir. Repoda bu tablo **yok**
(grep 0 — §4 dürüstlük kaydı).

### §3.3 Yetki yüzeyi (capability · seccomp)

| Kavram | Ne demek | Doktrin |
|---|---|---|
| capability | yetkinin parçalara bölünmesi | her süreçte ayrı yetki |
| seccomp | syscall süzme | yalnız izinli syscall |
| ioctl | dosya operasyonu genişletme | `-api` alanı |

**Doktrin (3.3.1):** Sürücü **yetkiyle** çalışır; capability/seccomp
sürücünün **erişimini** sınırlar. Güvenlik politikası `-security` dalındadır
(komşu sınır); burada yalnız yüzey taşınır.

### §3.4 Sürücü × modül eşleme (karışıklık önleme)

| Soru | Cevap | Bölüm |
|---|---|---|
| sürücü modül mü olabilir? | evet (out-of-tree) | §1.1 |
| modül sürücü mü? | zorunlu değil (yardımcı modül olabilir) | §1.1 |
| gömülü sürücü var mı? | evet (built-in) | §1.1 |
| hangisi daha güvenli? | politika (`-security`) | §3.3 |

**Eşleme kuralı:** "Sürücü" = işlev (donanım konuşur); "modül" = paket
(taşınabilir). İkisi **farklı eksenlerdir**; "sürücü modülü" her ikisini
birden anlatır.

### §3.5 Windows yüzeyi — ayrı resmi konu (işaretli)

| Kavram | Karşılığı | Durum |
|---|---|---|
| kernel-mode sürücü | WDK ile derlenir | resmi konu (§4 URL) |
| driver-store | sürücü paketi dağıtımı | resmi konu (§4 URL) |
| DeviceIoControl | ioctl yüzeyi | resmi konu (§4 URL) |
| Static Driver Verifier | statik doğrulama | resmi konu (§4 URL) |

**Doktrin (3.5.1):** Windows yüzeyi **aynı doktrinin** farklı API'sidir;
kavram (sürücü · yetki · yaşam döngüsü) aynı, detay farklı. Hedef çekirdek
bilinmediği için (Q4) iki yüzey de **işaretli** taşınır, biri **seçilmez**.

## §4 Kaynakça (envanter · HTTP 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| M-W1 | Sürücü geliştirme arayüzü ana kaynağı | https://docs.kernel.org/driver-api/index.html | 200 (envanter) |
| M-W2 | Sürücü/donanım bağlama modeli (driver-model) | https://docs.kernel.org/driver-api/driver-model/index.html | 200 (envanter) |
| M-W3 | VFS çekirdek arayüzü (file_operations) | https://docs.kernel.org/filesystems/vfs.html | 200 (envanter) |
| M-W4 | Modül imzalama (ABI güvenlik) | https://docs.kernel.org/admin-guide/module-signing.html | 200 (envanter) |
| M-W5 | `insmod(8)` modül yükleme aracı | https://man7.org/linux/man-pages/man8/insmod.8.html | 200 (envanter) |
| M-W6 | `delete_module(2)` modül kaldırma | https://man7.org/linux/man-pages/man2/delete_module.2.html | 200 (envanter) |
| M-W7 | Windows kernel-mode sürücü yüzeyi | https://learn.microsoft.com/en-us/windows/hardware/drivers/kernel/ | 200 (envanter) |
| M-W8 | Kbuild makefile kuralları | https://docs.kernel.org/kbuild/makefiles.html | 200 (envanter) |
| M-W9 | Kconfig dil referansı | https://docs.kernel.org/kbuild/kconfig-language.html | 200 (envanter) |
| M-W10 | `depmod(8)` bağımlılık haritası | https://man7.org/linux/man-pages/man8/depmod.8.html | 200 (envanter) |
| M-W11 | `modinfo(8)` modül bilgi okuma | https://man7.org/linux/man-pages/man8/modinfo.8.html | 200 (envanter) |
| M-W12 | Windows driver-store | https://learn.microsoft.com/en-us/windows-hardware/drivers/install/driver-store | 200 (envanter) |
| M-W13 | Windows DeviceIoControl (ioctl) | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | 200 (envanter) |
| M-W14 | Windows WDK geliştirme merkezi | https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/ | 200 (envanter) |

**Doğrulama notu:** 14 URL de `-dogrulama.md` §E envanterinden yeniden
kullanıldı (2026-10-13 HTTP 200); bu dosyada **yeni** URL doğrulanmadı.
Linux ioctl (`userspace-api/ioctl.html`) bu oturumda **404** döndü —
**kullanılmadı**; Linux ioctl ayrıntısı `⚠️ VERIFICATION REQUIRED`.

### §4.1 URL × bölüm eşlemesi

| Bölüm | Destekleyen URL |
|---|---|
| §1 (yerleşim/API) | M-W1, M-W2, M-W8, M-W9 |
| §2 (yaşam döngüsü) | M-W4, M-W5, M-W6, M-W10, M-W11 |
| §3 (sınıflar/yetki) | M-W3, M-W7, M-W12, M-W13, M-W14 |

## §5 Açık / doğrulanamayan iddialar (bu dosya)

| # | İddia | Etiket | Neden |
|---|---|---|---|
| A1 | hedef çekirdek Linux'tur | ⚠️ (Q4) | UNKNOWN — model olarak taşınıyor |
| A2 | Linux ioctl ayrıntıları | ⚠️ VERIFICATION REQUIRED | userspace-api/ioctl.html 404 |
| A3 | `electroncis` ↔ sürücü sınırı (Q1) | UNKNOWN | sınır netleşmedi |
| A4 | core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar |
| A5 | repo'da modül/sürücü kodu yok | ✅ (grep 0) | dürüstlük kaydı (§6) |

## §6 Repo kanıtı (negatif — dürüstlük kaydı)

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| N1 | `insmod\|modprobe\|init_module` | tüm `*.php` | 0 | modül yükleme kodu yok |
| N2 | `file_operations` | tüm `*.php` | 0 | VFS operasyon tablosu yok |
| N3 | `spinlock` | tüm `*.php` | 0 | çekirdek kilit kodu yok |

**Dürüstlük cümlesi:** 0 isabet "**repoda o kod yok**" der; "**sistemde
sürücü yok**" **demez** — çekirdek her syscall'un altında çalışır
(`cekirdek-yonetim` ornekler §2.7 köprüsü). Bu dosyadaki iddialar yalnız
grep kapsamıyla sınırlıdır (2026-10-13, tüm `*.php`).

## §7 Derinleştirme — modül/sürücü pratiği (doktrin eki)

### §7.1 Modül yükleme akışı — adım adım (doktrin)

```text
[kullanıcı] --> [insmod/depmod] --> [syscall: init_module]
                                         |
                                         v
                              [çekirdek: vermagic denetimi]
                                         |
                              [imza denetimi (§2.4)]
                                         |
                              [init() çağrısı] --> [başarılı: modül aktif]
                                         |                  |
                              [hata: temizle]          [exit() ile kaldırma]
```

| Adım | Olay | Hata | Kaynak |
|---|---|---|---|
| 1 | araç çağrısı | dosya yok | M-W5 |
| 2 | syscall | vermagic uyuşmaz | M-W6 |
| 3 | imza denetimi | imzasız | M-W4 |
| 4 | init() | tahsis hatası | §2.2 |
| 5 | aktif | — | — |
| 6 | exit() | sızıntı | §2.5 |

**Akış kuralı:** Adım 3 **her yüklemede** çalışır (eğer politika zorunlu
kıldıysa); adım 4'ün **hata yolu** en kritik yoldur (§2.2 doktrini).

### §7.2 Sürücü × yaşam döngüsü tam matrisi

| Sürücü türü ↓ / Yaşam → | init | çalış | exit | Kaldırma riski |
|---|---|---|---|---|
| karakter aygıtı | kaynak + VFS kaydı | byte akışı | VFS kaydı bırak | açık tutucu |
| blok aygıtı | disk kaydı | blok erişimi | disk kaydı bırak | I/O tamponu |
| ağ sürücüsü | arayüz kaydı | paket | arayüz bırak | trafik |
| dosya sistemi | VFS kaydı | dosya işlem | VFS bırak | açık dosya |
| sanal aygıt | sanal kaynak | sanal işlem | sanal bırak | düşük |

**Matris kuralı:** Her türün init/exit'i **eşleşir** (§2.1 doktrini);
"Kaldırma riski" sütunu **kavramsal** risktir, ölçüm **değildir**.

### §7.3 Yetki × sürücü — hangi yetki hangi sürücüde gerekir

| Yetki | Ne verir | Sürücü etkisi | Kaynak |
|---|---|---|---|
| capability | parçalı yetki | sürücü erişimi sınırlı | §3.3 |
| seccomp | syscall süzme | sürücü çağrıları süzülür | §3.3 |
| imza | yayıncı doğrulama | imzasız modül reddi | §2.4 |
| VFS izni | dosya erişimi | sürücü dosyası erişimi | §3.2 |

**Matris kuralı:** Yetkiler **katmanlıdır**; birinin olması diğerinin yerine
geçmez (imza ≠ capability ≠ VFS izni).

## §8 Sık yapılan hatalar — bu dalda görülmesi muhtemel 8 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| H1 | modülü sürücü sanmak | kavram kayması | §3.4 eşleme |
| H2 | init/exit eşleşmesi ihlali | kaynak sızıntısı | §2.1 doktrini |
| H3 | init hata yolunu ihmal | kısmi tahsis kalır | §2.2 doktrini |
| H4 | uyumsuz modülü zorla yükleme | tahmin edilemez davranış | §1.5 doktrini |
| H5 | imzalama ≠ güvenlik sanmak | yanlış güven | §2.4 doktrini |
| H6 | Windows/Linux API'sini karıştırma | yanlış yüzey | §3.5 doktrini |
| H7 | 0 isabeti "sürücü yok" diye genişletmek | iddia taşması | §6 dürüstlük |
| H8 | ioctl ayrıntısını doğrulanmış sanmak | 404 URL | §5 A2 |

**Hata kuralı:** H1, H5, H7, H8 bu dosyanın **doğruluk** hatalarıdır;
diğerleri **yorum** hatalarıdır. Doğruluk hatası bir kez işlendiğinde
dosya `draft`'tan çıkmaz (yeniden denetim gerekir).

### §8.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| H1/H5 | kavram denetimi | ✅ geçti (§2.4, §3.4) |
| H2/H3 | kod incelemesi | ❌ açık (repo'da modül kodu yok) |
| H4 | politika denetimi | ✅ geçti (§1.5 doktrini) |
| H6 | hedef çekirdek | ❌ açık (Q4 UNKNOWN) |
| H7/H8 | URL/olumsuz kanıt | ✅ geçti (§4, §6) |

## §9 Belirsizlik ve sınır tablosu (bu dosyanın kapanışı)

| Konu | Durum | Nereye kapılı |
|---|---|---|
| donanım topolojisi (Q1) | UNKNOWN | `K001-donanım`/`electroncis` |
| hedef çekirdek (Q4) | UNKNOWN | üst karar |
| core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar |
| ioctl uygulama yüzeyi | bu dosya dışı | `isletim-sistemi-api` |
| güvenlik politikası | bu dosya dışı | `isletim-sistemi-security` |
| test (kunit/QEMU) | bu dosya dışı | `isletim-sistemi-testing` |
| repo modül/sürücü kodu | **yok** (grep 0) | §6 dürüstlük |

**Sınır kuralı:** Bu dosya **yalnız** modül yerleşimi + yaşam döngüsü +
sürücü sınıfları + yetki yüzeyini işler; her satır ya bu dosyada kanıtlanır
ya da yukarıdaki tabloya kapılır — **belirsizlik taşınmaz, işaretlenir**.

### §9.1 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | 14 URL envanterden taşındı | kanıt kapısı |
| 2026-10-13 | Linux ioctl 404 kaydedildi (kullanılmadı) | kanıt kapısı |
| 2026-10-13 | §7-§9 derinleştirme eklendi | dosya derinliği hedefi |

## §10 Bağlayıcı kurallar özeti (bu dosyanın 10 kuralı)

| # | Kural | Bölüm |
|---|---|---|
| B-M1 | out-of-tree modülde sürüm bağımlılığı varsayılan risktir | §1.2 |
| B-M2 | uyumsuz modül zorla yüklenmez (force yasak) | §1.5 |
| B-M3 | init/exit eşleştirilir; hata yolu temizlenir | §2.1, §2.2 |
| B-M4 | meşgul modül kaldırılmaz (reddi özelliktir) | §2.5 |
| B-M5 | imzalama yetki denetimidir, doğruluk değil | §2.4 |
| B-M6 | sürücü = işlev, modül = paket (farklı eksen) | §3.4 |
| B-M7 | sürücü yalnız VFS sözleşmesini sunar | §3.2 |
| B-M8 | yetkiler katmanlıdır (imza ≠ capability ≠ VFS izni) | §7.3 |
| B-M9 | Windows/Linux yüzeyi karıştırılmaz (hedef UNKNOWN) | §3.5 |
| B-M10 | 0 isabet yalnız kapsamıyla der (sürücü-yok demez) | §6 |

**Kural kuralı:** Bu 10 kural bu ağacın bağlayıcısıdır; `-surucu` ve
`-ornekler` bunlara **aykırı** yazamaz (aykırılık → ADR/üst karar).

### §10.1 Kuralların komşu daldaki karşılığı

| Kural | `cekirdek-yonetim` karşılığı | `kesme-ve-senkronizasyon` karşılığı |
|---|---|---|
| B-M1 | — | — |
| B-M3 | anayasa §6.4 (fork/wait eşleşme) | anayasa §1.2 (kesme akışı) |
| B-M6 | — | anayasa §2.1 (kilit türleri) |
| B-M10 | ornekler §3.2 dürüstlük | surucu §6.1 dürüstlük |

**Eşleme kuralı:** Karşılığı olan kural **iki dosyada da** taşınır (tekrar
değil, tutarlılıktır); karşılığı olmayan kural **bu dala özgüdür**.

## §11 Ölçüm — bu dosyanın kendi ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | URL sayısı (yeniden kullanım) | 14 (HTTP 200) | ölçüldü (2026-10-13) |
| 2 | yeni URL doğrulama | 0 | bilinçli (envanter yeterli) |
| 3 | negatif kanıt (grep) | 3 arama / 0 isabet | ölçüldü (2026-10-13) |
| 4 | kullanılmayan URL | 1 (ioctl 404) | ölçüldü (2026-10-13) |
| 5 | hedef çekirdek | — | `UNKNOWN` (Q4) |
| 6 | repo modül kodu | — | **yok** (grep N1-N3) |
| 7 | açık soru | 3 (Q1, Q2, Q4) | açık |

**Ölçüm dürüstlüğü:** Tablodaki 1-4 **yapısal ölçüm** (URL/grep); 5-7
**durum ölçümüdür** ve gerçek değer üretilmemiştir.

## §12 Komşu sınır matrisi — bu dosyanın bilinçli taşımadıkları

| Taşınmayan | Nereye ait | Neden taşınmadı |
|---|---|---|
| donanım topolojisi · PCB · DAC/ADC | `K001-donanım`/`electroncis` | Q1 sınırı (UNKNOWN) |
| OS türü (yükleme modeli sınıflandırması) | `isletim-sistemi-turu` | tür `-turu` |
| ses sürücüsü (ASIO/ALSA/CoreAudio) | `isletim-sistemi-audio` | ses `-audio` |
| sürücü güvenlik politikası · imzalama politikası | `isletim-sistemi-security` | politika `-security` |
| sürücü gözlemi · modül logu | `isletim-sistemi-observability` | sayaç `-observability` |
| sürücü testi · kunit/QEMU testi | `isletim-sistemi-testing` | test `-testing` |
| uygulama↔sürücü ioctl yüzeyi | `isletim-sistemi-api` | API `-api` |

**Sınır kuralı:** Bu dosya **yalnız** modül yerleşimi + yaşam döngüsü +
sınıf/yetki doktrinini işler; yukarıdaki satırlar **bilinçli taşınmaz** —
taşınırsa çakışma (overlap) doğar ve komşu dalın alanı gasp edilir.

### §12.1 Taşınmayan × taşınan özeti (tek tablo)

| Taşınan (bu dosya) | Taşınmayan (komşu) | Ayrım çizgisi |
|---|---|---|
| modül yaşam döngüsü doktrini (§2) | test kodu (kunit/QEMU) | doktrin vs kod |
| VFS/file_operations kavramı (§3.2) | ioctl uygulama (`-api`) | kavram vs uç nokta |
| imzalama mekanizması (§2.4) | imzalama politikası (`-security`) | mekanizma vs politika |
| yetki yüzeyi kavramı (§3.3) | güvenlik denetimi (`-security`) | yüzey vs denetim |
| Windows/Linux yüzey ayrımı (§3.5) | hedef seçimi (Q4) | ayrım vs seçim |

## §13 Bağlayıcı özet — modül/sürücü doktrini omurgası (2026-10-15)

> Append-only ekleme: §0–§12 değişmemiştir; bu bölüm mevcut bölümleri derler,
> yeni kural / yeni ölçüm eklemez.

### §13.1 Bölüm haritası

| Grup | Bölümler | Not |
|---|---|---|
| Doktrin | §1 Modül yerleşimi (çekirdek-içi vs out-of-tree) · §2 Modül yaşam döngüsü · §3 Sürücü sınıfları ve yetki yüzeyi | çekirdek omurgası |
| Kaynak | §4 Kaynakça (envanter · HTTP 200 · 2026-10-13) | tarihli |
| Dürüstlük | §5 Açık / doğrulanamayan iddialar · §6 Repo kanıtı (negatif — dürüstlük kaydı) | işaretli eksiklik |
| Pratik | §7 Derinleştirme — modül/sürücü pratiği (doktrin eki) · §8 8 sık hata | — |
| Kapanış | §9 Belirsizlik ve sınır tablosu · §10 Bağlayıcı kurallar özeti (10 kural) · §11 Ölçüm tablosu · §12 Komşu sınır matrisi | — |

### §13.2 Bağlayıcı kurallar karnesi

- 10 bağlayıcı kural §10'da sayılır; bu bölüm kural metnini tekrar etmez.
- Ölçüm §11'de bu dosyanın kendi ölçüm tablosu olarak taşınır; dış ölçüm alınmaz.
- Repo kanıtı negatiftir (§6) — "repo'da sürücü kodu vardır" iddiası bu dosyada YOKTUR.

### §13.3 Sınır beyanı

- Test omurgası (KUnit · QEMU · statik doğrulama) kardeş sürücü dosyasındadır — burada taşınmaz.
- Açık sorular açık, doğrulanamayan iddialar işaretli kalır (§5).
- `git commit` bu oturumda ATILMADI — bu ekleme working tree'dedir (commit orkestratöre ait).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
