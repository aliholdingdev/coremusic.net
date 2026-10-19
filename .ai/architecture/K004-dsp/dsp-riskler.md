---
node_id: K004-dsp/dsp-riskler
node_type: standard
status: draft
parent_id: K004-dsp
children: []
depends_on: [K003-audio, K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence:
  - "K004-dsp/dsp-master.md §B.7 (R1-R7 risk matrisi) · 2026-10-13"
  - "K004-dsp/dsp-master.md §C.7-C.8 (0 kod kanıtı / FM/dual-boot) · 2026-10-13"
  - "K002-electroncis/elektronik-master.md §3 risk R2 (PMC PSU × DSP zincir) · 2026-10-13"
validation: draft — kanıt zinciri master §B.7/§C.7-C.8'e dayanır; sahip/tahmin alanları TASARIM etiketli, red-team review bekliyor
open_questions:
  - "⚠️ R2 altındaki latency tahminleri (4096-sample ≈ 85ms @48k) basit bölmedir — tasarım bütçesi T8 kapsamında UNKNOWN'dır"
  - "⚠️ R6 Signalsmith sürüm/commit bilinmiyor (UNKNOWN) — pin öncesi kilitlenmeli"
  - "⚠️ Sahip sütunu varsayımsal rol atamasıdır ( TASARIM ) — ekip teyidi gerekir"
group: kanit
---

# DSP — Riskler (K004-dsp)

> **Bu dosya nedir?** DSP zincirinin (host C++/JUCE + XMOS + I2S/TDM) ürettiği risklerin
> olasılık × etki × azaltma × saip matrisi. `dsp-master.md` §B.7'nin (R1-R7) K004 kök
> standardına taşınmış ve K004'e özgü üç riskle (R8-R10) genişletilmiş hâlidir.

## 1. ELI15 — Risk Dosyası Neden Vardır?

Ses yazılımı çökünce "müzik kesildi" olur; ama asıl tehlike **sessiz** olanlardır:
yanlış yazılmış bir sayı, lisansı belirsiz bir kütüphane, kararı verilmemiş bir gecikme
bütçesi… Bu dosya bu tuzakları önceden yazar. Her satır dört soru sorar: **ne sıklıkla
olur (Olasılık)?** **Olursa ne kadar yakar (Etki)?** **Önlemi ne (Azaltma)?**
**Kim uygular (Sahip)?** `⚠️` işareti bilinmeyeni, `[TASARIM]` işareti henüz
kanıtlanmamış tahmini gösterir — ikisi de gerçeğin yerine geçmez.

## 2. Risk Matrisi — §B.7 Taşınan Riskler (R1-R7)

| ID | Risk (master §B.7) | Olasılık | Etki | Azaltma | Sahip | Kanıt |
|----|--------------------|----------|------|---------|-------|-------|
| R1 | C++/DSP kanıtı repo'da 0 satır — komponent yığını kâğıt üstünde (Q1-Q4 açık) | **Yüksek** (gözlenen) | Kritik — planlanan işin temeli gerçek değil | Q1-Q4'ü kapat; her bileşen için kaba prototip önce; MVP'yi "en küçük gerçek DSP" ile başlat | Mimari (K004 kök) | `dsp-master.md` §C.7 · `grep -r Cpp`: 0 isabet |
| R2 | latency budget verisi yok (T8); ADR-038 H001 "host mix 100ms" nihai sayı değil | **Yüksek** | Yüksek — ADR-017'nin "instrument <=3.3ms" hedefini **ettiremez** (4096-sample ≈ 85ms, 100ms ≈ 4608-sample) | buffer/blok ölçüm kampanyası; bütçeyi yaz; H001 ile çelişkiyi kapat; ASIO/WASAPI seçimi | DSP + performance-engineer | `dsp-kararlar.md` §5 T8 · ADR-038 §H001 |
| R3 | DSPark karar bekliyor (90+ yıldız umut verici, 2★ bakımsız) — Q1 | Orta | Orta | Bench prototip + lisans taraması; eşik: >100 test, >50 kullanıcı, MIT/BSD | DSP (Q1 sahibi) | `dsp-master.md` §B.2 Q1 |
| R4 | Signalsmith MIT lisansı doğrulandı ama sürüm/commit **bilinmiyor** (UNKNOWN) | Orta | Orta — sürüm bilinmeden üretim kilidi kırılgan | `vcpkg/conan` ile pinle; vendor değil submodule; sürümü dokümante et | DSP + dependency-manager | `dsp-kararlar.md` §5 T9 · `dsp-kararlar.md` §6 |
| R5 | Dynamics hâlâ belirsiz: kompresör/limiter dahil mi — Q3 | Orta | Orta — loudness (EBU R128) için limiter **zorunlu** | Q3'ü kapat; LUFS-14 hedefi ile birlikte karar ver | DSP (Q3 sahibi) | `dsp-master.md` §B.2 Q3 |
| R6 | Ölçüm stratejisi eksik — Q4 | Orta | Yüksek — kapılar (SNR/THD) **ölçülmeden geçilemez** | `dsp-test-stratejisi.md` uygulanır: kalibrasyonlu MIC + EBU R128 + Linux sweep | DSP + qa-engineer | `dsp-master.md` §B.2 Q4 |
| R7 | Sürüm iddiaları doğrulanmadı: JUCE 9 · ASIO 2.3.4 · compilers (`⚠️ VERIFICATION REQUIRED`) | Orta | Orta — yanlış sürümle yazılmış kod derlenmez/çalışmaz | Sürümleri resmi kaynaktan doğrula; `CMakeLists.txt`'de sürümleri sabitle | DSP + dependency-manager | `dsp-kararlar.md` §6 |

## 3. Risk Matrisi — K004'e Özgü Genişletme (R8-R10)

| ID | Risk | Olasılık | Etki | Azaltma | Sahip | Kanıt |
|----|------|----------|------|---------|-------|-------|
| R8 | Lisans atıf riski: `DSP-Zone`/`SoundTokenizer` MIT atfı yalnız `NOTICE`'ta (master §B.7 R7 taşıması) | Düşük-Orta | Orta — ticari dağıtımda atıf ihlali | CI'da `reuse-lint`/`licensechecker`; `NOTICE`'ı paketle; SPDX header'ları kod-ornekleri şablonundan | dependency-manager + DSP | `dsp-master.md` §B.7 R7 |
| R9 | FM doğrulanmadan kopyalama: `AIFM/FM8/*` dekontları referans kabul edilirse yanlış davranış tekrarlanır | Orta | Orta | Dekontları **yalnız referans** say; davranış eşitliğini N=16 testiyle doğrula (test-stratejisi §6) | DSP + qa-engineer | `dsp-master.md` §C.8-C.9 |
| R10 | Q5 KAPANDI ama uygulama geride: host C++/JUCE zinciri yazılmadı; XMOS I2S/TDM profili kâğıtta | **Yüksek** (Q5 kararı 2026-10-15, kod 0) | Kritik — ADR-017 kararı uygulanmadan K005'e geçiş erken olur | M1-M4 yol haritası sırasıyla; her milette "çalışan最小 kanıt" kapısı | DSP + embedded-engineer | `dsp-kararlar.md` §2 Q5 (ADR-017) · §C.7 |

## 4. İlişkili Dış Riskler (K002/K005 köprüsü)

| ID | Risk (kaynak K-faş) | İlişki | Azaltma (K004 payı) |
|----|----------------------|--------|---------------------|
| R-ext-PSU | PMC/Class-AB PSU giriş 5V DC ≤2A — regülatör olayı (K002 elektronik R2) | DSP zinciri bu 5V'dan beslenir; olayında ses kesilir | K005 boot/`watchdog` ile PSU-olay-koşulu; K004 tarafında ölçüm kapıları (ripple/DC offset testleri) |
| R-ext-FW | K005 firmware çıpası: `HAL_I2S_*` tek kapı, lock-free, jitter ≤150µs (K005 özet) | DSP zamanlaması I2S/TDM'e bağlı — jitter ses tıkırtısı üretir | ADR-017 sınırı: DSP zinciri host'ta, XMOS yalnız hard-RT zamanlama — jitter bütçesini `dsp-test-stratejisi` §7 ile ölç |
| R-ext-OS | T8/latency verisi K000 os-riskler dosyasıyla paylaşılan boşluk | Bütçe yoksa OS/uygulama katmanı da ölçemez | K000 `os-riskler.md` ile aynı T8 kaydı; iki dosya da `UNKNOWN` der |

## 5. Bilinmeyenler (UNKNOWN / ⚠️) — Sayı Uydurmak Yasak

| # | Bilinmeyen | Durum | Kapatma Yolu |
|---|-----------|-------|--------------|
| 1 | **T8 gecikme bütçesi** (hedef ms: canlı-≤20 / enstrüman ≤3.3 / kayıt-yazma ≤5) | UNKNOWN | Buffer/blok ölçüm kampanyası → `dsp-test-stratejisi` §7 |
| 2 | Signalsmith sürüm/commit hash | UNKNOWN | Pin kararı T9 |
| 3 | JUCE 9 / ASIO SDK 2.3.4 gerçek sürümleri | ⚠️ VERIFICATION REQUIRED | Resmi sürüm sayfası doğrulaması |
| 4 | Kompresör/limiter kapsamı (Q3) · ölçüm seti (Q4) | Açık soru | Q3/Q4 kapatma kararları |
| 5 | R1-R10 sahipleri (rol atamaları) | [TASARIM] | Ekip teyidi |

## 6. Kabul Kriterleri (Risk Kapıları)

1. **R1 kapanır** — C++/DSP kodu repo'da ilk kez derlenen/çalışan commit ile (kanıt: CI log).
2. **R2 kapanır** — T8 bütçesi sayısal olarak yazılır ve enstrüman yolu ile ölçülür.
3. **R6 kapanır** — `dsp-test-stratejisi.md` §8'deki kapıların **hepsi** ölçüm verisiyle geçilir (SNR >100dB · THD+N <0.01%@1kHz · LUFS-14).
4. **R10 kapanır** — M4'e kadar ADR-017 host zinciri çalışır kanıt (M1 kuyruk → M4 kapalı döngü).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
