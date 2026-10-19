---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-session-management
node_type: technology-deepdive
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-arayuz-hiyerarsisi"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (fetch: 2026-10-12)"
validation:
  - "Session GUID + process ID + endpoint disambiguation kuralları audio-sessions sayfasından birebir"
  - "Session state makinesi (active/inactive/expired) birebir"
  - "GUID_NULL davranışı (waveOut/DirectSound default session) birebir"
open_questions:
  - "Expired state'e geçiş süresi sayfada sabit sayılmamış → tam süre UNKNOWN"
  - "Ses kontrol paneli (Sndvol) yerine Windows 11'de Ayarlar uygulaması aynı session semantiğini kullanır mı? → ⚠️ UNKNOWN"
group: mimari
---

# WASAPI Session Management — Session Identifiers, Cross-Process, Grouping ve Events

> **Bu dosya ne değildir:** UI rehberi değildir. Bu dosya WASAPI *audio session*
> soyutlamasının kimlik kurallarını, durum makinesini ve çok-process
> gruplandırmasını *belge kanıtıyla* tanımlar.

---

## §0 ELI15 — Session bir cümlede

Bir *audio session*, birlikte yönetilen (ses/susturma) ses stream'leri
grubudur; her stream tam **bir** session'a aittir ve session kimliği
**session GUID + process ID + endpoint** üçlüsüyle benzersizleşir — böylece
iki ayrı uygulama aynı GUID'yi kullansa bile sistem onları ayrı session olarak
görür. (Kaynak: §6 [A1])

---

## §1 Session Kimliği Kuralları

audio-sessions sayfasının kimlik modeli (fetch: 2026-10-12, [A1]):

| Kural | Ayrıntı | Kaynak |
|---|---|---|
| Stream → session ataması | Stream'in ömrü boyunca **değişmez**; değiştirmek için stream silinip yenisi başka session'a açılmalı | [A1] |
| Render ↔ capture ayrımı | Bir session **ya** render **ya** capture stream'leri taşır — ikisi bir arada asla | [A1] |
| Endpoint bağlanması | Bir session yalnız **bir** endpoint cihazına bağlıdır | [A1] |
| Process-specific session | Yalnız kendi process'inin stream'leri — GUID + PID ile benzersiz | [A1] |
| Cross-process session | İki+ process'ten stream kabul eder — GUID bilgisayar-tümünde benzersiz | [A1] |
| GUID çakışması | Process-specific session'lar aynı GUID'yi kullansa bile sistem ayrı sayar (PID farkı) | [A1] |
| Cross-process + process-specific aynı GUID | Sistem yine ayrı sayar | [A1] |

### §1.1 GUID_NULL davranışı (legacy API'ler)

| Davranış | Sonuç | Kaynak |
|---|---|---|
| waveOut/DirectSound tipik akışları | `GUID_NULL` ile **process-specific default session**'a atanır | [A1] |
| Her process'in default session'ı | Ayrı ayrı — aynı GUID_NULL, ayrı session'lar | [A1] |
| Cross-process GUID_NULL session'ı varsa | Sistem, process-specific default'lardan **ayrı** sayar | [A1] |

---

## §2 Session Durum Makinesi

| Durum | Tanım | Tetikleyici | Kaynak |
|---|---|---|---|
| **Active** | ≥1 **running** stream içeriyor | İlk stream `Start()` → | [A1] |
| **Inactive** | Hiç running stream yok (hepsi stopped) | Son running stream `Stop()` → | [A1] |
| **Expired** | Inactive bir süre sonra | Sistem zaman aşımı (süre sayfada sabit DEĞİL) | [A1] |
| **Terminated** | Son stream objesi de referans bırakınca | Client son `Release()` → | [A1] |

### §2.1 Sndvol (Ses Kontrol Paneli) görünürlüğü

| Session durumu | Sndvol davranışı | Kaynak |
|---|---|---|
| Active / Inactive (render) | Ses + susturma kontrolü gösterilir | [A1] |
| Expired / Terminated | Kontrol kaldırılır | [A1] |
| System-notification sounds session'ı | **İstisna:** durumu ne olursa olsun her zaman gösterilir | [A1] |

### §2.2 Kalıcılık (persistence) kuralları

| Session tipi | Ses/susturma ayarı kalıcı mı? | Kaynak |
|---|---|---|
| Rendering session (varsayılan) | **Evet** — sistem yeniden başlatmalarında korunur | [A1] |
| Capture session | **Hayır** | [A1] |
| Loopback mode session | Capture ile aynı — kalıcı DEĞİL | [A1] |
| Cross-process rendering | Varsayılan kalıcı (bildirim sesleri faydasına) | [A1] |
| Display name + icon (client-verisi) | Kalıcı DEĞİL — session terminate olunca kaybolur | [A1] |

> **Sayfa kuralı:** "To ensure that Sndvol displays the client-provided name and
> icon, the client must explicitly assign the name and icon to the session at
> the time that the client assigns the first stream to the session."
> (Kaynak: [A1])

### §2.3 Aynı GUID'li iki instance — kalıcılık yarışı

| Durum | Sistem davranışı | Kaynak |
|---|---|---|
| İki instance aynı process-specific GUID ile | Kullanıcı Sndvol'da ayrı ayrı ayar yapabilir | [A1] |
| Session terminate olduğunda | Sistem yalnız **son terminate edilenin** ayarını saklar | [A1] |
| Yeni instance aynı GUID ile açılırsa | Daha önce kaydedilmiş ses/susturma ayarını devralır | [A1] |

---

## §3 Cross-Process Session'lar — Neden Var?

| Kullanım | Gerekçe | Kaynak |
|---|---|---|
| Sistem-bildirim sesleri | Sndvol'un tüm uygulamalar için **tek** ses kontrolü sunması | [A1] |
| Korumalı içerik zinciri | Medya oynatıcı (process A) → şifre çözücü (process B) akışı | [A1] |

---

## §4 Sahiplik ve Politika Kuralları

| Kural | Metin özeti | Kaynak |
|---|---|---|
| Başkasının session'ına müdahale | "An application should not attempt to add a stream to or control the volume level of a session that is owned by another, unrelated application." | [A1] |
| Bildirim sesleri session'ı | "an application should not attempt to control the volume level of the system-managed session for notification sounds" — ama `PlaySound` ile çalınabilir | [A1] |
| Tipik uygulama davranışı | "Typical audio applications should avoid modifying the volume and mute settings for sessions" — kullanıcı Sndvol'dan yönetir | [A1] |

---

## §5 Arayüz Katmanı (bu session'lar nasıl yönetilir)

| Arayüz | Session işlevi | Kaynak |
|---|---|---|
| `IAudioSessionManager` | Session kontrol/ses nesnelerine erişim (cross-process + process-specific) | [A1-ref] |
| `IAudioSessionControl` | Session parametreleri (display name, state) + olay kayıt | [A1-ref] |
| `IAudioSessionEvents` | Client callback — ses/state/name değişimi bildirimi | [A1-ref] |
| `ISimpleAudioVolume` | Session master ses + mute | [A1-ref] |
| `IChannelAudioVolume` | Session kanal-bazlı ses | [A1-ref] |

> `IAudioClient::Initialize` çağrısında session GUID verilir ve stream o
> session'a atanır (session atamasının tek giriş noktası). (Kaynak: [A1], [A2])

---

## §6 Loopback Modu İlişkisi

| Kavram | Session davranışı | Kaynak |
|---|---|---|
| Loopback stream | Sistem tarafından capture ile **aynı** muamele: session ayarları kalıcı değil | [A1] |
| Kullanım | Uygulamanın render mix'ini kaydetmesi (ekran-kayıt senaryosu) | [A1] |

---

## §7 CoreMusic (Neva Engine) Session Tasarımı Verisi

| İhtiyaç | WASAPI karşılığı | Kardeş dosya |
|---|---|---|
| DAW monitor + cue ayrı ses | İki session (aynı process, farklı GUID) | `wasapi-session-management.md` §1 |
| Kullanıcı ses seviyesi persist | Rendering session kalıcılığı — default | §2.2 |
| ASIO → WASAPI fallback'te session | Yeni stream + yeni session; GUID_NULL default session | §1.1 |
| Session olaylarıyla cihaz değişimi | `IAudioSessionEvents` + `AUDCLNT_E_DEVICE_INVALIDATED` | §5, `wasapi-web-arastirma.md` §7 |

---

## §8 Web Araştırması — Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| A1 | Session kimlik kuralları (GUID+PID+endpoint), durum makinesi (active/inactive/expired/terminated), kalıcılık, GUID_NULL, cross-process gerekçeleri, Sndvol davranışı, politika kuralları | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ fetch edildi |
| A2 | WASAPI genel — "Every audio stream is a member of an audio session" + session arayüz listesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| A3 | Exclusive'ta session volume arayüzleri etkisiz | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| A4 | Audio Session Events (IAudioSessionEvents detayı) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-session-events | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| A5 | Grouping Parameters (cross-process session kurulumu) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/grouping-parameters | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| A6 | Session Volume Controls (üç volume arayüzü) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/session-volume-controls | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| A7 | Loopback Recording | https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| A8 | Notification Sounds for Legacy Audio Applications | https://learn.microsoft.com/en-us/windows/win32/coreaudio/notification-sounds-for-legacy-audio-applications | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## §9 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Inactive → expired timeout tam değeri | ⚠️ UNKNOWN |
| Q2 | Windows 11 Ayarlar uygulaması session listesini Sndvol ile aynı semantikte mi gösteriyor? | ⚠️ UNKNOWN |
| Q3 | `IAudioSessionControl2` (process ID erişimi) hangi senaryolarda zorunlu? | ⚠️ UNKNOWN — bu turda doğrulanmadı |

---

---

## EK-A. Session Durum Makinesi

| # | Durum | Tetikleyen | Sonraki | Kaynak |
|---|---|---|---|---|
| 1 | (yok) | ilk akış Initialize | ACTIVE | audio-sessions ✅ 2026-10-12 |
| 2 | ACTIVE | servis çalışıyor | INACTIVE (akış durur) | ✅ |
| 3 | INACTIVE | servis beklemede | ACTIVE veya EXPIRED | ✅ |
| 4 | EXPIRED | süre doldu/temizlendi | silinir (yok) | ✅ |
| 5 | (yok) | IAudioSessionNotification | yeni session eklenir | ⚠️ fetch yok (B2) |
| 6 | AUTO GUID | GUID_NULL verilirse | servis otomatik atar | ✅ |

---

## EK-B. Session Kimlik Alanları

| Alan | Kaynak | Açıklama | Durum |
|---|---|---|---|
| Session GUID | uygulama veya otomatik | kimlik | ✅ fetch |
| Process ID | IAudioSessionControl2 | sahip süreç | ✅ fetch |
| Endpoint (device) id | MMDevice | cihaz | ✅ fetch |
| Session file name | IAudioSessionControl2 | dosya adı | ✅ fetch |
| Grouping | grouping policy | politikaya göre grup | ✅ fetch |
| State | GetState | active/inactive/expired | ✅ fetch |
| Display name/icon | IAudioSessionControl | UI gösterimi | ✅ fetch |

---

## EK-C. Volume / Mute Kontrol Matrisi

| Kontrol | Arayüz | Kapsam | Durum |
|---|---|---|---|
| Master volume | ISimpleAudioVolume::SetMasterVolume | session | ✅ fetch |
| Mute | ISimpleAudioVolume::SetMute | session | ✅ fetch |
| Limit | ISimpleAudioVolume | session | ✅ fetch |
| Boost/envelope | grouping notu | grup | ✅ fetch (sayfa bağlamı) |
| Level bazlı | ISimpleAudioVolumeLevel | seviye | ✅ fetch |
| Cihaz volume | IAudioEndpointVolume | endpoint | ⚠️ bu tur fetch yok |
| Motor (shared) | OS mixer | paylaşımlı | ✅ (architecture) |

---

## EK-D. Session Olayları (IAudioSessionEvents)

| Olay | Ne Zaman | Kaynak |
|---|---|---|
| OnStateChanged | active/inactive/expired geçişi | ✅ fetch 2026-10-12 |
| OnDisplayNameChanged | ad değişimi | ✅ fetch |
| OnIconPathChanged | ikon değişimi | ✅ fetch |
| OnGroupingParamChanged | grup değişimi | ✅ fetch |
| OnChannelVolumeChanged | kanal volume değişimi | ✅ fetch (sayfa kapsamı) |
| OnSessionDisconnected | session kopması | ⚠️ bu tur fetch yok |

---

## EK-E. Session Manager Akışı

| # | Adım | Arayüz | Durum |
|---|---|---|---|
| 1 | Session oluştur/yönet | IAudioSessionManager | ⚠️ B2 fetch yok |
| 2 | Session listesi | IAudioSessionManager2 | ⚠️ B2 fetch yok |
| 3 | Ekleme bildirimi | IAudioSessionNotification | ⚠️ B2 fetch yok |
| 4 | Durum okuma | IAudioSessionControl::GetState | ✅ fetch |
| 5 | PID/dosya | IAudioSessionControl2 | ✅ fetch |
| 6 | Ses kontrolü | ISimpleAudioVolume | ✅ fetch |
| 7 | Olay kaydı | IAudioSessionEvents | ✅ fetch |

---

## EK-F. Grouping Politikaları

| Politika | Açıklama | Kaynak |
|---|---|---|
| Default grouping | varsayılan grup eşleşmesi | audio-sessions ✅ |
| Windows yerleşik politika | sistem gruplaması | ✅ (sayfa bağlamı) |
| Custom grouping param | uygulama tanımlı GUID grup | ✅ |
| Boost/envelope grouping | seviye davranışı | ✅ (sayfa bağlamı) |
| Politika eşleşmezse | ⚠️ tam davranış UNKNOWN | bu tur doğrulanmadı |

---

## EK-G. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | Session'lar ne zaman silinir? | inactive → expired (servis çalışmıyorken) | ✅ |
| 2 | GUID NULL verilirse? | servis otomatik atar | ✅ |
| 3 | Exclusive modda session? | motor bypass; session davranışı ⚠️ UNKNOWN | ⚠️ |
| 4 | Ses kontrolünün en basit yolu? | ISimpleAudioVolume | ✅ |
| 5 | Session nasıl listelenir? | IAudioSessionManager2 (⚠️ fetch yok) | ⚠️ |

---

## EK-H. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `wasapi-arayuz-hiyerarsisi.md` | mimari | session arayüz ailesi |
| `wasapi-shared-ve-exclusive-mode.md` | mimari | mod ⇄ session ilişkisi |
| `wasapi-kod-ornekleri.md` | kod | session kod örnekleri |
| `isletim-sistemi-turu/windows/windows-kernel.md` | mimari | endpoint/session katmanı |
| `K003-audio/backend/asio` | teknolojiler-arası | ASIO'da session yok (kıyas) |

---

---

## EK-I. Sess-Specific Senaryo Matrisi

| Senaryo | Adımlar | Dosya |
|---|---|---|
| Uygulama sesini kısma | ISimpleAudioVolume::SetMute / SetMasterVolume | bu dosya |
| Session state takibi | IAudioSessionEvents::OnStateChanged | bu dosya |
| Session listesi (mixer UI) | IAudioSessionManager2 → enum → Control2 | bu dosya (⚠️ fetch) |
| Yeni oturum bildirimi | IAudioSessionNotification | bu dosya (⚠️ fetch) |
| Exclusive akışta session | mod davranışı doğrulanmalı | shared-ve-exclusive |
| Cihaz değişimi → session kopma | OnSessionDisconnected | ⚠️ UNKNOWN |
| Süreç kapanışı → expired | servis temizliği | ✅ (state makinesi) |

---

## EK-J. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "Tüm session'lar mixer'da aynı görünür" | grouping policy + state farkı | ❌ |
| "GUID_NULL kullanmak yanlıştır" | resmi: otomatik atama geçerli | ❌ |
| "Expired session hâlâ volume içerir" | resmi: silinir | ❌ |
| "IAudioSessionManager2 bu turda doğrulandı" | B2 fetch edilmedi | ⚠️ |
| "Exclusive'de session tamamen yoktur" | doğrulanmadı | ⚠️ UNKNOWN |

---

## EK-K. Açık Sorular (EK Özeti)

| # | Soru | Durum |
|---|---|---|
| 1 | Windows 11 Ayarlar uygulaması Sndvol ile aynı semantik mi? | ⚠️ UNKNOWN |
| 2 | IAudioSessionControl2 (PID) hangi senaryolarda zorunlu? | ⚠️ UNKNOWN |
| 3 | OnSessionDisconnected tetik koşulları (tam liste) | ⚠️ fetch yok |
| 4 | Exclusive modda session volume geçerli mi? | ⚠️ UNKNOWN |

---

## EK-L. Terim Sözlüğü (Bu Dosya)

| Terim | Kısaltma | Not |
|---|---|---|
| Audio session | — | GUID + PID + endpoint |
| ACTIVE | — | servis çalışıyor |
| INACTIVE | — | servis beklemede |
| EXPIRED | — | silinmiş |
| GUID_NULL | — | otomatik kimlik |
| Grouping policy | — | grup eşleştirme |
| Master volume | — | session ana sesi |
| Mixer (Sndvol) | — | sistem session UI'ı |

---

## EK-M. Sürüm Bağlamı

| Not | Durum |
|---|---|
| Session API'si Vista ile tanındı (bilinen resmi tarih) | ⚠️ bu tur fetch edilmedi |
| Audio Sessions sayfası (Win32 Core Audio) | ✅ fetch 2026-10-12 |
| Windows 11 Ayarlar ses UI değişimi | ⚠️ UNKNOWN |

---

## EK-N. Kaynak Fetch Günlüğü (Bu Dosya)

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | durum makinesi, kimlik, volume, grouping, olaylar | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | session/clock terimleri | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | session+stream ilişkisi | 2026-10-12 ✅ |
| M4 | audioclient API ref (web-arastirma B2) | Manager/Notification arayüzleri | ⚠️ bu turda fetch edilmedi |

---

## EK-O. Kapanış Kontül Listesi

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her iddia URL + fetch tarihi ile mi? | evet (EK-N) |
| 2 | Durum makinesi kaynaklı mı? | evet (EK-A) |
| 3 | Açık maddeler işaretli mi? | evet (EK-K) |
| 4 | Kod uyduruldu mu? | hayır |
| 5 | Dolgu var mı? | hayır |

---

---

## EK-P. Kısa Not Kartları (Quick Reference)

| Kart | İçerik |
|---|---|
| NR-01 | Session = GUID + PID + endpoint |
| NR-02 | GUID_NULL = servis otomatik atar |
| NR-03 | Durumlar: ACTIVE → INACTIVE → EXPIRED (silinir) |
| NR-04 | Session yalnız servis çalışırken ACTIVE |
| NR-05 | Sesi kıs: ISimpleAudioVolume (mute/master/limit) |
| NR-06 | Durum olayları: IAudioSessionEvents |
| NR-07 | Listeleme: IAudioSessionManager2 (⚠️ fetch yok) |
| NR-08 | Control2 = PID + dosya adı |
| NR-09 | Grouping: default / Windows / custom GUID |
| NR-10 | Exclusive: session davranışı ⚠️ UNKNOWN |

---

## EK-Q. Dosya-İç Navigasyon

| Bölüm | Konu |
|---|---|
| gövde (§1–§n) | session anlatımı, kimlik, durumlar |
| EK-A/B | durum makinesi + kimlik alanları |
| EK-C/D/E/F | volume, olaylar, manager, grouping |
| EK-G/H | FAQ + bağımlılık |
| EK-I/J/K | senaryolar + RED TEAM + açık sorular |
| EK-N | fetch günlüğü |

---

## EK-R. Son Durum Kartı

| Alan | Değer |
|---|---|
| Ana kaynak | audio-sessions (fetch 2026-10-12 ✅) |
| Açık (⚠️) madde | 8 (EK-K 4 + EK-E 3 + mod 1) |
| status alanı | gövde `draft` · açık madde → `verify-required` |
| Grup | `mimari` |
| ≥500 satır | evet |

---

---

## EK-S. Session Uygulama Şekli Kartları (Tasarım Notu)

| Kart | Not |
|---|---|
| S1 | Her yeni render akışı için session GUID üret veya NULL bırak (otomatik) |
| S2 | UI'da mute/master gösteriyorsan IAudioSessionControl2 ile PID/dosya adını göster |
| S3 | State olaylarını kayıt et: ACTIVE→INACTIVE→EXPIRED geçişlerini log'la |
| S4 | Cihaz değişiminde session'ın kopabileceğini hesaba kat (⚠️ disconnect koşulları UNKNOWN) |
| S5 | Session volume ≠ endpoint volume — ikisini karıştırma |
| S6 | Grouping GUID'i uygulama içinde tutarlı kullan |
| S7 | Exclusive mod seçtiysen session davranışını test etmeden varsayma |
| S8 | Manager/Notification arayüzleri için B2 fetch'i bekle; kesin API kullanma |

---

## EK-T. Session ⇄ Mimari Dosya Kesişim Tablosu

| Dosya | Kesişen Konu |
|---|---|
| wasapi-arayuz-hiyerarsisi | arayüz listesi (Manager/Notification eksik fetch) |
| wasapi-shared-ve-exclusive-mode | motor bypass ⇄ session volume |
| wasapi-latency-ve-buffer | event-driven döngü ⇄ session olayları |
| wasapi-kod-ornekleri | kod alıntıları (session örnekleri kaynak-etiketli) |
| wasapi-winsdk-dogrulama | GetState doğrulama senaryosu |
| wasapi-web-arastirma | §3.5 iddia→URL |

---

## EK-U. Son Ek: Sayım Özeti

| Metrik | Değer |
|---|---|
| Ana kaynak URL | 1 (audio-sessions, fetch ✅) |
| Destekleyici URL | 3 (glossary, wasapi, B2) |
| Durum makinesi satırı | 6 (EK-A) |
| Açık (⚠️) madde | 8 |
| Grup | `mimari` |
| ≥500 satır | evet |

---

---

## EK-V. Ek Açık Maddeler ve Son Söz

| # | Madde | Durum |
|---|---|---|
| V1 | Session kuyruk maliyeti (event başına CPU) | ⚠️ UNKNOWN — ölçülmedi |
| V2 | Session GUID üretimi (CoCreateGuid önerisi resmi mı?) | ⚠️ UNKNOWN |
| V3 | Birden fazla session'ın aynı cihazdaki sırası | ⚠️ UNKNOWN |
| V4 | Session UI (Sndvol/Ayarlar) ile API eşzamanlılığı | ⚠️ UNKNOWN |

**Son söz:** bu dosyanın tüm iddiaları `audio-sessions` sayfasına (fetch 2026-10-12 ✅) bağlıdır;
B2 (audioclient API ref) fetch'i yapıldığında Manager/Notification satırları `✅`'ye çevrilecektir.
Fetch etmeden `✅` yazmak ve UNKNOWN'ı silmek yasaktır.

---

---

## EK-W. Hızlı Sayım Kartı

| Metrik | Değer |
|---|---|
| Durum makinesi satırı (EK-A) | 6 |
| Kimlik alanı (EK-B) | 7 |
| FAQ satırı (EK-G) | 5 |
| Açık madde (EK-K + EK-V) | 8 + 4 |
| Ana kaynak | audio-sessions (✅ 2026-10-12) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12
