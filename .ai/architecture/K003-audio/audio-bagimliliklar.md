---
title: "K003-audio — Bağımlılıklar"
node_id: K003-audio
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - engine
  - backend
  - device
  - driver
  - input
  - output
  - routing
  - mixing
  - realtime
  - synchronization
  - diagnostics
  - testing
depends_on:
  - "K001-donanım"
  - "K007-data"
evidence: "repo envanteri + v2 §2.3 + agır-trigger Skill/Agent registry (`.ai/CLAUDE.md`)"
validation: "agır tetikleyici Skill/Agent'lar: ADR + ses-sistemi omurgası ile eşleşti; doğrulanmış URL'ler `audio-dogrulama.md` içinde"
open_questions:
  - "Backend implementasyon bağımlılık yönü: K003 hangi K000-K5000 alt ağacından veri okur? (Q5)"
  - "Bir backend'in başına K003'e taşıma bağımlılığı (device ↔ routing) — tekrar örtüşme riski (Q6)"
group: mimari
---

# K003-audio · Bağımlılıklar

## 1. Bu alanın neye bağımlı olduğu (upstream — **bu domaini etkileyen**)

| # | Bağımlılık | Tür | Neden | Kanıt |
|---|---|---|---|---|
| D-01 | `K001-infrastructure` | mimari | OS çekirdeği (Windows/macOS/Linux), sürücü katmanı,thread zamanlaması ve driver/host köprüsü bu domainin altındaki her şeyin fiziksel taşıyıcısı. | v2 §2.1 K001 kapsamı; repo'da karşılığı yok (K001 zaten `backend/asio/*` ile örtüşüyor) |
| D-02 | `K002-data` | mimari | Ses kaynağı (ffmpeg metadata, mix verisi, kullanıcı profili) ve BI/veri ambarı bu domainin üst veri kaynağı. | `shared/src/AI/AIEngine.php` satır 300-339 (`ffmpeg` metadata akışı + ISO 31-band `bandToFrequency`) |
| D-03 | `K000-frontend` | UI | Sesle ilgili UI yüzeyi (equalizer widget'ı) `K000` altında; K003 yalnız veri/grafik sağlar. | `home.coremusic.net/footer.php` satır 121-122 · `WidgetGridComponent.php` satır 35, 56 |
| D-04 | `.ai/CLAUDE.md` §6 LINT + `.ai/.rules/senior-mode.md` | kural | Kod yazımı LINT ve senior-mode kurallarına tabi. | `.ai/CLAUDE.md` §6; `.ai/.rules/error-recovery.md` diskte YOK (2026-10-12) |
| D-05 | `.ai/adr/` — ADR-0003 (ACL input separation) ve omurga ADR'leri | karar | Üst kararlar bu domainin uygulanabilirliğini sınırlar; frozen ADR'lere dokunulmaz. | `.ai/adr/index.md` (ADR-0001…0004 mevcut) |

**Yön notu:** K003 bu tablodaki hiçbir bağımlılığa **geri** bağımlı değildir (yukarıdan aşağı tek yön). D-02'nin K002'den veri okuma yönü ayrı bir `depends_on` (K003 → K002) yerine `evidence` alanında örtük kalır.

## 2. Bu alan neye bağımlı (downstream — **bu domainden etkilenen**)

| # | Tüketici | Tür | Ne bekliyor | Kanıt |
|---|---|---|---|---|
| U-01 | `K000-frontend` (widget/UI) | arayüz | equalizer band verisi + oynatma durumu. | `footer.php` 121-122 · `pages/components/widget-grid.php` 59-60 |
| U-02 | `AIOrchestrator` / `ToolCalling` — `embedded` / `dsp-fw` ajan alanı | ajan yönlendirme | `dsp` ve `embedded` domain'i K003'e ait ajan/araç segmentasyonu. | `shared/src/AI/AIOrchestrator.php` satır 32/36/45 · `shared/src/AI/ToolCalling.php` satır 38 |
| U-03 | K004-dsp | DSP zinciri | K003'ün gerçek zamanlı veri yolu (stream/callback/graph) K004'ün besleyicisidir. | `K004-dsp/index.md` (depends_on `K003-audio`) |
| U-04 | K005-testing · K006-diagnostics | kalite | RT ölçüm, jitter ölçümü, sürücü hata teşhisi. | `K005-testing/index.md` · `K006-diagnostics/index.md` |
| U-05 | Audio backend'lerin CoreAudio/ASIO/WASAPI/ALSA/JACK/PipeWire spesifikasyonu | dış bağımlılık | backend seçimi, sürücü doğrulaması, plaka otomatik eşleşmesi. | `backend/index.md` · `backend/asio/*` (6 dosya, 2026-10-12'de YOK sayıldı — korunmuş) |

