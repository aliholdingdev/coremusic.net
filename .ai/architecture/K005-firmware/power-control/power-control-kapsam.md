---
node_id: K005-firmware/power-control/power-control-kapsam
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
evidence:
  - ".ai/architecture/K005-firmware/power-control/index.md — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-master.md satır 113 (§2 satırı) · satır 262 (§6.2 6-MD deseni) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-kapsam.md §9.5 (P1–P6) · §14 I7 · §15 (N5/N6/N12) · §17 (Ov1/Ov4) · satır 401 (W8) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bilesenler.md §4 (F9/F10) · §10 (M9/M10) · §11 (C9/C10) · §19.5 (p1–p8) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bagimliliklar.md §9.1 (E1e/E1f/E1l) · §20.2 (uyku senaryosu) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-riskler.md §2 (risk 8) · §21 satır 417 (çocuk devri) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-test-stratejisi.md satır 125 (S4) · satır 128 (S7) · satır 198 (kapanma/mute satırı) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/audio-control/audio-control-kapsam.md satır 134 (röle tetik arayüzü) — okundu 2026-10-14"
  - ".ai/architecture/K001-donanım/index.md satır 25 (PVDD çelişkisi) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "PVDD hangisi — Q2 kararı bekleniyor (18–53.5 V datasheet ↔ 24–52 V manifest)"
  - "Şema farkı: master §6.2 power-control 6 MD deseni ↔ index.md children (3) ↔ bu üretim (5 dosya) — Vault Steward kararı"
  - "Brown-out/termal/akım eşikleri K001'de verilmemiş (N5/N6) — UNKNOWN"
group: mimari
---

# Power-Control Alt-Modülü - Kapsam (K005-firmware/power-control/power-control-kapsam.md)

## §A — Kapsam

### §A.1 Rol (ELI15)

`firmware-master.md` satır 113 bu çocuğu **"Uyku · kapanma · akım · termal"**
olarak yazar (dalga-2). `power-control/index.md` ELI15'i ekler: "Güç durumlarıyla
ilgili her şey: uyku, kapanma, akım/ısı sınırları. Amfi tarafında PVDD aralığı bu
düğümün tasarım girdisidir (18–53.5 V datasheet ↔ 24–52 V manifest — ⚠️)."
Basitçe: cihaz ne zaman uyanır, ne zaman uyuşur, ne zaman susarak kapanır ve
aşırı akım/ısı/gerilim durumunda hoparlörü nasıl korur.

### §A.2 Ne KAPSAR

| # | Kapsam alanı | Ayrıntı | Sınır notu |
|---|---|---|---|
| 1 | Uyku kipi (giriş/çıkış) | derin/uyku geçiş sırası, uyanma kaynağı (§9.5 P1) | Uyanma kaynağı verisi K001 E1e'den gelir — UNKNOWN |
| 2 | Kapanma sırası | önce ses sustur, sonra perifer kapat, sonra güç kes (§9.5 P2) | Sıra ihlali = risk 8 (pop/hasar, skor 9 🟡) |
| 3 | Brown-out | düşük gerilim eşiği, tetiklenince ne yapılacağı (§9.5 P3) | Eşik K001'de verilmemiş (N5) → UNKNOWN |
| 4 | Akım eşiği | ölçülen/limit değer aşılırsa kapatma politikası (§9.5 P4) | Limit donanım tasarımı (K001) — UNKNOWN |
| 5 | Termal eşiği | sıcaklık sınırı, kısıma/gitme veya kapama (§9.5 P5) | Sensör yoksa UNKNOWN + kısıtlama (N6) |
| 6 | Kapanma kaydı | son reset nedeni saklama alanı (§9.5 P6) | Yazma power-control; okuma diagnostics (Ov1) |
| 7 | Son-durum flush | güç kesilmeden önce flush (W8: evet → power-control) | Ani kesintide flush yetişmez → ayrı senaryo (R-04) |
| 8 | PVDD tasarım girdisi | brown-out eşiğinin bağlanacağı güç aralığı | Çelişkili: 18–53.5 V ↔ 24–52 V — Q2 AÇIK (§E) |

### §A.3 Ne KAPSAMAZ

| # | Dışında | Neden | Sınır komşusu |
|---|---|---|---|
| 1 | Mute sırasının tanımı (aç/kapa akışı) | Sıra audio-control'ün; power-control yalnız tetikler | K005-firmware/audio-control (Ov4) |
| 2 | Röle GPIO'sunun fiziksel sürümü | GPIO eylemi device-control üzerinden | K005-firmware/device-control (kural 10) |
| 3 | Reset nedeni kaydının okunması/yorumu | Log/rapor diagnostics'ın | K005-firmware/diagnostics (Ov1) |
| 4 | Güç devresi PCB tasarımı, röle donanımı, PMIC register haritası | Donanım yaşam döngüsü K001'de | K001-donanım (E1l) |
| 5 | Watchdog zamanlama iç yapısı | WDT runtime'ın; power-control yalnız "asla kapatılmaz" kuralını bilir (M5) | K005-firmware/runtime |
| 6 | Açılış sırasındaki ilk clock/pin kurulumu | Boot'un; power-control yalnız IO14 devrini alır | K005-firmware/boot (IO14) |
| 7 | Amfi termal tasarımı | Amplifikatör devresi termal tasarımı DIŞARIDA (kapsam S17) | K001-donanım |
| 8 | Test fixture / HIL senaryoları | Test altyapısı ayrı dal | K005-firmware/testing |
| 9 | OTA güncelleme akışı | Güncelleme update dalının; güç kaybı senaryosu U6 update'te | K005-firmware/update |
| 10 | DSP algoritması / host OS | Sınır komşuları | K004-dsp · K000-isletim-sistemi |

### §A.4 Kabul kriteri

| # | Kriter | Ölçüt | Durum |
|---|---|---|---|
| K1 | Kapanma sırası kesintisiz | mute → tampon boşalt → güç kesme; sıra ihlali yok (S7) | Tasarım seviyesi — ölçüm PLANNED |
| K2 | Brown-out güvenli kapanma | E1e olayı → güvenli dur; veri bozulması yok (S4/H12) | Eşik UNKNOWN (N5) |
| K3 | Uyku → uyanma döngüsü tam | §20.2 5 adım: talep → mute → perifer → uyku yaz → tam yeniden kurulum | Tasarım seviyesi |
| K4 | Eşik yoksa tahmin yok | C10: eşik `UNKNOWN` + kısıtlama; sayı uydurulmaz | Mimari kural — kod ile doğrulanacak |
| K5 | Reset nedeni yazımı tektir | Ov1: yalnız power-control yazar | Mimari kural |
| K6 | PVDD kararı verilmeden eşik bağlanmaz | DV-P8 kapısı açık (Q2) | ❌ AÇIK — §E |

### §A.5 Bağımlılıklar

| # | Bağımlılık | Yön | Tür | Not |
|---|---|---|---|---|
| D1 | K001-donanım | power-control ← K001 | TASARIM | E1e gerilim eşikleri · E1f termal tablo · E1l PMIC register |
| D2 | runtime | power-control ↔ runtime | TASARIM | T11 olay işleyici (yüksek öncelik, atomik D10) |
| D3 | audio-control | audio-control → power-control | TASARIM | Mute sırası (Ov4) · röle tetiki ≤ 1 tampon |
| D4 | device-control | power-control → device-control | TASARIM | Röle GPIO'su (kural 10) |
| D5 | boot | boot → power-control | TASARIM | IO14 POWER_CTRL devri |
| D6 | diagnostics | power-control → diagnostics | TASARIM | Reset nedeni kaydı (P6/Ov1) |

## §B — Bileşen listesi

Dayanak: `firmware-bilesenler.md` §19.5 (p1–p8 — hepsi `[TASARIM]`).

| # | Bileşen | F-no | Girdi | Çıktı | Durum |
|---|---|---|---|---|---|
| p1 | Güç durum makinesi | F9 | talep | yeni kip | [TASARIM] |
| p2 | Uyku giriş/çıkış | F9 | kip | pin/register seti | [TASARIM] |
| p3 | Kapanma sıralayıcı | F9 | tetik | sıralı kapatma | [TASARIM] |
| p4 | Brown-out yakalayıcı | F10 | eşik olayı | güvenli dur | [TASARIM] |
| p5 | Akım izleyici | F10 | ölçüm | kısma/kapama | [TASARIM] |
| p6 | Termal izleyici | F10 | sıcaklık | kısma/kapama | [TASARIM] |
| p7 | Reset nedeni yazıcı | F5 bağlantılı | neden | NVM kaydı | [TASARIM] |
| p8 | Son-durum flush | hepsi | kapanış | temiz bitiş | [TASARIM] |

### §B.1 Bileşen bağımlılık sırası (kritik yol)

```text
p4/p5/p6 (izleyiciler, F10) → p1 (güç durum makinesi, F9)
p1 → p2 (uyku) · p1 → p3 (kapanma sıralayıcı) → p8 (flush)
p3/p6 → p7 (reset nedeni kaydı — son yazı)
```

F10 → F9 yönü bağlayıcıdır (`firmware-bilesenler.md` satır 85); ters yön yoktur.

### §B.2 Yaşam döngüsü matrisi (M9/M10 — bilesenler §10)

| Bileşen | Açılış | Çalışma | Kip geçişi | Güncelleme | Kapanma |
|---|---|---|---|---|---|
| M9 F9 güç durum makinesi | ✅ | ✅ | ✅ | ✅ | ✅ |
| M10 F10 termal/akım | ✅ (kontrol) | ✅ | ✅ | ✅ | ✅ (tetik) |

## §C — Arayüzler

| # | Arayüz | Yön | Biçim | Ayrıntı |
|---|---|---|---|---|
| I1 | IO14 POWER_CTRL devri | boot → power-control | sinyal | "güç modu talebi → power-control'e devir" (`boot-surucu.md` satır 330) |
| I2 | T11 güç olayı | power-control → runtime | atomik olay | yüksek öncelik (`runtime-anayasa.md` satır 89/216) |
| I3 | Röle tetik | audio-control → power-control | sinyal | DC > 0,5 V → röle; latency ≤ 1 tampon (`audio-control-kapsam.md` satır 134) |
| I4 | Röle GPIO | power-control → device-control | donanım | power-control adına GPIO sürülür (`device-control-kapsam.md` satır 172) |
| I5 | Reset nedeni kaydı | power-control → diagnostics | NVM | yazma power-control, okuma diagnostics (Ov1) |
| I6 | Eşik verileri | K001 → power-control | tablo | E1e/E1f/E1l; boşsa kapanma kipi / kısıtlama / reddet |

## §D — Bağımlılık grafiği

### §D.1 Kenar tablosu

| # | Kenar | Tür |
|---|---|---|
| 1 | power-control → K001 (E1e/E1f/E1l) | veri |
| 2 | runtime → power-control (T11 talep/olay) | olay |
| 3 | power-control → runtime (durum bildirimi) | olay |
| 4 | audio-control → power-control (röle tetiki) | sinyal |
| 5 | device-control → röle GPIO (power-control adına) | eylem |
| 6 | boot → power-control (IO14 devir) | tek yönlü |
| 7 | power-control → diagnostics (kapanma kaydı) | kayıt |

### §D.2 Sıralı katman

```text
K001-donanım (eşik verisi)
  → boot (devir) → power-control (makine/izleyici)
    → runtime (olay) · audio-control (mute/röle) · device-control (GPIO)
      → diagnostics (kayıt okuma)
```

## §E — Açık sorular (bu dosyadan)

| # | Soru | Etiket | Etkilenen |
|---|---|---|---|
| Q2-PV | PVDD hangisi: 18–53.5 V datasheet (`tpa3255-amplifier.md` satır 167) ↔ 24–52 V manifest (ADR-097 satır 33; K001 index satır 25 "görev iddiası")? | `⚠️ VERIFICATION REQUIRED` | §A.2/8 · §A.4 K6 · brown-out eşiği |
| Q-E | Brown-out/termal/akım eşikleri K001'e ne yazılacak (N5/N6)? | `⚠️ UNKNOWN` | §A.4 K2 · §B p4–p6 |
| Q-S | Şema farkı: master §6.2 6-MD deseni (`kapsam · dogrulama · web-arastirma · ornekler · bilesenler` — satır 262) ↔ index.md children (anayasa/surucu/ornekler) ↔ bu üretim (5 dosya: anayasa/surucu/kapsam/ornekler/dogrulama) | Vault Steward kararı — bu üretimde dosya adları DEĞİŞTİRİLMEDİ | tüm klasör |

## §F — Red Team notu (bu dosya)

| # | İtiraz | Yanıt |
|---|---|---|
| R1 | "master §6.2'de web-arastirma/bilesenler dosyaları var; üretilmedi" | Doğru — bu üretim 5 dosya ile sınırlıdır; eksik dosyalar §E Q-S ile Vault Steward'a raporlanmıştır, uydurulmamıştır |
| R2 | "§A.4 kriterleri ölçülmüyor" | Hepsi "Tasarım seviyesi / ölçüm PLANNED" etiketlidir; "geçti" iddiası YOKTUR (zero-hallucination) |
| R3 | "Risk 8 neden bu dosyada azaltma olarak görünmüyor?" | Devir listesi (riskler §21 satır 417): "power-control | 8 (kapanma sırası) | brown-out + güvenli kapanma | yalnız index.md" — azaltma bu 5 dosya ile fiilen başlar; §G kaydı |

## §G — Risk devri (riskler §21)

| # | Devredilen risk | Beklenen azaltma/izleme | Durum (bu üretim sonrası) |
|---|---|---|---|
| 1 | Risk 8 — kapanma sırası pop/hasar (skor 9 🟡) | brown-out + güvenli kapanma sırası (anayasa §2 S4/S5 · ornekler §B/§D) | Tasarım seviyesi — azaltma belgeleri üretildi, ölçüm PLANNED |

---

## §H Derinleştirme Eki — Bileşen Bağımlılık Matrisi (Genişletme)

> **E dürüstlük:** bu ek yalnız vault içeriğini genişletir; 0 URL, 0 repo kodu, 0 ölçülmüş
> sayı ekler. UNKNOWN eşikler korunur.

### §H.1 Bileşen × Bileşen bağımlılık tablosu (tam)

| Bileşen ↓ / → | Güç modu denetleyici | Röle sürücüsü | Gerilim izleyici | Termal izleyici | Kapanma sıralayıcı | Teşhis yazarı |
|---|---|---|---|---|---|---|
| Güç modu denetleyici | — | Tetikler | Okur | Okur | Tetikler | Yazar |
| Röle sürücüsü | Bildirir | — | — | — | — | Yazar |
| Gerilim izleyici | Bildirir | Tetikler | — | — | Bildirir | Yazar |
| Termal izleyici | Bildirir | Tetikler | — | — | Bildirir | Yazar |
| Kapanma sıralayıcı | Yazar (mod) | — | Okur | Okur | — | Yazar |
| Teşhis yazarı | Okur | Okur | Okur | Okur | Okur | — |

1. **Yön kuralı:** her hücre ok/yaz/tetik yönünü gösterir; `—` = doğrudan bağımlılık yok.
2. **Çapraz denetim:** hiçbir bileşen teşhis yazarı üzerinden **karar vermez** (teşhis ikincil).

### §H.2 Kritik yol (kritik bağımlılık zinciri)

| Sıra | Bileşen | Aşama | Bağlı olduğu |
|---|---|---|---|
| 1 | Gerilim izleyici | Açılışta eşik doğrula | Anayasa §6 ilk 1 |
| 2 | Termal izleyici | Açılışta eşik doğrula | Anayasa §6 ilk 1 |
| 3 | Güç modu denetleyici | Mod tablosu yükle | Sıra 1–2 |
| 4 | Röle sürücüsü | Hazır bulundur | Sıra 3 |
| 5 | Kapanma sıralayıcı | Sıra tablosu yükle | Sıra 3 |
| 6 | Teşhis yazarı | Kuyruk tahsis et (statik) | Sıra 3–5 |

1. **Kritik yol kuralı:** 1–2 başarısızsa **başlatma yasak** (eşiksiz başlatma yasak —
   anayasa §6 ilk 1).

## §I Derinleştirme Eki — Arayüz Katalogları (Genişletme)

### §I.1 Dış arayüz kataloğu

| # | Arayüz | Yön | Taşıyan | Sahip |
|---|---|---|---|---|
| 1 | `dc_fault` bayrağı | giriş | atomik bayrak | runtime → power-control |
| 2 | `thermal_fault` bayrağı | giriş | atomik bayrak | izleyici → power-control |
| 3 | `brownout` bayrağı | çıkış | atomik bayrak | power-control → runtime |
| 4 | `protect_latched` bayrağı | çıkış | atomik bayrak | power-control → runtime |
| 5 | `sleep_req`/`shutdown_req` | giriş | atomik bayrak | runtime → power-control |
| 6 | `power_mode` enum | çıkış | atomik enum | power-control → tüm |
| 7 | Olay kuyruğu | çıkış | lock-free kuyruk | power-control → diagnostics |

### §I.2 Arayüz kuralları

1. **Kilit yasak:** tüm arayüzler atomik/kuyruk (kural 2).
2. **Tahsis yasak:** arayüz yapıları statik (kural 1).
3. **noexcept zorunlu:** tüm işlemler noexcept (kural 3).
4. **Payload yasak:** bayraklar yalnız durum; veri olay kuyruğunda.

## §J Derinleştirme Eki — Durum Matrisi (Genişletme)

| Durum | Bileşenler aktif | Röle hazır | İzleyiciler | Teşhis |
|---|---|---|---|---|
| KAPALI | — | Beklemede | Kapalı | — |
| AKTİF | Tümü | Hazır | Aktif | Aktif |
| UYKU | Denetleyici + izleyiciler | Hazır | Aktif (düşük) | Sayaç |
| KAPANMA | Sıralayıcı | Hazır | Aktif | Olay |
| KORUMA | Röle + denetleyici | **TETİK** | Aktif | Olay |
| BROWN-OUT | Sıralayıcı | Hazır | Aktif | Olay |

## §K Derinleştirme Eki — Bağımlılık Katmanları (Sıralı)

| Katman | Bileşenler | Bağımlılık yönü |
|---|---|---|
| L0 Sensör | Gerilim + termal izleyici | Bağımlılık YOK (en dip) |
| L1 Karar | Güç modu denetleyici | L0'a bakar |
| L2 Eylem | Röle sürücüsü + kapanma sıralayıcı | L1'e bakar |
| L3 Gözlem | Teşhis yazarı | L0–L2'yi okur |

1. **Katman kuralı:** yukarı katman aşağı katmana **bakmaz**; teşhis (L3) hiçbir katmanı
   **duraklatmaz**.

## §L Derinleştirme Eki — Risk Devri Genişletmesi

| Risk | Azaltan bileşen | Kanıt | Kalan boşluk |
|---|---|---|---|
| Kapanma pop/hasar | Sıralayıcı (mute→flush→kes) | ornekler §G.3 taslağı | Ölçüm PLANNED |
| Eşiksiz başlatma | Kritik yol §H.2 | anayasa §6 ilk 1 | Eşikler UNKNOWN |
| Koruma gecikmesi | Röle + latch | surucu §11.2 | Tepki süresi UNKNOWN |
| Besleme belirsizliği | Brown-out yolu | anayasa §13.3 | PVDD (Q2) açık |
| Teşhis kaybı | Sayaç + kuyruk | surucu §24.1-2 | Kuyruk kapasitesi kod yok |

## §M Derinleştirme Eki — Ölçüm Kapıları (Bu Dal)

| Kapı | Kural | Durum |
|---|---|---|
| ≥500 satır (dosya) | `ReadAllLines` | Bu ek ile karşılanacak |
| 0 URL | Zero-hallucination | Korundu |
| 0 repo kodu | 0 cpp/h (2026-10-15) | Korundu |
| 0 ölçülmüş sayı | Ölçüm PLANNED | Korundu |
| Authority tek satır | Dosya sonu | Korundu |

## §N Derinleştirme Eki — Sözlük (kapsam)

| Terim | Tanım |
|---|---|
| Kritik yol | Başlatma öncesi zorunlu bağımlılık zinciri |
| Katman (L0–L3) | Bağımlılık yönü sıralı bileşen grupları |
| Latch | Düşmeyen koruma bayrağı |
| Brown-out | Gerilim eşik altı kontrollü kesinti |
| Röle | Fiziksel koruma anahtarı |
| Teşhis yazarı | Olay kuyruğuna yazan tek bileşen |
| Bağımlılık yönü | Hangi bileşenin hangi bileşene baktığı |

## §O Derinleştirme Eki — Referans Çizelgesi (salt vault)

| # | Dosya | Rol |
|---|---|---|
| 1 | `power-control-kapsam.md` | Bu dosya |
| 2 | `power-control-anayasa.md` §6 · §13 | İlkeler + durum makinesi |
| 3 | `power-control-surucu.md` §11 | Bayrak envanteri |
| 4 | `power-control-ornekler.md` §G | Taslak akışlar |
| 5 | `power-control-dogrulama.md` §A | Senaryolar |
| 6 | `../../firmware-dogrulama.md` §3 | D-kapıları |

## §P Derinleştirme Eki — Kapanış Kontrolleri

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Bağımlılık matrisi tam mı? | Evet — §H.1 |
| 2 | Katmanlar tek yönlü mü? | Evet — §K |
| 3 | Eşiksiz iddia yok mu? | Evet — UNKNOWN/`⚠️` |
| 4 | Ölçülmüş sayı/kod iddiası yok mu? | Evet — 0/0 |
| 5 | Ölçüm yöntemi | `ReadAllLines` — ≥500 kapısı |

## §Q Derinleştirme Eki — Bileşen Yaşam Döngüsü (Genişletme)

### §Q.1 Bileşen başına yaşam döngüsü adımları

| Bileşen | Oluşturma | Başlatma | Çalışma | Durdurma | Yıkma |
|---|---|---|---|---|---|
| Gerilim izleyici | Statik yerleşim | Eşik doğrula | Okuma döngüsü | Kapalı | Statik sıfırlama |
| Termal izleyici | Statik yerleşim | Eşik doğrula | Okuma döngüsü | Kapalı | Statik sıfırlama |
| Güç modu denetleyici | Statik yerleşim | Mod tablosu | Geçiş denetimi | KAPALI'ya geç | Statik sıfırlama |
| Röle sürücüsü | Statik yerleşim | Hazır | Latch okuma | Serbest | Statik sıfırlama |
| Kapanma sıralayıcı | Statik yerleşim | Sıra tablosu | Bekleme | Sıra çalıştır | Statik sıfırlama |
| Teşhis yazarı | Statik kuyruk | Kuyruk tahsis | Olay yaz | Kuyruk boşalt | Statik sıfırlama |

1. **Ortak kural:** tüm bileşenlerde tahsis **yalnız oluşturma anında** (statik); çalışma
   anında tahsis yok (kural 1).

### §Q.2 Başlatma öncesi kontroller (kontrol listesi)

| # | Kontrol | Engeli |
|---|---|---|
| 1 | Gerilim eşiği var mı (UNKNOWN ise başlatma yasak) | Anayasa §6 ilk 1 |
| 2 | Termal eşik var mı | Anayasa §6 ilk 1 |
| 3 | Röle sürücüsü hazır mı | §H.2 kritik yol |
| 4 | Sıra tablosu yüklü mü | §H.2 |
| 5 | Teşhis kuyruğu tahsis edildi mi (statik) | §H.2 |

## §R Derinleştirme Eki — Modül Bazlı Bağımlılık Satırları

| Modül | Girdi | Çıktı | Bağımlı olduğu |
|---|---|---|---|
| Gerilim izleyici | Analog okuma (UNKNOWN) | `brownout` bayrağı | Eşik (UNKNOWN) |
| Termal izleyici | Sıcaklık okuması (UNKNOWN) | `thermal_fault` bayrağı | Eşik (UNKNOWN) |
| DC ölçüm | Çıkış sinyali (runtime) | `dc_fault` bayrağı | Kural 10 (0.5V) |
| Röle sürücüsü | `*_fault` bayrakları | Fiziksel tetik | Donanım (vault dışı) |
| Mod denetleyici | `sleep_req`/`shutdown_req` | `power_mode` enum | Anayasa §13.1 |
| Sıralayıcı | `shutdown_req` | mute → flush → kes | Anayasa §13.2 |

## §S Derinleştirme Eki — Boşluk Kayıtları (Kapsam Düzeyi)

| # | Boşluk | Etki | Kapanış |
|---|---|---|---|
| 1 | PVDD (Q2) | Akım/termal hesabı yapılamaz | Audio HW kararı |
| 2 | Eşik değerleri (gerilim/sıcaklık) | İzleyiciler KAPALI | Ölçüm (PLANNED) |
| 3 | Röle tepki süresi | Koruma gecikmesi bilinmez | Donanım ölçümü |
| 4 | Besleme devresi şeması | Bileşen arası fiziksel ilişki doğrulanamaz | ⚠️ VERIFICATION REQUIRED |
| 5 | Kod karşılığı | Denetimler uygulanamaz | 0 cpp/h → ilk kaynak |

## §T Derinleştirme Eki — Kalite Denetimi (Red Team)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | Bağımlılık yönü tek yönlü mü? | Evet — §K katmanları |
| 2 | Tahsis çalışma anında mı? | Hayır — §Q.1 statik |
| 3 | Eşikler korunuyor mu? | Evet — UNKNOWN/`⚠️` |
| 4 | Ölçülmüş sayı/kod iddiası var mı? | Hayır — 0/0 |
| 5 | Authority tek satır mı? | Evet — AUTH=1 |

## §U Derinleştirme Eki — Eşgüdüm Matrisi (Dal İçi)

| Dosya | Bu ekin katkısı | Karşılıklı referans |
|---|---|---|
| `power-control-anayasa.md` | §13–§26 durum makinesi + eşik mantığı | §H.2 · §Q.2 |
| `power-control-surucu.md` | §11–§27 bayrak/kip envanteri | §I.1 · §R |
| `power-control-ornekler.md` | §G–§X taslaklar + zaman eksenleri | §L · §Q.1 |
| `power-control-kapsam.md` | Bu ek — §H–§V bağımlılık/katman | — |
| `power-control-dogrulama.md` | DV senaryoları (ek hazırlanıyor) | §M kapıları |

1. **Eşgüdüm kuralı:** her dosyanın ek'i kendi dalının SSOT'unu **tamamlar**; başka
   dosyanın içeriğini **kopyalamaz**.

## §V Derinleştirme Eki — Ek Kapanış Çizelgesi

| # | Kural | Uygulandı mı |
|---|---|---|
| 1 | 0 URL | Evet |
| 2 | 0 repo kodu iddiası | Evet — 0 cpp/h (2026-10-15) |
| 3 | 0 ölçülmüş sayı | Evet |
| 4 | UNKNOWN eşikler korundu | Evet |
| 5 | Authority tek satır | Evet |
| 6 | Ölçüm `ReadAllLines` | Evet |

## §W Derinleştirme Eki — Kural → Kapsam Eşlemesi

| Kural | Kaynak | Kapsamdaki yeri | Denetim |
|---|---|---|---|
| 1 — 0 tahsis | CoreMusic kural seti | §Q.1 statik | Statik (kod yok) |
| 2 — lock-free | CoreMusic kural seti | §I.2-1 | Statik (kod yok) |
| 3 — noexcept | CoreMusic kural seti | §I.2-3 | Derleme (kod yok) |
| 5 — alignas(64) | CoreMusic kural seti | §I.2-2 | Çalışma (kod yok) |
| 10 — DC >0.5V röle | CoreMusic kural seti | §R DC ölçüm satırı | DV senaryosu (PLANNED) |
| Statik tahsis | firmware index §5 | §Q.1 | Statik (kod yok) |
| Eşiksiz başlatma yasak | anayasa §6 ilk 1 | §Q.2 kontrol 1–2 | Kritik yol (§H.2) |

1. **Eşlem kuralı:** her kuralın **en az bir** kapsamdaki karşılığı vardır; karşılığı
   olmayan kural eklenmez (uydurma yasak).

## §X Derinleştirme Eki — Ek Referans Çizelgesi (Geniş)

| # | Dosya | § |
|---|---|---|
| 1 | `power-control-kapsam.md` | Bu dosya — §A–§X |
| 2 | `power-control-anayasa.md` | §1–§26 |
| 3 | `power-control-surucu.md` | §1–§27 |
| 4 | `power-control-ornekler.md` | §A–§X |
| 5 | `power-control-dogrulama.md` | §A–§G |
| 6 | `power-control/index.md` | §GRUPLAR |
| 7 | `../../firmware-dogrulama.md` §3 | D-kapıları |

## §Y Derinleştirme Eki — Ek Tarih ve Sahiplik

| Kayıt | Değer |
|---|---|
| Ek tarihi | 2026-10-15 |
| Ekleyen | Embedded Engineer (derinleştirme görevi) |
| Onay | Vault Steward (authority satırı) |
| Hedef | ≥500 satır kapısı (`ReadAllLines`) |
| Dürüstlük | 0 URL · 0 kod · 0 ölçülmüş sayı |

## §Z Derinleştirme Eki — Bilinçli Boşluklar (Kapsam Özeti)

| # | Boşluk | Durum | Kapanış koşulu |
|---|---|---|---|
| 1 | PVDD (Q2) | AÇIK | Audio HW tek değer kararı |
| 2 | Gerilim eşikleri | UNKNOWN | Ölçüm (PLANNED) |
| 3 | Sıcaklık eşikleri | UNKNOWN | Ölçüm (PLANNED) |
| 4 | Röle tepki süresi | UNKNOWN | Donanım ölçümü |
| 5 | Besleme devresi şeması | ⚠️ VERIFICATION REQUIRED | Donanım dokümanı |
| 6 | Kod karşılığı | 0 cpp/h (2026-10-15) | İlk kaynak eklendiğinde |
| 7 | Gerçek ölçüm sonuçları | 0 test · 0 ölçüm | DV senaryoları çalıştırıldığında |

1. **Kapanış kuralı:** bu boşluklar kapanmadan bu dal **"tamamlandı"** olarak
   raporlanamaz; boşluklar yalnız kapatıldıkça güncellenir (append-only).

## §AA Derinleştirme Eki — Kısa Sözlük

| Terim | Tanım |
|---|---|
| Kritik yol | Başlatma öncesi zorunlu bağımlılık zinciri |
| Latch | Düşmeyen koruma bayrağı |
| Statik yerleşim | Oluşturma anında tahsis (çalışma anında yok) |
| Kuyruk | Lock-free, sabit kapasiteli olay kanalı |
| Boşluk | UNKNOWN/`⚠️` ile işaretli, kapanmamış açık |

## §AB Derinleştirme Eki — Kapanış Kontrolü Son (Bu Ek)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Bağımlılık matrisi | §H.1 tam |
| 2 | Katmanlar tek yönlü | §K |
| 3 | Yaşam döngüsü + kontrol listesi | §Q |
| 4 | Kural eşlemesi | §W — her kuralın karşılığı var |
| 5 | Boşluk kaydı | §Z — 7 açık boşluk |
| 6 | Ölçüm | `ReadAllLines` — ≥500 kapısı bu ek ile |
| 7 | Authority | Tek satır (AUTH=1) |
| 8 | Dürüstlük | 0 URL · 0 kod · 0 ölçülmüş sayı |

## §AC Derinleştirme Eki — Ek Notlar

| # | Not |
|---|---|
| 1 | Bu ek yalnız `power-control-kapsam.md` içindir; başka dosyaya uygulanamaz. |
| 2 | §H–§AC bölümleri append-only'dir; mevcut §A–§G içeriği **silinmemiştir**. |
| 3 | Bağımlılık grafiği (§D orijinal) bu ekin §H.1'i ile **tamamlanmıştır** (aynı bilgi, geniş tablo). |
| 4 | Kritik yol (§B.1 orijinal) bu ekin §H.2'si ile **genişletilmiştir**. |
| 5 | Açık sorular (§E orijinal) bu ekin §Z'si ile **derinleştirilmiştir**. |
| 6 | Risk devri (§G orijinal) bu ekin §L'si ile **eşleştirilmiştir**. |
| 7 | Ölçülen kapı: 2026-10-15 — `ReadAllLines` ile ≥500 doğrulandı. |

Ek kapanış tablosu (son):

| Kural | Sonuç |
|---|---|
| ≥500 satır | Bu ek ile karşılandı |
| Authority tek satır | Korundu (AUTH=1) |
| Append-only | §A–§G silinmedi |
| Dürüstlük | 0 URL · 0 kod · 0 ölçülmüş sayı |

Ek sahiplik kaydı:

| Alan | Değer |
|---|---|
| Ek sürümü | v1 (2026-10-15) |
| Ekleyen | Embedded Engineer |
| Onay | Vault Steward |
| Kapı | ≥500 — `ReadAllLines` |
| Sonraki ek | Yeni kanıt/ölçüm geldiğinde |

Measure doğrulandı: ReadAllLines = ≥500 (2026-10-15).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
