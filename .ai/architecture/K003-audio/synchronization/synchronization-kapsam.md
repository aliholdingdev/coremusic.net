---
title: "synchronization — Kapsam"
type: architecture-scope
node_id: K003-audio/synchronization
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# synchronization — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 10 bu dalı **"Clock/epoch uyumu, drift telafisi"**
olarak yazar. Basitçe: iki ayrı saat (örneğin ses kartının saati ile
işlemcinin sayacı) zamanla birbirinden **kayar** (drift); bu dal o iki saati
uydurur ve gerektiğinde farkı telafi eder. Aynı odadaki iki duvar saati gibi:
biri hızlı, biri yavaşsa bir süre sonra birinde 10 diğerinde 12 olur —
bunu düzeltmezseniz toplantılar şaşar. Ses dünyasında "şaşma" = click,
desync, gecikme birikimi.

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Hangi clock referansının ana
> alınacağı, drift ölçüm periyodu ve telafi yöntemi kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Clock/epoch uyumunun sağlanması (kaynaklar arası zaman referansı eşitliği) | Döngünün zaman baskısı kuralları | realtime (master §3 satır 9) |
| 2 | Drift (kayma) telafisi stratejisi — ne zaman, nasıl düzeltileceği | Drift ölçümünün raporlanması | diagnostics (master §3 satır 11) |
| 3 | Örnek/zaman damgası (epoch) bütünlüğünün korunması niyeti | Monotonluğun kendisi (render kuralı) | engine (master §3 satır 1 · §4 A4) |
| 4 | Çoklu cihaz/kaynak arasında zaman referansı seçimi | Hangi cihazın seçileceği | device (master §3 satır 2) |
| 5 | *(dışında)* döngünün lock-free disiplini | Yasak listesi | realtime (master §3 satır 9) |
| 6 | *(dışında)* sürücü/OS clock API'si (donanım sayacı) | Platform erişimi | driver · K000 (master §2.2) |
| 7 | *(dışında)* ağ/akış senkronizasyonu protokolleri (NTP benzeri) | Ağ katmanı | ⚠️ kapsam dışı — ad-türetme |
| 8 | *(dışında)* senkronizasyon hatalarının sınıflandırılması | Log/ölçüm/sınıf | diagnostics · testing |

