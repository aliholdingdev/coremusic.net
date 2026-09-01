# BLOK H — DOMAIN-SPESİFİK (320+ Soru)

**5 Ana Domain × 64+ Soru/Alan = Production-Grade Prompt Engineering**

---

## H1 — ELEKTRONİK & GÖMÜLÜ SİSTEMLER (64 Soru)

```
H1.1. Mikrodenetleyici ailesi? 
  STM32F4 / STM32H7 / ESP32 / ESP32-S3 / Arduino / ARM Cortex-M4 / 
  ARM Cortex-M7 / ARM Cortex-A / RISC-V / PIC / Zynq / Custom / None

H1.2. Temel işlemci mimarisi? 
  8-bit / 16-bit / 32-bit / 64-bit / Custom

H1.3. RTOS seçimi? 
  FreeRTOS / Zephyr / RIOT / µC/OS-III / Bare-metal / ThreadX / 
  embOS / NuttX / ChibiOS / Diğer

H1.4. Desteklenen iletişim protokolleri? 
  I2C / SPI / UART / CAN / USB 2.0 / USB 3.0 / Ethernet / WiFi 802.11ac / 
  BLE 4.2 / BLE 5.0+ / LoRa / NB-IoT / LTE-M / Zigbee / Z-Wave

H1.5. Güç kaynağı profili? 
  Şarj edilebilir batarya (mAh) / AC şebeke / Güneş paneli / PoE / 
  Kablosuz şarj / Kinetic harvesting / Karma

H1.6. Hedeflenen pil ömrü? 
  6-12 saat / 1-3 gün / 1-2 hafta / 1-3 ay / 3-6 ay / 6+ ay / Sınırsız

H1.7. Uyku modları? 
  Light sleep / Deep sleep / Hibernation / Off-mode / Hiçbiri / Özel

H1.8. Uyandırma mekanizması? 
  Timer-temelli / GPIO interrupt / Serial/UART / WiFi beacon / BLE advertisement / 
  Sensör tetiklemesi / Hareket deteksiyon / Ses tetiklemesi / Çoklu seçenekli

H1.9. Firmware OTA (Over-The-Air) güncellemesi? 
  Tam destekli / Planlı / Pilot aşaması / Kısıtlı / Desteklenmiyor

H1.10. Güncelleme mekanizması? 
  WiFi / BLE / USB / Ethernet / SD kart / Manufaktura portu / Hiçbiri

H1.11. Önyükleyici (Bootloader)? 
  Özel sertifikalı / Standard / Güvenli (SecureBoot) / İmzalı / 
  Hardware-backed / Açık / Kapalı

H1.12. Flash bellek kapasitesi? 
  <512 KB / 512 KB - 2 MB / 2-8 MB / 8-16 MB / 16-32 MB / >32 MB

H1.13. RAM bellek kapasitesi? 
  <4 KB / 4-8 KB / 8-32 KB / 32-128 KB / 128-512 KB / >512 KB / Harici RAM

H1.14. Harici bellek? 
  EEPROM / Flash / SD kart / NOR Flash / NAND Flash / Hiçbiri / Seçim

H1.15. İşlemci saat hızı? 
  <8 MHz / 8-16 MHz / 16-48 MHz / 48-120 MHz / 120-240 MHz / >240 MHz / Değişken

H1.16. Dinamik frekans ölçekleme? 
  Desteklenmiş / Manuel / Otomatik / Hiçbiri

H1.17. Harici bileşenler & sensörler? 
  Sıcaklık / Nem / Basınç / Işık / Gyro / Akselerometer / Magnetometre / 
  Ses / Kamera / GPS / Radar / LiDAR / Hiçbiri / Özel

H1.18. I2C adresleme şeması? 
  Standard 7-bit / 10-bit adresler / Özel aralık / Dinamik atama

H1.19. I2C hızı? 
  100 kbit/s (Standard) / 400 kbit/s (Fast) / 1 Mbit/s (Fast+) / 
  3.4 Mbit/s (High-speed) / Özel

H1.20. SPI hızı? 
  1 MHz / 5 MHz / 10 MHz / 25 MHz / 50 MHz / >50 MHz / Değişken

H1.21. SPI modu? 
  Mode 0 (CPOL=0, CPHA=0) / Mode 1 (0,1) / Mode 2 (1,0) / Mode 3 (1,1) / 
  Dual / Quad / Oktal

H1.22. UART baud hızı? 
  9600 / 19200 / 38400 / 57600 / 115200 / 230400 / 460800 / 921600 / Özel

H1.23. UART veri bitleri & pariteleri? 
  8-N-1 / 8-O-1 / 8-E-1 / 9-N-1 / Özel kombinasyon

H1.24. CAN bus hızı? 
  125 kbit/s / 250 kbit/s / 500 kbit/s / 1 Mbit/s / Özel

H1.25. CAN-FD desteği? 
  Evet / Hayır / Planlı

H1.26. USB türü? 
  Full-speed (12 Mbps) / High-speed (480 Mbps) / SuperSpeed (5 Gbps) / 
  SuperSpeed+ (10 Gbps) / Type-C / Micro / Mini / Hiçbiri

H1.27. Sertifikasyon gereksinimleri? 
  CE / FCC / RoHS / UL / UKCA / IC / NCC / Hiçbiri / Özel / Tümü

H1.28. EMC/EMI testleri? 
  Evet zorunlu / Önerilen / İsteğe bağlı / Hiçbiri

H1.29. ESD koruması? 
  ±200 V / ±500 V / ±1000 V / ±2000 V / Yapısalı / Hiçbiri

H1.30. Hata düzeltme mekanizması? 
  CRC / Hamming / Checksum / Forward error correction / Hiçbiri / Özel

H1.31. Debug arabirimleri? 
  JTAG / SWD / Serial port / OpenOCD / GDB / Hiçbiri / Seçili

H1.32. Izleme kapabiliteleri? 
  Gerçek-time debugging / Breakpoints / Watchpoints / Hardware trace / Hiçbiri

H1.33. Watchdog timer? 
  Etkinleştirilmiş / Devre dışı / Konfigüre edilebilir zaman aşımı / Hiçbiri

H1.34. Watchdog zaman aşımı değeri? 
  <100 ms / 100-500 ms / 500 ms - 1 sn / 1-10 sn / >10 sn / Özel

H1.35. Hata kurtarma mekanizması? 
  Otomatik reset / İşletim sistemi yönetimi / Manual intervention / Hiçbiri

H1.36. Bekleme modlarından çıkış? 
  Normal durum / Özel prosedür / Gerekli yeniden kalibrasyonu

H1.37. Termal yönetim? 
  Pasif / Aktif soğutma / Isı tüpü / Fanlar / Hiçbiri

H1.38. İşletme sıcaklık aralığı? 
  0-40°C / 0-70°C / -40 - +85°C / -40 - +125°C / Özel

H1.39. Nem dayanıklılığı? 
  Kuru ortam / <80% RH / <90% RH / Nem/yağış korumalı / IP65 / IP67 / IP68

H1.40. Titreşim dayanıklılığı? 
  Hız / 2-500 Hz / Özel profiline göre / Tämamen test edilmiş

H1.41. Şok dayanıklılığı? 
  <50 G / 50-100 G / >100 G / MIL-STD-810 / Özel

H1.42. Mekanik koruma sınıfı? 
  IP20 / IP54 / IP65 / IP67 / IP68 / NEMA TS2 / NEMA 4X

H1.43. Yazılım versiyonlama? 
  Semantic versioning / Git hash / Firmware version register / Özel

H1.44. Yazılım güncellemesi denetimi? 
  Otomatik doğrulama / Manuel inceleme / Hiçbiri

H1.45. Yedek/rollback mekanizması? 
  Dual boot / A/B partitions / Hiçbiri / Kısıtlı

H1.46. Elektronik İmza desteği? 
  RSA-2048 / RSA-4096 / ECC / SHA-256 / SHA-512 / Hiçbiri

H1.47. Bellek koruma birimleri (MPU)? 
  ARM M4 MPU / Özel koruma / Hiçbiri

H1.48. Stack overflow koruması? 
  Hardware / Software check / Hiçbiri

H1.49. Bellek sızıntısı yönetimi? 
  Static allocation / Malloc/free izleme / RTOS yönetimi / Hiçbiri

H1.50. Çoklu çekirdeği destekleme? 
  Tek çekirdek / Dual-core / Quad-core / >4 çekirdek / Asimetrik / Simetrik

H1.51. Çekirdekler arası iletişim? 
  Shared memory / Message passing / Mailbox / Interrupt / Hiçbiri

H1.52. Gerçek-zamanlı kısıtlamalar? 
  Hard real-time (<1ms) / Firm real-time (1-10ms) / Soft real-time / Hiçbiri

H1.53. Çoklu voltaj kaynakları? 
  3.3V / 5V / Ayarlanabilir / Karma / Hiçbiri

H1.54. Power supply ripple toleransı? 
  <5% / <10% / <20% / Özel / Hiçbiri

H1.55. Gürültü filtreleme? 
  Kapasitiv / Ferrit boncuk / Ferrit halka / Özel devre / Hiçbiri

H1.56. EMI shielding? 
  Evet gerekli / İsteğe bağlı / Dahili / Harici / Hiçbiri

H1.57. Crystal/Oscillator toleransı? 
  ±100 ppm / ±50 ppm / ±20 ppm / ±10 ppm / Özel

H1.58. RTC (Real-time clock)? 
  Harici kristal / Dahili / RTOS timer / Hiçbiri / Seçmeli

H1.59. Batarya yedekli RTC? 
  Evet / Hayır / Seçmeli

H1.60. Kalibrasyon ihtiyacı? 
  Başlangıç yalnızca / Periyodik / Hiçbiri / Özel

H1.61. Firmware optimizasyon hedefi? 
  Daha hızlı (performance) / Daha küçük (size) / Daha az güç / Denge / Hiçbiri

H1.62. Bellek sıkıştırması? 
  LZ4 / DEFLATE / Custom / Hiçbiri

H1.63. Kriptografi ivme? 
  AES-NI / SHA / ECC / Yazılım yalnızca / Hiçbiri

H1.64. Uzaktan yönetim? 
  SSH / VPN / Serial over network / MQTT / CoAP / Hiçbiri / Seçmeli
```

