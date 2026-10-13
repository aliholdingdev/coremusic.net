---
id: electroncis/guc-ve-koruma/guc-kaynaklari-ve-regulatorler
title: "Güç Kaynakları ve Regülatörler"
node_type: module
parent_id: electroncis/guc-ve-koruma
level: 4
group: kod
status: draft
updated: 2026-10-13
---

# guc-kaynaklari-ve-regulatorler

> `electroncis/guc-ve-koruma` alt-kapsamının regulator modülü: lineer (LDO) vs anahtarlama (buck/boost/buck-boost), topoloji seçimi, ripple & EMI, termal ve ölçüm. Scope Q1 (`⚠️ VERIFICATION REQUIRED`) geçerli. Repo'da devre seviyesi kod YOK — kaynak: .ai/electronic/** (60 dosya); gerçek bileşen değerleri `⚠️ VERIFICATION REQUIRED`.

## §0 · Kapsam

| Konu | Var | Komşu |
|---|---|---|
| Lineer vs anahtarlama | evet | — |
| Buck / boost / buck-boost | evet | — |
| Ripple & EMI | evet | — |
| Termal | sınır | K001 |
| Ölçüm | evet | — |
| Koruma eşiği | sınır | koruma-esik |

## §1 · Lineer (LDO) Regülatör

| Özellik | Artı | Eksi |
|---|---|---|
| Gürültü | çok düşük | — |
| PSRR | yüksek (düşük fs) | yüksek fs'de düşer |
| Karmaşıklık | basit | — |
| Verim | — | (VOUT/VIN) |
| Isı | — | (VIN−VOUT)·I |
| Yanıt | hızlı | — |

## §1b · LDO Seçim Kriterleri

| Kriter | Not | Durum |
|---|---|---|
| Giriş-çıkış farkı (dropout) | VIN−VOUT ≥ dropout | `⚠️ VERIFICATION REQUIRED` |
| Çıkış gürültüsü | µVrms | `⚠️ VERIFICATION REQUIRED` |
| PSRR | dB @ fs | `⚠️ VERIFICATION REQUIRED` |
| Akım kapasitesi | yük | `⚠️ VERIFICATION REQUIRED` |
| Termal (θJA) | °C/W | `⚠️ VERIFICATION REQUIRED` |

## §2 · Anahtarlama Regülatör — Topolojiler

| Topoloji | Girdi-Çıktı | Kullanım | Verim |
|---|---|---|---|
| Buck | VIN > VOUT | düşürme | yüksek |
| Boost | VIN < VOUT | yükseltme | yüksek |
| Buck-boost | iki yön | esnek | yüksek |
| Flyback | izole | güvenlik | orta |
| SEPIC | eş-eşdeğer | orta | orta |
| Charge-pump | pompa | küçük | orta |

## §2b · Buck Topolojisi

| Öğe | Değer/Not | Durum |
|---|---|---|
| Anahtar MOSFET | Rds(on) | `⚠️ VERIFICATION REQUIRED` |
| İndüktör L | DCR, satürasyon | `⚠️ VERIFICATION REQUIRED` |
| Çıkış kapasitör | ESR, ripple | `⚠️ VERIFICATION REQUIRED` |
| Frekans fs | PWM | `⚠️ VERIFICATION REQUIRED` |
| Duty D | VOUT/VIN | hesap |
| Ripple ΔIL | (VIN−VOUT)·D/(L·fs) | hesap |

## §2c · Boost Topolojisi

| Öğe | Not | Durum |
|---|---|---|
| İndüktör | akım satürasyon | `⚠️ VERIFICATION REQUIRED` |
| Diyot | Schottky | `⚠️ VERIFICATION REQUIRED` |
| Duty | VOUT/(VIN·η) | hesap |
| Giriş akımı | yüksek (yükseltme) | — |
| Ripple | çıkış kapasitör | `⚠️ VERIFICATION REQUIRED` |

## §2d · Buck-Boost Topolojisi

| Öğe | Not | Durum |
|---|---|---|
| Kapama | 4-anahtar / inverting | `⚠️ VERIFICATION REQUIRED` |
| Duty | VOUT/(VIN+VOUT) | hesap |
| Negatif çıkış | inverting | — |
| Kullanım | rail esnekliği | — |

## §3 · Ripple

| Kaynak | Belirti | Azaltma |
|---|---|---|
| Anahtarlama | fs frekansı | LC filtre |
| İndüktör ΔIL | üçgen | L↑ |
| Kapasitör ESR | Vripple=ΔI·ESR | düşük ESR |
| LDO | PSRR | LDO son katman |

## §3b · Ripple-Azaltma Yöntemleri

| Yöntem | Etki | Eksi |
|---|---|---|
| LC filtre | fs bastırma | alan, maliyet |
| Düşük-ESR kapasitör | ripple ↓ | maliyet |
| LDO son katman | ripple ↓↓ | ısı |
| Daha yüksek fs | L/C ↓ | EMI |
| Post-regülasyon | temiz | verim |

## §4 · EMI (Anahtarlama Kaynaklı)

| EMI kaynağı | Azaltma |
|---|---|
| Yüksek dV/dt | snubber, gate |
| Yüksek dI/dt | indüktör, layout |
| Anahtarlama düğüm | min alan |
| Loop alanı | küçük akım döngüsü |
| Filtre | LC / common-mode |

## §4b · EMI-Önleme PCB/Layout

| Kural | Neden |
|---|---|
| Küçük güç döngüsü | EMI ↓ |
| Toprak düzlemi | return yolu |
| Anahtarlama-node min | emisyon |
| Giriş kapasitörü yakın | ripple |
| Separasyon analog/dijital | gürültü |

## §5 · Termal

| Konu | Hesap | Not |
|---|---|---|
| LDO kaybı | (VIN−VOUT)·I | ısı |
| Buck kaybı | (1−η)·P | η |
| θJA | °C/W | paket |
| ΔT | P·θJA | sıcaklık |
| Maks P | (Tjmax−Ta)/θJA | limit |

## §5b · Termal-Derating

| Faktör | Etki |
|---|---|
| Yüksek Ta | ΔT başlangıç ↓ |
| θJA (hava) | soğutma |
| Soğutucu | θJA ↓ |
| Akım derating | sıcakta akım ↓ |

## §6 · Ölçüm

| Ölçüm | Alet | Kabul |
|---|---|---|
| Çıkış DC | DMM | ±%5 |
| Ripple | Osc AC | ≤ hedef |
| Verim | VIN·IIN vs VOUT·IOUT | η |
| PSRR | ripple inj + DMM | dB |
| Termal | TC/termal kamera | ΔT |
| Yük geçişi | E-load + Osc | overshoot |

## §6b · Ölçüm-Öncesi Hazırlık

| Adım | İş |
|---|---|
| 1 | Bilinen yük bağla |
| 2 | VIN ayarla |
| 3 | Ripple AC ölç |
| 4 | Verim hesapla |
| 5 | Termal izle |

## §1c · LDO vs Buck Karar Matrisi

| Durum | Seçim | Gerekçe |
|---|---|---|
| VIN≈VOUT, düşük akım | LDO | verim sorun değil, temiz |
| VIN≫VOUT, yüksek akım | Buck | LDO çok ısı |
| Temiz analog rail | Buck ön + LDO son | verim + temiz |
| Çok düşük gürültü | LDO | gürültü |
| Yüksek verim zorunlu | Buck | verim |

## §1d · LDO PSRR- Frekans İlişkisi

| Frekans bölgesi | PSRR | Not |
|---|---|---|
| DC / düşük | yüksek | — |
| fs (anahtarlama) | düşer | LDO son katman |
| Yüksek fs | düşük | LC ön filtre |
| Çok yüksek | — | ölçüm gerekli |

## §2e · Topoloji Karşılaştırma Tablosu

| Topoloji | Verim | Gürültü | Karmaşıklık | İzolasyon |
|---|---|---|---|---|
| LDO | düşük | çok düşük | basit | yok |
| Buck | yüksek | orta | orta | yok |
| Boost | yüksek | orta | orta | yok |
| Buck-boost | yüksek | orta | orta | yok |
| Flyback | orta | yüksek | karmaşık | var |
| Charge-pump | orta | düşük-orta | basit | yok |

## §3c · Ripple-Hesap Mantığı

| Büyüklük | İlişki | Etki |
|---|---|---|
| ΔIL | (VIN−VOUT)·D/(L·fs) | L↑ → ΔIL↓ |
| Vripple | ΔI·ESR | ESR↓ → ripple↓ |
| Vripple(C) | ΔI/(8·fs·C) | C↑ → ripple↓ |
| Toplam ripple | ikisi | min |

## §3d · LC-Filter Ripple-Azaltma

| Parametre | Etki |
|---|---|
| L (ön) | fs bastırma ↑ |
| C (ön) | fs bastırma ↑ |
| fs | L/C ↓ |
| Sönüm | ρ kontrol |

## §4c · EMI-Ölçüm Mantığı

| Ölçüm | Alet | Kabul |
|---|---|---|
| İletken emisyon | SA + LISN | limit (UNKNOWN) |
| Radyasyon | SA + anten | limit (UNKNOWN) |
| Differansiyel | — | LC |
| Common-mode | — | choke |

## §5c · Buck Termal-Hesap Mantığı

| Büyüklük | İlişki |
|---|---|
| Kayıp | (1−η)·Pout |
| ΔT | P·θJA |
| Tj | Ta+ΔT |
| Maks P | (Tjmax−Ta)/θJA |
| Derating | sıcakta ↓ |

## §5d · θJA- Faktörleri

| Faktör | θJA etkisi |
|---|---|
| Paket tipi | θJA |
| PCB bakır alan | θJA ↓ |
| Soğutucu | θJA ↓↓ |
| Hava akımı | θJA ↓ |

## §6c · Ripple-Ölçüm Yöntemi

| Adım | Alet | Not |
|---|---|---|
| 1 | Osc AC-koplu | ripple |
| 2 | Probe yakın | gerçek |
| 3 | BW limit | gürültü |
| 4 | Ripple ölç | mV |

## §6d · PSRR-Ölçüm Yöntemi

| Adım | İş |
|---|---|
| 1 | Girişe ripple enjekte |
| 2 | Çıkış ripple ölç |
| 3 | PSRR = 20log(Vin_ac/Vout_ac) |
| 4 | fs'de tekrarla |

## §6e · Yük-Geçişi ÖLÇÜM

| Adım | Alet | Kabul |
|---|---|---|
| 1 | E-load step | — |
| 2 | Osc tekilleme | overshoot |
| 3 | Settle süre | — |
| 4 | Karşılaştırma | sim |

## §2f · Buck-Duty Mantığı

| Büyüklük | İlişki |
|---|---|
| D | VOUT/(VIN·η) |
| 1−D | kesme oranı |
| Minimum D | yüksek VIN |
| Satürasyon | min off-time |

## §2g · Flyback / SEPIC — Kısa Not

| Topoloji | İzolasyon | Kullanım | Zorluk |
|---|---|---|---|
| Flyback | var | izole güç | tasarımı karmaşık |
| SEPIC | yok | VIN esnek | kapasitör |
| Ćuk | yok | negatif | karmaşık |

## §1e · LDO Termal-Örnek Mantığı

| Büyüklük | İlişki |
|---|---|
| P (LDO) | (VIN−VOUT)·Iout |
| ΔT | P·θJA |
| Tj | Ta+ΔT |
| Limit | Tjmax |

## §3e · Kapasitör-Tipi Ripple İlişkisi

| Tip | ESR | Kullanım |
|---|---|---|
| Elektrolitik | yüksek | bulk |
| Seramik (MLCC) | çok düşük | ripple |
| Tantal | düşük-orta | bulk-temiz |
| Polimer | düşük | çıkış |

## §4d · EMI-Snubber / Gate

| Yöntem | Etki | Eksi |
|---|---|---|
| RC snubber | dV/dt ↓ | kayıp |
| Gate direnç | geçiş yavaş | kayıp |
| Soft-switch | EMI ↓ | karmaşık |

## §5e · Termal-Rölanti (Buck vs LDO)

| VIN-VOUT | Akım | Seçim |
|---|---|---|
| küçük | düşük | LDO |
| küçük | yüksek | LDO (kontrol) |
| büyük | düşük | LDO |
| büyük | yüksek | Buck |

## §6f · Verim-ÖLÇÜM Tablosu

| Girdi | Ölçüm |
|---|---|
| VIN | DMM |
| IIN | DMM (seri) |
| VOUT | DMM |
| IOUT | DMM / yük |
| η | POUT/PIN |

## §8b · Açık Sorular — Devam

| Soru | Durum |
|---|---|
| Çoklu rail ( kaç rail ) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| DC-DC entegratör seçimi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| LDO marka/model | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Anahtarlama fs (hedef) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| EMI limit (hangi standart) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §6g · Ripple-Hedef (Ölçülmemiş)

| Rail | Ripple hedef | Durum |
|---|---|---|
| AVDD | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| DVDD | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| HP_OUT | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |

## §6h · ÖLÇÜM Kabul Matrisi (Regulator)

| Parametre | Kabul | Durum |
|---|---|---|
| Çıkış DC | ±%5 | `⚠️ VERIFICATION REQUIRED` |
| Ripple | ≤ hedef | `⚠️ VERIFICATION REQUIRED` |
| Verim η | hedef | `⚠️ VERIFICATION REQUIRED` |
| PSRR @fs | hedef | `⚠️ VERIFICATION REQUIRED` |
| ΔT | ≤ limit | `⚠️ VERIFICATION REQUIRED` |

## §1f · LDO-Parametre Özet

| Parametre | Not |
|---|---|
| VIN max | limit |
| VOUT | ayar |
| Iout max | yük |
| Dropout | VIN−VOUT |
| Gürültü | µVrms |
| PSRR | dB |

## §2h · Buck-Parametre Özet

| Parametre | Not |
|---|---|
| VIN range | giriş |
| VOUT | çıkış |
| Iout | yük |
| fs | frekans |
| L | indüktör |
| Cout | kapasitör |

## §3f · Ripple-Sönüm İlişkisi

| Faktör | Etki |
|---|---|
| ρ (ESR/L) | sönüm |
| ρ<0.5 | under-damped |
| ρ>0.5 | over-damped |
| ρ≈0.5 | critical |

## §4e · EMI-Gerçek Ölçüm Notu

| Not | Durum |
|---|---|
| EMI ölçüm platformu | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| LISN var mı | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Limit (dBµV) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §5f · Termal-Gerçek Ölçüm

| Alet | Ölçüm |
|---|---|
| Termokupl | Tj yaklaşma |
| Termal kamera | yüzey |
| DMM (temp) | — |

## §6i · Verim-Ölçüm-Sınırı

| Sınır | Durum |
|---|---|
| Verim hedefi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Ölçüm tekrarı | — |
| η-ölçüm kalibrasyon | — |

## §2i · Topoloji-Başına Kondansatör/İndüktör Notu

| Topoloji | L | C |
|---|---|---|
| Buck | giriş-çıkış | çıkış |
| Boost | giriş | çıkış |
| Buck-boost | — | çıkış |
| Flyback | transformer | çıkış |

## §1g · LDO-Bağlantı Notu

| Pin | Bağlantı |
|---|---|
| IN | ham DC |
| OUT | temiz rail |
| GND | star-ground |
| EN (varsa) | sequencing |
| NR (varsa) | gürültü ↓ |

## §3g · Ripple-Toplam Not

| Bileşen | Toplam |
|---|---|
| Kapasitif | ripple(C) |
| ESR | ripple(ESR) |
| LDO-red | PSRR |

## §8c · Açık Sorular — Son

| Soru | Durum |
|---|---|
| Post-LDO gürültü filtresi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Rail-güç bütçe ölçümü | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §5g · Termal-ÖLÇÜM Kabul

| Parametre | Kabul |
|---|---|
| Tj | ≤ Tjmax |
| ΔT | ≤ limit |
| Derating | uygulanır |

## §0b · Kapsam Özet

| Var | YOK |
|---|---|
| Topoloji seçimi | Soğutma (K001) |
| Ripple/EMI | Koruma esik (diğer modül) |
| Ölçüm | Firmware (K005) |

## §6j · Ölçüm-Sıra Özet

| Sıra | Ölçüm |
|---|---|
| 1 | DC |
| 2 | Ripple |
| 3 | Verim |

## §7 · Bilgi İddia Matrisi (Web Araştırma · 2026-10-13)

> Gerçek, envanter-dışı, tek tek HTTP 200 doğrulanmış URL'ler. Datasheet özellik/atıf `⚠️ VERIFICATION REQUIRED`.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| LM2596 buck datasheet | https://www.ti.com/lit/ds/symlink/lm2596.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| TPS62130 buck datasheet | https://www.ti.com/lit/ds/symlink/tps62130.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| TPS7A47 ultra-low-noise LDO datasheet | https://www.ti.com/lit/ds/symlink/tps7a47.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| TPS5430 buck ürün sayfası | https://www.ti.com/product/TPS5430 | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| LM5164 buck ürün sayfası | https://www.ti.com/product/LM5164 | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| SLOA093 uygulama notu (literatür-no) | https://www.ti.com/lit/an/sloa093/sloa093.pdf | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · başlık `⚠️ VERIFICATION REQUIRED` |

## §8 · Açık Sorular

| Soru | Durum |
|---|---|
| Ana regulator topolojisi (buck mu boost mü) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Çıkış rail gerilimleri (±V, 3.3/5/12) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Anahtarlama frekansı fs | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Giriş DC aralığı | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| θJA / soğutma (K001) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| LDO gürültü hedefi (µVrms) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §9 · Authority · Kapanış

- Bu modül `electroncis/guc-ve-koruma` yetkisi altındadır; scope Q1 (`⚠️ VERIFICATION REQUIRED`) nedeniyle `status: draft`.
- Gerçek regulator/ürün URL'leri (§7) tek tek HTTP 200 doğrulanmış, envanter-dışıdır; özellik/atıf `⚠️ VERIFICATION REQUIRED` işaretlidir.
- Ölçülmemiş değerler `UNKNOWN`/`⚠️ VERIFICATION REQUIRED`; ölçüme dayanmayan topoloji/verim `verified` sayılmaz.
- Bu dosyanın yazımı ve doğrulanması: bu görev oturumunda (2026-10-13).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
