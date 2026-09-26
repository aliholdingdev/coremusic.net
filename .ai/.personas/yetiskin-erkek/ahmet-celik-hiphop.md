---
title: "CoreMusic — Persona: Ahmet Çelik"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-erkek/ahmet-celik-hiphop"
updated: 2026-09-26
group: yetiskin-erkek
age: 29
mood: Hip-Hop
persona_id: CM-AC-29-HIP-IST
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Ahmet Çelik

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-erkek** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 29 yaşındaki bir Hip-Hop dinleyicisinin — lirik ve ritim odaklı, teknolojiye hâkim bir yazılım geliştiricinin — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Hip-Hop), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-erkek (25-45) |
| Birincil mood | Hip-Hop ([[personas/index]] §6.3 ataması — küme 15) |
| Test odağı | Söz senkronu, hızlı skip, tür/rap filtresi, offline indirme ([[mood-taxonomy]] §3.3 küme 15) |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Söz senkronu (lyrics sync) | Level 2 + 3 | Lirik odaklı dinleyici: sözler parça ile birebir senkron olmalı, kayma kabul edilmez |
| Hızlı skip (yüksek skip oranı) | Level 1 + 2 | 10 ardışık atlamada anlık geri bildirim; sırada kayıp olmamalı |
| Tür / rap filtresi + arama | Level 1 + 2 | Türkçe Rap ↔ Trap/Drill filtre geçişi; Türkçe karakterli arama (ç, ğ, ı, İ, ö, ş, ü) |
| Offline indirme (spor/metro) | Level 3 | Çevrimdışı indirme ve ağa dönüşte senkron (`use: { offline: true }` — P8.4) |
| Klavye kısayolu & geliştirici akışı | Level 2 | Space = oynat/duraklat, `/` = arama; odak görünürlüğü (2.4.7 / 2.4.11) |
| Hata ekranları & yavaş ağ | Level 2 + 3 | Kısa, suçlayıcı olmayan Türkçe metin (3.3.8); Slow 4G koşulunda hata yok |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel/aile detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.3 küme 15 | Küme adı + tanım alıntısı (Hip-Hop) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Ahmet Çelik | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 29 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 15 Eylül 1997 | Kaynak: kurgusal (persona verisi; yaş 29 ile tutarlılık için türetildi — §7.2) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İstanbul / Ataşehir | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Yazılım geliştirici (frontend odaklı, uzaktan çalışma) | Kaynak: kurgusal (persona verisi); üniversite bilgisi bankada doğrulanmadı → yazılmadı (§7.2) |
| **Ulaşım** | Metro + scooter (ehliyeti var, araba kullanmıyor — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `ahmetdev` | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AC-29-HIP-IST` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `AC` | Ad + soyad ilk harfleri (Ahmet Çelik → A, C) |
| `YY` | `29` | Yaş 2 hane (29 → `29`) |
| `ZZZ` | `HIP` | Mood tür kodu (Hip-Hop → HIP) |
| `XXX` | `IST` | Şehir kodu (İstanbul → IST) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → `Kaynak: [k1] + [k2]`, research-bank P6). Yaş çelişkisi (eski dosya 30 ↔ index 29) §7.2'de saklıdır.

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (kan grubu, burç, supplement, kilo takibi…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 183 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | Atletik yapı, düzenli spor yapan siluet (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah kısa saç, kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu); uzun ekran süresi için mavi ışık filtreli gözlük *düşünüyor* | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Sokak stili (oversized tişört, jogger); profil fotoğrafında koyu/renkli arka plan (kontrast etkisi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Günde 10+ saat ekran; hızlı klavye kullanımı → klavye kısayolu ve görünür odak beklentisi | Kaynak: kurgusal (persona verisi); kriter: `Kaynak: [k1] + [k2]` (P5.4) |

*Sağlık verisi notu:* Eski dosyadaki sağlık/supplement ayrıntıları 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 70 | Sosyaldir: takım içi kod incelemeleri, Discord sunucuları, spor arkadaşı çevreleriyle sürekli etkileşimdedir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | İşbirlikçidir ama kendi alanına/ürettiğine müdahale edilmesinden hoşlanmaz; gerektiğinde "hayır" demeyi bilir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 70 | Sprint ve teslimatlara sadıktır, planlı çalışır; gündelik rutinlerinde (ev/temizlik) daha gevşekdir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = sakin ve soğukkanlı; stresi spor ve müzikle yönetir, büyük dalgalanmalar yaşamaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 80 | Yeni teknoloji, yeni framework ve yeni türleri deneme açığı yüksektir; hype'a kapılmadan önce test eder. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Dönüşüm yöntemi | Eski salt-okunur dosyanın `/10` puanları 10 ile çarpıldı (7/10 → 70, 6/10 → 60, 8/10 → 80); "Duygusal Denge 7/10" (yüksek denge) → N ters kodlamada `100 − 70 = 30` → dönüşüm `⚠️ DERIVED` |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Hip-Hop |
| **İkincil küme** | Yok (eski dosyanın "Sporcu" ikinciliği mood-taxonomy §3 listesinde YOK → §7.2 çelişki kaydı) |
| **Küme tanımı (alıntı)** | "Rap/trap odaklı, lirik ve ritim duyarlı" ([[mood-taxonomy]] §3.3 küme 15) |
| **Tetikleyici durumlar** | Söz paneli kayması / senkron hatası (geri çekilme); tür filtresinin sonuç vermemesi; offline indirmenin başarısız olması (geri çekilme); hızlı atlamada hissedilir gecikme |
| **UI etkisi** | Test etkisi: "Söz senkronu, hızlı skip, tür filtresi" + küme Big Five eğilimi "Yüksek O, orta A", müzik "Rap, hip-hop, trap" ([[mood-taxonomy]] §3.3 küme 15) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Hip-Hop); ikincil eski dosyada "Sporcu" olup listede yok → §7.2'de çelişki olarak saklandı |
| Test etkisi | Söz senkronu, hızlı skip, tür filtresi — [[mood-taxonomy]] §3.3 küme 15 |
| Taksonomi BPM notu | §3.3 küme 15 "80-140 BPM ⚠️" işaretli **eski** taksonomi önermesidir → `⚠️ VERIFICATION REQUIRED`; test eşikleri olarak research-bank P4.5 rap/trap aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Lirik/ritim duyarlılık | Sözler parça ile senkron; zaman damgası kayması kabul edilmez | Level 3 E2E + trace (söz senkronu) |
| 2 | Hızlı skip | Her atlamada anlık geri bildirim; sırada kayıp yok; INP ≤ **200 ms** | DOM assertion + sayaç kontrolü |
| 3 | Tür filtresi (rap/trap) | Filtre değişince liste yenilenir; eski türden öğe kalmaz | DOM assertion + screenshot |
| 4 | Yüksek O (keşif) | Yeni tür/sanatçı önerileri görünür; filtre uygulanabilir | İçerik assertion |
| 5 | Offline ihtiyacı (spor/metro) | İndirme tamamlanır; çevrimdışı oynatma çalışır | Playwright `use: { offline: true }` |
| 6 | Geliştirici klavye alışkanlığı | Space = oynat/duraklat, `/` = arama; odak görünür (2.4.7) | Klavye navigasyon testi |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı** | 1) Türkçe Rap/Hip-Hop 2) Trap / Drill 3) Old School Hip-Hop (US) 4) Lo-fi / Chillhop 5) R&B / Neo-Soul | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Ceza, Ezhel, Sagopa Kajmer, Kendrick Lamar, Travis Scott | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | boom-bap **85-95** · trap **70-110** (davul ızgarası 130-170) · modern rap beat'i **~140** BPM | Kaynak: nevamuzik.com.tr + vocuno.com/tr/bpm-algilayici (research-bank P4.5 `VERIFIED`) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Antrenmanda yüksek enerji, kod yazarken sakin/odak tempo; sayısal tercih yazılmadı (kurgusal bağlam) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:00-08:30 (antrenman), 09:30-17:00 (kod — lo-fi), 22:30-23:30 (gece rap) · Hafta sonu 10:00-12:00 (antrenman) + serbest akşam | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (keşif; Türkçe rap arşivi için) + eski alışkanlık olarak başka platform (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Aktif kaşif: filtre + manuel arama birlikte; ilk önerilerde tutmazsa sorguyu değiştirir | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %60 (reklam toleransı düşük; arşiv kalitesine göre geçiş yapabilir — kurgusal) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkçe Rap/Hip-Hop | Liseden beri dinler; sözleri analiz eder, flow karşılaştırır — müzik bir yaşam tarzıdır |
| 2 | Trap / Drill | Antrenman enerjisi; agresif bass ve sert vuruşlar |
| 3 | Old School Hip-Hop (US) | 90'lar altın çağı; "gerçek rap" referansı |
| 4 | Lo-fi / Chillhop | Kod yazarken odak veren sözsüz beat'ler |
| 5 | R&B / Neo-Soul | Sakin/romantik akşam modunda köprü tür |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-08:30 | Antrenman | Trap / drill |
| Hafta içi | 09:30-12:30 | Derin çalışma (kod) | Lo-fi / chillhop |
| Hafta içi | 13:30-17:00 | Toplantı + kod | Lo-fi / hafif rap |
| Hafta içi | 17:30-18:30 | Oyun / boş zaman | Rap (arka plan) |
| Hafta içi | 22:30-23:30 | Gece oturumu | Melankolik Türkçe rap |
| Hafta sonu | 10:00-12:00 + akşam | Antrenman + serbest | Trap / karışık rap |

*Not: BPM satırları yalnızca tür/alt-tür aralığı düzeyindedir (P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.3 küme 15'in "80-140 BPM" notu eski taksonomidir → `⚠️ VERIFICATION REQUIRED`.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 15 Pro (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **Platform test cihazı** | Samsung Galaxy A34 5G — ana ekran çözünürlüğü **1080 × 2340** px | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") (P3.3 `VERIFIED`) |
| **OS** | iOS sürümü doğrulanmadı (test koşulu: banka Android emülasyonu) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari/Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev kablolu internet + mobil veri (marka/hız kurgusal; spor/metroda kesinti beklentisi) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablosuz kulaklık + stüdyo monitörü (model spec'siz) | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark (varsayılan — gece/odak kullanımı baskın) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 10+ saat (iş + kişisel — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 9/10 — frontend geliştirici; Vanilla JS/ITCSS, DevTools, performans metriklerine hâkim | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Dizüstü bilgisayar, harici monitör, oyun konsolu — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); günde 10+ saat ekranda | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); söz paneli/ipucu açılışında içerik 1.4.13 | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); antrenman salonu gürültülü | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; hızlı klavye + tek elle mobil kullanım | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükleme zorunlu adım olmamalı (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | teknik metni okur ama sabır eşiği düşük; akışta beklemez | Yardım tutarlılığı (3.2.6) · tekrar girdi zorunluluğu yok (3.3.7) · erişilebilir kimlik doğrulama (3.3.8) · odak 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi kurgusaldır; gerçek kişi verisi 6698 m.5/1 kapsamında yazılmaz (research-bank P6.5) |
| Renk kombinasyonu | Dark tema renk eşleşmesi hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Platformu "developer gözüyle" değerlendirir: önce performans ve kod kalitesi, sonra içerik.
- Lirik odaklıdır: sözlerin senkronunu ve anlamını önemser; kayan söz paneli güveni düşürür.
- Hızlı karar verir: ilk üç öneride aradığını bulamazsa filtre/sorgu değiştirir, beklemek istemez.
- Klavye kısayollarına alışkındır; fare/dokunma zorunlu adımlar onu yavaşlatır.
- Nostaljiye açıktır (90'lar US rap, 2000'ler Türkçe rap) ama hype'a kapılmaz — önce test eder.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Araştırmacı + fırsatçı: inceleme/öneri okur, arşiv ve fiyat netse geçiş yapar | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn; spinner'a ~5 sn dayanır, sonra developer moduna geçip ağ/tümleyici inceler | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Analitik: hata kodunu ve network isteğini kontrol eder; yığın izni görürse güven kaybı | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 2/10 — premium'a alışkındır; atlanamayan reklamda platformu bırakır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Kısa demo + klavye kısayolu; uzun metin okumaz, dokümantasyonu tarar | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Oynatma durumunu sohbette paylaşır; playlist önerilerini arkadaşına gönderir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece 23:00 sonrası karmaşık akış reddedilir; tek dokunuşlu devam beklenir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Hızlı yükleme + söz senkronu + offline çalışır → güvenir;klavye kısayolu varsa sadık kalır | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.3 küme 15 (lirik/ritim duyarlı) |

*Erişilebilirlik etkisi (özet):* hızlı skip + tür filtresi + offline → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme zorunlu olmamalı (2.5.7), odak görünür ve gizlenmemiş olmalı (2.4.7 / 2.4.11), hata metni erişilebilir (3.3.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ahmet Çelik'sin. 29 yaşındasın, İstanbul/Ataşehir'de yaşıyorsun, yazılım geliştiricisin (frontend odaklı, uzaktan çalışıyorsun).
Hip-Hop bir kişiliğin var: lirik ve ritim odaklısın, rap/trap filtreleriyle hızlı gezinirsin; ikincil mood ataması yok — yüksek keşif eğilimin (yüksek O) var.
Müzik zevkin: Türkçe Rap/Hip-Hop, Trap/Drill, Old School Hip-Hop, Lo-fi/Chillhop, R&B/Neo-Soul. En sevdiklerin: Ceza, Ezhel, Sagopa Kajmer, Kendrick Lamar, Travis Scott.
Telefonun (model spec'i doğrulanmadı); dizüstü bilgisayarın ve monitörün var ama spec'leri doğrulanmadı; teknoloji seviyen 9/10, DevTools'a hâkimsin.
Görme / işitme / hareket kısıtların yok; gündüz uzun ekran kullanıyorsun — net kontrast, klavye kısayolu ve görünür odak beklersin.
Sabır eşiğin 3 sn, reklam toleransın 2/10; offline indirme ve hızlı yükleme senin için kritiktir.
Test edilecek akışlar: söz senkronu, hızlı skip, tür/rap filtresi, Türkçe karakterli arama, offline indirme ve senkron, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Söz paneli kayarsa (senkron tutmazsa) parçayı kapatırsın; lirik önceliklidir.
- İlk üç öneride aradığını bulamazsan tür filtresini değiştirirsin, uzun beklemezsin.
- Offline indirme başarısızsa (metro/spor) uygulamayı bir daha denemezsin.
- Mojibake (Türkçe karakter bozulması) görürsen güvenin anında düşer.
- Klavye kısayolları (Space, `/`) yoksa fareyle gezmek seni yavaşlatır ve rahatsız eder.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili; teknik/geliştirici dili kabul eder, uzun anlatım sevmez |
| Gerçek veri yasak | Üniversite, maaş, sağlık, yüzdelik istatistik yok (ADR-005 / P6.5) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme + söz panelini aç | LCP ≤ **2500 ms**, sözler parça ile senkron (kayma yok), CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | 1.4.13 (`VERIFIED`) |
| 2 | Hızlı skip (10 parça ardışık) | Her atlamada anlık geri bildirim; sırada kayıp yok | DOM assertion + sayaç kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 3 | Tür / rap filtresi (Türkçe Rap → Trap/Drill) | Filtre değişince liste yenilenir; eski türden öğe kalmaz | DOM assertion + screenshot | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 4 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | — |
| 5 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; net oynat kontrolü; klavye erişimi | Trace + console log + a11y audit | İlk ses < **1 sn** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 6 | Offline indirme (çevrimdışı kip) | İndirme tamamlanır; indirilen liste çevrimdışı açılır ve çalar | Playwright offline + DOM assertion | `use: { offline: true }` (P8.4 `VERIFIED`) | — |
| 7 | Offline → online senkron | Ağa dönüşte kuyruk senkronlanır; çakışma/veri kaybı olmaz | Level 3 E2E + LocalStorage kontrolü | `use: { offline: true }` (P8.4 `VERIFIED`) | — |
| 8 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace | Slow 4G profili (P8.3 `VERIFIED`) | — |
| 9 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 10 | Favori (kalp) ekleme | Anlık görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 11 | Çalma listesi sıralama (sürükle-bırak) | Sürükleyicinin yanında klavye/dokunma alternatifi çalışır (2.5.7) | Manuel + a11y audit | — (P8 dışı) | 2.5.7 (`VERIFIED`) |
| 12 | Tema geçişi (dark / light) | Geçiş müziği kesmez; iki temada da metin kontrastı ≥ **4.5:1** | Manuel + ekran görüntüsü + kontrast denetimi | — (P8 dışı; eşik P5) | 1.4.3 (`VERIFIED`) |
| 13 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 1.4.11 (`VERIFIED`) |
| 14 | Hata sayfaları (404 / kopma) | Sade Türkçe; hata kodu + "Tekrar Dene"; yığın izni görünmez | Manuel + screenshot | — (P8 dışı) | 3.3.8 (`VERIFIED`) |
| 15 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 10 kriterin hiçbirinde ihlal yok | Lighthouse / axe | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8 (`VERIFIED`) |
| 16 | Odak sırası (klavye / gezinme) | Odak sırası mantıklı; odak görünür ve gizlenmemiş (2.4.7 / 2.4.11) | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 · 2.4.11 (`VERIFIED`) |
| 17 | Türkçe karakter içeriği (mojibake denetimi) | Sanatçı/şarkı adlarında karakter bozulmaz | `vault-utf8-writer verify` + ekran görüntüsü | — (P8 dışı) | — |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); WCAG kodları yalnız P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir); P8 dışı satırlarda metrik `— (P8 dışı)` olarak bırakılmıştır.*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi (15 Eylül 1997), semt, meslek, ulaşım, rumuz, ilişki/kurgu detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (29) — eski dosya 30 ↔ index §6.3 29 (çelişki) | Kurgusal (index ataması) | [[personas/index]] §6.3 | eski salt-okunur persona dosyası (30) | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` + ⚠️ çelişki §7.2 |
| 3 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 4 | KVKK/yaş karşılaştırması; KVKK'ya 16 numarasını atfetmek = YANLIŞ (TMK 18 · GDPR 16 · COPPA 13) | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Sağlık verisi yazılmadı (m.6 hassasiyeti) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 7 | Big Five puanları (E 70 · A 60 · C 70 · N 30 · O 80) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | /10 → 0-100 dönüşüm + N ters kodlama (Duygusal Denge 7/10 → N = 30) | Türetilmiş hesaplama | eski salt-okunur dosya (/10 puanları) | persona-template §3.5.3 (0-100 aralık) | `⚠️ DERIVED` |
| 9 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 10 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 11 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 12 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 13 | Mood küme adı (Hip-Hop) | Vault referanslı atama | [[mood-taxonomy]] §3.3 küme 15 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 14 | Küme tanımı alıntısı + test etkisi (söz senkronu, hızlı skip, tür filtresi) | Vault verisi | [[mood-taxonomy]] §3.3 küme 15 | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | İkincil mood "Sporcu" | Listede yok | eski salt-okunur persona dosyası | mood-taxonomy §3 (yok) | `⚠️ VERIFICATION REQUIRED` — yazılmadı; §7.2 çelişki |
| 16 | Taksonomi küme BPM notu (80-140) | Eski taksonomi önermesi | mood-taxonomy §3.3 küme 15 (⚠️ işaretli) | research-bank P4.7 | `⚠️ VERIFICATION REQUIRED` — eşik olarak kullanılmadı |
| 17 | Rap/Trap BPM aralıkları (boom-bap 85-95 · trap 70-110, davul ızgarası 130-170 · modern rap ~140) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED`) |
| 18 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 19 | "Ezhel Spotify Türkiye 2018-2021 en çok dinlenen" benzeri sıralama | Tek kaynak (SINGLE-SOURCE) | research-bank P4.6 / X-3 (2. bağımsız kaynak BULUNAMADI) | — | `⚠️ VERIFICATION REQUIRED` — olgu olarak YAZILMADI |
| 20 | Favori sanatçı adları (Ceza, Ezhel, Sagopa Kajmer, Kendrick Lamar, Travis Scott) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 21 | Cihaz: iPhone 15 Pro (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Cihaz spec'i: A34 5G — 1080 × 2340 ana ekran çözünürlüğü | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") | `Kaynak: [k1] + [k2]` (P3.1/P3.3 `VERIFIED`) |
| 23 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 24 | Diğer cihazlar (dizüstü, monitör, oyun konsolu) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 26 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 27 | Persona renk kombinasyonu (dark tema) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x'in açılmayanları) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Offline emülasyon (`use: { offline: true }`) + Playwright emülasyon alanları | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Eski dosyadaki yasak veri (üniversite, maaş, gelir/yüzde iddiaları, sağlık/supplement, 30 yaş, "Sporcu" ikinciliği, şarkı listesi BPM bağlamı) | Bankada YOK / kapsam dışı | research-bank P1-P8 kapsamı + ADR-005 + P6.5 | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ahmet-celik-hiphop.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; üniversite/maaş/sağlık verisi yazılmaz | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 15 Pro viewport = 390×844` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka A34 türevi ⚠️ DERIVED` |
| `Şarkı "Holocaust" 92 BPM` | Tür aralığı (P4.5, VERIFIED) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.18 = 18 (erginlik); COPPA 13; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 küme 15 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia ve yasak veri taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/.personas/yetiskin-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır (kuyruk mojibake'li — asla birebir kopyalanmaz; üniversite/maaş/sağlık/yüzde verisi taşınmaz) | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift yer tutucu açılışı | §4 bloğu dışında iki süslü parantez | Yer tutucuyu doldur veya §4/§5 dışına taşıma |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Ahmet için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yasak veri taşıma | Eski dosyadan üniversite/maaş/sağlık/yaş 30 satırı | Sil; §7.2'ye çelişki kaydı ekle |

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
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik (research-bank P8 değeri) / WCAG kriteri)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor; yer tutucu kalmamış (yalnız §4 Şablon-Önce bloğundaki değişken kural metni hariç)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (yalnız genel kurgusal/zorunlu satır)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden (1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8); persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.5); şarkı BPM'i yok; taksonomi "80-140" notu `⚠️ VERIFICATION REQUIRED`
- [ ] Yasak veri yok (üniversite, maaş, yüzde, sağlık, Ezhel Spotify sıralaması, yaş 30)
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
| Kaynak & Doğrulama satırı | 32 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 küme 15) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 30 | 29 | [[personas/index]] §6.3 kazanır (⚠️ iki değer de kayıtlıdır) |
| Doğum tarihi | 15 Eylül 1996 (30 ile tutarlı) | 15 Eylül 1997 | Yaş 29 ile tutarlılık için kurgusal türetme |
| İkincil mood | Sporcu / Disiplinli | Yok | "Sporcu" mood-taxonomy §3 listesinde YOK → yazılmadı, §7.2'de saklı |
| Birincil cihaz | iPhone 15 Pro (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| BPM | Eski dosyada kişisel 90-160 / spor 140-160 / şarkı listesi bağlamı | Tür aralıkları P4.5 (VERIFIED); şarkı BPM'i yok | §4.5 + X-2 |
| Yasak veri (üniversite, maaş, gelir/yüzde, sağlık/supplement) | Eski dosyada mevcut | yazılmadı | ADR-005 + P6.5 (6698 m.6) — bankada yok → yazma |
| Ezhel Spotify sıralaması | Eski/implied başarı anlatısı | olgu olarak yazılmadı | X-3 (SINGLE-SOURCE) → `⚠️ VERIFICATION REQUIRED` |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı (kuyruk mojibake'li olduğu için birebir kopyalanmadı), yaş index §6.3'e göre düzeltildi (30→29); yetişkin statüsü (veli onayı false), Big Five /10→0-100 dönüşümü ve N ters kodlama, ikincil mood + yasak veri çelişki kayıtları eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-erkek/ahmet-celik-hiphop
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
