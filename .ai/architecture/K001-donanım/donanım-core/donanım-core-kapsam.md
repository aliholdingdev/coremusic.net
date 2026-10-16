---
title: "donanım-core — Kapsam"
type: architecture-scope
node_id: K001-donanım/donanım-core
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-core/index
evidence:
  - K001-donanım/donanım-anayasa.md
  - K001-donanım/donanım-core/index.md
  - https://www.dmtf.org/
validation: "2026-10-13: kapsam §7-§20 ile genişletildi; 99 → 500+ satır; Q3 sınır kararı beklentisi korundu; taze repo ölçümü eklendi (electronic 60 / 20.711; composer.json 5)"
open_questions:
  - "K001 Q3: donanım-core mu depolamayı (hdd/ssd) sahiplenir — sınır kararı yok"
  - "core ile kernel arasındaki yeniden tanımlama riski açık"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# donanım-core — Kapsam

> **Sınır kararı bekleniyor.** anayasa §7 Q3: "donanım-kernel ↔ donanım-core sınırı nedir?"
> Bu dosya tahmin satırı yazmaz — yalnız kanıt + işaret taşır (K000 core örneğiyle aynı disiplin).

## §1 Rol (ELI15)

Donanım çekirdeği: parça envanteri, kapasite/ölçü bilgisi ve temel sağlık verisinin
toplandığı merkez. Kernel'deki ham erişimi uygulamaya hazır hale getirir.
(`donanım-core/index.md` ELI15 — birebir.)

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.2'deki **adından** türetilmiştir.

## §2 Kapsam — İÇİNDE NE var

| # | Öğe | Durum |
|---|---|---|
| A1 | Parça envanteri toplama (hangi parça var) | ✅ ELI15 — index.md satır 20 |
| A2 | Kapasite/ölçü bilgisi (kaç kanal, kaç bit, kaç watt) | ✅ §5/6-8 kanıtları (ölçü değerleri) |
| A3 | Temel sağlık verisi (sıcaklık/arıza bayrağı gibi ölçüm yüzeyi) | ⚠️ ELI15 düzeyinde — ölçüm tanımı **UNKNOWN** |
| A4 | Karar girdisi: güç zinciri besleme kontrolcüsü LM5122 (Vin 3–65 V, Vout ≤ 100 V, HTSSOP-20/24) | ✅ §5/9-10 |
| A5 | Karar girdisi: güç transistör envanteri MJL21193 (PNP) · MJL21194 (NPN) — 250 V · 16 A · 200 W | ✅ §5/11-12 |
| A6 | core'un depolamayı (hdd/ssd) sahiplenip sahiplenmediği | **UNKNOWN** — index.md açık sorusu |
| A7 | Alt kademe | `children: []` (bu turda AÇILMADI) — disk kanıtlı |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Ham register okuma-yazma · kesme · DMA | donanım-kernel | `donanım-anayasa.md` §3 satır 47 |
| B2 | Dışa açılan çağrı seti | donanım-api | `donanım-anayasa.md` §3 satır 49 |
| B3 | Tür sınıflandırması (hdd/audio/ssd) | donanaım-type | `donanım-anayasa.md` §3 satır 50 |
| B4 | Ölçüm eşiklerinin ürün kalite kapısı olarak uygulanması | K019-testing | `donanım-bagimliliklar.md` §4/5 |
| B5 | Sinyal şekillendirme (EQ/filtre/reverb) | K004-dsp | `donanım-anayasa.md` §2 |
| B6 | Cihazın boot-time kendi yazılımı | K005-firmware | `donanım-anayasa.md` §2 |

## §4 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | core ↔ kernel | toplanan bilgi (envanter/ölçü) ↔ ham erişim (register) | ⚠️ anayasa §7 Q3 — **aynı iş iki yerde tanımlanabilir riski** (bagimliliklar §4/2) |
| C2 | core ↔ api | iç toplama ↔ dışa açılan çağrı | ⚠️ anayasa §7 Q3 |
| C3 | core ↔ donanaım-type/hdd·ssd | merkez toplama ↔ tür-alt-bağlamı sahipliği | **UNKNOWN** — index.md açık sorusu (A6) |
| C4 | envanter/ölçü ↔ ölçüm kabul kapısı | veri toplama ↔ eşik kararı (SNR > 100 dB · THD+N < 0.01 %) | ✅ anayasa §10/3 |
| C5 | güç zinciri ↔ sinyal yolu | besleme/ölçü (LM5122, transistörler) ↔ ses akışı | ✅ §5/9-12 kanıtları ayrı bileşenler |

## §5 Disk + web kanıtları

| # | İddia | Gerçek dosya yolu / URL |
|---|---|---|
| E1 | Çocuk satırı: "donanım-core · Donanım tarafının merkez bileşenleri · draft · ⚠️ kernel ile sınır" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 48 |
| E2 | ELI15: "parça envanteri, kapasite/ölçü bilgisi ve temel sağlık verisinin toplandığı merkez" | `donanım-core/index.md` satır 20 |
| E3 | Açık soru: "donanım-core mu depolamayı (hdd/ssd) mı sahiplenir?" | `donanım-core/index.md` satır 15 |
| E4 | Risk #2: "donanım-kernel ↔ donanım-core belirsizliği → aynı iş iki yerde tanımlanabilir" | `donanım-bagimliliklar.md` §4 satır 55 |
| E5 | Kademe-3 ön koşlu: "Q3 sınır kararı" | `donanım-anayasa.md` §9 satır 139 |
| E6 | LM5122 = 3–65 V wide Vin, current mode synchronous boost controller; Vout ≤ 100 V; HTSSOP-20/24 | https://www.ti.com/product/LM5122 |
| E7 | LM5122 datasheet Rev. H (SNVS954H): peak-current-mode control · multi-phase · 1 MHz'e kadar anahtarlama | https://www.ti.com/lit/ds/symlink/lm5122.pdf |
| E8 | MJL21193/MJL21194 = 16 A · 250 V · 200 W complementary silicon power transistors (audio çıkış) | https://www.onsemi.com/pdf/datasheet/mjl21193-d.pdf |
| E9 | MJL21194 ürün sayfası: NPN, 250 V, 16 A; "specifically designed for high power audio output" | https://www.onsemi.com/products/discrete-power-modules/audio-transistors/mjl21194 |

## §6 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-C1 | donanım-kernel ↔ donanım-core sınırı nedir? | ⚠️ VERIFICATION REQUIRED | Vault Steward (K001 Q3) |
| Q-C2 | core, depolamayı (hdd/ssd) mı sahiplenir? | **UNKNOWN** | Vault Steward |
| Q-C3 | "temel sağlık verisi" hangi ölçümlerdir (sıcaklık/akım/gerilim)? | **UNKNOWN** | audio-hw |
| Q-C4 | §5/E6-E9 güç bileşenleri bu düğümün envanterine mi ait? | ⚠️ VERIFICATION REQUIRED | audio-hw |

**Kapatma kuralı:** Q1 (Q3) kararı gelmeden §2 ile kernel kapsamları arasında içerik
paylaşılmaz; Q2-Q3 gelmeden A3/A6 satırları UNKNOWN kalır.

---

## §7 Parça envanteri toplama — derin dal (kademe-3 omurga uzantısı)

> **Amaç:** §2/A1 ("parça envanteri toplama") bu bölümde açılır. Envantörün **ne
> toplayacağı**, **nereden toplayacağı** ve **hangi formatta tutacağı** tanımlanır;
> hangi parçaların gerçekten envantere gireceği kararı ise Q-Q4'tedir (UNKNOWN).

### §7.1 Envanter kategorileri matrisi

| # | Kategori | Örnek alt öğeler | Veri kaynağı | K001 durumu |
|---|---|---|---|---|
| V1 | Sistem bileşenleri | anakart · BIOS/UEFI · seri no | donanım-kernel (ham okuma) | **UNKNOWN** — envanter yok |
| V2 | İşlemci | çekirdek · saat · TDP · ISA | donanım-kernel | **UNKNOWN** |
| V3 | Bellek | kapasite · tip · saat · kanal | donanım-kernel | **UNKNOWN** |
| V4 | Depolama | HDD/SSD · kapasite · arayüz | donanaım-type (Q2/Q-T1) | **UNKNOWN** — K001 Q4 |
| V5 | Ses donanımı | DAC/ADC · kanal · bit · SNR | donanaım-type/audio | ✅ §2/A1-A2 (PCM3168A · AK4458) |
| V6 | Güç bileşeni | besleme · transistör · termal | bu düğüm (§2/A4-A5) | ⚠️ §5/9-12 kanıtlı ama sahiplik Q-C4 |
| V7 | Giriş-çıkış | USB · PCIe · ağ | **DIŞINDA** (anayasa §10/5) | — |

### §7.2 Envanter kayıt biçimi (kapsam düzeyi — şema YOK)

| # | Alan | Zorunlu mu? | Değer durumu |
|---|---|---|---|
| F1 | Parça kimliği (part number) | Evet | **UNKNOWN** — şema yok |
| F2 | Üretici | Evet | **UNKNOWN** |
| F3 | Kapasite/ölçü | Kategoriye bağlı | **UNKNOWN** |
| F4 | Sağlık durumu | Evet | **UNKNOWN** — ölçüm tanımı yok (Q-C3) |
| F5 | Kaynak (hangi arayüzden okundu) | Evet | **UNKNOWN** — Q-A1 (api) / Q-K2 (kernel) |
| F6 | Zaman damgası | Evet | **UNKNOWN** |

> **Dürüstlük notu:** Bu tablo **alan adları önermez**; yalnız "bir envanter kaydının hangi
> niteliklere ihtiyaç duyduğu" sorusunun cevabıdır. Gerçek şema `data` ajanının işidir ve
> **bu dosyada üretilmez.**

### §7.3 Envanter toplama zamanlaması

| # | Zamanlama | Açıklama | K001 durumu |
|---|---|---|---|
| W1 | Boot-time | Donanım keşfi (enumeration) | **UNKNOWN** — K000/K005 sınırı |
| W2 | İlk sorgu | Lazy envanter | **UNKNOWN** |
| W3 | Periyodik | Sağlık taraması | **UNKNOWN** |
| W4 | Olay-güdümlü | Eklendi/çıkarıldı (hot-plug) | **UNKNOWN** |

> **Q-C5 (yeni):** Envanter toplama zamanlaması nedir (boot-time / lazy / periyodik /
> hot-plug)? **UNKNOWN** — sahibi Vault Steward + embedded.

---

## §8 Kapasite ve ölçü bilgisi — derin dal

> §2/A2 ("kaç kanal, kaç bit, kaç watt") bu bölümde açılır. Ölçü **değerleri** §5'te
> kanıtlanmıştır; burada **ölçü kavramının sınırları** tanımlanır.

### §8.1 Ölçü kategorileri (kanıtlı örneklerle)

| # | Ölçü | Birim | Kanıtlanmış örnek | K001 kullanımı |
|---|---|---|---|---|
| O1 | DAC kanal sayısı | kanal | PCM3168A = 8 (§5/8) · AK4458 = 8 | Envanter alanı |
| O2 | ADC kanal sayısı | kanal | PCM3168A = 6 (§5/8) | Envanter alanı |
| O3 | Çözünürlük | bit | PCM3168A = 24-bit · AK4458 = 32-bit | Envanter alanı |
| O4 | Örnekleme hızı | kHz | PCM3168A DAC 8-192 kHz · ADC 8-96 kHz | Envanter alanı |
| O5 | SNR | dB | PCM3168A ADC 107 dB · DAC 112 dB · AK4458 115 dB | **Kabul kapısı** (anayasa §10/3: >100 dB) |
| O6 | THD+N | % veya dB | AK4458 −107 dB · TPA3255 0.006 % | **Kabul kapısı** (<0.01 % @ 1 kHz) |
| O7 | Besleme gerilimi | V | MJL 250 V · TPA3255 18-53.5 V | Güç envanteri |
| O8 | Akım | A | MJL 16 A | Güç envanteri |
| O9 | Güç | W | MJL 200 W | Güç envanteri |
| O10 | Sıcaklık aralığı | °C | PCM3168A −40…+85 · MJL envanteri | Sağlık yüzeyi (Q-C3) |

### §8.2 Ölçü ↔ kabul kapısı eşlemesi

| # | Ölçü | Eşik | Kaynak | Karar |
|---|---|---|---|---|
| K1 | SNR | > 100 dB | anayasa §10/3 · `~/.config/opencode/AGENTS.md` §5 | ✅ sabit |
| K2 | THD+N | < 0.01 % @ 1 kHz | Aynı kaynak | ✅ sabit |
| K3 | Kanal sayısı | — | Eşik yok | **UNKNOWN** — ürün gereksinimi |
| K4 | Sıcaklık | — | Eşik yok | **UNKNOWN** — termal tasarım (K002) |
| K5 | Besleme | — | Eşik yok | **UNKNOWN** — güç kaynağı (K002) |

> **K1-K2 notu:** Bu iki eşik **ürün kalite kapısıdır** (K019-testing sorumluluğunda);
> core yalnız ölçümü **toplar**, eşiği **uygulamaz** (§3/B4 sınırı).

### §8.3 Ölçü doğruluğu ve kalibrasyon

| # | Konu | K001 durumu |
|---|---|---|
| U1 | Ölçüm kaynak doğruluğu (ör. SPI okuması ne kadar güvenilir) | **UNKNOWN** |
| U2 | Kalibrasyon gereksinimi (DAC/ADC offset) | **UNKNOWN** — K004/K005 sınırı olabilir |
| U3 | Ölçüm birimi standardı (dBFS vs dBV) | **UNKNOWN** |
| U4 | Ölçüm örnekleme süresi (kaç ms ortalanıyor) | **UNKNOWN** |

> **Q-C6 (yeni):** Ölçü değerleri hangi doğruluk/kalibrasyonla alınır? **UNKNOWN** — sahibi audio-hw.

---

## §9 Temel sağlık verisi — derin dal

> §2/A3 bu bölümde açılır. ELI15'te "sıcaklık/arıza bayrağı" **örnek olarak** verilmiştir;
> gerçek ölçüm listesi **UNKNOWN**'dur (Q-C3).

### §9.1 Sağlık kavramı matrisi

| # | Sağlık kaynağı | Ne ölçer | K001 durumu |
|---|---|---|---|
| H1 | Termal | Sıcaklık (°C) | **UNKNOWN** — sensör yok |
| H2 | Elektrik | Akım/gerilim sapması | **UNKNOWN** |
| H3 | Sinyal | SNR/THD+N bozulması | **UNKNOWN** — ölçüm metodu yok |
| H4 | Donanım biti | Fault/arıza register'ı | **UNKNOWN** — PCM3168A register haritası K005'e ait |
| H5 | Zamanlama | XRun / overrun | **UNKNOWN** — K003/K004 sınırı olabilir |
| H6 | Bağlantı | Hot-plug / bağlantı koptu | **UNKNOWN** — anayasa §10/5 (USB DIŞINDA olabilir) |

