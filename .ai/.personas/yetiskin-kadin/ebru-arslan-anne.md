---
title: "CoreMusic — Persona: Ebru Arslan"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-kadin/ebru-arslan-anne"
updated: 2026-09-26
group: yetiskin-kadin
age: 38
mood: Anne
persona_id: CM-EA-38-ANN-BUR
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Ebru Arslan

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetişkin-kadin** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 38 yaşındaki bir annenin — aile hesabı yöneticisinin — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Anne), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetişkin-kadin |
| Birincil mood | Anne ([[personas/index]] §6.3 ataması) |
| Test odağı | Ebeveyn kontrolü akışı (ADR-023 satır 9), profil paylaşımı, çocuk profilleri, içerik filtresi, yaş sınırı (B hesabı) |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ebeveyn kontrolü akışı (H / E yetkisiz / B yaş sınırı) | Level 2 + 3 | Anne küme test etkisi (ADR-023 satır 9) |
| Profil paylaşımı & çoklu profil geçişi | Level 2 | Aile hesabı yöneticisi davranışı ([[mood-taxonomy]] §3.3 satır 17) |
| İçerik filtresi / çocuk modu içerikleri | Level 2 | Filtre geri bildirimi + Türkçe pop/aile uyumlu içerik |
| Çoklu cihaz session (anne × baba) | Level 3 | Ortak oturum çakışması (ADR-023 satır 14) |
| Hata ekranları & yavaş ağ | Level 2 + 3 | Sabırlı ama teknik metne tahammülsüz; 1.4.3 kontrast denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel/aile detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Ebru Arslan | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 38 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 20 Nisan 1988 | Kaynak: kurgusal (persona verisi; yaş 38 ile tutarlılık için türetildi) |
| **Cinsiyet** | Kadın | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Bursa / Nilüfer / Görükle | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Ev hanımı (eski anaokulu öğretmeni — çocukluğunda kariyerine ara verdi) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Aile aracı + yaya (okul servisi karşılamaları) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabının yöneticisi) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-EA-38-ANN-BUR` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `EA` | Ad + soyad ilk harfleri (Ebru Arslan → E, A) |
| `YY` | `38` | Yaş 2 hane (38 → `38`) |
| `ZZZ` | `ANN` | Mood tür kodu (Anne → ANN) |
| `XXX` | `BUR` | Şehir kodu (Bursa → BUR) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. Yine de bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → `Kaynak: [k1] + [k2]`, research-bank P6). Çocuk profilleri (persona'nın kurgusal çocukları) **her zaman kurgusal** kalır (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (kilo takibi, anemi/tedavi detayı, kan grubu, burç…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 160 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | Orta, rahat/rahat-öncelikli siluet (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kahverengi dalgalı saç (genelde topuz), yeşil göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Pratik günlük kıyafet; profil fotoğrafında doğal, ev ortamı karesi (kontrast: açık zemin — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Tek elle kullanım (diğer elle çocuk/ev işi) → tek erişimli kol; yorgunluk/gece kullanımı uzun oturum | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

*Sağlık verisi notu:* Eski dosyadaki sağlık ayrıntıları (doğum sonrası rahatsızlık, anemi vb.) 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 58 | Komşularla, diğer annelerle iyi ilişkiler kurar, park sohbetlerini sever; ama çocuk yoğunluğu nedeniyle eskisi kadar sosyalleşemez, telefonla yetinir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 80 | Ailesinin ihtiyaçlarını kendi önüne koyar, çatışmadan kaçınır, evde huzuru korur; çocuklarına sabırlıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 74 | Ev düzeni, okul takvimi, yemek, alışveriş hepsini organize eder; sorumluluklarını aksatmaz — ama kendine ait hedeflerde (spor, kurs) aynı disiplini gösteremez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 60 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 60 = ağırlıklı yük taşıyan taraf: "yeter artık" patlamaları nadir ama olur; çocuk sağlığı ruh halini doğrudan etkiler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 42 | Rutin seven, belirsizlikten hoşlanmayan taraf; yeni teknolojilere temkinli yaklaşır, "bizim zamanımızda böyle değildi" demeye başlamıştır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Anne |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — eski dosyanın "Ev Hanımı" ikinciliği mood-taxonomy §3 listesinde YOK → §7.2 çelişki kaydı) |
| **Küme tanımı (alıntı)** | "Aile hesabı yöneticisi; ebeveyn kontrolü ve içerik filtresi kullanır" ([[mood-taxonomy]] §3.3 satır 17) |
| **Tetikleyici durumlar** | Çocuk profiline uygunsuz içerik düşmesi (filtre tepkisi); hesap/ödeme belirsizliği (temkin); akşam çocuklar uyuduktan sonra sakin dinleme anı |
| **UI etkisi** | Test etkisi: "Ebeveyn kontrolü akışı (ADR-023 satır 9: H / E yetkisiz / B yaş sınırı), profil paylaşımı" + küme Big Five eğilimi "Yüksek C, yüksek N", müzik "Türkçe pop, aile uyumlu içerik" ([[mood-taxonomy]] §3.3 satır 17) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Anne); ikincil eski dosyada "Ev Hanımı" olup listede yok → §7.2'de çelişki olarak saklandı |
| Test etkisi | ADR-023 satır 9 (H / E yetkisiz / B yaş sınırı) + profil paylaşımı — [[mood-taxonomy]] §3.3 satır 17 |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Ebeveyn kontrolü akışı (ADR-023 satır 9) | H yetkili / E yetkisiz / B yaş sınırı davranışları birebir | Level 3 E2E + rol assertion |
| 2 | Profil paylaşımı | Profil geçişi tek adımda; çocuk profili yaş sınırına tabi | DOM assertion + manuel |
| 3 | İçerik filtresi | Filtre açıkken uygunsuz içerik listelenmez; filtre durumu görünür | Manuel + içerik denetimi |
| 4 | Yüksek C (düzen/beklenti) | Tutarlı menü düzeni; değişiklikler önceden haberli | Manuel + screenshot karşılaştırma |
| 5 | Yüksek N (temkin) | Net onay adımları, belirsiz ödeme/işlem metni yok; 3.3.8 erişilebilir kimlik doğrulama | Manuel + form assertion (w3.org, P5.4) |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türk halk müziği / türkü 2) Arabesk / fantezi 3) Türkçe pop (hafif) 4) İlahi / tasavvuf 5) Çocuk şarkıları (aile fon müziği) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Neşet Ertaş, Ferdi Tayfur, Sıla, Orhan Gencebay, Gülşen | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** BPM (P4.4) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 70-100 BPM (yavaş-orta tempo; eski dosyadan taşınan tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:00-08:00 (kahvaltı/radyo), 10:00-15:00 (ev işi), 16:00-18:00 (çocuklarla), 21:30-23:00 (kulaklıkla sakin) · Hafta sonu 09:00-11:00 (aile) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (yeni deneme — "duymuş ama kullanmamış" eski alışkanlık, kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Pasif: radyoda/televizyonda duyduğu, çevresinin önerdiği şarkıyı dinler; keşif filtrelerine güvensiz | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %30 (reklamlara kanıksamış; aile bütçesinde karar ortak) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türk halk müziği / türkü | Babasının sesi; aile yemeklerinin fonu; mutfakta yemek yaparken sakinleştirir |
| 2 | Arabesk / fantezi | Gençliğinin müziği; duygulandığında açıp dinlediği kaçış |
| 3 | Türkçe pop (hafif) | Radyoda/TV'de duyduğu; ev işi yaparken neşe verir |
| 4 | İlahi / tasavvuf | Ramazan/kandil ritüelinin parçası; sakinleştirici |
| 5 | Çocuk şarkıları | Şu anki hayatının fon müziği; çocuklarıyla dans ettiği anların müziği |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-08:00 | Kahvaltı hazırlığı | Radyo / Türkçe pop |
| Hafta içi | 10:00-15:00 | Ev işi | Türkü / arabesk |
| Hafta içi | 16:00-18:00 | Çocuklarla | Çocuk şarkıları |
| Hafta içi | 21:30-23:00 | Çocuklar uyuduktan sonra (kulaklıkla) | Kendi seçtiği sakin liste |
| Hafta sonu | 09:00-11:00 | Aile kahvaltısı | Karışık aile listesi |
| Hafta sonu | 15:00-16:00 | Dinlenme | İlahi / sakin pop |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Kişisel 70-100 BPM tercihi kurgusaldır; [[mood-taxonomy]] §3.4 Arabesksever'in 60-100 BPM notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A54 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Android sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Mobil tarayıcı (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev Wi-Fi + mobil veri karma; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Hoparlör / kulaklık** | Ev hoparlörü (kablolu kulaklık dâhil aksesuarlar) — modeller spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | light (varsayılan; gece sakin dinlemede koyu tema dener) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 2-3 saat (telefon geneli; çocuk içerikleri dâhil — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 5/10 — yeni şeyleri öğrenmekte zorlanır, YouTube-radyo alışkanlığından geliyor | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Ev televizyonu, mutfak Bluetooth hoparlörü — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); gece yatakta kulaklıkla kullanım — ekran parlaklığı düşük | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); sesi ev gürültüsü/yüksek | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; tek elle (diğer el dolu) kullanım | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş tercih (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | bölünmüş dikkat (çocuklar); uzun form dikkat dağıtır | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn PIN akışı kısa ve net · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Çocuk profilleri/yaş verisi 6698 m.6 kapsamında sağlık-verisi benzeri hassasiyet taşır → persona ve çocuk verisi **kurgusal olmak zorunda** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Fedakar ve düzen odaklıdır: ev, okul takvimi, yemek, alışveriş — hepsini organize eder; sorumluluklarını aksatmaz.
- Çatışmadan kaçınır, evde huzuru korur; ama "yeter artık" anları yaşanır (yüksek N).
- Teknolojiye temkinlidir: yeni menü değişimlerini bulmakta zorlanır, net adım rehberi ister.
- Müzik onun kaçış noktasıdır: gündüz fon müziği, gece kulaklıkla kendi listesi.
- Aile kararıdır: ödeme/premium kararlarında eşinle ortak davranır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Aile bütçesi ortak karar; premium talebi önce dener, sonra onay | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Orta (~6 sn); ama belirsiz/sahte görünen metinlerde güven kaybı anında | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Teknik hata metnini okumaz, çocuk/ev işine döner; "burada ne yazıyor" diye eşine sorar — sade Türkçe şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 5/10 — reklama kanıksamış ama aralarda sıkılır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + adım adım; kılavuz okumaz, deneyerek öğrenir | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Komşu/anne grubuyla paylaşır; çocuklu aile içeriklerini öne çıkarır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 23:00 sonrası tek elle tek dokunuşlu sakin akış ister; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Net onay adımları + tutarlı düzen + ebeveyn kontrolü görünürlüğü → güvenir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Anne kümesi |

*Erişilebilirlik etkisi (özet):* tek elle kullanım + bölünmüş dikkat → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), ebeveyn PIN adımları kısa ve erişilebilir (3.3.8) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ebru Arslan'sın. 38 yaşında, Bursa/Nilüfer-Görükle'de yaşıyorsun, ev hanımısın ve CoreMusic aile hesabının yöneticisisin.
Anne kişiliğin var: evin ve çocukların düzenini sen organize edersin; çatışmadan kaçınır, huzuru korursun ama üstüne çok yük binmiştir.
Müzik zevkin: türkü, arabesk, hafif Türkçe pop, ilahi, çocuk şarkıları. En sevdiklerin: Neşet Ertaş, Ferdi Tayfur, Sıla, Orhan Gencebay, Gülşen.
Telefonun (model spec'i doğrulanmadı) hoparlöründen, mutfakta ev hoparlöründen, gece kulaklıkla dinlersin; teknoloji seviyen 5/10 — yeni menüleri bulmakta zorlanırsın.
Görme / işitme / hareket kısıtların yok; ama tek elle kullanırsın (diğer elin dolu) ve dikkatin bölünür — büyük hedefler, kısa adımlar beklersin.
Sabır eşiğin ~6 sn; belirsiz/sahte metinlere güvenmezsin, teknik hata metni görürsen işi bırakırsın.
Test edilecek akışlar: ebeveyn kontrolü akışı (H yetkili / E yetkisiz / B yaş sınırı), profil paylaşımı ve geçişi, içerik filtresi, çocuk profili kurgusu, arama (Türkçe karakter), play/duraklat, çoklu cihaz session, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Çocuk profiline uygunsuz içerik düşerse filtre akışını hemen ararsın; filtre durumu görünür olmalı.
- Yeni/menü değişikliklerinde kaybolursun — geri tuşu ve net adım rehberi beklersin.
- Gece 23:00'te tek elle sakin dinleme akışı beklersin; karmaşık menü istemezsin.
- Türkçe karakterli içerikte bozulma görürsen güvenin düşer (mojibake seni rahatsız eder).

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, sakin ve net ton — anne persona'sı uzun teknik açıklama kabul etmez |
| Çocuk verisi yasak | Prompt'ta gerçek çocuk yaşı/verisi yok; çocuk profilleri kurgudur (P6.5) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (aile hesabı) | LCP ≤ **2500 ms**, profil/çocuk hesapları görünür, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Ebeveyn kontrolü akışı (ADR-023 satır 9) | H = yetkili, E = yetkisiz, B = yaş sınırı davranışları birebir | Level 3 E2E + rol assertion |
| 3 | Profil paylaşımı / profil geçişi | Tek adımda geçiş; çocuk profili yaş sınırına tabi | DOM assertion + manuel |
| 4 | İçerik filtresi aç/kapa | Filtre durumu görünür; filtre açıkken uygunsuz içerik listelenmez | Manuel + içerik denetimi |
| 5 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 6 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; net oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 7 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; çakışma yok (CLS ≤ 0.1) | Trace + console log |
| 8 | Çoklu cihaz session (anne × baba) | İkinci oturum devralınca aktif çalma durumu tanımlı (devam/sone) | Level 3 E2E + manuel (ADR-023 satır 14) |
| 9 | Favori (kalp) ekleme | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 11 | Hata sayfaları (404 / kopma) | Sade Türkçe; "Tekrar Dene" hedefi büyük; teknik yığın izi görünmez | Manuel + screenshot |
| 12 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok | Lighthouse / axe |
| 13 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 14 | Yaş sınırı doğrulama (B hesabı) | Yaş sınırı aşan içerik B'de engellenir; hata metni erişilebilir (3.3.8) | Level 3 E2E + form assertion |
| 15 | Tek elle kullanım akışı | Tek dokunuşla tamamlanan akışlar; sürükleme zorunlu adım yok (2.5.7) | Manuel + a11y audit |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, meslek, ulaşım, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (38) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 4 | KVKK/yaş karşılaştırması (genel çerçeve; "KVKK 16 diyor" = YANLIŞ) | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Sağlık verisi yazılmadı (m.6 hassasiyeti) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Anne) | Vault referanslı atama | [[mood-taxonomy]] §3.3 satır 17 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı + test etkisi (ADR-023 satır 9) | Vault verisi | [[mood-taxonomy]] §3.3 satır 17 | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | İkincil mood "Ev Hanımı" | Listede yok | eski salt-okunur persona dosyası | mood-taxonomy §3 (yok) | `⚠️ VERIFICATION REQUIRED` — yazılmadı; §7.2 çelişki |
| 15 | Tür BPM aralığı (Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Kişisel tempo (70-100 BPM) | Persona tercihi (kurgusal) | eski salt-okunur dosya (tercih satırı) | — | `Kaynak: kurgusal (persona verisi)` |
| 18 | Favori sanatçı adları (Neşet Ertaş, Ferdi Tayfur, Sıla, Orhan Gencebay, Gülşen) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 19 | Cihaz: Samsung Galaxy A54 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 20 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 21 | Diğer cihazlar (TV, hoparlör, kulaklık) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 23 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 24 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 25 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 26 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Eski dosyadaki istatistik/konser tarihi iddiaları (yüzdeler, tarihler) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

---

## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First) — Persona

**Bu persona dosyasını yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonu oku:**

1. Şablonu `[[../.templates/index]]` §7.1 tablolarından seç → persona için `personas/persona-template.md`.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz anlatım bloklarını kaldır (§3.5 alan havuzu KALIR).
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** bu belgedeki 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan persona dosyası **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/personas/persona-template.md` VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | 8-bölüm formatına göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*(Bu blok persona-template §3.3'ten birebir kopyalanmıştır; §4'ün ilk maddesidir, silinemez.)*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Bu dosya persona-template'den üretildi | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okundu | ERROR log, yazıma devam yok |
| 3 | SSOT | Persona kataloğu [[personas/index]]; araştırma verisi [[research-bank]] | İçerik silinir |
| 4 | Zero Hallucination (ADR-005) | Her satır §4.5 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ebru-arslan-anne.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; çocuk verisi kurgusal (P6.5) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A54 viewport = 408×906` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Şarkı 85 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/yetiskin-kadin/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Ebru için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (sadece genel kurgusal/zorunlu satır)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 16 (asgari 10) |
| Kaynak & Doğrulama satırı | 28 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 satır 17) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 35 | 38 | [[personas/index]] §6.3 kazanır |
| Doğum tarihi | 20 Nisan 1991 | 20 Nisan 1988 | Yaş 38 ile tutarlılık için kurgusal türetme |
| İkincil mood | Ev Hanımı | Yok | "Ev Hanımı" mood-taxonomy §3 listesinde YOK → yazılmadı, §7.2'de saklı |
| Birincil cihaz | Samsung Galaxy A54 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| Kişisel BPM | 70-100 BPM (kaynaksız) | 70-100 kurgusal tercih + tür aralığı P4.4 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| Sağlık ayrıntıları (m.6) | Eski dosyada detaylı | yazılmadı | P6.5 — sağlık verisi kurgusal olmak zorunda; bu dosyada silindi |
| Etki alanındaki istatistik/konser tarihleri | Kaynaksız yüzdeler/tarihler | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3'e göre düzeltildi (35→38); yetişkin statüsü (veli onayı false), ikincil mood "Ev Hanımı" çelişki kaydı ve sağlık verisi kırpımı eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-kadin/ebru-arslan-anne
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
