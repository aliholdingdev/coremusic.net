---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Master Architecture Index"
type: architecture-index
category: architecture
date: 2026-09-20
updated: 2026-09-24
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Master Architecture Index

**Toplam Katman:** 21 (K0-K20) — K16, K17, K18, K19, K20 dahil; beşi BAĞIMSIZ katmandır.
**Toplam Bileşen:** 1,095 (§2 satır toplamı — tek değer)
**Toplam Dosya:** 340 MD (disk sayımı 2026-09-24; başlık, §2 TOPLAM ve §4 özeti tek değer ile hizalı)
**Klasör:** 23 (21 katman klasörü + firmware + adr) + 6 kök dosya

> **Tek Değer Notları (2026-09-24 — 3 turlu mimari tartışma kararı bağlayıcıdır):**
>
> 1. **Dosya sayısı tek değer = 340.** Başlık, §2 TOPLAM ve §4 sayım özeti gerçek disk sayımı (322 katman + 8 firmware + 4 adr + 6 kök = 340) ile hizalandı; envanter kararı 2026-09-24.
> 2. **K16-K20 bileşen toplamı tek değer = 340** (120 + 85 + 45 + 50 + 40). k16-class-ab/README.md içindeki ham BOM sayımı katman bileşen sayımı olarak kullanılmaz; çelişki durumunda geçerli değer 340'tır.
> 3. **K15'in tek hedefi K14'tür.** Üretim/bileşen katmanlarına bağımlılık tamamen kaldırılmıştır (doğrulama: matris §2.1 K15 satırı — üretim sütunları boş).
> 4. **Bağımlılık kuralları kanonik olarak [[katman-baglilik-matrisi]] dosyasındadır**; bu dosya §3 yalnızca bağlantı taşır (kendi zinciri yoktur).
> 5. **ADR-024 birleşimi:** eski sürücü klasörünün 12 dosyası k2-surucu/ klasörüne taşındı (silinmedi); eski klasör artık diskte YOKTUR.

---

## 1. Katman Haritası

    ┌──────────────────────────────────────────────────────────────────────────────┐
    │  K13: CI/CD          │  K12: İZLEME       │  K15: MEDYA & STREAMING          │
    │  GitHub Actions      │  App Logs          │  FFmpeg • FLAC • MP3 • HLS       │
    │  Docker • K8s        │  Prometheus        │  DASH • Podcast • Radio          │
    ├──────────────────────┴────────────────────┴──────────────────────────────────┤
    │  K11: KULLANICI DENEYİMİ                                                     │
    │  ITCSS 9-layer • BEM • Design Tokens • Theme Engine • PWA • A11y             │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K10: UYGULAMA                                                               │
    │  Music • Home • Car • Studio • Admin • Download • Landing • Pro • Media      │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K14: AĞ & İLETİŞİM                                                          │
    │  HTTP/2 • WebSocket • mDNS • DLNA • AirPlay • WebRTC • DNS • VPN             │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K9: API & ROUTING                                                           │
    │  Gateway • BFF • CQRS • Event Bus • SPA Router • OpenAPI • Versioning        │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K8: SERVİS                                                                  │
    │  Control • Media • Audio • Device • Network • AI • Download • Health         │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K7: MIDDLEWARE                                                              │
    │  OriginCheck • CORS • RateLimit • SecurityHeaders • Session • CSRF           │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K6: GÜVENLİK                                                                │
    │  Auth • RBAC • CSRF • CSP • RateLimit • Encryption • Vault • Audit           │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K5: VERİ YÖNETİMİ                                                           │
    │  MySQL 18 DB • Redis • APCu • File System • SQLite • Backup • Archive        │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K4: YAPAY ZEKA                                                              │
    │  Analysis • Recommendation • Auto EQ • Voice • AI Gen • ML • Edge AI         │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K3: SES İŞLEME MOTORU                                                       │
    │  Neva Engine • DSP Chain • Mixer • EQ • Reverb • Compressor • Analyzer       │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K2: SÜRÜCÜ                                                                  │
    │  ASIO • WASAPI • ALSA • PipeWire • CoreAudio • I2S • USB • MIDI              │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K1: DONANIM                                                                 │
    │  XMOS XU316 • PCM3168A • AK4458 • Class AB ×8 • Speakers ×9 • Power          │
    │  firmware/ klasörü = K1.f (mikrodenetleyici firmware katmanı, yerinde kalır)  │
    ├──────────────────────────────────────────────────────────────────────────────┤
    │  K0: İŞLETİM SİSTEMİ                                                         │
    │  Windows • Linux • macOS • RPi5 • ReactOS • Docker • IPC • Memory • Threading│
    └──────────────────────────────────────────────────────────────────────────────┘

    Fiziksel üretim bloğu (beş BAĞIMSIZ katman — hiyerarşik üst üste binme YOKTUR):
    ┌────────────────┬────────────────┬────────────────┬────────────────┬────────────────┐
    │ K16: CLASS AB  │ K17: GÜÇ ±35V  │ K18: TERMAL    │ K19: PCB       │ K20: BOM       │
    │ 120 bileşen    │ 85 bileşen     │ 45 bileşen     │ 50 bileşen     │ 40 bileşen     │
    │ Modüler 8 kanal│ LM5122 ×2      │ SK53-100-SA    │ 6-layer        │ 1775 BOM satır │
    └────────────────┴────────────────┴────────────────┴────────────────┴────────────────┘

---

## 2. Katman Detayları

| Katman | İsim | Bileşen | Dosya | ADR |
|--------|------|---------|-------|-----|
| K0 | İşletim Sistemi | 50 | 15 | ADR-017, ADR-019 |
| K1 | Donanım (+ firmware = K1.f) | 120 | 23 + 8 (firmware) | ADR-038, ADR-089 |
| K2 | Sürücü | 40 | 14 | ADR-017 |
| K3 | Ses Motoru | 50 | 18 | ADR-025, ADR-062 |
| K4 | Yapay Zeka | 50 | 14 | ADR-030 |
| K5 | Veri Yönetimi | 55 | 14 | ADR-040 |
| K6 | Güvenlik | 40 | 16 | ADR-010/011/012/013/022/034 |
| K7 | Middleware | 35 | 14 | ADR-010/011/012/013/022 |
| K8 | Servis | 55 | 14 | ADR-039, ADR-086 |
| K9 | API & Routing | 30 | 14 | ADR-083, ADR-084 |
| K10 | Uygulama | 45 | 17 | ADR-039, ADR-045 |
| K11 | UX | 40 | 16 | ADR-001, ADR-044 |
| K12 | İzleme | 35 | 13 | ADR-006 |
| K13 | CI/CD | 35 | 14 | — |
| K14 | Ağ & İletişim | 40 | 16 | — |
| K15 | Medya & Streaming | 35 | 16 | — |
| K16 | Class AB | 120 | 22 | ADR-089 |
| K17 | Güç Kaynağı | 85 | 16 | ADR-089 |
| K18 | Termal | 45 | 12 | ADR-089 |
| K19 | PCB Tasarım (empedans alt konudur) | 50 | 12 | ADR-089 |
| K20 | BOM & Üretim | 40 | 12 | ADR-089 |
| | **TOPLAM** | **1,095** | **334 klasör + 6 kök = 340** | **30+** |

**§2 Sayım Notları (tek değer disiplini):**

- K16-K20 bileşen toplamı = **340** (tek değer). k16-class-ab/README.md'deki ham parça adedi SSOT dışıdır.
- K16, K17, K18, K19, K20 **beş bağımsız katmandır**; hiçbiri diğerinin alt klasörü değildir (eski §4 notları kaldırıldı).
- K19 = **PCB Tasarım** katmanıdır; controlled-impedance.md yalnızca onun bir alt konusudur.
- Dosya sütunu gerçek disk sayımıdır (2026-09-24 glob); her katman 8-23 dosya aralığındadır, ortalama ~14 dosyadır.
- Eski kaba toplam ifadeleri 340 ile değiştirildi; başlık, §2 TOPLAM ve §4 özeti aynı değeri taşır (adr/ ve 6 kök dosya dahil).

---

## 3. Katman Bağımlılık Matrisi (bağlantı — zincir burada YOKTUR)

**Kanonik kaynak:** [[katman-baglilik-matrisi]] → dosya: .ai/architecture/katman-baglilik-matrisi.md

Bu dosya bağımlılık kuralı ÜRETMEZ; yalnız bağlar (AGENTS.md §25.3 — bağlantı, ikinci kaynak değil).

- **Okuma anlamı (matris kanonik):** ok = "kayan katman → hedefin ARAYÜZÜNE bağımlı".
- Eski ardışık katman zinciri bu dosyadan **kaldırıldı**; matriste karşılığı olmayan izinler (K3 → K4, K11 → K12, K13 → K14 ve K15'in üretim/bileşen katmanlarına izni) artık hiçbir yerde izinli sayılmaz.
- K12 → K8 tek resmi bağımlılıktır (matris §2.2).
- K11 → K12 yalnız **gösterim verisi** yönüdür (üst → alt raporlama), bağımlılık değildir — matris §3.2'de ayrıca yazılır.
- Layer Violation denetimi K matrisine göre yapılır; A0-A5 yalnız alan etiketidir (matris §1.3).
- Yasak çiftler (Layer Violation örnekleri): K0 → K3, K0 → K10, K1 → K5, K3 → K1, K10 → K0 — tam liste matris §5.

---

## 4. Dosya Yapısı (GERÇEK DISKTEN — 2026-09-24 sayımı)

    .ai/architecture/
    ├── index.md                        ← Bu dosya (340 dosyalık envanterin sahibi)
    ├── katman-baglilik-matrisi.md      ← Katman bağımlılık matrisi (kanonik)
    ├── github-referanslari.md          ← GitHub referans tablosu (21 katman bölümü)
    ├── frontend-restructuring-plan.md  ← Frontend yeniden yapılandırma planı
    ├── adlandirma-kurali.md            ← K{n}.a.b.c adlandırma kuralı (SSOT)
    ├── katman-sayim-rehberi.md         ← Düğüm sayım rehberi (ADR-026)
    ├── adr/                            ← ADR-023…026 (4 dosya — frozen)
    │
    ├── (eski sürücü klasörü → YOK — ADR-024 ile k2-surucu/ içine birleşti, 12 dosya taşındı)

### 4.1 Kök Dosyalar (6)

    index.md
    katman-baglilik-matrisi.md
    github-referanslari.md
    frontend-restructuring-plan.md
    adlandirma-kurali.md
    katman-sayim-rehberi.md

### 4.2 Katman Klasörleri (21) + firmware (1) + adr (1) = 23 klasör

**k0-isletim-sistemi/ — 15 dosya**

    README.md
    CLAUDE.md
    index.md
    container-runtime.md
    cross-platform-api.md
    ipc-mekanizmalari.md
    linux-core.md
    macos-core.md
    memory-management.md
    process-isolation.md
    rpi5-core.md
    system-calls.md
    threading-model.md
    windows-api.md
    windows-core.md

**k1-donanim/ — 23 dosya**

    README.md
    CLAUDE.md
    index.md
    ak4458-dac.md
    analog-sinyal-yolu.md
    class-ab-amplifikator.md
    dac-adc-zinciri.md
    diff-pair-input.md
    feedback-network.md
    guc-kaynagi-analog.md
    hoparlor-dizilimi.md
    i2s-interface.md
    konnektorler.md
    koruma-devreleri.md
    mjle21194-93.md
    output-stage.md
    ozet-durum.md
    pcb-tasarim.md
    pcm3168a-dac-adc.md
    termal-yonetim.md
    usb-audio.md
    vas-stage.md
    xmos-xu316.md

**k2-surucu/ — 14 dosya (ADR-024 birleşim HEDEFİ; 12 dosya eski sürücü klasöründen taşındı)**

    README.md
    CLAUDE.md
    index.md                ← taşınan (eski sürücü klasörü index.md)
    alsa-native.md          ← taşınan
    asio-drivers.md         ← taşınan
    bluetooth-a2dp.md       ← taşınan
    buffer-management.md    ← taşınan
    core-audio-macos.md     ← taşınan
    driver-stack-mimari.md  ← taşınan
    latency-optimization.md ← taşınan
    network-audio-drivers.md← taşınan
    pipewire-modern.md      ← taşınan
    usb-audio-class.md      ← taşınan
    wasapi-exclusive.md     ← taşınan

**k3-ses-motoru/ — 18 dosya**

    README.md
    CLAUDE.md
    index.md
    analysis-spectrum.md
    bit-depth-conversion.md
    channel-processing.md
    dsp-chain.md
    dynamics-compressor.md
    effects-chorus-delay.md
    effects-reverb.md
    eq-parametric.md
    format-decoder.md
    mixer-routing.md
    neva-engine-core.md
    playback-gapless.md
    sample-rate-conversion.md
    stream-buffer.md
    surround-decoder.md

**k4-yapay-zeka/ — 14 dosya**

    README.md
    CLAUDE.md
    index.md
    ai-generation.md
    audio-fingerprinting.md
    auto-eq-optimization.md
    edge-ai.md
    lyrics-analysis.md
    ml-infrastructure.md
    mood-classification.md
    music-analysis.md
    recommendation-engine.md
    speech-to-text.md
    voice-assistant.md

**k5-veri-yonetimi/ — 14 dosya**

    README.md
    CLAUDE.md
    index.md
    apcu-memory.md
    backup-strategy.md
    cache-strategy.md
    connection-pooling.md
    data-security.md
    database-registry.md
    file-system-storage.md
    migration-strategy.md
    mysql-18-database.md
    redis-cache.md
    sqlite-local.md

**k6-guvenlik/ — 16 dosya**

    README.md
    CLAUDE.md
    index.md
    audit-logging.md
    authentication-jwt.md
    csrf-protection.md
    csp-policy.md
    encryption-aes256.md
    input-validation.md
    oauth2-integration.md
    password-hashing.md
    rate-limiting.md
    rbac-authorization.md
    security-headers.md
    session-management.md
    vault-secrets.md

**k7-middleware/ — 14 dosya**

    README.md
    CLAUDE.md
    index.md
    compression-middleware.md
    cors-policy.md
    csrf-middleware.md
    error-handler.md
    logging-middleware.md
    origin-check.md
    psr15-pipeline.md
    rate-limit-middleware.md
    request-validation.md
    security-headers-middleware.md
    session-middleware.md

**k8-servis/ — 14 dosya**

    README.md
    CLAUDE.md
    index.md
    ai-service.md
    audio-service.md
    control-service.md
    device-service.md
    download-service.md
    health-service.md
    media-service.md
    network-service.md
    notification-service.md
    search-service.md
    sync-service.md

**k9-api-routing/ — 14 dosya**

    README.md
    CLAUDE.md
    index.md
    api-gateway.md
    bff-pattern.md
    cqrs-pattern.md
    event-bus.md
    graphql-layer.md
    grpc-internal.md
    openapi-spec.md
    request-throttling.md
    response-caching.md
    spa-router.md
    versioning-strategy.md

**k10-uygulama/ — 17 dosya**

    README.md
    CLAUDE.md
    index.md
    accessibility-panel.md
    admin-panel.md
    car-panel.md
    download-panel.md
    home-panel.md
    landing-page.md
    media-panel.md
    mobile-responsive.md
    music-panel.md
    notification-panel.md
    pwa-features.md
    pro-panel.md
    studio-panel.md
    theme-customization.md

**k11-ux/ — 16 dosya**

    README.md
    CLAUDE.md
    index.md
    accessibility-wcag.md
    animation-system.md
    bem-naming.md
    color-system.md
    design-tokens.md
    icon-library.md
    itcss-9-layer.md
    pwa-features.md
    responsive-design.md
    spacing-system.md
    theme-engine.md
    typography-scale.md
    ui-components.md

**k12-izleme/ — 13 dosya**

    README.md
    CLAUDE.md
    index.md
    alerting-rules.md
    app-logs.md
    audit-logs.md
    distributed-tracing.md
    error-tracking.md
    grafana-dashboards.md
    infrastructure-monitoring.md
    log-aggregation.md
    performance-monitoring.md
    prometheus-metrics.md

**k13-cicd/ — 14 dosya**

    README.md
    CLAUDE.md
    index.md
    cd-pipeline.md
    ci-pipeline.md
    docker-build.md
    github-actions.md
    infrastructure-as-code.md
    kubernetes-deploy.md
    monitoring-deployment.md
    rollback-strategy.md
    security-scanning.md
    staging-environment.md
    testing-pipeline.md

**k14-ag/ — 16 dosya**

    README.md
    CLAUDE.md
    index.md
    airplay-streaming.md
    connection-pooling-network.md
    dlna-upnp.md
    dns-resolver.md
    http2-http3.md
    load-balancing.md
    mdns-discovery.md
    multicast-streaming.md
    network-qos.md
    tls-security.md
    vpn-support.md
    webrtc-p2p.md
    websocket-realtime.md

**k15-medya-streaming/ — 16 dosya**

    README.md
    CLAUDE.md
    index.md
    aac-decoder.md
    audio-transcoding.md
    codec-comparison.md
    content-delivery.md
    dash-streaming.md
    flac-support.md
    ffmpeg-pipeline.md
    hls-streaming.md
    media-metadata.md
    mp3-decoder.md
    podcast-support.md
    radio-streaming.md
    streaming-protocol.md

**k16-class-ab/ — 22 dosya (bağımsız katman)**

    README.md
    CLAUDE.md
    index.md
    8-channel-design.md
    bom-classab.md
    current-mirror.md
    darlington-pair.md
    diff-pair-input.md
    feedback-network.md
    frequency-compensation.md
    mjle21193.md
    mjle21194.md
    output-stage.md
    power-supply-requisite.md
    protection-dc-offset.md
    protection-overcurrent.md
    protection-short-circuit.md
    protection-thermal.md
    spice-model.md
    thermal-design.md
    vas-stage.md
    vbe-multiplier.md

**k17-guc-kaynagi/ — 16 dosya (bağımsız katman)**

    README.md
    CLAUDE.md
    index.md
    6s-lipo-battery.md
    battery-management.md
    bulk-capacitors.md
    current-sensing.md
    decoupling-strategy.md
    emc-filtering.md
    lm5122-dual-boost.md
    or-ing-circuit.md
    power-efficiency.md
    power-sequencing.md
    soft-start.md
    thermal-management-power.md
    voltage-regulation.md

**k18-termal/ — 12 dosya (bağımsız katman)**

    README.md
    CLAUDE.md
    index.md
    ambient-temperature.md
    enclosure-thermal.md
    fischer-heatsink.md
    heat-pipe-design.md
    ksd301-cutoff.md
    pwm-fan-control.md
    thermal-pad.md
    thermal-resistance.md
    thermal-simulation.md

**k19-pcb/ — 12 dosya (bağımsız katman; K19 = PCB Tasarım)**

    README.md
    CLAUDE.md
    index.md
    6-layer-stackup.md
    component-placement.md
    controlled-impedance.md
    emc-compliance.md
    pcb-fabrication.md
    power-distribution.md
    signal-integrity.md
    star-grounding.md
    thermal-vias.md

**k20-bom/ — 12 dosya (bağımsız katman)**

    README.md
    CLAUDE.md
    index.md
    capacitor-list.md
    connector-list.md
    cost-estimation.md
    diode-list.md
    ic-list.md
    inductor-list.md
    production-tools.md
    resistor-list.md
    transistor-list.md

**firmware/ — 8 dosya (K1.f — mikrodenetleyici firmware; K1 donanım katmanına bağlıdır, ayrı katman SAYILMAZ)**

    index.md
    bootloader.md
    dsp-firmware.md
    gpio-control.md
    i2s-driver.md
    mcu-support.md
    usb-audio-firmware.md
    xmos-firmware.md

**adr/ — 4 dosya (ADR metinleri — frozen, AGENTS §25.3 kural 2)**

    ADR-023-hibrit-derinlik.md
    ADR-024-surucu-firmware-birlesme.md
    ADR-025-k8-2-k15-siniri.md
    ADR-026-sayim-birimi-5000.md

**Klasör sayım özeti:** 21 katman klasörü + firmware + adr + scripts = 24 klasör · 334 klasör içi dosya + 6 kök dosya = **340 MD** (2026-09-24 envanter; scripts/ 1 .ps1 toplam dışı).

---

## 5. ADR-024 Birleşim Kaydı (eski sürücü klasörü → k2-surucu)

| Alan | Değer |
|------|-------|
| Karar | ADR-024 — eski sürücü klasörü k2-surucu/ ile birleştirilir |
| İşlem | Move (taşima) — HİÇBİR DOSYA SİLİNMEDİ |
| Taşınan dosya | 12 adet (index, alsa-native, asio-drivers, bluetooth-a2dp, buffer-management, core-audio-macos, driver-stack-mimari, latency-optimization, network-audio-drivers, pipewire-modern, usb-audio-class, wasapi-exclusive) |
| Hedefteki mevcut dosyalar | README.md, CLAUDE.md (çakışma YOK — hedef dosya güncellenmesi gerekmedi) |
| Hedef durum | k2-surucu/ = 14 dosya |
| Kaynak durum | Eski sürücü klasörü = 0 dosya (klasör artık referans edilmez) |
| Link taraması | Eski sürücü adı geçen tek link referansı k0-isletim-sistemi/README.md §Link tablosundaydı → k2-surucu/README.md olarak düzeltildi; düzeltilen dosyalarda artık 0 (adlandirma-kurali.md §6 kural dosyası kapsam dışı) |
| firmware/ | YERİNDE KALIR; K1.f olarak referanslanır (8 dosya) |

---

## 6. Doğrulama Listesi (2026-09-24)

- [x] 0 dosya silinmedi (git status delete = 0 — taşımalar Move ile yapıldı)
- [x] K15 izin taraması: üretim/bileşen katmanına izin 0 ✓ (§3 zinciri kaldırıldı; matris §2.1 K15 satırı tek hedef K14 — sütun başlığı hariç)
- [x] §3 artık yalnız [[katman-baglilik-matrisi]] bağlantısı taşır
- [x] §2 TOPLAM = 340 dosya, 1,095 bileşen (tek değer; adr/ 4 + kök 6 dahil)
- [x] K16-K20 = 340 bileşen (tek değer; ham README sayımı reddedildi)
- [x] §4 alt-katman notları kaldırıldı — K17-K20 bağımsız klasör olarak listelendi
- [x] Eksik dosyalar eklendi: frontend-restructuring-plan.md, github-referanslari.md
- [x] Klasör adları diskten: k5-veri-yonetimi, k9-api-routing, k13-cicd, k15-medya-streaming, k16-class-ab, k19-pcb, k20-bom

---

*Master Architecture Index v3.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
