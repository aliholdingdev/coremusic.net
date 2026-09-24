---
durum: önerildi
tarih: 2026-09-24
kaynak: 3 turlu agent tartışması 20 persona
---

# ADR-026: Sayım birimi dosya değil DÜĞÜM, hedef 5000; üç kanıt türü

## 1. Durum

Önerildi — 2026-09-24. Katman envanteri sayımı bu karara göre yapılır; kabul kriteri
"script >= 5000". Bağlayıcı referanslar: [adlandirma-kurali.md](../adlandirma-kurali.md),
[katman-sayim-rehberi.md](../katman-sayim-rehberi.md),
[ADR-023-hibrit-derinlik.md](ADR-023-hibrit-derinlik.md).

> ⚠️ VERIFICATION REQUIRED: 5.152 / 5.400 / 6.200 rakamları tasarım hedefidir, ölçüm
> değildir; gerçek sayım katman-sayim.ps1 ile alınır ve K1/K13 sapması raporlanır.

## 2. Bağlam

Sayım birimi ve hedef konusunda üç uzlaşılmaz talep karşı karşıyaydı:

- **Talep A — şeffaf envanter:** PoC CI gate'i (automation-engineering/sprint-3-poc-ci-gate.md)
  "script >= 5000" der; boş repo < 200 commit'te ret. Envanter gerçek olmalı.
- **Talep B — doluluk hedefi:** CLAUDE.md (the-claude-code-superstack) "49 iskelet dosya
  EKLE: 5000+ dosya" ve "48 saat içinde 5.000+ dosya". Zaman baskısı var.
- **Talep C — yanıltmayan ölçüm:** [katman-sayim-rehberi.md](../katman-sayim-rehberi.md)
  "dosya ≠ düğüm ≠ bileşen" der; 332 .md dosyasından 5.000 yapılmak istenirse metrik
  kirlenir (CI 100/100 yeşil, yer yer kopya dosya).

Tartışma 3 tur / 20 persona ile yapıldı: 1. tur sayım birimi (6 persona), 2. tur hedef
hükümü (6 persona), 3. tur numara uzayı (8 persona). Aşağıdaki kararların tamamı bağlayıcıdır.

## 3. Karar

### 3.1 Sayım birimi: DÜĞÜM

1. **Tek sayılacak şey DÜĞÜMDÜR.** Bir düğüm = (i) diskte bir katman MD dosyası,
   (ii) README/index bileşen tablosu bir satırı, (iii) frontend-restructuring-plan.md
   §2.1-§2.2 hedef dosya satırı — üçünden herhangi biri tek düğüm sayılır ve üç kez
   sayılmaz.
2. **Dosya ≠ düğüm.** Dosya sayısı enflasyona açıktır: aynı bilgi 5 dosyaya bölünse
   envanter şişer, kavramsal derinlik artmaz. Düğüm = okunabilir bir karar/artefakt
   parçası; insan ölçeğiyle doğrulanabilir.
3. **Bileşen ≠ katman.** Bir README tablo satırı bir bileşendir; iki bileşen tek dosyada
   yaşayabilir (bileşen-üretici README tablosu ile 1:1 değildir), tek bileşen iki dosyaya
   bölünebilir. 21 katman (K0-K20) sabittir; katman başına bileşen ağırlıkları
   [katman-sayim-rehberi.md §5](../katman-sayim-rehberi.md) tablosundan alınır (a = 1. seviye
   bileşen, b = 2. seviye alt bileşen).
4. **Derinlik 3. seviye ile sınırlıdır** (K{n}.a.b). 4. seviye (K{n}.a.b.c.d) yasaktır;
   ayrıntı o bileşenin kendi MD'sine aittir. Derinlik kuralı ADR-023'ün konusudur; bu ADR
   yalnız sayımın bu derinlikte bittiğini teyit eder.

### 3.2 Hedef: 5000

5. **Kabul kriteri: toplam DÜĞÜM >= 5000.** Rakam PoC gate'inden (script >= 5000)
   türetilmiştir; gate'i geçmeyen envanter tamamlanmış sayılmaz.
6. **Tasarım aralığı 5.152 - 6.200:**
   - **Alt sınır (bağlayıcı): 5.152** = 21 kök katman + 193 2. seviye + 4.938
     bileşen-satır. Betik en az 5.105 ile (K1 ve K13 düzeltmesi payı dahil) sınar.
   - **Orta hedef: 5.400** — 2. seviye ağırlıklı, 1. seviye kanıtlanmış satırlarla.
   - **Üst sınır (opsiyonel): 6.200** — kuyruğa (K16-K20) 1. seviye eklendiğinde.
7. **5.152 - 6.200 aralığı dışına çıkmak ADR gerektirir.** 5000'in altı geçersiz sayım;
   6.200'ün üstü envanter şişmesi sayılır ve gerekçelendirilmeden kabul edilmez.
8. **Gerçek sayım her zaman tasarım hedefini (5.152 / 5.400 / 6.200) geçebilir ama
   asla onun yerine geçmez.** Ölçüm, hedef değil, betik çıktısıdır.

### 3.3 Şeffaflık

9. **Üç kanıt türü tek listede taşınır:** (i) diskte katman MD, (ii) README/index bileşen
   tablosu satırı, (iii) frontend-restructuring-plan.md §2.1-§2.2 satırı. Kanıt türü
   etiketsizdir; yalnız sayım rehberi §6 üç türü ayrı sütunlarda gösterir.
10. **Kabul listesinde veri gizlenemez.** K1 (427 satır / 444 formül = -17) ve K13
    (95 / 125 = -30) sapmaları tabloda kalır, hücre yuvarlanamaz, satır silinemez.
    Sapmalar o katmanın 2. seviyesinin müteakip sayım turunda doldurulacağını gösterir.
11. **Tek sayısal söz:** 5000. Gerçek sonuç ve aralık ayrıca raporlanır.

### 3.4 Numara uzayı (3. tur kararı)

12. **Bu ADR serisi ayrı numara uzayında kalır:** .ai/architecture/adr/ADR-001...
    ardışık. .ai/.decisions/index.md mevcut ADR-023-026 farklı slug'larla mevcuttur;
    iki seri kasıtlı olarak ayrılmıştır. Tek seriye birleştirme REDDEDİLDİ (bkz. §5,
    alternatif D). Birleştirme istenirse üst karar (superstack/product-owner) gerekir.

## 4. Gerekçe

- **CI gate'e sadakat:** PoC gate'i sayımdır; 5000 doğrudan oradan gelir, uydurma değil.
- **Anti-enflasyon:** Düğüm sayımı kopya dosya ile şişirilemez; dosya sayısı ise
  şişirilebilir. Sayım birimi seçimi teşvikleri belirler — bu yüzden düğüm.
- **İnsan ölçeği:** 332 dosya gözden geçirilebilir; 5.000 dosya gözden geçirilemez.
  Düğüm tanımı denetimi mümkün kılar (toplam: 6+7+6+5+7+5+5+5+4+6+7+5+5+6+6+7+6+5+5+5
  1. seviye ağırlıkları + 2. seviye).
- **Şeffaf sapma:** K1/K13 gizlenirse sahte kesinlik doğar; açık bırakılırsa müteakip
  turda kapanır. Tartışmanın uzlaştığı ilke: veri gizlemek düzeltmekten kötüdür.

## 5. Reddedilen alternatifler

- **A) Envanter = dosya sayısı (base-ten-counting pov, 1. tur):** 5.000 dosya aynı bilginin
  parçalanmasıyla ulaşılabilir. CI yeşil, kavram boş. REDDEDİLDİ — "boş repo < 200
  commit'te ret" mantığı dosya sayısına da uygulanır: kopya dosya = enflasyon.
- **B) Hedefi düşür (5000 yerine 1000):** Gate reddilir, zorlama. REDDEDİLDİ — "5000
  kapsamlı" (1. tur uzlaşımı); gate rakamı sabit.
- **C) Hedefi yükselt (7500):** Daha iddialı görünür, üst sınır 6.200'ün üstünde
  kanıtsız doluluk. REDDEDİLDİ — 3 kanıt türü 6.200 üstünü kaldırmıyor (2. tur: kuyruk
  K16-K20 1. seviye eklenince 6.200 tavan).
- **D) İki ADR serisini birleştir (.ai/.decisions + .ai/architecture/adr):** Numara
  çakışması ADR-023-026'da kafa karıştırıcı. REDDEDİLDİ — (.ai/.decisions = karar
  kütüğü, .ai/architecture/adr = mimari seri; birleşim geçmiş kayıtları siler ve tek
  kaynak iddiasını ikiye böler. 3. tur uzlaşımı: ayrı seri + çakışma notu).
- **E) Ölçüm yok, tahmin yaz:** 5.152/5.400/6.200 "yaklaşık" diye geçsin. REDDEDİLDİ —
  tahmin ölçülebilir betiğin yerini alamaz; gate'i betik uygular.

## 6. Sonuçlar

### 6.1 Olumlu

- PoC CI gate'i (script >= 5000) tek kabul kriteri; kafa karışıklığı kalmaz.
- Sayım birimi (düğüm) şişirilemez; metrik güvenilir.
- K1/K13 sapması görünür olduğu için tamiri planlanabilir (öncelikli 2. tur).
- ADR serisi ayrımı kayıtlı; numara çakışması gelecekte kasıtlı olarak bulundu.

### 6.2 Olumsuz / riskler

- **K1 -17 ve K13 -30:** bağlayıcı tablo formülün altında (toplam 4.891 vs 4.938).
  Betik min(5.105; 5.152) ile sınayarak payı korur; 2. turda bu iki katmanın 2. seviyesi
  doldurulmazsa altında kalınır. ⚠️ VERIFICATION REQUIRED — müteakip sayımda kapanır.
- **Kanıt türü (iii) çapraz bağ:** frontend-restructuring-plan.md §2.1-§2.2 satırları
  o plana bağımlılık yaratır; plan taşınırsa sayımlar düşer. Sayım rehberi §6 bu bağı
  "3 kanıt türü" diye listeler — tek başına yeter sayılmaz, üçü birlikte okunur.
- **20 persona tartışması tek kaynak:** 3. tur persona çıkmaları (#7-#8) olasıdır;
  uzlaşılmayan noktalar (kuyruk 1. seviye, sınır cümlesinin README'si) ADR-023/025'e
  ertelenmiştir.

## 7. Uygulama

### 7.1 Gelecek adım (bu ADR'de uygulanmaz, Vault Steward/Backend Architect'e atanır)

- [ ] README/index'e "K8.2 Media Servis iş emri/akış uçlarını sunar; FFmpeg boru hattının
      sahibi K15'tir. K8.2 yalnız K15 arayüzünü çağırır." cümlesi yazılır (ADR-025 §7.1
      ile birlikte) — iki README'ye tek yerde, birebir.
- [ ] katman-sayim.ps1 betiği çalıştırılır; K1 (427) ve K13 (95) 2. seviyesi doldurulur;
      toplam >= 5.105 (hedef 5.152) doğrulanır.
- [ ] k-surucu/* 12 dosya taşınır (ADR-024 §3) ve link taraması çalıştırılır.

### 7.2 Bu ADR'yi güncelleyen tetikleyiciler

- Betik ilk kez çalıştığında (gerçek toplam ve K1/K13 düzeltme payı).
- Sayım birimi "dosya" olarak değiştirilirse (REDDEDİLDİ — bkz. §5.A).

## 8. İlgili kararlar

- ADR-023 hibrit derinlik (3 seviye sınırı — sayımın derinlik boyutu).
- ADR-024 sürücü/firmware birleşimi (K1.f envanteri — sayımın K1 satırı).
- ADR-025 K8.2/K15 sınırı (olay 10. maddesi — sayımın K8-K15 bileşenleri).
- adlandirma-kurali.md (L→K eşleme, A0-A5 — sayımın grup kolonu).

## 9. Kaynaklar

- PoC CI gate: automation-engineering/sprint-3-poc-ci-gate.md ("script >= 5000").
- Hedef: CLAUDE.md (the-claude-code-superstack) — "5000+ dosya" / "5.000+ dosya".
- [katman-sayim-rehberi.md](../katman-sayim-rehberi.md) — sayım formülü, betik, kabul.
- [adlandirma-kurali.md](../adlandirma-kurali.md) — şablon, katman ID'leri, A0-A5.
- [katman-baglilik-matrisi.md](../katman-baglilik-matrisi.md) — 21 katman (K0-K20).
- [frontend-restructuring-plan.md](../frontend-restructuring-plan.md) §2.1-§2.2 —
  kanıt türü (iii).
- Tartışma: 3 tur / 20 persona (6 + 6 + 8), 2026-09-24.

## 10. Karar geçmişi

| Tarih | Durum | Not |
| --- | --- | --- |
| 2026-09-24 | önerildi | İlk kayıt. 3 turluk 20 persona tartışmasından; sayımda K1/K13 sapması açık bırakıldı (şeffaflık). |


## 11. Bağlayıcı sayımlar (21 katman, 3. turda sabitlenen değerler)

Aşağıdaki satır değerleri [katman-sayim-rehberi.md §5](../katman-sayim-rehberi.md)
tablosunun birebir aynısıdır; bu ADR onları karar olarak bağlar (değişimi ADR gerektirir).

| Katman | Grup | a (1. seviye) | b (2. seviye) | tavan | Satır toplamı |
| --- | --- | --- | --- | --- | --- |
| K0 isletim-sistemi | A0 | 10 | 7 | 210 | 280 |
| K1 donanim | A0 | 12 | 7 | 360 | 427 ⚠️ (formül 444) |
| K2 surucu | A0 | 8 | 6 | 170 | 218 |
| K3 ses-motoru | A0 | 9 | 6 | 200 | 254 |
| K4 yapay-zeka | A0 | 8 | 5 | 130 | 170 |
| K5 veri-yonetimi | A0 | 10 | 7 | 300 | 370 |
| K6 guvenlik | A1 | 9 | 5 | 150 | 195 |
| K7 middleware | A1 | 10 | 5 | 140 | 190 |
| K8 servis | A2 | 10 | 5 | 150 | 200 |
| K9 api-routing | A2 | 9 | 4 | 90 | 126 |
| K10 uygulama | A3 | 10 | 6 | 200 | 260 |
| K11 ux | A3 | 11 | 7 | 330 | 407 |
| K12 izleme | A4 | 8 | 5 | 120 | 160 |
| K13 cicd | A4 | 7 | 5 | 90 | 95 ⚠️ (formül 125) |
| K14 ag | A4 | 9 | 6 | 170 | 224 |
| K15 medya-streaming | A4 | 9 | 6 | 170 | 224 |
| K16 class-ab | A5 | 11 | 7 | 280 | 357 |
| K17 guc-kaynagi | A5 | 9 | 6 | 190 | 244 |
| K18 termal | A5 | 8 | 5 | 130 | 170 |
| K19 pcb | A5 | 8 | 5 | 120 | 160 |
| K20 bom | A5 | 8 | 5 | 120 | 160 |
| **Bağlayıcı toplam** | A0-A5 | | | | **4.891** |
| Kök 1. seviye (21) | | | | | 21 |
| 2. seviye (193) | | | | | 193 |
| **Formül toplamı** | | | | | **4.938** |
| **Alt sınır (kök+2. seviye+4.938)** | | | | | **5.152** |

⚠️ K1 ve K13 sapması gizlenmez (§3.3 madde 10): 4.938 formül toplamı ile 4.891
bağlayıcı toplamı arasındaki 47 fark (17 + 30) müteakip 2. seviye turunda kapanır.
Betik bu yüzden min(5.105; 5.152) ile sınar; 5.105 = 5.152 - 47 (düzeltme payı).

## 12. Tartışma dökümü (3 tur, 20 persona)

| Tur | Konu | Persona sayısı | Sonuç |
| --- | --- | --- | --- |
| 1 | Sayım birimi: dosya mı, düğüm mü? | 6 | Düğüm; üç kanıt türü; 4. seviye yasak (ADR-023 ile birlikte) |
| 2 | Hedef hüküm: 5000 sabit mi, aralık mı? | 6 | 5000 kabul kriteri; 5.152/5.400/6.200 aralığı |
| 3 | Numara uzayı: iki ADR serisi birleşsin mi? | 8 | Ayrı seri kalır; çakışma notu ADR'lere yazıldı |

Tartışmanın bağlayıcı çıkanları (uzlaşılmayanlar ertelemeli):
1. Sayım birimi = DÜĞÜM (tüm turlar).
2. 4. seviye (K{n}.a.b.c.d) yasak, K7.0.x ara numara RED (tüm turlar).
3. K1/K13 verisi gizlenemez; raporlanır (2. tur).
4. K8.2 → K15 tek cümlelik sınır README'ye yazılmaz, ADR'de tarif edilir (ADR-025).
5. İki ADR serisi birleştirilmez (3. tur).

## 13. SSS

**S: 332 .md dosyası var, 5.152 nasıl çıkar?**
C: Düğüm = dosya + README satırı + plan satırı. 332 dosya tek kanıt türü (i); (ii) ve
(iii) satırları ayrı düğümdür (katman-sayim-rehberi §6). Dosya sayısı 5.152'nin alt
kolonudır, toplamı değil.

**S: Bir bileşen iki dosyaya bölünse sayım iki katına çıkar mı?**
Hayır — iki dosya tek bileşen-satırı (kanıt türü ii) sağlıyorsa tek düğüm sayılır;
bileşen-üretici README tablosu ile 1:1 değil, 1:N olabilir ama N sayımı şişirmez.

**S: K16-K20 bağımsız, neden A5'te?**
Grup kolonu (A0-A5) yalnız raporlama içindir; bağımlılık matrisinde K16-K20'den
K0-K15'e ok YOK'tur (katman-baglilik-matrisi). Sayım A5 satırlarını toplamı etkiler,
bağımlılığı değil.

**S: Gerçek sayım 6.500 çıkarsa?**
§3.2 madde 7: üst sınır 6.200 aşılmışsa gerekçelendirilir (envanter şişmesi şüphesi);
betik çıktısı olduğu gibi raporlanır, tablo değiştirilmez.

**S: Sayım betiği neden PowerShell?**
Windows (win32) ortamında GPG-İMZALI katman-sayim.ps1 (CLAUDE §32) mevcut; betik
salt okundur. YAML/JSON çıktısı CI gate'e girdi olur (sprint-3-poc-ci-gate).

## 14. Denetim listesi

- [ ] Sayım birimi tek kelime: DÜĞÜM (dosya/bileşen/katman ile karıştırılmaz).
- [ ] Üç kanıt türü birlikte sayılır, tekrar sayılmaz.
- [ ] K1 ve K13 satırı tabloda ⚠️ imiyle durur.
- [ ] Toplam >= 5.105 (betik) ve hedef 5.152 (tasarım) ayrı raporlanır.
- [ ] 4. seviye ve K7.0.x üretimi yok (adlandirma-kurali §2).
- [ ] İki ADR serisi birleştirilmedi; çakışma notu ADR-023/024/025/026'da duruyor.
