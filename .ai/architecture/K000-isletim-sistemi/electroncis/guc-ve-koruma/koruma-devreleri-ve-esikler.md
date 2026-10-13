---
id: electroncis/guc-ve-koruma/koruma-devreleri-ve-esikler
title: "Koruma Devreleri ve Eşikler"
node_type: module
parent_id: electroncis/guc-ve-koruma
level: 4
group: kod
status: draft
updated: 2026-10-13
---

# koruma-devreleri-ve-esikler

> `electroncis/guc-ve-koruma` alt-kapsamının koruma modülü: aşırı akım, aşırı gerilim/AKŞ (crowbar), termal kapanma, ESD ve eşik/tolerans hesabı. Scope Q1 (`⚠️ VERIFICATION REQUIRED`) geçerli; koruma eşikleri (ör. DC offset >0.5 V röle) kural, geri kalanı `⚠️ VERIFICATION REQUIRED`. Repo'da devre seviyesi kod YOK — kaynak: .ai/electronic/** (60 dosya).

## §0 · Kapsam

| Konu | Var | Komşu |
|---|---|---|
| Aşırı akım | evet | — |
| Aşırı gerilim / AKŞ | evet | — |
| Termal kapanma | evet | K001 |
| ESD | evet | — |
| Eşik / tolerans hesabı | evet | — |
| Topoloji/verim | sınır | guc-kaynaklari |

## §1 · Aşırı Akım Koruma

| Yöntem | Çalışma | Hız | Eksi |
|---|---|---|---|
| Sigorta (eriyen) | akım → erir | orta | tek-use |
| PTC (resettable) | ısın → direnç ↑ | yavaş | soğuma |
| eFuse | elektronik limit | hızlı | maliyet |
| Akım-sınırlı regülatör | limit | hızlı | ısı |
| Hot-swap kontrolörü | sıralı takma | hızlı | karmaşık |

## §1b · EFuse / Akım-Limit Parametreleri

| Parametre | Not | Durum |
|---|---|---|
| Akım limit | kesme eşiği | `⚠️ VERIFICATION REQUIRED` |
| Vblank (UVLO) | düşük-gerilim kapanma | `⚠️ VERIFICATION REQUIRED` |
| Slew (soft-start) | rampa | `⚠️ VERIFICATION REQUIRED` |
| Fault bayrağı | hata çıktısı | `⚠️ VERIFICATION REQUIRED` |
| Termal | dahili OTP | `⚠️ VERIFICATION REQUIRED` |

## §2 · Aşırı Gerilim / AKŞ (Crowbar)

| Eleman | İş | Not |
|---|---|---|
| TVS | tepe-bastırma | hızlı, tekrarlı |
| Zener | eşik | hassasiyet |
| SCR (crowbar) | kısa-devre kesme | AKŞ |
| MOV | surge enerji | arrester |
| Sensör (comparator) | eşik algılama | hız |

## §2b · AKŞ (Crowbar) Çalışma Sırası

| Adım | Olay | Sonuç |
|---|---|---|
| 1 | Gerilim > eşik | sensör tetik |
| 2 | SCR tetik | kısa-devre |
| 3 | Sigorta akım | sigorta erir |
| 4 | Güç kesil | yük korunur |

## §3 · Termal Kapanma (OTP)

| Yöntem | Sensör | Eşik |
|---|---|---|
| Dahili OTP | die sıcaklık | Tjmax |
| Termistör (NTC/PTC) | dış sıcaklık | ayar |
| Termal röle | mekanik | — |
| Firmware OTP | K005 | yazılım |

## §3b · Termal-Eşik Mantığı

| Büyüklük | İlişki | Durum |
|---|---|---|
| Tjmax | paket limit | `⚠️ VERIFICATION REQUIRED` |
| OTP eşiği | Tjmax − margin | `⚠️ VERIFICATION REQUIRED` |
| Hysteresis | yeniden-açılma | `⚠️ VERIFICATION REQUIRED` |
| θJA | °C/W | `⚠️ VERIFICATION REQUIRED` |

## §4 · ESD Koruma

| Seviye (HBM/kategori) | Deşarj | Koruma |
|---|---|---|
| İnsan-modeli | ±kV | TVS/array |
| Machines | ±V | TVS |
| IEC 61000-4-2 | ±kV | ESD array |

## §4b · ESD-Parametre Matrisi

| Parametre | Not | Durum |
|---|---|---|
| Eşik (±kV) | test seviyesi | `⚠️ VERIFICATION REQUIRED` |
| TVS kapasitör | düşük-kap (sinyal) | `⚠️ VERIFICATION REQUIRED` |
| Düzlem (layout) | akım yolu | `⚠️ VERIFICATION REQUIRED` |
| Topraklama | kısa yol | `⚠️ VERIFICATION REQUIRED` |

## §5 · Eşik / Tolerans Hesabı

| Eşik | Formül / Mantık | Durum |
|---|---|---|
| DC offset röle | |DC| > 0.5 V | kural |
| Akım limit | limit = yük×margin | `⚠️ VERIFICATION REQUIRED` |
| Gerilim sınır | Vrwm ≥ rail | `⚠️ VERIFICATION REQUIRED` |
| OTP | Tjmax − margin | `⚠️ VERIFICATION REQUIRED` |
| Tolerans | ±tol × nominal | `⚠️ VERIFICATION REQUIRED` |

## §5b · Tolerans / Worst-Case

| Kalem | Tolerans | Worst-case |
|---|---|---|
| Direnç | ±%1–5 | uç |
| Kapasitör | ±%10–20 | uç |
| Referans | ±%1 | uç |
| Eşik sapması | offset | uç |
| Sıcaklık | drift | uç |

## §5c · Eşik-Güvenilirlik (Hysteresis / Debounce)

| Teknik | Neden |
|---|---|
| Hysteresis | yanlış tetikleme önleme |
| Debounce (zaman) | gürültü-bloklama |
| Yeniden-açılma gecikme | kararlılık |

## §1c · Aşırı-Akım Senaryo Matrisi

| Senaryo | Algılama | Aksiyon | Hız |
|---|---|---|---|
| Çıkış kısa devre | akım ↑ | eFuse/sigorta | hızlı |
| Yük aşımı | akım > limit | limit | hızlı |
| İç arıza | akım ↑ | kesme | hızlı |
| Yumuşak kısa (ısınma) | ısınma | PTC/OTP | yavaş |

## §1d · EFuse vs Sigorta Karşılaştırma

| Kriter | eFuse | Sigorta |
|---|---|---|
| Tekrar | evet | hayır (eriyen) |
| Hız | hızlı | orta |
| Precision | yüksek | düşük |
| Maliyet | yüksek | düşük |
| Fault sinyali | var | yok |

## §1e · Hot-Swap Sırası

| Adım | Olay |
|---|---|
| 1 | Takma (hot) |
| 2 | Pre-charge (sınır) |
| 3 | Rail stabil |
| 4 | Tam bağlantı |
| 5 | Fault izleme |

## §2c · TVS-Parametre Matrisi

| Parametre | Not | Durum |
|---|---|---|
| Vrwm | çalışma | `⚠️ VERIFICATION REQUIRED` |
| Vbr | kırılma | `⚠️ VERIFICATION REQUIRED` |
| Vc | klamlama | `⚠️ VERIFICATION REQUIRED` |
| Ipp | tepe akım | `⚠️ VERIFICATION REQUIRED` |
| Kapasitör | sinyal | `⚠️ VERIFICATION REQUIRED` |

## §2d · Crowbar vs TVS Karşılaştırma

| Kriter | TVS | Crowbar (AKŞ) |
|---|---|---|
| Enerji | düşük-tepe | yüksek |
| Tekrar | evet | sigorta |
| Hız | çok hızlı | hızlı |
| Karmaşıklık | basit | orta |

## §2e · Eşik-Sapma (Comparator) Matrisi

| Parametre | Not | Durum |
|---|---|---|
| Offset | sapma | `⚠️ VERIFICATION REQUIRED` |
| Hysteresis | yanlış tetik | `⚠️ VERIFICATION REQUIRED` |
| Hız | prop. gecikme | `⚠️ VERIFICATION REQUIRED` |
| Referans | eşik | `⚠️ VERIFICATION REQUIRED` |

## §3c · Termal-Eşik-Karşılaştırma

| Yöntem | Tolerans | Hız | Durum |
|---|---|---|---|
| Dahili OTP | orta | orta | `⚠️ VERIFICATION REQUIRED` |
| NTC + comparator | ayarlanır | yavaş-orta | `⚠️ VERIFICATION REQUIRED` |
| Termal röle | düşük | yavaş | — |
| Firmware OTP | — | yavaş | K005 |

## §3d · Termistör-Eşik Mantığı

| Büyüklük | İlişki |
|---|---|
| R(T) | Steinhart-Hart |
| β | sabit |
| Eşik R | karşılık sıcaklık |
| Hysteresis | yeniden-açılma |

## §4c · ESD-Test-Kategori Matrisi

| Kategori | Seviye | Durum |
|---|---|---|
| HBM (insan) | ±kV | `⚠️ VERIFICATION REQUIRED` |
| Machine model | ±V | `⚠️ VERIFICATION REQUIRED` |
| IEC 61000-4-2 | ±kV | atıf `⚠️ VERIFICATION REQUIRED` |
| Charged-device | ±V | `⚠️ VERIFICATION REQUIRED` |

## §4d · ESD-Koruma-Yerleşimi

| Kural | Neden |
|---|---|
| Giriş yakını | akım yolu kısa |
| Düzlem-entegre | akım toprağa |
| Düşük-kap TVS | sinyal bozulmaz |
| Paralel bağlama | deşarj yolu |

## §5d · Eşik-Hesap Örnek Matrisi (Kavramsal, Ölçülmemiş)

| Eşik | Nominal | Margin | Worst-case |
|---|---|---|---|
| DC offset | 0.5 V | — | kural |
| Akım | UNKNOWN | ×1.5 | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | rail | +margin | `⚠️ VERIFICATION REQUIRED` |
| OTP | Tjmax | −margin | `⚠️ VERIFICATION REQUIRED` |

## §5e · Tolerans-Zincir (Worst-Case Toplam)

| Kalem | Sapma | Toplam |
|---|---|---|
| Direnç | ±%x | — |
| Kapasitör | ±%y | — |
| Referans | ±%z | — |
| Sıcaklık | drift | — |
| Toplam | — | worst-case |

## §5f · Eşik-Güvenilirlik — Hysteresis Örnek

| Durum | Tetik | Sonuç |
|---|---|---|
| Yükselen | > üst | kes |
| Düşen | < alt | geri |
| Arada | — | bekle |

## §1f · Eşik-Tipi Koruma Özet

| Koruma | Eşik | Hız |
|---|---|---|
| Akım | limit | hızlı |
| Gerilim | Vrwm | hızlı |
| Isı | OTP | orta |
| ESD | ±kV | çok hızlı |
| DC offset | 0.5 V | ms |

## §2f · AKŞ-Gerilim-Eşik Mantığı

| Büyüklük | İlişki |
|---|---|
| Eşik (V) | rail + margin |
| TVS Vrwm | ≥ rail |
| Vc | ≤ yük limit |
| Tolerans | uç |

## §7b · IEEE/IEC-Atıf Matrisi (Devam)

| Standart | Konu | Durum |
|---|---|---|
| IEEE 61000-4-2 | ESD | atıf `⚠️ VERIFICATION REQUIRED` |
| IEEE 1547 | dağıtım-arayüz | atıf `⚠️ VERIFICATION REQUIRED` |
| IEC 2724 | (yayın) | atıf `⚠️ VERIFICATION REQUIRED` |

## §1g · Sigorta / PTC-Parametre

| Parametre | Sigorta | PTC |
|---|---|---|
| Akım (Ih) | erime | tutma |
| Vmax | limit | limit |
| Reset | hayır | evet |
| Soğuma | — | zaman |

## §1h · Akım-Limit Hesap Mantığı

| Büyüklük | İlişki | Durum |
|---|---|---|
| Limit | Iload×margin | `⚠️ VERIFICATION REQUIRED` |
| Trip süre | akım²×t | `⚠️ VERIFICATION REQUIRED` |
| let-through | I²t | `⚠️ VERIFICATION REQUIRED` |

## §3e · Termal-Monitor (TMP461 benzeri) Mantığı

| Parametre | Not | Durum |
|---|---|---|
| Uzak diode | die/NTC | `⚠️ VERIFICATION REQUIRED` |
| Alert | uyarı | `⚠️ VERIFICATION REQUIRED` |
| T_HYST | histeresiz | `⚠️ VERIFICATION REQUIRED` |
| Ölçüm hassasiyet | ±°C | `⚠️ VERIFICATION REQUIRED` |

## §3f · Termal-Eşik-Sıra

| Adım | Olay |
|---|---|
| 1 | Sıcaklık ölç |
| 2 | > OTP eşiği kontrol |
| 3 | Hysteresis bekle |
| 4 | Kapanma |
| 5 | Soğuma → reopen |

## §4e · ESD-Akım-Yolu Matrisi

| Yol | Etki |
|---|---|
| Giriş → TVS → GND | deşarj |
| Düzlem akım | kısa |
| Toprak düzlemi | dağılma |
| İzole (opto) | ek |

## §4f · ESD-Koruma-Eleman Karşılaştırma

| Eleman | Kapasitör | Hız | Kullanım |
|---|---|---|---|
| TVS array | düşük | çok hızlı | sinyal |
| Tek TVS | orta | hızlı | güç |
| Varistor (MOV) | yüksek | yavaş | surge |
| Gas deşarj | yüksek | yavaş | yüksek-enerji |

## §5g · Tolerans-Kabul Matrisi

| Kalem | Kabul | Durum |
|---|---|---|
| Eşik sapması | ±tol | `⚠️ VERIFICATION REQUIRED` |
| Sıcaklık drift | ±ppm/°C | `⚠️ VERIFICATION REQUIRED` |
| Zaman-drift | — | `⚠️ VERIFICATION REQUIRED` |
| Worst-case | toplam | `⚠️ VERIFICATION REQUIRED` |

## §5h · DC-Offset Eşik-Güvenilirlik

| Faktör | Etki | Not |
|---|---|---|
| Ölçüm penceresi | tespit | blok/ornek |
| Eşik (0.5 V) | kural | — |
| Hysteresis | yanlış alarm | — |
| Gecikme | hoparlör koruma | ms |

## §5i · Eşik-Gereksinim Matrisi (Ölçülmemiş)

| Koruma | Eşik | Durum |
|---|---|---|
| Akım | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Isı | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| ESD | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| Surge | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| DC offset | 0.5 V | kural |

## §8b · Açık Sorular — Devam

| Soru | Durum |
|---|---|
| eFuse entegratör seçimi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| TVS marka/model | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Termal sensör yerleşimi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Röle kontak akımı | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| ESD yerleşimi (PCB) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §1i · Eşik-Akım-Güvenilirlik

| Faktör | Etki |
|---|---|
| let-through (I²t) | enerji |
| Trip süre | koruma |
| Soğuma | yeniden |

## §2g · Gerilim-Eşik-Güvenilirlik

| Faktör | Etki |
|---|---|
| Tolerans | sapma |
| Sıcaklık | drift |
| Vc (klamlama) | koruma |

## §3g · Termal-Güvenilirlik

| Faktör | Etki |
|---|---|
| Hysteresis | kararlılık |
| θJA | ΔT |
| Yerleşim | ölçüm |

## §4g · ESD-Güvenilirlik

| Faktör | Etki |
|---|---|
| Kapasitör | sinyal |
| Akım yolu | deşarj |
| Toprak | dağılma |

## §5j · Tolerans-Worst-Case-Özet

| Kalem | Uç-değer |
|---|---|
| Direnç | ±%x |
| Kapasitör | ±%y |
| Sıcaklık | drift |
| Toplam | worst |

## §1j · Koruma-Hız-Zinciri

| Koruma | Hız |
|---|---|
| ESD | çok hızlı |
| Akım | hızlı |
| Gerilim | hızlı |
| Isı | orta |
| DC röle | ms |

## §7c · Açık Sorular — ESD/Surge Devam

| Soru | Durum |
|---|---|
| ESD deşarj tipi (HBM/CDM) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Surge dalgıç formu | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Koruma katman sırası | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §2h · AKŞ-Çalışma-Parametre

| Parametre | Not |
|---|---|
| SCR akım | Ift |
| Trigger | eşik |
| Sigorta | erime |

## §3h · OTP-Kabul

| Parametre | Kabul |
|---|---|
| Tj | ≤ Tjmax |
| Hysteresis | uygulanır |
| Alert | var |

## §5k · Eşik-Tolerans-Özet

| Eşik | Tolerans |
|---|---|
| Akım | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | `⚠️ VERIFICATION REQUIRED` |
| Isı | `⚠️ VERIFICATION REQUIRED` |

## §0b · Kapsam Özet

| Var | YOK |
|---|---|
| Akım/gerilim/ısı/ESD | Topoloji (diğer modül) |
| Eşik/tolerans hesabı | Soğutma (K001) |
| AKŞ/TVS | Firmware (K005) |

## §5l · Eşik-Domain Özet

| Domain | Eşik | Durum |
|---|---|---|
| Akım | limit | `⚠️ VERIFICATION REQUIRED` |
| Gerilim | Vrwm | `⚠️ VERIFICATION REQUIRED` |
| Isı | Tjmax | `⚠️ VERIFICATION REQUIRED` |

## §1k · Akım-Eşik-Özet

| Eşik | Durum |
|---|---|
| Limit | `⚠️ VERIFICATION REQUIRED` |
| let-through | `⚠️ VERIFICATION REQUIRED` |

## §6 · Bilgi İddia Matrisi (Web Araştırma · 2026-10-13)

> Gerçek, envanter-dışı, tek tek HTTP 200 doğrulanmış URL'ler. Özellik/atıf `⚠️ VERIFICATION REQUIRED`.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| TPS25982 eFuse datasheet | https://www.ti.com/lit/ds/symlink/tps25982.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| LM5064 hot-swap / güç-izleme datasheet | https://www.ti.com/lit/ds/symlink/lm5064.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| TMP461 termal sensör datasheet | https://www.ti.com/lit/ds/symlink/tmp461.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| IEC standart yayını 2724 | https://webstore.iec.ch/publication/2724 | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · atıf `⚠️ VERIFICATION REQUIRED` |
| IEEE 61000-4-2 (ESD) standart | https://standards.ieee.org/ieee/61000/4278/ | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · atıf `⚠️ VERIFICATION REQUIRED` |

## §7 · Açık Sorular

| Soru | Durum |
|---|---|
| Akım limit eşiği (kesin) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Gerilim sınır (TVS Vrwm) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| OTP sıcaklık eşiği | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| ESD test seviyesi (±kV) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Surge enerji sınıfı (IEC) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| DC-offset röle gecikmesi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §8 · Authority · Kapanış

- Bu modül `electroncis/guc-ve-koruma` yetkisi altındadır; scope Q1 (`⚠️ VERIFICATION REQUIRED`) nedeniyle `status: draft`.
- Koruma eşiklerinden yalnız DC offset >0.5 V (röle) kural olarak kayıtlı; geri kalan eşik/tolerans `⚠️ VERIFICATION REQUIRED`.
- Dış iddialar (§6) tek tek HTTP 200 doğrulanmış, envanter-dışıdır; özellik/atıf `⚠️ VERIFICATION REQUIRED` işaretlidir.
- Bu dosyanın yazımı ve doğrulanması: bu görev oturumunda (2026-10-13).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
