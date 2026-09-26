---
title: "CoreMusic - Persona: Arda Şahin"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
group: erkek-cocuk
age: 7
mood: Sporcu
persona_id: CM-AS-07-SPR-ANT
veli_onayı_gerekli: true
---

# CoreMusic - Persona: Arda Şahin

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
| Test odağı | Tempo bazlı öneri, workout modu, hızlı kontrol, sessiz/odak dinleme akışı, ebeveyn onayı |
| Veli onayı | `veli_onayı_gerekli: true` (18 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Workout / antrenman modu | Level 2 + 3 | Hızlı kontrol, büyük dokunma hedefi, tempo bazlı öneri |
| Odak / ders modu (uzun oturum) | Level 2 + 3 | Döngü modu, kesintisiz akış, otomatik ilerleme |
| Arama (kısa sorgu + yazım hatası) | Level 2 | "Hans Zimer" gibi hatalı sorguda affedici sonuç (ADR-023 satır 11/18) |
| Hata ekranları & yavaş ağ | Level 2 | Düşük sabır eşiği değil — sakin, sessiz hata dilinin korunması |
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
| 3 | [[personas/mood-taxonomy]] §3.2.9 satır | Küme adı + tanım (Sporcu) |
| 4 | [[personas/index]] §6.3.2 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Arda Şahin | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 7 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3.2) |
| **Doğum Tarihi** | 17 Aralık 2018 (bu tarihte 7 tamamlanmadı → yaş 7 ile tutarlı) | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı, SSOT yaş ile hizalandı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Antalya / Muratpaşa / Lara | Kaynak: kurgusal (persona verisi) |
| **Okul / Sınıf** | Özel Antalya İlkokulu, 2. sınıf (yaş 7 ile hizalı — eski dosya "3. sınıf" yazar, §7.2) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Okul servisi + yaya (ev-okul yakın) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında çocuk profili) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-AS-07-SPR-ANT` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `AS` | Ad + soyad ilk ASCII harfleri (Arda Şahin → A, S; ş→s katlandı) |
| `YY` | `07` | Yaş 2 hane (7 → `07`; SSOT index §6.3.2) |
| `ZZZ` | `SPR` | Mood tür kodu (Sporcu → SPR; [[mood-taxonomy]] §3.2.9) |
| `XXX` | `ANT` | Şehir kodu (Antalya → ANT) |

> **KVKK / yaş sınırı notu (zorunlu — 18 yaş altı):** Bu persona 7 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "18 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). "Çocuk gelişim/okuma düzeyi" gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → kurgusal olması zorunludur (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki uzun ayrıntı (kan grubu, burç, ten rengi detayı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 128 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 27 kg, zayıf yapılı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Sarı düz saç, mavi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Miyop gözlük (-1.75) — sürekli takar; mavi çerçeve (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Sade spor kıyafet (gri/lacivert/beyaz); parlak renk reddi → profil görselinde sakin tonlar | Kaynak: kurgusal (persona verisi); renk eşleşmesi kontrastı `⚠️ DERIVED` (bkz. §3.5.7) |
| **UI'ı etkileyen fiziksel kısıt** | Gözlükle küçük font okuma zorlaşır; parmak hedefi kaçırma riski orta → büyük hedef + net odak bekler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px): `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 30 | Kalabalıktan ve yeni insanlardan rahatsız olur; sınıf parmak kaldırmaz, birkaç yakın arkadaşıyla yetinir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Çatışmadan kaçınır; uyumlu görünür ama bazen kendi isteklerini bastırır, "hayır" demekte zorlanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 80 | Çok düzenli: ödev zamanında, oda toplu, eşya kaybolmaz; mükemmeliyetçi eğilim. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 50 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 50 = orta; yüzeyde sakin, iç dünyasında kaygı yüksektir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 70 | Öğrenmeye ve yeni fikirlere açık — ama yeni sosyal deneyimlere değil; bireysel keşfi (kitap, satranç, Scratch) sever. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki harf eşlemesi bu tam adlarla çelişebilir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.2.9 Sporcu eğilimi "yüksek Sorumluluk, yüksek O" der; bu persona E=30 (düşük dışadönüklük) ile "yüksek E" beklentisine uymaz → küme eğilimi ≠ persona puanı, §7.2 çakışma kaydı |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Sporcu |
| **İkincil küme** | Meraklı (Kategori B, [[mood-taxonomy]] §3.3 satır 13) |
| **Küme tanımı (alıntı)** | "Antrenman odaklı; yüksek tempolu, motivasyon şarkısı ve workout playlist kullanır" ([[mood-taxonomy]] §3.2.9) |
| **Tetikleyici durumlar** | Yüzme/antrenman öncesi hazırlık (yaklaşma); odaklanma ihtiyacı — ders/satranç (yaklaşma); yüksek sesli/ani uyarı (kaçınma); zorla sosyalleştirme (duraklama) |
| **UI etkisi** | "Tempo bazlı öneri, workout modu, hızlı kontrol (dokunma hedefi ≥44/48px — ⚠️ WCAG research-bank)" ([[mood-taxonomy]] §3.2.9) — 44/48 px eşikleri research-bank P5'te AÇILMADI → `⚠️ VERIFICATION REQUIRED`; bankada tek güvenli asgari 2.5.8 ≥24×24 CSS px |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3.2 (Sporcu); ikincil = Meraklı (§3.3) — eski dosyanın birincil "Sessiz" kümesi SSOT ile değişti → §7.2'de çelişki kaydı |
| Test etkisi | ADR-023 satır 11 (basit arama) + tempo bazlı öneri / workout modu — [[mood-taxonomy]] §3.2.9 + §3.3 satır 13 |
| BPM notu | Küme müziği "130-180 BPM ⚠️" der (§3.2.9) — bu aralık **kategori düzeyinde, doğrulanmamış** → `⚠️ VERIFICATION REQUIRED`; dosyada yalnız research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Antrenman/çalışma modu (Sporcu) | Workout/odak modunda tek dokunuşla kontrol; büyük hedefler | DOM assertion + ekran görüntüsü |
| 2 | Tempo bazlı öneri | Öneri listesi yüksek enerji sıralaması sunar; filtre geri bildirimi görünür | Manuel + DOM assertion |
| 3 | Hızlı kontrol beklentisi | Oynat/atla INP ≤ **200 ms**; anlık geri bildirim | Performance trace (P8.1) |
| 4 | Basit arama + yazım hatası (Meraklı) | "Hans Zimer" → affedici sonuç, filtre açık (ADR-023 satır 11/18) | Manuel + DOM assertion |
| 5 | Sessiz odak beklentisi | Ani yüksek ses/animasyon yok; geçişler yumuşak | Manuel + screenshot |
| 6 | Geri tuşu beklentisi | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Klasik müzik 2) Film/dizi müzikleri (orkestral) 3) Lo-Fi / Chillhop 4) Ambient / sakin elektronik 5) Akustik pop (sakin) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Hans Zimmer, Ludovico Einaudi, Max Richter, Mozart, Ólafur Arnalds (yabancı); Pinhani, Can Bonomo, Kalben (Türkçe kolda) | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Pop **80-120** · Nu R&B **70-110** · Electro **90-130** · Dance **110-135** BPM (yalnız bu dört tür bankada tanımlı) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (klasik/ambient/lo-fi)** | Bankada tür-BPM aralığı YOK → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM (şarkı bazlı)** | Yazılmadı — "Interstellar Main Theme = X BPM" gibi iddia doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Düşük-orta tempo, sözsüz/enstrümantal ağırlık (kurgusal tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 08:30 servis, 16:00 ödev, 17:00 satranç, 21:30 uyku öncesi · Hafta sonu 10:00-12:00 çalışma, 20:00-21:00 sakin | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (aile hesabı — çocuk profili) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Algoritmik keşif: "Buna benzer" + "Haftalık Keşif"; beğenmediğini hemen atlar | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.3 satır 13 (Meraklı) |
| **Premium olasılığı** | %0 (aile planı) — reklamsız deneyimin değerini anlar | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Klasik müzik | Matematiksel yapısı hoşuna gider; satranç ve ders sırasında konsantrasyon sağlar |
| 2 | Film/dizi müzikleri (orkestral) | Sözsüz ve atmosferik; "Interstellar" ses dünyası onun uzay tutkusuyla eşleşir |
| 3 | Lo-Fi / Chillhop | Ödev arka planı: ritmik ama sözü dağıtmaz |
| 4 | Ambient / sakin elektronik | Uyku öncesi ve sakin anlar; uzay temalı parçalar |
| 5 | Akustik pop (sakin) | Annesinin arabada açtığı yumuşak gitar/piyano parçaları |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 08:30 | Okul servisi (kulaklık) | Lo-Fi / chillhop |
| Hafta içi | 16:00-17:00 | Ödev (1 saat) | Lo-Fi arka plan |
| Hafta içi | 17:00-18:00 | Satranç pratiği | Klasik / film müziği |
| Hafta içi | 19:30 | Babayla sessiz zaman | Hafif akustik |
| Hafta içi | 21:00-21:30 | Uyku öncesi | Ambient (uyku listesi) |
| Hafta sonu | 10:00-12:00 | Çalışma seansı (döngü modu) | Lo-Fi / klasik |
| Hafta sonu | 16:00 | Yüzme (antrenman) | Hareketli çalışma listesi (kurgusal) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "40-100 BPM" kişiye özel aralığı bankada doğrulanmadı → kurgusal tercih olarak taşındı, doğrulanmış iddia sayılmaz.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 100 Mbps fiber (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark (varsayılan tercih — göz yorgunluğu; kurgu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günlük toplam 90 dk (aile limiti; satranç süresi hariç — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 7/10 — Scratch kodlama, dosya yönetimi, araştırma; yaşına göre ileri (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPhone SE (2. nesil), HP Pavilion laptop, kablolu kulaklık — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Miyop — gözlük takar (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); küçük font büyütmeyi dener | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | Normal ama hassas — yüksek sesten rahatsız olur (kurgu) | Ani yüksek sesli geri bildirim yok; işitsel uyarı görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | İyi motor beceri; hızlı animasyon rahatsız edebilir (kurgu) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) · yumuşak geçiş | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | İlgi alanında 45-60 dk odak; ilgi alanı dışinda 5 dk (kurgu) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 · tutarlı yardım 3.2.6 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Çocuk gelişim/okuma düzeyi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 44/48 px dokunma hedefi (küme notu) | [[mood-taxonomy]] §3.2.9 bu rakamı "⚠️ WCAG research-bank" diye anar; research-bank P5'te yalnız 2.5.8 ≥24×24 px vardır → `⚠️ VERIFICATION REQUIRED` |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Sessiz ve derin odaklıdır: az konuşur, kelimelerini seçer; kalabalık ve yüksek ses onu yorar.
- Mükemmeliyetçidir: hata yapmak kaygısını artırır; düzen ve tekrar onu güvende tutar.
- Stratejik düşünür: satranç ve Scratch onun bireysel keşif alanıdır.
- Seçici sosyal: birkaç yakın arkadaşla derin bağ; yeni insanlarla tanışmak yorucudur.
- İç motivasyonlu: dış onay az, anne onayı belirleyicidir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Aile onaylı — kitap/satranç seti odaklı, lüks tüketim yok | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Sabırlıdır: yükleme spinner'ına ~15 sn dayanır; ama yavaşlık dikkatini dağıtır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sakin ama içine kapanır; teknik hata metni görürse yetişkine sorar — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 4/10 — konsantrasyonu bozmazsa görmezden gelir, uzunda bıkar | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel + okuma + deneme; YouTube'dan "nasıl yapılır" izleyerek kendi kendine öğrenir | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Ortak dinlemede sırayı paylaşır; sahne/sunum talebi onu strese sokar | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası dikkat düşer; karmaşık akışlar reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sade, öngörülebilir arayüz + tanıdık sessiz tasarım dili keşfi başlatır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.9 (workout/odak kontrolü) |

*Erişilebilirlik etkisi (özet):* gözlük + gelişim çağı → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme yerine tek dokunuş (2.5.7), odak görünür (2.4.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Arda Şahin'sin. 7 yaşında, Antalya/Muratpaşa-Lara'da yaşıyorsun, Özel Antalya İlkokulu 2. sınıf öğrencisisin.
Sporcu ataması var (CoreMusic SSOT): haftada 1 gün zorunlu yüzme var, antrenman öncesi/listesi hazırlarsın — ama asıl dünyan sessizlik ve odaktır.
İkincil olarak Meraklı'sın: satranç, uzay, kodlama (Scratch) hakkında sessizce araştırırsın; kısa sorularla arama yaparsın.
Müzik zevkin: klasik, film müzikleri, lo-fi, ambient, sakin akustik pop. En sevdiklerin: Hans Zimmer, Ludovico Einaudi, Max Richter, Mozart, Ólafur Arnalds.
Samsung Galaxy A34 5G kullanıyorsun, dark temada, internetin evde 100 Mbps (kurgusal); gözlük takıyorsun (miyop).
Görme dışında kısıtın yok; yüksek sesten ve ani animasyonlardan rahatsız olursun — sade, yumuşak, öngörülebilir arayüz beklersin.
Sabırlısın (spinner'a ~15 sn), ama yavaşlık dikkatini dağıtır; satın alma tamamen aile kararındadır.
Test edilecek akışlar: workout/antrenman modu, döngü modu (uzun oturum), kısa arama + yazım hatası, minimalist player, karanlık tema, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Yeni öneriyi önce listeyi inceleyerek dener; hızlı ve sessiz geri bildirim beklersin.
- "Hans Zimer" gibi yanlış yazarsın — arama affedici olmalı.
- Ekranda kalabalık/bağırış görürsen kapatırsın; hata dili sakin ve kısa olmalı.
- Sosyal özellikleri hiç kullanmazsın (paylaşma, profil, arkadaş etkinliği gereksiz).
- "Buna benzer" ve "Haftalık Keşif" ile keşfedersin; beğenmediğini hemen atlarsın.

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
| 2 | Workout / antrenman modu | Antrenman listesini başlat | Tek dokunuşla kontrol; hedefler ≥24×24 px; INP ≤ **200 ms** | DOM assertion + a11y audit | INP ≤200 ms (P8.1) | 2.5.8 |
| 3 | Döngü modu (uzun oturum) | "Ders" listesini döngüye al | 3 saatlik seans kesintisiz; şarkı geçişi sessiz; CLS ≤ **0.1** | Performance trace + console log | CLS ≤0.1 · TBT >50 ms kontrol (P8.6) | - |
| 4 | Basit arama + yazım hatası | "Hans Zimer" yaz | Affedici sonuç ("Hans Zimmer"), ilk sonuç anlamlı | Manuel + DOM assertion (ADR-023 satır 11/18) | INP ≤200 ms | 3.3.7 |
| 5 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve sanatçı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake = 0 | - |
| 6 | Şarkı çalma (play / duraklat) | İlk parçayı başlat | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür | Trace + console log + a11y audit | FCP 1 sn (P8.1) | 2.4.7 |
| 7 | Hızlı skip navigasyon | Ard arda 5 atla | INP ≤ **200 ms**; anlık geri bildirim; geri tuşu kaybolmaz | Performance trace + DOM assertion | INP ≤200 ms | 2.4.11 |
| 8 | SPA navigasyon (sayfalar arası) | Sayfalar arasında gezin | Müzik kesintisiz; yumuşak geçiş; CLS ≤ **0.1** | Trace + console log | LCP ≤2500 ms · CLS ≤0.1 | - |
| 9 | Favori (kalp) ekleme | Beğendiği parçayı kaydet | Görsel geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤200 ms | 2.5.8 |
| 10 | Karanlık tema (varsayılan) | Temayı aç/kapat | Tema değişimi müziği kesmez; kontrast ≥ **4.5:1** | Manuel + ekran görüntüsü + kontrast denetimi | kontrast 4.5:1 (P5.4) | 1.4.3 |
| 11 | Bağlantı yavaşlatma | Ağ throttling uygula | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) | 150 ms · 1.6/0.75 Mbps (P8.3) | - |
| 12 | Hata sayfaları (404 / kopma) | Bağlantıyı kes | Türkçe, sakin, kısa metin; büyük "Tekrar Dene" hedefi | Manuel + screenshot | INP ≤200 ms | 2.5.8 |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe çalıştır | 1.4.3 / 1.4.11 / 2.4.7 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | Lighthouse A11y (P8.6) | 1.4.11 |
| 14 | Kırılma noktası kontrolü | Viewport değiştir | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzen bozulmaz | Playwright `setViewportSize` + screenshot | 360×780 viewport (`⚠️ DERIVED`) | 1.4.13 |
| 15 | Çevrimdışı / yavaş ağ davranışı | CDP ile ağ kes | Yaşasına uygun yükleme animasyonu; teknik hata metni görünmez | CDP `Network.emulateNetworkConditions` + manuel | latency 400 ms sim. (P8.3) | - |
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
| 2 | Yaş (7) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3.2 | — | `Kaynak: kurgusal (persona verisi; index §6.3.2 ataması)` |
| 3 | `veli_onayı_gerekli: true` (politika sonucu) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları (boy, kilo, gözlük, saç/göz) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (E30 · A60 · C80 · N50 · O70) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon harfleri ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Sporcu) | Vault referanslı atama | [[personas/mood-taxonomy]] §3.2.9 | [[personas/index]] §6.3.2 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.2.9 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Eski mood (Sessiz birincil / Meraklı ikincil) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Karar: index kazanır (Sporcu); §7.2'ye kayıtlı |
| 14 | Küme eğilimi "yüksek E" ↔ persona E=30 | Vault içi çelişki | [[personas/mood-taxonomy]] §3.2.9 | Big Five tablosu (bu dosya) | `⚠️ VERIFICATION REQUIRED` — eğilim ≠ puan; §7.2 |
| 15 | Küme BPM notu "130-180 ⚠️" | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.9 | — | `⚠️ VERIFICATION REQUIRED` — dosyada kullanılmadı |
| 16 | Tür BPM aralıkları (Pop 80-120 · Nu R&B 70-110 · Electro 90-130 · Dance 110-135) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 17 | Klasik/ambient/lo-fi için tür-BPM aralığı | Kapsam dışı | research-bank P4.4 (bu türler listede yok) | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 18 | Şarkı bazlı BPM; eski dosyanın "40-100 BPM" aralığı | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 19 | Favori sanatçı adları (Hans Zimmer, Einaudi, Richter, Mozart, Arnalds; Pinhani, Can Bonomo, Kalben) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 21 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 22 | Eski cihazlar (iPhone SE, HP laptop) + 1334×750/1920×1080 çözünürlük | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Tarayıcı sürümü, internet hızı (100 Mbps), OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 24 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 25 | Persona renk kombinasyonu kontrastı (dark tema dahil) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 26 | 44/48 px dokunma hedefi iddiası (küme notu) | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.9 ("⚠️ WCAG research-bank") | research-bank P5 (yalnız 24×24 var) | `⚠️ VERIFICATION REQUIRED` |
| 27 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 28 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Eski dosyadaki istatistik/teknik iddialar (yüzdesel demografi, spec sayıları) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`arda-sahin.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 18 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `klasik 60 BPM` | Tür aralığı (Pop 80-120, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `iPhone SE 1334×750 viewport` (eski veri) | `yalnız A34 VERIFIED; diğer cihazlar ⚠️ VERIFICATION REQUIRED (X-1)` |
| `Sporcu kümesi = yüksek E, o zaman persona E=80` | `küme eğilimi ≠ persona puanı; puan kurgusal, çelişki §7.2/§3.5.11` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P1-P8 + [[personas/mood-taxonomy]] §3.2.9 + [[personas/index]] §6.3.2 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Arda için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski SSOT çelişki unutulması | Yaş 8 / mood Sessiz taşıması | §7.2 çakışma satırını ekle; index kazanır |

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
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; klasik/ambient kapsam dışı bırakıldı
- [ ] Mood küme adı `mood-taxonomy` §3.2.9 (Sporcu) ile birebir; eski "Sessiz" §7.2'de çelişki olarak kayıtlı
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
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.9 Sporcu) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 8 (doğum 17 Aralık 2018 — Aralık'ta 8 olacak) | 7 | [[personas/index]] §6.3.2 kazanır; doğum tarihi korunur ve 7 ile tutarlı |
| Mood (birincil) | Sessiz | Sporcu | [[personas/index]] §6.3.2 + [[personas/mood-taxonomy]] §2.2 (Erkek Çocuk Sporcu ×2: Efe, Arda) kazanır |
| Mood (ikincil) | Meraklı | Meraklı (korunur) | Çelişki yok — küme §3.3 satır 13'te mevcut |
| Sınıf | 3. sınıf (yaş 8 ile) | 2. sınıf (yaş 7 ile hizalı) | SSOT yaş kazanır; sınıf kurgusal olarak hizalandı |
| Birincil cihaz | iPhone SE (2. nesil) + HP Pavilion | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Ekran çözünürlüğü | 1334×750 / 1920×1080 | 1080×2340 → viewport 360×780 (`⚠️ DERIVED`) | X-1: doğrulanmamış cihaz spec'i yazılmaz |
| BPM aralığı | 40-100 BPM (kişiye özel) | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| Müzik zevki × mood | Sakin/enstrümantal zevk ↔ "Sporcu" ataması | İkisi de korunur (zevk kurgusal, mood SSOT) | Çelişki bilinçli: Sporcu test etkisi (workout modu) uygulanır, zevk değiştirilmez; §3.5.11'e kayıtlı |
| Küme Big Five eğilimi | "yüksek E" beklentisi | Persona E=30 | Eğilim ≠ puan; puan kurgusal — §3.5.3/§3.5.11 notu |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, yaş 8→7 ve mood Sessiz→Sporcu index §6.3.2 ile düzeltildi (çelişkiler §7.2'de), test tablosu 6 sütuna genişletildi, veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/erkek-cocuk/arda-sahin
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode