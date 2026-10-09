---
title: "K017 POWER «KANTAR» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K017-guc-kaynagi/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: guc-kaynagi
ssot: true
risk: high
owner: audio-hw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K017 POWER «KANTAR» — Katman Index

> **Authority:** Bu dosya K017 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K017 kartı > `.ai/CLAUDE.md` §5/§23 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak)
> + donanım kararı: `ADR-089-classab-24v` (accepted — 12–24V boost · ±35V · 6S LiPo).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** PHYSICAL PLANE (anayasa §A.0: `K017 · POWER · «KANTAR» · PHYSICAL` · ADR-096 §2.2).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K017 |
| Kanonik Ad | POWER |
| Teatral Epitet | «KANTAR» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | PHYSICAL (ADR-096 §2.2 — PHYSICAL = K016→K020) |
| Dizin deseni | `.ai/architecture/K017-guc-kaynagi/index.md` (R2.2 — dizin henüz üretilmedi, ls 2026-10-08) |
| Tier / Domain | 3 / guc-kaynagi |
| Owner (`.ai/AGENTS.md` §4 registry) | audio-hw (Audio Hardware Engineer — `audio-hw`, AGENTS.md L91) |
| Risk | **high** — 6S LiPo (22.2V) enerji kaynağı + dört koruma katmanı (UVP/OVP/OCP/OTP) + DC offset >0.5V koruma rölesi zorunluluğu (anayasa §23 #7) + boost yükseltilmiş barlar (±35V); hatalı tasarım/yazılım-kontrolü fiziksel hasar/yangın riski taşır → R4.4 gereği tam 20 alan kart zorunlu (bu dosya) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K017 POWER «KANTAR», CoreMusic'in güç kaynağı katmanıdır: 6S LiPo (22.2V) veya 12–24V DC
girişten LM5122 ×2 (boost + inverting) ile simetrik barların üretilmesi, OR-ing ile giriş
dayanıklılığı, %96 verim hedefi ve UVP/OVP/OCP/OTP koruma seti anayasa §5 K17 satırında
bağlayıcıdır. Mimarî **DC-only / şebekesizdir** (ADR-089 §1.3 — sıfır 50Hz şebeke gürültüsü);
giriş-çıkış yükseltilmiş olduğundan katman **fail-safe zorunluluğu** taşır (bu görev notunca
risk:high gerekçesi). **PHYSICAL PLANE** olduğundan K015/K009 yazılımına **senkron bağlantısı
YOKTUR** (ADR-096 §2.2 GÖREV 07); izleme yalnız telemetri olayı, kontrol yalnız sürücü/API
sınırındadır. Repo durumu: `find` `*.cpp/*.c/CMakeLists/platformio` → **0 sonuç** (2026-10-08)
— tüm kalemler DESIGN/PLANNED (R16.3).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K017 | POWER | «KANTAR» | guc-kaynagi | LM5122 ×2 (boost + inverting) anahtar-geçişli regülatör; SG3525 push-pull alternatif hattı (H2) · kontrol-yazılımı/firmware repo'su YOK (find 0, 2026-10-08) | AC/DC Input · DC Rails · PSU · Regulation · LM5122 · Sequencing · OVP · OCP · Fault Handling (EK A §A.1 · 85 bileşen — anayasa §5 K17) | 6S LiPo (22.2V nominal) veya 12–24V DC giriş · OR-ing giriş düğümü · termal durum (K018) · koruma tetikleyicileri (voltaj/akım/sıcaklık) | ±35V simetrik DC bar (K16 güç istemi) · regüle 12V/5V mantık barları (K001/K002/K002 yükleri) · koruma-fault durumu (kesme) · telemetri olayı | K000-K016 (EK A aralık) + port/adapter — **PHYSICAL uç: K015/K009 yazılımına yalnız sürücü/API/olay sınırı, senkron yok** (ADR-096 §2.2) | K017 → K018-K020 sağ/üst erişim (H20) · **K015/K009 yazılımı ile SENKRON bağ** · H19 veri paylaşımı · K016'ya K017-dışı kaynak bağlama (tek güç zinciri) · koruma devrelerinin yazılım-la bypass edilmesi · şebeke (AC) girişi (anayasa: DC-only) | yalnız ölçüm/telemetri (giriş voltajı · bar voltajı · akım · sıcaklık · verim-est); K015/K005 iş verisi PAYLAŞMAZ — üretim-test kaydı K020'ye devredilir | **fail-safe zorunlu:** UVP/OVP/OCP/OTP korumaları donanım-seviyesinde bağımsız çalışır (yazılım-kilitlenmesinde devreyi korur) · DC offset >0.5V koruma rölesi (anayasa §23 #7 — K016 uygular, tetik kaynağı güç-anomalisi olabilir) · lityum-pil güvenliği (6S LiPo) · kısa-devre/ters-polarite | **fail-safe:** koruma basamakları (UVP → kesme, OVP → kesme, OCP → kesme, OTP → kesme) sıfır-yazılım-bağımlı · OR-ing: giriş kaybında ikinci kaynağa devir · sequencing: barlar sırayla yükselir/iniş yapar (pop/hasar önleme) · fault → kilitlenme-sökme (latched) davranışı ⚠️ | telemetri olayları (giriş/bar voltajı · akım · sıcaklık · fault) → sürücü/API → K012 · üretim-test ölçümleri K020 · canlı metrik PLANNED | UVP/OVP/OCP/OTP eşik testleri · verim ölçümü (%96) · ripple/şalter-gürültüsü · OR-ing devrilme · sequencing sırası · DC-offset-röle tetik = **hedef test tanımı** (yazılmadı; "yapıldı" DEĞİL) | vault: `.ai/CLAUDE.md §5 K17 + §5 H2 + §23 #7` + `00-kspace-anayasa.md §A.1 K017` + `brain.md §5/§8` · ADR: ADR-061/063/064/089/090 (accepted/ ls) · repo: find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K017 |
| 2 | KANONİK_AD | POWER |
| 3 | TEATRAL_EPİTET | «KANTAR» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | guc-kaynagi |
| 5 | SUBDOMAIN | input-or-ing · dc-rails · boost-inverting · regulation · power-sequencing · protection-uvp-ovp-ocp-otp · fault-handling · telemetry |
| 6 | BOUNDED_CONTEXT | Enerji Dönüşümü ve Koruma — girişi simetrik barlara çevirir, tüm koruma kararlarını donanım-seviyesinde verir; medya/ağ/iş verisi üretmez |
| 7 | RUNTIME | anahtar-geçişli güç elektroniği (LM5122 ×2 · alternatif: SG3525 push-pull — H2) + koruma devresi + MCU/ölçüm telemetrisi HEDEF · firmware repo'su YOK (find 0, 2026-10-08) |
| 8 | SORUMLULUK | AC/DC Input · DC Rails · PSU · Regulation · LM5122 · Sequencing · OVP · OCP · Fault Handling (EK A §A.1 K017) · **UVP/OVP/OCP/OTP** (§5 K17 kısıtı) · 85 bileşen (anayasa §5 K17) |
| 9 | GIRDI | 6S LiPo (22.2V nominal) **veya** 12–24V DC adaptör (ADR-089 · README aktarımı) · OR-ing giriş düğümü · K018 termal durumu · koruma tetikleyicileri (voltaj/akım/sensör) |
| 10 | CIKTI | **±35V simetrik DC bar** (K16 amplifikatör) · regüle mantık/yardımcı barlar (K001/K002 yükleri) · fault/koruma durumu (kesme + latched) · telemetri olayı (giriş · bar · akım · sıcaklık) |
| 11 | IZINLI_BAGIMLILIK | K000-K016 aralık (EK A §A.1 "izinli=K000-K016") + port/adapter; somut uçlar: K000 (ölçüm/MCU runtime), K001 (güç konnektörleri/yükler), K002 (sürücü/API telemetri ucu), K016 (bar-tüketicisi — fiziksel besleme), K018 (termal kesme), K019 (PCB bar/akım yolları) |
| 12 | YASAK_BAGIMLILIK | K017 → K018-K020 sağ/üst katmanlara doğrudan erişim (H20) · **K015/K009/K010 yazılımı ile SENKRON BAĞ** (yalnız sürücü/API/olay — ADR-096 §2.2) · H19 veri paylaşımı · koruma devresinin yazılımla bypass edilmesi (UVP/OVP/OCP/OTP bağımsız) · şebeke (AC) girişi · K016-K020'den geri besleme (geri çağırma) |
| 13 | DATA_BOUNDARY | yalnız ölçüm/telemetri: giriş voltajı · ±bar voltajı · akım · sıcaklık · fault kodu · verim-est. İş/katalog/medya verisi PAYLAŞMAZ; üretim-test ölçümleri (verim/ripple/koruma eşikleri) K020'ye, canlı telemetri olayı K012'ye devredilir; kalıcı DB yazımı YOK |
| 14 | SECURITY_BOUNDARY | **fail-safe zorunlu (görev notu):** UVP/OVP/OCP/OTP donanım-seviyesinde, yazılımdan bağımsız · DC offset >0.5V koruma rölesi zorunluluğu (anayasa §23 #7 — enerji-anomali tetikleri dahil) · ters-polarite/kısa-devre koruması ⚠️ (tasarım zorunluğu, ölçüt ⚠️) · lityum-pil güvenliği (6S LiPo — şarj/koruma BMS ⚠️ kapsam:notu) · firmware-erişim/enable K002-K006 üzerinden |
| 15 | FAILURE_MODE | **fail-safe:** UVP (giriş düşüşü) → kesme · OVP (aşırı voltaj) → kesme · OCP (aşırı akım) → kesme · OTP (aşırı sıcaklık) → kesme; OR-ing → giriş kaybında ikinci kaynak; sequencing → sıralı açılış/kapanış (pop/hasar önleme); fault → latched kilitlenme ⚠️ (davranış tanımı eksik); DC-offset anomalisinde röle (K016 uygulaması); PCM5122/Class D benzeri yanlış-seçim red'leri K016 kapsamında |
| 16 | OBSERVABILITY | telemetri olayları (giriş · bar · akım · sıcaklık · fault) → sürücü/API → K012 · üretim-test kayıtları (verim/ripple/eşikler) K020'de · canlı metrik/izleme (Prometheus) PLANNED |
| 17 | TEST | **Hedef test tanımları (yazılı değil):** UVP/OVP/OCP/OTP eşik doğrulaması · verim ölçümü (hedef %96) · ripple/şalter-gürültüsü · OR-ing devrilme süresi · sequencing sırası/zamanlaması · DC-offset-röle tetik noktası (>0.5V) · ters-polarite/short-circuit · sıcaklık-kurtarma (OTP reset) · 85 bileşen envanteri doğrulaması (K020) |
| 18 | KANIT | vault: `.ai/CLAUDE.md §5 K17 satırı (LM5122 ×2 · 6S LiPo 22.2V · OR-ing · %96 · UVP/OVP/OCP/OTP · 85 bileşen)` + §5 H2 satırı (±40V Push-Pull SG3525/LM5122 · 12V-24V DC · Hi-Fi filtre) + §23 #7 (DC offset rölesi) · `00-kspace-anayasa.md §A.1 K017` · `brain.md §5 (power-supply-classab.md kaydı)` + §8 · ADR: ADR-061/063/064/089/090 (accepted/ ls) · repo: find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |
| 19 | KANIT_TARIHI | 2026-10-08 (vault read + accepted/ ls + repo find) |
| 20 | EPİTET_KALİTE_NOTU | «KANTAR» — tartan/ölçen metafor: enerji ölçülür ve dengelenir (OR-ing + koruma); 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K017 · POWER · «KANTAR» · PHYSICAL` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI'daki K015/K016 kayıtları
fiziksel besleme/sürücü-API/olay taşır; YASAK'taki K015 kaydı **senkron yazılım bağı** ve YASAK'taki
K016-K020 kaydı **geri besleme/geri çağrı** tikseder — koşul farklı → kesişim ∅; H20 aralığı
K018-K020 IZINLI aralığının dışında) · (c) KANIT 3'lü format — GEÇTİ (vault/ADR | repo 0 | web ⚠️) ·
(d) veri sınırı tek katmana ait (ölçüm/telemetri; üretim kaydı K020, iş verisi K005) — GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K017 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: AC Input · DC Rails · PSU · Regulation ·
LM5122 · Sequencing · OVP · OCP · Fault Handling".

| Kalem | Ne yapar | Durum (repo kodu yok → DESIGN/PLANNED — H1) | Kanıt |
|---|---|---|---|
| AC Input | (EK A maddesi) giriş kaynağı tanımı | **RED/NOT:** mimari DC-only (şebeke yok) — madde tarihsel/envanter kapsamıdır; anayasa "DC-Only" kısıtı esas | `.ai/CLAUDE.md §5 K1` (DC-Only + Class AB) · ADR-089 §1.3 ("DC-only mimari (şebekesiz)") |
| DC Rails | simetrik/mantık barların dağıtımı | DESIGN | `.ai/CLAUDE.md §5 K17` (±35V · 6S LiPo) · ADR-089 |
| PSU | güç kaynağı ünitesi (dönüşüm + regülasyon) | DESIGN | `.ai/CLAUDE.md §5 K17` + §5 H2 |
| Regulation | bar regülasyonu (sabit voltaj) | DESIGN (ölçüt ⚠️) | `00-kspace-anayasa.md §A.1 K017` · ADR-089 (±35V) |
| LM5122 | boost + inverting kontrolcü (×2) | DESIGN (kararlı bileşen seçimi) | `.ai/CLAUDE.md §5 K17` + §5 H2 (SG3525/LM5122) |
| Sequencing | açılış/kapanış bar sırası (pop/hasar önleme) | DESIGN (tanım eksik ⚠️) | `00-kspace-anayasa.md §A.1 K017` (Sequencing maddesi) |
| OVP | aşırı-voltaj koruması → kesme | DESIGN (zorunlu — §5 K17 kısıtı) | `.ai/CLAUDE.md §5 K17` |
| OCP | aşırı-akım (kısa devre) koruması → kesme | DESIGN (zorunlu) | `.ai/CLAUDE.md §5 K17` |
| Fault Handling | arıza algılama + kilitlenme/kurtarma davranışı | DESIGN (davranış ⚠️ eksik) | `00-kspace-anayasa.md §A.1 K017` (Fault Handling) |

### §4.2 Anayasa §5 K-Matrix Satırı (K17) — 85 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K17 Güç Kaynağı ±35V | LM5122 ×2 (boost +
inverting), 6S LiPo (22.2V), OR-ing, %96 verim | 85 bileşen | UVP/OVP/OCP/OTP koruma.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| LM5122 ×2 (boost + inverting) | girişten simetrik ±bar üretimi (yükselten + negatif-yapan) | DESIGN (kararlı) | `.ai/CLAUDE.md §5 K17` · ADR-089 başlığı (12–24V Boost) |
| 6S LiPo (22.2V) | birincil enerji kaynağı (pil) | DESIGN | `.ai/CLAUDE.md §5 K17` + §5 "Class AB Amplifier System" (Pil 6S LiPo 22.2V nominal) · ADR-089 |
| OR-ing | iki giriş kaynağının dikey-ya-da devri (pil/DC adaptör) | DESIGN | `.ai/CLAUDE.md §5 K17` |
| %96 verim | dönüşüm verim hedefi | DESIGN (hedef ölçüm — §6.4) | `.ai/CLAUDE.md §5 K17` · (README aktarımı ADR-089 §1.1: "%96 tepe verim") |
| UVP/OVP/OCP/OTP koruma | düşük/aşırı voltaj · aşırı akım · aşırı sıcaklık korumaları | DESIGN (zorunlu — fail-safe) | `.ai/CLAUDE.md §5 K17` kısıt satırı · görev notu (risk:high gerekçesi) |
| "85 bileşen" sayımı | §5 K17 bileşen sayısı (envanter — HEDEF) | HEDEF · alt-döküm ⚠️ | `.ai/CLAUDE.md §5 K17 satırı` · H10: hedef ≠ kanıt (repo 0; brain.md registry kaydı 1 dosya — diskte yok §4.5) |

### §4.3 Alt-Sistem Bazlı Derinlik (10 kalem)

#### §4.3.1 Mimarî · Boost + Inverting (LM5122 ×2)

| Boyut | İçerik |
|---|---|
| Kural | 22.2V DC'den hem pozitif hem negatif bar üretilir: boost (yükselten) + inverting (tersleyen) — anayasa §5 K17 |
| Durum | DESIGN (kararlı bileşen: LM5122) |
| Edge case | yük-adaptasyonu (8 kanal tam yükte) · geçiş-durumunda asimetri · start-up aşırı-voltajı |
| Ölçüt | bar toleransı ⚠️ (vault'ta sayı yok) · ripple ⚠️ |
| Kanıt | `.ai/CLAUDE.md §5 K17` · ADR-089 başlığı (12–24V Boost) · ADR-089 §1.3 (DC-only kısıtı) |

#### §4.3.2 Giriş · 6S LiPo / 12–24V DC + OR-ing

| Boyut | İçerik |
|---|---|
| Kural | birincil 6S LiPo (22.2V) veya 12–24V DC adaptör; OR-ing ile iki kaynak dikey devrede |
| Durum | DESIGN |
| Edge case | pil-deşarjı (UVP eşiği) · adaptör tak-çıkar (ark/gerilim-sıçraması) · iki kaynak eş-zamanlı (öncelik/paylaşım ⚠️) |
| Güvenlik | lityum-pil: BMS/koruma kapsamı ⚠️ (vault'ta tanımsız → research/gap) |
| Kanıt | `.ai/CLAUDE.md §5 K17` · ADR-089 §1.1 (README aktarımı: "6S LiPo (22.2V) veya 19–24V DC adaptör") |

#### §4.3.3 Çıkış Barları · ±35V (H2 ±40V ile çelişki kaydı)

| Boyut | İçerik |
|---|---|
| K17 satırı | **±35V simetrik** (anayasa §5 K17 + ADR-089 §1.1 aktarımı L117) |
| H2 satırı | **±40V Push-Pull (SG3525/LM5122)** (anayasa §5 "Critical Components (K1 Hardware Subsystems)" H2 + brain.md §5) |
| brain §8 | "Class AB Amp … ±35V DC" (K016 ile uyumlu) |
| **Kayıt** | iki bar değeri (±35V · ±40V) AYRI kaydedildi; hangisinin geçerli olduğu Vault Steward kararıdır (Contradiction Gate — §7.1 G1) — sayılarda birleştirme YOK (H10/R9.4) |
| Edge case | bar-sığası / ani yük (kick drum) · bar-denge (simetri) |

#### §4.3.4 Koruma · UVP (Under-Voltage Protection)

| Boyut | İçerik |
|---|---|
| Kural | giriş voltajı eşiğin altına düşerse devre kesilir (pil koruması + ses bozulması önleme) |
| Durum | DESIGN (zorunlu — §5 K17 kısıt satırı) |
| Edge case | histerizis (takılma-kesilme noktası ayrımı) · ani yükte dip (yanlış-UVP tetiği) |
| Fail-safe | kesme donanım-seviyesinde; yazılım-kilitlenmesinde de çalışır (görev notu fail-safe zorunlu) |
| Kanıt | `.ai/CLAUDE.md §5 K17` (UVP) · görev notu (risk:high gerekçesi) |

#### §4.3.5 Koruma · OVP + OCP (Aşırı Voltaj / Aşırı Akım)

| Boyut | İçerik |
|---|---|
| Kural | bar aşırı-voltajda kesilir; kısa-devre/aşırı akımda çıkış kesilir |
| Durum | DESIGN (zorunlu) |
| Edge case | kısa-devre dayanım süresi · kademeli-vs-anında kesme (OCP politikası ⚠️) · röle/kontakt kaynaşması (K016 rölesi ile etkileşim) |
| İlişki | OCP ile K016'nın DC-offset rölesi iki ayrı koruma halkasıdır (anayasa §23 #7 + §5 K17) |
| Kanıt | `.ai/CLAUDE.md §5 K17` (OCP/OVP) + §23 #7 · ADR-063 (kart kalitesi/EMC standartları — accepted/ ls) |

#### §4.3.6 Koruma · OTP (Over-Temperature Protection)

| Boyut | İçerik |
|---|---|
| Kural | aşırı sıcaklıkta güç kesilir; KSD301 termal-cutoff ile mekanik yedek (K018) |
| Durum | DESIGN |
| Edge case | sensör arızası (fail-safe: sensör-yok = kesme mi ⚠️) · soğuk-kurtarma eşiği · fan-kontrol etkileşimi (K018) |
| Sınır | kesme kararı K017/K018 sınırındadır; termal model K018 |
| Kanıt | `.ai/CLAUDE.md §5 K17` (OTP) · §5 K18 (KSD301 thermal cutoff) |

#### §4.3.7 Açılış/Kapanış · Sequencing

| Boyut | İçerik |
|---|---|
| Kural | barlar sırayla yükselir/iniş yapar (pop-sesini, hoparlör/çıkış hasarını önler) |
| Durum | DESIGN — **sıra/tanım vault'ta eksik ⚠️** (EK A "Sequencing" maddesi tek başına) |
| Edge case | kısmi-açılış (tek bar yukarı) · acil-kapanış · MCU-kaynaklı tetiklenme vs. donanım-sıra |
| İlişki | K016 enable-pin sırası ile eşleşmelidir (§5 K16 kısıtı) |
| Kanıt | `00-kspace-anayasa.md §A.1 K017` (Sequencing) · ⚠️ VERIFICATION REQUIRED (detay) |

#### §4.3.8 Verim & Filtre · %96 · Hi-Fi LC · Sıfır 50Hz

| Boyut | İçerik |
|---|---|
| Kural | dönüşüm verimi hedefi %96; giriş/çıkışta Hi-Fi LC filtre (voltaj çökmesini önleyen) · şebeke 50Hz gürültüsü yok (DC-only) |
| Durum | DESIGN (hedef ölçüm §6.4) |
| Edge case | tam-yükte verim düşüşü · şalter-gürültüsü/filtre-rezonansı · EMI (K019 sınırları) |
| Kanıt | `.ai/CLAUDE.md §5 K17` (%96) + §5 H2 ("Voltaj çökmesini önleyen Hi-Fi Filtreleme") · brain.md §5 (power-supply-classab.md kaydı: ±40V Push-Pull SG3525 · Hi-Fi Filtre) · ADR-089 §1.1 (README: "sıfır 50Hz şebeke gürültüsü") |

#### §4.3.9 Mimarî Kısıt · DC-Only (Şebekesiz)

| Boyut | İçerik |
|---|---|
| Kural | sistem şebekesiz/DC-only (K1 kısıtı "DC-Only + Class AB"; ADR-089 §1.3) |
| Durum | BAĞLAYICI (kısıt) |
| Edge case | AC girişi teklifi gelirse → kısıt ihlali, ADR'siz değişiklik YASAK (H08) |
| Kapsam | EK A'daki "AC Input" maddesi bu kısıtla birlikte yorumlanır (tarihsel kapsam) |
| Kanıt | `.ai/CLAUDE.md §5 K1` · ADR-089 §1.3 (accepted/ ls) |

#### §4.3.10 İzleme · Telemetri + Fault Kilitlenme

| Boyut | İçerik |
|---|---|
| Kural | giriş/bar voltajı, akım, sıcaklık, fault durumu ölçülür ve yukarı olay olarak yayınlanır; fault kilitlenir (latched) |
| Durum | DESIGN — ölçümler hedef; firmware yok (find 0) |
| Edge case | latched-fault reset politikası ⚠️ · ölçüm ADC kanal-MUX çakışması · telemetri-gecikmesi |
| Sınır | olay yukarı (K012) serbest; komut aşağı yalnız sürücü/API (K002) |
| Kanıt | `00-kspace-anayasa.md §A.1 K017` (Fault Handling) · ADR-096 §2.2 (çift uçak) · repo find 0 |

### §4.4 Kapsam Dışı / Sınır Tanımı (K017'nin YAPMADIĞI)

| Aday konu | Neden K017 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| Amplifikatör çıkış aşaması/DC-offset rölesi devresi | yükseltme katmanı | K016 AMPLIFIER | `.ai/CLAUDE.md §5 K16` + §23 #7 |
| Soğutma/fan/termal-cutoff montajı | termal katman | K018 THERMAL | `.ai/CLAUDE.md §5 K18` |
| Bar/akım yolları PCB üzerinde | PCB katmanı | K019 PCB | `.ai/CLAUDE.md §5 K19` |
| BOM/montaj/verim-testi hattı | üretim katmanı | K020 MANUFACTURING | `.ai/CLAUDE.md §5 K20` |
| MCU/ölçüm-runtime | donanım-yazılım köprüsü | K000/K002 | ADR-096 §2.2 |
| Enerji veri modeli/DB | veri katmanı | K005 | `.ai/CLAUDE.md §5 K5` |

### §4.5 Durum Özeti & Risk Gerekçesi (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K17 hedefi | 85 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (find 2026-10-08) | `*.cpp/*.c/*.ino` + `CMakeLists.txt` + `platformio.ini` → **0** |
| Karar kanıtı | ADR-089 (accepted — güç eksenleri) · ADR-061/063/064 (accepted) — ls 2026-10-08 |
| Durum dağılımı | DESIGN: 9 kalem (topoloji · giriş · barlar · UVP · OVP/OCP · OTP · sequencing · verim · telemetri) · BAĞLAYICI: 1 (DC-only) · PLANNED: ölçümler/firmware · IMPLEMENTED: 0 |
| Çelişki kaydı | ±35V (K17) ↔ ±40V (H2) bar farkı → ⚠️ (§4.3.3 · G1); brain.md `electronics/power-supply-classab.md` yolu **diskte YOK** (§4.5 G2) |
| Risk gerekçesi (high) | enerji kaynağı + 4 koruma + yükseltilmiş bar + lityum-pil; fail-safe zorunluluğu görev notunda açık; hatalı tasarım fiziksel hasar/yangın riski → R4.4 tam kart zorunlu |
| Web research | 0 URL bu dosyada → ⚠️ (R9 3'lü eksik; F1 EK B kapısı) |

### §4.6 K017 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K017 etkisi |
|---|---|---|
| ADR-061-electronics-architecture | Electronics Architecture (L6) — katman/kart/modül hiyerarşisi · bileşen seçim politikası | güç kartının elektronik-mimari içindeki yeri |
| ADR-063-hardware-design-standards | PCB kural seti · sinyal bütünlüğü & EMC · bileşen/kart kalitesi · AES17 fabrika test | güç-bölümü tasarım/test standartları (K019/K020 ile ortak) |
| ADR-064-electronics-platform-architecture | Electronics Platform (L0-L6 · 5 cihaz sınıfı · cihaz↔servis eşleme) | cihaz-sınıfına göre güç konfigürasyonu |
| ADR-089-classab-24v | Class AB Amplifikatör Sistemi (8×50W · 120dB+ · **12–24V Boost** · hibrit MCU) | **ana karar**: güç ekseni (boost · ±35V · 6S LiPo) |
| ADR-090-channel-variant-product-family | Kanal varyant ürün ailesi (mono…8+1) | varyant-başına güç yükü/farkı (K020 ile) |
| ADR-096-kspace-5000-boundary-model | K-Space V2 · dizin deseni · çift uçak (§2.2) | PHYSICAL plane kuralı + format kaynağı |

### §4.7 K017 Arayüz Sözleşmeleri (komşularla sınır — çift uçak)

| Komşu | Arayüz | K017'nin verdiği | K017'nin beklediği | Kanıt |
|---|---|---|---|---|
| K016 AMPLIFIER | ±35V simetrik bar (fiziksel) | regüle/sabit bar + korumalı besleme | akım/ihtiyaç profili (ölçüm — olay) | `.ai/CLAUDE.md §5 K17` |
| K001 HARDWARE | konnektör/yük barları (12V/5V) | yardımcı barlar | yük sınırları/şartname | `.ai/CLAUDE.md §5 K1` |
| K018 THERMAL | termal tetik/kesme | OTP için sıcaklık/fault sinyali | ısı dağılımı (K017 ısı kaynağı) | `.ai/CLAUDE.md §5 K18` |
| K019 PCB | bar/akım yolları geometrisi | akım yükü/iş akımı gereksinimleri | 2oz bakır · star ground · thermal vias | `.ai/CLAUDE.md §5 K19` |
| K020 MANUFACTURING | üretim test/ölçüm | verim/ripple/koruma-eşik ölçümleri | test hattı + BOM (85 bileşen) | ADR-063 (accepted/ ls) |
| K002/K006 (sürücü/güvenlik) | telemetri olayı + komut ucu | voltaj/akım/sıcaklık/fault olayları | enable/erişim kontrolü (senkron K015 yok) | ADR-096 §2.2 · R6.2 |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K017 → K000 OS | aşağı | ölçüm/MCU-runtime ortamı | `00-kspace-anayasa.md §A.1 K017 "izinli=K000-K016"` |
| K017 → K001 HARDWARE | aşağı | konnektör/yük/bar altyapısı | anayasa §A.1 K001 · `.ai/CLAUDE.md §5 K1` |
| K017 → K002 DRIVERS | aşağı (sürücü/API ucu) | telemetri/enable komut kanalı | ADR-096 §2.2 (yalnız driver/API) |
| K017 → K016 AMPLIFIER | aşağı (fiziksel besleme) | ±35V barını K016'ya sağlar (bar yönü K017→K016) | `.ai/CLAUDE.md §5 K17` |
| K017 → K015/K009 yazılım | aşağı (olay/sürücü-API) | telemetri/fault olayı — **senkron bağ YOK** | ADR-096 §2.2 (GÖREV 07) |
| port/adapter | yan | EK A istisnası (R6.3) | `00-kspace-anayasa.md §A.1 K017` |

**Çift uçak notu (ADR-096 §2.2 · GÖREV 07):** K017 PHYSICAL PLANE'dedir. K015/K009 yazılımı ile
ilişki yalnız sürücü/API/olay üzerinden; **senkron doğrudan bağlantı yasaktır** (ihlal → R6.5).

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K017 → K018-K020 doğrudan erişim / geri çağrı (H20) | klasik yön (üst/sağ katmana erişim yok) | `rules.md R6.1` · `ADR-096 §2` |
| K015/K009/K010 ile SENKRON BAĞ | çift uçak ihlali | `ADR-096 §2.2` · `rules.md R6.1` |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali (R4.4d) | `rules.md R6.1` |
| Koruma devresinin (UVP/OVP/OCP/OTP) yazılımla bypass edilmesi | fail-safe zorunlu (görev notu) + güvenlik bütünlüğü | `.ai/CLAUDE.md §5 K17` kısıt satırı · görev notu |
| Şebeke (AC) girişi | DC-only mimari kısıtı | `.ai/CLAUDE.md §5 K1` · ADR-089 §1.3 |
| K016-K020'den geri besleme/geri çağırma | H20 geri çağrı yasağı | `rules.md R6.1` · `ADR-096 §2` |
| K005'e veri yazımı / iş verisi saklama | veri sınırı: K017 yalnız ölçüm | EK A K017 · H19 |
| `SELECT *` / ORM / framework (yazılım tarafında) | ADR-001/002 mutlak yasakları | `rules.md R17` |

### §5.3 Boundary Matrisi

| Boundary | K017 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | ölçüm/telemetri (giriş · bar · akım · sıcaklık · fault · verim-est); DB yazımı yok | K020 (test kaydı) · K012 (olay) · K005 (iş verisi) |
| SECURITY_BOUNDARY | **fail-safe koruma seti (UVP/OVP/OCP/OTP bağımsız)** · DC-offset röle tetik kaynağı · ters-polarite/short-circuit (⚠️ ölçüt) · lityum-pil güvenliği (⚠️ kapsam) | K016 (röle uygulaması) · K018 (OTP kesme) · K006 (erişim) |
| FAILURE_MODE | fail-safe kesme + OR-ing devri + sequencing + latched-fault (⚠️ reset politikası) | K016/K018 (korumalar) · K012 (olay) |
| RUNTIME boundary | anahtar-geçişli regülatör süreçleri + koruma devresi (bağımsız) · firmware yok | K000 (ölçüm runtime) · K002 (sürücü) |
| ENERGY boundary | giriş enerjisi → ±35V bar + yardımcı barlar; K017'nin dışına yalnız bar/olay çıkar | K016 (tüketicisi) · K001 (yükler) |
| Olay (event) yukarı serbest | telemetri/fault olayları yukarı; senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay yayını, yukarı SERBEST)  ←  K017 telemetri/fault olayları
      ↑                                                        │
      │ (okuma)                                      [senkron K015/K009'a YASAK]
      └────────── K008 SERVICES (Event Bus) ──────────┘
                        │
  K017 yalnız alttan beslenir: giriş kaynağı + K001 konnektör + K002 sürücü-API  (aşağı ↓ izinli)
   ve bar sağlar: K016'ya ±35V (fiziksel besleme — bağımlılık yönü aşağı)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K017 → K012 telemetri/fault olayı | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K017 → K015/K009 senkron çağrı | — | YASAK (çift uçak + H20) | `ADR-096 §2.2` · `rules.md R6.1` |
| K017 → K016 güç barı | aşağı (K017→K016) | fiziksel besleme | `.ai/CLAUDE.md §5 K17/K16` |
| K017 → K018/K019 talep | aşağı (fiziksel) | ısı/iş akımı gereksinimleri | `.ai/CLAUDE.md §5 K18/K19` |
| K018 → K017 OTP tetiği | aşağı (korumalı sinyal) | termal kesme | `.ai/CLAUDE.md §5 K18` |

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K017 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K017 / input · rails · regulation · sequencing · protection · fault | SUBDOMAIN §3/5 |
| Module → Component | boost modülü · inverting modülü · OR-ing modülü · koruma modülü (4 kademeli) · sequencing modülü | hedef (devre seviyesi) |
| Service | (donanım katmanı — servis değil; telemetri sürücü-API'sinde) | çift uçak |
| Adapter | sürücü/API telemetri adaptörü · üretim-test adaptörü (K020) | port/adapter sıçraması |
| Döngü toleransı | sıfır (R6.6) | `rules.md R6.6` |

### §5.6 K017 Risk & Güvenlik Notları (anayasa §23 + görev notu hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| **Yüksek risk: dört koruma + DC-offset rölesi + lityum-pil** | fiziksel hasar/yangın/kanal-hasarı | UVP/OVP/OCP/OTP donanım-bağımsız fail-safe · DC offset >0.5V rölesi (§23 #7) · BMS ⚠️ | görev notu (risk:high) · `.ai/CLAUDE.md §5 K17` + §23 #7 |
| Yazılım-kilitlenmesinde korumasız kalma | enerji-hasarı | koruma devresi firmware'den bağımsız (fail-safe zorunlu) | görev notu · §5 K17 kısıtı |
| Bar değeri belirsizliği (±35V ↔ ±40V) | yanlış güç hesabı/K016 uyumsuzluğu | çelişki kaydı + 👤 karar (§4.3.3 · G1) | `.ai/CLAUDE.md §5 K17` + §5 H2 |
| Sequencing/fault-reset tanımı eksik | açılış-kapanış hasarı | tasarım + ADR ile tamamlama (⚠️) | `00-kspace-anayasa.md §A.1 K017` |
| Şebeke (AC) girişinin geri gelmesi | mimari kısıt ihlali | DC-only (ADR-089 §1.3); ADR'siz değişiklik YASAK | `.ai/CLAUDE.md §5 K1` · H08 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| Giriş kaynağı | 6S LiPo 22.2V / 12–24V DC | OR-ing düğümü (tek-aktif giriş) | §4.3.2 |
| Boost | OR-ing çıkışı | pozitif bar (yükseltilmiş) | §4.3.1 |
| Inverting | giriş/ara bar | negatif bar | §4.3.1 |
| Regülasyon | barlar | regüle yardımcı barlar (12V/5V ⚠️) | §4.3.1 |
| Sequencing | açılış/kapanış tetiği | sıralı bar-yüklenmesi | §4.3.7 |
| Koruma | voltaj/akım/sıcaklık ölçümü | kesme (UVP/OVP/OCP/OTP) | §4.3.4-6 |
| Teslim | ±35V + yardımcı barlar | K016/K001 yükleri | §5.4 |
| Telemetri | ölçüm ADC'leri | olay (yukarı) | §4.3.10 |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Topoloji | LM5122 ×2 (boost + inverting) · alternatif hat SG3525 push-pull (H2) | `.ai/CLAUDE.md §5 K17 + §5 H2` |
| Giriş | 6S LiPo (22.2V nominal) veya 12–24V DC · OR-ing | `.ai/CLAUDE.md §5 K17` · ADR-089 §1.1 |
| Çıkış | ±35V simetrik (K17) — H2 ±40V ⚠️ çelişki | `.ai/CLAUDE.md §5 K17/K17-H2` · §4.3.3 |
| Verim hedefi | %96 | `.ai/CLAUDE.md §5 K17` |
| Mimarî | DC-only (şebekesiz) · sıfır 50Hz gürültüsü hedefi | `.ai/CLAUDE.md §5 K1` · ADR-089 §1.3 |
| Bileşen sayısı hedefi | 85 (envanter) | `.ai/CLAUDE.md §5 K17` |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Telemetri (giriş/bar/akım/sıcaklık/fault) | ölçüm devresi + MCU → sürücü/API → K012 | PLANNED (firmware yok) |
| Koruma tetiği kayıtları | UVP/OVP/OCP/OTP olayı | PLANNED |
| Üretim-test ölçümleri (verim/ripple/eşik) | K020 test hattı (ADR-063 AES17) | PLANNED (K020) |
| Canlı metrik/izleme | Prometheus/Grafana (§5 K12) | PLANNED |
| Saha arıza kaydı | field service (K020) | PLANNED |

### §6.4 Test (Hedef Test Tanımları — "yapıldı" DEĞİL)

| # | Test | Ölçüt / Kabul | Durum |
|---|---|---|---|
| 1 | UVP eşik doğrulaması | giriş eşiği altında kesme + histerizis | TANIMLI (eşik değeri ⚠️) |
| 2 | OVP eşik doğrulaması | bar eşiği üstünde kesme | TANIMLI (eşik ⚠️) |
| 3 | OCP / kısa-devre | kısa devrede kesme + dayanım süresi | TANIMLI (süre ⚠️) |
| 4 | OTP | sıcaklık eşiğinde kesme + kurtarma | TANIMLI (eşik = KSD301 72°C ile hizalı olabilir ⚠️) |
| 5 | Verim ölçümü | **%96** (§5 K17) | TANIMLI, YAZILMADI |
| 6 | Ripple / şalter-gürültüsü | bar dalgalanması sınırı ⚠️ (değer yok) | TANIMLI (ölçüt eksik) |
| 7 | OR-ing devrilme | giriş kesilmesinde devir süresi | TANIMLI, YAZILMADI |
| 8 | Sequencing sırası | bar sırası + zamanlama ⚠️ | TANIMLI (ölçüt eksik) |
| 9 | DC offset röle tetiği | >0.5V → röle (anayasa §23 #7) | TANIMLI, YAZILMADI (K016 ile ortak) |
| 10 | Ters-polarite / kısa-devre | zarar yok + kesme | TANIMLI (ölçüt ⚠️) |
| 11 | 85 bileşen envanter doğrulaması | BOM ↔ şema tutarlılığı (K020) | TANIMLI, YAZILMADI |

### §6.5 Failure Mode Senaryoları (failure=fail-safe)

| # | Senaryo | K017 davranışı | Sistem etkisi | Kanıt |
|---|---|---|---|---|
| 1 | Giriş voltajı düşer (pil-deşarjı) | UVP → bar kesilir | sistem kapanır (kontrollü) | `.ai/CLAUDE.md §5 K17` |
| 2 | Bar aşırı voltaj | OVP → kesme | yükler korunur | `.ai/CLAUDE.md §5 K17` |
| 3 | Kısa devre / aşırı akım | OCP → kesme | çıkış kesilir | `.ai/CLAUDE.md §5 K17` |
| 4 | Aşırı sıcaklık | OTP → kesme (KSD301 yedek mekanik cutoff) | sistem kapanır | `.ai/CLAUDE.md §5 K17 + K18` |
| 5 | Birincil giriş kaybı (adaptör çıkar) | OR-ing → ikinci kaynağa devir | kesintisiz çalışma | `.ai/CLAUDE.md §5 K17` |
| 6 | Açılış/kapanış | sequencing sırası | pop/hasar önlenir | `00-kspace-anayasa.md §A.1 K017` |
| 7 | Fault sonrası yeniden başlatma | latched reset politikası ⚠️ tanımsız | kurtarma belirsiz | ⚠️ (G3) |
| 8 | DC offset anomali (K016 çıkışı) | tetik → K016 rölesi açar | hoparlör korunur | `.ai/CLAUDE.md §23 #7` |
| 9 | Firmware/MCU kilitlenmesi | koruma devresi bağımsız çalışır (fail-safe) | koruma sürer | görev notu (fail-safe zorunlu) |
| 10 | Bar değeri yanlış (±40V yerine ±35V vs.) | tasarım-uyumsuzluk → hasar riski | çelişki çözülmeden üretim YOK | §4.3.3 · R8 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K017 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K017 + §5 (K17/H2/K18/K1) + §23 + brain.md + rules.md + ADR-096 + ADR-089 başlık/§1 okundu |
| KAPI 9 hallucination damgası | `⚠️` ayağlar §7.1'de (±35V↔±40V · eşik değerleri · sequencing/fault-reset · BMS kapsamı · elektronik registry ölü yolları · web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — eşik/ölçüt sayıları uydurulmadı; eksikler `⚠️` |
| R16.3 (durum etiketi) | Uygun — tüm kalemler DESIGN/BAĞLAYICI/PLANNED (repo 0) |
| H10 (hedef ≠ kanıt) | Uygun — 85 bileşen hedefi ayrı; repo kanıtı ayrı |
| R8 (çelişki) | 1 açık kayıt: ±35V ↔ ±40V (§4.3.3) → 👤 (DUR/ask — üretime engel değil, kanıt olarak iki değer ayrı) |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok (gerekçe §3b) |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) — risk:high zorunluluğu |
| K3 | Fail-safe zorunluluğu | 4 koruma + bağımsızlık yazılı | dolu (§3/14/15 · §5.6) |
| K4 | Veri sınırı | ölçüm/telemetri; DB yazımı yok | dolu (§3/13 · §5.3) |
| K5 | Çift uçak | PHYSICAL + senkron-yasak yazılı | dolu (§1/§5.1/§5.4) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G5, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt (+ çelişki G1 kararı) | bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K017 - POWER «KANTAR»` (+ §A.0 anahtar satırı: PHYSICAL) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K17 · H2 · K1 · K16/K18/K19 satırları) · `§23 #7` · `§12` | dosya yolu (vault read 2026-10-08) | K-matrix + koruma + komşular |
| 3 | `brain.md §5` (H2 + Electronics Registry power-supply kaydı) · `§8` | dosya yolu (vault read 2026-10-08) | topoloji/ölçüt detayı |
| 4 | repo find: `*.cpp/*.c/*.ino` + `CMakeLists.txt` + `platformio.ini` → 0 | repo grep/find (2026-10-08) | IMPLEMENTED iddiasının yokluğu |
| 5 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 | ADR (ls teyitli) | karar atıfları |
| 6 | `rules.md R2/R3/R4/R6/R8/R9/R16.3` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` · görev notu (risk:high + fail-safe) | dosya yolu (vault read) | format + yön + risk gerekçesi |
| 7 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | ±35V (K17) ↔ ±40V (H2) bar çelişkisi | EK C 10 · §4.3.3 · §6.2 | Vault Steward kararı (R8.2 — DUR/ask) |
| G2 | `brain.md` Electronics Registry yolları (`electronics/power-supply-classab.md` vb.) diskte YOK | §4.5 · KANIT zinciri | dosya geri-gelişi veya atıf güncellemesi |
| G3 | Fault-latched reset / sequencing detayları tanımsız | §4.3.7 · §6.5 #7 | tasarım + ADR/ölçüt |
| G4 | Eşik/ölçüt sayıları (UVP/OVP/OCP/OTP · ripple · bar toleransı · yardımcı bar voltajları) yok | §6.4 · EK C 17 | hesap/ölçüm + 👤 onayı (uydurma YASAK) |
| G5 | Web kanıtı (URL+tarih) — topoloji/ölçüm iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G6 | BMS/lityum-pil güvenlik kapsamı vault'ta tanımsız | §4.3.2 · EK C 14 | kapsam kararı + research + 👤 |
| G7 | Ters-polarite/short-circuit ölçütleri yok | §6.4 #10 | tasarım şartnamesi |
| G8 | OCP kurtarma (yeniden-deneme) politikası tanımsız | §6.5 #3 | tasarım kararı + test |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2) — **risk:high katman olduğu için
G1 (bar çelişkisi) ayrıca 👤 kararı gerektirir.**

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» (senkron bağ YOK — sürücü/API/olay, çift uçak) · K016 AMPLIFIER «ZAR»
(fiziksel bar tüketicisi — besleme yönü K017→K016).
Sağ/üst (H20 yasak yönü): K018 THERMAL «MEZİT» · K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL».
İlişki türü: kardeş katmanlar arası yalnız `refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K014-ag.md | K014 NETWORK «ÇARK» | band-1 setinin parçası |
| b1-K015-medya.md | K015 MEDIA «MÜHÜR» | band-1 setinin parçası |
| b1-K016-amplifikator.md | K016 AMPLIFIER «ZAR» | band-1 setinin parçası |
| b1-K017-guc-kaynagi.md | K017 POWER «KANTAR» | bu dosya (draft) |
| b1-K018-termal.md | K018 THERMAL «MEZİT» | band-1 setinin parçası |
| b1-K019-pcb.md | K019 PCB «ALEV» | band-1 setinin parçası |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
