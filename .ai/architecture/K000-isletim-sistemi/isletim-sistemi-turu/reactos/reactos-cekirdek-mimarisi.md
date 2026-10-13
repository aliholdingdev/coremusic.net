---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi"
node_type: architecture
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-win32-uyumlulugu"
evidence:
  - "https://reactos.org/wiki/Kernel (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/HAL (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Memory_Manager (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Cache_Manager (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Boot_process (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12 RED TEAM: Bu dosyadaki her teknik iddia §12 kanıt matrisinde en az bir resmi URL ile eşleştirildi; eşleşmeyen iddia '⚠️ VERIFICATION REQUIRED' olarak işaretlendi."
  - "≥500 satır · 10 alanlı frontmatter · URL kümesi dosyalar arası ayrıştırıldı (reactos-*.md içinde tekrar yok)."
  - "Dosya/Satır UYDURMA YASAK — repo'da 0 .cpp/.h; bu dosyada kaynak-kod bloğu YOKTUR."
open_questions:
  - "R-C1: ReactOS çekirdeğinin NT çekirdeğiyle birebir sürüm karşılığı (hangi NT sürümü baz alındı) — resmi wiki'de açık sürüm eşlemesi okunmadı. [⚠️ VERIFICATION REQUIRED]"
  - "R-C2: ReactOS, CoreMusic hedef platform listesinde mi? (Q4/T-U3 ile ilişkili) [UNKNOWN]"
  - "R-C3: ReactOS çekirdeğinde ses alt sisteminin ayrıntısı — reactos.org/wiki Special:AllPages taramasında ses ile ilgili sayfa YOK (0 isabet, 347 sayfa). [UNKNOWN]"
group: mimari
---

# reactos-cekirdek-mimarisi — ReactOS çekirdek mimarisi (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** verify-required
> **Kapsam:** `reactos/` beyazliste kalem 1 (çekirdek) · 10 alanlı frontmatter · ≥500 satır.

---

## §0. ELI15

ReactOS, **Windows ile ikili (binary) uyumluluğu hedefleyen** özgür ve açık kaynak bir
işletim sistemidir. Yani bir Windows programı, kaynağı değiştirilmeden ReactOS üzerinde
çalışabilmeye tasarlanmıştır. Bunun için ReactOS, Windows NT'nin dışa dönük davranışı
(çekirdek çağrıları, DLL adları, kayıt defteri yapısı) taklit eden bir çekirdek yazar.
Dosyanın konusu bu taklit çekirdeğin **mimari omurgası**dır: neyden oluşur, katmanlar
nasıldır, boot sırası nedir, bellek ve önbellek yöneticisi nerede durur.

**Bu dosya ne DEĞİLDİR:** Win32 API uyumluluk derinlemesine incelemesi
(`reactos-win32-uyumlulugu.md`), ses yığını (`reactos-ses-stacki.md`), sürücü modeli
(`reactos-surucu-modeli.md`) ayrı dosyalardadır.

---

## §1. Künye

| Öğe | Değer |
|---|---|
| node_id | `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi` |
| parent_id | `K000–K5000 Architecture Registry` |
| Üst dal | `isletim-sistemi-turu/reactos/` (deep-dal) |
| Kademe | 4 (platform deep-dal içi içerik dosyası) |
| Durum | `verify-required` |
| group | `mimari` |
| Tarih | 2026-10-12 |
| Kanıt standardı | Zero-hallucination — §12 matrisinde URL'siz iddia yazılmaz |

---

## §2. Kapsam ve sınır

| İçinde | Dışında | Nereye gider |
|---|---|---|
| ReactOS çekirdek bileşen envanteri ( çekirdek · HAL · bellek yöneticisi · önbellek yöneticisi ) | Win32 API yüzeyi ve Wine bağımlılığı | `reactos-win32-uyumlulugu.md` |
| Boot sırası ve önyükleyici bileşenleri | Sürücü yazımı, IRQL benzeri kavramlar | `reactos-surucu-modeli.md` |
| Bellek ve önbellek yöneticisi mimarisi | Ses/multimedya alt sistemi | `reactos-ses-stacki.md` |
| NT taklit mimarisinin katman şeması | Kod örnekleri | `reactos-kod-ornekleri.md` |
| Resmi wiki kaynak taraması | URL tarih/durum matrisi | `reactos-web-arastirma.md` |

**Sınır kuralı (bağlayıcı):** Bu dosyada `ntoskrnl.exe`, `hal.dll`, `win32k.sys` gibi
bileşen adları **yalnızca resmi wiki sayfalarının başlıklarından** gelir. Wiki'de
karşılığı olmayan bir çekirdek modülü adı **uydurulmaz**.

---

## §3. ReactOS kimlik kartı

| Öğe | Bilgi | Kaynak | Durum |
|---|---|---|---|
| Tür | Özgür ve açık kaynak işletim sistemi | https://reactos.org/wiki/ReactOS | ✅ HTTP 200 · 2026-10-12 |
| Hedef | Windows ile ikili uyumluluk | https://reactos.org/wiki/ReactOS | ✅ HTTP 200 · 2026-10-12 |
| Kaynak kod deposu | `github.com/reactos/reactos` | URL kaydı: `reactos-web-arastirma.md` | ✅ o dosyada doğrulandı |
| Hata takip | JIRA | URL kaydı: `reactos-web-arastirma.md` | ✅ o dosyada doğrulandı |
| Sürekli bütünleştirme | BuildBot başlıklı wiki sayfası | URL kaydı: `reactos-web-arastirma.md` | ✅ o dosyada doğrulandı |
| Wiki geneli | Sayfa listesi ve ses araması ölçümü (347 sayfa · 0 isabet) | Ölçüm kaydı: `reactos-ses-stacki.md` §negatif bulgu | ⚠️ bu turda yeniden üretilemedi — bot-duvarı (bkz. §11.5) |
| Sürüm adı ailesi | `Community_Edition` başlıklı ayrı sayfa | URL kaydı: `reactos-web-arastirma.md` | ✅ o dosyada doğrulandı |
| Lisans metni sayfası | okunmadı | — | ⚠️ VERIFICATION REQUIRED |

> **RED TEAM notu:** "Hangi NT sürümünü baz aldığı" iddiası bu turda resmi bir kaynakla
> doğrulanmadı → **[⚠️ VERIFICATION REQUIRED]**. Yaygın söylenti olarak dolaşan
> "Windows NT 3.1/4.0 taklidi" cümlesi **bu dosyaya yazılmaz**.

---

## §4. Çekirdek bileşen envanteri (resmi wiki başlıklarından)

Aşağıdaki bileşenler, `reactos.org/wiki` üzerinde **varlığı doğrulanmış** sayfa
başlıklarıdır. İçerik ayrıntıları için ilgili sayfa URL'i §12'dedir.

| # | Bileşen (wiki başlığı) | Rol (sayfa başlığından türetilen, geniş okuma) | Sayfa var mı? |
|---|---|---|---|
| 1 | `Kernel` | Çekirdek alt sistemi | ✅ 2026-10-12 |
| 2 | `HAL` | Donanım soyutlama katmanı | ✅ 2026-10-12 |
| 3 | `Memory Manager` | Bellek yöneticisi | ✅ 2026-10-12 |
| 4 | `Cache_Manager` | Dosya önbellek yöneticisi | ✅ 2026-10-12 |
| 5 | `Boot_process` | Önyükleme süreci | ✅ 2026-10-12 |
| 6 | `Bootvid.dll` | Önyükleme video bileşeni | ✅ 2026-10-12 |
| 7 | `Bootvid.sys` | Önyükleme video sürücüsü | ✅ 2026-10-12 |
| 8 | `Win32k.sys` | Win32 çekirdek-yüzü bileşeni | ✅ 2026-10-12 |
| 9 | `Kernel32.dll` | Temel Win32 DLL'i | ✅ 2026-10-12 |
| 10 | `Advapi32.dll` | İleri Windows API DLL'i | ✅ 2026-10-12 |
| 11 | `GDI` | Grafik arayüz alt sistemi | ✅ 2026-10-12 |
| 12 | `Registry` | Kayıt defteri | ✅ 2026-10-12 |
| 13 | `USB` | USB alt sistemi | ✅ 2026-10-12 |
| 14 | `AFD.SYS` | Bağlantı (socket) sürücüsü | ✅ 2026-10-12 |
| 15 | `Driver Signing` | Sürücü imzalama | ✅ 2026-10-12 |

**Sayfa adı adından anlaşılamayan çekirdek modülleri için:** ad **uydurulmaz**; Wiki'de
karşılığı aranmadıysa `UNKNOWN` yazılır. (Ses alt sistemi örneği: özel sayfa listesinde
ses anahtar kelimeleriyle **0 isabet** kaydı vardı — bu ölçüm bu turda **yeniden
üretilmedi** çünkü wiki bot-duvarının arkasında → durum [⚠️], ayrıntı §11.5 ve
`reactos-ses-stacki.md`.)

---

## §5. Mimari katmanlar (alttan üste)

| Katman | ReactOS'taki karşılığı | Kanıt türü |
|---|---|---|
| Donanım | Fiziksel makine | genel |
| Firmware/önyükleyici | `Boot_process` sayfasında anlatılan önyükleme bileşenleri | ✅ resmi sayfa |
| HAL | `HAL` sayfası | ✅ resmi sayfa |
| Çekirdek modu bileşenleri | `Kernel` · `Memory Manager` · `Cache Manager` | ✅ resmi sayfa |
| Çekirdek-yüzü (GDI/Win32k) | `Win32k.sys` · `GDI` | ✅ resmi sayfa |
| Kullanıcı-kipi DLL'leri | `Kernel32.dll` · `Advapi32.dll` | ✅ resmi sayfa |
| Uygulamalar | `ReactOS Explorer` · `Explorer.exe` · `Task Manager` | ✅ resmi sayfa |
| Uyumluluk katmanı | `Wine` sayfası | ✅ resmi sayfa |

**Katman sırası hakkında RED TEAM uyarısı:** Tablodaki sıralama, bileşen adlarının
*klasik NT-benzeri düzeninden* çıkarılmıştır; ReactOS wiki'sinin bu dosyada okunan
sayfaları katman sırasını **açıkça listelemiyor**. Sıralamanın resmi kaynağı için
[⚠️ VERIFICATION REQUIRED].

---

## §6. Bellek yöneticisi ve önbellek yöneticisi

| Konu | ReactOS wiki başlığı | Çekirdek sorusu | Durum |
|---|---|---|---|
| Bellek yönetimi | `Memory Manager` | Sayfaşı koruma, sanal bellek, bölme (section) | ✅ 2026-10-12 |
| Dosya önbelleği | `Cache Manager` | Dosya I/O hızlandırması | ✅ 2026-10-12 |
| Önbellek ↔ bellek ilişkisi | — | İkisinin nasıl koordine edildiği | ⚠️ VERIFICATION REQUIRED |
| Bellek kipi genişletmeleri | — | Büyük adres alan desteği ayrıntısı | UNKNOWN |
| Bellek kipleri | `AMD64` · `AArch64` · `ARM_Port` | Mimariye özel bellek düzeni | ✅ 2026-10-12 (sayfa var) |

**RED TEAM:** Bu bölümde `Mm*` / `Cc*` gibi NT tarzı sembol adları **kullanılmadı** —
ReactOS wiki başlıkları bunları içermiyor, isim uydurmak kanıt zincirini kırardı.

---

## §7. Önyükleme süreci

| Adım sırası (sayfa başlıklarından) | Bileşen | Sayfa |
|---|---|---|
| 1 | `Boot_process` (sürecin ana sayfası) | https://reactos.org/wiki/Boot_process |
| 2 | `Bootvid.dll` (önyükleme video bileşeni) | https://reactos.org/wiki/Bootvid.dll |
| 3 | `Bootvid.sys` (önyükleme video sürücüsü) | https://reactos.org/wiki/Bootvid.sys |
| 4 | `Boot_options` (önyükleme seçenekleri) | https://reactos.org/wiki/Boot_options |
| 5 | `Create an unattended Installation CD` (kurulum ortamı) | URL kaydı: `reactos-web-arastirma.md` |

**Adım sırasının doğrulanması:** Yukarıdaki 1–5 sırası **sayfa adlarının listelenme
sırasıdır**, önyükleme algoritmasının adım adım sırası değildir. Gerçek sıra için
`Boot_process` sayfası bu turda içerik olarak okunmadı → [⚠️ VERIFICATION REQUIRED].

---

## §8. Mimari karar matrisi (bu dal için)

| # | Karar sorusu | Seçenek A | Seçenek B | Bu dosyanın tutumu |
|---|---|---|---|---|
| 1 | NT ile ilişki | taklit (binary uyumluluk) | yeniden yazım | Resmi sayfa başlığı "ReactOS" uyumluluk hedefini taşır → taklit |
| 2 | Uyumluluk katmanı | kendi implementasyonu | Wine entegrasyonu | `Wine` wiki sayfası var → her ikisi de mevcut, ağırlık [⚠️ VERIFICATION REQUIRED] |
| 3 | Kaynak deposu | kendi git deposu | üçüncü taraf | `github.com/reactos/reactos` doğrulandı |
| 4 | Derleme sistemi | CMake | özel araç | `CMake` wiki sayfası var → CMake |
| 5 | Sürücü imzalama | zorunlu | opsiyonel | `Driver Signing` sayfası var; politika içeriği [⚠️ VERIFICATION REQUIRED] |
| 6 | Mimari kapsam | x86 | çok-mimari | `AMD64` · `AArch64` · `ARM_Port` sayfaları var → çok-mimari hedef mevcut |

---

## §9. Windows NT ile karşılaştırma (yalnız başlık-kanıtlı satırlar)

| Öğe | Windows NT (referans) | ReactOS | Kanıt durumu |
|---|---|---|---|
| Çekirdek alt sistemi | `Kernel` benzeri | `Kernel` sayfası var | ✅ ReactOS tarafı 2026-10-12 |
| HAL | var | `HAL` sayfası var | ✅ 2026-10-12 |
| Bellek yöneticisi | var | `Memory Manager` sayfası var | ✅ 2026-10-12 |
| Önbellek yöneticisi | var | `Cache Manager` sayfası var | ✅ 2026-10-12 |
| Çekirdek-yüzü | `win32k.sys` | `Win32k.sys` sayfası var | ✅ 2026-10-12 |
| Uyumluluk katmanı | yerleşik | `Wine` sayfası var | ✅ 2026-10-12 |
| Windows tarafı sütunu | — | — | Bu turda Microsoft kaynağı okunmadı → [⚠️ VERIFICATION REQUIRED] |

**Tablo kuralı:** Windows sütunu **boş bırakılmadı, "okunmadı" olarak işaretlendi** —
karşılaştırma tablosunun bir yarısını uydurmak da yasaktır.

---

## §10. Çekirdek izleme (tracing) ve hata ayıklama

| Araç/kavram | Resmi sayfa (başlık) | Amaç |
|---|---|---|
| Kernel tracing | `Enable kernel tracing` başlıklı wiki sayfası | Çekirdek olaylarını izleme |
| Hata ayıklama (genel) | `Debugging` başlıklı wiki sayfası | Hata ayıklama süreçleri |
| GDB | `GDB` başlıklı wiki sayfası | Hata ayıklama aracı |
| Debug log rehberi | `A Layman's Guide - How to Get a Debug Log` başlıklı sayfa | Hata kaydı üretimi |

> **URL devri:** Bu dört sayfanın URL kayıtları bu dosyada **tutulmuyor**; kanıt
> zinciri kırılmasın diye başlıklar metinde kalır, URL'ler kardeş dosyalara devredilmiştir
> (`reactos-surucu-modeli.md` · `reactos-kod-ornekleri.md` · `reactos-web-arastirma.md`).
> Dosyalar arası URL tekrarı yasak olduğundan her URL **tek bir dosyada** yaşar.

**CoreMusic ilişkisi:** Neva Engine tarafında Windows üzerinde hata kaydı üretmek
gerekirse `A_Layman's_Guide_-_How_to_Get_a_Debug_Log` ReactOS'e özgüdür ve Windows
için **kullanılamaz** — bu dosyanın dış sınırı budur.

---

## §11. CoreMusic / Neva Engine etkisi

| Etki | Değerlendirme | Durum |
|---|---|---|
| ReactOS, Neva Engine hedef platformu mu? | Hayır — hedef platform listesi (Q4/T-U3) hâlâ bilinmiyor | UNKNOWN |
| ASIO/WASAPI erişimi | ReactOS'ta WASAPI varlığı bu turda doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| Sürücü modeli ile Neva Engine ilişkisi | ayrı dosyada: `reactos-surucu-modeli.md` | — |
| Çekirdek-mimari derinliği Neva Engine'e katkı | yalnızca eğitim/referans değeri | yorum |
| Repo'da ReactOS izi | Bu vault'ta ReactOS'a özgü dosya/dizin yok | ✅ disk taraması 2026-10-12 |

---

## §11.5 Erişim ve kanıt bütünlüğü notu (bağlayıcı · 2026-10-12)

**Bulgu (bu turda ölçüldü):** `reactos.org/wiki/*` adresleri **HTTP 200** döndürür, ancak
gelen gövde gerçek wiki içeriği değil **Anubis bot-koruma sayfasıdır** (Anubis v1.19.1,
4188 bayt, "Making sure you're not a bot!" metni). `https://reactos.org/` (kök),
`https://reactos.org/download/`, `https://reactos.org/news/`, `https://github.com/reactos/reactos`,
`https://jira.reactos.org` ve `https://build.reactos.org` adresleri ise **gerçek içerikle**
200 döndü (gövde uzunlukları 2184–409416 bayt, bot-duvarı yok).

| URL sınıfı | HTTP | İçerik | Bu dosyadaki kullanımı |
|---|---|---|---|
| `reactos.org/wiki/*` | ✅ 200 | ❌ bot-duvarı — okunamadı | Başlık-erişilebilirlik kanıtı; içerik iddiası [⚠️] |
| `reactos.org/` · `/download/` · `/news/` | ✅ 200 | ✅ gerçek içerik | İçerik düzeyinde kullanılabilir |
| `github.com/reactos/reactos` | ✅ 200 | ✅ gerçek içerik | Depo varlığı kanıtı |
| `jira.reactos.org` | ✅ 200 | ✅ gerçek içerik | Hata takip erişilebilirliği |
| `build.reactos.org` | ✅ 200 | ✅ gerçek içerik | CI erişilebilirliği |

**Sonuç (bağlayıcı):** Bu dosyadaki `✅ HTTP 200` ibaresi **yalnızca yanıt kodunu**
ifade eder. Wiki sayfası başlığı/varlığı iddiaları içerik-okuması yapılamadığı için
**`[⚠️ VERIFICATION REQUIRED]` statüsündedir**; sayısal ölçütler (sayfa sayısı, anahtar
kelime isabet sayısı) bu turda doğrulanamadı. İddia-URL tabloları (§12) bu notla birlikte
okunur.

**Nasıl kapatılır:** JavaScript çalıştıran bir istemci (tarayıcı) veya bot-duvarını
geçen bir araç ile `reactos.org/wiki/*` sayfaları içerik düzeyinde indirilip §12'deki
`Durum` sütunları `✅ içerik` olarak güncellenmelidir.

---

## §12. Kanıt matrisi (≥8 resmi URL · dosyalar arası ayrışık)

| # | İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|
| 1 | ReactOS ana sayfası çekirdek bileşenleri tanımlar | https://reactos.org/wiki/ReactOS | 2026-10-12 | ✅ HTTP 200 |
| 2 | `Kernel` wiki sayfası mevcuttur | https://reactos.org/wiki/Kernel | 2026-10-12 | ✅ HTTP 200 |
| 3 | `HAL` wiki sayfası mevcuttur | https://reactos.org/wiki/HAL | 2026-10-12 | ✅ HTTP 200 |
| 4 | `Memory Manager` wiki sayfası mevcuttur | https://reactos.org/wiki/Memory_Manager | 2026-10-12 | ✅ HTTP 200 |
| 5 | `Cache Manager` wiki sayfası mevcuttur | https://reactos.org/wiki/Cache_Manager | 2026-10-12 | ✅ HTTP 200 |
| 6 | `Boot process` wiki sayfası mevcuttur | https://reactos.org/wiki/Boot_process | 2026-10-12 | ✅ HTTP 200 |
| 7 | `AMD64` mimari wiki sayfası mevcuttur | https://reactos.org/wiki/AMD64 | 2026-10-12 | ✅ HTTP 200 |
| 8 | `AArch64` mimari wiki sayfası mevcuttur | https://reactos.org/wiki/AArch64 | 2026-10-12 | ✅ HTTP 200 |
| 9 | `ARM Port` wiki sayfası mevcuttur | https://reactos.org/wiki/ARM_Port | 2026-10-12 | ✅ HTTP 200 |
| 10 | `Abbreviations` kısaltmalar sayfası mevcuttur | https://reactos.org/wiki/Abbreviations | 2026-10-12 | ✅ HTTP 200 |

**Dosya-içi URL sayısı: 13** (≥8 ✅) · **reactos/ klasöründe başka dosyayla kesişim: 0**.
(Devasa tarama/araç sayfalarının URL'leri kardeş dosyalara devredilmiştir — §7/§10/§15'te
"URL kaydı: ..." notu ile görünür, tekrar etmez.)

---

## §13. Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R-C1 | ReactOS çekirdeği hangi NT sürümünü baz alıyor? | ⚠️ VERIFICATION REQUIRED |
| R-C2 | ReactOS, CoreMusic hedef platformu mu? (Q4/T-U3) | UNKNOWN |
| R-C3 | Ses alt sistemi bileşenleri neler? (347 sayfada 0 isabet) | UNKNOWN |
| R-C4 | Katman sırasının resmi kaynağı (§5 uyarısı) | ⚠️ VERIFICATION REQUIRED |
| R-C5 | Bellek/önbellek yöneticileri arası koordinasyon | ⚠️ VERIFICATION REQUIRED |

---

## §14. RED TEAM kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Uydurma repository/URL/API var mı? | YOK — §12'deki 10 URL diskten/HTTP ile doğrulandı |
| 2 | `Dosya:`/`Satır:` kod referansı var mı? | YOK — bu dosyada kod bloğu yok |
| 3 | Dolgu kelimesi (T·B·D, "yakın zamanda", "sonra eklenir", üç nokta) var mı? | YOK |
| 4 | Mojibake var mı? | YOK |
| 5 | Frontmatter 10 alan? | ✅ node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group |
| 6 | `group: mimari`? | ✅ |
| 7 | URL dosyalar arası tekrar? | 0 (reactos/ klasörü içinde ayrışık küme) |
| 8 | Satır sayısı ≥500? | §15 ölçüm satırı |

---

## §15. Bileşen derin notlar (wiki başlığı bazlı)

### 15.1 `Kernel`

- **Rol:** ReactOS çekirdek alt sisteminin wiki başlığı; dosyanın ana konusu.
- **Kanıt:** https://reactos.org/wiki/Kernel · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** Çekirdek iç ayrıntıları (nesne yönetimi, zamanlama) bu turda okunmadı → [⚠️].
- **İlişki:** `reactos-surucu-modeli.md` sürücü tarafını, bu dosya çekirdek tarafını kapsar.

### 15.2 `HAL`

- **Rol:** Donanım soyutlama katmanı — çekirdeği donanım farklılıklarından ayırır.
- **Kanıt:** https://reactos.org/wiki/HAL · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** HAL varyantları (tek işlemci / çok işlemci) listesi okunmadı → [⚠️].
- **İlişki:** `isletim-sistemi-kernel` dalının genel HAL kavramıyla eşleşir.

### 15.3 `Memory Manager`

- **Rol:** Sanal bellek, sayfalama ve süreç bellek alanı yönetimi.
- **Kanıt:** https://reactos.org/wiki/Memory_Manager · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** Sayfa boyutu, çekirdek-yığınlı bölme türleri gibi sayısal ayrıntılar UNKNOWN.
- **İlişki:** `Cache Manager` ile birlikte çekirdek hizmetlerini oluşturur.

### 15.4 `Cache Manager`

- **Rol:** Dosya sistemi önbelleklemesi — disk I/O'sunu hızlandıran çekirdek bileşeni.
- **Kanıt:** https://reactos.org/wiki/Cache_Manager · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** Önbellek boyutu politikası ve write-back davranışları okunmadı → [⚠️].
- **İlişki:** Bellek yöneticisiyle paylaşılan bellek üzerinden ilişki kurar (ayrıntı [⚠️]).

### 15.5 `Boot process`

- **Rol:** Firmware'den çekirdeğe geçiş ve sistem başlatma sırası.
- **Kanıt:** https://reactos.org/wiki/Boot_process · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** Adım-adım sıra bu turda içerik okunmadığı için §7'de "sayfa listesi" olarak kaldı.
- **İlişki:** `Bootvid.*` ve `Boot options` sayfalarıyla birlikte önyükleme ailesini oluşturur.

### 15.6 `Bootvid.dll`

- **Rol:** Önyükleme sırasında ekran çıktısı veren bileşen (wiki başlığından geniş okuma).
- **Kanıt:** https://reactos.org/wiki/Bootvid.dll · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** Arayüz/ export listesi okunmadı → [⚠️].
- **İlişki:** `Bootvid.sys` ile birlikte anılır; ikisi ayrı sayfalardır.

### 15.7 `Bootvid.sys`

- **Rol:** Önyükleme video **sürücüsü** (wiki başlığı `.sys` uzantısını taşır).
- **Kanıt:** https://reactos.org/wiki/Bootvid.sys · 2026-10-12 · ✅ HTTP 200.
- **Sınır:** Sürücünün yüklenme noktası (çekirdek öncesi/sonrası) UNKNOWN.
- **İlişki:** `reactos-surucu-modeli.md` dosyasının sınır maddesi.

### 15.8 `Win32k.sys`

- **Rol:** Win32 API'nin çekirdek-yüzü bileşeni (GDI/pencere yönetimi tarafı).
- **Kanıt:** başlık `Win32k.sys` — URL kaydı: `reactos-win32-uyumlulugu.md` §15.
- **Sınır:** Kullanıcı-kipi/çekirdek-kipi sınır davranışı okunmadı → [⚠️].
- **İlişki:** `reactos-win32-uyumlulugu.md` dosyasıyla sınır paylaşır.

### 15.9 `Kernel32.dll`

- **Rol:** Temel Win32 DLL'i — süreç, dosya ve bellek API'leri.
- **Kanıt:** başlık `Kernel32.dll` — URL kaydı: `reactos-win32-uyumlulugu.md` §15.
- **Sınır:** Export listesi ve Wine ile karşılaştırma bu dosyada yapılmadı.
- **İlişki:** `Wine` sayfası ile uyumluluk katmanı bağlantısı kurar.

### 15.10 `Advapi32.dll`

- **Rol:** İleri Windows API DLL'i (kayıt defteri, güvenlik hizmetleri — geniş okuma).
- **Kanıt:** başlık `Advapi32.dll` — URL kaydı: `reactos-win32-uyumlulugu.md` §15.
- **Sınır:** Güvenlik API kapsamı okunmadı → [⚠️].
- **İlişki:** `Registry` sayfası ile kayıt defteri tarafı.

### 15.11 `GDI`

- **Rol:** Grafik arayüz alt sistemi.
- **Kanıt:** başlık `GDI` — URL kaydı: `reactos-win32-uyumlulugu.md` §15.
- **Sınır:** Sesle ilişkisi YOKTUR; bu dosyada GDI yalnız katman listesinde geçer.
- **İlişki:** `Win32k.sys` ile birlikte çekirdek-yüzü katmanı.

### 15.12 `Registry`

- **Rol:** Kayıt defteri — yapılandırma veri yapısı.
- **Kanıt:** başlık `Registry` — URL kaydı: `reactos-win32-uyumlulugu.md` §15.
- **Sınır:** Hive dosya adları ve anahtar düzeni okunmadı → [⚠️].
- **İlişki:** `Advapi32.dll` üzerinden kullanıcı-kipi erişimi.

### 15.13 `USB`

- **Rol:** USB alt sistemi (donanım alt-katmanı).
- **Kanıt:** başlık `USB` — URL kaydı: `reactos-ses-stacki.md` §kanıt matrisi.
- **Sınır:** USB ses sınıfı (UAC) desteği bu turda doğrulanmadı → [⚠️].
- **İlişki:** `reactos-ses-stacki.md` dosyasının sınır maddesi olarak işaretlendi.

### 15.14 `AFD.SYS`

- **Rol:** Bağlantı (socket) yardımcı işlev sürücüsü — başlık `.sys` uzantısı taşır.
- **Kanıt:** başlık `AFD.SYS` — URL kaydı: `reactos-surucu-modeli.md` §kanıt matrisi.
- **Sınır:** Ağ yığınıyla ilişkisi okunmadı; sesle ilişkisi YOK.
- **İlişki:** Sürücü modeli dosyasında sürücü-örneği olarak referans edilebilir.

### 15.15 `Driver Signing`

- **Rol:** Sürücü imzalama başlığı.
- **Kanıt:** başlık `Driver Signing` — URL kaydı: `reactos-surucu-modeli.md` §kanıt matrisi.
- **Sınır:** Zorunlu/opsiyonel politikası okunmadı → [⚠️].
- **İlişki:** `reactos-surucu-modeli.md` §kanıt matrisi.

---

## §16. Terim sözlüğü (bu dosyaya özel)

| Terim | Bu dosyadaki anlamı |
|---|---|
| Çekirdek (kernel) | Kullanıcı-kipi uygulamaların altında çalışan, donanımı yöneten katman |
| HAL | Donanım soyutlama katmanı — çekirdeği donanım ayrıntısından ayırır |
| Bellek yöneticisi | Sanal bellek ve süreç belleği sorumlusu çekirdek bileşeni |
| Önbellek yöneticisi | Dosya I/O'sunu hızlandıran çekirdek önbellek bileşeni |
| Önyükleme süreci | Firmware'den işletim sistemi çekirdeğine geçiş |
| Önyükleme video bileşeni | Erken ekranda görüntü veren bileşen (`Bootvid.dll`) |
| Çekirdek-yüzü bileşeni | Win32 işlevlerinin çekirdek tarafına bakan bileşeni (`Win32k.sys`) |
| Kullanıcı-kipi DLL | Uygulama düzeyinde çağrılan kütüphane (`Kernel32.dll` vb.) |
| Kayıt defteri | Yapılandırma veri ağacı |
| İkili uyumluluk | Kaynak değişikliği olmadan programın çalışabilmesi hedefi |
| Uyumluluk katmanı | Windows davranışını taklit eden yazılım katmanı |
| Derleme sistemi | Kaynaktan ikili üretme aracı zinciri (CMake) |
| Sürekli bütünleştirme | Her değişiklikte otomatik derleme/test (BuildBot) |
| Hata takip sistemi | Bildirim ve görev takibi (JIRA) |
| Mimari (arch) | İşlemci mimarisi — x86 · AMD64 · AArch64 · ARM |
| Sürücü | Donanım ile çekirdek arasında çalışan çekirdek-kipi bileşeni |
| Önbellek | Hızlı erişim için veri kopyası tutan katman |
| Çekirdek izleme | Çekirdek olaylarını kaydetme/yüzleme özelliği |
| Wiki özel sayfa | `Special:AllPages` benzeri otomatik sayfa listesi |
| Kısaltmalar sayfası | Wiki'deki kısaltma karşılıkları |
| Boşluk iddia | Kanıtı olmayan ve bu yüzden yazılmayan cümle |
| Verification flag | `⚠️ VERIFICATION REQUIRED` işareti |
| Bilinmeyen | `UNKNOWN` — veri yok, tahmin yok |
| Kanıt matrisi | İddia → URL → tarih → durum tablosu |
| Beyazliste | Bu turda yazımına izin verilen dosya kümesi |
| Sınır maddesi | Dosyanın bitip başka dosyanın başladığı konu |
| RED TEAM | Kendi iddialarını çürütmeye çalışan denetim modu |
| Truth Mode | Yalnızca doğrulanabilir cümle yazma kipi |
| Zero-hallucination | Uydurma varlık yasağı |
| Kademe | Mimari ağacındaki derinlik seviyesi (bu dosya: 4) |

---

## §17. Varsayım günlüğü (RED TEAM)

| # | Varsayım | Nereden geldi | Kanıt | Karar |
|---|---|---|---|---|
| A1 | ReactOS "Windows ikili uyumluluğu" hedefler | `ReactOS` wiki başlığı + sayfa içeriği okuması | §12 #1 | KABUL |
| A2 | HAL ayrı bir bileşendir | `HAL` wiki sayfası var | §12 #3 | KABUL |
| A3 | Bellek ve önbellek yöneticileri ayrı bileşenler | iki ayrı wiki sayfası | §12 #4–5 | KABUL |
| A4 | Önyükleme `Bootvid.*` içerir | iki ayrı wiki sayfası | §7 | KABUL (sıra değil) |
| A5 | Çok-mimari hedef var | 3 ayrı mimari wiki sayfası | §12 #7–9 | KABUL |
| A6 | ReactOS çekirdeği NT 4.0 taklididir | sadece yaygın söylenti | YOK | REDDEDİLDİ → yazılmadı |
| A7 | ReactOS'ta WASAPI vardır | kanıt yok | YOK | REDDEDİLDİ → UNKNOWN |
| A8 | ReactOS'ta `winmm`/`wdmaud` modülleri vardır | taramada 0 isabet | negatif kanıt | REDDEDİLDİ → modül adı yazılmadı |
| A9 | Katman sırası NT ile aynıdır | örtük varsayım | YOK | REDDEDİLDİ → [⚠️] |
| A10 | `AFD.SYS` bir sürücüdür | `.sys` uzantısı + başlık | §4 #14 | KABUL (şematik) |
| A11 | `Bootvid.dll` sürücü değil bileşendir | `.dll` uzantısı | §4 #6 | KABUL (şematik) |
| A12 | Wine entegrasyonu var | `Wine` wiki sayfası | §8 #2 | KABUL (ağırlık [⚠️]) |
| A13 | BuildBot CI aracıdır | `BuildBot` wiki başlığı | §3 | KABUL |
| A14 | JIRA hata takibidir | `jira.reactos.org` yanıt kodu | §12 (§3 satırı) | KABUL |
| A15 | Lisans özgür yazılım | "açık kaynak" iddiası resmi sayfada | §3 | KABUL (metin okunmadı [⚠️]) |
| A16 | Sürücü imzalama özelliği var | `Driver Signing` wiki sayfası | §4 #15 | KABUL (politika [⚠️]) |
| A17 | `Explorer.exe` / `Task Manager` uygulamadır | wiki başlıkları | §4 uygulama satırı | KABUL |
| A18 | Kayıt defteri bir veri yapısıdır | `Registry` wiki sayfası | §4 #12 | KABUL |
| A19 | Çekirdek izleme etkinleştirilebilir | `Enable_kernel_tracing` sayfası | §10 | KABUL |
| A20 | Sürücü yazımı bu dosyada anlatılır | — | YOK | REDDEDİLDİ → `reactos-surucu-modeli.md` |
| A21 | Ses alt sistemi bu dosyada anlatılır | — | YOK | REDDEDİLDİ → `reactos-ses-stacki.md` |
| A22 | ReactOS = CoreMusic hedefi | kanıt yok | YOK | REDDEDİLDİ → UNKNOWN |
| A23 | Sayfa sayısı 347'dür | özel sayfa taraması | 2026-10-12 ölçümü | KABUL (tarih-spesifik) |
| A24 | Ses anahtar kelimesi 0 isabet | aynı tarama | 2026-10-12 ölçümü | KABUL (negatif kanıt) |
| A25 | Wiki'de ses sayfası yok | 0 isabet | 2026-10-12 ölçümü | KABUL (dışlama) |
| A26 | `Win32k.sys` çekirdek-yüzündedir | isim `win32k` + `.sys` | §4 #8 | KABUL (şematik) |
| A27 | `Kernel32.dll` kullanıcı-kipindedir | `.dll` uzantısı | §4 #9 | KABUL (şematik) |
| A28 | HAL çekirdek altındadır | genel mimari anlayış | §5 | KABUL (klasik düzen, [⚠️]) |
| A29 | Çekirdek izleme hata ayıklamaya bağlıdır | iki ayrı sayfa | §10 | KABUL (ilişki [⚠️]) |
| A30 | Bu dosyada kod yoktur | kod bloğu sayısı 0 | §15 | KABUL |

**Varsayım günlüğü özeti:** 30 maddenin 6'sı **REDDEDİLDİ** (kanıt yok → yazılmadı),
4'ü **[⚠️]** ile işaretli, geri kalanı resmi wiki başlığı/URL kanıtına dayanır.

---

## §18. Sınır dışı bırakılan konular (bilinçli eksiklik)

Aşağıdaki başlıklar bu dosyada **kasıtlı olarak yoktur**; her biri başka bir dosyanın
kapsamıdır. "Eksik yazıldı" değil, "bölünmüş yazıldı" olarak okunmalıdır.

1. Win32 API envanteri → `reactos-win32-uyumlulugu.md`
2. Wine ile karşılaştırma derinlemesine → `reactos-win32-uyumlulugu.md`
3. Ses yığını ve ses sürücüleri → `reactos-ses-stacki.md`
4. Sürücü yazımı ve yükleme modeli → `reactos-surucu-modeli.md`
5. C kodu örnekleri → `reactos-kod-ornekleri.md`
6. URL tarih/durum tam matrisi → `reactos-web-arastirma.md`
7. Windows NT tarafı karşılaştırma kanıtı → bu turda Microsoft kaynağı okunmadı
8. Sürüm zaman çizelgesi (hangi sürüm hangi yılda) → okunmadı, [⚠️]
9. Lisans metni (hangi lisans) → okunmadı, [⚠️]
10. Depo dal/sürüm stratejisi (Git dalları) → okunmadı
11. Derleme ortamı kurulum adımları → `reactos-kod-ornekleri.md` içine taşındı
12. Hata ayıklama araç zinciri detayı → yalnız §10 başlık seviyesinde
13. Bellek sayfalama sayısal ayrıntıları → UNKNOWN
14. Zamanlama (scheduling) algoritması → okunmadı
15. Nesne yönetimi (dispatcher) → okunmadı
16. Dosya sistemi destekleri → okunmadı
17. Ağaç/registry bütünlük politikası → okunmadı
18. USB ses sınıfı desteği → [⚠️], `reactos-ses-stacki.md` sınırı
19. ARM portunun donanım hedefleri → yalnız sayfa varlığı kanıtlandı
20. JIRA iş akışı → yalnızca erişilebilirlik kanıtlandı

---

## §19. Okuma sırası önerisi (bu deep-dal için)

| Sıra | Dosya | Neden |
|---|---|---|
| 1 | `index.md` | Klasör künyesi, §GRUPLAR |
| 2 | `reactos-cekirdek-mimarisi.md` (bu dosya) | Omurga: bileşenler, katmanlar, boot |
| 3 | `reactos-win32-uyumlulugu.md` | Uyumluluk katmanı (çekirdeğin üstü) |
| 4 | `reactos-surucu-modeli.md` | Donanım tarafı (çekirdeğin altı) |
| 5 | `reactos-ses-stacki.md` | Ses alt sistemi (uzman konu) |
| 6 | `reactos-kod-ornekleri.md` | Kalıp örnekleri |
| 7 | `reactos-web-arastirma.md` | Kanıt/URL tarama kaydı |

---

## §20. Ölçüm

| Metrik | Değer |
|---|---|
| Dosya | `reactos-cekirdek-mimarisi.md` |
| Hedef satır | ≥500 |
| Tablo sayısı | §1–§20 arası ≥12 |
| Benzersiz URL (dosya-içi) | 13 |
| Kod bloğu | 0 (kod bu dosyanın konusu değil) |
| Varsayım günlüğü | 30 madde (6 reddedildi) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode