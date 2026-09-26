---
title: "CoreMusic — Persona: Ege Koç"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/ege-koc-sporcu"
updated: 2026-09-26
group: genc-erkek
age: 15
mood: Sporcu
persona_id: CM-EK-15-SPR-ANT
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Ege Koç

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 15 yaşındaki bir genç sporcunun (açık hava / su sporları odaklı) gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Sporcu), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Sporcu ([[personas/index]] §6.3 ataması) |
| Test odağı | Açık hava koşu/antrenman listesi, outdoor kesintisiz dinleme, tempo bazlı öneri, büyük dokunma hedefi, güneş altında kontrast |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Açık hava koşu & antrenman listesi | Level 1 + 2 | [[mood-taxonomy]] §3.2.9 test etkisi; **outdoor ayrıştırması** (§7.2 — küme ×2) |
| Workout modu & tempo bazlı öneri | Level 1 + 2 | BPM filtre beklentisi (yalnız tür aralığı P4.4) |
| Crossfade / kesintisiz dinleme | Level 2 + 3 | Sahil/saha ortamında kesintisizlik; CLS ≤ 0.1 |
| Hızlı kontrol & dokunma hedefi | Level 2 | 2.5.8 ≥24×24 px (VERIFIED); 44/48 px iddiası → ⚠️ VERIFICATION REQUIRED |
| Zayıf kapsama / çevrimdışı davranış | Level 3 | Plaj/saha dışı ağ koşulu (P8.3 throttling) |
| Hızlı navigasyon + geri tuşu | Level 2 | [[mood-taxonomy]] §3.5 satır 12 (genç erkek 12-17) |

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
| **Ad Soyad** | Ege Koç | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 15 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 1 Haziran 2011 | Kaynak: kurgusal (persona verisi; yaş 15 ile tutarlılık için türetildi — eski dosyada 1 Haziran 2013 vardı, §7.2) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Antalya / Muratpaşa — Fener | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Anadolu Lisesi 10. sınıf; okul yüzme takımı kadrosu | Kaynak: kurgusal (persona verisi; yaş 15 ile tutarlılık için güncellendi — §7.2) |
| **Ulaşım** | Bisiklet + yürüyüş (sahile/antrenmana); aile aracı hafta sonu | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `ege_surf` (sosyal medya rumuzu) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-EK-15-SPR-ANT` | Kaynak: kurgusal (persona verisi; atama [[personas/index]] §6.3) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX` formatına uygundur — 2026-09-26 düzeltme):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `EK` | `EK` | Ad soyad baş harfleri (Ege Koç) |
| `15` | `15` | Yaş (index §6.3) |
| `SPR` | `SPR` | Mood kodu (Sporcu) |
| `ANT` | `ANT` | Şehir kodu (Antalya) |

