---
title: "K037.3 — WASAPI Shared Mode: Windows Miksaj Motoru, DSP ve Fallback Zinciri"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K037.3 — WASAPI Shared Mode

**Bağlantılar:** [[index]] · [[wasapi-exclusive-shared]] · [[wasapi-exclusive-mode]] · [[wasapi-format-negotiation]] · [[wasapi-buffer-latency]] · [[wasapi-hata-kodlari]] · [[wasapi-audio-session]] · [[wasapi-device-hotplug]] · [[../k036-asio-drivers/index]]

---

## §1 Genel Bakış

Shared mode, uygulamanın sesini **Windows Audio Service (ses motoru)** üzerinden göndermesidir: mix organizer devrede, çoklu uygulama aynı cihazı paylaşır, DSP Windows tarafından uygulanır. Kaynak doküman tanımı: "Windows Audio Service üzerinden · Mix organizer devrede · Çoklu uygulama · DSP eklenebilir · Yüksek latency · Win formats (PCM, IEEE float)".

> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L16–L31.

Shared mode bu klasörde **iki rol** taşır:

1. **Fallback yolu:** Exclusive reddedildiğinde (`0x8889000A` / `0x8889000E`) düşülen mod (index.md §4.3).
2. **Çoklu uygulama modu:** Aynı anda birden fazla uygulamanın tek cihazı paylaştığı normal Windows ses davranışı.

K2 öncelik sırasındaki yeri: **#6 (son çare)** — Kanıt: `_backup/.../k2-surucu/CLAUDE.md` L27–L34.

### §1.1 Bu dokümanın soruları

1. Windows miksaj motoru ne yapar, bit-perfect'i neden bozar? (§3)
2. Exclusive → Shared fallback zinciri nasıl işler? (§4)
3. Shared modda format sorumluluğu kimde? (§5, bkz. [[wasapi-format-negotiation]])
4. Shared latency neden 10–40 ms ve bütçeye nasıl oturur? (§6)

---

## §2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Shared mod davranışı, miksaj motoru, DSP | ASIO yolu → `../k036-asio-drivers/index` |
| Fallback zinciri (Exclusive → Shared) | Tam hata kodu tablosu → [[wasapi-hata-kodlari]] |
| Shared buffer/period davranışı | Period negotiation → [[wasapi-buffer-latency]] |
| Mix format ve otomatik dönüştürme | Format pazarlığı → [[wasapi-format-negotiation]] |
| Shared modda oturum/metering | Oturum arayüzleri → [[wasapi-audio-session]] |

---

## §3 Windows Miksaj Motoru (Mix Organizer)

### §3.1 Motorun yaptığı işler

| # | İş | Bit-perfect etkisi | Kaynak |
|---|----|--------------------|--------|
| 1 | Birden çok uygulama akışını tek uç noktada birleştirme (miksaj) | Bozar (kaynak karışımı) | `wasapi-exclusive.md` L24–L25 |
| 2 | Farklı SR/bit/kanal formatlarını ortak formata çevirme | Bozar (dönüşüm) | L29 "Win formats" |
| 3 | Efekt zinciri (DSP) uygulama | Bozar (işleme) | L26 "DSP eklenebilir" |
| 4 | Uygulama başına ses seviyesi (session volume) | Bozar (kısma) | [[wasapi-audio-session]] §5 |
| 5 | Ortak paylaşımlı formata uyarlama | Bozar (bit derinliği düşebilir) | [[wasapi-format-negotiation]] |

### §3.2 Shared mod veri yolu (ASCII)

```
 Uygulama A ─┐
 Uygulama B ─┼──▶ App Buffer ──▶ ┌──────────────────────────┐
 Uygulama C ─┘   (definite)      │ Windows Audio Engine     │
                                 │ · mix (birleştirme)      │
                                 │ · format dönüştürme      │
                                 │ · DSP / efekt zinciri    │
                                 │ · session volume         │
                                 │ (indefinite)             │
                                 └────────────┬─────────────┘
                                              ▼
                                   Donanım (HW) ──▶ hoparlör
```

Kaynak diyagram: `wasapi-exclusive.md` L72–L77 ("Shared Mode: App Buffer → Audio Engine → HW · definite/indefinite").

### §3.3 Exclusive ↔ Shared veri yolu farkı

| Boyut | Exclusive | Shared |
|-------|-----------|--------|
| Yol | App Buffer → Driver Buffer → HW | App Buffer → Audio Engine → HW |
| Buffer karakteri | definite / definite | definite / **indefinite** |
| Sahiplik | Tek uygulama | Çoklu uygulama |
| Latency | 1–3 ms | 10–40 ms |

Kanıt: `wasapi-exclusive.md` L65–L77 + L53–L59.

> **"indefinite" notu:** Shared'da uygulama ile donanım arasına Windows ses motoru girdiği için gecikmenin **tamamı uygulama tarafından kontrol edilemez**; bu, 10–40 ms bandının yapısal nedenidir.

---

## §4 Fallback Zinciri

### §4.1 Zincir

```
1. ASIO kullanılabilir mi?  ──evet──▶ ASIO (k2 #1)
        │hayır
        ▼
2. WASAPI Exclusive dene (k2 #2)
        │
        ├─ başarılı ──▶ ACTIVE_EXCLUSIVE (bit-perfect, ≤3 ms)
        │
        ├─ 0x8889000A ──▶ uygulama B kullanıyor ──┐
        ├─ 0x8889000E ──▶ yetki/bayrak reddi ─────┤
        └─ 0x88890008 ──▶ format uyuşmadı ────────┤
                                                  ▼
                                3. WASAPI SHARED dene (k2 #6)
                                        │
                                        ├─ başarılı ──▶ ACTIVE_SHARED
                                        │                (≤30 ms, DSP var)
                                        └─ başarısız ──▶ DEVICE_UNAVAILABLE
                                                         (kullanıcıya bildir)
```

Kaynaklar: `wasapi-exclusive.md` L124–L130 (hata kodları) · `_backup/.../k2-surucu/CLAUDE.md` L27–L34 (sıra #1/#2/#6) · akış tasarımı: `wasapi-exclusive-shared.md` §3.3.

### §4.2 Fallback karar tablosu

| Tetikleyen olay | Hedef mod | Kullanıcıya | Latency etkisi |
|-----------------|-----------|-------------|----------------|
| ASIO yok | WASAPI Exclusive | "ASIO bulunamadı, WASAPI Exclusive kullanılıyor" | ≤3 ms |
| `0x8889000A` | WASAPI Shared | "Cihaz başka uygulama tarafından kullanılıyor" | ≤30 ms |
| `0x8889000E` | Yetki denetimi → tekrar → Shared | "Yetki kontrolü" | ≤30 ms |
| `0x88890008` | Format pazarlığı → Shared | "Format dönüştürülüyor" | ≤30 ms |
| Cihaz kaybı | Cihaz bekleme → tekrar enumeration | "Cihaz bağlantısı kesildi" | — |
| Çoklu uygulama gereksinimi | Shared (doğrudan) | "Paylaşımlı mod" | ≤30 ms |

### §4.3 Fallback'in korunması gereken kuralları

| # | Kural | Kaynak |
|---|-------|--------|
| 1 | Exclusive reddi **sessizce** yutulmaz; mod düşüşü loglanır ve UI'a yansır | index.md §19.3 R1 |
| 2 | Fallback sırasında RT thread'de blocking yok (retry kısa, askıya alma UI katmanında) | `k2-surucu/CLAUDE.md` L18–L24 guardrail #2 |
| 3 | Shared'a düştükten sonra latency hedefi güncellenir (3 ms → 30 ms) | `wasapi-exclusive.md` L177–L183 |
| 4 | ASIO önceliği (#1) her yeniden denemede korunur | `k2-surucu/CLAUDE.md` L27–L34 |

---

## §5 Shared Modda Format Sorumluluğu

| Sorumluluk | Exclusive | Shared |
|------------|-----------|--------|
| Format seçimi | Uygulama + cihaz birebir | Windows **mix formatına** uyar |
| Dönüşüm | Yok (bit-perfect) | Windows motoru yapar |
| Redd'i | `0x88890008` → format değiştir | Motor varsayılanı genelde kabul edilir |
| Bit derinliği | Donanım formatı | Paylaşımlı format (kaynak: "Win formats (PCM, IEEE float)" L29) |

Ayrıntı: [[wasapi-format-negotiation]] §4 (mix format) · §5 (desteklenmeyen format → mix callback).

> **Guardrail bağlantısı:** k2 guardrail #4 "sample rate mismatch önlem" shared modda Windows'un dönüşümüne **bırakılmaz**; uygulama/cihaz SR uyuşmazlığı açılışta reddedilir (`k2-surucu/CLAUDE.md` L18–L24). Dönüşüm yalnız mod sınırları içinde kabul edilir.

---

## §6 Shared Latency Bütçesi

### §6.1 Kaynak değerler

| Metrik | Exclusive | Shared | Kaynak |
|--------|-----------|--------|-------|
| Latency bandı | 1–3 ms | 10–40 ms | `wasapi-exclusive.md` L53–L59 |
| Input Latency | 1.5 ms | 15 ms | L179 |
| Output Latency | 1.5 ms | 15 ms | L180 |
| Round-trip | 3 ms | 30 ms | L181 |
| CPU (boşta) | %0.5 | %2 | L182 |

### §6.2 Shared latency bileşen ayrıştırması

| Bileşen | Kaynağı | Kontrol edilebilirliği |
|---------|---------|------------------------|
| Uygulama buffer'ı | Uygulama | Tam |
| Windows ses motoru kuyruğu (mix period) | OS | Kısmi (IAudioClient3 ile shared period seçimi → [[wasapi-buffer-latency]] §6) |
| DSP/efekt zinciri | OS + kullanıcı efektleri | Kullanıcı kapatabilir (cihaz efektleri) |
| Donanım FIFO/endpoint buffer | Sürücü/donanım | Okunur, yazılamaz |

> ⚠️ VERIFICATION REQUIRED: Bu bileşenlerin **sayısal payları** repo'da ölçülmemiştir; yalnız toplam hedefler kaynaklıdır (§6.1).

### §6.3 Bütçe kuralı

```
Shared round-trip ≤ 30 ms (kaynak L181)
  = app buffer + engine period + endpoint buffer + ölçüm hatası

Ölçüm > 30 ms ise:
  1) mix period'u küçült dene (IAudioClient3)      → [[wasapi-buffer-latency]] §6
  2) uygulama buffer'ını küçült                    → [[wasapi-buffer-latency]] §4
  3) cihaz efektlerini devre dışı bırak            → bu dosya §3.1
  4) hâlâ > 30 ms → Exclusive/ASIO yoluna çık      → index.md §4.1
```

---

## §7 Shared Mod Oturum ve Metering Etkisi

Shared modda ses seviyesi **iki katmanda** çalışır:

| Katman | Sorumlu | Etki |
|--------|---------|------|
| Endpoint volume (donanım) | `AudioEndpointVolume` | Tüm cihaz sesi |
| Session volume (uygulama) | `AudioSessionControl` / basit ses kontrolü | Yalnız bu uygulama |

Exclusive modda session volume genelde **anlamsızdır** (motor devre dışı); shared'da ise her uygulamanın kendi oturumu vardır.

Ayrıntı: [[wasapi-audio-session]] §5 · Kanıt tablosu: `wasapi-exclusive.md` L88–L97.

---

## §8 Shared Açılış Prosedürü

| # | Adım | Girdi | Çıktı | Hata yolu |
|---|------|-------|-------|-----------|
| 1 | Endpoint seç | Rol | Cihaz | Yok → [[wasapi-device-hotplug]] |
| 2 | Mix formatı sorgula | — | Windows paylaşımlı formatı | Hata → [[wasapi-hata-kodlari]] |
| 3 | İstenen formatı belirle | SR/bit/kanal | `WAVEFORMATEX` | Dönüşüm gerekebilir → [[wasapi-format-negotiation]] |
| 4 | Open shared | Bayrak + süre | `IAudioClient` | `0x8889…` → [[wasapi-hata-kodlari]] |
| 5 | Render servisi + event | — | `IAudioRenderClient` | COM hatası → COM kuralı |
| 6 | Start | — | RUNNING | — |
| 7 | Latency doğrula | Round-trip | ≤30 ms | Sapma → §6.3 |
| 8 | UI bilgilendirmesi | Mod | "Paylaşımlı mod" | — |

---

## §9 Kenar Durumlar

| # | Kenar durum | Davranış |
|---|------------|----------|
| 1 | Paylaşımlı formatta 8 kanal istenirse | Motor 2 kanala upmix/mix yapar → bit-perfect yok |
| 2 | Başka uygulama session volume'ünü sıfırlar | Yalnız o uygulama etkilenir; global değil |
| 3 | Windows ses servisi yeniden başlar | Oturum koptu → [[wasapi-audio-session]] §7 |
| 4 | Varsayılan cihaz değişir | Akış yeni uç noktaya taşınır → [[wasapi-device-hotplug]] |
| 5 | Shared latency 30 ms'yi aşar | §6.3 kademeli azaltma / Exclusive'e dönüş |
| 6 | Exclusive ile aynı anda Shared açılması | Exclusive varken diğer uygulamalar paylaşımlı akışta kalır (motor ayrı yollar kullanır) ⚠️ VERIFICATION REQUIRED (davranış resmi dokümanla teyit edilecek) |
| 7 | Uygulama askıya alındı | Oturum durumu korunur (index.md §8 #6) |

---

## §10 Hata Modları

| Hata | Belirti | Kök neden | Düzeltme |
|------|---------|-----------|----------|
| Exclusive reddi → Shared reddi | Ses yok | Cihaz kaybı/lock | [[wasapi-device-hotplug]] |
| `0x88890008` | Dönüşüm fail | Format pazarlığı başarısız | [[wasapi-format-negotiation]] §5 |
| Latency taşması | Round-trip >30 ms | Engine period büyük | §6.3 |
| Xrun (underflow) | Crackling | RT thread geç kaldı | `k2-surucu/CLAUDE.md` guardrail #3 |
| SR uyuşmazlığı | Pitch shift | Guardrail #4 ihlali | Açılışta reddet |
| Oturum kaybı | Metering/UI durdu | Servis değişimi | [[wasapi-audio-session]] §7 |

---

## §11 Test Matrisi (SH-serisi)

| ID | Test | Beklenen | Durum |
|----|------|----------|-------|
| SH-W01 | Shared açılış | Başarılı | ⚠️ kod yok |
| SH-W02 | Exclusive reddi → otomatik Shared | Geçiş + UI bildirimi | ⚠️ kod yok |
| SH-W03 | Çoklu uygulama (2 süreç) | İkisi de çalar | ⚠️ kod yok |
| SH-W04 | Round-trip ölçümü | ≤30 ms | ⚠️ ölçüm yok |
| SH-W05 | CPU (boşta) | ≤%2 | ⚠️ ölçüm yok |
| SH-W06 | Session volume değişimi | Yalnız o uygulama etkilenir | ⚠️ kod yok |
| SH-W07 | Bit-perfect testi | **Başarısız olması beklenir** (shared'da miksaj var) | ⚠️ ölçüm yok |
| SH-W08 | Fallback zinciri uçtan uca | ASIO→Exc→Shared sırası korunur | ⚠️ kod yok |

---

## §12 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| Windows ses servisi (OS) | Alt | Mix motoru |
| Windows SDK | Alt | `wasapi-exclusive.md` L185–L192 |
| K1 Windows Core | Alt | thread/bellek/event |
| K3 Engine | Üst | Tüketici |
| K036 ASIO / K037 Exclusive | Yatay | Üst basamaklar (fallback kaynağı) |

---

## §13 Kanıt Satırları

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | Shared mode tanımı (Audio Service, mix devrede, çoklu uygulama, DSP, yüksek latency) | `_backup/.../k2-surucu/wasapi-exclusive.md` | L16–L31 |
| 2 | Shared veri yolu: App Buffer → Audio Engine → HW (indefinite) | aynı dosya | L61–L77 |
| 3 | Latency bandı 10–40 ms | aynı dosya | L53–L59 |
| 4 | Round-trip 30 ms, CPU %2 | aynı dosya | L177–L183 |
| 5 | Bit-perfect: Shared = Hayır | aynı dosya | L53–L59, L183 |
| 6 | Hata kodları (fallback tetikleyicileri) | aynı dosya | L124–L130 |
| 7 | Öncelik sırası #6 WASAPI Shared | `_backup/.../k2-surucu/CLAUDE.md` | L27–L34 |
| 8 | Guardrail #2/#3/#4 | aynı dosya | L18–L24 |
| 9 | Session bileşenleri | `wasapi-exclusive.md` | L88–L97 |

> §3 ve §5'teki **mix organizer / engine** kavramları kaynak dokümanın kendi ifadeleridir; motor iç implementasyon ayrıntıları (resmi arayüz listesi) official WASAPI dokümanına aittir ve repo kanıtı **değildir**.

---

## §14 Wiki-Bağlantılar

| Hedef | Bağlantı | İlişki |
|-------|----------|--------|
| Hub | [[index]] | K037 indeksi |
| Mod rehberi | [[wasapi-exclusive-shared]] | Üst karşılaştırma |
| Exclusive | [[wasapi-exclusive-mode]] | Üst basamak / fallback kaynağı |
| Buffer | [[wasapi-buffer-latency]] | Shared period |
| Format | [[wasapi-format-negotiation]] | Mix format |
| Oturum | [[wasapi-audio-session]] | Session volume |
| Hata | [[wasapi-hata-kodlari]] | Kod tablosu |
| Hotplug | [[wasapi-device-hotplug]] | Cihaz değişimi |
| ASIO | [[../k036-asio-drivers/index]] | #1 öncelik |

---

## §15 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | Shared bileşen latency payları (ölçüm) | ⚠️ VERIFICATION REQUIRED |
| 2 | Exclusive açıkken shared davranışının resmi teyidi | ⚠️ VERIFICATION REQUIRED |
| 3 | Kod implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 4 | Mix format örnek değerleri (cihaza göre değişir) | ⚠️ VERIFICATION REQUIRED |

---

## §16 Kontrol Listesi

- [x] Frontmatter 7 alan · `version: 4.0.0` · `updated: 2026-10-06`
- [x] Wiki-link yalnız mevcut disk hedeflerine
- [x] Kanıt = gerçek dosya yolu + satır aralığı
- [x] Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`
- [x] Yeni sayı/sürüm/hex üretilmedi
- [x] `git commit` atılmadı

---

## §17 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — Shared mode konu dosyası (multi-md yapılandırması) | Vault Documentation Specialist |
