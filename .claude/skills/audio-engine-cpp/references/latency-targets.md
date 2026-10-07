# Gecikme Hedefleri & Sürücü Notları — ASIO <10ms · WASAPI <20ms

> **Otorite:** `.ai/CLAUDE.md` §19 (Latency Hedefi: <10ms ASIO / <20ms WASAPI) ·
> `.ai/PROJECTS.md` §7.1.1 (aynı hedefler, Float32/48kHz) ·
> `.ai/ecosystem/asio-wasapi-rehber.md` §3.2–§3.4 (exclusive mod & ASIO gecikme karakteri,
> 2026-09-24 web doğrulaması) · `.ai/brain.md` + `.ai/CLAUDE.md` §23 #6 (ASIO exclusive lock).
> Bu dosya SKILL.md §4 üzerinden bağlayıcıdır.

## §1 Bağlayıcı Hedefler

| Kriter | ASIO | WASAPI |
|--------|------|--------|
| Gecikme hedefi | **<10ms** | **<20ms** |
| Örnek formatı | Float32 (32-bit) | Float32 (32-bit) |
| Örnek hızı | 48kHz standart (96/192kHz desteği var) | 48kHz standart (96/192kHz desteği var) |
| K2 sıralaması | **1. tercih** | 2. tercih: exclusive → 3. tercih: shared (fallback zinciri) |
| Tahmin edilebilirlik | Yüksek (donanım zamanlaması, deterministic callback) | Orta-yüksek (OS yığını + exclusive payı) |

Hedef aşıldıysa "tamam" denmez → xrun (underflow/overflow) ve buffer seçimi birlikte
değerlendirilir (rehber §3.4: en düşük gecikme tek sayı değil; buffer + sürücü yolu +
xrun toleransı birlikte sözleşmedir).

## §2 Exclusive Mod Notları

- **ASIO exclusive lock:** Aynı anda yalnız **tek** uygulama ASIO cihazını kullanabilir;
  çoklu deneme **sürücü çökmesine** yol açabilir (`.ai/brain.md` #6, `.ai/CLAUDE.md` §23 #6).
  İkinci bir örnek/kopya süreç aynı cihazı aynı anda açmamalı.
- **WASAPI exclusive:** Uygulama cihazın tamamını devralır, OS mix motorunu atlar;
  shared mod sistem mix'ine katılır (Microsoft dokümantasyonu, doğrulandı 2026-09-24 —
  `.ai/ecosystem/asio-wasapi-rehber.md` §3.2). Exclusive, cihazı tek uygulamaya kilitler
  (diğer uygulamalar susturulur).
- **K2 fallback zinciri:** ASIO → WASAPI exclusive → WASAPI shared (rehber §3.2/§5.1).
- **Evrensel yerleşik ASIO 64-bit-only:** Windows universal built-in ASIO yalnız 64-bit
  ASIO sürücülerini işletir; 32-bit hedef bu yolu kapatır → CoreMusic K0 64-bit politikası
  (rehber §3.3). Eski 32-bit sürücülü donanım → fallback WASAPI exclusive'e düşer.

## §3 Buffer Matematiği (kaynak-independent aritmetik)

Callback buffer'ının katkısı:

```text
buffer_latency = bufferSize_samples / sampleRate
  64  sample @ 48000 Hz = 1.33 ms
 128  sample @ 48000 Hz = 2.67 ms
 256  sample @ 48000 Hz = 5.33 ms
 512  sample @ 48000 Hz = 10.67 ms   ← ASIO <10ms hedefini buffer katkısı aşar
```

Toplam zincir (rehber §3.4 "tipik zincir"): **buffer + ADC/DAC + sürücü yığını** (ASIO) /
**OS yığını + exclusive payı** (WASAPI exclusive). Buffer katkısı <10ms olsa bile toplam
ölçüm yapılmalıdır — hedef toplam gecikmedir.

## §4 Ölçüm Yaklaşımı

1. **Callback süresi (deterministic kısmi ölçüm):** Hot-path başında/totalları
   `std::chrono::steady_clock` ile ölçülür; süre buffer deadline'ını
   (`bufferSize / sampleRate`) aşarsa xrun riski → K14'e xrun sayacı metriği (rehber §3.4).
2. **Xrun sayacı:** Underflow/overflow olayları atomik sayıcıyla izlenir; hedef ≈0.
   Xrun kaynağı ne? Buffer boyutu mü, sürücü yığını mı — ölçülemeden tahmin yok (Zero Hallucination).
3. **Round-trip (uçtan uca) ölçüm:** Loopback (çıkış→giriş fiziksel/jumper) ile
   impulse gönder → geri dönüş zamanı ölç. Kullanılan loopback araç/metodu vault'ta
   tanımlı değil → `⚠️ VERIFICATION REQUIRED` (aracı seçmeden önce vault'a sor / web'de teyit et).
4. **THD+N / SNR ölçüm setup'ı:** Vault'ta ölçüm prosedürü yok (yalnız hedefler:
   THD+N <%0.005, SNR >105dB — PROJECTS §2) → ölçüm kurulum detayı `⚠️ VERIFICATION REQUIRED`.

## §5 Kaçınılacaklar

- "Ölçmeden <10ms'yiz" iddiası (Zero Hallucination ihlali).
- Exclusive cihazı ikinci süreçle aynı anda açmak (ASIO sürücü çökmesi riski).
- Gecikme hedefini yalnız buffer hesabıyla ispat etmek (toplam zincir unutulur).
- Vault'a ters sürücü sıralaması (ASIO birinci tercihtir; K2 fallback'i değiştirilemez
  — `.ai/ecosystem/asio-wasapi-rehber.md` §5.1).

*CoreMusic Skill v3.0 — references/latency-targets.md — Updated: 2026-10-07*