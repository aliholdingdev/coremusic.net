---
title: "CoreMusic — Persona: Pınar Deniz"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/kiz-cocuk/pinar-deniz"
updated: 2026-09-26
group: kiz-cocuk
age: 9
mood: Kaşif
persona_id: CM-PD-09-KAS-SAM
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Pınar Deniz

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[personas/research-bank]] · [[personas/mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **kiz-cocuk (4-11 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 9 yaşındaki bir çocuğun gözünden arayüzü deneyimlemek için kullanılır — odağı keşif odaklı widget'lar, hızlı navigasyon ve anlık geri bildirimdir.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[personas/mood-taxonomy]] (Kaşif / Enerjik), veri → [[personas/research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | kiz-cocuk (4-11) |
| Birincil mood | Kaşif ([[personas/index]] §6.3 ataması) |
| Test odağı | Keşif widget'ları + öneri kalitesi, hızlı navigasyon, anlık geri bildirim, sık skip döngüsü |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[personas/research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Çocuk modu ana sayfa / keşif | Level 2 + 3 | Kaşif: keşif widget'ları, tür çeşitliliği, öneri blokları |
| Keşif filtresi / öneri kalitesi | Level 2 + 3 | Kaşif: "keşif algoritması, öneri kalitesi, tür çeşitliliği" test etkisi |
| Hızlı navigasyon & skip döngüsü | Level 2 + 3 | Enerjik ikincil: anlık geri bildirim, INP ≤ 200 ms, skip performansı |
| Ebeveyn kilidi & veli onayı akışı | Level 2 | 3.3.8 erişilebilir kimlik doğrulama; sürpriz yok |
| Müzik kesintisizliği (SPA gezinme) | Level 3 | CLS ≤ 0.1, INP ≤ 200 ms eşikleri |
| Erişilebilirlik denetimi (kontrast / hedef boyu) | Level 2 | 2.5.8 ≥24×24 px, 1.4.3 ≥4.5:1, 1.4.11 ≥3:1 denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki ayrıntılı fiziksel veri bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); şarkı bazlı BPM yazılmaz (P4.7 / X-2).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[personas/research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[personas/mood-taxonomy]] | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Pınar Deniz | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 9 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — eski dosya "10" diyordu, §7.2) |
| **Doğum Tarihi** | 13 Temmuz 2017 | Kaynak: kurgusal (persona verisi; yaş 9 ile tutarlılık için türetildi — eski kayıt 13 Temmuz 2016 idi, §7.2) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Samsun / Atakum / Güzelyalı Mahallesi | Kaynak: kurgusal (persona verisi; şehir: eski salt-okunur kaynak) |
| **Okul / Meslek** | Samsun Atakum İlkokulu, 4. sınıf | Kaynak: kurgusal (persona verisi; sınıf yaş 9'a göre güncellendi — eski kayıt "ortaokul 5. sınıf" idi, §7.2) |
| **Kardeş** | 1 erkek kardeş (Arda — 7 yaş küçük, "minik taraftarı") | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Servis + yürüyüş (sahile yakın) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (ebeveyn hesabında alt profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-PD-09-KAS-SAM` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `PD` | Ad + soyad ilk harfleri (Pınar Deniz → P, D) |
| `YY` | `09` | Yaş 2 hane (9 → `09`) |
| `ZZZ` | `KAS` | Mood tür kodu (Kaşif → KAS) |
| `XXX` | `SAM` | Şehir kodu (Samsun → SAM) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 9 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek çocuk verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki ayrıntılı ölçüm/ürün verileri bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 134 cm (yaşıtlarına göre orta — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 30 kg, atletik yapılı (voleybol — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Açık kumral dalgalı uzun saç (genelde at kuyruğu), ela göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok; sahada hızlı bakışlı, refleksleri keskin | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Rahat spor giyim (eşofman/tişört); canlı renkler (turuncu, elektrik mavisi, neon yeşili); profil fotoğrafında hareketli, doğal poz (kontrast: canlı renk × buğday ten — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Hızlı ve rekabetçi; bekleme/yükleme ekranlarında hemen sıkılır → anlık geri bildirim + hızlı skip döngüsü şart | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 90 | Takımın ateşleyicisi: saha kenarında ve sınıfta arkadaşlarını coşturur, grup çalışmasını yönetir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Rekabetçi ama adil — kazanmak ister, fair-play'i de; takım arkadaşının hatasını söyler ama affeder. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 80 | Antrenman/uyku/beslenme programına uyar; antrenman defterini ve playlist'lerini düzenli günceller. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 30 = dengeli; maç kaybedince üzülür ama ~10 dk'da toparlanır — spor ona duygusal regülasyon öğretti. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 60 | Yeni antrenman metotlarını ve yeni türleri merak eder; keşfi haftada birkaç parçayla sınırlı tutar, sevdiklerine saplanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki 9/6/8/7/6 benzeri /10 skorlar 0-100 ölçeğe kurgusal olarak ölçeklendi (§7.2) |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness) → P7.5 kuralı ("kod esastır"); çelişki §3.5.11'e kayıtlıdır |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Kaşif |
| **İkincil küme** | Enerjik |
| **Küme tanımı (alıntı)** | "Tüm türleri deneyen, yeni çıkanları ve deneysel müzikleri kovalayan keşif kullanıcısı" ([[personas/mood-taxonomy]] §3.2.6) · ikincil: "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" (§3.2.2) |
| **Tetikleyici durumlar** | Keşif odaklı öneri widget'ı / yeni çıkan bloğu / tür filtresi (yaklaşma); yavaş yükleme + anlık geri bildirimsiz ekran (geri çekilme) |
| **UI etkisi** | Kaşif: "Keşif odaklı widget'lar, öneri blokları" + test etkisi "Keşif algoritması, öneri kalitesi, tür çeşitliliği" ([[personas/mood-taxonomy]] §3.2.6); Enerjik ikincilken "Canlı renkler, hızlı animasyonlar, yüksek kontrast" (§3.2.2), test etkisi "Hızlı navigasyon, anlık geri bildirim, skip performansı" (§3.2.2) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[personas/mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Kaşif); ikincil = eski salt-okunur persona kaynağı (Enerjik) — çelişki kaydı §7.2'de |
| Test etkisi | ADR-023 satır 18 (müzik keşif — boş/uzun/karakter-sınırı sorgular) + §3.2.2 hızlı navigasyon — [[personas/mood-taxonomy]] |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Keşif algoritması (Kaşif) | Öneri kalitesi + tür çeşitliliği görünür | Manuel + skor raporu |
| 2 | Keşif widget'ı / tür filtresi (Kaşif) | Öneri blokları dokunulabilir, 1.4.11 ≥3:1 | A11y audit + DOM |
| 3 | Hızlı navigasyon (Enerjik) | Anlık geri bildirim; INP ≤ **200 ms** | Performance trace |
| 4 | Sık skip döngüsü (Enerjik) | Skip akıcı, gecikme/görsel sıçrama yok (CLS ≤ **0.1**) | Trace + sayaç |
| 5 | Canlı renk / yüksek kontrast (Enerjik) | 1.4.11 ≥3:1 · 1.4.3 ≥4.5:1 | A11y audit |
| 6 | Geri tuşu beklentisi (ADR-023 satır 12) | Geri tuşu her adımda görünür, kaybolmaz (2.4.11) | DOM assertion |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Rock / Hard Rock 2) Enerjik Pop 3) EDM 4) Türkçe Rock 5) Hip-Hop | Kaynak: kurgusal (persona verisi; sıra: eski salt-okunur kaynak) |
| **Favori sanatçılar (5)** | AC/DC, Queen, Dua Lipa, Manga, Eminem | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2/P4.3 kapsamı dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | P4.4 listesinde Rock/EDM/Türkçe Rock türleri **YOK** → o türler için iddia yazılmadı; Enerjik Pop için **80-120**, Hip-Hop için **80-130** | Pop + Hip Hop satırları: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (P4.4 `VERIFIED (ikincil)`) · Rock/EDM/Türkçe Rock: `⚠️ VERIFICATION REQUIRED` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 120-160 BPM (yüksek enerji — eski dosya tercihi, kurgu) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:30-17:00 (antrenman öncesi ısınma), 20:30-21:00 (ödev arka plan) · Hafta sonu 09:30-11:00 (antrenman/saha), 21:00+ (aile) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (çocuk modu) + aile ortamında radyo/TV (ebeveyn kontrolü) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Aktif: haftada 6-8 yeni parça; hızlı skip, beğendiği an playlist'e alır; antrenman playlist'leri (5 adet) ayrı ve dokunulmaz | Kaynak: kurgusal (persona verisi; mood: §3.2.6 + §3.2.2) |
| **Premium olasılığı** | %55 (keşif + antrenman kullanımı yoğun; ailece karar) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Rock / Hard Rock | Maç öncesi adrenalin ritüeli: kulaklık, gözler kapalı, maçı zihninde canlandırma |
| 2 | Enerjik Pop | Isınma turunun tempoya uyan, herkesin bildiği tür |
| 3 | EDM | Maç sonrası kutlama — zıplama ve yüksek enerji |
| 4 | Türkçe Rock | Takımla paylaştığı, sözlerini bildiği tür |
| 5 | Hip-Hop | Antrenmanda tempo ve motivasyon |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:00-07:30 | Kahvaltı + hazırlanma | Enerjik Pop |
| Hafta içi | 16:30-17:00 | Antrenman öncesi ısınma | Rock / Hard Rock |
| Hafta içi | 20:30-21:00 | Ödev (arka plan) | Türkçe Rock |
| Hafta içi | 21:00-21:30 | Uyku öncesi (ses kısık) | Tanıdık pop listesi |
| Hafta sonu | 09:30-11:00 | Antrenman / maç | Maç sonrası EDM |
| Hafta sonu | 20:00-21:00 | Aile zamanı | Karışık (aile playlist'i) |

*Not: BPM satırları P4.4 kapsamındaki Pop (80-120) ve Hip-Hop (80-130) için tür aralığı düzeyindedir; Rock/EDM/Türkçe Rock için bankada tür-BPM verisi YOK → yazılmadı. "Şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2); eski dosyadaki şarkı listesi de bu dosyaya taşınmadı (§7.2).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 60 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (canlı renkli görünüm tercihi — mood §3.2.2) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 75 dk/gün, hafta sonu 120 dk/gün (ebeveyn limiti) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — okur, arar, playlist kurar, paylaşır | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | iPhone 11, AirPods Pro, Apple Watch SE (antrenman takibi) — spec/marka doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); hızlı bakış, canlı renklere alışkın | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — canlı renkli temada bu eşikler özellikle kritik | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; antrenmanda kulaklıkla dinler | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | iyi; hızlı ve atak dokunur | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | 9 yaş; 15-25 dk odak (antrenman temposu) | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Sakatlık/antrenman yükü gibi ifadeler 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Takım kaptanı: sahada ve okulda yönlendirir; "haydi" çeken, sıraya dizendir.
- Rekabetçi ama adil: maç kaybedince üzülür, ~10 dk'da toparlanır; spor ona duygusal regülasyon öğretmiştir.
- Müzik = performans ritüeli: maç öncesi kulaklık, gözler kapalı, maçı zihninde canlandırır — en iyi smaçlarını hep yüksek tempolu parçalarla yapar.
- Keşif + sadakat dengesi: yeni türleri ve yeni çıkanları dener (Kaşif), ama antrenman playlist'ine kimseyi sokmaz (Enerjik rutini).
- Aile bağları: annesi beden eğitimi öğretmeni (programı birlikte kurar), babası balıkçı (deniz ve disiplin), küçük kardeşi Arda'nın "minik taraftarıdır".

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Ebeveyn onaylı — çocuk "ister", anne/baba karar verir | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 3 sn — en sabırsızlardan; yükleme uzarsa skip'ler, mümkünse uygulamadan çıkar | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Hata görürse nedenini ister; "Tekrar Dene" + hızlı dönüş beklentisi | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 2/10 — akışı bozduğu için hemen geçer, antrenman arasında dayanamaz | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Yaparak + görerek; anlatılanı hemen sahada ve uygulamada dener | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Takımı yönlendirir; playlist sırasını arkadaşına sorar ama son söz her zaman onundur | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 21:00 sonrası evde ses kısılır; kulaklıkla yalnızca tanıdık, ezber listeyi dinler | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Keşif odaklı öneri + hızlı skip döngüsü + canlı görünüm → keşif davranışı tetiklenir | Kaynak: kurgusal (persona verisi); mood: [[personas/mood-taxonomy]] Kaşif birincil + Enerjik ikincil küme |

*Erişilebilirlik etkisi (özet):* hızlı akış + canlı görünüm → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3) / ≥ **3:1** (1.4.11), sürükleme yerine tek dokunuş (2.5.7) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Pınar Deniz'sin. 9 yaşında, Samsun/Atakum'da yaşıyorsun, Samsun Atakum İlkokulu 4. sınıf öğrencisisin.
Birincil mood'un Kaşif, ikincil olarak Enerjik'sin: yeni türleri ve yeni çıkanları keşfetmeyi seversin, hızlı yükleme ve anlık geri bildirim beklersin; sık şarkı değiştirir (skip) ama beğendiğin listeye sadıksın.
Voleybol oynuyorsun, okul takımının kaptanısın; annen beden eğitimi öğretmeni, baban balıkçı, küçük kardeşin Arda senin minik taraftarın. Hayalin Filenin Sultanları'nda milli formayı giymek.
Müzik zevkin: Rock/Hard Rock, enerjik pop, EDM, Türkçe rock, hip-hop. En sevdiklerin: AC/DC, Queen, Dua Lipa, Manga, Eminem. Tempo tercihin 120-160 BPM (yüksek enerji, kurgusal).
Samsung Galaxy A34 5G kullanıyorsun, light ve canlı renkli temadasın, internetin evde 60 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; büyük dokunma hedefleri ve anlık tepi beklersin.
Sabır eşiğin 3 sn; satın alma tamamen ailenin onayına bağlıdır.
Test edilecek akışlar: keşif ana sayfası, öneri/keşif widget'ları, tür filtresi, hızlı navigasyon, sık skip + anlık geri bildirim, antrenman playlist'i, canlı renk kontrastı, ebeveyn kilidi, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Yeni çıkan bloğunu hemen dersin; öneri algoritması zayıfsa uygulamadan soğursun.
- Yükleme uzarsa şarkıyı atlar, sabırsızlanır, mümkünse uygulamayı kapatırsın.
- Takım arkadaşlarınla playlist paylaşırsın; sıralamayı değiştirmeyi seversin.
- Maç öncesi ritüelin var: kulaklık, gözler kapalı, yüksek tempolu ve tanıdık parçalar.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler — 9 yaş "karmaşık komut" yerine net davranış ister |
| Hız/geri bildirim | Enerjik ikincil küme nedeniyle prompt'ta "anlık geri bildirim" ve "hızlı navigasyon" beklentisi sabitlenir; yavaşlık senaryosu yalnız testte zorlanır |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Eylem | Beklenen | Doğrulama | Metrik | WCAG |
|---|------|-------|----------|-----------|--------|------|
| 1 | Ana sayfa yükleme (çocuk modu) | CoreMusic'i aç, keşif bloklarını bekle | Öneri blokları üstte, canlı ama düzenli yerleşim | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** | 1.4.11 ≥3:1 |
| 2 | Keşif widget'ı + tür filtresi | Öneri bloğunu aç, tür filtresini değiştir | Tür çeşitliliği görünür; kaliteli öneriler | Manuel + DOM assertion | — | 1.4.11 ≥3:1 |
| 3 | Hızlı navigasyon (Enerjik) | 5 bölümü hızlı art arda gez | Anlık geri bildirim; takılma/gecikme yok | Performance trace + console | INP ≤ **200 ms** | — |
| 4 | Şarkı çalma (play / duraklat) | Çal, duraklat, kaldığı yerden devam | İlk ses < 1 sn; büyük oynat kontrolü | Trace + console log | ilk ses < **1 sn** | 2.4.7 |
| 5 | Sık skip döngüsü (Enerjik) | 8 şarkıyı art arda atla | Skip akıcı; anlık görsel geri bildirim | Performance trace + sayaç | INP ≤ **200 ms** · CLS ≤ **0.1** | — |
| 6 | Geri navigasyon | 5 adımı geri geri gez | Geri tuşu kaybolmaz; odak korunur | DOM assertion + klavye testi | — | 2.4.11 |
| 7 | Favori / playlist ekleme | Kalbe dokun, playlist'e ekle, uygulamayı kapat-aç | Görsel + sesli geri bildirim; durum kalıcı | DOM assertion + LocalStorage kontrolü | — | 2.5.8 ≥24×24 px |
| 8 | Sürpriz kontrolü (popup) | Oturumda 5 dk gez | Onaysız sürpriz popup/açılır pencere yok | Manuel denetim + konsol | — | 3.3.8 |
| 9 | Ebeveyn kilidi (ayar geçişi) | Çocuk modu dışına çıkış dene | Kilit devrede; 3.3.8 gözetilir | Manuel + form assertion | — | 3.3.8 |
| 10 | SPA navigasyon (sayfalar arası) | Kategori değiştir | Müzik kesintisiz; çakışma yok | Trace + console log | CLS ≤ **0.1** | — |
| 11 | Bağlantı yavaşlatma | Slow 4G koşulunda ana sayfa + çalma | Hata yok; yaşa uygun yükleme animasyonu | Network throttling + trace | **150 ms / 1.6 Mbps / 750 Kbps** (P8.3) | — |
| 12 | Hata sayfaları (404 / kopma) | Bozuk URL aç | Türkçe, güven veren metin; büyük "Tekrar Dene" | Manuel + screenshot | — | 2.5.8 ≥24×24 px |
| 13 | Erişilebilirlik denetimi | Lighthouse + axe koş | 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | A11y skoru raporlanır | 1.4.3 ≥4.5:1 |
| 14 | Kırılma noktası kontrolü | `360 × 780` ve `412 × 915` görünümlerini aç | Düzen bozulmaz; bloklar taşmaz | Playwright `setViewportSize` + screenshot | — | 1.4.11 ≥3:1 |
| 15 | Türkçe karakter içeriği | ç/ğ/ı/İ/ö/ş/ü içeren menü ve şarkı adlarını aç | Karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | mojibake **0** | — |
| 16 | Odak sırası (klavye/gezinme) | Klavye ile 10 adımı gez | Odak mantıklı ve görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | — | 2.4.7 |

*Asgari 10 satır (şablon §3.5.10) — 16 satır yazıldı. Zorunlu sütunlar `Adım` · `Beklenen` · `Doğrulama` mevcut; görev gereği `Eylem` · `Metrik` · `WCAG` sütunları genişletilmiştir. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

**Ek doğrulama notları (test koşusundan önce — research-bank P8 `VERIFIED`):**

- **TBT:** > 50 ms ise uzun görev (long task) main thread'i bloklar; TBT, INP'nin laboratuvar proxy'sidir. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/optimize-vitals-lighthouse — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Test kırılım noktaları (P8.5 — 9 adet):** 320 × 568 · 375 × 667 · 375 × 812 · 414 × 896 · 768 × 1024 · 1280 × 720 · 1600 × 1200 · 1920 × 1080 · 2560 × 1440 — persona viewport'u (`360 × 780` / `412 × 915`, `⚠️ DERIVED` — §3) bu setle çapraz kontrol edilir. `Kaynak: [k1] + [k2]` (playwright.dev/docs/emulation + mintlify mobile-emulation — research-bank P8.5 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; Test Adımları tablosundaki eşikler research-bank P8'den alınır, persona'ya özel metrik hedefi bu dosyaya yazılmaz. `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, kardeş, ulaşım, aile meslekleri | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (9) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi + eski /10 skorların ölçeklenmesi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 ↔ mood-taxonomy §3.2.2 "O/A") | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Kaşif / Enerjik) | Vault referanslı atama | [[personas/mood-taxonomy]] §3 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[personas/mood-taxonomy]] §3.2.6 + §3.2.2 | — | `Kaynak: [[personas/mood-taxonomy]] (vault verisi)` |
| 13 | Pop tür BPM aralığı (80-120) + Hip-Hop (80-130) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 14 | Rock / EDM / Türkçe Rock tür-BPM aralıkları | Doğrulanmadı (bankada yok) | research-bank P4.4 (bu türler listede değil) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 15 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 16 | Favori sanatçı adları (AC/DC, Queen, Dua Lipa, Manga, Eminem) | Persona tercihi (kurgusal); sanatçı realitesi P4.2/P4.3 dışında | research-bank P4.2/P4.3 (bu isimler listede YOK) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 17 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 18 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 19 | Diğer cihazlar (iPhone 11, AirPods Pro, Apple Watch SE) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 20 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 22 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 23 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 24 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 25 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Eski dosyadaki kişisel ölçüm/ürün değerleri (persentil, ayakkabı no, kan grubu, burç, aile geliri, marka, şarkı listesi) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 28 | Eski cihaz/okul/yaş/mood değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |

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
| 4 | Zero Hallucination (ADR-005) | Her satır §4.5 etiketi taşır; bankada olmayan iddia yazılmaz | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`pinar-deniz.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Rock şarkısı 145 BPM` | Bankada tür-BPM yok → `⚠️ VERIFICATION REQUIRED`; Pop 80-120 · Hip-Hop 80-130 (VERIFIED ikincil) |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[personas/research-bank]] P3-P8 + [[personas/mood-taxonomy]] §3 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/documentation/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `coremusic.net.old/.ai/personas/kiz-cocuk/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
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
| Persona-özel eşik uydurma | "Pınar için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] Test Adımları tablosu ≥ 10 satır; zorunlu sütunlar `Adım` · `Beklenen` · `Doğrulama` mevcut (bu dosya `# / Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG` genişletilmiş biçimini kullanır)
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
| Test adımı | 16 (asgari 10) |
| Kaynak & Doğrulama satırı | 28 |
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
| `[[personas/research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[personas/mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.6, §3.2.2) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 10 | 9 | [[personas/index]] §6.3 kazanır |
| Mood | Enerjik (birincil) | Kaşif (birincil) / Enerjik (ikincil) | [[personas/index]] + [[personas/mood-taxonomy]] adları |
| Doğum tarihi | 13 Temmuz 2016 | 13 Temmuz 2017 | Yaş 9 ile tutarlılık için kurgusal türetme |
| Sınıf / okul | 5. sınıf (Samsun Atakum Ortaokulu) | İlkokul 4. sınıf (Samsun Atakum İlkokulu) | Yaş 9'a uygun kurgusal güncelleme |
| Kardeş yaşı | Arda (3) | Arda (2 — 7 yaş küçük) | Yaş uyumu için kurgusal güncelleme |
| Birincil cihaz | iPhone 11 (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[personas/research-bank]] P3 |
| Kulaklık / saat (marka/spec) | AirPods Pro + Apple Watch SE detayları | yazılmadı | P3.3 / X-1 → `⚠️ VERIFICATION REQUIRED` |
| BPM (120-160 tek satır) | "BPM: 120-160" (kaynaksız) | Kişisel tercih olarak kurgu + tür aralıkları P4.4 (yalnız Pop 80-120 · Hip-Hop 80-130) | Kurgu/gerçek ayrımı §3.5.5'te |
| Mood profili ↔ Big Five | Eski skorlar Enerjik profiline uygun (E 9/10, denge 7/10) | index Kaşif birincil; Kaşif'in "çok yüksek Deneyime Açıklık" beklentisi O=60 ile tam karşılanmıyor | Skorlar eski dosyadan kurgusal ölçeklendi (§7 satır 7 `⚠️ DERIVED`), değiştirilmedi — çelişki bilinçli saklandı |
| Enerjik küme harf kullanımı | mood-taxonomy §3.2.2 "Dışadönüklük (O), Açıklık (A)" | research-bank P7.3 (O=Openness, A=Agreeableness) | P7.5 "kod esastır"; §3.5.3 notu |
| Şarkı listesi ("Thunderstruck" vb. 5 şarkı) | Eski dosyada 5 satırlık şarkı listesi | yazılmadı | P4.7 / X-2 + şablon yasağı → `⚠️ VERIFICATION REQUIRED` |
| Banka dışı ölçüm/ürün | persentil, ayakkabı no, kan grubu, burç, aile geliri, marka | yazılmadı | research-bank P1-P8 kapsamı |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş (10→9) ve mood index §6.3'e göre düzeltildi (Enerjik→Kaşif birincil); veli_onayı + KVKK notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/kiz-cocuk/pinar-deniz
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
