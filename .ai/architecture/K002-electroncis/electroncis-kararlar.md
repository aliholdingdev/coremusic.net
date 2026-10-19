---
node_id: electroncis-kararlar
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K004-dsp, K005-firmware]
evidence:
  - ".ai/.decisions/index.md §ADR listesi (ADR-017 · ADR-038 · ADR-062 · ADR-063 · ADR-064 · ADR-089 · ADR-090) · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-063-hardware-design-standards.md §2 (satır 87) · ADR-064 §2 (satır 91) · ADR-089 §2 (satır 75) · ADR-062 §2 (satır 94) · 2026-10-18"
  - ".ai/architecture/K002-electroncis/electroncis-master.md §6-§10, §14 (eşikler + kaynakça) · 2026-10-18"
validation: "ADR numaraları yalnız .ai/.decisions/index.md'den alınır (uydurma YASAK); her ADR'nin başlığı ve durumu indeksle birebir; yerel kararlar KD- önekiyle ADR'den ayrışır."
open_questions:
  - "⚠️ ADR-062/063/064 Electronics kategorisinde; K002 ile bağları bu dosyada yeniden yorumlanmaz — bağ satırı ADR metninden gelmeli, yoksa '⚠️ bağ: yorumlanmadı'."
  - "⚠️ ADR-064 dosya durumu: ADR-063 §2(f) 'ADR-064 (platform, dosya yok ⚠️)' der — ancak `accepted/ADR-064-electronics-platform-architecture.md` diskte VAR (2026-10-18 glob) → çelişki C-ADR, Vault Steward kararı."
  - "⚠️ EMC standart numaraları (CISPR 32 · IEC 61000-4-x) master §14.3'te ⚠️ VERIFICATION REQUIRED — ADR-063 §2(b) bu matrisi bağlasa da numaralar bu turda doğrulanmadı."
group: mimari
---

# K002-electroncis — Kararlar (ADR + Yerel KD)

> **Kaynak kuralı:** ADR numaraları/başlıkları **yalnız**
> `.ai/.decisions/index.md`'den alınır. Burada yeni ADR **uydurulmaz**;
> domain-içi küçük kararlar `KD-` önekiyle işaretlenir ve **ADR değildir**.

## §1 ELI15

Karar = "şunu yapıyoruz, şunu yapmıyoruz, nedeni şu". Bu dosyada iki tür
karar var: (1) **ADR** — vault'ta kayıtlı, numarası indeksle kanıtlanan
bağlayıcı kararlar; (2) **KD** — K002 kökünde tutulan yerel kararlar.
ADR numarası indekste yoksa o numara **yazılmaz**.

## §2 Bağlayıcı ADR'ler (indeks kanıtlı)

| ADR | Başlık (indeks birebir) | Kategori | Durum | K002 etkisi |
|---|---|---|---|---|
| ADR-017 | DSP Hardware Mode (XMOS, JUCE, ASIO) | Audio | accepted | 32-bit float — DSP donanım modu sınırı (master §12) |
| ADR-038 | 8.1 Sound Card (PCM3168A + XMOS) | Audio | accepted | **H001: yalnız PCM3168A** — DAC seçimi kilidi (R7 yasağı) |
| ADR-062 | DSP Pipeline Architecture | Electronics | accepted | §2 (satır 94): tek sabit sıralı seri zincir + RT-güvenli parametre güncelleme |
| ADR-063 | Hardware Design Standards | Electronics | accepted | §2 (satır 87): PCB kural seti · SI/EMC · IPC-6012/A-610 Class 2 · dokümantasyon + AES17 fabrika testi |
| ADR-064 | Electronics Platform Architecture | Electronics | accepted | §2 (satır 91): 5 cihaz sınıfı + cihaz↔servis matrisi — "karar kod üretmez" |
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost | Electronics | accepted | §2 (satır 75): Class AB · 8×50W · 12–24V→boost ±35V · 1/2/4/6/8 kanal · hibrit MCU (XMOS+STM32H7/RP2040+RPi5) |
| ADR-090 | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) | Electronics | accepted | §2 (satır 108): kanal varyant matrisi + 4 maliyet kademesi |

## §3 ADR Özet Kararları (metinden — özet)

| ADR | Karar özeti (§2'den) | Reddedilenler |
|---|---|---|
| ADR-063 | Standart tek belgede bağlayıcı; **DRC kapısı fabrikasyon öncesi**, **EMC kapısı lansman öncesi** zorunlu; üretim çıktısı IPC-2581 (tercih) | Class 3 maliyeti (§2.1) |
| ADR-064 | 5 cihaz sınıfı; bağlayıcı servis sayısı **11** (13 iddiası doğrulanamadı) | kanıtsız "13 servis" sayımı |
| ADR-089 | Class AB + boost ±35V + hibrit MCU üç katman; **Class D (TPA3255) ana topoloji olarak REDDEDİLDİ** · **PCM5122 🚫 YASAKLI** · monolitik tek-PCB 8-kanal REDDEDİLDİ · AC şebeke REDDEDİLDİ (DC-ONLY) | §3.1 A1–A8 |
| ADR-062 | Sinyal zinciri tek, sabit sıralı seri zincir; RT kısıtları ADR-017'ye hizalı | değişken sıralı zincir |
| ADR-038 | DAC/codec: PCM3168A + XMOS (H001) | PCM5122 (hiçbir yerde referans yasağı) |

## §4 Yerel Kararlar (K002-içi — KD-, ADR DEĞİL)

| # | Karar | Alternatif | Gerekçe | Kanıt |
|---|---|---|---|---|
| KD-E01 | K002 yalnız **şartname** üretir; register map/boot sequence **kodu K005'e** ait | kodu buraya almak | master §4.3 "bu domainin SINIRI" | `electroncis-master.md:205-213` |
| KD-E02 | PCB/BOM tasarımı K002'nin; fiziksel envanter K001'in | ikisi tek düğümde | sınır KAPANDI 2026-10-15 | `K001-donanım/index.md` §GENEL BAKIŞ |
| KD-E03 | Boot bütçesi ~1.1s (8 aşama) şartname olarak yazılır | "yaklaşık" ifadesi | aşama bazlı süre bütçesi (R9 azaltması) | master §5.3 (boot-sequence.md satır 28-42) |
| KD-E04 | Koruma önceliği: short > DC offset > over-current > over-temp (tepki süreleri sıralı) | tek genel koruma | master §5.4 öncelik matrisi | `protection.md` satır 37-53 |
| KD-E05 | Ölçüm kapıları: SNR >100dB · THD+N <0.01%@1W · <0.001%@10W · ripple <10mV · DC offset <0.5V | pazarlama değeri | AES17 fabrika testi (ADR-063 §2e) | master §14.1-§14.2 |
| KD-E06 | PVDD aralığı Class D'de **18–53.5V (datasheet)** yazılır; görev iddiası 24–52V ⚠️ işaretli kalır | iddiayı düzeltmek | zero-hallucination: datasheet kazanır, vault satırı Vault Steward'a ait | master §5.2 · index FM open_question |

## §5 Değiştirilemez Kararlar (frozen)

1. **PCM5122 yasak** — ADR-038 H001: yalnız PCM3168A; alternatif **önerilemez** (ADR-089 §3.1 A3).
2. **32-bit float** — ADR-017 (DSP donanım modu) dışında dönüşüm yok.
3. **IPC-6012/A-610 Class 2** — ADR-063 §2(c); sınıf değişimi = yeni ADR.
4. **ADR-063 §2(a) PCB kural tablosu** — değişimi = yeni ADR (k19-pcb/index.md:44-55).

## §6 ⚠️ Açık Sorular / Çelişkiler (salt rapor)

- ADR-064 "dosya yok ⚠️" (ADR-063 §2f) ↔ dosya diskte VAR (2026-10-18) → **C-ADR**.
- `architecture/index.md` §6 "K002 yok (ID GAP)" ↔ `K002-electroncis/` 16 md VAR → **C1** (ADR-102 §6).
- EMC standart numaraları (CISPR/IEC) → master §14.3'te `⚠️ VERIFICATION REQUIRED`.
- `K002-altyapi` · `K002-data` · `K020-comilance` vb. ölü K referansları → salt rapor (düzeltme YASAK).

## §7 Kanıt

- `.ai/.decisions/index.md` — ADR listesi ve kategorileri (2026-10-18 okundu)
- `.ai/.decisions/accepted/ADR-{062,063,064,089,090}-*.md` §2 Karar satırları (grep+read, 2026-10-18)
- `electroncis-master.md` §4.3 · §5.3-§5.4 · §6 · §7 · §14
