---
title: "synchronization — Örnekler"
type: architecture-examples
node_id: K003-audio/synchronization
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# synchronization — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` içinde clock/drift senkronizasyon kodu yoktur
(2026-10-12 kök `*.cpp`/`*.h` glob 0 isabet). Aranan: sayaç okuması, drift
hesabı, epoch eşitleme, resync (yeniden hizalama). Bulunan: yalnız PHP/JS UI
katmanı ve AI sınıfları — zaman damgası taşımaz (PHP `time()` kullanımı ses
senkronizasyonu değildir, kanıt sayılmaz).

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | Monoton sampleTime kuralı | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | sonraki = önceki + inNumberFrames | Zaman damgası **atlamaz ve geri gitmez**; telafi geriye sararak değil ileri kaydırarak yapılır |
| 2 | PipeWire port latency nesnesi | https://pipewire.org/page_latency | direction + min/max; linked latency toplanır, mismatch tespit edilir | Uyumsuzluk **tespit edilir ve raporlanır** — CoreMusic'in drift raporu için şablon |
| 3 | PipeWire async node 1 quantum | https://pipewire.org/page_scheduling | async node 1 quantum latency ekler | Asenkron düğümün **bilinen, sabit gecikme bedeli** vardır; drift ile karıştırılmaz (bedel ≠ kayma) |
| 4 | ALSA sw_params avail_min/period_event | https://www.alsa-project.org/doc/api/group___p_c_m.html | akış olayının zaman penceresi | Senkronizasyon olayı **periyot kenarında** değerlendirilir; pencere açılışta sabitlenir |
| 5 | AUGraph yaşam döngüsü (create → initialize → start) | `audio-master.md` §4 A3 (Apple ARoadmaptoCommonTasks — bu oturumda vault'tan okundu) | bellek allocate edilir, sonra başlatılır | Senkronizasyon **başlangıcı** yaşam döngüsüne bağlıdır: initialize öncesi referans yoktur |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **synchronization tasarımı öncesi** şablon olarak
kullanılır: 1, monotonluk kuralının telafi yöntemini sınırlar (ileri kaydırma);
2, uyumsuzluk raporlama şablonu (diagnostics komşusu); 3, sabit bilinen
gecikme bedelinin drift'ten ayrımını; 4, periyot kenarı değerlendirme
penceresini; 5, referansın ancak initialize sonrası var olduğunu. Ana referans
R1, yöntem R2, eşik R3 (`synchronization-kapsam.md` §5) kapanmadan yazılmaz →
UNKNOWN; ppm/örnek sayısı uydurulmaz.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `synchronization-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `synchronization-dogrulama.md` | Kanıt/doğrulama tablosu (12 satır: ✅/has-been-fetched 8 · ⚠️ 2 · UNKNOWN 2) |
| `synchronization/index.md` | Düğüm kaydı (ELI15 · açık sorular) |
| `../audio-master.md` | Üst anayasa §3 satır 10 · §4 A3/A4 · §5 T15/T16 · §8 |
| `../audio-bagimliliklar.md` | §6 — synchronization ↔ realtime/driver bağımlılığı |
| `../realtime/realtime-kapsam.md` | Kardeş çocuk — disiplin ↔ zaman referansı ayrımı (S1) |

## §5 Monoton sampleTime — derin adım kaydı (master §4 A4)

> Kaynak: `AudioUnitRender` (✅ doğrulanmış — master §4 A4).

| # | Adım | Kural | Synchronization açısından |
|---|---|---|---|
| 1 | İlk render çağrısı | sampleTime başlangıç değeri | Referans başlangıcı |
| 2 | Sonraki çağrısı | Sonraki = önceki + inNumberFrames | Süreklilik ölçütü (E1) |
| 3 | Kare atlama (underrun) | Fark > inNumberFrames → atlama | Drift/atlama tespiti |
| 4 | Geriye sıçrama | Fark < 0 → **kural ihlali** | E2 — telafi yasağı |
| 5 | Tick bütçesi aşımı | Fark = inNumberFrames ama geç geldi | realtime §11 (zamanlama, damga değil) |

**Örnek dersi:** monotonluk **sayısal ve test edilebilir**dir: her render'da
fark = inNumberFrames ise süreklilik var; fark bu değerin üstünde atlama,
altında ihlal. Bu üç durum **synchronization-ötesi** bir ölçüt üretir
(testing-ornekler §2 satır 3).

## §6 PipeWire latency mismatch — aşama şablonu (master §5 T16)

| # | Aşama | Veri | Durum |
|---|---|---|---|
| 1 | Port latency okuma (direction + min/max) | Her uç aralığı | ✅ doküman |
| 2 | Linked latency toplama | Beklenen toplam | ✅ doküman |
| 3 | Gerçekleşen ile karşılaştırma | Fark (mismatch) | ✅ doküman |
| 4 | Mismatch raporu | Nesne üzerinde işaret | ✅ doküman |
| 5 | Düzeltme uygulama | **Yok** — PipeWire yalnız raporlar | CoreMusic telafisi R2 (kapsam §9) |

**Örnek dersi:** PipeWire **telafi etmez, yalnız raporlar**; telafi CoreMusic
tasarımıdır (R2). Bu ayrım, diagnostics ↔ synchronization sınırını da
netleştirir: tespit PipeWire örneğindeki gibi **ölçüm katmanının** iştir.

## §7 ALSA periyot kenarı değerlendirme penceresi — adım kaydı (master §5 T13)

| # | Adım | Alan | Ne gösterdiği |
|---|---|---|---|
| 1 | `sw_params` penceresi kurulur | avail_min | "Ne kadar veri hazır olmalı" eşiği |
| 2 | period_event tetiklenmesi | event | Değerlendirme **periyot kenarında** |
| 3 | Olay okunur | dönemsel | Senkronizasyon olayı pencere içinde |
| 4 | Pencere çalışma anında değişmez | — | Bütçe açılışta sabit (realtime-ornekler §6) |

**Örnek dersi:** senkronizasyon olayı **periyot kenarında** değerlendirilir;
CoreMusic drift ölçüm periyodu (R3) da bu kenarlara bağlanabilir — ama bu
**karar değildir**, şablondur (sayı uydurulmaz).

## §8 AUGraph yaşam döngüsü ↔ referans başlangıcı (master §4 A3)

| # | Aşama | Clock açısından |
|---|---|---|
| 1 | Create/NewNode | Clock referansı **yok** |
| 2 | Initialize (bellek allocate) | Hâlâ clock yok |
| 3 | Start | **Referans başlar** |
| 4 | Render döngüsü | Monoton sampleTime akar (§5) |
| 5 | Stop/Reset | Referans **sıfırlanır** (yeniden başlatmada başlangıç değişir) |

**Örnek dersi:** referans, **start ile** başlar; initialize öncesi "geçerli
zaman" yoktur. CoreMusic engine yaşam döngüsü de bu sıraya tabidir
(create → addNode → initialize → start — master §4 A3).

## §9 Resync (yeniden hizalama) senaryo şablonları

> Bu tablo **senaryo şablonlarıdır**; CoreMusic telafi yöntemi **seçilmemiştir**
> (R2). Değer/parametre yazılmaz.

| # | Senaryo | Tetikleyici | Şablon tepkisi | Sahip |
|---|---|---|---|---|
| 1 | Küçük kayma | Drift ölçümü eşiği geçti | Telafi yöntemi (R2) uygulanır | synchronization |
| 2 | Büyük kayma (desync) | Eşik belirgin aşıldı | **Yeniden eşleme** (master/slave sıfırlama) | synchronization + device |
| 3 | Clock kaynağı değişimi | Cihaz tak/çıkar | Yeni master seç → yeniden başla | device (Q6) |
| 4 | Geçiş/rampa anı | Kip/hız değişimi | Damga sürekliliği korunur (§5) | output + synchronization |
| 5 | Firmware reset (K005) | Donanım yeniden hazır | OS clock'a geçici bağımlılık → firmware hazır → yeniden eşle | K005 + synchronization |

**Kural:** her resync **ileriye** yapılır (§5 adım 4 — geriye sarma yasak);
eski veri **tekrar oynatılmaz** (output §12 U4 ile aynı yasak).

## §10 Uygulama kontrol listesi (synchronization tasarımı öncesi — taslak)

| # | Kontrol | Neden | Kapanış kapısı |
|---|---|---|---|
| 1 | Ana clock referansı seçildi mi? | R1 (kapsam §6) | SY-G1 |
| 2 | Telafi yöntemi seçildi mi? | R2 (kapsam §9) | SY-G2 |
| 3 | Drift periyodu + eşiği tanımlandı mı? | R3 (kapsam §7 J6/J7) | SY-G3 |
| 4 | Çoklu cihaz referans kararı verildi mi? | R4 (kapsam §11) | SY-G4 |
| 5 | Ağ senkronu kapsam kararı verildi mi? | R5 | SY-G5 |
| 6 | Telafi RT yasağına uygun mu (R6)? | realtime K-catalogu | SY-G6 |

## §11 Örnek okuma kuralı (bu dosya)

| Gösterilen | Gösterilmeyen |
|---|---|
| Referans sistemlerin senkron **akış şemaları** | K003'ün gerçek uygulaması |
| Adım sıraları | Ölçülmüş drift/jitter |
| Yeniden-hizalama senaryoları | Kabul eşiği |

`[KANIT]` etiketi bu dosyada **0** (repo'da `*.cpp`/`*.h` = 0).

## §12 Clock zinciri şablonu (kavramsal)

```text
  referans → sürücü → uygulama sayacı → DSP → çıkış
     ↑           ↑            ↑
   SY-G1      sürücü       epoch (§10)
```

| Düğüm | Sahip dosya |
|---|---|
| referans seçimi | synchronization-kapsam §6 |
| sürücü zamanı | `driver/*` |
| çıkış eşlemesi | `output/*` |

## §13 Drift ölçümü — örnek çalışma akışı (değer yok)

```text
  t0 : işaret (kaynak A)
  ... bekler ...
  t1 : işaret (kaynak A)
  fark = t1 - t0
  oran = fark / (t1 - t0) aralığı
```

| Adım | Durum |
|---|---|
| İşaretleme mekanizması | `UNKNOWN` |
| Bekleme penceresi | SY-G3 (açık) |
| Hesap | Tanımlı, uygulanmamış |
| Sonuç | **`UNKNOWN`** |

## §14 Jitter ölçümü — periyot tablosu iskeleti

| Periyot # | Beklenen süre | Gerçek süre | Fark |
|---|---|---|---|
| 1 | `UNKNOWN` | ölçülmedi | `UNKNOWN` |
| 2 | `UNKNOWN` | ölçülmedi | `UNKNOWN` |
| n | `UNKNOWN` | ölçülmedi | `UNKNOWN` |

Tablo **iskelettir**; doldurulması SY-G3 kapısını kapatır.

## §15 Yeniden-hizalama (resync) senaryoları — genişletme

| # | Tetik | Aksiyon | Taraf |
|---|---|---|---|
| 1 | Fark eşiği aşıldı | Telafi | SY-G2 kararı |
| 2 | Cihaz değişimi | Epoch yeniden kur | non-RT |
| 3 | Uygulama başlangıcı | Sayaç kur | non-RT |
| 4 | Kopma sonrası dönüş | Yeniden eşleme | non-RT |
| 5 | Telafi başarısız | Kullanıcıya bildir | non-RT |

**RT içinde resync başlatılmaz** (kural 2) — yalnız blok kenarı.

## §16 Epoch senaryosu — cihaz hot-plug

```text
  t0 : cihaz-1 aktif (epoch E0)
  t1 : cihaz-1 çıkarıldı
  t2 : cihaz-2 eklendi (epoch E1 ≠ E0)
  t3 : E0 damgaları E1 ile karşılaştırılamaz
```

| Adım | İşlem | Sahip |
|---|---|---|
| t1/t2 | Cihaz yaşam döngüsü | `device/*` |
| t2 | Yeni epoch kaydı | synchronization (§10) |
| t3 | Dönüştürme kuralı | `UNKNOWN` |

## §17 İki cihaz senaryosu — faz kayması iskeleti

| Ölçüm | Değer |
|---|---|
| Kaynak-1 fazı | `UNKNOWN` |
| Kaynak-2 fazı | `UNKNOWN` |
| Fark | `UNKNOWN` |
| Kabul eşiği | `UNKNOWN` (SY-G3) |
| Karar | ❓ SY-G4 |

## §18 Resample telafisi — iş yükü sorusu

| Soru | Cevap |
|---|---|
| Resample var mı? | Karar SY-G2 |
| Varsa iş yükü bütçesi | `UNKNOWN` — realtime dosyalarına bağımlı |
| RT'de uygulanabilir mi? | SY-G6 (açık) |

## §19 Sayaç birimi dönüşümü (yalnız tanım)

| Birim | Tanım | Not |
|---|---|---|
| Tik | OS sayaç adımı | Frekansı `UNKNOWN` |
| Örnek | Ses örneklemi | 48 kHz (sartname §8) |
| Saniye | SI | Dönüşüm için tik frekansı gerekir → `UNKNOWN` |

**48 kHz dışında hiçbir sayı yazılmaz.**

## §20 Senkron kopması — gözlem listesi

| Belirti | Sayaç | Eşik |
|---|---|---|
| Faz sıçraması | `UNKNOWN` | `UNKNOWN` |
| Epoch ihlali | `UNKNOWN` | 0 (mantıksal) |
| Kopma süresi | `UNKNOWN` | `UNKNOWN` |

## §21 Uygulama öncesi kontrol listesi (genişletilmiş)

| # | Kontrol | Kapı |
|---|---|---|
| 1 | Master clock seçildi mi? | SY-G1 |
| 2 | Drift ölçümü tanımlı mı? | SY-G3 |
| 3 | Telafi yöntemi seçildi mi? | SY-G2 |
| 4 | Çoklu cihaz referansı belli mi? | SY-G4 |
| 5 | Ağ kapsamı kararlaştırıldı mı? | SY-G5 |
| 6 | RT yasağı doğrulandı mı? | SY-G6 |
| 7 | Ölçüm sonuçları elle mi tutulacak? | — |

## §22 Referans platform akışları (adım kayıtlarının özeti)

| Adım kaydı | Bölüm | Ne bıraktı |
|---|---|---|
| Monoton sampleTime | §5 | Monotonluk kuralı |
| PipeWire mismatch | §6 | Uyumsuzluk akışı |
| ALSA periyot kenarı | §7 | Pencere kavramı |
| AUGraph döngüsü | §8 | Döngü şeması |

Dördüsü de **referans mimarisi**dir; K003'e birebir uygulanamaz
(platform seçimi yapılmadı) → `UNKNOWN`.

## §23 Karşılaştırma: iki telafi yöntemi (karar yok)

| | Örnekleme/drop | Resample |
|---|---|---|
| Basitlik | Yüksek | Düşük |
| Kalite | Riskli (click) | Daha iyi |
| İş yükü | Düşük | Yüksek |
| RT uyumu | Blok kenarı | Bütçe gerekir |
| Karar | ❓ SY-G2 | ❓ SY-G2 |

## §24 Bağımlılık: realtime ile sınır

| Soru | Cevap |
|---|---|
| Bütçe kimde? | realtime K003 |
| Bu dosya ne verir? | Telafi **zamanlaması** şartı |
| Kim ölçer? | SY-G3 kapısı |

## §25 Kapsam dışı listesi (tekrar değil, sınır)

| Konu | Neden dışarıda |
|---|---|
| Gecikme bütçesi | realtime |
| Çıkış cihazı seçimi | output |
| I2S/TDM ağacı | K005 firmware |
| Test ölçümü | testing |

## §26 Risk → kapı özeti

| Risk | Kapı |
|---|---|
| R1 master seçimi | SY-G1 |
| R2 telafi yöntemi | SY-G2 |
| R3 ölçüm | SY-G3 |
| R4 çoklu cihaz | SY-G4 |
| R5 ağ | SY-G5 |
| R6 RT yasağı | SY-G6 |

## §27 SSS

| Soru | Cevap |
|---|---|
| Örnekler kod mu? | Hayır — `[KANIT]` = 0 |
| Ölçüm var mı? | Hayır → `UNKNOWN` |
| Eşik yazıldı mı? | Hayır (uydurmak yasak) |
| Karar verildi mi? | Hayır — 6 kapı açık |

## §28 Terim sözlüğü (bu dosya)

| Terim | Karşılık |
|---|---|
| Resync | Yeniden hizalama (§15) |
| Epoch | Zaman damgası başlangıcı (§16) |
| Faz kayması | İki kaynağın zaman farkı (§17) |
| Pencere | Periyot kenarında ölçüm aralığı (§7) |

## §29 Uygulama sırası (kapsam §29 ile aynı)

SY-G1 → SY-G3 → SY-G2 → SY-G6 → SY-G4 → SY-G5.

## §30 Kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `synchronization/synchronization-ornekler.md` |
| 2 | Bölüm aralığı | §1–§40 (son ek: §11–§40, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** — şablon/niyet |
| 4 | `[KANIT]` sayısı | 0 |
| 5 | Açık kapı | SY-G1…SY-G6 |

## §31 Mono-tl — doğrulama adımları (şablon)

| # | Adım | Beklenen | Durum |
|---|---|---|---|
| 1 | Sayaç her artan okunur | Eşit/seçici artma | BLOCK |
| 2 | İki ardışık okuma | Sıfır/negatif fark yok | BLOCK |
| 3 | Cihaz değişiminden sonra | Yeni epoch kaydı | BLOCK |
| 4 | Uzun çalıştırma | Sayaç atlaması yok | BLOCK |

Eşik/kabul değeri `UNKNOWN` (SY-G3).

## §32 Latency mismatch senaryosu — kontrol adımları

| # | Adım | Not |
|---|---|---|
| 1 | İki tarafın bildirdiği gecikmeyi oku | Okuma mekanizması `UNKNOWN` |
| 2 | Farkı hesapla | Hesap tanımlı |
| 3 | Farkı telafi et | Yöntem SY-G2 |
| 4 | Yeniden ölç | Pencere SY-G3 |

## §33 Periyot kenarı penceresi — gözlem şablonu

```text
  [pencere]
   başlangıç : periyot kenarı - tolerans
   bitiş     : periyot kenarı + tolerans
   tolerans  : UNKNOWN (SY-G3)
```

Pencere **kavramsal**; sayısal genişlik yazılmaz.

## §34 Yaşam döngüsü döngüsü — referans şema

```text
  başlangıç → kurulum → çalıştırma → kapanış
      ↑           │           │
      └─ epoch ───┴─ resync ──┘
```

| Geçiş | Sahip |
|---|---|
| kurulum | non-RT |
| resync | non-RT (RT'de yasak) |
| kapanış | non-RT |

## §35 Dört referans adım kaydının ortak çıkarımı

| Ortak nokta | Çıkarım |
|---|---|
| Hepsi bir **zaman referansı** seçer | SY-G1 evrensel |
| Hepsinde ölçüm yok | SY-G3 evrensel |
| Hepsinde platform farklı | K003 platform seçimi `UNKNOWN` |

## §36 Doğrulama kapıları — beklenen kanıt özeti

| Kapı | Dosyada hazır | Eksik |
|---|---|---|
| SY-G1 | Aday tablosu (kapsam §15) | Onay |
| SY-G2 | Yöntem karşılaştırması (§23) | Ölçüm |
| SY-G3 | Ölçüm iskeleti (§13, §14) | Veri |
| SY-G4 | Senaryo (§17) | Cihaz sayısı |
| SY-G5 | Kapsam notu | Karar |
| SY-G6 | RT yasağı tarifi | Kod kanıtı |

## §37 Bağımlılık matrisi (bu dosya)

| Dosya | İlişki |
|---|---|
| `synchronization-kapsam.md` | Soru/kapı kaynağı |
| `realtime/*` | Bütçe + RT yasağı |
| `driver/*` | Sürücü zaman damgası |
| `output/*` | Çıkış zamanı |
| `testing/*` | Ölçüm testleri |

## §38 Riskler (bu dosya özelinde)

| Risk | Azaltma |
|---|---|
| Örneklerin gerçek sanılması | §11 statü satırı |
| Platforma taşınan referans adım | §35 — yalnız ortak çıkarım |
| Sayı uydurma | `UNKNOWN` disiplini |

## §39 Boşluk listesi

| Boşluk | Kapı |
|---|---|
| Master clock onayı | SY-G1 |
| Telafi yöntemi | SY-G2 |
| Ölçüm verisi | SY-G3 |
| Çoklu cihaz kararı | SY-G4 |
| Ağ kapsamı | SY-G5 |
| RT telafi doğrulaması | SY-G6 |

## §40 Sonraki tur notu

Bu dosyanın ihtiyacı **veridir**: bir drift/jitter ölçüm kaydı ve SY-G1
onayı. Bunlar olmadan `[KANIT]` geçişi (D6) **imkânsızdır**.

## §41 Sık karşılaşılan yanlış okumalar

| Yanlış okuma | Doğrusu |
|---|---|
| "§7 jitter buffer doldurulmuş" | Yalnız parametre **sütunları** var, değerler `UNKNOWN` |
| "Adım kayıtları K003'te uygulandı" | Referans mimarisi adımlarıdır (§35) |
| "48 kHz drift ölçümüdür" | Yapılandırma değeridir (sartname §8), ölçüm değil |
| "SY-G3 kapanmış sayılabilir" | Ölçüm verisi yok → açık |

## §42 Dosya içi çapraz referans tablosu

| Bölüm | Bağlı olduğu başka bölüm |
|---|---|
| §5 sampleTime | §13 drift akışı |
| §6 mismatch | §32 kontrol adımları |
| §7 ALSA penceresi | §33 pencere şablonu |
| §8 AUGraph döngüsü | §34 yaşam döngüsü şeması |
| §9 resync | §15 genişletilmiş senaryolar |
| §10 kontrol listesi | §21 genişletilmiş liste |

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
