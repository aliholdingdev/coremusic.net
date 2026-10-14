---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio
evidence:
  - https://github.com/reactos/reactos/tree/master/drivers/multimedia
  - https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/README
  - https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/CMakeLists.txt
  - https://github.com/reactos/reactos/tree/master/dll/win32/winmm
  - https://raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec
validation:
  - "2026-10-13 · README tam metni raw.githubusercontent üzerinden indirildi (birebir alıntı) · CMakeLists.txt indirildi (tek satır kanıt) · audio/CMakeLists.txt 404 (negatif kanıt) · katman adları yalnız README'de geçen adlar"
  - "2026-10-13 · wiki (wiki.reactos.org) bot-duvarı (Anubis) arkasında → wiki'den iddia alınmadı"
open_questions:
  - "Vista sonrası yığın (usermode ses servisi) ReactOS'ta var mı → README Vista'dan söz eder, ReactOS uygulaması UNKNOWN"
  - "A-Q3 AvStream kapsamı ReactOS'ta mevcut mu → UNKNOWN (README 'XP and DirectX 8 versions' der; ReactOS'un hangisini uyguladığı kanıtlanmadı)"
group: mimari
---

# reactos-audio-anayasa — Kimlik ve Katman Yığını

> **Bu dosya ne değildir:** Kurulum rehberi, kod incelemesi veya karşılaştırma
> dosyası değildir. Bu dosya ReactOS ses yığınını **yalnız depo `drivers/multimedia/README`
> metni ve depo CMake gerçeğiyle** kurar; geçmeyen hiçbir katman adı yazılmaz.
> Erişilemeyen her iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretidir.

**ELI15:** ReactOS ses yığını, Windows NT'nin klasik yolunu izler: uygulama sesi
`WINMM.DLL` üzerinden çağırır → kullanıcı düzeyi `WDMAUD` bu isteği çekirdeğe taşır →
çekirdekte `KS.SYS` (Kernel Streaming) akışı yönetir → `PORTCLS.SYS` gerçek ses
sürücüsüne yardım eder. Ama bu defterin başına **2006'dan beri** şöyle bir not
düşülmüş: *"Much of this code exists only as stubs for future implementation."* — yani
çizilen mimari bir vaat/iskelet; kendisi de "çalışıyor mu bilmiyorum" diyor.

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio/reactos-audio-anayasa` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/reactos-audio` |
| Kademe | 5 · yaprak-modül |
| group | `mimari` |
| Doğrulama tarihi | 2026-10-13 |
| Birincil kaynak | `drivers/multimedia/README` — raw.githubusercontent üzerinden tam metin indirildi |
| İkincil kaynak | `drivers/multimedia/CMakeLists.txt` · `dll/win32/winmm/winmm.spec` |
| README tarihi/yardımcısı | 3 Aralık 2006 · Andrew Greenwood (README imzası birebir) |

---

## §2 Bu dosyanın kanıt kaynakları

| Etiket | Kaynak | URL | 2026-10-13 Durum |
|---|---|---|---|
| [R1] | `drivers/multimedia/` depo ağacı (GitHub) | https://github.com/reactos/reactos/tree/master/drivers/multimedia | ✅ GET 200 |
| [R2] | `drivers/multimedia/README` — tam metin | https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/README | ✅ GET 200 · tam metin indirildi · alıntılar birebir |
| [R3] | `drivers/multimedia/CMakeLists.txt` | https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/CMakeLists.txt | ✅ GET 200 · içerik: `add_subdirectory(bdasup)` tek satır |
| [R4] | `drivers/multimedia/audio/CMakeLists.txt` | https://raw.githubusercontent.com/reactos/reactos/master/drivers/multimedia/audio/CMakeLists.txt | ❌ 404 — `audio/` altında CMakeLists **yok** (negatif kanıt) |
| [R5] | `dll/win32/winmm/` depo ağacı | https://github.com/reactos/reactos/tree/master/dll/win32/winmm | ✅ GET 200 |
| [R6] | `dll/win32/winmm/winmm.spec` — export listesi | https://raw.githubusercontent.com/reactos/reactos/master/dll/win32/winmm/winmm.spec | ✅ GET 200 · tam metin indirildi |
| [W1] | ReactOS wiki (bot-duvarı) | https://wiki.reactos.org/ | ⚠️ Anubis bot-duvarı — kullanılmadı |

> Not: Wiki, bot-duvarı arkasında olduğu için bu dosyadaki **hiçbir** iddia
> wiki'den alınmamıştır; kaynak R2/R3/R4/R6 ile sınırlıdır.

---

## §3 Kimlik

| Öğe | Değer | Kaynak |
|---|---|---|
| Platform | ReactOS — Win32-uyumlu açık kaynak işletim sistemi | üst dal `../../isletim-sistemi-turu/reactos/` |
| Ses yolu | Windows-uyumlu klasik yığın (WINMM → WDMAUD → KS) | [R2] README zinciri |
| Kapsam beyanı | "This is a directory for the kernel streaming components, along with multimedia kernel-mode drivers." | [R2] §giriş (birebir) |
| Olgunluk | "Much of this code exists only as stubs for future implementation." | [R2] §giriş (birebir) |
| Lisans/not | Depo erişimi açık (github.com/reactos/reactos) · README "should be moved to the main include tree later" diye not düşüyor | [R1] · [R2] |
| Derleme gerçeği | `drivers/multimedia/CMakeLists.txt` yalnız `bdasup` alt dizinini derliyor; `audio/` derlemeye dahil değil | [R3] · [R4] |
| CoreMusic sahibi agent | Windows Software Engineer (WASAPI/driver/PLAT) — ReactOS için routing yok, deep-dal sahibi bu ajan | `.ai/AGENTS.md` §6 |

---

## §4 Katman Yığını (README kanıtlı — birebir alıntılarla)

| Katman | Bileşen | Görev (README metni — birebir) | Durum |
|---|---|---|---|
| L0 · Uygulama | Win32 uygulaması | Ses çağırmak ister | çerçeveler |
| L1 · Kütüphane | `WINMM.DLL` | MME-style API'nin sahibi (README: "a MME-style driver (for WINMM.DLL)") | ✅ depoda `dll/win32/winmm/` [R5] |
| L2 · Köprü | `WDMAUD.DLL` / `WDMAUD.SYS` | "Effectively a relay from usermode to kernelmode" | README tanığı [R2] |
| L3 · Çekirdek akış | `KS.SYS` (Kernel Streaming) | "Deals with streaming media. This is the core component for this, and can be interacted with from usermode (usually via DirectX.)" · COM-style arayüzler | README tanığı [R2] |
| L4 · Topoloji | `SYSAUDIO.SYS` (SysAudio) | "Filter graph builder (deals with topology.) Not sure what this does yet." | README tanığı — README bile emin değil |
| L5 · Sürücü iskeleti | `PORTCLS.SYS` (Port Class) | "Used by WDM audio drivers, this handles many common tasks required for audio drivers." · COM-style arayüzler | README tanığı [R2] |
| L6 · Donanım sürücüsü | WDM ses sürücüleri (README: "Optional drivers") | PortClass üzerine yazılır | README tanığı [R2] |

### §4.1 Bileşen başına tam README alıntıları

#### §4.1.1 KS.SYS — Kernel Streaming

| Satır | README metni (birebir) |
|---|---|
| Başlık | "== Kernel Streaming (KS.SYS) ==" |
| Görev | "Deals with streaming media. This is the core component for this, and can be interacted with from usermode (usually via DirectX.)" |
| Sürüm notu | "XP and DirectX 8 versions of this component provide an additional suite of AvStream API functions." |
| Arayüz modeli | "COM-style interfaces are used with this component." |

**Yorum (kanıta bağlı):** README bu bileşeni çekirdeğin "core component'i" olarak
tanımlar ama AvStream'in hangi sürümde ReactOS'ta olduğu belirtilmez → A-Q3.

#### §4.1.2 SYSAUDIO.SYS — SysAudio

| Satır | README metni (birebir) |
|---|---|
| Başlık | "== SysAudio (SYSAUDIO.SYS) ==" |
| Görev | "Filter graph builder (deals with topology.)" |
| Durum itirafı | "Not sure what this does yet." |

**Yorum (kanıta bağlı):** README yazarı 2006'da topoloji katmanının ne işe
yaradığını henüz bilmediğini yazıyor — bu, "anayasa"nın kendi eliyle imzaladığı
bir bilinmezliktir; `UNKNOWN` etiketi yazarın ifadesidir, tahmin değildir.

#### §4.1.3 PORTCLS.SYS — Port Class

| Satır | README metni (birebir) |
|---|---|
| Başlık | "== Port Class (PORTCLS.SYS) ==" |
| Görev | "Used by WDM audio drivers, this handles many common tasks required for audio drivers." |
| Arayüz modeli | "COM-style interfaces are used with this component." |

#### §4.1.4 WDMAUD.DLL / WDMAUD.SYS — WDM Audio

| Satır | README metni (birebir) |
|---|---|
| Başlık | "== WDM Audio (WDMAUD.DLL / WDMAUD.SYS) ==" |
| Rol | "Effectively a relay from usermode to kernelmode, the usermode portion is a MME-style driver (for WINMM.DLL) which tells the kernelmode portion what to do." |
| Etkileşim | "This typically involves interacting with KS.SYS in some way." |
| Vista notu | "In Vista, the kernelmode portion is no longer present and the usermode portion interacts with the Vista audio stack" (metin burada kesilir — devamı README'de Vista paragrafı) |

#### §4.1.5 include/ ve tarihçe

| Satır | README metni (birebir) |
|---|---|
| include/ | "The 'include' directory exists as a place to store the KS related headers. It should be moved to the main include tree later." |
| İmza | "Andrew Greenwood / 3rd December, 2006" |

---

## §5 Derleme gerçeği (CMake kanıtlı — bu dosyanın en sert bulgusu)

| # | İddia | Kanıt | Sonuç |
|---|---|---|---|
| D1 | `drivers/multimedia/` alt dizinleri derleniyor | [R3] CMakeLists.txt içeriği **tek satır**: `add_subdirectory(bdasup)` | ❌ **yalnız `bdasup` derleniyor** |
| D2 | `drivers/multimedia/audio/` kendi derleme tanımına sahip | [R4] `audio/CMakeLists.txt` → **HTTP 404** | ❌ dosya yok → `audio/` derlemeye dahil edilemez |
| D3 | `audio/` altındaki 5 alt dizin (`ksfilter` · `wdm` · `portcls` · `optional` · `misc`) üretime giriyor | D1 + D2 birlikte | ❌ kanıt yok — README'nin "stubs" ifadesiyle uyumlu |

**Sonuç (kanıta bağlı):** `drivers/multimedia/audio/` ağacı depoda mevcuttur ama
**CMake grafiğine bağlı değildir** — README'nin *"exists only as stubs"* beyanı
derleme düzeyinde de desteklenir. Bu üç kanıt (R2+R3+R4) birlikte okunur; tek
başına hiçbirisi tek başına bu sonucu vermez.

---

## §6 Vista notu ve AvStream notu (README'nin kendi ifadesi)

| İddia | README metni (birebir) | Yorum / Etiket |
|---|---|---|
| Vista'da `WDMAUD.SYS` çekirdek parçası yok olur | "In Vista, the kernelmode portion is no longer present and the usermode portion interacts with the Vista audio stack" | ReactOS'un hangi yolu (XP klasik mi Vista mı) izlediği **UNKNOWN** → ⚠️ VERIFICATION REQUIRED (A-Q1) |
| XP ve DirectX 8 sürümleri AvStream ekler | "XP and DirectX 8 versions of this component provide an additional suite of AvStream API functions." | README kanıtlı · ReactOS'un kapsaması UNKNOWN (A-Q3) |
| COM-style arayüzler | KS.SYS ve PORTCLS.SYS bölümlerinde iki kez: "COM-style interfaces are used with this component." | README kanıtlı |

---

## §7 Anayasal Kurallar (bu deep-dal için bağlayıcı)

| # | Kural | Gerekçe |
|---|---|---|
| A1 | Katman adları **yalnız** depo README'sinden alınır; `wdmaud`/`winmm` dışında Win32 ses API adı (DirectSound, WASAPI vb.) bu dosyaya **yazılmaz** | Zero-hallucination |
| A2 | "stub" etiketi taşıyan bileşen üretim-çalışıyor sayılmaz; derleme iddiası yalnız CMake kanıtıyla yazılır (§5) | README + CMake açık kanıtı |
| A3 | Çekirdek/usermode sınırı (`WDMAUD.SYS` vs `.DLL`) her tabloda ayrılır | karışıklık önleme |
| A4 | Wiki içeriği bot-duvarı arkasında olduğundan wiki'den iddia alınmaz | [W1] negatif kanıt |
| A5 | Karşılaştırma `../wasapi/` ile **bu dosyada yapılmaz** (ayrı dal) | sınır temizliği |
| A6 | README alıntıları tırnak içinde ve birebir yazılır; parafraz "≈" işaretiyle ayrılır | kanıt bütünlüğü |

---

## §8 Kardeş ve üst bağlar

| Bağ | İlişki |
|---|---|
| `index.md` (üst) | künye, envanter, açık sorular |
| `reactos-audio-surucu.md` (kardeş) | L5/L6 sürücü ağacı detayı + `audio/` alt dizin envanteri |
| `reactos-audio-ornekler.md` (kardeş) | L1 WINMM `winmm.spec` export envanteri |
| `../wasapi/` | Windows gerçek yığını (karşılaştırma kapalı) |
| `../../isletim-sistemi-turu/reactos/reactos-ses-stacki.md` | negatif kanıt + doğrulama planı (üst dal) |

---

## §9 Açık sorular

| # | Soru | Etiket | Yeni kanıtla durum |
|---|---|---|---|
| A-Q1 | ReactOS hangi Vista-öncesi/sonrası ses modelini hedefliyor? | ⚠️ VERIFICATION REQUIRED | Açık — README yalnız Vista gerçeğini anlatır, ReactOS seçimi yazılmaz |
| A-Q2 | `SYSAUDIO.SYS` topoloji durumu nedir? | UNKNOWN | Açık — README'nin kendi ifadesi: "Not sure what this does yet." |
| A-Q3 | AvStream kapsamı ReactOS'ta mevcut mu? | UNKNOWN | Açık — "XP and DirectX 8 versions" der; ReactOS'un uygulaması kanıtlanmadı |
| A-Q4 | `audio/` ağacı derlemeye ne zaman girer/girecek mi? | ⚠️ VERIFICATION REQUIRED | CMake kanıtı eklendi (§5) — hâlâ dahil değil; gelecek planı bilinmiyor |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
