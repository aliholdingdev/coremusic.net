# Gecikme Hedefleri ve ASIO/WASAPI Rehberi (Neva Engine)

> Bu dosya skill içindeki gecikme/özel-erişim referansıdır. Vault ile çelişirse
> `SKILL.md §10.1` kuralı uygulanır: **disk kanıtı kazanır, kaynak "closes" olarak işaretlenir.**
> Kaynaklar: [S3] Steinberg ASIO SDK · [S5] Microsoft Learn — [sources.md](sources.md)

## 1. Proje Gecikme Hedefleri (giriş — hedef değerler)

**Kaynak:** `.ai/sartname.md` (§16.3) — nihai hedef:

| Kaynak | Gecikme Hedefi |
|---|---|
| Giriş (ASIO) | **< 10 ms** |
| Çıkış (ASIO) | **< 10 ms** |
| Toplam (giriş→çıkış) | **< 20 ms** |

**Geçici (giriş fazı — mevcut 512 örnek):**

| Backend | Gecikme Hedefi |
|---|---|
| ASIO | **< 10 ms** |
| WASAPI Exclusive | **< 20 ms** |

**Mevcut depo durumu (disk kanıtı):** yalnız `WasapiBackend` ve `NullBackend` vardır —
`wasapi_latency.md` (§3.3.14) hedefi: **10 ms'in altında**. ASIO backend dosyası diskte YOK
(→ §2.3 ve SKILL.md §10.1.3).

## 2. Gecikme Matematiği

### 2.1 Örnek→milisaniye dönüşümü

```
ms = örnek × 1000 / örnek-hız
```

| Örnek (buffer) | 48 kHz | 44.1 kHz |
|---|---|---|
| 64 | 1.33 ms | 1.45 ms |
| 128 | 2.67 ms | 2.90 ms |
| 256 | 5.33 ms | 5.80 ms |
| **512** | **10.67 ms** | **11.61 ms** |
| 1024 | 21.33 ms | 23.22 ms |

### 2.2 Toplam gecikme bileşenleri

Toplam gecikme yalnız buffer değildir — üç bileşen toplanır:

| Bileşen | Ne | Kontrol |
|---|---|---|
| **Ön-araç (pre-roll / render-ahead)** | Kaynaktan ilk bloğa kadar hazır tutulan örnek (sartname §3.2.3: "Kaynak, ilk render çağrısından en az 1 tam blok (512 örnek) geriden okumalı — ön-araç (pre-roll / render-ahead)") | ≥ 1 blok |
| **Dönüşüm + eşitleme (convert + align)** | `convert_plan.h` / `spsc_ring.h` — kaynak→zincir ve zincir→backend eşitleme tamponları | SPSC ring boyutu (64·8=512 varsayılan) |
| **Backend tamponu** | Cihazın isteği (WASAPI exclusive default 480, shared 480; ASIO driver-controlled) | Backend seçimi + buffer-size |

**512 örneğin 10.67 ms olduğu gerçeği:** 48 kHz'de 512 örnek **tek başına** giriş veya
çıkış hedefini (<10 ms) aşar. Bu yüzden en düşük gecikme bandı **256 örnektir**
(`wasapi_latency.md` §3.3.14: "256 örnek bandında (<5.4 ms) gerçek zamanlı ölçüm gerekir").

### 2.3 Buffer-size → gecikme tablosu (48 kHz, WASAPI Exclusive 480 varsayılan varsayımıyla)

| Buffer | Blok süresi | 2× blok (giriş+çıkış, sade backend) | Hedef durumu |
|---|---|---|---|
| 256 | 5.33 ms | 10.67 ms | ASIO <10 için uygun (tek yön), toplam <20 sınırında |
| 480 (WASAPI default) | 10.00 ms | 20.00 ms | Toplam hedefin sınırında |
| 512 | 10.67 ms | 21.33 ms | Geçici faz: tek yön ASIO <10 aşılır → buffer küçültme gerekir |
| 1024 | 21.33 ms | 42.67 ms | Hedefler aşılır — yalnız fallback |

> `IAudioClient::Initialize` / `IAudioClient3` çağrıları buffer süresini belirler (S5).

## 3. ASIO — Özel Erişim (Exclusive)

| Konu | Kural |
|---|---|
| **Tek uygulama** | ASIO Exclusive Lock: bir cihazı **yalnız BİR** uygulama exclusive olarak kullanabilir (AGENTS.md kural 9) |
| **Çoklu deneme riski** | Aynı anda ikinci bir ASIO uygulaması exclusive denerse **sürücü çökmesi** riski (vault brain #910) |
| **Buffer boyutu** | ASIO'da buffer boyutu driver tarafından yönetilir (`kAsioBufferSize` kanalı üzerinden uygulama önerisi gönderebilir) — backend kodu diskte YOK (SKILL.md §10.1.3) |
| **Sürüm kanıtı** | `ASIO-SDK_2.3.4_2025-10-15.zip` (vcpkg + MSYS2 kaynaklı; S3b/S3c). `steinberg.net` sayfası sürüm yazmaz → depo sürüm kanıtı **yalnız paket kayıtlarında** |

**Uygulama akışı (hedef mimari):**

```
backend seç → [ASIO] cihazı bul → exclusive başlat (tek uygulama)
            → buffer-size öner (256/512) → driver'ın kabul ettiğini doğrula
            → callback zinciri çalıştır → hata → WASAPI Exclusive → Shared → Null
```

## 4. WASAPI — Exclusive / Shared / IAudioClient3

| Mod | Ne | Gecikme | Disk kanıtı |
|---|---|---|---|
| **Exclusive** | Uygulama cihazı doğrudan kullanır, Windows miksersiz (S5) | En düşük (S5: "minimize latency... use exclusive-mode streams") | `wasapi_latency.md` §3.3.14: exclusive default 480 örnek |
| **Shared** | Windows miksersi üzerinden (tüm uygulamalar ses paylaşır) | Exclusive'den yüksek | `wasapi_latency.md` §3.3.10: shared default 480 |
| **IAudioClient3** | Win10+ düşük-gecikme (low-latency) API (S5: "minimize latency with IAudioClient3") | Exclusive'a yakın, daha esnek | API kullanımı diskte VAR (wasapi_backend.cpp) |

**Ölçüm gerçeği (vault `wasapi_latency.md` §3.3.10/§3.3.14):**

- Windows 11: exclusive/shared default **480** örnek
- Windows 10: exclusive default **480**, shared default **960**; `IAudioClient3` ile
  **128/256** örnek mümkün
- Uygulama buffer **önerisi** (`IAudioClient::Initialize` / `IAudioClient3`
  `IAudioClient3::IsFormatSupported` + `GetMixFormat`) her zaman driver'ın
  son sözü değildir — **negotiate edilen değeri geri oku** ve zinciri buna göre kur.

### 4.1 MMCSS — "Pro Audio" önceliği

`wasapi_backend.cpp` render callback'ini **MMCSS "Pro Audio"** iş programına kaydeder:

```cpp
// wasapi_backend.cpp (disk kanıtı)
AvSetMmThreadCharacteristicsW(L"Pro Audio", &dwTaskIndex)
```

Bu, callback thread'inin CPU planlayıcı önceliğini yükseltir ve xrun (underrun)
olasılığını düşürür (S5: "use the Multimedia Class Scheduler (MMCSS)").

## 5. ASIO vs WASAPI Karşılaştırma

| Kriter | ASIO | WASAPI Exclusive | WASAPI Shared |
|---|---|---|---|
| Gecikme | En düşük (driver buffer'ı) | Düşük (IAudioClient3 ile daha da düşük) | Orta (mikser ekler) |
| Özel erişim | Evet (tek uygulama — kural 9) | Evet (Windows miksersiz) | Hayır |
| Cihaz erişimi | Sürücü üzerinden | Windows üzerinden | Windows üzerinden |
| Sürücü bağımlılığı | Evet (ASIO driver şart) | Hayır (generic) | Hayır (generic) |
| Bağımlılık lisansı | ASIO SDK Steinberg lisansı (00-sdk-lib-envanter.md — lisans metni okunmalı, §VERIFICATION) | Windows SDK (GPL uyumlu, vault) | Windows SDK |
| Depo durumu | Backend dosyası YOK (SKILL.md §10.1.3) | **VAR** (`wasapi_backend.cpp` — üretim kanıtı) | VAR (aynı backend, mod seçimi) |

## 6. Ölçüm Yaklaşımı

1. **İç (matematiksel) ölçüm:** toplam gecikme = ön-araç + ring gecikmesi + backend
   tamponu. `neva_session` blok çağrısı sayacı ve `spsc_ring` dolum seviyesi ile
   hesaplanır (dosya: `include/neva/neva_session.h`).
2. **Dış (gerçek) ölçüm:** loopback (girişi çıkışı fiziksel olarak bağla → Δt ölç) —
   vault `wasapi_latency.md` §3.3.15 "Gecikme ölçüm metodu": "WASAPI Exclusive'te
   render callback süresi ölçülebilir".
3. **Ölçüm koşulu:** en düşük bant (256 örnek / <5.4 ms) test edilmeden "giriş
   gecikmesi <10 ms" iddia edilemez.

## 7. Fallback Zinciri (backend sırası)

```
ASIO (hedef; eksik → §10.1.3) → WASAPI Exclusive (IAudioClient3 → IAudioClient)
    → WASAPI Shared → NullBackend (test)
```

Her adım `BackendResult` (contracts.md §4) ile raporlanır; `kOk` değilse zincirde
bir sonraki backend denenir. `NullBackend` son çare ve test backend'idir.

## 8. Xrun (Underrun/Overrun) Teşhisi

| Belirti | Olası neden | Kontrol |
|---|---|---|
| Tıklama/crackling, ses boşlukları | Callback blok süresi bütçesi aşıldı | `convert_plan.h` SIMD yolları (§dsp-chain-map), /arch:AVX2 |
| Yalnız yüksek kanal sayısında | Kanal başına işlem maliyeti | EqChain 62 biquad (contracts.md §7) CPU bütçesi |
| Shared modda, exclusive'de yok | Mikser tamponu + diğer uygulamalar | Exclusive dene |
| Uygulama yeniden başlatınca düzelir | Ring/lock-free publish yarışı | spsc_ring.h incele (R6) |
| MMCSS olmadan | Callback düşük öncelikte | `AvSetMmThreadCharacteristicsW(L"Pro Audio")` (§4.1) |

**Ölçüm:** `null_backend` render-süresi sayacı (`null-backend.md`) veya ETW/WPR
profil kaydı ile callback süresi histogramı çıkarılır (ölçüm aracının depoda
entegrasyonu **VERIFICATION REQUIRED**).

## 9. Hızlı Denetim

1. Hedefler yazılı mı (§1)? (`sartname.md` §16.3)
2. Gecikme hesabı blok+bileşenlerle yapıldı mı (§2.2)?
3. 512 örneğin 10.67 ms olduğu biliniyor ve 256 bandı planlanmış mı (§2.3)?
4. ASIO tek-uygulama kuralı ve çoklu deneme riski dikkate alındı mı (§3)?
5. WASAPI exclusive/shared/IAudioClient3 farkı biliniyor mu (§4)?
6. MMCSS "Pro Audio" callback önceliği kodda var mı (§4.1)?
7. Fallback zinciri tanımlı mı (§7)?
8. Xrun teşhis tablosu biliniyor mu (§8)?
9. Ölçüm yalnız matematikle değil, gerçek loopback ile de yapıldı mı (§6)?
