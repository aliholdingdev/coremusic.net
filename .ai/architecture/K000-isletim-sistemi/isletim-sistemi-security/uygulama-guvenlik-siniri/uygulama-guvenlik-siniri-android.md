---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-android
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - https://source.android.com/docs/security/features/selinux
  - https://source.android.com/docs/security/features/selinux/implement
  - https://developer.android.com/training/permissions/requesting
  - https://developer.android.com/tools/adb
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Uygulama Guvenlik Siniri — Android OS Kartı

## §0 ELI15

Bu kart, Android'de **bir uygulamanın diğer uygulamadan** nasıl ayrıldığını anlatır:
her uygulama kurulumda aldığı **benzersiz Linux UID** ile kendi sandbox'ında
çalışır (dosya alanı `/data/data/<paket>`), üstüne **SELinux politika katmanı**
alan/etiket bazlı ikinci bir sınır koyar ve uygulama başına ek kısıtlar
(erişilebilir servisler, etiketler, kaynak grupları) eklenir. Kritik nokta:
**DAC (UID) tek başına yeterli değildir** — Android 4.3'ten beri SELinux,
sandbox sınırlarını tamamlar. Bu repoda bu sınırların **hiçbiri yoktur**
(2026-10-14; sandbox/selinux artefaktı 0).

> **Oncelikli gercek:** Repo'da sınıra en yakın kanıtlar uygulama katmanıdır:
> `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` (`realpath`)
> ve `:200` (`str_starts_with`) — bunlar **uygulama içi yol doğrulamasıdır**,
> OS sandbox'ı **değildir** (bkz. §3, §4 K009/K010 ayrimi).

## §1 Android güvenlik profili

**Ayrım (bağlayıcı):** Android Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
evrensel sandbox/cgroup/seccomp mekanizmalarını taşır; bu kart **Android'e özgü**
uygulama sandbox'ını (UID izolasyonu + `seapp_contexts` zinciri + Android SELinux
politika katmanı) öne çıkarır. İkisi birlikte okunur, biri diğerinin özeti değildir.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| UID izolasyonu (sandbox) | Uygulama kurulumunda **benzersiz Linux UID**; her uygulama kendi sandbox'ında çalışır | D3-1: sandbox'ın tabanı | upstream docs · repo 0 |
| SELinux sandbox sınırı | Android 4.3+ SELinux, UID sandbox'ının **sınırlarını** genişletir; 5.0+ tamamı enforcing | D3-2: politika katmanı | upstream docs · repo 0 |
| Default-deny | Politikada açıkça izin verilmeyen her erişim **red** edilir | D3-2: redd varsayılanı | upstream docs · repo 0 |
| `seapp_contexts` | Uygulama süreçlerine ve `/data/data` dizinlerine etiket atar; `zygote` her açılışta, `installd` başlangıçta okur | D3-2: etiket zinciri | upstream docs · repo 0 |
| `mac_permissions.xml` | İmza + paket adından `seinfo` etiketi üretir; `seapp_contexts`'te anahtar olur (`system_server` başlangıçta okur) | D3-3: imza → etiket eşlemesi | upstream docs · repo 0 |
| `file_contexts` / `genfs_contexts` | Dosya ve etiket desteklemeyen dosya sistemlerine (`proc`, `vfat`) etiket atar; `restorecon` ile uygulanır | D3-3: dosya yüzeyi sınırı | upstream docs · repo 0 |
| Çalışma zamanı izin (uygulama → OS) | Uygulama, sandbox dışındaki veriye OS izin mekanizmasıyla erişir; her işlemde yeniden kontrol | sınır işareti: `yetki-ve-erisim-modeli` | upstream docs · repo 0 |
| cgroup v2 | Süreçleri hiyerarşik organize edip kaynak dağıtır; `nsdelegate` cgroup namespace'lerini delegasyon sınırı sayar | D3-4: kaynak kısıtı yüzeyi | upstream docs · Android kullanımı ⚠️ VERIFICATION REQUIRED |
| adb shell erişimi | Cihazda Unix kabuğu erişimi; RSA onayı olmadan çalışmaz | denetim: kartı ölçme yolu | upstream docs · repo 0 |

**Okuma:** 9 satırın 8'i `upstream docs`, 0'ı bu repoda uygulanmıştır; tek
⚠️ satır (cgroup → Android kullanımı) okunmadığı için `VERIFICATION REQUIRED`
işaretlidir ve "uygulanıyor" diye kullanılamaz.

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — UID sandbox tabanı (referans)

```text
# kaynak: upstream docs - source.android.com/docs/security/features/selinux
guvenlik modeli: "Each application runs in its own sandbox."
Android 4.3 oncesi : sandik = kurulumda benzersiz Linux UID (uygulama basina)
Android 4.3+       : SELinux sandik sinirlarini TANIMLAR (UID tek basina yeterli degil)
Android 5.0+       : her sey enforcing (60+ alan); yalniz init disinda init alani calismaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — Android UID/sandbox artefaktı repoda 0 (§3) |
| Doğrulama | URL 2026-10-14 · HTTP 200 |

### §2.2 — SELinux politika katmanı: etiket zinciri (referans)

```text
# kaynak: upstream docs - source.android.com/docs/security/features/selinux/implement
mac_permissions.xml : imza + paket -> seinfo etiketi  (system_server acilista okur)
seapp_contexts      : seinfo ANAHTAR -> uygulama sureci + /data/data etiketi
                      (zygote her uygulama acilisinda, installd acilista okur)
file_contexts       : dosya etiketleri (restorecon / restorecon_recursive ile uygulanir)
genfs_contexts      : etiket desteklemeyen dosya sistemleri (proc, vfat)
*.te                : alan (domain) ve etiket tanimi - cihaz dizini:
                      /device/manufacturer/device-name/sepolicy
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `seapp_contexts` / `*.te` / `file_contexts` glob'u 0 |
| Sınır | Bu katman **OS politikasıdır**; OWASP/auth kontrolü bu kartta değil (§4) |

### §2.3 — Uygulama başına kısıt ölçümü: adb kabuğu (referans)

```sh
# kaynak: upstream docs - developer.android.com/tools/adb
adb shell
# cihazda Unix kabugu erisimi; USB debugging + RSA onay diyalogu onaylanmadan calismaz
# cikis: ornek kabuk istemcisi ($ ya da # ), yetki seviyesi cihaz yapilandirmasina bagli
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repo'da `adb` çağrısı/CI adımı yok |
| ⚠️ Not | Belirli bir `adb shell` kısıt komutu (ör. süreç/UID listeleme) bu oturumda **okunmadı** → ⚠️ VERIFICATION REQUIRED; blok yalnız erişim kapısını gösterir |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** repo genelinde OS güvenlik aracı taraması **0 isabet**
> (418 PHP+JS dosya; `vendor`/`node_modules` hariç).

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| Uygulama sandbox (UID) | Android UID atama / paket artefaktı | **YOK** | repo Android projesi değil · negatif kanıt (2026-10-14) |
| SELinux politika dosyaları | `*.te` · `seapp_contexts` · `file_contexts` | **YOK** | glob 0 · negatif kanıt |
| `mac_permissions.xml` / imza etiketi | XML politika | **YOK** | glob 0 · negatif kanıt |
| cgroup kısıt betiği | kabuk betiği / systemd benzeri birim | **YOK** | 2026-10-14 tarama 0 isabet · negatif kanıt |
| adb / cihaz ölçüm adımı | `adb` çağrısı (CI dahil) | **YOK** | `.github/workflows/` içinde `adb` yok · negatif kanıt |
| yol doğrulaması (uygulama katmanı) | realpath + sınır kontrolü | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `realpath` · **P** |
| yol sınırı kontrolü (uygulama katmanı) | string başlangıç denetimi | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:200` → `str_starts_with` · **P** |
| dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` · `:35` → `0750` · **P** |

**Okuma:** 8 satırın 5'i `YOK` (negatif kanıt), 3'ü uygulama katmanı pozitiftir.
`realpath` + `str_starts_with` **uygulama içi** yol denetimidir; OS sandbox'ının
**yerine geçmez** ve bu kartta onay kapısı olarak kullanılamaz.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Android dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Android platform geneli -> `isletim-sistemi-turu/android`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://source.android.com/docs/security/features/selinux | Her uygulama kendi sandbox'ında çalışır; 4.3 öncesi benzersiz Linux UID; 4.3+ SELinux sınırları ekler; 5.0+ tam enforcing | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/features/selinux/implement | `seapp_contexts`/`mac_permissions.xml`/`file_contexts`/`genfs_contexts` sorumlulukları ve okuyucu süreçleri; `restorecon` | 2026-10-14 · HTTP 200 |
| https://developer.android.com/training/permissions/requesting | "Every Android app runs in a limited-access sandbox"; sandbox dışı erişim için runtime izin gerekir | 2026-10-14 · HTTP 200 |
| https://developer.android.com/tools/adb | adb Unix kabuğu erişimi sunar; USB debugging + RSA onay mekanizması | 2026-10-14 · HTTP 200 |
| https://docs.kernel.org/admin-guide/cgroup-v2.html | cgroup v2 süreçleri hiyerarşik organize eder, kaynak dağıtır; `nsdelegate` delegasyon sınırıdır | 2026-10-14 · HTTP 200 |

> **Dürüstlük notu:** 5 satır bu oturumda içerik olarak okunmuş ve `HEAD` ile
> **HTTP 200** döndürmüştür. cgroup'un **Android'e özgü** kullanımına dair
> iddia okunmadığı için §1'de `⚠️ VERIFICATION REQUIRED` işaretlidir; uydurma
> CVE / API / sürüm **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 3 pozitif satır uygulama katmanı; sandbox sorumluluğunun uygulamaya mı OS'e mi ait olduğu yazılı değil |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız
mimari sahibin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
