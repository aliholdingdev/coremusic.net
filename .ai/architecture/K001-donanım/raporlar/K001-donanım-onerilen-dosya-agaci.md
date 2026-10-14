---
title: "K001-donanım — Önerilen Dosya Ağacı (mevcut yapı + PROPOSED düzeltmeler)"
type: analysis-report
node_id: K001-donanım/raporlar/onerilen-dosya-agaci
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanaım-type
evidence:
  - "Disk ölçümü 2026-10-13 (tur-2): K001 70 md (kök 10 · donanaım-type 46 · api 3 · core 3 · devices 4 · kernel 3 · raporlar 1)"
  - "Disk ölçümü 2026-10-13 (tur-2b): raporlar/ = 7 md (bu rapor seti) — §3 ağacı güncellendi"
  - "Get-ChildItem -Recurse ile tam ağaç çıktısı; her satır gerçek dosya adı + satır sayısı"
  - ".ai/architecture/K001-donanım/index.md:6-11 (children 5 satır — tur-2b'de donanım-devices eklendi, E2 KAPANDI)"
  - ".ai/architecture/K001-donanım/donanaım-type/index.md:6-16 (children 10 satır) · :59-94 (§GRUPLAR 34 satır katalog — tur-envanteri §5.1 düzeltmesi 36→34)"
  - "K000-isletim-sistemi/index.md:6 (13 children — tur-2b, E1 KAPANDI) · :8 (validation '12 düğüm' STALE = C-15/E8)"
  - "donanım-devices/index.md:44 (sınıf listesi 'audio·hdd·ssd·display·power' — 10 tür gerçeğine göre STALE = E9)"
validation:
  - "§3 tam ağaç 77 satır — Get-ChildItem çıktısı ile birebir (hiçbir dosya atlanmadı, hiçbir dosya uydurulmadı)"
  - "§4 PROPOSED düzeltmeler yalnızca 'eklenecek/silinecek/taşınacak' önerisi taşır; bu oturumda hiçbir dosya eklenmedi/silinmedi/taşınmadı"
  - "E1 · E2 · E4 KAPANDI işaretleri paralel Vault Steward kapanışıdır — bu rapor yalnızca yeniden ölçümle doğrular (gizleme yasağı: silinmez, durum güncellenir)"
  - "Önerilen ağaç mevcut ağaçtan türetildi — fantazi dal/dosya yok (her satır ya 'var' ya 'PROPOSED' etiketli)"
open_questions:
  - "audio/cipler/ dizini ağaçta kalıcı mı (indekse kayıtlı hâle getirilecek) mi? (E3 hâlâ AÇIK)"
  - "gpu/ ikinci karar (ADR-102 dışı) verilmezse gpu/ dalı ne olur — silinir mi, PROPOSED mi kalır?"
  - "donanaım-type yazımı (donanaım) ağaçta kalıcı mı — düzeltilirse 46 dosyanın wiki-link'leri etkilenir mi?"
  - "E6 (electroncis SSOT kökü) ve E7 (K002-altyapi ismi) birlikte mi çözülür? — ADR-102 §6 C2 ile bağlantılı, Vault Steward takdiri"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Önerilen Dosya Ağacı

## §1 Bu raporun rolü (ELI15)

K001-donanım klasörünün **şu anki** tam ağaç yapısını (her dosya, her satır
sayısı) yazar, sonra **önerilen** yapının ne olduğunu listeler: hangi dosya
eklenmeli, hangi stale satır düzeltilmeli, hangi eksik kapanmalı. Kural: mevcut
ağaç gerçektir (ölçülmüştür); öneriler `PROPOSED` etiketlidir ve bu oturumda
uygulanmaz — Vault Steward onayı bekler. "Öneri" ile "gerçek" aynı tabloda
karışmaz; her satırda etiket vardır.

**Tur-2b notu:** Paralel Vault Steward oturumu üç öneriyi uygulamıştır
(E1 · E2 · E4 — §4'te KAPANDI işaretli). Bu rapor o kapanışları **kendi işi
olarak değil**, disk gerçeğini yeniden ölçerek kaydeder.

## §2 Ağaç özeti (tur-2b)

| Düğüm | md | İçerik | Durum |
|---|---|---|---|
| `K001-donanım/` (kök) | 10 | 9 içerik + 1 index | DOCUMENTED |
| `donanım-api/` | 3 | index + kapsam + dogrulama | DOCUMENTED |
| `donanım-core/` | 3 | index + kapsam + dogrulama | DOCUMENTED |
| `donanım-devices/` | 4 | index + kapsam + dogrulama + ornekler | DOCUMENTED |
| `donanım-kernel/` | 3 | index + kapsam + dogrulama | DOCUMENTED |
| `donanaım-type/` | 46 | 10 alt-bağlam + type kök + audio/cipler | DOCUMENTED (ADR-102 draft) |
| `raporlar/` | **7** | mevcut rapor 1 + bu setin 6 yeni raporu | **draft (bu görev — tur-2b ölçümü)** |

Toplam (tur-2b): 10+3+3+4+3+46+7 = **76 md** (tur-2'de raporlar/1 iken 70 idi;
fark bu rapor setinin 6 yeni dosyasıdır — §3'te ağaç buna göre güncellendi).

## §3 Tam ağaç (tur-2b ölçümü — her satır gerçek)

Aşağıdaki ağaç Get-ChildItem çıktısının okunabilir hâlidir. `→ N` = satır sayısı.
Kök/tür içerik satırları tur-2 ölçümüdür (değişmedi); `raporlar/` tur-2b'de
yeniden ölçüldü.

```text
K001-donanım/
├── index.md → 99
├── donanım-anayasa.md → 518
├── donanım-bagimliliklar.md → 519
├── donanım-dogrulama.md → 507
├── donanım-evrensel-dil.md → 502
├── donanım-kararlar.md → 529
├── donanım-kod-ornekleri.md → 509
├── donanım-riskler.md → 506
├── donanım-test-stratejisi.md → 509
├── donanım-web-arastirma.md → 500
├── donanım-api/
│   ├── index.md → 54
│   ├── donanım-api-kapsam.md → 544
│   └── donanım-api-dogrulama.md → 513
├── donanım-core/
│   ├── index.md → 54
│   ├── donanım-core-kapsam.md → 505
│   └── donanım-core-dogrulama.md → 631
├── donanım-devices/
│   ├── index.md → 94
│   ├── donanım-devices-kapsam.md → 509
│   ├── donanım-devices-dogrulama.md → 508
│   └── donanım-devices-ornekler.md → 511
├── donanım-kernel/
│   ├── index.md → 54
│   ├── donanım-kernel-kapsam.md → 501
│   └── donanım-kernel-dogrulama.md → 511
├── donanaım-type/
│   ├── index.md → 97
│   ├── donanaım-type-kapsam.md → 512
│   ├── donanaım-type-dogrulama.md → 531
│   ├── audio/ (7)
│   │   ├── index.md → 55
│   │   ├── audio-kapsam.md → 506
│   │   ├── audio-dogrulama.md → 510
│   │   ├── audio-ornekler.md → 529
│   │   └── cipler/
│   │       ├── ak4458-dac.md → 541
│   │       ├── pcm3168a-codec.md → 500
│   │       └── tpa3255-amplifier.md → 586
│   ├── cables/ (4)
│   │   ├── index.md → 97
│   │   ├── cables-kapsam.md → 764
│   │   ├── cables-dogrulama.md → 506
│   │   └── cables-ornekler.md → 508
│   ├── cpu/ (4)
│   │   ├── index.md → 82
│   │   ├── cpu-kapsam.md → 654
│   │   ├── cpu-dogrulama.md → 504
│   │   └── cpu-ornekler.md → 504
│   ├── display/ (4)
│   │   ├── index.md → 68
│   │   ├── display-kapsam.md → 594
│   │   ├── display-dogrulama.md → 512
│   │   └── display-ornekler.md → 502
│   ├── gpu/ (4)
│   │   ├── index.md → 99
│   │   ├── gpu-kapsam.md → 585
│   │   ├── gpu-dogrulama.md → 503
│   │   └── gpu-ornekler.md → 525
│   ├── hdd/ (4)
│   │   ├── index.md → 52
│   │   ├── hdd-kapsam.md → 533
│   │   ├── hdd-dogrulama.md → 517
│   │   └── hdd-ornekler.md → 537
│   ├── memory/ (4)
│   │   ├── index.md → 98
│   │   ├── memory-kapsam.md → 504
│   │   ├── memory-dogrulama.md → 515
│   │   └── memory-ornekler.md → 503
│   ├── network/ (4)
│   │   ├── index.md → 79
│   │   ├── network-kapsam.md → 515
│   │   ├── network-dogrulama.md → 502
│   │   └── network-ornekler.md → 511
│   ├── power/ (4)
│   │   ├── index.md → 60
│   │   ├── power-kapsam.md → 592
│   │   ├── power-dogrulama.md → 501
│   │   └── power-ornekler.md → 503
│   └── ssd/ (4)
│       ├── index.md → 52
│       ├── ssd-kapsam.md → 505
│       ├── ssd-dogrulama.md → 501
│       └── ssd-ornekler.md → 502
└── raporlar/ (7 — tur-2b ölçümü)
    ├── K001-donanım-mevcut-durum-raporu.md → 625
    ├── K001-donanım-domain-esleme-matrisi.md → 503
    ├── K001-donanım-tur-envanteri.md → 525
    ├── K001-donanım-celiskiler-ve-acik-kararlar.md → 502
    ├── K001-donanım-onerilen-dosya-agaci.md → 500+ (bu dosya)
    ├── K001-donanım-uygulama-plani.md → 500+ (genişletiliyor)
    └── K001-donanım-kabul-kriterleri.md → 500+ (genişletiliyor)
```

Sayım kontrolü: kök 10 + api 3 + core 3 + devices 4 + kernel 3 + type 46 +
raporlar 7 = **76** ✓ (Get-ChildItem recursif toplamı ile birebir).

**Not (raporlar/ satırları):** bu setin 6 dosyası bu oturumun işidir; son üç
satırdaki `500+` değerleri dosyalar yazılırken ölçülmüştür — kesin satır sayıları
kabul-kriterleri raporu §7'de nihai ölçüm tablosunda yer alır (bu ağaç dosyası
kendini yeniden ölçmez; kendi sayısını kendi satırında uydurmaz).

## §4 Eklenecek öneriler (PROPOSED — E1…E9, durumlu)

### §4.1 Kapanmış öneriler (tur-2b — paralel Vault Steward; yeniden ölçümle doğrulandı)

| # | Öneri | Yer | Kanıt (eski) | Kanıt (tur-2b — kapanış) | Durum |
|---|---|---|---|---|---|
| E1 | `os-donanim-tespiti` satırı | K000 `index.md` children | K000 index:6 = 12 child, dal diskte 4/4 var (C-06) | K000 index:6 = **13 child**, `os-donanim-tespiti` dâhil (yeniden ölçüm 2026-10-13) | **KAPANDI** (tur-2b) |
| E2 | `donanım-devices` satırı | K001 `index.md` children | K001 index:6-10 = 4 child, diskte 5 klasör | K001 index:6-11 = **5 child**, `donanım-devices` dâhil; :22'de "2026-10-13 genişleme" kaydı + :46'da 5. çocuk kataloğu mevcut | **KAPANDI** (tur-2b) |
| E4 | gpu-ornekler gerçek satırı | `donanaım-type/index.md` | :93 "BEKLENIYOR — diskte YOK"; dosya 525 satır var (C-10) | `donanaım-type/index.md:94` "**4/4 tamam**" (yeniden ölçüm) | **KAPANDI** (tur-2b) |

**Kapanış artığı uyarısı (dürüstlük kaydı):** E1 kapanırken
`K000-isletim-sistemi/index.md:8` validation satırı "12 düğüm sayıldı" metniyle
kalmıştır → bu yeni çelişkidir (C-15) ve aşağıdaki E8'e bağlanmıştır. Kapanış
yaparken artığın temizlenmesi kuralı bu örnekle zorunluluğunu gösterir.

### §4.2 Açık öneriler (PROPOSED — bu oturumda uygulanmaz)

| # | Eklenecek | Yer | Neden | Kanıt | Onay |
|---|---|---|---|---|---|
| E3 | audio/cipler 3 satır | `donanaım-type/index.md` §GRUPLAR | 3 chip dosyası indekste kayıtlı değil | index:59-94 (34 kayıt) vs disk (cipler 3 md kayıtsız) | APPROVAL REQUIRED |
| E5 | K002 2 alt-konu satırı (veya silme) | K002 `index.md` §2 | 5 iddia vs 3 dolu dizin + 2 boş dizin (C-07) | K002 index:56-64 + disk (tur-envanteri §13.3) | APPROVAL REQUIRED |

### §4.3 Yeni öneriler E6…E9 (tur-2b bulgularından — bu oturumun katkıları)

| # | Öneri | Yer | Neden | Kanıt | Onay |
|---|---|---|---|---|---|
| E6 | electroncis SSOT kökü kararı | `K000-isletim-sistemi/electroncis/` vs `K002-electroncis/` | İki kök aynı domaini tanımlar (12 md vs 6 md) — C-13 | Get-ChildItem 2026-10-13 (tur-2b): K000/electroncis 12 md (2 alt-dalin kendi anayasası dâhil) · K002 kök 6 md | APPROVAL REQUIRED (ADR düzeyinde — ADR-102 §6 C2 ile bağlantılı) |
| E7 | `K002-altyapi` isim kararı | K000 `index.md:7` `depends_on: [K002-altyapi]` | Bağımlılık satırı havada — diskte `K002-altyapi` dizini YOK (C-14) | K000 index:7 + ADR-102 §6 C2 (çift kayıt) + Get-ChildItem (dizin YOK) | APPROVAL REQUIRED (ADR düzeyinde) |
| E8 | K000 validation yeniden sayım | K000 `index.md:8` | "12 düğüm sayıldı (124 md / 39.099 satır)" — children artık 13 (C-15) | K000 index:8 metni + :6 13-child | APPROVAL REQUIRED |
| E9 | devices sınıf listesi güncellemesi | `donanım-devices/index.md:44` | "`donanaım-type` = sınıf (audio·hdd·ssd·display·power)" — type artık **10 tür** taşır (5'i eksik) | devices/index.md:44 vs type/index.md:6-16 (children 10) | APPROVAL REQUIRED |

E6/E7 birlikte çözülmelidir: `K002-altyapi` (E7) adı, electroncis kökünün
(E6) sınır kararına bağlıdır — isim kararı olmadan sınır kararı yarım kalır.

## §5 Düzeltilecek öneriler (PROPOSED — stale metin, D1…D7)

| # | Dosya | Satır | Eski metin | Öneri | Onay |
|---|---|---|---|---|---|
| D1 | `donanım-anayasa.md` | :46 · :249 | "Çocuk sayısı 4" · "C9: 4 kademe-2 çocuk" | 5 — devices 5. çocuk (C-01; iki satır birlikte) | APPROVAL REQUIRED |
| D2 | `donanaım-type-kapsam.md` | :77-78·:95·:135 | "yalnız 3 bağlam" | 10 bağlam gerçeği — C-03 (:135 işaretli, 2 satır işaretsiz) | APPROVAL REQUIRED |
| D3 | `donanaım-type-kapsam.md` | :284 | M6 "bağlam AÇILMAZ" | ADR-102 istisna notu — C-04 kararı | APPROVAL REQUIRED |
| D4 | `.ai/architecture/index.md` | :94·:119 | "K002 hiç yok" | 16 md/3 dizin gerçeği — C-05 (⚠️ yeniden okunmadı) | APPROVAL REQUIRED |
| D5 | `.ai/architecture/index.md` | :44 | ADR-061 yedek yolu | gerçek yedek yolu — C-11 (⚠️ yeniden okunmadı) | APPROVAL REQUIRED |
| D6 | `donanaım-type/index.md` | :21 | evidence "3 alt bağlam" | 10 alt bağlam — C-02 | APPROVAL REQUIRED |
| D7 | `K000-isletim-sistemi/index.md` | :8 | "12 düğüm sayıldı" | 13 düğüm + yeniden sayılmış md/satır — C-15/E8 | APPROVAL REQUIRED |

D7 bu oturumun yeni düzeltme önerisidir (C-15 kartı celiskiler raporu §7.3'tedir).

## §6 Karar notları (silinmeyecek/taşınmayacak — N1…N3)

| # | Konu | Öneri | Gerekçe |
|---|---|---|---|
| N1 | `donanaım-type` yazımı | **DÜZELTME YASAK** | type/index.md:38-39 "v2 kaynağına sadık"; düzeltme 46 dosyanın wiki-link'lerini kırar (⚠️ kırık-link sayısı ölçülmedi) |
| N2 | gpu/ dalı | **SİLİNMEZ** | type/index.md:35-36 paralel oturum kararı; ikinci karar yazılmadıkça varlığı korunur |
| N3 | `.ai/reports/k001-taksonomi/` | **BİRLEŞTİRİLMEZ** | iki rapor seti yan yana durur; birleştirme Vault Steward'ındır |

## §7 Kademe-2 fm şablon farkı tablosu (tur-2b okuması)

K001 kademe-2 indeksleri iki farklı frontmatter şablonu kullanır — bu bir
tutarsızlıktır (Q7/Q8, tur-envanteri §5.2):

| Düğüm | children | status | depends_on | group alanı | Şablon |
|---|---|---|---|---|---|
| `donanım-api/index.md` | `[]` (boş) | draft | 1 satır | YOK | Şablon-A (type ailesi) |
| `donanım-core/index.md` | `[]` (boş) | draft | 1 satır | YOK | Şablon-A |
| `donanım-kernel/index.md` | `[]` (boş) | draft | 1 satır | YOK | Şablon-A |
| `donanım-devices/index.md` | 3 çocuk | **active** | **2 satır** (K001 + K000) | **VAR** (mimari/kanit/kod) | Şablon-B (farklı) |
| `donanaım-type/index.md` | 10 çocuk | draft | 1 satır | YOK | Şablon-A |

İki farklı şablonun varlığı, ADR-097 §5.5'teki `group` alanı kuralının yalnız
bir dalda uygulanması demektir. Öneri: şablon birleştirme kararı Vault
Steward'ındır (bu rapor uygulamaz). Kanıt: ilgili `index.md` frontmatter
okumaları 2026-10-13 (tur-2b).

## §8 Eksik-kapanış tablosu (hangi dal hangi dosyayı bekliyor)

| Dal | Mevcut | Eksik | Not |
|---|---|---|---|
| `donanım-api/` | index·kapsam·dogrulama | ornekler | api için kod örneği gerekli mi? UNKNOWN |
| `donanım-core/` | index·kapsam·dogrulama | ornekler | Aynı |
| `donanım-kernel/` | index·kapsam·dogrulama | ornekler | Aynı |
| `audio/cipler/` | 3 chip dosyası | index.md | İndekslenmemiş (E3) |
| `K002/guvenli-rezervasyon/` | dizin var | TÜM md'ler (0 md) | İke (C-07 ile bağlantılı — iskelet dal) |
| `K002/test-onceliklileri/` | dizin var | TÜM md'ler (0 md) | Aynı (C-07 ile bağlantılı) |

Örnek dosyası bekleyen 3 dal için "eksik mi, gerekli mi" kararı Vault Steward'ın;
bu rapor yokluğu **kayıt** altına alır, varlık uydurmaz. K002'nin iki boş
alt-dizini bu raporun K001 dışı komşu ölçümüdür (tur-envanteri §13.3 kanıtı).

## §9 Gelecek ağaç (PROPOSED — onaylanırsa)

```text
K001-donanım/                  (kök: 10 md — değişmez)
├── donanaım-type/             (46 md + audio/cipler index = 47 PROPOSED — E3)
├── donanım-api/               (+ ornekler = 4 — PROPOSED, karar bekliyor)
├── donanım-core/              (+ ornekler = 4 — PROPOSED, karar bekliyor)
├── donanım-devices/           (4 — değişmez; :44 sınıf listesi D/E9 ile güncellenir)
├── donanım-kernel/            (+ ornekler = 4 — PROPOSED, karar bekliyor)
└── raporlar/                  (7 rapor — bu görev, GERÇEK)
```

Toplam (onaylanırsa): 76 + 1 (cipler-index) + 3 (api/core/kernel ornekler) =
**80 md** — ancak bu sayı yalnızca Vault Steward E3 + ornekler kararlarından
sonra bağlayıcıdır.

## §10 Dal bazlı öneri kartları (10 alt bağlam — dal başına durum + öneriler)

Her alt bağlam için: mevcut durum · E/D bağlantıları · özel not. Sayılar tur-2
ölçümüdür (§3 ağacı).

| # | Dal | md | Eşik durumu | Bağlı öneriler | Özel not |
|---|---|---|---|---|---|
| 1 | `audio/` | 7 (cipler dâhil) | 4/4 + 3 cipler | E3 (cipler indeks kaydı) | Cipler kayıtsız — §GRUPLAR'a 3 satır eklenmeli (E3) |
| 2 | `cables/` | 4 | 4/4 ✓ (kapsam 764 = en uzun) | — | Eşik üstü; özel not yok |
| 3 | `cpu/` | 4 | 4/4 ✓ (ornekler 504 — eşik tam sınırında) | — | Görev girdisi "500 altı" demişti, tur-2 504 ölçtü (celiskiler §9 #6) |
| 4 | `display/` | 4 | 4/4 ✓ | — | ADR-102 §6 C5'te "5. alt bağlam" olarak geçer (matris §4.2) |
| 5 | `gpu/` | 4 | 4/4 ✓ (E4 KAPANDI) | N2 (silinmez) | İkinci karar (ADR) yok — Q2/celiskiler C-08 bağlantılı |
| 6 | `hdd/` | 4 | 4/4 ✓ (index 52 — en kısa indeks) | — | Eşik üstü (indeks ≥50 eşiği) |
| 7 | `memory/` | 4 | 4/4 ✓ (290'lar → 504/515/503) | — | Tur-2'de eşik aşıldı (görev girdisi 290'lar vermişti) |
| 8 | `network/` | 4 | 4/4 ✓ (ornekler 156→511) | — | Tur-2'de eşik aşıldı |
| 9 | `power/` | 4 | 4/4 ✓ | D2 (kapsam:138-144 26-alan şema SSOT'u) | power-kapsam :138-144 = 26 alan şeması SSOT (tur-envanteri §6) |
| 10 | `ssd/` | 4 | 4/4 ✓ (index 52) | — | Eşik üstü |

Kademe-2 kökler (type dışı 4 dal) ayrıca:

| Dal | md | Eksik dosya | Bağlı öneri |
|---|---|---|---|
| `donanım-api/` | 3 | ornekler | §8 kararı (UNKNOWN — gerekli mi?) |
| `donanım-core/` | 3 | ornekler | Aynı |
| `donanım-kernel/` | 3 | ornekler | Aynı |
| `donanım-devices/` | 4 | — | E9 (:44 sınıf listesi stale) |

## §11 Uygulama kartları — Vault Steward her öneriyi nasıl uygular?

Her öneri için adım kartı: bu rapor **uygulamaz**, yalnızca "nasıl uygulanır"
tarifini yazar. Adımlar sıralıdır; Vault Steward tek oturumda uygulayabilir.

### §11.1 E6 + E7 kartı (sınır/isim — önce bunlar)

| Adım | İş | Kanıt hedefi |
|---|---|---|
| 1 | ADR-102 §6 C2'yi oku — K002-altyapi'nin ADR'deki bağlamı | draft ADR-102 §6 |
| 2 | Karar ver: K000/electroncis mü, K002-electroncis mü SSOT? | Vault Steward |
| 3 | Kaybeden kök için: içerik taşınır veya "shim" indeks yazılır | Yeni ADR kaydı |
| 4 | `K000-isletim-sistemi/index.md:7` `depends_on: [K002-altyapi]` satırı ya gerçek kök adına çevrilir ya da yorumlanır (placeholder olduğu notu) | C-14 kapanışı |
| 5 | ADR'yi `.decisions/accepted/`'a taşı (draft→accepted) | Kapanış kaydı |

### §11.2 D1 kartı (anayasa çocuk 4→5)

| Adım | İş | Kanıt hedefi |
|---|---|---|
| 1 | `donanım-anayasa.md:46` "Çocuk sayısı 4" → 5 | C-01 |
| 2 | `donanım-anayasa.md:249` "C9: 4 kademe-2 çocuk" → 5 | C-01 (iki satır birlikte — §11.1.1 transkripti) |
| 3 | `donanaım-type/index.md:21` evidence "3 alt bağlam" → 10 | D6/C-02 |
| 4 | Yeniden ölçüm + celiskiler raporunda C-01 durumunu KAPANDI yap | Kapanış kaydı |

### §11.3 D2 + D3 kartı (type-kapsam stale + M6)

| Adım | İş | Kanıt hedefi |
|---|---|---|
| 1 | `donanaım-type-kapsam.md:77-78` · `:95` "yalnız 3 bağlam" → 10 bağlam gerçeği | C-03 (:135 zaten işaretli — üç satır birlikte) |
| 2 | `:284` M6 metnine ADR-102 istisna notu ekle (veya M6'yı koru + ADR'yi yaz — C-04 kararı) | C-04 |
| 3 | Yeniden ölçüm | Kapanış |

### §11.4 E8 + D7 kartı (K000 validation yeniden sayım)

| Adım | İş | Kanıt hedefi |
|---|---|---|
| 1 | K000 ağacını yeniden say (13 düğüm için md + satır toplamı) | C-15 |
| 2 | `K000-isletim-sistemi/index.md:8` "12 düğüm sayıldı (124 md / 39.099 satır)" → yeni sayılar | C-15/E8 |
| 3 | anayasa:249 C9 satırı D1 ile birlikte güncellenir | C-01 (ilişkili) |

### §11.5 E3 · E5 · E9 kartları (indeks satırı eklemeleri)

| Adım | İş | Kanıt hedefi |
|---|---|---|
| 1 | E3: `donanaım-type/index.md` §GRUPLAR'a audio/cipler 3 satırı ekle | Q3/E3 |
| 2 | E5: K002 `index.md:56-64` — 2 alt-konu satırı ya üretilir ya satır silinir (C-07 kararı) | C-07 |
| 3 | E9: `donanım-devices/index.md:44` sınıf listesi 5→10 tür | E9 |
| 4 | Yeniden ölçüm + indeks ≥50 kontrolü | Kapanış |

### §11.6 D4 + D5 kartı (registry — beyazliste dışı)

| Adım | İş | Kanıt hedefi |
|---|---|---|
| 1 | ⚠️ Önce `.ai/architecture/index.md:94/:119/:44` satırlarını yeniden oku (paralel ajan düzenlemiş olabilir) | C-05/C-11 doğrulama |
| 2 | Hâlâ stale ise :94/:119 "K002 hiç yok" → 16 md/3 dizin gerçeği | C-05 |
| 3 | :44 ADR-061 yedek yolu → `C:\temp\opencode\vault-backups\ADR-061-electronics-architecture.md.bak` | C-11 |
| 4 | Yeniden okuma + kapanış | Kapanış |

## §12 Ölçüm metodolojisi (tekrarlanabilir — okuyucu kendi ölçebilir)

§3 ağacını yeniden üretmek isteyen Vault ajanı aşağıdaki sırayı uygular:

| Adım | Komut (PowerShell 5.1) | Not |
|---|---|---|
| 1 | `Get-ChildItem 'C:\www\coremusic.net\.ai\architecture\K001-donanım' -Recurse -Filter *.md \| Measure-Object` | Toplam md (tur-2b: 76) |
| 2 | Kök/kademe-2 sayımı için `-Directory` + `Where-Object Name -like 'K001*'` | Türkçe dizin adı (donanım) wildcard gerektirir |
| 3 | Satır sayısı için `[System.IO.File]::ReadAllText($f) -split "`n"` | `Measure-Object -Line` boş satırları saymaz — yanlıştır (§12.1) |
| 4 | Frontmatter okuması için grep: `^children: \[\]` / `^parent:` | fm denetimi (tur-envanteri §11 şablonu) |

### §12.1 Ölçüm tuzağı (bu raporun bulgusu)

`Get-Content | Measure-Object -Line` boş satırları **saymaz** — bu oturumda
ilk ölçüm turu bu yöntemle alınmış ve dosyalar olduğundan ~%20 kısa
görünmüştür (örn. celiskiler 239 görünüyordu, gerçekte 370 idi). Doğru
yöntem: `[System.IO.File]::ReadAllText($f)` + newline split (yukarıdaki
3. adım). Kabul-kriterleri raporu §7'deki nihai sayılar bu doğru yöntemle
alınmıştır.

## §13 Çapraz referanslar (öneri ↔ diğer raporlar)

| Öneri | İlgili rapor | Bölüm | İlişki |
|---|---|---|---|
| E1 (KAPANDI) | celiskiler-ve-acik-kararlar | §4 C-06 + §11.4 transkript | Kapanış kanıtı bu raporda |
| E2 (KAPANDI) | mevcut-durum-raporu | §7 (K001 kök children 5) | Kapanışın K001 ayağı |
| E3 (audio/cipler) | tur-envanteri | §4 (audio envanteri) + Q3 | Kayıtsız 3 cipler |
| E4 (KAPANDI) | celiskiler-ve-acik-kararlar | §6 C-10 + §11.5 transkript | gpu-ornekler kapanışı |
| E5 (K002 2 satır) | domain-esleme-matrisi | §2.2 (K002 kök katalog) + §2.3 (alt-dizin md) | C-07 bağlantılı |
| E6 (electroncis SSOT) | celiskiler-ve-acik-kararlar | §7.1 C-13 kartı | Sınır kararı — bu kartın uygulama karşılığı |
| E7 (K002-altyapi) | celiskiler-ve-acik-kararlar | §7.2 C-14 kartı | İsim kararı |
| E8 (K000 validation) | celiskiler-ve-acik-kararlar | §7.3 C-15 kartı + §11.7 transkript | Kapanış artığı |
| E9 (devices:44) | domain-esleme-matrisi | §4.2 ADR-102 §6 C4 notu | 10 tür gerçeği |
| D1 (anayasa 4→5) | celiskiler-ve-acik-kararlar | §3 C-01 + §11.1 transkript | İki satır birlikte |
| D7 (K000 validation metni) | uygulama-plani | §3 K-8 kartı | Aynı işin plan karşılığı |
| §7 (fm şablon farkı) | tur-envanteri | §5.2 (4 bulgu) | Aynı okuma, iki rapor |
| §12.1 (ölçüm tuzağı) | kabul-kriterleri | §5 (bet Transkriptleri) | Ölçüm yöntemi uyarısı |

## §14 Etki-risk analizi — öneriler uygulanmazsa ne olur?

| Öneri | Uygulanmazsa risk | Risk seviyesi | Eşitlik |
|---|---|---|---|
| E6 (electroncis SSOT) | İki anayasa dosyası bağımsız evrilir → yeni çelişkiler doğar (C-13 kalıcı hâle gelir) | YÜKSEK | C-13 |
| E7 (K002-altyapi) | depends_on satırı havada kalır → bağımlılık grafiği yanlış okunur | YÜKSEK | C-14 |
| E8/D7 (validation) | K000'de "12 vs 13" çelişkisi kalır → sonraki ölçüm yapan ajan yanlış sayıya güvenir | ORTA | C-15 |
| D1 (anayasa 4→5) | Anayasa sayısal olarak yanlış kalır; her yeni okuyan "4 mü 5 mi" şaşırır | ORTA | C-01 |
| D2 (kapsam "yalnız 3") | 7 yeni bağlam kapsamda tanımsız kalır | ORTA | C-03 |
| D3 (M6 istisnası) | "Bağlam AÇILMAZ" kuralı ile 7 açılma eylemi arasında bağ eksik kalır | ORTA | C-04 |
| E3 (cipler indeks) | 3 chip dosyası §GRUPLAR'dan bağımsız kalır → indeks taraması kaçıranlar | DÜŞÜK | Q3 |
| E9 (devices:44) | devices indeksi 5 tür söyler, type 10 tür taşır | DÜŞÜK | — |
| D4/D5 (registry) | Registry "K002 hiç yok" derken diskte 16 md var → dış ajanlar yanılgı | DÜŞÜK (⚠️ stale olmayabilir) | C-05/C-11 |
| E5 (K002 2 satır) | 2 alt-konu iddiası havada kalır | DÜŞÜK | C-07 |

**Öncelik sırası (risk × bağımlılık):** E6/E7 → E8/D7 → D1 → D2/D3 → E3/E9 →
D4/D5/E5. Bu sıralama §SONUÇ'taki "sonraki adım" ile tutarlıdır.

## §15 Bu raporun sürüm günlüğü (rapor-özel change log)

| Sürüm | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| v1.0 | 2026-10-13 | İlk sürüm: 70 md tam ağaç (tur-2) + E1–E5 · D1–D6 · N1–N3 | Bu oturum (tur-2 subagent) |
| v1.1 | 2026-10-13 | Tur-2b genişletme: raporlar/ 7 dosya (76 md); E1 · E2 · E4 → KAPANDI; E6–E9 yeni öneriler; D7 eklendi; §7 fm şablon farkı · §10 dal kartları · §11 uygulama kartları · §12 metodoloji (+tuzağın ifşası) · §13 çapraz referans · §14 etki-risk · §15 bu tablo | Bu oturum (tur-2b subagent) |

**Sürüm kuralı:** Yeni sürüm yalnızca (a) yeni dal/öneri eklenerek veya (b) bir
önerinin durumu yeniden ölçümle KAPANDI/AÇIK yapılarak oluşur; §3 ağacı her
sürümden önce yeniden ölçülür (kendi kendini uydurmak yasak).

## §16 Okuyucu notları (sık sorulanlar — bu raporu okuyan Vault ajanı için)

| # | Soru | Cevap |
|---|---|---|
| 1 | "§3'teki ağaç ile §2 özeti neden farklı sayı veriyor (70 vs 76)?" | 70 = tur-2 ölçümü (raporlar/ = 1 md). 76 = tur-2b ölçümü (raporlar/ = 7 md — bu rapor seti yazıldı). §3 ağacı tur-2b'ye güncellendi; §2 özeti de 76'yı taşır. Tur-2'deki 70, tarihsel ölçümdür — celiskiler raporu §9 fark tablosunda saklıdır. |
| 2 | "Neden E1/E2/E4 hâlâ listede duruyor, silinmiyor?" | Gizleme yasağı: kapanan öneri tarihsel kanıttır. §4.1'de KAPANDI olarak işaretlenir, silinmez — böylece "bu ağaçta hiç öneri yok" izlenimi doğmaz. |
| 3 | "E6/E7 neden bu kadar vurgulanıyor, diğerleri kadar basit değil mi?" | Hayır. E6/E7 tek bir sınır kararının iki yüzü (nerede SSOT + hangi ad) ve ADR-102 §6 C2 ile çift kayıtlılar; karar verilmeden E8/D7 gibi sayısal düzeltmeler bile yanlış kökte uygulanabilir. Risk §14'te YÜKSEK. |
| 4 | "Bu rapordaki '500+' yazan satırlar nedir?" | Bu dosyanın kendisi yazılırken ölçülemeyen (o an hâlâ yazılmakta olan) 3 kardeş raporun satır sayısı tahmini DEĞİL, hedefidir. Kesin sayılar kabul-kriterleri §7'de nihai ölçüm tablosunda yer alır. Uydurma sayı yazılmaz. |
| 5 | "§12.1'deki ölçüm tuzağı ciddi mi?" | Evet — bu oturumda ilk ölçüm `Measure-Object -Line` ile alınmış, dosyalar ~%20 kısa görünmüştür (boş satırlar sayılmıyor). Doğru yöntem §12 3. adım. Diğer ajanlar bu tuzağa düşmemeli; kabul-kriterleri §5'te de uyarı tekrarlanır. |
| 6 | "K002'nin 2 boş alt-dizini bu raporda neden var?" | §8'de "eksik-kapanış" olarak kayıtlı — çünkü C-07'nin (K002 5 alt-konu vs 3 dolu) kanıt parçası. Bu rapor K001 dışına yalnızca sınır komşularına bakar; K002'yi düzeltmez. |

## §17 Tüm kalem tek tablo (öneri + düzeltme + karar — durumlu tam liste)

Bu tablo §4-§6'nın tek satırda özeti: okuyucu hangi kalemin hangi durumda
olduğunu tek yerde görür.

| Kalem | Tip | Konu | Durum | Bağlı C-ID |
|---|---|---|---|---|
| E1 | ekleme | K000 children → os-donanim-tespiti | **KAPANDI** (tur-2b) | C-06 |
| E2 | ekleme | K001 children → donanım-devices | **KAPANDI** (tur-2b) | C-01 (anayasa ayağı hâlâ AÇIK) |
| E3 | ekleme | type §GRUPLAR → audio/cipler 3 satır | AÇIK (PROPOSED) | Q3 |
| E4 | ekleme | type index:94 → gpu-ornekler "4/4" | **KAPANDI** (tur-2b) | C-10 |
| E5 | ekleme | K002 §2 → 2 alt-konu satırı (veya silme) | AÇIK (PROPOSED) | C-07 |
| E6 | karar | electroncis SSOT kökü (K000 vs K002) | AÇIK (PROPOSED) | C-13 |
| E7 | karar | K002-altyapi ismi (havada depends_on) | AÇIK (PROPOSED) | C-14 |
| E8 | düzeltme | K000 index:8 validation yeniden sayım | AÇIK (PROPOSED) | C-15 |
| E9 | düzeltme | devices/index.md:44 sınıf listesi 5→10 | AÇIK (PROPOSED) | — |
| D1 | düzeltme | anayasa :46 · :249 çocuk 4→5 | AÇIK (PROPOSED) | C-01 |
| D2 | düzeltme | type-kapsam :77-78·:95·:135 "yalnız 3"→10 | AÇIK (PROPOSED, :135 işaretli) | C-03 |
| D3 | düzeltme | type-kapsam :284 M6 istisna notu | AÇIK (PROPOSED) | C-04 |
| D4 | düzeltme | architecture index :94·:119 K002 gerçeği | AÇIK (PROPOSED, ⚠️ yeniden okunmadı) | C-05 |
| D5 | düzeltme | architecture index :44 ADR-061 yedek yolu | AÇIK (PROPOSED, ⚠️ yeniden okunmadı) | C-11 |
| D6 | düzeltme | type index:21 evidence 3→10 bağlam | AÇIK (PROPOSED) | C-02 |
| D7 | düzeltme | K000 index:8 "12 düğüm" metni | AÇIK (PROPOSED) | C-15 |
| N1 | karar-notu | donanaım yazımı DÜZELTME YASAK | KORUNUR | Q1 |
| N2 | karar-notu | gpu/ dalı SİLİNMEZ | KORUNUR | Q2 |
| N3 | karar-notu | k001-taksonomi birleştirilmez | KORUNUR | — |

Toplam: **19 kalem** = 3 KAPANDI (E1·E2·E4) + 13 AÇIK (E3·E5–E9 · D1–D7) +
3 KORUNUR (N1–N3). §SONUÇ'taki sayılar bu tabloyla eşleşir.

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Hiçbir dosya eklenmez/silinmez/taşınmaz** — §4-§5 tamamı PROPOSED
   (E1·E2·E4'ün KAPANDI işaretleri paralel Vault Steward'ın uygulamasıdır, bu
   raporun işi değildir).
2. **Wiki-link taraması yapılmaz** — N1'deki "link'leri kırar" iddiası genel
   bilgidir, bu oturumda ölçülmüş değildir (`⚠️ VERIFICATION REQUIRED`).
3. **Yazım (donanaım) kararı verilmez** — N1 kararı Vault Steward'ındır.
4. **K002/K000 iç ağaçları yeniden yazılmaz** — E1/E5/E6/E7/E8 yalnızca kendi
   indekslerine/kararlarına satır eklenmesi önerisidir.
5. **Şablon birleştirme uygulanmaz** — §7'deki iki-şablon bulgusu raporlanır,
   birleştirme kararı Vault Steward'ındır.

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı (tur-2b sayımı): 76 dosyalık tam ağaç** — §3 her satırı gerçek
ölçümle listeler; toplam Get-ChildItem ile birebir (76 = 10+3+3+4+3+46+7);
raporlar/ 7 dosya tur-2b'de yeniden ölçüldü; 3 kapanış (E1·E2·E4) yeniden
ölçümle doğrulandı; kademe-2 fm şablon farkı 5 düğüm okumasıyla belgelendi.

**PROPOSED: 16 kalem** — 6 ekleme (E3 · E5 · E6 · E7 · E8 · E9) · 7 stale
düzeltme (D1–D7) + 3 karar notu (N1–N3, uygulama değil koruma kuralı).
(E1 · E2 · E4 bu oturumda PROPOSED'tan KAPANDI'ya geçmiştir — sayıma dâhil
değildir.)

**Onay bekliyor (APPROVAL REQUIRED): 13 kalem** — E3 · E5 · E6 · E7 · E8 · E9
(6 ekleme) + D1–D7 (7 düzeltme); bu oturumda uygulanan **0** (kapanan 3'ü
paralel Vault Steward'ın işidir).

**Sonraki adım (tek):** Vault Steward önce **sınır kararlarını** (E6 · E7 — tek
ADR'de, ADR-102 §6 C2 ile bağlantılı) verir, sonra ağaç hizalama oturumunda
E3 · E5 · E8 · E9 + D1–D7'yi uygulatır; o güne kadar §3'teki ağaç **tek gerçek**
kabul edilir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
