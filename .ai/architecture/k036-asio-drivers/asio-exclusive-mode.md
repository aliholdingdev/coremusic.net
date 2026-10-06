---
title: "K036.1 — ASIO Exclusive Mode: Tek Sahiplik Kilidi ve Cihaz Mülkiyeti"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K036.1 — ASIO Exclusive Mode

**Bağlantılar:** [[index]] · `asio-buffer-callback` · [[../k037-wasapi-exclusive/wasapi-exclusive-shared]] · [[../k041-driver-stack-mimari/hal-abstraction]]

---

## §1 Genel Bakış

ASIO exclusive mode, bir ses cihazının **tek bir uygulama tarafından** doğrudan ve dış müdahale olmadan kullanılmasıdır. Windows'un paylaşımlı (shared) ses yolundaki birleşik miksaj, ses efekt zinciri ve volume arbitrajı bu yolda **devre dışıdır**: uygulama donanımın tam kontrolünü alır.

Bu doküman şu üç soruyu yanıtlar:

1. Mülkiyet (ownership) nasıl kazanılır ve kaybedilir?
2. İkinci bir uygulama talebi nasıl reddedilir, kullanıcıya nasıl bildirilir?
3. Cihaz aniden kaybolursa (unplug, driver reset) sistem nasıl kurtulur?

> ⚠️ **VERIFICATION REQUIRED:** Bu dokümandaki kilit davranışları `k2-surucu/asio-drivers.md` içindeki "ASIO Exclusive Mode" bölümünden (L24–L40) türetilmiştir; **kod implementasyonu repo'da bulunamadığı için** API çağrı adları örnektir, birebir imzalar değildir.

---

## §2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Cihaz mülkiyeti kazanma/kaybetme | Paylaşımlı mod davranışı → `../k037-wasapi-exclusive` |
| Tek uygulama kilidi ve yeniden deneme | Session/metering → `../k037-wasapi-exclusive/wasapi-audiosession` |
| Cihaz kaybı (device-loss) kurtarma | Sürücü lifecycle → `../k041-driver-stack-mimari/driver-stack-layers` |
| Exclusive↔Shared geçiş kararı | Genel hata sınıflandırması → `../k043-latency-optimization/latency-chain-budget` |

---

## §3 Exclusive vs Shared Karşılaştırması

Kaynak tablo (`wasapi-exclusive.md L53-L59`) Windows tarafı için verilmiştir; ASIO benzer şekilde **tek sahipli** bir yoldur.

| Özellik | Exclusive (ASIO / WASAPI Exclusive) | Shared (WASAPI Shared) |
|---------|--------------------------------------|-------------------------|
| Latency | 1–3ms | 10–40ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |
| Multi-app | Hayır | Evet |
| DSP kaynağı | Donanım | Windows |
| Sahiplik | Tek uygulama | Birden çok uygulama |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md L53-L59`

> Not: ASIO'nun kendi karşılaştırma satırları `asio-drivers.md L51-L60` civarında "Exclusive Mode Avantajları" başlığı altında verilir; sayısal değerler L156–L163 tablosundadır.

---

## §4 Mülkiyet Durum Makinesi

```
        ┌─────────────┐
        │  IDLE       │  cihaz serbest, kimse sahip değil
        └──────┬──────┘
               │ open(device) + exclusive flag
               ▼
        ┌─────────────┐   kilit reddedildi    ┌──────────────┐
        │  ACQUIRING  │ ────────────────────▶ │  BUSY        │
        └──────┬──────┘                       │ (diğer süreç)│
               │ başarı                       └──────────────┘
               ▼
        ┌─────────────┐   cihaz kaybı          ┌──────────────┐
        │  OWNED      │ ─────────────────────▶ │  LOST        │
        └──────┬──────┘                        └──────┬───────┘
               │ close()/hata                         │ yeniden deneme (max 3)
               ▼                                      ▼
        ┌─────────────┐                        ┌──────────────┐
        │  IDLE       │ ◀───────────────────── │  RECOVERING  │
        └─────────────┘   başarılı reopen      └──────────────┘
```

### §4.1 Durum geçiş tablosu

| # | Geçiş | Tetikleyen | Başarı koşulu | Hata yolu |
|---|-------|-----------|---------------|-----------|
| 1 | IDLE → ACQUIRING | Uygulama cihazı açar | Handle alındı | BUSY (diğer sahip) |
| 2 | ACQUIRING → OWNED | Exclusive flag kabul edildi | Cihaz exclusively açık | IDLE (retry) |
| 3 | OWNED → LOST | Unplug / driver reset | — | RECOVERING |
| 4 | RECOVERING → OWNED | Cihaz yeniden enumerate | Handle yenilendi | IDLE (vazgeç) |
| 5 | OWNED → IDLE | Kapanış / flush | Tüm buffer boşaltıldı | LOST |

---

## §5 Adım Adım: Kilidi Kazanma

1. **Cihaz listesini tara** — mevcut çıkış cihazlarını ve desteklenen SR/buffer kombinasyonlarını topla.
2. **Tek sahiplik ön kontrolü** — yerel kilidi (dosya kilidi / mutex) dene; başarısızsa BUSY durumuna geç ve kullanıcıya "diğer uygulama kullanıyor" mesajı ver.
3. **Cihazı exclusive bayrağıyla aç** — paylaşımlı miksajı atlayacak bayrakla aç; başarısızsa hata kodunu kaydet.
4. **Formatı sabitle** — örnekleme hızı, bit derinliği, kanal sayısı; uyuşmazlık varsa **açı reddet** (aksi halde pitch shift — guardrail #4).
5. **Buffer'ı hazırla** → `asio-buffer-callback` §5'teki çift buffer kurası.
6. **Bellek kilidini uygula** — buffer sayfalarını sayfa-fault'a karşı kilitle (kaynak: latency-optimization "Memory Lock 100%" hedefi).
7. **Callback'i kaydet** — sürücüye geri çağrım adresini ver.
8. **Akışı başlat** — veri akışı başlar; OWNED durumuna geç.
9. **Sağlık izleme başlat** — xrun sayacı, callback süresi, cihaz varlık sinyali.
10. **Kapanışta ters sıra** — akışı durdur → callback kaldır → buffer serbest → cihaz kapat → yerel kilidi bırak.

### §5.1 Adım tablosu (girdi/çıktı)

| Adım | Girdi | Çıktı | Hata durumunda |
|------|-------|-------|----------------|
| 1 | Cihaz listesi | Enumerate sonucu | Boş liste → cihaz yok uyarısı |
| 2 | Yerel lock | bool | false → BUSY, kullanıcı bilgilendirilir |
| 3 | device handle | exclusive açık | hata kodu → retry ≤3 |
| 4 | SR/bit/ch | sabit format | uyuşmazlık → ACQUIRING iptal |
| 5 | buffer boyutu | çift buffer | tahsis hatası → IDLE |
| 6 | buffer belleği | kilitli sayfa | kilitleme hatası → devam et + uyarı |
| 7 | callback fn | kayıtlı | — |
| 8 | — | akış aktif | hata → RECOVERING |
| 9 | — | izleme aktif | — |
| 10 | — | temiz kapanış | flush hatası → yine de close |

---

## §6 Kilidi Kaybetme Senaryoları

| # | Senaryo | Belirti | Sonuç (kural) |
|---|---------|---------|----------------|
| 1 | İkinci uygulama aynı cihazı dener | Reddedilen open | Kullanıcıya net hata; **sürücü çökmez** |
| 2 | Sahip uygulama zorla kapatılır (taskkill) | Beklenmedik close | OS handle'ı serbest bırakır → IDLE |
| 3 | USB cihaz fiziksel çıkarılır | Device-lost | LOST → RECOVERING |
| 4 | Sürücü firmware reseti | Ani sessizlik | LOST → RECOVERING |
| 5 | Uyku/uyanma (suspend/resume) | Handle geçersiz | LOST → RECOVERING |
| 6 | Kilit sahibi süreç askıda kalır (frozen) | Timeout | Yerel lock timeout → force-release |

> ⚠️ **VERIFICATION REQUIRED:** 3, 4 ve 5. senaryoların CoreMusic kodunda nasıl ele alındığı repo'da doğrulanamadı; davranış **tasarım kuralı** olarak yazıldı.

### §6.1 Lock kaybı → hata kodu eşlemesi ve zorunlu fallback (backup tabanlı)

Kaynaklar: `driver-stack-mimari.md` L116–L133, L239–L246 · `asio-drivers.md` L105–L113 ·
`.ai/AGENTS.md` §17 Edge Case #6.

| §6 senaryo | Eşlenen kod | Otomatik tepki | Kaçış yolu |
|---|---|---|---|
| 1 — İkinci uygulama reddi | `ERR_PERMISSION_DENIED (1004)` | Reddedilen open; **lock sahibi değişmez** | Kullanıcıya BUSY mesajı |
| 2 — Sahip process öldürüldü | OS handle serbest → `ERR_DEVICE_NOT_FOUND (1001)` benzeri yeniden enumeration | IDLE'a dön | Yeniden açma denemesi (≤3, `asio-drivers.md` L112) |
| 3/4/5 — Cihaz/firmware/suspend kaybı | `ERR_HARDWARE_FAILURE (1005)` sınıfı | LOST → RECOVERING | Retry başarısız → **WASAPI fallback (Edge Case #6)** |
| 6 — Frozen sahip | `ERR_TIMEOUT (1006)` | Yerel lock timeout → force-release | Retry (backup L255–L258: timeout retry'lı) |
| Exclusive mode reddi (çalışma anı) | `ASIOError_InvalidMode` | **Shared mode'a geç** (`asio-drivers.md` L109) | [[../k037-wasapi-exclusive/wasapi-exclusive-shared]] |

```
Lock kaybı akışı:
  belirti → ErrorChain::reportError(K2_DRIVER, ERR_*, ...)   (driver-stack L218-L236)
     → notifyUpperLayer → K3 çökmez                          (k2 index.md L52-L53)
     → retry (ERR_TIMEOUT / ERR_BUFFER_OVERFLOW)             (driver-stack L255-L258)
     → başarısız → createAutoDetect: WASAPI fallback         (driver-stack L120-L123)
```

⚠️ VERIFICATION REQUIRED: `ERR_*` ↔ `ASIOError_*` eşlemesi bu tabloda **yorumlanmıştır**;
backup iki ayrı dosyada bağımsız tanımlar verir, birebir eşleme yoktur (bkz.
[[asio-hata-yonetimi]] §3.1).

---

## §7 Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Öncelik |
|---|------------|-------------------|---------|
| 1 | Aynı süreç içinde iki kez open | İkinci open hata döner (idempotent değil) | HIGH |
| 2 | Cihaz adı değişir (yeniden enumeration) | handle değil, UID ile yeniden bağlan | HIGH |
| 3 | Exclusive açıldı ama format desteklenmiyor | Kapat + format listesini sun | MEDIUM |
| 4 | Kilit 30 sn'den uzun tutulur | Uyarı log + sağlık alarmı | MEDIUM |
| 5 | Kapanış sırasında callback hâlâ çalışıyor | Önce akışı durdur, sonra callback kaldır | CRITICAL |
| 6 | Aynı cihazın giriş/çıkış çifti ayrı açılarsa | Çiftte de exclusive sağlanmalı | HIGH |

---

## §8 Hata Modları

| Hata | Belirti | Kök neden | Düzeltme |
|------|---------|-----------|----------|
| BUSY / cihaz kullanımda | Açılış reddi | Başka süreç sahip | Kullanıcıya bildir, shared yola düş |
| Exclusive reddi | yetki/bayrak | Sürücü exclusive desteklemiyor | K2 sürücü listesinden düş → [[../k041-driver-stack-mimari/hal-abstraction]] |
| Format uyuşmazlığı | Pitch shift riski | SR/bit farkı | Açılışta sabitle, reddet |
| Device-lost | Ses kesilmesi | Unplug/reset | RECOVERING döngüsü |
| Flush hatası | Kapanışta pop sesi | Buffer boşaltılmadı | Ters sıra zorla |
| Lock timeout | Kilidi kırık | Sahip süreç öldü | Force-release + log |

---

## §9 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| K2 ASIO SDK arayüzü | Alt | Steinberg ASIO SDK (kaynakta "v2.3+" — kanıt `asio-drivers.md L169`) |
| K1 Windows Core servisleri | Alt | Thread/event/bellek → `k0-isletim-sistemi/windows-core.md` |
| K2 HAL abstraction | Alt | Cihaz erişim soyutlaması |
| K2 buffer yönetimi | Alt | Kilidin altındaki buffer → [[../k042-buffer-management/index]] |
| K3 ses motoru | Üst | Kilidi tutan uygulama |
| WASAPI fallback | Yatay | Cihaz kaybında yol → [[../k037-wasapi-exclusive/wasapi-exclusive-shared]] |

---

## §10 Doğrulama / Test Adımları

1. Tek uygulama cihazı exclusive açar → başarılı, akış başlar.
2. İkinci süreç aynı cihazı dener → **reddedilir**, birinci çalışır durumda kalır.
3. Birinci süreç kapanır → kilit serbest, ikinci süreç açabilir.
4. Cihaz kablosunu çek → device-lost sinyali, uygulama çökmez.
5. Kabloyu geri tak → cihaz yeniden enumerate, otomatik yeniden bağlan (veya açıkça yeniden dene).
6. SR'yi desteklenmeyen bir değere zorla → açılış reddedilir, hata kodu loglanır.
7. Kapanış sırasında buffer flush edilir → pop sesi oluşmaz.
8. Uyku/uyanma sonrası handle geçersiz → RECOVERING akışı çalışır.

**Test tablosu**

| Test | Girdi | Beklenen | Kriter |
|------|-------|----------|--------|
| T1 | 1 süreç open | başarılı | akış aktif |
| T2 | 2. süreç open | başarısız | birinci etkilenmez |
| T3 | kill -9 sahip | kilit serbest | 2. süreç açabilir |
| T4 | unplug | LOST | çökme yok |
| T5 | replug | RECOVERING→OWNED | ses geri gelir |
| T6 | yanlış SR | open reddi | pitch shift yok |

---

## §11 Performans / Davranış Metrikleri

| Metrik | Hedef | Kaynak |
|--------|-------|--------|
| Kilidi kazanma süresi | ⚠️ VERIFICATION REQUIRED | ölçüm yok |
| Cihaz kurtarma süresi (RECOVERING→OWNED) | ⚠️ VERIFICATION REQUIRED | ölçüm yok |
| Round-trip latency (exclusive yolu) | 1.34ms hedef / 1.33ms gerçek | `asio-drivers.md L160` |
| Buffer değişim süresi | <10µs hedef / 8µs gerçek | `asio-drivers.md L163` |
| CPU (boşta) | <%1 hedef / %0.3 | `asio-drivers.md L161` |

---

## §12 Kanıt Satırları

| # | İddia | Kanıt yolu | Satır |
|---|-------|-----------|-------|
| 1 | ASIO Exclusive Mode bölümü mevcut | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L24–L40 |
| 2 | Exclusive/Shared karşılaştırma (WASAPI) | `_backup/.../k2-surucu/wasapi-exclusive.md` | L53–L59 |
| 3 | ASIO SDK 2.3+ bağımlılığı | `_backup/.../k2-surucu/asio-drivers.md` | L167–L171 |
| 4 | "ASIO Exclusive Lock → tek uygulama" guardrail | `_backup/.../k2-surucu/CLAUDE.md` | L18–L24 |
| 5 | Windows Core servis listesi (thread/memory/event) | `_backup/.../k0-isletim-sistemi/windows-core.md` | L18–L331 |
| 6 | `enableExclusiveMode()` / `isExclusiveModeActive()` taslağı | `_backup/.../k2-surucu/asio-drivers.md` | L117–L158 |
| 7 | Fabrika: ASIO tercihli → WASAPI fallback | `_backup/.../k2-surucu/driver-stack-mimari.md` | L116–L133 |
| 8 | Durum makinesi geçiş izinleri (CREATED→DESTROYED) | aynı dosya | L148–L210 |
| 9 | `ERR_PERMISSION_DENIED = 1004` (lock ihlali kodu) | aynı dosya | L239–L246 |
| 10 | Senkronizasyon primitif yasağı (Critical Section/SRW ❌) | `_backup/.../k0-isletim-sistemi/windows-api.md` | L175–L185 |
| 11 | ASIO buffer konfigürasyonu (512/48000) | aynı dosya | L79–L87 |

### §12.1 Backup Kanıt Özeti (backup-first ekleme)

```
backup kanıtı: k2-surucu/asio-drivers.md           L24-L40   (Exclusive Mode bölümü — birincil)
backup kanıtı: k2-surucu/asio-drivers.md           L105-L113  (ASIOError_InvalidMode → Shared geçiş)
backup kanıtı: k2-surucu/asio-drivers.md           L117-L158  (ASIODriverManager exclusive taslağı)
backup kanıtı: k2-surucu/wasapi-exclusive.md        L40-L69    (WASAPI Exclusive/Shared karşılaştırma)
backup kanıtı: k2-surucu/driver-stack-mimari.md     L116-L133  (ASIO→WASAPI fallback factory)
backup kanıtı: k2-surucu/driver-stack-mimari.md     L148-L210  (durum makinesi geçiş izinleri)
backup kanıtı: k2-surucu/driver-stack-mimari.md     L239-L246  (ERR_* kodları — 1004 lock ihlali)
backup kanıtı: k2-surucu/CLAUDE.md                  L18-L24    (Exclusive Lock guardrail — tek uygulama)
backup kanıtı: k0-isletim-sistemi/windows-api.md    L175-L185  (senkronizasyon primitif yasağı)
backup kanıtı: k0-isletim-sistemi/windows-api.md    L79-L87    (buffer konfigürasyonu)
kanıt: .ai/AGENTS.md §17 Edge Case #6 (lock/CIHAZ kaybı → WASAPI fallback)
kanıt: .ai/ecosystem/asio-wasapi-rehber.md §5.1, §5.4
```

---

## §13 Wiki-Bağlantılar

| Hedef | Bağlantı |
|-------|----------|
| K036 indeksi | [[index]] |
| Buffer & callback | [[asio-buffer-callback]] |
| Latency hesabı | [[asio-latency-hesap]] |
| Cihaz yaşam döngüsü | [[asio-device-lifecycle]] |
| Thread modeli | [[asio-thread-model]] |
| SDK entegrasyonu | [[asio-sdk-entegrasyon]] |
| Hata yönetimi | [[asio-hata-yonetimi]] |
| WASAPI exclusive/shared | [[../k037-wasapi-exclusive/wasapi-exclusive-shared]] |
| HAL soyutlama | [[../k041-driver-stack-mimari/hal-abstraction]] |
| Driver stack katmanları | [[../k041-driver-stack-mimari/driver-stack-layers]] |
| Buffer yönetimi | [[../k042-buffer-management/index]] |
| Latency bütçesi | [[../k043-latency-optimization/latency-chain-budget]] |
| Kernel API | [[../k047-kernel-audio-api/index]] |
| Hotplug akışı | [[../k050-device-hotplug-power/hotplug-detect-flow]] |

---

## §14 Kontrol Listesi (Kilidin doğrulanması)

- [ ] Tek sahiplik yerel lock ile korunuyor
- [ ] Exclusive bayrağı set edilmeden akış başlamıyor
- [ ] SR/bit sabitleme reddi uygulanmış
- [ ] device-loss → RECOVERING yolu kodda var
- [ ] Kapanışta ters sıra (stop → detach → flush → close → unlock)
- [ ] BUSY durumu kullanıcıya anlaşılır iletiliyor
- [ ] shared fallback test edilmiş → [[../k037-wasapi-exclusive/wasapi-exclusive-shared]]
- [ ] Log'larda `[REDACTED]` kuralı ihlal edilmiyor

---

## §15 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | Kilidin hangi OS nesnesiyle kurulduğu | ⚠️ VERIFICATION REQUIRED |
| 2 | RECOVERING için retry sayacı değeri | ⚠️ VERIFICATION REQUIRED (tasarım önerisi: 3) |
| 3 | Çoklu çıkış (çift cihaz) kilidi | ⚠️ VERIFICATION REQUIRED |

---

## §16 Senaryo Yürütmeleri (Step-by-Step)

### §16.1 Senaryo A — Tek uygulama exclusive açılışı

1. Uygulama başlar, cihaz listesi okunur (çıkış cihazı + desteklenen SR/buffer).
2. Yerel tek-sahiplik lock denenir → serbest.
3. Cihaz, exclusive bayrağıyla açılır → handle alınır.
4. Format sabitlenir: SR/bit/kanal üçlüsü cihaz yeteneğiyle eşleşir.
5. Çift buffer tahsis edilir (bkz. `asio-buffer-callback` §5).
6. Buffer sayfaları kilitleme işlemine tabi tutulur (sayfa-fault önlenir).
7. Callback adresi sürücüye kaydedilir.
8. Akış başlatılır; durum OWNED.
9. İlk iki callback içinde cihaz FIFO'su doldurulur (underrun önlenir).
10. İzleme başlar: callback süresi, xrun sayacı, cihaz varlık bayrağı.
11. Uygulama normal çalışır; kilidin sahibi tek süreçtir.
12. Kapanışta ters sıra uygulanır: stop → detach → flush → close → unlock.

### §16.2 Senaryo B — İkinci uygulama çakışması

1. İkinci uygulama aynı cihazı açmak ister.
2. Yerel lock zaten tutulur → reddedilir.
3. İkinci uygulama, cihazı "kullanımda" olarak raporlar.
4. Kullanıcıya alternatif yol sunulur: WASAPI shared → [[../k037-wasapi-exclusive/wasapi-exclusive-shared]].
5. Birinci uygulama **etkilenmez**, akış sürer.
6. Olay log'a yazılır (maskeleme kuralı uygulanır).

### §16.3 Senaryo C — Cihaz aniden çıkarıldı (USB unplug)

1. Sürücü, cihaz-varlık sinyalini kaybeder.
2. Callback zinciri hatalı döner veya durur.
3. Durum OWNED → LOST geçer.
4. Uygulama ses yolunu kapatır, arayüz "cihaz kayboldu" durumuna geçer.
5. RECOVERING başlatılır (yeniden enumerate dene, maks. 3 deneme — tasarım önerisi).
6. Cihaz geri takılırsa handle yenilenir → OWNED.
7. Denemeler başarısızsa IDLE'e düşülür ve kullanıcı bilgilendirilir.

### §16.4 Senaryo D — Sahip süreç zorla sonlandırıldı

1. Sahip süreç taskkill ile öldürülür.
2. OS, sürücü handle'ını süreç sonu temizliğinde bırakır.
3. Yerel lock dosya/nesne kilidi ise OS tarafından bırakılır.
4. Cihaz IDLE durumuna döner.
5. Kalan bellek kilidi (page lock) süreç belleğiyle beraber çözülür.
6. Yeni süreç kilidi alabilir → akış yeniden kurulur.

### §16.5 Senaryo E — Örnekleme hızı uyuşmazlığı

1. Uygulama 96kHz ister, cihaz yalnız 48kHz destekler.
2. Format doğrulaması başarısız olur.
3. Açılış **reddedilir** (guardrail #4: sample-rate mismatch önlenir).
4. Uygulama desteklenen SR listesini gösterir.
5. Kullanıcı 48kHz seçerse açılış başarılı olur.
6. Red nedeni ve desteklenen liste loglanır.

### §16.6 Senaryo F — Uyku/uyanma (suspend/resume)

1. Sistem uykuya geçer, ses cihazı askıya alınır.
2. Uyanışta handle geçersiz olabilir.
3. Durum LOST → RECOVERING olarak işlenir.
4. Cihaz yeniden enumerate edilir.
5. Buffer ve callback yeniden kurulur.
6. Akış devam eder; başarısızlıkta kullanıcıya yeniden bağlanma seçeneği sunulur.

> ⚠️ **VERIFICATION REQUIRED:** Senaryo D, E ve F için repo'da otomatik test bulunamadı; adımlar tasarım kuralıdır.

---

## §17 Test Matrisi

| Test ID | Senaryo | Girdi | Beklenen sonuç | Öncelik |
|---------|---------|-------|----------------|---------|
| EX-01 | Normal açılış | Cihaz serbest | OWNED, akış aktif | P0 |
| EX-02 | Çakışma | İki süreç | İkincisi reddedilir | P0 |
| EX-03 | Kilit bırakma | Sahip kapanır | IDLE, ikincisi açabilir | P0 |
| EX-04 | Zorla sonlandır | taskkill sahip | Kilit serbest | P0 |
| EX-05 | Unplug | Kablosu çek | LOST, çökme yok | P0 |
| EX-06 | Replug | Kablo geri | RECOVERING → OWNED | P0 |
| EX-07 | SR uyuşmazlığı | 96kHz/48kHz | Açılış reddi | P0 |
| EX-08 | Bit uyuşmazlığı | 32-bit isteniyor | Açılış reddi | P1 |
| EX-09 | Kapanış flush | Akış aktifken close | Pop sesi yok | P1 |
| EX-10 | Kilit timeout | Sahip donmuş | Force-release + log | P1 |
| EX-11 | Çift cihaz | İki çıkış | Her ikisi exclusive | P1 |
| EX-12 | Uyanma | Suspend sonrası | Yeniden bağlanma | P1 |
| EX-13 | Callback overrun | Yapay gecikme | xrun sayacı artar | P1 |
| EX-14 | Bellek kilidi | Sayfa kilidi | Kilitsiz sayfa yok | P1 |
| EX-15 | Fallback | Exclusive başarısız | Shared yola düş | P2 |
| EX-16 | Log maskeleme | Hata akışı | `[REDACTED]` kuralı | P2 |
| EX-17 | Sürücü reset | Sürücü restart | RECOVERING | P1 |
| EX-18 | Kilit sahipliği | Aynı süreç 2. open | İkinci reddedilir | P2 |
| EX-19 | Cihaz UID değişimi | Re-enumeration | UID ile yeniden bağlan | P1 |
| EX-20 | Performans | Round-trip ölçümü | ≤1.34ms hedef | P0 |
| EX-21 | CPU | Boşta ölçüm | <%1 hedef | P2 |
| EX-22 | Kanal sınırı | 64×64 | Sınır korunur | P2 |
| EX-23 | Buffer değişim | Süre ölçümü | <10µs hedef | P1 |
| EX-24 | Kilit sızıntısı | Kapanış sonrası | Kaynak kalmaz | P1 |
| EX-25 | Eşzamanlı ölçüm | İki ölçüm aracı | Kilitsiz yarış yok | P2 |

---

## §18 Sık Sorulan Sorular

**S1: Exclusive mode neden shared moddan daha düşüktür gecikmesi?**
Çünkü miksaj, efekt zinciri ve yeniden örneklemeler Windows tarafından değil, doğrudan donanım katmanında yapılır; ara katman sayısı azalır. Sayısal karşılaştırma §3 tablosunda.

**S2: İkinci uygulama neden reddedilir?**
Tek-sahiplik kuralı (guardrail #1). Aksi halde iki süreç aynı FIFO'yu yazarsa veri bozulur ve sürücü kararlılığı kaybolur.

**S3: Kilit nasıl bırakılır?**
Kapanışta ters sıra (stop → detach → flush → close → unlock). Sahip süreç ölürse OS handle temizliği kilidi bırakır.

**S4: Cihaz kaybolunca uygulama neden çökmez?**
Durum makinesi LOST → RECOVERING akışına geçer; uygulama katmanına "cihaz yok" durumu bildirilir, doğrudan crash edilmez.

**S5: ASIO ile WASAPI Exclusive farkı nedir?**
İkisi de tek-sahipli düşük gecikme yollarıdır; CoreMusic öncelik sırası ASIO'yu #1, WASAPI Exclusive'i #2 koyar (kaynak: k2 CLAUDE.md L27–L34).

**S6: Buffer boyutu burada mı seçilir?**
Hayır — bu doküman kilidi anlatır; boyut seçimi [[../k043-latency-optimization/buffer-size-selection]] içinde.

**S7: Kilidin süresi var mı?**
Yerel lock için tasarım önerisi timeout uygulamaktır; kesin değer ⚠️ VERIFICATION REQUIRED.

**S8: Fallback yolu nedir?**
Exclusive başarısızsa WASAPI shared yolu denenir → [[../k037-wasapi-exclusive/wasapi-exclusive-shared]].

**S9: Ölçüm nasıl yapılır?**
Round-trip ölçüm adımları [[../k043-latency-optimization/latency-chain-budget]] §Ölçüm bölümündedir.

**S10: Güvenlik etkisi var mı?**
Kilit, süreç izolasyonuyla ilişkilidir; kapsam dışı → [[../k051-process-isolation/index]].

---

## §19 Terim Sözlüğü (K036 bağlamı)

| Terim | Tanım |
|-------|-------|
| Exclusive mode | Tek uygulamanın cihazı doğrudan kullandığı yol |
| Shared mode | Birden çok uygulamanın paylaştığı Windows ses yolu |
| Ownership | Cihazın kim tarafından açık tutulduğu |
| Device-loss | Cihazın beklenmedik biçimde kaybı |
| RECOVERING | Yeniden bağlanma deneme durumu |
| Flush | Kapanışta buffer'ı boşaltma |
| Round-trip | Girişi çıkışa kadar toplam gecikme |
| xrun | Underrun/overrun ortak adı |
| Handle | OS'nin cihaza verdiği açık nesne |
| SR | Sample rate (örnekleme hızı) |
| Guardrail | İhlali sistem durduran bağlayıcı kural |
| Fallback | Ana yol başarısızsa geçilen yol |
| Page lock | Bellek sayfalarını takas dışında tutma |
| Re-enumeration | Cihazın yeniden listelenmesi |
| UID | Cihazın kalıcı kimlik numarası |
| Callback | Sürücünün uygulamayı çağırdığı geri dönüş |

---

## §20 Komşu Modül Etkileşimi

| Komşu modül | Bağlantı | Etkileşim |
|-------------|----------|-----------|
| K037 WASAPI | [[../k037-wasapi-exclusive/index]] | Fallback yolu, karşılaştırma |
| K041 Driver stack | [[../k041-driver-stack-mimari/index]] | HAL üstü exclusive çağrısı |
| K042 Buffer | [[../k042-buffer-management/index]] | Çift buffer veri yapısı |
| K043 Latency | [[../k043-latency-optimization/index]] | Bütçe ve ölçüm |
| K047 Kernel API | [[../k047-kernel-audio-api/index]] | OS çağrı yüzeyi |
| K048 IRQ/DMA | [[../k048-interrupt-dma-flow/index]] | Donanım aktarımı |
| K050 Hotplug | [[../k050-device-hotplug-power/hotplug-detect-flow]] | Cihaz kaybı olayı |
| K051 Isolation | [[../k051-process-isolation/index]] | Süreç sahipliği sınırı |

---

## §21 Risk Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|----------|------|---------|
| R1 | Kilit sızıntısı (kilit kırık kalır) | Orta | Yüksek | Timeout + ters sıra |
| R2 | Device-loss sonrası geri dönülememe | Orta | Yüksek | Retry + kullanıcı bildirimi |
| R3 | SR uyuşmazlığının sessizce geçmesi | Düşük | Yüksek | Açılışta zorunlu doğrulama |
| R4 | Callback'te bloke çağrı | Orta | Yüksek | Kod taraması + RT kuralı |
| R5 | Ölçüm ile hedefin uyumsuzluğu | Orta | Orta | Ölçüm protokolü |
| R6 | Sürücü sürüm değişikliği | Düşük | Orta | HAL soyutlaması |
| R7 | İki cihazda tutarsız Exclusive | Düşük | Orta | Çift cihaz testi (EX-11) |
| R8 | Uyanma sonrası handle geçersiz | Orta | Orta | RECOVERING akışı |
| R9 | Ölçüm verisinin olmaması | Yüksek | Düşük | ⚠️ VERIFICATION REQUIRED |
| R10 | Kod implementasyonunun yokluğu | Yüksek | Yüksek | Faz planına alınmalı |

---

## §22 Uygulama Kontrol Listesi (Kod öncesi + kod sonrası)

- [ ] Cihaz listesi okunmadan exclusive open çağrılmıyor
- [ ] Yerel tek-sahiplik lock tüm süreçlerde tutarlı
- [ ] Exclusive bayrağı açık değilse akış başlamıyor
- [ ] SR/bit/kanal doğrulaması açılışta zorunlu
- [ ] Buffer tahsisi başarısızsa akış başlamıyor
- [ ] Page lock uygulanmış (takas dışında)
- [ ] Callback içinde malloc/new yok
- [ ] Callback içinde lock/wait/sleep yok
- [ ] Callback içinde dosya ağı loglama yok
- [ ] xrun sayacı her callback'te güncelleniyor
- [ ] Cihaz-varlık sinyali izleniyor
- [ ] LOST durumunda uygulama çökmüyor
- [ ] RECOVERING retry sınırı uygulanıyor
- [ ] Kapanışta ters sıra zorunlu
- [ ] Flush hatası close'u engellemiyor
- [ ] BUSY durumu kullanıcıya anlaşılır yazılıyor
- [ ] Fallback yolu kodda mevcut
- [ ] Log maskeleme kuralı uygulanıyor
- [ ] Ölçüm adımları dokümante edilmiş
- [ ] EX-01..EX-25 testleri tanımlı
- [ ] Kilit sızıntısı testi (EX-24) geçiyor
- [ ] Komşu modül bağlantıları geçerli (kırık link 0)
- [ ] Frontmatter 7 alan doğrulanmış
- [ ] Kanıt satırları gerçek yollar içeriyor

---

### §22.1 Ek doğrulamalar (kilit yaşam döngüsü)

- [ ] Lock acquire/release olayları loglanıyor (maskeleme kuralıyla)
- [ ] Lock timeout değeri yapılandırma dosyasında (⚠️ VERIFICATION REQUIRED: kesin değer)
- [ ] Force-release yalnız yetkili süreçte mümkün
- [ ] Sahip süreç PID'i kilide yazılıyor (yeniden başlatmada kirlenme kontrolü)
- [ ] Cihaz UID kilide bağlı, cihaz adı değil
- [ ] Re-open öncesi eski handle kapatıldığından emin olunuyor
- [ ] Exclusive→shared geçişi kapat-aç sırasıyla yapılıyor (arada açık iki handle yok)
- [ ] Kapanışta callback'in tamamlandığı bekleniyor
- [ ] İki çıkışlı senaryoda kilitler ayrı ayrı tutuluyor
- [ ] Test EX-04 (kill) sonrası kaynak sızıntısı yok

### §22.2 Kanıt hattı (bu bölümün dayanağı)

| İddia | Kaynak | Satır |
|-------|--------|-------|
| Exclusive mode bölümü | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | L24–L40 |
| Buffer yönetimi bölümü | aynı dosya | L41–L60 |
| Hata yönetimi bölümü | aynı dosya | L105–L113 |
| Performans tablosu | aynı dosya | L154–L163 |
| Bağımlılıklar | aynı dosya | L165–L171 |
| Guardrail tablosu | `_backup/.../k2-surucu/CLAUDE.md` | L18–L24 |

---

## §23 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — exclusive mode dokümanı | Vault Documentation Specialist |
| 2026-10-06 | 4.1.0 | Backup-first derinleştirme: §6.1 lock kaybı eşlemesi + fallback akışı, §12.1 backup kanıtı bloğu (+6 satır), §13 altı K036 topic linki (`[[asio-buffer-callback]]` düz metinden wiki-linke) — backup kanıtı: k2-surucu/driver-stack-mimari.md L116-L133, L148-L210, L239-L246; k2-surucu/asio-drivers.md L105-L113, L117-L158; k0-isletim-sistemi/windows-api.md L79-L87, L175-L185 | Embedded Engineer (backup-first revizyon) |
