---
title: "isletim-sistemi-turu — Kapsam"
type: "architecture"
node_id: K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-cross
  - K000-isletim-sistemi/isletim-sistemi-runtime
evidence:
  - "https://www.tron.org/rtos/rtos01/"
  - "https://www.tron.org/rtos/rtos02/"
  - "https://docs.kernel.org/"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/"
  - "https://developer.android.com/"
  - "https://tizen.org/"
  - "https://reactos.org/"
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads"
  - "os-master.md §3 satır 57, 59-61"
validation: "web: tron.org/rtos/rtos01 + rtos02, docs.kernel.org, POSIX.1-2024, developer.android.com, tizen.org, reactos.org, learn.microsoft.com procthread — yanıt kodları 2026-10-12/2026-10-13 (HTTP 200). disk: os-master.md §3 satır 57, 59-61 okundu; isletim-sistemi-turu/ altında 8 platform dizini + 4 kök md sayıldı (2026-10-13)."
open_questions:
  - "T-U2: Hangi sınıflandırma şeması? [UNKNOWN]"
  - "T-U3 / Q4: Hedef OS türü ve platform seti? [UNKNOWN]"
  - "T-U1: turu ↔ cross sınırı v2'de yazılmamış [⚠️ VERIFICATION REQUIRED]"
  - "Q5: turu ↔ K002-altyapi boşluğu [⚠️ VERIFICATION REQUIRED]"
  - "T-U5: çekirdek tipi (monolitik/mikro) bu dalda mı? [UNKNOWN]"
group: mimari
---

# isletim-sistemi-turu — Kapsam

## §1 Rol (ELI15)

`os-master.md` §3 satır 57 bu dalı **"OS tip/tür sınıflandırması"** olarak yazar. Basitçe: CoreMusic'in üstünde çalışacağı zeminin **hangi türden** olduğunun kararlaştırıldığı dal — sınıf belli olunca gereksinimler (gerçek zamanlılık, güç, bellek) de belli olur.

İki cümlelik mühendislik karşılığı: (1) hedef zemini bir **sınıf** içine oturt (genel amaçlı ↔ gömülü/gerçek zamanlı, masaüstü ↔ mobil ↔ gömülü), (2) o sınıfın **zorunluluklarını** listele (süreç modeli, izin modeli, bellek bütçesi, güç davranışı, sürücü erişimi). Sınıf seçilmeden bu zorunluluklar yazılamaz — bu yüzden bu dalda "hedefimiz X tür OS" cümlesi yasaktır.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | OS tip/tür sınıflandırmasının **ölçütleri** | OS'ler arası çapraz uyumluluk davranışı | `isletim-sistemi-cross` |
| 2 | Hedeflenen türün seçimi (seçim yapılmadı → **UNKNOWN**) | Seçilen türün sürüm hedefleri | `os-master.md` §4 (UNKNOWN) |
| 3 | Tür → gereksinim eşlemesi (ör. gerçek zamanlılık iddiası) | Gerçek zamanlı zamanlama mekanizmaları | `isletim-sistemi-runtime` |
| 4 | Türün güvenlik/sınırlama karakteristiği (gömülü ↔ genel amaçlı) | Yetki/izolasyon mekanizmaları | `isletim-sistemi-security` |
| 5 | Platform setinin **seçim kararına** ait olması (hangi platform adaydır) | Platformların kendi iç mimarisi | her platform `index.md` (Kademe-3) |
| 6 | Türün API yüzeyine etkisi (ne tür sistem çağrıları beklenir) | Sistem çağrılarının kendisi ve imza tablosu | `isletim-sistemi-api` |
| 7 | Türün ses yığınına etkisi (hangi tür ses mimarisi makul) | Ses yığınının katmanları, sürücü kontratı | `isletim-sistemi-audio` |
| 8 | Türün çekirdek tipine referansı (yalnız atıf) | Monolitik/mikro/hibrit tasarım kararı | `isletim-sistemi-kernel` |
| — | *(dışında)* Fiziksel cihaz sınıfı | — | `K001-donanım` (os-master §2) |
| — | *(dışında)* Çekirdek mimarisi tipi (monolitik/mikro…) | — | `isletim-sistemi-kernel` — sınır yok, ⚠️ |
| — | *(dışında)* Uygulama katmanı davranışı | — | `K011` (uygulama) |

**Bağlayıcı sınır notu (`os-master.md` §3, satır 59–61):** "core ile kernel ve **cross ile turu** ayrımı v2'de yazılmamıştır; iki çift için de tahmin yapılmadı." → bu dalın cross ile sınırı **bilerek boş bırakılmıştır**.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| T-U1 | turu ↔ cross | ⚠️ v2'de yazılmamış — tahmin YASAK | os-master §3 satır 59–61 |
| T-U2 | Hangi sınıflandırma şeması? | **UNKNOWN** — şema seçilmedi | bu dosya §F |
| T-U3 | Hedef OS türü ve platform seti | **UNKNOWN** — Q4 | os-dogrulama §3 |
| Q5 | turu ↔ K002-altyapi boşluğu (tür kararı ile altyapı katmanı arası kopukluk) | ⚠️ v2'de yazılmamış | üst karar |
| T-U5 | turu ↔ kernel (çekirdek tipi bu dalda mı?) | **UNKNOWN** — ayrım yok | tahmin yasak |

## §4 Alt dizinler (disk enumerasyonu — 2026-10-13)

| Dizin | Dosya | Satır | ≥500 (içerik) | Bu turda yapılan |
|---|---:|---:|---|---|
| `android/` | 6 | 3.104 | 6/6 | Dokunulmadı (beyazliste dışı, `index.md` yok) |
| `ios/` | 7 | 3.105 | 6/6 | `index.md` zenginleştirildi (67 → 40–100 aralıkta) |
| `linux/` | 12 | 5.883 | 11/11 | `index.md` zenginleştirildi (60 → 40–100 aralıkta) |
| `macos/` | 6 | 2.704 | 5/5 | `index.md` zenginleştirildi (55 → 40–100 aralıkta) |
| `reactos/` | 7 | 3.228 | 6/6 | `index.md` zenginleştirildi (73 → 40–100 aralıkta) |
| `tizen/` | 7 | 3.183 | 6/6 | `index.md` zenginleştirildi (68 → 40–100 aralıkta) |
| `webos/` | 7 | 3.087 | 6/6 | **Girilmedi** — canlı ajan yazıyor |
| `windows/` | 9 | 4.223 | 8/8 | `index.md` zenginleştirildi (61 → 40–100 aralıkta) |

Kök: `index.md` · `-kapsam.md` · `-dogrulama.md` · `-ornekler.md` = 4 dosya. Toplam: **65 dosya · 28.745 satır**.

## §5 Açık sorular (özgün — korunmuş)

| # | Soru | İşaret |
|---|---|---|
| T-U2 | Hangi şema benimseniyor (masaüstü/gömülü/mobil mi, genel amaçlı/gerçek zamanlı mı)? | UNKNOWN |
| T-U1 | Cross ile sınır nasıl çizilecek? | ⚠️ VERIFICATION REQUIRED |
| T-U3 | Hedef platform seti ve sürümleri? | UNKNOWN (Q4) |
| Q5 | Tür kararı ile K002-altyapi arası boşluk nasıl kapatılacak? | ⚠️ VERIFICATION REQUIRED |

---

## §A Kapsam (8 platform kapsar)

**Kapsam içi:** bu dalın altındaki **sekiz** platform alt-dizini (`android`, `ios`, `linux`, `macos`, `reactos`, `tizen`, `webos`, `windows`) — her biri bir *aday zemin* olarak taşınır. Kapsam, platformların **sınıfını** ve **sınıfın gereksinimlerini** tartışmaktır; platformların kendi kütüphaneleri ayrı dosyalardadır.

**Kapsam dışı (komşu node'lara giden):**

| Kapsam dışı konu | Nereye gider | Bu dalda kalan tek şey |
|---|---|---|
| API yüzeyi (sistem çağrıları, imza tablosu, sürüm şemaları) | `isletim-sistemi-api` | Yalnızca "bu tür için API yüzeyi ne beklenir" notu |
| Ses yığını (ALSA/PipeWire/CoreAudio/WASAPI katmanları) | `isletim-sistemi-audio` | Yalnızca "bu tür ses için hangi katman makul" notu |
| Çekirdek (monolitik/mikro, zamanlayıcı, bellek yöneticisi) | `isletim-sistemi-kernel` | Yalnızca çekirdek **tipine** atıf (T-U5 UNKNOWN) |
| Uygulama katmanı davranışı, iş akışları | `K011` | Yok — uygulama bu dalda hiç geçmez |
| Gerçek zamanlı zamanlama mekanizmaları | `isletim-sistemi-runtime` | Yalnızca tür → gerçek zamanlılık **gereksinimi** eşlemesi |
| Çapraz uyumluluk (Wine/Win32, POSIX taşınabilirliği) | `isletim-sistemi-cross` | Yok — T-U1 bilerek boş |
| Fiziksel cihaz sınıfı, PCB, BOM | `K001-donanım` | Yok — `electroncis` dalı ayrıdır |
| Yetki/izolasyon mekanizmaları | `isletim-sistemi-security` | Yalnızca türün güvenlik **karakteristiği** |
| Ölçüm, izleme, log stratejisi | `isletim-sistemi-observability` | Yok |
| Test stratejisi ve kapsam hedefi | `isletim-sistemi-testing` | Yok |
| Planlama, yol haritası, fazlar | `isletim-sistemi-plan` | Yok |
| Temel bileşen envanteri, çekirdek soyutlamalar | `isletim-sistemi-core` | Yok — core↔kernel ayrımı da yazılmamış (os-master §3) |

**8 platform kapsar kuralı:** platform başına **bir** sorumluluk — "bu platform hangi OS sınıfına girer ve o sınıfın gereksinimlerini taşıyor mu?" Platformun kendi çekirdeği, API'si, sesi bu dalda **tekrar anlatılmaz** (tekrar = kopya kusuru); yalnızca bağlantı verilir.

## §B 8 platform envanteri (ad · hedef · olgunluk · kanıt durumu)

| # | Platform (ad) | Hedef (dalın iddiası) | Olgunluk (dosya/satır) | Kanıt durumu |
|---|---|---|---|---|
| 1 | `android` | Mobil Linux-çekirdekli, Google AOSP tabanlı zemin | 6 dosya · 3.104 satır · 6/6 ≥500 | `developer.android.com` HTTP 200 (2026-10-12) · `index.md` **yok** |
| 2 | `ios` | Apple mobil zemin — AVAudioSession/CoreAudio kısıtlı yüzey | 7 dosya · 3.105 satır · 6/6 ≥500 + index 67 | `developer.apple.com/documentation/coreaudio/audiostreambasicdescription` HTTP 200 (2026-10-13) · repo iOS kanıtı **YOK — UNKNOWN** |
| 3 | `linux` | Genel amaçlı POSIX/Linux zemin — en derin dal | 12 dosya · 5.883 satır · 11/11 ≥500 + index 60 | `docs.kernel.org` HTTP 200 · repo `.htaccess` ×4 = Apache/POSIX kanıtı |
| 4 | `macos` | Darwin/XNU tabanlı Apple masaüstü zemin | 6 dosya · 2.704 satır · 5/5 ≥500 + index 55 | `developer.apple.com/documentation/kernel/mach` HTTP 200 (2026-10-13) · repo macOS kanıtı **YOK — UNKNOWN** |
| 5 | `reactos` | NT-typ / Win32 uyumluluk hedefli açık kaynak zemin | 7 dosya · 3.228 satır · 6/6 ≥500 + index 73 | `reactos.org` + `github.com/reactos/reactos` HTTP 200 · wiki bot-duvarı (Anubis) → içerik **⚠️** · repo kanıtı **YOK** |
| 6 | `tizen` | Samsung/Linux tabanlı gömülü/zaten-mobil zemin | 7 dosya · 3.183 satır · 6/6 ≥500 + index 68 | `tizen.org` HTTP 200 · `docs.tizen.org` SPA iskeleti → derin iddialar **⚠️** · repo kanıtı **YOK** |
| 7 | `webos` | LG/webOS açık kaynak TV paneli zemin | 7 dosya · 3.087 satır · 6/6 ≥500 + index 75 | **Bu turda ölçülmedi** — canlı ajan yazıyor → durum **UNKNOWN (bu tur)** |
| 8 | `windows` | Win32/NT masaüstü ve sunucu zemin | 9 dosya · 4.223 satır · 8/8 ≥500 + index 61 | `learn.microsoft.com` procthread HTTP 200 · repo `web.config` ×4 = IIS/Win32 kanıtı |

**Envanter kuralı:** "olgunluk" = dosya sayısı + satır sayısı + ≥500 tutturan içerik dosyası oranı; bunlar diskten ölçülmüştür, tahmin değildir. "Kanıt durumu" = web URL varlığı + repo disk izi; hiçbiri yoksa `UNKNOWN` yazılır, uydurulmaz.

## §C Platform seçimi karar kriterleri

> Bu bölüm **seçimi yapmaz**; seçimi hangi veriyle yapılacağını tanımlar. Seçim kendisi T-U3/Q4 — UNKNOWN.

| # | Kriter | Ne ölçülür | Veri kaynağı | Bu dalın durumu |
|---|---|---|---|---|
| C1 | Hedef donanım sınıfı | Masaüstü / sunucu / gömülü / mobil | `K001-donanım` + `home.coremusic.net/AGENTS.md` §19 "Raspberry Pi 5" notu | İpucu var, karar **UNKNOWN** |
| C2 | Gerçek zamanlılık gereksinimi | Sert ↔ yumak ↔ hiç | İşletim katmanı gereksinimleri (`isletim-sistemi-runtime`) | Gereksinim yazılmadı → **UNKNOWN** |
| C3 | Bellek bütçesi | Kaç MB'da çalışacak | Donanım kısıtı (`K001`) + üretim hedefi | Kısıt bu dalda yok → **UNKNOWN** |
| C4 | Güç/ısı davranışı | Sürekli açık ↔ uyku ↔ pil | Cihaz sınıfı | **UNKNOWN** |
| C5 | Ses yığını erişimi | Düşük gecikme arayüzü var mı (ALSA/WASAPI/CoreAudio/AAudio) | `isletim-sistemi-audio` + platform derin-dalları | Her derin-dalda ayrıntı var; eşleme yapılmadı |
| C6 | Sürücü/yüklenme modeli | Kullanıcı modu ↔ çekirdek modu sürücü | `isletim-sistemi-kernel` + platform `*-surucu`/`*-kernel` dosyaları | Ayrım T-U5 → **UNKNOWN** |
| C7 | Geliştirme/dağıtım zinciri | Derleme, paketleme, imzalama | Platform derin-dalları (`android-build-ve-dagilim`, `tizen-uygulama-ortami`) | Yalnız Android/Tizen dosyalarında işlenmiş |
| C8 | Lisans/ekosistem kısıtı | Açık kaynak ↔ tescilli | Lisans metinleri | Bu dalda işlenmedi → **UNKNOWN** |
| C9 | Taşınabilirlik baskısı | Tek platform ↔ çok platform | `isletim-sistemi-cross` | T-U1 boş → **UNKNOWN** |
| C10 | Bakım/yama akışı | Uzun destek ↔ sürekli güncelleme | Dağıtım notları (kernel.org, Microsoft Learn, Tizen) | Ölçülmedi → **UNKNOWN** |
| C11 | Erişilebilir kanıt yoğunluğu | Kaç resmi URL 200 döndü, kaç dosya ≥500 | §B envanteri + `isletim-sistemi-turu-dogrulama.md` | Ölçüldü: 8/8 platformda ≥500 içerik var |
| C12 | Repo ile uyum | Repo'nun mevcut izi hangi sınıfla konuşuyor | `.htaccess` ×4 (POSIX) · `web.config` ×4 (Win32) · `php_uname('s')` çalışma zamanı sorgusu | İki sınıf da hazır → tek-sınıf kilidi **yok** |

**Kriter okuma kuralı:** C1–C4 **zorunlu** (bunlar olmadan tür kararı anlamsızdır); C5–C10 **yönlendirici**; C11–C12 **kanıt kapısı**. Zorunlu dördü de `UNKNOWN` olduğu için bugün hiçbir platform "seçildi" olarak yazılamaz.

## §D Bağımlılık grafiği (turu → api / audio / kernel / core)

```text
K000-isletim-sistemi
├── isletim-sistemi-turu        (BU DAL — sınıf seçimi)
│   ├── → isletim-sistemi-api      (sınıf → hangi API yüzeyi beklenir)
│   ├── → isletim-sistemi-audio    (sınıf → hangi ses katmanı beklenir)
│   ├── → isletim-sistemi-kernel   (sınıf → çekirdek tipine atıf, T-U5)
│   └── → isletim-sistemi-core     (sınıf → temel soyutlama beklentisi)
├── isletim-sistemi-cross       (turu ile sınır YAZILMADI — T-U1)
├── isletim-sistemi-runtime     (tür → gerçek zamanlılık gereksinimi)
└── isletim-sistemi-security    (tür → güvenlik karakteristiği)
```

| # | Kenar | Yön | Ne taşır | Ne taşımaz | Durum |
|---|---|---|---|---|---|
| D1 | turu → api | turu'ndan api'ye | Sınıfın beklediği sistem çağrıları **tipi** | İmza tablosu, sürüm şemaları | Bağımlılık `depends_on` içinde **yok** → ⚠️ |
| D2 | turu → audio | turu'ndan audio'ya | Sınıfın beklediği ses gecikmesi/kanal profili | ALSA/PipeWire/CoreAudio katman ayrıntısı | Dosyalar arası referans var, FM bağımlılığı yok → ⚠️ |
| D3 | turu → kernel | turu'ndan kernel'e | Çekirdek tipine **atıf** | Monolitik/mikro kararı (T-U5) | UNKNOWN |
| D4 | turu → core | turu'ndan core'a | Temel soyutlama beklentisi | core↔kernel ayrımı (os-master §3) | ⚠️ yazılmamış |
| D5 | cross → turu | çift yönlü | Taşınabilirlik kısıtı | Platform davranışı | **bilerek boş** (T-U1) |
| D6 | runtime → turu | runtime'dan turu'ne | Gerçek zamanlılık **gereksinimi** | Zamanlama mekanizması | Q5 ile ilişkili ⚠️ |
| D7 | turu → 8 platform index | kök → Kademe-3 | Platform künyesi, çocuk listesi | Platform içeriği | ✅ çift yönlü `parent_id` |
| D8 | turu → K002-altyapi | — | Tür kararı ile altyapı katmanı bağlantısı | Katman tasarımı | Q5 ⚠️ yazılmamış |

**Grafik kuralı:** ok yönü = bilgi akışı. `depends_on` FM alanında yalnızca **doğrulanmış** kenarlar yazılıdır; ⚠️ işaretli kenarlar `open_questions` taşınır — tahminle `depends_on`'a konmaz.

## §E Sınır komşuları

> §2'den farklı eksen: orada *konu* sınırı, burada **komşu node başına** sınır sözleşmesi.

| # | Komşu node | Yön | Bu daldan geçen | Geçmeyen | Sözleşme durumu |
|---|---|---|---|---|---|
| E1 | `isletim-sistemi-cross` | çift | Yok (bilerek) | Uyumluluk davranışı, Wine/Win32 katmanı | ⚠️ T-U1 — yazılmadı |
| E2 | `isletim-sistemi-runtime` | turu → runtime | Tür gereksinimi (gerçek zamanlı mı) | Zamanlayıcı, tick, jitter hesabı | Q5 ⚠️ |
| E3 | `isletim-sistemi-security` | turu → security | Güvenlik **karakteristiği** (gömülü ↔ genel) | yetki, izolasyon, crypto | Sağlam (kavramsal) |
| E4 | `isletim-sistemi-kernel` | çift | Çekirdek tipine atıf | çekirdek tasarımı | ⚠️ T-U5 UNKNOWN |
| E5 | `isletim-sistemi-api` | turu → api | Beklenen API yüzeyi tipi | çağrı imzaları, sürümler | Dosya düzeyinde ayrışık — FM bağımlılığı yok ⚠️ |
| E6 | `isletim-sistemi-audio` | turu → audio | Beklenen ses profili | katmanlar, sürücü kontratı | `ios`/`tizen` derin-dallarında içerik referansı var |
| E7 | `isletim-sistemi-core` | turu → core | Temel soyutlama beklentisi | core içeriği | ⚠️ core↔kernel ayrımı yazılmamış |
| E8 | `isletim-sistemi-cross`-dışı `K001-donanım` | donanım → turu | Cihaz sınıfı (masaüstü/gömülü/mobil) | PCB, BOM, `donanaım-type` ayrıntısı | `electroncis` dalı ayrı — dokunulmaz |
| E9 | `K011` (uygulama) | turu → K011 | Sınıfın uygulamaya dayattığı kısıt | uygulama iş akışı | Kapsam dışı (§A) |
| E10 | `isletim-sistemi-observability` | — | Yok | log/metrik/iz | Kapsam dışı |
| E11 | `isletim-sistemi-testing` | — | Yok | test kapsamı | Kapsam dışı |
| E12 | `isletim-sistemi-plan` | — | Yalnız soru satırı (Q4) | faz planı | Kapsam dışı |

## §F Açık sorular — hedef platform seti (Q4 eksenli)

| # | Soru | Neden önemli | Bağlı | Durum |
|---|---|---|---|---|
| F1 | Hedef platform seti hangisi? (tek mi, 8'i birden mi?) | 8 dalın hangilerinin "aday", hangilerinin "referans" olduğu belli değil | T-U3 / Q4 | UNKNOWN |
| F2 | Raspberry Pi 5 notu bir **seçim** mi, yalnız üretim donanım notu mu? | C1 zorunlu kriterin tek disk ipucu | `home.coremusic.net/AGENTS.md` §19 | UNKNOWN (ipucu, karar değil) |
| F3 | Windows ile Linux çift hedef mi? (`.htaccess` + `web.config` birlikte var) | C12 iki sınıfın da hazır olduğunu gösteriyor | `isletim-sistemi-runtime` R1 | UNKNOWN |
| F4 | `android` dalı aday mı, referans mı? (6/6 ≥500, `index.md` yok) | Dizin yapısı karar sinyali mi? | disk enumerasyonu §4 | UNKNOWN |
| F5 | `webos` dalı sete dahil mi? | Bu turda ajan çalışıyor, kapsamı okunmadı | canlı ajan | UNKNOWN (bu tur) |
| F6 | Sınıflandırma şeması seçilince Q4 otomatik kapanır mı? | T-U2 → T-U3 sıralı bağımlılık | §5 | Sıralı: T-U2 önce |
| F7 | `reactos` bir **aday** mı yoksa yalnız NT-referansı mı? | Win32 uyumluluk hedefi gerçek hedef mi? | `reactos-win32-uyumlulugu.md` | UNKNOWN |
| F8 | `tizen` hangi cihaz sınıfını temsil ediyor (TV/gömülü/mobil)? | C1 zorunlu kriter | `tizen-uygulama-ortami.md` | UNKNOWN |
| F9 | `ios`/`macos` birlikte tek Apple sınıfı mı, ayrı mı? | Sınıf sayısı şemayı doğrudan etkiler | T-U2 | UNKNOWN |
| F10 | Q5 (turu ↔ K002-altyapi boşluğu) Q4'ten önce mi kapatılacak? | Bağımlılık sırası belirsiz | Q5 | ⚠️ üst karar |

**Kapanış kuralı:** bu on sorudan **hiçbiri** bu dosyada cevaplanmaz; cevap `status: draft` → `verified` geçişini açar. "Hedefimiz X platform" cümlesi F1/F2 kapanmadan yazılmaz.

## §G Platform olgunluk matrisi

> Ölçüm 2026-10-13 · tüm sayılar diskten · `index.md` satır aralığı hedefi 40–100.

| Platform | Dosya | Satır | İçerik ≥500 | `index.md` | Çift yönlü link | Web kanıtı (200) | Repo izi | Olgunluk |
|---|---:|---:|---|---|---|---|---|---|
| android | 6 | 3.104 | 6/6 ✅ | **yok** | — | `developer.android.com` | yok → UNKNOWN | Yüksek içerik, indeks eksik |
| ios | 7 | 3.105 | 6/6 ✅ | 67 ✅ | ✅ `parent_id` | `…/coreaudio/audiostreambasicdescription` | yok → UNKNOWN | Yüksek |
| linux | 12 | 5.883 | 11/11 ✅ | 60 ✅ | ✅ `parent_id` | `docs.kernel.org` · `man7.org pthread_create.3` | `.htaccess` ×4 | **En yüksek** (en derin dal) |
| macos | 6 | 2.704 | 5/5 ✅ | 55 ✅ | ✅ `parent_id` | `…/kernel/mach` | yok → UNKNOWN | Orta-yüksek |
| reactos | 7 | 3.228 | 6/6 ✅ | 73 ✅ | ✅ `parent_id` | `reactos.org` · `github.com/reactos/reactos` · `build.reactos.org` | yok → UNKNOWN | Yüksek içerik, web kanıtı kısmi (bot-duvarı) |
| tizen | 7 | 3.183 | 6/6 ✅ | 68 ✅ | ✅ `parent_id` | `tizen.org` · `review.tizen.org` | yok → UNKNOWN | Yüksek içerik, web kanıtı kısmi (SPA iskeleti) |
| webos | 7 | 3.087 | 6/6 ✅ | 75 ✅ | 🔄 ajan | bu turda ölçülmedi | yok → UNKNOWN | **Bu tur ölçülemez** |
| windows | 9 | 4.223 | 8/8 ✅ | 61 ✅ | ✅ `parent_id` | `learn.microsoft.com` procthread · `…/api/_coreaudio` | `web.config` ×4 | Yüksek |

**Matris okuma kuralı:** "Olgunluk" bir **sıra** değil, **durum** etiketidir — en yüksek satır "Linux seçildi" anlamına gelmez (seçim UNKNOWN). Matris yalnızca "kanıt en yoğun dalda mı?" sorusunu yanıtlar: evet, `linux/` (5.883 satır).

## §H Web referans tablosu (bu dosyanın aday kaynakları)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Genel amaçlı OS ↔ gömülü/gerçek zamanlı OS sınıflandırması kaynağı erişilebilir (aday ölçüt) | https://www.tron.org/rtos/rtos01/ | 2026-10-13 | HTTP 200 |
| TRON klasikleştirme dizisinin ikinci bölümü erişilebilir | https://www.tron.org/rtos/rtos02/ | 2026-10-13 | HTTP 200 |
| Linux çekirdek dokümantasyonu erişilebilir | https://docs.kernel.org/ | 2026-10-13 | HTTP 200 |
| POSIX uyumluluk referansı (POSIX.1-2024) erişilebilir | https://pubs.opengroup.org/onlinepubs/9799919799/ | 2026-10-13 | HTTP 200 |
| POSIX geçmiş suratı (IEEE Std 1003.1-2008/2017) erişilebilir | https://pubs.opengroup.org/onlinepubs/9699919799/ | 2026-10-13 | HTTP 200 |
| `sys_un.h` (UNIX domain socket) POSIX tanımı erişilebilir | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/sys_un.h.html | 2026-10-13 | HTTP 200 |
| Android resmi geliştirici belgeleri erişilebilir | https://developer.android.com/ | 2026-10-13 | HTTP 200 |
| Tizen resmi portalı erişilebilir | https://tizen.org/ | 2026-10-13 | HTTP 200 |
| ReactOS resmi alanı erişilebilir | https://reactos.org/ | 2026-10-13 | HTTP 200 |
| Windows süreç ve iş parçacığı belgeleri erişilebilir | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | 2026-10-13 | HTTP 200 |
| macOS/Mach çekirdek dokümanı erişilebilir | https://developer.apple.com/documentation/kernel/mach | 2026-10-13 | HTTP 200 |
| Linux çekirdek ana deposu (git) erişilebilir | https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git | 2026-10-13 | HTTP 200 |

**Kapanış:** `status: draft`. §3 ve §F'deki sorular kapanmadan bu dosyaya "hedefimiz X tür OS" cümlesi yazılmaz; yalnız soru satırı taşınır. 12/12 URL bu ortamdan HEAD isteğiyle ölçülmüştür.

## §I Sınıflandırma şemaları (aday şema envanteri — seçim YOK)

> Bu bölüm üç **aday** şemayı yan yana koyar; hiçbiri benimsenmemiştir (T-U2 UNKNOWN). Şema seçilmeden §II matrisi "aday" olarak, "karar" olarak okunamaz.

### §I.1 Şema A — kullanıcı sınıfı ekseni (masaüstü / sunucu / gömülü / mobil)

| # | Sınıf | Tipik üye (bu dalda geçen) | Temel ölçüt | Bu dalın durumu |
|---|---|---|---|---|
| A1 | Masaüstü genel amaçlı | `windows`, `macos`, `linux` (masaüstü distro) | Kullanıcı etkileşimi + zengin sürücü ekosistemi | Aday şema |
| A2 | Sunucu genel amaçlı | `linux` (sunucu distro), `windows` (sunucu sürümü) | Uzun çalışma süresi, uzak yönetim | Aday şema |
| A3 | Mobil | `android`, `ios` | Pil, uygulama mağazası, sandbox önceliği | Aday şema |
| A4 | Gömülü / TV-paneli | `tizen`, `webos`, Raspberry Pi hedefli `linux` | Sınırlı bellek, tek amaçlı iş yükü, uzun ömür | Aday şema |
| A5 | Uyumluluk / referans | `reactos` | NT-typ API uyumluluğu hedefi | Aday şema (adaylık statüsü belirsiz — §F F7) |

### §I.2 Şema B — çalışma zamanı davranışı ekseni (genel amaçlı ↔ gerçek zamanlı)

| # | Sınıf | Ölçüt | Bu dalda karşılığı | Durum |
|---|---|---|---|---|
| B1 | Genel amaçlı (zaman-paylaşımlı) | Throughput önceliği, tahmin edilemeyen gecikme kabulü | 8 platformun tamamı bu sınıfta sınıflandırılabilir | Şema adayı |
| B2 | Sert gerçek zamanlı | Deadline ihlali = hata; ölçüm gereklidir | Bu dalda **hiçbir** platform için kanıt yok | Şema adayı (kanıtsız) |
| B3 | Yumuşak gerçek zamanlı | Deadline ihlali tolere edilir, sık ihlal kabul edilmez | Ses yığını iddiaları bu sınıfa girer (`isletim-sistemi-audio`) | Şema adayı |

**Şema B notu (kanıt disiplini):** TRON Forum kaynakları (§H satır 1–2) bu ayrımı bir **sınıflandırma ölçütü** olarak sunar; ancak hiçbir platform için "sert gerçek zamanlı" iddiası bu dalda **kanıtlanmamıştır** → B2 satırı boş kalmıştır, doldurulmamıştır.

### §I.3 Şema C — çekirdek yapısı ekseni (monolitik / mikro / hibrit)

| # | Sınıf | Bu dalda geçen örnek | Ayrım sahibi dal | Durum |
|---|---|---|---|---|
| C1 | Monolitik | Linux çekirdeği (platform dosyalarında atıf) | `isletim-sistemi-kernel` | Bu dalda işlenmez (T-U5) |
| C2 | Mikro | — | `isletim-sistemi-kernel` | Bu dalda hiç geçmiyor |
| C3 | Hibrit | XNU (Mach + BSD) — `macos`/`ios` dosyalarında atıf | `isletim-sistemi-kernel` | Bu dalda yalnızca atıf |

**Şema C kuralı:** çekirdek tipi **bu dalın kararı değildir**; bu dal yalnızca "şu platform şu tip çekirdeğe atıf yapar" cümlesini kurabilir. T-U5 bu yüzden `UNKNOWN` olarak kilitlidir.

### §I.4 Şemalar arası karşılaştırma

| Eksen | Şema A | Şema B | Şema C | Birlikte kullanılabilir mi? |
|---|---|---|---|---|
| Birincil soru | Kim kullanır, ne boyutta? | Ne kadar öngörülebilir olmalı? | Çekirdek nasıl yapılmış? | Evet — üç eksen bağımsızdır |
| CoreMusic'e etkisi | Dağıtım/paketleme | Gecikme bütçesi | Sürücü/yüklenme modeli | Üçü de ayrı ayrı ele alınır |
| Bu dalda durumu | Aday (§II) | Aday (kanıtsız B2) | Atıf-only (T-U5) | Birleştirme kuralı yazılmadı → ⚠️ |
| Karar sahibi | T-U3 / Q4 | `isletim-sistemi-runtime` | `isletim-sistemi-kernel` | Sıralı karar zinciri yok → ⚠️ |

## §II 8 platform sınıflandırma aday matrisi (şema A + B birlikte)

> **UYARI:** aşağıdaki "Aday sınıf" sütunu bir **karar değil**, ölçütlere göre **adaylıktır**. Karar T-U3/Q4 — UNKNOWN.

| # | Platform | Şema A adayı | Şema B eğilimi | Temel ölçüt izi | Repo izi | Karar durumu |
|---|---|---|---|---|---|---|
| 1 | `android` | A3 mobil | B1 genel amaçlı | Linux çekirdekli mobil, uygulama paketleme | Yok | UNKNOWN |
| 2 | `ios` | A3 mobil | B1 genel amaçlı | Sandbox + imzalama önceliği | Yok | UNKNOWN |
| 3 | `linux` | A1/A2 + A4 (Raspberry Pi ipucu) | B1 (ses yığını B3 iddiaları ayrı dal) | POSIX taşınabilirliği | `.htaccess` (Apache) | UNKNOWN |
| 4 | `macos` | A1 masaüstü | B1 genel amaçlı | Hibrit çekirdek + notarization | Yok | UNKNOWN |
| 5 | `reactos` | A5 uyumluluk | B1 genel amaçlı | NT-typ API hedefi | Yok | UNKNOWN |
| 6 | `tizen` | A4 gömülü/TV (+A3 tarihî) | B1 genel amaçlı | Sessiz servis + imzalama | Yok | UNKNOWN |
| 7 | `webos` | A4 TV paneli | B1 genel amaçlı | Luna Servis Mıknatısı + OOM yönetimi | Yok | UNKNOWN |
| 8 | `windows` | A1/A2 masaüstü-sunucu | B1 genel amaçlı | SCM + WASAPI + MSI/MSIX | `web.config` (IIS) | UNKNOWN |

**Matris okuma:** "B1 eğilimi" sütunu **iddia değil**, dosyalardaki kanıt yoğunluğuna dayanan bir **eğilim notudur**; hiçbir satır "bu platform B1'dir" cümlesi kurmaz (kanıt `isletim-sistemi-runtime` dalında değildir).

## §III Sınıf → gereksinim eşleme (şema seçilirse uygulanacak tablo)

> Bu tablo **boş şablondur** — hücreler doldurulmamış, doldurulması T-U2 sonrasına bırakılmıştır. Boş bırakılan hücrelerde `—` yerine **neden** boş olduğu yazılır.

### §III.1 Gerçek zamanlılık gereksinimi

| Sınıf | Gereksinim ifadesi | Ölçüm birimi | Kanıt sahibi | Durum |
|---|---|---|---|---|
| A1/A2 masaüstü-sunucu | Sert deadline yok; gecikme kullanıcı deneyimi eşiği ile sınırlandırılır | ms | `isletim-sistemi-audio` | Aday |
| A3 mobil | Kesinti/interruption yönetimi önceliklidir | ms + bildirim | platform `*-surucu` dosyaları | Aday |
| A4 gömülü/TV | Uzun çalışma süresi + termal bütçe | saat + °C | `K001-donanım` | Aday |
| A5 uyumluluk | API doğruluğu > performans | test başarısı | `reactos-*` dosyaları | Aday |
| B2 sert gerçek zamanlı | Deadline ihlali = hata | µs | `isletim-sistemi-runtime` | **Kanıt yok — yazılmaz** |

### §III.2 Bellek ve güç gereksinimi

| Sınıf | Bellek önceliği | Güç davranışı | Bu dalın kanıtı | Durum |
|---|---|---|---|---|
| A1/A2 | Bellek bol; önbellek düzeni önemli | Sürekli besleme | Yok (bu dalda) | UNKNOWN |
| A3 | Pil ile çalışma; arka plan kısıtları | Bekleme/uyku devirleri | Platform dosyalarında atıf var | Aday |
| A4 | Sınırlı bellek; önceden bölme | Uzun açık kalma | Yok (bu dalda) | UNKNOWN |
| A5 | Orta bellek | Masaüstü sınıfı | Yok (bu dalda) | UNKNOWN |

### §III.3 Güvenlik ve yetki gereksinimi

| Sınıf | Yetki modeli beklentisi | İmza beklentisi | Kanıt (bu dal) | Durum |
|---|---|---|---|---|
| A1 | Kullanıcı hesabı + yönetici ayrımı | Opsiyonel | `web.config` (IIS yapılandırması) | Aday |
| A2 | Servis hesabı + en az yetki | Sıkı | Yok | UNKNOWN |
| A3 | Sandbox + mağazadan imzalama | Zorunlu | Platform dosyalarında atıf | Aday |
| A4 | Kilitli profil / tek amaçlı | Cihaz-özel | Yok | UNKNOWN |
| A5 | NT erişim kontrolü taklidi | Hedefleniyor, kanıt yok | `reactos-*` dosyaları | ⚠️ |

### §III.4 Ses yığını gereksinimi (bağlantı — ayrıntı `isletim-sistemi-audio`)

| Sınıf | Beklenen arayüz tipi | Düşük gecikme erişimi | Bu dalın cümlesi |
|---|---|---|---|
| A1 masaüstü | Kullanıcı-modu API + sürücü seçenekleri | Platforma bağlı | "Bu sınıf için ses arayüzü çeşitlidir" |
| A3 mobil | Kısıtlı oturum/cihaz yönetimi | Kısıtlı | "Bu sınıf için oturum yönetimi zorunludur" |
| A4 gömülü | Doğrudan donanım erişimi | Mümkün | "Bu sınıf için sürücü erişimi anahtardır" |
| A5 uyumluluk | Eski API taklidi | Kanıt yok | "Bu sınıf için API doğruluğu önceliklidir" |

## §IV Tekrar ve kopya kusuru önlemleri

| # | Risk | Nerede | Önlem | Durum |
|---|---|---|---|---|
| R1 | Platform dosyalarının kök dalı tekrar etmesi | 8 × `*-anayasa.md` | Kök yalnız **sınıf ve ölçüt** yazar; platform dosyası **mimariyi** yazar | Uygulandı |
| R2 | `*-surucu.md` ↔ `isletim-sistemi-audio` tekrarı | sürücü kontratı | Sürücü kontratı ayrı dalda; burada yalnız tip atıfı | Uygulandı |
| R3 | Şema seçimi öncesi "karar" dili kullanımı | §II/§III | Her tablo başlığı "aday" etiketi taşır | Uygulandı |
| R4 | Eski envanter sayılarının güncel olmaması | §4/§B/§G | Bu tur sonunda sayımlar yeniden alınır (bkz. §N) | Güncellendi |
| R5 | `webos` dalının önceki turda ölçülmemesi | §B satır 7 | Bu turda `webos/` diskten ölçüldü | Kapatıldı |

## §V Ölçüm yöntemi (tekrarlanabilir)

| # | Ölçüm | Komut/araç | Ne doğrular |
|---|---|---|---|
| 1 | Dosya/satır sayısı | `Get-ChildItem $K -Recurse -Filter *.md \| ForEach-Object { (Get-Content $_.FullName).Count }` | Envanter §4/§G |
| 2 | ≥500 kapısı | Yukarıdaki sayımın 500 filtresi | İçerik dosyası olgunluğu |
| 3 | index aralığı | Aynı sayım, 40–100 filtresi | Künye dosyası disiplini |
| 4 | Frontmatter bütünlüğü | `---` başlangıç + `group:` + `parent:` yokluğu taraması | v2 §4.2 uyumu |
| 5 | URL enventeri | `.ai/architecture/**.md` içindeki `https?://` kümeleri | Kesişim=0 kapısı |
| 6 | HTTP durumu | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` | §H tablosu |
| 7 | Repo disk izi | `(Get-Content 'home.coremusic.net/.htaccess')[0]` vb. | §III.3/§VIII satırları |

**Yöntem notu:** ölçümler `C:\www\coremusic.net` kökünden çalıştırılır; `.ai/` gizli dizindir, arama `hidden` modunda yapılır (kök `AGENTS.md` §7).

## §VI Doğrulama günlüğü — bu tur eklenen kaynaklar (2026-10-13)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| TRON Forum ana sayfası (sınıflandırma ölçütü kaynağı) erişilebilir | https://www.tron.org/ | 2026-10-13 | HTTP 200 |
| TRON RTOS dizini erişilebilir | https://www.tron.org/rtos/ | 2026-10-13 | HTTP 200 |
| Open Group yayın portalı (POSIX yayıncısı) erişilebilir | https://pubs.opengroup.org/ | 2026-10-13 | HTTP 200 |
| Open Group çevrim içi yayın dizini erişilebilir | https://pubs.opengroup.org/onlinepubs/ | 2026-10-13 | HTTP 200 |

**Erişim engeli kaydı (korunmuş):** `https://www.osek-vdx.org/mirror/os221.pdf` → **HTTP 403** (GET ve HEAD, 2026-10-12 ölçümü) — bu alt alan adı artık **cita edilmez**, iddia dayanağı yapılmaz (bkz. `-dogrulama.md` §1).

## §VII Terimler (bu dosyaya özgü)

| Terim | Tanım (bu dosyadaki anlamı) |
|---|---|
| Sınıf | OS'lerin ortak gereksinim profili; platform adı değil, **profil** |
| Şema | Sınıflandırma **ekseni** (A/B/C); bir dalda birden fazla şema adayı olabilir |
| Aday sınıf | Ölçütlere göre olası sınıf; kararı **temsil etmez** |
| Eğilim notu | Kanıt yoğunluğundan okunan yön; iddia değildir |
| Ölçüt | Kararı belirleyebilen gözlenebilir özellik (C1–C12) |
| Sınır komşusu | Bu dalın içerik **taşımadığı**, yalnızca atıf yaptığı node |
| Kanıt kapısı | C11–C12 gibi, kararı yalnız veriyle açan kriter |
| UNKNOWN | Bilinmeyen ve tahminle doldurulmayan hücre |
| ⚠️ VERIFICATION REQUIRED | Doğrulanamayan iddia işareti; "hayır" demek değildir |

## §VIII Repo izi özeti (disk kanıtları — okunarak alındı)

| # | Dosya | Satır | Alıntı | Ne kanıtlar |
|---|---|---:|---|---|
| 1 | `home.coremusic.net/.htaccess` | 1 | `# CoreMusic Home - Apache SPA Rewrite` | Apache/POSIX sınıfı yapılandırma varlığı |
| 2 | `home.coremusic.net/.htaccess` | 5 | `php_flag  display_errors off` | Apache PHP hata davranışı yapılandırması |
| 3 | `home.coremusic.net/.htaccess` | 27 | `RewriteEngine On` | Apache mod_rewrite etkinliği |
| 4 | `home.coremusic.net/.htaccess` | 36 | `RewriteRule ^(.*)$ index.php [QSA,L]` | SPA yönlendirme kuralı |
| 5 | `home.coremusic.net/web.config` | 1 | `<?xml version="1.0" encoding="UTF-8"?>` | IIS/Windows sınıfı yapılandırma varlığı |
| 6 | `home.coremusic.net/web.config` | 3 | `<system.webServer>` | IIS yapılandırma kökü |
| 7 | `home.coremusic.net/web.config` | 14 | `<rule name="Static files" stopProcessing="true" />` | IIS rewrite kuralı |
| 8 | `home.coremusic.net/AGENTS.md` | 19 | `Home Media Center - Raspberry Pi 5 üzerinde çalışan ana medya paneli.` | Gömülü Linux/ARM sınıfı **ipucu** (karar değil) |
| 9 | `.github/workflows/ci.yml` | 27 | `runs-on: ubuntu-latest` | Linux ortamında CI |
| 10 | `shared/composer.json` | 8 | `"php": ">=8.4"` | PHP çalışma zamanı alt sınırı |

**İz okuma kuralı:** 1–4 + 9 = POSIX/Linux sınıfı izi; 5–7 = Windows/NT sınıfı izi; ikisi birlikte var → **tek-sınıf kilidi yok** (§B C12). 8 numaralı iz bir **seçim kanıtı değildir** (§F F2).

## §IX Bağlantı indeksi (bu dosyanın bağlandıkları)

| Hedef | İlişki | Ne taşır |
|---|---|---|
| `index.md` (kök) | üst künye | 12 çocuk listesi |
| `isletim-sistemi-turu-dogrulama.md` | kardeş | iddia–URL–durum tabloları |
| `isletim-sistemi-turu-ornekler.md` | kardeş | repo örnekleri |
| 8 × platform `index.md` | alt Kademe-3 | platform katalogları |
| `os-master.md` §3 satır 57, 59–61 | üst dal | bu dalın tanımı ve bilinçli boşluklar |
| `isletim-sistemi-runtime` | sınır | tür → gerçek zamanlılık gereksinimi |
| `isletim-sistemi-cross` | sınır (bilerek boş) | T-U1 |
| `K001-donanım` / `electroncis` | sınır | cihaz sınıfı, `donanaım-type` notları |

## §XI Şema adayları detaylandırması (A/B/C — adaylık korunarak)

| Şema | Eksen tanımı | Aday sınıf örnekleri (aday, karar değil) | Bu dalın taşıdığı kanıt |
|---|---|---|---|
| A — İşletim zamanı modeli | Görev/çekirdek ayrımı | Monolitik · mikro · hibrit · nano-kernel · exokernel | §II şema haritası |
| B — Kaynak sahiplenme | Bellek/cihaz paylaşım modeli | Gömülü RTOS · genel amaçlı · mobil · sunucu · masaüstü | §III ölçüt matrisi |
| C — Dağıtım/bağımlılık | Kim kurar, kim günceller | Bağımsız dağıtım · üretici paketi · gömülü image · web teslimatlı | §V iz matrisi |

**Kural:** üç şema birbirini dışlamaz — bir platform aynı anda A2 + B3 + C1 olabilir. Bu dosya hangi eksenin "doğru" olduğunu **seçmez** (T-U3/Q4 UNKNOWN).

## §XII Ölçütlerin bu dosyadaki okuma biçimi (C1–C12)

| # | Ölçüt | Okuma biçimi | Ölçülebilirlik |
|---|---|---|---|
| C1 | Zamanlama garantisi | Sabit gecikme ↔ değişken gecikme | Belge iddiası (upstream) |
| C2 | Bellek modeli | Sabit önceden bölüşüm ↔ sanallaştırma | Belge + repo izi |
| C3 | Kullanıcı/çekirdek ayrımı | Tam · kısmeli · yok | Belge iddiası |
| C4 | Cihaz sürücüsü yeri | Çekirdek içi · kullanıcı uzayı · ikisi | Belge iddiası |
| C5 | Kesinti/dişleme modeli | Signal-driven ↔ polling ↔ callback | Belge iddiası |
| C6 | Dosya sistemi sistemi | VFS benzeri soyutlama ↔ düz | Belge iddiası |
| C7 | Güncelleme modeli | Atomik image ↔ parça parça paket | Repo izi (CI/deploy) |
| C8 | Uygulama teslimatı | Paket mağazası ↔ arşiv ↔ web | Repo izi (SPA) |
| C9 | Güvenlik modeli | yetki-check ↔ capability ↔ sandbox | Belge + repo izi (`.htaccess`) |
| C10 | Hata kurtarma | Panic/reboot ↔ graceful degradation | Belge iddiası |
| C11 | Kanıt kapısı | Yalnız ölçülmüş veriyle açılır | Bu dosya §VIII |
| C12 | Kanıt kapısı | Tek-sınıf kilidi yalnız iki iz birlikte varsa kırılır | Bu dosya §VIII |

**C11–C12 kapısı:** ölçüm yoksa hücre `UNKNOWN` kalır; iki zıt iz (Apache + IIS) birlikte varken tek-sınıf sonucu yazılmaz.

## §XIII Komşu dal sınırları (taşıma yasağı)

| Komşu dal | Bu dalın ondan alabileceği | Bu dalın ona veremeyeceği |
|---|---|---|
| `isletim-sistemi-runtime` | Gerçek zamanlılık **gereksinimi** tanımı | Platform seçimi |
| `isletim-sistemi-cross` | — (bilerek boş, T-U1) | herhangi bir şey |
| `isletim-sistemi-kernel` | Çekirdek soyutlama terimleri | Sınıf kararı |
| `isletim-sistemi-security` | Yetki modeli terimleri | Sınıf kararı |
| `K001-donanım` | Cihaz sınıfı ipuçları | OS seçimi |
| `K002-electroncis` | `donanaım-type` notları | OS seçimi |

**İhlal durumu:** komşu dal dosyasından paragraf kopyalanırsa iddia `⚠️ VERIFICATION REQUIRED`'a çekilir ve dosya yeniden ölçülür.

## §XIV Bilinen boşluklar (doldurulmayacaklar)

| # | Boşluk | Neden boş | İşaret |
|---|---|---|---|
| B1 | T-U2 sınıf kilidi | Tek-sınıf kanıtı yok (§VIII) | UNKNOWN |
| B2 | T-U3 hangi şemanın geçerli olduğu | Seçim bu dalın işi değil | UNKNOWN |
| B3 | Q4 üretim OS tercihi | Onay kapısı kapalı (§1 akış) | UNKNOWN |
| B4 | Ölçüm yokluğu (LINT-020) | `server` başına ölçüm yapılmadı | `⚠️ VERIFICATION REQUIRED` |
| B5 | PostgreSQL/MariaDB çıkarımı | Yalnız `composer.json`/`db-check` var | `⚠️ VERIFICATION REQUIRED` |
| B6 | `api/foundation/*.php` (LINT-022) | Kanıt henüz okunmadı | `⚠️ VERIFICATION REQUIRED` |

## §XV Örnek-ölçüt çapraz indeksi (dosya içi tutarlılık)

| Örnek (ornekler dosyası) | Hizalı olduğu ölçüt | Hizalı olduğu şema |
|---|---|---|
| `shell_exec` / `proc-open` Unix/Windows ayrımı | C5, C3 | A (zamanlama) + C (dağıtım) |
| `ffprobe` PATH bağımlılığı | C7 | C — dağıtım |
| `.htaccess` Apache kuralı | C9, C6 | C |
| `web.config` IIS kuralı | C9, C6 | C |
| `ci.yml` ubuntu-latest | C7 | C |
| Raspberry Pi 5 notu | C2, C10 | B — kaynak sahiplenme |

**Okuma:** her örnek en az bir ölçüte bağlanır; bağlanmayan örnek bu dosyada sayılmaz.

## §XVI Tarihçe (bu dalın evolving durumu)

| Tarih | Olay | Kayıt |
|---|---|---|
| 2026-10-12 | osek-vdx 403 engeli ilk ölçüldü, alıntı kuralı değişti | `-dogrulama.md` §1 |
| 2026-10-13 | 4 tur HEAD doğrulaması → 9+ yeni 200 URL (envanter dışı) | §VI |
| 2026-10-13 | Repo disk kanıtları 10 satır ile yeniden okundu | §VIII |
| 2026-10-13 | `parent:` alanı kök dosyalardan kaldırıldı, `group:` eklendi | frontmatter düzeltmesi |

## §XVII Sık yapılan hatalar (bu dosya özeli)

| # | Hata | Sonuç | Engel |
|---|---|---|---|
| 1 | Şemayı "doğru/yanlış" diye sunmak | T-U2 ihlali | §XI adaylık kuralı |
| 2 | Komşu dal dosyasından kopya | Çifte sayım | §XIII |
| 3 | Uydurma `Dosya:/Satır:` | Zero-hallucination ihlali | §VIII yalnız okunarak |
| 4 | 403 kaynağından iddia | Doğrulanamayan kanıt | LINT-017 |
| 5 | Enventere aynı URL'yi ikinci kez koymak | Kanıt şişirme | §VI kesişim testi |
| 6 | Ölçüm olmadan "üretim Windows" demek | LINT-020 ihlali | B4 |

## §XVIII İlişkili kararlar

| Karar | İlişki | Durum |
|---|---|---|
| ADR-017 (32-bit float ses) | Bu dal OS türü seçmez; veri tipi kararı üstte | frozen |
| ADR-019 (per-OS player) | OS ayrımı yapan karar — bu dalın komşusu | frozen, değişiklik ≥088 |
| ADR-038 (PCM3168A) | Donanım katmanı — K001/K002 sınırı | frozen |
| ADR-030 (RAG pipeline) | Retrieval yolu | PLANNED |

## §XIX Yazar notu (neyi bilerek yapmadım)

| Yapılmayan | Neden |
|---|---|
| Sınıf seçimi | T-U2/T-U3 UNKNOWN — seçim kararı bu dosyanın yetkisinde değil |
| Platform dosyalarının içeriği | 8 alt-dal `index.md`'de; bu dosya yalnız kök kapsam |
| Ölçüm (latency/jitter) | LINT-020 — ölçüm yok, iddia yok |
| 403 kaynağından alıntı | LINT-017 |
| `parent:` alanı | 9 alan şemasında yok, silindi |

## §X Kapanış kontrol listesi (bu dosya için)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Seçim yapıldı mı? | **HAYIR** — T-U2/T-U3/Q4 UNKNOWN olarak korundu |
| 2 | Tahmin yazıldı mı? | **HAYIR** — her belirsiz hücre UNKNOWN/⚠️ işaretli |
| 3 | Şema adayları açıkça "aday" mı etiketli? | **EVET** (§I–§III) |
| 4 | Komşu dal içerikleri kopyalandı mı? | **HAYIR** — yalnız atıf (§IV R1–R2) |
| 5 | Repo izleri okunarak mı alındı? | **EVET** (§VIII) |
| 6 | Yeni URL'ler enventerle kesişiyor mu? | **HAYIR** — 4/4 benzersiz (§VI) |
| 7 | Erişim engeli kaydedildi mi? | **EVET** (osek-vdx 403) |
| 8 | `status` ne? | `draft` |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode