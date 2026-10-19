---
node_id: K005-firmware/testing/testing-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/testing
  - K005-firmware/boot
  - K005-firmware/update
evidence:
  - "K005-firmware/testing/index.md (ELI15 — 4 senaryo: açılış · komut yankısı · güncelleme/geri alma · güç kesintisi dayanıklılığı) — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §5 (S1–S10) · §14 · §20 · §21 · §25 · §29 · §30 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §19.9 (t1–t5) · §23 (H8 · H9 · H17) — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §20.5 (HIL 5 adım) — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md §2 (risk 3 🔴) · §21 — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-ornekler.md §A-§B — kardeş şablon (Taslak/Kaynak disiplini), okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md §20 (TI1–TI16) — okundu 2026-10-14"
  - ".github/workflows/ci.yml — G6/G7 soft kapı gerçeği, tam dosya okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "test kapsamı hedefi tanımlı değil ⚠️ (index.md open_question — çözülmedi; örneklerde eşik sütunu UNKNOWN)"
  - "HIL tezgâhı yok — dört senaryo da PLANNED, hiçbiri koşulmadı"
  - "Senaryo zarfı ataması: komut yankısı için S-DEV-02 kullanıldı (mevcut S-DEV zarfı, §29) — onay bekliyor ⚠️"
  - "strateji §5 risk numaraları ile §14 R-zarf eşlemesi birleştirilmedi ⚠️ (eşitlik uydurulmadı)"
group: kod
---

# Testing Alt-Modülü — Örnekler (K005-firmware/testing/testing-ornekler.md)

> Bu dosya, `K005-firmware/testing/` alt-modülünün **örnekler**
> dokümanıdır. Repo'da derlenebilir firmware kaynak kodu BULUNMAMAKTADIR
> (0 dosya — 2026-10-13 keşif) ve HIL tezgâhı yoktur. Aşağıdaki senaryolar
> **taslak referanstır**; her bloğun başlığında `Taslak:` ve `Kaynak:`
> etiketi taşır, kesin dosya konumu iddia edilmez. Dosya/satır künyesi bu
> dosyada **kullanılmaz**. Dört senaryo `testing/index.md` ELI15'inin
> birebir karşılığıdır ve `firmware-test-stratejisi.md` §25 iskeletiyle
> üretilmiştir.

## §A Senaryo Detay Şablonu (§25 iskeletiyle — Durum: PLANNED)

### §A.1 Genel bakış

| # | Senaryo (index ELI15) | Çekirdek | Zarf | Katman | Gate (strateji §14) | Durum |
|---|---|---|---|---|---|---|
| 1 | Açılış testi | S1 (soğuk açılış) | S-BOOT-01 | L1 | G5 · firmware: G9 **YOK** ⚠️ | PLANNED |
| 2 | Komut yankısı | S8 (hatalı host komutu — negatif kol dahil) | S-DEV-02 (onay ⚠️) | L1 (taslak atama) | firmware: G9 **YOK** ⚠️ | PLANNED |
| 3 | Güncelleme / geri alma provası | S5 (A/B güncelleme + geri alma) | S-UPD-01 | L3 | G7 (soft) · firmware: G9 **YOK** ⚠️ | PLANNED |
| 4 | Güç kesintisi dayanıklılığı | S4 (brown-out · güç kesintisi) | S-PWR-01 | L5 | G6 (soft) · firmware: G9 **YOK** ⚠️ | PLANNED |

> Zarf kuralı: `S-<ÇOCUK>-<sıra>` kalıbı §29'dadır; S-BOOT · S-UPD ·
> S-PWR · S-DEV zarfları §14 tablosunda **zaten kullanılmaktadır** — bu
> üretim **yeni zarf açmamıştır** (yeni zarf `firmware-evrensel-dil.md` §8
> kaydı ister).

### §A.2 S-BOOT-01 — Açılış (enerji → ana döngü)

```text
Taslak: S-BOOT-01 açılış senaryosu — bu senaryo HIL tezgâhında ÇALIŞTIRILMAMIŞTIR.
Kaynak: firmware-test-stratejisi.md §25 (iskelet) · §5 S1 çekirdeği · §14 R-05 eşlemesi · testing/index.md ELI15.

S-BOOT-01 Açılış (enerji → ana döngü)
  Katman    : L1 (strateji §14: R-05 → S-BOOT-01 → L1 · G5)
  Ön koşul  : firmware görüntüsü flash'ta; HIL tezgâhı BAĞLI (PLANNED — §20)
  Adımlar   : 1) enerji ver  2) reset vektörü → ilk-stage → ana firmware sırasını kaydet
              3) beslemeyi kes → yeniden aç  4) sırayı tekrar karşılaştır
  Beklenen  : reset vektörü → ilk-stage → ana firmware sırası (S1 çekirdeği), her açılışta aynı
  Ölçüm     : açılış süresi — ⚠️ EŞİK YOK (strateji §6 ölçüm 1); süre yazılır, "hedef tuttu" YAZILMAZ
  Gate      : G5 (§14 eşlemesi) · firmware zinciri: G9 — workflow YOK ⚠️
  Durum     : PLANNED (dalga-3)
```

### §A.3 S-DEV-02 — Komut yankısı (host ↔ firmware)

```text
Taslak: S-DEV-02 komut yankısı senaryosu — depoda test çalıştırıcısı YOK (t1 dalga-3).
Kaynak: firmware-test-stratejisi.md §5 S8 çekirdeği · §25 iskelet · §29 zarf kalıbı (S-DEV mevcut);
        firmware-bilesenler.md §23 (H8 E_CMD_RANGE · H9 E_CMD_UNKNOWN — taslak adlar) · §19.9 (t1 · t2).

S-DEV-02 Komut yankısı (host ↔ firmware)
  Katman    : L1 (taslak atama — çekirdek S8; katman onayı dalga-3 ⚠️)
  Ön koşul  : host arayüzü bağlanır; stub sürücü t2 hazır (dalga-3); test kipi (§9.6 G5) açık
  Adımlar   : 1) geçerli komut gönder, yanıt/yankı bekle
              2) hatalı komut gönder (bilinmeyen opcode)
              3) aralık dışı argüman gönder  4) reddedilen komutları say
  Beklenen  : geçerli komut yanıt döner; hatalı komut → E_CMD_RANGE / E_CMD_UNKNOWN hata kodu (taslak)
              + log; SESSİZ YUTULMAZ (firmware-bilesenler.md §23 · anayasa A3)
  Ölçüm     : reddetme doğruluğu — hedef eşik tanımsız ⚠️ (kapsam §A.3 T4 · Q0)
  Gate      : G9 — firmware CI işi YOK ⚠️ (host yüzeyi: G4 · G5 mevcut)
  Durum     : PLANNED (dalga-3)
```

### §A.4 S-UPD-01 — Güncelleme + geri alma provası (risk 3 🔴)

```text
Taslak: S-UPD-01 geri alma senaryosu — HIL EĞER DONANIM VARSA çalışır; şu an YOK.
Kaynak: firmware-test-stratejisi.md §5 S5 çekirdeği · §14 (R-03 → S-UPD-01 · L3 · G7);
        firmware-riskler.md §2 risk 3 (rollback 🔴 skor 15) · §21 (testing'e devredildi);
        firmware-bilesenler.md §20 TS12 (sahte güç kesme — %100 kurtarma).

S-UPD-01 A/B güncelleme + geri alma
  Katman    : L3 (strateji §14: R-03 → S-UPD-01 · L3 · G7)
  Ön koşul  : A/B bölmeli güncelleme tasarımı (update modülü) · geri-alma sayacı etkin
  Adımlar   : 1) yeni sürümü yaz  2) doğrulamayı bilerek boz (imza/CRC)
              3) geri alma sayacını gözle  4) ara adımda beslemeyi kes (parçalı güncelleme)
  Beklenen  : yeni bölüm doğrulanmazsa ESKİYE DÖNÜŞ; cihaz tuğla OLMAZ (S5 çekirdeği · risk 3)
  Ölçüm     : kurtarma başarısı %100 (TS12 — sahte güç kesme, 10 durum) · gerçek donanım ölçümü PLANNED
  Gate      : G7 (§14 eşlemesi — SOFT: ci.yml `continue-on-error: true`) · firmware: G9 YOK ⚠️
  Durum     : PLANNED (dalga-3)
```

### §A.5 S-PWR-01 — Güç kesintisi dayanıklılığı

```text
Taslak: S-PWR-01 güç kesintisi senaryosu — besleme kesme işlemi donanımda YAPILMAMIŞTIR.
Kaynak: firmware-test-stratejisi.md §5 S4 çekirdeği · §14 (R-04 → S-PWR-01 · L5 · G6);
        firmware-riskler.md §2 (risk 3 · risk 8) · §21; firmware-bilesenler.md §20 TS12.

S-PWR-01 Güç kesintisi dayanıklılığı
  Katman    : L5 (strateji §14: R-04 → S-PWR-01 · L5 · G6)
  Ön koşul  : cihaz çalışır durumda; kayıt/log köprüsü açık (diagnostics)
  Adımlar   : 1) cihaz çalışırken beslemeyi aniden kes
              2) brown-out gerilimini kademeli uygula (gerilim düşürme)
              3) yeniden başlat  4) yazılan veri bütünlüğünü denetle
  Beklenen  : güvenli kapanma, VERİ BOZULMASI YOK (S4 çekirdeği); tutarsız kayıt yok
  Ölçüm     : veri bozulması = 0 · kurtarma = %100 (TS12/TS9) · brown-out eşiği ⚠️ UNKNOWN
  Gate      : G6 (§14 eşlemesi — SOFT: `continue-on-error: true`) · firmware: G9 YOK ⚠️
  Durum     : PLANNED (dalga-3)
```

## §B Taslak Betikler (PowerShell · CI)

### §B.1 Test koşucu PowerShell iskeleti (t1)

```powershell
# Taslak: S-DEV-02 komut yankısı testi için PowerShell iskeleti — repo'da bu betik YOKTUR;
# dosya/satır künyesi verilmez, çalışan iddiası taşımaz.
# Kaynak: firmware-test-stratejisi.md §21 (geliştirici makinesi ortamı) · §25 (iskelet);
#         firmware-bilesenler.md §19.9 (t1 test koşucusu — dalga-3).

$senaryo = "S-DEV-02"
# t1: senaryo -> sonuç (arayüz taslağı testing-kapsam.md §C.1)
$sonuc = & test-runner -Scenario $senaryo -Mode simulated

if ($LASTEXITCODE -ne 0) {
    # H17 E_TST_FAIL karşılığı (taslak ad): rapor + geç/kal — sessiz yutma YASAK
    Write-Error "E_TST_FAIL: $senaryo kaldirildi (taslak rapor)"
    exit 1
}
Write-Host "$senaryo tamamlandi - rapor: ./reports/$senaryo.md (t5 taslak)"
```

### §B.2 G9 firmware HIL workflow iskeleti (YOK olan kapı)

```yaml
# Taslak: G9 firmware HIL workflow ISKELETI — depoda böyle bir workflow YOKTUR.
# Kanıt: 2026-10-14 okuma — .github/workflows/ altında yalnız ci.yml ve secret-scan.yml vardır
#        (firmware-test-stratejisi.md §3: "G9 | Firmware HIL | tanımlı workflow YOK").
# Kaynak: firmware-test-stratejisi.md §3 · §28 (G9 tanımsız — DevOps + QA ortak tasarımı);
#         firmware-bagimliliklar.md §20.5 (HIL 5 adım).
name: Firmware HIL (TASLAK - mevcut degil)
on: workflow_dispatch            # elle tetikleme - taslak
jobs:
  hil:
    runs-on: [self-hosted, hil-bench]   # tezgâh etiketi PLANNED (strateji §21 ses laboratuvarı)
    steps:
      - uses: actions/checkout@v4
      - name: Test kipi gir            # §20.5 adım 1 (bayrak: F7 · F11)
        run: ./hil/test-mode --enter
      - name: Senaryo calistir         # adım 2 (F14 test kıyaslama)
        run: ./hil/run --scenario S-PWR-01
      - name: Olcum topla               # adım 3 — esik ⚠️ UNKNOWN (uydurma esik YOK)
        run: ./hil/measure
      - name: Rapor yaz                 # adım 4 (t5)
        run: ./hil/report
      - name: Kritere karsilastir       # adım 5 (t4 eşik denetleyici)
        run: ./hil/check
```

> Bu iskelet **mevcut dosya değildir**; `hil/` betik adları, koşucu ve
> koşucu etiketleri **taslaktır** — G9 kapısı açılmadan gerçek adlara
> dönüştürülür. Workflow adı/etiketi uydurulmuş **değildir**: "yokluğu"
> kanıtlanan şeyin kendisi burada yok sayılarak yazılmıştır.

### §B.3 G6 soft kapı raporu (yüzde EŞİĞİ YOK)

```powershell
# Taslak: G6 coverage raporu okuma — YÜZDE HEDEF EŞİĞİ YAZILMAZ (hedef tanımsız ⚠️).
# Kaynak: firmware-test-stratejisi.md §17 (coverage tek başına kabul kriteri değil · Q0);
#         ci.yml js-coverage job'u — continue-on-error: true (tam dosya okundu 2026-10-14).

$rapor = Get-Content ./coverage-summary.json -Raw | ConvertFrom-Json
Write-Host "G6 (SOFT) coverage raporu uretildi; hedef esigi TANIMLI DEGIL (open question)"
# Esik kontrolu YOK: hedef yokken sayisal esik koyan betik, olculmemis hedef uydurmak olur (anayasa P1)
```

## §C Senaryo ↔ Risk / Çekirdek İzlenebilirliği

| # | Senaryo | Çekirdek (strateji §5) | Bağlı risk (§5 sütunu) | §14 eşlemesi |
|---|---|---|---|---|
| 1 | Açılış (S-BOOT-01) | S1 — enerji → ana döngü | risk 2 | R-05 · L1 · G5 |
| 2 | Komut yankısı (S-DEV-02) | S8 — hatalı host komutu → hata kodu | risk 1 | §14'te eşlenmedi ⚠️ |
| 3 | Güncelleme/geri alma (S-UPD-01) | S5 — A/B + geri alma | risk 3 🔴 | R-03 · L3 · G7 |
| 4 | Güç kesintisi (S-PWR-01) | S4 — brown-out / güvenli kapanma | risk 8 | R-04 · L5 · G6 |

> **Numaralandırma notu:** §5 "Bağlı risk" sütunu risk numarası verir; §14
> `R-nn` satırı kendi senaryo↔risk eşlemesini verir. İki tablo **yan yana
> kaydedildi, birleştirilmedi** (eşitlik uydurulmadı — çapraz doğrulama
> dalga-3 işidir). Her senaryo en az bir riski test eder (§7 kural 5).

## §D Kapanış Kontrolü (bu dosya)

| # | Kontrol | Beklenti | Durum |
|---|---|---|---|
| 1 | FM anahtar sayısı | 10 alan, doğru sıra | evet |
| 2 | Senaryo sayısı | 4 (index ELI15 ile birebir) | evet |
| 3 | Durum etiketi | dört senaryo da `PLANNED` — "geçti" yok | evet |
| 4 | `Taslak:` + `Kaynak:` | her kod bloğunda mevcut | evet |
| 5 | Dosya/satır künyesi | 0 | evet |
| 6 | Yeni zarf | 0 (yalnız §29'da mevcut zarflar) | evet |
| 7 | Uydurma eşik/yüzde | 0 (UNKNOWN / PLANNED işaretli) | evet |
| 8 | URL | 0 (araştırma yapılmadı) | evet |
| 9 | Coverage açık sorusu | çözülmeden taşındı (Q0 · §B.3) | evet |
| 10 | Son satır | Authority birebir tek satır | evet |


---

## §E Senaryo Kartı — S-SEC-01 (imza doğrulama provası · PLANNED)

### §E.1 Genel bakış

| Alan | Değer |
|---|---|
| Amaç | security dalının DV-S1 kapısını test altyapısından tetikler |
| Katman | L3 (bütünleşik — EX3 blokajlı) |
| Bağımlılık | security-dogrulama §H.1 · fixture FX1 (bozuk imza) |
| Beklenen | boot kal + M1 olayı + olay defterde |
| Durum | BLOKE — firmware kodu yok (cpp/h = 0 · 2026-10-15) |

### §E.2 Adımlar (şartname)

1. Fixture FX1 üret (geçerli imzalı image + bayt değişimi — test ortamı).
2. Boot zincirini başlat (EX3 — blokajlı).
3. M1 olayını defterden oku (dosya + satır referansı).
4. Yeniden deneme olmadığını doğrula (boot-dogrulama M.5 kuralı).
5. Sonuç satırını §B.1 şemasına yaz (kanıt yoksa INVALID).

### §E.3 Kapı ilişkisi

- İlgili kapı: security K-S1 (security-kapsam §I) — kriter sahibi security dalı.
- Testing'in rolü: yalnız **çalıştırma + kanıt**; kapı kriteri kopyalanmaz.

---

## §F Senaryo Kartı — S-RT-01 (kesintisiz akış provası · PLANNED)

### §F.1 Genel bakış

| Alan | Değer |
|---|---|
| Amaç | runtime DV-RT-11 (kesintisiz akış) senaryosunu test altyapısına bağlar |
| Katman | L4 (zamanlama ölçümü — firmware kodu blokajlı) |
| Bağımlılık | runtime-dogrulama §A.2/§Y.1 · ölçüm protokolü (rtos.md §12 [Satır: 219]) |
| Beklenen | xrun yok · bütçe içinde · sonuç satırı (ölçüm değeri uydurulmaz) |
| Durum | BLOKE — EX3 yok |

### §F.2 Adımlar (şartname)

1. Ölçüm ortamını kur (EX3 — blokajlı; ortam adı uydurulmaz).
2. Uyarıcıyı üret (sürekli akış — runtime-dogrulama §F.1 uyarıcı sınıfları).
3. Gözlem ölçütlerini oku (runtime-dogrulama §F.2).
4. Bütçe ihlali varsa FAIL, yoksa PASS + ölçüm değerleri (kaynaklı).
5. Sonucu deftere yaz; ölçüm yoksa "ÇALIŞTIRILMADI".

---

## §G Senaryo Kartı — S-DIAG-01 (olay yolu bütünlüğü · PLANNED)

### §G.1 Genel bakış

| Alan | Değer |
|---|---|
| Amaç | diagnostics olay kaydının sessiz kayıp üretmediğini denetler |
| Katman | L3 |
| Bağımlılık | diagnostics dalı (olay formatı sahibi) · testing-kapsam §G.2 |
| Beklenen | dolu tamponda sayacın artması + işaretli kayıt (sessiz kayıp YOK) |
| Durum | PLANNED — EX1 ile başlanabilir (altyapı düzeyi) |

### §G.2 Adımlar (şartname)

1. Olay kuyruğunu doldur (test verisi — payload içerikli olmamalı).
2. Sayaç okuma noktasını bul (diagnostics şeması — alan adı uydurulmaz).
3. İşaretli en eski kaydı doğrula.
4. Sonuç satırını yaz (kanıt: sayaç + işaret satırı).

---

## §H Taslak Betikler (PowerShell — depoda YOKTUR; iskelet damgalı)

```powershell
# Taslak: test kosucusu iskeleti (t1) — depoda bu betik YOKTUR, calistirilmaz.
# Kanit: 2026-10-15 envanter — firmware test betigi/CI yok (G9 tanimsiz).
# Kaynak: testing-surucu.md §1 (t1 sonuc semasi) · testing-dogrulama.md §J.1.
# Adimlar: senaryo listesi -> ortam sec -> calistir -> sonuc satirlari -> defter
```

```powershell
# Taslak: kapi raporu uretimi (t2) — kriter sahibi firmware-test-stratejisi.md §3.
# verdict degerleri: PASS / FAIL / BLOKE / INVALID / BELIRSIZ (uydurma deger yok)
```

```powershell
# Taslak: kanit defteri eki (t3) — append-only; silme YASAK (audit butunlugu).
# alanlar: ts scen env verdict evidence actor repeat
```

---

## §I Fixture Listesi (şartname — uygulama yok)

| Kod | Fixture | Kullanıldığı kart | Üretim yolu |
|---|---|---|---|
| FX1 | bozuk imza image | S-SEC-01 | mutasyon (test ortamı) |
| FX2 | eski sürüm image | security DV-S4 varyantı | sürüm sabitleme |
| FX3 | kasa engelleyici | security DV-S3 | mock (EX1) |
| FX-T1 | bozuk kriter kaynağı | DV-T1 | EX1 |
| FX-T2 | kilitli kanıt yolu | DV-T2 | EX1 |
| FX-T3 | değişken tekrar verisi | DV-T3 | test verisi |
| FX-T4 | boş/dolu rapor | DV-T4 | rapor üretici |
| FX-T5 | bozuk fixture özeti | DV-T6 | mutasyon |

1. Fixture adları **şartnamedir**; uygulama dosyaları yoktur (uydurma dosya yasak).
2. FX1/FX2 gerçek imzaları bozar — test cihazı izolatedir (DX3 kuralı).

---

## §J Kapı Raporu İskeleti (taslak — dolgu satır yok)

```text
# Kapı Raporu İskeleti (taslak — bu vault'ta üretilmis rapor YOKTUR)
# kapilar: firmware-test-stratejisi §3/§18 (G-serisi) + security K-S* (kapsam §I)
# her kapi satiri: kod | durum (PASS/FAIL/BLOKE/BELIRSIZ) | kanit (dosya+satir) | sahip
# genel kapi sonucu: blokaj varsa BELIRSIZ (gizleme yasak — testing-dogrulama §L)
```

---

## §K Senaryo ↔ Risk / Çekirdek İzlenebilirliği (§C'yi genişletir)

| Kart | Risk | Kapı | Katman |
|---|---|---|---|
| S-SEC-01 | R-secure-boot (T1) | K-S1 | L3 |
| S-RT-01 | R-timing/xrun | runtime kapıları | L4 |
| S-DIAG-01 | R-audit kaybı | diagnostics kapıları | L3 |
| S-BOOT-01 (§A.2) | R-boot | G-firmware | L3–L4 |
| S-DEV-02 (§A.3) | R-komut | G-bütünleşik | L2–L3 |
| S-UPD-01 (§A.4) | R-rollback (risk 3) | update kapıları | L3–L5 |
| S-PWR-01 (§A.5) | R-güç kesintisi | G-HIL (G9) | L5 (BLOKE) |

### §K.1 İz kuralı (bu dosyaya özgü)

1. Kart, kapı **kriterini kopyalamaz** — yalnız çağırır (SSOT).
2. Kart, çalıştırma durumunu dürüst yazar: PLANNED/BLOKE/PASS (kanıtsız PASS yasak).
3. Yeni kart → §K tablosuna satır + §H/§E-F-G kalıbıyla ek (append-only).

---

## §L Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Mevcut §A–§D silindi mi? | HAYIR (append-only) |
| Frontmatter | korundu (10 alan + group: kod) |
| Betik "çalışıyor" iddiası | 0 (tümü taslak damgalı) |
| Uydurma dosya/ekipman/ad | 0 |
| Dolgu/TBD | 0 |
| Son satır Authority | birebir korunur |

### §L.1 Sonraki üretim koşulu

- EX3/G9 blokajları çözülürse kartlara **gerçek sonuç satırları** append edilir.
- Yeni kart eklemek §E–§G kalıbını izler; kalıp dışına çıkış ADR ister.


---

## §M Yeni Senaryo Kartı Şablonu (boş kalıp — dolduran çalıştırır)

### §M.1 Kart kalıbı (kopyala-doldur)

| Alan | Değer |
|---|---|
| Kart adı | (ör. S-XXX-nn) |
| Amaç | (tek cümle — ne doğrulanır) |
| Katman | (L1–L6 · testing-kapsam §H.1) |
| Ortam | (EX1–EX4 · testing-dogrulama §I) |
| Bağımlılık | (dosya + bölüm) |
| Uyarıcı | (ne verilir) |
| Beklenen | (ölçülebilir beklenti — sayı uydurma yok) |
| Kanıt | (dosya + satır) |
| Durum | TASLAK → HAZIR → ÇALIŞTI → KAPANDI / BLOKE / INVALID |

### §M.2 Kalıp kuralları

1. "Beklenen" alanı **ölçülebilir** olmalı ("iyi çalışsın" yasak).
2. "Kanıt" alanı olmadan kart KAPANDI olamaz (testing-kapsam §G.2 t3).
3. Kart yalnız **şartname** üretir; uygulama kodu başka sahiptir (bileşen yoksa BLOKE).

---

## §N Sık Sorulanlar (bu dosya — §M kalıbını destekler)

| Soru | Yanıt |
|---|---|
| Betikler çalışır mı? | Hayır — hepsi taslak damgalı; depoda firmware test altyapısı yok |
| Kart eklerken neyi kopyalıyorum? | Yalnız kapı **kodunu**; kriter metnini değil (SSOT) |
| Kapı raporu elle yazılır mı? | Hayır — t2 üretir; iskelet §J yalnız şablon |
| S-UPD-01 risk 3 ne demek? | Update geri alma provası — en yüksek öncelikli risk kartı (§A.4) |
| Flaky kart ne yapar? | t4 işaretler; kapıda ayrı işaretle PASS olur (DV-T3) |
| Yeni fixture nereye eklenir? | §I tablosuna satır (append) + kart referansı |
| Sonuç satırını kim yazar? | Çalıştırıcı (agent/kişi) — agent "geçti" bilemez, kanıt üretir |
| Ölçüm değeri nereden gelir? | Çalıştırma ortamından; tahmin/uydurma yasak |

---

## §O Örnek Kullanım Kılavuzu (kim ne okur)

1. **Geliştirici**: kart yazmadan önce §M.1 kalıbı + testing-kapsam §H.1 katman tanımı.
2. **QA**: §H (mevcut kartlar) + §I (fixture) + §K (risk izi) — plan kurarken.
3. **Vault Steward**: §J iskeleti + §L kapanış sayımı — kapı raporu incelemesinde.
4. **DevOps**: §F (S-RT-01 EX3 bağımlılığı) + §K (G9 blokajı).
5. **İnceleyen**: her kartta "taslak/blokaj damgası" var mı — yoksa uydurma kabul edilir.

### §O.1 Kart yaşam döngüsü hatırlatması

- TASLAK → HAZIR inceleme ile · HAZIR → ÇALIŞTI ortam hazır olunca ·
  ÇALIŞTI → KAPANDI kanıt yazılınca · BLOKE/INVALID ayrımı testing-kapsam §N.2.

---

## §P Red Team Notları (bu ek için)

1. **İtiraz:** "Kartı PASS işaretleyip kapıyı kapatalım." **Karar:** hayır — PASS
   yalnız çalıştırma + kanıt ile (§M.2); erken kapanış anayasa ihlali.
2. **İtiraz:** "BLOKE kartları rapordan çıkaralım." **Karar:** hayır — gizleme yasak
   (testing-dogrulama §N.5).
3. **İtiraz:** "Fixture'ı kod içine gömülelim." **Karar:** hayır — fixture envanteri
   §I'da görünür olmalı (sessiz fixture = mystery guest).
4. **İtiraz:** "Betikler yine de çalışır, damga formalite." **Karar:** damga zorunlu —
   depoda altyapı yok; damgasız blok uydurmadır (kanıt: cpp/h = 0 · 2026-10-15).

---

## §Q Terimler ve Kısaltmalar (bu dosya sözlüğü)

| Terim | Anlamı |
|---|---|
| Kart | senaryo şartnamesi (§M.1 kalıbı) |
| EX1–EX4 | ortam kademeleri (testing-dogrulama §I) |
| FX* | fixture kodları (§I · §H) |
| DV-T* | testing doğrulama senaryosu serisi |
| t1–t5, F14 | testing bileşenleri (testing-kapsam §G) |
| BLOKE/INVALID | FAIL değildir — ayrım testing-kapsam §N.2 |
| G9 | firmware HIL workflow — TANIMLI DEĞİL |
| taslak damga | betik/kartın çalıştırılmadığını gösteren zorunlu işaret |

### §Q.1 Kapanış cümlesi

Bu ek (§E–§Q) testing-ornekler.md dosyasını 3 ek kart, 3 taslak betik iskeleti,
fixture envanteri, kapı raporu iskeleti, kart şablonu, FAQ, kullanım kılavuzu ve
sözlükle tamamlamıştır; mevcut §A–§D append-only korunmuştur. Çalıştırılmış
iddiası: 0 · uydurma dosya/ad/URL: 0.


---

## §R Kart → Kaynak Çapraz Referansı (bu ek)

| Kart/Blok | Kaynak dosya | Bölüm |
|---|---|---|
| S-SEC-01 (§E) | security-dogrulama.md | §H.1 (DV-S1) |
| S-RT-01 (§F) | runtime-dogrulama.md | §A.2 · §Y.1 |
| S-RT-01 ölçüm çapası | .ai/electronic/firmware/rtos.md | Satır 219 (§12.1) |
| S-DIAG-01 (§G) | testing-kapsam.md | §G.2 |
| Betik iskeletleri (§H) | testing-surucu.md | §1 |
| Kapı iskeleti (§J) | firmware-test-stratejisi.md | §3 · §18 |
| Fixture listesi (§I) | testing-dogrulama.md | §Q.2 |
| Kalıp (§M) | testing-kapsam.md | §N |

### §R.1 Kapanış sayımı (bu ek)

1. Eklenen: §E–§R (3 kart · 3 betik iskeleti · fixture · şablon · FAQ · sözlük).
2. Silinen: 0 (append-only) · Frontmatter korundu · Son satır Authority birebir.
3. Uydurma/çalıştırılmış iddiası: 0 — tüm kartlar PLANNED/BLOKE damgalıdır.


---

## §S Son Sayım (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §E–§S |
| Kart/betik iskeleti | 3 kart · 3 iskelet · hepsi taslak damgalı |
| Silinen mevcut içerik | 0 |
| Dolgu/TBD | 0 |
| Son satır Authority | birebir korunur |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode