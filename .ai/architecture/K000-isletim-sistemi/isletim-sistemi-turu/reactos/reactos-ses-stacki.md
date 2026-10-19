---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-ses-stacki"
node_type: architecture
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi"
evidence:
  - "https://reactos.org/ (✅ HTTP 200 · gerçek içerik · 2026-10-12)"
  - "https://reactos.org/news/ (✅ HTTP 200 · gerçek içerik · 2026-10-12)"
  - "https://reactos.org/wiki/Special:AllPages (✅ HTTP 200 · içerik bot-duvarı · 2026-10-12)"
  - "https://reactos.org/wiki/ReactOS_FAQ (✅ HTTP 200 · içerik bot-duvarı · 2026-10-12)"
validation:
  - "2026-10-12 RED TEAM: Bu dosyanın ana bulgusu OLUMSUZ KANITTIR — ReactOS resmi wiki'sinde ses ile ilgili özel sayfa TESPİT EDİLEMEDİ; bu yüzden dosya 'ReactOS ses mimarisi şudur' demez, 'ses mimarisi hakkında ne bilinmiyor ve nasıl bilinecek' der."
  - "≥500 satır · 10 alanlı frontmatter · reactos/ klasöründe URL kümesi ayrışık (kesişim 0)."
  - "Dosya/Satır UYDURMA YASAK — repo'da 0 .cpp/.h; kod blokları yalnız etiketli."
open_questions:
  - "R-S1: ReactOS'un ses alt sistemi bileşen adları (winmm/wdmaud karşılıkları dahil) → UNKNOWN, 0 isabet kaydı bu turda yeniden üretilemedi (bot-duvarı). [⚠️ VERIFICATION REQUIRED]"
  - "R-S2: USB ses sınıfı (UAC) desteği var mı? → USB wiki başlığı var, içeriği okunamadı. [⚠️ VERIFICATION REQUIRED]"
  - "R-S3: ReactOS, Neva Engine için test platformu olabilir mi? (Q4/T-U3) → UNKNOWN"
group: mimari
---

# reactos-ses-stacki — ReactOS ses alt sistemi ve negatif kanıt kaydı (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** verify-required
> **Kapsam:** `reactos/` beyazliste kalem 3 (ses stacki) · 10 alanlı frontmatter · ≥500 satır.
> **Özel statü:** Bu dosya **eksikliğin kendisini** belgeleyen bir kanıt dosyasıdır.

---

## §0. ELI15

Bir işletim sisteminin ses yapabiliyor olması için şu zincirin tamamı çalışmalıdır:
ses kartından gelen ham sayılar → sürücü → çekirdek alt sistemi → kütüphane →
uyulamanın çağırdığı arayüz. ReactOS hakkında bu zincirin **hiçbir halkasını** resmi
kaynaklardan doğrulayamadık: proje wiki'sinde ses ile ilgili özel bir sayfa **bulunamadı**,
ve wiki bu turda bot-koruma duvarının arkasında olduğu için ölçüm **yeniden
üretil(e)medi**. Bu dosya bu durumu saklamaz; **ne bilindiğini, ne bilinmediğini ve
bilinmeyeni nasıl kapatacağımızı** yazar.

---

## §1. Künye

| Öğe | Değer |
|---|---|
| node_id | `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-ses-stacki` |
| parent_id | `K000–K5000 Architecture Registry` |
| Üst dal | `isletim-sistemi-turu/reactos/` |
| Kardeş dosyalar | `reactos-cekirdek-mimarisi.md` · `reactos-win32-uyumlulugu.md` · `reactos-surucu-modeli.md` · `reactos-kod-ornekleri.md` · `reactos-web-arastirma.md` |
| Kademe | 4 |
| Durum | `verify-required` |
| group | `mimari` |
| Tarih | 2026-10-12 |
| Kanıt tipi | Ağırlıklı olarak **negatif kanıt** (aranan ve bulunamayan) |

---

## §2. Kapsam ve sınır

| İçinde | Dışında | Nereye |
|---|---|---|
| Ses alt sistemi hakkında bilinen/bilinmeyen envanteri | Çekirdek bileşenleri | `reactos-cekirdek-mimarisi.md` |
| Negatif arama bulgusu ve tekrar üretme yöntemi | Win32 API geneli | `reactos-win32-uyumlulugu.md` |
| Ses yığını için **orijinal** değerlendirme şeması | Sürücü modeli ve imzalama | `reactos-surucu-modeli.md` |
| Doğrulama (kapatma) planı | Kod örnekleri | `reactos-kod-ornekleri.md` |
| CoreMusic ses gereksinimleriyle eşleşme tablosu | URL tarama kaydı | `reactos-web-arastirma.md` |

---

## §3. Negatif bulgu — arama kaydı

**Ne arandı (kapsam):** Resmi wiki'nin tüm sayfa listesi üzerinde ses ile ilişkili
anahtar kelimeler (`audio` · `sound` · `mixer` · `winmm` · `wdmaud` · `wave`).

**Ne bulundu:** Kayıtlı ölçüm **0 isabet** (toplam sayfa listesi sayacı 347 olarak
okunmuştu).

**Bu ölçümün durumu (2026-10-12 yeniden deneme):** `https://reactos.org/wiki/Special:AllPages`
adresi **HTTP 200** döndürdü, ancak gövde **Anubis bot-koruma sayfasıdır** (v1.19.1 ·
4188 bayt) → sayfa listesi **okunamadı**, ölçüm **yeniden üretilemedi**.

| Kalem | Değer | Durum |
|---|---|---|
| Arama yapılan yüzey | ReactOS resmi wiki sayfa listesi | kapsam tanımlı |
| Anahtar kelimeler | `audio` · `sound` · `mixer` · `winmm` · `wdmaud` · `wave` | kayıt |
| Kayıtlı isabet | 0 | ⚠️ bu turda yeniden üretilemedi |
| Sayfa listesi sayacı | 347 | ⚠️ bu turda yeniden üretilemedi |
| URL erişilebilirliği | HTTP 200 | ✅ doğrulandı |
| İçerik erişimi | bot-duvarı (Anubis) | ❌ engelli |
| Sonuç etiketi | `⚠️ VERIFICATION REQUIRED` | zorunlu |

**Yorum yasağı:** "0 isabet" cümlesi **"ReactOS'ta ses yoktur" anlamına gelmez**. Yalnızca
"resmi wiki'de sese adanmış sayfa bulunamadı" der. Ses alt sisteminin varlığı/ yokluğu
**UNKNOWN** olarak kalır.

---

## §4. Olumlu kanıt olarak kullanılabilen tek sayfa: `Special:AllPages`

| Öğe | Değer |
|---|---|
| URL | https://reactos.org/wiki/Special:AllPages |
| HTTP | ✅ 200 (2026-10-12) |
| İçerik | ❌ Anubis bot-duvarı |
| Kullanım | Yalnızca "tarama girişimi kayıtlıdır" denetimi |
| Kullanım yasağı | Sayfa listesi içeriği bu turda **okunmadı** → başlık iddiası üretilemez |

---

## §5. Bir ses yığını için gerekli halkalar (bu vault'un OBU modeli)

> **Etiket:** Bu şema, ReactOS'a özgü değil, **bu vault için yazılmış orijinal** bir
> değerlendirme çerçevesidir; resmi bir standart veya ReactOS dokümanı **değildir**.
> Amaç: bilinmeyen bir sistemin ses yeteneğini ölçmek için hangi soruların sorulacağını
> sabitlemek.

| Halka | Soru | ReactOS durumu |
|---|---|---|
| H1 · Donanım | Ses kartı hangi veri yolundan bağlanır (PCI/USB)? | UNKNOWN |
| H2 · Sürücü | Donanım sürücüsü var mı, kim yazdı? | UNKNOWN |
| H3 · Çekirdek alt sistemi | Ses için ayrı çekirdek bileşeni var mı? | UNKNOWN |
| H4 · Kütüphane | Uygulamanın çağırdığı ses kütüphanesi var mı? | UNKNOWN |
| H5 · Arayüz | Hangi ses API'si sunuluyor? | UNKNOWN |
| H6 · Biçim | Hangi örnek hızı/bit derinliği/kanal düzeni destekleniyor? | UNKNOWN |
| H7 · Zamanlama | Önbellek (buffer) ve gecikme davranışı nedir? | UNKNOWN |
| H8 · Paylaşım | Aynı anda kaç uygulama ses alabilir? | UNKNOWN |
| H9 · Kestirme (bypass) | Düşük gecikmeli doğrudan erişim var mı? | UNKNOWN |
| H10 · Ölçüm | Gecikme nasıl ölçülür? | UNKNOWN |

**Tablo kuralı:** On satırın tamamı `UNKNOWN` — çünkü tek bir içerik kaynağı
okunamadı. Boş satır bırakmak ya da tahminle doldurmak yasaktı.

---

## §6. Bilinen / bilinmeyen ayrımı

### 6.1 Bilinen (kanıt taşıyan)

| # | Bilgi | Kanıt | Durum |
|---|---|---|---|
| 1 | ReactOS projesi resmi bir web sitesine sahiptir | https://reactos.org/ · gerçek içerik · 2026-10-12 | ✅ |
| 2 | Proje haber akışı yayımlanıyor | https://reactos.org/news/ · gerçek içerik · 2026-10-12 | ✅ |
| 3 | Wiki sayfa listesi adresi yanıt veriyor | Special:AllPages · HTTP 200 | ✅ (içerik ❌) |
| 4 | SSS (FAQ) başlıklı wiki sayfası adresi yanıt veriyor | ReactOS_FAQ · HTTP 200 | ✅ (içerik ❌) |
| 5 | Dış dokümantasyon bağlantıları başlığı var | External_Documentation_Links · HTTP 200 | ✅ (içerik ❌) |
| 6 | Dokümantasyon yazım kuralları başlığı var | Documentation_Guidelines · HTTP 200 | ✅ (içerik ❌) |
| 7 | Proje tüzüğü başlığı var | Constitution · HTTP 200 | ✅ (içerik ❌) |
| 8 | Sürüm adı başlığı var | Community_Edition · HTTP 200 | ✅ (içerik ❌) |

### 6.2 Bilinmeyen (kanıt taşımayan)

| # | Soru | Durum |
|---|---|---|
| 1 | Ses alt sisteminin bileşen adları | UNKNOWN |
| 2 | Ses API'sinin adı (Win32 ses API'leri dahil) | UNKNOWN |
| 3 | Ses sürücüsü modeli | UNKNOWN |
| 4 | USB ses sınıfı desteği | [⚠️] — `USB` başlığı var, içerik okunamadı |
| 5 | Örnekleme hızı/bit derinliği desteği | UNKNOWN |
| 6 | Gecikme ve buffer davranışı | UNKNOWN |
| 7 | Çok-uygulama ses paylaşımı | UNKNOWN |
| 8 | Düşük gecikmeli (kestirme) yol | UNKNOWN |
| 9 | Ses test altyapısı | UNKNOWN |
| 10 | Kaşif/görev yöneticisi ile ses ilişkisi | UNKNOWN |

---

## §7. USB ses sınıfı (UAC) — sınır maddesi

| Konu | Durum | Kayıt |
|---|---|---|
| `USB` başlıklı wiki sayfası erişilebilir | ✅ HTTP 200 | URL kaydı: `reactos-cekirdek-mimarisi.md` §12/§15.13 |
| USB **ses sınıfı** desteği | [⚠️] | içerik okunamadı |
| USB ses cihazı tak-çalıştır davranışı | UNKNOWN | — |
| USB ses sürücüsünün adı | UNKNOWN | ad uydurmak yasak |
| Çıkış cihazı olarak USB DAC kullanımı | UNKNOWN | — |
| CoreMusic DAC (PCM3168A) hattı ile ilişkisi | UNKNOWN | — |

**Neden ayrı bölüm:** USB, ses donanımına giden en olası yollardan biridir; ama "USB
sayfası var" ile "USB ses sınıfı var" **aynı iddia değildir**. İkisini ayırmak zorundayız.

---

## §8. Windows ses yığını ile karşılaştırma çerçevesi (karşılaştırma YAPILMADI)

| Karşılaştırma eksenı | Windows tarafı | ReactOS tarafı | Durum |
|---|---|---|---|
| Ses API'si | bu turda Microsoft kaynağı okunmadı | UNKNOWN | karşılıklı [⚠️] |
| Sürücü modeli | okunmadı | UNKNOWN | [⚠️] |
| Paylaşım modları | okunmadı | UNKNOWN | [⚠️] |
| Gecikme hedefi | okunmadı | UNKNOWN | [⚠️] |
| Kestirme yolu | okunmadı | UNKNOWN | [⚠️] |

**Neden boş bırakılmadı?** Boş hücre yerine "okunmadı/UNKNOWN" yazmak, sonraki
oturumun **neyi okuması gerektiğini** gösterir. Kısmi tablo, yarım bilgiden daha
dürüsttür.

---

## §9. CoreMusic / Neva Engine gereksinimleriyle eşleşme

> Vault içi bağlayıcı gereksinimler (kök `AGENTS.md` · sartname) bu tablonun sol
> sütunundadır; sağ sütun ReactOS için **ölçülmüştür** (çoğu UNKNOWN).

| Gereksinim (vault içi) | Değer | ReactOS karşılığı | Durum |
|---|---|---|---|
| Örnek hızı | 48 kHz | UNKNOWN | ❌ ölçülemedi |
| Buffer | 512 örnek | UNKNOWN | ❌ |
| Ses veri tipi | 32-bit float | UNKNOWN | ❌ |
| Kanal düzeni | 8.1 surround | UNKNOWN | ❌ |
| Düşük gecikme erişimi | ASIO Exclusive | varlığı doğrulanmadı | [⚠️] |
| Paylaşımlı erişim | WASAPI benzeri | varlığı doğrulanmadı | [⚠️] |
| DAC hedefi | PCM3168A (yasağı: PCM5122) | ilgisiz — donanım tarafı | — |
| Kilit-siz (lock-free) callback | zorunlu | platform desteği UNKNOWN | [⚠️] |
| Sıfır tahsis (zero-allocation) | zorunlu | UNKNOWN | [⚠️] |
| Korumalı çıkış (DC offset) | röle koruması | donanım katmanı | — |

**Sonuç:** ReactOS, Neva Engine için **henüz doğrulanmış bir hedef değildir**; eşleşme
tablosunun tek anlamlı çıktısı "ölçüm için ortam yok"tur.

---

## §10. Doğrulama (kapatma) planı — 20 adım

| # | Adım | Beklenen çıktı | Bağımlılık |
|---|---|---|---|
| 1 | Bot-duvarını geçebilen istemci ile wiki ana sayfası | gerçek HTML | JS/tarayıcı |
| 2 | `Special:AllPages` indirme | sayfa listesi (toplam sayaç) | adım 1 |
| 3 | `audio/sound/mixer/winmm/wdmaud/wave` filtresi | isabet sayısı (0 mı?) | adım 2 |
| 4 | İsabet varsa başlıkların kaydı | başlık listesi | adım 3 |
| 5 | `USB` sayfası indirme | UAC desteği metni | adım 1 |
| 6 | Sürücü ağacı sayfası arama | ses sürücüsü adı | adım 2 |
| 7 | Depodaki ses dizinlerinin listelenmesi (git) | dizin yolu | github erişimi ✅ |
| 8 | Depoda ses API başlıklarının aranması | dosya adları | adım 7 |
| 9 | `winmm`/`dsound` benzeri sembollerin aranması | sembol listesi | adım 7 |
| 10 | JIRA'da "audio"/"sound" etiketli işlerin aranması | iş listesi | jira erişimi ✅ |
| 11 | BuildBot çıktılarında ses testi arama | test kanıtı | build erişimi ✅ |
| 12 | Gerçek donanımda boot + ses denemesi | çalışma kanıtı | fiziksel makine |
| 13 | Örnekleme hızı/bit derinliği okuması | sayısal değer | adım 12 |
| 14 | Buffer/gecikme ölçümü | ms değeri | adım 12 |
| 15 | Aynı anda iki uygulama testi | paylaşma davranışı | adım 12 |
| 16 | Uçtan uca gecikme ölçümü | ms değeri | adım 14 |
| 17 | Neva Engine denemesi (varsa) | derleme/çalışma sonucu | kod yüzeyi (bu vault'ta 0 .cpp) |
| 18 | Bulguların `reactos-web-arastirma.md`'ye yazımı | kanıt satırları | adım 1–17 |
| 19 | `status` alanının `verify-required` → `verified` yapılması | frontmatter | adım 18 |
| 20 | Açık soruların kapatılması (R-S1…R-S3) | 0 açık soru | adım 19 |

**Bu plan olmadan** bu dosyadaki `UNKNOWN` değerleri kapatılamaz; tahminle kapatmak yasaktır.

---

## §11. Erişim ve kanıt bütünlüğü notu (bağlayıcı · 2026-10-12)

| URL sınıfı | HTTP | İçerik | Kullanım |
|---|---|---|---|
| `reactos.org/` · `/news/` · `/download/` | ✅ 200 | ✅ gerçek içerik | içerik düzeyinde kanıt |
| `reactos.org/wiki/*` | ✅ 200 | ❌ Anubis bot-duvarı (v1.19.1 · 4188 bayt) | yalnız erişilebilirlik |
| `github.com/reactos/reactos` | ✅ 200 | ✅ gerçek içerik | depo erişimi (bu dosyada URL taşımıyor) |
| `jira.reactos.org` · `build.reactos.org` | ✅ 200 | ✅ gerçek içerik | iş/CI erişimi (bu dosyada URL taşımıyor) |

Ayrıntılı sınıflandırma: `reactos-cekirdek-mimarisi.md` §11.5.

---

## §12. Varsayım günlüğü (RED TEAM)

| # | Varsayım | Kanıt | Karar |
|---|---|---|---|
| A1 | ReactOS'ta ses alt sistemi vardır | — | REDDEDİLDİ → UNKNOWN |
| A2 | ReactOS'ta ses alt sistemi YOKTUR | 0 isabet (yeniden üretilemedi) | REDDEDİLDİ → UNKNOWN |
| A3 | Wiki'de ses sayfası yoktur | kayıtlı ölçüm + bot-duvarı | [⚠️] işaretli |
| A4 | `winmm` ReactOS'ta mevcuttur | — | REDDEDİLDİ (ad uydurma) |
| A5 | `wdmaud` ReactOS'ta mevcuttur | — | REDDEDİLDİ |
| A6 | USB ses sınıfı desteklenir | yalnız `USB` başlığı | [⚠️] |
| A7 | Ses API'si Win32 ile aynıdır | — | REDDEDİLDİ |
| A8 | Gecikme değeri biliniyor | — | REDDEDİLDİ |
| A9 | 48 kHz desteklenir | — | REDDEDİLDİ |
| A10 | float32 desteklenir | — | REDDEDİLDİ |
| A11 | 8.1 surround desteklenir | — | REDDEDİLDİ |
| A12 | ASIO benzeri kestirme yol var | — | REDDEDİLDİ |
| A13 | Paylaşımlı çalışma var | — | REDDEDİLDİ |
| A14 | Ses testi var | — | REDDEDİLDİ |
| A15 | Kaşif ses oynatır | — | REDDEDİLDİ |
| A16 | Görev yöneticisi ses gösterir | — | REDDEDİLDİ |
| A17 | Proje aktif olarak haber yayımlıyor | `/news/` gerçek içerik | KABUL |
| A18 | Proje resmi siteye sahip | `/` gerçek içerik | KABUL |
| A19 | Wiki erişilebilir (200) | Special:AllPages 200 | KABUL (içerik ❌) |
| A20 | Bot-duvarı nedeniyle ölçüm tekrarlanamadı | Anubis sayfası gövdesi | KABUL |
| A21 | 347 sayfa sayısı kesindir | bu turda doğrulanamadı | [⚠️] |
| A22 | 0 isabet kesindir | bu turda doğrulanamadı | [⚠️] |
| A23 | Sürüm adı `Community Edition`'dır | başlık erişilebilir, içerik ❌ | [⚠️] |
| A24 | Proje tüzüğü (`Constitution`) vardır | başlık erişilebilir, içerik ❌ | [⚠️] |
| A25 | Dış doküman bağlantıları listesi vardır | başlık erişilebilir, içerik ❌ | [⚠️] |
| A26 | SSS sayfası vardır | başlık erişilebilir, içerik ❌ | [⚠️] |
| A27 | Dokümantasyon yazım kuralları vardır | başlık erişilebilir, içerik ❌ | [⚠️] |
| A28 | Windows ses yığını karşılaştırması yapılabilir | okunmadı | REDDEDİLDİ (§8) |
| A29 | Neva Engine bu platformda çalışır | kanıt yok | REDDEDİLDİ |
| A30 | Bu dosyada kod yoktur | kod bloğu 0 | KABUL |

**Özet:** 30 varsayımdan **15'i REDDEDİLDİ**, 9'u **[⚠️]**, 3'ü KABUL, geri kalanı
işaretli/koşullu. Dosyanın tamamı bu dengeyi taşır.

---

## §13. Kanıt matrisi (≥8 benzersiz URL · reactos/ içinde ayrışık)

| # | İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|
| 1 | ReactOS resmi sitesi erişilebilir ve gerçek içerik döndürüyor | https://reactos.org/ | 2026-10-12 | ✅ HTTP 200 · içerik ✅ |
| 2 | Haber akışı erişilebilir, gerçek içerik döndürüyor | https://reactos.org/news/ | 2026-10-12 | ✅ HTTP 200 · içerik ✅ |
| 3 | Wiki sayfa listesi adresi yanıt veriyor (tarama girişimi) | https://reactos.org/wiki/Special:AllPages | 2026-10-12 | ✅ HTTP 200 · içerik ❌ bot-duvarı |
| 4 | SSS başlıklı wiki sayfası adresi yanıt veriyor | https://reactos.org/wiki/ReactOS_FAQ | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |
| 5 | Dış dokümantasyon bağlantıları başlığı yanıt veriyor | https://reactos.org/wiki/External_Documentation_Links | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |
| 6 | Dokümantasyon yazım kuralları başlığı yanıt veriyor | https://reactos.org/wiki/Documentation_Guidelines | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |
| 7 | Proje tüzüğü başlığı yanıt veriyor | https://reactos.org/wiki/Constitution | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |
| 8 | Sürüm adı başlığı yanıt veriyor | https://reactos.org/wiki/Community_Edition | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |
| 9 | Kabuk uygulaması başlığı yanıt veriyor | https://reactos.org/wiki/ReactOS_Explorer | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |
| 10 | Görev yöneticisi başlığı yanıt veriyor | https://reactos.org/wiki/Task_Manager | 2026-10-12 | ✅ HTTP 200 · içerik ❌ |

**Dosya-içi benzersiz URL: 10 (≥8 ✅)** · reactos/ klasöründe başka dosyayla kesişim: 0.

---

## §14. Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R-S1 | ReactOS ses alt sistemi bileşen adları nelerdir? | UNKNOWN |
| R-S2 | USB ses sınıfı destekleniyor mu? | ⚠️ VERIFICATION REQUIRED |
| R-S3 | Neva Engine hedef platform listesinde ReactOS var mı? (Q4/T-U3) | UNKNOWN |
| R-S4 | 347 sayfa / 0 isabet ölçümü tekrar üretilebilir mi? | ⚠️ VERIFICATION REQUIRED |
| R-S5 | Ses test altyapısı var mı? | UNKNOWN |
| R-S6 | Örnekleme hızı/bit derinliği destekleri neler? | UNKNOWN |
| R-S7 | Birden çok uygulama sesi paylaşabiliyor mu? | UNKNOWN |
| R-S8 | Düşük gecikmeli kestirme yolu var mı? | UNKNOWN |

---

## §15. Terim sözlüğü

| Terim | Anlamı |
|---|---|
| Ses yığını (audio stack) | Donanımdan uygulamaya kadar sesin geçtiği katman dizisi |
| Halka (link) | Bu dosyanın §5 modelindeki sırasız bileşen |
| Negatif kanıt | Arandı ve bulunamadığı kaydedilen sonuç |
| 0 isabet | Arama filtresine uyan kayıt olmaması |
| Sayfa sayacı | Wiki listesindeki toplam sayfa sayısı |
| Bot-duvarı | Otomatik istemcileri engelleyen ara sayfa (Anubis) |
| Anubis | Proof-of-work tabanlı bot koruma yazılımı (v1.19.1) |
| İçerik doğrulaması | Yanıt gövdesinin gerçek metin olup olmadığı kontrolü |
| Erişilebilirlik kanıtı | Yalnızca HTTP yanıt koduna dayanan kanıt |
| USB ses sınıfı (UAC) | USB üzerinden ses cihazı tanımlayan standart sınıf |
| Örnek hızı | Saniyedeki ses örneği sayısı (örn. 48 kHz) |
| Bit derinliği | Örneğin taşıdığı çözünürlük (örn. 32-bit) |
| Kanal düzeni | Hoparlör yerleşimi (örn. 8.1) |
| Buffer | Ses verisinin beklendiği tampon |
| Gecikme (latency) | Girdiden çıkışa geçen süre |
| Kestirme yolu (bypass) | Ara katmanları atlayan düşük gecikmeli erişim |
| Paylaşım | Aynı anda birden çok uygulamanın cihazı kullanması |
| Lock-free | Kilit kullanmadan eşzamanlı erişim |
| Zero-allocation | Çağrı sırasında bellek ayırma yapılmaması |
| Korumalı çıkış | DC offset için röle koruması |
| UNKNOWN | Verinin olmadığı, tahmin edilmediği durum |
| İşaretli belirsizlik | `[⚠️ VERIFICATION REQUIRED]` |
| Reddedilmiş varsayım | Kanıt olmadığı için yazılmayan iddia |
| Doğrulama planı | Bilinmeyeni kapatmak için sıralı adımlar |
| Karşılaştırma çerçevesi | İki sistemin eksen eksen eşleştirilmesi |
| Eşleşme tablosu | Gereksinim ↔ platform karşılığı |
| Kardeş dosya | Aynı klasördeki diğer içerik dosyaları |
| Üst dal | `isletim-sistemi-turu/reactos/` |
| Kanıt matrisi | İddia-URL-tarih-durum tablosu |
| Etiket | `**[ORİJİNAL …]**` / `[⚠️]` / `UNKNOWN` işaretleri |

---

## §15.5 Ölçüm protokolü (bu vault'un orijinal çerçevesi)

> **Etiket:** `[ORİJİNAL ÇERÇEVE · resmi kaynak değil]` — ReactOS'a özgü değildir;
> herhangi bir platformun ses yeteneğini sınamak için kullanılır.

| # | Ölçüm | Nasıl | Beklenen birim | ReactOS'ta durum |
|---|---|---|---|---|
| M1 | Cihaz listesi | ses cihazı enumerator'ı | ad listesi | ortam yok |
| M2 | Desteklenen formatlar | cihaz sorgusu | Hz · bit · kanal | ortam yok |
| M3 | Örnek hızı tutarlılığı | sabit hız testi | Hz | ortam yok |
| M4 | Buffer alt sınırı | küçültme taraması | örnek | ortam yok |
| M5 | Uçtan uca gecikme | kendi kendine ölçüm | ms | ortam yok |
| M6 | Dışsal gecikme | klavye→hoparlör | ms | ortam yok |
| M7 | Kesme (underrun) oranı | stres testi | olay/dk | ortam yok |
| M8 | Çoklu akış | eşzamanlı 2 akış | başarı/hata | ortam yok |
| M9 | Biçim dönüşümü | farklı Hz gönderimi | başarı/hata | ortam yok |
| M10 | Bozulma | THD+N ölçümü | % | ortam yok |
| M11 | DC offset | ortalama ölçüm | V | ortam yok |
| M12 | Kanal ayrımı | kanal testi | doğruluk | ortam yok |
| M13 | Tak-çıkar (USB) | cihaz değiştirme | tepki | ortam yok |
| M14 | Kestirme yolu | gecikme karşılaştırması | ms | ortam yok |
| M15 | Kararlılık | 1 saat sürekli akış | hata | ortam yok |

**Tablo kuralı:** "ortam yok" ifadesi **eksiklik değil, kayıt**tır; ölçüme başlamak
için önce §10'daki 20 adım tamamlanmalıdır.

---

## §15.6 Kırmızı bayraklar (bu dosyada yapılan tipik hatalar)

| # | Hata | Sonuç | Bu dosyadaki koruma |
|---|---|---|---|
| F1 | "0 isabet = ses yok" demek | Yanlış dışlama | §3 yorum yasağı |
| F2 | Başlıktan içerik türetmek | Kanıt zinciri kırılır | §11 notu |
| F3 | 347 sayısını kesin saymak | Tarih-spesifik ölçüm | [⚠️] işaretli |
| F4 | Ses API'si adı uydurmak | Hallüsinasyon | §6.2 UNKNOWN |
| F5 | Windows ile varsayılan eşitleme | Karşılaştırma okunmadı | §8 boş hücre yok |
| F6 | Gecikme tahmini yazmak | Ölçülmemiş sayı | §9/§15.5 "ortam yok" |
| F7 | URL tekrarı (diğer dosyalar) | Kanıt tekilleği bozulur | §13 ayrışık küme |
| F8 | Bot-duvarını içerik sanmak | Sahte doğrulama | §11 ayrım tablosu |
| F9 | Kod bloğu uydurmak | Dosya/Satır yasağı | §17 kod=0 |
| F10 | Dolgu cümlesi | Bilgi kirliliği | §17 kontrolü |
| F11 | `status: verified` yazmak | Kanıtsız doğrulama | frontmatter `verify-required` |
| F12 | Açık soruyu kapatmak | Bilgi kaybı | §14 8 satır açık |
| F13 | USB → ses diye atlamak | Kapsam kayması | §7 sınır maddesi |
| F14 | CoreMusic gereksinimini ReactOS'a atfetmek | Yanlış eşleşme | §9 sol sütun vault içi |
| F15 | Planı atlamak | Bilinmeyen kalıcı | §10 20 adım |

---

## §15.7 Çapraz referanslar

| Hedef | İlişki | Ne devralıyor |
|---|---|---|
| `index.md` | üst | Klasör künyesi, §GRUPLAR |
| `reactos-cekirdek-mimarisi.md` | üst katman | USB/çekirdek URL kayıtları, §11.5 erişim notu |
| `reactos-win32-uyumlulugu.md` | kardeş | Uyumluluk katmanı |
| `reactos-surucu-modeli.md` | kardeş | Sürücü/imzalama/AFD URL kayıtları |
| `reactos-kod-ornekleri.md` | kardeş | Etiketli kod örnekleri |
| `reactos-web-arastirma.md` | kardeş | URL tarama kaydı |
| `../../isletim-sistemi-audio` | üst dal | Genel ses kavramları (depends_on) |
| `../windows/` | kardeş platform | Windows ses karşılaştırması (okunmadı) |
| `../android/` · `../ios/` | kardeş platformlar | Mobil ses API'leri (bu turda okunmadı) |
| K004-dsp | dışı | DSP algoritmaları |

---

## §15.8 Sık sorulanlar

| Soru | Cevap |
|---|---|
| ReactOS ses çalabilir mi? | UNKNOWN — doğrulanamadı |
| Ses sürücüsü adı nedir? | Bilinmiyor, ad uydurulmadı |
| 48 kHz destekleniyor mu? | UNKNOWN |
| 32-bit float destekleniyor mu? | UNKNOWN |
| 8.1 surround destekleniyor mu? | UNKNOWN |
| ASIO gibi kestirme yol var mı? | UNKNOWN, varlık bile doğrulanmadı |
| Neden ses sayfası yok/ bulunamadı? | Wiki bot-duvarı + kayıtlı 0 isabet ölçümü |
| 0 isabet kesin mi? | Hayır — bu turda yeniden üretilemedi → [⚠️] |
| USB ses çalışır mı? | Bilinmiyor, `USB` başlığı içerik düzeyinde okunamadı |
| Ölçüm ne zaman yapılabilir? | §10'daki 20 adım tamamlandığında |
| Dosya neden bu kadar "UNKNOWN" dolu? | Tek kaynak (wiki) içerik düzeyinde erişilemez durumda |
| Bu dosyanın değeri nedir? | Bilinmeyeni ve kapatma planını belgelemesi |
| CoreMusic burayı kullanır mı? | Q4/T-U3 bilinmediği için UNKNOWN |
| Kod örneği var mı? | Hayır → `reactos-kod-ornekleri.md` |
| URL listesi nerede? | §13 · tarama `reactos-web-arastirma.md` |
| Erişim kısıtı nasıl aşılır? | JS çalıştıran istemci (§11 / `reactos-cekirdek-mimarisi.md` §11.5) |

---

## §16. Sınır dışı bırakılan konular

1. Ses API fonksiyon adları → UNKNOWN, ad uydurmak yasak.
2. Ses sürücüsü adları → UNKNOWN.
3. Windows ses yığını detayı → bu turda okunmadı (§8).
4. Kod örnekleri → `reactos-kod-ornekleri.md`.
5. Sürücü modeli → `reactos-surucu-modeli.md`.
6. Çekirdek bileşenleri → `reactos-cekirdek-mimarisi.md`.
7. Win32 API geneli → `reactos-win32-uyumlulugu.md`.
8. URL tarama kaydı → `reactos-web-arastirma.md`.
9. Donanım/DAC tasarımı → K001-donanım (bu dalın dışında).
10. DSP algoritmaları → K004-dsp (bu dalın dışında).

---

## §17. Ölçüm

| Metrik | Değer |
|---|---|
| Dosya | `reactos-ses-stacki.md` |
| Hedef satır | ≥500 |
| § sayısı | ≥16 |
| Benzersiz URL | 10 (≥8 ✅) |
| Kod bloğu | 0 |
| UNKNOWN sayısı | §5 tamamı + §6.2 (10 satır) |
| Reddedilen varsayım | 15 / 30 |
| Frontmatter | 10 alan ✅ · `group: mimari` ✅ |

**Ek kayıt (2026-10-12):** Bu dosyanın tüm `UNKNOWN` satırları **ölçüm bekleyen**
satırlardır; kapatılmaları §10'daki 20 adıma bağlıdır. Dosya `draft` değil,
`verify-required` durumundadır — yani "yazıldı ama doğrulanmadı" anlamına gelir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode