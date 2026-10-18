---
node_id: electroncis-test-stratejisi
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K005-firmware, K18-testing]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-master.md §6.1-§6.3 (eşikler + ölçüm kurulumu + termal) + §14.1-§14.3 (üretim testi) · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-063-hardware-design-standards.md §2(b,d,e): DRC/EMC kapıları · %100 elektrik test · AES17 fabrika testi · 2026-10-18"
validation: "Piramit 3 katmanlı; ≥10 K002-özel senaryo; her senaryonun kabul kriteri sayısal eşik veya kapı çıktısı olmalı; eşikler master §6/§14 ile birebir."
open_questions:
  - "⚠️ AES17-2020 / IEC 61606-1 / EIAJ CP-2404 test cihazı ve akredite lab bilgisi vault'ta yok → UNKNOWN (ADR-063 §2e yalnız şartnameyi bağlar)."
  - "⚠️ Termal senaryolarının hangi ortamda (kabin/kart) ölçüleceği belirsiz → ⚠️ VERIFICATION REQUIRED."
group: mimari
---

# K002-electroncis — Test Stratejisi (Piramit · Senaryolar · Kabul Kriteri)

## §1 ELI15

Elektronik test dört kattır: **kart üstü elektrik testi → ses performans
testi → termal/koruma testi → EMC/üretim testi**. Ucuzdan pahalıya gider:
multimetre ile kısa yoklama en altta, akredite EMC laboratuvarı en
üstte. Her senaryonun sayısal kabul kriteri vardır; eşik uydurulmaz
(master §6/§14).

## §2 Test Piramidi

```text
        /\
       /  \      EMC (akredite lab) + AES17 fabrika testi   — ADR-063 §2b/§2e
      /----\     Ses performansı (SNR/THD+N/sweep)           — §6.1 · §14.2
     /------\    Termal + koruma tetikleme (>80°C, DC offset) — §5.4 · §6.3
    /--------\   Kart üstü: continuity · kısa · ripple · bias — §14.1 + DRC (ADR-063 §2a)
```

| Katman | Kapsam | Kapı | Ne zaman |
|---|---|---|---|
| Statik | DRC (iz 0.1mm · boşluk 0.1mm · drill 0.2mm) | fabrikasyon öncesi **zorunlu** (ADR-063 §2a) | her layout revizyonu |
| Kart üstü | continuity · VCC-GND kısa · ripple · DC offset · bias | §14.1 beş test | prototip + her üretim partisi |
| Performans | 7 test (frekans yanıt → damping factor) | §14.2 | üretim hattı |
| Termal/Koruma | 5 sıcaklık kademesi + 6 koruma önceliği | §6.3 · §5.4 | prototip + AYM örnekleme |
| EMC/Üretim | pre-scan → conducted → radiated → immunity · AES17 | lansman öncesi kapanır (ADR-063 §2b) | sertifika öncesi |

## §3 K002-Özel Senaryolar

| # | Senaryo | Yöntem | KABUL KRİTERİ |
|---|---|---|---|
| TS-E1 | Continuity / kısa yoklama | multimetre, ohm | kısa yok, net yok (§14.1 #1) |
| TS-E2 | Güç vermeden rail kısa testi | ohm VCC-GND | VCC-GND kısa yok (§14.1 #2) |
| TS-E3 | Ripple ölçümü | osiloskop, AC coupling | **<10mV pk-pk** (§14.1 #3) |
| TS-E4 | DC offset koruması | DC coupled çıkış | **<0.5V**; >0.5V'ta röle 1s sonra açmalı (§14.1 #4 · §5.4 #2) |
| TS-E5 | Bias akımı | seri ampermetre | Sınıf-A tasarım değeri (§14.1 #5) |
| TS-E6 | Ses performansı (7 test) | sweep + analyzer + 8Ω load | frekans yanıt 20Hz-20kHz ±0.5dB · **THD+N <0.01%@1W** · **<0.001%@10W** · **SNR >100dB** · noise floor <-100dB · güç 100W@8Ω · damping >200 |
| TS-E7 | Ölçüm kurulumu doğrulaması | PC→DAC→Amplifier→Load→Audio Interface→Analyzer | 6 bileşen zinciri eksiksiz (§6.2); kurulum sapması ölçüm geçersiz kılar |
| TS-E8 | Termal merdiven | ısıtmalı ortam, 5 kademe | <50 normal · 50-60 uyarı · 60-70 fan · >70 güç azaltma · **>80 kapanma** (§6.3) |
| TS-E9 | Koruma önceliği (aynı anda 2 arıza) | kısa + DC offset eşzamanlı | tepki süreleri sıralı: short (µs-ms) > DC offset (1s) > over-current (ms) > over-temp (sn-dk) — §5.4 matrisi |
| TS-E10 | Boot bütçesi | aşama süreleri ölçümü | 8 aşama toplamı **~1.1s**; her aşama ayrı GEÇTİ/KALDI (§5.3) |
| TS-E11 | EMC ön tarama | iç lab pre-scan | yayılım haritası üretilir; **standart noyu ⚠️** (§14.3 — CISPR/IEC numarası doğrulanmadan iddia YOK) |
| TS-E12 | Üretim elektrik testi | %100 electrical test ↔ schematic uygunluğu | ADR-063 §2d asgari maddesi: test edilmeyen kart çıkış yapamaz |
| TS-E13 | AES17 fabrika testi | AES17-2020 + IEC 61606-1 + EIAJ CP-2404 | ADR-038 M1-M6 zinciri (ADR-063 §2e) — **cihaz bilgisi UNKNOWN** |

## §4 Kabul Kriteri (Genel Çapraz)

1. Her kapı (DRC · EMC · AES17) **kapatılmadan** lansman/üretim yok.
2. Eşikler `master §6.1` tablosundan okunur; dosyada **yeni eşik uydurulmaz**.
3. Eşik aşılmışsa ürün RED: test sonucu "kısmi PASS" ile gruplanmaz.
4. EMC standart numaraları doğrulanana kadar yalnız `⚠️ VERIFICATION REQUIRED`
   ile anılır (master §14.3 notu).
5. Ölçüm kurulumu (§6.2 altı bileşen) her raporda listelenir.

## §5 Kanıt

- `electroncis-master.md` §6.1 (satır 268) · §6.2 (281) · §6.3 (292) · §14.1 (471) · §14.2 (481) · §14.3 (493)
- ADR-063 §2(a,b,d,e) — DRC/EMC/kayıt/AES17 kapıları (2026-10-18 okundu)
- `.ai/electronic/**` ham içerik (60 dosya, 2026-10-13 ölçüm) — test metinlerinin kaynak satırları

## §6 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `K019-testing` · `K018-devops` — ölü K referansları (diskte yok).
- master §14.2/§14.1 satır atıfları `.ai/electronic/**` içindedir; bu görevde dosyalar **açılmadı** (kapsam dışı) → satır doğrulaması `VERIFICATION REQUIRED`.
