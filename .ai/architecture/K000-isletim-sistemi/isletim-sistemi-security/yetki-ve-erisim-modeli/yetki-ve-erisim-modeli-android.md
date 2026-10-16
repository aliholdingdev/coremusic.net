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
**yetki modeli** kartıdır; kimlik doğrulama/oturum/MFA `K010-identitiy` kapsamıdır
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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Android dosya/surec/erisim kisitlari ele alinir.

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

---

## §7 Çapraz kart matrisi (Android yetki/erişim kontrol haritası)

§1 yetki profili, §2 izin kalıpları (manifest / runtime / ret akışı) ve §5
referans burada çaprazlanır; her satır bir yetki kontrolüdür. Kanıtsız kontrol "var" sayılmaz.

| # | Yetki kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Manifest izin beyanı (install-time) | §2.1 manifest | PackageManager | Statik | P0 |
| K2 | Çalışma zamanı izin denetimi (request/grant) | §2.2 runtime | PermissionController | Dinamik | P0 |
| K3 | İzin reddi akışı (onRequestPermissionsResult) | §2.3 ret akışı | Uygulama UI + sistem | Dinamik | P1 |
| K4 | İzin grupları/sınır (dangerous vs normal) | §1 izin modeli | Permission split | Statik | P1 |
| K5 | UID/rol bazlı dosya erişimi (DAC/MAC örtüşme) | §1 depolama | DAC + SELinux | Statik + dinamik | P0 |
| K6 | İzin geri çekilmesi / revoke | §1 yaşam döngüsü | PermissionController | Dinamik | P1 |
| K7 | Signature-level / özel izin koruması | §1 paket | PackageManager | Statik | P0 |
| K8 | Rol atanması (role-based erişim) | §1 rol katmanı | RoleManager | Dinamik | P2 |

### §7.1 Matris okuma kuralı

1. **Statik kanıt** — manifest satırı, izin tanımı (Dosya: ile).
2. **Dinamik kanıt** — grant/revoke/red olayı (log/bağlam geri çağrısı).
3. **Ölçüm kanatı** — `adb` benzeri yeniden üretilebilir çıktı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır.

### §7.2 Çapraz kart eşlemesi (yetki → kart)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| İzin kartları | K1, K2, K3, K4 | §2.1–§2.3, §10 S1/S2 |
| Depolama kartları | K5 | §1, §10 S4 |
| Paket kartları | K7 | §1, §13 N5 |
| Yaşam döngüsü kartları | K6 | §10 S3 |
| Rol kartları | K8 | §7 K8, §13 N7 |
| Bağlantı kartları | K4 (ağ izni sınıfı) | §10 S5 |

## §8 Ses/RT yüzeyi (Android yetki modeli için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir gerçek cihazda bu turda doğrulanmadı.

### §8.1 Neden bu bölüm burada?

Ses erişimi (kayıt/oynatma izinleri, ses oturumu kimliği) yetki modelinin
gözlemlenebilir yüzeyidir: RECORD_AUDIO gibi izinler K1/K2/K3 zincirinden
geçer, cihaz erişimi UID ile bağlanır. Ses algoritması/kalite bu dosyanın dışıdır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Yetki)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses kaydı izni ister | Runtime izni (K2) | PLANNED |
| A2 | İzin reddedilir | Ret akışı (K3) → özellik kapalı | PLANNED |
| A3 | Veri/önbellek yazılır | UID/rol erişimi (K5) | PLANNED |
| A4 | İzin revoke edilir | Kapture: süreç/durdurma davranışı (K6) | PLANNED |

**Kapsam dışı:** ses algoritması/codec/DSP. **Harici referans:** `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma** (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Android yetki modeli)

| Mod | Tetikleyici | Gözlenen belirti | Yetki etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | Manifest beyanı eksik/yanlış | Kurulumda/deploy'da uyarı | İzin hiç gelmez | Manifest revizyonu |
| M2 | Runtime izni reddi | Grant reddi / ret | Özellik kapalı | §2.3 akışı |
| M3 | Ret akışının yanlış işlenmesi | Bozuk/eksik UI davranışı | UX + veri kaybı | Akış denetimi |
| M4 | Dosya erişim ihlali (UID/MAC) | EACCES/denied | Erişim kesilir | §7 K5 denetimi |
| M5 | İzin geri çekme sonrası davranış | Uygulama çökmesi/kaçak | Çalışma hatası | K6 canlanma yolu |
| M6 | İzin grubu yanlış sınıflandırma | Beklenmeyen geniş erişim | Sınır genişler | §7 K4 denetimi |
| M7 | Signature iznin sızması | İzinli çağrı | Paket/sınır ihlali | §7 K7 denetimi |
| M8 | Rol çakışması (role assignment) | Beklenmeyen erişim | Rol ihlali | RoleManager denetimi |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M4, M7:** fail-closed (izin/erişim yoksa özellik çalışmaz).
2. **P1 — M2, M3, M6:** tek özellik/akış.
3. **P2 — M5, M8:** yalnız yaşam döngüsü/rol.

`fail-open` hiçbir modda kabul edilmez. 3 başarısız turda dur + üst karar.

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama → sonuç (işaretli)**.

### §10.1 S1 — Manifest ↔ runtime tutarlılığı (P0)

- **Kural:** Runtime istenen izin manifest'te beyan edilmelidir (K1+K2).
- **Yüzey:** Manifest + PermissionController (§2.1/§2.2).
- **Doğrulama:** İzin isteme → beklenen: beyan edilmiş izin akışı.
- **Sonuç:** Tutarlı değilse M1. (Durum: PLANNED.)

### §10.2 S2 — İzin reddi akışı (P1)

- **Kural:** Ret, uygulamayı güvenli moda düşürür (K3, §9 M3).
- **Yüzey:** Ret akışı (§2.3).
- **Doğrulama:** Ret senaryosu → beklenen: özellik kapalı, çökme yok.
- **Sonuç:** Çökme/kaçak varsa M3. (Durum: PLANNED.)

### §10.3 S3 — İzin geri çekme (P1)

- **Kural:** Revoke sonrası çağrılar reddedilir, uygulama kurtarılır (K6, §9 M5).
- **Yüzey:** PermissionController (§1).
- **Doğrulama:** Revoke + API çağrısı → beklenen: ret + kurtarma.
- **Sonuç:** Çökme/erikse M5 → P1. (Durum: PLANNED.)

### §10.4 S4 — Dosya erişim sınırı (P0)

- **Kural:** UID/MAC dışı erişim ret (K5, §9 M4).
- **Yüzey:** DAC + SELinux (§1).
- **Doğrulama:** Başka uygulama alanına okuma → beklenen: EACCES/denied.
- **Sonuç:** Okunursa K5 ihlali → P0. (Durum: PLANNED.)

### §10.5 S5 — İzin grubu darlığı (P1)

- **Kural:** Beklenmeyen geniş izin beyanı reddedilmelidir (K4, §9 M6).
- **Yüzey:** İzin sınıflandırma (§1).
- **Doğrulama:** Gereksiz izin beyanı tespiti → beklenen: daraltma talebi.
- **Sonuç:** Geniş beyan sürerse M6. (Durum: PLANNED.)

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
| Bu dosya §0–§6 | Profil, §2.1–§2.3 izin kalıpları, sınırlar |
| K000 security kardeşleri | Çapraz okuma (§12) |
| K000 testing | Doğrulama → test (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Android izin dokümanı | K1–K8 | Erişilebilir URL eklenince işaretlenir |
| Cihaz/log örnekleri | §10 | ⚠️ VERIFICATION REQUIRED (alınmadı) |
| Manifest/runtime çıktıları | statik/ölçüm | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
yetki-ve-erisim-modeli-android.md
|-- Sec0 ELI15
|-- Sec1 Android guvenlik profili (yetki modeli)
|-- Sec2 Kod ornekleri (2.1 manifest | 2.2 runtime | 2.3 ret akisi)
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
| T1 | Beyanlı izinle runtime isteği (K1/K2) | Grant başarılı |
| T2 | Reddedilmiş izinle güvenli mod (K3) | Çökme yok |
| T3 | UID içi dosya erişimi (K5) | Başarılı |
| T4 | Revoke sonrası canlanma (K6) | Kurtarma |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Beyansız runtime izni | Ret/M1 | K1/K2 ihlali → P0 |
| N2 | Ret akışında zorlama | Kapatma/güvenli mod | K3 ihlali → P1 |
| N3 | Revoke sonrası çağrı | Ret (M5) | K6 ihlali → P1 |
| N4 | Başka UID alanına okuma | EACCES/denied (M4) | K5 ihlali → P0 |
| N5 | Signature izinle yetkisiz paket | Ret (M7) | K7 ihlali → P0 |
| N6 | Gereksiz geniş izin beyanı | Daraltma (M6) | K4 ihlali → P1 |
| N7 | Rol çakışmalı atama | Ret (M8) | K8 ihlali → P2 |

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
| M4 tekrarı | UID/MAC denetimi |
| M6/M7 tekrarı | İzin beyanı daraltma incelemesi |

Log append-only; `ts` geriye sarılmaz; `kanit` boş olay sayılmaz; P0 olayları
bir sonraki §13.2 turunda tekrar denenir. §6 S1/S2 etkilenmez; frontmatter
değişmez. Sıra: P0 → K1, K2, K5, K7 → P1 → K3, K4, K6 → P2 → K8/M5/M8.

### §15 Append-only + kapı özeti (yetki eki protokolü)

Her gelecek eki yalnız Authority kuyruğundan sonra eklenir; §0–§14,
frontmatter ve Authority satırı değişmez; sıradaki numara §16'dır. Yasaklar:
silme/yeniden yazma · dolgu/TBD · §6 S1/S2 kapatma · frontmatter değişikliği ·
Mojibake. Ekleme sonrası ölç; 500'ün altındaysa §<n+1> kaynak atıflı eklenir.

### §16 Zorunlu 8 soruluk kapı özeti (son okuma)

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | Manifest beyanı eksiksiz mi? | §2.1 + §13.2 N1 |
| 2 | Runtime izin akışı doğru mu? | §2.2 + §10.1 (PLANNED) |
| 3 | Ret akışı güvenli mi? | §2.3 + §13.2 N2 |
| 4 | İzin grupları dar mı? | §7 K4 + §13.2 N6 |
| 5 | Dosya erişimi UID/MAC korumalı mı? | §13.2 N4 |
| 6 | Revoke davranışı kurtarıcı mı? | §13.2 N3 |
| 7 | Signature izinler korunuyor mu? | §13.2 N5 |
| 8 | Rol atamaları çakışmasız mı? | §13.2 N7 |

3 dk rota: §16 → §9.1 → §6 (oku, **kapatma**). Hatalar: kanıtsız "çalışıyor"
iddiası, PLANNED'ı geçiş saymak, log düzenlemek, fail-open yorumu.
Sonraki tur: §8 → cihaz ölçümü · §10 → dinamik kanıt · §11.2 → tarihli URL ·
§13.2 → §14 olayı. Bu işler bu turda **yapılmadı**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §17 Yetki modeli ayrıntı kontrol listesi (Android)

§7 matrisinin genişletilmiş sürümü; her satır bağımsız kanıtlanabilir (§7.1).

### §17.1 İzin beyanı/darlık sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| A1 | Bildirilen izin listesi = kullanılan | Fazla beyan yok | manifest dökümü |
| A2 | İzin grupları dar mı (dangerous) | En az yetki ilkesi | izin listesi |
| A3 | maxSdkVersion / koşullu beyan | İstenmeyen sürüm yok | manifest |
| A4 | Kullanım gerekçesi (permission rationale) | Kullanıcı akışı doğru | UI akışı |
| A5 | features/zorunluluk beyanı | Tutarlı | manifest |
| A6 | Özel/signature izinler | Kapsamı dar | paket denetimi |
| A7 | Rol beyanı | Gereksiz rol yok | RoleManager |
| A8 | Debug/test izin izleri | Üretimde yok | üretim manifesti |

### §17.2 Runtime/erişim sınıfı ayrıntılar

| # | Ayrıntı | Beklenen | Kanıt |
|---|---|---|---|
| B1 | İzin isteme anları (lazy vs eager) | Gerektiğinde istenir | akış logu |
| B2 | Grant sonrası davranış | Özellik açılır | log |
| B3 | Ret sonrası davranış | Güvenli kapalı | çökme yok kanıtı |
| B4 | Revoke/denied davranışı | Kurtarıcı ret | ret olayı |
| B5 | Dosya erişim yolları (scoped) | Yalnız kendi alanı | EACCES kanıtı |
| B6 | IP kontrolleri (AIP benzeri erişim) | Dışa çağrısız | çağrı incelemesi |
| B7 | Servis/içerik sağlayıcı yetkileri | Exported sınırı | bileşen listesi |
| B8 | İzin günlüğü (grant geçmişi) | Tarihli geçmişi | sistem logu |

### §17.3 İzleme adımları (sıra)

1. §17.1 → §17.2 satırlarını sırayla ölç.
2. Her reti §14 şemasına yaz; beklenen ret gelmeyeni işaretle.
3. P0 açık satır için 3 tur sonra üst karar iste.
4. Çıktı yoksa `⚠️ VERIFICATION REQUIRED`.

### §17.4 Kapı ilişkisi + kardeş bağı

| Bağ | Katkı |
|---|---|
| İçerik kapısı | §17 uzunluğuyla ölçülür |
| §6 açık sorular | Etkilenmez (kapatma yasağı) |
| Frontmatter | Değişmez |
| §7 | §17 §7'i ayrıntılandırır |
| §9 / §10 | A/B satırları M/S'ye bağlanabilir |
| §13 / §14 | Sonuçlar negatif tur + olay şemasına akar |

### §17.5 SSS

**S: §17 satırları PLANNED mı?**
C: Beklenen sütunu şart; gözlenen sütunları kanıt gelene kadar PLANNED.

**S: Sıra?**
C: P0 (A1, A6, B3, B4, B5) önce; A7/A8, B6–B8 son.

**S: Kanıt nereye?**
C: Bu dosyaya eki satır; mevcut satırlar değişmez.

**S: Ne zaman tekrar?**
C: Manifest/paket değişiminde + haftalık (§17.6).

### §17.6 Ölçüm tekrarlanabilirlik tablosu

| Ölçüm | Kaynak | Sıklık |
|---|---|---|
| Manifest izin dökümü | §17.1 | Sürüm değişiminde |
| Runtime davranış | §17.2 | İzin akışı değişiminde |
| Dosya erişim ret | §17.2 B5 | Haftalık |
| Olay şeması bütünlüğü | §14 | Olay başına |

### §17.7 Süreklilik

§15 append protokolü + §16 kapı özeti birlikte geçerlidir. Bir sonraki eki
yazacak kişi: §16 → §9.1 → §6 okur, sonra yalnız Authority kuyruğundan sonra
§18 ekler. Ölç-ekle-devam döngüsü sürer; dolgu yasak.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
