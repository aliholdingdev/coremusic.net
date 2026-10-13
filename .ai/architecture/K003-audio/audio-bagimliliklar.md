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
  - "K001-infrastructure"
  - "K002-data"
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

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
