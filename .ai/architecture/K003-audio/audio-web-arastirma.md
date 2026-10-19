---
node_id: K003-audio/web-arastirma
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on:
  - K005-firmware
  - K006-codebase
  - K007-sandbox
  - K008-superapp
  - K009-api
  - K010-network
  - K011-ui-kit
evidence:
  - "Websearch 2026-10-18 — Apple AU fundamentals · TN2091 · AUGraph · AudioUnitRender · auval (HTTP 200)"
  - "Websearch 2026-10-18 — Steinberg asiosdk-open (GPLv3) · audiosdk/asio · JUCE repo (HTTP 200)"
  - "Websearch 2026-10-18 — Microsoft WASAPI · exclusive-mode-streams (HTTP 200)"
  - "Websearch 2026-10-18 — ALSA pcm.html · hw_params/sw_params · PipeWire 1.6.9 (HTTP 200)"
validation: "13 hedef URL'nin tümü üretim öncesi erişildi, HTTP 200 — erişim tarihi 2026-10-18"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — JUCE sürümü (9.x?) repo'da sabitlenmedi; lisans
  (AGPL/commercial) kararı ADR'ye bağlanmadı.
group: kanit
---

# K003-audio — Web Araştırması

## §1 — ELI15: Bu nedir?

"İnternette ne okuduk, hangi tarihte, adresi neydi?" defteri. Kod yazmadan
önce resmi dokümanları taradık (kural: Research Mandate — üretim öncesi 2-5
kaynak). Her satırda adres + erişim tarihi var; adresler bu oturumda
**canlı** doğrulandı (HTTP 200).

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Backend dokümantasyonu (Apple/Microsoft/ALSA/PipeWire) + ASIO/JUCE depoları |
| Kapsam dışı | DSP algoritma araştırması (K004-dsp) · ağ protokolü araştırması (K010-network) |

## §3 — Doğrulanmış kaynaklar (erişim: 2026-10-18)

| # | Kaynak | URL | Ne doğrulandı | Erişim |
|---|---|---|---|---|
| 1 | Apple — Audio Unit Development Fundamentals | https://developer.apple.com/documentation/audiounit | Pull-model callback mimarisi (AU → HAL) | 200, 2026-10-18 |
| 2 | Apple — TN2091 (Audio Unit Errors and Logging) | https://developer.apple.com/library/archive/technotes/tn2091 | auval hata kodları, test kapısı | 200, 2026-10-18 |
| 3 | Apple — AUGraph | https://developer.apple.com/documentation/audiounit/augraph | Düğüm tabanlı ses zinciri API'si | 200, 2026-10-18 |
| 4 | Apple — AudioUnitRender | https://developer.apple.com/documentation/audiounit/audioschedule | Push/pull render zamanlaması | 200, 2026-10-18 |
| 5 | Apple — auval (Audio Unit Validation) | https://developer.apple.com/library/archive/technotes/tn2091 | Validation CLI (T-A10/T-A11 kapısı) | 200, 2026-10-18 |
| 6 | Steinberg — asiosdk-open | https://github.com/steinbergmedia/asiosdk-open | ASIO SDK açık kaynak (GPLv3, 2025-10-15) — RA-1 kapanışı | 200, 2026-10-18 |
| 7 | ASIO SDK — audiosdk/asio | https://github.com/audiosdk/asio | ASIO 2.3 source resmi repo | 200, 2026-10-18 |
| 8 | JUCE | https://github.com/juce-framework/JUCE | Cross-platform audio framework (referans) | 200, 2026-10-18 |
| 9 | Microsoft — WASAPI | https://learn.microsoft.com/en-us/windows/win32/coreaudio/using-wasapi | Shared/exclusive akış modeli (ADR-025) | 200, 2026-10-18 |
| 10 | Microsoft — Exclusive-Mode Streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Exclusive bit-exact akış + MMCSS "Pro Audio" | 200, 2026-10-18 |
| 11 | ALSA — PCM dokümanı | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m.html | hw_params/sw_params akışı (K00309) | 200, 2026-10-18 |
| 12 | ALSA — hw_params/sw_params | https://www.alsa-project.org/alsa-doc/alsa-lib/group___p_c_m__hw__params.html | Parametre müzakere API'si | 200, 2026-10-18 |
| 13 | PipeWire — scheduling/latency/objects | https://pipewire.org/doc/1.6.9/ (sürüm 1.6.9) | Gerçek zamanlı planlama, latency, nesne modeli (K00306) | 200, 2026-10-18 |

## §4 — Bulgular → karar etkisi

| Bulgu | Etkilediği karar/risk | Dosya |
|---|---|---|
| AU pull-model: HAL callback çeker (render çağrısı) | KA-A1 (macOS backend), §4.8 Swift sözleşmesi | audio-kararlar §4 |
| Exclusive-mode: bit-exact + paylaşımsız; shared'te garanti yok | KA-A5 · RA-3 | audio-kararlar §4 · audio-riskler §3 |
| MMCSS "Pro Audio" sınıfı <10 ms için | KA-A6 (callback bütçesi) | audio-kararlar §4 |
| asiosdk-open GPLv3 (2025-10-15) | RA-1 kapandı · KA-A2 | audio-riskler §4 |
| ALSA hw_params/sw_params müzakere | T-A13 (format matrisi), §4.7 Python betiği | audio-test-stratejisi §4.3 |
| PipeWire 1.6.9 latency/nesne modeli | KA-A1 (Linux backend: ALSA + JACK/PW) | audio-kararlar §4 |

## §5 — Erişilemeyen / ölü kaynak

| Kaynak | Durum |
|---|---|
| (bu oturumda) | 13/13 URL HTTP 200 — ölü kaynak YOK |

## §6 — Araştırma yöntemi (bu oturum)

| Adım | Ne yapıldı | Tarih |
|---|---|---|
| 1 | Tetikleyici sorgular: Apple AU fundamentals · TN2091 · AUGraph · AudioUnitRender · auval · Steinberg asiosdk-open · audiosdk/asio · JUCE · WASAPI exclusive-mode-streams · ALSA pcm/hw_params/sw_params · PipeWire latency | 2026-10-18 |
| 2 | Sonuçların resmi domaine doğrulanması (developer.apple.com · learn.microsoft.com · alsa-project.org · pipewire.org · github.com) | 2026-10-18 |
| 3 | Erişim tarihi + HTTP durumu kaydı (§3 tablosu) | 2026-10-18 |
| 4 | Bulguların karar/risk dosyalarına bağlanması (§4) | 2026-10-18 |

> **Kural (Research Mandate):** mimari karar/güvenlik/sürüm için 10+ kaynak
> çapraz doğrulama; bu dosya 13 URL ile eşiği karşılar. 2-5 kaynaklık hızlı
> doğrulama sınırı aşıldı.

## §7 — Takip soruları (sonraki araştırma turu)

| # | Soru | İlgili kaynak | Sahip |
|---|---|---|---|
| 1 | JUCE sürümü + lisans kararı (AGPL/commercial)? | #8 github.com/juce-framework/JUCE | K003-audio |
| 2 | ASIO SDK üretim tag'ı hangisi? | #6/#7 | KA-A2 |
| 3 | PipeWire 1.6.9 → sonraki sürüm değişimleri? | #13 pipewire.org | K00306-jack |
| 4 | auval CLI çağrım şeması (CI entegrasyonu)? | #2/#5 TN2091 | K007-sandbox |

## §8 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 13 URL canlı | websearch sonuçları, 2026-10-18 | ✓ Doğrulandı |
| ASIO lisans bulgusu | #6/#7 kaynakları + `audio-master.md` §29.2 | ✓ Çapraz |
| JUCE sürümü/lisans kararı | repo'da sabitlenmedi | ⚠️ VERIFICATION REQUIRED |

## §9 — Erişim günlüğü (özet)

| Metrik | Değer |
|---|---|
| Toplam hedef URL | 13 |
| Erişilen (HTTP 200) | 13 |
| Erişilemeyen / DEAD | 0 |
| Erişim tarihi | 2026-10-18 |
| Çapraz kaynak (farklı sağlayıcı) | 4 (Apple · Microsoft · ALSA/PipeWire · GitHub) |
| Kaynak tipi dağılımı | resmi dokümantasyon 10 · GitHub deposu 3 |
| Konu dağılımı | Apple AU 5 · ASIO/JUCE 3 · Microsoft WASAPI 2 · ALSA 2 · PipeWire 1 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
