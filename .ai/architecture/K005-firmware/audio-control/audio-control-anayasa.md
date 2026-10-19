---
node_id: K005-firmware/audio-control/audio-control-anayasa
node_type: module
status: draft
parent_id: K005-firmware/audio-control
children: []
depends_on:
  - K005-firmware
  - K005-firmware/boot
evidence:
  - "K005-firmware/audio-control/index.md — okundu 2026-10-14"
  - "K005-firmware/audio-control/audio-control-kapsam.md — okundu 2026-10-14"
  - "K005-firmware/audio-control/audio-control-dogrulama.md §D — okundu 2026-10-14"
  - "K005-firmware/audio-control/audio-control-ornekler.md — okundu 2026-10-14"
  - "K005-firmware/boot/boot-anayasa.md — iskelet referansı — okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md — bayrak tanım referansı — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §9.4 · §15.3 · §17 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §19.4 · §23 — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §4-§7 · §9.3 · §20 · §21 — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §3 · §5 · §10 — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md §2 · §21 — okundu 2026-10-14"
  - "K005-firmware/firmware-evrensel-dil.md §2-§5 — okundu 2026-10-14"
  - "K005-firmware/firmware-kararlar.md §2 — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "kime ait: K003 mü K005 mı register'ı yazar? (bagimliliklar §4/7) — ÇÖZÜLMEDİ, taşındı"
  - "MCU seçimi kesin değil (XMOS XU316 ↔ STM32 ↔ ESP32) — firmware-master.md §10 Q4 — UNKNOWN"
  - "K003 sınırının kesin teyidi bekleniyor — firmware-master.md §10 Q5 — ⚠️ VERIFICATION REQUIRED"
  - "≤1200 ms boot→hazır atıf zinciri çelişkili (bkz. §15 T3) — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# audio-control-anayasa — Ses Donanımı Ayarı Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Hoparlörden ses gelmeden önce donanımın ayarlanması gerekir:
> örnekleme hızı, kanal düzeni, açılışta susma (mute) sırası. Bu dosya
> audio-control alt-modülünün **kurallarını ve sınırlarını** koyar; arayüz
> sözleşmeleri `audio-control-surucu.md`, uygulama kalıpları
> `audio-control-ornekler.md` içindedir. Repo'da (2026-10-13 ölçümü)
> derlenebilir firmware kodu **YOKTUR** — buradaki her kural
> **sözleşme düzeyindedir**; kod kanıtı olmadan "uygulandı" yazılmaz.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | audio-control alt-modülünün anayasası (kural + sınır) | K005-firmware/audio-control |
| Kapsam içi | DAC/ADC kip register'ları — **ayar**, akış değil | evet (A1) | audio-control |
| Kapsam içi | Clock ağacı: master clock oranı, kilitleme bekleme | evet (A2) | audio-control |
| Kapsam içi | Mute/mute-seq: açılış-kapanış sırası, pop koruması | evet (A3) | audio-control |
| Kapsam içi | Gain basamağı: dijital/analog adımlar, üst sınır kilidi | evet (A4) | audio-control |
| Kapsam içi | Kanal eşleme: fiziksel ↔ mantıksal kanal tablosu | evet (A5) | audio-control |
| Kapsam içi | Kip sorgusu: donanım hazır/yok hazır bayrağı (K003'e) | evet (A6) | audio-control |
| Kapsam dışı | Akış verisi işleme (buffer taşıma, örnekleme akışı) | hayır → O2/O3 (kapsam A7) | K003-audio |
| Kapsam dışı | DSP zinciri algoritması (EQ/reverb/konvolüsyon) | hayır → K004-dsp | K004 |
| Kapsam dışı | Güç devresi, röle fiziksel sürme, PCB | hayır → K001-donanım · tetik power-control | K001 · power-control |
| Kapsam dışı | I2C fiziksel sürücüsü ve register erişim yolu | hayır → device-control (§7) | device-control |
| Kapsam dışı | Boot zinciri, firmware yükleme, imza | hayır → K005-firmware/boot | boot |

> **Sınır notu (kaynak: firmware-kapsam.md §9.4 · §15.3):** audio-control
> *ayar* yapar, *akış taşımaz*. A6 kip sorgusu K003'e sorulan bir bayraktır;
> register'ı kimin yazdığı açık sorudur (§14 Q1) — bu dosya cevap üretmez.

## §2 Üç-Soru Sınır Testi

Her yeni kural/özellik bu üç sorudan geçmelidir; biri "hayır" ise kapsam dışıdır.

| # | Soru | "Evet" şartı | İhlal sonucu |
|---|---|---|---|
| S-A | Bu iş ses **ayarını** (register/clock/mute/gain/eşleme) değiştiriyor mu? | yalnız bu alanlar içindeyse | kapsam ihlali → reddet |
| S-B | Bu iş **akış verisi** taşıyor mu (örnek kopyalama, buffer doldurma)? | taşıyorsa O2/O3'e devret | K003 sınırı ihlali |
| S-C | Bu iş **gerçek donanım/PCB** veya **DSP algoritması** mı? | K001/K004'e devret | komşu ihlali |

**Test kuralı (T1):** Üç soruyu geçemeyen iş bu modülde yazılmaz; komşu
düğüme devredilir ve devir §9 tablosunda kayda geçer.

## §3 Ayar Akışı Kuralları (Register → Clock → Mute)

Kaynak: firmware-kapsam.md §9.4 A1–A3 · audio-control-kapsam.md (sınır kuralları).

| # | Kurallar | Aşama | Zorunlu sıra | İhlal sonucu |
|---|---|---|---|---|
| F1 | Register yalnız **ayar** için yazılır; akış verisine dokunulmaz (A1) | 1 · Register | önce kip yazımı | akış ihlali → reddet |
| F2 | Clock kilidi oturmadan ses yolu açılmaz (A2) | 2 · Clock | kilitleme bekleme bitmeden devam YASAK | kilitlenmeden çıkış → hata modu |
| F3 | Mute açılmadan clock/register değişikliği tamamlanmaz; pop koruması sırası bozulmaz (A3) | 3 · Mute | mute-seq sonunda açıl | pop/hasar riski (§9 R8) |
| F4 | Gain üst sınırı kilitlidir; aşılan değer iddia edilmez (A4) | 4 · Gain | mute açıkken değişmez | doyump/hasar → mute'a dön |
| F5 | Kanal eşleme tablosu tek tanımdır; çifte tablo yasak (A5) | 5 · Eşleme | tablo tek kaynak | yanlış kanal → geri okuma uyuşmazlığı |
| F6 | Kip sorgusu (A6) yalnız okunur; sorgu yazma işlemine dönüşmez | 6 · Sorgu | salt okunur | K003 sözleşme ihlali |

**Akış kuralı (A1):** Sıra **register → clock → mute → gain → eşleme → sorgu**
şeklindedir; geriye sıçrama (örn. mute açılmadan gain artışı) YASAKTIR.
Bu sıra anayasaldır — değişikliği §12 kuralı izler.

## §4 Anayasal İlkeler (P1–P12)

| # | İlke | Dayanak | Kaynak |
|---|---|---|---|
| P1 | Ayar ≠ akış: audio-control yalnız ayar yapar | kapsam A7 dışı | firmware-kapsam.md §9.4 |
| P2 | Belirsiz ara durum YASAK: her aşama ya geçer ya tanımlı hata modu | boot-anayasa A1 iskeleti | boot/boot-anayasa.md §2 |
| P3 | Clock kilidi oturmadan ses yolu açılmaz | A2 kilitleme bekleme | firmware-kapsam.md §9.4 |
| P4 | Açılışta mute yüksek seviyede (pop koruması) | S1 | firmware-kapsam.md (senaryo tablosu S1) |
| P5 | Gain üst sınırı kilitli; aşım yasağı | A4 | firmware-kapsam.md §9.4 |
| P6 | Tek register yazıcısı (tek sahip) — çift yazım yasak | açık soru Q1 çözülene kadar | audio-control/index.md open_questions |
| P7 | RT yolunda bellek ayırma/kilit yok; callback noexcept | K005 RT kuralları 1–3 | sibling dokümanlar (bagimliliklar/bilesenler) |
| P8 | Register adresi/sürücü kodu UYDURULMAZ — bilmeyen = UNKNOWN | zero-hallucination | kök AGENTS.md §3 |
| P9 | PCM3168A tek DAC; PCM5122 yasak | ADR-038 H001 · D-11 | dogrulama §D · kararlar §2 |
| P10 | 32-bit float veri tipi; 512 örnek @48 kHz = 10,67 ms tampon | ADR-017 · D-1..D-3 | dogrulama §D |
| P11 | DC > 0,5 V → röle koruması (mute sırası audio-control, tetik power-control) | D-7 · Ov4 | dogrulama §D · kapsam §17 |
| P12 | Firmware değişikliği onay ister (RT kural 13) | K005 RT kural 13 | sibling dokümanlar |

## §5 Durum Makinesi Kipleri

> ⚠️ VERIFICATION REQUIRED: Bu kipler **taslak**tır — karşılığı kod yok
> (repo'da firmware dosyası yok, 2026-10-13). Adlar sözleşme düzeyindedir.

| # | Kip | Ne yapar | Çıkış koşulu | Kötü durum |
|---|---|---|---|---|
| M1 | `init` | register + clock yazılmış, mute **yüksek** (kapalı) | clock kilidi oturdu → `mute` | kilide takıl → hata modu (E_AUD_LOCK taslağı) |
| M2 | `mute` | mute-seq yürütülür, pop koruması | sıra tamam → ses yolu açılabilir | sıra bozulursa → `init`'e dön |
| M3 | `inline-fail-safe` | herhangi bir doğrulama başarısızlığında **anında mute'a** düşer | sorun giderilirse `mute` → yeniden dene | kalıcı → device-control/power-control'a rapor |

**Kip kuralı (M1):** Sistem yalnız bu üç kiptedir; dördüncü bir "yarı açık"
durum tanımlanamaz (P2 ile aynı kökten gelir). Gerçek makine
`audio-control-surucu.md` §3'te ayrıntılanır.

## §6 K003 Devir Sözleşmesi

Kaynak: firmware-master.md §3 · §5 · firmware-kapsam.md §15.3.

| # | Öğe | Yön | Tanım | Durum |
|---|---|---|---|---|
| H1 | Hazır kip sorgusu | audio-control → K003 | donanım hazır/yok hazır bayrağı (A6) | sözleşme · taslak |
| H2 | Boot bayrakları F5–F8 | boot → audio-control | DSP_READY · I2S_OK · DAC_OK · USB_OK | boot-surucu.md §2 (tanımlı) |
| H3 | Akış verisi | K003 → (hoparlör) | O2/O3 — **audio-control'un işi değil** | kapsam dışı (A7) |
| H4 | K003 register yazımı | ? | register'ı kim yazar — **açık soru** | ⚠️ ÇÖZÜLMEDİ (§14 Q1) |

**Devir kuralı (H1):** audio-control, K003'e yalnız **durum** bildirir; K003
lehine ayar yapmaz. Ters yönlü talep (K003 → register yazımı) bu dosyada
tanımlanmaz — Q1 çözülene kadar her iki yön de ⚠️ işaretli kalır.

## §7 Komşu Modüller

| # | Komşu | İlişki | Tek nokta | Sahip |
|---|---|---|---|---|
| N1 | boot | F5–F8 bayrak alır, ≤1200 ms hedefi (§8 D-9) | boot-surucu.md §2 | boot |
| N2 | runtime | zamanlama/RT yol paylaşımı (P7) | K005 RT kuralları | runtime |
| N3 | device-control | I2C register erişim yolu (A1'in taşıyıcısı) | audio-control-surucu.md §7 | device-control |
| N4 | power-control | mute tetikleme + röle DC koruması (Ov4) | sıra: audio-control · tetik: power-control | power-control |
| N5 | K003-audio | akış + hazır sorgusu (§6) | firmware-master.md §5 | K003 |
| N6 | K004-dsp | DSP algoritması (bypass/preset bayrakları hariç) | firmware-master.md §5 | K004 |

**Komşu kuralı (N1):** Sınır geçişleri yalnız §6/§7'deki tek noktalardan
yapılır; doğrudan iç geçiş (örn. audio-control ↔ K004 iç register) yasaktır.

## §8 Ölçüm Kapıları

Kaynak: audio-control-dogrulama.md §D (D-1…D-12) — değerler birebir taşınmıştır.

| # | Kapı | Eşik | Dayanak | Durum |
|---|---|---|---|---|
| D-1 | Tampon boyutu | 512 örnek | sartname.md | ölçülen/taşınan |
| D-2 | Örnekleme frekansı | 48 kHz | sartname.md | ölçülen/taşınan |
| D-3 | Tampon süresi | 10,67 ms | 512/48000 hesabı | hesap kanıtlı |
| D-4 | Veri tipi | 32-bit float | ADR-017 | karar kanıtlı |
| D-5 | **SNR** | **> 100 dB** | K005-firmware audio standardı | kapı — ölçülmeden geçmez |
| D-6 | **THD+N** | **< %0,01 @ 1 kHz** | K005-firmware audio standardı | kapı — ölçülmeden geçmez |
| D-7 | DC offset koruma | > 0,5 V | ADR-038 H001 (kural 10) | koruma eşiği |
| D-8 | Röle tetik gecikmesi | ≤ 1 tampon (10,67 ms) | taslak hedef | ⚠️ gerçek donanım gecikmesi UNKNOWN |
| D-9 | Boot → hazır | ≤ 1200 ms | atıf: boot-dogrulama.md B-13 | ⚠️ atıf zinciri çelişkili (§15 T3) |
| D-10 | Crosstalk | < −80 dB | — | ⚠️ VERIFICATION REQUIRED (kesin eşik belirsiz) |
| D-11 | DAC | PCM3168A (yasak: PCM5122) | ADR-038 H001 | karar kanıtlı |
| D-12 | Kanal haritası | 8.1 (+ height belirsiz) | — | ⚠️ VERIFICATION REQUIRED (UNKNOWN) |

**Kapı kuralı (K1):** D-5 ve D-6 **zorunlu geçiş kapılarıdır**; ölçüm
raporu olmadan "ses yolu doğrulandı" iddiası yazılamaz (P8 ile uyumlu).

## §9 Risk Devri

Kaynak: firmware-riskler.md §2 (risk 8) · §21 (eski "yalnız index.md" ifadesi bayattır).

| # | Risk | Skor | Devredilen koruma | Sahip |
|---|---|---|---|---|
| R8 | Açılış/kapanışta pop → hoparlör hasarı | 9 (etki) | mute-seq sırası (M2) + röle DC koruması (D-7/D-8) | audio-control + power-control |
| R9 | Clock kilitlenmezse ses yolu açılması | 8 (etki) | P3 + M1 kilit bekleme | audio-control |
| R10 | Çift register yazıcısı (K003 ↔ K005) | 7 (etki) | P6 tek sahip kuralı — Q1 çözülene kadar ⚠️ | açık (§14 Q1) |
| R11 | Uydurma register adresi/kod | 9 (etki) | P8 — UNKNOWN/⚠️ zorunlu | tüm yazarlar |

**Risk kuralı (R1):** Skoru ≥ 8 olan riskler §15 Red Team itirazlarında
en az bir kez çürütülmeye çalışılır; çürütülemezse §14 açık soru listesine
eklenir ve kapanışta tekrar okunur.

## §10 Bağımlılık Grafiği

```text
K001-donanım ──(F5-F8 bayrak, güç)──► boot ──(≤1200 ms hedef, D-9⚠️)──► audio-control
K005-firmware ──(üst düğüm)──────────► audio-control
audio-control-anayasa ──(kural)──────► audio-control-surucu ──► audio-control-ornekler
audio-control ──(I2C yol)────────────► device-control
audio-control ──(mute tetik, Ov4)────► power-control ──(röle)──► K001
audio-control ──(A6 hazır bayrağı)───► K003-audio
audio-control ✗(akış A7 kapsam dışı)─► K003-audio (O2/O3)
```

**Bağımlılık kuralı (B1):** Grafikte ok yönü tersine çevrilemez; geriye
bağımlılık (örn. boot'un audio-control'a kurulması) mimari hata sayılır ve
§15 itiraz listesine yazılır.

## §11 Gereksinim Eşlemesi

| ID | Gereksinim (kapsam §9.4) | Bu dosyada karşılığı | Durum |
|---|---|---|---|
| A1 | Register yazımı — ayar, akış değil | §3 F1 · §4 P1 | anayasal |
| A2 | Clock ağacı — oran, kilitleme | §3 F2 · §4 P3 | anayasal |
| A3 | Mute/mute-seq — pop koruması sırası | §3 F3 · §5 M2 · §9 R8 | anayasal |
| A4 | Gain basamağı — üst sınır kilidi | §3 F4 · §4 P5 | anayasal |
| A5 | Kanal eşleme tablosu | §3 F5 | anayasal |
| A6 | Kip sorgusu (K003'e) | §6 H1 · §3 F6 | sözleşme ⚠️ |
| A7 | Akış verisi işleme | §1 kapsam dışı (O2/O3) | DIŞARIDA |

## §12 Terimler (evrensel dil ile uyum)

Kaynak: firmware-evrensel-dil.md §2–§5 — bu modül şu terimlerin sahibidir.

| # | Terim (TR) | EN | Kural |
|---|---|---|---|
| 14 | **mute** | mute | TR kullanımda "sessize alma" — anlamsal kayma yok |
| 15 | **gain** | gain | "ses seviyesi" ile karıştırılmaz; gain ≠ volume |
| 16 | **clock** | clock | "zamanlama" ile eşlenir; saat darbesi anlamında |

**Terim kuralı (L1):** Bu üç terim audio-control dışındaki K005 dosyalarında
**aynı** yazılır (TR-EN çifti korunur); farklı eşleme (örn. gain→ses)
kullanılırsa §15 itiraz kaydı açılır.

## §13 Kaynak ve Repo Kanıtı

| # | İddia | Dayanak dosya | Kanıt durumu |
|---|---|---|---|
| E1 | A1–A7 kapsam listesi | firmware-kapsam.md §9.4 | okundu 2026-10-14 |
| E2 | Eşik değerleri D-1…D-12 | audio-control-dogrulama.md §D | okundu 2026-10-14 |
| E3 | Boot F5–F8 bayrakları | boot/boot-surucu.md §2 | okundu 2026-10-14 |
| E4 | E_AUD_* hata kodu taslakları (H10/H11) | firmware-bilesenler.md §23 | okundu 2026-10-14 |
| E5 | Açık soru Q1 (register sahibi) | audio-control/index.md frontmatter | okundu 2026-10-14 |
| E6 | Risk 8 pop/hasar | firmware-riskler.md §2 | okundu 2026-10-14 |
| E7 | Derlenebilir firmware kodu yok | 2026-10-13 repo ölçümü (görev manifesti) | ölçüm — kod örneği üretilemez |
| E8 | mute/gain/clock sahipliği | firmware-evrensel-dil.md §2/§5 | okundu 2026-10-14 |

**Kanıt kuralı (V1):** Yukarıdaki liste dışında dosya/satır iddiası bu
dosyada **yazılmaz**; `Dosya:`/`Satır:` referansları bilinen değillerse
bırakılır, uydurulmaz.

## §14 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | kime ait: K003 mü K005 mı register'ı yazar? (bagimliliklar §4/7) | P6 tek sahip kuralı bloke | ÇÖZÜLMEDİ — index.md'den taşındı |
| Q2 | MCU seçimi kesin değil (XMOS XU316 ↔ STM32 ↔ ESP32) | kayıt yolu/timing UNKNOWN | master §10 Q4 — UNKNOWN |
| Q3 | K003 sınırının kesin teyidi | §6 H4 yön belirsizliği | master §10 Q5 — ⚠️ |
| Q4 | ≤1200 ms atıf zinciri (B-13 gerçekte rollback bayrağı mı?) | D-9 kapısı | ⚠️ §15 T3 |
| Q5 | TDM kanal eşlemesi ve `kChannels=12` doğruluğu | A5 eşleme tablosu | UNKNOWN |
| Q6 | Crosstalk eşik değeri kesinleşmedi | D-10 kapısı | ⚠️ VERIFICATION REQUIRED |

**Soru kuralı (Q1k):** Bu tablodaki satırlar kapanmaz; kapanışları yalnız
kanıtla (dosya + okuma tarihi) yapılır — tahminle kapatılan satır sayılmaz.

## §15 Red Team İtirazları

| # | İtiraz | Cevap | Sonuç |
|---|---|---|---|
| T1 | "Register sahibi belli değil, anayasa eksik." | Doğru — Q1 açık; P6 olarak işaretli kaldı | açık soru (Q1) |
| T2 | "Kod yok, kurallar uygulanamaz." | Doğru — tüm adlar SÖZLEŞME etiketli (P8/E7) | kabul edildi |
| T3 | "D-9 (≤1200 ms) yanlış dosyaya atfedilmiş: boot-dogrulama.md B-13 gerçekte DV-B13 rollback bayrağı; 1200 ms yalnız boot-ornekler §G/§H'de." | İtiraz geçerli — atıf düzeltilmeden kapı çalıştırılmaz | ⚠️ işaretli (Q4) |
| T4 | "boot toplam ~1,5 s (boot-anayasa §3) ≤1200 ms'ye sığmaz." | İtiraz geçerli — iki hedef çelişkili | raporlandı (§19) |
| T5 | "master §6.2 audio-control için 6 MD sayar (web-arastirma/bilesenler dahil); diskte 4 + bu 2." | İtiraz geçerli — sayım bayat | raporlandı (§19) |
| T6 | "riskler §21 'yalnız index.md' diyor; artık 6 dosya." | İtiraz geçerli — ifade bayat | raporlandı (§19) |
| T7 | "Mute sırası kimin? audio-control mu power-control mu?" | Ov4: sıra audio-control'de, tetik power-control'de | cevaplandı (kapsam §17) |
| T8 | "D-8 röle gecikmesi ölçülmemiş." | Doğru — taslak hedef, gerçek UNKNOWN | ⚠️ (D-8) |
| T9 | "F5–F8 bayrak adları iki yerde farklı geçebilir." | boot-surucu §2 tek tanım noktası (S1 benzeri) | tek kaynak: boot-surucu |
| T10 | "SNR/THD+N kapıları ölçüm altyapısız anlamsız." | Doğru — kapılar ölçüm raporu olmadan geçilmez (K1) | kabul + test köprüsü (§18) |

## §16 İddia ↔ Dayanak Tablosu

| # | İddia | Dayanak |
|---|---|---|
| C1 | Sıra register → clock → mute → gain → eşleme → sorgu | firmware-kapsam.md §9.4 A1–A6 sırası |
| C2 | Açılışta mute pin yüksek | firmware-kapsam.md senaryo S1 |
| C3 | SNR > 100 dB zorunlu kapı | audio-control-dogrulama.md D-5 |
| C4 | THD+N < %0,01 @ 1 kHz zorunlu kapı | audio-control-dogrulama.md D-6 |
| C5 | DC > 0,5 V koruması | audio-control-dogrulama.md D-7 (ADR-038 H001) |
| C6 | PCM3168A tek DAC, PCM5122 yasak | audio-control-dogrulama.md D-11 · firmware-kararlar.md §2 ADR-038 |
| C7 | 512/48 kHz = 10,67 ms | audio-control-dogrulama.md D-1…D-3 |
| C8 | 32-bit float | audio-control-dogrulama.md D-4 (ADR-017) |
| C9 | Akış O2/O3'e ait | firmware-kapsam.md §9.4 A7 |
| C10 | mute/gain/clock terim sahipliği | firmware-evrensel-dil.md §2/§5 |
| C11 | F5–F8 boot'tan gelir | boot/boot-surucu.md §2 |
| C12 | E_AUD_LOCK / E_AUD_MISMATCH taslak | firmware-bilesenler.md §23 H10/H11 |
| C13 | Pop riski skor 9 | firmware-riskler.md §2 risk 8 |
| C14 | Ov4 mute bölünmesi | firmware-kapsam.md §17 |
| C15 | K003 bağımlılığı | firmware-master.md §3/§5 |

## §17 Karar Bağlantıları

| # | Karar | İlişki | Not |
|---|---|---|---|
| Z1 | ADR-017 (DSP hardware mode / 32-bit float) | D-4 doğrudan dayanağı | frozen — okunur, değişmez |
| Z2 | ADR-038 H001 (DAC seçimi + DC kuralı) | D-7 · D-11 dayanağı | frozen — okunur, değişmez |
| Z3 | K-1…K-16 karar seti (firmware-kararlar §2) | genel K005 kararları | okundu 2026-10-14 |
| Z4 | K005 RT kuralları 1–13 | P7 · P12 dayanağı | sibling dokümanlardan taşındı |

## §18 Test Köprüsü

| # | Kural | Doğrulama yöntemi | Bağlantı |
|---|---|---|---|
| G1 | Sıra ihlali yok (A1) | mute-seq birim testi (taslak) | audio-control-ornekler.md |
| G2 | Kilitsiz açılış yok (P3) | kilit bekleme zamanlama testi | dogrulama §A senaryoları |
| G3 | SNR/THD+N kapıları (D-5/D-6) | ölçüm raporu zorunlu | dogrulama §D · §C metodoloji |
| G4 | DC/röle koruması (D-7/D-8) | röle gecikme ölçümü | dogrulama §D (⚠️ D-8) |
| G5 | Bayrak tüketimi F5–F8 | boot→audio devir testi | boot-surucu.md §2 |
| G6 | Terim tutarlılığı (L1) | vault lint: mute/gain/clock yazım denetimi | firmware-evrensel-dil.md |

## §19 Revizyon

| # | Tarih | Değişiklik | Otorite |
|---|---|---|---|
| V1 | 2026-10-14 | İlk yazım — anayasa iskeleti (boot-anayasa aynası), FM 10 alan | Vault Steward |
| V2 | 2026-10-14 | §15 T3–T6 çelişki kaydı eklendi (atıf/sayım bayatlıkları) | Vault Steward |

## §20 Kapanış Kontrolü (bu dosya)

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı (yalnız `parent_id`) | evet |
| `group` değeri | tirnaksız `mimari` | evet |
| Liste alanları | boşsa bile `[]` | evet |
| Açık soru Q1 | çözülmeden taşındı mı | evet (§14 Q1) |
| Uydurma register adresi/sürüm/URL | 0 | 0 |
| Son satır | Authority satırı (birebir) | evet |
| Bölüm sayısı | 15–24 arası `## §` | 20 |

## §21 Vault DSP/Firmware Kaynağı Derinletmesi (append · 2026-10-16)

> Kaynaklar bu ek için: `Dosya: .ai/electronic/firmware/dsp-firmware.md` (508 satır) · `Dosya: .ai/electronic/drivers/audio-drivers.md` (709 satır) — ikisi de okundu 2026-10-16. Her iddia Dosya+satır referanslıdır; repo `*.cpp/*.h/*.c` YOK (2026-10-15 ölçümü) → "çalışan kod" iddiası YASAK ve yazılmaz.

### §21.1 Clock Hiyerarşisi (§3 ayar akışının fiziksel dayanağı)

```
Kristal osilatör (Master Clock referansı)
    ↓
PLL → MCLK
    ↓
Clock divider → BCLK / WCLK (LRCLK)
    ↓
I2S/TDM veri hattı → DAC
```

Dayanak: `dsp-firmware.md:72` (§6).

| # | Clock kuralı | Bu dosyadaki karşılık | Dayanak |
|---|---|---|---|
| C1 | Tek clock kaynağı — tüm ses zinciri tek master'dan | §3 sıra kuralı (Register → Clock → Mute) | dsp-firmware.md:99 (§6.2) |
| C2 | Düşük jitter PLL; jitter SNR'ı sınırlar | §8 ölçüm kapıları (SNR/THD+N) | dsp-firmware.md:100 (§6.2) |
| C3 | USB (asenkron) ↔ I2S (senkron) ara buffer zorunlu | §7 komşu sınırı (K003) | dsp-firmware.md:101 (§6.2) |
| C4 | BCLK/WCLK MCLK'tan bölünür | §12 terimler | dsp-firmware.md:102 (§6.2) |
| C5 | İki bağımsız clock → slip/pop riski | §15 pop riski itirazı | dsp-firmware.md:103 (§6.2) |

### §21.2 Sample-Rate / Clock Domain Matrisi (§8 ölçüm kapılarının girdisi)

| Sample Rate | BCLK (stereo, 32-bit) | MCLK (tipik 256×fs) | Kullanım | Dayanak |
|---|---|---|---|---|
| 44.1 kHz | 2.8224 MHz | 11.2896 MHz | CD kalitesi | dsp-firmware.md:117 (§7) |
| 48 kHz | 3.072 MHz | 12.288 MHz | video/stüdyo | dsp-firmware.md:118 (§7) |
| 96 kHz | 6.144 MHz | 24.576 MHz | yüksek çözünürlük | dsp-firmware.md:119 (§7) |
| 192 kHz | 12.288 MHz | 49.152 MHz | en yüksek çözünürlük | dsp-firmware.md:120 (§7) |

Kural (dsp-firmware.md:124 §7.1): her sample-rate için PLL yapılandırması baştan tanımlanır; çalışma zamanı rate değişimi PLL yeniden kilitlenmesi + I2S yeniden yapılandırması gerektirir → §3'teki "çalışma zamanı ayar akışı" bu koşula bağlanır.

### §21.3 DAC Init Sırası (§3 Register → Clock → Mute zincirinin teyidi)

| # | Adım | Ters sıralamanın sonucu | Dayanak |
|---|---|---|---|
| 1 | Clock stabil | — | dsp-firmware.md:461 (§26.1) |
| 2 | DAC reset | — | dsp-firmware.md:461 (§26.1) |
| 3 | DAC format/rate yapılandır | kilitlenmezse kayıt bozulur | dsp-firmware.md:461 (§26.1) |
| 4 | Mute kaldır | ters sıra → pop noise | dsp-firmware.md:461 (§26.1) |

DAC parametresi eşlemesi (dsp-firmware.md:453 §26 tablo): MCLK oranı ↔ PLL yapılandırması · format (I2S/TDM) ↔ I2S modu · sample rate ↔ rate matrisi · kanal sayısı ↔ kanal haritası · reset/init ↔ boot sırasında init.

Bu satırlar bu dosyanın §3'ünü onaylar, yeni sıra ekmez; §15 T3 boot→hazır süresi çelişki kaydı aynen geçerlidir.

### §21.4 Ölçüm Kapıları — Dış Dayanak (§8 G3/G4 için)

| Kapı | Vault ölçüm kuralı | Dayanak |
|---|---|---|
| Gecikme | fiziksel loopback + cross-correlation; örnek cinsinden rapor; 10 ölçümün medyanı | audio-drivers.md:533 (§7.1) |
| Xrun oranı | <%0.01 (xrun/toplam period) | audio-drivers.md:546 (§7.2) |
| Kurtarma süresi | Xrun → RUNNING < 1 period | audio-drivers.md:546 (§7.2) |
| Tekrarlayan xrun | aynı 10 sn içinde ≥2 → 0 (buffer ayarı değişmeli) | audio-drivers.md:546 (§7.2) |
| DSP zinciri CPU | <%15 (8+1 kanal, 48 kHz) | audio-drivers.md:554 (§7.3) |
| Toplam ses yığını CPU | <%25 (ölçekli çekirdek) | audio-drivers.md:554 (§7.3) |

Ölçüm tekrarı (audio-drivers.md §7.6): her ölçüm en az 3 ortamda (soğuk açılış · 1 saat sürekli · sıcak) tekrarlanır, medyan + maksimum raporlanır, kayıt dosya/tarih/platform/sürücü sürümü ile damgalanır.

Ölçüm ortamı (audio-drivers.md §7.5): 48 kHz · buffer 512 (varsayılan) + 64/128/256/1024 eğrisi · 8.1 kanal · 32-bit float · sabit sıcaklık.

Ölçüm hataları (audio-drivers.md §7.8): yazılım döngü ölçümü DMA/IRQ gecikmesini ölçmez → fiziksel loopback şart · tek ölçüm kanıt değildir · ms raporu yerine örnek raporu.

### §21.5 SNR/THD+N Kapısı — Clock Bağı (§8 D-5/D-6 dayanak zinciri)

| Zincir | Adım | Dayanak |
|---|---|---|
| 1 | Kristal → PLL → MCLK jitter'i SNR'ı sınırlar | dsp-firmware.md:100 (§6.2) |
| 2 | Tek master clock yoksa slip/pop → THD+N artar | dsp-firmware.md:103 (§6.2) |
| 3 | Ölçüm fiziksel loopback ile, örnek cinsinden | audio-drivers.md:533 (§7.1) |
| 4 | 3 ortam × medyan + maksimum rapor | audio-drivers.md §7.6 |

Sonuç: §8'deki SNR/THD+N kapıları bu dosyada tanımsız kalmaz — dayanağı yukarıdaki 4 adımlı zincirdir; ancak ölçüm değeri vault'ta YOK → kapı hâlâ `⚠️ VERIFICATION REQUIRED` (gerçek ölçüm 0).

### §21.6 Hata Modu → Kapı Eşlemesi (§9 risk devrine ek)

| Hata modu | Teşhis | Kapı | Dayanak |
|---|---|---|---|
| Xrun (underrun/overrun) | §7.2 sayacı | G3 ölçüm kapısı | audio-drivers.md:546 |
| Clock drift (duplex kayma) | duplex kayma ölçümü | G3 | audio-drivers.md §7.4 |
| Format reddi | açma sonucu kaydı | G1 sıra testi | audio-drivers.md §7.4 |
| Röle hata durumu | donanım durum bildirimi | G4 röle gecikme ölçümü | audio-drivers.md §7.4 |
| Pop/noise | mute sırası ihlali / çift clock | G2 kilit bekleme testi | dsp-firmware.md:103 · dsp-firmware.md:461 |

### §21.7 Bayrak Devir Çapraz Kontrol (§6 K003 devri ile uyum)

| Bayrak yönü | Vault karşılığı | Uyum | Dayanak |
|---|---|---|---|
| boot → audio: clock/init tamam | boot sırasında DAC init | uyumlu | dsp-firmware.md:459 (§26) |
| audio → akışa hazır | DMA warm-up sonrası akış | uyumlu | audio-drivers.md §7.3 (CPU bütçesi) |
| audio → power: mute hazır | mute register akış bitiminde | uyumlu | embedded-drivers §11.1 (bkz. device-control-anayasa §21.1) |

### §21.8 Çapraz Referans ve Kapanış (bu ek)

| Dosya | Kullanılan bölüm | Erişim |
|---|---|---|
| .ai/electronic/firmware/dsp-firmware.md | §6 (72) · §7 (113) · §26 (449) | okundu 2026-10-16 |
| .ai/electronic/drivers/audio-drivers.md | §7 (533) | okundu 2026-10-16 |
| K005-firmware/audio-control/* | §3 · §6 · §8 · §9 · §15 | bu dosyanın mevcut bölümleri |

| Kontrol | Sonuç |
|---|---|
| Eklenen bölüm | §21 (append-only; §1–§20 silinmedi) |
| Silinen mevcut içerik | 0 |
| Uydurma register/sürüm/URL/Dosya:/Satır: | 0 |
| Chip/marka iddiası | yalnız vault kaynağından (dsp-firmware.md:453 §26) |
| Ölçüm değeri uydurma | 0 — kapılar hâlâ VERIFICATION REQUIRED |
| Son satır | Authority satırı korunur |


## §22 Zamanlama ve Xrun Ek Dayanağı (append · 2026-10-16)

> Kaynak: `Dosya: .ai/electronic/drivers/audio-drivers.md` §9 (satır 650–709) · `Dosya: .ai/electronic/firmware/dsp-firmware.md` §21 (satır 363) · §22 (satır 378) — okundu 2026-10-16.

### §22.1 Buffer ↔ Periyot ↔ Xrun Matematiği (§8 zamanlama kapısı girdisi)

```
T_period = period_size / sample_rate      → 512 / 48000 = 10.667 ms
T_buffer = buffer_size / period_size × T_period → 2 × 10.667 = 21.33 ms
xrun toleransı = T_buffer − iş gecikmesi (jitter)
Kabul: xrun toleransı > p99 jitter → underrun olmaz
```

Dayanak: `audio-drivers.md:652` (§9.2).

| Büyüklük | Değer | Not | Dayanak |
|---|---|---|---|
| T_period (48 kHz / 512) | 10.667 ms | 512/48000 | audio-drivers.md §9.2 |
| T_buffer (2 tampon) | 21.33 ms | §9.2 | audio-drivers.md §9.2 |
| p99 scheduler jitter (hedef) | ≤2 ms | RT çekirdek varsayımı | audio-drivers.md §9.2 |
| Kalan pay | 19.33 ms | DSP işleme süresi | audio-drivers.md §9.2 |

Kural: callback p99 ≤2 ms hedefini aşarsa underrun riski doğar; ölçülen p99 sürekli izlenir (audio-drivers.md §9.2).

### §22.2 Xrun Tipi ↔ Kök Neden (§9 risk devrine sınıf verisi)

| Tip | Kök neden | Belirti | Dayanak |
|---|---|---|---|
| Capture overrun | uygulama gecikmesi, scheduler | click, tekrar eden örnekleme | audio-drivers.md:654 (§9.1) |
| Playback underrun | DSP aşırı yük, öncelik düşmesi | click, sessizlik boşluğu | audio-drivers.md:655 (§9.1) |
| DMA underrun | IRQ gecikmesi, DMA kanal çakışması | hardware click | audio-drivers.md:656 (§9.1) |
| Period geç kalma | scheduler jitter | zamanlama kayması | audio-drivers.md:657 (§9.1) |

### §22.3 Xrun Önleme ve Kurtarma (§4/§8 köprüsü)

| Katman | Strateji | Dayanak |
|---|---|---|
| Buffer | ≥2 tampon (jitter emilimi) | audio-drivers.md §9.3 |
| Scheduler | SCHED_FIFO, çekirdek izolasyonu | audio-drivers.md §9.3 |
| IRQ | IRQ affinity, coalescing | audio-drivers.md §9.3 |
| DSP | SIMD, sabit tahsis (kural 1) | audio-drivers.md §9.3 |
| Gözlem | period_elapsed telemetrisi | audio-drivers.md §9.3 |

Kurtarma kuralı (audio-drivers.md §9.4): tek xrun sessizce kurtarılır · ≥3 tekrarlayan xrun buffer büyüme önerisi tetikler · DMA underrun → IRQ/DMA yeniden senkron.

### §22.4 Clock Gating Uyarısı (§8 DC/röle korumasına komşu kural)

| Durum | DSP | Clock | Güç | Dayanak |
|---|---|---|---|---|
| Aktif | çalışır | tam | normal | dsp-firmware.md:365 (§21) |
| Idle (ses yok) | döngü hazır | kısmi | düşük | dsp-firmware.md:366 (§21) |
| Suspend | duraklat | kapalı | çok düşük | dsp-firmware.md:367 (§21) |
| Uyku | kapalı | kapalı | minimum | dsp-firmware.md:368 (§21) |

Uyarı (dsp-firmware.md:373 §21.1): audio clock'u (MCLK) ani kapatmak DAC'ı bozar; clock gating, mute ve pop-free sıralamayla yapılır → §3 sıra kuralı ile aynı disiplin.

### §22.5 Gecikme (Latency) Bütçesi — Dış Dayanak

| Gecikme kaynağı | Tipik katkı | Dayanak |
|---|---|---|
| USB buffer (frame) | 1 ms (1 frame @1k) | dsp-firmware.md:383 (§22) |
| DSP processing | 1 frame (double-buffer) | dsp-firmware.md:384 (§22) |
| I2S/DMA buffer | 1 frame | dsp-firmware.md:385 (§22) |
| DAC filtre gecikmesi | DAC'a bağlı | dsp-firmware.md:386 (§22) |
| **Toplam (tipik)** | birkaç ms | dsp-firmware.md:387 (§22) |

⚠️ VERIFICATION REQUIRED: bu bütçe firmware/yazılım katmanını anlatır; bu dosyanın §15 T3'ündeki ≤1200 ms boot→hazır atıf zinciri ile **farklı büyüklüktür** — çelişki kaydı aynen geçerlidir, kapatılmaz.

### §22.6 Kapanış (§21–§22 ekleri)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §21 · §22 (append-only; §1–§20 silinmedi) |
| Silinen mevcut içerik | 0 |
| Uydurma değer/URL/Dosya:/Satır: | 0 |
| Ölçüm değeri iddiası | 0 (yalnız vault hedef değerleri aktarıldı) |
| Açık sorular | kapatılmadı (§14 aynen geçerli) |
| Son satır | Authority satırı korunur |


**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
