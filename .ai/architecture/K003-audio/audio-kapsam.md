---
title: "Audio Kapsam Tanımı"
node_id: K003-audio/audio-kapsam.md
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K004-dsp
  - K011-application
  - K014-media
  - K18-testing
evidence: "audio-master.md §3, §18 · audio-karar-matrisi.md · CoreMusic/Services/Audio (repo)"
validation: "Ön-inceleme (2026-10-17). Kanıt: audio-master.md §3 (rol), §18 (şartname), repo dizinleri (§2.3 eşleme)."
open_questions: "Q1: ASIO GPLv3 lisans kararı 2026-10-14'te kapatıldı (Q1 closed); Q3 macOS termal yönetimi ⚠️ VERIFICATION REQUIRED."
group: mimari
---

# Audio — Kapsam Tanımı (K003)

## §1 ELI15

CoreMusic ses motoru, bilgisayarın hoparlörlüre **müzik akışını hatasız, gecikmesiz ve kararlı** biçimde gönderen bir "kalp"tir. Uygulama çaldığı şarkıyı önce dijital bir banttan geçirir (karıştırma, ses seviyesi, efektler), sonra bu banttaki veriyi işletim sisteminin ses donanımına teslim eder. Bu teslimat zincirinde her adım (tampon boyutu, örnekleme hızı, sürücü seçimi) sesin kalitesini ve gecikmesini doğrudan belirler. K003 bu zincirin tamamını — çekirdek motoru, işletim sistemi ses I/O katmanını, sinyal yolunu ve otomasyonu — tanımlar; donanımdan (K001) bağımsız ama ona bağlı, DSP'den (K004) ayrı ama onunla konuşan, uygulama katmanına (K011) ses hizmeti sunan bir alt-alandır.

## §2 Kapsam İçinde

| # | Kapsam alanı | Açıklama | Kanıt |
|---|--------------|----------|-------|
| C1 | Motor çekirdeği (audio engine core) | Karıştırma, yönlendirme, transport durum yönetimi | audio-master.md §2 (Motor çekirdeği) |
| C2 | OS ses I/O katmanları | WASAPI (Windows), CoreAudio (macOS), ALSA/PipeWire (Linux) | audio-master.md §4 (A1-A6 URL) |
| C3 | Sinyal yolu (signal path) | Tampon yönetimi, örnekleme dönüştürme, bit derinliği | audio-master.md §2 (Sinyal yolu) |
| C4 | Donanım sürücüleri | ASIO (Windows), PCM3168A DAC (K001) | audio-master.md §5 T1-T17, §18 |
| C5 | Otomasyon | Ses seviyesi/parametre otomasyonu, zaman çizelgesi | audio-master.md §2 (Otomasyon) |
| C6 | Telemetri | Gecikme, tampon underrun/overrun, CPU ölçümü | audio-master.md §2 (Telemetri), §10 |
| C7 | DSP entegrasyon sınırı | K004'e parametre/girdi gönderimi | audio-master.md §3 (bağımlılıklar) |

## §3 Kapsam Dışı

| # | Dışarıda | Nereye | Neden |
|---|----------|--------|-------|
| X1 | DSP algoritması iç yapısı | K004-dsp | K003 yalnızca sınır ve parametre sözleşmesini tanımlar |
| X2 | Donanım PCB/tasarımı | K001-donanım | DAC/amplifier tasarımı donanım katmanının sorumluluğu |
| X3 | Medya dosyası biçimi/kodek | K014-media | Decode öncesi dosya okuma K014'ün sınırı |
| X4 | Uygulama UI/akışları | K011-application | Oynatma kontrolleri UI katmanında, motor yalnız servis sunar |
| X5 | İşletim sistemi çekirdeği/driver yükleme | K000-isletim-sistemi | Sürücü yükleme mekanizması OS katmanının sorumluluğu |

## §4 K003'ün 22-K Mimarisindeki Rolü

Kaynak: audio-master.md §3.

```
K014-media ──(decode edilmiş PCM)──▶ K003-audio ◀──(parametre)── K004-dsp
                                     │       │
                    (telemetri/metrik) │       │ (ses hizmeti API'si)
                                     ▼       ▼
                          K016-observability  K011-application
                                     │
                                     ▼
                    K001-donanım (DAC, sürücü) ── K000-isletim-sistemi
```

- **Girdi:** K014'ten decode edilmiş PCM akışı; K011'den transport komutları (oynat/duraklat/atla); K004'ten DSP parametreleri.
- **Çıktı:** K001 donanımına gönderilen ses akışı; K016'ya telemetri metrikleri.
- **Sınır:** K003 ses verisini işler, kullanıcı arayüzü çizmez, dosya çözümlemez, DSP algoritması üretmez.

## §5 Sınır ve Bağımlılıklar

| Bağımlılık | Yön | Tür | Açıklama |
|------------|-----|-----|----------|
| K000-isletim-sistemi | Giriş | Zorunlu | OS ses API'si (WASAPI/CoreAudio/ALSA) olmadan ses çıkışı yok |
| K001-donanım | Giriş | Zorunlu | DAC (PCM3168A) ve ses kartı sürücüleri (§18 şartname) |
| K004-dsp | Çıkış | Opsiyonel | Efekt zinciri parametreleri; K003 yalnızca çağırır, algoritma bilmez |
| K011-application | Çıkış | Zorunlu | Transport komutları ve durum geri bildirimi |
| K014-media | Giriş | Zorunlu | Decode edilmiş PCM kaynağı |
| K016-observability | Çıkış | Opsiyonel | Telemetri aktarımı; yoksa yerel log |
| K18-testing | Çıkış | Zorunlu | Test stratejisi bu K'da tanımlanır |

## §6 Şartname Özet (Kesin Ölçüler)

Kaynak: audio-master.md §18.

| Parametre | Değer | Durum |
|-----------|-------|-------|
| Bit derinliği | 512-bit float (dahili işleme) | Sabit (§18) |
| Örnekleme hızı | 48 kHz | Sabit (§18) |
| Donanım codec'i | PCM3168A | Sabit (§18) |
| Örnek format (dahili) | float32 (§18 şartnamesiyle uyumlu) | Sabit (§18) |
| Hedef gecikme | ≤ 5 ms (128-256 tampon) | Hedef — ölçüm gerekli |

## §7 Bilinmeyenler (⚠️)

| # | Bilinmeyen | Etki | Durum |
|---|------------|------|-------|
| U1 | macOS termal yönetimi stratejisi (Q3) | macOS'ta uzun süreli çalmada performans düşüşü riski | ⚠️ VERIFICATION REQUIRED |
| U2 | Linux PipeWire sürücü olgunluğu (T6) | Linux'ta gerçek zamanlı gecikme hedefi tutmayabilir | ⚠️ VERIFICATION REQUIRED |
| U3 | ASIO GPLv3 lisansının dağıtım modeline etkisi | Lisans uyumu için hukuki inceleme gerekli (Q1 closed, lisans kararı kapatıldı; dağıtım etkisi ⚠️) | ⚠️ VERIFICATION REQUIRED |

## §8 İlgili Dosyalar (K003 kökü)

| Dosya | İçerik |
|-------|--------|
| `audio-master.md` | Ana kaynak: A1-A6 URL kanıtları, T1-T17 teknoloji kanıtları, §10 telemetri, §18 şartname |
| `audio-karar-matrisi.md` | OS ses API seçimi karar matrisi |
| `asio-gplv3.md` | ASIO GPLv3 lisans analizi |
| `coreaudio-vs-wasapi.md` | macOS/Windows ses API karşılaştırması |
| `dsp-vs-c-uyumluluk.md` | DSP kod-C uyumluluk incelemesi |
| `cpu-cekirdek-yonetimi.md` | CPU çekirdek izolasyonu |
| `macos-isinma-yonetimi.md` | macOS termal yönetimi (Q3 kaynağı) |
| `windows-driver-analizi.md` | Windows sürücü analizi |
| `windows-driver-karar-matrisi.md` | Sürücü karar matrisi |
| `kod-incelemesi.md` | Kod incelemesi sonuçları |

## §9 Sonraki Adım

1. `audio-bilesenler.md` — bileşen ağacı detayları.
2. `audio-kararlar.md` — ADR referansları ve gerekçeler.
3. `audio-kod-ornekleri.md` — çok-dilli kod örnekleri.
4. Q3 macOS termal stratejisinin kapatılması (K001/K005 ile ortak).

---

*Bu dosya Stage-2-B kapsamında üretilmiştir (2026-10-17). Kanıt: audio-master.md. Commit atılmadı.*
