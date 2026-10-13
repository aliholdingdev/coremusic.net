---
node_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api
node_type: subdomain-scope
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api
children:
  - K000-isletim-sistemi/isletim-sistemi-api/platform-api/windows-api-yuzeyi
  - K000-isletim-sistemi/isletim-sistemi-api/platform-api/android-api-yuzeyi
  - K000-isletim-sistemi/isletim-sistemi-api/platform-api/apple-api-yuzeyi
depends_on: []
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/api/objbase/
  - https://developer.android.com/ndk
  - https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudioOverview/Introduction/Introduction.html
validation: []
open_questions:
  - "Q-P2 açık: Platform ses API'si bu dalda mı, isletim-sistemi-audio içinde mi? — ⚠️ VERIFICATION REQUIRED (üst dal index.md §5)"
group: mimari
---

# platform-api — Alt-dal Künyesi (Kademe-3)

**ELI15:** Çekirdek API'den (syscall) bir üst katman: işletim sistemi
**dağıtıcısının** (Microsoft · Google · Apple) kendi başlıkları, COM/JNI/Objective-C
mekanizmaları ve sürüm matrisleri. `isletim-sistemi-api` dalının 3. katmanı
("Platform API'si — Windows/Android/macOS başlık tabanlı yüzeyler") burada
derinleştirilir.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api |
| Kademe | 3 — alt-dal kapsamı (subdomain-scope) |
| Çocuk sayısı | 3 (disk kanıtlı — bu turda yazıldı) |
| Durum | draft |
| Grup | mimari (tüm dosyalarda 9. alan `group`) |

## ⚠️ Q-P2 — Sınır henüz kapanmadı

| Konu | Durum |
|---|---|
| Soru | "Platform ses API'si burada mı, `isletim-sistemi-audio` içinde mi?" (Q-P2 — üst dal `index.md` §5) |
| Karar | **VERIFICATION REQUIRED** — sınır **kapatılmadı** |
| Geçici tutum | Bu alt-dal platform API'lerinin **genel yüzeyini** (Win32/COM · NDK/JNI · AudioToolbox/CoreAudio) belgeler; **ses-özel akış davranışı** (akış başlatma · cihaz seçimi · önbellek) `isletim-sistemi-audio` dalının konusudur |
| Çakışma riski | Ses dosyaları (`windows-api-yuzeyi` §A, `android-api-yuzeyi` §B–§C, `apple-api-yuzeyi` §A–§B) yalnız API **tanımı** düzeyinde kalır — davranışsal tasarım oraya aittir |

## §1 Kapsam — bu alt-dal neyi taşır

| # | Kapsam başlığı | Karşıladığı dosya | Odak |
|---|---|---|---|
| 1 | Windows yüzeyi | `windows-api-yuzeyi.md` | Win32 başlık yüzeyi · COM · WinRT · DLL/ABI · SDK sürüm matrisi · PHP → FFI → Win32 sınırı |
| 2 | Android yüzeyi | `android-api-yuzeyi.md` | NDK · JNI · AAudio · OpenSL ES (deprecated yolu) · hizmet seviyesi kısıtları · sürüm matrisi |
| 3 | Apple yüzeyi | `apple-api-yuzeyi.md` | AudioToolbox · CoreAudio/AVAudioEngine · Objective-C++ sarmalama · macOS vs iOS · sürüm matrisi |

## §2 Üst dal ile ilişki

| Üst dal katmanı | Bu alt-daldaki karşılığı |
|---|---|
| Katman 3 — Platform API'si | Doğrudan karşılık: "Windows/Android/macOS başlık tabanlı yüzeyler · Core Audio başlıkları · AAudio · AudioToolbox" (üst dal `index.md` §1, katman 3) |

## §3 Sınır komutları

| Dışarıda olan | Nereye gider |
|---|---|
| POSIX soket ↔ Winsock farkları | `ag-api` alt-dalı (`posix-socket-ve-winsock-karsilastirmasi.md`) |
| Ses akışının davranışsal tasarımı (akış yaşam döngüsü · cihaz seçimi) | `isletim-sistemi-audio` (Q-P2 — sınır açık) |
| Kernel bileşenleri (WDM · ALSA çekirdek · XNU) | `isletim-sistemi-kernel` |
| WebOS · Tizen · Windows detay os dalları | `webos` · `tizen` · `windows` alt dalları (çalışan ajanlar) |

## §4 Grup notu

Bu alt-dalın tüm dosyaları `group: mimari` taşır (yeni konu dosyaları —
registry §8.1 eşlemesi). İki-ekseni gruplama `## §GRUPLAR` tablosu aşağıdadır.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Alt-dal künyesi, Q-P2 sınır uyarısı ve çocuk kataloğu | Kademe-3 |
| mimari | `windows-api-yuzeyi.md` | Win32/COM/WinRT/DLL yüzeyi ve sürüm matrisi | Kademe-3 |
| mimari | `android-api-yuzeyi.md` | NDK/JNI/AAudio/OpenSL ES yüzeyi ve sürüm matrisi | Kademe-3 |
| mimari | `apple-api-yuzeyi.md` | AudioToolbox/CoreAudio yüzeyi, sarmalama ve sürüm matrisi | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
