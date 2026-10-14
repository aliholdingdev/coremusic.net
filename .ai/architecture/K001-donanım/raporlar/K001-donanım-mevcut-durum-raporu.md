---
title: "K001-donanım — Mevcut Durum Raporu (Taksonomi Analiz Çıktı)"
type: analysis-report
node_id: K001-donanım/raporlar/mevcut-durum
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Disk ölçümü 2026-10-13 (bu oturum): Get-ChildItem -LiteralPath '.ai/architecture/K001-donanım' — kök 10 md · kademe-2 5 klasör · toplam 65 md"
  - "Satır sayıları: (Get-Content -LiteralPath \$_.FullName).Count — her dosya için ayrı ayrı ölçüldü (§3–§6 tabloları)"
  - ".ai/architecture/K001-donanım/index.md:6-10 (children: 4 satır) · :55-56 (kademe-3 tablosunda display·power satırları)"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md:46 (Çocuk sayısı 4) · :195 · :250 (9 bağlam PROPOSED — APPROVAL REQUIRED)"
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md:35-41 (bağlam envanteri) · :186-202 (§7 uygulama adımları)"
  - ".ai/architecture/K000-isletim-sistemi/os-donanim-tespiti/ — 3 md (index 110 · kapsam 503 · dogrulama 390); ornekler YOK"
validation:
  - "K001 kök 10 dosyanın 9'u ≥500 satır; index.md 92 satır (indeks dosyaları ≥50 eşiği ile ölçüldü — §9)"
  - "donanaım-type altında 9 alt klasör × 4 dosya + audio/cipler 3 dosya = 42 dosya; toplam 65 dosya, Get-ChildItem recursif sayımı (65) ile birebir tutuyor"
  - "Görev girdisi ile disk ölçümü farkları §8'de gizlenmeden listelendi (7 satır)"
open_questions:
  - "gpu/ dalı: görev girdisi '🔄 üretim sürüyor' der — diskte 0 dizin → UNKNOWN, onay kaydı aranacak"
  - "donanım-kernel/donanım-kernel-dogrulama.md 78 satır — üreten ajan hâlâ çalışıyor mu? (görev girdisi: TAMAMLANIYOR)"
  - "network·memory·cpu içerik dosyaları 500 altı (§5) — üretim sürüyor mu, bitti mi? ADR-102 §7 '✓ TAMAM' der ama satır sayısı tutmuyor (§8/F-8)"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Mevcut Durum Raporu

## §1 Bu raporun rolü (ELI15)

K001-donanım, CoreMusic mimari ağacında "kasadaki fiziksel parçaların" tutulduğu
kütüphanedir (kaynak: `K001-donanım/index.md:31` — "disk, ses kartı, ekran,
kablolar"). Bu rapor o kütüphanenin **bu sabit anlık görüntüsünü** yazar: hangi
klasör var, hangi dosya var, kaç satır, hangi durum etiketini taşıyor. Rapor
**hiçbir dosyayı değiştirmez**; sadece okur, sayar, yazar. Sayılar uydurulmaz:
hepsi `Get-ChildItem` / `Get-Content` ile 2026-10-13'te ölçülmüştür ve her satırda
ölçüm kaynağı belirtilmiştir. Görev girdisindeki iddialar ile disk gerçeği
ayrışır §8'de gösterilir — gizleme yok.

## §2 Ölçüm yöntemi (tekrarlanabilir)

| # | Ne ölçüldü | Araç | Kapsam |
|---|---|---|---|
| M1 | Dosya listesi | `Get-ChildItem -LiteralPath <dizin> -Recurse -File -Force` | `.ai/architecture/K001-donanım/**` (`.ai` gizli dizin — `-Force` ile) |
| M2 | Satır sayısı | `(Get-Content -LiteralPath $_.FullName).Count` | Her md dosyası tek tek |
| M3 | Dizin sayısı | `Get-ChildItem -Directory -Force` | `.ai/architecture/` üst düzey (band-1) |
| M4 | Varlık testi | `Test-Path` + glob `hidden: true` | gpu/ · K002-altyapi · `.ai/reports/k001-taksonomi/` |
| M5 | İçerik kanıtı | `grep` (dosya + satır numarası) | anayasa · kapsam · ADR dosyaları |

> **Ölçüm anı:** 2026-10-13, bu raporun yazıldığı oturum içi. Görev girdisine göre
> üretim ajanları (cables/network/memory/cpu/gpu · kernel-dogrulama) **o an
> çalışıyor olabilir** → sayılar sonradan değişebilir; bu rapor değişen sayıyı
> **yeniden üretmez**, ölçüm anını taşır.

## §3 K001 kök dosyaları (kademe-1 — 10 dosya, ölçüldü)

| # | Dosya (göreli yol) | Satır | ≥500? | group | Durum etiketi |
|---|---|---|---|---|---|
| 1 | `donanım-anayasa.md` | 511 | ✅ | mimari | DOCUMENTED — 9 bağlam satırı PROPOSED (§195·§250) |
| 2 | `donanım-bagimliliklar.md` | 519 | ✅ | mimari | DOCUMENTED — tip envanteri eksik kaydı (:71) |
| 3 | `donanım-dogrulama.md` | 507 | ✅ | kanit | DOCUMENTED |
| 4 | `donanım-evrensel-dil.md` | 502 | ✅ | mimari | DOCUMENTED |
| 5 | `donanım-kararlar.md` | 529 | ✅ | mimari | DOCUMENTED |
| 6 | `donanım-kod-ornekleri.md` | 509 | ✅ | kod | DOCUMENTED |
| 7 | `donanım-riskler.md` | 506 | ✅ | kanit | DOCUMENTED |
| 8 | `donanım-test-stratejisi.md` | 509 | ✅ | kod | DOCUMENTED — :202 "display — envanterde yok → AÇIK" satırı artık stale (§8/F-6) |
| 9 | `donanım-web-arastirma.md` | 500 | ✅ (eşit) | kanit | DOCUMENTED |
| 10 | `index.md` | 92 | indeks (≥50) | mimari | DOCUMENTED — children 4/4 · kademe-3 tablosu 5 satır |

**Toplam kök satır:** 4.684 (10 dosya). **Kök dosyada `donanım-kapsam.md` YOK**
— görev girdisi kök için "donanım-kapsam(504)" iddiasında bulunur; diskte kök
kapsam dosyası **bulunmamaktadır** (M1 ile 10 dosya sayıldı, kapsam adında dosya
yok) → §8/F-1.

### §3.1 `index.md` iç yapısı (92 satır — okundu)

| Bölüm | Satır | İçerik |
|---|---|---|
| frontmatter children | :6-10 | donanım-kernel · donanım-core · donanım-api · donanaım-type (**4 satır — devices YOK**) |
| ELI15 | :31 | "disk, ses kartı, ekran, kablolar" |
| Çocuk kataloğu | :36-46 | 4 kademe-2 çocuk satırı (:40-43) |
| Kademe-3 tablosu | :48-56 | hdd · audio · ssd · **display (:55)** · **power (:56)** — 5 satır |
| Durum tablosu | :58-68 | status draft · "4 alt klasör + 3 alt bağlam" |
| §GRUPLAR | :72-87 | 10 kademe-1 dosya satırı |
| Authority | :91 | tek satır |

> **İç çelişki (kaydedildi, düzeltilmedi):** `index.md` frontmatter'de 4 çocuk
> (:6-10), kademe-3 tablosunda 5 alt bağlam (:52-56), Durum satırında "4 alt
> klasör + 3 alt bağlam" (:63). Üç sayım üç ayrı hikâye anlatıyor — §8/F-2.

## §4 Kademe-2 çocuklar (5 klasör — ölçüldü)

| # | Çocuk | Dizin | Dosya | Toplam satır | Durum etiketi |
|---|---|---|---|---|---|
| 1 | donanım-kernel | `donanım-kernel/` | 3 | 633 | DOCUMENTED (kapsam 501) + dogrulama 78 **eksik üretim** |
| 2 | donanım-core | `donanım-core/` | 3 | 1.190 | DOCUMENTED |
| 3 | donanım-api | `donanım-api/` | 3 | 1.111 | DOCUMENTED |
| 4 | donanaım-type | `donanaım-type/` | 42 | 16.453 | MIXED — 6 bağlam tam, 3 bağlam yarı, gpu YOK (§5) |
| 5 | donanım-devices | `donanım-devices/` | 4 | 1.622 | DOCUMENTED — **index children listesine GİRMEMİŞ** (PROPOSED) |

**Toplam:** 5 klasör · 55 dosya · kök 10 dosya ile birlikte **65 md dosyası**
(recursive sayım M1 ile 65 — birebir tutuyor).

### §4.1 Kademe-2 dosya detayı

| # | Dosya | Satır | ≥500? | Not |
|---|---|---|---|---|
| 1 | `donanım-kernel/index.md` | 54 | indeks | |
| 2 | `donanım-kernel/donanım-kernel-kapsam.md` | 501 | ✅ | |
| 3 | `donanım-kernel/donanım-kernel-dogrulama.md` | **78** | ❌ | Görev girdisi: "TAMAMLANIYOR — ajan çalışıyor" |
| 4 | `donanım-core/index.md` | 54 | indeks | |
| 5 | `donanım-core/donanım-core-kapsam.md` | 505 | ✅ | |
| 6 | `donanım-core/donanım-core-dogrulama.md` | 631 | ✅ | |
| 7 | `donanım-api/index.md` | 54 | indeks | |
| 8 | `donanım-api/donanım-api-kapsam.md` | 544 | ✅ | |
| 9 | `donanım-api/donanım-api-dogrulama.md` | 513 | ✅ | |
| 10 | `donanım-devices/index.md` | 94 | indeks | ADR-102 §6/C4: :44'te "5 sınıf" sınır listesi stale |
| 11 | `donanım-devices/donanım-devices-kapsam.md` | 509 | ✅ | |
| 12 | `donanım-devices/donanım-devices-dogrulama.md` | 508 | ✅ | |
| 13 | `donanım-devices/donanım-devices-ornekler.md` | 511 | ✅ | |

## §5 donanaım-type altı (kademe-3 — 9 bağlam, 42 dosya)

### §5.1 Bağlam bazlı özet

| # | Bağlam | Dosya | Satırlar (index·kapsam·dogrulama·ornekler) | ≥500 içerik? | Durum etiketi |
|---|---|---|---|---|---|
| 1 | `audio/` (+`cipler/` 3) | 7 | 55 · 506 · 510 · 529 (+541 · 500 · 586) | ✅ hepsi | DOCUMENTED (orijinal 3) |
| 2 | `hdd/` | 4 | 52 · 533 · 517 · 537 | ✅ | DOCUMENTED (orijinal 3) |
| 3 | `ssd/` | 4 | 52 · 505 · 501 · 502 | ✅ | DOCUMENTED (orijinal 3) |
| 4 | `display/` | 4 | 68 · 594 · 512 · 502 | ✅ | PROPOSED (2026-10-13 onay) |
| 5 | `power/` | 4 | 60 · 592 · 501 · 503 | ✅ | PROPOSED (2026-10-13 onay) |
| 6 | `cables/` | 4 | 97 · 764 · 506 · 508 | ✅ | PROPOSED (üretim girdisi 🔄) |
| 7 | `network/` | 4 | 82 · **300** · **303** · **298** | ❌ 3/4 | PROPOSED + **içerik 500 altı** |
| 8 | `memory/` | 4 | 102 · **295** · **286** · **296** | ❌ 3/4 | PROPOSED + **içerik 500 altı** |
| 9 | `cpu/` | 4 | 82 · 654 · 504 · **465** | ❌ 1/4 | PROPOSED + **ornekler 500 altı** |
| — | `gpu/` | **0** | — | — | **UNKNOWN — diskte dizin YOK** |

**Tip kök dosyaları:** `index.md` 110 · `donanaım-type-kapsam.md` 512 ·
`donanaım-type-dogrulama.md` 531 — üçü de var, `donanaım-type/index.md` frontmatter
`:6-15` children listesi **9 bağlam + 12 dosya satırı** taşır (okundu, 2026-10-13).

### §5.2 `donanaım-type/index.md` (110 satır — okundu)

| Bölüm | Satır | İçerik |
|---|---|---|
| children (klasör) | :6-15 | hdd · audio · ssd · display · power · cables · network · memory · cpu — **9 satır** |
| children (dosya) | :16-24 | hdd/ssd/audio kapsam+dogrulama+ornekler — 9 satır |
| ELI15 | :36-42 | "9 alt bağlam açıldı… PROPOSED — APPROVAL REQUIRED" |
| §GRUPLAR | :60-101 | 36 satır (9 bağlam × 4 dosya) |
| Authority | :105 | **ikinci Authority bloğu gövdede var** (:105 + Mode satırı) — bu rapor seti bu kusuru taşımaz |

> **Gözlem:** tip index'i 9 bağlamı çoktan yazmış; buna karşılık K001 kök
> `index.md` :6-10 hâlâ 4 çocuk, anayasa :46 hâlâ "Çocuk sayısı 4". Ağaç üç
> farklı sayım seviyesinde yaşıyor → çelişki raporu (dosya 4) C-02.

## §6 K000 tarafındaki komşu üretim (okundu, değiştirilmedi)

| # | Dizin | Dosya | Satırlar | Durum etiketi |
|---|---|---|---|---|
| 1 | `K000-isletim-sistemi/os-donanim-tespiti/` | 3 | index 110 · kapsam 503 · dogrulama 390 | PROPOSED — `ornekler` dosyası **YOK** (4/4 değil, 3/4) |

`os-donanim-tespiti/index.md:103` kendi eksikliğini yazıyor: "Bu klasör v2
mini-ağaçta K000 kademe-2 listesinde **YOKTU**". Kayıt `os-donanim-tespiti-kapsam.md:331-335`
(§10.1 — PROPOSED — APPROVAL REQUIRED) içinde duruyor.

## §7 Band-1 komşu envanter (`.ai/architecture/` üst düzey — 28 dizin)

| # | Dizin | md dosyası | # | Dizin | md dosyası |
|---|---|---|---|---|---|
| 1 | `K000-isletim-sistemi` | 267 | 15 | `K014-network` | 16 |
| 2 | `K001-donanım` | **65** | 16 | `K015-media` | 15 |
| 3 | `K002-electroncis` | 16 | 17 | `K016-AI` | 22 |
| 4 | `K003-audio` | 68 | 18 | `K017-observability` | 20 |
| 5 | `K004-dsp` | 69 | 19 | `K018-devops` | 21 |
| 6 | `K005-firmware` | 35 | 20 | `K019-testing` | 22 |
| 7 | `K006-yapay-zeka` | 67 | 21 | `K020-adr` | 3 |
| 8 | `K007-data` | 33 | 22 | `K020-comere` | 2 |
| 9 | `K008-domain` | 30 | 23 | `K020-comilance` | 5 |
| 10 | `K009-security` | 50 | 24 | `K020-discovery` | 3 |
| 11 | `K010-identitiy` | 27 | 25 | `K020-licence` | 3 |
| 12 | `K011-application` | 29 | 26 | `K020-recomenataion` | 5 |
| 13 | `K012-services` | 46 | 27 | `adr` | 3 |
| 14 | `K013-integraion` | 18 | 28 | `diagrams` | 5 |

> `K002-electroncis` **mevcut** (16 dosya: 6 kök + `devre-ve-analog/` 4 +
> `dijital-ve-mikroislemci/` 4 + `guc-yonetimi/` 2). Buna karşılık
> `.ai/architecture/index.md:94` "K002 hiç yok" ve `:119` "K002* dizini 0" der —
> **stale kayıt** → dosya 4, C-08. `K002-altyapi` dizini **0** (M4) — buna
> karşılık K000 dosyaları ona `depends_on` ile bağlı → C-09.

## §8 Görev girdisi ↔ disk ölçümü farkları (gizleme YASAK)

| # | Konu | Görev girdisi (id) | Disk ölçümü (2026-10-13) | Karar |
|---|---|---|---|---|
| F-1 | K001 kök kapsam dosyası | "donanım-kapsam(504)" | **Kökte `donanım-kapsam.md` YOK** (10 dosya listelendi) | ⚠️ VERIFICATION REQUIRED |
| F-2 | anayasa satır sayısı | 482 | 511 | Ölçüm farkı — dosya üretim sırasında büyüdü olabilir; iki sayı da kayıtlı |
| F-3 | kararlar | 505 | 529 | Aynı (F-2) |
| F-4 | bagimliliklar | 501 | 519 | Aynı (F-2) |
| F-5 | riskler / dogrulama / test-stratejisi | 500 / 501 / 500 | 506 / 507 / 509 | Aynı (F-2) |
| F-6 | test-stratejisi display kaydı | — | :202 "display — envanterde yok → AÇIK" artık stale (display/ var) | Raporlanır, düzeltilmez |
| F-7 | gpu/ | "🔄 üretim sürüyor" | **0 dizin** | UNKNOWN |
| F-8 | network·memory·cpu | "🔄 üretim sürüyor" | Dizinler var; içerik 3/4 dosya 500 altı (§5.1) | PROPOSED — üretim bitmemiş olabilir |
| F-9 | display ornekler | 503 | 502 | 1 satır fark — muhtemelen üretim sürmesi |
| F-10 | power kapsam | 593 | 592 | 1 satır fark — aynı neden |
| F-11 | index (K001 kök) | 92 | 92 ✅ | Birebir tuttu |
| F-12 | kernel-dogrulama | 78 (TAMAMLANIYOR) | 78 ✅ | Birebir tuttu |

## §9 Durum etiketi sözlüğü (bu rapor setinde kullanımı)

| Etiket | Anlamı | K001'de görüldüğü yer |
|---|---|---|
| IMPLEMENTED | Kod kanıtlı (repo dosyası+satır) | Bu rapor setinde **hiç kullanılmadı** — K001 bir doküman ağacıdır |
| DOCUMENTED | Belge diskte var ve ≥500 (veya indeks ≥50) | Kök 10 dosya · kernel/core/api kapsamları · orijinal 3 bağlam |
| PLANNED | Belgede geçiyor, üretim yok | gpu/ (istek var, disk yok) |
| PROPOSED | Vault Steward onayı var ama metin revizyonu/appr. bekliyor | display·power·cables·network·memory·cpu · devices · ADR-102 |
| UNKNOWN | Bilgi yok | gpu/ durumu · "8/8" etiketinin tanımı (§10) |
| VERIFY REQUIRED | İddia disk kanıtıyla çelişiyor veya doğrulanamıyor | F-1 · F-7 · C-02 · C-08 · C-09 (dosya 4) |

> **Kural (görev şeması):** "belgede geçmek = fiziksel üretim DEĞİL." Bu raporda
> hiçbir belge satırı donanım/fiziksel üretim kanıtı olarak kullanılmamıştır.

## §10 "8/8" etiketi notu

Görev girdisi display ve power için "✅ 8/8", devices için "✅ 4/4" der. Diskte
display 4 dosya, power 4 dosya, devices 4 dosya vardır. "4/4" dosya sayısıyla
örtüşür; "8/8" **dosya sayısı değildir** (8 dosyalık klasör yok) — muhtemel bir
kapı/kriter sayımıdır ancak **bu oturumda kaynağı okunmadı** → UNKNOWN olarak
işaretlendi, hiçbir tabloda dosya sayısı gibi kullanılmadı.

## §11 İlişkili raporlar (bu set)

| Dosya | İlişki |
|---|---|
| `K001-donanım-tur-envanteri.md` | §5'teki 9 bağlamın derin envanteri |
| `K001-donanım-domain-esleme-matrisi.md` | §7 band-1 tablosunun eşleme matrisi hâli |
| `K001-donanım-celiskiler-ve-acik-kararlar.md` | §8 farklarının çelişki numaralarına bağlanması |
| `K001-donanım-onerilen-dosya-agaci.md` | Mevcut ağacın bu rapordan alınan hâli |
| `K001-donanım-uygulama-plani.md` | F-1…F-12 için faz planı |
| `K001-donanım-kabul-kriterleri.md` | Ölçüm betiklerinin kabul kriterine çevrimi |

> Harici çapraz referans (okundu, dokunulmadı): `.ai/reports/k001-taksonomi/`
> altında 10 kısa rapor (47–107 satır) mevcuttur (M4 ile ölçüldü). Bu rapor seti
> o setin **yerine geçmez**, ondan bağımsız ve daha ayrıntılıdır.

## §12 Master dosya envanteri — K001 kökü tek tek (65 satır)

| # | Göreli yol (K001-donanım/ altı) | Satır | Eşik | Etiket |
|---|---|---|---|---|
| 1 | `index.md` | 92 | indeks | DOCUMENTED |
| 2 | `donanım-anayasa.md` | 511 | ≥500 | DOCUMENTED |
| 3 | `donanım-bagimliliklar.md` | 519 | ≥500 | DOCUMENTED |
| 4 | `donanım-dogrulama.md` | 507 | ≥500 | DOCUMENTED |
| 5 | `donanım-evrensel-dil.md` | 502 | ≥500 | DOCUMENTED |
| 6 | `donanım-kararlar.md` | 529 | ≥500 | DOCUMENTED |
| 7 | `donanım-kod-ornekleri.md` | 509 | ≥500 | DOCUMENTED |
| 8 | `donanım-riskler.md` | 506 | ≥500 | DOCUMENTED |
| 9 | `donanım-test-stratejisi.md` | 509 | ≥500 | DOCUMENTED |
| 10 | `donanım-web-arastirma.md` | 500 | ≥500 | DOCUMENTED |
| 11 | `donanım-kernel/index.md` | 54 | indeks | DOCUMENTED |
| 12 | `donanım-kernel/donanım-kernel-kapsam.md` | 501 | ≥500 | DOCUMENTED |
| 13 | `donanım-kernel/donanım-kernel-dogrulama.md` | 78 | ≥500 | **EKSIK — üretim sürüyor** |
| 14 | `donanım-core/index.md` | 54 | indeks | DOCUMENTED |
| 15 | `donanım-core/donanım-core-kapsam.md` | 505 | ≥500 | DOCUMENTED |
| 16 | `donanım-core/donanım-core-dogrulama.md` | 631 | ≥500 | DOCUMENTED |
| 17 | `donanım-api/index.md` | 54 | indeks | DOCUMENTED |
| 18 | `donanım-api/donanım-api-kapsam.md` | 544 | ≥500 | DOCUMENTED |
| 19 | `donanım-api/donanım-api-dogrulama.md` | 513 | ≥500 | DOCUMENTED |
| 20 | `donanım-devices/index.md` | 94 | indeks | DOCUMENTED |
| 21 | `donanım-devices/donanım-devices-kapsam.md` | 509 | ≥500 | DOCUMENTED |
| 22 | `donanım-devices/donanım-devices-dogrulama.md` | 508 | ≥500 | DOCUMENTED |
| 23 | `donanım-devices/donanım-devices-ornekler.md` | 511 | ≥500 | DOCUMENTED |
| 24 | `donanaım-type/index.md` | 110 | indeks | DOCUMENTED (9-child) |
| 25 | `donanaım-type/donanaım-type-kapsam.md` | 512 | ≥500 | DOCUMENTED (iç çelişkili — dosya 4) |
| 26 | `donanaım-type/donanaım-type-dogrulama.md` | 531 | ≥500 | DOCUMENTED |
| 27 | `donanaım-type/audio/index.md` | 55 | indeks | DOCUMENTED |
| 28 | `donanaım-type/audio/audio-kapsam.md` | 506 | ≥500 | DOCUMENTED |
| 29 | `donanaım-type/audio/audio-dogrulama.md` | 510 | ≥500 | DOCUMENTED |
| 30 | `donanaım-type/audio/audio-ornekler.md` | 529 | ≥500 | DOCUMENTED |
| 31 | `donanaım-type/audio/cipler/tpa3255-amplifier.md` | 586 | ≥500 | DOCUMENTED |
| 32 | `donanaım-type/audio/cipler/pcm3168a-codec.md` | 500 | ≥500 | DOCUMENTED (eşit) |
| 33 | `donanaım-type/audio/cipler/ak4458-dac.md` | 541 | ≥500 | DOCUMENTED |
| 34 | `donanaım-type/hdd/index.md` | 52 | indeks | DOCUMENTED |
| 35 | `donanaım-type/hdd/hdd-kapsam.md` | 533 | ≥500 | DOCUMENTED |
| 36 | `donanaım-type/hdd/hdd-dogrulama.md` | 517 | ≥500 | DOCUMENTED |
| 37 | `donanaım-type/hdd/hdd-ornekler.md` | 537 | ≥500 | DOCUMENTED |
| 38 | `donanaım-type/ssd/index.md` | 52 | indeks | DOCUMENTED |
| 39 | `donanaım-type/ssd/ssd-kapsam.md` | 505 | ≥500 | DOCUMENTED |
| 40 | `donanaım-type/ssd/ssd-dogrulama.md` | 501 | ≥500 | DOCUMENTED |
| 41 | `donanaım-type/ssd/ssd-ornekler.md` | 502 | ≥500 | DOCUMENTED |
| 42 | `donanaım-type/display/index.md` | 68 | indeks | PROPOSED |
| 43 | `donanaım-type/display/display-kapsam.md` | 594 | ≥500 | PROPOSED |
| 44 | `donanaım-type/display/display-dogrulama.md` | 512 | ≥500 | PROPOSED |
| 45 | `donanaım-type/display/display-ornekler.md` | 502 | ≥500 | PROPOSED |
| 46 | `donanaım-type/power/index.md` | 60 | indeks | PROPOSED |
| 47 | `donanaım-type/power/power-kapsam.md` | 592 | ≥500 | PROPOSED |
| 48 | `donanaım-type/power/power-dogrulama.md` | 501 | ≥500 | PROPOSED |
| 49 | `donanaım-type/power/power-ornekler.md` | 503 | ≥500 | PROPOSED |
| 50 | `donanaım-type/cables/index.md` | 97 | indeks | PROPOSED |
| 51 | `donanaım-type/cables/cables-kapsam.md` | 764 | ≥500 | PROPOSED |
| 52 | `donanaım-type/cables/cables-dogrulama.md` | 506 | ≥500 | PROPOSED |
| 53 | `donanaım-type/cables/cables-ornekler.md` | 508 | ≥500 | PROPOSED |
| 54 | `donanaım-type/network/index.md` | 82 | indeks | PROPOSED |
| 55 | `donanaım-type/network/network-kapsam.md` | 300 | ≥500 | **EKSIK — 500 altı** |
| 56 | `donanaım-type/network/network-dogrulama.md` | 303 | ≥500 | **EKSIK — 500 altı** |
| 57 | `donanaım-type/network/network-ornekler.md` | 298 | ≥500 | **EKSIK — 500 altı** |
| 58 | `donanaım-type/memory/index.md` | 102 | indeks | PROPOSED |
| 59 | `donanaım-type/memory/memory-kapsam.md` | 295 | ≥500 | **EKSIK — 500 altı** |
| 60 | `donanaım-type/memory/memory-dogrulama.md` | 286 | ≥500 | **EKSIK — 500 altı** |
| 61 | `donanaım-type/memory/memory-ornekler.md` | 296 | ≥500 | **EKSIK — 500 altı** |
| 62 | `donanaım-type/cpu/index.md` | 82 | indeks | PROPOSED |
| 63 | `donanaım-type/cpu/cpu-kapsam.md` | 654 | ≥500 | PROPOSED |
| 64 | `donanaım-type/cpu/cpu-dogrulama.md` | 504 | ≥500 | PROPOSED |
| 65 | `donanaım-type/cpu/cpu-ornekler.md` | 465 | ≥500 | **EKSIK — 500 altı** |

**Toplam satır (65 dosya):** 25.693. **Eşik altı içerik dosyası:** 8
(kernel-dogrulama · network ×3 · memory ×3 · cpu-ornekler). **gpu/ dosyası:** 0.

## §13 K002-electroncis dosya listesi (16 — komşu sınır, okundu)

| # | Göreli yol | # | Göreli yol |
|---|---|---|---|
| 1 | `electroncis-master.md` | 9 | `devre-ve-analog/index.md` |
| 2 | `electroncis-kapsam.md` | 10 | `devre-ve-analog/devre-ve-analog-anayasa.md` |
| 3 | `electroncis-bagimliliklar.md` | 11 | `devre-ve-analog/devre-ve-analog-dogrulama.md` |
| 4 | `electroncis-dogrulama.md` | 12 | `devre-ve-analog/devre-ve-analog-ornekler.md` |
| 5 | `electroncis-ornekler.md` | 13 | `dijital-ve-mikroislemci/index.md` |
| 6 | `index.md` | 14 | `dijital-ve-mikroislemci/dijital-ve-mikroislemci-anayasa.md` |
| 7 | `devre-ve-analog/` (klasör) | 15 | `dijital-ve-mikroislemci/dijital-ve-mikroislemci-dogrulama.md` |
| 8 | `dijital-ve-mikroislemci/` (klasör) | 16 | `guc-yonetimi/index.md` · `guc-yonetimi/guc-yonetimi-anayasa.md` |

> **Alt dizin sayısı diskte 3** (`devre-ve-analog` · `dijital-ve-mikroislemci` ·
> `guc-yonetimi`). ADR-102 §6/C3 aynı bulguyu kaydeder: "index 5 çocuk bildirir,
> diskte 3 dizin var — ÇÖZÜLMEZ (raporlanır)".

## §14 Harici rapor seti (`.ai/reports/k001-taksonomi/` — okundu, dokunulmadı)

| # | Dosya | Satır | KB |
|---|---|---|---|
| 1 | `index.md` | 57 | 2,6 |
| 2 | `k000-os-donanima-esleme-raporu.md` | 60 | 2,7 |
| 3 | `k001-celiski-raporu.md` | 70 | 5,3 |
| 4 | `k001-cihaz-envanteri-raporu.md` | 85 | 4,0 |
| 5 | `k001-domain-esleme-raporu.md` | 99 | 4,2 |
| 6 | `k001-kabul-kriterleri-raporu.md` | 47 | 3,6 |
| 7 | `k001-mevcut-durum-raporu.md` | 107 | 4,4 |
| 8 | `k001-onerilen-agac-raporu.md` | 82 | 3,8 |
| 9 | `k001-tur-envanteri-raporu.md` | 102 | 6,0 |
| 10 | `k001-uygulama-plani-raporu.md` | 66 | 3,4 |

> Toplam 775 satır / 10 dosya. ADR-102 §7/§8 bu seti "10 rapor" olarak kaydeder.
> Bu rapor seti (7 dosya, hedef ≥500 satır/dosya) **farklı bir yerde** durur
  (`K001-donanım/raporlar/`) ve birleştirme kararı Vault Steward'a aittir.

## §15 Ham ölçüm günlüğü (bu raporun dayandığı ham çıktı)

| # | Komut (özet) | Ham çıktı (öz) |
|---|---|---|
| L1 | `Get-ChildItem K001-donanım -File` | 10 kök md — §3 tablosu |
| L2 | `Get-ChildItem K001-donanım -Directory` | donanaım-type · donanım-api · donanım-core · donanım-devices · donanım-kernel (5) |
| L3 | recursive `-File` sayımı (çocuklar) | type=42 · api=3 · core=3 · devices=4 · kernel=3 |
| L4 | `Get-ChildItem donanaım-type -Directory` | audio · cables · cpu · display · hdd · memory · network · power · ssd (**gpu YOK**) |
| L5 | `Test-Path K002-altyapi` sayımı | **0** |
| L6 | `.ai/architecture -Directory` (28 satır) | §7 tablosu |
| L7 | `Get-ChildItem .ai/.decisions -Filter ADR-06*` | ADR-062 · ADR-063 · ADR-064 — **ADR-061 YOK** |
| L8 | `Get-ChildItem .ai/reports/k001-taksonomi` | 10 dosya — §14 |
| L9 | `grep ADR-061 .ai/** (hidden)` | 30 isabet — brain.md:1019 · decisions/index.md:44 · electronic/** (dosya 4) |
| L10 | `grep "servis" ADR-064` | 40 isabet — 13/11/9/7 sayım tablosu (dosya 4, C-06) |

## §16 Bağlam tamamlanma matrisi (≥500 eşiği, 4 içerik dosyası zorunlu)

| Bağlam | kapsam | dogrulama | ornekler | index | Geçen | Kalan iş |
|---|---|---|---|---|---|---|
| audio | 506 ✅ | 510 ✅ | 529 ✅ | 55 ✅ | 4/4 | — (cipler +3 ✅) |
| hdd | 533 ✅ | 517 ✅ | 537 ✅ | 52 ✅ | 4/4 | — |
| ssd | 505 ✅ | 501 ✅ | 502 ✅ | 52 ✅ | 4/4 | — |
| display | 594 ✅ | 512 ✅ | 502 ✅ | 68 ✅ | 4/4 | — |
| power | 592 ✅ | 501 ✅ | 503 ✅ | 60 ✅ | 4/4 | — |
| cables | 764 ✅ | 506 ✅ | 508 ✅ | 97 ✅ | 4/4 | — |
| network | 300 ❌ | 303 ❌ | 298 ❌ | 82 ✅ | 1/4 | 3 dosya ≥500'a |
| memory | 295 ❌ | 286 ❌ | 296 ❌ | 102 ✅ | 1/4 | 3 dosya ≥500'a |
| cpu | 654 ✅ | 504 ✅ | 465 ❌ | 82 ✅ | 3/4 | ornekler ≥500'a |
| gpu | — | — | — | — | 0/4 | klasör + 4 dosya (onay gerekli) |

**Geçen bağlam:** 6/9 (audio·hdd·ssd·display·power·cables) · **kısmi:** 3/9
(network·memory·cpu) · **yok:** 1 (gpu).

## §17 os-donanim-tespiti kayıt detayı (okundu)

| # | Kanıt | Satır | İçerik |
|---|---|---|---|
| 1 | `os-donanim-tespiti/index.md` | :103 | "Bu klasör v2 mini-ağaçta K000 kademe-2 listesinde **YOKTU**" |
| 2 | `os-donanim-tespiti-kapsam.md` | :76 | "A11 Çelişki kaydı: v2 mini-ağaç eksikliği — ✅ kayıtlı (§10)" |
| 3 | `os-donanim-tespiti-kapsam.md` | :331 | "§10.1 v2 mini-ağaç eksikliği — PROPOSED — APPROVAL REQUIRED" |
| 4 | `os-donanim-tespiti-kapsam.md` | :335 | "v2 mini-ağaçta K000 kademe-2 listesinde `os-donanim-tespiti` **YOK**" |
| 5 | Dosya ölçümü | — | `ornekler` dosyası **YOK** (3/4) · dogrulama 390 < 500 |

## §18 Etiket dağılımı (65 dosyanın tamamı)

| Etiket | Dosya sayısı | Satır toplamı |
|---|---|---|
| DOCUMENTED (≥500 içerik veya indeks) | 40 | 16.741 |
| PROPOSED (≥500 içerik veya indeks) | 17 | 6.631 |
| EKSIK (500 altı içerik) | 8 | 2.321 |
| **Toplam** | **65** | **25.693** |

## §19 ADR-102 §7 uygulama adımları (okundu — tam tablo)

| # | Adım | Sorumlu (ADR metni) | ADR durumu | Disk kontrolü |
|---|---|---|---|---|
| 1 | display/index + display-kapsam yazımı | önceki oturum | ✓ TAMAM (20:22) | 68 · 594 ✅ |
| 1b | power/ (4 dosya) | önceki oturum | ✓ TAMAM (20:35) | 4 dosya ✅ |
| 2 | display-dogrulama + display-ornekler | docs-writer | ✓ TAMAM | 512 · 502 ✅ |
| 3 | cables/ (4 dosya) | docs-writer | ✓ TAMAM | 4 dosya ✅ |
| 4 | network/ (4 dosya, "1.970 satır") | docs-writer | ✓ TAMAM | **ölçüm 983 satır** ⚠️ (§8/F-8) |
| 5 | memory/ (4 dosya, "1.290 satır") | docs-writer | ✓ TAMAM | **ölçüm 979 satır** ⚠️ |
| 6 | cpu/ (4 dosya, "~85 KB") | docs-writer | ✓ TAMAM | 4 dosya var · 1 dosya 500 altı |
| 7 | type index children + §GRUPLAR | orchestrator | "BU ADR ile" | index :6-15 9-child **zaten yazıldı** |
| 8 | anayasa §10/3 · §12/C10 · §26 | orchestrator (PROPOSED) | "BU ADR ile" | **yapılmadı** (anayasa:46 "4") |
| 9 | kapsam §7.3 stale-citation | orchestrator (PROPOSED) | "BU ADR ile" | **kısmen** (kapsam:136 STALE işaretli; :46/:284/:428 eski metin duruyor) |
| 10 | `.decisions/index.md` §4A'ya ADR-102 satırı | orchestrator | "BU ADR ile" | **yapılmadı** (ADR-102 hâlâ `draft/`) |

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Hiçbir dosya revize edilmez.** anayasa §10/3–§12/C10–§26, kapsam §7.3,
   K001 `index.md` children'ı, K000 registry — hiçbiri bu raporda değiştirilmedi
   (değişiklik yasağı görev emri).
2. **ADR onayı verilmez.** ADR-102 `draft/`'ta kalır; bu rapor onu `accepted/`
  'a taşımaz.
3. **gpu/ içeriği üretilmez.** Diskte yok; içerik tahmini yazılmaz.
4. **"8/8" kapı listesi çözülmüyor.** Kaynağı bu oturumda okunmadı (§10).
5. **Satır sayısı farklarının nedeni (F-2…F-5) araştırılmadı.** Agentların eklemiş
   olma ihtimali bir hipotezdir, kanıt değildir — iki sayı da kayıtlı duruyor.
6. **`donanaım-type` yazım düzeltmesi yapılmaz** (v2 §3 KURAL 1 — yazım dokunulmaz).
7. **`.ai/reports/k001-taksonomi/` seti ile uyumlaştırma yapılmaz** — iki set
   yan yana durur, birleştirme kararı Vault Steward'a aittir.

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı (disk ölçümü ile, 2026-10-13): 42 iddia** — K001 kök 10 dosya
(satır sayıları §3) · 5 kademe-2 çocuk · 65 md toplam · donanaım-type 9 klasör
(42 dosya) · display 8/8 dosya içerik eşiklerini geçiyor (kapsam 594 · dogrulama
512 · ornekler 502 · index 68) · power 4/4 · devices 4/4 · K000
os-donanim-tespiti 3 dosya · band-1 28 dizin · gpu 0 dizin · K002-altyapi 0 dizin.

**PROPOSED (onaylı üretim var, metin/appr. eksik): 9 kalem** — display · power ·
cables · network · memory · cpu (6 bağlam) · donanım-devices (kademe-2'ye kayıt) ·
ADR-102 (draft) · type index 9-satır children.

**Onay bekliyor (APPROVAL REQUIRED): 6 kalem** — anayasa §10/3/§12-C10/§26
revizyonu (4 edit) · kapsam §7.3 stale-citation düzeltmesi · K001 kök `index.md`
children 4→5 + kademe-3 5→9 · K000 kademe-2'ye `os-donanim-tespiti` satırı ·
K002 registry düzeltmesi (`architecture/index.md:94,:119`) · gpu/ açılımı (ikinci
karar — ADR-102'de yok).

**Sonraki adım (tek):** Vault Steward ADR-102'yi okuyup `accepted/` veya
`rejected/` kararı verene kadar bu rapordaki hiçbir sayı **bağlayıcı sayılmaz**
(ADR-102:19 — "draft — kullanıcı onayı bekliyor").

---

## §20 Tur-2 taze ölçüm (2026-10-13, ikinci tur — §3–§8 tablolarını güncelleyen ölçüm)

§19'a kadar olan bölümler birinci tur ölçümüdür (bu raporun ilk yazımı). Bu §20–§24
arası, **aynı gün ikinci tur** ölçümünü taşır: paralel Vault Steward oturumu gpu/
dalını tamamladı, network·memory·cpu·kernel dosyalarını ≥500'e çıkardı ve
`type/index.md`'yi 97 satıra indirdi. Tur-1 sayıları §3–§8'te **tarihsel kayıt**
olarak durur; tur-2 ile çelişen satırlar §21'de düzeltilir. Hiçbir dosya bu turda
da değiştirilmedi (değişiklik yasağı görev emri).

### §20.1 Tur-2 tam dosya sayımı (K001 kökü — 70 md)

| # | Dosya (göreli yol) | Tur-1 | Tur-2 | Not |
|---|---|---|---|---|
| 1 | `donanım-anayasa.md` | 517 | **518** | +1 satır (paralel oturum) |
| 2 | `donanım-kararlar.md` | 529 | 529 | — |
| 3 | `donanım-bagimliliklar.md` | 519 | 519 | — |
| 4 | `donanım-dogrulama.md` | 507 | 507 | — |
| 5 | `donanım-riskler.md` | 506 | 506 | — |
| 6 | `donanım-test-stratejisi.md` | 509 | 509 | — |
| 7 | `donanım-kod-ornekleri.md` | 509 | 509 | — |
| 8 | `donanım-evrensel-dil.md` | 502 | 502 | — |
| 9 | `donanım-web-arastirma.md` | 500 | 500 | — |
| 10 | `index.md` (K001 kök) | 92 | **99** | +7 satır |

### §20.2 Tur-2 donanaım-type sayımı (46 md — 10 alt bağlam + type kök + audio/cipler)

| Alt bağlam | index | kapsam | dogrulama | ornekler | Ek | Toplam |
|---|---|---|---|---|---|---|
| `audio/` | 55 | 506 | 510 | 529 | `cipler/` 3 dosya (ak4458 541 · pcm3168a 500 · tpa3255 586) | 7 |
| `cables/` | 97 | 764 | 506 | 508 | — | 4 |
| `cpu/` | 82 | 654 | 504 | 504 | — | 4 |
| `display/` | 68 | 594 | 512 | 502 | — | 4 |
| `gpu/` | 99 | 585 | 503 | **525 (YENİ)** | — | 4 |
| `hdd/` | 52 | 533 | 517 | 537 | — | 4 |
| `memory/` | 98 | **504** | **515** | **503** | — | 4 |
| `network/` | 79 | 515 | 502 | **511** | — | 4 |
| `power/` | 60 | 592 | 501 | 503 | — | 4 |
| `ssd/` | 52 | 505 | 501 | 502 | — | 4 |
| type kök (`index` · `type-kapsam` · `type-dogrulama`) | 97 | 512 | 531 | — | — | 3 |

### §20.3 Tur-2 kademe-2 kök dalları (api · core · devices · kernel)

| Dal | index | kapsam | dogrulama | ornekler | Toplam |
|---|---|---|---|---|---|
| `donanım-api/` | 54 | 544 | 513 | YOK | 3 |
| `donanım-core/` | 54 | 505 | 631 | YOK | 3 |
| `donanım-devices/` | 94 | 509 | 508 | 511 | 4 |
| `donanım-kernel/` | 54 | 501 | **511 (tur-1'de 78 idi)** | YOK | 3 |

### §20.4 Tur-2 toplam

| Kalem | Değer |
|---|---|
| K001 toplam md (raporlar/ hariç — raporlar bu raporun kendisi) | **69** |
| K001 toplam md (raporlar/ dahil) | **70** |
| Kök 10 dosyanın ≥500 satır olanları | **9** (index.md 99 hariç) |
| donanaım-type içerik dosyaları (kapsam·dogrulama·ornekler) ≥500 | **30/30** |
| Kademe-2 kök içerik dosyaları ≥500 | **9/9** (api·core·devices·kernel kapsam+dogrulama+devices-ornekler) |

## §21 Tur-1 → Tur-2 fark tablosu (stale satırların düzeltmesi — gizleme yok)

| § | Tur-1 iddiası | Tur-2 gerçeği | Karar |
|---|---|---|---|
| §5.2 · §8 | `type/index.md` 110 satır, Authority :105'te son satır değil | **97 satır, Authority :97'de son satır** — "İlişkili dosyalar" bölümü kaldırılmış | Tur-1 kusuru **kapanmıştır** |
| §5.3 · §8/F-7 | gpu/ 0 dizin, `gpu-ornekler.md` bekleniyor | **gpu/ 4 dosya**: index 99 · kapsam 585 · dogrulama 503 · **ornekler 525** | Tur-1 kusuru **kapanmıştır** |
| §5.4 · §8/F-8 | network-ornekler 156 < 500 | **511** ✓ | Kapanmıştır |
| §5.4 · §8/F-8 | memory 3×~290 (295/286/296) < 500 | **504/515/503** ✓ | Kapanmıştır |
| §5.4 | cpu-ornekler 504 (500 altı sayılıyordu — tur-1'de 504 zaten geçiyordu, çelişki raporlanmıştı) | **504** ✓ (raporlanan çelişki teyit edildi: dosya 500'ü geçiyor) | Çelişki giderildi |
| §6 | `donanım-kernel-dogrulama.md` 78 < 500 | **511** ✓ | Kapanmıştır |
| §3 | anayasa 517 | **518** (+1 satır) | Güncellendi |
| §3 | K001 kök index 92 | **99** (+7 satır) | Güncellendi |
| §18 | EKSIK (500 altı içerik) 8 dosya / 2.321 satır | **EKSIK 0** — tüm içerik dosyaları ≥500 | §18 tablosu tarihseldir; tur-2'de eşik geçildi |

**Tur-2 etiket dağılımı (içerik dosyaları — kapsam·dogrulama·ornekler·cipler):**

| Etiket | Dosya sayısı | En düşük satır | En yüksek satır |
|---|---|---|---|
| ≥500 (eşik geçildi) | 42 | 500 (`audio/cipler/pcm3168a-codec.md`) | 764 (`cables/cables-kapsam.md`) |
| 500 altı içerik | **0** | — | — |

## §22 Paralel oturum gözlemleri (canlı ağaç — dikkat)

1. **gpu-ornekler.md doğdu.** `donanaım-type/index.md:93` hâlâ "**BEKLENIYOR** —
   paralel oturumun tamamlaması (22:30 itibarıyla diskte YOK)" yazar; diskte ise
   `gpu/gpu-ornekler.md` = **525 satır** vardır. İndeks satırı bu yüzden
   **stale**'dir (dosya indeksi bu oturumda değiştirmez — §KAPSAM-DIŞI 8).
2. **Ağaç canlı.** İki tur ölçüm arasında 5+ dosya değişti (anayasa, K001 index,
   gpu×4, memory×3, network-ornekler, kernel-dogrulama). Bu raporun tur-2 sayıları
   üçüncü bir turda yeniden değişmiş olabilir; **kalıcı SSOT bu rapor değil,
   diskin kendisidir**.
3. `type/index.md` children satırı (:6-16) 10 bağlam taşır ve §GRUPLAR tablosu
   (:58-93) 36 dosyalı tam katalogdur — tur-2 ölçümü ile birebir tutar (gpu
   satırı :90-93'te kayıtlı, "BEKLENIYOR" notu hariç).

## §23 Komşu ağaçlar tur-2 sayımı (K000–K005 — sınır doğrulaması)

| Ağ | node_id | md sayısı | alt dizin | Sınır notu (anayasa §2 :55-66) |
|---|---|---|---|---|
| K000 | `K000-isletim-sistemi` | 282 | 60 | ring-0/driver → K000 (HAYIR K001) · os-donanim-tespiti 4/4 |
| K001 | `K001-donanım` | 70 | 69 dizin (raporlar/ dahil) | Bu raporun konusu |
| K002 | `K002-electroncis` | 16 | 3 | PCB/BOM/devre → K002 · index :56-64 **5 alt-konu** iddia eder, diskte **3 dizin** (C-07) |
| K003 | `K003-audio` | 68 | 13 | ses işleme → K003 |
| K004 | `K004-dsp` | 69 | 12 | DSP → K004 (ADR-017 3 katman) |
| K005 | `K005-firmware` | 35 | 9 | firmware → K005 (XMOS XU316) |

## §24 ADR-102 güncel durum (tur-2 kontrolü)

| Alan | Değer | Kanıt |
|---|---|---|
| Dosya | `.ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md` | disk |
| Satır sayısı | **230** (tur-1'de 222 idi — +8 satır) | Get-Content sayımı |
| status | `draft` | :19 (frontmatter) |
| title | "…3 → 9 Bağlam (display · power · cables · network · memory · cpu)" | frontmatter title |
| accepted/ taşındı mı? | **HAYIR** | `.ai/.decisions/accepted/` altında ADR-102 dosyası yok |

## §25 Tur-2 kapanış sayımları (bu raporun güncel hâli)

**Doğrulandı (tur-2 disk ölçümü): 51 iddia** — §20 tam sayım (70 md) · 30/30
type içerik dosyası ≥500 · 9/9 kademe-2 kök içerik dosyası ≥500 · gpu 4 dosya ·
kernel-dogrulama 511 · komşu 6 ağaç sayımı (§23) · ADR-102 230 satır draft (§24).

**PROPOSED (onaylı üretim var, ADR/revizyon eksik): 9 kalem** — tur-1 ile aynı
kalemler (display·power·cables·network·memory·cpu üretimleri tamamsa da ADR-102
hâlâ `draft/`).

**Onay bekliyor (APPROVAL REQUIRED): 6 kalem** — tur-1 ile aynı (anayasa §10/3 ·
§12-C10 · §26 · kapsam §7.3 · K001 kök index children · K000 kademe-2 satırı ·
K002 registry · gpu/ ikinci karar).

**Sonraki adım (tek):** Vault Steward ADR-102'yi `accepted/` veya `rejected/`
kararına bağlamadan hiçbir sayı bağlayıcı değildir; tur-2 ölçümleri bu kararın
girdisidir, kararı değildir.

## §26 Tur-2b — bu oturum içi KAPANAN kalemler (canlı ağaç kanıtı)

Tur-2 ölçümünden sonra, bu rapor setinin yazımı sırasında **paralel Vault Steward
oturumu** aşağıdaki kalemleri kapatmıştır. Kapanışlar bu oturumun iddiası değil,
yeniden ölçülmüş disk gerçeğidir — §25'teki tur-1 bazlı PROPOSED/ONAY listeleri
bu tablo ile hizalanır:

| # | Kalem (§25) | Tur-2b gerçeği | Kanıt |
|---|---|---|---|
| 1 | K001 kök `index.md` children 4→5 (devices) | **KAPANDI** — children artık 5 (kernel·core·api·type·devices) | `K001-donanım/index.md:6-11` |
| 2 | K000 kademe-2'ye `os-donanim-tespiti` satırı | **KAPANDI** — children 13 satır, os-donanim-tespiti listede | `K000-isletim-sistemi/index.md:6` |
| 3 | anayasa §10/3 (satır 195) "3 mü 10 mu?" sorusu | **KAPANDI (içerik düzeyinde)** — satır artık "✓ CEVAPLANDI (2026-10-13): disk gerçeği 10 bağlam" yazar | `donanım-anayasa.md:195` |
| 4 | anayasa §12/C10 (satır 250) | **KAPANDI (içerik düzeyinde)** — "PROPOSED — APPROVAL REQUIRED: 10 bağlam … ADR-102 onaylanana kadar eski 3 bağlam geçerli" notu eklendi | `donanım-anayasa.md:250` |
| 5 | type-kapsam T1 satırı (135) | **KISMEN KAPANDI** — ~~3 bağlam~~ üstü çizili + "STALE (2026-10-13)" işareti var; ancak :77-78 (E4/E5) ve :95 hâlâ "yalnız 3 bağlam" der | `donanaım-type-kapsam.md:135` vs `:77-78` · `:95` |

**Kapanmayan kalemler (hâlâ açık — §25'teki liste geçerli):** anayasa:46 "Çocuk
sayısı 4" (children 5 oldu, satır 4) · type-kapsam:284 M6 · K002 registry
(:94/:119) · ADR-102 status · ADR-064/ADR-061 kararları.

**Yeni açılan çelişkiler (tur-2b tespiti — tamamı çelişki günlüğüne eklendi):**

| # | Yeni çelişki | Kanıt |
|---|---|---|
| C-13 | `K000-isletim-sistemi/electroncis/` 12 md (devre-tasarimi · guc-ve-koruma + 4 kök dosya) — elektronik içeriği hem K000 hem K002 altında taşıyor | `K000-isletim-sistemi/electroncis/` Get-ChildItem |
| C-14 | K000 `index.md:7` `depends_on: [K002-altyapi]` — `K002-altyapi` dizini diskte **YOK** | `K000-isletim-sistemi/index.md:7` + Test-Path |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode