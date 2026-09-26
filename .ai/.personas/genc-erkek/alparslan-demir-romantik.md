---
title: "CoreMusic — Persona: Alparslan Demir"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/alparslan-demir-romantik"
updated: 2026-09-26
group: genc-erkek
age: 17
mood: Romantik
persona_id: CM-AD-17-ROM-ANK
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Alparslan Demir

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 17 yaşındaki romantik/entelektüel bir gencin gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Romantik §3.2.1), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Romantik ([[personas/index]] §6.3 ataması) |
| Test odağı | Duygusal UI, renk paleti, slow geçiş animasyonları, şarkı sözü ekranı, az skip / tekrar dinleme, karanlık atmosferik tema |
| Veli onayı | `veli_onayı_gerekli: false` (17 ≥ 16 eşiği — `⚠️ DERIVED`, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / duygusal karşılama | Level 2 + 3 | Romantik UI tercihi: yumuşak renkler, yavaş animasyonlar ([[mood-taxonomy]] §3.2.1) |
| Mood-geçiş senaryosu | Level 1 + 2 | ADR-023 satır 16 (mood-geçiş) BPM eşikleri; yalnız tür aralığı (P4.4/P4.5) |
| Şarkı sözü ekranı | Level 2 + 3 | Söz tipografisi kontrastı (1.4.3 ≥4.5:1); uzun söz kaydırma |
| Tekrar dinleme / az skip | Level 1 + 2 | Çalma listesi kalıcılığı, "tekrar dinle" akışı; CLS ≤ 0.1 |
| Karanlık tema + renk paleti | Level 2 | 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 (P5.4 `VERIFIED`) |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel/aile detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); eski dosyadaki yüzdeli istatistikler (%25 indie, %85 premium…) kaynaksız olduğu için taşınmaz.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3.2.1) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Alparslan Demir | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 17 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 4 Şubat 2009 | Kaynak: kurgusal (persona verisi; yaş 17 ile tutarlılık için türetildi) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Ankara / Çankaya — Ayrancı | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | TED Ankara Koleji; Lise 11. sınıf (eşit ağırlık) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Toplu taşıma (metro/otobüs) + yürüyüş; okul servisi yok | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `alp_2am` (paylaştığı duygusal playlist adından) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AD-17-ROM-ANK` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` biçimi notu:**

| Konu | Değer |
|------|-------|
| Bu turda verilen ID | `CM-AD-17-ROM-ANK` (görev tablosu / registry eşlemesi) |
| Şablon §3.5.1 biçimi | `CM-XX-YY-ZZZ-XXX` (ör. `CM-AD-17-ROM-ANK`) |
| Durum | Şablon formatı ile uyumlu (`CM-XX-YY-ZZZ-XXX`) — 2026-09-26 düzeltmesi; §7.2 |

> **KVKK / yaş sınırı notu (16 yaş üzeri — veli onayı gerekmez):** Bu persona 17 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 `VERIFIED`). CoreMusic 16 yaş eşiği (`⚠️ DERIVED`, P6.4 son satır) karşılandığı için veli onayı `false`; TMK m.11 gereği 18 yaş altında ehliyet sınırlı olduğundan ödeme/abonelik akışları yine de dikkatle test edilir. Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (aile, burç, kan grubu, korkular, hayaller…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 182 cm (normal yapı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 74 kg, atletik değil, hafif çökük omuz (uzun süre oturarak okuma/çalma) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral, dağınık-uzun saç; ela göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; kablolu kulaklık kullanır (model §3.5.6) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Boho-entelektüel (keten gömlek, fular); profil fotoğrafında açık zemin × koyu saç (kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Gece uzun okuma/yazı yazma → göz yorgunluğu; yavaş, yumuşak animasyon bekler, parlak/beyaz zemin rahatsız eder | Kaynak: kurgusal (persona verisi); kontrast eşikleri: `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 30 | Kalabalıktan yorulur, derin sohbeti tercih eder; parti ortamında köşede insanları izler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 80 | Empatisi yüksektir, çatışmadan kaçınır ve herkesi anlamaya çalışır; bazen kendi needsini ihmal eder. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 50 | Müzik/yazı işlerinde disiplinlidir ama evrak, randevu, ders programı gibi pratik işlerde zayıftır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 60 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 60 = orta-üstü; duyguları yoğundur ve dalgalıdır — bir gün üretken, ertesi gün anlamsızlık. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Yeni müzik, yeni yazar, yeni felsefi akım sürekli keşiftedir; algoritmanın önerileri bile dar gelir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 `VERIFIED`) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.2.1 "Yüksek Uyumluluk (**C**), orta-düşük Dışadönüklük (**O**)" yazar — P7.3'e göre Uyumluluk=A, Dışadönüklük=E; kod harfleri `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır"), çelişki §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Romantik |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar; eski dosyada "Sosyal" ikincil idi → §7.2) |
| **Küme tanımı (alıntı)** | "Aşk/duygu odaklı dinleyici; şarkıya duygusal bağ kurar, az skip eder, tekrar dinler" ([[mood-taxonomy]] §3.2.1) |
| **Tetikleyici durumlar** | Akşam/gece yalnız dinleme; şiir veya şarkı sözü yazarken; duygusal an → aynı şarkıyı tekrar dinleme; karanlık/atmosferik tema beklentisi |
| **UI etkisi** | "Yumuşak renkler, yavaş animasyonlar, sıcak tonlar" + test etkisi "Duygusal UI, renk paleti, **slow geçiş animasyonları**; ADR-023 satır 16 (mood-geçiş) BPM eşikleri" ([[mood-taxonomy]] §3.2.1 "UI tercihi" / "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Romantik); ikincil bu turda yok — eski kaynakta mood farkı §7.2'de |
| Test etkisi | [[mood-taxonomy]] §3.2.1 "Duygusal UI, renk paleti, slow geçiş animasyonları; ADR-023 satır 16 (mood-geçiş) BPM eşikleri" — küme BPM notu (60-100) `⚠️ VERIFICATION REQUIRED` (research-bank P4'te böyle aralık YOK); testte banka tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Az skip eder, tekrar dinler (Romantik) | "Tekrar dinle"/çalma sırası durumu bozulmadan sürdürülür; liste kalıcı | DOM assertion + LocalStorage kontrolü |
| 2 | Duygusal UI / yumuşak renk paleti | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) korunur | Erişilebilirlik audit (axe / Lighthouse) |
| 3 | Slow geçiş animasyonları | Geçişler hissedilir ama yavaş; yerleşim sıçraması yok (CLS ≤ **0.1**) | Performance trace + screenshot |
| 4 | Mood-geçiş akışı (ADR-023 satır 16) | BPM eşikleri banka tür aralığı (P4.4/P4.5) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 5 | Şarkı sözü odaklı dinleme | Söz metni taşmaz, kaydırılabilir; kontrast ≥ 4.5:1 (1.4.3) | DOM assertion + a11y audit |
| 6 | Gece/yalnız dinleme | Karanlık temada odak görünür (2.4.7), geri tuşu kaybolmaz (2.4.11) | Klavye navigasyon testi |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe Alternatif / Indie 2) Indie Folk (yabancı) 3) Klasik Gitar / Enstrümantal 4) Anadolu Rock / Türkçe Slow 5) Slowcore / Dream Pop | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Pinhani, Cem Karaca, Bon Iver, Cigarettes After Sex, Şam | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Bankada indie / indie folk / Anadolu rock / slowcore için tür aralığı **YOK** → `⚠️ VERIFICATION REQUIRED` (P4.4 kapsam dışı); referans banka aralıkları: Pop **80-120** · Nu R&B **70-110** (yalnız referans, persona türüne birebir uygulanmaz) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 50-110 BPM (yavaş, söz öncelikli dinleme) | Kaynak: kurgusal (persona verisi — tercih, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 23:00-01:00 (gece yazısı), derste gizli kulaklık, 17:00-19:00 (gitar/pratik) · Hafta sonu 18:00-00:00 (yürüyüş, keşif, gece) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (duygusal playlist, şarkı sözü ekranı, mood bazlı radyo) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | "an" bazlı keşif: ruh hâline göre listeye dalar; algoritma önerisine mesafelidir, küratöryel listeleri sever | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Yüksek (kaliteli ses için; ödeme aile üzerinden — veli onayı `false`, 17 yaş) | Kaynak: kurgusal (persona verisi; yüzde taşınmadı — eski dosyadaki yüzsüz istatistikler §4.5 gereği yazılmaz) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe Alternatif / Indie | Sözler şiirseldir; kendi yazdığı yazışlara benzer |
| 2 | Indie Folk (yabancı) | Duygusal ve samimi; kış akşamlarında tekrar dinleme |
| 3 | Klasik Gitar / Enstrümantal | Ders çalışırken ve yazarken söz çalmaz |
| 4 | Anadolu Rock / Türkçe Slow | Babasının plaklarından gelen miras (Cem Karaca, Erkin Koray) |
| 5 | Slowcore / Dream Pop | Gece 01:00 melankolisi; "2 AM Thoughts" listesinin çekirdeği |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 08:30-09:00 | Uyanma (zor) | Sessizlik → enstrümantal |
| Hafta içi | 09:00-16:00 | Okul | Derste gizli kulaklık; teneffüste gitar |
| Hafta içi | 17:00-19:00 | Gitar pratiği / şarkı yazma | Kendi üretimi + referans indie |
| Hafta içi | 19:00-21:00 | Yürüyüş / şiir / günlük | Melankolik playlist (slowcore) |
| Hafta içi | 23:00-01:00 | Gece yazısı | "2 AM Thoughts" — Türkçe indie + dream pop |
| Hafta sonu | 18:00-00:00 | Müzik keşfi + film | Anadolu rock / film müzikleri |

*Not: BPM satırları yalnız tür düzeyindedir; "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.2.1'in 60-100 BPM küme notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları (P4.4/P4.5) kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 14 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev Wi-Fi + mobil veri karma; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablolu kulaklık (eski dosyada model: Sony MDR-7506) — model/spec bu turda doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (X-1) — spec yazılmadı |
| **Bilgisayar** | Dizüstü (eski dosyada MacBook Air — yazı/prodüksiyon denemeleri); spec'leri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |
| **Tema** | dark (gece kullanımı, beyaz zemin rahatsız eder — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 3-4 saat (telefon geneli — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — ihtiyacı kadar; şarkı sözü ve playlist odaklı | Kaynak: kurgusal (persona verisi) |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); gece uzun okuma alışkanlığı | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — karanlık temada söz metni dahil | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 `VERIFIED`) |
| **İşitme** | yok; kablolu kulaklıkla gece dinleme (dış ses yalıtımı) | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; orta hareket hassasiyeti — yavaş animasyon tercih eder | Sürükleme zorunlu öğelerde 2.5.7; işaretçi hedefi ≥ **24×24 CSS px** (2.5.8) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + digitalpolicy.gov.hk (P5.4 `VERIFIED`) |
| **Kognitif / dikkat** | gece/yalnız oturum; uzun şarkı sözünü okur, forma kısa dikkat | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — gece girişinde sabırlı ama form dikkati düşük | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 `VERIFIED`) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (ruh hâli/gece kullanımı dâhil) 6698 m.5/1 kapsamında; sağlık verisi yok ama persona yine de **kurgusal** olmak zorunda (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |
| P5 dışı AA kriterleri | 1.4.5, 2.4.3, 3.3.x tamamı paket dışı → numara kullanılırsa `⚠️ VERIFICATION REQUIRED` (X-7) |

### Kişilik & Davranış

- Sanatçı ruhludur: şarkı sözü, şiir ve günlük yazar; konuşarak ifade etmekte zorlanır, yazı onun terapisidir.
- Duyguları yoğundur ve dalgalıdır — bir gün üretken, ertesi gün hiçbir şey anlamsız gelir.
- Yalnız dinler: gece 23:00 sonrası karanlık temada uzun oturumlar kurar.
- Yüzeysel sohbetten nefret eder; 2-3 yakın arkadaşla derin bağ kurar (eski dosyanın "Sosyal (seçici)" ikincil teması — bu turda ikincil küme atanmadı, §7.2).
- Algoritmaya mesafelidir; "an" bazlı dinler, küratöryel listeleri keşfeder.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 17 yaş → kendi kararı gibi görünse de 18 yaş altı ehliyet sınırlı; ödeme aile üzerinden (`veli_onayı_gerekli: false` — 16 eşiği geçildi) | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |
| **Sabır eşiği** | ~5 sn; ama gece oturumunda sabırlıdır — içerik yüklenirse bekler, yavaş animasyonu sever | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Durulur, hatayı metniyle okur; duygusal akış bozulursa uygulamayı kapatır ve o şarkıyı bir daha açmaz | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Çok düşük — duygu akışını "sabotaj" olarak görür; gece oturumunda reklam = çıkış | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Okuyarak ve deneyerek; kılavuz/video izlemez, arayüzü gezer | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Yakın 2-3 arkadaşıyla paylaşır; kalabalık paylaşım azdır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 00:00 sonrası tek dokunuşlu devam akışı bekler; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Şarkı sözü ekranı + karanlık atmosferik tema + kesintisiz çalma → bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Romantik kümesi |

*Erişilebilirlik etkisi (özet):* gece uzun oturumda söz metni ve kontroller kontrast ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11), odak görünür (2.4.7) ve geri tuşu kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 `VERIFIED`).

### AI Rol Kartı

Sen Alparslan Demir'sin. 17 yaşındasın, Ankara/Çankaya-Ayrancı'da yaşıyorsun, TED Ankara Koleji 11. sınıfta (eşit ağırlık) okuyorsun.
Romantik kişiliğin var: şiir, günlük ve şarkı sözü yazarsın, gitar çalarsın; duyguların yoğundur ve dalgalıdır.
Müzik zevkin: Türkçe alternatif/indie, indie folk, klasik gitar, Anadolu rock, slowcore/dream pop. En sevdiklerin: Pinhani, Cem Karaca, Bon Iver, Cigarettes After Sex, Şam.
Gece 23:00-01:00 arası, karanlık temada, kablolu kulaklıkla uzun oturumlar yaparsın; beyaz/parlak zemin rahatsız eder, yavaş ve yumuşak animasyon beklersin.
Görme/işitme/hareket kısıtların yok; orta derecede hareket hassasiyetin var, uzun şarkı sözünü okursun.
Sabır eşiğin ~5 sn; ama gece oturumunda sabırlısın — duygu akışını bozan reklam/hata seni uygulamadan soğutur; premium'u ailen üzerinden kullanırsın.
Test edilecek akışlar: ana sayfa duygusal karşılama, slow geçiş animasyonları, mood-geçiş (ADR-023 satır 16), şarkı sözü ekranı, karanlık tema kontrastı, tekrar dinleme/az skip, arama (Türkçe karakter), hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- "An" bazlı dinlersin; algoritma önerisine değil, ruh hâline göre listeye dalarsın.
- Aynı şarkıyı arka arkaya tekrar dinlersin; çalma sırası bozulursa güvenin düşer.
- Şarkı sözü ekranını sık açarsın; söz metni taşarsa veya kontrast düşükse huzursuz olursun.
- Türkçe karakterli sanatçı/şarkı adlarında bozulma görürsen uygulamaya güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, sakin ve içe dönük ton — romantik persona uzun replik kabul eder, aceleci ton uymaz |
| Yasak veri | Prompt'ta gerçek kişi/konum verisi yok; aile/gelir detayları hiç yazılmadı (KVKK §4.7) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa duygusal karşılama | LCP ≤ **2500 ms**, CLS ≤ **0.1**, yumuşak renk paleti render edilir | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Slow geçiş animasyonu (sayfa/mood) | Geçişler yavaş ama kesintisiz; yerleşim sıçraması yok (CLS ≤ 0.1) | Trace + screenshot |
| 3 | Mood-geçiş akışı (ADR-023 satır 16) | BPM eşikleri banka tür aralığı (P4.4/P4.5) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 4 | Şarkı sözü ekranı | Metin kontrastı ≥ **4.5:1** (1.4.3); uzun söz kaydırılabilir; taşma/örtüşme yok | a11y audit (axe) + DOM assertion |
| 5 | Karanlık tema kontrastı | Metin ≥ 4.5:1 (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Lighthouse / axe |
| 6 | Tekrar dinleme / az skip davranışı | Çalma sırası durumu bozulmadan sürer; liste localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | İleri / geri navigasyon | INP ≤ **200 ms**; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 9 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ 0.1 | Trace + console log |
| 10 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 11 | Playlist oluşturma / paylaşıma alma | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 12 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 13 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok | Lighthouse / axe |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 17 | Gece uzun oturum dayanıklılığı | 30+ dk oturumda bellek sızıntısı/performans düşüşü yok; tema okunurluğu korunur | Manuel + performance trace |
| 18 | Düşük ışıkta okunurluk (gece modu) | Karanlık temada metin ≥ 4.5:1 (1.4.3) ve UI ≥ 3:1 (1.4.11) korunur; parlak beyaz parlama yok | a11y audit + screenshot (eşikler P5.4 `VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 18 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (17) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (16 yaş eşiği geçildi) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Mood-taxonomy §3.2.1 boyut kodları (Uyumluluk (C), Dışadönüklük (O)) | Vault içi çelişki | mood-taxonomy §3.2.1 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Romantik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.1 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.1 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | İkincil mood: bu turda yok (eski dosyada "Sosyal") | Vault ataması | [[personas/index]] §6.3 (tekil küme) | eski salt-okunur dosya | `⚠️ VERIFICATION REQUIRED` — çelişki §7.2 |
| 14 | Tür BPM aralıkları (Pop 80-120 · Nu R&B 70-110) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 15 | Indie/Anadolu rock/slowcore için tür BPM aralığı | Doğrulanmadı | research-bank P4.4 (bu türler YOK) | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 16 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Kişisel BPM tercihi (50-110) | Persona tercihi (kurgusal) | — | — | `Kaynak: kurgusal (persona verisi — tercih, ölçüm değil)` |
| 18 | Favori sanatçı adları (Pinhani, Cem Karaca, Bon Iver, CAS, Şam) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 19 | Romantik küme BPM notu (60-100) | Doğrulanmadı | mood-taxonomy §3.2.1 (⚠️ işaretli) | research-bank P4 (böyle aralık YOK) | `⚠️ VERIFICATION REQUIRED` — testte kullanılmadı |
| 20 | Cihaz: iPhone 14 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | Dizüstü bilgisayar + kablolu kulaklık spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 27 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Eski dosyadaki istatistik iddiaları (tür yüzdeleri %25…, premium %85, Spotify 2025 notları) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 30 | `persona_id` biçimi (`CM-AD-17-ROM-ANK` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu ataması | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |
| 31 | Mood-geçiş test etkisi (ADR-023 satır 16) | Vault verisi | [[mood-taxonomy]] §3.2.1 "Test etkisi" | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`alparslan-demir-romantik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 14 viewport = 390×844` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 92 BPM` | Tür aralığı (P4.4/P4.5, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `%25 indie / %85 premium` (eski dosya yüzdeleri) | Yüzsüz istatistik yazılmaz → yalnız nitel ("yüksek", "düşük") kurgusal ifade |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.1 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `coremusic.net.old/.ai/personas/genc-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 505`, `mojibake = 0` | Yazım sonrası zorunlu |

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
| 505 satır altı | `verify.lines < 505` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Alparslan için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yüzsüz yüzde istatistiği | "%85 premium" gibi | Yüzdeler bankada yok → sil, nitel ifade yaz |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 505 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ) — bu persona 17 → `false` + gerekçe
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok; indie türü aralığı yazılmadı (bankada YOK)
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 18 (asgari 10) |
| Kaynak & Doğrulama satırı | 31 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 505 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.1) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 17 | 17 | Çakışma YOK — [[personas/index]] §6.3 ile tutarlı |
| İkincil mood | "Sosyal (seçici)" | Yok (bu turda) | [[personas/index]] §6.3 tekil küme atar; eski tema §3.5.8 kişilik satırlarına taşındı |
| Birincil cihaz | iPhone 14 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM aralığı | 50-110 BPM (kişisel/kaynaksız) + tür yüzdeleri | Kişisel tercih kurgusal işaretli; tür aralığı bankada YOK (indie) → `⚠️ VERIFICATION REQUIRED` | §4.5 + X-2 |
| Tür yüzdeleri / premium %85 | Kaynaksız yüzdeler | yazılmadı (nitel ifade) | §4.5 kuralı 4 (bankada yok → yazma) |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-AD-17-ROM-ANK` — şablon `CM-XX-YY-ZZZ-XXX` (XX=AD, YY=17, ZZZ=ROM, XXX=ANK) | 2026-09-26 tarihinde şablon biçimine düzeltildi; atama görev tablosu/kataloğu korunur |
| Big Five kodları | mood-taxonomy §3.2.1 "Uyumluluk (C), Dışadönüklük (O)" | Uyumluluk=A, Dışadönüklük=E (P7.3) | P7.5 "kod esastır"; çelişki §3.5.3'te |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; veli_onayı `false` (17 ≥ 16, ⚠️ DERIVED) + KVKK notu eklendi; yüzsüz istatistikler yazılmadı | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/alparslan-demir-romantik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
