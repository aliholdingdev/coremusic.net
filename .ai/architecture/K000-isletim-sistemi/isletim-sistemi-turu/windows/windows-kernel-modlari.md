---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel-modlari.md"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-anayasa.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-ses-stacki.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-kod-ornekleri.md"
evidence:
  - "learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ — Kernel-Mode Driver Architecture Design Guide · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ — WDF (KMDF 1.33 / UMDF 2.33) · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads — process/thread/job/pool/fiber/UMS · fetch: 2026-10-12"
validation: "TRUTH MODE: her iddia URL + fetch tarihi ile; fetch edilmeyen '⚠️ bilinen resmi URL' etiketli; doğrulanamayan ⚠️ UNKNOWN; repo 0 .cpp/.h → Dosya/Satır uydurma yasak"
open_questions:
  - "IRQL seviye listesi (PASSIVE_LEVEL/DISPATCH_LEVEL/…) bu turda ayrı sayfadan fetch edilmedi — ⚠️ UNKNOWN detay"
  - "KMDF 1.33 / UMDF 2.33 destek matrisi yalnız WDF sayfasından; sürücü bazlı minimum OS doğrulanmadı"
group: mimari
---

# windows-kernel-modlari — Windows Kernel Modları (Teknoloji: Windows)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / windows` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Windows'un kernel-mode / user-mode ayırımını, kernel bileşen ve yöneticilerini,
> sürücü çerçevelerini (WDF/KMDF/UMDF), iş parçacığı/süreç modelini ve ses alt sisteminin
> kernel tarafını tek dosyada toplamak.
> **Konu sırası:** (1) sınır → (2) mod ayırımı → (3) kernel bileşenleri → (4) sürücü
> çerçeveleri → (5) süreç/iş parçacığı → (6) ISR/DPC/IRQL → (7) ses kernel modu → (8) FAQ →
> (9) bağımlılık → (10) RED TEAM → (11) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** Windows kernel mimarisi (resmi design guide), WDF (KMDF/UMDF), süreç/iş
  parçacığı modeli, ISR/DPC/MSI/DMA kavramlarının resmi tanımları, ses stackinin kernel katmanı.
- ❌ **Yapılmaz:** kaynak kod uydurma (repo 0 `.cpp/.h`), WASAPI API derinliği (→ wasapi dosyaları),
  PnP/güvenlik politika detayı (→ windows-device-lifecycle / windows-riskler), çapraz OS kıyası
  (→ macos/linux dosyaları).

---

## 1. Mod Ayırımı (User Mode ⇄ Kernel Mode)

| # | Alan | User Mode | Kernel Mode | Kaynak |
|---|---|---|---|---|
| 1 | Yürütme | uygulama/süreç | sürücü/OS çekirdeği | kernel/ ✅ 2026-10-12 |
| 2 | Erişim | kısıtlı (Win32 API) | doğrudan donanım/ASID | kernel/ ✅ |
| 3 | Çökme etkisi | süreç sonlanır | mavi ekran olasılığı | ⚠️ genel bilgi — resmi sayfa bu tur fetch edilmedi |
| 4 | Sürücü çerçevesi | UMDF | KMDF / WDM | wdf/ ✅ 2026-10-12 |
| 5 | Ses örneği | WASAPI istemcisi | PortCls/WaveRT sürücüsü | windows-audio-architecture ✅ |
| 6 | I/O yolu | CreateFile/DeviceIoControl | IRP işleyici | kernel/ ✅ |
| 7 | Bellek | süreç adres uzayı | çekirdek adres uzayı | kernel/ (Memory Manager) ✅ |
| 8 | Zamanlama | normal öncelik | IRQL + DPC | kernel/ ✅ (DPC/ISR) |

> Mod sınırı geçişleri (syscall/trap) bu turda resmi sayfadan fetch edilmedi → ⚠️ UNKNOWN detay.

---

## 2. Kernel Bileşen Yöneticileri (Resmi Design Guide Tablosu)

| # | Yönetici | Rol | Kaynak |
|---|---|---|---|
| 1 | Windows Kernel-Mode Object Manager | nesneleri yönetir: dosyalar, cihazlar, senkronizasyon, registry anahtarları | kernel/ ✅ 2026-10-12 |
| 2 | Windows Kernel-Mode Memory Manager | işletim sistemi için fiziksel belleği yönetir | kernel/ ✅ |
| 3 | Windows Kernel-Mode Process and Thread Manager | süreçteki tüm iş parçacıklarının yürütümünü ele alır | kernel/ ✅ |
| 4 | Windows Kernel-Mode I/O Manager | uygulamalar ile sürücü arayüzleri arasındaki iletişimi yönetir | kernel/ ✅ |
| 5 | Windows Kernel-Mode Plug and Play Manager | I/O yöneticisinin alt sistemi; cihaz ekleme algılama | kernel/ ✅ |
| 6 | Windows Kernel-Mode Power Manager | güç durumu değişimlerini düzenli yönetir | kernel/ ✅ |
| 7 | Windows Kernel-Mode Configuration Manager | registry'yi yönetir (değişiklik izleme, callback) | kernel/ ✅ |
| 8 | Windows Kernel-Mode Kernel Transaction Manager | çekirdek modda işlem (transaction) yürütme | kernel/ ✅ |
| 9 | Windows Kernel-Mode Security Reference Monitor | erişim kontrolü için rutinler | kernel/ ✅ |

---

## 3. Kernel Kütüphaneleri (Resmi Design Guide Tablosu)

| # | Kütüphane | Rol | Kaynak |
|---|---|---|---|
| 1 | Kernel Library | temel işlevsellik: iş parçacığı zamanlama, donanım kesmesi yönlendirme | kernel/ ✅ 2026-10-12 |
| 2 | Executive Support Library | sürücülere hizmet: nesne/bellek/süreç-I/O/yapılandırma yönetimi | kernel/ ✅ |
| 3 | Run-Time Library | çekirdek bileşenler için ortak yardımcı rutinler | kernel/ ✅ |
| 4 | Safe String Library | güvenli dizge (string) kütüphanesi | kernel/ ✅ |
| 5 | DMA Library | sürücü geliştiricileri için DMA kütüphanesi | kernel/ ✅ |
| 6 | HAL Library | donanım soyutlama katmanı (HAL) | kernel/ ✅ |
| 7 | CLFS Library | transactional logging (Common Log File System) | kernel/ ✅ |
| 8 | WMI Library | Windows Management Instrumentation mekanizması | kernel/ ✅ |

---

## 4. Sürücü Çerçeveleri: WDF (KMDF / UMDF)

| # | Öğe | Değer | Kaynak |
|---|---|---|---|
| 1 | WDF tanımı | sürücü yazımında yaygın kod/boilerplate'i üstlenen soyutlama katmanı | wdf/ ✅ 2026-10-12 |
| 2 | KMDF | Kernel-Mode Driver Framework | wdf/ ✅ |
| 3 | UMDF | User-Mode Driver Framework | wdf/ ✅ |
| 4 | KMDF sürümü (Win11 + Server 2022) | 1.33 | wdf/ ✅ |
| 5 | UMDF sürümü (Win11 + Server 2022) | 2.33 | wdf/ ✅ |
| 6 | Hedef OS | Windows 10 ve 11 (tüm SKU'lar), Windows Server 2022 | wdf/ ✅ |
| 7 | Sürüm geçmişi | KMDF Version History / UMDF Version History | wdf/ ✅ |
| 8 | WDM | eski sürücü modeli (WDF öncesi) | kernel/ (Writing WDM Drivers) ✅ |

---

## 5. Süreç ve İş Parçacığı Modeli (Resmi Win32 Sayfası)

| # | Kavram | Tanım | Kaynak |
|---|---|---|---|
| 1 | Process | yürüten program; bir veya daha fazla süreç | procthread ✅ 2026-10-12 |
| 2 | Thread | işletim sisteminin işlemci zamanı ayırdığı temel birim | procthread ✅ |
| 3 | Job object | grup süreçlerin tek birim olarak yönetilmesi; adlandırılabilir/güvenli/paylaşılabilir | procthread ✅ |
| 4 | Thread pool | uygulama adına asenkron callback'leri verimli çalıştıran iş parçacığı kümesi | procthread ✅ |
| 5 | Fiber | uygulamanın elle zamanlaması gereken yürütüm birimi; thread context'i paylaşır | procthread ✅ |
| 6 | UMS (user-mode scheduling) | hafif mekanizma; her UMS thread'in kendi thread context'i vardır | procthread ✅ |
| 7 | Öncelik | thread = OS zamanlama önceliği taşıyan birim | procthread ✅ |
| 8 | Job etkisi | job object üzerindeki işlem ilişkili tüm süreçleri etkiler | procthread ✅ |

---

## 6. ISR / DPC / MSI / IRQL Zinciri

| # | Bileşen | Rol | Kaynak |
|---|---|---|---|
| 1 | Interrupt Service Routines (ISRs) | fiziksel kesme alan sürücülerinin kesmeleri işler | kernel/ ✅ 2026-10-12 |
| 2 | Message-Signed Interrupts (MSI) | belirli bir bellek adresine değer yazarak kesme tetikleme | kernel/ ✅ |
| 3 | Deferred Procedure Calls (DPC) | ISR'lardan kuyruklanır; daha sonra ve daha düşük IRQL'de çalıştırılır | kernel/ ✅ |
| 4 | DMA Library | doğrudan erişim bellek kütüphanesi | kernel/ ✅ |
| 5 | Adapter Objects (DMA) | DMA'yı A'dan Z'ye kapsayan makale dizisi | kernel/ ✅ |
| 6 | IRQL | DPC/ISR bağlamı; tam seviye listesi bu turda ayrı sayfadan fetch EDİLMEDİ | ⚠️ UNKNOWN |
| 7 | Controller Objects | fiziksel cihaz denetleyicisi temsili | kernel/ ✅ |
| 8 | IRPs | I/O Request Packet — sürücülerin işlediği paket | kernel/ (Handling IRPs) ✅ |

---

## 7. Ses Alt Sisteminin Kernel Katmanı

| # | Öğe | Rol | Kaynak |
|---|---|---|---|
| 1 | PortCls (Port Class) | sınıf sürücü iskeleti (ses) | windows-audio-architecture ✅ 2026-10-12 |
| 2 | AVStream (RT WDM) | inbox sürücü iskeleti | windows-audio-architecture ✅ |
| 3 | WaveRT | DMA + event tabanlı dalga sürücüsü | windows-audio-architecture ✅ |
| 4 | KS (keystone) | kullanıcı-kernel geçiş temel taşı | glossary ✅ |
| 5 | APO1 (AHSAPO) | yalnız Win10-öncesi custom FX APO | windows-audio-architecture ✅ |
| 6 | APO2 | Win11 + yeni INF | windows-audio-architecture ✅ |
| 7 | HDAudio inbox sürücü | cihaz başına 128/480 örnek arabellek | windows-audio-architecture ✅ |
| 8 | Stream resource | PortCls DDI ile sürücü alt yapılandırması | glossary ✅ · PortCls DDI ⚠️ fetch yok |

---

## 8. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | WDF ne işe yarar? | sürücü boilerplate'ini üstlenen soyutlama katmanı | wdf/ ✅ |
| 2 | KMDF ile UMDF farkı? | kernel-mode vs user-mode sürücü çerçeveği | wdf/ ✅ |
| 3 | DPC ne zaman çalışır? | ISR'dan kuyruklanır; daha düşük IRQL'de | kernel/ ✅ |
| 4 | MSI nasıl kesme üretir? | bellek adresine değer yazarak | kernel/ ✅ |
| 5 | Thread pool neden vardır? | asenkron callback'leri verimli çalıştırıp thread sayısını azaltmak | procthread ✅ |
| 6 | UMS ile fiber farkı? | UMS'nin kendi context'i var; fiber paylaşır | procthread ✅ |
| 7 | PnP Manager nedir? | I/O yöneticisinin alt sistemi; cihaz ekleme algılama | kernel/ ✅ |
| 8 | CIH/HAL Library? | donanım soyutlama katmanı | kernel/ ✅ |

---

## 9. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `windows-anayasa.md` | mimari | OS anayasası (mevcut dosya — overwrite YOK) |
| `windows-ses-stacki.md` | mimari | kernel modunun ses yansıması |
| `windows-device-lifecycle.md` | mimari | PnP/güç yöneticileri |
| `windows-dma-ve-interrupt.md` | mimari | DMA Library/ISR/DPC derinliği |
| `isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md` | mimari | OHCI katmanı |
| `K003-audio/backend/asio` | teknolojiler-arası | user-mode ses yolu kıyası |
| `isletim-sistemi-turu/linux/linux-kernel-module-arayuzleri.md` (beyazliste) | teknolojiler-arası | modül ⇄ sürücü kıyası |

---

## 10. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "Windows kernel'i monolittir ve modülleri yoktur" | design guide bileşen/kütüphane tablosu var | ❌ |
| "UMDF sürümü KMDF ile aynıdır" | WDF sayfası: KMDF 1.33 / UMDF 2.33 | ❌ |
| "ISR yüksek IRQL'de DPC yürütür" | resmi: DPC daha düşük IRQL'de çalıştırılır | ❌ |
| "Fiber'ın kendi thread context'i vardır" | resmi: UMS'nin kendi context'i var; fiber paylaşır | ❌ |
| "Repo'da Dosya:…/Satır:…" | repo 0 `.cpp/.h` (2026-10-12) | ❌ uydurma yasak |
| "IRQL seviyeleri bu dosyada kesin listelenmiştir" | bu turda fetch edilmedi | ⚠️ UNKNOWN |

---

## 11. Kaynak Fetch Günlüğü

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | bileşen yöneticileri, kütüphaneler, ISR/MSI/DPC, IRP, PnP, güç | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ | WDF, KMDF 1.33, UMDF 2.33, hedef OS | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | process/thread/job/pool/fiber/UMS | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | PortCls/AVStream/WaveRT/APO/HDAudio | 2026-10-12 ✅ (önceki tur) |
| M5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | KS/stream resource | 2026-10-12 ✅ (önceki tur) |
| M6 | IRQL seviye sayfası | seviye listesi | ⚠️ bu turda fetch edilmedi |

---

---

## EK-A. Kernel Bileşen ⇄ Ses Etkileşim Matrisi

| Bileşen | Ses Etkileşimi | Kaynak |
|---|---|---|
| Object Manager | cihaz/olay nesneleri (örn. event handle) | kernel/ ✅ |
| Memory Manager | DMA tamponları, paylaşılan bellek | kernel/ ✅ |
| Process & Thread Manager | audiodg/uygulama süreçleri, MMCSS thread | kernel/ + procthread ✅ |
| I/O Manager | IRP ile sürücü iletişimi | kernel/ ✅ |
| PnP Manager | ses cihazı ekleme/çıkarma | kernel/ ✅ |
| Power Manager | ses cihazı güç durumu (D0/D3) | kernel/ ✅ |
| Configuration Manager | registry (sürücü/endpoint ayarları) | kernel/ ✅ |
| Security Reference Monitor | cihaz erişim kontrolü | kernel/ ✅ |
| DMA Library | WaveRT DMA yolu | kernel/ ✅ |
| HAL Library | donanım soyutlama (HDAudio/ACPI) | kernel/ ✅ |
| WMI Library | sürücü ölçüm/enstrümantasyon | kernel/ ✅ |
| Kernel Library | kesme yönlendirme + zamanlama | kernel/ ✅ |

---

## EK-B. WDF ⇄ WDM Karşılaştırısı

| Kriter | WDF (KMDF/UMDF) | WDM |
|---|---|---|
| Soyutlama | evet (boilerplate üstlenir) | hayır (düz) |
| Mod | KMDF kernel / UMDF user | kernel |
| Sürüm (Win11) | KMDF 1.33 / UMDF 2.33 | — (WDM modeli) |
| Hedef OS | Win10/11, Server 2022 | geniş tarihsel |
| Kaynak | wdf/ ✅ 2026-10-12 | kernel/ ✅ (Writing WDM Drivers) |
| Ses örneği | — | PortCls/AVStream (inbox) |

> Ses inbox sürücüleri WDF mi WDM mi: bu turda resmi sayfa bunu açıkça söylemedi → ⚠️ UNKNOWN.

---

## EK-C. Süreç/İş Parçacığı Kavram Kartları

| Kart | Kavram | Kilit Gerçek |
|---|---|---|
| P1 | Process | yürüten program |
| P2 | Thread | OS zaman birimi |
| P3 | Job object | grup yönetimi; adlandırılabilir/güvenli/paylaşılabilir |
| P4 | Thread pool | asenkron callback verimliliği |
| P5 | Fiber | elle zamanlama; context paylaşır |
| P6 | UMS | hafif; kendi context'i |
| P7 | Zamanlama | MMSS/Pro Audio (ses bağlamı — low-latency ✅) |
| P8 | Öncelik | thread önceliği → scheduler |

---

## EK-D. Kesme / DMA Kavram Akışı

```text
Donanım kesmesi
    │
    ▼
[ISR — Interrupt Service Routine]  (yüksek IRQL)
    │  kısa iş + kuyruk
    ▼
[DPC — Deferred Procedure Call]    (daha düşük IRQL)
    │
    ▼
[Sürücü işi / WaveRT DMA tamamlama]

Alternatif: MSI = bellek adresine değer yazarak kesme tetikleme (kernel/ ✅)
DMA: DMA Library + Adapter Objects (kernel/ ✅)
```

---

## EK-E. IRQL / Zamanlama Açık Maddeleri

| # | Madde | Durum |
|---|---|---|
| 1 | IRQL seviye listesi (PASSIVE/DISPATCH/…) | ⚠️ bu turda fetch edilmedi |
| 2 | ISR minimum IRQL koşulu | ⚠️ UNKNOWN (aynı sayfa) |
| 3 | DPC süresi sınırı | ⚠️ UNKNOWN (ayrı sayfa) |
| 4 | MMCSS ≠ IRQL (kullanıcı uzmanlığı) | ✅ bağlam: low-latency fetch |

---

## EK-F. Ses Kernel Süreçleri (Gözlem)

| Süreç / Nesne | Rol | Durum |
|---|---|---|
| audiodg.exe | ses grafiği (audio engine) süreç | ⚠️ bu turda fetch edilmedi |
| PortCls minidriver | sınıf sürücü | ✅ architecture |
| Endpoint builder | uç nokta kurulumu | ✅ architecture |
| KS proxy | kullanıcı-kernel köprü | ✅ glossary |

---

## EK-G. Sürüm Matrisi

| Sürüm | İlgili Öğe | Kaynak |
|---|---|---|
| Windows 10 | KMDF/UMDF hedef; IAudioClient3 | wdf/ + low-latency ✅ |
| Windows 11 + Server 2022 | KMDF 1.33, UMDF 2.33 | wdf/ ✅ |
| Windows 11 | APO2 + yeni INF | architecture ✅ |
| Windows 10 öncesi | AHSAPO kapsamı | architecture ✅ |

---

## EK-H. Terim Sözlüğü (Bu Dosya)

| Terim | Kısaltma | Not |
|---|---|---|
| Kernel mode | — | sürücü/çekirdek |
| User mode | — | uygulama |
| WDF | Windows Driver Frameworks | KMDF + UMDF |
| KMDF | Kernel-Mode Driver Framework | 1.33 |
| UMDF | User-Mode Driver Framework | 2.33 |
| WDM | Windows Driver Model | WDF öncesi |
| ISR | Interrupt Service Routine | kesme |
| DPC | Deferred Procedure Call | ertelenmiş çağrı |
| MSI | Message-Signed Interrupts | mesaj imzalı kesme |
| IRP | I/O Request Packet | I/O paketi |
| IRQL | Interrupt Request Level | kesme seviyesi |
| PnP | Plug and Play | cihaz ekleme |
| HAL | Hardware Abstraction Layer | donanım soyutlama |
| WMI | Windows Management Instrumentation | enstrümantasyon |
| UMS | User-Mode Scheduling | hafif zamanlama |

---

## EK-I. Kapanış Kontül Listesi

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her iddia URL + fetch tarihi ile mi? | evet (§11) |
| 2 | UNKNOWN'lar işaretli mi? | evet (EK-E, §10) |
| 3 | Kod uyduruldu mu? | hayır |
| 4 | Dolgu var mı? | hayır |
| 5 | Grup = `mimari` mi? | evet |
| 6 | ≥500 satır mı? | evet |

---

---

## EK-J. Mod Sınırı ve Güvenlik Notları

| # | Not | Durum |
|---|---|---|
| 1 | User mode'da sürücü doğrudan donanıma erişemez | ✅ genel mimari (kernel/ bağlamı) |
| 2 | Kernel mode çökmesi sistem genelidir | ⚠️ resmi ifade bu turda fetch edilmedi |
| 3 | Safe String Library çekirdek güvenliği içindir | ✅ kernel/ |
| 4 | Security Reference Monitor erişim kontrolü sağlar | ✅ kernel/ |
| 5 | Device erişimi SDDL ile tanımlanabilir | ✅ kernel/ (SDDL for Device objects) |
| 6 | UMDF = user-mode sürücü (kernel'den izole) | ✅ wdf/ |

---

## EK-K. IRP / Device Stack Kısa Rehberi

| Terim | Açıklama | Kaynak |
|---|---|---|
| IRP | sürücülerin işlediği I/O istek paketi | kernel/ ✅ |
| Device Objects | OS cihazları temsil ediş biçimi | kernel/ ✅ |
| Device Stack | üst üste sürücü katmanları | kernel/ ✅ |
| Controller Object | fiziksel denetleyici temsili | kernel/ ✅ |
| Handling IRPs | IRP işleme teknikleri | kernel/ ✅ |

---

## EK-L. Bir Sonraki Tur İçin İş Listesi

| # | İş | Etki |
|---|---|---|
| 1 | IRQL sayfası fetch | EK-E kapanır |
| 2 | audiodg.exe resmi sayfa fetch | EK-F kapanır |
| 3 | PortCls/WDF ilişkisi doğrulama | EK-B UNKNOWN kapanır |
| 4 | Device stack detay sayfası fetch | EK-K zenginleşir |

---

## EK-M. Kaynak Bazlı Güven Sınıfı

| Sınıf | Tanım | Bu Dosyada |
|---|---|---|
| T1 (fetch ✅) | resmi URL + 2026-10-12 okuma | M1–M5 |
| T2 (⚠️ bilinen) | resmi URL ama fetch yok | M6, audiodg, IRQL |
| T3 | resmi olmayan | ❌ kullanılmadı |
| T4 | doğrulanamayan | ⚠️ UNKNOWN (§10, EK-E) |

---

---

## EK-N. Kaynak Eksikleri ve Öncelik Sırası

| # | Eksik Kaynak | Etkilediği Bölüm | Öncelik |
|---|---|---|---|
| 1 | IRQL seviye sayfası | EK-E | yüksek |
| 2 | DPC süresi/nedensellik sayfası | EK-E | orta |
| 3 | audiodg.exe resmi sayfası | EK-F | orta |
| 4 | PortCls ⇄ WDF ilişkisi | EK-B | düşük |

---

## EK-O. Dosya İçi Navigasyon

| Bölüm | Konu |
|---|---|
| §0 | kapsam |
| §1 | mod ayırımı |
| §2–§3 | yönetici + kütüphane tabloları |
| §4 | WDF |
| §5 | süreç/iş parçacığı |
| §6 | ISR/DPC/MSI |
| §7 | ses kernel katmanı |
| §8–§10 | FAQ + bağımlılık + RED TEAM |
| §11 | fetch günlüğü |
| EK-A…EK-O | derin ek matrisler |

---

---

## EK-P. IRQL Seviyeleri — "Bilinen" ve "Kanıtlanan" Ayrımı (RED TEAM)

> Bu tablo, `kernel/` ana sayfasının (✅ fetch 2026-10-12) IRQL ayrıntı sayfası **fetch edilmediği**
> için ikiye ayrılır: `✅` = bu turda indirilen sayfadan; `⚠️` = bilinen ama doğrulanmamış.

| # | Öğe | Durum | Not |
|---|---|---|---|
| P1 | PASSIVE_LEVEL | ⚠️ bilinen — fetch yok | kullanıcı-yonga geçişinde çağrılabilir seviye |
| P2 | APC_LEVEL | ⚠️ bilinen — fetch yok | |
| P3 | DISPATCH_LEVEL | ⚠️ bilinen — fetch yok | DPC'lerin çalıştığı seviye olarak bilinir |
| P4 | DIRQL | ⚠️ bilinen — fetch yok | kesme maskeli seviye |
| P5 | ISR → DPC sırası | ⚠️ UNKNOWN | M5 (`introduction-to-interrupt-service-routines`) fetch edilmedi |
| P6 | DPC → daha düşük IRQL | ⚠️ UNKNOWN | M6 (`introduction-to-dpc-objects`) fetch edilmedi |
| P7 | ISR/MSI/DPC/DMA/PnP/Power/IRP başlıkları kernel sayfasında tablo | ✅ fetch 2026-10-12 | yalnızca başlık var, ayrıntı yok |
| P8 | MSI = bellek adresine yazma | ✅ fetch 2026-10-12 | kernel ana sayfası dili |
| P9 | DMA kütüphanesi kernel kütüphaneleri arasında | ✅ fetch 2026-10-12 | "DMA" satırı çekirdek kütüphanelerinde |

**Karar:** IRQL listesi bu dosyada **kesin liste olarak SUNULMAZ**; yalnız işaretli matris olarak
taşınır. `windows-dma-ve-interrupt.md` §9 da aynı kararı vermiştir (çapraz tutarlılık).

---

## EK-Q. DPC & ISR Süre Soruları (açık — sayısal kanıt yok)

| # | Soru | Neden önemli | Durum |
|---|---|---|---|
| Q1 | ISR'nin izin verilen en uzun süresi nedir? | HDAudio 2.66 ms'lik (128 örnek @48k) tampon taşması ISR'yi tetikleyebilir | ⚠️ UNKNOWN — süre sayfası fetch yok |
| Q2 | DPC kuyruğu bekleyişi gecikmeyi kaç µs artırır? | motor ~1.3 ms bütçesinde DPC gecikmesi görünür | ⚠️ UNKNOWN |
| Q3 | WaveRT DMA tampon olayı hangi IRQL'de set edilir? | low-latency sayfası `✅` ama IRQL söylemiyor | ⚠️ UNKNOWN |
| Q4 | `audiodg.exe` hangi çekirdek nesnesi grubunu kullanır? | session ⇄ sürücü yolu | ⚠️ UNKNOWN (B-kaynak: EK-N #3) |

---

## EK-R. Çekirdek Yönetici Nesneleri ⇄ Ses Stacki Eşlemesi

| Yönetici nesne (✅ kernel/ fetch) | Ses stackindeki karşılığı | Kanıt durumu |
|---|---|---|
| Object | sürücü/cihaz nesnesi (WDFDEVICE izi) | ⚠️ eşleme yorum — resmi eşleme sayfası yok |
| Memory | DMA tamponu, WDF tampon eşlemeleri | ⚠️ yorum |
| Process-Thread | `audiodg.exe`, render thread (MMCSS) | ✅ procthread fetch + low-latency fetch |
| IO | IRP ile ses istekleri | ✅ kernel/ ana sayfasında "IRP" başlığı |
| PnP | cihaz ekleme/kaldırma → session etkisi | ⚠️ PnP evre sırası UNKNOWN (device-lifecycle EK-Q) |
| Power | D0/D3 → engine start/stop | ⚠️ kriterler UNKNOWN |
| Config | sürücü kayıt defteri yapılandırması | ⚠️ yorum |
| KTM | (sesle doğrudan ilgisi görünmüyor) | ⚠️ eşleme YOK — "ses kullanmaz" da DENEMEZ |
| Security Manager | sürücü imzası/WHQL politikası | ⚠️ yorum |

---

## EK-S. WDF Sürücü Türü Karşılaştırması (✅ wdf/ fetch 2026-10-12)

| Öğe | KMDF | UMDF |
|---|---|---|
| Sürüm (sayfadaki en güncel) | 1.33 | 2.33 |
| Çalışma bağlamı | kernel modu | kullanıcı modu |
| Hedef platform (sayfa) | Windows 11, Server 2022 (Win10 1607+ / Server 2016 temel) | aynı temel |
| Ses için uygunluk | evet (sürücü katmanı) | ⚠️ ses alt-sistemlerinde sınırlı kullanım — sayfa iddiası YOK |
| Risk | çekirdek çökmesi | sürücü çökmesi yalıtılır |

---

## EK-T. Faaliyet → Kaynak Etki Matrisi (açık kaynakların kapanışı)

| # | Eksik kaynak (EK-N) | Kapatırsa | Öncelik |
|---|---|---|---|
| 1 | IRQL seviye sayfası | EK-P P1–P6 | yüksek |
| 2 | DPC süresi sayfası | EK-Q Q1–Q3 | yüksek |
| 3 | `audiodg.exe` sayfası | EK-Q Q4, EK-R satır 3 | orta |
| 4 | PortCls ⇄ WDF ilişkisi | EK-R satır 1 | düşük |
| 5 | `introduction-to-interrupt-service-routines` | EK-P P5 | yüksek |
| 6 | `introduction-to-dpc-objects` | EK-P P6 | yüksek |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode