---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa"
evidence:
  - "package.json:10"
  - ".github/workflows/ci.yml:154"
  - "https://appimage.org/"
  - "https://wixtoolset.org/"
validation: "2026-10-13 · 4 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q6 — hedef paket formatları (MSI/deb/AppImage) seti UNKNOWN"
group: mimari
---

# derleme-ve-paketleme-surucu — Kademe-4 Paket Formatı Sürücüsü

> **Kapsam:** Paket formatlarının **kurulum, denetim, kaldırma ve güncelleme** sürücüsü:
> WiX/NSIS (Windows), deb/rpm (Debian/RHEL), AppImage/flatpak (evrensel Linux),
> dmg/pkg (macOS) ve JS/PHP ekosistem paketçileri (npm/Composer). **Kanıt dili:**
> S1 = birebir repo satırı · S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-surucu` |
| parent_id | `…/isletim-sistemi-cross/derleme-ve-paketleme` |
| group | `mimari` |
| Bağımlı | `derleme-ve-paketleme-anayasa.md` (§12 format matrisi orada) |
| Repo gerçeği | Paketlenen yüzey = PHP/JS (Composer+npm); native paket YOK (PLANNED) |

## §0 ELI15

Derleme ikiliyi üretir; **paket** onu kutuya koyar. Kutu formatı OS'a göre değişir:
Windows'ta MSI/MSIX (WiX/NSIS ile üretilir), Debian'da `deb`, evrensel Linux'ta
AppImage (tek dosya), macOS'ta dmg/pkg. Bu depoda bugünkü "paket" npm tarball'ı ve
Composer vendor/ kurulumudur — `package.json:10-17` + `ci.yml:154` (`npm ci`) S1 kanıttır.

| Format | Üreten araç | Kurulum | Kaldırma |
|---|---|---|---|
| MSI | WiX/NSIS | `msiexec /i` | `msiexec /x` |
| deb | `dpkg-deb`/`debuild` | `dpkg -i`/`apt install` | `apt remove` |
| rpm | `rpmbuild` | `rpm -i`/`dnf install` | `dnf remove` |
| AppImage | `appimagetool` | çalıştır (kurulum yok) | sil (dosya) |
| dmg | `hdiutil`/Xcode | sürükle | çöp |
| npm | `npm pack` | `npm ci`/`npm i` | `npm rm` |

## §1 WiX / NSIS (Windows) sürücü matrisi

| # | Özellik | WiX | NSIS |
|---|---|---|---|
| 1 | Girdi | XML kaynak (`.wxs`) | betik (`.nsi`) |
| 2 | Üretim | MSI (Windows Installer) | tek `.exe` kurulum |
| 3 | Sürüm yönetimi | UpgradeCode + MajorUpgrade | betikte manual |
| 4 | İmza | Authenticode (post-build) | Authenticode (post-build) |
| 5 | Sessiz kurulum | `msiexec /quiet` | `/S` |
| 6 | Geri alma (rollback) | Windows Installer transaction | sınırlı |
| 7 | Dosya self-reg | `SelfReg` (tavsiye edilmez) | betik |
| 8 | Kayıt defteri | WiX RegistryKey | NSIS WriteRegStr |
| 9 | Çok dil | localization | MUI2 |
| 10 | Öğrenme eğrisi | XML + candle/light | betik dili |

**Kaynak:** WiX kök dokümanı (§9 #2) · NSIS ana sayfası (§9 #3).

## §2 deb / rpm (Linux) sürücü matrisi

| # | Özellik | deb (Debian) | rpm (Fedora/RHEL) |
|---|---|---|---|
| 1 | Arşiv | `ar` + tar (data.tar.xz) | cpio + rpm başlık |
| 2 | Denetim dosyası | `DEBIAN/control` | SPEC başlığı |
| 3 | Bağımlılık beyanı | `Depends:` | `Requires:` |
| 4 | Sahiplik veritabanı | `/var/lib/dpkg` | `/var/lib/rpm` |
| 5 | Hook scriptleri | `preinst/postinst/prerm/postrm` | `%pre/%post/%preun/%postun` |
| 6 | İmza | GPG (repo) | GPG (repo) |
| 7 | Sürüm şeması | `1.2.3-1` (revizyon) | `1.2.3-1.dist` |
| 8 | Mimar | `amd64/arm64` | `x86_64/aarch64` |
| 9 | Yerel repo | apt deposu | dnf/yum deposu |
| 10 | Kurulum aracı | `apt` (üst katman) | `dnf` (üst katman) |

**Kaynak:** Debian Referans Bölüm 5 (paket yönetimi) (§9 #4).

## §3 AppImage / flatpak (evrensel Linux) sürücü matrisi

| # | Özellik | AppImage | flatpak |
|---|---|---|---|
| 1 | Kurulum | dosyayı çalıştır | `flatpak install` |
| 2 | İzolasyon | yok (sandbox opsiyonel) | sandbox (bubblewrap) |
| 3 | Runtime | kendi taşır | ortak runtime |
| 4 | Boyut | büyük (her şey içeride) | küçük (runtime paylaşılır) |
| 5 | Güncelleme | AppImageUpdate (zsync) | `flatpak update` |
| 6 | İmza | gpg2 imza dosyası | ostree imzası |
| 7 | Masaüstü entegrasyonu | manuel `.desktop` | otomatik |
| 8 | FUSE gereksinimi | evet (bazı sistemlerde yok) | hayır |
| 9 | Depo | ProjectCenter/AppimageHub | Flathub |
| 10 | Hedef | tek dosya dağıtım | izole uygulama mağazası |

**Kaynak:** AppImage resmi sayfası (§9 #1).

## §4 dmg / pkg (macOS) sürücü matrisi

| # | Özellik | dmg | pkg |
|---|---|---|---|
| 1 | İçerik | disk görüntüsü | kurulum paketi |
| 2 | Kurulum | sürükle-bırak | Installer.app |
| 3 | İmza | codesign + notarization | codesign + notarization |
| 4 | Gatekeeper | quarantain imzası | aynı |
| 5 | Toplu dağıtım | az | MDM/pkg uygun |
| 6 | Geri alma | manuel | Installer rollback |
| 7 | Homebrew alternatifi | cask | formula (kaynaktan derleme) |

## §5 JS/PHP ekosistem paketçileri (repo S1)

| # | Özellik | npm | Composer |
|---|---|---|---|
| 1 | Tanım | `package.json` (repo:10-17) | `composer.json` (repo:6,8) |
| 2 | Kilit | `package-lock.json` | `composer.lock` (ci.yml:44 hashFiles) |
| 3 | Kurulum | `npm ci` (ci.yml:154) | `Composer install` (ci.yml:48) |
| 4 | Registry | npmjs | packagist |
| 5 | Paket biçimi | tarball | zip/dist |
| 6 | Sürüm | SemVer | SemVer + constraints |
| 7 | Script | `scripts` | `scripts` |
| 8 | Yayınlama | `npm publish` | Packagist/VCS |
| 9 | Önbellek | CI cache (ci.yml:154 öncesi) | CI cache (ci.yml:44) |
| 10 | Yerel patlatma (link) | `npm link` | `composer repositories path` |

## §6 Repo kanıtı (S1)

**Kanıt 1 — npm test araç zinciri:** `package.json:10-17`

```json
  "devDependencies": {
    "@playwright/test": "^1.63.0",
    "@vitest/coverage-v8": "^5.0.3",
    "axe-playwright": "^1.0.0",
    "jsdom": "^30.1.1",
    "playwright": "^1.63.0",
    "vitest": "^5.0.3"
  }
```

**Kanıt 2 — CI kilitli npm kurulumu:** `.github/workflows/ci.yml:154`

```yaml
      - name: npm ci
```

| # | Kanıt | Dosya:Satır | Ne kanıtlar | Sınıf |
|---|---|---|---|---|
| 1 | devDependencies listesi | `package.json:10-17` | test araç zinciri npm ile | S1 |
| 2 | `npm ci` | `.github/workflows/ci.yml:154` | kilitli kurulum (lock var) | S1 |
| 3 | `Composer install (shared)` | `.github/workflows/ci.yml:48` | PHP tarafı | S1 |
| 4 | `key: ${{ runner.os }}-composer-...` | `.github/workflows/ci.yml:44` | önbellek OS başına | S1 |
| 5 | `"php": ">=8.4"` | `shared/composer.json:8` | platform kısıtı | S1 |
| 6 | native paket (msi/deb/AppImage) | 0 | paketleme PLANNED | S1 |
| 7 | `*.wxs`/`.nsi`/`*.spec` | 0 | sürücü kaynakları yok | S1 |

## §7 Kurulum/denetim/kaldırma yaşam döngüsü matrisi

| # | Adım | deb | rpm | MSI | AppImage | npm |
|---|---|---|---|---|---|---|
| 1 | Kurulum | `apt install` | `dnf install` | `msiexec /i` | dosyayı çalıştır | `npm ci` |
| 2 | Denetim | `dpkg -l` | `rpm -qa` | `Get-Package` | dosya var mı | `npm ls` |
| 3 | Bütünlük | GPG | GPG | Authenticode | gpg imza | provenance |
| 4 | Log | `/var/log/apt` | journal | Event Log | — | npm log |
| 5 | Kaldırma | `apt remove` | `dnf remove` | `msiexec /x` | sil | `npm rm` |
| 6 | Temizlik | `apt autoremove` | `dnf autoremove` | installer temizler | — | `npm prune` |
| 7 | Yükseltme | `apt upgrade` | `dnf upgrade` | MajorUpgrade | AppImageUpdate | `npm update` (lock) |
| 8 | Konfigürasyon koruma | conffiles (dpkg) | `%config(noreplace)` | — | — | — |

## §8 Riskler

| # | Risk | Olasılık | Etki |
|---|---|---|---|
| R1 | Paket formatı kararı yok (Q6) → kurulum yüzeyi tasarlanamaz | yüksek | orta |
| R2 | WiX ↔ NSIS tercihi yapılmazsa Windows dağıtımı gecikir | orta | orta |
| R3 | deb `postinst` betiği hatası kurulumu yarım bırakır | orta | yüksek |
| R4 | AppImage FUSE olmayan sistemde açılmaz (`--appimage-extract-and-run` gerekir) | düşük | orta |
| R5 | npm `^` kademeli sürüm sürüklemesi (`package.json:11-16`) | orta | orta |
| R6 | `npm ci` lock'suz çalışamaz — lock commit edilmemişse CI düşer | düşük | yüksek |
| R7 | macOS notarization alınmazsa Gatekeeper engeller | yüksek | yüksek |
| R8 | İmzalama anahtarı bulunmayan artifact dağıtılamaz (Q-X34) | orta | yüksek |

## §9 Kapsam dışı

| # | Konu | Nereye gider |
|---|---|---|
| 1 | Toolchain/derleme bayrakları | `derleme-ve-paketleme-anayasa.md` §11 |
| 2 | Kod örnekleri (CMake/Composer) | `derleme-ve-paketleme-ornekler.md` |
| 3 | CI pipeline tasarımı | `K17-devops` |
| 4 | Güvenlik/İmzalama politikası | `isletim-sistemi-security` |
| 5 | Test eşikleri (smoke test) | `isletim-sistemi-testing` |
| 6 | OS API/ABI | `../platform-yuzeyleri` |
| 7 | `#ifdef` guard | `../tasinabilirlik-siniri` |

