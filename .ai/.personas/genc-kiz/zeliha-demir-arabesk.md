---
title: "CoreMusic — Persona: Zeliha Demir"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-kiz/zeliha-demir-arabesk"
updated: 2026-09-26
group: genc-kiz
age: 15
mood: Arabesksever
persona_id: CM-ZD-15-ARB-ADA
veli_onayı_gerekli: true
---

# CoreMusic — Persona: Zeliha Demir

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-kiz (13-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 15 yaşındaki bir genç kızın gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Arabesksever / Moody), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-kiz (13-17) |
| Birincil mood | Arabesksever ([[personas/index]] §6.3 ataması) |
| Test odağı | Duygusal UI (slow tempo, vokal öncelikli öneri), mood-geçiş (Arabesksever ↔ Danssever), eski albüm fuzzy araması, koyu tema kontrastı |
| Veli onayı | `veli_onayı_gerekli: true` (16 yaş altı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Mood-geçiş (ADR-023 satır 16) | Level 2 + 3 | Arabesksever ↔ Danssever geçişi; geçersiz mood değeri = error |
| Müzik keşif/arama (ADR-023 satır 18) | Level 2 + 3 | Fuzzy search ("muslum gurses" / "Müslüm Baba"); eski albüm diskografisi; boş sonuç = boundary |
| Playlist CRUD (ADR-023 satır 19) | Level 2 + 3 | "arabesk geceler" vb. kişisel listeler; iki istemci yarışı (B) / yetki (E) |
| Paylaşım → yayılım (ADR-023 satır 20) | Level 2 + 3 | WhatsApp/Instagram link paylaşımı; 280 karakter sınırı; gizli hesap (E) |
| Erişilebilirlik denetimi | Level 2 | Koyu temada 1.4.3 ≥4.5:1, 2.5.8 ≥24×24 px denetimi |

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
| 3 | [[mood-taxonomy]] §3.4 satır 19 + §3.2.4 | Küme adı + tanım alıntısı (Arabesksever / Moody) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Zeliha Demir | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 15 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 14 Mart 2011 | Kaynak: kurgusal (persona verisi; yaş 15 ile tutarlılık için taşındı) |
| **Cinsiyet** | Kız | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Adana / Seyhan / Yeşilyurt | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Adana Yeşilyurt Anadolu Lisesi, 9. sınıf | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | Belediye otobüsü (~30 dk; bazen baba bırakır — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | zeliha.arabesk.kizi | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-ZD-15-ARB-ADA` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `ZD` | Ad + soyad ilk harfleri (Zeliha Demir → Z, D) |
| `YY` | `15` | Yaş 2 hane (15 → `15`; şablon örnek aralığı 10-99'a uyar) |
| `ZZZ` | `ARB` | Mood tür kodu (Arabesksever → ARB) |
| `XXX` | `ADA` | Şehir kodu (Adana → ADA) |

> **KVKK / yaş sınırı notu (zorunlu — 16 yaş altı):** Bu persona 15 yaşındadır → frontmatter'de `veli_onayı_gerekli: true`. Dayanak: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir). Kaynak: dergipark.org.tr (mukayeseli inceleme) + kvkk.gov.tr (Yayın No 84) → `Kaynak: [k1] + [k2]` (research-bank P6.4 VERIFIED). "16 yaş altı → veli onayı" CoreMusic politika sonucudur → `⚠️ DERIVED` (P6.4 son satır). Persona tamamen kurgusaldır; gerçek kişi verisi kullanılmaz (6698 m.5/1 — `Kaynak: [k1] + [k2]`).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (kıyafet listesi, aksesuar, cilt bakımı…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 160 cm (ergenlik dönemi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 55 kg, orta yapılı ("balık etli" tanımı — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Koyu kestane dalgalı omuz boyu saç, ela göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (görme ve işitme iyi — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Koyu tonlar (siyah, bordo) + gümüş/nazar aksesuarları; profil fotoğrafı düşük ışık, duygusal estetik (renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Okul yolunda otobüste tek elle, hareket hâlinde kullanım → büyük ve hızlı tepki veren hedefler bekler | Kaynak: kurgusal (persona verisi); kontrast kriteri: `Kaynak: [k1] + [k2]` (P5.4) |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 40 | Yeni insanlarla tanışmakta çekingen; yakın çevresiyle konuşkandır — sosyal enerjisi dar alanda yüksektir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Uyumlu ve yumuşak başlıdır; çatışmadan kaçınır ("aman kavga çıkmasın"), ailesine saygılı, arkadaşlarına sadık. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 50 | Derslerini idare eder, verilen görevleri yapar ama hırslı değildir — düzen ile dağınık arası orta nokta. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Eksen belirsizliği:** eski kaynakta "Duygusal Denge 3/10 — duygusal dalgalanmalar yaşar, arabesk şarkılarla ağlar" yazar → ×10 = 30. N (nörotisizm) ekseninde ters kodlama varsa gerçek okuma 70 (yüksek gerginlik) olur; taksonomi §3.4 satır 19 önermesi "Yüksek N" bu ters okumayla tutarlı → `⚠️ VERIFICATION REQUIRED` (bkz. §7.2). | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120; eksen: `⚠️ VERIFICATION REQUIRED` |
| Deneyime Açıklık | O | 40 | Yeni müzik türlerine mesafeli ("arabesk en iyisi"); yine de pop'a yavaşça açılıyor — orta-düşük keşif açıklığı. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Dönüşüm yöntemi | Eski 10'luk puan × 10 (doğrusal); dönüşümün kendisi `⚠️ DERIVED` — ölçek eşdeğerliği iddia edilmez |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Arabesksever |
| **İkincil küme** | Moody ([[mood-taxonomy]] §3.2.4) |
| **Küme tanımı (alıntı)** | "Sürekli arabesk; hüzün/duygu odaklı dinleme" ([[mood-taxonomy]] §3.4 satır 19) |
| **Tetikleyici durumlar** | Hüzünlü/duygusal an (yaklaşma); arabeksiz genel öneri akışı ve şarkıyı bölen reklam (geri çekilme) |
| **UI etkisi** | "Duygusal UI, slow tempo, vokal öncelikli öneri"; test etkisi: "Anlık karar, tür değiştirme (genre switching), öneri doğruluğu; ADR-023 satır 16 (geçersiz mood değeri = error)" ([[mood-taxonomy]] §3.4 satır 19 + §3.2.4) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Arabesksever); ikincil = eski salt-okunur persona kaynağı (Moody) — §7.2'de kayıtlı |
| Test etkisi | ADR-023 satır 16 (mood-geçiş: Arabesksever ↔ Danssever) + satır 18 (arama) + satır 20 (paylaşım) — [[mood-taxonomy]] §3.4 satır 19 / §3.2.4 |
| Taksonomi BPM notu | §3.4 satır 19 "60-100 BPM ⚠️" ve §3.2.4 "80-140 (geniş) ⚠️" işaretli kurgusal önermedir (§1.2); test eşikleri olarak research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Duygusal UI / slow tempo beklentisi (Arabesksever) | Yavaş, duygusal karşılama; vokal öncelikli öneri kartı üstte | DOM assertion + screenshot |
| 2 | Mood-geçiş Arabesksever ↔ Danssever (ADR-023 satır 16) | Geçiş sırası korunur (H); geçersiz mood değeri hata üretir (E); BPM filtresi eşiği (B) | Manuel + hata akışı + sınır testi |
| 3 | Ruh haline göre tür değiştirme (Moody, §3.2.4) | Tür önerisi anlık güncellenir; öneri doğruluğu korunur | DOM assertion + sayaç kontrolü |
| 4 | Yüksek skip oranı (Moody) | Atlama kilidi/gecikme yok; INP ≤ **200 ms** | Performance trace + sayaç kontrolü |
| 5 | Vokal öncelikli öneri (Arabesksever) | Öneri kartında sanatçı + söz öne çıkar; iç içe metin taşması yok | DOM assertion + screenshot |
| 6 | Koyu tema / düşük etkileşim | Koyu temada kontrast ≥ **4.5:1** (1.4.3) korunur; az animasyon | Kontrast denetimi + screenshot |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Arabesk 2) Fantezi 3) Türk Halk Müziği 4) Türkçe Pop 5) Slow/Sanat Müziği | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Müslüm Gürses, Bergen, Ferdi Tayfur, İbrahim Tatlıses (P4 VERIFIED) + Tarkan (P4 dışı) | `Kaynak: [k1] + [k2]` (P4.2-P4.4 arabesk satırları `VERIFIED`) + Tarkan için `⚠️ VERIFICATION REQUIRED` (bankada yok); tercih sırası kurgusal |
| **BPM aralığı (tür)** | Türkçe Pop **80-120** BPM (P4.4) · Arabesk/Fantezi/THM için bankada aralık **YOK** | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`); eksik türler: `⚠️ VERIFICATION REQUIRED` |
| **BPM (şarkı bazlı)** | Yazılmadı — "Affet", "Acıların Kadını" vb. için BPM iddiası yazılmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Taksonomi önermesi (§3.4 satır 19)** | "Arabesk, duygusal vokal · 60-100 BPM ⚠️" | `Kaynak: [[mood-taxonomy]] (kurgusal önerme — §1.2)`; test eşiği yapılmaz |
| **Kişisel tempo tercihi** | 70-100 BPM (arabesk ağır) · 100-130 BPM (pop orta) — eski dosya tercihi | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil; P4.4 Pop 80-120 dışı satır §7.2'de) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 3-5 sa/gün (okul yolu, teneffüs, ders arka planı) · Hafta sonu 6-8 sa/gün | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (kişisel profil) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Arkadaş/aile tavsiyesi (%50), TikTok arabesk editleri (%30), Instagram (%15), YouTube (%5); algoritma yerine "bu sanatçıyı sevenler" listesini tercih eder | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | %30 (ücretsiz kullanıcı; babası doğum gününde almayı düşünüyor) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Arabesk | "Arabesk candır" — acıyı ve sevdayı anlatan, aile kültürünün parçası |
| 2 | Fantezi | Arabeskin daha melodik hâli; ev işi/yemek yaparken ideal |
| 3 | Türk Halk Müziği | Anneanneden miras; köklere bağlılık hissettirir |
| 4 | Türkçe Pop | Okul arkadaşlarından etkilenerek başladı; "hafif kaçıyor ama eğlenceli" |
| 5 | Slow/Sanat Müziği | Duygusal gecelerde ve ders çalışırken arka planda |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 07:30-08:20 | Otobüs yolculuğu (kulaklık) | "okul yolu" listesi — arabesk + hafif pop |
| Hafta içi | 10:15 | Teneffüs, arkadaşla kulaklık paylaşımı | Öneri / tek kulaklık |
| Hafta içi | 15:45-16:30 | Dönüş yolu | Günün yorgunluğuna arabesk |
| Hafta içi | 17:00-18:00 | Ders çalışma | Slow / sanat müziği (arka plan) |
| Hafta içi | 22:45 | Yatakta mesajlaşma | Hafif ses, kulaklıkla |
| Hafta sonu | 11:30-15:00 | Ev işi + arkadaş buluşması | Hareketli arabesk/fantezi + paylaşımlı kulaklık |

*Not: BPM satırları yalnızca P4.4 tür aralığı düzeyindedir (Türkçe Pop 80-120); arabesk/fantezi/THM aralığı bankada YOK → `⚠️ VERIFICATION REQUIRED`. "Şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Eski dosyanın "70-100 / 100-130 BPM" tercihi bankada doğrulanmadı → kurgusal tercih olarak taşındı.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy A34 5G | Kaynak: GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) + Samsung resmi (samsung.com — "Resolution (Main Display) 1080 x 2340") |
| **Ekran çözünürlüğü → viewport** | 1080 × 2340 px → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (research-bank P3.2); üretilmiş değer, 2. bağımsız kaynak **değildir** |
| **OS** | Android 13 (A34 için 4 büyük OS güncellemesi) | Kaynak: GSMArena + Samsung resmi / en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G (P3.3 `VERIFIED (3 kaynak)`) |
| **Tarayıcı** | Chrome (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev: 35 Mbps (ADSL — kurgusal) / Mobil: 15 GB paket (kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | dark (koyu tema tercihi — pil tasarrufu; duygusal/gece dinleme ile uyumlu) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Hafta içi 4-6 sa/gün, hafta sonu 7-8 sa/gün (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 5/10 — telefon/sosyal medya akıcı, teknik konularda abisine sorar | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | Abisiyle ortak eski Lenovo laptop, Bluetooth kulaklık (Çin malı) — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır; `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | yok (kurgu); gece/düşük ışıkta uzun ekran süresi | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — koyu temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | normal; kulaklıkla yüksek ses dinler | İşitsel geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | normal; otobüste hareket hâlinde tek elle kullanım | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · sürükleme yerine tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | orta-kısa; duygusal dalgalanma, eleştiriye alınma | Erişilebilir kimlik doğrulama min. (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 — hata metni kısa, suçlayıcı değil | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | 15 yaşında (16 altı) → veli onayı zorunlu; "aile/ergenlik" anlatısı sağlık verisi sayılabilir → persona'da **kurgusal olması zorunlu** (research-bank P6.5) |
| Renk kombinasyonu | Koyu tema paletinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil — bordo/siyah ton riski testte kontrol edilir |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Arabesk kültürüyle büyümüş: annesinin radyosunda Müslüm Gürses, Bergen, Ferdi Tayfur dinleyerek başladı; "arabesk sadece müzik değil, yaşam biçimi" der.
- Duygusal ve samimi: ağlamaktan çekinmez, "ben buyum" der; empati yüksektir, çatışmadan kaçınır.
- Aile düşkünü: özellikle anneannesi (türkü/arabesk kahramanı) ve babasıyla müzik sohbetleri.
- Temkinli yenilikçi: pop'a akran baskısıyla başladı, hâlâ "hafif" bulur; "arabesk gibisi yok" der.
- Çekingen ama içten: yeni ortamlarda sessiz, tanıdık çevrede konuşkandır; şarkı sözlerini çabuk ezberler.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Duygusal ve plansız; premium'i ister ama bütçesi yok — babası doğum gününde almayı düşünüyor | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | ~4 sn; yavaş yüklemede içine kapanır, "eski eskisi daha iyiydi" der | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Alışkanlığa bağlı: değişikliğe alışana kadar söylenir; A/B değişikliğinde geri dönüş bekler | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 5/10 — "en heyecanlı yerinde reklam giriyor!" der ama bırakmaz | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Duygu + söz: şarkı sözlerini şiir gibi okuyarak öğrenir (2-3 dinlemede ezberler) | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Arkadaşına WhatsApp'tan şarkı linki atar (haftada 3-5); okul bahçesinde kulaklık paylaşır | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.4 satır 19 (duygu odaklı paylaşım) |
| **Yorgunluk etkisi** | 23:00 sonrası duygusallaşır; gece arabeks listeleri devreye girer, uzun akış reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tanıdık arabesk klasiği çalarsa hemen listeye ekler ("Müslüm Baba" sırrı) | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.4 satır 19 + §3.2.4 |

*Erişilebilirlik etkisi (özet):* otobüste tek elle, hareket hâlinde kullanım → dokunma hedefi ≥ **24×24 CSS px** (2.5.8); koyu temada kontrast ≥ **4.5:1** (1.4.3) ayrıca test edilir — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Zeliha Demir'sin. 15 yaşında, Adana Seyhan/Yeşilyurt'ta yaşıyorsun, Yeşilyurt Anadolu Lisesi'nde 9. sınıfta okuyorsun.
Arabesksever bir kişiliğin var: arabesk senin için müzikten öte bir yaşam biçimi — hüzün ve duygu odaklı dinlersin; ikincil olarak Moody'sin — ruh hâline göre tür değiştirirsin, geniş tempo aralığında, yüksek skip oranıyla dinlersin.
Müzik zevkin: Arabesk, Fantezi, Türk Halk Müziği, Slow/Sanat Müziği, az da olsa Türkçe Pop. En sevdiklerin: Müslüm Gürses, Bergen, Ferdi Tayfur, İbrahim Tatlıses, Tarkan.
Samsung Galaxy A34 5G kullanıyorsun, koyu temada, internetin evde 35 Mbps (kurgusal).
Görme / işitme / hareket kısıtların yok; otobüste tek elle, hareket hâlinde dokunursun — büyük hedefler ve hızlı tepki beklersin.
Sabır eşiğin ~4 sn; yavaş ekranda içine kapanırsın, "eski eskisi daha iyiydi" dersin.
Test edilecek akışlar: mood-geçiş (Arabesksever ↔ Danssever), fuzzy arama (eski albümler), tür keşfi (arabesk kategorisi), playlist CRUD, paylaşım (WhatsApp/Instagram), koyu tema kontrastı, veli onayı akışı, hata ekranları, erişilebilirlik denetimi.
Davranış notları:
- Müslüm Gürses'e "Müslüm Baba" dersin; Bergen'in hayat hikayesi seni derinden etkiler.
- Pop'a arkadaşlarından başladın, hâlâ "hafif" bulursun ama Tarkan'ı da seversin.
- Ailene çok düşkünsün, özellikle anneannene; o sana türkü ve arabesk sevgisini aşıladı.
- Duygusal anlarında arabesk dinler, ağlar, sonra geçersin.
- Reklamlara 5/10 tahammülün var; "en heyecanlı yerinde reklam giriyor!" dersin.
- Premium istersin ama bütçen yok; baban doğum gününde almayı düşünür.
- Yeni şeyler denemekte temkinlisin: "arabesk gibisi yok" dersin.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, kısa cümleler; suçlayıcı/otoriter ton yok |
| Sürpriz yasak | Hassas persona: korkutucu/utanç verici senaryo yok; duygusal içerik kötüye kullanılmaz |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |
| Eski prompt temizliği | Eski AI_PROMPT'taki "Spotify TR 2025 bilgin: Yok…" satırı bu prompta KOPYALANMADI → `⚠️ VERIFICATION REQUIRED` (§7.2) |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme | LCP ≤ **2500 ms**, duygusal karşılama kartı üstte, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Mood-geçiş (Arabesksever arayüz, ADR-023 satır 16) | Geçiş sırası korunur (H); geçersiz mood değeri hata üretir (E); BPM filtresi eşiği (B) | Manuel + hata akışı + sınır testi |
| 3 | Müzik araması (fuzzy: "muslum gurses", "Müslüm Baba", "Zeliha" ğ/ş/ı ile) | Türkçe karakter bozulmadan eşleşme; INP ≤ **200 ms** | Trace + DOM assertion |
| 4 | Eski albüm diskografisi (sanatçı sayfası) | 80'ler/90'lar albümleri kronolojik listelenir; boş sonuçta anlamlı boş durum metni | DOM assertion + liste kontrolü |
| 5 | Tür keşfi (arabesk kategorisi) | Kategori sayfası zengin içerik sunar; BPM filtresi yalnız P4.4 türleriyle çalışır | DOM assertion + filtre kontrolü |
| 6 | Playlist oluşturma ("arabesk geceler") | Tek ekranda oluşturma; emoji/özel karakter isim desteklenir; anlık "oluşturuldu" geri bildirimi | DOM assertion + LocalStorage kontrolü |
| 7 | Playlist CRUD çakışması (ADR-023 satır 19) | Aynı anda iki istemciden aynı liste: yarış durumu (B) / yetki ihlali (E) uygulanır | Manuel + sınır/hata testi |
| 8 | Paylaşım (ADR-023 satır 20) | Paylaş akışı açılır; 280 karakter sınırı uygulanır; gizli hesap uyarısı doğru | Manuel + sınır testi |
| 9 | Şarkı çalma + ilk ses | İlk ses < 1 sn; kesintisiz akış; INP ≤ **200 ms** | Trace + console log |
| 10 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 12 | Veli onayı akışı (15 yaş — ADR-023 satır 9/10) | 16 yaş altı hesapta veli onayı istenir (H); onaysız deneme = hata (E); 16 eşiği = boundary (B) | Manuel akış testi + sınır testi |
| 13 | Hata sayfaları (404 / kopma) | Türkçe, kısa, suçlayıcı olmayan metin | Manuel + screenshot |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe |
| 15 | Koyu tema kontrastı | Koyu temada metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11) | Kontrast denetimi + ekran görüntüsü |
| 16 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 17 | Hafta sonu uzun oturum (~6-8 saat dinleme) | Oturum canlı kalır; uzun oturumda çalma listesi durumu bozulmaz | Manuel oturum testi + durum kontrolü |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, ilçe, okul, sınıf, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (15) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: true` (16 yaş altı politikası) | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi (eski 10'luk ×10) | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.4 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | N ekseni yönü ("Duygusal Denge 3/10" ↔ N nörotisizm ters kodlama) | Belirsiz dönüşüm | eski salt-okunur persona dosyası (Duygusal Denge 3/10) | persona-template §3.5.3 (N ters kodlama) | `⚠️ VERIFICATION REQUIRED` — ham ×10 değeri yazıldı (§7.2) |
| 12 | Mood küme adları (Arabesksever / Moody) | Vault referanslı atama | [[mood-taxonomy]] §3.4 satır 19 + §3.2.4 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); adlar mood-taxonomy §3 listesinden` |
| 13 | Mood küme tanımı alıntısı | Vault verisi | [[mood-taxonomy]] §3.4 satır 19 | [[mood-taxonomy]] §3.2.4 | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 14 | Mood↔Big Five eğilimi (Arabesksever: "Yüksek N, orta C") ile persona N=30 uyumu | Kurgusal önerme (§1.2) | mood-taxonomy §1.2 + §3.4 satır 19 | bu dosya §3.5.3 | `⚠️ VERIFICATION REQUIRED` — taksonomi önermesidir, veri hatası değildir (§7.2) |
| 15 | Türkçe Pop tür BPM aralığı (Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | Arabesk / Fantezi / Türk Halk Müziği tür BPM aralığı | Bankada YOK | research-bank P4.4/P4.5 (bu türler kapsam dışı) | — | `⚠️ VERIFICATION REQUIRED` — aralık yazılmadı |
| 17 | Şarkı bazlı BPM; kişisel 70-100/100-130 tercihi; taksonomi 60-100 önermesi | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — şarkı BPM'i yazılmadı; tercih/önerme kurgusal |
| 18 | Sanatçı verisi: Müslüm Gürses · Bergen · Ferdi Tayfur · İbrahim Tatlıses (P4) + tercih listesi (Tarkan P4 dışı) | Gerçek-dünya (arabesk) + kurgusal tercih | tr.wikipedia.org/wiki/Arabesk_müzik + tr.wikipedia Müslüm Gürses | ntv.com.tr (Bergen) + ilimvemedeniyet.com | `Kaynak: [k1] + [k2]` (P4 `VERIFIED`); tercih sırası kurgusal; Tarkan realitesi `⚠️ VERIFICATION REQUIRED` |
| 19 | Cihaz spec'i: A34 5G — 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, 1000 nit, GG5, IP67, Android 13, BT 5.3 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 20 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 21 | Diğer cihazlar (ortak Lenovo laptop, Bluetooth kulaklık) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Tarayıcı sürümü, internet hızı (35 Mbps ADSL), mobil paket | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 23 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 24 | Persona renk kombinasyonu (koyu tema / bordo-siyah palet) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 25 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 26 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 27 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 28 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Eski dosyadaki istatistik iddiaları (Spotify TR 2025 satırı, takipçi sayıları, uygulama kullanım yüzdeleri, aile geliri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`zeliha-demir-arabesk.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Galaxy A34 viewport = 360×780` (türetilmiş ama türetilmişsiz) | `viewport ≈ 360×780 (DPR 3) — ⚠️ DERIVED` |
| `Şarkı X BPM` / `Affet 78 BPM` | Tür aralığı (Pop 80-120, VERIFIED ikincil) + arabesk aralığı bankada yok: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.4 satır 19 / §3.2.4 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
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
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM/arabesk sanatçılar), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
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
| Persona-özel eşik uydurma | "Zeliha için INP 500 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] BPM yalnız tür aralığı (P4.4); arabesk/fantezi/THM aralığı ve şarkı BPM'i yok
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.4 satır 19 / §3.2.4) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | 📋 planlanan — `.ai/.personas/methodology.md` (bu turda başka agent sorumluluğunda; diskte henüz yok) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 15 (14 Mart 2011) | 15 | Çelişki yok |
| Mood | Arabesksever (birincil) / Moody (ikincil) | Arabesksever / Moody | Tutarlı — index §6.3 + mood-taxonomy §3.4 satır 19 / §3.2.4 |
| Kullanıcı ID | `GC-ZD-15-ARB-ADA` (eski footer) | `CM-ZD-15-ARB-ADA` (şablon §3.5.1 `CM-XX-YY-ZZZ-XXX`) | Şablon formatı kazanır |
| Birincil cihaz | Samsung Galaxy A34 (128GB) | Samsung Galaxy A34 5G (P3 VERIFIED) | Çelişki yok — eski kayıt P3 ile tutarlı; spec P3'ten alındı |
| N ekseni | "Duygusal Denge 3/10 — duygusal dalgalanmalar yaşar" | N = 30 (ham ×10); ters kodlama okuması 70 | `⚠️ VERIFICATION REQUIRED` — dönüşüm yönü doğrulanana kadar ham değer korunur (§3.5.3 / satır 11) |
| Mood↔Big Five önermesi | — | Taksonomi §3.4 satır 19 "Yüksek N, orta C" vs persona N=30 (ham) / 70 (ters okuma) | Kurgusal önerme (§1.2) — ters okumayla tutarlı, ham okumayla çelişir; veri hatası değil, not satırı (satır 14) |
| BPM tercihi | 70-100 (arabesk) / 100-130 (pop) | P4.4 Pop 80-120 + arabesk/fantezi/THM bankada yok | X-2: şarkı/türe özel doğrulanmamış BPM yazılmaz; tercih kurgusal taşındı (satır 17) |
| AI prompt istatistiği | "Spotify TR 2025 bilgin: Yok. Spotify kullanmazsın…" | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |
| Takipçi / uygulama % / aile geliri | Kaynaksız sayılar (320 takipçi, WhatsApp %30, 40.000 TL vb.) | yazılmadı | §4.5 kuralı 4 |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı; arabesk sanatçıları P4 VERIFIED ile desteklendi; ID formatı şablona uyarlandı; veli_onayı + KVKK notu eklendi; N ekseni belirsizliği §7.2'ye kaydedildi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-kiz/zeliha-demir-arabesk
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
