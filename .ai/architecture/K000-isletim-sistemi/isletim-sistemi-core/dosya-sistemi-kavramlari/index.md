---
node_id: K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core
children:
  - K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-anayasa
  - K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-surucu
  - K000-isletim-sistemi/isletim-sistemi-core/dosya-sistemi-kavramlari/dosya-sistemi-kavramlari-ornekler
depends_on: [K000-isletim-sistemi/isletim-sistemi-core]
evidence: ["https://man7.org/linux/man-pages/man7/path_resolution.7.html", "https://man7.org/linux/man-pages/man7/inode.7.html", "home.coremusic.net/include/Stream/MusicStreamHandler.php:189"]
validation: "2026-10-13 · 3 içerik dosyası diskte (ReadAllLines: 382 / 385 / 382 satır) · 2 URL envanter reuse (-dogrulama §E.3 · 2026-10-13 HTTP 200) · repo satırı bu oturumda Read ile birebir · yeni URL yok"
open_questions:
  - "Q-F1 kademe etiketi (⚠️) · Q-F2 LOCK_EX primitive (⚠️) · Q-F3 dosya sistemi türü (UNKNOWN) · Q-F4 RLIMIT_NOFILE (UNKNOWN-PLANNED) · Q-F5 dosya adı uyuşmazlığı (⚠️) · Q2 core↔kernel (⚠️) → tam metin §7"
group: mimari
---

# dosya-sistemi-kavramlari — Deep-Dal İndeksi (Kademe-3)

## §0 ELI15

"Dosyalara erişim kuralları ne?" sorusunun cevabı: işletim sistemi her yola **tek
bir kapıdan** bakar (VFS), her dosyayı bir **inode** ile tanır, her açılışı bir
**dosya tanıtıcısına** (fd) bağlar, yol çözümlemede **symlink / `..`** kaçışını
denetler ve eşzamanlı yazımda **kilit** kullanır. Bu dal üç dosyada taşır: tanım
(`-anayasa`), mekanizma/hata modu (`-surucu`), repodaki gerçek yüzey (`-ornekler`).
Kimlik/izin (UID-GID · mod · ACL · capability) komşu `izin-ve-guvenlik-kavramlari` dalındadır.

## §1 Amaç

| # | Amaç | Gittiği dosya |
|---|---|---|
| 1 | VFS · inode/dizin/kök · fd · yol çözümleme · symlink · kilit · fsync · link sayısı · mount kavramlarını bağlayıcı tanıma bağlamak | `-anayasa` §2 (M1–M9) |
| 2 | Yol çözümleme · kaçış denetimi · kilit kazanma · `umask`→mode→ACL · kalıcılık akışlarını adım adım belgelemek | `-surucu` §2 (A1–A6) |
| 3 | Repodaki dosya sistemi yüzeyini (realpath zinciri · `LOCK_EX` · `@mkdir` modları · `.htaccess`) satır kanıtıyla göstermek | `-ornekler` §1–§4 |

URL kanıtı: `path_resolution(7)` + `inode(7)` — envanter reuse (`-dogrulama` §E.3), 2026-10-13 HTTP 200; bu dosyada **yeni URL yok**.

## §2 Kademe ve rol

| # | Kademe (kök §2/§4) | Kademe (kardeş şablon) | Dosya | Rol |
|---|---|---|---|---|
| 1 | Kademe-3 | Kademe-4 | `dosya-sistemi-kavramlari/index.md` | bu dalın künyesi (bu dosya) |
| 2 | Kademe-4 | Kademe-5 | `-anayasa.md` | bağlayıcı tanımlar, ilke ve eşikler (§0–§9) |
| 3 | Kademe-4 | Kademe-5 | `-surucu.md` | akış · hata modu · test · ölçüm · repo kanıtı (§1–§7) |
| 4 | Kademe-4 | Kademe-5 | `-ornekler.md` | çalışan örnekler, olumsuz kanıt, anti-pattern (§1–§6) |

İki sütun **çelişir ve bilinçlidir** (kök `index.md` §2–§4 vs kardeş
`bellek-kavramlari/index.md` H1/§2 + var olan içerik H1'leri) → Q-F1 (⚠️), düzeltme yetkisi bu dosyada değil.

## §3 Envanter

| # | Dosya | Kapsadığı kavramlar | Disk (satır) |
|---|---|---|---|
| 1 | `-anayasa.md` | VFS · inode/dizin/kök · fd · yol çözümleme · symlink/kaçış · kilit (flock/POSIX/bayt) · fsync · link sayısı · mount | 382 (2026-10-13) |
| 2 | `-surucu.md` | 10 akış · hata modları · test stratejisi (D-1..D-7) · ölçüm · repo kanıtı (mod çelişkisi · kilit · realpath) | 385 (2026-10-13) |
| 3 | `-ornekler.md` | repo örnekleri · olumsuz kanıt (grep) · anti-pattern · örnek katalog · test taslakları | 382 (2026-10-13) |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `dosya-sistemi-kavramlari/index.md` | alt-dal künyesi | Kademe-3 (kök §4) |
| mimari | `dosya-sistemi-kavramlari-anayasa.md` | bağlayıcı tanım ve ilke seti | Kademe-4/5 (Q-F1) |
| mimari | `dosya-sistemi-kavramlari-surucu.md` | mekanizma envanteri ve hata modları | Kademe-4/5 (Q-F1) |
| kod | `dosya-sistemi-kavramlari-ornekler.md` | repo örnekleri ve test taslakları | Kademe-4/5 (Q-F1) |

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| Dosya sistemi syscall yüzeyi (`open` · `read` · `dup` · `fcntl` imzaları) | `isletim-sistemi-api` | kavram + bayrak **anlamı** kalır |
| Kilit/izin denetiminin **uygulanması** (politika, ACL dağıtımı) | `isletim-sistemi-security` | kilit **tanımı** kalır, politika gitmez |
| VFS/inode çekirdek içi veri yapısı, journal, dcache, IO scheduler | `isletim-sistemi-kernel` | katman **tanımı** kalır, kod gitmez |
| Dosya sistemi türü karşılaştırması (ext4 vs NTFS vs NFS) | `isletim-sistemi-turu` | örnek düzeyinde |
| `fsync` / kilit test stratejisi → `isletim-sistemi-testing` · log okuma (`/proc`) → `isletim-sistemi-observability` · disk blok/`mmap` → `K001-donanım` · `bellek-kavramlari` | bu dal sınırları | yalnız senaryo/kavram düzeyinde |
| Kimlik (UID/GID) · mod(rwx) · ACL · capability · realpath denetimi | `izin-ve-guvenlik-kavramlari` (kardeş dal) | erişim **kapısı** burada, **yetkisi** orada |

`core` ↔ `kernel` sınır tanımı **yok** (Q2): iki dal da kapsam genişletmez →
`-kapsam.md` §A.4 · kök `index.md` §5. Bu indeks bu sınırı **tanımlamaz**.

## §6 depends_on

`depends_on: [K000-isletim-sistemi/isletim-sistemi-core]` — üst düğümün §3
envanterindeki 3. satır ("VFS · yol · dosya tanıtıcısı · kilit") bu dalın
konusudur (kök `index.md` §3 · `-kapsam.md` §A.8 madde 3 · §B.3).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-F1 | Kademe etiketi: kök §2/§4 (Kademe-3/4) mi, kardeş şablon (Kademe-4/5) mi? | ⚠️ VERIFICATION REQUIRED |
| Q-F2 | PHP `LOCK_EX` → üretimde hangi primitive (`flock` / `fcntl` / platform bağımlı)? | ⚠️ VERIFICATION REQUIRED |
| Q-F3 | Hedef üretim dosya sistemi türü (ext4 / NTFS / NFS) — `rename` ve kilit davranışı buna bağlı | UNKNOWN |
| Q-F4 | Üretimde açık dosya tanıtıcısı tavanı (`RLIMIT_NOFILE`) | UNKNOWN — ölçüm PLANNED |
| Q-F5 | `kapsam.md` §B.3 dosya adları ile diskteki `-surucu`/`-ornekler` adlandırması hangisi geçerli? | ⚠️ VERIFICATION REQUIRED |
| Q2 | `core` ↔ `kernel` sınır tanımı (kök `index.md` §7 Q-C1)? | ⚠️ VERIFICATION REQUIRED |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
