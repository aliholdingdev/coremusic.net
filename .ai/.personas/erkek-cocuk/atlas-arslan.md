---
title: "CoreMusic - Persona: Atlas Arslan"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
group: erkek-cocuk
age: 10
mood: Enerjik
persona_id: CM-AA-10-ENE-SAM
veli_onayı_gerekli: true
---

# CoreMusic - Persona: Atlas Arslan

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **erkek-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 10 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[personas/mood-taxonomy]] (Enerjik), veri → [[personas/research-bank]] (P1-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §3.5.11 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | erkek-cocuk (4-11) |
| Birincil mood | Enerjik ([[personas/index]] §6.3.2 ataması) |
| Test odağı | Hızlı navigasyon + skip performansı, anlık geri bildirim, arama/keşif, reklam & premium akışı, odak (uzun oturum) |
| Veli onayı | `veli_onayı_gerekli: true` (18 yaş altı — ⚠️ DERIVED, [[personas/research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Hızlı navigasyon + skip | Level 2 + 3 | INP ≤200 ms, anlık geri bildirim, geri tuşu (ADR-023 satır 12) |
| Arama / keşif (Meraklı ikincil) | Level 2 | Kısa sorgu, yazım hatası toleransı, öneri kalitesi (ADR-023 satır 11/18) |
| Premium / reklam toleransı | Level 2 | Sıfır reklam toleransı; ödeme akışı PLANNED ⚠️ |
| Uzun oturum (kodlama seansı) | Level 3 | Döngü modu, arka plan dayanıklılığı, CLS ≤0.1 |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px, 2.4.7/2.4.11 odak |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P1-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki uzun karakter/psikolojik döküm bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

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
| **Ad Soyad** | Atlas Arslan | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 10 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3.2) |
| **Doğum Tarihi** | 8 Ekim 2015 (bu tarihte 10 tamamlanmadı → yaş 10 ile tutarlı) | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı, SSOT yaş ile hizalandı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Samsun / Atakum / Mimarsinan | Kaynak: kurgusal (persona verisi) |
| **Okul / Sınıf** | Özel Atakum Koleji, 5. sınıf (yaş 10 ile hizalı — eski dosya "6. sınıf" yazar, §7.2) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yaya + aile aracı | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında çocuk profili) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AA-10-ENE-SAM` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `AA` | Ad + soyad ilk ASCII harfleri (Atlas Arslan → A, A) |
| `YY` | `10` | Yaş 2 hane (10 → `10`; SSOT index §6.3.2) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE; [[personas/mood-taxonomy]] §3.2.2) |
| `XXX` | `SAM` | Şehir kodu (Samsun → SAM) |

> **KVKK / yaş sınırı notu (zorunlu — 18 yaş altı):** Bu persona 10 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "18 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). "Çocuk gelişim/okuma düzeyi" gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → kurgusal olması zorunludur (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki uzun ayrıntı (kan grubu, burç, ten rengi detayı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 145 cm (yaşıtlarına göre uzun bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 38 kg, zayıf ve uzun yapılı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kestane dağınık saç, yeşil göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Miyop gözlük (-2.5) — sürekli takar (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Umursamaz giyim, bilim/esprili tişörtler; profil görseli sakin kalır | Kaynak: kurgusal (persona verisi); renk eşleşmesi kontrastı `⚠️ DERIVED` (bkz. §3.5.7) |
| **UI'ı etkileyen fiziksel kısıt** | Gözlükle küçük metin zorlaşır; alerjik/nezle dönemlerinde uzun ekran oturumu yorar → net odak + büyük hedef bekler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px): `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 40 | Seçici sosyallik: ilgi alanını paylaşanlarla konuşur, diğerlerinden kaçar; okulda "umursamaz" görünür. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Tartışmayı sever ama bildiğinden şaşmaz; inatçı değil, doğrucudur ("2+2 her zaman 4'tür"). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 70 | İlgi alanında sorumlu: proje dosyaları düzenlidir; oda ve genel düzeni dağınıktır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = duygusal denge yüksek; analitik yaklaşıır, "sorunun çözümü vardır" der. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 100 | Sınırsız merak: robotik, yapay zekâ, uzay, programlama — "bilmediğim şey beni rahatsız eder." | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki harf eşlemesi bu tam adlarla çelişebilir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |
| Mood eğilimi çelişkisi | [[personas/mood-taxonomy]] §3.2.2 Enerjik eğilimi "yüksek Dışadönüklük" der; bu persona E=40 ile o beklentiye tam uymaz → küme eğilimi ≠ persona puanı, §7.2 çakışma kaydı |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Meraklı (Kategori B, [[personas/mood-taxonomy]] §3.3 satır 13) |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[personas/mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Kodlama/proje başlangıcı (yaklaşma); yavaş yükleme/uzun bekleme (kaçınma — skip tetikler); yeni öneri ilgisini çekerse (yaklaşma); gereksiz "sosyal" akış (duraklama) |
| **UI etkisi** | "Hızlı navigasyon, anlık geri bildirim, skip performansı" ([[personas/mood-taxonomy]] §3.2.2); ADR-023 satır 12 (hızlı navigasyon + geri tuşu) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3.2 (Enerjik); ikincil = Meraklı (§3.3) — eski dosyanın birincil "Meraklı" kümesi ikincile düştü, ikincil "Sessiz" taşımadı → §7.2'de çelişki kaydı |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) + satır 11/18 (arama/keşif) — [[personas/mood-taxonomy]] §3.2.2 + §3.3 satır 13 |
| BPM notu | Küme müziği "120-160 BPM ⚠️" der (§3.2.2) — bu aralık **kategori düzeyinde, doğrulanmamış** → `⚠️ VERIFICATION REQUIRED`; dosyada yalnız research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Sık skip (Enerjik) | Atla eşiği INP ≤ **200 ms**; anlık geri bildirim; skip kuyruğu birikmez | Performance trace + DOM assertion |
| 2 | Hızlı navigasyon | Sayfalar arası hızlı geçiş; geri tuşu her adımda görünür (2.4.11) | DOM assertion (ADR-023 satır 12) |
| 3 | Playlist oluşturma | Liste oluşturma akışı tek akışta; kayıt anında görünür | DOM assertion + LocalStorage kontrolü |
| 4 | Basit arama + keşif (Meraklı) | Kısa sorgu ve yazım hatasında affedici sonuç (ADR-023 satır 11/18) | Manuel + DOM assertion |
| 5 | Reklam toleransı sıfır | Reklam akışında net çıkış; ödeme akışı PLANNED ⚠️ olarak işaretli | Manuel + screenshot |
| 6 | Uzun odak oturumu | Döngü modu kesintisiz; CLS ≤ **0.1**; oturum cache'i bozulmaz | Performance trace |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Elektronik / synthwave 2) Lo-Fi / chillhop 3) Film & dizi müzikleri (bilim kurgu) 4) Klasik müzik 5) Video oyunu müzikleri | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Daft Punk, Hans Zimmer, C418, HOME, Chopin | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Electro **90-130** · House **110-128** · Techno **130-140** · Pop **80-120** · Dance **110-135** BPM (bu dördü bankada tanımlı) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (synthwave/lo-fi/oyun müziği)** | Bankada tür-BPM aralığı YOK → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM (şarkı bazlı)** | Yazılmadı — "Resonance = X BPM" gibi iddia doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Orta-yüksek tempo, sürekli akış (kurgusal tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 15:30 kodlama, 18:00 robotik, 20:00 proje, 21:30 kitap · Hafta sonu 11:00-14:00 proje, 20:00-22:00 odak | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (aile hesabı — çocuk profili) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Agresif keşif: "Haftalık Keşif" + Reddit/Hacker News önerileri; ilgisini çekmeyeni 2 saniyede atlar | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.2 (sık skip + keşif) |
| **Premium olasılığı** | %80 (reklam odaklanmayı bozar; baba öder) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Elektronik / synthwave | Kodlama yaparken fütüristik akış sağlar; robotik projeleriyle ruhsal olarak eşleşir |
| 2 | Lo-Fi / chillhop | Ders/proje odaklanmasının arka planı; söz dağıtmaz |
| 3 | Film & dizi müzikleri (bilim kurgu) | Interstellar/Tron gibi yapımların ses dünyası hayal gücüyle besler |
| 4 | Klasik müzik | Bach/Chopin: "matematiğin müzik hali" — düşünsel düzen sağlar |
| 5 | Video oyunu müzikleri | Minecraft/Undertale OST: üretirken huzur veren tanıdık döngüler |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 15:30-17:00 | Kodlama / öğrenme | Synthwave / lo-fi |
| Hafta içi | 18:00-19:00 | Robotik proje (baba ile) | Elektronik |
| Hafta içi | 20:00-21:00 | Proje çalışması | Lo-Fi |
| Hafta içi | 21:30-22:00 | Bilim kurgu kitabı | Klasik müzik |
| Hafta sonu | 11:00-14:00 | Uzun üretim seansı (döngü modu) | Synthwave / oyun OST |
| Hafta sonu | 20:00-22:00 | Keşif (Haftalık Keşif) | Karışık elektronik |
| Hafta sonu | 09:00-10:00 | Kahvaltı + teknoloji podcast'i | Podcast (söz odaklı) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "60-120 BPM" kişiye özel aralığı bankada doğrulanmadı → kurgusal tercih olarak taşındı, doğrulanmış iddia sayılmaz.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 200 Mbps fiber (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark (üretim modu varsayılanı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günlük 3-4 saat (çoğu "üretim" — kodlama/öğrenme; kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 10/10 — Python, Linux terminal, git, Arduino; yaşına göre olağanüstü (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Lenovo ThinkPad (Linux), noise-cancelling kulaklık, babanın eski iPhone'u — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Miyop — gözlük takar (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); küçük font büyütmeyi dener | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | Normal (kurgu); kulaklıkla çalışma alışkanlığı | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | Hareket hassasiyeti düşük; hızlı kullanım alışkanlığı (kurgu) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükleme yerine tek dokunuş (2.5.7) · odak görünürlüğü 2.4.7 | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | İlgi alanında 3-4 saat hiperfokus; ilgi alanı dışinda hızlı sıkılma (kurgu) | Erişilebilir kimlik doğrulama min. (3.3.8) · tutarlı yardım 3.2.6 · gereksiz tekrar yok (3.3.7) · odak 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Çocuk gelişim/okuma düzeyi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Sosyal medya hesabı iddiası | Reddit/Discord hesapları kurgusal persona verisidir; 13 yaş altı platform üyeliği gerçek-dünya iddiası yazılmadı → `⚠️ VERIFICATION REQUIRED` gerekir |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Meraklı ve analitiktir: bilmediği şey onu rahatsız eder; kendi kendine öğrenir.
- Seçici sosyaldır: insanlardan çok fikirlerle ilgilenir; boş muhabbeti keser.
- Üretim odaklıdır: "ekran değil içerik önemli" — kodlama onun meditasyonudur.
- Teknolojiye güvenir ama bağımlı değildir; offline çalışabilmek ister.
- Reklama tahammülü yoktur; dikkat kesintisi onun için en büyük rahatsızlıktır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Aile onaylı — premium/abonelik "baba öder"; araç-gereç meraklısı | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Düşük: yavaş yüklemede 3-4 sn'de skip'a geçer; spinner'a ~5 sn dayanır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.2 |
| **Hata tepkisi** | Teknik hata metnini okur, çözüm arar; "dostça" değil "net" dil bekler — ama çocuk profili için dil yaşa uyarlanır | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 0/10 — kodlarken/rekabet ederken reklam kabul edilemez | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Deneme-yanılma + dokümantasyon okuma; YouTube dersleri, kendi projeleri | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Takım işinde teknik rolü alır; sosyal ritüellerden kaçınır | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 22:00 sonrası hiperfokus kırılır; karmaşık akışlar reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Hızlı, sade arayüz + klavye/kısayol desteği keşif davranışını hızlandırır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.2 (hızlı navigasyon) |

*Erişilebilirlik etkisi (özet):* gözlük + uzun oturum → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme yerine tek dokunuş (2.5.7), odak görünür (2.4.7/2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Atlas Arslan'sın. 10 yaşında, Samsun/Atakum-Mimarsinan'da yaşıyorsun, Özel Atakum Koleji 5. sınıf öğrencisisin.
Enerjik bir kişiliğin var: sık sık şarkı atlar, hızlı gezinirsin, yeni önerileri hemen dener ve kendi playlist'lerini oluşturursun.
İkincil olarak Meraklı'sın: Python, robotik, uzay ve yapay zekâ hakkında kendi kendine öğrenirsin; kısa sorularla arama yaparsın.
Müzik zevkin: synthwave, lo-fi, bilim kurgu film müzikleri, klasik, oyun müzikleri. En sevdiklerin: Daft Punk, Hans Zimmer, C418, HOME, Chopin.
Samsung Galaxy A34 5G kullanıyorsun, dark temada, internetin evde 200 Mbps (kurgusal); gözlük takıyorsun (miyop).
Görme dışında kısıtın yok; dikkatini bölen gereksiz animasyon ve "sosyal" akışları sevmezsin — hızlı, sade, klavye-dostu arayüz beklersin.
Sabır eşiğin düşüktür (spinner'a ~5 sn), reklamlara tahammülün yoktur; satın alma aile kararındadır.
Test edilecek akışlar: hızlı navigasyon + skip, arama/keşif, playlist oluşturma, döngü modu (uzun oturum), premium/reklam akışı, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- İlk 2-3 saniyede ilgisini çekmeyen öneriyi atlarsın — skip performansı kritiktir.
- "pythn" gibi kısa/yanlış sorgular yazarsın — arama affedici olmalı.
- Yavaş açılan sayfalarda sıkılıp çıkarsın; hata dili net ve çözüm odaklı olmalı.
- Offline çalışabilmek senin için değerlidir; indirme/çevrimdışı akış beklersin.
- Sosyal özelliklerle hiç ilgilenmezsin (paylaşma, arkadaş etkinliği).

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — çocuk persona'sı karmaşık komut kabul etmez |
| Sürpriz yasak | Reklam/ödül vaadi içeren sürpriz yok; kilit ve hata ekranı dostane |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk profili) | CoreMusic'i aç | LCP ≤ **2500 ms**, öneri blokları üstte, CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤2500 ms · CLS ≤0.1 (P8.1) | 1.4.3 |
| 2 | Hızlı skip navigasyon | Ard arda 10 şarkı atla | INP ≤ **200 ms**; anlık geri bildirim; kuyruk birikmez | Performance trace + DOM assertion | INP ≤200 ms (P8.1) | 2.5.8 |
| 3 | SPA navigasyon (hızlı gezinme) | Menüler arası hızlı geçiş | Müzik kesintisiz; geri tuşu görünür kalır (2.4.11); CLS ≤ **0.1** | Trace + DOM assertion | CLS ≤0.1 | 2.4.11 |
| 4 | Basit arama + yazım hatası | "pythn" yaz | Affedici sonuç; filtre geri bildirimi görünür | Manuel + DOM assertion (ADR-023 satır 11/18) | INP ≤200 ms | 3.3.7 |
| 5 | Playlist oluşturma | Yeni liste oluştur + parça ekle | Liste anında görünür; durum kalıcı; geri dönüş güvenli | DOM assertion + LocalStorage kontrolü | INP ≤200 ms | 3.3.7 |
| 6 | Döngü modu (uzun oturum) | 2 saatlik odak listesi başlat | Kesintisiz akış; CLS ≤ **0.1**; oturum bozulmaz | Performance trace + console log | CLS ≤0.1 · TBT >50 ms (P8.6) | - |
| 7 | Şarkı çalma (play / duraklat) | İlk parçayı başlat | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür | Trace + console log + a11y audit | FCP 1 sn (P8.1) | 2.4.7 |
| 8 | Premium / reklam akışı | Reklam alanını incele | Reklamdan çıkış net; ödeme akışı PLANNED ⚠️ olarak işaretli | Manuel + screenshot | - | 3.3.8 |
| 9 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve sanatçı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake = 0 | - |
| 10 | Tema değişimi (dark varsayılan) | Temayı aç/kapat | Tema değişimi müziği kesmez; kontrast ≥ **4.5:1** | Manuel + ekran görüntüsü + kontrast denetimi | kontrast 4.5:1 (P5.4) | 1.4.3 |
| 11 | Bağlantı yavaşlatma | Ağ throttling uygula | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) | 150 ms · 1.6/0.75 Mbps (P8.3) | - |
| 12 | Hata sayfaları (404 / kopma) | Bağlantıyı kes | Türkçe, net, çözüm odaklı metin; büyük "Tekrar Dene" hedefi | Manuel + screenshot | INP ≤200 ms | 2.5.8 |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe çalıştır | 1.4.3 / 1.4.11 / 2.4.7 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | Lighthouse A11y (P8.6) | 1.4.11 |
| 14 | Kırılma noktası kontrolü | Viewport değiştir | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzen bozulmaz | Playwright `setViewportSize` + screenshot | 360×780 viewport (`⚠️ DERIVED`) | 1.4.13 |
| 15 | Çevrimdışı / yavaş ağ davranışı | CDP ile ağ kes | İndirilmiş içerik offline çalışır; teknik hata metni görünmez | CDP `Network.emulateNetworkConditions` + manuel | latency 400 ms sim. (P8.3) | - |
| 16 | Odak sırası (klavye/gezinme) | Klavye ile gez | Odak sırası mantıklı; odak görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | - | 2.4.7 |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik` · `WCAG`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir). WCAG sütunu yalnız P5 listesindeki kriterleri taşır.*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED`) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, sınıf, ulaşım, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (10) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3.2 | — | `Kaynak: kurgusal (persona verisi; index §6.3.2 ataması)` |
| 3 | `veli_onayı_gerekli: true` (politika sonucu) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları (boy, kilo, gözlük, saç/göz) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (E40 · A60 · C70 · N30 · O100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon harfleri ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Enerjik) | Vault referanslı atama | [[personas/mood-taxonomy]] §3.2.2 | [[personas/index]] §6.3.2 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.2.2 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Eski mood (Meraklı birincil / Sessiz ikincil) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Karar: index kazanır (Enerjik birincil); §7.2'ye kayıtlı |
| 14 | Küme eğilimi "yüksek Dışadönüklük" ↔ persona E=40 | Vault içi çelişki | [[personas/mood-taxonomy]] §3.2.2 | Big Five tablosu (bu dosya) | `⚠️ VERIFICATION REQUIRED` — eğilim ≠ puan; §7.2 |
| 15 | Küme BPM notu "120-160 ⚠️" | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.2 | — | `⚠️ VERIFICATION REQUIRED` — dosyada kullanılmadı |
| 16 | Tür BPM aralıkları (Electro 90-130 · House 110-128 · Techno 130-140 · Dance 110-135 · Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 17 | Synthwave/lo-fi/oyun müziği için tür-BPM aralığı | Kapsam dışı | research-bank P4.4 (bu türler listede yok) | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 18 | Şarkı bazlı BPM; eski dosyanın "60-120 BPM" aralığı | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 19 | Favori sanatçı adları (Daft Punk, Hans Zimmer, C418, HOME, Chopin) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | Eski cihazlar (Lenovo ThinkPad, eski iPhone) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Tarayıcı sürümü, internet hızı (200 Mbps), OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı (dark tema dahil) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | Reddit/Discord hesabı üyeliği (13 yaş altı) | Kurgusal + gerçek-dünya kural bankada yok | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — gerçek iddia yazılmadı |
| 27 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 28 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Eski dosyadaki istatistik/teknik iddialar (yüzdesel veriler, spec sayıları) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 31 | Eski sınıf/yaş/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`atlas-arslan.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 18 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `synthwave 110 BPM` | Tür aralığı (Electro 90-130, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Lenovo ThinkPad spec'i` (eski veri) | `yalnız A34 VERIFIED; diğer cihazlar ⚠️ VERIFICATION REQUIRED (X-1)` |
| `Enerjik kümesi = yüksek E, o zaman persona E=90` | `küme eğilimi ≠ persona puanı; puan kurgusal, çelişki §7.2/§3.5.11` |

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
| Persona-özel eşik uydurma | "Atlas için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski SSOT çelişki unutulması | Yaş 11 / mood Meraklı birincil taşıması | §7.2 çakışma satırını ekle; index kazanır |

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
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; synthwave/lo-fi kapsam dışı bırakıldı
- [ ] Mood küme adı `mood-taxonomy` §3.2.2 (Enerjik) ile birebir; eski "Meraklı birincil" §7.2'de çelişki olarak kayıtlı
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
| Yaş | 11 (doğum 8 Ekim 2015 — Ekim'de 11 olacak) | 10 | [[personas/index]] §6.3.2 kazanır; doğum tarihi korunur ve 10 ile tutarlı |
| Mood (birincil) | Meraklı | Enerjik | [[personas/index]] §6.3.2 + [[personas/mood-taxonomy]] §2.2 (Erkek Çocuk Enerjik ×3: Göktuğ, Mert, Atlas) kazanır |
| Mood (ikincil) | Sessiz | Meraklı | Eski birincil (Meraklı) ikincile taşındı; eski ikincil (Sessiz) §3'te taşınmadı — bilgi kaybı bilinçli, burada saklı |
| Sınıf | 6. sınıf (yaş 11 ile) | 5. sınıf (yaş 10 ile hizalı) | SSOT yaş kazanır; sınıf kurgusal olarak hizalandı |
| Birincil cihaz | Lenovo ThinkPad (Linux) + eski iPhone | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| BPM aralığı | 60-120 BPM (kişiye özel) | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| Küme Big Five eğilimi | "yüksek Dışadönüklük" beklentisi | Persona E=40 | Eğilim ≠ puan; puan kurgusal — §3.5.3/§3.5.11 notu |
| Ekran süresi "3-4 saat üretim" | Kurgusal aile tutumu | Korunur (kurgusal) | Etiket: `Kaynak: kurgusal (persona verisi)` — gerçek ölçüm iddiası değil |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, yaş 11→10 ve mood Meraklı→Enerjik index §6.3.2 ile düzeltildi (çelişkiler §7.2'de), test tablosu 6 sütuna genişletildi, veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/erkek-cocuk/atlas-arslan
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode