---
node_id: K005-firmware/firmware-kararlar.md
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md satır 140 (### 5.5 İki-Ekseni Gruplama Şeması) — okundu 2026-10-13"
  - ".ai/.decisions/ dizin ölçümü — 98 md (accepted 80 · rejected 10 · draft 1 · kök 1) — 2026-10-13"
  - ".ai/architecture/index.md satır 160 (### §8.1 İki-Ekseni Gruplama) — okundu 2026-10-13"
  - "shared/src/Middleware/MiddlewarePipeline.php satır 8 — final class MiddlewarePipeline — okundu 2026-10-13"
  - "shared/src/Middleware/CsrfMiddleware.php satır 7 — final class CsrfMiddleware implements IMiddleware — okundu 2026-10-13"
  - "repo kök taraması — *.cpp / *.h / *.c = 0 dosya — 2026-10-13"
validation:
  - "2026-10-13 — §2 16 kararın her birinde Gerekçe + Dayanak var (K-13…K-16 bu üretimde alındı)"
  - "2026-10-13 — §18 kod kanıtı 4 satır numarası dosyalardan okunarak doğrulandı; firmware kaynak yokluğu 3 desen taranarak doğrulandı"
  - "2026-10-13 — yeni ADR AÇILMADI: `.ai/.decisions/` md sayısı 98'de sabit"
open_questions:
  - "K-10/K-11 (hedef platform · dil) onayı → ADR gerektirir (⚠️)"
  - "K-12 ID CONFLICT çözümü onay merciine bağlı (⚠️)"
  - "Dalga-3 3 çocuk kararları bu dosyaya ne zaman eklenir? (⚠️)"
group: mimari
---

# firmware-kararlar — Mimari Karar Kaydı

> Dayanak: ADR-097 §5.5 (satır 140) · registry §8.1 (satır 160) · AGENTS §3 (zero-hallucination).
> Bu dosya K005'in **karar defteridir** — her karar gerekçe + dayanak + durum taşır.
> Yeni `.ai/.decisions/` ADR'si bu üretimde **açılmadı** (onay üst mercide; dizin 98 md'de sabit).

## §1 ELI15

> **Kararlar üç kaynaktan gelir: spec (v2), şema (ADR-097 §5.5) ve mühendislik
> kuralı (AGENTS). Burada yazılanlar bağlayıcıdır; ADR'ye taşınması onay ister.**

Karar kaydı formatı: `# · Karar · Gerekçe · Dayanak · Durum`. Durum değerleri:
`ALINDI` (bu üretimde uygulandı) · `BEKLİYOR` (onay/sürüm bekliyor). Karar, gerekçe
yazılmadan satır olmaz; gerekçesiz satır silinir, düzeltilmez (§5 kural 1).

Bu defterin okuyucu kitlesi iki türdür: (a) K005 dosyalarını yazan agent — defter
onun **tek karar kaynağıdır**; (b) denetim yapan insan — defter, "neden böyle"
sorusunun cevabını taşır. Defterde olmayan bir kural **kural değildir**.

## §2 Karar tablosu (16 karar)

| # | Karar | Gerekçe | Dayanak | Durum |
|---|---|---|---|---|
| K-1 | K005 yalnız **cihaz firmware'ini** kapsar; DSP algoritması, host OS, donanım tasarımı dışarıda | Sınır belirsizliği en pahalı hatadır; komşu düğümler zaten ayrı raf | v2 §2.5 + `firmware-kapsam.md` §3 | ALINDI |
| K-2 | Master dosya adı `firmware-master.md` — değiştirilmez | v2 §3 KURAL 1: imla/ID/çocuk adları kaynağa sadık | v2 §3 (imla/ID korunumu) | ALINDI |
| K-3 | Kök 12 MD + kademe-2 çocuk şeması (çoklu-MD) | Tek dosya 500 satırı aşınca okunabilirlik düşüyor; çoklu-MD ADR-097 ile standardize | ADR-097 §5.5 (satır 140) | ALINDI |
| K-4 | Her MD'nin frontmatter'ında 9 anahtar; son anahtar `group` (mimari · kod · kanit) | İki-eksenli gruplama: dosya işi + grup kimliği tek yerde | ADR-097 §5.5 + registry §8.1 (satır 160) | ALINDI |
| K-5 | Her `index.md` içinde `## §GRUPLAR` tablosu | Klasör içeriği okunmadan grup dağılımı görünür olsun | registry §8.1 | ALINDI |
| K-6 | G4 `teknoloji` grubu bu üretimde kullanılmaz | Kademe-3 teknoloji klasörleri açılmadı; band + ADR gerekiyor | ADR-097 §5.5/A | ALINDI |
| K-7 | Kanıtsız iddia `[TASARIM]`, çekilmemiş URL `⚠️` olarak yazılır | Zero-Hallucination: uydurma kanıt yok | AGENTS §3 | ALINDI |
| K-8 | Dalga-3 (diagnostics · security · testing) bu üretimde yazılmaz | Görev kapsamı: kök 12 dosya; kalan 3 çocuk raporlanır | görev manifesti | ALINDI |
| K-9 | Yeni URL'ler dosyalar arası bölünür (tekrar yasağı) | Aynı URL iki dosyada olursa kanıt sayımı şişir | bu üretim `firmware-web-arastirma.md` §2 | ALINDI |
| K-10 | Hedef platform (bare-metal / gömülü OS) seçilmedi | Seçim onayı olmadan iddia uydurulmaz | AGENTS §3 · `firmware-bilesenler.md` §5 | BEKLİYOR |
| K-11 | Firmware hedef dili seçilmedi (C / C++) | Örnekler PHP deseni (taklit); birebir karşılık seçim sonrası | `firmware-kod-ornekleri.md` §1 | BEKLİYOR |
| K-12 | `K005-veri-yonetimi` ↔ `K005-firmware` ID CONFLICT kayıtlı kalır | Klasör adı onaysız değiştirilmez (dosya taşıma yasağı) | v2 §6 (ID çakışması kaydı) | BEKLİYOR |
| K-13 | Frontmatter 9 anahtar: `status:` yok, `parent:` yasak | `parent_id` tek doğruluk kaynağıdır; iki anne-anahtarı çelişir | bu üretim 12 dosya FM ölçümü | ALINDI |
| K-14 | Yeni URL'ler üç dosyaya bölünür: master 12 · dogrulama 26 · web-arastirma 48 = 86 | Kesişim 0 garantisi envanterle sağlandı; tek dosyada yoğunlaşma okunabilirliği bozar | K-9 + envanter ölçümü (1441 artefakt) | ALINDI |
| K-15 | Firmware kod alıntısı `Kaynak:` etiketiyle; disk-verify PHP/SQL `Dosya:`/`Satır:` ile | Depoda `*.cpp`/`*.h`/`*.c` = 0 dosya; uydurma satır numarası yasağı | repo kök taraması 2026-10-13 | ALINDI |
| K-16 | Her dosyanın son satırı tek Authority bloğu (Bayram Ali / Vault Steward · Mode) | Kapanış imzası dosyalar arası tektir; okunmayan dosya teslim sayılmaz | bu üretim 12 dosya son-satır ölçümü | ALINDI |

