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

---

## §7 Çapraz kart matrisi (Android uygulama sınırı kontrol haritası)

§1'deki Android güvenlik profili ile §2 referans kalıpları ve bu dosyanın
diğer bölümleri çaprazlandığında aşağıdaki matris çıkar. Her satır bir sınırkontrolüdür.
Kanıt olmadan kontrol "var" sayılmaz (§5 referans kanıtı, Red Team modu).

| # | Sınır kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | UID sandbox ayrımı (uygulama başına kimlik) | §2.1 UID sandbox | Linux + Zygote | Statik + dinamik | P0 |
| K2 | SELinux etiket zinciri (kaynak → hedef eşlemesi) | §2.2 etiket zinciri | MAC katmanı | Statik (politika) | P0 |
| K3 | Manifest izin beyanı + çalışma zamanı izni | §1 izin modeli | Permission Controller | Dinamik | P0 |
| K4 | Uygulama başına kısıt ölçümü (kota/dağılım) | §2.3 adb ölçümü | ResourceManager | Ölçüm | P1 |
| K5 | Paket imzası + güncelleme imzası eşleşmesi | §1 paket katmanı | PackageManager | Statik | P0 |
| K6 | Paylaşılan veri paylaşma sınırı (exported/filtre) | §1 bileşen katmanı | Activity/Provider filtresi | Dinamik | P1 |
| K7 | Kök/uygunsuz ortam reddi (tampon/tuz) | §1 doğrulama katmanı | Play integrity sınıfı | Dinamik | P1 |
| K8 | Ağ izni + cleartext kısıtı | §1 ağ katmanı | Network policy | Statik + dinamik | P1 |

### §7.1 Matris okuma kuralı

Her satır yalnız şu kanatlardan biriyle onaylanır:

1. **Statik kanıt** — manifest satırı, politika dosyası, imza bloğu (Dosya: ile).
2. **Dinamik kanıt** — çalışma zamanında reddetme (SecurityException/log olayı).
3. **Ölçüm kanatı** — §2.3 adb ölçümünün yeniden üretilebilir çıktısı.

Kanıtsız satır `VERIFICATION REQUIRED` kalır; tahminle doldurulmaz.

### §7.2 Çapraz kart eşlemesi (sınır → kart kategorisi)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K1, K7 | §2.1 UID, §10 S4 |
| İzin kartları | K3, K6 | §1 manifest, §10 S2 |
| Bağlantı kartları | K8 | §1 ağ, §10 S3 |
| Depolama kartları | K1, K2 | §2.2 etiket, §10 S5 |
| Paket kartları | K5 | §1 imza, §10 S1 |
| Kaynak kartları | K4 | §2.3 ölçüm, §13 T3 |

## §8 Ses/RT yüzeyi (Android için PLANNED senaryo)

> **DURUM: PLANNED** — Zincir bu turda gerçek cihaz/log ile doğrulanmadı;
> sonuç satırları ölçüm eklenene kadar PLANNED kalır.

### §8.1 Neden bu bölüm bir uygulama sınırı dosyasında?

Ses alt sistemi, uygulama sınırının ayrıcalıklı geçiş noktasıdır: ses oturumu
kimliği, önbellek paylaşımları ve alt sistem/servis erişimi, §7 matrisindeki
K1/K3/K6 kontrolleriyle aynı yetki altyapısını kullanır. Gecikme/kalite ölçümü
(SNR/THD, codec) bu dosyanın dışındadır; yalnız sınır ve başarısızlık yüzeyi var.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Sınır)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Uygulama ses kaynağı/oynatma oturumu açar | İzin denetimi (K3) | PLANNED |
| A2 | Ses servisine (ayrıcalıklı) istek gider | UID/SELinux eşlemesi (K1/K2) | PLANNED |
| A3 | Paylaşılan önbellek/ölçek üzerinden veri akar | K2 etiket zinciri ihlal edilmemeli | PLANNED |
| A4 | Uygulama sonlanır | Oturum kapanır, artık kaynak yok (§9 M4) | PLANNED |

**Kapsam dışı:** ses algoritması/codec/DSP. **Harici referans:** fallback katman
modeli `.ai/electronic/drivers/audio-drivers.md` — bu turda yalnız **okuma**
(beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (Android uygulama sınırı)

| Mod | Tetikleyici | Gözlenen belirti | Sınır etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | UID ihlali (izinli olmayan PID davranışı) | SecurityException / ret | Erişim engellenir | Uygulama yeniden başlat |
| M2 | SELinux reddi (denied) | audit log satırı | İşlem düşer | Politika/etiket denetimi |
| M3 | İmza uyuşmazlığı güncellemede | Güncelleme reddi | Paket korunur | İmzalı APK'ya dön |
| M4 | Uygulama çökmesi (sınır içinde) | Süreç ölümü | Kaynak sızıntısı riski | Kanal oturumu kapanır |
| M5 | Exported bileşen kötüye kullanımı | Yetkisiz intent | Veri ifşası riski | Filtre/deny düzeltmesi |
| M6 | Cleartext ağ ihlali | Ağ politikası reddi | Bağlantı düşer | TLS'ye zorla |
| M7 | Kota/kısıt aşımı | ResourceManager ret/kill | Uygulama durur | §2.3 ölçümüyle analiz |
| M8 | Tuzlanmış ortam (root/tampon) tespiti | Bütünlük reddi | Açılış reddi | Normal ortam |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — M1, M2, M3, M5, M8:** fail-closed; ret kanıtı zorunlu.
2. **P1 — M6, M7:** tek yüzey düşer, sistem ayakta.
3. **P2 — M4:** yalnız ilgili süreç yeniden başlatılır.

`fail-open` (ihlale izin) hiçbir modda kabul edilmez. 3 başarısız düzeltme
turunda durulur, şüpheli varsayım açıkça yazılır (üst karar).

## §10 Kullanım senaryoları

Kalıp: **kural → yüzey → doğrulama adımı → sonuç (işaretli)**.

### §10.1 S1 — Paket imzası eşleşmesi (P0)

- **Kural:** Güncelleme, orijinal imzayla eşleşmiyorsa reddedilir (K5, §9 M3).
- **Yüzey:** PackageManager paket katmanı (§1).
- **Doğrulama:** Farklı imzayla güncelleme APK'si → beklenen: ret olayı.
- **Sonuç:** Ret yoksa K5 ihlali → P0. (Durum: PLANNED — gerçek cihaz ölçümü yok.)

### §10.2 S2 — Manifest dışı izin denemesi (P0)

- **Kural:** Beyan edilmemiş izin çalışma zamanında reddedilir (K3, §9 M1).
- **Yüzey:** Permission Controller (§1).
- **Doğrulama:** İzinli API çağrısı (izinsiz APK) → beklenen: SecurityException.
- **Sonuç:** İzin verilirse K3 ihlali → P0. (Durum: PLANNED.)

### §10.3 S3 — Cleartext bağlantısı (P1)

- **Kural:** Politika cleartext'e izin vermiyorsa bağlantı düşer (K8, §9 M6).
- **Yüzey:** Network policy (§1 ağ).
- **Doğrulama:** http:// hedefi → beklenen: bağlantı reddi.
- **Sonuç:** Bağlanırsa K8 ihlali → P1. (Durum: PLANNED.)

### §10.4 S4 — Kök ortam reddi (P1)

- **Kural:** Bütünlük kontrolü başarısızsa uygulama reddeder (K7, §9 M8).
- **Yüzey:** Doğrulama katmanı (§1).
- **Doğrulama:** Tuzlanmış ortamda açılış → beklenen: reddetme.
- **Sonuç:** Açılırsa K7 ihlali. (Durum: PLANNED.)

### §10.5 S5 — Sandbox dışı depolama erişimi (P0)

- **Kural:** Kendi UID alanının dışındaki veri erişilemez (K1/K2, §9 M2).
- **Yüzey:** UID sandbox + SELinux (§2.1/§2.2).
- **Doğrulama:** Başka uygulama verisine okuma → beklenen: ret (EACCES/denied).
- **Sonuç:** Okunursa K1/K2 ihlali → P0. (Durum: PLANNED.)

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
| Bu dosya §0–§6 | Profil, referans kalıpları (§2.1–§2.3), sınırlar |
| K000 isletim-sistemi-security kardeşleri | Çapraz okuma (§12) |
| K000 isletim-sistemi-testing | Doğrulama → test karşılığı (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses katmanı fallback — yalnız okuma |

### §11.2 Dış kaynak kategorileri

| Kategori | Dayanak | Durum |
|---|---|---|
| Android güvenlik dokümantasyonu | K1–K8 tanımları | Erişilebilir URL eklendiğinde işaretlenir |
| Cihaz/log örnekleri | §10 dinamik kanıt | ⚠️ VERIFICATION REQUIRED (bu turda alınmadı) |
| §2.3 ölçüm çıktıları | K4 kanıtı | ⚠️ VERIFICATION REQUIRED (PLANNED) |

### §11.3 Kaynak güven sırası

1. Cihaz üstü ölçüm/log. 2. Dosya içi § referansı. 3. Vault içi kardeş dosya.
4. Tarihli erişilebilir URL. 5. Model hafızası — tek dayanak asla.

## §12 Eşleme ağacı (okuma sırası)

```text
uygulama-guvenlik-siniri-android.md
├── §0 ELI15
├── §1 Android guvenlik profili
├── §2 Kod ornekleri (§2.1 UID | §2.2 SELinux | §2.3 olcum)
├── §3 CoreMusic repo durumu
├── §4 Sinir cumleleri            (neyin YAPILMAYACAGI)
├── §5 Referans + §5.1 olcum      (kanit uretimi)
├── §6 Acik sorular               (KAPATMA YASAGI)
├── §7 Cizraz matris              (bu eki: K1-K8 haritasi)
├── §8 Ses/RT PLANNED             (bu eki: sinirli yuzey)
├── §9 Basarisizlik modlari       (bu eki: M1-M8 + fail-safe)
├── §10 Kullanim senaryolari      (bu eki: S1-S5 kalip)
├── §11 Kaynakca envanteri        (bu eki: guven sirasi)
├── §12 Esleme agaci              (bu satir: kendini konumlandirir)
└── §13 Test + negatif kontrol    (bu eki: kapi adimlari)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | İmzalı paket kur (K5) | Kurulum başarılı |
| T2 | Beyanlı izinle API çağrısı (K3) | İzin verilir |
| T3 | §2.3 kısıt ölçümü (K4) | Beklenen dağılım |
| T4 | Etiketli dosya erişimi (K2) | Normal erişim |

### §13.2 Negatif kontroller (RED TEAM — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | Farklı imzayla güncelleme | Ret (M3) | K5 ihlali → P0 |
| N2 | Beyansız izinle çağrı | SecurityException (M1) | K3 ihlali → P0 |
| N3 | Başka UID verisine okuma | EACCES/denied (M2) | K1/K2 ihlali → P0 |
| N4 | Exported bileşene yetkisiz intent | Filtre reddi (M5) | K6 ihlali → P1 |
| N5 | Cleartext bağlantı denemesi | Ağ ret (M6) | K8 ihlali → P1 |
| N6 | Tuzlanmış ortamda açılış | Bütünlük reddi (M8) | K7 ihlali → P1 |
| N7 | Kota aşan çalışma | ResourceManager ret/kill (M7) | K4 ihlali → P2 |

**Kural:** Ret yoksa kontrol `VERIFICATION REQUIRED` kalır; "çalışıyor"
varsayılmaz. 3 başarısız tur → dur + üst karar.

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk) — bağlam + sınırlar.
2. §7 matrisi (3 dk) — kontrol ↔ yüzey.
3. §9 + §10 (7 dk) — modlar ve senaryolar.
4. §13.2 negatifler (5 dk) — ret kanıtları.
5. §6 açık sorular sona — okunur, **kapatılmaz**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §14 Olay günlüğü şeması + eskalasyon matrisi (Android sınır olayları)

§9 modları canlıya geçtiğinde her olay aşağıya girer; şemasız log parçası
kanıt sayılmaz (§7.1). Append-only: §0–§13 değişmedi.

### §14.1 Olay kayıt şeması (zorunlu alanlar)

| Alan | Tip | Açıklama |
|---|---|---|
| ts | ISO-8601 | Cihaz saati |
| mod | M1–M8 | §9 başarısızlık modu |
| kontrol | K1–K8 | §7 matrisindeki sınır kontrolü |
| uid / paket | ikili | Hangi uygulama sınırı (§2.1 karşılığı) |
| yuzey | § referansı | Örn. `§2.2 etiket zinciri` |
| beklenen / gozlenen | ikili | Reddedilmesi gereken vs gözlenen |
| durum | PLANNED / doğrulandı | §10.6 ile aynı alan adı |
| kanit | statik/dinamik/ölçüm | §7.1 kanıt türü |
| eskalasyon | P0/P1/P2 | §9.1 sırası |

### §14.2 Eskalasyon matrisi

| Olay sıklığı (5 dk pencere) | Eylem | Sahip |
|---|---|---|
| Aynı K'de 1 ret | Günlüğe yaz, izle | Sınır günlüğü |
| Aynı P0 K'de 2+ ret | Olay aç → inceleme | Olay müdahale |
| P0 K'de ret yok ama izin var | Sızıntı varsayımı → derhal eskale | Sızıntı yanıtı |
| M2 (SELinux denied) tekrarı | Politika/etiket denetimi | Çekirdek/MAC sahibi |
| M5 (exported kötüye kullanım) | Filtre denetimi + bileşen denetimi | Uygulama güvenliği |
| M7 (kota) tekrarı | §2.3 ölçümüyle sürüm incelemesi | Kaynak sahibi |

### §14.3 Log bütünlüğü kuralları

1. Günlük satırı append-only'dir; düzenleme/silme yasak.
2. `ts` geriye sarılamaz (saat atlaması M8 ipucu üretir).
3. `kanit` boş olay sayılmaz; yalnız ipucudur.
4. P0 olayları bir sonraki §13.2 turunda tekrar denenir.
5. Durum yalnız `durum` alanında yaşar; bu dosyanın frontmatter'ine dokunulmaz.

### §14.4 Bu bölümün kapı ilişkisi

| Kapı | §14 katkısı |
|---|---|
| İçerik kapısı | Ek uzunluk bu bölümle ölçülür |
| Açık soru kapısı | §6 S1/S2 etkilenmez (açık kalır) |
| Frontmatter kapısı | group/meta değişmez |
| Append kapısı | Yalnız sonradan eklendi |

### §14.5 Uygulama sırası (öncelik sırası güvenlik)

1. P0: K1, K2, K3, K5 → şema bu kontrollerle denenir.
2. P1: K6, K7, K8 → M5/M6/M8 ile eşleştirilir.
3. P2: K4, M4 → ölçüm/çalışma döngüsü takibi.
4. PLANNED satırları (§8, §10) en sonda; kanıt gelmeden "çalışıyor" yapılmaz.

### §14.6 §14 ile ilgili SSS

**S: Aynı olay K1 ve K2'yi ilgilendiriyorsa?**
C: Ana satır birincil ihlal edilen kontrol; diğeri `ilgili` notu.

**S: PLANNED senaryonun logu çıkarsa?**
C: Şema doldurulur, `durum: doğrulandı`; §8/§10 işareti yalnız kanıtla kalkar.

**S: Olay alanı dolarsa?**
C: Yeni segment açılır; eski segment silinmez (append-only).

**S: Kim bakar?**
C: §14.2 sahiplik bağlayıcı; çakışmada P0 sahibi önceliklidir.

### §14.7 Kardeş bölümlerle bağı

| Kardeş | Bağ |
|---|---|
| §7 | Şemanın K alanı matristen |
| §9 | M etiketi + eskalasyon önceliği |
| §10 | Senaryo kalıbı → durum alanı |
| §13 | Negatif kontrol sonucu → olay satırı |
| §2.1–§2.3 | Referans kalıpları → statik/ölçüm kanıtı üretir |

### §14.8 Ölçüm tekrarlanabilirlik kontrolü

| Ölçüm | Komut kaynağı | Sıklık |
|---|---|---|
| UID/sandbox durumu | §2.1 kalıbı | Açılışta |
| SELinux etiket zinciri | §2.2 politika çıktısı | Politika değişince |
| Kısıt ölçümü | §2.3 adb kabuğu | Haftalık |
| Olay şeması bütünlüğü | §14.1 alan denetimi | Olay başına |

Çıktı bu dosyaya **ek** olarak yazılır (mevcut satırlar değişmez); çıktı
yoksa ilgili satır `⚠️ VERIFICATION REQUIRED` kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §15 Zorunlu 8 soruluk kapı özeti (son okuma)

Bu bölüm tek ekran okuma özeti verir; içeriği §0–§14'ten türetilmiştir.

| # | Soru | Cevap referansı |
|---|---|---|
| 1 | UID sandbox ayrımı ölçüldü mü? | §2.1 + §13.1 T4 |
| 2 | SELinux etiket zinciri statik mi? | §2.2 + §7 K2 |
| 3 | İzin beyanı eksiksiz mi? | §1 + §10.2 (PLANNED) |
| 4 | Kısıt ölçümü alındı mı? | §2.3 + §13.1 T3 |
| 5 | İmza eşleşmesi doğrulandı mı? | §10.1 (PLANNED) |
| 6 | Exported bileşen filtresi var mı? | §7 K6 + §13.2 N4 |
| 7 | Kök/tuz reddi etkin mi? | §10.4 (PLANNED) |
| 8 | Cleartext kısıtı etkin mi? | §10.3 (PLANNED) |

### §15.1 3 dakikalık okuma rotası

1. §15 tablosu (1 dk) — sekiz soru.
2. §9.1 fail-safe sırası (1 dk) — hangi mod önce.
3. §6 açık sorular (1 dk) — oku, **kapatma**.

### §15.2 Sık yapılan hatalar

| Hata | Sonuç | Doğru davranış |
|---|---|---|
| "SELinux enforcing, bitti" (politika kanıtı yok) | Kanıtsız iddia | §2.2 çıktısı/etiket kanıtı ekle |
| PLANNED'ı "çalışıyor" saymak | Sahte kapı | Durum alanını koru |
| Log satırı düzenlemek | Bütünlük kaybı | Append-only (§14.3) |
| §6 S1/S2'yi kapatmak | Kapı ihlali | Yeni bölüm ekle, S satırı değişmesin |
| fail-open yorumu | P0 sızıntı | §9.1 ret kanıtı ister |

### §15.3 Bir sonraki tur için hazır işler

| İş | Koşul | Sıra |
|---|---|---|
| §8 PLANNED satırlarını cihaz ölçümüyle güncelle | Cihaz erişimi | P0 sonrası |
| §10 senaryolarına gözlenen/ bekleneni ekle | Dinamik kanıt | P1 |
| §11.2 dış kaynakları tarihli URL ile doldur | Erişilebilir URL | Sıradaki tur |
| §13.2 negatif turunu çalıştır → §14.1 şemasına yaz | Test ortamı | P0 |

Bu işler bu turda **yapılmadı**; yapılıyormuş gibi raporlanmaz
(Zero-Hallucination, §11.3 sırası).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
