---
node_id: K005-firmware/power-control/power-control-anayasa
node_type: module
status: draft
parent_id: K005-firmware/power-control
children: []
depends_on:
  - K005-firmware
  - K001-donanım
evidence:
  - ".ai/architecture/K005-firmware/power-control/index.md — okundu 2026-10-14 (ELI15 · PVDD Q2 açık sorusu)"
  - ".ai/architecture/K005-firmware/firmware-kapsam.md §9.5 (P1–P6) · §14 I7 · §15 (N5/N6/N12) · satır 401 (W8 flush) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bilesenler.md §4 (F9/F10) · §10 (M9/M10) · §11 (C9/C10) · §19.5 (p1–p8) · §23 (H12/H13) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bagimliliklar.md §9.1 (E1e/E1f/E1l) · §20.2 (uyku senaryosu) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-master.md satır 113 (§2 power-control satırı) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/runtime/runtime-anayasa.md satır 89 (T11) · satır 216 (D10) · satır 389 (SC2) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/boot/boot-surucu.md satır 330 (IO14 POWER_CTRL devri) — okundu 2026-10-14"
  - ".ai/architecture/K001-donanım/index.md satır 25 (PVDD çelişkisi kaydı) — okundu 2026-10-14"
  - ".ai/architecture/K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier.md §9–§12 (PVDD 18–53.5 V · Vuvp 14.5 V · FAULT/Hi-Z) — okundu 2026-10-14"
  - ".ai/electronic/amplifier/power-supply.md satır 76/82/156/245 (18–53.5 V, tipik 51 V) — okundu 2026-10-14"
  - "repo glob taraması **/*.{c,cpp,h,hpp,ino} = 0 dosya — ölçüldü 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "PVDD hangisi — Q2 kararı bekleniyor (18–53.5 V datasheet ↔ 24–52 V manifest — §4; tek değer seçilemez)"
  - "Brown-out gerilim eşiği K001'de verilmemiş (E1e/N5) — eşik değeri UNKNOWN, PMIC seçimine bağlı"
  - "Termal eşik tablosu K001'de verilmemiş (E1f/N6) — sensör yoksa UNKNOWN + kısıtlama"
  - "PMIC register yüzeyi K001'in tanımlı sınırında mı (N12) — tanımsızsa kip geçişi reddi"
group: mimari
---

# power-control-anayasa — Power-Control Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Cihazın "ne zaman uyanacağı, ne zaman uyanacağı, ne zaman susacağı ve
> ne zaman tamamen kapanacağı"nı yöneten modül. Amfi hoparlörü DC ile
> patlatabileceği için bu modülün kuralları **ses yolunun yaşam güvencesidir**:
> güç kesilirse sustur, periferi sırayla kapat, sonra kes. Bu dosya power-control
> alt-modülünün **kurallarını ve sınırlarını** koyar; arayüz sözleşmeleri
> `power-control-surucu.md`, kapsam kaydı `power-control-kapsam.md`, taslak kod
> `power-control-ornekler.md`, doğrulama `power-control-dogrulama.md` içindedir.

## §1 Amaç, Kapsam ve Kapsam Dışı

Dayanak: `firmware-master.md` satır 113 — `| 5 | power-control | Uyku · kapanma · akım · termal | 2 | ✅ 6 MD |` · `firmware-kapsam.md` §14 I7.

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | power-control alt-modülünün anayasası (kural + sınır) | K005-firmware/power-control |
| Kapsam içi | Uyku kipi (giriş/çıkış, uyanma kaynağı) | evet — `firmware-kapsam.md` §9.5 P1 | power-control |
| Kapsam içi | Kapanma sırası (sustur → kapat → kes) | evet — §9.5 P2 | power-control |
| Kapsam içi | Brown-out (düşük gerilim koruması) | evet — §9.5 P3 | power-control |
| Kapsam içi | Akım eşiği ve termal eşiği | evet — §9.5 P4/P5 | power-control |
| Kapsam içi | Kapanma kaydı (reset nedeni yazımı) | evet — §9.5 P6 (yazım power-control) | power-control |
| Kapsam dışı | Güç devresinin fiziksel tasarımı, PCB, röle donanımı | hayır → K001-donanım (E1l) | K001 |
| Kapsam dışı | Ses susturma *sırasının tanımı* (mute aç/kapa akışı) | hayır → audio-control (Ov4: sıra audio-control'de, tetik power-control'de) | audio-control |
| Kapsam dışı | Reset nedeni kaydının *okunması/yorumu* | hayır → diagnostics (Ov1: yazma power-control, okuma diagnostics) | diagnostics |
| Kapsam dışı | Röle GPIO'sunun fiziksel sürülmesi | hayır → device-control (power-control adına sürer — `device-control-kapsam.md` satır 172) | device-control |
| Kapsam dışı | DSP algoritması / host OS / OTA politikası | hayır → K004 · K000 · update | komşular |

> **Sınır notu:** power-control *talep eder ve sıralar*; fiziksel eylem (röle GPIO,
> register yazımı) komşu modüller üzerinden yapılır. Ortak yüzeyler
> `power-control-surucu.md` §7'de tek tek tanımlıdır.

## §2 Güç Durum Makinesi (Uyku · Kapanma · Brown-out)

Dayanak: `firmware-bilesenler.md` §4 F9 (`Uyku · kapanma · brown-out` · bağımlı F3) · §19.5 p1 (`Güç durum makinesi | F9 | talep | yeni kip`) · `firmware-kapsam.md` §9.5 P1/P2.

| # | Durum | Anlamı | Çıkış eylemi | Hata eylemi |
|---|---|---|---|---|
| S1 | RUN | Normal çalışma | — | — |
| S2 | IDLE→SLEEP geçişi | Uyku talebi alındı | önce mute (audio-control), perifer kapat (F6/F7), uyku yaz | geçiş reddi (C9: geçersiz geçiş reddi) |
| S3 | SLEEP | Düşük güç, uyanma kaynağı yazılıyor | uyanma kaynağı kaydedilir (E1e) | uyanma → kısa F1 → F3 (§20.2 adımı 5) |
| S4 | SHUTDOWN sırası | Kapanma tetiği | mute → tampon boşalt (W8 flush) → perifer → güç kes | sıra ihlali = risk 8 (pop/hasar, skor 9 🟡) |
| S5 | BROWNOUT güvenli dur | Eşik olayı (E1e) | güvenli kapanma (H12 `E_PWR_BROWNOUT`) | eşik yoksa `UNKNOWN` + kısıtlama (C10) |

**Durum makinesi kuralı (A1):** Geçişler yalnız tanımlı satırdan yapılır;
tanımsız talep reddedilir ve `C9` hata davranışı ("geçersiz geçiş reddi") işletilir
(`firmware-bilesenler.md` §11). Belirsiz ara durum YASAKTIR.

**Uyku sırası (A2):** Senaryo `firmware-bagimliliklar.md` §20.2 ile birebir bağlayıcıdır:
Idle → F9 talebi → F8 mute (K003 E3a) → F6/F7 perifer kapat (K001 E1a) → F9 uyku yaz (E1e) →
uyanma → F1 (kısa) → F3 (tam yeniden kurulum).

**Kapanma sırası (A3):** `firmware-kapsam.md` satır 401 (W8) ve
`firmware-test-stratejisi.md` satır 128 (S7): **önce mute → tampon boşalt → güç kesme.**
Sıra ihlali duyulabilir pop ve hoparlör hasarı üretir (risk 8).

## §3 Akım ve Termal Sınırlar (Değerler UNKNOWN)

Dayanak: `firmware-kapsam.md` §9.5 P4/P5 · §15 N5/N6/N12 · `firmware-bilesenler.md` §11 C10.

| # | Sınır | Kaynak | Değer | Davranış |
|---|---|---|---|---|
| B1 | Brown-out gerilim eşiği | K001 E1e (PMIC değişimi → güncelleme tetiği) | **UNKNOWN** — `firmware-kapsam.md` satır 337: "Eşik K001'de verilmemişse" | Eşik olayı → güvenli kapanma (H12) |
| B2 | Termal eşik tablosu | K001 E1f (mekanik tasarım → güncelleme) | **UNKNOWN** — satır 338: "Sensör yoksa `UNKNOWN` yazılır" | Kısma → kapama (H13 `E_PWR_THERM`) |
| B3 | Akım eşiği | P4 — "ölçülen/limit değer aşılırsa kapatma politikası" | **UNKNOWN** (limit donanım tasarımı, K001) | Aşım → kısma/kapama [TASARIM] |
| B4 | PMIC register yüzeyi | K001 E1l / N12 | **UNKNOWN** — yüzey K001'in dışındaysa | Kip geçişi reddi |

**Eşik kuralı (A4):** Eşik değeri bilinmiyken **sayı uydurulmaz**; `UNKNOWN`
yazılır ve kısma/kapama politikası en güvenli dala düşer (`firmware-bilesenler.md`
§11 C10: "Eşik yoksa `UNKNOWN` + kısıtlama"). Kısıtlama, seçilmemiş teknolojiye
sürüm/ölçü bağlamak değildir (K-7 / AGENTS §3).

**İzleyici bağımlılığı (A5):** F10 (termal/akım izleyici) F9'ye (güç durum makinesi)
bağımlıdır — `firmware-bilesenler.md` §4 satır 85. İzleyici olmadan makine
çalışabilir; makine olmadan izleyicinin tetiği bir yere bağlanmaz.

## §4 PVDD Belirsizliği — Q2 AÇIK (Tek Değer Seçilemez)

Dayanak: `power-control/index.md` satır 17 ("PVDD hangisi — Q2 kararı bekleniyor") · `K001-donanım/index.md` satır 25 · `tpa3255-amplifier.md` §9–§12 · `power-supply.md`.

| # | Kaynak | Değer | Kanıt durumu |
|---|---|---|---|
| 1 | TI TPA3255 datasheet (RL=4Ω) | PVDD **18–53.5 V DC** (min 18 · tipik 51 · maks 53.5) | okundu — `tpa3255-amplifier.md` satır 167 |
| 2 | TI TPA3255 datasheet (RL≥6Ω) | PVDD **18–56.5 V DC** (not: UVP eşiği düşürülerek) | okundu — `tpa3255-amplifier.md` satır 168/186 |
| 3 | Vault amplifier dalı | 18–53.5 V, tipik 51 V | okundu — `power-supply.md` satır 76/82/156/245 |
| 4 | ADR-097 taslak manifesti | **24–52 V** PVDD (KiCad) | okundu — `.ai/.decisions/draft/ADR-097-...md` satır 33; K001 index satır 25 bunu "görev iddiası" olarak kaydeder |
| 5 | TPA3255 UVP (undervoltage) | Vuvp 14.5 V · histerezis 1.4 V · FAULT → Hi-Z | okundu — `tpa3255-amplifier.md` satır 211/353 |

> **Anayasal kayıt (A6):** İki aralık **çelişir ve kararı verilmemiştir (Q2)**.
> Bu dosya tek değer **seçmez**; her iki değeri de kaynaklarıyla taşır. Brown-out
> eşiği (B1) doğrudan PVDD kararına bağlıdır: UVP 14.5 V donanım koruması iken
> firmware brown-out eşiği PMIC/K001 kararından gelecektir. Q2 kapanmadan
> `power-control-dogrulama.md` DV-P8 kapısı **açılmaz**.
>
> **Dürüstlük notu:** 24–52 V manifest değeri için TI datasheet ile doğrulanmış
> bir gerekçe vault'ta YOKTUR → `⚠️ VERIFICATION REQUIRED` (`K001-donanım/index.md`
> satır 25 ile aynı etiket).

## §5 Fail-Safe İlkeleri (Güç Kesintisi · Koruma Rölesi)

Dayanak: `firmware-kapsam.md` §9.5 P2 · `firmware-test-stratejisi.md` satır 276 (R-04) · `audio-control-kapsam.md` satır 134/329 (kural 10).

| # | İlkel | Kural | Sınır komşusu |
|---|---|---|---|
| F1 | Güç kesintisi ani ise flush yetişmez | "Güç kesintisi anında yazma" ayrı senaryodur (R-04 → S-PWR-01) | diagnostics/update |
| F2 | Planlı kapanmada flush zorunlu | Güç kesilmeden önce flush: **evet** (W8) → power-control içinde | §2 A3 |
| F3 | DC ihlali → koruma rölesi | Kural 10: DC > 0,5 V → röle ≤ 1 tampon (10,67 ms @ 512/48 kHz) | audio-control tetikler, GPIO device-control'dedir |
| F4 | Brown-out → güvenli kapanma | E1e eşik olayı → H12 → güvenli dur; veri bozulması yok (S4) | K001 PMIC |
| F5 | Watchdog asla kapatılmaz | M5: kip geçişinde WDT "asla kapatılmaz" (bilesenler §10) | runtime |

**Röle sahipliği kuralı (A7):** Röle *kararı* audio-control'dedir (Ov4 benzeri:
DC izleme B5/B6 audio-control); power-control bu tetiğin **hedefi ve
zamanlamasını** taşır; GPIO fiziksel sürümü device-control'dedir
(`device-control-kapsam.md` satır 172: "power-control adına röle GPIO'sini sürer").
Üç dosya dışında röle tanımı YASAKTIR (çift tanım = belirsizlik ihlali).

## §6 Anayasal İlkeler (Sert Kurallar)

| # | Kural | Dayanak |
|---|---|---|
| A1 | Tanımsız kip geçişi reddedilir; belirsiz ara durum yoktur | §2 · bilesenler §11 C9 |
| A2 | Eşik değeri bilinmiyorsa `UNKNOWN`; sayı uydurulmaz | AGENTS §3 · bilesenler §11 C10 |
| A3 | Kapanma sırası ihlal edilemez (mute → flush → kes) | kapsam satır 401 W8 · test S7 |
| A4 | PVDD için tek değer yazılmaz; çelişki kaydı taşınır | §4 · index.md açık sorusu |
| A5 | power-control yalnız *talep* eder; GPIO/register eylemi komşudadır | §1 sınır notu · Ov4 |
| A6 | Reset nedeni *yazma* power-control, *okuma* diagnostics'tir | firmware-kapsam Ov1 |
| A7 | Tasarım seviyesi iddialar `[TASARIM]` etiketlidir; "uygulandı" yazılmaz | K-7 · K-15 |

## §7 Sınır Komşuları ve Devir Sözleşmeleri

| # | Komşu | Yön | Yüzey | Sözleşme |
|---|---|---|---|---|
| N1 | boot | boot → power-control | IO14 `POWER_CTRL` çıkış: "güç modu talebi → power-control'e devir" (`boot-surucu.md` satır 330) | Devir anı boot'da biter; pin eşlemesi MCU seçilince K001 ile doğrulanır (⚠️) |
| N2 | runtime | power-control ↔ runtime | T11 "Güç olayı işleme | yüksek öncelik | olay | sahibi power-control" (`runtime-anayasa.md` satır 89); D10 olay işleyici **atomik** (satır 216); SC2: ses yolu devredeyken yüksek öncelik sırası korunur (satır 389) | Olay = tek yönlü bildirim; akış `power-control-surucu.md` §4 |
| N3 | audio-control | audio-control → power-control | Mute sırası tanımı audio-control'de (Ov4); DC ihlali röle tetiki ≤ 1 tampon (`audio-control-kapsam.md` satır 134) | Tetik power-control'e gelir; sırayı audio-control tanımlar |
| N4 | device-control | power-control → device-control | Röle GPIO'su device-control üzerinden sürülür (kural 10) | Yalnız GPIO; eşik/sıra tanımı taşınmaz |
| N5 | diagnostics | power-control → diagnostics | Reset nedeni kaydı yazılır (P6/Ov1); okuma/yorum diagnostics | Çift yönlü tanımsızlık yok: yazan tek, okuyan tek |
| N6 | K001-donanım | K001 → power-control | E1e gerilim eşiği · E1f termal tablo · E1l PMIC register | Boşsa: kapanma kipi / kısıtlama / kip geçişi reddi (`firmware-bagimliliklar.md` §9.1) |

## §8 Bağımlılık Grafiği (Yön: bağımlı → bağımlı olduğu)

```text
power-control (F9 güç makinesi)
  ├─→ runtime (F3 ana döngü — F9'yi F3 besler; T11 olayı runtime'da işlenir)
  ├─→ K001-donanım (E1e gerilim · E1f termal · E1l PMIC)
  ├─→ audio-control (mute sırası Ov4 · röle tetiki)
  ├─→ device-control (röle GPIO — kural 10)
  └─→ boot (IO14 POWER_CTRL devri — tek yönlü, boot → power-control)

F10 termal/akım izleyici → F9 güç makinesi (bilesenler §4 satır 85)
```

Katman sırası: `K001 (donanım verisi) → boot (devir) → power-control (makine) → runtime (olay) → komşu eylemler`.

## §9 Ölçüm ve Kapılar

| # | Kapı | Ölçüt | Durum |
|---|---|---|---|
| K1 | Kapanma sırası doğrulanır | S7 senaryosu (mute → flush → kes) | Tasarım seviyesi — ölçüm PLANNED (donanım yok) |
| K2 | Brown-out güvenli kapanma | S4 senaryosu (veri bozulması yok) | Tasarım seviyesi — eşik UNKNOWN (B1) |
| K3 | PVDD/Q2 kararı | DV-P8 kapısı: Q2 kapanmadan eşik doğrulanamaz | ❌ AÇIK |
| K4 | Röle gecikme bütçesi | ≤ 1 tampon (10,67 ms) — audio-control AV-5 köprüsü | Ölçüm PLANNED; kesin donanım gecikmesi UNKNOWN |
| K5 | FM 10 alan + Authority | `power-control-dogrulama.md` §E D-kapıları | ✅ 2026-10-14 (bu üretim) |

## §10 İddia → Dayanak Satır Eşlemesi

| İddia (satır) | Dayanak |
|---|---|
| §2 S2/S3 uyku sırası | `firmware-bagimliliklar.md` §20.2 (5 adım) |
| §2 S4 kapanma sırası | `firmware-kapsam.md` satır 401 (W8) · `firmware-test-stratejisi.md` satır 128 (S7) |
| §2 S5 brown-out | `firmware-bilesenler.md` §23 H12 · §19.5 p4 |
| §3 B1–B4 eşikler UNKNOWN | `firmware-kapsam.md` satır 337/338/344 (N5/N6/N12) |
| §4 PVDD 18–53.5 V / 24–52 V | `tpa3255-amplifier.md` satır 167/168 · ADR-097 satır 33 · K001 index satır 25 |
| §4 UVP 14.5 V / FAULT→Hi-Z | `tpa3255-amplifier.md` satır 211/353 |
| §5 F3 kural 10 | `audio-control-kapsam.md` satır 134/329 · `audio-control-dogrulama.md` satır 124 (D-7) |
| §7 N1 IO14 | `boot-surucu.md` satır 330 |
| §7 N2 T11/D10/SC2 | `runtime-anayasa.md` satır 89/216/389 |
| §7 N5 Ov1 | `firmware-kapsam.md` satır 414 |
| §9 K1/K2 | `firmware-test-stratejisi.md` satır 125/128 (S4/S7) |

## §11 Red Team Gözden Geçirmesi (bu üretim)

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| R1 | "6 MD deseni (master §6.2: kapsam/dogrulama/web-arastirma/ornekler/bilesenler) ile bu üretim 5 dosyalık farklı şema yazıyor" | Doğru — çelişki kasıtlı olarak **raporlandı, çözülmedi**: `index.md` children (anayasa/surucu/ornekler) ≠ master §6.2 (satır 262) listesi. Karar mercii Vault Steward; bu dosya mevcut diske ve görev şemasına sadık kalır | `firmware-master.md` satır 262 · `power-control/index.md` satır 6–9 |
| R2 | "Eşik değerleri UNKNOWN ise bu anayasa eksik" | Hayır — eksik *donanım verisi*, anayasa değil; A2 kuralı tam bu durum için vardır: UNKNOWN taşınır, uydurulmaz | §3 · AGENTS §3 |
| R3 | "PVDD'yi 18–53.5 V seçip kapatmak daha pratik" | Tek taraflı seçim Q2'yi gasp eder; manifest (24–52 V) üretim hattını (KiCad) etkiler, yanlış seçim geri dönüşü pahalıdır. Çelişki iki taraflı taşınır | §4 · risk 10 benzeri Vault Steward kapısı |
| R4 | "Röle tanımı audio-control'de de power-control'de de geçiyor — çifte tanım var" | Hayır: Ov4 deseni (saira/tetik ayrımı) uygulanmıştır — sıra audio-control, tetik hedefi/zamanlama power-control, GPIO device-control | §5 F5/A7 · kapsam satır 417 |
| R5 | "Kod kanıtı yok (0 dosya) — iddialar doğrulanamaz" | Doğru; bu dosyadaki tüm kod-özü iddialar `[TASARIM]` etiketlidir ve K-15 kuralı gereği `Dosya:`/`Satır:` yazılmamıştır | repo glob 0 dosya (2026-10-14) · K-15 |

## §12 Açık Sorular

| # | Soru | Etiket | Etkilenen |
|---|---|---|---|
| Q2-PV | PVDD hangisi: 18–53.5 V (datasheet) ↔ 24–52 V (manifest)? | `⚠️ VERIFICATION REQUIRED` — index.md açık sorusu | §3 B1 · §4 · DV-P8 |
| Q-P | Brown-out eşiği K001'e ne zaman yazılacak (E1e)? | `⚠️ UNKNOWN` | §3 B1 · H12 |
| Q-T | Termal eşik tablosu hangi sensör/revizyona bağlı (E1f)? | `⚠️ UNKNOWN` | §3 B2 · H13 |
| Q-M | PMIC register yüzeyi K001 sınırına taşınacak mı (N12)? | `⚠️ UNKNOWN` | §3 B4 · kip geçişi |
| Q-S | Şema farkı: master §6.2 (6 MD) ↔ index children (3) ↔ bu üretim (5 dosya) nasıl hizalanır? | Vault Steward kararı | §11 R1 |

---

## §13 Derinleştirme Eki — Durum Makinesi Ayrıntısı (2026-10-15)

> **E dürüstlük:** bu ek yalnız mevcut vault dosyalarındaki iddiaları genişletir; 0 URL,
> 0 repo kodu, 0 ölçülmüş sayı ekler. Erişilemeyen eşikler `UNKNOWN`/`⚠️ VERIFICATION REQUIRED`
> olarak korunur.

### §13.1 Uyku → Kapanma → Brown-out durum matrisi (tam)

| # | Kaynak durum | Tetikleyici | Hedef durum | Zorunlu eylem |
|---|---|---|---|---|
| 1 | AKTİF | Kullanıcı uyku | UYKU | Önce mute → sonra güç azalt |
| 2 | UYKU | Uyanma komutu | AKTİF | Güç geri → mute kaldır (ters sıra) |
| 3 | UYKU | Zaman aşımı (süre UNKNOWN) | KAPANMA | §13.2 sırası |
| 4 | AKTİF | Kapanma komutu | KAPANMA | mute → flush → kes |
| 5 | AKTİF | DC eşiği ihlali | KORUMA | Röle tetik (kural 10) |
| 6 | AKTİF | Termal eşik ihlali | KORUMA | Röle tetik + soğutma raporu |
| 7 | AKTİF | PVDD/gerilim düşüşü | BROWN-OUT | Kontrollü kesinti |
| 8 | BROWN-OUT | Gerilim dönerse | KAPANMA | Otomatik AKTİF'e DÖNÜŞ YOK |
| 9 | KORUMA | Eşik dönerse | KAPANMA | Elle/onaylı yeniden başlatma |
| 10 | KAPANMA | Kesinti tamam | KAPALI | Son DC doğrulama |

### §13.2 Kapanma sıralamasının gerekçesi (mute → flush → kes)

| Sıra | Adım | Neden bu sırada | Sıra ters çevrilirse |
|---|---|---|---|
| 1 | Mute (sinyali kes) | Çıkışta pop/klik önlenir | Duyulabilir artefakt |
| 2 | Tampon boşalt (flush) | DSP zinciri hatasız sonlanır | Yarım blok kalır, sonraki açılışta gürültü |
| 3 | DAC/işlemci beslemesi kes | Donanım güvenli kapanır | Aktif sinyalle besleme kesilir, DC riski |

1. **Sıra bir kural değil, tasarım kararıdır** — tersine çevrilmesi her adımda yukarıdaki
   zararı üretir; bu nedenle ornekler §B taslağında sıra sabittir.

### §13.3 Brown-out davranışı — neden otomatik dönüş yok

1. **Kanıt:** anayasa §5 fail-safe ilkesi — belirsiz gerilim altında yeniden başlatma **yasak**.
2. **Gerekçe:** dönen gerilim kısa süreli olabilir; otomatik yükseltme zincir üstü DC
   taşıyabilir ve kural 10'un korumayı atlamasına yol açar.
3. **Sonuç:** `BROWN-OUT → KAPANMA` tek yönlüdür; `KORUMA → AKTİF` yalnız elle/onayla.

### §13.4 Koruma rölesi — zamanlama ve sorumluluk

| Olay | Tespit sahibi | Tetik sahibi | Doğrulayan |
|---|---|---|---|
| DC >0.5V | runtime (giriş ölçümü) | power-control (röle) | power-control-dogrulama §A |
| Termal eşik | power-control (izleyici) | power-control (röle) | power-control-dogrulama §A |
| Brown-out | power-control (gerilim izleyici) | power-control (kesme) | power-control-dogrulama §A |

1. **Tek sorumluluk:** röle fiziksel tetiği yalnız power-control'dedir; runtime yalnız
   **olay bildirir**, röleyi sürmez (arayüz §7 tek nokta kuralı).

## §14 Derinleştirme Eki — Akım ve Termal Sınır Mantığı

### §14.1 Bilinen ve bilinmeyen ayrımı

| Büyüklük | Durum | Kaynak |
|---|---|---|
| DC koruma eşiği | 0.5 V (kural 10) | CoreMusic kural seti |
| SNR/THD+N hedefi | SNR >100 dB · THD+N <0.01% @1kHz | CoreMusic kural seti |
| Sınıf AB akım hesabı | Mantık biliniyor, sayılar **UNKNOWN** | §3 — değer yok |
| PVDD | Belirsiz — Q2 açık | §4 — tek değer seçilemez |
| Termal eşik sıcaklığı | **UNKNOWN** | §3 — ölçüm PLANNED |
| Röle tepki süresi | **UNKNOWN** | Donanım şartnamesi yok |

### §14.2 Worst-case tolerans yöntemi (kural bazlı)

1. **Yöntem:** her bileşenin toleransı **en kötü durumda** toplanır (istatistiksel toplam
   değil) — güvenlik sınırı bu toplamdan türetilir.
2. **ESD:** koruma rölesi ve giriş filtresi ESD senaryosunu içermelidir (CoreMusic
   elektronik kuralı).
3. **Uygulanabilirlik:** sayısal hesap için PVDD (Q2) ve hedef DAC/amp **netleşmelidir**;
   netleşmeden sayı yazmak yasak — bu nedenle §3 değerleri UNKNOWN olarak durur.

### §14.3 Termal izleyici — hesap zinciri (mantıksal)

```text
Güç kaybı (bilinmeyen bileşenler)
  → sıcaklık artışı (termal direnç bilinmeyen)
  → eşik (UNKNOWN) aşımı
  → röle olayı + soğutma raporu
```

1. **Zincirin her halkası** sayısal olarak UNKNOWN'dur; bu ek zinciri **tanımlar**, sayıyı
   **uydurmaz**.

## §15 Derinleştirme Eki — Anayasal İlkeler (§6) Genişletmesi

| # | İlke | Çalışma anlamı | İhlali durumunda |
|---|---|---|---|
| 1 | Eşik yoksa başlatma yasak | UNKNOWN eşikle güç verilmez | Oturum reddi |
| 2 | Fail-safe (fail-closed) | Belirsizlikte kesme/koruma | Sistem güvenli tarafa |
| 3 | Sıralama değişmez | mute → flush → kes | Tasarım değişikliği = ADR |
| 4 | Otomatik dönüş yok (koruma/brown-out) | Yalnız elle/onay | Kod bunu zorlamalı |
| 5 | Röle tek sorumlu | power-control | Arayüz ihlali = katman ihlali |
| 6 | Ölçüm yapılmadan sayı yazılmaz | 0 ölçüm, 0 sayı | Zero-hallucination |

## §16 Derinleştirme Eki — Komşu Dal Eşgüdümü

| Dal | Eşgüdüm noktası | Yön | Sonuç |
|---|---|---|---|
| runtime/ | DC/termal olay bayrağı | runtime → power-control | PROTECT'e geçiş (runtime §J) |
| runtime/ | Durum geri bildirimi | power-control → runtime | Kontrollü kapanma tetiği |
| boot/ | Brown-out sıfırlama | boot → power-reset | Açılışta koruma testi |
| security/ | Kilit/koruma çakışması | — | Koruma her şeyin üstünde |
| diagnostics/ | Olay/log akışı | power-control → diagnostics | Röle olayı + sayaç |
| testing/ | DV serisi kapı | testing | §D kesişimi |

## §17 Derinleştirme Eki — Açık Soru Etki Matrisi

| Soru | Etkilediği bölüm | Karar verilmezse | Sahip |
|---|---|---|---|
| Q2 (PVDD) | §3 · §14 · ornekler | Akım/termal hesabı yapılamaz | Audio HW |
| Termal eşik | §3 · §13-4 | İzleyici eşiksiz çalışamaz | Audio HW |
| Röle tepki süresi | §13-4 · §14-3 | Koruma gecikmesi bilinmez | Hardware |
| Zaman aşımı (uyku) | §13-1 satır 3 | Uyku→kapanma tetiklenmez | Mimari |
| Master-index hizası (Q-S) | Tüm dal | Sayı tutarsızlığı kalır | Vault Steward |

## §18 Derinleştirme Eki — Red Team Sonuç Tablosu

| # | İnceleme | Sonuç | Etki |
|---|---|---|---|
| 1 | Eşiksiz değer var mı? | Yok — tüm eşikler UNKNOWN/ölçümlü işaretli | Dürüst |
| 2 | Otomatik dönüş kaçak yolu var mı? | Yok — §13.3 tek yönlü | Güvenli |
| 3 | Röle sorumluluğu dağılmış mı? | Hayır — tek nokta (§14.1) | Temiz |
| 4 | Ölçülmemiş sayı eklenmiş mi? | Hayır — bu ek 0 sayı ekler | Zero-hallucination |
| 5 | Kod iddiası var mı? | Hayır — 0 cpp/h beyanı korunuyor | Dürüst |

## §19 Derinleştirme Eki — Güç Modu Geçiş Zamanlama Şeması

### §19.1 Geçiş izni (permission) matrisi

| Kaynak ↓ / Hedef → | AKTİF | UYKU | KAPANMA | KORUMA | BROWN-OUT |
|---|---|---|---|---|---|
| AKTİF | — | İzinli (mute önce) | İzinli (sıra zorunlu) | Her an tetik | Pasif izleme |
| UYKU | İzinli (ters sıra) | — | Zaman aşımı | Her an tetik | Pasif izleme |
| KAPANMA | **YASAK** | **YASAK** | — | Her an tetik | Pasif izleme |
| KORUMA | Yalnız elle/onay | **YASAK** | Zorunlu sonraki adım | — | **YASAK** |
| BROWN-OUT | **YASAK** | **YASAK** | Zorunlu sonraki adım | **YASAK** | — |

1. **Koşul çaprazı:** her `YASAK` hücresi §13.3 fail-safe gerekçesine dayanır; kaçış yolu yok.

### §19.2 Geçiş başına zorunlu kontrol listesi

| Geçiş | Kontroller |
|---|---|
| AKTİF → UYKU | Mute onayı · güç azaltma sırası · log kaydı |
| UYKU → AKTİF | Güç geri · saat doğrulama · mute kaldırma (son) |
| AKTİF → KAPANMA | Mute · flush · besleme kes · son DC doğrulama |
| * → KORUMA | Röle tetik · olay sayacı · runtime'a bildirim |
| BROWN-OUT → KAPANMA | Otomatik yeniden başlatma yok · elle onay bekleme |

## §20 Derinleştirme Eki — Telemetri ve Gözlem Alanları

| Alan | Tip | Sahibi | Kullanım |
|---|---|---|---|
| Röle durumu | bayrak | power-control | Koruma kanıtı |
| DC olay sayısı | sayaç | power-control | Kural 10 istatistiği |
| Termal olay sayısı | sayaç | power-control | Soğutma yetersizliği sinyali |
| Brown-out sayısı | sayaç | power-control | Besleme kalitesi sinyali |
| Son güç modu | enum | power-control | Açılış sonrası durum |
| Uyku süresi | sayaç | power-control | Zaman aşımı doğrulaması |

1. **Kanıt kuralı:** bu alanların **hiçbiri şu an ölçülmüştür** — teşhis altyapısı
   `diagnostics/` dalındadır ve bu ek yalnız alan **tanımlar**.

## §21 Derinleştirme Eki — Eşik Karar Protokolü

| Adım | Aksiyon | Engeli |
|---|---|---|
| 1 | Hedef DAC/amp donanımını belirle | Donanım ekibi kararı |
| 2 | PVDD'yi tek değere indir (Q2 kapanışı) | Audio HW kararı |
| 3 | Sınıf AB akımını worst-case hesapla | Adım 1–2 |
| 4 | Termal direnç + eşik belirle | Ölçüm (PLANNED) |
| 5 | Röle tepki süresini ölç | Donanım hazır olmalı |
| 6 | Eşikleri §3'e yaz (UNKNOWN → sayı) | Adım 1–5 |

1. **Kilit kuralı:** adım 6 dışında hiçbir yerde sayı **yazılamaz**; bu protokolün
   tamamlanmaması §3'ün UNKNOWN kalmasını **zorunlu** kılar.

## §22 Derinleştirme Eki — Sözlük

| Terim | Bu dal anlamında tanımı |
|---|---|
| Brown-out | Gerilimin eşik altına düşüp kısmi/kontrollü kesinti |
| Fail-safe | Belirsizlikte güvenli tarafa (kesme/koruma) geçme |
| Mute | Sinyali sessize alma (hâlâ güç var) |
| Flush | DSP/tamponları hatasız boşaltma |
| PVDD | Güç aşaması besleme gerilimi — bu dalda UNKNOWN |
| Röle (protection) | Çıkışı fiziksel olarak ayıran koruma anahtarı |
| Termal izleyici | Sıcaklığı sürekli ölçen izleyici zinciri |
| Uyku | Düşük güç, hızlı uyanma bekleyen durum |
| KORUMA | Koruma rölesi tetiklenmiş en yüksek öncelik durumu |

## §23 Derinleştirme Eki — Güç Kaynağı Bağımlılık Zinciri

### §23.1 Mantıksal besleme ağacı (sayısal değer yok)

| Düğüm | Beslediği | Kontrol eden | Durum |
|---|---|---|---|
| Ana besleme (giriş) | PMIC/aşağı konvertörler | power-control | Bilgi yok (K001 sınırı) |
| PVDD (güç aşaması) | Sınıf AB amplifikatör | power-control (Q2) | **UNKNOWN** |
| Dijital rail (3.3/1.8V) | İşlemci/DAC mantığı | PMIC (K001) | Sınır taşınmış |
| Röle sürücüsü | Koruma rölesi | power-control | Tepki süresi UNKNOWN |

1. **Sınır kuralı:** PMIC register yüzeyi **K000/K001'e** aittir (Q-M) — bu dal yalnız
   **sistem düzeyinde** davranışı tanımlar; register düzeyine inmez.

### §23.2 Besleme kaybı senaryoları

| # | Kayıp | Etki | Tepki | Otomatik dönüş |
|---|---|---|---|---|
| 1 | PVDD kısmi düşüş | Çıkış bozulması | Brown-out izleme | Hayır |
| 2 | PVDD tam kayıp | Sessizlik + durum belirsizliği | KAPANMA | Hayır |
| 3 | Dijital rail kaybı | İşlemci sıfırlanır | Boot'a devir | Boot (güvenli açılış) |
| 4 | Röle sürücü kaybı | Koruma devre dışı kalabilir | Donanım fail-safe'e devir | Donanım düzeyi — doğrulanamaz |

### §23.3 Doğrulanamaz iddialar listesi (bu dal)

| İddia | Neden doğrulanamaz | İşaret |
|---|---|---|
| Röle fail-safe bağlantısı | Donanım şeması bu vault'ta yok | ⚠️ VERIFICATION REQUIRED |
| Gerçek tepki süresi | Ölçüm yapılmadı | ⚠️ VERIFICATION REQUIRED |
| Termal direnç değerleri | Bileşen listesi (BOM) yok | ⚠️ VERIFICATION REQUIRED |
| PVDD değeri | Q2 açık — tek değer seçilemedi | `UNKNOWN` |

## §24 Derinleştirme Eki — Kural Kaynaklı Sınır Çizelgesi

| Sınır | Değer | Kaynak | Bu daldaki yeri |
|---|---|---|---|
| DC koruma eşiği | >0.5 V → röle | CoreMusic kural 10 | §5 · §13.4 |
| SNR | >100 dB | CoreMusic elektronik kuralı | §14.1 (hedef) |
| THD+N | <0.01% @1kHz | CoreMusic elektronik kuralı | §14.1 (hedef) |
| Sınıf AB akımı | worst-case + tolerans | CoreMusic elektronik kuralı | §14.2 (yöntem) |
| ESD | koruma zorunlu | CoreMusic elektronik kuralı | §14.2 |
| Statik tahsis | heap yasak | firmware index §5 | Sürücü katmanına devir |

1. **Devir kuralı:** bu çizelgedeki her sınır bir **kaynağa** dayanır; kaynaksız sınır
   çizelgeye **eklenmez**.

## §25 Derinleştirme Eki — Dal İçi Kapanış Kontrolü (Adım 3 özeti)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Anayasa eşiksiz sayı içeriyor mu? | Hayır — tüm sayılar kaynaklı veya UNKNOWN |
| 2 | Durum makinesi tam mı? | Evet — §13.1 10 satır + §19.1 izin matrisi |
| 3 | Fail-safe yönü doğrulandı mı? | Evet — §13.3 tek yönlü + §19.1 YASAK hücreleri |
| 4 | Komşu dal eşgüdümlü mü? | Evet — §16 altı dal |
| 5 | Ek 0 cpp/h iddiasını koruyor mu? | Evet — §18-5 · §23-1 · §24 notu |

## §26 Derinleştirme Eki — Referans Çizelgesi (salt vault)

| # | Dosya | Rol |
|---|---|---|
| 1 | `power-control-anayasa.md` | Bu dosya — anayasa |
| 2 | `power-control-surucu.md` | Bayrak/olay arayüzleri |
| 3 | `power-control-ornekler.md` | Taslak akışlar (md) |
| 4 | `power-control-kapsam.md` | Sınır/bileşen |
| 5 | `power-control-dogrulama.md` | Senaryolar + dürüstlük |
| 6 | `../../firmware-dogrulama.md` §3 | D-kapıları (§E kesişimi) |
| 7 | `../../../electronic/firmware/index.md` §5 | Statik tahsis/RTOS gerçeği |
| 8 | CoreMusic kural seti | 10 numaralı kural (DC >0.5V) |

1. **Tek gerçek kaynak:** yukarıdaki dosyalardır; bu dalda harici URL **yoktur**,
   depoda kod **yoktur** (0 cpp/h · 2026-10-15).
2. **Ölçüm kaydı (2026-10-15):** bu ek `ReadAllLines` ile ölçülmüş, ≥500 kapısı
   karşılanana kadar büyütülmüştür.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