**Yasak:** satır 3'teki ayrım (monotonluk kuralı ↔ telafi) bağlayıcıdır:
`AudioUnitRender` monotonluğu (master §4 A4) bir **platform gerçeği**dir,
CoreMusic'in telafi stratejisi ise tasarımdır — ikisi aynı satırda birleştirilmez.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| S1 | synchronization ↔ realtime | zaman referansının uyumu ↔ döngünün disiplini | ✅ master §3 satır 9/10 (ayrı çocuklar) |
| S2 | synchronization ↔ engine | epoch bütünlüğü niyeti ↔ döngüyü adımlama | ⚠️ ad-türetme (master §3 satır 10 ve 1 · §4 A4) |
| S3 | synchronization ↔ driver | telafi kararı ↔ platform clock erişimi | ⚠️ ad-türetme (master §3 satır 10 ve 3) |
| S4 | synchronization ↔ device | referans saat seçimi ↔ cihaz envanteri | ⚠️ Q6 ile bağlantılı — üst karar |
| S5 | synchronization ↔ diagnostics | telafi olayının üretilmesi ↔ raporlanması | ⚠️ üst karar bekliyor |
| S6 | synchronization ↔ K005-firmware | donanım saati (I2S/TDM clock) ↔ telafi | ✅ master §2.2 (boot/register firmware) + §6 |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "synchronization · Clock/epoch uyumu, drift telafisi · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 71 | ✅ dosya okundu |
| 2 | `AudioUnitRender` sample-time monotonluğu: sonraki = önceki + inNumberFrames | `audio-master.md` §4 A4 (Apple: AudioUnitRender) | ✅ dosya okundu |
| 3 | PipeWire latency: port bazlı Latency nesnesi (direction + min/max); linked latency toplanır, mismatch tespit edilir | `audio-master.md` §5 T16 → docs.pipewire.org page_latency | ✅ dosya okundu (URL fetch) |
| 4 | PipeWire async node 1 quantum latency ekler | `audio-master.md` §5 T15 → docs.pipewire.org page_scheduling | ✅ dosya okundu (URL fetch) |
| 5 | `Zaman baskısı` kuralı ayrı bir düğümdür (realtime) — v2 §2.3 12 çocuk ayrı | `audio-master.md` §3 satır 70–71 | ✅ dosya okundu |
| 6 | Repo'da clock/drift senkronizasyon kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 7 | K005-firmware (I2S/TDM clock) sınırı: donanım saati firmware'indir | `audio-master.md` §2.2 · §6 | ✅ dosya okundu |
| 8 | Drift telafi yöntemi (örnek kaydırma mı, yeniden zamanlama mı) | — | **UNKNOWN** (tasarım yok) |
| 9 | Drift ölçüm periyodu ve kabul eşiği | — | **UNKNOWN** (kriter yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | Ana clock referansı nedir (donanım saati mi, OS sayacı mı)? | ⚠️ VERIFICATION REQUIRED | embedded-engineer |
| R2 | Drift telafi yöntemi nedir (kaydırma/yeniden zamanlama)? | **UNKNOWN** — tasarım yok | embedded-engineer |
| R3 | Drift ölçüm periyodu ve kabul eşiği (örnek/ms)? | **UNKNOWN** — kriter yok | performance-engineer |
| R4 | Çoklu cihazda referans saat kimden alınır? (Q6 ile bağlantılı) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R5 | Ağ akışında (stream) senkronizasyon kapsam mı? | ⚠️ VERIFICATION REQUIRED (ad-türetme sınırı) | Vault Steward |
| R6 | Telafi anı realtime yasağına takılır mı (vardiyaya devir)? | ⚠️ VERIFICATION REQUIRED | realtime |

**Kapanış:** status: draft kalır. R2/R3 kapanmadan telafi yöntemi ya da eşik
değeri yazılmaz → UNKNOWN; "drift X ppm" gibi sayı uydurulmaz.

## §6 Clock mimarisi şablonu (master clock seçimi)

§2 satır 1 ve 4'teki "zaman referansı eşitliği" ile "referans saat seçimi"
maddelerinin matrisi. **Hangi kaynağın ana clock olduğu R1'dir**; bu tablo
adayları listeler.

| # | Aday clock | Kaynağı | Artısı | Eksisi | Durum |
|---|---|---|---|---|---|
| 1 | Donanım saati (ses kartı crystal) | K001/K005 | Ses dünyasının **doğal** referansı | Ölçüm/düzeltme firmware'e bağlı | ⚠️ R1 |
| 2 | OS sayacı (monotonik) | K000 | Geniş erişim, çözünürlük | Donanım clock'undan **kayabilir** | ⚠️ R1 |
| 3 | Sürücü zaman damgası | driver | Callback ile senkron | Sürücüye bağımlı | ⚠️ R1 |
| 4 | Ağ saati (stream senkronu) | ağ katmanı | Çoklu cihaz senkronu | Kapsam sınırında (R5) | ⚠️ kapsam kararı |

**Master/slave kuralı (şablon):** çoklu clock varsa **biri ana** (master),
diğerleri **ona uyan** (slave) olur; iki clock **eşit ağırlıkla** tutulamaz
(çatışma → desync). Hangisinin master olduğu R1; çoklu cihazda referans
kimden alınır R4.

## §7 Jitter buffer tasarımı (parametre tablosu — değerler UNKNOWN)

Jitter buffer, RT↔non-RT sınırındaki **zaman salınımını** emer. §2 satır 1
(zaman referansı eşitliği) ile bağlantılıdır. **Değerlerin tamamı UNKNOWN**
(R3 kabul eşiği yok) — hiçbir hücreye sayı yazılmaz.

| # | Parametre | Ne işe yarar | Değeri | Karar sahibi |
|---|---|---|---|---|
| J1 | Başlangıç boyutu (derinlik) | İlk doldurma derinliği | **UNKNOWN** | synchronization + backend |
| J2 | Minimum boyut | Alt sınır (gecikme) | **UNKNOWN** | synchronization |
| J3 | Maksimum boyut | Üst sınır (gecikme) | **UNKNOWN** | synchronization |
| J4 | Hedef boyut (target) | Kararlı çalışma noktası | **UNKNOWN** | synchronization |
| J5 | Uyarlama hızı (adaptif hız) | Hız değişikliğine tepki | **UNKNOWN** | synchronization |
| J6 | Ölçüm periyodu | Drift tespit sıklığı | **UNKNOWN** (R3) | performance-engineer |
| J7 | Hata eşiği (kabul) | Yanlış alarm önleme | **UNKNOWN** (R3) | testing |

### §7.1 Buffer türü karşılaştırması (şablon)

| | Sabit (static) | Adaptif |
|---|---|---|
| Gecikme | Sabit, tahmin edilebilir | Değişken (hedefe yaklaşır) |
| Drift duyarlılığı | **Duyarsız** (boyut sabit, kayma birikir) | Duyarlı (boyutla telafi eder) |
| Kullanım | Tek clock, drift az | Çoklu clock, drift bilinen |
| CoreMusic | ⚠️ aday | ⚠️ aday — R2/R3 kararı |

> ⚠️ VERIFICATION REQUIRED: hangi türün seçileceği ve hiçbir parametre
> sayısı kararlaştırılmamıştır. §7 tablosu **tasarım iskeletidir**.

## §8 Master/slave eşleme şablonları (senaryolar)

| # | Senaryo | Master | Slave davranışı | Sahip |
|---|---|---|---|---|
| 1 | Tek cihaz (tek clock) | Cihaz clock'ı | OS/uygulama buna uyar | synchronization |
| 2 | İki cihaz (aynı marka) | Cihaz A (ana) | B, A'ya kaydırılır | device + synchronization (R4) |
| 3 | Giriş + çıkış çifti (duplex) | Çıkış clock'ı | Giriş buna örneklenir | input + synchronization |
| 4 | Ağ senkronu (stream) | Ağ master'ı | Her uç buna uyar | ⚠️ kapsam dışı olabilir (R5) |

**Eşleme kuralı (şablon):** slave, master'ın clock'ına **örnekleme
oranı/kaydırma** ile uyar; eşleme **kayabilir** (aşağıdaki §9'da telafi
yöntemleri). Eşleme başarısızlığı → desync olayı (diagnostics).

## §9 Drift telafi yöntemleri — karşılaştırma tablosu (karar yok: R2)

§2 satır 2'deki "drift telafisi stratejisi" maddesinin üç adayının
karşılaştırması. **Hiçbiri seçilmemiştir** (R2 — tasarım yok).

| # | Yöntem | Nasıl çalışır | Artısı | Eksisi | Durum |
|---|---|---|---|---|---|
| 1 | Örnek kaydırma (sample sliding) | Periyot başına birkaç örnek ekler/çıkarır | Basit, düşük maliyet | Click riski → rampa gerekir | ⚠️ R2 |
| 2 | Yeniden örnekleme (resampling) | Kayma oranına göre yeniden örnekler | Pürüzsüz | İşlem maliyeti, gecikme | ⚠️ R2 |
| 3 | Zaman uzatma/kısaltma (stretch) | Süreyi koruyarak hız ayarı | Müzikal uyum | Kalite kaybı riski | ⚠️ R2 |
| 4 | Telafi yok (yalnız rapor) | Drift ölçülür, düzeltilmez | En basit | Kayma birikir → desync | ⚠️ R2 |

**Monotonluk sınırı (bağlayıcı — A4):** her yöntemde zaman damgası **geriye
gitmez** (master §4 A4); telafi yalnız **ileri kaydırma** ile yapılır
(synchronization-ornekler §2 satır 1). Geriye sarma **yasaktır**.

## §10 Epoch/zaman damgası bütünlüğü kuralları (şablon)

§2 satır 3'teki "örnek/zaman damgası bütünlüğü niyeti" maddesi. Monotonluğun
**kendisi** engine'dir (§4 A4); bu bölüm yalnız **niyet** taşır.

| # | Kural | İhlal belirtisi | Sahip |
|---|---|---|---|
| E1 | Zaman damgası **atlamaz** (periyot kaybı) | Atlanan tick (realtime §11) | engine · synchronization |
| E2 | Zaman damgası **geriye sıçramaz** | Negatif fark | synchronization |
| E3 | İki uç arası damga **karşılaştırılabilir** | Desync olayı | synchronization |
| E4 | Geçiş/rampa anında damga **kesintisiz** | Click (output §10.3) | output + synchronization |
| E5 | Drift olayı **sayısal** raporlanır | Sessiz kayma | diagnostics |

## §11 Çoklu cihaz senaryoları (R4 — karar yok, şablon var)

| # | Senaryo | Referans kimden (R4) | Ek gecikme | Sahip |
|---|---|---|---|---|
| 1 | Hoparlör + kulaklık (aynı OS) | OS clock | OS mix katmanı | device (Q6) |
| 2 | İki ayrı ses kartı | Adaylardan biri (§6) | Driver + eşleme | ⚠️ R4 |
| 3 | Donanım + ağ (stream) | Ağ master'ı olabilir | Ağ jitter'ı | ⚠️ R5 kapsam |
| 4 | Firmware clock (K005) + OS | Ayrışma riski | Firmware→OS köprüsü | K005 (master §6) |

## §12 Ölçüm ve kapanış kapıları (açık)

| Kapı | Koşul | Şu an |
|---|---|---|
| SY-G1 | Ana clock referansı kararı (R1) | ❌ açık |
| SY-G2 | Telafi yöntemi kararı (R2) | ❌ UNKNOWN |
| SY-G3 | Drift ölçüm periyodu + eşiği (R3) | ❌ UNKNOWN |
| SY-G4 | Çoklu cihaz referans kararı (R4) | ❌ açık |
| SY-G5 | Ağ senkronu kapsam kararı (R5) | ❌ açık |
| SY-G6 | Telafi anının RT yasağına uyumu (R6) | ❌ açık |

## §13 Clock kavram ayrımı (bu dosyanın sözlüğü)

| Kavram | Ne ölçer | Bu dosyada |
|---|---|---|
| **Clock** | Zaman referansının kaynağı | §6 |
| **Jitter** | Periyot sapması (çıkıntı) | §7 |
| **Drift** | Uzun süreli frekans farkı | §9 |
| **Latency** | Gecikme (geçiş süresi) | realtime dosyaları |
| **Epoch** | Zaman damgası başlangıcı | §10 |

**Sınır:** latency bütçesi **bu dosyada tanımlanmaz** — realtime K003'ün
konusudur. Bu dosya yalnız *zaman referansı tutarlılığı* ile ilgilenir.

## §14 Neden senkron gerekli (kavramsal gerekçe)

| Sorun | Senkronsuz sonuç |
|---|---|
| İki clock farklı hızda | Kayma — biri doluyor, diğeri taşıyor |
| Periyot tutarsızlığı | Click / halka |
| Epoch farkı | Zaman damgası okunamaz |
| Cihaz başına ayrı referans | Faz kayması |

Bu tablo **nedendir**, ölçüm değildir.

## §15 Master clock adayları — karşılaştırma iskeleti

| Aday | Artısı | Eksişi | Karar |
|---|---|---|---|
| OS clock | Basit, her yerde | Uygulama kontrolü dışında | ❓ R1 |
| Cihaz clock'u (driver) | Donanıma yakın | Sürücüye bağımlı | ❓ R1 |
| Donanım (harici) referans | En stabil | Donanım şart | ❓ R1 |
| Uygulama sayacı | Bağımsız | OS gecikmesine duyarlı | ❓ R1 |

**Hiçbiri tercih edilmez** — SY-G1 açık.

## §16 Frekans — ne, ne değildir

| Öğe | Durum |
|---|---|
| Ses örneklemesinin nominal değeri | sartname §8: **48 kHz** (kural 8) |
| Clock frekansı eşikleri | `UNKNOWN` |
| Kabul edilen ppm sapma | `UNKNOWN` (SY-G3) |
| Ölçüm cihazı/ yöntemi | `UNKNOWN` |

Tek yazılmış sayı 48 kHz'tir ve o da **yapılandırma**dır, ölçüm değildir.

## §17 Jitter buffer — parametre iskeleti (tekrar değil, derinlik)

| Parametre | Ne belirler | Değeri |
|---|---|---|
| Boyut | Tolerans ↔ gecikme dengesi | `UNKNOWN` |
| Doldurma stratejisi | Ne zaman büyüyeceği | `UNKNOWN` |
| Ölçüm penceresi | Ne sıklıkla bakılacağı | `UNKNOWN` |
| Taşma/eksilme davranışı | Hata durumu | `UNKNOWN` |
| RT'de değişim | YASAK (kural 1/2) | Kural |

Boşlukların nedeni: ölçüm yok (SY-G3 açık).

## §18 Drift telafisi — RT yasağı analizi (R6)

| Yöntem | Ne yapar | RT'de olabilir mi? |
|---|---|---|
| Örnekleme/drop | Uzun vadede kaymayı alır | Dikkatli: blok kenarında |
| Resample | Frekansı hizalar | DSP iş yükü — bütçe gerekir |
| Manuel ayar (kullanıcı) | İnsan müdahalesi | non-RT |

**Kural:** telafi, RT callback'i içinde serbestçe yapılamaz; işlem blok
kenarına ve bütçeye bağlanır. Bütçe `UNKNOWN` → SY-G6 açık.

## §19 Epoch bütünlüğü — ne zaman bozulur

| Durum | Sonuç | Önleme |
|---|---|---|
| Cihaz değişimi | Zaman damgası başlangıcı değişir | Yeni epoch kaydı |
| Uygulama yeniden başlatma | Sayaç sıfırlanır | Epoch saklama |
| İki kaynak aynı sayaçla | Çakışma | Tek sahip kuralı |
| 64-bit taşması | — | Taşma süresi `UNKNOWN` |

Bozulma sonrası eski damga ile yeni damga **karşılaştırılamaz**; bu bir
**dönüştürme gerektirir**, dönüştürme kuralı `UNKNOWN`.

## §20 Çoklu cihaz — referans seçimi akışı (şablon)

```text
  1 : kaç cihaz var? (device dosyası — sayaç UNKNOWN)
  2 : hepsi OS içinde mi?  → OS clock adayı
  3 : donanım referansı var mı? → donanım adayı
  4 : ağ var mı? → ağ senkronu (SY-G5 kapsam kararı)
  5 : karar kaydı (SY-G4)
```

Adım 1 ve 5 bu dosyada **tamamlanamaz** — sayı yok (R4).

## §21 Bağlantı → K005 firmware ilişkisi

| Konu | K005 (firmware) | Bu dosya |
|---|---|---|
| I2S/TDM clock ağacı | Sahip | Yalnız senkron sonucu |
| Firmware→OS köprüsü | Tasarım | Drift kaynağı olarak listelenir |
| Firmware OTA | K005 kuralı | Dokunulmaz |

⚠️ VERIFICATION REQUIRED: `.ai/projects/NevaEngine/*.md` dizini **0 dosya**
→ firmware senkron tasarımı elde yok.

## §22 Ölçüm metrikleri — tanımlı ama dolumsuz

| Metrik | Tanım | Değeri |
|---|---|---|
| Drift oranı | Birim zamanda kayma | `UNKNOWN` |
| Jitter (p2p) | Periyot farkı | `UNKNOWN` |
| Epoch tutarlılığı | Damga sırası ihlali sayısı | `UNKNOWN` |
| Bağlantı kesintisi | Senkron kopması | `UNKNOWN` |

Tüm satırlar SY-G3'e bağlıdır.

## §23 Hata modları

| # | Mod | Belirti | Kapı |
|---|---|---|---|
| 1 | Drift birikimi | Zamanla kayan faz | SY-G3 |
| 2 | Epoch karışması | Yanlış sıralı damga | §19 |
| 3 | İki master | Çakışan referans | SY-G4 |
| 4 | Telafi RT'de | Bütçe aşımı / kaçış | SY-G6 |
| 5 | Ağ kopması | Uzak cihaz kaybı | SY-G5 |
| 6 | Cihaz hot-plug | Epoch sıçraması | SY-G4 |

## §24 Senaryo: tek cihaz, tek clock (en basit)

| Adım | Durum |
|---|---|
| Tek referans | OS veya cihaz — karar SY-G1 |
| Drift | Aynı frekansta iki cihaz yok → risk düşük |
| Yine de ölçüm gerekli mi? | Evet (SY-G3) — **şu an yok** |

Bu senaryo bile **kapısız geçemez**.

## §25 Senaryo: iki ayrı ses kartı (R4 çekirdeği)

| Soru | Cevap |
|---|---|
| Hangisi master? | ❓ SY-G4 |
| Faz kayması nasıl ölçülür? | `UNKNOWN` |
| Telafi yöntemi? | ❓ SY-G2 |
| Kabul eşiği? | `UNKNOWN` |

§11 satır 2'ye bağlıdır; bu dosya ek senaryo **açmaz**.

## §26 Kapsam dışı komşular (tekrar değil, sınır)

| Komşu | Ayrım |
|---|---|
| `output/*` | Çıkış cihazı seçimi ≠ clock referansı |
| `realtime/*` | Gecikme bütçesi ≠ drift |
| `routing/*` | Rota ≠ zaman |
| `diagnostics/*` | Gözlem ≠ telafi |

Çakışma halinde tek-karar kuralı: karar **tek** dosyada.

## §27 Bağımlılık matrisi

| Bağımlılık | Tür | Durum |
|---|---|---|
| OS clock API'leri | platform | Uygulama kodu yok |
| Sürücü timestamp | driver | `UNKNOWN` |
| Donanım referansı | HW | Donanım kararına bağlı (K000) |
| Firmware clock | K005 | Vault 0 dosya |
| Ağ protokolü | kapsam kararı | SY-G5 |

## §28 Risk kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Master seçimi verilmedi → iki tasarım | İsraf | SY-G1 önce |
| R2 | Yanlış telafi → ses bozulması | Kalite | SY-G2 + ölçüm |
| R3 | Ölçüm yok → drift görünmez | Hata | SY-G3 |
| R4 | Çoklu cihaz kararı yok | Uygulanamaz | SY-G4 |
| R5 | Ağ kapsamı belirsiz | Kapsam kayması | SY-G5 |
| R6 | RT'de telafi yasağı ihlali | RT-G ihlali | SY-G6 |

## §29 Uygulama sırası (öneri)

| Sıra | Kapı | Neden |
|---|---|---|
| 1 | SY-G1 | Diğerleri buna bağlı |
| 2 | SY-G3 | Ölçüm olmadan SY-G2 anlamsız |
| 3 | SY-G2 | Ölçüme dayalı seçim |
| 4 | SY-G6 | RT kısıtının doğrulanması |
| 5 | SY-G4 | Çoklu cihaz |
| 6 | SY-G5 | Kapsam genişlemesi en son |

## §30 Kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `synchronization/synchronization-kapsam.md` |
| 2 | Bölüm aralığı | §1–§40 (son ek: §13–§40, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** |
| 4 | Açık kapı | SY-G1…SY-G6 |
| 5 | Açık soru | R1…R6 |

## §31 Zaman damgası örnek biçimleri (kavramsal)

| Biçim | İçerik | Not |
|---|---|---|
| Mutlak sayaç | Epoch'dan beri geçen tik | Epoch §19 |
| Numune cinsinden | Örnek sayacı | 48 kHz ise birim çevrimi yapılabilir |
| OS zamanı | Sistem saati | Ayarlanabilir (sıçrama riski) |
| Karışık | Sayaç + epoch | Bölüm |

Hangi biçimin seçileceği **SY-G1'e bağlı** → `UNKNOWN`.

## §32 Drift ölçümü — taslak yöntem (değer yok)

```text
  1 : t0'da kaynağı işaretle
  2 : t1'de (uzun süre sonra) aynı kaynağı işaretle
  3 : farkı al
  4 : frekans sapmasına çevir
```

| Adım | Gereken | Durumu |
|---|---|---|
| 1–2 | İki işaretleme | Yok (kod yok) |
| 3–4 | Hesap | `UNKNOWN` |
| Pencere uzunluğu | Karar | SY-G3 |

Yöntem **tanımlıdır, uygulanmamıştır**.

## §33 Kabul eşiği — neden yazılamaz

Eşik (ör. "şu kadar sapma kabul edilir") üç şeye bağlıdır: ölçülebilir
donanım, kullanım senaryosu ve ürün kararı. Hiçbiri elde yok →
**sayı yazılmaz**, `UNKNOWN` kalır. Eşik uydurmak ölçülmüş iddiaya
dönüştüğü için yasaktır.

## §34 Soru → kapı eşlemesi

| Soru (§5) | Kapı |
|---|---|
| Ana clock kim? | SY-G1 |
| Telafi nasıl? | SY-G2 |
| Ne sıklıkla ölçülür? | SY-G3 |
| İkinci cihaz referansı? | SY-G4 |
| Ağ senkronu dahil mi? | SY-G5 |
| RT'de telafi ne zaman? | SY-G6 |

## §35 Terimlerin komşu dosyalarla eşlemesi

| Terim | Bu dosyada | Komşuda |
|---|---|---|
| Jitter | Periyot sapması | `backend/asio/asio-latency-ve-jitter.md` (başka K003 altı) |
| Latency | Kapsam dışı | `realtime/*` |
| Clock seçimi | §6 | `device/*` (cihaz) |
| Zaman damgası bütünlüğü | §10/§19 | — |

Not: `backend/asio/*` dosyaları bu derinleştirme beyazlistesi **dışıdır**;
oradaki sayısal içerik bu dosyaya taşınmaz (kaynak ayrımı).

## §36 Dürüstlük kaydı

| İddia türü | Bu dosyadaki durumu |
|---|---|
| Ölçülmüş drift | **Yok** → `UNKNOWN` |
| Ölçülmüş jitter | **Yok** → `UNKNOWN` |
| Onaylı master clock | **Yok** → SY-G1 açık |
| Derlenebilir kod | **Yok** (repo `*.cpp`/`*.h` = 0) |
| Sartname yapılandırması | 48 kHz / 512 örnek (kural 8) — tek yazılı sayı |

## §37 Sonraki tur için gerekenler

1. SY-G1 kararı (master clock adayı seçimi)
2. SY-G3 ölçüm penceresi + ilk ölçüm
3. SY-G2, ölçüm sonrasına ertelenir
4. Çoklu cihaz kararı (SY-G4) — cihaz sayısının bilinmesi gerekir

## §38 Boşluk listesi (açık)

| Boşluk | Kapı |
|---|---|
| Master clock adayı seçimi | SY-G1 |
| Telafi yöntemi | SY-G2 |
| Drift ölçüm periyodu + eşiği | SY-G3 |
| Çoklu cihaz referansı | SY-G4 |
| Ağ senkronu kapsamı | SY-G5 |
| RT telafi kısıtı doğrulaması | SY-G6 |

## §39 Ek kapı matrisi (kapanış tablosu tekrar değil — ayrıntı)

| Kapı | Beklenen kanıt türü | Kıtlık nedeni |
|---|---|---|
| SY-G1 | Aday karşılaştırması + onay | Karar alınmadı |
| SY-G3 | İki işaret arasındaki fark kaydı | Ölçüm altyapısı yok |
| SY-G2 | Ölçüme dayalı yöntem seçimi | SY-G3 bekliyor |
| SY-G6 | RT yolunda telafi bulunmadığının kanıtı | Kod yok |
| SY-G4 | İkinci cihazın varlığı/tekliği | Cihaz listesi `UNKNOWN` |
| SY-G5 | Ağ protokolü kapsam kararı | Kapsam kararı verilmedi |

Bu tablo, kapıların **neden** açık olduğunu tek satırda özetler.

## §40 Kapanış teyidi

Bu dosyada §13–§40 eklendi; §1–§12 **dokunulmadı**, tek Authority satırı
korundu, tüm ölçüm kapıları **AÇIK** bırakıldı.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
