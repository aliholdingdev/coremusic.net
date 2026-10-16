---
node_id: "K015-media/media-testing/media-testing-kapsam"
node_type: "module"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "media-master.md §9.11 (rol: medya testleri — v2 §2.15; kapsam: decode/çalma/akış kalitesi; araç satırı ⚠️ UNKNOWN; §9.11 'kanıt oranını DEĞİŞTİRmez')"
  - "media-master.md §12/10 (media-testing araç seti → ⚠️ UNKNOWN)"
  - "media-master.md §13/1-2 (decode/çalma doğruluğu ve akış kalitesi senaryoları — sahiplikleri belirsiz)"
  - "media-master.md §7/4 · §14/4 (K019-testing vs K015/media-testing sahipliği → açık kapı)"
validation: "Draft — tüm tasarım satırları TASARIM İDDİASI'dır; §7/4 sahiplik, §12/10 araç seti ve tolerans eşikleri açık bırakıldı (2026-10-16)"
open_questions:
  - "Araç seti (ffprobe/ffmpeg hangi sürüm, hangi bayraklar) → ⚠️ UNKNOWN (media-master §12/10)"
  - "Sahiplik: K019-testing vs K015/media-testing → ⚠️ VERIFICATION REQUIRED (§7/4 — çözülmedi)"
  - "Tolerans eşikleri (süre ms, PSNR dB, LUFS) → ⚠️ UNKNOWN — bu dosyada sayısal eşik yazılmaz"
group: mimari
---

# K015 · media-testing · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Test Edilir, Ne Hariç
>
> **Genel etiket:** bu dosyadaki her tasarım satırı **TASARIM İDDİASI**'dır — kanıt değildir; kanıt
> `media-testing-web-arastirma.md` §1'de, iskelet kod `media-testing-ornekler.md`'dedir.

---

## §1 KAPSAM İÇİ (modellenen)

**TASARIM İDDİASI —** aşağıdaki 6 kalem bu çocuğun medya test yüzeyini oluşturur:

1. **Test senaryosu durum takibi** — her medya senaryosu bir durum makinesinden geçer (§3: draft/running/passed/failed/skipped) — rol media-master §9.11 (v2 §2.15).
2. **Decode doğrulama yüzeyi** — dosyanın baştan sona çözülebilirliği (bozuk/eksik-stream tespiti) — media-master §13/1 senaryosu; **sahipliği belirsiz** (§6-Q4).
3. **Metadata/süre doğruluğu** — beklenen süre, codec, kanal/samplerate ile ölçülen değerin karşılaştırılması; karşılaştırma **toleranslı** yapılır (byte-eşitlik değil — gerekçe: `media-testing-web-arastirma.md` §2 C-2).
4. **Akış kalitesi ölçümü yüzeyi** — bitrate/kesinti metriklerinin toplanacağı alan — media-master §13/2; **sahipliği belirsiz** (§6-Q4).
5. **Sonuç kaydı** — senaryo sonucu (passed/failed/skipped + gerekçe) kalıcı kayda yazılır; rapor bu çocuktan okunur.
6. **Test fixture yüzeyi** — küçük örnek medya dosyalarının nereden geldiği ve sürüm sabitlemesi — araç seti §6-Q1 (UNKNOWN) olduğu için **bağlayıcı değildir**.

**Sınır kuralı (TASARIM İDDİASI):** bu dosyanın satırları media-master §9.11'i genişletir; §9.11'deki "Araç ⚠️ UNKNOWN" ve §13'teki sahiplik belirsizlikleri bu dosyada **karar sayılmaz**, yalnız açık soru olarak taşınır (§6).

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli)

1. **Ortak test altyapısı, CI, test çalıştırıcı** — K019-testing ile sahiplik **§7/4, bu dosyada çözülmez** (açık soru: §6-Q2). Gerekçe: media-master §7/4 satırı "Vault Steward kararı" ister.
2. **Ses motoru/DSP iç testleri** (DSP chain, ASIO gecikme) — K003-audio alanı; bu çocuk medya *dosyası/akışı* test eder, sinyal zincirini değil.
3. **Araç seti kararı** — ffprobe/ffmpeg/PHPUnit/Playwright hangi sürüm ve bayrakla kullanılacak → media-master §12/10 ⚠️ UNKNOWN; bu kapsam araç **seçmez**, yalnız araçtan girdi bekleyen akışı modeller (§6-Q1).
4. **Numaralı tolerans eşikleri** (süre kaç ms, PSNR kaç dB, LUFS kaç) — sayısal değer buraya **yazılmaz** (§6-Q3); yanlış eşik = yanlış test.
5. **Test edilen davranışın kendisi** — decode/çalma/akış K015 playback · streaming · downloads · media-cache çocuklarının işidir; bu çocuk yalnız **doğrular**, üretmez.
6. **§13 senaryolarının 3-8 numaralıları** (çevrimdışı, indirme resume, önbellek TTL, metadata, artwork, eşitleme) — sahiplikleri ilgili çocuklardadır (media-master §13); bu çocuk onların **veri yüzeyini** taşır, testi yapmaz (bkz §2-3).

---

## §3 DURUM MAKİNESİ (TASARIM İDDİASI)

> Aşağıdaki tablonun tüm satırları **TASARIM İDDİASI**'dır — media-master §9.11'da durum makinesi **yoktur**;
> 5 durum bu dosyada önerilir.

| Durum | Anlam | Çıkış geçişleri |
|---|---|---|
| `draft` | senaryo tanımlandı, çalıştırılmadı (fixture/araç bekliyor) | → `running` (çalıştırma tetiği) · → `skipped` (fixture/araç yok — §5 T-1/T-6) |
| `running` | decode/ölçüm sürüyor (araç çağrısı açık) | → `passed` · → `failed` · → `skipped` (zaman aşımı — §5 T-5) |
| `passed` | tüm karşılaştırmalar tolerans içinde | → `draft` (senaryo yeniden tanımlanırsa) |
| `failed` | tolerans dışı sonuç veya araç/decode hatası | → `draft` (düzeltme sonrası) · → `skipped` (kararla atlanma — gerekçe zorunlu) |
| `skipped` | çalıştırılmadı (araç/fixture yok veya bilinçli atlama) | → `draft` (koşul geri gelince) |

**Geçiş kuralı (TASARIM İDDİASI):** `passed` ve `failed` terminal değildir — her yeniden tanımda senaryo `draft`'a döner. `running → passed` yalnız tolerans karşılaştırmasından geçer; araç çıkışı **tek başına** geçiş üretmez (§5 T-7).

---

## §4 VERİ AKIŞI (TASARIM İDDİASI)

```text
[fixture / test medyası]
   │ (1) fixture doğrula (var mı, sabit mi) — yoksa skipped (T-6)
   ▼
[running] ── (2) araç çağrısı (decode/probe — araç seti UNKNOWN, Q-1)
   │
   ├─ araç yok / exit ≠ 0 ──▶ [failed] (T-1/T-2)
   ▼
(3) ölçülen değerleri ayrıştır (süre, codec, bitrate, kesinti)
   ▼
(4) beklenenle toleranslı karşılaştır (byte-eşitlik YOK — C-2)
   │
   ├─ tolerans dışı ──▶ [failed] (T-3/T-4)
   ▼
(5) sonuç kaydı (durum + gerekçe + ölçüm değerleri)
   ▼
[passed]
```

| Adım | Veri | Sorumluluk |
|---|---|---|
| 1 | fixture künyesi (dosya, checksum/sürüm) | fixture yüzeyi (§1-6) |
| 2 | araç çağrısı + çıkış kodu + ham çıktı (JSON/metin) | araç katmanı (araç seti §6-Q1 UNKNOWN) |
| 3 | ölçülen alanlar (duration_ms, codec_name, bitrate_v, kesinti sayısı) | ölçüm ayrıştırma |
| 4 | beklenen değerler + izinli sapma (eşik §6-Q3 UNKNOWN) | karşılaştırma |
| 5 | sonuç kaydı (senaryo adı, durum, ölçüm, gerekçe) | gözlem (sınır: log redaction kuralı geçerli) |

---

## §5 HATA DURUMLARI (TASARIM İDDİASI)

| # | Hata | Tetik | Davranış | İşaret |
|---|---|---|---|---|
| T-1 | araç bulunamadı | ffprobe/ffmpeg yok veya çalışmıyor | senaryo `skipped`; araç seti kararı bekle | **§12/10 UNKNOWN → `⚠️ VERIFICATION REQUIRED` işaretle** |
| T-2 | decode hatası | araç çıkış kodu ≠ 0 / çözülemeyen dosya | `failed` + gerekçe (ilk hata satırı) | bozuk medya kanıtı |
| T-3 | süre toleransı aşımı | \|beklenen − ölçülen\| > eşik | `failed`; sapma değeri kayda yazılır | eşik §6-Q3 UNKNOWN |
| T-4 | metadata uyuşmazlığı | codec/kanal/samplerate farkı | `failed` (alan adı + iki değer) | alan bazlı rapor |
| T-5 | zaman aşımı | araç çağrısı izinli süreyi aştı | `failed` → tekrar `draft`; üst üste tekrar eşiği **UNKNOWN** (Q-5) | flaky koruması |
| T-6 | fixture eksik | örnek dosya diskte/CI'da yok | `skipped` + gerekçe; uydurma fixture **üretilmez** | `⚠️ VERIFICATION REQUIRED` |
| T-7 | byte-eşitlik talebi | bir karşılaştırma "aynı byte mı?" ister | talebi **reddet**, özellik/tolerans karşılaştırmasına çevir | determinism-over-exactness (web-arastirma §2 C-2) |
| T-8 | bilinmeyen ölçüm alanı | araç çıktısında tanımsız alan | alanı `UNKNOWN` işaretle, **ezme/uydurma**; sonraki tarihe bırak | `⚠️ VERIFICATION REQUIRED` |

**Kural (TASARIM İDDİASI):** T-1, T-6 ve T-8'te sistem **sessizce "geçmiş" saymaz** — eksik araç, eksik fixture ve bilinmeyen alan açıkça işaretlenir; tolerans eşikleri sayısal olarak onaylanana kadar T-3'te **eşik varsayılmaz** (bu dosyada sayısal eşik yok — §6-Q3).

---

## §6 AÇIK SORULAR (kapanışı gerekenler)

| # | Açık soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| Q-1 | **Araç seti:** ffprobe/ffmpeg/PHPUnit/Playwright — hangi araçlar, hangi sürümler? | ⚠️ UNKNOWN (media-master §12/10) | Vault Steward kararı — §4 adım 2 araç katmanıyla revize edilir |
| Q-2 | **Sahiplik:** bu çocuk mu, K019-testing mi ortak test altyapısını taşıyor? | ⚠️ VERIFICATION REQUIRED (§7/4) | Vault Steward kararı — **bu dosyada çözülmez** |
| Q-3 | **Tolerans eşikleri:** süre sapması kaç ms, PSNR kaç dB, LUFS kaç? | ⚠️ UNKNOWN | sayısal eşik onayı — onaya kadar §5 T-3 eşik koymaz |
| Q-4 | §13/1 (decode doğruluğu) ve §13/2 (akış kalitesi) senaryolarının sahipliği | ⚠️ belirsiz (media-master §13) | sahiplik satırının bir çocuğa bağlanması |
| Q-5 | Zaman aşımı / tekrar (retry) eşiği — kaç tekrarda `failed` kalıcı? | ⚠️ UNKNOWN | sayısal eşik onayı |
| Q-6 | P.863 benzeri algısal kalite ölçümü bu çocukta kullanılacak mı (erişim/standart)? | ⚠️ VERIFICATION REQUIRED (web-arastirma §4-W4) | kullanım kararı + erişim teyidi |

---

## § REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-16 | İlk yazım — §1-§6 (kapsam içi/dışı, durum makinesi, veri akışı, hata, açık sorular) | Vault Steward (subagent) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
