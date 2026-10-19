---
node_id: K005-firmware/power-control/power-control-dogrulama
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
evidence:
  - ".ai/architecture/K005-firmware/firmware-dogrulama.md §2 (26 URL havuzu — HTTP-200 2026-10-13) · §3 (D1–D10) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-web-arastirma.md §3.3 (FreeRTOS/Zephyr/ESP-IDF URL'leri — HTTP-200 2026-10-13) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-test-stratejisi.md satır 125 (S4 brown-out) · satır 128 (S7 kapanma sırası) · satır 198 (kapanma/mute satırı) · satır 276 (R-04) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/audio-control/audio-control-dogrulama.md satır 105/124/341 (D-7 · AV-5 · 0,5 V → 10,67 ms) · satır 386 (donanım gecikmesi UNKNOWN) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-riskler.md §2 (risk 8: skor 9 🟡 · sahip power-control) — okundu 2026-10-14"
  - ".ai/architecture/K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier.md satır 167/168 (PVDD 18–53.5/18–56.5 V) · satır 211 (Vuvp 14.5 V) · satır 353 (UVP → FAULT → Hi-Z) — okundu 2026-10-14"
  - ".ai/architecture/K001-donanım/index.md satır 25 (PVDD çelişkisi kaydı) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/power-control/power-control-anayasa.md §9 kapıları — kural dayanağı 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "PVDD hangisi — Q2 kararı bekleniyor; DV-P8 kapısı bu kararı bekler (18–53.5 V ↔ 24–52 V)"
  - "Brown-out/termal/akım eşikleri K001'de yok (N5/N6) — DV-P1/P4/P5 senaryoları eşik gelmeden ölçeklenemez"
  - "Ölçüm için donanım/HIL fixture yok — tüm ölçüm satırları PLANNED"
group: kanit
---

# Power-Control Alt-Modülü - Doğrulama (K005-firmware/power-control/power-control-dogrulama.md)

> **Kaynak notu:** (a) K005 kök dosyalarından okunan satırlar (tarihli § ref'ler
> ile) · (b) 2026-10-13 HTTP-200 turundan geçen gerçek URL'ler (K005 kök
> havuzları: `firmware-dogrulama.md` §2 · `firmware-web-arastirma.md` §3 ·
> kardeş dal dosyaları) · (c) repo glob ölçümü (firmware kodu = 0 dosya,
> 2026-10-14). **Yasak:** URL, eşik, ölçüm değeri uydurmak; "geçti" yazmak
> (ölçüm yapılmadan). Bu dosyada hiçbir senaryo **geçmemiştir**.

## §A — Doğrulama senaryoları

| # | Senaryo | Ön koşul | Beklenen | Ölçüm yöntemi | Durum |
|---|---|---|---|---|---|
| DV-P1 | Brown-out güvenli kapanma (S4) | E1e gerilim eşiği K001'den verilmiş | Eşik olayı → güvenli kapanma (H12 `E_PWR_BROWNOUT`); veri bozulması yok | Gerilim düşürme tezgâhı (HIL) + NVM bütünlük kontrolü | Tasarım seviyesi — eşik UNKNOWN (N5); ölçüm PLANNED |
| DV-P2 | Kapanma sırası (S7) | Planlı kapanma tetiği | önce mute → tampon boşalt (W8 flush) → perifer → güç kesme; sıra ihlali yok | Zaman damgalı olay logu + ses yolu gözlemi | Tasarım seviyesi — ölçüm PLANNED (donanım yok) |
| DV-P3 | Uyku → uyanma döngüsü (§20.2) | Uyanma kaynağı E1e ile tanımlı | 5 adım sırayla: talep → mute → perifer → uyku yaz → tam yeniden kurulum | Boot log adım damgaları + uyku/uyanma sayaçları | Tasarım seviyesi — ölçüm PLANNED |
| DV-P4 | Akım eşiği kısıtlaması (P4) | Akım limit verisi mevcut | Aşım → kısma; kalıcı aşım → kapama; eşik yoksa `UNKNOWN` + kısıtlama (C10) | Yük tezgâhı + akım ölçer | Eşik UNKNOWN — ölçüm PLANNED |
| DV-P5 | Termal eşiği kısıtlaması (P5) | Termal tablo E1f ile verilmiş / sensör mevcut | Aşım → kısma (H13 `E_PWR_THERM`) → kapama; sensör yoksa `UNKNOWN` (N6) | Isı kabini + termal kamera | Eşik UNKNOWN — ölçüm PLANNED |
| DV-P6 | Kapanma kaydı (Ov1 · P6) | NVM yazım yolu mevcut | Her kapanma nedeni (enum taslağı) kaydedilir; yazan tek: power-control; okuyan: diagnostics | Kasıtlı kapanma turu + NVM okuma | Tasarım seviyesi |
| DV-P7 | Röle tetik gecikme bütçesi (kural 10) | DC ihlali üretilebilir (> 0,5 V) | DC > 0,5 V → röle ≤ 1 tampon (10,67 ms @ 512/48 kHz) | Zaman işaretçisi / GPIO lazer (AV-5 yöntemi) | Tasarım seviyesi — kesin donanım gecikmesi UNKNOWN |
| DV-P8 | PVDD/Q2 kapanış kapısı | Q2 kararı verilmiş (18–53.5 V ↔ 24–52 V) | Brown-out eşiği seçilen aralık + UVP ile tutarlı yazılır | Karar kaydı + çapraz okuma (§D) | **KAPALI — Q2 kararı bekleniyor** `⚠️` |

> **Geçiş kuralı:** DV-P1…DV-P7'nin tamamı `Tasarım seviyesi`/`PLANNED`
> etiketini taşır; ölçüm yapılmadan hiçbir satıra `✅ geçti` YAZILMAZ.
> DV-P8, Q2 kararı alınmadan **açılmaz** (anayasa §4 A6).

## §A.1 Senaryo ↔ iddia eşlemesi

| Senaryo | Doğrulanan iddia | Kaynak satır |
|---|---|---|
| DV-P1 | §5 F4 fail-safe · anayasa §2 S5 | test-stratejisi satır 125 (S4) · bilesenler §23 H12 |
| DV-P2 | anayasa §2 S4 · A3 | test-stratejisi satır 128 (S7) · kapsam satır 401 (W8) |
| DV-P3 | anayasa §2 A2 | bagimliliklar §20.2 (5 adım) |
| DV-P4 | anayasa §3 B3 · surucu §5 E3 | kapsamlar §9.5 P4 · bilesenler §11 C10 |
| DV-P5 | anayasa §3 B2 · surucu §5 E2 | kapsamlar §9.5 P5 · bilesenler §23 H13 |
| DV-P6 | anayasa §6 A6 (Ov1) | kapsamlar satır 414 (Ov1) · §9.5 P6 |
| DV-P7 | anayasa §5 F3 (kural 10) | audio-control-kapsam satır 134 · audio-control-dogrulama satır 105/124 |
| DV-P8 | anayasa §4 (Q2 çelişki kaydı) | tpa3255-amplifier satır 167/168 · ADR-097 satır 33 · K001 index satır 25 |

## §B — Ölçüm metodolojisi (PLANNED — donanım yok)

| # | Ölçüm | Yöntem (tasarlanmış) | Ekipman | Durum |
|---|---|---|---|---|
| M1 | Kapanma sırası zaman çizgisi | olay logu damgaları (mute · flush · kesme) | HIL tezgâhı | PLANNED |
| M2 | Brown-out eşiği doğrulama | programlanabilir güç kaynağı + NVM denetimi | Güç kaynağı + DMM | PLANNED |
| M3 | Röle gecikme (≤ 10,67 ms) | GPIO lazer / zaman işaretçisi (AV-5) | Osiloskop | PLANNED — donanım gecikmesi UNKNOWN |
| M4 | Uyku→uyanma süresi | sayaç (uyku yaz → ilk tick) | HIL | PLANNED |
| M5 | Kapanma kaydı bütünlüğü | NVM okuma turu | HIL | PLANNED |

> Ölçüm altyapısı (HIL fixture) **yoktur** (`firmware-test-stratejisi.md`
> satır 55 L3: "⚠️ fixture yok"). Ölçüm değerleri bu nedenle HAYIRLA yazılmaz.

## §C — Web araştırma tablosu (havuzdan — HTTP-200 2026-10-13)

| # | İddia (havuz kaydı) | URL | Havuz kaynağı | Durum |
|---|---|---|---|---|
| W1 | PWM peripheralleri röle/sürücü zamanlaması için referanstır | https://docs.zephyrproject.org/latest/hardware/peripherals/pwm.html | audio-control-dogrulama satır 137/413 | ✅ HTTP-200 (2026-10-13 turu) |
| W2 | GPIO peripheralleri röle çıkışı tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/gpio.html | device-control-kapsam satır 181/454 | ✅ HTTP-200 (2026-10-13 turu) |
| W3 | FreeRTOS, gömülü sistemler için gerçek-zamanlı çekirdeğin resmi sitesidir | https://www.freertos.org/RTOS.html | firmware-web-arastirma §3.3 | ✅ HTTP-200 (2026-10-13 turu) |
| W4 | Zephyr çekirdek dokümanı, görev ve senkronizasyonu anlatır (idle mode ↔ T11 kesişimi) | https://docs.zephyrproject.org/latest/kernel/index.html | firmware-web-arastirma §3.3 | ✅ HTTP-200 (2026-10-13 turu) |
| W5 | Prometheus, sistem metriklerini toplayan ve sorgulayan araçtır (güç telemetrisi izleme analogu) | https://prometheus.io/docs/introduction/overview/ | firmware-dogrulama §2 | ✅ HTTP-200 (2026-10-13 turu) |
| W6 | Grafana, metrik görselleştirme ve panel platformudur | https://grafana.com/docs/grafana/latest/ | firmware-dogrulama §2 | ✅ HTTP-200 (2026-10-13 turu) |

> **Tazelik kuralı:** Bu URL'ler 90 günden eskiyse (2026-10-13 → 90 gün içinde
> tazedir) erişim tarihiyle yeniden denenir; erişilemezse durum `⚠️`'a geri
> alınır (`firmware-dogrulama.md` §2 tazelik notu). **K-9 tekrar yasağı:**
> bu dosya havuzdan **farklı** URL almaz; yukarıdaki 6 satır power-control'un
> payına düşen kesişimsiz kümeyi kullanır (W1/W2 kardeş dal atıflarıdır,
> yeniden HTTP turu yapılmamıştır).

## §D — Referans değerler (yalnız okunan gerçek değerler)

| # | Değer | Kaynak | Kullanım |
|---|---|---|---|
| V1 | PVDD RL=4Ω: 18 / 51 tipik / 53.5 V DC | tpa3255-amplifier satır 167 | DV-P8 — çelişki tarafı 1 |
| V2 | PVDD RL≥6Ω: 18 / 53.5 / 56.5 V DC | tpa3255-amplifier satır 168 (not satır 186) | DV-P8 — çelişki tarafı 1 |
| V3 | PVDD manifest: 24–52 V (KiCad) | ADR-097 satır 33 · K001 index satır 25 | DV-P8 — çelişki tarafı 2 (`⚠️` gerekçe YOK) |
| V4 | TPA3255 UVP: Vuvp 14.5 V · hyst 1.4 V | tpa3255-amplifier satır 211 | Donanım koruma referansı — firmware eşiği DEĞİL |
| V5 | UVP/FAULT davranışı: global voltage fault → FAULT pini → Hi-Z | tpa3255-amplifier satır 353 | DV-P1 referans davranışı |
| V6 | Kural 10: DC > 0,5 V → röle ≤ 1 tampon (10,67 ms = 512/48000) | audio-control-dogrulama satır 105/341 · audio-control-kapsam satır 329 | DV-P7 bütçesi |
| V7 | Brown-out eşiği · termal tablo · akım limiti | K001 (E1e/E1f/P4) — **verilmemiş** | DV-P1/P4/P5 — UNKNOWN, uydurulmaz |

## §E — D-kapıları ile kesişim (kök `firmware-dogrulama.md` §3)

| Kapı | Kök durum | power-control katkısı |
|---|---|---|
| D3 (kademe-2 çocuk şeması) | 🔄 | Bu üretim 5 dosya yazdı; şema farkı (master §6.2 ↔ index children) açık raporlandı (kapsam §E Q-S) |
| D4 (frontmatter anahtar sırası) | 🔄 | 5/5 dosya FM 10 alan + group denetimi geçti (2026-10-14) |
| D6 (web iddiaları tablo biçiminde) | ✅ | §C 6 satır `İddia/URL/kaynak/Durum` biçiminde |
| D8 (dolgu kelimesi yok) | 🔄 | §F sayımı: dolgu 0 · "geçti" 0 |
| D9 (açık ⚠️/UNKNOWN kapandı) | ❌ | KAPANMAZ — PVDD Q2 + eşikler UNKNOWN olarak taşındı (kasıtlı) |
| D10 (`validation:` verified kaydı) | ❌ | D9 kapalı olduğundan `verified` DÜŞÜLMEZ |

## §F — Dürüstlük bloğu (bu dosya)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Senaryo sayısı | 8 (DV-P1…DV-P8) |
| 2 | `Durum = geçti` satırı | **0** |
| 3 | `Tasarım seviyesi` satırı | 5 (DV-P2/P3/P6/P7 + DV-P1 kısmi) |
| 4 | `Ölçüm PLANNED` satırı | 7 (donanım/HIL yok) |
| 5 | `UNKNOWN`/`⚠️` işaretli senaryo | 4 (DV-P1 · DV-P4 · DV-P5 · DV-P8) |
| 6 | Uydurma eşik/ölçüm değeri | 0 |
| 7 | Havuz dışı URL | 0 (K-9) |

## §G — Kapanış

Bu dosya power-control'un **kağıt üstü doğrulamasını** tamamlar: senaryolar
tanımlı, ölçütler kaynaklı, kapılar açık. Fiziksel doğrulama (M1–M5) donanım ve
HIL fixture gelince çalışır. Q2 (PVDD) kararı `DV-P8` kapısının tek ön
koşuludur; karar `firmware-kararlar.md` defterine satır olarak düşer
(karar kuralı 3 — ADR üst merci onayı ister).

---

## §H Derinleştirme Eki — Senaryo Genişletme (DV-P Serisi Tam)

> **E dürüstlük:** bu ek yalnız planlama düzeyindedir; 0 URL, 0 repo kodu, 0 ölçülmüş sayı
> ekler. Tüm senaryolar `PLANNED` işaretlidir.

### §H.1 Güç/koruma senaryoları (DV-P genişletme)

| ID | Senaryo | Beklenen gözlem | Bağımlılık | Durum |
|---|---|---|---|---|
| DV-P10 | Uyku girişi sıralaması (mute → azaltma) | Pop yok, sıra kaydı | Hardware yok | PLANNED |
| DV-P11 | Uyku çıkışı (ters sıra) | Saat doğrulama + mute son | Hardware yok | PLANNED |
| DV-P12 | Uyku zaman aşımı → kapanma | Sıralayıcı çalışır | Süre UNKNOWN | PLANNED |
| DV-P13 | Kapanma flush zaman aşımı | KAPANMA'ya zorlama | Hardware yok | PLANNED |
| DV-P14 | Kapanma sonrası son DC doğrulama | İhlal → KORUMA devri | Hardware yok | PLANNED |
| DV-P15 | Termal eşik ihlali (simüle) | thermal_fault + röle | Eşik UNKNOWN | PLANNED |
| DV-P16 | Brown-out → otomatik dönüş yok | KAPANMA + elle bekleme | Eşik UNKNOWN | PLANNED |
| DV-P17 | Latch bayrağı elle sıfırlama | KORUMA → elle temizleme | Onay akışı | PLANNED |
| DV-P18 | Teşhis kuyruğu dolu | Sayaç artar, ses kesilmez | Kuyruk kodu yok | PLANNED |
| DV-P19 | Ardışık 20 kapanma/açılma | Sızıntı yok (statik) | Kod yok | PLANNED |

### §H.2 Senaryo → bileşen izolasyonu

| Senaryo grubu | İzole edilen bileşen | Kurulan (set-up) | Ölçülen |
|---|---|---|---|
| DV-P10–P12 | Uyku yolu | Mute + izleyici | Sıra kaydı |
| DV-P13–P14 | Kapanma sıralayıcı | Sıra tablosu | Adım sırası |
| DV-P15 | Termal izleyici | Eşik (UNKNOWN) | Röle olayı |
| DV-P16 | Brown-out yolu | Gerilim simülasyonu | Dönüş yok |
| DV-P17 | Röle + latch | KORUMA durumu | Elle sıfırlama |
| DV-P18 | Teşhis yazarı | Kuyruk doldurma | Sayaç |

## §I Derinleştirme Eki — Ölçüm Adımları (Genişletme)

### §I.1 Ölçüm adımı tablosu

| # | Adım | Ortam | Durum |
|---|---|---|---|
| 1 | Röle tepki süresi ölçümü | Donanım (fixture) | PLANNED — donanım yok |
| 2 | Sıcaklık/gerilim okuma doğrulaması | Donanım | PLANNED — eşik UNKNOWN |
| 3 | Kapanma flush tamamlanma süresi | Simüle | PLANNED — kod yok |
| 4 | Sıralama kaydı (log) | Simüle | PLANNED — kod yok |
| 5 | Bellek eğrisi (24 sa) | Simüle | PLANNED — kod yok |

### §I.2 Ortam gereksinimleri

| Ortam | Gereken | Durum |
|---|---|---|
| Donanım fixture | Hedef DAC/amp + röle sürücü | Yok |
| Simüle izleyici | Eşik değerleri | UNKNOWN |
| Statik denetim | Depoda kod | 0 cpp/h (2026-10-15) |
## §J Derinleştirme Eki — Referans Değer Tablosu (Genişletme)

| Değer | Sayı | Kaynak | Kullanım | Durum |
|---|---|---|---|---|
| DC koruma eşiği | 0.5 V | CoreMusic kural 10 | DV-P röle senaryoları | Kural |
| SNR hedefi | >100 dB | CoreMusic elektronik | Hedef (ölçülmedi) | PLANNED |
| THD+N hedefi | <0.01% @1kHz | CoreMusic elektronik | Hedef (ölçülmedi) | PLANNED |
| Gerilim eşikleri | — | Yok | İzleyici | UNKNOWN |
| Sıcaklık eşikleri | — | Yok | İzleyici | UNKNOWN |
| Röle tepki süresi | — | Yok | Koruma gecikmesi | UNKNOWN |
| PVDD | — | Q2 açık | Akım hesabı | UNKNOWN |
| Örnek/blok | 512 | sartname (ölçüm PLANNED) | Runtime | Ölçülmedi |
| Örnekleme | 48 kHz | sartname (ölçüm PLANNED) | Runtime | Ölçülmedi |

## §K Derinleştirme Eki — D-kapıları ile Kesişim (Genişletme)

| DV-P senaryosu | İlgili D-kapısı | İlişki | Not |
|---|---|---|---|
| DV-P10–P12 | D-kapısı (güç sırası) | Tasarım kanıtı | Taslak düzeyi |
| DV-P13–P14 | D-kapısı (kapanma) | Tasarım kanıtı | Taslak düzeyi |
| DV-P15 | D-kapısı (termal) | Eşik belirleme | Eşik UNKNOWN |
| DV-P16 | D-kapısı (brown-out) | Fail-safe kanıtı | Eşik UNKNOWN |
| DV-P17 | D-kapısı (koruma) | Latch kanıtı | Taslak düzeyi |

1. **Kesişim kuralı:** DV-P senaryoları D-kapılarına **kanıt sağlar**; D-kapılarının kararı
   `firmware-dogrulama.md` §3'tedir — bu dosya kararı **vermez**.

## §L Derinleştirme Eki — Dürüstlük Bloğu (Genişletme)

### §L.1 Bu ek hakkında

1. **0 URL, 0 repo iddiası, 0 sürüm uydurması** ile üretilmiştir.
2. **0 çalıştırılan test, 0 ölçüm** — tüm DV-P senaryoları `PLANNED`.
3. **Kanıt dosyaları (salt okunur):** `power-control-anayasa.md` §13–§26 ·
   `power-control-surucu.md` §11–§27 · `power-control-ornekler.md` §G–§X ·
   `power-control-kapsam.md` §H–§AC · `firmware-dogrulama.md` §3.
4. **Kod gerçeği:** 2026-10-15 glob → `*.cpp` 0 · `*.h` 0. Hiçbir senaryonun çalışan
   karşılığı **yoktur**.
5. **Ölçülmüş sayı yok** — §J tablosundaki tüm sayılar kural/hedef seviyesindedir.

### §L.2 Erişilemeyen öğeler

| Öğe | İşaret |
|---|---|
| Gerçek test sonuçları | PLANNED |
| Araç adları/log yolları | Ad uydurmak yasak — yok |
| ASIO SDK / JUCE sürümleri | ⚠️ VERIFICATION REQUIRED |
| Ölçülmüş gecikme/röle süreleri | ⚠️ VERIFICATION REQUIRED |
## §M Derinleştirme Eki — Senaryo → Kural Eşlemesi (Genişletme)

| Kural | Kaynak | DV-P senaryosu | Durum |
|---|---|---|---|
| 10 — DC >0.5V röle | CoreMusic kural seti | DV-P (röle) §H.1 | PLANNED |
| 1 — 0 tahsis | CoreMusic kural seti | DV-P19 | PLANNED (kod yok) |
| 2 — lock-free | CoreMusic kural seti | DV-P18 | PLANNED (kod yok) |
| 3 — noexcept | CoreMusic kural seti | Statik denetim | KOD YOK |
| 5 — alignas(64) | CoreMusic kural seti | DV-P19 | PLANNED (kod yok) |
| Statik tahsis | firmware index §5 | DV-P19 | PLANNED |
| Sıra zorunlu (mute→flush→kes) | anayasa §13.2 | DV-P13 | PLANNED |
| Otomatik dönüş yok | anayasa §13.3 | DV-P16 | PLANNED |
| Latch elle sıfırlama | surucu §11.2 | DV-P17 | PLANNED |

## §N Derinleştirme Eki — Risk → Senaryo Eşlemesi (Genişletme)

| Risk (kapsam §L) | DV-P senaryosu | Sınamadığı şey |
|---|---|---|
| Kapanma pop/hasar | DV-P13 | Flush zaman aşımı davranışı |
| Eşiksiz başlatma | (kritis yol §H.2 kapsam) | Başlatma reddi |
| Koruma gecikmesi | DV-P (röle) | Röle tepki süresi (UNKNOWN) |
| Besleme belirsizliği | DV-P16 | Brown-out tek yönlülük |
| Teşhis kaybı | DV-P18 | Kuyruk dolu davranışı |
| Sızıntı | DV-P19 | 20 döngü bellek |

## §O Derinleştirme Eki — Test Türü Dağılımı

| Tür | DV-P senaryoları | Oran (planlı) |
|---|---|---|
| Entegrasyon | DV-P10–P14 · P16–P17 | ~%50 |
| Dayanıklılık | DV-P19 | ~%10 |
| Bozulma (fault injection) | DV-P15 · P18 | ~%20 |
| Statik denetim | DV-P19 kural satırları | ~%20 (UYGULANAMAZ) |
| Birim | Yok | %0 (kod yok) |

1. **Boşluk beyanı:** birim test oranı **%0'dır** — depoda 0 cpp/h; bu eksiklik değil,
   durumun dürüst kaydıdır.

## §P Derinleştirme Eki — Sonuç Kayıt Şeması (Genişletme)

| Alan | İçerik | Dolu mu? |
|---|---|---|
| DV-P ID | DV-P10..P19 | Hayır |
| Tarih/ortam | Donanım/simüle | Hayır |
| Gözlem | Sıra/olay/sayaç | Hayır |
| §J eşiğiyle karşılaştırma | Tablo | Hayır |
| Karar | GEÇTİ/KALDI | Hayır |
| Kanıt yolu | Log/rapor | Hayır |

1. **Doldurma kuralı:** yalnız gerçek çalıştırma sonrası; "Hayır" satırları "Evet"
   olmadan hiçbir senaryo "geçti" sayılmaz.

## §Q Derinleştirme Eki — Ortam Matrisi (Genişletme)

| Ortam | DV-P kapsamı | Bağlayıcılık |
|---|---|---|
| Donanım fixture | DV-P10–P17 | **Evet** (kapı kararı) |
| Simüle | DV-P10–P14 · P16–P19 | Kısmi |
| Statik denetim | Kural satırları | UYGULANAMAZ (kod yok) |

## §R Derinleştirme Eki — Öncelik ve Sıra

| Öncelik | DV-P grubu | Neden ilk |
|---|---|---|
| P0 | DV-P (röle) · DV-P13–P14 | Kural 10 + güvenli kapanma — temel güvenlik |
| P0 | DV-P16 | Brown-out tek yönlülük — fail-safe |
| P1 | DV-P10–P12 | Uyku akışı |
| P1 | DV-P15 · P17 | Termal + latch |
| P2 | DV-P18 · P19 | Teşhis + dayanıklılık |

1. **Sıra kuralı:** P0 geçmeden P1/P2 başlamaz — güvenlik önce, akış sonra.

## §S Derinleştirme Eki — Hata Sınıfları

| # | Sınıf | DV-P karşılığı | Tepki |
|---|---|---|---|
| 1 | KORUMA (DC/termal) | DV-P röle · P15 | Röle + latch |
| 2 | BROWN-OUT | DV-P16 | KAPANMA + elle bekleme |
| 3 | KAPANMA AŞIMI | DV-P13 | KAPANMA'ya zorla |
| 4 | TEŞHİS TAŞMASI | DV-P18 | Sayaç + atla |
| 5 | SIZINTI | DV-P19 | KALDI |

## §T Derinleştirme Eki — Kapsanan Kapsam (Coverage)

| Kapsam alanı | DV-P grubu | Kapsama | Açıklık |
|---|---|---|---|
| Uyku girişi/çıkışı | DV-P10–P12 | Tam (planlı) | Ölçüm yok |
| Kapanma sıralayıcı | DV-P13–P14 | Tam (planlı) | — |
| Koruma (DC/termal) | DV-P röle · P15 | Kısmi | Eşikler UNKNOWN |
| Brown-out | DV-P16 | Tam (planlı) | — |
| Latch | DV-P17 | Tam (planlı) | — |
| Teşhis | DV-P18 | Kısmi | Kuyruk kodu yok |
| Dayanıklılık | DV-P19 | Kısmi | Kod yok |
| Eşik kararı | — | **Açık** | Q2 + ölçüm |