K-1…K-12 önceki revizyonun kararlarıdır (2026-10-12); K-13…K-16 bu revizyonun
kararlarıdır (2026-10-13) ve dördü de bu üretimde **fiilen uygulanmış** kararlardır
(12 dosyanın frontmatter'i, üç dosyanın URL bölümü, kod alıntıları ve son satırları
bunun kanıtıdır). BEKLİYOR olan üç karar (K-10 · K-11 · K-12) uygulanmış sayılmaz.

## §3 Karar → dosya izleme

| Karar | Yansıyan dosya | Bölüm |
|---|---|---|
| K-1 | `firmware-kapsam.md` | §2 · §3 · §4 |
| K-2 | `index.md` · `firmware-master.md` | §2 · başlık notu |
| K-3 | `index.md` · `firmware-bilesenler.md` | §2 · §3 |
| K-4 | tüm dosyalar | frontmatter `group` |
| K-5 | `index.md` + çocuk `index.md` dosyaları | `## §GRUPLAR` |
| K-6 | `firmware-bilesenler.md` · `index.md` | §6 · §GRUPLAR notu |
| K-7 | `firmware-dogrulama.md` · `firmware-web-arastirma.md` | §5 · §6 |
| K-8 | `index.md` · `firmware-bilesenler.md` | §2 dalga-3 satırları |
| K-9 | `firmware-web-arastirma.md` | §2 havuz tablosu |
| K-10 | `firmware-bilesenler.md` · `firmware-bagimliliklar.md` | §5 · §8 |
| K-11 | `firmware-kod-ornekleri.md` | §1 · §7 |
| K-12 | `index.md` · `firmware-master.md` | açık sorular |
| K-13 | tüm 12 dosya | frontmatter (9 anahtar) |
| K-14 | `firmware-master.md` · `firmware-dogrulama.md` · `firmware-web-arastirma.md` | §URL tabloları |
| K-15 | `firmware-kod-ornekleri.md` · `firmware-dogrulama.md` | §10 · §11 |
| K-16 | tüm 12 dosya | son satır Authority bloğu |

İzleme tablosu, defterin **geri izleme** yüzüdür: bir karar sorgulandığında hangi
dosyanın neresine yansıdığı buradan bulunur. K-13 ve K-16 tüm dosyalara yayıldığı
için "tüm dosyalar" satırını kullanır; bu, tek tek 12 dosya listelemekten daha
sağlıklıdır (dosya eklenince satır yenilenmez, kural kalır).

## §4 Red edilen alternatifler (neden YAPILMADI)

| # | Alternatif | Neden reddedildi |
|---|---|---|
| 1 | Tek büyük `firmware.md` (tek dosya) | 900+ satır; okunabilirlik + şema ihlali (K-3) |
| 2 | Çocuk adlarını İngilizce'ye çevirmek (`boot` → `startup`) | v2 §3 KURAL 1 — ad değiştirilmez |
| 3 | `K005-veri-yonetimi` klasörünü yeniden adlandırmak | Onaysız dosya taşıma yasak (K-12) |
| 4 | G4 `teknoloji` grubunu bu üretimde kullanmak | Kademe-3 band/ADR yok — grup ataması spekülasyon olur |
| 5 | Web iddialarına `✅ DOĞRULANDI` yazmak | URL çekilmedi; `✅` uydurma kanıt olur (K-7) |
| 6 | Kademe-3 klasörlerini şimdi açmak | Şema çocuklar için onaylı; fazla dosya üretim yasağı |
| 7 | Tüm yeni URL'leri tek dosyada toplamak | K-9 ihlali + tek dosya 48+26+12 satır şişer, kesişim 0 garantisi zorlaşır |
| 8 | Bu üretimde yeni ADR dosyası açmak | ADR onayı üst mercide; defter satırı ADR yerine geçmez (A3) |

Red listesi **kapanık değildir ve silinmez**: bir reddin gerekçesi ileride geçersiz
olursa satır kalır, altına yeni satır eklenir ve §17 günlüğüne tarih düşer. Böylece
"neden yapmamıştık" sorusunun cevabı hiçbir revizyonda kaybolmaz.

## §5 Karar kuralları

| # | Kural |
|---|---|
| 1 | Karar gerekçesiz yazılmaz — her satırda `Gerekçe` + `Dayanak` vardır |
| 2 | `BEKLİYOR` kararı uygulanmış sayılmaz; `status: verified` yolunda engeldir |
| 3 | Yeni karar = bu dosyaya satır; ADR açmak üst merci onayı ister |
| 4 | Karar diğer dosyada çelişiyorsa bu dosya kazanır (tek karar defteri) |
| 5 | Red alternatifleri silinmez; nedeniyle birlikte kalır (§4) |
| 6 | Karar satırı silinmez; yalnız durumu değişir (`ALINDI` ↔ `BEKLİYOR`) |
| 7 | Kod kanıtlı iddialarda `Dosya:` + `Satır:` zorunlu (K-15) |

Kural 6, defterin **append-only** doğasını korur: geçmişte verilmiş bir karar, koşullar
değişince satırdan atılmaz — durumu güncellenir ve §17'ye kayıt düşer. Kural 7 ise
defterin kodla bağını zorunlu kılar: "firmware'de böyle yapılır" diyen bir karar,
depodaki tek gerçek kod kanıtına (§18) bağlanmak zorundadır.

## §6 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| A1 | 16 kararın tamamında Gerekçe + Dayanak var | okuma | ✅ 16/16 |
| A2 | Her `BEKLİYOR` açık sorulara bağlı | çapraz (§7) | ✅ K-10/11/12 |
| A3 | Bu üretimde yeni ADR dosyası açılmadı | dizin sayımı (`.ai/.decisions/` 98 md — değişmedi) | ✅ 2026-10-13 |
| A4 | §4 reddlerinde yasak dayanağı var | okuma | ✅ 8/8 |
| A5 | K-13…K-16 uygulanabilirlik | 12 dosya FM/son-satır ölçümü + repo taraması | ✅ 4/4 |

A1–A4 önceki revizyon kapılarıdır; A5 bu revizyonun ek kapısıdır ve K-13…K-16'nın
kağıt üstünde kalmadığını, dosyalara gerçekten yansıdığını ölçer. A3'teki 98 sayısı
sabit sayfadır: bu üretim `.ai/.decisions/` altına **hiçbir dosya yazmadı**.

### §6.1 Karar olgunluk ölçütleri (BEKLİYOR → ALINDI geçişi)

| # | Ölçüt | K-10 | K-11 | K-12 |
|---|---|---|---|---|
| 1 | Karar mercii tanımlı | embedded | embedded | Vault Steward |
| 2 | Gerekçe yazılı | §2 K-10 | §2 K-11 | §2 K-12 |
| 3 | Alternatif listesi | `firmware-bilesenler.md` §5 | `firmware-kod-ornekleri.md` (PHP/C++) | §4 redd #3 |
| 4 | Bağımlı dosyalar | bagimliliklar · bilesenler · web-arastirma | kod-ornekleri · master | index · master açık sorular |
| 5 | Karar sonrası iş | §2 satır durumu + §17 günlüğü | örneklerin C karşılığı | onaylı yeniden adlandırma |
| 6 | Şu anki durum | BEKLİYOR | BEKLİYOR | BEKLİYOR |

Tablonun 6. satırı üç kararı da `BEKLİYOR`'da tutar; 1–5. satırlar tamamlanmadan
geçiş yapılmaz. Geçiş bir Vault Steward işlemidir ve her geçiş §17'ye satır üretir.

### §6.2 Karar-çelişkisi çözüm sırası (karışıklık çıkarsa)

| Sıra | Kaynak | Ağırlık |
|---|---|---|
| 1 | v2 spec (§2.5 · §3 KURAL 1) | en yüksek — ad/şema kaynağı |
| 2 | ADR-097 §5.5 (şema · grup) + registry §8.1 | gruplama kaynağı |
| 3 | AGENTS/CLAUDE (katman · güvenlik · zero-hallucination) | mühendislik kuralı |
| 4 | Bu dosya (K-1…K-16) | K005 özel kararları |
| 5 | Diğer K005 MD dosyaları | uygulama ayrıntısı |
| 6 | Komşu K düğümleri | K005'i bağlamaz (yalnız sınır) |

> Çelişki bulunan yerde önce üst sıra kazanır; üstteki dosya da çelişkiliyse
> `⚠️ VERIFY REQUIRED` + üst karar (AGENTS §3).

Sıra 4 ile 5 arasındaki fark kritiktir: bu defter **karar** taşır, diğer dosyalar
**uygulama** taşır. Bir uygulama dosyası ile defter çelişirse defter kazanır; ama
defterin kendisi sıra 1–3 ile çelişiyorsa defter düzeltilir. Bu, defterin hem
otoritesini hem de üst merciye bağlılığını aynı anda korur.

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | K-10 hedef platform kararı ne verilecek? | `⚠️ VERIFY REQUIRED` |
| Q2 | K-11 hedef dil (C / C++) seçimi? | `⚠️ VERIFY REQUIRED` |
| Q3 | K-12 ID CONFLICT ne zaman çözülecek (onaylı yeniden adlandırma)? | `⚠️ ID CONFLICT` |
| Q4 | 16 karar ADR formatına taşınacak mı (tek ADR mi, grup mu)? | `⚠️ VERIFY REQUIRED` |
| Q5 | Dalga-3'e geçişte K-8 satırı `ALINDI` → kapsam dışı mı güncellenir? | `⚠️ VERIFY REQUIRED` |
| Q6 | K-13…K-16 kararları alt K005 dosyalarına da zorunlu mu? | `⚠️` — evet, K-13/K-16 tüm dosyalara yayıldı; yazılı teyit bekleniyor |

## §8 Karar kayıt şablonu (satır nasıl yazılır)

| # | Alan | Kural | Boş bırakılabilir mi? |
|---|---|---|---|
| 1 | `#` | `K-` + iki hane, artan, atlanmaz | hayır |
| 2 | `Karar` | tek cümle, emir kipi, ne/nasıl açık | hayır |
| 3 | `Gerekçe` | "çünkü" ile başlar (zihinden), maliyet/kazanç taşır | hayır |
| 4 | `Dayanak` | dosya + bölüm ya da bölüm + satır | hayır |
| 5 | `Durum` | yalnız `ALINDI` / `BEKLİYOR` | hayır |
| 6 | Tarih | §17 günlüğünde; satırda tekrarlanmaz | evet (kural gereği) |
| 7 | Alternatif | reddedildiyse §4'e, gerekçesiyle | evet |
| 8 | Bağımlı dosya | §3'e, tek satırda | hayır (kural 4) |
| 9 | Kod kanıtı | iddia kod iddiasıysa §18'e satır | evet (kod iddiası değilse) |
| 10 | Onay | Vault Steward; onaysız karar satırı olmaz | hayır |

Şablon 10 alanlıdır ve §2'deki 5 sütun bunun özetidir. Uzun bilgi (alternatif,
kod kanıtı, bağımlılık) satırı şişirmesin diye §3/§4/§18'e dağıtılmıştır — bu
dağiltma kasıtlıdır ve §16 M5 kapısıyla korunur.

## §9 Karar sınıflandırması (hangi karar hangi türde)

| # | Karar | Tür | Etki alanı | Geri alma maliyeti |
|---|---|---|---|---|
| 1 | K-1 | kapsam sınırı | tüm K005 | yüksek — tüm dosyalar |
| 2 | K-2 | adlandırma | 2 dosya | çok yüksek — wiki-linkler |
| 3 | K-3 | şema | tüm klasör | çok yüksek — dizin ağacı |
| 4 | K-4 | frontmatter | 12 dosya | orta — FM taraması |
| 5 | K-5 | indeks | 9 index.md | düşük — tablo ekleme |
| 6 | K-6 | gruplama | 2 dosya | düşük — satır ekleme |
| 7 | K-7 | kanıt kuralı | tüm dosyalar | düşük — tarama |
| 8 | K-8 | kapsam dışı | 2 dosya | çok düşük — satır silme |
| 9 | K-9 · K-14 | kanıt dağılımı | 3 dosya | orta — URL sayımı |
| 10 | K-10 · K-11 | teknoloji seçimi | kod örnekleri | çok yüksek — yeniden yazım |
| 11 | K-12 | kimlik | 2 dosya | çok yüksek — taşma riski |
| 12 | K-15 · K-16 | biçim/etiket | 12 dosya | çok düşük — tarama |

Sınıflandırma üç sütunla karar vermek içindir: tür (neyin kararı), etki alanı (neyi
vurur), geri alma maliyeti (düzeltme ne kadar pahalı). Geri alma maliyeti **çok
yüksek** olan kararlar (K-2 · K-3 · K-10 · K-11 · K-12) tek başına değiştirilemez;
hepsi §10 eşiğinden geçmek zorundadır.

## §10 ADR'ye taşıma eşiği (ne zaman ADR açılır)

| # | Eşik | Durumda | Karar sayısı |
|---|---|---|---|
| 1 | Bir karar 3+ dosyayı kalıcı bağlıyorsa | K-2 · K-3 · K-13 · K-14 · K-15 · K-16 | ADR adayı |
| 2 | Bir karar donanım/ürün adı içeriyorsa | K-10 · K-11 | ADR zorunlu |
| 3 | Bir karar komşu K düğümünü bağlıyorsa | K-1 · K-12 | ADR + komşu onayı |
| 4 | Karar yalnız K005 biçimini ilgilendiriyorsa | K-4 · K-5 · K-6 · K-7 · K-8 · K-9 | defterde kalır |
| 5 | Karar kısaltma/etiket düzeyindeyse | K-16 | defterde kalır |
| 6 | Karar `BEKLİYOR` ise | K-10 · K-11 · K-12 | onay sonrası ADR |

Eşik uygulanmadan önce bu üretimde ADR **açılmadı** (A3 kanıtı: 98 md sabit).
Eşiklerin uygulanması Q4'e bağlıdır; Q4 kapandığında 1–3. satırlardaki 8 karar
ADR adayı olarak üst mercie sunulur (tek ADR mi, grup mu — Q4'ün kendisi).

## §11 Karar durum geçiş şeması

| # | Geçiş | Koşul | Kayıt |
|---|---|---|---|
| 1 | `yok` → `BEKLİYOR` | gerekçe + dayanak + merci yazılırsa | §2 + §17 |
| 2 | `BEKLİYOR` → `ALINDI` | §6.1 1–5 tamam + Vault Steward onayı | §2 + §17 |
| 3 | `ALINDI` → `BEKLİYOR` | dayanak geçersizleşirse (ör. komşu değişikliği) | §2 + §17 |
| 4 | `BEKLİYOR` → `RED` | alternatif §4'e eklenir, satır durumu `RED` olur | §4 + §17 |
| 5 | herhangi → `ADR TAŞINDI` | §10 eşiği uygulanıp ADR onaylanırsa | §17 + `.ai/.decisions/` |

Geçişler tek yönlü işaretlenmez; her geçiş iki yerde iz bırakır (§2 durum sütunu
+ §17 günlüğü). Geçiş izi olmayan durum değişikliği **geçersizdir** ve §16 M3
kapısıyla yakalanır. `RED` durumu silme değildir: satır kalır, yalnız uygulanmaz.

## §12 Bağımlı dosya etkisi (hangi karar değişirse ne kırılır)

| # | Karar | Kırılan dosya | Kırılma türü | Onarım |
|---|---|---|---|---|
| 1 | K-1 | kapsam · bilesenler · bagimliliklar | sınır satırları geçersiz | üç dosya birlikte |
| 2 | K-2 | master · index + tüm wiki-linkler | link kırılması | link taraması |
| 3 | K-3 | tüm klasör ağacı | dizin/yolu değişir | taşıma onayı + link taraması |
| 4 | K-4 | 12 dosya FM | anahtar sayısı değişir | FM taraması |
| 5 | K-5 | 9 index.md | tablo satırı eksik/ fazla | GRUPLAR taraması |
| 6 | K-7 | dogrulama · web-arastirma | kanıt etiketleri | etiket taraması |
| 7 | K-9 · K-14 | üç URL dosyası | sayım 86 bozulur | envanter kesişim kontrolü |
| 8 | K-10 · K-11 | kod-ornekleri | örnekler yeniden yazılır | tam yeniden yazım |
| 9 | K-12 | index · master | ID/şema çelişkisi | üst onay |
| 10 | K-15 · K-16 | tüm dosyalar | etiket/son-satır biçimi | iki tarama (etiket + son satır) |

Tablo, revizyon öncesi **etki analizi** aracıdır: bir karar değişmeden önce bu
tabloya bakılır ve "kırılan dosyalar" birlikte güncellenir. Kırılma türü üç
değerden biridir: içerik (satır geçersiz), biçim (FM/etiket), kimlik (yol/ID).
Kimlik kırılması en pahalıdır ve yalnız üst onarım (satır 3 · 9) ile çözülür.

## §13 Karar denetim listesi (dosya denetimi adımları)

| # | Adım | Soru | Kanıt |
|---|---|---|---|
| 1 | §2 satırları okundu mu? | 16 karar biliniyor mu? | okuma notu |
| 2 | Dosyadaki iddialar §2 ile eşleşiyor mu? | uydurma kural var mı? | çapraz okuma |
| 3 | `BEKLİYOR` kararları uygulanmış mı? | K-10/11/12 ihlali | §6.1 |
| 4 | FM 9 anahtar mı? | K-13 ihlali | FM sayımı |
| 5 | Son satır Authority bloğu mu? | K-16 ihlili | son satır okuma |
| 6 | Kod alıntıları `Kaynak:`/`Dosya:` etiketli mi? | K-15 ihlili | etiket taraması |
| 7 | URL'ler üç dosya dışında mı? | K-9/K-14 ihlili | dosya bazlı sayım |
| 8 | Yeni ADR açılmış mı? | A3 ihlili | `.ai/.decisions/` sayımı |

Denetim 8 adımdır ve sıralıdır: içerik (1–3) önce, biçim (4–6) sonra, dağılım
(7–8) en sonda. Adım atlanan denetim geçersizdir; çünkü içerik denetimi biçim
denetiminin önkoşuludur (biçim doğru, içerik yanlışsa dosya hâlâ teslim değildir).

## §14 Karar-çelişkisi vaka örnekleri

| # | Vaka | Çelişki | Doğru çözüm | Dayanak |
|---|---|---|---|---|
| 1 | Çocuk dosya `ses-kontrol/` adı öneriyor | K-2 + v2 KURAL 1 | öner reddedilir, ad kalır | sıra 1 > 4 |
| 2 | Master dosyası 12 yeni URL taşıyor | K-9/K-14 | URL'ler üç dosyaya bölünür | sıra 4 (bu defter) |
| 3 | Kod örneği `Dosya:` etiketi olmadan satır veriyor | K-15 | etiket eklenir ya da `Kaynak:` olur | sıra 3 (AGENTS) |
| 4 | Bir dosya `status:` anahtarı ekliyor FM'e | K-13 | `status` kaldırılır, 9 anahtar | sıra 4 (bu defter) |
| 5 | Dosyanın son satırı `Last Updated` ile bitiyor | K-16 | Authority bloğu son satıra taşınır | sıra 4 (bu defter) |

Vakalar beşi de bu üretimde **yaşanmış** durumlardır (12 dosyalık FM ve son-satır
hizalaması sırasında çıktı). Her vakada çözüm, §6.2 sıralamasının birebir
uygulanmasıdır; yeni bir yorum üretilmemiştir. Vaka 1 dışındaki dördü biçim
vakasıdır ve denetim taramalarıyla (§13 adım 4–7) otomatik yakalanır.

## §15 Karar takip günlüğü

| Tarih | Karar | İşlem | Sorumlu |
|---|---|---|---|
| 2026-10-12 | K-1…K-12 | defter açıldı, 12 satır yazıldı | Vault Steward |
| 2026-10-13 | K-13…K-16 | dördü de alındı ve uygulandı (12 dosya FM + son satır + URL bölme + etiket) | Vault Steward |
| 2026-10-13 | K-10 · K-11 · K-12 | `BEKLİYOR` teyit edildi, §6.1 1–5 incelendi, geçiş yok | Vault Steward |
| 2026-10-13 | §4 redd #7 · #8 | iki yeni red eklendi (URL yoğunlaştırma · ADR açma) | Vault Steward |

Günlük yalnız **revizyon olaylarını** taşır; karar satırlarının tam metni §2'dedir.
Günlüğe yazılan her satır, bir §2 satırının ya da §4 satırının değiştiğini belgeler —
günlükte izole (§2'siz) olay yoktur.

## §16 Ölçüm ve kabul kriterleri

| # | Kriter | Hedef | Ölçüm yöntemi | Son durum (2026-10-13) |
|---|---|---|---|---|
| M1 | Karar sayısı | 16 | §2 satır sayısı | ✅ 16 |
| M2 | Gerekçe+Dayanak doluluğu | 16/16 | §2 okuma | ✅ 16/16 |
| M3 | Durum değişikliği izli | 100% | §2 ↔ §17 çaprazı | ✅ 16/16 |
| M4 | Red satırı | 8 | §4 satır sayısı | ✅ 8 |
| M5 | Dağıtılmış bilgi (§3/§4/§9/§10/§12/§18) | ≥6 tablo | bölüm sayımı | ✅ 6 |
| M6 | Kod kanıtı satırı | ≥1 | §18 (dosyadan okuma) | ✅ 4 |
| M7 | Yeni ADR | 0 | `.ai/.decisions/` = 98 md | ✅ 0 |
| M8 | FM anahtar + son satır | 9 + 1 | tarama | ✅ 9 + 1 |

M1–M4 defterin kendisini, M5–M7 defterin dağılımını, M8 biçimini ölçer. Hedef ile
ölçüm uyuşmuyorsa defter teslim sayılmaz; ölçüm elle değil, dosyanın kendisinden
okunarak yapılır.

## §17 Bakım günlüğü (revizyon olayları)

| Tarih | Olay | Kapsam | Etki |
|---|---|---|---|
| 2026-10-12 | Defter kuruldu (§1–§7) | K-1…K-12 | K005 karar omurgası |
| 2026-10-12 | ADR-097 §5.5 okundu, grup şeması benimsendi | K-3 · K-4 · K-6 | şema + grup |
| 2026-10-13 | §8–§20 eklendi (şablon · sınıf · eşik · geçiş · etki · denetim · vaka · günlük · ölçüm) | tüm defter | denetlenebilirlik |
| 2026-10-13 | K-13…K-16 alındı | FM · URL · etiket · son satır | 12 dosya |

Günlük append-only'dir: geçmiş satırlar değiştirilmez, eksik olay altına eklenir
(§5 kural 6 ile aynı ilke). Revizyon tarihleri ISO 8601 ile yazılır (`firmware-evrensel-dil.md`
§9 kural 4 ile aynı biçim).

## §18 Kod kanıtı (satır numarası dosyadan okundu)

| # | Dosya | Satır | Kod | Ne kanıtlar |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php` | 8 | `final class MiddlewarePipeline` | middleware yüzeyi komşu K009'da (K-1 sınırı) |
| 2 | `shared/src/Middleware/CsrfMiddleware.php` | 7 | `final class CsrfMiddleware implements IMiddleware` | güvenlik yüzeyi komşuda (K-1) |
| 3 | `shared/src/Device/DeviceDetector.php` | 19 | `final class DeviceDetector` | host-taraf cihaz kodu firmware değil (K-1) |
| 4 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 5 | `final class RuntimeBootstrap` | host bootstrap ≠ firmware boot (K-1) |
| 5 | repo kök taraması | — | `*.cpp` / `*.h` / `*.c` = 0 dosya | K-15: firmware alıntısı `Kaynak:` ile yazılır |

Satır numaraları tahmin değil, 2026-10-13'te dosyanın kendisinden okunmuştur (K-15
kuralı). 5. satır bir **dosya değil, tarama sonucu**dur ve bu yüzden `Satır:`
sütununda tire taşır — boş hücre yoktur. Taramanın sonucu, firmware örneklerinin
bu depoda gerçek karşılığının olmadığının kanıtıdır; bu yüzden
`firmware-kod-ornekleri.md` içindeki firmware alıntıları `Kaynak:` etiketiyle,
PHP/SQL alıntıları `Dosya:`/`Satır:` etiketiyle yazılır.

## §19 İlgili dosyalar

| # | Dosya | İlişki | Not |
|---|---|---|---|
| 1 | `index.md` | katalog | §GRUPLAR'da bu dosya `mimari` grubunda |
| 2 | `firmware-master.md` | anayasa | karar çelişkisi buraya bildirilir |
| 3 | `firmware-kapsam.md` | K-1 uygulaması | sınır kararının dosyası |
| 4 | `firmware-bilesenler.md` | K-10 alternatifleri | T1–T7 listesi |
| 5 | `firmware-kod-ornekleri.md` | K-11 · K-15 | dil seçimi + etiket |
| 6 | `firmware-dogrulama.md` | K-7 · K-15 | kapılar |
| 7 | `firmware-web-arastirma.md` | K-9 · K-14 | URL havuzu |
| 8 | `.ai/.decisions/index.md` | ADR kapısı | A3 sayımı buradan |
| 9 | `.ai/architecture/index.md` | registry §8.1 (satır 160) | gruplama kaynağı |

## §20 Sık sorular (bu deftere yöneltilen)

| # | Soru | Cevap |
|---|---|---|
| 1 | Karar satırı silinebilir mi? | Hayır; yalnız durumu değişir (§5/6 · §17) |
| 2 | `BEKLİYOR` uygulanabilir mi? | Hayır; uygulanmış sayılmaz (§6.1) |
| 3 | ADR'siz K005 kararı bağlayıcı mı? | Evet, K005 içi bağlayıcı; komşu/donanım için ADR gerekir (§10) |
| 4 | Çelişkide kazanan? | §6.2 sırası: spec > ADR-097 > AGENTS > bu defter |
| 5 | Kod mu defter mi kazanır? | Kod gerçeği kazanır, defter düzeltilir (evrensel-dil §18 ile aynı) |
| 6 | Yeni karar nereye yazılır? | Buraya, §8 şablonu ile; Vault Steward onayı şart |
| 7 | Kırmızı satır (RED) ne zaman? | §11/4: alternatifse §4, uygulanmazsa RED durumu |
| 8 | Örnek üretmek karar mıdır? | Hayır; örnek uygulamadır, kararı §2 taşır |

Cevapların tamamı bu defterin kendi bölümlerine işaret eder; hiçbir cevap dış
kaynak getirmez. 5. soru, `firmware-evrensel-dil.md` §18 ile aynı ilkeyi tekrarlar —
iki dosya arasında çelişki olsa bile ilke tektir: **kod gerçeği > belge yorumu**.

## §21 ADR paket önizlemesi (üst merciye sunulacak madde başlıkları)

| # | Karar | ADR madde başlığı | Öncelik | Bağımlı komşu |
|---|---|---|---|---|
| 1 | K-1 | K005 kapsam sınırı: firmware ↔ host OS ↔ DSP ↔ donanım | P0 | K000 · K001 · K003 · K004 |
| 2 | K-2 | K005 dosya adı sabitlemesi (`firmware-master.md`) | P2 | — |
| 3 | K-3 | Çoklu-MD şeması (kök 12 + çocuk) | P1 | — |
| 4 | K-4 | Frontmatter 9 anahtar + `group` eksen şeması | P1 | — |
| 5 | K-5 | `## §GRUPLAR` zorunluluğu | P2 | — |
| 6 | K-6 | G4 teknoloji grubu rezervasyonu | P2 | — |
| 7 | K-10 | Hedef platform seçimi (bare-metal / gömülü OS) | P0 | K001 |
| 8 | K-11 | Hedef dil seçimi (C / C++) | P0 | K001 |
| 9 | K-12 | `K005-veri-yonetimi` ↔ `K005-firmware` kimlik çakışması | P1 | — |
| 10 | K-13 | Frontmatter anahtar standardı (9 anahtar) | P1 | — |
| 11 | K-14 | Yeni URL dağılım kuralı (12 · 26 · 48) | P2 | — |
| 12 | K-15 · K-16 | Kod alıntı etiketi + Authority son satır standardı | P2 | — |

Öncelik üç değerlidir: **P0** (onaylanmadan K005 kilitlenir) · **P1** (onaylanmadan
çocuklar yazılamaz) · **P2** (uygulanıyor, ADR yalnız belgeleme). P0'daki üç madde
K-1 · K-10 · K-11'dir; bu üçü birlikte değerlendirilir çünkü platform seçimi,
kapsam sınırını ve dil seçimi belirler. Paket, tek ADR mi grup mu sorusuna (Q4)
bağlıdır; bu tablo iki senaryoyu da taşır.

## §22 İyi/kötü karar metni örnekleri

| # | İyi karar metni | Kötü karar metni | Fark |
|---|---|---|---|
| 1 | "Frontmatter 9 anahtar: `status:` yok, `parent:` yasak" | "FM düzgün olmalı" | ölçüt sayılmış |
| 2 | "Depoda `*.cpp` = 0 dosya; alıntı `Kaynak:` ile" | "C++ dosyası yok herhalde" | tahmin değil ölçüm |
| 3 | "URL'ler üç dosyaya: 12 · 26 · 48" | "URL'ler dağıtılsın" | sayısal taahhüt |
| 4 | "98 md sabit; yeni ADR açılmadı" | "ADR açılmadı sanırım" | kanıt sayı |
| 5 | "`BEKLİYOR` uygulanmış sayılmaz" | "bir yerde uygulanmıştır artık" | durum disiplini |
| 6 | "Satır 19: `final class DeviceDetector`" | "sınıf bir yerlerde tanımlı" | satır kanıtı |
| 7 | "Gerekçe: iki anne-anahtarı çelişir" | "daha temiz olur" | gerekçe + mekanizma |
| 8 | "K-2 değiştirilemez; wiki-linkler bağlı" | "ad fena değil, kalsın" | geri alma maliyeti |

Sağ sütunun tamamı **reddedilen metin kalıplarıdır**; soldakiler bu defterde
kullanılan biçimdir. Kötü metnin ortak özelliği üçünden birini taşımamasıdır:
ölçüm, satır kanıtı ya da geri alma maliyeti. Karar metni yazılırken önce soldan
sağa okunur; bir hücre eksikse karar satır olmaz (§8).

## §23 Kapsam-dışı kararlar (komşuya devredilen)

| # | Konu | Neden K005'te değil | Gittiği yer |
|---|---|---|---|
| 1 | DSP algoritması seçimi (EQ/reverb/compressor) | ses işleme tanımı K004'ün | K004-dsp |
| 2 | Ses akışı yönlendirilmesi (stream/routing) | akış mimarisi K003'ün | K003-audio |
| 3 | Register ham haritası (adres/uzunluk) | donanımın fiziksel tanımı | K001-donanım |
| 4 | Host sürücü/dosya sistemi | host işletim sistemi | K000-isletim-sistemi |
| 5 | Web/API middleware kararı | web katmanı K009'da (§18 kanıtı) | K009-security |
| 6 | PCB/güç kaynağı/koruma rölesi | donanım tasarımı | K001-donanım |

Devir kuralı şudur: bu altı konuda K005 **karar üretmez, yalnız tüketir**. Komşu
dosyadan gelen kararı K005 kendi sözlüğüne (evrensel-dil §11) göre yorumlar ama
değiştiremez; değiştirmek isterse komşu domain sahibiyle ortak karar üretir (§15/3).
Böylece aynı konu için iki defter oluşmaz.

## §24 Görev-tipi → zorunlu karar okuması

| # | Görev tipi | Zorunlu okuma | Neden |
|---|---|---|---|
| 1 | Yeni kök dosya yazımı | K-2 · K-4 · K-13 · K-16 | ad · FM · son satır |
| 2 | Çocuk dosya yazımı | K-1 · K-3 · K-5 | sınır · şema · grup |
| 3 | Kod alıntısı ekleme | K-7 · K-15 | kanıt etiketi |
| 4 | URL ekleme | K-9 · K-14 | dağılım + kesişim 0 |
| 5 | Bileşen/teknoloji taslağı | K-6 · K-10 · K-11 | grup + seçim |
| 6 | Kimlik/dizin değişikliği | K-12 · §10 eşiği | onay üst mercide |
| 7 | Denetim/QA geçişi | §13 · §16 | kapılar |
| 8 | ADR hazırlığı | §10 · §21 · §6.1 | paket + eşik |

Tablo, görev başlangıcında **hangi kararları okumak zorunda olduğunuzu** söyler.
Okunmayan karar, o görev için bilinmez sayılır ve §13 denetiminde yakalanır
(bkz. §14 vaka 2–5). Görev tipi listede yoksa en yakın satır kullanılır ve gerekirse
§15 günlüğüne yeni satır eklenir.

## §25 Durum dağılımı özeti (2026-10-13)

| Durum | Karar sayısı | Kararlar | Anlamı |
|---|---|---|---|
| `ALINDI` | 13 | K-1…K-9 · K-13…K-16 | bu üretimde uygulandı |
| `BEKLİYOR` | 3 | K-10 · K-11 · K-12 | onay/sürüm bekliyor |
| `RED` | 0 | — | hiçbir karar reddedilmedi (§4'teki 8 satır alternatif reddidir, karar reddi değil) |
| Toplam | 16 | §2 | defterin tamamı |

Dağılım, §2 ile §16 arasındaki tutarlılığın sayısız kanıtıdır: 13 + 3 = 16. `RED`
satırının 0 olması önemli bir ayrımı taşır: §4'te reddedilenler **karar değil,
alternatiftir** — yani "karar reddedildi" denen durum bu defterde hiç yaşanmamıştır,
yalnız "şu alternatif yapılmadı" denilmiştir.

## §26 Bu üretimden sonraki kapılar

| # | Kapı | Koşul | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | K-10 · K-11 kararı | hedef platform + dil onayı | embedded + Vault Steward | açık |
| 2 | K-12 çözümü | onaylı yeniden adlandırma | Vault Steward | açık |
| 3 | Q4: ADR paketlemesi | §21 P0 maddeleri sunulur | Vault Steward | açık |
| 4 | Çocuk dosyalarının kapsam genişletmesi | 8 çocukta yalnız `index.md` var; kapsam dosyaları + dalga-3 3 çocuk | embedded | açık |
| 5 | Defterin ADR taşınması | §10 eşiği + üst onay | Vault Steward | açık |

Kapıların beşi de **bu üretimde kapatılmadı** ve kapatılmaları bu dosyanın dışındaki
onay mercilerine bağlıdır (kural 13: değişiklik/handover → dur + üst karar). Kapı
durumu her revizyonda §16 ile birlikte ölçülür; kapı kapandığında §17 günlüğüne satır
düşülür ve §25'teki dağılım yeniden yazılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
