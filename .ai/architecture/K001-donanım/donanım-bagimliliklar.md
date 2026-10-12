---
title: "K001-donanım — Bağımlılıklar"
type: architecture-dependencies
node_id: K001-donanım
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# donanım-bagimliliklar — K001 Bağımlılık Matrisi

## §1 Etiket kuralı (önce oku)

| Etiket | Anlamı |
|---|---|
| **[TASARIM]** | Tasarım iddiası — disk kanıtı yok, mimari niyeti yansıtır |
| **[KANIT]** | Dosya yolu / URL ile kanıtlanmış satır |
| **[BİLİNMEYEN]** | Tahmin edilmedi → ⚠️ VERIFICATION REQUIRED |

> Bu dosyanın §2 matrisi **[TASARIM]** etiketlidir; K001 bu turda kod olarak
> uygulanmamıştır. [KANIT] taşıyan satırlar §3 ve §4'tedir.

## §2 depends_on / used_by matrisi

| Düğüm | Yön | Etiket | Gerekçe |
|---|---|---|---|
| K001-donanım | → K000-isletim-sistemi | [TASARIM] | Fiziksel parça ancak OS (sürücü) üzerinden görünür olur |
| K001-donanım | → K005-firmware | [TASARIM] | Donanımın başlatılması/kontrolü firmware'in işidir (bileşim, tek yön değil) |
| K003-audio | → K001-donanım | [TASARIM] | Ses motoru DAC/ADC ve ses kartına fiziksel olarak bağlanır |
| K004-dsp | → K001-donanım | [TASARIM] | DSP çıktısı sonunda fiziksel amplifikatöre/ hoparlöre iner |
| K000-isletim-sistemi | → K001-donanım | [TASARIM] | OS, donanımı sürücü arayüzünden görür |
| K001-donanım | → (geriye, K003/K004) | [TASARIM] | Donanım, yazılım katmanına **geri bağımlı değildir** |

### §2.1 Özet

- **Giden:** 2 tasarım iddiası (K000 · K005)
- **Gelen:** 4 tasarım iddiası (K000 · K003 · K004 · K005)
- **Kanıtlanmış bağımlılık:** 0 → hepsi [TASARIM]

## §3 Entegrasyon sınırları

| Sınır | Ne taşır | Etiket |
|---|---|---|
| Donanım ↔ sürücü (K000) | Cihaz keşfi, DMA/buffer aktarımı | [TASARIM] |
| Donanım ↔ firmware (K005) | Boot, register yazımı, güncelleme | [TASARIM] |
| Ses donanımı ↔ K003 | DAC/ADC, I2S/TDM hattı | [TASARIM] |
| Ölçüm eşikleri ↔ kabul | SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | [KANIT] — ~/.config/opencode/AGENTS.md §5 · 2026-10-12 |
| Çip değişimi kuralı | PCM5122 → PCM3168A / AK4458 öner | [KANIT] — .ai/AGENTS.md §17 · 2026-10-12 |

## §4 Riskli bağımlılıklar

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | PVDD aralığı uyuşmazlığı (24–52 V ↔ 18–53.5 V) | Güç tasarım hatası → termal/EMI riski | ⚠️ donanım-dogrulama.md Q2 |
| 2 | donanım-kernel ↔ donanım-core belirsizliği | Aynı iş iki yerde tanımlanabilir | ⚠️ kapsam onayı |
| 3 | donanaım-type envanteri eksik (ekran, kablo) | Kapsam boşluğu | ⚠️ envanter + ADR |
| 4 | K002 ID GAP | Kayıt defteri tutarsızlığı | ⚠️ registry §6 — onaysız düzeltilmez |
| 5 | Ölçüm eşikleri kod tarafında karşılanmıyor mu | Ürün kalite kapısı | Ölçüm protokolü K019-testing'e taşınır |

## §5 Kapanış

- Bu dosya **salt tasarım kaydıdır**; status: draft.
- Kanıtlanan her satır [TASARIM] → [KANIT] olur ve donanım-dogrulama.md §1'e düşülür.
- Etiketsiz bağımlılık satırı bu dosyada geçerli sayılmaz.

## §6 Bağımlılık yönü ve katman kapısı

A1 bant kuralı: K000 (zemin) → K001 (donanım) → K003 (ses motoru) → K004 (DSP) → K005 (firmware).
K001 bu dizide **ikinci katmandır**; aşağısına (K000) bağımlıdır, yukarısına (K003/K004/K005)
yalnızca **fiziksel/arayüz** yüzeyinden görünür — geriye kod bağımlılığı taşımaz.

| Yön | Satır | Tür | Etiket |
|---|---|---|---|
| K001 → K000 | §2 satır 1 | arayüz (sürücü) | [TASARIM] |
| K001 → K005 | §2 satır 2 | zamanlama (boot/register) | [TASARIM] |
| K003 → K001 | §2 satır 3 | fiziksel (DAC/ADC, I2S/TDM) | [TASARIM] |
| K004 → K001 | §2 satır 4 | fiziksel (amplifikatör çıkışı) | [TASARIM] |
| K000 → K001 | §2 satır 5 | arayüz (sürücü keşfi) | [TASARIM] |

### §6.1 Bağımlılık türü sınıflandırması

| Tür | Seviye | K001 örneği | Kanıt durumu |
|---|---|---|---|
| Fiziksel | PCB / kablo / IC | DAC ↔ amplifikatör sinyal hattı | [TASARIM] |
| Arayüz | sürücü / protokol | donanım ↔ K000 sürücü katmanı | [TASARIM] |
| Zamanlama | boot / register / heartbeat | K005'in donanımı başlatması | [TASARIM] |
| Ölçüm | eşik / protokol | SNR · THD+N kabul kriteri | [KANIT] — §3 |

### §6.2 Kapı (gate) kuralı

1. Yeni bağımlılık satırı **yalnız** [TASARIM] ile açılır; kanıtsız [KANIT] yazılmaz.
2. Bir satır [KANIT]'a dönerse kanıtı donanım-dogrulama.md §1'e numarayla işlenir.
3. Yön tersine dönerse (K001 → K003 gibi) bu bir **layer violation** sayılır ve §4'e
   yeni risk satırı olarak eklenir — silinmez.
4. §2 ile §6 çelişirse §2 matrisi kazanır; §6 yalnız açıklama katmanıdır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12