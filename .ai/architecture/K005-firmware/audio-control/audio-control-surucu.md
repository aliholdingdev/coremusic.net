---
node_id: K005-firmware/audio-control/audio-control-surucu
node_type: module
status: draft
parent_id: K005-firmware/audio-control
children: []
depends_on:
  - K005-firmware/audio-control/audio-control-anayasa
evidence:
  - "K005-firmware/audio-control/audio-control-anayasa.md — kural dayanağı 2026-10-14"
  - "K005-firmware/audio-control/index.md — okundu 2026-10-14"
  - "K005-firmware/audio-control/audio-control-kapsam.md — okundu 2026-10-14"
  - "K005-firmware/audio-control/audio-control-dogrulama.md §D — okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md — arayüz iskeleti + F5-F8 tanımı — okundu 2026-10-14"
  - "K005-firmware/boot/boot-ornekler.md §G-§I — hata kodu kalıbı — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §19.4 · §23 — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §4-§7 · §9.3 · §20 · §21 — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §3 · §5 · §10 — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §9.4 · §15.3 · §17 — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "kime ait: K003 mü K005 mı register'ı yazar? (bagimliliklar §4/7) — ÇÖZÜLMEDİ, taşındı"
  - "Hata kodu kümesi tanımlı DEĞİL (E_AUD_* yalnız taslak) — ⚠️ VERIFICATION REQUIRED"
  - "TDM kanal eşlemesi / kChannels=12 doğrulanmadı — UNKNOWN"
  - "Gerçek işlev/imza adları kodda yok — adlar sözleşme düzeyinde, ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# audio-control-surucu — Ses Ayarı Arayüz ve Sözleşme Katmanı (K005-firmware)

> **ELI15:** `audio-control-anayasa.md` kuralları koyar; bu dosya
> audio-control'un **dışarıya açılan kapısını** tanımlar: bayraklar, durum
> makinesi, boot↔runtime devri, K003/device-control/power-control arayüzleri,
> hata kodu ve telemetri kanalları. Buradaki adlar **sözleşme düzeyindedir**;
> karşılığı olan kod henüz yazılmamıştır (repo'da firmware kodu YOK — 2026-10-13).

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | audio-control modülünün arayüz/sözleşme katmanı | K005-firmware/audio-control |
| Kimler bu arayüzü kullanır? | boot · runtime · K003-audio · device-control · power-control | §5–§8 komşu tabloları |
| Adlar kod adı mı? | Hayır — kod yok, sözleşme adı (⚠️ VERIFICATION REQUIRED) | §16 Q4 |
| Bu dosya anayasayı değiştirir mi? | Hayır — anayasa > bu dosya > örnekler | audio-control-anayasa.md |
| Kapsam dışı | DSP algoritması (K004) · akış verisi (O2/O3 → K003) | anayasa §1 |
| Kapsam dışı | I2C fiziksel sürücüsü iç yapısı | device-control (§7) |
| Kapsam dışı | Röle/güç devresi fiziksel tasarımı | K001 · power-control (§8) |
| Kapsam dışı | Boot zinciri iç akışı | boot (§5) |

**Sözleşme kuralı (S1):** Bir arayüz adı yalnız **tek dosyada** tanım
kazanır; diğer dosyalar yalnız kullanır (çift tanım = belirsizlik ihlali).

**Sözleşme kuralı (S2):** Arayüz adları kodla eşleşinceye kadar
`SÖZLEŞME`/`TASLAK` etiketli kalır; eşleşme kanıtı olmadan "uygulandı" yazılmaz.

## §2 Bayrak ve Olay Sözlüğü (Tek Tanım Noktası)

### §2.1 Tüketilen bayraklar (boot → audio-control)

Kaynak: boot/boot-surucu.md §2 (tek tanım noktası — burada yeniden tanımlanmaz).

| # | Bayrak | Üreten | Tüketen | Anlamı | Kötü durum |
|---|---|---|---|---|---|
| F5 | DSP_READY | boot çekirdeği | audio-control | DSP zinciri hazır | alternatif profil (boot) |
| F6 | I2S_OK | boot çekirdeği | audio-control | seri yol hazır | yeniden konfigürasyon (boot) |
| F7 | DAC_OK | boot çekirdeği | audio-control | PCM3168A hazır | E2 alternatif (boot) |
| F8 | USB_OK | boot çekirdeği | audio-control · runtime | USB ses sınıfı hazır | E3 LED kodu (boot) |

> **Not:** `audio-control/index.md` frontmatter'ında `flags` alanı
> `'F7 · F8 (record hattı)'` kısaltması geçer; boot-surucu §2'deki F7=DAC_OK
> tanımı ile kısaltmanın ifadesi birebir örtüşmez — §17 T2'de çelişki olarak
> kayıtlıdır; çelişkide **tek tanım noktası boot-surucu §2'dir** (S1).

### §2.2 Üretilen bayraklar (audio-control → dışarı)

> ⚠️ TASLAK — bu adlar bu dosyada **önerilmektedir**; kod ile eşleşmemiştir.

| # | Bayrak | Üreten | Tüketen | Anlamı | Kötü durum |
|---|---|---|---|---|---|
| A-F1 | `path_ready` (TASLAK) | audio-control | K003 · runtime | ayar sırası tamam, ses yolu açılabilir | `init`'e dön (anayasa M1) |
| A-F2 | `channels_ok` (TASLAK) | audio-control | K003 | kanal eşleme tablosu doğrulandı | E_AUD_MISMATCH taslağı |
| A-F3 | `rate_ok` (TASLAK) | audio-control | K003 | örnekleme hızı 48 kHz kilitti | clock yeniden dene |
| A-F4 | `out_active` (TASLAK) | audio-control | power-control · telemetri | ses yolu açık (mute kaldırıldı) | DC/röle korumasına dön |
| A-F5 | `mute_state` (TASLAK) | audio-control | power-control · telemetri | mute-seq durumu (anayasa §5 M2) | fail-safe mute (M3) |
| A-F6 | `hw_ready` (TASLAK) | audio-control | K003 (A6 kip sorgusu) | donanım hazır/yok hazır bayrağı | yok-hazır bildirimi |

**Bayrak kuralı (F1):** Yalnız bu iki tablo bayrak tanımı taşır; başka
dosyada bayrak tanımı görülürse S1 ihlali sayılır ve §17 itirazına yazılır.

## §3 Durum Makinesi

Kaynak: anayasa §5 (kip kökleri) — burada geçişler ayrıntılanır. ⚠️ TASLAK.

| # | Durum | Giriş koşulu | Çıkış | Hata dalı |
|---|---|---|---|---|
| St1 | `Idle` | reset/devir sonrası | F5–F8 alındı → `Preparing` | bayrak gelmez → kalır (boot raporlar) |
| St2 | `Preparing` | register + clock yazımı | kilidi oturdu → `Ready` | kilit zaman aşımı → `Tripped` (E_AUD_LOCK taslağı) |
| St3 | `Ready` | mute-seq tamam | K003'e `hw_ready` → `Streaming` | geri okuma uyuşmaz → `Tripped` |
| St4 | `Streaming` | ses yolu açık (`out_active`) | istek/kapanış → `Recovering` (kapanışta) | DC > 0,5 V → derhal `Tripped` |
| St5 | `Tripped` | herhangi bir hata | otomatik → `Recovering` | kalıcı → device-control/power-control'a rapor |
| St6 | `Recovering` | mute'a çek + yeniden dene | başarılı → `Ready` | max tekrar aşıldı → `Tripped` (kalıcı) |

```text
Idle ─► Preparing ─► Ready ─► Streaming ─► Recovering ─► Ready
          │            │          │             │
          └─── Tripped ◄┴──────────┴─────────────┘
                  │
                  └── (kalıcı) → power-control / device-control raporu
```

**Makine kuralı (M1):** Geçişler yalnız tabloda tanımlı yönde olur; ters
geçiş (örn. `Streaming` → `Preparing` mute'suz) YASAKTIR (anayasa P2).

## §4 Geçiş Sözleşmeleri

| # | Geçiş | Garanti (girişte) | Zorunlu eylem (çıkışta) | İspat |
|---|---|---|---|---|
| G1 | Idle → Preparing | F5–F8 bayraklarının tamamı alındı | register yazımı anayasa §3 sırasıyla | boot-surucu §2 |
| G2 | Preparing → Ready | clock kilidi oturdu (P3) | mute-seq başlat (M2) | anayasa §3 F2/F3 |
| G3 | Ready → Streaming | `hw_ready` K003'e bildirildi (A6) | `out_active` bayrağı set edilir | anayasa §6 H1 |
| G4 | Streaming → Recovering | mute anında çekildi (P4/P11) | DC/röle kontrolü power-control'de | anayasa Ov4 (kapsam §17) |
| G5 | * → Tripped | fail-safe mute aktif (M3) | hata kodu set edilir (§9) | anayasa §5 M3 |

**Geçiş kuralı (G1k):** Her geçişte garanti **ve** zorunlu eylem birlikte
tanımlıdır; biri boşsa geçiş geçersizdir (boş hücre yasağı).

## §5 Boot ↔ Runtime Devri

Kaynak: boot-surucu.md §5 (devir) · anayasa §8 D-9 (≤1200 ms — ⚠️ çelişkili).

| # | Öğe | Yön | Tanım | Durum |
|---|---|---|---|---|
| D1 | F5–F8 bayrakları | boot → audio-control | §2.1 | tanımlı (boot-surucu §2) |
| D2 | ≤1200 ms boot→hazır hedefi | boot → audio-control | ses yolu hazırlık bütçesi | ⚠️ atıf zinciri çelişkili (anayasa §15 T3/Q4) |
| D3 | devir sonrası sorumluluk | audio-control → runtime | ready sonrası zamanlama/RT paylaşımı (P7) | sözleşme |
| D4 | rollback bayrağı | boot içi | boot-dogrulama.md B-13 **DV-B13 rollback** — ses hedefi DEĞİL | düzeltme kaydı (§17 T3) |

**Devir kuralı (D1k):** Devir anından sonra boot, ses yolu için sorumlu
değildir (anayasa §1 sınır notu); ready sonrası hatalar audio-control +
runtime zincirinde işlenir.

## §6 K003 Arayüzü

Kaynak: firmware-master.md §3/§5 · firmware-kapsam.md §15.3 · anayasa §6.

| # | Arayüz | Yön | Girdi | Çıktı | Garanti |
|---|---|---|---|---|---|
| K1 | hazır kip sorgusu (A6) | K003 → audio-control | sorgu | `hw_ready` (A-F6 taslağı) | salt okunur, sorgu yazmaz |
| K2 | ayar isteği | K003 → audio-control | hız/format talebi | anayasa §3 sırasıyla uygulanır | talep sahibi register YAZMAZ |
| K3 | akış verisi | K003 ↔ K003 (audio-control hariç) | O2/O3 | — | kapsam dışı (A7) |
| K4 | register sahibi | ? | — | — | ⚠️ AÇIK: K003 mü K005 mi? (anayasa Q1) |

**K003 kuralı (K1k):** K003 ile tek temas noktaları K1/K2'dir; K003 doğrudan
register'a yazarsa bu dosya S1 ihlali olarak §17 itirazına düşer — Q1
çözülene kadar hem K3 hem K4 ⚠️ işaretli kalır.

## §7 device-control Arayüzü (I2C Yolu)

| # | Arayüz | Yön | Girdi | Çıktı | Garanti |
|---|---|---|---|---|---|
| I1 | register oku | audio-control → device-control | register adı (**bilinmiyor — UNKNOWN**) | değer | salt okunur |
| I2 | register yaz | audio-control → device-control | register adı + değer | onay | yalnız ayar (A1), akış verisine dokunmaz |
| I3 | kilit/arbiter | device-control → audio-control | yol müsaitliği | sıra izni | tek yazıcı (P6 — Q1'e bağlı ⚠️) |
| I4 | geri okuma doğrulama | device-control → audio-control | yazılan ↔ okunan | uyuşur/uyuşmaz | uyuşmazlık → E_AUD_MISMATCH (taslak) |

**I2C kuralı (I1k):** Register **adresleri bu dosyada YOKTUR** — bilinmiyor
(P8/zero-hallucination); I1/I2'de "register adı" bir yer tutucu değil,
sözleşme parametresidir ve değer gelene kadar UNKNOWN işaretli kalır.

## §8 power-control Arayüzü (Mute / Röle)

Kaynak: anayasa §1 N4 · kapsam §17 Ov4 · dogrulama D-7/D-8.

| # | Arayüz | Yön | Girdi | Çıktı | Garanti |
|---|---|---|---|---|---|
| P1 | mute tetik isteği | audio-control → power-control | mute aç/kapat | onay | sıra tanımı audio-control'de, tetik power-control'de (Ov4) |
| P2 | DC ihlali bildirimi | audio-control → power-control | DC > 0,5 V algılandı | röle çekildi | röle ≤ 1 tampon (10,67 ms) — ⚠️ taslak hedef (D-8) |
| P3 | açılış mute pin | audio-control → K001 (yol: power-control) | mute pin yüksek | — | açılışta susma (anayasa P4/S1) |
| P4 | kapanış sırası | audio-control → power-control | mute-seq kapanış | onay | pop koruması sırası bozulmaz (A3) |

**Röle kuralı (P1k):** Röle gecikmesi ölçülmeden D-8 kapısı "geçti"
denemez; gerçek donanım gecikmesi UNKNOWN'dur ve uydurulmaz.

## §9 Hata Kodu Arayüzü

> ⚠️ VERIFICATION REQUIRED: audio-control için **hata kodu seti tanımlı
> değildir**. Boot-ornekler §G'deki BOOT_E001–E010 tablosu "boot-anayasa §14
> (audio kontrol — tanımlı değil)" notunu taşır. Aşağıdaki iki kod
> **taslaktır** (firmware-bilesenler.md §23, F8 kaynağı) ve küme değildir.

| # | Kod | Kaynak | Tetik | Eylem | Durum |
|---|---|---|---|---|---|
| H1 | `E_AUD_LOCK` | bilesenler §23 (H10) | F8 · kilidi bekle → timeout | fail-safe mute (M3) | TASLAK |
| H2 | `E_AUD_MISMATCH` | bilesenler §23 (H11) | F8 · geri okuma uyuşmazlığı | `Tripped` → tekrar dene | TASLAK |
| H3 | kalan kod kümesi | — | — | — | ⚠️ TANIMLI DEĞİL — uydurulmaz |

**Hata kuralı (E1k):** Yalnız H1/H2 adları geçebilir; yeni kod üretmek
(örn. "E_AUD_RATE") bu dosyaya yetki DEĞİLDİR — küme §16 Q2 çözülünce
tek noktadan (S1) tanımlanır.

## §10 Telemetri ve Gözlem Arayüzü

| # | Kanal | Girdi | Tüketen | Sıklık | Durum |
|---|---|---|---|---|---|
| T1 | durum değişimi | St1–St6 geçişleri | runtime telemetri | her geçişte | TASLAK |
| T2 | hazır bayrağı | `hw_ready` · `path_ready` | K003 | sorgu üzerine | TASLAK |
| T3 | ölçüm özetleri | SNR · THD+N · DC | doğrulama (dogrulama §C) | ölçüm seansında | kapı verisi (D-5..D-7) |
| T4 | hata olayları | E_AUD_* (taslak) | diagnostics/testing | olay anında | TASLAK ⚠️ |

**Telemetri kuralı (T1k):** Telemetri **gözlemdir, komut değildir** — T1–T4
kanallarından geriye komut yazımı tanımlanmaz; komut gerekiyorsa §4 geçiş
sözleşmesi üzerinden yapılır.

## §11 Bellek ve RT Kuralı Bağlantısı

| # | Kural | Uygulama | Dayanak |
|---|---|---|---|
| M1 | RT callback noexcept | durum makinesi geçişleri exception fırlatmaz | K005 RT kuralı 1 (sibling dokümanlar) |
| M2 | RT yolunda malloc yok | bayrak/durum alanları baştan allocate | K005 RT kuralı 2 |
| M3 | RT yolunda mutex yok | durum atomik/tek yazıcı (P6) | K005 RT kuralı 3 |
| M4 | alignas(64) tamponlar | 512-örnek tampon (D-1) hizalı | K005 RT kuralı (sibling) |
| M5 | ASIO exclusive lock | host tarafı kural 9 — audio-control içinde DEĞİL | kapsam dışı (runtime/host) |

**Bellek kuralı (M1k):** Bu dosyadaki arayüzler RT yolunda çağrılabilir;
arayüz sözleşmeleri P7'yi ihlal eden bir çağrı imzası (örn. kopyalama
talebi içeren) önerilemez.

## §12 Sözleşme Değişiklik Kuralı

| # | Değişiklik | Yetki | Adım |
|---|---|---|---|
| C1 | Bayrak adı ekleme/değiştirme | bu dosya (S1 tek nokta) | §2 tablosu + §17 itiraz kaydı |
| C2 | Anayasal kural değişimi (P1–P12, sıra) | **anayasa** | önce audio-control-anayasa.md §12, sonra buraya yansı |
| C3 | Eşik değeri değişimi (D-5/D-6 vb.) | doğrulama | audio-control-dogrulama.md §D tek nokta; burası yalnız okur |
| C4 | Hata kodu kümesi | ⚠️ tanımsız | Q2 çözülmeden C4 uygulanamaz |
| C5 | Komşu arayüzü (K003/device/power) | ilgili komşu + bu dosya | çift onay — tek başına değişmez |

**Değişiklik kuralı (Ch1):** Anayasa > bu dosya > örnekler sırası bozulamaz;
bu dosyadan anayasaya ters etki (C2 ihlali) mimari hata sayılır.

## §13 Bağımlılık Grafiği

```text
audio-control-anayasa (kural) ──► audio-control-surucu (bu dosya) ──► audio-control-ornekler
boot (F5-F8, D2⚠️) ──► this §5 ──► this §2.1
runtime (RT/telemetri) ◄── this §5 D3 · §10
K003-audio ◄──► this §6 (K1/K2 · K4⚠️)
device-control ◄──► this §7 (I1-I4)
power-control ◄──► this §8 (P1-P4)
dogrulama (eşikler D-*) ──► this §10 T3 (yalnız okuma, C3)
```

## §14 Terimler

| # | Terim | Kullanım | Dayanak |
|---|---|---|---|
| L1 | mute · gain · clock | anayasa §12'deki TR-EN çifti birebir korunur | firmware-evrensel-dil.md §2/§5 |
| L2 | SÖZLEŞME | kodla eşleşmemiş ad etiketi | bu dosya S2 |
| L3 | TASLAK | bu dosyada önerilmiş, doğrulanmamış değer/ad | P8 |
| L4 | fail-safe | hata anında mute'a düşme kipi | anayasa M3 |

## §15 Kaynak ve Repo Kanıtı

| # | İddia | Dayanak | Kanıt durumu |
|---|---|---|---|
| E1 | F5–F8 anlamları | boot/boot-surucu.md §2 | okundu 2026-10-14 |
| E2 | E_AUD_LOCK/E_AUD_MISMATCH | firmware-bilesenler.md §23 (H10/H11) | okundu 2026-10-14 |
| E3 | audio hata kümesi "tanımlı değil" | boot/boot-ornekler.md §G notu | okundu 2026-10-14 |
| E4 | Ov4 mute bölünmesi | firmware-kapsam.md §17 | okundu 2026-10-14 |
| E5 | A6 kip sorgusu | firmware-kapsam.md §9.4 A6 | okundu 2026-10-14 |
| E6 | Bayrak sözlüğü §21 (taslak) | firmware-bagimliliklar.md §21 | okundu 2026-10-14 |
| E7 | ≤1200 ms atıf sorunu | boot-dogrulama.md B-13 ≠ süre hedefi | okundu 2026-10-14 (çelişki §17 T3) |
| E8 | Kod yok | 2026-10-13 repo ölçümü | ölçüm — işlev adları sözleşme düzeyinde |

## §16 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | kime ait: K003 mü K005 mı register'ı yazar? (bagimliliklar §4/7) | §6 K4 · §7 I3 bloke | ÇÖZÜLMEDİ — taşındı |
| Q2 | Hata kodu seti ne zaman tanımlanacak? | §9 H3 · §12 C4 | ⚠️ TANIMLI DEĞİL |
| Q3 | TDM kanal eşlemesi / kChannels=12 doğrulanabilir mi? | A-F2 `channels_ok` | UNKNOWN |
| Q4 | Gerçek işlev/imza adları | tüm arayüz adları | ⚠️ SÖZLEŞME düzeyinde |
| Q5 | ≤1200 ms hedefinin gerçek kaynağı | §5 D2 bütçesi | ⚠️ (anayasa Q4 ile aynı) |

## §17 Red Team İtirazları

| # | İtiraz | Cevap | Sonuç |
|---|---|---|---|
| T1 | "Bayrak adları kodda yok, uyduruyorsun." | Doğru — §2.2 tamamı TASLAK etiketli (S2); boot bayrakları ise boot-surucu'dan alındı | kabul, etiket korundu |
| T2 | "index.md F7/F8'i 'record hattı' diyor; boot-surucu F7=DAC_OK diyor." | Çelişki gerçek; S1 gereği boot-surucu tek tanım | çelişki raporlandı |
| T3 | "≤1200 ms'yi boot-dogrulama B-13'ten verdiniz; B-13 orada rollback bayrağı." | İtiraz geçerli | ⚠️ işaretli (Q5 · anayasa T3) |
| T4 | "Hata kodu kümesi yok; iki taslak kodla yetinmek eksik." | Doğru — §9 H3 açıkça TANIMLI DEĞİL | Q2 |
| T5 | "Register adresleri nerede?" | Bilinmiyor — P8 gereği yazılmadı (§7 I1k) | UNKNOWN, kasıtlı |
| T6 | "Durum makinesi gerçekte var mı?" | Hayır — kod yok; tamamı TASLAK (E8) | ⚠️ (Q4) |
| T7 | "power-control ile çift yönlü mute el sıkışması deadlock olur mu?" | P1/P2 tek yönlü istek+onay; kilit device-control'de (I3) | tasarım notu — test G-benzeri doğrulama bekler |
| T8 | "Telemetri kanalı komuta dönüşürse?" | T1k yasağı — gözlem/sınır net | kabul edildi |

## §18 İddia ↔ Dayanak Tablosu

| # | İddia | Dayanak |
|---|---|---|
| C1 | F5–F8 boot'tan gelir ve sesle ilgili | boot-surucu.md §2 |
| C2 | Durum makinesi 6 durumlu TASLAK | anayasa §5 kökleri + bu dosya §3 |
| C3 | A6 sorgusu salt okunur | firmware-kapsam.md §9.4 A6 |
| C4 | mute tetiği power-control'de | firmware-kapsam.md §17 Ov4 |
| C5 | DC > 0,5 V → röle | dogrulama D-7 (ADR-038 H001) |
| C6 | röle ≤ 1 tampon taslak hedef | dogrulama D-8 (gerçek UNKNOWN) |
| C7 | E_AUD_* yalnız taslak | firmware-bilesenler.md §23 |
| C8 | audio hata kümesi resmen yok | boot-ornekler §G notu |
| C9 | bayrak sözlüğü §21 taslak | firmware-bagimliliklar.md §21 |
| C10 | RT noexcept/malloc-yok/mutex-yok | K005 RT kuralları 1–3 (sibling) |
| C11 | SNR/THD+N eşikleri | dogrulama D-5/D-6 |
| C12 | akış kapsam dışı (O2/O3) | firmware-kapsam.md §9.4 A7 |

## §19 Test Köprüsü

| # | Arayüz | Doğrulama | Bağlantı |
|---|---|---|---|
| G1 | §3 durum makinesi | geçiş tablo testi (taslak) | audio-control-ornekler.md |
| G2 | §6 K1 hazır sorgusu | boot→audio→K003 devir senaryosu | bagimliliklar §20 senaryoları |
| G3 | §8 P2 DC/röle | DC ihlali → röle ≤ 1 tampon ölçümü | dogrulama D-7/D-8 (⚠️) |
| G4 | §9 hata kodları | kod kümesi tanımlanınca: tetik→eylem testi | ⚠️ Q2 bloklu |
| G5 | §2 bayrak tutarlılığı | boot-surucu §2 ↔ §2.1 çapraz denetim | boot-surucu.md |

## §20 Kapanış Kontrolü (bu dosya)

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı (yalnız `parent_id`) | evet |
| `group` değeri | tirnaksız `mimari` | evet |
| depends_on | anayasa dosyası (boot-surucu aynası) | evet |
| Uydurma register adresi/hata kodu/sürüm/URL | 0 (taslaklar açıkça TASLAK/⚠️) | 0 |
| Açık soru Q1 | çözülmeden taşındı | evet (§16 Q1) |
| Son satır | Authority satırı (birebir) | evet |
| Bölüm sayısı | 15–24 arası `## §` | 20 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode