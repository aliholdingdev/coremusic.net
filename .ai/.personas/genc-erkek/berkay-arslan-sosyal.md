---
title: "CoreMusic — Persona: Berkay Arslan"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/berkay-arslan-sosyal"
updated: 2026-09-26
group: genc-erkek
age: 13
mood: Sosyal
persona_id: CM-BA-13-SOS-IZM
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Berkay Arslan

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 13 yaşındaki bir genç sosyal kullanıcının gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Sosyal), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Sosyal ([[personas/index]] §6.3 ataması) |
| Test odağı | Paylaşım akışı, işbirlikli playlist, sosyal feed, trend keşfi, hızlı navigasyon + geri tuşu |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4); 13 yaş COPPA eşiğindedir |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Paylaşım akışı (şarkı/playlist → sosyal) | Level 1 + 2 | [[mood-taxonomy]] §3.2.7 test etkisi; ADR-023 satır 20 (paylaşım → yayılım / 280 karakter) |
| İşbirlikli playlist (ortak liste) | Level 2 + 3 | ADR-023 satır 19 (playlist CRUD + yarış durumu) |
| Sosyal feed / trend keşfi akışı | Level 2 | Mood test etkisi; BPM yalnız tür aralığı (P4.4) |
| Hızlı navigasyon + geri tuşu | Level 2 | [[mood-taxonomy]] §3.5 satır 12 (genç erkek 12-17) |
| Arama (Türkçe karakter) | Level 2 + 3 | Mojibake yasak; sonuç < 2 sn |
| Hızlı kontrol & dokunma hedefi | Level 2 | 2.5.8 ≥24×24 px (VERIFIED); 44/48 px iddiası → ⚠️ VERIFICATION REQUIRED |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Beklenen / Doğrulama yöntemi) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

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
| **Ad Soyad** | Berkay Arslan | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 13 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 19 Nisan 2013 | Kaynak: kurgusal (persona verisi; yaş 13 ile tutarlılık için türetildi — eski dosyada 19 Nisan 2010 vardı, §7.2) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İzmir / Bornova — Küçükpark | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Ortaokul 8. sınıf; okulun sosyal etkinlik ve müzik ekibinde | Kaynak: kurgusal (persona verisi; yaş 13 ile tutarlılık için güncellendi — §7.2) |
| **Ulaşım** | Okul servisi; arkadaş buluşmalarında aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `berkay_izmir` (sosyal medya rumuzu) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-BA-13-SOS-IZM` | Kaynak: kurgusal (persona verisi; atama [[personas/index]] §6.3) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` formatına uygundur — 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `BA` | `BA` | Ad soyad baş harfleri (Berkay Arslan) |
| `13` | `13` | Yaş (index §6.3) |
| `SOS` | `SOS` | Mood kodu (Sosyal) |
| `IZM` | `IZM` | Şehir kodu (İzmir) |

> **Format uyumu:** Atanan `CM-BA-13-SOS-IZM` şablon formatı (`CM-XX-YY-ZZZ-XXX`) ile birebir örtüşür → 2026-09-26 düzeltme (şablon §3.5.1; atama index §6.3). Yaş/şehir parçası (`13` / `IZM`) bu seride bulunur.

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 13 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). 13 yaş ayrıca COPPA eşiğinde olduğundan veli onayı **kesinleştirilir** (`⚠️ DERIVED`). Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, kilo takibi, tıbbi ölçüm…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 172 cm (13 yaş — kurgu; eski dosyada 180 cm, yaş 16 varsayımına dayanıyordu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 60 kg, atletik-yapı (fitness ile ilgilenir) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral kısa saç (fade kesim), yeşil göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; kablosuz kulaklık kullanır (model §3.5.6) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Trend marka giyim; profil fotoğrafında arkadaş grubu pozu (kontrast: açık zemin × koyu saç — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Kalabalık sosyal ortamda tek elle hızlı paylaşım → büyük hedef + az adım; hareketli ortamda ince hedef kaçırılır | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 92 | Okulun en bilinen yüzlerinden; her buluşmayı o başlatır, yeni insanlarla ilk o konuşur — ama ilgi görmediği gruptan hızla uzaklaşır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 78 | Her arkadaş grubuna uyum sağlar, çatışmadan kaçar; ama sosyal planlarında kendi fikrinden ödün vermez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 55 | Dersleri idare eder; sosyal planlarını ve playlist'lerini mükemmel organize eder, ödevleri son güne bırakır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yyüksek puan = düşük duygusal denge (ters kodlama).** 30 = duygusal olarak dengeli taraf; dikkat görmediğinde FOMO artar, paylaşım onayı gelmezse modu düşer. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 88 | Yeni mekan, yeni trend ve yeni türleri ilk o dener; keşif listelerinde çeşitlilik bekler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Ek çelişki: [[mood-taxonomy]] §3.2.7 "Yüksek Dışadönüklük (**O**), yüksek Uyumluluk (**C**)" yazar — Dışadönüklük = E, Uyumluluk = A (P7.3); kod harfi `⚠️ VERIFICATION REQUIRED`, P7.5 kuralı ("kod esastır") uygulanır ve çelişki §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Sosyal |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Paylaşan, takip eden, akışta dolaşan; müzik sosyalleşme aracıdır" ([[mood-taxonomy]] §3.2.7) |
| **Tetikleyici durumlar** | Arkadaş grubu buluşması (paylaşım beklentisi); trend şarkıyı ilk duyma (keşif); paylaşım sonrası beğeni/aciliyet hissi (FOMO); partide/okulda ortak liste açma |
| **UI etkisi** | "Paylaşım butonları, sosyal özellikler öne çıkar" + test etkisi "Paylaşım akışı, işbirlikli playlist, sosyal feed" ([[mood-taxonomy]] §3.2.7 "UI tercihi" / "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Sosyal); ikincil bu turda yok — eski kaynakta mood farkı §7.2'de |
| Test etkisi | [[mood-taxonomy]] §3.2.7 "Paylaşım akışı, işbirlikli playlist, sosyal feed; ADR-023 satır 20 (paylaşım → yayılım / gizli hesap / 280 karakter sınırı)" — kümenin "100-140 BPM" notu research-bank P4'te YOK → `⚠️ VERIFICATION REQUIRED`; geçerli aralıklar P4.4 tür BPM'leridir (Pop 80-120 · House 110-128 · Dance 110-135 · Electro 90-130) |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Paylaşım akışı (Sosyal) | Şarkı/playlist paylaş → yayılır; 280 karakter sınırı uygulanır (ADR-023 satır 20) | Level 2 + 3 senaryosu + DOM assertion |
| 2 | İşbirlikli playlist | Ortak liste CRUD çalışır; iki kullanıcı yazarsa yarış durumu tanımlı (ADR-023 satır 19) | Level 3 E2E + manuel |
| 3 | Sosyal feed akışı | Akış kaydırması akıcı; INP ≤ **200 ms**, CLS ≤ **0.1** | Performance trace (P8.1 VERIFIED) |
| 4 | Trend keşfi (ilk duyan olma) | Keşif/trend listesi hızlı yüklenir; sonuçlar tür BPM aralığı (P4.4) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 5 | Hızlı navigasyon + geri tuşu | Hızlı geçiş; geri tuşu kaybolmaz (2.4.11), odak görünür (2.4.7) | [[mood-taxonomy]] §3.5 satır 12 + a11y audit |
| 6 | Tek elle hızlı paylaşım | Tıklanabilir hedef ≥ **24×24 CSS px** (2.5.8 VERIFIED); 44/48 px iddiası yazılmadı | Erişilebilirlik audit (axe / Lighthouse) |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Pop (Türkçe & Global) 2) Deep House / Tropical House 3) Türkçe Rap (pop-rap) 4) Reggaeton / Latin 5) Modern R&B | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Semicenk, Edis, Dua Lipa, The Weeknd, Bad Bunny | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** · House **110-128** · Dance **110-135** · Electro **90-130** (P4.4) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **BPM (bankada olmayan tür)** | Reggaeton / R&B için tür BPM aralığı research-bank P4.4'te YOK → yazılmadı | `⚠️ VERIFICATION REQUIRED` (P4.4 kapsamı) |
| **Kişisel tempo tercihi** | 100-130 BPM (parti/akış temposu — persona tercihi, ölçüm değil; test eşiği olarak KULLANILMAZ) | Kaynak: kurgusal (persona verisi) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:00-08:00 (servis), 10:00-10:15 (teneffüs), 16:30-18:30 (buluşma), 20:00-23:00 (akış/paylaşım) · Hafta sonu 11:00-14:00 (buluşma), 21:00-23:30 (parti/akış) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (trend listeleri + paylaşım) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Önce sosyal medyada trendi görür, CoreMusic'te listeler; her keşfini arkadaşına gönderir | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %60 (aile planı — veli onayına bağlı, `veli_onayı_gerekli: true`) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Pop (Türkçe & Global) | Arkadaş grubunun ortak dili; her mekanda çalan, herkesin bildiği şarkılar |
| 2 | Deep House / Tropical House | Gün batımı buluşmaları ve yaz partileri; dansa kaldırır ama sohbeti bastırmaz |
| 3 | Türkçe Rap (pop-rap) | Trend olan her şey; sosyal medyada en çok paylaşılan tür |
| 4 | Reggaeton / Latin | Partide herkesi dans ettiren; akışta fark yaratır |
| 5 | Modern R&B | Sakin anlar ve araç yolculuğu; "cool" görünür |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-08:00 | Servis / yol | Pop (tanıdık liste) |
| Hafta içi | 10:00-10:15 | Teneffüs | Türkçe rap / trend (kısa) |
| Hafta içi | 16:30-18:30 | Arkadaş buluşması | Paylaşılan ortak liste (Sosyal) |
| Hafta içi | 20:00-21:30 | Ders + sosyal medya | Keşif / yeni trend |
| Hafta içi | 22:00-23:00 | Akış / paylaşım | House / R&B (chill) |
| Hafta sonu | 11:00-14:00 | Buluşma | Parti listesi (Dance) |
| Hafta sonu | 21:00-23:30 | Parti / grup akışı | Reggaeton + pop karışık |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.2.7'un 100-140 BPM küme notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları kullanılır. Eski dosyadaki tür yüzdeleri (%40 pop, %25 house …) bankada kaynak taşımadığı için YAZILMADI (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 15 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev Wi-Fi + mobil veri karma (servis/okulda zayıf kapsama — kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık / hoparlör** | Kablosuz kulaklık + taşınabilir hoparlör (arkadaş buluşması) — modeller spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | light (gündüz/sosyal ortam kullanımı — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 4-5 saat (telefon geneli — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — paylaşım kısayolları, trend takibi, playlist düzenleme aktif | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Aile ortak dizüstü/tablet (ödev + video) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — açık temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; kalabalık sosyal ortamda kulaklıkla/-hoparlörle dinleme | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; tek elle hızlı paylaşım/kontrol | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme yok (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | akışta hızlı tarama; uzun form/uzun açıklama dikkat dağıtır | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — veli onay akışında beklemeye tahammülü az | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (yaş 13 + paylaşım/sosyal bağlam) 6698 m.5 kapsamında özel nitelikli/kişisel veri işlenmesi gibi davranır → persona'da **kurgusal olması zorunlu** (research-bank P6.5; 6698 m.5/1 `Kaynak: [k1] + [k2]`) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| ≥44/48 px dokunma hedefi | [[mood-taxonomy]] §3.2.9 test etkisinde geçer; research-bank P5'te YOK → `⚠️ VERIFICATION REQUIRED`; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Sosyal kelebek: her buluşmayı o organize eder, keşfettiği şarkıyı hemen paylaşır ("Berkay'dan duydum" cümeli).
- FOMO duyar: telefonu sessiz kaldığında "bir şey kaçırıyorum" hissine kapılır; akış ve bildirim beklentisi yüksektir.
- Trend odaklıdır: listelerde ne varsa onu dener; yeni keşiflerini arkadaş grubuna ilk o yollar.
- Hız bekler: sosyal ortamda yükleme spinner'ına ~3 sn dayanır, paylaşımın anında "gittiğini" görmek ister.
- Yalnız kalamaz: tek başına geçen planlı olmayan bir akşam onun için boşa geçmiş gibidir; uygulama "dost canlısı" boş durum ister.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 13 yaş → satın alma/abonelik tamamen veli onayına bağlı (`veli_onayı_gerekli: true`); "aile planı" beklentisi | Kaynak: kurgusal (persona verisi); politika: ⚠️ DERIVED (P6.4) |
| **Sabır eşiği** | ~3 sn; paylaşım/akışta bekleme hissedilirse uygulamayı kapatır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Gruba "çalışmıyor" yazar, alternatif uygulamaya geçer; teknik hata metni görmezden gelinir — hızlı kurtarma şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 1/10 — paylaşım/akış ortasında kesinti kabul edilmez | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Video + deneme; ayarları keşfederek bulur, kılavuz okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Arkadaşlarıyla ortak playlist açar, şarkıyı paylaşır; beğeni/aciliyet beklentisi yüksektir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Sosyal kümesi |
| **Yorgunluk etkisi** | Gece 23:00 sonrası tek dokunuşlu "devam" akışı bekler; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Paylaşılan listenin gerçekten arkadaşına gitmesi + tanıdık trend akışı → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.7 |

*Erişilebilirlik etkisi (özet):* sosyal ortamda tek elle hızlı paylaşım → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), geri tuşu/paylaşım kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED); ≥44/48 px hedefi `⚠️ VERIFICATION REQUIRED`.

### AI Rol Kartı

Sen Berkay Arslan'sın. 13 yaşında, İzmir/Bornova — Küçükpark'ta yaşıyorsun, ortaokul 8. sınıfta okuyorsun ve okulun sosyal etkinlik ekibindesin.
Sosyal kişiliğin var: her buluşmayı sen organize edersin, keşfettiğin şarkıyı hemen arkadaşına gönderirsin; yalnız kalmaktan hoşlanmazsın (FOMO).
Müzik zevkin: pop (Türkçe & global), deep house/tropical house, pop-rap, reggaeton, modern R&B. En sevdiklerin: Semicenk, Edis, Dua Lipa, The Weeknd, Bad Bunny.
Kablosuz kulaklığın ve taşınabilir hoparlörün var; telefonun (model spec'i doğrulanmadı), açık temayı tercih edersin; gündüz/sosyal ortamda parlak ekranda kullanırsın.
Görme / işitme / hareket kısıtların yok; ama tek elle hızlı paylaşım yaparsın — büyük butonlar ve az adım beklersin.
Sabır eşiğin ~3 sn, paylaşım/akış ortasında reklam/kesinti kabul etmezsin; satın alma/abonelik tamamen veli onayına bağlıdır.
Test edilecek akışlar: ana sayfa yükleme, paylaşım akışı (280 karakter), işbirlikli playlist, sosyal feed, trend keşfi, arama (Türkçe karakter), hızlı navigasyon + geri tuşu, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Arkadaşının paylaştığı listeyi hemen açarsın; ortak playlist'te iki kişi yazarsa yarış durumu net olmalı.
- Trend listesinde ilk sen keşfetmek istersin; keşif sayfası kritiktir.
- Türkçe karakterli sanatçı/şarkı adlarında bozulma görürsen uygulamaya güvenin düşer.
- Paylaşımda "gitti/ gitmedi" geri bildirimi yoksa tekrar tekrar denersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, direkt ve hızlı ton — 13 yaşındaki sosyal kullanıcı uzun açıklama kabul etmez |
| KVKK yasak | Prompt'ta gerçek çocuk verisi yok — yaş, şehir, okul, çevre hepsi kurgu (P6.5); veli onayı frontmatter'da işaretli |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (hızlı başlatma) | LCP ≤ **2500 ms**, paylaşım/keşif blokları üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Paylaşım akışı (şarkı/playlist paylaş) | Paylaşım yayılır; 280 karakter sınırı uygulanır; geri bildirim görünür (ADR-023 satır 20) | Level 2 + 3 senaryosu + DOM assertion |
| 3 | İşbirlikli playlist (ortak liste) | Ortak liste CRUD çalışır; eşzamanlı yazımda yarış durumu tanımlı (ADR-023 satır 19) | Level 3 E2E + manuel |
| 4 | Sosyal feed kaydırma | INP ≤ **200 ms**; kaydırmada sıçrama yok (CLS ≤ **0.1**) | Performance trace + DOM assertion |
| 5 | Trend keşfi listesi | Liste tür BPM aralığı (P4.4) içinde sunulur; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 6 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 8 | Hızlı navigasyon + geri tuşu (Sosyal davranışı) | INP ≤ **200 ms**; geri tuşu kaybolmaz (2.4.11); anlık geri bildirim | [[mood-taxonomy]] §3.5 satır 12 + performance trace |
| 9 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 10 | Favori / playlist kaydetme | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok (44/48 px iddiası test edilmez — VRREQUIRED) | Lighthouse / axe |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 15 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 16 | Yoğun içerikli feed performansı | Uzun akışta LCP ≤ **2500 ms**, INP ≤ **200 ms**, CLS ≤ **0.1** korunur | Performance trace (P8.1 `VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul/sınıf, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (13) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (19 Nisan 2013) | Türetilmiş kurgu (yaş tutarlılığı) | [[personas/index]] §6.3 (age 13) | — | `Kaynak: kurgusal (persona verisi; yaş 13 ile tutarlılık için türetildi)` |
| 4 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (mood-taxonomy "Dışadönüklük (O), Uyumluluk (C)") | Vault içi çelişki | mood-taxonomy §3.2.7 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Sosyal) | Vault referanslı atama | [[mood-taxonomy]] §3.2.7 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.7 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood UI tercihi + test etkisi (paylaşım/işbirlikli playlist/sosyal feed) | Vault verisi | [[mood-taxonomy]] §3.2.7 | ADR-023 satır 19-20 (mood-taxonomy §3.5) | `Kaynak: [[mood-taxonomy]] + [[ADR-023-persona-driven-testing]] (vault verisi)` |
| 15 | Tür BPM aralıkları (Pop 80-120 · House 110-128 · Dance 110-135 · Electro 90-130) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Reggaeton / R&B tür BPM aralığı | Doğrulanmadı (P4.4 kapsam dışı) | research-bank P4.4 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 18 | Sosyal küme BPM notu (100-140) | Doğrulanmadı | mood-taxonomy §3.2.7 (⚠️ işaretli) | research-bank P4 (böyle aralık YOK) | `⚠️ VERIFICATION REQUIRED` — testte kullanılmadı |
| 19 | Favori sanatçı adları (Semicenk, Edis, Dua Lipa, The Weeknd, Bad Bunny) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Cihaz: iPhone 15 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | Diğer cihazlar (kulaklık, hoparlör, aile dizüstü/tablet) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 27 | Dokunma hedefi ≥44/48 px (mood-taxonomy §3.2.9 notu) | Doğrulanmadı | mood-taxonomy §3.2.9 (kendi notu "⚠️ WCAG research-bank") | research-bank P5 (44/48 YOK) | `⚠️ VERIFICATION REQUIRED` — asgari 2.5.8 (≥24×24 px) kullanıldı |
| 28 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Eski dosyadaki tür yüzdeleri (%40 pop, %25 house…) ve "Spotify TR 2025" iddiası | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 31 | `persona_id` formatı (`CM-BA-13-SOS-IZM` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | persona-template §3.5.1 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`berkay-arslan-sosyal.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 15 viewport = 390×844` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 128 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

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
| 3 | Eski dosya: salt-okunur kaynak dizin; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
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
| Persona-özel eşik uydurma | "Berkay için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok
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
| Kaynak & Doğrulama satırı | 31 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.7) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 16 | 13 | [[personas/index]] §6.3 kazanır |
| Doğum tarihi | 19 Nisan 2010 (yaş 16 varsayımı) | 19 Nisan 2013 (yaş 13 ile tutarlı) | Yaş atamasıyla tutarlılık — §3.5.1 kuralı (kurgu, türetildi) |
| Okul / sınıf | Özel İzmir Anadolu Lisesi, 10. sınıf | Ortaokul 8. sınıf | 13 yaş ile tutarlılık; kurgusal |
| persona_id formatı | (eski dosyada yok) | `CM-BA-13-SOS-IZM` — şablon `CM-XX-YY-ZZZ-XXX` ile birebir örtüşür | 2026-09-26 tarihinde şablon biçimine düzeltildi (§3.5.1 + §6.1 satır 31) |
| Birincil cihaz | iPhone 15 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM aralığı | 100-130 BPM (kişisel/kaynaksız) | Tür aralıkları P4.4 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| Tür yüzdeleri | %40 pop / %25 house / %15 rap / %10 / %10 | yazılmadı (sıralı tür listesi + gerekçe kaldı) | §4.5 kuralı 4 (bankada yok → yazma) |
| Dış dünya iddiaları | "Spotify TR 2025 listeleri" (AI Rol Kartı) | yazılmadı | research-bank P1-P8 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| Mood kodu | "Yüksek Dışadönüklük (O), Uyumluluk (C)" (mood-taxonomy §3.2.7) | Dışadönüklük = E, Uyumluluk = A (P7.3) | P7.5 "kod esastır"; çelişki §3.5.3'te |
| Kulaklık / hoparlör / aile cihazı | Model isimleri (spec'siz) | spec yazılmadı | research-bank P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |
| Fiziksel ölçüler | 180 cm / 73 kg (yaş 16) | 172 cm / 60 kg (yaş 13) | Yaş atamasıyla tutarlılık — kurgusal |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3'e göre düzeltildi (16→13), doğum tarihi/okul/fiziksel tutarlılığa göre türetildi; veli_onayı + KVKK notu, persona_id 2026-09-26'da şablon formatına düzeltildi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/berkay-arslan-sosyal
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
