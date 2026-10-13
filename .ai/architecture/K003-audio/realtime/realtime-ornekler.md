---
title: "realtime — Örnekler"
type: architecture-examples
node_id: K003-audio/realtime
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# realtime — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` içinde realtime callback, lock-free kuyruk ya da
ön-atama kodu yoktur (2026-10-12 kök `*.cpp`/`*.h` glob 0 isabet). Aranan:
callback işlevi, atomik sayaç, ring buffer, sabit bellek havuzu. Bulunan:
PHPUnit testleri (22 dosya) ve CI workflow'ları — bunlar realtime değildir.

```php
// Yardımcı gerçek (realtime DEĞİL, test altyapısı):
// shared/tests/ — 22 dosya, PHPUnit ^10.5/^11.0 (require-dev kanıtı).
// .github/workflows/ — ci.yml, secret-scan.yml (envanter ölçümü 2026-10-12).
// Bu dosyalar realtime kuralının DOĞRULAMASINA katkı vermez.
```

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | `AudioUnitRender` monoton sampleTime | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | sonraki sampleTime = önceki + inNumberFrames | Deadline referansı **örnek sayımıdır**: her tik ne kadar gecikti sayısal olarak görünür |
| 2 | PipeWire driver node scheduling | https://pipewire.org/page_scheduling | driver node döngüyü başlatır; required/pending sayaçları | Zamanlama **sayaç tabanlıdır**: tetiklenen node'un beklediği iş sayısı bitince çalışır |
| 3 | PipeWire async node: 1 quantum latency | https://pipewire.org/page_scheduling | async node 1 quantum ekler | Asenkron düğüm **gecikme bedeli öder** — realtime kuralları bunu ölçmeli (T15) |
| 4 | ALSA hw_params periyot/buffer pazarlığı | https://www.alsa-project.org/doc/api/group___p_c_m.html | format/rate/channels/buffer → sw_params: avail_min/period_event | Bütçe (periyot boyutu) **açılışta pazarlık edilir**, çalışma anında değişmez |
| 5 | ASIO `bufferSwitch` callback (vault kanıtı) | `backend/asio/asio-arayuzler.md` §ilgili (salt-okunur dosya, bu oturumda okundu) | sürücü döngüsü host'u çağırır, host veriyi hazırlar | Platform callback'i **dışarıdan gelir**; CoreMusic döngüsü ona tabidir (Q5) |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **realtime tasarımı öncesi** şablon olarak kullanılır:
1–2, deadline'ın örnek sayımıyla ölçüldüğünü ve sayaç tabanlı tetiklemenin
şablonunu; 3, asenkron katman gecikme bedelini; 4, bütçenin açılışta
pazarlık edilip çalışma anında sabit kaldığını; 5, callback'in dışarıdan
geldiğini. Öncelik/bütçe değerleri R1/R2 (`realtime-kapsam.md` §5)
kapanmadan yazılmaz → UNKNOWN; "lock-free uygulandı" iddiası kod kanıtı olmadan
yazılmaz (repo'da C++ yok).

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `realtime-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `realtime-dogrulama.md` | Kanıt/doğrulama tablosu (12 satır: ✅/has-been-fetched 8 · ⚠️ 2 · UNKNOWN 2) |
| `realtime/index.md` | Düğüm kaydı (ELI15 · açık sorular) |
| `../audio-master.md` | Üst anayasa §3 satır 9 · §4 A4 · §5 T9/T15 · §8 Q5 |
| `../audio-bagimliliklar.md` | §2/§6 — realtime ↔ backend kuyruk riski |
| `../synchronization/synchronization-kapsam.md` | Kardeş çocuk — disiplin ↔ zaman referansı ayrımı (RT2) |

## §5 PipeWire scheduling sayaç modeli — adım kaydı (master §5 T15)

> Kaynak: `docs.pipewire.org/page_scheduling.html` (✅ doğrulanmış, master §5 T15).

| # | Adım | Ne gösterdiği | Realtime açısından |
|---|---|---|---|
| 1 | Driver node döngüyü başlatır | Zamanlama **tek bir sürücü** tarafından sahiplenilir | CoreMusic'te "kimi çağıracak" sorusu (R5) |
| 2 | Required/pending sayaçları okunur | Node tetiklenmesi **sayaç tabanlıdır** | Deadline referansı örnektir (§2 satır 3) |
| 3 | Node işini bitirir, sayacı azaltır | İş bitimi ölçülebilir | Tick bütçesi (R2) için şablon |
| 4 | Async node 1 quantum ekler | Asenkron katman **bilinen bedel** öder | Non-RT↔RT sınırı bedeli (§8) |
| 5 | Quantum boyutu sabittir | Bütçe **açılışta** sabitlenir | Çalışma anında değişmez (§6 ile aynı ders) |

**Örnek dersi:** PipeWire, zamanlamayı **sayı sayaçlarına** indirger;
CoreMusic engine'i de kendi sayaçlarını tanımlamalıdır (tick sayısı, iş
bitimi) — bu bir **şablondur**, değer/uygulama iddiası değildir.

## §6 ALSA periyot/buffer pazarlığı — adım kaydı (master §5 T13)

| # | Adım | Ne gösterdiği | Realtime açısından |
|---|---|---|---|
| 1 | `hw_params` çağrısı format/rate/channels/buffer kurar | Bütçe **açılışta** pazarlık | Çalışma anında değişmez (§7 B5) |
| 2 | Periyot boyutu (iş penceresi) belirlenir | Tick süresi periyota bağlı | §7 bütçe formülü |
| 3 | `sw_params` avail_min/period_event kurar | Zamanlama penceresi sabitlenir | R1/R2 kapılarının kapanacağı yer |
| 4 | Red durumunda alternatif denenir (plug) | Bütçe **dayatılmaz** | Fallback şablonu (output-ornekler §7) |

**Örnek dersi:** periyot boyutu **sürücüyle anlaşılır**, uydurulmaz; bu yüzden
CoreMusic'in "512 örnek buffer" hedefi de bir **pazarlık başlangıcıdır**,
garanti değil (⚠️ VERIFICATION REQUIRED — sartname hedefi, sürücü teyidi yok).

## §7 ASIO/WASAPI callback akışı — şablon örneği (iç kanıt T8/T10 + T7)

| # | Adım | Kaynak | Ne gösterdiği |
|---|---|---|---|
| 1 | Sürücü callback'i dışarıdan tetikler | realtime-ornekler §2 satır 5 (ASIO `bufferSwitch` — vault kanıtı) | Host çağırmaz, çağrılır |
| 2 | Host veriyi hazırlar (pull) | master §4 A1 | Veri hazırlığı callback **içinde** yapılır |
| 3 | Veri yazıldı, dönüş | T7 (WASAPI olay tabanlı akış ✅) | Yazma tamam → olay tetiklenir |
| 4 | Cihaz kaybı → fallback | T8 (iç kanıt — `.ai/AGENTS.md` §17 #6) | Kesinti güvenli kapatma → WASAPI'ye geçiş |
| 5 | Exclusive lock tek uygulama | kural #9 (`.ai/AGENTS.md` — okundu) | Eşzamanlı iki uygulama **engellenir** |

**Örnek dersi:** callback **dışarıdan gelir ve ev sahibi (host) ona tabidir**;
CoreMusic engine'i kendi döngüsünü bu callback'in **içine** koyar (R5).

## §8 Priority inversion senaryo örneği (çerçeve — klasik disiplin)

> Bu bölüm bir **çerçeve**dir (klasik RT mühendisliği disiplini); CoreMusic
> uygulaması **yoktur** (repo'da C++ yok). Kaynak iddiası taşımaz.

| # | Senaryo | Sonuç (çerçeve) | CoreMusic karşılığı (şablon) |
|---|---|---|---|
| 1 | RT, non-RT'nin mutex'ini bekler | Zincir etkisi: RT gecikir | K2 kuralı — RT yolunda mutex yok |
| 2 | Non-RT, atomik yazımı yapamaz (yavaş) | Parametre geç güncellenir | Rampa ile uygulama (§8.3) — bozulma yok |
| 3 | OS, RT'yi düşük öncelikle çalıştırır | Periyot aşımı | R1 — öncelik kararı |

**Örnek dersi:** ses yolunda priority inversion'ın en etkili çözümü **kilidi
kaldırmaktır** (miras alma mekanizması kilit tabanlıdır ve bu yüzden
kullanılmaz). Bu kural realtime-kapsam §9'da bağlayıcı olarak kayıtlıdır.

## §9 RT↔non-RT sınırı şablon örnekleri (yapısal — iddia değil)

| # | Şablon | RT tarafı | non-RT tarafı | Bekleyen |
|---|---|---|---|---|
| 1 | Ölçüm halkası | Sayaç yazar (doluysa drop-oldest) | Periyodik okur, raporlar | Eşik kararı (testing) |
| 2 | Parametre rampası | Atomik okur, rampa uygular | Hedef değeri yazar | Ramp süresi kararı (UNKNOWN) |
| 3 | Komut kuyruğu | Atomik okur, durum geçişi | Duraklat/başlat yazar | Durum kümesi (master §8 Q7) |
| 4 | Yapılandırma değişimi | Blok kenarında uygular | Yeni tanımı hazırlar | Blok kenarı kuralı (R3 routing) |

**Örnek dersi:** her şablonun **tek bir RT yazarı** vardır; ikinci yazar
eklenirse lock-free kuralı bozulur (K2). Bu, §8.2'deki SPSC tercihinin
gerekçesidir.

## §10 Uygulama kontrol listesi (realtime tasarımı öncesi — taslak)

| # | Kontrol | Neden | Kapanış kapısı |
|---|---|---|---|
| 1 | RT öncelik kararı verildi mi? | R1 | RT-G1 (kapsam §12) |
| 2 | Tick bütçesi + periyot boyutu ölçüldü mü? | R2 | RT-G2 |
| 3 | Kuyruk yapısı seçildi mi? | §8.2 (Q5 ile kilitli) | RT-G3 |
| 4 | Yasak ihlali tespit mekanizması kuruldu mu? | R6 | RT-G4 |
| 5 | İlk `[KANIT]` geçişi (lock-free kod kanıtı) | master §7 D6 | RT-G5 |

## §11 PipeWire sayaç modeli — derin ders (T15)

§5'teki adım tablosunun **derinlemesine** hâli: sayaç modelinin **RT
açısından** ne anlama geldiği.

| # | Ders | Ayrıntı | K003 etkisi |
|---|---|---|---|
| 1 | Zamanlama **sayı** ile | Sayaç okunur → tetik | Deadline referansı (§5) |
| 2 | Driver **tek** | Tek düğüm başlatır | CoreMusic'te "kimi çağıracak" (R5) |
| 3 | Async **bedel öder** | 1 quantum ekler | Non-RT↔RT sınırı bedeli |
| 4 | Quantum **sabit** | Bütçe açılışta sabit | Çalışma anında değişmez |
| 5 | Sayaç **taşabilir** | Yığın riski | §14 kuyruk derinliği |

**Derin ders:** PipeWire **sayaç** ile çalışır; CoreMusic **tick** ile
çalışacaktır — ikisi aynı **sayı-tabanlı** disiplindir (§2 satır 3).

## §12 ALSA periyot — derin ders (T13)

§6'daki pazarlık tablosunun **derinlemesine** hâli.

| # | Ders | Ayrıntı | K003 etkisi |
|---|---|---|---|
| 1 | Bütçe **açılışta** | Periyot boyutu sabitlenir | RT-G2 ölçümünün sınırı |
| 2 | Periyot = iş penceresi | Her periyot bir iş | §7 B5 |
| 3 | Reddebilir | Alternatif (plug) | output §13 (kurtarma zinciri) |
| 4 | sw_params **pencere** | avail_min eşiği | synchronization-ornekler §7 |
| 5 | Değişmez | Çalışma anında **yok** | K6 yasağı ile uyumlu |

**Derin ders:** periyot boyutu **pazarlıkla** belirlenir; bu yüzden
CoreMusic'in "512 örnek" hedefi bir **başlangıç isteğidir**, garanti değil.

## §13 ASIO/WASAPI — derin ders (T8 · T7)

§7'deki akış tablosunun **derinlemesine** hâli.

| # | Ders | Ayrıntı | K003 etkisi |
|---|---|---|---|
| 1 | Callback **dışarıdan** | Host çağırmaz | R5 (engine içine oturur) |
| 2 | Pull (A1 ile uyumlu) | Veri callback'te hazırlanır | §21 kapsam |
| 3 | Olay tabanlı (WASAPI) | Doldurma olayla | output §7.2 A5 |
| 4 | Fallback **zorunlu** | Cihaz kaybında | T8 — device loss zinciri |
| 5 | Exclusive lock **tek** | Eşzamanlı iki uygulama yok | Kural #9 |

**Derin ders:** ASIO/WASAPI **ev sahibi değildir**; CoreMusic **ona tabidir**.
Bu, §21 ve §22'deki (kapsam) "host'a tabidir" dersinin **platform** hâlidir.

## §14 Priority inversion — derin ders (çerçeve)

§8'deki çerçeve tablosunun **derinlemesine** hâli: inversion'ın **üç**
türünün çözümü.

| # | Tür | Tanım | Çözüm |
|---|---|---|---|
| 1 | Sınıfik (classic) | Mutex zinciri | K2 (kilit yok) |
| 2 | Türev (secondary) | Kaynak paylaşımı | §14 (SPSC) |
| 3 | Zamansal (temporal) | OS zamanlama | R1 (öncelik) |

**Derin ders:** üç türün **ikisi** kilit/yardıma bağlıdır (K2 + SPSC);
yalnız üçüncüsü **OS'e** bağlıdır (R1). Bu, "önce kural, sonra ölçüm"
ilkesinin inversion'daki hâlidir.

## §15 Vardiya şablonları — derin ders (§9 kapsam)

§9'daki dört şablonun **davranış derinliği**.

| # | Şablon | RT davranışı | non-RT davranışı | Risk |
|---|---|---|---|---|
| 1 | Ölçüm halkası | Yaz (doluysa drop) | Periyodik oku | Eşik (R1) |
| 2 | Parametre rampası | Atomik oku + rampa | Hedef yaz | Ramp süresi UNKNOWN |
| 3 | Komut kuyruğu | Atomik oku + geçiş | Duraklat/başlat yaz | Durum kümesi (Q7) |
| 4 | Yapılandırma | Blok kenarında uygula | Yeni tanım hazırla | Blok kenarı (R3) |

**Derin ders:** her şablonun **tek yazarı** vardır (§14 SPSC dersi); ikinci
yazar eklenirse K2 ihlali olur (lock-free kuralı bozulur).

## §16 RT — platform callback derin karşılaştırma (§25 kapsam uzantısı)

§25 platform karşılaştırmasının **davranış** hâli.

| # | Platform | Tetikleme | Zamanlama sahibi | Ev sahibi davranışı |
|---|---|---|---|---|
| 1 | ASIO | Sürücü | Sürücü | Tabidir |
| 2 | WASAPI | Olay | Uygulama (event) | Kısmen sahip |
| 3 | Core Audio | Render çağrısı | Sistem | Tabidir |
| 4 | ALSA | Yazma döngüsü | Uygulama | Sahip |
| 5 | PipeWire | Driver node | Driver node | Tabidir |
| 6 | JACK | Process callback | Graf | Tabidir |

**Derin ders:** WASAPI/ALSA'da **uygulama kısmen zaman sahibidir**; ASIO/
Core Audio/PipeWire'da **tamamen sürücü/sistem** sahiptir. Bu fark,
"CoreMusic zamanlaması" tasarımını **platforma göre** ikiye böler (R5).

## §17 RT — kısıt ↔ test eşlemesi (testing dalına devir)

§6–§16'daki kısıtların **test** karşılıkları: her kısıt **hangi** testle
doğrulanır.

| # | Kısıt | Test | Test tipi |
|---|---|---|---|
| 1 | K1 (bellek) | Bellek ölçümü | L2 |
| 2 | K2 (kilit) | Statik denetim + stres | L1/L2 |
| 3 | K3 (I/O) | Süre pikleri | L2 |
| 4 | K4 (log) | I/O ölçümü | L2 |
| 5 | K5 (exception) | `noexcept` derleme | L1 (compile) |
| 6 | K6 (sleep) | Süre ölçümü | L2 |
| 7 | K7 (dal) | Dal maliyeti ölçümü | L2 |
| 8 | K8 (UI) | Kod incelemesi | — (manuel) |

**Eşleme dersi:** Y5 **tek otomatik** testtir (compile); diğerleri **ölçüm**
ister — bu yüzden testing dalı **ölçüm altyapısına** bağımlıdır (TS-G1).

## §18 RT — cross-ölçüm örneği: periyot aşımı zinciri (§21 kapsam)

§21'deki altı adımlı zincirin **adım adım ölçüm** hâli.

| # | Adım | Ölçüm | Beklenen |
|---|---|---|---|
| 1 | Aşım | Sayaç (§17) | 0 (normalde) |
| 2 | Kısmi tick | Sessizlik + rapor | Sınıf 2 |
| 3 | Tekrarlı | N sayacı | Sınıf 4 |
| 4 | Engine geçişi | Durum okuma | Q7 |
| 5 | Rapor | Olay | diagnostics |
| 6 | Kapı | TS-G1 | ❌ açık |

## §19 RT — FAQ derinlemesi (§19 kapsam)

§19 FAQ'ının **derin** hâli: her sorunun **derin** nedeni.

| # | Soru | Derin neden | Bağlantı |
|---|---|---|---|
| 1 | Öncelik kaç? | OS + sürücü + uygulama üçgeni | R1 · K000 |
| 2 | Periyot boyutu? | Donanım pazarlığı | R2 · driver |
| 3 | Kuyruk derinliği? | Bütçe + okuma periyodu | Q5 · backend |
| 4 | Vardiya hangi yapı? | Yazar/okuyucu sayısı | §14 |
| 5 | İhlal tespiti? | Otomasyon seviyesi | R6 · §16 kapsam |
| 6 | Aşım eşiği N? | Ölçüm yok | R2 · §17 kapsam |
| 7 | Watchdog kim? | Gözlemci tasarımı | §15 |

## §20 RT — sık yapılan hatalar derinlemesi (§22 kapsam)

§22 uyarılarının **derin** hâli: her hatanın **kök nedeni**.

| # | Hata | Kök neden | Önlem |
|---|---|---|---|
| 1 | Mutex | Kısa yolculuk yanılgısı | K2 |
| 2 | Runtime bellek | "Bir kez olur" yanılgısı | K1 |
| 3 | Lazy init | Tembellik | Y10 |
| 4 | Tek sayaç | Ölçüm ataleti | §17 |
| 5 | MPSC karmaşıklığı | Esneklik isteği | §14 — SPSC |
| 6 | Gözlemci kilidi | Gözlemcilik yanılgısı | §15 |
| 7 | Aşım sessizliği | Korku | §11 sınıf 4 |

## §21 RT — devir zinciri derinlemesi (§23 kapsam)

§23 devir planının **zincir** hâli: her devrin **ardından** gelen devir.

| # | Devir | Hedef | Ardından gelen |
|---|---|---|---|
| 1 | Bütçe ölçümü | diagnostics · testing | TS-G1 |
| 2 | Kuyruk yapısı | backend | Q5 kapanışı |
| 3 | İhlal tespiti | diagnostics | RT-G4 |
| 4 | Engine durum | engine | Q7 kapanışı |
| 5 | CPU profili | performance | RT-G1 |

## §22 RT — platform callback örnekleri (§16 sonrası derin tablo)

| # | Platform | Ne zaman çağrılır | Çıkışa etkisi |
|---|---|---|---|
| 1 | ASIO | Sürücü periyodu | output §7.2 |
| 2 | WASAPI | Olay | output §7.2 A5 |
| 3 | Core Audio | Render | output §6 |
| 4 | ALSA | Yazma | output §13 |
| 5 | PipeWire | Sayaç | output §13 |
| 6 | JACK | Graf | output (⚠️ erişim yok) |

## §23 RT — cross-reference özeti (dosyanın komşuları)

| # | Komşu dosya | İlişki |
|---|---|---|
| 1 | `realtime-kapsam.md` | Kısıt kataloğu (§6–§35) |
| 2 | `../synchronization/…` | Disiplin ↔ zaman referansı (RT2/S1) |
| 3 | `../output/…` | Çıkış underrun (§12 U3) |
| 4 | `../testing/…` | Eşik/kapı (TS-G1) |
| 5 | `../audio-master.md` | §5 T9 · §8 Q5 |

## §24 RT — kanıt durumu özeti (dosyanın kendisi)

| # | Bölüm | Kanıt durumu |
|---|---|---|
| 1 | §1–§4 (mevcut) | ✅ dosya okundu · ⚠️/UNKNOWN |
| 2 | §5–§10 (şablon) | ✅ doküman çerçevesi — CoreMusic kararı yok |
| 3 | §11–§23 (derin şablon) | ⚠️ şablon — `[KANIT]` almaz |
| 4 | §24–§30 (ek derin şablon) | ⚠️ yapı iskeleti |
| 5 | Kapılar (RT-G1…G5) | ❌ hepsi açık |

## §25 RT — sık yapılan hatalar dosya yüzeyi (gözlem)

| # | Hata | Gözlem | Kayıt |
|---|---|---|---|
| 1 | Mutex RT yolunda | Periyot aşımı | realtime RT-G2 |
| 2 | Runtime bellek | Bellek ölçüm artışı | Y1 |
| 3 | Lazy init | İlk çağrı pikleri | Y10 |
| 4 | Tek sayaç | Sınıf gizlenir | §17 |
| 5 | MPSC | Yarış stresi bulgusu | §14 |
| 6 | Gözlemci kilidi | Gözlemci profili | §15 |
| 7 | Aşım sessizliği | Kör nokta | §11 sınıf 4 |

## §26 RT — devir sonrası periyodik kontrol (§29 kapsam)

| # | Kontrol | Sahip |
|---|---|---|
| 1 | RT-G kapıları | Vault Steward |
| 2 | Yeni platform kanıtı | researcher |
| 3 | Repo C++ durumu | embedded |
| 4 | Q5/Q7 | Vault Steward |
| 5 | Yeni yasak | realtime |

## §27 RT — cross-ölçüm yüzeyi (§28 kapsam)

| # | Sayaç | Toplama | Rapor |
|---|---|---|---|
| 1 | Periyot aşım | Periyot kenarı | diagnostics |
| 2 | Underrun | Periyot kenarı | diagnostics |
| 3 | Drift | Ölçüm periyodu | diagnostics |
| 4 | İhlal | Debug/test | diagnostics |
| 5 | Fallback | Olay anı | diagnostics |

## §28 RT — kısıt ↔ platform uyum (§34 kapsam)

| # | Kısıt | Tüm platformlar |
|---|---|---|
| 1 | K1 (bellek) | kritik |
| 2 | K2 (kilit) | kritik |
| 3 | K3 (I/O) | yüksek |
| 4 | K6 (sleep) | kritik |
| 5 | K8 (UI) | orta |

## §29 RT — kapı-kanıt özeti (§35 kapsam)

| Kapı | Kanıt | Bu dosyada? |
|---|---|---|
| RT-G1 | OS öncelik kararı | ❌ |
| RT-G2 | Periyot başına süre ölçümü | ❌ |
| RT-G3 | Yarış stresi testi | ❌ |
| RT-G4 | Statik denetim | ❌ |
| RT-G5 | Lock-free kod kanıtı | ❌ |

## §30 RT — son özet tablo

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `realtime/realtime-ornekler.md` |
| 2 | İlk örnek | §1 ("— yok" — repo'da C++ yok) |
| 3 | Derinleştirilen bölümler | §5–§30 (2026-10-13) |
| 4 | Kanıt oranı | **Değişmedi** |
| 5 | Açık kapı | RT-G1…RT-G5 (kapsam §12) |

## §31 RT — SPSC ring buffer kullanım örüntüsü (şablon)

> **Statü:** Şablon/niyet. Repo'da `*.cpp` / `*.h` yok → derlenebilir kod
> iddiası **YASAKTIR**. Aşağıdaki yapı, K003 realtime mimarisinin *nasıl
> kurulacağını* gösterir; hiçbir satır diskte var olan kod değildir.

```text
[kuyruk yapısı — örüntü]
  kapasite      : 512'nin katı (sartname §8: 512 örnek / 48 kHz)
  yazıcı        : non-RT (dosya / UI / decode)
  okuyucu       : RT (audio callback)
  index         : atomik, wrap-around maskeli
  bellek        : baştan allocate — RT içinde büyütme YOK
```

| Kural | Gerekçe | Kapı |
|---|---|---|
| Tek yazıcı / tek okuyucu (SPSC) | Kilit gerektirmez | RT-G5 |
| Kapasite sabit, başlatmada belirlenir | RT'de `new`/`malloc` yasak | RT-G5 |
| Index atomik + bellek sırası (release/acquire) | Veri görünürlüğü | RT-G5 |
| Dolu durumda yazıcı düşer (overwrite yok) | RT gecikme bütçesi bozulmasın | RT-G2 |

**Bilinmeyenler:** gerçek kapasite seçimi, ölçülmüş doluluk oranı →
`UNKNOWN` (repo'da kod yok, ölçüm yok).

## §32 RT — noexcept audio callback örüntüsü (şablon)

```text
[callback örüntüsü]
  imza          : noexcept (kural 3)
  ilk iş        : girdi tamponunu yerinde işle (kopya allocate yok)
  durum         : atomik flaş / bayrak — mutex YOK
  çıkış         : hazır tamponu yaz, sıfırla (DC kontrolü dahil)
  hata          : exception fırlatma; bayrakla bildir
```

| Adım | RT içinde? | Not |
|---|---|---|
| Tampon allocate | **HAYIR** | Baştan ayrılmalı (kural 1) |
| `mutex` / `lock_guard` | **HAYIR** | Kural 2 — atomik kullan |
| `throw` | **HAYIR** | `noexcept` ihlali → terminate |
| Parametre okuma (atomik) | Evet | Non-RT yazar, RT okur |
| Dönüş değeri / bayrak yazma | Evet | Lock-free durum paylaşımı |

⚠️ VERIFICATION REQUIRED: `noexcept` davranışı JUCE callback zinciriyle
birebir uyumlu mu — JUCE sürümü doğrulanmadan **UNKNOWN** (kural 12).

## §33 RT — non-RT ↔ RT sınırı örnek akışı

| # | Yön | Veri | Mekanizma | Kilit? |
|---|---|---|---|---|
| 1 | UI → RT | Ses seviyesi | atomik `float` | Hayır |
| 2 | UI → RT | Preset değişimi | iki fazlı hazır bayrağı | Hayır |
| 3 | RT → UI | Ölçüm (RMS/peak) | atomik yazım + UI tarafında örnek okuma | Hayır |
| 4 | Dosya → RT | Çalınacak örnek | SPSC ring (§31) | Hayır |
| 5 | RT → Dosya | Kayıt | SPSC ring (ters yön) | Hayır |

**Örüntü notu:** preset değişimi *tek atomik yazım* ile yapılmaz — RT
yarım blok okumasın. İki tampon + atomik seçim (A/B) kullanılır; bu
örüntünün ölçülmüş gecikme katkısı `UNKNOWN`.

## §34 RT — priority inversion senaryosu ve gözlem listesi

| # | Senaryo | Belirti | Doğrulama |
|---|---|---|---|
| 1 | RT, kilit beklerken düşük öncelikli tutucu CPU'da | Periyot kaçışı | RT-G2 |
| 2 | Uzun non-RT kritik bölüm | RT gecikmesi sıçraması | RT-G2 + RT-G5 |
| 3 | Öncelik mirası yok (OS desteklemiyor) | Düşük öncelik bloklar | RT-G1 |
| 4 | Çalışma zamanı kuyruğa alınmış | Beklenmedik uyanma | RT-G3 |

Gözlem listesi (ölçüm kapıları — sayı uydurmak yasak):

- Periyot kaçış sayacı (RT-G2)
- En kötü-case callback süresi → `UNKNOWN` (ölçüm PLANNED)
- Kilit bekleme süresi → RO (kodda kilit olmamalı)
- CPU korelasyonu (RT çekirdeği izolasyonu) → `UNKNOWN`

## §35 RT — gecikme bütçesi örnek iskeleti (sayı yok)

> Bu bölümde **hiçbir milisaniye/saniye değeri üretilmez**; bütçe satırları
> yalnız taşındığı yeri gösterir. Dolum, ölçüm sonrası yapılır.

| Bütçe satırı | Kaynak | Değer |
|---|---|---|
| Cihaz tamponu (512 örnek @ 48 kHz) | sartname §8 | Hesap edilebilir (§35.1) |
| ASIO driver tamponu | sürücü | `UNKNOWN` |
| JUCE/ASIO zinciri ek katmanı | uygulama | `UNKNOWN` |
| DSP zinciri grubu gecikmesi | K003 engine | `UNKNOWN` |
| WASAPI paylaşımlı mod kuyruğu | Windows | `UNKNOWN` |
| **Toplam** | — | **`UNKNOWN` — ölçüm PLANNED** |

### §35.1 Bilinen tek değer: tampon süresi

`512 / 48000` — bu iki sayı sartnameden gelir (kural 8). Sonuç birim
dönüşümüdür, **ölçüm değildir**; toplam bütçeye tek başına eklenemez çünkü
diğer satırlar `UNKNOWN`.

## §36 RT — hata modu ve gözlem örüntüsü

| Hız modu | Tetikleyici | Davranış (tasarım) | Kapı |
|---|---|---|---|
| Kuyruk taşması | Yazıcı çok hızlı | Paket düşür, sayacı artır, UI'a bildir | RT-G3 |
| Periyot kaçışı | OS/gecikme | Sayacı artır, susturmadan raporla | RT-G2 |
| Cihaz kaybı | ASIO device loss | Graceful kapanış → fallback (kural 9/edge 6) | RT-G4 |
| DC eşik aşımı | Çıkış DC > 0.5V | Koruma rölesi (kural 10) | RT-G4 |
| NaN/Inf sızması | Bozuk DSP girdisi | Sessize al + bayrak | RT-G5 |

Hiçbir satırda ölçülmüş sıklık/egen değer yoktur → `UNKNOWN`.

## §37 RT — regresyon senaryoları (kapı listesi)

| ID | Senaryo | Beklenen | Tür |
|---|---|---|---|
| RT-R1 | 512 tampon + 48 kHz yapılandırması | Derleme/çalışma (kod yok → BLOCK) | Yapılandırma |
| RT-R2 | Yarış stresi (U sanallaştırma) | Kayıp/çakışma yok | Stres |
| RT-R3 | Statik denetim (kilit/allocate taraması) | RT yolunda 0 isabet | Statik |
| RT-R4 | Uzun süreli çalıştırma | Kaçış sayacı artmaz | Süre |
| RT-R5 | Cihaz kaybı sonrası kurtarma | Kontrollü kapanış | Hata |

**Gerçekleştirilme durumu:** kod yok → **hepsi BLOCK (PLANNED)**.

## §38 RT — dosya kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `realtime/realtime-ornekler.md` |
| 2 | Bölüm aralığı | §1–§38 (son ek: §31–§38, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** — şablon/niyet |
| 4 | Açık kapı | RT-G1…RT-G5 |
| 5 | Repo kod durumu | `*.cpp`/`*.h` = 0 → derlenebilir kod iddiası yok |

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