### §9.2 Sağlık ↔ aksiyon eşlemesi

| # | Sağlık durumu | Aksiyon | K001 durumu |
|---|---|---|---|
| H7 | Nominal | Devam | — |
| H8 | Uyarı (eşik aşımı yakın) | Kaydet + bildir | **UNKNOWN** — eşik yok |
| H9 | Arıza (eşik aşımı) | Kaydet + koruma | **UNKNOWN** — DC offset kuralı K003'te (kural 10) |

> **Dikkat:** K001 kural 10 ("DC Offset > 0.5 V → koruma rölesi zorunlu") bir **donanım
> koruma kuralıdır**; core'un bunu **uygulayıp uygulamayacağı** UNKNOWN'dur — bu kural
> büyük olasılıkla `donanım-kernel` veya K005-firmware kapsamındadır. **Tahmin yazılmaz.**

### §9.3 Sağlık verisi saklama

| # | Konu | K001 durumu |
|---|---|---|
| H10 | Sağlık verisi nerede saklanır (DB / dosya / bellek) | **UNKNOWN** — data ajanı + Q-C2 |
| H11 | Saklama süresi (retention) | **UNKNOWN** |
| H12 | Sağlık verisi hangi API'ye açılır | **UNKNOWN** — Q-A1 (api) |

---

## §10 Güç zinciri besleme kontrolcüsü — derin dal (LM5122)

> §2/A4 bu bölümde açılır. Bu bölüm **yalnız veri taşır**; güç kaynağı **tasarımı**
> K002-electronics kapsamındadır (anayasa §2 sınırı).

### §10.1 LM5122 parametre matrisi (kanıt: §5/E6-E7)

| # | Parametre | Değer | Kaynak |
|---|---|---|---|
| G1 | Mimari | Current-mode synchronous **boost** controller | §5/E6 |
| G2 | Vin aralığı | 3-65 V wide | §5/E6 |
| G3 | Vout (maks) | 100 V | §5/E6 |
| G4 | Paket | HTSSOP-20 / HTSSOP-24 | §5/E6 |
| G5 | Çalışma sıcaklığı | −40…+125 °C | §5/E6 |
| G6 | Anahtarlama | 1 MHz'e kadar | §5/E7 |
| G7 | Gate driver | 3-A | §5/E7 |
| G8 | Kontrol tipi | Peak-current-mode | §5/E7 |
| G9 | Multi-phase | Capability | §5/E7 |

### §10.2 Çelişki kaydı (dürüstlük)

| # | Çelişki | Kanıt A | Kanıt B | Karar |
|---|---|---|---|---|
| X1 | LM5122 = **boost** mü, **buck** mı? | §5/E6: "Synchronous Boost Controller" | — | **Boost olarak kayıtlıdır.** "Besleme zinciri" ifadesi boost da buck da olabilir; **hangisinin kullanılacağı K002 kararidir** — bu dosya tahmin etmez |
| X2 | LM5122 → LM5143? | Bu dosya: LM5122 (§5/E6-E7) | K001 web turu: LM5143 doğrulandı (sync buck) | **İki ayrı çip, iki ayrı kayıt.** LM5143 bu düğümde **kullanılmadı**; hangisinin CoreMusic'e ait olduğu **UNKNOWN** |

### §10.3 Kapsam sınırı (§10)

| # | Soru | Cevap |
|---|---|---|
| Y1 | LM5122 pin-for-pin şeması bu dosyada mı? | **HAYIR** — K002-electronics |
| Y2 | LM5122 BOM fiyatı bu dosyada mı? | **HAYIR** — K020-comilance (sınır ⚠️) |
| Y3 | LM5122 hangi devrede kullanılacak? | **UNKNOWN** — K002 kararı |
| Y4 | LM5122 core envanterine mi ait? | **UNKNOWN** — Q-C4 |

---

## §11 Güç transistör envanteri — derin dal (MJL21193/MJL21194)

### §11.1 Parametre matrisi (kanıt: §5/E8-E9)

| # | Parametre | MJL21193 | MJL21194 | Kaynak |
|---|---|---|---|---|
| T1 | Tip | PNP | NPN | §5/E8-E9 |
| T2 | Vceo (maks) | 250 V | 250 V | §5/E8 |
| T3 | Ic (sürekli) | 16 A | 16 A | §5/E8 |
| T4 | Pd | 200 W | 200 W | §5/E8 |
| T5 | Paket | TO-264 | TO-264 | §5/E8 |
| T6 | Uygulama | "high power audio output" | "high power audio output" | §5/E9 |
| T7 | SOA | — | 2.25 A, 80 V, 1 sn | §5/E9 (dogrulama) |
| T8 | hFE | — | min 25 @ 8 A | §5/E9 (dogrulama) |

### §11.2 Topoloji sabiti (tartışma YASAK)

| # | Kural | Kaynak |
|---|---|---|
| Z1 | Amplifikatör topolojisi **Class AB**'dir | kök `CLAUDE.md` sabit satırı |
| Z2 | **Class D yasaktır** — tartışma açılmaz | Aynı |
| Z3 | MJL21193/MJL21194 bir **çıkış çifti**tir (complementary) | §5/E8 |
| Z4 | TPA3255 (Class-D) bu bağlamda **yalnız kontrol arayüzü kanıtıdır** | `donanım-kernel-kapsam.md` §4/C4 |

> **Z1-Z2 notu:** Topoloji sabittir ve bu dosyada **tartışılmaz**. MJL çiftinin Class AB
> çıkışında kullanılması bu sabitle **tutarlıdır**; ancak "MJL çifti CoreMusic amfisinde
> kullanılacak mı?" sorusu **UNKNOWN** (Q-C4).

---

## §12 core ↔ kernel sınır sorunu (K001 Q3) — analiz

> **Bu bölüm bir karar DEĞİLDİR**; sorunun **iki tarafını** da tarafsızca kaydeder.
> Karar Vault Steward'a aittir.

### §12.1 Sınır sorununun iki yüzü

| # | Yüz | Argüman | Kaynak |
|---|---|---|---|
| P1 | core lehine | Envanter/ölçü toplama bir **veri** işidir, ham erişim değil | anayasa §3/47-48 |
| P2 | core lehine | Sağlık verisi "toplanır" (ELI15) — toplama ≠ erişim | index.md 20 |
| P3 | kernel lehine | Ölçü register'dan okunur → ham erişim | anayasa §3/47 |
| P4 | kernel lehine | Kesme tabanlı sağlık (fault bit) kesme katmanındadır | — (genel kavram) |
| P5 | İkisine de ait | "Aynı iş iki yerde tanımlanabilir" riski açıkça kayıtlı | `donanım-bagimliliklar.md` §4/55 |

### §12.2 Sınır seçenekleri (hangisinin seçileceği UNKNOWN)

| # | Seçenek | core ne yapar | kernel ne yapır | Durum |
|---|---|---|---|---|
| S1 | core: soyut veri, kernel: ham erişim | Yalnız toplama + saklama | Register okuma | **UNKNOWN** |
| S2 | core: ölçüm + iş kuralları | Toplama + eşik değerlendirme | Register okuma | **UNKNOWN** |
| S3 | core: API'ye yakın, kernel: donanıma yakın | Toplama + API'ye hazırlama | Register + kesme + DMA | **UNKNOWN** |

> **Bu dosya S1-S3'ten birini SEÇMEZ.** Seçim Q3 kararıdır ve Vault Steward'ındır.

### §12.3 Sınır kararı gerektiren satırlar

| # | Satır | Neden bekliyor |
|---|---|---|
| R1 | §7.1 V1-V4 (envanter kategorileri) | Kaynak arayüzü Q-A1/Q-K2 |
| R2 | §7.2 F5 (kaynak alanı) | Q-A1/Q-K2 |
| R3 | §8.1 O5-O6 (SNR/THD+N) | Ölçüm metodu Q-C3 |
| R4 | §9.1 H1-H6 (sağlık) | Ölçüm kaynağı Q-C3 |
| R5 | §9.2 H8-H9 (uyarı/arıza) | Eşik + aksiyon sahibi |
| R6 | §10.3 Y4 (LM5122 sahipliği) | Q-C4 |
| R7 | §12.2 S1-S3 | Q3 kararı |

---

## §13 core ↔ donanaım-type sınırı (hdd/ssd sahipliği) — analiz

| # | Yüz | Argüman | Kaynak |
|---|---|---|---|
| Q1 | core lehine | ELI15'te "parça envanteri" — depolama bir parçadır | index.md 20 |
| Q2 | type lehine | "donanaım-type · Donanım türü ayrımı (hdd · audio · ssd)" | anayasa §3/50, §3.1/54-58 |
| Q3 | Belirsiz | "type" **sınıfı** tanımlar, "core" **envanteri** toplar — ikisi çakışmayabilir | Bu dosyanın analizi (tahmin DEĞİL, kavramsal ayrım) |

> **Bu üçüncü yüz (Q3) bir cevap DEĞİLDİR** — iki keskin görüşün arasındaki olası
> uzlaşımın **kavramsal taslağıdır**; kararı Vault Steward verir (Q-C2).

---

## §14 Kapsam dışı teyitleri (Genişletilmiş)

| # | Kapsam dışı konu | Nereye gider | Dayanak |
|---|---|---|---|
| X3 | Ölçüm eşiklerinin ürün kalite kapısı olarak uygulanması | K019-testing | anayasa §10/3 · bagimliliklar §4/5 |
| X4 | Sinyal şekillendirme (EQ/filtre/reverb) | K004-dsp | anayasa §2 |
| X5 | Cihazın boot-time kendi yazılımı | K005-firmware | anayasa §2 |
| X6 | Devre/PCB tasarımı | K002-electronics | anayasa §2 |
| X7 | Sürücü kodu | K000-isletim-sistemi | anayasa §10/2 |
| X8 | Ses motoru | K003-audio | anayasa §2 |
| X9 | Tür sınıflandırması | donanaım-type | anayasa §3/50 |
| X10 | Ham erişim | donanım-kernel | anayasa §3/47 |
| X11 | Çağrı seti | donanım-api | anayasa §3/49 |

---

## §15 Resmi kaynaklar (Genişletilmiş)

| # | Kaynak | Neden | Durum |
|---|---|---|---|
| R1 | https://www.ti.com/product/LM5122 | §5/E6 · §10 | ✅ HTTP 200 |
| R2 | https://www.ti.com/lit/ds/symlink/lm5122.pdf | §5/E7 · §10 | ✅ HTTP 200 |
| R3 | https://www.onsemi.com/pdf/datasheet/mjl21193-d.pdf | §5/E8 · §11 | ✅ HTTP 200 |
| R4 | https://www.onsemi.com/products/discrete-power-modules/audio-transistors/mjl21194 | §5/E9 · §11 | ✅ HTTP 200 |
| R5 | https://www.dmtf.org/ | frontmatter evidence (DMTF = sağlık/yonetim standardı org.) | ✅ HTTP 200 |

> **R5 notu:** DMTF (Distributed Management Task Force) frontmatter `evidence` listesinde
> yer alır; ancak bu dosyada DMTF standardına ait **hiçbir iddia taşınmaz** — yalnız
> kaynak varlığı kayıtlıdır. Kullanım için **Q-C7 (yeni):** DMTF (CIM/Redfish) K001
> envanterinde kullanılacak mı? **UNKNOWN** — sahibi Vault Steward.

---

## §16 Kapsam genişletme özeti (Genişletilmiş)

| Bölüm | Öğe sayısı | UNKNOWN | ⚠️ | Kanıt |
|-------|-----------|---------|----|----|
| §7 Envanter toplama | 7+6+4 | 17 | 0 | ELI15 + genel kavram |
| §8 Kapasite/ölçü | 10+5+4 | 13 | 0 | §5/E6-E9 + genel kavram |
| §9 Sağlık verisi | 6+3+3 | 12 | 0 | ELI15 + genel kavram |
| §10 LM5122 | 9+2+4 | 6 | 0 | §5/E6-E7 |
| §11 MJL transistör | 8+4 | 1 | 0 | §5/E8-E9 |
| §12 core↔kernel sınırı | 5+3+7 | 15 | 0 | anayasa §7 Q3 |
| §13 core↔type sınırı | 3 | 3 | 0 | anayasa §3/50 |
| §14 Kapsam dışı | 9 | 0 | 0 | anayasa |
| §15 Resmi kaynak | 5 | 1 | 0 | HTTP 200 |

**Sonuç:** donanım-core kapsamı kademe-3 omurgasını (envanter toplama · kapasite/ölçü ·
sağlık verisi · güç bileşeni envanteri) kapsar. CoreMusic'e özgü hiçbir parametre
uydurulmamıştır; tüm spesifik değerler UNKNOWN veya ⚠️ olarak işaretlidir. **Q-C1 (Q3)
sınır kararı** gelmeden §12'deki S1-S3 seçeneklerinden **hiçbiri seçilmez**; Q-C2 gelmeden
§2/A6 satırı UNKNOWN kalır; Q-C3 gelmeden §9'daki sağlık ölçümleri **listelenmez**.

---

## §17 Ölçüm ve doğrulama notları (2026-10-13)

| # | Ölçüm | Değer | Not |
|---|---|---|---|
| M1 | `.ai/electronic/**` md dosyası | 60 | Taze sayım |
| M2 | `.ai/electronic/**` toplam satır | 20.711 | 2026-10-12 ölçümü 18.980 idi — fark +1.731 |
| M3 | composer.json envanteri | 5 (`shared` · `media` · `api` · `home` · `auth`) | ci.yml yorumu "3" diyor — çelişki §8/C1 |
| M4 | `api.coremusic.net/composer.json:15` | `"php": ">=8.4",` | Read ile doğrulandı |
| M5 | `home.coremusic.net/composer.json:15` | `"php": ">=8.4",` | Read ile doğrulandı |

> **M2 notu:** 18.980 → 20.711 farkı **sessizce düzeltilmez**; iki ölçüm de kayıtlıdır.

---

## §18 Bilgi kaynakları hiyerarşisi

| # | Kaynak | Seviye | Kullanım |
|---|---|---|---|
| H1 | `.ai/architecture/K001-donanım/**` | 1 — SSOT | §1-§6, §12-§14 |
| H2 | `.ai/electronic/**` (60 / 20.711) | 2 | §17 |
| H3 | Resmi üretici (ti.com · onsemi.com) | 3 | §5, §10, §11 |
| H4 | Genel donanım mühendisliği | 4 | §7-§9 (genel-kavram) |
| H5 | Model hafızası | 5 | Kullanılmadı |

---

## §19 Açık sorular özeti

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-C1 | core ↔ kernel sınırı (Q3) | ⚠️ | Vault Steward |
| Q-C2 | core depolamayı sahiplenir mi? | **UNKNOWN** | Vault Steward |
| Q-C3 | Sağlık verisi hangi ölçümler? | **UNKNOWN** | audio-hw |
| Q-C4 | LM5122/MJL bu envanterde mi? | ⚠️ | audio-hw |
| Q-C5 | Envanter toplama zamanlaması? | **UNKNOWN** | Vault Steward + embedded |
| Q-C6 | Ölçü doğruluğu/kalibrasyonu? | **UNKNOWN** | audio-hw |
| Q-C7 | DMTF (CIM/Redfish) kullanılacak mı? | **UNKNOWN** | Vault Steward |

---

## §20 Devir kaydı

**Devir:** Bu dosya 2026-10-13 tarihinde Vault Steward'a devredilmiştir. Q-C1'den Q-C7'ye
kadar tüm açık sorular **UNKNOWN** veya **⚠️** olarak işaretlidir; karar sonrası yalnız
ilgili satır güncellenir, tarihçe korunur.

---

## §21 Kapsam içi yasaklar

| # | Yasak | Gerekçe | Dayanak |
|---|---|---|---|
| Y1 | `TBD` / `// ...` / `{...}` / boş hücre | Doldurucu (Guardrail #6) | `donanım-dogrulama.md` §y |
| Y2 | Uydurma ölçü değeri (SNR/THD+N/W/V/A) | Zero-hallucination §3 | kök `AGENTS.md` §3 |
| Y3 | Register adresi/değeri yazmak | K005-firmware / donanım-kernel | anayasa §3/47 |
| Y4 | Ölçüm eşiklerini core'da uygulamak | K019-testing | anayasa §10/3 |
| Y5 | Pin-for-pin şema / PCB çizimi | K002-electronics | anayasa §2 |
| Y6 | BOM fiyatı / tedarikçi listesi | K020-comilance | anayasa §2 (sınır ⚠️) |
| Y7 | `donanaım-type` yazımını düzeltmek | v2 §3 KURAL 1 | anayasa §3/60-61 |
| Y8 | Class AB topolojisini tartışmak | Sabit | kök `CLAUDE.md` |
| Y9 | core ↔ kernel sınırını tek başına çözmek | Q3 kararı Vault Steward'ın | anayasa §7 |

---

## §22 Kapsam ↔ komşu düğümler tutarlılık matrisi

| # | Komşu | İlişki | Kapsam satırı | Tutuyor mu? |
|---|---|---|---|---|
| T1 | donanım-kernel | core toplar, kernel okur | §3/B1 · §4/C1 | ✅ — anayasa §3/47-48 |
| T2 | donanım-api | core toplar, api çağırır | §3/B2 · §4/C2 | ✅ — anayasa §3/49 |
| T3 | donanaım-type | core merkez, type tür-alt-bağlamı | §3/B3 · §4/C3 · §13 | ✅ — anayasa §3/50 |
| T4 | K000-isletim-sistemi | core K001'de, sürücü K000'de | §14/X7 | ✅ — anayasa §10/2 |
| T5 | K005-firmware | core kalıcı, firmware boot-time | §14/X5 | ✅ — anayasa §2 |
| T6 | K003-audio / K004-dsp | core donanım verisi, ses motoru yazılım | §14/X4, X8 | ✅ — anayasa §2 |
| T7 | K019-testing | core toplar, testing uygular | §3/B4 · §8.2/K1-K2 | ✅ — anayasa §10/3 |
| T8 | K002-electronics | core envanter verisi, K002 devre tasarımı | §14/X6 | ✅ — anayasa §2 |

**§22 sonucu:** 8 komşu düğümle **çelişki yoktur**; her ilişki anayasa satırıyla
desteklenmiştir.

---

## §23 Sorumluluk ve devir matrisi

| # | Konu | Sorumlu ajan | Durum |
|---|---|---|---|
| R1 | Q-C1 core↔kernel sınırı (Q3) | Vault Steward | AÇIK |
| R2 | Q-C2 depolama sahipliği | Vault Steward | AÇIK |
| R3 | Q-C3 sağlık ölçümleri listesi | audio-hw | AÇIK |
| R4 | Q-C4 güç bileşeni sahipliği | audio-hw | AÇIK |
| R5 | Q-C5 toplama zamanlaması | Vault Steward + embedded | AÇIK |
| R6 | Q-C6 ölçüm doğruluğu | audio-hw | AÇIK |
| R7 | Q-C7 DMTF kullanımı | Vault Steward | AÇIK |
| R8 | Envanter şeması (DB) | data | AÇIK |
| R9 | Ölçüm kabul kapısı (SNR/THD+N) | K019-testing | SABİT |

**§23 sonucu:** 9 sorumluluktan **8'i açık**, 1'i sabittir (R9 — anayasa §10/3).
Hiçbir açık satır bu dosyada **doldurulmamıştır**; tahmin yazılmamıştır.

---

## §OS × DİL ERİŞİM MATRİSİ (core)

> **Kapsam:** Bellek/disk/sistem bilgisi katmanı — toplam RAM, disk kök listesi,
> sistem bilgi çağrıları. Yalnız kanıtlanmış arayüzler; uydurma API yok.

### §OSD.1 Erişim matrisi (13 dil × 3 OS)

| Dil | Windows | macOS | Linux |
|---|---|---|---|
| JavaScript/Node | Disk `Get-PSDrive` çocuk süreç (§G1.2) · bellek `os.totalmem()`/`os.freemem()` (§G1.3) | Disk `/Volumes` fs okuma (§G1.2) · bellek `os.totalmem()` (§G1.3) | Disk `/proc/mounts` fs (§G1.2) · bellek `os.totalmem()` (§G1.3) |
| C | `GetLogicalDrives` + `GlobalMemoryStatusEx` (Win32, §G2.2) | `uname` (POSIX) + `sysctl` ⚠️ (§G2.4 · §K4 R1) | `/proc` + sysfs (§G2.3) |
| Python | Disk kökleri §G3.2 · `ctypes` + `GlobalMemoryStatusEx` (§G3.4) ⚠️ URL'siz | Disk kökleri §G3.2 · `sysctl hw.memsize` ⚠️ (§G3.4 · R1) | `/proc/meminfo` (§G3.3) + disk kökleri §G3.2 |
| Go | `SystemDrive` + ADR ile tam liste (§G4.4) | `/` + `/Volumes/*` (§G4.4) | `/proc/mounts` (§G4.4) + `/proc/meminfo` (§G4.2) |
| Rust | §G5.2 `sistem_surumu` — std disk/bellek **içermez** ⚠️ FFI/ADR bekliyor (§G5.3) | §G5.2 `volume_kokleri` — aynı sınır (§G5.3) | §G5.2 `bellek_satirlari` · `/proc` satır ayrımı |
| C# | `DriveInfo.GetDrives` + `GlobalMemoryStatusEx` P/Invoke (§G6.4) | `DriveInfo.GetDrives` · bellek IOKit ⚠️ üye adı doğrulanmadı (§G6.4) | `DriveInfo.GetDrives` + `/proc/meminfo` (§G6.4) |
| PHP | `disk_total_space` aday taraması (§G7.2) · bellek PowerShell (§G7.3) | `disk_total_space` aday taraması (§G7.2) · `sysctl` ⚠️ (§G7.3 · R1) | `disk_total_space` aday taraması (§G7.2) · `/proc/meminfo` (§G7.3) |
| Bash | Git Bash/MSYS2 `uname -s` çıktısı ⚠️ doğrulanamadı (§G8.1) | `sysctl -n hw.memsize` ⚠️ (§G8.1 · R1) | `awk` + `/proc/meminfo` (§G8.1) |
| PowerShell | `DriveInfo::GetDrives` + `RuntimeInformation` (§G8.2) | Algılama `[OSPlatform]::OSX` — disk/bellek şablonu yok (§G8.2) | Algılama `[OSPlatform]::Linux` — disk/bellek şablonu yok (§G8.2) |
| Java | Disk/bellek şablonu belgelenmedi — yalnız `os.name` algılama (§G8.3) | Aynı (§G8.3) | Aynı — `/proc` şablonu da yok (§G8.3) |
| Swift | Foundation `ProcessInfo`/`FileManager` ⚠️ üye adları doğrulanmadı (§G9.5) | Foundation `FileManager` + `/Volumes` ⚠️ (§G9.4) | Foundation + `/proc/meminfo` ⚠️ (§G9.3) |
| Kotlin | JVM yolu yalnız `os.name` algılama (§G10.6) — disk/bellek şablonu yok | Aynı JVM sınırı (§G10.6) | Native `platform.posix` + `/proc/meminfo` (§G10.6) |
| C++ | `GetLogicalDrives` + `GlobalMemoryStatusEx` (§G11.6) | `uname` (POSIX, §G11.6) · `__APPLE__` ⚠️ (§K4 R4) | `/proc/meminfo` `std::ifstream` (§G11.6) |

### §OSD.2 Öneri ve risk notları

**Öncelikli öneri (PHP/JS):** repo dili PHP/JS — bellek Node `os.totalmem()` (§G1.3),
Linux PHP `/proc/meminfo` (§G7.3); disk PHP `disk_total_space` aday taraması (§G7.2),
Node fs `/proc/mounts` · `/Volumes` · PowerShell çocuk süreç (§G1.2). Windows'ta
ayrıntılı Win32 bellek çağrısı PHP/JS'ten **yapılmaz** — çocuk süreç/alt sistem kullanılır.

**Risk notları:**
- `sysctl hw.memsize` (macOS) — Apple dokümantasyonu 404 (§K4 R1) → ⚠️ VERIFICATION REQUIRED.
- `__APPLE__` ön-tanımlı makrosu — doğrulanmış birincil URL yok (§K4 R4) → ⚠️ VERIFICATION REQUIRED.
- `getifaddrs` (macOS ağ arayüzü) — Apple arşiv sayfası 404 (§K4 R8) → ⚠️ VERIFICATION REQUIRED.
- WASAPI (Windows) · CoreAudio (macOS) · ALSA (Linux) sahiplikleri K000-isletim-sistemi ile
  paylaşılır — bu dosya yalnız çağrı şablonu gösterir (§PLATFORM MATRİSİ P5 sınır notu).

### §OSD.3 Kaynak atıfı

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
