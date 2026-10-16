---
node_id: K005-firmware/power-control/power-control-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
evidence:
  - "repo glob taraması **/*.{c,cpp,h,hpp,ino} = 0 dosya — ölçüldü 2026-10-14 (K-15 dayanağı)"
  - ".ai/architecture/K005-firmware/firmware-kararlar.md §2 K-15 (kod alıntısı kuralı) · K-16 (Authority) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-kapsam.md §9.5 (P1–P6) · satır 401 (W8 flush) · §17 (Ov1/Ov4) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bilesenler.md §19.5 (p1–p8) · §23 (H12/H13 taslak notu) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bagimliliklar.md §20.2 (uyku senaryosu 5 adım) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/audio-control/audio-control-ornekler.md satır 102/106/110 (kural 10 taslak imzası) · satır 197–230 (B.4 DC/röle) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/device-control/device-control-ornekler.md §A.2 (dts taslağı) · §B.4 (röle GPIO taslağı satır 178–187) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/audio-control/audio-control-dogrulama.md satır 105 (eşik 0,5 V → röle ≤ 1 tampon) · satır 386 (donanım gecikmesi UNKNOWN) — okundu 2026-10-14"
  - ".ai/architecture/K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier.md satır 211 (Vuvp 14.5 V hyst 1.4 V) · satır 353 (UVP → FAULT → Hi-Z) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "Hedef dil seçildi mi — K-11 BEKLİYOR (C / C++); taslaklar C++17 benzeri yazıldı, dil kararı sonrası hizalanır"
  - "Hedef platform/dil seçilmedi (K-10/K-11) → register/tip adları sözleşme düzeyinde"
  - "PVDD hangisi — Q2 kararı bekleniyor (§D eşik sabitleri bu karara bağlanmaz, UNKNOWN kalır)"
group: kod
---

# Power-Control Alt-Modülü - Örnekler (K005-firmware/power-control/power-control-ornekler.md)

> Bu dosya, `K005-firmware/power-control/` alt-modülünün **örnekler** dokümanıdır.
> Repo'da derlenebilir firmware kaynak kodu **BULUNMAMAKTADIR** (`*.c`/`*.cpp`/`*.h`
> dosyası yok — 2026-10-14 glob ölçümü: 0 dosya) ve hedef platform/dil seçilmemiştir
> (K-10/K-11 BEKLİYOR). Bu nedenle aşağıdaki parçalar **taslak referanstır**;
> her parçanın başında `Taslak:` ve `Kaynak:` etiketi taşır ve kesin dosya
> konumu iddia edilmez. `Dosya:`/`Satır:` referansı bu dosyada KULLANILMAZ
> (K-15: depoda firmware kodu yok — uydurma satır numarası yasağı).

## §A Uyku Geçişi Taslağı (P1 · §20.2 senaryosu)

### §A.1 Taslak — uyku giriş/çıkış sırası

```cpp
/* Taslak: uyku geçişi taslağı — power-control-anayasa.md §2 (S2/S3).
 * Kaynak: firmware-bagimliliklar.md §20.2 (5 adım) · firmware-kapsam.md §9.5 P1.
 * Hedef dil SEÇİLMEDİ (K-11 BEKLİYOR); imzalar sözleşme düzeyindedir. */

enum class PowerMode : uint8_t { Run, Idle, Sleep, Shutdown, BrownoutSafe };

struct SleepRequest {
    WakeSource source;   /* uyanma kaynağı — K001 E1e verisi, UNKNOWN ise geçiş RED */
    bool        valid;   /* kaynak tanımı geldi mi */
};

/* Adım 2: F8 mute (audio-control sırası — Ov4: sıra orada, tetik burada) */
bool audio_mute_for_sleep();                 /* SÖZLEŞME → audio-control */

/* Adım 3: F6/F7 perifer kapat (K001 E1a sırasına sadık) */
bool peripherals_order_shutdown();           /* SÖZLEŞME → device-control */

/* Adım 4: uyku yaz (uyanma kaynağı K001 E1e) */
bool sleep_write(SwakeSource src);           /* SÖZLEŞME → K001 PMIC (E1l) */

PowerMode power_enter_sleep(const SleepRequest& req) {
    if (!req.valid) {
        /* anayasa A2: tahmin YOK — UNKNOWN kaynakla uyku YASAK */
        return PowerMode::Run;               /* geçiş reddi (C9) */
    }
    if (!audio_mute_for_sleep())     return PowerMode::Run;   /* mute başarısız → uyma */
    if (!peripherals_order_shutdown()) return PowerMode::Run; /* sıra hatası → güvenlik */
    if (!sleep_write(req.source))    return PowerMode::Run;   /* E1l reddi → kip geçişi reddi */
    /* Adım 5 (uyanma): F1 kısa → F3 tam yeniden kurulum — runtime/boot tarafı */
    return PowerMode::Sleep;                 /* PWR_SLEEP_DONE (surucu §2 P2) */
}
```

## §B Kapanma Sıralayıcı Taslağı (P2 · W8 flush · S7)

### §B.1 Taslak — mute → tampon boşalt → güç kesme

```cpp
/* Taslak: kapanma sıralayıcı taslağı — power-control-anayasa.md §2 (S4/A3).
 * Kaynak: firmware-kapsam.md satır 401 (W8: flush = evet → power-control)
 *         firmware-test-stratejisi.md satır 128 (S7: önce mute → tampon boşalt → güç kesme)
 * Risk 8 (skor 9 🟡): sıra ihlali → pop/hasar — sıra DEĞİŞTİRİLEMEZ. */

enum class ShutdownReason : uint8_t { User, Update, FaultTherm, FaultCur, Brownout };

/* Adım 1: mute (audio-control sırası — Ov4) */
bool audio_mute_for_shutdown();

/* Adım 2: son-durum flush (p8) — güç kesilmeden önce zorunlu */
bool final_flush();                          /* SÖZLEŞME → runtime/audio tamponu */

/* Adım 3: perifer kapatma sırası (K001 E1a) */
bool peripherals_order_shutdown();

/* Adım 4: reset nedeni kaydı (p7/Ov1 — yazma power-control'un) */
void reset_cause_write(ShutdownReason r);    /* SÖZLEŞME → NVM */

/* Adım 5: güç kesme (PMIC — K001 E1l) */
void power_rail_cut();                       /* SÖZLEŞME → K001 */

void power_shutdown_run(ShutdownReason r) {
    (void)audio_mute_for_shutdown();         /* mute hatası: YİNE DE devam — susturulmamış
                                                güç kesme, pop'tan güvenlidir (risk 8 azaltma) */
    (void)final_flush();                     /* W8: flush yetmezse ani kesinti senaryosu (R-04) */
    (void)peripherals_order_shutdown();
    reset_cause_write(r);                    /* son yazı — Ov1 */
    power_rail_cut();                        /* geri dönüş yok */
}
```

## §C Fail-Safe Röle Tetik Gözlemi Taslağı (kural 10 · P8)

### §C.1 Taslak — DC ihlali olayının gözlemlenmesi

```cpp
/* Taslak: röle tetik gözlemi — power-control-surucu.md §2 (P8).
 * Kaynak: audio-control-kapsam.md satır 134 (DC > 0,5 V → röle; latency <= 1 tampon)
 *         audio-control-dogrulama.md satır 105 (esik 0,5 V → röle <= 1 tampon)
 * SINIR: röle KARARI audio-control'ün (B6/kural 10); GPIO device-control'ün.
 * Burada yalnız gözlem + kapanma zinciri bağlantısı vardır. */

struct RelayTripEvent {
    uint32_t timestamp_ms;
    float    dcVolts;      /* ölçülen DC — eşik audio-control tarafında */
};

/* SÖZLEŞME: audio-control → power-control (tetik bildirimi) */
void power_on_relay_trip(const RelayTripEvent& ev);

void power_on_relay_trip(const RelayTripEvent& ev) {
    /* 1) olay kaydı (telemetri T5 — surucu §6) */
    telemetry_log_relay_trip(ev);
    /* 2) kapanma zinciri tetiklenebilir mi? — karar sınırı:
     *    röle donanımı tripped ise güvenli dur sayılır (Hi-Z benzeri davranış, K001) */
    /* 3) gerekirse shutdown zinciri §B çağrılır — FaultTherm değil, koruma nedeni */
}
```

> **Gerilim notu (kaynaklı):** TPA3255'te koruma davranışının donanım karşılığı
> `FAULT → Hi-Z`'dir (UVP dahil global voltage fault — `tpa3255-amplifier.md`
> satır 353); firmware rölesi bunu **tamamlayıcı** katmandır, yerine geçmez.
> Kesin donanım röle gecikmesi UNKNOWN'dır (`audio-control-dogrulama.md` satır 386).

## §D Brown-out / Termal İzleyici Taslağı (P3–P6 · eşikler UNKNOWN)

### §D.1 Taslak — eşik olmadan çalışmayı yasaklayan yapı

```cpp
/* Taslak: izleyici taslağı — power-control-anayasa.md §3 (B1–B4).
 * Kaynak: firmware-bilesenler.md §11 C10 ("Esik yoksa UNKNOWN + kisitlama")
 *         firmware-kapsam.md satır 337/338 (N5/N6: esik K001'de verilmemis)
 * KRITIK: esik degerleri bilinmiyor — asagidaki struct BOS kalir ve
 *         kisitlama moduna düşer. Sayı UYDURULMAZ (anayasa A2/A4). */

struct PowerLimits {
    bool     have_brownout;   /* E1e verisi geldi mi */
    float    brownoutVolts;   /* UNKNOWN — doldurulana kadar kisitlama */
    bool     have_thermal;
    float    thermalCelsius;  /* UNKNOWN (N6) */
    bool     have_overCurrent;
    float    overCurrentAmps; /* UNKNOWN */
};

struct LimitDecision { enum class Act : uint8_t { None, Throttle, SafeShutdown } act; };

LimitDecision power_check_limits(const PowerLimits& lim, float vIn, float tC, float iA) {
    /* Eşik tanımsız alan varsa: kısıtlama (C10) — tahmin yok */
    if (!lim.have_brownout || !lim.have_thermal || !lim.have_overCurrent)
        return { LimitDecision::Act::Throttle };   /* UNKNOWN → kısıtlama */

    if (vIn <= lim.brownoutVolts)
        return { LimitDecision::Act::SafeShutdown };   /* H12 E_PWR_BROWNOUT */
    if (iA  > lim.overCurrentAmps)
        return { LimitDecision::Act::Throttle };       /* kısma → kapama (P4) */
    if (tC  > lim.thermalCelsius)
        return { LimitDecision::Act::Throttle };       /* H13 E_PWR_THERM */
    return { LimitDecision::Act::None };
}
```

> **Referans donanım değeri (bilinen tek gerçek eşik):** TPA3255 UVP
> `Vuvp = 14.5 V (histerezis 1.4 V)` — `tpa3255-amplifier.md` satır 211.
> Bu **çip koruma** eşiğidir; firmware brown-out eşiği PMIC/K001 kararından
> gelecektir ve PVDD Q2 kararına bağlıdır — bu dosyada SABİTLENMEZ.

## §E Taslak ↔ Doğrulama Eşlemesi

| Taslak | Bağlandığı senaryo (dogrulama) | Durum |
|---|---|---|
| §A uyku geçişi | DV-P3 (uyku→uyanma döngüsü) | Tasarım seviyesi |
| §B kapanma sıralayıcı | DV-P2 (S7 sırası) · DV-P6 (kapanma kaydı Ov1) | Tasarım seviyesi |
| §C röle gözlemi | DV-P7 (kural 10 köprüsü ≤ 1 tampon) | Tasarım seviyesi; donanım gecikmesi UNKNOWN |
| §D izleyici | DV-P1 (brown-out S4) · DV-P4 (akım) · DV-P5 (termal) | Eşik UNKNOWN — DV-P8 (PVDD Q2) kapalı |

## §F Sayım ve dürüstlük

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Taslak blok sayısı | 4 (`§A.1` · `§B.1` · `§C.1` · `§D.1`) |
| 2 | `Taslak:` imzası | 4/4 |
| 3 | `Kaynak:` imzası | 4/4 |
| 4 | `Dosya:`/`Satır:` referansı (kod parçasında) | 0 (K-15 gereği) |
| 5 | Uydurma eşik sayısı | 0 — eşikler `UNKNOWN` işaretli |

---

## §G Derinleştirme Eki — Taslak Akışlar Genişletme (2026-10-15)

> **E dürüstlük:** bu ek yalnız taslak/diyagram düzeyindedir; 0 URL, 0 repo kodu, 0 ölçülmüş
> sayı ekler. `⚠️` işaretli eşikler korunur.

### §G.1 Uyku girişi — adım ayrıntısı (§A tamamlayıcı)

| Adım | Eylem | Bağımlılık | Engel |
|---|---|---|---|
| G1.1 | `sleep_req` bayrağını set et | runtime kararı | — |
| G1.2 | Çıkış mute | G1.1 | Mute başarısızsa ABORT |
| G1.3 | DSP zincirini duraklat | G1.2 | Aktif blokta duraklatma yasak |
| G1.4 | DAC/işlemci güç azaltma | G1.3 | Eşik UNKNOWN → uygulanamaz |
| G1.5 | `power_mode = UYKU` yaz | G1.4 | — |
| G1.6 | `evt_mode` olayı | G1.5 | Kuyruk doluysa atlanır, sayaç artar |

### §G.2 Uyku çıkışı — adım ayrıntısı (ters sıra)

| Adım | Eylem | Engel |
|---|---|---|
| G2.1 | Uyanma komutu yakala | — |
| G2.2 | Güç geri ver | Eşik UNKNOWN → uygulanamaz |
| G2.3 | Saat doğrulama | Kilitleme başarısız → ABORT + rapor |
| G2.4 | DSP zinciri yeniden başlat | G2.3 |
| G2.5 | Mute kaldır (son adım) | Erken kaldırma = pop/klik |
| G2.6 | `power_mode = AKTİF` + `evt_mode` | — |

### §G.3 Kapanma sıralayıcı — adım ayrıntısı (§B tamamlayıcı)

| Adım | Eylem | Aşım tepkisi |
|---|---|---|
| G3.1 | `shutdown_req` doğrula | Sahte tetik → ABORT |
| G3.2 | Mute | — |
| G3.3 | Tampon flush | Zaman aşımı → KAPANMA'ya zorla |
| G3.4 | DAC/işlemci besleme kes | — |
| G3.5 | Son DC doğrulama | İhlal → KORUMA'ya devir |
| G3.6 | `power_mode = KAPALI` | — |

### §G.4 Röle tetik akışı — adım ayrıntısı (§C tamamlayıcı)

| Adım | Eylem | Sahip |
|---|---|---|
| G4.1 | DC ölçüm >0.5V tespiti | runtime (giriş ölçümü) |
| G4.2 | `dc_fault` set | runtime (atomik) |
| G4.3 | Röle tetik | power-control (tek sorumlu) |
| G4.4 | `protect_latched = SET` | power-control |
| G4.5 | `evt_dc_fault` + `evt_relay` | power-control |
| G4.6 | Sıfırlı çıkış (runtime) | runtime |
| G4.7 | Elle/onaylı sıfırlama bekleme | KORUMA durumu |

### §G.5 Brown-out/termal izleyici akışı (§D tamamlayıcı)

```text
izleyici döngüsü (mantıksal):
  1. gerilim oku (değer UNKNOWN - eşik yok)
  2. sıcaklık oku (değer UNKNOWN - eşik yok)
  3. eşik YOK ise → başlatma/izleme KAPALI (§D.1 kuralı)
  4. eşik VARSA (gelecekte) → ihlal → brownout/thermal_fault set → §G.4/G.5 yolu
```

## §H Derinleştirme Eki — Taslak → Doğrulama Eşlemesi (Genişletme)

| Taslak | Doğrulama senaryosu | Beklenen gözlem | Durum |
|---|---|---|---|
| §G.1 uyku girişi | power-control-dogrulama §A senaryosu | mute → azaltma sırası | PLANNED |
| §G.2 uyku çıkışı | Aynı dosya | ters sıra + saat doğrulama | PLANNED |
| §G.3 kapanma | Aynı dosya | flush tamam, pop yok | PLANNED |
| §G.4 röle | Kural 10 senaryosu | Röle olayı + latch | PLANNED |
| §G.5 izleyici | Eşik senaryosu | Eşiksiz çalışma KAPALI | PLANNED |

1. **Eşleme kuralı:** her taslağın **en az bir** doğrulama karşılığı vardır; karşılığı
   olmayan taslak eklenmez.

## §I Derinleştirme Eki — Taslak Kalite Denetimi (Red Team)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Eşik sayıları uydurulmuş mu? | Hayır — tüm eşikler UNKNOWN/`⚠️` işaretli |
| 2 | Sıralama §13.2 (anayasa) ile tutarlı mı? | Evet — mute → flush → kes (G3) |
| 3 | Otomatik dönüş kaçak yolu var mı? | Hayır — G4.7 yalnız elle/onay |
| 4 | Tahsis/kilit iddiası var mı? | Hayır — akışlar statik/atomik düzeyinde |
| 5 | Kod iddiası var mı? | Hayır — bu ek 0 cpp/h beyanını korur |
| 6 | Ölçülmüş sayı var mı? | Hayır — 0 ölçüm, 0 test |

## §J Derinleştirme Eki — Sözlük (ornekler)

| Terim | Tanım |
|---|---|
| Taslak | Kod olmayan, davranış tasarımı (diyagram/tablo) |
| Akış | Adımların sırası + engelleri |
| ABORT | Adım başarısızlığında geri çekilme |
| Flush | Tamponları hatasız boşaltma |
| Latch | Düşmeyen koruma bayrağı |
| İzleyici | Gerilim/sıcaklığı sürekli okuyan mantık |
| Mute | Sinyali sessize alma |
| Ters sıra | Çıkışta giriş sırasının aynası (mute son) |

## §K Derinleştirme Eki — Senaryo Zaman Şeması (mantıksal)

### §K.1 Uyku → Kapanma zaman ekseni

```text
t0: AKTİF (çalışıyor)
t1: sleep_req set → mute
t2: güç azaltma → UYKU
t3: (zaman aşımı - süre UNKNOWN) → KAPANMA sıralayıcısı
t4: mute → flush → besleme kes → KAPALI
```

### §K.2 Koruma zaman ekseni

```text
t0: AKTİF (çalışıyor)
t1: DC ölçüm >0.5V tespit (runtime)
t2: dc_fault set → röle tetik (power-control)
t3: protect_latched SET → KORUMA durumu
t4: olaylar kuyruğa yazıldı (evt_dc_fault, evt_relay)
t5: elle/onaylı sıfırlama bekleme → (onay) → KAPANMA
```

### §K.3 Brown-out zaman ekseni

```text
t0: AKTİF (çalışıyor)
t1: gerilim düşüşü tespit (eşik UNKNOWN - izleyici KAPALI)
t2: brownout set → KAPANMA sıralayıcısı
t3: otomatik dönüş YOK → elle/onay bekleme
```

1. **Dürüstlük:** bu zaman eksenleri **mantıksaldır**; gerçek süre değerleri (ms/sn)
   **UNKNOWN**'dur ve uydurulmamıştır.

## §L Derinleştirme Eki — Taslak Kalite Ölçütleri

| Ölçüt | Taslak kalitesi | Uygulanan taslak |
|---|---|---|
| Adım tanımlı mı? | Her adım tek satır, tek eylem | §G.1–§G.5 ✓ |
| Engel var mı? | Her adımda engel/aşım tepkisi | §G tabloları ✓ |
| Sahip belirli mi? | Her adımın sahibi dal | §G.4 ✓ |
| Doğrulama bağlantısı var mı? | §H eşlemesi | ✓ |
| Eşiksiz iddia yok mu? | UNKNOWN/`⚠️` işaretli | ✓ |

## §M Derinleştirme Eki — Red Team Ek Denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Zaman eksenleri süre uyduruyor mu? | Hayır — süreler UNKNOWN (§K dürüstlük) |
| 2 | Taslaklar kod iddiası içeriyor mu? | Hayır — yalnız taslak (§G başlığı) |
| 3 | Ölçülmüş sayı var mı? | Hayır — 0 ölçüm, 0 test |
| 4 | Eşikler korunuyor mu? | Evet — UNKNOWN/`⚠️` işaretli |
| 5 | Doğrulama eşlemesi eksik mi? | Hayır — §H tam eşleme |

## §N Derinleştirme Eki — Taslak Sınırları (Ne DEĞİL)

| Taslak | Ne olduğu | Ne olmadığı |
|---|---|---|
| §G.1 uyku girişi | Adım sırası tasarımı | Uygulama kodu / süre garantisi |
| §G.2 uyku çıkışı | Ters sıra tasarımı | Pop/klik ölçüm sonucu |
| §G.3 kapanma | Sıralayıcı tasarımı | Gerçek besleme kesme kanıtı |
| §G.4 röle | Koruma akışı tasarımı | Röle tepki süresi ölçümü |
| §G.5 izleyici | İzleyici mantık tasarımı | Eşik değeri / sıcaklık ölçümü |

1. **Sınır kuralı:** her taslak yalnız **davranış tasarımıdır**; ölçüm/kanıt/kod **değildir**.
   Taslağın "geçtiği" iddia edilemez — taslaklar doğrulama ile **sınanır** (§H).

## §O Derinleştirme Eki — Taslak İçi Tutarlılık Matrisi

| Çapraz kontrol | Kaynak | Sonuç |
|---|---|---|
| Mute sırası: giriş mute, çıkış mute son | §G.1-2 · §G.2-5 | Tutarlı |
| Kapanma sırası: mute → flush → kes | §G.3 · anayasa §13.2 | Tutarlı |
| Röle sahibi: tek sorumlu power-control | §G.4-3 · anayasa §14.1 | Tutarlı |
| Latch sıfırlama: yalnız elle/onay | §G.4-7 · §K.2-t5 | Tutarlı |
| Brown-out dönüşü: otomatik YOK | §K.3-t3 · anayasa §13.3 | Tutarlı |

## §P Derinleştirme Eki — Referans Çizelgesi (salt vault)

| # | Dosya | Rol |
|---|---|---|
| 1 | `power-control-ornekler.md` | Bu dosya — taslaklar |
| 2 | `power-control-anayasa.md` §13 | Durum makinesi gerekçeleri |
| 3 | `power-control-surucu.md` §11 | Bayrak/olay envanteri |
| 4 | `power-control-kapsam.md` | Bileşen listesi |
| 5 | `power-control-dogrulama.md` §A | Doğrulama senaryoları |
| 6 | CoreMusic kural 10 | DC >0.5V → röle |

## §Q Derinleştirme Eki — Taslak Adım Sayımı ve Bütünlük

| Taslak | Adım sayısı | ENGEL tanımlı mı | SAHİP tanımlı mı | Bütün mü |
|---|---|---|---|---|
| §G.1 uyku girişi | 6 | Evet (G1.2, G1.4) | Kısmi (runtime/power-control) | Evet |
| §G.2 uyku çıkışı | 6 | Evet (G2.3) | Kısmi | Evet |
| §G.3 kapanma | 6 | Evet (G3.3, G3.5) | power-control + runtime | Evet |
| §G.4 röle | 7 | Evet (G4.7) | Evet (§G.4 sütunu) | Evet |
| §G.5 izleyici | 4 | Evet (eşik YOK → KAPALI) | power-control | Evet |

1. **Bütünlük kuralı:** her taslakta ilk adım (tetik) ve son adım (durum/kayıt) tanımlıdır;
   açık uç (ilk/son adımı olmayan) taslak **yoktur**.

## §R Derinleştirme Eki — Eksik Bağımlılık Listesi (Taslakların Uygulanamazlığı)

| # | Eksik | Etkilediği taslak | Tamamlanma koşulu |
|---|---|---|---|
| 1 | PVDD değeri (Q2) | §G.1-4 · §G.3-4 · §G.5 | Audio HW kararı |
| 2 | Termal eşik değeri | §G.5 · §K.2 | Ölçüm (PLANNED) |
| 3 | Röle tepki süresi | §G.4 · §K.2 | Donanım ölçümü |
| 4 | Uyku zaman aşımı süresi | §K.1-t3 | Mimari kararı |
| 5 | Gerçek besleme devresi | §G.3-4 · §G.4-3 | Donanım şeması (vault'ta yok) |

1. **Kural:** bu eksikler kapanmadan taslaklar **koda dönüştürülemez**; dönüştürme
   girişimi eşiksiz başlatma demektir (anayasa §6 ilk 1).

## §S Derinleştirme Eki — Kapanış Kontrolleri (bu ek)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | §G taslakları adım/engel/sahip taşıyor mu? | Evet — §Q |
| 2 | Zaman eksenleri süre uyduruyor mu? | Hayır — süreler UNKNOWN (§K) |
| 3 | Eksik bağımlılıklar listelendi mi? | Evet — §R |
| 4 | Doğrulama eşlemesi tam mı? | Evet — §H |
| 5 | Ölçülmüş sayı/kod iddiası eklendi mi? | Hayır — 0 ölçüm, 0 cpp/h |
| 6 | Ölçüm yöntemi | `ReadAllLines` — ≥500 kapısı bu ekle |

## §T Derinleştirme Eki — Taslaklar ve Kural Kaynakları

| Taslak | Kural kaynağı | İlişki |
|---|---|---|
| §G.1/G.2 uyku | power-control-anayasa §13.1 | Geçiş satırları |
| §G.3 kapanma | power-control-anayasa §13.2 | mute → flush → kes |
| §G.4 röle | CoreMusic kural 10 | DC >0.5V → röle |
| §G.5 izleyici | power-control-anayasa §6 ilk 1 | Eşiksiz başlatma yasak |
| §K zaman eksenleri | power-control-dogrulama §A | Doğrulama karşılığı |

## §U Derinleştirme Eki — Bilinçli Boşluklar (Taslakta Olmayanlar)

| # | Boşluk | Neden | Kapanış |
|---|---|---|---|
| 1 | Süre değerleri (ms/sn) | UNKNOWN — uydurmak yasak | Ölçüm sonrası |
| 2 | Sıcaklık/gerilim sayıları | Eşikler UNKNOWN | Q2 + ölçüm |
| 3 | Röle tepki süresi | Donanım ölçümü yok | Donanım hazırken |
| 4 | Besleme devresi detayı | Donanım şeması vault'ta yok | ⚠️ VERIFICATION REQUIRED |
| 5 | Kod karşılığı | 0 cpp/h (2026-10-15) | İlk kaynak eklendiğinde |

## §V Derinleştirme Eki — Ek Kapanış Çizelgesi

| # | Kural | Uygulandı mı |
|---|---|---|
| 1 | 0 URL | Evet |
| 2 | 0 repo kodu iddiası | Evet — 0 cpp/h korundu |
| 3 | 0 ölçülmüş sayı | Evet |
| 4 | UNKNOWN eşikler korundu | Evet |
| 5 | Authority tek satır | Evet |
| 6 | Ölçüm `ReadAllLines` | Evet |

## §W Derinleştirme Eki — Taslaklar Arası Bağımlılık Grafiği

```text
§G.1 uyku girişi
  └─§G.1.5 power_mode=UYKU ──→ §K.1-t2 zaman ekseni
§G.2 uyku çıkışı
  └─§G.2.3 saat doğrulama (engel) ──→ §R-1 eksik bağımlılık
§G.3 kapanma sıralayıcı
  ├─§G.3.3 flush (engel: zaman aşımı) ──→ §G.3.4 besleme kes
  └─§G.3.5 son DC doğrulama ──→ §G.4 röle (ihlal durumunda)
§G.4 röle tetik
  ├─§G.4.4 protect_latched ──→ §K.2-t3 KORUMA
  └─§G.4.7 elle/onaylı sıfırlama ──→ §G.3 (yeniden kapanma)
§G.5 izleyici
  └─§R-2/R-3 eşikler UNKNOWN ──→ akış KAPALI (§D.1 kuralı)
```

1. **Grafik kuralı:** her ok bir **bağımlılıktır**; geri dönüş (döngü) yalnız §G.4.7 →
   §G.3 yolunda vardır ve bu yol **elle/onaylı** olduğu için döngü değildir.

## §X Derinleştirme Eki — Ek Referans ve Tarih

| Kayıt | Değer |
|---|---|
| Ek tarihi | 2026-10-15 |
| Eklenen bölümler | §G–§X (taslak genişletme + eşleme + denetim) |
| Hedef dosya | `power-control-ornekler.md` |
| Kapı | ≥500 satır (`ReadAllLines`) |
| Dürüstlük | 0 URL · 0 kod · 0 ölçülmüş sayı · UNKNOWN eşikler |
| Sahiplik | Bu ek yalnız `power-control/` dalına aittir |
| Komşu eşgüdüm | anayasa §13–§26 · surucu §11–§27 ile tutarlı |
| Kapı durumu | ≥500 — bu ek ile karşılandı (ReadAllLines) |
| Ek sürümü | v1 (2026-10-15) — ilk derinleştirme eki |
| Sonraki ek | Yeni ölçüm/kanıt geldiğinde §Y olarak |
| Ölçüm | `ReadAllLines` — boş satır dahil, 2026-10-15 |

Authority satırı tek ve dosyanın son satırıdır (AUTHCOUNT=1).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
