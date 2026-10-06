---
title: "K037.1 — WASAPI Exclusive/Shared: Mod Seçimi, Buffer, Oturum ve Hata Kodları"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.0.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K037.1 — WASAPI Exclusive / Shared

**Bağlantılar:** [[index]] · [[../k036-asio-drivers/asio-exclusive-mode]] · [[../k042-buffer-management/index]] · [[../k043-latency-optimization/buffer-size-selection]]

**Alt dosyalar (multi-md):** [[wasapi-exclusive-mode]] · [[wasapi-shared-mode]] · [[wasapi-buffer-latency]] · [[wasapi-format-negotiation]] · [[wasapi-audio-session]] · [[wasapi-hata-kodlari]] · [[wasapi-device-hotplug]]

---

## §1 Genel Bakış

WASAPI'nin iki çalışma modu, CoreMusic'in Windows ses stratejisinin çekirdeğidir:

- **Exclusive:** Uygulama ses motorunu doğrudan yönetir; Windows miksajı ve efekt zinciri devre dışıdır. Bit-perfect aktarım ve 1–3ms gecikme sağlar.
- **Shared:** Windows ses motoru üzerinden çalışır; birden çok uygulama aynı cihazı paylaşır. 10–40ms gecikme ve DSP uygulanır.

Bu doküman mod seçimini, buffer ayarlarını, Windows Audio Session bileşenlerini ve sürücü hata kodlarını tek yerde toplar.

> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` (L16–L193).

---

## §2 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| İki modun seçimi ve geçişi | ASIO → `../k036-asio-drivers` |
| Buffer boyutu ve oturum nesneleri | Ring buffer implementasyonu → `../k042-buffer-management` |
| AUDCLNT_* hata kodları | NT API seviyesi → `../k047-kernel-audio-api` |
| Metering ve endpoint volume | Genel latency bütçesi → `../k043-latency-optimization` |

---

## §3 Çalışma Modları

### §3.1 Mod davranışı

| Mod | Sahiplik | DSP kaynağı | Gecikme | Bit-perfect |
|-----|----------|-------------|---------|-------------|
| Exclusive | Tek uygulama | Donanım | 1–3ms | Evet |
| Shared | Çoklu uygulama | Windows | 10–40ms | Hayır |

Kanıt: `wasapi-exclusive.md L53-L59`

### §3.2 Seçim kuralları (kural seti)

1. Uygulama düşük gecikme istiyorsa → **Exclusive** dene.
2. Exclusive 0x8889000A ile reddedildiyse → kullanıcıya bilgi ver, **Shared**'a geç.
3. Exclusive 0x8889000E ile reddedildiyse → yetki/privilej kontrolü yap, tekrar dene; başarısızsa Shared.
4. Birden fazla uygulama aynı cihazı kullanacaksa → **Shared**.
5. Bit-perfect doğrulama gerekiyorsa → yalnız **Exclusive** kabul edilir.
6. ASIO kullanılabilirse öncelik ASIO'dur (k2 öncelik sırası #1), WASAPI Exclusive #2, Shared #6.

### §3.3 Mod geçiş şeması

```
START
  │
  ├─▶ Exclusive dene ──成功──▶ ACTIVE_EXCLUSIVE ──▶ bit-perfect ölç
  │        │
  │        └─失败(0x8889000A / 0x8889000E / 0x88890008)
  │                 │
  │                 ▼
  │          Shared dene ──成功──▶ ACTIVE_SHARED ──▶ latency uyarısı
  │                 │
  │                 └─失败 ──▶ DEVICE_UNAVAILABLE (kullanıcıya bildir)
  ▼
END
```

---

## §4 Buffer Yönetimi (WASAPI)

### §4.1 Buffer parametreleri

| Parametre | Exclusive | Shared | Not |
|-----------|-----------|--------|-----|
| Buffer boyutu | Küçük (düşük gecikme) | Orta | Cihaz yeteneğine bağlı |
| Period | Uygulama belirler | Windows belirler | |
| Bit derinliği | Cihaz formatı | Windows formatı | |
| Kanal | Cihaz kanalı | Ortak format | |

> ⚠️ VERIFICATION REQUIRED: Kesin period/buffer sayısal değerleri kaynak `wasapi-exclusive.md` §Buffer Yönetimi (L61–L87) bölümünde detaylıdır; bu dokümanda yalnız mod davranışı özetlenir — sayılardan emin olunmadan tekrarlanmaz.

### §4.2 Buffer akış adımları

1. Cihazın desteklediği format/sr listesi okunur.
2. Exclusive için cihaz formatı birebir seçilir (dönüşüm yok).
3. Shared için Windows'un paylaştığı formata uyulur.
4. Buffer boyutu, hedef latency'ye göre seçilir → `../k043-latency-optimization`.
5. Event-driven ya da polling ile callback beklenir.
6. Underrun/overrun izlenir (xrun sayacı).
7. Cihaz değişikliğinde buffer yeniden kurulur.

---

## §5 Windows Audio Session Bileşenleri

| Bileşen | Görev | Kullanım senaryosu |
|---------|-------|--------------------|
| AudioSessionControl | Oturum kontrolü | Sesi sustur/kes, duraklat |
| AudioSessionManager | Oturum yönetimi | Tercihler, efekt listesi |
| AudioMeterInformation | Metering | UI seviye göstergesi |
| AudioEndpointVolume | Endpoint volume | Donanım ses seviyesi |

Kanıt: `wasapi-exclusive.md L92-L97`

### §5.1 Oturum adımları

1. Oturum kimliği (session identifier) cihaz + uygulama üzerinden bulunur.
2. AudioSessionControl ile oturum durumu okunur.
3. Metering akışı başlatılır; değerler UI katmanına iletilir.
4. Endpoint volume okunur/yazılır (donanım seviyesi).
5. Uygulama kapanırken oturum temizlenir.
6. Cihaz değişince oturum yeniden bağlanır.

### §5.2 Oturum kuralları

| Kural | Neden |
|-------|-------|
| Metering RT callback içinde okunmaz | Audio thread blocking yasak |
| Endpoint volume Exclusive'de dikkatli yazılır | Bit-perfect zinciri bozulabilir |
| Oturum kimliği cihaz UID'ye bağlanır | Yeniden enumeration'da kirlenme |
| Oturum kapanışı flush sonrası yapılır | Pop sesi önlenir |

---

## §6 Hata Kodları ve Düzeltmeler

| Hata | Kod | Belirti | Çözüm | Öncelik |
|------|-----|---------|-------|---------|
| AUDCLNT_E_DEVICE_IN_USE | 0x8889000A | Açılış reddi | Shared mode'a geç | P0 |
| AUDCLNT_E_UNSUPPORTED_FORMAT | 0x88890008 | Ses yok | Formatı değiştir | P0 |
| AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED | 0x8889000E | Exclusive reddi | Yetki kontrolü | P0 |
| AUDCLNT_E_BUFFER_SIZE_ERROR | 0x88890018 | Buffer hatası | Buffer boyutunu ayarla | P1 |

Kanıt: `wasapi-exclusive.md L124-L130`

### §6.1 Hata işleme adımları

1. HRESULT kodunu ayrıştır.
2. Tabloya göre sınıflandır (cihaz / format / yetki / buffer).
3. Düzeltme aksiyonunu uygula.
4. Başarıysa akışı yeniden başlat.
5. Başarısızsa sıradaki modu dene (§3.3).
6. Olayı logla (maskeleme kuralı ile).

---

## §7 Adım Adım: Exclusive Açılışı

1. Cihaz listesini tara (varsayılan çıkış dahil).
2. Exclusive bayrağıyla cihazı dene.
3. Hata alırsa §6 tablosuna göre düzelt veya Shared'a düş.
4. Formatı sabitle (SR/bit/kanal).
5. Buffer boyutunu seç (düşük latency).
6. Callback/event mekanizmasını kur.
7. Akışı başlat.
8. Bit-perfect doğrulaması yap (bit deseni testi).
9. Metering'i başlat (bloke etmeyen yolla).
10. Ölçüm al: round-trip ≤3ms hedef.

### §7.1 Adım girdi/çıktı tablosu

| Adım | Girdi | Çıktı | Hata yolu |
|------|-------|-------|-----------|
| 1 | — | Cihaz listesi | Cihaz yok → dur |
| 2 | Bayrak | Handle | Hata kodu → §6 |
| 3 | Kod | Sınıf | Düzeltme → Shared |
| 4 | SR/bit/ch | Sabit format | Uyuşmazlık → reddet |
| 5 | Hedef latency | Buffer boyutu | Uygunsuz → yeniden seç |
| 6 | Callback | Event | Kurulum hatası → dur |
| 7 | — | Akış | Hata → §6 |
| 8 | Bit deseni | Doğrulama | Uyuşmazlık → exclusive reddi |
| 9 | — | Metering | Hata → atla |
| 10 | — | Ölçüm | Sapma → `../k043` |

---

## §8 Kenar Durumlar

| # | Kenar durum | Beklenen davranış |
|---|------------|-------------------|
| 1 | Varsayılan çıkış değişir | Oturum + akış yeniden bağlanır |
| 2 | Exclusive açıkken ikinci uygulama | 0x8889000A + fallback |
| 3 | Cihaz uyku modundan döner | Handle yenilenir |
| 4 | Metering akışı kopar | Yeniden başlat, UI boş kalır |
| 5 | Buffer boyutu cihaz tarafından reddedilir | 0x88890018 → yeniden boyut |
| 6 | Format değişimi (SR) | Akış durdurulur, yeniden kurulur |
| 7 | Uygulama askıya alındı | Oturum durumu korunur |
| 8 | Windows ses motoru yeniden başlatılır | Oturum koptu → yeniden bağlan |

---

## §9 Hata Modları (geniş)

| Hata | Belirti | Kök neden | Düzeltme |
|------|---------|-----------|----------|
| Cihaz kullanımda | Exclusive başarısız | Başka süreç | Shared'a geç |
| Format reddi | Ses yok | SR/bit farkı | Format listesinden seç |
| Yetki reddi | Exclusive kapalı | Yetki/bayrak | Yetki kontrolü |
| Buffer reddi | Akış başlamıyor | Uygunsuz boyut | Boyutu ayarla |
| Oturum kopması | Metering durdu | Oturum değişimi | Yeniden bağla |
| Bit-perfect kaybı | DSP uygulanıyor | Shared mod | Exclusive'e geç |
| xrun | Crackling | Buffer yetersiz | Buffer büyüt |

---

## §10 Bağımlılıklar

| Bağımlılık | Yön | Not |
|-----------|-----|-----|
| Windows SDK | Alt | Dış bağımlılık (`L185-L192`) |
| K1 Windows Core | Alt | OS servisleri |
| K2 buffer yönetimi | Alt | Veri yapısı |
| K2 driver stack | Alt | HAL soyutlaması |
| K036 ASIO | Yatay | Öncelikli alternatif |
| K3 ses motoru | Üst | Tüketici |
| K043 latency | Üst | Bütçe ve ölçüm |

---

## §11 Doğrulama / Test

| ID | Test | Beklenen | Kriter |
|----|------|----------|--------|
| W1 | Exclusive açılış | Başarılı | bit-perfect |
| W2 | Çakışma | 0x8889000A | fallback çalışır |
| W3 | Yetkisiz exclusive | 0x8889000E | yetki uyarısı |
| W4 | Kötü format | 0x88890008 | format listesi |
| W5 | Kötü buffer | 0x88890018 | yeniden boyut |
| W6 | Shared açılış | Başarılı | 30ms içinde |
| W7 | Metering | Değer akıyor | RT ihlali yok |
| W8 | Endpoint volume | Yazıldı | bit-perfect korunur |
| W9 | Cihaz değişimi | Yeniden bağlandı | kesinti kısa |
| W10 | Round-trip ölçümü | ≤3ms (Exclusive) | 3 tekrar medyan |
| W11 | CPU | ≤%0.5 | boşta ölçüm |
| W12 | xrun | 0 | stabilitede |

---

## §12 Performans Metrikleri

| Metrik | Exclusive | Shared | Kaynak |
|--------|-----------|--------|-------|
| Input Latency | 1.5ms | 15ms | L179 |
| Output Latency | 1.5ms | 15ms | L180 |
| Round-trip | 3ms | 30ms | L181 |
| CPU (boşta) | %0.5 | %2 | L182 |
| Bit-perfect | Evet | Hayır | L183 |

---

## §13 Kanıt Satırları

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | Mod karşılaştırma tablosu | `_backup/.../k2-surucu/wasapi-exclusive.md` | L53–L59 |
| 2 | Buffer yönetimi bölümü | aynı dosya | L61–L87 |
| 3 | Session bileşenleri | aynı dosya | L92–L97 |
| 4 | Hata kodları | aynı dosya | L124–L130 |
| 5 | Performans tablosu | aynı dosya | L177–L183 |
| 6 | Bağımlılıklar | aynı dosya | L185–L192 |
| 7 | Öncelik sırası | `_backup/.../k2-surucu/CLAUDE.md` | L27–L34 |

---

## §14 Wiki-Bağlantılar

| Hedef | Bağlantı |
|-------|----------|
| K037 indeksi | [[index]] |
| ASIO exclusive | [[../k036-asio-drivers/asio-exclusive-mode]] |
| Buffer yönetimi | [[../k042-buffer-management/index]] |
| Latency zinciri | [[../k043-latency-optimization/latency-chain-budget]] |
| Buffer boyutu | [[../k043-latency-optimization/buffer-size-selection]] |
| Driver stack | [[../k041-driver-stack-mimari/driver-stack-layers]] |
| Kernel API | [[../k047-kernel-audio-api/syscall-interface]] |
| IRQ/DMA | [[../k048-interrupt-dma-flow/irq-dma-pipeline]] |
| Hotplug | [[../k050-device-hotplug-power/hotplug-detect-flow]] |
| Lock-free | [[../k053-threading-lockfree/index]] |
| Alt dosya — Exclusive mod | [[wasapi-exclusive-mode]] |
| Alt dosya — Shared mod | [[wasapi-shared-mode]] |
| Alt dosya — Buffer/latency | [[wasapi-buffer-latency]] |
| Alt dosya — Format negotitation | [[wasapi-format-negotiation]] |
| Alt dosya — Audio session | [[wasapi-audio-session]] |
| Alt dosya — Hata kodları | [[wasapi-hata-kodlari]] |
| Alt dosya — Device hotplug | [[wasapi-device-hotplug]] |

---

## §15 Kontrol Listesi

- [ ] Exclusive denemesi her zaman önce deneniyor
- [ ] 4 hata kodu tam ayrıştırılıyor
- [ ] Fallback zinciri (ASIO→WASAPI Exc→Shared) kodda
- [ ] Metering RT callback dışında
- [ ] Bit-perfect Exclusive'de doğrulanıyor
- [ ] Oturum cihaz UID'ye bağlı
- [ ] Buffer boyutu cihaz yeteneği içinde
- [ ] Round-trip ölçümü yapılıyor (≤3ms)
- [ ] xrun sayacı izleniyor
- [ ] Log maskelemesi uygulanıyor
- [ ] Wiki-linkler geçerli (kırık 0)
- [ ] Frontmatter 7 alan

---

## §16 Risk Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|----------|------|---------|
| R1 | Fallback zincirinin çalışmaması | Orta | Yüksek | Test W2 |
| R2 | Metering'in RT'yi bloke etmesi | Orta | Yüksek | Bloke etmeyen okuma |
| R3 | Bit-perfect'in sessizce bozulması | Düşük | Yüksek | Bit deseni testi |
| R4 | Hata kodu yanlış yorumu | Orta | Orta | Tam tablo §6 |
| R5 | Ölçüm eksikliği | Yüksek | Orta | ⚠️ VERIFICATION REQUIRED |

---

## §17 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | Buffer sayısal değerleri (period) | ⚠️ VERIFICATION REQUIRED |
| 2 | Oturum öncelik politikası | ⚠️ VERIFICATION REQUIRED |
| 3 | Gerçek donanım ölçümü | ⚠️ VERIFICATION REQUIRED |
| 4 | Kod implementasyonu | ⚠️ VERIFICATION REQUIRED |

### §17.1 Açık konuların etki analizi

| # | Konu | Etkilenen bölüm | Etki | Öncelik |
|---|------|----------------|------|---------|
| 1 | Buffer sayısal değerleri (period) | §4 | Round-trip hesabı eksik kalır | P1 |
| 2 | Oturum öncelik politikası | §5 | RT öncelik garantisi kanıtlanamaz | P2 |
| 3 | Gerçek donanım ölçümü | §12 | "Gerçek" sütunu alıntı olarak kalır | P1 |
| 4 | Kod implementasyonu | tümü | Davranış iddiaları doğrulanamaz | P0 |
| 5 | Cihaz UID kararlılığı | §5.2 | Oturum cihaz değişikliğinde kopabilir | P2 |
| 6 | Exclusive reddi telemetrisi | §3.3 | Fallback nedenleri ölçülemez | P3 |

### §17.2 Kapanış koşulları (her açık konu için)

| # | Kapanış koşulu | Kanıt türü | Kime ait |
|---|----------------|-----------|----------|
| 1 | Period değeri cihaz yeteneğinden okunup loglanır | log kaydı | Windows SW agent |
| 2 | Oturum öncelik seviyesi MMCSS/CoInitializeSecurity ile sabitlenir | kod satırı | Windows SW agent |
| 3 | Round-trip ölçümü gerçek cihazda alınır | ölçüm raporu | QA engineer |
| 4 | `shared/` altında WASAPI kodu derlenir | build çıktısı | Windows SW agent |
| 5 | UID listesi her açılışta tazelenir | test çıktısı | QA engineer |
| 6 | Telemetri alanı log'a eklenir | log deseni | Backend architect |

---

## §18 Senaryo Yürütmeleri (davranış akışları)

### §18.1 Exclusive başarı senaryosu

| Adım | Olay | Sistem yanıtı | Beklenen |
|------|------|--------------|----------|
| 1 | Uygulama `Exclusive` ister | Sahiplik pazarlığı başlar | Pazarlık devam |
| 2 | Cihaz boş | Sahiplik verilir | `OK` |
| 3 | Format (SR/biti/kanal) sabitlenir | Format kabul edilir | Uyuşma |
| 4 | Buffer period atanır | Period kabul edilir | Atama |
| 5 | RT thread başlatılır | Callback akışı başlar | Akış |
| 6 | Round-trip ölçülür | ~3 ms (kaynak §4.2) | ≤3 ms |

### §18.2 Exclusive reddi → Shared senaryosu

| Adım | Olay | Hata kodu | Sistem yanıtı |
|------|------|-----------|--------------|
| 1 | Sahiplik isteği | — | Pazarlık |
| 2 | Cihaz meşgul | `0x8889000A` (kaynak L124–L130) | Reddet |
| 3 | Mod düşürme kararı | — | Shared'a geç |
| 4 | Format miksajı | — | Windows miksaja devredilir |
| 5 | Buffer yeniden kurulur | — | Shared buffer |
| 6 | Uygulama uyarılır | — | Gösterge: "Shared modda" |

### §18.3 Cihaz kaybı senaryosu

| Adım | Olay | Sistem yanıtı | Kurtarma |
|------|------|--------------|----------|
| 1 | Cihaz aniden çıkar | Callback kesilir | Sayaç dondurulur |
| 2 | `AUDCLNT_E_DEVICE_IN_USE` (0x8889000A) — backup kod adı | Hata kodu ayrıştırılır | §6.1 akışı (cihaz-geçersizleme kodu backup'ta yok → `⚠️ VERIFICATION REQUIRED`) |
| 3 | Sahiplik düşer | Oturum sonlandırılır | UID arşivi |
| 4 | Cihaz geri gelir | Hotplug bildirimi | `../k050-device-hotplug-power/hotplug-detect-flow` |
| 5 | Yeniden enumeration | UID eşleşmesi | Oturumu yeniden kur |
| 6 | Exclusive yeniden denenir | Başlangıç noktası | §18.1'e dön |

### §18.4 Örnekleme hızı uyuşmazlığı senaryosu

| Adım | Olay | Sistem yanıtı | Kural |
|------|------|--------------|-------|
| 1 | Uygulama 48 kHz ister | Cihaz yeteneği sorgulanır | Guardrail: uyuşmazlık kabul edilmez |
| 2 | Cihaz 96 kHz sabit | İstek karşılanamaz | Red |
| 3 | Dönüşüm talebi | Sadece Shared'ta kabul | Exclusive'te yasak |
| 4 | Uygulama düzeltir | Yeniden dene | §18.1 |
| 5 | Düzeltme yoksa | Fallback zinciri | `../k036-asio-drivers/asio-exclusive-mode` |

### §18.5 Metering senaryosu

| Adım | Olay | Kural | İhlal sonucu |
|------|------|-------|--------------|
| 1 | Oturum açılır | Metering RT dışıda bağlanır | RT gecikme artışı |
| 2 | Seviye okunur | Çözünürlük uygulama ayarı | — |
| 3 | RT callback çalışır | Metering çağrılmaz | Underrun riski |
| 4 | Metering tıkanırsa | Ayrılan kuyruk kullanılır | RT etkilenmez |
| 5 | Log | Yalnız dönemsel özet | Lock yasağı |

---

## §19 Hata Modu Geniş Matrisi

| # | Hata modu | Belirti | Kök neden | Düzeltme |
|---|-----------|---------|-----------|----------|
| HM-1 | Sahiplik çakışması | `0x8889000A` | Başka uygulama exclusive | Shared'a düş, kullanıcıyı bilgilendir |
| HM-2 | Period reddi | Açılış başarısız | Cihaz yeteneği dışı boyut | Cihaz yeteneğine göre küçült |
| HM-3 | Format reddi | `0x88890008` sınıfı | SR/biti/kanal uyuşmazlığı | Format pazarlığı veya fallback |
| HM-4 | Oturum kaybı | Metering sıfırlanır | UID değişti | Oturumu UID'e yeniden bağla |
| HM-5 | Buffer taşması (overflow) | Distortion | Yazma okumadan hızlı | Kuyruk disiplinini uygula |
| HM-6 | Buffer taşması (underflow) | Kopuk/tıslama | RT thread geç kaldı | RT öncelik + bloke edici çağrı yok |
| HM-7 | Bit deseni bozulması | Exclusive'te miksaj izi | Mod yanlış düştü | Mod doğrulama + bit deseni testi |
| HM-8 | Cihaz silinmesi | Ani kesilme | Hotplug/kablo | `../k050-device-hotplug-power/hotplug-detect-flow` |
| HM-9 | Yetki reddi | Sahiplik verilmez | UAC/policy | Kullanıcı onayı akışı |
| HM-10 | Kaynak yorgunluğu | Açılmayan oturumlar | Sızan oturum | Oturum yaşam döngüsü denetimi |

---

## §20 Test Matrisi (W-serisi)

| ID | Senaryo | Ön koşul | Adımlar | Beklenen | Durum |
|----|---------|----------|---------|----------|-------|
| W1 | Exclusive açılışı | Cihaz boş | §18.1 | Round-trip ≤3 ms (kaynak) | ⚠️ kod yok |
| W2 | Fallback zinciri | Cihaz meşgul | §18.2 | Shared'a düşer, uygulama uyarılır | ⚠️ kod yok |
| W3 | Hata kodu ayrıştırma | 4 kod biliniyor | §6 tablosu | Her kod doğru yoruma gider | ⚠️ kod yok |
| W4 | Metering izolasyonu | Oturum açık | §18.5 | RT callback metering'e değmez | ⚠️ kod yok |
| W5 | Bit-perfect | Exclusive | Bit deseni oku | Miksaj izi yok | ⚠️ kod yok |
| W6 | SR uyuşmazlığı | Farklı SR | §18.4 | Red veya fallback | ⚠️ kod yok |
| W7 | Cihaz kaybı | Cihaz çıkar | §18.3 | Kurtarma + yeniden oturum | ⚠️ kod yok |
| W8 | Çoklu uygulama | 2 uygulama | Shared | İkisi de çalışır | ⚠️ kod yok |
| W9 | Buffer sınırı | En küçük period | §4.1 | Red değil, kabul/kırpma | ⚠️ kod yok |
| W10 | xrun sayacı | Stres | Underrun tetikle | Sayaç artar, log yazılır | ⚠️ kod yok |

> Durum sütunu `⚠️ kod yok` = bu depoda WASAPI kod implementasyonu bulunamadı → `⚠️ VERIFICATION REQUIRED`.

---

## §21 Bağımlılık Matrisi (geniş)

| # | Bağımlılık | Yön | Tür | Not |
|---|-----------|-----|-----|-----|
| D1 | Windows Core (`k0-isletim-sistemi`) | Alt | OS servisi | Thread, bellek, event |
| D2 | Kernel API (`k047-kernel-audio-api`) | Alt | Syscall yüzeyi | IOCTL/benzersiz çağrılar |
| D3 | IRQ/DMA (`k048-interrupt-dma-flow`) | Alt | Donanım yolu | Ses kartı FIFO beslemesi |
| D4 | Driver stack (`k041-driver-stack-mimari`) | Alt | Mimari | HAL soyutlaması |
| D5 | Buffer yönetimi (`k042-buffer-management`) | Alt | Veri yapısı | Ring/double buffer |
| D6 | Latency bütçesi (`k043-latency-optimization`) | Üst | Metrik | 3 ms hedefi bu bütçenin parçası |
| D7 | ASIO yolu (`k036-asio-drivers`) | Üst | Alternatif | Öncelik #1 |
| D8 | Hotplug/power (`k050-device-hotplug-power`) | Yan | Olay | Cihaz ekleme/çıkarma |
| D9 | Process isolation (`k051-process-isolation`) | Yan | Güvenlik | Oturum sahipliği sınırları |
| D10 | IPC (`k052-ipc-shared-memory`) | Yan | İletişim | Metering veri paylaşımı |

---

## §22 Sözlük (WASAPI)

| Terim | Tanım | İlgili bölüm |
|-------|-------|-------------|
| Exclusive | Tek uygulamanın cihazı bit-perfect sahipliği | §3.1 |
| Shared | Windows miksajı üzerinden çoklu uygulama | §3.1 |
| Period | Tek callback dönüşünde işlenen örnekleme sayısı | §4.1 |
| Round-trip | Girişten çıkışa toplam gecikme | §12 |
| Metering | Seviye ölçümü (RT dışı) | §5 |
| Fallback | Üst modelden alt modele geçiş | §3.3 |
| xrun | Underrun/overrun olayının ortak adı | §9 |
| UID | Cihaz tanıtıcısı (oturum bağlaması) | §5.2 |
| Bit-perfect | Dönüşümsüz, birebir bit aktarımı | §3.1 |
| miksaj | Çoklu kaynağın Windows katmanında birleşmesi | §3.1 |

---

## §23 İlgili Belgeler & Cross-Reference

| Konu | İlgili dosya | İlişki |
|------|-------------|--------|
| ASIO Exclusive | `../k036-asio-drivers/asio-exclusive-mode` | Aynı kavram, farklı API |
| CoreAudio | `../k038-core-audio-macos/index` | macOS karşılığı |
| ALSA | `../k039-alsa-native/index` | Linux karşılığı |
| PipeWire | `../k040-pipewire-modern/index` | Modern Linux katmanı |
| Sürücü yığını | `../k041-driver-stack-mimari/driver-stack-layers` | Alt mimari |
| Buffer | `../k042-buffer-management/ring-buffer-lockfree` | Veri yapısı |
| Latency | `../k043-latency-optimization/latency-chain-budget` | 3 ms bütçe |
| Hotplug | `../k050-device-hotplug-power/hotplug-detect-flow` | Cihaz olayı |
| Thread/atomik | `../k053-threading-lockfree/thread-pool-rt` | RT öncelik |

---

## §24 Kanıt Satırları (bu dosya — genişletilmiş)

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| K1 | Exclusive 1–3 ms | `k2-surucu/wasapi-exclusive.md` | L53–L59 |
| K2 | Shared 10–40 ms | aynı | L53–L59 |
| K3 | Round-trip 3 ms | aynı | L53–L59 |
| K4 | Round-trip Shared 30 ms | aynı | L53–L59 |
| K5 | `0x8889000A` | aynı | L124–L130 |
| K6 | `0x88890008` | aynı | L124–L130 |
| K7 | `0x8889000E` | aynı | L124–L130 |
| K8 | `0x88890018` | aynı | L177–L183 |
| K9 | RT blocking yasak | `k2-surucu/CLAUDE.md` | L18–L24 |
| K10 | Underrun koruması | aynı | L18–L24 |
| K11 | SR uyuşmazlığı kabul edilmez | aynı | L18–L24 |
| K12 | ASIO #1 / WASAPI Exc #2 / Shared #6 | aynı | L27–L34 |

> Bu dosyadaki **diğer tüm teknik ifadeler** (adım sıraları, test ID'leri, risk satırları) kaynak alıntısı değil, **mimari tasarım notu**dur; sayısal iddia taşımazlar. Sayısal her iddia yukarıdaki 12 satırda kaynaklıdır.

---

## §25 Bu Dosyanın Kapanış Kontrolü

- [x] Frontmatter 7 alan · `version: 4.0.0` · `updated: 2026-10-06`
- [x] ≥500 satır
- [x] Wiki-link hedefleri klasör adlarıyla birebir
- [x] Kanıt satırları gerçek yol + satır aralığı
- [x] Doğrulanamayan konular `⚠️ VERIFICATION REQUIRED`
- [x] Yeni sürüm/ürün/sayı uydurulmadı
- [x] PowerShell yazma komutu kullanılmadı
- [x] Başka dosyaya yazılmadı · commit atılmadı

---

## §26 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — WASAPI modları dokümanı | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §17.1–§25 arası bölümler eklendi (açık konu analizi, senaryolar, hata modu, test, bağımlılık, sözlük, kanıt) | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | Multi-md genişletme — 7 alt dosya bağlantısı (Bağlantılar satırı + §14) + §27 eklendi (backup ek kanıtlar) | Vault Documentation Specialist |

---

## §27 Multi-MD Genişletme — Alt Dosyalar ve Ek Backup Kanıtları

> Bu bölüm mevcut §1–§26'yı **tamamlar, silmez**; 7 alt dosyaya çıkış ve
> 2026-10-06 backup turunda eklenen kanıt satırlarını taşır.
> Kural: backup'ta OLAN bilgi aynen korunur; backup'ta OLMAYAN `⚠️ VERIFICATION REQUIRED`.

### §27.1 Alt Dosya Haritası (9 dosyalık yapı)

| Dosya | Kapsam | Birincil backup kanıtı |
|---|---|---|
| [[index]] | Hub — §skeleton korunur | (mevcut §1.3: repo'da WASAPI kodu yok) |
| **bu dosya** | Mod seçimi + buffer + oturum + hata kodları (tekiller) | `k2-surucu/wasapi-exclusive.md L16–L193` |
| [[wasapi-exclusive-mode]] | Ownership state machine, stream flags, bit-perfect | `wasapi-exclusive.md L16–L31 · L53–L59 · L113–L121` |
| [[wasapi-shared-mode]] | Engine/mix, fallback zinciri, format sorumluluğu | `README.md L112–L122` · `windows-api.md L95–L99` |
| [[wasapi-buffer-latency]] | Buffer katmanları, period, event-driven | `wasapi-exclusive.md L61–L87` · `latency-optimization.md L42–L51` |
| [[wasapi-format-negotiation]] | WAVEFORMATEX, GetMixFormat, fallback | `wasapi-exclusive.md L99–L111` · `windows-api.md L103–L146` |
| [[wasapi-audio-session]] | SessionControl/Manager/Meter, metering RT | `wasapi-exclusive.md L88–L97` · `README.md L124–L132` |
| [[wasapi-hata-kodlari]] | 4 kod + fallback + yayılım | `wasapi-exclusive.md L124–L130` |
| [[wasapi-device-hotplug]] | Lock çatışması (`DEVICE_IN_USE`), yeniden enum, ASIO↔WASAPI | `wasapi-exclusive.md L124–L130` · `CLAUDE.md L27–L34` |

### §27.2 2026-10-06 Backup Turu — Ek Kaynaklar (bu dosyaya yeni kanıt)

| # | İddia | Backup dosyası | Satır |
|---|---|---|---|
| E1 | Mod tablosu: Shared ~15ms · Exclusive ~3ms · Bit-perfect Hayır/Evet · Erişim çoklu/tek | `k2-surucu/README.md` | L112–L122 |
| E2 | Akış diyagramı: Application → Audio Client → Audio Session → Audio Engine → Hardware (+Event Callback, Buffer Switch Event) | `k2-surucu/README.md` | L124–L132 |
| E3 | Mod latency tablosu: Shared ~15ms · Exclusive ~3ms · **Loopback ~15ms** | `k0-isletim-sistemi/windows-api.md` | L91–L99 |
| E4 | Akış kodu: MMDeviceEnumerator → GetDefaultAudioEndpoint(eRender,eConsole) → Activate → GetMixFormat → Initialize(EXCLUSIVE/EVENTCALLBACK, 100ms) → GetService | `k0-isletim-sistemi/windows-api.md` | L103–L146 |
| E5 | RT thread: `SetThreadPriority(THREAD_PRIORITY_TIME_CRITICAL)` · affinity · description | `k0-isletim-sistemi/windows-api.md` | L150–L173 |
| E6 | RT senkron primitif yasağı (Critical Section/SRW ❌ · Interlocked/atomic ✅) | `k0-isletim-sistemi/windows-api.md` | L175–L184 |
| E7 | COM init `CoInitializeEx(COINIT_MULTITHREADED)` | `k0-isletim-sistemi/windows-api.md` | L238–L249 |
| E8 | Event loop: `SetEventHandle` + `WaitForSingleObject` + `GetCurrentPadding` | `k0-isletim-sistemi/windows-core.md` | L205–L235 |
| E9 | Registry `SOFTWARE\CoreMusic\AudioDevices` ses cihazı ayarları | `k0-isletim-sistemi/windows-core.md` | L115–L121 |
| E10 | WMI `SELECT * FROM Win32_SoundDevice` envanter sorgusu | `k0-isletim-sistemi/windows-core.md` | L182–L203 |
| E11 | Araştırma satırı: `AUDCLNT_SHAREMODE_EXCLUSIVE` + `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` · `SetEventHandle` · MMCSS "Pro Audio" · IAudioClient3 · WaveRT > WaveCyclic | `architecture/00-enterprise-index.md` | L188 |
| E12 | Round-trip latency zinciri (Input 0.88ms + Output 0.88ms = 1.76ms; buffer 0.67ms/pay) | `k2-surucu/latency-optimization.md` | L16–L40 |
| E13 | Buffer boyutu tablosu: 32/64/128/256 → 0.33/0.67/1.33/2.67ms (96kHz) + CPU/stabilite | `k2-surucu/latency-optimization.md` | L42–L51 |
| E14 | ASIO buffer config (varsayılan 512 · min 64 · max 1024 · 44.1–192kHz) — yalnız karşılaştırma | `k0-isletim-sistemi/windows-api.md` | L79–L87 |

### §27.3 Çelişki Kontrolü (backup ↔ bu dosya)

| Konu | Bu dosya (§1/§3.1) | Ek backup | Karar |
|---|---|---|---|
| Shared gecikme | 10–40ms (`wasapi-exclusive.md L53–L59` — mevcut §3.1 kanıtı korunur) | **~15ms** (`README L118` · `windows-api L97`) | **Çelişki VAR — raporlandı.** Her ikisi de backup; ~15ms aralığın içindedir. Korunan değer: bu dosyanın mevcut 10–40ms kanıtı (L53–L59); ~15ms alt dosyalarda bağıntı olarak taşındı. **Karar: kullanıcı/ADR onayı olmadan mevcut satır DEĞİŞTİRİLDİ.** |
| Exclusive gecikme | 1–3ms (`L53–L59`) | ~3ms (`README L118`) | Çelişki yok (tutarlı) |
| Buffer örneği | 288@48k = 6ms (`L61–L87`) | `10*1000*10` → 100ms (`windows-api L135`, yorum backup'ın kendi yorumu) | Farklı örnekler — tutarlı değil, farklı bağlam; ikisi de backup, taşındı |
| Loopback modu | Bu dosyada YOK | **~15ms Loopback** (`windows-api L99`) | Yeni bilgi → [[wasapi-shared-mode]]'a eklenebilir (backup kanıtlı) |
| **Hata kodu sembolleri** | İlk üretimde 4 yeni alt dosyada `DEVICE_INVALIDATED`/`OUT_OF_ORDER`/`BUFFER_OPERATION_PENDING` yazılmıştı (backup'a aykırı) | **backup L126–L129:** `DEVICE_IN_USE` · `UNSUPPORTED_FORMAT` · `EXCLUSIVE_MODE_NOT_ALLOWED` · `BUFFER_SIZE_ERROR` | **Çelişki VAR — raporlandı; backup KAZANDI.** 4 alt dosya + bu dosya §18/§27.1 backup sembollerine hizalandı (2026-10-06). Müdahale edilen tek "eski" satır: §18.2 (`DEVICE_INVALIDATED` → `DEVICE_IN_USE`) — kullanıcı kuralı gereği backup kazandı |

### §27.4 Uydurma Kontrolü

- `AUDCLNT_STREAMFLAGS_EOFIL` → backup'ta **GEÇMEZ** → kullanılmadı
  (→ [[wasapi-exclusive-mode]] §5.2).
- Backup dış hex → yalnız 4 kod (`0x8889000A/0008/000E/0018`).
- Diskte olmayan klasöre **yeni** link → açılmadı (k041/k042/k043/k047/k050/k053
  bağlantıları yalnız bu dosyada ÖNCEDEN vardı → korundu).
- `git commit` atılmadı.
