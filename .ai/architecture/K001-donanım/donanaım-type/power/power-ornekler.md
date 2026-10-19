---
title: power — Örnekler (Repo Kodu, Şablonlar, Çalışma Sayfaları)
type: mimari-ornekler
node_id: K001-donanım/donanaım-type/power/power-ornekler
node_type: architecture-examples
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - "§2 repo-code kanıtı (700 dosya sayımı)"
  - "§4 AC/DC–DC/DC–LDO kayıt şablonları (boş)"
  - "§6 sigorta/koruma çalışma sayfası (boş)"
  - "§8 güç ağacı çalışma sayfası (boş)"
depends_on: K001-donanım
evidence:
  - "kendi repo sayımım (2026-10-13): 700 kaynak dosyada PSU=0 VRM=0 LDO=0 BMS=0 fuse/sigorta=0 power supply=0"
  - "HEAD (kendi çalıştırığım, 2026-10-13): 80plus.org=200 · energystar.gov=200 · iec.ch=202 — kanıt tablosu power-kapsam/power-dogrulama'da"
validation: "⚠️ VERIFICATION REQUIRED — §1 'yok' üzerine kurulu; şablonlar boştur; S1 ölçüm = 0"
open_questions:
  - "VD-02: power tipsiz mi — sayım 0; kasıtlı-yok kararı bekliyor"
  - "şablonlar gerçek cihazda doldurulmadı (P-K01, P-A, P-B, P-C boş)"
  - "güç ağacı şeması (§8) mevcut sistem için çizilmedi"
date: 2026-10-13
updated: 2026-10-13
group: kod
---

# power — Örnekler

> **Kapsam**: power/ için örnek kanıtlar, şablonlar, çalışma sayfaları.
> **Gerçeklik kuralı**: repo'da power'e ait kod **yok** (§1) — bölüm "yok" der, şablonlarla
> boşluğu işaretler. Şablonlar **boştur**: uydurma ölçüm, uydurma marka/model, uydurma voltaj yok.
> HEAD kanıt tablosu (URL'li) `power-kapsam.md §Kaynaklar` ve `power-dogrulama.md §9`'dadır —
> G6 unique URL nedeniyle burada URL **tekrarlanmaz**, yalnız sonuç özetlenir.


---

## §1 Repo-code örnekleri — durum: YOK

| Aranan (kendi sayımım, 2026-10-13) | Sonuç (700 kaynak dosya) |
|---|---|
| `psu` | **0** |
| `vrm` | **0** |
| `ldo` | **0** |
| `bms` | **0** |
| `fuse` / `sigorta` | **0** |
| `power supply` | **0** |
| güç ölçüm scripti (`.cpp`/`.c`/`.ino`) | **0** (3186 dosyada) |

**Sonuç**: power'e ait **üretilmiş kod örneği yok**. Boşluk kaydıdır, başarı değil: ya güç
alt yapısı kod tarafında **kasıtlı olarak yok** (VD-02) ya da henüz yazılmamıştır. Karar
Vault Steward'ın; bu dosya kararı **koymaz**.

---

## §2 Sayım metodu (tekrarlanabilir)

```
Tarih: 2026-10-13 (kendi çalıştırığım)
Kapsam: C:\www\coremusic.net · .ai/, .git/, vendor/, node_modules/ hariç
700 kaynak dosya: *.php *.js *.sql *.css
Aranan terimler: psu · vrm · ldo · bms · fuse · sigorta · "power supply"
Sonuç: 0 · 0 · 0 · 0 · 0 · 0 · 0
```

**Yorum sınırı**: "şu anki çalışma ağacında yok" der; stash/commit edilmemiş değişikliği ve
geçmişi görmez (geçmiş için `git log -S` gerekir — bu oturumda çalıştırılmadı, `UNKNOWN`).

---

## §3 Boş — repo-code kod bloğu

> Repo'da power kodu olmadığı için kopyalanacak örnek **yoktur**. Uydurma kod bloğu yazılmaz;
> yerine "ne kaydı olurdu"nun şablonu §4–§9'dadır.

**Yok**: güç ağacı tanımı yok · PSU telemetri okuması yok · şarj/dşarj kontrol kodu yok ·
sigorta/koruma eşik kaydı yok · sıcaklık izleme yok · verim ölçümü yok.

---

## §4 AC/DC kayıt şablonu (P-T03 — boş)

```text
[AC/DC KAYDI — DOLDURULACAK]
tarih          : 
ölçen          : 
cihaz (ölçenin kendi cihazı — marka/model) : 
giriş koşulu (şebeke V/Hz — ölçüm varsa)    : 
--- çıkış ---
VOUT (V)       : 
ripple (mVpp)  : 
yük koşulu     : 
--- koruma/ortam ---
sıcaklık (°C)  : 
koruma özellik : 
--- KURAL ---
boşluk = — ; 0 yalnız gerçekten sıfır ölçüldüğünde ; uydurma değer yasak
```

---

## §5 DC/DC ve LDO kayıt şablonları (P-T04, P-T05 — boş)

**P-B1 — DC/DC:**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| VIN (V) / VOUT (V) | — | — | — | — |
| Yük akımı (A) | — | — | — | — |
| Verim (%) @ yük | — | — | — | — |
| Anahtarlama frekansı (kHz — ölçüm/okuma) | — | — | — | — |
| Yüzey sıcaklığı (°C) | — | — | — | — |

**P-B2 — LDO:**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| VIN / VOUT (V) | — | — | — | — |
| Dropout (mV) | — | — | — | — |
| Gürültü (mVrms / µVrms) | — | — | — | — |
| Yüzey sıcaklığı (°C) | — | — | — | — |

> Ses yolu LDO'su (P-T05 audio_relevance=YÜKSEK) için gürültü ölçümü **kritik** — ama bu form
> boş; doldurulması ayrı ölçüm oturumu ister (K-P4).

---

## §6 Sigorta/koruma çalışma sayfası (P-T10…P-T13 — boş)

| # | Koruma | Konum (hangi ray/yol) | Tip | Derece/eşik (etiketli kaynak) | Ölçülen tetik (S1) | Durum |
|---|---|---|---|---|---|---|
| 1 | sigorta | — | — | — | — | `UNKNOWN` |
| 2 | aşırı akım (OCP) | — | — | — | — | `UNKNOWN` |
| 3 | aşırı gerilim (OVP) | — | — | — | — | `UNKNOWN` |
| 4 | ESD | — | — | — | — | `UNKNOWN` |
| 5 | ısıl koruma | — | — | — | — | `UNKNOWN` |

**Çalışma kuralı**: "etiketli kaynak" = ölçenin kendi cihazının datasheet'i/markası (S1/S3);
**literatür tipik değeri eşik yerine yazılmaz.** Gerçek tetik testleri kontrollü koşul ister
(bkz. `power-dogrulama §5 P-B` güvenlik notu) — bu sayfa test talimatı **değil**, kayıt sayfasıdır.

---

## §7 Koruma doğrulama formu — özet referans

> Tam form `power-dogrulama.md §5`tedir (P-A, P-B, P-C). Buradaki özet, ornekler dosyasının
> neyi gösterdiğini sınırlar: **form var, dolum yok**.

| Form | Kapı | Durum 2026-10-13 |
|---|---|---|
| PSU/rail ölçümleri (P-K01) | K-P4 | **BOŞ** (S1=0) |
| LDO/VRM çıkış (P-A) | K-P4 | **BOŞ** |
| koruma eşik testleri (P-B) | K-P4 | **BOŞ** |
| batarya/BMS (P-C) | K-P4 | **BOŞ** |

---

## §8 Güç ağacı (power tree) çalışma sayfası (boş)

> Amaç: sistemin enerji akışını tek sayfada modellemek — **tip↔arayüz↔protokol ayrımı korunur**
> (bkz. `power-kapsam §5`). Bu sayfa bilinçli olarak boştur; dolusu uydurma olurdu.

| # | Kaynak (tip) | Arayüz/konektör | Dönüştürücü/regülatör | Yük (alt sistem) | Ölçülen akım (S1) | Kanıt |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — |
| 2 | — | — | — | — | — | — |
| 3 | — | — | — | — | — | — |
| 4 | — | — | — | — | — | — |
| 5 | — | — | — | — | — | — |

**Doldurma kuralı**: her satırda kaynak **tip** (P-T01…P-T13 iddiası) ile arayüz ayrı sütun;
"şu voltajda besliyor" yazan her hücre ya ölçüme (S1) ya etiketli kaynağa (S3) bağlanır.

---

## §9 Batarya/BMS kayıt formu (P-T07, P-T08 — boş)

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Paket/hücre düzeni | — | — | — | — |
| Hücre gerilimi (V) | — | — | — | — |
| Kapasite (Ah) — etiket | — | — | — | — |
| SoC (%) | — | — | — | — |
| Şarj/dşarj akımı (A) | — | — | — | — |
| BMS telemetri (varsa: arayüz/protokol) | — | — | — | — |
| Sıcaklık (°C) | — | — | — | — |

**Not**: sistemde batarya **varsa** doldurulur; yoksa satırlar `—` kalır ve "yok" notu düşülür
(`UNKNOWN` yerine `NOT PRESENT` yazımı Vault Steward kararıdır — bu dosya karar koymaz).

---

## §10 Ölçüm doldurma kuralları (özet)

1. Her sayısal alan: değer + ölçen + tarih + koşul — dördü birlikte; tek başına sayı yok.
2. Ölçülmedi = `—`; `0` yalnız gerçekten sıfır ölçüldüğünde.
3. Etiket/80plus/energystar sınıfı **S4'tür**, formdaki S1 alanına kopyalanmaz.
4. Marka/model yalnız **ölçenin kendi cihazı** ise yazılır.
5. Güvenlik sınırları test formunda işaretli kalır; eşik testleri kontrollü koşul ister.
6. Şablon başlıkları uyarlanabilir; **uydurma dolgu asla**.

---

## §11 HEAD sonuç özeti (URL'siz — kanıt tabloları başka dosyalarda)

| # | Kaynak (ad) | HEAD (2026-10-13, kendi HEAD'ım) | Kanıt tablosu | İçerik |
|---|---|---|---|---|
| 1 | 80plus (kurum) | **200** | `power-kapsam §Kaynaklar` · `power-dogrulama §9` | ⚠️ okunmadı |
| 2 | Energy Star (kurum) | **200** | aynı | ⚠️ okunmadı |
| 3 | IEC (kurum) | **202** | aynı | ⚠️ okunmadı |
| 4 | JEDEC | **403** | aynı | erişilemedi — **kullanılmadı** |
| 5 | analog.com / littelfuse.com / formfactors.org | **ERR** | aynı | erişilemedi — **kullanılmadı** |

> G6 unique URL: URL'lerin tam hali yalnız `power-kapsam.md` ve `power-dogrulama.md`'de geçer;
> bu dosya adla anar (tekrarı önleme). 200/202 = erişildi ≠ içerik doğrulandı.

---

## §12 Kullanım özeti — bu dosya neyi kanıtlar

| Kanıtlanan | Seviye |
|---|---|
| power kodu repo'da yok (700 dosya, 0 eşleşme ×6 terim) | S2 |
| şablonlar hazır; dolum **0** | — |
| 3 kaynak erişilebilir, 4 reddedildi (kullanılmadı) | S4 |
| koruma/güç ağacı/batarya sayfaları boş — işaretli boşluk | S2 |

---

## §13 Tip-bazlı şablon kayıtları (P-T01…P-T13 — hepsi boş)

> Her tip için "bu tip ölçülecekse şu alanlar doldurulur" kartı. **Tahmin yasak; hepsi `—`.**

### Şablon P-T01 · ATX PSU

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Ray 1–3 çıkışları (V) | — | — | — |
| Toplam giriş gücü (W) | — | — | — |
| Verim (%) @ yük | — | — | — |
| Gövde sıcaklığı (°C) | — | — | — |

### Şablon P-T02 · DC adaptör

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| DC çıkışı (V/A — okunan/ölçülen) | — | — | — |
| Ripple (mVpp) | — | — | — |
| Konnektör tipi | — | — | — |
| PD kontratı (varsa, okunan) | — | — | — |

### Şablon P-T03 · AC/DC

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Giriş (V/Hz) | — | — | — |
| Çıkış (V) | — | — | — |
| Ripple (mVpp) | — | — | — |
| Sıcaklık (°C) | — | — | — |

### Şablon P-T04 · DC/DC

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| VIN/VOUT (V) | — | — | — |
| Yük akımı (A) | — | — | — |
| Verim (%) | — | — | — |
| Sıcaklık (°C) | — | — | — |

### Şablon P-T05 · LDO

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| VIN/VOUT (V) | — | — | — |
| Dropout (mV) | — | — | — |
| Gürültü (mVrms) | — | — | — |
| Sıcaklık (°C) | — | — | — |

### Şablon P-T06 · VRM

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Bölge girişi/çıkışı (V) | — | — | — |
| Yük akımı (A) | — | — | — |
| Faz sayısı (okunan) | — | — | — |
| Sıcaklık (°C) | — | — | — |

### Şablon P-T07 · Batarya

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Hücre gerilimi (V) | — | — | — |
| Kapasite (Ah — etiket) | — | — | — |
| SoC (%) | — | — | — |
| Sıcaklık (°C) | — | — | — |

### Şablon P-T08 · BMS

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Telemetri erişimi (varsa) | — | — | — |
| Firmware sürümü | — | — | — |
| Şarj/dşarj akımı (A) | — | — | — |
| Balance davranışı notu | — | — | — |

### Şablon P-T09 · Güç dağıtımı

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Kaynak→yük yolları (şema kanıtı) | — | — | — |
| Toprak düzeni notu | — | — | — |
| Kablo/kesit (okunan) | — | — | — |
| Düşme (mV — ölçüm) | — | — | — |

### Şablon P-T10 · Sigorta

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Konum (hangi ray/yol) | — | — | — |
| Tip/boyut (okunan) | — | — | — |
| Derece (A — okunan) | — | — | — |
| Durum (sağlam/yapısal) | — | — | — |

### Şablon P-T11 · OCP/OVP

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Eşik (okunan/ölçülen) | — | — | — |
| Tetik testi (kontrollü koşul!) | — | — | — |
| Kilitlenme/rapor sinyali | — | — | — |
| Kurtarma davranışı | — | — | — |

### Şablon P-T12 · ESD

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Korunan arayüz(ler) | — | — | — |
| Bileşen tipi (okunan) | — | — | — |
| Test raporu (varsa) | — | — | — |
| Sınır (kV — rapor değeri) | — | — | — |

### Şablon P-T13 · Isıl koruma

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Eşik sıcaklığı (°C) | — | — | — |
| Sensör konumu | — | — | — |
| Fan davranışı notu | — | — | — |
| Kapanış testi (kontrollü koşul!) | — | — | — |

**Kural**: 13 şablon × 4 satır = 52 boş satır — bu dosyanın **kanıt** değildir; doldurulması
ayrı ölçüm oturumu ister (power-dogrulama K-P4). Güvenli olmayan testler (P-T11/P-T13)
**kontrollü koşul** ister — bu kartlar talimat değil, kayıt yeridir. Her şablonda `ölçen` ve
`tarih` sütunları doldurulmadan satır geçerli sayılmaz; doldurulmayan hücre `—` kalır.

---

## §14 Ölçüm ortamı kayıt formu (tüm power ölçümleri için — boş)

| Alan | Değer |
|---|---|
| Oturum adı | — |
| Ölçülen cihaz (ölçenin kendi cihazı) | — |
| Yük koşulu (ne yük bağlandı) | — |
| Ortam sıcaklığı (°C) | — |
| Ölçüm aracı (multimetre/scope/PowerMeter) | — |
| Ölçen kişi | — |
| Tarih | — |
| Ölçüm sayısı (n) | — |
| Güvenlik notu (kontrollü koşul var mı) | — |

**Kural**: form eksikse ölçüm sonuçları `VERIFY REQUIRED` kalır; `0` doldurulmaz.

---

## §15 Sık hatalar (power — bağlamsal uyarı listesi)

| # | Hata | Sonuç | Kaçınma |
|---|---|---|---|
| 1 | 80plus/etiket sınıfını ölçüm gibi yazmak | sahte S1 | etiket S3/S4 olarak ayrılır |
| 2 | taslak/niyeti "koruma çalışıyor" sanmak | doğrulanmamış güvenlik | P-B test kaydı zorunlu |
| 3 | K001'de devre hesabı yapmak | sınır ihlali | §sınır: tasarım K002 |
| 4 | sigorta derecesini tahmin etmek | yanlış koruma kaydı | okunan derece zorunlu |
| 5 | batarya yokken BMS kaydı | hayali envanter | varlık önce doğrulanır |
| 6 | ölçülmediği hâlde `0` yazmak | veri kirliliği | `—` kuralı |
| 7 | anayasa revizesi olmadan `DOCUMENTED` | yetki aşımı | status=proposed disiplini |
| 8 | kontrollü testi talimat sanmak | güvenlik riski | form = kayıt yeri, talimat değil |

---

## §16 İlişkili dosyalar (cross-reference)

| Dosya | İlişki |
|---|---|
| `power/index.md` | bu dosyanın künyesi ve kapı özeti |
| `power/power-kapsam.md` | P-T01…P-T13 tanımları · §0 çatışma · §sınır (K002/ADR-063/064) |
| `power/power-dogrulama.md` | kapılar K-P1…K-P8 · P-K01/P-A/P-B/P-C formları · HEAD tablosu |
| `donanaım-type/index.md` | kademe-3 kataloğu (power satırı, 2026-10-13) |
| `K001-donanım/index.md` | kademe-3 tablosu (power satırı, 2026-10-13) |
| `donanım-anayasa.md` | §2 çatışma kaynağı — **dokunulmaz**, yalnız okundu |
| `donanaım-type/donanaım-type-kapsam.md` | §2/A6, §7/3-T1, §12/M6 çatışma kaynağı — dokunulmaz |
| `audio/audio-ornekler.md` | yapısal şablon (önceki oturum) — dokunulmadı |

---

## §17 Sürüm geçmişi (bu dosya)

> Append-only revizyon kaydı — ilk satır bu oturumun (2026-10-13) yazımıdır.

| # | Tarih | Ne değişti | Değiştiren |
|---|---|---|---|
| 1 | 2026-10-13 | ilk yazım (§1–§17) | Vault Steward (subagent) |

> Gelecek revizyonlar bu tabloya eklenir; silme yasak (append-only).

---

## §18 Doldurma akışı (kavramsal — ölçüm talimatı değil)

1. Ölçüm oturumu açılır → §14 ortam formu doldurulur (koşullarsız ölçüm sayılmaz).
2. İlgili tip şablonu seçilir (§13: P-T01…P-T13) → yalnız o şablonun alanları doldurulur.
3. Koruma eşik testleri (P-T11/P-T13) **kontrollü koşul** gerektirir — güvenlik notu okunur.
4. Güç ağacı sayfası (§8) güncellenir — tip, arayüz ve yük ayrı hücrelerde.
5. Sigorta/koruma sayfası (§6) güncellenir — derece/eşik yalnız okunan değerle yazılır.
6. Her doldurulan satırda ölçen + tarih + koşul bulunur; bulunmayan satır geri alınır.
7. Kapı hesabı: hangi kapıların kapandığı `power-dogrulama §3` tablosundan işaretlenir.
8. **Bu dosyaya uydurma veri girişi yasak** — doldurulmayan `—` kalır.

> Bu akış bir **plan**tır; uygulaması ölçüm oturumuna aittir ve bu dosyada gerçekleşmez.

---

## §19 Terim notları (power — kısa, bağlamsal değil)

> Bu terimler bu dosyada **yalnız kayıt dilini** standartlaştırır; teknik tanım/idddia
>.power-kapsam §3'tedir — burada çoğaltılmaz (SSOT).

| Terim | Bu dosyadaki anlamı |
|---|---|
| ray | PSU'nun ürettiği sabit gerilim hattı (tekil besleme) |
| ripple | DC üzerindeki dalgalanma (mVpp) |
| dropout | regülatörün çalışabilmesi için giriş-çıkış farkı |
| verim | çıkış gücü / giriş gücü (%) |
| eşik | korumanın tetiklendiği ölçülen/okunan değer |
| kontrollü koşul | testin güvenli yapılabildiği sınırlı laboratuvar koşulu |
| güç ağacı | kaynaktan yüklerine enerji akışının tek sayfalık modeli |

---

## §20 Kapanış — bu dosya neyi kanıtlar (özet)

| Kanıtlanan | Seviye |
|---|---|
| power kodu repo'da yok (700 dosya, 0 eşleşme ×6 terim) | S2 |
| şablonlar hazır; dolum **0** | — |
| 3 kaynak erişilebilir, 4 reddedildi (kullanılmadı) | S4 |
| koruma/güç ağacı/batarya sayfaları boş — işaretli boşluk | S2 |
| 13 tip şablonu + ortam formu + akış boş | — |
| git commit | **ATILMADI** (subagent taahhüdü) |

---

## §21 Ölçüm oturum günlüğü (boş)

> Her satır bir fiziksel ölçüm oturumunu temsil eder; oturum yapılmadıkça satır `—` kalır.
> Oturum adı, doldurulan form ve kapı sütunları birlikte doldurulur — tek başına sonuç yazılmaz.

| # | Tarih | Oturum | Kapı | Doldurulan form | Sonuç | Ölçen |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — |
| 2 | — | — | — | — | — | — |
| 3 | — | — | — | — | — | — |
| 4 | — | — | — | — | — | — |
| 5 | — | — | — | — | — | — |

> Oturum sayısı = gerçek yapılan ölçüm sayısı; boş satırlar doldurulmayı bekler; uydurma satır yok.

---

## §22 K001↔K002 sınır kılavuzu (bu dosyaya özgü — ne gönderilir)

> Bu tablo `power-kapsam §sınır`'ın bu dosyadaki uygulama yüzüdür — kural değişmez,
> yalnız hangi kaydın hangi tarafa gittiği burada işaretlenir.

| Bu dosyada KALIR (K001) | K002-electroncis'e GİDER |
|---|---|
| tip/envanter kaydı (P-T01…P-T13) | devre topolojisi ve hesabı |
| arayüz/konektör sınıflandırması | bileşen seçimi ve datasheet hesabı |
| koruma **özellik** envanteri | koruma devresinin tasarımı |
| ölçüm **sonuçlarının** kaydı (kapı) | ölçümün **yöntem tasarımı** (test jig) |
| sınır ihlali raporu (bulunursa) | PCB/BOM/ısıl simülasyon |

> Sınır kuralının dayanağı: anayasa §2 "Fiziksel PCB / BOM → HAYIR" (okundu) +
> `power-kapsam §sınır` tablosu. Bu dosya sınırı **raporlar**, ihlalini **düzeltmez**.

**Uygulama notu**: bu iki sütun, bir kayıt yazılırken sorulan tek soruyu sadeleştirir —
"bu bilgi tip/envanter mi, tasarım mı?" Tasarım ise bu dosyaya yazılmaz; sınır komşusuna
gönderilir ve bu tabloya `K002'ye yönlendirildi (tarih)` notu düşülür — bu oturumda
yönlendirilen kayıt **yoktur** (henüz hiç ölçüm yapılmadı).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
