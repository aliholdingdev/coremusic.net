---
title: "K019 PCB «ALEV» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K019-pcb/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: pcb
ssot: true
risk: medium
owner: audio-hw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K019 PCB «ALEV» — Katman Index

> **Authority:** Bu dosya K019 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K019 kartı > `.ai/CLAUDE.md` §5 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak)
> + kart kural seti: `ADR-063-hardware-design-standards` (accepted — PCB kural seti · sinyal bütünlüğü
> & EMC · AES17 fabrika test hizası).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** PHYSICAL PLANE (anayasa §A.0: `K019 · PCB · «ALEV» · PHYSICAL` · ADR-096 §2.2).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K019 |
| Kanonik Ad | PCB |
| Teatral Epitet | «ALEV» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | PHYSICAL (ADR-096 §2.2 — PHYSICAL = K016→K020) |
| Dizin deseni | `.ai/architecture/K019-pcb/index.md` (R2.2 — dizin henüz üretilmedi, ls 2026-10-08) |
| Tier / Domain | 3 / pcb |
| Owner (`.ai/AGENTS.md` §4 registry) | audio-hw (Audio Hardware Engineer — `audio-hw`, AGENTS.md L91) |
| Risk | medium — kart tasarımı diğer fiziksel katmanların (K016-K018) taşıyıcısıdır; hatası dolaylı etki yaratır (termal/EMC/empedans), ancak doğrudan enerji-yükü ve koruma kararları K017/K018'dedir → medium |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K019 PCB «ALEV», CoreMusic'in baskılı-devre katmanıdır: 6-layer stackup, empedans-kontrollü
hatlar, thermal vias, star ground, ENIG finish ve 2oz copper anayasa §5 K19 satırında bağlayıcıdır
(kısıt: "ENIG finish, 2oz copper"); kart-boyut/klas referansı 200×100mm · IPC Class 3 · 90Ω USB /
50Ω I2S'tir (§5 H4 satırı). K019, K016 (kanal-bağımsız kart), K017 (bar/akım yolları) ve K018
(thermal vias) katmanlarının **fiziksel taşıyıcısıdır**; içeriği yalnız geometri/kural seti olup
iş verisi üretmez. **PHYSICAL PLANE** olduğundan yazılım katmanlarına **senkron bağlantısı YOKTUR**
(ADR-096 §2.2 GÖREV 07) — tasarım-dosyaları (KiCad/EAGLE) ve üretim çıktısı K020'ye gider. Repo
durumu: `find` `*.kicad_pcb/*.brd/CMakeLists/platformio` → **0 sonuç** (2026-10-08) → DESIGN/PLANNED
(R16.3); ayrıca `brain.md` Electronics Registry'teki `electronics/pcb-classab.md` yolu **diskte
YOK** (§4.5 · §7.1 G3).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K019 | PCB | «ALEV» | pcb | tasarım-aracı + fabrika süreci (KiCad/EAGLE kaynak yasak-notu: ADR-089 §1.3 — AGPL/uyumsuz lisanslı donanım dosyası kopyalanmaz) · CAD dosyası repo'da YOK (find 0, 2026-10-08) | Analog/Digital/Power Section · Clocking · Grounding · Signal Integrity · EMI/EMC · Routing (EK A §A.1 · 50 bileşen — anayasa §5 K19) | şematik/bileşen yerleşimi (K016/K017/K018 gereksinimleri) · empedans şartları (90Ω USB · 50Ω I2S) · termal/EMC kuralları (ADR-063) | 6-layer stackup kart geometrisi · ENIG finish + 2oz copper · star ground ağı · thermal vias dizisi · empedans-kontrollü hatlar · üretim çıktısı (K020'ye) | K000-K018 (EK A aralık) + port/adapter — **PHYSICAL uç: yazılıma yalnız sürücü/API/olay, senkron yok** (ADR-096 §2.2); fiziksel hizmet: K016/K017/K018'e taşıma-zemin-tepki | K019 → K020 sağ/üst erişim (H20) · **K015/K009 yazılımı ile SENKRON bağ** · H19 veri paylaşımı · empedans/grounding şartlarının ihlali (kural seti) · AGPL/uyumsuz lisanslı CAD kaynağı kopyalama (ADR-089 §1.3) · K016-K018 iç devrelerine müdahale | yalnız tasarım-geometri verisi (stackup · empedans · yerleşim · via · kural-ihlal kaydı); iş/katalog verisi PAYLAŞMAZ; üretim çıktısı/geri-izlenebilirlik K020'ye | EMC/EMI sinyal-bütünlüğü (parazit/örtüşme önleme — §5 K1 kısıtı ile hizalı) · star ground ile toprak-pliti/çınlama kontrolü · lisans kısıtı (AGPL/uyumsuz CAD kaynağı yasak — ADR-089 §1.3) · üretim-kalitesi IPC Class 3 (K020 QC) | tasarım-hataları: empedans-uyumsuzluk → yansıma/EMI · ground-pliti/loop → gürültü · thermal-vias yetersizliği → sıcak-nokta (K018'e devredilir) · üretim-defosu → K020 QC reddi · (kart-yeniden-revizyon döngüsü) | DRC/ERC raporları (tasarım-kural denetimi) · üretim-test kayıtları (K020 AES17) · EMC-ölçüm raporları (PLANNED) · geri-izlenebilirlik (part/rev — K020) | **Hedef test tanımları (yazılmadı):** empedans ölçümü (90Ω/50Ω) · DRC/ERC temiz · EMC/EMI ölçümü · termal-vias/geçiş direnci · star-ground sürekliliği · IPC Class 3 muayene · revizyon/geri-izlenebilirlik doğrulaması |
| KANIT | vault: `.ai/CLAUDE.md §5 K19 + §5 H4 + §5 K1 kısıtı` + `00-kspace-anayasa.md §A.1 K019` + `brain.md §5 (pcb-classab.md kaydı)` · ADR: ADR-061/063/064/089 (accepted/ ls) · repo: CAD/`*.cpp` → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K019 |
| 2 | KANONİK_AD | PCB |
| 3 | TEATRAL_EPİTET | «ALEV» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | pcb |
| 5 | SUBDOMAIN | stackup · sections-analog-digital-power · clocking · grounding · signal-integrity · emi-emc · routing · finish-copper |
| 6 | BOUNDED_CONTEXT | Baskılı Devre Geometrisi — kartın kural-uyumlu çizimi; bileşen seçimi/ölçüm/koruma kararı üretmez, iş verisi taşımaz |
| 7 | RUNTIME | PCB tasarım-aracı akışı (CAD) + fabrika süreçleri (ENIG · 2oz · IPC Class 3) · CAD kaynak dosyası repo'da YOK (find 0, 2026-10-08) · lisans kısıtı: AGPL/uyumsuz donanım dosyası kopyalanmaz (ADR-089 §1.3) |
| 8 | SORUMLULUK | Analog/Digital/Power Section · Clocking · Grounding · Signal Integrity · EMI/EMC · Routing (EK A §A.1 K019) · **ENIG finish · 2oz copper** (§5 K19 kısıtı) · 50 bileşen (anayasa §5 K19) |
| 9 | GIRDI | K016 kanal-kartı yerleşim gereksinimleri (bağımsız PCB — ADR-089 §1.1 aktarımı) · K017 akım-yolu/bar şartları · K018 thermal-vias/ısıl şartlar · ADR-063 kural seti · empedans hedefleri (90Ω USB · 50Ω I2S — §5 H4) |
| 10 | CIKTI | 6-layer stackup · ENIG + 2oz bakır · star ground ağı · thermal vias · empedans-kontrollü hatlar · üretim çıktısı (Gerber/BOM — K020'ye) · DRC/ERC raporu |
| 11 | IZINLI_BAGIMLILIK | K000-K018 aralık (EK A §A.1 "izinli=K000-K018") + port/adapter; somut uçlar: K016 (kart-ihtiyacı), K017 (akım/bar), K018 (ısı-iletim şartı), K001 (bileşen-yerleşim), K002 (saat/kesme-hattı etkileşimi), K000 (tasarım/üretim-toolchain runtime'ı) |
| 12 | YASAK_BAGIMLILIK | K019 → K020 sağ/üst katmanlara doğrudan erişim (H20) · **K015/K009/K010 yazılımı ile SENKRON BAĞ** (ADR-096 §2.2) · H19 veri paylaşımı · empedans/grounding kural ihlali · AGPL/uyumsuz lisanslı CAD kaynağı kopyalama (ADR-089 §1.3) · K016-K018 iç devre kararlarına müdahale · K005'e veri yazımı |
| 13 | DATA_BOUNDARY | yalnız tasarım/geometri verisi: stackup · empedans · yerleşim · via · kural-ihlal kaydı · revizyon. İş/katalog verisi PAYLAŞMAZ; üretim çıktısı/geri-izlenebilirlik (part/rev) K020'ye devredilir; kalıcı DB yazımı YOK |
| 14 | SECURITY_BOUNDARY | sinyal-bütünlüğü/EMC (parazit/örtüşme önleme — anayasa §5 K1 "sinyal zincirine parazit yasak" ile hizalı) · star ground ile gürültü/çınlama kontrolü · lisans güvenliği (AGPL/uyumsuz CAD kaynağı yasak) · üretim kalitesi/geri-izlenebilirlik K020 QC'ye (IPC Class 3) |
| 15 | FAILURE_MODE | empedans-uyumsuzluk → yansıma/EMI bozulması · ground-loop/plit → gürültü (ses kalitesi) · thermal-vias yetersiz → sıcak-nokta (K018 cutoff'a kadar süre) · akım-yolu ince → ısınma · üretim-defosu → K020 QC reddi · kart-revizyonu (rev bump) ile kurtarma |
| 16 | OBSERVABILITY | DRC/ERC raporları · EMC/ölçüm raporları (PLANNED) · üretim-test kayıtları (K020 AES17) · geri-izlenebilirlik (part/rev — K020) · tasarım-revizyon geçmişi |
| 17 | TEST | **Hedef test tanımları (yazılı değil):** empedans ölçümü (90Ω USB · 50Ω I2S) · DRC/ERC temiz geçiş · EMC/EMI ölçümü · thermal-vias geçiş direnci · star-ground sürekliliği · IPC Class 3 görsel/muayene · termal-geçiş (K018 ile ortak) · revizyon/geri-izlenebilirlik doğrulaması |
| 18 | KANIT | vault: `.ai/CLAUDE.md §5 K19 satırı (6-layer stackup · impedance matched · thermal vias · star ground · ENIG · 2oz · 50 bileşen · kısıt: ENIG finish, 2oz copper)` + §5 H4 satırı (200×100mm · IPC Class 3 · 90Ω USB · 50Ω I2S) + §5 K1 (parazit yasağı) · `00-kspace-anayasa.md §A.1 K019` · `brain.md §5` (pcb-classab.md kaydı: 6-layer rules) · ADR: ADR-061/063/064/089 (accepted/ ls) · repo: CAD/find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |
| 19 | KANIT_TARIHI | 2026-10-08 (vault read + accepted/ ls + repo find) |
| 20 | EPİTET_KALİTE_NOTU | «ALEV» — ateş/kızgınlık metaforu: ısının/bakırın taşındığı yüzey; 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K019 · PCB · «ALEV» · PHYSICAL` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI'daki K016-K018 kayıtları
fiziksel şart/hizmet yönünde (ihtiyaç → geometri); YASAK'taki "K016-K018 iç devre kararlarına
müdahale" bu katmanların münhasır iç-kararıdır; YASAK'taki K015 kaydı **senkron yazılım bağı** →
koşul farklı → kesişim ∅; H20 aralığı K020 IZINLI aralığının dışında) · (c) KANIT 3'lü format —
GEÇTİ (vault/ADR | repo 0 | web ⚠️) · (d) veri sınırı tek katmana ait (geometri/kural; üretim kaydı
K020, iş verisi K005) — GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K019 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Analog/Digital/Power Section · Clocking ·
Grounding · Signal Integrity · EMI/EMC · Routing".

| Kalem | Ne yapar | Durum (CAD dosyası yok → DESIGN/PLANNED — H1) | Kanıt |
|---|---|---|---|
| Analog/Digital/Power Section | kart üzerinde üç bölüm ayrımı (bölme/bölge) | DESIGN | `00-kspace-anayasa.md §A.1 K019` · brain.md §5 (pcb-classab.md: placement/EMI) |
| Clocking | saat hattı yerleşimi/dağılımı | DESIGN (detay ⚠️) | `00-kspace-anayasa.md §A.1 K019` (Clocking) · ADR-063 (kural seti) |
| Grounding | star ground (tek-nokta toprak) ağı | DESIGN | `.ai/CLAUDE.md §5 K19` (star ground) |
| Signal Integrity | empedans-kontrollü hatlar (90Ω USB · 50Ω I2S) | DESIGN | `.ai/CLAUDE.md §5 K19` (impedance matched) + §5 H4 (90Ω/50Ω) |
| EMI/EMC | parazit/örtüşme/şeritleme kuralları | DESIGN (kural seti ADR-063'te) | `.ai/CLAUDE.md §5 K1` (parazit yasağı) · ADR-063 |
| Routing | hat çekim kuralları (uzunluk/örtüşme/akım) | DESIGN | `00-kspace-anayasa.md §A.1 K019` · ADR-063 |

### §4.2 Anayasa §5 K-Matrix Satırı (K19) — 50 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K19 PCB Tasarım | 6-layer stackup, impedance
matched, thermal vias, star ground | 50 bileşen | ENIG finish, 2oz copper.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| 6-layer stackup | 6 katmanlı katman-sırası (güç/zemin/sinyal dağılımı) | DESIGN | `.ai/CLAUDE.md §5 K19` + §5 H4 (6-layer) · brain.md §5 (pcb-classab.md) |
| Impedance matched | empedans-kontrollü hatlar (90Ω USB · 50Ω I2S) | DESIGN | `.ai/CLAUDE.md §5 K19` + §5 H4 |
| Thermal vias | ısıl iletim via'ları (ısıyı alt katmanlara/yüzeye taşıma) | DESIGN | `.ai/CLAUDE.md §5 K19` · K018 entegrasyonu (§4.3.5) |
| Star ground | tek-nokta topraklama (plit/loop önleme) | DESIGN | `.ai/CLAUDE.md §5 K19` |
| ENIG finish + 2oz copper (kısıt) | yüzey bitişi + kalın bakır (akım/termal) | BAĞLAYICI (anayasa kısıtı) | `.ai/CLAUDE.md §5 K19` kısıt sütunu |
| "50 bileşen" sayımı | §5 K19 bileşen sayısı (envanter — HEDEF) | HEDEF · alt-döküm ⚠️ | `.ai/CLAUDE.md §5 K19 satırı` · H10: hedef ≠ kanıt (repo 0) |

### §4.3 Alt-Sistem Bazlı Derinlik (10 kalem)

#### §4.3.1 Stackup · 6 Katman

| Boyut | İçerik |
|---|---|
| Kural | 6 katman: sinyal/zemin/güç dağılımı (anayasa §5 K19 — 6-layer stackup) |
| Durum | DESIGN (katman-sırası detayı ⚠️ — ADR-063 kural seti içinde) |
| Edge case | zemin-kesintisi (signal-devre) · güç-zemin çifti (decoupling) · katman-ısıl-iletimi (thermal via ile) |
| Ölçüt | DRC/ERC temiz · empedans hedefi (§4.3.3) |
| Kanıt | `.ai/CLAUDE.md §5 K19 + §5 H4` · brain.md §5 (pcb-classab.md kaydı) · ADR-063 (accepted/ ls) |

#### §4.3.2 Bakır & Yüzey · 2oz + ENIG

| Boyut | İçerik |
|---|---|
| Kural | **kısıt: ENIG finish · 2oz copper** (§5 K19 kısıt sütunu — bağlayıcı) |
| Durum | BAĞLAYICI/DESIGN |
| Edge case | 2oz ↔ ince-hat çözünürlüğü (üretim kabiliyeti) · ENIG kalınlığı/kontak-direnç · düzlem-sıcaklık (lehim) |
| Kapsam | K017 akım-yolları (2oz = akım kapasitesi), K016 kanal kartı |
| Kanıt | `.ai/CLAUDE.md §5 K19` (ENIG, 2oz) + §5 H4 (2oz copper) |

#### §4.3.3 Empedans · 90Ω USB / 50Ω I2S

| Boyut | İçerik |
|---|---|
| Kural | hedef empedanslar: **90Ω USB · 50Ω I2S** (§5 H4) — "impedance matched" (§5 K19) |
| Durum | DESIGN (ölçüm-hedefi §6.4) |
| Edge case | konektör/geçiş noktalarında empedans-sürekliliği · katman-geçme (via) yansıması · I2S yüksek-hızlı saat |
| İlişki | I2S hattı K001/K002 (XMOS/codec) arası; USB hattı K001 (XMOS XU316 USB Audio Class 2.0 — brain.md §8) |
| Kanıt | `.ai/CLAUDE.md §5 H4` · `.ai/CLAUDE.md §5 K19` · brain.md §8 (XMOS USB Audio Class 2.0) |

#### §4.3.4 Topraklama · Star Ground

| Boyut | İçerik |
|---|---|
| Kural | tek-nokta topraklama: güç-gündem-sinyal-nötr ayrımı, ortak-nokta (star) |
| Durum | DESIGN |
| Edge case | döngü (loop) oluşumu · dijital-akımın analog-zemine karışması · bağlantı-noktası-direnç |
| Etki | gürültü/çınlama → SNR/THD etkisi (K016 ölçütleri §4.3.9 K016 dosyası) |
| Kanıt | `.ai/CLAUDE.md §5 K19` · ADR-063 (sinyal bütünlüğü & EMC — accepted/ ls) |

#### §4.3.5 Termal · Thermal Vias (K018 ile ortak)

| Boyut | İçerik |
|---|---|
| Kural | ısıyı bakır-ile-imdadiyeten alt/yüzeye taşıyan via dizileri (§5 K19) |
| Durum | DESIGN (gerekli via-yoğunluğu ⚠️) |
| Edge case | via-yoğunluğu ↔ akım-yolu çatışması · sıcak-nokta (K018'e devir) · kapatma-toleransı (fill/tent) |
| İlişki | K018 ısı şartını verir, K019 geometriyi çizer; ölçüm K020/K018 testi |
| Kanıt | `.ai/CLAUDE.md §5 K19 (thermal vias) + K18` · ADR-063 |

#### §4.3.6 Bölümler · Analog / Digital / Power Ayrımı

| Boyut | İçerik |
|---|---|
| Kural | üç bölgenin fiziksel ayrımı (karışım önleme) — EK A "Analog/Digital/Power Section" |
| Durum | DESIGN (bölge-çizgisi detayı ⚠️) |
| Edge case | güç-bölümü ısı/sinyal yayılımı · dijital saat gürültüsünün analog çıkışa sızmaması |
| Kapsam dışı | bileşen-seçimi (K001/K016), ısıl-model (K018) |
| Kanıt | `00-kspace-anayasa.md §A.1 K019` · brain.md §5 (pcb-classab.md: placement) |

#### §4.3.7 Saat · Clocking

| Boyut | İçerik |
|---|---|
| Kural | saat kaynağı/dağılımı yerleşimi (kısa-dönüşlü, korumalı hat) |
| Durum | DESIGN (kaynak/gerilim ⚠️ — vault'ta yok) |
| Edge case | saat-irtibatı → EMI · I2S BCLK/LRCK hizası (K002 sürücü - K001 codec) |
| İlişki | ASIO/48kHz zinciri (anayasa §19) ile kare-uyumu |
| Kanıt | `00-kspace-anayasa.md §A.1 K019 (Clocking)` · `.ai/CLAUDE.md §19` |

#### §4.3.8 EMC · EMI/EMC Kuralları

| Boyut | İçerik |
|---|---|
| Kural | parazit/örtüşme/şeritleme/serbest-alan kuralları — ADR-063 kural seti |
| Durum | DESIGN (kural seti var — uygulama/denetim yok) |
| Edge case | dış-şeritleme/konnektör-filtresi · harmonik/şerit-gürültüsü · sertifika gereksinimi ⚠️ |
| İlişki | §5 K1 kısıtı "sinyal zincirine parazit yasak" |
| Kanıt | ADR-063 (accepted/ ls — başlık: sinyal bütünlüğü & EMC) · `.ai/CLAUDE.md §5 K1` |

#### §4.3.9 Çekim · Routing Kuralları

| Boyut | İçerik |
|---|---|
| Kural | hat çekim kuralları: uzunluk/örtüşme/akım-genişliği/via politikası (ADR-063 + brain.md kaydı) |
| Durum | DESIGN (kural metni ADR-063/pcb-classab.md'de — CAD uygulaması yok) |
| Edge case | yüksek-akım hatları (2oz ile) · differential çiftler (USB) · via-sayısı/kısıtlama |
| Kanıt | ADR-063 (accepted/ ls) · brain.md §5 (pcb-classab.md kaydı) |

#### §4.3.10 Mekanik & Kalite · 200×100mm · IPC Class 3 · Revizyon

| Boyut | İçerik |
|---|---|
| Kural | kart boyutu 200×100mm (§5 H4) · üretim sınıfı **IPC Class 3** (§5 H4) · kanal-bağımsız kart (ADR-089 §1.1 aktarımı) |
| Durum | DESIGN (boyut/kalite hedefi) · revizyon-izleme K020 |
| Edge case | kasa-uyumu/montaj delikleri ⚠️ · varyant-boyutu (ADR-090 mono…8+1) |
| Lisans | AGPL/uyumsuz lisanslı donanım CAD kaynağı kopyalanmaz (ADR-089 §1.3) |
| Kanıt | `.ai/CLAUDE.md §5 H4` · ADR-089 §1.3 + §1.1 · ADR-090 (accepted/ ls) |

### §4.4 Kapsam Dışı / Sınır Tanımı (K019'un YAPMADIĞI)

| Aday konu | Neden K019 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| Bileşen seçimi/satınalma | üretim/BOM katmanı | K020 MANUFACTURING | `.ai/CLAUDE.md §5 K20` |
| Termal-model/kesme eşiği | termal katman | K018 THERMAL | `.ai/CLAUDE.md §5 K18` |
| Güç korumaları/koruma-devresi | güç katmanı | K017 POWER | `.ai/CLAUDE.md §5 K17` |
| Amplifikatör devre-topolojisi | yükseltme katmanı | K016 AMPLIFIER | `.ai/CLAUDE.md §5 K16` |
| Üretim-test/ölçüm kaydı | üretim katmanı | K020 (AES17 — ADR-063 hizası) | ADR-063 (accepted/ ls) |
| CAD dosyasının (kaynak) lisansı-depolanması | lisans/kaynak politikası | ADR-089 §1.3 + K020 | ADR-089 (accepted/ ls) |

### §4.5 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K19 hedefi | 50 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (find 2026-10-08) | `*.kicad_pcb/*.brd/Gerber` + `*.cpp/*.c` + `CMakeLists.txt` → **0** |
| Karar kanıtı | ADR-063 (accepted — PCB kural seti) · ADR-061/064/089 (accepted) — ls 2026-10-08 |
| Durum dağılımı | DESIGN: 10 kalem (stackup · bakır/finish · empedans · grounding · termal · bölümler · clocking · EMC · routing · mekanik/kalite) · BAĞLAYICI: 2 (ENIG+2oz kısıtı · IPC Class 3) · IMPLEMENTED: 0 |
| Kırılgan kaynak | `brain.md` Electronics Registry `electronics/pcb-classab.md` yolu **diskte YOK** (ls 2026-10-08) → §7.1 G3; kural-metninin teyit edilmiş tek fiziksel kaynağı ADR-063 |
| Ölçüm/kanıt eksikliği | empedans/EMC/termal-geçiş ölçümleri (hedef test — yazılmadı) |
| Web research | 0 URL bu dosyada → ⚠️ (R9 3'lü eksik; F1 EK B kapısı) |

### §4.6 K019 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K019 etkisi |
|---|---|---|
| ADR-061-electronics-architecture | Electronics Architecture (L6) — katman/kart/modül hiyerarşisi · bileşen seçim politikası | kart-hiyerarşisi/ölçek ilişkisi |
| ADR-063-hardware-design-standards | **PCB Kural Seti · Sinyal Bütünlüğü & EMC · Bileşen/Kart Kalitesi · Dokümantasyon Standardı · AES17 Fabrika Test Hizası** | **ana karar** — K019'un bağlayıcı kural seti + K020 test hizası |
| ADR-064-electronics-platform-architecture | Electronics Platform (L0-L6 · 5 cihaz sınıfı · cihaz↔servis eşleme) | cihaz-sınıfına göre kart konfigürasyonu |
| ADR-089-classab-24v | Class AB Amplifikatör Sistemi (8×50W · hibrit MCU) — §1.1 kanal-bağımsız PCB · §1.3 lisans kısıtı | kanal-kartı geometrisi + lisans yasağı |
| ADR-090-channel-variant-product-family | Kanal varyant ürün ailesi (mono…8+1) | varyant-başına kart boyutu/kapsamı |
| ADR-096-kspace-5000-boundary-model | K-Space V2 · dizin deseni · çift uçak (§2.2) | PHYSICAL plane kuralı + format kaynağı |

### §4.7 K019 Arayüz Sözleşmeleri (komşularla sınır — çift uçak)

| Komşu | Arayüz | K019'un verdiği | K019'un beklediği | Kanıt |
|---|---|---|---|---|
| K016 AMPLIFIER | kart geometrisi | kanal-bağımsız PCB (6-layer) | yerleşim/iş akımı/ısı şartları | `.ai/CLAUDE.md §5 K19` · ADR-089 §1.1 |
| K017 POWER | akım-yolu/bakır | 2oz + geniş akım hatları | bar/akım yük şartları | `.ai/CLAUDE.md §5 K19/K17` |
| K018 THERMAL | thermal vias/ısıl-iletim | ısı-iletim geometrisi | ısı yükü/hot-spot şartı | `.ai/CLAUDE.md §5 K19/K18` |
| K001 HARDWARE | konnektör/bileşen yerleşimi | montaj/delik/kart alanı | pinout/yerleşim | `.ai/CLAUDE.md §5 K1` |
| K002 DRIVERS | saat/kesme hattı | clocking/kesme hatları | I2S/USB uyumu (50Ω/90Ω) | `.ai/CLAUDE.md §5 H4` |
| K020 MANUFACTURING | üretim çıktısı/ölçüm | Gerber/BOM/DRC çıktısı · part/rev | IPC Class 3 + AES17 test hizası | ADR-063 (accepted/ ls) |
| K015/K009 (yazılım) | **olay/sürücü-API yalnız (senkron YOK)** | tasarım/rev olayları | komut kanalı (senkron bağ yasak) | ADR-096 §2.2 · R6.1 |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K019 → K000 OS | aşağı | tasarım/üretim-toolchain runtime'ı | `00-kspace-anayasa.md §A.1 K019 "izinli=K000-K018"` |
| K019 → K001 HARDWARE | aşağı | bileşen/konnektör yerleşimi (pinout) | `.ai/CLAUDE.md §5 K1` |
| K019 → K002 DRIVERS | aşağı (arayüz) | saat/kesme/I2S-USB hat uyumu | `.ai/CLAUDE.md §5 H4` |
| K019 ← K016/K017/K018 | karşı-yöne gelen şartlar | yerleşim/akım/ısı şartları → geometri (K019 çizer; iç karara müdahale yok — §5.2) | `.ai/CLAUDE.md §5 K16/K17/K18/K19` |
| K015/K009 yazılım | aşağı (olay/sürücü-API) | tasarım/rev/ölçüm olayı — **senkron bağ YOK** | ADR-096 §2.2 (GÖREV 07) |
| port/adapter | yan | EK A istisnası (R6.3) | `00-kspace-anayasa.md §A.1 K019` |

**Çift uçak notu (ADR-096 §2.2 · GÖREV 07):** K019 PHYSICAL PLANE'dedir. Yazılım katmanları ile
ilişki yalnız sürücü/API/olay üzerinden; **senkron doğrudan bağlantı yasaktır** (ihlal → R6.5).

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K019 → K020 doğrudan erişim / geri çağrı (H20) | klasik yön (üst/sağ katmana erişim yok) | `rules.md R6.1` · `ADR-096 §2` |
| K015/K009/K010 ile SENKRON BAĞ | çift uçak ihlali | `ADR-096 §2.2` · `rules.md R6.1` |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali (R4.4d) | `rules.md R6.1` |
| Empedans/grounding kural ihlali (90Ω/50Ω · star ground) | sinyal-bütünlüğü/EMC bütünlüğü (§5 K19 kısıtı) | `.ai/CLAUDE.md §5 K19 + K1` |
| AGPL/uyumsuz lisanslı donanım CAD kaynağı kopyalama | ADR-089 §1.3 kısıtı | ADR-089 §1.3 (accepted/ ls) |
| K016-K018 iç devre kararlarına müdahale | katman münhasırlığı (K019 yalnız geometri) | `00-kspace-anayasa.md §A.1 K019` |
| K005'e veri yazımı / iş verisi saklama | veri sınırı: K019 yalnız tasarım/geometri | EK A K019 · H19 |
| `SELECT *` / ORM / framework (yazılım tarafında) | ADR-001/002 mutlak yasakları | `rules.md R17` |

### §5.3 Boundary Matrisi

| Boundary | K019 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | tasarım/geometri (stackup · empedans · yerleşim · via · rev · ihlal kaydı); DB yazımı yok | K020 (üretim çıktısı/geri-izlenebilirlik) · K005 (iş verisi) |
| SECURITY_BOUNDARY | sinyal-bütünlüğü/EMC (parazit yasağı) · star ground · lisans kısıtı (AGPL) · üretim kalitesi (IPC Class 3) | K001/K006 (donanım/erişim) · K020 (QC) |
| FAILURE_MODE | empedans-uyumsuzluk · ground-loop · thermal-vias yetersiz · akım-yolu ince · üretim-defosu → K020 QC reddi · revizyon ile kurtarma | K018 (termal) · K020 (QC) |
| RUNTIME boundary | CAD + fabrika süreçleri (CAD dosyası repo'da yok) | K000 (toolchain) · K020 (üretim) |
| MEASUREMENT boundary | EMC/empedans/termal-geçiş ölçümleri üretim-testinde (K020/AES17) | K020 (test) · K013 (otomasyon) |
| Olay (event) yukarı serbest | tasarım/rev/ölçüm olayları yukarı; senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay yayını, yukarı SERBEST)  ←  K019 tasarım/rev/ölçüm olayları
      ↑                                                        │
      │ (okuma)                                      [senkron K015/K009'a YASAK]
      └────────── K008 SERVICES (Event Bus) ──────────┘
                        │
  K019 yalnız alttan beslenir: K001 yerleşim · K002 saat/kesme · K016/K017/K018 şartları  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K019 → K012/ölçüm olayı | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K019 → K015/K009 senkron çağrı | — | YASAK (çift uçak + H20) | `ADR-096 §2.2` · `rules.md R6.1` |
| K016/K017/K018 → K019 şart | aşağı (fiziksel şart) | yerleşim/akım/ısı gereksinimleri | `.ai/CLAUDE.md §5 K16-K19` |
| K019 → K020 çıktı | devir (üretim çıktısı) | Gerber/BOM/DRC + part/rev | ADR-063 · §5.2 (erişim yasak — çıktı akışı sınırda) |

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K019 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K019 / stackup · grounding · SI · EMC · routing | SUBDOMAIN §3/5 |
| Module → Component | stackup modülü · zemin ağı · via dizileri · bölge-ayrımı | hedef (tasarım seviyesi) |
| Service | (donanım katmanı — servis değil; çıktısı K020'ye) | çift uçak |
| Adapter | CAD/üretim-tool adaptörü · test/ölçüm adaptörü (K020) | port/adapter sıçraması |
| Döngü toleransı | sıfır (R6.6) | `rules.md R6.6` |

### §5.6 K019 Risk & Güvenlik Notları (görev notu + anayasa hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| Empedans/EMC ihlali | ses kalitesi/EMI bozulması (§5 K1 parazit yasağı) | ADR-063 kural seti + DRC/EMC ölçümü (hedef) | `.ai/CLAUDE.md §5 K1` · ADR-063 |
| Thermal-vias yetersizliği | sıcak-nokta → K018'e kadar süre | K018 şartı + ölçüm (hedef) | `.ai/CLAUDE.md §5 K18/K19` |
| Lisans ihlali (AGPL/uyumsuz CAD kaynağı) | hukuki risk | ADR-089 §1.3 yasağı | ADR-089 §1.3 |
| Kural-metni ölü referans (pcb-classab.md diskte yok) | kanıt-zayıflığı | ADR-063 teyidi + atıf güncellemesi (⚠️ G3) | `brain.md §5` + ls 2026-10-08 |
| Ölçüm/kanıt eksikliği (empedans/EMC) | "matched" iddiasının kanıtsızlığı | hedef test tanımları (§6.4) + research (R14) | H10 · `rules.md R9` |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| Şema/alma | K016-K018 şartları + ADR-063 kuralları | netlist + bölge planı | §4.3.6 |
| Stackup | 6 katman sırası + 2oz/ENIG | katman-sırası | §4.3.1-2 |
| Routing | empedans + akım + saat şartları | hatlar/vialar | §4.3.3-4-9 |
| Termal | ısı şartları (K018) | thermal via dizileri | §4.3.5 |
| Denetim | netlist/geometri | DRC/ERC raporu | §6.3 |
| Üretim çıktısı | onaylı tasarım | Gerber/BOM/üretim dosyası → K020 | §4.7 |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Stackup | 6-layer | `.ai/CLAUDE.md §5 K19 + H4` |
| Bakır/finish | 2oz copper · ENIG (kısıt) | `.ai/CLAUDE.md §5 K19 kısıtı` |
| Empedans | 90Ω USB · 50Ω I2S | `.ai/CLAUDE.md §5 H4` |
| Boyut/klas | 200×100mm · IPC Class 3 | `.ai/CLAUDE.md §5 H4` |
| Zemin | star ground | `.ai/CLAUDE.md §5 K19` |
| CAD/araç | repo'da dosya YOK (find 2026-10-08) · araç seçimi ⚠️ (ADR-089 §1.3 lisans kısıtı ile) | repo find · ADR-089 §1.3 |
| Bileşen hedefi | 50 (envanter) | `.ai/CLAUDE.md §5 K19` |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| DRC/ERC raporu | CAD aracı | PLANNED (CAD yok) |
| EMC ölçüm raporu | laboratuvar/ölçüm | PLANNED |
| Üretim-test kaydı | K020 AES17 hattı (ADR-063) | PLANNED (K020) |
| Geri-izlenebilirlik (part/rev) | üretim kaydı (K020) | PLANNED |
| Tasarım-revizyon geçmişi | CAD sürüm kontrolü | PLANNED (depo yok) |

### §6.4 Test (Hedef Test Tanımları — "yapıldı" DEĞİL)

| # | Test | Ölçüt / Kabul | Durum |
|---|---|---|---|
| 1 | Empedans ölçümü | 90Ω USB · 50Ω I2S (§5 H4) | TANIMLI, YAZILMADI |
| 2 | DRC/ERC | temiz (hata yok) | TANIMLI, YAZILMADI |
| 3 | EMC/EMI ölçümü | sınır değer ⚠️ (vault'ta yok) | TANIMLI (ölçüt eksik) |
| 4 | Thermal-vias geçiş direnci | ⚠️ (eşik yok) | TANIMLI (ölçüt eksik) |
| 5 | Star-ground sürekliliği | direnç sınırı ⚠️ | TANIMLI (ölçüt eksik) |
| 6 | IPC Class 3 muayene | sınıf-3 kabul kriterleri | TANIMLI, YAZILMADI (ölçüt: ADR-063) |
| 7 | Termal-geçiş (K018 ile ortak) | ısı-iletim direnci ⚠️ | TANIMLI (ölçüt eksik) |
| 8 | Revizyon/geri-izlenebilirlik | part/rev eşleşmesi | TANIMLI, YAZILMADI |
| 9 | Kanal-bağımsız kart kontrolü | ADR-089 §1.1 (bağımsız PCB) | TANIMLI, YAZILMADI |
| 10 | Varyant kapsamı | ADR-090 (mono…8+1) kart varyantları | TANIMLI, YAZILMADI |

### §6.5 Failure Mode Senaryoları (failure=design/üretim hatası)

| # | Senaryo | K019 davranışı | Sistem etkisi | Kanıt |
|---|---|---|---|---|
| 1 | Empedans uyumsuzluğu | yansıma/EMI → sinyal bozulması | ses/iletişim kalitesi | `.ai/CLAUDE.md §5 K19/H4` |
| 2 | Ground-loop/plit | gürültü/çınlama | SNR/THD düşüşü | `.ai/CLAUDE.md §5 K19` |
| 3 | Thermal-vias yetersiz | sıcak-nokta → K018'e kadar süre → cutoff | kısıtlama/kapanış | `.ai/CLAUDE.md §5 K18/K19` |
| 4 | İnce akım-yolu (2oz ihlali) | ısınma/devre hasarı | güç-kesintisi (K017 OCP) | `.ai/CLAUDE.md §5 K19 kısıtı` |
| 5 | Üretim-defosu (IPC 3 dışı) | K020 QC reddi | kart geri çevirimi | ADR-063 · `.ai/CLAUDE.md §5 H4` |
| 6 | Lisans-uyumsuz CAD kaynağı | kullanım yasak | tasarım-red | ADR-089 §1.3 |
| 7 | Kural-metni erişilemez (ölü referans) | kanıt-zayıflığı → ölçüm/araştırma kapısı | üretim gecikmesi | §4.5 · G3 |
| 8 | Varyant-boyut uyuşmazlığı | kart-uyumsuzluk | montaj hatalı | ADR-090 · §6.4 #10 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K019 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K019 + §5 (K19/H4/K1/K18) + brain.md + rules.md + ADR-096 + ADR-063/089 başlıkları okundu |
| KAPI 9 hallucination damgası | `⚠️` ayağlar §7.1'de (stackup sırası · EMC/termal eşikleri · CAD aracı · pcb-classab.md ölü referansı · web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — ölçütler yalnız anayasa/ADR'den; uydurma değer yok |
| R16.3 (durum etiketi) | Uygun — tüm kalemler DESIGN/BAĞLAYICI (repo 0) |
| H10 (hedef ≠ kanıt) | Uygun — 50 bileşen hedefi ayrı; repo kanıtı ayrı |
| R8 (çelişki) | 1 açık: kural-metni ölü referansı (G3) → atıf güncellemesi 👤 |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok (gerekçe §3b) |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) |
| K3 | Kısıtların kavranması | ENIG + 2oz + 6-layer + IPC 3 yazılı | dolu (§2/§3/§4.2) |
| K4 | Veri sınırı | geometri; üretim kaydı K020 | dolu (§3/13 · §5.3) |
| K5 | Çift uçak | PHYSICAL + senkron-yasak yazılı | dolu (§1/§5.1/§5.4) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G7, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt (+ G3 teyidi) | bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K019 - PCB «ALEV»` (+ §A.0 anahtar satırı: PHYSICAL) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K19 · H4 · K1 kısıtı · K16/K17/K18 satırları) · `§19` (clocking/48kHz) | dosya yolu (vault read 2026-10-08) | K-matrix + kısıt + komşular |
| 3 | `brain.md §5` (Electronics Registry `pcb-classab.md` kaydı + H4 satırı) | dosya yolu (vault read 2026-10-08) | kural seti izi (dosya diskte yok → G3) |
| 4 | repo find: `*.kicad_pcb/*.brd/*Gerber*` + `*.cpp/*.c` + `CMakeLists.txt` → 0 | repo grep/find (2026-10-08) | IMPLEMENTED iddiasının yokluğu |
| 5 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 | ADR (ls teyitli) | karar atıfları (ADR-063 = ana kural seti) |
| 6 | `rules.md R2/R3/R4/R6/R8/R9/R16.3` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` | dosya yolu (vault read) | format + yön |
| 7 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | 6-layer stackup sırası detayı yok (yalnız "6-layer") | §4.3.1 · EK C 7 | ADR-063 iç detayı + tasarım belgesi |
| G2 | EMC/termal-geçiş/star-ground eşik değerleri yok | §6.4 #3-7 | ölçüt kararı 👤 + ölçüm |
| G3 | `brain.md` Electronics Registry `electronics/pcb-classab.md` yolu diskte YOK | §4.5 · KANIT zinciri | dosya geri-gelişi veya atıf güncellemesi |
| G4 | CAD aracı/ortam seçimi belirsiz (lisans kısıtı ile) | §6.2 · EK C 7 | araç-seçim kararı (ADR-089 §1.3 ile uyumlu) |
| G5 | Kanal-kartı yerleşim detayı (ADR-089 §1.1 aktarımı) tek başına | §4.3.10 | tasarım belgesi |
| G6 | Kasa/istif mekanik uyumu yok | §4.3.10 | mekanik şartname (⚠️) |
| G7 | Web kanıtı (URL+tarih) — stackup/empedans/EMC iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G8 | Gerber/BOM üretim-çıktı formatı (K020 ile sınır) | §4.7 · §6.1 | K020 dosyasıyla ortak sözleşme |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2).

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» (senkron bağ YOK — sürücü/API/olay, çift uçak) · K016 AMPLIFIER «ZAR» ·
K017 POWER «KANTAR» · K018 THERMAL «MEZİT» (üçü de K019'a şart verir; K019 iç kararlara müdahale edemez).
Sağ/üst (H20 yasak yönü): K020 MANUFACTURING «BUZUL».
İlişki türü: kardeş katmanlar arası yalnız `refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K014-ag.md | K014 NETWORK «ÇARK» | band-1 setinin parçası |
| b1-K015-medya.md | K015 MEDIA «MÜHÜR» | band-1 setinin parçası |
| b1-K016-amplifikator.md | K016 AMPLIFIER «ZAR» | band-1 setinin parçası |
| b1-K017-guc-kaynagi.md | K017 POWER «KANTAR» | band-1 setinin parçası |
| b1-K018-termal.md | K018 THERMAL «MEZİT» | band-1 setinin parçası |
| b1-K019-pcb.md | K019 PCB «ALEV» | bu dosya (draft) |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
