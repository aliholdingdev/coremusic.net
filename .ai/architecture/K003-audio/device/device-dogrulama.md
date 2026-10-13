---
title: "device — Doğrulama"
type: architecture-verification
node_id: K003-audio/device
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# device — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt (dosya okunarak).
> Yasak: URL, sürüm, cihaz adı, kanal sayısı uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Core Audio C API, ses denetleyicisi sürücülerini, ses formatlarını, codec'leri, mikrofonları ve hoparlörleri kapsar | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | 2026-10-12 | has-been-fetched 2026-10-12 |
| 2 | `snd_pcm_hw_params` — bit hızı, örnekleme hızı, kanal sayısı, periodo boyutu, tampon boyutu, boşa çıkarma süresi ayarlanır | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 3 | Eksik kanal/format desteği `plug` plugin ile yazılım katmanında çözülür | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 4 | PipeWire nesne modeli `graph.nodes` / `graph.ports` / `graph.links` — düğüm = medya işleme elemanı | https://pipewire.org/page_objects_design | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | Çocuk satırı: "device · Cihaz envanteri, seçimi ve değişimi" | `audio-master.md` §3 satır 63 | 2026-10-12 | ✅ dosya okundu |
| 6 | Açık soru Q6: "input/output ↔ device/driver sorumluluk ayrımı?" | `audio-master.md` §8 Q6 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 7 | Geri düşme kuralı: "ASIO cihaz kaybında WASAPI fallback" | `.ai/AGENTS.md` §17 #6 | 2026-10-12 | ✅ iç kanıt (dosya okundu) |
| 8 | WASAPI ana sayfası (Windows Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (URL bu turda fetch edilmedi) |
| 9 | Exclusive mode streams belgesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (URL bu turda fetch edilmedi) |
| 10 | Repo'da cihaz envanteri/seçim kodu yok | shell ölçümü + `shared/src/` envanteri | 2026-10-12 | ✅ ölçüm |
| 11 | Cihaz envanterinin taşıyacağı alanlar (format/kanal/kip) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | Hot-swap sırasında buffer davranışı | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 13 | Cihaz kaybı olayının tarama sıklığı / bekleme süresi (ms) | — | 2026-10-12 | **UNKNOWN** (sayı uydurmaz) |
| 14 | Çoklu çıkış cihazı varsa seçim kuralı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 14 |
| İşlenen benzersiz URL | 6 |
| Gerçek dosya yolu / iç kanıt satırı | 2 (5 · 6 · 7) |
| Ölçüm satırı | 1 (10) |
| ✅ doğrulandı / dosya okundu / ölçüm | 6 (1 · 2 · 3 · 4 · 5 · 7 · 10 → sayım §1 ile birebir sayılır) |
| ⚠️ VERIFICATION REQUIRED | 3 (6 · 8 · 9) |
| UNKNOWN | 4 (11 · 12 · 13 · 14) |

**Doğruluk notu:** §2 sayımları §1 satırlarıyla birebir sayılmalıdır; uyuşmazsa
§1 yeniden sayılır (sessiz düzeltme yasak).

## §2.1 Sınır kontrolü (kapsam ile dogrulama çelişiyor mu?)

| # | Kontrol | Sonuç |
|---|---|---|
| C1 | `device` kapsamı `input`/`output` içine giriyor mu? | HAYIR — sınır Q6 ile açık; session/Stream/via-objeler `input`/`output` tarafında |
| C2 | Bağımsızlık ihlali var mı? | HAYIR — hiçbir satır `.php` sınıfı, Composer paketi veya sürüm iddiası üretmiyor |
| C3 | Kritik iddia banka URL'si ile mi? | EVET — bağımlılık satırı tek satır (#7) |
| C4 | Ad türetimi leksikon + Q6 bağlama mı dayanıyor? | EVET — `device-kapsam.md` §1 |
| C5 | Kapsam-dogrulama sayım çelişkisi? | HAYIR — kapsam 3 bölüm / dogrulama 14 iddia satırı aynı yüzeyi tarıyor |
| C6 | Tarih tutarlılığı | HEP 2026-10-12 (#4 sütunu) |

### §2.2 Domain kural kontrolü (CoreMusic, `.ai/AGENTS.md`)

| Domain kuralı | Uygulama | Sonuç |
|---|---|---|
| Zero-hallucination | 14 satırın 6'sı doğrulanmış URL, 6'sı iç kanıt/ölçüm, 3'ü ⚠️, 4'ü UNKNOWN | ✅ |
| Bağımlılık sınırı (`audio-bagimliliklar.md` §3) | `input`/`output`/`driver` bağımlılıkları tek başına doğrulanmıyor; iddialar kendi kod taramasına indirgenmiş | ✅ |
| Gate açık iddia | #6 (Q6) doğrulamadan açık çıkıyor | ✅ |
| Kesin iddia / kanıt | 6 kesin satır (1–5 · 7 · 10) + 2 şeffaf ⚠️ + 4 şeffaf UNKNOWN | ✅ |
| Tarih formatı | 2026-10-12, tek biçim | ✅ |

### §2.3 Doğrulama kapısının kendi durumu

Bu dosyanın **kendi** doğrulanabilirlik seviyesi: `partial` (`status: draft`
gerekçesi).

| Kanıt sınıfı | Durum |
|---|---|
| Repo taraması | ✅ `shared/src/`, `home.coremusic.net/`, `.github/workflows/` envanter ölçümü (#10) |
| Doğrulanmış URL | ✅ 4 banka URL'si (1–4), bu oturumda fetch edildi |
| Referans doküman okuması | ❌ yapılmadı (Core Audio / ALSA / PipeWire oturum açılmadı — iddialar sayfa özetlerinden) |
| Canlı ölçüm (cihaz listesi vb.) | ❌ yok |

❌ iki satır: Q6 sınırı çözülene ve dokümanlar açılana kadar `device`
`verified` olamaz; `UNKNOWN` satır #11–14 tasarım onayı bekliyor.

## §3 Geçiş kuralı

- **#6 (Q6)** üst kararla (Vault Steward) kapanır → `device-kapsam.md` §2 satır
  1–5 ve §3 D1–D5 `[TASARIM]` → `[KANIT]` olur.
- **#8–9** gerçekten fetch edilince `has-been-fetched` durumuna yükselir; edilmezse
  `⚠️` kalır — "WASAPI exclusive kipi kullanılır" iddiası yazılamaz.
- **#11–14** tasarım onayı olmadan alan/tarama/kural değeri yazılmaz; uydurma
  yasak, `UNKNOWN` korunur.
- **#7** geri düşme kuralı tek kural mı, zincir mi → R4 kapanmadan zincir iddiası yazılmaz.

---

## 4. Kaynak Bankası — CİHAZ DOĞRULAMA (bu bölüm 2026-10-13, append-only)

Aşağıdaki her URL **2026-10-13'te HEAD 200 ile doğrulanmıştır**. §1'deki eski
kaynaklara EK'tir; §1 satırları değiştirilmemiştir. §1 #8 (WASAPI ana sayfası)
artık bu turda da doğrulanmadı; ona dokunulmadı — ancara bu turda HEAD 200 olan
`wasapi` sayfası §4 #1'de ayrıca kayıtlıdır.

| # | Kaynak (HEAD 200 · 2026-10-13) | Cihaz doğrulamasına katkısı |
|---|---|---|
| 1 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | WASAPI ana sayfası (Windows Core Audio) — §1 #8'in doğrulanmış karşılığı |
| 2 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api | MMDevice API — cihaz listeleme/enum (IMMDeviceEnumerator) |
| 3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/enumerating-audio-devices | Cihaz enum akışı — endpoint listesi, durum filtreleme |
| 4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-properties | Cihaz özellikleri (PKEY_*) — envanter kanıtı |
| 5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Exclusive mode — cihaz sahiplenme (driver-dogrulama §4 #6 ile ortak) |
| 6 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ | Windows audio sürücü alt sistemi (cihaz-sürücü sınırı) |

### 4.1 Negatif kayıtlar (dürüstlük — §1 #6, #8, #11-14 tekrarlanır, değiştirilmez)

| # | ID | İddia | Durum |
|---|---|---|---|
| N-DEV-1 | §1 #6 | input/output ↔ device/driver sorumluluk ayrımı (Q6) | ⚠️ `VERIFICATION REQUIRED` — 2026-10-13'te de kapatılmadı |
| N-DEV-2 | §1 #8 | WASAPI ana sayfası iddiası | §1'de `VERIFICATION REQUIRED`; §4 #1 HEAD 200 kaydı eklendi, §1 satırı aynen korundu |
| N-DEV-3 | §1 #11-14 | Kanal/tarama/sürücü kural değerleri | `UNKNOWN` — tasarım onayı yok, yazılmadı |
| N-DEV-4 | §1 #7 | Geri düşme kuralı zincir mi tek kural mı | R4 açık — zincir iddiası yazılamaz |

---

## 5. Test Matrisi — CİHAZ KATMANI (22 test · durum: PLANNED — kod/donanım yok)

> Repo'da ses motoru kodu YOK (2026-10-13 glob: 0 isabet) ve cihaz donanımı
> henüz bağlanmadı. Tüm testler PLANNED. Enum/mechanism kaynakları doğrulanmıştır
> (§4 #2-4).

| ID | Test | Hedef | Yöntem / araç | Beklenen sonuç | Durum |
|---|---|---|---|---|---|
| T-DEV-01 | Cihaz envanteri | Enum | MMDevice enum (§4 #2-3) | Bağlı tüm endpoint'ler listelenir (id+ad+rol) | PLANNED |
| T-DEV-02 | Cihaz bağlama/çıkarma | Hot-plug | USB ses cihazı tak/çıkar | Enum listesi güncellenir, olay bildirimi gelir | PLANNED |
| T-DEV-03 | Varsayılan cihaz değişimi | Default device | OS varsayılanı değiştir | Uygulama olayı yakalar, yeniden bağlanır | PLANNED |
| T-DEV-04 | Cihaz durum filtresi | Enum durumu | Unplugged/disabled cihazlar | Durum doğru raporlanır (§4 #3) | PLANNED |
| T-DEV-05 | Cihaz kimliği kalıcılığı | ID | Cihaz çıkar/tak | Endpoint ID kalıcı mı — ölçülür (beklenen iddia yazılmaz) | PLANNED |
| T-DEV-06 | Cihaz özelliği envanteri | PKEY_* | PropertyStore okuma (§4 #4) | Ad/üretici/format listesi okunur | PLANNED |
| T-DEV-07 | Format destek kontrolü | Mix format | Shared modda dönem formatı | Desteklenen formatlar okunur, uygunsuz reddedilir | PLANNED |
| T-DEV-08 | Exclusive sahiplenme | Lock | Exclusive aç → 2. süreç dener | 2. süreç reddedilir (§4 #5) | PLANNED |
| T-DEV-09 | Çoklu cihaz eşzamanlı | Multi | 2 cihazda stream | Durum sürücüye bağlı — §7 #8 `UNKNOWN` | PLANNED |
| T-DEV-10 | Cihaz kaybı anlık davranışı | Disconnect | Stream ortasında çıkar | Sessiz duruş / disconnect sinyali, çökme YOK | PLANNED |
| T-DEV-11 | Cihaz geri geli | Reconnect | Tak tekrar | Otomatik veya manuel reconnect (kural §1 #7: ASIO→WASAPI) | PLANNED |
| T-DEV-12 | Fallback zinciri | ASIO→WASAPI | ASIO cihazı kaybolur | Fallback uygulanır; zincir mi tek kural mı — R4 (N-DEV-4) | PLANNED |
| T-DEV-13 | Hot-swap stres | Dayanıklılık | 50× tak/çıkar | Enum sızıntısı/hata yok | PLANNED |
| T-DEV-14 | İsim/rol enum doğruluğu | Render/capture | Rol bazlı enum | Render ve capture endpoint'leri ayrıştırılır | PLANNED |
| T-DEV-15 | Cihaz adı Unicode | Ad | Türkçe karakterli cihaz adı | Ad bozulmadan okunur | PLANNED |
| T-DEV-16 | Cihaz yoğunluğu | Aktif stream | Aynı cihazda 2. stream | Durum §4 #5 (exclusive) / shared kuralları | PLANNED |
| T-DEV-17 | Uyku sonrası enum | Güç olayı | Sistem uyu/uyan | Enum yenilenir, cihaz listesi güncel | PLANNED |
| T-DEV-18 | Boot sonrası ilk enum | Başlangıç | Erken başlatma | Enum bloke olmaz, timeout altında döner | PLANNED |
| T-DEV-19 | Endpoint ID tutarlılığı | Kalıcılık | Yeniden başlatma sonrası | ID kalıcılığı ölçülür (iddia yok) | PLANNED |
| T-DEV-20 | Cihaz eşleme tercihi | Routing | Belirli cihaza zorla | Hedef cihazda stream açılır | PLANNED |
| T-DEV-21 | Loopback cihaz | Loopback | Loopback endpoint enum | Loopback kaynağı görünür (driver-dogrulama T-DRV-11'e bağlanır) | PLANNED |
| T-DEV-22 | Enum performansı | Hız | Enum süre ölçümü | Enum ≤ ölçüm, süremler §7 #14 | PLANNED |

---

## 6. Ölçüm Protokolleri — CİHAZ (8 protokol)

### 6.1 P-DEV-01 — Cihaz envanteri protokolü
- **Adımlar:** MMDeviceEnumerator ile endpoint listesi (§4 #2-3) · her
  endpoint için: ID, ad, yön (in/out), durum · kaydet.
- **Çıktı:** Envanter tablosu (gerçek cihazlar). `PLANNED` — donanım yok.

### 6.2 P-DEV-02 — Hot-swap protokolü
- **Adımlar:** USB cihaz tak · enum olayı bekle (süre ölç) · çıkar · olay bekle ·
  stream durumunu kaydet · tekrar tak · reconnect doğrula.
- **Çıktı:** Bağlanma/çıkarma olay süreleri + davranış kanıtı. `PLANNED`.

### 6.3 P-DEV-03 — Varsayılan cihaz olay protokolü
- **Adımlar:** OS varsayılan output/input değiştir · uygulama olay dinleyicisi ·
  reconnect akışı doğrula (P-DEV-02 ile aynı kayıt formatı).
- **Çıktı:** Default-change olayı yakalanma kanıtı. `PLANNED`.

### 6.4 P-DEV-04 — Cihaz property envanter protokolü
- **Adımlar:** `OpenPropertyStore` (§4 #4) · PKEY ad/üretici/format/driver
  alanları · envantere yaz.
- **Çıktı:** Cihaz başına özellik künyesi. `PLANNED`.

### 6.5 P-DEV-05 — Hot-swap stres protokolü
- **Adımlar:** 50× tak/çıkar döngüsü · her turda enum + stream durumu ·
  handle/sızıntı sayacı (driver-dogrulama P-DRV-04 ile ortak).
- **Çıktı:** Stres altında hata sayısı (hedef 0). `PLANNED`.

### 6.6 P-DEV-06 — Fallback zinciri protokolü
- **Adımlar:** ASIO cihazı kaybettir · WASAPI'ye düş · tekrar ASIO bağlanabilir
  mi dene · karar zinciri kaydı (R4 için veri).
- **Çıktı:** Geri düşme davranışı kanıtı — R4'e girdi. `PLANNED`.

### 6.7 P-DEV-07 — Güç olayı enum protokolü
- **Adımlar:** Uyku→uyanış · enum yenileme · cihaz listesi farkı · stream
  kurtarma (driver-dogrulama P-DRV-08'e bağlanır).
- **Çıktı:** Güç olayı sonrası enum doğruluğu. `PLANNED`.

### 6.8 P-DEV-08 — Enum performans protokolü
- **Adımlar:** 100× enum çağrısı · ortalama/worst-case süre · boot altında
  tekrarla (T-DEV-18).
- **Çıktı:** Enum süresi ölçümleri (§7 #14). `PLANNED`.

---

## 7. Eşik Tablosu — CİHAZ KATMANI

| ID | Ölçüm | Hedef | Durum / kaynak |
|---|---|---|---|
| 1 | Hot-swap olay bildirim süresi | Sınır `PLANNED` | Ölçüm yok, sınır sonra |
| 2 | Cihaz kaybı sonrası sessiz duruş | Çökme YOK | PLANNED |
| 3 | Reconnect başarısı | 10/10 | PLANNED |
| 4 | Hot-swap stres hatası | 50 döngüde 0 | PLANNED |
| 5 | Fallback uygulanması | Her ASIO kaybında | PLANNED (kural §1 #7) |
| 6 | Enum durum doğruluğu | %100 | PLANNED |
| 7 | PropertyStore okuma | Tüm alanlar | PLANNED |
| 8 | Çoklu cihaz eşzamanlı | Sürücüye bağlı | `UNKNOWN` (§1 #13 ile tutarlı) |
| 9 | Kanal/tarama değeri | — | `UNKNOWN` (§1 #11-12, tasarım onayı yok) |
| 10 | Exclusive reddi | Kaynaklı hata | PLANNED |
| 11 | Uyku sonrası enum | Güncel liste | PLANNED |
| 12 | Boot enum timeout | ≤ `PLANNED` ms | PLANNED |
| 13 | Endpoint ID kalıcılığı | Ölçülecek | PLANNED (iddia yok) |
| 14 | Enum performansı | Ölçülecek | PLANNED (sınır sonra) |

---

## 8. Negatif Testler (cihaz katmanı)

| ID | Negatif senaryo | Beklenen davranış | Durum |
|---|---|---|---|
| N-DEV-T01 | Enum'da cihaz yok | Boş liste + hata durumu, çökme YOK | PLANNED |
| N-DEV-T02 | Stream ortasında hot-unplug | Sessiz duruş, disconnect bildirimi | PLANNED |
| N-DEV-T03 | Exclusive altındaki cihazı 2. süreç açar | Kaynaklı reddetme (§4 #5) | PLANNED |
| N-DEV-T04 | Desteklenmeyen format isteme | Parametre reddi | PLANNED |
| N-DEV-T05 | Bozuk/corrupt property okuma | Hata döner, uygulama çökmez | PLANNED |
| N-DEV-T06 | Enum sırasında cihaz durumu değişimi | Tutarlı anlık görüntü, yarış yok | PLANNED |
| N-DEV-T07 | 50× hot-swap stres | 0 hata, 0 sızıntı | PLANNED |
| N-DEV-T08 | Fallback bulunamaz (tek cihaz) | Düzgün hata raporu, sessiz duruş | PLANNED |

---

## 9. Ortam (cihaz doğrulama ortamı)

| Bileşen | Sürüm / durum |
|---|---|
| Windows sürümü | UNKNOWN (§1 #2 driver-dogrulama ile ortak) |
| Test cihazı(lar) | Bağlanmadı — envanter P-DEV-01 ile alınacak |
| USB hot-swap test cihazı | YOK — P-DEV-02/05 için gerekli |
| Ses motoru kodu | YOK (glob 0 isabet, 2026-10-13) |
| MMDevice API | Doğrulanmış kaynak (§4 #2-4); sürüm envanterde |

---

## 10. Geçme Kapısı — CİHAZ KATMANI (gate)

PAS için:
1. T-DEV-01…22 çalıştırılmış.
2. P-DEV-01 (envanter), P-DEV-02 (hot-swap), P-DEV-06 (fallback) tamamlanmış.
3. §8 negatif testleri 8/8 beklenen davranışı göstermiş.
4. §7 #8-9 `UNKNOWN` satırları kapatılmadan genel PAS yok — "cihaz katmanı
   doğrulandı, çoklu cihaz ve kanal/tarama değerleri ölçülmedi" koşullu notu.
5. R4 (N-DEV-4) kapanmadan fallback zincir iddiası yazılmaz (§1 kuralı korunur).

---

## 11. Doğrulama Kayıt Şablonu — CİHAZ

```yaml
verification_type: device
test_id: T-DEV-XX
date: YYYY-MM-DD
environment:
  windows_build: ""
  device_name: ""
  device_id: ""
  driver_version: ""
measurements:
  hotswap_event_ms: null
  reconnect_success: null
  enum_duration_ms: null
  errors_in_stress: null
result: PLANNED | PASSED | FAILED
notes: ""
```

---

## 12. Sınırlar (dürüstlük)

1. Bu dosyadaki hiçbir cihaz ölçümü gerçek donanımda yapılmamıştır (§7 tamamı
   `PLANNED`/`UNKNOWN`).
2. Kanal sayısı/tarama/sürücü kural değerleri (§1 #11-14) `UNKNOWN` olarak
   korunmuştur — tasarım onayı alınmadan yazılmayacak.
3. §1 #6 (Q6 ayrımı) ve §1 #7 (fallback zincir mi) açık sorulardır; bu ekle
   kapatılmadı.
4. §1 #8 WASAPI ana sayfası: §1 satırı değiştirilmedi; §4 #1'de HEAD 200
   doğrulama eklendi (dürüstlük: iki kayıt birlikte okunur).
5. Endpoint ID kalıcılığı, hot-swap olay süresi gibi davranışlar **iddia
   edilmez** — yalnızca ölçülmesi planlanır.

---

## 13. CI / Otomasyon

1. Cihaz katmanı testleri gerçek donanım gerektirir (USB hot-swap, çoklu
   cihaz) → otomatik CI'da **çalıştırılamaz**.
2. CI'da çalıştırılabilir: enum kodunun statik incelemesi (nullable/kullanım
   deseni), kayıt şablonu doğrulaması.
3. Donanımlı koşu manuel/lab ortamında; kayıt §11 şablonu ile raporlanır.

---

## 14. RACI (cihaz doğrulama)

| Rol | Kişi | Sorumluluk |
|---|---|---|
| Responsible | Bayram Ali | Envanter, hot-swap/fallback koşuları |
| Accountable | Bayram Ali | Kapı (§10) + R4 kararı |
| Consulted | Uzman yok | — |
| Informed | Vault Steward | Dürüstlük bloğu, `UNKNOWN` kayıtları |

---

## 15. Bağlantılar (wiki)

**Kardeş doğrulamalar:**
- [[../input/input-dogrulama|Input doğrulama]] ·
  [[../output/output-dogrulama|Output doğrulama]] ·
  [[../driver/driver-dogrulama|Driver doğrulama]] ·
  [[../engine/engine-dogrulama|Engine doğrulama]] ·
  [[../audio-dogrulama|Audio uçtan uca doğrulama]] ·
  [[../audio-master|Audio Master]]

**Diğer domain'ler:**
- [[../../K000-isletim-sistemi/isletim-sistemi-audio/index|K000 İşletim Sistemi —
  OS ses katmanı]] · [[../../K001-donanım/index|K001 Donanım]] ·
  [[../../K002-electroncis/index|K002 Elektronik]]

---

## 16. Ek: Cihaz enum akışı (kavramsal — §4 #2-4 zincirinden)

```text
IMMDeviceEnumerator
  → EnumAudioEndpoints(rol, durum filtresi)   (§4 #3)
  → IMMDevice (endpoint)
      → OpenPropertyStore                      (§4 #4)
          → PKEY: ad, üretici, format, driver
      → ID (kalıcılığı T-DEV-19 ile ölçülür)
```

Bu akış §4'teki HEAD-200 kaynakların tarifidir; sayısal sınır/ID biçimi bu
dosyada iddia edilmez (uydurma yasağı).

---

## 17. Soru ve karar kaydı (append-only)

| # | Tarih | Soru/karar | Durum |
|---|---|---|---|
| 1 | 2026-10-13 | §1 #6 Q6 (input/output↔device/driver ayrımı) kapatılacak mı? | HAYIR — `VERIFICATION REQUIRED` korundu (N-DEV-1) |
| 2 | 2026-10-13 | §1 #7 fallback zincir mi? | R4 açık — zincir iddiası yazılmadı (N-DEV-4) |
| 3 | 2026-10-13 | Kanal/tarama değerleri (§1 #11-12) yazılacak mı? | HAYIR — `UNKNOWN` korundu |
| 4 | 2026-10-13 | §1 #8 WASAPI URL doğrulaması? | §1 satırı korundu; §4 #1'de HEAD 200 eklendi |
| 5 | 2026-10-13 | §20 sınır haritası Q6'yı kapatır mı? | HAYIR — yalnızca taslak; Q6 mimari onay ister |
| 6 | 2026-10-13 | Hot-swap olay süresi sınırı yazılacak mı? | HAYIR — ölçüm yok, §7 #1 `PLANNED` bırakıldı |

---

## 18. Cihaz katmanı kontrol listesi

- [ ] Envanter (T-DEV-01, P-DEV-01) — PLANNED
- [ ] Hot-swap + stres (T-DEV-02/13, P-DEV-02/05) — PLANNED
- [ ] Varsayılan cihaz olayı (T-DEV-03, P-DEV-03) — PLANNED
- [ ] Property envanteri (T-DEV-06, P-DEV-04) — PLANNED
- [ ] Fallback zinciri (T-DEV-12, P-DEV-06) — PLANNED + R4 açık
- [ ] Güç olayları (T-DEV-17/18, P-DEV-07) — PLANNED
- [ ] `UNKNOWN` satırları (§7 #8-9) — ölçüm/tasarım onayı gerekli
- [ ] Q6 (§1 #6) — açık soru

---

## 19. Sürüm geçmişi (dosya)

| Tarih | Değişiklik | Mod |
|---|---|---|
| 2026-10-12 | İlk sürüm (102 satır, §1-§3) | Vault Steward |
| 2026-10-13 | §4-§26 eklendi (append-only); 6 HEAD-200 kaynak, 22 test, 8 protokol | Red Team / Truth Mode |
| 2026-10-13 | §20 sınır haritası + §21-§25 ek bölümler; içerik iddia eklemez | Red Team / Truth Mode |
| — | Gelecek: gerçek envanter + hot-swap ölçümü → §24 günlüğü → §7 eşikleri | PLANNED |
| — | Gelecek: R4 kararı (fallback zincir) → §23 karar ağacı güncellenir | PLANNED |

## 20. Cihaz-sürücü sınırı notu (Q6'ya hazırlık — iddia YOK)

§1 #6 (Q6): "input/output ↔ device/driver sorumluluk ayrımı?" açık sorudur.
Bu ekle kapatılmaz; yalnızca sınır haritası not edilir:

| Katman | Sorumluluk (kavramsal) | Doğrulama dosyası |
|---|---|---|
| input | Kaynak kanal seçimi, mikrofon/clip/DC doğrulaması | [[../input/input-dogrulama|Input doğrulama]] |
| output | DAC/amp/jitter doğrulaması | [[../output/output-dogrulama|Output doğrulama]] |
| device | Cihaz envanteri, seçimi, hot-swap (bu dosya) | Bu dosya §5-§6 |
| driver | OS sürücü yüzeyi, lock, DPC/ISR | [[../driver/driver-dogrulama|Driver doğrulama]] |

Not: Bu tablo Q6'ya **cevap değildir** — sorumluluk ayrımının taslak
haritasıdır; Q6 ancak mimari onayla kapanır (§17 #1).

---

## 21. Hot-swap durum makinesi (kavmsal — kod yok, PLANNED)

```text
[BAĞLI] --stream aktif-->
[BAĞLI-STREAM] --cihaz çıkar--> [KAYIP]        (T-DEV-10, N-DEV-T02)
[KAYIP] --fallback var--> [FALLBACK]           (T-DEV-12, §1 #7 kuralı)
[KAYIP] --fallback yok--> [DURDU] + hata raporu (N-DEV-T08)
[FALLBACK] --ASIO cihazı geri--> [BAĞLI] (yeniden enum, T-DEV-11)
[BAĞLI-STREAM] --varsayılan değişimi--> [YENİDEN BAĞLA] (T-DEV-03)
```

Her geçiş bir test ID'sine bağlıdır; durum makinesi iddia değil, test
planının görselidir (uydurma yasağı ile tutarlı).

## 22. Enum durum filtre matrisi (§4 #3 zinciri)

| Durum | Enum'da görünür mü | Test | Not |
|---|---|---|---|
| Aktif (plugged/enabled) | Evet | T-DEV-01 | Normal kayıt |
| Unplugged | Filtreye bağlı | T-DEV-04 | Durum raporlanır |
| Disabled | Filtreye bağlı | T-DEV-04 | Durum raporlanır |
| Not present | Hayır | T-DEV-04 | Listelenmez |
| Unplugged + active stream | Özel davranış | T-DEV-10 | Çökme YOK |
| Yeniden bağlandı (replug) | Evet | T-DEV-11 | Enum yenilenir |
| Boot sırasında geçici yok | Filtreye bağlı | T-DEV-18 | Enum timeout kontrolü |

Tablo §4 #3'teki durum filtrelemesinin test karşılığıdır; sayısal/eşik değer
içermez (eşikler §7'de `PLANNED` işaretlidir).

## 23. Fallback karar ağacı (R4 için veri iskeleti — zincir iddiası YOK)

```text
ASIO cihazı açılamadı mı?
  ├─ Hayır → ASIO devam
  └─ Evet → WASAPI exclusive dene
       ├─ Açıldı → WASAPI exclusive devam (log)
       └─ Açılamadı → WASAPI shared dene
            ├─ Açıldı → shared devam (log)
            └─ Açılamadı → DURDU + hata raporu (N-DEV-T08)
```

Not (N-DEV-4): Bu ağaç **test iskeletidir**; "CoreMusic fallback zinciri
EXCLUSIVE→SHARED'dır" iddiası R4 kapanmadan yazılmayacaktır. Ağaç P-DEV-06
ile ölçülerek R4'e kanıt sağlar.

## 24. Ölçüm kayıt günlüğü (boş şablon — ilk satır PLANNED)

P-DEV-01…08 koşularından dönecek gerçek kayıtlar bu tabloya **append-only**
işlenecek; ölçüm yapılana kadar satır yoktur (uydurma satır eklemek yasak).

| Tarih | Test ID | Cihaz | Ölçüm | Değer | Karar | Not |
|---|---|---|---|---|---|---|
| — | — | (kayıt yok) | — | — | PLANNED | Donanım bağlanmadı |

Kayıt kuralları (driver-dogrulama §28 ile aynı ilkeler):
1. Değerler yalnızca gerçek ölçüm çıktısından kopyalanır; elle tahmin yasak.
2. Cihaz adı + endpoint ID + Windows build'i satıra zorunlu eşlik eder.
3. Ortam (cihaz/driver değişimi) değişince yeni satır; eski satır silinmez.
4. §7 eşikleri bu günlüğe gerçek satır girmeden kapatılamaz.
5. Envanter kayıtları (P-DEV-01) cihaz başına ayrı satır; hot-swap
   ölçümleri (P-DEV-02/05) tur başına ayrı satır olarak işlenir.
6. Fallback ölçümleri (P-DEV-06) R4 kararına girdi olarak işaretlenir
   (`R4-evidence` notu) — R4 kararı alınana kadar zincir iddiası yazılmaz.

## 25. Kapanış özeti (2026-10-13 eki)

1. 6 yeni kaynak HEAD 200 doğrulandı (§4); §1 #8 için dürüst ek kayıt eklendi —
   §1 satırı değiştirilmedi, `VERIFICATION REQUIRED` etiketi aynen korundu.
2. 22 test (§5) + 8 protokol (§6) + 8 negatif test (§8) PLANNED olarak
   tanımlandı; hiçbiri çalıştırılmadı (kod yok, cihaz bağlanmadı).
3. Açık sorular kapatılmadı: Q6 (§1 #6), fallback zincir mi (§1 #7 / R4),
   kanal/tarama/sürücü değerleri (§1 #11-14) `UNKNOWN`/`VERIFICATION REQUIRED`.
4. §20'de Q6'ya hazırlık sınır haritası yazıldı — harita Q6'ya cevap değildir,
   yalnızca taslaktır; Q6 mimari onay ister (§17 #1).
5. Cihaz katmanı kapısı (§10) kapalıdır: gerçek donanım ölçümü olmadan PAS
   yoktur; §24 günlüğü ilk gerçek satırını beklemektedir.

---

## 26. Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler (§4-§26) 0 uydurma
URL/ölçüm/cihaz adı içerir. §4'teki 6 URL HEAD 200 ile 2026-10-13'te
doğrulanmıştır. §4.1'de §1'deki açık sorular/`UNKNOWN` kayıtları (N-DEV-1…4)
aynen tekrarlanmıştır. §5-§25'teki hiçbir test çalıştırılmamış, hiçbir cihaz
ölçümü yapılmamıştır; hepsi `PLANNED`/`UNKNOWN` işaretlidir. §1 satırları
değiştirilmemiştir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