> **Format uyumu:** Atanan `CM-EK-15-SPR-ANT` şablon formatı (`CM-XX-YY-ZZZ-XXX`) ile birebir örtüşür → 2026-09-26 düzeltme (şablon §3.5.1; atama index §6.3). Yaş/şehir parçası (`15` / `ANT`) bu seride bulunur.

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 15 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek genç verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, kilo takibi, tıbbi ölçüm…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 176 cm (15 yaş — kurgu; eski dosyada 162 cm, yaş 13 varsayımına dayanıyordu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 66 kg, uzun/atletik (yüzme+kıyı koşu yapısı) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Denizden açılmış sarı saç, mavi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; su geçirmez/kulaklık kullanır (model §3.5.6) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Şort + tişört, sörfçü stili; profil fotoğrafında sahil/deniz pozu (kontrast: parlak açık zemin × bronz ten — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Dış ortamda terli/el ıslak hızlı kontrol → büyük hedef + az adım; güneş altında ince/kontrastsız arayüz okunmaz | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 78 | Sahilde/takımda herkesi tanır, yabancı turistlerle rahat konuşur; ama antrenman öncesi içe kapanıp müziğine dalar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 72 | Takım ve arkadaş grubuyla uyumludur, düzeni birlikte kurar; antrenman programında taviz vermez. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 58 | Antrenman/saha saatlerine uyar, playlist'lerini aktiviteye göre ayırır; okul ödevlerinde ortalama gidişat. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 26 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 26 = sakin taraf; deniz/antrenman sonrası stresi hızlı düşer, kötü seansta ertesi güne taşımaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 86 | Yeni plaj, yeni rota, yeni tür dener; keşif listelerinde çeşitlilik ve açık hava enerjisi bekler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Ek çelişki: [[mood-taxonomy]] §3.2.9 "Yüksek Sorumluluk (**E**)" yazar — Sorumluluk = C (P7.3); kod harfi `⚠️ VERIFICATION REQUIRED`, P7.5 kuralı ("kod esastır") uygulanır ve çelişki §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Sporcu |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Antrenman odaklı; yüksek tempolu, motivasyon şarkısı ve workout playlist kullanır" ([[mood-taxonomy]] §3.2.9) |
| **Tetikleyici durumlar** | Sabah sahil koşusu saati (outdoor liste); su antrenmanı öncesi ısınma (yüksek tempo); gün batımı/serinleme (chill liste); kötü seans sonrası motivasyon arayışı (kesintisiz liste) |
| **UI etkisi** | "Büyük butonlar, hızlı erişim, ses kontrolü" + test etkisi "tempo bazlı öneri, workout modu, hızlı kontrol" ([[mood-taxonomy]] §3.2.9 "UI tercihi" / "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Sporcu); **küme Genç Erkek'te ×2 (Metehan + Ege)** → ayrıştırma §7.2'de; ikincil bu turda yok |
| Test etkisi | [[mood-taxonomy]] §3.2.9 "Tempo bazlı öneri, workout modu, hızlı kontrol (dokunma hedefi ≥44/48px — ⚠️ WCAG research-bank)" — 44/48 px research-bank P5'te YOK → `⚠️ VERIFICATION REQUIRED`; geçerli asgari eşik 2.5.8 ≥24×24 px (P5.4 VERIFIED); kümenin "130-180 BPM" notu da birincil ölçüm değil → `⚠️ VERIFICATION REQUIRED` |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Tempo bazlı öneri (Sporcu) | BPM/tür filtresi çalışır; sonuçlar tür aralığı (P4.4) içinde sunulur | DOM assertion + filtre testi |
| 2 | Workout modu beklentisi | Antrenman listesi tek dokunuşla; arka plan çalma kesintisiz | Manuel + LocalStorage kontrolü |
| 3 | **Açık hava koşu listesi (outdoor ayrıştırması)** | Koşu listesi sırayla/tek dokunuşla; dış ortamda kesintisiz çalma; Menü ≤2 adım | Level 2 + 3 senaryosu + trace |
| 4 | Hızlı kontrol (terli/ıslak el) | Tıklanabilir hedef ≥ **24×24 CSS px** (2.5.8 VERIFIED); 44/48 px iddiası yazılmadı | Erişilebilirlik audit (axe / Lighthouse) |
| 5 | Hızlı erişim / düşük gecikme | INP ≤ **200 ms**; skip sonrası anlık geri bildirim | Performance trace (P8.1 VERIFIED) |
| 6 | Ses kontrolü + güneş altında okuma | Büyük ses/skip kontrolleri; metin kontrastı ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11); geri tuşu kaybolmaz (2.4.11) | DOM assertion + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Surf Rock / Reggae 2) Tropical House / Deep House 3) Pop (yazlık) 4) Akdeniz / Latin 5) Elektronik / Chill | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Jack Johnson, Bob Marley, Kygo, Semicenk, Lost Frequencies | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | House **110-128** · Electro **90-130** · Dance **110-135** · Pop **80-120** (P4.4 — tropical/deep house, chill elektronik ve yaz pop için) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **BPM (bankada olmayan tür)** | Surf rock / reggae / Akdeniz-Latin için tür BPM aralığı research-bank P4.4'te YOK → yazılmadı | `⚠️ VERIFICATION REQUIRED` (P4.4 kapsamı) |
| **Kişisel tempo tercihi** | 110-130 BPM (açık hava koşu tempoyu taşır — persona tercihi, ölçüm değil; test eşiği olarak KULLANILMAZ) | Kaynak: kurgusal (persona verisi) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 06:30-07:15 (sahil koşusu), 17:00-19:00 (takım/su antrenmanı), 21:00-22:00 (serinleme) · Hafta sonu 09:00-11:00 (sörf/kıyı), 18:00-19:30 (gün batımı koşusu) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (açık hava antrenman listeleri + tür filtresi) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Yeni plaj/rota keşfinde yeni liste dener; antrenman öncesi tanıdık listeye döner | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %55 (dış ortamda kesintisiz/çevrimdışı beklentisi — veli onayına bağlı) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Surf Rock / Reggae | Sörf öncesi ve plaj ritmi; denizle büyümüş biri için doğal fon |
| 2 | Tropical House / Deep House | Gün batımı koşusu ve beach buluşmalarının temposu |
| 3 | Pop (yazlık) | Takım/yaz kamplarının ortak dili; enerjik ama zahmetsiz |
| 4 | Akdeniz / Latin | Antalya'nın çok kültürlü ortamı; sahilde dans ettirir |
| 5 | Elektronik / Chill | Antrenman sonrası serinleme ve dinlenme listesi |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 06:30-07:15 | Sahil koşusu (açık hava) | Tropical house / workout mix |
| Hafta içi | 17:00-19:00 | Takım / su antrenmanı | Dance / electro (kesintisiz) |
| Hafta içi | 21:00-22:00 | Serinleme / dinlenme | Chill elektronik (düşük ses) |
| Hafta sonu | 09:00-11:00 | Sörf / kıyı aktivitesi | Surf rock / reggae |
| Hafta sonu | 18:00-19:30 | Gün batımı koşusu | Deep house (tempo sabit) |
| Hafta sonu | 20:00-21:00 | Liste düzenleme | Playlist sıralama (tür filtresi) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.2.9'un 130-180 BPM küme notu da birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`; test hedefi olarak banka tür aralıkları kullanılır. Eski dosyadaki tür yüzdeleri (%30 surf rock, %25 house …) bankada kaynak taşımadığı için YAZILMADI (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 12 (eski salt-okunur dosyadan; not: "su geçirmez kılıf" kurgusal aksesuardır) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (mobil — kurgusal; P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Dış ortamda mobil veri değişken; sahilde zayıf kapsama (kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık / hoparlör** | Kulaklık + taşınabilir hoparlör (plaj/antrenman) — modeller spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | light (gündüz/dış ortam kullanımı — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 2-3 saat (dışarıda daha çok — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — liste ve filtreleri kullanır, güç özellikleri keşfetmez | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Aile ortak cihazı (TV/PC) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); **güneş altında okuma** (dış ortam) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — açık temada ve parlak ortamda geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; rüzgâr/dalga sesinde kulaklıkla dinleme | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; koşu anında terli/el ıslak hızlı-kaba kontrol | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme yok (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | hareket halinde bölünme toleransı düşük; uzun form dikkat dağıtır | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — veli onay akışında beklemeye tahammülü az | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi (antrenman/su sporları bağlamı + yaş 15) 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| ≥44/48 px dokunma hedefi | [[mood-taxonomy]] §3.2.9 test etkisinde geçer; research-bank P5'te YOK → `⚠️ VERIFICATION REQUIRED`; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Sabah planlıdır: kalkış, koşu ve antrenman saati bellidir; liste önceden hazırdır.
- Dış ortam insanıdır: saha/sahil koşusunda kesintisiz çalma ve büyük kontrol bekler, ince hedefler kaçar.
- Sakin ve pozitiftir: kötü seansta ertesi güne taşımaz, motivasyon listesiyle toparlanır.
- Keşifçidir: yeni rota/plaj = yeni liste; ama antrenman öncesi tanıdık listeye döner.
- Paylaşımcıdır: takım arkadaşlarıyla ortak antrenman listesi kullanır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 15 yaş → aile onaylı; premium/çevrimdışı talebi veli onayına bağlı (`veli_onayı_gerekli: true`) | Kaynak: kurgusal (persona verisi); politika: ⚠️ DERIVED (P6.4) |
| **Sabır eşiği** | ~4 sn; koşu öncesi 8 sn'den fazla bekleyip listeyi telefonda açar | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Dış ortamda hata görürse uygulamayı kapatır, telefonu müzik moduna alır; teknik hata metni görmezden gelinir — hızlı kurtarma şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 3/10 — antrenman ortasında kesinti kabul edilmez | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Video + deneme; ayarları keşfederek bulur, kılavuz okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Takım arkadaşlarıyla playlist paylaşır; ortak liste/çalma sırası bekler | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Sporcu kümesi |
| **Yorgunluk etkisi** | Gün batımı sonrası tek dokunuşlu "devam et" akışı bekler; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık açık hava antrenman listesi + kesintisiz çalma → uygulamaya bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Sporcu kümesi |

*Erişilebilirlik etkisi (özet):* dış ortamda kaba/hızlı kontrol → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3) / ≥ **3:1** (1.4.11), geri tuşu/skip kaybolmaz (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED); ≥44/48 px hedefi `⚠️ VERIFICATION REQUIRED`.

### AI Rol Kartı

Sen Ege Koç'sun. 15 yaşında, Antalya/Muratpaşa — Fener'de yaşıyorsun, Anadolu Lisesi 10. sınıf öğrencisisin ve okul yüzme takımı kadrosundasın.
Sporcu kişiliğin var: sabah sahil koşusu ve su antrenmanı saatleri bellidir, playlist'lerini aktiviteye göre ayırırsın.
Müzik zevkin: surf rock/reggae, tropical & deep house, yazlık pop, Akdeniz/Latin, chill elektronik. En sevdiklerin: Jack Johnson, Bob Marley, Kygo, Semicenk, Lost Frequencies.
Kulaklığın ve taşınabilir hoparlörün var; telefonun (model spec'i doğrulanmadı), açık temayı kullanırsın — güneş altında arayüzü okumak zorundadır.
Görme / işitme / hareket kısıtların yok; ama koşu anında terli veya ıslak elle hızlı kontrol yaparsın — büyük butonlar ve tek dokunuş beklersin.
Sabır eşiğin ~4 sn, antrenman ortasında reklam/kesinti kabul etmezsin; premium/çevrimdışı talebin veli onayına bağlıdır.
Test edilecek akışlar: ana sayfa hızlı başlatma, açık hava koşu listesi, workout modu, tempo bazlı öneri/BPM filtresi, crossfade kesintisiz dinleme, hızlı skip, arama (Türkçe karakter), zayıf kapsama davranışı, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Antrenman öncesi tanıdık listeye dönersin; yeni türü rota/plaj keşfinde denersin.
- Dış ortamda menü ≤2 adım ister, ince/kontrastsız metni okuyamazsın (güneş).
- Düşük tempolu/uzun intro şarkılarda hızlı atlarsın (skip INP ≤ 200 ms beklersin).
- Türkçe karakterli sanatçı/şarkı adlarında bozulma görürsen uygulamaya güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, direkt ve hızlı ton — genç sporcu persona'sı uzun açıklama kabul etmez |
| Antrenman yasak | Prompt'ta gerçek antrenman/sağlık verisi (kilo, nabız, sakatlık) yok — hepsi kurgu (P6.5) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (hızlı başlatma) | LCP ≤ **2500 ms**, antrenman bloğu üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Açık hava koşu listesi açılışı (outdoor ayrıştırması) | Tek dokunuşla koşu listesi; arka plan çalma kesintisiz; menü ≤2 adım | Level 2 + 3 senaryosu + trace |
| 3 | Workout modu açılışı | Tek dokunuşla antrenman listesi; arka plan çalma kesintisiz | Manuel + LocalStorage kontrolü |
| 4 | Tempo bazlı öneri / BPM filtresi | Filtre çalışır; sonuçlar tür aralığı (P4.4) içinde; şarkı-BPM iddiası yok | DOM assertion + filtre testi |
| 5 | Crossfade / kesintisiz geçiş | Geçişte sıçrama yok (CLS ≤ **0.1**); tempo değişimi ani değil (kurgusal beklenti) | Trace + console log |
| 6 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür (2.4.7) | Trace + console log + a11y audit |
| 7 | Hızlı skip navigasyon (Sporcu davranışı) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim; geri tuşu kaybolmaz (2.4.11) | Performance trace + DOM assertion |
| 8 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; çakışma yok (CLS ≤ **0.1**) | Trace + console log |
| 9 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 10 | Favori / playlist paylaşımı | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 11 | Bağlantı yavaşlatma / zayıf kapsama | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 ihlali yok (44/48 px iddiası test edilmez — VRREQUIRED) | Lighthouse / axe |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 15 | Güneş / parlak ortam okunabilirliği (dış mekan) | Metin kontrastı ≥ **4.5:1** (1.4.3) ve UI ≥ **3:1** (1.4.11) korunur | Kontrast audit (axe) + screenshot (parlaklık emülasyonu) |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul/sınıf, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (15) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Doğum tarihi (1 Haziran 2011) | Türetilmiş kurgu (yaş tutarlılığı) | [[personas/index]] §6.3 (age 15) | — | `Kaynak: kurgusal (persona verisi; yaş 15 ile tutarlılık için türetildi)` |
| 4 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (mood-taxonomy "Sorumluluk (E)") | Vault içi çelişki | mood-taxonomy §3.2.9 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Sporcu) | Vault referanslı atama | [[mood-taxonomy]] §3.2.9 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.9 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Sporcu kümesi Genç Erkek'te ×2 (Metehan + Ege) | Vault verisi + atama çakışması | mood-taxonomy §3.2.9 "Grup ataması: Genç Erkek ×2" | [[personas/index]] §6.3 | `Kaynak: [[mood-taxonomy]] + index (vault verisi)`; ayrıştırma §7.2 |
| 15 | Tür BPM aralıkları (House 110-128 · Electro 90-130 · Dance 110-135 · Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Surf rock / reggae / Latin tür BPM aralığı | Doğrulanmadı (P4.4 kapsam dışı) | research-bank P4.4 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 18 | Sporcu küme BPM notu (130-180) | Doğrulanmadı | mood-taxonomy §3.2.9 (⚠️ işaretli) | research-bank P4 (böyle aralık YOK) | `⚠️ VERIFICATION REQUIRED` — testte kullanılmadı |
| 19 | Favori sanatçı adları (Jack Johnson, Bob Marley, Kygo, Semicenk, Lost Frequencies) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Cihaz: iPhone 12 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | "Su geçirmez kılıf" + diğer cihaz spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı (kılıf: kurgusal aksesuar) |
| 23 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 27 | Dokunma hedefi ≥44/48 px (mood-taxonomy §3.2.9 notu) | Doğrulanmadı | mood-taxonomy §3.2.9 (kendi notu "⚠️ WCAG research-bank") | research-bank P5 (44/48 YOK) | `⚠️ VERIFICATION REQUIRED` — asgari 2.5.8 (≥24×24 px) kullanıldı |
| 28 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Eski dosyadaki tür yüzdeleri (%30 surf rock, %25 house…) ve "Spotify TR 2025 yaz listesi" iddiası | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 31 | `persona_id` formatı (`CM-EK-15-SPR-ANT` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | persona-template §3.5.1 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ege-koc-sporcu.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 12 viewport = 375×667` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
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
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.9 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Ege için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.9) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 13 | 15 | [[personas/index]] §6.3 kazanır |
| **Sporcu kümesi ×2 (Metehan + Ege)** | Eski dosyada tekil Sporcu persona'sı varsayımı | Aynı kümede 2 Genç Erkek persona'sı: Metehan Şahin + Ege Koç ([[mood-taxonomy]] §3.2.9 "Grup ataması: Genç Erkek ×2") | İkisi de korunur; **test odağı ayrıştırılır**: Metehan → salon/takım antrenmanı (workout modu, BPM sıralı liste), Ege → **açık hava/su sporları** (sahil koşu listesi, dış ortam kesintisiz dinleme, güneş kontrastı). Grup temsilcisi ortak: ADR-023 satır 12 (genç erkek — hızlı navigasyon + geri tuşu) |
| Doğum tarihi | 1 Haziran 2013 (yaş 13 varsayımı) | 1 Haziran 2011 (yaş 15 ile tutarlı) | Yaş atamasıyla tutarlılık — §3.5.1 kuralı (kurgu, türetildi) |
| Okul / sınıf | Devlet Ortaokulu, 7. sınıf | Anadolu Lisesi, 10. sınıf | 15 yaş ile tutarlılık; kurgusal |
| Fiziksel ölçüler | 162 cm / 50 kg (yaş 13) | 176 cm / 66 kg (yaş 15) | Yaş atamasıyla tutarlılık — kurgusal |
| persona_id formatı | (eski dosyada yok) | `CM-EK-15-SPR-ANT` — şablon `CM-XX-YY-ZZZ-XXX` ile birebir örtüşür | 2026-09-26 tarihinde şablon biçimine düzeltildi (§3.5.1 + §6.1 satır 31) |
| Birincil cihaz | iPhone 12 (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM aralığı | 80-125 BPM (kişisel/kaynaksız) | Tür aralıkları P4.4 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| Tür yüzdeleri | %30 surf rock / %25 house / %20 pop / %15 latin / %10 chill | yazılmadı (sıralı tür listesi + gerekçe kaldı) | §4.5 kuralı 4 (bankada yok → yazma) |
| Dış dünya iddiaları | "Spotify TR 2025 yaz listesi trendleri" (AI Rol Kartı) | yazılmadı | research-bank P1-P8 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| Premium | %40 (aile planı isteyecek) | %55 (kurgusal — açık hava kesintisiz/çevrimdışı beklentisi; veli onayına bağlı) | Kurgusal persona verisi; politika `⚠️ DERIVED` |
| Mood kodu | "Yüksek Sorumluluk (E)" (mood-taxonomy §3.2.9) | Sorumluluk = C (P7.3) | P7.5 "kod esastır"; çelişki §3.5.3'te |
| Kulaklık / hoparlör / aile cihazı | Model isimleri (spec'siz) | spec yazılmadı | research-bank P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3'e göre düzeltildi (13→15), doğum tarihi/okul/fiziksel tutarlılığa göre türetildi; **Sporcu kümesi ×2 çakışması** (Metehan + Ege) §7.2'de kaydedildi ve test odağı açık hava olarak ayrıştırıldı; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/ege-koc-sporcu
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
