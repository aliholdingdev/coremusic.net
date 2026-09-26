---
title: "CoreMusic — Persona: Toprak Erdem"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/toprak-erdem-sosyal-trend"
updated: 2026-09-26
group: genc-erkek
age: 13
mood: Sosyal
persona_id: CM-TE-13-SOS-MER
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Toprak Erdem

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 13 yaşındaki, trend ve sosyal paylaşım odaklı bir ortaokul gencinin gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Sosyal §3.2.7), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Sosyal ([[personas/index]] §6.3 ataması) |
| Test odağı | Paylaşım akışı (paylaş → yayılım), işbirlikli playlist, sosyal feed, trend keşif, mobil dokunma hedefi, veli onay akışı |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4); 13 yaş COPPA eşiğinde (13) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / trend akış | Level 2 + 3 | Sosyal UI beklentisi: "Paylaşım butonları, sosyal özellikler öne çıkar" ([[mood-taxonomy]] §3.2.7) |
| Paylaşım akışı (paylaş → yayılım) | Level 2 | Küme test etkisi + ADR-023 satır 20 (gizli hesap / 280 karakter sınırı) |
| İşbirlikli / ortak playlist | Level 2 + 3 | "İşbirlikli playlist, sosyal feed" ([[mood-taxonomy]] §3.2.7 Test etkisi) |
| Trend keşif (Pop / Rap / EDM / J-Pop) | Level 1 + 2 | BPM yalnız tür aralığı (P4.4 Pop 80-120 · Hip Hop 80-130 · Dance 110-135 · J-Pop 150-190) |
| Mobil dokunma hedefi + kontrast | Level 2 | İşaretçi hedefi ≥ **24×24 px** (2.5.8) · metin ≥ **4.5:1** (1.4.3) · UI ≥ **3:1** (1.4.11) |
| Veli onay akışı (13 yaş) | Level 2 + 3 | 3.3.8 erişilebilir kimlik doğrulama; onay adımı kısa ve net |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki aile/kişilik 100+ satır detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); eski dosyadaki yüzdeler (%35 pop, %50 premium, 600 takipçi) kaynaksızdır → yazılmaz.

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
| **Ad Soyad** | Toprak Erdem | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 13 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 25 Temmuz 2013 | Kaynak: kurgusal (persona verisi; eski salt-okunur dosyadaki 25 Temmuz 2014 → age 12 idi; age 13 ile tutarlı olacak şekilde türetildi → `⚠️ DERIVED`) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Mersin / Yenişehir — Pozcu | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Özel Mersin Ortaokulu; 7. sınıf | Kaynak: kurgusal (persona verisi; eski dosyada 6. sınıf → 13 yaş ile tutarlılık düzeltmesi, §7.2) |
| **Ulaşım** | Okul servisi; AVM/sahil etkinliklerinde aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Sosyal medya hesabı (Instagram/TikTok — rumuz kurgusal) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-TE-13-SOS-MER` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` formatına uygundur — 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `TE` | `TE` | Ad soyad baş harfleri (Toprak Erdem) |
| `13` | `13` | Yaş (index §6.3) |
| `SOS` | `SOS` | Mood kodu (Sosyal) |
| `MER` | `MER` | Şehir kodu (Mersin) |

> **Format uyumu:** Atanan `CM-TE-13-SOS-MER` şablon formatı (`CM-XX-YY-ZZZ-XXX`) ile birebir örtüşür → 2026-09-26 düzeltme (şablon §3.5.1; atama görev tablosu). Yaş/şehir parçası (`13` / `MER`) bu seride bulunur.

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 13 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13** (13 yaş = eşiğe ulaşıldı; COPPA 13 yaş altını kapsar), GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). 13 yaş GDPR 16 eşiğinin altında → CoreMusic'in 16 yaş altı politikası gereği onay **zorunlu** → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki aile/profil 100+ satır ayrıntısı bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 158 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 48 kg, orta yapı; haftada 2 gün basketbol kursu (sosyalleşme amaçlı) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah, dalgalı, modern ortadan ayrım; kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu); kulaklık §3.5.6 | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Trend bilinçli spor-marka giyim; profil fotoğrafında canlı renk × nötr zemin (kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Görsel/işitsel/hareket kısıtı yok; tek-elle mobil kullanım + hızlı dokunma beklentisi | Kaynak: kurgusal (persona verisi); dokunma hedefi eşikleri: `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 90 | Sınıfın en popüler çocuğu; her ortamda ilgi odağıdır, herkesi tanır — sosyal onay onun yakıtıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Arkadaşlarıyla arası iyidir ve paylaşır; ama "ben bilirim" tavrı ile bazen şımarık davranır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 50 | Yaşına göre orta; dersleri idare eder, telefonda/sosyal medyada kendini sınırlamakta zorlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 50 = orta; istemediğinde tepki gösterir, ruh hali sosyal medya onayına bağlıdır — ama genelde neşelidir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 80 | Yeni trendler, yeni uygulamalar, yeni akımlar — hemen dener; "kaçırma korkusu" (FOMO) belirgindir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.2.7 Sosyal satırında "Yüksek **Dışadönüklük (O)**, yüksek **Uyumluluk (C)**" yazar — bu kullanım dosyanın puanlarıyla (E 90 · A 60) nitel olarak örtüşür; ancak **O** harfi Dışadönüklük yerine kullanıldığı için P7.3 (O=Openness) ile çelişir → kod harfleri `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır"), çelişki §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Sosyal |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar; eski dosyada "Enerjik" ikincili §7.2) |
| **Küme tanımı (alıntı)** | "Paylaşan, takip eden, akışta dolaşan; müzik sosyalleşme aracıdır" ([[mood-taxonomy]] §3.2.7) |
| **Tetikleyici durumlar** | Yeni trend/viral şarkı keşfi; arkadaşla ortak playlist/paylaşım; akışta gezinirken "şu an dinliyorum" anını sergileme; okul koridorunda duyulan şarkıyı bulma |
| **UI etkisi** | "Paylaşım butonları, sosyal özellikler öne çıkar" + Test etkisi "**Paylaşım akışı, işbirlikli playlist, sosyal feed**; ADR-023 satır 20 (paylaşım → yayılım / gizli hesap / 280 karakter sınırı)" ([[mood-taxonomy]] §3.2.7) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Sosyal); ikincil bu turda yok — eski kaynakta mood farkı §7.2'de |
| Test etkisi | [[mood-taxonomy]] §3.2.7 "Paylaşım akışı, işbirlikli playlist, sosyal feed" + ADR-023 satır 20 — küme satırındaki **BPM 100-140** değeri taksonomide `⚠️` ile işaretlidir (§1.2 kurgusal önerme) → `⚠️ VERIFICATION REQUIRED`, test eşiği olarak **kullanılmaz**; testte banka tür BPM aralıkları (P4.4/P4.5) esas alınır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Paylaşım beklentisi (Sosyal) | Paylaş butonu görünür ve çalışır; paylaşım → yayılım akışı tamamlanır; 280 karakter sınırı uygulanır | Level 2 + 3 (ADR-023 satır 20) |
| 2 | Sosyal feed / akış | Akış sonsuz yüklenir, CLS ≤ **0.1**; yeni öğeler düzeni bozmaz | Performance trace + screenshot |
| 3 | İşbirlikli playlist | Ortak düzenleme/davet akışı çalışır; erişim reddi düzgün iletilir | Level 3 E2E |
| 4 | Gizli hesap / izin sınırı | Gizli hesap paylaşımında yetkilendirme uyarısı görünür; hata tonu kibar | Level 2 (ADR-023 satır 20) |
| 5 | Trend keşif (Pop/Rap/EDM/J-Pop) | Sonuçlar tür BPM aralığı içinde (P4.4 Pop 80-120 · Hip Hop 80-130 · Dance 110-135 · J-Pop 150-190); şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 6 | Mobil tek-elle hızlı kullanım | Dokunma hedefi ≥ **24×24 px** (2.5.8); kontrast ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) | Erişilebilirlik audit (axe / Lighthouse) |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Pop (Türkçe & Global) 2) Türkçe Rap / Pop-Rap 3) Reggaeton / Latin Pop 4) EDM / Future Bass 5) Anime OST / J-Pop | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Semicenk, BLOK3, Edis, Hadise, Mert Demir | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgileri: `⚠️ VERIFICATION REQUIRED` (research-bank P4.2/P4.3'te bu isimler YOK) |
| **Yabancı tercihleri (eski dosyadan)** | The Weeknd, Bad Bunny, Dua Lipa, BTS, Alan Walker | `Kaynak: kurgusal (persona zevki)` + `⚠️ VERIFICATION REQUIRED` (research-bank P4'te yabancı sanatçı kapsamı YOK) |
| **Bankada VERIFIED Türkçe rap karşılığı** | Ezhel (Hip hop/rap/trap; 2017 "Müptezhel") — bu persona favorisi DEĞİL, arşiv doğrulama örneğidir | `Kaynak: [k1]` (tr.wikipedia Ezhel) — tek kaynaklı satır `SINGLE-SOURCE ⚠️` (P4.6/P4.7) |
| **BPM aralığı (tür)** | Pop **80-120** · Hip Hop **80-130** · Dance **110-135** · J-Pop **150-190** (P4.4); rap alt türleri: boom-bap **85-95** · trap **70-110** · modern rap **~140** (P4.5) | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`; P4.5 `VERIFIED`) |
| **BPM aralığı (Reggaeton / Latin Pop)** | P4.4'te bu tür YOK → `⚠️ VERIFICATION REQUIRED`, aralık yazılmadı | research-bank P4.4 (17 tür arasında reggaeton yok) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Küme BPM notu** | Sosyal kümesi: "100-140 BPM ⚠️" (mood-taxonomy §3.2.7 — kurgusal önerme, §1.2) | `⚠️ VERIFICATION REQUIRED` — test eşiği olarak KULLANILMADI |
| **Kişisel tempo tercihi** | 100-140 BPM (eski dosyadan — hareketli/trend odaklı) | Kaynak: kurgusal (persona verisi — tercih, ölçüm değil; test eşiği olarak KULLANILMAZ) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:30-08:15 (servis), 16:00-18:30 (okul sonrası), 20:00-22:30 (ev + ödev) · Hafta sonu 11:00-13:00, 15:00-18:00 (AVM/sahil), 21:00-23:00 (akış/keşif) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (trend keşif, playlist paylaşımı, sosyal feed) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Kısa video sesleri, reels/shorts akışları, arkadaş önerileri; duyduğu şarkıyı anında arar | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Orta/istekli (kurgusal) — reklamı sevmez, "anne premium alır mısın" diyecek kadar aileye bağlı; ödeme veli onayına bağlı (`veli_onayı_gerekli: true`) | Kaynak: kurgusal (persona verisi); yüzde taşınmadı — eski dosyadaki %50 premium §4.5 gereği yazılmaz |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Pop (Türkçe & Global) | Listede/akışta ne varsa onu dinler; "herkesin dinlediği" şarkı sosyal statüdür |
| 2 | Türkçe Rap / Pop-Rap | "Havalı" sound; okul grubunda ortak dil |
| 3 | Reggaeton / Latin Pop | Dans videolarının ritmi — hareketli içerik üretirken kullanır |
| 4 | EDM / Future Bass | Montaj/geçiş videolarının fon müziği; enerji yükseltir |
| 5 | Anime OST / J-Pop | Gizli tutku; kendi seçimi olduğu için değerlidir |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:15 | Okul servisi | Pop / trend liste |
| Hafta içi | 16:00-18:30 | Okul sonrası + arkadaşlarla | Türkçe rap (grup ortak dinleme) |
| Hafta içi | 20:00-22:30 | Ödev + kısa video akışı | EDM / pop karışık |
| Hafta sonu | 11:00-13:00 | Geç kahvaltı + akış | Yeni keşifler |
| Hafta sonu | 15:00-18:00 | AVM / sahil / basketbol | Dans edilebilir pop, reggaeton |
| Hafta sonu | 21:00-23:00 | Akış + playlist düzenleme | Anime OST / J-Pop (kendi seçkisi) |

*Not: BPM satırları yalnız tür düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Reggaeton türü P4.4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED`. [[mood-taxonomy]] §3.2.7 küme BPM notu (100-140) `⚠️` işaretli kurgusal önermedir → test eşiği yapılmadı. Eski dosyadaki tür yüzdeleri (%35 pop, %30 rap, %15 reggaeton, %10 EDM, %10 anime OST) bankada kaynak taşımadığı için YAZILMADI (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 13 (eski salt-okunur dosyada "annenin eskisi") | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı (eski dosyada yalnız model adı var) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Mobil tarayıcı (kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Mobil veri + ev Wi-Fi; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablosuz kulaklık (eski dosyada "kopya/uydurma" ürün — model/spec belirsiz) | `⚠️ VERIFICATION REQUIRED` (X-1) — spec yazılmadı |
| **Tema** | Canlı renkler, yuvarlak köşeler, modern/trend görünüm (kurgu — eski AI Rol Kartı tercihi) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 4-5 saat (kurgusal — eski dosyadan nitel taşındı; aile sınırlama denemesi kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — kısa video üretimi, filtre ve düzenleme araçlarına hâkim (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Diğer uygulamalar (eski dosyadan)** | Kısa video/uçak modu sosyal uygulamalar aktif; masaüstü/platform üretimi YOK | Kaynak: kurgusal (persona verisi); P3 kapsamı dışı detaylar yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi). Ekran süresi ve okuryazarlık skoru kurgusaldır (ölçüm yok).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Kısıt yok (kurgu); günlük uzun mobil kullanım | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — canlı renkli temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 `VERIFIED`) |
| **İşitme** | Kısıt yok; kulaklıkla servis/odada dinleme | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | Kısıt yok; tek elle hızlı mobil kullanım | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme zorunlu olmamalı (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 `VERIFIED`) |
| **Kognitif / dikkat** | Hızlı akış, FOMO, kısa dikkat; veli onay adımı ile karşılaşır | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — onay akışında kısa, net adım bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 `VERIFIED`) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (13 yaş + ekran süresi/sosyal medya bağlamı) 6698 m.5 kapsamında **özel önem** gerektiren çocuk verisi gibi davranır → persona'da **kurgusal olması zorunlu** (research-bank P6.5; 6698 m.5/1 `Kaynak: [k1] + [k2]`) |
| Renk kombinasyonu | Canlı/trend palet kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Paylaşım gizliliği | Paylaşım akışında gizli hesap/izin durumları ADR-023 satır 20 kapsamındadır; KVKK/yaş onayı §3.5.1 notunda |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Sosyal onayla yaşar: paylaştığı, beğendiği ve trend olan şey onun kimliğidir; "şu an dinliyorum" paylaşımı statü aracıdır.
- Trend avcısıdır: duyduğu her şeyi hemen dener ve arkadaşlarıyla paylaşır; uygulama "yeni" ve güncel görünmek zorundadır — eski görünen her şey kötüdür.
- Mobil-önceliklidir: tek elle, hızlı, büyük dokunuşlu arayüz bekler; masaüstü davranışı yoktur.
- Aileye bağlı ödeme: "anne premium alır mısın" — satın alma tamamen veli onayına bağlı (`veli_onayı_gerekli: true`).
- Reklam toleransı düşüktür; akışı ve paylaşımı kesen reklam sinirlendirir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 13 yaş → premium/abonelik tamamen veli onayına bağlı (`veli_onayı_gerekli: true`); ödeme aile üzerinden | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |
| **Sabır eşiği** | ~2 sn; akış ve butonlar anında yanıt vermelidir | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Hata görürse "bu uygulama eski" diye reddeder; kibar ve kısa hata metni + Tekrar Dene bekler | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Düşük — akış/paylaşım kesintisi premium tetikleyicisi | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel/hareketli: kısa demo ve ikonlarla öğrenir, metin okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Arkadaşlarıyla playlist paylaşır, ortak listede yarışır; paylaşım = sosyal statü | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.7 (Sosyal) |
| **Yorgunluk etkisi** | Geç saatte tek dokunuşla devam; karmaşık menü kaybolur | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Hızlı akış + çalışan paylaşım + trend güncelliği → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.7 |

*Erişilebilirlik etkisi (özet):* hızlı mobil kullanımda dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3) / UI ≥ **3:1** (1.4.11), odak görünür (2.4.7 / 2.4.11) ve veli onay akışında erişilebilir kimlik doğrulama (3.3.8) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 `VERIFIED`).

### AI Rol Kartı

Sen Toprak Erdem'sin. 13 yaşındasın, Mersin/Yenişehir — Pozcu'da yaşıyorsun, Özel Mersin Ortaokulu 7. sınıf öğrencisisin.
Sosyal kişiliğin var: paylaşırsın, takip edersin, akışta dolaşırsın; müzik senin için sosyalleşme aracıdır — sınıfın popüler çocuğusun.
Müzik zevkin: Pop (Türkçe & Global), Türkçe rap/pop-rap, reggaeton, EDM, anime OST/J-Pop. Favorilerin: Semicenk, BLOK3, Edis, Hadise, Mert Demir.
Telefonun var (model spec'i doğrulanmadı), kablosuz kulaklıkla dinlersin; günde 4-5 saat ekrandasın ve kısa video üretirsin.
Görme/işitme/hareket kısıtın yok; tek elle hızlı kullanım ve büyük dokunuş hedefleri beklersin. Canlı renkli, modern/yuvarlak köşeli arayüz istersin.
13 yaşındasın — ödeme/abonelik için veli onayı zorunludur; "anne premium alır mısın" senin cümlendir.
Test edilecek akışlar: ana sayfa/trend akış, paylaşım akışı (paylaş → yayılım), işbirlikli playlist, sosyal feed, trend keşif, arama (Türkçe karakter), dokunma hedefi/kontrast, veli onay akışı, hata kurtarma.
Davranış notları:
- Paylaş butonu görünür ve tek dokunuşta çalışmalı; paylaşım → yayılım akışı kesintisiz olmalı.
- Trend liste güncel olmalı; "eski" görünen arayüz güven kaybettirir.
- Kısa video sesi/akışı entegrasyonu kritiktir — duyduğun şarkıyı anında bulabilmelisin.
- Türkçe karakterli sanatçı/şarkı adlarında bozulma (mojibake) görürsen uygulamaya güvenin düşer.
- Reklam akışı ve paylaşımı kesmemeli; veli onay adımı kısa ve "bebekçe" olmayan bir dille olmalı.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa ve enerjik ton — 13 yaşındaki kullanıcı uzun açıklama kabul etmez |
| KVKK yasak | Prompt'ta gerçek çocuk verisi yok — yaş, şehir, okul, aile hepsi kurgu (P6.5); veli onayı frontmatter'da işaretli |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (trend odaklı açılış) | LCP ≤ **2500 ms**, CLS ≤ **0.1**, içerik anında görünür | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Sosyal feed / trend akış kaydırma | Akış akıcı, INP ≤ **200 ms**; yeni öğeler CLS ≤ **0.1** ile yerleşir | Performance trace + screenshot |
| 3 | Paylaşım akışı (paylaş → yayılım) | Paylaş tamamlanır; 280 karakter sınırı uygulanır; gizli hesap uyarısı görünür | Level 2 + 3 (ADR-023 satır 20) |
| 4 | İşbirlikli / ortak playlist | Davet + ortak düzenleme akışı çalışır; yetki reddi düzgün iletilir | Level 3 E2E |
| 5 | Trend keşif sayfası (Pop / Rap / EDM / J-Pop) | Sonuçlar tür BPM aralığında (P4.4 Pop 80-120 · Hip Hop 80-130 · Dance 110-135 · J-Pop 150-190); şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 6 | Renk paleti kontrast denetimi | Metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) — canlı temada da | Lighthouse / axe (P5.4 `VERIFIED`) |
| 7 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 8 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; dokunma hedefi ≥ **24×24 px** (2.5.8) | Trace + console log + a11y audit |
| 9 | Favori / playlist kaydetme + paylaşmaya hazır işaretleme | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); kibar ton; teknik yığın izi görünmez | Manuel + screenshot |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok | Lighthouse / axe |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 15 | Odak sırası + veli onay akışı | Odak görünür (2.4.7); onay adımı kısa, erişilebilir kimlik doğrulama (3.3.8) ihlali yok | Klavye navigasyon testi + a11y audit |
| 16 | Uzun mobil oturum dayanıklılığı | 3+ saat kullanım temsilinde performans/bellek düşüşü yok; akış kayması yok | Manuel + performance trace |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul/sınıf, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (13) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (25 Temmuz 2013) | Türetilmiş (eski 2014 → age 12 ile çelişiyordu) | eski salt-okunur dosya (25 Temmuz 2014) | [[personas/index]] §6.3 (age 13) | `⚠️ DERIVED` (yaş 13 ile tutarlılık) |
| 4 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (mood-taxonomy §3.2.7 "O=Dışadönüklük" ↔ P7.3 O=Openness) | Vault içi çelişki (kod tutarsızlığı) | mood-taxonomy §3.2.7 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Sosyal) | Vault referanslı atama | [[mood-taxonomy]] §3.2.7 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı ("Paylaşan, takip eden, akışta dolaşan…") | Vault verisi | [[mood-taxonomy]] §3.2.7 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood UI/test etkisi alıntısı ("Paylaşım akışı, işbirlikli playlist, sosyal feed") | Vault verisi | [[mood-taxonomy]] §3.2.7 | [[ADR-023-persona-driven-testing]] (satır 20) | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | İkincil mood: bu turda yok (eski dosyada "Enerjik") | Vault ataması | [[personas/index]] §6.3 (tekil küme) | eski salt-okunur dosya | `⚠️ VERIFICATION REQUIRED` — çelişki §7.2 |
| 16 | Sosyal küme BPM notu (100-140 ⚠️) | Kurgusal önerme (taksonomi §1.2) | mood-taxonomy §3.2.7 | — | `⚠️ VERIFICATION REQUIRED` — test eşiği yapılmadı |
| 17 | BPM tür aralıkları (Pop 80-120 · Hip Hop 80-130 · Dance 110-135 · J-Pop 150-190) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Rap BPM (boom-bap 85-95 · trap 70-110 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 19 | Reggaeton / Latin Pop tür BPM aralığı | Doğrulanmadı (P4.4 kapsam dışı) | research-bank P4.4 (bu tür YOK) | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 20 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 21 | Kişisel BPM tercihi (100-140) | Persona tercihi (kurgusal) | — | — | `Kaynak: kurgusal (persona verisi — tercih, ölçüm değil)` |
| 22 | Favori sanatçılar (Semicenk, BLOK3, Edis, Hadise, Mert Demir + yabancılar: The Weeknd, Bad Bunny, Dua Lipa, BTS, Alan Walker) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 dışı | research-bank P4 (bu isimler YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 23 | Ezhel — Türkçe rap arşiv doğrulama örneği (Hip hop/rap/trap, 2017 "Müptezhel") | Gerçek-dünya (tek kaynak) | tr.wikipedia Ezhel | 2. bağımsız kaynak YOK (P4.6) | `VERIFIED (sanatçı/tür)` + Spotify sıralaması iddiası `⚠️ VERIFICATION REQUIRED` (SINGLE-SOURCE) |
| 24 | Cihaz: iPhone 13 (annenin eskisi) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 26 | Kulaklık (kablosuz/kopya ürün) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 27 | Tarayıcı sürümü, iOS sürümü, internet hızı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` (iOS sürümü: `⚠️ VERIFICATION REQUIRED`) |
| 28 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 29 | Persona renk kombinasyonu kontrastı (canlı/trend palet) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 30 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 31 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn + Slow 4G 150 ms/1.6 Mbps/750 Kbps | Gerçek-dünya | web.dev/articles/vitals | github.com/GoogleChrome/lighthouse (docs/throttling.md) + developer.chrome.com | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Eski dosyadaki yüzdeler (%35 pop…, %50 premium, 600 takipçi) + "Spotify TR 2025 Top 50" iddiası | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 33 | `persona_id` biçimi (`CM-TE-13-SOS-MER` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`toprak-erdem-sosyal-trend.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 13 viewport = 390×844` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 128 BPM` | Tür aralığı (P4.4/P4.5, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `%35 pop / %50 premium` (eski dosya yüzdeleri) | Yüzsüz istatistik yazılmaz → yalnız nitel ("yüksek", "düşük") kurgusal ifade |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.7 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Toprak için INP 300 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yüzsüz yüzde istatistiği | "%50 premium" gibi | Yüzdeler bankada yok → sil, nitel ifade yaz |

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
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ) — bu persona 13 → `true`
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); reggaeton aralığı yazılmadı (bankada YOK); şarkı BPM'i yok; küme BPM 100-140 `⚠️` → test eşiği değil
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
| Kaynak & Doğrulama satırı | 33 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.7) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 12 | 13 | Çakışma VAR — [[personas/index]] §6.3 (age 13) esası; doğum tarihi 25 Temmuz 2014 → **25 Temmuz 2013** türetildi (`⚠️ DERIVED`) |
| Sınıf | 6. sınıf | 7. sınıf | 13 yaş ile tutarlılık düzeltmesi (eski 12 yaş / 6. sınıf eşlemesi korunsa yaş çelişirdi) — kurgusal uyum |
| İkincil mood | "Enerjik" | Yok (bu turda) | [[personas/index]] §6.3 tekil küme atar; eski ikincil tema §3.5.8 kişilik satırına taşındı (spor/sosyal enerji davranışı) |
| Küme ×2 çakışması | — | Sosyal kümesi: Berkay Arslan + Toprak Erdem (2 kişi) | [[mood-taxonomy]] §2.2 ataması; bu dosya **trend feed + paylaşım yayılımı** odağını, Berkay dosyası farklılaşmayı taşır (`berkay-arslan-sosyal.md` diskte VAR) |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-TE-13-SOS-MER` — şablon `CM-XX-YY-ZZZ-XXX` (XX=TE, YY=13, ZZZ=SOS, XXX=MER) | 2026-09-26 tarihinde şablon biçimine düzeltildi; index/görev tablosu ataması korunur (§3.5.1) |
| Birincil cihaz | iPhone 13 (annenin eskisi) + kablosuz "kopya" kulaklık (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM | "100–140 BPM" (kişisel/kaynaksız) + küme "100-140 ⚠️" | Tür aralıkları P4.4 (Pop 80-120 · Hip Hop 80-130 · Dance 110-135 · J-Pop 150-190) + P4.5 rap; reggaeton P4.4'te YOK → yazılmadı; şarkı BPM'i yok | §4.5 + X-2; küme BPM `⚠️VR` → test eşiği değil |
| Tür yüzdeleri / premium %50 | Kaynaksız yüzdeler (%35/%30/%15/%10/%10, %50) | yazılmadı (nitel ifade) | §4.5 kuralı 4 (bankada yok → yazma) |
| Dış dünya iddiaları | "Spotify TR 2025 Top 50'yi takip eder" (AI Rol Kartı) + 600 takipçi | yazılmadı | research-bank P1-P8 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| Big Five | 9/10 · 6/10 · 5/10 · 5/10 · 8/10 (/10 ölçek) | 0-100 ölçek (E 90 · A 60 · C 50 · N 50 · O 80) | Normalize `⚠️ DERIVED` (P7.4); "Duygusal Denge 5/10" → N ters kodlama ile 50 |
| Mood kodu | §3.2.7 "Dışadönüklük (O)" kullanımı | P7.3: O=Openness, A=Uyumluluk, C=Conscientiousness | P7.5 "kod esastır"; çelişki §3.5.3'te `⚠️ VERIFICATION REQUIRED` |
| Sanatçı kapsam | Semicenk, BLOK3, Edis, Hadise, Mert Demir + yabancılar | hepsi kurgusal zevk + `⚠️VERIFICATION REQUIRED`; bankada VERIFIED tek Türkçe rap karşılığı Ezhel (P4.2) not edildi | §4.5; favoriler kişisel zevk olarak etiketli kaldı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3 ile düzeltildi (12 → 13, doğum tarihi türetildi, sınıf 6 → 7), veli_onayı `true` + KVKK notu, persona_id 2026-09-26'da şablon formatına düzeltildi ve Sosyal ×2 küme çakışması (Berkay ↔ Toprak) kaydedildi; yüzsüz istatistikler yazılmadı | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/toprak-erdem-sosyal-trend
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
