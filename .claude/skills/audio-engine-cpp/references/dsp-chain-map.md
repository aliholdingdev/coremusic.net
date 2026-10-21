# DSP Zincir Haritası — Pipeline Sırası ve Bileşenler (Neva Engine)

> Hedef pipeline: `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` (vault spec).
> Mevcut disk durumu: yalnız EQ zinciri var (EqChain). Bu dosya "hedef ↔ disk"
> farkını ve pipeline sırasını tek yerde toplar.

## 1. Hedef Pipeline Sırası (vault spec)

```
Kaynak (Dosya/Radio/Line-In/Mikrofon)
    │
    ▼
[0] Çözücü (decode) ──── 32-bit float iç formata çevirme (decode_stub.cpp: null-destek)
    │
    ▼
[1] EQ (Graphic EQ)  ──── EqChain: 31 bant × 2 (min/max) = 62 biquad (DISKTE VAR)
    │
    ▼
[2] Reverb           ──── hall/plate/room — mod enum'u diskte YOK (§4, §6)
    │
    ▼
[3] Crossover        ──── 2/3 yollu, Linkwitz-Riley
    │
    ▼
[4] Limiter          ──── çıkış eşiği (0 dBFS koruması)
    │
    ▼
[5] Kanal matrisi    ──── 1.0 → 8.1 upmix (§3)
    │
    ▼
Backend (WASAPI Exclusive/Shared · ASIO · Null)
```

**DoD (vault):** `x8 → passthrough (bypass) doğrulanmalı` — zincir bypass edildiğinde
giriş≡çıkış (tolerans dahilinde) olmalı.

## 2. Mevcut Disk Durumu (2026-10-21)

| Bileşen | Durum | Kanıt |
|---|---|---|
| Kaynak | **VAR** (Dosya · Radio · Line-In · Mikrofon) | `neva_session.h` kaynak kanalları |
| Çözücü | **VAR** (null) | `src/decode_stub.cpp` |
| EQ | **VAR** (31×2 = 62 biquad) | `include/neva/eq_chain.h` (contracts.md §7) |
| Reverb | **YOK** | Diskte reverb dosyası/enum yok (SKILL.md §10.1.2) |
| Crossover | **PLANLI** | Vault spec; diskte kod yok |
| Limiter | **PLANLI** | Vault spec; diskte kod yok |
| Kanal matrisi | **PLANLI** | Vault: "1.0 → 8.1" (§3; §10.1.4 çelişki) |
| Backend | **VAR** (WASAPI · Null) | `wasapi_backend.cpp` · `null_backend.cpp` |
| ASIO backend | **YOK** | SKILL.md §10.1.3 |
| Test | **VAR** (3) | `biquad_eq_ring` · `eq_grids_gates` · `icy_parser_gates` (ctest + `<cassert>`) |

## 3. Kanal Sayıları

| Konu | Değer | Kaynak |
|---|---|---|
| Giriş (depolanan kaynak) | 2.0 (stereo) | `neva_session.h` |
| Çıkış | 8.1 | AGENTS.md "8.1 surround", sartname §1.1 |
| Upmix | 1.0 → 8.1 | vault `02-dsp-zincir-mimari.md` |
| Backend kanal haritası | FL FR C LFE SL SR BL BR | `wasapi_backend.md` §4 (S5 dizilimine göre) |
| Çelişki | vault "2.0→8.1" ↔ "1.0→8.1" | SKILL.md §10.1.4 — karar: upmix girdisi 1.0 (tek kanal) VE 2.0 (stereo) desteklenmeli |

## 4. EQ (EqChain) — mevcut zincir

- 31 bant × iki kurulum (min EQ / max EQ) = **62 biquad** (contracts.md §7)
- Biquad: b0 b1 b2 a1 a2 (DF1/DF2 — `biquad.h`)
- Grid: `eq_grids.h` (31 bant tipik 20 Hz – 20 kHz, oktabav)
- **Parametre yayını:** RT-olmayan thread'de hazırla → atomik publish → RT thread
  blok sınırında uygular + rampa (R5; `eq_chain.h` publish+ramp deseni)
- **RT güvenliği:** biquad katsayıları blok içinde yeniden hesaplanmaz (R2/R3)

## 5. Reverb — hedef zincir

Vault spec: `hall / plate / room` üç mod; RT güvenli delay-line tabanlı
(Comtus-reverb referansı — lisansı **okunmalı**: 00-sdk-lib-envanter.md; lisans
kanıtı VERIFICATION REQUIRED).

**Kısıt (disk-öncelikli):** Enum YOK → yeni mod eklenmeden önce ADR + onay gerekir
(SKILL.md §10.1.2, kural DUR). Tasarım onaylandığında:

```
ReverbParam { mode: hall|plate|room, wet(%), room-size, damping, predelay }
```

RT kuralları: wet/dry rampa (R5), delay-line buffer'ları baştan allocate (R1),
predelay için hazır ring (R6), blok sınırında parameter geçişi (R6/R7).

## 6. Crossover — hedef zincir

Linkwitz-Riley (LR2/LR4) biquad çiftleri; kanal matrisinden ÖNCE, limiter'dan önce
(vault sırası: EQ → Reverb → Crossover → Limiter). Bant başına gain rampası (R5).

## 7. Limiter — hedef zincir

Çıkışta son işlem; eşiği aşılmaz (0 dBFS). RT güvenli: lookahead tamponu baştan
allocate (R1), attack/release katsayıları blok içinde tablo lookup ile (R2).

## 8. Kanal Matrisi (upmix)

```
1.0 (LFE hariç tek kanal)  ─┐
                            ├→ 8.1: FL FR C LFE SL SR BL BR
2.0 (stereo)              ─┘
```

Matris katsayıları `constexpr` (R4); LFE ayrı kanaldır (bass-upmix kararı
vault'a ait — varsayma, §10.1.4).

## 9. Kalite Hedefleri (sartname §1.1)

| Metrik | Hedef |
|---|---|
| SNR | > 100 dB |
| THD+N | < 0.01% @ 1 kHz |

Bu hedefler EQ→limiter zincirinin toplam gürültü/distorsiyon bütçesine dağıtılır;
her bileşen kendi THD katkısını ölçümler (ölçüm altyapısı depoda YOK →
planlanacak; ölçüm cihazı donanım-envanterine bağlı).

## 10. Denetim Listesi

1. Pipeline sırası vault'a uyuyor mu (§1)?
2. Yeni bileşen diskte var mı, yoksa DUR + onay mı (§2)?
3. Kanal matrisi giriş/çıkış sayıları tutarlı mı (§3)?
4. Her yeni DSP bloğu R1-R7'ye (realtime-rules.md) uygun mu?
5. Kalite hedefleri (§9) ihlal ediliyor mu?
6. Bypass testi (DoD) yazıldı mı (§1)?
