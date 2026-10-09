---
title: "K018 THERMAL «MEZİT» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K018-termal/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: termal
ssot: true
risk: medium
owner: audio-hw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K018 THERMAL «MEZİT» — Katman Index

> **Authority:** Bu dosya K018 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K018 kartı > `.ai/CLAUDE.md` §5 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak)
> + donanım kararı: `ADR-089-classab-24v` (accepted — Fischer SK53-100-SA · KSD301 · Noctua NF-A8).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** PHYSICAL PLANE (anayasa §A.0: `K018 · THERMAL · «MEZİT» · PHYSICAL` · ADR-096 §2.2).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K018 |
| Kanonik Ad | THERMAL |
| Teatral Epitet | «MEZİT» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | PHYSICAL (ADR-096 §2.2 — PHYSICAL = K016→K020) |
| Dizin deseni | `.ai/architecture/K018-termal/index.md` (R2.2 — dizin henüz üretilmedi, ls 2026-10-08) |
| Tier / Domain | 3 / termal |
| Owner (`.ai/AGENTS.md` §4 registry) | audio-hw (Audio Hardware Engineer — `audio-hw`, AGENTS.md L91) |
| Risk | medium — aşırı-sıcaklık koruması mekanik/fail-safe olarak tanımlı (KSD301 cutoff), enerji-yoğunluğu K017'de (high); K018'in başarısızlığı K017/K016 korumalarına devredilir → medium |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K018 THERMAL «MEZİT», CoreMusic'in ısı yönetim katmanıdır: 8×50W Class AB amplifikatörün
ürettiği ısıyı (kısıt: **41W/kanal ısı yönetimi**) Fischer SK53-100-SA heatsink, 80mm PWM fan ve
KSD301 thermal cutoff ile yönetir — anayasa §5 K18 satırı bağlayıcıdır. Katmanın kritik özelliği
**fail-safe zorunluluğudur**: KSD301 mekanik termal-sigorta olduğu için firmware/MCU
kilitlense bile devreyi keser (görev notu: "K018: thermal cutoff fail-safe"). **PHYSICAL
PLANE** olduğundan K015/K009 yazılımına **senkron bağlantısı YOKTUR** (ADR-096 §2.2 GÖREV 07);
sıcaklık verisi yalnız telemetri olayı olarak yukarı çıkar. Bilinen çelişki: anayasa §5 içinde
K18 satırı **Fischer SK53-100-SA** derken H3 (K1 Hardware Subsystems) satırı **Fischer SK82-150-SA**
der — ikisi ayrı satırlarda ve bu dosyada ayrı kaydedilmiştir (§4.3.2 · R8/Contradiction Gate).
Repo durumu: `find` `*.cpp/*.c/CMakeLists/platformio` → **0 sonuç** (2026-10-08) → DESIGN/PLANNED.

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K018 | THERMAL | «MEZİT» | termal | pasif heatsink + 80mm PWM fan (PWM sürücü/K002) + KSD301 mekanik cutoff · fan-kontrol firmware'i HEDEF (repo 0, 2026-10-08) | Thermal Model · Heat Sources · Heat Sink · Fan Control · Temp Sensors · Protection · Shutdown (EK A §A.1 · 45 bileşen — anayasa §5 K18) | ısı girdisi (K016 amplifikatör · K017 güç kaynakları — W) · sıcaklık ölçümü (sensor) · PWM/fan komutu (sürücü-API) | heatsink yüzey-atımı · hava akışı (fan) · sıcaklık/ölçüm olayı · **thermal cutoff sinyali (mekanik kesme)** | K000-K017 (EK A aralık) + port/adapter — **PHYSICAL uç: yazılıma yalnız sürücü/API/olay, senkron yok** (ADR-096 §2.2); fiziksel ortaklar: K016 (ısı kaynağı) · K017 (OTP tetiği) · K019 (thermal vias) | K018 → K019-K020 sağ/üst erişim (H20) · **K015/K009 yazılımı ile SENKRON bağ** · H19 veri paylaşımı · termal-cutoff'ın yazılımla devre dışı bırakılması (fail-safe ihlali) · ısı-kaynağına doğrudan müdahale (K016/K017 içi) | yalnız sıcaklık/ölçüm verisi (sensor okuma · fan PWM durumu · cutoff durumu); iş/katalog verisi PAYLAŞMAZ; üretim-test (termal-soak) kaydı K020'ye | **fail-safe zorunlu:** KSD301 thermal cutoff firmware'den bağımsız mekanik kesme (görev notu) · kademeli koruma: fan-artışı → kısıtlama → kesme · shutdown davranışı (EK A) · fan-kontrol fail-open YASAK (sensör-yok = en kötü-durum davranışı ⚠️ ölçüt) | fail-safe cutoff (aşırı-sıcaklık → mekanik kesme) · fan arızası → pasif heatsink kapasitesi + cutoff · sensör arızası → en kötü-durum PWM/kesme (⚠️ politika tanımsız) · heatsink teması-kaybı → cutoff'a kadar süre ⚠️ | sıcaklık/ölçüm olayları → sürücü/API → K012 · PWM/duty ölçümü · üretim-test termal kayıtları (K020) · canlı metrik PLANNED | **Hedef test tanımları (yazılmadı):** termal-soak (tam yükte sürekli) · 41W/kanal ısı dengesi · KSD301 cutoff doğrulaması (72°C — ADR-089 aktarımı) · fan PWM eğrisi · sensör-fail davranışı · termal-kamera ölçümü · heatsink temas basıncı | vault: `.ai/CLAUDE.md §5 K18 + §5 H3 + §5 K16 (50W/kanal ısı kaynağı)` + `00-kspace-anayasa.md §A.1 K018` + `brain.md §5` · ADR: ADR-061/063/064/089 (accepted/ ls) · repo: find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K018 |
| 2 | KANONİK_AD | THERMAL |
| 3 | TEATRAL_EPİTET | «MEZİT» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | termal |
| 5 | SUBDOMAIN | thermal-model · heat-sources · heatsink · fan-control · temp-sensors · protection-cutoff · shutdown |
| 6 | BOUNDED_CONTEXT | Isı Yönetimi ve Aşırı-Sıcaklık Koruması — ısıyı dağıtır/ister ve kesme verir; iş verisi üretmez, güç dönüşümü yapmaz, sinyali işlemez |
| 7 | RUNTIME | pasif heatsink (Fischer SK53-100-SA — anayasa K18 satırı) + 80mm PWM fan (Noctua NF-A8 — H3/ADR-089 aktarımı) + KSD301 mekanik termal-cutoff + sıcaklık sensörleri · fan-kontrol firmware'i HEDEF (repo 0, 2026-10-08) |
| 8 | SORUMLULUK | Thermal Model · Heat Sources · Heat Sink · Fan Control · Temp Sensors · Protection · Shutdown (EK A §A.1 K018) · **41W/kanal ısı yönetimi** (§5 K18 kısıtı) · 45 bileşen (anayasa §5 K18) |
| 9 | GIRDI | ısı yükü: K016 amplifikatör (8×50W sınıfı) · K017 güç dönüşüm kayıpları · ortam sıcaklığı · sensör okumaları · fan PWM komutu (sürücü-API) |
| 10 | CIKTI | yüzey-atım/heatsink yayılımı · hava akışı (fan PWM) · sıcaklık/ölçüm olayı · **thermal cutoff sinyali (mekanik kesme)** · shutdown durumu |
| 11 | IZINLI_BAGIMLILIK | K000-K017 aralık (EK A §A.1 "izinli=K000-K017") + port/adapter; somut uçlar: K000 (ölçüm/runtime), K002 (fan PWM sürücü-API), K016 (ısı kaynağı — K018 üzerine/geçiş), K017 (OTP/koruma etkileşimi), K019 (thermal vias/bakır alan) |
| 12 | YASAK_BAGIMLILIK | K018 → K019-K020 sağ/üst katmanlara doğrudan erişim (H20) · **K015/K009/K010 yazılımı ile SENKRON BAĞ** (yalnız sürücü/API/olay — ADR-096 §2.2) · H19 veri paylaşımı · KSD301 cutoff'ın yazılımla bypass/devre-dışı bırakılması · ısı kaynağına (K016/K017 iç devresine) doğrudan müdahale · K005'e veri yazımı |
| 13 | DATA_BOUNDARY | yalnız ölçüm: sıcaklık (sensor) · PWM duty · cutoff durumu · termal-soak sonucu (üretimde K020'ye). İş/katalog/medya verisi PAYLAŞMAZ; kalıcı DB yazımı YOK; canlı olay K012'ye |
| 14 | SECURITY_BOUNDARY | **fail-safe (görev notu):** KSD301 mekanik termal-cutoff firmware'den bağımsız · kademeli koruma (fan → kısıtlama → kesme) · fan/sensör arızasında en-kötü-durum davranışı (fail-open yasak — ⚠️ ölçüt eksik) · cutoff-sonrası yeniden-açma (manual reset) politikası ⚠️ |
| 15 | FAILURE_MODE | fail-safe: aşırı-sıcaklık → KSD301 ile mekanik kesme · fan arızası → pasif kapasite + artış-sinyali + kesme · sensör arızası → en-kötü-durum (⚠️) · heatsink temassızlığı → cutoff öncesi süre (⚠️) · PWM sürücü kaybı → fan-tam-hız varsayımı (fail-safe tercihi ⚠️) |
| 16 | OBSERVABILITY | sıcaklık/PWM/cutoff olayları → sürücü/API → K012 · üretim-test termal kayıtları (K020) · canlı metrik/izleme (Prometheus) PLANNED |
| 17 | TEST | **Hedef test tanımları (yazılı değil):** termal-soak (tam yük, sürekli) · 41W/kanal ısı dengesi doğrulaması · KSD301 cutoff sıcaklığı doğrulaması (72°C — ADR-089 aktarımı) · fan PWM eğrisi/gürültüsü · sensör-fail senaryosu · kesme sonrası kurtarma · termal-kamera yüzey ölçümü · heatsink temas basıncı |
| 18 | KANIT | vault: `.ai/CLAUDE.md §5 K18 satırı (Fischer SK53-100-SA · 80mm PWM fan · KSD301 · 45 bileşen · kısıt: 41W/kanal)` + §5 H3 satırı (Fischer SK82-150-SA · Noctua NF-A8 · sıcaklık kontrollü sessiz fan) + §5 K16 (50W/kanal — ısı kaynağı) · `00-kspace-anayasa.md §A.1 K018` · `brain.md §5` (H3 + thermal-design-classab.md kaydı) + ADR-089 §1.1 aktarımı (SK53 300×75×49mm · NF-A8 · KSD301) · ADR: ADR-061/063/064/089 (accepted/ ls) · repo: find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |
| 19 | KANIT_TARIHI | 2026-10-08 (vault read + accepted/ ls + repo find) |
| 20 | EPİTET_KALİTE_NOTU | «MEZİT» — siper/sığınak metaforu: ısıya karşı koruyan set; 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K018 · THERMAL · «MEZİT» · PHYSICAL` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI'daki K015/K016/K017
kayıtları fiziksel geçiş/sürücü-API/olay taşır; YASAK'taki K015 kaydı **senkron yazılım bağı**,
YASAK'taki "ısı kaynağına müdahale" K016/K017'nin İÇ devresi (K018'in erişim alanı dışı) —
koşul farklı → kesişim ∅; H20 aralığı K019-K020 IZINLI aralığının dışında) · (c) KANIT 3'lü format —
GEÇTİ (vault/ADR | repo 0 | web ⚠️) · (d) veri sınırı tek katmana ait (ölçüm; üretim kaydı K020,
iş verisi K005) — GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K018 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Thermal Model · Heat Sources · Heat Sink ·
Fan Control · Temp Sensors · Protection · Shutdown".

| Kalem | Ne yapar | Durum (repo kodu yok → DESIGN/PLANNED — H1) | Kanıt |
|---|---|---|---|
| Thermal Model | ısı üretimi/dağılımı modeli (kaynak → heatsink → hava) | DESIGN (kısıt: 41W/kanal) | `.ai/CLAUDE.md §5 K18` · §5 K16 (50W/kanal sınıfı) |
| Heat Sources | ısı kaynaklarının tanımları (amplifikatör · güç dönüşümü) | DESIGN | `.ai/CLAUDE.md §5 K16 + K17` · ADR-089 (8×50W) |
| Heat Sink | Fischer SK53-100-SA (K18 satırı) — yüzey-atım | DESIGN (çelişki: H3 SK82-150-SA — §4.3.2) | `.ai/CLAUDE.md §5 K18` · §5 H3 |
| Fan Control | 80mm PWM fan kontrolü (sıcaklık kontrollü) | DESIGN (PWM eğrisi ⚠️) | `.ai/CLAUDE.md §5 K18` + §5 H3 ("Sıcaklık kontrollü sessiz fan") |
| Temp Sensors | sıcaklık sensörleri (kaynak/çıkış ölçümü) | DESIGN (konum/sayı ⚠️) | `00-kspace-anayasa.md §A.1 K018` (Temp Sensors) |
| Protection | KSD301 thermal cutoff — mekanik aşırı-sıcaklık koruması | DESIGN (zorunlu — fail-safe) | `.ai/CLAUDE.md §5 K18` · ADR-089 §1.1 (KSD301) |
| Shutdown | aşırı-sıcaklıkta kademeli kapanma/kısıtlama | DESIGN (davranış ⚠️ eksik) | `00-kspace-anayasa.md §A.1 K018` (Shutdown maddesi) |

### §4.2 Anayasa §5 K-Matrix Satırı (K18) — 45 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K18 Termal Tasarım | Fischer SK53-100-SA,
80mm PWM fan, KSD301 thermal cutoff | 45 bileşen | 41W/kanal ısı yönetimi.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Fischer SK53-100-SA | pasif heatsink (yüzey-atım) | DESIGN (seçim kararlı — K18 satırı) | `.ai/CLAUDE.md §5 K18` · ADR-089 §1.1 (PROJECTS aktarımı: 300×75×49mm) |
| 80mm PWM fan | hava akışı (sıcaklık kontrollü) | DESIGN | `.ai/CLAUDE.md §5 K18` · §5 H3 (Noctua NF-A8) · ADR-089 §1.1 (Noctua NF-A8 PWM 80mm) |
| KSD301 thermal cutoff | mekanik aşırı-sıcaklık sigortası (fail-safe) | DESIGN (zorunlu) | `.ai/CLAUDE.md §5 K18` · ADR-089 §1.1 (KSD301 + DC offset koruma rölesi) · ADR-089 §1.1 (KSD301 72°C) |
| "45 bileşen" sayımı | §5 K18 bileşen sayısı (envanter — HEDEF) | HEDEF · alt-döküm ⚠️ | `.ai/CLAUDE.md §5 K18 satırı` · H10: hedef ≠ kanıt (repo 0) |
| Kısıt: "41W/kanal ısı yönetimi" | kanal başına taşınması gereken ısı gücü | BAĞLAYICI (anayasa kısıtı) | `.ai/CLAUDE.md §5 K18` kısıt sütunu |

### §4.3 Alt-Sistem Bazlı Derinlik (10 kalem)

#### §4.3.1 Termal Model · Isı Kaynakları ve 41W/Kanal Dengesi

| Boyut | İçerik |
|---|---|
| Kural | kanal başına **41W/kanal ısı yönetimi** (§5 K18 kısıtı) — 50W/kanal sınıfının (§5 K16) çıkış-verim kaybı ısıya dönüşür |
| Durum | DESIGN (denge hesabı vault'ta yok → ⚠️ ölçüm gerekiyor) |
| Edge case | 8 kanal eş-zamanlı tam yük · düşük verim rejimi (boost kaybı K017) · ortam-sıcaklık yükselişi (araç/stüdyo) |
| Kapsam dışı | ısı kaynağının elektroniği (K016/K017 içi), kart-üzeri dağılım (K019) |
| Kanıt | `.ai/CLAUDE.md §5 K18` + §5 K16 · ADR-089 (8×50W) |

#### §4.3.2 Heatsink · Fischer SK53-100-SA ↔ SK82-150-SA (Çelişki Kaydı)

| Boyut | İçerik |
|---|---|
| K18 satırı | **Fischer SK53-100-SA** (anayasa §5 K18 — bu katmanın satırı) |
| H3 satırı | **Fischer SK82-150-SA heatsink, Noctua NF-A8 fan** (anayasa §5 "Critical Components (K1 Hardware Subsystems)" H3 + brain.md §5) |
| ADR-089 §1.1 aktarımı | "Fischer SK53 72°C" · "Fischer SK53-100-SA (300×75×49mm)" (PROJECTS aktarımı) |
| **Kayıt** | iki heatsink modeli AYRI kaydedildi (K18 satırı esas — bu katmanın satırı); hangisinin geçerli olduğu Vault Steward kararıdır (R8.2 — §7.1 G1); sayı/model birleştirme YOK (R9.4) |
| Edge case | temas-basıncı/termal-ped · montaj yönü · heatsink yüksekliği (kasa — K019/mechanical ⚠️) |

#### §4.3.3 Fan · 80mm PWM (Noctua NF-A8)

| Boyut | İçerik |
|---|---|
| Kural | 80mm PWM fan; "sıcaklık kontrollü sessiz fan" (H3 kısıt-özeti) |
| Durum | DESIGN (seçim: Noctua NF-A8 PWM 80mm — ADR-089 §1.1 aktarımı) |
| Edge case | PWM eğrisi (sıcaklık↔devir) · fan-arızası (devir-0) · başlangıç-düşük-devir (kick-start) · toz/ömür |
| Ölçüm | gürültü (dBA) ⚠️ — "sessiz" ölçütü sayısal değil |
| Kanıt | `.ai/CLAUDE.md §5 K18 + §5 H3` · ADR-089 §1.1 (accepted/ ls) |

#### §4.3.4 Sensörler · Temp Sensors

| Boyut | İçerik |
|---|---|
| Kural | sıcaklık sensörleri: amplifikatör çıkışı · güç bölümü · heatsink/ortam |
| Durum | DESIGN — sensör sayısı/konumu/tipi ⚠️ (EK A maddesi tek başına) |
| Edge case | sensör arızası/açık devre · yanlış konum (yanlış okuma) · çarpışma (sensör-MUX K017 telemetrisi ile) |
| Fail-safe | sensör-yok davranışi: en-kötü-durum PWM + kesme mi ⚠️ (politika tanımsız) |
| Kanıt | `00-kspace-anayasa.md §A.1 K018` (Temp Sensors) · ⚠️ VERIFICATION REQUIRED |

#### §4.3.5 Koruma · KSD301 Thermal Cutoff (Fail-Safe)

| Boyut | İçerik |
|---|---|
| Kural | KSD301 mekanik termal-cutoff: belirlenen sıcaklıkta devreyi **fiziksel olarak** keser |
| Durum | DESIGN (zorunlu — görev notu: "K018: thermal cutoff fail-safe") · kesme sıcaklığı: **72°C** (ADR-089 §1.1 aktarımı "KSD301 72°C") |
| Edge case | cutoff kaynaşması/ömür (çoklu çevrim) · yanlış-sıcaklık montajı · reset (manuel mi otomatik mi ⚠️) |
| Neden fail-safe | firmware/MCU kilitlense bile kesme çalışır (bağımsız mekanik zincir) — R6.5 ihlali sayılmaz, koruma-katmanıdır |
| Kanıt | `.ai/CLAUDE.md §5 K18` · görev notu (fail-safe zorunluluğu) · ADR-089 §1.1 (72°C + röle kaydı) |

#### §4.3.6 Kademeli Koruma · Fan → Kısıtlama → Shutdown

| Boyut | İçerik |
|---|---|
| Kural | EK A "Protection · Shutdown" maddeleri: önce fan PWM artışı, sonra güç-kısıtlama (K017 OTP/enable), son çare cutoff |
| Durum | DESIGN — kademe eşikleri/sırası ⚠️ (vault'ta sayı yok) |
| Edge case | kademe atlaması (aniden cutoff) · geri dönüş histerizisi (yeniden-açma) |
| İlişki | K017 OTP (elektronik) ↔ K018 KSD301 (mekanik) — iki bağımsız halka |
| Kanıt | `00-kspace-anayasa.md §A.1 K018` (Protection/Shutdown) · `.ai/CLAUDE.md §5 K17/K18` |

#### §4.3.7 Etkileşim · K017 OTP ve K016 Isı Kaynağı

| Boyut | İçerik |
|---|---|
| Kural | K016 ısı üretir → K018 dağıtır; K017 OTP tetiği K018 sensörleriyle hizalı olmalı |
| Durum | DESIGN (arayüz eşikleri ⚠️) |
| Edge case | OTP ↔ cutoff sıcaklık sırası (hangisi önce tetikler) · çakışan-reset |
| Sınır | K018 kesme verir ama güç devresine iç-zorunlulukla değil; uygulama K017'de |
| Kanıt | `.ai/CLAUDE.md §5 K17 (OTP) + K18 (cutoff)` · `00-kspace-anayasa.md §A.1 K017/K018` |

#### §4.3.8 Akustik · Sessiz Fan Politikası

| Boyut | İçerik |
|---|---|
| Kural | H3 kısıt özeti: "Sıcaklık kontrollü sessiz fan" — akustik hedef |
| Durum | DESIGN — dBA ölçütü sayısal değil (⚠️) |
| Edge case | yüksek-devir gürültüsü · PWM tıkırtısı · resonans (kasa) |
| Kullanım | Home/Studio profillerinde gürültü sınırı ⚠️ (profil tanımı yok) |
| Kanıt | `.ai/CLAUDE.md §5 H3` · ADR-019 (per-OS Neva player — kullanıcı ortamı, accepted/ ls) |

#### §4.3.9 Entegrasyon · K019 Thermal Vias / Bakır Alanı

| Boyut | İçerik |
|---|---|
| Kural | ısı, PCB üzerinden thermal vias / bakır alanla taşınır (K019 kısıtı: "thermal vias") |
| Durum | DESIGN (K019 ile ortak tasarım) |
| Edge case | via-yoğunluğu ↔ akım-yolu çatışması · sıcak nokta (hot spot) · katman-ısı iletimi |
| Sınır | geometri/stackup K019'da; K018 ısı-yükü şartını verir |
| Kanıt | `.ai/CLAUDE.md §5 K19` (thermal vias) · §5 K18 |

#### §4.3.10 Ölçüm · Termal-Soak / Yüzey Sıcaklığı (Hedef Test)

| Boyut | İçerik |
|---|---|
| Kural | tam yükte süre-sıcaklık dengesi ölçümü; termal-kamera ile sıcak-nokta haritası |
| Durum | **TANIMLI, YAZILMADI** (hedef test — "yapıldı" DEĞİL) |
| Ölçüt | 41W/kanal dengesi · cutoff 72°C doğrulaması · steady-state süresi ⚠️ (eşik yok) |
| Kapsam | üretim-test kaydı K020'ye (AES17 hattı — ADR-063) |
| Kanıt | `.ai/CLAUDE.md §5 K18` (kısıt) · ADR-063 (accepted/ ls) · ADR-089 §1.1 (72°C) |

### §4.4 Kapsam Dışı / Sınır Tanımı (K018'in YAPMADIĞI)

| Aday konu | Neden K018 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| Amplifikatör ısı üretimi içi-çaprazi (bias/verim) | yükseltme katmanı | K016 AMPLIFIER | `.ai/CLAUDE.md §5 K16` |
| Güç dönüşümü/OTP elektronik devresi | güç katmanı | K017 POWER | `.ai/CLAUDE.md §5 K17` |
| PCB stackup/thermal via geometrisi | PCB katmanı | K019 PCB | `.ai/CLAUDE.md §5 K19` |
| Üretim-test/termal-kayıt | üretim katmanı | K020 MANUFACTURING | ADR-063 · `.ai/CLAUDE.md §5 K20` |
| DSP/fan-rpm-software kontrol UI'ı | yazılım/uç-kullanım | K002 (PWM sürücü) · K010/K011 (UI) | ADR-096 §2.2 |
| Fan fiziği/elektroniği (motor) | donanım-alt-sistem | K001 HARDWARE | `.ai/CLAUDE.md §5 K1` |

### §4.5 Durum Özeti & Çelişki Envanteri (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K18 hedefi | 45 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (find 2026-10-08) | `*.cpp/*.c/*.ino` + `CMakeLists.txt` + `platformio.ini` → **0** |
| Karar kanıtı | ADR-089 (accepted — SK53 · KSD301 · NF-A8 aktarımı) · ADR-061/063/064 (accepted) — ls 2026-10-08 |
| Durum dağılımı | DESIGN: 8 kalem (model · kaynak · heatsink · fan · sensör · cutoff · kademeli koruma · entegrasyon) · TANIMLI-YAZILMADI: 1 (ölçüm) · PLANNED: firmware/PWM eğrisi · IMPLEMENTED: 0 |
| Çelişki envanteri | **C1:** heatsink SK53-100-SA (K18) ↔ SK82-150-SA (H3) → §4.3.2 · G1 · **C2:** cutoff 72°C yalnız ADR-089 aktarımında (K18 satırında sıcaklık yok) → G2 · **C3:** brain.md `electronics/thermal-design-classab.md` yolu diskte YOK → G3 |
| Risk gerekçesi (medium) | koruma mekanik/fail-safe tanımlı; enerji-yükü K017 (high); K018 başarısızlığı K017 OTP'ye devredilir |
| Web research | 0 URL bu dosyada → ⚠️ (R9 3'lü eksik; F1 EK B kapısı) |

### §4.6 K018 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K018 etkisi |
|---|---|---|
| ADR-061-electronics-architecture | Electronics Architecture (L6) — kart/modül hiyerarşisi · bileşen seçim politikası | termal-bileşen seçim politikası çerçevesi |
| ADR-063-hardware-design-standards | PCB kural seti · sinyal bütünlüğü & EMC · bileşen/kart kalitesi · AES17 fabrika test | termal ölçümlerin üretim-test hizası (K020 ile) |
| ADR-064-electronics-platform-architecture | Electronics Platform (L0-L6 · 5 cihaz sınıfı) | cihaz-sınıfına göre termal konfigürasyon |
| ADR-089-classab-24v | Class AB Amplifikatör Sistemi (8×50W · 12–24V Boost · hibrit MCU) | **ana karar**: SK53 · KSD301 · NF-A8 aktarım zinciri |
| ADR-090-channel-variant-product-family | Kanal varyant ürün ailesi (mono…8+1) | varyant-başına ısı yükü (az kanal = az ısıl yük) |
| ADR-096-kspace-5000-boundary-model | K-Space V2 · dizin deseni · çift uçak (§2.2) | PHYSICAL plane kuralı + format kaynağı |

### §4.7 K018 Arayüz Sözleşmeleri (komşularla sınır — çift uçak)

| Komşu | Arayüz | K018'in verdiği | K018'in beklediği | Kanıt |
|---|---|---|---|---|
| K016 AMPLIFIER | ısı yükü (fiziksel) + sıcaklık-okuma | ısı atımı/hava akışı + cutoff koruması | ısı gücü (W) — 41W/kanal sınıfı | `.ai/CLAUDE.md §5 K18 + K16` |
| K017 POWER | OTP etkileşimi + güç-bölümü ısısı | sıcaklık/fault sinyalleri + cutoff | OTP sıcaklık eşikleri hizası | `.ai/CLAUDE.md §5 K17` |
| K019 PCB | thermal vias/bakır alan şartı | ısı-iletim geometrisi gereksinimleri | heatsink yeri/yüksekliği · sıcak noktalar | `.ai/CLAUDE.md §5 K19` |
| K002 DRIVERS | fan PWM sürücü-API | PWM duty komutu | devir/geri bildirim olayı | ADR-096 §2.2 (yalnız driver/API) |
| K015/K009 (yazılım) | **olay/sürücü-API yalnız (senkron YOK)** | sıcaklık/PWM/cutoff olayları | komut kanalı (senkron bağ yasak) | ADR-096 §2.2 · R6.1 |
| K020 MANUFACTURING | termal-test/ölçüm | cutoff/soak doğrulama verisi | AES17 test hattı | ADR-063 (accepted/ ls) |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K018 → K000 OS | aşağı | ölçüm/runtime ortamı (fan-kontrol süreçleri) | `00-kspace-anayasa.md §A.1 K018 "izinli=K000-K017"` |
| K018 → K002 DRIVERS | aşağı (sürücü/API ucu) | fan PWM komut/geri-bildirim kanalı | ADR-096 §2.2 (yalnız driver/API) |
| K018 ← K016 AMPLIFIER | karşı-yöne gelen ısı yükü | K018 ısıyı YÖNETİR; K016'ya geri-çağrı yok (yalnız fiziksel geçiş + olay) | `.ai/CLAUDE.md §5 K18` · EK A aralık (K018 izinli = K000-K017) |
| K018 ↔ K017 POWER | fiziksel/olay etkileşimi | OTP sıcaklık eşikleri + güç-bölümü ısısı — çift yönlü olay/fiziksel, senkron yazılım yok | `.ai/CLAUDE.md §5 K17/K18` |
| K018 ← K019 PCB | karşı-yöne gelen ısı-iletim (thermal vias) | geometri K019'da; K018 şart verir | `.ai/CLAUDE.md §5 K19` |
| K015/K009 yazılım | aşağı (olay/sürücü-API) | telemetri + PWM komutu — **senkron bağ YOK** | ADR-096 §2.2 (GÖREV 07) |
| port/adapter | yan | EK A istisnası (R6.3) | `00-kspace-anayasa.md §A.1 K018` |

**Çift uçak notu (ADR-096 §2.2 · GÖREV 07):** K018 PHYSICAL PLANE'dedir. K015/K009 yazılımı ile
ilişki yalnız sürücü/API/olay üzerinden; **senkron doğrudan bağlantı yasaktır** (ihlal → R6.5).

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K018 → K019-K020 doğrudan erişim / geri çağrı (H20) | klasik yön (üst/sağ katmana erişim yok) | `rules.md R6.1` · `ADR-096 §2` |
| K015/K009/K010 ile SENKRON BAĞ | çift uçak ihlali | `ADR-096 §2.2` · `rules.md R6.1` |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali (R4.4d) | `rules.md R6.1` |
| **KSD301 cutoff'ın yazılımla devre dışı bırakılması** | fail-safe zorunluluğu (görev notu) — koruma zinciri ihlali | görev notu (K018 fail-safe) · `.ai/CLAUDE.md §5 K18` |
| Isı kaynağına (K016/K017 iç devresine) doğrudan müdahale | katman içi-münhasırlık (K018 yalnız yönetir/ölçer/keser) | `00-kspace-anayasa.md §A.1 K018` (sorumluluk) |
| Fan-kontrolün fail-open olması (sensör yok → pasif) | en-kötü-durum zorunlu (⚠️ ölçüt olarak not) | `rules.md R9` · görev notu fail-safe |
| K005'e veri yazımı / iş verisi saklama | veri sınırı: K018 yalnız ölçüm | EK A K018 · H19 |
| `SELECT *` / ORM / framework (yazılım tarafında) | ADR-001/002 mutlak yasakları | `rules.md R17` |

### §5.3 Boundary Matrisi

| Boundary | K018 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | sıcaklık · PWM duty · cutoff durumu · termal-soak sonucu (üretimde); DB yazımı yok | K020 (test kaydı) · K012 (olay) · K005 (iş verisi) |
| SECURITY_BOUNDARY | **fail-safe cutoff (mekanik, firmware-bağımsız)** · kademeli koruma · bypass yasağı · fan/sensör fail politikası (⚠️) | K017 (OTP + röle) · K006 (erişim) |
| FAILURE_MODE | fail-safe kesme · fan-arızası → pasif+kritik-sinyal · sensör-arızası → en-kötü-durum (⚠️) · temas-kaybı → süre ⚠️ | K017 (koruma halkası) · K012 (olay) |
| RUNTIME boundary | pasif + PWM + mekanik zincir; firmware yok (find 0) | K000 (ölçüm) · K002 (PWM sürücü) |
| MEASUREMENT boundary | termal-soak/72°C/yüzey ölçümleri üretim-testinde (K020/AES17) | K020 (test) · K013 (test otomasyonu) |
| Olay (event) yukarı serbest | sıcaklık/PWM/cutoff olayları yukarı; senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay yayını, yukarı SERBEST)  ←  K018 sıcaklık/PWM/cutoff olayları
      ↑                                                        │
      │ (okuma)                                      [senkron K015/K009'a YASAK]
      └────────── K008 SERVICES (Event Bus) ──────────┘
                        │
  K018 yalnız alttan beslenir: K002 PWM · K016 ısı yükü · K017 OTP · K019 ısı-iletim  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K018 → K012 sıcaklık/cutoff olayı | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K018 → K015/K009 senkron çağrı | — | YASAK (çift uçak + H20) | `ADR-096 §2.2` · `rules.md R6.1` |
| K016 → K018 ısı yükü | aşağı (fiziksel) | ısı yönetimi yükü | `.ai/CLAUDE.md §5 K18` |
| K018 → K017 OTP tetiği/uyumu | aşağı (korumalı sinyal) | sıcaklık eşikleri | `.ai/CLAUDE.md §5 K17` |
| K018 → K019 ısı-iletim şartı | aşağı (geometri talebi) | thermal vias gereksinimi | `.ai/CLAUDE.md §5 K19` |

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K018 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K018 / model · sources · heatsink · fan · sensors · cutoff · shutdown | SUBDOMAIN §3/5 |
| Module → Component | heatsink modülü · fan modülü (PWM) · sensör dizisi · cutoff zinciri | hedef (devre/mekanik seviyesi) |
| Service | (donanım katmanı — servis değil; telemetri sürücü-API'sinde) | çift uçak |
| Adapter | PWM sürücü adaptörü (K002) · termal-test adaptörü (K020) | port/adapter sıçraması |
| Döngü toleransı | sıfır (R6.6) | `rules.md R6.6` |

### §5.6 K018 Risk & Güvenlik Notları (görev notu + anayasa hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| **Fail-safe ihlali (cutoff bypass)** | aşırı-sıcaklık → bileşen/yangın hasarı | KSD301 mekanik zincir + bypass yasağı (§5.2) | görev notu · `.ai/CLAUDE.md §5 K18` |
| Heatsink modeli belirsizliği (SK53 ↔ SK82) | yanlış kapasite hesabı | çelişki kaydı + 👤 karar (§4.3.2 · G1) | `.ai/CLAUDE.md §5 K18/H3` |
| Fan/sensör arızası | pasif kapasite yetersizliği | kademeli koruma + en-kötü-durum (⚠️ ölçüt) | `00-kspace-anayasa.md §A.1 K018` |
| 41W/kanal dengesinin kanıtsızlığı | termal-yetersizlik | hedef test (soak + termal kamera) — yazılmadı | `.ai/CLAUDE.md §5 K18 kısıtı` · H10 |
| cutoff sonrası yeniden-açma belirsizliği | kullanıcı-kilitlenmesi | reset politikası kararı (⚠️) | §4.3.5 · G4 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| Isı alımı | K016 ısısı (W/kanal) + K017 kayıpları | heatsink'e aktarılan yük | §4.3.1 |
| Dağıtım | thermal vias/bakır (K019) + heatsink | yüzey-atım | §4.3.9 |
| Aktif soğutma | sıcaklık okuması | PWM duty (fan devri) | §4.3.3 |
| Ölçüm | sensörler | sıcaklık olayı (yukarı) | §4.3.4 |
| Koruma | eşik-aşımı | KSD301 cutoff → kesme | §4.3.5 |
| Shutdown | kritik durum | kademeli kapanma | §4.3.6 |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Pasif | Fischer SK53-100-SA (K18 satırı — 300×75×49mm, ADR-089 aktarımı) · SK82-150-SA (H3 satırı — ⚠️ çelişki) | `.ai/CLAUDE.md §5 K18 + §5 H3` |
| Aktif | 80mm PWM fan (Noctua NF-A8 — ADR-089 aktarımı) | `.ai/CLAUDE.md §5 K18 + H3` · ADR-089 §1.1 |
| Mekanik koruma | KSD301 thermal cutoff (72°C — ADR-089 aktarımı) | ADR-089 §1.1 · `.ai/CLAUDE.md §5 K18` |
| Kısıt | 41W/kanal ısı yönetimi | `.ai/CLAUDE.md §5 K18` kısıt sütunu |
| Fan kontrol | PWM (sıcaklık kontrollü) — eğrisi ⚠️ | `.ai/CLAUDE.md §5 H3` |
| Firmware | HEDEF — repo 0 (find 2026-10-08) | repo find |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Sıcaklık okumaları | sensör → sürücü/API → K012 | PLANNED (sensör/firmware yok) |
| PWM duty / devir | fan sürücüsü | PLANNED |
| Cutoff durumu | KSD301 zinciri (mekanik) | PLANNED (devre yok) |
| Termal-soak üretim kaydı | K020 test hattı (ADR-063 AES17) | PLANNED (K020) |
| Canlı metrik/izleme | Prometheus/Grafana (§5 K12) | PLANNED |

### §6.4 Test (Hedef Test Tanımları — "yapıldı" DEĞİL)

| # | Test | Ölçüt / Kabul | Durum |
|---|---|---|---|
| 1 | Termal-soak (tam yükte sürekli çalışma) | steady-state sıcaklık ≤ hesap ⚠️ (eşik yok) | TANIMLI, YAZILMADI |
| 2 | 41W/kanal ısı dengesi | §5 K18 kısıtına uyum | TANIMLI, YAZILMADI |
| 3 | KSD301 cutoff doğrulaması | 72°C'de kesme (ADR-089 aktarımı) | TANIMLI, YAZILMADI |
| 4 | Fan PWM eğrisi | sıcaklık↔devir haritası ⚠️ (eğri yok) | TANIMLI (ölçüt eksik) |
| 5 | Fan gürültüsü | "sessiz" → dBA ölçütü ⚠️ (sayı yok) | TANIMLI (ölçüt eksik) |
| 6 | Sensör-fail senaryosu | sensör yok → en-kötü-durum | TANIMLI, YAZILMADI |
| 7 | Cutoff sonrası kurtarma | reset politikası ⚠️ | TANIMLI (ölçüt eksik) |
| 8 | Termal-kamera sıcak-nokta haritası | yüzey sıcaklığı dağılımı | TANIMLI, YAZILMADI |
| 9 | Heatsink temas basıncı/ped | termal-geçiş direnci ⚠️ | TANIMLI (ölçüt eksik) |
| 10 | Az-kanal varyant termal kontrolü | ADR-090 varyantları (daha az ısı) | TANIMLI, YAZILMADI |

### §6.5 Failure Mode Senaryoları (failure=fail-safe)

| # | Senaryo | K018 davranışı | Sistem etkisi | Kanıt |
|---|---|---|---|---|
| 1 | Aşırı sıcaklık (aşım) | KSD301 mekanik cutoff → devre kesilir | sistem kapanır (koruma) | `.ai/CLAUDE.md §5 K18` · görev notu |
| 2 | Fan arızası (devir 0) | pasif heatsink kapasitesi + kritik-sinyal → cutoff | performans kısıtlama/kapanış | `00-kspace-anayasa.md §A.1 K018` |
| 3 | Sensör arızası | en-kötü-durum davranışı ⚠️ (politika tanımsız) | belirsiz | ⚠️ (G5) |
| 4 | Heatsink temassızlığı | sıcaklık yükselir → cutoff'a kadar süre ⚠️ | hasar riski | ⚠️ (G6) |
| 5 | Firmware/MCU kilitlenmesi | cutoff bağımsız çalışır (fail-safe) | koruma sürer | görev notu |
| 6 | Fan PWM sürücü kaybı | fail-safe tercihi ⚠️ (tam-hız mı pasif mi) | belirsiz | ⚠️ (G7) |
| 7 | cutoff'ın yazılımla bypass edilmesi | **yasak ihlali** → revert + CRITICAL log | güvenlik ihlali | §5.2 · R6.5 |
| 8 | Ortam-sıcaklığı ani yükseliş (araç) | kademeli koruma (fan → kısıtlama → kesme) | kısıtlama/kapanış | `00-kspace-anayasa.md §A.1 K018` |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K018 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K018 + §5 (K18/H3/K16/K17/K19) + brain.md + rules.md + ADR-096 + ADR-089 §1 okundu |
| KAPI 9 hallucination damgası | `⚠️` ayağlar §7.1'de (SK53↔SK82 çelişkisi · PWM eğrisi · dBA · sensör-fail · cutoff reset · firmware · web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — 72°C/ölçütler ADR aktarımı olarak yazıldı; uydurma eşik yok |
| R16.3 (durum etiketi) | Uygun — tüm kalemler DESIGN/TANIMLI/PLANNED (repo 0) |
| H10 (hedef ≠ kanıt) | Uygun — 45 bileşen hedefi ayrı; repo kanıtı ayrı |
| R8 (çelişki) | 3 kayıt: SK53↔SK82 (§4.3.2 · G1) · 72°C kaynağı (G2) · electronics/* ölü yolları (G3) → 👤 |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok (gerekçe §3b) |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) |
| K3 | Fail-safe zorunluluğu | cutoff + bypass yasağı yazılı | dolu (§3/14 · §5.2 · §5.6) |
| K4 | Veri sınırı | ölçüm; DB yazımı yok | dolu (§3/13 · §5.3) |
| K5 | Çift uçak | PHYSICAL + senkron-yasak yazılı | dolu (§1/§5.1/§5.4) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G8, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt (+ G1 kararı) | bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K018 - THERMAL «MEZİT»` (+ §A.0 anahtar satırı: PHYSICAL) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K18 · H3 · K16 · K17 · K19 satırları) · `§5 K18 kısıtı` (41W/kanal) | dosya yolu (vault read 2026-10-08) | K-matrix + kısıt + komşular |
| 3 | `brain.md §5` (H3 satırı + Electronics Registry thermal-design kaydı) | dosya yolu (vault read 2026-10-08) | bileşen/ölçüt detayı |
| 4 | ADR-089 §1.1 aktarım zinciri (PROJECTS L305-311: SK53 300×75×49mm · NF-A8 · KSD301 · "TASARIM AŞAMASINDA") | dosya yolu (vault read — ADR içi) | boyut/ölçüt detayı |
| 5 | repo find: `*.cpp/*.c/*.ino` + `CMakeLists.txt` + `platformio.ini` → 0 | repo grep/find (2026-10-08) | IMPLEMENTED iddiasının yokluğu |
| 6 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 | ADR (ls teyitli) | karar atıfları |
| 7 | `rules.md R2/R3/R4/R6/R8/R9/R16.3` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` · görev notu (fail-safe) | dosya yolu (vault read) | format + yön + fail-safe zorunluluğu |
| 8 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | Heatsink çelişkisi: SK53-100-SA (K18) ↔ SK82-150-SA (H3) | §4.3.2 · EK C 7 | Vault Steward kararı (R8.2 — DUR/ask) |
| G2 | KSD301 kesme sıcaklığı (72°C) yalnız ADR-089 aktarımında; K18 satırında yok | §4.3.5 · EK C 15 | anayasa/ADR teyidi + 👤 |
| G3 | `brain.md` Electronics Registry yolları (`electronics/thermal-design-classab.md`) diskte YOK | §4.5 · KANIT zinciri | dosya geri-gelişi veya atıf güncellemesi |
| G4 | Cutoff sonrası reset politikası tanımsız | §4.3.5 · §6.5 #7 | tasarım kararı + test |
| G5 | Sensör-fail / PWM-fail politikaları tanımsız | §6.5 #3/#6 · EK C 15 | fail-safe şartnamesi + 👤 |
| G6 | Heatsink temas/termal-geçiş ölçütleri yok | §6.4 #9 | mekanik şartname |
| G7 | PWM eğrisi + dBA ölçütü sayısal değil | §6.4 #4/#5 | ölçüm + eşiğin yazılması |
| G8 | Web kanıtı (URL+tarih) — termal/bileşen iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2).

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» (senkron bağ YOK — sürücü/API/olay, çift uçak) · K016 AMPLIFIER «ZAR»
(ısı kaynağı — K018 üzerine yönetilir) · K017 POWER «KANTAR» (OTP/güç-bölümü ısısı).
Sağ/üst (H20 yasak yönü): K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL».
İlişki türü: kardeş katmanlar arası yalnız `refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K014-ag.md | K014 NETWORK «ÇARK» | band-1 setinin parçası |
| b1-K015-medya.md | K015 MEDIA «MÜHÜR» | band-1 setinin parçası |
| b1-K016-amplifikator.md | K016 AMPLIFIER «ZAR» | band-1 setinin parçası |
| b1-K017-guc-kaynagi.md | K017 POWER «KANTAR» | band-1 setinin parçası |
| b1-K018-termal.md | K018 THERMAL «MEZİT» | bu dosya (draft) |
| b1-K019-pcb.md | K019 PCB «ALEV» | band-1 setinin parçası |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
