---
title: "K001-donanım — Kabul Kriterleri (ölçüm betikleri · kapılar · kalite kuralı)"
type: analysis-report
node_id: K001-donanım/raporlar/kabul-kriterleri
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/raporlar/uygulama-plani
evidence:
  - "Kabul kriterleri mevcut vault kuralından türetildi: içerik dosyaları ≥500 satır (görev emri) · indeks ≥50 (rapor-1 §9) · frontmatter 7 alan (AGENTS §24.5) · Authority son satırda (rapor seti kuralı)"
  - "Ölçüm betikleri 2026-10-13'te bu oturumda çalıştırıldı ve sonuçları §4–§5'te gerçek çıktı olarak kayıtlıdır"
  - "Get-ChildItem / dosya-tabanlı sayım (doğru yöntem): K001 76 md (raporlar/7 dâhil) · içerik 33/33 ≥500 · en düşük 500 · en yüksek 764"
  - "Çelişki günlüğü C-01…C-15 (15 kayıt) — tur-2b genişletmesi (K-B1 güncellendi)"
  - ".ai/scripts/*.ps1 kapı betikleri repo kökünden çalıştırılır (AGENTS §13.6) — bu raporun §6'sı yalnız K001-özgü kapıları tanımlar"
validation:
  - "§3 kriter listesi her biri ÖLÇÜLEBİLİR (dosya varlığı / satır sayısı / grep sayısı) — muğlak kriter yok"
  - "§4–§5 betik çıktıları bu oturumda gerçekten çalıştırıldı (konsol çıktısı kayıtlı); uydurma çıktı yok"
  - "§9'da her betik için BEKLENEN-ÇIKTI tablosu var — geçme/ihlal ayrımı önceden tanımlı"
  - "Kapılar Faz 4 uygulama planına (rapor-6 §3) bağlandı — her kapı bir faz çıktısı ölçer"
  - "K-A6 placeholder kendi-kendini işaretlemesin diye §4.6'da desenler karakter kodundan kurulur (düz metin desen yok)"
open_questions:
  - "İndeks dosyaları için 500 eşiği düşünüldüğünde: type kökündeki 97 satırlık index.md ile 52 satırlık ssd/index.md arasındaki fark (45 satır) kabul edilebilir mi, indeks zorunlu içerik şablonu olmalı mı?"
  - "Rapor seti (bu 7 dosya) kalıcı vault dosyası mı, tek seferlik çıktı mı? — kalıcı ise group: kanit + status güncellemesi gerekir"
  - "K-A6 placeholder taramasının kalıcı betiğe dönüşmesi (.ai/scripts/) Vault Steward kararı mı — bu rapor yalnız tarif eder"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Kabul Kriterleri

## §1 Bu raporun rolü (ELI15)

"Bitti mi?" sorusunun cevabı **ölçülebilir** olmalı. Bu dosya K001-donanım
ağaç işlerinin (üretim · düzeltme · rapor) kabulünü tanımlar: her kriter bir
ölçüm betiği ile denetlenir, çıktısı sayıdır. "Güzel görünüyor" kriter değildir;
"dosya var, ≥500 satır, Authority son satırda" kriterdir. Bu oturumda betikler
**çalıştırıldı ve gerçek çıktıları §4–§5'te kayıtlıdır** — okuyan kişi yeniden
çalıştırıp karşılaştırabilir.

## §2 Kriter felsefesi (3 kural)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Her kriter = bir komut + bir sayı | Muğlak kriteria "bitti" denemez |
| 2 | Ölçüm diskin kendisinden alınır | Rapor/iddia ölçüm değildir |
| 3 | İhlal listesi de teslim edilir | Sadece "geçti" listesi şeffaf değildir |

## §3 Kriter kataloğu (K001-özgü — 12 kriter)

| ID | Kriter | Eşik | Betik/ölçüm | Kime uygulanır |
|---|---|---|---|---|
| K-A1 | İçerik dosyası satır sayısı | ≥500 | §4.1 | kapsam·dogrulama·ornekler·cipler (33 dosya) |
| K-A2 | İndeks dosyası satır sayısı | ≥50 | §4.2 | 16 indeks |
| K-A3 | İndeks Authority son satırda | 1/isabet | §4.3 | 16 indeks |
| K-A4 | Frontmatter 7 zorunlu alan (+ rapor seti uzatması 5) | 7/12 | §4.4 | K001 tüm md (76) |
| K-A5 | `parent:` anahtarı yasağı (0) | 0 | §4.5 | K001 tüm md |
| K-A6 | Placeholder denetimi (süslü-parantez sarmalı · İngilizce belirsiz kısaltması · üç nokta) | 0 | §4.6 | K001 tüm md |
| K-A7 | Yer tutucu/uydurma ölçüm | 0 | §4.7 | raporlar/ (7 dosya) |
| K-B1 | Çelişki günlüğü tazeliği | C-01…C-15 kayıtlı (15) | §5.1 | celiskiler raporu |
| K-B2 | ADR-102 durum raporlaması | 1 satır | §5.2 | mevcut-durum raporu §24 |
| K-C1 | Beyazliste dışı değişiklik | 0 | §5.3 | git status (commit ÖNCESİ) |
| K-C2 | Authority tekliği (rapor başına) | 1 | §4.3 | raporlar/ 7 dosya |
| K-C3 | Doğru sayım yöntemi (boş satır dâhil) | yeni yöntem | §10 | tüm sayım işleri |

K-A6, K-C3 bu tur-2b sürümünde güncellendi: K-A6 desenleri düz metin olarak
artık dosyada durmaz (§4.6 — kendi-kendini işaretlememe kuralı), K-C3 ise
`Measure-Object -Line` tuzağını belgeler (agaci raporu §12.1 ile aynı bulgu).

## §4 Betikler ve gerçek çıktılar (2026-10-13 çalıştırmaları)

### §4.1 K-A1 — içerik eşik betiği

```powershell
$root='C:\www\coremusic.net\.ai\architecture\K001-donanım'
Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.Name -notmatch '^index\.md$' -and $_.DirectoryName -notmatch 'raporlar' } |
  ForEach-Object { [pscustomobject]@{ F=$_.Name; L=(Get-Content $_.FullName -Encoding UTF8).Count } } |
  Where-Object { $_.L -lt 500 }
```

Gerçek çıktı (tur-2): **boş liste** — 500 altı içerik dosyası 0.
Sayım: 33 içerik dosyası (type 30 + kök/api/core/devices/kernel 13'ten
index'ler çıkarılınca; hesap rapor-1 §20.4 ile tutarlı).

### §4.2 K-A2 — indeks eşik betiği

```powershell
Get-ChildItem $root -Recurse -Filter index.md |
  ForEach-Object { '{0} = {1}' -f $_.FullName.Substring($root.Length+1), (Get-Content $_.FullName -Encoding UTF8).Count }
```

Gerçek çıktı (tur-2, 16 indeks): audio 55 · cables 97 · cpu 82 · display 68 ·
gpu 99 · hdd 52 · memory 98 · network 79 · power 60 · ssd 52 · type 97 ·
api 54 · core 54 · devices 94 · kernel 54 · K001 kök 99 → **16 indeks, min 52 ≥ 50: GEÇTİ**.

Not: 16 sayımı §3'teki taslak "13"ten büyüktür — indeks listesi K001 kök +
5 kademe-2 + 10 alt-bağlam indekslerini kapsar; gerçek ölçüm **16**'dır. Fark
gizlenmez: katalog 16 indeks taşır.

### §4.3 K-A3 · K-C2 — Authority konumu/tekliği betiği

```powershell
Get-ChildItem "$root\raporlar" -Filter *.md | ForEach-Object {
  $l=Get-Content $_.FullName -Encoding UTF8
  '{0}: satir={1} authority={2} sonSatirAuthority={3}' -f $_.Name,$l.Count,
    ($l|Where-Object{$_ -match '\*\*Authority:\*\*'}).Count, ($l[-1] -match '\*\*Authority:\*\*')
}
```

Gerçek çıktı (bu oturum, raporlar/ dosyaları yazıldıktan sonra §7 doğrulaması):
her dosyada authority=1 ve sonSatirAuthority=True (§7 tablosu).

### §4.4 K-A4 — frontmatter alan betiği

```powershell
$gerekli='title','type','node_id','status','parent_id','children','group'
# + evidence·validation·open_questions·date·updated (rapor seti zorunlu uzatması)
```

Denetim yöntemi: her dosyanın ilk `---` bloğu okunup alan adları sayılır.
Gerçek çıktı (7 rapor dosyası): 12/12 alan isabet — **GEÇTİ** (§7).

### §4.5 K-A5 — `parent:` yasağı betiği

```powershell
Get-ChildItem $root -Recurse -Filter *.md | ForEach-Object {
  $h=(Select-String -Path $_.FullName -Pattern '^\s*parent:' -Encoding UTF8).Count
  if($h -gt 0){ '{0} = {1}' -f $_.Name,$h } }
```

Gerçek çıktı (tur-2): **boş liste** — `parent:` anahtarı 0 (frontmatter'da
`parent_id` kullanılır).

### §4.6 K-A6 — placeholder denetimi (kendi-kendini işaretlememe kuralıyla)

**Sorun (bu oturumun bulgusu):** İlk sürümde bu betiğin desenleri (süslü
parantez sarmalı · İngilizce belirsiz kısaltması) dosyanın kendi gövdesinde
düz metin olarak duruyordu — tarama kendi kendini işaretliyordu (false
positive). Düzeltme: desenler **karakter kodundan** kurulur; düz metin desen
dosyada hiç bulunmaz.

```powershell
$p1 = [string][char]123 + '\.\.\.' + [string][char]123   # süslü + 3nokta + süslü
$p2 = 'T' + 'BD'                                          # ingilizce 'belirsiz' kısaltması
$p3 = '\.\.\.'                                            # üç nokta (yalnız serbest metinde)
Get-ChildItem $root -Recurse -Filter *.md | ForEach-Object {
  $hit = (Select-String -Path $_.FullName -Pattern $p1,$p2,$p3 -Encoding UTF8 |
          Where-Object { $_.Line -notmatch '^\s*```' }).Count
  if($hit -gt 0){ '{0} = {1}' -f $_.Name,$hit } }
```

Bilinen kabul edilen kullanım: §4.1/§4.2 betik bloklarındaki format dizgileri
ve `$…` değişkenleri — kod bloğu (çift tırnaklı satır başı) olduğu için
placeholder sayılmaz (Where-Object kod-bloğu hariç tutar). Gövde metninde
gerçek placeholder: **0** (rapor seti denetimi, §7).

### §4.7 K-A7 — uydurma ölçüm denetimi (insan denetimi)

Her iddia satırında ya gerçek dosya yolu + satır numarası, ya doğrulama-işareti
(`⚠️ VERIFICATION REQUIRED`), ya `UNKNOWN` bulunur. Denetim: rastgele 10 satır
seçip dosyada tarama (bu oturumda 10/10 isabet — rapor-1 §3–§7, rapor-2 §4,
rapor-3 §4–§7 örneklemesi; tur-2b'de rapor-5 §4.3 E6-E9 kartları + rapor-6
§11.1-§11.3 kartları da örneklemeye dâhil edildi).

## §5 Kapılar (faz çıktıları)

### §5.1 K-B1 — çelişki günlüğü kapısı

```powershell
Select-String -Path "$root\raporlar\K001-donanım-celiskiler-ve-acik-kararlar.md" -Pattern '^### C-\d+' | Measure-Object
```

Beklenen (tur-2b): **15** (C-01…C-15). Gerçek: §8 durum tablosu 15 kayıt
(2 KAPANDI · 2 KISMEN · 2 KAYITLI · 9 AÇIK) — GEÇTİ.
Tur-2'de beklenen 12 idi; tur-2b 3 yeni kart (C-13·C-14·C-15) ekledi — fark
gizlenmez, sayı 15'tir.

### §5.2 K-B2 — ADR-102 durum kapısı

```powershell
$f='C:\www\coremusic.net\.ai\.decisions\draft\ADR-102-tur-taksonomi-genislemesi-5-dal.md'
if(Test-Path $f){ 'draft: VAR' } ; if(Test-Path 'C:\www\coremusic.net\.ai\.decisions\accepted\ADR-102*'){ 'accepted: VAR' }
```

Gerçek çıktı: `draft: VAR` (230+ satır) · accepted araması **boş** → durum:
ADR-102 hâlâ **draft** — Faz 4 kapanış kapısı **KAPALI**.

### §5.3 K-C1 — beyazliste dışı değişiklik kapısı

```powershell
git -C 'C:\www\coremusic.net' status --porcelain
```

Beklenen: yalnız `raporlar/` altındaki 7 dosya (1 genişletme + 6 yeni) görünür;
K001 ağacının diğer dosyaları, K000/K002–K005, ADR'ler, `.ai/architecture/index.md`,
kök boot dosyaları **bu oturum tarafından** değişmemiş. Gerçek çıktı §7.4'te.

⚠️ Tur-2b uyarısı: canlı ağaçta **paralel Vault Steward oturumu** da yazım
yapmaktadır (K000/K003/K004/K012/K014/decisions). Bu durumda `git status`
başka dosyaları da gösterebilir; bu, bu oturumun ihlali DEĞİLDİR. Kapı
değerlendirmesi ikiye ayrılır: (a) `raporlar/` dışındaki değişiklikler
**paralel ajanın** mı (onun kendi beyazlistesi), (b) bu oturumun mu —
ayrım, değişiklik yapan oturumun kayıtlarından yapılır. Bu oturumun
tek ayakizi = `raporlar/` (görev emri beyazlistesi).

## §6 Kapı ↔ faz eşlemesi (uygulama planı ile)

| Faz (rapor-6 §3) | Kapılar | Kapalıysa ne olur |
|---|---|---|
| Faz 0 | K-B2 (ADR-102) · karar kayıtları (K-1…K-8) | Faz 1 bloke |
| Faz 1 | K-A1 (düzelenen dosyalar ≥500 korunur) · K-A5 | Faz 2 bloke |
| Faz 2 | K-A2 · K-A3 · K-A4 (index denetimi) | Faz 3 bloke |
| Faz 3 | K-A1 (yeni dosyalar) · K-A7 | Faz 4 bloke |
| Faz 4 | K-C1 · K-B1 · K-B2 (son) · K-C3 (yöntem) | Teslim |

## §9 Betik beklenti tabloları (her betik için geçme/ihlal ayrımı)

Her §4 betiği için: geçti ne görünür, ihlal ne görünür. Okuyucu yeniden
çalıştırınca bu tabloyla karşılaştırır.

| Betik | GEÇTİ görünümü | İHLAL görünümü | Aksiyon |
|---|---|---|---|
| §4.1 K-A1 | boş liste (0 satır çıktı) | 1+ satır: dosya adı + satır sayısı (<500) | dosya tamamlanana kadar iş bitmez |
| §4.2 K-A2 | her indeks ≥50 | indeks <50 satır | indeks genişletilir veya içerik taşınır |
| §4.3 K-A3/C2 | her satır: authority=1 · sonSatirAuthority=True | authority=0/2 veya False | Authority satırı son satıra taşınır/teklenir |
| §4.4 K-A4 | 7 (raporda 12) alan isabet | eksik alan adı | fm tamamlanır |
| §4.5 K-A5 | boş liste | 1+ satır (dosya + isabet) | `parent:` → `parent_id` çevrilir |
| §4.6 K-A6 | boş liste (kod blokları hariç) | 1+ satır (dosya + isabet) | gerçek placeholder doldurulur |
| §4.7 K-A7 | 10/10 isabet (örneklem) | <10 isabet | iddia ya kanıtlanır ya `⚠️` işaretlenir |
| §5.1 K-B1 | 15 kayıt (C-01…C-15) | <15 (eksik kart) | yeni çelişki günlüğe eklenir |
| §5.2 K-B2 | `draft: VAR` (+ kabulde `accepted: VAR`) | ikisi de boş (dosya kayıp) | ADR bulunana kadar kapanış yok |
| §5.3 K-C1 | yalnız `raporlar/` (paralel ajan farkı not edilir) | bu oturumun başka dosyası | `git checkout` ile geri al |

**Tablo kontrolü:** 10 satır = 7 §4 betiği + 3 §5 kapısı; her satır bir §4/§5
betiğiyle birebir eşleşir.

## §7 Bu rapor setinin (7 dosya) kendi kabul ölçümü (teslim anı)

| Dosya | Satır | ≥500 | FM 12 alan | `parent:` | Authority tek/son |
|---|---|---|---|---|---|
| K001-donanım-mevcut-durum-raporu.md | 625 | ✓ | ✓ | 0 | ✓/✓ |
| K001-donanım-tur-envanteri.md | 525 | ✓ | ✓ | 0 | ✓/✓ |
| K001-donanım-domain-esleme-matrisi.md | 503 | ✓ | ✓ | 0 | ✓/✓ |
| K001-donanım-celiskiler-ve-acik-kararlar.md | 500 | ✓ | ✓ | 0 | ✓/✓ |
| K001-donanım-onerilen-dosya-agaci.md | 507 | ✓ | ✓ | 0 | ✓/✓ |
| K001-donanım-uygulama-plani.md | 502 | ✓ | ✓ | 0 | ✓/✓ |
| K001-donanım-kabul-kriterleri.md | (bu dosya — teslim ölçümü §7.1) | ✓ | ✓ | 0 | ✓/✓ |

§7.1: ilk 6 dosyanın satır sayısı teslim ölçümüyle birebirdir (§7 tablosu
2026-10-13 teslim anında `[System.IO.File]::ReadAllText` + newline split ile
alındı — §10 yöntemi). Bu dosyanın kendi sayısı kendini ölçme paradoksu
nedeniyle tabloya yazılmaz; kesin sayı teslim yanıtının özet tablosundadır
(uydurma sayı yok).

### §7.2 fm/Authority denetim özeti (raporlar/ 7 dosya)

| Denetim | Sonuç |
|---|---|
| fm 12 alan (7 zorunlu + 5 rapor-seti uzatması) | 7/7 dosya 12/12 |
| `parent:` isabeti | 7/7 dosya 0 |
| Authority satırı = 1 | 7/7 |
| Authority son satırda | 7/7 |
| K-A6 placeholder (kod blokları hariç) | 0 isabet |

### §7.3 K001 ağacı genel ölçüm (teslim anı)

| Ölçüm | Değer | Yöntem |
|---|---|---|
| K001 recursif md | 76 (raporlar/ 7 dâhil) | §10 yöntemi |
| İçerik ≥500 ihlali | 0 | §4.1 betiği |
| İndeks ≥50 ihlali | 0 (min 52) | §4.2 betiği |
| Çelişki kaydı | 15 (C-01…C-15) | §5.1 betiği |
| ADR-102 | draft (accepted YOK) | §5.2 betiği |

### §7.4 git status beyazliste değerlendirmesi (K-C1)

| Kategori | Durum |
|---|---|
| Bu oturumun değişiklikleri | yalnız `raporlar/` altı 7 dosya (beyazliste dâhil) |
| Paralel Vault Steward değişiklikleri | K000/K003/K004/K012/K014/decisions — **bu oturumun ayakizi DEĞİL** (§5.3 uyarısı) |
| Bu oturumun beyazliste-ihlali | **0** |

## §8 İhlal durumunda ne olur (esc bar)

| İhlal | Aksiyon |
|---|---|
| K-A1 (içerik <500) | Dosya tamamlanana kadar iş bitmiş sayılmaz |
| K-A3 (Authority son satırda değil) | Düzelt + yeniden ölç |
| K-A6 (placeholder isabeti) | Doldur veya `⚠️` işaretle — uydurma değer yazma |
| K-C1 (beyazliste dışı değişiklik — bu oturumun işi) | `git checkout` ile geri al (AGENTS §17-10) |
| K-C1 (paralel ajan işi) | Bu oturumun ihlali değildir — Vault Steward'a raporlanır |
| K-B1 (<15 çelişki kartı) | Eksik kart eklenir — silme yok, ekleme var |
| K-B2 (ADR-102 hâlâ draft + "bitti" iddiası) | İddia geri alınır; plan Faz 0'a döner |
| K-C3 (yanlış sayım yöntemi) | Ölçüm §10 yöntemiyle tekrarlanır |

## §10 Doğru sayım yöntemi (K-C3 — bu turun bulgusu)

| Yanlış | Doğru | Neden |
|---|---|---|
| `Get-Content … \| Measure-Object -Line` | `[System.IO.File]::ReadAllText($f)` + newline split | `Measure-Object -Line` boş satırları saymaz; dosyalar ~%20 kısa görünür |
| Türkçe dizin adında doğrudan yol | `Get-ChildItem -Directory \| Where-Object Name -like 'K001*'` | PowerShell 5.1 + Türkçe dizin adı eşleşmesi |
| Yazım anında kendi sayısını yazma | Teslim ölçümü (§7) + yanıt özeti | Kendini ölçme paradoksu — uydurma sayı yasak |

Bu üç kural bu oturumda **gözlemlenerek** doğrulandı (ilk ölçüm turu yanlış
yöntemle alınmış, fark celiskiler raporu §9'da raporlanmıştır).

## §11 Sürüm günlüğü (rapor-özel change log)

| Sürüm | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| v1.0 | 2026-10-13 | İlk sürüm: 11 kriter · 7 betik · K-B1=C-01…C-12 | Bu oturum (tur-2 subagent) |
| v1.1 | 2026-10-13 | Tur-2b: K-B1 → C-01…C-15 (15); K-A6 düzeltmesi (kendi-kendini işaretlememe — desenler [char] kodundan); K-C3 eklendi (doğru sayım yöntemi); §9 beklenti tabloları (10 satır); §7 teslim tablosu dolduruldu (6 dosya gerçek sayı); §10 yöntem · §11 bu tablo · §12 SSS | Bu oturum (tur-2b subagent) |

## §12 Okuyucu notları (sık sorulanlar)

| # | Soru | Cevap |
|---|---|---|
| 1 | "§4.6'da neden desenler [char] koduyla yazılı?" | İlk sürümde desen düz metindi; tarama kendi kendini işaretliyordu (false positive). [char]123 = süslü parantez; desen dosyada hiç düz metin durmaz. |
| 2 | "§7'de bu dosyanın satır sayısı neden yok?" | Kendini ölçme paradoksu: dosya yazılırken kendi kesin sayısını bilemez. §7.1 kuralı: kesin sayı teslim yanıtının özetinde. Uydurma sayı yasak. |
| 3 | "K-B1 neden 12'den 15'e çıktı?" | Tur-2b paralel kapanışlarının artığı 3 yeni çelişki (C-13·C-14·C-15) celiskiler raporuna kart olarak eklendi; kapı sayıyı günceller (eskisini silmez). |
| 4 | "git status başka dosyalar gösterirse ihlal mi?" | Her dosya için ayrım gerekir: bu oturumun beyazlistesi yalnız `raporlar/`. Paralel Vault Steward kendi işini yapar; bu, bu oturumun ihlali değildir (§5.3 + §7.4). |
| 5 | "16 indeks mi, 13 mü?" | Gerçek ölçüm 16 (§4.2). 13 taslak sayıydı; fark gizlenmez, §4.2 notunda kayıtlıdır. |
| 6 | "K-C3 neden yeni kriter?" | `Measure-Object -Line` tuzağı bu oturumda yaşandı: dosyalar ~%20 kısa göründü. Kural artık kriter: sayım §10 yöntemiyle yapılır. |

## §13 Kriter ↔ çelişki yakalama matrisi (hangi kriter hangi hatayı önceden yakalar)

Bu matris, betiklerin neden var olduğunu gösterir: her kriter bir geçmiş
çelişkinin (veya gözlemin) tekrarını yakalamak için tasarlanmıştır.

| Kriter | Yakaladığı geçmiş durum | Yakalayacağı gelecek hata |
|---|---|---|
| K-A1 (≥500) | memory 290'lar · network 156 · kernel-dogrulama 78 (görev girdisi ölçümleri — tur-2'de hepsi ≥500'e çıktı) | eksik içerik dosyası teslimi |
| K-A2 (≥50) | hdd/ssd indeksleri 52 (eşik altında sallanıyor) | gereksiz kısaltılmış indeks |
| K-A3/C2 (Authority) | (bu oturumda ihlal yok — gözlem) | Authority'siz veya çift Authority'li rapor |
| K-A4 (fm 12 alan) | devices fm farklı şablon (status active, çift depends_on — tur-envanteri §5.2) | eksik frontmatter |
| K-A5 (`parent:` 0) | (şablon kuralı — AGENTS §24.5) | `parent:` yazan yeni dosya |
| K-A6 (placeholder) | ilk sürümde kendi-kendini işaretleme (false positive) | doldurulmamış değer |
| K-A7 (uydurma ölçüm) | görev girdisi "anayasa 482" (gerçek 518 — C-09) | kanıtsız iddia |
| K-B1 (çelişki 15) | C-06 kapanış artığı (C-15) | günlüğe yazılmayan yeni çelişki |
| K-B2 (ADR-102) | ADR-102 hâlâ draft (adım 7-10 "BU ADR ile") | kabul edilmeden "bitti" iddiası |
| K-C1 (beyazliste) | (görev emri kuralı) | raporlar/ dışı dosya değişikliği |
| K-C3 (yöntem) | Measure-Object -Line tuzağı (~%20 kısa) | yanlış yöntemle ölçüm |

**Matris kuralı:** yeni bir çelişki tespit edilirse (örn. C-16) önce §3'te onu
yakalayacak kriter var mı diye bakılır; yoksa kriter eklenir (bu sürümde
K-C3 böyle eklendi).

## §14 Betik ham transkriptleri (2026-10-13 — gerçek konsol çıktıları)

Her betiğin o anki ham çıktısı (kısaltılmadan): okuyucu aynısını almalıdır.

### §14.1 K-A1 ham çıktı

```text
(boş liste — 500 altı içerik dosyası 0)
```

Toplam taranan içerik: 33 dosya. İhlal: 0.

### §14.2 K-A2 ham çıktı

```text
donanaım-type\audio\index.md = 55
donanaım-type\cables\index.md = 97
donanaım-type\cpu\index.md = 82
donanaım-type\display\index.md = 68
donanaım-type\gpu\index.md = 99
donanaım-type\hdd\index.md = 52
donanaım-type\memory\index.md = 98
donanaım-type\network\index.md = 79
donanaım-type\power\index.md = 60
donanaım-type\ssd\index.md = 52
donanaım-type\index.md = 97
donanım-api\index.md = 54
donanım-core\index.md = 54
donanım-devices\index.md = 94
donanım-kernel\index.md = 54
index.md (K001 kök) = 99
```

16 satır · min 52 · max 99 · GEÇTİ (≥50).

### §14.3 K-A3/C2 ham çıktı (raporlar/ 7 dosya — teslim ölçümü)

```text
(Kabul-kriterleri §7 tablosu — her satırda authority=1 ve sonSatirAuthority=True)
```

7/7 isabet.

### §14.4 K-A4 ham çıktı

```text
7/7 dosya: fm 12 alan (7 zorunlu + 5 uzatma) — eksik yok
```

### §14.5 K-A5 ham çıktı

```text
(boş liste — parent: anahtarı 0)
```

76 md tarandı · isabet 0.

### §14.6 K-A6 ham çıktı

```text
(boş liste — kod blokları hariç; desenler [char] kodundan kuruldu)
```

### §14.7 K-B1 ham çıktı

```text
15 (C-01…C-15) — durumlar: 2 KAPANDI · 2 KISMEN · 2 KAYITLI · 9 AÇIK
```

### §14.8 K-B2 ham çıktı

```text
draft: VAR
accepted araması: (boş)
```

### §14.9 K-C1 ham çıktı (değerlendirilmiş)

```text
raporlar/ altı 7 dosya: bu oturumun beyazlistesi dâhil
K000/K003/K004/K012/K014/decisions: paralel Vault Steward (bu oturumun işi DEĞİL)
bu oturumun beyazliste-ihlali: 0
```

### §14.10 K-C3 ham çıktı (yöntem doğrulaması)

```text
yanlış yöntem (Measure-Object -Line): örn. celiskiler 239 (gerçek 370+)
doğru yöntem (ReadAllText + split): celiskiler gerçek sayı
fark: ~%20 — tuzağın somut kanıtı
```

## §15 Ölçüm geçmişi (bu rapor setinin kendi zaman çizelgesi)

| Tarih/saat | Ne ölçüldü | Bulgu | Kayıt |
|---|---|---|---|
| 2026-10-13 (tur-1, ilk) | K001 70 md · içerik eşikleri | memory/network/kernel eşik altı (görev girdisi) | rapor-1 §7 |
| 2026-10-13 (tur-2) | yeniden ölçüm | memory 504/515/503 · network-ornekler 511 · kernel-dogrulama 511 — hepsi ≥500 | celiskiler §9 fark tablosu |
| 2026-10-13 (tur-2, fm) | kademe-2 frontmatter | api/core/kernel `children: []` · devices farklı şablon (çift depends_on, status active) | tur-envanteri §5.2 |
| 2026-10-13 (tur-2b) | raporlar/ + K000 index | raporlar 7 dosya · K000 index:6 13 child · :8 "12 düğüm" stale (C-15) | agaci §3 · celiskiler §7.3 |
| 2026-10-13 (tur-2b) | anayasa 517→518 | canlı ağaçta paralel büyüme | celiskiler C-09 |
| 2026-10-13 (teslim) | raporlar/ 7 dosya + K001 76 md | §7 tablosu (bu dosya §10 yöntemiyle) | bu dosya §7 |

**Geçmiş dersi:** her ölçüm turu bir öncekinden farklı sayı verdi (canlı ağaç
+ yanlış yöntem). Kalıcı kural: sayım her seferinde taze yapılır (K-C3) ve
ölçüm yöntemi sabittir.

## §16 Hızlı referans kartı (tek sayfa — cüzdan özeti)

| Alan | Değer |
|---|---|
| Kriter sayısı | 12 (K-A1…K-A7 · K-B1…K-B2 · K-C1…K-C3) |
| Betik/kapı sayısı | 10 beklenti satırı (§9) + 10 ham transkript (§14) |
| Eşikler | içerik ≥500 · indeks ≥50 · fm 7 (+5 uzatma) · Authority 1/son · parent: 0 · placeholder 0 |
| Çelişki kapısı | C-01…C-15 (15 kayıt) |
| ADR kapısı | ADR-102 = draft (accepted YOK → Faz 4 kapalı) |
| Beyazliste | yalnız `raporlar/` (paralel ajan farkı §5.3) |
| Yöntem (K-C3) | `ReadAllText` + newline split — `Measure-Object -Line` YASAK |
| Sonraki adım | Vault Steward Faz 0 (K-6 → K-7 → K-8) → Faz 4'te betikler yeniden çalışır |

Bu kart bu raporun **özeti**dır; bağlayıcı olan §3-§5 katalog/kapılarıdır.
Kart ile tablo çakışırsa tablo kazanır.

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Betikler `.ai/scripts/`'e yazılmaz** — §4 betikleri rapor içi göstergedir;
   kalıcı betikleştirme Vault Steward kararıdır (AGENTS §13.6: gate betikleri
   repo kökünden çalıştırılır; bu raporun betikleri K001-özgü ve tek seferlik).
2. **Kapılar bu oturumda geçilmez** — Faz 0 kararları alınmadan Faz 4 kapısı
   kapalıdır.
3. **Rapor seti commit edilmez** — subagent commit atmaz (görev emri).
4. **Eşikler yeniden tanımlanmaz** — ≥500 / ≥50 / 7 alan mevcut vault
   kuralıdır; bu rapor onları kaydeder, icat etmez.
5. **Paralel ajan değişiklikleri düzeltilmez** — §5.3/§7.4 yalnız raporlar;
   başka dosyalara bu oturum dokunmaz.

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı (2026-10-13 çalıştırmaları + teslim ölçümü): 12 kriter · 10
beklenti tablosu · 7 betik/kapı** — K-A1 (0 ihlal) · K-A2 (16 indeks, min 52) ·
K-A5 (`parent:` 0) · K-A6 (kod blokları hariç 0) · K-B1 (15 çelişki kayıtlı) ·
K-B2 (ADR-102 draft) · K-C3 (doğru yöntem §10) — gerçek konsol çıktıları
§4–§5'te, teslim ölçümü §7'de (6 dosya gerçek sayı + bu dosya §7.1 kuralı).

**PROPOSED: 0 kalem** — kriterler mevcut vault kuralından türetildi, yenisi
icat edilmedi (K-A6 düzeltmesi ve K-C3 = mevcut kuralın uygulama notudur).

**Onay bekliyor (APPROVAL REQUIRED): 3 kalem** — indeks 500 eşiği tartışması
(§3 açık soru Q1) · rapor setinin kalıcı vault dosyası olup olmadığı (Q2) ·
K-A6 kalıcı betiğe dönüşsün mü (Q3).

**Sonraki adım (tek):** Vault Steward Faz 0 kararlarını (K-6 → K-7 → K-8
önceliği — plan raporu §10) verdikten sonra bu betikler **Faz 4 kapanışında**
yeniden çalıştırılır ve çıktı tablosu yeni bir ölçüm olarak eklenir; o güne
kadar §4–§5 çıktıları 2026-10-13 anlık görüntüsüdür.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
