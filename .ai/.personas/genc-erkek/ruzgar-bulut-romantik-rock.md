---
title: "CoreMusic — Persona: Rüzgar Bulut"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/ruzgar-bulut-romantik-rock"
updated: 2026-09-26
group: genc-erkek
age: 16
mood: Romantik
persona_id: CM-RB-16-ROM-DIY
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Rüzgar Bulut

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 16 yaşındaki, elektro gitar çalan, duygusal bağ kuran bir romantik/rock dinleyicinin gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Romantik §3.2.1), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Romantik ([[personas/index]] §6.3 ataması) |
| Test odağı | Duygusal UI (sıcak tonlar, yumuşak renk), slow geçiş animasyonları, rock/slow geçiş + gece modu, lirik görüntüleme, gece oturumu dayanıklılığı |
| Veli onayı | `veli_onayı_gerekli: false` (16 yaş — politika eşiği tamamlandı; gerekçe `⚠️ DERIVED`, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / duygusal açılış | Level 2 + 3 | Romantik beklentisi: "Yumuşak renkler, yavaş animasyonlar, sıcak tonlar" ([[mood-taxonomy]] §3.2.1) |
| Slow geçiş animasyonları | Level 2 | "Duygusal UI, renk paleti, slow geçiş animasyonları" ([[mood-taxonomy]] §3.2.1 Test etkisi) + ADR-023 satır 16 |
| Renk paleti kontrast denetimi | Level 2 | Metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) — P5.4 `VERIFIED` |
| Türkçe rock arşivi + şarkı sözü (lirik) | Level 1 + 2 | P4.3 Türkçe rock `VERIFIED` (Kargo…Hayko Cepkin); şarkı BPM'i yok (X-2) |
| Gece modu / uzun oturum dayanıklılığı | Level 2 + 3 | Romantik "Akşam, gece, yalnız" dinleme zamanı (§3.2.1); çalma kesintisiz |
| Türkçe karakter arama + hata kurtarma | Level 2 + 3 | Mojibake yasak; sonuç < 2 sn; Tekrar Dene akışı |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki aile/kişilik 100+ satır detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); eski dosyadaki yüzdeler (%30 Türkçe rock, %70 premium, 900 takipçi) kaynaksızdır → yazılmaz.

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
| **Ad Soyad** | Rüzgar Bulut | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 16 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 9 Mart 2010 | Kaynak: kurgusal (persona verisi; eski salt-okunur dosyadaki 9 Mart 2009 → age 17 idi; age 16 ile tutarlı olacak şekilde türetildi → `⚠️ DERIVED`) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Diyarbakır / Kayapınar — Diclekent | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Özel Diyarbakır Anadolu Lisesi; 11. sınıf (sayısal) — amatör müzisyen (elektro gitar) | Kaynak: kurgusal (persona verisi; 11. sınıf 16 yaş ile tutarlı) |
| **Ulaşım** | Okul servisi; prova/konsere gidişlerde aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `siyahgunesrock` (grup Instagram hesabı) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-RB-16-ROM-DIY` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` formatına uygundur — 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `RB` | `RB` | Ad soyad baş harfleri (Rüzgar Bulut) |
| `16` | `16` | Yaş (index §6.3) |
| `ROM` | `ROM` | Mood kodu (Romantik) |
| `DIY` | `DIY` | Şehir kodu (Diyarbakır) |

> **Format uyumu:** Atanan `CM-RB-16-ROM-DIY` şablon formatı (`CM-XX-YY-ZZZ-XXX`) ile birebir örtüşür → 2026-09-26 düzeltme (şablon §3.5.1; atama görev tablosu). Yaş/şehir parçası (`16` / `DIY`) bu seride bulunur.

> **Yaş / veli onayı notu (zorunlu — 16 yaş):** Bu persona 16 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). 16 yaş hem COPPA (13) hem GDPR (16) eşiğini karşıladığından ve CoreMusic'in 16 yaş altı onay politikasının dışında kaldığından onay **gerekmez** → bu sonuç `⚠️ DERIVED` (P6.4; politika yorumu). TMK m.11'e göre 18 yaş altı olması notu korunur; ödeme/abonelik işlemleri aile üzerinden yürütülür. Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki aile/profil 100+ satır ayrıntısı bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 184 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 76 kg, uzun/ateşli yapı; düzenli spor yapmaz, sahne enerjisiyle idare eder | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah, uzun, dağınık ("rock yıldızı saçı"); kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu); kulaklık §3.5.6 | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Rocker/grunge: siyah tişört (grup logolu), kot ceket, deri bileklik; profil fotoğrafında koyu zemin × açık ten (kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Uyku düzeni bozuk → gece/prova sonrası uzun dinleme; görsel kısıt yok ama düşük ışıkta okunurluk beklentisi | Kaynak: kurgusal (persona verisi); kontrast eşikleri: `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 50 | Sahnede dışa dönük ve karizmatik, günlük hayatta daha içe dönük — performans kişiliği ile gerçek kişiliği arasında fark var. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Genelde uyumludur ve grubuna karşı fedakârdır; ama sanatsal vizyon söz konusu olduğunda inatçıdır — "sound benim" der. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 60 | Gruba ve müziğine karşı sorumludur (provalara zamanında gelir); okulda/derste orta seviyedir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 50 = orta; yoğun duygu yaşar, şarkı sözlerine melankoli/tutku yayar — ama sahne stresi altında ayakta kalır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Yeni sound'lar, yeni gruplar, alt türler (progresif, blues rock) ve dünya müziği araştırır; rock'ın tüm alt türlerini dener. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.2.1 Romantik satırında "Yüksek **Uyumluluk (C)**, orta-düşük **Dışadönüklük (O)**, orta **Duygusal Denge (N)**" yazar — bu kullanım C=Uyumluluk, N=Duygusal Denge olarak dosyanın puanlarıyla (A 60 · N 50) örtüşür; ancak **O** harfi Dışadönüklük yerine kullanıldığı için P7.3 (O=Openness) ile çelişir → kod harfleri `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır"), çelişki §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Romantik |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar; eski dosyada "Sosyal" ikincili §7.2) |
| **Küme tanımı (alıntı)** | "Aşk/duygu odaklı dinleyici; şarkıya duygusal bağ kurar, az skip eder, tekrar dinler" ([[mood-taxonomy]] §3.2.1) |
| **Tetikleyici durumlar** | Gece/yalnız dinleme (§3.2.1 "Akşam, gece, yalnız"); sahne/prova öncesi hazırlık; sevdiği şarkıya duygusal bağ — tekrar tekrar dinleme; melankolik sözlerde duraklama |
| **UI etkisi** | "Yumuşak renkler, yavaş animasyonlar, sıcak tonlar" + Test etkisi "Duygusal UI, renk paleti, **slow geçiş animasyonları**; ADR-023 satır 16 (mood-geçiş) BPM eşikleri" ([[mood-taxonomy]] §3.2.1) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Romantik); ikincil bu turda yok — eski kaynakta mood farkı §7.2'de |
| Test etkisi | [[mood-taxonomy]] §3.2.1 Test etkisi "Duygusal UI, renk paleti, slow geçiş animasyonları" — küme satırındaki **BPM 60-100** değeri taksonomide `⚠️` ile işaretlidir (§1.2 kurgusal önerme) → `⚠️ VERIFICATION REQUIRED`, test eşiği olarak **kullanılmaz**; testte banka tür BPM aralıkları (P4.4) esas alınır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Yumuşak/sıcak arayüz beklentisi (Romantik) | Sıcak tonlu palet uygulanır; metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) | Erişilebilirlik audit (axe / Lighthouse) |
| 2 | Slow geçiş animasyonları | Geçişler akıcı ve tamamlanır; geçiş sırasında yerleşim sıçraması yok (CLS ≤ **0.1**) | Performance trace + screenshot |
| 3 | Az skip / tekrar dinleme | Aynı parça tekrar oynatıldığında durum bozulmaz; favori işareti kalıcı | Level 3 E2E + LocalStorage kontrolü |
| 4 | Duygusal UI + renk paleti (ADR-023 satır 16) | Mood-geçiş akışı çalışır; geçersiz mood değeri hata döner (ADR-023 satır 16) | Level 1 + 2 (ADR-023 senaryosu) |
| 5 | Türkçe rock arşivi + lirik | Sonuçlar gelir; P4.3 kapsamındaki sanatçılar listelenir; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 6 | Gece/uzun oturum (yalnız dinleme) | INP ≤ **200 ms** (P8.1 `VERIFIED`); karanlık modda okunurluk korunur | Performance trace + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe Rock / Anadolu Rock 2) Alternatif Rock / Grunge 3) Klasik Rock 4) Blues Rock 5) Progresif Rock | Kaynak: kurgusal (persona verisi) |
| **Ek tür notu** | Yerel / folk (Dengbej geleneği, doğu sound'ları) ilham kaynağı — 6. sıra (eski dosyadan nitel taşındı) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Duman, Teoman (research-bank P4.2 `VERIFIED`); Şebnem Ferah, Cem Karaca, Erkin Koray | Duman/Teoman: `Kaynak: [k1] + [k2]` (P4.2 `VERIFIED` — tr.wikipedia + prmedya); diğer 3: `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi `⚠️ VERIFICATION REQUIRED` (P4.2/P4.3 kapsamı dışı) |
| **BPM aralığı (tür)** | Türkçe rock / alternatif / klasik / blues / progresif rock için tür BPM aralığı P4.4'te **YOK** → `⚠️ VERIFICATION REQUIRED`, aralık yazılmadı | research-bank P4.4 (17 tür arasında rock yok) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Küme BPM notu** | Romantik kümesi: "60-100 BPM ⚠️" (mood-taxonomy §3.2.1 — kurgusal önerme, §1.2) | `⚠️ VERIFICATION REQUIRED` — test eşiği olarak KULLANILMADI |
| **Kişisel tempo tercihi** | 60-140 BPM (eski dosyadan — gece yavaş, sahne öncesi orta tempo) | Kaynak: kurgusal (persona verisi — tercih, ölçüm değil; test eşiği olarak KULLANILMAZ) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:00-18:00 (okul sonrası), 21:00-23:30 (ödev + yavaş tempo), 23:30-01:00 (gece/prova sonrası) · Hafta sonu 14:00-17:00 (grup provası), 21:00-00:30 (uzun dinleme/keşif) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Türkçe rock arşivi, şarkı sözü, playlist) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | YouTube önerileri, online müzik dergileri/forumlari, grubun bas gitaristi ve arkadaş tavsiyesi | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Yüksek (kurgusal) — "rock dinlerken reklam olmaz" beklentisi; ödeme aile üzerinden (16 yaş, TMK 18 notu §3.5.1) | Kaynak: kurgusal (persona verisi); yüzde taşınmadı — eski dosyadaki %70 premium §4.5 gereği yazılmaz |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe Rock / Anadolu Rock | Kimlik; Anadolu'nun sesini rock'a taşıyan çizgi — kendi şarkı sözlerinin kaynağı |
| 2 | Alternatif Rock / Grunge | İsyan ve duygu dengesi; grup provalarının ana repertuvarı |
| 3 | Klasik Rock | Gitar ilhamı ve "büyükler" saygısı; klasikleri bilmek statüdür |
| 4 | Blues Rock | Elektro gitar tekniği ve solo çalışı — sahne öncesi ısınma |
| 5 | Progresif Rock | Kompleks yapı sevgisi; uzun parçalarda söz + enstrüman dengesi |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 16:00-18:00 | Okul sonrası dinlenme | Türkçe rock (orta tempo) |
| Hafta içi | 21:00-23:30 | Ödev + gitar çalışması | Blues rock / klasik rock |
| Hafta içi | 23:30-01:00 | Gece solo dinleme | Yavaş, duygusal parçalar |
| Hafta sonu | 14:00-17:00 | Grup provası ("Siyah Güneş") | Prova listesi / repertuvar |
| Hafta sonu | 17:00-18:30 | Şarkı sözü yazımı | Sessiz/arka plan liste |
| Hafta sonu | 21:00-00:30 | Uzun keşif oturumu | Progresif + yerel/folk |

*Not: BPM satırları yalnız tür düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Rock türleri P4.4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED`. [[mood-taxonomy]] §3.2.1 küme BPM notu (60-100) `⚠️` işaretli kurgusal önermedir → test eşiği yapılmadı. Eski dosyadaki tür yüzdeleri (%30 Türkçe rock, %25 alternatif, %15 klasik, %10 blues, %10 progresif, %10 folk) bankada kaynak taşımadığı için YAZILMADI (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy S23 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Android/One UI sürümü doğrulanmadı (eski dosyada yalnız model adı var) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Mobil tarayıcı (kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Mobil veri + ev Wi-Fi; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablolu kulaklık (marka/model belirsiz) + amfiye bağlı aux (pratik/prova için) | `⚠️ VERIFICATION REQUIRED` (X-1) — spec yazılmadı |
| **Tema** | Sıcak tonlu açık tema (gündüz/okul) + gece oturumunda koyu tema (kurgu — §3.2.1 UI tercihi) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 3-4 saat (kurgusal — eski dosyadan nitel taşındı) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — ses kaydı/sosyal medya için yeterli; kayıt ekipmanını kullanır (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Diğer ekipman (eski dosyadan)** | Elektro gitar + amfi + kayıt ekipmanı (marka/spec bu turda DOĞRULANMADI) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi). Ekran süresi ve okuryazarlık skoru kurgusaldır (ölçüm yok).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Kısıt yok (kurgu); gece/az ışıkta uzun dinleme | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — koyu temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 `VERIFIED`) |
| **İşitme** | Kısıt yok; kablolu kulaklıkla oturum | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | Kısıt yok; elektro gitar + amfi kullanımı el becerisi ister | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme zorunlu olmamalı (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 `VERIFIED`) |
| **Kognitif / duygusal** | Duygusal bağ kurma, az skip, tekrar dinleme; gece yorgunluğu | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — abonelik/ödeme adımlarında kısa akış bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 `VERIFIED`) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (16 yaş + ekran süresi/gece kullanımı bağlamı) 6698 m.5 kapsamında dikkatle ele alınır → persona'da **kurgusal olması zorunlu** (research-bank P6.5; 6698 m.5/1 `Kaynak: [k1] + [k2]`) |
| Renk kombinasyonu | Sıcak tonlu palet kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Gece modu | Karanlık tema beklentisi kurgusaldır; zorunlu kriter yalnız eşiklerdir (P5.4) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Duygu odaklıdır: şarkıya bağlanır, az skip eder, aynı parçayı tekrar dinler; arayüzün "hissettirmesi" onun için teknik doğruluk kadar önemlidir.
- Müzik kimliğidir: grup ("Siyah Güneş") ve sahne onun dışavurumudur; şarkı sözü/lyrics ve gitar tab benzeri derinlik özelliklerine değer verir.
- Gece yaşamı: uyku düzeni bozuktur, gece dinler; gece modu/az ışık okunurluğu beklentisi yüksektir.
- Reklama tahammülü yoktur: atmosferi bozar → premium beklentisi nitel olarak yüksek (yüzde yazılmadı).
- Özgürlükçü ama bağlı: bağımsız keşif ister (YouTube/forumlari), ama önerinin "ilham veren" olmasını bekler.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 16 yaş → `veli_onayı_gerekli: false` (P6.4 eşiği tamamlandı, `⚠️ DERIVED`); ödeme yine aile üzerinden (TMK 18 notu §3.5.1) | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |
| **Sabır eşiği** | ~3 sn; gece oturumunda açılış ve etkileşim akıcı olmalıdır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Arama/öneri boş dönerse "arşiv zayıf" diye güven düşer; hata metni kibar ve kısa olmalı | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Düşük — rock/melankoli atmosferini kesen reklam premium tetikleyicisi | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + söz odaklı: klip/lyrics örnekleriyle öğrenir, uzun doküman okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Grup arkadaşlarıyla repertuvar/playlist paylaşır; sahne listesini birlikte kurarlar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.1 (Romantik) + eski ikincil "Sosyal" §7.2 |
| **Yorgunluk etkisi** | 00:00 sonrası sadece tek dokunuşla devam; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sıcak/sakin arayüz + kesintisiz çalma + derin rock arşivi → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.1 |

*Erişilebilirlik etkisi (özet):* gece/uzun oturumda kontrast ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11), dokunma hedefi ≥ **24×24 CSS px** (2.5.8), odak görünür (2.4.7 / 2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 `VERIFIED`).

### AI Rol Kartı

Sen Rüzgar Bulut'sın. 16 yaşındasın, Diyarbakır/Kayapınar — Diclekent'te yaşıyorsun, Özel Diyarbakır Anadolu Lisesi 11. sınıf (sayısal) öğrencisisin ve elektro gitar çalıyorsun.
Romantik kişiliğin var: şarkıya duygusal bağ kurar, az skip eder, sevdiğin parçayı tekrar tekrar dinlersin; grubun "Siyah Güneş" ile sahne alır, kendi sözlerini yazarsın.
Müzik zevkin: Türkçe rock/Anadolu rock, alternatif rock/grunge, klasik rock, blues rock, progresif rock. Bankada doğrulanmış favorilerin: Duman ve Teoman; ayrıca Şebnem Ferah, Cem Karaca, Erkin Koray.
Telefonun var (model spec'i doğrulanmadı); kablolu kulaklıkla dinler, elektro gitar + amfi + kayıt ekipmanın vardır.
Görme/işitme/hareket kısıtın yok; gece/az ışıkta uzun dinlediğin için okunurluk ve sıcak/sakin tasarım beklentin yüksektir. Şarkı sözü (lyrics) ve playlist senin için kritiktir.
16 yaşındasın — veli onayı gerekmez, ödeme/abonelik aile üzerinden yürür.
Test edilecek akışlar: ana sayfa (sıcak tonlar), slow geçiş animasyonları, renk paleti kontrastı, Türkçe rock arşivi + lirik, arama (Türkçe karakter), gece modu/uzun oturum, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Arayüz yumuşak ve sakin olmalı; sert/parlak animasyonlar ve agresif renkler seni rahatsız eder.
- Yavaş geçişler hissedilir ama gecikme değil "yavaşlık" olmalı — bekleme spinner'ı istemezsin.
- Türkçe rock ve niş alt türlerde (blues, progresif) arşiv derin olmalı; arama boş dönerse güven düşer.
- Şarkı sözlerinde Türkçe karakter bozulması (mojibake) görürsen uygulamaya güvenin düşer.
- Reklam, melankoli/atmosferi bozar — premium beklentin yüksektir.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, duygusal ve müzik odaklı ton — 16 yaşındaki müzisyen kısa, hisli ifade bekler |
| KVKK yasak | Prompt'ta gerçek genç verisi yok — yaş, şehir, okul, aile hepsi kurgu (P6.5); `veli_onayı_gerekli: false` frontmatter'da işaretli |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (sıcak tonlu açılış) | LCP ≤ **2500 ms**, CLS ≤ **0.1**, içerik anında görünür | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Slow geçiş animasyonları (Romantik test etkisi) | Geçişler tamamlanır, akıcıdır; geçişte CLS ≤ **0.1** | Performance trace + screenshot |
| 3 | Renk paleti kontrast denetimi | Metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) — açık ve koyu temada | Lighthouse / axe (P5.4 `VERIFIED`) |
| 4 | Türkçe rock arşivi (keşif) | Sonuçlar gelir; P4.3 kapsamındaki Türkçe rock sanatçıları listelenir; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 5 | Şarkı sözü (lirik) görüntüleme | Sözler tam görünür; Türkçe karakter bozukluğu (mojibake) yok | DOM assertion + `vault-utf8-writer verify` |
| 6 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing |
| 7 | Şarkı çalma (play / duraklat / tekrar) | İlk ses < 1 sn; tekrar oynatmada durum bozulmaz; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | Geçiş performansı (etkileşim) | INP ≤ **200 ms**; tıklama sonrası akıcı yanıt, bekleme spinner'ı yok | Performance trace (P8.1 `VERIFIED`) |
| 9 | Favori / playlist kaydetme | Görsel geri bildirim; durum localStorage'da kalıcı; tekrar dinlemede korunur | DOM assertion + LocalStorage kontrolü |
| 10 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); kibar hata tonu; teknik yığın izi görünmez | Manuel + screenshot |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok | Lighthouse / axe |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 15 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 16 | Gece uzun oturum dayanıklılığı | 2+ saat gece oturumunda performans/bellek düşüşü yok; koyu temada okunurluk korunur | Manuel + performance trace |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul/sınıf, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (16) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (9 Mart 2010) | Türetilmiş (eski 2009 → age 17 ile çelişiyordu) | eski salt-okunur dosya (9 Mart 2009) | [[personas/index]] §6.3 (age 16) | `⚠️ DERIVED` (yaş 16 ile tutarlılık) |
| 4 | `veli_onayı_gerekli: false` (16 yaş — politika eşiği tamam) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (mood-taxonomy §3.2.1 "O=Dışadönüklük" ↔ P7.3 O=Openness) | Vault içi çelişki (kod tutarsızlığı) | mood-taxonomy §3.2.1 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Romantik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.1 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı ("Aşk/duygu odaklı dinleyici…") | Vault verisi | [[mood-taxonomy]] §3.2.1 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood UI/test etkisi alıntısı ("Yumuşak renkler, yavaş animasyonlar, sıcak tonlar" · "slow geçiş animasyonları") | Vault verisi | [[mood-taxonomy]] §3.2.1 | [[ADR-023-persona-driven-testing]] (satır 16) | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | İkincil mood: bu turda yok (eski dosyada "Sosyal") | Vault ataması | [[personas/index]] §6.3 (tekil küme) | eski salt-okunur dosya | `⚠️ VERIFICATION REQUIRED` — çelişki §7.2 |
| 16 | Romantik küme BPM notu (60-100 ⚠️) | Kurgusal önerme (taksonomi §1.2) | mood-taxonomy §3.2.1 | — | `⚠️ VERIFICATION REQUIRED` — test eşiği yapılmadı |
| 17 | Türkçe rock tür BPM aralığı | Doğrulanmadı (P4.4 kapsam dışı) | research-bank P4.4 (rock türü YOK) | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 18 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 19 | Kişisel BPM tercihi (60-140) | Persona tercihi (kurgusal) | — | — | `Kaynak: kurgusal (persona verisi — tercih, ölçüm değil)` |
| 20 | Duman — tür/tarihçe (alternatif rock, grunge, Anadolu rock; Kaan Tangöze) | Gerçek-dünya | tr.wikipedia Duman | tr.wikipedia Türkçe rock | `Kaynak: [k1] + [k2]` (P4.2 `VERIFIED`) |
| 21 | Teoman — tür/tarihçe (alternatif rock, pop rock, soft rock; melankolik) | Gerçek-dünya | tr.wikipedia Teoman | prmedya | `Kaynak: [k1] + [k2]` (P4.2 `VERIFIED`) |
| 22 | Türkçe rock 2000'ler "altın çağı" (Kargo, Mavisakal, Mor ve Ötesi, Duman, Athena, maNga, Vega, Redd, Zakkum, Hayko Cepkin) | Gerçek-dünya | tr.wikipedia Türkçe rock | prmedya.com/rock-sanatcilari | `Kaynak: [k1] + [k2]` (P4.3 `VERIFIED`) |
| 23 | Şebnem Ferah, Cem Karaca, Erkin Koray (favori sanatçı) | Sanatçı realitesi P4.2/P4.3 dışı | research-bank P4 (bu isimler YOK) | — | `Kaynak: kurgusal (persona zevki)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 24 | Yabancı sanatçı/şarkı tercihleri (eski dosyada Nirvana, Pink Floyd vb.) | Bankada YOK | research-bank P4 (yabancı sanatçı kapsamı yok) | — | `⚠️ VERIFICATION REQUIRED` — favori listesine YAZILMADI |
| 25 | Cihaz: Samsung Galaxy S23 | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 26 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 27 | Kulaklık / gitar-amp ekipman spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 28 | Tarayıcı sürümü, OS/One UI sürümü, internet hızı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` (OS sürümü: `⚠️ VERIFICATION REQUIRED`) |
| 29 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 30 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn · Slow 4G 150 ms/1.6 Mbps/750 Kbps | Gerçek-dünya | web.dev/articles/vitals | github.com/GoogleChrome/lighthouse (docs/throttling.md) + developer.chrome.com | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Eski dosyadaki yüzdeler (%30 rock…, %70 premium, 900 takipçi) + "Spotify TR 2025 rock zayıf" iddiası | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 32 | `persona_id` biçimi (`CM-RB-16-ROM-DIY` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ruzgar-bulut-romantik-rock.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 yaş onay durumu frontmatter'da işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy S23 viewport = 1080×2340` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 96 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + rock türü bankada YOK → `⚠️ VERIFICATION REQUIRED` + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| `%30 rock / %70 premium` (eski dosya yüzdeleri) | Yüzsüz istatistik yazılmaz → yalnız nitel ("yüksek", "düşük") kurgusal ifade |

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
| Persona-özel eşik uydurma | "Rüzgar için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yüzsüz yüzde istatistiği | "%70 premium" gibi | Yüzdeler bankada yok → sil, nitel ifade yaz |

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
- [ ] Yaş onay durumu: 16 → `veli_onayı_gerekli: false` + gerekçe `⚠️ DERIVED` (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); rock türü P4.4'te yok → aralık yazılmadı; şarkı BPM'i yok; küme BPM 60-100 `⚠️` → test eşiği değil
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
| Kaynak & Doğrulama satırı | 32 |
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
| Yaş | 17 | 16 | Çakışma VAR — [[personas/index]] §6.3 (age 16) esası; doğum tarihi 9 Mart 2009 → **9 Mart 2010** türetildi (`⚠️ DERIVED`) |
| Sınıf | 11. sınıf (sayısal) | 11. sınıf (değişmedi) | 16 yaş ile tutarlı — çakışma YOK |
| İkincil mood | "Sosyal" | Yok (bu turda) | [[personas/index]] §6.3 tekil küme atar; eski ikincil tema §3.5.8 kişilik satırına taşındı (grup/prova paylaşım davranışı) |
| Küme ×2 çakışması | — | Romantik kümesi: Alparslan Demir + Rüzgar Bulut (2 kişi) | [[mood-taxonomy]] §2.2 ataması; bu dosya **rock/slow geçiş + gece modu + lirik** odağını, Alparslan dosyası farklılaşmayı taşır (`alparslan-demir-romantik.md` diskte VAR) |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-RB-16-ROM-DIY` — şablon `CM-XX-YY-ZZZ-XXX` (XX=RB, YY=16, ZZZ=ROM, XXX=DIY) | 2026-09-26 tarihinde şablon biçimine düzeltildi; index/görev tablosu ataması korunur (§3.5.1) |
| Birincil cihaz | Samsung Galaxy S23 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM | "60–140 BPM" (kişisel/kaynaksız) + küme "60-100 ⚠️" | Tür aralığı: rock P4.4'te YOK → yazılmadı; küme BPM `⚠️ VERIFICATION REQUIRED`; kişisel 60-140 yalnız kurgusal tercih | §4.5 + X-2; test eşiği P8'den |
| Tür yüzdeleri / premium %70 | Kaynaksız yüzdeler (%30/%25/%15/%10/%10/%10, %70) | yazılmadı (nitel ifade) | §4.5 kuralı 4 (bankada yok → yazma) |
| Takipçi / dış dünya iddiaları | "900 takipçi", "Spotify TR 2025: Rock zayıf" (AI Rol Kartı) | yazılmadı | research-bank P1-P8 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| Big Five | 5/10 · 6/10 · 6/10 · 5/10 · 9/10 (/10 ölçek) | 0-100 ölçek (E 50 · A 60 · C 60 · N 50 · O 90) | Normalize `⚠️ DERIVED` (P7.4); "Duygusal Denge 5/10" → N ters kodlama ile 50 |
| Mood kodu | §3.2.1 "Dışadönüklük (O)" kullanımı | P7.3: O=Openness, A=Uyumluluk, C=Conscientiousness | P7.5 "kod esastır"; çelişki §3.5.3'te `⚠️ VERIFICATION REQUIRED` |
| Sanatçı kapsamı | Duman, Teoman + Şebnem Ferah, Cem Karaca, Erkin Koray + yabancılar (Nirvana, Pink Floyd…) | Duman/Teoman `VERIFIED` (P4.2); 3 Türk sanatçı `⚠️VR`; yabancılar favori listesine YAZILMADI | §4.5; P4.3 bağlamı olarak Türkçe rock altın çağı listesi kullanıldı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3 ile düzeltildi (17 → 16, doğum tarihi türetildi); veli_onayı `false` + `⚠️ DERIVED` gerekçe, persona_id 2026-09-26'da şablon formatına düzeltildi ve Romantik ×2 küme çakışması (Alparslan ↔ Rüzgar) kaydedildi; yüzsüz istatistikler yazılmadı | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/ruzgar-bulut-romantik-rock
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
