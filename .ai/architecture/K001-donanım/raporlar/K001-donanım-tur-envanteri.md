---
title: "K001-donanım — Tür Taksonomisi Derin Envanteri (donanaım-type)"
type: analysis-report
node_id: K001-donanım/raporlar/tur-envanteri
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanaım-type
evidence:
  - "Disk ölçümü 2026-10-13 (tur-2): Get-ChildItem -LiteralPath '.ai/architecture/K001-donanım' -Recurse -Filter *.md — donanaım-type 46 md, K001 toplam 70 md"
  - "Satır sayıları: (Get-Content -Encoding UTF8).Count — §3–§5 tablolarındaki her dosya için ayrı ayrı ölçüldü"
  - ".ai/architecture/K001-donanım/donanaım-type/index.md:2 (node_id 'K001-donanım/donanaım-type' — yazım kaynağa sadık) · :6-16 (children: 10 alt bağlam) · :30-37 (dal üretim kronolojisi — §10) · :38-39 (yazım kuralı: düzeltme yasak) · :59-94 (§GRUPLAR 34 satır — tur-2b düzeltmesi, §5.1)"
  - ".ai/architecture/K001-donanım/donanaım-type/power/power-kapsam.md:136-144 (26 alan şema tanımı — type_id…proposed_path)"
  - ".ai/architecture/K001-donanım/donanaım-type/audio/audio-kapsam.md (ADR-038 TEYİT: PCM3168A · AK4458 · TPA3255 — type/index.md:69 kaydı)"
  - "Kademe-2 indeks fm okumaları (§5.2): api/core/kernel children=[] + group:mimari · devices 3 çocuk + group YOK + çift depends_on · type 10 çocuk + group YOK"
  - "Ölçüm geçmişi (§7.1): tur-1→tur-2 9 dosyalık fark tablosu — 6 eşik aşımı, 1 küçülme (type/index 110→97, iyileşme), 1 anayasa +1, 1 rapor seti büyümesi"
validation:
  - "46 dosyanın tamamı 2026-10-13 tur-2 sayımında ölçüldü; en düşük içerik dosyası 500 satır (audio/cipler/pcm3168a-codec.md), en yüksek 764 (cables/cables-kapsam.md)"
  - "İçerik dosyaları (kapsam·dogrulama·ornekler·cipler): 33 dosyanın 33'ü ≥500 — 500 altı içerik 0"
  - "İndeks dosyaları 13: 52–99 satır arası (indeksler için 500 eşiği geçerli değildir — §7)"
  - "§GRUPLAR sayımı tur-2b'de dosyadan birebir kopyalandı: 34 kayıt + 12 kayıtsız = 46 ✓ (önceki '36' iddiası düzeltildi — §5.1)"
open_questions:
  - "donanaım-type yazımı 'donanım' mı 'donanaım' mı? Diskte klasör adı 'donanaım-type' (yanlış A ile); type/index.md:38-39 bunu v2 kaynağına sadakat olarak beyan eder — yazım kaynağı v2 §3 KURAL 1 okunmadan karar verilemez"
  - "gpu/ dalı ADR-102 kapsamı DIŞI (type/index.md:35-36, :90) — ikinci karar (ADR?) yazılmadı"
  - "audio/cipler/ dizini type/index.md §GRUPLAR tablosunda (:59-94) KAYITLI DEĞİL — 3 chip dosyası indekslenmemiş"
  - "cpu tip-id aralığı 'P-Ç-T01…' — aralık sonu belirsiz (index:87)"
  - "type-kapsam :77-78·95 'yalnız 3 bağlam' — :135 STALE işaretlendi (tur-2b), diğer ikisi hâlâ işaretsiz"
  - "§GRUPLAR'da gpu/index.md kayıtlı ama 9 dal indeksi kayıtsız — tutarlı kural ne? (§5.1)"
  - "devices/type indeksleri fm'de group taşımıyor — ADR-097 §5.5 ihlali mi? (§5.2)"
  - "api/core/kernel children: [] — kapsam/dogrulama children'a eklenmeli mi? (§5.2)"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Tür Taksonomisi Derin Envanteri

## §1 Bu raporun rolü (ELI15)

K001-donanım ağacındaki `donanaım-type` dalı, "donanımın türlerine göre ayrıldığı
raf"tur (`donanaım-type/index.md:30`). Bu rapor o rafın **her bir çekmecesini**
tek tek açar: 10 alt bağlam (hdd · audio · ssd · display · power · cables ·
network · memory · cpu · gpu), her alt bağlamın 3–7 dosyası, her dosyanın satır
sayısı ve grubu. Amacım "rafta ne var?" sorusuna **sayısal, kanıtlı** cevap
vermektir. Rapor hiç bir dosyayı değiştirmez; sadece okur ve sayar. Bir dosyanın
varlığını iddia edersem gerçek yolunu ve satır sayısını yazarım; sayamadığım
şeye `UNKNOWN` derim.

## §2 Envanter yöntemi (tekrarlanabilir)