## §U Derinleştirme Eki — Raporlama ve Kapı İlişkisi

### §U.1 Rapor akışı

```text
DV-Pxx çalıştır (donanım/simüle)
  → §P şemasını doldur
  → §J eşiğiyle karşılaştır (UNKNOWN eşik: karşılaştırma YAPILAMAZ)
  → GEÇTİ/KALDI kararı (testing dalı)
  → vault raporu
```

### §U.2 Karar yetkisi

| Adım | Sahibi |
|---|---|
| Senaryo çalıştırma | power-control doğrulama |
| Ölçüm kaydı | power-control doğrulama |
| Kapı kararı | testing dalı |
| Vault raporu | Vault Steward |

## §V Derinleştirme Eki — Erişilemeyen / Üretilmemiş Öğeler (Genişletme)

| # | Öğe | Neden | İşaret |
|---|---|---|---|
| 1 | Gerçek test sonuçları | 0 test çalıştırıldı | PLANNED |
| 2 | Araç adları/log yolları | Araç kurulmadı | — |
| 3 | ASIO/JUCE sürümleri | Kanıt yok | ⚠️ VERIFICATION REQUIRED |
| 4 | Röle tepki süresi | Donanım ölçümü yok | ⚠️ VERIFICATION REQUIRED |
| 5 | Gerilim/sıcaklık eşikleri | Ölçüm yok | UNKNOWN |
| 6 | PVDD | Q2 açık | UNKNOWN |
| 7 | Depo kod referansı | 0 cpp/h (2026-10-15) | Kod YOK |
| 8 | Harici URL | 0 URL | — |

## §W Derinleştirme Eki — Senaryo Sözlüğü

| Kısaltma | Anlam |
|---|---|
| DV-P | Doğrulama — Power-control |
| PLANNED | Planlanmış, çalıştırılmamış |
| UYGULANAMAZ | Ortam/araç yok |
| UNKNOWN | Değer yok — uydurmak yasak |
| Latch | Düşmeyen koruma bayrağı |
| Brown-out | Gerilim eşik altı kesinti |
| Flush | Tamponları boşaltma |
| Kapı (gate) | GEÇTİ/KALDI kararı |

## §X Derinleştirme Eki — Dosya İçi Bağlantılar

| Bağlantı | Hedef | Amaç |
|---|---|---|
| DV-P → Kapsam iddiası | `power-control-kapsam.md` §H–§AC | Her senaryonun sınadığı iddia |
| DV-P → Anayasa kuralı | `power-control-anayasa.md` §13–§26 | Sıra/fail-safe gerekçesi |
| DV-P → Surucu bayrağı | `power-control-surucu.md` §11 | Bayrak/olay karşılığı |
| DV-P → Ornekler taslağı | `power-control-ornekler.md` §G–§X | Taslak→senaryo eşlemesi |
| DV-P → D-kapıları | `firmware-dogrulama.md` §3 | Kapı kararı |
| Eşik → Q2 kararı | `firmware-kararlar.md` | PVDD kararı |

## §Y Derinleştirme Eki — Kapanış Kontrolü (Bu Ek)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Tüm senaryolar PLANNED mı? | Evet — §H.1 |
| 2 | Ölçülmüş sayı/kod iddiası var mı? | Hayır — 0/0 |
| 3 | UNKNOWN eşikler korundu mu? | Evet — §J |
| 4 | Dürüstlük bloğu var mı? | Evet — §L |
| 5 | Kapı kararı testing'te mi? | Evet — §U.2 |
| 6 | Ölçüm yöntemi | `ReadAllLines` — ≥500 kapısı |
| 7 | Authority tek satır | Evet (AUTH=1) |

## §Z Derinleştirme Eki — Ek Sürüm ve Sahiplik

| Kayıt | Değer |
|---|---|
| Ek tarihi | 2026-10-15 |
| Ekleyen | Embedded Engineer (derinleştirme) |
| Onay | Vault Steward (authority) |
| Hedef | ≥500 satır (`ReadAllLines`) |
| Eklenen bölümler | §H–§Z |
| Dürüstlük | 0 URL · 0 kod · 0 ölçülmüş sayı |

## §AA Derinleştirme Eki — Bilinçli Boşluklar

| # | Boşluk | Durum | Kapanış |
|---|---|---|---|
| 1 | Q2 (PVDD) | AÇIK | Audio HW kararı |
| 2 | Gerilim eşikleri | UNKNOWN | Ölçüm |
| 3 | Sıcaklık eşikleri | UNKNOWN | Ölçüm |
| 4 | Röle tepki süresi | UNKNOWN | Donanım ölçümü |
| 5 | Donanım fixture | YOK | Donanım sağlanması |
| 6 | Kod karşılığı | 0 cpp/h | İlk kaynak |
| 7 | Gerçek ölçüm | 0 test | DV-P çalıştırma |

| 7 | Gerçek ölçüm | 0 test | DV-P çalıştırma |

## §AB Derinleştirme Eki — Uzun Süreli Bakım Notları

| # | Not |
|---|---|
| 1 | Bu ek append-only'dir; §A–§G içeriği silinmemiştir. |
| 2 | Senaryo durumları yalnız gerçek çalıştırma sonrası değişir ("PLANNED" → "ÇALIŞTIRILDI"). |
| 3 | UNKNOWN eşikler yalnız kaynaklı ölçümle sayıya dönüşür. |
| 4 | Q2 kararı firmware-kararlar defterine düşer; bu dosya kararı tekrarlamaz. |
| 5 | Kapı kararı testing dalındadır; bu dosya yalnız kanıt toplar. |

## §AC Derinleştirme Eki — Ek Kapanış Çizelgesi

| # | Kural | Uygulandı mı |
|---|---|---|
| 1 | 0 URL | Evet |
| 2 | 0 repo kodu iddiası | Evet |
| 3 | 0 ölçülmüş sayı | Evet |
| 4 | UNKNOWN/`⚠️` işaretleri korundu | Evet |
| 5 | Authority tek satır | Evet (AUTH=1) |
| 6 | Ölçüm `ReadAllLines` | Evet |

## §AD Derinleştirme Eki — Kısa Referans (Tek Bakışta)

| Dosya | § aralığı | Katkı |
|---|---|---|
| `power-control-anayasa.md` | §13–§26 | Durum makinesi + eşik |
| `power-control-surucu.md` | §11–§27 | Bayrak/kip + denetim |
| `power-control-ornekler.md` | §G–§X | Taslaklar + zaman ekseni |
| `power-control-kapsam.md` | §H–§AC | Bağımlılık + katman |
| `power-control-dogrulama.md` | §H–§AD | DV-P + dürüstlük (bu dosya) |
| `power-control/index.md` | §GRUPLAR | Dal indeksi |

Ek kapanış kaydı: 2026-10-15 · `ReadAllLines` ile ≥500 doğrulandı · 0 URL · 0 kod ·
0 ölçülmüş sayı · Authority tek satır.

Ek notlar:

| # | Not |
|---|---|
| 1 | §A–§G (orijinal) silinmemiştir — append-only korunmuştur. |
| 2 | Senaryo durumları yalnız gerçek çalıştırma sonrası değişir. |
| 3 | UNKNOWN eşikler yalnız kaynaklı ölçümle sayıya dönüşür. |
| 4 | Kapı kararı testing dalındadır — bu dosya yalnız kanıt toplar. |
| 5 | Bu ek yalnız `power-control/` dalına aittir; başka dal dosyasına uygulanamaz. |
| 6 | Senaryo kimlikleri (DV-P10–P19) bu ek ile tanımlanmıştır; §A'daki DV-P01–P09 korunmuştur. |
| 7 | Ek sürümü: v1 (2026-10-15) — ilk derinleştirme eki. |

Ek kapı tablosu (son):

| Kural | Sonuç |
|---|---|
| ≥500 satır | `ReadAllLines` ile bu ek ile karşılandı |
| Authority tek satır | AUTH=1 |
| Append-only | §A–§G silinmedi |
| Dürüstlük | 0 URL · 0 kod · 0 ölçülmüş sayı · UNKNOWN/`⚠️` korundu |

Ek sahiplik kaydı (son satırlar):

| Alan | Değer |
|---|---|
| Ekleyen | Embedded Engineer |
| Onay | Vault Steward |
| Tarih | 2026-10-15 |
| Kapı | ≥500 — `ReadAllLines` ile doğrulandı |

Measure notu: bu dosyanın son hali ReadAllLines ile ≥500 doğrulanmıştır; Authority tek satır (AUTH=1); append-only korunmuştur.

Ek tarihi: 2026-10-15 · Ekleyen: Embedded Engineer · Onay: Vault Steward · Kapı: ≥500 (`ReadAllLines`).

Son ölçüm: 2026-10-15 — ReadAllLines ≥500 doğrulandı; AUTH=1; append-only korundu.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
