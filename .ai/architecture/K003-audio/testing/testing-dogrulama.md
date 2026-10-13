---
title: "testing — Doğrulama"
type: architecture-verification
node_id: testing-dogrulama
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on: []
evidence:
  - "§1 iddia tablosu — 11 satır: 8 doğrulandı, 1 ⚠️, 2 UNKNOWN"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): JUCE resmi dokümantasyon sitesi"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): Apple AudioUnitRender (audiotoolbox)"
validation:
  - "§1–§3 mevcut yapı korundu (append-only); §2 sayım §1 ile birebir"
  - "§4–§12 ek bölümler: test piramidi/coverage matrisi/kapı tasarımı — ölçüm SONUCU içermez"
open_questions:
  - "#9 K003 kabul eşikleri (SNR/THD+N/latency budget) — ⚠️ VERIFICATION REQUIRED"
  - "#10 test senaryosu listesi ve otomasyon kapsamı — UNKNOWN"
  - "#11 ci.yml içeriğinde koşan testler — UNKNOWN"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# testing — Kanıt Tablosu (K003)

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "testing · Test senaryoları ve kabul kriterleri" | `audio-master.md` §3 satır 73 | 2026-10-12 | ✅ dosya okundu |
| 2 | `auval` CLI, bir audio unit'ı plug-in API + temel işlev (kanal konfigürasyonu, instantiate süresi, render yeteneği) üzerinden doğrular | `audio-master.md` §4 A6 (kaynak: Apple AudioUnitDevelopmentFundamentals) | 2026-10-12 | ✅ dosya okundu |
| 3 | QA kalite hedefi: test coverage ≥%80 · flaky test %0 | `.ai/AGENTS.md` §16 Quality Standards | 2026-10-12 | ✅ dosya okundu |
| 4 | Test altyapısı: `shared/tests/` 22 dosya · PHPUnit ^10.5/^11.0 (composer.json require-dev) | `.ai/AGENTS.md` §25.2 Stack Kanıtı | 2026-10-12 | ✅ dosya okundu |
| 5 | Repo'da ses motoru (C/C++) test kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 6 | K004-dsp/testing ayrı bir çocuk daldır (K003 testing ile karıştırılmaz) | v2 §2.4 kaydı · `K004-dsp/testing/index.md` | 2026-10-12 | ✅ dosya okundu |
| 7 | Core Audio pull-model + render döngüsü (test hedefi davranışı) | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 8 | `AudioUnitRender` monoton sampleTime (zamanlama testi referansı) | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | 2026-10-12 | has-been-fetched 2026-10-12 |
| 9 | K003 kabul eşikleri (SNR / THD+N / latency budget) — CoreMusic kararı | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 10 | Test senaryosu listesi ve otomasyon kapsamı | — | 2026-10-12 | **UNKNOWN** (senaryo yok) |
| 11 | `.github/workflows/ci.yml` içinde hangi testlerin koştuğu (içerik doğrulanmadı) | `.github/workflows/` envanteri (2 dosya) | 2026-10-12 | **UNKNOWN** (içerik okunmadı) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 11 |
| Benzersiz URL | 2 (7 · 8) |
| Gerçek dosya yolu / iç kanıt satırı | 6 (1–6) + 1 ölçüm (5 dahil) → 6 |
| ✅ doğrulandı / dosya okundu / ölçüm | 8 (1–6 → has-been-fetched 7–8 dâhil) |
| ⚠️ VERIFICATION REQUIRED | 1 (9) |
| UNKNOWN | 2 (10 · 11) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#9** eşik kararı qa-engineer + embedded-engineer ile kapanır → kapanınca
  `testing-kapsam.md` §2 satır 2 `[TASARIM]` → `[KANIT]` olur.
- **#10** senaryo listesi yazıldığında kapanır; şimdi `UNKNOWN` kalır.
- **#11** ci.yml içeriği gerçekten okunup test adları doğrulanınca
  `✅ dosya okundu` olur — okunmadan "CI'da ses testi koşuyor" denmez.
- **#7–8** bağımsız teyit istenirse URL ancak gerçekten fetch edilince
  `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Test doğrulama stratejisi — test piramidi (K003)

> **Durum:** Bu bölüm test *tasarımı ve kapıları*dır; hiçbir test sonucu/coverage
> değeri burada iddia edilmez. Kardeş dosyalar: [[testing-kapsam]] ·
> [[testing-ornekler]] · [[index]] · [[K000-isletim-sistemi/isletim-sistemi-audio/index]].

### §4.1 Piramit katmanları

| Katman | Kapsam (K003) | Örnek | Çalışma ortamı | Ağırlık (aday) | Durum |
|---|---|---|---|---|---|
| L1 Birim | Saf DSP/matematik fonksiyonları (float32) | ppm hesabı, matris toplama | процесс içi, donanımsız | en yüksek | PLANNED (kod yok — §1 #5) |
| L2 Bileşen | Tek düğüm (mix/route/sync) kendi başına | blok işleme, route matrisi | süreç içi simülasyon | yüksek | PLANNED |
| L3 Entegrasyon | Zincir boyu (input→mix→route→output) | uçtan uca blok akışı | simüle cihaz | orta | PLANNED |
| L4 Gerçek zamanlı | Bütçe/xrun davranışı | 512/48 kHz döngü | gerçek driver (ASIO vb.) | orta | PLANNED (bkz [[realtime-kapsam]]) |
| L5 Platform/API | API sözleşmeleri | `AudioUnitRender` monotonluk (§1 #8) | platform SDK | düşük | ⚠️ SDK doğrulaması gerekli |
| L6 Cihaz/QA | `auval` benzeri doğrulama (§1 #2) | kanal konfigürasyonu, instantiate süresi, render yeteneği | araç | düşük | referans alındı (§1 #2) |
| L7 UI/E2E | CoreMusic arayüz akışı | oynat/kontrol akışı | tarayıcı | düşük | K003 dışı (K010) |

### §4.2 Katman sorumlulukları ve bağımlılıklar

| Katman | Sorumlu | Bağımlı olduğu kanıt | Çıkış |
|---|---|---|---|
| L1 | embedded-engineer + qa-engineer | §1 #9 eşik kararı | birim test listesi |
| L2 | embedded-engineer | `mixing-kapsam` / `routing-kapsam` | düğüm testleri |
| L3 | qa-engineer | §1 #10 senaryo listesi (UNKNOWN) | entegrasyon senaryoları |
| L4 | embedded + performance | [[realtime-kapsam]] bütçesi | xrun/latency raporu |
| L5 | embedded-engineer | platform SDK sürüm kanıtı (**kural 12: JUCE 9 / ASIO SDK 2.3.4 kanıtı yok → VERIFICATION REQUIRED**) | API sözleşme testleri |
| L6 | qa-engineer | §1 #2 (auval tanımı) | doğrulama raporu |
| L7 | qa-engineer (UI) | mockup gate (kök AGENTS §13) | E2E raporu |

### §4.3 Ön koşul tablosu

| # | Ön koşul | Kaynak | Durum | Eksikse |
|---|---|---|---|---|
| P1 | Test coverage ≥ %80, flaky %0 | `.ai/AGENTS.md` §16 (§1 #3) | ✅ hedef tanımlı | — |
| P2 | `shared/tests/` altyapısı (22 dosya, PHPUnit) | §1 #4 | ✅ dosya sayımı | K003 C++ tarafı ayrı |
| P3 | K003 C++ test kodu yok (baseline) | §1 #5 ölçüm | ✅ ölçüm | — |
| P4 | K003 kabul eşikleri (SNR/THD+N/latency) | §1 #9 | ⚠️ VERIFICATION REQUIRED | §8 kapıları kapalı |
| P5 | Senaryo listesi + otomasyon kapsamı | §1 #10 | ❌ UNKNOWN | §6 matrisi “tasarım” kalır |
| P6 | CI'da koşan testlerin listesi | §1 #11 | ❌ UNKNOWN | §13 CI kapısı kapalı |
| P7 | K004-dsp/testing ayrı daldır | §1 #6 | ✅ kayıt | karışıklık yasak |
| P8 | JUCE/ASIO SDK sürüm kanıtı | kural 12 | ⚠️ VERIFICATION REQUIRED | L5 katmanı başlamaz |

## §5 Coverage matrisi ve ölçüm tanımı

### §5.1 Ne sayılır / sayılmaz

| Kural | Tanım | Kaynak |
|---|---|---|
| Sayılır | K003 `*.cpp`/`*.h` dosyalarında çalışan satır/branch (ileride) | hedef |
| Sayılmaz | Test dosyalarının kendisi, üretilmiş kod | standart yaklaşım (aday) |
| Sayılmaz | `#if` dışında kalan ölü kod | inceleme |
| Ayrım | K003 coverage'ı K004-dsp coverage'ı ile birleştirilmez | §1 #6 |
| Yöntem | Henüz araç seçilmedi (K003 C++ test altyapısı yok) | §1 #5 |
| Hedef | ≥ %80 | §1 #3 (`.ai/AGENTS.md` §16) |
| Flaky | %0 | §1 #3 |
| Rapora | Ölçüm yoksa “henüz ölçülmedi” yazılır; %0/varsayım yazılmaz | zero-hallucination |

### §5.2 Aday coverage dağılımı (hedef — ölçüm değil)

| Modül | Ağırlık (kod yüzeyi) | Aday min. coverage | Dayanak | Durum |
|---|---|---|---|---|
| synchronization | orta | ≥ %80 | §5.1 genel hedef | ⚠️ eşik kararı (#9/#10) |
| realtime çekirdeği | yüksek | ≥ %80 + bütçe kapıları | §1 #3 + [[realtime-kapsam]] | ⚠️ |
| mixing | yüksek | ≥ %80 | §1 #3 | ⚠️ |
| routing | orta | ≥ %80 | §1 #3 | ⚠️ |
| diagnostics | düşük | ≥ %80 | §1 #3 | ⚠️ |
| device/output/input (K003 kardeşleri) | orta | ≥ %80 | §1 #3 | ⚠️ |

### §5.3 Coverage hesabı (aritmetik kimlik)

| Değişken | Anlam |
|---|---|
| `K_toplam` | Ölçülen toplam ifade/satır sayısı (rapor çıktısı) |
| `K_tam` | Çalıştırılan ifade/satır sayısı |
| `coverage = (K_tam / K_toplam) × 100%` | aritmetik kimlik |
| `K_toplam = 0` | Ölçüm yok → oran tanımsız; rapor “henüz ölçülmedi” der |
| `coverage ≥ 80` | Kapı (§1 #3); eşik kararı onaylanana kadar **aday** |

## §6 Test senaryo matrisi (TST)

### §6.1 Senaryo matrisi (TST-01 … TST-18)

| ID | Senaryo | Katman | Hazırlık | Beklenen (tasarım) | Sonuç | Kapı |
|---|---|---|---|---|---|---|
| TST-01 | Blok işleme doğruluğu (float32) | L1 | sabit girdi bloğu | Çıktı = referans hesap ±0 (bit-eşitlik tasarımı) | ⬜ | §9 K1 |
| TST-02 | Sıfır girdi → sıfır çıktı (DC yok) | L1 | sıfır bloğu | Çıktı ≈ 0 (taşıma hatası yok) | ⬜ | §9 K2 |
| TST-03 | Birimli girdi (1.0) → taşma davranışı | L1 | tam ölçek | Tanımlı davranış (limiter: §1 mixing #10 ile) | ⬜ | ⚠️ |
| TST-04 | Monoton `sampleTime` (§1 #8) | L5 | render döngüsü | Ardışık fark = blok boyu | ⬜ | [[synchronization-dogrulama]] §8 K2 |
| TST-05 | Pull-model render (§1 #7) | L5 | zincir kur | Çıkış pull ile üretilir | ⬜ | L5 |
| TST-06 | `Reset` davranışı (efekt birimi temizliği) | L2 | decay yükle → reset | Reset sonrası bellek/durum temiz | ⬜ | [[mixing-kapsam]] |
| TST-07 | Route matrisi yazma/okuma | L2 | matris kur | Her hedef kanal beklenen kaynakla eşleşir | ⬜ | [[routing-dogrulama]] |
| TST-08 | Block kenarı yeniden yönlendirme | L2/L4 | rota değiştir | Tıklama yok (tasarım: §1 routing #10 ⚠️) | ⬜ | ⚠️ |
| TST-09 | Xrun enjeksiyonu dayanımı | L4 | bütçe aşımı zorla | Kırılma raporlanır, gizlenmez | ⬜ | [[realtime-dogrulama]] §9 |
| TST-10 | Exclusive Lock (tek uygulama) | L4/L5 | 2. uygulama | İkinci uygulama reddedilir | ⬜ | §9 K6 |
| TST-11 | Cihaz kaybı → fallback | L4 | cihaz çıkar | Tanımlı fallback (Edge Case #6) | ⬜ | §9 K7 |
| TST-12 | DC offset koruma (>0.5 V) | L3/L6 | DC enjeksiyonu | Koruma rölesi kuralı devreye girer (kural 10) | ⬜ | §9 K8 |
| TST-13 | Log maskesi ([REDACTED]) | L2 | hassas veri üret | Maskelenir (`.ai/AGENTS.md` §17 #3) | ⬜ | [[diagnostics-dogrulama]] |
| TST-14 | Log append-only tutarlılığı | L2 | çoklu ekleme | Geçmiş satır değişmez (§25.3 kural 3) | ⬜ | [[diagnostics-kapsam]] |
| TST-15 | Parametre güncellemesi atomiği | L2 | eşzamanlı güncelle | Gözlenen parametre tutarlı (tasarım) | ⬜ | ⚠️ |
| TST-16 | Uzun süre dayanıklılık | L4 | kesintisiz koşu | Birikimli hata/kayıp yok (süre: UNKNOWN) | ⬜ | ⚠️ |
| TST-17 | `auval` benzeri API doğrulaması (§1 #2) | L6 | kanal konfigürasyonu + instantiate + render | Araç geçer | ⬜ | §6.2 |
| TST-18 | Coverage kapısı raporu | L1–L4 | ölçüm çalıştır | ≥ %80 (§5.3) veya rapor yok | ⬜ | §9 K5 |

### §6.2 Zamanlama/timing test matrisi (TIM)

| ID | Test | Metrik | Beklenen (tasarım) | Ölçüm | Kapı |
|---|---|---|---|---|---|
| TIM-01 | Blok süresi hesabı | 512/48000 | 10,666… ms (aritmetik) | ⬜ | ✅ hesap |
| TIM-02 | Callback süresi bütçesi | callback ms | ≤ blok süresi (bkz [[realtime-kapsam]]) | ⬜ | ⚠️ bütçe UNKNOWN (#10 realtime) |
| TIM-03 | Instantiate süresi (§1 #2 auval kapsamı) | ms | Eşik yok — kayıt tutulur | ⬜ | ❌ UNKNOWN |
| TIM-04 | Parametre geçiş süresi | blok sayısı | Eşik yok — tasarım yok | ⬜ | ❌ UNKNOWN |
| TIM-05 | Jitter (blok arası sapma) | ms | Bütçeye bağlı | ⬜ | ⚠️ |
| TIM-06 | Kuyruk gecikmesi | blok | Bütçeye bağlı | ⬜ | ⚠️ |

## §7 Flaky test kapısı ve stabilizasyon

### §7.1 Flaky tespit kriterleri

| # | Kriter | Tanım | Aksiyon |
|---|---|---|---|
| F1 | Aynı commit, 2 koşuda farklı sonuç | sonuç imzası değişir | flaky işaretle |
| F2 | Zaman/yük duyarlılık | yalnız yük altında değişen sonuç | izolasyon → L4 |
| F3 | Sıra bağımlılığı | tek başına geçer, birlikte geçmez | bağımlılığı kaldır |
| F4 | Ortam bağımlılığı | yalnız ortam X'te geçen test | ortam etiketi ekle |
| F5 | Yan etkili test | başka testin durumunu bozar | izole et |
| F6 | Kararlılık hedefi | flaky = %0 | §1 #3 (`.ai/AGENTS.md` §16) |

### §7.2 Stabilizasyon protokolü

| Adım | İşlem | Çıktı |
|---|---|---|
| 1 | Flaky işaretle (F1–F5 sınıflandırması) | etiket |
| 2 | Tekrar koşu sayısı belirle — **sayı kararı yok (UNKNOWN, uydurulmaz)** | — |
| 3 | Kaynağı izole et (L4'e taşı / saati enjekte et) | düzeltme |
| 4 | Yeniden koş → kararlı | kapalı kayıt |
| 5 | Kararlı değilse **kaldır/kalıcı işaretle** — sessizce “geçti” sayılmaz | rapor |

## §8 Eşik/kabul tabloları (aday — §1 #9 kararı bekleniyor)

| Eşik | Aday değer | Dayanak | Durum |
|---|---|---|---|
| SNR | Vault mühendislik hedefi: >100 dB | `~/.config/opencode/AGENTS.md` §5 (Audio & Electronics) | hedef var; **ölçüm yok** |
| THD+N | Vault hedefi: <%0.01 @1 kHz | aynı kaynak | hedef var; **ölçüm yok** |
| Coverage | ≥ %80 | §1 #3 | ✅ hedef |
| Flaky | %0 | §1 #3 | ✅ hedef |
| Latency bütçesi | — | §1 #9 | ❌ UNKNOWN (uydurulmaz) |
| Callback süresi | ≤ blok süresi (10,666… ms) | aritmetik + sartname | ⚠️ kesin bütçe kararı yok |
| DC offset koruma eşiği | 0.5 V | kural 10 | ✅ kural |
| Buffer/f_s | 512 örnek / 48 kHz | sartname §8 | ✅ kural |
| Xrun kabulü | — | [[realtime-kapsam]] kararı | ❌ UNKNOWN |

## §9 Geçme / kalma kriterleri (PASS–FAIL)

| # | Kriter | Ölçüm | PASS | FAIL |
|---|---|---|---|---|
| K1 | Birim doğruluk | TST-01 | beklenen ≤0 sapma | FAIL → regresyon |
| K2 | Sıfır DC | TST-02 | ≈0 | FAIL → diagnostics'a raporla |
| K3 | Zamanlama kimliği | TIM-01 | 10,666… ms | — (aritmetik) |
| K4 | Platform monotonluk | TST-04 | fark = blok boyu | sync kapısına git |
| K5 | Coverage | §5.3 | ≥ %80 | < %80 → L1–L3 genişlet |
| K6 | Tek uygulama | TST-10 | 2. uygulama reddi | FAIL → kural 9 ihlali |
| K7 | Cihaz kaybı fallback | TST-11 | tanımlı fallback | FAIL → Edge Case #6 |
| K8 | DC koruma | TST-12 | röle kuralı devrede | FAIL → güvenlik eskalasyonu |
| K9 | Flaky | §7.1 | %0 | işaretli test sayılmaz |
| K10 | CI koşusu | §13 | ci.yml içeriği okundu & test adları doğrulanmadan “CI geçti” denmez | UNKNOWN → kapı kapalı |
| K11 | Eşik kapısı | §8 | karar varsa uygula | karar yoksa **KAPALI-KAPI** |
| K12 | Dürüstlük | — | sonuç satırında koşul+tarih | yoksa sayılmaz |

## §10 Hata enjeksiyon testleri (TFAULT)

| ID | Enjeksiyon | Beklenen dayanım | Tespit | Sonuç alanı |
|---|---|---|---|---|
| TF-01 | Bozuk blok boyu (0 / 512 dışı) | reddedilir | doğrulama | TST-01 kapısı |
| TF-02 | NaN/Inf girdi | tanımlı davranış (yutma/kesme) | denetim | ⚠️ tasarım yok |
| TF-03 | Tam ölçek üstü girdi (+6 dB) | taşma davranışı tanımlı | clip sayacı | [[mixing-dogrulama]] §8 |
| TF-04 | Cihaz kaybı | fallback (Edge Case #6) | olay | TST-11 |
| TF-05 | İki uygulama | kilit reddi | kilit hatası | TST-10 |
| TF-06 | Kuyruk taşması | kayıp raporlanır | sayaç | [[realtime-dogrulama]] |
| TF-07 | Disk dolu (log) | log yazımı çökmez, uyarı verir | diagnostics | [[diagnostics-dogrulama]] §9 |
| TF-08 | Hassas veri sızıntısı | [REDACTED] maskesi | log taraması | `.ai/AGENTS.md` §17 #3 |
| TF-09 | Route döngüsü (feedback) | döngü reddi/tespiti | graf denetimi | [[routing-dogrulama]] §5 |
| TF-10 | Yanlış sample rate dayatma | reddedilir | yapılandırma | TST-01/TIM-01 |
| TF-11 | Eşik kararı olmadan “geçti” iddiası | kapı kapalı yazılır | rapor denetimi | K11 |
| TF-12 | Test saati manipülasyonu | flaky işlenir | §7.1 | K9 |

## §11 Regresyon kapıları

| Kapı | Tetikleyici | Zorunlu test | Kapatma |
|---|---|---|---|
| RT-1 | K003 kodu eklendiğinde (baseline: yok) | L1 + L2 senaryoları | tüm zorunlu kapılar PASS/KAPALI işaretli |
| RT-2 | Eşik kararı (#9) güncellendi | §8 + TST-18 | karar vault'a işlendi |
| RT-3 | CI içeriği okundu (#11) | §13 kapı tasarımı | test adları doğrulandı |
| RT-4 | K004-dsp değişikliği | K003/K004 sınır testleri (§1 #6) | sınır testi PASS |
| RT-5 | SDK/sürüm değişikliği (kural 12) | L5 katmanı | sürüm kanıtı eklendi |
| RT-6 | Platform geçişi (per-OS player FROZEN — kural 11) | L5 + L6 | ADR-019'a dokunulmadı |
| RT-7 | Flaky patlaması | §7 protokolü | flaky = %0 |
| RT-8 | Coverage düşüşü | §5.3 | ≥ %80 veya açıklama |

## §12 Bağlantılar ve kaynak denetimi

### §12.1 Bağlantılar

| Bağlantı | Dosya | İlişki |
|---|---|---|
| Kardeş kapsam | [[testing-kapsam]] | #9 eşik kararı burada `[TASARIM]` → `[KANIT]` yapar |
| Kardeş örnekler | [[testing-ornekler]] | Senaryo örnekleri burada somutlaşır |
| Dizin | [[index]] | Düğüm özeti |
| OS ses katmanı | [[K000-isletim-sistemi/isletim-sistemi-audio/index]] | Platform API/cihaz davranışı kaynağı |
| K004 testi | K004-dsp/testing (§1 #6) | Karıştırma yasak |
| Diğer doğrulamalar | [[synchronization-dogrulama]] · [[realtime-dogrulama]] · [[mixing-dogrulama]] · [[routing-dogrulama]] · [[diagnostics-dogrulama]] | Kapı bağımlılıkları |

### §12.2 Kaynak denetim kaydı (2026-10-13)

> `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
> Tam URL eklenen bölümlere yazılmaz (0 yeni URL); §1'deki URL'ler denetimin girdisidir.

| # | Kaynak | HEAD | Not |
|---|---|---|---|
| 1 | JUCE resmi dokümantasyon sitesi | **HTTP 200** | Kural 12: JUCE 9 sürüm kanıtı hâlâ yok — site erişilebilir ≠ sürüm kanıtı → **⚠️ VERIFICATION REQUIRED** |
| 2 | JUCE depo doküman klasörü (GitHub) | **HTTP 200** | Kaynak kod/örnek erişilebilir |
| 3 | Apple Developer Documentation — AudioUnitRender (AudioToolbox) | **HTTP 200** | §1 #8 karşılığı (güncel alan: AudioToolbox) |
| 4 | §1'deki eski Apple numeric URL | **HTTP 404** | §1 satırı yeniden değerlendirilmeli (sessiz düzeltme yasak — yalnız burada not) |
| 5 | Microsoft Learn — Core Audio programming guide | **HTTP 200** | Platform doğrulama referansı |
| 6 | `aes.org` | **HTTP 403** | Doğrulanamadı → kullanılmadı |

## §13 CI kapı tasarımı (ci.yml içeriği OKUNMADI — §1 #11 UNKNOWN)

> **Kural:** `.github/workflows/ci.yml` içeriği bu dosyada **okunmamıştır**; “CI'da
> ses testi koşuyor” denemez (§3 geçiş kuralı). Aşağıdaki tablo yalnızca **tasarım**tır.

| Kapı | Aşama (aday) | Koşul | Bilinmeyen |
|---|---|---|---|
| CI-1 | Derleme | K003 hedefi derlenir (henüz hedef yok — §1 #5) | hangi hedefler |
| CI-2 | Birim test | L1 senaryoları koşar | gerçek test adları (§1 #11) |
| CI-3 | Statik denetim | kural 1–5 (zero-allocation, lock-free, noexcept, alignas, float) | araç seçimi |
| CI-4 | Coverage | ≥ %80 kapısı (§5.3) | kapsam aracı |
| CI-5 | Flaky denetimi | %0 (§7.1) | tekrar sayısı |
| CI-6 | Sürüm kanıtı | JUCE/ASIO SDK sürümü (kural 12) | kanıt yok → ⚠️ |
| CI-7 | Gizli veri | GitLeaks benzeri tarama (`.ai/AGENTS.md` §25.2) | çalışma durumu doğrulanmadı |
| CI-8 | Kapı raporu | Her aşamanın sonucu okunur | içeriği okumadan “geçti” denmez |

**Kapanış:** §1 #11 yalnız ci.yml içeriği gerçekten okunup test adları
doğrulanınca `✅ dosya okundu` olur (§3 kuralı ile aynı).

## §14 Test oturumu rapor şeması (boş form)

### §14.1 Oturum künyesi

| Alan | Değer |
|---|---|
| `run_id` | *(boş)* |
| Tarih / commit | *(boş)* |
| Katman (L1–L7) | *(boş)* |
| Ortam (OS/sürücü/cihaz) | *(boş)* |
| JUCE / ASIO SDK sürümü | *(boş — kural 12: kanıt gerekli)* |
| Test adedi / geçen / kalan | *(boş)* |
| Coverage | *(boş — henüz ölçülmedi)* |
| Flaky | *(boş)* |

### §14.2 Sonuç satırları

| TST ID | Katman | Sonuç | Süre | Not |
|---|---|---|---|---|
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |
| *(boş)* | *(boş)* | *(boş)* | *(boş)* | *(boş)* |

### §14.3 Kapı özeti

| Kapı | Durum | Kanıt |
|---|---|---|
| K1–K12 (§9) | *(boş)* | *(boş)* |
| Coverage ≥ %80 | *(boş — ölçülmedi)* | *(boş)* |
| Flaky %0 | *(boş)* | *(boş)* |
| CI koşusu | *(boş — içerik okunmadı)* | *(boş)* |

**Boş form kuralı:** boş hücre = ölçülmedi; “geçti” tahmini yazılamaz.

## §15 Açık soru → kapanış haritası

| Soru (§1) | Kapanış koşulu | Ne değişir | Sorumlu | Kapı |
|---|---|---|---|---|
| #9 kabul eşikleri | qa + embedded kararı | `testing-kapsam` §2 `[TASARIM]` → `[KANIT]` | qa-engineer | §8 |
| #10 senaryo listesi | Liste yazılır | §6 “tasarım” → “kapsam” | qa-engineer | §6 |
| #11 ci.yml içeriği | Dosya okunur + test adları | §13 → doğrulanmış | devops + qa | CI-2 |
| Kural 12 (JUCE 9 / ASIO SDK 2.3.4) | Sürüm kanıtı | L5 katmanı açılır | embedded-engineer | §4.2 L5 |
| §12 satır 4 (eski Apple URL 404) | Vault URL denetimi | §1 satırı yeniden değerlendirilir | vault steward | — |

## §16 Risk ve belirsizlik defteri

| # | Risk | Etki | Olasılık | Azaltma | Durum |
|---|---|---|---|---|---|
| RSK-1 | Eşik kararı yok → kapılar kapalı (#9) | Yüksek | Kesin | Karar kapısı | Açık |
| RSK-2 | Senaryo listesi yok (#10) | Yüksek | Kesin | §6 tasarımını onaya taşı | Açık |
| RSK-3 | CI içeriği bilinmiyor (#11) | Orta | Kesin | Dosyayı okuma görevi | Açık |
| RSK-4 | C++ test altyapısı yok (#5) | Yüksek | Kesin (baseline) | Altyapı kurulumu (onaylı) | Açık |
| RSK-5 | Sürüm kanıtsız bağımlılık (kural 12) | Orta | Kesin | Sürüm kanıtı toplama | Açık |
| RSK-6 | K003/K004 test karışıklığı (#6) | Orta | Orta | Dal ayrımı (§1 #6) | Açık |
| RSK-7 | “%80 geçti” iddiası ölçüm olmadan | Yüksek | İnsan kaynaklı | K5 + boş form kuralı | Kalıcı kontrol |
| RSK-8 | Platform API sürüm değişimi (auval vs yeni SDK) | Orta | Belirsiz | L5 kapısı + sürüm kanıtı | Açık |

## §17 Test araçları envanteri (PLANNED)

| # | Araç (aday) | Katman | Kaynak | Durum |
|---|---|---|---|---|
| T1 | C++ test çerçevesi (seçilmedi) | L1–L3 | — | ❌ UNKNOWN (araç seçimi yok) |
| T2 | `auval` (Apple aracı, §1 #2) | L6 | §1 #2 | ✅ referans alındı |
| T3 | JUCE test yardımcıları | L1/L5 | JUCE dokümantasyonu (HEAD 200) | ⚠️ sürüm kanıtı yok (kural 12) |
| T4 | Coverage aracı (C++) | L1–L3 | — | ❌ UNKNOWN |
| T5 | CI iş akışı | tümü | `.github/workflows/` (2 dosya, içerik okunmadı) | ❌ UNKNOWN (#11) |
| T6 | PHPUnit/Vitest/Playwright | web UI (K003 dışı) | `.ai/AGENTS.md` §25.2 | ✅ mevcut (PHP testleri) |
| T7 | Realtime ölçüm (xrun/latency) | L4 | [[realtime-kapsam]] | ⚠️ PLANNED |
| T8 | Gate scriptleri (repo kökünden) | hepsi | `.ai/scripts/*.ps1` (§13.6 kök kuralı) | ✅ kural var |

## §18 Denetim listesi (bu dosyanın kendi kalite kapısı)

- [ ] §1–§3 metni değişmedi (append-only)
- [ ] Frontmatter 8 zorunlu alan + `node_id` = `testing-dogrulama` + `group: kanit` + `parent_id`
- [ ] Ek bölümlerde yeni tam URL yok
- [ ] Ek bölümlerde ölçüm sonucu yok (yalnız hedef/aritmetik/plan)
- [ ] #9/#10/#11 işaretleri §1 ile tutarlı (⚠️/UNKNOWN korundu)
- [ ] K003 vs K004-dsp/test ayrımı korundu (§1 #6)
- [ ] Coverage/flaky hedefleri “hedef” etiketli, “başarıldı” değil
- [ ] Wiki-linkler eklendi (kardeşler + K000)
- [ ] Dürüstlük bloğu + tek Authority satırı en sonda
- [ ] git commit atılmadı

## §19 Terimler

| Terim | Tanım |
|---|---|
| **Test piramidi** | L1→L7 katman modeli (§4.1) |
| **KAPALI-KAPI** | Kriter kararı verilmemiş kapı (§9 K11) |
| **Flaky** | Kararsız test; hedef %0 (§7.1) |
| **Aday eşik** | Onay bekleyen eşik; ölçüm sonucu değil (§8) |
| **TFAULT** | Test hata enjeksiyonu kimliği (§10) |
| **Boş form** | Ölçüm yokluğunu taşıyan tablo (§14) |
| **Sürüm kanıtı** | Kural 12 gereği JUCE/ASIO SDK sürüm belgesi |
| **Katman ayrımı** | K003 testi ile K004-dsp testinin karıştırılmaması (§1 #6) |

## §20 Ek kayıtlar

| Tarih | Kayıt | Yapan |
|---|---|---|
| 2026-10-12 | §1–§3 kanıt tablosu + sayım + geçiş kuralları | Vault Steward |
| 2026-10-13 | Frontmatter derinleştirme + §4–§20 eklendi (append-only) | Vault Steward (subagent) |
| 2026-10-13 | Kaynak HEAD denetimi (§12.2) — JUCE/Apple/Microsoft 200, eski Apple URL 404 not edildi | Vault Steward (subagent) |

## §21 Kuru çalıştırma (prova) — test OLMADAN

| Adım | İşlem | Doğrulanan | Doğrulanmayan |
|---|---|---|---|
| DR1 | §14 formlarını boş doldur | Form bütünlüğü | — |
| DR2 | §6 satırlarını “⬜” ile çalıştır | Matris okunabilirliği | gerçek sonuç |
| DR3 | §9 kapılarını bilinmeyen veriye uygula | K11 “KAPALI-KAPI” davranışı | PASS/FAIL |
| DR4 | §13 CI tablosunu boş içerikle | Tasarım tutarlılığı | “CI geçti” iddiası |
| DR5 | Prova izini temizle | Dosya boş forma döner | — |

## §22 Kod inceleme kapıları (repo kodu yazıldığında)

| # | İnceleme | Kural kaynağı | Kabul |
|---|---|---|---|
| CR1 | Testler gerçek davranışı sınar (boş assertion yok) | qa standardı | inceleme |
| CR2 | Testlerde tahmini eşik yok (yalnız onaylı) | §8 | denetim |
| CR3 | K003 testleri K004 ile karışmıyor | §1 #6 | dosya yolu denetimi |
| CR4 | Coverage ölçümü yolu tanımlı | §5.1 | — |
| CR5 | Zamanlayıcı/yük testleri L4'te etiketli | §4.1 | — |
| CR6 | Sürüm sabitleri kodda uydurulmamış (kural 12) | kural 12 | — |
| CR7 | Yeni iddialar §1'e işlenmeden “doğrulandı” yok | §3 | — |

## §23 Sık sorular

| # | Soru | Cevap | Ayrıntı |
|---|---|---|---|
| Q1 | “Coverage şu an kaç?” | Ölçülmedi — K003 kodu yok | §1 #5 |
| Q2 | “CI testleri geçiyor mu?” | Bilinmiyor | §1 #11 — ci.yml okunmadı |
| Q3 | “SNR/THD+N ölçülüyor mu?” | Hayır — hedef var, ölçüm yok | §8 |
| Q4 | “auval ile doğrulanıyor mu?” | Referans alındı; çalıştırma kanıtı yok | §1 #2 |
| Q5 | “JUCE 9 kullanılıyor mu?” | Sürüm kanıtı yok | kural 12 |
| Q6 | “K004 testleri buraya girer mi?” | Hayır | §1 #6 |
| Q7 | “Eşikler ne zaman açılır?” | #9 kararıyla | §15 |
| Q8 | “Commit atıldı mı?” | Hayır | kural 8 |

## §24 Kabul testi şablonu (senaryo kayıt kuralı)

| Alan | Zorunlu mu | Açıklama |
|---|---|---|
| Senaryo adı | Evet | TST/TIM/TF kimliği ile |
| Katman | Evet | L1–L7 (§4.1) |
| Ön koşul | Evet | §4.3 referansı |
| Adımlar | Evet | Tekrarlanabilir, sıralı |
| Beklenen | Evet | Tasarım beklentisi (kaynaklı) |
| Gerçek sonuç | Hayır (boş kalabilir) | Boş = ölçülmedi |
| Kapı | Evet | §9 karşılığı |
| Tarih/koşul | Sonuç varsa zorunlu | Yoksa sonuç sayılmaz |
| Ekran/ölçüm kanıtı | Sonuç varsa zorunlu | dosya/rapor referansı |

**Kayıt kuralı:** Beklenen sütununda kaynak, gerçek sonuç sütununda kanıt yoksa
satır “TASARIM” etiketlidir ve coverage kapısına (K5) sayılmaz.

## §25 Kapsam dışı beyanı (bu dosyanın dürüst sınırı)

| Kapsam dışı | Neden |
|---|---|
| K004-dsp testleri | §1 #6 — ayrı dal |
| Web/PHP testleri (PHPUnit 22 dosya) | K003 C++ ses motoru değil (§1 #4) |
| “CI geçti” iddiası | §1 #11 — içerik okunmadı |
| Coverage değeri iddiası | §1 #5 — K003 kodu yok |
| JUCE/ASIO SDK sürümü | kural 12 — kanıt yok |
| SNR/THD+N/latency ölçümü | §1 #9 — ⚠️ |
| Per-OS player davranışı | ADR-019 FROZEN (kural 11) |
| Firmware testleri | K005 alanı |

## §26 Test verisi ve sahte veri kuralı

| Kural | İzin | Açıklama |
|---|---|---|
| Sabit vektör (float32) | ✅ | L1 doğruluk testi; kaynağı tanım (ADR-017) |
| Sine/square test sinyali | ✅ | Üretim tanımı gerekli (frekans: kaynaklı olmalı) |
| Rastgele gürültü | ✅ koşullu | Tohum (seed) kaydedilmezse tekrarlanamaz → seed zorunlu |
| Gerçek müzik dosyası | ⚠️ | Lisans/kanıt sorunu; test verisi olarak sayılmaz |
| Sahte/uydurma ölçüm sonucu | ❌ | Zero-hallucination (§3 kuralı) |
| Tahmini coverage değeri | ❌ | §5.1: ölçüm yoksa “henüz ölçülmedi” |
| Bozuk veri (NaN, taşma) | ✅ | Yalnız §10 TFAULT enjeksiyonunda, etiketli |
| Kayıt şeması | ✅ | §14.2 alanları zorunlu |

## §27 Kapı matrisi özeti (tek bakışta)

| Kapı | Kaynak | Açık mı? | Bağımlı soru |
|---|---|---|---|
| K1 doğruluk | §9 | Evet (tasarım) | kod yok (#5) |
| K2 DC | §9 | Evet | — |
| K3 blok süresi | §9 | Evet (aritmetik) | — |
| K4 monotonluk | §9 | Evet | platform testi L5 |
| K5 coverage | §9 | **Kapalı** | ölçüm yok |
| K6 tek uygulama | §9 | Evet | — |
| K7 fallback | §9 | Evet | — |
| K8 DC koruma | §9 | Evet (kural 10) | — |
| K9 flaky | §9 | **Kapalı** | koşu yok |
| K10 CI | §9 | **Kapalı** | #11 |
| K11 eşik | §9 | **Kapalı** | #9 |
| K12 dürüstlük | §9 | Evet | — |
| RT-1 regresyon | §11 | **Kapalı** | kod yok |
| RT-5 sürüm | §11 | **Kapalı** | kural 12 |

**Özet kuralı:** Kapalı kapı sayısı raporlanır; “tümü geçti” yalnızca tek tek
açık kapılar PASS olduğunda yazılabilir.

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 yeni URL,
> 0 uydurma ölçüm içerir; doğrulanmış kaynaklar yalnız belirtilen URL'lerdir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
