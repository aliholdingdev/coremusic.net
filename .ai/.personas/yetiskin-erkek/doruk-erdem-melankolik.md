---
title: "CoreMusic — Persona: Doruk Erdem"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-erkek/doruk-erdem-melankolik"
updated: 2026-09-26
group: yetiskin-erkek
age: 39
mood: Melankolik
persona_id: CM-DE-39-MEL-ANT
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Doruk Erdem

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-erkek** segmentinin **Melankolik** kümesini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 39 yaşındaki emekli bir subayın — derin dinleyen, gece ve yalnız dinleyen bir kullanıcının — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Melankolik, §3.2.3), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]] §6.3.
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-erkek |
| Birincil mood | Melankolik ([[personas/index]] §6.3 ataması — satır 68) |
| Test odağı | Karanlık tema, keşif algoritması, düşük etkileşim, metin ölçekleme, yavaş ağ ([[mood-taxonomy]] §3.2.3 test etkisi) |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Karanlık tema / gece dinleme (21:00-22:30 rutini) | Level 2 + 3 | Düşük ışıkta kontrast (1.4.3 / 1.4.11) |
| Büyük metin / metin ölçekleme (%200) | Level 2 + 3 | Yoğun okuma alışkanlığı; 1.4.3 |
| Klavye gezinme & dokunma hedefi boyutu | Level 2 + 3 | Sade/arayüz beklentisi; 2.4.7 · 2.4.11 · 2.5.8 |
| Yavaş ağ (Slow 4G) kurtarma | Level 2 + 3 | Sabırlı ama tahammülsüz hata metnine; P8.3 |
| Keşif / öneri davranışı (düşük etkileşim) | Level 2 | Melankolik küme: az tıklama, derin dinleme |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (6 sütunlu: Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 600 satırlık psikoloji/aile/rutin detayı bu şablonda yasaktır; askerlik rütbesi ve okul bilgisi (X-6), gelir/maaş/yüzde istatistikleri (§4.5), 50+ yaş grubu istatistikleri (X-4) ve sağlık verisi (P6.5) **yazılmaz**; bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.3 | Melankolik küme adı + tanım alıntısı |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Doruk Erdem | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 39 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 3 Mayıs 1987 | Kaynak: kurgusal (persona verisi; yaş 39 ile tutarlılık için türetildi — eski dosyada 3 Mayıs 1981, §7.2 çelişki) |
| **Cinsiyet** | Erkek | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | Antalya / Muratpaşa / Konyaaltı | Kaynak: kurgusal (persona verisi) |
| **Meslek** | Emekli Subay (2023'ten beri emekli; yarı zamanlı güvenlik danışmanlığı kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Eğitim / Okul** | Yazılmadı — askeri okul ve üniversite detayları bu turda kapsam dışı (X-6) | `⚠️ VERIFICATION REQUIRED` (research-bank X-6) |
| **Ulaşım** | Kendi aracı (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (gerçek adla sade profil) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-DE-39-MEL-ANT` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `DE` | Ad + soyad ilk harfleri (Doruk Erdem → D, E) |
| `YY` | `39` | Yaş 2 hane (39 → `39`) |
| `ZZZ` | `MEL` | Mood tür kodu (Melankolik → MEL) |
| `XXX` | `ANT` | Şehir kodu (Antalya → ANT) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → `Kaynak: [k1] + [k2]`, research-bank P6). Rütbe, askeri okul ve sağlık gibi hassas alanlar **yazılmamıştır** (X-6 / P6.5 → §7.2).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki sağlık ve aile ayrıntıları (diz sakatlığı, reçete, Alzheimer, gelir) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 182 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | Atletik-orta, düzgün duruşlu siluet (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kısa siyah (seyrelen), kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Sade ve düzenli: kot + gömlek/polo; profil fotoğrafında tek başına sahil karesi | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Yoğun okuma alışkanlığı + gece/düşük ışıkta dinleme → büyük metin, düşük ışıkta yüksek kontrast, sade düzen beklentisi | Kaynak: kurgusal (persona verisi); WCAG 1.4.3 (≥4.5:1) kriteri: `Kaynak: [k1] + [k2]` |

*Sağlık verisi notu:* Eski dosyadaki sağlık ayrıntıları (göz reçetesi, eklem rahatsızlığı, aile hastalığı vb.) 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 30 | İçe dönüktür; insan yönetmeyi mesleğinde öğrenmiştir ama yorulur; emekli olduktan sonra yalnızlığı ve sessizliği seçmiştir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | Disiplinli ama anlayışlıdır; otoriter duruş ile ailesine karşı yumuşak kalp arasında denge kurar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 90 | Askeri disiplin kalıcıdır: dakiktir, sözünü tutar, görevini aksatmaz; emeklilikte de rutinini korur. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 40 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 40 = melankolik tarafı baskın: yas ve yalnızlık duygusunu derin yaşar, müziği ifade kanalı olarak kullanır. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 50 | Rutini sever, ani değişiklikten hoşlanmaz; buna karşılık entelektüel merakı (tarih, strateji, felsefe) orta-yüksektir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir**; eski dosyadaki /10 değerlerinin ×10 karşılığıdır |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| **Ters kodlama (zorunlu)** | N boyutu **ters koddur**: IPIP-NEO-120'de yüksek N = yüksek nörotisizm = **düşük** duygusal denge; bu dosyada puan 40 = "melankolik/düşük denge tarafı" olarak yorumlanır (kod adı değil, yorum yönü tersidir) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Melankolik ([[personas/index]] §6.3 ataması; küme: [[mood-taxonomy]] §3.2.3) |
| **İkincil küme** | Yok (eski dosyanın "Baba / Koruyucu" ikinciliği mood-taxonomy §3 listesinde ayrı küme olarak YOK — "Baba" zaten Emre'ye atanmış → §7.2 çelişki kaydı) |
| **Küme tanımı (alıntı)** | "Derin dinleyici; şarkı sözü okur, az etkileşim kurar, gece ve yalnız dinler" ([[mood-taxonomy]] §3.2.3 satır 163) |
| **Big Five eğilimi (küme)** | "Yüksek Duygusal Denge/N (ters kodlama: yüksek puan = düşük denge), orta A" ([[mood-taxonomy]] §3.2.3 — §1.2 toplu `⚠️ VERIFICATION REQUIRED` kapsamındadır) |
| **Tetikleyici durumlar** | Gece 21:00-22:30 yalnız dinleme; yas/nostalji anlarında türkü-arabesk arayışı; karmaşık/arabalıktan rahatsız olma; reklamla bölünme |
| **UI etkisi** | Test etkisi: "Karanlık tema, keşif algoritması, düşük etkileşim" + UI tercihi "Karanlık tema, minimalist, az animasyon" ([[mood-taxonomy]] §3.2.3 satır 167-168) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Melankolik); ikincil eski dosyada "Baba / Koruyucu" olup listeye yeni küme eklenmedi → §7.2'de çelişki olarak saklandı |
| BPM/tür iddiası | mood-taxonomy §3.2.3 "60-90 BPM" ve tür sıralaması **eski taksonomi** → `⚠️ VERIFICATION REQUIRED` (§1.2); yalnız küme **adı** kurgusal tasarım kararı olarak taşınır |
| Test etkisi | Karanlık tema + düşük etkileşim + keşif algoritması (ADR-023 satır 16 ile mood-geçiş senaryoları) |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Gece / yalnız dinleme | Karanlık temada okunabilirlik korunur; parlaklık göz yormaz; metin ≥ **4.5:1** (1.4.3) | Manuel + kontrast denetimi |
| 2 | Düşük etkileşim (az tıklama) | Ana akış 2-3 dokunuşta biter; menü derinliği az | Level 2 + DOM assertion |
| 3 | Derin dinleme (uzun oturum) | Çalma kesintisiz; odak kaybolmaz (2.4.7 · 2.4.11) | Level 3 E2E + klavye testi |
| 4 | Nostalji / arşiv arayışı | Arama ve filtreler hızlı (INP ≤ **200 ms**); yazım hatası toleransı | DOM assertion + trace |
| 5 | Sade düzen beklentisi | Kalabalık/karmaşık görünüm yok; hata metni suçlayıcı değil | Manuel + screenshot |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Türk halk müziği (türkü) 2) Özgün / protest müzik 3) Slow-arabesk (belirli isimler) 4) Klasik batı müziği & film müzikleri 5) Alternatif rock (seçme) | Kaynak: kurgusal (persona verisi) |
| **Favori Türk sanatçılar (5)** | Neşet Ertaş, Ahmet Kaya, Zülfü Livaneli, Ferdi Tayfur, Müslüm Gürses | Kaynak: kurgusal (persona verisi — tercih); Müslüm Gürses ve Ferdi Tayfur'un icracı listesi research-bank P4.6 `VERIFIED`, diğerleri P4 dışı → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **Favori yabancı sanatçılar (5)** | Ennio Morricone, Hans Zimmer, Beethoven, Leonard Cohen, Johnny Cash | Kaynak: kurgusal (persona verisi); gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | **YOK** — Doruk'un türleri (türkü, özgün, arabesk, klasik, film müziği) research-bank P4.4/P4.5 listesinde **yer almıyor**; bankada VERIFIED tür aralığı bulunmayan tür için sayı yazılmaz | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 — 17 tür listesi bu türleri içermiyor) |
| **BPM (referans — platform geneli)** | Pop **80-120** BPM (CoreMusic pop kataloğu için referans; Doruk'un birincil türü DEĞİL) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr → `Kaynak: [k1] + [k2]` (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM (mood-taxonomy notu)** | Melankolik küme için "60-90 BPM" — eski taksonomi değeri, research-bank'ta doğrulanmadı | `⚠️ VERIFICATION REQUIRED` ([[mood-taxonomy]] §1.2 + §3.2.3) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Yavaş-orta tempo (kurgusal tercih; sayısal BPM yazılmadı — P4 dışı herhangi bir BPM değeri `⚠️` gerektirir) | Kaynak: kurgusal (persona verisi) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 06:30-07:30 (sahil yürüyüşü — podcast/klasik), 21:00-22:30 (çalışma odası = "müzik saati") · Hafta sonu 21:00-23:00 + araba içi (sanat müziği/slow radyo) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic henüz denenmemiş; rakip aile paketi mevcut (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Düşük: arşiv derinliği (orijinal kayıt, tam diskografi) görünürse keşfeder; sosyal paylaşım yapmaz | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.3 (düşük etkileşim) |
| **Premium olasılığı** | Orta — aile paketi alışkanlığı var; "reklamsız + orijinal arşiv" görürse geçer (kurgusal) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Türk halk müziği (türkü) | Babasının sesiyle özdeşleşmiş tür; "bozkırın sesi" onun ruhunun sesidir |
| 2 | Özgün / protest müzik | Gençliğinin idealist dönemini hatırlatır |
| 3 | Slow-arabesk | Acının müzikteki karşılığı; gece yalnız dertleştiği ses |
| 4 | Klasik batı müziği & film müzikleri | Törenlerde duyduğu marş/senfoni; sinemadan Shazam'ladığı besteler |
| 5 | Alternatif rock (seçme) | Melankolik/akustik ağırlıklı alternatif parçalar (P4.6: Teoman "melankolik, akustik ağırlıklı" `VERIFIED`) |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 06:30-07:30 | Konyaaltı sahil yürüyüşü | Podcast / klasik müzik |
| Hafta içi | 09:00-12:30 | Danışmanlık ofisi (haftada 3 gün) | Müzik yok (odak) |
| Hafta içi | 14:00-17:00 | Serbest: kitap, ev işleri | Bazen arka planda türkü |
| Hafta içi | 21:00-22:30 | Çalışma odası — MÜZİK ZAMANI | Türkü, arabesk, film müzikleri |
| Hafta sonu | 21:00-23:00 | Uzun dinleme oturumu | Alternatif / klasik |
| Her gün | Araba içi | Yolculuk | Sanat müziği / slow radyo |

*Not: BPM satırları yalnız platform referansı düzeyindedir (P4.4 VERIFIED ikincil); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Doruk türleri için VERIFIED BPM aralığı research-bank'ta bulunmadığından sayı yazılmamış, `⚠️ VERIFICATION REQUIRED` etiketi kullanılmıştır; mood-taxonomy §3.2.3 "60-90 BPM" de birincil ölçüm değildir → `⚠️ VERIFICATION REQUIRED`.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | Samsung Galaxy S23 Ultra (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS** | Android sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Mobil tarayıcı (sürüm kurgusal — P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ev kablolu internet + mobil veri (marka/hız kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Kulaklık** | Kablosuz kulaklık (kurgusal) — model/spec yok | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Ev ses sistemi** | Eski analog pikap + amfi (kurgusal aile yadigârı) — model/spec iddiası yazılmadı | Kaynak: kurgusal (persona verisi) |
| **Tema** | dark (gece dinleme varsayılanı — kurgusal; mood: [[mood-taxonomy]] §3.2.3 "Karanlık tema") | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde 3-4 saat (kişisel — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 6/10 — temel mobil kullanım, ayarlar karmaşık gelirse bırakır | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar** | Araç multimedyası, ev televizyonu — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); gece/düşük ışıkta uzun okuma alışkanlığı | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) · hover/focus ipucu kapatılabilir (1.4.13) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu) | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; ince motor becerisi iyi | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürüklemesiz tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | odak yüksek, multitasking düşük; kısa ve net akış ister | Yardım bağlantısı tutarlı yerde (3.2.6) · gereksiz bilgi tekrarı yok (3.3.7) · erişilebilir kimlik doğrulama (3.3.8) · odak görünürlüğü 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Gerçek sağlık verisi yazılmaz → her kısıt **kurgusal olmak zorunda** (research-bank P6.5) |
| Renk kombinasyonu | Karanlık tema renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Melankolik derin dinleyicidir: şarkı sözü okur, gece ve yalnız dinler; müziği "dinlemek" değil "hissetmek" için kullanır.
- Sade düzen bekler: kalabalık arayüz, gereksiz adım ve uzun animasyon onu yorar.
- Disiplinli ve dakiktir: sözünde durur, rutinini (21:00 müzik saati) bozan şeylere tahammülsüzdür.
- Nostaljiktir: arşiv derinliği, orijinal kayıt ve tam diskografi görürse sadık kalır.
- Sosyal paylaşım yapmaz; reklamla bölünmek istemez, ama sabırlıdır — hata metni suçlayıcıysa güveni kırılır.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Aile bütçesi ortak karar; "reklamsız + orijinal arşiv" faydasını görünce geçer | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Yüksek (~10 sn — uzun okuma alışkanlığı); ama gereksiz adım tekrarında sıkılır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Sakin: hata kodunu not alır, sade çözüm adımı ister; teknik yığın izni görürse güven kaybı | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Düşük (3/10) — "rahatsız edilmeyi" sevmez; reklamsız deneyim kritiktir | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Adım adım + neden-sonuç; kısa kılavuz kabul eder, video izlemez | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Silah arkadaşları/eş dost ile WhatsApp'tan tek tıkla paylaşır; kendisi pek paylaşmaz | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | 23:00 sonrası tek dokunuşlu sakin akış ister; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Sade düzen + tutarlı konum + net fiyat/üyelik + reklamsızlık → güvenir; belirsizlikte ödünleşmez | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.3 (Melankolik) |

*Erişilebilirlik etkisi (özet):* gece/düşük ışıkta dinleme + yoğun okuma → metin ≥ **4.5:1** (1.4.3), UI ≥ **3:1** (1.4.11), dokunma hedefi ≥ **24×24 CSS px** (2.5.8), yardım konumu tutarlı (3.2.6), giriş adımları tekrarsız ve erişilebilir (3.3.7 · 3.3.8), odak kaybolmaz (2.4.7 · 2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Doruk Erdem'sin. 39 yaşındasın, Antalya Konyaaltı'nda yaşıyorsun, emekli subaysın ve yarı zamanlı güvenlik danışmanlığı yapıyorsun.
Melankolik ama güçlü bir kişiliğin var: içe dönüksün, yalnızlığı ve sessizliği seçmişsin; askerlikten gelen disiplin seni dakik ve sözünde tutuyor.
Müzik zevkin: Türk halk müziği, özgün/protest müzik, slow-arabesk, klasik batı müziği ve film müzikleri; seçme alternatif rock. En sevdiklerin: Neşet Ertaş, Ahmet Kaya, Zülfü Livaneli, Ferdi Tayfur, Müslüm Gürses; yabancılardan Morricone, Zimmer, Beethoven, Cohen, Johnny Cash.
Telefonun (model spec'i doğrulanmadı); evde analog pikap/amfi ve aracın multimediası var ama spec'leri doğrulanmadı; teknoloji seviyen 6/10. Karanlık temayı, sade düzeni ve büyük metni seversin.
Görme / işitme / hareket kısıtların yok; ama gece düşük ışıkta uzun süre okuduğun için yüksek kontrast ve büyük metin beklersin.
Sabır eşiğin yüksektir (~10 sn); gereksiz adım tekrarını, karmaşık menüyü ve suçlayıcı hata metnini sevmezsin.
Test edilecek akışlar: gece/karanlık tema, metin ölçekleme (%200), klavye gezinme ve odak, dokunma hedefi, yavaş ağ (Slow 4G) kurtarma, arama/keşif, reklamsız premium akışı, erişilebilirlik denetimi.
Davranış notları:
- Arşiv derinliği (orijinal kayıt, tam diskografi) görünürse keşfedersin; sosyal paylaşım ve trend listeleri seni ilgilendirmez.
- Karanlık temada metin okunmazsa veya menü karmaşıklaşırsa uygulamayı bırakırsın.
- Reklamla bölünürsen güvenin düşer; hata ekranında sade Türkçe ve net adım beklersin.
- Türkçe karakterli içerikte bozulma (mojibake) görürsen güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; yer tutucu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, sakin ve net ton — melankolik persona uzun, gereksiz coşkulu anlatım kabul etmez |
| Yasaklı veri | Prompt'ta rütbe/okul/sağlık/gelir verisi yok; tüm kimlik kurgusaldır (X-6 / P6.5) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme (gerçek cihaz: Samsung Galaxy A34 5G, 1080×2340) | LCP ≤ **2500 ms**; yerleşim kayması yok, CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | — |
| 2 | Ağı Slow 4G'ye kısıtla, sayfayı yeniden yükle | İlk içerik **1 sn** içinde görünür; ağ hatası yok | Network throttling + trace | Slow 4G **150 ms / 1.6 Mbps / 750 Kbps** (P8.3 `VERIFIED`) · FCP **1 sn** (P8.1 `VERIFIED`) | — |
| 3 | Metin ölçekleme: tarayıcıda %200 zoom | Yatay kaydırma yok; türkü kartları ve menü kırpılmaz | Tarayıcı zoom + screenshot | — (P8 dışı) | 1.4.3 (`VERIFIED`) |
| 4 | Sistem yazı boyutunu 200% yap | Metin okunur kalır; satır taşması bozulmaz | Cihaz erişilebilirlik ayarı + screenshot | — (P8 dışı) | 1.4.3 (`VERIFIED`) |
| 5 | Gece / düşük ışık senaryosu (karanlık tema) | Metin ≥ **4.5:1**, UI/grafik ≥ **3:1**; parlaklık göz yormaz | Manuel + kontrast denetimi | — (P8 dışı) | 1.4.3 · 1.4.11 (`VERIFIED`) |
| 6 | Sadece klavye ile gezinme (menü → liste → player) | Odak her adımda görünür; gizli odak yok | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 (`VERIFIED`) |
| 7 | Shift+Tab ile geri gezinme | Odak sırası görsel sıra ile aynı; sabit başlık odak ÖNDE tutar | Klavye sırası kaydı + screenshot | — (P8 dışı) | 2.4.11 (`VERIFIED`) |
| 8 | Dokunma hedefi ölçümü (liste satırı, kalp ikonu) | Hedef ≥ **24×24 CSS px**; tek dokunuşta yanıt | DOM ölçümü + dokunma testi | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 9 | Çalma listesi sıralama (sürükleme) | Tek dokunuş alternatifi sunulur; sürüklemek zorunlu değil | Manuel + DOM assertion | — (P8 dışı) | 2.5.7 (`VERIFIED`) |
| 10 | Arama/ipucu alanına hover + focus | İpucu gösterilip kapatılabilir; içerik kaybolmaz | Manuel + DOM assertion | — (P8 dışı) | 1.4.13 (`VERIFIED`) |
| 11 | Yardım bağlantısının konumunu sayfalar arasında karşılaştır | Yardım her sayfada aynı konumda | DOM assertion | — (P8 dışı) | 3.2.6 (`VERIFIED`) |
| 12 | Giriş akışında bilgi tekrarı | Önceden girilen bilgi ikinci kez istenmez | Form assertion | — (P8 dışı) | 3.3.7 (`VERIFIED`) |
| 13 | Kimlik doğrulama (giriş) | Bilişsel test (hafıza/bulmaca) yok; kısa ve net adım | Form assertion + a11y audit | — (P8 dışı) | 3.3.8 (`VERIFIED`) |
| 14 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log | INP ≤ **200 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 15 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP **1 sn** (P8.1 `VERIFIED`) | — |
| 16 | Türkçe karakter içeriği (ç, ğ, ı, İ, ö, ş, ü) | Sanatçı/türkü adlarında karakter bozulmaz (mojibake yok) | `vault-utf8-writer verify` + ekran görüntüsü | — (P8 dışı) | — |
| 17 | Hata sayfaları / yavaş ağ kurtarma (404, kopma) | Türkçe, kısa, yol gösteren; suçlayıcı metin ve yığın izni yok | Manuel + screenshot | Slow 4G profili (P8.3 `VERIFIED`) | 3.3.8 (`VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Öncelikli senaryolar: büyük metin/ölçekleme (3-4), klavye/hedef boyutu (6-9), düşük ışık (5), yavaş ağ (2, 17). Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); WCAG kodları yalnız P5.2/P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, medeni durum, çocuk varlığı, ulaşım, rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (39), grup (yetiskin-erkek), mood (Melankolik) ataması | Kurgusal (index ataması) | [[personas/index]] §6.3 (satır 68) | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Yaş çelişki: **45** (eski salt-okunur dosya) / **39** (index SSOT) | Vault içi çelişki | eski salt-okunur persona dosyası (üçlü blok, satır 3) | [[personas/index]] §6.3 (satır 429) | `⚠️ VERIFICATION REQUIRED` — iki değer yazıldı; §7.2 kayıtlı |
| 4 | Doğum tarihi çelişki: **3 Mayıs 1981** (eski, 45 yaş) / **3 Mayıs 1987** (39 yaş ile tutarlı) | Vault içi çelişki | eski dosya (satır 13) | yaş 39 ile tutarlılık türetmesi | `⚠️ VERIFICATION REQUIRED` — iki değer; §7.2 kayıtlı |
| 5 | Meslek "Emekli Subay" + Şehir "Antalya / Konyaaltı" | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Rütbe/askeri okul/eğitim detayı (Kurmay Binbaşı, Kara Harp Okulu, bölüm) yazılmadı | Kapsam dışı (EXCLUDED) | research-bank §6.4 X-6 | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 7 | Maaş/gelir/yüzde istatistikleri + 50+ yaş grubu istatistikleri yazılmadı | Bankada YOK (EXCLUDED) | research-bank P1-P8 kapsamı | research-bank §6.4 X-4 | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 8 | Sağlık verisi yazılmadı (reçete, rahatsızlık, aile hastalığı) | Kural uygulaması (m.6 hassasiyeti) | research-bank P6.5 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı; kısıtlar kurgusal |
| 9 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal (persona verisi)` |
| 10 | "KVKK 16 diyor" ifadesi YANLIŞ; TMK m.11 = 18 · GDPR m.8 = 16 · COPPA = 13 | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 11 | Fiziksel özet satırları (boy, siluet, giyim, kısıt) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 12 | Big Five puanları: E 30 · A 60 · C 90 · N 40 · O 50 | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 13 | 0-100 normalize yöntemi (eski /10 → ×10) | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 14 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) + N ters kodlama yorumu | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 15 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 16 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 17 | Mood küme adı (Melankolik) + §3.2.3 ataması | Vault referanslı atama | [[mood-taxonomy]] §3.2.3 (satır 159) | [[personas/index]] §6.3 | `Kaynak: kurgusal (persona verisi; ad mood-taxonomy §3 listesinden)` |
| 18 | Mood tanımı alıntısı + Big Five eğilimi + test etkisi (karanlık tema, düşük etkileşim) | Vault verisi (toplu ⚠️ altında) | [[mood-taxonomy]] §3.2.3 (satır 163-168) | [[mood-taxonomy]] §1.2 | `Kaynak: kurgusal (persona verisi; vault alıntısı — BPM/tür iddiası ⚠️)` |
| 19 | İkincil mood "Baba / Koruyucu" yazılmadı | Listede yok | eski salt-okunur dosya (satır 81) | mood-taxonomy §3 ("Baba" Emre'de, §2.2) | `⚠️ VERIFICATION REQUIRED` — yazılmadı; §7.2 çelişki |
| 20 | Müzik türleri (5, sıralı) + gerekçe tabloları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | Favori sanatçılar (Neşet Ertaş, Ahmet Kaya, Zülfü Livaneli, Ferdi Tayfur, Müslüm Gürses + 5 yabancı) | Persona tercihi (kurgusal); icracı listesi kısmen bankada | research-bank P4.6 (Müslüm Gürses, Ferdi Tayfur, Orhan Gencebay `VERIFIED`) | — | `Kaynak: kurgusal (persona verisi)` + P4 dışı bilgi: `⚠️ VERIFICATION REQUIRED` |
| 22 | Doruk türleri için VERIFIED tür-BPM aralığı (türkü/özgün/arabesk/klasik/film/alternatif) | Doğrulanmadı | research-bank P4.4 (17 tür listesi — bu türler yok) | research-bank P4.5 (yalnız rap/trap) | `⚠️ VERIFICATION REQUIRED` — sayı yazılmadı |
| 23 | Referans tür BPM: Pop **80-120** (platform geneli — birincil türü değil) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 24 | Melankolik küme BPM notu "60-90 BPM" | Eski taksonomi (bankada yok) | [[mood-taxonomy]] §1.2 (toplu ⚠️) | [[mood-taxonomy]] §3.2.3 (satır 165) | `⚠️ VERIFICATION REQUIRED` — doğrulanmadı |
| 25 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 26 | Cihaz: Samsung Galaxy S23 Ultra (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 27 | Test cihazı: Samsung Galaxy A34 5G — 1080 × 2340 (FHD+) | Gerçek-dünya | gsmarena.com/samsung_galaxy_a34-12074.php | samsung.com (resmi ürün sayfası) + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 28 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 29 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 30 | Karanlık tema renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 31 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn + Slow 4G (150 ms / 1.6 Mbps / 750 Kbps) | Gerçek-dünya | web.dev/articles/vitals | github.com/GoogleChrome/lighthouse (docs/throttling.md) + developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Persona-özel LCP/INP hedefleri (X-10) + P5 dışı WCAG kodları (1.4.5, 2.4.3 … X-7) yazılmadı | EXCLUDED | research-bank §6.4 X-10 | research-bank §6.4 X-7 | `⚠️ DERIVED` (eşik gerekirse) — persona'ya özel eşik/ek kod YOK |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`doruk-erdem-melankolik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; rütbe/sağlık/gelir verisi yazılmaz (X-6 / P6.5) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `Yaş: 45` (eski dosya) | `Yaş: 39` ([[personas/index]] §6.3 SSOT) + çelişki §7.2'de |
| `Türkü 74 BPM` / `şarkı X BPM` | Tür için bankada VERIFIED aralık yok → `⚠️ VERIFICATION REQUIRED`; şarkı BPM'i: X-2 |
| `KVKK rıza yaşı 16` | `TMK m.11 = 18; GDPR m.8 = 16; COPPA = 13; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Emekli Kurmay Binbaşı / Kara Harp Okulu / maaş aralığı` | `Emekli Subay (kurgusal) + rütbe/okul/gelir: yazılmadı (X-6 / §4.5)` |
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
| 5 | Test Adımları ≥10 (6 sütun) → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/yetiskin-erkek/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır (kuyruk mojibake'li — asla birebir kopyalanmaz); rütbe/okul/sağlık/gelir satırları TAŞINMAZ | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI (yasaklı satırlar hariç) → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 (6 sütun) → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Şablon bloğu dışındaki çift süslü parantezli yer tutucu | §4 bloğu dışında yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Doruk için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Yaş/rütbe eski değer taşıması | 45 / Kurmay Binbaşı ifadeleri | index §6.3'e dön + §7.2 çelişki kaydı |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 12 alanlı frontmatter var (7 zorunlu: `title`, `type`, `category`, `version`, `status`, `authority`, `updated` + 5 ek: `group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`); hedef ≥ 506
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG); metrikler yalnız P8, WCAG kodları yalnız P5
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, yer tutucu kalmamış (yalnız §4 şablon bloğundaki VARIABLE kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (sadece genel kurgusal/zorunlu satır)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5 listesinden (1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8); persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız P4.4/P4.5 VERIFIED tür aralığı ya da `⚠️`; şarkı BPM'i yok (X-2)
- [ ] Yaş 39 (index §6.3); eski 45 değeri yalnız §7.2 çelişki tablosunda; rütbe/okul/sağlık/gelir/50+ istatistik yazılmadı
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] §7.2 Çakışma Kayıtları + §7.3 Değişiklik Geçmişi (append-only) + Authority footer mevcut

### §6.2 Quality Report

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Alan havuzu | 11/11 alt bölüm |
| Test adımı | 17 (asgari 10; 6 sütun) |
| Kaynak & Doğrulama satırı | 32 (28-32 aralığında) |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.3 Melankolik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 45 | 39 | [[personas/index]] §6.3 kazanır (satır 429); iki değer de burada saklı |
| Doğum tarihi | 3 Mayıs 1981 | 3 Mayıs 1987 | Yaş 39 ile tutarlılık için kurgusal türetme |
| İkincil mood | Baba / Koruyucu | Yok | mood-taxonomy §3'te ayrı küme YOK; "Baba" zaten Emre'ye atanmış → yazılmadı, burada saklı |
| Birincil cihaz | Samsung Galaxy S23 Ultra (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı |
| Rütbe & eğitim | Emekli Kurmay Binbaşı; Kara Harp Okulu, Sistem Mühendisliği | "Emekli Subay"; eğitim alanı yazılmadı | research-bank X-6 (üniversite/bölüm kapsam dışı) + rütbe kanonik değil |
| Sağlık ayrıntıları (m.6) | Göz reçetesi, eklem rahatsızlığı, aile hastalığı | yazılmadı | P6.5 — sağlık verisi kurgusal olmak zorunda; bu dosyada silindi |
| Gelir / maaş / yüzde istatistikleri | Maaş aralığı, aile geliri, %'li demografik iddialar | yazılmadı | §4.5 kural 4 (bankada yok → yazma) + X-4 (50+ yaş dilimi) |
| Kişisel/şarkı BPM | Şarkı listesi + BPM bağlamı, tür BPM notları | Tür için bankada VERIFIED aralık yok → `⚠️`; şarkı BPM'i yok | §4.5 + X-2 + P4.4 |
| mood-taxonomy BPM | Melankolik küme "60-90 BPM" (eski taksonomi) | `⚠️ VERIFICATION REQUIRED` olarak taşındı | [[mood-taxonomy]] §1.2 toplu etiket |
| Persona-özel test eşiği | Eski dosyada kişisel beklenti cümleleri | Yok; yalnız genel P8 eşikleri (X-10) | research-bank §6.4 X-10 → gerekirse `⚠️ DERIVED` |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı (kuyruk mojibake'li ve yasaklı satırlar içerdiği için birebir kopyalanmadı), yaş index §6.3'e göre düzeltildi (45→39); yetişkin statüsü (veli onayı false), ikincil mood çelişki kaydı, rütbe/okul/sağlık/gelir kırpımı, 6 sütunlu 17 test adımı ve 32 satırlık kaynak tablosu eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-erkek/doruk-erdem-melankolik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
