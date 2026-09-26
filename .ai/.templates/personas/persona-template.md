---
title: "CoreMusic — Persona Şablonu"
type: template
category: personas
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-26
---

# CoreMusic — Persona Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

Bu şablon, CoreMusic platformu için **persona dosyalarının** üretileceği standart dış iskeleti tanımlar: `kirik-ad-surname-mood.md` biçimli her persona dosyası bu şablondan başlar. Persona, hedef kullanıcının **kurgusal ama kaynak-destekli** profilidir; AI'ın o kişi gibi davranıp (Level 1 rol testi), o kişinin cihaz/tema/internet koşullarında gerçek tarayıcıda test yapması (Level 2/3) için kullanılır. Guardrail #16 gereği **şablonsuz persona dosyası üretilmez**.

| Alan | Değer |
|------|-------|
| Template Name | `persona-template.md` |
| Template Path | `.ai/.templates/personas/persona-template.md` |
| Hedef Dosya Tipi | Persona profili (`kirik-ad-surname-mood.md`) |
| Hedef Konum | `.ai/personas/<segment>/<dosya>.md` (segment klasörü örn. `genc-kiz/`, `orta-yasli/`) |
| Guardrail | #16 (Template Mandatory) — şablonsuz persona üretilemez |
| Birincil Yazar | QA Engineer (persona-test) + UI Designer (erişilebilirlik verisi) |
| İkincil Yazarlar | Master Orchestrator (registry senkronu), UX Researcher |
| Routing | `[[../../AGENTS.md]]` §6: test, persona, accessibility → QA Engineer; vault/registry → MO |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter) |
| Zorunlu Blok | §3.3 Şablon-Önce Kural Bloğu — persona versiyonu (üretilen dosyada SİLİNEMEZ) |
| Zorunlu Alan Havuzu | §3.5 — 11 alt bölüm (Kimlik → Kaynak & Doğrulama) |
| Zorunlu Tablolar | §2 Kapsam/Kapsam Dışı + §3.5 Test Adımları (≥10) + §6.1 Doğrulama listesi + §7.2 Değişiklik Geçmişi |
| Kayıt Kuralı | Değişiklik Geçmişi append-only — mevcut satıra dokunulmaz |
| Kanıt — Vault | `.ai/CLAUDE.md` (anayasa), `.ai/.templates/index.md` (registry), `.ai/.templates/documentation/docs-md-template.md` (yapısal anahtar) |
| Kanıt — Format | 7 alanlı frontmatter, `[[wiki-link]]`, 8-bölüm iskeleti, ≥500 satır |
| Korunan Bilgi | Dil (TR), mojibake yasak, REDACTED/KVKK, In-Place, append-only log |
| Değişken Formatı | `{{VARIABLE}}` (bkz. §3.4) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII, küçük harf, tire |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-26 |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

Persona **kullanıcı taklidi yapar**: var olmayan bir insanı, gerçekçi ama doğrulanabilir veriyle canlandırır. Karar kaydı üretmez (ADR), test stratejisi yazmaz (strategy), kod üretmez.

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| Kullanıcı persona profili | ✅ Bu şablon | — |
| Genel doküman / rehber | `[[.templates/documentation/docs-md-template]]` | ❌ |
| Mimari karar kaydı | `[[../adr/adr-template]]` | ❌ |
| Test protokolü / stratejisi | `[[../testing/phpunit-template]]` + `personas/methodology` | ❌ |
| Ajan profili (AI rolü değil insan rolü) | `[[../agents/agents-template]]` | ❌ |
| UI spec / ekran akışı | `[[../ui-design/screen-spec-template]]` · `[[../ui-design/flow-template]]` | ❌ |
| Kod dosyası (PHP/JS/CSS/SQL/C++) | İlgili kod şablonu (`../backend/*`, `../frontend/*`, `../other/*`) | ❌ |

**Ayırıcı test:** "Dosya bir kullanıcıyı mı taklit ediyor?" → Evet ise bu şablon. "Test adımlarını mı tanımlıyor?" → Evet ise `personas/methodology` + `testing/persona-test-protocol`. "Bir kararı mı kaydediyor?" → Evet ise `adr-template`.

### §1.2 Şablon-Önce Kuralı (Temel İlke)

Bu şablonu kullanırken uygulanan kural, üretilen persona dosyasının içine de gömülür: **AI her dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları okur.** Kural metni §3.3'te persona versiyonuyla verilmiştir; şablon ve insan geliştirici aynı metne uyar.

| Boyut | Açıklama |
|-------|----------|
| Ne zaman | Her dosya yazımından ÖNCE (okuma adımından hemen sonra) |
| Nereden | `.ai/.templates/index.md` → kategori (`personas/`) → şablon |
| Ne yapılacak | Şablon varsa ona göre yaz; yoksa bu belgedeki standart formata göre yaz |
| İhlal sonucu | Dosya geçersiz sayılır, revert edilir, `log.md`'ye ERROR girilir (Guardrail #16) |
| İstisna | Yok — persona dosyaları da ≥500 satır derinlik kuralına tabidir |

---

## §2 Kapsam

### §2.1 Kullanım Anları

| # | Dosya Tipi | Kullanım Anı | Sorumlu Agent |
|---|-----------|--------------|---------------|
| 1 | Yeni persona dosyası (`personas/<segment>/*.md`) | Yeni kullanıcı segmenti tanımlanırken | QA Engineer + UX Researcher |
| 2 | Mevcut persona revizyonu (In-Place) | Test sonucu persona ile çelişince | QA Engineer |
| 3 | AI Rol Kartı güncelleme | Rol testi (Level 1) davranışı sapınca | QA Engineer |
| 4 | Test Adımları güncelleme | Yeni ekran/akış eklendiğinde | QA Engineer |
| 5 | Kaynak & Doğrulama tablosu | Yeni gerçek-dünya iddiası eklendiğinde | Yazar agent + MO denetimi |

### §2.2 Kapsam

- **Kapsam:** Persona dosyasının dış iskeleti (frontmatter + H1 + §1-§7), zorunlu Şablon-Önce bloğu, 11 parçalı alan havuzu (§3.5), kaynak etiketleme kuralı (§4.5), dosya adı formatı, derinlik/dil kuralları, doğrulama listesi.
- **Kapsam dışı:** Mood taksonomisinin kendisi (`personas/mood-taxonomy`), test seviyeleri ve başarı metrikleri (`personas/methodology`, `testing/persona-test-protocol`), persona kataloğu (`personas/index`), test raporu şablonu (rapor ayrı dosyadır), ADR (`../adr/*`).

### §2.3 Hedef Kitle

| Kitle | Bu Şablonu Nasıl Kullanır |
|-------|---------------------------|
| AI ajanları (QA/UI) | Görevde şablonu oku → kopyala → §3.5 alanlarını doldur → §6.1 doğrula |
| İnsan geliştirici | Bölüm tablolarını doldurur, §7.2'yi tarihçe olarak sürdürür |
| Vault Steward | Denetimde §4.5 kaynak etiketlerini ve §6.1 listesini kontrol eder |
| Test otomasyonu (Playwright) | Üretilen dosyadaki Cihaz & Teknoloji + Test Adımları satırlarını script'e besler |

### §2.4 Kapsam Dışı İstisnalar

| Durum | Ne Yapılır |
|-------|-----------|
| Persona hem kişisel blog tanıtımı da içeriyor | Önce MO'ya sor (Contradiction Gate); sonra tek şablon seçilir |
| Eski vault'taki 100+ satırlık fiziksel detay taşıyan persona | In-Place sadeleştirme; dosya adı değişmez, §3.5.2 5-8 satıra çekilir, içerik silinmez (`log.md`'ye kaydedilir) |
| Persona gerçek kişiye ait (gerçek kullanıcı verisi) | DUR — KVKK (§4.7); kurguya çevrilir veya `[REDACTED]` |
| Şablonla vault çelişiyor | DUR → `[[../CLAUDE.md]]` §2.1 öncelik sırası uygulanır |

---

## §3 Mimari — Üretilen Persona Dosyasının İskeleti

### §3.1 Dış İskelet (Frontmatter + H1 + Bağlantılar)

````markdown
---
title: "CoreMusic Persona — {{PERSONA_AD_SOYAD}}"
type: persona
category: personas
version: 1.0.0
status: active
authority: reference
updated: {{DATE}}
---

# CoreMusic Persona — {{PERSONA_AD_SOYAD}}

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/mood-taxonomy]] · [[../.templates/index]]

---

## §1 Amaç
## §2 Kapsam
## §3 Mimari
## §4 Kurallar
## §5 Workflow
## §6 Doğrulama
## §7 Referanslar

---

**Authority:** {{AUTHOR}}
**Last Updated:** {{DATE}}
**Mode:** Red Team · Human Mode · Truth Mode
````

### §3.2 8-Bölüm Eşlemesi (İskelet → Persona İçeriği)

| İskelet Bölümü | Persona Dosyası Karşılığı | Zorunlu İçerik |
|-----------------|---------------------------|----------------|
| Başlık | H1 + frontmatter | 7 zorunlu alan + tek H1 |
| §1 Amaç | Bu persona neden var, hangi test için | 2-5 cümle + segment |
| §2 Kapsam | Hangi akışlar/dokunma noktaları | 2 sütunlu tablo ≥ 3 satır |
| §3 Mimari | §3.5 alan havuzunun dolu hâli | 11 alt bölümün hepsi dolu |
| §4 Kurallar | Persona verisi yazım kuralları + Şablon-Önce bloğu | Numaralı kurallar + Yasak/Doğru |
| §5 Workflow | Persona üretme adımları | 5 adım + tablo |
| §6 Doğrulama | §6.1 kontrol listesi | `- [ ]` listesi + metrik |
| §7 Referanslar | Bağlantılar + tarihçe | Wiki-link tablosu + Değişiklik Geçmişi |

### §3.3 ZORUNLU — Şablon-Önce Kural Bloğu (Persona Versiyonu — Kopyalanacak Metin)

> **Bu blok üretilen her persona dosyasında §4'ün ilk maddesi olarak birebir bulunmak ZORUNDA'dır.** Kısaltılamaz, yorumlanamaz, yerine başka cümle konulamaz.

````markdown
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
````

### §3.4 Değişken Tablosu

| Değişken | Açıklama | Örnek |
|----------|----------|-------|
| `{{PERSONA_AD_SOYAD}}` | Kurgusal ad soyad (Türkçe karakterli olabilir) | Zeliha Demir |
| `{{PERSONA_SEGMENT}}` | Segment klasörü | `genc-kiz` |
| `{{PERSONA_DOSYA_ADI}}` | ASCII, küçük harf, tire (§4.8) | `zeliha-demir-arabesk.md` |
| `{{CM_KULLANICI_ID}}` | `CM-XX-YY-ZZZ-XXX` | `CM-ZD-15-ARB-ADA` |
| `{{MOOD_BIRINCIL}}` / `{{MOOD_IKINCIL}}` | Mood-taxonomy küme adı | Arabesksever / Moody |
| `{{CIHAZ_MODELI}}` + `{{VIEWPORT}}` | Cihaz + ekran çözünürlüğü | Samsung Galaxy A34 · 2340x1080 |
| `{{TEMA}}` | `light` / `dark` | dark |
| `{{INTERNET_HIZI}}` | Simüle edilecek bağlantı | 35 Mbps (ADSL) |
| `{{BFL_*}}` | Big Five 5 puanı (0-100) | `{{BFL_DISADONUKLUK}}` … |
| `{{KAYNAK_ETIKETI}}` | §4.5 formatı | `Kaynak: kurgusal (persona verisi)` |
| `{{DATE}}` | YYYY-MM-DD | 2026-09-26 |
| `{{AUTHOR}}` | Yazar agent | QA Engineer |
| `{{TEST_ADIMLARI}}` | ≥10 adım satırı (Adım/Beklenen/Doğrulama) | bkz. §3.5.10 |

### §3.5 Persona Alan Havuzu (Boş İskelet — 11 Zorunlu Alt Bölüm)

> **Kural:** 11 alt bölümün tamamı üretilen dosyada bulunur; boş bırakılamaz. Dolu olmayan alan `yok` / `VERIFICATION REQUIRED` olarak açıkça yazılır.

#### §3.5.1 Kimlik Kartı

````markdown
## §3 Mimari

### Kimlik Kartı

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Ad Soyad** | {{PERSONA_AD_SOYAD}} | Kaynak: kurgusal (persona verisi) |
| **Yaş** | {{YAS}} | Kaynak: kurgusal (persona verisi) |
| **Doğum Tarihi** | {{DOGUM_TARIHI}} | Kaynak: kurgusal (persona verisi) |
| **Cinsiyet** | {{CINSIYET}} | Kaynak: kurgusal (persona verisi) |
| **Şehir / İlçe / Semt** | {{SEHIR}} / {{ILCE}} / {{SEMT}} | Kaynak: kurgusal (persona verisi) |
| **Okul / Meslek** | {{OKUL_MESLEK}} | Kaynak: kurgusal (persona verisi) |
| **Ulaşım** | {{ULASIM}} | Kaynak: kurgusal (persona verisi) |
| **Online Rumuz** | {{ONLINE_RUMUZ}} | Kaynak: kurgusal (persona verisi) |
| **CoreMusic Kullanıcı ID** | {{CM_KULLANICI_ID}} | Kaynak: kurgusal (persona verisi) |
````

**`CoreMusic Kullanıcı ID` formatı:** `CM-XX-YY-ZZZ-XXX`

| Parça | Anlam | Kural | Örnek |
|-------|-------|-------|-------|
| `CM` | Sabit önek | Değiştirilmez | `CM` |
| `XX` | Ad-soyad baş harfleri | 2 ASCII harf | `ZD` (Zeliha Demir) |
| `YY` | Yaş | 2 hane (10-99) | `15` |
| `ZZZ` | Tür kodu | 3 ASCII harf | `ARB` (Arabesk) |
| `XXX` | Şehir kodu | 3 ASCII harf | `ADA` (Adana) |

> Türkçe karakter ASCII'ye katlanır: ı→i, ş→s, ğ→g, ü→u, ö→o, ç→c (örn. Şule → `SU`... değil `SL` — ilk iki farklı harf: ad harfi + soyad harfi, bkz. §4.8 dosya adı kuralı).

#### §3.5.2 Fiziksel Özet (5-8 satır — STANDARD)

````markdown
### Fiziksel Özet

> **Standard:** 5-8 satır. Eski persona dosyalarındaki 100+ satırlık ayrıntı (burun, kaş, diş, ayakkabı numarası, postür, el yazısı…) BU ŞABLONDA YASAKTIR — sadece testi etkileyen görsel erişim/temsiliyet notları kalır.

| Boy | {{BOY}} | Kaynak: kurgusal (persona verisi) |
|-----|---------|-----------------------------------|
| **Kilo / Vücut tipi** | {{KILO_TIPE}} | Kaynak: kurgusal (persona verisi) |
| **Saç / Göz** | {{SAOC_GÖZ}} | Kaynak: kurgusal (persona verisi) |
| **Gözlük / Lens / İşitme cihazı** | {{GORUNUM_DURUMU}} | Kaynak: kurgusal (persona verisi) |
| **Giyim / temsil notu** | {{GIYIM_NOTU}} (avatar, profil fotoğrafı, kontrast etkisi) | Kaynak: kurgusal (persona verisi) |
| **UI'ı etkileyen fiziksel kısıt** | {{UI_ETKI}} (ör. küçük parmak → dokunma hedefi, gözlük → font boyutu) | Kaynak: kurgusal (persona verisi) |
````

*Asgari 5, azami 8 satır. Fazlası §2.4'e göre In-Place sadeleştirilir; silinen satır `log.md`'ye taşınır.*

#### §3.5.3 Big Five (OCEAN) — IPIP-NEO

````markdown
### Big Five (OCEAN)

**Ölçek:** IPIP-NEO (International Personality Item Pool — NEO formu) · **Aralık:** 0-100 · **Zorunlu:** 5/5 boyut + her boyut için 1 satır gerekçe.

| Boyut | Kod | Puan (0-100) | Gerekçe (1 satır) | Kaynak |
|-------|-----|--------------|-------------------|--------|
| Dışadönüklük | O | {{BFL_DISADONUKLUK}} | {{GEREKCE_O}} | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO |
| Uyumluluk | C | {{BFL_UYUMLULUK}} | {{GEREKCE_C}} | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO |
| Sorumluluk | E | {{BFL_SORUMLULUK}} | {{GEREKCE_E}} | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO |
| Duygusal Denge (N tersi) | N | {{BFL_DUYGUSAL_DENGE}} | {{GEREKCE_N}} | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO |
| Deneyime Açıklık | A | {{BFL_DENEYIME_ACIKLK}} | {{GEREKCE_A}} | Kaynak: kurgusal (persona verisi); ölçek: IPIP-NEO |
````

| Kural | Değer |
|-------|-------|
| Puan aralığı | 0-100 (eski `/10` puanı 10 ile çarpılarak ölçeklenir: 4/10 → 40) |
| Yön | N boyutunda **yüksek puan = düşük duygusal denge** (ters kodlama notu yazılır) |
| Gerekçe | Her boyutta tam 1 satır — kanıt değil, karakter davranışı |
| Doğrulama | `Kaynak: kurgusal (persona verisi)` + ölçek künyesi (`IPIP-NEO`) gerçek-dünya iddiasıdır → §4.5'e göre 2. kaynak (ölçek tanımı) eklenir |

#### §3.5.4 Mood Profili

````markdown
### Mood Profili

| Alan | Değer |
|------|-------|
| **Birincil küme** | {{MOOD_BIRINCIL}} |
| **İkincil küme** | {{MOOD_IKINCIL}} |
| **Küme tanımı (alıntı)** | {{MOOD_TANIM}} |
| **Tetikleyici durumlar** | {{MOOD_TETIKLEYICI}} |
| **UI etkisi** | {{MOOD_UI_ETKISI}} (öneri sırası, renk/tema, boş durum metni) |
````

**Küme adı zorunlu olarak `[[personas/mood-taxonomy]]` listesinden alınır** — uydurma küme adı yazılmaz. Taksonomi diskte yoksa: `⚠️ VERIFICATION REQUIRED` + mevcut en yakın küme adı kullanılır ve `log.md`'ye eksiklik kaydedilir.

#### §3.5.5 Müzik DNA'sı

````markdown
### Müzik DNA'sı

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Türler (3-5, sıralı)** | 1) {{TUR_1}} 2) {{TUR_2}} 3) {{TUR_3}} (4. ve 5. opsiyonel) | Kaynak: kurgusal (persona verisi) |
| **Favori sanatçılar (3-5)** | {{SANATCI_1}}, {{SANATCI_2}}, {{SANATCI_3}} | Kaynak: kurgusal (persona verisi) |
| **BPM aralığı** | {{BPM_ALT}}–{{BPM_UST}} BPM | Kaynak: [sanatçı/şarkı kaynağı 1] + [kaynak 2] |
| **Dinleme saati (hafta içi / sonu)** | {{DINLEME_SAATI}} | Kaynak: kurgusal (persona verisi) |
| **Platform** | {{PLATFORM}} (CoreMusic + {{IKINCIL_PLATFORM}}) | Kaynak: kurgusal (persona verisi) |
| **Keşif davranışı** | {{KESIF}} (arkadaş tavsiyesi / algoritma / editör listesi) | Kaynak: kurgusal (persona verisi) |
| **Premium olasılığı** | {{PREMIUM_YUZDE}}% | Kaynak: kurgusal (persona verisi) |
````

> **Not:** Sanatçı adı ve BPM değidi **doğrulanabilir gerçek-dünya iddiasıdır** (§4.5) — en az 2 bağımsız kaynak (ör. sanatçı diskografisi + müzik veritabanı) gerekir; bulunamazsa `⚠️ VERIFICATION REQUIRED`.

#### §3.5.6 Cihaz & Teknoloji

````markdown
### Cihaz & Teknoloji

| Alan | Değer | Kaynak |
|------|-------|--------|
| **Cihaz modeli** | {{CIHAZ_MODELI}} | Kaynak: [üretici spec sayfası] + [bağımsız inceleme] |
| **Ekran çözünürlüğü → viewport** | {{COZUNURLUK}} → `{{VIEWPORT_W}}x{{VIEWPORT_H}}` (DPR {{DPR}}) | Kaynak: [üretici spec] + [cihaz veritabanı] |
| **OS** | {{ISLETIM_SISTEMI}} {{OS_SURUMU}} | Kaynak: [üretici] + [yayın payı kaynağı] |
| **Tarayıcı** | {{TARAYICI}} {{TARAYICI_SURUMU}} | Kaynak: kurgusal (persona verisi) |
| **İnternet hızı** | {{INTERNET_HIZI}} (ev) / {{MOBIL_HIZ}} (mobil) | Kaynak: [sağlayıcı paket sayfası] + [hız raporu] |
| **Tema** | {{TEMA}} (light / dark) | Kaynak: kurgusal (persona verisi) |
| **Ekran süresi / kullanım** | {{EKRAN_SURESI}} | Kaynak: kurgusal (persona verisi) |
| **Teknoloji okuryazarlığı** | {{TEKNO_OKURYAZARLIK}}/10 | Kaynak: kurgusal (persona verisi) |
````

**Viewport karşılığı zorunludur** — Level 2 (Browser MCP) testi bu satırdan `Emulation.setDeviceMetricsOverride` besler. Çözünürlük + DPR → CSS viewport (`cozunurluk / DPR`).

#### §3.5.7 Erişilebilirlik / Kısıt

````markdown
### Erişilebilirlik / Kısıt

| Alan | Durum | WCAG Etkisi | Kaynak |
|------|-------|-------------|--------|
| **Görme** | {{GORUNME}} (yok / renk körlüğü / düşük görme) | {{WCAG_GORUNME}} | Kaynak: kurgusal (persona verisi); kriter: WCAG 2.2 AA |
| **İşitme** | {{ISITME}} | {{WCAG_ISITME}} | Kaynak: kurgusal (persona verisi); kriter: WCAG 2.2 AA |
| **Hareket / motor** | {{HAREKET}} | {{WCAG_HAREKET}} | Kaynak: kurgusal (persona verisi); kriter: WCAG 2.2 AA |
| **Kognitif / dikkat** | {{KOGNITIF}} | {{WCAG_KOGNITIF}} | Kaynak: kurgusal (persona verisi); kriter: WCAG 2.2 AA |
````

**Kısıt yoksa alan `yok` yazılır — satır ATLANMAZ.** WCAG kriter numarası verilecekse (ör. 1.4.3 Kontrast) gerçek-dünya iddiasıdır → §4.5 (2 kaynak).

#### §3.5.8 Kişilik & Davranış

````markdown
### Kişilik & Davranış

- {{MADDE_1}}
- {{MADDE_2}}
- {{MADDE_3}}
- (azami 5 madde)

| Davranış | Değer | Kaynak |
|----------|-------|--------|
| **Satın alma davranışı** | {{SATIN_ALMA}} (plansız-duygusal / araştırmacı / fiyat-odaklı) | Kaynak: kurgusal (persona verisi) |
| **Sabır eşiği** | {{SABIR_ESIGI}} (ör. 3 sn'de sıkılır, spinner'a 10 sn dayanır) | Kaynak: kurgusal (persona verisi) |
| **Hata tepkisi** | {{HATA_TEPKISI}} | Kaynak: kurgusal (persona verisi) |
| **Reklam toleransı** | {{REKLAM_TOLERANSI}}/10 | Kaynak: kurgusal (persona verisi) |
````

#### §3.5.9 AI Rol Kartı

````markdown
### AI Rol Kartı

Sen {{PERSONA_AD_SOYAD}}'sin. {{YAS}} yaşında, {{SEHIR}}/{{ILCE}}'de yaşıyorsun, {{OKUL_MESLEK}}.
{{MOOD_BIRINCIL}} bir kişiliğin var; ikincil olarak {{MOOD_IKINCIL}}'sin.
Müzik zevkin: {{TUR_1}}, {{TUR_2}}, {{TUR_3}}. En sevdiklerin: {{SANATCI_1}}, {{SANATCI_2}}, {{SANATCI_3}}.
{{CIHAZ_MODELI}} kullanıyorsun, {{TEMA}} temada, internetin {{INTERNET_HIZI}}.
{{GORUNME}} / {{ISITME}} / {{HAREKET}} kısıtın: {{ERISILEBILIRLIK_OZET}}.
Sabır eşiğin {{SABIR_ESIGI}}; satın almanda {{SATIN_ALMA}}.
Test edilecek akışlar: {{TEST_AKISLARI}}.
Davranış notları:
- {{DAVRANIS_NOTU_1}}
- {{DAVRANIS_NOTU_2}}
- {{DAVRANIS_NOTU_3}}
````

| Kural | Değer |
|-------|-------|
| Biçim | Tek paragraf + madde listesi; `Sen ...'sin` ile başlar |
| Kapsam | Kimlik, mood, müzik DNA, cihaz, kısıt, davranış, test kapsamı |
| Yasak | Gerçek kişilik/gerçek API anahtarı/KVKK verisi; `{{...}}` yer tutucusu bırakmak |
| Kullanım | Level 1 (AI Rol Testi) prompt'a birebir gömülür |

#### §3.5.10 Test Adımları (≥10 adım)

````markdown
### Test Adımları

| # | Adım | Beklenen | Doğrulama yöntemi |
|---|------|----------|-------------------|
| 1 | Ana sayfa yükleme | LCP < {{LCP_HEDEF}}s, {{MOOD_BIRINCIL}} içerik üstte | Performance trace + screenshot |
| 2 | Kayıt / giriş akışı | Türkçe karakterli ad kabul, ≤3 adım | Manuel + form assertion |
| 3 | Karşılama / öneri ekranı | Öneriler {{TUR_1}} ağırlıklı | İçerik assertion |
| 4 | Arama ({{SANATCI_1}}) | Fuzzy sonuç, <300ms | DOM assertion + timing |
| 5 | SPA navigasyon | <{{NAV_MS}}ms, müzik kesintisiz | Trace + console log |
| 6 | Player kontrol | 48x48px hedef, klavye erişimi | Erişilebilirlik audit |
| 7 | Çalma listesi oluştur/düzenle | <2s, Türkçe ad | DOM assertion |
| 8 | Paylaşım | Bağlantı kopyalanır, hata yok | Manuel + console |
| 9 | Profil / ayarlar | Tercihler kalıcı | LocalStorage kontrolü |
| 10 | Hata sayfaları (404/403) | Türkçe mesaj, dönüş butonu | Manuel + screenshot |
| 11 | Erişilebilirlik audit | Lighthouse A11y ≥ {{A11Y_HEDEF}} | Lighthouse / axe |
| 12 | Bağlantı yavaşlatma | {{INTERNET_HIZI}} simülasyonu, hata yok | Network throttling + trace |
````

**Asgari 10 satır.** Her satırda 3 sütun zorunlu: `Adım` · `Beklenen` · `Doğrulama yöntemi`. Seviye karşılıkları: `[[personas/methodology]]` Level 1 (AI rol) · Level 2 (Browser MCP) · Level 3 (Playwright E2E).

#### §3.5.11 Kaynak & Doğrulama Tablosu

````markdown
### Kaynak & Doğrulama

| # | Alan / İddia | İddia Türü | Kaynak 1 | Kaynak 2 | Etiket |
|---|--------------|-----------|----------|----------|--------|
| 1 | Ad, doğum tarihi, semt, okul, rumuz | Kurgusal persona verisi | — | — | `Kaynak: kurgusal (persona verisi)` |
| 2 | Cihaz teknik özellikleri (çözünürlük, OS) | Gerçek-dünya | [üretici spec] | [bağımsız inceleme] | `Kaynak: [k1] + [k2]` |
| 3 | Sanatçı / BPM | Gerçek-dünya | [diskografi] | [müzik veritabanı] | `Kaynak: [k1] + [k2]` |
| 4 | WCAG kriteri | Gerçek-dünya | [W3C WCAG 2.2] | [erişilebilirlik rehberi] | `Kaynak: [k1] + [k2]` |
| 5 | KVKK / yaş sınırı | Gerçek-dünya | [mevzuat] | [ikincil kaynak] | `Kaynak: [k1] + [k2]` veya `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| 6 | Big Five ölçeği (IPIP-NEO) | Gerçek-dünya | [IPIP kaynağı] | [psikometri kaynağı] | `Kaynak: [k1] + [k2]` |
````

*Bu tablo üretilen dosyada **boş satırsız** doldurulur; yeni satır eklenir, mevcut satır değiştirilmez (append-only).*

### §3.6 Anti-Pattern Tablosu (Üretim Sırası Sık Yapılan Hatalar)

| # | Anti-Pattern | Neden Kötü | Doğrusu |
|---|--------------|-----------|---------|
| 1 | Şablon-Önce bloğunu "gereksiz" bulup çıkarmak | Guardrail #16 metni zayıflar | §3.3 bloğu birebir kalır |
| 2 | Fiziksel özellikleri 100+ satıra dökmek | Token israfı, test değeri yok | §3.5.2 → 5-8 satır standart |
| 3 | Big Five'ı `/10` ile yazmak | Ölçek tutarsız (IPIP-NEO 0-100) | 0-100 + 1 satır gerekçe |
| 4 | Kaynak etiketi yazmamak | Hallüsinasyon (ADR-005) | Her alan satırında §4.5 etiketi |
| 5 | 2. kaynağı uydurmak | Doğrulanmayan "kaynak" = yalan | Bulunamazsa `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| 6 | Test adımını "güzel görünür" diye yazmak | Test edilemez iddia | Adım/Beklenen/Doğrulama yöntemi 3 sütunu |
| 7 | Mutlak yol / Windows yolu linklemek | Kırılgan link | `[[relative/path]]` (§4.4) |
| 8 | Frontmatter'siz dosya | Standart ihlali | 7 alan zorunlu (§4.2) |
| 9 | Mojibake (ş→¿) bırakma | Okunamaz | `vault-utf8-writer verify` → mojibake 0 |
| 10 | 500 altı satır | Derinlik standardı ihlali | §3.5 + §4.6 ile derinleştir |

---

## §4 Kurallar

> §4'ün **ilk maddesi** §3.3'teki Şablon-Önce bloğudur (üretilen dosyada birebir gömülüdür); aşağıda bloğun ardından bağlayıcı kurallar gelir.

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First) — Persona

*(§3.3 bloğu birebir buraya yapıştırılır — 5 madde + Durum/Aksiyon tablosu; silinemez.)*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Template Mandatory (Guardrail #16) | Persona bu şablondan üretilir | Dosya geçersiz, revert |
| 2 | Şablon Önce | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | SSOT | Bilgi vault'tan; persona kataloğu `personas/index` | İçerik silinir |
| 4 | Zero Hallucination (ADR-005) | Doğrulanamayan iddia §4.5'e göre etiketlenir | Etiketsiz iddia silinir |
| 5 | In-Place Refactoring | Dosya adı/yolu değişmez (tek istisna §4.8) | Dosya geri yüklenir |
| 6 | Dil | Türkçe; mojibake yasak (§4.3) | `repair` ile onarılır |
| 7 | REDACTED / KVKK | Gerçek kişi verisi, secret asla yazılmaz (§4.7) | Sızıntı sayılır |
| 8 | Append-only log | Değişiklikler `log.md`'ye eklenir | Geçmiş satır değişmez |
| 9 | UTF-8 yazım | `vault-utf8-writer` tek yazma arayüzü | Bozuk dosya onarılır |
| 10 | Domain boundary | Persona dosyası QA Engineer tarafından yazılır | Layer violation → revert |

### §4.2 Frontmatter — 7 Zorunlu Alan

| Alan | Zorunlu | Kural | Örnek |
|------|---------|-------|-------|
| `title` | ✅ | Tırnak içinde, `CoreMusic Persona — Ad Soyad` | `"CoreMusic Persona — Zeliha Demir"` |
| `type` | ✅ | Persona dosyasında `persona`; **şablonun kendisinde `template`** | `persona` |
| `category` | ✅ | Persona dosyasında `personas`; şablonda `personas` | `personas` |
| `version` | ✅ | Semver | `1.0.0` |
| `status` | ✅ | `active` / `draft` / `deprecated` | `active` |
| `authority` | ✅ | Persona/şablon `reference` (SSOT self-claim yasak) | `reference` |
| `updated` | ✅ | `YYYY-MM-DD` | `2026-09-26` |

### §4.3 Dil, Kod Adı ve Mojibake

| Kural | Detay |
|-------|-------|
| Ana dil | Türkçe — ç ğ ı İ ö ş ü doğru yazılır |
| Dosya/klasör adı | ASCII, küçük harf, tire (§4.8) — Türkçe karakter katlanır |
| Teknik terim | Gerektiği yerde İngilizce kalır (viewport, throttling, Lighthouse) |
| Mojibake yasak | `Ã-` ile başlayan diziler ve U+FFFD yer tutucuları `verify` ile tespit edilir |
| Yazım aracı | `node .ai/scripts/vault-utf8-writer.mjs` (append / write / insert-before-marker) |
| PowerShell yazım yasağı | `Set-Content`, `Out-File`, `Add-Content`, `echo >` kullanılmaz (Windows-1254 bozar) |
| Doğrulama | Yazım sonrası `verify --file` → `mojibake: 0`, `hasBom: false` |

### §4.4 Wiki-Link ve Bağlantı Formatı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| `[[../index]]` | `[index](../index.md)` |
| `[[.templates/index]]` | `[reg](C:\www\coremusic.net\.ai\.templates\index.md)` |
| `[[personas/mood-taxonomy]]` | `https://iç-sistem/personas/mood-taxonomy` |
| Harici URL düz metin | `https://...` (wiki-link yapılmaz) |

**Göreli yol kuralı:** bağlantı, dosyanın kendi dizininden göreli yazılır; hedef diskte yoksa §7.1 tablosunda `📋 planlanan` olarak işaretlenir ve `log.md`'ye kaydedilir.

### §4.5 Kaynak Etiketleme (KRİTİK — ADR-005)

**Her satırda / her alanda kaynak belirtilir. Etiketsiz iddia yazılamaz.**

| # | İddia Türü | Örnek | Zorunlu Kaynak | Etiket Formatı |
|---|-----------|-------|----------------|----------------|
| 1 | **Kurgusal persona verisi** | ad, doğum tarihi, burç, kan grubu, semt, okul adı, online rumuz, kurgusal aile | Kurgu olduğu açıkça belirtilir | `Kaynak: kurgusal (persona verisi)` |
| 2 | **Gerçek-dünya doğrulanabilir iddia** | nüfus/istatistik, cihaz teknik özellikleri, sanatçı-BPM, WCAG kriteri, KVKK/yaş sınırı, Big Five ölçeği (IPIP-NEO) | **EN AZ 2 bağımsız kaynak** | `Kaynak: [kaynak1] + [kaynak2]` |
| 3 | 2. kaynak bulunamazsa | tek güvenilir kaynak mevcut | 1 kaynak + gerekçe | `⚠️ VERIFICATION REQUIRED` + 1 kaynak |
| 4 | Hiçbir kaynak yok | — | İddia YAZILMAZ | Silinir / `log.md`'ye eksiklik kaydı |

| ✅ Doğru | ❌ Yanlış (Hallüsinasyon) |
|----------|---------------------------|
| `Kaynak: kurgusal (persona verisi)` | Kaynak satırı olmaması |
| `Kaynak: Samsung spec sayfası + GSMArena inceleme` | `Kaynak: internet` · `Kaynak: biliniyor` |
| `⚠️ VERIFICATION REQUIRED — 2. kaynak bulunamadı (1/2)` | Uydurma ikinci kaynak adı |
| `burç: Balık (kurgu)` | `burç: Balık` (kaynaksız gerçek iddia gibi sunum) |

**UYDURMA YOK (ADR-005):** Doğrulanamayan hiçbir sayısal/teknik iddia `Kaynak:` etiketsiz satırda duramaz.

### §4.6 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Persona şablonu (bu dosya) | ≥ 500 satır (ham ölçüm — `vault-utf8-writer verify` → `lines`) |
| Üretilen persona dosyası | ≥ 500 satır (docs-md §4.6 — istisnası YOKTUR) |
| Fiziksel Özet istisnası | §3.5.2 → 5-8 satır (bölüm içi standart; dosya derinliğini azaltmaz) |
| Kapsam dışı | `session-log-template.md` (144 satır — bu şablonla ilgisi yok) |
| İhlal | 500 altındaysa dosya tamamlanmış sayılmaz |

### §4.7 REDACTED, KVKK ve Sır Politikası

| Durum | Aksiyon |
|-------|---------|
| Gerçek kullanıcı verisi (ad, e-posta, telefon) | Persona dosyasına yazılmaz; kurguya çevrilir veya `[REDACTED]` |
| 18 yaş altı persona (KVKK) | Kurgusal olduğu ve test amaçlı olduğu belirtilir; gerçek çocuk verisi asla |
| API key, token, parola | Vault'a asla yazılmaz; `[REDACTED]` |
| `.env` içeriği | Örnek bile olsa yapıştırılmaz |
| Log'a sızan secret | `log.md` girişinde `[REDACTED]` |

### §4.8 Dosya Adı, In-Place ve Frozen ADR Koruması

| Kural | Uygulama |
|-------|----------|
| Dosya adı formatı | `kirik-ad-surname-mood.md` → ASCII, küçük harf, tire; örnek: `zeliha-demir-arabesk.md`, `mehmet-yilmaz-lofi.md` |
| Ad değişmez | In-Place istisnası dışında dosya adı DEĞİŞTİRİLMEZ (`-v2.md`, `(1).md` üretilmez) |
| Segment klasörü | `.ai/personas/<segment>/` — segment adı da ASCII/tire |
| Frozen ADR 001-037 | Okunur, referans edilir; değiştirilmez |
| Yeni ADR gerekirse | `ADR-088+` numarasıyla ayrı dosyada açılır |
| Silinmezlik | Eski içerik düzeltilir, `log.md`'ye kaydedilir |

---

## §5 Workflow

### §5.1 Persona Üretim Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Şablonu oku | `personas/persona-template.md` | 3 dk |
| 2 | `[[.templates/index]]` §4 + `[[personas/mood-taxonomy]]` + `[[personas/methodology]]` oku | Küme adı + test seviyeleri | 5 dk |
| 3 | Segment ve hedef akışı belirle; eski vault/persona örneğini salt-okunur incele | Kapsam kararı | 5 dk |
| 4 | Şablonu kopyala → `{{VARIABLE}}` alanlarını doldur | Taslak | 15 dk |
| 5 | §3.5 alan havuzunun 11/11 alt bölümünü doldur | Dolu §3 | 20 dk |
| 6 | §4.5'e göre **her satıra kaynak etiketi** yaz (kurgusal / 2 kaynak / VERIFICATION) | Kaynaklı dosya | 10 dk |
| 7 | Test Adımları tablosunu ≥10 satıra çıkar, 3 sütunu doldur | Test planı | 10 dk |
| 8 | §6.1 kontrol listesini çalıştır | İşaretli liste | 5 dk |
| 9 | `vault-utf8-writer verify --file` | UTF-8 + satır raporu (`lines ≥ 500`) | <1 dk |
| 10 | `log.md` append + `personas/index` + registry senkronu (gerekirse) | Audit trail | 3 dk |

```text
ŞABLONU OKU → KOPYALA → {{PLACEHOLDER}} DOLDUR → 11/11 ALANI DOLDUR → KAYNAK ETİKETLE → TEST ADIMLARI ≥10 → DOĞRULA → UTF-8 VERIFY → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Şablon okunmadan yazım | §6.1'de 3+ madde düşer | Şablondan yeniden üret |
| Eksik frontmatter | FM BAD | 7 alanı tamamla (§4.2) |
| Eksik alan havuzu | §6.1'de "11/11" düşer | §3.5'e dön, boş alanı `yok` diye işaretle |
| Kaynak etiketsiz satır | §6.1 "kaynak" düşer | §4.5 formatıyla etiketle; 2. kaynak yoksa `⚠️ VERIFICATION REQUIRED` |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| Kırık wiki-link | Hedef diskte yok | §7.1'de `📋 planlanan` işaretle + `log.md` |
| Test adımı <10 | §6.1 "test adımı" düşer | §3.5.10'dan tamamla |
| KVKK / gerçek veri tespiti | Gerçek kişi izi | DUR → §4.7 kurguya çevir veya `[REDACTED]` |

### §5.3 Bitiş Koşulları

- [ ] Dosya hedef yolda, adı `kirik-ad-surname-mood.md` biçiminde, `.md` uzantılı
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0, `lines ≥ 500`)
- [ ] `log.md` append edildi
- [ ] Wiki-link'ler geçerli ya da §7.1'de durum notu var

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (tek H1 + §1 Amaç · §2 Kapsam · §3 Mimari · §4 Kurallar · §5 Workflow · §6 Doğrulama · §7 Referanslar + Referanslar altı)
- [ ] Dosya derinliği ≥ 500 satır (`verify` → `lines`)
- [ ] **Her alanda/satırda kaynak etiketi var** (§4.5: `kurgusal` · `[k1] + [k2]` · `⚠️ VERIFICATION REQUIRED`)
- [ ] Big Five 5/5 boyut dolu, 0-100 aralığında, her birinde 1 satır gerekçe
- [ ] Test Adımları tablosu ≥ 10 satır ve 3 sütun (Adım / Beklenen / Doğrulama yöntemi)
- [ ] §3.5 alan havuzu 11/11 alt bölüm dolu (boş alan `yok` diye işaretli)
- [ ] AI Rol Kartı `Sen ...'sin` paragrafıyla başlıyor, `{{...}}` kalmamış
- [ ] Wiki-link'ler `[[relative/path]]` formatında; hedefi olmayanlar §7.1'de durum sütunuyla işaretli
- [ ] Mojibake yok (`verify` → mojibake 0, BOM false)
- [ ] `⚠️ VERIFICATION REQUIRED` varsa gerekçeli (neden, eksik olan hangi kaynak)
- [ ] §3.3 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] §7.2 Değişiklik Geçmişi append-only tablo var + Authority footer

### §6.2 Quality Report (Şablon Dosyasının Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Durum | Red Team · Human Mode · Truth Mode verified |
| Bölüm | 7 (H1 + §1-§7) + frontmatter |
| Zorunlu blok | 1 (§3.3 Şablon-Önce — persona versiyonu, silinemez) |
| Alan havuzu | 11 alt bölüm (§3.5.1-§3.5.11) |
| Guardrail kapsamı | #16 merkezli; #2, #3, #4, #5, #12 bağlayıcı |
| Anti-pattern | 10 (§3.6) |
| Frontmatter alan | 7 zorunlu |
| Kaynak kuralı | 4 senaryo (§4.5) — 2 bağımsız kaynak zorunlu |
| Test adımı asgari | 10 (§3.5.10) |
| Fiziksel özet standardı | 5-8 satır (§3.5.2) |
| Dil | Türkçe (mojibake yasak) |
| Derinlik | ≥ 500 satır (verify `lines`) |
| Kayıt | `log.md` append + registry senkron |

### §6.3 Örnek — Dolu İskelet (İlk 2 Bölüm)

````markdown
## §1 Amaç

Bu persona, CoreMusic'te **{{SEGMENT}}** segmentini temsil eder; Level 1 (AI rol) ve Level 2 (Browser MCP) testleri için kullanılır.
Hedef kitle: QA Engineer, UI Designer, test otomasyonu.
Vault bağlantısı: mood → `[[personas/mood-taxonomy]]`, metodoloji → `[[personas/methodology]]`, registry → `[[.templates/index]]`.

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 11 parçalı persona alan havuzu | Test başarı metrikleri (→ methodology) |
| Kaynak etiketleme (§4.5) | ADR kararı (→ adr-template) |
| ≥10 test adımı | Kod/otomasyon scripti (→ testing/*) |

*Alt konular:* kimlik → fiziksel özet → Big Five → mood → müzik DNA → cihaz → erişilebilirlik → davranış → AI rol kartı → test adımları → kaynaklar.
Kapsam dışı için: genel doküman → `[[.templates/documentation/docs-md-template]]`, karar → `[[../adr/adr-template]]`.
````

*(§3-§7 örnekleri kasıtlı kısaltılmıştır; üretimde §3.5 alan havuzu ve §4.5 kaynak kuralı tam doldurulur.)*

---

## §7 Referanslar

### 7.1 Wiki-link Referansları

| Wiki-link | İlişki | Durum / Gerçek hedef |
|-----------|--------|----------------------|
| `[[.templates/index]]` | Şablon registry'si — bu şablonun kayıt defteri | ✅ `.ai/.templates/index.md` |
| `[[.templates/documentation/docs-md-template]]` | Yapısal anahtar (8-bölüm, 7 alan, §3.3, §4.3-§4.6) | ✅ `.ai/.templates/documentation/docs-md-template.md` |
| `[[personas/index]]` | Persona kataloğu | 📋 planlanan — `.ai/personas/index.md` (bu şablonla üretilecek) |
| `[[personas/methodology]]` | Test seviyeleri (Level 1/2/3) ve akışı | 📋 planlanan — `.ai/personas/methodology.md` |
| `[[personas/mood-taxonomy]]` | Mood küme adları (§3.5.4 kaynağı) | 📋 planlanan — `.ai/personas/mood-taxonomy.md` |
| `[[testing/persona-test-protocol]]` | Teknik test protokolü | 📋 planlanan — `.ai/testing/persona-test-protocol.md` |
| `[[ADR-023-persona-driven-testing]]` | Persona-driven testing kararı (frozen) | ✅ `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` — göreli biçim: `[[../.decisions/accepted/ADR-023-persona-driven-testing]]` |
| `[[ADR-005-ultrathink-protocol]]` | Zero-hallucination protokolü (§4.5 dayanağı) | ✅ `.ai/.decisions/accepted/ADR-005-ultrathink-protocol.md` — göreli biçim: `[[../.decisions/accepted/ADR-005-ultrathink-protocol]]` |

*`📋 planlanan` satırlar hedef dosyalar diskte oluşana kadar kırık kabul edilir; oluştuğunda bu satır `✅`ye çevrilir ve `log.md`'ye append edilir.*

### 7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — registry'ye eklendi; §3.3 Şablon-Önce bloğu (persona) + §3.5 11 alanlı havuz + §4.5 kaynak etiketleme zorunlu | vault-updater |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-26
**Mode:** Red Team · Human Mode · Truth Mode
