---
title: "CoreMusic - Persona: Efe Demir"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
group: erkek-cocuk
age: 7
mood: Sporcu
persona_id: CM-ED-07-SPR-IZM
veli_onayı_gerekli: true
---

# CoreMusic - Persona: Efe Demir

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **erkek-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 7 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Sporcu), veri → [[research-bank]] (P1-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | erkek-cocuk (4-11) |
| Birincil mood | Sporcu ([[personas/index]] §6.3.2 ataması) |
| Test odağı | Workout/antrenman modu, hızlı skip navigasyon, playlist sıralama, offline (servis/tünel), tek elle kullanım |
| Veli onayı | `veli_onayı_gerekli: true` (18 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Workout / antrenman modu | Level 2 + 3 | Büyük dokunma hedefi, hızlı kontrol, tek elle kullanım |
| Hızlı navigasyon + skip | Level 2 + 3 | INP ≤200 ms, anlık geri bildirim, geri tuşu (ADR-023 satır 12) |
| Arama (kısa sorgu + yazım hatası) | Level 2 | "Imagne Dragons" gibi hatalı sorguda affedici sonuç (ADR-023 satır 11/18) |
| Hata ekranları & tünel/4G kopması | Level 2 | Offline cache'e otomatik geçiş; sakin hata dili |
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
| 3 | [[personas/mood-taxonomy]] §3.2.9 + §3.2.2 | Küme adları + tanım (Sporcu, Enerjik) |
| 4 | [[personas/index]] §6.3.2 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Efe Demir | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 7 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3.2 — eski dosyayla ÇAKIŞMA YOK) |
| **Doğum Tarihi** | 5 Eylül 2019 (bu tarihte 7 tamamlanmış → yaş 7 ile tutarlı) | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İzmir / Karşıyaka / Bostanlı | Kaynak: kurgusal (persona verisi) |
| **Okul / Sınıf** | Özel Karşıyaka İlkokulu, 2. sınıf (yaş 7 ile hizalı) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Servis + antrenman dönüşü aile arabası | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında çocuk profili) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-ED-07-SPR-IZM` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `ED` | Ad + soyad ilk ASCII harfleri (Efe Demir → E, D) |
| `YY` | `07` | Yaş 2 hane (7 → `07`; SSOT index §6.3.2) |
| `ZZZ` | `SPR` | Mood tür kodu (Sporcu → SPR; [[mood-taxonomy]] §3.2.9) |
| `XXX` | `IZM` | Şehir kodu (İzmir → IZM) |

> **KVKK / yaş sınırı notu (zorunlu — 18 yaş altı):** Bu persona 7 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "18 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). "Çocuk gelişim/okuma düzeyi" gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → kurgusal olması zorunludur (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki uzun ayrıntı (kan grubu, burç, ten rengi detayı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 124 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 25 kg, atletik yapılı (futbol okulu — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kestane düz saç, yeşil göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Forma/eşofman ağırlıklı; profil görselinde enerjik ama kontrastlı renkler | Kaynak: kurgusal (persona verisi); renk eşleşmesi kontrastı `⚠️ DERIVED` (bkz. §3.5.7) |
| **UI'ı etkileyen fiziksel kısıt** | Hareket halinde/terli parmakla dokunma → büyük hedef + yüksek dokunma toleransı bekler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px): `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Takım kaptanı; saha içinde lider, saha dışında popüler — kalabalığı ve arkadaş ortamını sever. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Pas vermeyi ve takım arkadaşını yüceltmeyi bilir — ama rekabet anında hırslanıp centilmenliği unutabilir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 70 | Antrenmanlara geç kalmaz, çantasını kendisi hazırlar, önce ödev sonra antrenman disiplini. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 60 | **Yüksek puan = düşük duygusal denge (ters kodlama).** Maç kaybedince yıkılır, hakem kararlarına sinirlenir; galibiyette zirve yapar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 60 | Yeni sporlara açık ama rutinini sever; antrenman programı değişince huzursuz olur, müzikte yeni keşif minimaldir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki harf eşlemesi bu tam adlarla çelişebilir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.2.9 Sporcu eğilimi "yüksek Sorumluluk, yüksek O" der; bu persona E=80 (yüksek dışadönüklük) ile kümenin "yüksek E" beklentisine UYAR, O=60 ise "yüksek O" beklentisine TAM UYMAZ → kısmi çelişki §7.2 |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Sporcu |
| **İkincil küme** | Enerjik (Kategori A, [[mood-taxonomy]] §3.2.2 — eski dosyanın ikincil kümesi korunur) |
| **Küme tanımı (alıntı)** | "Antrenman odaklı; yüksek tempolu, motivasyon şarkısı ve workout playlist kullanır" ([[mood-taxonomy]] §3.2.9) |
| **İkincil küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Antrenman/maç öncesi hazırlık (yaklaşma); hızlı skip/beğenme (yaklaşma — Enerjik); reklam/kesinti (kaçınma); maç kaybı/antrenman iptali (duraklama) |
| **UI etkisi** | "Tempo bazlı öneri, workout modu, hızlı kontrol" ([[mood-taxonomy]] §3.2.9) + "Hızlı navigasyon, anlık geri bildirim, skip performansı" (§3.2.2, ADR-023 satır 12) — 44/48 px eşikleri research-bank P5'te AÇILMADI → `⚠️ VERIFICATION REQUIRED`; bankada tek güvenli asgari 2.5.8 ≥24×24 CSS px |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3.2 (Sporcu); ikincil = Enerjik (§3.2.2) — eski dosyayla mood çakışması YOK (§7.2) |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon + geri tuşu) + workout modu / skip performansı — [[mood-taxonomy]] §3.2.9 + §3.2.2 |
| BPM notu | Enerjik kümesi "120-160 BPM ⚠️" der (§3.2.2); eski dosya da "120-160" yazar — her ikisi de **kategori düzeyinde, doğrulanmamış** → `⚠️ VERIFICATION REQUIRED`; dosyada yalnız research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Workout/antrenman modu (Sporcu) | Tek dokunuşla kontrol; hedefler ≥24×24 px; INP ≤ **200 ms** | DOM assertion + a11y audit |
| 2 | Hızlı skip (Enerjik) | Ard arda skip'te gecikme yok, anlık geri bildirim (ADR-023 satır 12) | Performance trace + DOM assertion |
| 3 | Playlist sıralama (ısınma-ansoğuma) | Sıralama değişimi CLS ≤ **0.1**; mevcut çalma kesilmez | Manuel + trace |
| 4 | Basit arama + yazım hatası | "Imagne Dragons" → affedici sonuç, filtre açık (ADR-023 satır 11/18) | Manuel + DOM assertion |
| 5 | Tek elle kullanım (hareket halinde) | Ana kontroller alt yarıda; büyük hedef (2.5.7/2.5.8) | Manuel + screenshot |
| 6 | Geri tuşu beklentisi | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Spor marşları / taraftar şarkıları 2) Hip-hop / rap (temiz versiyon) 3) Elektronik / EDM 4) Rock (enerjik) 5) Pop (hareketli) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Semicenk, Edis, Ceza, Manga (Türkçe); Eminem, Imagine Dragons, AC/DC, Queen, The White Stripes (yabancı kolda) | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Pop **80-120** · Electro **90-130** · Dance **110-135** BPM (yalnız bu türler bankada tanımlı — EDM/pop/rock karşılıkları) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (spor marşı / hip-hop / rock)** | Bankada tür-BPM aralığı YOK → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM (şarkı bazlı)** | Yazılmadı — "'Till I Collapse = X BPM" gibi iddia doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yüksek tempo, motivasyon odaklı; hızlı ritim (kurgusal tercih) — "120-160" küme/eski notu `⚠️ VERIFICATION REQUIRED` | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 15:30 ödev (sessiz), 17:00 antrenman, 19:00 dönüş · Hafta sonu maç günü sabahı | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (aile hesabı — çocuk profili) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Minimal: antrenörün/babasının önerileri + "Buna benzer"; shuffle + crossfade alışkanlığı | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.2 (Enerjik — sık skip, playlist kuran) |
| **Premium olasılığı** | %0 (aile planı) — reklamsız deneyim ister, kendi talep etmez | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Spor marşları / taraftar şarkıları | Maç öncesi ritüeli; tribün sesleri onun için müziktir |
| 2 | Hip-hop / rap (temiz) | Ritmik yapı koşu temposuna uyar; "kazanma" temalı sözler |
| 3 | Elektronik / EDM | Isınma/soğuma için sözsüz, yüksek tempolu parçalar |
| 4 | Rock (enerjik) | Babasının klasik rock etkisi; maça giderken araba listesi |
| 5 | Pop (hareketli) | Ailece arabada dinlenen; annesinin tercihiyle ortak zemin |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 15:30-16:30 | Ödev | Sessiz (müzik yok) |
| Hafta içi | 17:00-19:00 | Antrenman / yüzme | Antrenman listesi (EDM/rap) |
| Hafta içi | 19:00-19:30 | Dönüş (araba) | Zafer/teselli listesi |
| Hafta içi | 20:00-20:30 | Babayla pas çalışması | Hafif arka plan pop/rock |
| Hafta içi | 20:30-21:00 | Serbest (FIFA) | Oyun içi müzik |
| Hafta sonu | 09:00 | Maç günü sabahı | "Maç Öncesi" playlist |
| Hafta sonu | 12:00-14:00 | Maç sonrası | Zafer listesi (rock/pop) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "120-160 BPM" aralığı bankada doğrulanmadı → kurgusal tercih olarak taşındı, doğrulanmış iddia sayılmaz (`⚠️ VERIFICATION REQUIRED`).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Evde fiber (kurgusal) / mobilde 4G + tünelde kopma (kurgu — offline senaryosu) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark (akşam antrenmanı dönüşü — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günlük toplam 60-90 dk (aile limiti; spor yoğunluğu nedeniyle düşük — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 5/10 — müzik ve mobil futbol oyunları için yeterli; sosyal medya yok (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPhone XR (annesinin eskisi), aile laptopu — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — 1792×828 dahil spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Normal, gözlük yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); hareket halinde hızlı okunabilirlik | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | Normal — yüksek sesle dinleme alışkanlığı (kurgu) | Ses aşırıya kaçsa da görsel geri bildirim tam olmalı; işitsel uyarı görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | Çok iyi motor beceri (sporcu refleksleri); terli parmak / hareket halinde kullanım (kurgu) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükleme yerine tek dokunuş (2.5.7) · büyük hedef | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | Spor dışı dikkat 10-15 dk; spor içeriğinde 30+ dk (kurgu) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 · tutarlı yardım 3.2.6 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Çocuk gelişim/okuma düzeyi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 44/48 px dokunma hedefi (küme notu) | [[mood-taxonomy]] §3.2.9 bu rakamı "⚠️ WCAG research-bank" diye anar; research-bank P5'te yalnız 2.5.8 ≥24×24 px vardır → `⚠️ VERIFICATION REQUIRED` |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Disiplinli ve hedef odaklıdır: antrenman çantasını kendisi hazırlar, geç kalmaz, "önce ödev sonra antrenman" kuralı yaşar.
- Rekabetçi: her şeyi yarışmaya çevirir; maç kaybını kişisel alır, analiz eder ("Nerede hata yaptım?").
- Takım oyuncusu: pas vermeyi bilir, kaptandır; onay kaynağı antrenör ve takım arkadaşlarıdır.
- Tek odaklı: müziği "antrenman aracı" olarak görür; keyif için değil iş için dinler.
- Hızlı karar verir: sık skip eder, uzun akış beklemez (Enerjik ikincil küme).

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Spor odaklı (krampon, forma) — aile onaylı, lüks yok | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Düşük: yükleme spinner'ına ~5 sn dayanır; antrenman öncesi gecikme kabul edilemez | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sinirlenir, uygulamayı kapatır; teknik hata metni görürse yetişkine sorar — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 0-1/10 — antrenman öncesi reklam kesin reddedilir; ritmi bozar | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + video (maç özetleri/beceri videoları); deneme-yanılma | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Takım içinde paylaşır (ortak playlist talebi); sahne/sunum onu germez ama spor dışı konu onu sıkar | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:30 sonrası dikkat düşer; karmaşık akışlar reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Hazır "Maç Öncesi" listesi + hızlı başlatma + öngörülebilir kontroller keşfi başlatır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.9 (workout kontrolü) + §3.2.2 (hızlı skip) |

*Erişilebilirlik etkisi (özet):* hareket halinde tek el + gelişim çağı → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme yerine tek dokunuş (2.5.7), odak görünür (2.4.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Efe Demir'sin. 7 yaşında, İzmir/Karşıyaka-Bostanlı'da yaşıyorsun, Özel Karşıyaka İlkokulu 2. sınıf öğrencisisin.
Sporcu ataması var (CoreMusic SSOT): hayatın futbol etrafında dönüyor — antrenman öncesi motivasyon listesi senin ritüelin.
İkincil olarak Enerjik'sin: sık sık atlar (skip), listeleri kendin kurarsın, hızlı geri bildirim beklersin.
Müzik zevkin: spor marşları, temiz hip-hop/rap, EDM, enerjik rock, hareketli pop. En sevdiklerin: Semicenk, Ceza, Manga; Eminem, Imagine Dragons, AC/DC, Queen.
Samsung Galaxy A34 5G kullanıyorsun (aile hesabı), dark temada; mobilde 4G var ama tünelde kopar — offline mod hayati.
Gözlük/işitme kısıtın yok; en büyük kısıtın hareket halinde tek elle, terli parmakla kullanım.
Sabır eşiğin düşüktür (spinner'a ~5 sn); reklam antrenman öncesi kabul edilemez; satın alma tamamen aile kararındadır.
Test edilecek akışlar: workout/antrenman modu, hızlı skip navigasyon, playlist sıralama (ısınma-ansoğuma), offline (tünel/4G kopması), tek elle kullanım, kısa arama + yazım hatası, karanlık tema, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Müziği keyif için değil spor için dinlersin — öneri listesi hızlı ve hazır olmalı.
- "Imagne Dragons" gibi yanlış yazarsın — arama affedici olmalı.
- Bağlantı koparsa cache'ten devam etmelisin; duraksama = uygulamayı kapatırsın.
- Sosyal özellikleri sınırlı kullanırsın (paylaşma, profil özelleştirme gereksiz).
- Maç kaybedince ruh halin değişir; sakin/teselli listesi görünür olmalı.

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
| 1 | Ana sayfa yükleme (çocuk profili) | CoreMusic'i aç | LCP ≤ **2500 ms**, sade düzen, CLS ≤ **0.1**, splash yok | Performance trace + screenshot | LCP ≤2500 ms · CLS ≤0.1 (P8.1) | 1.4.3 |
| 2 | Workout / antrenman modu | "Maç Öncesi" listesini başlat | Tek dokunuşla kontrol; hedefler ≥24×24 px; INP ≤ **200 ms** | DOM assertion + a11y audit | INP ≤200 ms (P8.1) | 2.5.8 |
| 3 | Hızlı skip navigasyon (Enerjik) | Ard arda 5 atla | INP ≤ **200 ms**; anlık geri bildirim; geri tuşu kaybolmaz | Performance trace + DOM assertion (ADR-023 satır 12) | INP ≤200 ms | 2.4.11 |
| 4 | Basit arama + yazım hatası | "Imagne Dragons" yaz | Affedici sonuç ("Imagine Dragons"), ilk sonuç anlamlı | Manuel + DOM assertion (ADR-023 satır 11/18) | INP ≤200 ms | 3.3.7 |
| 5 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve sanatçı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake = 0 | - |
| 6 | Şarkı çalma (play / duraklat) | İlk parçayı başlat | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür | Trace + console log + a11y audit | FCP 1 sn (P8.1) | 2.4.7 |
| 7 | Playlist sıralama (ısınma-ansoğuma) | Sıralamayı değiştir | Çalan parça durmaz; liste düzeni CLS ≤ **0.1** | Manuel + trace + DOM assertion | CLS ≤0.1 | - |
| 8 | SPA navigasyon (sayfalar arası) | Sayfalar arasında gezin | Müzik kesintisiz; yumuşak geçiş; CLS ≤ **0.1** | Trace + console log | LCP ≤2500 ms · CLS ≤0.1 | - |
| 9 | Favori (kalp) ekleme | Beğendiği parçayı kaydet | Görsel geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤200 ms | 2.5.8 |
| 10 | Karanlık tema (akşam antrenmanı) | Temayı aç/kapat | Tema değişimi müziği kesmez; kontrast ≥ **4.5:1** | Manuel + ekran görüntüsü + kontrast denetimi | kontrast 4.5:1 (P5.4) | 1.4.3 |
| 11 | Bağlantı yavaşlatma (4G) | Ağ throttling uygula | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) | 150 ms · 1.6/0.75 Mbps (P8.3) | - |
| 12 | Hata sayfaları (tünelde kopma) | Bağlantıyı kes | Türkçe, sakin, kısa metin; cache'ten devam + büyük "Tekrar Dene" | Manuel + screenshot | INP ≤200 ms | 2.5.8 |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe çalıştır | 1.4.3 / 1.4.11 / 2.4.7 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | Lighthouse A11y (P8.6) | 1.4.11 |
| 14 | Kırılma noktası kontrolü | Viewport değiştir | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzen bozulmaz | Playwright `setViewportSize` + screenshot | 360×780 viewport (`⚠️ DERIVED`) | 1.4.13 |
| 15 | Tek elle kullanım (alt yarı erişim) | Sadece alt bölgeyle gez | Temel kontroller alt yarıda; hedefler ≥24×24 px | Manuel + DOM measurement | 24×24 px (P5.4) | 2.5.7 |
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
| 1 | Ad, semt, okul, sınıf, aile detayları (Ayşe/Ali/Zeynep/Şimşek) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (7) ve atama | Kurgusal (index ataması; eski dosyayla çakışma yok) | [[personas/index]] §6.3.2 | — | `Kaynak: kurgusal (persona verisi; index §6.3.2 ataması)` |
| 3 | `veli_onayı_gerekli: true` (politika sonucu) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları (boy, kilo, saç/göz, gözlük yok) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (E80 · A70 · C70 · N60 · O60) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon harfleri ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Sporcu) | Vault referanslı atama | [[personas/mood-taxonomy]] §3.2.9 | [[personas/index]] §6.3.2 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntıları (Sporcu §3.2.9, Enerjik §3.2.2) | Vault verisi | [[personas/mood-taxonomy]] §3.2.9 + §3.2.2 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Eski mood (Sporcu birincil / Enerjik ikincil) | Çelişki YOK | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Aynı — §7.2'de "çakışma yok" kaydı |
| 14 | Küme eğilimi "yüksek O" ↔ persona O=60 | Vault içi kısmi çelişki | [[personas/mood-taxonomy]] §3.2.9 | Big Five tablosu (bu dosya) | `⚠️ VERIFICATION REQUIRED` — kısmi çelişki; §7.2 |
| 15 | Küme BPM notu "120-160 ⚠️" (Enerjik) + eski "120-160" | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.2 | eski salt-okunur persona dosyası | `⚠️ VERIFICATION REQUIRED` — dosyada kullanılmadı |
| 16 | Tür BPM aralıkları (Pop 80-120 · Electro 90-130 · Dance 110-135) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 17 | Spor marşı / hip-hop / rock için tür-BPM aralığı | Kapsam dışı | research-bank P4.4 (bu türler listede yok) | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 18 | Şarkı bazlı BPM ("'Till I Collapse = X BPM") | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 19 | Favori sanatçı adları (Semicenk, Edis, Ceza, Manga; Eminem, Imagine Dragons, AC/DC, Queen, White Stripes) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | Eski cihaz (iPhone XR 1792×828, aile laptopu) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Tarayıcı sürümü (Safari/Chrome), internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı (enerjik kapak görselleri dahil) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | 44/48 px dokunma hedefi iddiası (küme notu) | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.9 ("⚠️ WCAG research-bank") | research-bank P5 (yalnız 24×24 var) | `⚠️ VERIFICATION REQUIRED` |
| 27 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 28 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Eski dosyadaki istatistik/teknik iddialar ("%70 spor temalı öneri", yüzde demografisi) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 31 | Eski yaş/mood değerleri (§7.2 çakışma kontrolü) | Çelişki YOK | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Aynı değerler — §7.2'de "çakışma yok" kaydı |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`efe-demir.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 18 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `antrenman 140 BPM` | Tür aralığı (Dance 110-135, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `iPhone XR 1792×828 viewport` (eski veri) | `yalnız A34 VERIFIED; diğer cihazlar ⚠️ VERIFICATION REQUIRED (X-1)` |
| `Sporcu kümesi = yüksek O, o zaman persona O=80` | `küme eğilimi ≠ persona puanı; puan kurgusal, çelişki §7.2/§3.5.11` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P1-P8 + [[personas/mood-taxonomy]] §3.2.9/§3.2.2 + [[personas/index]] §6.3.2 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Efe için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski SSOT çelişki unutulması | (bu dosyada yaş/mood çelişkisi yok) | §7.2'de "çakışma yok" kaydı tut |

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
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; spor marşı/hip-hop/rock kapsam dışı bırakıldı
- [ ] Mood küme adı `mood-taxonomy` §3.2.9 (Sporcu) + §3.2.2 (Enerjik) ile birebir; eski değer çelişkisi yok (§7.2)
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
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.9 Sporcu, §3.2.2 Enerjik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 7 | 7 | **Çakışma YOK** — index §6.3.2 ile aynı |
| Mood (birincil) | Sporcu | Sporcu | **Çakışma YOK** — index §6.3.2 ile aynı |
| Mood (ikincil) | Enerjik | Enerjik (korunur) | Çelişki yok — küme §3.2.2'de mevcut |
| Sınıf | 2. sınıf | 2. sınıf | Çakışma yok (yaş 7 ile hizalı) |
| Big Five (0-10) | E8 · A7 · C7 · N6 · O6 | E80 · A70 · C70 · N60 · O60 | Ölçek dönüştürmesi (0-10 → 0-100); normalize `⚠️ DERIVED` |
| Küme Big Five eğilimi | — | Persona O=60 ↔ küme "yüksek O" beklentisi | Eğilim ≠ puan; puan kurgusal — §3.5.3/§3.5.11 notu |
| Birincil cihaz | iPhone XR (annesinin eskisi) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Ekran çözünürlüğü | 1792×828 | 1080×2340 → viewport 360×780 (`⚠️ DERIVED`) | X-1: doğrulanmamış cihaz spec'i yazılmaz |
| BPM aralığı | 120–160 BPM (kişiye özel) | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı (yaş/mood çakışması YOK), Big Five 0-10 → 0-100 ölçeklendi, test tablosu 6 sütuna genişletildi, veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/erkek-cocuk/efe-demir
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
