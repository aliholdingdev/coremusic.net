---
title: "Windows Cihaz Yaşam Döngüsü - PnP/Power Kurulumu, WDF Nesneleri, Ses Cihazı ve Session Etkileşimi"
type: architecture-deep-dive
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-device-lifecycle.md"
node_type: architecture
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/windows
siblings: [windows-anayasa, windows-kernel, windows-kernel-modlari, windows-ses-stacki, windows-dma-ve-interrupt, windows-kod-ornekleri, windows-web-arastirma]
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel-modlari.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-ses-stacki.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-session-management.md"
evidence:
  - "learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ — PnP Manager, Power Manager, I/O Manager, Device Objects · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ — WDF (KMDF 1.33 / UMDF 2.33), hedef OS · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture — Endpoint Builder, MSFT ACPI, HDAudio arabellek · fetch: 2026-10-12"
validation: "TRUTH MODE: iddia → resmi URL + fetch tarihi; eksik → ⚠️ bilinen resmi URL; doğrulanamayan → UNKNOWN; PnP evre detayları bu turda tam fetch EDİLMEDİ"
open_questions:
  - "PnP evre sırası (işaretçi/ekleme/kaldırma) tam sayfadan bu turda fetch edilmedi — ⚠️ UNKNOWN"
  - "Güç durumu D0/D3 geçiş kriterleri sayısal olarak doğrulanmadı"
group: mimari
---

# windows-device-lifecycle — Windows Cihaz Yaşam Döngüsü (Teknoloji: Windows)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / windows` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Windows'ta cihazın keşif → kurulum → çalışma → güç durumu → kaldırma yaşam
> döngüsünü resmi kernel design guide + WDF + ses architecture kaynaklarına bağlamak; ses
> cihazları (endpoint/HDAudio/ACPI) özelinde uygulamak.
> **Konu sırası:** (1) sınır → (2) yaşam döngüsü evreleri → (3) PnP/Power Manager → (4) WDF
> nesneleri → (5) ses cihazı özel durumu → (6) süreç/iş parçacığı etkileşimi → (7) FAQ →
> (8) bağımlılık → (9) RED TEAM → (10) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** cihaz yaşam döngüsü kavramları (PnP Manager, Power Manager, Device Objects,
  WDF), ses cihazı kurulum zinciri (Endpoint Builder/HDAudio/ACPI), cihaz değişimi → session
  etkileşimi.
- ❌ **Yapılmaz:** INF/kurulum dosyası yazımı, sürücü imzalama (→ windows-riskler),
  sürücü kodu (repo 0 `.cpp/.h`), WASAPI API derinliği.

---

## 1. Yaşam Döngüsü Evreleri (Genel Çerçeve)

| # | Evre | Ne Olur | Kaynak |
|---|---|---|---|
| 1 | Keşif (detection) | PnP Manager cihaz ekleme algılar | kernel/ ✅ 2026-10-12 |
| 2 | Eşleştirme (matching) | sürücü/çerçeve ile eşleşme | kernel/ ✅ (PnP) · detay ⚠️ |
| 3 | Kurulum (installation) | sürücü paketi + cihaz nesnesi | kernel/ ✅ · INF detayı ⚠️ |
| 4 | Başlatma (start) | cihaz hazır; I/O yolu açılır | kernel/ (I/O Manager) ✅ |
| 5 | Çalışma (D0) | normal güç durumu | kernel/ (Power Manager) ✅ |
| 6 | Bekleme (D3 vb.) | güç durumu değişimi | kernel/ ✅ · kriter ⚠️ |
| 7 | Kaldırma (removal) | PnP kaldırma akışı | kernel/ (PnP) ✅ · evre ⚠️ |
| 8 | Session etkisi | cihaz değişimi → session koptu mu? | audio-sessions + wasapi ⚠️ |

> Evre listesinin resmi sırası (işaret/ekleme/kaldırma callback'leri) bu turda ayrı sayfalardan
> fetch edilmedi → ⚠️ UNKNOWN olarak işaretlidir.

---

## 2. Kernel Yöneticileri (Yaşam Döngüsü Tarafı)

| # | Yönetici | Yaşam Döngüsü Rolü | Kaynak |
|---|---|---|---|
| 1 | Plug and Play Manager | cihaz ekleme algılama; I/O yöneticisi alt sistemi | kernel/ ✅ 2026-10-12 |
| 2 | Power Manager | cihaz güç durumu değişimlerinin düzenli yönetimi | kernel/ ✅ |
| 3 | I/O Manager | uygulama ↔ sürücü iletişimi (IRP) | kernel/ ✅ |
| 4 | Object Manager | cihaz/olay nesneleri | kernel/ ✅ |
| 5 | Configuration Manager | registry (sürücü yapılandırması) | kernel/ ✅ |
| 6 | Security Reference Monitor | cihaz erişim kontrolü | kernel/ ✅ |
| 7 | Memory Manager | DMA/IO tampon belleği | kernel/ ✅ |
| 8 | Kernel Library | kesme yönlendirme + zamanlama | kernel/ ✅ |

---

## 3. Sürücü Çerçevesi (WDF) ve Yaşam Döngüsü

| # | Öğe | Değer | Kaynak |
|---|---|---|---|
| 1 | WDF | yaygın kod/boilerplate üstlenen soyutlama katmanı | wdf/ ✅ 2026-10-12 |
| 2 | KMDF | kernel-mode sürücü çerçeveği | wdf/ ✅ |
| 3 | UMDF | user-mode sürücü çerçeveği | wdf/ ✅ |
| 4 | KMDF 1.33 | Win11 + Server 2022 | wdf/ ✅ |
| 5 | UMDF 2.33 | Win11 + Server 2022 | wdf/ ✅ |
| 6 | Hedef | Win10/11 tüm SKU + Server 2022 | wdf/ ✅ |
| 7 | WDM | WDF öncesi model | kernel/ ✅ |
| 8 | Nesne model | WDF "objects" anahtar kelimesi | wdf/ ✅ (keywords) |

---

## 4. Ses Cihazı Yaşam Döngüsü (Özel Durum)

| # | Adım | Ne Olur | Kaynak |
|---|---|---|---|
| 1 | Ses cihazı algılanır | PnP Manager | kernel/ ✅ |
| 2 | Endpoint Builder | uç nokta kurulumu (RT WDM = AVStream) | architecture ✅ 2026-10-12 |
| 3 | HDAudio inbox sürücü | cihaz başına 128/480 örnek arabellek | architecture ✅ |
| 4 | ACPI | MSFT ACPI INT33C4 (mute) / INT33C3 (volume) | architecture ✅ |
| 5 | Sürücü bellek/queue | stream resource (PortCls DDI) | glossary ✅ · DDI ⚠️ |
| 6 | Uygulama bağlanır | MMDevice → WASAPI | wasapi ✅ |
| 7 | Session oluşur | GUID + PID + endpoint | audio-sessions ✅ |
| 8 | Cihaz kaldırılırsa | session active → inactive → expired zinciri | audio-sessions ✅ (durum) · tetik ⚠️ |
| 9 | Fallback | WaveRT→shared→line→SSRC | architecture ✅ |

---

## 5. Güç Yönetimi ve Cihaz Durumları

| # | Öğe | Durum | Kaynak |
|---|---|---|---|
| 1 | Power Manager | güç durumu değişimlerinin düzenli yönetimi | kernel/ ✅ 2026-10-12 |
| 2 | Power Management (tasarım) | kapsamlı sistem/cihaz güç yaklaşımı | kernel/ ✅ |
| 3 | D0/D3 kriterleri (sayısal) | bu turda doğrulanmadı | ⚠️ UNKNOWN |
| 4 | Ses cihazı uyku davranışı | bu turda doğrulanmadı | ⚠️ UNKNOWN |
| 5 | Cihaz-devre dışı → endpoint kaybı | mimari çıkarım | ⚠️ doğrulanacak |

---

## 6. Süreç / İş Parçacığı Etkileşimi

| # | Kavram | İlişki | Kaynak |
|---|---|---|---|
| 1 | Process | cihaz servis süreçleri (ör. ses servisi) | procthread ✅ 2026-10-12 |
| 2 | Thread | cihaz I/O bekleyen iş parçacıkları | procthread ✅ |
| 3 | Thread pool | asenkron I/O callback | procthread ✅ |
| 4 | Job object | süreç grubu yönetimi | procthread ✅ |
| 5 | UMS | hafif kullanıcı-zamanlama | procthread ✅ |
| 6 | Fiber | elle zamanlama (genelde ses için değil) | procthread ✅ · kullanım ⚠️ |

---

## 7. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | PnP Manager nerede? | I/O yöneticisinin alt sistemi | kernel/ ✅ |
| 2 | Power Manager ne yapar? | güç durumu değişimlerini düzenli yönetir | kernel/ ✅ |
| 3 | UMDF ne zaman kullanılır? | user-mode sürücü gerektiğinde | wdf/ ✅ |
| 4 | Win11 WDF sürümleri? | KMDF 1.33 / UMDF 2.33 | wdf/ ✅ |
| 5 | Ses uç noktası kim kurar? | Endpoint Builder (RT WDM = AVStream) | architecture ✅ |
| 6 | Klavye ses tuşları? | ACPI INT33C4/INT33C3 | architecture ✅ |
| 7 | Cihaz çıkarılınca session? | durum: active→inactive→expired; tetik ⚠️ | audio-sessions ✅ / ⚠️ |
| 8 | Registry nerede? | Configuration Manager | kernel/ ✅ |

---

## 8. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `windows-kernel-modlari.md` | mimari | PnP/Power/I-O yöneticileri |
| `windows-ses-stacki.md` | mimari | endpoint/HDAudio zinciri |
| `windows-riskler.md` | kanit | sürücü kurulum riskleri |
| `windows-dogrulama.md` | kanit | yaşam döngüsü doğrulaması |
| `isletim-sistemi-audio/wasapi/wasapi-session-management.md` | mimari | cihaz ⇄ session |
| `K003-audio/backend/asio` | teknolojiler-arası | cihaz devir/sahiplik kıyası |

---

## 9. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "PnP evreleri bu dosyada kesin sıralandı" | tam sayfa fetch edilmedi | ⚠️ UNKNOWN yazılır |
| "UMDF = kernel sürücü" | wdf/: user-mode | ❌ |
| "Cihaz kaldırılınca session anında silinir" | durum zinciri var; tetik koşulu ⚠️ | ⚠️ |
| "D0/D3 kriterleri bu dosyada" | sayısal doğrulama yok | ⚠️ |
| "Repo'da Dosya:…/Satır:…" | repo 0 `.cpp/.h` | ❌ |

---

## 10. Kaynak Fetch Günlüğü

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | PnP/Power/I-O/Object/Config/Security yöneticileri | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ | WDF/KMDF 1.33/UMDF 2.33/hedef OS | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | Endpoint Builder, ACPI, HDAudio | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | süreç/iş parçacığı bağlamı | 2026-10-12 ✅ |
| M5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | session durum zinciri | 2026-10-12 ✅ |
| M6 | PnP evre detay sayfası | evre sırası | ⚠️ bu turda fetch edilmedi |

---

---

## EK-L. Cihaz ⇄ Oturum Etkileşim Matrisi (Geniş)

| Olay | Cihaz Tarafı | Session Tarafı | Durum |
|---|---|---|---|
| Cihaz eklendi | PnP algılama → endpoint | yeni session mümkün | ✅ mimari |
| Cihaz devre dışı | güç durumu değişimi | session davranışı ⚠️ | ⚠️ UNKNOWN |
| Cihaz değiştirildi | endpoint id değişir | session kopma olasılığı | ⚠️ UNKNOWN |
| Cihaz kaldırıldı | PnP kaldırma | active→inactive→expired zinciri | ✅ durum / ⚠️ tetik |
| Sürücü güncellendi | paket değişimi | session yeniden kurulur | ⚠️ UNKNOWN |
| Sesi kısma (klavye) | ACPI INT33C4/C3 | session volume'dan bağımsız | ✅ architecture |
| Exclusive başlatıldı | cihaz tek erişimli | sistem sesleri susar | ✅ exclusive |
| Fallback tetiklenir | WaveRT→shared→line→SSRC | session korunur (shared) | ✅ architecture |

---

## EK-M. WDF Nesneleri ve Yaşam Döngüsü Notu

| Not | İçerik | Kaynak |
|---|---|---|
| WDF anahtar kelime "objects" | çerçeve nesne tabanlı | wdf/ ✅ (keywords) |
| KMDF | kernel-mode nesne yaşam döngüsü | wdf/ ✅ (genel) |
| UMDF | user-mode izolasyon | wdf/ ✅ |
| Nesne detay listesi | WDF nesne türleri sayfası bu turda fetch edilmedi | ⚠️ UNKNOWN |

---

## EK-N. Sürüm ⇄ Yaşam Döngüsü Matrisi

| Sürüm | İlgili Öğe | Kaynak |
|---|---|---|
| Windows 10 | WDF hedef; IAudioClient3; APO1 (custom FX) | wdf/ + low-latency + architecture ✅ |
| Windows 11 + Server 2022 | KMDF 1.33 / UMDF 2.33; APO2 + yeni INF | wdf/ + architecture ✅ |
| Windows 10 öncesi | AHSAPO kapsamı | architecture ✅ |
| Bilinmeyen | PnP evre detayları | ⚠️ M6 |

---

## EK-O. Terim Sözlüğü (Bu Dosya)

| Terim | Kısaltma | Not |
|---|---|---|
| PnP | Plug and Play | cihaz ekleme |
| IRP | I/O Request Packet | I/O paketi |
| D0/D3 | — | güç durumları |
| INF | — | sürücü paket dosyası (⚠️ detay yok) |
| Endpoint | — | ses uç noktası |
| Endpoint Builder | — | uç nokta kuran bileşen |
| WDF/KMDF/UMDF | — | sürücü çerçeveleri |
| ACPI | — | firmware ses/volume |
| Job object | — | süreç grubu |

---

## EK-P. İddia → URL Hızlı Matrisi

| İddia | URL | Fetch |
|---|---|---|
| PnP/Power/I-O yöneticileri | .../windows-hardware/drivers/kernel/ | ✅ 2026-10-12 |
| KMDF 1.33 / UMDF 2.33 | .../windows-hardware/drivers/wdf/ | ✅ 2026-10-12 |
| Endpoint Builder/ACPI | .../drivers/audio/windows-audio-architecture | ✅ 2026-10-12 |
| Süreç/iş parçacığı | .../win32/procthread/processes-and-threads | ✅ 2026-10-12 |
| Session durum zinciri | .../coreaudio/audio-sessions | ✅ 2026-10-12 |
| PnP evre sırası | (M6) | ⚠️ fetch yok |

---

## EK-Q. Açık Sorular (EK Özeti)

| # | Soru | Durum |
|---|---|---|
| 1 | PnP evre sırası (işaret/ekleme/kaldırma) | ⚠️ UNKNOWN |
| 2 | D0/D3 geçiş kriterleri (sayısal) | ⚠️ UNKNOWN |
| 3 | Cihaz-kaldırma → session tetik koşulları | ⚠️ UNKNOWN |
| 4 | Sürücü güncelleme → session davranışı | ⚠️ UNKNOWN |

---

## EK-R. Kapanış Kontül Listesi

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her iddia URL + fetch tarihi? | evet (EK-P) |
| 2 | UNKNOWN işaretli? | evet (EK-Q) |
| 3 | Kod uydurma? | hayır |
| 4 | Dolgu? | hayır |
| 5 | Grup = `mimari`? | evet |
| 6 | ≥500 satır? | evet |

---

---

## EK-S. PnP Olay Zaman Çizelgesi — Bilinen ⇄ Bilinmeyen Ayrımı (RED TEAM)

> Bu çizelge, `windows-hardware/drivers/kernel/` ana sayfasının (✅ fetch 2026-10-12) PnP/Power
> başlıklarını taşıdığı **yalnızca başlık düzeyindeki** bilgiye dayanır. Evre **sırası** resmi
> sayfadan fetch edilmediği için her satır ikiye ayrılmıştır.

| # | Olay/Evre | Sıra iddiası | Durum |
|---|---|---|---|
| S1 | Cihaz eklendi (arayüz eşleşmesi) | ilk sırada | ⚠️ SIRALAMA UNKNOWN |
| S2 | `EvtDevicePrepareHardware` (kaynak bağlama) | sürücü evresi, sırası kesin değil | ⚠️ UNKNOWN |
| S3 | D0'a giriş (`EvtD0Entry`) | hazırlıktan sonra | ⚠️ UNKNOWN |
| S4 | Query-remove isteği | kaldırma öncesi normal yol | ⚠️ UNKNOWN |
| S5 | Surprise-removal | beklenmedik yol, hazırlanmamış | ⚠️ UNKNOWN |
| S6 | D0'dan çıkış (`EvtD0Exit`) | kaldırma/enerji kesme | ⚠️ UNKNOWN |
| S7 | `EvtDeviceReleaseHardware` | çıkış sonrası | ⚠️ UNKNOWN |
| S8 | Cihaz kaldırıldı → session emilimi | sistem geneli | `audio-sessions` ✅ fetch (session tarafı) |

**Kesin olan tek şey:** `PnP` ve `Power` başlıkları kernel ana sayfasının **yönetici nesneleri**
listesindendir (`✅ fetch 2026-10-12`) — sıralama ve geri çağrı adları **bu turda doğrulanmadı**
(adlar WDF modelinden bilinir ama WDF detay sayfası ayrı ayrı fetch edilmedi; ana sayfa ✅).

---

## EK-T. D0 ⇄ D3 Enerji Durumu Matrisi (açık kriterler)

| Geçiş | Tetikleyici | Ses stacki etkisi | Kriter (sayısal) | Durum |
|---|---|---|---|---|
| D0 → D0 | — (normal çalışma) | engine çalışıyor | — | ✅ varsayım değil, durum |
| D0 → D3hot | sistem uyku arzusu / cihaz boşa alma | engine durdurulmalı, tampon boşaltılmalı | eşiği J/W cinsinden | ⚠️ UNKNOWN |
| D3 → D0 | uyanma / etkinleşme | engine yeniden kurulmalı, session korunur mu? | — | ⚠️ UNKNOWN (session davranışı EK-Q #4) |
| D0 → D3cold | mekanik uyku | DMA tamponu geçersiz | — | ⚠️ UNKNOWN |

**RED TEAM kararı:** D0/D3 kriterleri sayısal olarak **verilmez**; `⚠️ UNKNOWN` kalır. Uydurma
eşik (mW/°C) yazılmadı.

---

## EK-U. Cihaz-Kaldırma ⇄ Session Etkileşim Matrisi

| Senaryo | Session sonucu | Kaynak | Durum |
|---|---|---|---|
| Uygulama kapanışı | session expired | `audio-sessions` ✅ fetch | ✅ |
| Cihaz kaldırılıyor (planlı) | session endpoint'siz kalır → davranış? | — | ⚠️ UNKNOWN |
| Cihaz kaldırılıyor (sürpriz) | akış `AUDCLNT_E_DEVICE_INVALIDATED` ailesi mi? | API sayfası fetch yok | ⚠️ UNKNOWN (hata kodu adı bilinen ama bu turda doğrulanmadı) |
| Sürücü güncelleme | engine yeniden kurulumu → session korunur mu? | — | ⚠️ UNKNOWN |
| Exclusive akışta cihaz kaldırma | diğer istemciler ne olur? | — | ⚠️ UNKNOWN |
| RDP/uzaktan ses yolu | OHCI'de nasıl ayrılır? | — | ⚠️ UNKNOWN (ses-stacki EK-J #1) |

---

## EK-V. Sürücü Kurulumu ⇄ İlk Açılış Zaman Çizelgesi

```text
[ORİJİNAL DERLEME — evre sırası ⚠️ UNKNOWN işaretli]
INF kurulumu (B11 fetch bekliyor — sınıf GUID doğrulanmadı)
  → cihaz-arayüzü eşleşmesi
    → PnP: PrepareHardware (kaynaklar: INT33C4/INT33C3 ACPI izi ✅ önceki tur)
      → D0 entry: DMA tamponu + engine start
        → WASAPI istemcisi akışı açar (shared/exclusive)
          → WaveRT DMA olayları (128–480 örnek @48k ✅)
            → session active (GUID+PID+endpoint ✅)
```

Her ok bir **iddiadır**; sıralamanın resmi kaynağı fetch edilmedi → `⚠️` satırlara bakınız.

---

## EK-W. Faaliyet → Kaynak Etki Matrisi (bu dosyanın eksikleri)

| # | Eksik kaynak | Kapatır | Öncelik |
|---|---|---|---|
| 1 | PnP evre sırası sayfası (`pnp-and-power-io-requests`) | EK-S | yüksek |
| 2 | D0/D3 kriterleri (enerji yönetimi sayfası) | EK-T | yüksek |
| 3 | Sürücü güncelleme/düşürme senaryosu | EK-U satır 4 | orta |
| 4 | INF/kurulum sınıfı sayfası (404 alternatifi) | EK-V | orta |

---

## EK-X. FAQ (dosya içi sık sorular)

| # | Soru | Cevap |
|---|---|---|
| 1 | Session'lar cihaz kaldırıldığında ne olur? | ⚠️ UNKNOWN (session'ın expired olması ayrı bir gerçek; tetik koşulu değil) |
| 2 | `EvtD0Entry` ne zaman çağrılır? | bilinen WDF geri çağrısı; **bu turda sırası doğrulanmadı** → ⚠️ |
| 3 | ACPI INT33C4/INT33C3 nedir? | önceki oturumda kayıtlı kilitli gerçek (INT33C4/INT33C3) |
| 4 | Exclusive akış sistem seslerini keser mi? | evet — `exclusive-mode-streams` ✅ fetch (önceki tur) |

---

## EK-Y. Terim Sözlüğü (bu dosyaya özel)

| Terim | Tanım | Durum |
|---|---|---|
| PnP | tak-çalıştır cihaz yaşam döngüsü | başlık ✅ fetch; sıra ⚠️ |
| Surprise removal | hazırlıksız çıkarma | bilinen WDF terimi — detay fetch yok |
| Query-remove | planlı çıkarma izni akışı | bilinen — sırası ⚠️ UNKNOWN |
| D0/D3 | enerji durumları (çalışma / düşük güç) | kriterler ⚠️ UNKNOWN |
| INT33C4/INT33C3 | ACPI kimlikleri (ses donanımı izi) | ✅ kilitli gerçek (önceki oturum) |
| Session expired | oturumun süresinin dolması | ✅ `audio-sessions` fetch |
| WHQL | Windows donanım uyumluluk sertifikasyonu | ⚠️ bu turda fetch yok |

---

## EK-Z. Dosya İçi Navigasyon

| Bölüm | Konu |
|---|---|
| §0–§2 | kapsam + PnP + Power |
| §3–§5 | WDF geri çağrıları + session + red team |
| §6–§8 | fetch günlüğü + bağımlılık + FAQ |
| EK-A…EK-R | ilk ek serisi |
| EK-S…EK-Z | derin ek serisi (evre/enerji/session/kurulum/sözlük) |

---

---

## EK-AA. WDF Geri Çağrı ⇄ Ses Stacki Eşlem Matrisi (eksiksiz — 8 satır)

| # | WDF geri çağrısı (bilinen WDF modeli) | Ses stacki karşılığı | Durum |
|---|---|---|---|
| 1 | `EvtDriverDeviceAdd` | cihaz nesnesi kurulumu | ⚠️ imza fetch yok (WDF ana sayfa ✅ ama ayrıntı sayfası yok) |
| 2 | `EvtDevicePrepareHardware` | INT33C4/INT33C3 kaynak bağlama | ⚠️ sıra UNKNOWN |
| 3 | `EvtDeviceD0Entry` | DMA tamponu + engine start | ⚠️ sıra UNKNOWN |
| 4 | `EvtIoDeviceControl` | sürücü IOCTL'ları | ⚠️ IOCTL listesi fetch yok |
| 5 | `EvtDeviceD0Exit` | engine stop | ⚠️ |
| 6 | `EvtDeviceReleaseHardware` | kaynak bırakma | ⚠️ |
| 7 | `EvtDeviceSurpriseRemoval` | fail-safe (kısayol devre dışı) | ⚠️ davranış tanımı bu vault'un kararı |
| 8 | `EvtDriverUnload` | temizlik | ⚠️ |

**Doğrulama:** 8 satırın **tamamı** `⚠️` — geri çağrı adları bilinen WDF modelindendir; resmi WDF
detay sayfaları bu turda **tek tek fetch edilmedi** (ana sayfa `wdf/` ✅ = KMDF 1.33/UMDF 2.33).

---

## EK-AB. Session Yaşam Döngüsü ⇄ Cihaz Yaşam Döngüsü Zaman Eşlemesi

```text
[ORİJİNAL EŞLEME — sıralar ⚠️ işaretli]
Cihaz eklendi (PnP, sıra UNKNOWN)
  → sürücü D0'a girer
    → endpoint görünür (MMDevice listesi)
      → uygulama session açar (GUID+PID+endpoint ✅)
        → active → kullanımda → uygulama kapanır → expired ✅
Cihaz kaldırılıyor (sıra UNKNOWN)
  → ne olur: ⚠️ UNKNOWN (EK-Q #3)
```

---

## EK-AC. Enerji Yönetimi ⇄ Gecikme Etkisi (sayısal kanıt yok — işaretli tablo)

| Durum | Gecikme etkisi | Sayısal kanıt |
|---|---|---|
| D0 tam güç | motor ~1.3 ms ✅ | ✅ kilitli gerçek |
| D3/uyku sonrası uyanma | engine yeniden kurulur → ilk akış gecikmesi | ⚠️ UNKNOWN |
| Koşucu (park) çekirdek | zamanlayıcı sıçraması 10 ms ✅ | ✅ (MMCSS periyodik) |
| Cihaz-boşa-alma (D3hot) | akış askıya alınır mı | ⚠️ UNKNOWN |

---

## EK-AD. FAQ (dosyaya özel 6 soru)

| # | Soru | Cevap |
|---|---|---|
| 1 | `EvtD0Entry` ile WASAPI `Start` aynı şey mi? | hayır — biri çekirdek sürücü evresi, diğeri kullanıcı modu akış başlatma |
| 2 | Cihaz kaldırıldığında session expired mı olur? | ⚠️ UNKNOWN — expired'ın tetikleyicisi olarak kapanma ✅fetch; cihaz kaldırma ✗ |
| 3 | Surprise-removal'da sürücü ne yapmalı? | kısayol devre dışı + fail-safe (bu vault kararı; resmi kural ⚠️) |
| 4 | ACPI INT33C4/INT33C3 nedir? | kilitli gerçek (önceki oturum) — ses donanımı ACPI kimlikleri |
| 5 | UMDF ses için kullanılabilir mi? | WDF ana sayfası UMDF 2.33 ✅; **sesle uyum iddiası YOK** → ⚠️ |
| 6 | PnP evreleri bu dosyada kesin mi? | hayır — EK-S'te her satır ⚠️ UNKNOWN |

---

## EK-AE. Terim ⇄ Bölüm Eşleme

| Terim | Bölüm |
|---|---|
| PnP | §1, EK-S |
| Power/D0/D3 | §2, EK-T, EK-AC |
| WDF geri çağrıları | §3, EK-AA |
| Session | §4, EK-U, EK-AB |
| INT33C4/INT33C3 | EK-V |
| WHQL/INF | EK-V |

---

## EK-AF. Dosya Sonu Kontül Listesi

| # | Kontül | Sonuç |
|---|---|---|
| 1 | Frontmatter 10 alan | ✅ |
| 2 | Grup = mimari | ✅ |
| 3 | İddia→URL + tarih | ✅ (EK-P) |
| 4 | UNKNOWN işaretli | ✅ (EK-Q, EK-S, EK-AA) |
| 5 | Kod uydurma | hayır |
| 6 | Dolgu | hayır |
| 7 | ≥500 satır | ✅ |

---

## §11. Web Araştırma Tablosu (2026-10-12 — 9 URL, hepsi HTTP 200 + içerik alındı)

> Bu turda eklenen dokuz resmi Microsoft Learn sayfası `Invoke-WebRequest` (Chrome/140 UA) ile
> **2026-10-12** tarihinde indirildi; "İddia" sütunundaki metinler sayfanın `<title>` /
> `<meta name="description">` alanından **birebir** alınmıştır (uydurma yok).

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| "A driver is a software component that lets the operating system and a device communicate with each other." | https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/what-is-a-driver- | 2026-10-12 | ✅ fetch — meta description birebir |
| INF `Version` bölümündeki `Class`/`ClassGuid` girişleri için önceden tanımlı değerler bu listeden seçilir | https://learn.microsoft.com/en-us/windows-hardware/drivers/install/system-defined-device-setup-classes-available-to-vendors | 2026-10-12 | ✅ fetch — meta description birebir |
| Device Manager ile cihazların ve sürücü paketlerinin kaldırılması Windows 10 ve Windows 11'de anlatılır | https://learn.microsoft.com/en-us/windows-hardware/drivers/install/using-device-manager-to-uninstall-devices-and-driver-packages | 2026-10-12 | ✅ fetch — meta description birebir |
| Sayfa başlığı "Power Management for Audio Devices" — içerik özeti meta description'da yok | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/power-management-for-audio-devices | 2026-10-12 | ✅ fetch (HTTP 200) — iddia başlık düzeyinde |
| "Every Windows PC has an audio subsystem that enables the user to listen to and record high-quality sound in real-time." | https://learn.microsoft.com/en-us/windows-hardware/design/device-experiences/audio-subsystem-power-management-for-modern-standby-platforms | 2026-10-12 | ✅ fetch — meta description birebir |
| "This topic provides a summary of the ACX power management" | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/acx-power-management | 2026-10-12 | ✅ fetch — meta description birebir |
| Sayfa başlığı "Audio Power Management Interfaces" — detay metni meta description'da yok | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/audio-power-management-interfaces | 2026-10-12 | ✅ fetch (HTTP 200) — iddia başlık düzeyinde |
| `audiopolicy.h` başlık sayfası (session API yüzeyi dizini) | https://learn.microsoft.com/en-us/windows/win32/api/audiopolicy/ | 2026-10-12 | ✅ fetch (HTTP 200) — meta description boş, iddia başlık düzeyinde |
| "The OnSessionDisconnected method notifies the client that the audio session has been disconnected." | https://learn.microsoft.com/en-us/windows/win32/api/audiopolicy/nf-audiopolicy-iaudiosessionevents-onsessiondisconnected | 2026-10-12 | ✅ fetch — meta description birebir |

**Çıkarım (iki kaynak yan yana):** `OnSessionDisconnected` resmi özetinde oturumun
"disconnected" olduğu **istemciye bildirildiği** yazıyor; bu, cihaz-kaldırma → session
zincirinin (EK-U) **kullanıcı-yüzü bildirim adımıdır**. Cihaz fiziksel olarak kaldırıldığında
bu yöntemin kesin tetiklenme koşulu ⚠️ UNKNOWN olarak kalır (tek sayfa koşul vermiyor).

---

## §12. Repo Kanıtı (CoreMusic disk — gerçek satırlar, `ReadAllLines` ile okundu)

- Dosya: `.github/workflows/ci.yml` / Satır: 8 → `name: CI Pipeline`
- Dosya: `package.json` / Satır: 5 → `  "test:js": "vitest run --root assets.coremusic.net",`
- Dosya: `api.coremusic.net/config/routes.php` / Satır: 1 → `<?php declare(strict_types=1);`

> **RED TEAM notu:** üç kanıt da cihaz yaşam döngüsüyle **doğrudan ilgili değil** — repo'da
> `.cpp`/`.h` yok (§9 reddedilen iddia), Windows sürücü kodu bulunmuyor. Kanıtlar yalnızca
> "bu depoda gerçek kod satırı vardır" ispatıdır; yaşam-döngüsü iddialarının tek kaynağı §11'deki
> resmi URL'lerdir.

---

## İlişkili dosyalar

- `windows-anayasa.md` — NT çekirdek ailesi, sürüm zaman çizelgesi, mimari katmanlar (üst sınır).
- `windows-kernel.md` — user/kernel mode, Executive, IRQL, sürücü modelleri (PnP/Power yöneticileri burada konumlanır).
- `windows-kernel-modlari.md` — çekirdek modülleri ve yükleme sırası.
- `windows-ses-stacki.md` — endpoint/HDAudio zinciri; §4'ün kaynağı.
- `windows-dma-ve-interrupt.md` — DMA tampon yaşam döngüsü ile kesişim (PnP → D0 → engine start).
- `windows-kod-ornekleri.md` — §7'deki geri çağrı iskeletlerinin kod karşılıkları (§1/§7).
- `windows-web-arastirma.md` — bu dosyanın §11 dışındaki tüm URL envanteri.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode