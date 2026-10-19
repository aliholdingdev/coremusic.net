---
node_id: izin-ve-guvenlik-kavramlari
node_type: subdomain_index
status: draft
parent_id: isletim-sistemi-core
children:
  - izin-ve-guvenlik-kavramlari-anayasa
  - izin-ve-guvenlik-kavramlari-surucu
  - izin-ve-guvenlik-kavramlari-ornekler
depends_on:
  - dosya-sistemi-kavramlari
  - isletim-sistemi-core-kapsam
evidence: "man7.org/credentials.7 + capabilities.7 + acl.5 + setuid.2 + setreuid.2 + setresuid.2 + setgid.2 + chown.2 + chmod.2 + fchmod.2 + fchmodat.2 + umask.2 + access.2 + getfacl.1 + getpwuid.3 + pathconf.3 + user_namespaces.7 (envanter reuse, HTTP 200 2026-10-13); man7.org/setcap.8 + getcap.8 + openat.2 (YENİ, HTTP 200 doğrulandı 2026-10-13); OG chmod/setuid/fchmod/chown/pathconf.htm (envanter reuse); repo: PermissionMiddleware.php:47-63, FileHandler.php:169-181, AuthGuard.php:34-48, MusicStreamHandler.php:173-209, bin/api-key-create.php:487-493, SessionConfig.php:35, home.coremusic.net/.htaccess:18-37, home.coremusic.net/config/constants.php:75-78 (kendi grep/Read 2026-10-13)"
validation: "7 doğrulama testi (İ-1..İ-7, isletim-sistemi-core-dogrulama.md §A.4) PLANNED; ölçülmüş metrik YOK (UNKNOWN); repo kanıtları dosya:satır ile 2026-10-13 grep/Read ile teyit edildi; bu index + surucu + ornekler 2026-10-13 yazıldı"
open_questions:
  - "Q-I1: PHP'de uid/yetki kavramı yok — POSIX kimlik modeli repo'da oturum + rol (RBAC) ile EMÜLE edilir; işletim sistemi yetki modeli ile uygulama yetki modeli arasındaki sınır vault'ta net değil (kapsam: izin kasası hangi katmanın?)"
  - "Q-I2: mod geçiş hesapları (mkdir 0775/0750/0777/0700) kaynak kod sabiti — dosya-sistemi kasasına ait, pratikte bu daldaki Q-F5 ile kesişir"
  - "Q-I3: ACL/getfacl uygulaması repo'da YOK (grep: getfacl/setfacl/posix_acl = 0) — vault bölümü PLANNED, uygulama kanıtı yok"
  - "Q-I4: capability (setcap/getcap) uygulaması repo'da YOK (grep: setcap = 0) — yalnız Linux çekirdek modeli anlatılıyor, uygulama kanıtı yok (PLANNED)"
  - "Q-I5: O_NOFOLLOW repo'da YOK (grep = 0) — symlink kaçış savunmasında yalnızca realpath()+prefix denetimi var; TOCTOU yüzeyi açık"
  - "Q-I6: 'erişim reddi log'u VAR (FileHandler.php:169-181 authz_denied → warning; PermissionMiddleware.php:47-50 çağırıyor) — anayasa dosyasındaki 'log yok' cümlesi disk ile ÇELİŞİYOR, düzeltme anayasa dosyasında (bu görevde düzenlenmedi)"
  - "Q2: core↔kernel sınırı (kapsam §A.8: salt kavramsal) — AÇIK, ⚠️ VERIFICATION REQUIRED"
group: subdomain
---

# index (izin-ve-guvenlik-kavramlari) — İzin ve Güvenlik Kasası Alt Dizin Kataloğu (Kademe-3/4)

## §0 ELI15

Bu klasör, **"bir dosyaya kim, nasıl erişebilir?"** sorusunun cevap kasasıdır: POSIX kimlik modeli (UID/GID/umask), izin bitleri (rwx), ACL, capability, setuid ailesi ve symlink kaçış denetimi. Repoda gerçek karşılığı **uygulama katmanı yetkilendirmesi**dir: oturum (AuthGuard), rol denetimi (PermissionMiddleware, `authz_denied`), kök-dizin REALPATH kilidi (MusicStreamHandler:173-209) ve sunucu katmanı reddi (.htaccess `Require all denied`) — POSIX yetki primitive'leri repoda **kodlanmaz** (grep: chmod/chown/setuid/getfacl/setcap = 0; negatif kanıt, 2026-10-13).

## §1 Amaç ve Kapsam

