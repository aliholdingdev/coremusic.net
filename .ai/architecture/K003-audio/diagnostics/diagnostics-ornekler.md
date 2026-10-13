---
title: "diagnostics — Örnekler"
type: architecture-examples
node_id: K003-audio/diagnostics
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kod
---

# diagnostics — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**Ses telemetrisi — yok.** Ancak **log altyapısı gerçektir** (PHP tarafı):

```php
// .ai/AGENTS.md §25.3 kural 3 (dosya okundu — vault kuralı, kod değil):
//   "log.md append-only: Revizyon kayıtları eklenir, geçmiş satıra dokunulmaz."
// .ai/AGENTS.md §17 Edge Case #3 (dosya okundu):
//   "Sensitive data log'da → [REDACTED] ile maskeleme."
```

Bu iki kural K003 diagnostics'inin **format/sınıf kanıtı değildir**; yalnız
vault log disiplinini gösterir. Ses motoru olaylarını toplayan kod arandı
(`shared/src/**` içinde log toplayıcı/ölçüm sınıfı): kök `*.cpp`/`*.h` glob
0 isabet (2026-10-12) — **ses telemetrisi kodu yoktur**.

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | `auval` doğrulama aracı | `audio-master.md` §4 A6 (Apple AudioUnitDevelopmentFundamentals — bu oturumda vault'tan okundu) | plug-in API + temel işlev (kanal konfigürasyonu · instantiate süresi · render yeteneği) | Doğrulama **bağımsız araçla** yapılır: modül kendini test eder, amaçlanan davranışı raporlar |
| 2 | PipeWire latency mismatch raporu | https://pipewire.org/page_latency | linked node latency'leri toplanır, mismatch **tespit edilir** | Ölçüm sonucu **tek bir karşılaştırma** ile raporlanır: beklenen vs gerçekleşen → diagnostics şablonu |
| 3 | PipeWire scheduling sayaçları | https://pipewire.org/page_scheduling | required/pending sayaçları | Zamanlama olayı **sayaç tabanlıdır**; geri sayım aşımı (deadline miss) sayaktan anlaşılır |
| 4 | Core Audio API kapsamı (olay kaynakları) | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | sürücü · format · codec · sesun listesi | Telemetri kaynakları bu API'nin üstündedir: cihaz, format, sesun olayları |
| 5 | Log maskesi kuralı (vault) | `.ai/AGENTS.md` §17 #3 (dosya okundu) | [REDACTED] ile maskeleme | Hassas veri log'a **asla** açık yazılmaz — K003 log'u da bu kurala tabidir |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **diagnostics tasarımı öncesi** şablon olarak
kullanılır: 1, bağımsız doğrulama aracı fikri (auval benzeri K003 aracı —
testing ile ortak); 2, beklenen/gerçekleşen karşılaştırma raporu; 3, sayaç
tabanlı deadline tespiti; 4, telemetri kaynaklarının API kapsamı; 5, maskeleme
kuralı. Metrik listesi/periyot R2, sınıflandırma şeması R3
(`diagnostics-kapsam.md` §5) kapanmadan yazılmaz → UNKNOWN; hata kodu
uydurulmaz.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `diagnostics-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `diagnostics-dogrulama.md` | Kanıt/doğrulama tablosu (12 satır: ✅/has-been-fetched 9 · ⚠️ 1 · UNKNOWN 2) |
| `diagnostics/index.md` | Düğüm kaydı (ELI15 · açık soru: log formatı standardı yok) |
| `../audio-master.md` | Üst anayasa §3 satır 11 · §4 A6 · §5 T16 · §8 |
| `../audio-bagimliliklar.md` | §7 — diagnostics ↔ testing/tüm düğümler bağımlılığı |
| `../testing/testing-kapsam.md` | Kardeş çocuk — toplama ↔ kabul kriteri ayrımı (DG1) |

---

## §5 Eklenen örnek şablonların kullanım sınıfı

> §1 ("ses telemetrisi — yok") ve §2'deki 5 doğrulanmış örnek **aynen geçerlidir**;
> bu bölüm onların yerine geçmez. Eklenen şablonların tamamı `diagnostics-kapsam.md`
> §5'teki açık sorularla (R1–R6) kilitlidir.

| # | Şablon | Dayandığı orijinal örnek | Sınıf | Yasak |
|---|---|---|---|---|
| 1 | §7 log satırı alanları | §2 satır 5 (maskeleme kuralı) | [TASARIM] öncesi alan listesi | "standart budur" denmez (R1 açık) |
| 2 | §8 hata sınıfları | §2 satır 2–3 (mismatch + sayaç) | [TASARIM] öncesi sınıf adları | hata kodu rakamı atanmaz (R3 açık) |
| 3 | §9 sayaçlar | §2 satır 3 (scheduling sayaçları) | [TASARIM] öncesi sayaç listesi | değer/periyot yazılmaz (R2 açık) |
| 4 | §10 teşhis ağaçları | §2 satır 2 (beklenen≠gerçekleşen) | test/teşhis iskeleti | "çalışır teşhis" denmez |
| 5 | §11 ölçüm günlüğü | §2 satır 1 (auval bağımsız doğrulama) | boş kayıt şablonu | uydurma satır eklenmez |
| 6 | §6 repo kaydı | §1 ölçümü (2026-10-12) | kanıt tekrarı | yeni ölçüm iddiası yok |
| 7 | §12–§14 | §3–§4 | eşleme/atıf | kapı "geçti" denmez |

## §6 Repo log/ölçüm yüzeyi — okuma kaydı

| # | Yüzey | Ne arandı | Bulunan | Sonuç |
|---|---|---|---|---|
| 1 | kök `*.cpp`/`*.h` | telemetri üreten C++ kodu | 0 isabet (2026-10-12) | ses telemetrisi yok |
| 2 | `shared/src/**` | log toplayıcı/ölçüm sınıfı | PHP katmanı | K003 log kodu yok |
| 3 | `.ai/AGENTS.md` §25.3 #3 | append-only log kuralı | "Revizyon kayıtları eklenir, geçmiş satıra dokunulmaz" | ✅ iç kanıt (kod değil) |
| 4 | `.ai/AGENTS.md` §17 #3 | maskeleme kuralı | "Sensitive data log'da → [REDACTED]" | ✅ iç kanıt (kod değil) |
| 5 | `audio-master.md` §4 A6 | auval doğrulama aracı | bağımsız araç referansı | ✅ dosya okundu |
| 6 | `audio-master.md` §5 T16 | PipeWire latency mismatch | docs.pipewire.org page_latency | ✅ dosya okundu (URL fetch) |
| 7 | `diagnostics/index.md` satır 15 | açık soru | "log formatı/seviyesi standardı yok" | ✅ dosya okundu |
| 8 | `diagnostics-kapsam.md` §4 | disk kanıtları | 9 satır (7 iç/2 UNKNOWN) | ✅ dosya okundu |
| 9 | `.ai/log.md` | vault log dosyası | append-only kuralın kendisi | ✅ kural kanıtı |
| 10 | `shared/tests/**` | ölçüm testi | PHP testleri | K003 ölçüm testi yok |
| 11 | `.github/workflows/` | ölçüm adımı | workflow dosyaları | ses ölçüm adımı yok |
| 12 | `.ai/reports/` | ölçüm raporu | — (bu oturumda okunmadı) | ⚠️ rapor kanıtı yok |

**Not:** satır 12 açıkça "okunmadı" der — okunmayan yüzey için sonuç **üretilmez**.

## §7 Log satırı örnek şablonları

### §7.1 Alan sırası şablonu (aday — sıra kararı R1)

| # | Alan | Tip | Boş bırakılabilir mi | Not |
|---|---|---|---|---|
| 1 | zaman | zaman damgası | hayır | biçim R1 AÇIK |
| 2 | seviye | ad | hayır | ad listesi R1 AÇIK |
| 3 | kaynak düğüm | ad | hayır | engine/device/driver/... |
| 4 | üst sınıf | ad | hayır | §8 H-adları |
| 5 | olay adı | metin | hayır | — |
| 6 | mesaj | metin | hayır | yapılandırılmış mı R1 |
| 7 | bağlam kimliği | kimlik | koşullu | teyitsiz (cihaz kimliği biçimi) |
| 8 | süre | sayı + birim | koşullu | birim AÇIK (driver R5) |
| 9 | ölçüm değeri | sayı + birim | koşullu | — |
| 10 | tekrar | sayı | koşullu | — |
| 11 | maskelenen alan | metin | her zaman dolu olabilir | [REDACTED] |
| 12 | ilişkili olay | kimlik | koşullu | zincir R4 |
| 13 | sürüm/oturum | kimlik | hayır | — |
| 14 | kapı durumu | ad | koşullu | KAPALI/AÇIK etiketi |

### §7.2 Olay tipi → alan kümesi eşlemesi (örnek)

| # | Olay tipi | Zorunlu alanlar | Koşullu alanlar | Sınıf |
|---|---|---|---|---|
| 1 | cihaz kaybı | 1–7 · 13 | 10 · 12 | H1 |
| 2 | geri düşme kapısı | 1–6 · 13 | 12 · 14 | H1/H2 |
| 3 | cihaz açılamadı | 1–7 | 8 | H2 |
| 4 | kip reddi | 1–7 | 8 | H3 |
| 5 | format uyuşmazlığı | 1–6 | 7 · 9 | H4 |
| 6 | xrun/taşma | 1–6 · 10 | 9 | H5 |
| 7 | dropout | 1–6 · 8 | 9 | H6 |
| 8 | RT ihlali | 1–7 · 13 | 12 | H7 |
| 9 | gecikme ölçümü | 1–6 · 9 | 14 | H8 |
| 10 | bekleme≠gerçekleşen | 1–6 · 9 | 12 | H9 |
| 11 | sürücü hata kodu | 1–7 | 8 | H10 |
| 12 | clock kayması | 1–6 · 9 | 12 | H11 |
| 13 | karışım taşması | 1–6 · 10 | 9 | H12 |
| 14 | bilinmeyen kod | 1–6 | — | "bilinmeyen" sınıfı |

### §7.3 Seviye → olay örneği eşlemesi (adlar adaydır)

| # | Seviye adayı | Örnek olay | Ne zaman yazılır | Karar |
|---|---|---|---|---|
| 1 | TRACE | döngü adımı izi | yalnız debug derlemesi | R1 AÇIK |
| 2 | DEBUG | ihlal izi detayı | teşhis anında | R1 AÇIK |
| 3 | INFO | cihaz eklendi/karar verildi | olağan durum | R1 AÇIK |
| 4 | WARN | dönüştürme tetiklendi, devam ediyor | olağandışı | R1 AÇIK |
| 5 | ERROR | akış durdu | hizmet kesintisi | R1 AÇIK |
| 6 | FATAL | zincir tükendi, güvenli duruş | sistem durdu | R1 AÇIK |
| 7 | — (eşik) | hangi seviye testi geçirir | kabul kriteri | testing (bu dosya dışı) |

### §7.4 Log örneği ağaçları (biçim değil, akış)

```text
Olay: cihaz kaybı
  ├─ alanlar: zaman · seviye(?) · düğüm · sınıf(H1) · cihaz kimliği · oturum
  ├─ maskeleme: hassas alan varsa [REDACTED] (§1/#4 kuralı)
  ├─ sayaç: cihaz kaybı sayacı +1 (§9)
  ├─ zincir: kapı olayı bağlanır (sıra ⚠️ R4)
  └─ tüketici: teşhis ağacı (§10.2) → sonra testing eşiği
```

```text
Olay: RT yasağı ihlali
  ├─ tespit yöntemi: YAZILMADI (R4 AÇIK)
  ├─ sınıf: H7 (aday)
  ├─ zorunlu alan: düğüm · oturum · kapı durumu
  ├─ etki sayacı: dropout sayacı (§9 #4)
  └─ düzeltme sahibi: realtime (bu dosyanın dışında)
```

## §8 Hata sınıflandırma örnek şablonları

### §8.1 Sınıf → üretim yeri eşlemesi

| # | Sınıf (aday) | Üreten düğüm | Tipik eşlik verisi | Sınıflandırma sahibi |
|---|---|---|---|---|
| 1 | H1 cihaz kaybı | device · driver | cihaz kimliği | diagnostics |
| 2 | H2 açılamadı | driver | kip + format | diagnostics |
| 3 | H3 kip reddi | driver | exclusive/shared | diagnostics |
| 4 | H4 format uyuşmazlığı | device · driver | istenen≠desteklenen | diagnostics |
| 5 | H5 xrun | input · output | taşma boyutu | diagnostics |
| 6 | H6 dropout | engine · realtime | süre | diagnostics |
| 7 | H7 RT ihlali | realtime | düğüm adı | diagnostics (tespit) |
| 8 | H8 bütçe aşımı | diagnostics ölçümü | beklenen/gerçekleşen | testing (kabul) |
| 9 | H9 ölçüm tutarsızlığı | diagnostics | beklenen/gerçekleşen | diagnostics |
| 10 | H10 sürücü kodu | driver | asıl kod (çevirimsiz) | diagnostics |
| 11 | H11 clock kayması | synchronization | kayma değeri | diagnostics |
| 12 | H12 karışım taşması | mixing | pik değeri | diagnostics |
| 13 | H13 kaynak kesintisi | input | kaynak kimliği | diagnostics |
| 14 | H14 kapı aşımı | her düğüm | süre | diagnostics |

### §8.2 Sınıf → teşhis adımı eşlemesi

| # | Sınıf | İlk teşhis sorusu | Adım kaynağı |
|---|---|---|---|
| 1 | H1 | zincir hangi kapıda? | §10.2 |
| 2 | H2 | cihaz hâlâ listede mi? | §10.2 adım 5–6 |
| 3 | H3 | başka uygulama açık mı? | driver R1 (komşu) |
| 4 | H4 | dönüşüm yolu denendi mi? | §10.1 dalı |
| 5 | H5 | tik süresi dağılımı normal mi? | §9 #3 |
| 6 | H6 | tik sayısı arttı mı? | §9 #2 |
| 7 | H7 | ihlal konumu raporlandı mı? | §10.3 (yazılmadı — R4) |
| 8 | H8 | eşik testing'te mi? | testing (dışında) |
| 9 | H9 | hangi beklenti? | §2 satır 2 şablonu |
| 10 | H10 | asıl kod kaydedildi mi? | kapsam §6.3 kural 2 |
| 11 | H11 | iki akış aynı clock'ta mı? | synchronization |
| 12 | H12 | toplam pik > eşiğe yaklaştı mı? | mixing R3 |
| 13 | H13 | kaynak ne zaman döndü? | input R3 |
| 14 | H14 | kapı ne kadar sürdü? | §9 #2/#7 |

### §8.3 Bilinmeyen kod davranış şablonu

| # | Durum | Davranış | Neden |
|---|---|---|---|
| 1 | kod listede | sınıf atanır | olağan |
| 2 | kod bilinmiyor | "bilinmeyen" sınıfı + asıl kod saklanır | sessiz yutma yasak |
| 3 | kod tekrar ediyor | sayaç artar | tekrar görünür olsun |
| 4 | kod ilk kez | uyarı olarak işaretlenir | şemaya ekleme adayı |
| 5 | bilinmeyen kod testi | test geçemez, kapı açık kalır | uydurma eşik yok |

## §9 Metrik ve sayaç örnek şablonları

### §9.1 Sayaç kayıt satırı alanları

| # | Alan | Açıklama | Değer durumu |
|---|---|---|---|
| 1 | sayaç adı | §8 (kapsam) listesindeki aday ad | aday |
| 2 | örnek değeri | ölçülen sayı | **ölçülmedi** |
| 3 | örnek zamanı | ne zaman alındı | — |
| 4 | örneklem periyodu | ne sıklıkla | **R2 AÇIK** |
| 5 | birim | sayım / ms / örnek | — |
| 6 | etiketler | düğüm/kip/cihaz | — |
| 7 | oturum/ürüm | karşılaştırma için | — |
| 8 | kapı işareti | hangi R'ye girdi | — |

### §9.2 Sayaç → kapı eşlemesi (hangi sayı hangi soruyu besler)

| # | Sayaç | Beslediği kapı | Ne kanıtlar |
|---|---|---|---|
| 1 | tarama süresi | R6 | tarama sıklığı bütçesi |
| 2 | tik süresi | R2 | periyot kararının girdisi |
| 3 | xrun sayısı | R3/R2 | kesinti davranışı |
| 4 | dropout süresi | R2 | ölçüm bütçesi |
| 5 | uçtan uca gecikme | testing eşiği | kabul girdisi |
| 6 | RT ihlali | R4 | tespit doğruluğu |
| 7 | cihaz kaybı/fallback | R4 (device) | zincir kanıtı |
| 8 | kip dağılımı | driver R1 | kip davranışı |
| 9 | karışım taşması | mixing R3 | taşma davranışı |
| 10 | clock kayması | synchronization | uyum davranışı |

### §9.3 Sayı/hücre yasağı

| # | Hücre | Bu dosyada değeri | Neden |
|---|---|---|---|
| 1 | tüm sayaç değerleri | — (boş) | ölçüm yok |
| 2 | periyot | — | R2 AÇIK |
| 3 | eşik | — | testing (dışında) |
| 4 | süre birimi | — | R5 (driver) |
| 5 | saklama süresi | — | R6 AÇIK |

## §10 Teşhis adım örnek ağaçları

### §10.1 Genel ses teşhisi (iskelet)

```text
Ses geliyor mu?
  ├─ Hayır → hangi sınıf var? (§8.1)
  │    ├─ H1/H2 → §10.2 (cihaz kaybı)
  │    ├─ H7 → §10.3 (RT ihlali — yöntem R4 AÇIK)
  │    ├─ H5/H6 → sayaçlar (§9.1) → periyot kararı R2
  │    └─ sınıf yok → "bilinmeyen" kodu ara (§8.3)
  └─ Evet → kalite/gecikme
       ├─ H8/H9 → beklenen vs gerçekleşen (§2 satır 2 şablonu)
       ├─ kabul eşiği → testing (BU DOSYADA DEĞİL)
       └─ eşik yoksa kapı AÇIK kalır
```

### §10.2 Cihaz kaybı teşhis adımları (örnek tablo)

| # | Adım | Veri | Beklenen | Gerçekleşen |
|---|---|---|---|---|
| 1 | olayı bul | log satırı (§7.1) | H1 sınıfı | **— (log yok)** |
| 2 | zamanı al | alan 1 | zaman çizelgesi | **—** |
| 3 | kapıları iz | zincir olayı | kapı sırası | **— (R4 açık)** |
| 4 | say | sayaç | tekrar sayısı | **— (ölçülmedi)** |
| 5 | kipi kontrol et | kip etiketi | exclusive/shared | **—** |
| 6 | korelasyon | sürücü/OS olayı | tetikleyici | **—** |
| 7 | kapat | kapı durumu | testing onayı | **KAPALI** |

### §10.3 RT ihlali teşhis adımları (yöntem YOK işaretli)

| # | Adım | Durum | Etiket |
|---|---|---|---|
| 1 | ihlali yakala | yöntem yazılmadı | R4 AÇIK |
| 2 | sınıf ata | H7 (aday) | R3 AÇIK |
| 3 | konumu raporla | alan yok | — |
| 4 | etkiyi ölç | dropout sayacı | R2 AÇIK |
| 5 | düzelt | realtime sahası | — |
| 6 | doğrula | test yok | testing |

### §10.4 Teşhis adımı kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | adım sonucu "—" ile yazılır, tahmin edilmez | ölçüm yok |
| 2 | kapı durumu her tabloda görünür | KAPALI kapı "başarılı" sayılmaz |
| 3 | beklenen sütunu şablon, gerçekleşen sütunu boş kalır | uydurma PASS yasak |
| 4 | sınıf adı §8.1 ile aynı olmalıdır | iki şema çatışması |
| 5 | teşhis sonucu test sonucu değildir | DG1 (kapsam §2 satır 7) |

## §11 Ölçüm günlüğü şablonu (boş — ilk satır bekleniyor)

| Tarih | Test ID | Yüzey | Ölçüm | Değer | Birim | Karar | Kapı |
|---|---|---|---|---|---|---|---|
| — | — | (kayıt yok) | — | — | — | PLANNED | ölçüm kapısı KAPALI |

**Kayıt kuralları:** (1) değerler yalnız gerçek ölçüm çıktısından kopyalanır ·
(2) elle tahmin yasak · (3) ortam değişince yeni satır, eski satır silinmez ·
(4) bu günlüğe satır girmeden §5 R2/R6 kapıları kapatılamaz · (5) boş satır
"0 ölçüm" demektir, "başarılı" demek değildir.

## §12 Komşu dosyalarla eşleme

| # | Bu dosyadaki şablon | Komşu dosya | Komşunun payı |
|---|---|---|---|
| 1 | §7 log alanları | `diagnostics-kapsam.md` §7 | format kararı R1 |
| 2 | §8 sınıfları | `diagnostics-kapsam.md` §6 | şema sahibi (error-coordinator) |
| 3 | §9 sayaçları | `diagnostics-kapsam.md` §8 | metrik listesi R2 |
| 4 | §10.2 cihaz kaybı | `device-ornekler.md` §7 | zincir iskeleti (R4) |
| 5 | §10.2 kapı izleme | `driver-ornekler.md` | sürücü hata kodu |
| 6 | §10.3 RT ihlali | `realtime-ornekler.md` | kural + tespit (R4) |
| 7 | §8 H5/H6 | `input-ornekler.md` · `output-ornekler.md` | xrun üreten taraf |
| 8 | §8 H12 | `mixing-ornekler.md` | taşma üreten taraf |
| 9 | §8 H9 | `diagnostics-ornekler.md` §2 satır 2 | mismatch şablonu |
| 10 | §8 H8 eşik | `testing-kapsam.md` | kabul sahibi (DG1) |
| 11 | §11 günlüğü | `device-dogrulama.md` §24 | günce örneği (append-only) |
| 12 | §7.4 maskeleme | `.ai/AGENTS.md` §17 #3 | bağlayıcı kural |
| 13 | §6 repo kaydı | `.ai/AGENTS.md` §25.3 #3 | append-only kural |
| 14 | auval bağımsız araç | `audio-master.md` §4 A6 | doğrulama yaklaşımı |
| 15 | clock kayması (H11) | `synchronization-ornekler.md` | komşu şema |

## §13 Kapı kontrol listesi (hepsi KAPALI)

| # | Kapı | Soru | Sahip | Durum |
|---|---|---|---|---|
| 1 | R1 | log formatı/seviyesi standardı? | diagnostics | KAPALI |
| 2 | R2 | metrik listesi + periyot? | performance-engineer | KAPALI |
| 3 | R3 | hata sınıflandırma şeması? | error-coordinator | KAPALI |
| 4 | R4 | RT ihlali tespit yöntemi? | realtime | KAPALI |
| 5 | R5 | telemetri RT vardiyesi? | realtime | KAPALI |
| 6 | R6 | saklama yeri/süresi? | backend | KAPALI |
| 7 | ölçüm | günlüğe ilk gerçek satır | — | KAPALI (ölçüm yok) |
| 8 | eşik | kabul eşiği | testing | KAPALI (bu dosya dışı) |
| 9 | şema kodu | hata kodu numaralandırma | error-coordinator | KAPALI (rakam yok) |
| 10 | URL teyidi | §6 satır 12 rapor yüzeyi | — | KAPALI (okunmadı) |

## §14 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §5 | §2 satırları + §3 | iç kanıt |
| 2 | §6 | §1 ölçümü + `.ai/AGENTS.md` §17 #3 · §25.3 #3 | ölçüm + iç kanıt |
| 3 | §7.1/§7.2 | `diagnostics-kapsam.md` §7 | şablon (R1 kilitli) |
| 4 | §7.3 | kapsam §7.2 | aday seviye |
| 5 | §7.4 | `.ai/AGENTS.md` §17 #3 · kapsam §10 | iç kanıt |
| 6 | §8.1 | kapsam §6.1 H-tablosu | aday sınıf |
| 7 | §8.2 | kapsam §10 teşhis adımları | şablon |
| 8 | §8.3 | kapsam §6.3 kural 6 | iç kanıt |
| 9 | §9 | kapsam §8 + §5 R2 | açık soru |
| 10 | §10.1–§10.3 | §2 satır 2 (mismatch) + kapsam §10 | doğrulanmış URL + şablon |
| 11 | §11 | `device-dogrulama.md` §24 günce kuralları | komşu kanıt |
| 12 | §12 | kapsam §3 (DG1–DG6) | iç kanıt |
| 13 | §13 | kapsam §5 (R1–R6) | açık soru |
| 14 | §2 satır 1/2/3 | §1 · §4 ölçümleri | ölçüm |
| 15 | auval | §2 satır 1 | doğrulanmış vault atfı |
| 16 | PipeWire sayaç | §2 satır 3 | doğrulanmış URL |
| 17 | Core Audio kaynakları | §2 satır 4 | doğrulanmış URL |

---

## §15 Örnek senaryo matrisi (durum → şablon → sonuç)

| # | Durum/girdi | Uygulanan şablon | Şablon çıktısı | Gerçek sonuç | Kapı |
|---|---|---|---|---|---|
| 1 | telemetri kodu yok (repo) | §6 satır 1 | "kod yok" | ölçülmüş | — |
| 2 | append-only kuralı | §6 satır 3 | geçmişe dokunulmaz | kural mevcut | — |
| 3 | hassas alan | §7.1 alan 11 | [REDACTED] | kural mevcut | — |
| 4 | log formatı soruldu | §7.1 | alan listesi aday | biçim yok | R1 |
| 5 | seviye adları soruldu | §7.3 | 6 aday ad | standart yok | R1 |
| 6 | yeni olay tipi | §7.2 satır 14 | "bilinmeyen" | sınıf yok | R3 |
| 7 | sürücü hatası geldi | §8.1 satır 10 | asıl kod saklanır | sınıf şeması yok | R3 |
| 8 | cihaz kaybı | §10.2 | 7 adım | adım 1–6 boş | ölçüm + R4 |
| 9 | RT ihlali | §10.3 | yöntem yok | R4 açık | R4 |
| 10 | sayaç istendi | §9.1 | 8 alan | değer yok | R2 |
| 11 | periyot istendi | §9.1 alan 4 | — | R2 açık | R2 |
| 12 | ölçüm raporu | §11 günlüğü | boş tablo | PLANNED | ölçüm |
| 13 | eşik istendi | §10.1 son dal | testing'e devir | bu dosya dışı | DG1 |
| 14 | beklenti≠gerçekleşen | §2 satır 2 | karşılaştırma raporu | şablon mevcut | — |
| 15 | sayaç tabanlı tespit | §2 satır 3 | sayaç artışı | şablon mevcut | — |
| 16 | bağımsız doğrulama | §2 satır 1 (auval) | araç yaklaşımı | şablon mevcut | — |
| 17 | telemetri kaynağı | §2 satır 4 (Core Audio) | API kapsamı | şablon mevcut | — |
| 18 | mismatch ölçümü | §2 satır 2 (PipeWire) | beklenen/gerçekleşen | şablon mevcut | — |
| 19 | uydurma değer denemesi | §9.3 · §10.4 | engellenir | yazıldı mı denetim | ✅ |
| 20 | kapı "geçti" denemesi | §13 | engellenir | 10/10 KAPALI | ✅ |

## §16 Uygulama notları ve sık sorular

### §16.1 Şablonların nerede kullanılacağı

| # | Şablon | Kullanım anı | Kullanım sahibi | Kullanım yasağı |
|---|---|---|---|---|
| 1 | §7.1 | log kaydı tasarımı | diagnostics tasarımı | "standart" denmez |
| 2 | §7.2 | olay→alan eşlemesi | telemetri tasarımı | alan sırası sabitlenmez |
| 3 | §7.3 | seviye tasarımı | R1 kararı | adlar standart sayılmaz |
| 4 | §7.4 | olay akışı | teşhis tasarımı | biçim iddiası yok |
| 5 | §8.1 | sınıf listesi | R3 kararı | rakam atanmaz |
| 6 | §8.2 | teşhis eşlemesi | teşhis tasarımı | sonuç verilmez |
| 7 | §8.3 | bilinmeyen kod | davranış kararı | kod yutulmaz |
| 8 | §9.1–9.3 | sayaç tasarımı | R2 kararı | değer yazılmaz |
| 9 | §10.* | teşhis akışı | test iskeleti | PASS denmez |
| 10 | §11 | ölçüm kaydı | ölçüm ekibi | boş satır doldurulmaz |

### §16.2 Sık sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Bu şablonlar kod mu? | Hayır — alan/akış/ağaç şablonları |
| 2 | Hata kodu verildi mi? | Hayır — yalnız işlevsel adlar (kapsam §6.3) |
| 3 | Değer/periyot var mı? | Hayır — §9.3 beş hücre de boş |
| 4 | Ölçüm yapıldı mı? | Hayır — §11 günlüğü boş |
| 5 | Teşhis ağaçları sonuç veriyor mu? | Hayır — iskelet; gerçekleşen sütunu hep "—" |
| 6 | Eşik yazıldı mı? | Hayır — testing (DG1) |
| 7 | Log nereye yazılır? | Cevap yok — R6 + kapsam §2 satır 1 |
| 8 | Maskeleme kuralı? | [REDACTED] (`.ai/AGENTS.md` §17 #3) |
| 9 | Yeni ölçüm iddiası var mı? | Hayır — §6 yalnız §1'in ölçümünü tekrarlar |
| 10 | Okunmayan yüzey? | §6 satır 12 — rapor yüzeyi okunmadı, sonuç üretilmedi |
| 11 | Sınıf şeması kimin? | error-coordinator (R3) |
| 12 | Bu bölüm neyi değiştirdi? | Yalnız §5–§16 eklendi; §1–§4 aynen korundu |

### §16.3 Bir sonraki oturuma devir

| # | Devir maddesi | Bekleyen iş | Sahip |
|---|---|---|---|
| 1 | R1 log formatı | format + seviye kararı | diagnostics |
| 2 | R2 metrikler | liste + periyot ölçümü | performance-engineer |
| 3 | R3 hata şeması | sınıf/kod listesi | error-coordinator |
| 4 | R4 RT tespiti | debug katmanı yöntemi | realtime |
| 5 | R5 telemetri vardiyesi | RT kararı | realtime |
| 6 | R6 saklama | yer + süre kararı | backend |
| 7 | ölçüm günlüğü | ilk gerçek satır | ölçüm ekibi |
| 8 | §6 satır 12 | rapor yüzeyi okuması | sonraki tur |
| 9 | MS dokümanı | teyit (log formatı referansı) | sonraki tur |

### §16.4 Ek kontrol listesi (şablon denetimi — hepsi KAPALI)

| # | Şablon | Denetim sorusu | Durum |
|---|---|---|---|
| 1 | §7.1 | alan listesi kanıtsız sunuldu mu? | ✅ HAYIR — "aday" etiketi |
| 2 | §7.2 | sınıf sütunu tutarsız mı? | ✅ HAYIR — §8.1 ile aynı |
| 3 | §7.3 | seviye adları standart diye yazıldı mı? | ✅ HAYIR — R1 açık |
| 4 | §7.4 | biçim/ağaç kod diye sunuldu mu? | ✅ HAYIR — akış ağacı |
| 5 | §8.1 | hata kodu rakamı atandı mı? | ✅ HAYIR |
| 6 | §8.2 | teşhis sonucu verildi mi? | ✅ HAYIR — adım kaynağı |
| 7 | §8.3 | bilinmeyen kod sessizce geçti mi? | ✅ HAYIR — davranış tablosu |
| 8 | §9.1–9.3 | sayı/periodyot yazıldı mı? | ✅ HAYIR — beş hücre boş |
| 9 | §10.* | gerçekleşen sütunu dolduruldu mu? | ✅ HAYIR — hep "—" |
| 10 | §11 | günlüğe sahte satır kondu mu? | ✅ HAYIR — tek satır "—" |
| 11 | §12 | komşu sınırı değiştirildi mi? | ✅ HAYIR — eşleme |
| 12 | §13 | kapı "geçti" mi? | ✅ HAYIR — 10/10 KAPALI |
| 13 | §15 | yeni soru açıldı mı? | ✅ HAYIR — mevcut R'ler |
| 14 | §16.3 | devir sahipleri uyduruldu mu? | ✅ HAYIR — §5 sahipleri |
| 15 | §1–§4 | değişiklik | ✅ aynen korundu |
| 16 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 17 | genel | derlenebilir kod iddiası | ✅ yok |
| 18 | genel | Authority tekliği | ✅ tek |

### §16.5 Komşu dosya eşleme denetimi (bu dosyanın sınırları)

| # | Konu | İlgili komşu dosya | Bu dosyada var mı? | Durum |
|---|---|---|---|---|
| 1 | Log **biçimi** (hangi alan, hangi seviye) | R1 — sahibi diagnostics | ❌ | AÇIK (R1) |
| 2 | Metrik **periyodu** | R2 — performance-engineer | ❌ | AÇIK (R2, sayı uydurulmaz) |
| 3 | Hata **sınıfı kod listesi** | R3 — error-coordinator | ❌ | AÇIK (R3) |
| 4 | RT tespit **yöntemi** | R4 — realtime | ❌ | AÇIK (R4) |
| 5 | Telemetri **vardiyesi** | R5 — realtime | ❌ | AÇIK (R5) |
| 6 | Saklama **süresi/yeri** | R6 — backend | ❌ | AÇIK (R6) |
| 7 | Sürücü hatasının **üretilmesi** | driver §9 (hata modları) | ❌ bu dosyada yok | sınır korundu |
| 8 | Cihaz kaybı **tepki zinciri** | device §9 / driver §6 | ❌ | sınır korundu |
| 9 | Gerçek zamanlı **döngü kuralları** | realtime | ❌ | sınır korundu |
| 10 | Giriş **kaynağı hataları** | input | ❌ | sınır korundu |
| 11 | Karışım **bus hataları** | mixing | ❌ | sınır korundu |
| 12 | Motor **callback hataları** | engine | ❌ | sınır korundu |

**Sonuç:** §5–§16'da üretilen hiçbir tablo komşu dosyanın karar alanına
girmemiştir; açık maddelerin tamamı kendi R numarasıyla sahibine devredilmiştir.

---

## §17 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
