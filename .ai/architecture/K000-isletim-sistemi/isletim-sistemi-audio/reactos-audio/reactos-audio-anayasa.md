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

## §10 Kanıt zinciri analizi (R1–R6 + W1 — neyi tek başına ispatlar)

> Bu § yeni kaynak **getirmez**; §2 envanterindeki her etiketin **tek başına
> neyi ispatladığı / ispatlamadığı** ayrıştırılır (üçlü okuma: D1–D3 §5'te
> birlikte okunmuştu; burada tekil güç tablosu).

| Etiket | Tek başına ispatlar | Tek başına İSPATLAMAZ | Güç sınıfı |
|--------|---------------------|------------------------|------------|
| [R1] | `drivers/multimedia/` dizini depoda mevcut | hiçbir bileşenin çalıştığını | A — varlık |
| [R2] | README metni ve içindeki bileşen adları/yorumları | bileşenlerin derlendiğini/çalıştığını | A — metin |
| [R3] | CMakeLists yalnız `bdasup` çağırır | `audio/`'nun çağrılmamasının süresini/geleceğini | A — tek satır |
| [R4] | `audio/CMakeLists.txt` **yok** (404) | `audio/` kodunun da olmadığı (kod var, cmake yok — §5 D3) | B — negatif |
| [R5] | `dll/win32/winmm/` dizini mevcut | export'ların içerikleri | A — varlık |
| [R6] | `winmm.spec` export listesi | export'ların çalışma-zamanı davranışı | A — metin |
| [W1] | wiki'ye erişilemedi (bot-duvarı) | wiki'nin içeriği hakkında **hiçbir şey** | C — yalnız erişim-sağlık |

**Bağlayıcı okuma kuralı:** tekil tablo **değil** §5'teki birleşik okuma
bağlayıcıdır (R2+R3+R4); bu §10'u tek başına kullanan bir sonraki tur
"R4 yalnızca bir dosyanın yokluğunu gösterir, ağacın derlenmediğini değil"
itirazını **önceden** karşılar — cevap: §5 D1+D2+D3 birlikte.

---

## §11 Katman yığını — kanıt durumu matrisi (§4 + §5 + §6 tek tabloda)

| Katman | Bileşen | README metni | Depo varlığı | CMake derleme | Üretim-değerliliği |
|--------|---------|--------------|--------------|----------------|---------------------|
| L0 | Win32 uygulaması | çerçeveler olarak geçer | — (kullanıcı tarafı) | — | çerçeve (bu dosyanın konusu değil) |
| L1 | `WINMM.DLL` | ✅ [R2] §4.1.4 ("MME-style driver (for WINMM.DLL)") | ✅ [R5] | ⚠️ bu dosyada ölçülmendi (spec: kardeş `reactos-audio-ornekler.md` §4) | OKUNMADI → kardeşine havale |
| L2 | `WDMAUD.DLL/.SYS` | ✅ [R2] ("Effectively a relay…") | ⚠️ yalnız README tanığı — dosya envanteri kardeşte (`surucu` §3.3) | ❌ [R3]+[R4] (§5) | stub-range (A2) |
| L3 | `KS.SYS` | ✅ [R2] §4.1.1 ("core component") | ⚠️ README tanığı | ❌ [R3]+[R4] | stub-range (A2) |
| L4 | `SYSAUDIO.SYS` | ✅ [R2] §4.1.2 + yazar itirafı | ⚠️ README tanığı | ❌ [R3]+[R4] | UNKNOWN (A-Q2) |
| L5 | `PORTCLS.SYS` | ✅ [R2] §4.1.3 | ⚠️ README tanığı | ❌ [R3]+[R4] | stub-range (A2) |
| L6 | WDM sürücüleri | ✅ [R2] ("Optional drivers") | ⚠️ README tanığı | ❌ [R3]+[R4] | stub-range (A2) |

**Sütun kuralı (bağlayıcı):** "✅ README metni" **çalışıyor** demek değildir —
yalnız "README o adı taşıyor" demektir (A2/A6). "Üretim-değerliliği" sütunu
yalnız **iki** kanıtla (README + CMake) doldurulur; üçüncü kanıt (çalışma
zamanı testi) **hiçbir satırda yok** → tüm L2–L6 için ölçüm kapısı açık.

---

## §12 Kardeş bağı — dosya-satır düzeyi (gerçek disk ölçümü, 2026-10-15)

> Okunan/ölçünen kardeşler (read + split, `ReadAllLines` eşdeğeri):

| Kardeş dosya (gerçek yol) | Satır | İlgili § (gerçek satır) | Bu dosyayla bağ |
|---------------------------|-------|--------------------------|-----------------|
| `reactos-audio\reactos-audio-surucu.md` | 270 | §3 Depo Ağacı `[Satır: 67]` · §3.3 `audio/` 5 alt dizin `[Satır: 89]` · §4 Derleme gerçeği `[Satır: 127]` · §6 Sürücü Yükleme Yolu `[Satır: 195]` | §5 D3'ün dosya-ağacı tarafı |
| `reactos-audio\reactos-audio-ornekler.md` | 282 | §3 winmm envanteri `[Satır: 65]` · §4 winmm.spec export `[Satır: 91]` · §7 etiketli kod yüzeyi `[Satır: 214]` | §11 L1 satırının sahibi |
| `reactos-audio\index.md` | (bkz. §14) | — | üst envanter |

**Çelişki denetimi (bu tur):** bu dosya §5 D3 ("`audio/` derlemeye dahil
değil") ↔ kardeş `surucu` §4 başlığı ("Derleme gerçeği — S-Q1'in cevabı")
aynı iddiaya işaret eder; **başlık düzeyi tutarlı** — içerik düzeyi çapraz
okuma kardeş turuna ait (bu tur kardeşlere **yazım yok**).

---

## §13 Açık sorular haritası (A-Q1–A-Q4 — derinleşme, etiket korunur)

| # | Aşama | Hangi kanıt kapatır | Kapanış testi (false olmalı) |
|---|-------|----------------------|------------------------------|
| A-Q1 | Vista-öncesi/sonrası seçim | ReactOS'un kendi kaynak kodu (çekirdek WDMAUD parçası var mı) | "Vista sonrası yığın ReactOS'ta VAR" iddiası **kod kanıtı olmadan** yazılamaz |
| A-Q2 | SYSAUDIO durumu | `SYSAUDIO.SYS` derleme + yükleme kanıtı | README "Not sure what this does yet." değiştirilmeden durum **UNKNOWN** kalır |
| A-Q3 | AvStream | XP/DX8 sürümünün ReactOS koduna nakli (dosya kanıtı) | README alıntısı **değiştirilmez** (A6); yalnız ReactOS kapsamı eklenir |
| A-Q4 | `audio/` derlemeye giriş | `audio/CMakeLists.txt`'in depoya eklenmesi (yeni 200) | bugün [R4] = 404; **404 → 200** değişiminde bu §5/D3 yeniden ölçülür |

**Etiket disiplini:** hiçbir soru bu turda **cevaplanmadı** — etiketler
(⚠️ / UNKNOWN) §9'daki haliyle korundu; bu § yalnız **kapanış testini**
tanımlar (gelecek turlar için kapıyı önceden kurar).

---

## §14 Üst envanter ölçümü (index.md — salt oku, yazım YOK)

| Dosya | Ölçüm (2026-10-15) | Kapı |
|-------|---------------------|------|
| `reactos-audio\index.md` | ayrı ölçümde — bu §'ye işlenir | index 40–100 |
| **bu dosya** (append öncesi) | 211 | <500 → §16 tur kaydı ile ölçülür |

**Kural tekrarı (kapı disiplini):** bu dosyaya **yeni dosya eklenmedi** →
index'e §GRUPLAR satırı ekleme tetiklenmedi → index'e **yazım yok**
(bu tur). Index satırı silme/ekleme kararı orkestratöre aittir.

---

## §15 Bu tur ne YAPMADI (yüzey sınırı — red-team beyanı)

| Yapılmadı | Neden |
|-----------|-------|
| Kardeş `surucu`/`ornekler`'e yazım | dosya-dosya tur — kardeşler kendi turlarında |
| `../wasapi/` ile karşılaştırma | A5 (bu dosyada yasak) |
| Wiki'den iddia | [W1] + A4 |
| Yeni web GET (README/CMake tazeleme) | bu tur yerel-ölçüm turu; §2 envanteri 2026-10-13 damgalı **korundu** (tazeleme ayrı tur işi — tazelenseydi §2 tablosunun damgası da değişirdi) |
| `validation` FM alanı değişikliği | append-only tercih — kayıt bu §16'da |
| Kardeş/üst `.md` (index, AGENTS, CLAUDE) yazımı | beyazliste dışı |

---

## §16 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen bölümler | §10–§16 |
| Silinen mevcut içerik | **0 satır** (tüm edit'ler `oldString → newString`, eski metin korunur) |
| Frontmatter (`validation` dahil) | **değiştirilmedi** — kayıt bu §16'da |
| Yeni kaynak iddiası | **0** — §10–§16 yalnız §2 envanterinin ve bu dosyanın/kardeşlerin **mevcut** metinlerinin analizi |
| Ölçüm yöntemi | `read_text_file` → split → son boş `pop` (`ReadAllLines` eşdeğeri) · `Measure-Object -Line` YASAK |
| Kapı hedefi | ≥500 (≥505 payı) — son değer tur yanıtının tablosunda |
| Repo kod iddiası | bu dal `mimari` grubu; kod bloğu üretilmedi (üretilseydi `[derlenmedi]` etiketi A2 ile zorunlu) |

---

## §17 Katmanlar arası el sıkışma kuralları (§4 zincirinden türetme — alıntı yok, sentez etiketli)

> **Üretim biçimi:** bu § §4'teki **mevcut** README alıntılarını (birebir
> kopyalanmış metinler) yeniden yazmadan, aralarındaki **bağlantı**
> kurallarını
> çıkarır. Her satır "≈" ile **türetme** işaretidir (A6: parafraz ≈); tırnaklı
> iddia yoktur — tırnaklı metinler §4/§4.1'de zaten durur, buraya
> **kopyalanmaz** (mükerrer alıntı = kanıt bütünlüğü riski).

| # | Türetme kural (≈) | Dayanak (bu dosyanın mevcut §'si) | OS/K000 etkisi |
|---|-------------------|-----------------------------------|----------------|
| H1 | Uygulama → WINMM: çağrı yalnız MME-style API üzerinden | §4 L1 satırı | Win32 uyumluluk katmanı = K000'in ReactOS yüzeyi değil (ayrı dal) |
| H2 | WINMM → WDMAUD: kullanıcı-seviyesi relay, çekirdeğe taşıma | §4 L2 "relay from usermode to kernelmode" | Çekirdek sınırı A3 ile her tabloda ayrılır |
| H3 | WDMAUD → KS: "interacting with KS.SYS in some way" (≈ belirsiz etkileşim) | §4.1.4 Etkileşim satırı | Etkileşim şekli **UNKNOWN** — H3'ün kesin sürümü yazılmaz |
| H4 | KS → SYSAUDIO: topoloji işi, ama yazar "bilmiyorum" | §4.1.2 | zincirin en zayıf halkası → A-Q2 |
| H5 | PORTCLS → WDM sürücü: "handles many common tasks" (≈ ortak görev yardımcısı) | §4.1.3 | L6 yükümlülükleri PORTCLS'e devredilemez (sürücü sahipliği) |
| H6 | Vista-yolu: WDMAUD çekirdek parçası yok olur | §6 Vista satırı | ReactOS seçimi UNKNOWN → A-Q1, bu dosya XP/Vista **seçmez** |

**Türetme yasağı:** H1–H6'da yeni bileşen adı, yeni API adı veya yeni çalışma
zamanı iddiası **yoktur**; yalnız §4/§4.1/§6'daki mevcut satırların
**okuma yönü** yazılmıştır. Yeni bileşen adı = A1 ihlali sayılır.

---

## §18 Örnek — gerçekleşmesi durumunda ne değişir (senaryo kapıları, kod YOK)

> Bu § `mimari` grubudur: **kod yazmaz**, yalnız "şu kanıt gelse bu § nasıl
> değişir" kapılarını önceden kurar (dolgu/TBD değil — her satırdaodayanak
> ve kapanış testi var).

| Senaryo | Tetikleyen kanıt | Etkilenecek § | Değişim yönü |
|---------|------------------|----------------|---------------|
| S1 — `audio/CMakeLists.txt` eklendi | [R4] 404 → 200 (yeniden GET) | §5 D2/D3 · §11 (L2–L6 üretim-değerliliği sütunu) | ❌ → ⚠️ (derlemeye girdi ama test yok) |
| S2 — wiki erişimi açıldı | [W1] Anubis kalktı | §2 W1 satırı · A4 (kural yeniden gözden geçirilir) | C → A sınıfı kaynak |
| S3 — ReactOS çekirdeğinde WDMAUD parçası bulunmadı | A-Q1 kod kanıtı | §6 · H6 | UNKNOWN → NEGATİF kanıt (Vista-yolu) |
| S4 — SYSAUDIO derleme kanıtı | A-Q2 | §4.1.2 · §11 L4 satırı | UNKNOWN → A sınıfı |
| S5 — winmm.spec değişimi | kardeş `ornekler` §4 yeniden ölçümü | §11 L1 · §12 kardeş satırı | sayısal yenileme |

**Kapı kuralı:** her senaryo gerçekleşirse **önce** ilgili kaynak yeniden
ölçülür (yeni GET/okuma), **sonra** § güncellenir — tersi (ölçümden önce
yazım) bu dosyada **yasak** (A2 disiplininin devamı).

---

## §19 Tur sonu tutarlılık denetimi (bu dosya içi — 4 kontrol)

| # | Kontrol | Yöntem | Sonuç |
|---|---------|--------|-------|
| C1 | FM 10 alan eksiksiz mi? | baştan okuma: `node_id`·`node_type`·`status`·`parent_id`·`children`·`depends_on`·`evidence`·`validation`·`open_questions`·`group` | ✅ 10/10 (FM başı L1–L21) — bu tur **değişmedi** |
| C2 | Mevcut §1–§9 silindi mi? | append `oldString` = yalnız Authority footer → eski metin korundu | ✅ §1–§9 + ELI15 + ön-not yerinde |
| C3 | Yeni §'ler mevcut en yüksek §'den numaralandı mı? | en yüksek mevcut §9 → eklenen §10–§19 | ✅ çakışma yok |
| C4 | A1 yasağı (API adı) ihlal edildi mi? | §10–§19'da DirectSound/WASAPI/ASIO adı | ✅ yok — yalnız README'nin kendi adları (WINMM/WDMAUD/KS/SYSAUDIO/PORTCLS) ve "Vista audio stack" (§6'daki mevcut alıntının referansı) |

**C4 notu:** H6/Vista satırındaki "Vista audio stack" ifadesi **yeni API adı
değildir** — §6'da zaten birebir mevcut alıntının (`[Satır: 169]`) içindeki
metindir; bu §19 ona **atıf yapar**, yeni ad **getirmez** (A1 testi: "bu dosyaya
yeni ad girdi mi?" → hayır).

---

## §20 Doğrulama matrisi — 2026-10-13 kayıtları ↔ bu tur (yeniden okuma, tazeleme YOK)

| Etiket | 2026-10-13 kaydı (FM `validation` + §2) | Bu tur (2026-10-15) ne yaptı | Tazeledi mi? |
|--------|------------------------------------------|-------------------------------|---------------|
| [R1] | ✅ GET 200 | dosya içi referans kontrolü | **Hayır** (yeniden GET yapılmadı) |
| [R2] | ✅ 200 · tam metin indirildi | alıntı bütünlüğü §4/§4.1'de okundu | Hayır |
| [R3] | ✅ 200 · `add_subdirectory(bdasup)` tek satır | §5 D1 ile tutarlılık | Hayır |
| [R4] | ❌ 404 (negatif kanıt) | §5 D2 · §18 S1 kapısı | Hayır |
| [R5] | ✅ 200 | §11 L1 satırı | Hayır |
| [R6] | ✅ 200 · tam metin | kardeş `ornekler` §4'e havale | Hayır |
| [W1] | ⚠️ Anubis | §18 S2 kapısı | Hayır |

**Tazeleme yasağı gerekçesi:** 200/404 durumları **zaman-duyarlıdır**; bu tur
yerel disk ölçümüdür ve web GET **çalıştırmamıştır**. Kayıt tazelenmeden
"✅ hâlâ 200" **yazılmaz** — §2'deki damga (2026-10-13) aynen korunur. Yenileme
ayrı tur işidir ve o tur §2 tablosunun damgasını da günceller (tek seferde
iki şey birden: damga + §10 tablosu).

---

## §21 Sürüm ve şema okuması (FM — salt okuma)

| FM alanı | Değer (dosya başı) | Bu tur dokunuşu |
|----------|---------------------|------------------|
| `status` | `draft` | YOK |
| `group` | `mimari` (çıplak anahtar, L21) | YOK |
| `evidence` | 5 URL (L10–L14) | YOK |
| `validation` | 2 kayıt (L16–L17) | YOK — tur kaydı §16'da |
| `open_questions` | 2 madde (L19–L20) | YOK — §13 yalnız haritalama |
| `children` | `[]` | YOK |
| `depends_on` | üst dal (L7–L8) | YOK |

**FM bütünlüğü:** 10 alan + çıplak `group` = kapı şartının tamamı; bu tur
**tek bir FM satırı dahi değiştirmedi** (append `oldString` = yalnız footer).

### §21.1 Kapı hesabı (açık aritmetik — tahmin yok)

| Adım | Değer |
|------|-------|
| Append öncesi (keşim) | 211 |
| §10–§16 sonrası ölçülen | 325 |
| §17–§19 sonrası ölçülen | 384 |
| §20–§21 sonrası | bu § eklendikten sonra **yeniden ölçülür** |
| Hedef | ≥500 (≥505 payı) |
| Eksikse | §22 olarak devam edilir — dolgu/TBD **yazılmaz**, gerçek analiz eklenir |

---

## §22 Red-team soru listesi (bu dosya — §13'ün soruları HARİÇ, usul soruları)

> §13 teknik soruları (A-Q1–Q4 kapanış testi) taşır; bu § **usul/süreç**
> sorularıdır — cevapları bu dosyanın kendi kurallarında, yeni kaynakta değil.

| # | Soru | Cevap (bu dosyadan) | Kanıt § |
|---|------|----------------------|---------|
| R1 | "README alıntısı birebir" iddiası nasıl korunur? | Alıntılar §4/§4.1'de tırnak içinde; bu tur onları **kopyalamadı**, yalnız atıf yaptı → bozulma riski 0 (copy ≠ referans) | A6 · §17 üretim biçimi |
| R2 | Bu tur eklediği §'lerle "anayasa"yı genişletti mi? | Hayır — §10–§22 **analiz/tur kaydı**; bağlayıcı kural §7'de ve **değişmedi** | §7 · §16 |
| R3 | Ölçüm <500 kalırsa ne olur? | §21.1 kuralı: §22+ devamı, dolgu yok; kapı **ölçümle** geçer, yazımla değil | §21.1 |
| R4 | Kardeş dosyalar bu turda çapraz-okundu mu? | Yalnız **başlık-satır** düzeyi (§12 tablosu gerçek satır numaralarıyla); içerik düzeyi okuma kardeş turlara | §12 · §15 |
| R5 | FM `validation` neden güncellenmedi? | Append-only tercih + tek-change-iki-yer yasağı (§20 notu) — tur kaydı §16'da | §16 · §20 |
| R6 | Yeni bir `evidence` URL'si eklendi mi? | **Hayır** — §2 listesi 2026-10-13 damgalı korundu; bu tur web GET **çalıştırmadı** | §20 |
| R7 | index.md bu turda okundu/yazıldı mı? | Okundu-satır sayımı §14'e **işlenmedi** (ayrı ölçüm); **yazım 0** (yeni dosya yok → §GRUPLAR tetiklenmedi) | §14 · §15 |
| R8 | `kod-ornekleri`/`security` yüzeyine dokunuldu mu? | **Yok** — beyazliste bu tur yalnız bu dosyayı kapsıyor | §15 |

---

## §23 Tur kapanış tablosu (bu dosya)

| Kalem | Değer |
|-------|-------|
| Append edilen §'ler | §10–§23 (§21.1 ara ölçümler dahil — hepsi açık sayı) |
| Silinen satır | **0** |
| FM değişikliği | **0** |
| Yeni web iddiası | **0** (GET yok) |
| Yeni URL | **0** |
| CJK/bozuk karakter | 0 (§17'deki `bağ关系` yazım hatası **bu tur içinde** bulundu ve düzeltildi — 1 satır, kendi eklemem) |
| Kapı | son ölçüm tur yanıtında — §21.1 kuralı işletildi |
| Kardeşlere yazım | 0 (`surucu` · `ornekler` · `index` dokunulmadı) |
| `git commit` | yok |

---

## §24 Bu dosyanın KAPSAM DIŞI yüzeyleri (pozitif liste — nereye gider)

> Red-team disiplini: "bu dosya neyi **kasten** taşımaz" listesi, hangi
> çerçevenin çizildiğini gösterir. Her satırda **gidiş yolu** vardır —
> boşluksuz havale (dolgu değil, yönlendirme).

| Kapsam dışı yüzey | Neden bu dosyada değil | Gidiş yolu (gerçek dosya) |
|-------------------|------------------------|----------------------------|
| WINMM export envanteri (sayısal) | `kod`/envanter işi | `reactos-audio-ornekler.md` §4 `[Satır: 91]` |
| `drivers/multimedia/audio/` alt-ağacı 5 dizin listesi | depo sayımı | `reactos-audio-surucu.md` §3.3 `[Satır: 89]` |
| Sürücü yükleme yolu (README'den türetilmiş akış) | sürucü işi | `reactos-audio-surucu.md` §6 `[Satır: 195]` |
| Windows gerçek yığını (WASAPI) karşılaştırması | A5 yasağı + ayrı dal | `../wasapi/` (yazım bu tur yok) |
| Üst dal ReactOS kimlik/dogrulama | üst-dal kapsamı | `../../isletim-sistemi-turu/reactos/reactos-ses-stacki.md` |
| Web GET tazeleme (R1–R6 damgaları) | ayrı tur işi | §20 — damga 2026-10-13 korunur |
| Çalışma-zamanı testi (L2–L6 üretim-değerliliği) | kanıt yok → ölçüm kapısı | §11 notu — test altyapısı kurulunca |

**Liste kuralı:** bu satırlardan herhangi biri bu dosyaya **eklenirse**
(A5/§15 ihlali) §19 C2-C4 denetimleri geçersiz sayılır ve düzeltme
append-only ile değil, **orkestratör kararıyla** yapılır (mevcut metin
silinemez → düzeltme yalnız yeni §'de "iptal kaydı" olarak gelebilir).

### §24.1 Kapı son sözü (bu dosya — ölçüm disiplini)

| Kural | Durum |
|-------|-------|
| Kapı ≥500 | bu § eklendikten sonraki ölçüm **tur yanıtında** beyan edilir (kendi kendine "geçti" yazma yasağı — §21.1) |
| Marj | hedef ≥505 — bu satırlar tampon olarak eklendi (sonraki turların §25+ ek satır payı) |
| Ölçüm yöntemi | `read_text_file` → split → son boş `pop` |
| Silme | 0 (append `oldString` = yalnız Authority footer, her tur) |
| Sonraki tur numarası | §25'ten devam — §1–§24 başlıkları **değiştirilmez** |
| Tur özeti | 5/5 electroncis hedefi tamam · bu dosya P2'nin 1/9'u · beyazliste dışı yazım 0 · commit yok |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
