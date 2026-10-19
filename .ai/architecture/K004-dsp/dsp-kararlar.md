---
node_id: K004-dsp/dsp-kararlar
node_type: subdomain
status: draft
parent_id: K004-dsp
children: []
depends_on: [K003-audio, K000-isletim-sistemi, K001-donanım, K005-firmware]
evidence:
  - ".ai/.decisions/index.md ADR listesi (ADR-017 Audio · ADR-019 Audio · ADR-038 Audio · ADR-097 draft/Architecture) · 2026-10-18"
  - ".ai/architecture/K004-dsp/dsp-master.md §B.1 (M1-M6 karar kademesi) · §B.3 (teknoloji matrisi) · §B.6 (Q1-Q5) · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md (Q5 kapatan karar — 2026-10-15) · 2026-10-18"
validation: "ADR numaraları/başlıkları yalnız .ai/.decisions/index.md'den alınır; yeni ADR uydurulmaz; yerel kararlar KD-D önekiyle ADR'den ayrışır; ADR-097 DRAFT durumundadır — bağlayıcı değil, atıf olarak taşınır."
open_questions:
  - "⚠️ ADR-097 draft'tır (K-Space V3, Architecture) — §5.5 tier-3 kuralı bu dosyada atıf olarak kullanılır; draft onaylanana kadar bağlayıcı sayılmaz."
  - "⚠️ Q1 (DSPark '90+' ↔ 2★) · Q2 (Signalsmith basics MIT LICENSE teyidi) · Q3 (dynamics↔compressor/limiter) · Q4 (measurement↔testing) açık — Vault Steward/researcher kapısı."
  - "⚠️ JUCE 9 ve ASIO SDK 2.3.4 sürüm iddialarının bağımsız kanıtı YOK (master §C.6) — bağımlılık bump talebi gelirse DUR + handover."
group: mimari
---

# K004-dsp — Kararlar (ADR + Yerel KD)

> **Kaynak kuralı:** ADR numaraları/başlıkları **yalnız**
> `.ai/.decisions/index.md`'den alınır. Burada yeni ADR **uydurulmaz**;
> domain-içi küçük kararlar `KD-` önekiyle işaretlenir ve **ADR değildir**.

## §1 ELI15

Karar = "şunu yapıyoruz, şunu yapmıyoruz, nedeni şu". Bu dosyada iki tür karar
vardır: (1) **ADR** — vault'ta kayıtlı, numarası indeksle kanıtlanan bağlayıcı
kararlar; (2) **KD-D** — K004 kökünde tutulan yerel DSP kararları. ADR numarası
indekste yoksa o numara **yazılmaz**.

## §2 Bağlayıcı ADR'ler (indeks kanıtlı)

| ADR | Başlık (indeks birebir) | Kategori | Durum | K004 etkisi |
|---|---|---|---|---|
| ADR-017 | DSP Hardware Mode (XMOS, JUCE, ASIO) | Audio | accepted | **Zincir hesabı HOST'ta** (NevaEngine/JUCE); XMOS XU316 yalnız hard-RT zamanlama + I2S/TDM. 32-bit float (master §C.1 C6) |
| ADR-019 | Per-OS Neva Player | Audio | accepted | Per-OS player **FROZEN** — değişiklik ≥088 (master §C.1 C10) |
| ADR-038 | 8.1 Sound Card (PCM3168A + XMOS) | Audio | accepted | H001: yalnız PCM3168A · DC offset >0.5 V → koruma rölesi (master §C.1 C9) |
| ADR-097 | K-Space V3: Sıfırdan Kurulum, Çoklu-MD Düğüm Şeması, İsimlendirme Spesifikasyonu ve Band-2..7 Rezervasyonu | Architecture | **draft** | §5.5: tier-3 dosya adı `<teknoloji>-<rol>` öneki zorunlu (soyut ad YASAK) — atıf, bağlayıcı değil |

## §3 Yerel Kararlar (KD-D — K004 kökü, ADR değildir)

| # | Karar | Gerekçe | Kanıt |
|---|---|---|---|
| KD-D01 | K004, K003'ün realtime döngüsüne **graf düğümü** olarak eklenir; kendi thread/clock'ı yoktur | Sınır kuralı — ters yön (K003'ün K004'ü bilmesi) ihlaldir | master §A.6 · §B.5 |
| KD-D02 | Ses verisi biçimi **32-bit float** | ADR-017 kilidi | master §C.1 C6 |
| KD-D03 | Buffer 512 örnek · 48 kHz — **şartname, ölçüm PLANNED** | Sartname; ölçülmemiş "gerçek zamanlı" denmez | master §C.5 |
| KD-D04 | Harici DSP bağımlılığı yalnız lisans+sabit sürüm kanıtıyla girer; aksi halde **kendi kodumuz varsayılan** | framework bağımlılık artışı → kanıtsız bağımlılık alınmaz | master §C.4 |
| KD-D05 | `parent:` alanı (eski şema) yazılmaz → `parent_id` | FM v2 §4.2 | master §C.2 Y4 |
| KD-D06 | Envanterle kesişen URL dosyalara bağ olarak yazılmaz; iddia metni korunur, URL envanter-dışı havuzdan veya URL'siz taşınır | Görev kuralı (Y5) | master §C.2 Y5 · §B.4 politika notu |

## §4 Açık Sorular (Q1-Q5 — kök seviye, master §B.6 birebir)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | DSPark '90+' iddiası ne anlama geliyor, doğru depo hangisi? (T5 '90+' ↔ T6 2★ çelişkisi) | ⚠️ VERIFY REQUIRED | researcher |
| Q2 | Signalsmith basics lisansı MIT mi (LICENSE teyidi)? | ⚠️ VERIFY REQUIRED | researcher |
| Q3 | dynamics ↔ compressor/limiter hiyerarşisi mi, kardeş mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | measurement ↔ testing sorumluluk ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | DSP hedefi donanım içinde mi (K005) host'ta mı (K003 üstü)? | ✅ **KAPANDI 2026-10-15** — ADR-017 (accepted) | Vault Steward (ADR-017) |

## §5 Teknoloji Karar Matrisi (kanıt durumuna göre — master §B.3)

| Teknoloji | Kanıt | K004 kararı | Not |
|---|---|---|---|
| JUCE dsp modülü (Limiter/Reverb/Convolution/IIR/Oversampling) | ✅ resmi doxygen (2026-10-13 §B.4) | Aday işleyici kütüphanesi | Sürüm kanıtı ayrı → §6 |
| Signalsmith dsp | ✅ repo (2026-10-12) | Aday DSP kütüphanesi | Sürüm bilgisi yok → UNKNOWN |
| Signalsmith basics | ✅ repo (2026-10-12) | Aday yardımcı kütüphane | LICENSE teyidi → Q2 |
| Patina (analog modeling, C++17) | ✅ repo (2026-10-12) | Referans inceleme kaynağı | Lisans okunmadı → ⚠️ |
| DSPark v1 | ⚠️ çelişkili (90+ ↔ 2★) | **Karar yok** — kanıt gelene kadar kullanılmaz | Q1 |
| Harici bağımlılıksız kendi kodumuz | — | Varsayılan yaklaşım | KD-D04 |

**Kural:** Bu matrisde ✅ olmayan satır, K004 tasarımında teknoloji seçimi olarak
kullanılamaz; yalnız ad olarak anılır (master §B.3).

## §6 Sürüm İddiaları (VERIFICATION REQUIRED — master §C.6)

| # | İddia | Durum | Davranış |
|---|---|---|---|
| 1 | JUCE 9 | ⚠️ bağımsız sürüm kanıtı YOK | Doxygen erişimi dokümantasyonu kanıtlar, sürümü KANITLAMAZ |
| 2 | ASIO SDK 2.3.4 | ⚠️ bağımsız sürüm kanıtı YOK | Bağımlılık bump / firmware OTA / refactor talebi → **DUR** + handover |

## §7 Karar Kuralı (M-seviyeleri — master §B.1)

| Seviye | Tanım | K004 durumu |
|---|---|---|
| M1 | Domain anayasası (sınır, çocuk listesi) | ✅ 12 çocuk = v2 §2.4 birebir (2026-10-12) |
| M2 | Mimari yaklaşım (graf düğümü, clock yok) | ✅ [TASARIM] (KD-D01) |
| M3 | Teknoloji adayı seçimi | ⚠️ Q2 lisans teyidi bekliyor |
| M4 | Sayısal parametre (ceiling, ratio, latency bütçesi) | **UNKNOWN — uydurulmaz** (T8) |
| M5 | Ölçüm eşiği (SNR >100 dB · THD+N <0.01%@1kHz) | ✅ tek kaynak: `.config/opencode/AGENTS.md` §5 |
| M6 | Kod yazımı (motor) | ❌ başlamadı — depoda 0 satır (master §C.7) |

**M4 yasağı:** latency bütçesi gibi hiçbir sayı, kanıt gelene kadar yazılmaz
(ör. "10 ms latency" uydurulmaz).

## §8 Çapraz Referanslar

| Hedef | İlişki |
|---|---|
| `dsp-master.md` §B | Karar kademesi SSOT |
| `dsp-kapsam.md` §6 | Ölçüm kapıları (M5'in tablo hali) |
| `dsp-riskler.md` | Q1-Q4'ün risk karşılıkları (R1-R5) |
| `.ai/.decisions/index.md` | ADR numara/başlık SSOT |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
