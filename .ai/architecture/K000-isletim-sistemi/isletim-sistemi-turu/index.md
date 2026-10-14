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
validation: "web: developer.android.com, tron.org/rtos/rtos01, docs.kernel.org, POSIX.1-2024 yanıt kodları 2026-10-12 (HTTP 200). osek-vdx.org/mirror/os221.pdf → 403 (erişim engeli, bkz. os-dogrulama ölü-URL tablosu). disk: os-master.md §3 satırları okundu; alt dizinler windows/linux/macos (disk enumerasyonu). 2026-10-14: kök'e isletim-sistemi-turu-kod-seti.md eklendi (938 satır) → yeniden sayım 66 dosya / 31.258 satır."
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

Seçilmiş sınıflandırma şeması **yok** (T-U2 UNKNOWN), hedef tür **bilinmiyor** (T-U3/Q4 UNKNOWN). Bu yüzden kapsam dosyası yalnız ölçütleri ve bilinen sınır notlarını taşır; "hedefimiz X tür OS" cümlesi **yazılmaz**. Altında sekiz platform alt-dizini (`android`, `ios`, `linux`, `macos`, `reactos`, `tizen`, `webos`, `windows`) diskte mevcuttur (ölçüm 2026-10-13, §PLATFORMLAR) — derinlemesine içerik dosyaları bu turda **dokunulmaz**, yalnız platform `index.md'leri zenginleştirilir. **2026-10-14 eklemesi:** kök `isletim-sistemi-turu-kod-seti.md` eklendi — gruplu cross-platform kod seti (8 grup · 10 dil · 938 satır · üretim Linux, kod Windows/macOS dahil tüm sınıflara taşınabilir).

## Dosya indeksi

| Dosya | Ne içerir |
|---|---|
| [`isletim-sistemi-turu-kapsam.md`](isletim-sistemi-turu-kapsam.md) | Rol, sınır (içinde/dışında/komşu), ayrım noktaları U1–U5, bağımlılık grafiği, olgunluk matrisi |
| [`isletim-sistemi-turu-dogrulama.md`](isletim-sistemi-turu-dogrulama.md) | Web (≥5) + disk kanıtları, 403 engelli URL kaydı, web doğrulama matrisi |
| [`isletim-sistemi-turu-ornekler.md`](isletim-sistemi-turu-ornekler.md) | Gerçek repo kodu: tür/ipucu kanıtları (`php_uname`, hedef platform notu) |
| [`isletim-sistemi-turu-kod-seti.md`](isletim-sistemi-turu-kod-seti.md) | Gruplu cross-platform kod seti — 8 grup (platform tespiti · yol · süreç · dış araç · kilit · ortam · kabuk · CI) × 10 dil (PHP/JS/Python/Go/Rust/C#/C/C++/Bash/PowerShell) · Linux birincil |
| `android/` · `ios/` · `linux/` · `macos/` · `reactos/` · `tizen/` · `webos/` · `windows/` | 8 platform alt-dizini — ayrıntı §PLATFORMLAR tablosunda, her birinin kendi `index.md`'si vardır |

## §PLATFORMLAR

> Disk ölçümü 2026-10-13 · `Get-ChildItem -Recurse -Filter *.md` + `Get-Content` satır sayımı. `≥500` sütunu = içerik dosyalarının 500 satır hedefini tutturanı / toplam içerik dosyası (`index.md` bu hedefe tabi değildir).

| Platform | Dizin | Dosya | Satır | ≥500 | Durum | Not |
|---|---|---:|---:|---|---|---|
| android | `android/` | 6 | 3.104 | 6/6 | ✅ tam | `index.md` yok — bu tur beyazlistesi dışında |
| ios | `ios/` | 7 | 3.105 | 6/6 | ✅ tam | Apple mobil · CoreAudio sınırı `-audio/coreaudio`'ya |
| linux | `linux/` | 12 | 5.883 | 11/11 | ✅ tam | En derin dal · çekirdek/dağıtım ayrımı `-kernel`/`-core`'a |
| macos | `macos/` | 6 | 2.704 | 5/5 | ✅ tam | Darwin/XNU · Gatekeeper · `-cross`'a |
| reactos | `reactos/` | 7 | 3.228 | 6/6 | ✅ tam | Wine/Win32 uyumluluğu · `-cross`'a |
| tizen | `tizen/` | 7 | 3.183 | 6/6 | ✅ tam | Samsung/Linux tabanı · `-audio/tizen-audio`'ya |
| webos | `webos/` | 7 | 3.087 | 6/6 | 🔄 ajan çalışıyor | Yazım yasak — canlı ajan (`webos-web-arastirma.md`) |
| windows | `windows/` | 9 | 4.223 | 8/8 | ✅ tam | Win32/NT · `-audio/wasapi` · `-api/platform-api`'ya |

**Toplam:** 66 dosya · 31.258 satır (kök 5 · 2.619 + platform 61 · 28.639 — 2026-10-14 taze sayım). **Çift yönlü link:** her platform `index.md` `parent_id: K000-isletim-sistemi/isletim-sistemi-turu` taşır ve tablo satırında köke geri döner — kök ⇄ platform linki tekil değil, iki yönlüdür.

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