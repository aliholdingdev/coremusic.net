---
title: "K005-firmware — Master"
type: architecture-master
node_id: K005-firmware
status: draft
date: 2026-10-12
updated: 2026-10-12
source: "v2 §2.5 (coremusic-katmanli-mimari-agaci-v2.md)"
---

# firmware-master — K005 Firmware Anayasası

## §1 Rol (ELI15)

**Donanımın kendi küçük yazılımı — cihaz açılırken çalışan 'iç işletim sistemi'.**

K005, çipin üstünde yaşar ve donanımı **uyandıran, ayarlayan, gözeten**
yazılımdır. ELI15 benzetme: bir arabanın gösterge panelinin arkasındaki yazılım
— marşa basınca motoru tanıtır (boot), çalışırken sıcaklığı izler (runtime),
koltuğu ısıtır (device-control), yakıtı keser (power-control) ve arıza olursa
lambayı yakar (diagnostics). Sürücünün kullandığı müzik uygulaması değil,
arabanın kendisini ayakta tutan içerideki koddur.

## §2 Kapsam / Sınır

### §2.1 Ne K005'in işidir

| Alan | Kapsam içi |
|---|---|
| açılış | boot — ilk kod, kendini tanıma, yükleme |
| anlık | runtime — sürekli dönen kontrol döngüsü |
| komut | device-control · audio-control · power-control |
| sağlık | diagnostics — kendi kendini test |
| değişim | update — güncelleme ve geri alma |
| koruma | security — imza/erişim kilidi |
| kanıt | testing — firmware testleri |

### §2.2 Ne DEĞİL (sınır)

| Konu | Sahibi | Gerekçe |
|---|---|---|
| PCB, DAC/ADC, amplifikatör devresi | K001-donanım | Donanım tasarımı ayrı alan |
| Host işletim sistemi, sürücü yükleme | K000-isletim-sistemi | Karşıdaki büyük OS |
| Ses verisinin taşınması | K003-audio | Firmware veri yolunu **ayarlar**, kendi başına **taşıyıcı değildir** |
| EQ/reverb matematiği | K004-dsp | Şekillendirme ayrı alan |
| Kullanıcıya görünen uygulama | uygulama katmanı | Firmware görünmezdir |

**Sınır kuralı:** Firmware, donanımın (K001) içindedir ama donanımın **tasarımı
değildir**; K003'ün işini yapmaz — yalnızca K003'ün ihtiyaç duyduğu kip ve
ayarlari register üzerinden kurar. İki taraf da aynı register'a yazarsa çakışma
oluşur → §5 risk satırı, sessiz çözüm yok.

## §3 Kademe-2 çocuk tablosu (v2 §2.5 — birebir, 9 çocuk)

| # | Çocuk | Kapsam (1 satır) | Üst düğüm | Durum |
|---|---|---|---|---|
| 1 | boot | İlk kod, kendini tanıma ve yükleme sırası | K005-firmware | draft |
| 2 | runtime | Sürekli dönen kontrol döngüsü | K005-firmware | draft |
| 3 | device-control | Parçalara komut (aç/kapat/ayarla) | K005-firmware | draft |
| 4 | audio-control | Ses donanımı kipi ve ayarları | K005-firmware | draft |
| 5 | power-control | Güç durumları ve akım kontrolü | K005-firmware | draft |
| 6 | diagnostics | Kendi kendini test ve hata raporu | K005-firmware | draft |
| 7 | update | Güncelleme ve geri alma (rollback) | K005-firmware | draft |
| 8 | security | İmza, erişim kilidi, koruma | K005-firmware | draft |
| 9 | testing | Firmware testleri ve kabul kriterleri | K005-firmware | draft |

> Çocuk tanımları yalnız addan türetilmiştir (v2 §3 KURAL 3) → her satır
> ⚠️ VERIFICATION REQUIRED taşır.

## §4 Teknoloji (yalnızca doğrulanmış web bulguları)

| # | Bulgu | Kanıt (URL) | Erişim | Durum |
|---|---|---|---|---|
| T1 | Steinberg ASIO SDK açık kaynak ilanı (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı |
| T2 | ASIO SDK lisansının K005 entegrasyonuna etkisi | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T3 | TPA3255 Class-D PBTL amplifikatör — TI datasheet'i | https://www.ti.com/lit/gpn/TPA3255 | 2026-10-12 | ✅ doğrulandı |
| T4 | TPA3255 PVDD aralığı: datasheet 18 V–53.5 V | aynı URL | 2026-10-12 | ✅ doğrulandı |
| T5 | Görev manifesti iddiası: TPA3255 24–52 V PVDD | görev manifesti | 2026-10-12 | ⚠️ T4 ile uyuşmuyor |
| T6 | TPA3255 PBTL için KiCad referans projesi (şematik + PCB) | https://github.com/EliMattingly22/TPA3255_ClassD_PBTL | 2026-10-12 | ✅ doğrulandı |
| T7 | ASIO→WASAPI fallback kuralı (host tarafı) | .ai/AGENTS.md §17 #6 | 2026-10-12 | ✅ dosya okundu |
| T8 | XMOS XU316 / I2S / TDM hedef yığını | .ai/AGENTS.md §4 (agent 10) | 2026-10-12 | ✅ dosya okundu (hedef) |
| T9 | XMOS araç zinciri/SDK sürümü bağımsız teyidi | — | 2026-10-12 | **UNKNOWN** |
| T10 | Güvenlik algoritması hedefi (Argon2id / AES-256-GCM) | .ai/AGENTS.md §5 (Security satırı) | 2026-10-12 | ✅ dosya okundu (hedef) |

**T4 ↔ T5 çelişki kaydı (PVDD):** İki değer de aynen kaydedilmiştir; **tek
taraflı düzeltme yasaktır**. Firmware/güç kontrolü (power-control) için hangi
aralığın geçerli olacağı üst karar gerektirir → açık soru Q2.

**T1/T2 lisans notu (⚠️):** GPLv3 kapsamındaki bir SDK'yı firmware/SDK
entegrasyonuna dahil etmek lisans yükümlülüğü doğurabilir; etki hukuki
değerlendirme gerektirir → açık soru Q1.

**Yasak:** T9 gibi doğrulanmamış araç/sürüm adları uydurulmaz — kanıt gelene
kadar UNKNOWN kalır.

## §5 Bağımlılık özeti

| Yön | Bağımlılık | Tür | Etiket |
|---|---|---|---|
| K005 → K001-donanım | Register'lar, sensörler, güç devresi | fiziksel | [TASARIM] |
| K001-donanım → K005 | Donanım, firmware olmadan uyanmaz | zamanlama | [TASARIM] |
| K003-audio → K005 | Ses yolu hazır durumu / kip sorgusu | çalışma zamanı | [TASARIM] |
| K004-dsp → K005 | DSP donanım içindeyse firmware sınırı | çalışma zamanı | [TASARIM] ⚠️ |
| K000-isletim-sistemi → K005 | Host, cihazı firmware üzerinden görür | arayüz | [TASARIM] |

A1 dizisi: K000 → K001 → K003 → K004 → K005. K005 **beşinci katmandır**, ancak
bağımlılığı en alttaki K001'e iner (döngüsel görünen ama farklı yüzeylerden:
firmware donanımı **ayarlar**, donanım firmware'i **barındırır**). Bu iki yön
farklı satırlardır ve ikisi de [TASARIM] etiketlidir.

## §6 Doğrulama özeti

| Kriter | Tanım | Şu an |
|---|---|---|
| D1 | v2 §2.5 mini-ağaç birebirliği (9 çocuk) | ✅ 2026-10-12 |
| D2 | 9 çocuk klasöründe index.md (8 zorunlu alan) | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama dosyaları | ✅ 2026-10-12 |
| D4 | açık ⚠️ / UNKNOWN satırı kalmadı | ❌ T2, T5 ⚠️ + T9 UNKNOWN |
| D5 | açık sorular kapandı | ❌ 5 açık |
| D6 | en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 | status: verified | ❌ D1–D7 ✅ olunca |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified. Kısmi yok.

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | ASIO SDK GPLv3, firmware/SDK entegrasyonunu nasıl etkiler? | ⚠️ VERIFY REQUIRED | legal-advisor + embedded |
| Q2 | TPA3255 PVDD hangisi: 24–52 V (manifest) mi, 18–53.5 V (TI) mı? | ⚠️ VERIFY REQUIRED | audio-hw |
| Q3 | boot ↔ runtime ↔ device-control sorumluluk sınırı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | update ↔ security (imzalı güncelleme) kime ait? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | Donanım hedefi (XMOS XU316) kesinleşti mi, araç zinciri? | ⚠️ VERIFY REQUIRED | embedded + dsp-fw |

## §8 Kaynaklar

| Kaynak | İlgili bölüm | Bağlantı |
|---|---|---|
| v2 §2.5 — K005-firmware/ mini-ağaç | §1, §2, §3 | coremusic-katmanli-mimari-agaci-v2.md (G: Drive · okundu 2026-10-12) |
| v2 §3 — adlandırma kuralları | §2, §3 | aynı dosya |
| v2 §4.1–4.3 — kayıt şeması | §6 | aynı dosya |
| ADR-097 K2 — multi-MD şeması | §1, §3 | .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md |
| Steinberg ASIO SDK (açık kaynak / GPLv3) | §4 T1, T2 · §7 Q1 | https://www.steinberg.net/developers/asiosdk-open |
| TI TPA3255 datasheet (18 V–53.5 V) | §4 T3, T4, T5 · §7 Q2 | https://www.ti.com/lit/gpn/TPA3255 |
| TPA3255 KiCad PBTL referansı | §4 T6 | https://github.com/EliMattingly22/TPA3255_ClassD_PBTL |
| Agent registry (XMOS XU316, I2S/TDM; ASIO→WASAPI) | §4 T7, T8 | .ai/AGENTS.md §4 · §17 (okundu 2026-10-12) |
| Güvenlik hedef yığını (Argon2id, AES-256-GCM) | §4 T10 | .ai/AGENTS.md §5 (okundu 2026-10-12) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
