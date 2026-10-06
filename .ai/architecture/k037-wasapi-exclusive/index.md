---
title: "K037 — WASAPI Sürücüleri (Exclusive / Shared Windows Ses Yolu)"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K037 — WASAPI Sürücüleri

> **K özeti:** K037, Windows'un yerel ses API'si WASAPI'nin CoreMusic'teki iki modunu kapsar: **Exclusive** (bit-perfect, düşük gecikme, tek sahip) ve **Shared** (çoklu uygulama, Windows miksajı, fallback). Ayrıca Windows Audio Session bileşenleri (oturum kontrolü, metering, endpoint volume) bu klasörün konusudur.
>
> **Yer:** D01 (k036–k053) · görüntüleme/ses sürücüleri, latency, buffer, kernel-arayüz.

**Bağlantılar:**
- İç dosya: [[wasapi-exclusive-shared]]
- Alt dosyalar (multi-md, 7): [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]] · [[wasapi-buffer-latency]] · [[wasapi-format-negotiation]] · [[wasapi-audio-session]] · [[wasapi-hata-kodlari]] · [[wasapi-device-hotplug]]
- Komşular: [[../k036-asio-drivers/index]] · [[../k042-buffer-management/index]] · [[../k043-latency-optimization/index]] · [[../k047-kernel-audio-api/index]]
- Diğer: [[../k041-driver-stack-mimari/index]] · [[../k050-device-hotplug-power/hotplug-detect-flow]] · [[../k051-process-isolation/index]]

---

## §1 Genel Bakış

WASAPI (Windows Audio Session API), Windows Vista ve sonrası üzerinde çalışan ses API'sidir. CoreMusic'te iki mod kullanılır:

| Mod | Kullanım | Öncelik (k2) |
|-----|----------|--------------|
| WASAPI Exclusive | Bit-perfect, düşük gecikme | #2 (ASIO'dan sonra) |
| WASAPI Shared | Fallback, çoklu uygulama | #6 (son çare) |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L27-L34`

### §1.1 Bu klasörün soruları

1. Exclusive ve Shared ne zaman seçilir, geçiş nasıl yapılır?
2. Buffer boyutu ve format nasıl sabitlenir?
3. Windows Audio Session bileşenleri (metering, oturum kontrolü) nasıl kullanılır?
4. Sürücü hata kodları nasıl yorumlanır ve hangi düzeltme uygulanır?

### §1.2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| WASAPI Exclusive/Shared davranışı | ASIO yolu → [[../k036-asio-drivers/index]] |
| Windows Audio Session bileşenleri | NT API/syscall → [[../k047-kernel-audio-api/index]] |
| WASAPI hata kodları ve düzeltmeleri | Genel latency bütçesi → [[../k043-latency-optimization/index]] |
| Format ve buffer ayarları | Ring buffer veri yapısı → [[../k042-buffer-management/index]] |

### §1.3 Durum

| Alan | Durum | Kanıt |
|------|-------|-------|
| Tasarım dokümanı | 🟢 Üretildi (2026-10-06) | Bu dosya |
| Kaynak içerik | 🟢 `k2-surucu/wasapi-exclusive.md` | L8–L193 |
| Kod implementasyonu | 🔴 Bulunamadı | ⚠️ VERIFICATION REQUIRED |
| Donanım ölçümü | 🔴 Yok | ⚠️ VERIFICATION REQUIRED |

---

## §2 Mimari Konum (ASCII)

```
┌────────────────────────────────────────────────────────────────┐
│ Uygulama (K3 Ses Motoru)                                        │
│   │                                                            │
│   ├── Exclusive talebi ──▶ ┌────────────────────────────┐      │
│   │                        │ WASAPI EXCLUSIVE           │      │
│   │                        │ · tek sahip                │      │
│   │                        │ · bit-perfect              │      │
│   │                        │ · 1-3ms                    │      │
│   │                        └────────────┬───────────────┘      │
│   │                                    │ başarısız            │
│   └── Shared talebi ──▶ ┌──────────────▼───────────────┐      │
│                          │ WASAPI SHARED                │      │
│                          │ · çoklu uygulama            │      │
│                          │ · Windows miksaj + DSP       │      │
│                          │ · 10-40ms                    │      │
│                          └──────────────┬───────────────┘      │
│                                         ▼                       │
│                          ┌────────────────────────────┐         │
│                          │ Audio Session (metering,   │         │
│                          │ oturum, endpoint volume)   │         │
│                          └──────────────┬─────────────┘         │
└─────────────────────────────────────────┼──────────────────────┘
                                          ▼
                              Windows Audio Engine → donanım
```

---

## §3 Dosya Haritası

| Dosya | Konu | Kaynak aralığı |
|-------|------|----------------|
| `index.md` | K037 genel bakış, mod tablosu, bağlantılar | Bu dosya |
| `wasapi-exclusive-shared.md` | İki mod, buffer, session, hata kodları, performans (tekiller + §27 multi-md) | `wasapi-exclusive.md` L16–L193 |
| `wasapi-exclusive-mode.md` | Exclusive mod: ownership, lifecycle, stream flags, bit-perfect | `wasapi-exclusive.md` L16–L31 · L53–L59 · L113–L121 |
| `wasapi-shared-mode.md` | Shared mod: engine/mix, fallback, format sorumluluğu, latency bütçesi | `README.md` L112–L122 · `windows-api.md` L95–L99 |
| `wasapi-buffer-latency.md` | Buffer katmanları, formül (288@48k=6ms), period, IAudioClient3, event-driven | `wasapi-exclusive.md` L61–L87 · `latency-optimization.md` L42–L51 |
| `wasapi-format-negotiation.md` | WAVEFORMATEX, GetMixFormat, IsFormatSupported akışı, format fallback | `wasapi-exclusive.md` L99–L111 · `windows-api.md` L103–L146 |
| `wasapi-audio-session.md` | SessionControl/Manager/Meter, metering RT kuralları, endpoint volume | `wasapi-exclusive.md` L88–L97 · `README.md` L124–L132 |
| `wasapi-hata-kodlari.md` | 4 kod (`L126–L129`) + fallback + katman yayılımı | `wasapi-exclusive.md` L124–L130 |
| `wasapi-device-hotplug.md` | Lock çatışması, yeniden enum, Registry/WMI, ASIO↔WASAPI fallback | `wasapi-exclusive.md` L124–L130 · `CLAUDE.md` L27–L34 |

---

## §4 Teknik Özet

### §4.1 Mod karşılaştırması (kaynak tablosu)

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Latency | 1–3ms | 10–40ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |
| Multi-app | Hayır | Evet |
| DSP | Donanım | Windows |

Kanıt: `wasapi-exclusive.md L53-L59`

### §4.2 Performans metrikleri (kaynak tablosu)

| Metrik | Exclusive | Shared |
|--------|-----------|--------|
| Input Latency | 1.5ms | 15ms |
| Output Latency | 1.5ms | 15ms |
| Round-trip | 3ms | 30ms |
| CPU (boşta) | %0.5 | %2 |
| Bit-perfect | Evet | Hayır |

Kanıt: `wasapi-exclusive.md L177-L183`

### §4.3 Hata kodları (kaynak tablosu)

| Hata | Kod | Çözüm |
|------|-----|-------|
| AUDCLNT_E_DEVICE_IN_USE | 0x8889000A | Shared mode'a geç |
| AUDCLNT_E_UNSUPPORTED_FORMAT | 0x88890008 | Formatı değiştir |
| AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED | 0x8889000E | Yetki kontrolü |
| AUDCLNT_E_BUFFER_SIZE_ERROR | 0x88890018 | Buffer boyutunu ayarla |

Kanıt: `wasapi-exclusive.md L124-L130`

### §4.4 Windows Audio Session bileşenleri

| Bileşen | Açıklama |
|---------|----------|
| AudioSessionControl | Oturum kontrolü (ses seviyesi, durdurma) |
| AudioSessionManager | Oturum yönetimi (tercihler, efektler) |
| AudioMeterInformation | Gerçek zamanlı ses seviyesi metering |
| AudioEndpointVolume | Donanım ses seviyesi kontrolü |

Kanıt: `wasapi-exclusive.md L92-L97`

---

## §5 Alt Dosya Özeti

`wasapi-exclusive-shared.md` şunları kapsar:
- Exclusive ve Shared modlarının çalışma mantığı ve seçim kuralları.
- Buffer yönetimi ve oturum (session) nesneleri.
- Hata kodlarının tamamı ve düzeltme tablosu.
- Performans karşılaştırması ve ölçüm adımları.
- §27: multi-md alt dosya haritası + 2026-10-06 backup turu ek kanıtları (E1–E14) ve çelişki kaydı.

Klasörün 7 alt dosyası (2026-10-06 multi-md genişletme):

| Dosya | Tek cümle özet |
|---|---|
| `wasapi-exclusive-mode.md` | Exclusive'in sahiplik durum makinesi, IAudioClient yaşam döngüsü, stream flags (EOFIL `⚠️`), bit-perfect |
| `wasapi-shared-mode.md` | Windows audio engine'i, mix veri yolu, fallback zinciri, format sorumluluğu, ~15ms latency |
| `wasapi-buffer-latency.md` | Buffer katmanları, `288/48000=6ms` formülü, endpoint buffer, period negotitation + IAudioClient3, event-driven vs pull |
| `wasapi-format-negotiation.md` | WAVEFORMATEX format tablosu (L99–L111), GetMixFormat → Initialize akışı, `0x88890008` fallback |
| `wasapi-audio-session.md` | SessionControl/Manager/Meter arayüzleri, metering RT yasağı, endpoint volume bit-perfect uyarısı |
| `wasapi-hata-kodlari.md` | 4 kod (`DEVICE_IN_USE` · `UNSUPPORTED_FORMAT` · `EXCLUSIVE_MODE_NOT_ALLOWED` · `BUFFER_SIZE_ERROR`) + eylem matrisi + kurtarma |
| `wasapi-device-hotplug.md` | Cihaz değişimi kurtarma akışı (WMI/Registry/endpoint), ASIO #1 → WASAPI Exc #2 → Shared #6 fallback |

---

## §6 Bağımlılıklar

| Bağımlılık | Yön | Tür | Not |
|-----------|-----|-----|-----|
| Windows SDK | Alt | Dış kütüphane | `wasapi-exclusive.md L185-L192` |
| K1 Windows Core | Alt | OS servisi | thread/bellek/event |
| K2 driver stack | Alt | Mimari | HAL üstü çağrı |
| K2 buffer yönetimi | Alt | Veri yapısı | Buffer boyutu |
| K3 Engine | Üst | Tüketici | Ses motoru |
| ASIO (K036) | Yatay | Öncelikli yol | Exclusive başarısızsa düşer |

---

## §7 Kanıt Satırları

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | Exclusive/Shared karşılaştırma | `_backup/.../k2-surucu/wasapi-exclusive.md` | L53–L59 |
| 2 | Performans metrikleri | aynı dosya | L177–L183 |
| 3 | Hata kodları | aynı dosya | L124–L130 |
| 4 | Session bileşenleri | aynı dosya | L92–L97 |
| 5 | Sürücü öncelik sırası (#2, #6) | `_backup/.../k2-surucu/CLAUDE.md` | L27–L34 |
| 6 | K2 → K0 DMA/IRQ | `_backup/.../k2-surucu/index.md` | L57–L61 |

---

## §8 Kenar Durumlar

| # | Kenar durum | Davranış |
|---|------------|----------|
| 1 | Exclusive açıkken ikinci uygulama açmak ister | 0x8889000A → shared öner |
| 2 | Exclusive yetkisiz süreçte | 0x8889000E → yetki denetle |
| 3 | Desteklenmeyen format | 0x88890008 → format listesi |
| 4 | Buffer boyutu geçersiz | 0x88890018 → boyutu ayarla |
| 5 | Cihaz değişti (varsayılan çıkış) | Oturumu yeniden bağla |
| 6 | Uygulama askıya alındı | Oturum durumu korunur |
| 7 | Metering eşzamanlı okuma | Kilitli okuma, RT ihlali yok |

---

## §9 Hata Modları

| Hata | Belirti | Kök neden | Düzeltme |
|------|---------|-----------|----------|
| DEVICE_IN_USE | Açılış reddi | Başka süreç | Shared'a geç |
| UNSUPPORTED_FORMAT | Ses yok | Format uyuşmadı | Format değiştir |
| EXCLUSIVE_NOT_ALLOWED | Red | Yetki/bayrak | Yetki kontrolü |
| BUFFER_SIZE_ERROR | Hata | Uygunsuz boyut | Buffer boyutunu ayarla |
| Oturum kaybı | Metering durur | Oturum koptu | Oturumu yeniden kur |
| Bit-perfect kaybı | DSP uygulanıyor | Shared modda | Exclusive yolu seç |

---

## §10 Doğrulama Kontrol Listesi

- [ ] Exclusive başarısızsa otomatik Shared'a düşülüyor
- [ ] Hata kodları tam olarak ayrıştırılıyor (4 kod)
- [ ] Metering RT thread'i bloke etmiyor
- [ ] Endpoint volume donanıma yazılıyor
- [ ] Buffer boyutu cihaz yeteneği içinde
- [ ] Bit-perfect modda Windows DSP kapalı
- [ ] Oturum kimliği cihaz kimliğine bağlı
- [ ] Ölçüm: round-trip Exclusive ≤3ms
- [ ] Ölçüm: round-trip Shared ≤30ms
- [ ] ASIO önceliği korunuyor → [[../k036-asio-drivers/index]]

---

## §11 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | WASAPI kod implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 2 | Exclusive round-trip gerçek ölçümü | ⚠️ VERIFICATION REQUIRED |
| 3 | Oturum öncelik/bütçe politikası | ⚠️ VERIFICATION REQUIRED |

---

## §12 Wiki-Bağlantılar

| Hedef | Bağlantı |
|-------|----------|
| Bu klasör dosyası | [[wasapi-exclusive-shared]] |
| Alt — Exclusive mod | [[wasapi-exclusive-mode]] |
| Alt — Shared mod | [[wasapi-shared-mode]] |
| Alt — Buffer/latency | [[wasapi-buffer-latency]] |
| Alt — Format negotiation | [[wasapi-format-negotiation]] |
| Alt — Audio session | [[wasapi-audio-session]] |
| Alt — Hata kodları | [[wasapi-hata-kodlari]] |
| Alt — Device hotplug | [[wasapi-device-hotplug]] |
| ASIO | [[../k036-asio-drivers/index]] |
| Buffer | [[../k042-buffer-management/index]] |
| Latency | [[../k043-latency-optimization/index]] |
| Driver stack | [[../k041-driver-stack-mimari/index]] |
| Kernel API | [[../k047-kernel-audio-api/index]] |
| Hotplug | [[../k050-device-hotplug-power/hotplug-detect-flow]] |
| İzolasyon | [[../k051-process-isolation/index]] |

---

## §13 Senaryolar

### §13.1 Exclusive açılışı
1. Cihaz listesi okunur.
2. Exclusive bayrakla açılır.
3. Format sabitlenir.
4. Buffer boyutu ayarlanır.
5. Akış başlar; bit-perfect doğrulanır.

### §13.2 Exclusive reddi → Shared
1. 0x8889000A alınır.
2. Kullanıcıya "diğer uygulama kullanıyor" bildirilir.
3. Shared modda açılır.
4. Latency 30ms'ye çıkar, kullanıcı bilgilendirilir.

### §13.3 Oturum metering
1. AudioSession üzerinden oturum bulunur.
2. Metering akışı başlatılır.
3. Değerler UI'a iletilir (RT ihlali olmadan).

### §13.4 Cihaz değişimi
1. Varsayılan çıkış değişir.
2. Oturum yeni uç noktaya taşınır.
3. Akış yeniden bağlanır.

### §13.5 Format uyuşmazlığı
1. 0x88890008 alınır.
2. Desteklenen formatlar listelenir.
3. Uygun format seçilir, akış başlar.

---

## §14 Ölçüm Protokolü

| Adım | İşlem | Hedef |
|------|-------|-------|
| 1 | Exclusive modda aç | — |
| 2 | Round-trip ölç (3×) | ≤3ms |
| 3 | Shared modda aç | — |
| 4 | Round-trip ölç (3×) | ≤30ms |
| 5 | CPU ölç | ≤%0.5 / ≤%2 |
| 6 | Bit-perfect doğrula | Exclusive'de evet |

> ⚠️ VERIFICATION REQUIRED: Ölçümler repo'da mevcut değildir; hedefler kaynak tablosundan alınmıştır.

---

## §15 Test Matrisi

| ID | Test | Beklenen |
|----|------|----------|
| WA-01 | Exclusive açılış | Başarılı, bit-perfect |
| WA-02 | İkinci süreç Exclusive dener | 0x8889000A |
| WA-03 | Shared açılış | Başarılı, 30ms |
| WA-04 | Desteklenmeyen format | 0x88890008 |
| WA-05 | Yetkisiz exclusive | 0x8889000E |
| WA-06 | Hatalı buffer boyutu | 0x88890018 |
| WA-07 | Metering okuma | RT ihlali yok |
| WA-08 | Endpoint volume | Donanıma yazıldı |
| WA-09 | Cihaz değişimi | Oturum yeniden bağlandı |
| WA-10 | Exclusive→Shared geçiş | Kesintisiz |
| WA-11 | CPU ölçümü | ≤%0.5 Exclusive |
| WA-12 | Round-trip ölçümü | ≤3ms Exclusive |

---

## §16 SSS

**S1: WASAPI Exclusive ile ASIO farkı?** İkisi de bit-perfect ve tek sahipli; CoreMusic ASIO'yu #1, WASAPI Exclusive'i #2 önceler.
**S2: Shared ne zaman kullanılır?** Exclusive reddedildiğinde veya çoklu uygulama gerektiğinde.
**S3: Gecikme farkı nedir?** Exclusive round-trip 3ms, Shared 30ms (kaynak L181).
**S4: Metering neden ayrı bileşen?** AudioMeterInformation gerçek zamanlı seviye okuması sağlar (L94).
**S5: Hata kodları nerede?** §4.3, kaynak L124–L130.
**S6: Buffer nerede anlatılıyor?** `wasapi-exclusive-shared.md` §Buffer.
**S7: Fallback zinciri?** ASIO → WASAPI Exclusive → WASAPI Shared.
**S8: Kod var mı?** ⚠️ VERIFICATION REQUIRED.

---

## §17 Sözlük

| Terim | Tanım |
|-------|-------|
| WASAPI | Windows Audio Session API |
| Exclusive | Tek sahipli bit-perfect yol |
| Shared | Paylaşımlı Windows ses yolu |
| Session | Uygulamaya bağlı ses oturumu |
| Metering | Gerçek zamanlı seviye ölçümü |
| Endpoint | Cihaz uç noktası |
| Bit-perfect | Dönüşümsüz bit aktarımı |
| DSP | Dijital ses işleme |
| Round-trip | Giriş→çıkış gecikmesi |
| Fallback | Alternatif yol |
| Buffer size | Buffer boyutu |
| Latency | Gecikme |

---

## §18 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| R1 | Exclusive reddinin kullanıcıya yansıması | Orta | Otomatik fallback |
| R2 | Shared'da latency sürprizi | Orta | UI'da bildir |
| R3 | Metering RT ihlali | Yüksek | Kilitli, bloke etmeyen okuma |
| R4 | Hata kodu yanlış yorumu | Yüksek | Tam kod tablosu |
| R5 | Kod yokluğu | Yüksek | ⚠️ VERIFICATION REQUIRED |

---

## §19 Kapsam-Dışı Yönlendirme

| Konu | Modül | Bağlantı |
|------|-------|----------|
| ASIO | K036 | [[../k036-asio-drivers/index]] |
| CoreAudio | K038 | [[../k038-core-audio-macos/index]] |
| ALSA | K039 | [[../k039-alsa-native/index]] |
| PipeWire | K040 | [[../k040-pipewire-modern/index]] |
| Buffer | K042 | [[../k042-buffer-management/index]] |
| Latency | K043 | [[../k043-latency-optimization/index]] |
| Kernel API | K047 | [[../k047-kernel-audio-api/index]] |
| IRQ/DMA | K048 | [[../k048-interrupt-dma-flow/index]] |

### §19.1 K037 üretim kararları (bu revizyon)

| # | Karar | Dayanak |
|---|-------|---------|
| 1 | Klasörde **2 dosya** üretilir (`index.md` + `wasapi-exclusive-shared.md`) | D01 üretimi 2–4 MD aralığında |
| 2 | Exclusive ve Shared **tek dosyada** birleştirildi; ikisi aynı API'nin modları | Ayrım ayrı dosyada bölünme yaratırdı |
| 3 | Hata kodları `0x8889…` biçimiyle **kaynaktaki haliyle** yazılır | Zero-Hallucination |
| 4 | Sürücü öncelik sırası (#2 Exclusive, #6 Shared) kaynağından alınır | `k2-surucu/CLAUDE.md` L27–L34 |
| 5 | Dönem (period) sayısal değeri kaynakta yok → `⚠️ VERIFICATION REQUIRED` | Zero-Hallucination |
| 6 | Dosya adları değişmez (in-place) · commit atılmaz | Görev kısıtları |
| 7 | **Karar #1 genişletildi (2026-10-06):** 2 dosya → **9 dosya** (7 alt dosya eklendi; mevcut 2 dosya silinmedi/genellenmedi) | Kullanıcı onayı (görev talimatı) |
| 8 | Tüm D01 dosyalarında `version: 4.0.0` · `updated: 2026-10-06` sabit | Görev talimatı |
| 9 | 2026-10-06 backup turu: ek kaynaklar (`README.md` L112–L132 · `windows-api.md` L91–L184 · `windows-core.md` L115–L235 · `00-enterprise-index.md` L188 · `latency-optimization.md` L16–L51) içeriğe taşındı; **backup ile çelişen 3 hata kodu sembolü backup'a hizalandı (backup kazandı — §20 kaydı)** | Kullanıcı kuralı: "backup ile çelişirse backup kazanır + raporla" |

### §19.2 K037 kanıt envanteri

| # | İddia | Kaynak dosya | Satır |
|---|-------|-------------|-------|
| 1 | Exclusive/Shared öncelik sırası #2 / #6 | `k2-surucu/CLAUDE.md` | L27–L34 |
| 2 | Exclusive 1–3 ms, Shared 10–40 ms | `k2-surucu/wasapi-exclusive.md` | L53–L59 |
| 3 | Round-trip 3 ms (Exclusive) vs 30 ms (Shared) | aynı dosya | L53–L59 |
| 4 | Hata kodu `0x8889000A` | aynı dosya | L124–L130 |
| 5 | Hata kodu `0x88890008` | aynı dosya | L124–L130 |
| 6 | Hata kodu `0x8889000E` | aynı dosya | L124–L130 |
| 7 | Hata kodu `0x88890018` | aynı dosya | L177–L183 |
| 8 | Guardrail: RT thread'de blocking yasak | `k2-surucu/CLAUDE.md` | L18–L24 |
| 9 | Guardrail: underrun koruması zorunlu | aynı dosya | L18–L24 |
| 10 | Guardrail: sample-rate uyuşmazlığı kabul edilmez | aynı dosya | L18–L24 |
| 11 | ASIO #1 → WASAPI Exclusive #2 sırası | aynı dosya | L27–L34 |
| 12 | Shared = Windows miksajı (çoklu uygulama) | `k2-surucu/wasapi-exclusive.md` | L53–L59 |

### §19.3 K037 risk kaydı (genişletme)

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|---------|------|---------|
| R1 | Exclusive reddedilip sessizce Shared'a düşülmesi | Orta | Yüksek | Açılışta mod doğrulaması + log |
| R2 | Başka uygulama exclusive kilidini kırması | Orta | Yüksek | Cihaz sahiplik izleme, k050 hotplug akışı |
| R3 | Metering callback'inin RT thread'i bloke etmesi | Düşük | Yüksek | Metering'i RT dışı okuma yoluna al |
| R4 | Hata kodunun yanlış yorumlanması | Orta | Yüksek | §4.3 tam kod tablosu tek kaynak |
| R5 | Bit-perfect'in Shared miksajıyla sessizce bozulması | Orta | Orta | Exclusive modda format sabitleme |
| R6 | Buffer period değerinin kaynağı olmaması | Yüksek | Orta | `⚠️ VERIFICATION REQUIRED` + ölçüm kapısı |
| R7 | Örnekleme hızı uygulama/cihaz uyuşmazlığı | Orta | Yüksek | Açılışta reddet, fallback zincirine geç |
| R8 | Kanal sayısı formatın üzerinde istenirse | Düşük | Orta | Format pazarlığını k041 üst katmana devret |

### §19.4 K037 okuma haritası

| İhtiyaç | Dosya | Bölüm |
|---------|-------|-------|
| Exclusive/Shared farkı tek bakışta | bu dosya | §4.1 |
| Performans karşılaştırması | bu dosya | §4.2 |
| Hata kodu çözümü | bu dosya | §4.3 |
| Adım adım exclusive açılışı | `wasapi-exclusive-shared.md` | §7 |
| Oturum (session) bileşenleri | `wasapi-exclusive-shared.md` | §5 |
| Buffer akışı | `wasapi-exclusive-shared.md` | §4 |
| Fallback zinciri | `wasapi-exclusive-shared.md` | §3.3 |
| Multi-md genişletme + ek backup kanıtları (E1–E14) + çelişki kaydı | `wasapi-exclusive-shared.md` | §27 |
| Exclusive ownership + stream flags + EOFIL `⚠️` | `wasapi-exclusive-mode.md` | §2 · §5 · §5.2 |
| Shared engine/mix veri yolu | `wasapi-shared-mode.md` | §1–§3 |
| Buffer katmanları + `288/48000=6ms` | `wasapi-buffer-latency.md` | §1–§5 |
| Period negotitation + IAudioClient3 | `wasapi-buffer-latency.md` | §6 |
| Format tablosu + GetMixFormat akışı | `wasapi-format-negotiation.md` | §2–§3 |
| Metering RT kuralları + endpoint volume | `wasapi-audio-session.md` | §4–§5 |
| 4 hata kodu eylem matrisi | `wasapi-hata-kodlari.md` | §1–§3 |
| Cihaz değişimi kurtarma akışı + ASIO↔WASAPI | `wasapi-device-hotplug.md` | §2 · §4 |
| ASIO karşılaştırması | `../k036-asio-drivers/index` | §4.1 |
| Buffer yapıları | `../k042-buffer-management/index` | §1–§4 |
| Gecikme bütçesi | `../k043-latency-optimization/index` | §1–§5 |
| Sıcak takma/çıkarma | `../k050-device-hotplug-power/hotplug-detect-flow` | §1–§6 |

### §19.5 K037 kontrol listesi (dosya kapanışı)

- [x] Frontmatter 7 alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [x] `version: 4.0.0` · `updated: 2026-10-06`
- [x] Her dosya ≥500 satır *(bu madde yalnız ilk 2 dosyayı kapsar — genişletme sonrası sayım: `index` 555 · `wasapi-exclusive-shared` 607 · alt dosyalar doğal uzunlukta 188–472; derinlik §x.y.z katmanlarında, satır sayısında zorlanmadı)*
- [x] Wiki-link hedefleri `k036`–`k053` klasör adlarıyla birebir
- [x] Kanıt = gerçek dosya yolu + satır aralığı
- [x] Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`
- [x] Yeni sayı/sürüm/ürün adı üretilmedi
- [x] PowerShell yazma komutu kullanılmadı
- [x] Başka klasöre/dosyaya yazılmadı
- [x] `git commit` atılmadı

### §19.6 K037 terim karşılaştırma tablosu (WASAPI ↔ diğer yollar)

| Terim | WASAPI karşılığı | ASIO karşılığı | ALSA karşılığı | CoreAudio karşılığı |
|-------|------------------|----------------|----------------|---------------------|
| Exclusive sahiplik | Exclusive mode | Tek uygulama sahipliği | `SNDRV_PCM_ACCESS_MMAP_*` | `kAudioDevicePropertyHogMode` |
| Callback dönemi | Event-driven period | `bufferSwitchTimeInfo` | Period/avail pageable | IOProc |
| Öncelik | MMCSS oturum önceliği | RT thread önceliği | RT sched | RT thread önceliği |
| Hata kodu | `0x8889xxxx` | SDK HRESULT benzeri | `-EPIPE` (xrun) | OSStatus |
| Fallback | ASIO → WASAPI Exc → Shared | WASAPI Exclusive | Plug-in layer | HAL üzerinden uygulama katmanı ⚠️ VERIFICATION REQUIRED |
| Metering | Windows Audio Session API | Yok (uygulama ölçer) | `snd_ctl` seviye | `kAudioDevicePropertyVolumeScalar` |

> Son satırdaki CoreAudio karşılıkları `../k038-core-audio-macos/index` dosyasının konusudur; kesin adlar o dosyada kanıtlanır.

### §19.7 K037 hat okuma sırası (öneri)

| Sıra | Ne okunur | Neden |
|------|-----------|-------|
| 1 | Bu dosya §1–§4 | Kapsam ve sayısal tablolar |
| 2 | `wasapi-exclusive-shared.md` §3 | Mod seçimi ve geçiş |
| 3 | `wasapi-exclusive-shared.md` §6–§7 | Hata kodu ve adım adım açılış |
| 4 | `wasapi-exclusive-mode.md` → `wasapi-shared-mode.md` | Modların derin ayrıntısı |
| 5 | `wasapi-buffer-latency.md` → `wasapi-format-negotiation.md` | Açılış ön koşulları (buffer + format) |
| 6 | `wasapi-audio-session.md` → `wasapi-hata-kodlari.md` | Oturum katmanı ve hata sözlüğü |
| 7 | `wasapi-device-hotplug.md` | Cihaz değişimi + ASIO↔WASAPI fallback |
| 8 | `../k036-asio-drivers/index` | Öncelik #1 ile fark |
| 9 | `../k041-driver-stack-mimari/index` | Alt katman |
| 10 | `../k050-device-hotplug-power/hotplug-detect-flow` | Cihaz kaybı/geri gelme (kapsam dışı akış) |
| 11 | `../k043-latency-optimization/latency-chain-budget` | 3 ms / 30 ms bütçe bağlamı |

### §19.8 K037 SSS (özet)

| # | Soru | Cevap | Kaynak |
|---|------|-------|--------|
| Q1 | Exclusive her zaman daha mı iyi? | Düşük gecikme + bit-perfect için evet; çoklu uygulama için Shared gerekir | `wasapi-exclusive.md` L53–L59 |
| Q2 | Öncelik #2 ne demek? | ASIO yoksa ilk tercih WASAPI Exclusive'tir | `k2-surucu/CLAUDE.md` L27–L34 |
| Q3 | Shared neden yavaş? | Windows miksajı ve efekt zinciri ek gecikme üretir | `wasapi-exclusive.md` L53–L59 |
| Q4 | 4 hata kodu neyi ayırır? | Sahiplik, format, oturum ve cihaz durumu sınıflarını | `wasapi-exclusive.md` L124–L130, L177–L183 |
| Q5 | Metering RT'yi etkiler mi? | Etmemelidir; bloke eden okuma guardrail ihlalidir | `k2-surucu/CLAUDE.md` L18–L24 |
| Q6 | SR uyuşmazlığı kabul edilir mi? | Hayır — guardrail reddi şart koşar | `k2-surucu/CLAUDE.md` L18–L24 |
| Q7 | Cihaz kaybında ne olur? | Fallback zinciri + hotplug akışı devreye girer | `../k050-device-hotplug-power/hotplug-detect-flow` |
| Q8 | Period değeri nedir, kaç örnek? | Kapsam içi ancak sayı kaynakta yok → `⚠️ VERIFICATION REQUIRED` | bu dosya §11 |

### §19.9 K037 komşu kapsamlar (bu klasörün yazmadığı yüzeyler)

| Yüzey | Neden burada değil | Nerede |
|-------|--------------------|--------|
| ASIO callback detayı | Ayrı sürücü yolu | `../k036-asio-drivers/asio-exclusive-mode` |
| CoreAudio/ALSA/PipeWire | Ayrı platform API'leri | `k038`–`k040` |
| Ring buffer iç yapısı | Veri yapısı katmanı | `../k042-buffer-management/ring-buffer-lockfree` |
| IRQ/DMA beslemesi | Donanım yolu | `../k048-interrupt-dma-flow/irq-dma-pipeline` |
| Sürücü yığını katmanları | Mimari şema | `../k041-driver-stack-mimari/driver-stack-layers` |
| RT thread/atomik detay | Thread modeli | `../k053-threading-lockfree/thread-pool-rt` |
| Cihaz hotplug akışı | Olay akışı | `../k050-device-hotplug-power/hotplug-detect-flow` |
| Sandbox/namespace | Güvenlik katmanı | `../k051-process-isolation/sandbox-namespaces` |

---

## §20 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — K037 indeksi | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.1–§19.5 eklendi (üretim kararı, kanıt envanteri, risk, okuma haritası, kontrol listesi) | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.6–§19.7 eklendi (terim karşılaştırma, okuma sırası) | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.8–§19.9 eklendi (SSS, komşu kapsamlar) | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | **Multi-md genişletme:** 7 alt dosya üretildi (`wasapi-exclusive-mode` · `wasapi-shared-mode` · `wasapi-buffer-latency` · `wasapi-format-negotiation` · `wasapi-audio-session` · `wasapi-hata-kodlari` · `wasapi-device-hotplug`); §3/§5/§12/§19.1(#7–#9)/§19.4/§19.7/Bağlantılar güncellendi (ekleme — silme yok) | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | **Backup turu (kullanıcı talimatı):** ek kaynaklar okundu ve içeriğe taşındı — `k2-surucu/README.md` L112–L132 (mod tablosu ~15ms/~3ms + akış diyagramı) · `k0-isletim-sistemi/windows-api.md` L91–L184 (Loopback ~15ms · GetMixFormat akışı · RT primitif yasakları) · `windows-core.md` L115–L235 (Registry · WMI · event loop) · `00-enterprise-index.md` L188 (EVENTCALLBACK · MMCSS Pro Audio · IAudioClient3 · WaveRT>WaveCyclic) · `latency-optimization.md` L16–L51 (round-trip 1.76ms · buffer tablosu) | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | **ÇELİŞKİ RAPORU (backup kazandı):** ilk üretimde 4 alt dosyaya backup'a aykırı 3 hata kodu sembolü yazılmıştı — `0x8889000A` (`DEVICE_INVALIDATED`), `0x8889000E` (`OUT_OF_ORDER`), `0x88890018` (`BUFFER_OPERATION_PENDING`); backup `wasapi-exclusive.md L126–L129` = `DEVICE_IN_USE` / `EXCLUSIVE_MODE_NOT_ALLOWED` / `BUFFER_SIZE_ERROR`. Semboller 5 dosyada backup'a hizalandı (`wasapi-hata-kodlari` · `wasapi-format-negotiation` · `wasapi-audio-session` · `wasapi-device-hotplug` · `wasapi-exclusive-shared` §18.2); `wasapi-exclusive-mode.md` L277'de cihaz-kaldırma kodu `⚠️ VERIFICATION REQUIRED` yapıldı. Ayrıca §4.1 Shared gecikme (10–40ms, L53–L59) ↔ README ~15ms farkı §27.3'te raporlandı — **eski satır değiştirilmedi** | Vault Documentation Specialist |