| Adım | Komut / araç | Ne verir |
|---|---|---|
| 1 | `Get-ChildItem '…\K001-donanım' -Recurse -Filter *.md` | Tam dosya listesi (göreli yol) |
| 2 | `Get-Content $f -Encoding UTF8` → `.Count` | Satır sayısı (dosya başına) |
| 3 | `type/index.md:58-93` §GRUPLAR okuması | Dosya → grup (mimari/kanit/kod) eşlemesi |
| 4 | Frontmatter okuması (`node_id` · `status` · `children`) | Düğüm künyesi |

Ölçüm tarihi: **2026-10-13 (tur-2)**. Bu rapor bir tur-1 (ilk ölçüm) ile tur-2
(aynı gün ikinci ölçüm) arasındaki farkları taşır; tur-1 sayıları
`K001-donanım-mevcut-durum-raporu.md` §3–§8'dedir.

### §2.1 Tam ölçüm betiği (tekrarlanabilir — PowerShell 5.1)

```powershell
# K001 tam envanter betiği (2026-10-13 — repo kökünden)
$arch='C:\www\coremusic.net\.ai\architecture'
$k1=(Get-ChildItem $arch -Directory | Where-Object { $_.Name -like 'K001*' }).FullName
# 1) Tam dosya listesi + satır sayısı
Get-ChildItem $k1 -Recurse -Filter *.md | ForEach-Object {
  '{0} = {1}' -f $_.FullName.Substring($k1.Length+1), (Get-Content $_.FullName -Encoding UTF8).Count
}
# 2) İçerik eşik ihlali (index hariç, raporlar hariç)
Get-ChildItem $k1 -Recurse -Filter *.md |
  Where-Object { $_.Name -ne 'index.md' -and $_.DirectoryName -notmatch 'raporlar' } |
  ForEach-Object { [pscustomobject]@{ F=$_.FullName.Substring($k1.Length+1); L=(Get-Content $_.FullName -Encoding UTF8).Count } } |
  Where-Object { $_.L -lt 500 }
# 3) Toplam
(Get-ChildItem $k1 -Recurse -Filter *.md).Count
```

Betik notları: PowerShell 5.1 üçlü operatör (`? :`) desteklemez — betik
`if/ForEach` yapısıyla yazılır; Türkçe karakterli yol için `Get-ChildItem
-Directory | Where-Object Name -like 'K001*'` kısayolu kullanılır (konsol
mojibake'sine karşı dayanıklıdır). Adım 2 çıktısı **boş liste** = eşik ihlali 0
(gerçek çıktı §7).

## §3 Ağ genel görünümü (K001 kökü → kademe-2 → kademe-3)

| Kademe | Düğüm | Dosya sayısı | Not |
|---|---|---|---|
| 1 | `K001-donanım` (kök) | 10 md | 9 içerik + 1 index (99) |
| 2 | `donanım-api/` | 3 md | index 54 · kapsam 544 · dogrulama 513 |
| 2 | `donanım-core/` | 3 md | index 54 · kapsam 505 · dogrulama 631 |
| 2 | `donanım-devices/` | 4 md | index 94 · kapsam 509 · dogrulama 508 · ornekler 511 |
| 2 | `donanım-kernel/` | 3 md | index 54 · kapsam 501 · dogrulama **511** (tur-1'de 78 idi) |
| 2 | `donanaım-type/` | **46 md** | Bu raporun konusu — §4–§9 |
| 2 | `raporlar/` | 1 md | Mevcut durum raporu (599 satır, genişletilmiş) |
| — | **K001 toplam** | **70 md** | Get-ChildItem recursif sayımı |

## §4 10 alt bağlam — derin envanter

Her alt bağlam için: dosya tablosu (satır sayısı · grup) + kapsam özeti (type/index.md
§GRUPLAR kaynaklı) + durum notu. Satır sayıları tur-2 disk ölçümüdür.

### §4.1 audio/ (7 dosya — en zengin dal)

| Dosya | Satır | group | Not |
|---|---|---|---|
| `audio/index.md` | 55 | mimari | Klasör künyesi |
| `audio/audio-kapsam.md` | 506 | mimari | PCM3168A · AK4458 · TPA3255 · ADR-038 (TEYİT) |
| `audio/audio-dogrulama.md` | 510 | kanit | M1–M6 eşik · envanter · HEAD-200 kanıt matrisi |
| `audio/audio-ornekler.md` | 529 | kod | I2S/TDM pin · init sırası · kanal eşleme · jitter |
| `audio/cipler/ak4458-dac.md` | 541 | — | **§GRUPLAR'da KAYITLI DEĞİL** |
| `audio/cipler/pcm3168a-codec.md` | 500 | — | K001'in en düşük içerik satırı (tam 500 = eşikte) |
| `audio/cipler/tpa3255-amplifier.md` | 586 | — | §GRUPLAR'da KAYITLI DEĞİL |

Durum: 3 chip dosyası diskte var ama `type/index.md` §GRUPLAR tablosu (:58-93)
yalnız 4 ses dosyasını listeler (`index` · `kapsam` · `dogrulama` · `ornekler`).
İndeks-kayıt farkı §8'de açık soru olarak taşınır.

### §4.2 cables/ (4 dosya)

| Dosya | Satır | group |
|---|---|---|
| `cables/index.md` | 97 | mimari |
| `cables/cables-kapsam.md` | **764** | mimari — ağacın en uzun dosyası |
| `cables/cables-dogrulama.md` | 506 | kanit — K-C1…K-C8 kapıları |
| `cables/cables-ornekler.md` | 508 | kod — konnektör/pin-out formları |

Kapsam: C-T01…C-T08 tip kayıtları · §0 çatışma kaydı · K002 sınırı (type/index.md:78).

### §4.3 cpu/ (4 dosya)

| Dosya | Satır | group |
|---|---|---|
| `cpu/index.md` | 82 | mimari |
| `cpu/cpu-kapsam.md` | 654 | mimari — P-Ç-T01… tip kayıtları · ADR-038 sınırı |
| `cpu/cpu-dogrulama.md` | 504 | kanit |
| `cpu/cpu-ornekler.md` | 504 | kod |

Durum: tur-1'de `cpu-ornekler` 504 olarak ölçülmüş ama "500 altı" listesine
girmişti (görev girdisi çelişkisi) — tur-2 doğrulaması: **504 ≥ 500, eşik
geçilmiştir**.

### §4.4 display/ (4 dosya)

| Dosya | Satır | group |
|---|---|---|
| `display/index.md` | 68 | mimari |
| `display/display-kapsam.md` | 594 | mimari — panel↔arayüz ayrımı · D-T01…D-T08 |
| `display/display-dogrulama.md` | 512 | kanit — K-D1…K-D8 · EDID/piksel formları |
| `display/display-ornekler.md` | 502 | kod — EDID/DDC şablonları |

### §4.5 gpu/ (4 dosya — tur-2'de doğdu)

| Dosya | Satır | group |
|---|---|---|
| `gpu/index.md` | 99 | mimari — sınır çelişkisi özeti |
| `gpu/gpu-kapsam.md` | 585 | mimari — hesaplama/render · display sınırı |
| `gpu/gpu-dogrulama.md` | 503 | kanit — repo ölçümü (Vulkan/CUDA/OpenCL=0) |
| `gpu/gpu-ornekler.md` | **525** | kod — **tur-1'de YOKTU** (type/index.md:93 hâlâ "BEKLENIYOR" der) |

Durum: gpu paralel Vault Steward oturumunda, **ADR-102 kapsamı DIŞI** açıldı
(type/index.md:35-36). İkinci karar (ADR?) yazılmadı — §8 açık soru.

### §4.6 hdd/ (4 dosya)

| Dosya | Satır | group |
|---|---|---|
| `hdd/index.md` | 52 | mimari |
| `hdd/hdd-kapsam.md` | 533 | mimari — kapasite · risk · 3-2-1 |
| `hdd/hdd-dogrulama.md` | 517 | kanit — SMART · yüzey · gürültü |
| `hdd/hdd-ornekler.md` | 537 | kod — tarama · streaming-buffer |

### §4.7 memory/ (4 dosya — tur-2'de eşik aşıldı)

| Dosya | Satır | group |
|---|---|---|
| `memory/index.md` | 98 | mimari |
| `memory/memory-kapsam.md` | **504** | mimari (tur-1: 295) |
| `memory/memory-dogrulama.md` | **515** | kanit (tur-1: 286) |
| `memory/memory-ornekler.md` | **503** | kod (tur-1: 296) |

### §4.8 network/ (4 dosya — tur-2'de eşik aşıldı)

| Dosya | Satır | group |
|---|---|---|
| `network/index.md` | 79 | mimari |
| `network/network-kapsam.md` | 515 | mimari — N-T01…N-T08 |
| `network/network-dogrulama.md` | 502 | kanit — N1–N6 kapıları |
| `network/network-ornekler.md` | **511** | kod (tur-1: 156 — ADR-102 §7 "1.970 satır" iddiası ile de uyuşmuyordu) |

### §4.9 power/ (4 dosya)

| Dosya | Satır | group |
|---|---|---|
| `power/index.md` | 60 | mimari |
| `power/power-kapsam.md` | 592 | mimari — P-T01…P-T13 · **26 alan şema kaynağı** (§6) |
| `power/power-dogrulama.md` | 501 | kanit — K-P1…K-P8 |
| `power/power-ornekler.md` | 503 | kod — AC/DC–DC/DC–LDO şablonları |

### §4.10 ssd/ (4 dosya)

| Dosya | Satır | group |
|---|---|---|
| `ssd/index.md` | 52 | mimari |
| `ssd/ssd-kapsam.md` | 505 | mimari — flash · WAF · TRIM · TBW |
| `ssd/ssd-dogrulama.md` | 501 | kanit — hız · TRIM · sağlık |
| `ssd/ssd-ornekler.md` | 502 | kod — indeksleme · önbellek |

## §5 type kök dosyaları (3 md)

| Dosya | Satır | group | Not |
|---|---|---|---|
| `donanaım-type/index.md` | 97 | mimari | node_id `K001-donanım/donanaım-type` (:2) · children 10 (:6-16) · §GRUPLAR 34 satır (:59-94) · Authority son satırda (:98 — 2026-10-12 tarihli, rapor seti formatından farklı imza) — tur-1'de 110 satır ve Authority son satırda değildi, **tur-2'de düzeldi** |
| `donanaım-type-kapsam.md` | 512 | mimari | **STALE satırlar**: "yalnız 3 bağlam" (:77-78 · :95) — :135 tur-2b'de STALE işaretlendi; 10 bağlam gerçeği |
| `donanaım-type-dogrulama.md` | 531 | kanit | İddia/URL doğrulama matrisi |

### §5.1 §GRUPLAR tam tablosu (type/index.md:59-94 — 34 satır, birebir kopya)

Bu tablo `donanaım-type/index.md` §GRUPLAR bölümünün **tam kopyasıdır** (dosya
okuması 2026-10-13); her satır = bir kayıtlı dosya:

| # | group | Dosya | Kademe | # | group | Dosya | Kademe |
|---|---|---|---|---|---|---|---|
| 1 | mimari | `donanaım-type-kapsam.md` | K2 | 18 | kod | `power/power-ornekler.md` | K3 |
| 2 | mimari | `index.md` | K2 | 19 | mimari | `cables/cables-kapsam.md` | K3 |
| 3 | kanit | `donanaım-type-dogrulama.md` | K2 | 20 | kanit | `cables/cables-dogrulama.md` | K3 |
| 4 | mimari | `hdd/hdd-kapsam.md` | K3 | 21 | kod | `cables/cables-ornekler.md` | K3 |
| 5 | kanit | `hdd/hdd-dogrulama.md` | K3 | 22 | mimari | `network/network-kapsam.md` | K3 |
| 6 | kod | `hdd/hdd-ornekler.md` | K3 | 23 | kanit | `network/network-dogrulama.md` | K3 |
| 7 | mimari | `ssd/ssd-kapsam.md` | K3 | 24 | kod | `network/network-ornekler.md` | K3 |
| 8 | kanit | `ssd/ssd-dogrulama.md` | K3 | 25 | mimari | `memory/memory-kapsam.md` | K3 |
| 9 | kod | `ssd/ssd-ornekler.md` | K3 | 26 | kanit | `memory/memory-dogrulama.md` | K3 |
| 10 | mimari | `audio/audio-kapsam.md` | K3 | 27 | kod | `memory/memory-ornekler.md` | K3 |
| 11 | kanit | `audio/audio-dogrulama.md` | K3 | 28 | mimari | `cpu/cpu-kapsam.md` | K3 |
| 12 | kod | `audio/audio-ornekler.md` | K3 | 29 | kanit | `cpu/cpu-dogrulama.md` | K3 |
| 13 | mimari | `display/display-kapsam.md` | K3 | 30 | kod | `cpu/cpu-ornekler.md` | K3 |
| 14 | kanit | `display/display-dogrulama.md` | K3 | 31 | mimari | `gpu/index.md` | K3 |
| 15 | kod | `display/display-ornekler.md` | K3 | 32 | mimari | `gpu/gpu-kapsam.md` | K3 |
| 16 | mimari | `power/power-kapsam.md` | K3 | 33 | kanit | `gpu/gpu-dogrulama.md` | K3 |
| 17 | kanit | `power/power-dogrulama.md` | K3 | 34 | kod | `gpu/gpu-ornekler.md` | K3 |

**Sayım düzeltmesi (tur-1 hatası gizlenmez):** tur-1 raporu ve bu raporun
önceki sürümü §GRUPLAR'ı "36 satır" olarak andı; tur-2b dosya okuması **34
satır** buldu (yukarıdaki tablo 1–34). Fark: 2 satır fazla sayım — kaynak
belirlenemedi (`UNKNOWN`); doğru değer 34'tür ve bu satır eski sayıyı
düzeltir. Dağılım: mimari 13 · kanit 11 · kod 10 = **34** ✓ (§7'deki grup
toplamı zaten 34 ile tutarlıydı; "36" sadece üst başlıkta yanlıştı).

Kayıtsız dosya hesabı (netleştirildi): 46 disk dosyası − 34 kayıtlı = **12
kayıtsız** = 9 dal indeksi (gpu hariç — gpu indeksi §GRUPLAR'da kayıtlı) + 3
cipler. gpu indeksinin kayıtlı olması "dal indeksleri kayıtsız" kuralını
tutarsız kılar — bu bir tutarsızlık kaydıdır (`UNKNOWN`: kasıtlı mı hata mı);
Vault Steward'dan E3 (cipler kaydı) + indeks-kayıt kararı beklenmektedir.

## §6 26 alan şema (tip kaydı formatı — SSOT: power-kapsam.md:138-144)

| # | Alan | # | Alan |
|---|---|---|---|
| 1 | `type_id` | 14 | `driver_requirements` |
| 2 | `name_tr` | 15 | `firmware_dependency` |
| 3 | `name_en` | 16 | `hardware_abstraction_requirement` |
| 4 | `category` | 17 | `audio_relevance` |
| 5 | `subcategory` | 18 | `coremusic_relevance` |
| 6 | `description` | 19 | `parent_domain` |
| 7 | `physical_component_or_device` | 20 | `owner_domain` |
| 8 | `primary_function` | 21 | `existing_document` |
| 9 | `interfaces` | 22 | `evidence` |
| 10 | `protocols` | 23 | `status` |
| 11 | `power_requirements` | 24 | `confidence` |
| 12 | `operating_environment` | 25 | `open_questions` |
| 13 | `os_dependencies` | 26 | `proposed_path` |

Kurallar (power-kapsam.md:143-144): bilinmeyen `UNKNOWN` / `NOT PROVIDED` /
`VERIFY REQUIRED`; ölçüm iddiası yok (S1=0); status etiketleri
IMPLEMENTED/DOCUMENTED/PLANNED/PROPOSED/UNKNOWN/VERIFY REQUIRED.

Bilinen tip-kimlik aralıkları (type/index.md §GRUPLAR kaynaklı): power P-T01…P-T13 ·
cables C-T01…C-T08 · display D-T01…D-T08 · memory M-T01…M-T08 · network N-T01…N-T08 ·
cpu "P-Ç-T01…" (aralık sonu belirtilmemiş — UNKNOWN). hdd/audio/ssd/gpu için
tip-id aralığı type/index.md'de yazılmamıştır — `UNKNOWN`.

## §7 Eşik ve grup dağılımı (46 dosya)

| Sınıf | Adet | Aralık | Eşik |
|---|---|---|---|
| İçerik dosyası (kapsam·dogrulama·ornekler·cipler) ≥500 | **33** | 500–764 | 500 satır — 33/33 geçti |
| İçerik dosyası <500 | **0** | — | — |
| İndeks dosyası | 13 | 52–99 | 500 eşiği indekslere uygulanmaz (rapor-1 §9 kuralı) |

### §7.1 Ölçüm geçmişi (tur-1 → tur-2 → tur-2b — dosya bazlı, kayıplar gizlenmez)

Değişen dosyalar (değişmeyenler listede yok — sabit kabul edilir):

| Dosya | tur-1 | tur-2 | tur-2b | Not |
|---|---|---|---|---|
| `donanaım-type/memory-kapsam.md` | 295 | 504 | 504 | ✓ eşik aşıldı |
| `donanaım-type/memory-dogrulama.md` | 286 | 515 | 515 | ✓ |
| `donanaım-type/memory-ornekler.md` | 296 | 503 | 503 | ✓ |
| `donanaım-type/network-ornekler.md` | 156 | 511 | 511 | ✓ (ADR-102 §7 "1.970 satır" iddiası ile uyuşmuyordu — 1.970 dal toplamı olabilir, `UNKNOWN`) |
| `donanım-kernel-dogrulama.md` | 78 | 511 | 511 | ✓ TAMAM |
| `donanaım-type/gpu/` (4 dosya) | YOK | 99·585·503·525 | aynı | tur-2'de doğdu (paralel oturum) |
| `donanaım-type/index.md` | 110 | 97 | 97 | küçüldü — Authority son satıra taşındı (iyileşme) |
| `donanım-anayasa.md` | 517 | 518 | 518 | +1 (tur-2b paralel edit) |
| `raporlar/mevcut-durum-raporu.md` | 464 | 599 | 625 | bu rapor seti — §20–§26 eklentisi + tur-2b bölümü |

tur-1 "500 altı" listesindeki 6 dosyanın 6'sı da tur-2'de eşik üstüne çıktı;
hiçbir dosya tur-2'de **küçülmedi** (yalnız type/index.md küçüldü — bu bir
iyileşme: ölü satır temizliği + Authority taşıma, kanıt: Authority son satırda).

### §7.2 Grup dağılımı (type/index.md §GRUPLAR :59-94 — 34 kayıtlı satır)

| group | Kayıtlı dosya | Kayıtsız (diskte var, indekste yok) |
|---|---|---|
| mimari | 13 (3 type-kök + 9 dal kapsam + gpu-index — gpu indeksi dahil) | 9 dal indeksi (gpu hariç) |
| kanit | 11 (10 dal dogrulama + type-dogrulama) | — |
| kod | 10 (10 dal ornekler) | — |
| **toplam** | **34** | **12** (9 indeks + 3 cipler) |

Sayım kontrolü: 34 + 12 = 46 ✓ (tur-2 Get-ChildItem ile birebir).

### §5.2 Kademe-2 dal indeksleri — frontmatter şablon karşılaştırması (4+1 okuma)

type kökü dışında K001 kademe-2'sinin 5 indeksi ölçüldü (5. indeks
`donanaım-type/index.md` §5.1'de):

| Dal | Çocuk satırı | depends_on | group | Şablon ailesi |
|---|---|---|---|---|
| `donanım-api/index.md` (54) | `children: []` | K000-isletim-sistemi | mimari | v2 mini-ağaç (20 satır fm) |
| `donanım-core/index.md` (54) | `children: []` | K000-isletim-sistemi | mimari | aynı |
| `donanım-kernel/index.md` (54) | `children: []` | K000-isletim-sistemi | mimari | aynı |
| `donanım-devices/index.md` (94) | **3 çocuk satırı** (kapsam·dogrulama·ornekler) | K001-donanım + K000-isletim-sistemi | — (fm'de `title`+`type: architecture-index` var, `group` YOK) | **farklı şablon** (aktif-dizin) |
| `donanaım-type/index.md` (97) | **10 çocuk satırı** | K000-isletim-sistemi | — (fm'de `group` YOK — §GRUPLAR gövdede) | v2 mini-ağaç genişletilmiş |

Bulgular:

1. **`children: []` üçlüsü** (api·core·kernel): bu üç dalın 2-3 dosyası
   (kapsam·dogrulama) çocuk listesinde değil — `donanım-devices` bunu
   children'a yazan ilk dal. Şablon tutarsızlığı, içerik kusuru değil.
2. **`group` alanı tutarsız**: api/core/kernel indeksleri fm'de `group: mimari`
   taşır; devices ve type indeksleri taşımaz (type'da gruplar gövde §GRUPLAR
   tablosunda). ADR-097 §5.5 "her dosyanın fm'dinde 9. alan group" der —
   devices/type indeksleri bu kurala uymuyor olabilir (`⚠️ VERIFICATION
   REQUIRED`: ADR-097 içeriği okunmadı, yalnız type/index.md:57 kural metni
   okundu).
3. **devices `depends_on` iki satır** (K001 + K000) — diğerleri tek satır
   (K000). devices'in K001-kök-bağımlılığı mantıklıdır (kod kanıtı ADR-064'e
   bağlı, §3.1 matris).
4. `donanaım-type` yazım hatası **yalnız type indeksinin node_id'sinde ve
   klasör adında** — api/core/devices/kernel node_id'leri doğru "donanım" yazar
   (okuma: fm satırları). Yani yazım hatası tek düğümde izole (Q1).

## §8 Açık sorular (envantere özgü)

| # | Soru | Kanıt |
|---|---|---|
| Q1 | `donanaım-type` yazımı düzeltilmeli mi? — diskte klasör adı "donanaım" (yanlış A'lı), type/index.md:38-39 bunu "v2 kaynağına sadık — düzeltme yasak" olarak beyan eder | dizin listesi + index:38-39 |
| Q2 | gpu/ için ikinci karar (ADR?) — ADR-102 kapsamı DIŞI açıldı, onay kaydı yok | type/index.md:35-36 |
| Q3 | audio/cipler/ 3 dosya §GRUPLAR'da kayıtlı değil — indekse eklensin mi? | index:58-93 vs disk |
| Q4 | cpu tip-id aralığı "P-Ç-T01…" — aralık sonu (kaç tip?) belirsiz | index:87 |
| Q5 | `type-kapsam.md:77-78·95` "yalnız 3 bağlam" — 10 bağlam gerçeği ile çelişir (STALE; :135 tur-2b'de STALE işaretlendi) | kapsam okuması, rapor-1 §7 |
| Q6 | §GRUPLAR'da `gpu/index.md` kayıtlı ama diğer 9 dal indeksi kayıtsız — tutarlı kural ne? | §5.1 (34 kayıt vs 12 kayıtsız) |
| Q7 | devices/type indeksleri fm'de `group` taşımıyor — ADR-097 §5.5 kuralı ihlali mi? | §5.2 fm okuması + type/index.md:57 |
| Q8 | api/core/kernel `children: []` — kapsam/dogrulama dosyaları children'a eklenmeli mi (devices şablonu)? | §5.2 |

## §9 Dal bazlı sınır notları (komşu etkileşimi — type dallarının K001 dışına bakan yüzü)

Her alt bağlamın komşusu (anayasa §2 + ADR'ler + komşu dizin adları):

| Dal | Komşu ağaç/ADR | Sınır notu |
|---|---|---|
| audio | ADR-038 · K005-firmware · K003-audio | PCM3168A+AK4458+TPA3255 TEYİT; firmware K005, ses işleme K003 — audio/ klasörü yalnız donanım envanteri |
| hdd · ssd | K000-isletim-sistemi (sürücü katmanı) | depolama envanteri K001; SMART/IOCTL K000 |
| display | ADR-064 (5 sınıf: monitör/gömülü) | panel↔arayüz ayrımı; UI tier'ları ui-design dünyası |
| power | K002-electroncis · ADR-063/064 | **en riskli sınır** — güç devresi tasarımı K002/ADR-063, güç gereksinimi envanteri K001 (power-kapsam:136-144 26 alan şema) |
| cables | K002-electroncis (devre) · K003 (sinyal kabloları) | konnektör/pin-out envanteri K001; sinyal bütünlüğü hesabı komşu |
| network | K000 (sürücü/NIC) | PHY/NIC envanteri K001; sürücü K000 |
| memory | K000 (bellek yönetimi) | DIMM/EEPROM envanteri K001; sayfalama K000 |
| cpu | ADR-038 (çekirdek/SoC) · K000 | SoC envanteri K001; çekirdek yönetimi K000 |
| gpu | display (sınır çelişkisi, gpu/index.md kayıtlı) · ADR-102 DIŞI | hesaplama/render — display ile sınır gpu-kapsam.md'de kayıtlı; ikinci karar yok (Q2) |

Kaynak: her satır ilgili dalın kapsam dosyası adı + anayasa §2 satırı +
komşu-dizin adları (§2 kataloğu) ile türetildi; dal içeriklerinin tamamı bu
oturumda okunmadı (`⚠️ VERIFICATION REQUIRED`: sınır notları ad/özet düzeyidir).

## §10 Dal üretim kronolojisi (type/index.md:30-37 okuması — dal doğum tarihleri)

type/index.md ELI15 paragrafı dal açılma sırasını **kendi metninde** taşır
(okuma, 2026-10-13):

| Sıra | Dal(lar) | Tarih/saat | Karar kaydı |
|---|---|---|---|
| 1 | hdd · audio · ssd | v2 mini-ağaç (ilk açılan) | `donanım-anayasa.md §3` — "3 alt bağlam" iddiasının kaynağı (C-02/C-03) |
| 2 | display · power | 2026-10-13 önceki oturum | ADR-102 taslağı PROPOSED |
| 3 | cables · network · memory · cpu | 2026-10-13 bu oturum | ADR-102 taslağı PROPOSED (`PROPOSED — APPROVAL REQUIRED`) |
| 4 | gpu | 2026-10-13 21:52–22:49 paralel Vault Steward oturumu | **ADR-102 kapsamı DIŞI** — 4/4 dosya tamam (gpu-ornekler 22:49'da yazıldı) |

Bu kronoloji üç çelişkinin kökenini açıklar:

1. **C-02/C-03 kökeni**: 1–2. sıradaki açılımlar anayasaya/kapsama
   işlenmemiş; 3. sıradaki genişleme ADR-102'ye taşınmış, 4. sıra hiç ADR'ye
   girmemiş. Üç farklı "işlenme düzeyi" var: işlenmemiş · ADR'de PROPOSED ·
   kararsız (gpu).
2. **audio/cipler neden kayıtsız**: cipler 1. sıradaki (eski) audio dalının
   içine sonradan eklendi; §GRUPLAR tablosu 3. sıradaki genişlemeyle birlikte
   yeniden yazılmış ama cipler atlanmış (`UNKNOWN`: unutuldu mu, bilinçli mi).
3. **gpu indeksi neden kayıtlı**: gpu 4. sırada (en son) açıldı; §GRUPLAR o
   oturumda güncellendi — gpu indeksi tabloya girerken önceki 9 indeks
   girmemiş (Q6'nın kökeni).

## §11 Dosya-künye denetim şablonu (type dalı frontmatter standardı — hdd örneği)

Bir type dalı dosyasının beklenen fm künyesi (hdd-ornekler.md başlıkları +
diğer dal örnekleriyle karşılaştırma):

| fm alanı | hdd-ornekler (tipik) | Beklenen | İhlal var mı? |
|---|---|---|---|
| `title` | ✓ | zorunlu | 0 ihlal (7 dosya örneklemi) |
| `node_id` | ✓ `K001-donanım/donanaım-type/hdd/hdd-ornekler` | yazım kaynağa sadık (donanaım) | ✓ |
| `parent_id` | ✓ | `parent:` anahtarı YASAK (kabul-kriterleri K-A5) | 0 |
| `status` | ✓ | draft/active | ✓ |
| `group` | ✓ kod | 9. alan (ADR-097 §5.5) | ✓ (dal dosyalarında var; indekslerde yok — §5.2 Q7) |
| `evidence` | ✓ | en az 1 satır | ✓ |
| `validation` | ✓ | en az 1 satır | ✓ |
| `open_questions` | ✓ | en az 1 satır | ✓ |
| `date`/`updated` | ✓ | tarih | ✓ |

Denetim bu oturumda 7 dosyalık örneklemle yapıldı (hdd·ssd·audio·display·
power·cables·network kör örnekleme — hepsinde fm 9 alan mevcut). Tam 46
dosyalık fm taraması `⚠️ VERIFICATION REQUIRED` (tek tek okunmadı; kabul-
kriterleri raporu §4.4 betiği Faz 4 kapanışında tam tarama yapar).

## §12 Ölçüm uyarıları (okuyucu için — sayının nasıl okunacağı)

| Uyarı | Açıklama |
|---|---|
| Sayılar anlıktır | Canlı ağaçta paralel Vault Steward oturumu yazım yapıyor (§7.1'deki anayasa 517→518 gibi); bu raporun sayıları 2026-10-13 tur-2/tur-2b anlık görüntüsüdür — Faz 4 kapanışında yeniden ölçülür |
| "46 md" recursif sayımdır | `Get-ChildItem -Recurse -Filter *.md` — kök + kademe-2 + kademe-3 + cipler dâhil; .png/.json/etc. sayılmaz |
| İndeks eşikleri farklıdır | ≥500 yalnız içerik dosyalarına; indeksler ≥50 (rapor-1 §9 kuralı); bu rapor iki eşiği ayrı ayrı raporlar |
| Satır sayısı ≠ kalite | 764 satırlık cables-kapsam ile 500 satırlık pcm3168a-codec eşik geçer; "daha uzun = daha iyi" DEĞİLDİR — eşik altı 0 kusur hedefidir |
| Yazım "donanaım" kasıtlıdır | Düzeltme yasak (index:38-39); bu rapor da yazımı korur |
| Çelişki = gizlenmemiş veri | Bu rapordaki STALE/çelişki işaretleri kusur değil, dürüstlük kaydıdır; düzeltme yetkisi Vault Steward'ındır |

## §13 Komşu envanter ek kanıtları (tur-2b — K000/K002 sayımı, sınır notu)

> Bu bölüm K001 dışına, sınır komşularına bakar: sayımdır, içerik kararı değildir.

### §13.1 K000-isletim-sistemi/electroncis/ — 12 md tam katalog

`K000-isletim-sistemi/electroncis/` dizininde **12 md** sayıldı (tur-2b ölçümü).
İki alt-dalin kendi anayasa dosyaları vardır (C-13 kanıtı — celiskiler raporu §C-13):

| # | Dosya | Satır (yaklaşık) |
|---|---|---|
| 1 | `index.md` | 100+ |
| 2 | `anayasa.md` | 500+ |
| 3 | `kapsam.md` | 500+ |
| 4 | `dogrulama.md` | 500+ |
| 5 | `ornekler.md` | 500+ |
| 6 | `bagimliliklar.md` | 500+ |
| 7–12 | alt-dal dosyaları ×6 | (2 alt-dalin kendi anayasası dâhil) |

Bu 12 md, `K002-electroncis/` kökündeki 6 md (index 100 · master 505 · kapsam 507 ·
dogrulama 507 · ornekler 505 · bagimliliklar 501 — matris raporu §2.2) ile **iki-köklü**
bir yapı oluşturur; hangisinin bağlayıcı olduğu C-13 karar konusudur.

### §13.2 K000-isletim-sistemi kök index.md — kademe-2 kaydı

K000 kök `index.md` (304 satır) kademe-2 kaydı içerir: `index.md:132-146` = 12
alt-başlıklı tablo (bilesen · surec · dosya-sistemi · cihaz · mod · yerlesim ·
kaynak · izleme · kararlar · girdi · guvenlik · arsiv). Bu tablo §GRUPLAR'ın os
ayağıdır; `donanaım-type/index.md:94` ("type/index.md §GRUPLAR — BÜTÜN") ile
**çelişmez, tamamlar** — K000 kökü 12 dalı kendi ağacında, K001 kökü 5 kademe-2'yi
kendi ağacında listeler. Tutarlılık doğrulandı (2026-10-13).

### §13.3 K002 alt-dizin md derinlik ölçümleri

| Alt-dizin | Derinlik 1 md | Toplam md |
|---|---|---|
| `bilesen-analiz` | 4 | 4 |
| `donanim-konfigurasyon` | 4 | 4 |
| `enerji-verimliligi` | 4 | 4 |
| `guvenli-rezervasyon` | 0 (dizin boş) | 0 |
| `test-onceliklileri` | 0 (dizin boş) | 0 |
| **K002 alt-dizin toplamı** | | **12** |

İki alt-dizin (`guvenli-rezervasyon` · `test-onceliklileri`) **dizin olarak var ama
md yok** — K002 ayağındaki kayıtsız-dal bulgusu; C-13 sınır kararını etkiler
(K000/electroncis 12 md bu iki boş alt-diziyle çakışıyor olabilir).

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Hiçbir dosya revize edilmez** — envanter salt okumadır.
2. **Yazım düzeltmesi yapılmaz** — "donanaım-type" yazımı (Q1) karar konusu olup
   bu raporun yetkisi dışındadır.
3. **§GRUPLAR'a audio/cipler kaydı eklenmez** — indeks dosyaları beyazliste
   dışıdır (Q3).
4. **26 alan şemanın her alt bağlamda birebir uygulanıp uygulanmadığı denetlenmez**
   — şema SSOT'u power-kapsam.md'dir; alt bağlam şablon denetimi ayrı bir iştir.
5. **.ai/reports/k001-taksonomi/ seti ile uyumlaştırma yapılmaz.**

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı (tur-2 + tur-2b disk ölçümü): 46 dosya envanteri** — 10 alt bağlam × 3–4
dosya + audio/cipler ×3 + type kök ×3; içerik eşikleri 33/33 ≥500; en düşük 500
(audio/cipler/pcm3168a-codec.md), en yüksek 764 (cables/cables-kapsam.md); K001
toplam 70 md; §GRUPLAR 34 kayıt + 12 kayıtsız = 46 ✓ (§5.1 sayım düzeltmesi
36→34 dahil); ölçüm geçmişi 9 dosyalık tur-1→tur-2 tablosu (§7.1); kademe-2
frontmatter 5 okuma (§5.2); dal-bazlı sınır notu 10 satır (§9).

**PROPOSED: 0 kalem** — bu rapor üretim değil ölçüm taşır.

**Onay bekliyor (APPROVAL REQUIRED): 8 kalem** — Q1 yazım kararı · Q2 gpu ikinci
karar (ADR) · Q3 audio/cipler indeks kaydı · Q4 cpu tip-id aralık sonu · Q5
type-kapsam STALE satır düzeltmesi (kısmi işaretli) · Q6 §GRUPLAR indeks-kayıt
tutarlılığı (gpu indeksi vs 9 indeks) · Q7 fm `group` alanı tutarsızlığı ·
Q8 api/core/kernel children boşluğu.

**Sonraki adım (tek):** Vault Steward Q1–Q8 kararlarını `.decisions/` altına
yazana kadar envanter sayımı bağlayıcıdır; içerik kararı değil, sayımdır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode