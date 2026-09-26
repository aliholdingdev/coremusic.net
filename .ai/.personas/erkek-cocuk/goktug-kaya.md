---
title: "CoreMusic - Persona: Göktuğ Kaya"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
group: erkek-cocuk
age: 10
mood: Enerjik
persona_id: CM-GK-10-ENE-ANK
veli_onayı_gerekli: true
---

# CoreMusic - Persona: Göktuğ Kaya

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **erkek-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 10 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Enerjik, §3.2.2), veri → [[research-bank]] (P1-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | erkek-cocuk (4-11) |
| Birincil mood | Enerjik ([[personas/index]] §6.3.2 ataması) |
| Test odağı | Hızlı navigasyon + anlık geri bildirim + skip performansı, oynat kontrolü, arka plan çalma, ebeveyn süresi limiti, veli onayı |
| Veli onayı | `veli_onayı_gerekli: true` (18 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Hızlı navigasyon + geri tuşu | Level 2 + 3 | ADR-023 satır 12; INP ≤200 ms, geri tuşu kaybolmaz |
| Skip performansı (hızlı atlatma) | Level 2 + 3 | Ard arda atlama; anlık geri bildirim |
| Arka plan / oturum devamlılığı | Level 2 + 3 | Sayfalar arası gezintide müzik kesintisiz |
| Ebeveyn süresi limiti (çocuk modu) | Level 2 | Süre dolunca dostane ekran; pin korumalı ayarlar |
| Veli onayı akışı | Level 1 + 2 | TMK 18 / COPPA 13 / GDPR 16 — onaysız kişiselleştirme yok |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px, 2.4.7 odak denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P1-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki uzun fiziksel/psikolojik döküm bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[personas/research-bank]] P1-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[personas/mood-taxonomy]] §3.2.2 satır | Küme adı + tanım (Enerjik) |
| 4 | [[personas/index]] §6.3.2 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Göktuğ Kaya | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 10 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3.2) |
| **Doğum Tarihi** | 22 Temmuz 2016 (SSOT yaş 10 ile hizalı — eski dosya "22 Temmuz 2020" yazar, §7.2) | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı, SSOT yaş ile hizalandı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Ankara / Çankaya / Ümitköy | Kaynak: kurgusal (persona verisi) |
| **Okul / Sınıf** | Özel Çankaya İlkokulu, 5. sınıf (yaş 10 ile hizalı — eski dosya "anaokulu hazırlık" yazar, §7.2) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Okul servisi + araba (aile) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında çocuk profili — veli onaylı) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-GK-10-ENE-ANK` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `GK` | Ad + soyad ilk ASCII harfleri (Göktuğ Kaya → G, K; ğ→g katlandı) |
| `YY` | `10` | Yaş 2 hane (10 → `10`; SSOT index §6.3.2) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE; [[mood-taxonomy]] §3.2.2) |
| `XXX` | `ANK` | Şehir kodu (Ankara → ANK) |

> **KVKK / yaş sınırı notu (zorunlu — 18 yaş altı):** Bu persona 10 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "18 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). "Çocuk gelişim/okuma düzeyi" gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → kurgusal olması zorunludur (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki uzun ayrıntı (kan grubu, burç, ten rengi detayı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 145 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 36 kg, atletik yapılı (yüzme + jimnastik + park koşusu — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah kısa saç, kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok — görme ve işitme normal (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Spor kıyafet + koşu ayakkabısı; kırmızı seven, sade değil canlı tonlar | Kaynak: kurgusal (persona verisi); renk eşleşmesi kontrastı `⚠️ DERIVED` (bkz. §3.5.7) |
| **UI'ı etkileyen fiziksel kısıt** | Hızlı, dürtüsel dokunuşlar + refleksli parmak → çift tıklama ve kaçırılan hedef riski yüksek | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px): `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 90 | Tam bir sosyal kelebek: herkesle konuşur, ortamda ilgi odağı olur, sessiz kalamaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Paylaşımcıdır ama enerjisi yüksekken baskın olabilir; iyi niyetli, farkındalığı düşük. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 30 | Düzen onun doğasında yok: eşya toplamaz, unutur, kuralları görmezden gelir — yaşına göre düşük. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 40 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 40 = düşük-orta kaygı: neşelidir, öfkesi hızlı gelir hızlı geçer. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 80 | Yeni deneyimlere bayılır: yeni park, yeni spor, hepsine atlar; rutinden sıkılır, "sırada ne var?" der. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.2.2 "Dışadönüklük (O), Deneyime Açıklık (A)" der — taksonomi harfleri P7.3 ile çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §7.2'ye kayıtlıdır. |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.2.2 Enerjik eğilimi "yüksek Dışadönüklük, yüksek Deneyime Açıklık, düşük N" der; bu persona E90 · O80 · N40 ile eğilimle **uyumlu** → çelişki yok (§7.2'de "uymu" satırı) |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Kaşif (§3.2.6 — eski dosyanın ikincil kümesi korunur) |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[personas/mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Hareketli müzik/oyun (yaklaşma); engellenme ve zorla bekletilme (duraklama/öfke); yavaş yüklenen ekran (kaçınma) |
| **UI etkisi** | "Canlı renkler, hızlı animasyonlar, yüksek kontrast" ([[personas/mood-taxonomy]] §3.2.2) — test etkisi: "Hızlı navigasyon, anlık geri bildirim, skip performansı; ADR-023 satır 12 (hızlı navigasyon + geri tuşu)" |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3.2 (Enerjik); ikincil = Kaşif (§3.2.6) — eski dosya zaten Enerjik, mood çakışması YOK → §7.2'de "çakışma yok" satırı |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) + skip performansı — [[personas/mood-taxonomy]] §3.2.2 |
| BPM notu | Küme müziği "120-160 BPM ⚠️" der (§3.2.2) — bu aralık **kategori düzeyinde, doğrulanmamış** → `⚠️ VERIFICATION REQUIRED`; dosyada yalnız research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Hızlı navigasyon (Enerjik) | Sayfalar arası anlık geçiş; geri tuşu her adımda görünür (2.4.11) | DOM assertion + ekran görüntüsü |
| 2 | Sık skip (Enerjik) | Ard arda atlama INP ≤ **200 ms**; anlık geri bildirim, yığılma yok | Performance trace (P8.1) |
| 3 | Anlık geri bildirim beklentisi | Oynat/atla dokunuşu ses/titreşimle onaylanır | Manuel + DOM assertion |
| 4 | Arka plan / multitasking (oyun + müzik) | Navigasyonda müzik kesintisiz; oturum kaybolmaz | Level 3 E2E + trace |
| 5 | Sabırsız yükleme | LCP ≤ **2500 ms**; iskelet düzeni, uzun beklemede yok | Performance trace (P8.1) |
| 6 | Veli onayı + süre limiti | Onaysız kişiselleştirme yok; süre dolunca dostane ekran | E2E + manuel |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Hareketli Pop / Dans 2) Çocuk Şarkıları / Animasyon temaları 3) Spor Marşları / Taraftar 4) Elektronik / EDM (sözsüz) 5) Enerjik Rock | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Edis, Semicenk, Mabel Matiz, Hadise, Rafet El Roman; yabancı kolda: Imagine Dragons, The Score, Fall Out Boy, Coldplay, Queen | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Pop **80-120** · Dance **110-135** BPM (yalnız bu dört türden bankada tanımlı olanlar) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (marş/EDM/rock/çocuk)** | Bankada tür-BPM aralığı YOK → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM (şarkı bazlı)** | Yazılmadı — "şarkı = X BPM" ve "120+ her şey" iddiaları bankada yok; eski "110-160 BPM" aralığı da doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yüksek tempo, ritmik, tekrarlanabilir (kurgusal tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Sabah kalkar kalkmaz (enerji), okul dönüşü araba, akşam baba-oyun saati · Hafta sonu park/ yüzme sonrası arabada | Kaynak: kurgusal (persona verisi); mood: §3.2.2 "Sabah, spor, araba, arkadaşlarla" |
| **Platform** | CoreMusic (aile hesabı — çocuk profili, veli onaylı; annesi yönetir) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Pasif-ikincil: annesinin "buna benzer" önerilerini kabul eder; ikincil Kaşif kümesiyle hızlı ve yüzeysel keşif | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.6 (Kaşif) |
| **Premium olasılığı** | %0 — aile planı; reklam toleransı sıfır | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Hareketli Pop / Dans | Dans etmeye ve zıplamaya uygun her şey; kontrol imkânsız |
| 2 | Çocuk Şarkıları / Animasyon | Süper kahraman temaları — Örümcek Adam ve Batman favori |
| 3 | Spor Marşları | Babasıyla maçlarda öğrendiği marşlar; ritmik alkış |
| 4 | Elektronik / EDM (sözsüz) | Koşarken/oyunda arka plan; sözsüz olması odak sağlar |
| 5 | Enerjik Rock | Babasının arabada açtığı klasik rock; masaya ritim tutar |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:15 | Kahvaltı (hızlı) | Hareketli pop (arka plan) |
| Hafta içi | 08:30 | Servis | Servis radyosu / hareketli şarkılar |
| Hafta içi | 15:30 | Eve dönüş (araba) | Dans pop + animasyon temaları |
| Hafta içi | 16:00-17:30 | Park / yüzme | Yok (fiziksel aktivite) |
| Hafta içi | 19:00 | Babayla oyun | "Enerji" playlisti (arka plan) |
| Hafta içi | 21:00-21:30 | Uyku öncesi | Sakinleştirici (anne açar) |
| Hafta sonu | 10:00-12:00 | Maç / açık hava | Spor marşları (arabada) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "110-160 BPM" kişiye özel aralığı bankada doğrulanmadı → yazılmadı (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Evde fiber (kurgusal) — paket dışı veri | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (varsayılan; akşam otomatik kumara yakın tonlar — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günlük ~90 dk (aile limiti — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — oyun indirme, video gezinme, fotoğraf; şifreyi veli girer (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | "iPad Air (5. nesil)", "annesinin iPhone'u", "eski PlayStation 3" — modeller P3 kapsamında DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Normal — gözlük yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); canlı renk isteği kontrastı düşürmemeli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | Normal (kurgu) | Yüksek ses talebi ("daha yüksek!") kullanıcı davranışı — işitsel uyarı görselle desteklenmeli; kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | İyi motor beceri, hızlı refleks (kurgu) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · çift tıklama tek tıklama sayılmalı · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | Statik aktivitede dikkat kısa, interaktifte uzun (kurgu) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 · tutarlı yardım 3.2.6 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Çocuk gelişim/okuma düzeyi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 44/48 px dokunma hedefi (küme notu) | [[mood-taxonomy]] bu rakamı "⚠️ WCAG research-bank" diye anar; research-bank P5'te yalnız 2.5.8 ≥24×24 px vardır → `⚠️ VERIFICATION REQUIRED` |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Enerji topudur: sabah gözünü açar açmaz koşar; "Bugün ne yapacağız?" en sık sorusudur.
- Hareket başlatıcıdır: parkta 30 saniyede arkadaş olur, "Hadi koşalım!" der, herkesi peşine takar.
- Kinestetik öğrenicidir: yaparak, dokunarak, hareket ederek öğrenir; masa başı ona işkencedir.
- Duyguları hızlıdır: üzüntüsü de sevinci de hızlı gelir hızlı gider; "umursamaz" sanılır ama henüz derinlemesine işlemeyi öğrenmemiştir.
- Dürtüseldir: düşünmeden hareket eder, kuralları unutur, bekleme tahammülü düşüktür — "sıkıldım" en sık cümlesidir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | İsteklidir — "Bunu al!"; aile onaylı, lüks yok | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | ~3 sn: sayfa 3 sn'den uzun yüklenirse terk eder, "Bitti mi?" der | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Bağırma/öfke patlaması kısa sürer; teknik hata metninde veliye sorar — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 0/10 — "reklam çıktı!" çığlığı; uzunda bırakır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Kinestetik: oyun/fiziksel aktivite içinde hızla kavrar; video taklit eder | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Oyun kurar ama doğal lider değil "hareket başlatıcı"dır; söz keser | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası dikkat düşer; sakin ekranlar zoraki kabul görür | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Büyük renkli butonlar + anında geri bildirim + sürekli yenilenen içerik keşfi başlatır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.2 (canlı renkler, hızlı animasyon) |

*Erişilebilirlik etkisi (özet):* dürtüsel dokunuş + kısa sabır → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), çift tıklama tek tıklamaya (2.5.7 dışı — tolere), odak görünür (2.4.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Göktuğ Kaya'sın. 10 yaşında, Ankara Çankaya Ümitköy'te yaşıyorsun, Özel Çankaya İlkokulu 5. sınıf öğrencisisin.
Enerjik ataması var (CoreMusic SSOT): enerji topusun, yerinde duramazsın, sık sık şarkı atlar (skip) ve hızlıca gezinirsin; beklemek sana işkencedir — 3 saniyeden uzun yüklenen ekranı terk edersin.
İkincil olarak Kaşif'sin: enerjin seni yeni şarkı/tür keşfetmeye iter ama hızlı ve yüzeysel incelemekle kalırsın.
Müzik zevkin: hareketli pop/dans, animasyon temaları, spor marşları, sözsüz elektronik, enerjik rock. En sevdiklerin: Edis, Semicenk, Mabel Matiz, Imagine Dragons, The Score, Queen.
Samsung Galaxy A34 5G kullanıyorsun (test referansı), light temada; tek çocuksun, aile hesabında çocuk profisisin.
Görme ve işitme kısıtın yok; büyük, canlı renkli butonlar ve anında geri bildirim istersin, çift tıklama yapabilirsin.
Babanla futbol ve güreş oynarsın, yüzme kursuna gidersin, süper kahramanları seversin; annen düzen ve güvenlik odaklıdır, ekran süreni limitler.
Reklam toleransın sıfırdır; premium'u bilmezsin, aile planı vardır. Sosyal özellikleri kullanmazsın (veli onayı yok).
Test edilecek akışlar: hızlı navigasyon + geri tuşu (ADR-023 satır 12), skip performansı, arka plan/oturum devamlılığı, ebeveyn süresi limiti, veli onayı, erişilebilirlik denetimi.
Davranış notları:
- Hızlı ve dürtüsel dokunursun; arayüz çift tıklamayı ve kaçan dokunuşları tolere etmeli.
- Yavaşlıkta "daha hızlı!" dersin; iskelet düzeni + anlık geri bildirim şart.
- Reklamda bağırırsın; kapat düğmesi büyük ve ilk anda erişilebilir olmalı.
- Uzun bekleme anlarında sıkılırsın — bekleme ekranı ilgi çekici olmalı.
- Kişiselleştirme veli onayıyla başlar; onaysız akış bozulmadan devam etmeli.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — çocuk persona'sı karmaşık komut kabul etmez |
| Sürpriz yasak | Ani yüksek ses/şaşırtıcı animasyon yok; kilit ve hata ekranı dostane |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk profili) | CoreMusic'i aç | LCP ≤ **2500 ms**, iskelet düzen, CLS ≤ **0.1**, splash yok | Performance trace + screenshot | LCP ≤2500 ms · CLS ≤0.1 (P8.1) | 1.4.3 |
| 2 | Hızlı navigasyon + geri tuşu | Sayfalar arasında hızlı gez | Geçiş anlık; geri tuşu her adımda görünür, kaybolmaz (ADR-023 satır 12) | DOM assertion + trace | INP ≤200 ms (P8.1) | 2.4.11 |
| 3 | Skip performansı | Ard arda 5 şarkı atla | INP ≤ **200 ms**; anlık geri bildirim; yığılma/race yok | Performance trace + DOM assertion | INP ≤200 ms | 2.5.8 |
| 4 | Oynat / duraklat (büyük hedef) | Tek dokunuşla kontrol | Çift tıklama tek sayılır; şarkı ATLANMAZ | Manuel + DOM assertion | hata oranı 0 | 2.5.7 |
| 5 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve sanatçı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake = 0 | - |
| 6 | Arka plan / oturum devamlılığı | Çalarken sayfa değiştir | Müzik kesintisiz; dönüşte durum korunur | Level 3 E2E + trace | oturum sürekliliği | 2.4.7 |
| 7 | Ebeveyn süresi limiti | Süreyi bitir | Dostane "bugünlük bu kadar" ekranı; müzik yumuşak durur | E2E + manuel | limit davranışı | 3.3.8 |
| 8 | Veli onayı akışı | Kaydı başlat | Onay istemi görünür; reddedince kişiselleştirme başlamaz | E2E (Playwright) + manuel | TMK 18 / COPPA 13 / GDPR 16 (P6.4) | 3.3.7 |
| 9 | Reklam kapatma (ücretsiz) | Reklam X'ine bas | Kapatma ≥**24×24 CSS px**; odak reklama hapsolmaz | a11y audit + DOM assertion | dokunma hedefi (P5.4) | 2.5.8 |
| 10 | Bağlantı yavaşlatma | Ağ throttling uygula | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) | 150 ms · 1.6/0.75 Mbps (P8.3) | - |
| 11 | Hata sayfaları (kopma) | Bağlantıyı kes | Türkçe, sakin, dostane metin; büyük "Tekrar Dene" hedefi | Manuel + screenshot | INP ≤200 ms | 2.5.8 |
| 12 | Erişilebilirlik denetimi | Lighthouse + axe çalıştır | 1.4.3 / 1.4.11 / 2.4.7 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | Lighthouse A11y (P8.6) | 1.4.11 |
| 13 | Kırılma noktası kontrolü | Viewport değiştir | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzen bozulmaz | Playwright `setViewportSize` + screenshot | 360×780 viewport (`⚠️ DERIVED`) | 1.4.13 |
| 14 | Klavye / odak sırası | Klavye ile gez | Odak sırası mantıklı; odak görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | - | 2.4.7 |
| 15 | Tutarlı yardım | Sayfalar arasında gez | Yardım erişimi her sayfada aynı yerde | DOM assertion | konum tutarlılığı | 3.2.6 |
| 16 | TBT / uzun görev kontrolü | Ağır listede kaydır | TBT ≤ 50 ms; main thread bloklanmaz (INP proxy'si) | Performance trace (P8.6) | TBT ≤50 ms | - |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik` · `WCAG`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir). WCAG sütunu yalnız P5 listesindeki kriterleri taşır.*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED`) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, semt, okul, ulaşım, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (10) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3.2 | — | `Kaynak: kurgusal (persona verisi; index §6.3.2 ataması)` |
| 3 | `veli_onayı_gerekli: true` (politika sonucu) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Doğum tarihi 22 Temmuz 2016 (yaş 10 ile hizalı) | Kurgusal + hizalama | eski salt-okunur persona dosyası (22 Temmuz 2020) | [[personas/index]] §6.3.2 | `⚠️ VERIFICATION REQUIRED` — §7.2'de çelişki |
| 6 | Fiziksel özet satırları (boy, kilo, giyim) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (E90 · A60 · C30 · N40 · O80) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (P7.3 tam adları ↔ mood-taxonomy harfleri) | Vault içi çelişki | research-bank P7.3/P7.5 | [[personas/mood-taxonomy]] §3.2.2 ("Dışadönüklük (O)") | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Enerjik) | Vault referanslı atama | [[personas/mood-taxonomy]] §3.2.2 | [[personas/index]] §6.3.2 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı ("Yüksek enerjili, sık skip eden…") | Vault verisi | [[personas/mood-taxonomy]] §3.2.2 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 14 | Eski mood (Enerjik birincil / Kaşif ikincil) | Uyumlu | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Çelişki YOK — index ile birebir; §7.2'de "çakışma yok" satırı |
| 15 | Küme eğilimi (yüksek E, yüksek O, düşük N) ↔ persona E90/O80/N40 | Uyumlu | [[personas/mood-taxonomy]] §3.2.2 | Big Five tablosu (bu dosya) | Eğilim ile puan uyumlu — çelişki yok |
| 16 | Küme BPM notu "120-160 BPM ⚠️" | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.2 | — | `⚠️ VERIFICATION REQUIRED` — dosyada kullanılmadı |
| 17 | Tür BPM aralıkları (Pop 80-120 · Dance 110-135) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Marş/EDM/rock/çocuk için tür-BPM aralığı | Kapsam dışı | research-bank P4.4 (bu türler listede yok) | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 19 | Şarkı bazlı BPM; "120+ her şey" / "110-160 BPM" iddiaları | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 20 | Favori sanatçı adları (Edis, Semicenk, Mabel Matiz, Hadise, Rafet El Roman; Imagine Dragons, The Score, Fall Out Boy, Coldplay, Queen) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 21 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 22 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 23 | Eski cihazlar (iPad Air 5, iPhone, PS3) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 25 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 26 | Persona renk kombinasyonu kontrastı (canlı renkler dahil) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 27 | 44/48 px dokunma hedefi iddiası (küme notu) | Doğrulanmadı | [[personas/mood-taxonomy]] (⚠️ WCAG research-bank ibaresi) | research-bank P5 (yalnız 24×24 var) | `⚠️ VERIFICATION REQUIRED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.1, 1.4.4, 2.4.3, 3.3.1, 4.1.2…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Eski dosyadaki istatistik/gelir/kan grubu/burç iddiaları (~120.000 TL hane geliri dahil) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 3 | SSOT | Persona kataloğu [[personas/index]]; araştırma verisi [[personas/research-bank]] | İçerik silinir |
| 4 | Zero Hallucination (ADR-005) | Her satır §3.5.11 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`goktug-kaya.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 18 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `dans 125 BPM` | Tür aralığı (Dance 110-135, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `iPad 2360×1640 viewport` (eski veri) | `yalnız A34 VERIFIED; diğer cihazlar ⚠️ VERIFICATION REQUIRED (X-1)` |
| `Enerjik kümesi yüksek E, o zaman persona bilimsel olarak yüksek E` | `küme eğilimi ≠ bilimsel ölçüm; puan kurgusal, uyum §7.2'de` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P1-P8 + [[personas/mood-taxonomy]] §3.2.2 + [[personas/index]] §6.3.2 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra kaynak etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append (raporlanır) | Doğrulanmış dosya |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/documentation/docs-md-template.md` §4.2-§4.6 | Her üretimde |
| 2 | research-bank P1 (demografi), P2 (okul), P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya salt-okunur; yalnız kurgusal kimlik/müzik/rutin taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §3.5.11 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-ölçüsüz eşik uydurma | "Göktuğ için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski SSOT çelişki unutulması | Yaş 6 taşıması | §7.2 çakışma satırını ekle; index kazanır |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 7 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (`| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |` birebir)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4.3 bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 18 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; marş/EDM/rock/çocuk kapsam dışı bırakıldı
- [ ] Mood küme adı `mood-taxonomy` §3.2.2 (Enerjik) ile birebir; eski mood ile çelişki yok (§7.2'de kayıtlı)
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 16 (asgari 10, 6 sütun) |
| Kaynak & Doğrulama satırı | 31 |
| Frontmatter alan | 7 zorunlu + 5 ek |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only (bu turda raporlanır, dosyaya yazılmaz) |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[personas/research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2 Enerjik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 6 | 10 | [[personas/index]] §6.3.2 kazanır; doğum tarihi 2020→2016 hizalandı |
| Doğum Tarihi | 22 Temmuz 2020 | 22 Temmuz 2016 (yaş 10 ile tutarlı) | SSOT yaş kazanır; tarih kurgusal olarak hizalandı |
| Mood (birincil) | Enerjik | Enerjik | **ÇAKIŞMA YOK** — eski dosya zaten index §6.3.2 ile birebir (§2.2: Erkek Çocuk Enerjik ×3: Göktuğ, Mert, Atlas) |
| Mood (ikincil) | Kaşif | Kaşif (korunur) | Çelişki yok — küme §3.2.6'da mevcut |
| Sınıf | Anaokulu — hazırlık (6 yaş grubu) | 5. sınıf (yaş 10 ile hizalı) | SSOT yaş kazanır; sınıf kurgusal olarak hizalandı |
| Birincil cihaz | iPad Air (5. nesil) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Ekran çözünürlüğü | iPad 2360×1640 | 1080×2340 → viewport 360×780 (`⚠️ DERIVED`) | X-1: doğrulanmamış cihaz spec'i yazılmadı |
| BPM aralığı | 110-160 BPM / "120+ her şey" | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmadı |
| Küme Big Five eğilimi | "yüksek E, yüksek O, düşük N" | Persona E90 · O80 · N40 | Eğilim ile puan **uyumlu** — çelişki yok; §3.5.3'te kayıtlı |
| Küme harf eşlemesi | "Dışadönüklük (O), Deneyime Açıklık (A)" | P7.3: E/A/C/N/O tam adları | Taksonomi harfleri çelişir → P7.5 "kod esastır", §3.5.3 |
| Gelir/kan grubu/burç iddiaları | ~120.000 TL, B RH+, Yengeç | Yazılmadı (bankada yok) | X-10: doğrulanamaz iddia bu şablonda taşınmaz |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, yaş 6→10 index §6.3.2 ile düzeltildi (mood zaten uyumlu; çelişkiler §7.2'de), test tablosu 6 sütuna genişletildi, veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/erkek-cocuk/goktug-kaya
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
