---
title: "CoreMusic — Persona: Buket Kaya"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-kadin/buket-kaya-enerjik"
updated: 2026-09-26
group: yetiskin-kadin
age: 28
mood: Enerjik
persona_id: CM-BK-28-ENE-IZM
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Buket Kaya

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-kadin (25-45 yaş)** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 28 yaşındaki serbest (freelance) bir grafik tasarımcının — enerjik, keşif odaklı bir dinleyicinin — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Enerjik), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-kadin (25-45) |
| Birincil mood | Enerjik ([[personas/index]] §6.3 ataması) |
| Test odağı | Hızlı navigasyon + anlık geri bildirim + skip performansı (ADR-023 satır 12); keşif, playlist, paylaşım, Türkçe içerik |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Hızlı navigasyon & geri tuşu (ADR-023 satır 12) | Level 2 + 3 | Enerjik küme test etkisi: hızlı navigasyon, anlık geri bildirim, skip performansı |
| Keşif & öneri akışı | Level 1 + 2 | Aktif kaşif: keşfet sayfası, tür bazlı radyo, benzer sanatçı önerisi |
| Playlist oluşturma & paylaşım | Level 2 + 3 | Mood bazlı playlist'ler; Instagram story'ye şarkı paylaşımı |
| Türkçe içerik & mojibake | Level 2 + 3 | Türkçe alternatif rock keşfi; karakter bozulması güven kaybı yaratır |
| Tema & kontrast | Level 2 | Tasarımcı gözü: kırık padding/font hemen fark edilir; 1.4.3 kontrast |
| Yavaş ağ & hata ekranları | Level 2 + 3 | Enerjik/acesi olan ton; uzun bekleme ve suçlayıcı metin yasak |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki üniversite adı, maaş aralığı, yüzdeler ve sağlık/spor ayrıntıları bu şablonda yasaktır (X-6 / 6698 m.6); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5).

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.2 | Küme adı + tanım alıntısı (Enerjik) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Buket Kaya | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 28 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3) |
| **Doğum Tarihi** | 3 Temmuz 1998 | Kaynak: kurgusal (persona verisi; yaş 28 ile tutarlılık için türetildi) |
| **Cinsiyet** | Kadın | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İzmir / Konak / Alsancak | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Üniversite adı YAZILMADI (X-6) · Grafik Tasarımcı (freelance + yarı zamanlı ajans) | Kaynak: kurgusal (persona verisi); üniversite adı: `⚠️ VERIFICATION REQUIRED` (X-6, 6698 m.6) |
| **Ulaşım** | Toplu taşıma + bisiklet (araba yok — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Kurgusal rumuz (persona verisi) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-BK-28-ENE-IZM` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `BK` | Ad + soyad ilk harfleri (Buket Kaya → B, K) |
| `YY` | `28` | Yaş 2 hane (28 → `28`) |
| `ZZZ` | `ENE` | Mood tür kodu (Enerjik → ENE) |
| `XXX` | `IZM` | Şehir kodu (İzmir → IZM) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz. "KVKK 16 diyor" ifadesi **YANLIŞTIR** (GDPR m.8 değeri); doğru çapalar TMK m.11 = 18 · GDPR m.8 = 16 · COPPA = 13 (research-bank P6.4 `VERIFIED`). Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1 → research-bank P6).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (burç, kan grubu, kafein tüketimi, uyku saati, kilo takibi…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 170 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | Orta, atletik-enerjik siluet (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kızıl-kahve uzun saç (genelde atkuyruğu), kahverengi göz | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | Cesur renk kombinasyonları, oversized parçalar; profil fotoğrafında canlı renkli kare (kontrast eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Yok (kurgu) — ama tasarımcı gözü: kırık hizalama/padding anında fark edilir; hızlı tek elle kullanım → büyük hedef beklentisi | Kaynak: kurgusal (persona verisi); WCAG 2.5.8 (≥24×24 CSS px) kriteri: `Kaynak: [k1] + [k2]` |

*Sağlık/spor verisi notu:* Eski dosyadaki sağlık ve spor ayrıntıları 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı** (X-6); persona'da herhangi bir sağlık kısıtı varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 80 | Sosyal enerji kaynağı: partiler/konserler/festivaller onu besler; yalnız uzun süre kalamaz, hızlı iletişim kurar. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 60 | İyi geçinir ama fikirlerinden ödün vermez; "dürüst ve doğrudan" bilinir, grupta fikir lideri olur. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 50 | İş teslimlerine sadıktır ama ev düzeni/planlamada dağınıktır; çoğu işi son dakikada bitirir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 60 | **Yüksek puan = düşük duygusal denge (ters kodlama).** 60 = yoğun yaşar, hızlı toparlanır; proje stresinde gerginleşir ama uzun süre mutsuz kalmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |
| Deneyime Açıklık | O | 90 | Yeni müziğe, yeni şehre, yeni fikre çok açıktır; rutinden nefret eder, sürekli keşif peşindedir. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — eski dosyadaki 10 üzerinden puanlar 10× ile ölçeklendi (kurgusal dönüşüm); IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §7.2'ye kayıtlıdır. |
| Ters kodlama | N boyutu zorunlu ters-kodlu okunur: yüksek puan = düşük duygusal denge (research-bank P7.3); düz puan gibi yorumlanması yasaktır. |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Enerjik |
| **İkincil küme** | Yok (eski dosyanın "Maceracı" ikinciliği mood-taxonomy §3 listesinde YOK → §7.2 çelişki kaydı) |
| **Küme tanımı (alıntı)** | "Yüksek enerjili, sık skip eden, yeni şarkı keşfeden ve playlist oluşturan dinleyici" ([[mood-taxonomy]] §3.2.2) |
| **Tetikleyici durumlar** | Yeni içerik/öneri akışı; enerji düşük hissettiğinde yüksek tempolu müzik; yaratıcı tıkanıklıkta yeni türler; sıradan/uzun bekleme anlarında sıkılma |
| **UI etkisi** | Test etkisi: "Hızlı navigasyon, anlık geri bildirim, skip performansı" + ADR-023 satır 12 (hızlı navigasyon + geri tuşu) ([[mood-taxonomy]] §3.2.2) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Enerjik); ikincil eski dosyada "Maceracı" olup listede yok → §7.2'de çelişki olarak saklandı |
| Test etkisi | ADR-023 satır 12 (hızlı navigasyon, anlık geri bildirim, skip) — [[mood-taxonomy]] §3.2.2 |
| Big Five eğilimi | [[mood-taxonomy]] §3.2.2 kurgusal eğilimi "Yüksek Dışadönüklük (O), yüksek Deneyime Açıklık (A), düşük N" — kod harfleri P7.3 tam adlarıyla çelişir (O≠Dışadönüklük) → `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır"); §3.5.3 puanları P7.3 kodlarına göredir |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Hızlı navigasyon + geri tuşu (ADR-023 satır 12) | Sayfalar arası hızlı geçiş; geri tuşu her adımda görünür ve çalışır | Level 3 E2E + geri tuşu assertion |
| 2 | Sık skip (ardışık parça atlama) | Her atlamada anlık geri bildirim; sırada kayıp yok | DOM assertion + sayaç |
| 3 | Yeni şarkı keşfi | Keşfet akışı önerilerle dolu; boş/uzun bekleme ekranı yok | Level 2 + manuel |
| 4 | Playlist oluşturma | Tek dokunuşla oluşturma; liste kalıcı; kapak görünür | DOM assertion + LocalStorage |
| 5 | Yüksek O (yenilik) | Yeni özellik kartları meraklandırır; değişiklikler şaşırtmaz | Manuel + screenshot |
| 6 | Düşük C (dağınık) | Uzun form/adım tekrarı yorar; tek dokunuşlu akış beklentisi | Form assertion + a11y audit |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Elektronik/Dance 2) Indie Pop/Alternatif 3) Latin/Salsa 4) Türkçe Alternatif Rock 5) World Music/Etnik | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Hey Douglas, Jakuzi, Duman, Mor ve Ötesi, Büyük Ev Ablukada | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **Yabancı sanatçılar (5)** | Tame Impala, FKJ, Rosalía, Kaytranada, Fred again.. | Kaynak: kurgusal (persona verisi); gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| **BPM aralığı (tür)** | Elektronik/Dance için: Dance **110-135** · Electro **90-130** · House **110-128**; Indie Pop/Alternatif için: Pop **80-120** (P4.4) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **BPM aralığı (Türkçe Alternatif Rock / Latin / World)** | P4.4 tablosunda bu türler için aralık YOK | `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 — aralık uydurulmadı) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | 90-130 BPM (enerjik çalışma/dans aralığı — kurgusal tercih) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 08:00 (sabah) · 10:00-18:00 (tasarım çalışması, kesintisiz) · 20:00-22:00 (akşam) · 23:30-01:00 (gece) · Hafta sonu 11:00-14:00 (çarşı/plakçı) + gece eğlencesi | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic paralel kullanımda (Türkçe alternatif keşfi için — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Yüksek: haftalık keşif listelerini kontrol eder, tür bazlı radyo dener, "benzeyenler" önerilerine bakar | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Niteliksel: CoreMusic premium'a henüz geçmedi, paralel kullanım sürüyor (yüzde yazılmadı) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Elektronik/Dance | Tasarım yaparken ritmi yaratıcı akışı besler; festival kültürü |
| 2 | Indie Pop/Alternatif | Niş, karakterli sound'lar; keşif hazzını en çok burada yaşar |
| 3 | Latin/Salsa | Dans müziği; hareket ve özgürlük duygusu |
| 4 | Türkçe Alternatif Rock | Lise yıllarının sesi; hâlâ konserlerine gider |
| 5 | World Music/Etnik | Seyahat merakı; her yolculuktan yeni bir türle döner |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 08:00-09:00 | Sabah rutini | Chill elektronik / indie pop |
| Hafta içi | 10:00-13:00 | Tasarım (derin odak) | Elektronik, FKJ tarzı |
| Hafta içi | 13:00-17:00 | Öğle/ajans işi | Indie alternatif (toplantıda yok) |
| Hafta içi | 20:00-22:00 | Akşam | Pop/dans (dışarıda) · indie (evde) |
| Hafta içi | 23:30-01:00 | Gece çalışması | Elektronik / chill |
| Hafta sonu | 11:00-14:00 | Çarşı, plakçılar | Mekanın müziği |
| Hafta sonu | 22:00-02:00 | Gece eğlencesi | Canlı müzik / dans |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). [[mood-taxonomy]] §3.2.2 Enerjik küme "120-160 BPM" notu eski taksonomi alıntısıdır → `⚠️ VERIFICATION REQUIRED` (research-bank P4.4 kapsamı dışıdır).*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone (eski salt-okunur dosyadan model adı) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport** | Cihaza özel viewport YOK (spec doğrulanmadı); test için banka viewport'u: `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | `⚠️ DERIVED` — türetme: A34 çözünürlüğü ÷ DPR (research-bank P3.1/P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **OS / tarayıcı** | Sürüm doğrulanmadı (mobil tarayıcı, sürüm kurgusal — P3'te tarayıcı sürümü YOK) | `⚠️ VERIFICATION REQUIRED` (X-1); kurgusal bağlam: Kaynak: kurgusal (persona verisi) · research-bank P3.4 |
| **Bilgisayar / tablet** | Tasarım bilgisayarı ve tablet var — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |
| **Kulaklık** | Günlük kablosuz kulaklık + çalışma kulaklığı — model/spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **İnternet hızı** | Ev fiber + mobil veri (marka/hız kurgusal) | Kaynak: kurgusal (persona verisi); research-bank P3.4 (hız verisi paket dışı) |
| **Tema** | light (varsayılan — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Yoğun (iş + sosyal medya — kurgusal; sayı yazılmadı) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 9/10 — yaratıcı profesyonel: tasarım araçlarına hâkim, arayüz değişikliklerine duyarlı | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (ev)** | Akıllı hoparlör yok; ev televizyonu var — spec'siz | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi = `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmedi).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); 20/20 görüş | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); yüksek sesle dinleme alışkanlığı | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; hızlı, tek elle kullanım | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürüklemesiz tek dokunuş (2.5.7) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | enerjik ve acesi olan; rutin işlerde çabuk dağılır | Yardım tutarlı (3.2.6) · gereksiz veri tekrarı yok (3.3.7) · erişilebilir kimlik doğrulama (3.3.8) · odak 2.4.7 + 2.4.11 | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Yetişkin persona — veli onayı gerekmez; yine de tüm persona verisi kurgusaldır (6698 m.5/1, research-bank P6) |
| Hover/focus içerik | Tooltip/bağlam menüsü gibi hover içeriği AA'da 1.4.13 ile denetlenir (P5.4 VERIFIED) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.3/P5.5) |

### Kişilik & Davranış

- Enerjik ve sosyaldir: hızlı konuşur, hızlı karar verir; sessiz/uzun bekleme ortamlarında sıkılır.
- Yaratıcı ve yenilik açıktır: yeni tür, yeni sanatçı, yeni arayüz hepsiyle hemen ilgilenir.
- Tasarımcı gözüyle eleştirir: kırık hizalama, düşük kontrast, yanlış font anında fark edilir ve geri bildirim verir.
- Dağınık-son-dakikadır: uzun formlar ve adım tekrarları C düzeyinde yorar; tek dokunuşlu akış sever.
- Sosyal paylaşım odaklıdır: sevdiği şarkıyı hemen story'ye koyar, sevgilisi/arkadaşlarıyla değiştirir.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Düşük-orta bağlılık: güzel deneyime hemen yönelir, marka değişiminden çekinmez (impulsif — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | Kısa: uzun yükleme/adım tekrarında sıkılır, hızlı akış bekler (kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Suçlayıcı metni sevmez; kısa, yol gösteren hata ister; tasarım hatasını hemen raporlar | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | Düşük — premium kullanıcı olmaya alışmış, şimdilik tolere ediyor (yüzde yazılmadı) | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Görsel ve hızlı: kısa demo + deneme; uzun metin okumaz | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | Arkadaş/sevgiliyle ortak playlist ve story paylaşımı; sosyal onay önemlidir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gece yarısı sonrası tek dokunuşlu, hızlı akış ister; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tutarlı düzen + anlık geri bildirim + net fiyat/abonelik şeffaflığı → güvenir | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] Enerjik kümesi |

*Erişilebilirlik etkisi (özet):* enerjik/hızlı kullanım + tasarımcı hassasiyeti → dokunma hedefi ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), yardım tutarlı (3.2.6), gereksiz tekrar yok (3.3.7), odak görünür (2.4.7/2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Buket Kaya'sın. 28 yaşındasın, İzmir/Alsancak'ta yaşıyorsun, grafik tasarımcısın — freelance ve yarı zamanlı ajans işleriyle çalışıyorsun.
Enerjik bir yapın var: hızlı düşünür, hızlı karar verirsin; sık sık şarkı atlar (skip) ve yeni şarkı keşfetmeyi seversin.
Müzik zevkin: Elektronik/Dance, Indie Pop/Alternatif, Latin/Salsa, Türkçe Alternatif Rock, World Music/Etnik. En sevdiklerin: Hey Douglas, Jakuzi, Duman, Mor ve Ötesi, Büyük Ev Ablukada; yabancı favorilerin: Tame Impala, FKJ, Rosalía, Kaytranada, Fred again...
Telefonun (model spec'i doğrulanmadı); tasarım bilgisayarın, tablet'in ve kulaklıkların var ama spec'leri doğrulanmadı; teknoloji seviyen 9/10 — arayüz değişikliklerini ilk sen fark edersin.
Görme / işitme / hareket kısıtların yok; ama hızlı kullanırsın — büyük dokunma hedefleri, anlık geri bildirim ve tek dokunuşlu akış beklersin.
Sabır eşiğin kısadır: uzun yükleme, gereksiz adım tekrarı ve belirsiz metin seni sıkar; tasarımcı gözüyle kırık hizalama ve düşük kontrastı hemen fark edersin.
Test edilecek akışlar: hızlı navigasyon ve geri tuşu, ardışık skip, keşif, Türkçe arama, SPA devamsızlığı, play, playlist + kapak, paylaşım, favori, tema, yavaş ağ, hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Türkçe karakterli içerikte bozulma (mojibake) görürsen güvenin düşer.
- Kesintisiz müzik + anlık geri bildirim yoksa uygulamayı bırakırsın; geri tuşu kaybolursa kaybolursun.
- Uzun formlar ve tekrarlı veri girişi yorar; tek dokunuşlu akış beklersin.
- Reklam toleransın düşüktür; suçlayıcı veya teknik hata metni görürsen güvenin sarsılır.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, dinamik ve saygılı ton — enerjik persona uzun anlatım kabul etmez |
| Gerçek kişi verisi | Prompt'ta gerçek kişi/üniversite/maaş/sağlık verisi yok; tamamı kurgudur (P6 / X-6) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ana sayfa yükleme (hızlı navigasyon — ADR-023 satır 12) | LCP ≤ **2500 ms**, karşılama/kartlar üstte, CLS ≤ **0.1** | Performance trace + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 2 | Ardışık skip (10 parça atlama) | Her atlamada anlık geri bildirim; sırada kayıp yok | DOM assertion + sayaç | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 3 | Keşif sayfası (yeni çıkanlar + öneri) | Öneri akışı dolu; boş bekleme ekranı yok; kapaklar görünür | DOM assertion + screenshot | LCP ≤ **2500 ms** (P8.1 `VERIFIED`) | 1.4.11 (`VERIFIED`) |
| 4 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; sonuç hızlı; mojibake yok | DOM assertion + `vault-utf8-writer verify` | — (P8 dışı) | — |
| 5 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; CLS ≤ **0.1** | Trace + console log | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 6 | Şarkı çalma (play / duraklat) | Oynatma hızlı başlar; net oynat kontrolü; odak görünür | Trace + console log + a11y audit | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 7 | Playlist oluşturma + kapak seçimi | Tek dokunuşla oluşturma; kapak görünür; liste kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 8 | Paylaşım (story/link) | Paylaşım menüsü anında açılır; iptal geri bildirimi net | Manuel + DOM assertion | — (P8 dışı) | 1.4.13 (`VERIFIED`) |
| 9 | Favori (kalp) ekleme | Anlık görsel geri bildirim; durum localStorage'da kalıcı | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 10 | Tema geçişi (dark / light) | Geçiş müziği kesmez; iki temada da kontrast ≥ **4.5:1** | Manuel + ekran görüntüsü + kontrast denetimi | — (P8 dışı; eşik P5) | 1.4.3 (`VERIFIED`) |
| 11 | Bağlantı yavaşlatma | **150 ms / 1.6 Mbps / 750 Kbps** ("Slow 4G") koşulunda hata yok | Network throttling + trace | Slow 4G profili (P8.3 `VERIFIED`) | — |
| 12 | Hata sayfaları (404 / kopma) | Türkçe, kısa, yol gösteren; yığın izni görünmez | Manuel + screenshot | — (P8 dışı) | 3.3.8 (`VERIFIED`) |
| 13 | Kırılma noktası kontrolü | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | — |
| 14 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 2.5.7 / 2.5.8 ihlali yok | Lighthouse / axe | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 · 2.5.7 · 2.5.8 (`VERIFIED`) |
| 15 | Yardım erişimi + tekrarlı giriş | Yardım aynı yerde (3.2.6); aynı veri iki kez istenmez (3.3.7) | Form assertion + a11y audit | — (P8 dışı) | 3.2.6 · 3.3.7 (`VERIFIED`) |
| 16 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür; gizli/örtülü odak yok | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 · 2.4.11 (`VERIFIED`) |
| 17 | Giriş / kimlik doğrulama | Giriş adımları erişilebilir ve kısa; engelli doğrulama yok | Form assertion + a11y audit | — (P8 dışı) | 3.3.8 (`VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı. Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E). Eşik değerleri research-bank P8'den (`VERIFIED`); WCAG kodları yalnız P5.2/P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, cinsiyet, semt, meslek, ulaşım, rumuz, ilişki detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (28) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 (16 yaş altı politikası bu persona için geçersiz) | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 4 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 5 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 6 | Sağlık/spor verisi yazılmadı (m.6 hassasiyeti) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 7 | Üniversite adı + maaş/gelir aralığı yazılmadı | Bankada YOK (X-6) | research-bank P1-P8 kapsamı | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |
| 8 | Big Five puanları (5×0-100; eski /10 puanların 10× dönüşümü) | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 9 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 10 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 11 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 12 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 13 | Mood küme adı (Enerjik) | Vault referanslı atama | [[mood-taxonomy]] §3.2.2 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 14 | Mood küme tanımı alıntısı + test etkisi (ADR-023 satır 12) | Vault verisi | [[mood-taxonomy]] §3.2.2 | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 15 | İkincil mood "Maceracı" | Listede yok | eski salt-okunur persona dosyası | mood-taxonomy §3 (yok) | `⚠️ VERIFICATION REQUIRED` — yazılmadı; §7.2 çelişki |
| 16 | §3.2.2 Big Five eğilim kodları (O = Dışadönüklük, A = Deneyime Açıklık) | Vault içi çelişki | [[mood-taxonomy]] §3.2.2 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` — puanlar P7.3 kodlarına göre yazıldı |
| 17 | Tür BPM aralıkları: Dance 110-135 · Electro 90-130 · House 110-128 · Pop 80-120 | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 18 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 19 | mood-taxonomy §3.2.2 "120-160 BPM" (Enerjik küme notu) | Eski taksonomi alıntısı | [[mood-taxonomy]] §3.2.2 | — | `⚠️ VERIFICATION REQUIRED` — P4.4 kapsamı dışı |
| 20 | Kişisel tempo (90-130 BPM) | Persona tercihi (kurgusal) | eski salt-okunur dosya (tercih bağlamı) | — | `Kaynak: kurgusal (persona verisi)` |
| 21 | Favori sanatçı adları (Hey Douglas, Jakuzi, Duman, Mor ve Ötesi, Büyük Ev Ablukada) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 22 | Cihaz: iPhone (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 23 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) | Türetilmiş (platform test girdisi) | Türetme: A34 1080÷3 · 2340÷3 (P3.1 verisi) | — | `⚠️ DERIVED` (P3.2) |
| 24 | Diğer cihazlar (bilgisayar, tablet, kulaklık, TV) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 25 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 26 | WCAG eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 27 | P5 kapsamında olmayan AA kriterleri (1.4.5, 1.4.10, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 28 | Persona renk kombinasyonu kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 29 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn | Gerçek-dünya | web.dev/articles/vitals | web.dev/articles/defining-core-web-vitals-thresholds | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 30 | Ağ throttling: 150 ms · 1.6 Mbps ↓ / 750 Kbps ↑ · "Slow 4G" | Gerçek-dünya | github.com/GoogleChrome/lighthouse (docs/throttling.md) | developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 31 | Playwright emülasyon alanları / breakpoint listesi | Gerçek-dünya | playwright.dev/docs/emulation | github.com/microsoft/playwright (docs/src/emulation.md) | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | §6.1/§6.2 sayaçları (test adımı, kaynak satırı, derinlik) | İç tutarlılık | Bu dosyanın §3.5.10/§3.5.11 tabloları | `vault-utf8-writer verify` | `Kaynak: kurgusal (dosya içi sayım; verify ile doğrulanır)` |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`buket-kaya-enerjik.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; üniversite adı/maaş/sağlık yazılmaz (X-6 / m.6) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone viewport = 2556×1179` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka türevi ⚠️ DERIVED` |
| `Şarkı 124 BPM` | Tür aralığı (P4.4, VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; COPPA = 13; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.2 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 → §6.1 kontrol listesi → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/yetiskin-kadin/` salt-okunur; yalnız kurgusal kimlik/müzik/rutin taşınır (üni/maaş/sağlık kırpılır) | Yalnız ilk üretimde |
| 4 | 11 alt bölüm tek tek doldurulur; boş bırakılan alan `yok` olarak işaretlenir | Her üretimde |
| 5 | `node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>` → `lines ≥ 500`, `mojibake = 0` | Yazım sonrası zorunlu |

```text
ŞABLONU OKU → BANKA/TAKSONOMİ/INDEX OKU → ESKİ KAYNAKTAN KURGU TAŞI → 11/11 ALAN → ETİKETLE → TEST ADIMLARI ≥10 → §6.1 → VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|----------|----------|
| Etiketsiz satır | §6.1 "kaynak" maddesi düşer | §4.5 formatıyla etiketle |
| Şarkı BPM'i / uydurma 2. kaynak | ADR-005 ihlali | Sil + `⚠️ VERIFICATION REQUIRED` |
| Eksik alan havuzu | 11/11 düşer | §3.5'e dön; boş alan `yok` diye işaretle |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Çift `{{` | Şablon-Önce bloğu dışındaki yer tutucu | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Buket için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] Frontmatter 12 alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`, `group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`)
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 505 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe + ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG)
- [ ] Kaynak & Doğrulama tablosu 28-32 satır ve 6 sütun
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor; bölümde yer tutucusu kalmamış (yer tutucu kural metni yalnız §4 Şablon-Önce bloğundadır)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (yalnız genel kurgusal/zorunlu satır)
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; persona cihazı `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5.2/P5.4 listesinden; persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4/P4.5); şarkı BPM'i yok
- [ ] Üniversite adı, maaş, sağlık/spor verisi, kaynaksız yüzde/istatistik YOK (X-6 / m.6)
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
| Kaynak & Doğrulama satırı | 32 |
| Frontmatter alan | 12 (7 zorunlu + 5 ek) |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 505 satır (`verify` → `lines`) |
| Kayıt | `log.md` append-only |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[personas/index]]` | Persona kataloğu — yaş/mood atamasının sahibi | ✅ `.ai/.personas/index.md` |
| `[[personas/persona-template]]` | Persona şablonu — bu dosyanın iskeleti | ✅ `.ai/.templates/personas/persona-template.md` |
| `[[research-bank]]` | Araştırma paketleri P1-P8 — tek gerçek-dünya veri kaynağı | ✅ `.ai/.personas/research-bank.md` |
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.2 Enerjik) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` (diskte mevcut — 2026-09-26 doğrulandı) |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir. Bu turda tüm hedefler diskte mevcuttu.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | 28 | 28 | Tutarlı — çelişki yok; [[personas/index]] §6.3 zaten 28 yazar |
| Birincil mood | Enerjik | Enerjik | Tutarlı — çelişki yok |
| Doğum tarihi | 3 Temmuz 1998 | 3 Temmuz 1998 | Yaş 28 ile tutarlılık için korundu (kurgusal) |
| İkincil mood | Maceracı | Yok | "Maceracı" mood-taxonomy §3 listesinde YOK → yazılmadı, §7.2'de saklı |
| Üniversite adı | Dokuz Eylül Üniversitesi (eski dosyada) | yazılmadı | X-6 (kurum adı gerçek kişi verisine açılır) → silindi |
| Maaş/gelir aralığı | TL aralığı (eski dosyada) | yazılmadı | X-6 + §4.5 kuralı 4 (bankada yok → yazma) |
| Sağlık/spor ayrıntıları (m.6) | Egzersiz/sağlık satırları (eski dosyada) | yazılmadı | P6.5 — sağlık verisi kurgusal olmak zorunda; bu dosyada silindi |
| Kişisel BPM | "90-130; dans 120-130; yoga 70-90" (eski dosyada) | Tür aralığı P4.4 (VERIFIED ikincil); şarkı BPM'i yok; kişisel tercih kurgusal | §4.5 + X-2 |
| Birincil cihaz | iPhone 15 Pro + MacBook (spec'li) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) | [[research-bank]] P3 — cihaz spec'i yazılmadı (X-1) |
| Kaynaksız istatistik/yüzde iddiaları | Keşif/sayı/yüzde iddiaları (eski dosyada) | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik/rutin taşındı (üni/maaş/sağlık kırpıldı), index §6.3 ile yaş/mood doğrulandı (28/Enerjik tutarlı); yetişkin statüsü (veli onayı false), ikincil mood "Maceracı" çelişki kaydı, A34-türevi viewport ve X-1/X-2/X-6 kırpımları eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-kadin/buket-kaya-enerjik
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
