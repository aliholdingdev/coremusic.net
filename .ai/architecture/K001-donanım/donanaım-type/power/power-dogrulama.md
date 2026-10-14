---
title: power — Doğrulama (Kanıt Seviyeleri, Kapılar, Ölçüm Formları)
type: mimari-dogrulama
node_id: K001-donanım/donanaım-type/power/power-dogrulama
node_type: architecture-verification
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - "§3 kapılar K-P1…K-P8"
  - "§6 iddia→kanıt matrisi"
  - "§9 HEAD doğrulama tablosu (2026-10-13)"
depends_on: K001-donanım
evidence:
  - "K001-donanım/donanım-anayasa.md §2 — okundu (çatışma: power klasörü vs 'yalnız 3 bağlam') → power-kapsam §0"
  - "K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A6, §7/3-T1, §12/M6 — okundu"
  - "kendi repo sayımım (2026-10-13): 700 kaynak dosyada PSU=0 VRM=0 LDO=0 BMS=0 fuse/sigorta=0 power supply=0"
  - "HEAD (kendi çalıştırığım, 2026-10-13): 80plus.org=200 · energystar.gov=200 · iec.ch=202"
validation: "⚠️ VERIFICATION REQUIRED — K-P3/K-P4/K-P5 açık; S1 ölçüm = 0; Q-T2 kapandı değil"
open_questions:
  - "VD-02: power tipsiz mi — 700 dosyada 0 eşleşme; kasıtlı-yok kararı bekliyor"
  - "P-T01..P-T13 power_requirements alanları NOT PROVIDED — ölçüm gerekir"
  - "anayasa §2 metni revizesi (K-P3) — APPROVAL REQUIRED"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# power — Doğrulama

> **Kapsam**: power/ tip klasörünün doğrulama planı — kanıt seviyeleri, kapılar, boş ölçüm
> formları, iddia→kanıt matrisi, HEAD kaydı.
> **Bu dosya ne yapar ne**: (a) güç ölçümü üretmez (b) anayasa/kapsam metnini revize etmez
> (c) devre tasarımı doğrulaması yapmaz — tasarım kapıları K002'nin (bkz. `power-kapsam §sınır`)
> (d) `parent:` anahtarı kullanmaz (yasak).
> **İlişki**: çatışma kaydı `power-kapsam.md §0` — 1–3. adımlar yürüdü; 4–5. adımlar ONAY BEKLİYOR.


---

## §1 Kanıt seviyeleri (S1–S5 — power)

| Seviye | Ad | Tanım | power'da tipik kullanım | Kullanılamaz |
|---|---|---|---|---|
| **S1** | Kendi ölçümüm | Kendi cihazımla ölçtüğüm sayı (V, A, W, °C, ms) | çıkış gerilimi, verim, sıcaklık, sigorta akımı | — (asıl kapı budur, şu an 0) |
| **S2** | Repo gözlemi | Çalışma ağacında regex sayımı (tarihli) | PSU/VRM/LDO/BMS/sigorta = 0 | "kod yok = donanım yok" demek |
| **S3** | Belge referansı | Vault dosyasında yazılmış (anayasa §2, ADR-063/064 varlığı) | sınır kuralı, çatışma satırı | yazılanın ölçülmüş/doğru olması |
| **S4** | Literatür/kurum | HEAD ile erişimi doğrulanmış sayfa; içerik **okunmadı** | 80plus, energystar, iec | içeriği/madde numarası |
| **S5** | Yok | İlgili kanıt üretilmemiş | çoğu `power_requirements` alanı | — |

**Kendi ölçüm kaydım (S1, power)**: **0 satır.** Bu dosyadaki tüm form alanları `—`.

**S3'ün ikiyüzlülüğü (kayıtlı)**: `power-kapsam §sınır`'da ADR-063/064'e **varlık olarak**
dayanıldı (dosya adı + konum, içerik okunmadı) — sınır kuralı "dosya var" kanıtına değil,
anayasa §2 metnine (`Fiziksel PCB / BOM → HAYIR`, okundu) dayanır. ADR referansı destekleyicidir,
kanıtın kendisi değildir.

---

## §2 İddia türleri (power)

| # | İddia tür | Min seviye | Tipik hata | Bu yazımın durumu |
|---|---|---|---|---|
| I-1 | "şu tip repo'da üretilmiş" | S2 | belge ↔ kod karıştırması | PSU/VRM/LDO/BMS/fuse = 0 ⇒ `UNKNOWN` |
| I-2 | "şu çıkış şu voltajda/akımda" | **S1** | datasheet/etiket değerini ölçüm gibi sunmak | S1=0; alanlar `NOT PROVIDED` |
| I-3 | "şu koruma eşikte kapanıyor" | S1 (test) | tasarım niyetini doğrulanmış davranış sanmak | S1=0; `VERIFY REQUIRED` |
| I-4 | "şu standart/program böyle diyor" | S4 | içerik okunmadan madde uydurmak | 3 URL S4; içerik ⚠️ |
| I-5 | "tasarım K002'ye ait" | S3 (anayasa §2) | sınırı genişletip K001'de hesap yapmak | sınır §sınır'da kural |
| I-6 | "sigorta/LDO sistemde mevcut" | S2+S1 | varlık varsaymak | envanter kanıtı yok ⇒ `UNKNOWN` |

---

## §3 Doğrulama kapıları (K-P1…K-P8)

| Kapı | Condition | Evidence | Status 2026-10-13 |
|---|---|---|---|
| **K-P1** | power/ 8 alan dosyası mevcut · önizleme · son satır Authority | S2 | **KAPALI** (bu görevde yazıldı) |
| **K-P2** | `power-kapsam §0` çatışma kaydı + §sınır (K002/ADR) yazıldı | S2 | **KAPALI** (bu görevde yazıldı) |
| **K-P3** | anayasa §2/§10 + donanaım-type-kapsam §2/A6, §7/3-T1, §12/M6 metni revize + Q-T2 KAPANDI | S3+onay | **AÇIK** — APPROVAL REQUIRED |
| **K-P4** | kendi ölçümüm: çıkış V/A/W, verim, sıcaklık, sigorta akımı, koruma eşik testi | **S1** | **AÇIK** — S1=0 |
| **K-P5** | VD-02 sonuçlandı (power tipsiz mi: 0 eşleşme ya kasıtlı-yok notu) | S2 | **AÇIK** — karar bekliyor |
| **K-P6** | P-T01..P-T13 `power_requirements` alanları en azından etiketli bir kaynağa bağlandı | S3/S4/S1 | **AÇIK** — alanlar NOT PROVIDED |
| **K-P7** | 3 HEAD URL tekrar doğrulandı + içerik başlıkları okundu | S4→S3 | **AÇIK** — HEAD var, içerik okunmadı |
| **K-P8** | Bu dosyanın iddiaları §6 matrisinde kanıtsız satır bırakmıyor | S2 | **KAPALI** (matris §6) |

**Kapı notu**: K-P3 display ile ortak (aynı çatışma) — iki tip dosyasında da açık duruyor,
tek seferde Vault Steward kararıyla kapanır.

---

## §4 Ölçüm formu — PSU/çıkış rail ölçümleri (D-K01 karşılığı: P-K01)

> **Doldurma kuralı**: alan ölçülmeden `—`; `0` yalnız gerçekten sıfır ölçüldüğünde;
> ölçen + tarih + koşul (cihaz, yük, ortam sıcaklığı) zorunlu. Bu form **boştur** (S1=0).

| Rail / nokta | Beklenen (etiketli kaynak — marka/model ölçenin kendi cihazı) | Ölçülen V | Ölçülen ripple (mV) | Yük koşulu | Ölçen | Tarih |
|---|---|---|---|---|---|---|
| ray 1 | — | — | — | — | — | — |
| ray 2 | — | — | — | — | — | — |
| ray 3 | — | — | — | — | — | — |
| ray 4 | — | — | — | — | — | — |

| Sistem | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Toplam giriş gücü (W) | — | — | — | — |
| Verim (%) @ yük | — | — | — | — |
| PSU sıcaklığı (°C, gövde içi/çıkış) | — | — | — | — |
| Fan devri (varsa, RPM) | — | — | — | — |

**Yasak**: 80plus.org'taki sınıf/verim değerlerini bu tabloya **kopyalamak** (S4 ≠ S1);
marka/model uydurmak; "tipik ±%5" gibi tahmin yazmak.

---

## §5 Ölçüm formu — regülatör ve koruma (P-T05, P-T06, P-T10…P-T13)

**P-A — LDO/VRM çıkış doğrulama:**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| VIN / VOUT (V) | — | — | — | — |
| Dropout (mV) @ yük | — | — | — | — |
| Gürültü (mVrms) @ yük | — | — | — | — |
| Yüzey sıcaklığı (°C) | — | — | — | — |

**P-B — koruma eşik testleri (sigorta / OCP / OVP / termal):**

| Koruma | Test yöntemi | Eşik (ölçülen) | Tetiklendi mi | Tarih | Güvenli sınırlar |
|---|---|---|---|---|---|
| sigorta (P-T10) | — | — | — | — | hayır — laboratuvar kontrolü gerekir |
| aşırı akım (P-T11) | — | — | — | — | hayır |
| aşırı gerilim (P-T11) | — | — | — | — | hayır |
| ESD (P-T12) | — | — | — | — | hayır — deşarj testi ekipman ister |
| ısıl kapanış (P-T13) | — | — | — | — | hayır |

> **Güvenlik notu (kural)**: koruma eşik testleri kontrollü koşul ister; bu form kavramsal
> kayıt yeridir, talimat değildir. Gerçek test ayrıca planlanır (Vault Steward onayı).

**P-C — batarya/BMS (P-T07, P-T08) — varsa:**

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Hücre gerilimi (V) | — | — | — |
| Kapasite/SoC (%) | — | — | — |
| Şarj akımı (A) | — | — | — |
| BMS telemetri erişimi (varsa) | — | — | — |

---

## §6 iddia → kanıt matrisi (her satır seviyeli)

| İddia (dosya·bölüm) | İddia sahibi | İstenen kanıt | Mevcut kanıt | Boşluk |
|---|---|---|---|---|
| power/ 8 dosya açıldı | Vault Steward (bu görev) | dosya varlığı | **S2** | — |
| Çatışma kaydı yazıldı | bu görev | power-kapsam §0 | **S2** | — |
| anayasa "yalnız 3 bağlam" + "güç kaynağı bağlamı AÇILMAZ" | `donanım-anayasa.md` §2 | belge metni | **S3** (okundu) | K-P3 (revize edilmedi) |
| kapsam §2/A6, §7/3-T1, §12/M6 dar tutuyor | donanaım-type-kapsam | belge metni | **S3** | K-P3 |
| tasarım K002'ye ait | anayasa §2 "PCB/BOM → HAYIR" | belge metni | **S3** | — (sınır kuralı) |
| ADR-063/064 mevcut | kendi kontrolüm | dosya varlığı | **S3** (ad+konum) | içerik okunmadı |
| K002-electroncis = 16 md | kendi sayımım | glob | **S2** | — |
| PSU/VRM/LDO/BMS/fuse repo'da yok | kendi sayımım | regex (700 dosya) | **S2** (0) | VD-02 (K-P5) |
| tip kayıtları 13×26 alan dolu | bu görev | alan dökümü | **S2** | alanların çoğu NOT PROVIDED (K-P6) |
| 80plus/energystar/iec erişilebilir | HEAD | URL durumu | **S4** (200/200/202, 2026-10-13) | içerik okunmadı (K-P7) |
| çıkış voltaj/verim/sıcaklık değerleri | — | kendi ölçümüm | **S1 = YOK** | P-K01, P-A (K-P4) |
| koruma eşikleri doğrulandı | — | test | **S1 = YOK** | P-B (K-P4) |

---

## §7 Dosya kanıt listesi (2026-10-13)

| Dosya | Durum |
|---|---|
| `power/index.md` | MEVCUT (bu görev) |
| `power/power-kapsam.md` | MEVCUT (bu görev) |
| `power/power-dogrulama.md` | bu dosya |
| `power/power-ornekler.md` | MEVCUT (bu görev) |

**Dokunulmayanlar (S2, kendi kontrolüm)**: `donanım-anayasa.md` · `donanaım-type-kapsam.md` ·
`donanaım-type-dogrulama.md` · `hdd/` · `ssd/` · `audio/` · `cipler/` · ADR dosyaları ·
`.ai/architecture/index.md` — değiştirilmedi (rapor §11).

---

## §8 Sayım metodu (tekrarlanabilir — VD-02 kanıtı)

```
Tarih: 2026-10-13 (kendi çalıştırığım)
Kapsam: C:\www\coremusic.net · .ai/, .git/, vendor/, node_modules/ hariç
700 kaynak dosya: *.php *.js *.sql *.css
Aranan: psu · vrm · ldo · bms · fuse · sigorta · "power supply"
Sonuç: 0 0 0 0 0 0 0
```

**Yorum sınırı**: "şu anki ağaçta yok" der; stash/commit edilmemiş değişikliği ve geçmişi görmez
(geçmiş `git log -S` ile ayrıca — bu oturumda çalıştırılmadı, `UNKNOWN`).

---

## §9 HEAD doğrulama tablosu (2026-10-13, kendi çalıştırığım)

| # | URL | HEAD | Kullanıldı mı | İçerik |
|---|---|---|---|---|
| H-1 | `https://www.80plus.org/` | **200** | EVET (power-kapsam) | ⚠️ okunmadı |
| H-2 | `https://www.energystar.gov/` | **200** | EVET (power-kapsam) | ⚠️ okunmadı |
| H-3 | `https://www.iec.ch/` | **202** | EVET (power-kapsam) | ⚠️ okunmadı |
| H-4 | `https://jedec.org/` | **403** | HAYIR | erişilemedi |
| H-5 | `https://www.analog.com/` | ERR | HAYIR | erişilemedi |
| H-6 | `https://www.littelfuse.com/` | ERR | HAYIR | erişilemedi |
| H-7 | `https://formfactors.org/` | ERR | HAYIR | erişilemedi |

> Komut: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` (kendi çalıştırığım).
> 200/202 = erişildi; **403/ERR kullanılmadı.** Erişilebilir ≠ içerik doğrulandı.

---

## §10 Boşluk → sonraki eylem (sorumlusu belli)

| # | Boşluk | Eylem | Sahibi | Kapı |
|---|---|---|---|---|
| 1 | anayasa/kapsam metni hâlâ "3 bağlam" | metin revizesi onayı | Vault Steward | K-P3 (+K-D3 display) |
| 2 | S1=0 (tüm güç ölçümleri) | ölçüm oturumu (kontrollü koşul) | ilgili ölçüm oturumu | K-P4 |
| 3 | VD-02 açık | kasıtlı-yok kararı veya kod kalemi | Vault Steward | K-P5 |
| 4 | power_requirements NOT PROVIDED | etiketli kaynak/ölçüm bağlama | Vault Steward + ölçüm | K-P6 |
| 5 | 3 URL içerik okunmadı | içerik başlığı okuma + özet | ilgili oturum | K-P7 |

---

## §11 Rapor özeti (bu dosyanın iddiası)

| Öğe | Değer |
|---|---|
| Kapılar KAPALI | K-P1, K-P2, K-P8 (3/8) |
| Kapılar AÇIK | K-P3, K-P4, K-P5, K-P6, K-P7 (5/8) |
| S1 ölçüm satırı | **0** |
| HEAD: kullanıldı / reddedildi | 3 / 4 |
| Etiket | `PROPOSED — APPROVAL REQUIRED` (K-P3 nedeniyle) |
| git commit | bu oturumda **ATILMADI** (subagent taahhüdü) |

---

## §12 Tip-bazlı doğrulama kartları (P-T01…P-T13)

> Her kart: tipin **neyi** doğrulayacağı, hangi kapıya bağlı olduğu, hangi formun doldurulacağı.
> Hepsi bu yazım anında **boş** (S1=0).

### Kart P-T01 · ATX PSU

| Alan | Değer |
|---|---|
| tip_id | P-T01 |
| doğrulanacak | PSU envanterde var mı · ray ölçümleri · verim |
| kapı | K-P4 (P-K01) · K-P5 |
| form | §4 P-K01 PSU formu |
| beklenen kanıt | ölçüm (S1) + envanter (S2) |
| durum 2026-10-13 | `UNKNOWN` |
| risk | etiket/80plus sınıfını ölçüm sanmak (I-2) |

### Kart P-T02 · DC adaptör

| Alan | Değer |
|---|---|
| tip_id | P-T02 |
| doğrulanacak | adaptör envanteri · DC jack çıkışı · PD kontratı (varsa) |
| kapı | K-P4 · K-P6 |
| form | §4 (adaptör satırı) + §5 P-A |
| beklenen kanıt | ölçüm (S1) + etiket (S3) |
| durum | `UNKNOWN` |
| risk | PD kontratını tahmin etmek |

### Kart P-T03 · AC/DC

| Alan | Değer |
|---|---|
| tip_id | P-T03 |
| doğrulanacak | dönüşüm aşaması çıkışı (V/ripple) |
| kapı | K-P4 |
| form | power-ornekler §4 şablonu |
| beklenen kanıt | ölçüm (S1) |
| durum | `UNKNOWN` |
| risk | ripple ölçümü olmadan "temiz çıkış" iddiası |

### Kart P-T04 · DC/DC

| Alan | Değer |
|---|---|
| tip_id | P-T04 |
| doğrulanacak | giriş/çıkış · verim · sıcaklık |
| kapı | K-P4 |
| form | power-ornekler §5 P-B1 |
| beklenen kanıt | ölçüm (S1) |
| durum | `UNKNOWN` |
| risk | verimi hesaptan yazmak (ölçüm değil) |

### Kart P-T05 · LDO

| Alan | Değer |
|---|---|
| tip_id | P-T05 |
| doğrulanacak | dropout · gürültü · sıcaklık |
| kapı | K-P4 |
| form | §5 P-A + power-ornekler §5 P-B2 |
| beklenen kanıt | ölçüm (S1) |
| durum | `UNKNOWN` |
| risk | gürültü ölçümü olmadan "ses yoluna uygun" iddiası (audio_relevance=YÜKSEK) |

### Kart P-T06 · VRM

| Alan | Değer |
|---|---|
| tip_id | P-T06 |
| doğrulanacak | bölge varlığı · çıkış · telemetri |
| kapı | K-P4 · K-P6 |
| form | §5 P-A |
| beklenen kanıt | ölçüm (S1) + doküman (S3) |
| durum | `UNKNOWN` |
| risk | telemetri arayüzünü tahmin etmek |

### Kart P-T07 · Batarya

| Alan | Değer |
|---|---|
| tip_id | P-T07 |
| doğrulanacak | paket/hücre · kapasite · SoC |
| kapı | K-P4 |
| form | §5 P-C + power-ornekler §9 |
| beklenen kanıt | ölçüm/etiket (S1/S3) |
| durum | `UNKNOWN` |
| risk | kapasiteyi etiketten ölçüm gibi sunmak |

### Kart P-T08 · BMS

| Alan | Değer |
|---|---|
| tip_id | P-T08 |
| doğrulanacak | BMS varlığı · telemetri erişimi · firmware |
| kapı | K-P4 · K-P6 |
| form | §5 P-C |
| beklenen kanıt | erişim denemesi (S1/S2) |
| durum | `UNKNOWN` |
| risk | batarya yokken BMS kaydı tutmak |

### Kart P-T09 · Güç dağıtımı

| Alan | Değer |
|---|---|
| tip_id | P-T09 |
| doğrulanacak | dağıtım düzeni · toprak yapısı · yükler |
| kapı | K-P4 · K-P6 |
| form | power-ornekler §8 güç ağacı sayfası |
| beklenen kanıt | şema/ölçüm (S3/S1) |
| durum | `UNKNOWN` |
| risk | şema kanıtı olmadan "düzen şu" iddiası |

### Kart P-T10 · Sigorta

| Alan | Değer |
|---|---|
| tip_id | P-T10 |
| doğrulanacak | varlık · derece · konum |
| kapı | K-P4 · K-P5 |
| form | power-ornekler §6 |
| beklenen kanıt | okuma/etiket (S1/S3) |
| durum | `UNKNOWN` |
| risk | dereceyi tahmin etmek |

### Kart P-T11 · OCP/OVP

| Alan | Değer |
|---|---|
| tip_id | P-T11 |
| doğrulanacak | eşik · tetik davranışı |
| kapı | K-P4 |
| form | §5 P-B |
| beklenen kanıt | test (S1 — kontrollü koşul) |
| durum | `UNKNOWN` |
| risk | tasarım niyetini doğrulanmış davranış sanmak |

### Kart P-T12 · ESD

| Alan | Değer |
|---|---|
| tip_id | P-T12 |
| doğrulanacak | korunan arayüzler · test raporu |
| kapı | K-P4 · K-P6 |
| form | §5 P-B (ESD satırı) |
| beklenen kanıt | test raporu (S1/S3) |
| durum | `UNKNOWN` |
| risk | ADR-063 varlığını "uygulanmış" sanmak (I-5 benzeri) |

### Kart P-T13 · Isıl koruma

| Alan | Değer |
|---|---|
| tip_id | P-T13 |
| doğrulanacak | eşik · kapanış davranışı · fan |
| kapı | K-P4 |
| form | §5 P-B + §4 (sıcaklık satırı) |
| beklenen kanıt | ölçüm (S1) |
| durum | `UNKNOWN` |
| risk | sıcaklık ölçümü yapmadan "yeterli soğutma" iddiası |

---

## §13 Tip × kapı matrisi (power)

| Tip \ Kapı | K-P3 | K-P4 | K-P6 |
|---|---|---|---|
| P-T01 ATX PSU | ✔ | ✔ | ✔ |
| P-T02 adaptör | ✔ | ✔ | ✔ |
| P-T03 AC/DC | ✔ | ✔ | — |
| P-T04 DC/DC | ✔ | ✔ | ✔ |
| P-T05 LDO | ✔ | ✔ | — |
| P-T06 VRM | ✔ | ✔ | ✔ |
| P-T07 batarya | ✔ | ✔ | ✔ |
| P-T08 BMS | ✔ | ✔ | ✔ |
| P-T09 dağıtım | ✔ | ✔ | ✔ |
| P-T10 sigorta | ✔ | ✔ | — |
| P-T11 OCP/OVP | ✔ | ✔ | — |
| P-T12 ESD | ✔ | ✔ | ✔ |
| P-T13 ısıl | ✔ | ✔ | ✔ |

> K-P3 (anayasa revizesi) **tüm tipleri** bağlar — hiçbir tip o kapı kapalıyken `DOCUMENTED`
> etiketi alamaz; hepsi şu an `UNKNOWN`/`proposed`.

---

## §14 Tip bazlı risk kaydı (power)

| # | Tip | Risk | Etki | Olasılık | Azaltım | Kapı |
|---|---|---|---|---|---|---|
| R-P1 | P-T01/P-T02 | etiket değerini ölçüm gibi yazmak | sahte S1 | yüksek | etiket S3/S4 olarak ayrılır | K-P4 |
| R-P2 | P-T10 | sigorta derecesini tahmin etmek | yanlış koruma kaydı | orta | okunan derece zorunlu | K-P4 |
| R-P3 | P-T11/P-T12/P-T13 | kontrollü test olmadan eşik iddiası | güvenlik yanlışlığı | yüksek | P-B formu + güvenlik notu | K-P4 |
| R-P4 | P-T08 | batarya yokken BMS kaydı | hayali envanter | orta | batarya varlığı önce doğrulanır | K-P5 |
| R-P5 | tümü | anayasa revizesi beklerken `DOCUMENTED` | yetki aşımı | düşük | status=proposed disiplini | K-P3 |
| R-P6 | tümü | K002 tasarım işine girmek | sınır ihlali | orta | §sınır tablosu | K-P2 (kapalı) |
| R-P7 | P-T07 | kapasiteyi etiketten ölçüm sunmak | kanıt şişirme | orta | etiket ayrımı | K-P4 |

---

## §15 Kapı kapanış kontrol listesi ( Vault Steward için — boş )

| # | Adım | Kapı | Tarih (doldurulacak) | İmza (doldurulacak) |
|---|---|---|---|---|
| 1 | anayasa §2/§10 metni revizesi onaylandı | K-P3 | — | — |
| 2 | donanaım-type-kapsam revizesi onaylandı | K-P3 | — | — |
| 3 | Q-T2 KAPANDI olarak işaretlendi | K-P3 | — | — |
| 4 | PSU/rail ölçümleri dolduruldu (P-K01) | K-P4 | — | — |
| 5 | LDO/VRM ölçümleri dolduruldu (P-A) | K-P4 | — | — |
| 6 | koruma eşik testleri tamamlandı (P-B) | K-P4 | — | — |
| 7 | batarya/BMS kaydı dolduruldu (P-C) | K-P4 | — | — |
| 8 | VD-02 kararı verildi | K-P5 | — | — |
| 9 | power_requirements alanları kaynağa bağlandı | K-P6 | — | — |
| 10 | 3 HEAD URL içerik başlığı okundu | K-P7 | — | — |

> Bu tablo **bilerek boştur** — 10 adımın hiçbiri bu oturumda atılmadı; atılmayan adımın
> tarihi/imzası yazılmaz (uydurma kayıt yazmak yasak).

---

## §16 Kapanış (bu dosyanın durumu)

| Öğe | Değer |
|---|---|
| Kapılar KAPALI | K-P1, K-P2, K-P8 (3/8) |
| Kapılar AÇIK | K-P3, K-P4, K-P5, K-P6, K-P7 (5/8) |
| Tip kartları (§12) | 13/13 yazıldı, 13/13 boş |
| S1 ölçüm satırı | **0** |
| HEAD: kullanıldı / reddedildi | 3 / 4 |
| Etiket | `PROPOSED — APPROVAL REQUIRED` (K-P3) |
| git commit | bu oturumda **ATILMADI** (subagent taahhüdü) |

---

## §17 İlişkili dosyalar (cross-reference)

> Okuma sırası: önce `power-kapsam §0` (çatışma), sonra bu dosya (kapılar), sonra
> `power-ornekler` (şablonlar). Anayasa/kapsam yalnız **okunur** — revize edilmez.
> Sınır komşusu K002-electroncis: devre/PCB/BOM işi oraya aittir (power-kapsam §sınır).

| Dosya | İlişki |
|---|---|
| `power/index.md` | bu dosyanın künyesi ve kapı özeti |
| `power/power-kapsam.md` | P-T01…P-T13 tip tanımları · §0 çatışma kaydı · §sınır (K002) |
| `power/power-ornekler.md` | şablonlar (AC/DC, DC/DC, LDO, sigorta, güç ağacı) |
| `donanaım-type/index.md` | kademe-3 kataloğu (power satırı, 2026-10-13) |
| `K001-donanım/index.md` | kademe-3 tablosu (power satırı, 2026-10-13) |
| `donanım-anayasa.md` | §2 çatışma kaynağı — **dokunulmaz**, yalnız okundu |
| `donanaım-type/donanaım-type-kapsam.md` | §2/A6, §7/3-T1, §12/M6 çatışma kaynağı — dokunulmaz |
| `K002-electroncis/` · ADR-063/064 | sınır komşusu — **dokunulmadı**, varlık kanıtı |

---

## §18 Sürüm geçmişi (bu dosya)

| # | Tarih | Ne değişti | Değiştiren |
|---|---|---|---|
| 1 | 2026-10-13 | ilk yazım (§1–§18) — çatışma kaydı power-kapsam §0'a bağlandı | Vault Steward (subagent) |

> Gelecek revizyonlar bu tabloya eklenir; silme yasak (append-only).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
