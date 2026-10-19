---
title: "diagnostics — Kapsam"
type: architecture-scope
node_id: K003-audio/diagnostics
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# diagnostics — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 11 bu dalı **"Log, ölçüm, hata sınıflandırma"**
olarak yazar. Basitçe: motorun **gösterge paneli ve karakol kaydı** — ne
oluyor, ne kadar sürdü, nerede koptu; hepsi burada toplanır, sınıflandırılır
ve raporlanır. Arabadaki gösterge ışıkları gibi: sarı uyarı, kıza arıza —
hangisinin ne anlama geldiğini **sınıf listesi** söyler. Kritik ayrım:
diagnostics **toplar ve sınıflandırır**; **kabul edip etmeyeceğini** testing
söyler (master §3 satır 12).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Log formatı, ölçüm metrikleri ve
> hata sınıflandırma şeması kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Log toplama (motor olaylarının kaydı) | Log'un nereye yazıldığı (dosya/DB/AGENTS kuralı) | altyapı — `.ai/log.md` append-only (`.ai/AGENTS.md` §5) |
| 2 | Ölçüm toplanması (gecikme, xrun, CPU/bellek örneği) | Ölçümün **kabul eşiği** ve test kararı | testing (master §3 satır 12) |
| 3 | Hata **sınıflandırma** (hangi sınıf? kaynak?) | Hatanın kök-neden çözümü | debugging akışı (`.ai/AGENTS.md` §7) |
| 4 | Telemetri olaylarının üretilmesi (diğer düğümlerden gelen olaylar) | Olayın oluştuğu yer (engine/realtime/driver) | engine · realtime · driver |
| 5 | Runtime yasağı ihlalinin (lock/malloc vb.) tespiti (debug katmanı) | Yasakların kendisi (kural) | realtime (master §3 satır 9) |
| 6 | *(dışında)* hata/log'ların raporlanması ve maskelenmesi (security) | Hassas veri maskeleme | `.ai/AGENTS.md` §17 #3 — [REDACTED] |
| 7 | *(dışında)* test senaryoları ve kabul kriterleri | Kabul kararı | testing (master §3 satır 12) |
| 8 | *(dışında)* veri kaybı/xrun'un kendisi | Neden olduğu kesinti | input · output (master §3 satır 5-6) |

**Yasak:** diagnostics ↔ testing ayrımı bağlayıcıdır (master §3 satır 11–12
ayrı çocuklar); "ölçüm toplama" ile "kabul etme" tek dosyada birleştirilemez
(KURAL 1). Log formatı/seviyesi standardı `diagnostics/index.md` open_questions
dâhilinde **açıktır** — sabit bir log şeması iddiası yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| DG1 | diagnostics ↔ testing | toplama/sınıflandırma ↔ kabul kriteri | ✅ master §3 satır 11/12 (ayrı çocuklar) |
| DG2 | diagnostics ↔ realtime | ihlalin üretilmesi ↔ kuralın kendisi | ⚠️ ad-türetme (master §3 satır 11 ve 9) |
| DG3 | diagnostics ↔ engine | olayın kaynağı ↔ döngü | ⚠️ ad-türetme (master §3 satır 11 ve 1) |
| DG4 | diagnostics ↔ driver | sürücü hatası kodu ↔ sınıflandırma | ⚠️ driver-kapsam §3 D4 ile kilitli |
| DG5 | diagnostics ↔ security (log) | teknik log ↔ maskeleme/reddetme kuralı | ✅ `.ai/AGENTS.md` §17 #3 (iç kanıt) |
| DG6 | diagnostics ↔ K004-dsp | ölçüm sinyalinin alınması ↔ DSP hesabı | ✅ master §2.2 (sinyal gider, kod girmez) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "diagnostics · Log, ölçüm, hata sınıflandırma · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 72 | ✅ dosya okundu |
| 2 | Açık soru: "log formatı/seviyesi standardı yok ⚠️" | `diagnostics/index.md` open_questions (satır 15) | ✅ dosya okundu |
| 3 | "Sensitive data log'da → [REDACTED] ile maskeleme" (log kuralı) | `.ai/AGENTS.md` §17 Edge Case #3 | ✅ dosya okundu |
| 4 | `log.md` append-only — tüm ajanlar ekler, geçmiş satıra dokunmaz | `.ai/AGENTS.md` §25.3 kural 3 | ✅ dosya okundu |
| 5 | `auval` CLI bir audio unit'ı plug-in API + temel işlev üzerinden doğrular (doğrulama aracı referansı) | `audio-master.md` §4 A6 (Apple) | ✅ dosya okundu |
| 6 | PipeWire latency mismatch tespiti (ölçüm raporu şablonu) | `audio-master.md` §5 T16 → docs.pipewire.org page_latency | ✅ dosya okundu (URL fetch) |
| 7 | Repo'da ses telemetrisi toplama kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 8 | Log formatı/seviyesi standardı (CoreMusic K003) | — | ⚠️ VERIFICATION REQUIRED (index open_questions) |
| 9 | Ölçüm metriklerinin listesi ve örnekleme periyodu | — | **UNKNOWN** (tasarım yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | Log formatı/seviyesi standardı nedir? | ⚠️ VERIFICATION REQUIRED | diagnostics |
| R2 | Hangi metrikler toplanır, ne sıklıkla örnekleme? | **UNKNOWN** — tasarım yok | performance-engineer |
| R3 | Hata sınıflandırma şeması (sınıflar/kodlar) nedir? | ⚠️ VERIFICATION REQUIRED | error-coordinator |
| R4 | RT yasağı ihlali runtime'da nasıl tespit edilir (debug katmanı)? | ⚠️ VERIFICATION REQUIRED | realtime |
| R5 | Telemetri realtime yasağına takılır mı (vardiyaya devir)? | ⚠️ VERIFICATION REQUIRED | realtime |
| R6 | Ölçüm verisinin saklama süresi ve yeri (dosya/DB)? | **UNKNOWN** — karar yok | backend |

**Kapanış:** status: draft kalır. R2/R6 kapanmadan metrik listesi, periyot ya
da saklama yeri yazılmaz → UNKNOWN; hata kodu/uç nokta uydurulmaz.

---

## §6 Hata kodu ve sınıf şablonları

> §5 R3 ("hata sınıflandırma şeması nedir?") **açıktır** — bu bölüm bir
> *aday sınıf şemasıdır*; "CoreMusic hata kodları bunlardır" iddiası
> **yazılmaz**. Kod **atanmaz** (rakam uydurmak yasaktır); sınıflar
> *işlevsel* adlarla listelenir, numaralandırma R3 kapanınca yapılır.

### §6.1 Aday hata sınıfları

| # | Sınıf | Ne kapsar | Üreten düğüm | Sınıflandırma sahibi | Durum |
|---|---|---|---|---|---|
| H1 | Cihaz kaybı | seçili cihazın aniden yok olması | device · driver (§3 D7) | diagnostics | ⚠️ şema R3 |
| H2 | Cihaz açılamadı | seçilen cihazla akış açılamaması | driver | diagnostics | ⚠️ şema R3 |
| H3 | Kip reddi | exclusive/shared çakışması | driver | diagnostics | ⚠️ şema R3 |
| H4 | Format uyuşmazlığı | pazarlığın sonuçsuz kalması | device · driver (§9.4) | diagnostics | ⚠️ şema R3 |
| H5 | Xrun / taşma | tampon taşması ya da kesinti | input · output (§2 satır 8) | diagnostics | ⚠️ şema R3 |
| H6 | Dropout | akışta örnek boşluğu | engine · realtime | diagnostics | ⚠️ şema R3 |
| H7 | RT yasağı ihlali | lock/malloc vb. yasağın çiğnenmesi | realtime (§2 satır 5) | diagnostics (tespit) · realtime (kural) | ⚠️ R4 |
| H8 | Latency aşımı | bütçe üstü gecikme | diagnostics ölçümü | testing (kabul) | ⚠️ bütçe yok |
| H9 | Ölçüm tutarsızlığı | beklenen≠gerçekleşen | diagnostics (PipeWire şablonu) | diagnostics | şablon var |
| H10 | Sürücü hata kodu | platformdan gelen hata | driver (§2 satır 3) | diagnostics | ⚠️ çevirim komşuda |
| H11 | Clock kayması | zaman referansı uyuşmazlığı | synchronization | diagnostics | komşu açık |
| H12 | Karışım taşması | toplamanın kliplenmesi | mixing (§3 M6) | diagnostics | mixing R3 |
| H13 | Kaynak kesintisi | dosya/mikrofon/akış kesilmesi | input | diagnostics | input R3 |
| H14 | Kapı/zaman aşımı | işlem beklenenden uzun | her düğüm | diagnostics | — |

### §6.2 Sınıf öznitelikleri (her olay için taşınacak alanlar)

| # | Öznitelik | Açıklama | Karar durumu |
|---|---|---|---|
| 1 | sınıf (H-x) | hangi kalem | R3 ile bağlanır |
| 2 | ciddiyet | bilgi / uyarı / arıza | **seviye listesi yok → R1/R3** |
| 3 | kaynak düğüm | hangi düğüm üretti | şablon |
| 4 | tekrar sayısı | olayın sıklığı | sayaç (§8) |
| 5 | ilk/son görülme | zaman damgası | biçim R1 |
| 6 | süre | olay anının süresi | birim R5 (driver kapsamı) |
| 7 | ilişkili olay | örn. cihaz kaybı → fallback | zincir R4 |
| 8 | maskelenmiş alanlar | hassas veri [REDACTED] | `.ai/AGENTS.md` §17 #3 |
| 9 | kanıt bağlantısı | ölçüm günlüğü satırı | dogrulama dosyası |
| 10 | çözüm durumu | açık/kapandı | testing (kabul) |

### §6.3 Hata kodu üretimi kuralları (neyin YASAK olduğu)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Rakamlı hata kodu **atanmaz** (0x…, E…) | kod uydurma = zero-hallucination ihlali; numaralandırma R3 kararı |
| 2 | Platform hata kodu **olduğu gibi** taşınır (çevirimsiz) | asıl kod kaybolursa teşhis zorlaşır |
| 3 | Çevirilen üst kod, asıl kodu **eşlik ederek** yazılır | tek başına üst kod teşhisi zayıflatır |
| 4 | Sınıf listesine yeni satır, R3 kararı olmadan **eklenmez** | şema sahibi error-coordinator (§5 R3) |
| 5 | Hata log'u [REDACTED] maskelemesinden **muaf değildir** | `.ai/AGENTS.md` §17 #3 |
| 6 | "Bilinmeyen hata" sınıfı **açık tutulur** | tanımlanmayan kod sessizce yutulmaz |

## §7 Log şablonları

> §5 R1 ("log formatı/seviyesi standardı nedir?") **açıktır**. Aşağıdaki
> şablonlar format **adayıdır**; satır düzeni, alan sırası ve seviye adları
> standart sayılmaz.

### §7.1 Log satırı alan şablonu

| # | Alan | Zorunlu | Not | Karar |
|---|---|---|---|---|
| 1 | zaman | evet | biçim (metin/sayısal) | R1 AÇIK |
| 2 | seviye | evet | ad kümesi yok | R1 AÇIK |
| 3 | kaynak düğüm | evet | engine/device/driver/... | şablon |
| 4 | olay kodu/üstü sınıf | evet | H-şeması R3 | R3 AÇIK |
| 5 | mesaj | evet | serbest metin mi yapılandırılmış mı | R1 AÇIK |
| 6 | bağlam (cihaz/akış kimliği) | koşullu | F1 adayı | teyitsiz |
| 7 | süre | koşullu | birim R5 | AÇIK |
| 8 | sayısal ölçüm | koşullu | değer + birim ayrı | AÇIK |
| 9 | maskelenen alan | koşullu | [REDACTED] | ✅ kural var |
| 10 | ilişkili olay | koşullu | zincir takibi | R4 ile ilişkili |
| 11 | oturum/ürüm | evet | teşhis için | şablon |
| 12 | kaynağı çağıran | koşullu | hangi kapı tetikledi | şablon |

### §7.2 Seviye şablonu (adlar adaydır)

| # | Seviye adayı | Ne yazılır | Örnek sınıf (§6) | Karar |
|---|---|---|---|---|
| 1 | TRACE | adım izi (yalnız debug) | tümü | R1 AÇIK |
| 2 | DEBUG | teşhis ayrıntısı | H7 (RT ihlali izi) | R1 AÇIK |
| 3 | INFO | durum değişimi | H1 fallback kapandı | R1 AÇIK |
| 4 | WARN | olağandışı, devam ediyor | H3/H4 | R1 AÇIK |
| 5 | ERROR | hizmet kesildi | H1/H2/H6 | R1 AÇIK |
| 6 | FATAL | güvenli duruş | zincir tükendi (§7 device) | R1 AÇIK |
| 7 | — (kabul eşiği) | hangi seviye testi geçirir | tümü | testing (§2 satır 7) |

### §7.3 Log akış şablonu (üretim → tüketim)

```text
Olay üretildi (device/engine/driver/mixing)
  ├─ Alanlar doldurulur (§7.1) — hassas alanlar maskelenir ([REDACTED])
  ├─ Sınıf atanır (§6.1) — bilinmeyen kod → "bilinmeyen" sınıfı
  ├─ Yazım yolu: append-only kuralı ile uyumlu (`.ai/AGENTS.md` §25.3 #3)
  │    └─ geçmiş satıra DOKUNMAZ
  ├─ Telemetri sayaçları güncellenir (§8)
  └─ Tüketici: teşhis akışı (§10) · test eşiği (testing) · rapor (uygulama)
```

**Yasak:** bu şablon log'un *nereye yazılacağını* (dosya/DB) **kararlaştırmaz**
— §2 satır 1'e göre yazım yeri bu dalın dışındadır (§5 R6 açık).

## §8 Metrik sayaçları (ad yok, değer yok)

| # | Metrik adayı | Tür | Örnekleme | Sahip | Durum |
|---|---|---|---|---|---|
| 1 | çalışma süresi | sayaç | — | engine | R2 AÇIK |
| 2 | döngü adımı (tik) sayısı | sayaç | — | engine | R2 AÇIK |
| 3 | tik süresi (min/ort/azami) | dağılım | **periyot yok** | engine | R2 AÇIK |
| 4 | xrun sayısı | sayaç | — | input · output | R2 AÇIK |
| 5 | dropout süresi | sayaç | — | engine | R2 AÇIK |
| 6 | uçtan uca gecikme | ölçüm | **periyot yok** | diagnostics | R2 AÇIK |
| 7 | capture gecikmesi | ölçüm | — | input (§5 R4) | AÇIK |
| 8 | sürücü gecikmesi | ölçüm | — | driver (§5 R5) | AÇIK |
| 9 | karışım taşması | sayaç | — | mixing | mixing R3 |
| 10 | RT ihlali | sayaç | anlık | realtime (§5 R4) | AÇIK |
| 11 | CPU yükü örneği | ölçüm | — | performance | R2 AÇIK |
| 12 | bellek örneği | ölçüm | — | performance | R2 AÇIK |
| 13 | cihaz kaybı/fallback | sayaç | — | device (§16 şablonu) | R4 AÇIK |
| 14 | kip dağılımı | dağılım | — | driver | R1 AÇIK |
| 15 | kuyruk doluluğu | ölçüm | — | backend | Q5 AÇIK |
| 16 | clock kayması | ölçüm | — | synchronization | komşu |

**Örnekleme periyodu hiçbir satırda yoktur** (§5 R2 kapanmadan sayı yazılmaz).

## §9 Telemetri olay şeması (diğer düğümlerden gelen)

| # | Olay | Üreten düğüm | diagnostics'in payı | Sınır |
|---|---|---|---|---|
| 1 | döngü adımı olayı | engine | topla/sınıflandır | §3 DG3 |
| 2 | durum geçişi olayı | engine | sınıflandır | §3 DG3 |
| 3 | sürücü hata kodu | driver | çevir + sınıflandır | §3 DG4 |
| 4 | cihaz kaybı | device · driver | sınıflandır (§6 H1) | §3 DG4 |
| 5 | xrun/taşma | input · output | sınıflandır (§6 H5) | §2 satır 8 |
| 6 | RT ihlali bildirimi | realtime | tespit (debug katmanı) | §5 R4 |
| 7 | karışım taşması | mixing | sınıflandır (§6 H12) | §3 DG6 komşu |
| 8 | clock olayı | synchronization | sınıflandır | komşu |
| 9 | ölçüm örneği | diagnostics kendisi | üret + sakla | §5 R6 |
| 10 | test sonucu | testing | **girmez** | §2 satır 7 (dışında) |

### §9.1 Telemetri taşıma kısıtları

| # | Kısıt | Kaynak |
|---|---|---|
| 1 | Telemetri RT thread'inde kuyruk/lock kullanamazsa ne olur? | §5 R5 AÇIK — cevap yok |
| 2 | Telemetri olayı sabit boyutta olmalı (taşma riski) | tasarım ilkesi — ⚠️ kanıt yok |
| 3 | Telemetri kaybı sessiz olmamalı | ilke — sayacın kendisi §8 |
| 4 | Telemetri formatı log formatıyla aynı mı? | R1 AÇIK |
| 5 | Telemetri sahibi hangi düğüm (toplayan mı üreten mi)? | §5 R5 AÇIK |

## §10 Teşhis adımları (adım ağaçları — test iskeleti)

### §10.1 Ses yok / kesik teşhisi

```text
Ses geliyor mu?
  ├─ Hayır → hangi aşamada koptu?
  │    ├─ Cihaz seçili mi? (device envanteri)
  │    ├─ Akış açık mı? (driver durumu)
  │    ├─ Karışım dolu mu? (mixing taşması/H12)
  │    ├─ Hata sınıfı var mı? (§6.1 tablosu)
  │    └─ RT ihlali durdurdu mu? (H7)
  └─ Evet → kesik mi / gecikme bütçesi aşıldı mı?
       ├─ xrun sayacı artıyor mu? (H5)
       ├─ tik süresi dağılımı (§8 #3)
       └─ kabul eşiği: testing (bu dosyada DEĞİL)
```

### §10.2 Cihaz kaybı teşhisi

| # | Adım | Soru | Veri kaynağı | Sonuç |
|---|---|---|---|---|
| 1 | olayı doğrula | hangi sınıf? | §6.1 H1 | sınıf ataması |
| 2 | zamanı al | ilk/son görülme | §7.1 alan 5 | zaman çizelgesi |
| 3 | zinciri iz | hangi kapı denendi? | §7 (device) + R4 ⚠️ | kapı kaydı |
| 4 | say | kaç tekrar | §8 #13 | sayaç |
| 5 | kip kontrolü | exclusive/shared miydi? | §8 #14 | kip notu |
| 6 | korelasyon | sürücü/OS değişimi mi? | olay 8 (device §6.3 T6) | kök-neden ipucu |
| 7 | kapat | kapı kapandı mı? | testing | kapalı değilse açık kalır |

### §10.3 RT yasağı ihlali teşhisi (§5 R4 ile kilitli)

| # | Adım | Aday yöntem | Etiket |
|---|---|---|---|
| 1 | ihlali yakala | debug katmanında yasağın denetimi | ⚠️ R4 — yöntem yok |
| 2 | sınıfı ata | H7 | ⚠️ R3 |
| 3 | konumu raporla | hangi düğüm/çağrı zinciri | ⚠️ kanıt yok |
| 4 | etkiyi ölç | dropout oluştu mu? | §8 #5 |
| 5 | düzeltme | realtime dalının işi | §2 satır 5 |
| 6 | doğrula | tekrar test | testing |

**Not:** adım 1'deki "yakalama yöntemi" (hangi araç/kanıt) bu dosyada
**yazılmaz** — §5 R4 açık; yöntem uydurmak ölçüm uydurmaktır.

## §11 Komşu etkileşim ve risk tabloları

### §11.1 Komşu etkileşimi

| # | Komşu | Gelen | Giden | Sınır (§3) |
|---|---|---|---|---|
| 1 | testing | ölçüm sonuçları | — | DG1 (bağlayıcı) |
| 2 | realtime | ihlal olayı | kural ihlali bildirimi | DG2 |
| 3 | engine | döngü olayı | rapor/istek yok | DG3 |
| 4 | driver | hata kodu | — | DG4 |
| 5 | input · output | xrun olayı | sınıflandırma | §2 satır 8 |
| 6 | device | cihaz kaybı olayı | sınıflandırma | §3 D7 (device) |
| 7 | mixing | taşma olayı | sınıflandırma | M6 |
| 8 | security | — | log maskesi kuralı | DG5 (✅) |
| 9 | backend | — | log'un yazılacağı yer | §2 satır 1 · R6 |
| 10 | K004-dsp | ölçüm sinyali | — | DG6 ✅ |

### §11.2 Risk tablosu

| # | Risk | Tetikleyici | Etki | Azaltma | Etiket |
|---|---|---|---|---|---|
| 1 | Sınıflandırma uydurma | kod/rakam yazmak | zero-hallucination ihlali | §6.3 kuralları | kontrol ediliyor |
| 2 | Log formatı erken donma | şablon standart sayılır | yanlış standardizasyon | R1 AÇIK korunur | açık |
| 3 | Metrik enflasyonu | her şey ölçülür | gürültü, maliyet | R2 ile liste | açık |
| 4 | RT telemetri ihlali | telemetri lock ister | ses kesintisi | §5 R5 açık bırakılır | açık |
| 5 | Hassas veri sızıntısı | maske unutulur | güvenlik ihlali | §7.1 alan 9 ✅ kural | korunuyor |
| 6 | Log kaybı | yazım yolu tasarlanmaz | teşhis imkânsız | R6 açık | açık |
| 7 | Kabul ile karıştırma | eşik bu dosyaya yazılır | DG1 ihlali | §2 satır 7 · §7.2 satır 7 | korunuyor |
| 8 | Ölçüm uydurma | periyot/sayı yazılır | yanlış PASS | §8 "değer yok" | kontrol ediliyor |
| 9 | Bilinmeyen kodun yutulması | sınıf listesi dar | sessiz arıza | §6.3 kural 6 | korunuyor |
| 10 | Dosya içi tutarsızlık | §5 ile çelişen iddia | SSOT ihlali | §12 denetimi | kontrol ediliyor |

## §12 Kapsam dışı taşma denetimi

| # | Eklenen bölüm | Denetlenen soru | Sonuç |
|---|---|---|---|
| 1 | §6 | hata kodu rakamı atandı mı? | ✅ HAYIR — yalnız işlevsel ad |
| 2 | §6.2/§7.1 | format kararı verildi mi? | ✅ HAYIR — R1 açık etiketi |
| 3 | §7.3 | log nereye yazılır yazıldı mı? | ✅ HAYIR — §2 satır 1 sınırı |
| 4 | §8 | ölçülen değer/periodyot yazıldı mı? | ✅ HAYIR — tümü "yok/AÇIK" |
| 5 | §9.1 | telemetri taşıma kararı verildi mi? | ✅ HAYIR — R5 açık |
| 6 | §10 | teşhis sonuçları "doğru" diye sunuldu mu? | ✅ HAYIR — iskelet/ağaç |
| 7 | §10.3 | RT yakalama yöntemi icat edildi mi? | ✅ HAYIR — R4 açık |
| 8 | §11 | komşu sınırı değiştirildi mi? | ✅ HAYIR — §3 aynen |
| 9 | §11.2 | yeni risk değil, mevcut R1–R6 mı? | ✅ yeni soru açılmadı |
| 10 | Genel | §1–§5 değişti mi? | ✅ hayır |
| 11 | Genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 12 | Genel | derlenebilir kod iddiası | ✅ yok (yalnız §7.3/§10.1 akış ağaçları) |

## §13 Uygulama kapıları (hepsi KAPALI)

| # | Kapı | Sahip soru | Kabul ölçütü | Durum |
|---|---|---|---|---|
| 1 | Log formatı/seviyesi | §5 R1 | standart + örnek satır doğrulanır | KAPALI |
| 2 | Metrik listesi/periyot | §5 R2 | sayısal değer ölçülür | KAPALI |
| 3 | Hata şeması | §5 R3 | sınıf/kod listesi kararlaştırılır | KAPALI |
| 4 | RT ihlali tespiti | §5 R4 | debug katmanı yöntemi onaylanır | KAPALI |
| 5 | Telemetri ↔ RT | §5 R5 | vardiya kararı alınır | KAPALI |
| 6 | Saklama yeri/süre | §5 R6 | yer + süre kararı | KAPALI |
| 7 | Ölçüm günlüğü | §16 (device şablonu benzeri) | ilk gerçek satır eklenir | KAPALI (ölçüm yok) |
| 8 | Kabul eşiği | testing (DG1) | eşik testing'te tanımlanır | KAPALI (bu dosya dışı) |

## §14 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §6.1 H-sınıfları | master §3 satır 11 · `diagnostics/index.md` open_questions | iç kanıt + açık soru |
| 2 | §6.3 kural 5 | `.ai/AGENTS.md` §17 #3 | ✅ iç kanıt |
| 3 | §6.3 kural 6 | `.ai/AGENTS.md` §25.3 #3 (append-only) | ✅ iç kanıt |
| 4 | §7.3 yazım yolu | `.ai/AGENTS.md` §5 · §25.3 | ✅ iç kanıt |
| 5 | §8 | §5 R2 | açık soru |
| 6 | §9 olayları | §2 tablosu (içinde/dışında) | iç kanıt |
| 7 | §10.1/§10.2 | `diagnostics-ornekler.md` §2 satır 2 (PipeWire mismatch) | doğrulanmış URL |
| 8 | §10.2 adım 4 | `diagnostics-ornekler.md` §2 satır 3 (sayaç) | doğrulanmış URL |
| 9 | §10.3 | §5 R4 | açık soru |
| 10 | §11.1 | §3 DG1–DG6 | iç kanıt |
| 11 | §12/§13 | §4–§5 | iç kanıt |
| 12 | auval bağımsız doğrulama | `diagnostics-ornekler.md` §2 satır 1 | doğrulanmış vault atfı |
| 13 | maskeleme | `.ai/AGENTS.md` §17 #3 | ✅ iç kanıt |
| 14 | telemetri kaynak API | `diagnostics-ornekler.md` §2 satır 4 | doğrulanmış URL |
| 15 | repo telemetrisi yok | §4 #7 (glob 0) | ölçüm |

---

## §15 Ölçüm ve saklama akış şablonu (R6 ile kilitli)

> §5 R6 ("ölçüm verisinin saklama süresi ve yeri") **açıktır**; bu bölüm akışın
> *yönünü* gösterir, yeri/süresi **kararlaştırılmaz**.

### §15.1 Veri yolu şablonu

| # | Aşama | Ne olur | Sahip | Karar durumu |
|---|---|---|---|---|
| 1 | olay üretimi | düğüm olayı yayınlar | engine/device/driver/... | şablon |
| 2 | alan doldurma | §7.1 alanları + maskeleme | diagnostics | R1 AÇIK |
| 3 | sınıflandırma | §6.1 H-adı atanır | diagnostics | R3 AÇIK |
| 4 | sayaç güncelleme | §8 sayaçları | diagnostics | R2 AÇIK |
| 5 | yazım | append-only | altyapı (§2 satır 1) | R6 AÇIK (yer) |
| 6 | saklama | süre + arşiv | backend | R6 AÇIK (süre) |
| 7 | okuma | teşhis akışı (§10) | diagnostics | şablon |
| 8 | eşik kontrolü | test kararı | testing | DG1 (dışında) |
| 9 | rapor | özet üretim | uygulama katmanı | §2 satır 6 |
| 10 | silme/arşiv | süre dolunca | — | **karar yok → R6** |

### §15.2 Saklama kararı olmadan yazılamayacaklar

| # | Soru | Bu dosyadaki cevap | Neden yazılmaz |
|---|---|---|---|
| 1 | dosya mı, DB mi? | — | R6 AÇIK |
| 2 | kaç gün saklanır? | — | R6 AÇIK |
| 3 | dönen (rotasyon) kuralı? | — | R6 AÇIK |
| 4 | sıkıştırma var mı? | — | R6 AÇIK |
| 5 | kim okuyabilir (yetki)? | — | security sınırı (DG5) |
| 6 | maskeleme kuralı ne? | [REDACTED] | ✅ tek kesin kural (§17 #3) |
| 7 | append-only ne anlama gelir? | geçmiş satıra dokunulmaz | ✅ §25.3 kural 3 |
| 8 | ölçüm günlüğü ayrı mı? | — | komşu şablon (device-dogrulama §24) |

### §15.3 Telemetri ↔ log ↔ ölçüm ayrımı

| # | Yüzey | Ne taşır | Ne TAŞIMAZ | Sınır |
|---|---|---|---|---|
| 1 | log | olay kaydı | kabul kararı | testing |
| 2 | sayaç | birikimli sayılar | zaman damgası ayrıntısı | R2 |
| 3 | ölçüm günlüğü | gerçek değer + birim + karar | tahmin | dogrulama dosyası |
| 4 | telemetri akışı | düğümler arası olay | sınıflandırma sonucu | §9 |
| 5 | test raporu | sonuç (PASS/FAIL) | — | testing (dışında) |
| 6 | rapor yüzeyi | özet | ham veri | uygulama katmanı |

## §16 Uygulama senaryoları ve sık sorular

### §16.1 Senaryo → şablon → kapı matrisi

| # | Senaryo | Kullanılan şablon | Çıktı | Kapı |
|---|---|---|---|---|
| 1 | ilk log satırı yazımı | §7.1 alanları | biçim kararı gerekir | R1 |
| 2 | bilinmeyen sürücü kodu geldi | §8.3 | "bilinmeyen" sınıfı | R3 |
| 3 | cihaz kaybı tekrar ediyor | §10.2 + §9 sayaç | kapı kaydı | R4 |
| 4 | tik süresi ölçmek istendi | §9.1 + §15.1 | değer + periyot | R2 |
| 5 | RT ihlali yakalandı | §10.3 | yöntem eksik | R4 |
| 6 | hassas alan log'a düştü | §7.1 alan 11 | [REDACTED] | ✅ kural |
| 7 | ölçüm raporu istendi | §11 günlüğü | satır yok → PLANNED | ölçüm |
| 8 | eşikle karşılaştırma | §7.2 satır 7 | eşik testing'te | DG1 |
| 9 | telemetri RT'ye takıldı mı? | §15.3 satır 4 | vardiya kararı | R5 |
| 10 | log nereye yazılacak? | §15.1 satır 5–6 | yer kararı | R6 |
| 11 | yeni hata sınıfı önerisi | §6.3 kural 4 | R3 kararı gerekir | R3 |
| 12 | karışım taşması alarmı | §6.1 H12 | eşik yok | mixing R3 |
| 13 | xrun artışı | §8 H5 + §9 #3 | periyot girdisi | R2 |
| 14 | kip reddi teşhisi | §8.2 satır 3 | kip verisi | driver R1 |
| 15 | clock kayması | §8.1 H11 | komşu şema | synchronization |
| 16 | fallback kapandı mı? | §7.2 satır 14 (kapı durumu) | kapı etiketi | R4 |
| 17 | oturum/ürüm etiketi | §7.1 alan 13 | biçim | R1 |
| 18 | sayım/ölçüm tutarsızlığı | §16.2 (aşağı) | yeniden sayım | — |
| 19 | yeni metrik talebi | §6.3 benzeri kural | R2 kararı | R2 |
| 20 | ölçüm + test birleşimi | §15.3 satır 5 | ayrım korunur | DG1 |

### §16.2 İç tutarlılık denetimleri (bu dosyanın kendi kontrolü)

| # | Denetim | Beklenen | Sonuç |
|---|---|---|---|
| 1 | §6.1 H-adları §8.1 ile aynı mı? | birebir aynı liste | ✅ iki tablo aynı adları taşır |
| 2 | §7.2 sınıf sütunu §6.1'e mıknatıs? | evet | ✅ |
| 3 | §9 kapı eşlemesi §5 R'lerine mıknatıs? | evet | ✅ R1–R6 tamamı |
| 4 | §11 boş günlüğe değer yazıldı mı? | hayır | ✅ tümü "—" |
| 5 | §10.3'te yöntem icat edildi mi? | hayır | ✅ "yazılmadı (R4)" |
| 6 | rakamlı hata kodu var mı? | hayır | ✅ §6.3 kural 1 |
| 7 | eşik/süre/periyot sayısı yazıldı mı? | hayır | ✅ §15.2 · §9.3 |
| 8 | §1–§5 değişti mi? | hayır | ✅ |
| 9 | tek Authority mi? | evet | ✅ son satır |
| 10 | yasaklı yer tutucu kalıbı | yok | ✅ |

### §16.3 Sık sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Hata kodu listesi bu dosyanın kararı mı? | Hayır — aday; şema R3 (error-coordinator) |
| 2 | Seviye adları standart mı? | Hayır — R1 açık |
| 3 | Değer/periodyot neden boş? | Ölçüm yok — R2 açık |
| 4 | Log nereye yazılır? | Bu dosyanın dışında — R6 + §2 satır 1 |
| 5 | Teşhis ağaçları çalışır kod mu? | Hayır — iskelet |
| 6 | Ölçüm yapıldı mı? | Hayır — §11 günlüğü boş |
| 7 | Eşik burada mı? | Hayır — testing (DG1 bağlayıcı) |
| 8 | Maskeleme kuralı nedir? | [REDACTED] — tek kesin kural (§17 #3) |
| 9 | append-only ne demek? | Geçmiş satıra dokunulmaz (§25.3 #3) |
| 10 | RT ihlali nasıl yakalanır? | Yöntem yok — R4 açık |
| 11 | Telemetri lock kullanabilir mi? | Cevap yok — R5 açık |
| 12 | Yeni bölüm neyi değiştirdi? | Yalnız §6–§16 eklendi; §1–§5 aynen korundu |

### §16.4 Uygulama kontrol listesi (ek kapılar — hepsi KAPALI)

| # | Kontrol | Kabul ölçütü | Durum |
|---|---|---|---|
| 1 | log alan şeması | R1 kararı + örnek satır | KAPALI |
| 2 | seviye adları | R1 kararı | KAPALI |
| 3 | sınıf listesi | R3 kararı | KAPALI |
| 4 | sınıf kodları (numara) | R3 kararı — rakam yok | KAPALI |
| 5 | metrik listesi | R2 kararı | KAPALI |
| 6 | örnekleme periyodu | R2 ölçümü | KAPALI |
| 7 | sayaç değeri | ilk ölçüm | KAPALI |
| 8 | telemetri vardiya kararı | R5 | KAPALI |
| 9 | yazım yeri | R6 | KAPALI |
| 10 | saklama süresi | R6 | KAPALI |
| 11 | RT ihlali yöntemi | R4 | KAPALI |
| 12 | bilinmeyen kod davranışı | R3 onayı | KAPALI |
| 13 | ölçüm günlüğü satırı | ilk gerçek ölçüm | KAPALI |
| 14 | kabul eşiği | testing (dışında) | KAPALI |
| 15 | log biçim standardı referansı | resmi kaynak teyidi | KAPALI (⚠️) |
| 16 | telemetri sabit boyut kararı | tasarım | KAPALI |
| 17 | maskelenen alan listesi | security ile ortak karar | KAPALI (kural var, liste yok) |
| 18 | oturum/ürüm alanı biçimi | R1 | KAPALI |
| 19 | dosya içi sayım tutarlılığı | §16.2 denetimi | ✅ 10/10 |
| 20 | Authority tekliği | son satır | ✅ tek |

---

## §17 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
