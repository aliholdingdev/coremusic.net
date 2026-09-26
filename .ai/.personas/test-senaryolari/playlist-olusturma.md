---
title: "CoreMusic — Test Senaryosu: Playlist Oluşturma (PLY)"
type: test-scenario
category: testing
version: 1.0.0
status: active
authority: "Test Senaryosu — SSOT: personas/test-senaryolari/playlist-olusturma"
updated: 2026-09-26
---

# CoreMusic — Test Senaryosu: Playlist Oluşturma (PLY)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/methodology]] · [[personas/research-bank]] · [[personas/test-scenarios-mapping]] · [[personas/persona-template]] · [[ADR-023-persona-driven-testing]] · [[personas/test-senaryolari/a11y-erisilebilirlik]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `playlist-olusturma.md` |
| Dosya Yolu | `.ai/.personas/test-senaryolari/playlist-olusturma.md` |
| Dosya Tipi | Test senaryosu (6 senaryodan 1'i) — **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (birincil), UI Designer (liste/sürükleme UI), Vault Steward (denetim) |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| ADR-023 Karşılığı | 20 persona matrisi **satır 19 — Playlist kişisi** (§2.2a; kaynak: eski vault `test-senaryolari/playlist-olusturma.md`) |
| Mapping Karşılığı | S2 (parça seçimi) + S5 (kısıtlı ayarlar) yüzeyi; işbirlikçi paylaşım S6'ya kayar — `[[personas/test-scenarios-mapping]]` §4.2 |
| Test Seviyeleri | Seviye 1 AI Rol · Seviye 2 Browser MCP · Seviye 3 Playwright · Seviye 4 Rapor |
| Eşik Kaynağı | `[[personas/research-bank]]` **P8 (metrik)** + **P5 (WCAG — özellikle 2.5.7 sürükleme, 2.5.8 hedef boyutu, 3.3.7 yinelenen giriş)** |
| Adım Sayısı | **15** (asgari 10 — §5.3) |
| Test Blokları | PLY-001 … PLY-010 (10 blok — §3.1) |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Eski Kaynak | `coremusic.net.old/.ai/personas/test-senaryolari/playlist-olusturma.md` (519 satır, **salt okunur**) — iskelet alındı, P8/P5 ile yeniden bağlandı |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `Test Senaryosu — SSOT: personas/test-senaryolari/playlist-olusturma` |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |

---

## §1 Amaç

Bu dosya, CoreMusic'in **playlist oluşturma test senaryosudur**: CRUD işlemleri, parça ekleme/çıkarma, sıralama/shuffle, işbirlikli (collaborative) playlist, içe/dışa aktarma, akıllı playlist, kapak resmi ve grup bazlı playlist davranışlarının (çocuk — veli hesabına bağlı; genç — paylaşım izinleri; yetişkin — toplu yönetim) hangi adımlarla ve hangi `VERIFIED` eşiklerle test edileceğini tanımlar. Eski vault'taki 519 satırlık senaryo **kopyalanmamış**; iskelet alınıp research-bank P8/P5 ile **yeniden bağlanmıştır** (ADR-005 Zero Hallucination).

| Boyut | Değer |
|-------|-------|
| Ne taşır | PLY test blokları, ≥10 adımlık adım tablosu (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG), sürükleme alternatifi (2.5.7), işbirlikçilik izin matrisi, KVKK/veli adımı, Playwright kod bloğu |
| Ne taşımaz | Eşik kaynağı (→ research-bank), test akışı (→ methodology), grup matrisi (→ mapping), karar (→ ADR-023), izin modeli mimarisi (→ backend/security) |
| Neden yazıldı | Eski senaryo research-bank'tan önce yazılmıştı → sürükleme/hedef boyutu eşikleri P5 ile bağlandı |
| Kim okumalı | Playlist/CRUD testi çalıştıran her ajan (QA birincil) |
| Kanıt zinciri | `research-bank` P5/P8 → `methodology` → persona → **bu dosya** → `ADR-023` (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Playlist CRUD/sıralama/işbirlikçilik test adımları | ✅ Bu dosya | — |
| Sürükleme/hedef boyutu eşikleri | `[[personas/research-bank]]` P5.2 | ❌ (taşınır) |
| Metrik eşikleri (LCP/INP/CLS) | `[[personas/research-bank]]` P8.1 | ❌ (taşınır) |
| Test seviyeleri/akış | `[[personas/methodology]]` | ❌ |
| Grup kısıtları (S5 veli, S4 yaş kilidi) | `[[personas/test-scenarios-mapping]]` §4.4 | ❌ |
| Karar/gate | `[[ADR-023-persona-driven-testing]]` | ❌ |

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Playlist oluşturma/düzenleme/silme (CRUD) + onay geri bildirimi | Playlist veri modeli şeması (→ architecture/k5-veri-yonetimi) |
| Parça ekleme/çıkarma, sıralama (manuel + shuffle) | Ses oynatma motoru (→ k15-medya-streaming) |
| İşbirlikli playlist: davet, izin seviyeleri, eşzamanlı düzenleme | Davet e-postası altyapısı (→ backend) |
| İçe/dışa aktarma (CSV/JSON benzeri) | Dışa aktarma formatı kararı (`⚠️ VERIFICATION REQUIRED`) |
| Akıllı playlist (kural tanımlı) | Kural motoru performans eşiği (`⚠️ VERIFICATION REQUIRED`) |
| Kapak resmi yükleme (boyut/format hata durumları) | Görsel işleme servisi |
| Çocuk playlist: veli hesabına bağlı, 48px+ hedefler ⚠️ | Veli onayının hukuki yorumu (→ P6 `⚠️ DERIVED`) |
| 2.5.7 (sürükleme alternatifi), 2.5.8 (≥24×24 px), 3.3.7 (yinelenen giriş) doğrulaması | Derin a11y denetimi (→ a11y senaryosu) |

### §2.2 Hedef Kitle

| Persona | Bu senaryoda rolü |
|---------|-------------------|
| QA Engineer (`qa`) | Birincil yürütücü |
| UI Designer (`ui`) | Sürükleme/sıralama UI doğrulama |
| Security Engineer (`security`) | İzin/davet akışı denetimi (ikincil) |
| Vault Steward | Denetim — §6 envanter |

### §2.3 Kapsam Dışı İstisnalar

| # | İstisna | Nereye Gider |
|---|---------|--------------|
| 1 | İşbirlikli düzenleme **eşzamanlılık çakışması** (iki kullanıcı aynı satır) | Sunucu tarafı davranış — `⚠️ VERIFICATION REQUIRED` |
| 2 | Dışa aktarma formatı (CSV/JSON) kararı | `⚠️ VERIFICATION REQUIRED` |
| 3 | Akıllı playlist kural motoru eşiği (kaç kural, derinlik) | `⚠️ VERIFICATION REQUIRED` |

---

## §3 Mimari — Senaryo Yapısı

### §3.0 Dizin ve Bağımlılık Ağacı

```
.ai/.personas/test-senaryolari/playlist-olusturma.md   ← bu dosya (SSOT: senaryo)
├── [[personas/research-bank]] P5.2                    (2.5.7 · 2.5.8 · 3.3.7 — VERIFIED)
├── [[personas/research-bank]] P8.1–P8.5               (metrik — VERIFIED)
├── [[personas/research-bank]] P6.4                    (veli onayı — DERIVED)
├── [[personas/methodology]] §2.2, §3.3, §3.4
├── [[personas/test-scenarios-mapping]] §4.4-S5/S6     (grup beklentileri + ortak katman)
├── [[personas/mood-taxonomy]] §2                      (küme adları)
└── [[ADR-023-persona-driven-testing]] satır 19        (eşleme + coverage gate)
```

### §3.1 Test Blokları (PLY-001 → PLY-010)

| # | Blok | Kapsam | Birincil Kriter / Not |
|---|------|--------|----------------------|
| PLY-001 | Playlist CRUD | Oluşturma (boş + parçalı), yeniden adlandırma, silme + geri alma | 4.1.3 `⚠️` (durum mesajı numarası P5.5'te açılmadı) |
| PLY-002 | Parça Ekleme/Çıkarma | Ekleme bildirimi, tekrar ekleme, çoğaltma engeli | 3.3.7 · 1.4.3 |
| PLY-003 | Sıralama & Shuffle | Manuel sıralama + **klavye alternatifi**, shuffle durumu | **2.5.7** · 2.5.8 · 2.1.1 `⚠️` |
| PLY-004 | İşbirlikli Playlist | Davet, izin seviyesi, ortak düzenleme, ayrılma | 3.2.6 · S6 kısıtları `⚠️` |
| PLY-005 | İçe/Dışa Aktarma | Dışa aktarma, yeniden içe aktarma, bozuk dosya | `⚠️ VERIFICATION REQUIRED` (format) |
| PLY-006 | Akıllı Playlist | Kural tanımlama, otomatik güncelleme | `⚠️ VERIFICATION REQUIRED` (kural eşiği) |
| PLY-007 | Kapak Resmi | Yükleme, boyut/format hatası, kaldırma | 1.4.3 · 1.4.11 |
| PLY-008 | Çocuk Playlist | Velilere bağlı, paylaşım yok, 48px+ hedef ⚠️, veli onayı | 2.5.8 · KVKK `⚠️ DERIVED` |
| PLY-009 | Genç Playlist | Görünürlük modları, paylaşım izinleri, yaş kilidi ⚠️ | mapping §4.4-S5 |
| PLY-010 | Yetişkin Playlist | Toplu işlem, dışa aktarma, tüm cihazlardan senkron | 3.2.6 · 3.3.7 |

### §3.1.1 Blok Detayları (Açıklama — Başarı Kriteri)

**PLY-001 — CRUD:** Oluşturma boş veya parçalı olabilir; yeniden adlandırma anlık (yeniden yükleme yok); silme onay ister ve başarı geri bildirimi görünür. Başarı: işlem sonrası liste durumu kalıcı (yenilemede korunur).

**PLY-002 — Ekleme/Çıkarma:** Aynı parça iki kez eklenemez (veya çoğaltma engellenir); çıkarma geri alınabilir/geri al kipine sahip mesaj. Yinelenen giriş azaltma 3.3.7 kapsamında: playlist adı/form alanları ikinci kez **tam yeniden yazılmaz**.

**PLY-003 — Sıralama & Shuffle:** Sürükleme ile sıralama **ve** klavye/alternatif yöntem (ör. yukarı/aşağı taşımak) zorunlu (2.5.7 — sürükleme hareketi olmayan alternatif sunulmalı). Başarı: her iki yöntem de aynı sonucu verir; hedefler ≥ 24×24 px (2.5.8).

**PLY-004 — İşbirlikli:** Davet gönderilir; izin seviyesi (görüntüle/ekle/düzenle) görünür; davetsiz kullanıcı düzenleyemez. Not: mapping §4.4-S6'ya göre **yetişkinlerde bile mesaj ❌ / akış ❌** kısıtlı → işbirlikçilik yalnız playlist paylaşım katmanında, `⚠️ VERIFICATION REQUIRED` (izin modeli kapsamı).

**PLY-005 — İçe/Dışa Aktarma:** Dışa aktarma dosyası indirilir; bozuk dosya reddedilir ve hata net. Format (CSV/JSON) `⚠️ VERIFICATION REQUIRED` — research-bank'ta karar **YOK**.

**PLY-006 — Akıllı Playlist:** Kural tanımlanır; yeni parça eklendiğinde liste güncellenir. Kural sayısı/derinlik eşiği `⚠️ VERIFICATION REQUIRED`.

**PLY-007 — Kapak Resmi:** Yükleme başarılı; geçersiz format/boyut hatası açıklamalı (1.4.3 metin okunur); kaldırma işlevi var.

**PLY-008 — Çocuk:** Çocuk playlist'i veli hesabına bağlıdır (mapping §4.8: "S5 veli hesabına bağlı"); paylaşım/dışa aktarma **reddedilir ve red görünürdür**; butonlar 48px+ `⚠️ DERIVED`; **veli onayı adımı zorunlu** (§5.3/11) — P6.4 `⚠️ DERIVED`.

**PLY-009 — Genç:** Profil görünürlüğü/dinleme durumu/yaş gizleme 3 mod (mapping §4.4-S5); paylaşım izni boundary (mood-taxonomy §3.6 notu); yaş kilidi 16 ⚠️.

**PLY-010 — Yetişkin:** Toplu seçme/silme, dışa aktarma (GDPR dışa aktarma `⚠️ VERIFICATION REQUIRED` — mapping §4.4-S5), cihazlar arası senkron turu.

### §3.1.2 Edge Case'ler

| # | Edge Case | Beklenen |
|---|-----------|----------|
| 1 | Boş playlist'e shuffle | Nazik durum mesajı; hata yok |
| 2 | Son parça silinir | Liste boş kalır ama silinmez; boş-durum görünür |
| 3 | Davet kabul edilmeden düzenleme denenir | Red + açıklama (izin hatası okunur — 1.4.3) |
| 4 | Kopyala-yapıştır ile 50 parça ekleme | Partikül ekleme bildirimi tekilleşir; INP ≤ 200 ms |
| 5 | Kapak yükleme sırasında çıkış | Yükleme iptal edilir; bozuk parçalı durum kalmaz |
| 6 | Çocuk paylaşım butonunu bulamazsa | Buton **yok** veya görünür red; gizli zorba davranış yok |
| 7 | Playlist adı boş bırakılır | Zorunlu alan hatası; kaydetme sessizce başarısız olmaz |
| 8 | Silme onayında "vazgeç" seçilir | Liste değişmez; odak geri gelir (2.4.7/2.4.11) |

### §3.2 WCAG Kriter — Eşik Tablosu (research-bank P5 — `VERIFIED`)

| Kriter | Başlık | Eşik / Değişken | Durum |
|--------|--------|-----------------|-------|
| 1.4.3 | Contrast (Minimum) | Metin ≥ 4.5:1; büyük ≥ 3:1 | `VERIFIED` |
| 1.4.11 | Non-text Contrast | UI bileşenleri ≥ 3:1 | `VERIFIED` |
| 1.4.13 | Content on Hover or Focus | AA | `VERIFIED` |
| 2.4.7 | Focus Visible | AA | `VERIFIED` |
| 2.4.11 | Focus Not Obscured (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.7 | Dragging Movements | AA — WCAG 2.2 **YENİ** (alternatif zorunlu) | `VERIFIED` |
| 2.5.8 | Target Size (Minimum) | ≥ 24×24 CSS px (5 istisna) | `VERIFIED` |
| 3.2.6 | Consistent Help | A | `VERIFIED` |
| 3.3.7 | Redundant Entry | A | `VERIFIED` |
| 3.3.8 | Accessible Authentication (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |

### §3.3 Persona / Mood Eşlemesi (`[[personas/test-scenarios-mapping]]`)

| Grup (n) | Playlist beklentisi | Kısıt |
|---|---|---|
| Çocuk (29) | Avatar/tema kadar sade; paylaşım yok; veli hesabına bağlı | S4 ❌ · S6 ❌ · S5 ⚠️ veli |
| Genç (29) | Görünürlük modları, paylaşım izinleri, yaş gizleme | Yaş kilidi 16 ⚠️ |
| Yetişkin (10) | Toplu işlem, dışa aktarma, senkron | S6 kısıtlı: mesaj ❌ akış ❌ |

Mood çaprazı: Lider (çocuk) → "Sıralama/önceliklendirme kontrolleri, rol/izin akışı" (mood-taxonomy §3.3/12); Sosyal (5) → paylaşım akışı (ikincil S6); Sporcu (4) → workout playlist (S3/S4); Arabesk/Dans türevi → playlist türü tutarlılığı (S2→S3 köprüsü).

### §3.1.3 Playlist — Mood Kontrol Çaprazı

| Küme | Playlist beklenen durum | Doğrulama |
|---|---|---|
| Enerjik (13) | Workout/enerji listeleri; shuffle canlı | Adım 6 |
| Arabesksever (19) | Arabesk listeler korunur (küme ≠ tür kayması) | Adım 10 |
| Danssever (20) | Dans listeleri BPM sırası bozulmaz | Adım 6 |
| Lider (12) | Sıralama kontrolü + rol/izin görünür | Adım 3, 7 |
| Anne/Baba/Profesyonel (3) | Toplu işlem + dışa aktarma | Adım 8, 12 |

### §3.4 Cihaz / Breakpoint Matrisi (research-bank P8.5 — `VERIFIED`)

| # | Viewport | # | Viewport |
|---|----------|---|----------|
| 1 | 320 × 568 | 6 | 1280 × 720 |
| 2 | 375 × 667 | 7 | 1600 × 1200 |
| 3 | 375 × 812 | 8 | 1920 × 1080 |
| 4 | 414 × 896 | 9 | 2560 × 1440 |
| 5 | 768 × 1024 | — | — |

> Sürükleme 2.5.7 için **dokunmatik** viewport'lar (320–414) birincil; klavye alternatimi her viewport'ta.

### §3.5 Emülasyon Ayarlar (P8 — `VERIFIED`)

| Ayar | Değer | Kaynak |
|------|-------|--------|
| Ağ gecikmesi / down / up | **150 ms · 1.6 Mbps / 750 Kbps** | P8.3 |
| TBT | **> 50 ms** long task bloklar | P8.3 |
| Cihaz registry | `playwright.devices` (userAgent, screenSize, viewport, hasTouch, deviceScaleFactor, isMobile, colorScheme, locale, timezoneId) | P8.4 |
| Viewport override | `page.setViewportSize()` | P8.4 |
| Offline | `use: { offline: true }` | P8.4 |
| CDP ağ | `Network.emulateNetworkConditions` | P8.4 |

### §3.5.1 Persona-Spesifik Notlar (Grup Bazlı)

| Grup | Playlist davranışı | Doğrulanacak şey | Kaynak |
|---|---|---|---|
| Çocuk (Lider) | Yetki devri görünür mü? | Rol/izin akışı görünür; paylaşım red'i | mood-taxonomy §3.3/12 |
| Çocuk (Utangaç/Kaşif) | Sade akış, büyük hedef | 48px+ ⚠️; veli PIN yolculuğu | mapping §4.8 |
| Genç (Sosyal) | Paylaşım izin boundary | İzin modu görünür + reddi görünür | mood-taxonomy §3.6 |
| Genç (Moody/Romantik) | Görünürlük 3 modu | Mod geçişi anlık | mapping §4.4-S5 |
| Yetişkin (Anne/Baba/Profesyonel) | Toplu + dışa aktarma | Adım 8, 12 | mapping §4.4-S5 |

### §3.5.2 Ortak Katman Uyumu (mapping §4.6-S5 — taşınan)

Playlist yüzeyi S5 ortak katmanının parçasıdır; aşağıdaki maddeler bu senaryoda **playlist'e değen** başlıklar olarak test edilir:

| # | Ortak Katman Maddesi | Bu Senaryoda Karşılığı | Adım |
|---|----------------------|------------------------|------|
| 1 | Profil: görünen ad değiştirme | Liste sahibi görünen adı değişince başlık güncellenir mi? | 3 |
| 2 | Tema: anlık geçiş (yeniden yükleme yok) | Liste görünümü tema değişiminde kaymaz (CLS) | 2 |
| 3 | Oturum düşüşü | Düzenleme yarım kalır → net durum mesajı | 15 |
| 4 | Bildirim susturma | İşbirlikçi davet bildirimi susturulabilir mi? | 8 |
| 5 | Çocukta parola değişikliği ❌ | Çocuk hesabında playlist ≠ hesap ayarı; veli alanına yönlendirme | 11 |

> Bu tablo mapping'ten **taşınır**; orijinalde olmayan madde eklenmez (ADR-005).

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

Bu dosya `.templates/documentation/docs-md-template.md`'ye göre yazılmıştır; bu blok **silinemez**. Yeni PLY bloğu eklenirken önce şablon, sonra §4.x (Guardrail #16).

### §4.1 Bağlayıcı Kurallar

| # | Kural |
|---|-------|
| 1 | Eşikler research-bank'tan taşınır; uydurulmaz |
| 2 | `VERIFIED` ↓ `⚠️` olur; `⚠️` ↑ `VERIFIED` olmaz (ADR-005) |
| 3 | Frozen ADR'ler (001–037) değiştirilmez |
| 4 | Dosya adı değişikliği onay gerektirir |
| 5 | Her değişiklik `log.md` append-only |

### §4.2 Frontmatter — 7 Zorunlu Alan

`title` · `type` · `category` · `version` · `status` · `authority` · `updated`.

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

Yazım yalnız `vault-utf8-writer.mjs`; PowerShell yazma cmdlet'leri **YASAK**.

### §4.4 Wiki-Link ve Bağlantı Formatı

`[[relative/path/to/file]]` — zorunlu 7 bağlantı H1 altında (§7.1).

### §4.5 Kaynak Etiketleme — Durum Aktarımı (ADR-005)

`VERIFIED` taşınır + P# kaynağı; `⚠️ DERIVED` türetme olduğuyla; `⚠️ VERIFICATION REQUIRED` aynen — doğrulanmadan iddia edilmez.

### §4.6 Derinlik Standardı (500+)

≥ 500 satır; ekleme sonrası §6.1 tekrar çalıştırılır.

### §4.7 REDACTED, KVKK ve Sür Politikası

Gerçek kişi/anahtar verisi yazılmaz; **16 yaş altı veli onayı `⚠️ DERIVED`** (P6.4); "KVKK 16" gerekçesi yasak (P6.5); dosya adı değişmez.

### §4.8 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| Sürükleme zorunlu (alternatifsiz) | 2.5.7: alternatif sunulmalı |
| Hedef boyutu "iyi his" ile | 2.5.8: ≥ 24×24 px |
| Dışa aktarma formatını kesin sanmak | `⚠️ VERIFICATION REQUIRED` |
| PowerShell ile yazma | `vault-utf8-writer.mjs` |
| 500 satır altı | §4.6 |

---

## §5 Workflow

### §5.1 Genel Akış

```
PREPARE (§5.2) → EXECUTE (§5.3 + §5.4) → REPORT (§5.5)
```

### §5.2 PREPARE — Hazırlık Adımları

| # | Hazırlık | Çıktı |
|---|----------|-------|
| P1 | P8 eşikleri + P5.2 kriterleri oku | Eşik listesi |
| P2 | mapping §4.4-S5/S6 + §4.8 oku | Grup kısıtları |
| P3 | Test hesapları (çocuk/genç/yetişkin) | 3 hesap |
| P4 | Viewport + throttling seç | Koşu profili |
| P5 | İşbirlikçi izin modeli kapsamı notu (`⚠️`) | §6.5 satırı |

### §5.3 Adım Tablosu (15 adım — asgari 10)

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Throttling uygula (150 ms · 1.6/0.75 Mbps) | Ağ koşulu aktif | Network Conditions | 150 ms · 1.6/0.75 (`VERIFIED`) | — |
| 2 | Playlist sayfasını yükle (cold) | FCP/LCP kaydedilir | Performance panel | FCP ≤ 1 sn · LCP ≤ 2500 ms (`VERIFIED`) | — |
| 3 | Yeni playlist oluştur (ad + açıklama) | Oluştu + görünür geri bildirim | Liste DOM + bildirim | CLS ≤ 0.1 (`VERIFIED`) | 1.4.3 |
| 4 | Parça ekle (×5) + aynı parçayı tekrar dene | 5 eklendi; tekrar engellendi/uyarıldı | Sayım | INP ≤ 200 ms (`VERIFIED`) | 3.3.7 |
| 5 | Sıralama: sürükleme ile 2. sıra → 1. sıra | Sıra değişti | DOM sırası | — | **2.5.7** |
| 6 | Sıralama: **klavye alternatifi** ile geri al | Aynı sonuç (alternatif mevcut) | Klavye testi | — | **2.5.7** · 2.4.7 |
| 7 | Shuffle'a bas, 3 kez tekrarla | Sıra her seferinde değişir; durum görünür | Sıra karşılaştırma | — | 2.5.8 (hedef ≥ 24 px) |
| 8 | İşbirlikçi davet gönder + izin seviyesi seç | Davet görünür; izin seviyesi okunur | UI akışı | İzin kapsamı `⚠️ VERIFICATION REQUIRED` | 3.2.6 |
| 9 | Davetsiz 2. kullanıcıyla düzenleme dene | Red görünür + açıklama | Red ekranı | — | 1.4.3 |
| 10 | Dışa aktar → yeniden içe aktar | Liste birebir geri gelir | Karşılaştırma | Format `⚠️ VERIFICATION REQUIRED` | 3.3.7 |
| 11 | **KVKK/veli onayı (çocuk hesabı)** — paylaşım/dışa aktarma dene | Red görünür; veli onayı akışı | Ekran + veli onayı `⚠️` | Veli onayı `⚠️ DERIVED` (P6.4) | 2.5.8 (48px+ ⚠️) |
| 12 | Yetişkin: 20 parçalık toplu silme | Toplu işlem + geri alma | Bildirim | — | 3.3.7 |
| 13 | Kapak yükle (geçersiz format) | Hata mesajı açıklayıcı | Hata ekranı | — | 1.4.3 |
| 14 | 320×568 + 768×1024 tekrar (adım 5–7) | Sıralama her viewport'ta çalışır | 2 viewport | 2 breakpoint (`VERIFIED`) | 2.5.8 · **2.5.7** |
| 15 | Rapor (Seviye 4) | Geç/kal + etiket dökümü | §6.1 | P8.1 (`VERIFIED`) | §6.2 |

### §5.4 Playwright / CDP Kod Bloğu (P8 — `VERIFIED` API adlar)

```js
const { devices } = require('@playwright/test');

const context = await browser.newContext({ ...devices['Desktop Chrome'] });
const page = await context.newPage();

// Viewport override (P8.4) — dokunmatik sürükleme için
await page.setViewportSize({ width: 375, height: 667 });

// CDP ağ emülasyonu (P8.4)
const cdp = await context.newCDPSession(page);
await cdp.send('Network.emulateNetworkConditions', {
  offline: false,
  latency: 150,                              // P8.3
  downloadThroughput: 1.6 * 1024 * 1024 / 8, // 1.6 Mbps
  uploadThroughput: 750 * 1024 / 8,          // 750 Kbps
});

// ❌ YASAK: P8'de doğrulanmamış API'ler (dosya yükleme/dışa aktarma API'si
//    adları P8'de YOK) → kullanılırsa: // ⚠️ VERIFICATION REQUIRED
```

### §5.5 REPORT (Seviye 4)

| Alan | İçerik |
|------|--------|
| Ortam | Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED`, viewport, throttling |
| Metrik | FCP/LCP/INP/CLS vs P8.1 |
| Kriterler | 2.5.7 · 2.5.8 · 3.3.7 sonucu (§6.2) |
| Grup | Çocuk (11), genç (8–9), yetişkin (10–12) |
| KVKK | Veli onayı `⚠️ DERIVED` notu |
| Etiket dökümü | §6.5 |

### §5.6 Hata Modları

| # | Hata Modu | Tipik Neden | Müdahale |
|---|-----------|-------------|----------|
| 1 | Sürükleme yalnız destekli (alternatif yok) | UI eksik | UI — 2.5.7 ihlali |
| 2 | Küçük sıralama tutamaçları | Responsive eksik | UI — 2.5.8 ihlili |
| 3 | Davetsiz düzenleme geçti | İzin modeli hatası | **CRITICAL** → Security |
| 4 | Çocukta paylaşım sızıntısı | Red UI yok | **CRITICAL** → Security + KVKK |
| 5 | Yinelenen parça ekleme | Çoğaltma kontrolü yok | Backend — 3.3.7 |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] Frontmatter 7 alan
- [ ] Zorunlu 7 wiki-link
- [ ] §1–§7 + §4.0
- [ ] Adım tablosu ≥ 10 satır, 6 sütun (§5.3 → 15)
- [ ] KVKK/veli adımı mevcut (§5.3/11)
- [ ] 2.5.7 sürükleme alternatifi adımı mevcut (§5.3/6)
- [ ] Eşikler P5/P8/P6 etiketli
- [ ] ≥ 500 satır; mojibake 0; BOM yok
- [ ] log.md append-only giriş

### §6.2 WCAG Kriter Durum Tablosu (boş — test sonunda doldurulur)

| Kriter | Adım(lar) | Durum | Kanıt |
|--------|-----------|-------|-------|
| 1.4.3 | 3, 9, 13 | ⬜ | — |
| 1.4.11 | 7 (slider/ikon kontrastı) | ⬜ | — |
| 1.4.13 | 7 (hover ipucu varsa) | ⬜ | — |
| 2.4.7 | 6 | ⬜ | — |
| 2.4.11 | 6 | ⬜ | — |
| 2.5.7 | 5, 6, 14 | ⬜ | — |
| 2.5.8 | 7, 11, 14 | ⬜ | — |
| 3.2.6 | 8 (yardım konumu) | ⬜ | — |
| 3.3.7 | 4, 10, 12 | ⬜ | — |
| 3.3.8 | — (auth yok) | N/A | — |

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

Kapsam sayı iddiaları (41/55/46) kullanılmaz; `EXCLUDED` kayıtlar atlanır.

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Bölüm | §1–§7 |
| Blok | PLY-001 … PLY-010 (10) |
| Adım | 15 (§5.3) |
| Status | Red Team · Human Mode · Truth Mode |

### §6.5 Bu Dosyadaki ⚠️ Etiketleri (Toplam)

**⚠️ VERIFICATION REQUIRED — 12 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | İşbirlikçi izin modeli kapsamı | §2.3 |
| 2 | Eşzamanlı düzenleme çakışması davranışı | §2.3 |
| 3 | Dışa aktarma formatı (CSV/JSON) | §2.3 |
| 4 | Akıllı playlist kural eşiği | §2.3 |
| 5 | GDPR/veri dışa aktarma iddiası | mapping §4.4-S5 |
| 6 | Yaş kilidi 16 | mapping §4.8 |
| 7 | 4.1.3 durum mesajı numarası P5.5'te tek tek açılmadı | P5.5 |
| 8 | Tarayıcı sürüm envanteri | P8.7 |
| 9 | 41/55/46 sayıları | mapping §5.3 |
| 10 | Oturum zaman aşımı değeri (30 dk iddiası) | mapping §4.6 |
| 11 | Retry-After 60 sn (auth bağlı akış) | mapping §4.6 |
| 12 | RFC 5322 e-posta doğrulama biçimi | mapping §4.6 |

**⚠️ DERIVED — 3 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | 16 yaş altı → veli onayı | P6.4 |
| 2 | Çocuk 48px+ buton hedefi | mapping §4.4/§4.8 |
| 3 | Persona'ya özel metrik hedefi | P8.7 |

---

## §6.6 Test Çalıştırma Kontrol Listesi & Test Koşusu Şablonu

### §6.6.1 Çalıştırma Öncesi (Run Gate)

| # | Kontrol | Kaynak |
|---|---------|--------|
| 1 | P5.2 (2.5.7/2.5.8/3.3.7) + P8 eşikleri okundu mu? | P5/P8 |
| 2 | 3 test hesabı (çocuk/genç/yetişkin) hazır mı? | mapping §4.3 |
| 3 | Viewport seti (özellikle dokunmatik) seçildi mi? | P8.5 |
| 4 | Veli onayı akışı (`⚠️ DERIVED`) notu hazır mı? | P6.4 |
| 5 | §4.0 blok korunuyor mu? | Guardrail #16 |

### §6.6.2 Test Koşusu — [TARIH]

**Ortam:** Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED` · throttling 150 ms/1.6/0.75 · viewport §3.4 · Seviye 1–4.

**Metrik Sonuçları:** FCP ≤ 1 sn · LCP ≤ 2500 ms · INP ≤ 200 ms · CLS ≤ 0.1 · TBT ≤ 50 ms → ölçüm/geç-kal.

**Kriter Sonuçları:** 2.5.7 (adım 6) · 2.5.8 (7/11/14) · 3.3.7 (4/10/12) → PASS/FAIL + kanıt.

**Sonuç:** `[GEÇ / KAL]` — neden + eskalasyon (§5.6).

---

## §6.7 Ek Doğrulama Dayanakları (research-bank P8 — `VERIFIED`)

- **FCP iyi eşiği 1 saniye** — Lighthouse v8 score eğrisi bu eşiğe hizalıdır; §6.6.2'deki "FCP ≤ 1 sn" hedefinin dayanağıdır. `Kaynak: [k1] + [k2]` (web.dev — "existing FCP good threshold is 1 second" + github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md — research-bank P8.6 `VERIFIED`)
- **Lighthouse v8 performans skor ağırlıkları:** LCP 25 · TBT 30 · CLS 15 · FCP 10 · Speed Index 10 · TTI 10 — skor yorumu sürüm duyarlıdır (v8 baz alınır). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/v8-perf-faq.md + googlechrome.github.io/lighthouse/scorecalc — research-bank P8.6 `VERIFIED`)
- **Ağ throttling preset adı:** "Slow 4G" (eski adı "Fast 3G") ≈ %85 persentil mobil bağlantı, paket kaybı yok; DevTools preset'leri = Slow 3G · Fast 3G · Slow 4G · Fast 4G (+ özel profil). `Kaynak: [k1] + [k2]` (github.com/GoogleChrome/lighthouse docs/throttling.md + developer.chrome.com/docs/devtools/network/reference — research-bank P8.6 `VERIFIED`)
- **Eşiklerin yorumu:** LCP/INP/CLS/FCP eşikleri 75. yüzdelik alanına göredir; persona'ya özel metrik hedefi bu dosyaya yazılmaz (kapsam sınırı: §6.5 "Persona'ya özel metrik hedefi" kaydı). `Kaynak: [k1] + [k2]` (web.dev/articles/vitals + web.dev/articles/defining-core-web-vitals-thresholds — research-bank P8.1/P8.6 `VERIFIED`)

## §7 Referanslar

### §7.1 Wiki-link Referanslar

| Bağlantı | Rol |
|----------|-----|
| [[personas/index]] | Persona ana indeksi |
| [[personas/methodology]] | Test metodolojisi |
| [[personas/research-bank]] | P5/P6/P8 eşik kaynağı |
| [[personas/test-scenarios-mapping]] | Grup × senaryo matrisi |
| [[personas/persona-template]] | Persona alan şablonu |
| [[ADR-023-persona-driven-testing]] | Karar + coverage gate |
| [[personas/test-senaryolari/a11y-erisilebilirlik]] | Kardeş senaryo (a11y derinliği) |

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yapan |
|-------|-------|-----------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — eski iskelet (519 satır) P5/P8 ile yeniden bağlandı; 15 adım, 10 blok, 2.5.7 alternatif + KVKK adımı eklendi | Vault Documentation Specialist |
