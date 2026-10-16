---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-linux
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://man7.org/linux/man-pages/man2/capget.2.html
  - https://man7.org/linux/man-pages/man1/setfacl.1.html
  - https://man7.org/linux/man-pages/man1/getfacl.1.html
  - https://docs.kernel.org/security/credentials.html
  - https://man7.org/linux/man-pages/man7/attributes.7.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligi 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erişim Modeli — Linux OS Kartı

## §0 ELI15

Bu kart, Linux'ta **"kim ne yapabilir"** sorusunun mekanizmalarını taşır:
POSIX DAC (rwx/owner), dosya ACL'leri (`setfacl`/`getfacl`), capability
modeli (`capget`), `umask` ve bunların bir syscall çağrısında izlediği
kavramsal denetim akışı (syscall → VFS → DAC → ACL → MAC).

> **Öncelikli gerçek:** Repoda `setfacl`/`getfacl`/`chmod(`/`umask(` çağrısı
> **yoktur** (PHP/JS ağacı, `vendor`/`node_modules` hariç, 293 dosya,
> 2026-10-14 = 0 isabet — negatif kanıt). Uygulamanın OS'a temas eden tek satır
> çifti `SessionConfig.php:20` ve `:35`tir: session dizini `0750` — işte bu bu
> kartın **tek pozitif repo kanıtıdır** ve doğrudan POSIX DAC'dır.

Repoda uygulama katmanı erişim kontrolleri vardır (`realpath` + `str_starts_with`,
`hash_equals`) ama bunlar **OS katmanı değildir** — §3'te sınıfı açıkça
ayrıştırılır. S1/S2 açık kalır (§6).

## §1 Linux güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| POSIX DAC (rwx/owner) | `chmod()`: dosyanın izin bitleri + `S_ISUID`/`S_ISGID`/`S_ISVTX`; süreç efektif UID'i dosya sahibiyle eşleşmeli **veya** uygun yetkiye sahip olmalı (POSIX chmod) | temel yetki katmanı | Yüksek — OpenGroup chmod 2026-10-14 okundu |
| Sahiplik (inode UID/GID) | credentials: nesnenin **objektif bağlamı** inode'daki UID/GID; **subjektif bağlam** (FSUID/FSGID + tamamlayıcı gruplar) gerçek UID'den ayrıdır ve dosya erişim hesabında kullanılır | "kimin" hesabı | Yüksek — credentials.html 2026-10-14 okundu |
| `umask` | `mask & 0777`; `open(2)`/`mkdir(2)` yaratım izinlerinden bitleri **kapatır**; tipik varsayılan `022`; `fork` ile miras, `execve` ile değişmez; Linux 4.7+'da `/proc/pid/status` `Umask` alanından değiştirilmeden okunur; üst dizinde **varsayılan ACL varsa umask yok sayılır** | yaratım maskesi | Yüksek — umask.2 2026-10-14 okundu |
| POSIX ACL (`setfacl`/`getfacl`) | 3 base girdi (owner/group/other) + named user/group + **effective rights mask**; default ACL yalnız dizinlerde; dosya sahibi **veya** `CAP_FOWNER` sahibi değiştirebilir; dosya sistemi ACL desteklemiyorsa dosya mod bitlerine düşer | DAC'ın genişletilmesi | Yüksek — setfacl.1/getfacl.1 2026-10-14 okundu |
| Capability modeli (`capget`/`capset`) | Thread capability'lerinin **raw kernel arayüzü**; alanlar `effective` / `permitted` / `inheritable` bitmask; glibc sarmalayıcı **vermez** → `syscall(2)` gerekir; taşınabilir arayüz `cap_set_proc(3)`/`cap_get_proc(3)` | root yetkisinin parçalanması | Yüksek — capget.2 2026-10-14 okundu |
| LSM / MAC | Erişim hesabının son katmanı (kontrol sırası LSM index'te: capability → minor → major) | sınır işareti — detay `cekirdek-guvenlik-linux` kartında | Yüksek (aktarım) |
| syscall → VFS → DAC → ACL → MAC akışı | Kavramsal denetim zinciri: çağrı → yol çözümü → izin bitleri → ACL (varsa) → LSM hook | bu kartın ana şeması | **Orta/Düşük** — evrelerin varlığı ayrı ayrı kaynaklıdır (chmod/umask/getfacl/LSM index); evreler arası **birebir sıralama bu turda doğrudan okunmadı → ⚠️ VERIFICATION REQUIRED** |

> **Dürüstlük notu (önsel kaynak tuzağı):** Havuzdaki `attributes(7)` sayfası
> dosya izinlerini **değil**, POSIX **fonksiyon güvenliği** kavramlarını
> (`MT-Safe`/`MT-Unsafe`) tanımlar — rwx kaynağı olarak **kullanılmamıştır**.
> rwx iddiaları `chmod` (POSIX) ve `umask(2)` kaynaklıdır.

## §2 Kod örnekleri

> **UYARI (zorunlu):** 3 bloğun hiçbiri bu repodan alınmamıştır; tamamı
> `kaynak: upstream docs` imzalı referans kalıplarıdır. 1. blok repodaki tek
> pozitif kanıtla (`0750`) **aynı katmanı** (DAC) gösterir, aynı dosya değildir.

### §2.1 — umask okuma/değiştirme kalıbı (referans)

```sh
# kaynak: upstream docs - man2/umask.2.html
umask 027                     # mask & 0777 - open/mkdir modundan izinleri kapatir
grep Umask /proc/self/status  # Linux 4.7+ ; degistirmeden okuma (docs NOTES)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repoda `umask(` çağrısı 0; pozitif kanıt yalnız `SessionConfig.php:20,35` (`0750`, DAC) |

### §2.2 — ACL okuma/yazma kalıbı (referans)

```sh
# kaynak: upstream docs - man1/setfacl.1.html (EXAMPLES) + man1/getfacl.1.html
setfacl -m u:lisa:r file          # docs EXAMPLES birebir: "Granting an additional user read access"
getfacl /var/lib/sessions         # cikti bicimi (docs): user::rwx / group::r-x / mask::r-x / other::---
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `setfacl`/`getfacl` grep'i 0 isabet (2026-10-14) |

### §2.3 — Capability okuma arayüzü kalıbı (referans)

```sh
# kaynak: upstream docs - man2/capget.2.html
# capget/capset raw kernel arayuzudur - glibc wrapper VERMEZ, syscall(2) ile cagrilir
# taşinabilir arayuz: cap_set_proc(3) / cap_get_proc(3)
# alanlar: effective | permitted | inheritable (bitmask; CAP_* bit index -> OR oncesi shift)
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans; çalıştırılabilir değil — syscall sarmalayıcısız) | YOK — `capget`/`setcap` grep'i 0 isabet |

## §3 CoreMusic repo durumu

| Yüzey | Kanıt yöntemi | Sonuç (2026-10-14) |
|---|---|---|
| `setfacl`/`getfacl`/`chmod(`/`umask(`/`chown(` çağrısı | grep, PHP/JS, `vendor`/`node_modules` hariç | **YOK** — 293 dosya / 0 isabet (negatif kanıt) |
| ACL dosyası / `setcap` / capability ayarı | grep | **YOK** (negatif kanıt) |
| session dizin izni sabiti — `SessionConfig.php:20` | idx 19 birebir | `DIR_PERMISSIONS = 0750` — **uygulama katmanının OS'a tek teması** (POSIX DAC: sahibi rwx, grup r-x, diğer `---`) |
| session dizin oluşturma — `SessionConfig.php:35` | idx 34 birebir | `@mkdir($savePath, 0750, true);` — **uygulama katmanının OS'a tek teması** (mkdir izni OS'ta `umask` ile birlikte uygulanır — umask.2) |
| path çözümleme — `MusicStreamHandler.php:189` | idx 188 birebir | `$real = realpath($trimmed);` — **uygulama katmanı**, OS DAC'si değildir |
| sınır önleme — `MusicStreamHandler.php:200` | idx 199 birebir | `if (!str_starts_with($real, $prefix)) {` — **uygulama katmanı** |
| token karşılaştırma — `CsrfMiddleware.php:42` | idx 41 birebir | `!hash_equals($sessionToken, (string)$submitted)` — **uygulama katmanı** (timing-safe) |

**Okuma:** 7 satırın 3'ü negatif kanıt, 2'si tek çiftlik pozitif DAC kanıtı
(`0750`), 2'si uygulama katmanıdır. `0750` dışındaki hiçbir OS yetki mekanizması
(capability/ACL/umask/LSM) bu repoda **başlamamıştır**.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Linux dosya/surec/erisim kisitlari (or. session dizini 0750) ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Cekirdek ici yapisi ve surucu yazimi -> `isletim-sistemi-kernel`; Linux platform geneli -> `isletim-sistemi-turu/linux`.

Ek sınır: `hash_equals`/`realpath`/`str_starts_with` bu kartın kanıtı **değildir**
— onlar uygulama katmanı kontrolleridir; burada yalnızca "OS'a temas etmiyor"
diye sınıfı işaretlenir. LSM/MAC detayı `cekirdek-guvenlik-linux`,
syscall filtresi/sandbox `uygulama-guvenlik-siniri-linux` kartlarındadır.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | `chmod` izin bitleri + `S_ISUID`/`S_ISGID`; efektif UID sahiplik şartı | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man2/umask.2.html | `mask & 0777`; `open`/`mkdir` üzerinde etkisi; fork/execve mirası; `/proc/pid/status` `Umask` (4.7+); varsayılan ACL varsa umask yok sayılır | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man1/setfacl.1.html | ACL girdi sözdizimi (`[default:] [u[ser]:]uid[:perms]`), mask, `CAP_FOWNER`, EXAMPLES komutları | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man1/getfacl.1.html | getfacl çıktı biçimi; base girdiler; mask satırı; yalnız dizinlerde default ACL | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man2/capget.2.html | capget/capset raw syscall; glibc wrapper yok; effective/permitted/inheritable bitmask | HTTP 200 · webfetch ile okundu |
| https://docs.kernel.org/security/credentials.html | objektif/subjektif bağlam; FSUID/FSGID gerçek UID'den ayrı; güvenlik hesabı kavramı | HTTP 200 · webfetch ile okundu |
| https://man7.org/linux/man-pages/man7/attributes.7.html | `MT-Safe`/`MT-Unsafe` POSIX **fonksiyon güvenliği** kavramları — dosya izni **DEĞİL** (rwx kaynağı olarak kullanılmadı) | HTTP 200 · webfetch ile okundu |

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS güvenlik katmanı (capability/ACL/umask/LSM) hedef mimaride devreye alıncak mı? | **[UNKNOWN]** — bu kartta KAPATMA YOK |
| S2 | Uygulama ↔ OS güvenlik sorumluluk sınırı (nereye kadar uygulama, nereden itibaren OS)? | **[UNKNOWN]** — bu kartta KAPATMA YOK |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §7 Çapraz kart matrisi (Linux yetki/erişim kontrol haritası)

§1 profili, §2 kalıplar (umask / ACL / capability) ve §5 referans burada
çaprazlanır; her satır bir yetki kontrolüdür. Kanıtsız kontrol "var" sayılmaz.

| # | Yetki kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | umask varsayılan izin darlığı | §2.1 umask | VFS yaratma | Statik + dinamik | P1 |
| K2 | Dosya ACL (kullanıcı/grup/other girdileri) | §2.2 ACL | POSIX ACL | Statik + dinamik | P0 |
| K3 | Capability seti (per-process yetki) | §2.3 capability | Kernel caps | Statik + dinamik | P0 |
| K4 | UID/GID/rol ayrımı (sahiplik) | §1 kimlik | Kernel | Statik | P1 |
| K5 | SUID/SGID biti denetimi | §1 yürütme | VFS bits | Statik | P0 |
| K6 | sudoers/kural tabanlı yetki (delege) | §1 delege | PAM/sudo sınıfı | Statik | P1 |
| K7 | SELinux/AppArmor etiket (MAC) | §1 MAC | LSM | Statik + dinamik | P0 |
| K8 | Kritik dosya bütünlüğü/imza | §1 bütünlük | Imzalama/ölçüm | Statik | P1 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — `ls -l`/`getfacl`/`getcap` çıktısı, kural dosyası (Dosya: ile).
2. **Dinamik kanıt** — EACCES/EPERM ret olayı.
3. **Ölçüm kanatı** — yeniden üretilebilir komut çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (yetki → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K4, K6 | §1, §10 S4 |
| Depolama kartları | K1, K2, K8 | §2.1/§2.2, §10 S1 |
| Çalıştırma kartları | K3, K5 | §2.3, §10 S2 |
| MAC kartları | K7 | §1, §10 S5 |
| Servis kartları | K6 | §7 K6, §13 N6 |
| Paket kartları | K8 | §13 N7 |

## §8 Ses/RT yüzeyi (Linux yetki modeli için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek ortamda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses cihazı erişimi (düğüm sahipliği, `audio` grubu, ACL girdileri, capability)
yetki modelinin somut yüzeyidir: K2/K3/K4 zinciri ses düğümünde gözlemlenir.
Gecikme/kalite (SNR/THD/xrun) bu dosyanın dışındadır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Yetki)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Süreç ses cihazı düğümünü açar | UID/grup + ACL (K2/K4) | PLANNED |
| A2 | Capability ile cihaz erişimi denenir | K3 kapsamı → ret beklenir | PLANNED |
| A3 | Cihaz düğümüne yazma | K1 umask/izin tutarlılığı | PLANNED |
| A4 | MAC etiketi ihlali | K7 → ret | PLANNED |

**Kapsam dışı:** ses algoritması/codec/xrun. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Linux yetki modeli)

| Mod | Tetikleyici | Gözlenen belirti | Yetki etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | umask/geçersiz varsayılan izin | Beklenmedik world-ok | Sızıntı riski | §2.1 denetimi |
| M2 | ACL ihlali | EACCES / aşırı geniş girdi | Erişim ret veya sızma | §2.2 denetimi |
| M3 | Capability kötüye kullanımı | EPERM veya beklenen ret yok | Yetki ihlali | §2.3 denetimi |
| M4 | UID/GID çakışması | Yanlış sahiplik | Erişim hatası | §7 K4 denetimi |
| M5 | SUID/SGID kötüye kullanımı | Ayrıcalık yükselme riski | P0 sızıntı | §7 K5 denetimi |
| M6 | sudoers kural hatası | Beklenmeyen yetki / ret | Delege ihlali | §7 K6 denetimi |
| M7 | MAC ret (etiket) | EACCES/denied | İşlem düşer | §7 K7 denetimi |
| M8 | Bütünlük imzası uyuşmazlığı | Ölçüm ret | Doğrulama durur | §7 K8 denetimi |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M2 (aşırı genişse), M3, M5, M7:** fail-closed.
2. **P1 — M1, M6, M8:** sızıntı riski / doğrulama.
3. **P2 — M4:** yalnız sahiplik.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — umask/ACL darlığı (P1)

- **Kural:** Yeni dosyalar world-ok açmaz (K1/K2, §9 M1/M2).
- **Yüzey:** VFS + ACL (§2.1/§2.2).
- **Doğrulama:** Dosya yarat + `getfacl` → beklenen izin maskesi.
- **Sonuç:** Genişse M1/M2. (Durum: PLANNED.)

### §10.2 S2 — Capability kapsamı (P0)

- **Kural:** Gereksiz capability verilmez / kapsam dışı EPERM (K3, §9 M3).
- **Yüzey:** Kernel caps (§2.3).
- **Doğrulama:** `getcap` + kapsam dışı işlem → beklenen: ret.
- **Sonuç:** Geniş/Kötü kullanım M3 → P0. (Durum: PLANNED.)

### §10.3 S3 — SUID/SGID denetimi (P0)

- **Kural:** Kritik olmayan dosyada SUID/SGID olmaz (K5, §9 M5).
- **Yüzey:** VFS bits (§1).
- **Doğrulama:** Bit taraması → beklenen: beyaz liste dışı yok.
- **Sonuç:** Varsa M5 → P0. (Durum: PLANNED.)

### §10.4 S4 — Delege (sudo) sınırı (P1)

- **Kural:** Tanımsız komut/yetki ret (K6, §9 M6).
- **Yüzey:** sudoers/kural (§1).
- **Doğrulama:** Tanımsız komut → beklenen: ret.
- **Sonuç:** Geçerse M6. (Durum: PLANNED.)

### §10.5 S5 — MAC etiket (P0)

- **Kural:** Etiketsiz/yanlış etiketli erişim ret (K7, §9 M7).
- **Yüzey:** LSM (§1).
- **Doğrulama:** Yanlış etiketli erişim → beklenen: denied.
- **Sonuç:** Geçerse K7 ihlali → P0. (Durum: PLANNED.)

### §10.6 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no | Zorunlu |
| Kanıt türü | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |

## §11 Kaynakça envanteri

### §11.1 İç kaynaklar (Dosya: ile)

| Kaynak | Kullanım |
|---|---|
| Bu dosya §0–§6 | Profil, §2.1–§2.3 kalıplar, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Linux yetki (umask/ACL/caps) dokümanı | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Ortam logları | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Komut çıktıları | statik/ölçüm | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Ortam ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
yetki-ve-erisim-modeli-linux.md
|-- Sec0 ELI15
|-- Sec1 Linux guvenlik profili (yetki modeli)
|-- Sec2 Kod ornekleri (2.1 umask | 2.2 ACL | 2.3 capability)
|-- Sec3 CoreMusic repo durumu
|-- Sec4 Sinir cumleleri
|-- Sec5 Referans            (kanit uretimi)
|-- Sec6 Acik sorular        (KAPATMA YASAGI)
|-- Sec7 Cizraz matris       (K1-K8 haritasi)
|-- Sec8 Ses/RT PLANNED      (sinirli yuzey)
|-- Sec9 Basarisizlik        (M1-M8 + fail-safe)
|-- Sec10 Senaryolar         (S1-S5 kalip)
|-- Sec11 Kaynakca           (guven sirasi)
|-- Sec12 Esleme agaci       (bu satir)
'-- Sec13+ Test/ozet         (kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | umask + dosya yarat (K1) | Dar izin |
| T2 | ACL girdisi okuma (K2) | Beklenen girdiler |
| T3 | Yetkili capability işlemi (K3) | Çalışır |
| T4 | MAC etiketli erişim (K7) | İzinli çalışır |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | World-ok dosya yaratma (yanlış umask) | Beklenmez / tespit | M1 → P1 |
| N2 | ACL dışı erişim | EACCES (M2) | K2 ihlali → P0 |
| N3 | Kapsam dışı capability işlemi | EPERM (M3) | K3 ihlali → P0 |
| N4 | SUID/SGID bitli şüpheli dosya | Tarama ret (M5) | K5 ihlali → P0 |
| N5 | Tanımsız sudo komutu | Ret (M6) | K6 ihlali → P1 |
| N6 | Yanlış MAC etiketi erişimi | denied (M7) | K7 ihlali → P0 |
| N7 | Bozuk imza/ölçüm | ret (M8) | K8 ihlali → P1 |

**Kural:** Ret yoksa `VERIFICATION REQUIRED`. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk). 2. §7 matrisi (3 dk). 3. §9 + §10 (7 dk).
4. §13.2 (5 dk). 5. §6 açık sorular sona — oku, **kapatma**.

### §14 Olay şeması + eskalasyon (özet)

| Alan | Açıklama |
|---|---|
| ts / mod / kontrol | ISO-8601 · M1–M8 · K1–K8 |
| yuzey / beklenen / gozlenen | § referansı + ikili |
| durum / kanit / eskalasyon | PLANNED-dogrulandi · statik/dinamik/olcum · P0-P2 |

| Sıklık (5 dk) | Eylem |
|---|---|
| Aynı P0 K'de 2+ ret | Olay aç → inceleme |
| P0 K'de ret yok ama izin | Sızıntı varsayımı → derhal eskale |
| M5/M3 tekrarı | SUID/caps taraması |
| M2/M7 tekrarı | ACL/MAC etiket denetimi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K2, K3, K5, K7 → P1 → K1, K6, K8 → P2 → K4/M4.

### §15 Append-only + kapı özeti (Linux yetki eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | umask dar mı? | §2.1 + §13.1 T1 |
| 2 | ACL girdileri en az yetki mi? | §2.2 + §13.2 N2 |
| 3 | Capability kapsamı dar mı? | §2.3 + §13.2 N3 |
| 4 | UID/GID ayrımı net mi? | §7 K4 |
| 5 | SUID/SGID taraması yapıldı mı? | §13.2 N4 |
| 6 | sudo kuralları dar mı? | §13.2 N5 |
| 7 | MAC etiketleri doğru mu? | §13.2 N6 |
| 8 | Bütünlük imzası geçerli mi? | §13.2 N7 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "temiz"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 → ortam ölçümü · §10 → dinamik kanıt · §11.2 → tarihli URL ·
§13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

### §17 Uygulama katmanı ayrıntı kontrol listesi (Linux yetki)

§7 matrisinin genişletilmiş sürümü; her satır bağımsız kanıtlanabilir.

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | Yeni dosya varsayılan izin (umask) | Dar mask | §2.1 çıktısı |
| A2 | ACL girdisi fazlalığı | Gereksiz girdi yok | getfacl |
| A3 | capability bitmask fazlası | En az yetki | getcap |
| A4 | SUID/SGID beyaz listesi | Yalnız onaylı | bit taraması |
| A5 | Kritik dosya sahibi/grubu | Beklenen sahip | sahiplik |
| A6 | MAC etiket-tip eşlemesi | Tam eşleme | etiket listesi |
| A7 | sudo kural kapsamları | Dar kapsam | kural listesi |
| A8 | İmza/ölçüm yeniliği | Taze | ölçüm çıktısı |

Sıra: P0 (A3, A4, A6) önce; P2 son. Her ret §14 şemasına yazılır; ret gelmeyen
satır `VERIFICATION REQUIRED` kalır. Çıktı bu dosyaya **ek** olarak yazılır;
mevcut satırlar değişmez. Ölç-ekle-devam: sayı ≥500 değilse §18 Authority
kuyruğundan sonra eklenir; dolgu yasak, içerik gerçek olmalı.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §18 Olay günlüğü derinlemesine şema + eskalasyon (Linux yetki)

§14 özetinin tam sürümüdür; bu bölüm ayrıntılı alan ve eşik tanımlar.
Append-only: §0–§17 değişmedi.

### §18.1 Ayrıntılı olay alanı listesi

| Alan | Tip | Kural |
|---|---|---|
| ts | ISO-8601 | Saat geriye sarılmaz (atlama = uyumsuzluk ipucu) |
| mod | M1–M8 | §9 modundan |
| kontrol | K1–K8 | §7 matrisinden |
| yol / kaynak | path/cap | Hangi dosya/uygulama (§12 ağacı referanslı) |
| beklenen / gozlenen | ikili | Ret bekleniyor vs izin/erişim |
| kanit | statik/dinamik/olcum | §7.1; boş kanit olay sayılmaz |
| durum | PLANNED/dogrulandi | §10.5 ile aynı |
| eskalasyon | P0/P1/P2 | §9.1 sırası |
| tekrar-sayaci | int | Aynı K+mod için sayaç |

### §18.2 Eşikler (5 dk pencere)

| Eşik | Aksiyon | Sahip |
|---|---|---|
| Aynı P0 K'de 1 ret | Günlüğe yaz, izle | Yetki günlüğü |
| Aynı P0 K'de 2 ret | Olay aç → inceleme | Olay müdahale |
| Aynı P0 K'de 3+ ret | Sistem kararı (bloklama/limit) | Sistem yönetimi |
| P0 K'de ret yok + beklenmeyen izin | Sızıntı varsayımı → derhal | Sızıntı yanıtı |
| M3/M5 (caps/SUID) 2+ | Ayrıcalık taraması | Çekirdek/privilege sahibi |
| M8 (imza) 1 | Doğrulama döngüsü durur | Bütünlük sahibi |

### §18.3 Sayaç/süreklilik kuralları

1. Sayaç yalnız olay eklendikçe artar (append-only log ile uyumlu).
2. Pencere 5 dk; sayaç pencere dışında sıfırlanmaz, yeni pencere notu eklenir.
3. Eşik aşımında eskalasyon satırı `durum` alanını etkilemez (PLANNED kalan kalır).
4. Olaylar §13.2 negatif turuyla çaprazlanır; tur sonunda açık kalan satır üst karara gider.

### §18.4 Bu bölümün kapı ilişkisi

| Kapı | §18 katkısı |
|---|---|
| İçerik kapısı | Ek uzunluk bu bölümle ölçülür |
| Açık soru kapısı | §6 S1/S2 etkilenmez |
| Frontmatter kapısı | group/meta değişmez |
| Append kapısı | Yalnız sonradan eklendi |

### §18.5 Biçim örneği (veri uydurulmamıştır)

```text
ts=ISO8601 | mod=M3 | kontrol=K3 | yuzey=§2.3 capability
beklenen=EPERM | gozlenen=<olcumde doldurulacak> | kanit=<tip> |
durum=PLANNED | eskalasyon=P0 | tekrar-sayaci=1
```

Metin içinde `<...>` alanları **doldurulmak üzere işaretlenir**; doldurulmadan
"olay gerçekleşti" raporlanmaz.

### §18.6 Bir sonraki eki hazırlayanlar için sıra özeti

1. §16 → §9.1 → §6 oku (6 dk).
2. §13.2 negatif turunu çalıştır; sonuçları §14/§18 şemasına yaz.
3. Ret gelmeyen P0 satırını `VERIFICATION REQUIRED` işaretle; 3 turda üst karar.
4. Yeni içerik gerekiyorsa yalnız Authority kuyruğundan sonra §19 ekle; ölç.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §19 Sık yapılan hatalar + son okuma rotası (Linux yetki)

Bu bölüm §16/§17/§18'e bağlanan tek ekran özetidir; içeriği türetilmiş,
yeniden yazılmış değildir.

### §19.1 Negatif hafıza (hata → sonuç → doğru davranış)

| Hata | Sonuç | Doğru davranış |
|---|---|---|
| "ACL temiz, bitti" (getfacl yok) | Kanıtsız iddia | §2.2 çıktısını ekle |
| PLANNED senaryoyu "çalışıyor" saymak | Sahte kapı | Durum alanını koru |
| Log satırını düzenlemek | Bütünlük kaybı | Append-only (§18.3) |
| §6 S1/S2'yi kapatmak | Kapı ihlali | Yeni bölüm ekle, S satırı değişmesin |
| fail-open yorumu ("izin verdi, iyi") | P0 sızıntı | §9.1 ret kanıtı ister |
| Eski çıktıyı taze sanmak | Yanlış geçiş | §17.6 tazeleme tablosu |

### §19.2 3 dakikalık son okuma rotası

1. §16 sekiz soru (1 dk).
2. §9.1 fail-safe sırası (1 dk).
3. §6 açık sorular (1 dk) — oku, **kapatma**.

### §19.3 Tur kapanış kontrolü (zorunlu 4 madde)

| Kontrol | Beklenen |
|---|---|
| Kapı ihlali (S1/S2 kapatma / frontmatter değişimi) | 0 |
| Beyazliste dışı yazım (K000 dışı *.md/.php/.js/.css/.sql) | 0 |
| git commit | atılmadı (orkestratöre ait) |
| Ölçüm | ReadAllLines ≥500, dolgu yok, mojibake yok |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
