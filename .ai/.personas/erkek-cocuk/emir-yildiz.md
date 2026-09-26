---
title: "CoreMusic - Persona: Emir Yıldız"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
group: erkek-cocuk
age: 6
mood: Kaşif
persona_id: CM-EY-06-KAS-ESK
veli_onayı_gerekli: true
---

# CoreMusic - Persona: Emir Yıldız

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **erkek-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 6 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Kaşif, §3.2.6), veri → [[research-bank]] (P1-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | erkek-cocuk (4-11) |
| Birincil mood | Kaşif ([[personas/index]] §6.3.2 ataması) |
| Test odağı | Offline mod (otobüs/antrenman yolu), düşük bant genişliği, ücretsiz sürüm + reklam toleransı, keşif/öneri kalitesi, sesli arama, veli onayı |
| Veli onayı | `veli_onayı_gerekli: true` (18 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çevrimdışı / indirilen içerik (otobüs yolculuğu) | Level 2 + 3 | İndirme akışı, uçuş modunda oynatma, kurtarma dili |
| Yavaş ağ / düşük bant genişliği | Level 2 + 3 | 150 ms · 1.6 Mbps koşulunda hata metni teknik olmamalı |
| Keşif & öneri (Kaşif) | Level 2 | ADR-023 satır 18 (uzun / karakter-sınırı / boş sorgu), tür çeşitliliği |
| Ücretsiz sürüm + reklam | Level 2 | Reklam kapatma ≥24×24 px, odak tuzağı yok |
| Sesli arama (Türkçe karakter) | Level 2 | "ş, ğ, ı, İ" sonuçları bozulmaz |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px, 2.4.7 odak denetimi |
| Veli onayı akışı | Level 1 + 2 | TMK 18 / COPPA 13 / GDPR 16 — onaysız kişiselleştirme yok |

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
| 3 | [[personas/mood-taxonomy]] §3.2.6 satır | Küme adı + tanım (Kaşif) |
| 4 | [[personas/index]] §6.3.2 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Emir Yıldız | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 6 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3.2) |
| **Doğum Tarihi** | 15 Şubat 2020 (SSOT yaş 6 ile hizalı — eski dosya "15 Şubat 2018" yazar, §7.2) | Kaynak: kurgusal (persona verisi; eski dosyadan taşındı, SSOT yaş ile hizalandı) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Eskişehir / Odunpazarı / Vişnelik | Kaynak: kurgusal (persona verisi) |
| **Okul / Sınıf** | Devlet İlkokulu, 1. sınıf (yaş 6 ile hizalı — eski dosya "3. sınıf" yazar, §7.2) | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Yaya (ev-okul yakın) + belediye otobüsü (antrenman, ~45 dk) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (aile hesabında çocuk profili — veli onaylı) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-EY-06-KAS-ESK` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `EY` | Ad + soyad ilk ASCII harfleri (Emir Yıldız → E, Y; ı→i katlandı) |
| `YY` | `06` | Yaş 2 hane (6 → `06`; SSOT index §6.3.2) |
| `ZZZ` | `KAS` | Mood tür kodu (Kaşif → KAS; [[mood-taxonomy]] §3.2.6) |
| `XXX` | `ESK` | Şehir kodu (Eskişehir → ESK) |

> **KVKK / yaş sınırı notu (zorunlu — 18 yaş altı):** Bu persona 6 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "18 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`). "Çocuk gelişim/okuma düzeyi" gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → kurgusal olması zorunludur (P6.5).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki uzun ayrıntı (kan grubu, burç, ten rengi detayı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 118 cm (yaşıtlarına göre normal bant — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 22 kg, atletik yapılı (minikler güreş antrenmanı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral kısa saç, kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok — görme ve işitme normal (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Eşofman takımı + temiz spor ayakkabı; sade tonlar → profil görseli abartısız | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Küçük el + antrenman yorgunluğu → dokunma hedefi kaçırma riski orta; yazmak yerine sesli giriş bekler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px): `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 60 | Antrenmanda dışa dönük, günlük hayatta gözcü ve sessizdir; "fazla öne çıkmama" refleksi vardır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 70 | Kardeşine bakar, aileye yardım eder; güreşte centilmendir, çatışmadan kaçınmaz ama kazanmayı sever. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 80 | Yaşına göre aşırı sorumlu: ödev zamanında, ev işlerine yardım, kardeş bakımı, antrenman disiplini. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = düşük kaygı; maddi sıkıntıyı şikayet etmeden taşır, yüzeyde sakindir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 50 | Meraklıdır ama sınırlı kaynakla keşfeder: "önce ne getireceğini bilmeliyim" — ortalama puan, sorgulayarak keşif. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). [[mood-taxonomy]] §3.2.6 "Deneyime Açıklık (A), orta O" der — taksonomi harfleri P7.3 ile çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §7.2'ye kayıtlıdır. |
| Mood eğilimi çelişkisi | [[mood-taxonomy]] §3.2.6 Kaşif eğilimi "çok yüksek Deneyime Açıklık" der; bu persona O=50 (orta) ile "çok yüksek" beklentisine uymaz → küme eğilimi ≠ persona puanı, §7.2 çakışma kaydı |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Kaşif |
| **İkincil küme** | Sporcu (§3.2.9 — eski dosyanın birincil kümesi ikincile taşındı, §7.2) |
| **Küme tanımı (alıntı)** | "Tüm türleri deneyen, yeni çıkanları ve deneysel müzikleri kovalayan keşif kullanıcısı" ([[personas/mood-taxonomy]] §3.2.6) |
| **Tetikleyici durumlar** | Yeni şarkı/tür keşfi (yaklaşma); antrenman öncesi hazırlık — Sporcu ikincil (yaklaşma); teknik hata metni ve beklenmedik ekran (kaçınma); zorla uzun form doldurma (duraklama) |
| **UI etkisi** | "Keşif odaklı widget'lar, öneri blokları" ([[personas/mood-taxonomy]] §3.2.6) — test etkisi: "Keşif algoritması, öneri kalitesi, tür çeşitliliği; ADR-023 satır 18 (müzik keşif — boş/uzun/karakter-sınırı sorgular)" |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3.2 (Kaşif); ikincil = Sporcu (§3.2.9) — eski dosyanın birincil "Sporcu" kümesi SSOT ile Kaşif oldu → §7.2'de çelişki kaydı |
| Test etkisi | ADR-023 satır 18 (keşif sorguları) + keşif algoritması/öneri kalitesi — [[personas/mood-taxonomy]] §3.2.6; ikincil Sporcu ile hızlı kontrol (§3.2.9) |
| BPM notu | Küme müziği "tüm aralıklar ⚠️" der (§3.2.6) — bu aralık **kategori düzeyinde, doğrulanmamış** → `⚠️ VERIFICATION REQUIRED`; dosyada yalnız research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Keşif algoritması (Kaşif) | Öneri blokları ilk ekranda; tür çeşitliliği görünür; filtre geri bildirimi anlık | DOM assertion + ekran görüntüsü |
| 2 | Uzun / karakter-sınırı arama sorgusu | ADR-023 satır 18: sınırda affedici sonuç, boş sonuçta yönlendirme | Manuel + DOM assertion |
| 3 | Offline oynatma (otobüs/antrenman yolu) | İndirilen parça uçuş modunda kesintisiz oynar; "çevrimdışı" rozeti görünür | Level 3 E2E (`offline: true`) |
| 4 | Hızlı kontrol beklentisi (Sporcu ikincil) | Oynat/atla INP ≤ **200 ms**; anlık geri bildirim | Performance trace (P8.1) |
| 5 | Reklam toleransı (ücretsiz sürüm) | Reklam kapatma ≥24×24 px; odak reklama hapsolmaz | a11y audit + DOM assertion |
| 6 | Veli onayı beklentisi | Onay olmadan profil/kişiselleştirme oluşmaz; dil dostane | E2E + manuel |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türkü / Halk Müziği 2) Rap / Hip-Hop (motivasyon) 3) Anadolu Rock 4) Arabesk (motivasyon) 5) Pop (hareketli) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Ceza, Ezhel, Cem Karaca, Ferdi Tayfur, Semicenk | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Pop **80-120** BPM (yalnız bu dört türden bankada tanımlı olan) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (türkü/rock/arabesk/rap)** | Bankada tür-BPM aralığı YOK → sayı yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsam dışı) |
| **BPM (şarkı bazlı)** | Yazılmadı — "şarkı = X BPM" gibi iddia doğrulanmadı; eski dosyanın "80-140 BPM" aralığı da bankada yok | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Orta tempo, sözü takip edebileceği motivasyon şarkıları (kurgusal tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:30 otobüs (antrenmana), 17:30-19:30 antrenman, 20:00 dönüş · Hafta sonu sabah kahvaltı, öğleden sonra sokak/mahalle | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (aile hesabı — çocuk profili, veli onaylı) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Kaşif: radyoda duyduğu şarkının adını satır satır arar, "Buna benzer" ile zincirleme keşfeder; antrenör önerilerini de dener | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.6 (Kaşif) |
| **Premium olasılığı** | %0 — aile bütçesi; reklamsız deneyimi ister, alamaz | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türkü / Halk Müziği | Dededen, babadan gelen; minder'de de türkü dinler — aile bağı |
| 2 | Rap (motivasyon) | Antrenman ve otobüs yolculuğu: "yapabilirsin" enerjisi |
| 3 | Anadolu Rock | Babasının kasetleri; "Tamirci Çırağı" hayat hikâyesine benzer |
| 4 | Arabesk (motivasyon) | Annesinin favorisi; "hüzünlü ama güçlü" — anne bağı |
| 5 | Pop (hareketli) | Okul sonrası, evde, kardeşle ortak dinleme |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 14:30 | Eve dönüş, ablanın yaptığı yemek | Radyo (arka plan pop) |
| Hafta içi | 15:00-16:00 | Ödev | Sessiz / hafif arka plan |
| Hafta içi | 16:30 | Otobüse binme (kulaklık) | Rap motivasyon |
| Hafta içi | 17:30-19:30 | Güreş antrenmanı | Antrenman listesi (türkü + rap) |
| Hafta içi | 20:00 | Otobüse dönüş | Anadolu rock / arabesk |
| Hafta içi | 21:00-21:30 | Kardeşle oyun, kitap | Sessiz |
| Hafta sonu | 10:00-12:00 | Mahalle / sokak | Pop (hareketli, ortak dinleme) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "80-140 BPM" kişiye özel aralığı bankada doğrulanmadı → kurgusal tercih olarak taşınmadı, doğrulanmış iddia sayılmaz (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Evde Wi-Fi (kurgusal) / mobil veri sınırlı — kotalı aile paketi (kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (varsayılan — yaşa uygun sadelik; kurgu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günlük ~45 dk (aile limiti; antrenman saati hariç — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — okuma-yazma gelişiyor; sesli giriş ve büyük ikonlar tercih (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | "Annesinin eski Samsung Galaxy J7" — model P3 kapsamında DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | Normal — gözlük yok (kurgu) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11); 6 yaş görsel ipucuna güvenir, renk tek başına yeterli değil | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | Normal (kurgu) | İşitsel uyarı görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | Küçük el; hızlı animasyon rahatsız edebilir (kurgu) | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) · yumuşak geçiş | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | Okuma-yazma gelişmekte; ilgi 10-15 dk (kurgu) | Erişilebilir kimlik doğrulama min. (3.3.8) — ebeveyn kilidi dostane · odak görünürlüğü 2.4.7 + 2.4.11 · tutarlı yardım 3.2.6 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Çocuk gelişim/okuma düzeyi" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 44/48 px dokunma hedefi (küme notu) | [[mood-taxonomy]] bu rakamı "⚠️ WCAG research-bank" diye anar; research-bank P5'te yalnız 2.5.8 ≥24×24 px vardır → `⚠️ VERIFICATION REQUIRED` |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Meraklı ama hesaplıdır: her şeyin nasıl çalıştığını sorgular, bilmediği şeyden korkmaz ama önce gözlemler.
- Azimlidir: antrenmanda pes etmez; "annemi çalıştırmayacağım" sözü iç motivasyonudur.
- Erken olgunlaşmıştır: markette iki kez düşünür, israftan nefret eder, gururludur — yardım isterken zorlanır.
- Sessiz gözlemcidir: arkadaşlarının yeni eşyalarını kıskanmaz, içinden "bir gün ben de…" der.
- İç motivasyonlu: babasının "gurur duydum" demesi ve annesinin gülümsemesi belirleyicidir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | İhtiyaç odaklı — güreş ayakkabısı için biriktirir; lüks tüketim yok | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | ~8 sn: yükleme spinner'ında bir kez dener, iki kez olursa uygulamayı bırakır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sessizleşir, bir kez dener; teknik hata metnini görürse annesine/babasına sorar — dostça dil şart | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 5/10 — parası olmadığı için tolere eder ama sevmez; uzunda bıkar | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Deneme-yanılma + video izleme (antrenman taktiği); yavaşlatıp tek tek çözer | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Ortak dinlemede sırayı paylaşır; ablası kontrol eder, o önerir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Antrenman sonrası (20:00+) dikkat düşer; yazmak yerine sesli giriş ister | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Offline kesintisizlik + öngörülebilir, sade arayüz keşfi başlatır | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] §3.2.6 (keşif odaklı widget'lar) |

*Erişilebilirlik etkisi (özet):* 6 yaş + gelişim çağı + yorgunluk → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), sürükleme yerine tek dokunuş (2.5.7), odak görünür (2.4.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Emir Yıldız'sin. 6 yaşında, Eskişehir Odunpazarı Vişnelik'te yaşıyorsun, Devlet İlkokulu 1. sınıf öğrencisisin.
Kaşif ataması var (CoreMusic SSOT): her şeyin nasıl çalıştığını sorgularsın, radyoda duyduğun şarkının adını satır satır ararsın, "Buna benzer" ile keşfe dalarsın.
İkincil olarak Sporcu'sun: minikler güreşinde antrenmanlara gidiyorsun, pes etmezsin, centilmence yarışırsın.
Müzik zevkin: türkü, motivasyon rap'i, Anadolu rock, arabesk, hareketli pop. En sevdiklerin: Ceza, Ezhel, Cem Karaca, Ferdi Tayfur, Semicenk.
Samsung Galaxy A34 5G kullanıyorsun (test referansı), light temada, evde Wi-Fi var ama mobil verin sınırlı.
Görme ve işitme kısıtın yok; küçük elin var — büyük dokunma hedefleri beklersin, yorgunken yazmak yerine sesli arama istersin.
Annene ve babana bağlısın: baban mevsimlik inşaatçı, annen hastanede temizlik görevlisi; "annemi çalıştırmayacağım" sözün var. Ablan Elif derslerinde çalıştırır, küçük kardeşin Yusuf'a bakarsın.
Satın alma tamamen aile kararındadır; premium'un yoktur, reklamları tolere edersin ama sevmezsin.
Test edilecek akışlar: offline mod (otobüs/antrenman), düşük bant genişliği, ücretsiz sürüm + reklam, keşif/öneri (uzun ve boş sorgu), sesli arama (Türkçe karakter), veli onayı, erişilebilirlik denetimi.
Davranış notları:
- Bağlantın koparsa panik değil, alternatif ararsın — hata dili sakin ve kısa olmalı.
- "Hans Zimer" gibi yanlış yazabilirsin ya da sesli ararsın; arama affedici olmalı.
- Uygulama kasma yaparsa bırakırsın; telefon paylaşımlı ve eski hissi vermemeli.
- Sosyal özellikleri hiç kullanmazsın (paylaşma, profil, arkadaş etkinliği gereksiz — veli onayı yok).
- Onaysız kişiselleştirme seni durdurur; akış veli onayıyla devam etmeli.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 6 yaş persona'sı karmaşık komut kabul etmez |
| Sürpriz yasak | Ani yüksek ses/şaşırtıcı animasyon yok; kilit ve hata ekranı dostane |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk profili) | CoreMusic'i aç | LCP ≤ **2500 ms**, sade düzen, CLS ≤ **0.1**, splash yok | Performance trace + screenshot | LCP ≤2500 ms · CLS ≤0.1 (P8.1) | 1.4.3 |
| 2 | Veli onayı akışı | Kaydı başlat | Onay istemi görünür; reddedince kişiselleştirme başlamaz | E2E (Playwright) + manuel | TMK 18 / COPPA 13 / GDPR 16 (P6.4) | 3.3.8 |
| 3 | Keşif / öneri bloğu | Öneri alanını aç | Tür çeşitliliği görünür; filtre geri bildirimi anlık | DOM assertion + screenshot | INP ≤200 ms (P8.1) | 2.5.8 |
| 4 | Uzun / sınır sorgu | Çok uzun şarkı adı yaz | ADR-023 satır 18: affedici sonuç, taşma yok | Manuel + DOM assertion (ADR-023 satır 18) | INP ≤200 ms | 3.3.7 |
| 5 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Menü ve sanatçı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake = 0 | - |
| 6 | Offline oynatma | Uçuş modunu aç | İndirilen parça kesintisiz oynar; çevrimdışı rozeti görünür | Playwright `use: { offline: true }` (P8.4) | offline success | 2.4.7 |
| 7 | Reklam kapatma (ücretsiz) | Reklam X'ine bas | Kapatma ≥**24×24 CSS px**; odak reklama hapsolmaz | a11y audit + DOM assertion | dokunma hedefi (P5.4) | 2.5.8 |
| 8 | Sesli arama | Mikrofon simgesine bas | Türkçe sonuç listesi; Latin/Türkçe karakterler bozulmaz | Manuel + DOM assertion | SR hata oranı | 2.5.7 |
| 9 | Hızlı skip navigasyon (Sporcu ikincil) | Ard arda 5 atla | INP ≤ **200 ms**; anlık geri bildirim; geri tuşu kaybolmaz | Performance trace + DOM assertion | INP ≤200 ms | 2.4.11 |
| 10 | Şarkı çalma (play / duraklat) | İlk parçayı başlat | İlk ses < 1 sn; büyük oynat kontrolü; odak görünür | Trace + console log + a11y audit | FCP 1 sn (P8.1) | 2.4.7 |
| 11 | Bağlantı yavaşlatma | Ağ throttling uygula | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) | 150 ms · 1.6/0.75 Mbps (P8.3) | - |
| 12 | Hata sayfaları (kopma) | Bağlantıyı kes | Türkçe, sakin, kısa metin; büyük "Tekrar Dene" hedefi | Manuel + screenshot | INP ≤200 ms | 2.5.8 |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe çalıştır | 1.4.3 / 1.4.11 / 2.4.7 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | Lighthouse A11y (P8.6) | 1.4.11 |
| 14 | Kırılma noktası kontrolü | Viewport değiştir | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzen bozulmaz | Playwright `setViewportSize` + screenshot | 360×780 viewport (`⚠️ DERIVED`) | 1.4.13 |
| 15 | Tutarlı yardım | Sayfalar arasında gez | Yardım erişimi her sayfada aynı yerde | DOM assertion | konum tutarlılığı | 3.2.6 |
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
| 1 | Ad, semt, okul, sınıf, ulaşım, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (6) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3.2 | — | `Kaynak: kurgusal (persona verisi; index §6.3.2 ataması)` |
| 3 | `veli_onayı_gerekli: true` (politika sonucu) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Doğum tarihi 15 Şubat 2020 (yaş 6 ile hizalı) | Kurgusal + hizalama | eski salt-okunur persona dosyası (15 Şubat 2018) | [[personas/index]] §6.3.2 | `⚠️ VERIFICATION REQUIRED` — §7.2'de çelişki |
| 6 | Fiziksel özet satırları (boy, kilo, giyim) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Big Five puanları (E60 · A70 · C80 · N30 · O50) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 8 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 9 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 10 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 11 | Boyut kodu eşlemesi (P7.3 tam adları ↔ mood-taxonomy harfleri) | Vault içi çelişki | research-bank P7.3/P7.5 | [[personas/mood-taxonomy]] §3.2.6 ("Deneyime Açıklık (A)") | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 12 | Mood küme adı (Kaşif) | Vault referanslı atama | [[personas/mood-taxonomy]] §3.2.6 | [[personas/index]] §6.3.2 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı ("Tüm türleri deneyen…") | Vault verisi | [[personas/mood-taxonomy]] §3.2.6 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 14 | Eski mood (Sporcu birincil / Sessiz ikincil) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3.2 | Karar: index kazanır (Kaşif); §7.2'ye kayıtlı |
| 15 | Küme eğilimi "çok yüksek Deneyime Açıklık" ↔ persona O=50 | Vault içi çelişki | [[personas/mood-taxonomy]] §3.2.6 | Big Five tablosu (bu dosya) | `⚠️ VERIFICATION REQUIRED` — eğilim ≠ puan; §7.2 |
| 16 | Küme BPM notu "tüm aralıklar ⚠️" | Doğrulanmadı | [[personas/mood-taxonomy]] §3.2.6 | — | `⚠️ VERIFICATION REQUIRED` — dosyada kullanılmadı |
| 17 | Tür BPM aralığı (Pop 80-120 BPM) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Türkü/rock/arabesk/rap için tür-BPM aralığı | Kapsam dışı | research-bank P4.4 (bu türler listede yok) | — | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 19 | Şarkı bazlı BPM; eski dosyanın "80-140 BPM" aralığı | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 20 | Favori sanatçı adları (Ceza, Ezhel, Cem Karaca, Ferdi Tayfur, Semicenk) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 21 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 22 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 23 | Eski cihaz ("annesinin eski Samsung Galaxy J7") | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 25 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 26 | Persona renk kombinasyonu kontrastı (light tema dahil) | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 27 | 44/48 px dokunma hedefi iddiası (küme notu) | Doğrulanmadı | [[personas/mood-taxonomy]] (⚠️ WCAG research-bank ibaresi) | research-bank P5 (yalnız 24×24 var) | `⚠️ VERIFICATION REQUIRED` |
| 28 | P5 kapsamında olmayan AA kriterleri (1.4.1, 1.4.4, 2.4.3, 3.3.1, 4.1.2…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Eski dosyadaki istatistik/kan grubu/burç iddiaları (aylık gelir ~25.000 TL dahil) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`emir-yildiz.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 18 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `türkü 90 BPM` | Tür aralığı (Pop 80-120, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Galaxy J7 720×1280 viewport` (eski veri) | `yalnız A34 VERIFIED; diğer cihazlar ⚠️ VERIFICATION REQUIRED (X-1)` |
| `Kaşif kümesi = çok yüksek O, o zaman persona O=80` | `küme eğilimi ≠ persona puanı; puan kurgusal, çelişki §7.2/§3.5.11` |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P1-P8 + [[personas/mood-taxonomy]] §3.2.6 + [[personas/index]] §6.3.2 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-ölçüsüz eşik uydurma | "Emir için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski SSOT çelişki unutulması | Yaş 8 / mood Sporcu taşıması | §7.2 çakışma satırını ekle; index kazanır |

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
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; türkü/rock/arabesk/rap kapsam dışı bırakıldı
- [ ] Mood küme adı `mood-taxonomy` §3.2.6 (Kaşif) ile birebir; eski "Sporcu" §7.2'de çelişki olarak kayıtlı
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
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.6 Kaşif) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 8 | 6 | [[personas/index]] §6.3.2 kazanır; doğum tarihi 2018→2020 hizalandı |
| Doğum Tarihi | 15 Şubat 2018 | 15 Şubat 2020 (yaş 6 ile tutarlı) | SSOT yaş kazanır; tarih kurgusal olarak hizalandı |
| Mood (birincil) | Sporcu | Kaşif | [[personas/index]] §6.3.2 + [[personas/mood-taxonomy]] §2.2 (Erkek Çocuk Kaşif ×2: Yusuf, Emir) kazanır |
| Mood (ikincil) | Sessiz | Sporcu (eski birincil ikincile taşındı) | Bilinçli karar: Sporcu test etkisi (hızlı kontrol) ikincil olarak korundu; §3.5.11'e kayıtlı |
| Sınıf | 3. sınıf (yaş 8 ile) | 1. sınıf (yaş 6 ile hizalı) | SSOT yaş kazanır; sınıf kurgusal olarak hizalandı |
| Birincil cihaz | "Annesinin eski Samsung Galaxy J7" | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Ekran çözünürlüğü | J7 spec'i (bankada yok) | 1080×2340 → viewport 360×780 (`⚠️ DERIVED`) | X-1: doğrulanmamış cihaz spec'i yazılmadı |
| BPM aralığı | 80-140 BPM (kişiye özel) | Yalnız tür aralıkları (P4.4) + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmadı |
| Müzik zevki × mood | Motivasyon/türkü zevki ↔ "Kaşif" ataması | İkisi de korunur (zevk kurgusal, mood SSOT) | Çelişki bilinçli: Kaşif test etkisi (keşif algoritması) uygulanır, zevk değiştirilmez; §3.5.11'e kayıtlı |
| Küme Big Five eğilimi | "çok yüksek Deneyime Açıklık" beklentisi | Persona O=50 (orta) | Eğilim ≠ puan; puan kurgusal — §3.5.3/§3.5.11 notu |
| Gelir/kan grubu/burç iddiaları | ~25.000 TL, A RH+, Kova | Yazılmadı (bankada yok) | X-10: doğrulanamaz iddia bu şablonda taşınmaz |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P1-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı, yaş 8→6 ve mood Sporcu→Kaşif index §6.3.2 ile düzeltildi (çelişkiler §7.2'de), test tablosu 6 sütuna genişletildi, veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/erkek-cocuk/emir-yildiz
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
