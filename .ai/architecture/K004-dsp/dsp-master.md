---
node_id: K004-dsp/dsp-master
node_type: subdomain
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K004-dsp
evidence:
  - "v2 §2.4 mini-ağaç (2026-10-12 okundu)"
  - "doğrulanmış web bulguları — bu tur 2026-10-13 (§B.4 tablosu)"
  - "repo envanteri — disk okuması 2026-10-13 (§C.7)"
validation: "D-kriterleri §D.2; iddia·URL·2026-10-13·durum satırları §B.4 ve dsp-dogrulama.md §A"
open_questions:
  - "Q1 DSPark '90+' ↔ 2★ çelişkisi"
  - "Q2 Signalsmith MIT lisans teyidi"
  - "Q3 dynamics ↔ compressor/limiter hiyerarşisi"
  - "Q4 measurement ↔ testing ayrımı"
  - "Q5 DSP hedefi host mu, hardware mi"
group: mimari
---

# dsp-master — K004 DSP Anayasası (Kök · §A-§D)

> **Node:** `K004-dsp/dsp-master` · **Type:** subdomain · **Status:** draft · **Group:** mimari
> **Üst düğüm:** `K004-dsp` (domain) · **Bağımlı:** `K004-dsp` (domain)
> **Disk durumu:** `K004-dsp` düğümü **PLANNED** — depoda C++/DSP motoru (`engine/`,
> `*.cpp`/`*.h`) **yoktur**. Bu dosya şartnamedir, çalışan kod değildir.
> **Eski FM notu:** `parent_id: K000–K5000 Architecture Registry` yazımı bu turda
> görev manifesti kuralıyla değiştirilmiştir: kök düzey `parent_id: K004-dsp`,
> `node_type: subdomain` (10 alan şeması §C.8).

## §A Ağaç — 13 alt-modül (kök + 12 Kademe-2 çocuk)

### §A.1 Rol (ELI15)

**Sesin şekillendirildiği yer — bass, tiz, ekolayzır, oda hissi.**

K004, sesi **değiştiren** katmandır. Ham akış K003'ten gelir, burada işlemden
geçer ve tekrar akışa döner. ELI15 benzetme: K004 bir stüdyo miksaj masasıdır —
fader'lar, ekolayzır, kompresör ve oda simülasyonu üstüne oturur; masanın kendisi
müziği üretmez, yalnızca duyulanı şekillendirir.

K004'ün iki temel özelliği:

1. **Graf düğümü olarak yaşar.** Kendi thread'i / clock'ı iddia etmez; K003'ün
   realtime döngüsüne bir işleyici (node) olarak eklenir.
2. **Durum taşır.** Filtre bellekleri, kompresör envelope'u, reverb geçmişi =
   örnek başına güncellenen durum → sıfırlama/başlatma semantiği `pipeline` çocuğunun
   sorumluluğudur.

### §A.2 Sayım notu (13 nedir — zero-hallucination)

| Kaynak | Sayım | Okuma |
|---|---|---|
| `K004-dsp/index.md` §2 (v2 §2.4 birebir) | **12 çocuk** | Kademe-2 klasör sayısı — diskte 12 klasör doğrulandı (2026-10-13) |
| `.ai/architecture/index.md` §9.6 (2026-10-12 22:05 ölçümü) | **kök + 13 alt-modül** | Öğe sayısı 69 dosya olarak kayıtlı |
| Bu dosya §A başlığı | **13 alt-modül** | **12 Kademe-2 çocuk + 1 kök dosya paketi (dsp-master/bagimliliklar/dogrulama) = 13 düğüm** |

> ⚠️ VERIFICATION REQUIRED: `.ai/architecture/index.md` §9.6'daki "13 alt-modül"
> ile v2 §2.4'teki "12 çocuk" arasındaki fark, kök üç dosyanın ayrı bir öğe
> sayılmasından gelir (böyle okunmuştur). Vault içi iki kayıt da diskte 12 Kademe-2
> klasörü gösterir → **çocuk sayısı 12'dir**; 13 yalnız bu dosyanın §A başlığını
> Kök + çocuklar toplamı olarak tanımlar. Fark tek taraflı "düzeltmez", iki kayıt
> birlikte taşınır (v2 §4.3 çelişki kuralı).

### §A.3 Alt-modül ağacı (13 düğüm — v2 §2.4 birebir, 12 çocuk + kök)

| # | Düğüm | Kapsam (1 satır) | Üst düğüm | Dosya (disk) | Durum |
|---|---|---|---|---|---|
| 0 | **K004-dsp (kök)** | DSP anayasası, bağımlılıklar, doğrulama | K003-audio | 5 md (index · master · bagimliliklar · bilesenler · dogrulama) | draft |
| 1 | pipeline | İşlem basamaklarının sırası ve akış grafiği | K004-dsp | 5 md | draft |
| 2 | equalizer | Frekans bantlarına göre seviye ayarı | K004-dsp | 6 md + tier-3 `eq/` | draft |
| 3 | filters | Alçak/yüksek/geçiren bant filtreleri | K004-dsp | 6 md | draft |
| 4 | dynamics | Dinamik işlemin şemsiye tanımı | K004-dsp | 6 md | draft |
| 5 | compressor | Eşik üstü seviyeyi tırpanlama | K004-dsp | 6 md + tier-3 `compressor/` | draft |
| 6 | limiter | Aşırı seviyeyi kesen güvenlik katmanı | K004-dsp | 6 md + tier-3 `limiter/` (planlı) | draft |
| 7 | reverb | Yansıma/yankı taklidi — oda hissi | K004-dsp | 5 md + tier-3 `reverb/` | draft |
| 8 | delay | Belirli gecikmeli kopya | K004-dsp | 6 md | draft |
| 9 | crossover | Frekansa göre hoparlör ayrımı | K004-dsp | 6 md | draft |
| 10 | spatial-audio | Konum/hissiyat katmanı | K004-dsp | 5 md | draft |
| 11 | measurement | Frekans/zaman tepkisi ölçümü | K004-dsp | 5 md + tier-3 `fft-analiz/`, `loudness/` | draft |
| 12 | testing | DSP testleri ve bozulma kontrolü | K004-dsp | 5 md | draft |

> Çocuk tanımları yalnız addan türetilmiştir (v2 §3 KURAL 3) → her satır
> ⚠️ VERIFICATION REQUIRED taşır. v2'deki yazım/typo'lar olduğu gibi korunur (KURAL 1).
> Dosya sayıları 2026-10-13 `Get-Content` sayımıdır; `index.md`'ye dokunulmamıştır.

### §A.4 Çocuklar arası iç ilişki iddiaları (henüz [TASARIM])

| İlişki | Tür | Kanıt |
|---|---|---|
| pipeline → tümü | sıra/kontrol | adından türetildi ⚠️ |
| dynamics → compressor · limiter | hiyerarşi mi kardeş mi? | ⚠️ Q3 |
| filters → crossover | frekans ayrımı pratiği | adından türetildi ⚠️ |
| measurement → testing | ölçüm verisi testi besler mi? | ⚠️ Q4 |
| delay → reverb | reverb iç gecikme satırı olabilir mi | adından türetildi ⚠️ |

Bu tablo hiçbir `depends_on` alanına yazılmaz; alan içi ilişki = `evidence` notudur
(v2 §4.3 komşu-farkındalık kuralı).

### §A.5 Tier-3 derin dal planı (7 dal × 8 dosya — ADR-097 §5.5)

> Tier-3 dalı yalnız bir teknolojiyi derinleştirir; her dosya adı `<teknoloji>-<rol>`
> öneki taşır (soyut ad YASAK — ADR-097 §5.5).

| # | Tier-3 dalı | Üst çocuk | 8 dosyalık rol seti (önek = dal adı) |
|---|---|---|---|
| 1 | `equalizer/eq` | equalizer | eq-anayasa · eq-filtre-topolojileri · eq-coefficient-hesaplari · eq-kod-ornekleri · eq-gercek-zaman-ve-performans · eq-test · eq-dogrulama · eq-bilesenler |
| 2 | `pipeline/resampler` | pipeline | resampler-anayasa · resampler-nesnel-kesme-ve-aliasing · resampler-interpolasyon · resampler-kod-ornekleri · resampler-performans · resampler-test · resampler-dogrulama · resampler-bilesenler |
| 3 | `measurement/fft-analiz` | measurement | fft-analiz-anayasa · fft-analiz-pencil-function · fft-analiz-windowing · fft-analiz-spektrum · fft-analiz-kod-ornekleri · fft-analiz-test · fft-analiz-dogrulama · fft-analiz-bilesenler |
| 4 | `measurement/loudness` | measurement | loudness-anayasa · loudness-itu-r-bs1770 · loudness-k-weighting · loudness-integrasyon-ve-lufs · loudness-kod-ornekleri · loudness-test · loudness-dogrulama · loudness-bilesenler |
| 5 | `reverb/reverb` | reverb | reverb-anayasa · reverb-fdn-topolojileri · reverb-odme-aglari · reverb-yutma-filtreleri · reverb-kod-ornekleri · reverb-test · reverb-dogrulama · reverb-bilesenler |
| 6 | `compressor/compressor` | compressor | compressor-anayasa · compressor-ozellik-tanima · compressor-attack-release · compressor-knee-ve-ratio · compressor-kod-ornekleri · compressor-test · compressor-dogrulama · compressor-bilesenler |
| 7 | `limiter/limiter` | limiter | limiter-anayasa · limiter-lookahead · limiter-gain-riding · limiter-true-peak · limiter-kod-ornekleri · limiter-test · limiter-dogrulama · limiter-bilesenler |

**Yerleşim kararı:** `reverb/`, `compressor/`, `limiter/` teknoloji adı zaten klasör
adıyla aynı olduğu için dosyalar doğrudan `<çocuk>/<teknoloji>/` altında yaşar (K003
`backend/asio/`, K000 `isletim-sistemi-audio/wasapi/` örneğiyle aynı örüntü). `eq`,
`resampler`, `fft-analiz`, `loudness` teknoloji adları çocuk adından farklı olduğu için
ayrı alt klasör alır.

**Kod olmayan 6 çocuk:** filters · dynamics · delay · crossover · spatial-audio ·
testing → tier-3 yok (kapsam tek dosyada yeterli; derinleştirme needsiz genişleme → YAGNI).

### §A.6 Kapsam / sınır (ne K004'ün işidir, ne DEĞİL)

| Alan | Kapsam içi |
|---|---|
| pipeline | İşlem basamaklarının sırası ve akış grafiği |
| frekans | equalizer · filters · crossover |
| dinamik | dynamics · compressor · limiter |
| uzamsal | reverb · delay · spatial-audio |
| ölçüm | measurement (tepki ölçümü) · testing (bozulma kontrolü) |

| Konu | Sahibi | Gerekçe |
|---|---|---|
| Sesin taşınması, cihaz seçimi, zamanlama | K003-audio | Taşıyıcı ayrı alan |
| DAC/ADC, amplifikatör devresi, PCB | K001-donanım | Fiziksel tasarım |
| Cihaz içindeki kontrol/komut yazılımı | K005-firmware | Donanım içi yazılım |
| OS kaynağı, sürücü, thread yönetimi | K000-isletim-sistemi | Zemin katmanı |
| Kullanıcının EQ preset arayüzü | uygulama katmanı | K004 motoru, UI'ı bilmez |
| Ses kaynağı metadata (ffmpeg) / BI ölçüm | K002-data | Veri katmanı |

**Sınır kuralı:** K004, K003'ün realtime döngüsüne **graf düğümü olarak** eklenir;
kendi başına ayrı bir zaman çizgisi (thread/clock) iddia etmez. İddia ederse bu
K003 ile çakışma → §B.7 risk satırı olur, sessizce çözülmez.

### §A.7 Dosya-grup eşlemesi (iki-eksen, ADR-097 §5.5 · registry §8.1)

| Grup | Kök dosyalar | Çocuk dosyaları | Tier-3 dosyaları |
|---|---|---|---|
| mimari | index · dsp-master · dsp-kapsam · dsp-bagimliliklar · dsp-bilesenler · dsp-kararlar · dsp-evrensel-dil | index · -kapsam · -bilesenler | -anayasa · -bilesenler · teknoloji-topoloji dosyaları (eq-filtre-topolojileri, resampler-interpolasyon, loudness-k-weighting, compressor-knee-ve-ratio, limiter-lookahead, reverb-fdn-topolojileri, fft-analiz-windowing) |
| kod | dsp-kod-ornekleri · dsp-test-stratejisi | -ornekler | -kod-ornekleri · -test · -gercek-zaman-ve-performans · -performans · -pencil-function · -spektrum · -odme-aglari · -yutma-filtreleri · -ozellik-tanima · -attack-release · -gain-riding · -true-peak · -nesnel-kesme-ve-aliasing · -itu-r-bs1770 · -integrasyon-ve-lufs |
| kanit | dsp-dogrulama · dsp-web-arastirma · dsp-riskler | -dogrulama · -web-arastirma | -dogrulama |

### §A.8 Üç dosyalık rol seti (18 modül dosyası — şablon bağlayıcılığı)

> Her Kademe-2 çocuk bu turda **3 dosya** ile temsil edilir: `-kapsam` → `-dogrulama`
> → `-ornekler` **sırasıyla** yazılır (§D.3 F2). Sıralama gerekçesi: kapsam
> tanımlanmadan doğrulama senaryosu yazılamaz, doğrulanmadan örnek kod
> yazılamaz — kanıt zinciri tersine işlemez.

| Dosya | Bölüm seti | §A | §B | §C | §D | group |
|---|---|---|---|---|---|---|
| `<çocuk>-kapsam.md` | §A-§D | Kapsar / kapsamaz / kabul kriteri | Bileşenler | Arayüzler | Bağımlılık grafiği | mimari |
| `<çocuk>-dogrulama.md` | §A-§D | Senaryolar | Test stratejisi | Ölçüm metodolojisi | Referans değerler | kanit |
| `<çocuk>-ornekler.md` | §A-§D | Yapılandırma | C++ gövde + `Dosya:`/`Satır:` kanıtı | Kötü uygulama | Karşılaştırmalı | kod |

Üç dosyanın da geçtiği ortak kurallar:

1. **≥500 tam satır** — 499 satır başarısızdır; ölçüm PowerShell
   `Get-Content -Encoding UTF8` (veya `[System.IO.File]::ReadAllLines`) ile yapılır.
2. **FM 10 alan** zorunlu (§C.8); `group` alanı FM kapanış `---`'ünden hemen önce.
3. **Son satır** sabittir: `**Authority:** Bayram Ali / Vault Steward · **Mode:**
   Red Team · Human Mode · Truth Mode`.
4. Dolgu yasağı §C.2 Y3 satırıdır (yer tutucu metin / üç nokta / geçmiş-ekleme
   sözcüğü / boş gövdeli yorum / boş hücre / mojibake).
5. Web iddiası yalnız bu turun havuzundan girer (§B.4; envanter-dışı, HTTP 200,
   2026-10-13). Kesişen iddia URL'siz taşınır (§B.2 politikası).
6. **Repo kod kanıtı ≥1 satır/dosya** — `Dosya:`/`Satır:` biçiminde ve diskten
   doğrulanmış satır numarasıyla.

**Repo kanıtı gerçekliği (tüm `-ornekler` dosyaları için):** depoda C++/DSP motoru
**yoktur** (§C.7). Bu yüzden `-ornekler` §B'sindeki C++ gövdeleri **şartname
gövdesidir** (JUCE `juce::dsp` sözleşmesine göre kurgulanmış, derlenmemiş kod) ve
böyle işaretlenir; dosyanın *gerçek* `Dosya:`/`Satır:` kanıtı ayrı satırda PHP/JS
kaynaklarından verilir (AIEngine · footer · widget-grid · coreplayer JS · middleware
· CI dosyaları). Şartname gövdesine gerçek repo satırıymış gibi satır numarası
**uydurulmaz** (§C.2 Y6).

## §B Master kararlar

### §B.1 Karar kademesi (hangi seviyede karar verilir)

| # | Karar seviyesi | Örnek | Sahibi | Durum |
|---|---|---|---|---|
| M1 | Domain anayasası (sınır, çocuk listesi) | 12 çocuk = v2 §2.4 birebir | Vault Steward | ✅ 2026-10-12 |
| M2 | Mimari yaklaşım (graf düğümü, clock yok) | K004 → K003 yönü tek yön | mimari | ✅ [TASARIM] |
| M3 | Teknoloji adayı seçimi | Signalsmith/Patina referans, bağımlılık yok | embedded | ⚠️ lisans teyidi → Q2 |
| M4 | Sayısal parametre (ceiling, ratio, latency bütçesi) | tümü | — | **UNKNOWN — uydurulmaz** |
| M5 | Ölçüm eşiği | SNR >100 dB · THD+N <0.01%@1kHz | iç kanıt | ✅ AGENTS.md §5 |
| M6 | Kod yazımı (motor) | `engine/` | embedded | ❌ başlamadı — depoda 0 satır |

**Karar kuralı:** M4 seviyesindeki hiçbir sayı, kanıt gelene kadar yazılmaz; M5'teki
eşikler tek kaynaktan (`.config/opencode/AGENTS.md` §5) okunur, iki yere iki farklı
sayı yazılmaz.

### §B.2 Teknoloji bulguları (T1-T10 — kaynak adı taşınır, envanter URL'leri çıkar)

> **URL politikası (bu tur):** T1-T3/T9-T10 satırlarının eski URL'leri vault
> envanteriyle (1441 URL) kesiştiği için **bağlantı sütunundan çıkarıldı**; iddia
> metni korundu, kanıt durumu `envanter üyesi` olarak işaretlendi. Güncellenmiş
> doğrulanmış URL'ler §B.4ededir (2026-10-13, HTTP 200, envanter-dışı).

| # | Bulgu | Kaynak (URL bu tur yazılmadı) | Erişim | Durum |
|---|---|---|---|---|
| T1 | Patina — C++17 analog modeling projesi mevcut | github.com/ShmKnd/Patina (envanter üyesi) | 2026-10-12 | ✅ doğrulandı · URL kesişim nedeniyle burada yok |
| T2 | Signalsmith basics — MIT lisanslı C++ yardımcıları (116★) | github.com/Signalsmith-Audio/basics (envanter üyesi) | 2026-10-12 | ✅ doğrulandı · URL kesişim nedeniyle burada yok |
| T3 | Signalsmith dsp — DSP kütüphanesi (277★) | github.com/Signalsmith-Audio/dsp (envanter üyesi) | 2026-10-12 | ✅ doğrulandı · URL kesişim nedeniyle burada yok |
| T4 | Signalsmith basics MIT lisans iddiası (LICENSE teyidi) | repo LICENSE satırı okunmalı | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T5 | DSPark v1 — '90+' iddiası (manifest) | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T6 | DSPark GitHub ölçümü (2★) ve eşleşen depo | depo URL'i bulunamadı | 2026-10-12 | ⚠️ VERIFY REQUIRED + URL **UNKNOWN** |
| T7 | Ölçüm eşikleri: SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | ~/.config/opencode/AGENTS.md §5 | 2026-10-12 | ✅ iç kanıt |
| T8 | DSP/zaman hedefi (ör. latency bütçesi) sayısal değeri | — | 2026-10-13 | **UNKNOWN** |
| T9 | Apple Core Audio — `AudioUnitRender` çekirdek işlevi (K003 kaynağı, K004 işleyici ekleme bağlamı) | developer.apple.com (envanter üyesi) | 2026-10-12 | ✅ doğrulandı · URL kesişim nedeniyle burada yok |
| T10 | ALSA hw_params/sw_params ayrımı (K003 kaynaklı; K004 zamanlama bağlamı) | alsa-project.org (envanter üyesi) | 2026-10-12 | ✅ doğrulandı · URL kesişim nedeniyle burada yok |

**T5/T6 çelişki kaydı:** Görev manifesti DSPark v1 için '90+' (birim belirtilmemiş)
iddiasını taşırken, GitHub ölçümü 2★ verdi ve eşleşen depo bulunamadı. İki değer de
kayıtlıdır; **hiçbiri tek taraflı düzeltilmez** → açık soru Q1.

**Yasak:** T8 gibi doğrulanmamış sayısal hedefler uydurulmaz (ör. "10 ms latency")
— kanıt gelene kadar UNKNOWN kalır.

### §B.3 Kanıt durumuna göre teknoloji kararı matrisi

| Teknoloji | Kanıt | K004 kararı | Not |
|---|---|---|---|
| Patina (analog modeling) | ✅ repo (2026-10-12) | Referans inceleme kaynağı | Lisans okunmadı → ⚠️ |
| Signalsmith basics | ✅ repo (2026-10-12) | Aday yardımcı kütüphane | LICENSE teyidi → Q2 |
| Signalsmith dsp | ✅ repo (2026-10-12) | Aday DSP kütüphanesi | Sürüm bilgisi yok → UNKNOWN |
| DSPark v1 | ⚠️ çelişkili (90+ ↔ 2★) | **Karar yok** — kanıt gelene kadar kullanılmaz | Q1 |
| JUCE dsp modülü (Limiter/Reverb/Convolution/IIR/Oversampling) | ✅ resmi doxygen (2026-10-13 §B.4) | Aday işleyici kütüphanesi | Sürüm kanıtı ayrı → §C.6 |
| Harici bağımlılıksız kendi kodumuz | — | Varsayılan yaklaşım | framework bağımlılık artışı → kanıtsız bağımlılık alınmaz |

**Kural:** Bu matrisde ✅ olmayan satır, K004 tasarımında teknoloji seçimi olarak
kullanılamaz; yalnız ad olarak anılır. Karar verilen her satır, kanıtıyla birlikte
bu tabloya işlenir ve `dsp-dogrulama.md` §A'ya düşer — sessiz güncelleme yasak.

### §B.4 Web araştırma tablosu (bu tur — 2026-10-13, envanter-dışı, HTTP 200)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| C++ dil standardı (draft) erişilebilir — K004 kodu C++20 kurallarına bağlanır | https://eel.is/c++draft/ | 2026-10-13 | ✅ HTTP 200 |
| C++ referans (cppreference) — `std::atomic`, `alignas` vb. dil varlıkları için birincil ikincil kaynak | https://en.cppreference.com/w/ | 2026-10-13 | ✅ HTTP 200 |
| ISO C++ üyelik sayfası — dil/başlık standardı referans kapısı | https://isocpp.org/ | 2026-10-13 | ✅ HTTP 200 |
| GNU GCC belge indeksi — `-ffast-math`/FCM-semantik ailesi uyarıları için resmi referans | https://gcc.gnu.org/onlinedocs/ | 2026-10-13 | ✅ HTTP 200 |
| MSVC `/fp` (floating-point behavior) belgesi — derleyici FP yarı-kesinliği uyarı kaynağı | https://learn.microsoft.com/en-us/cpp/build/reference/fp-specify-floating-point-behavior | 2026-10-13 | ✅ HTTP 200 |
| UBSan (Clang) — kayan-nokta/undefined davranış denetim aracı | https://clang.llvm.org/docs/UndefinedBehaviorSanitizer.html | 2026-10-13 | ✅ HTTP 200 |
| Valgrind — bellek/performans denetim aracı (Linux hedefi) | https://valgrind.org/ | 2026-10-13 | ✅ HTTP 200 |
| Cppcheck — statik analiz aracı | https://cppcheck.sourceforge.io/ | 2026-10-13 | ✅ HTTP 200 |
| CMake en son sürüm dokümantasyonu — K004 build tanımı için referans | https://cmake.org/cmake/help/latest/ | 2026-10-13 | ✅ HTTP 200 |
| RFC 2119 — "MUST/SHOULD" anahtar kelimelerinin tanımı (şartname dili) | https://www.rfc-editor.org/rfc/rfc2119 | 2026-10-13 | ✅ HTTP 200 |
| NIST SP 800-82r3 — kontrol/ölçüm güvence çerçevesi (test kapısı referansı) | https://csrc.nist.gov/pubs/sp/800/82/r3/final | 2026-10-13 | ✅ HTTP 200 |
| ITU-R BS.1770-5 — loudness/ölçüm algoritması önerisi (measurement dalının standardı) | https://www.itu.int/rec/R-REC-BS.1770-5/en | 2026-10-13 | ✅ HTTP 200 |
| EBU Tech 3344 — program loudness ölçüm/raporlama teknik notu | https://tech.ebu.ch/docs/tech/tech3344.pdf | 2026-10-13 | ✅ HTTP 200 |
| JUCE resmi doxygen kökü — dsp namespace dokümantasyon kapısı | https://docs.juce.com/ | 2026-10-13 | ✅ HTTP 200 |
| `juce::dsp` namespace sayfası — işleyici envanteri (Limiter, Reverb, Convolution, Oversampling…) | https://docs.juce.com/master/namespacejuce_1_1dsp.html | 2026-10-13 | ✅ HTTP 200 |
| MDN Web Audio API ana sayfası — tarayıcı-tabanlı referans işleyici modeli | https://developer.mozilla.org/en-US/docs/Web/API/Web_Audio_API | 2026-10-13 | ✅ HTTP 200 |
| W3C Web Audio API önergesi — referans işleyici/parametre modeli | https://www.w3.org/TR/webaudio/ | 2026-10-13 | ✅ HTTP 200 |

> Bu tablo yalnızca bu oturumda **HTTP 200** doğrulanmış ve **1441 URL'lik vault
> envanteriyle kesişmeyen** adresleri taşır. Kesişen URL'ler (Apple, ALSA, PipeWire,
> GitHub depoları, eski EBU R128 PDF'i, IEEE, dsprelated-kitap bölümü vb.) bu tur
> yeniden yazımda **bağlantı olarak kullanılmaz** — iddia metni korunur, URL satır
> dışı bırakılır (bkz. §B.2 politika notu).

### §B.5 Bağımlılık özeti (özet — tam tablo `dsp-bagimliliklar.md` §A/§B)

| Yön | Bağımlılık | Tür | Etiket |
|---|---|---|---|
| K004 → K003-audio | Akış kaynağı ve graph'a ekleme noktası | çalışma zamanı | [TASARIM] |
| K004 → K000-isletim-sistemi | CPU, thread, bellek kaynağı | çalışma zamanı | [TASARIM] |
| K004 → K001-donanım | Nihai çıkış amplifikatöre/hoparlöre iner | fiziksel | [TASARIM] |
| K004 → K005-firmware | DSP donanım içinde çalışıyorsa firmware sınırı | — | [TASARIM] ⚠️ Q5 |
| K004 → (geriye K001/K000'e) kod bağımlılığı | — | — | yok (yalnız arayüz) |

A1 dizisi: K000 → K001 → K003 → K004 → K005. K004 **dördüncü katmandır** ve
K003'ün üstünde yaşar (K004 → K003 yönü). Ters yön (K003'ün K004'ü bilmesi)
sınır ihlalidir: K003 yalnızca soyut bir işlem noktası tanır.

### §B.6 Açık sorular (Q1-Q5 — kök seviye)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | DSPark '90+' iddiası ne anlama geliyor, doğru depo hangisi? | ⚠️ VERIFY REQUIRED | researcher |
| Q2 | Signalsmith basics lisansı MIT mi (LICENSE teyidi)? | ⚠️ VERIFY REQUIRED | researcher |
| Q3 | dynamics ↔ compressor/limiter hiyerarşisi mi, kardeş mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | measurement ↔ testing sorumluluk ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | DSP hedefi donanım içinde mi (K005) host'ta mı (K003 üstü)? | ⚠️ VERIFICATION REQUIRED | embedded + dsp-fw |

### §B.7 Riskler

| # | Risk | Etki | Eylem |
|---|---|---|---|
| R1 | Q3 çözülmezse dynamics↔compressor/limiter iki kez tanımlanır | kapsam tekrarı | Vault Steward kararı |
| R2 | Q5 çözülmezse K004 ↔ K005 sınırı belirsiz kalır | katman ihlali riski | embedded + dsp-fw incelemesi |
| R3 | latency bütçesi UNKNOWN iken "gerçek zamanlı" iddiası yazılamaz | yanlış [TASARIM] | UNKNOWN korunur |
| R4 | Lisansı okunmamış kütüphane (Patina/Signalsmith) üretime alınır | lisans riski | LICENSE teyidi zorunlu |
| R5 | DSPark '90+' kanıtsız kullanılır | kanıtsız bağımlılık | Q1 kapanana kadar karar yok |
| R6 | §A.2'deki 12↔13 sayım farkı sessiz "düzeltilir" | iki kayıt çelişkisi | iki değer birlikte taşınır |

## §C Çapraz kurallar (her K004 dosyası için bağlayıcı)

### §C.1 Realtime kod kuralları (audio callback — L0 altyapı)

| # | Kural | Kaynak |
|---|---|---|
| C1 | Sıfır tahsis: audio thread'de `malloc`/`new`/`delete` **YASAK** | L0 kural seti |
| C2 | Lock-free: audio thread'de `mutex`/`lock_guard`/`unique_lock` **YASAK** | L0 kural seti |
| C3 | Audio callback'lerde `noexcept` **zorunlu** | L0 kural seti |
| C4 | Derleme-zamanı hesapları `constexpr` | L0 kural seti |
| C5 | Cache-line hizası `alignas(64)` | L0 kural seti |
| C6 | Ses verisi 32-bit float (ADR-017) | ADR-017 |
| C7 | Buffer 512 örnek · 48 kHz (sartname; ölçüm PLANNED) | sartname |
| C8 | ASIO Exclusive Lock: aynı anda **tek** uygulama | L0 kural seti |
| C9 | DC offset >0.5 V → koruma rölesi zorunlu | ADR-038 H001 |
| C10 | Per-OS player FROZEN — değişiklik ≥088 (ADR-019) | ADR-019 |

### §C.2 Donanım/kod yasakları (K004 dosyalarında geçişsiz)

| # | Yasak | Zorunlu karşılık | Kaynak |
|---|---|---|---|
| Y1 | PCM5122 | **yalnız PCM3168A** | ADR-038 H001 |
| Y2 | Bilinmeyen API/sınıf/sürüm uydurma | `VERIFICATION REQUIRED` / `UNKNOWN` yazılır | zero-hallucination |
| Y3 | Dolgu satırı: yer tutucu metin · üç nokta · geçmiş-ekleme sözcüğü · boş gövdeli yorum · kısa kod parçası · boş hücre · mojibake | içerik satırı yazılır | görev kuralı |
| Y4 | `parent:` alanı (eski şema) | `parent_id` | FM v2 §4.2 |
| Y5 | Envanterle kesişen URL | envanter-dışı havuz (§B.4) veya URL'siz iddia | görev kuralı |
| Y6 | Uydurma satır numarası | diskten okunup doğrulanır (`Dosya:`/`Satır:`) | zero-hallucination |
| Y7 | Subagent `git commit` | commit orkestratöre ait | §6 kuralı |

### §C.3 İmla koruması (düzeltilmez — kaynağındaki gibi kalır)

Aşağıdaki yazım hataları kasıtlıdır ve yeniden yazımda **korunur**:
`electroncis` · `donanaım-type` · `identitiy` · `integraion` · `comilance` ·
`comere` · `recomenataion`. Ayrıca v2 §2.4 çocuk adlarındaki typos'lar (KURAL 1)
olduğu gibi taşınır. Konsol çıktısındaki `?`/bozuk harf görünümü dosya bozukluğu
değildir (konsol codepage'i) — dosya içeriği UTF-8'dir, mojibake **yazılmaz**.

### §C.4 Bağımlılık/artış kuralı

K004'e harici DSP bağımlılığı yalnız iki koşulla girer: (1) lisans metni okunmuş
ve CoreMusic lisansıyla uyumlu, (2) sürümü sabitlenmiş ve kanıtı `dsp-bagimliliklar.md`
§E'ye yazılmış. Aksi halde bağımlılık = kanıtsız → alınmaz; kendi kodumuz varsayılandır.

### §C.5 Ölçüm eşikleri (tek kaynak — iki yerde iki sayı yazılmaz)

| Eşik | Değer | Kaynak |
|---|---|---|
| SNR | > 100 dB | `.config/opencode/AGENTS.md` §5 (okundu 2026-10-12) |
| THD+N | < 0.01 % @ 1 kHz | aynı dosya |
| Buffer / örneklem | 512 · 48 kHz | sartname (ölçüm PLANNED) |
| DC offset koruma | > 0.5 V | ADR-038 H001 |
| Ses veri biçimi | 32-bit float | ADR-017 |
| Latency bütçesi | — | **UNKNOWN — sayı uydurulmaz (T8)** |

### §C.6 Sürüm kanıtı kuralı (VERIFICATION REQUIRED)

"JUCE 9" ve "ASIO SDK 2.3.4" sürümleri için **bağımsız sürüm kanıtı bu turda
toplanmadı** → bu iki iddia K004 dosyalarında `⚠️ VERIFICATION REQUIRED` olarak
taşınır; §B.4'teki JUCE doxygen adresleri dokümantasyon erişimini kanıtlar,
**sürüm numarasını kanıtlamaz**. Bağımlılık bump / firmware OTA / refactor talebi
gelirse süreç **DUR** + handover (ork. kural 13).

### §C.7 Repo gerçekliği (CoreMusic kodunda K004 karşılığı)

> Zero-hallucination: repo'da C++/DSP motoru **YOKTUR**; yalnız aşağıdaki gerçek
> PHP/JS satırları DSP çağrışımı taşır. Satır numaraları 2026-10-13 disk okumasıdır.

| Dosya | Satır | Ne var | K004 karşılığı |
|---|---|---|---|
| `shared/src/AI/AIEngine.php` | 300–339 | `ffmpeg` metadata çıkışı + `extractFeatures()` placeholder + `bandToFrequency()` ISO 31-band EQ (20 Hz–20 kHz) | equalizer — **tek gerçek kod kanıtı** |
| `shared/src/AI/AIEngine.php` | 347, 362 | `calculateRoomCorrection()` — hedef/gerçek tepki farkı × roomFactor × (1 − reverb) | reverb-koklu placeholder (miktar: sözde-ses) |
| `shared/src/AI/AIOrchestrator.php` | 32, 36, 45 | `embedded` / `dsp-fw` ajan alan eşlemeleri (audio-dsp) | yalnız ajan yönlendirmesi |
| `shared/src/AI/ToolCalling.php` | 38 | `dsp` araç alanı eşlemesi | yalnız ajan yönlendirmesi |
| `home.coremusic.net/footer.php` | 121–122 | `<button … aria-label="Ekolayzır" data-action="openEqualizerPopup">` | uygulama katmanı (K004 değil) |
| `home.coremusic.net/pages/components/widget-grid.php` | 59–60 | `widget-card--eq` hızlı erişim kartı | uygulama katmanı (K004 değil) |

**Örnek satır (doğrulanmış):**

```
Dosya: home.coremusic.net/footer.php
Satır: 121
  <button class="footer__utility-icon" aria-label="Ekolayzır" data-action="openEqualizerPopup" title="Ekolayzır">
```

**Sonuç:** K004 için repo'da çalışan DSP kodu **0 satır**; `bandToFrequency()`
yalnız bant→Hz karşılığı üreten bir tablodur, filtre/dinamik uygulaması değildir.

### §C.8 Frontmatter şeması (10 alan — her K004-dsp dosyasında zorunlu)

Her K004-dsp dosyasının frontmatter'ında **10 alan** bulunur:
`node_id · node_type · status · parent_id · children · depends_on · evidence ·
validation · open_questions · group`.

| Alan | Kök dosya (bu dosya, bagimliliklar, dogrulama) | Modül dosya (6 klasör × 3) |
|---|---|---|
| `node_id` | `K004-dsp/dsp-master` · `K004-dsp/dsp-bagimliliklar` · `K004-dsp/dsp-dogrulama` | `K004-dsp/limiter/limiter-kapsam` gibi dosya-yeleği |
| `node_type` | `subdomain` | `module` |
| `parent_id` | `K004-dsp` | `K004-dsp/limiter` gibi klasör düğümü |
| `children` | `[]` | `[]` |
| `depends_on` | `[K004-dsp]` | `[K004-dsp]` |
| `group` | master · bagimliliklar → `mimari` · dogrulama → `kanit` | `-kapsam` → `mimari` · `-ornekler` → `kod` · `-dogrulama` → `kanit` |

`group` alanı ADR-097 §5.5'teki üç değerden biridir (mimari / kod / kanit) ve
**frontmatter'da `---` kapanışından hemen önce** gelir. `parent:` alanı (eski şema)
yazılmaz → `parent_id`. `parent_id: K000–K5000 Architecture Registry` yazımı görev
manifesti gereği bu turda değiştirilmiştir (eski kayıt bu notta taşınır).

### §C.9 Ölçüm araç zinciri — bilinen ve UNKNOWN ayrımı

K004 doğrulaması üç katman üzerinde çalışır; bu turda yalnız **araç adresleri**
doğrulanmıştır (§B.4), **entegrasyon durumu** doğrulanmamıştır:

| Katman | Araç | Adres kanıtı | K004 entegrasyonu |
|---|---|---|---|
| Statik analiz | Cppcheck | ✅ §B.4 (HTTP 200) | **UNKNOWN** — CI'a eklenmedi |
| Bellek/denetime | Valgrind (Linux) | ✅ §B.4 (HTTP 200) | **UNKNOWN** — hedef OS net değil (Q5) |
| Tanı | UBSan (Clang) | ✅ §B.4 (HTTP 200) | **UNKNOWN** — C++ kaynağı yok (§C.7) |
| Derleyici FP denetimi | MSVC `/fp` · GCC belgeleri | ✅ §B.4 (HTTP 200) | **UNKNOWN** — derleme tanımı yok |
| Build | CMake | ✅ §B.4 (HTTP 200) | **UNKNOWN** — CMakeLists = 0 (L0 disk kuralı) |
| Ölçüm standardı | ITU-R BS.1770-5 · EBU Tech 3344 | ✅ §B.4 (HTTP 200) | hedef — measurement dalı şartnamesi |

> **Kural:** "araç doğrulandı" ile "araç kullanılıyor" aynı şey değildir. Bu tabloda
> entegrasyon sütununda UNKNOWN olan hiçbir satır, dosyalarda "CI'da çalışıyor"
> biçiminde yazılamaz. Ölçüm araç zincirinin K004'e bağlanması ayrı bir faz
> kapısıdır (§D.3 F4 sonrası) ve bağımlılık bump/refactor talebi gelirse süreç
> **DUR** + handover (§C.6).

### §C.10 Dosya-içi kanıt türleri (etiket sözlüğü)

Her K004-dsp dosyasındaki iddialar aşağıdaki etiketlerden birini taşır; etiketsiz
iddia **yazılmaz**:

| Etiket | Anlam | Örnek kullanım |
|---|---|---|
| ✅ | Disk/HTTP kanıtı var, 2026-10-13 doğrulandı | §B.4 satırları · §C.7 satır numaraları |
| ⚠️ VERIFICATION REQUIRED | Kaynak mevcut ama teyit edilmedi | JUCE 9 / ASIO SDK 2.3.4 sürümleri (§C.6) |
| UNKNOWN | Sayısal değer/kayıt hiç yok — uydurulmaz | latency bütçesi (T8) |
| [TASARIM] | Tasarım iddiası, henüz kod kanıtı yok | graf düğümü yönelimi (§A.6) |
| [KANIT] | İlgili kod depoda çalışıyor | K004'te bu etiket **0** (§D.2 D6) |

Bu tablo dosyalar arasında **tektir**: bir `-kapsam` dosyasındaki ✅, `-dogrulama`
dosyasında tekrar ✅ yapılmaz — referans verilir (`dsp-master.md §B.4` biçiminde),
kanıt iki kez bağımsız doğrulanmaz (SSOT).

## §D Yol haritası

### §D.1 Üretim durumu (2026-10-13 disk ölçümü — dürüst sayım)

| Katman | Hedef | Diskte | ≥500 satır | Durum |
|---|---|---|---|---|
| Kök (index + 11 dosya) | 12 dosya | 5 dosya (index · master · bagimliliklar · bilesenler · dogrulama) | bilesenler 503 · bu turda master/bagimliliklar/dogrulama ≥500'e geniştiriliyor | 🔄 bu tur |
| Kademe-2 çocuk dosyaları (12 × 6) | 72 dosya | 64 dosya (12 index + 18 bu tur hedefi + eski içerik) | bu tur hedefi: 18 dosya ≥500 | 🔄 bu tur |
| Tier-3 derin dal | 7 × 8 = 56 dosya | 4 dosya (limiter 2 · measurement 1 · pipeline 1 · reverb 1 · spatial 1 · testing 1 · compressor 1 = 7 içerik dosyası) | 7 dosya zaten ≥500 (503–509) | ⏳ genişletilecek |
| **Bu turun hedefi** | **21 dosya** | mevcut: kök 276/142/95 · modüller 48–87 satır | **0 / 21** | 🔄 bu tur |

**Gerçeklik notu (zero-hallucination):** Eski §13 tablosu "140 dosya / ~45.000 satır
✅" yazıyordu; 2026-10-13 `Get-Content` ölçümü bu iddiayı **desteklemiyor** (K004
içinde ≥500 satırlık dosya 7 içerik dosyasıyla sınırlıydı, 21 hedefin hiçbiri
≥500 değildi). Eski tablo **düzeltildi** — uydurma üretim yüzdesi taşınmadı. Depoda
C++ DSP kodu hâlâ 0 satırdır (§C.7) — bu dosyalar mimari şartnamedir, çalışan koddur.

### §D.2 D-kriterleri (status geçiş kapısı)

| Kriter | Tanım | Şu an |
|---|---|---|
| D1 | v2 §2.4 mini-ağaç birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md (8 zorunlu alan) | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama dosyaları | ✅ 2026-10-12 |
| D4 | açık ⚠️ / UNKNOWN satırı kalmadı | ❌ T4, T5, T6 ⚠️ + T8 UNKNOWN |
| D5 | açık sorular kapandı | ❌ 5 açık |
| D6 | en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 | status: verified | ❌ D1–D7 ✅ olunca |

**Geçiş kuralı:** D1–D7 tamamı ✅ → `index.md` `status: draft → verified`. Kısmi yok.

### §D.3 Faz planı (bu tur → sonraki)

| # | Faz | Kapsam | Kapı |
|---|---|---|---|
| F1 | Kök 3 geniştirme | dsp-master · dsp-bagimliliklar · dsp-dogrulama → ≥500 | satır ölçümü ≥500 |
| F2 | 6 klasör × 3 dosya | -kapsam → -dogrulama → -ornekler sırasıyla | her dosya ≥500 + FM 10 alan |
| F3 | Ölçüm ve rapor | satır · URL kesişim · dolgu · FM kesik | Çıktı tablosu + ÖLÇÜM bloğu |
| F4 | Q1–Q5 kapatma | researcher + Vault Steward turları | D4/D5/D6 ❌ → ✅ |
| F5 | Tier-3 genişletme | 7 dal × 8 dosya (≥500) | ayrı görev — bu turda YOK |
| F6 | Commit | **orkestratöre ait** — subagent atmaz | onay |

### §D.4 Sonraki adım (bu turun kapanışı)

1. F1: kök 3 dosya (bu dosya + bagimliliklar + dogrulama) ≥500 — **sırasıyla**.
2. F2: limiter → measurement → pipeline → reverb → spatial-audio → testing;
   her klasörde `-kapsam` → `-dogrulama` → `-ornekler`.
3. F3: PowerShell ölçümü (dosya=21 · ≥500=N · URL kesişim=0 · dolgu/MOJIBAKE/parent:/FM kesik=0).
4. Commit **yok** — rapor kullanıcıya sunulur, onay orkestratörde.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
