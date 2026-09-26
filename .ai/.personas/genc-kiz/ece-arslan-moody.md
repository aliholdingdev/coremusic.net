---
title: "CoreMusic — Persona: Ece Arslan"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-kiz/ece-arslan-moody"
updated: 2026-09-26
group: genc-kiz
age: 15
mood: Moody
persona_id: CM-EA-15-MOD-SAK
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Ece Arslan

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-kiz (13-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 15 yaşındaki bir genç kızın gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Moody / Melankolik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-kiz (13-17) |
| Birincil mood | Moody ([[personas/index]] §6.3 ataması) |
| Test odağı | Koyu/AMOLED tema, EQ & bass boost, niş tür araması, Discord paylaşımı, gece uzun oturum |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Mood-geçiş (ADR-023 satır 16) | Level 2 + 3 | Moody → tür değiştirme (genre switching); geçersiz mood değeri = error |
| Karanlık tema / AMOLED siyah | Level 2 + 3 | Neon vurgular, siyah zemin, kontrast 4.5:1 kontrolü |
| Keşif & niş tür araması | Level 2 | witch house / industrial / DnB sorguları; boş sonuç metni |
| Paylaşım akışı (Discord) | Level 2 + 3 | Tek tıkla paylaşım, bağlantı önizlemesi, 280 karakter sınırı |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px, 2.4.7 odak denetimi |

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
| 3 | [[mood-taxonomy]] §3.2.4 | Küme adı + tanım alıntısı (Moody) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Ece Arslan | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 15 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 31 Ekim 2011 | Kaynak: kurgusal (persona verisi; yaş 15 ile tutarlılık için türetildi) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Sakarya / Serdivan / (semt kurgu) | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Serdivan Anadolu Lisesi, 10. sınıf | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yaya + okul otobüsü (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | ece.cyber | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-EA-15-MOD-SAK` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `EA` | Ad + soyad ilk harfleri (Ece Arslan → E, A) |
| `YY` | `15` | Yaş 2 hane (15 → `15`) |
| `ZZZ` | `MOD` | Mood tür kodu (Moody → MOD) |
| `XXX` | `SAK` | Şehir kodu (Sakarya → SAK) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 15 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek kişi verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (makyaj, piercing, ayakkabı numarası…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 162 cm (ergenlik dönemi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 49 kg, orta yapı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah kısa asimetrik kesim, koyu kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme ve işitme iyi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Karanlık-cyber estetik: siyah ağırlıklı, neon yeşil/elektrik mavisi detaylar, zincir aksesuar (profil görselinde neon vurgu — `⚠️ DERIVED` renk eşleşmesi, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Gece düşük ışıkta uzun oturum (01:00'e kadar), uyku düzensiz — göz yorgunluğu riski | Kaynak: kurgusal (persona verisi); kontrast kriteri: `Kaynak: [k1] + [k2]` (P5.4) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 30 | Kendi dünyasında yaşar, yalnız takılmayı sever; buna karşın online oyunda ekip oyuncusudur. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 30 | Otoriteye karşıdır, kendi kuralları vardır; ailesiyle giyim/estetik tartışmaları sürer. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 40 | Dersleri orta düzeydedir ve umursamaz görünür; sevdiği işlerde (müzik, oyun, mixtape) disiplinlidir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = uçlara rağmen toparlanması hızlıdır; eski dosyanın "moody" betimlemesiyle eksen yönü tartışmalı → §7.2. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Alternatif kültür, yeraltı müzik, cyberpunk estetiği; deneysel olan her şeyi dener. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki /10 puanları ×10 ile taşındı (dönüşüm: `⚠️ DERIVED`) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Moody |
| **İkincil küme** | Melankolik ([[mood-taxonomy]] §3.2.3) |
| **Küme tanımı (alıntı)** | "Ruh hâline göre tür değiştiren; geniş BPM aralığında, yüksek skip oranlı dinleyici" ([[mood-taxonomy]] §3.2.4) |
| **Tetikleyici durumlar** | Gece + yalnızlıkta derin dinleme ve tür değiştirme (yaklaşma); gündüz okul ortamı ve popüler baskıdan kaçış (geri çekilme) |
| **UI etkisi** | "Değişken, kişiselleştirilebilir tema/öneri ayarı"; test etkisi: "Anlık karar, tür değiştirme (genre switching), öneri doğruluğu; ADR-023 satır 16 (geçersiz mood değeri = error)" ([[mood-taxonomy]] §3.2.4) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Moody); ikincil = eski salt-okunur persona kaynağı (Melankolik) — §7.2'de kayıtlı |
| Test etkisi | ADR-023 satır 16 (mood-geçiş) + tür değiştirme / öneri doğruluğu — [[mood-taxonomy]] §3.2.4 |
| Taksonomi BPM notu | §3.2.4 "80-140 (geniş)" notu ⚠️ işaretli kurgusal önermedir (§1.2); test eşikleri olarak research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Yüksek skip / ruh haline göre tür değiştirme | Sonraki/önceki parça geçişi gecikmesiz; öneri türü ruh haline uyarlanır | Trace + sayaç kontrolü |
| 2 | Geniş BPM aralığında dinleme | BPM/tür filtresi hatasız çalışır; geçersiz filtre değeri hata (error) üretir | Manuel + hata akışı testi |
| 3 | Gece düşük ışık kullanımı | Koyu/AMOLED tema varsayılan okunur; kontrast ≥ **4.5:1** (1.4.3) | Kontrast denetimi + screenshot |
| 4 | Mood-geçiş (ADR-023 satır 16) | Geçerli mood → arayüz uyarlanır; geçersiz değer → hata (error) | Manuel + hata akışı testi |
| 5 | Özelleştirilebilir tema beklentisi | Tema ayarı kalıcı (localStorage), oturumda geri gelir | DOM assertion + LocalStorage kontrolü |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Synthwave / Darkwave 2) Industrial / EBM 3) Drum and Bass 4) Cyberpunk oyun & film müzikleri 5) Witch House | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Carpenter Brut, Perturbator, Gesaffelstein, Nine Inch Nails, HEALTH | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Electro **90-130** BPM (synthwave/industrial için en yakın VERIFIED aralık) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (DnB / witch house)** | P4.4'te yer almıyor → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 120-175 BPM (eski dosya tercihi — kurgusal, ölçeklenmedi) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 07:45 (okul yolu), 16:30-19:00 (oyun), 19:45-01:00 (müzik/üretim) · Hafta sonu 10:00-02:00 | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic + SoundCloud (kendi mixtape'leri) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Reddit, Bandcamp, Discord müzik sunucuları; algoritmadan çok topluluk önerisi | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %50 (kendi alamaz, aileye söylemez; reklam idare eder) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Synthwave / Darkwave | Cyberpunk estetiği, retro-fütüristik neon geceler |
| 2 | Industrial / EBM | Sert, agresif, endüstriyel sesler |
| 3 | Drum and Bass | Yüksek tempolu, oyun için ideal arka plan |
| 4 | Cyberpunk oyun & film müzikleri | Deus Ex, Blade Runner 2049 tarzı atmosfer |
| 5 | Witch House | Karanlık, gizemli, deneysel |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:45-08:15 | Okul yolu | Synthwave |
| Hafta içi | 16:30-19:00 | Oyun (Valorant / LoL) | DnB / oyun müziği |
| Hafta içi | 19:45-22:00 | Mixtape üretimi (SoundCloud) | Industrial / electro |
| Hafta içi | 22:00-01:00 | Discord + Reddit keşfi | Witch house / niş |
| Hafta sonu | 12:00-18:00 | Uzun oturum (oyun + müzik) | Karışık, geniş aralık |
| Hafta sonu | 20:00-02:00 | Gece derin dinleme | Darkwave / ambient |

*Not: BPM satırları yalnız tür aralığı düzeyindedir (P4.4/P4.7); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "120-175 BPM" tercihi bankada doğrulanmadı → kurgusal tercih olarak taşındı.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G (test referans cihazı) | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 60 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark (gece kullanımı baskın; açık tema gözünü yorar) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 3-4 sa/gün (telefon); asıl oturum masaüstünde | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — PC toplama, ses yazılımı, oyun modları | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | MSI gaming laptop, Xiaomi telefon, HyperX Cloud II kulaklık — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |
| **Gaming donanım etkisi** | Kulaklık + PC kullanımı mobil viewport'u daraltır: testte A34 5G viewport'u esastır | Kaynak: kurgusal (persona verisi); viewport: `⚠️ DERIVED` |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); gece düşük ışıkta kullanım yoğun | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — karanlık/neon temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; kulaklıkla yüksek ses dinleme | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | normal | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükle-bırak yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | sabır düşük, alaycı ton riski | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 — hata metni küçümseyici olmamalı | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Uyku düzensizliği / ruh hâli" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Neon/karanlık renk kombinasyonu | Kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil — neon × siyah riski testte kontrol edilir |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Özgün ve asi: sıradanlaşmaktan korkar, "tuhaf kız" etiketinden gurur duyar.
- Gece yaşıyor: en verimli saatleri 22:00 sonrası; sabah okulu zor gelir.
- Müziği yalnız dinlemez, üretir: mixtape yapar, EQ ile oynar, bass'i vücudunda hissetmek ister.
- Discord dünyası: online arkadaşları gerçek hayattakilerinden yakındır; paylaşım oraya yapılır.
- Otoriteyle çatışma: kural/kısıt mesajları küçümseyici olursa savunmaya geçer.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Kendi alamaz; aileye söylemeye çekinir, reklamla idare eder | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn; spinner'a ~6 sn dayanır, sonra oyunu bırakır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Alaycı bir tepki verir, hatayı platforma yazar; suçlayıcı ton tetikler | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 5/10 — kısa reklamı idare eder, uzunda beklemek yerine uygulamayı kapatır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Deneyerek; ayarları elle kurcalar, dokümantasyon okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Discord'da 5+ sunucu;playlist önerisini doğrudan paylaşır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 01:00 sonrası karar vermesi zorlaşır; karmaşık akış reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Niş katalog derinliği + koyu tema görünce keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.4 (geniş aralık, yüksek skip) |

*Erişilebilirlik etkisi (özet):* gece kullanımı + koyu tema → kontrast ≥ **4.5:1** (1.4.3) ayrıca açık temada test edilir; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ece Arslan'sın. 15 yaşında, Sakarya/Serdivan'da yaşıyorsun, Serdivan Anadolu Lisesi 10. sınıf öğrencisisin.
Moody bir kişiliğin var: ruh haline göre tür değiştirirsin, geniş tempo aralığında dinler, hızlı atlatma (skip) yaparsın; ikincil olarak Melankolik'sin — gece yaşar, yalnız dinlersin.
Müzik zevkin: synthwave/darkwave, industrial/EBM, drum and bass, cyberpunk oyun & film müzikleri, witch house. En sevdiklerin: Carpenter Brut, Perturbator, Gesaffelstein, Nine Inch Nails, HEALTH.
Test cihazın Samsung Galaxy A34 5G'dir; hayatın gaming laptop + oyuncu kulaklığıdır (kurgu).
Görme / işitme / hareket kısıtların yok; gece düşük ışıkta uzun oturum kurarsın — koyu tema ve kontrast senin için kritiktir.
Sabır eşiğin 3 sn, spinner'a ~6 sn dayanırsın; hata metinlerinde alaycı/ küçümseyici dil seni savunmaya geçirir.
Test edilecek akışlar: koyu/AMOLED tema, EQ & bass boost, niş tür araması (witch house / industrial / DnB), Discord paylaşımı, gece uzun oturum, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Karanlık ve neon vurgulu temaları seversin; açık tema gözünü yorar.
- EQ, bass boost ve görsel efekt ayarları senin için birincil özelliklerdir.
- Ana akım pop listelerini görmezden gelirsin; niş katalog derinliği test edilir.
- Discord'a doğrudan paylaşım istersin.
- Oyun oynarken arka planda müzik kesintisiz kalmalıdır.
- Gece boyunca (6+ saat) oturum açık kalır; oturum dayanıklılığı test edilir.
- Kendi mixtape'lerini yaparsın (SoundCloud alışkanlığı — kurgu).
- Hızlı navigasyon istersin; yavaş arayüzde uygulamayı bırakırsın.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler; suçlayıcı/otoriter ton yok |
| Sürpriz yasak | Hassas persona: korkutucu/utanç verici senaryo yok |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (koyu tema) | LCP ≤ **2500 ms**, CLS ≤ **0.1**, niş tür kartları görünür | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Mood-geçiş (Moody arayüz) | Geçerli mood → arayüz uyarlanır; geçersiz mood değeri hata (error) üretir (ADR-023 satır 16) | Manuel + hata akışı testi |
| 3 | Niş tür araması (witch house / industrial) | Sonuç listesi dolu; boşsa anlamlı, alaycı olmayan boş durum metni | DOM assertion + screenshot |
| 4 | EQ / bass boost ayarı | Ayar kaydedilir, oturumda kalıcı; arayüz geri bildirimi anlık | DOM assertion + LocalStorage kontrolü |
| 5 | Şarkı çalma (play / duraklat / skip) | İlk ses < 1 sn; hızlı atlatma gecikmesiz, INP ≤ **200 ms** | Trace + console log |
| 6 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 7 | Discord'a paylaşım | Tek tıkla paylaşım; bağlantı önizlemesi doğru; karakter sınırı korunur | Manuel + DOM assertion |
| 8 | Tema geçişi (dark / light) | Geçiş müziği kesmez; iki temada da kontrast ≥ **4.5:1** (1.4.3) | Manuel + ekran görüntüsü + kontrast denetimi |
| 9 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 10 | Hata sayfaları (404 / kopma) | Türkçe, küçümseyici olmayan metin; teknik yığın izi yok | Manuel + screenshot |
| 11 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 12 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 13 | Gece / düşük ışık senaryosu | Karanlık temada okunabilirlik korunur; neon vurgular göz kamaştırmaz | Manuel + ekran görüntüsü |
| 14 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Şarkı/sanatçı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü |
| 15 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 16 | Uzun oturum (gece, 6+ saat simülasyonu) | Oturum dayanıklılığı; cache TTL sınırında yeniden giriş akışı düzgün | CDP zaman atlatma + manuel |
| 17 | Çevrimdışı / yavaş ağ davranışı | Yavaş ağda yumuşak yükleme animasyonu; teknik hata metni görünmez | CDP `Network.emulateNetworkConditions` + manuel |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, ilçe, okul, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (15) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100; eski /10 ×10) | Kurgusal persona verisi + dönüşüm | eski salt-okunur persona dosyası (yalnız kurgu) | — | `Kaynak: kurgusal (persona verisi)` + `⚠️ DERIVED` (dönüşüm) |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.4 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | N eksen yönü: eski "Duygusal Denge" başlığı ↔ N nörotizm yönü | Vault içi belirsizlik | eski persona dosyası (3/10) | research-bank P7.3 | `⚠️ VERIFICATION REQUIRED` (§7.2 çakışma satırı) |
| 12 | Mood küme adları (Moody / Melankolik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.4 + §3.2.3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.4 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Elektro BPM aralığı (90-130) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 15 | DnB / witch house / industrial BPM aralıkları | Doğrulanmadı (P4.4 kapsam dışı) | research-bank P4.4 | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 16 | Şarkı bazlı BPM; kişisel "120-175 BPM" tercihi | Doğrulanmadı (EXCLUDED) + kurgusal tercih | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` + `Kaynak: kurgusal (persona verisi)` |
| 17 | Favori sanatçı adları (Carpenter Brut, Perturbator, Gesaffelstein, Nine Inch Nails, HEALTH) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 18 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 19 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 20 | Diğer cihazlar (MSI laptop, Xiaomi, HyperX) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 23 | Persona renk kombinasyonu (neon × siyah) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 24 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 25 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Eski dosyadaki istatistik iddiaları (Spotify/TR yüzdeleri, oyun istatistikleri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 29 | Eski cihaz/okul/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ece-arslan-moody.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `DnB 174 BPM` / `witch house 80 BPM` | Tür aralığı (Electro 90-130, VERIFIED ikincil) + DnB/witch house: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.4 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
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
| 3 | Eski dosya: `.ai/personas/genc-kiz/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | §4 bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Ece için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
| Kaynak & Doğrulama satırı | 29 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.4 / §3.2.3) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 15 (tutarlı) | 15 | Çelişki yok |
| Mood | Moody (birincil) / Melankolik (ikincil) | Moody / Melankolik | Tutarlı — index §6.3 + mood-taxonomy §3.2.4/§3.2.3 |
| Kullanıcı ID | `CM-EA-15-MOD-SAK` | `CM-EA-15-MOD-SAK` | Format zaten şablona (§3.5.1) uygun — değişiklik yok |
| Birincil cihaz | MSI gaming laptop + Xiaomi (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| BPM tercihi | 120-175 (kişiye özel) | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| N ekseni yönü | "Duygusal Denge 3/10" (düşük denge) ↔ N=30 (ters kodlama: düşük nörotizm) | N=30 (doğrudan ×10 dönüşüm) | Çelişki saklı; ekseni yeniden doğrulamak `⚠️ VERIFICATION REQUIRED` (P7.5) |
| Mood ↔ Big Five eğilimi | Taksonomi §3.2.4 "yüksek N" önermesi ↔ persona N=30 | Taksonomi önermesi kurgusal (§1.2); persona puanı esas | Çelişki veri hatası değil — taksonomi ⚠️ önerme |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; cihaz A34 5G'ye uyarlandı; veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-kiz/ece-arslan-moody
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
