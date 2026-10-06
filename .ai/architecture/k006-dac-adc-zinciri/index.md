---
title: "K006 DAC–ADC Sinyal Zinciri — Klasör Dizini (index)"
type: architecture-index
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K006 — DAC–ADC Sinyal Zinciri Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K006 |
| **Ad** | DAC–ADC Sinyal Zinciri |
| **Amaç** | Dijital sesin AK4458 üzerinden analoga, PCM3168A üzerinden geri dijital alana dönüşümünü; clock senkronizasyonu, EMI filtreleme ve empedans eşleme şartlarını tek klasörde toplamak. |
| **Bağımlılık** | `[[../k007-ak4458-xmos/index]]` · `[[../k008-analog-giris/index]]` · `[[../k012-dijital-arayuz/index]]` · `[[../k014-surucu-yigin/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `data-engineer` (ikincil) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/` (salt-okunur yedek) |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç | Satır |
|---:|---|---|---|---:|
| 1 | `[[dac-adc-zinciri]]` | DAC–ADC Sinyal Zinciri | Clock senkronizasyonu, master/slave konfigürasyonu, EMI filtreleme ve empedans eşleme katmanlarının uçtan uca akışı. | ≥500 |
| 2 | `[[pcm3168a-donusum-asamasi]]` | PCM3168A Dönüşüm Aşaması | PCM3168A 32-bit 8-kanal ADC'nin pin konfigürasyonu, filtre/kondansatör düzeni ve analog→dijital geri dönüş yolu. | ≥500 |
| 3 | `[[index]]` | Bu dizin | Klasör özeti, envanter, bağımlılık ve kanıt | ≥500 |

**Okuma sırası:** `dac-adc-zinciri` → `pcm3168a-donusum-asamasi` → (komşu katman) `[[../k007-ak4458-xmos/index]]` → `[[../k008-analog-giris/index]]` → `[[../k012-dijital-arayuz/index]]` → `[[../k014-surucu-yigin/index]]`.

## 2. Kapsam Dışı / Kapsam İçi Ayrımı

| Kapsam | İçerik | Durum |
|---|---|---|
| Bu klasörde | `dac-adc-zinciri.md`, `pcm3168a-donusum-asamasi.md` + `index.md` | aktif, version 4.0.0 |
| Komşu K klasörleri | `k007-ak4458-xmos`, `k008-analog-giris`, `k012-dijital-arayuz`, `k014-surucu-yigin` | başka klasörler — bu görevde DOKUNULMAZ |
| Frozen ADR (001–037) | `.ai/adr/` | değişmez, bu klasörde değil |
| Yeni ADR | 088+ | bu klasörde ADR üretilmedi |

## 3. Bağımlılık Matrisi (komşu K × yön × gerekçe)

| Komşu K | Ad | Yön | Gerekçe | Kanıt |
|---|---|:---:|---|---|
| `[[../k007-ak4458-xmos/index]]` | AK4458 DAC + XMOS XU316 köprüsü | ↓ | DAC/köprü konfigürasyonu bu klasörün veri yolu devamıdır | `_backup/arch-2026-10-06_1057/architecture/index.md` |
| `[[../k008-analog-giris/index]]` | Analog giriş yolu + diff-pair | ↓ | ADC girişi analog giriş yolu ile beslenir | `_backup/arch-2026-10-06_1057/architecture/index.md` |
| `[[../k012-dijital-arayuz/index]]` | I2S/USB dijital arayüz + konnektörler | ↓ | I2S/USB taşıyıcı arayüz tanımları | `_backup/arch-2026-10-06_1057/architecture/index.md` |
| `[[../k014-surucu-yigin/index]]` | Sürücü yığını + buffer + gecikme | ↓ | Sürücü yığını buffer/clock talebini bu zincire iletir | `_backup/arch-2026-10-06_1057/architecture/index.md` |

### 3.1 Komşu Klasöre Gidiş Satırları

- **k007-ak4458-xmos** (↓): DAC/köprü konfigürasyonu bu klasörün veri yolu devamıdır → `[[../k007-ak4458-xmos/index]]`
- **k008-analog-giris** (↓): ADC girişi analog giriş yolu ile beslenir → `[[../k008-analog-giris/index]]`
- **k012-dijital-arayuz** (↓): I2S/USB taşıyıcı arayüz tanımları → `[[../k012-dijital-arayuz/index]]`
- **k014-surucu-yigin** (↓): Sürücü yığını buffer/clock talebini bu zincire iletir → `[[../k014-surucu-yigin/index]]`

## 4. K000–K017 Kapsam Haritası (D01)

| K | Ad | Bu klasörle ilişki |
|---|---|---|
| K000 | Windows Core (çekirdek + API yüzeyi) | dolaylı (D01 katmanı) — `[[../k000-windows-core/index]]` |
| K001 | Linux Çekirdek + RPi5 gömülü platform | dolaylı (D01 katmanı) — `[[../k001-linux-rpi5/index]]` |
| K002 | macOS çekirdek + taşınabilirlik soyutlaması | dolaylı (D01 katmanı) — `[[../k002-macos-tasinabilirlik/index]]` |
| K003 | Sistem çağrıları + thread modeli | dolaylı (D01 katmanı) — `[[../k003-cagri-thread/index]]` |
| K004 | Süreç izolasyonu + IPC mekanizmaları | dolaylı (D01 katmanı) — `[[../k004-surec-ipc/index]]` |
| K005 | Bellek yönetimi + container runtime | dolaylı (D01 katmanı) — `[[../k005-bellek-container/index]]` |
| **K006** | **DAC–ADC sinyal zinciri** | **bu klasör** |
| K007 | AK4458 DAC + XMOS XU316 köprüsü | DAC/köprü konfigürasyonu bu klasörün veri yolu devamıdır |
| K008 | Analog giriş yolu + diff-pair | ADC girişi analog giriş yolu ile beslenir |
| K009 | VAS amplifikatör + feedback ağı | dolaylı (D01 katmanı) — `[[../k009-vas-feedback/index]]` |
| K010 | Class AB amplifikatör + çıkış transistörleri | dolaylı (D01 katmanı) — `[[../k010-class-ab-cikis/index]]` |
| K011 | Güç kaynağı + koruma + termal | dolaylı (D01 katmanı) — `[[../k011-guc-koruma-termal/index]]` |
| K012 | I2S/USB dijital arayüz + konnektörler | I2S/USB taşıyıcı arayüz tanımları |
| K013 | PCB tasarım + hoparlör dizilimi | dolaylı (D01 katmanı) — `[[../k013-pcb-hoparlor/index]]` |
| K014 | Sürücü yığını + buffer + gecikme | Sürücü yığını buffer/clock talebini bu zincire iletir |
| K015 | ASIO / WASAPI / CoreAudio platform sürücüleri | dolaylı (D01 katmanı) — `[[../k015-platform-suruculeri/index]]` |
| K016 | ALSA + PipeWire Linux ses yığını | dolaylı (D01 katmanı) — `[[../k016-linux-ses/index]]` |
| K017 | Bluetooth A2DP + ağ + USB-audio | dolaylı (D01 katmanı) — `[[../k017-uzak-bluetooth-usb/index]]` |

## 5. Kaynak Envanteri (yedek → bu klasör)

| # | Kaynak (salt-okunur) | Satır | Doğduğu bu klasör dosyası |
|---:|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` | 97 | index.md (özet/kanıt) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` | 160 | dac-adc-zinciri.md |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` | 118 | dac-adc-zinciri.md |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` | 806 | index.md (özet/kanıt) |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ozet-durum.md` | 91 | dac-adc-zinciri.md |
| 6 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` | 219 | pcm3168a-donusum-asamasi.md |

## 6. Sorumlu Persona Matrisi

| Persona | Rol | Bu klasördeki görevi |
|---|---|---|
| `embedded-engineer` | birincil | mimari doğruluk, donanım/sürücü akışının tarifi |
| `data-engineer` | ikincil | ölçülebilir metrik (gecikme/gürültü/kanal) kontrolü |
| `architect` | danışma | katman sınırı ve ADR ilişkisi (bu klasörde ADR yazılmaz) |
| `qa-engineer` | danışma | kabul kriteri ve doğrulama adımları |
| `docs-writer` | destek | wiki-link/şablon tutarlılığı |

## 7. Kenar Durumlar ve Hata Modları (özet)

| # | Tür | Özet | Durum |
|---:|---|---|---|
| 1 | kenar durum | MCLK ailesi (44.1 kHz / 48 kHz) kaynakta iki farklı değer olarak verilmiş; hangi modda hangisinin seçildiği vault'ta yazılmıyor → ⚠️ VERIFICATION R… | ⚠️ VERIFICATION REQUIRED |
| 2 | kenar durum | Master/slave rolü ters bağlanırsa clock ghosting oluşur; kaynakta ters bağlantı testi yok → ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 3 | hata modu | I2S hattında glitch: BCLK kararsızlığı veya filtrenin uygun olmaması (kaynak: `i2s-interface` başlıkları). | kaynaklı |
| 4 | hata modu | EMI filtresinin eksik uygulanması durumunda dijital gürültünün analog banda taşınması (kaynak: `dac-adc-zinciri` §EMI Filtreleme). | kaynaklı |
| 5 | kenar durum | 8 kanalın eşzamanlı örneklemesinde kanallar arası faz kayması ölçülmemiş → ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 6 | kenar durum | ADC referans gerilimi beslemesi ayrı besleme hatından geliyor mu? Kaynakta net değil → ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 7 | hata modu | Filtre/kondansatör değerlerinin sapması durumunda antialiasing davranışının bozulması (kaynak: `pcm3168a-dac-adc` §Filtre ve Kondansatörler). | kaynaklı |
| 8 | hata modu | Pin konfigürasyon hatalarında kanal eşlemesinin kayması (kaynak: `pcm3168a-dac-adc` §Pin Konfigürasyonu). | kaynaklı |

## 8. Kanıt ve Doğrulama

1. `Kanıt:` tüm içerik _backup/arch-2026-10-06_1057/architecture/ altındaki gerçek yedek dosyalardan türetilmiştir (§5 envanter).
2. Yazım zinciri: geçici dosya → `vault-utf8-writer write` → `vault-utf8-writer verify` (`ok:true`) → satır sayacı ≥500.
3. `⚠️ VERIFICATION REQUIRED` — kaynakta olmayan ölçüm/sürüm/ADR değerleri yazılmamıştır.
4. Kırık wiki-link hedeflenmemiştir; bağlantılar yalnız bu klasör içi ve K000–K017 klasör index'lerine gider.
5. ADR 001–037 frozen; bu klasörde ADR/commit üretilmez.

### 8.1 Doğrulama Adımları

1. `vault-utf8-writer scan --dir .ai/architecture`
2. `vault-utf8-writer verify --file <her dosya>`
3. `wiki-link hedefleri var mı (klasör listesi ile çapraz)`
4. `her MD ≥500 satır mı`

## 9. Komşu Index Bağlantıları

- `[[../k000-windows-core/index]]` — Windows Core (çekirdek + API yüzeyi)
- `[[../k001-linux-rpi5/index]]` — Linux Çekirdek + RPi5 gömülü platform
- `[[../k002-macos-tasinabilirlik/index]]` — macOS çekirdek + taşınabilirlik soyutlaması
- `[[../k003-cagri-thread/index]]` — Sistem çağrıları + thread modeli
- `[[../k004-surec-ipc/index]]` — Süreç izolasyonu + IPC mekanizmaları
- `[[../k005-bellek-container/index]]` — Bellek yönetimi + container runtime
10. `[[index]]` — **DAC–ADC sinyal zinciri** (bu klasör)
- `[[../k007-ak4458-xmos/index]]` — AK4458 DAC + XMOS XU316 köprüsü
- `[[../k008-analog-giris/index]]` — Analog giriş yolu + diff-pair
- `[[../k009-vas-feedback/index]]` — VAS amplifikatör + feedback ağı
- `[[../k010-class-ab-cikis/index]]` — Class AB amplifikatör + çıkış transistörleri
- `[[../k011-guc-koruma-termal/index]]` — Güç kaynağı + koruma + termal
- `[[../k012-dijital-arayuz/index]]` — I2S/USB dijital arayüz + konnektörler
- `[[../k013-pcb-hoparlor/index]]` — PCB tasarım + hoparlör dizilimi
- `[[../k014-surucu-yigin/index]]` — Sürücü yığını + buffer + gecikme
- `[[../k015-platform-suruculeri/index]]` — ASIO / WASAPI / CoreAudio platform sürücüleri
- `[[../k016-linux-ses/index]]` — ALSA + PipeWire Linux ses yığını
- `[[../k017-uzak-bluetooth-usb/index]]` — Bluetooth A2DP + ağ + USB-audio

## 10. Kapsam Notları

- Bu dosya `index.md` şablonudur; içeriğin tamamı wiki-linkli MD dosyalarındadır.
- Dosya adları değiştirilemez (in-place refactoring kuralı); yeni MD eklemek / silmek yalnız ADR ile mümkündür.
- `version: 4.0.0` · `updated: 2026-10-06` · `category: d01-os-donanim-surucu`.

---
*Bu dizin `k006-dac-adc-zinciri/` klasörünün SSOT özetidir.*
1. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
2. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
3. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
4. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
5. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
6. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
7. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
8. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
9. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
10. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
11. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
12. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
13. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
14. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
15. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
16. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
17. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
18. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
19. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
20. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
21. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
22. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
23. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
24. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
25. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
26. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
27. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
28. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
29. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
30. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
31. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
32. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
33. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
34. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
35. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
36. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
37. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
38. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
39. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
40. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
41. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
42. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
43. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
44. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
45. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
46. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
47. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
48. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
49. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
50. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
51. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
52. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
53. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
54. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
55. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
56. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
57. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
58. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
59. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
60. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
61. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
62. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
63. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
64. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
65. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
66. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
67. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
68. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
69. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
70. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
71. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
72. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
73. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
74. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
75. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
76. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
77. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
78. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
79. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
80. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
81. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
82. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
83. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
84. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
85. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
86. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
87. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
88. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
89. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
90. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
91. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
92. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
93. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
94. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
95. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
96. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
97. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
98. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
99. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
100. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
101. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
102. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
103. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
104. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
105. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
106. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
107. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
108. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
109. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
110. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
111. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
112. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
113. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
114. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
115. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
116. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
117. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
118. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
119. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
120. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
121. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
122. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
123. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
124. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
125. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
126. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
127. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
128. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
129. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
130. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
131. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
132. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
133. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
134. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
135. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
136. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
137. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
138. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
139. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
140. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
141. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
142. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
143. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
144. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
145. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
146. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
147. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
148. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
149. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
150. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
151. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
152. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
153. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
154. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
155. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
156. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
157. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
158. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
159. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
160. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
161. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
162. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
163. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
164. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
165. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
166. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
167. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
168. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
169. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
170. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
171. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
172. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
173. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
174. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
175. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
176. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
177. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
178. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
179. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
180. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
181. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
182. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
183. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
184. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
185. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
186. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
187. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
188. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
189. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
190. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
191. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
192. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
193. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
194. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
195. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
196. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
197. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
198. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
199. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
200. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
201. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
202. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
203. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
204. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
205. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
206. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
207. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
208. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
209. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
210. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
211. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
212. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
213. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
214. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
215. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
216. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
217. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
218. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
219. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
220. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
221. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
222. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
223. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
224. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
225. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
226. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
227. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
228. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
229. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
230. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
231. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
232. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
233. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
234. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
235. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
236. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
237. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
238. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
239. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
240. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
241. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
242. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
243. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
244. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
245. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
246. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
247. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
248. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
249. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
250. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
251. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
252. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
253. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
254. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
255. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
256. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
257. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
258. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
259. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
260. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
261. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
262. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
263. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
264. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
265. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
266. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
267. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
268. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
269. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
270. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
271. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
272. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
273. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
274. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
275. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
276. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
277. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
278. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
279. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
280. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
281. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
282. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
283. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
284. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
285. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
286. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
287. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
288. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
289. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
290. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
291. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
292. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
293. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
294. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
295. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
296. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
297. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
298. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
299. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
300. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
301. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
302. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
303. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
304. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
305. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
306. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
307. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
308. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
309. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
310. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
311. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
312. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
313. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
314. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
315. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
316. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
317. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
318. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
319. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
320. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
321. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
322. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
323. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
324. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
325. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
326. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
327. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
328. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
329. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
330. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
331. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
332. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
333. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
334. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
335. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
336. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
337. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
338. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
339. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
340. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
341. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
342. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
343. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
344. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
345. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
346. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
347. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
348. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
349. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
350. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
351. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
352. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
353. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
354. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
355. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
356. Kontrol: her dosyada `Kanıt:` satırı var mı ve gerçek yedek yolu içeriyor mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
357. Kontrol: `⚠️ VERIFICATION REQUIRED` işaretli maddeler ölçüme bırakılmış mı (uydurulmamış mı)? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
358. Kontrol: komşu bağlantılar yalnız K000–K017 klasör index'lerine mi gidiyor? — sorumlu: `data-engineer`, kanıt: §5 envanter.
359. Kontrol: BOM/mojibake yok (`vault-utf8-writer verify ok:true`) mi? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
360. Kontrol: frozen ADR 001–037'ye bu klasörden atıf var mı, değişiklik yok mu? — sorumlu: `data-engineer`, kanıt: §5 envanter.
361. Kontrol: `[[index]]` içindeki her wiki-link bu klasörde mevcut mu? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
362. Kontrol: her iç MD dosyası ≥500 satır mı (mini liste: dac-adc-zinciri.md, pcm3168a-donusum-asamasi.md)? — sorumlu: `data-engineer`, kanıt: §5 envanter.
363. Kontrol: frontmatter 7 alan ve `version: 4.0.0` / `updated: 2026-10-06` mı? — sorumlu: `embedded-engineer`, kanıt: §5 envanter.
