---
title: "CoreMusic — Persona: Can Yıldız"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-erkek/can-yildiz-enerjik"
updated: 2026-09-26
group: yetiskin-erkek
age: 31
mood: Enerjik
persona_id: CM-CY-31-ENE-IZM
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Can Yıldız

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-erkek** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 31 yaşındaki bir DJ / müzisyenin — hem dinleyici hem üretici olan kullanıcının — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Enerjik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-erkek |
| Birincil mood | Enerjik ([[personas/index]] §6.3 ataması — yazım birebir "Enerjik") |
| Test odağı | Hızlı navigasyon + anlık geri bildirim + skip performansı (ADR-023 satır 12); set/akış, hızlı skip, karışım listesi, yüksek ses & parazit ortamı |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Set akışı (DJ set listesi → kesintisiz çalma) | Level 2 + 3 | Enerjik küme test etkisi: hızlı navigasyon + geri tuşu (ADR-023 satır 12) |
| Hızlı skip (ardışık parça atlama) | Level 2 + 3 | Anlık geri bildirim, sırada kayıp yok; INP ≤ 200 ms |
| Karışım listesi (karışık tür listesi oluşturma/düzenleme) | Level 2 | Playlist oluşturma davranışı — mood-taxonomy §3.2.2 |
| Yüksek ses & parazit ortamı (DJ kabini benzeri gürültü) | Level 2 | Görsel + metin geri bildirimi; kontrast 1.4.3 / 1.4.11 |
| Keşif / öneri ve hata kurtarma | Level 2 + 3 | Aktif kaşif; yavaş ağda sabır eşiği düşük → 3.2.6 / 3.3.8 |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki üniversite/maaş/sağlık/istatistik ayrıntıları bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.2 | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Can Yıldız | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 31 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 8 Ağustos 1995 | Kaynak: kurgusal (persona verisi; yaş 31 ile tutarlılık için türetildi — eski dosya 8 Ağustos 1999, §7.2) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İzmir / Karşıyaka / Bostanlı | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | DJ / müzik prodüktörü (okul alanı: bankada doğrulanmadı → yazılmadı, X-6) | Kaynak: kurgusal (persona verisi); research-bank §6.4 X-6 |
| **Ulaşım** | Bisiklet + toplu taşıma + yürüyüş (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `can-yildiz` (kurgusal platform rumuzu) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-CY-31-ENE-IZM` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `CY` | Ad + soyad ilk harfleri (Can Yıldız → C, Y) |
| `YY` | `31` | Yaş 2 hane (31 → `31`) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE) |
| `XXX` | `IZM` | Şehir kodu (İzmir → IZM) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. Yaş **31** ([[personas/index]] §6.3 SSOT); eski salt-okunur dosya **27** yazar → §7.2'de iki değer `⚠️` ile saklıdır, indirgeme YOKTUR. Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → `Kaynak: [k1] + [k2]`, research-bank P6).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (aile profili, kira, gelir, uyku/sigara vb.) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 176 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 68 kg, ince/atletik sahne silueti (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral uzun saç (omuz hizası, dağınık topuz), yeşil göz (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Bohem/sanatçı giyim: keten gömlek, salaş pantolon, boncuk bileklik; profil fotoğrafında sahne karesi (kontrast: koyu zemin — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Kısıt yok (kurgu); düşük ışıklı/gürültülü ortamda hızlı tek elle dokunma → büyük hedef, net tipografi ve görsel geri bildirim gerekir | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

*Sağlık verisi notu:* Eski dosyadaki sağlık/biyoloji ayrıntıları (sigara kullanımı, uyku düzeni, işitme testi/ölçüm değerleri vb.) 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Sahnede/ DJ kabininde kalabalığı coşturur; sosyal ortamda enerjisi yükselir, yeni insanlarla tanışmayı sever. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Kolay arkadaşlık kurar, mekan sahipleri ve müzisyenlerle iyi geçinir; ama sanatsal konularda ödün vermez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 40 | "Akışına bırak" felsefesi: randevulara geç kalır, planlı yaşamakta zorlanır; en zayıf yönü budur. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 50 = orta; sanatçı mizacı — bir gün uçar, ertesi gün "başaramayacağım" der; sezon dışı kaygısı artar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 100 | Müzik onun için sonsuz keşif alanı: yeni sesler, yeni türler, yeni prodüksiyon teknikleri; sürekli öğrenir, sürekli dener. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; eski dosyanın `/10` puanları 10 ile çarpılarak ölçeklendi (8/7/4/5/10 → 80/70/40/50/100 — şablon §3.5.3 kuralı); normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| N ters-kodlama | N satırında **yüksek puan = düşük duygusal denge**; okuma bu yönle yapılır (zorunlu not) |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi ve [[mood-taxonomy]] §3.2.2 eğilim satırındaki (Dışadönüklük "O", Açıklık "A") eşleme bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — eski dosyanın "Yaratıcı" ikinciliği mood-taxonomy §3 listesinde YOK → §7.2 çelişki kaydı) |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Set öncesi hazırlıkta karışım listesi arama; ardışık hızlı skip; yeni keşif akışında ilk üç öneride bulunamama; gürültülü ortamda sesli uyarı kaçırma |
| **UI etkisi** | Test etkisi: "Hızlı navigasyon, anlık geri bildirim, skip performansı" + ADR-023 satır 12 (hızlı navigasyon + geri tuşu); küme Big Five eğilimi "Yüksek Dışadönüklük, yüksek Deneyime Açıklık, düşük N" (kod eşlemesi ⚠️), müzik "Pop, dance, elektronik, hareketli · 120-160 BPM ⚠️" ([[mood-taxonomy]] §3.2.2) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Enerjik, yazım birebir); ikincil eski dosyada "Yaratıcı" olup listede yok → §7.2'de çelişki olarak saklandı |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) — [[mood-taxonomy]] §3.2.2 |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Hızlı navigasyon + geri tuşu (ADR-023 satır 12) | Sayfalar arası geçişte geri tuşu kaybolmaz; hissedilir gecikme yok | Level 3 E2E + trace |
| 2 | Sık skip (parça atlama) | Her atlamada anlık geri bildirim; sırada kayıp yok | DOM assertion + sayaç |
| 3 | Playlist oluşturma davranışı | Tek dokunuşla karışım listesi oluşturulur, kalıcı olur | DOM assertion + LocalStorage |
| 4 | Yüksek O / yüksek E (kurgusal eğilim) | Keşif akışında öneri yoğunluğu ve görsel canlılık | Manuel + içerik denetimi |
| 5 | Gürültülü ortamda kullanım | Sesli geri bildirim görselle desteklenir; metin kontrastı korunur (1.4.3) | Manuel + kontrast denetimi |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Deep house / organic house 2) Elektronik (deneysel / ambient) 3) Türkçe alternatif / indie 4) Caz / funk 5) Hip-hop (enstrümantal beat) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Islandman, Hey Douglas, Jakuzi, Büyük Ev Ablukada, &ME | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | House **110-128** · Electro **90-130** · Dance **110-135** · Disco **100-130** · Pop **80-120** (set akışı için referans türler) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 118-124 BPM (yakın türler; set/karışım listesi tercihi) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 11:00-13:00 (keşif), 15:00-17:00 (prodüksiyon), 22:00-02:00 (set) · Hafta sonu set + gündüz dinlenme | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (yeni deneme — Türkçe alternatif/indie arşivi için, kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Yüksek: her gün yeni müzik, karışık kaynak taraması (yayın radarları, plak dükkanları, diğer DJ'lerin listeleri) | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %60 (sanatçı profili + yüksek kalite sunulursa geçiş eğilimi güçlü; kurgusal) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Deep house / organic house | İmza sound'u; DJ setlerinin çekirdeği |
| 2 | Elektronik (deneysel / ambient) | Prodüksiyon yaparken sınır zorladığı tür |
| 3 | Türkçe alternatif / indie | Köklerinden kopmamak; sahnede çaldığı yerli repertuvar |
| 4 | Caz / funk | Sample kaynağı; eski plakları karıştırma hobisi |
| 5 | Hip-hop (enstrümantal beat) | Beat yapımının temel referansı |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 11:00-13:00 | Keşif / liste taraması | Türkçe alternatif, indie |
| Hafta içi | 15:00-17:00 | Prodüksiyon (referans dinleme) | Elektronik, caz/funk |
| Hafta içi | 19:00-21:00 | Set hazırlığı (karışım listesi) | Deep house, organic house |
| Hafta içi | 22:00-02:00 | DJ seti | Deep house, organic house, afro house |
| Hafta sonu | Set sonrası | Dinlenme | Ambient, chill, caz |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Kişisel 118-124 BPM tercihi kurgusaldır; [[mood-taxonomy]] §3.2.2 küme BPM notu (120-160) eski taksonomidendir → `⚠️ VERIFICATION REQUIRED`.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 14 Pro (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Mobil tarayıcı (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev geniş bant + mobil veri (marka/hız kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kişisel kulaklık (model/spec'siz) | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark (düşük ışıklı ortam — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Yoğun günlük kullanım (prodüksiyon + sosyal — süre kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 9/10 — prodüksiyon yazılımı ve sahne ekipmanına hâkim, dijital araçlarda ileri seviye | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Dizüstü bilgisayar, ses kartı, stüdyo monitörü, DJ kontrolcü — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); düşük ışıklı ortamda ekrana kısa bakışlar | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); hover/focus içeriği kapatılabilir olmalı (1.4.13) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); yüksek sesli/gürültülü ortamlarda çalışır | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; hızlı tek elle dokunma (set akışı) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürüklemesiz alternatif (2.5.7) · odak görünür (2.4.7), örtülmemiş odak (2.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | hız odaklı, sabır eşiği düşük | Yardım erişimi tutarlı (3.2.6) · gereksiz tekrar yok (3.3.7) · erişilebilir kimlik doğrulama (3.3.8) | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Sağlık verisi 6698 m.6 kapsamında hassastır → persona sağlık satırı **kurgusal olmak zorunda** (research-bank P6.5); bu dosyada sağlık satırı yazılmadı |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Enerjisi bulaşıcıdır: sahnede ve sosyal ortamlarda öne çıkar; prodüksiyonda saatlerce odaklanabilir.
- Hız ve anlık geri bildirim bekler; bekletilen arayüzde sabrı hızla düşer.
- Keşif odaklıdır: yeni sesleri, yeni listeleri ve remix/farklılaşma içerikleri arar.
- Sanatsal konularda inatçıdır; "sound"undan ödün vermez, algoritmadan çok insan küratörlüğüne güvenir.
- Üretici kimliğidir: dinlediğini değerlendirir, listelerini düzenli günceller ve paylaşır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Araştırmacı + dürtüsel ikilemi: müzikle ilgili araç/aboneliğe hızlı, zorunlu harcamaya çekingen | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 2 sn; spinner'a ~5 sn dayanır, sonra akışı bırakır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sabırsız: kısa yol gösteren metin ister; teknik yığın izni görürse platformdan soğur | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 5/10 — kesintisiz set akışını bozan reklamda listeyi kapatır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel demo + kısayol; uzun metin okumaz, hemen dener | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Listelerini ve mix'lerini paylaşır; DJ topluluğuyla dosya/haberleşme alışkanlığı vardır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Set sonrası (03:00+) karmaşık akış reddedilir; tek dokunuşlu sakin mod beklenir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Hızlı açılış + şeffaf içerik (kredi/atıf görünür) + tutarlı düzen → güvenir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.2 |

*Erişilebilirlik etkisi (özet):* gürültülü ortamda sesli uyarı kaçabilir → görsel geri bildirim zorunlu, metin kontrastı ≥ **4.5:1** (1.4.3); hızlı atlamada dokunma hedefi ≥ **24×24 CSS px** (2.5.8), geri tuşu kaybolmaz (2.4.11), yardım erişimi tutarlıdır (3.2.6) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Can Yıldız'sın. 31 yaşında, İzmir/Karşıyaka-Bostanlı'da yaşıyorsun, DJ ve müzik prodüktörüsün.
Enerjik bir kişiliğin var: sık sık parça atlarsın, yeni şarkı keşfeder ve karışım listeleri oluşturursun; ikincil küme ataması yok — yaratıcı yönün mood listesinde ayrı küme değildir.
Müzik zevkin: deep house / organic house, elektronik (deneysel/ambient), Türkçe alternatif/indie, caz/funk, enstrümantal hip-hop beat. En sevdiklerin: Islandman, Hey Douglas, Jakuzi, Büyük Ev Ablukada, &ME.
Telefonunun model spesifikasyonu doğrulanmadı; dizüstü bilgisayarın, ses kartının, monitörünün ve DJ kontrolcünün spec'leri de doğrulanmadı; teknoloji seviyen 9/10.
Görme / işitme / hareket kısıtların yok; ama ortamın yüksek sesli ve loş — görsel geri bildirim, büyük hedefler ve yüksek kontrast beklersin.
Sabır eşiğin 2 sn, spinner'a ~5 sn dayanırsın; hata ekranlarında kısa, yol gösteren metin istersin.
Test edilecek akışlar: hızlı navigasyon ve geri tuşu, hızlı skip, karışım listesi oluşturma/düzenleme, set akışında kesintisiz çalma, keşif/öneri, gürültülü ortamda görsel geri bildirim, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Ardışık skip'te her atlamada anlık geri bildirim almalısın; sırada kayıp olursa listeyi tamamen yenilersin.
- Karışım listesi tek dokunuşla oluşmalı ve kalıcı olmalı; kayıt adımda gereksiz tekrar istemezsin (3.3.7).
- Gürültülü ortamda yalnız sesli uyarıya güvenmezsin; görsel ipucu yoksa o adımı kaçırırsın (1.4.3 / 1.4.11).
- Türkçe karakterli içerikte bozulma (mojibake) görürsen güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa ve enerjik cümleler — uzun anlatım kabul edilmez |
| Sağlık/gerçek veri yasak | Prompt'ta sağlık verisi (m.6), üniversite adı (X-6), gerçek platform istatistiği yok |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme (DJ profili) | LCP ≤ **2500 ms**, profil ve son çalınan görünür, CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | 1.4.3 (`VERIFIED`) |
| 2 | Set akışı — karışım listesi açma | Liste anında dolar; sıralama bozulmaz; CLS ≤ **0.1** | DOM assertion + trace | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 1.4.11 (`VERIFIED`) |
| 3 | Hızlı skip (15 parça ardışık) | Her atlamada anlık geri bildirim; sırada kayıp yok | DOM assertion + sayaç | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 4 | Sonraki / önceki parça kontrolü | Kontrol anında yanıt; odak kontrolde kalır | Trace + console log | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 5 | Karışım listesi oluşturma | Tek dokunuşla oluşturma; liste kalıcı; yeniden adlandırma çalışır | DOM assertion + LocalStorage kontrolü | — (P8 dışı) | 3.3.7 (`VERIFIED`) |
| 6 | Liste sıralama (sürükleme ile) | Sürükleyerek sıralama çalışır; sürükleme YOKSA menü alternatifi sunulur | DOM assertion + manuel | — (P8 dışı) | 2.5.7 (`VERIFIED`) |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; net oynat kontrolü; INP ≤ **200 ms** | Trace + console log | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 1.4.11 (`VERIFIED`) |
| 8 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 9 | Favori (kalp) ekleme | Anlık görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 10 | Yüksek ses & parazit ortamı senaryosu | Sesli geri bildirim görselle desteklenir; metin kontrastı korunur | Manuel + ekran görüntüsü + kontrast denetimi | — (P8 dışı; eşik P5) | 1.4.3 (`VERIFIED`) |
| 11 | Metadata ipucu (parça bilgisi — hover/focus) | İpucu açılır, kapatılabilir, odakta kalır | A11y audit + DOM assertion | — (P8 dışı; eşik P5) | 1.4.13 (`VERIFIED`) |
| 12 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | — |
| 13 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace | Slow 4G profili (P8.3 `VERIFIED`) | — |
| 14 | Hata sayfaları (404 / kopma) | Sade Türkçe; hata kodu + "Tekrar Dene"; yardım erişimi her sayfada aynı yerde | Manuel + screenshot | — (P8 dışı) | 3.2.6 (`VERIFIED`) |
| 15 | Giriş / oturum akışı | Gereksiz tekrar istemez; erişilebilir kimlik doğrulama; yanlış adımda net geri bildirim | Form assertion + a11y audit | — (P8 dışı) | 3.3.8 · 3.3.7 (`VERIFIED`) |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Sanatçı/parça adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | — (P8 dışı) | — |
| 17 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür; gizli/örtülmemiş odak yok | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 · 2.4.11 (`VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); WCAG kodları yalnız P5.2/P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, meslek, ulaşım, rumuz, aile/çevre detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (31) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Yaş çakışması: **31** (index §6.3 SSOT) ↔ **27** (eski salt-okunur dosya) | Vault içi CONFLICT | [[personas/index]] §6.3 | eski salt-okunur persona dosyası | İki değer `⚠️` — indirgeme YOK; karar: index kazanır (§7.2) |
| 4 | Doğum tarihi (8 Ağustos 1995 — yaş 31'e göre türetme) | Kurgusal türetme | Yaş 31 ([[personas/index]] §6.3) | eski dosya: 8 Ağustos 1999 (yaş 27) | `Kaynak: kurgusal (persona verisi)` + türetme notu (§7.2) |
| 5 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 6 | KVKK/yaş karşılaştırması (genel çerçeve; "KVKK 16 diyor" = YANLIŞ) | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 7 | Fiziksel özet satırları (boy, kilo, saç/göz, giyim) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | Sağlık verisi yazılmadı (sigara/uyku/işitme ölçümü — m.6 hassasiyeti) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 9 | Big Five puanları (5×0-100: 80/70/40/50/100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 10 | Eski `/10` puanlarının 0-100'e dönüşümü (×10) | Türetilmiş ölçekleme | persona-template §3.5.3 (kural) | eski salt-okunur dosya (sıralı puanlar) | `⚠️ DERIVED` + puanlar kurgusal |
| 11 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 12 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 13 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 14 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları ↔ mood-taxonomy §3.2.2 eğilimi) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 15 | Mood küme adı (Enerjik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.2 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 16 | Mood küme tanımı alıntısı + test etkisi (ADR-023 satır 12) | Vault verisi | [[mood-taxonomy]] §3.2.2 | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 17 | Küme BPM notu (120-160) | Eski taksonomi (birincil ölçüm değil) | mood-taxonomy §3.2.2 (⚠️ işaretli) | research-bank P4 (böyle bir aralık YOK) | `⚠️ VERIFICATION REQUIRED` — ana BPM kaynağı P4.4 |
| 18 | İkincil mood "Yaratıcı" | Listede yok | eski salt-okunur persona dosyası | mood-taxonomy §3 (yok) | `⚠️ VERIFICATION REQUIRED` — yazılmadı; §7.2 çelişki |
| 19 | Tür BPM aralıkları (House 110-128 · Electro 90-130 · Dance 110-135 · Disco 100-130 · Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 20 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 21 | Kişisel tempo (118-124 BPM) | Persona tercihi (kurgusal) | eski salt-okunur dosya (tercih bağlamı) | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | Favori sanatçı adları (Islandman, Hey Douglas, Jakuzi, Büyük Ev Ablukada, &ME) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 23 | Üniversite / konservatuvar adı (eski dosyada) | Kapsam dışı (EXCLUDED) | research-bank §6.4 X-6 | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 24 | Cihaz: iPhone 14 Pro (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Cihaz spec'i: Galaxy A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, Android 13 | Gerçek-dünya (test cihazı) | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 26 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 27 | Diğer cihazlar/ekipman (dizüstü, ses kartı, monitör, DJ kontrolcü) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 28 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 29 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 30 | Persona renk kombinasyonu (dark tema) kontrastı; P5 kapsamında olmayan AA kriterleri (1.4.5, 1.4.10, 2.4.3, 3.3.x tamamı…) | Türetilmiş hesap + kapsam dışı | research-bank P5.5 | — | `⚠️ DERIVED` (kontrast) + `⚠️ VERIFICATION REQUIRED` (P5 dışı kriterler — kullanılmadı) |
| 31 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn · Slow 4G 150 ms/1.6 Mbps/750 Kbps | Gerçek-dünya | web.dev/articles/vitals (+ defining-core-web-vitals-thresholds) | github.com/GoogleChrome/lighthouse (docs/throttling.md) + developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Eski dosyadaki istatistik/yüzde/iddia satırları (üniversite, maaş aralığı, dinlenme sayıları, blog/chart sıralaması) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`can-yildiz-enerjik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; sağlık verisi m.6 → kurgusal (P6.5) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 14 Pro viewport = 390×852` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Şarkı 121 BPM` / `set 122 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.18 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Yaş 27` (eski dosya tek başına) | `31 (index §6.3 SSOT) ↔ 27 (eski dosya) — iki değer ⚠️, §7.2` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.2 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia, üniversite/sağlık/yüzde taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 (6 sütun) → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/yetiskin-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır (kuyruk mojibake'li — asla birebir kopyalanmaz) | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 506`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 (6 SÜTUN) → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Yaş tek değere indirgenmiş | §7.2 çakışma satırı kayboldu | 31 ↔ 27 iki değerini `⚠️` ile geri getir |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Yer tutucusu / 500 altı satır | `verify.lines < 506` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Can için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 12 alanlı frontmatter (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`, `group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 506 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, yer tutucusu kalmamış
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (sadece genel kurgusal/zorunlu satır)
- [ ] Yaş: 31 (index §6.3) kullanıldı; 27 ↔ 31 çakışması §7.2'de `⚠️` ile iki değer olarak duruyor
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5.2/P5.4 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok; taksonomi 120-160 notu `⚠️ VERIFICATION REQUIRED`
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 17 (asgari 10; 6 sütun) |
| Kaynak & Doğrulama satırı | 32 (aralık 28-32) |
| Frontmatter alan | 12 (7 zorunlu + 5 ek) |
| Yaş | 31 (index §6.3 SSOT; eski dosya 27 → §7.2 `⚠️`) |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 506 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2 satır 147) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | **27** | **31** | [[personas/index]] §6.3 kazanır; iki değer `⚠️` ile korunur, indirgeme yok |
| Doğum tarihi | 8 Ağustos 1999 (yaş 27 ile tutarlı) | 8 Ağustos 1995 (yaş 31 ile tutarlı kurgusal türetme) | Yaş 31 SSOT olduğu için türetildi |
| İkincil mood | Yaratıcı | Yok | "Yaratıcı" mood-taxonomy §3 listesinde YOK → yazılmadı, §7.2'de saklı |
| Mood yazımı | "Enerjik / Yaratıcı" (ikili başlık) | Enerjik | [[personas/index]] §6.3 yazımı birebir "Enerjik" |
| Küme BPM | Eski taksonomi 120-160 ⚠️ | Tür aralıkları P4.4 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2; taksonomi notu `⚠️ VERIFICATION REQUIRED` |
| Birincil cihaz | iPhone 14 Pro (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| Üniversite/konservatuvar | Eski dosyada okul adı kayıtlı | yazılmadı | research-bank §6.4 X-6 (kapsam dışı) |
| Sağlık ayrıntıları (m.6) | Eski dosyada sigara/uyku/işitme ölçümü | yazılmadı | P6.5 — sağlık verisi kurgusal olmak zorunda; bu dosyada silindi |
| Yüzde/istatistik/maaş satırları | Kaynaksız gelir, dinlenme sayısı, blog sıralaması iddiaları | yazılmadı | §4.5 kural 4 (bankada yok → yazma; X-3) |
| Eski Big Five `/10` puanları | 8 / 7 / 4 / 5 / 10 | 80 / 70 / 40 / 50 / 100 (0-100) | persona-template §3.5.3 ×10 ölçeklemesi; normalize `⚠️ DERIVED` |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; 6 sütunlu Test Adımları (17 satır) ve 33 satırlık Kaynak & Doğrulama tablosu; eski salt-okunur dosyadan kimlik/müzik taşındı (kuyruk mojibake'li olduğu için birebir kopyalanmadı), yaş index §6.3'e göre düzeltildi (27→31, çakışma §7.2'de iki değer olarak saklı), üniversite/sağlık/yüzde verileri kırpıldı (X-6 / P6.5 / ADR-005) | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-erkek/can-yildiz-enerjik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
