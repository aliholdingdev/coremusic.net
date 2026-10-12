---
title: "K004-dsp — Bağımlılıklar"
type: architecture-dependencies
node_id: K004-dsp
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# dsp-bagimliliklar — K004 Bağımlılık Matrisi

## §1 Etiket kuralı (önce oku)

| Etiket | Anlamı |
|---|---|
| **[TASARIM]** | Tasarım iddiası (design claim) — disk kanıtı yok, mimari niyeti yansıtır |
| **[KANIT]** | Dosya yolu / URL ile kanıtlanmış satır |
| **[BİLİNMEYEN]** | Tahmin edilmedi → ⚠️ VERIFICATION REQUIRED |

> Bu dosyanın §2 ve §5 matrisleri **açıkça tasarım iddiasıdır**; K004 bu turda
> kod olarak uygulanmamıştır. Kanıt satırları §3, §4.1 ve dsp-dogrulama.md §1'dedir.

## §2 depends_on / used_by matrisi

| Düğüm | Yön | Etiket | Gerekçe |
|---|---|---|---|
| K004-dsp | → K003-audio | [TASARIM] | DSP'in işleyeceği ham akış oradan gelir; graph'a orada eklenir |
| K004-dsp | → K000-isletim-sistemi | [TASARIM] | CPU, thread ve bellek kaynağı OS zemininde |
| K004-dsp | → K001-donanım | [TASARIM] | Şekillendirilmiş sinyal nihayet amplifikatöre/hoparlöre iner |
| K004-dsp | → K005-firmware | [TASARIM] ⚠️ | DSP donanım içinde çalışırsa firmware sınırına girer — kapsam belirsiz |
| K003-audio | → (çağıran) K004 | [TASARIM] | Motor, işlem noktasını soyut düğüm olarak çağırır (used_by) |
| Uygulama katmanı | → (çağıran) K004 | [TASARIM] | EQ/reverb preset komutları akışa veri olarak girer (used_by) |

### §2.1 Özet

- **Giden (depends_on):** 3 net + 1 belirsiz tasarım iddiası (K000 · K001 · K003 · K005⚠️)
- **Gelen (used_by):** 2 tasarım iddiası (K003 · uygulama katmanı)
- **Kanıtlanmış bağımlılık:** 0 → hepsi [TASARIM]
- **Önemli:** K004 → K005 satırı tek taraflı iddiadır; K005 kaydında karşılığı
  henüz yok → çapraz kayıt simetrisi kanıt turunda doğrulanır.

## §3 Entegrasyon sınırları

| Sınır | Ne taşır | Etiket |
|---|---|---|
| K004 ↔ K003 | İşlenmiş akış + soyut işlem düğümü (graph node) | [TASARIM] |
| K004 ↔ K004 pipeline | Sıra: filters → dynamics → reverb vb. (sıra iddiası) | [TASARIM] ⚠️ Q3 |
| K004 ↔ uygulama | Preset/parametre verisi (kullanıcı ayarı) | [TASARIM] |
| K004 ↔ ölçüm | measurement çıktısı → kabul kriteri karşılaştırması | [TASARIM] |
| Ölçüm eşikleri ↔ kabul | SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | [KANIT] — ~/.config/opencode/AGENTS.md §5 · 2026-10-12 |
| Layer violation kuralı | K003'ün K004 içine kod girmesi yasak | [KANIT] — .ai/AGENTS.md §17 #7 · 2026-10-12 |

## §4 Riskli bağımlılıklar

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | DSPark iddiası (90+) ↔ ölçüm (2★) çelişkisi | Teknoloji kararının dayanağı zayıf | ⚠️ Q1 — researcher |
| 2 | Harici DSP kütüphane bağımlılığı (lisans/sürüm) | Lisans ve bakım riski | §4.1 matrisi — ✅ olmayan kullanılmaz |
| 3 | K004 → K005 kapsam belirsizliği | İki yerde aynı iş | ⚠️ Q5 — embedded + dsp-fw |
| 4 | pipeline sırası tanımsız | Sonuç tutarsız, test edilemez | ⚠️ Q3 — kapsam onayı |
| 5 | dynamics ↔ compressor/limiter hiyerarşisi | Kapsam örtüşmesi | ⚠️ Q3 |
| 6 | K003 ↔ K004 çift yönlü sızma | Circular dependency | Sınır: veri gider, kod girmez (master §2.2) |
| 7 | Latency bütçesi bilinmiyor (T8 UNKNOWN) | Realtime ihlal riski | ⚠️ kanıt turu — sayı uydurulmaz |

## §5 Bağımlılık yönü ve katman kapısı

A1 bant sırası: K000 (zemin) → K001 (donanım) → K003 (motor) → K004 (DSP) → K005 (firmware).
K004 bu dizide **dördüncü katmandır**: K003'ün üstünde yaşar, altındakilere
yalnızca akış/parametre arayüzünden bağımlıdır.

| Tür | Seviye | K004 örneği | Kanıt durumu |
|---|---|---|---|
| Çalışma zamanı | akış | K003 graph düğümü | [TASARIM] |
| Çalışma zamanı | kaynak | OS CPU/thread/bellek | [TASARIM] |
| Fiziksel | çıkış | amplifikatör/hoparlör (K001) | [TASARIM] |
| Ölçüm | eşik | SNR · THD+N kabul kriteri | [KANIT] — §3 |
| Lisans | kütüphane | Signalsmith MIT (teyit bekliyor) | ⚠️ — §4.1 |

### §5.1 Kapı (gate) kuralı

1. Yeni bağımlılık satırı yalnız [TASARIM] ile açılır; kanıtsız [KANIT] yazılmaz.
2. Satır [KANIT]'a dönerse kanıtı dsp-dogrulama.md §1'e numarayla işlenir.
3. Yön tersine dönerse bu layer violation'dır; silinmez, §4'e risk satırı eklenir.
4. §2 matrisi ile §5 çelişirse §2 kazanır; §5 yalnız açıklama katmanıdır.
5. §4.1 (master) matrisinde ✅ olmayan teknolojiye bağımlılık satırı açılamaz.

## §6 Kapanış

- Bu dosya **salt tasarım kaydıdır**; status: draft.
- Etiketsiz bağımlılık satırı bu dosyada geçerli sayılmaz.
- Kanıtlanan her satır [TASARIM] → [KANIT] olur ve dogrulama §1'e düşülür.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
