---
node_id: electroncis-riskler
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K004-dsp, K005-firmware]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-master.md §7 Riskler (R1-R10) + §14 test eşikleri · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md (H001 kilidi) + ADR-089 §3.1 (reddedilenler) · 2026-10-18"
validation: "≥14 risk · her satır Olasılık×Etki×Azaltma×Sahip içerir · master §7'den türeyen satırlar R numarasını korur."
open_questions:
  - "⚠️ Sahip sütunu ROLE etiketiyle yazıldı — gerçek ekip ataması UNKNOWN (sahip matrisi vault'ta yok)."
  - "⚠️ R-11 EMC standart numaraları (CISPR 32 · IEC 61000-4-x) master §14.3'te ⚠️ VERIFICATION REQUIRED — standart noyu uydurmadan azaltma yalnız 'akredite lab + pre-scan' olarak yazıldı."
  - "⚠️ R-14 boot bütçesi ~1.1s hedefinin ölçüm otomasyonu (hangi araç) tanımlı değil → UNKNOWN."
group: mimari
---

# K002-electroncis — Riskler (Olasılık × Etki × Azaltma × Sahip)

## §1 ELI15

Elektronikte risk yakar: hoparlör, çip, sertifika, teslimat. Her satırda
dört şey var — ne sıklıkla olur (olasılık), ne bozar (etki), ne
yapıyoruz (azaltma), kim bakar (sahip). Azaltması olmayan risk yazılmaz.

## §2 Risk Kayıt Defteri

| # | Risk | Olasılık | Etki | Azaltma | Sahip |
|---|---|---|---|---|---|
| R1 | DC offset hoparlör bobinini yakar | Orta | Yüksek | Röle koruması >0.5V (1s gecikme → açma) — master §5.4 #2 | ROLE:audio-hw |
| R2 | Boost converter PVDD aşımı | Düşük | Yüksek | Over-voltage >±48V izleme + güç azaltma (§5.4 #4) | ROLE:audio-hw |
| R3 | Termal kaçış (thermal runaway) | Düşük | Yüksek | θ<1°C/W heatsink + 80°C kapanma (§5.4 #5) | ROLE:audio-hw |
| R4 | EMC uyumsuzluk — sertifikasyon reddi | Orta | Yüksek | Pre-scan + stitching via + filtre (ADR-063 §2b; **EMC kapısı lansman öncesi**) | ROLE:embed |
| R5 | MLCC DC bias kaybı — filtre kayması | Orta | Orta | Kritik noktalarda X7R yerine C0G (master §7 R5) | ROLE:audio-hw |
| R6 | PLL jitter — SNR bozulması | Düşük | Orta | Düşük-jitter kristal, PLL bypass; jitter <1ps hedef (§4.1 #6) | ROLE:embed |
| R7 | PCM5122 kullanımı (YASAK) | Düşük | Kritik | ADR-038 H001: yalnız PCM3168A — alternatif önerilemez (ADR-089 §3.1 A3) | ROLE:audio-hw |
| R8 | Analog/dijital plane karışımı — gürültü | Orta | Orta | Tek nokta birleşim, ayırma; saat izi yarığı kesmez (ADR-063 §2a) | ROLE:embed |
| R9 | Boot zinciri kopması — sistem açılmaz | Düşük | Yüksek | Aşama bazlı süre bütçesi (~1.1s, 8 aşama — master §5.3) | ROLE:embed |
| R10 | Tedarik riski (EOL bileşen) | Orta | Orta | Çoklu kaynak, drop-in alternatif (§7 R10) | ROLE:embed |
| R11 | EMC standart numarası doğrulanmadan test planı yazılması | Yüksek | Orta | Standart noyu `⚠️ VERIFICATION REQUIRED` tut; pre-scan raporu + akredite lab onayı olmadan sertifika iddiası YOK (master §14.3) | ROLE:qa |
| R12 | PVDD aralığı çelişkisi (görev 24–52V ↔ datasheet 18–53.5V) | Orta | Yüksek | Datasheet kazanır (KD-E06); tasarım 18–53.5V'a göre, vault satır düzeltmesi Vault Steward'da | ROLE:audio-hw |
| R13 | PCM3168A tek-kaynak tedarik kırılganlığı (ADR-038 kilidi alternatifi engeller) | Orta | Yüksek | Stok tamponu + çoklu kanal siparişi; alternatif ancak **yeni ADR** ile (frozen kural) | ROLE:audio-hw |
| R14 | Boot bütçesi ~1.1s aşımı (K005 kodu gecikirse) | Orta | Yüksek | Aşama süreleri ayrı ölçülür (GEÇTİ/KALDI ayrı rapor); bütçe aşımı → K005'e eskalasyon | ROLE:embed |
| R15 | Üretim testinde yanlış PASS (ölçüm eşiği düşük) | Düşük | Yüksek | §14.1-§14.2 kapıları: ripple <10mV · SNR >100dB · THD+N <0.01% · %100 elektrik test (ADR-063 §2d) | ROLE:qa |

## §3 Sözlük

Olasılık/Etki: Düşük / Orta / Yüksek / Kritik (etki için Kritik de
kullanılır). Sahip: `ROLE:*` etiketi — gerçek kişi ataması **UNKNOWN**
(açık soru §O-1). Azaltmada ölçülebilir eşik **varsa** yazar (ör.
>100dB), yoksa yöntem yazılır.

## §4 Eskalasyon

1. Kritik etki (R7) → dur + `ROLE:audio-hw` veto.
2. Sınır ihlali (K000/K005 koduna taşma) → `LAYER_VIOLATION`, revert.
3. Ölçülemeyen eşik → `UNKNOWN` yaz, kapıyı geçme.

## §5 Kanıt

- `electroncis-master.md` §7 (R1-R10) · §5.3-§5.4 · §14.1-§14.3
- ADR-038 (H001) · ADR-063 §2(a,b,d,e) · ADR-089 §2-§3
- `electroncis-kararlar.md` §4 KD-E06 (PVDD)

## §6 ⚠️ Bilinen Bozuk Referanslar (salt rapor — DÜZELTME YASAK)

- `K002-altyapi` · `K002-data` · `K019-testing` · `K018-devops` — ölü K referansları (yalnız işaretli).
- `K020-comilance` · `K020-comere` · `K0100-identitiy` · `K020-licence` · `K001-infrastructure` — yazım hatalı ölü düğümler (ad düzeltme YASAK).
