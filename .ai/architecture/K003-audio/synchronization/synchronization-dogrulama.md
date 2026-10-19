---
title: "synchronization — Doğrulama"
type: architecture-verification
node_id: synchronization-dogrulama
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on: []
evidence:
  - "§1 iddia tablosu — 8/12 satır doğrulandı (dosya okuma + ölçüm + has-been-fetched)"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): Apple AudioUnitRender (audiotoolbox)"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): PipeWire latency dokümanı (docs.pipewire.org)"
validation:
  - "§1–§3 mevcut yapı korundu (append-only); §2 sayım §1 ile birebir"
  - "§4–§12 ek bölümler: protokol/matris/eşik tasarımı — ölçüm SONUCU içermez"
open_questions:
  - "#9 drift telafi yöntemi (kaydırma mı yeniden zamanlama mı) — UNKNOWN"
  - "#10 drift ölçüm periyodu ve kabul eşiği — UNKNOWN"
  - "#11 ana clock referansı — ⚠️ VERIFICATION REQUIRED"
  - "#12 ağ akışı senkronizasyonu kapsamı — ⚠️ VERIFICATION REQUIRED"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# synchronization — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "synchronization · Clock/epoch uyumu, drift telafisi" | `audio-master.md` §3 satır 71 | 2026-10-12 | ✅ dosya okundu |
| 2 | `AudioUnitRender` render zaman damgasında `inTimeStamp.sampleTime` monoton artar: sonraki = önceki + inNumberFrames | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| 3 | PipeWire latency: port bazlı Latency nesnesi (direction + min/max); linked node latency'leri toplanır, mismatch tespit edilir | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| 4 | PipeWire scheduling: async node 1 quantum latency ekler; driver node döngüyü başlatır | https://pipewire.org/page_scheduling | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | ALSA sw_params `avail_min` / `period_event` — akış olayının zaman penceresi | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 6 | Core Music AU: AUGraph create → addNode → initialize (bellek) → start — senkronizasyon başlangıcı yaşam döngüsüne bağlı | `audio-master.md` §4 A3 (Apple ARoadmaptoCommonTasks) | 2026-10-12 | ✅ dosya okundu |
| 7 | Repo'da clock/drift senkronizasyon kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 8 | Donanım saati (I2S/TDM clock) K005-firmware'nindir (K003 sınırı) | `audio-master.md` §2.2 · §6 | 2026-10-12 | ✅ dosya okundu |
| 9 | Drift telafi yöntemi (kaydırma mı yeniden zamanlama mı) | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 10 | Drift ölçüm periyodu ve kabul eşiği (örnek/ms/ppm) | — | 2026-10-12 | **UNKNOWN** (kriter yok) |
| 11 | Ana clock referansı (donanım saati vs OS sayacı) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | Ağ akışında (stream) senkronizasyon bu düğümün kapsamında mı? | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 12 |
| Benzersiz URL | 4 (2 · 3 · 4 · 5) |
| Gerçek dosya yolu / iç kanıt satırı | 4 (1 · 6 · 7 · 8) |
| ✅ doğrulandı / dosya okundu / ölçüm | 8 (1–8) |
| ⚠️ VERIFICATION REQUIRED | 2 (11 · 12) |
| UNKNOWN | 2 (9 · 10) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#11** ana clock referansı kararı embedded-engineer onayıyla kapanır →
  kapanınca `synchronization-kapsam.md` §3 S3/S4 satırları `[TASARIM]` →
  `[KANIT]` olur.
- **#9–10** telafi yöntemi ve eşik ölçüm/karar olmadan yazılmaz → `UNKNOWN`
  kalır; ppm/örnek sayısı uydurulmaz.
- **#12** kapsam kararı (ağ akışı dâhil mi) üst kararla kapanır.
- **#2–5** bağımsız teyit istenirse URL ancak gerçekten fetch edilince
  `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Doğrulama protokolü — clock-drift ölçüm düzeneği (PLANNED)

> **Durum:** Bu bölüm bir *ölçüm sonucu* değil, **ölçüm protokolüdür** — §1 #9–#11
> açık sorularını kapatmak için tasarlandı. Sonuç sütunları doldurulmadan hiçbir
> ppm / örnek / süre sayısı iddia edilemez (uydurma yasak).

### §4.1 Amaç, kapsam ve sorumluluk

| # | Öğe | Tanım |
|---|---|---|
| A1 | Amaç | İki saat kaynağı (aday ana clock ↔ referans) arasındaki kayma oranını ppm cinsinden ölçmek |
| A2 | Kapsam | K003 senkronizasyon düğümü — I2S/TDM donanım saati K005-firmware'nindir (§1 #8) |
| A3 | Kapsam dışı | Ağ (stream) senkronizasyonu — §1 #12 kapsam kararıyla belirlenecek |
| A4 | Çıktı | Ham zaman serisi + ppm hesabı + PASS/FAIL kaydı (şema: §4.4) |
| A5 | Girdi | 48 kHz · 512 örnek tampon (sartname §8); blok süresi 512/48000 = 10,666… ms (aritmetik) |
| A6 | Tekrar sayısı | Pencere başına tekrar N — §1 #10 kararıdır, şimdilik **UNKNOWN** (uydurulmaz) |
| A7 | Sorumluluk | embedded-engineer (ölçüm) · qa-engineer (kapı değerlendirmesi) |
| A8 | OS katmanı bağımlılığı | Saat/scheduler davranışı K000 altındadır: [[K000-isletim-sistemi/isletim-sistemi-audio/index]] |
| A9 | Kardeş dosyalar | [[synchronization-kapsam]] · [[synchronization-ornekler]] · [[index]] |

### §4.2 Ön koşul tablosu

| # | Ön koşul | Kaynak | Durum | Eksikse |
|---|---|---|---|---|
| P1 | Örnekleme hızı 48 kHz | sartname §8 | ✅ tanımlı | — |
| P2 | Tampon boyutu 512 örnek | sartname §8 | ✅ tanımlı | — |
| P3 | Ses veri yolu 32-bit float | ADR-017 | ✅ tanımlı | — |
| P4 | İkinci referans saat kaynağı seçimi | §1 #11 | ⚠️ VERIFICATION REQUIRED | protokol başlamaz (S1 DUR) |
| P5 | Drift telafi yöntemi (kaydırma / yeniden zamanlama) | §1 #9 | ❌ UNKNOWN | ölçüm sonrası karar kapısı |
| P6 | Ölçüm penceresi ve periyodu | §1 #10 | ❌ UNKNOWN | pencere tanımsız |
| P7 | Donanım saati sahipliği sınırı (K003 ↔ K005) | `audio-master.md` §2.2 · §6 | ✅ dosya okundu | — |
| P8 | Ölçüm arayüzü (DMA sayacı / OS sayacı / `sampleTime`) | — | ⚠️ VERIFICATION REQUIRED | kayıt alınamaz |
| P9 | Ölçüm süresince tek uygulama (ASIO Exclusive Lock) | kural 9 (proje kuralları) | ✅ kural | kilit çakışması → ölçüm geçersiz |
| P10 | Baseline: repo'da senkron kodu yok | §1 #7 (glob 0 isabet) | ✅ ölçüm | — |
| P11 | 32-bit float zinciri boyunca korunur | ADR-017 + ADR-038 (PCM3168A) | ✅ tanımlı | dönüşüm ölçümü bozar |

### §4.3 Ölçüm adımları

| Adım | İşlem | Çıktı | Kayıt | Hata durumunda |
|---|---|---|---|---|
| S1 | Referans ve aday saat kaynağını belirle (#11 kararı) | 2 kaynak tanımı | karar kaydı | DUR — protokol başlamaz |
| S2 | Cihazı 48 kHz / 512 örnek / float'a sabitle | hw_params benzeri kayıt | metin | P2 ihlali → iptal |
| S3 | Exclusive Lock'u al (tek uygulama, kural 9) | kilit kaydı | log satırı | kilit alınamazsa iptal |
| S4 | İki kaynağı eşzamanlı örneklemeye başlat | t0 referansı | ham seri | t0 ayrımı >0 ise not düş |
| S5 | Pencere boyunca blok başına aday `sampleTime` + referans sayacı yaz | ham zaman serisi | CSV satırı | eksik satır → pencere geçersiz |
| S6 | Seriyi doğrula: monotonluk, boşluk, tekrar eden değer | doğrulama raporu | tablo | monotonluk ihlali → FAULT-03 |
| S7 | ppm hesapla (§5.1 formülü) | ppm değeri | hesap satırı | — |
| S8 | Eşikle (§7) ve PASS/FAIL ver (§8) | kapı sonucu | kapı kaydı | FAIL → §10 regresyon kapısı |
| S9 | Ham veriyi sakla ve imzala (yalnızlık ilkesi: ekleme) | arşiv girişi | dosya | — |
| S10 | Sonucu [[synchronization-kapsam]] ve [[synchronization-ornekler]] ile karşılaştır | tutarlılık notu | — | çelişki → DUR + üst karar |
| S11 | OS tarafı davranışını K000 dokümanıyla karşılaştır | çapraz not | — | §12 kaynak denetimine işle |
| S12 | Kapı sonucunu `testing` dalıyla paylaş (test piramidi katmanı) | el ilkesi kaydı | — | bkz [[testing-kapsam]] |

### §4.4 Ham kayıt şeması (yalnız alan tanımı — değer yok)

| Alan | Tip | Birim | Not |
|---|---|---|---|
| `block_index` | uint64 | — | blok sırası (0'dan) |
| `sample_time` | uint64 | örnek | aday kaynak monoton sayacı |
| `ref_counter` | uint64 | *birim?* | referans sayacı birimi §1 #11 kararına bağlı |
| `wall_stamp` | uint64 | — | kaydetme anı (yalnız korelasyon, hesapta kullanılmaz) |
| `xrun_flag` | bool | — | bu blokta kırılma var mı (bkz [[realtime-kapsam]]) |
| `channel_pair` | id | — | test edilen kanal çifti (§6.1) |
| `device_id` | string | — | cihaz kimliği (§1 #7 baseline: kod yok) |
| `run_id` | id | — | deney oturumu kimliği |
| `note` | string | — | serbest not (boş bırakılabilir) |

### §4.5 Deney tekrarı ve istatistik planı (aday — §1 #10 kararı bekleniyor)

| Kavram | Aday yaklaşım | Durum |
|---|---|---|
| Pencere boyu | Sabit blok sayısı cinsinden (blok süresi 10,666… ms'tir) | ⚠️ VERIFICATION REQUIRED (süre uydurulmadı) |
| Tekrar sayısı N | En az 2 bağımsız pencere; N değeri kararı yok | ❌ UNKNOWN |
| Özet istatistik | Medyan + maksimum mutlak sapma (mean tek başına yeterli değil) | tasarım önerisi |
| Aykırı değer | Xrun'lu bloklar (§ realtime) ana örneklemez | tasarım önerisi |
| Karşılaştırma | Aynı pencerede iki koşul (ref vs aday) yan yana | tasarım önerisi |
| Rapor | Pencere başına tek satır + kapı sonucu | tasarım önerisi |

## §5 Drift hesabı ve birim doğrulaması

### §5.1 ppm formülü (aritmetik kimlik — veri içermez)

| Sembol | Anlam | Kaynak |
|---|---|---|
| `Ö_t` | t penceresindeki beklenen örnek sayısı (adclock frekansı × süre) | tanımdan |
| `Ö_g` | t penceresindeki gözlenen örnek sayısı (adlayıcı sayacın farkı) | §4.4 `sample_time` farkı |
| `Δ = Ö_t − Ö_g` | örnek sapma | aritmetik |
| `drift_ppm = (Δ / Ö_t) × 10^6` | parçacık/milyon kayma oranı | aritmetik kimlik |
| `süre_sapma_ms = (Δ / f_s) × 1000` | örnekleme hızı f_s = 48000 Hz (sartname) | aritmetik |

### §5.2 Birim ve boyut denetimi (hesabın kendi kendini test etmesi)

| # | Denetim | Koşul (aritmetik) | Sonuç |
|---|---|---|---|
| D1 | Birim: saniye cinsinden blok süresi | 512 / 48000 = 0,010666… s (≈ %10,67 ms) | ✓ hesap |
| D2 | Birim: Δ boyutu örnek | Δ = Ö_t − Ö_g → örnek − örnek = örnek | ✓ boyut uyumu |
| D3 | Birim: ppm boyutu | (örnek/örnek) × 10^6 = boyutsuz × 10^6 = ppm | ✓ boyut uyumu |
| D4 | Ters kontrol | Δ_hesap = drift_ppm × Ö_t / 10^6 → Δ ile eşit | ✓ kimlik |
| D5 | Süre ↔ örnek geçişi | Δ_örnek = Δ_ms × 48000 / 1000 | ✓ kimlik |
| D6 | İşaret anlamı | Δ > 0 → aday yavaş (daha az örnek); Δ < 0 → hızlı | yorum kuralı |
| D7 | Sıfır pay | Ö_t = 0 ise oran tanımsız → pencere geçersiz sayılır | hata kuralı |
| D8 | Yuvarlama | Hesap orta adımda yuvarlanmaz; yalnız raporda gösterim yuvarlanır | raporlama kuralı |

## §6 Test senaryo matrisi — senkron (SYNC)

> Her satır bir **çalıştırılabilir senaryodur**; “Beklenen” sütunu *tasarım beklentisidir*,
> gerçek sonuç ölçülünce doldurulur. Ölçüm sütunu bilinçli olarak **boş** bırakılmıştır.

### §6.1 Senaryo matrisi (SYNC-01 … SYNC-16)

| ID | Senaryo | Bağımsız değişken | Bağımlı metrik | Beklenen (tasarım) | Ölçüm | Kapı |
|---|---|---|---|---|---|---|
| SYNC-01 | Referans sabitliği | — | `drift_ppm` | Referans kaynakta kayma ≈ 0 (tanım gereği) | ⬜ | §7 E1 |
| SYNC-02 | Aday clock kısa pencere | pencere uzunluğu | `drift_ppm` | Eşik içinde (§7 aday) | ⬜ | §7 E2 |
| SYNC-03 | Aday clock uzun pencere | pencere uzunluğu | `drift_ppm` | Kısa pencereyle aynı işaret/yön | ⬜ | §7 E2 |
| SYNC-04 | Sıcaklık koşulu (donanım değişkeni) | koşul kimliği | `drift_ppm` sapması | Koşullar arası fark ≤ aday eşik | ⬜ | §7 E3 |
| SYNC-05 | Cihaz yeniden bağlama | tak-çıkar | yeniden kilitlenme süresi | Yeniden kilitlenince seri kesintisiz devam eder | ⬜ | §8 K4 |
| SYNC-06 | Örnekleme hızı değişimi (48 kHz dışı) | f_s | reddedilme/kabul | Tanım dışı hız reddedilir (sartname 48 kHz) | ⬜ | §8 K3 |
| SYNC-07 | Tampon boyutu değişimi | blok boyu | `sample_time` artışı | Artış = blok boyu kadar (blok başına) | ⬜ | §8 K2 |
| SYNC-08 | İki kaynak arasında手工 yeniden senkron | senkron komutu | yeniden senkron sonrası Δ | Komuttan sonra Δ tekrar tanımlı eşik içinde | ⬜ | §7 E2 |
| SYNC-09 | Uzun kesintisiz çalışma (dayanıklılık) | süre | birikimli Δ (kümülatif) | Kümülatif sapma doğrusal/öngörülü modelle uyumlu | ⬜ | §10 R1 |
| SYNC-10 | Xrun sonrası senkron | kırılma enjeksiyonu | kırılma sonrası ilk blok | İlk blokta monotonluk korunur | ⬜ | §9 F5 |
| SYNC-11 | Cihaz kaybı sonrası senkron | cihaz çıkar | yeniden başlatma | Yeniden başlangıçta sayaç taşması/zıplaması yok | ⬜ | §9 F2 |
| SYNC-12 | Eşzamanlı çoklu kanal | kanal çifti | kanallar arası ofset | Kanallar arası örnek ofseti = 0 (§6.2) | ⬜ | §6.2 |
| SYNC-13 | Exclusive Lock çakışması | 2. uygulama | kilit hatası | İkinci uygulama reddedilir; ölçüm tek kalır | ⬜ | §8 K6 |
| SYNC-14 | OS saat sıçraması etkisi | saat olayı | `ref_counter` bozulması | Aday `sample_time` etkilenmez (monoton) | ⬜ | §9 F3 |
| SYNC-15 | Kısıtlı CPU koşulu | yük enjeksiyonu | xrun sayısı | Bütçe içinde kalır (bkz [[realtime-kapsam]]) | ⬜ | §9 F6 |
| SYNC-16 | Yeniden başlatma sonrası tekrarlanabilirlik | koşul | iki koşul ppm farkı | Aynı koşul iki çalıştırma arasında tekrarlanır | ⬜ | §10 R2 |

**Sütun anlamı:** ⬜ = henüz ölçülmedi. Hiçbir satırda ölçüm sonucu yazılmamıştır.

### §6.2 Sample-accurate senkron test matrisi (SAMP-01 … SAMP-10)

| ID | Test | Yöntem | Beklenen (tasarım) | Ölçüm | Kapı |
|---|---|---|---|---|---|
| SAMP-01 | Kanallar arası faz | Aynı sinyal iki kanala; örnek örnek karşılaştırma | Ofset = 0 örnek | ⬜ | K-1 |
| SAMP-02 | Blok kenarı hizası | Blok sınırında örnek indeksi yazımı | Blok sınırı ile yazım aynı örnekte | ⬜ | K-2 |
| SAMP-03 | Monotonluk | `sample_time` ardışık farkı | Fark > 0 ve = blok boyu | ⬜ | §8 K2 |
| SAMP-04 | Sayaç taşması | Uzun çalışmada sayaç sınırı | Taşma tanımlı ve kesintisiz işlenir (*taşma davranışı tasarımı UNKNOWN*) | ⬜ | ⚠️ |
| SAMP-05 | Cihaz↔uygulama ofseti | Cihaz sayacı vs uygulama sayacı | Ofset sabit (drift yoksa ofset sabit kalır) | ⬜ | §7 E4 |
| SAMP-06 | Mekik (round-trip) gecikmesi | Çıkış → giriş döngü | Sabit gecikme, jitter ≤ bütçe (bkz [[realtime-kapsam]]) | ⬜ | §7 E5 |
| SAMP-07 | Çift cihaz senkronu | İki cihaz aynı anda | İki cihaz arası kayma ≤ aday eşik (*üst sınır UNKNOWN*) | ⬜ | ⚠️ |
| SAMP-08 | Format dönüşümü etkisi | Kaynak format ≠ 32-bit float | Dönüşüm ofsete yol açmaz | ⬜ | ADR-017 |
| SAMP-09 | Parametre değişimi ofseti | Parametre güncellemesi sırasında | Ofset değişmez (block kenarı kuralı, bkz [[routing-kapsam]]) | ⬜ | §10 R3 |
| SAMP-10 | Kademeli başlangıç | İlk N blok | İlk bloktan itibaren monoton (sıfır atlaması) | ⬜ | §8 K1 |

## §7 Eşik tabloları (aday — onay bekliyor)

> **Kural:** Aday eşikler *tasarım teklifi*dir; §1 #10 kriter kararı yazılmadan
> “kabul edildi” denmez. Hiçbir sayı bir ölçüm sonucu olarak sunulmaz.

### §7.1 Aday eşikler

| Eşik | Aday değer | Dayanak | Durum |
|---|---|---|---|
| E1 | Referans kayma = 0 | tanım gereği (referans) | ✅ tanım |
| E2 | `drift_ppm` kabul üst sınır | — | ❌ UNKNOWN (#10 — uydurulmaz) |
| E3 | Koşullar arası `drift_ppm` farkı üst sınır | — | ❌ UNKNOWN (#10) |
| E4 | Cihaz↔uygulama ofset kararlılığı | — | ❌ UNKNOWN |
| E5 | Mekik jitter üst sınırı | realtime latency bütçesine bağlı | ⚠️ VERIFICATION REQUIRED (bkz [[realtime-kapsam]]) |
| E6 | Monotonluk ihlali toleransı | Apple dokümanı: `sampleTime` monoton artar (§1 #2) | **0 tolerans** (ihlal = hata) |
| E7 | Blok başına sayaç artışı | = blok boyu (§1 #2: sonraki = önceki + `inNumberFrames`) | **0 sapma** |
| E8 | Blok süresi hesabı | 512 / 48000 = 10,666… ms | ✅ aritmetik (sartname) |
| E9 | Kümülatif sapma büyüme modeli | doğrusal/öngörülü model önerisi | ⚠️ VERIFICATION REQUIRED (model kararı yok) |
| E10 | Yeniden senkron sonrası toparlanma süresi | — | ❌ UNKNOWN |
| E11 | Xrun sonrası ilk blok davranışı | monotonluk korunur | ⚠️ VERIFICATION REQUIRED |
| E12 | Uzun çalışma (dayanıklılık) penceresi | — | ❌ UNKNOWN |

### §7.2 Eşik dayanağı sınıfları

| Sınıf | Açıklama | Eşikler |
|---|---|---|
| ✅ Tanım/aritmetik | Kaynağı tanımdan gelen, ölçülemez-çünkü-tanım değerler | E1, E8 |
| ✅ Kaynaklı | Vault içindeki doğrulanmış iddiadan türeyen | E6, E7 (§1 #2) |
| ⚠️ Bağımlı | Başka düğümün (realtime/routing) kararı gereken | E5, E9, E11 |
| ❌ UNKNOWN | Karar/ölçüm yok; sayı uydurmak yasak | E2, E3, E4, E10, E12 |

## §8 Geçme / kalma kriterleri (PASS–FAIL matrisi)

| # | Kriter | Ölçüm | PASS | FAIL aksiyonu |
|---|---|---|---|---|
| K1 | İlk bloktan itibaren monotonluk | `sample_time` ardışık farkı | Fark > 0, her blokta | Senkron düzeneği reddedilir; §9 F3'e git |
| K2 | Blok başına artış | fark − blok boyu | = 0 | §6 SYNC-07 tekrar; FAIL → regresyon |
| K3 | Örnekleme hızı uyumu | f_s | 48 kHz (sartname) | Cihaz yapılandırması reddedilir |
| K4 | Yeniden bağlama sonrası süreklilik | cihaz olayı sonrası seri | Yeni seri tanımlı şekilde başlar | §9 F2 enjeksiyonu tekrar |
| K5 | ppm kapısı | `drift_ppm` (§5.1) | E2 kararı verilince uygulanır | Karar yoksa kapı **kapalı** (UNKNOWN) |
| K6 | Tek uygulama (Exclusive Lock) | 2. uygulama denemesi | 2. uygulama reddedilir | Ölçüm geçersiz sayılır |
| K7 | Kanallar arası ofset | SAMP-01 | = 0 örnek | Kanal eşleme hatası → routing kapısına git |
| K8 | Kayıt bütünlüğü | §4.4 alan doluluğu | Eksik alan yok | Pencere geçersiz, tekrar |
| K9 | Xrun'suz pencere (bazı koşullar) | `xrun_flag` | Koşula göre (§ realtime bütçesi) | Bütçe kararı yok → ⚠️ VERIFICATION REQUIRED |
| K10 | Tekrarlanabilirlik | İki çalıştırma ppm farkı | Karar yok | ❌ UNKNOWN — kapı tanımsız |
| K11 | Varsayılan-geçiş yok | K5/K10 kapıları açıkken | “Geçti” raporlanamaz | Rapor “KAPALI KAPI” olarak yazılır |
| K12 | Dürüstlük | Her ölçüm satırında koşul + tarih | Yoksa | Satır sayılmaz |

## §9 Hata enjeksiyon senaryoları (FAULT)

| ID | Enjeksiyon | Yöntem (tasarım) | Beklenen dayanım | Tespit kaynağı | Sonuç alanı |
|---|---|---|---|---|---|
| F1 | Referans saat kaynağı kaybı | Kaynağı kapat/bağlantıyı kes | Ölçüm penceresi geçersiz sayılır; sahte sonuç yok | §4.3 S6 doğrulama | Pencere iptal |
| F2 | Cihaz kaybı (tak-çıkar) | Fiziksel/lojik çıkarma | Yeniden başlatma; sayaç zıplaması raporlanır | cihaz olayı logu | SYNC-11 |
| F3 | Saat sıçraması (ileri/geri) | OS saat olayı enjeksiyonu | `ref_counter` bozulabilir; `sample_time` bozulmaz | §6 SYNC-14 | K1 ihlali varsa FAIL |
| F4 | Yapay kayma enjeksiyonu | Tanımlı ±drift uygula (aracı sürücü/analiz) | Ölçüm eşik yönünü doğru görmeli | ppm hesabı §5.1 | Kalibrasyon kontrolü |
| F5 | Xrun/underrun enjeksiyonu | Bütçe aşımı zorla | Kırılma sonrası monotonluk korunur | `xrun_flag` | SYNC-10 |
| F6 | CPU kısıtlaması | Yük ekle | Kırılma varsa raporlanır; gizlenmez | realtime sayaçları (bkz [[realtime-kapsam]]) | K9 |
| F7 | Yanlış tampon boyu | 512 dışı değer dayat | Yapılandırma reddedilir | hw_params kontrolü | K3 |
| F8 | Yanlış f_s | 48 kHz dışı dayat | Yapılandırma reddedilir | hw_params kontrolü | K3 |
| F9 | İki uygulama eşzamanlı | 2. uygulama başlat | Exclusive Lock reddi (kural 9) | kilit hatası | K6 |
| F10 | Kayıt yolu dolması | Kayıt alanını doldur | Eksik-veri uyarısı; “geçti” yazılmaz | şema kontrolü | K8 |
| F11 | Seri içinde değer tekrarı | Sahte tekrar satırı enjeksiyonu | Monotonluk denetimi yakalar | §4.3 S6 | K1 |
| F12 | Sıfır/pozitif sonsuz değeri | Bozuk ham seri | Hesap reddedilir (D7) | §5.2 D7 | Pencere geçersiz |

## §10 Regresyon kapıları

| Kapı | Tetikleyici | Zorunlu test kümesi | Kapatma koşulu |
|---|---|---|---|
| R1 | Senkron kodu değişikliği (henüz yok — §1 #7 baseline) | §6.1 tamamı + §8 K1–K4, K7 | Tüm zorunlu kapılar PASS veya KAPALI-KAPI olarak işaretli |
| R2 | Tekrarlanabilirlik kapısı | SYNC-16 iki çalıştırma | K10 kararı verilene kadar kapalı |
| R3 | Routing/parametre değişikliği | SAMP-09 + SAMP-02 | Ofset = 0 doğrulandı |
| R4 | Realtime bütçe değişikliği | SAMP-06 + §9 F5/F6 | Bütçe tablosu güncellendi mi? (bkz [[realtime-kapsam]]) |
| R5 | Firmware saati değişikliği (K005) | §4.3 S1–S8 tamamı | Yeni saat kaynağı kararı #11 ile yenilendi |
| R6 | OS katmanı değişikliği (K000) | §4.3 S11 çapraz kontrol | K000 dokümanı ile çelişki yok |
| R7 | Eşik kararı güncellenmesi | §7.1 yeniden değerlendirme | UNKNOWN → karar; karar yoksa kapı kapalı |
| R8 | Kapı “geçti” iddiası | §8 K11 | Tüm ölçümler koşul+tarih içerir |

## §11 Bağlantılar ve izleyen adımlar

| Bağlantı | Dosya | İlişki |
|---|---|---|
| Kardeş kapsam | [[synchronization-kapsam]] | Protokol buradaki tasarım satırlarını `[TASARIM]` → `[KANIT]` yapar |
| Kardeş örnekler | [[synchronization-ornekler]] | Senaryo örnekleri (SYNC/SAMP) burada somutlaşır |
| Dizin | [[index]] | Düğüm özeti ve çocuk listesi |
| OS ses katmanı | [[K000-isletim-sistemi/isletim-sistemi-audio/index]] | Saat/scheduler/cihaz davranışı sahibi katman |
| Realtime bütçesi | [[realtime-kapsam]] | E5/E9 eşikleri bu bütçeye bağlı |
| Test piramidi | [[testing-kapsam]] | §6 senaryoları otomasyon kapsamına girer |
| Kapsamın örneği | [[routing-kapsam]] · [[mixing-kapsam]] | SAMP-09 (block kenarı) sınırı |

## §12 Kaynak denetim kaydı (2026-10-13)

> **Yöntem:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
> Bu tabloda tam URL yazılmaz (eklenen bölümler 0 yeni URL içerir); §1'deki URL'ler
> bu denetimin de girdisidir.

| # | Kaynak | HEAD sonucu | Not |
|---|---|---|---|
| 1 | Apple Developer Documentation — AudioUnitRender (AudioToolbox) | **HTTP 200** | §1 #2 iddiasının güncel sayfası |
| 2 | Apple Developer Documentation — AUAudioUnit (AudioToolbox) | **HTTP 200** | Render yaşam döngüsü referansı |
| 3 | PipeWire dokümantasyonu — latency sayfası (docs.pipewire.org) | **HTTP 200** | §1 #3 karşılığı (güncel alan adı) |
| 4 | PipeWire dokümantasyonu — tasarım sayfası (docs.pipewire.org) | **HTTP 200** | Graf/nesne modeli |
| 5 | Microsoft Learn — Core Audio programming guide | **HTTP 200** | OS saat/cihaz davranışı referansı |
| 6 | ALSA projesi wiki — ALSA Library API | **HTTP 200** | §1 #5 karşılığı |
| 7 | §1'deki `pipewire.org/page_*` adresleri (3 adet) | **HTTP 404** | İçerik taşınmış görünüyor — **§1 satırları bu denetimle yeniden değerlendirilmelidir**; sessiz düzeltme yasak olduğu için yalnızca burada not edildi |
| 8 | §1'deki `alsa-project.org/doc/api/*` adresi | **HTTP 404** | Aynı neden — not kaydı |
| 9 | §1'deki Apple `coreaudio/audiounit/1571111-…` adresi | **HTTP 404** | Yeni konum: AudioToolbox alanı (satır 1) |
| 10 | `aes.org` | **HTTP 403** | Doğrulanamadı → **⚠️ VERIFICATION REQUIRED** (bu dosyada kullanılmadı) |

## §13 Uçtan uca doğrulama akışı (kapı sırası)

| # | Aşama | Girdi | İşlem | Kapı | Çıktı | Kapı kapalıysa |
|---|---|---|---|---|---|---|
| 1 | Karar bekleme | §1 #9–#11 | Clock referansı kararı (#11) | C0 | Seçili 2 kaynak | DUR — protokol yok |
| 2 | Ön koşu denetimi | §4.2 P1–P11 | Tablo satırı satır kontrol | C1 | Ön koşu raporu | P4/P8 yoksa DUR |
| 3 | Yapılandırma | 48 kHz · 512 · float | Cihazı sabitle | C2 | hw_params kaydı | P2/P3 ihlali → iptal |
| 4 | Kilit | Exclusive Lock | Tek uygulama kilidi | C3 | kilit kaydı | kilit yoksa iptal |
| 5 | Referans kurulumu | seçilen referans | Ölçüm arayüzünü bağla | C4 | referans senkronu | P8 çözülmediyse DUR |
| 6 | Pencere çalıştırma | §4.5 planı | Ham seri topla (§4.4) | C5 | ham seri dosyası | eksik alan → geçersiz |
| 7 | Seri doğrulama | ham seri | Monotonluk/boşluk/tekrar | C6 | doğrulama raporu | ihlal → §9 F3/F11 |
| 8 | Hesap | doğrulanmış seri | ppm + süre (§5) | C7 | hesap satırları | D7 ihlali → pencere geçersiz |
| 9 | Eşikleme | hesap + §7 | Aday eşiklerle karşılaştır | C8 | eşik tablosu | E2 yoksa K5 kapalı |
| 10 | Kapı değerlendirme | §8 | PASS / FAIL / KAPALI-KAPI | C9 | kapı sonucu | “geçti” yazımı yalnız C9 PASS'ta |
| 11 | Hata enjeksiyonu | §9 F1–F12 | Dayanım testi | C10 | enjeksiyon raporu | — |
| 12 | Tekrar | §6 SYNC-16 | İkinci koşu | C11 | tekrar karşılaştırması | K10 kararı yoksa kapalı |
| 13 | Rapor | tüm kayıtlar | Oturum raporu (§14) | C12 | rapor | eksik kayıt → sayılmaz |
| 14 | Karar haritası | açık sorular | §15 kapanış haritası | C13 | güncellenen soru durumları | — |
| 15 | Vault işleme | sonuçlar | `synchronization-kapsam` `[TASARIM]` → `[KANIT]` | C14 | vault satır güncellemesi | yalnız gerçek ölçümle |
| 16 | Regresyon kaydı | §10 | Kapıları kayda al | C15 | regresyon defteri | — |

## §14 Ölçüm oturumu rapor şeması (boş form — doldurulmadı)

### §14.1 Oturum künyesi

| Alan | Değer |
|---|---|
| `run_id` | *(boş)* |
| Tarih | *(boş)* |
| Sorumlu | *(boş)* |
| Cihaz | *(boş)* |
| Referans kaynak (#11 kararı) | *(boş — UNKNOWN)* |
| f_s / blok boyu | 48000 Hz / 512 örnek (sartname §8) |
| Yazılım sürümü | *(boş)* |
| Exclusive Lock doğrulandı mı | *(boş)* |

### §14.2 Pencere sonuçları (satır = pencere)

| run_id | pencere | Δ (örnek) | Ö_t | drift_ppm | xrun | K1–K12 kapıları | Sonuç |
|---|---|---|---|---|---|---|---|
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |

### §14.3 Enjeksiyon sonuçları (satır = FAULT)

| FAULT ID | Uygulandı mı | Beklenen | Gözlenen | Sonuç |
|---|---|---|---|---|
| F1 | *(boş)* | pencere iptali | *(boş)* | *(boş)* |
| F2 | *(boş)* | sayaç zıplama raporu | *(boş)* | *(boş)* |
| F3 | *(boş)* | `sampleTime` bozulmaz | *(boş)* | *(boş)* |
| F4 | *(boş)* | ppm yönü tutarlı | *(boş)* | *(boş)* |
| F5 | *(boş)* | monotonluk korunur | *(boş)* | *(boş)* |
| F6 | *(boş)* | kırılma gizlenmez | *(boş)* | *(boş)* |
| F7/F8 | *(boş)* | yapılandırma reddi | *(boş)* | *(boş)* |
| F9 | *(boş)* | kilit reddi | *(boş)* | *(boş)* |
| F10/F11/F12 | *(boş)* | eksik/bozuk veri yakalanır | *(boş)* | *(boş)* |

**Boş form kuralı:** Bu tabloların doluluğu = ölçümün varlığıdır. Hiçbir hücre
“tahmini” doldurulamaz; boş = ölçülmedi.

## §15 Açık soru → kapanış haritası

| Soru (§1) | Kapanış koşulu | Kapanışta ne değişir | Sorumlu | Kapı |
|---|---|---|---|---|
| #9 drift telafi yöntemi | Ölçüm + tasarım kararı | `synchronization-kapsam` satırı `[TASARIM]` → `[KANIT]` | embedded-engineer | C1 |
| #10 ölçüm periyodu / eşik | Eşik kararı (§7.1 E2) | E2 UNKNOWN → karar; K5 açılır | embedded + qa | C8 |
| #11 ana clock referansı | Karar onayı | §4.2 P4 ✅; protokol başlayabilir | embedded-engineer | C0 |
| #12 ağ akışı kapsamı | Üst karar | A3 kapsamı netleşir | architect | C0 |
| §12 satır 7–9 (URL 404) | Vault URL denetimi süreci | §1 satırları yeniden değerlendirilir (sessiz düzeltme yasak) | vault steward | — |

## §16 Risk ve belirsizlik defteri

| # | Risk / belirsizlik | Etki | Olasılık | Azaltma | Durum |
|---|---|---|---|---|---|
| RSK-1 | #11 kararı yok → protokol başlamaz | Yüksek | Kesin (şu an) | Karar kapısı C0 | Açık |
| RSK-2 | Ölçüm arayüzü bilinmiyor (P8) | Yüksek | Yüksek | Arayüz keşfi + K000 çapraz kontrol | Açık |
| RSK-3 | §1 URL'leri 404 (§12 satır 7–9) | Orta | Gerçekleşmiş | URL yenileme süreci (onaylı) | Açık |
| RSK-4 | Telafi yöntemi seçimi (#9) ölçümü etkiler | Orta | Orta | Önce ölçüm, sonra yöntem seçimi | Açık |
| RSK-5 | Donanım saati K005'e bağımlı | Orta | Orta | K003 sınırında kal (§1 #8) | Açık |
| RSK-6 | Ağ kapsamı (#12) genişlerse protokol büyür | Düşük | Belirsiz | Kapsam kararı erkenden | Açık |
| RSK-7 | Eşik uydurma baskısı | Yüksek | İnsan kaynaklı | §7 yalnız UNKNOWN/⚠️ işareti taşır | Kalıcı kontrol |
| RSK-8 | K004 DSP zinciri ofsete yol açar (SAMP-09) | Orta | Belirsiz | Routing/regresyon kapısı R3 | Açık |

## §17 Ölçüm araçları envanteri (PLANNED — kullanım öncesi doğrulanacak)

| # | Araç (aday) | Amaç | Kaynak/kanıt | Durum |
|---|---|---|---|---|
| T1 | Uygulama içi sayaç (`sampleTime` okuma) | Aday kaynak serisi | §1 #2 (Apple dokümanı) | ⚠️ kod yok (§1 #7) |
| T2 | OS sayacı (referans) | Referans seri | K000 katmanı | ⚠️ VERIFICATION REQUIRED |
| T3 | Donanım sayacı (I2S/TDM, K005) | Donanım referansı | §1 #8 (K005 sahipliği) | ⚠️ VERIFICATION REQUIRED |
| T4 | Xrun sayacı | Kırılma bayrağı | `realtime-kapsam` | ⚠️ VERIFICATION REQUIRED |
| T5 | CSV/JSON kaydedici | Ham seri saklama | iç geliştirme | PLANNED |
| T6 | ppm hesaplayıcı (§5) | Hesap | bu dosya §5.1–5.2 | ✅ tasarım var |
| T7 | Exclusive Lock denetçisi | Kural 9 kontrolü | proje kuralları | ✅ kural var |
| T8 | Kapı değerlendirici (§8) | PASS/FAIL | bu dosya §8 | ✅ tasarım var |

## §18 Denetim listesi (bu dosyanın kendi kalite kapısı)

- [ ] §1–§3 orijinal metni değişmedi (append-only)
- [ ] Frontmatter: 8 zorunlu alan yerinde (node_type, status, parent_id, children, depends_on, evidence, validation, open_questions)
- [ ] `node_id` = dizin adı + `-dogrulama`
- [ ] `group: kanit` ve `parent_id: K003-audio` mevcut
- [ ] Eklenen bölümlerde yeni tam URL yok
- [ ] Eklenen bölümlerde ölçüm sonucu (sayısal sonuç) yok — yalnız aritmetik/plan
- [ ] Tüm UNKNOWN/⚠️ işaretleri §1 ile tutarlı
- [ ] Eşiklerin tamamı “aday” etiketli (§7.1)
- [ ] Wiki-linkler: kardeşler + K000 isletim-sistemi-audio
- [ ] Dürüstlük bloğu mevcut
- [ ] Tek Authority satırı, dosyanın en sonunda
- [ ] git commit atılmadı (orkestratöre ait)

## §19 Terimler (bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| **Drift (ppm)** | Saat kaynağı frekans sapmasının milyonda biri; §5.1 formülü |
| **Pencere** | Kesintisiz toplanan blok serisi (§4.5) |
| **KAPALI-KAPI** | Kriter kararı henüz verilmemiş kapı; “geçti” yazımı yasak (§8 K11) |
| **Aday eşik** | Onay bekleyen eşik değeri; ölçüm sonucu değildir (§7.1) |
| **SAMP** | Sample-accurate senkron test kimliği (§6.2) |
| **FAULT** | Hata enjeksiyonu senaryo kimliği (§9) |
| **Çapraz kontrol** | K000/K004/K005 düğümleriyle belge karşılaştırması (§4.3 S10–S11) |
| **Boş form** | Ölçüm yokluğunu dürüstçe taşıyan tablo (§14) |
| **Sessiz düzeltme** | Kayıt satırını iz bırakmadan değiştirme — yasak |

## §20 Ek kayıtlar

| Tarih | Kayıt | Yapan |
|---|---|---|
| 2026-10-12 | §1–§3 ilk kanıt tablosu + sayım + geçiş kuralları | Vault Steward |
| 2026-10-13 | Frontmatter derinleştirme (8 zorunlu alan) + §4–§20 eklendi (append-only) | Vault Steward (subagent) |
| 2026-10-13 | Kaynak HEAD denetimi (§12) — §1 URL'lerinden 3 grup 404 tespiti raporlandı, §1 değiştirilmedi | Vault Steward (subagent) |

## §21 Kuru çalıştırma (prova) protokolü — ölçüm OLMADAN

> Amaç: protokolün kendisini, gerçek clock kararı olmadan **yalnız akış/şekil**
> açısından sınamak. Bu prova hiçbir eşik/kapı sonucu üretmez.

| Adım | İşlem | Doğrulanan | Doğrulanmayan |
|---|---|---|---|
| DR1 | §4.4 şemasını dosyala (boş veriyle) | Alan adları/tipler okunur | — |
| DR2 | 5 satır örnek üret (rastgele, `run_id=DRY`) | Şema esnekliği | ppm/iddda — **yazılmaz** |
| DR3 | §5.1 formülünü örnek satıra uygula | Hesap hattı çalışır (aritmetik) | Doğruluk iddiası |
| DR4 | §8 kapılarını örnek satıra çalıştır | “KAPALI-KAPI” yazımı çıkar (K11) | Gerçek PASS/FAIL |
| DR5 | §14.2 formunu DR verisiyle değil boş doldur | Boş form bütünlüğü | — |
| DR6 | §9 F10–F12'yi bozuk metinle dene | Şema hatası yakalanır | — |
| DR7 | Sonuç satırlarını sil, dosyayı boş forma döndür | Prova izi kalmaz | — |

**Kuru çalıştırma kuralı:** DR çıktıları hiçbir yerde “ölçüm” diye
referans gösterilemez; §18 denetim listesinde ayrıca işaretlenir.

## §22 Kod inceleme kapıları (repo kodu yazıldığında uygulanacak)

| # | İnceleme | Kural kaynağı | Kabul |
|---|---|---|---|
| CR1 | Callback `noexcept` | kural 3 (proje kuralları) | her callback imzası |
| CR2 | Ses thread'inde `malloc/new/delete` yok | kural 1 | tarama 0 isabet |
| CR3 | Ses thread'inde mutex/lock yok | kural 2 | tarama 0 isabet |
| CR4 | `alignas(64)` tampon hizası | kural 5 | statik doğrulama |
| CR5 | 32-bit float zinciri | ADR-017 | tip denetimi |
| CR6 | PCM5122 yasağı (yalnız PCM3168A) | ADR-038 H001 | kod/envanter taraması |
| CR7 | 512/48 kHz sabitleri tanım kaynaklı | sartname §8 | sabit tanım kontrolü |
| CR8 | Yeni iddialar §1'e işlenmeden “doğrulandı” yok | bu dosya §1 | diff denetimi |
| CR9 | Eşik sabitleri kodda “verified” etiketli değil | §7.1 | etiket denetimi |
| CR10 | Lock-free kuyruk tasarımı varsa `realtime-kapsam` ile çelişmiyor | §10 R4 | çapraz okuma |

## §23 Sık sorular (bu dosyaya özgü)

| # | Soru | Kısa cevap | Ayrıntı |
|---|---|---|---|
| Q1 | “Drift kaç ppm ölçülü?” | Henüz ölçülmüştür | §1 #10 — UNKNOWN, sayı uydurulmaz |
| Q2 | “Hangi clock referans?” | Karar verilmedi | §1 #11 — ⚠️ VERIFICATION REQUIRED |
| Q3 | “Protokol başlayabilir mi?” | Hayır — C0 kapısı kapalı | §13 aşama 1 |
| Q4 | “§1'deki URL'ler çalışıyor mu?” | Bir kısmı 404 | §12 satır 7–9 |
| Q5 | “Ölçüm sonucu bu dosyada mı?” | Hayır — yalnız form/protokol | §14 boş form |
| Q6 | “Ağ senkronu dahil mi?” | Bilinmiyor | §1 #12 — üst karar |
| Q7 | “Kanallar arası ofset beklentisi?” | 0 örnek (tasarım) | §6.2 SAMP-01 |
| Q8 | “Kim onaylar?” | embedded (ölçüm) + qa (kapı) | §4.1 A7 |
| Q9 | “Commit atıldı mı?” | Hayır — orkestratöre ait | kural 8 |

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 yeni URL,
> 0 uydurma ölçüm içerir; doğrulanmış kaynaklar yalnız belirtilen URL'lerdir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
