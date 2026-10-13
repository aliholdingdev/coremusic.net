---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children:
  - K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-stream-yasam-dongusu
  - K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-ve-opensl-es-gecisi
depends_on: []
evidence:
  - https://developer.android.com/about/versions/pie/android-9.0
  - https://developer.android.com/games/agdk
validation:
  - "2026-10-13 · 2 resmi URL HEAD 200 · envanter kesişimi 0 · dosya envanteri disk glob ile sayıldı"
open_questions:
  - "CoreMusic reposunda Android/AAudio ses kodu YOK (215 kaynak dosya taraması 0 isabet) → UNKNOWN"
  - "Android hedefi CoreMusic yol haritasında mı? → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# aaudio — Deep-Dal İndeksi (Kademe-4)

**ELI15:** AAudio, Android'in **NDK içindeki düşük gecikme (low-latency) ses kapısıdır** —
uygulama sesi native C kodundan bu akış (stream) üzerinden okur/yazar, sistem
gerekirse MMAP yoluna indirir.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | Android (NDK · API 26+ çekirdek) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Android 9 (API 28) sürüm notları AAudio API genişlemesini (usage · content type · input preset · session ID) doğrudan sayar | https://developer.android.com/about/versions/pie/android-9.0 | 2026-10-13 | ✅ HEAD 200 · GET içerikte "AAudio" geçiyor |
| Android Game Development Kit (AGDK) sayfası düşük gecikme ses akışı ve Oboe katmanını sayfalarının konusu yapar | https://developer.android.com/games/agdk | 2026-10-13 | ✅ HEAD 200 · GET içerikte "Low latency audio" geçiyor |

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-13)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |
| 2 | `aaudio-anayasa.md` | Kimlik · mimari · OpenSL ES geçişi · exclusive/mod | **oluşturuldu** |
| 3 | `aaudio-stream-yasam-dongusu.md` | Stream build→open→start · callback · buffer · hata | **oluşturuldu** |
| 4 | `aaudio-ve-opensl-es-gecisi.md` | OpenSL ES durumu · ikili çalışma · API seviyesi matrisi | **oluşturuldu** |

> Kural: bu 4 dosyanın yüzeyi bu deep-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-audio/*.md`, `wasapi/`, `alsa/`, `coreaudio/` dosyalarına
> bu turda **dokunulmadı** (başka ajanların yüzeyi).

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| Oboe kütüphanesi uygulama kodu (C++ sarmalayıcı detayı) | bu dal §3 · uygulama katmanı kodu değil |
| OpenSL ES'in genel Khronos spesifikasyonu | bu dal §3 · Khronos belgesi bu dalın dışında |
| Android ses efektleri · audio effects zinciri | K004-dsp |
| Ses motoru C++ kodu (Neva Engine) | K003-audio |
| Diğer platform ses API'leri (WASAPI · ALSA · CoreAudio) | `../index.md` §1 |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| A1 | CoreMusic Android/AAudio hedefi var mı? (repo taraması 0 isabet) | UNKNOWN |
| A2 | AAudio exclusive (MMAP) yolunun üretici cihazda desteklenip desteklenmediği ölçümü yapıldı mı? | ⚠️ VERIFICATION REQUIRED |
| A3 | `minSdk`/API seviyesi kararı (26 mı, Oboe fallback ile daha düşük mi)? | ⚠️ VERIFICATION REQUIRED |
| A4 | Ölçüm hedefi (512 örnek · 48 kHz · sartname) Android cihazda geçerli mi? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `aaudio-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-4 |
| mimari | `aaudio-stream-yasam-dongusu.md` | Stream yaşam döngüsü ve callback sözleşmesi | Kademe-4 |
| mimari | `aaudio-ve-opensl-es-gecisi.md` | Geçiş stratejisi ve API seviyesi matrisi | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
