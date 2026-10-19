---
node_id: K003-audio/evrensel-dil
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
  - ".ai/architecture/K003-audio/audio-dil-kapsami.md (dosya adı FARKLI — dil kapsamı matrisi) — okundu 2026-10-18"
  - ".ai/architecture/K003-audio/audio-master.md §2-§3.4 (terim kaynakları) — okundu 2026-10-18"
  - "Websearch 2026-10-18 — WASAPI/ALSA/CoreAudio terim karşılıkları (HTTP 200 kaynaklar)"
validation: "18 terim EN/TR; audio-dil-kapsami.md (grup matrisi) ile DOSYA ÇAKIŞMASI YOK"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — evrensel-dil.md beyazliste dışı (dokunulmadı);
  sonraki turda bu sürümle birleştirilecek (sahip: K003-audio).
group: mimari
---

# K003-audio — Evrensel Dil (EN/TR Sözlük)

## §1 — ELI15: Bu nedir?

Aynı şeyin iki adı olunca kafa karışır: "buffer" mı "tampon" mı? Bu dosya
ses motoru terimlerinin **tek** İngilizce ve **tek** Türkçe karşılığını
sabitler. Doküman, kod yorumu ve PR tartışmasında herkes bu sözlüğü
kullanır. (Not: `audio-dil-kapsami.md` ayrı bir dosyadır — o dil *grupları*
matrisidir, bu sözlük değildir.)

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | K003-audio terimleri: backend, ring buffer, callback, durum, ölçüm birimleri |
| Kapsam dışı | DSP algoritma terimleri (K004-dsp/evrensel-dil) · ağ terimleri (K010) |

## §3 — Sözlük (EN ↔ TR, 18 terim)

| # | English | Türkçe | Kısa tanım (TR) | Kaynak |
|---|---|---|---|---|
| 1 | Audio backend | Ses arka ucu | Donanıma konuşan sürücü katmanı (ASIO/WASAPI/ALSA/CoreAudio) | `audio-master.md` §6 |
| 2 | Ring buffer | Halka tampon | Başa sararak dolan kilitssiz geçiş tamponu (SPSC) | ADR-019 |
| 3 | Callback | Geri çağırım | Ses thread'inin periyodik işleyici fonksiyonu (5-20 ms) | `audio-master.md` §3.4 Q7 |
| 4 | Exclusive mode | Münhasır mod | Cihazı tek uygulamaya kapatan bit-exact akış | ADR-025 · MS doc #10 |
| 5 | Shared mode | Paylaşımlı mod | Cihazın işletim sistemi mikseriyle paylaşıldığı mod | ADR-025 · MS doc #9 |
| 6 | Bit-exact | Bit-dobru | Dönüşümsüz, birebir bit aktarımı | `audio-master.md` §3.4 Q3 |
| 7 | Sample rate | Örnekleme hızı | Saniyedeki ses örneği sayısı (Hz; 44.1k-384k) | `audio-master.md` §3.4 Q2 |
| 8 | Bit depth | Bit derinliği | Örnek başına bit (16/24/32f) | `audio-master.md` §3.4 Q2 |
| 9 | Round-trip latency | Gidiş-dönüş gecikme | Çıkış→kulak→mik→giriş toplam gecikme (ms) | K00305-latency |
| 10 | Xrun | Taşma/kesinti | Bütçe aşımı → duyulabilir click (underrun/overrun) | KA-A6 · ADR-019 |
| 11 | Hot-plug | Sıcak ekleme | Çalışırken cihaz tak-çıkar; device-lost olayı | C-dev-1 |
| 12 | Device ID | Cihaz kimliği | Kalıcı donanım kimliği (eşleme/profil bağlama) | `audio-master.md` §3.4 |
| 13 | Drain | Boşaltma | Duraklatmada buffer'ın bitmesini bekleme (RUNNING→DRAINING) | C-st-1 |
| 14 | Biquad | İki kutuplu filtre | EQ'nun temel bina taşı (katsayı seti) | K004-dsp köprüsü |
| 15 | Audio Unit (AU) | Ses birimi | macOS eklenti formatı (v2/v3), HAL plugin (ADR-038) | ADR-037/038 · doc #1 |
| 16 | auval | Ses birimi doğrulama | macOS eklenti doğrulama CLI'sı (test kapısı) | doc #2/#5 |
| 17 | MMCSS | Multimedya zamanlama sınıfı | Windows "Pro Audio" öncelik sınıfı (<10 ms) | MS doc #10 |
| 18 | MIDI-CI | MIDI cihaz bilgisi | MIDI 2.0 cihaz keşif/uyumluluk katmanı | KA-A7 |

## §4 — Yazım kuralları

| Kural | Örnek |
|---|---|
| Kodda İngilizce, dokümanda çift yazım ilk kullanımda | "ring buffer (halka tampon)" |
| Birimler: Hz, ms, dB, LUFS — Türkçe ek almaz | "48 kHz", "10 ms" |
| Ölçümlerde ondalık virgül DEĞİL nokta | "0.01" (script/CI uyumu) |
| Backend adları olduğu gibi yazılır | "WASAPI", "CoreAudio" — "çekirdek ses" DEĞİL |

## §5 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 18 terim EN/TR | bu dosya §3 | ✓ 2026-10-18 |
| Terim kaynakları (ADR/master) | `audio-master.md` §3.4 · index.md §3 | ✓ Okundu 2026-10-18 |
| Çakışma yok (dil-kapsami ayrı dosya) | `audio-dil-kapsami.md` ≠ bu dosya | ✓ 2026-10-18 |
| Mevcut evrensel-dil.md senkronu | beyazliste dışı, dokunulmadı | ⚠️ Manuel birleştirme |

## §5 — Terim üretimi kuralı (sonraki terimler için)

Yeni terim eklerken bu şablon zorunlu:

```yaml
en: "English term"
tr: "Türkçe karşılık"
tanim: "tek cümlelik kısa tanim (TR)"
kaynak: "<dosya> §<bölüm>" | "resmi doküman #n"
sinif: backend | olcum | zaman | cihaz | plugin
```

### Sınıf dağılımı (bu sürüm)

| Sınıf | Adet | Terimler (kısa) |
|---|---|---|
| Backend | 5 | #1, #4, #5, #11, #12 |
| Zaman/bütçe | 3 | #3, #9, #10 |
| Format/ölçüm | 4 | #7, #8, #13, #14 |
| Plugin | 3 | #15, #16, #18 |
| Çapraz (diğer K) | 3 | #2, #6, #17 |

### Kapsam sınırı komşuları

| Komşu sözlük | Kapsamı | Ayrım çizgisi |
|---|---|---|
| K004-dsp/evrensel-dil | Algoritma (biquad, LUFS, THD+N) | "Ses nasıl işlenir" |
| K010-network/evrensel-dil | Ağ (SSE, backoff, ETag) | "Ses nasıl taşınır" |
| Bu dosya (K003) | Backend/donanım (ASIO, xrun, hot-plug) | "Ses donanıma nasıl ulaşır" |

### Çakışma taraması (bu sürüm)

| Tarama | Sonuç |
|---|---|
| K003 audio-dil-kapsami.md vs bu dosya | FARKLI dosya (grup matrisi vs sözlük) — çakışma YOK ✓ |
| K004 dsp-evrensel-dil.md (bu oturum üretildi) | Sınırlar ayrıştırıldı: K004=algoritma, K003=backend ✓ |
| K010 network-evrensel-dil.md (bu oturum üretildi) | Sınırlar ayrıştırıldı: K010=ağ ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
