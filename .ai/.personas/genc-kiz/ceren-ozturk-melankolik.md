---
title: "CoreMusic — Persona: Ceren Öztürk"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-kiz/ceren-ozturk-melankolik"
updated: 2026-09-26
group: genc-kiz
age: 13
mood: Melankolik
persona_id: CM-CO-13-MEL-BAL
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Ceren Öztürk

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-kiz (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 13 yaşındaki bir genç kızın gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Melankolik / Yaratıcı), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-kiz (12-17) |
| Birincil mood | Melankolik ([[personas/index]] §6.3 ataması) |
| Test odağı | Karanlık tema kontrastı (1.4.3), keşif/öneri algoritması, düşük etkileşim (az animasyon), şarkı sözü okunabilirliği, gece akışı |
| Veli onayı | `veli_onayı_gerekli: true` (13 yaş — 16 yaş altı, §4.2) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Karanlık tema / dark-light kontrast (mood-taxonomy §3.2.3) | Level 2 + 3 | Melankolik arayüz; 1.4.3 ≥4.5:1 koyu temada |
| Keşif algoritması / öneri kalitesi (mood-taxonomy §3.2.3) | Level 2 + 3 | Derin dinleyici; az etkileşimle doğru öneri |
| Düşük etkileşim (az animasyon) (mood-taxonomy §3.2.3) | Level 2 | Sessiz, sakin arayüz; hareket azaltma desteği |
| Şarkı sözü okunabilirliği | Level 2 + 3 | Uzun metin satırlarında kontrast ve satır aralığı |
| Uzun gece oturumu | Level 2 + 3 | Uzun oturumda CLS/INP kararlılığı |
| Erişilebilirlik denetimi | Level 2 | 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px denetimi |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel detay bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.3 | Küme adı + tanım alıntısı (Melankolik) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Ceren Öztürk | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 13 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 22 Ocak 2013 | Kaynak: kurgusal (persona verisi; yaş 13 ile tutarlılık için türetildi) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Balıkesir / Altıeylül (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Altıeylül Ortaokulu, 8. sınıf | Kaynak: kurgusal (persona verisi; yaş 13 ile tutarlılık için türetildi — §7.2) |
| **Ulaşım** | Yürüme (ev-okul ~10 dk) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | ceren_note | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-CO-13-MEL-BAL` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `CO` | Ad + soyad ilk harfleri (Ceren Öztürk → C, Ö → `CO`) |
| `YY` | `13` | Yaş 2 hane (13 → `13`; şablon örnek aralığı 10-99'a uyar) |
| `ZZZ` | `MEL` | Mood tür kodu (Melankolik → MEL) |
| `XXX` | `BAL` | Şehir kodu (Balıkesir → BAL) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 13 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır); yaş 13 bu eşiğin altında olduğu için onay **gerekir**. Persona tamamen kurgusaldır; gerçek kişi verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (makyaj, parfüm, ayakkabı numarası…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 158 cm (ergenlik dönemi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 46 kg, ince yapı | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kumral saç, ela göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | İnce çerçeveli gözlük (miyopi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Sade/monokrom giyim; koyu tonlar; profil fotoğrafında düşük ışık (renk eşleşmesi `⚠️ DERIVED`, bkz. §3.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Gece uzun süreli şarkı sözü okuma → gözlüklü kullanıcıda göz yorgunluğu; koyu temada yüksek kontrast beklentisi | Kaynak: kurgusal (persona verisi); kontrast kriteri: `Kaynak: [k1] + [k2]` (P5.4) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 20 | Sessiz dinler; paylaşımdan çok okur ve düşünür. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 90 | Çatışmadan kaçar; grubun seçimine uyum sağlar, nazik ton bekler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 80 | Dinleme listelerini düzenli kurar; okul ve müzik rutini tutarlıdır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 60 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 60 = hüzünlü parçalarda duygusal derinlik; eleştiriye açık. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Klasikten soundtrack'e uzanan geniş yelpaze; deneysel akustik arar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Melankolik ([[mood-taxonomy]] §3.2.3) |
| **İkincil küme** | Yaratıcı ([[mood-taxonomy]] §3.2.10 — kurgusal atama, bkz. not) |
| **Küme tanımı (alıntı)** | "Derin dinleyici; şarkı sözü okur, az etkileşim kurar, gece ve yalnız dinler" ([[mood-taxonomy]] §3.2.3) |
| **Tetikleyici durumlar** | Karanlık tema + sakin öneri kartı (yaklaşma); parlak/animasyonlu arayüz ve yüksek etkileşim baskısı (geri çekilme) |
| **UI etkisi** | "Karanlık tema, minimalist, az animasyon"; test etkisi: "Karanlık tema, keşif algoritması, düşük etkileşim; dark/light kontrast (WCAG 1.4.3)" ([[mood-taxonomy]] §3.2.3) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Melankolik); ikincil = Yaratıcı — **kurgusal persona ataması**; taksonomi §3.2.10 grup ataması "Kız Çocuk ×3"tür, Genç Kız ataması taksonomide yoktur → bu ikincil eşleme persona-özel kurgusaldır (§7.2'de kayıtlı) |
| Test etkisi | Karanlık tema kontrastı + keşif algoritması + düşük etkileşim — [[mood-taxonomy]] §3.2.3 |
| Taksonomi BPM notu | §3.2.3 "60-90 BPM ⚠️" işaretli kurgusal önermedir (§1.2); research-bank P4'te klasik/jazz/akustik/soundtrack için aralık YOK → sayısal eşik olarak kullanılmaz, `⚠️ VERIFICATION REQUIRED` |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Karanlık tema tercihi | Koyu temada metin ≥ **4.5:1**, UI ≥ **3:1** | Manuel + kontrast denetimi |
| 2 | Düşük etkileşim (az animasyon) | Animasyonlar sakin; hareket azaltma etkili | Manuel + ayar testi |
| 3 | Şarkı sözü okuma | Uzun metin okunur; satır aralığı ve kontrast korunur | Manuel + screenshot |
| 4 | Keşif algoritması | Az etkileşimle de doğru öneri akışı | DOM assertion + screenshot |
| 5 | Gece / yalnız dinleme | Gece modunda parlaklık ani sıçramaz; CLS ≤ **0.1** | Performance trace + screenshot |
| 6 | Az skip, tekrar dinleme | Favori/tekrar akışında sayaç tutarlı | DOM assertion + sayaç kontrolü |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Klasik 2) Akustik 3) Jazz 4) Türkçe alternatif 5) Soundtrack | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Chopin, Norah Jones, Erik Satie, Dedublüman, Joe Hisaishi | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | P4'te bu türler için doğrulanmış aralık **YOK** (klasik, akustik, jazz, alternatif, soundtrack) | `⚠️ VERIFICATION REQUIRED` (research-bank P4 — aralık yok) + kurgusal tercih: yavaş-orta tempo |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yavaş-orta tempo, sakin ritim (kurgusal — ölçüm değil) | Kaynak: kurgusal (persona verisi); taksonomi §3.2.3 notu "60-90 ⚠️" kurgusaldır, bank verisi değildir |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:00-18:00 (ödev yanı), 21:30-00:30 (gece) · Hafta sonu 10:00-12:00, 20:00-23:00 | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (kişisel profil, çevrimdışı söz akışı) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Az etkileşimli keşif; benzer ruh hâli listelerinde uzun kalır | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %35 (sabit harçlık; reklamsız deneme eğilimi düşük — kurgusal) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Klasik | Söz olmadan duygu; ödev yanında odak |
| 2 | Akustik | Sade düzenlemeler; sakin dinleme |
| 3 | Jazz | Gece ritmi; uzun oturumlarda yormaz |
| 4 | Türkçe alternatif | Söz okumayı sever; derin sözler |
| 5 | Soundtrack | Görsel-müzik eşleşmesi (ikincil Yaratıcı kümesiyle örtüşür) |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 16:00-18:00 | Ödev yanı | Akustik / klasik |
| Hafta içi | 19:00-20:30 | Akşam sessizliği | Jazz |
| Hafta içi | 21:30-00:30 | Gece, yalnız | Türkçe alternatif / soundtrack |
| Hafta sonu | 10:00-12:00 | Resim/yazı (yaratıcı) | Soundtrack / enstrümantal |
| Hafta sonu | 15:00-17:00 | Kitap okuma | Klasik / akustik |
| Hafta sonu | 20:00-23:00 | Yağmurlu hava dinlemesi | Hüzünlü karışık liste |

*Not: BPM satırları bu dosyada SAYISAL olarak verilmemiştir — research-bank P4'te klasik/jazz/akustik/alternatif/soundtrack aralığı yoktur → türler `⚠️ VERIFICATION REQUIRED` + kurgusal yavaş-orta tempo tercihi olarak taşındı. "Şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2); taksonomi §3.2.3'teki "60-90 BPM ⚠️" kurgusal önermedir, bank verisi değildir.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | 50 Mbps (ev, kurgusal) / mobil veri kurgusal | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark (gece ve söz okuma baskın — karanlık tema kontrast testi) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 2-3 sa/gün, hafta sonu 3-4 sa/gün (kurgusal, derin-uzun oturum) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — temel akışlar akıcı; gelişmiş ayarlar sınırlı | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Aile tableti (ortak kullanım, eski) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | miyopi (gözlük — kurgu); gece düşük ışıkta uzun söz okuma | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — koyu temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; kulaklıkla sakin dinleme | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | normal; az etkileşim tercihi | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | derin odak, düşük uyaran toleransı | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 — sakin, yol gösteren metin | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | "Ruh hâli/duygusal sağlık" ifadesi 6698 m.6 kapsamında sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Koyu tema kontrastı | Karanlık tema renk eşleşmesi hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil — koyu zemin × düşük parlaklık riski testte kontrol edilir |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Şarkı sözünü ekranda okur; söz paneli görünür ve okunabilir olmalı.
- Az etkileşim kurar; uzun süre dinler, sık sık dokunmaz.
- Gece ve yalnız dinler; arayüz sakin ve sessiz olmalı.
- Paylaşımı azdır; beğendiğini defterine/yazısına taşır.
- Hata metninde suçluluk/panik tonu görürse geri çekilir; kısaca yol gösteren metin ister.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Sabit harçlık; premium'a çekinceli | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | 4 sn; sakin yükleme ekranına ~10 sn dayanır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sessizce uygulamadan çıkar; suçlayıcı metni hatırlar | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 3/10 — sakinliği bozan atlanamayan reklamda listeyi kapatır | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Uzun ama sakin anlatım; söz/metin okumayı sever | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Az paylaşır; yakın arkadaşla tek şarkı gönderir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 00:30 sonrası kapatma eğilimi; parlak/animasyonlu akış reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sade, minimalist boş durum metni keşfi güvenli kılar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.3 (düşük etkileşim) |

*Erişilebilirlik etkisi (özet):* gece kullanımı + gözlük + koyu tema → kontrast ≥ **4.5:1** (1.4.3) ayrıca karanlık temada test edilir; dokunma hedefi ≥ **24×24 CSS px** (2.5.8) — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Ceren Öztürk'sün. 13 yaşında, Balıkesir/Altıeylül'de yaşıyorsun, Altıeylül Ortaokulu 8. sınıf öğrencisisin.
Melankolik bir kişiliğin var: şarkı sözü okur, az etkileşim kurar, gece ve yalnız dinlersin; ikincil olarak Yaratıcı'sın — resim/yazı çalışırken uzun oturum kurarsın.
Müzik zevkin: klasik, akustik, jazz, Türkçe alternatif, soundtrack. En sevdiklerin: Chopin, Norah Jones, Erik Satie, Dedublüman, Joe Hisaishi.
Samsung Galaxy A34 5G kullanıyorsun, koyu temadasın, gözlük takarsın, internetin evde 50 Mbps (kurgusal).
Görme kısıdın yok ama miyopisin — gece düşük ışıkta uzun söz okursun, koyu temada net kontrast beklersin.
Sabır eşiğin 4 sn, sakin yükleme ekranına ~10 sn dayanırsın; hata ekranlarında kısa, yol gösteren ve suçlayıcı olmayan metin istersin.
Test edilecek akışlar: karanlık tema kontrastı, keşif/öneri algoritması, düşük etkileşim (az animasyon), şarkı sözü okunabilirliği, gece akışı, erişilebilirlik denetimi.
Davranış notları:
- Söz panelinde metni satır satır okursun; kontrast düşerse okumayı bırakırsın.
- Animasyonlar az ve yavaş olmalı; parlak ani parlamalarda gözün yorulur.
- Öneriler az etkileşimle de doğru olmalı; algoritma seni tanımalı, sık dokundurmamalı.
- Arayüzde "sen" dili, kısaca yol gösteren ve suçlayıcı olmayan metin beklersin.

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

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme (koyu tema) | LCP ≤ **2500 ms**, karanlık tema üstte, CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | 1.4.3 (`VERIFIED`) |
| 2 | Karanlık tema kontrastı | Metin ≥ **4.5:1**, UI ≥ **3:1** (mood-taxonomy §3.2.3) | Manuel + kontrast denetimi | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 (`VERIFIED`) |
| 3 | Şarkı sözü paneli okuma | Söz okunur; satır aralığı ve kontrast korunur | Manuel + screenshot | — (P8 dışı) | 1.4.3 (`VERIFIED`) |
| 4 | Düşük etkileşim / az animasyon | Animasyonlar sakin; hareket azaltmada tamamen sakinleşir | Manuel + ayar testi | — (P8 dışı) | 2.5.7 (`VERIFIED`) |
| 5 | Keşif / öneri algoritması | Az etkileşimle doğru öneri akışı; ruh hâli tutarlı | DOM assertion + screenshot | — (P8 dışı) | 1.4.11 (`VERIFIED`) |
| 6 | Gece akışı / düşük ışık | Ani parlaklık sıçraması yok; okunabilirlik korunur | Manuel + ekran görüntüsü | — (P8 dışı) | 1.4.3 (`VERIFIED`) |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; INP ≤ **200 ms** | Trace + console log | İlk ses < **1 sn** (P8.1 `VERIFIED`) | — |
| 8 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 9 | Favori ekleme (az etkileşim) | Tek dokunuş yeterli; anlık sakin geri bildirim | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 10 | Tema geçişi (dark / light) | Geçiş müziği kesmez; iki temada da kontrast ≥ **4.5:1** (1.4.3) | Manuel + ekran görüntüsü + kontrast denetimi | — (P8 dışı; eşik P5) | 1.4.3 (`VERIFIED`) |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace | Slow 4G profili (P8.3 `VERIFIED`) | — |
| 12 | Hata sayfaları (404 / kopma) | Türkçe, kısa, yol gösteren; suçlayıcı olmayan metin | Manuel + screenshot | — (P8 dışı) | 3.3.8 (`VERIFIED`) |
| 13 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 · 2.5.7 · 2.5.8 (`VERIFIED`) |
| 14 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 1.4.10 ⚠️ VERIFICATION REQUIRED (X-7) |
| 15 | Uzun gece oturumu | Uzun oturumda CLS/INP kararlı; oturum düşmesi yok | Performance trace (uzun oturum) | CLS ≤ **0.1** (P8.1 `VERIFIED`) | — |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Sanatçı/şarkı adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | — (P8 dışı) | — |
| 17 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 · 2.4.11 (`VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); WCAG kodları yalnız P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (13) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları (gözlük dâhil) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adları (Melankolik / Yaratıcı) | Vault referanslı atama | [[mood-taxonomy]] §3.2.3 + §3.2.10 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama)`; Yaratıcı ikinciliği **persona-özel kurgusal** (taksonomi §3.2.10 grup ataması Kız Çocuk ×3) |
| 12 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.2.3 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Melankolik test eşlemesi (karanlık tema · keşif · düşük etkileşim) | Vault verisi | [[mood-taxonomy]] §3.2.3 | [[personas/test-scenarios-mapping]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Taksonomi BPM notu (§3.2.3 "60-90 ⚠️") | Kurgusal öneri (banka değil) | mood-taxonomy §1.2/§3.2.3 | research-bank P4 (aralık yok) | `⚠️ VERIFICATION REQUIRED` — sayısal eşik yapılmadı |
| 15 | Klasik/jazz/akustik/alternatif/soundtrack BPM aralığı | Doğrulanmadı | research-bank P4 (bu türler için aralık YOK) | — | `⚠️ VERIFICATION REQUIRED` + kurgusal tercih |
| 16 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı |
| 17 | Favori sanatçı adları (Chopin, Norah Jones, Erik Satie, Dedublüman, Joe Hisaishi) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 18 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 19 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 20 | Diğer cihazlar (eski aile tableti) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 21 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 22 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 23 | Persona renk kombinasyonu (koyu tema) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 24 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 25 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 26 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Eski dosyadaki istatistik iddiaları (TÜİK/RTÜK/WHO/Spotify yüzdeleri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 29 | Davranış eşikleri (sabır 4 sn, spinner ~10 sn, reklam 3/10, premium %35) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 30 | Dinleme saatleri / ekran süresi / internet hızı | Kurgusal (paket dışı) | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 31 | Eski dosyadaki yaş/mood/cihaz değerleri (§7.2 çakışma tablosu) | Vault içi çelişki | eski salt-okunur persona dosyası | [[personas/index]] §6.3 | Karar: index kazanır; çelişki §7.2'de saklı |
| 32 | §6.1/§6.2 sayaçları (test adımı, kaynak satırı, derinlik) | İç tutarlılık | Bu dosyanın §3.10/§3.11 tabloları | `vault-utf8-writer verify` | `Kaynak: kurgusal (dosya içi sayım; verify ile doğrulanır)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`ceren-ozturk-melankolik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `klasik 72 BPM` | Bankta tür aralığı yok → `⚠️ VERIFICATION REQUIRED` + kurgusal tercih; şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.3 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| Persona-özel eşik uydurma | "Ceren için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış (yalnız §4 Şablon-Önce bloğundaki `{{VARIABLE}}` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] 16 yaş altı: `veli_onayı_gerekli: true` + KVKK notu (TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok; bankta aralık yoksa `⚠️ VERIFICATION REQUIRED`
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.3 / §3.2.10) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 12 | 13 | Index §6.3 kazanır; okul/sınıf yaşa göre yeniden kurgulandı (§3.1) |
| Mood | Melankolik (birincil) / Yaratıcı (ikincil) | Melankolik / Yaratıcı | Birincil: index §6.3 + mood-taxonomy §3.2.3; ikincil **kurgusal** (taksonomi §3.2.10 grup ataması Kız Çocuk ×3 — Genç Kız ataması yok, §3.4 notu) |
| Kullanıcı ID | `CM-CO-12-BAL` (eski format) | `CM-CO-13-MEL-BAL` (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX`) | Şablon formatı kazanır |
| Birincil cihaz | Aile tableti (EXCLUDED) | Samsung Galaxy A34 5G (P3 VERIFIED) | [[research-bank]] P3 |
| BPM tercihi | Hüzünlü şarkılar için doğrulanmamış BPM | Bankta tür aralığı yok → `⚠️ VERIFICATION REQUIRED` + kurgusal tercih | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz |
| Etki alanındaki istatistikler | Kaynaksız yüzdeler | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; yaş/mood index §6.3'e göre yeniden atandı; ID formatı şablona uyarlandı; veli_onayı + KVKK notu eklendi; Yaratıcı ikinciliği kurgusal notuyla kaydedildi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-kiz/ceren-ozturk-melankolik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