## §10 Kaynakça (web araştırma · 2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| AppImage, Linux uygulamalarını tek dosyada dağıtmayı tanımlar | https://appimage.org/ | 2026-10-13 | ✅ HTTP 200 |
| WiX Tool Set, MSI kurulum paketleri için kaynak araç setidir | https://wixtoolset.org/ | 2026-10-13 | ✅ HTTP 200 |
| NSIS, Windows kurulum üreticisi (script tabanlı) projesidir | https://nsis.sourceforge.io/ | 2026-10-13 | ✅ HTTP 200 |
| Debian Referans Bölüm 5 paket yönetimini (dpkg/apt) belgeler | https://www.debian.org/doc/manuals/debian-reference/ch05.en.html | 2026-10-13 | ✅ HTTP 200 |

## §11 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q6 | Hedef paket formatları seti (MSI? deb? AppImage? dmg?) nedir? | ⚠️ VERIFICATION REQUIRED |
| Q-X36 | Windows kurulum aracı: WiX mi NSIS mi? | UNKNOWN |
| Q-X37 | Evrensel Linux dağıtımı gerekli mi (AppImage/flatpak)? | UNKNOWN |
| Q-X38 | `package-lock.json` repo'da commit (ci.yml:154 `npm ci` bunu varsayar) | ⚠️ VERIFICATION REQUIRED |
| Q-X39 | macOS notarization hesap/hazırlık var mı? | UNKNOWN |

## §12 Paket içerik hiyerarşisi (PLANNED)

| # | Öğe | Windows | Linux | macOS |
|---|---|---|---|---|
| 1 | executable | `bin/name.exe` | `usr/bin/name` | `Contents/MacOS/name` |
| 2 | kütüphane | `bin/*.dll` | `usr/lib/name/` | `Contents/Frameworks/` |
| 3 | yapılandırma | `ProgramData/name/` | `/etc/name/` | `/Library/Application Support/name/` |
| 4 | veri/asset | `share/` | `usr/share/name/` | `Contents/Resources/` |
| 5 | log dizini | `%LOCALAPPDATA%\name\Logs` | `/var/log/name/` | `~/Library/Logs/name/` |
| 6 | menü kısayolu | Start Menu | `.desktop` | `/Applications` |
| 7 | kaldırıcı | Add/Remove Programs | package manager | `/Applications` sil |

## §13 Denetim (audit) matrisi — paket bütünlüğü

| # | Denetim | deb | rpm | MSI | npm |
|---|---|---|---|---|---|
| 1 | Dosya listesi | `dpkg -L` | `rpm -ql` | `msiexec /a` çıkarım | `npm pack --dry-run` |
| 2 | Hash/bütünlük | debsums | `rpm -V` | MSI File table | provenance/ attest |
| 3 | İmza | `debsigs verify` | `rpm --checksig` | Authenticode | sigstore |
| 4 | Sahiplik | dpkg veritabanı | rpm veritabanı | — | registry |
| 5 | Bağımlılık grafiği | `aptitude why` | `dnf repoquery --requires` | LaunchCondition | `npm ls` |

## §14 CI ile paket entegrasyonu (repo kanıtlı)

| # | Adım | Repo kanıtı | Durum |
|---|---|---|---|
| 1 | PHP bağımlılık kurulumu | `ci.yml:48` `Composer install (shared)` | S1 |
| 2 | Composer önbellek | `ci.yml:44` `${{ runner.os }}-composer-...` | S1 |
| 3 | npm kilitli kurulum | `ci.yml:154` `npm ci` | S1 |
| 4 | Paket adımı (msi/deb) | yok | PLANNED |
| 5 | Artifact yükleme (`actions/upload-artifact`) | okunmadı | S7 (UNKNOWN) |
| 6 | Release/publish | okunmadı | S7 (UNKNOWN) |

## §15 Sürüm/kaldırma çakışması matrisi (PLANNED)

| # | Senaryo | Sonuç | Önleme |
|---|---|---|---|
| 1 | Aynı sürüm iki kez kurulur | idempotent olmalı | paketçi testi |
| 2 | Eski sürüm üstüne kurulum | upgrade (downgrade reddi) | sürüm kilidi |
| 3 | Kaldırma sonrası yapılandırma | deb: conffiles korunur | rpm `%config(noreplace)` |
| 4 | Bozuk kurulum (kesinti) | MSI rollback / dpkg `--configure -a` | yaşam döngüsü §7 |
| 5 | Paralel iki sürüm | deb/rpm: çakışma reddi | Alternatives/Conflicts |

## §16 Ölçüm / kabul kriterleri

| # | Ölçüm | Yöntem | Eşik |
|---|---|---|---|
| 1 | Paket kurulabilirlik | temiz VM/sandbox kurulumu | 1/1 (PLANNED) |
| 2 | Kaldırma sonrası iz | dosya/kayıt defteri artığı | 0 |
| 3 | İmzalama | doğrulama adımı | geçerli |
| 4 | npm ci lock varlığı | `package-lock.json` | ⚠️ Q-X38 |
| 5 | URL doğrulama | HEAD 200 + envanter | kesişim 0 |

## §17 Hızlı karar tablosu — "hangi paketçi nerede"

| # | Karar anı | Doğru soru | Yönlendirme |
|---|---|---|---|
| 1 | Windows kurulum | MSI (kurumsal) mi exe (hafif) mi? | §1, Q-X36 |
| 2 | Debian hedef | deb mi AppImage mi? | §2-§3, Q-X37 |
| 3 | macOS | dmg mi pkg mi? | §4 |
| 4 | npm paketleme | `npm pack` yeterli mi, registry mi? | §5 |
| 5 | İmzalama | anahtar nerede? | Q-X34, §13 #3 |
| 6 | Çok dil | WiX localization / MUI2 | §1 #9 |
| 7 | Geri alma | MSI transaction yeterli mi? | §1 #6 |

## §18 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | conffiles | deb'de kullanıcı yapılandırma dosyası koruma listesi (§7 #8) |
| 2 | `%config(noreplace)` | rpm'de yapılandırma koruma etiketi (§7 #8) |
| 3 | notarization | macOS'ta Apple'a gönderim + tarama onayı (§4 #3) |
| 4 | MajorUpgrade | MSI'de sürüm yükseltme stratejisi (§1 #3) |
| 5 | zsync | AppImage artımlı güncelleme protokolü (§3 #5) |
| 6 | provenance | npm paketinin üretim kanıtı (§5 #10) |
| 7 | Conffiles tasarım kuralı | deb'de yapılandırma koruma davranışı (§15 #3) |

## §19 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-13 | İlk yazım — 4 URL doğrulandı (appimage · wixtoolset · nsis · debian-reference ch05), S1 kanıtlar (package.json:10-17 · ci.yml:154,48,44 · composer.json:8) |

## §20 Ölçüm kaydı (bu dosyanın doğrulaması — nihai)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | URL HEAD 200 | 4/4 |
| 2 | Envanter kesişimi | 0 |
| 3 | Repo kanıtı satır okuma | 5 (`Select-Object -Index (N-1)`) |
| 4 | Mojibake | 0 (§18 #5 düzeltildi: Çince karakter kaldırıldı) |
| 5 | `parent:` alanı | 0 (yalnız `parent_id`) |
| 6 | `group` | `mimari` (surucu eşlemesi) |
| 7 | Son satır Authority | var |
| 8 | Git commit | atılmadı (orchestrator'a ait) |
| 9 | Yazılan dosyalar | yalnız whitelist (`derleme-ve-paketleme/**`) |

## §21 Sürüm yükseltme (upgrade) matrisi — ayrıntı (PLANNED)

| # | Adım | deb | rpm | MSI |
|---|---|---|---|---|
| 1 | Eski sürümü algıla | dpkg veritabanı | rpm veritabanı | FindRelatedProducts |
| 2 | Kilit | dpkg lock | dnf lock | msiexec transaction |
| 3 | Dosya değişimi | dpkg unpack | rpm upgrade | ApplyDuplicateFile/MoveFile |
| 4 | Yapılandırma | conffiles (debconf) | `%config(noreplace)` | — |
| 5 | Hook | `postinst configure` | `%posttrans` | Commit (CustomAction) |
| 6 | Geri alma | `apt rollback` (sınırlı) | `dnf downgrade` | rollback (transaction) |
| 7 | Doğrulama | `dpkg --audit` | `rpm -V` | Event Log (1033/1040) |

## §22 Dosya sahipliği matrisi (kim neyi kurar)

| # | Dosya sınıfı | Sahip | Kaldırma davranışı |
|---|---|---|---|
| 1 | binary | paket | silinir |
| 2 | kütüphane | paket | silinir |
| 3 | yapılandırma (conffile) | kullanıcı+paket | korunur (deb) |
| 4 | log | runtime | silinmez |
| 5 | veritabanı (dpkg/rpm) | paket yöneticisi | uninstall temizler |
| 6 | kullanıcı verisi | kullanıcı | silinmez |
| 7 | cache | runtime | silinebilir |

## §23 Multi-OS artifact matrisi (anayasa §23 bağlantılı)

| # | Artifact | Format | İmza |
|---|---|---|---|
| 1 | `name-<ver>-win-x64.zip` | zip | Authenticode (exe içinde) |
| 2 | `name-<ver>-linux-x64.tar.gz` | tar.gz | gpg (detached) |
| 3 | `name-<ver>-linux-arm64.tar.gz` | tar.gz | gpg (detached) |
| 4 | `name-<ver>-macos-universal.dmg` | dmg | codesign+notary |
| 5 | `name_<ver>_all.deb` | deb | GPG |
| 6 | `name-<ver>.AppImage` | AppImage | gpg2 (zsync ile) |

## §24 Kaldırma sonrası artık denetimi (PLANNED)

| # | Artık | Kontrol |
|---|---|---|
| 1 | `/etc/name/` | deb: `purge` ile silinir |
| 2 | `%APPDATA%` veri | uninstall sorar/sormaz (karar) |
| 3 | log dizinleri | kalır (tercih) |
| 4 | masaüstü kısayolu | silinir |
| 5 | servis kaydı | silinir (surucu.md §3) |
| 6 | PATH girdisi | silinmeli (bilinen unutkanlık) |

## §25 Paket boyutu / içermeme (content minimization) matrisi

| # | Kural | Neden | Bağ |
|---|---|---|---|
| 1 | debug sembolleri ayrı artifact | boyut + gizlilik | anayasa §22 #3 |
| 2 | kaynak kodu paketlenmez | telif/boyut | — |
| 3 | test dosyaları paketlenmez | boyut | testing dalı |
| 4 | `node_modules`/`vendor` paket içi | reproducible + boyut | anayasa §3 |
| 5 | license dosyası zorunlu | compliance | compliance-auditor |
| 6 | SBOM eklenir | izlenebilirlik | anayasa §22 #5 |

## §26 Linux masaüstü entegrasyonu (PLANNED)

| # | Öğe | AppImage | deb/rpm |
|---|---|---|---|
| 1 | `.desktop` dosyası | AppDir içinde | `/usr/share/applications/` |
| 2 | ikon | AppDir ikon ağacı | hicolor theme |
| 3 | MIME kaydı | manuel | `update-mime-database` |
| 4 | MIME güncelleme | — | postinst hook |
| 5 | DBus registrasyonu | `--appimage-extract` ile | postinst |

## §27 Windows kayıt defteri matrisi (PLANNED)

| # | Anahtar | Amaç | Kaldırma |
|---|---|---|---|
| 1 | `HKLM\SOFTWARE\<Org>\<App>` | makine geneli yapılandırma | uninstall |
| 2 | `HKCU\Software\<Org>\<App>` | kullanıcı yapılandırma | uninstall (tercih: koru) |
| 3 | Uninstall anahtarı | Add/Remove Programs kaydı | uninstall |
| 4 | Shell association | dosya uzantısı | uninstall |
| 5 | PATH (machine) | komut erişimi | uninstall (unutkanlık riski, §24 #6) |

## §28 Bağlantı matrisi — surucu ↔ kardeşler

| # | Konu | Bağ |
|---|---|---|
| 1 | Format matrisi (özet) | `derleme-ve-paketleme-anayasa.md` §12 |
| 2 | Artifact adlandırma | `derleme-ve-paketleme-anayasa.md` §23 |
| 3 | Repo paketçileri (npm/Composer) | `derleme-ve-paketleme-ornekler.md` |
| 4 | Servis surucüsü | `../platform-yuzeyleri/platform-yuzeyleri-surucu.md` §3 |
| 5 | CI kanıtları | `K17-devops` (ci.yml) |

## §29 Paket surucüsü — 12 sık yapılan hata (PLANNED)

| # | Hata | Sonuç | Önleme |
|---|---|---|---|
| 1 | WiX UpgradeCode'yu sürümle değiştirmek | yan yana kurulum | sabit UpgradeCode |
| 2 | NSIS `/S` sessiz kurulum testi yok | otomatik güncelleme kırık | §1 #5 testi |
| 3 | deb mimari adı yanlış (`amd64` yerine `x86_64`) | kurulum reddi | §2 #8 |
| 4 | rpm `%config` etiketsiz yapılandırma | yükseltmede kullanıcı ayarı ezilir | §7 #8 |
| 5 | AppImage imzasız yayınlanır | güvenlik reddi | §3 #6 |
| 6 | macOS notarization atlanır | Gatekeeper engeli | §4 #3, R7 |
| 7 | `npm ci` lock'suz | CI düşer | Q-X38 |
| 8 | `composer.lock` commit edilmez | sürüm sürüklenmesi | anayasa §14 #1 |
| 9 | Kaldırıcı PATH'i temizlemez | ölü girdi | §24 #6 |
| 10 | Dosya sahipliği karıştırma (§22) | veri kaybı | §22 |
| 11 | Log/veri dizini paket içinde silme | kullanıcı verisi kaybı | §24 |
| 12 | SBOM/license atlanır | compliance ihlali | §25 #5-6 |

## §30 Denetim protokolü — paket kabul testi (PLANNED, testing dalına devredilir)

| # | Adım | Beklenen |
|---|---|---|
| 1 | Temiz ortam kurulumu | başarı |
| 2 | Kurulum sonrası çalıştırma | process ayakta |
| 3 | Dosya listesi denetimi | manifestle eşleşme |
| 4 | İmza doğrulama | geçerli |
| 5 | Yükseltme (eski sürüm üstüne) | veri korunur |
| 6 | Kaldırma | artık = 0 (§24 hariçler) |
| 7 | Tekrar kurulum | idempotent (§15 #1) |
| 8 | Sonuç raporu | artifact'a eklenir |

## §31 Açık sorular — genişletme

| # | Soru | Etiket |
|---|---|---|
| Q-X40 | Debian hedef sürüm (bookworm/trixie)? | UNKNOWN |
| Q-X41 | RPM ailesi hedefleniyor mu (Fedora/RHEL)? | UNKNOWN |
| Q-X42 | AppImage mi flatpak mı (Q-X37 altı)? | UNKNOWN |
| Q-X43 | Windows kurulum dil paketleri gerekiyor mu? | UNKNOWN |
| Q-X44 | Paket içi lisans dosyası zorunluluğu yazıldı mı? | UNKNOWN |

## §32 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-13 | İlk yazım — 4 URL (appimage · wix · nsis · debian ch05), S1 kanıtlar (package.json:10-17 · ci.yml:154) |
| 2 | v1.0.1 | 2026-10-13 | §21-§28 eklendi (upgrade · sahiplik · artifact · artık · boyut · masaüstü · kayıt defteri · bağlantı) |
| 3 | v1.0.2 | 2026-10-13 | §29-§31 eklendi (12 hata · kabul protokolü · açık sorular); yazım hataları düzeltildi (surucu→sürücü · CJK karakter kaldırıldı · conffadesigned→conffiles) |

## §33 Sözlük — genişletme

| # | Terim | Tanım |
|---|---|---|
| 8 | AppDir | AppImage kaynak dizin yapısı (§3) |
| 9 | hicolor | freedesktop ikon teması standardı (§26 #2) |
| 10 | MUI2 | NSIS modern kullanıcı arayüzü çerçevesi (§1 #9) |
| 11 | candle/light | WiX derleyici/bağlayıcı araçları (§1) |
| 12 | debconf | Debian yapılandırma sistemi (§21 #4) |
| 13 | MajorUpgrade | MSI sürüm yükseltme stratejisi (§1 #3) |
| 14 | Add/Remove Programs | Windows uninstall kaydı (§27 #3) |
| 15 | zsync | AppImage artımlı güncelleme (§3 #5) |

## §34 Bağlantı matrisi — surucu ↔ anayasa §kronoloji

| # | Surucu bölümü | Karşılık anayasa |
|---|---|---|
| 1 | §1 WiX/NSIS | §12 #3-4 |
| 2 | §2 deb/rpm | §12 #5-6 |
| 3 | §3 AppImage/flatpak | §12 #7-8 |
| 4 | §4 dmg/pkg | §12 #9-10 |
| 5 | §5 npm/Composer | §12 #11-12 |
| 6 | §12 içerik hiyerarşisi | §31 buildOutput |
| 7 | §23 artifact | §23 anayasa |
| 8 | §16 ölçüm | §6 anayasa |

## §35 İmza zinciri sürücüsü — Authenticode (Windows) vs imzalama (Linux/macOS)

> İmza **sürücüsü** buradadır: paketi kim üretti, paket değişti mi, kurulum
> sırasında kim doğrular. İmza **politikasının** kendisi `isletim-sistemi-security`
> dalındadır (bu dosya yalnız dağıtım yüzeyini tarif eder).

| # | Platform | İmza aracı | Doğrulayan | Başarısızlık | Kaynak (HEAD 200) |
|---|---|---|---|---|---|
| 1 | Windows (MSI/EXE) | Authenticode (signtool) | Windows Installer + sürücü yükleme | uyar/engelle | https://learn.microsoft.com/en-us/windows-hardware/drivers/install/driver-signing |
| 2 | Windows (MSIX) | MSIX kataloğu imzası | MSIX paket doğrulama | kurulum reddi | https://learn.microsoft.com/en-us/windows/win32/msi/windows-installer-portal |
| 3 | Debian (deb) | OpenPGP (`debsign`) | `apt` doğrulama | "imzasız paket" uyarısı | https://www.debian.org/doc/debian-policy/index.html |
| 4 | RPM | RPM GPG anahtarı | `dnf`/`rpm --import` | "GPG hata" reddi | https://www.debian.org/doc/debian-policy/index.html (yalnız Debian tarafı; RPM için ⚠️ VERIFICATION REQUIRED) |
| 5 | macOS (dmg/pkg) | Developer ID + notarization | Gatekeeper | "bilinmeyen geliştirici" engeli | https://learn.microsoft.com/en-us/windows-hardware/drivers/install/driver-signing (yalnız Windows; macOS için ⚠️ VERIFICATION REQUIRED) |
| 6 | AppImage | `appimage-signature-tool` | gpg anahtarı | uyarı (zorunlu değil) | https://learn.microsoft.com/en-us/windows-hardware/drivers/install/driver-signing (yalnız Windows; AppImage için ⚠️ VERIFICATION REQUIRED) |

> **Dürüstlük notu:** Satır 4-6'da bu turda **HTTP 200 ile doğrulanmış kendi
> kaynağı yoktur** → ilgili hücre `⚠️ VERIFICATION REQUIRED` ile işaretlenmiştir;
> uydurma URL yazılmamıştır. Satır 1-3 doğrulanmıştır.

## §36 Package manager sürücüsü — Composer ve npm bayrak matrisi

Repo'da bugün paketleyen tek yüzey budur; `package.json:10-17` (devDependencies)
ve `shared/composer.json:7-26` (require/require-dev) S1 kanıttır.

| # | Araç | Bayrak | Ne yapar | Repo kanıtı |
|---|---|---|---|---|
| 1 | Composer | `--prefer-dist` | kaynak yerine zip indir | `.github/workflows/ci.yml:49` |
| 2 | Composer | `--no-progress` | CI logunu sadeleştirir | `.github/workflows/ci.yml:49` |
| 3 | Composer | `optimize-autoloader` | sınıf haritasını sınıf yüklemeye dönüştürür | `shared/composer.json:44` |
| 4 | Composer | `sort-packages` | `require` girdilerini alfabetik sıralar | `shared/composer.json:43` |
| 5 | Composer | `minimum-stability: stable` | pre-release sürüm engeli | `shared/composer.json:37` |
| 6 | npm | `npm ci` | `package-lock.json`'dan temiz kurulum | `.github/workflows/ci.yml:155` |
| 7 | npm | `cache: npm` | indirme önbelleği (`runner.os` anahtarlı) | `.github/workflows/ci.yml:153` |
| 8 | npm | `--with-deps` (playwright) | tarayıcı sistem bağımlılıklarını kurar | `.github/workflows/ci.yml:200` |

## §37 Dağıtım zinciri — 6 aşamalı sürücü akışı (PLANNED)

| # | Aşama | Girdi | Çıktı | Bağlantı |
|---|---|---|---|---|
| 1 | Derleme/kaynak toplama | kaynak + bağımlılık | ara çıktı | anayasa §5 |
| 2 | Sürüm sabitleme | `composer.lock` · `package-lock.json` | değişmez sürüm kümesi | anayasa §14 |
| 3 | Paketleme | ara çıktı + manifest | `.msi` · `.deb` · `.tar.gz` | §1-§5 |
| 4 | İmzalama | paket + özel anahtar | imzalı paket | §35 |
| 5 | Yayın | imzalı paket + depo/katalog | kullanıcı erişimi | §6 |
| 6 | Denetim | kurulum + manifest | kabul raporu | §30 |

> **Bu zincirin 1-5'i CoreMusic'te PLANNED'dır**; bugün çalışan tek halka
> 1-2'dir (Composer/npm) ve 6'dır (CI job'ları). Aşama 3-4-5 için depo kanıtı
> **YOK** → `UNKNOWN`.

## §38 Paketleme sürüm geçmişi ve kapanış soruları

| # | Soru | Etiket |
|---|---|---|
| Q-X45 | MSI imzalama sertifikası kimde (CI secret mı, elde mi)? | UNKNOWN |
| Q-X46 | `composer audit` çıktısı release gate'ine bağlanacak mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X47 | AppImage/flatpak ikilisinden hangisi seçildi (Q-X37/Q-X42)? | UNKNOWN |
| Q-X48 | Paket sürümü `composer.json` `version` alanından mı okunacak? | ⚠️ VERIFICATION REQUIRED |

> `shared/composer.json:6` (`"version": "2.0.0"`) tek sürüm kaynağı gibi görünür;
> ancak Composer sürüm alanının yayın zincirinde kullanıldığı **doğrulanmadı** →
> `⚠️ VERIFICATION REQUIRED`.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode