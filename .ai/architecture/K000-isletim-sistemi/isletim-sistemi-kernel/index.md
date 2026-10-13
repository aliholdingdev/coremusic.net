---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: [cekirdek-yonetim, kesme-ve-senkronizasyon, surucu-ve-modul-modeli]
depends_on: []
evidence:
  - https://docs.kernel.org/core-api/index.html
  - https://man7.org/linux/man-pages/man2/syscalls.2.html
  - https://docs.kernel.org/security/index.html
  - https://man7.org/linux/man-pages/man2/seccomp.2.html
  - https://man7.org/linux/man-pages/man7/capabilities.7.html
  - https://docs.kernel.org/scheduler/index.html
validation: "2026-10-13 · 6 resmi URL HTTP 200 · children: 3 alt-dal disk kanıtlı (bu turda AÇILDI)"
open_questions:
  - "core ↔ kernel ayrım tanımı yok → ⚠️ VERIFICATION REQUIRED (Q2)"
  - "Hedef çekirdek (Linux mu, Windows kernel mi, XNU mu) → UNKNOWN (Q4)"
  - "Çekirdek sürümleri/spin off bilgisi (Linux fork) doğrulanmadı"
group: mimari
---

# isletim-sistemi-kernel — Kademe-3 Kayıt

**ELI15:** İşletim sisteminin **çalışan kısmı** — donanımı yöneten, süreçleri bölen, çağrıları karşılayan çekirdek. `core` kavramı *ne demek* derse, `kernel` *nasıl çalışır* der. **Kademe:** 3 · subdomain · **Master:** `os-master.md` §2 (katman 1) · §3.1-§3.5 · **Alt kademe:** `children: 3 alt-dal (bu turda AÇILDI — disk kanıtlı)` · **Durum:** `draft` (Q2 açık)

## §1 Çekirdeğin 5 işlevi (kanıtlı) ve alt-dal dağılımı

| # | İşlev | Ne yapar | Kaynak | Alt-dal dağılımı (bu turda) |
|---|---|---|---|---|
| 1 | **Sistem çağrılarını karşılar** | kullanıcı↔kernel tek geçiş | `syscalls(2)` ✅ | `kesme-ve-senkronizasyon` (trap) · `cekirdek-yonetim` (bağlam) |
| 2 | **Zamanlar** | CPU'yu süreçlere dağıtır | `scheduler/index.html` ✅ | `cekirdek-yonetim` (zamanlama mekanizması) |
| 3 | **Bellek ayırır** | her süreç kendi adres alanı | `mmap(2)` · Windows memory ✅ | `cekirdek-yonetim` (sayfa/buddy) |
| 4 | **Dosya sağlar** | VFS üzerinden tek kapı | `core-api/index.html` ✅ | `surucu-ve-modul-modeli` (arayüz) · `-api` (kullanıcı yüzeyi) |
| 5 | **Güvenliği uygular** | yetki, filtre, politika | `capabilities(7)` · `seccomp(2)` ✅ | `surucu-ve-modul-modeli` (yetki/ioctl) · `-security` (politika) |

> Q2 (`core` ↔ `kernel` sınırı) bu dağılımın **dışındadır** — bkz. §3 ve `-kapsam.md` §E.

## §2 Platform çekirdekleri

| Platform | Çekirdek ailesi | Kaynak | Durum |
|---|---|---|---|
| Linux | Linux çekirdeği | https://www.kernel.org/ | ✅ HTTP 200 |
| Windows | Windows kernel (kernel-mode) | https://learn.microsoft.com/en-us/windows/hardware/drivers/kernel/ | ✅ HTTP 200 |
| macOS | Apple Kernel API | https://developer.apple.com/documentation/kernel | ✅ HTTP 200 |

⚠️ **CoreMusic'in hedef aldığı çekirdek bilinmiyor** (Q4) — CI yalnız `ubuntu-latest` gösterir (test platformu).

## §3 `core` ↔ `kernel` sınırı — TANIMSIZ (Q2)

| # | Soru | Durum |
|---|---|---|
| 1 | `core` = kavramlar, `kernel` = mekanizma mı? | ⚠️ VERIFICATION REQUIRED |
| 2 | Zamanlama neden `kernel`'de, bellek neden `core`'da? | ⚠️ VERIFICATION REQUIRED |
| 3 | v2 §2.1 iki ayrı child verir, sınır tanımı vermez | ⚠️ VERIFICATION REQUIRED |

> Her iki dal da **kapsam genişletmez**. `core` bu turda 4 alt-dala bölünüyor (süreç/zamanlama · bellek · dosya sistemi · izin); sınır `-core` tarafındaki dosyada referanslanır — burada **tekrar edilmez** (çakışma = 0).

## §4 Repo'daki karşılığı (dolaylı erişim)

| # | Çekirdek işlevi | Repo örneği | Dolaylı mı? |
|---|---|---|---|
| 1 | syscall karşılar | `AIEngine.php:290-295` (`shell_exec`) | ✅ dolaylı |
| 2 | süreç yaratır | `ApiKeyCreateCommandTest.php:253-258` (`proc_open`) | ✅ dolaylı |
| 3 | dosya/izin | `SessionConfig.php:33-36` (`mkdir 0750`) | ✅ dolaylı |
| 4 | yol çözümleme | `MusicStreamHandler.php:185-208` (`realpath`) | ✅ dolaylı |
| 5 | güvenlik filtresi | `.htaccess` başlıkları (uygulama düzeyi, **çekirdek değil**) | ❌ ayrım |

> **Kritik ayrım:** CoreMusic çekirdeği **asla doğrudan çağırmaz** — her şey PHP → libc → syscall zincirinden geçer (`os-master.md` §2).

## §5 Doğrulanmış kaynaklar (2026-10-12)

6 resmi URL (core-api · syscalls.2 · security · seccomp.2 · capabilities.7 · scheduler) — hepsi **HTTP 200**, frontmatter `evidence` alanındadır. Bu turda eklenen **yeni** URL'ler (envanterle kesişim 0): `-dogrulama.md` §E.

## §6 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-K1 | `core` ↔ `kernel` sınırı (Q2)? | ⚠️ VERIFICATION REQUIRED |
| Q-K2 | Hedef çekirdek hangisi (Q4)? | UNKNOWN |
| Q-K3 | Zamanlama/bellek kavramları hangi dalda? | ✅ bu turda kapandı: `cekirdek-yonetim` (yalnız mekanizma) |

**İlişkili:** `isletim-sistemi-kernel-{kapsam,dogrulama,ornekler}.md` · `cekirdek-yonetim/index.md` · `kesme-ve-senkronizasyon/index.md` · `surucu-ve-modul-modeli/index.md` · `../os-master.md` §3 · `../isletim-sistemi-core/index.md` (Q2 komşusu)

## §GRUPLAR — 9. alan `group` (ADR-097 §5.5 · registry §8.1)

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `isletim-sistemi-kernel-kapsam.md` | Kapsam sınırı, alt-dal envanteri, komşular | Kademe-3 |
| kod | `isletim-sistemi-kernel-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-3 |
| kanit | `isletim-sistemi-kernel-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-3 |

> Alt-dal (Kademe-4) grupları kendi `index.md`'lerinde tanımlıdır: `cekirdek-yonetim/` · `kesme-ve-senkronizasyon/` · `surucu-ve-modul-modeli/` — burada **tekrar edilmez** (çakışma = 0).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