- **Kapsam:** kasadaki 3 içerik dosyası — anayasa (model), surucu (akış + hata + test), ornekler (repo örnekleri).
- **Sınır:** syscall yüzeyi → `-api` dalı; denetim uygulaması → `-security` dalı; `core↔kernel` (Q2) → **⚠️ VERIFICATION REQUIRED**, kapsam genişletilmez.
- **URL politikası:** envanter reuse + 4 YENİ URL (aşağıda), hepsi HTTP 200 ile doğrulandı (2026-10-13).

## §2 Kademe Etiketi (etiket çakışması — iki konvansiyon)

| Kaynak | Bu dalın kademesi |
|---|---|
| Kök `isletim-sistemi-core/index.md` §2/§4 (alt-alan indeksi) | Kademe-3 (içerik = Kademe-4) |
| Bellek şablonu + kardeş dalların H1'i (alt-alan içeriği) | Kademe-4 (içerik = Kademe-5) |

Bu index H1'inde **(Kademe-3)**, içerik dosyalarının H1'inde **(Kademe-5)** kullanıldı — iki konvansiyon da kayıtta; çakışma Q-F1 ile taşınıyor.

## §3 Envanter

| Dosya | Satır | Not |
|---|---|---|
| `index.md` (bu dosya) | 40–100 hedef | 2026-10-13 yazıldı |
| `izin-ve-guvenlik-kavramlari-anayasa.md` | 338 | 2026-10-13 21:14 (öncesi) — **⚠️ 380 eşiğinin altında (eksik)**, kanıt/teyit aşaması |
| `izin-ve-guvenlik-kavramlari-surucu.md` | 380–460 hedef | 2026-10-13 yazıldı |
| `izin-ve-guvenlik-kavramlari-ornekler.md` | 380–460 hedef | 2026-10-13 yazıldı |

## §4 Gruplar ve URL Kaynakları

| Kaynak grubü | URL'ler | Durum |
|---|---|---|
| Kimlik/credential | credentials.7, getpwuid.3 | envanter reuse |
| Mod/chmod ailesi | chmod.2, fchmod.2, fchmodat.2, umask.2, access.2, pathconf.3 | envanter reuse |
| Yetki değişim | setuid.2, setreuid.2, setgid.2, chown.2 | envanter reuse |
| ACL/capability | acl.5, capabilities.7, user_namespaces.7, getfacl.1 | envanter reuse |
| Open Group | chmod, setuid, fchmod, chown, pathconf `.htm` | envanter reuse |
| **Yeni (2026-10-13, HTTP 200)** | `man7.org/linux/man-pages/man2/setresuid.2.html`, `man7.org/linux/man-pages/man8/setcap.8.html`, `man7.org/linux/man-pages/man8/getcap.8.html`, `man7.org/linux/man-pages/man2/openat.2.html` | **4 yeni** — dağılım: surucu → setresuid.2 + getcap.8 + openat.2; ornekler → setcap.8 |

404 bilinen ve asla kullanılmayanlar: `acl.7.html` (doğrusu `acl.5`), `realpath.7.html` (doğrusu `realpath(3)`), `docs.kernel.org/filesystems/posix-acls.html`.

## §5 Komşu Sınırı

| Komşu | Ne devredilir | Ne devredilmez |
|---|---|---|
| `dosya-sistemi-kavramlari` | path_resolution, fd, symlink kaçış zinciri (Q-F1..Q-F5) | izin biti/hesabı (bu dal) |
| `bellek-kavramlari` | oturum belleği, session id | oturum *yetki* kararı (bu dal) |
| `-api` | syscall/yüzey (openat, setresuid syscall listesi) | kasada yorum |
| `-security` | denetim uygulaması (pentest, log politikası) | model anlatımı |

## §6 depends_on

- `dosya-sistemi-kavramlari` (realpath/is_link zinciri bu daldan okunur)
- `isletim-sistemi-core-kapsam` (§A.8 salt-kavramsal sınır, §G.1 credential URL'leri)
- `isletim-sistemi-core-dogrulama` (§A.4 İ-1..İ-7 testleri, §E.4 URL envanteri)

## §7 Açık Sorular

Q-I1 (uid emülasyonu sınırı) · Q-I2 (mod geçişleri = Q-F5 kesişimi) · Q-I3 (ACL uygulaması YOK) · Q-I4 (capability uygulaması YOK) · Q-I5 (O_NOFOLLOW YOK → TOCTOU) · **Q-I6 (anayasa dosyasındaki 'log yok' ifadesi disk ile çelişiyor — FileHandler.php:169-181)** · Q2 (core↔kernel, **⚠️ VERIFICATION REQUIRED**) · Q-F1 (kademe etiketi çakışması). Tam metin: `izin-ve-guvenlik-kavramlari-anayasa.md` §6/§7 + bu dosya FM `open_questions`.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
