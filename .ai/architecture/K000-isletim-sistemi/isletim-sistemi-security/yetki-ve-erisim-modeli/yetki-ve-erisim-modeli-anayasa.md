---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security
  - K000-isletim-sistemi/isletim-sistemi-core
evidence:
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://man7.org/linux/man-pages/man7/attributes.7.html
  - https://man7.org/linux/man-pages/man2/capget.2.html
  - https://docs.kernel.org/admin-guide/LSM/SELinux.html
  - https://docs.kernel.org/userspace-api/landlock.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionInitializer.php:31
  - shared/src/Middleware/PermissionMiddleware.php:31
validation:
  - "2026-10-13: 6 web URL HEAD ile HTTP 200; ucu da 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 3 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? [UNKNOWN]"
group: mimari
---

# yetki-ve-erisim-modeli-anayasa

> **Kapsam (tek cumle):** Isletim sisteminde **kim ne yapabilir** sorusunun dort
> katmani — DAC (sahiplik), MAC (etiket/siyaset), RBAC (rol), BEL (bilgi
> seviyesi) — ve bu katmanlarin POSIX/ACL/capability mekanizmalariyla nasil
> karsilandigi.

> **Oncelikli gercek:** Bu dalin anayasasi **OS seviyesinde tanim** tasir; repo'da
> bu tanimlarin uygulama-katmani karsiligi **vardir** (RBAC middleware, dizin
> izni, `.htaccess` FilesMatch) ama OS-katmani karsiligi (capability, LSM, ACL
> komutlari) **yoktur** (2026-10-13 negatif kanit: `umask(`/`chmod(`/`setfacl`
> cagrisi PHP agacinda 0 sonuc, vendor/node_modules hariç).

## §1 — ELI15 (15 yasinda birine anlatim)

Bir evi dusun. Dort tane kilit katmani var:

1. **Sahip kilidi (DAC):** Kapinin anahtari kimdeyse o acar. Dosyanin sahibi
   kisi, dosya uzerinde en yetkilidir; grubu ve herkesi ayrica sinirlarsiniz.
2. **Etiket kilidi (MAC):** Eve "misafir odasi" etiketi yapistirilmistir; anahtar
   olsa bile etiket "mutfak" diyorsa mutfaga giremezsiniz. Etiketi sistem
   yonetirisi koyar, kullanici cikaramaz.
3. **Rol kilidi (RBAC):** "Temizlikci" rolu sadece belirli isleri yapar; rol
   degisince yetki degisir, kisi degismese bile.
4. **Bilgi seviyesi (BEL):** Gazete "herkese acik", sozlesme "sadece yoneticiye".
   Bilgi sinifi, erisimden once gelir.

Dort katman da birlikte calisir; **hicbiri digerinin yerine gecmez.** DAC
varsayilan olandir, MAC uzerine bindirilen ikinci kilit, RBAC uygulama
katmaninda yasar, BEL nadir kullanilir.

## §2 — Kavram haritasi (dort katman karsilastirmasi)

| Katman | Soru | Karar veren | Kaldirilabilir mi | Repo'da karsiligi |
|---|---|---|---|---|
| DAC | Sahibi miyim? | dosya sahibi + `mode` | sahip evet (chmod) | `mkdir 0750` / `0777` |
| MAC | Etiket uyusuyor mu? | cekirdek + siyaset | hayir (yonetici bile) | **yok** (LSM kullanilmiyor) |
| RBAC | Rolum yetki veriyor mu? | uygulama middleware'i | evet (rol atama) | `PermissionMiddleware` |
| BEL | Bilgi sinifi ne? | dosya etiketi / siniflandirma | hayir | **yok** (tasarim disi) |

**Sira kurali (onemli):** Denetim sirasi genelde `BEL -> MAC -> DAC -> ACL ->
RBAC` seklindedir; ilk "deny" kazanir (fail-closed). RBAC'in DAC onune gecmesi
mumkun degildir — uygulama yetkisi, isletim sisteminin izin vermedigi bir
dosyaya yazamaz.

