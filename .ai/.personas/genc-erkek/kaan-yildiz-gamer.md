---
title: "CoreMusic — Persona: Kaan Yıldız"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/genc-erkek/kaan-yildiz-gamer"
updated: 2026-09-26
group: genc-erkek
age: 16
mood: Gamer
persona_id: CM-KY-16-GAM-BUR
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Kaan Yıldız

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **genc-erkek (12-17 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 16 yaşındaki bir Bursalı gamer'in gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Gamer, §3.3 satır 16), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | genc-erkek (12-17) |
| Birincil mood | Gamer ([[personas/index]] §6.3 ataması) |
| Test odağı | Düşük gecikme (INavigation), arka plan çalma dayanıklılığı, klavye kısayolları, çoklu görev, mini player |
| Veli onayı | `veli_onayı_gerekli: false` (16 yaş eşiği karşılandı — ⚠️ DERIVED, [[research-bank]] P6.4) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Düşük gecikme / INavigation | Level 2 + 3 | Test etkisi "Düşük gecikme (INavigation)" ([[mood-taxonomy]] §3.3 satır 16) |
| Arka plan çalma dayanıklılığı | Level 2 + 3 | Test etkisi "arka plan çalma dayanıklılığı" — oyun/sekmelerarası süreklilik |
| Klavye kısayolları | Level 1 + 2 | Test etkisi "kısayollar" — masaüstü mini player + kısayol tuşları |
| Çoklu görev (oyun + müzik) | Level 2 + 3 | "Çoklu görev" tanımı → CPU/gecikme dayanıklılığı |
| Reklam/kesinti toleransı (düşük) | Level 1 + 3 | Oyun ortasında kesinti kabul edilmez — durum geri bildirimi |
| Ağ yavaşlatma / offline | Level 2 + 3 | 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ (research-bank P8.3 VERIFIED) |

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
| 3 | [[mood-taxonomy]] §3.3 | Küme adı + tanım alıntısı (yalnız §3 listesi) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Kaan Yıldız | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 16 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 30 Ekim 2009 | Kaynak: kurgusal (persona verisi; yaş 16 ile tutarlılık için türetildi) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Bursa / Nilüfer — Beşevler | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Anadolu Lisesi 11. sınıf (MEB okul türü: Anadolu Lisesi, 4 yıl) | Kaynak: kurgusal (persona verisi); okul türü: [k1]+[k2] (research-bank P2.7 VERIFIED) |
| **Ulaşım** | Okul servisi; çoğunlukla evde (masaüstü düzeni) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | `KaanX` (oyun/Discord rumuzu — gerçek isminden çok bilinir) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-KY-16-GAM-BUR` | Kaynak: kurgusal (görev tablosu ataması) |

**`CoreMusic Kullanıcı ID` biçimi notu:**

| Alan | Değer |
|------|-------|
| Bu turda verilen ID | `CM-KY-16-GAM-BUR` (görev tablosu / registry eşlemesi) |
| Şablon §3.5.1 biçimi | `CM-XX-YY-ZZZ-XXX` (ör. `CM-KY-16-GAM-BUR`) |
| Parça okuması | `CM` sabit · `KY` = Kaan Yıldız · `16` = yaş · `GAM` = Gamer · `BUR` = Bursa |
| Durum | Şablon formatı ile uyumlu (`CM-XX-YY-ZZZ-XXX`) — 2026-09-26 düzeltmesi; §7.2 |

> **Veli onayı notu (zorunlu — 16 yaş ve üzeri):** Bu persona 16 yaşındadır → frontmatter'de `veli_onayı_gerekli: false`. **Gerekçe:** CoreMusic veli-onayı eşiği 16'dır (research-bank P6.4 son satır → `⚠️ DERIVED`); persona bu eşiği karşıladığı için veli onayı akışı **test edilmez**, onay akışı olan persona'larla (16 yaş altı) ayrıştırılır. Dayanak tablosu değişmez: TMK m.11 erginlik **18 yaş**, COPPA **13**, GDPR m.8 **16**; Türkiye'de çocuk için özel rıza yaşı düzenlemesi **YOK** — "KVKK 16 diyor" ifadesi **YANLIŞTIR** (16, GDPR değeridir) → `Kaynak: [k1] + [k2]` (P6.4 VERIFIED). Not: 16 yaş = ergin olmadığından (TMK 18) ödeme/satın alma adımları ayrı senaryoda işaretlenir; persona tamamen kurgusaldır (6698 m.5/1).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (ayakkabı numarası, kilo takibi, tıbbi ölçüm…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 170 cm (oturma ağırlıklı yaşam — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | 78 kg, hafif kilolu | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Siyah düz saç, kahverengi göz; dağınık kesim | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Mavi ışık filtreli gözlük (uzun ekran süresi — kurgusal reçete); gaming kulaklık kullanır | Kaynak: kurgusal (persona verisi); kulaklık modeli §3.5.6 |
| **Giyim / temsil notu** | Gaming kültürü: oyun temalı tişört, siyah hoodie, eşofman; profil fotoğrafı avatar (kontrast: koyu zemin × açık logo — renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Uzun oturum/az hareket → fare-klavye ağırlıklı kontrol; küçük oynat hedefi yorar, kısayol bekler | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 40 | Fiziksel ortamda sessizdir; online takımda konuşkan ve yönlendiricidir — okulda sunum yaparken terler. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 66 | Discord topluluğunda yardımsever ve uyumludur (moderatörlük yapar); evde ekran süresi tartışmasında diretir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 45 | Dersleri geri plana atar; ama turnuva/kayıt saatlerine dakikasında girer, playlist'lerini oyun türüne göre ayırır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 58 | **Yüksek puan = düşük duygusal denge (ters kodlama).** Online oyunda sert tepki verir (rage quit), gerçek hayatta daha sakindir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 76 | Yeni oyun mekanikleri, yeni build'ler ve yeni elektronik/phonk alt türleri sürekli dener; keşif listesi hiç kapanmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Mood eğilimi eşlemesi | [[mood-taxonomy]] §3.3 satır 16 "Yüksek A, yüksek O" → A 66 (yüksek), O 76 (yüksek) ile uyumlu |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Gamer |
| **İkincil küme** | Yok (bu turda ikincil atama yapılmadı — [[personas/index]] §6.3 tekil küme atar) |
| **Küme tanımı (alıntı)** | "Oyun içi/akış müzikleri; çoklu görev ve düşük gecikme beklentisi" ([[mood-taxonomy]] §3.3 satır 16) |
| **Big Five eğilimi (alıntı)** | "Yüksek A, yüksek O" ([[mood-taxonomy]] §3.3 satır 16) — §3.5.3 puanlarıyla eşleştirildi |
| **Tetikleyici durumlar** | Oyun oturumu başlangıcı (arka plan liste); yarışma/clutch anı (düşük gecikme); ders arası/kaçış (lo-fi listesi) |
| **UI etkisi (test etkisi alıntısı)** | "Düşük gecikme (INavigation), arka plan çalma dayanıklılığı, kısayollar" ([[mood-taxonomy]] §3.3 satır 16 "Test etkisi") |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesi; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Gamer); ikincil bu turda yok — eski kaynakta "İkincil Mood: Enerjik (online)" farkı §7.2'de |
| BPM notu | §3.3 satır 16'da BPM değeri YOKTUR (yalnız tür listesi) → testte research-bank P4.4/P4.5 tür aralıkları kullanılır; uydurma aralık yazılmaz |
| Küme kuralı | Gamer kümesi bu grupta ×2 (Kaan, Çınar A.) → Çınar bu üretim turunda yok; Kaan tek üretilen, test odağı §7.2'de kayıtlıdır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Düşük gecikme beklentisi | INP ≤ **200 ms**; oyun/sekmelerarası dönüşte anlık tepki | Performance trace (P8.1 VERIFIED) |
| 2 | Arka plan çalma dayanıklılığı | Sekme arkası/oyun sırasında çalma sürer; durum kaybı yok | Level 3 E2E + LocalStorage/durum kontrolü |
| 3 | Kısayol beklentisi | Klavye kısayolları (oynat/duraklat/skip) çalışır; odak görünür (2.4.7) | Klavye navigasyon testi + DOM assertion |
| 4 | Çoklu görev (oyun + müzik) | Uzun oturumda bellek/gecikme bozulmaz; CLS ≤ **0.1** | Trace + performans ölçümü |
| 5 | Türkçe karakterle arama | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 6 | Kesinti/reklam hassasiyeti | Geçişte açık geri bildirim; kör nokta yüklenme (2.4.11) korunur | Manuel + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Elektronik / EDM / Phonk 2) Oyun müzikleri (OST) 3) Lo-fi / chillhop 4) Rap / trap (montaj için) 5) Hafif rock / metal | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Alan Walker, C418, Toby Fox, NCS (NoCopyrightSounds), Bring Me The Horizon | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Electro **90-130** · Dance **110-135** · Hip Hop **80-130** (trap mix için) · Pop **80-120** (chill geçişler) (P4.4) · trap **70-110** (P4.5) | Kaynak: nevamuzik.com.tr + turkipedia.com/Beats_per_minute (P4.4) · nevamuzik.com.tr + vocuno.com/tr/bpm-algilayici (P4.5) → `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)` |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Oyun sırasında yüksek tempo (Electro/Dance); ders/kaçışta düşük tempo (lo-fi — tür aralığı bankada YOK, yazılmadı) | Kaynak: kurgusal (persona verisi — persona tercihi); lo-fi BPM: `⚠️ VERIFICATION REQUIRED` |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 16:30-20:00 (oyun), 21:00-23:30 (ders + arka plan), 00:00-02:00 (gece oturumu) · Hafta sonu 11:00-18:00 (uzun oturum) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (oyun OST + elektronik arşivi; masaüstü mini player) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | YouTube/Discord önerilerinden düşen parçaları arar; oyun türüne göre ayrı ayrı playlist ayırır | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | İster ama genelde ücretsiz kalır (veli onayı gerekmez; kendi harçlığı kararı) | Kaynak: kurgusal (persona verisi); politika: `⚠️ DERIVED` (P6.4) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Elektronik / EDM / Phonk | Oyun sırasında odak ve yüksek tempo; clutch anlarında ritim |
| 2 | Oyun müzikleri (OST) | Oyun dünyasının parçası; ana menü/final sahnesi nostaljisi |
| 3 | Lo-fi / chillhop | Ders/kayıt arası sakinleştirme; düşük dikkat yükü |
| 4 | Rap / trap | Montaj videolarının fon müziği |
| 5 | Hafif rock / metal | Gaming montajlarında enerji; playlist monotonluğunu kırar |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 16:30-18:00 | Okul sonrası oyun | EDM / phonk |
| Hafta içi | 18:00-20:00 | Takım maçı (Discord) | Oyun OST (arka plan) |
| Hafta içi | 21:00-23:30 | Ders + ara sıra oyun | Lo-fi / chillhop |
| Hafta içi | 00:00-02:00 | Gece oturumu | Karışık elektronik |
| Hafta sonu | 11:00-18:00 | Uzun oturum + montaj | Rap/trap + NCS |
| Hafta sonu | 20:00-21:00 | Playlist düzenleme | "Gaming Mode", "Montage", "Chill Craft" |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4/P4.5); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.3 satır 16'nın BPM notu da yoktur — uydurma aralık eklenmez; test hedefi olarak banka tür aralıkları kullanılır.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Birincil cihaz** | Gaming PC (kendi topladığı — eski salt-okunur dosyadan; parça spec'leri) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **İkincil cihaz (telefon)** | Samsung Galaxy A54 (eski dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — model spec'i doğrulanmadı, sayı yazılmadı (A34 VERIFIED'tir, bu model DEĞİL) |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS / Tarayıcı** | Masaüstü OS ve tarayıcı sürümü doğrulanmadı (kurgusal masaüstü kullanımı) | `⚠️ VERIFICATION REQUIRED` (P3.3/P3.4) — sürüm yazılmadı |
| **İnternet hızı** | Evde kablolu/kablosuz hızlı bağlantı (kurgusal); hız değeri paket dışı | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **Kulaklık** | Gaming kulaklık (kablolu — model/spec'siz) | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | dark (gece oturumu — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 8-10 saat (oyun + ders + video — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 9/10 — kendi toplama PC, Discord bot, OBS, video düzenleme; kısayolları sever | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | İkinci monitör / mobil cihazlar — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi). Masaüstü kırılma noktaları için banka breakpoint listesi (P8.5, VERIFIED) kullanılır.

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok; uzun ekran süresi yorgunluğu (kurgu; reçete verisi yazılmadı) | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) — dark temada da geçerli | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | yok; gaming kulaklıkla uzun süre dinler | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; fare-klavye ağırlıklı, hızlı tıklama | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8 — WCAG 2.2 yeni, AA) · tek dokunuş yerine sürükleme yok (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | çoklu görev sırasında bölünme toleransı yüksek ama gecikmeye tahammülü yok | Odak görünürlüğü 2.4.7 + 2.4.11; erişilebilir kimlik doğrulama min. (3.3.8) — uzun yeniden giriş istemez | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Persona verisi kurgusal olmak zorunda; 16 yaş → veli akışı YOK (P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| Dokunma hedefi | Mood-taxonomy §3.3'te 44/48 px iddiası YOK; asgari güvenli eşik 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Online dünyada liderdir: takıma strateji anlatır, moderatörlük yapar; fiziksel ortamda çekingen kalır.
- Geciktirmeye (latency) tahammülü yoktur; uygulama hissizleşirse anında geri bildirim ister.
- Pratiktir: kısayol, filtre, sıralama gibi güç özelliklerini ilk gün keşfeder.
- Reklam/kesintiye tahammülü düşüktür — oyun ortasında akış kesilirse platformu bırakır.
- Uzun oturumlarda bile listeyi oyun türüne göre düzenler; bozulan sıralamayı hemen fark eder.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | 16 yaş → kendi harçlığı kararı; veli onayı eşiği karşılandığı için akış veli beklemez (`veli_onayı_gerekli: false`) | Kaynak: kurgusal (persona verisi); politika: ⚠️ DERIVED (P6.4) |
| **Sabır eşiği** | ~2 sn; oyun ortasında 3 sn'den uzun yükleme kabul edilmez | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Ekran görüntüsü alıp Discord'a yollar; teknik metni okumaz — yeniden deneme + kısayol ister | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Düşük — arka arkaya kesintide uygulamayı kapatır, alternatif akışa döner | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Deneyerek + kısa video; dokümantasyon okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Discord arkadaşlarıyla playlist paylaşır; ortak dinleme/çalma sırası beklentisi | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece 01:00 sonrası kısayolla tek tuş "devam et" ister; menü derinliği reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Arka plan çalmada kesintisizlik + kısayol tutarlılığı → platforma bağlılık artar | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Gamer kümesi |

*Erişilebilirlik etkisi (özet):* fare-klavye/kısayol ağırlıklı kullanım → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), odak görünür ve örtülmemiş (2.4.7 + 2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Kaan Yıldız'sın. 16 yaşında, Bursa/Nilüfer-Beşevler'de yaşıyorsun, Anadolu Lisesi 11. sınıfta okuyorsun.
Gamer kişiliğin var: günün büyük kısmını oyun ve masaüstünde geçirirsin, takımında moderatörlük yaparsın; rumuzun `KaanX`.
Müzik zevkin: elektronik/EDM/phonk, oyun müzikleri (OST), lo-fi/chillhop, rap/trap montajları, hafif rock/metal. En sevdiklerin: Alan Walker, C418, Toby Fox, NCS, Bring Me The Horizon.
Kendi topladığın masaüstü bilgisayarın ve ikinci cihazın var (spec'leri doğrulanmadı); mavi ışık filtreli gözlük ve gaming kulaklık kullanıyorsun; dark tema tercih edersin; teknoloji seviyen 9/10.
Görme / işitme / hareket kısıtların yok; fare-klavye ile hızlı kontrol yaparsın, kısayolları beklersin.
Sabır eşiğin ~2 sn'dir; gecikme ve arka arkaya kesinti kabul edilmez; premium kararın kendi harçlığın kadardır, veli onayı beklemezsin.
Test edilecek akışlar: ana sayfa, düşük gecikme (INavigation), arka plan çalma dayanıklılığı, klavye kısayolları, çoklu görev/mini player, tür filtresi ve playlist, Türkçe karakter arama, ağ yavaşlatma, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Oyun sırasında uygulama arkada kalmalı; çalma durumu kaybolursa uygulamayı bırakırsın.
- Kısayol yoksa fare ile uzun yoldan gidersin ama ikinci kez istemezsin.
- "Gaming Mode" listesinde sıralama bozulursa hemen fark eder, düzeltmek istersin.
- Türkçe karakterli oyun/parça adlarında bozulma görürsen platforma güvenin düşer.
- 16 yaşındasın: satın alma akışında veli onayı beklemezsin, ama ödeme adımları ayrı senaryoda işaretlenir.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; `{{...}}` yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, teknik ve hızlı ton — gamer persona'sı uzun açıklama kabul etmez |
| Gerçek veri yasak | Prompt'ta gerçek çocuk/kişi verisi yok — hepsi kurgu (P6.5); veli onayı yalnız test alanı |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme (masaüstü + mobil) | LCP ≤ **2500 ms**, mini player görünür, CLS ≤ **0.1** | Performance trace + screenshot (eşikler: research-bank P8.1 `VERIFIED`) |
| 2 | Arka plan çalma dayanıklılığı (sekme arkası) | Çalma sürer; durum/konum kaybı yok; dönüşte anlık tepki | Level 3 E2E + LocalStorage/durum kontrolü |
| 3 | Klavye kısayolları (oynat / duraklat / skip) | Kısayollar çalışır; odak görünür (2.4.7); gizli odak yok | Klavye navigasyon testi + DOM assertion |
| 4 | Hızlı skip / INavigation (Gamer davranışı) | INP ≤ **200 ms**; skip sonrası anlık geri bildirim | Performance trace + DOM assertion |
| 5 | Çoklu görev (oyun + müzik, uzun oturum) | Bellek/gecikme bozulmaz; CLS ≤ **0.1**; çalma kesintisiz | Trace + oturum dayanıklılık testi |
| 6 | Tür filtresi + playlist ("Gaming Mode" vb.) | Filtre çalışır; sıralama korunur; tür aralığı (P4.4/P4.5) içinde | DOM assertion + filtre testi |
| 7 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç < 2 sn; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` |
| 8 | Veli onayı adımı YOK (16 yaş) | Satın alma akışı veli onayı istemez; ödeme adımı senaryoda işaretli | Level 3 E2E + manuel (P6.4 `⚠️ DERIVED` eşiği) |
| 9 | Favori / playlist paylaşımı (Discord) | Görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü |
| 10 | Bağlantı yavaşlatma (ev/oyun trafiği) | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace (P8.3 `VERIFIED`) |
| 11 | Hata sayfaları (404 / kopma) | Hızlı kurtarma (Tekrar Dene); teknik yığın izi görünmez | Manuel + screenshot |
| 12 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.4.7 / 2.4.11 / 2.5.8 ihlali yok | Lighthouse / axe |
| 13 | Kırılma noktası kontrolü (mobil görünüm) | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) düzeni bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) |
| 14 | Masaüstü breakpoint kontrolü | Banka breakpoint'lerinde (P8.5) düzen bozulmaz | Playwright `setViewportSize` + screenshot (P8.5 `VERIFIED`) |
| 15 | Çevrimdışı / oturum devamlılığı | Offline liste erişilebilir; yeniden bağlanınca sıralama korunur | CDP `Network.emulateNetworkConditions` + manuel |
| 16 | Reklam/kesinti durum geri bildirimi | Kesinti öncesi uyarı; kör nokta yüklenme yok (2.4.11) | Manuel + a11y audit |
| 17 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; masaüstü mini player odak döngüsünde | Klavye navigasyon testi + DOM assertion |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, ulaşım, rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (16) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (16 yaş eşiği) + gerekçe | Politika sonucu (türetilmiş) | research-bank P6.3/P6.4 (TMK 18 · COPPA 13 · GDPR 16) | — | `⚠️ DERIVED` + dayanak: P6.4 son satır |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Big Five puanları (5×0-100) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 8 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 9 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 10 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 11 | Mood küme adı (Gamer) | Vault referanslı atama | [[mood-taxonomy]] §3.3 satır 16 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 12 | Mood küme tanımı alıntısı ("Oyun içi/akış müzikleri; çoklu görev ve düşük gecikme beklentisi") | Vault verisi | [[mood-taxonomy]] §3.3 satır 16 | — | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 13 | Mood Big Five eğilimi ("Yüksek A, yüksek O") | Vault verisi (kurgusal eğilim) | [[mood-taxonomy]] §3.3 satır 16 | — | `Kaynak: [[mood-taxonomy]] (vault verisi; kurgusal)` |
| 14 | Gamer küme BPM notu | Doğrulanmadı (bu satırda BPM yok) | mood-taxonomy §3.3 satır 16 (BPM alanı boş) | research-bank P4 | `⚠️ VERIFICATION REQUIRED` — uydurma aralık yazılmadı |
| 15 | BPM tür aralıkları (Electro 90-130 · Dance 110-135 · Hip Hop 80-130 · Pop 80-120) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 16 | BPM alt tür aralığı (trap 70-110) | Gerçek-dünya (ikincil) | nevamuzik.com.tr | vocuno.com/tr/bpm-algilayici | `Kaynak: [k1] + [k2]` (P4.5 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 17 | Lo-fi / chillhop BPM aralığı (bankada tür satırı yok) | Doğrulanmadı | research-bank P4.4 (lo-fi YOK) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 18 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 19 | Favori sanatçı adları (Alan Walker, C418, Toby Fox, NCS, BMTH) | Persona tercihi (kurgusal); sanatçı realitesi P4 kapsamı dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 20 | Okul türü: Anadolu Lisesi (4 yıl) | Gerçek-dünya | MEB OGM "Okul Türleri" PDF | MEB "Türkiye Eğitim Sistemi" raporu | `Kaynak: [k1] + [k2]` (P2.7 `VERIFIED`) |
| 21 | Cihaz: Gaming PC (kendi toplama — spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 22 | Cihaz: Samsung Galaxy A54 (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı (yalnız A34 VERIFIED) |
| 23 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 24 | Tarayıcı/OS sürümü, internet hızı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 25 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 26 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 27 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 28 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 29 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Breakpoint listesi (320×568 … 2560×1440) | Gerçek-dünya | playwright.dev/docs/emulation | mintlify mobile-emulation | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | `persona_id` biçimi (`CM-KY-16-GAM-BUR` ↔ şablon `CM-XX-YY-ZZZ-XXX`) | Şablon formatı uyumu | görev tablosu | persona-template §3.5.1 | `Kaynak: kurgusal (atama); biçim §3.5.1 ile uyumlu (2026-09-26 düzeltme)` |
| 32 | Eski dosyadaki yüzdeler/istatistikler (% tür dağılımı, % premium, saat istatistikleri) | Bankada YOK | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 33 | Aile/gelir/kan grubu/burç satırları | Kapsam dışı (bu tur şablonu) | persona-template §3.5 (alan yok) | — | Yazılmadı — §7.2 |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`kaan-yildiz-gamer.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; 16 altı için veli onayı işaretli | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Gaming PC çözünürlüğü = 1920×1080` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Bu şarkı 128 BPM` | Tür aralığı (P4.4/P4.5, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.3 satır 16 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/genc-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır | Yalnız ilk üretimde |
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
| Persona-özel eşik uydurma | "Kaan için INP 150 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

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
- [ ] 16 yaş ve üzeri: `veli_onayı_gerekli: false` + gerekçe (eşik P6.4 `⚠️ DERIVED`; TMK 18 / COPPA 13 / GDPR 16; "KVKK 16" YANLIŞ)
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
| Kaynak & Doğrulama satırı | 33 |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.3 satır 16) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 14 | 16 | [[personas/index]] §6.3 kazanır |
| Doğum tarihi | 30 Ekim 2012 | 30 Ekim 2009 | Yaş 16 ile tutarlılık (index) |
| Okul sınıfı | 8. sınıf (LGS hazırlık) | 11. sınıf (yaş 16 ile tutarlı) | Yaş index'ten geldi → sınıf yaşa göre uyarlandı |
| Mood adı | Gamer | Gamer | Çakışma YOK — [[personas/index]] §6.3 ile tutarlı |
| Gamer kümesi ×2 (Kaan, Çınar A.) | Tek dosya varsayımı | Aynı kümede 2 persona | Çınar bu üretim turunda YOK; Kaan tek üretilen — test odağı: düşük gecikme + arka plan çalma + kısayollar (mood-taxonomy §3.3 satır 16) |
| Birincil cihaz | Gaming PC (kendi toplama) + Samsung Galaxy A54 (spec'siz) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı (yalnız A34 VERIFIED) |
| BPM aralığı | 80-160 BPM (kişisel/kaynaksız) | Tür aralıkları P4.4/P4.5 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 |
| `persona_id` biçimi | Eski dosyada ID alanı yok | `CM-KY-16-GAM-BUR` — şablon `CM-XX-YY-ZZZ-XXX` (XX=KY, YY=16, ZZZ=GAM, XXX=BUR) | 2026-09-26 tarihinde şablon biçimine düzeltildi; atama görev tablosu/kataloğu korunur |
| Eski AI rol kartı iddiası ("Spotify TR 2025'te oyun müzikleri kategorisi yok") | Kaynaksız iddia | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |
| Yüzdeler/istatistikler (% tür, % premium, saat dağılımı) ve aile/gelir/kan grubu/burç | Eski dosyada mevcut | yazılmadı | Bu tur şablonu §3.5 kapsam dışı |
| İkincil mood (Enerjik — online) | "İkincil Mood — Enerjik" | İkincil atama yok | [[personas/index]] §6.3 tekil küme atar |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı, yaş index §6.3'e göre düzeltildi (14→16), sınıf yaşa göre uyarlandı; veli_onayı=false + gerekçe; Gamer kümesi ×2 notu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/genc-erkek/kaan-yildiz-gamer
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode