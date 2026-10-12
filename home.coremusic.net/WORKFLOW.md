---
title: "CoreMusic — home.coremusic.net İş Akışı"
type: docs
category: domain
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# home.coremusic.net — WORKFLOW.md

**docType:** workflow · **Klasör:** `home.coremusic.net/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `home.coremusic.net/` üzerinde bir değişikliğin **adım adım akışını** (adım → çıktı → neden → atlarsan ne olur) ve **kapıları** tanımlar. Home, subdomain sayfa servisi olduğu için hata doğrudan kullanıcıya yansır; bu yüzden kapılar (keşif → mockup → uygulama → test → rapor) zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Test kapısı phpunit ^10.5; `composer.json` `scripts` bloğu **YOK** | `home.coremusic.net/composer.json` doğrudan okundu |
| Test yapılandırması `phpunit.xml` | dosya diskte var |
| Mockup Before Frontend: `home-1024` PNG'leri | [[../.ai/ui-design/01-mockup-index]] + home `AGENTS.md` §4 Zorunlu 1 |
| Yeni PHP dosyası şablondan türetilir | [[../.ai/.templates/backend/php-template]] (Guardrail #16) |
| Commit subagent'a ait değil | Kök [[../AGENTS.md]] §7/§8 — "commit (subagent ATMAYACAK)" |
| Hata → kural dosyası → yeniden yaz | Kök [[../AGENTS.md]] §7 |

> VERIFICATION REQUIRED: `home.coremusic.net/composer.json` içinde `scripts` bloğu yoktur (doğrudan okundu) — bu yüzden test komutu phpunit üzerinden (`phpunit.xml`) tanımlıdır; script eklenmesi MO/Backend kararına bağlıdır. Ayrıca `.ai/.rules/error-recovery.md` **diskte YOK** (`.ai/.rules/` altında yalnız `senior-mode.md` var) — hata kurtarma adımı kök `AGENTS.md` §7 metnine dayanır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `home.coremusic.net/` kod/config/sayfa/test değişiklik akışı | `vendor/` içeriği (composer yönetir, elle değil) |
| Kapılar: keşif → mockup → uygulama → phpunit → browser → rapor | Deploy/CI pipeline → DevOps |
| Commit kuralı (subagent atmaz) | Vault `.ai/` içi doküman akışı → [[../AGENTS.md]] / `.ai/WORKFLOW.md` |
| Yeni dosya için şablon zorunluluğu | `assets.coremusic.net` CSS/JS üretimi (oradan OKUNUR) |

- **Kullananlar:** Backend Architect (birincil), UI Designer (görünüm), QA Engineer (tests/), MO (kapı + rapor).
- **Ön koşul:** [[CONTEXT.md]] (envanter) + [[CLAUDE.md]] (kural) okunmuş; hedef dosya diskte mevcut.
- **Gizli veri:** `config/.env` içeriği okunmaz/vault'a yazılmaz.

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → çıktı → neden → atlarsan ne olur)

| # | Adım | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|-------|------------------|-------|-------|
| 1 | Görev tanımını netleştir (prompt-maker / soru kapısı) | Onaylı görev özeti | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert | Önce ne istendiğini anlamak; yanlış anlarsan tüm iş boşa gider. | Kapı (kök `AGENTS.md` §1) onaysız sonraki aşamaya geçmez. Tanım okunmadan başlarsan beklenti ile kod ayrışır. Kapıdan geçince elinde ölçülebilir bir çıktı (özeti) olur. Atlarsan yanlış işin üretimi ve zaman kaybı. |
| 2 | Klasör kurallarını oku: [[CLAUDE.md]] + [[AGENTS.md]] + [[CONTEXT.md]] | Kural + envanter | Klasör-specific yasaklar orada | Yasak ihlali (ör. `.env` sızıntısı, oturum çifte kaynağı) | Bu klasörün özel kurallarını öğrenmek. | Kurallar klasör dosyalarında tutulur çünkü genel kural her detayı taşıyamaz. Okunmadan girilirse `HomeSessionManager`/`HomeAuthBridge` yasakları fark edilmez. Envanter okununca hangi dosyanın kaç parçadan oluştuğu bellidir. Atlarsan kapsam ve güvenlik hataları. |
| 3 | **Mockup kapısı** (görünüm işi ise): [[../.ai/ui-design/01-mockup-index]] + ilgili `home-1024` PNG | Okunmuş mockup | Görsel kanıt zorunlu | Kanonik olmayan UI, revert | Ekranın gerçek ölçüsünü görmek. | Kök `AGENTS.md` §7 bu adımı zorunlu kılar; home `AGENTS.md` §4 Zorunlu 1 header/footer'ı mockup ile karşılaştırmayı şart koşar (Header 60px, içerik 450px, Footer 90px kanonik referans — vault iddiası, PNG ölçümünde doğrulanır). Görsel okunamıyorsa DUR ve bildir. Atlarsan tasarım sapması. |
| 4 | Hedef dosyayı 1. kez oku + disk kanıtı topla | Okunmuş dosya, envanter | Anti-overthink: ilk okumadan KARAR VER | Aynı dosya 2. kez okunur, token israfı | Dosyayı bir kez okuyup karar vermek. | Anti-waste kuralı (kök `AGENTS.md` §5) aynı dosyayı görevde 2. kez okumayı yasaklar. İlk okuma yeterli kanıtı verir; ikinci okuma "emin olmak" bahanesidir. Karar ilk okumadan verilir. Atlarsan tekrarlı okuma ve gecikme. |
| 5 | Değişikliği yap — yalnız hedef dizin: `config/` · `include/` · `pages/` | Kod diff'i | Kapsam disiplini + katman sınırı | Kapsam genişlemesi, çakışma, katman ihlali | İstenen yeri değiştirmek, başka yere dokunmamak. | Kod yalnız ilgili dizine girer: mantık `include/`'a, görünüm `pages/`'e, ayar `config/`'e (CONTEXT §3.1). Kapsam genişlerse başka oturumun çalışması bozulur. Kilitli diff, incelemeyi ve revert'i kolaylaştırır. Atlarsan çakışma ve temizlik işi. |
| 6 | Yeni PHP dosyası → `php-template.md`'den türetilir | Şablondan üretilmiş dosya | Guardrail #16 — şablonsuz dosya üretilmez | Standartsız dosya, vault reddi | Yeni dosyayı hazır kalıba göre açmak. | Şablon zorunluluğu kök `AGENTS.md` §4 + Guardrail #16'dır. Şablon okunmadan üretilen dosya standart dışı kalır ve revizyonda elenir. Şablon, frontmatter/kuralları hazır getirir. Atlarsan reddedilen dosya. |
| 7 | Test ekle/koru: `tests/Unit/` altına | Yeni/güncel test dosyası | Regresyon kapısı | Sessiz kırılma, production'da arıza | Kodun doğru kaldığını kanıtlayan soru kâğıdı. | Test ayrı dosyadır çünkü kod değişince test değişmez, yalnız doğrular. `tests/` hiyerarşisi `Unit/{Component,Layout,Loader}` desenindedir (disk: 4 test + 1 support). Eklenmezse gelecekteki değişiklik kırığı yakalamaz. Kapıyı atlarsan hata ancak kullanıcıda görülür. |
| 8 | Doğrulama: phpunit (`phpunit.xml` üzerinden) | Yeşil test | Regresyon kapısı | Hatalı değişiklik git'e girer | Sınavı çalıştırıp geçmek. | `composer.json` `scripts` yok (doğrudan okundu) → koşu `phpunit.xml` üzerinden tanımıdır. Kapıdan geçmeyen iş tamamlanmış sayılmaz. Kırmızı ise `.ai/.rules/` yerine kök `AGENTS.md` §7: kurala dön → yeniden yaz → tekrar kontrol. |
| 9 | UI etkisi varsa browser testi (gerçek sayfa) | Ekran doğrulaması | Yerleşim kırığını yakala | Bozuk layout kullanıcıya ulaşır | Değişiklikten sonra sayfayı tarayıcıda kontrol etmek. | Kök `AGENTS.md` §7 #7 bu adımı zorunlu kılar; DOM/layout iddiası kod ile kanıtlanamaz. Mockup (PNG) ile karşılaştırma burada yapılır. Görsel kapı geçince kullanıcı deneyimi güvenceye alınır. Atlarsan gizli yerleşim hatası. |
| 10 | Rapor yaz; **commit ATMAZ** | Rapor | Yetki sınırı | Yetkisiz tarih, revert riski | İş bitince haber vermek; commit'i başkasının atması. | Subagent commit atmaz (kök `AGENTS.md` §7/§8); commit yetkisi orkestratördedir. Tarih (git log) tek elden yazılmazsa iz sürme bozulur. Rapor, kapıyı orkestratöre teslim eden çıktıdır. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur. |

**eli10 / eli15 blokları (§3.1'deki 10 adımın karşılığı — tablo dışı tam metin):**

**Adım 1 — Görev tanımı**
> **eli10 (basit):** Önce ne istendiğini anlamak.
> **eli15 (detay):** Kapı onaysız başlamaz; tanım okunmadan kod yazılırsa beklenti ile sistem ayrışır. Çıktı, ölçülebilir bir özet (görev özeti) olur. Atlarsan yanlış iş üretirsin. İlk kapı her görevde aynıdır.

**Adım 2 — Klasör kuralları**
> **eli10 (basit):** Bu klasörün kendi kurallarını öğrenmek.
> **eli15 (detay):** Kurallar ayrı dosyadır çünkü genel kural her detayı taşıyamaz. Okunmadan girilirse `.env` yasağı ve oturum tek-kapı kuralı fark edilmez. Envanter de okununca kaç dosya olduğu baştan bellidir. Atlarsan güvenlik ve kapsam hatası.

**Adım 3 — Mockup kapısı**
> **eli10 (basit):** Ekranın gerçek ölçüsünü resimden görmek.
> **eli15 (detay):** Home'da header/footer ölçüleri kanonik PNG'ye bağlıdır. Görsel okunamıyorsa DUR ve bildir; tahmini ölçü yazılmaz. Kapı, kod ile görseli karşılaştırır. Atlarsan tasarım sapması ve revert.

**Adım 4 — 1. kez okuma**
> **eli10 (basit):** Dosyayı bir kez okuyup karar vermek.
> **eli15 (detay):** Anti-waste kuralı ikinci okumayı yasaklar. İlk okuma kanıtı verir; ikinci okuma israftır. Karar ilk okumadan verilince akış hızlanır. Atlarsan token israfı ve gecikme.

**Adım 5 — Uygulama**
> **eli10 (basit):** Yalnız hedef dosyada değişiklik yapmak.
> **eli15 (detay):** Kapsam kilitlidir çünkü eşzamanlı oturumlar vardır. Mantık `include/`'a, görünüm `pages/`'e girer. Kapsam genişlerse başka oturumun çalışması bozulur. Atlarsan çakışma ve temizlik işi.

**Adım 6 — Şablon**
> **eli10 (basit):** Yeni dosyayı hazır kalıba göre açmak.
> **eli15 (detay):** Guardrail #16 şablonsuz dosyayı reddeder. Şablon, kural ve standartları hazır getirir. Okunmadan üretilen dosya revizyonda elenir. Atlarsan standartsız dosya + ikinci iş.

**Adım 7 — Test**
> **eli10 (basit):** Kodun doğru kaldığını kanıtlayan sorular yazmak.
> **eli15 (detay):** Test ayrıdır ki kod değişince test yalnız doğrulasın. Mevcut desen `tests/Unit/*` (disk: 4 test, 1 support). Yeni bileşen = yeni test. Atlarsan sessiz kırılma.

**Adım 8 — phpunit**
> **eli10 (basit):** Sınavı çalıştırıp geçmek.
> **eli15 (detay):** `composer.json` `scripts` bloğu yok; koşu `phpunit.xml` üzerindendir. Kapıdan geçmeyen iş bitmez. Kırmızıda kök `AGENTS.md` §7 kurala dönme prosedürü işletilir. Atlarsan kırık değişiklik git'e girer.

**Adım 9 — Browser testi**
> **eli10 (basit):** Değişikliği gerçek sayfada görmek.
> **eli15 (detay):** DOM/layout iddiası kod ile kanıtlanamaz. Mockup PNG'si ile karşılaştırma burada yapılır. Kök `AGENTS.md` §7 #7 zorunlu kılar. Atlarsan gizli yerleşim hatası.

**Adım 10 — Rapor**
> **eli10 (basit):** İş bitince haber vermek; commit'i başkasına bırakmak.
> **eli15 (detay):** Commit yetkisi orkestratördedir; tarih tek elden yazılır. Rapor, kapıyı teslim eden çıktıdır. Dağınık commit iz sürmeyi bozar. Atlarsan iş görünmez kalır.

### 3.2 Kapılar (Gate)

```text
[K1 KEŞİF: kural + envanter + mockup] ─onay─▶ [K2 UYGULAMA: config|include|pages]
    ─ şablonsuz yeni dosya yok ─▶ [K3 TEST: phpunit yeşil]
    ─ UI etkisi varsa K4 BROWSER ─▶ [K5 RAPOR → ORKESTRATÖR → COMMIT (MO)]
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| K1 Keşif | CLAUDE + AGENTS + CONTEXT okundu; görünüm işi ise mockup okundu; 1. okumadan karar verildi | Görev uygulamaya alınmaz (mockup yoksa DUR ve bildir) |
| K2 Uygulama | Diff yalnız `config/`, `include/`, `pages/`, `tests/`; yeni dosya `php-template.md`'den | Kapsam daraltılır / dosya şablondan yeniden üretilir |
| K3 Test | phpunit (`phpunit.xml`) yeşil | Kök `AGENTS.md` §7 → kurala dön → yeniden yaz → tekrar kontrol (3 başarısız → DUR + 1 soru) |
| K4 Browser | Gerçek sayfa + mockup karşılaştırması geçti | Düzelt, tekrar test |
| K5 Rapor | Rapor tam; **commit ORKESTRATÖRDE** | Subagent commit atmaz |

**eli10 / eli15 blokları (§3.2 — 5 kapı):**

**K1 Keşif**
> **eli10 (basit):** Kurallar ve resim okunmadan işe başlamamak.
> **eli15 (detay):** Kapı, yanlış dosyayı ve yanlış tasarımı baştan engeller. Okunmayan kural ihlal edilir. Mockup yoksa iş durur, tahmin yürütülmez. Red durumunda görev uygulamaya alınmaz.

**K2 Uygulama**
> **eli10 (basit):** Yalnız izinli dizinlerde değişiklik.
> **eli15 (detay):** Kapsam disiplini çakışmayı önler. Yeni dosya şablondan türetilir. Redde genişletilmiş kapsam geri alınır. Katman sınırı korunur.

**K3 Test**
> **eli10 (basit):** Sınav yeşil olmadan iş bitmez.
> **eli15 (detay):** Kapı, regresyonu yakalar. Kırmızıda kurala dönülür, kör deneme yapılmaz. 3 başarısız düzeltmede durulur ve tek soru sorulur. Atlarsan hata git geçmişine girer.

**K4 Browser**
> **eli10 (basit):** Değişikliğin gerçek ekranda doğru olduğunu görmek.
> **eli15 (detay):** Kod ile yerleşim kanıtlanamaz. Mockup ile karşılaştırma zorunludur. Redde düzeltip tekrar test edilir. Atlarsan kullanıcıya bozuk sayfa gider.

**K5 Rapor**
> **eli10 (basit):** İşi haber vermek; commit'i orkestratöre bırakmak.
> **eli15 (detay):** Yetki sınırı burada kapanır. Tarih tek elden yazılır. Rapor olmadan iş görünmezdir. Subagent commit atmaz.

### 3.3 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük, güvenli | Her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Adımın tek sahibi | Sahipsiz adım |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsızlık, tarama |
| 4 | **Cihaz izolasyonu** | Cihaz farkı ayrı | Yayılır, drift |
| 5 | **Vendor karantinası** | `vendor/` ayrı | Güncelleme kodu bozar |

> **eli10 (basit):** Adımlar ve kapılar ayrı kutularda; bir kutu değişince diğeri bozulmasın.
> **eli15 (detay):** Süreç ayrı dosyadır çünkü kapılar (onay, mockup, test, rapor) koddan farklı hızda değişir; kod içine gömülse kapı unutulur. Okunması, işe sırayla başlamayı sağlar. Yazması, tekrarlanabilir ve denetlenebilir akış bırakır. Bölünmezse "hangi kapı kaldı" sorusu cevapsız kalır.

### 3.4 Değişiklik Tipi → Zorunlu Adım → Kapı Eşlemesi

| # | Değişiklik tipi | Zorunlu adımlar | Geçilecek kapılar | eli10 | eli15 |
|---|-----------------|-----------------|-------------------|-------|-------|
| 1 | `pages/*.php` görünüm değişikliği | Adım 1-5 + **Adım 3 (mockup)** + Adım 9 | K1, K2, K4, K5 | Ekran değişikliği önce resimle görülür. | Görünüm dosyası değişince kanonik ölçü (home-1024) ile karşılaştırma zorunludur; aksi hâlde ölçüm hatası kullanıcıya gider. Kapılar sırayla geçilir. Mockup yoksa DUR ve bildir. |
| 2 | `include/Component/*` bileşen değişikliği | Adım 1-2 + Adım 4-5 + Adım 7-8 | K1, K2, K3, K4 | Parça kodu değişince testi de değişir. | Bileşen, `ComponentInterface` sözleşmesine bağlıdır; sözleşme dışına çıkan değişiklik katman ihlalidir. Mevcut test deseni `tests/Unit/Component/` altındadır. Test eklenmeden kapı geçilmez. |
| 3 | `include/Repository/` veya `Stream/` değişikliği | Adım 1-2 + Adım 4-5 + Adım 7-8 | K1, K2, K3 | Veri ve yayın işi ayrı kapıdadır. | Veri erişimi PDO (ADR-002) üzerinden yürür; sorgu değişikliği hazır ifade (prepared) içerir. Yayın akışı ağ davranışı olduğu için ayrıca sınanır. Güvenlik yüzeyi ise K3'e Security eklenir. |
| 4 | `config/` ayar değişikliği (`.env` hariç) | Adım 1-2 + Adım 4-5 | K1, K2 | Ayar tek yerden değişir, kod değil. | `.env` içeriği okunmaz/yazılmaz; yalnız yapılandırmada bulunmayan sabitler `constants.php`/`config.php` üzerinden düzenlenir. Değişiklik diff'i gizli değer içermez. Kapı, gizli veri sızıntısını da kontrol eder. |
| 5 | `config/.env` değişikliği | Adım 1-2 + **Security bilgilendirmesi** | K1 + onay | Gizli dosya elde değişmez. | `.env` değerleri hiçbir dokümana, log'a veya rapora kopyalanmaz. Değişiklik talebi reddedilip sunucu yöneticisine/Security'e götürülür. Kapı, Secret Yok guardrail'ıdır. |
| 6 | `tests/` test değişikliği | Adım 1-2 + Adım 4 + Adım 7-8 | K1, K2, K3 | Test dosyası kendi kapısından geçer. | Test, üretim koduna dokunmaz; yalnız doğrular. Mevcut hiyerarşi `Unit/{Component,Layout,Loader}` korunur. Yeni klasör yalnız `phpunit.xml` ile birlikte eklenir. Kapı yeşil kalır. |
| 7 | Yeni dosya (PHP) | Adım 1-2 + **Adım 6 (şablon)** + Adım 7-8 | K1, K2, K3 | Yeni dosya her zaman hazır kalıpla açılır. | Guardrail #16 şablonsuz dosyayı reddeder; `php-template.md` okunmadan üretim yapılmaz. Dosya doğru dizine (`include/` mi `pages/` mi) şablonla birlikte kararlaştırılır. Atlarsan reddedilen dosya + ikinci iş. |
| 8 | `header.php` / `footer.php` değişikliği | Adım 1-5 + **Adım 3 (mockup)** + Adım 9-10 | K1, K2, K4, K5 | Üst/alt bilgi tüm sayfaları etkiler. | home `AGENTS.md` §4 Zorunlu 1: header/footer ölçüsü mockup ile karşılaştırılır (kanonik referans: Header 60px · İçerik 450px · Footer 90px — vault iddiası, PNG'de doğrulanır). Tek dosya değişimi tüm sayfalara yayıldığı için browser kapısı zorunludur. |
| 9 | `vendor/` değişikliği | — (RED) | K1 reddi | Kurulan kutuya elle girilmez. | Yalnız `composer install/update` yönetir; elle dosya ekleme/silme güncellemede kaybolur. Talep DevOps/Backend'e raporlanır. Kapı reddi, revert yerine baştan engellemedir. |

**eli10 / eli15 blokları (§3.4 — 9 değişiklik tipi):**

**1 · `pages/` görünüm**
> **eli10 (basit):** Ekran görünümü değişince önce resimle karşılaştırılır.
> **eli15 (detay):** Mockup kapısı zorunludur; tahmini ölçü yazılmaz. Kapılar sırayla geçilir. Yoksa DUR ve bildir. Atlarsan ölçüm hatası kullanıcıya gider.

**2 · Bileşen**
> **eli10 (basit):** Parça kodu değişince testi de değişir.
> **eli15 (detay):** Sözleşme (`ComponentInterface`) korunur. Test deseni `Unit/Component/` altındadır. Katman dışına çıkan değişiklik revert edilir. Kapı test olmadan geçilmez.

**3 · Veri/yayın**
> **eli10 (basit):** Sorgu ve yayın işi ayrı kapıdadır.
> **eli15 (detay):** Sorgu prepared statement ile yürür. Yayın akısı ayrıca sınanır. Güvenlik yüzeyi ise Security eklenir. Kapı, sessiz kırığı yakalar.

**4 · Ayar**
> **eli10 (basit):** Ayar tek yerden değişir; gizli dosya değil.
> **eli15 (detay):** Sabitler `config/` üzerinden düzenlenir. Diff gizli değer içermez. Kapı sızıntıyı da kontrol eder. Değişiklik genelde küçük ve tek dosyalıdır.

**5 · `.env`**
> **eli10 (basit):** Gizli dosya elde değişmez, talep reddedilir.
> **eli15 (detay):** Değerler hiçbir yere kopyalanmaz. Talep sunucu yöneticisine/Security'e götürülür. Kapı Secret Yok guardrail'ıdır. Atlarsan sızıntı.

**6 · Test**
> **eli10 (basit):** Test dosyası kendi kapısından geçer.
> **eli15 (detay):** Test üretim koduna dokunmaz. Hiyerarşi korunur. Yeni klasör `phpunit.xml` ile birlikte eklenir. Kapı yeşil kalır.

**7 · Yeni dosya**
> **eli10 (basit):** Yeni dosya her zaman hazır kalıpla açılır.
> **eli15 (detay):** Şablon okunmadan üretim yapılmaz. Doğru dizin şablonla birlikte seçilir. Şablonsuz dosya revizyonda elenir. Atlarsan ikinci iş.

**8 · header/footer**
> **eli10 (basit):** Tüm sayfaları etkileyen üst/alt bilgi değişikliği.
> **eli15 (detay):** Ölçü mockup ile karşılaştırılır. Tek değişiklik tüm sayfalara yayılır. Browser kapısı zorunludur. Atlarsan site geneli kayma.

**9 · `vendor/`**
> **eli10 (basit):** Kurulan kutuya elle girilmez — talep reddedilir.
> **eli15 (detay):** Yalnız composer yönetir. Elle değişiklik güncellemede kaybolur. Talep raporlanır. Kapı revert yerine baştan engeller.

### 3.5 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Mockup okumadan `header.php`/`footer.php` düzenlemek | Adım 3 → PNG ile ölçüm, sonra kod |
| 2 | Yeni PHP dosyasını şablonsuz açmak | Adım 6 → `php-template.md` (Guardrail #16) |
| 3 | Mantığı `pages/` içine yazmak | Mantık `include/`'a; `pages/` yalnız görünüm |
| 4 | Testi atlayıp "geçiyordur" demek | Adım 8 → `phpunit.xml` koşusu zorunlu |
| 5 | `.env` değerini rapora/log'a yapıştırmak | Değer asla kopyalanmaz; talep reddedilir |
| 6 | Aynı dosyayı "emin olmak" için 2. kez okumak | Adım 4 → ilk okumadan karar (kök `AGENTS.md` §5) |
| 7 | Subagent'ın commit atması | Adım 10 → rapor; commit orkestratörde |

**eli10 / eli15 (§3.5 bloğu):**

> **eli10 (basit):** En sık yapılan yedi hata ve doğrusu — hepsi yukarıdaki adımlara çıkar.
> **eli15 (detay):** Bu tablo, kapıların neden var olduğunu örneklerle gösterir. İçine yalnız tekrar eden gerçek hatalar girer. Okunması, işe başlarken aynı tuzağa düşmeyi engeller. Yeni hata türü görülünce tek satır eklenir; eski satırlar silinmez. Hata 3 başarısız düzeltmeye ulaşırsa §4 #9 gereği durulur ve tek soru sorulur.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Faz 1 keşif onaysız kod yok | Yanlış dosyaya yazımı engeller (kök `AGENTS.md` §4) |
| 2 | Aynı dosya görevde 2. kez okunmaz | Anti-overthink / token israfı (kök `AGENTS.md` §5) |
| 3 | Görünüm işi → mockup okunmadan kod yazılmaz | Kanonik ölçü korunur (home `AGENTS.md` §4 Zorunlu 1) |
| 4 | `config/.env` içeriği hiçbir yere yazılmaz | Secret Yok guardrail'ı (home `AGENTS.md` §4 Yasak 1) |
| 5 | Oturum yalnız `HomeSessionManager`, kimlik yalnız `HomeAuthBridge` | Çift kaynak doğmasın (home `AGENTS.md` §4) |
| 6 | Hata → kök `AGENTS.md` §7 kurala dön → yeniden yaz → tekrar kontrol | Kör düzeltme döngüsü durur (`.ai/.rules/error-recovery.md` diskte YOK — §1 notu) |
| 7 | UI etkisi → browser testi (gerçek sayfa) | Görsel kanıt zorunlu (kök `AGENTS.md` §7) |
| 8 | **Commit subagent ATMAZ** | Tarih/entegrasyon orkestratörde (kök `AGENTS.md` §7/§8) |
| 9 | 3 başarısız düzeltme → DUR + 1 kısa soru | Kör deneme döngüsüne girilmez (kök `AGENTS.md` §5) |

> **eli10 (basit):** Bu kurallar işin sırayla, güvenli ve onayla yürümesini, commit'in tek elden atılmasını sağlar.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazmaları, denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: CLAUDE+AGENTS+CONTEXT (+mockup)] → [K2: config|include|pages diff]
  → (yeni dosya: php-template) → [K3: phpunit yeşil]
  → (UI ise [K4: BROWSER — mockup karşılaştırma])] → [K5: RAPOR — commit yok]
  → ORKESTRATÖR → COMMIT (MO)
```

Adım detayı §3.1 · kapı detayı §3.2.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit |
| 3 | Adım tablosu | Her adımda "Çıktı" + "Neden" + "Atlarsan ne olur" dolu (10 satır) |
| 4 | Commit kuralı | §3.1 #10 + §4 #8 "ATMAZ" ifadesi mevcut |
| 5 | Kapılar | K1–K5 tablosu + zincirle uyumlu |
| 6 | Placeholder | Dosyada doldurulmamış şablon değişkeni kalmadı (arama deseni: iki parantez + harf) |
| 7 | Wiki-link | Wiki-link biçimi (çift köşeli parantez) kullanıldı; hedefler diskte var (olmayanlar §1/§6 notunda) |
| 8 | eli10 + eli15 | §3.1 (10) + §3.2 (5) + §3.3 + §4 bloklarında etiketli blok |
| 9 | Disk kanıtı | `composer.json` `scripts` YOK + `phpunit.xml` VAR + `.ai/.rules/error-recovery.md` YOK — doğrulandı |
| 10 | Dokunulmaz | Mevcut `CLAUDE.md` / `AGENTS.md` değiştirilmedi |
| 11 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Uygulama kuralları |
| Klasör rolleri | [[AGENTS.md]] | Routing (MEVCUT) |
| Klasör context | [[CONTEXT.md]] | Envanter + çelişki kaydı |
| Kök master kurallar | [[../AGENTS.md]] | §1 kapı · §4 keşif · §5 anti-overthink · §7 loop |
| Vault süreç | [[../AGENTS.md]] + `.ai/WORKFLOW.md` | Faz kapıları |
| Mockup indeksi | [[../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend |
| PHP şablonu | [[../.ai/.templates/backend/php-template]] | Yeni dosya (Guardrail #16) |
| Context şablonu | [[../.ai/.templates/frontend/context-template]] | İskelet (Guardrail #16) |
| Test yapılandırması | `home.coremusic.net/phpunit.xml` | K3 kapısı kanıtı |
| Paket tanımı | `home.coremusic.net/composer.json` | phpunit ^10.5 · `scripts` YOK kanıtı |
| Auth süreç | [[../auth.coremusic.net/WORKFLOW.md]] | Köprü hedefinin akışı |
| Template kaynağı | `.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03 · **docType:** workflow
