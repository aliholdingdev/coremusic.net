---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-android
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - https://developer.android.com/guide/topics/permissions/overview
  - https://developer.android.com/guide/topics/manifest/uses-permission-element
  - https://developer.android.com/training/permissions/requesting
  - https://source.android.com/docs/security/features/selinux
  - https://docs.kernel.org/admin-guide/LSM/SELinux.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Middleware/CsrfMiddleware.php:42
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Yetki ve Erisim Modeli — Android OS Kartı

## §0 ELI15

Bu kart, Android'de **izin (yetki) modelinin** nasıl çalıştığını anlatır: tabanda
Linux'un **DAC** modeli (uygulama başına benzersiz UID/GID + dosya sahipliği),
üstünde ise Android'in **izin sistemi** — kurulumda otomatik verilen izinler,
çalışma zamanında kullanıcı onayı istenen izinler (`dangerous`) ve özel
(`appop`) izinler. Çalışma zamanı izinlerinde **her erişimde yeniden denetim**
yapılır; kullanıcı reddederse uygulama özellikten zarifçe vazgeçer. Bu repoda
bu modelin **hiçbir karşılığı yoktur** (2026-10-14; AndroidManifest/izin kodu 0).

> **Oncelikli gercek:** Repo'da izin modeline en yakın satır
> `shared/src/Session/SessionConfig.php:20` (`DIR_PERMISSIONS = 0750`) ve
> `shared/src/Middleware/CsrfMiddleware.php:42` (`hash_equals`); ikisi de
> **uygulama katmanıdır** — Android runtime izin mekanizmasıyla ilişkileri
> **yoktur**, yalnızca kavramsal analogudur (bkz. §3).

## §1 Android güvenlik profili

**Ayrım (bağlayıcı):** Android Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
evrensel DAC/capability/ACL tanımlarını taşır; bu kart **Android'e özgü** katmanı
(izin tipleri, çalışma zamanı izinleri, izin reddi akışı) öne çıkarır. İkisi
birlikte okunur, biri diğerinin özeti değildir.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Linux DAC (UID/GID) | Her uygulama kurulumda **benzersiz Linux UID** ile çalışır; dosya sahipliği erişimi belirler (sandbox tabanı) | D2-1: izin modelinin tabanı | upstream docs · repo 0 |
| Install-time (normal) izin | Kurulumda otomatik verilir; sandığın ötesine sınırlı erişim, düşük risk | D2-1: izin tipleri | upstream docs · repo 0 |
| Install-time (signature) izin | Yalnız aynı sertifika ile imzalanmış uygulama/OS tarafından verilir (VPN, autofill gibi) | D2-1: imza bazlı yetki | upstream docs · repo 0 |
| Runtime (dangerous) izin | Android 6.0+/API 23+ **çalışma zamanında** kullanıcı istemiyle verilir; konum, mikrofon, kamera vb. | D2-2: çalışma zamanı izinleri | upstream docs · repo 0 |
| Special (appop) izin | Yalnız platform/OEM tanımlar; "diğer uygulamaların üstünde çizim" gibi güçlü işlemler | D2-1: özel yetki sınıfı | upstream docs · repo 0 |
| Permission group | Mantıksal olarak ilişkili izin grubu; sistem diyaloğu sayısını azaltır | D2-2: grup davranışı | upstream docs · repo 0 |
| İzin reddi akışı | Her işlemde yeniden kontrol → rasyonel → istek → GRANTED/DENIED → **zarifçe geri çekilme** | D2-3: reddi akışı | upstream docs · repo 0 |
| SELinux (üst katman) | DAC üstünde MAC; Android'de ihlaller `dmesg`+`logcat`'a yazılır | devir: `cekirdek-guvenlik` | upstream docs · repo 0 |
| Linux çekirdek SELinux dokümanı | SELinux'un LSM çekirdek tarafı | sınır: bu kartta yalnız referans | upstream docs (docs.kernel.org) · repo 0 |

**Okuma:** 9 satırın 8'i `upstream docs`, 0'ı bu repoda uygulanmıştır. Bu kart
**yetki modeli** kartıdır; kimlik doğrulama/oturum/MFA `K0100-identitiy` kapsamıdır
(bkz. §4).

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — Manifest izin bildirimi (referans)

```xml
# kaynak: upstream docs - developer.android.com/guide/topics/manifest/uses-permission-element
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission
    android:name="android.permission.WRITE_EXTERNAL_STORAGE"
    android:maxSdkVersion="18" />
<!-- Android 5.1 ve asagida: kurulumda verilir; Android 6.0 ve ustunde: calisma zamani istenir -->
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `AndroidManifest.xml` repoda 0 (§3 negatif kanıt) |
| Doğrulama | URL 2026-10-14 · HTTP 200 |

### §2.2 — Çalışma zamanı izin denetimi (referans)

```java
# kaynak: upstream docs - developer.android.com/training/permissions/requesting
// kaynak metin: "You must check whether you have a permission every time..."
if (ContextCompat.checkSelfPermission(context, "android.permission.CAMERA")
        == PERMISSION_GRANTED) {
    // izin verilmis: erisime devam
} else {
    // izin verilmemis/revize edilmis: graceful degrade - ozelligi kapat, kullaniciyi engelleme
}
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `checkSelfPermission` çağrısı PHP/JS ağacında 0 |
| ⚠️ Not | Sabit `PERMISSION_GRANTED`/`PERMISSION_DENIED` kaynakta okundu; **sınıflandırma (namespace) belirtilmemiştir** → ⚠️ VERIFICATION REQUIRED |

### §2.3 — İzin reddi akışı (referans)

```text
# kaynak: upstream docs - developer.android.com/training/permissions/requesting
1) manifest'te bildir  2) kullanici eylemi bekle  3) her islemde yeniden kontrol et
4) rasyonel gerekirse goster  5) izni iste (sistem diyalogu)
6) PERMISSION_GRANTED -> erisim izinli
7) PERMISSION_DENIED  -> graceful degrade (ozelligi devre disi birak; kullaniciyi bloklama)
8) "Don't assume any system behavior" - izin grubu davranisi varsayilmaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — izin reddi/allback akışı repoda yok |
| Kapsam | Yalnız **OS izin** akışı; auth/session akışı bu kartta değil (§4 K010) |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** repo genelinde OS güvenlik aracı taraması **0 isabet**
> (418 PHP+JS dosya; `vendor`/`node_modules` hariç).

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| Manifest izin bildirimi | `AndroidManifest.xml` · `<uses-permission>` | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| Çalışma zamanı izin kodu | `checkSelfPermission` · izin istek akışı | **YOK** | PHP/JS ağacı 0 · negatif kanıt |
| İzin reddi / geri çekilme akışı | izin durumu dalı | **YOK** | PHP/JS ağacı 0 · negatif kanıt |
| UID/GID sandbox (uygulama başına) | Android UID atama artefaktı | **YOK** | repo Android projesi değil · negatif kanıt |
| OS yetki aracı (setfacl/chmod/setcap) | kabuk/denetim betiği | **YOK** | 2026-10-14 tarama 0 isabet · negatif kanıt |
| session dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` → `DIR_PERMISSIONS = 0750` · **P** |
| session dizini oluşturma | PHP `mkdir` | **VAR** | `shared/src/Session/SessionConfig.php:35` → `@mkdir($savePath, 0750, true)` · **P** |
| zamanlama-güvenli token karşılaştırma | uygulama katmanı | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals` · **P** |

**Okuma:** 8 satırın 5'i `YOK` (negatif kanıt), 3'ü uygulama katmanı pozitiftir.
`0750` bir **POSIX DAC ayarıdır**; Android'in UID + runtime izin modelinin
**yerine geçmez** — bu kartta yalnız `Sınıf: P` olarak, uygulama katmanı
kanıtı sayılır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca Android dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Android platform geneli -> `isletim-sistemi-turu/android`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://developer.android.com/guide/topics/permissions/overview | İzin tipleri: install-time (normal/signature), runtime (dangerous), special (appop); korunan veri/eylem tanımı | 2026-10-14 · HTTP 200 |
| https://developer.android.com/guide/topics/manifest/uses-permission-element | `<uses-permission android:name=... android:maxSdkVersion=...>` söz dizimi; Android 5.1 altı kurulumda, 6.0+ çalışma zamanında verilir | 2026-10-14 · HTTP 200 |
| https://developer.android.com/training/permissions/requesting | 8 adımlı istek akışı; `ContextCompat.checkSelfPermission()` → `PERMISSION_GRANTED`/`PERMISSION_DENIED`; her işlemde kontrol; reddedilince graceful degrade | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/features/selinux | Uygulama sandbox'ı kurulumda benzersiz Linux UID ile oluşur (4.3 öncesi); 4.3+ SELinux sınırları ekler | 2026-10-14 · HTTP 200 |
| https://docs.kernel.org/admin-guide/LSM/SELinux.html | SELinux'un çekirdek alt sistemi ve kullanıcı alanı araçları | 2026-10-14 · HTTP 200 |

> **Dürüstlük notu:** 5 satır bu oturumda içerik olarak okunmuş ve `HEAD` ile
> **HTTP 200** döndürmüştür. Uydurma CVE / API / sürüm **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 3 pozitif satır uygulama katmanı; izin modelinin hangi katmanda devralacağı yazılı değil |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız
mimari sahibin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
