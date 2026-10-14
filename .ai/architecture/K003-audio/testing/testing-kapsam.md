---
title: "testing — Kapsam"
type: architecture-scope
node_id: K003-audio/testing
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# testing — Kademe-2 Kapsam Kaydı (K003)

## §1 Rol (ELI15)

`audio-master.md` §3 satır 12 bu dalı **"Test senaryoları ve kabul kriterleri"**
olarak yazar. Basitçe: motorun **kabul komisyonu** — "şu koşullarda şu
sonuç çıkarsa geçer" listesi. Diagnostics ölçer, testing **karar verir**:
ölçülen değer eşiğin altındaysa ✅, üstündeyse ❌. auval'ın audio unit'ı
doğrulaması gibi: bağımsız bir araç, tanımlı kriterlere bakıp geçer/geçmez
der (master §4 A6).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Test senaryosu listesi, eşik
> değerleri ve otomasyon kapsamı kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Test senaryolarının tanımı (ne, hangi koşulda test edilir) | Ölçüm değerinin toplanması | diagnostics (master §3 satır 11) |
| 2 | Kabul kriterleri (eşik/geçme-değeri) — **değerler UNKNOWN, karar bekliyor** | Ölçümün kendisi (metrik üretimi) | diagnostics |
| 3 | Bağımsız doğrulama aracının (auval benzeri) gereksinimi | Aracın uygulanması | araç/seviye — master §4 A6 |
| 4 | K003 genelinde regresyon testi kapsamı (ne zaman, kaç senaryo) | Test altyapısı (PHPUnit/Vitest mevcut ama ses testi yok) | QA (`.ai/AGENTS.md` §4 qa satırı) |
| 5 | Xrun/desync/yanlış-routing kabul kriterlerinin tanımı | Bu olayların raporlanması | diagnostics (master §3 satır 11) |
| 6 | *(dışında)* DSP algoritması doğrulaması (EQ/reverb/ölçüm testi) | K004 testleri | K004-dsp/testing (ayrı dal) |
| 7 | *(dışında)* donanım/firmware testleri | Donanım testi | K001 · K005 (master §2.2) |
| 8 | *(dışında)* hata loglarının sınıflandırması | Sınıf şeması | diagnostics (master §3 satır 11) |

**Yasak:** K003 testing ↔ K004 testing **iki ayrı daldır** (v2 §2.3 ve §2.4'te
ayrı çocuk adları — KURAL 1); tek dosyada birleştirilemez. diagnostics ↔
testing ayrımı (ölç ↔ karar ver) bağlayıcıdır (master §3 satır 11–12).

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| TS1 | testing ↔ diagnostics | kabul kararı ↔ ölçüm verisi | ✅ master §3 satır 11/12 (ayrı çocuklar) |
| TS2 | testing ↔ K004/testing | K003 motor testi ↔ DSP algoritma testi | ✅ v2 §2.3/§2.4 ayrı dallar (KURAL 1) |
| TS3 | testing ↔ engine | test edilen sistem ↔ test edilen döngü | ⚠️ ad-türetme (master §3 satır 12 ve 1) |
| TS4 | testing ↔ realtime | zamanlama kriteri ↔ kuralın kendisi | ⚠️ ad-türetme (master §3 satır 12 ve 9) |
| TS5 | testing ↔ QA (repo) | K003 kabul kriteri ↔ repo test altyapısı (PHPUnit) | ✅ `.ai/AGENTS.md` §4 qa (iç kanıt) |
| TS6 | testing ↔ backend/asio | araç gereksinimi ↔ platform doğrulama aracı | ⚠️ ad-türetme (master §4 A6) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "testing · Test senaryoları ve kabul kriterleri · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 73 | ✅ dosya okundu |
| 2 | `auval` CLI, bir audio unit'ı plug-in API + temel işlev üzerinden doğrular | `audio-master.md` §4 A6 (Apple) | ✅ dosya okundu |
| 3 | QA agent hedefi: test coverage ≥%80, flaky test %0 | `.ai/AGENTS.md` §16 Quality Standards (QA satırı) | ✅ dosya okundu |
| 4 | Test altyapısı gerçektir: `shared/tests/` 22 dosya, PHPUnit ^10.5/^11.0 | composer.json require-dev · `shared/tests/` envanteri (`.ai/AGENTS.md` §25.2) | ✅ dosya okundu |
| 5 | Repo'da ses motoru test kodu yok (C++/DSP testi) | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 6 | K004 testing ayrı bir çocuk (v2 §2.4) | v2 §2.4 kaydı · K004-dsp/testing/ klasörü | ✅ dosya okundu |
| 7 | K003 kabul eşikleri (xrun/desync/latency) | — | ⚠️ VERIFICATION REQUIRED (eşik yok) |
| 8 | Test senaryosu listesi ve otomasyon kapsamı | — | **UNKNOWN** (senaryo yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | K003 kabul eşikleri nedir (SNR/THD+N/latency budget)? | ⚠️ VERIFICATION REQUIRED | qa-engineer |
| R2 | Test senaryosu listesi (hangi senaryo, hangi koşul)? | **UNKNOWN** — senaryo yok | qa-engineer |
| R3 | auval benzeri bağımsız K003 doğrulama aracı yapılacak mı? | ⚠️ VERIFICATION REQUIRED | embedded-engineer |
| R4 | K003 testing ↔ K004 testing sınırı tam olarak nerede? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R5 | Otomasyon (CI'da) hangi testleri çalıştıracak? | ⚠️ VERIFICATION REQUIRED (.github/workflows ci.yml var, içerik doğrulanmadı) | devops-engineer |
| R6 | Flaky/ölçüm testleri ne sıklıkla koşulur? | **UNKNOWN** — plan yok | qa-engineer |

**Kapanış:** status: draft kalır. R1/R2 kapanmadan eşik ya da senaryo listesi
yazılmaz → UNKNOWN; "coverage %80 karşılandı" iddiası ölçüm olmadan yazılmaz
(ölçüm: ses testi 0 — satır 5).

## §6 Test stratejisi piramidi (K003 — şablon)

§2 satır 1 ve 4'teki "test senaryolarının tanımı" ile "regresyon kapsamı"
maddelerinin piramit şablonu. **Katmanlar ve ağırlık (ne kadar çok test)
öneridir**; sayı/coverage hedefi R1'e bağlıdır.

| Katman | Ne test edilir | K003 örneği | Ağırlık (öneri) | Sahip |
|---|---|---|---|---|
| L1 Birim (unit) | Tek bileşen, dış bağımlılık yok | Matris bütünlüğü, monoton sayaç, kuyruk taşması | Çok | testing |
| L2 Entegrasyon | İki+ bileşen sınırı | RT↔non-RT kuyruk, routing↔mixing sınırı | Orta | testing + diagnostics |
| L3 Sistem | Uçtan uca ses yolu | Dosya → motor → çıkış (döngü testi) | Az | testing + QA |
| L4 Ölçüm (measurement) | Sayısal ses kalitesi | SNR/THD+N, jitter, latency | Az ama **zaman alanıcı** | testing + diagnostics |

**L4 notu:** ölçüm testleri **cihaz gerektirir** (gerçek DAC/hoparlör ya da
döngü testi — loopback); repo'da bunların hiçbiri **yoktur** (§4 satır 5).

## §7 SNR / THD+N test şablonları (eşikler UNKNOWN — R1)

§2 satır 2'deki "kabul kriterleri" maddesinin ölçüm şablonu. **Metot
tanımlıdır; eşikler tanımsızdır** (R1 — sayı uydurulmaz).

| # | Ölçüm | Girdi | Çıktı ölçümü | Eşik |
|---|---|---|---|---|
| 1 | SNR (sinyal-gürültü oranı) | Sessizlik + referans sinyal | Gürültü katmanı | **UNKNOWN** (R1) |
| 2 | THD+N (bozulma) | 1 kHz sinüs | harmonik + gürültü | **UNKNOWN** (R1) |
| 3 | Frekans tepkisi | Süpürme (sweep) | Tepki eğrisi | **UNKNOWN** (R1) |
| 4 | Kanal ayrımı (crosstalk) | Tek kanal sinyal | Komşu kanal sızıntısı | **UNKNOWN** (R1) |
| 5 | DC offset | Sessizlik | Ortalama DC | **UNKNOWN** (kural #10: >0.5V relay — donanım kuralı, yazılım eşiği yok) |

**Ölçüm protokolü şablonu (her ölçüm için):**

| Aşama | Ne yapılır | Not |
|---|---|---|
| 1 | Ortam hazırlığı (cihaz, sıcaklık, kablo) | K001'e bağlı |
| 2 | Kalibrasyon (referans seviyesi) | Ölçüm öncesi |
| 3 | Örnek toplama (N örnek) | N **UNKNOWN** (R1) |
| 4 | Hesap (ortalama, RMS) | Yöntem seçimi R1 |
| 5 | Karşılaştırma (eşik) | R1 kapanınca |

## §8 PASS/FAIL ölçüt şablonu (TS1 bağlantılı)

Diagnostics **ölçer**, testing **karar verir** (§3 TS1). Bu şablon karar
mantiğinin iskeletidir.

| # | Ölçüt | Kural (şablon) | Karar |
|---|---|---|---|
| 1 | Ölçüm değeri < eşik | Kabul | ✅ PASS |
| 2 | Ölçüm değeri ≥ eşik | Red | ❌ FAIL |
| 3 | Ölçüm yok (cihaz bağlı değil) | **Ölçülmedi** (geçerli sonuç değil) | ⚠️ INCONCLUSIVE |
| 4 | Ölçüm değişken (kararsız) | Tekrarlama gerekir | ⚠️ INCONCLUSIVE |
| 5 | Eşik tanımsız (R1 açık) | Karar **verilemez** | ⚠️ DOOR-OPEN |

**Kural:** eşik tanımsızken "PASS" **yazılamaz** (uydurma eşik = hallüsinasyon).
§8 satır 5, bu dosyanın mevcut durumunu da tanımlar: R1/R2 açık →
DOOR-OPEN kapıları açık kalır.

## §9 Regresyon matrisi şablonu (R2 — senaryo yok)

§2 satır 4'teki "regresyon testi kapsamı" maddesi. **Senaryolar R2'de
tanımlanacaktır**; bu matris yalnız **sütun yapısını** verir.

| # | Senaryo (R2) | Ne zaman | Otomatik mi? | Beklenen |
|---|---|---|---|---|
| 1 | Pull-model zinciri | Her commit (planlanan) | Evet (cihaz yoksa mock) | Zincir pull çağrılıyor |
| 2 | Monoton sampleTime | Her commit | Evet | Süreklilik (§7 testing-ornekler) |
| 3 | Lock-free (RT yolunda yasak yok) | Her commit (statik denetim) | Kısmen | İhlal 0 |
| 4 | Underrun davranışı | Periyodik (cihazla) | Hayır (cihaz) | Sessizlik, eski veri yok |
| 5 | SNR/THD ölçümleri | Sürüm öncesi (cihazla) | Hayır (cihaz) | Eşik altında (R1) |
| 6 | Çoklu çıkış (R3 kararı verilirse) | Sürüm öncesi | Hayır | Ayna doğru |

## §10 Xrun/desync/yanlış-routing kabul senaryoları (şablon)

§2 satır 5'teki "kabul kriterlerinin tanımı" maddesinin üç senaryo ailesi.

| # | Aile | Test girdisi | Beklenen davranış | Sahip |
|---|---|---|---|---|
| 1 | Xrun (underrun) | Zorla besleme durdurma | Sessizlik + sayaç + rapor (output §12) | testing + diagnostics |
| 2 | Desync | İki clock kayması zorla | Drift raporu (PipeWire örüntüsü) | testing + synchronization |
| 3 | Yanlış routing | Matris bozulur | §9 testing-ornekler — tespit | testing + routing (R5) |
| 4 | Cihaz kaybı | Cihaz çıkarılır | Fallback kuralı (T8) | testing + device |
| 5 | Priority inversion denemesi | RT yolunda kilitleme zorla | Derleme/denetim hatası (K2) | testing + realtime |

## §11 Otomasyon kapıları (CI — R5)

§2 satır 4 ve §5 R5 ile bağlantılı. Repo'da **gerçek CI kanıtı** vardır
(`.github/workflows/` = ci.yml, secret-scan.yml — §4 satır 3-4) ama bu
workflow'ların **içeriği doğrulanmadı** → R5 ⚠️.

| # | Otomasyon adayı | CI uyumu | Durum |
|---|---|---|---|
| 1 | Birim testleri (L1) | Uygun (cihaz yok) | ⚠️ R5 — içerik doğrulanmadı |
| 2 | Statik denetim (lock-free yasağı) | Uygun (analiz) | ⚠️ araç seçimi yok |
| 3 | Entegrasyon (L2) | Kısmen (cihaz mock) | ⚠️ R2 |
| 4 | Ölçüm testleri (L4) | **Uygun değil** (cihaz gerektirir) | ❌ ayrı laboratuvar akışı |
| 5 | Regresyon (§9) | Uygun (L1/L2) | ⚠️ R2 |

## §12 Ölçüm ve kapanış kapıları (açık)

| Kapı | Koşul | Şu an |
|---|---|---|
| TS-G1 | Kabul eşikleri (SNR/THD/latency — R1) | ❌ ⚠️ |
| TS-G2 | Senaryo listesi (R2) | ❌ UNKNOWN |
| TS-G3 | Bağımsız doğrulama aracı (R3, auval benzeri) | ❌ ⚠️ |
| TS-G4 | K003 ↔ K004 testing sınırı (R4) | ❌ ⚠️ |
| TS-G5 | CI otomasyon kapsamı (R5) | ❌ ⚠️ |
| TS-G6 | Ölçüm sıklığı planı (R6) | ❌ UNKNOWN |

## §13 Test stratejisi — kapsam tanımı (piramit neyi kapsar)

| Katman | Ne test edilir | Bu dosyada |
|---|---|---|
| Birim | Tek fonksiyon/bileşen davranışı | §6 iskeleti |
| Entegrasyon | Zincirin birleşmesi (routing→output vb.) | §6 iskeleti |
| Sistem (Uçtan uca) | Gerçek cihaz + gerçek akış | §10 senaryoları |
| Performans | Latans/xrun bütçesi | realtime dosyalarına bağımlı |
| Bağımsız doğrulama | Ölçüm aracı (auval benzeri) | TS-G3 (açık) |

**Piramit yüksekliği (kaç test)** → `UNKNOWN` (R2: senaryo listesi yok).

## §14 SNR/THD+N — testin ne ölçtiği (tanım)

| Metrik | Ne | Birim |
|---|---|---|
| SNR | Sinyal / gürültü oranı | dB (değer `UNKNOWN`) |
| THD+N | Bozulma + gürültü / sinyal | % veya dB (`UNKNOWN`) |
| Frekans noktası | 1 kHz gibi | Sartnamede 1 kHz anılıyor mu → kontrol §7 |
| Seviye | Test seviyesi | `UNKNOWN` |

**Eşik yazmak yasaktır** (R1) — eşik, ürün kararı + ölçüm verisi ister.

## §15 PASS/FAIL ölçütü — neden kapalı

```text
  ölç   → veri
  karşılaştır → eşik (UNKNOWN)
  karar → PASS/FAIL
```

| Adım | Durum |
|---|---|
| Ölçüm altyapısı | Yok (TS-G3 açık) |
| Eşik | `UNKNOWN` (TS-G1 açık) |
| Karar | **Yazılabilir değil** |

Bu nedenle §8 tamamen `UNKNOWN` doludur — bilgi eksikliği değil, **kapı
açık**.

## §16 Regresyon matrisi — neyi korur

| Korunan davranış | Kaynağı | Senaryo |
|---|---|---|
| 512/48k yapılandırması | sartname §8 | `UNKNOWN` (R2) |
| Lock-free yol | kural 1/2 | `UNKNOWN` |
| Kanal sırası | output | `UNKNOWN` |
| Eşleme | routing | `UNKNOWN` |
| DC koruma | kural 10 | `UNKNOWN` |

Her satırda **senaryo adı yok** → R2 kapanmaz.

## §17 Xrun / desync / yanlış-routing — kabul senaryoları analizi

| Olay | Kabul mü, hata mı? | Eşik |
|---|---|---|
| Xrun | Kesinlikle hata (muhtemelen) | Ama **kaç tanesi kabul** → `UNKNOWN` |
| Desync | Hata | Eşik `UNKNOWN` |
| Yanlış routing | Hata | Sıfır mı → `UNKNOWN` |

Not: "hata olması" ile "kabul eşiği" ayrı şeylerdir; kapı yalnız
ikinciyi ister.

## §18 CI otomasyon — kapsam soruları (R5)

| Soru | Cevap |
|---|---|
| Hangi testler CI'da? | `UNKNOWN` |
| Repo'da CI var mı? | `.github/workflows/` 2 dosya (`ci.yml`, `secret-scan.yml`) — **K003 C++ testleri için doğrulanmadı** |
| Eşik (kaç test geçmeli)? | `UNKNOWN` |
| Ortam (donanım)? | Ölçüm donanımı gerekli → `UNKNOWN` |

## §19 Ölçüm sıklığı (R6) — neden UNKNOWN

| Seçenek | Artısı | Eksişi |
|---|---|---|
| Her derlemede | Erken yakalar | Donanım bağlar |
| Günlük | Denge | — |
| Sürümde | Ucuz | Geç yakalar |

Seçim, TS-G6'ya bağlıdır; **hiçbiri tercih edilmez**.

## §20 Test verisi üretimi (şablon)

| Veri türü | Kullanım | Üretim |
|---|---|---|
| Sine (1 kHz vb.) | SNR/THD | Sartnamedeki frekans kontrolü gerekir |
| Beyaz gürültü | SNR | Üretici kod yok → `UNKNOWN` |
| Sweep | Frekans yanıtı | `UNKNOWN` |
| Bozuk veri (NaN) | Dayanıklılık | Kurgu — `UNKNOWN` |

Üretici kodu repo'da **yok** (`*.cpp` = 0).

## §21 Bağımsız doğrulama aracı (TS-G3) — gereksinim listesi

| Gereksinim | Not |
|---|---|
| Cihazdaki çıkışı ölçebilmek | Fiziksel erişim |
| Sinyali geri okuyabilmek | Loopback |
| Karşılaştırma raporu | Eşik olmadan PASS/FAIL vermez |
| Otomasyon | TS-G5 |

Aracın adı/ürüni **bilinmiyor** → "auval benzeri" ifadesi yalnız
**karşılaştırma** amaçlıdır; gerçek araç `UNKNOWN`.

## §22 K003 ↔ K004 testing sınırı (R4)

| Görev | K003 | K004 |
|---|---|---|
| Ses motoru davranışı | ✅ | — |
| Ölçüm/teşhis altyapısı | — | `?` |
| Ortak test senaryoları | `?` | `?` |

Sınır kararı yok → R4 açık. Bu dosya **iki tarafa da** test yazmaz.

## §23 Test → kapı eşlemesi

| Test türü | Kapı |
|---|---|
| SNR/THD ölçümü | TS-G1 |
| Regresyon senaryosu | TS-G2 |
| Bağımsız doğrulama | TS-G3 |
| Sınır testleri | TS-G4 |
| CI koşusu | TS-G5 |
| Sıklık | TS-G6 |

## §24 Risk kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Eşik yok → hiçbir test PASS diyemez | Test anlamsız | TS-G1 önce |
| R2 | Senaryo yok → regresyon kaçar | Hata | TS-G2 |
| R3 | Ölçüm aracı yok → bağımsız doğrulama yok | İddia doğrulanamaz | TS-G3 |
| R4 | Sınır belirsiz → çift iş | İsraf | TS-G4 |
| R5 | CI kapsamı belirsiz → otomasyon yok | Manuel test | TS-G5 |
| R6 | Sıklık yok → ölçüm unutulur | Süreçsizlik | TS-G6 |

## §25 Uygulama sırası (öneri)

TS-G1 → TS-G2 → TS-G3 → TS-G4 → TS-G5 → TS-G6.
**Gerekçe:** eşiksiz ölçüm anlamsızdır; senaryo olmadan otomasyon boştur.

## §26 Bu dosyanın komşularla sınırı

| Dosya | Ne alır | Ne vermez |
|---|---|---|
| `realtime/*` | Bütçe/kaçış tanımı | Test eşiği |
| `output/*` | Kanal sırası | Ölçüm |
| `routing/*` | Yanlış yönlendirme tanımı (R5) | Test çalıştırma |
| `synchronization/*` | Drift metriği | Kabul eşiği |
| `diagnostics/*` | Gözlem sinyali | Karar |

## §27 Dürüstlük kaydı

| İddia | Durum |
|---|---|
| Çalıştırılmış test | **Yok** → BLOCK |
| Ölçülmüş SNR/THD | **Yok** → `UNKNOWN` |
| CI'da K003 testi | **Doğrulanmadı** → `UNKNOWN` |
| Geçen kapsam yüzdesi | **Yok** → `UNKNOWN` |

## §28 SSS

| Soru | Cevap |
|---|---|
| Eşikler yazıldı mı? | Hayır — R1/TS-G1 açık |
| Test kodu var mı? | Hayır (`*.cpp` = 0) |
| Regresyon listesi var mı? | Hayır — §9 şablon |
| CI bağlandı mı? | Hayır — TS-G5 açık |

## §29 Kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `testing/testing-kapsam.md` |
| 2 | Bölüm aralığı | §1–§38 (son ek: §13–§38, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** |
| 4 | Açık kapı | TS-G1…TS-G6 |
| 5 | Açık soru | R1…R6 |

## §30 Eşik kararı — hangi girdiler gerekli

| Girdi | Kaynak | Durumu |
|---|---|---|
| Hedef SNR | Ürün kararı + donanım | `UNKNOWN` |
| Hedef THD+N | DAC/ADC datasheet'i (K000) | `UNKNOWN` (vault 0 dosya) |
| Latency bütçesi | sartname + ölçüm | `UNKNOWN` |
| Kabul edilen xrun | Ürün kararı | `UNKNOWN` |
| Ölçüm noktası | Test tasarımı | `UNKNOWN` |

**Hiçbir eşik bu dosyadan türetilmez** — hepsi TS-G1'e bağlıdır.

## §31 Ölçüm ortamı gereksinimleri (şablon)

| Gereksinim | Neden | Durum |
|---|---|---|
| Fiziksel loopback | Sinyali geri okumak | Donanım kararı yok |
| Referans cihaz | Bağımsız ölçüm | `UNKNOWN` |
| Kararlı ortam | Gürültü ölçümü | `UNKNOWN` |
| Tekrarlanabilirlik | Regresyon | Plan yok (R6) |

## §32 Birim test iskeleti (öncelikli, donanımsız)

| Aday | Neden | Engeli |
|---|---|---|
| Matris/routing mantığı | Saf veri | Kararlar açık (RO-G*) |
| Ring buffer mantığı | Saf veri | Kod yok (`*.cpp` = 0) |
| Birim dönüşümleri | Saf veri | Kod yok |
| DSP hesapları | Saf veri | Zincir PLANNED |

**Ortak engel:** test edilecek kod henüz var değil.

## §33 Entegrasyon testi — zincir tanımı

```text
  kaynak → routing → pipeline → output → (loopback) → ölç
```

| Parça | Durum |
|---|---|
| routing | PLANNED |
| pipeline | K004 — PLANNED |
| output | PLANNED |
| loopback | Donanım yok |

Zincirin tamamı PLANNED → entegrasyon testi **çalıştırılamaz**.

## §34 Regresyon tetikleyicileri (plan)

| Tetikleyici | Aksiyon | Kapı |
|---|---|---|
| Kod değişikliği | Birim + entegrasyon | TS-G5 |
| Sürüm adayı | Tam ölçüm seti | TS-G6 |
| Donanım değişikliği | Ölçüm tekrarı | TS-G1 |
| Sartname değişikliği | Eşik gözden geçirme | TS-G1 |

Tetikleyici-aksiyon eşleşmesi **plan taslağıdır**, CI bağlantısı yok.

## §35 Hata → kapı akışı

```text
  test başarısız
    → eşiğe bak (TS-G1) → eşik yoksa: karar YOK
    → senaryoya bak (TS-G2) → senaryo yoksa: kapsam bilinmiyor
    → raporla (log) → kapanış
```

İlk iki adım **kapı açık** olduğu için sonuç daima "karar yok"tur.

## §36 Bağımsız doğrulama (TS-G3) — karar seçenekleri

| Seçenek | Artısı | Eksişi |
|---|---|---|
| Dış ürün | Bağımsız | Bütçe/licence `UNKNOWN` |
| Kendi aracı | Entegre | Bağımsızlık zayıf |
| Manuel ölçüm | Ucuz | Tekrarlanamaz |

**Hiçbiri seçilmedi.**

## §37 Ölçüm sıklığı seçenekleri (R6)

| Sıklık | Artısı | Eksişi |
|---|---|---|
| Her derlemede | Erken uyarı | Donanım bağlar |
| Günlük | Denge | Donanım bağlar |
| Sürümde | Ucuz | Geç fark |
| Manuel | Bağımsız | Tekrarlanamaz |

Seçim yok → TS-G6 açık.

## §38 Sonraki tur için gerekenler

1. TS-G1 eşikleri (ürün kararı + datasheet)
2. TS-G2 senaryo listesi (bu dosyanın §9–§10'u doldurur)
3. TS-G3 araç kararı
4. TS-G5 CI bağlantısı (repo'daki `ci.yml` genişlemesi)

## §39 K003 test etiketleme iskeleti

| Etiket | Ne ayırır | Durum |
|---|---|---|
| `birim` | Donanımsız testler | Plan yok |
| `entegrasyon` | Zincir testleri | Plan yok |
| `ölçüm` | Fiziksel ölçüm | TS-G1/TS-G3 |
| `manuel` | İnsan gerektiren | R6 |
| `ci` | Otomatik koşan | TS-G5 |

Etiketler **kategorilerdir**; kaç test taşır → `UNKNOWN`.

## §40 Kapı kapanış kriterleri tablosu

| Kapı | Kapanışta görülecek kanıt |
|---|---|
| TS-G1 | Onaylı eşik tablosu |
| TS-G2 | En az bir dolu senaryo kartı (testing-ornekler §12) |
| TS-G3 | Seçilen araç + ilk bağımsız ölçüm |
| TS-G4 | K003↔K004 sınır kararnamesi |
| TS-G5 | CI logunda K003 test koşusu |
| TS-G6 | Sıklık planı + ilk iki tekrar kaydı |

## §41 Bağımlılık: bu dosyanın K004'e bağımlılığı (tekrar değil, kapı)

TS-G4 kapanmadan K003↔K004 test sınırı **çizilemez**; bu, §22'deki
sınır tablosunun kapı karşılığıdır.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
