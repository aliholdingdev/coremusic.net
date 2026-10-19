---
node_id: K000-isletim-sistemi/isletim-sistemi-api
node_type: domain
status: draft
parent_id: K000-isletim-sistemi
children:
  - K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari
  - K000-isletim-sistemi/isletim-sistemi-api/cihaz-erisimi
  - K000-isletim-sistemi/isletim-sistemi-api/platform-kutuphaneleri
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/syscalls.2.html
  - https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/unistd.h.html
  - https://www.gnu.org/software/libc/manual/html_node/Program-Components.html
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §3"
validation: "2026-10-13 · 3 yeni alt-dal açıldı · §GRUPLAR 3 satır Kademe-2 · children 3 alt-dal · index 107 → 100 (künye tablosu FM'e de-duplike edildi) | 2026-10-14 · isletim-sistemi-api-kod-seti.md yazıldı (607 satır · 13 kod bloğu · 12 resmi URL HTTP 200 · spot-check: orkestratör)"
open_questions:
  - "Q-P1 Hangi API yüzeyi kastediliyor — kernel API mi, kullanıcı uzay hizmetleri mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-P5 Mevcut syscall/ag-api/platform-api dizinleri bu 3 alt-dalın parçası mı, ayrı yapı mı? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# isletim-sistemi-api — Domain Künyesi (Kademe-2)

**ELI15:** İşletim sisteminin uygulamalara açtığı **sistem arayüzü** — syscall yüzeyi,
sistem kütüphaneleri, cihaz erişim API'si ve platform API'leri. HTTP/REST API
(K011/K012) bu dalın **kapsam dışındadır**.

> Künye (node_id · node_type · parent_id · children · status): FM alanları **tek
> kaynaktır**; ayrı künye tablosu kaldırıldı (dedup, 2026-10-13). Kademe: 2.

## §1 Amaç

Uygulamanın işletim sistemiyle kurduğu **düşük seviyeli sözleşmeyi** belgelemek:
bir program kaynak/dosya/cihaz/zaman talep ettiğinde bu talebin geçtiği arayüz
katmanları, hata modeli ve taşınabilirlik sınırları. Çekirdek içi yürütme
(preemption, zamanlama), merkez OS servisleri ve uygulama kodu bu dalın dışıdır.

## §2 Kademe ve rol

| Katman | Kademe | Bu dalın rolü |
|---|---|---|
| syscall yüzeyi | 3 (sistem-cagrilari) | kullanıcı↔çekirdek geçişinin ham sözleşmesi |
| cihaz erişim API'si | 3 (cihaz-erisimi) | dosya tanıtıcı ve ioctl üzerinden cihaz erişimi |
| sistem/platform kütüphaneleri | 3 (platform-kutuphaneleri) | libc/libSystem/Win32 soyutlama katmanı |

> Çoğu uygulama syscall'ı **asla doğrudan çağırmaz**; kütüphane sargısı (wrapper) üzerinden çalışır (`os-master.md` §2 katman 3).

## §3 Alt-dal envanteri (3)

| # | Alt-dal | node_type | Odak |
|---|---|---|---|
| 1 | `sistem-cagrilari/` | subdomain | syscall numaralandırma, dönüş değeri/hata, uyumluluk |
| 2 | `cihaz-erisimi/` | subdomain | `/dev`, fd, ioctl, hotplug, yetki kontrolü, mmap |
| 3 | `platform-kutuphaneleri/` | subdomain | libc/libSystem/Win32, soyutlama, taşınabilirlik, sürüm uyumluluğu |

## §4 Komşu sınırı

| Dışında olan | Nereye gider |
|---|---|
| HTTP/REST API, endpoint, middleware | K011-application · K012-api |
| Çekirdek içi yürütme, zamanlama, preemption | `isletim-sistemi-kernel` |
| Merkez OS servisleri | `isletim-sistemi-core` |
| Ses uç noktası akış davranışı | `isletim-sistemi-audio` |
| CoreMusic uygulama kodunun kendisi | K001+ (`os-master.md` §2) |

## §5 depends_on

| Bağımlılık | Tür |
|---|---|
| `../os-master.md` | master kayıt (§2 katman · §3 alan) |
| `../os-bagimliliklar.md` | alan bağımlılık haritası |
| POSIX.1-2024 · Linux man-pages · Win32 API | dış standart (kaynakça alt-dallarda) |

## §6 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-P1 · Q-P5 | FM `open_questions` alanında (bu dosyada tekrarlanmadı) | ⚠️ VERIFICATION REQUIRED |
| Q-P3 | Hedef platform API seti — syscall seviyesi mi, kütüphane API'si mi? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `isletim-sistemi-api-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kanit | `isletim-sistemi-api-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| kod | `isletim-sistemi-api-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kod | `isletim-sistemi-api-kod-seti.md` | Cross-platform open/read köprüsü, hata kodu deseni ve FFI sınırı — 607 satır · 13 kod bloğu · 12 resmi URL | Kademe-2 |

## §7 İlişkili dosyalar

`isletim-sistemi-api-{kapsam,dogrulama,ornekler}.md` · üç alt-dal `index.md` ·
`../os-master.md` · `../os-dogrulama.md` — §GRUPLAR + §3'teki listeler esastır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