## §3 — POSIX izin modeli (bit ve octal)

Dokuz bit + 3 ek bit:

| Bit | Anlami | Octal agirlik |
|---|---|---|
| `r` (sahip/grup/diğer) | okuma | 4 |
| `w` (sahip/grup/diğer) | yazma | 2 |
| `x` (sahip/grup/diğer) | calistirma / dizin icinde gezinme | 1 |
| `s` (setuid) | calisirken dosya sahibi kimligine gecer | 4000 (octal) |
| `s` (setgid) | calisirken dosya grubu kimligine gecer | 2000 (octal) |
| `t` (sticky) | sadece dosya sahibi silebilir ( /tmp ) | 1000 (octal) |

`0750` cozumlemesi:

```text
0     -> koruma biti yok (octal baslangic)
7     -> sahip  : rwx  (4+2+1 = 7)
5     -> grup   : r-x  (4+0+1 = 5)
0     -> diger  : ---  (0+0+0 = 0)
```

`0777` cozumlemesi: sahip/grup/diger hepsi `rwx` — herkes yazabilir ve
silebilir. Paylasilan oturum dizinleri icin **guvenli degildir** (bkz. §4).

Setuid/setgid riski: bu biti olan bir program, cagiren herkesin yetkisiyle
calisir. Boyle dosyalar imza/ sahiplik bakimindan sik kontrol edilir; setuid
shell dusurme, klasik yer-gecis (privilege escalation) yontemidir.

## §4 — umask ve dizin izinleri (repo kaniti)

`umask`, olusturulan dosyadan **izin cikaran** maskedir:

```text
istenen mod  &  ~umask  =  nihai mod
```

Tipik degerler: `0022` (grup yazamaz) · `0077` (sadece sahip) · `0007` (grup
girer, diger girmez). `umask` cagrisi PHP agacinda **gozlenmedi** (2026-10-13
negatif kanit); izinler `mkdir` ucuncu/ikinci argumaniyla **acikca** veriliyor.

**Kontrast kaniti (bu dalin en onemli bulgusu):** ayni uygulama icinde oturum
dizini izni **iki farkli degerle** olusturuluyor:

```text
Dosya: shared/src/Session/SessionInitializer.php
Satir: 31
Select-Object -Index 30
```

```php
            @mkdir($savePath, 0777, true);
```

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

| Kaynak | Satir | Mod | Diger erisim |
|---|---|---|---|
| `shared/src/Session/SessionInitializer.php` | 31 | `0777` | herkes okur/yazar/siler |
| `shared/src/Session/SessionConfig.php` | 35 | `0750` | sadece sahip + grup |
| `shared/src/Session/SessionConfig.php` | 20 | `0750` (`DIR_PERMISSIONS`) | sabit tanim |
| `home.coremusic.net/config/constants.php` | 77 | `0750` | ayni deger |

**Guvenlik etkisi:** `0777` ile olusturulan oturum dizini, paylasilan bir
makine farkli kullanici tarafindan okunabilir/yazilabilir olur (oturum
sablonlari / session dosyasi sizintisi). `0750` tercih edilmelidir; **iki
degerin birbirine indirgenmesi gerekliligi mimari sahibi kararidir**
(`open_questions` S2'ye baglidir, bu dosyada karar verilmez).

## §5 — POSIX ACL (erisim kontrol listesi)

Klasik 9-bit model "sahip/grup/diger" disindaki kisileri ayrim yapamaz. POSIX
ACL bunu genisletir:

| Araç | Ne yapar | Repo durumu |
|---|---|---|
| `getfacl` | ACL okuma | cagri **yok** |
| `setfacl` | ACL yazma | cagri **yok** |
| `mask` | grup/ACL girisi ust siniri | — |
| `default:ACL` | yeni olusturulanlara miras | — |

ACL girisi bicimi: `user:kullanici:rw-` · `group:muhendis:r--` ·
`mask:rwx` · `other:---`.

**Ornek senaryo:** proje klasorunu iki muhendise ac, ucuncuye kapat. DAC bunu
`grup` bitiyle cozemez (iki kullanici ayni grupta olmayabilir); ACL
`user:ahmet:rw-` + `user:veli:rw-` ile cozulur.

`setfacl` / `getfacl` cagrilari PHP agacinda **0 sonuc** (2026-10-13 olcum,
vendor/node_modules hariç). ACL bu sistemde **kullanilmiyor**; klasik 9-bit +
`0750` yeterli gorulmus. Bu bir **bilincli secim mi, eksiklik mi** -> S2 acik
sorusuna baglidir `[UNKNOWN]`.

## §6 — Capability modeli (yetki setleri)

Tum "root = her sey" yetkisi, ince ayarlanabilir yetki setlerine bolunur:

| Set | Ne yapar | Ornek |
|---|---|---|
| `CAP_NET_BIND_PORT` | 1024 altina port acma | `bind` |
| `CAP_SYS_ADMIN` | mount, sistem yonetimi | genis yetki |
| `CAP_SETUID` | uid degistirme | su benzeri |
| `CAP_DAC_OVERRIDE` | DAC'i atlatma | klasik root yetkisi |
| `CAP_SYS_PTRACE` | baskasini izleme | hata ayiklama |
| `CAP_SYS_MODULE` | kernel modulu yukleme | surucu yukleme |

Okuma araci: `capget(2)` (hucre U52) · insan-okur arac: `getpcaps`.
`capget(` / `capset(` cagrilari PHP agacinda **0 sonuc** (negatif kanit).

**Neden onemli:** Capability, "root mu, degil mi" ikiligini kirar. Bir ag
sunucusu `CAP_NET_BIND_PORT` alip `CAP_SYS_MODULE`'den mahrum birakilabilir.
Bu dalin `-surucu` dosyasi surucu yukleme yetkisinin (`CAP_SYS_MODULE`)
neden ayri ele alindigini aciklar.

## §7 — MAC: SELinux / AppArmor / Landlock

| Mekanizma | Siyaset bicimi | Kapsam | Repo durumu |
|---|---|---|---|
| SELinux | sinif/etiket bazli (rule) | process + dosya etiketi | yok |
| AppArmor | profil bazli (path) | tek process profili | yok |
| Landlock | basit, salt Linux | process kendi kendini sinirlar | yok |
| Yama | ptrace sinirlama | yalnizca izleme | yok |

Farklar:

- **SELinux:** her nesnenin etiketi (`user_u:object_r:httpd_sys_content_t`)
  var; siyaset etiketler uzerinden yazilir. Etiketi yonetici koyar.
- **AppArmor:** path tabanli profil (`/var/www/** r,`). SELinux'a gore daha
  basit, daha az bakim.
- **Landlock:** uygulama **kendi kendini** sinirlar; root yetkisi gerekmez.
  Sandbox kurmak icin en dusuk yetki yolu.

**Bu sistemde hicbiri etkin degil** (2026-10-13: `aa-status`/`sestatus`
cagrisi yok, PHP agacinda LSM etkilesimi 0). Bu, bir **acik mi, kasitli mi**
-> `open_questions` S1 `[UNKNOWN]`.

## §8 — Dosya sistemi etiketleri (MAC'in dosya yuzu)

Etiket yazma/okuma araçlari:

| Araç | Islev | Repo durumu |
|---|---|---|
| `setfilecon(3)` | tek nesneye etiket yazma | yok |
| `setfiles(8)` | dizinde etiket-esleme (restorecon benzeri) | yok |
| `chcon(1)` | calistirilirken etiket degistirme | yok |
| `ls -Z` | etiket goruntuleme | — |
| `matchpathcon` | dogru etiketi hesaplama | yok |

**Risk:** `chcon` ile verilen etiket, dosya tekrar etiketlendiginde kaybolur;
kalici etiket `setfilecon`/siyaset yoluyla verilir. Etiketsiz bir dosya,
MAC siyasetinde "herhangi bir etiket" gibi islenir -> genelde **en gevsek
dusme** (fail-open) yonune calisir; bu yuzden etiket sagligi denetlenir.

## §9 — Windows ACL yuzu (karsilastirma)

Windows tarafinda DAC benzeri yapilandirma Security Descriptor (SD) uzerindedir:

| Kavram | Windows karsiligi | Ilgili arac |
|---|---|---|
| ACL | Dis / Ici ACL | `InitializeSecurityDescriptor` (U40) |
| SACL | Denetim kaydi ACL | `auditpol` |
| Yetki biti | GenericRead/Write/Execute | `access mask` |
| App Control | Uygulama calistirma politikasi | WDAC / App Control (U64) |
| Job Object | Surec kisitlama (sandbox benzeri) | `CreateJobObjectA` (U39) |

`InitializeSecurityDescriptor` / `CreateJobObjectA` cagrilari PHP agacinda
**0 sonuc** (negatif kanit). Windows ACL yuzu bu repo icin **sadece hedef
referansidir**.

## §10 — RBAC: uygulama katmanindaki gercek denetim

OS disindaki yetki katmani middleware'de yasar. Gercek kod:

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 31
Select-Object -Index 30
```

```php
        if ($requiredRole !== null && $userRole !== $requiredRole) {
```

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 39
Select-Object -Index 38
```

```php
            if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
```

Okuma zinciri:

| Adim | Dosya:S | Ne yapar |
|---|---|---|
| Rol cekme | `shared/src/Middleware/AuthMiddleware.php:26` | `role` oturum verisine yazilir |
| Yetki dizisi | `shared/src/Middleware/AuthMiddleware.php:29` | `permissions` dizisi (RBAC B-F-12) |
| Rol karsilastirma | `shared/src/Middleware/PermissionMiddleware.php:31` | esit degilse 403 |
| Yetki karsilastirma | `shared/src/Middleware/PermissionMiddleware.php:39` | `in_array(..., true)` — **strict** |

**Iyi tasarim noktalari:**

1. `in_array(..., true)` — ucuncu arguman `true` ile **tip guvenli** karsilastirma
   (`"1" === 1` olmaz); tip-donusumu soylem (type juggling) kapali.
2. `?? 'user'` — rol yoksa **en dusuk yetki** varsayilir (fail-closed tarafli).
3. Rol karsilastirmasi `!==` ile yapilir; esitlik `==` degildir.
4. Yetki dizisi `is_array` ile once tip kontrolunden gecer.

**Kalan acik nokta:** rol karsilastirmasi **esitlik** (`!==`) uzerinden —
yani hiyerarsik rol (admin > editor > user) **degil**, birebir eslesme aranir.
Hiyerarsi gerekiyorsa bu kasitli bir karar midir -> `open_questions` S2.

## §11 — Denetim akisi (syscall'ten karara)

```text
istemci istegi
  -> uygulama middleware zinciri (Auth -> Permission -> Validation)
       [RBAC katmani — §10]
  -> dosya sistemi cagrisi (open/mkdir/write)
       [ACL varsa — §5]
       [DAC: sahip/grup/diger bitleri — §3 + umask — §4]
       [MAC: etiket + siyaset — §7/§8 — bu sistemde etkin degil]
       [capability: CAP_DAC_OVERRIDE vb. — §6 — bu sistemde etkin degil]
  -> karar: allow / deny (ilk deny kazanir, fail-closed)
```

**Denetim kaydi (audit):** Linux `auditd`/`audit.log`, Windows Security Log.
Bu repo'da denetim kaydi **uygulama seviyesinde** tutulur (vault `log.md`
append-only kurali); isletim sistemi audit alt yapisi **etkin degil** (negatif
kanit: `audit` cagrisi PHP agacinda 0).

## §12 — Olcum komutlari (yeniden uretilebilir)

```powershell
# dosya izni (Linux hedefi)
Get-Content /path/dizin | Select-Object -First 1   # sembolik: ls -l

# etiket goruntuleme (Linux)
Get-Content /proc/self/attr/current

# umask degeri
Get-Content /proc/self/status

# capability setleri
Get-Content /proc/self/status    # CapEff / CapPrm alanlari (hex)

# Windows ACL
(Get-Acl C:\yol).Access
```

Bu komutlarin hicbiri bu oturumda calistirilmadi (**taslak**); calistirilan
negatif kanitlar `-dogrulama.md` D-01..D-43 listesindedir (PHP agaci taramasi).

## §13 — Riskler (bu katmanin kendi riskleri)

| # | Risk | Etki | Katman |
|---|---|---|---|
| 1 | dizin `0777` ile acilmasi | paylasilan makinede oturum verisi okunur | DAC |
| 2 | setuid biti olan bakimsiz binary | yer-gecis (privilege escalation) | DAC |
| 3 | ACL mask'inin genis birakilmasi | gruptaki herkes yazar | ACL |
| 4 | etiketin `chcon` ile kaybedilmesi | MAC fail-open | MAC |
| 5 | `CAP_DAC_OVERRIDE` verilmis hizmet | tum dosyalari okur | capability |
| 6 | rol esitligi ile hiyerarsi sanilmasi | yetki genislemesi | RBAC |
| 7 | fail-open rol varsayilani (bos rol = admin) | yetkisiz erisim | RBAC |

**S1/S2 etkisi:** Bu dosyada 4, 5, 7 numarali risklerin OS-karsiligi
`[UNKNOWN]` olarak kalir; 1, 3, 6 numaralilar repo'da **gozlenebilir** ve
ilgili `-ornekler` dosyasinda koduyla yazilmistir.

## §14 — Kontrol listesi (revizyon kapisi)

- [ ] Her `mkdir` cagrisinin mod degeri `0750` ile tutarli mi? (§4 kontrasti)
- [ ] `umask` acikca ayarlanmis mi, yoksa sistem varsayilanina mi birakilmis?
- [ ] RBAC karsilastirmalari strict (`===` / `in_array(..., true)`) mi?
- [ ] Bos rol/izin durumu **en dusuk yetkiye** mi duser? (fail-closed)
- [ ] ACL/`setfacl` kullanilacaksa mask degeri yazili mi?
- [ ] MAC (SELinux/AppArmor) etkinlestirilecekse etiket sagligi denetleniyor mu?
- [ ] `CAP_SYS_MODULE` / `CAP_DAC_OVERRIDE` hicbir hizmete verilmis mi?
- [ ] Windows hedefi icin ACL miras devri (inheritance) kapali mi?

## §15 — Referans (HTTP 200, 2026-10-13, envanter disi)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| POSIX `chmod` spesifikasyonu yetki bitlerini tanimlar | https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | 2026-10-13 | HTTP 200 (HEAD) |
| `umask(2)` olusturulan izinlerden maske cikarir | https://man7.org/linux/man-pages/man2/umask.2.html | 2026-10-13 | HTTP 200 (HEAD) |
| POSIX dosya nitelikleri (attributes) belgesi | https://man7.org/linux/man-pages/man7/attributes.7.html | 2026-10-13 | HTTP 200 (HEAD) |
| `capget(2)` yetki setlerini okur | https://man7.org/linux/man-pages/man2/capget.2.html | 2026-10-13 | HTTP 200 (HEAD) |
| SELinux cekirdek entegrasyonu LSM uzerindedir | https://docs.kernel.org/admin-guide/LSM/SELinux.html | 2026-10-13 | HTTP 200 (HEAD) |
| Landlock uygulamanin kendini sinirlamasini saglar | https://docs.kernel.org/userspace-api/landlock.html | 2026-10-13 | HTTP 200 (HEAD) |
| `setfacl(1)` POSIX ACL girdisi yazar | https://man7.org/linux/man-pages/man1/setfacl.1.html | 2026-10-13 | HTTP 200 (HEAD) |
| `getfacl(1)` POSIX ACL girdisini okur | https://man7.org/linux/man-pages/man1/getfacl.1.html | 2026-10-13 | HTTP 200 (HEAD) |

Tum URL'ler `-dogrulama.md` §1.2 U-havuzundan alinmistir; 2097'lik K000
katalog envanteriyle kesisim **0** (ayni dosyada olculdu).

## §16 — Acik sorular (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S1 | OS guvenlik katmani (capability/LSM/ACL) hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | Mimari sahibinin yazili karari + ADR (yeni ise `>=088`) |
| S2 | Oturum dizini izni neden tek degere (muhtemelen `0750`) indirgenmiyor? | `[UNKNOWN]` | Mimari sahibi karari; kod degisikligi bu vault'ta YAPILMAZ |

> **SORUMLULUK SINIRI:** Bu dosya tanim/olculer tasir. Izin degeri degisikligi,
> rol modeli degisikligi veya LSM devreye alimi **backend-architect** isidir;
> security-engineer degil.

## §17 — Sözlük (bu dosyanin terimleri)

| Terim | Kisa tanim | Ilgili § |
|---|---|---|
| DAC | Discretionary Access Control — sahip karari | §3 |
| MAC | Mandatory Access Control — sistem/etiket karari | §7 |
| RBAC | Role-Based Access Control — rol uzerinden yetki | §10 |
| BEL | Bilgi seviyesi / siniflandirma katmani | §2 |
| ACL | Access Control List — nesne-bazli izin listesi | §5 |
| umask | Olusturulan dosyadan izin cikaran maske | §4 |
| setuid | Program calisirken sahip kimligine gecis | §3 |
| setgid | Program calisirken grup kimligine gecis | §3 |
| sticky bit | Silme yetkisini dosya sahibine kisitlar | §3 |
| capability | Ince ayarlanabilir yetki parcasi | §6 |
| LSM | Linux Security Module catisi (SELinux/AppArmor) | §7 |
| etiket | MAC'in nesneye yapistirdigi sinif | §8 |
| fail-closed | Belirsizlikte erisimi reddetme | §11 |
| fail-open | Belirsizlikte erisimi acma (riskli) | §11 |
| Security Descriptor | Windows ACL yapisi | §9 |
| Job Object | Windows surec kisitlayici | §9 |
| mask (ACL) | ACL girislerinin ust siniri | §5 |
| profil | AppArmor'un path tabanli siyaseti | §7 |
| hiyerarsi | Rol kademelenmesi (admin > editor) | §10 |
| privilege escalation | Yetki yukseltme saldirisi | §13 |

## §18 — Platform bazli yetki yuzeyi karsilastirmasi

| Ozellik | Linux | Windows | macOS |
|---|---|---|---|
| DAC birimi | inode `mode` + sahip/grup | Security Descriptor + SID |
| ACL uzantisi | POSIX ACL / NFSv4 ACL | Evet (dis/ici ACL) |
| ACL araci | `setfacl` / `getfacl` | `icacls` / `Get-Acl` |
| MAC | SELinux / AppArmor / Landlock | App Control (WDAC) |
| MAC etiketi | xattr (`security.selinux`) | imza / politika kimligi |
| Yetki ince ayari | capability | access mask / token |
| Surec kisiti | namespace / cgroup | Job Object |
| Denetim kaydi | `auditd` | Security Log |
| Sistemdeki durum | **etkin degil** | **etkin degil** | **etkin degil** |

**Tablo okumasi:** uc platformun da yetki yuzeyi **vardir**; bu repo'da
**hicbiri PHP agacindan cagirilmamistir** (2026-10-13 negatif kanit: `chmod(`,
`umask(`, `setfacl`, `icacls`, `Get-Acl` cagrisi 0). Tek istisna, izinlerin
`mkdir` argumaniyla **dolayli** verilmesidir (§4).

## §19 — Rol modeli bilesenleri (uygulama katmani)

RBAC'i olusturan dort bilesen ve bu repo'daki karsiligi:

| Bilesen | Tanim | Repo karsiligi |
|---|---|---|
| Rol | Yetki kumesinin adi | `AuthMiddleware` rol alani |
| Yetki (permission) | Tek bir eylem izni | `permissions` dizisi |
| Rol-Yetki eslemesi | Rolun hangi yetkileri icerigi | route meta `requiredPermission` |
| Kullanici-Rol atamasi | Kullaniciya rol verilmesi | oturum verisi `role` |

Dort bilesenin de **tanim yeri farklidir**: rol adlari kodda, esleme route
meta'sinda, atama oturum verisindedir. Bu daginiklik, yetki modelinin
**nereden okunacagini** belirsizlestirir -> sozlesme netligi `-surucu`
dosyasinda ele alinir.

## §20 — Denetim zinciri (kim, neyi, ne zaman)

Denetim (audit), erisim kararindan **ayri** bir katmandir:

| Soru | Kaynak | Repo durumu |
|---|---|---|
| Kim? | oturum `role` / kullanici id | oturum verisi var |
| Ne yapti? | uygulama log kaydi | vault `log.md` (append-only) |
| Ne zaman? | kayit timestamp'i | vault logu |
| Sonuc? | allow / deny | middleware `forbidden()` |
| OS seviyesi kayit? | `auditd` / Security Log | **etkin degil** |

**Onemli sinir:** isletim sistemi denetimi kapaliyken, "kim hangi dosyaya
eristi" sorusu **dosya sistemi seviyesinde yanitlanamaz**; yanit yalnizca
uygulama seviyesinde vardir. Bu, S1 acik sorusunun pratik sonucudur.

## §21 — Risk senaryolari (bu katmana ozel)

| # | Senaryo | Katman | Sonuc | S1/S2 baglantisi |
|---|---|---|---|---|
| 1 | paylasilan makinede `0777` oturum dizini | DAC | baska kullanici oturum verisi okur | S2 (izin degeri) |
| 2 | bos rol = admin varsayilani | RBAC | yetkisiz erisim | S2 (rol modeli) |
| 3 | MAC kapaliyken dosya etiketi yok | MAC | fail-open yonu | S1 (devreye alim) |
| 4 | `CAP_SYS_MODULE` verilmis hizmet | capability | keyfi modul yukleme | S1 |
| 5 | ACL mask genis birakilmis | ACL | gruptaki herkes yazar | S2 |
| 6 | setuid bakimsiz binary | DAC | yer-gecis | — |
| 7 | `chcon` ile gecici etiket | MAC | reindekslemede etiket kaybi | S1 |

**Olculebilirlik:** 1 ve 2 numarali senaryolar repo'da **gözlenebilir**
(kod kaniti §4 ve §10'da). 3, 4, 7 OS-katmani senaryolari icin kanit **yok**
(cunku o katman etkin degil) -> `UNKNOWN`.

## §22 — Bu dosyanin siniri (ne yazilmaz)

Asagidakilar **bu dosyada karara baglanmaz**; ilgili sahibine devredilir:

| Konu | Neden burada degil | Sahibi |
|---|---|---|
| `0777` -> `0750` degisikligi | kod degisikligi | backend-architect |
| Rol hiyerarisi kurulmasi | is kurali | mimari sahibi |
| LSM (SELinux/AppArmor) secimi | yeni ADR (>=088) | mimari sahibi + security |
| ACL uygulamaya gecis | altyapi karari | devops-engineer |
| OWASP denetimi | K009 kapsaminda | security-engineer |

## §23 — Bu dosyanin kanit baglantisi

| İddia | Kanit tipi | Nerede |
|---|---|---|
| dizin izni `0777` / `0750` kontrasti | disk (kod satiri) | §4 + `-ornekler.md` |
| RBAC strict karsilastirma | disk (kod satiri) | §10 + `-ornekler.md` |
| ACL/capability/LSM kullanimi yok | negatif kanit (0 sonuc) | `-dogrulama.md` D-01..D-43 |
| 8 URL HTTP 200 + envanter disi | web (HEAD) | `-dogrulama.md` §1.2 U-havuzu |
| S1 / S2 acik sorulari | acik soru | `-kapsam.md` §7 |

> **Kanit zinciri kurali:** Bu dosyadaki her iddia ya bir `Dosya:` + `Satir:`
> kod satiri, ya bir U-havuzu URL'si, ya da bir negatif olcumdur; hicbiri
> model hafizasindan alinmamistir.

## §24 — OS grupları (8 platform · 2026-10-14)

> "Kim ne yapabilir" sorusunun erisim kontrol modeli platform bazinda farkli
> mekanizmalarla kurulur; platform bazli detay ve upstream-docs kaynakli kod
> ornekleri ayni klasordeki OS kartlarindadir (her kart: §0 ELI15 · §1 OS profili ·
> §2 kod ornegi `kaynak: upstream docs` imzali, "repo'da uygulaniyor" yazilmaz ·
> §3 repo durumu = hedef referans (2026-10-14: OS araci taramasi 0 isabet / 418 PHP+JS dosya, vendor/node_modules hariç) ·
> §4 sinir cumleleri (K010/K009/komsu dal) · §5 referans · §6 acik sorular S1/S2 kapatilmaz).

| OS | Kart | Erisim kontrol modeli karsiligi | Repo durumu |
|---|---|---|---|
| Windows | [`yetki-ve-erisim-modeli-windows.md`](yetki-ve-erisim-modeli-windows.md) | ACL + SID/token · Mandatory Integrity Control | hedef referans (0 kullanim) |
| Linux | [`yetki-ve-erisim-modeli-linux.md`](yetki-ve-erisim-modeli-linux.md) | DAC + MAC (SELinux/AppArmor/Landlock) + POSIX ACL + capability | hedef referans (0 kullanim) |
| macOS | [`yetki-ve-erisim-modeli-macos.md`](yetki-ve-erisim-modeli-macos.md) | POSIX DAC + MAC (codesign/App Sandbox) + TCC | hedef referans (0 kullanim) |
| iOS | [`yetki-ve-erisim-modeli-ios.md`](yetki-ve-erisim-modeli-ios.md) | sandbox + entitlements + Keychain access groups | hedef referans (0 kullanim) |
| Android | [`yetki-ve-erisim-modeli-android.md`](yetki-ve-erisim-modeli-android.md) | UID/GID DAC + SELinux MAC + runtime permissions | hedef referans (0 kullanim) |
| Tizen | [`yetki-ve-erisim-modeli-tizen.md`](yetki-ve-erisim-modeli-tizen.md) | Smack MAC + RID (resource identifier) | hedef referans (0 kullanim) |
| webOS | [`yetki-ve-erisim-modeli-webos.md`](yetki-ve-erisim-modeli-webos.md) | ACG sandbox + ACL | hedef referans (0 kullanim) |
| ReactOS | [`yetki-ve-erisim-modeli-reactos.md`](yetki-ve-erisim-modeli-reactos.md) | NT ACL modeli (kismi, olgunslasmamis) | hedef referans (0 kullanim) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
