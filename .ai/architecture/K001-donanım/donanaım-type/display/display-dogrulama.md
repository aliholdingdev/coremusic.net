---
title: "display — Doğrulama (Kanıt Seviyeleri, Kapılar, Ölçüm Formları)"
type: mimari-dogrulama
node_id: K001-donanım/donanaım-type/display/display-dogrulama
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - "§3 kapılar K-D1…K-D8"
  - "§6 iddia→kanıt matrisi"
  - "§9 HEAD doğrulama tablosu (2026-10-13)"
depends_on:
  - K001-donanım
  - ADR-102-tur-taksonomi-genislemesi-5-dal
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §4 (display satırı: ekran kabloları/çıkış sınıflandırması cables dalında — display dalı ekran sınıfı) · §4.1 (sınır) · §4.2 (ADR-038/089/064 DEĞİŞMEZ) — 2026-10-13 eklendi (orkestratör cerrahi patch)"
  - "K001-donanım/donanım-anayasa.md §2 (yalnız 3 bağlam) — okundu, çatışma kaydı §0"
  - "K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A6, §7/3-T1, §12/M6, §6/Q-T2 — okundu"
  - "K001-donanım/donanaım-type/audio/audio-dogrulama.md — 510 satır, kanıt kurgusu (önceki oturum, COUNT edilmedi)"
  - "repo sayımı (kendi çalıştırığım, 2026-10-13): *.cpp=0 *.h=0 *.c=0 *.xc=0 *.ino=0 · 700 kaynak dosyada EDID/DDC/HDMI/DisplayPort/VESA=0"
  - "HEAD (kendi çalıştırığım, 2026-10-13): vesa.org=200 · hdmi.org=200 · displayport.org=200 · displayhdr.org=200 · cta.tech=200 · khronos.org=200 · usb.org=200 (G6 nedeniyle kullanılmadı)"
validation: "⚠️ VERIFICATION REQUIRED — bölüm kapıları (K-D1…K-D8) kapatılmadı; Q-T2 bu dosyalarda cevaplandı ama kapı kapandı değil"
open_questions:
  - "Q-T2 (kapsam §6): yanıtlandı (display/ ve power/ açıldı, §0 çatışma kaydı yazıldı) — KAPANDI DEĞİL, anayasa/kapsam metni revizesi onay bekliyor"
  - "VD-01: display tipsiz mi — 700 dosyada 0 eşleşme, kapı K-D7 açık"
  - "VD-02: EDID/DDC/ölçüm — D-K01..D-K05 kapıları açık, ölçüm YOK"
  - "VD-03: anayasa §2/§10 revizesi — APPROVAL REQUIRED, bu dosyanın konusu değil"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# display — Doğrulama

> **Kapsam**: display/ tip klasörünün doğrulama planı — kanıt seviyeleri, kapılar (gates),
> boş ölçüm formları, iddia→kanıt matrisi, HEAD URL kaydı.
> **Bu dosya ne yapar ne**: (a) donanım ölçümü üretmez (b) anayasa/kapsam metnini revize etmez
> (c) Q-T2'yi KAPANDI olarak kapatmaz (d) `parent:` anahtarı kullanmaz (yasak).
> **İlişki**: çatışma kaydı `display-kapsam.md §0` — oradaki 5 adımlı kapanış tablosunun
> 1–3. adımları bu dosya grubuyla yürüdü; 4–5. adımlar (anayasa revizesi + Q-T2 kapanışı) ONAY BEKLİYOR.


---

## §1 Kanıt seviyeleri (S1–S5)

> Display alt tipi için kanıt hiyerarşisi. Kayıt `display-kapsam.md §6`'daki `evidence:`
> alanı bu seviyelerden birini taşır; seviye yükseldikçe iddia daha az yorum, daha çok gözlem.

| Seviye | Ad | Tanım | Ne İSPATLAR | Ne İSPATLAMAZ |
|---|---|---|---|---|
| **S1** | Kendi ölçümüm | Kendi cihazımla, kendi koşulumda üretilen sayı (volt, akım, sıcaklık, süre, piksel) | O koşulda o değer gerçekleşti | Genelliği; başka örneğe taşınmaz |
| **S2** | Repo gözlemi | Çalışma ağacında dosya/regex sayımı (tarih + komut ile) | "şu an bu ağaçta X=0/Y=kayıt" | Geçmişi; commit edilmemiş değişikliği; gelecekteki kodu |
| **S3** | Belge referansı** | Başka bir vault dosyasında yazılmış örneğin/ölçümün varlığı | "şu dosyada öyle yazıyor" | Yazılanın doğru/gerçek/ölçülmüş olmasını |
| **S4** | Literatür/kurum | HEAD ile erişimi doğrulanmış kurum sayfası; içerik **okunmadı** | URL 2026-10-13'te cevap verdi | İçeriği; standardın maddesini; değerleri |
| **S5** | Yok | İlgili kanıt üretilmemiş | — | —; kayıt `UNKNOWN` / `VERIFY REQUIRED` kalır |

**S2-S3-S4'ün üstü**: `display-kapsam.md §0` çatışma satırları (anayasa §2 "yalnız 3 bağlam"
ve kapsam §2/A6, §7/3-T1, §12/M6) — bunlar **iki belge arasında gerilim kaydıdır**, tek doğruluk
idddiası değil; çözümleri onaya bağlıdır, bu dosyanın doğrulama kapıları bunu kapatmaz.

**G6 unique URL kuralı uygulaması**: kendi HEAD çalışmamda `usb.org`, `amd.com`, `nvidia.com`,
`arm.com` 200 döndü ama bu tipin dosyalarında **kullanılmadı** — anayasa §8 G6 URL uniqueness
gerekçesiyle (ilgili bağlantı zaten başka K001/K005 dosyalarında). Benim display için kullandığım
6 URL (vesa, hdmi, displayport, displayhdr, cta, khronos) bu tip dosyalarında **ilk kez** geçiyor.

---

## §2 Kanıt gerektiren iddia türleri (display)

| # | İddia türü | Minimum seviye | Tipik hata | display'de durum |
|---|---|---|---|---|
| I-1 | "şu tip repo'da var/üretilmiş" | S2 (sayım + tarih) | "belgede geçiyor" ile "üretilmiş" karışması | EDID/DDC/HDMI/DP: S2 ile 0 — `UNKNOWN` |
| I-2 | "şu panel şu arayüzle konuşur" | S3 (doküman) + kaynak | panel tipi ile protokolü birbirine karıştırmak (anayasa §12.1) | 4 eksenli ayrım §5 — kayıtlarda `interfaces`≠`protocols` |
| I-3 | "şu ölçüm ( parlaklık/tepe/süre )" | S1 | literatür değerini ölçüm gibi sunmak | **S1 = 0 kayıt** (display ölçümü yapılmadı) |
| I-4 | "şu IPS/standart böyle diyor" | S4 (erişildi, içerik okunmadı) | içerik okunmadığı halde madde numarası uydurmak | 6 URL S4; içerik `⚠️` (okunmadı) |
| I-5 | "şu cihaz tüketicide/kullanımda" | S1+S2 | ekran görüntüsü/CI'dan cihaz sonucu çıkarmak | `T17-monitor-22fhd` = arayüz tasarımı (S3), cihaz değil |
| I-6 | "şu arayüz şu DP/HDMI sürümünde" | S3 veya S1 | sürümü tahmin etmek | `UNKNOWN` / `VERIFY REQUIRED` |

---

## §3 Doğrulama kapıları (K-D1…K-D8)

> Her kapı: **condition** (neyle kapanır) · **evidence** (hangi seviye) · **status** (açık/kapalı).
> Hepsi bu yazım anında **AÇIK** — kapıların kapatılması ölçüm/inceleme gerektirir, yazım değildir.

| Kapı | Condition | Evidence (min) | Status 2026-10-13 |
|---|---|---|---|
| **K-D1** | `display-kapsam.md §0` çatışma kaydı her iki -kapsam dosyasında ve kimlik sütununda | S2 | **KAPALI** (bu görevde yazıldı — çatışma kaydı mevcut) |
| **K-D2** | display/ 8 alan dosyası mevcut, önizleme doğru, son satır Authority | S2 | **KAPALI** (dosya listesi §8) |
| **K-D3** | anayasa §2/§10 + donanaım-type-kapsam §2/A6, §7/3-T1, §12/M6 metni revize edildi ve Q-T2 KAPANDI | S3+onay | **AÇIK** — APPROVAL REQUIRED (bu dosyanın yetkisi dışında) |
| **K-D4** | D-K01…D-K05 ölçümleri dolduruldu (EDID/DDC okuma, arayüz link, piksel hata, tepe süre, dokunma gecikme) | **S1** | **AÇIK** — ölçüm yok (repo'da display ölçüm altyapısı da yok) |
| **K-D5** | panel tipi ↔ arayüz/protokol ayrımı başka bir K001 tipiyle çelişmiyor (I2S benzeri ayrım bozulmamış) | S3 | **AÇIK** — sadece display içinde model kuruldu; çapraz tip incelemesi yapılmadı |
| **K-D6** | 6 HEAD URL'i tekrar doğrulandı ve içerik başlıkları okunup özetlendi | S4→S3 | **AÇIK** — 2026-10-13 HEAD var, içerik okunmadı |
| **K-D7** | VD-01 (display tipsiz mi) sonuçlandı: EDID/DDC/ekran arayüzü kodda belirdi mi ya da "kasıtlı yok" notu düşüldü mü | S2 | **AÇIK** — 700 dosyada 0; kasıtlı-yok kararı alınmadı |
| **K-D8** | Bu dosyanın iddiaları §6 matrisinde kanıtsız satır bırakmıyor | S2 | **KAPALI** (matris §6 — kanıtsız satır yok, hepsi seviyeli) |

---

## §4 Ölçüm formu — EDID / DDC (D-K01)

> **Doldurma kuralı**: her alan ölçülmeden `—` kalır; `—` asla `0` yazmaz; `0` yalnız gerçekten
> ölçülüp sıfır bulunan değerdir. Ölçüm yapan kişi + tarih + koşul (cihaz, kablo, sürücü, OS) yazılır.
> Bu form şu an **boştur** — display için kendi ölçümüm: **yok** (S1=0).

| Alan | Değer | Ölçen | Tarih | Koşul (cihaz/kablo/sürücü/OS) |
|---|---|---|---|---|
| EDID okundu mu (hangi araçla) | — | — | — | — |
| EDID üretici/ürün kodu | — | — | — | — |
| EDID desteklenen modlar (räslolution/refresh) | — | — | — | — |
| DDC/CI yazma- okuma denemesi | — | — | — | — |
| Hotplug algılama (tak-çıkar) | — | — | — | — |
| Sürüm/revizyon (DP/HDMI) | — | — | — | — |

**Bu formun gösterdiği boşluk**: repo'da EDID/DDC ile ilgili **tek satır kod yok** (S2, §5).
Yani D-K01'i kapatacak altyapı da yok — kapı, önce display arayüz kodunun varlığına bağlı.

---

## §5 Ölçüm formu — arayüz link ve piksel (D-K02, D-K03)

**D-K02 — arayüz link doğrulama (kablo/tak-çalıştır):**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Bağlı port tipi (DP/HDMI/SMBus/USB) | — | — | — | — |
| Link eğitiminde hata/retry | — | — | — | — |
| Max çözünürlük+refresh @ bu kablo | — | — | — | — |
| Hotplug gecikmesi (ms) | — | — | — | — |

**D-K03 — piksel/görüntü bütünlüğü:**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Dead/stuck piksel sayısı (siyah-beyaz-kırmızı test) | — | — | — | — |
| Backlight sızması (siyah ekran, karanlık oda) | — | — | — | — |
| Tepki süresi (giriş→görüntü, ms) | — | — | — | — |
| Panel tipi doğrulaması (menü/EDID ile) | — | — | — | — |

**Kural**: bu iki form, literatür tipik değerlerle **doldurulamaz** — `display-kapsam.md`'deki
`⚠️ VERIFICATION REQUIRED` etiketli referanslar ölçüm yerine geçmez (I-3 çaprazı).

---

## §6 Ölçüm formu — display gecikme ve dokunma (D-K04, D-K05)

**D-K04 — uçtan uca görüntü gecikmesi (input lag):**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Yöntem (yüksek fps kamera / photodiode / yazılım) | — | — | — | — |
| Mod + refresh (ör. 1080p@60) | — | — | — | — |
| Ölçülen tepe (ms) | — | — | — | — |
| Ölçülen ortalama (ms) | — | — | — | — |
| Ölçüm sayısı (n) | — | — | — | — |

**D-K05 — dokunma katmanı doğrulama (D-T07):**

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Dokunma teknolojisi (kapasitif/rezistif) | — | — | — | — |
| Örnek noktası hatası (kalibrasyon sonrası, px) | — | — | — | — |
| Multi-touch parmak sayısı (gerçekten çalışan) | — | — | — | — |
| Dokunma→görüntü gecikmesi (ms) | — | — | — | — |
| Gövde/touch layer ayrımı doğrulandı mı (§6, bileşen↔cihaz) | — | — | — | — |

**Repo bağı**: D-K05'in bileşen↔cihaz ayrımı için veri kaynağı olarak `T17-monitor-22fhd`
ekranları (S3) kullanılabilir; ama bu ekranlar dokunma **etişileşimini** de tanımlar mı, o
**VD-01** ile ilgili ve açık.

---

## §7 iddia → kanıt matrisi (her satır seviyeli)

| İddia (dosya·bölüm) | İddia sahibi | İstenen kanıt | Mevcut kanıt (seviye) | Boşluk |
|---|---|---|---|---|
| display/ ve power/ açıldı | Vault Steward (bu görev) | dosya varlığı | **S2** (§8 dosya listesi) | — |
| Çatışma kaydı yazıldı | bu görev | iki -kapsam §0 | **S2** | — |
| anayasa "yalnız 3 bağlam" | `donanım-anayasa.md` §2 | belge metni | **S3** (okundu) | revize edilmedi (K-D3) |
| "ekran/güç kaynağı bağlamı AÇILMAZ" | anayasa §2 | belge metni | **S3** | çatışma kaydı ile askıda |
| Q-T2 yanıtlandı | bu görev (kapsam §6 kaydı) | yanıt metni | **S2** | KAPANDI değil |
| EDID/DDC repo'da yok | kendi sayımım | regex sayımı | **S2** (700 dosya, 0) | kasıtlı-yok kararı (K-D7) |
| panel ↔ arayüz ayrı eksen | bu görev modeli | ayrım gerekçesi | **S2+S3** (model §5) | çapraz tip incelemesi (K-D5) |
| DisplayPort/HDMI organizasyonları var | HEAD | URL durumu | **S4** (200, 2026-10-13) | içerik okunmadı (K-D6) |
| DisplayHDR/CTA/Khronos erişilebilir | HEAD | URL durumu | **S4** (200) | içerik okunmadı |
| ölçüm değerleri (giriş lag, piksel, hotplug) | — | kendi ölçümüm | **S1 = YOK** | D-K01..05 (K-D4) |
| tüketici monitör cihazda mı | `T17-monitor-22fhd` | cihaz kanıtı | **S3** (arayüz tasarımı) | cihaz kanıtı yok |
| tip kayıtları D-T01..08 alan dolu | bu görev | 26 alan | **S2** (alanlar doldu; UNKNOWN alanlar var) | doğrulama K-D4/K-D5 |

---

## §8 Dosya kanıt listesi (2026-10-13, kendi sayımım)

| Dosya | Sayı | Durum |
|---|---|---|
| `display/index.md` | 62 | MEVCUT (bu görev) |
| `display/display-kapsam.md` | 556 | MEVCUT (bu görev) |
| `display/display-dogrulama.md` | bu dosya | MEVCUT (bu görev) |
| `display/display-ornekler.md` | — | MEVCUT (bu görev) |
| `power/index.md` | — | MEVCUT (bu görev) |
| `power/power-kapsam.md` | — | MEVCUT (bu görev) |
| `power/power-dogrulama.md` | — | MEVCUT (bu görev) |
| `power/power-ornekler.md` | — | MEVCUT (bu görev) |

> Sayılar yazım anındaki değerdir; dosya düzenlemesiyle değişir (kanıt = tarihli anlık görüntü).
> Bu dosyanın kendi satır sayısı da yazım anında §8'e işlenmiştir ve raporlamada kontrol edilir.

**Yasak dosyalara dokunulmadı (S2, kendi kontrolüm)**: `donanım-anayasa.md`,
`donanaım-type-kapsam.md`, `donanaım-type-dogrulama.md`, `hdd/`, `ssd/`, `audio/`, `cipler/`,
ADR dosyaları, `.ai/architecture/index.md` — bu görevde değiştirilmedi (rapor §11).

---

## §9 HEAD doğrulama tablosu (2026-10-13, kendi çalıştırığım)

| # | URL | HEAD sonucu | Bu tipin dosyasında kullanıldı mı | İçerik |
|---|---|---|---|---|
| H-1 | `https://vesa.org/` | **200** | EVET (display-kapsam) | ⚠️ okunmadı |
| H-2 | `https://hdmi.org/` | **200** | EVET (display-kapsam) | ⚠️ okunmadı |
| H-3 | `https://www.displayport.org/` | **200** | EVET (display-ornekler) | ⚠️ okunmadı |
| H-4 | `https://displayhdr.org/` | **200** | EVET (display-dogrulama) | ⚠️ okunmadı |
| H-5 | `https://www.cta.tech/` | **200** | EVET (display-dogrulama) | ⚠️ okunmadı |
| H-6 | `https://www.khronos.org/` | **200** | EVET (display-ornekler) | ⚠️ okunmadı |
| H-7 | `https://usb.org/` | 200 | **HAYIR** — G6 unique URL (başka dosyada) | — |
| H-8 | `https://www.amd.com/` | 200 | **HAYIR** — G6 | — |
| H-9 | `https://www.nvidia.com/` | 200 | **HAYIR** — G6 | — |
| H-10 | `https://www.arm.com/` | 200 | **HAYIR** — G6 | — |
| H-11 | `https://jedec.org/` | **403** | HAYIR (erişilemedi) | — |
| H-12 | `https://analog.com/` | ERR | HAYIR | — |
| H-13 | `https://www.littelfuse.com/` | ERR | HAYIR | — |
| H-14 | `https://formfactors.org/` | ERR | HAYIR | — |

> Komut: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` (kendi çalıştırığım).
> 200/202 = erişildi; **403/ERR kullanılmadı.** Erişilebilir ≠ içerik doğrulandı — H-1..H-6
> içerik başlıkları **okunmadı**, bu yüzden ilgili iddialar `⚠️ VERIFICATION REQUIRED`.

---

## §10 power/ için doğrulama kapıları (kısa — tam kurgu `power/power-dogrulama.md`)

| Kapı | Condition | Status |
|---|---|---|
| **K-P1** | power/ 8 alan dosyası mevcut + Authority son satır | **KAPALI** (§8) |
| **K-P2** | power-kapsam §0 çatışma kaydı + K002/ADR-063/064 sınır notu | **KAPALI** (bu görev) |
| **K-P3** | anayasa §2 metni revizesi (onay) | **AÇIK** (APPROVAL REQUIRED) |
| **K-P4** | kendi ölçümüm: PSU/VRM/LDO/güç koruma — **S1 = 0** | **AÇIK** |
| **K-P5** | 700 dosyada PSU/VRM/LDO/BMS/sigorta=0 ⇒ VD-02 sonuçlandı | **AÇIK** |

---

## §11 Onay bekleyenler (bu dosyanın kararı değil)

| # | Bekleyen | Sahibi | Etiket |
|---|---|---|---|
| 1 | anayasa §2/§10 + donanaım-type-kapsam §2/A6, §7/3-T1, §12/M6 metin revizesi | Vault Steward onayı | `PROPOSED — APPROVAL REQUIRED` |
| 2 | Q-T2'nin KAPANDI sayılması | Vault Steward | açık (yanıtlandı ≠ kapandı) |
| 3 | D-K01..D-K05 ölçüm kampanyası | ilgili ölçüm oturumu | `VERIFY REQUIRED` |
| 4 | K-D5 çapraz tip incelemesi (panel↔protokol ayrımı başka tiplerle) | Vault Steward | açık |
| 5 | VD-01: kasıtlı-yok notu ya da EDID kodu kararı | Vault Steward | açık |

## §12 Tip-bazlı doğrulama kartları (D-T01…D-T08)

> Her kart: tipin **neyi** doğrulayacağı, hangi kapıya bağlı olduğu, hangi formun doldurulacağı.
> Hepsi bu yazım anında **boş** (S1=0) — kartlar plan değil, doldurulmayı bekleyen formlardır.

### Kart D-T01 · LCD (kendi sınıfı)

| Alan | Değer |
|---|---|
| tip_id | D-T01 |
| doğrulanacak | panel tipi iddiası (LCD sınıfı) + EDID'de sınıf okuması |
| kapı | K-D4 (EDID) · K-D5 (ayrım) |
| form | §4 EDID kaydı |
| beklenen kanıt | EDID panel tipi satırı (S1) + doküman (S3) |
| durum 2026-10-13 | `UNKNOWN` — form boş |
| risk | panel tipi tahminiyle karıştırma (I-2 tuzağı) |

**Doldurulacak alanlar**: EDID üretici/ürün kodu · panel tipi satırı · desteklenen modlar ·
ölçen · tarih · koşul — hepsi `—`.

### Kart D-T02 · LED arkadan aydınlatmalı LCD

| Alan | Değer |
|---|---|
| tip_id | D-T02 |
| doğrulanacak | aydınlatma tipi iddiası (LED backlight) + panel sınıfı ayrımı |
| kapı | K-D5 (ayrım: aydınlatma ≠ panel) |
| form | §5 piksel/backlight formu (backlight sızması satırı) |
| beklenen kanıt | EDID/doküman (S3) + görsel/ölçüm (S1) |
| durum | `UNKNOWN` |
| risk | "LED ekran" söylemiyle OLED'i karıştırma |

**Doldurulacak alanlar**: aydınlatma tipi kaynağı · sızma ölçümü · uniformite notu ·
ölçen · tarih — hepsi `—`.

### Kart D-T03 · OLED

| Alan | Değer |
|---|---|
| tip_id | D-T03 |
| doğrulanacak | emisif panel iddiası (OLED) |
| kapı | K-D4 (EDID) · K-D5 |
| form | §5 piksel formu (ölü piksel satırı) |
| beklenen kanıt | EDID (S1/S3) |
| durum | `UNKNOWN` |
| risk | tip-sınıfı ile marka-model karıştırılması |

**Doldurulacak alanlar**: EDID panel tipi · ölü piksel sayımı · yanma test notu ·
ölçen · tarih — hepsi `—`.

### Kart D-T04 · GPU display çıkışı

| Alan | Değer |
|---|---|
| tip_id | D-T04 |
| doğrulanacak | sistemde display çıktısı üreten bileşenin varlığı |
| kapı | K-D7 (repo: GPU/çıktı kodu var mı) |
| form | §5 D-K02 arayüz link formu |
| beklenen kanıt | repo sayımı (S2 — şu an 0) + sistem envanteri |
| durum | `UNKNOWN` |
| risk | ekran görüntüsü/CI'dan donanım sonucu çıkarmak (I-5) |

**Doldurulacak alanlar**: çıkış tipi · bağlı port · max mod · ölçen · tarih — hepsi `—`.

### Kart D-T05 · DisplayPort bağlantısı

| Alan | Değer |
|---|---|
| tip_id | D-T05 |
| doğrulanacak | DisplayPort arayüzü var mı + sürüm iddiası |
| kapı | K-D4 (sürüm okuma) · K-D6 |
| form | §7 arayüz sürüm kayıt formu (DisplayPort satırı) |
| beklenen kanıt | sürüm okunması (S1) veya doküman (S3) |
| durum | `UNKNOWN` |
| risk | sürüm tahmini ("1.4 gibi" yazmak yasak) |

**Doldurulacak alanlar**: sürüm · link hızı · hotplug davranışı · ölçen · tarih — hepsi `—`.

### Kart D-T06 · HDMI bağlantısı

| Alan | Değer |
|---|---|
| tip_id | D-T06 |
| doğrulanacak | HDMI arayüzü var mı + sürüm iddiası |
| kapı | K-D4 · K-D6 |
| form | §7 arayüz sürüm kayıt formu (HDMI satırı) |
| beklenen kanıt | sürüm okunması (S1) veya doküman (S3) |
| durum | `UNKNOWN` |
| risk | sürüm/tepe mod tahmini |

**Doldurulacak alanlar**: sürüm · ses kanalları · EDID üzerinden mod · ölçen · tarih — `—`.

### Kart D-T07 · Dokunma katmanı

| Alan | Değer |
|---|---|
| tip_id | D-T07 |
| doğrulanacak | dokunma katmanının varlığı + teknolojisi (kapasitif/rezistif) |
| kapı | K-D4 (D-K05) · K-D7 (19 eşleşme ayrıştırması) |
| form | §6 D-K05 dokunma formu |
| beklenen kanıt | ölçüm (S1) + repo ayrıştırma (S2) |
| durum | `UNKNOWN` (19 eşleşme arayüz katmanı, donanım kanıtı değil) |
| risk | tasarım katmanını donanım sanmak |

**Doldurulacak alanlar**: teknoloji · kalibrasyon sapması · parmak sayısı · gecikme ·
ölçen · tarih — hepsi `—`.

### Kart D-T08 · Display controller

| Alan | Değer |
|---|---|
| tip_id | D-T08 |
| doğrulanacak | kontrolcü sınıfının varlığı ve sürücü bağımlılığı |
| kapı | K-D5 · K-D7 |
| form | §6 (kontrolcü satırı) + §7 sürüm formu |
| beklenen kanıt | repo sayımı (S2 — 0) + doküman (S3) |
| durum | `UNKNOWN` |
| risk | firmware/sürücü bağımlılığını tahmin etmek |

**Doldurulacak alanlar**: kontrolcü tipi · sürücü adı · firmware sürümü · ölçen · tarih — `—`.

---

## §13 EDID blok-analiz detay formu (K-D4 uzantısı)

> §4'teki özet formun detaylı sürümü — EDID blokları tek tek. **Hepsi boş.**

| Blok | Alan | Değer | Not |
|---|---|---|---|
| başlık | üretici 3-harf kodu | — | EDID'den okunacak |
| başlık | ürün kodu | — | |
| başlık | seri no | — | |
| başlık | üretim haftası/yılı | — | |
| genişletme | desteklenen renk alanı | — | |
| genişletme | ses desteği (var mı) | — | |
| genişletme | CEA-Mod listesi | — | |
| panel | EDID'de panel tipi yazımı | — | D-T01/D-T02/D-T03 ayrımı |
| panel | tercih edilen mod | — | |
| panel | refresh aralığı | — | |
| DDC/CI | MCCS sürümü | — | |
| DDC/CI | VCP okuma denemesi | — | |
| DDC/CI | VCP yazma denemesi | — | |
| DDC/CI | başarısızlık kodu (varsa) | — | |
| hotplug | tak gecikmesi (ms) | — | |
| hotplug | çıkar-gir tekrar testi | — | |
| ortam | işletim sistemi | — | ölçüm koşulu |
| ortam | kablo/port | — | ölçüm koşulu |
| ortam | ölçen | — | zorunlu |
| ortam | tarih | — | zorunlu |

**Kural**: 20 satırın hiçbirine tahmin yazılmaz; doldurulmayan `—` kalır; `0` yalnız gerçekten
sıfır okunduğunda.

---

## §14 Tip × kapı matrisi (hangi kapı hangi tipi kapatır)

| Tip \ Kapı | K-D3 | K-D4 | K-D5 | K-D6 | K-D7 |
|---|---|---|---|---|---|
| D-T01 LCD | — | ✔ (EDID) | ✔ | — | — |
| D-T02 LED LCD | — | — | ✔ | — | — |
| D-T03 OLED | — | ✔ (EDID) | ✔ | — | — |
| D-T04 GPU çıkışı | — | ✔ (link) | — | — | ✔ |
| D-T05 DisplayPort | — | ✔ (sürüm) | — | ✔ | — |
| D-T06 HDMI | — | ✔ (sürüm) | — | ✔ | — |
| D-T07 dokunma | — | ✔ (D-K05) | — | — | ✔ |
| D-T08 controller | — | — | ✔ | — | ✔ |

> K-D3 (anayasa revizesi) **tüm tipleri** bağlar — hiçbir tip o kapı kapalıyken `DOCUMENTED`
> etiketi alamaz; hepsi şu an `UNKNOWN`/`proposed`.

---

## §15 Tip bazlı risk kaydı (display)

| # | Tip | Risk | Etki | Olasılık | Azaltım | Kapı |
|---|---|---|---|---|---|---|
| R-D1 | D-T01/T02/T03 | panel tipi tahminiyle kayıt açmak | yanlış envanter | orta | EDID okuması zorunlu | K-D4 |
| R-D2 | D-T02/D-T03 | "LED ekran" söylemiyle OLED karıştırması | yanlış tip | orta | 4 eksenli ayrım (kapsam §5) | K-D5 |
| R-D3 | D-T04 | ekran görüntüsünden donanım sonucu çıkarmak | hayali envanter | yüksek | S2 sayımı + envanter kanıtı | K-D7 |
| R-D4 | D-T05/D-T06 | sürüm tahmini yazmak | doğrulanamayan iddia | orta | sürüm yalnız okunarak yazılır | K-D4 |
| R-D5 | D-T07 | arayüz katmanını donanım sanmak | çift sayım | yüksek | bileşen↔cihaz ayrımı | K-D5/K-D7 |
| R-D6 | D-T08 | firmware/sürücü bağımlılığını uydurmak | yanlış bağımlılık | orta | `VERIFY REQUIRED` etiketi | K-D7 |
| R-D7 | tümü | anayasa revizesi beklerken `DOCUMENTED` etiketi koymak | yetki aşımı | düşük | status=proposed disiplini | K-D3 |
| R-D8 | tümü | S4 kaynağı "okundu" gibi sunmak | kanıt şişirme | orta | ⚠️ okunmadı etiketi | K-D6 |

**Kural**: her risk satırı kapıyla eşleşir; kapı kapalıyken risk kapanmış sayılır ve bu tabloya
`KAPANDI (tarih)` notu düşülür — **bu oturumda hiçbir satır kapanmadı.**

---

## §16 Kapı kapanış kontrol listesi ( Vault Steward için — boş )

| # | Adım | Kapı | Tarih (doldurulacak) | İmza (doldurulacak) |
|---|---|---|---|---|
| 1 | anayasa §2/§10 metni revizesi onaylandı | K-D3 | — | — |
| 2 | donanaım-type-kapsam §2/A6, §7/3-T1, §12/M6 revizesi onaylandı | K-D3 | — | — |
| 3 | Q-T2 KAPANDI olarak işaretlendi | K-D3 | — | — |
| 4 | EDID/DDC ölçümü dolduruldu (D-K01) | K-D4 | — | — |
| 5 | arayüz link + piksel testi dolduruldu (D-K02/03) | K-D4 | — | — |
| 6 | giriş lag + dokunma testi dolduruldu (D-K04/05) | K-D4 | — | — |
| 7 | panel↔protokol ayrımı çapraz incelendi | K-D5 | — | — |
| 8 | 6 HEAD URL içerik başlığı okundu | K-D6 | — | — |
| 9 | VD-01 kararı verildi | K-D7 | — | — |

> Bu tablo **bilerek boştur** — 9 adımın hiçbiri bu oturumda atılmadı; atılmayan adımın
> tarihi/imzası yazılmaz.

---

## §17 Kapanış (bu dosyanın durumu)

| Öğe | Değer |
|---|---|
| Kapılar KAPALI | K-D1, K-D2, K-D8 (3/8) |
| Kapılar AÇIK | K-D3, K-D4, K-D5, K-D6, K-D7 (5/8) |
| S1 ölçüm satırı | **0** |
| Tip kartları (§12) | 8/8 yazıldı, 8/8 boş |
| HEAD: kullanıldı / reddedildi | 6 / 8 (4 G6 + 403/ERR hariç — bkz §9) |
| Etiket | `PROPOSED — APPROVAL REQUIRED` (K-D3) |
| git commit | bu oturumda **ATILMADI** (subagent taahhüdü) |

---

## §18 Kaynak → kapı eşlemesi (hangi kaynak hangi kapıyı besler)

| Kaynak (dosya) | Beslediği kapı | Ne getirir | Ne getirmez |
|---|---|---|---|
| `display-kapsam.md §0` | K-D1 | çatışma kaydı satırları | anayasa revizesi |
| `display-kapsam.md §6` (D-T01..08) | K-D5 | 4 eksenli tip modeli | ölçüm |
| `display-ornekler.md §2` (sayım) | K-D7 | 700 dosya/0 eşleşme (S2) | geçmiş/niyet |
| `display-ornekler.md §4–§9` (formlar) | K-D4 | doldurulacak formlar | dolum |
| `display-dogrulama §9` (HEAD) | K-D6 | erişim kanıtı (S4) | içerik |
| Vault Steward kararı (dış) | K-D3 | onay | — |
| ölçüm oturumu (dış, henüz yok) | K-D4 | S1 veri | — |

**Kural**: kapı, yalnız besleyicileri **dolu** olduğunda kapanır; bu eşleme tablosu hangi
boşluğun hangi kapıyı beklediğini gösterir — kapanış sihirbazı değil, haritadır.

---

## §19 Sürüm geçmişi (bu dosya)

| # | Tarih | Ne değişti | Değiştiren |
|---|---|---|---|
| 1 | 2026-10-13 | ilk yazım (§1–§19) | Vault Steward (subagent) |

> Gelecek revizyonlar bu tabloya eklenir; silme yasak (append-only).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
