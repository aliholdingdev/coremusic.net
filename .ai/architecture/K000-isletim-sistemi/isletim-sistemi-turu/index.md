---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/index
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children:
  - index.md
  - isletim-sistemi-turu-kapsam.md
  - isletim-sistemi-turu-dogrulama.md
  - isletim-sistemi-turu-ornekler.md
  - isletim-sistemi-turu-kod-seti.md
  - android
  - ios
  - linux
  - macos
  - reactos
  - tizen
  - webos
  - windows
depends_on:
  - isletim-sistemi-cross
  - isletim-sistemi-runtime
evidence:
  - "https://developer.android.com/"
  - "https://www.tron.org/rtos/rtos01/"
  - "https://docs.kernel.org/"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/"
  - "os-master.md §3 satır 57, 59-61"
validation: "web: developer.android.com, tron.org/rtos/rtos01, docs.kernel.org, POSIX.1-2024 yanıt kodları 2026-10-12 (HTTP 200). osek-vdx.org/mirror/os221.pdf → 403 (erişim engeli, bkz. os-dogrulama ölü-URL tablosu). disk: os-master.md §3 satırları okundu; alt dizinler windows/linux/macos (disk enumerasyonu). 2026-10-14: kök'e isletim-sistemi-turu-kod-seti.md (760 satır) + 8 platforma <os>-kod-seti.md eklendi (toplam +3.695 satır) → yeniden sayım 74 dosya / 34.846 satır (ReadAllLines; boş satırları dahil — Measure-Object -Line boş satır attığı için eski 938/28.639 iddiaları düzeltildi)."
open_questions:
  - "Q5: turu ↔ K002-altyapi boşluğu — tür seçimi ile altyapı katmanı arasındaki kopukluk v2'de yazılmamış. [⚠️ VERIFICATION REQUIRED]"
  - "T-U1: turu ↔ cross sınırı v2'de yazılmamış — tahmin YASAK. [⚠️ VERIFICATION REQUIRED]"
  - "T-U2: Hangi sınıflandırma şeması benimsenecek (masaüstü/gömülü/mobil mi, genel amaçlı/gerçek zamanlı mı)? [UNKNOWN]"
  - "T-U3: Hedef OS türü ve platform seti? (Q4 ile ilişkili) [UNKNOWN]"
group: mimari
---

# isletim-sistemi-turu

> **Kapsam (tek cümle):** İşletim sistemi **tür/tip sınıflandırması** dalı — CoreMusic'in üstünde çalışacağı zeminin hangi türden olduğunun kararlaştırıldığı yer; sınıf belli olunca gereksinimler (gerçek zamanlılık, güç, bellek) de belli olur.

## Durum özeti

Seçilmiş sınıflandırma şeması **yok** (T-U2 UNKNOWN), hedef tür **bilinmiyor** (T-U3/Q4 UNKNOWN). Bu yüzden kapsam dosyası yalnız ölçütleri ve bilinen sınır notlarını taşır; "hedefimiz X tür OS" cümlesi **yazılmaz**. Altında sekiz platform alt-dizini (`android`, `ios`, `linux`, `macos`, `reactos`, `tizen`, `webos`, `windows`) diskte mevcuttur (ölçüm 2026-10-13, §PLATFORMLAR) — derinlemesine içerik dosyaları bu turda **dokunulmaz**, yalnız platform `index.md'leri zenginleştirilir. **2026-10-14 eklemesi:** kök `isletim-sistemi-turu-kod-seti.md` eklendi — gruplu cross-platform kod seti (8 grup · 10 dil · 760 satır · üretim Linux, kod Windows/macOS dahil tüm sınıflara taşınabilir). **Aynı gün:** 8 platform alt-dizininde birer `<os>-kod-seti.md` eklendi (android · ios · macos · tizen · webos · reactos · windows · linux) — her biri taşınabilir dil çekirdeğine (Node/Python/Go/Rust/PHP/C#) platform-özel yüzey (mobil native · TPK/launchd · IPK/Luna · Win32) ekler; kök dosyaya `depends_on` ile bağlıdır. 6/8 dosya <500 satır (kod-ağırlıklı dosyalar — ≥500 hedefi bilinçli gevşetildi, bkz. §PLATFORMLAR notu).

## Dosya indeksi

| Dosya | Ne içerir |
|---|---|
| [`isletim-sistemi-turu-kapsam.md`](isletim-sistemi-turu-kapsam.md) | Rol, sınır (içinde/dışında/komşu), ayrım noktaları U1–U5, bağımlılık grafiği, olgunluk matrisi |
| [`isletim-sistemi-turu-dogrulama.md`](isletim-sistemi-turu-dogrulama.md) | Web (≥5) + disk kanıtları, 403 engelli URL kaydı, web doğrulama matrisi |
| [`isletim-sistemi-turu-ornekler.md`](isletim-sistemi-turu-ornekler.md) | Gerçek repo kodu: tür/ipucu kanıtları (`php_uname`, hedef platform notu) |
| [`isletim-sistemi-turu-kod-seti.md`](isletim-sistemi-turu-kod-seti.md) | Gruplu cross-platform kod seti — 8 grup (platform tespiti · yol · süreç · dış araç · kilit · ortam · kabuk · CI) × 10 dil (PHP/JS/Python/Go/Rust/C#/C/C++/Bash/PowerShell) · Linux birincil |
| `android/` · `ios/` · `linux/` · `macos/` · `reactos/` · `tizen/` · `webos/` · `windows/` | 8 platform alt-dizini — her birinde `index.md` + içerik dosyaları + **2026-10-14 eklenen `<os>-kod-seti.md`** (taşınabilir dil çekirdeği + platform-özel yüzey); ayrıntı §PLATFORMLAR tablosunda |

## §PLATFORMLAR

> Disk ölçümü 2026-10-14 · `[System.IO.File]::ReadAllLines` satır sayımı (boş satırları dahil eder — `Measure-Object -Line` boş satırları atldığı için eski "938/28.639" tarzı iddialar bu yöntemle düzeltildi). `≥500` sütunu = içerik dosyalarının 500 satır hedefini tutturanı / toplam içerik dosyası (`index.md` bu hedefe tabi değildir). **Not:** 8 yeni `<os>-kod-seti.md`'den 6'sı kod-ağırlıklı olduğundan <500 satırdır (tizen 620 ve webos 561 hariç) — ≥500 hedefi bu dosyalar için bilinçli gevşetildi.

| Platform | Dizin | Dosya | Satır | ≥500 | Durum | Not |
|---|---|---:|---:|---|---|---|
| android | `android/` | 8 | 3.728 | 6/8 | ✅ tam | `android-kod-seti.md` + **`android-dma-ve-interrupt.md`** (2026-10-14) eklendi — dizinde `index.md` YOK (bkz. §GRUPLAR notu), DMA dosyası kök bu tabloya kayıtlıdır |
| ios | `ios/` | 9 | 3.680 | 6/8 | ✅ tam | Apple mobil · `ios-kod-seti.md` + **`ios-dma-ve-interrupt.md`** eklendi |
| linux | `linux/` | 13 | 6.298 | 11/12 | ✅ tam | En derin dal · `linux-kod-seti.md` (üretim kontratı); DMA dosyası (2026-10-12) mevcut |
| macos | `macos/` | 8 | 3.275 | 5/7 | ✅ tam | Darwin/XNU · `macos-kod-seti.md` + **`macos-dma-ve-interrupt.md`** eklendi |
| reactos | `reactos/` | 9 | 3.870 | 6/8 | ✅ tam | Wine/Win32 · `reactos-kod-seti.md` + **`reactos-dma-ve-interrupt.md`** eklendi |
| tizen | `tizen/` | 9 | 3.959 | 7/8 | ✅ tam | Samsung/Linux · `tizen-kod-seti.md` (≥500) + **`tizen-dma-ve-interrupt.md`** eklendi |
| webos | `webos/` | 9 | 3.811 | 7/8 | ✅ tam | `webos-kod-seti.md` (≥500) + **`webos-dma-ve-interrupt.md`** eklendi |
| windows | `windows/` | 10 | 4.645 | 8/9 | ✅ tam | Win32/NT · `windows-kod-seti.md`; DMA dosyası (2026-10-12) mevcut |

**Toplam:** 80 dosya · 35.909 satır (kök 5 · 2.640 + platform 75 · 33.269 — 2026-10-14 taze recursive sayım, ReadAllLines, DMA turu + indeks güncellemeleri sonrası). **6 yeni `<os>-dma-ve-interrupt.md`** (macos · ios · android · reactos · tizen · webos) bu turda eklendi — her biri 151–188 satır (tablo ağırlıklı, referans dosyası); ≥500 hedefi bu altı dosya için **bilinçli gevşetildi** (72 içerik dosyasının 60'ı ≥500). windows/linux DMA dosyaları 2026-10-12'den beri mevcuttur → **8/8 platformda DMA dosyası var**. **DMA kanıt durumu:** windows · linux · macos · ios · android · reactos = resmi URL ile doğrulanmış; tizen · webos = belge boşluğu `⚠️`/`UNKNOWN` ile işaretli (uydurma akış YOK). **Çift yönlü link:** her platform `index.md` `parent_id: K000-isletim-sistemi/isletim-sistemi-turu` taşır ve tablo satırında köke geri döner — kök ⇄ platform linki tekil değil, iki yönlüdür.

## Açık sorular

Q5 tür↔altyapı boşluğu · T-U1 cross sınırı · T-U2 şema seçimi · T-U3 hedef tür — hiçbiri kapatılmadı.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `isletim-sistemi-turu-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `isletim-sistemi-turu-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kod | `isletim-sistemi-turu-kod-seti.md` | Gruplu cross-platform kod seti (8 grup × 10 dil · Linux birincil) | Kademe-2 |
| kanit | `isletim-sistemi-turu-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| mimari | `android/index.md` | Platform künyesi ve çocuk kataloğu — **diskte yok**, beyazliste dışı | Kademe-3 |
| mimari | `ios/index.md` | Platform künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `linux/index.md` | Platform künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `macos/index.md` | Platform künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `reactos/index.md` | Platform künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `tizen/index.md` | Platform künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `webos/index.md` | Platform künyesi ve çocuk kataloğu — **canlı ajan yazıyor, dokunulmadı** | Kademe-3 |
| mimari | `windows/index.md` | Platform künyesi ve çocuk kataloğu | Kademe-3 |

> `android/index.md` ve `webos/index.md` satırları disk durumunu yansıtır (android'de indeks yok, webos'a bu tur girilmez) — satır silinmez, durum notu taşınır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode