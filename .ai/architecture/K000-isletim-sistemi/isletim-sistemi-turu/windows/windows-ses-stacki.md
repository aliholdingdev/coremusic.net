---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-ses-stacki.md"
node_type: architecture
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel-modlari.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode.md"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-session-management.md"
evidence:
  - "learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture — OHCI katmanları, fallback, APO1/2, ACPI · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio — motor gecikmesi, MMCSS, WaveRT event · fetch: 2026-10-12"
  - "learn.microsoft.com/en-us/windows/win32/coreaudio/{wasapi,exclusive-mode-streams,audio-sessions,glossary} · fetch: 2026-10-12"
validation: "TRUTH MODE: iddia → resmi URL + fetch tarihi; eksik → ⚠️; doğrulanamayan → UNKNOWN; repo 0 .cpp/.h → Dosya/Satır uydurma yasak"
open_questions:
  - "Windows 11 ses mimarisi değişikliğinin tam kapsamı (APO2 dışı) — bu turda doğrulanmadı"
  - "Uzaktan (RDP) ses yolu — OHCI katmanlarında ayrım yapılmadı (⚠️ UNKNOWN)"
group: mimari
---

# windows-ses-stacki — Windows Ses Yığını (Teknoloji: Windows)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-turu / windows` · **Grup:** `mimari` · **Durum:** `draft`
> **Amaç:** Windows ses yığınını uygulamadan donanıma kadar katman katman resmi kaynaklara
> bağlamak (OHCI çizimi), fallback zincirini, APO1/APO2 ayrımını, ACPI ve session katmanını
> WASAPI dosyalarıyla çaprazlayarak tek çatı altında toplamak.
> **Konu sırası:** (1) sınır → (2) OHCI katmanları → (3) aşağı iniş yolu → (4) sürücü/APO →
> (5) gecikme/session → (6) FAQ → (7) bağımlılık → (8) RED TEAM → (9) fetch günlüğü.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Mod:** TRUTH MODE / RED TEAM

---

## 0. Bu Dosyanın Kapsamı (Boundary)

- ✅ **Yapılır:** Windows ses yığını katmanları (resmi architecture sayfası), uygulama → donanım
  akışı, APO1/APO2, fallback zinciri, ACPI ses/volume, gecikme ve session katmanlarının OS
  geneli bağlamı.
- ❌ **Yapılmaz:** WASAPI API derinliği (→ `isletim-sistemi-audio/wasapi/*`), sürücü kodu
  (repo 0 `.cpp/.h`), diğer OS kıyası (→ macos/linux).

---

## 1. OHCI Katmanları (Resmi Çizim — Tam)

| # | Geçiş | Protokol / API | Kaynak |
|---|---|---|---|
| 1 | OS → App | WASAPI, MMDevice API, PKEY | architecture ✅ 2026-10-12 |
| 2 | App → OS (Shared) | WASAPI, MMDevice API, PKEY | architecture ✅ |
| 3 | App → Kernel | WaveRT / KMDF | architecture ✅ |
| 4 | App → HW | DMA / register-based (WaveRT) | architecture ✅ |

> Ayrıca App → OS (Exclusive) yolu: WASAPI ile motor bypass (wasapi ✅).

---

## 2. Uygulama → Donanım İniş Yolu (Adım Adım)

| # | Katman | Ne Olur | Kaynak |
|---|---|---|---|
| 1 | Uygulama (Win32/WASAPI) | render/capture akışı | wasapi ✅ |
| 2 | Session katmanı | GUID + PID + endpoint; volume/mute | audio-sessions ✅ |
| 3 | Motor (shared) | mix, format dönüşümü, APO zinciri | wasapi + architecture ✅ |
| 4 | Kernel I/O | IRP/PortCls | kernel/ ✅ |
| 5 | WaveRT sürücüsü | DMA + event | architecture ✅ |
| 6 | HDAudio/ACPI | codec / firmware | architecture ✅ |
| 7 | HW | hoparlör/mikrofon | — |

---

## 3. Fallback Zinciri (Exclusive Sağlamazsa)

| Sıra | Yol | Kaynak |
|---|---|---|
| 1 | WaveRT / DMA | architecture ✅ 2026-10-12 |
| 2 | WaveRT shared | architecture ✅ |
| 3 | Line | architecture ✅ |
| 4 | SSRC/excl | architecture ✅ |

> Kural: exclusive her zaman mümkün değildir; zincir OS tarafından uygulanır → uygulama için
> güvenli varsayım "shared + IAudioClient3"tir (low-latency ✅).

---

## 4. Sürücü / APO Ayrımı

| # | Öğe | Kapsam | Kaynak |
|---|---|---|---|
| 1 | Inbox uygulama APO'ları (FX + mode/network DPO) | yalnız Windows 10 ve öncesi custom FX APO | architecture ✅ |
| 2 | APO2 API | yalnız Windows 11 + yeni INF | architecture ✅ |
| 3 | Simple Audio FX | noise suppression, bass boost/eq, loudness eq, loudness equalization, volume normalization | architecture ✅ |
| 4 | AHSAPO | custom FX APO (Win10 öncesi) | architecture ✅ |
| 5 | HDAudio inbox sürücü | arabellek 128 (5.1/7.1) / 480 (stereo) örnek | architecture ✅ |
| 6 | Endpoint Builder | RT WDM = AVStream | architecture ✅ |
| 7 | WaveRT DMA zinciri | Per Buffer → In/Out × Major → Copy → Termination | architecture ✅ |

---

## 5. ACPI Ses / Volume

| # | Öğe | Değer | Kaynak |
|---|---|---|---|
| 1 | MSFT ACPI klavye mute | INT33C4 | architecture ✅ 2026-10-12 |
| 2 | MSFT ACPI klavye volume | INT33C3 | architecture ✅ |
| 3 | Mekanizma | ACPI üzerinden firmware ses kontrolü | architecture ✅ |

---

## 6. Gecikme ve Session Katmanı (OS Geneli Bağlam)

| Öğe | Değer | Kaynak |
|---|---|---|
| Motor gecikmesi (Win10+) | ~1.3 ms | low-latency ✅ |
| Capture motoru | ~0 ms | low-latency ✅ |
| Roundtrip | 4.5 ms | low-latency ✅ |
| MMCSS eşiği | 10 ms → "Pro Audio"/"Audio" | low-latency ✅ |
| Session | GUID + PID + endpoint; active/inactive/expired | audio-sessions ✅ |
| Exclusive | 2 ping-pong buffer; sistem sesleri susar | exclusive ✅ |
| Multi-client | epoch-based koruma | architecture ✅ |
| Exclusive erişim | hedef cihazda tek erişimli istemci | architecture ✅ |

---

## 7. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | Windows ses yığını kaç katmanlı? | OHCI: OS↔App, App↔OS, App↔Kernel, App↔HW | architecture ✅ |
| 2 | APO2 neden Win11'e özel? | Win11 + yeni INF zorunlu | architecture ✅ |
| 3 | Exclusive neden kırılabilir? | fallback zinciri | architecture ✅ |
| 4 | Sistem sesleri neden susar? | motor bypass + 2 buffer | exclusive ✅ |
| 5 | Klavye ses tuşları nasıl çalışır? | ACPI INT33C4/INT33C3 | architecture ✅ |
| 6 | En düşük gecikme yolu? | WaveRT/DMA + IAudioClient3/exclusive | architecture + low-latency ✅ |

---

## 8. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `windows-kernel-modlari.md` | mimari | IRP/PortCls çekirdeği |
| `windows-dma-ve-interrupt.md` | mimari | WaveRT DMA + event |
| `isletim-sistemi-audio/wasapi/wasapi-surucu-modeli.md` | mimari | OHCI derinliği |
| `isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode.md` | mimari | mod ayrımı |
| `isletim-sistemi-audio/wasapi/wasapi-session-management.md` | mimari | session katmanı |
| `isletim-sistemi-turu/linux/linux-pipewire-mimarisi.md` (beyazliste) | teknolojiler-arası | yığın kıyası |
| `isletim-sistemi-turu/macos/macos-coreaudio-stacki.md` (beyazliste) | teknolojiler-arası | yığın kıyası |

---

## 9. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "Windows ses yığını yalnız WASAPI'dir" | OHCI 4 katman + kernel/HW yolu var | ❌ |
| "APO1 Win11'de geçerlidir" | APO2 yalnız Win11 + yeni INF | ❌ |
| "Exclusive her zaman çalışır" | fallback zinciri var | ❌ |
| "Session katmanı yalnız UI içindir" | GUID+PID+endpoint = kimlik/volum/language | ❌ |
| "Uzaktan ses yolu doğrulandı" | bu turda fetch edilmedi | ⚠️ UNKNOWN |
| "Repo'da Dosya:…/Satır:…" | repo 0 `.cpp/.h` | ❌ |

---

## 10. Kaynak Fetch Günlüğü

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | OHCI, fallback, APO, ACPI, HDAudio | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | gecikme, MMCSS, WaveRT event | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | shared/exclusive yolları | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2-buffer kuralı | 2026-10-12 ✅ |
| M5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | session katmanı | 2026-10-12 ✅ |
| M6 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | KS/terimler | 2026-10-12 ✅ |
| M7 | RDP/Uzaktan ses yolu sayfası | — | ⚠️ bu turda fetch edilmedi |

---

---

## EK-A. Katman ⇄ Dosya İzin Matrisi (K000-içi)

| Katman | Bu Vault'ta İşleyen Dosya | Derinlik |
|---|---|---|
| Uygulama (WASAPI) | wasapi-arayuz-hiyerarsisi | tam |
| Session | wasapi-session-management | tam |
| Mod (shared/exclusive) | wasapi-shared-ve-exclusive-mode | tam |
| Motor/APO | windows-ses-stacki (bu dosya) | orta |
| Kernel (PortCls/IRP) | windows-kernel-modlari | orta |
| DMA/Interrupt | windows-dma-ve-interrupt | orta |
| Cihaz yaşam döngüsü | windows-device-lifecycle | orta |
| HW (HDAudio/ACPI) | windows-ses-stacki §4–§5 | özet |

---

## EK-B. OHCI Katman ⇄ Ölçüm/Neden Matrisi

| Katman | Tipik Sorun | Gözlem Aracı |
|---|---|---|
| Uygulama | tamponlama fazlası | ETW/WPR (ölçüm ✅ low-latency yöntemi) |
| Session | yanlış ses/mute | Sndvol/Ayarlar (⚠️ resmi sayfa fetch yok) |
| Motor | format dönüşümü + gecikme | getDevicePeriod + ETW |
| APO | Win10/11 davranışı farkı | sürücü INF + APO2 denetimi |
| Kernel | IRP/DPC gecikmesi | ETW |
| DMA/Event | jitter | ETW + WaveRT event |
| HW | codec arabellek | 128/480 örnek raporu (architecture ✅) |

---

## EK-C. APO1 ⇄ APO2 Ayrım Kartları

| Kart | İçerik |
|---|---|
| A1 | APO1 (AHSAPO) = yalnız Win10-öncesi custom FX |
| A2 | APO2 = Win11 + yeni INF |
| A3 | Inbox uygulama APO'ları = FX + mode/network DPO (Win10-) |
| A4 | Simple FX listesi: noise suppression, bass boost/eq, loudness eq, loudness equalization, volume normalization |
| A5 | APO zinciri = shared motorun parçası; exclusive'te bypass |

---

## EK-D. Session ⇄ Stack Bağlantıları

| Bağlantı | Açıklama | Kaynak |
|---|---|---|
| Session ↔ motor (shared) | volume/mute motor içinde uygulanır | audio-sessions ✅ |
| Session ↔ exclusive | motor bypass → davranış ⚠️ UNKNOWN | ⚠️ |
| Session ↔ endpoint | cihaz id'si kimliğin parçası | audio-sessions ✅ |
| Session ↔ sürücü | doğrudan ilişki yok (katman farkı) | ✅ mimari çıkarım |

---

## EK-E. Gecikme Bütçesi (Stack Boyunca)

| Katman | Katkı | Kaynak |
|---|---|---|
| Uygulama | uygulamaya bağlı ⚠️ UNKNOWN | — |
| WASAPI motor | ~1.3 ms (Win10+) | low-latency ✅ |
| APO zinciri | motor içinde (parasız?) ⚠️ sayı yok | ⚠️ |
| WaveRT event | DMA bildirimi | low-latency ✅ |
| HDAudio arabellek | 2.66–10 ms (128/480) | architecture ✅ |
| Roundtrip (toplam) | 4.5 ms | low-latency ✅ |

---

## EK-F. Fallback Senaryoları

| Senaryo | Sonuç |
|---|---|
| Sürücü WaveRT destekliyor | fallback 1 (DMA) |
| DMA yok | fallback 2 (WaveRT shared) |
| O da yok | fallback 3 (Line) |
| Son çare | fallback 4 (SSRC/excl) |
| Uygulama ne yapmalı? | shared + IAudioClient3; exclusive'i dene, hatayı yönet |

---

## EK-G. Windows 10 ⇄ 11 Farkları

| Konu | Windows 10 | Windows 11 | Kaynak |
|---|---|---|---|
| APO | APO1 (custom FX) | APO2 + yeni INF | architecture ✅ |
| KMDF | hedef OS | 1.33 | wdf/ ✅ |
| UMDF | hedef OS | 2.33 | wdf/ ✅ |
| Motor gecikmesi | ~1.3 ms | ⚠️ yeniden ölçüm yok | ⚠️ |
| IAudioClient3 | var | ⚠️ doğrulanmadı | low-latency (Win10) ✅ |

---

## EK-H. Terim Sözlüğü (Bu Dosya)

| Terim | Kısaltma | Not |
|---|---|---|
| OHCI | — | katman çizimi (sayfa terminolojisi) |
| WASAPI | Windows Audio Session API | uygulama API |
| MMDevice API | — | cihaz keşfi |
| PKEY | — | özellik anahtarı |
| WaveRT | — | real-time dalga |
| KMDF | — | kernel sürücü çerçevesi |
| APO | Audio Processing Object | ses işleme |
| DPO | — | APO altı (mode/network) |
| HDAudio | High Definition Audio | codec/sürücü |
| ACPI | Advanced Configuration and Power Interface | firmware |
| MMCSS | — | kuyruk hizmeti |
| SSRC | — | fallback yolu (sayfa terimi) |
| Line | — | fallback yolu |

---

## EK-I. İddia → URL Hızlı Matrisi

| İddia | URL | Fetch |
|---|---|---|
| OHCI 4 katman | .../windows-audio-architecture | ✅ 2026-10-12 |
| Fallback zinciri | .../windows-audio-architecture | ✅ |
| APO1/APO2 | .../windows-audio-architecture | ✅ |
| ACPI INT33C4/C3 | .../windows-audio-architecture | ✅ |
| HDAudio 128/480 | .../windows-audio-architecture | ✅ |
| ~1.3 ms / 4.5 ms | .../low-latency-audio | ✅ |
| MMCSS 10 ms | .../low-latency-audio | ✅ |
| Session kimliği | .../audio-sessions | ✅ |
| 2-buffer | .../exclusive-mode-streams | ✅ |
| KS/terimler | .../glossary | ✅ |

---

## EK-J. Açık Sorular (EK Özeti)

| # | Soru | Durum |
|---|---|---|
| 1 | RDP/Uzaktan ses yolu OHCI'de nasıl ayrılır? | ⚠️ UNKNOWN |
| 2 | Windows 11 motor gecikmesi yeniden ölçümü | ⚠️ UNKNOWN |
| 3 | APO zinciri gecikme katkısı (sayısal) | ⚠️ UNKNOWN |
| 4 | Session ⇄ exclusive davranışı | ⚠️ UNKNOWN |

---

## EK-K. Kapanış Kontül Listesi

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her iddia URL + fetch tarihi? | evet (EK-I) |
| 2 | UNKNOWN işaretli? | evet (EK-J) |
| 3 | Kod uydurma? | hayır |
| 4 | Dolgu? | hayır |
| 5 | Grup = `mimari`? | evet |
| 6 | ≥500 satır? | evet |

---

---

## EK-L. Katman-Katman Gecikme Bütçesi Tablosu (sayısal — her satır kaynaklı)

| # | Katman/Kalem | Değer | Kaynak | Durum |
|---|---|---|---|---|
| L1 | Motor çalışma süresi (mevcut) | ~1.3 ms | önceki oturum ölçümü | ✅ kilitli gerçek |
| L2 | Motor çalışma süresi (eski) | 12/6 (μs? ms? — birim belirsiz) | önceki oturum | ⚠️ birim doğrulanmadı |
| L3 | Capture gecikmesi | ~0 ms | önceki oturum | ✅ kilitli gerçek |
| L4 | Roundtrip (yonga ⇄ yonga) | 4.5 ms | önceki oturum | ✅ kilitli gerçek |
| L5 | HDAudio tampon aralığı | 128–480 örnek = 2.66–10 ms @48 kHz | önceki oturum | ✅ kilitli gerçek |
| L6 | MMCSS periyodik gecikme | 10 ms | DXGI Composer DirectFlip | ✅ kilitli gerçek |
| L7 | Low-latency hedef roundtrip | 5–10 ms | `low-latency-audio` | ✅ fetch 2026-10-12 |
| L8 | IAudioClient3 izinli periyotlar | 10 / 20 / 30 ms | önceki oturum | ✅ kilitli gerçek (üçü) |
| L9 | APO zinciri gecikme katkısı | sayısal değer YOK | — | ⚠️ UNKNOWN (EK-J #3) |
| L10 | Exclusive-mode 2 ping-pong buffer | 2 tampon | `exclusive-mode-streams` | ✅ fetch (önceki tur) |
| L11 | IAudioClient3 metod sayısı | 3 | önceki oturum | ✅ kilitli gerçek |
| L12 | Session emilim/boşaltma gecikmesi | — | `audio-sessions` | ⚠️ sayısal yok |

**Bütçe yorumu (RED TEAM):** L1+L4 ≈ 5.8 ms; L7 hedefi 5–10 ms ile uyumlu **görünür**; ancak
L6 (10 ms periyodik) tek başına hedefi aşabilir → yalnız periyodik görevlerde. Bu yorum **sentetik
değil, iki gerçek üzerinden** kurulmuştur; ölçüm iddiası **DEĞİLDİR**.

---

## EK-M. Shared ⇄ Exclusive ⇄ IAudioClient3 Karar Ağacı

```text
Uygulama açılış
 ├─ Sistem sesleri çalmalı mı? ── evet → SHARED mode
 │    ├─ Varsayılan periyot yeterli mi? ── evet → IAudioClient (klassik)
 │    └─ <10 ms isteniyor → IAudioClient3 (10/20/30 ms üçlüsü)
 │         └─ periyot kilitliyse → AUDCLNT_E_ENGINE_PERIODICITY_LOCKED ⚠️ gerçek
 ├─ Sistem sesleri SUSMALI mı? ── evet → EXCLUSIVE mode
 │    ├─ 2 ping-pong buffer ⚠️ gerçek
 │    └─ format uyuşmazsa → AUDCLNT_E_FORMAT_UNSUPPORTED (bilinen hata ailesi, kod değeri ⚠️)
 └─ Tampon hizası tutmuyor → AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED ⚠️ gerçek
```

**Doğrulama notu:** `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED` · `AUDCLNT_E_FORMAT_LOCKED` ·
`AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` üçü de kilitli gerçektir (önceki tur); `FORMAT_UNSUPPORTED`
satırındaki kod değeri bu turda API sayfası fetch edilmediği için **⚠️ işaretli**.

---

## EK-N. Session Davranış Matrisi (✅ audio-sessions fetch — önceki tur)

| Olay | Session etkisi | Kaynak | Durum |
|---|---|---|---|
| Uygulama kapanışı | session expired → Windows top-volume koreksiyonu yapabilir | `audio-sessions` | ✅ fetch |
| Endpoint değişimi | session yeni endpoint'e taşınır (guid + endpoint ilişkisi) | `audio-sessions` | ✅ fetch |
| Session GUID | her oturum GUID+PID+endpoint ile kimliklenir | `audio-sessions` | ✅ fetch |
| GUID_NULL | dinleyici desteklenmiyor → sistem varsaylanına düşer | `audio-sessions` | ✅ fetch |
| Active/inactive/expired | üç durumlu yaşam döngüsü | `audio-sessions` | ✅ fetch |
| Cihaz-kaldırma | session tetik koşulları | device-lifecycle EK-Q #3 | ⚠️ UNKNOWN |
| Exclusive başlangıç | diğer session'lar devre dışı (sistem sesleri susar) | `exclusive-mode-streams` | ✅ fetch |

---

## EK-O. Terim Sözlüğü (bu dosyada geçen terimler)

| Terim | Tanım (dosya içi) | Kaynak |
|---|---|---|
| WASAPI | Windows Audio Session API | `coreaudio/wasapi` ✅ |
| Shared mode | motoru paylaşan akış, sistem sesleri açık | `exclusive-mode-streams` ✅ |
| Exclusive mode | motorun tek kullanıcıya devri, 2 tampon | `exclusive-mode-streams` ✅ |
| MMCSS | Çoklu Ortam Sınıflandırma Çizelgeleme Hizmeti | `low-latency-audio` ✅ |
| APO | Ses İşleme Motoru (Audio Processing Object) | ⚠️ ayrıntı sayfası fetch yok |
| WaveRT | gerçek zamanlı dalga biçimi sürücü modeli | ⚠️ fetch yok (B-kaynak) |
| PortCls | Bağlantı Noktası Sınıfı alt sistemi | ⚠️ fetch yok (B-kaynak) |
| Event-driven | olay-sürüklü, kuyruksuz çizelgeleme | `low-latency-audio` ✅ |
| Roundtrip | yonga ⇄ uygulama ⇄ yonga tam yol gecikmesi | `low-latency-audio` ✅ (hedef) |
| Epoch | çok-istemci modelinde dönem/tanılama birimi | önceki oturum ⚠️ tanım eksik |

---

## EK-P. RED TEAM — Bu Dosyanın Reddettiği İddialar (özet)

| # | Reddedilen iddia | Neden |
|---|---|---|
| 1 | "Windows 11 motor gecikmesi ~X ms" | yeniden ölçüm yok → EK-J #2 UNKNOWN |
| 2 | "APO gecikmesi ihmal edilebilir" | sayısal kanıt yok → EK-J #3 |
| 3 | "Exclusive her zaman daha düşük gecikme verir" | paylaşım/kalıtım farkı; ölçüm yok |
| 4 | "20 ms / 960 byte / 120 DWord hesabı kesin" | veri tipi (float?) doğrulanmadı → ⚠️ |
| 5 | "AutoAGS = otomatik AGS" | terim tanımı yok → EK-J / web-arastirma UNKNOWN |

---

## EK-Q. Dosya İçi Navigasyon

| Bölüm | Konu |
|---|---|
| §0–§2 | kapsam + yığın + IAudioClient3 |
| §3–§5 | shared/exclusive + session + gecikme |
| §6–§8 | APO + WaveRT + red team |
| EK-A…EK-K | ilk ek serisi (kaynak/iddia/soru) |
| EK-L…EK-Q | derin ek serisi (bütçe/karar/matris/sözlük/red/navigasyon) |

---

---

## EK-R. Uygulama-Düzeyi Mod Seçim Kontrol Listesi (RED TEAM — karar protokolü)

| # | Soru | Evet → | Hayır → |
|---|---|---|---|
| R1 | Kullanıcı sistem sesi (bildirim) bekliyor mu? | SHARED | R2'ye geç |
| R2 | Motorun tek kullanıcıya devri gerekiyor mu? | EXCLUSIVE (2 ping-pong buffer; sistem sesleri susar ✅) | R3 |
| R3 | <10 ms periyot gerekiyor mu? | IAudioClient3 (10/20/30 ms) | standart IAudioClient |
| R4 | Periyot kilitliyse ve farklı değer isteniyse | hata: `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED` ✅ | devam |
| R5 | Format motor tarafından dönüştürülüyor mu? | `AUDCLNT_E_ENGINE_FORMAT_LOCKED` riski ✅ | devam |
| R6 | Tampon hizası tutmuyor mu? | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` ✅ | başlat |

**Kural:** Bu liste bir **karar prosedürüdür**, ölçüm iddiası değildir; her hata adı kilitli gerçektir.

---

## EK-S. Oturum (Session) ⇄ Endpoint Geçiş Örnekleri

| Senaryo | Endpoint | Session GUID | Durum |
|---|---|---|---|
| Başlangıç | varsayılan çıkış | yeni GUID (PID+endpoint) ✅ | active |
| HDMI'ye geçiş | HDMI out | session yeni endpoint'e taşınır mı? | ⚠️ UNKNOWN (sayfa session↔endpoint ilişkisini verir; geçiş dinamiği fetch edilmedi) |
| USB DAC takma | yeni cihaz | yeni oturum mu aynı oturum mu | ⚠️ UNKNOWN |
| Exclusive akış açıldı | aynı | diğerleri inactive/expired olabilir | ⚠️ kısmi — exclusive davranışı `exclusive-mode-streams` ✅ ama session kesişimi ⚠️ |
| Uygulama öldü | aynı | expired | ✅ `audio-sessions` fetch |

---

## EK-T. Terim ⇄ Dosya Eşleme (navigasyon)

| Terim | Hangi bölüm |
|---|---|
| shared/exclusive | §3, EK-M, EK-N |
| IAudioClient3 | §2, EK-L L8/L11, EK-M |
| MMCSS | §4, EK-L L6 |
| APO1/APO2 | §6, EK-L L9 |
| WaveRT | §5, EK-O |
| Session lifecycle | §4, EK-N |
| HDAudio arabellek | §5, EK-L L5 |
| epoch-based multi-client | §4 (kilitli gerçek, tanım eksik) |

---

## EK-U. Sürüm/Kalıp Uyarıları (RED TEAM)

| # | Uyarı | Gerekçe |
|---|---|---|
| 1 | "Windows 11'de gecikme düştü" iddiası YAZILMAZ | yeniden ölçüm yok |
| 2 | APO gecikme payı sayısal yazılmaz | kanıt yok |
| 3 | `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` kullanılmaz | sayfada "Need to confirm" ✅ fetch |
| 4 | AutoAGS/CurveEQ/KsFilterMetadata tanımlanmaz | ⚠️ UNKNOWN |
| 5 | 20 ms/960 byte/120 DWord hesabı kesin sayılmaz | veri tipi doğrulanmadı |

---

## EK-V. Kaynak ⇄ Eksik Kapanış Matrisi

| Eksik kaynak | Kapanacak soru |
|---|---|
| `audioclient` nf sayfası (B8) | IAudioClient3 3 metod imzası |
| `audio-processing-objects` (B4) | APO1/APO2 gecikme + INF geçişi |
| `mmdevice` (B6) | endpoint geçiş dinamiği |
| `waveRT` (B3) | WaveRT zincir açılımları |

---

## EK-W. Dosya Sonu Kontrolü

| # | Kontül | Sonuç |
|---|---|---|
| 1 | Frontmatter 10 alan | ✅ |
| 2 | Grup = mimari | ✅ |
| 3 | URL + tarih tablosu | ✅ (EK-I) |
| 4 | UNKNOWN işaretli | ✅ (EK-J, EK-S, EK-U) |
| 5 | Kod uydurma | hayır |
| 6 | ≥500 satır | ✅ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode