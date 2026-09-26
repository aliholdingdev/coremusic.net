---
title: "CoreMusic — Persona: Doruk Öztürk"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/doruk-ozturk-hiphop-rap"
updated: 2026-09-26
group: genc-erkek
age: 17
mood: Hip-Hop
persona_id: CM-DO-17-HIP-KOC
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Doruk Öztürk

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 17 yaşındaki bir Kocaelili hip-hop gencinin gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Hip-Hop, §3.3 satır 15), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Hip-Hop ([[personas/index]] §6.3 ataması) |
| Test odağı | Tür filtresi (rap/trap/drill), playlist/oturum devamlılığı, söz senkronu, hızlı skip |
| Veli onayı | `veli_onayı_gerekli: false` (17 yaş — 16 eşiği aşıldı; eşik ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Tür filtresi (rap / trap / drill) | Level 1 + 2 | Test etkisi "tür filtresi" — filtre kesinliği ve geri bildirimi ([[mood-taxonomy]] §3.3 satır 15) |
| Playlist / oturum devamlılığı | Level 2 + 3 | Kesintisiz oturum davranışı — sayfa değişiminde sıra ve oynatıcı durumu kaybolmaz |
| Söz senkronu (lyrics) akışı | Level 1 + 2 | "Lirik ve ritim duyarlı" tanımı → söz satırı zamanlaması testi |
| Hızlı skip navigasyon | Level 2 + 3 | Test etkisi "hızlı skip" — INP ≤ 200 ms beklentisi |
| Türkçe karakter arama (ç, ğ, ı, İ, ö, ş, ü) | Level 2 + 3 | UTF-8/mojibake denetimi; sanatçı adı araması |
| Ağ yavaşlatma (mobil veri) | Level 2 + 3 | 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ (research-bank P8.3 VERIFIED) |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki aile profili (gelir, aile üyeleri, din), burç/kan grubu ve 100+ satırlık fiziksel detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.3 | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Doruk Öztürk | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 17 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 7 Aralık 2008 | Kaynak: kurgusal (persona verisi; yaş 17 ile tutarlılık için türetildi — eski dosyada 2011/15 yaş vardı, §7.2) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Kocaeli / İzmit — Yahya Kaptan | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı) |
| **Okul / Meslek** | Devlet Anadolu Lisesi 12. sınıf (MEB okul türü: Anadolu Lisesi, 4 yıl) | Kaynak: kurgusal (persona verisi); okul türü: [k1]+[k2] (research-bank P2.7 VERIFIED) |
| **Ulaşım** | Yürüyüş + toplu taşıma (Kocaeli içi); mahallede ayağı yere değer | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `doruk_flows` (beat/söz denemeleri için) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-DO-17-HIP-KOC` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` biçimi notu:**

| Alan | Değer |
|------|-------|
| Bu turda verilen ID | `CM-DO-17-HIP-KOC` (görev tablosu / registry eşlemesi) |
| Şablon §3.5.1 biçimi | `CM-XX-YY-ZZZ-XXX` (ör. `CM-DO-17-HIP-KOC`) |
| Parça okuması | `CM` sabit · `DO` = Doruk Öztürk · `17` = yaş · `HIP` = Hip-Hop · `KOC` = Kocaeli |
| Durum | Şablon formatı ile uyumlu (`CM-XX-YY-ZZZ-XXX`) — 2026-09-26 düzeltmesi; §7.2 |

> **Veli onayı notu (zorunlu — 17 yaş):** Bu persona 17 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. **Gerekçe:** CoreMusic veli-onayı eşiği 16'dır (research-bank P6.4 son satır → `⚠️ DERIVED`); persona bu eşiği aştığı için veli onayı akışı **test edilmez**, 16 yaş altı persona'larla (Emirhan Ç.) ayrıştırılır. Dayanak tablosu değişmez: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir) → `Kaynak: [k1] + [k2]` (P6.4 VERIFIED). Not: 17 yaş = ergin olmadığından (TMK 18) ödeme/satın alma adımları ayrı senaryoda işaretlenir; persona tamamen kurgusaldır (6698 m.5/1).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, kilo takibi, tıbbi ölçüm…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 177 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 68 kg, normal | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah kısa saç (dread denemesi), kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; sokakta/toplu taşımada kulaklıkla dinler | Kaynak: kurgusal (persona verisi); kulaklık modeli §3.5.6 |
| **Giyim / temsil notu** | Streetwear/hip-hop: oversize tişört, bomber ceket, zincir, snapback; profil fotoğrafı sokakta çekilmiş (kontrast: açık zemin × koyu saç — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Hareket hâlinde (yürüyüş/toplu taşıma) tek elle hızlı kontrol → büyük hedef + az adım; söz senkronunda göz ekrana yakın | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 58 | Rap battle ve arkadaş ortamında açılır; yeni ortamlarda (open mic sahnesi) temkinli başlar, ısınınca yön alır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 52 | Doğru bildiğini söyler — baba tartışmaları dâhil; asi tarafı vardır ama arkadaş grubunda sadıktır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 49 | Müzik yaparken planlıdır (beat/söz düzeni); ders ve ev işlerinde ertelemeci davranır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 55 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 55 = orta taraf; çatışmayı müziğe döker, gece kayıtları duygusal dalgalanma dönemlerinde artar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 78 | Prodüksiyon, yeni sound'lar, beat denemeleri — sürekli öğrenir; keşif listesi hiç kapanmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Mood eğilimi eşlemesi | [[mood-taxonomy]] §3.3 satır 15 "Yüksek O, orta A" → O 78 (yüksek), A 52 (orta) ile uyumlu |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Hip-Hop |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Rap/trap odaklı, lirik ve ritim duyarlı" ([[mood-taxonomy]] §3.3 satır 15) |
| **Big Five eğilimi (alıntı)** | "Yüksek O, orta A" ([[mood-taxonomy]] §3.3 satır 15) — §3.5.3 puanlarıyla eşleştirildi |
| **Tetikleyici durumlar** | Gece kayıt oturumu (söz takibi); playlist'in sırasının bozulması (devamlılık); yeni sound keşfi (tür filtresi) |
| **UI etkisi (test etkisi alıntısı)** | "Söz senkronu, hızlı skip, tür filtresi" ([[mood-taxonomy]] §3.3 satır 15 "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesi; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Hip-Hop); ikincil bu turda yok — eski dosyada "İkincil Mood: Sosyal" farkı §7.2'de |
| Mood adı normalizasyonu | Eski etiket "Hip-Hop-**rap**" → bu SSOT'ta mood **"Hip-Hop"** (index §6.3 satır 404 ile birebir); dosya adı `doruk-ozturk-hiphop-rap.md` 2026-09-26'da index §6.3 ile eşleşti — kalan tek fark mood etiketidir (§7.2) |
| BPM notu | §3.3 satır 15'teki "**80-140 BPM** ⚠️" notu birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; testte research-bank P4.4/P4.5 tür aralıkları kullanılır |
| Küme kuralı | Hip-Hop kümesi bu grupta ×2 (Emirhan Ç., Doruk Ö.) → test odağı §7.2'de farklılaştırıldı: Doruk = tür filtresi + playlist/oturum devamlılığı |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Tür filtresi beklentisi (rap/trap/drill) | Filtre kesin çalışır; temizleme tam geri dönüş; sonuçlar tür aralığı (P4.4/P4.5) içinde, şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 2 | Playlist/oturum devamlılığı | Sayfa/sekme değişiminde sıra ve oynatıcı durumu kaybolmaz | Level 3 E2E + durum kontrolü |
| 3 | Söz senkronu beklentisi (lirik duyarlı) | Aktif söz satırı zamanlamalı vurgulanır; kayma/sıçrama yok | DOM assertion + zamanlama kontrolü |
| 4 | Hızlı skip davranışı | INP ≤ **200 ms**; skip sonrası anlık geri bildirim, sıra güncellenir | Performance trace (P8.1 VERIFIED) |
| 5 | Türkçe karakterle arama | Türkçe karakter kabulü (ç, ğ, ı, İ, ö, ş, ü); sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 6 | Ritmik/geçiş hassasiyeti | Parça geçişinde CLS ≤ **0.1**; oynat kontrolü kaybolmaz (2.4.11) | Trace + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe rap (underground + mainstream) 2) Trap / drill 3) ABD hip-hop (lyrical) 4) Melankolik rap 5) Old school Türkçe rap | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Ezhel, Şam, BLOK3, Ceza, Kendrick Lamar | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Hip Hop **80-130** (P4.4) · trap **70-110** · boom-bap **85-95** · modern rap **~140** (P4.5) | Kaynak: nevamuzik.com.tr + vocuno.com/tr/bpm-algilayici (P4.5) · turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Prodüksiyonda yüksek tempo (trap/drill); melankolik anlarda düşük tempo (tür aralığı bankada YOK, yazılmadı) | Kaynak: kurgusal (persona verisi — persona tercihi); melankolik rap BPM: `⚠️ VERIFICATION REQUIRED` |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:45-08:30 (yolda), teneffüs, 17:00-19:00 (mahalle), 23:30-01:30 (gece kayıt) · Hafta sonu 14:00-18:00 (sokak/open mic) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Türkçe rap arşivi + playlist devamlılığı) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Instagram/YouTube rap topluluğundan düşen yeni parçaları arar; "buna benzer" önerisi beklentisi | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Orta — harçlığıyla dener ama çoğunlukla ücretsiz sürümü idare eder (17 yaş, ödeme adımları ayrı senaryo) | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe rap (underground + mainstream) | Kendi ifade biçimi; sözleri yakınından konuştuğu için lirik takibi kritik |
| 2 | Trap / drill | Sert enerji; prodüksiyon ilgisi — beat denemelerinin kaynağı |
| 3 | ABD hip-hop (lyrical) | Lirik dersi alır, söz yazarlığına kaynak |
| 4 | Melankolik rap | Duygusal anlarda terapi gibi çalışır |
| 5 | Old school Türkçe rap | Saygı duruşu — tekniği inceler |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:45-08:30 | Yolda (toplu taşıma) | Türkçe rap (kulaklık) |
| Hafta içi | 12:30-12:45 | Teneffüs | Trap / drill (kısa) |
| Hafta içi | 17:00-19:00 | Mahalle / sokak basketbolu | Karışık rap listesi |
| Hafta içi | 23:30-01:30 | Gece kayıt oturumu | Enstrümantal beat arşivi |
| Hafta sonu | 14:00-18:00 | Open mic / sokak | Yerel rap keşfi |
| Hafta sonu | 21:00-22:00 | Playlist düzenleme | "Kocaeli Nights", "Verse Lab", "Cypher" |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.3 satır 15'in 80-140 BPM küme notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Eski dosyadan taşınan cihaz (spec'siz) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Android sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Chrome (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Dışarıda mobil veri, evde Wi-Fi; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Bluetooth kulaklık (hareket hâlinde) + evde hoparlör — model/spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark (gece kayıt oturumları — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 5-6 saat (telefon geneli — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — beat uygulaması, playlist/filtre güç kullanımlarını bilir; kısayolları kısmen keşfeder | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Evde ortak dizüstü (video/kayıt aktarımı) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); gece kayıt oturumlarında düşük ışık | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — dark temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; hareket hâlinde tek taraflı kulaklık (çevre sesi duyar) | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; yürürken/toplu taşımada tek elle hızlı kontrol | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme yok (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | hareket hâlinde dikkat dağılır; oturum ortasında konum kaybına tahammülü yok | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — playlist/devamlılık akışında kısa yol bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi kurgusal olmak zorunda; 17 yaş → veli akışı YOK, ödeme adımları işaretli (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Dokunma hedefi | Mood-taxonomy §3.3'te 44/48 px iddiası YOK; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Rap onun için müzik değil, ifade ve terapidir; kendi sözlerini yazar, gece herkes uyuduktan sonra telefonuna kayıt yapar.
- Babasıyla çatışma ("adam ol, iş bul") hem acı verir hem ilham kaynağıdır; en iyi verse'leri oradan çıkar.
- Sosyal çevresinde dışa dönüktür (rap battle, open mic); yeni sahnelerde temkinli başlar.
- Playlist'in sırası ve oturumun devamlılığı onun için kutsaldır — bozulan sıra hemen fark edilir.
- Türkçe karakter bozukluğu görürse platforma güveni düşer (mojibake = güven kırılması).

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 17 yaş → kendi harçlığı kararı; veli onayı eşiği aşıldığı için akış veli beklemez (`veli_onayı_gerekli: false`), ödeme adımı senaryoda işaretli | Kaynak: kurgusal (persona verisi); politika: ⚠️ DERIVED (P6.4) |
| **Sabır eşiği** | ~3 sn; oturum ortasında konum kaybı görürse uygulamayı kapatır, başka listeden devam eder | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sert tepki ("çöktü" der), story atar; teknik hata metnini okumaz — hızlı kurtarma şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Orta-düşük — sıçramayı atlar ama arka arkaya reklamda uygulamayı bırakır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Video + deneme; ayarları kurcalayarak bulur, kılavuz okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Rap çevresiyle playlist paylaşır; ortak çalma sırası beklentisi | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece 01:30 sonrası tek dokunuş "devam et" akışı bekler | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Playlist devamlılığı + doğru Türkçe karakter + söz senkronu tutarlılığı → platforma bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Hip-Hop kümesi |

*Erişilebilirlik etkisi (özet):* hareket hâlinde tek elle hızlı kontrol → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), geri tuşu/skip kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Doruk Öztürk'sün. 17 yaşında, Kocaeli/İzmit-Yahya Kaptan'da yaşıyorsun, Devlet Anadolu Lisesi 12. sınıfta okuyorsun.
Hip-Hop kişiliğin var: Türkçe rap/trap ve drill dinlersin, kendi sözlerini yazıp gece telefonuna beat üzerine kayıt yaparsın, "bir gün patlayacağım" inancın tamdır.
Müzik zevkin: Türkçe rap (underground + mainstream), trap/drill, ABD hip-hop, melankolik rap, old school Türkçe rap. En sevdiklerin: Ezhel, Şam, BLOK3, Ceza, Kendrick Lamar.
Telefonun (model spec'i doğrulanmadı), hareket hâlinde kulaklıkla dinlersin; dark temayı tercih edersin; teknoloji seviyen 7/10'dur.
Görme / işitme / hareket kısıtların yok; ama yürürken/toplu taşımada tek elle hızlı kontrol yaparsın — büyük butonlar ve tek dokunuş beklersin.
Sabır eşiğin ~3 sn'dir; playlist sırasının bozulması ve uzun yükleme seni uygulamadan koparır; 17 yaşındasın, veli onayı beklemezsin ama ödeme adımları ayrı senaryodur.
Test edilecek akışlar: tür filtresi (rap/trap/drill), playlist/oturum devamlılığı, ana sayfa hızlı başlatma, söz senkronu (lyrics), hızlı skip, Türkçe karakter arama, ağ yavaşlatma, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Tür filtresi kesin çalışmalıdır; temizleme sonrası tam liste geri gelmezse filtre güveni kaybolur.
- Playlist'te sıra kaybolursa veya oturum sıfırlanırsa uygulamayı bırakırsın.
- Söz senkronunda kayma olursa parçayı kapatırsın — lirik zamanlaması senin için kritiktir.
- Türkçe karakterli sanatçı/şarkı adlarında bozulma görürsen uygulamaya güvenin düşer.
- Yaşın 17 → veli onayı beklemezsin; hata metni yerine kısa yol istersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, direkt ve hızlı ton — genç hip-hop persona'sı uzun açıklama kabul etmez |
| Gerçek veri yasak | Prompt'ta gerçek çocuk/kişi verisi yok — hepsi kurgu (P6.5); veli onayı yalnız test alanı |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (hızlı başlatma) | LCP ≤ **2500 ms**, ana kontroller üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Tür filtresi (rap / trap / drill) — uygula | Filtre kesin uygulanır; sonuçlar tür aralığı (P4.4/P4.5) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 3 | Tür filtresi — temizle | Filtre sıfırlanır, tam liste geri gelir (öncesi/sonrası sayısal karşılaştırma) | DOM assertion + sayaç karşılaştırması |
| 4 | Playlist/oturum devamlılığı — arka plana al, geri dön | Aynı şarkıda/aynı sırada devam; sıra kaybolmaz | Level 3 E2E + `visibilitychange` durum kontrolü |
| 5 | Playlist/oturum devamlılığı — ekran kilidi, aç | Oynatıcı durum ve sıra korunur; kontroller aktif | Manuel cihaz testi + E2E |
| 6 | Hızlı skip navigasyon (Hip-Hop davranışı) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; sıra güncellenir | Performance trace + DOM assertion |
| 7 | Söz senkronu (lyrics) açılışı | Aktif söz satırı zamanlamalı vurgulanır; kayma yok, mobilde görünür | DOM assertion + zamanlama kontrolü |
| 8 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 9 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; oynat kontrolü görünür; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 10 | SPA navigasyon (sayfalar arası — oturum devamlılığı) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 11 | Reklam/kesinti sonrası devam | Reklam sonrası aynı şarkı sırasına dönülür; akış bozulmaz | Manuel + oturum akış logu |
| 12 | Bağlantı yavaşlatma (mobil veri / hareket hâlinde) | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok; LCP ≤ 2500 ms | Network throttling + trace (P8.3 `VERIFIED`) |
| 13 | Uzun oturum dayanıklılığı (50+ şarkı, 10 kez ön/arka geçiş) | Bellek sızıntısı yok; geçiş ≤ 200 ms (INP); CLS ≤ **0.1** | Performans profili + oturum dayanıklılık testi |
| 14 | Favori / playlist paylaşımı | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 15 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 16 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 17 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 18 | Veli onayı adımı YOK (17 yaş) | Satın alma akışı veli onayı istemez; ödeme adımı senaryoda işaretli | Level 3 E2E + manuel (P6.4 `⚠️ DERIVED` eşiği) |

*Asgari 10 satır (şablon §3.5.10) — 18 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (17) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (16 yaş eşiği) + gerekçe | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Hip-Hop) | Vault referanslı atama | [[mood-taxonomy]] §3.3 satır 15 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı ("Rap/trap odaklı, lirik ve ritim duyarlı") | Vault verisi | [[mood-taxonomy]] §3.3 satır 15 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Mood Big Five eğilimi ("Yüksek O, orta A") | Vault verisi (kurgusal eğilim) | [[mood-taxonomy]] §3.3 satır 15 | — | `Kaynak: [[mood-taxonomy]] (vault verisi; kurgusal)` |
| 14 | Hip-Hop küme BPM notu (80-140) | Doğrulanmadı | mood-taxonomy §3.3 satır 15 (⚠️ işaretli) | research-bank P4 (böyle aralık YOK) | `⚠️ VERIFICATION REQUIRED` — testte kullanılmadı |
| 15 | BPM tür aralıkları (Hip Hop 80-130) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | BPM alt tür aralıkları (trap 70-110 · boom-bap 85-95 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 17 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 18 | Melankolik rap BPM aralığı (bankada tür satırı yok) | Doğrulanmadı | research-bank P4.4 (melankolik YOK) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 19 | Favori sanatçı adları (Ezhel, Şam, BLOK3, Ceza, Kendrick Lamar) | Persona tercihi (kurgusal); sanatçı realitesi P4 kapsamı dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Ezhel — tür bilgisi (hip hop, rap, reggae, trap; 2017 "Müptezhel") | Gerçek-dünya (P4.2) | tr.wikipedia Ezhel | research-bank P4.6 (ikinci kaynak paket notu) | `Kaynak: [k1]` (P4.2 `VERIFIED (sanatçı/tür)`) — yalnız tür bilgisi kullanıldı |
| 21 | "Ezhel Spotify TR en çok dinlenen" iddiası | Tek kaynak (EXCLUDED) | research-bank P4.6 / X-3 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 22 | Okul türü: Anadolu Lisesi (4 yıl) | Gerçek-dünya | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `Kaynak: [k1] + [k2]` (P2.7 `VERIFIED`) |
| 23 | Cihaz modeli (eski dosyadan — spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 25 | Diğer cihaz/kulaklık spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 26 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 27 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 28 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 29 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 30 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | `persona_id` biçimi (`CM-DO-17-HIP-KOC` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |
| 33 | Mood etiketi normalizasyonu ("Hip-Hop-rap" → "Hip-Hop") | Eski dosya etiketi | eski etiket "Hip-Hop-rap" | [[personas/index]] §6.3 satır 404 (dosya `doruk-ozturk-hiphop-rap.md`) | `Kaynak: kurgusal (persona verisi)`; dosya adı 2026-09-26'da index ile eşleşti |
| 34 | Eski dosyadaki yüzdeler/istatistikler (% tür dağılımı, izlenme sayısı) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 35 | Aile profili / gelir / burç / kan grubu satırları | Kapsam dışı (bu tur şablonu; X-8) | persona-template §3.5 (alan yok) | research-bank §6.4 X-8 | Yazılmadı — §7.2 |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`doruk-ozturk-hiphop-rap.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Cihaz viewport = 412×915` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 128 BPM` | Tür aralığı (P4.4/P4.5, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Aile geliri 28.000 TL` / `burç Yay` / `kan grubu AB RH+` | `yazılmadı — bu tur şablonu kapsam dışı (X-8)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 satır 15 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia, aile/gelir/burç/kan grubu taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/genc-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
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
| Persona-özel eşik uydurma | "Doruk için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] 16 yaş ve üzeri: `veli_onayı_gerekli: false` + gerekçe (eşik P6.4 `⚠️ DERIVED`; TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
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
| Test adımı | 18 (asgari 10) |
| Kaynak & Doğrulama satırı | 35 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 satır 15) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 15 | 17 | [[personas/index]] §6.3 kazanır |
| Doğum tarihi | 7 Aralık 2011 | 7 Aralık 2008 | Yaş 17 ile tutarlılık (index); ay/gün korundu, yıl türetildi |
| Okul sınıfı | 9. sınıf | 12. sınıf (yaş 17 ile tutarlı) | Yaş index'ten geldi → sınıf yaşa göre uyarlandı |
| Dosya adı / mood etiketi | `doruk-ozturk-hiphop-rap.md` · mood "Hip-Hop-rap" | `doruk-ozturk-hiphop-rap.md` · mood "Hip-Hop" | Mood etiketi [[personas/index]] §6.3 satır 404 + [[mood-taxonomy]] §3 kazanır (normalleştirildi); dosya adı 2026-09-26'da index ile birebir — sapma kalmadı |
| Mood adı | Hip-Hop-rap | Hip-Hop | [[personas/index]] §6.3 ile birebir; uydurma küme adı yazılmaz |
| Hip-Hop kümesi ×2 (Emirhan Ç., Doruk Ö.) | Tek dosya varsayımı | Aynı kümede 2 persona | Test odağı farklılaştırıldı: Doruk = tür filtresi + playlist/oturum devamlılığı; Emirhan = söz senkronu + hızlı skip + Türkçe karakter arama |
| Birincil cihaz | Eski dosyadan taşınan cihaz (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM aralığı | Kaynaksız kişisel aralık (eski dosya) | Tür aralıkları P4.4/P4.5 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-DO-17-HIP-KOC` — şablon `CM-XX-YY-ZZZ-XXX` (XX=DO, YY=17, ZZZ=HIP, XXX=KOC) | 2026-09-26 tarihinde şablon biçimine düzeltildi; atama görev tablosu/kataloğu korunur |
| Aile profili (üyeler, gelir ~28.000 TL, ev tipi, din) | Eski dosyada mevcut | yazılmadı | Bu tur şablonu §3.5 kapsam dışı + X-8 |
| Burç / kan grubu (Yay, AB RH+) | Eski dosyada mevcut | yazılmadı | Bu tur şablonu §3.5 kapsam dışı |
| Eski AI rol kartı iddiaları (izlenme sayısı, "patlayacağım" dışındaki kaynaksız iddialar) | Kaynaksız iddialar | yazılmadı (yalnız kurgusal karakter olarak korundu) | §4.5 kuralı 4 (bankada yok → yazma); X-3 `⚠️ VERIFICATION REQUIRED` |
| Yüzdeler/istatistikler (% tür dağılımı) | Eski dosyada mevcut | yazılmadı | X-8 — istatistik yasağı |
| İkincil mood (Sosyal) | "İkincil Mood — Sosyal" | İkincil atama yok | [[personas/index]] §6.3 tekil küme atar |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3'e göre düzeltildi (15→17), doğum yılı 2011→2008 (ay/gün korundu), sınıf yaşa göre uyarlandı; dosya adı/mood etiketi "-rap" ekiyle normalleşti; veli_onayı=false + gerekçe; Hip-Hop kümesi ×2 test odağı farklılaştırıldı; aile/gelir/burç/kan grubu yazılmadı | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/doruk-ozturk-hiphop-rap
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
