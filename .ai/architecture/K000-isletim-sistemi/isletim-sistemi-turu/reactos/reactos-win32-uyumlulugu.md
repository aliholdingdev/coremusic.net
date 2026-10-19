---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-win32-uyumlulugu"
node_type: architecture
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-api"
evidence:
  - "https://reactos.org/wiki/Win32 (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Win32k.sys (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Wine (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Kernel32.dll (✅ HTTP 200 · 2026-10-12)"
  - "https://reactos.org/wiki/Registry (✅ HTTP 200 · 2026-10-12)"
validation:
  - "2026-10-12 RED TEAM: Win32 API fonksiyon adları bu turda resmi Microsoft kaynağıyla doğrulanmadı → yalnızca wiki başlığı düzeyinde iddia, fonksiyon adı uydurulmadı."
  - "≥500 satır · 10 alanlı frontmatter · reactos/ klasöründe URL kümesi ayrışık."
  - "Dosya/Satır UYDURMA YASAK — repo'da 0 .cpp/.h; kod blokları yalnız etiketli."
open_questions:
  - "R-W1: ReactOS'un Win32 API kapsam yüzdesi (hangi alt-sistem eksik) → [⚠️ VERIFICATION REQUIRED]"
  - "R-W2: Wine bileşenlerinin ReactOS'a ne şekilde entegre edildiği (versiyon/paketleme) → [⚠️ VERIFICATION REQUIRED]"
  - "R-W3: Uygulama uyumluluk listesi (hangi uygulama test edilmiş) → UNKNOWN"
group: mimari
---

# reactos-win32-uyumlulugu — ReactOS Win32 uyumluluk katmanı (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** verify-required
> **Kapsam:** `reactos/` beyazliste kalem 2 (Win32 uyumluluk) · 10 alanlı frontmatter · ≥500 satır.

---

## §0. ELI15

Bir işletim sisteminin "Windows uyumlu" olması, uygulamaların Windows'ta gördüğü
**kapisinin** (API'nin) aynısını sunması demektir. ReactOS bu kapiyi iki katman halinde
kurar: altta Windows çekirdek-yüzüne benzeyen bir bileşen (`Win32k.sys`), üstte
uygulamaların çağırdığı kütüphaneler (`Kernel32.dll`, `Advapi32.dll` ve diğerleri).
Ayrıca ReactOS, bu kütüphanelerin bir kısmını kendi yazmak yerine **Wine** projesinden
devralır. Bu dosya, bu uyumluluk katmanının **sınırlarını ve sorumluluk dağılımını**
tanımlar.

**Sınır:** API fonksiyon listesi, test edilen uygulama listesi ve sürüm karşılıkları bu
dosyada **yoktur** — kaynağı doğrulanmadığı için yazılmamıştır.

---

## §1. Künye

| Öğe | Değer |
|---|---|
| node_id | `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-win32-uyumlulugu` |
| parent_id | `K000–K5000 Architecture Registry` |
| Üst dal | `isletim-sistemi-turu/reactos/` |
| Kardeş dosyalar | `reactos-cekirdek-mimarisi.md` · `reactos-ses-stacki.md` · `reactos-surucu-modeli.md` |
| Kademe | 4 |
| Durum | `verify-required` |
| group | `mimari` |
| Tarih | 2026-10-12 |

---

## §2. Kapsam ve sınır

| İçinde | Dışında | Nereye |
|---|---|---|
| Uyumluluk katmanının tanımı ve katmanları | Çekirdek iç mekanizması | `reactos-cekirdek-mimarisi.md` |
| `Win32k.sys` · `GDI` · `Kernel32.dll` · `Advapi32.dll` rolleri | Sürücü tarafı | `reactos-surucu-modeli.md` |
| Wine ilişkisi (başlık seviyesi) | Ses API'leri (waveOut, mixer vb.) | `reactos-ses-stacki.md` |
| Kayıt defteri ve clipboard başlıkları | Kod örnekleri | `reactos-kod-ornekleri.md` |
| Uyumluluk seviyeleri şeması | URL tarama kaydı | `reactos-web-arastirma.md` |

---

## §3. Uyumluluk kavramının üç boyutu

| Boyut | Soru | ReactOS için bu turdaki cevap |
|---|---|---|
| Kaynak uyumluluğu | Windows'ta derlenen kaynak burada derlenir mi? | Bilinmiyor → UNKNOWN |
| İkili (binary) uyumluluğu | Windows ikilisi burada çalışır mı? | Hedef olarak iddia ediliyor → [⚠️] |
| Davranış uyumluluğu | Aynı girdi aynı sonucu verir mi? | Test kapsamı okunmadı → UNKNOWN |
| Arayüz uyumluluğu | Aynı fonksiyon adları/davranışları var mı? | Wiki'de `Win32` sayfası var → başlık kanıtı |
| Kullanıcı deneyimi uyumluluğu | Pencere/dosya davranışı aynı mı? | `GDI` · `Explorer.exe` başlıkları → [⚠️] |

**RED TEAM notu:** "ReactOS Windows uygulamalarının %X'ini çalıştırır" gibi bir sayı
**hiçbir yerde yazılmadı** — ölçüm okunmadı, tahmin yasak.

---

## §4. Uyumluluk katmanının kesiti (alttan üste)

```text
[Uygulama]  (Windows programı)
     |  çağrı
     v
[Win32 DLL katmanı]  Kernel32.dll · Advapi32.dll · diğerleri
     |  sistem çağrısı benzeri geçiş
     v
[Çekirdek-yüzü bileşeni]  Win32k.sys (pencere/GDI tarafı)
     |  çekirdek çağrıları
     v
[Çekirdek]  Kernel · Memory Manager · Cache Manager
     |  HAL üzerinden
     v
[Donanım]
```

> **Kesit notu:** Şema, §4'teki wiki başlıklarının *mantıksal* sıralamasıdır; okların
> hangi somut mekanizma ile gerçekleştirdiği bu turda okunmadı → [⚠️ VERIFICATION REQUIRED].

| Katman | Bileşen (wiki başlığı) | Sayfa |
|---|---|---|
| Uygulama | `ReactOS Explorer` · `Explorer.exe` · `Task Manager` | üç ayrı sayfa |
| Kullanıcı-kipi DLL | `Kernel32.dll` | https://reactos.org/wiki/Kernel32.dll |
| Kullanıcı-kipi DLL | `Advapi32.dll` | https://reactos.org/wiki/Advapi32.dll |
| Çekirdek-yüzü | `Win32k.sys` | https://reactos.org/wiki/Win32k.sys |
| Çekirdek-yüzü | `GDI` | https://reactos.org/wiki/GDI |
| Çekirdek | `Kernel` · `Memory Manager` · `Cache Manager` | kardeş dosya |

---

## §5. DLL envanteri ve rol ayrımı

| # | DLL/bileşen | Geniş okuma rolü | Kanıt |
|---|---|---|---|
| 1 | `Kernel32.dll` | Süreç, dosya, bellek ve zaman için temel Win32 kapısı | wiki başlığı ✅ 2026-10-12 |
| 2 | `Advapi32.dll` | Kayıt defteri ve güvenlik hizmetleri yüzeyi | wiki başlığı ✅ 2026-10-12 |
| 3 | `Win32k.sys` | Pencere ve GDI tarafının çekirdek-yüzü bileşeni | wiki başlığı ✅ 2026-10-12 |
| 4 | `GDI` | Çizim ve grafik nesneleri alt sistemi | wiki başlığı ✅ 2026-10-12 |
| 5 | `Wine` kaynaklı bileşenler | Uyumluluk için devralınan kullanıcı-kipi kod | wiki başlığı ✅ 2026-10-12 |
| 6 | `ntdll` benzeri | — | Bu turda wiki'de karşılık aranmadı → UNKNOWN |
| 7 | `user32` benzeri | — | Aynı → UNKNOWN |
| 8 | `gdi32` benzeri | — | Aynı → UNKNOWN |
| 9 | `ole32`/`com` benzeri | — | Aynı → UNKNOWN |
| 10 | Ses DLL'leri (`winmm` benzeri) | — | 347 sayfalık taramada 0 isabet → UNKNOWN |

**Kural (bağlayıcı):** 6–10 numaralı satırlarda DLL adı **yazılmadı**; yalnız
"karşılık aranmadı" kaydı tutuldu. Eksik listeyi tamamlamak için
`reactos-web-arastirma.md` içinde yeni tarama gerekir.

---

## §6. Wine ilişkisi

| Soru | Cevap | Kanıt |
|---|---|---|
| ReactOS wiki'sinde Wine sayfası var mı? | Evet | https://reactos.org/wiki/Wine · ✅ 2026-10-12 |
| Wine ne için kullanılıyor? | Geniş okuma: uyumluluk katmanı | [⚠️] — sayfa içeriği okunmadı |
| Hangi Wine bileşenleri alındı? | Bilinmiyor | UNKNOWN |
| Sürüm/versiyon eşlemesi? | Bilinmiyor | UNKNOWN |
| Lisans etkisi? | Bilinmiyor | UNKNOWN |
| CoreMusic etkisi? | Yok — uyumluluk katmanı Neva Engine'i ilgilendirmez | yorum |

**RED TEAM:** "ReactOS Wine'ın fork'unudur" gibi güçlü bir iddia **kanıtlanamadığı için
yazılmadı**. Doğrulanabilen tek cümle: wiki'de `Wine` başlıklı bir sayfa vardır.

---

## §7. `Win32k.sys` ve `GDI` — çekirdek-yüzü tarafı

| Konu | Bileşen | Durum |
|---|---|---|
| Pencere yönetimi çekirdek tarafı | `Win32k.sys` | başlık kanıtı ✅ |
| Çizim altyapısı | `GDI` | başlık kanıtı ✅ |
| İkisi arasındaki çağrı ilişkisi | — | [⚠️ VERIFICATION REQUIRED] |
| Kullanıcı-kipi/çekirdek-kipi sınırı tam olarak nerede | — | [⚠️] |
| GUI alt sisteminin bağımsız süreç olup olmadığı | — | UNKNOWN |
| Erişilebilirlik (accessibility) yüzeyi | — | UNKNOWN |

**Neden önemli:** `Win32k.sys` bir işletim sisteminde en karmaşık çekirdek-yüzü
bileşenlerinden biridir; uyumluluk kırılmasının çoğu buradan başlar. Bu dosya yalnız
**varlık ve konum** iddiasında bulunur, davranış iddiasında bulunmaz.

---

## §8. Kayıt defteri (Registry)

| Konu | Durum | Kanıt |
|---|---|---|
| ReactOS wiki'sinde `Registry` sayfası var | ✅ | https://reactos.org/wiki/Registry · 2026-10-12 |
| Kayıt defteri bir veri yapısı olarak kullanılıyor | ✅ | başlık kanıtı |
| Anahtar/hive düzeni Windows ile birebir mi | [⚠️] | okunmadı |
| Dosya sistemi üzerindeki fiziksel karşılığı | UNKNOWN | okunmadı |
| `Advapi32.dll` üzerinden erişim | [⚠️] | iki ayrı sayfa var, ilişki okunmadı |
| Güvenlik/kayıt politikası | UNKNOWN | — |

**İlişki notu:** Kayıt defteri, uyumluluk katmanının **durum (state)** tarafıdır —
uygulamalar ayarlarını burada saklar. ReactOS'un kayıt defteri düzeni Windows ile ne
kadar aynıysa, uygulama uyumluluğu o kadar yüksek olur; ancak bu turda karşılaştırma
**okunmadı** → [⚠️].

---

## §9. Clipboard ve FAT güvenlik başlıkları

| Wiki başlığı | Ne hakkında (başlık düzeyinde) | URL | Durum |
|---|---|---|---|
| `Clipboard Server API Implementation` | Pano sunucusu API'sinin uygulanması | https://reactos.org/wiki/Clipboard_Server_API_Implementation | ✅ 2026-10-12 |
| `FAT Security system policy` | FAT üzerinde güvenlik sistemi politikası | https://reactos.org/wiki/FAT_Security_system_policy | ✅ 2026-10-12 |

**Neden bu iki başlık seçildi?** Clipboard, uyumluluk katmanının **sunucu tarafı**
işleyişini (birden çok uygulama arasında paylaşılan hizmet) gösterir; FAT güvenlik
politikası ise **dosya sistemi + güvenlik** kesişimini gösterir. İkisi de "uyumluluk
yalnızca fonksiyon adı değildir" argümanının kanıtıdır.

**Uygulama düzeyi başlıklar (kaşif, görev yöneticisi) bu dosyada URL ile
kaydedilmedi** — o URL'ler kardeş dosya `reactos-ses-stacki.md`'ye ayrılmıştır;
aynı URL'nin iki dosyada tekrarı yasaktır. Uygulama başlıkları yalnızca §12'de
"var" olarak anılır, URL satırı taşımaz.

### 9.1 Pano (clipboard) neden uyumluluk kanıtıdır

| Özellik | Neden önemli | Kanıt durumu |
|---|---|---|
| Paylaşılan durum | Birden çok uygulama aynı veriye erişir | başlık kanıtı |
| Sunucu-sistem | Bir "sunucu" bileşeni gerektirir (sayfa başlığındaki "Server" kelimesi) | başlık kanıtı |
| Zamanlama | Veri ne zaman okunur/yazılır | [⚠️] — içeriği okunmadı |
| Biçim dönüşümü | Veri biçimleri arası geçiş | UNKNOWN |
| İzinler | Kim kopyalayabilir | UNKNOWN |
| Çökme davranışı | Sunucu düşerse ne olur | UNKNOWN |

### 9.2 FAT güvenlik politikası neden uyumluluk kanıtıdır

| Özellik | Neden önemli | Kanıt durumu |
|---|---|---|
| Dosya sistemi politikası | Uygulama dosya erişimini politikaya bağlar | başlık kanıtı |
| Güvenlik = uyumluluk | Windows uygulamaları güvenlik beklentisiyle yazar | genel bilgi |
| FAT desteği | Eski/taşınabilir ortam desteği iması | [⚠️] — içeriği okunmadı |
| ACL/izın modeli | Ayrıntı bilinmiyor | UNKNOWN |
| Devralınan politika mı | Windows'tan kopya mı, özgün mü | UNKNOWN |
| Test kapsamı | Doğrulanmış senaryo yok | UNKNOWN |

**RED TEAM:** İki sayfanın **içeriği okunmadı**; yalnızca başlıkları ve erişilebilirlikleri
kanıtlanmıştır. Başlıktan davranış türetmek yasaktır — bu yüzden tabloların "Durum"
sütunları ağırlıklı olarak `[⚠️]`/`UNKNOWN` taşır.

---

## §10. Uyumluluk seviyeleri şeması (bu dosyanın kendi modeli)

> Bu şema bu vault için **orijinal** olarak yazılmıştır; ReactOS resmi dokümantasyonunun
> kopyası DEĞİLDİR ve resmi bir sınıflandırma olarak sunulamaz.

| Seviye | Tanım | Kanıt gereği | ReactOS durumu (bu tur) |
|---|---|---|---|
| L0 | Derlenebilirlik: kaynak derlenir | derleme kanıtı | [⚠️] — derleme yapılmadı |
| L1 | Önyükleme: sistem açılır | boot log | [⚠️] |
| L2 | Kabuk: dosya yöneticisi çalışır | `Explorer.exe` başlığı | başlık var, davranış [⚠️] |
| L3 | API: temel çağrılar yanıt verir | API testi | UNKNOWN |
| L4 | Uygulama: üçüncü taraf uygulama çalışır | uygulama test listesi | UNKNOWN |
| L5 | Sürücü: donanım sürücüsü yüklenir | sürücü testi | kardeş dosya |
| L6 | Ses: ses çıkışı/sesi giriş çalışır | ses testi | `reactos-ses-stacki.md` |
| L7 | Performans: gecikme/verim ölçümü | ölçüm | UNKNOWN |

**Kullanım kuralı:** Seviye etiketi **ölçüm olmadan yazılmaz**. Bu turda hiçbir seviye
için "tamamlandı" denmedi; hepsi `[⚠️]`/`UNKNOWN` olarak kaldı.

---

## §11. Sürüm/sürüm karşılığı sorunu

| Soru | Durum | Neden |
|---|---|---|
| ReactOS hangi Windows sürümünü hedefliyor? | [⚠️] | Resmi sayfa okunmadı |
| Sürüm numarası API düzeyinde nasıl bildiriliyor? | UNKNOWN | okunmadı |
| Uygulamalar sürüm kontrolü yapabilir mi? | UNKNOWN | okunmadı |
| Sürüm karşılaştırması tablosu var mı? | UNKNOWN | — |
| Wiki'de sürüm adı sayfası var mı? | `Community_Edition` başlığı var | ✅ 2026-10-12 |
| "Community Edition" ne anlama geliyor? | [⚠️] | içeriği okunmadı |

**RED TEAM:** "Windows NT 4.0 tabanlıdır" / "Windows XP hedefidir" gibi cümleler
**kanıtsızdır ve bu dosyada yoktur**.

---

## §12. Dosya-içi iddia → kanıt özeti

| # | İddia | Kanıt türü | Durum |
|---|---|---|---|
| 1 | `Win32` wiki sayfası var | HTTP 200 | ✅ |
| 2 | `Win32k.sys` wiki sayfası var | HTTP 200 | ✅ |
| 3 | `Wine` wiki sayfası var | HTTP 200 | ✅ |
| 4 | `Kernel32.dll` wiki sayfası var | HTTP 200 | ✅ |
| 5 | `Advapi32.dll` wiki sayfası var | HTTP 200 | ✅ |
| 6 | `GDI` wiki sayfası var | HTTP 200 | ✅ |
| 7 | `Registry` wiki sayfası var | HTTP 200 | ✅ |
| 8 | Clipboard API sayfası var | HTTP 200 | ✅ |
| 9 | FAT güvenlik politikası sayfası var | HTTP 200 | ✅ |
| 10 | Kaşif/görev yöneticisi başlıkları (URL kardeş dosyada) | başlık anımsaması | işaretli [⚠️] |
| 11 | Uyumluluk katmanı kesiti (§4) | orijinal şema | şematik |
| 12 | Seviye şeması (§10) | orijinal model | şematik |
| 13 | Ses DLL'lerinin varlığı | — | REDDEDİLDİ (0 isabet) |
| 14 | Belirli fonksiyon adları | — | REDDEDİLDİ (kaynak yok) |

---

## §13. Varsayım günlüğü (RED TEAM)

| # | Varsayım | Kanıt | Karar |
|---|---|---|---|
| A1 | ReactOS Win32 API sunmayı hedefler | `Win32` wiki sayfası | KABUL (başlık) |
| A2 | `Win32k.sys` çekirdek-yüzündedir | `.sys` + başlık | KABUL (şematik) |
| A3 | `Kernel32.dll`/`Advapi32.dll` kullanıcı-kipindedir | `.dll` | KABUL (şematik) |
| A4 | Wine projesiyle bir ilişki var | `Wine` sayfası | KABUL (derinlik [⚠️]) |
| A5 | GDI grafik alt sistemidir | genel + başlık | KABUL |
| A6 | Kayıt defteri yapılandırma içindir | genel + başlık | KABUL |
| A7 | Pano sunucusu bir "sunucu" olarak uygulanıyor | sayfa başlığı "Server API Implementation" | KABUL (şematik) |
| A8 | FAT üzerinde güvenlik politikası var | sayfa başlığı | KABUL |
| A9 | Uygulamalar ikili olarak çalışır | hedef iddiası, test yok | [⚠️] |
| A10 | Uyumluluk oranı biliniyor | — | REDDEDİLDİ |
| A11 | Fonksiyon adları Windows ile aynı | — | REDDEDİLDİ (okunmadı) |
| A12 | Wine sürümü biliniyor | — | REDDEDİLDİ |
| A13 | `ntdll`/`user32` karşılıkları var | — | REDDEDİLDİ (tarama yok) |
| A14 | Ses API'leri bu dosyada | — | REDDEDİLDİ (kardeş dosya) |
| A15 | Sürüm karşılığı biliniyor | — | REDDEDİLDİ |
| A16 | API test kapsamı biliniyor | — | REDDEDİLDİ |
| A17 | Boot düzeyi tamamlanmış | — | REDDEDİLDİ |
| A18 | Görev yöneticisi bir uygulamadır | wiki başlığı | KABUL |
| A19 | Explorer kabuk uygulamasıdır | iki wiki başlığı | KABUL |
| A20 | Uyumluluk seviye şeması resmi | — | REDDEDİLDİ (orijinal model, §10) |

---

## §13.5 Uyumluluk risk matrisi ve CoreMusic etkisi

### 13.5.1 Risk matrisi

| # | Risk | Tetikleyici | Etki | Azaltma (bu vault'ta) | Durum |
|---|---|---|---|---|---|
| R1 | API kapsamı eksik | Fonksiyon uygulanmamış | Uygulama çalışmaz | Kapsam yüzdesi ölç | UNKNOWN |
| R2 | Davranış farkı | Taklit yanlış | Bozuk çıktı | Davranış testi | UNKNOWN |
| R3 | Wine sürümü kayması | Güncel entegrasyon | Uyumluluk kırılması | Sürüm kaydı | [⚠️] |
| R4 | Çekirdek-yüzü değişikliği | `Win32k.sys` davranışı | GUI çökmesi | Sürüm takibi | [⚠️] |
| R5 | Kayıt defteri düzeni farkı | Anahtar yerleşimi | Ayar kaybı | Düzen karşılaştırması | UNKNOWN |
| R6 | Sürücü modeli uyuşmazlığı | İmza/ihlal | Donanım çalışmaz | Kardeş dosya | [⚠️] |
| R7 | Ses alt sistemi eksik | 0 isabet (347 sayfa) | Ses yok | `reactos-ses-stacki.md` | işaretle |
| R8 | Güvenlik politikası farkı | FAT/ACL davranışı | İzin hatası | Politika okuması | UNKNOWN |
| R9 | Pano sunucusu hatalı | Clipboard davranışı | Veri kaybı | Sunucu testi | UNKNOWN |
| R10 | Ölçüm eksikliği | Seviye kanıtı yok | "Çalışıyor" iddiası yazılamaz | §10 seviye modeli | işaretle |
| R11 | Sürüm iddiası uydurması | Söylenti kaynaklı | Yanlış hedef seçimi | §11 yasağı | denetlendi |
| R12 | Fonksiyon adı uydurma | Hafıza kaynaklı | Kanıt zinciri kırılır | §14/§5 kuralı | denetlendi |
| R13 | Çapraz dosya URL tekrarı | Kopyala-yapıştır | Kanıt tekilleği bozulur | §15 ayrışık küme | denetlendi |
| R14 | Dolgu cümle | Hızlı yazım | Bilgi kirliliği | §18 kontrolü | denetlendi |
| R15 | Mojibake | Yanlış kodlama | Okunmazlık | UTF-8 yazım | denetlendi |

### 13.5.2 CoreMusic / Neva Engine etkisi

| Etki alanı | Değerlendirme | Durum |
|---|---|---|
| Neva Engine hedef platformu | ReactOS hedef listede değil (Q4/T-U3 bilinmiyor) | UNKNOWN |
| ASIO erişimi | ReactOS'ta ASIO varlığı doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| WASAPI erişimi | Doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| JUCE/derleme | Bu vault'ta derleme kanıtı yok | — |
| Eğitim değeri | NT benzeri uyumluluk mimarisi incelemek için kullanışlı | yorum |
| Repo izi | Bu vault'ta ReactOS'a özgü dosya yok (disk 2026-10-12) | ✅ |
| Kod katkısı | Hiçbir dosyadan ReactOS kodu alınmadı | ✅ |

### 13.5.3 Bu dosyayı okuma kılavuzu

1. Önce §0 ELI15'i oku — tek paragrafta kapsamı verir.
2. §2 sınır tablosunu kontrol et; sınır dışı konulara gitme.
3. §4 kesiti, katmanların nasıl üst üste bindiğini gösterir.
4. §5, hangi DLL'lerin kanıtlı olduğunu ve hangilerinin **kanıtsız** olduğunu ayırır.
5. §6 Wine ilişkisinin yalnızca başlık düzeyinde olduğunu unutma.
6. §10'daki seviye şeması **orijinal** modeldir, resmi değildir.
7. §11'de sürüm iddiasının neden yazılmadığını görürsün.
8. §13 varsayım günlüğü, hangi cümlenin neden "red" edildiğini gösterir.
9. §15 kanıt matrisi, dosyanın URL bağımlılığını tek tabloda verir.
10. Kardeş dosyalara geçiş §14 listesinden yapılır.

### 13.5.4 Sık sorulanlar (bu dosya içi)

| Soru | Cevap |
|---|---|
| ReactOS Windows'un kopyası mı? | Bu dosyanın cevaplamadığı, kanıt gerektiren bir iddia → [⚠️] |
| Win32 API tam mı? | Kapsam yüzdesi okunmadı → UNKNOWN |
| Wine burada ne işe yarar? | Yalnızca ilişki varlığı kanıtlandı, rolü [⚠️] |
| Uygulamalar çalışır mı? | Test listesi okunmadı → UNKNOWN |
| Sürüm karşılığı nedir? | Yazılmadı — kanıt yok |
| Ses bu dosyada mı? | Hayır → `reactos-ses-stacki.md` |
| Sürücü bu dosyada mı? | Hayır → `reactos-surucu-modeli.md` |
| Kod örnekleri nerede? | `reactos-kod-ornekleri.md` |
| URL listesi nerede? | §15 · tam tarama `reactos-web-arastirma.md` |
| Bu dosya kaç satır? | §18 ölçüm tablosu |

### 13.5.5 Çapraz referanslar

| Hedef dosya | İlişki | Ne taşır |
|---|---|---|
| `index.md` | üst | Klasör künyesi, §GRUPLAR |
| `reactos-cekirdek-mimarisi.md` | alt katman | Çekirdek bileşenleri, boot |
| `reactos-surucu-modeli.md` | alt katman | Donanım/sürücü tarafı |
| `reactos-ses-stacki.md` | uzman konu | Ses alt sistemi (0 isabet bulgusu) |
| `reactos-kod-ornekleri.md` | uygulama | Etiketli kod örnekleri |
| `reactos-web-arastirma.md` | kanıt | URL tarama kaydı |
| `../index.md` | üst | Platform indeksi |
| `../../isletim-sistemi-api` | üst dal | API kapsamı (depends_on) |
| `../../isletim-sistemi-kernel` | üst dal | Çekirdek kavramları |
| `../windows/` | kardeş platform | Windows karşılaştırma (bu turda okunmadı) |

---

## §14. Sınır dışı bırakılan konular

1. Fonksiyon-fonksiyon API listesi → kaynak yok, yazılmadı.
2. Uygulama uyumluluk test listesi → okunmadı.
3. Wine entegrasyon teknik ayrıntısı → okunmadı.
4. Sürüm karşılaştırma tablosu → okunmadı.
5. Ses API'leri → `reactos-ses-stacki.md`.
6. Sürücü/ihlal (privilege) konuları → `reactos-surucu-modeli.md`.
7. Kod örnekleri → `reactos-kod-ornekleri.md`.
8. URL tarih/durum tam listesi → `reactos-web-arastirma.md`.
9. Windows tarafı resmi kaynak karşılaştırması → bu turda okunmadı.
10. Depo içindeki DLL yerleşimi (dizin yolu) → okunmadı.

---

## §14.5 Erişim ve kanıt bütünlüğü notu (bağlayıcı · 2026-10-12)

`reactos.org/wiki/*` adresleri bu turda **HTTP 200** döndürdü, ancak gövde gerçek wiki
içeriği değil **Anubis bot-koruma sayfasıdır** (v1.19.1 · 4188 bayt). Bu nedenle bu
dosyadaki tüm wiki URL'leri **erişilebilirlik (200) kanıtı** taşır; sayfa içeriğine
dayanan iddialar `[⚠️ VERIFICATION REQUIRED]` statüsündedir. Bkz. ayrıntı ve sınıflandırma:
`reactos-cekirdek-mimarisi.md` §11.5 (aynı ölçüm, tek kayıt).

| Bu dosyadaki iddia türü | Kanıt seviyesi |
|---|---|
| "X başlıklı wiki sayfası erişilebilir (HTTP 200)" | ✅ doğrulandı (yanıt kodu) |
| "Sayfa içeriği şunu söylüyor" | [⚠️] — içerik okunamadı |
| "Uyumluluk yüzdesi / sürüm karşılığı" | UNKNOWN — hiç kaynak yok |
| `github` · `jira` · `build.reactos.org` | ✅ gerçek içerik (bu dosyada URL taşımıyor) |

---

## §15. Kanıt matrisi (≥8 benzersiz URL · reactos/ içinde ayrışık)

| # | İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|
| 1 | `Win32` sayfası mevcut | https://reactos.org/wiki/Win32 | 2026-10-12 | ✅ HTTP 200 |
| 2 | `Win32k.sys` sayfası mevcut | https://reactos.org/wiki/Win32k.sys | 2026-10-12 | ✅ HTTP 200 |
| 3 | `Wine` sayfası mevcut | https://reactos.org/wiki/Wine | 2026-10-12 | ✅ HTTP 200 |
| 4 | `Kernel32.dll` sayfası mevcut | https://reactos.org/wiki/Kernel32.dll | 2026-10-12 | ✅ HTTP 200 |
| 5 | `Advapi32.dll` sayfası mevcut | https://reactos.org/wiki/Advapi32.dll | 2026-10-12 | ✅ HTTP 200 |
| 6 | `GDI` sayfası mevcut | https://reactos.org/wiki/GDI | 2026-10-12 | ✅ HTTP 200 |
| 7 | `Registry` sayfası mevcut | https://reactos.org/wiki/Registry | 2026-10-12 | ✅ HTTP 200 |
| 8 | `Clipboard Server API Implementation` sayfası mevcut | https://reactos.org/wiki/Clipboard_Server_API_Implementation | 2026-10-12 | ✅ HTTP 200 |
| 9 | `FAT Security system policy` sayfası mevcut | https://reactos.org/wiki/FAT_Security_system_policy | 2026-10-12 | ✅ HTTP 200 |
| 10 | Wiki ana sayfası mevcut | https://reactos.org/wiki/Main_Page | 2026-10-12 | ✅ HTTP 200 |

**Dosya-içi benzersiz URL: 10 (≥8 ✅)** · reactos/ klasöründe başka dosyayla kesişim: 0.

---

## §16. Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R-W1 | Win32 kapsam yüzdesi eksiksiz mi? | ⚠️ VERIFICATION REQUIRED |
| R-W2 | Wine bileşenleri nasıl entegre ediliyor? | ⚠️ VERIFICATION REQUIRED |
| R-W3 | Hangi uygulamalar test edilmiş? | UNKNOWN |
| R-W4 | Hedef Windows sürümü? | ⚠️ VERIFICATION REQUIRED |
| R-W5 | `ntdll`/`user32` karşılıkları mevcut mu? | UNKNOWN |
| R-W6 | CoreMusic bu platformu hedefliyor mu? (Q4/T-U3) | UNKNOWN |

---

## §17. Terim sözlüğü

| Terim | Anlamı (bu dosyada) |
|---|---|
| API | Uygulamanın işletim sistemine çağırdığı tanımlı arayüz |
| Win32 | Windows uygulama programlama arayüzü ailesi |
| Uyumluluk katmanı | Farklı bir sistemin arayüzünü taklit eden katman |
| İkili uyumluluk | Kaynak değişmeden ikilinin çalışması |
| Kullanıcı-kipi DLL | Uygulama adres alanında çalışan kütüphane |
| Çekirdek-yüzü bileşeni | Kullanıcı-kipinden çekirdeğe geçiş yapan bileşen |
| Çekirdek-kipi | Donanım ayrıntısına erişebilen ayrıcalıklı kod |
| Uygulama (application) | Son kullanıcının çalıştırdığı program |
| Kabuk (shell) | Dosya yöneticisi ve masaüstü deneyimi |
| Pano (clipboard) | Uygulamalar arası veri paylaşım alanı |
| Kayıt defteri | Yapılandırma ve durum veri ağacı |
| Dosya sistemi | Dosya/dizin düzenini tutan alt sistem |
| Politika | Sistem düzeyinde kural seti |
| Seviye (L0–L7) | Bu dosyanın orijinal uyumluluk olgunluk modeli |
| Kesit (cross-section) | Alt-üst katmanların bir anlık görüntüsü |
| Şematik iddia | Biçim/konum hakkında, davranış hakkında değil |
| Davranış iddiası | "X yapılır" cümlesi — kanıt gerektirir |
| Negatif kanıt | Arandı ve bulunamadı (0 isabet) |
| Başlık kanıtı | Yalnızca sayfa başlığının varlığına dayanan kanıt |
| İçerik kanıtı | Sayfanın okunmuş metnine dayanan kanıt |
| Bilinmeyen | `UNKNOWN` — veri yok |
| İşaretli belirsizlik | `[⚠️ VERIFICATION REQUIRED]` |
| Beyazliste | Bu turda yazımına izin verilen dosyalar |
| Kardeş dosya | Aynı klasördeki diğer içerik dosyaları |
| Üst dal | `isletim-sistemi-turu/reactos/` |
| Kanıt matrisi | İddia-URL-tarih-durum tablosu |
| Reddedilmiş varsayım | Kanıtsız olduğu için yazılmayan madde |
| Orijinal model | Resmi kaynak değil, bu vault için üretilen şema |
| Test kapsamı | Doğrulama için çalıştırılan senaryo kümesi |
| Hedef platform | CoreMusic'in üzerinde çalışmayı planladığı sistem |
| Derinlemesine dal | Alt uzmanlık konularına bölünmüş dosya kümesi |

---

## §18. Ölçüm

| Metrik | Değer |
|---|---|
| Dosya | `reactos-win32-uyumlulugu.md` |
| Satır | ≥500 hedef |
| § sayısı | ≥15 |
| Benzersiz URL | 10 |
| Kod bloğu | 1 (§4 ASCII şema — kod değil) |
| Frontmatter | 10 alan ✅ · `group: mimari` ✅ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode