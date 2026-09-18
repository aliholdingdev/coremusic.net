---
title: "ADR-089: 1000+ Bileşenli 16 Katmanlı Açık Kaynak Odaklı Mimari ve Push-Pull Ses Güç Topolojisi"
type: adr
category: architecture
date: 2026-09-18
status: accepted
---

# ADR-089: 1000+ Bileşenli 16 Katmanlı Açık Kaynak Odaklı Mimari ve Push-Pull Ses Güç Topolojisi

## Durum
Accepted

## Bağlam
Sistem mimarisinin yüksek modülerliğe (1000+ alt bileşen) ulaştırılması, "Single Source of Truth" bağımlılıklarının açık kaynak (GitHub) standardı etrafında şekillendirilmesi ve "High-Fidelity (Hi-Fi)" ses sistemi gereksinimleri doğrultusunda saf DC güce geçiş ihtiyacı doğmuştur. Özellikle Class AB amfilerin ± (simetrik) voltaj ihtiyacının standart bir "Boost" devresi yerine, voltaj çökmesini önleyen özel SMPS topolojileriyle sağlanması şarttır.

## Kararlar

### 1. 16 Katmanlı ve 1000+ Bileşenli Sistem Modeli
Tüm yazılım ve donanım altyapısı aşağıdaki 16 ana katmana (K0-K15) ayrılmıştır:
- **K0: OS (İşletim Sistemi):** Windows, Linux, macOS, RPi5, ReactOS.
- **K1: Donanım Altyapısı:** MCU'lar, DAC'ler, Hoparlör Matrisi, Güç Elektroniği.
- **K2: Sürücü Katmanı:** ASIO, WASAPI, ALSA, PipeWire, I2S.
- **K3: Ses İşleme Motoru:** DSP, EQ, Crossover, Neva Engine.
- **K4: Yapay Zeka (AI):** Öneri motorları, Müzik Analizi.
- **K5: Veri Yönetimi:** MySQL 9 BCNF, Redis, APCu, Storage.
- **K6: Güvenlik:** Auth, CSRF, RBAC, Encryption.
- **K7: Middleware Pipeline:** OriginCheck, Cors, SessionManager.
- **K8: Servis Katmanı:** Control, Media, Audio, AI Microservices.
- **K9: API & Routing:** API Gateway, BFF, CQRS.
- **K10: Uygulama Katmanı (Frontend):** PWA, Web, Car, TV Panelleri.
- **K11: Kullanıcı Deneyimi (UX):** ITCSS, BEM, Design Tokens, A11y.
- **K12: İzleme & Log (Monitoring):** App/Error Logs, Prometheus, Grafana.
- **K13: CI/CD & Deploy:** GitHub Actions, Playwright, Vitest, Docker.
- **K14: Ağ & İletişim:** HTTP/3, WebRTC, DLNA, AirPlay, mDNS.
- **K15: Medya & Streaming:** FFmpeg, FLAC, HLS, DASH, ID3.

### 2. GitHub Açık Kaynak Referans Kullanımı
Tekerleği yeniden icat etmemek adına, her katman için en çok yıldız alan açık kaynak projeler entegre edilecektir:
- **C++ Ses & DSP:** [JUCE (8.8k+ ★)](https://github.com/juce-framework/JUCE), [EasyEffects (9.8k+ ★)](https://github.com/wwmm/easyeffects)
- **Streaming Backend:** [Koel (17k+ ★)](https://github.com/koel/koel), [Ampache (3.7k+ ★)](https://github.com/ampache/ampache)
- **Tasarım / UI:** Vanilla JS ES6+ ve saf ITCSS yapısı korunacaktır.

### 3. DC-DC Push-Pull (İt-Çek) Güç Topolojisi ve Hi-Fi Filtreleme
**Problem:** Sistem AC kullanmayacak; doğrudan pilden (12V-24V) beslenecektir. Class AB amplifikatörler, AC ses sinyalini bozmadan üretebilmek için simetrik (örneğin +42V, 0V, -42V) gerilime ihtiyaç duyar. Ayrıca Class AB amfiler ani bass vuruşlarında anlık yüksek akım çeker. Standart bir "Boost Converter", tek yönlü voltaj üretir ve ani akım taleplerinde voltajın çökmesiyle seste "çamurlaşmaya" (Distortion/Clipping) neden olur.

**Çözüm (K1 Donanım - Güç Sistemi):**
1. **Push-Pull veya Half-Bridge SMPS Topolojisi:** SG3525, KA3525 veya çok fazlı LM5122 kontrolcü çipleri kullanılarak, özel sarım merkez uçlu (center-tapped) yüksek frekans transformatörü sürülecektir. Bu sayede 12V-24V DC giriş, izole edilmiş ±40V (simetrik) çıkışa dönüştürülecektir.
2. **Anahtarlama Frekansı (Switching Frequency):** Ses frekans bandının (20Hz-20kHz) çok üzerinde, >100kHz frekansta anahtarlama yapılarak sese elektriksel "vızıltı" karışması önlenecektir.
3. **LC Filtre ve Bulk Capacitors:** Çıkış katında devasa kapasitör bankaları (Örn: 4x 10000uF Nichicon Audio Grade) ve seri bobin (Choke) kullanılarak, güç çökmesi önlenecek, Class AB'nin anlık akım talebi bu havuzdan "çamurlaşma" olmadan anında karşılanacaktır.
4. **Referans Devre:** Açık kaynaklı araç amplifikatörü SMPS devreleri (Örn: SG3525 Car Audio DC-DC) baz alınacaktır.

## İlgili Guardrail'ler ve Standartlar
- **SOLID & Clean Architecture:** Katmanlar K0 -> K15 sırasıyla dışarıya bağımlılık olmadan içe doğru konuşur. K15 asla doğrudan K0 ile konuşmaz.
- **Audio Standards:** THD+N <0.01%, SNR >100dB, 24-bit/192kHz sinyal zinciri.
