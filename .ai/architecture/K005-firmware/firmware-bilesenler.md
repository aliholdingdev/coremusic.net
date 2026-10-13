---
node_id: K005-firmware/firmware-bilesenler.md
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.5 — 9 çocuk adı — okundu 2026-10-12"
  - "firmware-master.md §6 (bu üretim) — bileşen kataloğu — yazıldı 2026-10-13"
  - "ADR-097 §5.5 — mimari grubu — okundu 2026-10-12"
  - "K005-firmware/ disk ölçümü — 12 kök MD — 2026-10-13"
validation:
  - "2026-10-13 — §2 katalog 12 kök + 6 çocuk şemasını kapsıyor"
  - "2026-10-13 — §5'te seçilmiş teknoloji YOK (seçim onayı bekliyor) → UNKNOWN işaretli"
  - "2026-10-13 — §4 işlev sayısı F1–F14 = 14, her biri tek sahip"
open_questions:
  - "Hedef MCU ailesi seçilmedi → §4 bileşenleri genel kaldı (⚠️)"
  - "Kademe-3 teknoloji klasörleri (G4) band/ADR bekliyor (⚠️ RESERVED — ADR)"
  - "§5 T6 OTA uç noktası seçimi — sağlayıcı bilinmiyor (⚠️ UNKNOWN)"
group: mimari
---

# firmware-bilesenler — Bileşen Kataloğu

> Dayanak: `firmware-master.md` §6 · v2 §2.5. Bu dosya K005'in **bileşen envanteri**
> ve kademe-3 (teknoloji derin-dal) **planıdır**. Seçim içermaz — envanter + plan.

## §1 ELI15

> **Bileşenler iki katmanda dizilir: 12 kök belge (ne söylüyor) ve 9 çocuk
> modülü (cihazın hangi parçası). Kademe-3'te teknoloji eklenir — henüz yok.**

Katalog üç sütun taşır: **bileşen · sahibi çocuk · durum**. Durum ya `belge-yazıldı`
(şema bu üretimde) ya `dalga-3` ya `planlandı` (kademe-3). Bir bileşenin iki sahibi
olamaz; sahipsiz işlev diagnostis/security listesine yazılır (`firmware-kararlar.md` §5).

## §2 Kök bileşen kataloğu (12 MD)

| # | Bileşen (dosya) | Grup | Görev | Durum |
|---|---|---|---|---|
| 1 | `index.md` | mimari | Künye · çocuk kataloğu · §GRUPLAR | belge-yazıldı |
| 2 | `firmware-master.md` | mimari | Anayasa — omurga · iddialar · D1–D10 | belge-yazıldı |
| 3 | `firmware-kapsam.md` | mimari | İçinde / dışında / komşu sınırı | belge-yazıldı |
| 4 | `firmware-bagimliliklar.md` | mimari | Bağımlılık haritası + mimari↔kod | belge-yazıldı |
| 5 | `firmware-dogrulama.md` | kanit | Kriterler (D1–D10) + 26 iddia satırı | belge-yazıldı |
| 6 | `firmware-web-arastirma.md` | kanit | 48 iddia/URL kaydı | belge-yazıldı |
| 7 | `firmware-bilesenler.md` | mimari | Bu dosya — envanter + kademe-3 planı | belge-yazıldı |
| 8 | `firmware-kod-ornekleri.md` | kod | Gerçek repo örnekleri (Dosya/Satır) | belge-yazıldı |
| 9 | `firmware-kararlar.md` | mimari | Karar kaydı (ADR bağlantılı) | belge-yazıldı |
| 10 | `firmware-riskler.md` | kanit | Risk matrisi + azaltma | belge-yazıldı |
| 11 | `firmware-test-stratejisi.md` | kod | Test piramidi · HIL · gate'ler | belge-yazıldı |
| 12 | `firmware-evrensel-dil.md` | mimari | TR domain / EN teknik sözlük | belge-yazıldı |

## §3 Kademe-2 çocuk bileşenleri (6 çocuk × 6 MD = 36)

| # | Çocuk | Bileşen dosyaları (6) | Grup dağılımı | Durum |
|---|---|---|---|---|
| 1 | boot | `index` · `boot-kapsam` · `boot-dogrulama` · `boot-web-arastirma` · `boot-ornekler` · `boot-bilesenler` | mimari 3 · kanit 2 · kod 1 | belge-yazıldı |
| 2 | runtime | `index` · `runtime-kapsam` · `runtime-dogrulama` · `runtime-web-arastirma` · `runtime-ornekler` · `runtime-bilesenler` | mimari 3 · kanit 2 · kod 1 | belge-yazıldı |
| 3 | audio-control | `index` · `audio-control-kapsam` · `audio-control-dogrulama` · `audio-control-web-arastirma` · `audio-control-ornekler` · `audio-control-bilesenler` | mimari 3 · kanit 2 · kod 1 | belge-yazıldı |
| 4 | device-control | `index` · `device-control-kapsam` · `device-control-dogrulama` · `device-control-web-arastirma` · `device-control-ornekler` · `device-control-bilesenler` | mimari 3 · kanit 2 · kod 1 | belge-yazıldı |
| 5 | power-control | `index` · `power-control-kapsam` · `power-control-dogrulama` · `power-control-web-arastirma` · `power-control-ornekler` · `power-control-bilesenler` | mimari 3 · kanit 2 · kod 1 | belge-yazıldı |
| 6 | update | `index` · `update-kapsam` · `update-dogrulama` · `update-web-arastirma` · `update-ornekler` · `update-bilesenler` | mimari 3 · kanit 2 · kod 1 | belge-yazıldı |

Dalga-3 (bu üretimde yazılmadı — 6 MD şeması bekliyor): `diagnostics` · `security` · `testing`.

## §4 Firmware işlevsel bileşenleri (çocuk sahipliği)

| # | İşlevsel bileşen | Sahip çocuk | Sorumluluk | Bağımlı |
|---|---|---|---|---|
| F1 | Reset & ilk-stage yükleyici | boot | Vektör · bellek init · imza kontrolü | — |
| F2 | Clock/pin kurucusu | boot | Kristal/PLL · GPIO başlangıç yönü | F1 |
| F3 | Görev/ana döngü | runtime | Zamanlama · öncelik · idle | F1 |
| F4 | Kesme yöneticisi | runtime | ISR kısa tutma · kuyruk devri | F3 |
| F5 | Watchdog bekçisi | runtime | Periyodik besleme · reset nedeni | F3 |
| F6 | Bus denetçisi | device-control | I²C/SPI/UART serileştirme | F3 |
| F7 | GPIO/driver sürücüsü | device-control | Aç/kapat/ayarla komut yolları | F6 |
| F8 | Ses register/ayar katmanı | audio-control | Clock · mute · gain adımları | F6 |
| F9 | Güç durum makinesi | power-control | Uyku · kapanma · brown-out | F3 |
| F10 | Termal/akım izleyici | power-control | Eşik · güvenli kapanma tetiği | F9 |
| F11 | Kendini test (self-test) | diagnostics | Açılış/kendini denetme · log | F1–F10 |
| F12 | Güncelleme motoru | update | Manifest · A/B · rollback · sayaç | F1 |
| F13 | İmza/anahtar kasası | security | Secure boot · erişim kilidi | F1 · F12 |
| F14 | Test kıyaslama (HIL) | testing | Sabit senaryo · kabul kriteri | hepsi |

## §5 Teknoloji seçenek envanteri (SEÇİM YAPILMADI — yalnız seçenek listesi)

| # | Katman | Seçenekler (envanter) | Durum | Etiket |
|---|---|---|---|---|
| T1 | İşlemci ailesi | Cortex-M sınıfı · Cortex-A sınıfı · RISC-V · XMOS | SEÇİLMEDİ | ⚠️ UNKNOWN |
| T2 | RTOS | FreeRTOS · Zephyr · bare-metal ana döngü | SEÇİLMEDİ | ⚠️ UNKNOWN |
| T3 | Derleme/ivme (debug) | GCC-arm · Clang/LLVM · üretici IDE | SEÇİLMEDİ | ⚠️ UNKNOWN |
| T4 | Bellek/örtü | linker script · scatter file · OTA bölmeleri | SEÇİLMEDİ | ⚠️ UNKNOWN |
| T5 | İmza | imza algoritması + anahtar uzunluğu | SEÇİLMEDİ | ⚠️ UNKNOWN |
| T6 | OTA protokolü | A/B bölme · uç nokta/bulut sağlayıcı | SEÇİLMEDİ | ⚠️ UNKNOWN |
| T7 | Bus | I²C · SPI · UART · USB · I2S/TDM (ses) | SEÇİLMEDİ | ⚠️ UNKNOWN |

> **Kural:** Seçim `⚠️ UNKNOWN` işaretliyken sürüm/ölçü **uydurulmaz** (AGENTS §3).

## §6 Kademe-3 (G4 `teknoloji`) planı — bu üretimde AÇILMADI

```text
K005-firmware/{çocuk}/{teknoloji}/{teknoloji}-{rol}.md
  ├─ grup: teknoloji (ADR-097 §5.5/A — G4)
  ├─ örnek: runtime/freertos/freertos-gorev-modeli.md
  ├─ ön koşul: band rezervasyonu + ADR onayı
  └─ durum: RESERVED — ADR (bu üretimde açılmadı)
```

| # | Kademe-3 klasör (planlanan) | Eklenecek rol dosyaları | Ön koşul |
|---|---|---|---|
| 1 | `boot/{teknoloji}/` | `boot-{rol}.md` ×N | ADR + band |
| 2 | `runtime/{teknoloji}/` | `runtime-{rol}.md` ×N | ADR + band |
| 3 | `device-control/{teknoloji}/` | `device-control-{rol}.md` ×N | ADR + band |
| 4 | `audio-control/{teknoloji}/` | `audio-control-{rol}.md` ×N | ADR + band |
| 5 | `power-control/{teknoloji}/` | `power-control-{rol}.md` ×N | ADR + band |
| 6 | `update/{teknoloji}/` | `update-{rol}.md` ×N | ADR + band |

## §7 Bileşen kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Her bileşen tek bir çocuk tarafından sahiplenilir (iki sahip yok) | master §2 |
| 2 | Sahip olmayan işlev `diagnostics`/`security` (dalga-3) listesine yazılır | v2 §2.5 |
| 3 | Seçilmemiş teknoloji için sürüm/ölçü yazılmaz | AGENTS §3 |
| 4 | Kademe-3 dosyası yalnız ADR onayıyla açılır | ADR-097 §5.5 |
| 5 | Yeni bileşen = bu dosyaya satır + `index.md` §GRUPLAR'a dokunulmaz | şema bütünlüğü |

## §8 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| E1 | §2 = 12 satır (kök şema) | sayım | ✅ 12/12 |
| E2 | §3 = 6 satır + 36 dosya adı | sayım | ✅ 36/36 |
| E3 | §4 = 14 işlev tek sahip | okuma | ✅ 2026-10-13 |
| E4 | §5'te seçilmiş teknoloji yok | grep (`SEÇİLMEDİ` 7/7) | ✅ 2026-10-13 |
| E5 | Kademe-3 dosyası üretilmedi | dizin sayımı | ✅ 0 (planlandığı gibi) |

## §9 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | §5'teki 7 katman için seçim sırası ve sahibi kim? | `⚠️ UNKNOWN` |
| Q2 | Hedef MCU ailesi + frekans aralığı ne? | `⚠️ VERIFY REQUIRED` |
| Q3 | Kademe-3 band rezervasyonu (ADR-097) ne zaman onaylanır? | `⚠️ RESERVED — ADR` |

## §10 Bileşen × yaşam döngüsü matrisi (hangi bileşen hangi evrede çalışır)

| # | Bileşen | Açılış | Çalışma | Kip geçişi | Güncelleme | Kapanma |
|---|---|---|---|---|---|---|
| M1 | F1 Reset & ilk-stage | ✅ | — | — | ✅ (yeniden) | — |
| M2 | F2 Clock/pin kurucusu | ✅ | ✅ (yeniden ayar) | ✅ | ✅ | ✅ (kapat) |
| M3 | F3 Görev/ana döngü | ✅ | ✅ | ✅ | ✅ | ✅ |
| M4 | F4 Kesme yöneticisi | ✅ | ✅ | ✅ | ✅ (kısa) | ✅ (kapanış) |
| M5 | F5 Watchdog bekçisi | ✅ | ✅ | ✅ (asla kapatılmaz) | ✅ | ✅ (son besleme) |
| M6 | F6 Bus denetçisi | ✅ | ✅ | ✅ | ✅ | ✅ (kapanış) |
| M7 | F7 GPIO/driver | ✅ | ✅ | ✅ | ✅ | ✅ |
| M8 | F8 Ses register/ayar | ✅ (mute ile) | ✅ | ✅ | ✅ | ✅ (mute → kapat) |
| M9 | F9 Güç durum makinesi | ✅ | ✅ | ✅ | ✅ | ✅ |
| M10 | F10 Termal/akım | ✅ (kontrol) | ✅ | ✅ | ✅ | ✅ (tetik) |
| M11 | F11 Self-test | ✅ (asıl) | ⬜ (periyodik) | — | ✅ (kurulum sonrası) | — |
| M12 | F12 Güncelleme motoru | ✅ (boot'ta kontrol) | ⬜ (tetikleme) | — | ✅ | — |
| M13 | F13 İmza/anahtar | ✅ (zincir) | ✅ (erişim) | ✅ | ✅ (doğrulama) | ✅ |
| M14 | F14 HIL/test | ✅ (test kipi) | ✅ (test kipi) | ✅ | ✅ (test) | ✅ |

> `✅` = çalışır · `⬜` = koşullu çalışır · `—` = çalışmaz. Bu matris
> `firmware-test-stratejisi.md` §3'teki senaryo örtüsü için tabanıdır.

## §11 Bileşen arayüz sözleşmeleri (girdi · çıktı · hata)

| # | Bileşen | Girdi | Çıktı | Hata davranışı |
|---|---|---|---|---|
| C1 | F1 ilk-stage | reset kaynağı | devredilmiş durum | Güvenli yeniden başlatma |
| C2 | F2 clock/pin | hedef frekans tablosu | ayarlanmış saat/pin | Varsayılana düşme + işaret |
| C3 | F3 ana döngü | görev listesi | dönemli çağrı | Görev reddi sayacı |
| C4 | F4 kesme | IRQ numarası | kısa ISR + kuyruk | Kuyruk taşması sayacı |
| C5 | F5 watchdog | besleme periyodu | besleme / reset | Reset nedeni kaydı |
| C6 | F6 bus | çerçeve (adres+veri) | onay/yanıt | Tekrar sınırı + hata kodu |
| C7 | F7 GPIO/driver | komut (aç/kapat/ayarla) | pin durumu | Bilinmeyen komut reddi |
| C8 | F8 ses ayar | register+kısmi maske | hazır bayrağı | Kilitli bekleme → timeout |
| C9 | F9 güç makinesi | kip talebi | yeni kip | Geçersiz geçiş reddi |
| C10 | F10 termal/akım | ölçüm | eşik kararı | Eşik yoksa `UNKNOWN` + kısıtlama |
| C11 | F11 self-test | test listesi | sonuç + log | Bağlı testin iptali |
| C12 | F12 update | manifest | bölüm durumu | İmza hatası → reddet |
| C13 | F13 imza | blok + anahtar | geçerli/geçersiz | Geçersiz → açamama |
| C14 | F14 HIL | senaryo | rapor | Senaryo reddi rapora yazılır |

## §12 Bellek/örtü yerleşim planı (envanter — SEÇİM YOK, yalnız bölgeler)

| # | Bölge | İçerik | Büyüklük | Etiket |
|---|---|---|---|---|
| Z1 | Vektör tablosu | IRQ/reset vektörleri | sabit (çip belirler) | ⚠️ UNKNOWN |
| Z2 | `.text` | kod flash'da | ölçülmeli | ⚠️ UNKNOWN |
| Z3 | `.rodata` | sabit tablolar | ölçülmeli | ⚠️ UNKNOWN |
| Z4 | `.data` | başlangıç değeri olan veri | RAM | ⚠️ UNKNOWN |
| Z5 | `.bss` | sıfırlanacak veri | RAM | ⚠️ UNKNOWN |
| Z6 | yığın (stack) | ISR + görev yığınları | zirve ölçümü gerekli | ⚠️ UNKNOWN |
| Z7 | yığın dışı (heap) | RTOS varsa | **plan: kullanılmamalı** (sıfır tahsis kuralı) | KURAL |
| Z8 | OTA A/B bölümleri | iki görüntü + sayaç alanı | flash/2 | ⚠️ UNKNOWN |
| Z9 | NVM ayar alanı | kalıcı ayarlar | kayıt boyu × kopya | ⚠️ UNKNOWN |
| Z10 | Log tamponu | döngüsel log | sabit boy | ⚠️ UNKNOWN |

## §13 Kurulum (init) sırası — bağımlılık zincirine göre

| # | Adım | Bileşen | Koşul | Başarısızlıkta |
|---|---|---|---|---|
| I1 | Reset vektörü | F1 | her zaman | Güvenli döngü |
| I2 | Bellek init (BSS/data) | F1 | her zaman | Dur ve işaretle |
| I3 | Saat/pin ilk ayar | F2 | F2 tamam | Varsayılana düş |
| I4 | Güç/termal başlangıç | F9 · F10 | F3 öncesi | Kapanma kipine git |
| I5 | Kesme altyapısı | F4 | F3 öncesi | Devre dışı ana döngü |
| I6 | Ana döngü/görev açılışı | F3 | I1–I5 | Reset (watchdog) |
| I7 | Bus/driver açılışı | F6 · F7 | F3 içinde | Cihaz yok bayrağı |
| I8 | Ses ayar katmanı (mute) | F8 | F6 içinde | Mute'da kal |
| I9 | Self-test | F11 | I8 sonrası | Hata logu + kısma |
| I10 | Update kontrolü | F12 | İmza zinciri ile | Rollback |
| I11 | Normal çalışma | hepsi | I9 geçtiyse | Dalga-3 kuralı |

## §14 Kademe-3 genişleme senaryoları (ne zaman, hangi dosya)

| # | Tetikleyici | Açılacak klasör | Karar dosyası | Öncelik |
|---|---|---|---|---|
| Y1 | RTOS seçimi onaylanır | `runtime/{teknoloji}/` | `firmware-kararlar.md` | Yüksek |
| Y2 | MCU ailesi seçilir | `boot/{teknoloji}/` | `firmware-kararlar.md` | Yüksek |
| Y3 | İmza algoritması seçilir | `security/{teknoloji}/` | ADR | Yüksek |
| Y4 | OTA sağlayıcısı seçilir | `update/{teknoloji}/` | ADR | Orta |
| Y5 | Bus seti kesinleşir | `device-control/{teknoloji}/` | `firmware-kararlar.md` | Orta |
| Y6 | Ses DAC kesinleşir | `audio-control/{teknoloji}/` | ADR-038 + ADR | Yüksek |
| Y7 | Güç kipleri netleşir | `power-control/{teknoloji}/` | `firmware-kararlar.md` | Orta |
| Y8 | Test ortamı seçilir | `testing/{teknoloji}/` | `firmware-test-stratejisi.md` | Orta |
| Y9 | Log/telemetri şekli | `diagnostics/{teknoloji}/` | ADR | Düşük |

## §15 Bileşen durum sözlüğü

| Durum | Anlamı | Kullanım yeri |
|---|---|---|
| `belge-yazıldı` | Şema/plan bu üretimde yazıldı, kod yok | §2 · §3 sütunu |
| `dalga-3` | Üçüncü üretim dalgasında ele alınacak | §4 · §14 |
| `planlandı` | Kademe-3 planlandı, ADR bekliyor | §6 |
| `SEÇİLMEDİ` | Seçenek listesi var, seçim yok | §5 |
| `⚠️ UNKNOWN` | Değer bilinmiyor — uydurulmaz | §5 · §12 |
| `⚠️ RESERVED — ADR` | Band/klasör ADR onayı bekliyor | §6 · §9 Q3 |
| `REDDEDİLİR` | Kural gereği kabul edilmez (ör. PCM5122) | `firmware-kararlar.md` |

## §16 Çapraz referans (bu dosyanın bağlandığı kök dosyalar)

| # | Bölüm | Giden dosya | Amaç |
|---|---|---|---|
| R1 | §2 | `index.md` §GRUPLAR | Grup eşlemesi |
| R2 | §3 | `firmware-master.md` §6 | Şema sahibi |
| R3 | §4 | `firmware-kapsam.md` §9 | Çocuk kapsamı |
| R4 | §5 | `firmware-web-arastirma.md` §3 | Seçenek iddiaları |
| R5 | §6 | `firmware-kararlar.md` §5 | ADR bağlantısı |
| R6 | §10 | `firmware-test-stratejisi.md` §3 | Evre örtüsü |
| R7 | §11 | `firmware-dogrulama.md` §1 | Sözleşme kriteri |
| R8 | §12 | `firmware-bagimliliklar.md` §4 | Bellek bağı |
| R9 | §13 | `firmware-kod-ornekleri.md` | Init kodu örneği |
| R10 | §14 | `firmware-riskler.md` §2 | Gecikme riski |
| R11 | §15 | `firmware-evrensel-dil.md` §6 | Durum terimleri |
| R12 | §17 | `firmware-master.md` §3 | Çapraz kural |

## §17 Bileşen anti-pattern'leri (envanterle çelişen davranışlar)

| # | Yanlış | Doğru | Dayanak |
|---|---|---|---|
| A1 | İki bileşeni tek çocuk koduna yazmak | Böl: §4 sahipliği | §7 kural 1 |
| A2 | Sahipsiz işlevi sessizce eklemek | diagnostics/security listesine | §7 kural 2 |
| A3 | Seçilmemiş teknolojiye sürüm yazmak | `⚠️ UNKNOWN` kalır | §7 kural 3 |
| A4 | Kademe-3 klasörünü onaysız açmak | ADR + band beklenir | §7 kural 4 |
| A5 | Yeni kök dosya eklemek | 12 sabittir; içerik genişletilir | §2 + KURAL 1 |
| A6 | Heap'ten sürekli tahsis | Z7 kuralı: tahsis yok | §12 |
| A7 | Init sırasını değiştirmek | §13 zinciri korunur | §13 |
| A8 | Sözleşmeyi kod içine gömmek | §11 tablosu tek referans | §11 |
| A9 | Durum kelimesini serbest uydurmak | §15 sözlüğü | §15 |
| A10 | Test örtüsünü bileşensiz tasarlamak | §10 matrisi taban | §10 |

## §18 Özet (bu dosyanın sayısal özeti)

| # | Sayı | Değer |
|---|---|---|
| 1 | Kök bileşen | 12 |
| 2 | Kademe-2 çocuk | 9 (6 şema yazıldı · 3 dalga-3) |
| 3 | Kademe-2 dosya (planlanan) | 54 (9 × 6) — bu üretimde yazılan 36 |
| 4 | İşlevsel bileşen | 14 (F1–F14) |
| 5 | Teknoloji katmanı (seçimsiz) | 7 (T1–T7) |
| 6 | Arayüz sözleşmesi | 14 (C1–C14) |
| 7 | Bellek bölgesi | 10 (Z1–Z10) |
| 8 | Init adımı | 11 (I1–I11) |
| 9 | Kademe-3 tetikleyici | 9 (Y1–Y9) |
| 10 | Açık soru | 3 (Q1–Q3) |

## §19 Çocuk-bazlı bileşen detayı (9 çocuk — kendi satır kümesi)

> Her alt bölüm §3'teki çocuk ile §4'teki F-satırlarını birleştirir; kademe-3
> klasörü açıldığında bu tablolar `K005-firmware/{çocuk}/` altındaki dosyalara
> taşınır (kopya değil, **bağ** ile — tek yazıcı korunur).

### §19.1 boot

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| b1 | Reset yakalayıcı | F1 | reset kaynağı | ilk PC değeri | [TASARIM] |
| b2 | Bellek init (BSS/data) | F1 | bölüm tablosu | hazır RAM | [TASARIM] |
| b3 | Clock/pin ilk ayar | F2 | kristal/PLL tablosu | çalışan saat | [TASARIM] |
| b4 | Vektör bağlama | F4 öncesi | IRQ listesi | bağlı tablo | [TASARIM] |
| b5 | İlk-stage son kontrol | F11 öncesi | self-test listesi | bayrak | [TASARIM] |
| b6 | Boot log (kısa) | F11 | olay | sabit alan | dalga-3 |
| b7 | İmza zinciri devri | F13 | bölüm başlığı | geçerli/geçersiz | dalga-3 |
| b8 | Rollback sayacı okuma | F12 | sayaç | bölüm seçimi | [TASARIM] |

### §19.2 runtime

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| r1 | Ana döngü/super-loop | F3 | görev listesi | dönemli çağrı | [TASARIM] |
| r2 | Zamanlayıcı (tick) | F3 | saat kaynağı | tick | [TASARIM] |
| r3 | Kesme yönlendirici | F4 | IRQ vektörü | ISR çağrısı | [TASARIM] |
| r4 | Kritik bölüm koruması | F4 | bayrak | giriş/çıkış | [TASARIM] |
| r5 | Kuyruk (ISR → döngü) | F4 | olay | döngüde iş | [TASARIM] |
| r6 | Watchdog besleme | F5 | periyot | kick/reset | [TASARIM] |
| r7 | Idle/kip köprüsü | F3 → F9 | boş zaman | düşük güç talebi | [TASARIM] |
| r8 | Ölçüm sayacı | F3 | başlangıç/bitiş | süre (µs) | [TASARIM] |

### §19.3 device-control

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| d1 | GPIO erişim katmanı | F7 | pin+değer | durum | [TASARIM] |
| d2 | I²C transferi | F6 | adres+veri | yanıt | [TASARIM] |
| d3 | SPI transferi | F6 | CS+veri | yanıt | [TASARIM] |
| d4 | UART alım kuyruğu | F6 | bayt | paket | [TASARIM] |
| d5 | Komut ayrıştırıcı | F7 | ham komut | niyet+argüman | [TASARIM] |
| d6 | Aralık/onay denetimi | F7 | argüman | kabul/red | [TASARIM] |
| d7 | Bus sahiplik kilidi | F6 | istek | sahip | [TASARIM] |
| d8 | Driver kip tablosu | F7 | kip | pin/register seti | [TASARIM] |

### §19.4 audio-control

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| a1 | Register yazma katmanı | F8 | adres+maske | onay | [TASARIM] |
| a2 | Clock kilit bekleme | F8 | hedef oran | hazır bayrağı | [TASARIM] |
| a3 | Mute sırası (aç/kapa) | F8 | yön | tamamlandı | [TASARIM] |
| a4 | Gain adımlama | F8 | dB adımı | register değeri | [TASARIM] |
| a5 | Kanal eşleme tablosu | F8 | mantıksal kanal | fiziksel kanal | [TASARIM] |
| a6 | Hazırlık sorgusu | F8 → K003 | — | bayrak | [TASARIM] |
| a7 | Kip geçiş doğrulama | F8 | yeni kip | kabul/red | [TASARIM] |
| a8 | Ayar günlüğü (audit) | F11 bağlantılı | değişiklik | kayıt | dalga-3 |

### §19.5 power-control

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| p1 | Güç durum makinesi | F9 | talep | yeni kip | [TASARIM] |
| p2 | Uyku giriş/çıkış | F9 | kip | pin/register seti | [TASARIM] |
| p3 | Kapanma sıralayıcı | F9 | tetik | sıralı kapatma | [TASARIM] |
| p4 | Brown-out yakalayıcı | F10 | eşik olayı | güvenli dur | [TASARIM] |
| p5 | Akım izleyici | F10 | ölçüm | kısma/kapama | [TASARIM] |
| p6 | Termal izleyici | F10 | sıcaklık | kısma/kapama | [TASARIM] |
| p7 | Reset nedeni yazıcı | F5 bağlantılı | neden | NVM kaydı | [TASARIM] |
| p8 | Son-durum flush | hepsi | kapanış | temiz bitiş | [TASARIM] |

### §19.6 diagnostics (dalga-3)

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| g1 | Power-on self-test | F11 | test listesi | sonuç | dalga-3 |
| g2 | Perifer denetimi | F11 | kayıt | geçti/kaldı | dalga-3 |
| g3 | Log yazıcı (döngüsel) | F11 | olay | tampon | dalga-3 |
| g4 | Rapor sunucu | F11 | kayıt | rapor paketi | dalga-3 |
| g5 | Test kipi anahtarı | F7 | istek | kip | dalga-3 |
| g6 | İstatistik sayacı | F3 | olay | sayaç | dalga-3 |

### §19.7 update

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| u1 | Manifest ayrıştırıcı | F12 | manifest | alanlar | [TASARIM] |
| u2 | Bölme seçici | F12 | flash haritası | hedef bölüm | [TASARIM] |
| u3 | İndirme/doldurma | F12 | paket | bölüm içeriği | [TASARIM] |
| u4 | Bütünlük+imza denetimi | F13 bağlantılı | imza | geçerli/geçersiz | [TASARIM] |
| u5 | Geçiş (switch) yazılımı | F12 | hazır bayrağı | sayaç++ | [TASARIM] |
| u6 | Rollback denetleyici | F12 · F1 | sayaç | eskiye dönüş | [TASARIM] |
| u7 | Yarıda kesme kurtarma | F12 · F1 | güç durumu | kaldığı yerden | [TASARIM] |

### §19.8 security (dalga-3)

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| s1 | Zincir doğrulayıcı | F13 | ROM→boot→app | zincir sonucu | dalga-3 |
| s2 | Anahtar kasası | F13 | anahtar no | erişim/red | dalga-3 |
| s3 | Yetki denetleyici | F7 bağlantılı | komut seviyesi | kabul/red | dalga-3 |
| s4 | Anti-rollback | F12 bağlantılı | sürüm | kabul/red | dalga-3 |
| s5 | Kayıt imzalayıcı | F11 bağlantılı | log bloğu | imza | dalga-3 |
| s6 | Güvenli silme | F13 | alan | silindi | dalga-3 |

### §19.9 testing (dalga-3)

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| t1 | Test koşucusu | F14 | senaryo | sonuç | dalga-3 |
| t2 | Sahte (stub) sürücü | F6/F7 | komut | kayıtlı yanıt | dalga-3 |
| t3 | HIL kıyaslama | F14 | donanım | rapor | dalga-3 |
| t4 | Eşik denetleyici | F14 | ölçümler | geç/kal | dalga-3 |
| t5 | Rapor yazar | F14 | sonuç | rapor dosyası | dalga-3 |

## §20 Test edilebilirlik ve ölçüm sözleşmeleri

| # | Bileşen grubu | Nasıl test edilir | Ne ölçülür | Eşik |
|---|---|---|---|---|
| TS1 | F1 boot | Bölüm/hex inceleme + hedefde | açılış süresi | ⚠️ UNKNOWN (hedef yok) |
| TS2 | F2 clock/pin | Saat ölçüm noktası | frekans sapması | ⚠️ UNKNOWN |
| TS3 | F3 ana döngü | Sahte zaman kaynağı | dönem süresi | ⚠️ UNKNOWN |
| TS4 | F4 kesme | Yazılım tetikleme | ISR süresi | ⚠️ UNKNOWN |
| TS5 | F5 watchdog | Zaman aşımı zorlama | kurtarma başarısı | %100 |
| TS6 | F6 bus | Sahte cihaz (stub) | hata/deneme oranı | ⚠️ UNKNOWN |
| TS7 | F7 GPIO | Pin okuma/yazma döngüsü | yanılgı | 0 |
| TS8 | F8 ses ayar | Register geri okuma | uyuşmazlık | 0 |
| TS9 | F9 güç | Kip geçişi senaryosu | geçiş başarısı | %100 |
| TS10 | F10 termal | Sahte sensör | tepki gecikmesi | ⚠️ UNKNOWN |
| TS11 | F11 self-test | Bilinçli hata enjeksiyonu | yakalama oranı | %100 |
| TS12 | F12 update | Sahte güç kesme (10 durum) | kurtarma | %100 |
| TS13 | F13 imza | Yanlış imza denemesi | reddetme | %100 |
| TS14 | F14 HIL | Gerçek donanım | tüm eşikler | `firmware-test-stratejisi.md` §5 |

## §21 Bileşen → grup → dosya üçlü matrisi

| # | Bileşen grubu | Grup | Bu şemadaki dosya | Kademe-2 karşılığı |
|---|---|---|---|---|
| G1 | boot (b1–b8) | mimari | `firmware-bilesenler.md` §19.1 | `boot/bilesenler` |
| G2 | runtime (r1–r8) | mimari | `firmware-bilesenler.md` §19.2 | `runtime/bilesenler` |
| G3 | device-control (d1–d8) | mimari | `firmware-bilesenler.md` §19.3 | `device-control/bilesenler` |
| G4 | audio-control (a1–a8) | mimari | `firmware-bilesenler.md` §19.4 | `audio-control/bilesenler` |
| G5 | power-control (p1–p8) | mimari | `firmware-bilesenler.md` §19.5 | `power-control/bilesenler` |
| G6 | update (u1–u7) | mimari | `firmware-bilesenler.md` §19.7 | `update/bilesenler` |
| G7 | diagnostics (g1–g6) | kanit | dalga-3 dosyası | `diagnostics/index` |
| G8 | security (s1–s6) | kanit | dalga-3 dosyası | `security/index` |
| G9 | testing (t1–t5) | kod | dalga-3 dosyası | `testing/index` |

## §22 Altın kurallar (bu dosyanın bağlayıcı özeti)

1. **Tek sahiplik:** her bileşenin tek bir sahibi vardır; ikinci sahip çıkarsa
   `firmware-kararlar.md` §5'e karar satırı yazılır, dosya sessizce bölünmez.
2. **Seçimsizlik:** §5'teki 7 katman `SEÇİLMEDİ` kalır; seçilmeyen teknolojiye
   sürüm/ölçü/performans değeri yazılmaz (Zero-Hallucination).
3. **Kademe-3 kapısı:** `K005-firmware/{çocuk}/{teknoloji}/` yalnız ADR + band
   onayıyla açılır; bu üretimde 0 klasör açıldı (ölçüm §8 E5).
4. **Sözleşme:** her bileşenin girdi/çıktı/hata satırı §11'dedir; kod içine
   gizli sözleşme yazılmaz.
5. **Ölçüm:** §20'de `⚠️ UNKNOWN` olan eşik, hedef donanım seçilmedikçe
   uydurulmaz; eşik yalnız `firmware-test-stratejisi.md` §5'e yazılır.
6. **Örtüşme:** aynı iş iki bileşende geçiyorsa tek yazıcı seçilir
   (`firmware-kapsam.md` §17) — bu dosya yalnız envanteri taşır.
7. **Güncelleme:** yeni bileşen = bu dosyaya satır; `index.md` §GRUPLAR ve dosya
   adları değiştirilemez (KURAL 1).

## §23 Hata kodu ↔ bileşen eşlemesi (TASLAK — nihai tablo dalga-3)

| # | Hata alanı (taslak) | Üreten bileşen | Davranış | Sahip |
|---|---|---|---|---|
| H1 | `E_BOOT_VEC` | F1 | Güvenli döngü + işaret | boot |
| H2 | `E_BOOT_CLK` | F2 | Varsayılana düşme | boot |
| H3 | `E_RT_TICK` | F3 | Tick kaynağı yeniden kur | runtime |
| H4 | `E_RT_ISR_TIME` | F4 | ISR süresi ihlali sayacı | runtime |
| H5 | `E_RT_WDT` | F5 | Reset + neden kaydı | runtime |
| H6 | `E_BUS_TIMEOUT` | F6 | Tekrar sınırı → hata | device-control |
| H7 | `E_BUS_NACK` | F6 | Cihaz yok bayrağı | device-control |
| H8 | `E_CMD_RANGE` | F7 | Komut reddi | device-control |
| H9 | `E_CMD_UNKNOWN` | F7 | Komut reddi + log | device-control |
| H10 | `E_AUD_LOCK` | F8 | Kilidi bekle → timeout | audio-control |
| H11 | `E_AUD_MISMATCH` | F8 | Geri okuma uyuşmazlığı | audio-control |
| H12 | `E_PWR_BROWNOUT` | F10 | Güvenli kapanma | power-control |
| H13 | `E_PWR_THERM` | F10 | Kısma → kapama | power-control |
| H14 | `E_UPD_SIG` | F13 | Reddet + eski bölüm | update |
| H15 | `E_UPD_HALF` | F12 | Kurtarma (kaldığı yerden) | update |
| H16 | `E_SEC_CHAIN` | F13 | Açamama (güvenli dur) | security |
| H17 | `E_TST_FAIL` | F14 | Rapor + geç/kal | testing |
| H18 | `E_DIAG_TEST` | F11 | Bağlı testi iptal | diagnostics |

> Bu alan adları **taslaktır** — `firmware-evrensel-dil.md` §6 nihai tabloyu
> yayımlayana kadar çoğaltılmaz; kodda kullanılmadan önce dalga-3 kararı gerekir.

## §24 Bakım ve sürüm notları

| # | Not | Değer | Tarih |
|---|---|---|---|
| N1 | Bu dosyanın ilk üretimi | 12 kök MD şeması içinde | 2026-10-12 |
| N2 | Genişletme (§10–§24) | envanter + plan korunarak | 2026-10-13 |
| N3 | Dosya adı değişikliği | 0 (KURAL 1) | 2026-10-13 |
| N4 | Kademe-3 klasör açılışı | 0 | 2026-10-13 |
| N5 | Seçim (teknoloji) eklenmedi | 0 — `SEÇİLMEDİ` 7/7 | 2026-10-13 |
| N6 | URL ekleme | 0 — bu dosyanın kanıt tipi envanterdir | 2026-10-13 |
| N7 | Silinen satır | 0 | 2026-10-13 |
| N8 | Sonraki gözden geçirme | dalga-3 başlangıcında | ⏳ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
