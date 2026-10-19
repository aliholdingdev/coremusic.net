---
node_id: K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api
children:
  - K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-anayasa
  - K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-surucu
  - K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-ornekler
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api
evidence:
  - https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/unistd.h.html
  - https://www.gnu.org/software/libc/manual/html_node/Program-Components.html
  - https://www.kernel.org/doc/html/latest/core-api/kernel-api.html
validation: "2026-10-13 · 3 içerik dosyası · omurga §0-§9 · group mimari/kod"
open_questions:
  - "Q-S1 Hedef mimari (x86_64/arm64) syscall numarası aralığı netleşmedi → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# sistem-cagrilari — Alt-dal Künyesi (Kademe-3)

**ELI15:** Bir program işletim sisteminden bir iş istediğinde (dosya aç, bellek ayır,
zamanı oku), bu isteğin **ham sözleşmesi** sistem çağrılarıdır. Uygulama bu çağrıyı
genellikle doğrudan yapmaz; kütüphane sargısı (wrapper) üzerinden yapar. Bu alt-dal
o sözleşmenin numaralandırmasını, dönüş değerini ve hata modelini belgeler.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari |
| node_type | subdomain |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api |
| Kademe | 3 |
| children | 3 dosya (anayasa · surucu · ornekler) |
| Durum | draft |

## §1 Amaç

Kullanıcı uzay ile çekirdek arasındaki **geçiş sözleşmesini** tanımlamak: sistem
çağrısı numarası, argüman kaydı düzeni, dönüş değeri, hata (errno) modeli ve
mimari uyumluluk sınırları. Amaç çekirdek içi mekanizmayı değil, uygulamanın
gördüğü **arayüz**ü belgelemektir.

## §2 Kademe / rol

| Katman | Bu alt-dalın rolü |
|---|---|
| Ham syscall | numara + register düzeni + dönüş/hata |
| Kütüphane sargısı | libc'nin syscall'ı sarmaladığı fonksiyon |
| Platform API'si | Windows/POSIX başlık tabanlı yüzey (bkz. `platform-kutuphaneleri/`) |

> Çoğu kod yalnızca 2. ve 3. katmanı kullanır. Doğrudan syscall çağrısı ancak
> taşınabilirlikten vazgeçildiğinde yapılır.

## §3 Dosya envanteri (3)

| # | Dosya | Odak |
|---|---|---|
| 1 | `sistem-cagrilari-anayasa.md` | Kavram modeli, mekanizma akışı, ölçüm/eşik |
| 2 | `sistem-cagrilari-surucu.md` | Uygulama katmanı, akış, hata modları, test stratejisi |
| 3 | `sistem-cagrilari-ornekler.md` | Çalışan yapılandırmalar, kod örnekleri, anti-pattern |

## §4 Komşu sınırı

| Dışında olan | Nereye gider |
|---|---|
| Cihaz tanıtıcı erişimi, ioctl, mmap | `cihaz-erisimi/` |
| libc/libSystem/Win32 soyutlama katmanı | `platform-kutuphaneleri/` |
| Çekirdek içi dispatcher, sys_call_table | `../isletim-sistemi-kernel` |
| HTTP/REST yüzeyi | K011 · K012 |

## §5 depends_on

| Bağımlılık | Tür |
|---|---|
| `../index.md` (isletim-sistemi-api) | üst domain |
| `../os-master.md` §2 · §3 | katman ve alan tanımı |
| POSIX.1-2024 `unistd.h` | dış standart (syscall numarası kaynağı) |

## §6 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-S1 | Hedef mimari (x86_64/arm64) syscall numarası aralığı | ⚠️ VERIFICATION REQUIRED |
| Q-S2 | Uygulama hangi sargıyı kullanıyor — glibc mü, platform API'si mi? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `sistem-cagrilari-anayasa.md` | Kavram modeli ve mekanizma akışı | Kademe-4 |
| mimari | `sistem-cagrilari-surucu.md` | Uygulama katmanı, hata modları, test | Kademe-4 |
| kod | `sistem-cagrilari-ornekler.md` | Çalışan yapılandırma ve kod örnekleri | Kademe-4 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
