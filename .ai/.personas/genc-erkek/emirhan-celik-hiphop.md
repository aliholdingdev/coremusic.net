---
title: "CoreMusic — Persona: Emirhan Çelik"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/emirhan-celik-hiphop"
updated: 2026-09-26
group: genc-erkek
age: 14
mood: Hip-Hop
persona_id: CM-EC-14-HIP-ADA
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Emirhan Çelik

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 14 yaşındaki bir Adanalı hip-hop gencinin gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Hip-Hop, §3.3 satır 15), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Hip-Hop ([[personas/index]] §6.3 ataması) |
| Test odağı | Söz senkronu (lyrics), hızlı skip, tür filtresi, Türkçe karakter arama, veli onayı akışı (16 yaş altı) |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Ana sayfa / hızlı başlatma | Level 2 + 3 | Hip-Hop UI beklentisi: hızlı skip, tek dokunuş kontrol ([[mood-taxonomy]] §3.3 satır 15) |
| Söz senkronu (lyrics) akışı | Level 1 + 2 | "Lirik ve ritim duyarlı" tanımı → söz satırı zamanlaması testi |
| Tür filtresi (rap / trap / drill) | Level 1 + 2 | Test etkisi "tür filtresi" — filtre geri bildirimi |
| Türkçe karakter arama (ç, ğ, ı, İ, ö, ş, ü) | Level 2 + 3 | UTF-8/mojibake denetimi; sanatçı adı araması |
| Veli onayı akışı (16 yaş altı) | Level 1 + 3 | `veli_onayı_gerekli: true` → ADR-023 veli/izin satırı |
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
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

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
| **Ad Soyad** | Emirhan Çelik | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 14 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 23 Temmuz 2012 | Kaynak: kurgusal (persona verisi; yaş 14 ile tutarlılık için türetildi) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Adana / Seyhan — Kurtuluş | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Anadolu Lisesi 9. sınıf (MEB okul türü: Anadolu Lisesi, 4 yıl) | Kaynak: kurgusal (persona verisi); okul türü: [k1]+[k2] (research-bank P2.7 VERIFIED) |
| **Ulaşım** | Yürüyüş + okul servisi; mahalle içinde ayağı yere değer | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `emirhan_beats` (beat denemeleri için) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-EC-14-HIP-ADA` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` biçimi notu:**

| Alan | Değer |
|------|-------|
| Bu turda verilen ID | `CM-EC-14-HIP-ADA` (görev tablosu / registry eşlemesi) |
| Şablon §3.5.1 biçimi | `CM-XX-YY-ZZZ-XXX` (ör. `CM-EC-14-HIP-ADA`) |
| Parça okuması | `CM` sabit · `EC` = Emirhan Çelik · `14` = yaş · `HIP` = Hip-Hop · `ADA` = Adana |
| Durum | Şablon formatı ile uyumlu (`CM-XX-YY-ZZZ-XXX`) — 2026-09-26 düzeltmesi; §7.2 |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 14 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, kilo takibi, tıbbi ölçüm…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 174 cm (ergenlik çağı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 63 kg, normal/atiletik | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah kıvırcık saç, kahverengi göz; fade kesim | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; açık havada kulaklıkla (tek kulak) dinler | Kaynak: kurgusal (persona verisi); kulaklık modeli §3.5.6 |
| **Giyim / temsil notu** | Streetwear: oversize tişört, kep, zincir kolye; profil fotoğrafı mahallede çekilmiş (kontrast: güneşli açık zemin × koyu saç — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Sokakta/güneşte tek elle hızlı kontrol → büyük hedef + az adım; söz senkronunda göz ekrana yakın | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 74 | Mahallede herkesi tanır, freestyle paylaşır, arkadaş grubunu yönlendirir; ama yeni ortamlarda ilk önce sessizce dinler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 54 | Arkadaşlarına karşı sadık, şakacı; ama "laf sokmayı" sever ve sınırı bazen aşar — orta düzey uyumluluk. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 47 | Okul notları ortalamanın altında; beat denemeleri için kendi kendine kurduğu haftalık plana uyar, ödevde gecikir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 53 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 53 = orta taraf; sert görünür ama eleştiri birkaç gün etkiler, sevdiği rap ile toparlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 71 | Yeni alt türleri (drill, trap, reggaeton) ve yeni prodüktörleri dener; "mahalleden kopmam" der ama keşif listesi hep açık kalır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Mood eğilimi eşlemesi | [[mood-taxonomy]] §3.3 satır 15 "Yüksek O, orta A" → O 71 (yüksek), A 54 (orta) ile uyumlu |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Hip-Hop |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Rap/trap odaklı, lirik ve ritim duyarlı" ([[mood-taxonomy]] §3.3 satır 15) |
| **Big Five eğilimi (alıntı)** | "Yüksek O, orta A" ([[mood-taxonomy]] §3.3 satır 15) — §3.5.3 puanlarıyla eşleştirildi |
| **Tetikleyici durumlar** | Mahallede/gecede dinleme başlangıcı (söz takibi); yavaş intros/uzun giriş (skip dürtüsü); yeni rap keşfi arayışı (tür filtresi) |
| **UI etkisi (test etkisi alıntısı)** | "Söz senkronu, hızlı skip, tür filtresi" ([[mood-taxonomy]] §3.3 satır 15 "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesi; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Hip-Hop); ikincil bu turda yok — eski kaynakta "İkincil Mood: Sosyal" farkı §7.2'de |
| BPM notu | §3.3 satır 15'teki "**80-140 BPM** ⚠️" notu birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; testte research-bank P4.4/P4.5 tür aralıkları kullanılır |
| Küme kuralı | Hip-Hop kümesi bu grupta ×2 (Emirhan, Doruk Ö.) → test odağı §7.2'de farklılaştırıldı |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Söz senkronu beklentisi (lirik duyarlı) | Aktif söz satırı zamanlamalı vurgulanır; kayma/sıçrama yok | DOM assertion + zamanlama kontrolü |
| 2 | Hızlı skip davranışı | INP ≤ **200 ms**; skip sonrası anlık geri bildirim, sıra güncellenir | Performance trace (P8.1 VERIFIED) |
| 3 | Tür filtresi (rap/trap/drill) | Filtre çalışır; sonuçlar tür aralığı (P4.4/P4.5) içinde sunulur; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 4 | Türkçe karakterle arama | Türkçe karakter kabulü (ç, ğ, ı, İ, ö, ş, ü); sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 5 | Veli onayı beklentisi (16 altı) | Satın alma/veli akışı engellenir ya da onay ister; açık hata metni yok | Level 3 E2E + manuel (ADR-023 veli satırı) |
| 6 | Ritmik/geçiş hassasiyeti | Parça geçişinde CLS ≤ **0.1**; oynat kontrolü kaybolmaz (2.4.11) | Trace + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkçe rap / trap 2) Drill (Türkçe + İngilizce) 3) ABD hip-hop (eski + yeni) 4) Reggaeton / latin trap 5) Arabesk rap | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | BLOK3, Ati242, Ezhel, Central Cee, Kendrick Lamar | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Hip Hop **80-130** (P4.4) · trap **70-110** · boom-bap **85-95** · modern rap **~140** (P4.5) | Kaynak: nevamuzik.com.tr + vocuno.com/tr/bpm-algilayici (P4.5) · turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Hızlı taraf: drill/trap geçişlerinde aceleci, uzun intro'da sıkılır | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:30-08:15 (yolda), teneffüs, 16:30-18:00 (mahalle), 22:30-00:30 (beat denemesi) · Hafta sonu 13:00-17:00 (sokak/sahil) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (Türkçe rap arşivi + hızlı skip) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | TikTok/Reels'ten düşen yeni drill/trap parçalarını arar; "buna benzer" önerisi beklentisi | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Düşük — ücretsiz sürümü reklamlı idare eder, veli onayına bağlı | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe rap / trap | Mahallenin sesi; sözler yakınından konuştuğu için lirik takibi kritik |
| 2 | Drill | Agresif enerji; sokakta/tempoda ritim tutar |
| 3 | ABD hip-hop | Lirik dersi alır, söz yazarlığına kaynak |
| 4 | Reggaeton / latin trap | Yaz sıcağında gece ritmi; yaz listeleri |
| 5 | Arabesk rap | Duygusal anlarda yeni nesil arabesk-rap karışımı |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:15 | Yolda (servis) | Türkçe rap (tek kulak) |
| Hafta içi | 10:00-10:15 | Teneffüs | Drill (kısa) |
| Hafta içi | 16:30-18:00 | Mahalle / saha | Trap + reggaeton |
| Hafta içi | 22:30-00:30 | Beat denemesi | Enstrümantal beat arşivi |
| Hafta sonu | 13:00-17:00 | Sokak / sahil | Karışık rap listesi |
| Hafta sonu | 21:00-22:00 | Playlist düzenleme | "Mahalle", "Gece Sürüşü", "Cypher" |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.3 satır 15'in 80-140 BPM küme notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Redmi Note 12 (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Android sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Chrome (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Dışarıda mobil veri, evde Wi-Fi karma; hız kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Tek kulaklıkla (yolda) + evde hoparlör — model/spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark (gece beat denemeleri — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 5-6 saat (telefon geneli — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — beat uygulaması, filtre/playlist kullanır; güç özelliklerini kısmen keşfeder | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Evde ortak dizüstü (video/ödev) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); güneşte dışarıda okuma zorluğu | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — açık havada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; yolda tek kulaklıkla dinler (çevre sesi duyar) | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; yürürken/sokakta tek elle hızlı kontrol | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme yok (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | sokakta dikkat dağılır; uzun form/yüklenme beklemez | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — veli onayı akışında kısa yol bekler | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi kurgusal olmak zorunda; 14 yaş = 16 altı → veli akışı test edilir (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Dokunma hedefi | Mood-taxonomy §3.3'te 44/48 px iddiası YOK; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Mahalle kültürüyle büyümüştür: dışa dönüktür, esprilidir, grubu o yönlendirir; ama yeni ortamlarda önce dinler.
- Beat yapmaya çalışır (telefon uygulaması) — "bir gün prodüktör olacağım" der; özgüveni yüksektir.
- Hız bekler: yavaş yüklemede sıkılır, uzun intro'da skip atar; sokakta tek elle kontrol yapar.
- Okul motivasyonu düşüktür; kardeşlerine/aileye karşı sorumludur.
- Türkçe karakter bozukluğu görürse platforma güveni düşer (mojibake = güven kırılması).

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 14 yaş → kendi kararı yok; premium akışı veli onayına bağlı (`veli_onayı_gerekli: true`) | Kaynak: kurgusal (persona verisi); politika: ⚠️ DERIVED (P6.4) |
| **Sabır eşiği** | ~3 sn; 8 sn'den fazla beklerse uygulamayı kapatır, kulaklıkta başka listeden devam eder | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sert tepki ("çöktü" der), arkadaşına yazar; teknik hata metnini okumaz — hızlı kurtarma şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Orta-düşük — sıçramayı atlar ama arka arkaya reklamda uygulamayı bırakır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Video + deneme; ayarları kurcalayarak bulur, kılavuz okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Mahalle grubuyla playlist paylaşır; ortak çalma sırası beklentisi | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece 00:00 sonrası tek dokunuş "devam et" akışı bekler | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Doğru Türkçe karakter + söz senkronu tutarlılığı → platforma bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Hip-Hop kümesi |

*Erişilebilirlik etkisi (özet):* sokakta tek elle hızlı kontrol → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), geri tuşu/skip kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Emirhan Çelik'sin. 14 yaşında, Adana/Seyhan-Kurtuluş'ta yaşıyorsun, Anadolu Lisesi 9. sınıfta okuyorsun.
Hip-Hop kişiliğin var: Türkçe rap/trap ve drill dinlersin, telefonunda beat denemeleri yaparsın, mahallede "rapçi Emirhan" olarak bilinirsin.
Müzik zevkin: Türkçe rap/trap, drill, ABD hip-hop, reggaeton, arabesk rap. En sevdiklerin: BLOK3, Ati242, Ezhel, Central Cee, Kendrick Lamar.
Telefonun (model spec'i doğrulanmadı), tek kulaklıkla yolda dinlersin; dark temayı tercih edersin; teknoloji seviyen 6/10'dur.
Görme / işitme / hareket kısıtların yok; ama sokakta yürürken tek elle hızlı kontrol yaparsın — büyük butonlar ve tek dokunuş beklersin.
Sabır eşiğin ~3 sn'dir; uzun yükleme ve arka arkaya reklam seni uygulamadan koparır; premium akışın veli onayına bağlıdır.
Test edilecek akışlar: ana sayfa hızlı başlatma, söz senkronu (lyrics), hızlı skip, tür filtresi (rap/trap/drill), Türkçe karakter arama, veli onayı akışı, ağ yavaşlatma, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Uzun intro ve yavaş yüklenmede hemen skip atarsın; INP ≤ 200 ms hissi beklersin.
- Söz senkronunda kayma olursa parçayı kapatırsın — lirik zamanlaması senin için kritiktir.
- Türkçe karakterli sanatçı/şarkı adlarında bozulma görürsen uygulamaya güvenin düşer.
- Keşifte "buna benzer" önerisi ve tür filtresi senin için vazgeçilmezdir.
- Yaşın 14 → satın alma/veli akışında onay beklersin; hata metni yerine kısa yol istersin.

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
| 2 | Söz senkronu (lyrics) açılışı | Aktif söz satırı zamanlamalı vurgulanır; kayma yok, mobilde görünür | DOM assertion + zamanlama kontrolü |
| 3 | Hızlı skip navigasyon (Hip-Hop davranışı) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; sıra güncellenir | Performance trace + DOM assertion |
| 4 | Tür filtresi (rap / trap / drill) | Filtre uygulanır; sonuçlar tür aralığı (P4.4/P4.5) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 5 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; oynat kontrolü görünür; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 6 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 7 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 8 | Veli onayı akışı (14 yaş — 16 altı) | Satın alma/veli akışı onay ister; yetkisiz adımda engel + nazik mesaj | Level 3 E2E + manuel (ADR-023 + P6.4 `⚠️ DERIVED`) |
| 9 | Favori / playlist paylaşımı | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | Bağlantı yavaşlatma (mobil veri) | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 11 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 12 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 13 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 14 | Reklam/kesinti toleransı akışı | Arka arkaya reklamda uygulama akışı bozulmaz; geçiş geri bildirimi var | Manuel + screenshot |
| 15 | Çevrimdışı / mobil veri davranışı | İndirme/gösterge doğru; offline liste erişilebilir | CDP `Network.emulateNetworkConditions` + manuel |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 17 | Veli rolü ≠ çocuk rolü (izin hiyerarşisi) | Çocuk rolü kısıtlı; veli rolü onay verebilir (ADR-023 satır 9) | Level 1 + Level 3 E2E |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (14) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
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
| 18 | Reggaeton BPM aralığı (bankada tür satırı yok) | Doğrulanmadı | research-bank P4.4 (reggaeton YOK) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 19 | Favori sanatçı adları (BLOK3, Ati242, Ezhel, Central Cee, Kendrick Lamar) | Persona tercihi (kurgusal); sanatçı realitesi P4 kapsamı dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Ezhel — tür bilgisi (hip hop, rap, reggae, trap; 2017 "Müptezhel") | Gerçek-dünya (P4.2) | tr.wikipedia Ezhel | research-bank P4.6 (ikinci kaynak paket notu) | `Kaynak: [k1]` (P4.2 `VERIFIED (sanatçı/tür)`) — yalnız tür bilgisi kullanıldı |
| 21 | "Ezhel Spotify TR en çok dinlenen" iddiası | Tek kaynak (EXCLUDED) | research-bank P4.6 / X-3 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 22 | Okul türü: Anadolu Lisesi (4 yıl) | Gerçek-dünya | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `Kaynak: [k1] + [k2]` (P2.7 `VERIFIED`) |
| 23 | Cihaz: Redmi Note 12 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 25 | Diğer cihaz/kulaklık spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 26 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 27 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 28 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 29 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 30 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | `persona_id` biçimi (`CM-EC-14-HIP-ADA` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |
| 33 | Eski dosyadaki yüzdeler/istatistikler (% tür dağılımı, % premium, Spotify TR trendleri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 34 | Aile/gelir/kan grubu/burç satırları | Kapsam dışı (bu tur şablonu) | persona-template §3.5 (alan yok) | — | Yazılmadı — §7.2 |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`emirhan-celik-hiphop.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Redmi Note 12 viewport = 412×915` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 128 BPM` | Tür aralığı (P4.4/P4.5, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 satır 15 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
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
| Persona-özel eşik uydurma | "Emirhan için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| Test adımı | 17 (asgari 10) |
| Kaynak & Doğrulama satırı | 34 |
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
| Yaş | 15 | 14 | [[personas/index]] §6.3 kazanır |
| Doğum tarihi | 23 Temmuz 2011 | 23 Temmuz 2012 | Yaş 14 ile tutarlılık (index) |
| Mood adı | Hip-Hop | Hip-Hop | Çakışma YOK — [[personas/index]] §6.3 ile tutarlı |
| Birincil cihaz | Xiaomi Redmi Note 12 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM aralığı | 90-160 BPM (kişisel/kaynaksız) | Tür aralıkları P4.4/P4.5 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| Hip-Hop kümesi ×2 (Emirhan, Doruk Ö.) | Tek dosya varsayımı | Aynı kümede 2 persona | Test odağı farklılaştırıldı: Emirhan = söz senkronu + hızlı skip + Türkçe karakter arama; Doruk = tür filtresi + playlist/oturum devamlılığı |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-EC-14-HIP-ADA` — şablon `CM-XX-YY-ZZZ-XXX` (XX=EC, YY=14, ZZZ=HIP, XXX=ADA) | 2026-09-26 tarihinde şablon biçimine düzeltildi; atama görev tablosu/kataloğu korunur |
| Eski AI rol kartı iddiaları ("Spotify TR 2025 trend", "premium APK ara") | Kaynaksız/UYGUNSUG iddialar | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma); X-3 `⚠️ VERIFICATION REQUIRED` |
| Yüzdeler/istatistikler (% tür, % premium) ve aile/gelir/kan grubu/burç | Eski dosyada mevcut | yazılmadı | Bu tur şablonu §3.5 kapsam dışı |
| İkincil mood (Sosyal) | "İkincil Mood — Sosyal" | İkincil atama yok | [[personas/index]] §6.3 tekil küme atar |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3'e göre düzeltildi (15→14); veli_onayı + KVKK notu; Hip-Hop kümesi ×2 test odağı farklılaştırıldı | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/emirhan-celik-hiphop
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode