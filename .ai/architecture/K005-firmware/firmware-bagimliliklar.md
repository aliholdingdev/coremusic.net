---
title: "K005-firmware — Bağımlılıklar"
type: architecture-dependencies
node_id: K005-firmware
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# firmware-bagimliliklar — K005 Bağımlılık Matrisi

## §1 Etiket kuralı (önce oku)

| Etiket | Anlamı |
|---|---|
| **[TASARIM]** | Tasarım iddiası (design claim) — disk kanıtı yok, mimari niyeti yansıtır |
| **[KANIT]** | Dosya yolu / URL ile kanıtlanmış satır |
| **[BİLİNMEYEN]** | Tahmin edilmedi → ⚠️ VERIFICATION REQUIRED |

> §2 matrisi **açıkça tasarım iddiasıdır**; K005 bu turda kod olarak
> uygulanmamıştır. Kanıt satırları §3, §4 ve firmware-dogrulama.md §1'dedir.

## §2 depends_on / used_by matrisi

| Düğüm | Yön | Etiket | Gerekçe |
|---|---|---|---|
| K005-firmware | → K001-donanım | [TASARIM] | Register, sensör ve güç devresi fiziksel olarak altındadır |
| K001-donanım | → (barındıran) K005 | [TASARIM] | Donanım, firmware olmadan işlev görmez (barındırma yönü) |
| K000-isletim-sistemi | → (çağıran) K005 | [TASARIM] | Host, cihazı firmware üzerinden keşfeder/kontrol eder |
| K003-audio | → (çağıran) K005 | [TASARIM] | Ses yolu hazır durumu ve kip ayarı firmware'de |
| K004-dsp | → K005 | [TASARIM] ⚠️ | DSP donanım içindeyse firmware'e bağlanır — kapsam belirsiz |
| K005-firmware | → K000-isletim-sistemi | [TASARIM] | Firmware'in güncelleme/rapor kanalı host tarafına açılır |

### §2.1 Özet

- **Giden (depends_on):** 2 tasarım iddiası (K001 · K000) + 1 belirsiz (K004 ⚠️)
- **Gelen (used_by):** 4 tasarım iddiası (K001 · K000 · K003 · K004)
- **Kanıtlanmış bağımlılık:** 0 → hepsi [TASARIM]
- **Not:** K001 ↔ K005 çift satırı **döngü değildir** — biri barındırma (fiziksel),
  diğeri ayarlama (yazılım) yönüdür; ikisi ayrı satır olarak taşınır.

## §3 Entegrasyon sınırları

| Sınır | Ne taşır | Etiket |
|---|---|---|
| K005 ↔ K001 | Register yazımı, sensör okuma, güç devresi | [TASARIM] |
| K005 ↔ K000 | Cihaz keşfi, güncelleme kanalı, rapor | [TASARIM] |
| K005 ↔ K003 | Ses kipi/ayar sorgusu (kayıt: veri yolu taşınmaz) | [TASARIM] |
| update ↔ security | İmzalı güncelleme ve geri alma | [TASARIM] ⚠️ Q4 |
| Güç aralığı ↔ kabul | TPA3255 PVDD: 18–53.5 V (TI datasheet) | [KANIT] — https://www.ti.com/lit/gpn/TPA3255 · 2026-10-12 |
| PVDD manifest iddiası | 24–52 V (görev manifesti) — ⚠️ uyuşmazlık | [KANIT] iki taraflı kayıt · Q2 |
| Referans şema | TPA3255 PBTL KiCad projesi | [KANIT] — https://github.com/EliMattingly22/TPA3255_ClassD_PBTL · 2026-10-12 |
| Lisans sınırı | ASIO SDK GPLv3 — etki değerlendirilmedi | [KANIT] URL · etki ⚠️ Q1 |

## §4 Riskli bağımlılıklar

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | PVDD uyuşmazlığı (24–52 V ↔ 18–53.5 V) | Güç tasarımı hatası → termal/EMI riski | ⚠️ Q2 — audio-hw kararı |
| 2 | ASIO SDK GPLv3 lisans etkisi | Yasal uyum riski | ⚠️ Q1 — legal-advisor |
| 3 | update ↔ security kapsam örtüşmesi | İmzalı güncelleme boşluğu | ⚠️ Q4 — kapsam onayı |
| 4 | boot ↔ runtime ↔ device-control sınırı belirsiz | Aynı iş iki yerde | ⚠️ Q3 |
| 5 | Donanım/araç zinciri hedefi kesin değil (XMOS) | Planlama dayanağı zayıf | ⚠️ Q5 |
| 6 | K004 ↔ K005 çift yönlü sızma | Circular dependency | Sınır: veri gider, register'a iki taraf da tek yazıcıyla dokunur |
| 7 | Register sahipliği belirsiz (K003 mü K005 mı yazar) | Çakışan yazım, tahmin edilemez davranış | §3 sınır satırı — sahiplik kararı gerekli |

## §5 Bağımlılık yönü ve katman kapısı

A1 bant sırası: K000 (zemin) → K001 (donanım) → K003 (motor) → K004 (DSP) → K005 (firmware).
K005 bu dizide **beşinci (son) katmandır**; bağımlılığı dipte K001'e iner.

| Tür | Seviye | K005 örneği | Kanıt durumu |
|---|---|---|---|
| Fiziksel | register/devre | K001 sensör ve güç devresi | [TASARIM] |
| Arayüz | host kanalı | K000 cihaz keşfi / güncelleme kanalı | [TASARIM] |
| Çalışma zamanı | sorgu | K003 ses kipi hazır durumu | [TASARIM] |
| Ölçüm | güç aralığı | TPA3255 PVDD 18–53.5 V | [KANIT] URL · karşı iddia ⚠️ |
| Lisans | SDK | ASIO SDK GPLv3 | [KANIT] URL · etki ⚠️ |

### §5.1 Kapı (gate) kuralı

1. Yeni bağımlılık satırı yalnız [TASARIM] ile açılır; kanıtsız [KANIT] yazılmaz.
2. Satır [KANIT]'a dönerse kanıtı firmware-dogrulama.md §1'e numarayla işlenir.
3. Yön tersine dönerse bu layer violation'dır; silinmez, §4'e risk satırı eklenir.
4. §2 matrisi ile §5 çelişirse §2 kazanır; §5 yalnız açıklama katmanıdır.
5. PVDD gibi çelişkili sayılar **iki değer de** korunur; biri silinmez.

## §6 Kapanış

- Bu dosya **salt tasarım kaydıdır**; status: draft.
- Etiketsiz bağımlılık satırı bu dosyada geçerli sayılmaz.
- Kanıtlanan her satır [TASARIM] → [KANIT] olur ve dogrulama §1'e düşülür.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
