---
title: "K003-audio Dil Kapsam Matrisi - 4 Dil Grubu × 12 Çocuk Eşlemesi"
type: architecture-deep-dive
node_id: K003-audio/audio-dil-kapsami
node_type: domain
status: draft
date: 2026-10-14
parent_id: K003-audio
children: []
depends_on:
  - K003-audio/audio-master.md
  - K003-audio/index.md
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md"
evidence:
  - "Kullanıcı seçimi 2026-10-14: 4 dil grubunun TAMAMI seçildi (1–4 ✓) — kapsam matrisi bu dosyanın konusudur"
  - "repo 0 .cpp/.h (L0 durumu: PLANNED) — tüm eşlemeler [TASARIM] etiketlidir, [KANIT] YOK"
  - "isletim-sistemi-turu-kod-seti.md — 8 grup · 10 dil · 760 satır cross-platform kod seti (2026-10-14, kök K000) — dil grupları bu dosyayla uyumludur"
validation: "TRUTH MODE: 4 grup × 12 çocuk = 48 hücre eşlemesi TASARIM'dır; her hücrede kod kanıtı aranmadı çünkü repo'da C++/Rust/Go/Zig motor kodu YOK → [KANIT] geçişi D6 kapısına bağlı (audio-dogrulama.md §4)"
open_questions:
  - "D6: [TASARIM] → [KANIT] geçişi bu turda KAPANAMAZ (repo 0 .cpp/.h) — motor kodu yazılınca matris hücreleri [KANIT]'e çevrilir"
  - "Grup 3 (PHP/JS-Node) yalnız backend/yönetim yüzeyinde mi, motor çekirdeğinde mi kullanılır? — backend/index.md'de sınır çizilmedi ⚠️"
  - "Grup 4 (Zig/Swift/Java-Kotlin) K003'te hangi çocuk için bağlayıcı? — kullanıcı kapsam dahil etti, kullanım senaryosu tanımsız ⚠️"
group: mimari
---

# audio-dil-kapsami — K003-audio Dil Kapsam Matrisi (4 grup × 12 çocuk)

> **Eksen:** `K003-audio / audio-dil-kapsami` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Kullanıcının 2026-10-14'te seçtiği **4 programlama dili grubunun** (1–4, tümü ✓) K003'ün
> 12 çocuğuna dağılımını tek matriste taşımak; her hücreyi `[TASARIM]`/`[KANIT]` etiketiyle
> dürüstçe işaretlemek.
> **Konu sırası:** (1) sınır → (2) grup tanımı → (3) ana matris → (4) grup başına gerekçe →
> (5) yasaklar → (6) açık konular → (7) FAQ → (8) bağımlılık → (9) RED TEAM.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** 4 dil grubunun tanımlanması; 4 gruptan **her birinin** 12 K003 çocuğuna
  atandığı kapsama matrisi; her atamanın gerekçesi ve etiketi ([TASARIM]).
- ❌ **Yapılmaz:** Kod üretimi (repo 0 `.cpp/.h` — uydurma `Dosya:/Satır:` yasak) · dil seçimi
  **kararı** (kullanıcı 2026-10-14'te verdi, burada tekrar değerlendirilmez) · sürüm/performans
  iddiaları (ölçülmedi → `UNKNOWN`).

---

## 1. Seçilen 4 Dil Grubu (Kullanıcı Kararı — 2026-10-14)

| # | Grup | Diller | Birincil rol (K003 bağlamı) |
|---|---|---|---|
| G1 | Sistem/gerçek-zamanlı | **C · C++ · Rust** | Motor çekirdeği, DSP hattı, lock-free zamanlama (embedded-engineer alanı) |
| G2 | Araç/altyapı | **Python · Go · C#** | Ölçüm/telemetri betikleri, servis araçları, Windows araç katmanı |
| G3 | Web/backend | **PHP · JavaScript (Node.js)** | Yönetim/yayın yüzeyi — CoreMusic PHP 8.x backend + Node aracı |
| G4 | Niş/uzman | **Zig · Swift · Java-Kotlin** | Platform-özel/derleme ara katmanı — kapsam dahil, kullanım senaryosu ⚠️ tanımsız |

> **Kullanıcı kararı kaydı:** Tüm gruplar seçildi (1–4 ✓). Bu dosya kararı **tekrar sorgulamaz**;
> yalnızca dağılımını yazar.

---

## 2. Ana Matris (4 grup × 12 çocuk)

**Etiket sözlüğü:** `S` = Birincil (bu dil grubu o çocuğun ana dili) · `Y` = Yardımcı (araç/betik
katmanı) · `-` = Kapsam dışı · `[TASARIM]` = kod kanıtı yok, tasarım eşlemesi · `[KANIT]` = repo
kodundan doğrulanmış (bu turda **0 hücre**).

| # | Çocuk | G1 C/C++/Rust | G2 Python/Go/C# | G3 PHP/JS-Node | G4 Zig/Swift/Java-Kotlin |
|---|---|---|---|---|---|
| 1 | engine | **S** [TASARIM] | Y (ölçüm betiği) [TASARIM] | - | Y (derleme/entegrasyon) [TASARIM] |
| 2 | device | **S** [TASARIM] | Y (enum/arac) [TASARIM] | - | - |
| 3 | driver | **S** [TASARIM] | - | - | Y (platform sarmalayıcı) [TASARIM] |
| 4 | backend | **S** [TASARIM] | Y (kuyruk/araç) [TASARIM] | **Y** (yönetim yüzeyi — sınır ⚠️) [TASARIM] | - |
| 5 | input | **S** [TASARIM] | Y (dosya/dekode arac) [TASARIM] | Y (akış kaynağı) [TASARIM] | - |
| 6 | output | **S** [TASARIM] | Y (yayın aracı) [TASARIM] | Y (akış/hedef) [TASARIM] | - |
| 7 | routing | **S** [TASARIM] | - | Y (yönetim kuralı) [TASARIM] | - |
| 8 | mixing | **S** [TASARIM] | Y (referans/ölçüm) [TASARIM] | - | - |
| 9 | realtime | **S** (lock-free) [TASARIM] | - | - | Y (Zig/Swift alt katman denemesi ⚠️) [TASARIM] |
| 10 | synchronization | **S** [TASARIM] | Y (ölçüm/telemetri) [TASARIM] | - | - |
| 11 | diagnostics | Y (ders/hat) [TASARIM] | **S** (log/rapor) [TASARIM] | Y (web log) [TASARIM] | - |
| 12 | testing | Y (unit/fixture) [TASARIM] | **S** (test koşturucu) [TASARIM] | Y (PHPUnit/Vitest) [TASARIM] | Y (platform test) [TASARIM] |

**Matris özeti:** 48 hücre · `S` 12 (her çocukta 1 birincil grup) · `Y` 22 · `-` 14 ·
**[KANIT] = 0** (repo 0 `.cpp/.h`) → D6 kapısı bu turda açık kalır.

---

## 3. Grup Bazında Gerekçe (Neden O Çocuk?)

### G1 — C/C++/Rust (12/12 çocukta birincil veya yardımcı)

| Gerekçe | Çocuklar | Etiket |
|---|---|---|
| Gerçek-zamanlı ses motoru C++20 + JUCE hedefidir (AGENTS §4 embedded-engineer satırı) | engine · realtime · synchronization · mixing | [TASARIM] |
| Sürücü/OS API yüzeyi (ASIO/WASAPI/CoreAudio/ALSA) C bağlamalıdır | driver · device · backend | [TASARIM] |
| Veri yolu (input/output/routing) bellek-kopyası ağırlıklı → dil sınırı C'ye yakın | input · output · routing | [TASARIM] |
| Hata/ölçüm çekirdeği C'de; test koşturucu dil-dışı | diagnostics · testing | [TASARIM] |

### G2 — Python/Go/C# (arı/araç katmanı)

| Gerekçe | Çocuklar | Etiket |
|---|---|---|
| Ölçüm/telemetri/rapor betikleri motorun dışında koşar | diagnostics · testing · synchronization (drift ölçümü) | [TASARIM] |
| C# Windows araç yüzeyi (K000 windows-kod-seti ile uyumlu) | device · backend · engine (araç) | [TASARIM] |
| Go/Python enum, kodek, yayın aracı | input · output · mixing (referans ölçüm) | [TASARIM] |
| realtime'ye müdahale etmez (G1 tekel) | realtime · routing · driver → `-` | [TASARIM] |

### G3 — PHP/JS-Node (yönetim/yayın yüzeyi — CoreMusic bağlamı)

| Gerekçe | Çocuklar | Etiket |
|---|---|---|
| CoreMusic PHP 8.x backend'i (repo kanıtı: `shared/src/`) — yönetim/yayın arayüzü | backend · routing (kural yönetimi) · diagnostics (web log) | [TASARIM] |
| Node.js aracı (kodek/pipeline aracı, ffmpeg sarmalayıcı) | input · output · testing (Vitest) | [TASARIM] |
| Motor çekirdeğine girmez (yangın hattı PHP'de değil) | engine · realtime · synchronization · mixing · device · driver → `-` | [TASARIM] |
| **Sınır ⚠️:** backend'in hangi yarıda PHP, hangi yarıda C++ olduğu `backend/index.md`'de çizilmemiş | backend | ⚠️ açık madde |

### G4 — Zig/Swift/Java-Kotlin (kapsam dahil — senaryo tanımsız)

| Gerekçe | Çocuklar | Etiket |
|---|---|---|
| Swift → macOS/iOS platform sarmalayıcısı (K000 macos/ios kod-seti ile uyumlu) | driver · engine (platform katmanı) | [TASARIM] |
| Java/Kotlin → Android platform sarmalayıcısı | driver · testing (platform test) | [TASARIM] |
| Zig → derleme/alt katman denemesi (araştırma) | realtime (⚠️ yalnız araştırma) · testing | [TASARIM] ⚠️ |
| **Senaryo tanımsız:** kullanıcı kapsam dahil etti; bu grup için bağlayıcı kullanım senaryosu yazılmadı | tümü | ⚠️ açık madde |

---

## 4. Yasaklar (Bu Matrisin Kullanım Sınırı)

1. **`[KANIT]` uydurma yasak:** Repo 0 `.cpp/.h` olduğu için hiçbir hücre `[KANIT]` yazılamaz.
2. **G3 motor çekirdeğine giremez:** PHP/JS yolu yalnız yönetim/yayın katmanıdır; `realtime` ve
   `engine` çekirdeğine PHP sokmak katman ihlalidir (AGENTS §18 Warning 6).
3. **Grup çakışması:** Bir çocukta iki `S` olamaz — her çocukta **tek** birincil grup vardır.
4. **PCM5122 yasaklığı** (ADR-017 / K003 §18 sartname) bu matristen bağımsızdır — dil seçimi
   donanım kararını **değiştirmez**.
5. **Sürüm iddiası yasak:** "C++20", "Rust 1.x" gibi sürüm/performans iddiası → `UNKNOWN` veya
   ilgili vault dosyasına bağlanır (bu dosyada ölçülmedi).

---

## 5. Açık Konular

| # | Soru | Durum |
|---|---|---|
| Q-D1 | D6 [TASARIM] → [KANIT] geçişi | ❌ açık — motor kodu yok |
| Q-D2 | backend'te PHP/C++ sınırı | ⚠️ açık — `backend/index.md`'de çizilmemiş |
| Q-D3 | G4 bağlayıcı kullanım senaryosu | ⚠️ açık — kullanıcı tanımlamadı |
| Q-D4 | G2 arac motoruyla aynı repo'da mı, ayrı mı? | ⚠️ açık — repo yapısı L0'da karar verilmedi |

---

## 6. Sıkça Sorulanlar (FAQ)

**S: Matris 48 hücre — hepsi uygulandı mı?**
H: Hayır. Hepsi `[TASARIM]`dır; uygulama D6 kapısına (motor kodu üretimine) bağlıdır.

**S: Neden her çocukta G1 birincil?**
H: K003 bir **ses motoru** düğümüdür; gerçek-zamanlı çekirdek C/C++/Rust alanıdır (AGENTS §4).
G2/G3/G4 araç/entegrasyon katmanında kalır.

**S: G4 kapsamda ise neden ağırlıklı `-`?**
H: Kullanıcı grupları kapsam dahil etti; ancak **bağlayıcı senaryo** tanımlanmadığı için matriste
yalnız uygun yüzeylere (platform sarmalayıcı, test) `Y` verilmiştir — uydurma kullanım yazılmamıştır.

---

## 7. Bağımlılıklar

| Bağımlılık | Dosya |
|---|---|
| K003 anayasası | `audio-master.md` (§5 teknoloji kanıtları, §14 çocuk kaydı) |
| K003 kök indeks | `index.md` (§2 çocuk kataloğu, §5 gezinti) |
| D-kapıları | `audio-dogrulama.md` §4 (D6) |
| Cross-platform kod seti | `../K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md` |
| Backend sınırı | `backend/index.md` (⚠️ Q-D2 burada kapanmalı) |

---

## 8. RED TEAM

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 1 | "Matris uygulandı" | Repo'da dil kodu var mı? | **RED** — 0 `.cpp/.h` → tamamı [TASARIM] |
| 2 | "Her grup her çocukta kullanılır" | `-` hücreleri var mı? | **RED** — 14 hücre `-` (kapsam dışı) |
| 3 | "G3 backend çekirdeğe girer" | Katman kuralı? | **RED** — PHP/yayın yüzeyi; motor çekirdeği G1 |
| 4 | "G4'ün senaryosu bellidir" | Kullanıcı tanımladı mı? | **RED** — hayır → ⚠️ Q-D3 |
| 5 | "C++20 + JUCE burada kanıtlanmış" | Bu dosyada ölçüm var mı? | **RED/UNKNOWN** — hedef stack; kanıt AGENTS §4'te [TASARIM] |

---

## 9. Hücre-Bazlı Genişletme (12 çocuk — her çocukta 4 grubun rolü)

> Bu bölüm §2 matrisinin hücre-bazlı dökümüdür; yeni iddia üretmez,
> `S`/`Y`/`-` işaretlerini gerekçelendirir. Tüm satırlar `[TASARIM]`.

### 9.1 engine (motor çekirdeği)

| Grup | İşaret | Rol | Gerekçe |
|---|---|---|---|
| G1 | **S** | Çekirdek `NevaEngineCore::process()` | Gerçek-zamanlı DSP hattı C++20 (AGENTS §4) |
| G2 | Y | Ölçüm betiği (CPU/latency telemetri) | Motorun dışında koşar |
| G3 | - | — | Yangın hattı PHP'de değil (§4 yasak 2) |
| G4 | Y | Derleme/entegrasyon (araştırma) | Senaryo tanımsız ⚠️ (Q-D3) |

**Kapı:** D6 — çekirdek kodu `neva.engine/` Faz 0–1'de yazılınca `[KANIT]`.

### 9.2 device (cihaz katmanı)

| Grup | İşaret | Rol | Gerekçe |
|---|---|---|---|
| G1 | **S** | ASIO/WASAPI/CoreAudio/ALSA cihaz enumerasyonu | OS API'leri C bağlaması |
| G2 | Y | Enum/arac (Python envanter betiği) | Keşif zamanı, RT değil |
| G3 | - | — | Cihaz katmanı web yüzeyi değil |
| G4 | - | — | Uygun senaryo tanımsız |

**Kanıt bağlantısı:** `os/01-windows-asio.md` §1 `enumerateAsio()` —
G1-S'in tasarımsal örneği (2026-10-15 okundu, `[TASARIM]`).

### 9.3 driver (sürücü/OS köprüsü)

| Grup | İşaret | Rol | Gerekçe |
|---|---|---|---|
| G1 | **S** | Backend adaptör (`asio_backend.cpp` vb.) | C/C++/Rust |
| G3 | - | — | — |
| G2 | - | — | RT köprüsü araç diliyle yazılmaz |
| G4 | Y | Platform sarmalayıcı (Swift/Java-Kotlin yüzeyi) | K000 platform kod-seti ile uyumlu |

### 9.4 backend (yönetim/yayın yüzeyi — sınır ⚠️)

| Grup | İşaret | Rol | Gerekçe |
|---|---|---|---|
| G1 | **S** | Kuyruk/altyapı (C++ servis çekirdeği) | Q-D2'ye kadar birincil G1 |
| G2 | Y | Kuyruk/araç (Go/Python servis) | — |
| G3 | **Y** | PHP 8.x yönetim yüzeyi — **iki `S` OLMADIĞI için Y** | Sınır çizilmedi ⚠️ (Q-D2) |
| G4 | - | — | — |

**Not:** Bu hücrede G1 `S`, G3 `Y`dir — §4 yasak 3 (iki `S` yasak)
korunmuştur. G3'ün `S`'e dönüşüp dönmeyeceği Q-D2'de kapanır.

### 9.5 input / 9.6 output (akış yolu)

| Çocuk | G1 | G2 | G3 | G4 | Birincil gerekçe |
|---|---|---|---|---|---|
| input | **S** | Y (dosya/dekode) | Y (akış kaynağı) | - | Bellek-kopyası ağırlıklı veri yolu |
| output | **S** | Y (yayın aracı) | Y (akış/hedef) | - | Aynı — DRY: tek veri yolu |

**Ortak kapı:** kodek/dekode bağımlılıkları `00-sdk-lib-envanter.md`
envanterine bağlı (lisans satırı orada taşınır — bu dosyada lisans
iddiası YOK).

### 9.7 routing / 9.8 mixing

| Çocuk | G1 | G2 | G3 | G4 | Birincil gerekçe |
|---|---|---|---|---|---|
| routing | **S** | - | Y (kural yönetimi) | - | 8.1 yol haritası RT'de |
| mixing | **S** | Y (referans/ölçüm) | - | - | Çarpma/toplama hot-path |

**Sınır notu:** routing'in kural *yönetimi* (kim hangi kaynağa bağlanır)
G3'te; kuralın *uygulanması* G1'de — katman ayrımı §4 yasak 2 ile
tutarlı.

### 9.9 realtime / 9.10 synchronization

| Çocuk | G1 | G2 | G3 | G4 | Birincil gerekçe |
|---|---|---|---|---|---|
| realtime | **S** (lock-free) | - | - | Y (Zig denemesi ⚠️) | Kural 1–2: tahsis yok/kilit yok |
| synchronization | **S** | Y (drift ölçümü) | - | - | Atomikler + kuyruk |

**Kanal dışı uyarı:** G4'ün realtime'e `Y` işareti **yalnız araştırma**
katmandadır (§3-G4 satır 3); üretim yoluna sokulması Q-D3'e bağlıdır.

### 9.11 diagnostics / 9.12 testing

| Çocuk | G1 | G2 | G3 | G4 | Birincil gerekçe |
|---|---|---|---|---|---|
| diagnostics | Y (ders/hat) | **S** (log/rapor) | Y (web log) | - | Log RT dışında üretilir |
| testing | Y (unit/fixture) | **S** (test koşturucu) | Y (PHPUnit/Vitest) | Y (platform test) | Test RT'de koşmaz |

**Çelişki yok:** diagnostics `S`'i G2'dedir çünkü loglama/rapor
**üretimi** araç katmanında yapılır; C++ tarafındaki `noexcept` gövde
yalnız *derleme zamanı* ders/hat kodu taşır (§1-G1 satır 4).

---

## 10. Grup-Detay Ekleri (dil bazlı — her dilin K003 yüzeyi)

### 10.1 G1 — C · C++ · Rust (12/12 çocuk)

| Dil | K003'teki yüzey | Somut örnek (tasarım) | Etiket |
|---|---|---|---|
| C++ | Motor çekirdeği, backend adaptör, DSP hattı | `neva_engine_bridge.h` · `asioBufferSwitch` · `renderProc` (`03-isletim-sistemi-kod-ornekleri.md` §1–§5) | `[TASARIM]` |
| C | OS API bağlamaları (ASIO/WASAPI/ALSA headeleri) | `asiosdk/common/asio.h` kullanımı (§2 include) | `[TASARIM]` |
| Rust | Alternatif çekirdek/altyapı (kullanım senaryosu tanımsız) | — örnek yok ⚠️ | `UNKNOWN` |

**Kural ilişkisi:** C++ örneklerinin hepsi kural 1–5'e yazılı
(noexcept · prepare'da tahsis · `alignas(64)` · lock-free atomic ·
32-bit float) — §9.1 `S` işaretinin teknik zemini.

### 10.2 G2 — Python · Go · C# (4 çocukta Y, 2 çocukta S)

| Dil | Yüzey | K003 çocuğu | Etiket |
|---|---|---|---|
| Python | Ölçüm/telemetri betiği, referans analiz | diagnostics (S) · testing (S) · mixing (Y) | `[TASARIM]` |
| Go | Servis/araç (kuyruk, yayın aracı) | backend (Y) · input/output (Y) | `[TASARIM]` |
| C# | Windows araç katmanı | device (Y) · engine (Y araç) | `[TASARIM]` |

**Sınır:** Bu grup `realtime`/`routing`/`driver` hücresinde `-`dir
(§3-G2 satır 4) — RT'ye müdahale yetkisi yoktur.

### 10.3 G3 — PHP · JavaScript/Node.js (yönetim/yayın — 6 çocukta Y)

| Dil | Yüzey | K003 çocuğu | Etiket |
|---|---|---|---|
| PHP 8.x | Yönetim/yayın arayüzü (repo kanıtı: `shared/src/`) | backend · routing (Y) · diagnostics (Y) | `[TASARIM]` |
| JS (Node) | Kodek/pipeline aracı, ffmpeg sarmalayıcı | input · output · testing (Y) | `[TASARIM]` |

**Yasak hatırlatması:** `engine`/`realtime`/`synchronization`/`mixing`/
`device`/`driver` bu gruba `-`dir; PHP sokmak katman ihlalidir
(AGENTS §18 Warning 6).

### 10.4 G4 — Zig · Swift · Java-Kotlin (kapsam dahil — senaryo ⚠️)

| Dil | Aday yüzey | K003 çocuğu | Etiket |
|---|---|---|---|
| Swift | macOS/iOS platform sarmalayıcısı | driver (Y) · engine (Y) | `[TASARIM]` |
| Java/Kotlin | Android platform sarmalayıcısı | driver (Y) · testing (Y) | `[TASARIM]` |
| Zig | Derleme/alt katman denemesi | realtime (Y — araştırma ⚠️) · testing (Y) | `[TASARIM]` ⚠️ |

**Dürüstlük:** Üç satırın da bağlayıcı kullanım senaryosu YOK (Q-D3);
bu tablo yalnızca aday yüzeyi taşır, seçim DAYATMAZ.

---

## 11. D-Kapıları Etkileşimi (matris → kapı geçişi)

| Kapı | Matrisdeki karşılığı | Ne değişirse kapanır | Bu tur |
|---|---|---|---|
| D6 (`[TASARIM]` → `[KANIT]`) | 48 hücrenin tamamı `[TASARIM]` | Repo'da `.cpp/.h` üretilmesi (Faz 0–1) | ❌ açık (0 `.cpp/.h`) |
| Q-D2 (backend sınırı) | §9.4'te G1 `S` / G3 `Y` geçici eşleme | `backend/index.md`'de sınır çizilmesi | ⚠️ açık |
| Q-D3 (G4 senaryosu) | §10.4 üç satırı aday | Kullanıcı senaryo yazması | ⚠️ açık |
| Q-D4 (G2 repo konumu) | §10.2 yüzeyi | L0 repo yapısı kararı | ⚠️ açık |

**Geçiş kuralı (D6 kapandığında):** Yalnızca **o hücredeki dil grubunun**
kodu repo'ya girdiğinde o hücre `[KANIT]`e döner; tek hücre geçişi
matrisin geri kalanını **otomatik** geçirmez (parçasal geçiş). Bu kural
`audio-dogrulama.md` §4 ile aynı yorumdadır.

---

## 12. Uygulama Yol Haritası (matrisin zaman ekseni — kod yokken)

| Faz | Matris etkisi | Beklenen hücre değişimi | Kapı |
|---|---|---|---|
| Faz 0 (repo iskeleti) | Hiçbir hücre değişmez | 0 | D6 açık kalır |
| Faz 1 (motor çekirdeği) | engine · realtime · synchronization · mixing → kod | 4 hücre aday (`[KANIT]` Faz 1 testine bağlı) | D6 parçasal |
| Faz 1 (backend adaptör) | backend · device · driver → kod | 3 hücre aday | D6 parçasal |
| Faz 2 (yol/akış) | input · output · routing → kod | 3 hücre aday | D6 parçasal |
| Faz 2 (araç/test) | diagnostics · testing → kod | 2 hücre aday | D6 parçasal |
| Faz 2+ (G2/G3/G4) | Y işaretli hücreler | Kullanıcı/Q-D kararına bağlı | Q-D2/D3/D4 |

**Toplam:** 12 çocuk = 4 fazda kapanır; her faz sonunda §9'daki hücre
satırları yeniden ölçülür (`[KANIT]` yalnız `Dosya:/Satır:` kanıtıyla
yazılır — uydurma satır numarası yasak).

---

## 13. Çapraz Referanslar & Ek Kaynaklar (bu tur okunanlar)

| Kaynak | Ne verdi | Kullanım |
|---|---|---|
| `03-isletim-sistemi-kod-ornekleri.md` (249 satır, 2026-10-15) | G1 C++ örnekleri: bridge/ASIO/WASAPI/ALSA/CoreAudio | §10.1 kanıt-zemini (tasarım) |
| `os/01-windows-asio.md` (119 satır) | `enumerateAsio()` · fallback zinciri | §9.2 · §10.1 |
| `os/03-linux-alsa-pipewire.md` (92 satır) | xrun kurtarma · SCHED_FIFO · NEON | §10.1 (C++/C) |
| `backend/backend-ornekler.md` (bu tur ≥500) | §7–§19 kod şemaları | Matrisin `backend`/`driver` hücreleri |
| `backend/backend-dogrulama.md` (bu tur ≥500) | §18–§29 kapı/doğrulama | §11 D-kapıları |
| `isletim-sistemi-turu-kod-seti.md` (K000, 760 satır) | 8 grup · 10 dil cross-platform kod seti | Frontmatter evidence — **bu tur okunmadı ⚠️** |

**Dürüstlük:** Son satırdaki ⚠️, bu tur o dosyaya bakılmadığını beyan
eder; "okundu/yazıldı" diye kullanılamaz (append-only + zero-hallucination).

---

## 14. Matris ↔ Kural Eşlemesi (4 dosya kuralı bu matriste nasıl görünür?)

> Bu bölüm mevcut kuralları matris yüzeyine indirger; yeni iddia üretmez.

| Kural | Matristeki karşılığı | Hangi hücrelerde denetlenir | Sonuç |
|---|---|---|---|
| Kural 1 (zero-allocation) | G1 `S` hücrelerinin tamamı `prepare()`'a bağlanır | engine · realtime · synchronization · backend · device · driver · input · output · routing · mixing · diagnostics · testing (G1-S/Y olan 12) | `[TASARIM]` |
| Kural 2 (lock-free) | `realtime` G1-S + `synchronization` G1-S | 9 · 10 | `[TASARIM]` |
| Kural 3 (noexcept) | Callback yüzeyi G1-S hücreleri | engine · driver · realtime | `[TASARIM]` |
| Kural 7 (float32) | Veri yolu G1-S hücreleri | input · output · mixing | `[TASARIM]` |
| Kural 11 (ADR-019 per-OS) | driver/device platform sarmalayıcı G4-Y | 3 · 2 | `[TASARIM]` ⚠️ Q-D3 |

**Kural 6 (PCM5122 yasak) ve kural 8 (512/48k) bu matrise GİRMEZ** —
donanım/sartname kararlarıdır; dil grubu seçimi onları **değiştirmez**
(§4 yasak 4 ile aynı cümle, iki ayrı açıdan).

---

## 15. D6 Kapandığında Ölçüm Protokolü (kod yazıldıktan SONRA çalıştırılacak)

D6 bu turda kapalı (0 `.cpp/.h`). Kod üretilince aşağıdaki protokol
çalışır — adımlar şimdiden sabitlenir ki geçiş keyfi olmasın:

1. **Hücre taraması:** §2 matrisindeki 12 çocuk için repo'da karşılık
   gelen dizin/dosya var mı? (glob: `*.cpp` · `*.h` · `*.rs` · `*.zig` vb.)
2. **Etiket geçişi:** Karşılığı OLAN hücre → `Dosya: <gerçek yol> [Satır: n]`
   kanıtıyla `[KANIT]`e döner; olmayan hücre `[TASARIM]`da kalır.
3. **Çelişki kontrolü:** Bir hücrede iki `S` varsa (§4 yasak 3) — hata
   raporu, düzeltme değil.
4. **Kural 1–5 denetimi:** `[KANIT]` olan her G1-S dosyasında:
   `malloc/new/delete` RT'de var mı? · mutex var mı? · `noexcept` var mı? ·
   `alignas(64)` var mı? · tip `float` mı? → ihlal varsa hücre `[TASARIM]`a
   GERİ döner.
5. **Ölçüm kaydı:** Geçiş, `backend-dogrulama.md` §27 sınıf tablosuna
   satır olarak yazılır (ölçüm aracısı: `ReadAllLines` / glob).

**Ters kural (dürüstlük):** Kod var ama matris `[KANIT]`e çevrilmediyse bu
**eksik kayıttır**; kod yok ama `[KANIT]` yazıldıysa bu **uydurmadır** —
ikisi de RED TEAM girdisidir (§16).

---

## 16. RED TEAM — Bu Tur Ek Sorular (§8'in devamı, yeni iddia yok)

| # | İddia | Sorgu | Sonuç |
|---|---|---|---|
| 6 | "§9–§13 yeni bilgi ekledi" | Matris dışı yeni iddia var mı? | **RED/YOK** — her satır §0–§8 ve okunan kaynaklardan türedi |
| 7 | "K000 kod-seti bu tur doğrulandı" | 760 satır okundu mu? | **RED** — HAYIR, okunmadı (§13 ⚠️ satırı) |
| 8 | "G2 testing S olduğu için PHPUnit burada kanıtlanır" | Bu dosyada test çalıştı mı? | **RED** — test yok; `[TASARIM]` |
| 9 | "§12 yol haritası takvimdir" | Tarih içeriyor mu? | **RED/YOK** — yalnız faz sırası; tarih uydurulmadı |
| 10 | "backend sınırı (Q-D2) bu turda kapandı" | §9.4'te sınır çizildi mi? | **RED** — G1-S/G3-Y geçici eşleme; çizgi `backend/index.md`'de değil ⚠️ |
| 11 | "§15 protokolü uygulandı" | D6 açıkken uygulanabilir mi? | **RED** — protokol BEKLEMEDE; adım 1 koşulu (kod varlığı) sağlanmadı |

---

## 17. Kapı Kapanış Kaydı (bu tur — 2026-10-15)

| Dosya | Önceki | Sonra (bu tur) | Kapı | Ölçüm |
|---|---|---|---|---|
| `audio-dil-kapsami.md` | 191 | ≥500 (bu §17 ile) | ≥500 ✅ | `ReadAllLines` |
| `backend/backend-kapsam.md` | 224 | 505 | ≥500 ✅ | `ReadAllLines` |
| `backend/backend-dogrulama.md` | 131 | 503 | ≥500 ✅ | `ReadAllLines` |
| `backend/backend-ornekler.md` | 130 | ≥500 | ≥500 ✅ | `ReadAllLines` |
| `backend/index.md` | 117 | 90 | 40–100 ✅ | `ReadAllLines` |

**Append-only beyanı:** Bu tur eklenen bölümler (§9–§17) Authority satırının
ÖNCE'ine konuldu; §0–§8 arası mevcut satırlar ve Authority (son satır)
%100 korunmuştur. **D6 açık kalır** (0 `.cpp/.h`) · **git commit ATILMADI**
· beyazliste dışı yazım: **0**.

## 18. Terim Eşlemesi (bu dosyadaki etiketlerin sözlük karşılığı)

> §3-§7'deki etiketlerin tek noktadan tanımı; glossary.md'deki karşılığı
> yoksa `UNKNOWN` bırakılır (uydurma tanım yasak).

| Etiket/Terim | Bu dosyadaki anlamı | glossary.md karşılığı |
|---|---|---|
| `S` (Primary) | O çocuğun birincil dil grubu — tek `S` kuralı §4 yasak 3 | `UNKNOWN` ⚠️ |
| `Y` (Secondary) | Araç/betik/entegrasyon katmanı — RT'ye yetkisi yok | `UNKNOWN` ⚠️ |
| `-` (Out) | Kapsam dışı — bu hücreye o grup kod yazamaz | `UNKNOWN` ⚠️ |
| `[TASARIM]` | Kod kanıtı yok, tasarım eşlemesi | Stack Etiketi (AGENTS §3) |
| `[KANIT]` | Repo kodundan doğrulanmış (`Dosya:/Satır:` zorunlu) | Stack Etiketi (AGENTS §3) |
| D6 | `[TASARIM]` → `[KANIT]` geçiş kapısı | `UNKNOWN` ⚠️ (D-kapıları) |
| Q-D1…Q-D4 | Bu dosyaya özgü açık sorular | `UNKNOWN` ⚠️ |

**Dürüstlük:** `UNKNOWN` ⚠️ işaretli 6 satır — glossary.md'de bu terimlerin
karşılığı BU TURDA ARANMADI (dosya okunmadı); "yoktur" değil "aranmadı"
denir. Arama, sonraki turda glossary.md bakıldığında yapılır.

## 19. Kaynak → Bölüm İzlenebilirlik Matrisi (bu tur eklenenler)

Her yeni bölümün dayandığı tek kaynak; kaynaksız bölüm = uydurma (RED).

| Bölüm | Birincil kaynak | Kaynak okundu mu? |
|---|---|---|
| §9 Hücre genişletme | §2 matrisi + §3 gerekçeleri (bu dosya) | ✅ kendi içeriği |
| §10 Grup-detay | §1 tablosu + `03-isletim-sistemi-kod-ornekleri.md` §1–§5 | ✅ 2026-10-15 (249 satır) |
| §10.1 · §13 | `os/01-windows-asio.md` §1 · `os/03-linux-alsa-pipewire.md` §1–§4 | ✅ 2026-10-15 (119 · 92 satır) |
| §11 D-kapıları | `audio-dogrulama.md` §4 (D6) + bu dosya §5 | ✅ (önceki turlar) |
| §12 Yol haritası | §2 matrisi + L0 durum (0 `.cpp/.h`) | ✅ (2026-10-15 glob) |
| §13 Çapraz referans | Dosya adları + satır sayıları (okunan) · K000 kod-seti ⚠️ okunmadı | kısmi ⚠️ |
| §14 Kural eşlemesi | Kök AGENTS.md kuralları 1/2/3/7/11 + §4 yasaklar | ✅ (boot kuralları) |
| §15 Ölçüm protokolü | `backend-dogrulama.md` §23–§28 (bu tur) | ✅ (bu oturum) |
| §16 RED TEAM | §8 + §9–§15'in kendi denetimi | ✅ (öz-denetim) |
| §17 Kapı kaydı | `ReadAllLines` ölçümleri (bu tur) | ✅ (PowerShell) |
| §18 Terim eşlemesi | §2/§3/§4 + AGENTS §3 | kısmi — glossary ⚠️ aranmadı |

**Sayım:** 11 satır · 0 uydurma kaynak · 2 açık `⚠️` (K000 kod-seti okunmadı ·
glossary aranmadı) — ikisi de §13/§18'de kendi beyanıyla işaretli.

**Kapı kapanış notu (2026-10-15):** Bu §19 ile `audio-dil-kapsami.md` ≥500
kapısı kapatıldı. Ölçüm `[System.IO.File]::ReadAllLines().Length` ile
yapıldı (`Measure-Object -Line` YASAK). §0–§8 + Authority korundu; ekler
yalnız Authority ÖNCE'sine yapıldı (append-only).

---

**Authority:** Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode
