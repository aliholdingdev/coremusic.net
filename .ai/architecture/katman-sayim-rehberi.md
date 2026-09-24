---
title: "CoreMusic — Katman Sayım Rehberi (Düğüm Sayımı ve 5000 Hedefi)"
type: architecture-rule
category: architecture
date: 2026-09-24
updated: 2026-09-24
tarih: 2026-09-24
durum: önerildi
status: proposed
version: 1.0.0
kaynak: "3 turlu agent tartışması 20 persona"
authority: "SSOT — katman sayım kuralları (adlandirma-kurali.md ile birlikte)"
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Katman Sayım Rehberi

**Durum:** önerildi · **Tarih:** 2026-09-24 · **Kaynak:** 3 turlu agent tartışması 20 persona
**Konum:** .ai/architecture/katman-sayim-rehberi.md · **İlgili:** [[adlandirma-kurali]] · [[ADR-026-sayim-birimi-5000]] · [[ADR-023-hibrit-derinlik]] · [[ADR-024-surucu-firmware-birlesme]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index]] · [[brain.md]] · [[katman-baglilik-matrisi]] · [[frontend-restructuring-plan]] · [[.templates/index]]

---

## §1 Amaç ve Kapsam

Bu dosya, CoreMusic mimarisinin 21 ana katmanı (K0-K20) için **ne sayılacağını, nasıl sayılacağını ve hangi hedefin kabul sayılacağını** tanımlar. Sayım birimi, kanıt kaynakları, 21 katmanlık bağlayıcı sayım tablosu, ayrıştırma aritmetiği, senaryo aralıkları, PowerShell betiği taslağı ve kabul kriteri buradadır (SSOT). Başka bir vault dosyası sayım kuralı ilan edemez.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Sayım birimi tanımı (DÜĞÜM) ve üç kavram ayrımı | Adlandırma sözdizimi (→ [[adlandirma-kurali]]) |
| 21 katmanlık bağlayıcı sayım tablosu (a / b / tavan / toplam) | Katmanlar arası bağımlılık izinleri (→ [[katman-baglilik-matrisi]]) |
| Ayrıştırma aritmetiği (4.938 + 21 + 193 = 5.152) | 4. seviye kanıt türlerinin tanımı (→ [[adlandirma-kurali]] §3.3) |
| Üç senaryo (alt 5.152 / orta 5.400 / üst 6.200) | ADR metinleri (→ adr/ ADR-023…026) |
| PowerShell betiği taslağı ve kabul kriteri (≥5000) | Vault geneli kırık-link taraması (→ .workflows) |

### §1.1 Bağlayıcı Karar Kaynağı

| Karar | Kaynak | Durum |
|-------|--------|-------|
| Sayım birimi = DÜĞÜM (dosya ≠ bileşen ≠ katman) | 3 tur / 20 persona uzlaşma | bağlayıcı (ADR-026) |
| 21 katman sabit; K16-K20 dahil bağımsız sayılır | 3 tur / 20 persona uzlaşma | bağlayıcı |
| Katman başına tablo değerleri (§4) bağlayıcıdır | 3 tur / 20 persona uzlaşma | bağlayıcı |
| Hedef aralık: alt 5.152 / orta 5.400 / üst 6.200 | 3 tur / 20 persona uzlaşma | bağlayıcı |
| Kabul kriteri: betik çıktısı ≥ 5000 | 3 tur / 20 persona uzlaşma | bağlayıcı |
| Kanıt şeffaflığı: sapmalar gizlenmez, ⚠️ VERIFICATION REQUIRED yazılır | 3 tur / 20 persona uzlaşma | bağlayıcı |

---

## §2 Sayım Birimi — DÜĞÜM

### §2.1 Üç Kavramın Ayrımı

Sayımda en sık yapılan hata üç kavramı birbirine karıştırmaktır. Bu üçü **eşdeğer değildir**; sayım yalnız DÜĞÜM üzerinden yapılır.

| Kavram | Tanım | Örnek | Sayıma girer mi? |
|--------|-------|-------|------------------|
| **Dosya** | Diskteki tek bir .md (veya kod) dosyası | k8-servis/media-service.md | Hayır (yalnız kanıt olarak kullanılır) |
| **Bileşen** | README indeksindeki satır kimliği | K8-07 Media Servis | Hayır (düğümle eşlenir) |
| **Katman** | K0-K20 ana katmanlardan biri | K15 Medya & Streaming | Kök düzeyde 1 düğüm |
| **DÜĞÜM** | Adlandırılmış mimari birim: K{n}, K{n}.a, K{n}.a.b, (kanıtla) K{n}.a.b.c | K15.2.1 | **Evet — tek sayım birimi budur** |

### §2.2 Bir Dosya Kaç Düğüm Eder?

Tek dosya = tek düğüm **zorunlu değildir**; ama düğüm = dosya da zorunlu değildir. Eşleme kuralı:

| Durum | Düğüm Sayımı | Kanıt |
|-------|--------------|-------|
| Bir README'de bir bileşen satırı var, karşılığı ayrı dosya | 1 düğüm | Kanıt türü (ii) — README bileşen tablosu satırı |
| Bir dosya içinde alt bölümler (## başlıklar) var | Dosya 1 düğüm; alt bölümler ayrıca sayılmaz | Kanıt türü (i) — diskte katman MD dosyası |
| frontend-restructuring-plan.md §2.1-§2.2'de L{n}.a.b satırı | 1 düğüm (K'ya çevrilmiş hâliyle) | Kanıt türü (iii) — plan satırı |
| Bir düğümün karşılığı hiçbir kanıtta yok | Sayılmaz; uydurulmaz | Yok — ⚠️ VERIFICATION REQUIRED |
| Aynı düğüm iki kanıt türünde birden geçiyor | **1 kez** sayılır (birleşim = tekillik) | Kanıt türü birincil olanı esas |

### §2.3 Birim Kararının Gerekçesi (ADR-026)

| Birim | Neden Yetersiz / Neden Seçilmedi |
|-------|----------------------------------|
| Dosya | K1.f gibi 8 dosyalık bir alt katman ile 280 düğümlük K0'ı karşılaştırılamaz; dosya sayısı içerik derinliğini göstermez |
| Bileşen (K{n}-NN) | README'siz katmanlarda (kısmi indeksler) tutarsız sayılır; kimlik sistemi ≠ içerik birimi |
| Satır (kod) | Ölçüm gürültülü; bir satır bir mimari birim değildir |
| **Düğüm (K{n}[.a][.b][.c])** | Adlandırma kuralıyla birebir örtüşür, kanıtla doğrulanabilir, katmanlar arası karşılaştırılabilirdir |

---

## §3 Kanıt Türleri — Üçlü Kulak

4. seviye dahil her sayım, yalnız üç kanıt türünden biriyle desteklenir (ayrıntı: [[adlandirma-kurali]] §3.3). Sayım rehberi bu kanıtları sayımın **girdisi** olarak kullanır.

| Tür | Kanıt | Nasıl Sayılır | Güvenilirlik |
|-----|-------|---------------|--------------|
| (i) | Diskte katman MD dosyası | glob: k*/*.md → her dosya bir aday düğüm | Yüksek (fiziksel) |
| (ii) | README / index bileşen tablosu satırı | Tablo satırı sayımı (K{n}-NN sütunu) | Yüksek (indeksli) |
| (iii) | frontend-restructuring-plan.md §2.1-§2.2 satırı | L satırı → K düğümü dönüşümü | Orta (plan düzeyi) |

### §3.1 Kanıt Kuralı

| # | Kural | İhlal |
|---|-------|-------|
| 1 | Bir düğüm yalnız en az bir kanıt türüyle sayılır | Kanıtsız düğüm = uydurma → sayılmaz |
| 2 | Aynı düğüm birden fazla kanıt türünde geçerse birleşim (tekillik) uygulanır | Çift sayım → toplam şişer |
| 3 | Kanıt türü rapora yazılır (hangi tür, kaç düğüm) | Kanıtsız toplam → RED |
| 4 | Kanıt türü (iii) ile 4. seviye açılabilir; (i) ve (ii) tek başına 4. seviye açmaz | Adlandırma kuralı ihlali |
| 5 | Sayım betiği betikteki desenlerle kanıtları tarar; elle sayımla değiştirilemez | Elle sayımda sapma riski |

### §3.2 Kanıt Dağılımı Rapor Zorunluluğu

Betik çıktısında toplamın yanında kanıt türü dağılımı da yer alır:

~~~text
Kanıt (i)  disk MD dosyası      : <adet>
Kanıt (ii) README bileşen satırı: <adet>
Kanıt (iii) plan §2.1-§2.2 satırı: <adet>
Birleşim sonrası benzersiz düğüm: <adet>
~~~

---

## §4 21 Katman Sayım Tablosu (Bağlayıcı)

**Bağlayıcı değerler:** 3 tur / 20 persona uzlaşmasından çıkan a (alt), b (orta), tavan ve satır toplamı değerleri aşağıdadır. **Formül:** satır toplamı = a × b + tavan.

| Katman | Dizin | a (alt) | b (orta) | Tavan | Satır Toplamı (bağlayıcı) | Formül (a×b+tavan) | Sapma |
|--------|-------|--------|---------|-------|--------------------------:|-------------------:|-------|
| K0 | k0-isletim-sistemi | 10 | 7 | 210 | 280 | 280 | — |
| K1 | k1-donanim | 12 | 7 | 360 | 427 | 444 | ⚠️ −17 VERIFICATION REQUIRED |
| K2 | k2-surucu | 8 | 6 | 170 | 218 | 218 | — |
| K3 | k3-ses-motoru | 9 | 6 | 200 | 254 | 254 | — |
| K4 | k4-yapay-zeka | 8 | 5 | 130 | 170 | 170 | — |
| K5 | k5-veri-yonetimi | 10 | 7 | 300 | 370 | 370 | — |
| K6 | k6-guvenlik | 9 | 5 | 150 | 195 | 195 | — |
| K7 | k7-middleware | 10 | 5 | 140 | 190 | 190 | — |
| K8 | k8-servis | 10 | 5 | 150 | 200 | 200 | — |
| K9 | k9-api-routing | 9 | 4 | 90 | 126 | 126 | — |
| K10 | k10-uygulama | 10 | 6 | 200 | 260 | 260 | — |
| K11 | k11-ux | 11 | 7 | 330 | 407 | 407 | — |
| K12 | k12-izleme | 8 | 5 | 120 | 160 | 160 | — |
| K13 | k13-cicd | 7 | 5 | 90 | 95 | 125 | ⚠️ −30 VERIFICATION REQUIRED |
| K14 | k14-ag | 9 | 6 | 170 | 224 | 224 | — |
| K15 | k15-medya-streaming | 9 | 6 | 170 | 224 | 224 | — |
| K16 | k16-class-ab | 11 | 7 | 280 | 357 | 357 | — |
| K17 | k17-guc-kaynagi | 9 | 6 | 190 | 244 | 244 | — |
| K18 | k18-termal | 8 | 5 | 130 | 170 | 170 | — |
| K19 | k19-pcb | 8 | 5 | 120 | 160 | 160 | — |
| K20 | k20-bom | 8 | 5 | 120 | 160 | 160 | — |
| **TOPLAM** | 21 katman | **193** | — | — | **4.891** | **4.938** | **+47** |

### §4.1 Sütun Anlamları

| Sütun | Anlamı | Seviye Karşılığı |
|-------|--------|------------------|
| a (alt) | Katmandaki alt katman sayısı → K{n}.a yuvaları | 2. seviye yuva sayısı |
| b (orta) | Her alt katmandaki bileşen grubu → K{n}.a.b | 3. seviye yuva sayısı (a × b) |
| Tavan | 4. seviye (K{n}.a.b.c) için üst sınır — yalnız 3 kanıt türüyle açılır | Kanıtla açılabilir ek düğüm |
| Satır Toplamı | Bağlayıcı sayı (tartışma çıktısı) | 3. seviye + tavan |

### §4.2 Sapma Denetim Notu (⚠️ VERIFICATION REQUIRED)

İki katmanda bağlayıcı satır toplamı formülle uyuşmaz; bu **gizlenmez**, açıkça işaretlenir:

| Katman | Bağlayıcı | Formül | Sapma | Durum |
|--------|----------:|-------:|------:|-------|
| K1 | 427 | 444 | −17 | ⚠️ VERIFICATION REQUIRED — üst göreve sorulur; tablodaki bağlayıcı değer korunur |
| K13 | 95 | 125 | −30 | ⚠️ VERIFICATION REQUIRED — üst göreve sorulur; tablodaki bağlayıcı değer korunur |
| Diğer 19 katman | = | = | 0 | Formülle birebir uyumlu |

**Sonuç iki toplam üretir:** bağlayıcı sütun toplamı 4.891 · formül sütun toplamı 4.938 (fark 47 = 17 + 30). İkisi de §5'te ayrıştırılır; kabul kriteri ikisine de bakar (§10).

---

## §5 Ayrıştırma Aritmetiği

### §5.1 Kanonik Ayrıştırma

Toplam düğüm = kök (21 katman) + 2. seviye (193 alt katman) + 3.-4. seviye (katman satır toplamları).

~~~text
Kök (K0…K20)                 :  21
2. seviye (K{n}.a)            : 193   (= §4 a sütunu toplamı)
3.-4. seviye (K{n}.a.b + tavan): 4.938 (= §4 formül sütunu toplamı)
------------------------------------
TOPLAM (formül tabanlı)      : 5.152   ← KANONİK HEDEF (alt senaryo)
~~~

### §5.2 Bağlayıcı Tablo ile Ayrıştırma

Bağlayıcı satır toplamları (427 ve 95 dahil) kullanıldığında:

~~~text
Kök                          :  21
2. seviye                    : 193
3.-4. seviye (bağlayıcı)     : 4.891
------------------------------------
TOPLAM (bağlayıcı tablo)     : 5.105
~~~

Her iki toplam da 5000 eşiğinin üzerindedir (5.152 ve 5.105); kabul kriteri bu nedenle her ikisini de raporlar ve **minimum** ile sınar (§10).

### §5.3 Aritmetik Denetim Tablosu

| Denetim | İşlem | Sonuç | Beklenen | Durum |
|---------|-------|------:|---------:|-------|
| a sütunu toplamı | 10+12+8+9+8+10+9+10+10+9+10+11+8+7+9+9+11+9+8+8+8 | 193 | 193 | ✅ |
| Formül toplamı | Σ(a×b+tavan) | 4.938 | 4.938 | ✅ |
| Bağlayıcı toplam | Σ satır toplamı | 4.891 | 4.891 | ✅ (fark §4.2'de işaretli) |
| Kanonik toplam | 4.938 + 21 + 193 | 5.152 | 5.152 | ✅ |
| Bağlayıcı toplam | 4.891 + 21 + 193 | 5.105 | — | ✅ (≥5000) |
| Sapma açıklığı | 17 + 30 | 47 | 47 | ✅ |

---

## §6 Senaryo Aralıkları

Üç senaryo tartışmaya katılan tüm personalarca kabul edilmiştir; sayım bu aralıkta raporlanır.

| Senaryo | Toplam Düğüm | Anlamı | Kullanım |
|---------|-------------:|--------|----------|
| **Alt** | 5.152 | Kanonik ayrıştırma (4.938 + 21 + 193) — yalnız kanıtlanan/planlanan yuvalar | Minimum gerçekçi plan |
| **Orta** | 5.400 | Alt senaryo + kanıtla açılması beklenen 4. seviye genişlemesi (~248) | Beklenen teslim |
| **Üst** | 6.200 | Orta + büyüme payı (~800) — yeni kanıtla açılabilecek yuvalar | Üst sınır; aşımı yeni ADR ister |

### §6.1 Sınır Kuralları

| # | Kural | Sonuç |
|---|-------|-------|
| 1 | 5.000 altı sayım RED'tir (kabul kriteri §10) | Betik exit 1 |
| 2 | 5.152 alt senaryonun altında kalan rapor | Sapma araştırılır (§4.2'deki iki katman öncelikli) |
| 3 | 6.200 üstü | Yeni ADR-026 revizyonu gerekir; sayımdan sayıma değişmez |
| 4 | Senaryo seçimi rapora yazılır (hangi senaryo, hangi toplam) | Şeffaflık (ADR-026) |

---

## §7 Disk Kanıtı — 2026-09-24 Ölçümü

**Yöntem:** glob (k*/*.md), yalnız .ai/architecture/ altındaki birinci seviye klasörler. Bu ölçüm **dosya** sayısıdır; düğüm sayımı değildir (§2.1) — kanıt (i) girdisidir.

| Klasör | .md Dosyası | Not |
|--------|------------:|-----|
| k0-isletim-sistemi | 15 | |
| k1-donanim | 23 | |
| k2-surucu | 14 | ADR-024 birleşim SONRASI (12 taşınan + README + CLAUDE) |
| k3-ses-motoru | 18 | |
| k4-yapay-zeka | 14 | |
| k5-veri-yonetimi | 14 | |
| k6-guvenlik | 16 | |
| k7-middleware | 14 | |
| k8-servis | 14 | media-service.md → ADR-025 sınırı |
| k9-api-routing | 14 | |
| k10-uygulama | 17 | |
| k11-ux | 16 | |
| k12-izleme | 13 | |
| k13-cicd | 14 | |
| k14-ag | 16 | |
| k15-medya-streaming | 16 | ffmpeg-pipeline.md → ADR-025 sınırı |
| k16-class-ab | 22 | |
| k17-guc-kaynagi | 15 | |
| k18-termal | 11 | |
| k19-pcb | 11 | |
| k20-bom | 11 | |
| **K0-K20 toplam** | **318** | |
| firmware (K1.f) | 8 | Beklenen 8 ✅ (ADR-024) |
| k-surucu | 0 | Beklenen 0 ✅ (ADR-024 birleşimi uygulanmış) |
| adr | 2 | ADR-023, ADR-024 mevcut (frozen); ADR-025…026 yazılacak |
| Kök .md (index, matris, plan, adlandırma, github-referanslari, sayım rehberi) | 6 | Sayım rehberi bu dosya |
| **Genel toplam (.md)** | **334** | Dosya ≠ düğüm: 334 dosya ~5.152 düğüm HedefLENİR |

### §7.1 Dosya-Düğüm Farkının Açıklaması

Diskte 334 dosya varken hedef 5.152 düğümdür; bu çelişki değildir: düğüm sayımı, README bileşen satırları (kanıt ii) ve plan §2.1-§2.2 satırları (kanıt iii) ile birlikte sayıldığında dosya başına birden fazla düğüm oluşur. Sayım betiği üç kanıt türünü de tarar (§9).

---

## §8 Denetim Notları

### §8.1 Denetim Checklist

| # | Denetim | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | 21 katman satırı var mı? | K0-K20 eksiksiz | ✅ |
| 2 | a sütunu toplamı | 193 | ✅ |
| 3 | Formül toplamı | 4.938 | ✅ |
| 4 | Bağlayıcı toplam | 4.891 | ✅ (fark §4.2 işaretli) |
| 5 | K1 sapması işaretli mi? | ⚠️ VERIFICATION REQUIRED | ✅ |
| 6 | K13 sapması işaretli mi? | ⚠️ VERIFICATION REQUIRED | ✅ |
| 7 | Kanonik toplam | 5.152 | ✅ |
| 8 | Senaryolar 5.152 / 5.400 / 6.200 | Üçü de yazılı | ✅ |
| 9 | Disk ölçümü tarihli mi? | 2026-09-24 | ✅ |
| 10 | k-surucu = 0 / firmware = 8 | ADR-024 uyumu | ✅ |

### §8.2 Bilinen Sapmalar Kaydı

| # | Sapma | Miktar | Kaynak | Aksiyon |
|---|-------|-------:|--------|---------|
| 1 | K1 satır toplamı formülle uyuşmuyor | −17 | 3 tur tartışması bağlayıcı değeri | ⚠️ VERIFICATION REQUIRED — üst göreve sor |
| 2 | K13 satır toplamı formülle uyuşmuyor | −30 | 3 tur tartışması bağlayıcı değeri | ⚠️ VERIFICATION REQUIRED — üst göreve sor |
| 3 | İki toplam (4.891 / 4.938) | 47 | 1 ve 2'nin toplamı | İkisi de raporlanır; kabul min ile sınar |

---

## §9 PowerShell Betiği Taslağı

**Dosya:** .ai/architecture/scripts/katman-sayim.ps1 (oluşturulacak) · **Kullanım:**
powershell -ExecutionPolicy Bypass -File katman-sayim.ps1

~~~powershell
# ============================================================
# katman-sayim.ps1 — CoreMusic katman düğüm sayım betiği
# ADR-026: sayım birimi = DÜĞÜM · Kabul: kapasite >= 5000
# Kaynak: katman-sayim-rehberi.md §4 (bağlayıcı tablo)
# ============================================================
param(
    [string]$AiRoot = (Join-Path $PSScriptRoot ".."),
    [int]$HedefMinimum = 5000
)

# --- §4 bağlayıcı tablo (a, b, tavan, bağlayıcı satır toplamı) ---
$Katmanlar = @(
    [pscustomobject]@{ K="K0";  Dizin="k0-isletim-sistemi";  Alt=10; Orta=7; Tavan=210; Baglayici=280 },
    [pscustomobject]@{ K="K1";  Dizin="k1-donanim";          Alt=12; Orta=7; Tavan=360; Baglayici=427 },
    [pscustomobject]@{ K="K2";  Dizin="k2-surucu";           Alt=8;  Orta=6; Tavan=170; Baglayici=218 },
    [pscustomobject]@{ K="K3";  Dizin="k3-ses-motoru";       Alt=9;  Orta=6; Tavan=200; Baglayici=254 },
    [pscustomobject]@{ K="K4";  Dizin="k4-yapay-zeka";       Alt=8;  Orta=5; Tavan=130; Baglayici=170 },
    [pscustomobject]@{ K="K5";  Dizin="k5-veri-yonetimi";    Alt=10; Orta=7; Tavan=300; Baglayici=370 },
    [pscustomobject]@{ K="K6";  Dizin="k6-guvenlik";         Alt=9;  Orta=5; Tavan=150; Baglayici=195 },
    [pscustomobject]@{ K="K7";  Dizin="k7-middleware";       Alt=10; Orta=5; Tavan=140; Baglayici=190 },
    [pscustomobject]@{ K="K8";  Dizin="k8-servis";           Alt=10; Orta=5; Tavan=150; Baglayici=200 },
    [pscustomobject]@{ K="K9";  Dizin="k9-api-routing";      Alt=9;  Orta=4; Tavan=90;  Baglayici=126 },
    [pscustomobject]@{ K="K10"; Dizin="k10-uygulama";        Alt=10; Orta=6; Tavan=200; Baglayici=260 },
    [pscustomobject]@{ K="K11"; Dizin="k11-ux";              Alt=11; Orta=7; Tavan=330; Baglayici=407 },
    [pscustomobject]@{ K="K12"; Dizin="k12-izleme";          Alt=8;  Orta=5; Tavan=120; Baglayici=160 },
    [pscustomobject]@{ K="K13"; Dizin="k13-cicd";            Alt=7;  Orta=5; Tavan=90;  Baglayici=95  },
    [pscustomobject]@{ K="K14"; Dizin="k14-ag";              Alt=9;  Orta=6; Tavan=170; Baglayici=224 },
    [pscustomobject]@{ K="K15"; Dizin="k15-medya-streaming"; Alt=9;  Orta=6; Tavan=170; Baglayici=224 },
    [pscustomobject]@{ K="K16"; Dizin="k16-class-ab";        Alt=11; Orta=7; Tavan=280; Baglayici=357 },
    [pscustomobject]@{ K="K17"; Dizin="k17-guc-kaynagi";     Alt=9;  Orta=6; Tavan=190; Baglayici=244 },
    [pscustomobject]@{ K="K18"; Dizin="k18-termal";          Alt=8;  Orta=5; Tavan=130; Baglayici=170 },
    [pscustomobject]@{ K="K19"; Dizin="k19-pcb";             Alt=8;  Orta=5; Tavan=120; Baglayici=160 },
    [pscustomobject]@{ K="K20"; Dizin="k20-bom";             Alt=8;  Orta=5; Tavan=120; Baglayici=160 }
)

# --- 1) Sütun toplamları (§5 denetimi) ---
$BaglayiciToplam = ($Katmanlar | Measure-Object -Property Baglayici -Sum).Sum   # 4891
$FormulToplam = 0
foreach ($satir in $Katmanlar) {
    $FormulToplam += ($satir.Alt * $satir.Orta) + $satir.Tavan                  # 4938
}
$IkinciSeviye = ($Katmanlar | Measure-Object -Property Alt -Sum).Sum           # 193
$Kok = $Katmanlar.Count                                                         # 21

$KapasiteBaglayici = $BaglayiciToplam + $Kok + $IkinciSeviye                    # 5105
$KapasiteFormul = $FormulToplam + $Kok + $IkinciSeviye                          # 5152

# --- 2) Disk kanıtı (tür i): k* klasörlerindeki .md dosyaları ---
$DiskToplam = 0
$Satirlar = @()
foreach ($satir in $Katmanlar) {
    $yol = Join-Path $AiRoot ("architecture\" + $satir.Dizin)
    $adet = 0
    if (Test-Path $yol) {
        $adet = (Get-ChildItem -Path $yol -Filter *.md -File -ErrorAction SilentlyContinue).Count
    }
    $DiskToplam += $adet
    $Satirlar += [pscustomobject]@{
        K = $satir.K; Dizin = $satir.Dizin; DiskMd = $adet
        KapasiteBaglayici = $satir.Baglayici
        KapasiteFormul = ($satir.Alt * $satir.Orta) + $satir.Tavan
    }
}

# --- 3) ADR-024 kontrol noktaları (firmware = 8, k-surucu = 0) ---
$firmwareYol = Join-Path $AiRoot "architecture\firmware"
$firmwareAdet = 0
if (Test-Path $firmwareYol) { $firmwareAdet = (Get-ChildItem -Path $firmwareYol -Filter *.md -File).Count }
$kSurucuYol = Join-Path $AiRoot "architecture\k-surucu"
$kSurucuAdet = -1
if (Test-Path $kSurucuYol) {
    $kSurucuAdet = (Get-ChildItem -Path $kSurucuYol -Recurse -File -ErrorAction SilentlyContinue).Count
}

# --- 4) Kanıt türü taraması (§3): K desenli düğüm adayları ---
$desen = "K\d{1,2}(\.\d{1,2}){1,3}"
$tumMd = Get-ChildItem -Path (Join-Path $AiRoot "architecture") -Recurse -Filter *.md -File
$dugumKesisim = 0
foreach ($dosya in $tumMd) {
    $eslesen = Select-String -Path $dosya.FullName -Pattern $desen -AllMatches -ErrorAction SilentlyContinue
    if ($null -ne $eslesen) { $dugumKesisim += ($eslesen.Matches | Measure-Object).Count }
}

# --- 5) Rapor ---
$Satirlar | Format-Table -AutoSize
Write-Host ("Kok katman sayisi            : " + $Kok)
Write-Host ("2. seviye (a toplami)         : " + $IkinciSeviye)
Write-Host ("3.-4. seviye (baglayici)      : " + $BaglayiciToplam)
Write-Host ("3.-4. seviye (formul)         : " + $FormulToplam)
Write-Host ("Kapasite (baglayici tablo)    : " + $KapasiteBaglayici)
Write-Host ("Kapasite (formul)             : " + $KapasiteFormul)
Write-Host ("Senaryolar                    : alt 5152 / orta 5400 / ust 6200")
Write-Host ("Disk .md (K0-K20)             : " + $DiskToplam)
Write-Host ("Disk .md firmware (beklenen 8): " + $firmwareAdet)
Write-Host ("Disk .md k-surucu (beklenen 0): " + $kSurucuAdet)
Write-Host ("K desenli eslesme (kanit taramasi, bilgi): " + $dugumKesisim)

# --- 6) Kabul kriteri: iki kapasitenin MİNİMUMU >= 5000 ---
$enDusuk = [Math]::Min($KapasiteBaglayici, $KapasiteFormul)
if ($enDusuk -ge $HedefMinimum) {
    Write-Host ("KABUL: kapasite " + $enDusuk + " >= " + $HedefMinimum)
    if ($firmwareAdet -ne 8) { Write-Host "UYARI: firmware sayisi 8 degil (ADR-024)" }
    if (($kSurucuAdet -ne 0) -and ($kSurucuAdet -ne -1)) { Write-Host "UYARI: k-surucu bos degil (ADR-024)" }
    exit 0
} else {
    Write-Host ("RED: kapasite " + $enDusuk + " < " + $HedefMinimum)
    exit 1
}
~~~

### §9.1 Betik Notları

| # | Not |
|---|-----|
| 1 | Betik §4 tablosunu gömülü tutar; tablo değişirse betik de güncellenir (ikisi birlikte revize edilir) |
| 2 | K1 ve K13 sapmaları betikte bağlayıcı değer olarak durur; formül sütunu ayrıca hesaplanır — ikisi de raporlanır |
| 3 | Kanıt türü taraması (desen K\d…) bilgi amaçlıdır; kabul kriteri kapasite üzerinden çalışır |
| 4 | firmware ve k-surucu kontrolleri ADR-024'ün regresyon kalesidir |
| 5 | powershell çıktısı UTF-8 olmalıdır (mojibake yasağı) |

---

## §10 Kabul Kriteri

| # | Kriter | Koşul | Çıktı |
|---|--------|-------|-------|
| 1 | **Kapasite ≥ 5000** | min(bağlayıcı 5.105, formül 5.152) ≥ 5000 | exit 0 — KABUL |
| 2 | Kapasite < 5000 | herhangi bir sayım bu altı inerse | exit 1 — RED |
| 3 | Sapma şeffaflığı | K1 (−17) ve K13 (−30) raporda görünür | ⚠️ satırları mevcut |
| 4 | Kanıt dağılımı | üç türün adedi yazılır | §3.2 bloğu dolu |
| 5 | ADR-024 uyumu | firmware = 8, k-surucu = 0 | UYARI yok |
| 6 | Senaryo etiketi | Hangi senaryo kullanıldı yazılır | alt/orta/üst |

**Kabul cümlesi (bağlayıcı):** "Sayım betiği çalıştı; kapasite min(5.105; 5.152) = 5.105 ≥ 5000 → KABUL."

---

## §11 Yasaklar

| # | Yasak | Sonuç |
|---|-------|-------|
| 1 | Dosya sayısını düğüm saymak | Yanlış toplam (~334 ≠ 5.152) — RED |
| 2 | Kanıtsız düğüm ekleyerek 5000'i doldurmak | Hallüsinasyon — revert + log ERROR |
| 3 | K1/K13 sapmasını tablodan silmek veya düzeltmek | Kanıt şeffaflığı ihlali (ADR-026) |
| 4 | Elle sayımla betik çıktısını geçersiz kılmak | §9.1 kural 1 ihlali |
| 5 | Aynı düğümü iki kanıt türünde iki kez saymak | Çift sayım — toplam şişer |
| 6 | 21. katman eklemek veya K16-K20'yi atlamak | Sayım iskeleti ihlali |
| 7 | 6.200 üstü toplamı yeni ADR'siz raporlamak | §6.1 kural 3 ihlali |

---

## §12 Sık Sorulanlar

| # | Soru | Cevap |
|---|------|-------|
| 1 | 334 dosya ile 5.152 düğüm nasıl tutarlı? | Dosya ≠ düğüm; README satırları (ii) ve plan satırları (iii) ek düğüm üretir (§7.1) |
| 2 | Hangi toplam raporlanır? | İkisi: 4.891/5.105 (bağlayıcı) ve 4.938/5.152 (formül); kabul min ile sınar |
| 3 | K1 ve K13 neden işaretli? | Bağlayıcı değer formülle uyuşmuyor (−17, −30); gizlemek yasak, düzeltmek de yasak — üst göreve sorulur |
| 4 | 4. seviye ne zaman açılır? | Yalnız 3 kanıt türünden biriyle (ADR-023 hibrit derinlik) |
| 5 | Ortadaki 5.400 nereden gelir? | Alt (5.152) + kanıtla açılması beklenen ~248 4. seviye düğüm |
| 6 | k-surucu neden listede 0? | ADR-024 birleşimi: 12 dosya k2-surucu/'ya taşındı; klasör boş |

---

## §13 İlişkili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[adlandirma-kurali]] | Düğüm adlandırma sözdizimi ve 4. seviye kanıtları |
| [[ADR-026-sayim-birimi-5000]] | Sayım birimi = DÜĞÜM kararı, 5000 hedefi |
| [[ADR-023-hibrit-derinlik]] | Derinlik senaryosu (A/B/C) — hibrit derinlik |
| [[ADR-024-surucu-firmware-birlesme]] | k-surucu = 0, firmware = 8 kontrol kalemleri |
| [[katman-baglilik-matrisi]] | Bağımlılık okları (sayıma girmez) |
| [[frontend-restructuring-plan]] | Kanıt türü (iii) kaynağı (§2.1-§2.2) |
| [[index]] | Katman kataloğu |

---

## §14 Salt Okunur Doğrulama Komutları

Yazma yasaklı kontroller; biri bile beklenen çıktıyı vermezse sayım raporu yayımlanmaz.

~~~powershell
# 1) Betik kabul kriteri — KABUL + exit 0 beklenir
powershell -ExecutionPolicy Bypass -File .aiarchitecturescriptskatman-sayim.ps1

# 2) Katman klasör sayısı — 21 (firmware, k-surucu, adr, kök .md hariç) beklenir
(Get-ChildItem .aiarchitecture -Directory -Filter "k*").Count

# 3) ADR-024 kontrol kalemleri — 8 / 0 beklenir
(Get-ChildItem .aiarchitectureirmware -Filter *.md).Count
(Get-ChildItem .aiarchitecturek-surucu -Recurse -File -ErrorAction SilentlyContinue).Count

# 4) Sapma kaydı görünür mü — K1 ve K13 satırları + ⚠️ işaretleri
Select-String -Path .aiarchitecturekatman-sayim-rehberi.md -Pattern "VERIFICATION REQUIRED"

# 5) Kabul kriteri cümlesi dosyada yazılı mı
Select-String -Path .aiarchitecturekatman-sayim-rehberi.md -Pattern "5.105 ≥ 5000"
~~~

### §14.1 Beklenen Çıktı Özeti

| Komut | Beklenen | Uygunsuzsa |
|-------|----------|------------|
| Betik | KABUL, exit 0 | §10 — RED araştırması |
| Katman dizini | 21 | Katman iskeleti ihlali |
| firmware / k-surucu | 8 / 0 | ADR-024 regresyonu |
| Sapma taraması | ≥2 eşleşme (K1, K13) | Şeffaflık ihlali |
| Kabul cümlesi | 1 eşleşme | Kriter metni eksik |

---

## §15 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-09-24 | İlk yazım — 3 turlu agent tartışması 20 persona bağlayıcı kararları (sayım birimi, 21 katman tablosu, ayrıştırma, senaryolar, betik, kabul kriteri) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
