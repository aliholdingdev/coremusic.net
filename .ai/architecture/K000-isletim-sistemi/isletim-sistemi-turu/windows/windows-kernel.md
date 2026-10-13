---
title: "Windows Kernel — User/Kernel Mode, Executive, IRQL ve Sürücü Modelleri"
type: architecture-deepdive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel
status: draft
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Windows Kernel (windows-kernel.md)

**ELI15:** Windows'un kalbi olan kernel (çekirdek) modu, donanıma en yakın çalışan
koddur. Bu dosya: ring 0/ring 3 ayrımını, NT executive'in yönetici kümesini, sürücü
kodunu yöneten IRQL öncelik seviyelerini (PASSIVE/APC/DISPATCH/DIRQL), kesme
(ISR) → ertelenmiş işlem (DPC) desenini ve sürücü modeli ailesini (WDM · KMDF ·
UMDF · ACX) anlatır. CoreMusic bağlamında ses sürücüleri (Portcls.sys, Ks.sys,
Usbaudio.sys) ve ACX bu katmanda konumlanır.

**Kapsam / sınır:** Win32 API/COM → `windows-api.md` · CRT/WDK kurulumu →
`windows-framework.md` §7 · Core Audio API'leri → `windows-ses-yigini.md` ·
ASIO → `K003-audio/backend/asio/` (ayrı deep-dive). Bu dosya yalnız kernel-mode
dünyasını anlatır.

| Öğe | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel |
| Durum | draft · Şartname: ✅ IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0) |
| Birincil kaynak | learn.microsoft.com/windows-hardware/drivers/**kernel** + **wdf** |
| Tarih | 2026-10-12 |

---

## §1 Neden Kernel Ayrımı Var?

### §1.1 Bu Dosyanın Okuma Haritası

| Bölüm | Kimin için | Ana soru |
|---|---|---|
| §1–§2 | Tüm okuyucu | Kernel ne, IRQL ne demek? |
| §3 | Sürücü/driver okuyucusu | Executive neyi sağlar? |
| §4–§5 | Sürücü yazacak olan | Rutinler ve sürücü tipleri hangileri? |
| §6 | Platform kararı veren (win-sw) | WDM/KMDF/UMDF/ACX'ten hangisi? |
| §7–§8 | CoreMusic mimarı | Bizim yığınımız nerede duruyor? |

Microsoft, sürücü geliştirme dokümanlarının girişinde (User Mode and Kernel Mode)
iki modu tanımlar:

1. **User mode (ring 3):** uygulamalar ve user-mode DLL'leri çalışır; her uygulama
   kendi sanal adres alanıyla sınırlıdır, bir uygulamanın hatası genelde yalnız
   o uygulamayı etkiler.
2. **Kernel mode (ring 0):** çekirdek, executive ve sürücüler çalışır; tüm sistem
   belleğine ve donanıma erişebilir. Kernel mode'daki bir hata sistemi
   çökertecek şekilde sonuçlanabilir.

Sürücü neden kernel mode'dadır? Çünkü sürücü doğrudan donanım register'larına,
DMA'ya ve fiziksel adreslere erişmek zorundadır; bu erişim user mode'da verilmez.
Karşılıklı istisna: **UMDF** (User-Mode Driver Framework), sürücünün bir user-mode
host sürecinde çalışmasına izin verir (§6).

```text
        Uygulama (ring 3)                    Sürücü (ring 0)
  +--------------------------+        +--------------------------+
  | Neva Engine · PHP · Node |        | Portcls.sys · Ks.sys ... |
  | kernel32/advapi32/...    |        | KMDF/WMDE runtime        |
  +-----------+--------------+        +------------+-------------+
              |  NTDLL syscall                     │
              +----------------→ NTOSKRNL ←--------+
                                 (Executive+Kernel)
```

**Kaynaklar:** `gettingstarted/user-mode-and-kernel-mode` ·
`kernel/overview-of-windows-components`.

---

## §2 IRQL Modeli — Sürücü Kodunun Öncelik Dili

### §2.1 Tanım

IRQL (Interrupt Request Level), kodun hangi öncelik seviyesinde çalıştığını
belirtir. Microsoft docs `managing-hardware-priorities` sayfasında seviyeleri
tanımlar:

| IRQL | Seviye | Ne zaman kullanılır | Örnek |
|---|---|---|---|
| **PASSIVE_LEVEL** | en düşük | Normal thread çalışması; pageable kod burada | DriverEntry, Dispatch rutinleri, IRP_MJ_* işleyicileri |
| **APC_LEVEL** | – | Alertable beklemeler | Nadir; special APC kullanımı |
| **DISPATCH_LEVEL** | – | DPC'ler ve dispatch kodu; sayfa değiştirme YAPILAMAZ | IoAllocateDPC ile planlanan işler |
| **DIRQL** | en yüksek | Device IRQL — donanıma özgü kesmeler | ISR (kesme servis rutini) |

**Temel kural (docs):**

- IRQL yükseldikçe çalışan kod pageable olamaz (sayfa diske çıkarılamaz), çünkü
  DISPATCH_LEVEL ve üstünde pageable sayfa hatası mümkündür ve bu bir BUG
  (sistem çöküşü) üretir.
- `KeRaiseIrQL` / `KeLowerIrQL` benzeri API'lerle seviye yükseltilir/alçaltılır
  (API adlarının birebir imzası bu görevde tekil doğrulanmadı — ⚠️ VERIFICATION
  REQUIRED, yalnız kavram docs'ta).

### §2.2 ISR → DPC Deseni

Microsoft docs `introduction-to-interrupt-service-routines` ve
`dispatch-routines-and-irqls` sayfaları klasik deseni anlatır:

```text
Donanım kesmesi (IRQ)
   │
   ▼
ISR — Interrupt Service Routine   (DIRQL, KISA sürer)
   │  - donanımın neden kesme verdiğini anlar
   │  - minimal iş: veriyi al/temizle
   │  - kesmeyi yeniden etkinleştir (IoRequestInterrupt... ⚠️ imza doğrulanmadı)
   ▼
DPC — Deferred Procedure Call     (DISPATCH_LEVEL, planlanır)
   │  - ağır iş burada: tamamlama, kuyruk işleme
   ▼
İlgili sürücü rutini / completion
```

**Neden?** ISR kısa kalmalıdır çünkü tüm sistem ISR sırasında bekler (yüksek
IRQL). Uzun iş DPC'ye devredilir — ses tamponu doldurma/boşaltma mantığı da
donanım kesmeleriyle tetiklenir.

**CoreMusic ilgisi:** ASIO/WASAPI sürüşünde uygulama kodu user mode'dadır; ISR/DPC
yalnızca sürücü ve miniport katmanında çalışır. Neva Engine'in RT callback'i
(`.ai/brain.md` §7.1) user mode'da, AVRT önceliğiyle çalışır — IRQL kuralı
uygulama kodunu **etkilemez**; yalnızca nezaket (Windows'un callback'inizi
DISPATCH_LEVEL'a taşımadığı) anlamına gelir. ⚠️ Ayrıntı
`windows-ses-yigini.md` §5.

### §2.3 IRQL ve Dispatch Rutinleri

WDK `dispatch-routines-and-irqls` sayfası, sürücü dispatch rutinlerinin hangi
IRQL'de çağrıldığını tablolar (ör. `IRP_MJ_CREATE` gibi istek işleyicileri genelde
PASSIVE_LEVEL'da gelir). Kesin tablo satırları için kaynak sayfa; bu dosyada
özet düzeyinde tutulur, tekil satır iddiası yazılmaz (⚠️ detay doğrulama
`windows-dogrulama.md`).

**IRQL anotasyonları:** Microsoft, sürücü kodu için IRQL anotasyonlarını
(`_IRQL_requires_max_`, `_IRQL_requires_min_` benzeri SAL anotasyonları) bir
başlık altında belgeler ("IRQL annotations" sayfası). Sürücü başlıkları bu
anotasyonlarla işaretlenir; araçlar (Code Analysis) ihlali raporlar. Birebir
anotasyon adları ⚠️ VERIFICATION REQUIRED (sayfa adı doğrulandı, imzalar bu
görevde tek tek taranmadı).

### §2.4 IRQL Karar Checklist'i (sürücü yazarı gözünden)

| # | Soru | Evet → sonuç |
|---|---|---|
| 1 | Kod pageable bellek kullanabilir mi? | Sadece PASSIVE/APC_LEVEL'da |
| 2 | Bekleme (wait) gerekiyor mu? | DISPATCH_LEVEL'da yasak; PASSIVE'e devret |
| 3 | Kesme (interrupt) bağlamında mıyım? | DIRQL — ISR kuralları geçerli |
| 4 | Tamamlama (completion) çağrılıyor mu? | DISPATCH_LEVEL olabilir; pageable veri taşıma |
| 5 | Uygulama callback'imiz varsa (Neva Engine)? | Uygulama user mode'da; IRQL kuralı geçerli değil — AVRT yeterli (`windows-ses-yigini.md` §5) |

> Bu checklist docs kavramlarından (managing-hardware-priorities +
> dispatch-routines-and-irqls) türetilmiş **özet**tir; birebir docs tablosu
> değildir — "türetilmiş" işareti korunur.

---

## §3 NT Executive — Yönetici Kümesi (Manager'lar)

Kaynak: `kernel/overview-of-windows-components` (WDK). Executive, NTOSKRNL
içindeki yüksek seviye hizmetlerdir; sürücüyle en çok ilgili olanlar I/O Manager
ve KTM'dir.

### §3.1 Yönetici Tablosu

| Yönetici | Görev (docs özeti) | Ses yığınındaki izi |
|---|---|---|
| **Object Manager** | Kernel nesnelerinin (handle, event, semaphor) yaratılması/öklenmesi | Dosya handle'ları, event'ler |
| **Memory Manager** | Sanal/fiziksel bellek, sayfalama, DMA için kilitli sayfalar | Tampon paylaşımı (user↔kernel) |
| **Process-Thread Manager** | Süreç ve thread yaşam döngüsü | Neva Engine process'i, audiodg thread'leri |
| **I/O Manager** | İ/O istek paketleri (IRP) üretir ve sürücü yığınına gönderir | Ses IRP'leri miniport'a kadar |
| **PnP Manager** | Plug and Play — cihaz keşfi, eşleştirme, ekleme/çıkarma | USB audio tak/çıkar olayları |
| **Power Manager** | Güç durumu geçişleri | Cihaz uyku/uyanma → akış koptu (`AUDCLNT_E_DEVICE_INVALIDATED`) |
| **Configuration Manager** | Registry (yapılandırma) | Ses cihaz tercihleri |
| **KTM** | Kernel Transaction Manager — atomik işlemler | — |
| **Security Reference Monitor** | Erişim kontrolü (access check) | Cihaz erişim izinleri |

### §3.2 Kütüphaneler (Library'ler)

WDK `overview-of-windows-components` + `libraries-and-headers` sayfaları
sürücü kodunun kullanabileceği kütüphaneleri listeler:

| Kütüphane | Rol |
|---|---|
| **Kernel library** | Çekirdek hizmetleri (thread, senkronizasyon, DPC) |
| **Executive support library** | Executive destek rutinleri (liste, kuyruk, ERESOURCE benzeri veri yapıları — imzalar ⚠️) |
| **RTL (Runtime Library)** | Düşük seviye yardımcılar (string, liste, RB-tree) |
| **Safe String Library** | Güvenli string/dizi rutinleri (`RtlSafe...` ailesi — imza ⚠️) |
| **DMA Library** | DMA transferleri için yardımcılar |
| **HAL library** | HAL hizmetlerine erişim (`windows-kernel-mode-hal-library` sayfası) |
| **CLFS** | Common Log File System kütüphanesi |
| **WMI** | Windows Management Instrumentation desteği |

### §3.3 I/O Manager — IRP'ler ve Sürücü Yığınıları

Kaynak: `kernel/windows-kernel-mode-i-o-manager`.

- I/O Manager, user mode'dan gelen istekleri **IRP (I/O Request Packet)** biçiminde
  paketler ve doğru sürücü yığınına (device stack) gönderir.
- IRP, birden fazla sürücünün bulunduğu yığında **her katmanda işlenerek** aşağı
  iner; her sürücü kendi I/O stack location'ını doldurur/ilerletir.
- Sürücü, IRP'yi tamamlayabilir (completion) veya üst katmana geri
  gönderebilir/başka sürücüye devredebilir.
- Tamamlama çağrıları DISPATCH_LEVEL benzeri yüksek IRQL'de yapılabilir
  (birebir kural ⚠️ — sayfa özeti, imza doğrulanmadı).

**Ses bağlamı:** Uygulama WASAPI'de `IAudioClient::Initialize` dediğinde user-mode
DLL'ler (mmdevapi/audioses) sonunda I/O Manager üzerinden sürücü yığınına iner;
ISRs ve DPC'ler cihazdan veriyi taşır (`windows-ses-yigini.md` §2 bu akışı
genişletir).

### §3.4 Nt/Zw — Kernel ve User Tarafı Aynası

WDK `libraries-and-headers` sayfası: `Ntoskrnl.exe` içindeki **Nt**/**Zw** sistem
servisleri kernel-mode'dan çağrılır; aynı NT servislerinin **user-mode stub'ları
NTDLL.DLL** içindedir. Bu, §1'deki ring geçişinin hedefidir:

```text
Uygulama → Win32 DLL → Ntdll.dll (Nt*/Zw* stub) → syscall → NTOSKRNL (Nt*/Zw*)
```

> ⚠️ Not: Zw vs Nt'nin semantik farkı (Zw her zaman önce "previous mode=kernel"
> varsayar) sürücü literatüründe yaygındır; bu görevde tekil Microsoft sayfası
> doğrulanmadığı için **VERIFICATION REQUIRED** olarak işaretlidir.

---

## §4 Standart Sürücü Rutinleri

Kaynak: `kernel/introduction-to-standard-driver-routines`.

Her sürücünün yaşadığı tipik yaşam döngüsü:

```text
Sürücü yüklenir
   │
   ▼
DriverEntry (WDM) / EvtDriverDeviceAdd (KMDF)   ← giriş/kurulum
   │
   ▼
AddDevice / device object oluşturma              ← cihaz ekleme
   │
   ▼
Dispatch rutinleri (IRP_MJ_* / KMDF EvtIo*)     ← çalışma dönemi
   │     (PASSIVE/DISPATCH IRQL — §2)
   ▼
Unload / cleanup                                  ← kaldırma
```

| Rutin | WDM karşılığı | KMDF karşılığı | Not |
|---|---|---|---|
| Sürücü girişi | `DriverEntry` | `EvtDriverDeviceAdd` (DriverEntry KMDF'e iletilir) | Sürücü nesnesini kaydeder |
| Cihaz ekleme | `AddDevice` | PnP/Power olay callback'leri | Donanım keşfinde |
| İstek işleme | Dispatch rutinları (IRP işleyicileri) | EvtIo* olayları (queue üzerinden) | KMDF IRP'yi soyar |
| Kaldırma | Unload rutini | `EvtDriverUnload` benzeri | ⚠️ birebir ad doğrulanmadı |

**UMDF tarafı:** UMDF 2 sürücüsü C arayüzleriyle yazar (`DriverEntry` benzeri
giriş + olay callback'leri); UMDF 1 COM/C++ tabanlıdır (deprecated) — §6.

---

## §5 Sürücü Tipleri

Kaynak: `kernel/types-of-windows-drivers`.

| Tip | Ne yapar | Ses örneği |
|---|---|---|
| **Software driver** | Donanıma değil yazılım/özel cihaza bağlanır | Sanal ses cihazı (⚠️ örnektir, doğrulanmadı) |
| **Virtual driver** | Gerçek donanımı taklit eder | — |
| **Root-enumerated driver** | PnP manager'ın oluşturduğu kök cihazlara bağlanır | — |
| **Bus driver** | Alt cihazları keşfeder/yönetir | USB denetleyicisi (Usbaudio üstü) |
| **Function driver** | Cihazın ana işleyicisi | Ses miniport (function) |
| **Filter driver** | Yığına üstten/alttan eklenir; istekleri değiştirir | APO'lar (ses üzerinde) |

> Port/miniport ayrımı (ses özelinde): Microsoft audio dokümanlarında ses
> minidriver'ı "port miniport" modeliyle yazılır — port tarafı sınıf
> kütüphanesine (Portcls.sys) bağlanır, miniport donanıma bakar. Ayrıntı
> `windows-ses-yigini.md` §6.

---

## §6 Sürücü Modeli Ailesi — WDM · KMDF · UMDF · ACX

### §6.1 Hangi Model? (choosing-a-driver-model)

Microsoft, `choosing-a-driver-model` sayfasında modele göre seçim tablosu verir
(özet): yeni bir sürücü için WDF önerilir; WDM yalnız özel durumlarda; UMDF,
donanıma user-mode'dan erişilebiliyorsa tercih edilir. Ses minidriver'ları için
sınıf framework'leri (Portcls/KS/ACX) geçerlidir.

### §6.2 WDM vs KMDF

Kaynak: `wdf/differences-between-wdm-and-kmdf`.

| Konu | WDM (raw) | KMDF |
|---|---|---|
| IRP yönetimi | Sürücü IRP'yi elle alır/işler/devreder | Framework IRP'yi soyar; sürücü Evt olaylarını dinler |
| PnP/Power | Elle state machine | Framework yönetir; sürücü callback verir |
| Senkronizasyon | Kilit/el işi | Queue + framework nesne modeli |
| Kod hacmi | Azı (küçük sürücü) dahil yüksek | Daha az tekrar eden kod |
| Öğrenme eğrisi | IRQL/IRP bilgisi şart | Daha yüksek seviye API |

**Gerçek:** KMDF da **kernel mode'da** çalışır; fark yüksek seviye framework
katmanıdır, mod değil.

### §6.3 KMDF / UMDF sürümleri

Kaynak: `wdf` ana sayfa (sürümler tablosu).

| Windows sürümü | KMDF sürümü | UMDF sürümü |
|---|---|---|
| Windows 11 / Server 2022 | **1.33** | **2.33** |
| (önceki sürümler için sayfadaki tablo) | ⚠️ tekil satırlar `windows-dogrulama.md` §3'te | ⚠️ |

> Bu dosyada kesin kullanılan satır: **Windows 11 & Server 2022 → KMDF 1.33 ·
> UMDF 2.33** (docs `wdf` sayfası). Diğer satırlar doğrulama tablosuna
> referansla alınır.

### §6.4 UMDF — User-Mode Driver Framework

Kaynaklar: `wdf/overview-of-the-umdf` · `wdf/detailed-view-of-the-umdf-architecture`
· `wdf/comparing-umdf-2-0-functionality-to-kmdf` · `wdf/getting-started-with-umdf-version-2`.

| Özellik | UMDF 1 | UMDF 2 |
|---|---|---|
| Dil | COM / C++ | **C** |
| Minimum sürüm | Windows Vista | Windows 8.1+ |
| Durum | Deprecated (yeni sürücü yazma) | Güncel |
| KMDF işlevselliği | Kısmi | KMDF 2 ile karşılaştırılır (sayfa: "comparing UMDF 2.0 functionality to KMDF") |

**UMDF mimarisi (detailed-view sayfası):**

```text
UMDF sürücüsü (DLL)
   │
   ▼
Reflector (kernel-mode bileşeni, WUDFRd)  ← user/kernel geçişini taşır
   │
   ▼
WUDFHost (Wudfhost.exe — sürücünün çalıştığı user-mode host süreci)
   │
Driver Manager (WUDFMgr — host yaşam döngüsü, yükler/başlatır/durdurur)
```

- Sürücü, driver manager tarafından **host sürecinde** (Wudfhost) yüklenir;
  kernel-mode'daki reflector, I/O'yu host'a taşır.
- UMDF sürücüleri için CRT kuralı ayrı sayfadadır
  (`develop/using-the-microsoft-c-runtime-with-user-mode-drivers-and-apps`):
  **WindowsUserModeDriver10.0** toolset'i — statik C++ CRT + dinamik UCRT;
  ayrıntı `windows-framework.md` §4.

**Neden UMDF?** Donanıma doğrudan DMA/IRQ erişimi gerektirmeyen, yüksek seviye
protokollerde sürücü yazmak isteyenler için. Ses uç nokta sürücüleri genelde
kernel-mode sınıf yığını kullanır; UMDF'in ses ile ilgisi bu görevde
**doğrulanmadı** → ⚠️ VERIFICATION REQUIRED.

### §6.5 ACX — Windows 11 Ses Sürücü Modeli

Kaynak: `windows-hardware/drivers/audio/acx-*` (ACX overview · ACX 1.1).

- ACX (Audio Class eXtension), Windows ses sürücüleri için **üst seviye nesne
  modeli** getirir; **KMDF üzerine** inşa edilmiştir.
- Docs gerekçesi: UMDF yerine KMDF seçilmiştir; çünkü UMDF'te user/kernel
  süreç geçişi (task-switch) ses için gecikme yaratır (latency maliyeti).
- ACX 1.1 güncel sürümdür; Windows 10 2004+ (KMDF 1.31) ile başlar (sürüm
  satırı ⚠️ — sayfa özeti, tekil doğrulama `windows-dogrulama.md`).
- ACX nesneleri: device, bundle, module, topology, circuit, widget vb. (nesne
  listesi docs'ta; birebir adlar ⚠️ doğrulanmadı).

**CoreMusic ilgisi:** ADR-017'deki ASIO/WASAPI host, ACX'in üzerinde çalıştığı
Windows ses sürücü altyapısına (WASAPI tarafı) bağlanır; ACX'in kendisi
sürücü-yazan tarafın konusudur (firmware/driver değilse dokunulmaz — yalnız
kavramsal konum).

---

## §7 HAL ve Donanım Soyutlama

Kaynak: `kernel/windows-kernel-mode-hal-library`.

- HAL, donanım farklılıklarını (interrupt controller, DMA, timer) çekirdeğin
  önünde soyutlar; sürücüler doğrudan donanımın ham register'ına değil HAL/
  kaynak API'lerine bağlanır (bus/sürücü katmanı hariç).
- HAL library, sürücüden HAL hizmetlerine erişimi sağlar (birebir fonksiyon
  listesi ⚠️ VERIFICATION REQUIRED).

**Neden önemli?** Ses donanımının IRQ/DMA'sı farklı platformlarda farklıdır;
Portcls/miniport bu farklılığı porta yükler — mimarinin "correspondence"
notu (ALSA benzerliği) K003 ASIO/anayasa notlarında referans verilir.

---

## §8 CoreMusic Bağlamı — Kernel Tarafının Bizim İçin Ne İfadesi

| Konu | CoreMusic etkisi | Dosya |
|---|---|---|
| ASIO sürücüsü | ASIO driver'ı kernel-mode sürücüdür; COM host ile konuşur (asio/asiolist.cpp) | `K003-audio/backend/asio/` |
| WASAPI fallback | Uygulama user mode'da kalır; kernel tarafına I/O Manager ile inilir | `windows-ses-yigini.md` |
| Portcls/miniport | Donanım kaybı → PnP olayı → uygulamaya `AUDCLNT_E_DEVICE_INVALIDATED` | `windows-ses-yigini.md` §4 |
| ACX | Sürücü tarafı modeli; uygulama tarafı değildir | bu dosya §6.5 |
| IRQL | Uygulama kodunu etkilemez; sürücü/driver dokümanıdır | bu dosya §2 |
| UMDF/CRT | Yalnız sürücü yazarsak ilgilidir (WindowsUserModeDriver10.0) | `windows-framework.md` §4 |
| WDK | Sürücü derlemek için gerekir; SDK ile build eşleşmeli | `windows-framework.md` §7 |

**Durum etiketi:** Bu dosya şartnamedir — ✅ IMPLEMENTED (belge). Çekirdek/RT C++
kodu yoktur: ⚠️ PLANNED (cpp=0, `projects/NevaEngine` = 0).

### §8.1 Ses Sürücülerinin Kernel/User Yerleşimi (özet — ayrıntı `windows-ses-yigini.md`)

Kaynak: `windows-hardware/drivers/audio/windows-audio-architecture` +
audio-components sayfaları.

| Bileşen | Mod | Rol |
|---|---|---|
| audiodg.exe | user (özel süreç) | Audio engine + APO'lar yüklenir (audioeng.dll) |
| audiosrv.dll / audioendpointbuilder.exe | user/servis | Windows Audio servisi zinciri |
| mmdevapi.dll / audioses.dll | user | Core Audio API uygulaması (MMDevice, oturum) |
| Portcls.sys | **kernel** | Port/miniport sınıf kütüphanesi (WDM ses) |
| Ks.sys (AVStream) | **kernel** | Kernel Streaming / AVStream minidriver altyapısı |
| Usbaudio.sys | **kernel** | USB ses sınıfı sürücüsü |
| SysAudio.sys | **kernel** | Sistem ses yönlendirme (topoloji) |
| Audiokse.dll (KSEndpoint) | kernel-side KS bileşeni | KS uç nokta keşfi (docs: audio components) |
| WDMAud.drv/.sys | kernel/user köprüsü | Legacy WinMM/MMS yolu |
| KMixer | **kernel** (legacy) | Eski kernel mixer (WASAPI öncesi) — docs'ta legacy konumu |

> **ACX notu (§6.5):** Windows 11 ACX modeli bu kernel tarafını yeniden düzenler;
> uygulama tarafı (Neva Engine) yalnız WASAPI/ASIO üzerinden görür.

---

## §9 Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q-K1 | Zw vs Nt semantik farkı için tekil Microsoft sayfası var mı? | ⚠️ VERIFICATION REQUIRED |
| Q-K2 | IRQL anotasyon adlarının (`_IRQL_requires_*`) birebir SAL listesi doğrulandı mı? | ⚠️ VERIFICATION REQUIRED (sayfa var, imza taraması yok) |
| Q-K3 | Windows 10 KMDF/UMDF sürüm satırları (docs tablosu) tam çekildi mi? | ⚠️ kısmi — yalnız Win11/Server 2022 kesin |
| Q-K4 | UMDF'in ses uç noktalarıyla ilişkisi? | ⚠️ VERIFICATION REQUIRED (bu görevde yok) |
| Q-K5 | ACX nesne listesinin birebir adları (bundle/module/circuit...)? | ⚠️ listelendi, imzalar doğrulanmadı |
| Q-K6 | Portcls.sys + Port/miniport modelinin ALSA ile eşdeğerlik iddiasının kaynağı? | UNKNOWN (vault içi not — bu görevde dış kaynak doğrulanmadı) |

---

## §10 Kaynak URL Listesi

1. `https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/user-mode-and-kernel-mode`
2. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/overview-of-windows-components`
3. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/managing-hardware-priorities`
4. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/introduction-to-interrupt-service-routines`
5. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/dispatch-routines-and-irqls`
6. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/windows-kernel-mode-i-o-manager`
7. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/windows-kernel-mode-executive-support-library`
8. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/windows-kernel-mode-hal-library`
9. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/introduction-to-standard-driver-routines`
10. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/types-of-windows-drivers`
11. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/libraries-and-headers`
12. `https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/choosing-a-driver-model`
13. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/differences-between-wdm-and-kmdf`
14. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/` (KMDF 1.33 / UMDF 2.33)
15. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/overview-of-the-umdf`
16. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/detailed-view-of-the-umdf-architecture`
17. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/comparing-umdf-2-0-functionality-to-kmdf`
18. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/getting-started-with-umdf-version-2`
19. `https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/using-the-microsoft-c-runtime-with-user-mode-drivers-and-apps`
20. `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/acx-overview` (ACX — sayfa yolu ⚠️, başlık doğrulandı)

---

## §11 İlişkili Dosyalar

- `windows-anayasa.md` — NT zaman çizelgesi + katmanlar (§2 IRQL buradaki §2'ye öncülük eder)
- `windows-api.md` — NTDLL'nin user-mode karşılığı, Win32 DLL'leri
- `windows-framework.md` — WDK/SDK kurulumu, UMDF CRT kuralı
- `windows-ses-yigini.md` — Portcls/KS/audiodg/ACX konumlandırma (ses özelinde)
- `windows-dogrulama.md` — iddia · URL · statü tablosu
- `K003-audio/backend/asio/` — ASIO (sınır dışı)

---

## §12 Terim Sözlüğü

| Terim | Tanım |
|---|---|
| **IRQL** | Interrupt Request Level — kodun çalıştığı öncelik seviyesi (PASSIVE/APC/DISPATCH/DIRQL) |
| **ISR** | Interrupt Service Routine — kesme servis rutini (DIRQL, kısa) |
| **DPC** | Deferred Procedure Call — ertelenmiş işlem (DISPATCH_LEVEL) |
| **IRP** | I/O Request Packet — I/O Manager'ın sürücüye gönderdiği istek paketi |
| **WDM** | Windows Driver Model — düşük seviye (raw) sürücü modeli |
| **KMDF** | Kernel-Mode Driver Framework — WDF'ın kernel-mode sürümü |
| **UMDF** | User-Mode Driver Framework — host süreçte çalışan sürücü (reflector) |
| **WDF** | Windows Driver Framework — KMDF + UMDF çatısı |
| **ACX** | Audio Class eXtension — KMDF üzerinde Windows 11 ses sürücü modeli |
| **Port/miniport** | Ses minidriver'ı: port = sınıf kütüphanesi (Portcls), miniport = donanım |
| **HAL** | Hardware Abstraction Layer — donanım soyutlama |
| **Executive** | NTOSKRNL içindeki yönetici kümesi (§3.1) |
| **Reflector** | UMDF'ın kernel-side köprü bileşeni (WUDFRd) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Status:** draft — tüm iddialar `windows-dogrulama.md` §3'te URL ile kayıtlı;
⚠️ işaretli satırlar doğrulanmadan kesin sayılmaz (zero-hallucination).
**Durum etiketi:** Şartname: ✅ IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0).

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode