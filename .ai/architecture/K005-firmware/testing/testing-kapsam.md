---
node_id: K005-firmware/testing/testing-kapsam
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/testing
  - K005-firmware/boot
evidence:
  - "K005-firmware/testing/index.md (ELI15 4 senaryo · open_questions · children) — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §2 · §3 · §5 · §7 · §9 · §11 · §20 · §21 · §25 · §27 · §29 — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §5 (S20) · §9.9 (T1–T5) · satır I11 · satır 322 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §4 (F14) · §19.9 (t1–t5) · §20 (TS1–TS14) — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §17 (D14 · E1j) · §20.5 — okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §2 (testing satırı) · §10 Q1 — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md §2 · §21 (testing satırı) — okundu 2026-10-14"
  - "K005-firmware/firmware-evrensel-dil.md §2 (terim 24 HIL) · §5 — okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md §20 (TI1–TI16) — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-kapsam.md §A-§C — kardeş şablon, okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "test kapsamı hedefi tanımlı değil ⚠️ (index.md open_question — çözülmedi, KAPSAM/Kabul arasında açık taşındı)"
  - "HIL fixture (donanım tezgâhı) tanımlı değil — T3 kabulü ölçülemiyor ⚠️ UNKNOWN"
  - "G9 firmware test workflow'u yok — T1/T2 firmware yüzeyi CI'ya bağlanamıyor ⚠️"
  - "Performans eşikleri (açılış süresi · jitter) sahibi ve değeri belli değil ⚠️ (TS1 · TS3)"
group: mimari
---

# Testing Alt-Modülü — Kapsam (K005-firmware/testing/testing-kapsam.md)

> Bu dosya, `K005-firmware/testing/` alt-modülünün **kapsam** dokümanıdır.
> Repo'da derlenebilir C/C++ firmware kodu BULUNMAMAKTADIR (0 dosya —
> 2026-10-13 keşif) ve HIL tezgâhı yoktur — bileşen listesi, kabul
> kriterleri ve arayüzler **mimari taslaktır**; kesin eşik/kod referansı
> verilmez. **Test kapsamı hedefi (yüzde) tanımlı değildir** ve bu dosyada
> da tanımlanmaz (açık soru, §A.3).

## §A Kapsam

### §A.1 KAPSAR

- Test piramidi L1–L6 katmanlarının testing çocuğunda uygulanması (unit · integration · system · regression · negative · performans)
- Senaryo çekirdekleri S1–S10'un test/ çocuk dosyalarında detaylaştırılması (strateji §5 · §25 iskelet)
- CI gate takibi G1–G9: 8 gerçek kapı + eksik G9'un raporlanması (strateji §3)
- D-kapılarının test karşılıklarının uygulanması (strateji §4 · anayasa §3)
- Ölçüm ve kayıt (M1–M8) · flaky politikası (5 adım) · coverage yorumu (strateji §6 · §16 · §17)
- HIL senaryo tasarımı ve PLANNED durum disiplini (strateji §20)
- Risk ↔ senaryo izlenebilirliği: her yeni risk için ≥1 senaryo (strateji §7 kural 5 · §14)
- Test kipi üzerinden hata senaryosu tetikleme (kapsam §5 S20)
- Rapor üretimi: sonuç → geç/kal → rapor dosyası (t4 · t5)

### §A.2 KAPSAMAZ

- Host yüzeyi PHP/JS testlerinin **yazımı** (mevcut: G4 PHPUnit · G5 Vitest — QA/DevOps sahibi)
- DSP/algoritma doğrulama eşiklerinin belirlenmesi (→ `audio-control` · K004; strateji §19 köprüsü)
- Donanım devresi · röle · PCB tasarımı (→ K001-donanım · `.ai/electronics/`)
- OTA yükleme/imza/rollback tasarımının kendisi (→ `update`; testing yalnız HIL provasını ister)
- Secure boot politikası · anahtar yaşam döngüsü (→ `security`)
- Telemetri saklama/raporlama altyapısı (→ `diagnostics`)
- Firmware kodunun kendisi (repo'da yok — 0 dosya) ve hedef MCU seçimi (K-10 · K-11 — `UNKNOWN`)

### §A.3 Kabul Kriterleri (T1–T5 — hepsi dalga-3)

| # | Kriter | Hedef | Ölçüm yöntemi | Durum (2026-10-14) |
|---|---|---|---|---|
| T1 | Unit test (saf fonksiyonlar, bağımlılık soyutlama, çalıştırma ortamı) | firmware yüzeyi: **hedef tanımsız ⚠️** (host yüzeyi G4/G5 mevcut) | CI raporu (G4/G5) + M1–M8 | host: kapı var · firmware: dalga-3 |
| T2 | Integration test (bus/driver sahteleriyle birlikte deneme) | sahte (stub) sürücü t2 ile | strateji §2 L2 | ⚠️ simülatör/stub henüz yok |
| T3 | HIL (gerçek donanım üstünde otomatik senaryo) | donanım-geçti — **hiçbir satır "geçti" sayılmaz** | strateji §20 (4 kalem) | PLANNED — tezgâh yok |
| T4 | Kabul kriteri (ne geçerse "bitti" — sayısal eşikler) | eşiklerin çoğu `⚠️ UNKNOWN` (TS1 · TS3 · TS6 · TS10) | strateji §11 M1–M8 | M7 = 0 (uydurma yok) |
| T5 | Örtü/rapor (sonuç nereye yazılır, nasıl okunur) | rapor şablonu | strateji §25 iskelet | taslak — dalga-3 |

> **Kabul kuralı (K1):** "Test kapsamı hedefi tanımlı değil ⚠️" —
> `testing/index.md` open_question **çözülmemiştir**; T1–T5'te hedef sütunu
> bu yüzden `⚠️` işaretlidir. Yüzde hedefi G9 kapatılmadan sabitlenmez
> (strateji §17 madde 4); ölçülmeden yazılmaz (§7 kural 1).

### §A.4 Bağımlılıklar

| Bağımlılık | Tür | Yön | Not |
|---|---|---|---|
| `K005-firmware` (kök) | Modül | testing → kök | D-kapıları · master §2 rol satırı |
| `K005-firmware/boot` | Modül | testing → boot | TI1–TI16 test girdileri (boot-surucu §20) |
| `K005-firmware/update` | Modül | testing → update | S5 geri alma senaryosu — risk 3 🔴 devri |
| `K005-firmware/diagnostics` | Modül | testing ↔ diagnostics | test kipi (G5) · E_DIAG_TEST (H18) |
| `K005-firmware/device-control` | Modül | testing → device-control | DV-1…DV-12 tetikleri · stub sürücü t2 |
| `K005-firmware/security` | Modül | testing ↔ security | L5 negatif imza senaryosu (S2) |
| `.github/workflows/ci.yml` · `secret-scan.yml` | CI | testing → CI | G1–G8 kapıları (okundu 2026-10-14) |
| Gerçek donanım (DAC · röle · osilloskop · DMM) | Harici | HIL | PLANNED — yok (strateji §20 · §21) |
| Hedef MCU platform/dil | Harici | — | `UNKNOWN` (K-10 · K-11) — tahmin yazılmaz |

## §B Bileşen Listesi (t1–t5 + F14)

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| t1 | Test koşucusu | F14 | senaryo | sonuç | dalga-3 |
| t2 | Sahte (stub) sürücü | F6/F7 | komut | kayıtlı yanıt | dalga-3 |
| t3 | HIL kıyaslama | F14 | donanım | rapor | dalga-3 |
| t4 | Eşik denetleyici | F14 | ölçümler | geç/kal | dalga-3 |
| t5 | Rapor yazar | F14 | sonuç | rapor dosyası | dalga-3 |

**Bileşen kuralı (K2):** F14 = "Test kıyaslama (HIL)" — testing tek
sahiptir (`firmware-bilesenler.md` §4). Tümü `dalga-3` etiketlidir; bileşen
**tarif edilmiştir, üretilmemiştir** (repo'da test çalıştırıcısı yok).

**Ölçüm sözleşmesi (TS1–TS14 — seçilmiş):**

| # | Bileşen grubu | Nasıl test edilir | Eşik |
|---|---|---|---|
| TS1 | F1 boot | Bölüm/hex inceleme + hedefde | ⚠️ UNKNOWN (açılış hedefi yok) |
| TS5 | F5 watchdog | Zaman aşımı zorlama | %100 (kurtarma başarısı) |
| TS12 | F12 update | Sahte güç kesme (10 durum) | %100 (kurtarma) |
| TS14 | F14 HIL | **Gerçek donanım** | tüm eşikler → strateji §5 |

> TS14'ün kaynağı strateji §5'tir; eşiklerin çoğu `UNKNOWN` olduğundan
> §A.3 T4 ile çelişmez — ikisi de "eşik yok" der.

## §C Arayüzler

### §C.1 Dahili arayüz (taslak imzalar)

```text
Taslak: test koşucu arayüz sözleşmesi — repo'da karşılığı dosya yok; dosya/satır künyesi verilmez.
Kaynak: firmware-test-stratejisi.md §25 (senaryo iskeleti) · firmware-bilesenler.md §19.9 (t1–t5).

runScenario(SenarioId)            -> Sonuc   // t1: senaryo -> sonuç (PLANNED)
stubCommand(Komut, YanitKaydi)    -> void    // t2: bus/driver sahtesi (PLANNED)
hilMeasure(Olcum)                 -> Rapor   // t3: donanım -> rapor (PLANNED)
checkThreshold(Olcum, Esik)       -> GecKal  // t4: ölçümler -> geç/kal (Esik: UNKNOWN)
writeReport(Sonuc)                -> RaporDosyasi // t5: sonuç -> rapor dosyası (PLANNED)
```

### §C.2 Dış arayüzler

| # | Arayüz | Yön | Ne geçer | Durum |
|---|---|---|---|---|
| A1 | Test tetikleyici TI-T1…TI-T16 | testing → test ortamı | tetik/olay (kod değil) | SÖZLEŞME (`testing-surucu.md` §2) |
| A2 | Boot test girdisi TI1–TI16 | boot → testing | reset · bozuk imza · retry · debug tetikleri | SÖZLEŞME (`boot-surucu.md` §20) |
| A3 | CI gate G1–G8 | testing ↔ GitHub Actions | job sonuçları · rapor | aktif (2026-10-14 okundu) |
| A4 | CI gate G9 | testing → CI | firmware/HIL işi | **yok** — ⚠️ eksik |
| A5 | HIL ekipman H1–H7 | testing ↔ tezgâh | ölçüm sinyali | PLANNED (`testing-surucu.md` §5) |
| A6 | Hata kodu `E_TST_FAIL` (H17) | testing → rapor/diagnostics | geç/kal sonucu | taslak |
| A7 | DV-1…DV-12 tetikleri | testing → device-control | doğrulama tetiği | devir (kardeş anayasa §10) |

## §D Test Ortamları

| Ortam | İçerik | Kapı |
|---|---|---|
| CI (GitHub Actions) | PHP + Node işleri | G1–G8 |
| Geliştirici makinesi | `composer test` · `composer stan` | yerel |
| Ses laboratuvarı | gerçek DAC + ölçüm cihazı | HIL (PLANNED) |
| Sahada | kullanıcı cihazı | yok (yalnız geri bildirim) |

> Ortam tablosu `firmware-test-stratejisi.md` §21'den aktarılmıştır; dördüncü
> satırda "kapı: yok" yazar — saha verisi test kapısı değildir.

## §E Kapsam Sınırı Kararları (devir · çakışma)

| # | Konu | Karar | Kaynak |
|---|---|---|---|
| E1 | Risk 3 (rollback 🔴, skor 15) | HIL senaryosu testing kapsamındadır | `firmware-riskler.md` §2 · §21 |
| E2 | F14 sahipliği | testing (tek sahip) | bilesenler §4 |
| E3 | HIL terimi (evrensel-dil #24) | bağlayıcı terim testing'e ait | evrensel-dil §2 · §5 |
| E4 | Test kipi kararı | kipi diagnostics belirler, bayrak runtime'da yaşar | kapsam §9.6 Ov7 |
| E5 | master §2 testing satırı | "Unit · integration · HIL testleri" — **adından türetildi ⚠️** (v2 §2.5 notu) | firmware-master §2 |
| E6 | Dosya yerleşimi | `testing-kapsam.md` kök dosyanın §27 planında; bu üretim plana uyar | strateji §27 |

> **Çakışma kaydı (rapor):** `firmware-master.md` §2 testing satırı hâlâ
> "⏳ yalnız eski index.md" der ve `firmware-riskler.md` §21 testing satırı
> "yalnız index.md" der — bu iki satır bu üretimde **düzeltilemez** (başka
> dosyalara dokunma yasağı). Çelişki kapanış raporundadır.

## §F Açık Sorular (çözülmeden taşındı)

| # | Soru | Etiket |
|---|---|---|
| Q0 | **test kapsamı hedefi tanımlı değil** (index open_question) | `⚠️` — KAPATILMADI |
| Q1 | HIL fixture/tezgâh tanımı ne zaman? (T3) | `⚠️ UNKNOWN` |
| Q2 | G9 workflow'u ne zaman eklenir? (T1/T2 CI bağlantısı) | `⚠️ VERIFY REQUIRED` |
| Q3 | Performans eşikleri kime ait? (TS1 · T4) | `⚠️ VERIFY REQUIRED` |
| Q4 | Sahte (stub) sürücü t2 hangi bus arayüzünü sahteler? (bus topolojisi `UNKNOWN`) | `⚠️` |


---

## §G Bileşen Ayrıntısı (t1–t5 + F14 · görev-girdi-çıktı derinliği)

| Kod | Bileşen | Görev | Girdi | Çıktı | Kapsam dışı |
|---|---|---|---|---|---|
| t1 | Senaryo koşucusu | DV senaryolarını sırayla çalıştırır | senaryo listesi + ortam | sonuç satırları (§C.1) | üretimi test (uygulama kodu) |
| t2 | Kapı denetleyici | G-kapıları değerlendirir | sonuç satırları + kapı kriteri | PASS/BLOCK/BELIRSIZ | kapı tanımı (firmware-test-stratejisi) |
| t3 | Kanıt toplayıcı | dosya+satır kanıtlarını biriktirir | sonuçlar | kanıt defteri | kanıt saklama (diagnostics) |
| t4 | Flaky izleyici | tekrar-labilen testleri işaretler | tekrar verisi | flaky işareti + rapor | test düzeltmesi (ilgili sahip) |
| t5 | Coverage yorumlayıcı | raporu okur, yorum üretir | coverage raporu | yorum satırı (eşik YOK) | yüzde hedefi koyma (yasak) |
| F14 | HIL düzeneği | fiziksel döngü testi sağlar | ekipman + senaryo | fiziksel sonuç | workflow kurulumu (DevOps) |

### §G.1 Bileşen ↔ bağımlılık

- t1 → t3 (sonuç üretir) · t2 → t1/t3 (değerlendirir) · t4 → t1 (tekrar verisi).
- t5 → t3 (rapor okur) · F14 → t1 (fiziksel ortam) — F14, G9 workflow **tanımlı olmadığı**
  için şu an **kullanılamaz** (testing-ornekler §B.2 · workflow YOK kaydı).
- Yön kuralı: bileşenler kapı **tanımlar değil**, kapıya **veri** üretir (t2 yalnız
  değerlendirir; kriter sahibi firmware-test-stratejisi).

### §G.2 Her bileşenin başarısızlık modu

| Bileşen | Başarısızlık | Sonuç davranışı |
|---|---|---|
| t1 | senaryo çalışmaz | sonucu BLOCKED (sessiz geçiş yok) |
| t2 | kriter okunamaz | BELIRSIZ — PASS üretilemez |
| t3 | kanıt yazılamaz | sonuç **geçersiz sayılır** (kanıtsız PASS yasak) |
| t4 | tekrar verisi eksik | işaretleme yapılmaz; boşluk not edilir |
| t5 | rapor yoksa | yorum üretilmez (boşluk dürüst yazılır) |
| F14 | ekipman yok | senaryo BLOCKED (simülasyonla geçmez) |

---

## §H Test Katmanları — Tanım Derinliği (L1–L6)

### §H.1 Katman tanımları (kimde, ne ölçer)

| Katman | Ne ölçer | Ne ÖLÇMEZ | Sahip |
|---|---|---|---|
| L1 birim | iş mantığı kararları | donanım davranışı | geliştirici |
| L2 bileşen | modül arayüzleri | zamanlama | geliştirici |
| L3 bütünleşik | modüller arası akış | fiziksel sinyal | QA |
| L4 sistem | firmware + ortam davranışı | insan faktörü | QA |
| L5 HIL | fiziksel döngü (cihaz) | üretim filosu | QA + DevOps |
| L6 filo/canary | dağıtım davranışı | tekil cihaz içi hata | DevOps |

1. Katman **kapsam dışı** satırları bağlayıcıdır: L1'de zamanlama testi "geçti"
   sayılmaz (yanlış katmanda ölçüm, ölçüm değildir).
2. L5 bu projede **PLANNED** (G9 workflow yok) → L5 sonuç satırı üretemz.
3. L6, update dalının kademeli dağıtım kapısıyla ilişkilidir (update §K · oradan çağrılır).

### §H.2 Katman ↔ kapı eşlemesi

| Kapı | Minimum katman | Not |
|---|---|---|
| G-araştırma (lint) | L1 | depo CI'ı mevcut (ci.yml okundu 2026-10-14) |
| G-bütünleşik | L2–L3 | firmware kapsamı bu depoda değil (kod yok) |
| G-firmware | L4–L5 | workflow TANIMLI DEĞİL → kapı BELIRSIZ |
| G-HIL (G9) | L5 | workflow YOK → BLOCKED |
| G-coverage (G6) | L1+ | eşik YOK (hedef tanımsız — yasak) |

### §H.3 Katmanlar arası kanıt aktarımı

1. Alt katman sonucu üst katmanın **kanıtıdır**, yerine geçmez (L1 PASS ≠ L4 PASS).
2. Aktarım formatı testing-surucu §1 sorgusu (t1 sonuç şeması) üzerinden.
3. Aktarımda kayıp varsa (ör. ekipman yok) üst katman **BLOCKED** yazılır —
   eksik veriyle PASS yasaktır.

---

## §I Ortam, Kanıt ve Kayıt Sahipliği

| Kayıt | Üretici | Sahip | Saklama | Kullanıcı |
|---|---|---|---|---|
| Senaryo sonucu | t1 | testing | kanıt defteri (t3) | t2 · Vault Steward |
| Kapı kararı | t2 | firmware-test-stratejisi (kriter) | rapor | QA · DevOps |
| Kanıt satırı | t3 | testing | kanıt defteri | denetçi (audit) |
| Flaky işareti | t4 | testing | rapor | geliştirici |
| Coverage raporu | CI | ilgili job | CI artefaktı | t5 (yorum) |
| HIL ekipman kaydı | F14 | DevOps + QA | HIL defteri | t2 |

### §I.1 Kanıt defteri kuralları (kanıt grubu ile uyum)

1. Her satır: ts · scen · env · verdict · evidence (dosya+satır) · actor.
2. Kanıtsız sonuç **yoktur**; kanıt yazılamıyorsa sonuç iptal edilir (§G.2 t3).
3. Defter append-only'dir; silme yalnız ADR ile (audit bütünlüğü).

### §I.2 Ortam ayrımı (geliştirici makinesi ↔ CI ↔ HIL)

- Geliştirici makinesi: L1–L2 · firmware-test-stratejisi §21 (çalıştırma ortamı tanımı).
- CI: L2–L3 + lint/coverage job'ları (gerçek workflow: ci.yml · secret-scan.yml —
  2026-10-14 okuma; firmware dalı iş akışı **yok**).
- HIL: L5 · F14 · ekipman listesi bu dosyada **üretilmez** (equipment adı uydurmak yasak).

---

## §J Kapsam Sınırı Kararları — Genişletme (devir · çakışma)

### §J.1 Çakışma kayıtları (çözülmemiş — dürüstlük)

| # | Çakışma | Taraf 1 | Taraf 2 | Durum |
|---|---|---|---|---|
| Ç1 | Kapı kriteri sahipliği | firmware-test-stratejisi (tanım) | testing (uygulama) | BÖLÜNMÜŞ — tanımla-uygula ayrımı bilinçli |
| Ç2 | Coverage eşiği | t5 (yorum) | CI (rapor) | EŞİK YOK — Q0 açık |
| Ç3 | HIL kurulumu | testing (F14) | DevOps (workflow) | BEKLEMEDE — G9 tanımsız |
| Ç4 | Flaky düzeltmesi | testing (işaretler) | ilgili modül sahibi (düzeltir) | DEVİRLİ — testing yazmaz |
| Ç5 | Firmware test kodu | testing (şartname) | kod sahibi (yok) | BLOKE — depoda firmware kodu yok |

### §J.2 Çakışma çözüm kuralı

1. Tanım ≠ uygulama: kriter satırı yalnız firmware-test-stratejisi'nde değişir;
   testing o satırı **kopyalamaz**, referans verir (SSOT).
2. Eşik yalnız ölçüm sonrası konabilir (Q0 çözülmeden sayısal eşik = uydurma).
3. Sahipsiz iş (Ç5) kapıda BLOCKED sayılır; sahipsiz bırakılırsa kapı **gizlenir** —
   bu vault'ta gizleme yasak.

### §J.3 Kapsam dışı (bilinçli — sahibiyle)

| Kapsam dışı | Sahibi | Testing'e düşen |
|---|---|---|
| Uygulama/onay senaryosu üretimi | ilgili modül (boot/update/…) | yalnız şartname + kapı |
| CI workflow dosyası yazımı | DevOps | gereksinim + kabul |
| Cihaz/HIL ekipmanı temini | DevOps/Donanım | ekipman şartnamesi |
| Üretim filosu telemetri | update/diagnostics | yalnız L6 talebi |

---

## §K Bağımlılık Grafiği — Genişletme (yön + kritik yol)

### §K.1 Kenar tablosu (ek kenarlar)

| Kaynak | Hedef | Tür | Kritik yol mu |
|---|---|---|---|
| t1 | t3 | veri (sonuç) | evet (kanıt yoksa geçersiz) |
| t3 | t2 | veri (kanıt) | evet |
| t2 | kapı raporu | karar | evet |
| t4 | t1 | geri besleme (tekrar) | hayır |
| t5 | rapor | yorum | hayır |
| F14 | t1 | ortam | HIL senaryolarında evet |
| G9 (workflow) | F14 | altyapı | **bloke edici — yok** |

### §K.2 Kritik yol ve blokajlar

1. Kritik yol: t1 → t3 → t2 → kapı raporu → Vault Steward.
2. Blokaj B1: firmware test edilecek kod **yok** (depoda cpp/h = 0) — t1 firmware
   senaryoları için girdi üretemz; yalnız **şartname** üretilir (bu dosyanın işi).
3. Blokaj B2: G9 workflow yok → F14 → L5 kapısı kapalı.
4. B1/B2 çözülmeden kapı sonucu yalnız **BELIRSIZ/BLOCKED** olabilir.

### §K.3 Bağımlılık → risk

- Tek nokta bağımlılık (t3 kanıt yazımı): kanıt deposu çökerse tüm kapılar durur →
  yedek kanıt yolu **tanımlı değil** (açık kayıt).
- DevOps bağımlılığı (G9): en pahalı çözüm — erken planlanmalı (§J Ç3).

---

## §L Kaynak ve Dayanak Kanıtı (bu dosyanın derinleştirmesi)

| İddia | Kaynak | Konum | Okuma |
|---|---|---|---|
| Test piramidi L1–L6 iskeleti | K005-firmware/testing/testing-anayasa.md | §2 | 2026-10-14 |
| G-kapıları gerçek workflow kaydı | K005-firmware/firmware-test-stratejisi.md | §3 | 2026-10-14 |
| Coverage eşiği yok (Q0) | K005-firmware/firmware-test-stratejisi.md | §17 | 2026-10-14 |
| HIL 5 adım (geliştirme planı) | K005-firmware/firmware-bagimliliklar.md | §20.5 | 2026-10-14 |
| t1 test koşucusu bileşeni | K005-firmware/firmware-bilesenler.md | §19.9 | 2026-10-14 |
| CI workflow envanteri | .github/workflows/ (ci.yml · secret-scan.yml) | tam okuma | 2026-10-14 |
| WCET ölçüm protokolü (çapa) | .ai/electronic/firmware/rtos.md | Satır 219 (§12.1) | 2026-10-15 |
| Gecikme ölçüm yöntemi (çapa) | .ai/electronic/drivers/audio-drivers.md | Satır 535 (§7.1) | 2026-10-15 |
| Firmware kodu yok (kanıt sınırı) | 2026-10-15 ölçümü | cpp/h = 0 | 2026-10-15 |

### §L.1 İddia kuralı (bu dosya)

1. Çalıştırılmış/ölçülmüş iddiası: **0** — tüm kapılar PLANNED/BLOCKED.
2. Erişilemeyen kaynak: ⚠️ VERIFICATION REQUIRED (uydurma URL yok).
3. Ekipman/marka adı: yalnız vault kaynaklı; bu ekte yeni ad üretilmedi.

---

## §M Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Mevcut §A–§F silindi mi? | HAYIR (append-only) |
| Frontmatter | korundu (10 alan + group) |
| Dolgu/TBD satırı | 0 |
| Uydurma eşik/marka/URL | 0 |
| Kapı "geçti" iddiası | 0 |
| Son satır Authority | birebir korunur |

### §M.1 Sonraki üretim koşulu

- Q0 (coverage eşiği) çözülmeden §H.2 tablosuna eşik satırı **eklenmez**.
- G9 workflow'u tanımlanırsa §G.2/F14 satırı yeni append ile güncellenir.


---

## §N Senaryo Kaydı Veri Modeli (şartname — uygulama yok)

### §N.1 Zorunlu alanlar (t1 girdisi)

| Alan | Tip | Açıklama | Boş bırakılabilir mi |
|---|---|---|---|
| scen | kod | DV-T / S-* serisi | HAYIR |
| katman | L1–L6 | §H.1 tanımı | HAYIR |
| env | ortam kodu | DX/I1/I2… (bu dosyada tanımlı değil → UNKNOWN) | HAYIR |
| uyaran | metin | ne verildi | HAYIR |
| beklenen | metin | ne bekleniyor | HAYIR |
| kanit_yolu | dosya+satır | sonuç kanıtı | HAYIR (yoksa sonuç geçersiz) |
| verdict | enum | PASS/FAIL/BLOCKED/INVALID | çalıştırma sonrası |

### §N.2 Senaryo lifecycle durumları

| Durum | Anlamı | Geçişler |
|---|---|---|
| TASLAK | şartname yazıldı | → HAZIR (inceleme) |
| HAZIR | bağımlılık + fixture hazır | → ÇALIŞTI |
| ÇALIŞTI | çalıştırıldı | → KAPANDI |
| BLOKE | ön koşul eksik (ör. G9) | → HAZIR (koşul gelince) |
| INVALID | fixture/kanıt bozuk | → HAZIR |
| KAPANDI | sonuç + kanıt defterde | (son — append ile düzeltme) |

### §N.3 Yaşam döngüsü kuralları

1. TASLAK senaryo kapıya **girmez** (içeleme: inceleme gereken kapı verisi).
2. BLOKE, FAIL sayılmaz — kapı raporunda ayrı satır (karıştırma yasak).
3. KAPANDI senaryonun sonucu yalnız append ile **düzeltilebilir** (yeni satır);
   eski satır silinmez (audit bütünlüğü §I.1).

---

## §O Kabul Kriterileri — Derinletme (T1–T5)

| Kriter | Bu dosyadaki derinlik | Bağlantı | Durum |
|---|---|---|---|
| T1 Senaryo kapsamı | her alt-dal DV serisi ≥1 senaryoya sahip | anayasa §3 | Kısmi (firmware kodu yok) |
| T2 Kapı tanımı | her G-kapısının kriteri yazılı | firmware-test-stratejisi §3 | Evet (tanım) |
| T3 Kanıt zinciri | sonuç → kanıt satırı → kapı → rapor | §I.1 | Evet (şartname) |
| T4 Flaky disiplini | tekrar-labilen işaretlenir; kapıyı geçici geçmek yasak | anayasa §6 | Evet (şartname) |
| T5 HIL kapısı | L5 eşiği yalnız G9 sonrası | §G F14 | BLOKE (workflow yok) |

### §O.1 Kabul kriteri ↔ kapı sonucu davranışı

1. T1–T3 tamamsa kapı **değerlendirilebilir**; ama "geçti" için ayrıca çalıştırma gerekir.
2. T4 sinyali varsa kapı sonucu **"geçti (flaky)"** olarak ayrı işaretlenir —
   normal PASS ile karıştırılmaz (dürüstlük).
3. T5 bloke iken genel firmware kapısı **BELIRSIZ** yazılır (gizleme yasak).

### §O.2 Kabul kriteri sahipliği (RACI özeti)

| Kriter | R (yapan) | A (onay) | C (danışılan) | I (bilgilendirilen) |
|---|---|---|---|---|
| T1 | testing | QA | modül sahipleri | Vault Steward |
| T2 | firmware-test-stratejisi sahibi | QA | DevOps | testing |
| T3 | t3 bileşeni | Vault Steward | denetçi | QA |
| T4 | t4 + geliştirici | QA | modül sahibi | Vault Steward |
| T5 | DevOps + QA | DevOps | donanım | Vault Steward |

---

## §P Risk → Test İzi (devralınan riskler — testing açısından)

| Risk | Test karşılığı | Katman | Durum |
|---|---|---|---|
| R-1 boot güvenilirliği | S-BOOT-01 (anayasa/ornekler) | L3–L4 | PLANNED |
| R-2 cihaz komut güvenilirliği | S-DEV-02 | L2–L3 | PLANNED |
| R-3 update geri alma | S-UPD-01 (risk 3) | L3–L5 | PLANNED · L5 bloke |
| R-4 güç kesintisi dayanıklılığı | S-PWR-01 | L5 | BLOKE (G9) |
| R-5 güvenlik imza yolu | DV-S serisi (security dalı) | L2–L4 | PLANNED · K-S2 bloke |
| R-6 xrun/timing | DV-RT serisi (runtime dalı) | L4 | PLANNED |

### §P.1 İz kuralı

1. Her risk ≥1 senaryoya bağlanır; bağlantısız risk **kabul edilmez** (kapıda sayılır).
2. Senaryo → kapı → rapor üçlüsü olmadan risk **kapanmaz** (anayasa §9 K-15 zinciri).
3. Yeni risk → bu tabloya append; silme yalnız ADR.

---

## §Q Kullanım ve Devam Koşulları (bu ek)

### §Q.1 Kim ne okur

1. **QA**: §G (bileşen) + §H (katman) + §O (kabul) — test planı kurarken.
2. **DevOps**: §J Ç3 + §K.1 (G9 bağımlılığı) — altyapı planında.
3. **Geliştirici**: §P (risk izi) + §G.2 (başarısızlık modları).
4. **Vault Steward**: §I (sahiplik) + §N.3 (append düzeltme kuralı).

### §Q.2 Sonraki üretim

- Q0/G9 çözümlerinde bu dosyaya **yeni bölüm (§R)** açılır; §H/§J tabloları
  append satırla güncellenir (silme yok).
- Ekipman/envanter adları yalnız vault kaynaklı eklenebilir (uydurma yasak).


---

## §R Terimler ve Kısaltmalar (bu ek sözlüğü)

| Terim | Anlamı (bu dosyadaki) |
|---|---|
| DV-T / S-* | testing doğrulama senaryosu serisi (anayasa §3 · ornekler §A) |
| L1–L6 | test katmanları (§H.1) |
| t1–t5, F14 | testing bileşen envanteri (§B · §G) |
| Q0 | coverage eşiği açık sorusu (çözülmedi) |
| G9 | firmware HIL workflow kapısı — TANIMLI DEĞİL |
| BLOKE | ön koşul eksik; FAIL değildir (§N.2) |
| INVALID | fixture/kanıt bozuk; FAIL değildir |
| BELIRSIZ | kapı kriteri okunamaz veya kanıt eksik |
| kanıt defteri | append-only sonuç+kanıt kaydı (§I.1) |
| flaky | tekrar-labilen test işareti (§G t4) |

### §R.1 Ek kapanış cümlesi

Bu ek (§G–§R) testing-kapsam.md dosyasını bileşen görev derinliği, katman/kayıt
sahipliği, çakışma/blokaj kayıtları, veri modeli, RACI, risk izi ve sözlükle
tamamlamıştır; mevcut §A–§F ile frontmatter append-only korunmuştur. Kapılar
tanımlıdır, **hiçbiri geçti olarak işaretlenmemiştir** (firmware kodu yok · G9 yok).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode