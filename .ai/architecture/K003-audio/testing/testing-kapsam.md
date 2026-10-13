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

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