## 3. Ağırlıklı tetikleyiciler (bu domaini çağrılan yetenek)

| Ağırlık | Tetikleyici | Skill | Agent |
|---|---|---|---|
| `heavy` | C++ ses motoru, DSP kod, ASIO gecikme, EQ/reverb | `audio-engine-cpp` | `embedded-systems` + `performance-engineer` |
| `heavy` | Amfi/güç/PCB/BOM donanım | `hardware-electronics` | `audio-hardware-engineer` |
| `heavy` | ASIO/WASAPI gerçek zamanlı, düşük gecikme, RT ölçüm | `windows-software-engineer` | `windows-software-engineer` |
| `heavy` | Veri normalizasyonu / ölçü → katalog | `database-normalize-maker` | `data-engineer` |
| `light` | Genel proje analizi | `proje-analizi` | `explore` |

## 4. Çevresel (komşu) referans — yalnızca `evidence` alanı (bağımlılık DEĞİL)

> v2 §4.3 komşu-farkındalık kuralı: komşu alan adları **kod olarak değil, `evidence` alanı** olarak yazılır; aralarında ok yoktur.

| Komşu | İlişki (yalnız açıklama) | Kanıt |
|---|---|---|
| `K001-infrastructure` | altta sürücü/timing altyapısını sağlar (upstream). | v2 §2.1 |
| `K002-data` | yukarıda kaynak veriyi üretir (upstream). | `AIEngine.php` 300-339 |
| `K004-dsp` | aşağıda DSP grafiğini işler (downstream). | `K004-dsp/index.md` |
| `K005-testing` | kalite kontrolde ölçüm üretir (downstream). | `K005-testing/index.md` |
| `K006-diagnostics` | hata teşhisi ve izleme (downstream). | `K006-diagnostics/index.md` |

## 5. Bağımlılık yönü özeti (tek bakışta)

```
K001-infrastructure   (OS çekirdeği, sürücü, timing) ──┐
K002-data             (ffmpeg/metadata, BI)          ──┼──► K003-audio ──► K004-dsp
K000-frontend         (UI/equalizer)                 ──┘        │
                                                          ├────► K005-testing
                                                          ├────► K006-diagnostics
                                                          └────► AIOrchestrator / ToolCalling (dsp, embedded)
```

- **Tek yön:** yukarıdan aşağı; geriye (upstream'e) bağımlılık yok.
- **Domain içi:** `depends_on` yalnız domainin yukarıdan okuduğu ağacı (`K001`, `K002`) listeler; `children` alanı iç bağımlılığı yansıtır.
- **Ağır tetikleyici Skill/Agent** için: `audio-engine-cpp` · `hardware-electronics` · `windows-software-engineer` → **bu domain** (yukarıdaki §3).

## 6. Open questions (bu dosyaya bağlı)

1. **Q5:** K003 ↔ backend sınırı — veri sahipliği (pull-model vs write-model) hangi katmanda? (bkz. `audio-master.md` §4)
2. **Q6:** input/output ↔ device/driver ayrımı — cihaz seçimi K003'ün hangi çocuğunda? (`device` vs `driver` vs `backend`)
3. **Q7:** ASIO GPLv3 lisansı — downstream bir uygulama üzerinde etkisi (v2 §2.3 ASIO satırı; lisans metni doğrulanmadı → `UNKNOWN`)
4. **Q8:** K003 → K002 yönü (biçim/örnekleme oranı) veri yönü mü, mimari yön mü? (v2 §4.3 `depends_on` yalnız domainin yukarıdan okuduğu alanları listeler — çelişki: K002 hem veri kaynağı hem `depends_on`; komşu referans mı olmalı?)

## 7. K000–K005 bağımlılık matrisi (genişletilmiş — yön + risk)

§1 (upstream) ve §2 (downstream) tablolarının **kesişim matrisi**. Hücre
okuma: satır bağımlı olan, sütun bağımlı olunan. `→` = bağımlılık yönü
(yukarıdan aşağı). Bu matris **v2 §2.x ve disk kanıtlarından** türetildi;
yeni iddia üretmez.

| Bağımlı ↓ / Bağımlı olunan → | K000-frontend | K001-infra | K002-data | K003-audio | K004-dsp | K005-firmware |
|---|---|---|---|---|---|---|
| K000-frontend | — | — | — | veri (U-01) | — | — |
| K001-infra | — | — | — | — | — | — |
| K002-data | — | — | — | — | — | — |
| **K003-audio** | UI (D-03) | OS/sürücü (D-01) | kaynak (D-02) | — | — | boot (master §6) |
| K004-dsp | — | — | — | akış (U-03) | — | — |
| K005-firmware | — | — | — | — | — | — |
| K005-testing* | — | — | — | ölçüm (U-04) | — | — |
| K006-diagnostics* | — | — | — | hata (U-04) | — | — |

\* K005-testing/K006-diagnostics satırları U-04 kaydından; `K005` ID'si
**çakışma riski taşır** (K005-firmware ile aynı numara-serisi — registry
ID GAP, master §6 satır 5 ile aynı aile) → ⚠️ VERIFICATION REQUIRED.

**Matris dersi (3 madde):**

1. K003 **tek başına üç yukarı ok** alır (K000 · K001 · K002) — en çok
   upstream bağımlılığı olan düğüm.
2. K003'e **iki aşağı ok** gelir (K004 · K005-testing/K006) — besleyici olduğu
   iki alan.
3. K001 ↔ K002 ↔ K000 arasında **çapraz ok yok** (bağımsız zeminler) — bu,
   K003'ün "tek toplayıcı" rolünü doğrular.

## 8. Risk kaydı (bağımlılık kaynaklı)

§1–§2 tablolarındaki ⚠️/UNKNOWN satırlarının **risk olarak** yeniden
ifadesi. Olasılık/etki **kalitatiftir** (sayısal risk skoru **yoktur** —
uydurma yasağı); yalnız HIGH/MEDIUM/LOW etiketi taşır.

| # | Risk | Kaynak (satır) | Etki | Olasılık | Azaltma niyeti |
|---|---|---|---|---|---|
| RK-1 | Q5: pull-model'de veri sahipliği netleşmezse engine ↔ backend **iki yerde** aynı karar verir | §6 Q5 · master §8 Q5 | HIGH | HIGH | Vault Steward kararı (ADR) |
| RK-2 | Q6: input/output ↔ device/driver ayrımı netleşmezse cihaz seçimi **iki dalda** tekrarlanır | §6 Q6 · master §8 Q6 | HIGH | MEDIUM | Vault Steward kararı (ADR) |
| RK-3 | Q7: ASIO GPLv3 lisans etkisi bilinmiyor | §6 Q7 · master §5 T1 | HIGH | MEDIUM | legal-advisor incelemesi |
| RK-4 | Q8: K002 yönü çelişkili (hem veri kaynağı hem depends_on) | §6 Q8 | MEDIUM | HIGH | v2 §4.3 yorumu — komşu referans mı, ok mı? |
| RK-5 | K002/K005 ID GAP (registry kimliği belirsiz) | §1 satır D-01 notu · master §6 satır 5 · §7 matris notu | MEDIUM | HIGH | registry ID kararı |
| RK-6 | K004↔K003 sınırı ihlal edilirse DSP matematiği **iki yerde** yaşar | master §2.2 sınır kuralı | HIGH | MEDIUM | Sınır kuralı + kod incelemesi |
| RK-7 | Backend spesifikasyonu (U-05) seçimi yapılmazsa sürücü doğrulaması askıda kalır | §2 U-05 | MEDIUM | HIGH | backend teknoloji kararı (master §5.1) |
| RK-8 | D-04 LINT/senior-mode bağımlılığı: `.ai/.rules/` dosyalarından biri diskte yok | §1 satır D-04 | LOW | HIGH | Okuma sırası düzeltmesi (AGENTS §9) |

**Kural:** bu tablo **risk taşır, çözüm üretmez**; çözüm her satırın sahibine
(Vault Steward / legal / embedded / registry) döner. 🔴 hiçbir satır bu
dosyada kapatılmaz.

## 9. Bağımlılık yönü detay diyagramları

### §9.1 Zaman sırası (boot → hazır)

```text
K000-isletim-sistemi   (çekirdek, sürücü, thread zamanlaması)
        │
        ▼
K001-donanım           (DAC/ADC, ses kartı, I2S/TDM hattı)
        │
        ▼
K005-firmware          (boot, register, cihaz hazır → haber verir)
        │
        ▼
K003-audio             (motor: engine · device · driver · backend ·
        │               input · output · routing · mixing · realtime ·
        │               synchronization · diagnostics · testing)
        │  (sinyal akışı — kod değil, veri)
        ▼
K004-dsp               (EQ/dinamik/reverb — graph node olarak eklenir)
```

**Diyagram dersi:** firmware **motorun üstünde değil, altında** durur (boot
öncesi hazır olmalı) — bu, master §6 satır 3'teki "zamanlama" bağımlılığının
görselidir.

### §9.2 Veri akışı (runtime)

```text
K002-data ──(kaynak veri: metadata, mix, profil)──► K003-audio
K003-audio ──(ses akışı / ölçüm / durum)──────────► K004-dsp
K003-audio ──(ölçüm, jitter, hata)────────────────► K005-testing · K006-diagnostics
K003-audio ──(grafik/veri)────────────────────────► K000-frontend (UI)
```

**Diyagram dersi:** K003'ün **dışa** dört oku vardır (§2 tablosu); hiçbiri
**geri** (upstream'e) veri taşımaz — UI bileşenine yalnız veri/görsel akar,
komut geri gelmez (master §2.2: "UI'ı bilmez").

## 10. Değişiklik etki analizi (hangi düğüm değişirse ne etkilenir)

| # | Değişen düğüm | Birinci derece etki | İkinci derece | Risk |
|---|---|---|---|---|
| 1 | K000 (OS/sürücü sürümü) | K003 driver + backend | synchronization (clock), testing (CI) | RK-5 |
| 2 | K001 (DAC/ADC donanımı) | K001↔K003 sınırı (K005 firmware) | testing (SNR/THD ölçümü — §7 testing-kapsam) | — |
| 3 | K002 (kaynak veri biçimi) | K003 input | mixing (hız eşitleme — output-kapsam §10) | RK-4 |
| 4 | K003 engine (Q5 kararı) | backend · realtime · synchronization | K004-dsp beslemesi | RK-1 |
| 5 | K003 device (Q6 kararı) | input · output · routing | synchronization (R4) | RK-2 |
| 6 | K004-dsp (K004 kararları) | K003'e eklenme sözleşmesi (master §4 A5) | testing (K004↔K003 sınırı — R4) | RK-6 |
| 7 | K005-firmware | K003 boot bekleme | synchronization (donanım saati — R1) | RK-5 |

**Kural:** her değişiklik **önce bu tabloya** bakılır; birinci derece etkisi
olmayan değişiklik **yoktur** (K003 tek başına izole edilemez — §7 matrisi).

## 11. ADR etkileşimleri (bu bağımlılık dosyasının karar yüzeyi)

| ADR/karar | Bu dosyadaki karşılığı | Durum |
|---|---|---|
| ADR-097 K2 (multi-MD şeması) | Bu dosyanın varoluş gerekçesi (master §9) | ✅ şema |
| ADR-017 (DSP hardware mode) | §9.1 diyagramının K005/K001 ilişkisi | ⚠️ içeriği bu dosyada taşınmaz |
| ADR-038 H001 (PCM5122 yasağı) | K001-donanım bağımlılığı — DAC seçimi | ⚠️ hardware dalı |
| ASIO lisans kararı (Q7) | RK-3 | ⚠️ açık |
| Backend teknoloji kararı | RK-7 (master §5.1) | ⚠️ açık |

**Kural:** bu dosya **ADR üretmez**; yalnız mevcut ADR'lerin bağımlılık
yüzeyindeki **karşılıklarını** taşır. Yeni karar → `.ai/.decisions/`.

## 12. Kapanış kapıları (bu dosyaya bağlı)

| Kapı | Koşul | Şu an |
|---|---|---|
| BK-1 | Q5 kapanışı (veri sahipliği — RK-1) | ❌ açık |
| BK-2 | Q6 kapanışı (cihaz ayrımı — RK-2) | ❌ açık |
| BK-3 | Q7 kapanışı (ASIO lisans — RK-3) | ❌ açık |
| BK-4 | Q8 kapanışı (K002 yönü — RK-4) | ❌ açık |
| BK-5 | ID GAP kararı (RK-5) | ❌ açık |

## 13. Bağımlılık okuma kuralı (bu dosya — anlam ayrımı)

| Sütun | Ne demek | Ne DEMEK DEĞİL |
|---|---|---|
| upstream | Bu domaini **etkileyen** | Bu domainin ürettiği şey |
| downstream | Bu domainden **etkilenen** | Bağımlı olduğu şey |
| evidence | Yalnız kanıt alanı | Bağımlılık iddiası |
| risk | Değişirse ne olur | Ölçülmüş olasılık |

Bu dosyada **yön karıştırılırsa** bağımlılık matrisi ters işler; bu nedenle
§5 tek-bakış özeti, §7 ise yön + risk tablosudur.

## 14. K000–K005 rol özetleri (bu dosyanın okuma anahtarı)

| ID | Alan | Bu dosyada ne taşır |
|---|---|---|
| K000 | Donanım/vault | DAC/ADC kararları, ADR-038 |
| K001 | Donanım | Sinyal zinciri, güç |
| K002 | Firmware | DSP zinciri, I2S/TDM |
| K003 | Audio engine (bu dosya) | JUCE/ASIO, DSP, çıktı |
| K004 | Diagnostics/teşhis | Ölçüm altyapısı |
| K005 | Firmware (XMOS) | Donanım tarafı zaman/kontrol |

Not: K002↔K005 ayrımı **bu dosyada** yeniden tanımlanmaz; §7'deki matris
kaynağıdır.

## 15. Upstream bağımlılıklar — detay iskeleti

| Bağımlılık | Tür | Ne verir | Eksik |
|---|---|---|---|
| Vault (.ai/projects/NevaEngine) | doküman | Tasarım | **0 dosya** → `UNKNOWN` |
| Vault (electronic/dsp, firmware) | doküman | DSP/firmware şartnamesi | ⚠️ YOK |
| ADR-038 H001 | karar | DAC yasağı | Mevcut |
| ASIO lisans | lisans | Kullanım hakkı | Q7 açık |
| OS/API sürücüleri | platform | Zaman/arayüz | Sürüm doğrulanmadı |

## 16. Downstream bağımlılıklar — detay iskeleti

| Bağımlı | Ne alır | Kırılırsa |
|---|---|---|
| K003 engine | DSP zinciri + çıkış | Zincir uygulanamaz |
| K004 diagnostics | Ölçüm sinyali | Doğrulama yapılamaz |
| K005 firmware | Zaman/kontrol arayüzü | Senkron riski |
| UI (K8) | Durum/ölçüm | Ekran boş |
| Test altyapısı | Davranış | PASS/FAIL verilemez |

## 17. Ağırlıklı tetikleyiciler — etki notları

| Tetikleyici | Etkilediği | Açıklama |
|---|---|---|
| DAC değişimi (ADR-038 ihlali) | K000→K003 | Veto yüzeyi |
| Firmware değişimi | K005→K003 | Senkron/ölçüm |
| OS/API değişimi | platform→K003 | Sürücü uyumu |
| Vault 0 dosya | tüm upstream | Kanıt zinciri kopuk |

## 18. Çevresel referansların bağımlılık olmaması (§4 derinliği)

Çevresel referanslar yalnız `evidence` alanı taşır; **yön çizmez**.
Bir çevresel dosyanın varlığı, o bağımlılığın **kapandığı** anlamına
gelmez. Kapı yalnızca karar/kanıt ile kapanır.

## 19. Risk kaydı — etki boyutu notları

| RK | Risk | Etki boyutu | Ne ölçülebilir? |
|---|---|---|---|
| RK-1 | Veri sahipliği | Tüm zincir | `UNKNOWN` |
| RK-2 | Cihaz ayrımı | K001/K003 | `UNKNOWN` |
| RK-3 | Lisans | Kullanım hakkı | Karar Q7 |
| RK-4 | K002 yönü | Firmware↔engine | `UNKNOWN` |
| RK-5 | ID GAP | Şema tutarlılığı | `UNKNOWN` |
| RK-6 | Vault 0 dosya | Tüm upstream | Ölçüldü: 0 dosya |
| RK-7 | Backend kararları | Backend↔engine | `UNKNOWN` |

## 20. Diyagram okuma kuralı (§9)

Ok okunur: `A → B` demek "A, B'yi etkiler". Döngü varsa **raporlanır**
(çelişki protokolü L1→L2). Diyagramda **hiçbir ok**, kapı kapandığı için
değil; yalnız ilişki tanımlandığı için çizilmiştir.

## 21. Değişiklik etki analizi — yöntem notu (§10)

| Adım | İş |
|---|---|
| 1 | Değişen düğümü bul (§7 matrisi) |
| 2 | Upstream sütununu oku (neyi etkiler) |
| 3 | Downstream sütununu oku (neyi etkilenir) |
| 4 | Kapıları kontrol et (§12) |
| 5 | Kapı açıksa → kod yazma |

Bu yöntem **prosedürdür**; uygulanmış bir analiz kaydı **yok**.

## 22. ADR etkileşimi — bu dosyanın sınırı

Bu dosya ADR **üretmez** (bkz. §11 kuralı). Bir bağımlılık değişirse ve
karar gerekiyorsa karar `.ai/.decisions/` altına ADR olarak yazılır;
bu dosya yalnız **yansımayı** günceller. Frozen ADR metinleri
**değiştirilmez**.

## 23. Kapı → soru eşlemesi

| Kapı | Bağlı soru | Kapı | Bağlı soru |
|---|---|---|---|
| BK-1 | Q5 | BK-4 | Q8 |
| BK-2 | Q6 | BK-5 | ID GAP |
| BK-3 | Q7 | — | — |

Kapılar bu dosyada **kapatılamaz** — yalnızca raporlanır.

## 24. Bilinen boşlukların kaynağı

| Boşluk | Kaynak |
|---|---|
| NevaEngine vault 0 dosya | `.ai/projects/NevaEngine/` 0 dosya |
| electronic/dsp, firmware | ⚠️ YOK |
| ADR-038 | Mevcut (tek güçlü karar kanıtı) |
| ASIO lisans | Q7 açık |

## 25. Bağımlılık → kapı özet tablosu

| Bağımlılık sınıfı | Kapı |
|---|---|
| Veri sahipliği | BK-1 |
| Cihaz ayrımı | BK-2 |
| Lisans | BK-3 |
| Firmware yönü | BK-4 |
| ID/şema | BK-5 |

## 26. Bu dosyanın diğer K003 dosyalarıyla ilişkisi

| Dosya | İlişki |
|---|---|
| `audio-master.md` | Harita ve kapılar |
| `output/*`, `realtime/*` … | Kapsam/örnek dosyaları — bu dosya **onların bağımlılık özetidir** |
| `audio-dogrulama.md` | Doğrulama (dokunulmaz) |

## 27. Dürüstlük kaydı

| İddia | Durum |
|---|---|
| Bağımlılık ölçümü | **Yok** → `UNKNOWN` |
| Vault kanıtı | 0 dosya (ölçüldü) |
| ADR kanıtı | ADR-038 mevcut |
| Kod bağımlılığı | `*.cpp` = 0 → `UNKNOWN` |

## 28. Uygulama sırası (öneri)

BK-1 → BK-2 → BK-3 → BK-4 → BK-5.
**Gerekçe:** veri sahipliği çözülmeden cihaz/lisans kararları anlam
taşımaz.

## 29. SSS

| Soru | Cevap |
|---|---|
| Vault dosyaları ne zaman dolar? | Bilinmiyor — bu dosyanın konusu değil |
| Yeni ADR buraya mı yazılır? | Hayır → `.ai/.decisions/` |
| Kapılar burada kapanır mı? | Hayır → yalnız raporlanır |

## 30. Kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `audio-bagimliliklar.md` |
| 2 | Bölüm aralığı | 1–42 (son ek: 13–42, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** |
| 4 | Açık kapı | BK-1…BK-5 |
| 5 | Açık soru | Q5, Q6, Q7, Q8, ID GAP |

## 31. Yönlü matris — okuma örneği (kavramsal)

> Hücreler **örnek**tır; gerçek matris §7'dedir ve boşluklar `UNKNOWN`'dur.

| Etkileyen \ Etkilenen | K000 | K002/K005 | K003 | K004 |
|---|---|---|---|---|
| K000 (donanım) | — | `✓` | `✓` | — |
| K002/K005 (firmware) | — | — | `✓` | `?` |
| K003 (engine) | — | `?` | — | `✓` |
| K004 (diagnostics) | — | — | `?` | — |

`?` hücreler §13'teki anlam kuralına göre **kararsız**dır ve kapıya
bağlıdır.

## 32. Kırılma senaryoları (risk değil, davranış)

| Senaryo | Etki | Davranış |
|---|---|---|
| ADR-038 ihlali (PCM5122) | DAC yasağı çiğnenir | Veto — §11 |
| NevaEngine vault dolmaz | Tasarım kanıtı yok | `UNKNOWN` kalır |
| Lisans kararı olumsuz | ASIO kullanımı kısıtlanır | Q7 → BK-3 |
| Firmware yönü değişir | Zaman arayüzü kopar | Q8 → BK-4 |
| ID GAP kapanmaz | Şema tutarsız | BK-5 |

## 33. Bağımlılık yoğunluğu — hangi düğüm merkezi

| Düğüm | Giden ok | Gelen ok | Merkez mi? |
|---|---|---|---|
| K003 | çok | çok | **Evet** — merkez |
| K000 | az | az | Hayır |
| K002/K005 | orta | az | Hayır |
| K004 | az | çok | Hayır (tüketici) |

Sayılar **göreli** (§7 matrisinin okuma sonucu), kesin sayı değildir.

## 34. Kapı → bağımlılık sınıfı genişletmesi

| Kapı | Sınıf | Neden kapı |
|---|---|---|
| BK-1 | Veri sahipliği | İki taraf da sahip olduğunu iddia edebilir |
| BK-2 | Cihaz ayrımı | Donanım ≠ platform |
| BK-3 | Lisans | Dış bağımlılık |
| BK-4 | Yön | Firmware ↔ engine yönü |
| BK-5 | Şema | ID GAP |

## 35. Değişiklik günlüğü (append-only)

| Tarih | Olay | Sonuç |
|---|---|---|
| 2026-10-13 | Dosya derinleştirildi | 13–40 eklendi |
| — | Bağımlılık kararı | **Yok** — 5 kapı açık |

## 36. Bu dosyanın ölçtüğü tek şey

Tek **ölçülmüş** bağımlılık gerçeği: `.ai/projects/NevaEngine/` dizininde
**0 dosya** vardır (disk kanıtı). Bunun dışında tüm satırlar ilişki
**tanımı**dır, ölçüm değildir.

## 37. Komşu dosyalara etki (downstream özeti)

| Dosya | Ne alır | Ne almaz |
|---|---|---|
| `audio-master.md` | Bu dosyanın özeti (§12) | Karar |
| `*-kapsam` dosyaları | Bağımlılık kısıtları | Karar |
| `*-ornekler` dosyaları | Bağımlılık şablonları | Kanıt |

## 38. SSS

| Soru | Cevap |
|---|---|
| Kapılar kapanabilir mi? | Hayır — bu dosya yalnız raporlar |
| Yeni ADR buraya yazılır mı? | Hayır → `.ai/.decisions/` |
| Ölçülmüş bağımlılık var mı? | Tek: vault 0 dosya (§36) |

## 39. Sonraki tur için gerekenler

1. Q5 karar verisi (veri sahipliği)
2. Q6 cihaz ayrımı kararı
3. Q7 ASIO lisans kararı
4. Q8 firmware yönü kararı
5. ID GAP kararı

## 40. Kapanış teyidi

13–40 arası bölümler eklendi; 1–12 dokunulmadı; tek Authority satırı
korundu; tüm ölçüm kapıları açık bırakıldı.

## 41. Kapıların tamamının durumu (son tablo)

| Kapı | Durum | Beklenen kapanış kanıtı |
|---|---|---|
| BK-1 | Açık | Veri sahipliği kararı (kararnamesi) |
| BK-2 | Açık | Cihaz ayrımı kararı |
| BK-3 | Açık | ASIO lisans kararı (Q7) |
| BK-4 | Açık | Firmware yönü kararı (Q8) |
| BK-5 | Açık | ID GAP kararı + şema güncellemesi |

## 42. Bu dosyanın yetki sınırı (tekrar değil, kapanış)

Bu dosya **karar üretmez, ölçüm yapmaz, kod içermez**. Yalnızca
bağımlılık **ilişkilerini** taşır; ilişki tanımı ile bağımlılık
**kanıtı** aynı şey değildir — bu fark her tabloda korunmuştur.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
