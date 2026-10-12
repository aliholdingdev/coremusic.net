---
title: "K004-dsp — Master"
type: architecture-master
node_id: K004-dsp
status: draft
date: 2026-10-12
updated: 2026-10-12
source: "v2 §2.4 (coremusic-katmanli-mimari-agaci-v2.md)"
---

# dsp-master — K004 DSP Anayasası

## §1 Rol (ELI15)

**Sesin şekillendirildiği yer — bass, tiz, ekolayzır, oda hissi.**

K004, sesi **değiştiren** katmandır. Ham akış K003'ten gelir, burada
işlemden geçer ve tekrar akışa döner. ELI15 benzetme: K004 bir stüdyo
miksaj masasıdır — fader'lar, ekolayzır, kompresör ve oda simülasyonu
üstüne oturur; masanın kendisi müziği üretmez, yalnızca duyulanı şekillendirir.

## §2 Kapsam / Sınır

### §2.1 Ne K004'ün işidir

| Alan | Kapsam içi |
|---|---|
| pipeline | İşlem basamaklarının sırası ve akış grafiği |
| frekans | equalizer · filters · crossover |
| dinamik | dynamics · compressor · limiter |
| uzamsal | reverb · delay · spatial-audio |
| ölçüm | measurement (tepki ölçümü) · testing (bozulma kontrolü) |

### §2.2 Ne DEĞİL (sınır)

| Konu | Sahibi | Gerekçe |
|---|---|---|
| Sesin taşınması, cihaz seçimi, zamanlama | K003-audio | Taşıyıcı ayrı alan |
| DAC/ADC, amplifikatör devresi, PCB | K001-donanım | Fiziksel tasarım |
| Cihaz içindeki kontrol/komut yazılımı | K005-firmware | Donanım içi yazılım |
| OS kaynağı, sürücü, thread yönetimi | K000-isletim-sistemi | Zemin katmanı |
| Kullanıcının EQ preset arayüzü | uygulama katmanı | K004 motoru, UI'ı bilmez |

**Sınır kuralı:** K004, K003'ün realtime döngüsüne **graf düğümü olarak** eklenir;
kendi başına ayrı bir zaman çizgisi (thread/clock) iddia etmez. İddia ederse
bu K003 ile çakışma → §5 risk satırı olur, sessizce çözülmez.

## §3 Kademe-2 çocuk tablosu (v2 §2.4 — birebir, 12 çocuk)

| # | Çocuk | Kapsam (1 satır) | Üst düğüm | Durum |
|---|---|---|---|---|
| 1 | pipeline | İşlem basamaklarının sırası ve akış grafiği | K004-dsp | draft |
| 2 | equalizer | Frekans bantlarına göre seviye ayarı | K004-dsp | draft |
| 3 | filters | Alçak/yüksek/geçiren bant filtreleri | K004-dsp | draft |
| 4 | dynamics | Dinamik işlemin şemsiye tanımı | K004-dsp | draft |
| 5 | compressor | Eşik üstü seviyeyi tırpanlama | K004-dsp | draft |
| 6 | limiter | Aşırı seviyeyi kesen güvenlik katmanı | K004-dsp | draft |
| 7 | reverb | Yansıma/yankı taklidi — oda hissi | K004-dsp | draft |
| 8 | delay | Belirli gecikmeli kopya | K004-dsp | draft |
| 9 | crossover | Frekansa göre hoparlör ayrımı | K004-dsp | draft |
| 10 | spatial-audio | Konum/hissiyat katmanı | K004-dsp | draft |
| 11 | measurement | Frekans/zaman tepkisi ölçümü | K004-dsp | draft |
| 12 | testing | DSP testleri ve bozulma kontrolü | K004-dsp | draft |

> Çocuk tanımları yalnız addan türetilmiştir (v2 §3 KURAL 3) → her satır
> ⚠️ VERIFICATION REQUIRED taşır.

## §4 Teknoloji (yalnızca doğrulanmış web bulguları)

| # | Bulgu | Kanıt (URL) | Erişim | Durum |
|---|---|---|---|---|
| T1 | Patina — C++17 analog modeling projesi mevcut | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı |
| T2 | Signalsmith basics — MIT lisanslı C++ yardımcıları (116★) | https://github.com/Signalsmith-Audio/basics | 2026-10-12 | ✅ doğrulandı |
| T3 | Signalsmith dsp — DSP kütüphanesi (277★) | https://github.com/Signalsmith-Audio/dsp | 2026-10-12 | ✅ doğrulandı |
| T4 | Signalsmith basics MIT lisans iddiası (LICENSE teyidi) | repo LICENSE satırı okunmalı | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T5 | DSPark v1 — '90+' iddiası (manifest) | — | 2026-10-12 | ⚠️ VERIFY REQUIRED |
| T6 | DSPark GitHub ölçümü (2★) ve eşleşen depo | depo URL'i bulunamadı | 2026-10-12 | ⚠️ VERIFY REQUIRED + URL **UNKNOWN** |
| T7 | Ölçüm eşikleri: SNR > 100 dB · THD+N < 0.01 % @ 1 kHz | ~/.config/opencode/AGENTS.md §5 | 2026-10-12 | ✅ iç kanıt |
| T8 | DSP/zaman hedefi (ör. latency bütçesi) sayısal değeri | — | 2026-10-12 | **UNKNOWN** |

**T5/T6 çelişki kaydı:** Görev manifesti DSPark v1 için '90+' (birim belirtilmemiş)
iddiasını taşırken, GitHub ölçümü 2★ verdi ve eşleşen depo bulunamadı. İki değer
de kayıtlıdır; **hiçbiri tek taraflı düzeltilmez** → açık soru Q1.

**Yasak:** T8 gibi doğrulanmamış sayısal hedefler uydurulmaz (ör. '10 ms latency')
— kanıt gelene kadar UNKNOWN kalır.

### §4.1 Kanıt durumuna göre teknoloji kararı matrisi

| Teknoloji | Kanıt | K004 kararı | Not |
|---|---|---|---|
| Patina (analog modeling) | ✅ repo (2026-10-12) | Referans inceleme kaynağı | Lisans okunmadı → ⚠️ |
| Signalsmith basics | ✅ repo (2026-10-12) | Aday yardımcı kütüphane | LICENSE teyidi → Q2 |
| Signalsmith dsp | ✅ repo (2026-10-12) | Aday DSP kütüphanesi | Sürüm bilgisi yok → UNKNOWN |
| DSPark v1 | ⚠️ çelişkili (90+ ↔ 2★) | **Karar yok** — kanıt gelene kadar kullanılmaz | Q1 |
| Harici bağımlılıksız kendi kodumuz | — | Varsayılan yaklaşım | .ai/AGENTS.md §18 #8: framework bağımlılık artışı → kanıtsız bağımlılık alınmaz |

**Kural:** Bu matrisde ✅ olmayan satır, K004 tasarımında teknoloji seçimi olarak
kullanılamaz; yalnız ad olarak anılır. Karar verilen her satır, kanıtıyla birlikte
bu tabloya işlenir ve dogrulama §1'e düşer — sessiz güncelleme yasak.

## §5 Bağımlılık özeti

| Yön | Bağımlılık | Tür | Etiket |
|---|---|---|---|
| K004 → K003-audio | Akış kaynağı ve graph'a ekleme noktası | çalışma zamanı | [TASARIM] |
| K004 → K000-isletim-sistemi | CPU, thread, bellek kaynağı | çalışma zamanı | [TASARIM] |
| K004 → K001-donanım | Nihai çıkış amplifikatöre/hoparlöre iner | fiziksel | [TASARIM] |
| K004 → K005-firmware | DSP donanım içinde çalışıyorsa firmware sınırı | — | [TASARIM] ⚠️ |
| K004 → (geriye K001/K000'e) kod bağımlılığı | — | — | yok (yalnız arayüz) |

A1 dizisi: K000 → K001 → K003 → K004 → K005. K004 **dördüncü katmandır** ve
K003'ün üstünde yaşar (K004 → K003 yönü). Ters yön (K003'ün K004'ü bilmesi)
sınır ihlalidir: K003 yalnızca soyut bir işlem noktası tanır.

## §6 Doğrulama özeti

| Kriter | Tanım | Şu an |
|---|---|---|
| D1 | v2 §2.4 mini-ağaç birebirliği (12 çocuk) | ✅ 2026-10-12 |
| D2 | 12 çocuk klasöründe index.md (8 zorunlu alan) | ✅ 2026-10-12 |
| D3 | master + bagimliliklar + dogrulama dosyaları | ✅ 2026-10-12 |
| D4 | açık ⚠️ / UNKNOWN satırı kalmadı | ❌ T4, T5, T6 ⚠️ + T8 UNKNOWN |
| D5 | açık sorular kapandı | ❌ 5 açık |
| D6 | en az bir [TASARIM] → [KANIT] geçişi | ❌ 0 |
| D7 | status: verified | ❌ D1–D7 ✅ olunca |

**Geçiş kuralı:** D1–D7 tamamı ✅ → index.md status: draft → verified. Kısmi yok.

## §7 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | DSPark '90+' iddiası ne anlama geliyor, doğru depo hangisi? | ⚠️ VERIFY REQUIRED | researcher |
| Q2 | Signalsmith basics lisansı MIT mi (LICENSE teyidi)? | ⚠️ VERIFY REQUIRED | researcher |
| Q3 | dynamics ↔ compressor/limiter hiyerarşisi mi, kardeş mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q4 | measurement ↔ testing sorumluluk ayrımı? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| Q5 | DSP hedefi donanım içinde mi (K005) host'ta mı (K003 üstü)? | ⚠️ VERIFICATION REQUIRED | embedded + dsp-fw |

## §8 Kaynaklar

| Kaynak | İlgili bölüm | Bağlantı |
|---|---|---|
| v2 §2.4 — K004-dsp/ mini-ağaç | §1, §2, §3 | coremusic-katmanli-mimari-agaci-v2.md (G: Drive · okundu 2026-10-12) |
| v2 §3 — adlandırma kuralları | §2, §3 | aynı dosya |
| v2 §4.1–4.3 — kayıt şeması | §6 | aynı dosya |
| ADR-097 K2 — multi-MD şeması | §1, §3 | .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md |
| Patina (C++17 analog modeling) | §4 T1 | https://github.com/ShmKnd/Patina |
| Signalsmith basics | §4 T2 · §7 Q2 | https://github.com/Signalsmith-Audio/basics |
| Signalsmith dsp | §4 T3 | https://github.com/Signalsmith-Audio/dsp |
| DSPark (manifest iddiası — ⚠️) | §4 T5, T6 · §7 Q1 | URL UNKNOWN — kanıt turu-2 |
| Ölçüm eşikleri (SNR/THD+N) | §4 T7 | ~/.config/opencode/AGENTS.md §5 (okundu 2026-10-12) |
| Agent registry — layer violation / framework bağımlılık uyarısı | §4.1, §2.2 | .ai/AGENTS.md §17 #7 · §18 #8 (okundu 2026-10-12) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
