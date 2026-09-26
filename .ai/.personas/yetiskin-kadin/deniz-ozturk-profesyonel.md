---
title: "CoreMusic — Persona: Deniz Öztürk"
type: persona
category: personas
version: 1.0.0
status: active
authority: "Persona — SSOT: personas/yetiskin-kadin/deniz-ozturk-profesyonel"
updated: 2026-09-26
group: yetiskin-kadin
age: 41
mood: Profesyonel
persona_id: CM-DO-41-PRO-IST
veli_onayı_gerekli: false
---

# CoreMusic — Persona: Deniz Öztürk

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/persona-template]] · [[research-bank]] · [[mood-taxonomy]] · [[personas/methodology]] · [[personas/test-scenarios-mapping]] · [[ADR-023-persona-driven-testing]]

---

## §1 Amaç

Bu persona, CoreMusic'te **yetiskin-kadin** segmentini temsil eder; Level 1 (AI rol testi), Level 2 (Browser MCP) ve Level 3 (Playwright E2E) testlerinde 41 yaşındaki bir şirket yöneticisinin — ofiste masaüstü ve çoklu-sekme akışının — gözünden arayüzü deneyimlemek için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → [[mood-taxonomy]] (Profesyonel), veri → [[research-bank]] (P3-P8), metodoloji → [[personas/methodology]], registry → [[personas/index]].
Bu dosya `persona-template.md` §3.5 alan havuzunun **11/11** alt bölümüyle doldurulmuş hâlidir; her satır §4.5 kaynak etiketi taşır.

| Alan | Değer |
|------|-------|
| Segment | yetiskin-kadin (25-45) |
| Birincil mood | Profesyonel ([[personas/index]] §6.3 ataması — yazım birebir) |
| Test odağı | Ofis/uzun oturum, çoklu sekme, klavye-odaklı masaüstü akış, düşük bant genişliği (ADR-023 satır 7) |
| Veli onayı | `veli_onayı_gerekli: false` (yetişkin — 16 yaş altı politikası uygulanmaz) |

**Test kapsamı (bu persona ile koşan senaryo aileleri):**

| Senaryo ailesi | Seviye | Persona katkısı |
|----------------|--------|-----------------|
| Uzun oturum + cache TTL boundary | Level 2 + 3 | Profesyonel küme test etkisi: productivity modu, oturum süresi (ADR-023 satır 7) |
| Çoklu sekme / pencere geçişi | Level 2 + 3 | Arka plan performansı; çalmaya devam, oturum kaybı yok |
| Ofis / masaüstü düzeni | Level 2 + 3 | 1280×720 · 1920×1080 breakpoint (research-bank P8.5) |
| Düşük bant genişliği (Slow 4G) | Level 2 + 3 | 150 ms / 1.6 Mbps / 750 Kbps koşulunda hata yok (P8.3) |
| Klavye-odaklı erişim | Level 2 + 3 | 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 odak/hedef denetimi |
| Hata ekranları & yavaş ağ | Level 2 | Sabırlı ama tahammülsüz; panikletici/yığın izni metni yasak |
| Erişilebilirlik denetimi | Level 2 | P5.2/P5.4 listesindeki 10 kriterin ihlali aranır |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu (Kimlik → Kaynak & Doğrulama) | Test başarı metrikleri ve seviye tanımı (→ [[personas/methodology]]) |
| Kaynak etiketleme (kurgusal / [k1]+[k2] / ⚠️) — research-bank P3-P8 verisi | Test senaryosu envanteri (→ [[personas/test-scenarios-mapping]]) |
| ≥10 test adımı (Adım / Eylem / Beklenen / Doğrulama / Metrik / WCAG) | Karar kaydı (→ ADR; bu dosya ADR üretmez) |
| AI Rol Kartı (Level 1 prompt'u) | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → big five → mood → müzik DNA → cihaz → erişilebilirlik → kişilik → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı istisnalar: eski vault'taki 100+ satırlık fiziksel/aile/maaş detayı bu şablonda yasaktır (§3.5.2 → 5-8 satır); bankada olmayan gerçek-dünya iddiası yazılmaz (§4.5); üniversite adı (X-6) ve sağlık verisi (6698 m.6) kopyalanmaz.

**Okuma sırası (bu dosyayı kullanan agent için):**

| Sıra | Dosya | Ne için |
|------|-------|---------|
| 1 | [[personas/persona-template]] | Zorunlu iskelet (§3.3 blok, §3.5 alan havuzu) |
| 2 | [[research-bank]] P3-P8 | Tek gerçek-dünya veri kaynağı + etiket sözlüğü (§4.1) |
| 3 | [[mood-taxonomy]] §3.2.5 | Küme adı + tanım alıntısı (Profesyonel) |
| 4 | [[personas/index]] §6.3 | Yaş/mood/filename ataması (SSOT) |
| 5 | Bu dosya §4-§6 | Kurallar → workflow → doğrulama |

---

## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | Deniz Öztürk | Kaynak: kurgusal (persona verisi) |
| **Yaş** | 41 | Kaynak: kurgusal (persona verisi; atama: [[personas/index]] §6.3 — SSOT) |
| **Doğum Tarihi** | 7 Mart 1985 | Kaynak: kurgusal (persona verisi; yaş 41 ile tutarlılık için türetildi — eski dosyada 7 Mart 1984, bkz. §7.2) |
| **Cinsiyet** | Kadın | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | İstanbul / Beşiktaş / Etiler | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | Şirket Yöneticisi (yükseköğretim mezunu — okul adı yazılmadı, EXCLUDED X-6) | Kaynak: kurgusal (persona verisi); X-6 notu: `⚠️ VERIFICATION REQUIRED` |
| **Ulaşım** | Özel araç + toplu taşıma karma (ofis güzergâhı, kurgu) | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | Yok (kurumsal/profesyonel hesaplarda gerçek ad) | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | `CM-DO-41-PRO-IST` | Kaynak: kurgusal (persona verisi) |

**`CoreMusic Kullanıcı ID` parça dökümü** (şablon §3.5.1 formatı `CM-XX-YY-ZZZ-XXX`):

| Parça | Değer | Kural |
|-------|-------|-------|
| `CM` | `CM` | Sabit önek |
| `XX` | `DO` | Ad + soyad ilk harfleri (Deniz Öztürk → D, Ö→O ASCII katlaması) |
| `YY` | `41` | Yaş 2 hane (41 → `41`; index §6.3 SSOT) |
| `ZZZ` | `PRO` | Mood tür kodu (Profesyonel → PRO) |
| `XXX` | `IST` | Şehir kodu (İstanbul → IST) |

*Not (yetişkin persona):* `veli_onayı_gerekli: false` — KVKK/yaş sınırı notu 16 yaş altı persona'lar içindir; bu persona için uygulanmaz (genel çerçeve: TMK m.11 **18**, COPPA **13**, GDPR m.8 **16**; "KVKK 16 diyor" ifadesi **YANLIŞTIR** — 16, GDPR değeridir → `Kaynak: [k1] + [k2]`, research-bank P6.4). Bu dosya tamamen kurgusaldır: gerçek kişi verisi yazılmaz (6698 m.5/1). Eski dosyadaki üniversite/maaş/gelir/yüzde verileri bu dosyaya **TAŞINMADI** (X-6 + ADR-005, bkz. §7.2).

### Fiziksel Özet

> **Standard:** 5-8 satır. Eski dosyadaki 100+ satırlık ayrıntı (marka giyim listesi, ölçü, check-up/beslenme takibi, diyetisyen…) bu şablonda yasaktır; yalnız testi etkileyen görsel/temsiliyet notları kalır.

| Boyut | Değer | Kaynak |
|-------|-------|--------|
| **Boy** | 170 cm (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Kilo / Vücut tipi** | Orta yapı, yönetici silueti (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | Kumral saç, ela göz (kurgu) | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | Yok (kurgu) — reçete/ölçü/sağlık ayrıntısı 6698 m.6 gereği yazılmadı | Kaynak: kurgusal (persona verisi); m.6 kuralı: research-bank P6.5 |
| **Giyim / temsil notu** | Ofiste takım/kaşmir-sade; profil fotoğrafında açık zemin (renk eşleşmesi `⚠️ DERIVED`, bkz. §3.5.7) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | Uzun masaüstü oturumu (fare + klavye); küçük/gizli hedefe tahammülü yok → işaretçi hedefi ≥ **24×24 CSS px** (2.5.8) | Kaynak: kurgusal (persona verisi); kriter: `Kaynak: [k1] + [k2]` (P5.4 VERIFIED) |

*Sağlık verisi notu:* Eski dosyadaki sağlık ayrıntıları (göz ölçüsü, check-up, beslenme/diyet takibi vb.) 6698 m.6 kapsamında **sağlık verisidir** → bu dosyada **yazılmadı**; persona'da herhangi bir kısıt varsa dahi **kurgusal olması zorunludur** (research-bank P6.5).

### Big Five (OCEAN)

**Ölçek:** IPIP-NEO-120 (International Personality Item Pool — NEO formu; **120 madde, 5 domain, 30 facet; Johnson 2014**) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | E | 70 | İşte sunum, toplantı ve müzakerede kendinden emindir; özel hayatında ise seçici ve sınırlı çevrelidir. | Kaynak: kurgusal (persona verisi; eski /10 → 7×10 = 70); ölçek: IPIP-NEO-120 |
| Uyumluluk | A | 40 | Sonuç odaklı ve yüksek standartlıdır; "sevgi değil, saygı" beklentisi çatışmada sertleştirir. | Kaynak: kurgusal (persona verisi; eski /10 → 4×10 = 40); ölçek: IPIP-NEO-120 |
| Sorumluluk | C | 90 | Ajandası dakikalara bölünmüştür; plan, zaman ve söz onun için değişmez bir disiplindir. | Kaynak: kurgusal (persona verisi; eski /10 → 9×10 = 90); ölçek: IPIP-NEO-120 |
| Duygusal Denge (N tersi) | N | 30 | **Yüksek puan = düşük duygusal denge (ters kodlama).** Eski dosyada "Duygusal Denge 7/10" (soğukkanlı) → ×10 = 70 → N ters-kodlama **100 − 70 = 30**; krizde panik yapmaz. | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO-120; ters-kodlama `⚠️ DERIVED` |
| Deneyime Açıklık | O | 80 | Yeni dijital araçlara ve yeni iş modellerine açıktır; ama her yeniliği veriyle test eder, hevesle değil. | Kaynak: kurgusal (persona verisi; eski /10 → 8×10 = 80); ölçek: IPIP-NEO-120 |

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100; eski dosyanın `/10` puanı **10 ile çarpılır** (7/4/9/7/8 → 70/40/90/70/80); normalize yöntem `⚠️ DERIVED` (research-bank P7.4: "resmi ölçek 0-100 değildir; yöntem türetilmiştir") |
| N ters-kodlama | Zorunlu not: **yüksek N = düşük duygusal denge**; "duygusal denge" skoru N'nin tersidir → eski 70 → N 30 (`⚠️ DERIVED`, persona-template §3.5.3 kuralı) |
| Puanların kendisi | Kurgusal persona verisi (`Kaynak: kurgusal (persona verisi)`) — IPIP-NEO ile **ölçülmemiştir** |
| Ölçek künyesi | IPIP-NEO-120, Johnson (2014) → `Kaynak: novopsych.com (Technical Paper) + APA PsycNET (PsycTests kaydı)` (research-bank P7.4 VERIFIED) |
| Türkçe geçerlilik | Bulunamadı (P7.5 / X-9) → "IPIP-NEO Türkçe geçerli" iddiası yazılmadı, `⚠️ VERIFICATION REQUIRED` |
| Boyut kodu eşlemesi | Satır sırası `persona-template` §3.5.3 gibidir; kod harfleri `research-bank` P7.3 tam adlarına göredir (E=Extraversion, A=Agreeableness, C=Conscientiousness, N=Neuroticism, O=Openness). Şablon satırındaki O/C/E/A harf eşlemesi bu tam adlarla çelişir → P7.5 kuralı ("kod esastır") uygulanır, çelişki `⚠️ VERIFICATION REQUIRED` olarak §3.5.11'e kayıtlıdır. |
| Küme eğilimi karşılaştırması | [[mood-taxonomy]] §3.2.5 önermesi "Yüksek Sorumluluk (E), düşük Dışadönüklük (O)" → Sorumluluk 90 **tutarlı**; Dışadönüklük 70 **tutarlı değil** (iki kurgusal değer — küme önermesi `⚠️ VERIFICATION REQUIRED`, §1.2 toplu etiket); §7.2'ye kayıtlıdır |

### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | Profesyonel |
| **İkincil küme** | Lider ([[mood-taxonomy]] §3.3 katalog #12 — eski dosyadan taşındı; küme adı listede VAR, grup ataması "Kız Çocuk ×2" olduğundan bu persona için atamasız → `⚠️ VERIFICATION REQUIRED`, §7.2) |
| **Küme tanımı (alıntı)** | "Odaklanma amaçlı, arka planda dinleyen; verimlilik odaklı, az etkileşimli kullanıcı" ([[mood-taxonomy]] §3.2.5) |
| **Tetikleyici durumlar** | Ofiste odak_NEEDS_CHECK — uzun oturumda yavaş sayfa/tekrarlanan adım (geri çekilme); çoklu sekme geçişinde çalmanın durması (geri çekilme); sade/öngörülebilir ve hızlı arayüz (yaklaşma) |
| **UI etkisi** | Küme tercihi: "Minimal, hızlı, verimli, az dikkat dağıtıcı"; test etkisi: "Productivity modu, arka plan performansı, oturum (session) süresi" + ADR-023 satır 7 (uzun oturum + cache TTL boundary) ([[mood-taxonomy]] §3.2.5) |

| Kural | Değer |
|-------|-------|
| Küme adları | Yalnızca [[mood-taxonomy]] §3 listesinden; uydurma küme adı yazılmaz (taksonomi kuralı) |
| Atama | Birincil = [[personas/index]] §6.3 (Profesyonel — yazım birebir); ikincil = eski salt-okunur dosya (Lider, §3.3'te tanımlı) — §7.2'de kayıtlı |
| §2.2 atama notu | mood-taxonomy §2.2/§3.2.5: Profesyonel kümesi **Yetişkin Kadın ×1 (Deniz Ö.)** olarak atalıdır; "atamasız" notu yalnız **yetişkin erkek** grubu içindir → bu persona için atama VAR, yine de `⚠️ Vault Steward` notu olarak korunur |
| Test etkisi | ADR-023 satır 7 (uzun oturum + cache TTL boundary) + productivity modu / arka plan performansı — [[mood-taxonomy]] §3.2.5 |
| Taksonomi BPM notu | §3.2.5 "60-100 BPM ⚠️" işaretli eski taksonomi önermesidir (§1.2 toplu etiket) → test eşikleri olarak research-bank P4.4 tür aralıkları kullanılır |

**Mood → test beklentisi eşlemesi:**

| # | Mood davranışı | Beklenen UI tepkisi | Doğrulama |
|---|----------------|---------------------|-----------|
| 1 | Uzun oturum + cache TTL (ADR-023 satır 7) | Oturum sürekliliği; uzun beklemede sürpriz logout/bozuk durum yok | Level 3 E2E + oturum assertion |
| 2 | Arka plan performansı (çoklu sekme) | Sekme arka plandayken çalma ve sayaçlar tutarlı kalır | Manuel + trace + console log |
| 3 | Minimal, az dikkat dağıtıcı UI | Gereksiz animasyon/oda-üstü bildirim yok; odak görünür (2.4.7) | A11y audit + screenshot |
| 4 | Verimlilik / az tıklama | Klavye ile tamamlanan akış; tekrarlı veri girişi yok (3.3.7) | Klavye navigasyon + form assertion |
| 5 | Tutarlı yardım (3.2.6) | Yardım/destek her sayfada aynı konumda | DOM assertion |
| 6 | Düşük bant genişliği toleransı | Slow 4G koşulunda hata yok, kademeli yükleme net | Network throttling + trace |

### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (5, sıralı)** | 1) Caz (smooth / modern) 2) Klasik 3) Ambient / chill-out 4) Türkçe pop (seçme) 5) World / eklektik | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (5)** | Tarkan, Sertab Erener, Mirkelam, Diana Krall, Sting | Kaynak: kurgusal (persona verisi — sanatçı seçimi persona zevkidir); sanatçıların gerçek-dünya bilgileri research-bank P4.2 kapsamı dışında → `⚠️ VERIFICATION REQUIRED` (ek doğrulama turu) |
| **BPM aralığı (tür)** | Pop **80-120** · House **110-128** BPM (P4.4 — ofis/arka plan akışları için) | Kaynak: turkipedia.com/Beats_per_minute + nevamuzik.com.tr (research-bank P4.4 `VERIFIED (ikincil kaynaklar — birincil ölçüm DEĞİL)`) |
| **Taksonomi küme BPM notu** | 60-100 BPM (mood-taxonomy §3.2.5 — eski taksonomi) | `⚠️ VERIFICATION REQUIRED` ([[mood-taxonomy]] §1.2 toplu etiket; research-bank P4.5 bu türler için aralık vermez) |
| **BPM (şarkı bazlı)** | Yazılmadı — parçaya özgü BPM değeri doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P4.7 / EXCLUDED X-2) |
| **Kişisel tempo tercihi** | Sakin-orta tempolu odak dinleme (kurgusal tercih — sayısal aralık taksonomi notuna bağımlı, `⚠️`) | Kaynak: kurgusal (persona verisi — persona tercihi, ölçüm değil) |
| **Dinleme saati (hafta içi / sonu)** | Hafta içi 08:30-09:00 (ofis yolu), 13:00-14:00 (öğle arası odak), 19:00-20:30 (akşam) · Hafta sonu 11:00-12:30 (ev) | Kaynak: kurgusal (persona verisi) |
| **Platform** | CoreMusic (ikincil platform — masaüstü/ofis denemesi; kurgu) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | Küratörlü/liste odaklı ve kısa: zamanı değerli olduğu için keşife sınırlı vakit ayırır | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | Değer/kalite odaklı yüksek eğilim (kurgusal — sayısal yüzde yazılmadı: ADR-005 bankada yok) | Kaynak: kurgusal (persona verisi) |

**En sevdiği türler — gerekçe tablosu (kurgusal):**

| Sıra | Tür | Neden sevdiği |
|------|-----|---------------|
| 1 | Caz (smooth / modern) | Toplantı sonrası geçiş ritüeli; dikkat dağıtmadan odak verir |
| 2 | Klasik | Uzun rapor/okurken fon; sakin ve öngörülebilir akış |
| 3 | Ambient / chill-out | Uzun oturumda konsantrasyon; kesintisiz arka plan |
| 4 | Türkçe pop (seçme) | Pazar/trend takibi; yerel içerik bağı |
| 5 | World / eklektik | Seyahat ve ev/ofis fonu; farklılık arayışı |

**Dinleme zaman çizelgesi (kurgusal):**

| Gün | Saat | Aktivite | Öne çıkan tür |
|-----|------|----------|---------------|
| Hafta içi | 08:30-09:00 | Ofis yolu | Caz / seçme pop |
| Hafta içi | 13:00-14:00 | Öğle arası odak | Ambient / klasik (arka plan) |
| Hafta içi | 15:00-17:00 | Toplantı bloğu | Müzik yok (kesintisiz iş) |
| Hafta içi | 19:00-20:30 | Akşam eve dönüş/ev | Smooth jazz / world |
| Hafta sonu | 11:00-12:30 | Ev, sakin | Klasik / chill-out |
| Hafta sonu | 21:00-22:00 | Haftaya hazırlık | Ambient (fon) |

*Not: BPM satırları yalnızca tür aralığı düzeyindedir (P4.4); "şarkı X BPM" iddiası bu dosyada YOKTUR (EXCLUDED X-2). Caz / klasik / ambient türleri için research-bank'te doğrulanmış BPM aralığı **yok** → o türler `⚠️ VERIFICATION REQUIRED` + kurgusal tercih olarak taşındı; mood-taxonomy §3.2.5 "60-100" notu da birincil ölçüm değildir.*

### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | iPhone 15 Pro Max (eski salt-okunur dosyadan) | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları YAZILMADI; bankada VERIFIED cihaz yalnız Samsung Galaxy A34 5G |
| **Ekran çözünürlüğü → viewport (mobil test)** | Cihaza özel viewport YOK; test girdisi banka cihazı A34: **1080 × 2340** (VERIFIED) → `360 × 780` (DPR 3) · alternatif `412 × 915` (DPR 2.625) | Çözünürlük: `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`); viewport: `⚠️ DERIVED` — türetme: çözünürlük ÷ DPR (P3.2); persona cihazına **uygulanmaz**, platform test girdisidir |
| **Masaüstü / ofis kırılma noktaları** | `1280 × 720` · `1920 × 1080` (bu persona'nın birincil test alanı) | `Kaynak: [k1] + [k2]` (research-bank P8.5 `VERIFIED` — playwright.dev/docs/emulation + mintlify) |
| **OS** | iOS sürümü doğrulanmadı | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / X-1) — sürüm yazılmadı |
| **Tarayıcı** | Safari (masaüstü/iPad) — sürüm kurgusal (P3'te tarayıcı sürümü YOK) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **İnternet hızı** | Ofis/kurumsal kablolu hat + 5G mobil (marka/hız kurgusal — paket dışı) | Kaynak: kurgusal (persona verisi); research-bank P3.4 |
| **Kulaklık** | Kablosuz kulaklık (ofis/travel) — model spec'siz | `⚠️ VERIFICATION REQUIRED` (X-1) — model/spec yazılmadı |
| **Tema** | light (ofis varsayılanı — kurgusal) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | Günde ~8 saat ekran (iş ağırlıklı, uzun oturum — kurgu) | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | 8/10 — tüketici olarak ileri düzey; üretici değil | Kaynak: kurgusal (persona verisi) |
| **Diğer cihazlar (eski dosyadan)** | İş bilgisayarı, tablet, ev hoparlörü/ses sistemi — spec'leri bu turda DOĞRULANMADI | `⚠️ VERIFICATION REQUIRED` (research-bank P3.3 / EXCLUDED X-1) — spec sayıları yazılmadı |

*Viewport notu (Level 2/3 beslemesi):* `Emulation.setDeviceMetricsOverride` / `page.setViewportSize()` girdisi mobil testte `360 × 780 @ DPR 3` satırıdır (platform test varsayımı, `⚠️ DERIVED`); masaüstü/ofis testinde P8.5 breakpoint listesi kullanılır (`VERIFIED`). Persona'nın kendi cihaz viewport'u **yoktur** (spec doğrulanmadı — X-1). `412 × 915 @ DPR 2.625` alternatifi de yalnız `⚠️ DERIVED` işaretli olarak kullanılabilir (P3.4: DPR 2.625 resmi kaynakla desteklenmiyor).

### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | kısıt yok (kurgu); uzun oturumda göz yorgunluğu beklentisi | Metin kontrastı ≥ **4.5:1** (1.4.3), UI/grafik ≥ **3:1** (1.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + w3.org/WAI/WCAG22/quickref/ (P5.4 VERIFIED) |
| **İşitme** | kısıt yok (kurgu); ofis ortamında kulaklıkla dinleme | Sesli geri bildirimler görselle desteklenmeli — kriter numarası P5 kapsamında AÇILMADI → sayısal eşik yazılmadı | Kaynak: kurgusal (persona verisi); research-bank P5.5 → `⚠️ VERIFICATION REQUIRED` |
| **Hareket / motor** | kısıt yok; fare + klavye ağırlıklı masaüstü kullanımı | İşaretçi hedefi ≥ **24×24 CSS px** (2.5.8) · sürükleme alternatifi zorunlu (2.5.7) · hover/focus içeriği kaybolmayacak ve örtmeyecek (1.4.13) | Kaynak: kurgusal (persona verisi); kriter: w3.org/TR/WCAG22/ + quickref (P5.4 VERIFIED) |
| **Kognitif / dikkat** | uzun oturum + sık bölünme; kısa, tutarlı akış beklentisi | Tutarlı yardım konumu (3.2.6) · tekrarlı veri girişi azaltma (3.3.7) · erişilebilir kimlik doğrulama (3.3.8) · odak görünür ve örtülü olmayan (2.4.7 + 2.4.11) | Kaynak: kurgusal (persona verisi); kriter: w3.org + digitalpolicy.gov.hk (P5.4 VERIFIED) |

| Not | Ayrıntı |
|-----|---------|
| Kısıt alanı KVKK | Kısıt/sağlık verisi 6698 m.6 kapsamında hassastır → persona'da **kurgusal olmak zorunda**; eski dosyadaki göz ölçüsü/reçete kopyalanmadı (research-bank P6.5) |
| Renk kombinasyonu | Kişisel tema/renk eşleşmelerinin kontrast oranı hesaplanmadı → `⚠️ DERIVED` (research-bank P5.5); eşikler (4.5:1 / 3:1) VERIFIED, persona'nın kendi renkleri değil |
| 72×72 / 64×64 dp gibi tasarım hedefleri | Bankada yok → bu dosyada YAZILMADI; asgari güvenli eşik = 2.5.8 (≥24×24 CSS px, VERIFIED) |
| AAA iddiası | "AAA uyumlu" yazılmaz — AAA kriterleri yalnız referanstır (P5.5) |

### Kişilik & Davranış

- Veriyle ve planla karar alır; duygusal değil, sonuç odaklıdır.
- Zamanı değerlidir: az tıklama, hızlı yükleme, öngörülebilir akış ister; tekrar eden adımlarda sabrı erir.
- Detaycıdır: belirsiz metin, kaynaksız iddia ve panikletici hata ekranı güvenini düşürür.
- Küratörlü/seçilmiş içeriği algoritmik "rasgele" öneriden önce tercih eder.
- Ofiste masaüstü/klavye ile, yolda ve evde farklı cihazda devam eder; süreklilik bekler.

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | Fiyat değil değer odaklı; kalite/ayrıcalık varsa öder, ama ikna edilmek ister | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | ~3 sn; aynı hatayı ikinci kez görürse akışı bırakır | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | Analitik: kodu ve nedeni ister, sade çözüm adımı bekler; yığın izni/gereksiz teknik metin görürse güven kaybı | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | 5/10 — dikkat dağıtan ve atlanamayan kesintiye tahammülü düşüktür | Kaynak: kurgusal (persona verisi) |
| **Öğrenme biçimi** | Kısayol + kısa kılavuz; video izlemez, deneyerek çözer | Kaynak: kurgusal (persona verisi) |
| **Grup davranışı** | İş/ekip listelerini paylaşır; kişisel listeleri genelde paylaşma eğiliminde değildir | Kaynak: kurgusal (persona verisi) |
| **Yorgunluk etkisi** | Gün sonunda tek dokunuşlu sade akış ister; karmaşık menü reddedilir | Kaynak: kurgusal (persona verisi) |
| **Güven tetikleyicisi** | Tutarlı düzen + net gizlilik/KVKK mesajı + hızlı tepki → güvenir; belirsizlikte ödünleşmez | Kaynak: kurgusal (persona verisi); mood: [[mood-taxonomy]] §3.2.5 (Profesyonel) |

*Erişilebilirlik etkisi (özet):* uzun masaüstü oturumu + fare/klavye → hedef ≥ **24×24 CSS px** (2.5.8), kontrast ≥ **4.5:1** (1.4.3), tekrarlı veri girişi yok (3.3.7), yardım konumu sabit (3.2.6), odak asla örtülmez (2.4.11) zorunludur — kriterler: `Kaynak: w3.org/TR/WCAG22/ + quickref` (P5.4 VERIFIED).

### AI Rol Kartı

Sen Deniz Öztürk'sün. 41 yaşında, İstanbul/Beşiktaş-Etiler'de yaşıyorsun, şirket yöneticisisin.
Profesyonel bir kişiliğin var: odaklanma amaçlı, arka planda dinleyen, verimlilik odaklı ve az etkileşimlisin; ikincil olarak Lider'sin — listeleri ve sıralamayı sen belirlersin.
Müzik zevkin: caz (smooth/modern), klasik, ambient/chill-out, seçme Türkçe pop, world/eklektik. En sevdiklerin: Tarkan, Sertab Erener, Mirkelam, Diana Krall, Sting.
Cihazların (model spec'leri doğrulanmadı): telefon, iş bilgisayarın, tablet ve ev ses sistemin var; masaüstü/çoklu sekme birincil kullanımın, internetin ofiste kablolu, mobilde 5G (kurgusal); teknoloji seviyen 8/10.
Görme / işitme / hareket kısıtların yok; ama uzun oturumlarda fareyle küçük hedef kovalamak ve tekrar eden form adımları seni yorar.
Sabır eşiğin ~3 sn; gereksiz adım tekrarını, belirsiz metni ve panikletici hata ekranını sevmezsin; gizlilik/KVKK şeffaflığı ve hızlı tepki beklersin.
Test edilecek akışlar: uzun oturum + cache TTL, çoklu sekme, ofis/masaüstü düzen, klavye-odaklı erişim, düşük bant genişliği (Slow 4G), hata kurtarma, erişilebilirlik denetimi.
Davranış notları:
- Arka planda çalan liste kesintiye uğramamalı; sekme/gezinme değişiminde oturum kaybı olmamalı.
- Klavyeyle tamamlanan kısa akışlar beklersin; aynı veriyi iki kez girmek seni durdurur.
- Hata ekranında kod + tek adımlık çözüm istersin; yığın izni ve suçlayıcı metin görürsen uygulamayı bırakırsın.
- Türkçe karakterli içerikte bozulma (mojibake) görürsen güvenin düşer.

*Bu paragraf Level 1 (AI Rol Testi) prompt'a birebir gömülür; yer tutucusu ve gerçek kişi verisi içermez (KVKK §4.7).*

**Prompt kullanım notları:**

| Not | Ayrıntı |
|-----|---------|
| Biçim | Paragraf olduğu gibi gömülür; satır sonları korunur |
| Yaş dili | "sen" dili, net ve saygılı ton — yönetici persona'sı uzun anlatım kabul etmez |
| Gerçek veri yasak | Prompt'ta üniversite/maaş/sağlık verisi yok; tümü kurgusal (X-6 + 6698 m.6) |
| Çıktı beklenisi | Türkçe, mojibake'siz; beklenen davranış §3.5.10 tablosundaki "Beklenen" sütunundan okunur |
| Seviye | Level 1 (AI rol testi) — Level 2/3'te bu metin test verisi olur |

### Test Adımları

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Ofis/masaüstü ana sayfa yükleme + kırılma noktası (`1280 × 720` · `1920 × 1080`) | LCP ≤ **2500 ms**, içerik CLS ≤ **0.1** ile yerleşir; düzen ve odak bozulmaz | Performance trace + Playwright `setViewportSize` + screenshot | LCP ≤ **2500 ms** · CLS ≤ **0.1** (P8.1 `VERIFIED`) · breakpoint listesi (P8.5 `VERIFIED`) | 1.4.3 · 2.4.11 (`VERIFIED`) |
| 2 | Uzun oturum (≥30 dk arka planda) | Oturum ve çalma durumu korunur; cache TTL'de sürpriz logout yok (ADR-023 satır 7) | Manuel + oturum assertion | — (P8 dışı) | 2.4.7 (`VERIFIED`) |
| 3 | Çoklu sekme (5 sekme açık) | Çalma diğer sekmeye geçince durmaz; sekmeler arası tutarlılık | Manuel + trace + console log | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 3.2.6 (`VERIFIED`) |
| 4 | Arka plan performansı (uzun liste) | Uzun oturumda CPU/bellek tüketimi stabil; etkileşim akıcı | Performance trace | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 1.4.11 (`VERIFIED`) |
| 5 | SPA navigasyon (sayfalar arası) | Müzik kesintisiz; hissedilir gecikme yok; FCP ≤ **1 sn** | Trace + console log | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 2.4.7 (`VERIFIED`) |
| 6 | Arama (Türkçe karakter: ç, ğ, ı, İ, ö, ş, ü) | Türkçe karakter kabulü; hızlı sonuç; mojibake yok | DOM assertion + timing + `vault-utf8-writer verify` | — (P8 dışı) | 3.3.7 (`VERIFIED`) |
| 7 | Şarkı çalma (play / duraklat) | İlk ses < 1 sn; net oynat kontrolü; INP ≤ **200 ms** | Trace + console log | İlk ses < **1 sn** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 8 | Favori ekleme (klavye ile) | Anlık görsel geri bildirim; durum kalıcı; ikinci kez veri girişi yok | DOM assertion + LocalStorage kontrolü | INP ≤ **200 ms** (P8.1 `VERIFIED`) | 3.3.7 (`VERIFIED`) |
| 9 | Düşük bant genişliği (Slow 4G) | **150 ms / 1.6 Mbps / 750 Kbps** koşulunda hata yok; kademeli yükleme net | Network throttling + trace | Slow 4G profili (P8.3 `VERIFIED`) | — |
| 10 | Uzun sayfa kaydırma + hover içerik | Kaydırmada içerik üstüne binmiyor; hover/focus içeriği kaybolmuyor ve alanı örtmüyor | Trace + manuel kontrol | CLS ≤ **0.1** (P8.1 `VERIFIED`) | 1.4.13 (`VERIFIED`) |
| 11 | Hata sayfaları (404 / kopma) | Sade Türkçe; hata kodu + "Tekrar Dene"; yığın izni görünmez | Manuel + screenshot | — (P8 dışı) | 3.3.8 (`VERIFIED`) |
| 12 | Giriş / kimlik doğrulama | Karmaşık hafıza görevi yok; erişilebilir doğrulama akışı | Form assertion + a11y audit | — (P8 dışı) | 3.3.8 · 3.3.7 (`VERIFIED`) |
| 13 | Yardım erişimi (tutarlı yardım) | Yardım/destek bağlantısı her sayfada aynı konumda | DOM assertion | — (P8 dışı) | 3.2.6 (`VERIFIED`) |
| 14 | Sürükle-bırak alternatifi | Sürükleme zorunlu hiçbir adım yok; klavye/tık alternatifi var | Manuel + a11y audit | — (P8 dışı) | 2.5.7 (`VERIFIED`) |
| 15 | Erişilebilirlik denetimi | Lighthouse A11y + axe; 1.4.3 / 1.4.11 / 1.4.13 / 2.4.7 / 2.4.11 / 2.5.7 / 2.5.8 / 3.2.6 / 3.3.7 / 3.3.8 ihlali yok | Lighthouse / axe | — (P8 dışı; eşik P5) | 1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8 (`VERIFIED`) |
| 16 | Kırılma noktası kontrolü (mobil) | `360 × 780` (DPR 3) ve `412 × 915` (DPR 2.625) görünümlerinde düzen bozulmaz | Playwright `setViewportSize` + screenshot (`⚠️ DERIVED` viewport) | FCP ≤ **1 sn** (P8.1 `VERIFIED`) | 2.5.8 (`VERIFIED`) |
| 17 | Odak sırası (klavye/gezinme) | Odak sırası mantıklı; odak görünür (2.4.7); örtülü/gizli odak yok (2.4.11) | Klavye navigasyon testi + DOM assertion | — (P8 dışı) | 2.4.7 · 2.4.11 (`VERIFIED`) |

*Asgari 10 satır (şablon §3.5.10) — 17 satır yazıldı (şablon bandı 16/17: ofis + çoklu-sekme + uzun oturum + düşük bant genişliği önceliği, masaüstü kırılma noktası 1. adımla birleştirildi). Her satırda 6 sütun zorunlu: `Adım` · `Eylem` · `Beklenen` · `Doğrulama` · `Metrik (research-bank P8 değeri)` · `WCAG kriteri`. Metrik sütunu yalnız research-bank P8 değerlerinden beslenir; WCAG kodları yalnız P5.2/P5.4 listesinden; persona'ya özel eşik yazılmadı (X-10 → `⚠️ DERIVED` gerekir). Seviye karşılıkları: [[personas/methodology]] Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E).*

### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, cinsiyet, semt, meslek, ulaşım, rumuz, aile detayları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Yaş (41) ve atama | Kurgusal (index ataması) | [[personas/index]] §6.3 | — | `Kaynak: kurgusal (persona verisi; index §6.3 ataması)` |
| 3 | Yaş/ doğum tarihi çakışması (41 ↔ 42; 7 Mart 1985 ↔ 7 Mart 1984) | Vault içi çelişki | [[personas/index]] §6.3 (41 — SSOT) | eski salt-okunur persona dosyası (42) | `⚠️ VERIFICATION REQUIRED` — iki değer korunur, indirgeme YOK (§7.2) |
| 4 | `veli_onayı_gerekli: false` (yetişkin) | Kural sonucu | research-bank P6.3/P6.4 | — | `Kaynak: kurgusal + kural (P6.4 uygulaması)` |
| 5 | KVKK/yaş karşılaştırması; "KVKK 16 diyor" = YANLIŞ | Gerçek-dünya | dergipark.org.tr (mukayeseli inceleme) | kvkk.gov.tr (Yayın No 84) + mgm.adalet.gov.tr | `Kaynak: [k1] + [k2]` (P6.4 `VERIFIED`) |
| 6 | Fiziksel özet satırları | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 7 | Sağlık verisi yazılmadı (göz ölçüsü/reçete vb.) | Kural uygulaması | research-bank P6.5 | — | `Kaynak: [k1] + [k2]` (P6 `VERIFIED`) + uygulama: yazılmadı |
| 8 | Big Five puanları (E70 · A40 · C90 · N30 · O80) | Kurgusal persona verisi (eski /10 ×10) | eski salt-okunur dosya (7 · 4 · 9 · 7 · 8) | — | `Kaynak: kurgusal (persona verisi)` |
| 9 | 0-100 normalize yöntemi | Türetilmiş hesaplama | research-bank P7.4 (IPIP-NEO-120 yapısı) | persona-template §3.5.3 | `⚠️ DERIVED` |
| 10 | N ters-kodlama (duygusal denge 70 → N 30) | Türetilmiş ters kodlama | persona-template §3.5.3 (yüksek N = düşük denge) | research-bank P7.5 | `⚠️ DERIVED` |
| 11 | Ölçek künyesi: IPIP-NEO-120, Johnson (2014) | Gerçek-dünya | novopsych.com IPIP-NEO-120 Technical Paper | APA PsycNET (PsycTests kaydı) | `Kaynak: [k1] + [k2]` (P7.4 `VERIFIED`) |
| 12 | "IPIP-NEO Türkçe geçerli" iddiası | Doğrulanmadı | research-bank P7.5 / X-9 (yalnız Endonezya örneği) | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 13 | Boyut kodu eşlemesi (şablon O/C/E/A ↔ P7.3 tam adları) | Vault içi çelişki | persona-template §3.5.3 + mood-taxonomy §3.2.5 | research-bank P7.3/P7.5 | `⚠️ VERIFICATION REQUIRED` (P7.5: "kod esastır") |
| 14 | Mood küme adı (Profesyonel) | Vault referanslı atama | [[mood-taxonomy]] §3.2.5 | [[personas/index]] §6.3 | `Kaynak: kurgusal (atama); ad mood-taxonomy §3 listesinden` |
| 15 | Mood küme tanımı alıntısı + test etkisi (ADR-023 satır 7) | Vault verisi | [[mood-taxonomy]] §3.2.5 | [[ADR-023-persona-driven-testing]] | `Kaynak: [[mood-taxonomy]] (vault verisi)` |
| 16 | İkincil mood "Lider" (§3.3 #12) | Vault'ta tanımlı; atama farkı | eski salt-okunur persona dosyası | mood-taxonomy §3.3 (grup ataması: Kız Çocuk ×2) | `⚠️ VERIFICATION REQUIRED` — ad listede, bu grup için atamasız (§7.2) |
| 17 | Profesyonel kümesi atama notu (§2.2) | Vault notu | mood-taxonomy §2.2 + §3.2.5 (Yetişkin Kadın ×1 — Deniz Ö.) | — | `⚠️ Vault Steward` — bu persona için atama VAR (yetişkin erkek için atamasız) |
| 18 | Küme Big Five eğilimi (Yüksek Sorumluluk / düşük Dışadönüklük) | Kurgusal öneri | mood-taxonomy §3.2.5 + §1.2 | — | `⚠️ VERIFICATION REQUIRED` (§1.2 toplu etiket) |
| 19 | Tür BPM aralığı (Pop 80-120 · House 110-128) | Gerçek-dünya (ikincil) | turkipedia.com/Beats_per_minute | nevamuzik.com.tr | `Kaynak: [k1] + [k2]` (P4.4 `VERIFIED (ikincil — birincil ölçüm DEĞİL)`) |
| 20 | Taksonomi §3.2.5 "60-100 BPM" notu | Doğrulanmadı (eski taksonomi) | mood-taxonomy §1.2 + §3.2.5 | — | `⚠️ VERIFICATION REQUIRED` |
| 21 | Şarkı bazlı BPM | Doğrulanmadı (EXCLUDED) | research-bank P4.7 / X-2 | — | `⚠️ VERIFICATION REQUIRED` — yazılmadı |
| 22 | Favori sanatçı adları (Tarkan, Sertab Erener, Mirkelam, Diana Krall, Sting) | Persona tercihi (kurgusal); sanatçı realitesi P4 dışında | research-bank P4.2 (kapsam dışı) | — | `Kaynak: kurgusal (persona verisi)` + gerçek-dünya bilgi: `⚠️ VERIFICATION REQUIRED` |
| 23 | Cihaz: iPhone 15 Pro Max (spec'siz) | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 24 | Cihaz test referansı A34: 6.6" AMOLED, 1080×2340, 19.5:9, ~390 ppi, 120 Hz, Android 13 | Gerçek-dünya | GSMArena (gsmarena.com/samsung_galaxy_a34-12074.php) | Samsung resmi + en.wikipedia.org/wiki/Samsung_Galaxy_A34_5G | `Kaynak: [k1] + [k2]` (P3.3 `VERIFIED (3 kaynak)`) |
| 25 | Viewport ≈360×780 (DPR 3) / ≈412×915 (DPR 2.625) + masaüstü breakpoint (1280×720 · 1920×1080) | Türetilmiş (platform test girdisi) | Türetme: 1080÷3 · 2340÷3 (P3.1 verisi) | playwright.dev/docs/emulation (P8.5 `VERIFIED`) | `⚠️ DERIVED` (P3.2) + breakpoint `VERIFIED` |
| 26 | Diğer cihazlar (iş bilgisayarı, tablet, ev ses sistemi) spec'leri | Doğrulanmadı (EXCLUDED) | research-bank P3.3 / X-1 | — | `⚠️ VERIFICATION REQUIRED` — spec yazılmadı |
| 27 | Tarayıcı sürümü, internet hızı, OS payı | Paket dışı | research-bank P3.4 | — | `Kaynak: kurgusal (persona verisi)` |
| 28 | WCAG kodları ve eşikleri: 1.4.3 ≥4.5:1 · 1.4.11 ≥3:1 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 ≥24×24 px · 3.2.6 · 3.3.7 · 3.3.8 | Gerçek-dünya | w3.org/TR/WCAG22/ | w3.org/WAI/WCAG22/quickref/ + digitalpolicy.gov.hk | `Kaynak: [k1] + [k2]` (P5.4 `VERIFIED`) |
| 29 | Persona renk kombinasyonu (ofis/light tema) kontrastı | Hesap gerekli (türetilmiş) | research-bank P5.5 | — | `⚠️ DERIVED` |
| 30 | P5 kapsamında olmayan AA kriterleri (1.4.5, 2.4.3, 3.3.x tamamı…) | Doğrulanmadı | research-bank P5.5 / X-7 | — | `⚠️ VERIFICATION REQUIRED` — kullanılmadı |
| 31 | Test eşikleri: LCP ≤2500 ms · INP ≤200 ms · CLS ≤0.1 · FCP 1 sn + Slow 4G (150 ms · 1.6 Mbps ↓ / 750 Kbps ↑) | Gerçek-dünya | web.dev/articles/vitals + github.com/GoogleChrome/lighthouse (docs/throttling.md) | web.dev/articles/defining-core-web-vitals-thresholds + developer.chrome.com/docs/devtools/network/reference | `Kaynak: [k1] + [k2]` (P8.6 `VERIFIED`) |
| 32 | Eski dosyadaki üniversite/maaş/gelir/yüzde/sağlık iddiaları (X-6) | Bankada YOK | research-bank P1-P8 kapsamı + §6.4 X-6 | — | `⚠️ VERIFICATION REQUIRED` — bu dosyaya YAZILMADI |

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
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (`deniz-ozturk-profesyonel.md`) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz; üniversite/maaş/sağlık verisi yazılmaz (X-6, 6698 m.6) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engine (persona-test) sorumluluğunda | Layer violation → revert |

### §4.2 Yasak / Doğru

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `iPhone 15 Pro Max viewport = 393×852` (spec doğrulanmadı) | `spec yok — ⚠️ VERIFICATION REQUIRED (X-1); test viewport = banka A34 türevi ⚠️ DERIVED` |
| `Şarkı 92 BPM` / `caz 65 BPM` | Tür aralığı (Pop 80-120, House 110-128 — P4.4 VERIFIED ikincil) + şarkı BPM'i: `⚠️ VERIFICATION REQUIRED` |
| `KVKK rıza yaşı 16` | `GDPR m.8 = 16; TMK m.11 = 18; TR'de çocuk için özel düzenleme YOK (P6)` |
| `Big Five puanı bilimsel IPIP-NEO sonucu` | `puan kurgusal + normalize ⚠️ DERIVED (IPIP-NEO-120, Johnson 2014)` |
| `Maaş 400.000 TL` / `Spotify %65 pazar payı` / `Boğaziçi mezunu` | Bankada yok (X-6) → yazılmaz; yalnız `Kaynak: kurgusal (persona verisi)` veya `⚠️ VERIFICATION REQUIRED` |
| `Kaynak: internet` / `Kaynak: biliniyor` | `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` + 1 kaynak |

---

## §5 Workflow

### §5.1 Bu Persona İçin Üretim/Test Adımları

| Adım | Aksiyon | Çıktı |
|------|---------|-------|
| 1 | persona-template + docs-md-template oku (Guardrail #16) | Şablon + iskelet |
| 2 | [[research-bank]] P3-P8 + [[mood-taxonomy]] §3.2.5 + [[personas/index]] §6.3 oku | Etiketli veri + küme adı + yaş/mood |
| 3 | Eski salt-okunur persona kaynağını incele (yalnız kimlik/müzik/rutin; etiketsiz/maaş/sağlık iddia taşınmaz) | Kurgusal içerik |
| 4 | §3.5 11/11 alanı doldur → her satıra §4.5 etiketi yaz | Dolu §3 |
| 5 | Test Adımları ≥10 (ofis/çoklu-sekme/uzun oturum/düşük bant genişliği) → §6.1 → `vault-utf8-writer verify` → `log.md` append | Doğrulanmış dosya |

**Adım ayrıntıları:**

| Adım | Ayrıntı | Sıklık |
|------|---------|--------|
| 1 | `.ai/.templates/personas/persona-template.md` §3.3 + §3.5; `.ai/.templates/docs-md-template.md` | Her üretimde |
| 2 | research-bank P3 (cihaz), P4 (müzik/BPM), P5 (WCAG), P6 (KVKK), P7 (Big Five), P8 (test eşikleri) | Her üretimde |
| 3 | Eski dosya: `.ai/personas/yetiskin-kadin/` salt-okunur; yalnız kurgusal kimlik/müzik taşınır; üniversite (X-6), maaş/gelir/yüzde ve sağlık verisi KOPYALANMAZ | Yalnız ilk üretimde |
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
| Yer tutucusu bloğu dışında | §4 bloğu dışındaki `{{` | Yer tutucuyu doldur veya sil |
| 500 satır altı | `verify.lines < 500` | Açıklama satırı/tablo ekle, yeniden verify |
| Persona-özel eşik uydurma | "Deniz için LCP 3000 ms" benzeri | Sil; genel P8 eşiği + `⚠️ DERIVED` gerekir (X-10) |
| Eski dosyadan maaş/üniversite/sağlık taşıma | X-6 / 6698 m.6 ihlali | Sil; §7.2'ye "yazılmadı" kaydı ekle |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) + ek alanlar (`group`, `age`, `mood`, `persona_id`, `veli_onayı_gerekli`) — toplam 12 alan
- [ ] Tek H1 + Zorunlu Bağlantılar satırı (≥ 1 wiki-link)
- [ ] §1-§7 başlıkları eksiksiz; §3 altında 11 alt bölüm (Kimlik → Kaynak & Doğrulama) dolu
- [ ] §4'ün ilk maddesi = §3.3 Şablon-Önce bloğu birebir (5 madde + Durum/Aksiyon tablosu)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] Her alanda/satırda kaynak etiketi var (`kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED` · `⚠️ DERIVED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralıkta, her birinde 1 satır gerekçe + N ters kodlama notu
- [ ] Test Adımları tablosu ≥ 10 satır ve 6 sütun (Adım / Eylem / Beklenen / Doğrulama / Metrik (research-bank P8 değeri) / WCAG kriteri)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, yer tutucusu kalmamış (yalnız §4 Şablon-Önce bloğundaki `VARIABLE` kural metni)
- [ ] Wiki-link'ler `[[...]]` formatında; hedefi olmayanlar §7.1'de `📋 planlanan`
- [ ] Yetişkin persona: `veli_onayı_gerekli: false`; 16 yaş altı KVKK notu YOK (yalnız genel TMK 18 / COPPA 13 / GDPR 16 çerçevesi)
- [ ] Yaş = 41 (index §6.3 SSOT); §7.2'de 41 ↔ 42 çelişkisi iki değerle `⚠️` kayıtlı, indirgeme yok
- [ ] Cihaz: yalnız A34 spec'i VERIFIED; viewport `⚠️ DERIVED`; iPhone/diğer cihazlar `⚠️ VERIFICATION REQUIRED`
- [ ] WCAG kodları yalnız P5.2/P5.4 listesinden (1.4.3 · 1.4.11 · 1.4.13 · 2.4.7 · 2.4.11 · 2.5.7 · 2.5.8 · 3.2.6 · 3.3.7 · 3.3.8); persona renkleri `⚠️ DERIVED`
- [ ] BPM yalnız tür aralığı (P4.4); şarkı BPM'i yok; taksonomi §3.2.5 notu `⚠️ VERIFICATION REQUIRED`
- [ ] X-6 / 6698 m.6: üniversite, maaş/gelir, yüzde istatistikleri ve sağlık verisi yazılmadı
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
| Frontmatter alan | 7 zorunlu + 5 ek (12) |
| Yaş | 41 (index §6.3 SSOT) · §7.2 çelişkisi 41 ↔ 42 `⚠️` |
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
| `[[mood-taxonomy]]` | Mood küme adları ve tanımları (§3.2.5 Profesyonel · §3.3 Lider · §2.2 atama notu) | ✅ `.ai/.personas/mood-taxonomy.md` |
| `[[personas/test-scenarios-mapping]]` | Test senaryosu eşlemesi | ✅ `.ai/.personas/test-scenarios-mapping.md` |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) | ✅ `.ai/.personas/methodology.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` |

*`📋 planlanan` satır hedef dosya diskte oluşana kadar kırık kabul edilir; oluştuğunda `✅`ye çevrilir ve `log.md`'ye append edilir.*

### §7.2 Çakışma Kayıtları (eski salt-okunur kaynak ↔ index SSOT)

| Alan | Eski dosya (salt-okunur) | Yeni değer (SSOT) | Karar |
|------|--------------------------|-------------------|-------|
| Yaş | **42** | **41** | [[personas/index]] §6.3 kazanır; **iki değer de `⚠️` ile korunur, indirgeme YOK** (bu dosyada 41 kullanılır) |
| Doğum tarihi | 7 Mart 1984 | 7 Mart 1985 | Yaş 41 ile tutarlılık için kurgusal türetme (eski değer §7.2'de saklı) |
| Meslek unvanı | Pazarlama Direktörü / CMO | Şirket Yöneticisi | Künye standardı (genel meslek tanımı) kullanıldı; ayrı unvan + şirket adı yazılmadı |
| Eğitim (üniversite adları) | Boğaziçi / Columbia vb. | yazılmadı | X-6 — research-bank'te yok → §4.5 kuralı 4 |
| Maaş / gelir / pazar payı yüzdeleri | Kaynaksız TL tutarları ve % değerleri | yazılmadı | ADR-005 + X-6 (maaş/gelir asla) |
| Sağlık verisi (göz ölçüsü, check-up, beslenme takibi) | Eski dosyada ayrıntılı | yazılmadı | 6698 m.6 — sağlık verisi kurgusal olmak zorunda; sayısal değer kopyalanmadı |
| Birincil cihaz | iPhone 15 Pro Max (spec'siz, X-1) | Test viewport: banka A34 türevi (`⚠️ DERIVED`) + masaüstü P8.5 breakpoint | [[research-bank]] P3/P8 — cihaz spec'i yazılmadı |
| İkincil mood | Lider | Lider (mood-taxonomy §3.3 #12 — ad listede) | Küme adı taşındı; grup ataması "Kız Çocuk ×2" olduğundan bu persona için atamasız → `⚠️ VERIFICATION REQUIRED` + `⚠️ Vault Steward` |
| Küme Big Five eğilimi | mood-taxonomy §3.2.5 "düşük Dışadönüklük (O)" | Persona puanı E = 70 (eski 7/10 ×10) | İki kurgusal değer; mood-taxonomy §1.2 `⚠️` toplu etiketi uygulanır, puan değiştirilmedi |
| BPM | Eski dosya "80-110 BPM" + taksonomi "60-100" | Tür aralığı P4.4 (VERIFIED ikincil); şarkı BPM'i yok | §4.5 + X-2 + mood-taxonomy §1.2 |
| Etki alanındaki istatistik/idDia satırları | Kaynaksız yüzde/idDia satırları | yazılmadı | §4.5 kuralı 4 (bankada yok → yazma) |

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — persona-template §3.5 (11/11) + research-bank P3-P8 etiketli veri; eski salt-okunur dosyadan kimlik/müzik taşındı (üniversite/maaş/sağlık verileri X-6 ve 6698 m.6 gereği kopyalanmadı), yaş index §6.3'e göre 41 (eski 42 §7.2'de `⚠️` ile saklandı); Big Five eski /10 → ×10 (N ters-kodlama 30), mood Profesyonel (§3.2.5) + ikincil Lider, test adımları ofis/çoklu-sekme/uzun oturum/düşük bant genişliği odaklı 17 satır / 6 sütun, Kaynak tablosu 32 satır eklendi | vault-updater (persona) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Persona — SSOT: personas/yetiskin-kadin/deniz-ozturk-profesyonel
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