---

## H2 — SES & AUDIO İŞLEME (64 Soru)

```
H2.1. SNR (Signal-to-Noise Ratio) hedefi? 
  >60 dB / >80 dB / >100 dB / >120 dB / >130 dB / Özel

H2.2. THD+N (Total Harmonic Distortion + Noise) hedefi? 
  <0.5% / <0.1% / <0.01% / <0.001% / <0.0001% / Özel

H2.3. Frekans yanıtı aralığı? 
  20 Hz - 20 kHz / 20 Hz - 40 kHz / 5 Hz - 200 kHz / Özel / Full range

H2.4. Ses API seçimi? 
  ASIO / WASAPI / CoreAudio / ALSA / JACK / PortAudio / miniaudio / 
  SDL / DirectSound / Hiçbiri / Platform-bağımsız abstraction

H2.5. Hedeflenen gecikme? 
  <2 ms / <5 ms / <10 ms / <20 ms / <50 ms / >50 ms / Kısıtlama yok

H2.6. Çalışma hızı (Sample rate)? 
  44.1 kHz / 48 kHz / 96 kHz / 192 kHz / 384 kHz / Değişken / Dinamik

H2.7. Bit derinliği? 
  16-bit int / 24-bit int / 32-bit int / 32-bit float / 64-bit float / Seçili

H2.8. Kanal sayısı? 
  Mono (1) / Stereo (2) / 2.1 / 5.1 Surround / 7.1 Surround / 
  Atmosferik 3D (Atmos, Auro-3D) / 64+ kanal / Özel

H2.9. Kanal etiketleme? 
  WAVE_FORMAT_PCM / WAVE_FORMAT_IEEE_FLOAT / Custom / Hiçbiri

H2.10. DSP bileşenleri? 
  EQ (Parametric / Grafik) / Kompresör / Reverb / Delay / Limiter / 
  Gate / Chorus / Flanger / Phaser / Distortion / Exciter / De-esser / 
  Stereo widening / Metering / Hiçbiri / Tam suite

H2.11. EQ modu? 
  31-band grafik / Parametric (peaking, shelf) / Dynamic / Linear-phase / 
  Minimum-phase / Özel

H2.12. EQ karakteristiği? 
  Müzik / Ses post-prodüksiyon / Telecom / Tıbbi / Özel / Türlerine bağlı

H2.13. Kompresyon oranı? 
  2:1 / 4:1 / 8:1 / 16:1 / >16:1 / Yumuşak / Hiçbiri

H2.14. Kompresyon look-ahead? 
  Yok / 1-5 ms / 5-20 ms / 20-100 ms / >100 ms

H2.15. DSP buffer boyutu? 
  64 / 128 / 256 / 512 / 1024 / 2048 / 4096 / Özel / Dinamik

H2.16. Buffer sayısı (triple, quad)? 
  Double buffering / Triple buffering / Quad+ / Özel / Dinamik

H2.17. Ses codec seçimi? 
  PCM (WAV) / MP3 / AAC / HE-AAC / Opus / FLAC / Vorbis / WMA / ALAC / 
  AC3 / Dolby Digital+ / DTS / Custom

H2.18. Codec bitrate? 
  128 kbit/s / 192 kbit/s / 256 kbit/s / 320 kbit/s / Lossless / Hiçbiri / Seçili

H2.19. Codec gecikme? 
  <10 ms / <50 ms / <100 ms / <500 ms / >500 ms / Kısıtlama yok

H2.20. Kodlama/Dekodlama hız? 
  Real-time / Faster-than-real-time / Slower-than-real-time / Özel

H2.21. Gerçek-zamanlı DSP işleme? 
  Lock-free algoritma / Critical section (mutex) / Disable interrupts / Yazılım / Hiçbiri

H2.22. Worker thread sayısı? 
  Tek thread / Dual-thread / Quad-thread / CPU cores kadar / Özel

H2.23. Thread affinite? 
  Çekirdek sabitleme / Dinamik / Hiçbiri

H2.24. Ses giriş kaynakları? 
  Mikrofon / USB ses aygıtı / Ses kartı / Network stream / File / 
  Loopback / Hiçbiri / Seçili

H2.25. Ses çıkış hedefleri? 
  Hoparlör / Kulaklık / USB aygıtı / Ses kartı / Network stream / 
  File (record) / Virtual output / Hiçbiri / Seçili

H2.26. Giriş seviyesi ölçüm? 
  VU metre / Peak metre / RMS / LUFS / PPM / Hiçbiri / Seçili

H2.27. Çıkış seviyesi ölçüm? 
  VU metre / Peak metre / RMS / LUFS / PPM / Hiçbiri / Seçili

H2.28. Phantom güç (Condenser mikrofon)? 
  +48V / +12V / Ayarlanabilir / Yoktur / Seçmeli

H2.29. Mikrofon impedansı? 
  Yüksek (>10 kΩ) / Düşük (<2 kΩ) / Hattını eşleştir / Seçili

H2.30. Ses kartı örnekleme senkronizasyonu? 
  Master mode / Slave mode / Word clock / AES/EBU / Diğer / Hiçbiri

H2.31. Jitter kontrolü? 
  PLL (Phase-locked loop) / Dijital filtre / Hiçbiri / Özel

H2.32. Ses gösterimi (Visualization)? 
  Waveform / Spectrum analizi (FFT) / Spektrogram / Metering / Hiçbiri

H2.33. FFT boyutu? 
  256 / 512 / 1024 / 2048 / 4096 / 8192 / 16384 / 32768 / Dinamik

H2.34. FFT pencere fonksiyonu? 
  Hann / Hamming / Blackman / Welch / Custom / Hiçbiri

H2.35. FFT güncelleme hızı? 
  10 Hz / 30 Hz / 60 Hz / 144 Hz / >144 Hz / Kustomize edilebilir

H2.36. Spektrum gösterimi renk şeması? 
  Isıl harita / Siyah-beyaz gradient / RGB custom / Hiçbiri

H2.37. Stereo görüntüsü? 
  Mono / Stereo genişletme / L-R eğimi gösterimi / Geniş panorama / Hiçbiri

H2.38. Ses kaydetme (Recording)? 
  Çoklu format support / Tek format / Gerçek-zaman / Post-processing / Hiçbiri

H2.39. Kayıt bit derinliği? 
  16-bit / 24-bit / 32-bit float / 64-bit float / Seçili / Orijinal kalıt

H2.40. Kayıt sıkıştırması? 
  Kayıpsız (FLAC) / Kayıplı (MP3/AAC) / Hiçbiri / Seçmeli

H2.41. Ses tanıma (Voice recognition)? 
  Desteklenmiş / Planlı / Hiçbiri

H2.42. Dil desteği (Voice)? 
  Türkçe / İngilizce / Çokkültürlü / Hiçbiri / Seçili

H2.43. Ses komutu yürütme? 
  Gerçek-zamanlı / Batch işleme / Hiçbiri

H2.44. Echo cancellation (AEC)? 
  Desteklenmiş / Planlı / Hiçbiri / Opsiyonel

H2.45. Gürültü azaltma (Noise reduction)? 
  Spectral subtraction / Wiener filter / Deep learning / Hiçbiri

H2.46. Gürültü azaltma derecesi? 
  Agresif / Orta / Hafif / Kapalı

H2.47. Konuşma geliştirme? 
  Desteklenmiş / Planlı / Hiçbiri

H2.48. Mekansal ses (Spatial audio)? 
  3D Surround / HRTF (Head-related transfer function) / Binaural / 
  Ambisonics / WFS (Wave field synthesis) / Hiçbiri

H2.49. Mekansal ses kodlama? 
  Dolby Atmos / DTS:X / Auro-3D / Custom / Hiçbiri

H2.50. HRTF kişileştirmesi? 
  KEMAR / Ölçülen / Kişiye özel / Hiçbiri

H2.51. Stereo genişletme? 
  Desteklenmiş / Planlı / Hiçbiri

H2.52. Bas yönetimi? 
  Subwoofer routing / Low-pass filter / Dinamik bas / Hiçbiri

H2.53. Bas frekansı? 
  <20 Hz / 20-50 Hz / 50-100 Hz / 100+ Hz / Hiçbiri

H2.54. Dinamik işleme? 
  Multiband compression / Lookahead limiting / Hiçbiri / Seçili

H2.55. Crossover tasarımı? 
  Butterworth / Linkwitz-Riley / Bessel / Custom / Hiçbiri

H2.56. Yüksek-geçiş filtresi? 
  20 Hz / 50 Hz / 100 Hz / 200 Hz / Özel / Hiçbiri

H2.57. Alçak-geçiş filtresi? 
  5 kHz / 10 kHz / 15 kHz / 20 kHz / Özel / Hiçbiri

H2.58. Filter derecesi? 
  1st order (-20 dB/oct) / 2nd order (-40 dB/oct) / 3rd order / 4th order+ / Özel

H2.59. Phase coherence? 
  Korunan / Normalize / Hiçbiri

H2.60. Ses kalibrasyonu? 
  Otomatik (SPL metre) / Manuel / Hiçbiri

H2.61. Kalibrasyondan sonra dosya tasarrufu? 
  Evet / Hayır / Seçmeli

H2.62. A/B karşılaştırması? 
  Desteklenmiş / Planlı / Hiçbiri

H2.63. Metering ve logging? 
  Gerçek-zamanlı / Kaydedilen / Hiçbiri

H2.64. CPU yükü? 
  <5% / 5-10% / 10-20% / 20-50% / >50% / Ölçülmüş / Dinamik
```

---

## H3 — MAKİNE ÖĞRENMESİ & YP (64 Soru)

```
H3.1. Framework seçimi? 
  TensorFlow / Keras / PyTorch / JAX / MXNet / ONNX / HuggingFace / 
  MLflow / Fast.ai / Hugging Face Transformers / SciKit-Learn / 
  CatBoost / XGBoost / LightGBM / Özel / Hiçbiri

H3.2. Framework versiyonu? 
  Son stable (TF 2.x, PyTorch 2.x) / LTS / Pinned / Eski uyumluluğu

H3.3. Model türü? 
  Sınıflandırma (Classification) / Regresyon / NLP / Bilgisayarla Görü / 
  Zaman serisi / Multimodal / Derin öğrenme / Ensemble / Hiçbiri / Seçili

H3.4. Sınıflandırma problemi? 
  Binary / Multi-class / Multi-label / Hiyerarşik / Hiçbiri / Seçili

H3.5. Eğitim yaklaşımı? 
  In-house eğitim / Yönetilen hizmet / Pre-trained model / 
  Transfer learning / Fine-tuning / Few-shot / Zero-shot

H3.6. Inference (Çıkarım) türü? 
  Batch (offline) / Real-time (online) / Edge (mobil) / 
  Serverless (Lambda/Cloud Functions) / Streaming / Hiçbiri

H3.7. Model boyutu? 
  <1 MB / 1-10 MB / 10-100 MB / 100 MB - 1 GB / 1-5 GB / >5 GB

H3.8. Inference latency hedefi? 
  <1 ms / <10 ms / <50 ms / <100 ms / <1 sn / Kısıtlama yok

H3.9. Throughput hedefi? 
  <10 req/sn / 10-100 req/sn / 100-1000 req/sn / 1000+ req/sn / Custom

H3.10. GPU gereksinimi? 
  Zorunlu / İsteğe bağlı / CPU-only / Multi-GPU / TPU

H3.11. GPU tipi? 
  NVIDIA (CUDA) / AMD (ROCm) / Apple Silicon / Intel Arc / 
  Startup / Custom / Hiçbiri

H3.12. CUDA sürümü? 
  11.x / 12.x / En son / Pinned / Uyumluluk modu

H3.13. cuDNN sürümü? 
  8.x / 9.x / Compatible / Pinned

H3.14. Dağıtılı eğitim? 
  Desteklenmiş / Planlı / Hiçbiri

H3.15. Veri parallelizmi? 
  Evet / Hayır / Seçmeli

H3.16. Model parallelizmi? 
  Evet / Hayır / Gerekli / Seçmeli

H3.17. Mixed precision training? 
  FP16 / BF16 / TF32 / Hiçbiri / Otomatik

H3.18. Nicelik (Quantization)? 
  Int8 / Int4 / Float16 / Dynamic / Static / Hiçbiri

H3.19. Nicelik kalibrasyonu? 
  Post-training / QAT (Quantization-aware training) / Hiçbiri

H3.20. Model budama (Pruning)? 
  Yapılandırılmış / Yapılandırılmamış / Nicelik-sonrası / Agresif / Orta / Hafif / Hiçbiri

H3.21. Budama oranı? 
  <10% / 10-30% / 30-50% / >50% / Hiçbiri

H3.22. Bilgi distilasyonu? 
  Desteklenmiş / Planlı / Hiçbiri

H3.23. Model sıkıştırması kombinasyonu? 
  Distillation + Pruning / Quantization alone / Hybrid / Hiçbiri

H3.24. Model versiyonlama? 
  Semantic versioning / Git hash / Model registry / Hiçbiri / Seçili

H3.25. Model versiyonlama sistemi? 
  MLflow / HuggingFace / DVC (Data Version Control) / Custom / None

H3.26. Model registry? 
  MLflow Model Registry / HuggingFace Hub / Custom storage / Hiçbiri

H3.27. Model artefakt depolaması? 
  S3 / GCS / Azure Blob / Local filesystem / DVC remote / Custom

H3.28. Model imzalama & doğrulama? 
  Desteklenmiş / Planlı / Hiçbiri

H3.29. Model metrikleri izleme? 
  Accuracy / Precision / Recall / F1 / AUC-ROC / BLEU (NLP) / 
  Perplexity / Custom / Hiçbiri / Seçili

H3.30. Data drift detection? 
  Desteklenmiş / Planlı / Hiçbiri / Manual review

H3.31. Model drift detection? 
  Desteklenmiş / Planlı / Hiçbiri / Threshold-based

H3.32. Model performance degredation alert? 
  Automatic / Manual / Hiçbiri / Threshold

H3.33. Model retraining plan? 
  Günlük / Haftalık / Aylık / İstek üzerine / Hiçbiri / Stratejik

H3.34. Retraining trigger? 
  Scheduled / Drift-based / Performance threshold / Manual / Özel

H3.35. A/B testing modelleri? 
  Desteklenmiş / Planlı / Hiçbiri

H3.36. Canary deployment (modeller)? 
  Desteklenmiş / Planlı / Hiçbiri

H3.37. Model rollback capability? 
  Otomatik / Manuel / Hiçbiri / Limited

H3.38. Eğitim veri boyutu? 
  <10K samples / 10K-100K / 100K-1M / 1M-10M / >10M

H3.39. Doğrulama veri split? 
  80-20 / 70-30 / 90-10 / Cross-validation / Stratified / Hiçbiri

H3.40. Test veri istatistikleri? 
  Eğitim setiyle aynı / Farklı dağılım / Kontrol edilmiş / Üretilmiş / Gerçek

H3.41. Veri artırma (Augmentation)? 
  Görüntü (rotation, flip) / NLP (paraphrase) / Ses (pitch shift) / 
  Custom / Hiçbiri / Seçili

H3.42. Sınıf dengeleme? 
  Weighted loss / Resampling / SMOTE / Undersampling / Hiçbiri

H3.43. Feature engineering? 
  Manual / Otomatik / Domain-specific / Hiçbiri

H3.44. Feature selection? 
  Correlation / Mutual information / Permutation importance / Hiçbiri

H3.45. Outlier handling? 
  Kaldır / Clipping / Transformation / Hiçbiri

H3.46. Missing value strategy? 
  Sil / Ortalama / Medyan / Mode / Interpolasyon / Model-based / Hiçbiri

H3.47. Hiperparametre optimizasyonu? 
  Grid search / Random search / Bayesian / HyperBand / Population-based / 
  Manual tuning / Hiçbiri

H3.48. Learning rate scheduler? 
  Constant / Step decay / Exponential / Cosine annealing / Custom / Hiçbiri

H3.49. Early stopping? 
  Desteklenmiş / Planlı / Hiçbiri / Eğitim uzunluğu sabit

H3.50. Regularizasyon? 
  L1 / L2 / Dropout / Batch normalization / Layer normalization / 
  Data augmentation / Hiçbiri / Seçili

H3.51. Batch size stratejisi? 
  Small (16-32) / Medium (64-128) / Large (256+) / Adaptive / Değişken

H3.52. Optimizer seçimi? 
  SGD / Adam / RMSprop / AdaGrad / AdaDelta / Lamb / Özel

H3.53. Loss function? 
  CrossEntropy / MSE / MAE / Custom / Multi-task / Weighted

H3.54. Model yorumlanabilirliği? 
  SHAP / LIME / Attention weights / Feature importance / Hiçbiri

H3.55. Fairness & bias check? 
  Auditted / Planlı / Hiçbiri / Aksiyonal plan yok

H3.56. Model ethics review? 
  Evet, resmi / Ad-hoc / Hiçbiri / Planlanmış

H3.57. Explainability düzeyi? 
  Full transparency / Partial / Black-box / Hiçbiri

H3.58. Reproducibility? 
  Deterministik seed / Tamamen tekrarlanabilir / Stokastik / Hiçbiri

H3.59. Model documentation? 
  Kapsamlı / Temel / Hiçbiri / Auto-generated

H3.60. Model card (HuggingFace format)? 
  Evet, tam / Kısmi / Hiçbiri / Planlı

H3.61. Training metrics logging? 
  TensorBoard / Weights & Biases / MLflow / Özel dashboard / Hiçbiri

H3.62. Experiment tracking? 
  Systematic (MLflow, W&B) / Spreadsheet / Notebook / Hiçbiri / Manual

H3.63. Inference serving? 
  TensorFlow Serving / TorchServe / Triton / KServe / FastAPI / 
  Custom REST / Hiçbiri

H3.64. Model hotloading? 
  Supported / Planned / Downtime required / Hiçbiri
```

---

## H4 — FINTECH & ÖDEME SİSTEMLERİ (64 Soru)

```
H4.1. Ödeme işleme yaklaşımı? 
  Kendi altyapısı (in-house) / Üçüncü taraf gateway (Stripe, Square) / 
  Hybrid / Merupakan bank / Hiçbiri

H4.2. PCI DSS uyumluluğu seviyesi? 
  Level 1 (<6M transaction) / Level 2 / Level 3 / Level 4 / Exempt / Hiçbiri

H4.3. PCI DSS sertifikasyon durumu? 
  Sertifikalı / Nitelenmiş tarayıcı sonuçları / Kendi değerlendirmesi / Hiçbiri

H4.4. Desteklenen ödeme yöntemleri? 
  Kredi/Banka kartı / ACH (Automated Clearing House) / Tel transfer / 
  Kripto para / Banka havalesi / E-cüzdan / Çek / Apple Pay / Google Pay / 
  Özel / Hiçbiri

H4.5. Kripto para desteği? 
  Bitcoin / Ethereum / USDC / Stablecoin / Özel token / Hiçbiri / Planlı

H4.6. Kredi kartı ağları? 
  Visa / Mastercard / American Express / Discover / JCB / UnionPay / 
  Mir / RuPay / Özel / Hiçbiri

H4.7. Debit kart desteği? 
  Evet / Hayır / Seçmeli

H4.8. Ön ödemeli kart? 
  Evet / Hayır / Seçmeli

H4.9. Dolandırıcılık tespiti yöntemi? 
  3D Secure 2.0 / ML modeli / Rule-based / Hiçbiri / Karma

H4.10. Dolandırıcılık tespiti latency hedefi? 
  <50 ms / <100 ms / <200 ms / <500 ms / <1 sn / Kısıtlama yok

H4.11. Dolandırıcılık riski skoru? 
  0-100 ölçeği / Kategori (Düşük/Orta/Yüksek) / Hiçbiri / Özel

H4.12. False positive oranı? 
  <0.5% / <1% / <2% / >2% / Ölçülmez

H4.13. Tokenization? 
  Desteklenmiş / Planlı / Hiçbiri / Third-party

H4.14. Token süresi geçmesi? 
  24 saat / 30 gün / 1 yıl / Sınırsız / Özel

H4.15. One-click checkout? 
  Desteklenmiş / Planlı / Hiçbiri

H4.16. Saved payment methods? 
  Desteklenmiş / Planlı / Hiçbiri

H4.17. KYC/AML gereklilik? 
  Zorunlu / İsteğe bağlı / Eşik-tabanlı / Hiçbiri / Düzenleyici

H4.18. KYC/AML sağlayıcı? 
  Stripe / Onfido / Sum&Substance / Jumio / Custom / In-house / Hiçbiri

H4.19. KYC dokümantasyon? 
  Pasaport / Ehliyet / Nüfus cüzdanı / Kamu hizmeti faturası / 
  Selfie verification / Liveness check / Özel

H4.20. KYC/AML check latency? 
  Real-time (<1 sn) / <5 sn / <1 dakika / <1 saat / Batch / Hiçbiri

H4.21. AML watchlist kontrolü? 
  OFAC / EU sanctions / UK sanctions / UN / Custom lists / Hiçbiri / Seçili

H4.22. Transaction limit? 
  Günlük / Aylık / Per-transaction / Unlimited / Tier-based / Özel

H4.23. Decline reason communication? 
  Müşteriye açık / Merchant-only / Hiçbiri / Encrypted

H4.24. Ödeme retry mekanizması? 
  Automatic / Manual / Özel logic / Hiçbiri

H4.25. Retry count sınırı? 
  1 / 2 / 3 / Unlimited / Configurable

H4.26. Transaction timeout? 
  10 sn / 30 sn / 1 dakika / 5 dakika / Özel / Hiçbiri

H4.27. Ödeme onayı mekanizması? 
  3D Secure / CVV / 3DS2 / Zero-authentication / Özel / Hiçbiri

H4.28. Ödeme dönemi (Settlement)? 
  Aynı gün / T+1 / T+2 / T+3 / Custom / Variable

H4.29. Batch settlement? 
  Automatic / Manual / Hybrid / Hiçbiri

H4.30. Settlement hesap? 
  Banka hesabı / Platform wallet / Özel / Hiçbiri

H4.31. Multi-currency desteği? 
  Tek para birimi / Multi-currency (sabit) / Dynamic conversion / 
  Yönetilen / Özel / Hiçbiri

H4.32. Desteklenen para birlikleri? 
  USD / EUR / GBP / JPY / CNY / INR / Türk Lirası / Özel / Seçili

H4.33. Döviz kuru kaynağı? 
  Fixed rate (daily) / Real-time / Bank rate / Crypto exchange / 
  Custom / Hiçbiri

H4.34. Spread (mark-up) stratejisi? 
  Fixed % / Dynamic / Hiçbiri / Negotiated

H4.35. Audit trail detayı? 
  Full (timestamp, user, IP, action) / Basic (user, action) / 
  Minimal / Hiçbiri / Regulatory minimum

H4.36. Audit log retention? 
  1 yıl / 3 yıl / 7 yıl / Perpetual / Regulatory / Özel

H4.37. Audit log immutability? 
  Append-only database / Blockchain / Cryptographic verification / Hiçbiri

H4.38. Compliance raporlama? 
  FATCA / FBAR / GDPR / PSD2 / Custom / Hiçbiri / Automated

H4.39. Vergi hesaplaması? 
  Otomatik (TaxJar, Avalara) / Manuel / Tax service integration / Hiçbiri

H4.40. Vergi oranı güncellenmesi? 
  Real-time / Haftalık / Aylık / Manual / Hiçbiri

H4.41. Ürün iadesi politikası? 
  Otomatik geri ödeme / Manuel inceleme / Şartlı / Hiçbiri / Özel

H4.42. İade başlatma süresi? 
  30 gün / 60 gün / 90 gün / Unlimited / Custom / Hiçbiri

H4.43. İade işleme süresi? 
  Hemen / 1 gün / 3 gün / 5-7 gün / Özel

H4.44. Chargebacks yönetimi? 
  Otomatik cevaplama / Manuel inceleme / Kanıt sunumu / Hiçbiri / Seçili

H4.45. Chargeback itiraz süreci? 
  Desteklenmiş / Planlı / Manual / Hiçbiri

H4.46. Webhook güvenliği? 
  HMAC-SHA256 imzalama / IP whitelist / Mutual TLS / API key / 
  Özel / Hiçbiri

H4.47. Webhook retry stratejisi? 
  Exponential backoff / Linear / Fixed / Hiçbiri / Custom

H4.48. Webhook timeout? 
  5 sn / 10 sn / 30 sn / Custom / Hiçbiri

H4.49. Escrow desteği? 
  Evet / Hayır / Planlı

H4.50. Multi-party payments? 
  Evet / Hayır / Limited / Özel

H4.51. Subscription/Recurring? 
  Full support / Limited (interval only) / Hiçbiri

H4.52. Subscription billing cycle? 
  Günlük / Haftalık / Aylık / Yıllık / Custom / Özel

H4.53. Subscription trial period? 
  Supported / Not supported / Required / Optional

H4.54. Dunning mekanizması (başarısız ödeme)? 
  Intelligent retry / Notification emails / Hiçbiri / Custom

H4.55. Usage-based billing? 
  Desteklenmiş / Planlı / Hiçbiri

H4.56. Proration (pro-rata billing)? 
  Automatic / Manual / Hiçbiri

H4.57. Coupon/Discount system? 
  Percentage / Fixed amount / Free period / BOGO / Custom / Hiçbiri

H4.58. Coupon validation? 
  Real-time / Batch / Hiçbiri

H4.59. Customer segmentation for pricing? 
  Enterprise / Pro / Standard / Free / Custom tiers / Hiçbiri / Seçili

H4.60. Rate limiting? 
  Desteklenmiş / Planlı / Hiçbiri

H4.61. Rate limit stratejisi? 
  IP-based / User-based / API key-based / Custom / Hiçbiri

H4.62. Rate limit threshold? 
  100 req/min / 1000 req/min / 10000 req/min / Custom / Unlimited

H4.63. API versioning? 
  Semantic (v1, v2) / Date-based / Header-based / Custom / Hiçbiri

H4.64. Backward compatibility süresi? 
  6 ay / 1 yıl / 2 yıl / Indefinite / Hiçbiri / Deprecation policy
```

---

## H5 — HEALTHTECH & METABOLİZM (64 Soru)

```
H5.1. HIPAA uyumluluğu? 
  Zorunlu / İsteğe bağlı / Eğilim / Hiçbiri / Tamamlanmış / Planlı

H5.2. HIPAA sertifikasyon durumu? 
  Sertifikalı / Uyumluluk kontrol yapılmış / İç audit / Hiçbiri

H5.3. BAA (Business Associate Agreement)? 
  Gerekli / Sağlanan / Hiçbiri / Custom

H5.4. Hasta verisi şifrelemesi? 
  Statik (at-rest) / Transit (in-transit) / Her ikisi / Hiçbiri / Seçili

H5.5. Encryption standartı? 
  AES-256-GCM / AES-256-CBC / TLS 1.3 / TLS 1.2 / Özel / Hiçbiri

H5.6. Anahtar yönetimi? 
  HSM (Hardware Security Module) / AWS KMS / Azure Key Vault / 
  Self-managed / Hiçbiri

H5.7. Anahtar rotasyonu? 
  Automatic / Manual / Hiçbiri / Compliance-driven

H5.8. HL7/FHIR desteği? 
  Full HL7 v2.x / HL7 v3 / FHIR STU3 / FHIR R4 / Partial / Hiçbiri / Planlı

H5.9. HL7/FHIR versiyonu? 
  Latest / Pinned / Backward compatible / Hiçbiri

H5.10. EHR (Electronic Health Record) entegrasyonu? 
  Desteklenmiş (API) / Manual import/export / Hiçbiri / Planlı

H5.11. Uyumlu EHR sistemleri? 
  Epic / Cerner / Athena / NextGen / Custom / Hiçbiri / Seçili

H5.12. Hasta rızası (Consent) yönetimi? 
  Yazılı (signed) / Dijital / E-signature (DocuSign, HelloSign) / 
  Hiçbiri / Seçili

H5.13. Telemedicine desteği? 
  Video call / Audio call / Messaging / Eklenmiş / Hiçbiri / Planlı

H5.14. Telemedicine codec? 
  H.264 / H.265 / VP9 / WebRTC / Özel / Hiçbiri

H5.15. Telemedicine latency? 
  <500 ms / <1 sn / <2 sn / Özel / Hiçbiri

H5.16. Tıbbi cihaz entegrasyonu? 
  Kan basıncı monitörü / Pulse oksimetresi / Ağırlık ölçer / 
  Glukometresi / Holter / Özel / Hiçbiri / Seçili

H5.17. Cihaz veri formatı? 
  FHIR / HL7 / Proprietary / CSV / API / Hiçbiri / Multiple

H5.18. İlaç reçetesi yönetimi? 
  E-prescrip / Doktor onayı gerekli / Auto-renew / Hiçbiri / Planlı

H5.19. İlaç etkileşim kontrolü? 
  Desteklenmiş / Planlı / Hiçbiri / Manuel inceleme

H5.20. Alerji/Kontrendikasyon uyarısı? 
  Otomatik / Manuel / Konfigüre edilebilir / Hiçbiri

H5.21. Lab sonuçları entegrasyonu? 
  Otomatik itme / API pull / Manuel upload / Hiçbiri / Seçili

H5.22. Lab referans aralığı? 
  Otomatik flagging / Manuel / Hiçbiri / Age/gender-specific

H5.23. Veri saklama süresi? 
  6 yıl / 7 yıl / 10 yıl / Perpetual / Yasal minimum / Özel

H5.24. Veri silme politikası? 
  Otomatik purge / Manual request / Özel prosedür / Hiçbiri / Devre dışı

H5.25. Audit logging detayı? 
  Comprehensive (WHO, WHAT, WHEN, WHERE, WHY) / Basic / Minimal / Hiçbiri

H5.26. Access log retention? 
  1 yıl / 3 yıl / 7 yıl / Perpetual / Yasal / Özel

H5.27. Kullanıcı kimlik doğrulaması? 
  MFA mandatory / 2FA / Password only / Hiçbiri / Seçmeli

H5.28. MFA yöntemi? 
  TOTP / SMS / Email / Hardware token / Biometric / Push notification / Özel

H5.29. Session timeout? 
  5 dakika / 15 dakika / 30 dakika / 1 saat / Özel / Hiçbiri

H5.30. Session timeout uyarısı? 
  Evet / Hayır / Seçmeli

H5.31. Erişim kontrol modeli? 
  RBAC (Role-based) / ABAC (Attribute-based) / Provider-specific / Hiçbiri

H5.32. Hasta erişim kendi verilerine? 
  Full / Kısıtlı / Hiçbiri / Selective viewing

H5.33. Rol ayrılması? 
  Evet (Admin/Provider/Patient) / Kısıtlı / Hiçbiri

H5.34. Acil durum erişimi? 
  Desteklenmiş (logged) / Desteklenmiş (unlogged) / Hiçbiri / Kısıtlı

H5.35. Acil durum erişim denetimi? 
  Sonradan inceleme / Tıbbi onay / Hiçbiri / Otomatik alert

H5.36. Tıbbi cihaz sertifikasyon? 
  FDA Class I / FDA Class II / FDA Class III / CE / ISO / Hiçbiri / Planlı

H5.37. FDA 510(k) status? 
  Gerekli / Açısından muaf / Tamamlanmış / Planlı / Hiçbiri

H5.38. Klinik validasyon? 
  Yapılmış / Planlı / Hiçbiri / Devam ediyor

H5.39. Klinik trial katılımı? 
  Aktif / Tamamlandı / Planlanmış / Hiçbiri

H5.40. Kanıt dayanağı (Evidence base)? 
  Peer-reviewed / Published / Internal study / Hiçbiri / Anecdotal

H5.41. Interoperabilitas testi? 
  Evet, systematik / Ad-hoc / Hiçbiri / Planlı

H5.42. Uyumluluk test senaryoları? 
  FHIR sandbox / Real EHR systems / Simulated / Hiçbiri / Özel

H5.43. Risk değerlendirmesi türü? 
  Clinical (safety, efficacy) / Technical / Siber güvenlik / Tümü / Hiçbiri

H5.44. Risk değerlendirmesi metodolojisi? 
  ISO 14971 / OWASP / Custom / Hiçbiri / Eski yöntem

H5.45. Güvenlik tehdidi modelleme? 
  Yapılmış / Planlı / Hiçbiri / Ad-hoc

H5.46. Etik komite onayı? 
  IRB gerekli / Gerekli değil / Tamamlandı / Planlı / Hiçbiri

H5.47. Hastaya zarar (Adverse event) raporlama? 
  Otomatik / Manuel / Hiçbiri / Seçmeli

H5.48. Adverse event tracking sistem? 
  Desteklenmiş / Planlı / Hiçbiri

H5.49. Kütüphaneler / Yazılım sürümleri? 
  Up-to-date / Pinned / End-of-life / Custom patches / Hiçbiri / Mixed

H5.50. Yazılım güvenlik güncellemeleri? 
  Automatic / Monthly / Quarterly / Manual / Hiçbiri / Ad-hoc

H5.51. Dayanıklılık & yedekleme? 
  Gerekli RTO (<1 saat) / RTO 1-4 saat / RTO >1 gün / Custom / Hiçbiri

H5.52. Yazılım yedekleme sıklığı? 
  Real-time / Hourly / Daily / Weekly / Manual / Hiçbiri

H5.53. Yazılım yedekleme test? 
  Quarterly / Annually / Hiçbiri / Never

H5.54. Veri yedekleme sıklığı? 
  Real-time replication / Hourly / Daily / Scheduled / Manual / Hiçbiri

H5.55. Veri yedekleme depolama? 
  Ayrı veri merkezi / Coğrafi olarak ayrı / Cloud (AWS, Azure, GCP) / 
  On-prem / Hybrid / Özel

H5.56. Yedek şifrelemesi? 
  Evet / Hayır / Seçmeli / Hiçbiri

H5.57. İş sürekliliği planı? 
  Dokumente / Test edilmiş / Drill'lenmiş / Hiçbiri / Sürüyor

H5.58. Felaket kurtarma planı? 
  Dokumente / Test edilmiş / Drill'lenmiş / Hiçbiri / Basit

H5.59. RTO (Kurtarma Süresi Hedefi)? 
  <15 dakika / <1 saat / <4 saat / <24 saat / Özel / Belirsiz

H5.60. RPO (Kurtarma Noktası Hedefi)? 
  Real-time / <1 saat / <1 gün / <1 hafta / Özel / Belirsiz

H5.61. Multi-site redundancy? 
  Active-active / Active-passive / Single-site / Planlı / Hiçbiri

H5.62. Coğrafi yedeklilik? 
  Aynı ülke / Aynı kıta / Global / Hiçbiri / Planlı

H5.63. Uptime SLA? 
  99.9% (8.7 saat/yıl) / 99.95% / 99.99% / Custom / Hiçbiri / Belirsiz

H5.64. SLA ihlali tazminatı? 
  Service credits / Refund / Hiçbiri / Özel / Kontratual
```

---

## Kategori Özeti

| Blok | Domain | Soru Sayısı | Kapsamı |
|------|--------|-----------|---------|
| **H1** | Elektronik & Gömülü | 64 | Mikro denetleyici, RTOS, protokoller, güç yönetimi, sertifikasyon |
| **H2** | Ses & Audio | 64 | SNR/THD, API, DSP, codec, mekansal ses, görselleştirme |
| **H3** | Makine Öğrenmesi & YP | 64 | Framework, training, inference, model yönetimi, drift detection |
| **H4** | Fintech & Ödeme | 64 | Ödeme gateway, PCI DSS, dolandırıcılık, KYC/AML, subscription |
| **H5** | Healthtech & Metabolizm | 64 | HIPAA, HL7/FHIR, telemedicine, tıbbi cihaz, risk assessment |
| **TOPLAM** | — | **320+** | Production-grade enterprise specifications |

---

*BLOK H: 320+ Sorular × 5 Domain × Production-Ready Prompt Engineering*  
*Her soru: Tercih seçenekleri ALT ALTA | Turkish | Enterprise scope*  
*Commit: 2026-06-11 | Status: Complete*
