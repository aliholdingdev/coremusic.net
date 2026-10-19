---
title: "Neva Connect — Çoklu-EXE Mimarisi & Windows Sürüm Bazlı Derleme Matrisi (Windows SDK / MSVC)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "Microsoft Learn (Configuring Programs for Windows XP, Windows SDK overview) + Stack Overflow/Q&A kanıtları + 01 export matrisi"
updated: 2026-10-15
---

# Neva Connect — Çoklu-EXE Mimarisi & Windows Sürüm Bazlı Derleme Matrisi (Windows SDK / MSVC)

> **Kapsam:** Bu doküman, Neva Connect'in **Windows dalı** için **çoklu-EXE** mimarisini ve
> **her Windows sürümü için ayrı derleme** stratejisini tanımlar. **Kod içermez** (kod yazımı
> kullanıcı onayıdır — 01 §1 kuralı). Zero-Hallucination: her teknik iddia §9'daki kaynak
> numarasıyla ([K1], [K2] …) ilişkilendirilir; kaynağı olmayan iddia `⚠️ VERIFICATION REQUIRED`
> ile işaretlenir.
>
> **Kardeş dokümanlar:**
> [[01-wlanapi-sürüm-matrisi-ve-windows-plani]] (export matrisi, MD5 kanıtlı — XP = 36 Wlan* export)
> · **02 = bu dosya** (çoklu-EXE + derleme matrisi)
> · **03 = PLANNED** (runtime resolver: `LoadLibrary`/`GetProcAddress` tablosu + handle RAII —
> bu dokümanda yalnızca **atıf** yapılır, içeriği burada tekrarlanmaz)

---

## §1 Mimari Prensip: Bir EXE'ye tümü yüklenmez

**Kullanıcı direktifi (2026-10-15):** *"birden fazla exe ile olacaktır"* — bu bir tercih değil,
bağlayıcı mimari karardır. Neva Connect Windows dalı **tek süreç/tek EXE** yerine
**yetenek başına ayrı EXE** ile kurulur.

### §1.1 Temel gerekçe — süreç izolasyonu (crash blast-radius)

| Gerekçe | Açıklama |
|---|---|
| **Çökme izolasyonu** | Bir alt-sistem (ör. USB envanteri) çökerse **WLAN EXE'si yaşamaya devam eder**; tek-EXE mimaride bir alt-sistem istisnası tüm uygulamayı öldürür |
| **Sürüm-bazlı ayrı derleme** (§2) | Her yetenek farklı OS hedeflerine çıkabilir (XP'ye yalnız WLAN gider) — tek EXE tek hedefe kilitlenir |
| **Yükleme/dağıtım esnekliği** | XP makineye yalnız `neva-wlan_x86_xp.exe` kopyalanır; BT/USB/storage kodu hiç taşınmaz |
| **Önyükleme maliyeti** | Küçük EXE daha az import, daha az DLL, daha hızlı başlatma (import kümesi §7'de `dumpbin /imports` ile kanıtlanır) |
| **Bağımsız güncelleme** | Tek bir yetenek bağımsız sürüm/patch alır; imzalama ve MD5 kaydı (§3.4) EXE başına yapılır |

### §1.2 EXE envanteri (kod adları — bağlayıcı olmayan öneri, ONAY BEKLIYOR)

| # | EXE | Kapsam (F-kodu) | Öncelik | Hedef sürüm menzili |
|---|---|---|---|---|
| 1 | `neva-wlan.exe` | **F1**: wifi list / connect / status / profile | **ÖNCELİKLİ — İLK** | XP SP3 → Win11 (tek kod, çok derleme — §2.3) |
| 2 | `neva-wifi7.exe` | **F2** (opsiyonel) | 2. sırada | Win11 24H2+ (bkz §1.3 tartışma) |
| 3 | `neva-bt.exe` | **F3** Bluetooth | 3. sırada | Win7+ (⚠️ sürüm menzili doğrulanmadı) |
| 4 | `neva-usb.exe` | **F4** USB envanter/iletişim | 4. sırada | Win7+ (⚠️ doğrulanmadı) |
| 5 | `neva-storage.exe` | **F5**: SATA / NVMe / M.2 envanteri | 5. sırada | Win7+ (⚠️ doğrulanmadı) |

**Paylaşılan katman (tüm EXE'lerin ortak bağımlılığı):**

- **Seçenek A — `wlabase.dll`** (dinamik kütüphane): resolver + handle RAII → **03 doc'unun konusudur**.
- **Seçenek B — header-only static lib** (ör. `wlabase/` include-only): hiçbir runtime artefaktı üretmez.

**XP özelinde DLL vs static tartışması (karar maddesi):**

| Kriter | A: `wlabase.dll` | B: header-only static |
|---|---|---|
| Kurulum/dağıtım | Her EXE yanına DLL = 6 dosya (5 EXE + 1 DLL); DLL yolu/versiyon yan etkisi riski | 5 EXE = 5 dosya; **sıfır yan etki** |
| Crash izolasyonu | Resolver çökerse **tüm** EXE'ler etkilenir (paylaşılan süreç görüntüsü) | Her EXE kendi kopyasını taşır; izolasyon **tam** |
| Bellek | Tek kopya (paylaşılan) | EXE başına kopya (kod boyutu küçük, maliyet ihmal edilebilir) |
| XP davranışı | XP'de DLL arama yolu/profilleri farklı davranır — yanlış DLL çekme riski (DLL redirection/side-by-side) | Yok |
| Derleme matrisi (§2) | DLL de **her sürüm için ayrı** derlenmeli → artefakt sayısı ×2 | Artefakt sayısı sabit |

> **Geçici öneri (ONAY BEKLIYOR):** XP dalı için **B (header-only static)**, Win8+ dalı için
> **A (wlabase.dll)** — gerekçe: XP'de DLL yükleme/arama yolu ve redist bağımlılığı ek risk
> üretirken, modern EXE'lerde paylaşılan resolver tek güncellemeye izin verir.
> Bu karar 03 doc'unda ADR olarak kayda geçirilmelidir.

### §1.3 `neva-wifi7.exe` — ayrı EXE mi, WLAN EXE'si içinde feature mı? (tartışma)

**Bilgi kanıtı:** Wi-Fi 7 (802.11be) desteğinin **Windows 11, sürüm 24H2** ile başladığı
Microsoft tarafından belgelenmiştir [K33]. Yani Wi-Fi 7 = **yalnızca modern Windows**;
XP/Win7/8/10 hedefleriyle hiçbir kesişimi yoktur.

| Seçenek | Artıları | Eksileri | Güvenilirlik |
|---|---|---|---|
| **A) Ayrı `neva-wifi7.exe`** | XP/Win10 derlemelerine 802.11be kodu **hiç girmez** (§4.1 mantığı); imza/dağıtım bağımsız; derleme menzili yalnız Win11 | 6. artefakt; WLAN ile kod paylaşımı `wlabase` üzerinden çözülmeli | **ÖNERİLEN** |
| **B) `neva-wlan.exe` içinde feature** | Tek EXE, tek sürüm döngüsü; WLAN radio zaten aynı API üzerinden görünür | XP/Win10 EXE'si kodu **taşır ama hiç çağırmaz** (boyut + review yükü); Wi-Fi 7'ye özel opcode/özellikler WLAN çekirdeğini kirletir | Alternatif |
| **C) Hiçbirinde (öteleyin)** | İlk teslimat kısalır (F1 = WLAN) | F2 gereksinimi ertelenir | Faz 2 |

> **Karar durumu:** Bölüm başlığındaki "(tartışma)" maddesi **AÇIK KONU** olarak §8'de
> listelenmiştir; onaylanana kadar kod eklenmez.

### §1.4 Çökme izolasyonunun uygulanabilirlik kuralları

1. Her EXE **kendi top-level catch**'ine sahiptir (§5.4) — istisna dışarı sızmaz.
2. Bir EXE'nin çökmesi **diğer EXE'lere sinyal göndermez** (paylaşılan `wlabase.dll` varsa
   sadece *onun* kodu risk altındadır — §1.2 tablosu).
3. EXE'ler arası iletişim **paylaşılan bellek/pipe üzerinden** yapılır (⚠️ mekanizma seçimi
   `UNKNOWN` — 03 doc'una bırakılmıştır).
4. Gözlemlenebilirlik: her EXE kendi log dosyasını üretir; log **salt-append** (CoreMusic kuralı).

---

## §2 Windows SDK & Toolset Matrisi — HER SÜRÜM AYRI DERLEME

### §2.1 Windows XP hedefi — doğrulanmış gerçekler (Microsoft Learn)

| # | İddia | Durum | Kaynak |
|---|---|---|---|
| 1 | **Visual Studio 2019 ve sonrası kendi toolset'i ile XP kodu DESTEKLEMEZ**; XP desteği yalnızca **VS2017 v141_xp toolset** ile mümkündür | ✅ Doğrulandı | [K1] |
| 2 | v141_xp, **Visual Studio 2017 C++ derleyicisi** + **Windows 7 SDK** sürümüdür ("a version of the Windows 7 SDK, but it uses the Visual Studio 2017 C++ compiler") | ✅ Doğrulandı | [K1] |
| 3 | v141_xp **yalnızca VS2017'de gerçekleştirilen C++ dil özelliklerini** destekler (v140_xp → yalnız VS2015 özellikleri) | ✅ Doğrulandı | [K1], dil konformansı: [K21] |
| 4 | Minimum desteklenen OS: **Windows XP SP3 (x86)**, **Windows XP SP2 (x64)**, Windows Server 2003 SP2 (x86/x64) | ✅ Doğrulandı | [K1] |
| 5 | v141_xp toolset'i **static analysis desteklemez** (SAL anotasyonları 7.1A SDK ile uyumsuz) | ✅ Doğrulandı | [K1] |
| 6 | v141_xp toolset'i ile **uzaktan debug (XP üzerinde) desteklenmez** | ✅ Doğrulandı | [K1] |
| 7 | XP hedefli çalışma zamanı kütüphaneleri: **CRT, C++ STL, ATL, ConcRT/PPL, MFC** = destekli; **C++ AMP** = destekli DEĞİL | ✅ Doğrulandı | [K1] |
| 8 | **Görev direktifi "MFC v141_xp'te YOK (tablo)" ifadesi KAYNAKLA ÇELİŞİYOR** — MS tablosunda MFC satırı XP sütununda **X** (destekli), **boş kalan sütun C++ AMP** | ⚠️ **DÜZELTME** | [K1] |
| 9 | Son XP uyumlu VC++ Redistributable = **VS2019 16.7 / dosya sürümü 14.27**; sonrasında çıkan redist'ler XP'de **çalışmaz** (SHA-256 imza desteği yokluğu) | ✅ Doğrulandı | [K1], [K7], [K8] |
| 10 | v141_xp kurulumu: **VS Installer → Individual components → Compilers, build tools, and runtimes → "C++ Windows XP Support for VS 2017 (v141) tools [Deprecated]"** | ✅ Doğrulandı | [K1], [K23] |

> **§2.1 #8 düzeltmesi (Zero-Hallucination):** Görev direktifindeki "MFC v141_xp'te YOK"
> ifadesi, Microsoft Learn tablosundaki değerle **uyuşmamaktadır**. Doğrulanmış durum:
> **MFC → XP toolset'inde DESTEKLİ, C++ AMP → DESTEKLİ DEĞİL.** Dokümanda kaynak ne diyorsa
> o yazılmıştır; direktifin aksi **kullanılmamıştır**.
>
> **Ek not:** XP hedefinde **MFC'nin redist ile dağıtımı** ayrı bir konudur (MFC'nin
> çalışma zamanı bileşenleri) → `⚠️ VERIFICATION REQUIRED` (XP'de MFC dağıtım gereksinimi bu
> araştırmada doğrulanmadı). Neva Connect'in MFC kullanmaması önerilir (Win32 + C++ STL yeterli).

### §2.2 v141_xp kurulumu ve bilinen tuzaklar

**Kurulum adımı [K1], [K23]:**

1. Visual Studio Installer → **Modify** → workload: **Desktop development with C++** seçili olmalı.
2. **Individual components** sekmesi → **Compilers, build tools, and runtimes** →
   **"C++ Windows XP Support for VS 2017 (v141) tools [Deprecated]"** → **Install/Modify**.
3. Proje → **Configuration Properties → General → Platform Toolset** →
   **"Visual Studio 2017 - Windows XP (v141_xp)"** (All Configurations).

**Bilinen tuzaklar (kanıtla):**

| # | Tuzak | Kanıt | Etki |
|---|---|---|---|
| T1 | **`v7.1A` klasörü boş kalabilir** — araçlar `WindowsSDKv7.1A` yolunu arar; "Windows 7 SDK" indirmesi **7.1** (A değil) sürümü verir, hata geçerli değildir | [K22] (SO #58755564 — v140_xp senaryosu; aynı arama davranışı) | XP toolset derlemesi "SDK bulunamadı" hatası verir |
| T2 | Bileşenin VS2022'de de "Deprecated" olarak adlandırılması kullanıcıyı yanıltabilir — **kurulum yolu aynıdır** | [K23] (SO #70263892) | Yanlış yere VS2017 kurmaya çalışmak |
| T3 | **v141_xp yalnızca VS2017 derleyicisiyle gelen dil özelliklerini** verir → proje `/std:c++20` beklentisi varsa XP dalı **farklı dil seviyesine** düşer | [K1], [K21] | §8 Açık Konu #1 |
| T4 | XP toolset'te **static analysis yok** → analiz default toolset'e geçirilip geri dönülür | [K1] | CI'da analiz adımı ayrı koşul (§3.4) |
| T5 | Pek çok üçüncü parti kütüphane (ör. vcpkg paketleri) XP toolset'i ile **derlenmeyebilir** | `⚠️ VERIFICATION REQUIRED` (paket-bazlı teyit yapılmadı) | Bağımlılık seçimi gözden geçirilir |
| T6 | **XP toolset'inde HLSL varsayılan derlenmez** (Haziran 2010 DirectX SDK gerekir) | [K1] | Neva Connect'i etkilemez (HLSL yok) |

### §2.3 Sürüm bazlı derleme matrisi — HER SATIR AYRI DERLEME

> **Kural:** Her satır **ayrı toolset + ayrı SDK + ayrı define seti + ayrı çıktı adı** ile
> derlenir. Tek kaynak ağacı (§6) değişmez; **yalnız derleme girdileri** değişir.

| Hedef | Toolset | Windows SDK | `_WIN32_WINNT` / `WINVER` | `NTDDI_VERSION` | Subsystem | Not / Kaynak |
|---|---|---|---|---|---|---|
| **Windows XP** (SP3 x86 / SP2 x64) | **v141_xp** | **Windows 7.1A SDK** | `0x0501` | `NTDDI_WINXPSP3` (0x05010300) | `/SUBSYSTEM:CONSOLE,5.01` (x86) | [K1], [K3], [K4] |
| **Windows 7** | **v141 / v142** | **Windows SDK 8.1** (veya Win10 SDK) | `0x0601` | `NTDDI_WIN7` (0x06010000) | varsayılan | [K4], [K3] |
| **Windows 8** | v142/v143 | **SDK 8.1** | `0x0602` | `NTDDI_WIN8` (0x06020000) | varsayılan | [K4] |
| **Windows 8.1** | v142/v143 | **SDK 8.1** | `0x0603` | `NTDDI_WINBLUE` (0x06030000) | varsayılan | [K4] |
| **Windows 10** | **v143** (VS2022) | **Windows 10 SDK** (19041 / 20348 / 26100 …) | `0x0A00` | `NTDDI_WIN10` (0x0A000000) | varsayılan | [K4], [K3] |
| **Windows 11** | **v143** | **Windows 11 SDK 22000.194+** (dual-STA için) / güncel **10.0.28000** | `0x0A00` | `NTDDI_WIN10` (0x0A000000) | varsayılan | [K17], [K2] |

**Matris notları:**

- **XP satırı tek istisnadır**: SDK **7.1A** (Windows 7.1 SDK'nın XP'yi hedefleyen parçası) ve
  **5.01 alt-sistem sürümü** kullanılır. **Windows 7.1 SDK (A dahil), XP/2003'ü hedefleyen son
  SDK'dır** [K3]. v141_xp toolset'i "a version of the Windows 7 SDK" olarak tanımlanır [K1].
- **`5.01` vs `5.02`**: XP **x86** için 5.01; XP **x64**/Server 2003 x64 için **5.02** kullanımı
  yaygındır → `⚠️ VERIFICATION REQUIRED` (MS `/SUBSYSTEM` dokümanında "minimum version"
  alanı vardır [K10], ancak 5.02 değerinin XP-x64 için zorunluluğu bu araştırmada teyit
  edilmedi). Kanıt adayı: [K40].
- **SDK 8.1 → Win7 hedefi**: Yeni SDK ile eski OS hedeflemenin standardı, **`_WIN32_WINNT`
  değerini en eski hedefe göre tanımlamaktır** [K3]. Not: **Windows 8.x SDK'ları varsayılan
  `_WIN32_WINNT` değerini "en yeni" OS'a ayarlar** (en eski değil) → tanımsız bırakılırsa
  Vista+ API'ler başlıkta görünür ve link/runtime'da patlar [K3].
- **SDK 8.1'in Win7'yi hedefleyebilmesi** walbourn'un "How do I use a newer SDK to target an
  older version of Windows?" bölümü ile desteklenir [K3]; "SDK 8.1 → Win7 target" ifadesinin
  doğrudan Microsoft sayfasındaki teyidi **bu araştırmada bulunamadı** → `⚠️ VERIFICATION REQUIRED`.
- **Güncel SDK'nın hedef listesi**: Windows SDK **10.0.28000**, Windows 11 26H1'i hedefler;
  kurulum/desteklenen OS listesi **Win11 21H2+ · Win10 1507+ · Server 2022/2019/2016/2012R2
  (komut satırı) · Win8.1 · Win7 SP1** [K2]. **19041 SDK artık destek dışıdır** (EOS
  2025-10-14, [K2]) → yeni Win10 derlemelerinde **26100+** SDK tercih edilir.
- **`NTDDI` değerleri** [K4] tablosundan alınmıştır (NTDDI_WINXPSP3 = 0x05010300,
  NTDDI_WIN7 = 0x06010000, NTDDI_WIN8 = 0x06020000, NTDDI_WINBLUE = 0x06030000,
  NTDDI_WIN10 = 0x0A000000).

### §2.4 SDK ↔ Visual Studio eşleşme tablosu (walbourn, "A Brief History of Windows SDKs")

| Visual Studio | Varsayılan/opsiyonel SDK | XP toolset SDK'sı |
|---|---|---|
| Visual Studio 2022 | Windows SDK (22000), Windows 10 SDK (18362, 19041, 20348) [ops.] | — |
| Visual Studio 2019 | Windows 10 SDK (17763–19041), Windows 8.1 SDK [ops.] | — |
| Visual Studio 2017 | Windows 10 SDK (14393/15063/16299/17763), Win 8.1 SDK [ops.] | **Windows 7.1A SDK → `v141_xp`** |
| Visual Studio 2015 | Windows 8.1 SDK, Windows 10 SDK (10240/10586/14393) [ops.] | **Windows 7.1A SDK → `v140_xp`** |
| Visual Studio 2013 | Windows 8.1 SDK | Windows 7.1A SDK → `v120_xp` |
| Visual Studio 2012 | Windows 8.0 SDK | Windows 7.1A SDK → `v110_xp` (Update 1 ile) |
| Visual Studio 2010 | Windows SDK 7.0A | — |

Ayrıca [K3]: **"Windows SDK 7.1 … XP ve Windows Server 2003'ü hedefleyen son sürümdür."**
Geliştirme (kurulum) platformu ≠ hedef platform ayrımı da aynı kaynakta belgelenmiştir
(Windows 10 SDK → Win7 SP1+ makinelere kurulur; Win8.1 SDK → Win7 SP1/8/8.1).

### §2.5 Define kuralı — header'dan ÖNCE

**Kural:** `_WIN32_WINNT`, `WINVER`, `NTDDI_VERSION`, `_WIN32_IE` **ilk `#include <windows.h>`
(dolaylı olarak `SDKDDKVer.h`) ÖNCESİ** tanımlanmalıdır [K4], [K5].

- **`NTDDI_VERSION` tanımlanıyorsa `_WIN32_WINNT` de tanımlanmalıdır** — Microsoft'un
  açık kuralı [K4].
- Tanımlanmazsa: başlıklar sürüm koşullu API'leri **açmaz** → `error C2065: undeclared
  identifier` [K4]; modern SDK'larda ise `_WIN32_WINNT` tanımsız bırakılırsa
  **"_WIN32_WINNT not defined. Defaulting to _WIN32_WINNT_MAXVER"** uyarısı ile **en yüksek**
  sürüm seçilir ve API'ler **runtime'da XP'de bulunmaz** (linkte değil, çalışma anında
  import/GetProcAddress hatası) [K32].
- Servis-paketi tuzağı [K4]: XP'yi hedeflerken `_WIN32_WINNT = 0x0501` yazan bir kod,
  XP-SP1'e eklenmiş API'leri (ör. `GetDllDirectory`) **kaçırabilir** → SP3 için
  `NTDDI_WINXPSP3` (0x05010300) ile birlikte düşünülmelidir.
- **Tercih edilen desen** (`neva_target.h` — İLK include):

```cpp
// NEVA — hedef sürüm makrosu (İLK include'dan önce)
#pragma once
#include <WinSDKVer.h>          // varsayılan _WIN32_WINNT'i seçmek için (opsiyonel)
#ifndef _WIN32_WINNT
#define _WIN32_WINNT 0x0501     // XP (matristen okunur; derleme -D ile ezilebilir)
#endif
#ifndef WINVER
#define WINVER     _WIN32_WINNT
#endif
#ifndef NTDDI_VERSION
#define NTDDI_VERSION 0x05010300  // NTDDI_WINXPSP3
#endif
#include <SDKDDKVer.h>
```

> **Kaynak notu (Zero-Hallucination):** "define header'dan önce" kuralı için görev direktifinde
> **Microsoft Q&A** kaynağı isteniyordu; bu araştırmada **doğrudan Microsoft Q&A sayfası
> BULUNAMADI** → URL uydurulmadı, yerine birincil otorite olan **Microsoft Learn** sayfaları
> ([K4], [K5]) ve kanıt olarak [K32] (Stack Overflow) kullanılmıştır.

### §2.6 CMake mi, .vcxproj mi? (AÇIK MADDE — karar verilmedi)

| Kriter | CMake | Doğrudan .vcxproj |
|---|---|---|
| Toolset seçimi | `-T v141_xp` (VS generator) [K24], [K25] | IDE'de Platform Toolset alanı [K1] |
| SDK seçimi | `CMAKE_SYSTEM_VERSION` (SDK sürümü seçmek için) [K26]; **ancak bu özelliğin davranışı üzerine açık bug kaydı var** [K27] | "Windows SDK Version" alanı |
| Matris (§2.3) yönetimi | toolchain dosyası / preset ile **satır başına bayrak** — matris tek yerde | **her satır için ayrı yapılandırma** (props dosyası = §6) |
| XP alt-sistem sürümü | `/SUBSYSTEM` bayrağı elle eklenmeli | Linker → System → Subsystem + Version |
| Multi-config karmaşası | Tek CMakeLists, **çok generator** | Her hedef için ayrı proje |
| Öğrenme eğrisi | Orta | Düşük (Windows-native) |

**CMake toolchain örneği (TASLAK — doğrulanmadı):**

```cmake
# cmake/toolsets/neva-xp.cmake  — XP hedefi
set(NEVA_TARGET_TRIPLE "x86_xp")
set(CMAKE_GENERATOR_TOOLSET "v141_xp" CACHE STRING "" FORCE)
# ⚠️ VERIFICATION REQUIRED: CMAKE_SYSTEM_VERSION=5.01 iddiası bu araştırmada
# teyit EDİLMEDİ; [K26] yalnızca "CMAKE_SYSTEM_VERSION SDK sürümünü seçer"
# der, [K27] ise özelliği VS generator'da çalışmıyor olarak raporlar.
set(CMAKE_SYSTEM_VERSION "5.01")
add_compile_options(/D_WIN32_WINNT=0x0501 /DNTDDI_VERSION=0x05010300)
add_link_options(/SUBSYSTEM:CONSOLE,5.01)
```

> **Öneri (ONAY BEKLIYOR):** Windows dalı + 6 satırlık OS matrisi + XP istisnası göz önüne
> alındığında **her ikisi de uygundur**; karar için kriter: ekibin CMake'e aşinalığı.
> **Geçici: .vcxproj + `build/win-*.props`** (§6) önerilir — çünkü XP satırı (`v141_xp`,
> 7.1A SDK, 5.01) doğrudan MSBuild property'leriyle ifade edilir ve CMake'in
> `CMAKE_SYSTEM_VERSION` belirsizliği [K26]/[K27] atlanır. Bu madde §8'de açıktır.

### §2.7 CRT kararı — statik (/MT) vs dinamik (/MD) [XP]

| Seçenek | Artıları | Eksileri | Kaynak |
|---|---|---|---|
| **`/MT` statik CRT** (XP için) | Redist **kurulumu gerekmez** → XP makinede "bu DLL yok" riski sıfır; §7'de import listesi temiz | EXE boyutu büyür; CRT yamaları **EXE'ye gömülü** (yeniden dağıtım gerekir) | [K9] (/MD /MT tanımı), [K1] (redist sınırı) |
| **`/MD` + 14.27 redist** | Küçük EXE; güncellemeler ayrı | **Son XP uyumlu redist = 14.27 (VS2019 16.7)**; sonraki redist'ler XP'de **çalışmaz** [K1], [K7], [K8] | [K1] |
| **`/MD` + güncel redist** | Modern standart | **XP'de kesinlikle çalışmaz** (SHA-256 imza desteksiz) | [K1] |

> **Öneri:** **XP satırında `/MT`** (tek EXE, sıfır bağımlılık); modern satırlarda **`/MD`**.
> Gerekçe: [K1]'deki son-redist sınırı + XP kurulumlarında hazır VC runtime bulunmaması
> ihtimali [K38]. Bu karar §8'de ADR olarak kayda geçirilecektir.

---

## §3 AYRI DERLEME Akışı (tek kaynak → 5 EXE × N OS)

### §3.1 Akış şeması

```
[tek kaynak ağacı]  (§6 — src/ hiç kopyalanmaz)
      │
      ├─→ ADIM 1: hedef seç    : x86_xp | x64_xp | x64_win7 | x64_win81 | x64_win10 | x64_win11
      ├─→ ADIM 2: toolset/SDK  : v141_xp+7.1A | v141/v142+8.1 | v143+Win10/11 SDK
      ├─→ ADIM 3: define seti  : _WIN32_WINNT / NTDDI_VERSION / WINVER (§2.5)
      ├─→ ADIM 4: hedef seçimi : neva-wlan | neva-wifi7 | neva-bt | neva-usb | neva-storage
      ├─→ ADIM 5: derleme      : cl/link  (CRT: XP=/MT, modern=/MD — §2.7)
      ├─→ ADIM 6: doğrulama    : dumpbin /imports + subsystem (§7.2)
      ├─→ ADIM 7: adlandırma   : neva-<mod>_<arch>_<os>.exe  (§3.3)
      └─→ ADIM 8: kayıt        : MD5 + sürüm manifesti (§3.4)
```

**"Kaynak çoğaltma" YOKTUR:** Kaynak ağacı tek kopyadır; **çoğaltılan şey derleme
yapılandırmasıdır** (farklı define/toolset/SDK), dosya değil. Çoğaltma (copy-paste) yerine
**#if + yapılandırma** (§4) kullanılır; yalnız çıktı adları ayrışır.

### §3.2 Toolset/SDK/matris ayrımı (özet)

| Ayrışan girdi | XP satırı | Modern satır |
|---|---|---|
| Platform Toolset | `v141_xp` | `v143` |
| Windows SDK | 7.1A | 10.0.26100+ / 10.0.28000 |
| `_WIN32_WINNT` | 0x0501 | 0x0A00 |
| `NTDDI_VERSION` | 0x05010300 | 0x0A000000 |
| CRT | `/MT` | `/MD` |
| Subsystem sürümü | 5.01 | varsayılan |
| C++ dil seviyesi | VS2017 özellikleri [K1] | C++20 tam destek |
| Static analysis | yok [K1] | var |
| Çıktı adı | `neva-wlan_x86_xp.exe` | `neva-wlan_x64_win10.exe` |

### §3.3 Çıktı adlandırma şeması

`neva-<mod>_<arch>_<os>.exe`

| Örnek | Anlam |
|---|---|
| `neva-wlan_x86_xp.exe` | WLAN · 32-bit · Windows XP (SP3) |
| `neva-wlan_x64_xp.exe` | WLAN · 64-bit · Windows XP x64 (SP2) |
| `neva-wlan_x64_win7.exe` | WLAN · 64-bit · Windows 7 |
| `neva-wlan_x64_win81.exe` | WLAN · 64-bit · Windows 8.1 |
| `neva-wlan_x64_win10.exe` | WLAN · 64-bit · Windows 10 |
| `neva-wlan_x64_win11.exe` | WLAN · 64-bit · Windows 11 |
| `neva-bt_x64_win10.exe` | Bluetooth · 64-bit · Win10 |
| `neva-storage_x86_win7.exe` | Storage · 32-bit · Win7 (opsiyonel) |

**Kural:** ad `_x86_xp` ise → yalnız o dosya `v141_xp` ile derlenmiştir ve **§7'deki XP
kanıtları** (`dumpbin` import temizliği + subsystem 5.01) o dosyaya **zorunludur**.

### §3.4 CI matrisi (GitHub Actions)

> ⚠️ **VERIFICATION REQUIRED — bu bölüm HİÇBİR canlı CI koşumu ile doğrulanmamıştır.**
> Aşağıdaki riskler araştırmada **kanıtlanmış**tır; çözümü henüz denenmemiştir.

| Risk | Kanıt | Sonuç |
|---|---|---|
| `windows-2022` görseli eski MSVC toolset'lerini (**14.0 / 14.16** vb.) artı kuramıyor | [K28] (actions/runner-images #12764) | `v141_xp` (VS2017 toolset) **görselde hazır olmayabilir** |
| `windows-2022` görselinden fazladan VC bileşenleri temizleniyor | [K29] (runner-images #9701) | XP bileşeni görselde bulunmayabilir |
| `windows-2019` kaldırıldı; kullanıcılar VS2019 toolset'ini 2022 görselinde istiyor | [K31] (runner-images #12486) | sürüm/matris kırılması |
| **Windows Server görsellerinde `WLAN AutoConfig (wlansvc)` varsayılan olarak ETKİN DEĞİL** | [K30] (runner-images #9305) | **WLAN smoke testi CI'da çalışmaz** → servis manuel açılmalı veya test gerçek Windows 10/11 makineye alınmalı |

**Önerilen (DOĞRULANMAMIŞ) CI matrisi iskeleti:**

```yaml
# .github/workflows/neva-connect-windows.yml  — TASLAK (⚠️ VERIFICATION REQUIRED)
strategy:
  matrix:
    include:
      - { target: x86_xp,    toolset: v141_xp, sdk: "7.1A",  winnt: 0x0501, crt: MT, runner: windows-2022 }
      - { target: x64_win7,  toolset: v142,    sdk: "8.1",   winnt: 0x0601, crt: MD, runner: windows-2022 }
      - { target: x64_win10, toolset: v143,    sdk: "10.0.26100.x", winnt: 0x0A00, crt: MD, runner: windows-2022 }
# adım A: v141_xp bileşeni kurulu mu?  → kurulmuyorsa [K28]/[K29] gereği runner görüntüsü
#         kendi kendine yazılmalı (self-hosted) veya VS Build Tools indirilmeli — DOĞRULANMADI
# adım B: dumpbin /imports doğrulaması (§7.2) — XP satırında ZORUNLU gate
# adım C: smoke test — windows-2022 (Server) görselinde wlansvc YOK [K30] → gate skipped
```

**CI kararı (öneri):** XP satırı için **self-hosted Windows XP/Win10 makine** ya da
**önceden hazırlanmış (snapshot) runner görseli**; bulut runner'da XP toolset kurulumu
[K28]/[K29] nedeniyle **kırılgandır**.

---

## §4 Sürüm-Şartlı Derleme TeknikLERİ

### §4.1 Compile-time: `#if` ile kod gövdesi eleme

**İlke:** Aynı kaynak dosyası, **farklı define** ile derlenir → "ayrı derleme"nin kaynağı
budur. `NTDDI_VERSION` büyük olduğu için kod bloğu **derleme anında** dahil edilir/elenir.

```cpp
// src/wlan/features_hostednetwork.cpp  — 01 §3.1 kanıtı:
// XP wlanapi.dll'te WlanHostedNetwork* export'ları YOKTUR (36 export tabanı)
#if (NTDDI_VERSION >= NTDDI_WIN7)
bool IsHostedNetworkSupported() {
    // WlanHostedNetworkQueryStatus vb. — XP build'inde BU SATIRLAR YOKTUR
    return QueryHostedNetworkStatus();
}
#else
bool IsHostedNetworkSupported() { return false; }   // XP: hiç çağrı yok
#endif
```

**Compile-time (#if) – Runtime (GetProcAddress) ayrım tablosu:**

| Durum | Teknik | Gerekçe | Örnek |
|---|---|---|---|
| **Bu EXE o sürümde ASLA çalışmaz** (XP exe'si) | **`#if (NTDDI_VERSION >= …)`** | Kod hiç üretilmez → import tablosu temiz, boyut küçük, review kolay | HostedNetwork blokları XP build'inde **elenir** [K34 — 01 §3.1] |
| **EXE birden fazla sürümde çalışır, API olmayabilir** | **`GetProcAddress` gate** (03 doc) | Aynı binary Win7→Win11'de dolaşır; API varlığı **export tablosuyla** keşfedilir (sürüm numarasıyla değil — 01 §4.2 D2) | `WlanGetAvailableNetworkList2` keşfi |
| **API başlıkta var, OS'ta olmayabilir** | **runtime keşif + `/D _WIN32_WINNT`** | Başlık sürüm bayrağı link riskini azaltır; kalan risk runtime'da | `NTDDI_WINBLUE` bloğu |
| **OS davranışı sürüme göre değişir (ama API aynı)** | **runtime sürüm/feature sorgusu** | Tek binary, davranış ayrımı | radio band bilgisi |
| **Delay-load mümkünse** | **`/DELAYLOAD`** linker seçeneği [K16], [K39] | Import tablosuna girer ama DLL ancak ilk çağrıda yüklenir | Opsiyonel optimizasyon |

> **Çift katman kuralı:**
> **Compile-time = "bu EXE hangi OS'lara gidiyor?"** · **Runtime = "bu EXE'nin gittiği OS'ta o
> özellik var mı?"**. XP exe'si `WlanHostedNetwork` **çağrısını hiç içermez** — çünkü o exe XP
> define'ıyla derlendi ve kod gövdesi `#if` ile elendi; Win10 exe'si **hepsini** alır.

### §4.2 Export matrisi (01) ile ilişkilendirme

| 01 §3 export tablosu | Bu dokümandaki karşılığı |
|---|---|
| **XP = 36 Wlan* export** (MD5 doğrulamalı DLL, REA+PE iki yöntem) | XP satırı `#if` ile yalnız 36 export'u çağıran koda derlenir |
| XP → Win7: **+12** (11 `WlanHostedNetwork*` + `WlanRegisterVirtualStationNotification`), **−0** | `NTDDI_VERSION >= NTDDI_WIN7` blokları — XP'de yok |
| Win7 → Win8: **+55** (PLAP, WFD GO, `WlanStartAP/StopAP`, LowPriv vb.), **−0** | `>= NTDDI_WIN8` blokları + **runtime GetProcAddress** (aynı binary'de çalışsın diye) |
| Win8 → Win8.1: **+5**, Win8.1 → Win10 (10240): **+13** (ANQP/hotspot-2.0 vb.), **−0** | `>= NTDDI_WINBLUE` / `>= NTDDI_WIN10` blokları + runtime gate |
| Win10 10240 → 19041: **+27** (FTM/802.11mc, non-disruptive scan, device services …), **−0** | Yalnız modern derlemede; sürüm bayrağı `_WIN32_WINNT=0x0A00` |

**Kural:** Export'lar **hiçbir sürümde silinmiyor** (01 §3 ana bulgu, `−0`) → runtime resolver
(03 doc) **"export var mı?"** ile çalışır; compile-time `#if` ise **"bu EXE bu OS'a gitmiyor"**
kararını verir. İkisi birlikte kullanılır, birbirinin yerine değil.

### §4.3 Çift katman örneği (kavramsal akış)

```
neva-wlan_x86_xp.exe   :  _WIN32_WINNT=0x0501 → #if blokları ELENDİ
                          → kalan kod: 36 export (01 §3)
                          → runtime gate: LoadLibrary("wlanapi.dll") + GetProcAddress [K14][K15]
                          → eksik export → Level 1 fallback (§5.2)

neva-wlan_x64_win10.exe:  _WIN32_WINNT=0x0A00 → #if blokları DAHİL (148 export, 19041)
                          → runtime gate hâlâ VAR (Update KB'ları export ekleyebilir — 01 D2)
                          → eksik export → Level 1 fallback
```

---

## §5 İstisnalar & Fallback — "çökmesin" (kullanıcı direktifi)

### §5.1 Seviyeli hata şeması

| Seviye | Tanım | Kaynak | Kapatılabilir mi? | Örnek |
|---|---|---|---|---|
| **Level 0** | **Kernel'den kaçınma** — sürücü/donanım katmanına dokunma | Tasarım kısıtı | Evet (zaten yapılmaz) | NDIS/kernel sürücüsü **yazılmaz**; yalnız user-mode API |
| **Level 1** | **Feature-not-supported** — API/export/feature yok | Runtime keşif (`GetProcAddress` / `#if`) | Evet — **graceful degrade** | HostedNetwork XP'de yok → "Bu özellik bu Windows sürümünde yok" |
| **Level 2** | **API hatası** — API var ama `ERROR_*` döndü | `GetLastError` / WLAN reason code | Evet — retry/alternatif | `WlanOpenHandle` → `ERROR_NOT_ENOUGH_MEMORY` |
| **Level 3** | **Sistem/istisna hatası** — beklenmeyen C++/SEH istisnası | catch / `__except` | **Hayır — yakalanır, loglanır, süreç düzgün kapanır** | null deref, bellek hatası |

### §5.2 Seviye → kullanıcı mesajı → fallback eylemi tablosu

| Seviye | Durum | Kullanıcı mesajı (TR, taslak) | Fallback eylemi |
|---|---|---|---|
| L1 | `wlanapi.dll` yüklenemedi | "Kablosuz arayüz kütüphanesi bulunamadı (wlanapi.dll)" | Durum raporu; EXE kapanmadan **sürüm bilgisi yaz** (§7.3) |
| L1 | Export yok (ör. HostedNetwork) | "Bu Windows sürümünde hotspot özelliği desteklenmiyor" | İlgili menüyü **gizle/pasif yap** — süreç devam |
| L1 | Wi-Fi 7 (F2) · Win11 < 24H2 | "Wi-Fi 7 desteği bu Windows sürümünde yok" [K33] | F2 EXE'si → bilgilendirip çıkış kodu 2 |
| L2 | **`WlanOpenHandle` başarısız** | **"Kablosuz ağ servisi başlatılamadı (WZC / WLAN AutoConfig)"** [K35] | Retry (ör. 3 × 2 sn) → başarısızsa **durum raporu** + servis durumunu logla; süreç **ayakta kalır** |
| L2 | `WlanEnumInterfaces` → 0 arayüz | "Kablosuz adaptör bulunamadı" | Enum'ı tekrarla (hot-plug notification) → 10 sn sonra bilgi mesajı |
| L2 | `WlanConnect` + reason code | "Bağlantı reddedildi: {reason-code çeviris}" (`WlanReasonCodeToString`) | Alternatif profili dene → Level 1'e düşür |
| L2 | Bellek (`WlanAllocateMemory`) | "Bellek ayrılamadı" | Retry ×1 → Level 3'e düşür |
| L3 | C++ istisnası | "Beklenmeyen hata — ayrıntılar log dosyasında" | Top-level catch (§5.4) → log → **exit code 70** |
| L3 | SEH (`ACCESS_VIOLATION` vb.) | "Bir bileşen çöktü; diğer modüller çalışmaya devam ediyor" | `__except` → crash log → **çökme yalnız bu EXE'yi etkiler** (§1.1) |

### §5.3 Sonuç tipi — `std::expected` YERİNE özel `Result`

> **Neden özel tip:** `v141_xp` yalnızca **VS2017'de gerçekleştirilen C++ dil özelliklerini**
> destekler [K1] → VS2017'de olmayan bekleme tipleri XP dalında **derlenemez**
> (`std::expected`'ın hangi dil standardında olduğu → `⚠️ VERIFICATION REQUIRED`).
> XP dalı için **özel `Result`** zorunludur.

```cpp
// src/common/neva_result.h — XP toolset'inde (VS2017 dilleri) derlenebilir
#pragma once
#include <cstdint>
#include <utility>

namespace neva {

enum class Level : std::uint8_t { Ok = 0, FeatureNotSupported = 1, ApiError = 2, SystemError = 3 };

struct Error {
    Level         level = Level::ApiError;
    std::uint32_t code  = 0;     // GetLastError() veya WLAN reason code
    const char*   where = "";    // __func__ / statik dize
};

template <typename T>
class Result {
public:
    static Result ok(T v)       { Result r; r.ok_ = true;  r.value_ = static_cast<T&&>(v); return r; }
    static Result fail(Error e) { Result r; r.ok_ = false; r.error_ = e; return r; }

    bool has_value() const noexcept          { return ok_; }
    explicit operator bool() const noexcept  { return ok_; }

    T&           value() noexcept      { return value_; }
    const Error& error() const noexcept { return error_; }

private:
    bool   ok_ = false;
    T      value_{};
    Error  error_{};
};

} // namespace neva
```

### §5.4 nothrow init + top-level catch

```cpp
// src/wlan/main.cpp — neva-wlan.exe (kavramsal iskelet; KOD ONAYI BEKLER)
#include "neva_target.h"   // §2.5 — İLK include, define'lar burada
#include "neva_result.h"

#include <new>
#include <cstdio>

int main() noexcept
{
    try {
        // Level 2/3 savunması: nothrow ile başla — bad_alloc fırlatmasın
        auto* ctx = new (std::nothrow) WlanContext();
        if (ctx == nullptr) {
            // L3: "Bellek ayrılamadı" → süreç temiz kapanır
            return Report(Level::SystemError, 8, "out-of-memory");
        }

        auto open = ctx->Open();               // WlanOpenHandle karşılığı
        if (!open) {                           // L2 → §5.2 satırı
            return Report(open.error().level, open.error().code,
                          "Kablosuz ag servisi baslatilamadi (WZC / WLAN AutoConfig)");
        }
        // ... F1 akışı: list / connect / status / profile ...
        return 0;
    }
    catch (const std::bad_alloc&) {
        return Report(Level::SystemError, 8, "out-of-memory");
    }
    catch (const std::exception& e) {
        return Report(Level::SystemError, 0, e.what());   // L3 — log + exit 70
    }
    catch (...) {
        return Report(Level::SystemError, 0, "unknown");  // L3 — asla yukarı sızmaz
    }
}
```

**SEH katmanı (`__try/__except`):**

```cpp
// Son savunma hattı (MS ISO istisnalarını tercih eder — [K20])
int WlanEntryPoint() {
    __try {
        return RunWlan();
    }
    __except (EXCEPTION_EXECUTE_HANDLER) {
        const unsigned long code = GetExceptionCode();   // ACCESS_VIOLATION vb.
        return Report(Level::SystemError, code, "seh-fault");
    }
}
```

> **Not (Zero-Hallucination):** SEH (`__try/__except`), MSVC'nin **Windows genelindeki**
> Microsoft uzantısıdır [K18], [K19]; Microsoft bu mekanizmanın **Windows/Visual C++
> tarafından desteklendiğini** belirtir, **sürüm bazlı bir yasak dokümante etmez** → "XP'de
> SEH var" ifadesi **pratikte doğrudur**; ancak **"XP'de SEH'nin belgelendiği resmi sayfa"**
> bu araştırmada **bulunamadı** → `⚠️ VERIFICATION REQUIRED`. Ayrıca Microsoft, **C++ kodunda
> ISO istisnalarını (`/EHsc`) tercih eder** [K20] → SEH yalnızca **son savunma hattı** olarak
> kullanılır, birincil hata mekanizması değil.

---

## §6 Dizin Yapısı Önerisi — tek kaynak, çok EXE

```
neva-connect-windows/
├─ CMakeLists.txt                  # veya NevaConnect.sln + .vcxproj (§2.6 AÇIK MADDE)
├─ build/
│   ├─ win-xp.props                # MSBuild: v141_xp + 7.1A + 0x0501 + /MT + subsystem 5.01
│   ├─ win-win7.props              # v142 + SDK 8.1 + 0x0601 + /MD
│   ├─ win-win81.props             # v143 + SDK 8.1 + 0x0603 + /MD
│   ├─ win-win10.props             # v143 + Win10 SDK 26100+ + 0x0A00 + /MD
│   └─ win-win11.props             # v143 + Win11 SDK 22000.194+ + 0x0A00 + /MD
├─ cmake/
│   ├─ toolsets.cmake              # CMake yolunda: -T v141_xp / SDK seçimi (§2.6)
│   └─ toolsets/neva-xp.cmake      # XP toolchain (⚠️ CMAKE_SYSTEM_VERSION doğrulanmadı)
├─ include/
│   └─ neva_target.h               # §2.5 define şablonu (İLK include)
├─ src/
│   ├─ common/                     # paylaşılan: Result (§5.3), log, hata seviyeleri
│   │   ├─ neva_result.h
│   │   ├─ neva_errors.h           # Level 0-3 + mesaj kataloğu (§5.2)
│   │   └─ neva_log.h
│   ├─ wlabase/                    # resolver + handle RAII → 03 doc (header mı DLL mı: §1.2)
│   │   ├─ resolver.h              # LoadLibrary/GetProcAddress tablosu (03)
│   │   └─ wlan_handle.h           # RAII: WlanOpenHandle/CloseHandle, WlanAllocateMemory
│   ├─ wlan/                       # neva-wlan.exe (F1) — ÖNCELİKLİ
│   │   ├─ main.cpp
│   │   ├─ features_enum.cpp       # #if blokları (§4.1)
│   │   ├─ features_profile.cpp
│   │   └─ features_connect.cpp
│   ├─ wifi7/                      # neva-wifi7.exe (F2) — AÇIK MADDE §1.3
│   ├─ bt/                         # neva-bt.exe (F3)
│   ├─ usb/                        # neva-usb.exe (F4)
│   └─ storage/                    # neva-storage.exe (F5)
├─ tests/                          # ctest — her hedef satırında koşar (§7.1)
└─ dist/                           # §3.3 çıktı adları + §3.4 manifest
```

**Kurallar:**

1. **Kaynak çoğaltma YOK** — `src/` tek kopya; ayrışma **yalnız `build/*.props` /
   `cmake/toolsets*.cmake`** içindedir (§3.1 ADIM 2-3).
2. **Her EXE = `src/<mod>/main.cpp` + `src/common` + `src/wlabase`** — EXE'ler birbirinin
   dizinini **include etmez** (yalnız `common` / `wlabase` üzerinden paylaşırlar).
3. **Taşıma/silme yasağı** (CoreMusic §10) — bu yapı **öneridir**; mevcut dosya adlandırması
   ve import zinciri onaysız değiştirilmez.
4. **Karar bekleyenler:** (a) CMake vs .vcxproj [§2.6], (b) `wlabase` DLL vs header [§1.2],
   (c) `neva-wifi7` ayrı EXE vs feature [§1.3] → üçü de §8'de açıktır.

---

## §7 Test & Yayın

### §7.1 Her artefaktın OS ucu smoke testi

| Artefakt | Test ucu | Kabul kriteri |
|---|---|---|
| `neva-wlan_x86_xp.exe` | **Gerçek Windows XP SP3 x86** | `status` komutu çıkış kodu 0 · 36 export dışına **çağrı yok** · crash yok |
| `neva-wlan_x64_xp.exe` | Windows XP x64 SP2 (varsa) | Aynı · `⚠️ VERIFICATION REQUIRED` (x64 XP ortamı mevcut değil) |
| `neva-wlan_x64_win7.exe` | Windows 7 SP1 | enum + connect + profile · `WlanHostedNetwork` erişilebilir |
| `neva-wlan_x64_win81.exe` | Windows 8.1 | enum + profile + 108 export erişimi |
| `neva-wlan_x64_win10.exe` | Windows 10 19041+ | 148 export'un erişilebilir olanları · ANQP/FTM graceful degrade |
| `neva-wlan_x64_win11.exe` | Windows 11 24H2 | F2 (Wi-Fi 7) erişimi [K33] · dual-STA opsiyonel [K17] |
| `neva-bt/` `neva-usb/` `neva-storage` | Win10 + Win11 | envanter komutu dönüş · L1 fallback tetiklenir |
| **Tümü** | **CI `windows-2022`** | `wlansvc` yok [K30] → **WLAN smoke testi ATLANIR**, yalnız derleme + `dumpbin` gate koşar |

### §7.2 `dumpbin /imports` doğrulaması — XP EXE'si için ZORUNLU gate

**Amaç:** XP exe'sinde **Vista+ API import'u OLMADIĞINI** kanıtlamak.

```bat
rem XP hedefi doğrulaması (XP toolset kurulu bir VS Developer Command Prompt)
dumpbin /imports dist\neva-wlan_x86_xp.exe > dist\neva-wlan_x86_xp.imports.txt
dumpbin /headers dist\neva-wlan_x86_xp.exe | findstr /i "SUBSYSTEM"

rem KABUL KRİTERLERİ:
rem  1) imports listesinde YALNIZCA XP'de var olan DLL'ler:
rem     kernel32.dll, user32.dll, advapi32.dll, ws2_32.dll, ole32.dll (+ wlanapi.dll dinamikse)
rem  2) Vista+/Win7+ imzalari YOK (örn. WlanHostedNetwork* XP import'unda GÖRÜNMEZ;
rem     çünkü kod #if ile elendi — §4.1)
rem  3) SUBSYSTEM alanı 5.01 olmali (XP)
```

| Doğrulama | Komut (kaynak) | Kabul |
|---|---|---|
| Import listesi | `dumpbin /imports` [K11], genel referans [K12] | XP'de olmayan DLL/sembol **YOK** |
| Alt-sistem sürümü | `dumpbin /headers` → `SUBSYSTEM` [K10], [K12] | **5.01** (x86 XP) |
| Bağımlılık listesi | `dumpbin /dependents` [K13] | Yalnız XP'de var olan DLL'ler + (`/MT` ise CRT DLL'i YOK) |
| CRT statikliği | `/MD` ise `msvcp140.dll`/`vcruntime140.dll` görünür | XP satırında **görünmemeli** (`/MT`, §2.7) |

> **Not:** `/OPT:REF` gibi linker optimizasyonları bazı `dumpbin` çıktılarını daraltır;
> `/dependents` yalnızca belirli durumlarda anlamlıdır [K13] → **`/imports` birincil kanıttır**.

### §7.3 Sürüm & MD5 kaydı (manifest şeması)

```json
{
  "component": "neva-wlan",
  "target": "x86_xp",
  "toolset": "v141_xp",
  "sdk": "Windows SDK 7.1A",
  "defines": { "_WIN32_WINNT": "0x0501", "NTDDI_VERSION": "0x05010300" },
  "crt": "/MT",
  "subsystem": "5.01",
  "file": "neva-wlan_x86_xp.exe",
  "size": 0,
  "md5": "<compute>",
  "sha256": "<compute>",
  "built_at": "ISO-8601",
  "git_commit": "<hash>",
  "imports_gate": "PASS|FAIL"
}
```

**Kural:** Her artefakt için **MD5 + SHA-256 + git commit + `imports_gate` sonucu** yazılır;
01 doc'undaki MD5 disiplini (pconlife kanıtları) ile **aynı yöntem** kullanılır. Manifest
`dist/manifest.json` içine **eklenerek** (append) tutulur; eski kayıt **üzerine yazılmaz**.

---

## §8 Açık Konular

| # | Açık konu | Durum | Karar sahibi |
|---|---|---|---|
| 1 | **C++20 vs v141_xp**: XP toolset yalnız VS2017 dil özelliklerini destekler [K1] → XP dalı **tam C++20 DEĞİL**. Proje standardı C++20 (ROLE §13) ile çelişiyor. Seçenekler: (a) XP dalını C++17'ye sabitle, (b) XP'yi bırak, (c) dil bayrağını hedefe göre ayır | `⚠️ VERIFICATION REQUIRED` + stratejik karar | **Kullanıcı** |
| 2 | **MFC iddia düzeltmesi**: direktif "MFC yok" diyor, MS tablosu "MFC var / C++ AMP yok" diyor [K1] → direktif mi, kaynak mı? | **Kaynak öncelikli yazıldı**; teyit bekliyor | Kullanıcı |
| 3 | **CMake vs .vcxproj** (§2.6) | AÇIK | Kullanıcı |
| 4 | **`wlabase.dll` vs header-only static** (§1.2) | AÇIK → 03 doc'unda ADR | Kullanıcı |
| 5 | **`neva-wifi7.exe` ayrı EXE mi, WLAN içinde feature mı?** (§1.3) | AÇIK | Kullanıcı |
| 6 | **XP x64 subsystem 5.02** doğrulanmadı (§2.3) | `⚠️ VERIFICATION REQUIRED` | Araştır |
| 7 | **CI**: `v141_xp` bulut runner'da kurulumu [K28]/[K29] + `wlansvc` yokluğu [K30] | `⚠️ VERIFICATION REQUIRED` — hiç canlı koşulmadı | DevOps |
| 8 | **CMake `CMAKE_SYSTEM_VERSION=5.01`** iddiası | `⚠️ VERIFICATION REQUIRED` [K26]/[K27] | Araştır |
| 9 | **BT / USB / Storage hedef OS menzilleri** (§1.2 tablosunda işaretli) | `⚠️ VERIFICATION REQUIRED` | Araştır |
| 10 | **XP'de SEH'nin resmi belgelendiği sayfa** (§5.4 notu) | `⚠️ VERIFICATION REQUIRED` | Araştır |
| 11 | **XP redist dağıtım yolu** (14.27 vs `/MT`) — §2.7 önerisi henüz ADR değil | AÇIK | Kullanıcı |
| 12 | **`WlanGetAvailableNetworkList2` ilk public sürümü** (01 §6.3'ten devralındı) | `VERIFICATION REQUIRED` | Araştır |
| 13 | **"SDK 8.1 → Win7 hedefi"** ifadesinin doğrudan Microsoft teyidi (§2.3 notu) | `⚠️ VERIFICATION REQUIRED` | Araştır |

---

## §9 Kaynaklar

> Güvenilirlik: **Yüksek** = birincil otorite (Microsoft Learn / resmi repo) ·
> **Orta** = uzman blog / vendor · **Düşük** = topluluk Q&A (kanıt olarak kullanılır, otorite değil).

| # | Kaynak | URL | Güvenilirlik |
|---|---|---|---|
| K1 | Microsoft Learn — *Configuring Programs for Windows XP* | https://learn.microsoft.com/en-us/cpp/build/configuring-programs-for-windows-xp?view=msvc-170 | Yüksek |
| K2 | Microsoft Learn — *Windows SDK overview* (10.0.28000, hedef/servis tablosu) | https://learn.microsoft.com/en-us/windows/apps/windows-sdk | Yüksek |
| K3 | Chuck Walbourn (ex-MSFT) — *A Brief History of Windows SDKs* (SDK↔VS, Win7.1 = son XP SDK) | https://walbourn.github.io/a-brief-history-of-windows-sdks/ | Orta |
| K4 | Microsoft Learn — *Using the Windows Headers* (NTDDI/_WIN32_WINNT tabloları) | https://learn.microsoft.com/en-us/windows/win32/winprog/using-the-windows-headers | Yüksek |
| K5 | Microsoft Learn — *Update WINVER and _WIN32_WINNT* | https://learn.microsoft.com/en-us/cpp/porting/modifying-winver-and-win32-winnt?view=msvc-170 | Yüksek |
| K6 | Microsoft Learn — *C++ features deprecated or removed from Visual Studio* | https://learn.microsoft.com/en-us/cpp/porting/features-deprecated-in-visual-studio?view=msvc-170 | Yüksek |
| K7 | Microsoft Learn — *C++ binary compatibility between Visual Studio versions* (XP redist sonu) | https://learn.microsoft.com/en-us/cpp/porting/binary-compat-2015-2017?view=msvc-170 | Yüksek |
| K8 | Microsoft Learn — *Latest Supported Visual C++ Redistributable Downloads* | https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist?view=msvc-170 | Yüksek |
| K9 | Microsoft Learn — */MD, /MT, /LD (Use runtime library)* | https://learn.microsoft.com/en-us/cpp/build/reference/md-mt-ld-use-run-time-library?view=msvc-170 | Yüksek |
| K10 | Microsoft Learn — */SUBSYSTEM (Specify Subsystem)* | https://learn.microsoft.com/en-us/cpp/build/reference/subsystem-specify-subsystem?view=msvc-170 | Yüksek |
| K11 | Microsoft Learn — *IMPORTS (DUMPBIN)* | https://learn.microsoft.com/en-us/cpp/build/reference/imports-dumpbin?view=msvc-170 | Yüksek |
| K12 | Microsoft Learn — *DUMPBIN Reference* | https://learn.microsoft.com/en-us/cpp/build/reference/dumpbin-reference?view=msvc-170 | Yüksek |
| K13 | Microsoft Learn — *DEPENDENTS (DUMPBIN)* | https://learn.microsoft.com/en-us/cpp/build/reference/dependents?view=msvc-170 | Yüksek |
| K14 | Microsoft Learn — *GetProcAddress function* | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-getprocaddress | Yüksek |
| K15 | Microsoft Learn — *Using Run-Time Dynamic Linking* | https://learn.microsoft.com/en-us/windows/win32/dlls/using-run-time-dynamic-linking | Yüksek |
| K16 | Microsoft Learn — *Linker support for delay-loaded DLLs* | https://learn.microsoft.com/en-us/cpp/build/reference/linker-support-for-delay-loaded-dlls?view=msvc-170 | Yüksek |
| K17 | Microsoft Learn — *Dual-STA query and configuration* (Windows 11 SDK 10.0.22000.194) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/dual-sta-query-config | Yüksek |
| K18 | Microsoft Learn — *try-except statement* | https://learn.microsoft.com/en-us/cpp/cpp/try-except-statement?view=msvc-170 | Yüksek |
| K19 | Microsoft Learn — *Structured Exception Handling (C/C++)* | https://learn.microsoft.com/en-us/cpp/cpp/structured-exception-handling-c-cpp?view=msvc-170 | Yüksek |
| K20 | Microsoft Learn — *EH (Exception handling model)* | https://learn.microsoft.com/en-us/cpp/build/reference/eh-exception-handling-model?view=msvc-170 | Yüksek |
| K21 | Microsoft Learn — *Microsoft C/C++ language conformance* (XP sayfasının referans verdiği sayfa) | https://learn.microsoft.com/en-us/cpp/overview/visual-cpp-language-conformance?view=msvc-170 | Yüksek |
| K22 | Stack Overflow — *Visual Studio 2019: How to use Platform Toolset v140_xp?* (v7.1A arama sorunu) | https://stackoverflow.com/questions/58755564/visual-studio-2019-how-to-use-platform-toolset-v140-xp | Düşük (kanıt) |
| K23 | Stack Overflow — *How to compile C++ app for Windows XP in MSVS?* ("C++ Windows XP Support … [Deprecated]") | https://stackoverflow.com/questions/70263892/how-to-compile-c-app-for-windows-xp-in-msvs | Düşük (kanıt) |
| K24 | Stack Overflow — *How to target Windows XP with MSVC2017 and CMake?* | https://stackoverflow.com/questions/72377387/how-to-target-windows-xp-with-msvc2017-and-cmake | Düşük (kanıt) |
| K25 | CMake Issue #19672 — *Failed generating 2017 sln with -T v141_xp,version=14.15* | https://gitlab.kitware.com/cmake/cmake/-/issues/19672 | Orta (resmi repo) |
| K26 | CMake Discourse — *VS: Select Windows SDK version* (`CMAKE_SYSTEM_VERSION`) | https://discourse.cmake.org/t/vs-select-windows-sdk-version/5603 | Orta |
| K27 | Visual Studio Community — *CMAKE_SYSTEM_VERSION does not set Windows SDK version* | https://developercommunity.visualstudio.com/t/CMAKE_SYSTEM_VERSION-does-not-set-Window/10181222 | Orta |
| K28 | GitHub runner-images Issue #12764 — *MSVC toolsets 14.0 and 14.16 can no longer be installed* | https://github.com/actions/runner-images/issues/12764 | Orta (resmi repo) |
| K29 | GitHub runner-images Issue #9701 — *[Windows-2022] Multiple VC Build Tools will be removed* | https://github.com/actions/runner-images/issues/9701 | Orta |
| K30 | GitHub runner-images Issue #9305 — *Add WLAN AutoConfig service for Windows image* (wlansvc varsayılan yok) | https://github.com/actions/runner-images/issues/9305 | Orta |
| K31 | GitHub runner-images Issue #12486 — *Please add the VS2019 toolset to the 2022 runner* | https://github.com/actions/runner-images/issues/12486 | Orta |
| K32 | Stack Overflow — *How to get rid of _WIN32_WINNT not defined warning?* (varsayılan = MAXVER) | https://stackoverflow.com/questions/12871513/how-to-get-rid-of-win32-winnt-not-defined-warning | Düşük (kanıt) |
| K33 | Microsoft Support — *Faster and more secure Wi-Fi in Windows* (Wi-Fi 7 = Windows 11 24H2) | https://support.microsoft.com/en-us/windows/experience/connectivity-networking/faster-and-more-secure-wi-fi-in-windows | Yüksek |
| K34 | *(yerel)* Neva Connect 01 — wlanapi.dll sürüm/export matrisi (XP 36 export) | [[01-wlanapi-sürüm-matrisi-ve-windows-plani]] | Yüksek (MD5+REA kanıtı) |
| K35 | Microsoft Learn — *Wireless LAN overview in Windows Server* (WLAN AutoConfig servisi) | https://learn.microsoft.com/en-us/windows-server/networking/technologies/wlan/wireless-networking | Yüksek |
| K36 | Microsoft Learn — *LoadLibraryA function* | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibrarya | Yüksek |
| K37 | Microsoft Learn — *Visual Studio Build Tools workload and component IDs* (bileşen adları) | https://learn.microsoft.com/en-us/visualstudio/install/workload-component-id-vs-build-tools?view=visualstudio | Yüksek |
| K38 | Super User — *Does a stock Windows XP SP3 install include the Visual C++ runtime?* | https://superuser.com/questions/1459256/does-a-stock-windows-xp-sp3-install-include-the-visual-c-2005-runtime | Düşük (kanıt) |
| K39 | Microsoft Learn — *QueryOptionalDelayLoadedAPI* (delay-load = runtime dynlink alternatifi) | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi2/nf-libloaderapi2-queryoptionaldelayloadedapi | Yüksek |
| K40 | Stack Overflow — *Significance of /SUBSYSTEM parameter* (alt-sistem sürümü semantiği) | https://stackoverflow.com/questions/14353668/significance-of-subsystem-parameter-msdn | Düşük (kanıt) |
| K41 | Colin Finck — *Modern Visual Studio meets ancient Windows* (v141_xp pratik rehberi) | https://colinfinck.de/posts/modern-visual-studio-meets-ancient-windows/ | Orta |
| K42 | Microsoft Tech Community — *Introducing Wi-Fi 7 for enterprise connectivity* | https://techcommunity.microsoft.com/blog/windows-itpro-blog/introducing-wi-fi-7-for-enterprise-connectivity/4456751 | Orta |

**Araştırmada BULUNAMAYAN / uydurulmayanlar:**

- "Define header'dan önce" kuralını taşıyan **doğrudan Microsoft Q&A sayfası** → bulunamadı;
  otorite olarak [K4], [K5] kullanıldı (bkz §2.5 notu).
- **XP x64 için `5.02` alt-sistem sürümünün** resmi zorunluluğu → bulunamadı → §8 #6.
- **`CMAKE_SYSTEM_VERSION=5.01`** değerinin XP için doğrulanmış örneği → bulunamadı → §8 #8.
- **CI'da canlı `v141_xp` derlemesi** → hiç çalıştırılmadı → §8 #7.
- **"SDK 8.1 → Win7 hedefi"** ifadesinin doğrudan Microsoft kaynağı → bulunamadı → §8 #13.

---

**Authority:** Researcher Agent (dispatch: 02 doc üretimi) · **Mode:** Zero-Hallucination · Truth Mode
**Status:** plan — **kod yazımı YOK**, §8 açık konuları kullanıcı onayı bekliyor.
