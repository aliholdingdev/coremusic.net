---
title: "K003-audio — Bağımlılıklar"
type: architecture-dependencies
node_id: K003-audio
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# audio-bagimliliklar — K003 Bağımlılık Matrisi

## §1 Etiket kuralı (önce oku)

| Etiket | Anlamı |
|---|---|
| **[TASARIM]** | Tasarım iddiası (design claim) — disk kanıtı yok, mimari niyeti yansıtır |
| **[KANIT]** | Dosya yolu / URL ile kanıtlanmış satır |
| **[BİLİNMEYEN]** | Tahmin edilmedi → ⚠️ VERIFICATION REQUIRED |

> Bu dosyanın tamamı **açıkça tasarım iddiasıdır**; K003 bu turda kod olarak
> uygulanmamıştır. Kanıt satırları yalnız §3/§4 ve audio-dogrulama.md §1'dedir.

## §2 depends_on / used_by matrisi

| Düğüm | Yön | Etiket | Gerekçe |
|---|---|---|---|
| K003-audio | → K000-isletim-sistemi | [TASARIM] | Ses API'si, sürücü yükleme ve kaynak yönetimi OS zemininde yaşar |
| K003-audio | → K001-donanım | [TASARIM] | DAC/ADC, ses kartı ve I2S/TDM hattı fiziksel muhattır |
| K003-audio | → K005-firmware | [TASARIM] | Donanım boot olduktan sonra ses yolu kullanılabilir hale gelir |
| K004-dsp | → K003-audio | [TASARIM] | DSP zinciri, motorun akışına graph olarak eklenir |
| K000-isletim-sistemi | → (çağıran) K003 | [TASARIM] | OS, ses hizmetini istemci olarak çağırır (used_by) |
| Uygulama katmanı | → (çağıran) K003 | [TASARIM] | Oynat/duraklat komutları motoru tetikler (used_by) |

### §2.1 Özet

- **Giden (depends_on):** 3 tasarım iddiası (K000 · K001 · K005)
- **Gelen (used_by):** 3 tasarım iddiası (K000 · K004 · uygulama katmanı)
- **Kanıtlanmış bağımlılık:** 0 → hepsi [TASARIM]
- **Çapraz yön (K003 ↔ K004):** tek yönlü sayılır — K004 K003'e bağımlıdır;
  K003, K004'ü yalnızca **graf düğümü olarak çağırır**, içine girmez.

## §3 Entegrasyon sınırları

| Sınır | Ne taşır | Etiket |
|---|---|---|
| K003 ↔ K000 | Ses API çağrısı, sürücü yükleme, thread/öncelik | [TASARIM] |
| K003 ↔ K001 | DAC/ADC veri hattı, cihaz keşfi, DMA/buffer | [TASARIM] |
| K003 ↔ K004 | İşlenmiş/işlenmemiş akış + DSP parametresi (veri) | [TASARIM] |
| K003 ↔ K005 | Boot tamamlama sinyali, cihaz hazır durumu | [TASARIM] |
| Realtime döngü ↔ lock-free kuralı | Callback içinde kilitleme yok; buffer baştan allocate | [KANIT] — ~/.config/opencode/AGENTS.md §5 · 2026-10-12 |
| ASIO ↔ WASAPI fallback | Cihaz kaybı durumunda geri düşme | [KANIT] — .ai/AGENTS.md §17 #6 · 2026-10-12 |
| Exclusive mode ↔ paylaşımlı kip | Düşük gecikme / dayanıklılık tercihi | [KANIT] — Microsoft WASAPI docs · 2026-10-12 |

## §4 Riskli bağımlılıklar

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | ASIO SDK GPLv3 lisansı | Yasal uyum riski (derived work tartışması) | ⚠️ Q1 — legal-advisor |
| 2 | JUCE sürüm/sayım belirsizliği (8811★ kanıtlanamadı) | Karar dayanağı zayıf | ⚠️ Q3 — researcher |
| 3 | NAudio+ASIO thread başlığı eşleşmedi | Kaynak atfı hatalı olabilir | ⚠️ Q2 — researcher |
| 4 | engine ↔ backend kapsam örtüşmesi | İki yerde aynı döngü/queue | ⚠️ Q5 — kapsam onayı |
| 5 | input/output ↔ device/driver ayrımı belirsiz | Sorumluluk dağılımı bulanık | ⚠️ Q6 — kapsam onayı |
| 6 | K002 ID GAP | Kayıt defteri tutarsızlığı | ⚠️ registry §6 — onaysız düzeltilmez |
| 7 | K003 ↔ K004 çift yönlü sızma | Circular dependency | Sınır kuralı: master §2.2 — veri gider, kod girmez |

## §5 Bağımlılık yönü ve katman kapısı

A1 bant sırası: K000 (zemin) → K001 (donanım) → K003 (motor) → K004 (DSP) → K005 (firmware).
K003 bu dizide **üçüncü katmandır**: altındakilere arayüz yüzeyinden bağımlıdır,
üstündekiler (K004 ve uygulama) onu çağıran taraftır.

| Tür | Seviye | K003 örneği | Kanıt durumu |
|---|---|---|---|
| Arayüz | API / sürücü | K000 ses API'si | [TASARIM] |
| Fiziksel | kart / hat | DAC/ADC, I2S/TDM (K001) | [TASARIM] |
| Zamanlama | boot / hazır | K005 boot tamamlama | [TASARIM] |
| Zamanlama | realtime | lock-free callback, ön-allocate buffer | [KANIT] — §3 |
| Lisans | SDK | ASIO SDK GPLv3 | [KANIT] URL · etki ⚠️ |

### §5.1 Kapı (gate) kuralı

1. Yeni bağımlılık satırı yalnız [TASARIM] ile açılır; kanıtsız [KANIT] yazılmaz.
2. Satır [KANIT]'a dönerse kanıtı audio-dogrulama.md §1'e numarayla işlenir.
3. Yön tersine dönerse (ör. K001 → K003) bu layer violation'dır; silinmez, §4'e
   yeni risk satırı olarak eklenir.
4. §2 matrisi ile §5 açıklaması çelişirse §2 kazanır; §5 yalnız açıklama katmanıdır.

## §6 Kapanış

- Bu dosya **salt tasarım kaydıdır**; status: draft.
- Etiketsiz bağımlılık satırı bu dosyada geçerli sayılmaz.
- Kanıtlanan her satır [TASARIM] → [KANIT] olur ve dogrulama §1'e düşülür.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
