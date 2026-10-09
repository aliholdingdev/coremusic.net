---
title: "K020 MANUFACTURING «BUZUL» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K020-uretim/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: uretim
ssot: true
risk: medium
owner: audio-hw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K020 MANUFACTURING «BUZUL» — Katman Index

> **Authority:** Bu dosya K020 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K020 kartı > `.ai/CLAUDE.md` §5 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak)
> + test/üretim hizası: `ADR-063-hardware-design-standards` (accepted — AES17 fabrika test hizası).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** PHYSICAL PLANE (anayasa §A.0: `K020 · MANUFACTURING · «BUZUL» · PHYSICAL` · ADR-096 §2.2).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K020 |
| Kanonik Ad | MANUFACTURING |
| Teatral Epitet | «BUZUL» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION (son katman) |
| Uçak | PHYSICAL (ADR-096 §2.2 — PHYSICAL = K016→K020) |
| Dizin deseni | `.ai/architecture/K020-uretim/index.md` (R2.2 — dizin henüz üretilmedi, ls 2026-10-08) |
| Tier / Domain | 3 / uretim |
| Owner (`.ai/AGENTS.md` §4 registry) | audio-hw (Audio Hardware Engineer — `audio-hw`, AGENTS.md L91) |
| Risk | **low-medium** — katman üretim-sonrası süreçleri (BOM/tedarik/montaj/test/seri/saha) taşır; doğrudan enerji/güvenlik-riski üretmez (yüksek risk K017'de), ancak **BOM/maliyet çelişkisi** (1.775/$682 ↔ 639/$1.067) nedeniyle kanıt-temelli dikkat gerekir → low-medium (gerekçe §4.5) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K020 MANUFACTURING «BUZUL», Bant 1'in **son katmanıdır**: BOM · Suppliers · Assembly ·
Calibration · Production Test · QC · Serialisation · Field Service (EK A §A.1). Anayasa §5 K20
satırı bağlayıcıdır: **1.775 BOM satırı · Mouser/Digikey tedarik · ~$682 sistem maliyeti ·
"Üretim araçları dahil" (kısıt)**. Katman, K016-K019 fiziksel katmanlarının çıktılarını
(Gerber/BOM/ölçüm şartları) alır ve üretilebilir-ürün + geri-izlenebilir-kayıt üretir. Bilinen
çelişki: anayasa §5 içinde H5 satırı **639 bileşen · ~$1.067 (8 kanal) / ~$133 (kanal)** derken
K20 satırı **1.775 satır · ~$682** der — bu iki ölçüm ayrı kaydedilmiştir ve Vault Steward
kararı bekler (Contradiction Gate — §4.3.1 · G1). **PHYSICAL PLANE** olduğundan yazılım
katmanlarına **senkron bağlantısı YOKTUR** (ADR-096 §2.2 GÖREV 07). Repo durumu: üretim-araçları/
BOM dosyası `find` ile **0** (2026-10-08; `bom-classab.md` yalnız stub olarak diskte — §4.3.1)
→ DESIGN/PLANNED (R16.3).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|--- |---|---|---|
| K020 | MANUFACTURING | «BUZUL» | uretim | üretim/süreç araçları (BOM yönetimi · tedarik · montaj · test-iş-istasyonu) — araçlar/kod repo'da YOK (find 0, 2026-10-08) | BOM · Suppliers · Assembly · Calibration · Production Test · QC · Serialisation · Field Service (EK A §A.1 · 40 bileşen — anayasa §5 K20) | K016-K019 çıktıları (Gerber/BOM/ölçüm şartları) · tedarikçi verisi (Mouser/Digikey) · ölçüm değerleri (THD · empedans · termal · cutoff) · varyant seçimi (ADR-090) | üretilir ürün + seri-numaralı kayit · kalibrasyon sertifikası/ölçüm kaydı · QC kabul/reddi · saha-servis geçmişi · revize BOM | K000-K019 (EK A aralık — Bant 1'in tamamı) + port/adapter — **PHYSICAL uç: yazılıma yalnız sürücü/API/olay, senkron yok** (ADR-096 §2.2) | K020 → bant-2 ve üzerine doğrudan erişim / geri çağrı (H20) · **K015/K009 yazılımı ile SENKRON bağ** · H19 veri paylaşımı · K016-K018 iç tasarım kararlarına müdahale · kanıtlanmamış BOM/maliyet sayısını "gerçek" sunmak (H10 ihlali) | üretim kayıtları: BOM satırları · ölçümler · seri-numarası · rev · QC kararı · saha-servis geçmişi (K020'ye ait); iş/katalog verisi PAYLAŞMAAZ — kayıt/erişim DB'si yok (üretim kaydı ürün-üzerinde/K020 arşivinde, K005'te DEĞİL) | tedarik-özgünlüğü (orijinal/izlenebilir kaynak — Mouser/Digikey) · üretim-test güvenilirliği (AES17 hizası — ADR-063) · seri/rev geri-izlenebilirlik (counterfeit/yanlış-parça riski) · QC kararının atlanamazlığı (PASS olmadan sevkiyat) · lisans/ölçek: AGPL/uyumsuz CAD kaynağı kullanılamaz (ADR-089 §1.3) | QC reddi → kart/ürün geri çevirim (rework/devre dışı) · ölçüm-uşgunsuzluğu → kalibrasyon tekrarı/devre dışı · tedarikte yanlış-parça → Batch/seri ile geri-çekme (recall) ⚠️ (politika tanımsız) · saha-arızası → field service döngüsü · BOM-revizenin kaybolması → üretememe (⏳ PLANNED) | üretim-test ölçümleri (THD · empedans · termal · cutoff · verim) · QC karar logları · seri/rev kayıtları · tedarik emirleri · saha-servis kayıtları; metrik altyapısı PLANNED | **Hedef test tanımları (yazılmadı):** AES17 üretim-test paketi (ADR-063) · THD/DC-offset doğrulama (K016) · empedans (90Ω/50Ω — K019) · termal-soak/cutoff 72°C (K018) · verim %96 (K017) · seri/rev geri-izlenebilirlik doğrulaması · BOM↔şema tutarlılığı | vault: `.ai/CLAUDE.md §5 K20 + §5 H5 + §5 K16-K19 ölçüm bağlantıları` + `00-kspace-anayasa.md §A.1 K020` + `brain.md §5 (Electronics Registry bom-classab.md kaydı)` + `--/architecture/k16-class-ab/bom-classab.md` (stub — çelişki notu) · ADR: ADR-061/063/064/089/090 (accepted/ ls) · repo: find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K020 |
| 2 | KANONİK_AD | MANUFACTURING |
| 3 | TEATRAL_EPİTET | «BUZUL» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | uretim |
| 5 | SUBDOMAIN | bom · suppliers · assembly · calibration · production-test · qc · serialisation · field-service · cost-variants |
| 6 | BOUNDED_CONTEXT | Üretim ve Yaşam Döngüsü Kayıtları — ürünün yapılabilirliği, testi ve geri-izlenebilirliği; tasarım kararları üretmez, iş verisi taşımaz |
| 7 | RUNTIME | üretim/süreç araçları (BOM yönetimi · tedarik emri · montaj-iş-istasyonu · test-istasyonu) — araç-kodları repo'da YOK (find 0, 2026-10-08); mevcut fiziksel iz: `--/architecture/k16-class-ab/bom-classab.md` (stub — kaynak BOM diskte silinmiş, 2026-10-07) |
| 8 | SORUMLULUK | BOM · Suppliers · Assembly · Calibration · Production Test · QC · Serialisation · Field Service (EK A §A.1 K020) · **Üretim araçları dahil** (§5 K20 kısıtı) · 40 bileşen (anayasa §5 K20) |
| 9 | GIRDI | K016-K019 çıktıları (Gerber/BOM/ölçüm şartları · ADR-063 kural seti) · tedarikçi kataloğu (Mouser/Digikey) · ölçüm değerleri (THD · empedans · termal · cutoff · verim) · varyant seçimi (ADR-090 mono…8+1) · saha geri-bildirimi (arıza) |
| 10 | CIKTI | üretilir ürün · seri-numaralı + rev kaydı · kalibrasyon/ölçüm kaydı · QC kabul/reddi · revize BOM · saha-servis geçmişi |
| 11 | IZINLI_BAGIMLILIK | K000-K019 aralık (EK A §A.1 — Bant 1 tamamı; somut uçlar: K016-K018 ölçüm şartları, K019 üretim çıktısı, K000 süreç-runtime'ı, K013 test otomasyonu hizası) + port/adapter |
| 12 | YASAK_BAGIMLILIK | K020 → bant-2+ katmanlara doğrudan erişim / geri çağrı (H20) · **K015/K009/K010 yazılımı ile SENKRON BAĞ** (ADR-096 §2.2) · H19 veri paylaşımı · K016-K019 iç tasarım kararlarına müdahale · kanıtlanmamış sayı/maliyeti "gerçek" olarak sunmak (H10/R9) · AGPL/uyumsuz lisanslı CAD kaynağı kullanımı (ADR-089 §1.3) · K005'e iş verisi yazımı |
| 13 | DATA_BOUNDARY | üretim kayıtları: BOM satırları · ölçüm sonuçları · seri/rev · QC kararı · tedarik emri · saha-servis geçmişi — bunlar K020'nin kendi üretim-arşividir (ürün/CI üzerinde), K005 DB'sinde DEĞİLDİR; iş/katalog verisi PAYLAŞMAZ; canlı sistem-telemetrisi K012'ye olay olarak gider |
| 14 | SECURITY_BOUNDARY | tedarik-özgünlüğü (orijinal kaynak: Mouser/Digikey — counterfeits önleme) · üretim-test güvenilirliği (AES17 hizası — ADR-063) · seri/rev geri-izlenebilirlik (hatalı-partiyi geri-çekme) · QC PASS zinciri (atlanamaz) · lisans kısıtı (AGPL/uyumsuz CAD kaynağı — ADR-089 §1.3) |
| 15 | FAILURE_MODE | QC reddi → rework/devre dışı · ölçüm-uşgunsuzluk → kalibrasyon tekrarı · yanlış-parça → seri/batch ile geri-çekme (⚠️ politika tanımsız) · saha-arızası → field service döngüsü · BOM/rev eksikliği → üretememe · tedarik-kesintisi → alternatif-onay zorunlu (⚠️) |
| 16 | OBSERVABILITY | üretim-test ölçüm kayıtları · QC karar logları · seri/rev kayıtları · tedarik emirleri · saha-servis kayıtları · maliyet takibi (BOM revizyonu); merkezi metrik altyapısı PLANNED |
| 17 | TEST | **Hedef test tanımları (yazılı değil):** AES17 üretim-test paketi (ADR-063) · THD/DC-offset (K016 §6.4) · empedans 90Ω/50Ω (K019 §6.4) · termal-soak + KSD301 72°C (K018 §6.4) · verim %96 (K017 §6.4) · seri/rev geri-izlenebilirlik · BOM↔şema tutarlılığı (1.775 vs 639) |
| 18 | KANIT | vault: `.ai/CLAUDE.md §5 K20 satırı (1.775 BOM satırı · Mouser/Digikey · ~$682 · kısıt: Üretim araçları dahil · 40 bileşen)` + §5 H5 satırı (639 bileşen · ~$1.067/8 kanal · ~$133/kanal · kaynak: bom-classab.md — disk doğrulandı 2026-09-26) · `00-kspace-anayasa.md §A.1 K020` · `brain.md §5` (Electronics Registry bom-classab.md kaydı 2026-09-26) · stub: `--/architecture/k16-class-ab/bom-classab.md` (çelişki notu, 2026-10-07) · ADR: ADR-061/063/064/089/090 (accepted/ ls) · repo: find → 0 (2026-10-08) · web: ⚠️ VERIFICATION REQUIRED (R9) |
| 19 | KANIT_TARIHI | 2026-10-08 (vault read + accepted/ ls + repo find + stub okuma) |
| 20 | EPİTET_KALİTE_NOTU | «BUZUL» — buzul/kesinti metaforu: üretim zincirinin donmuş, ölçülmüş anı (ölçüm-değil-sezgi); 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K020 · MANUFACTURING · «BUZUL» · PHYSICAL` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI aralığı K000-K019;
YASAK kümesi bant-2+ erişimi + senkron yazılım + iç-tasarım-müdahalesi + kanıtsız-sayı iddiası →
aralık ve koşul kesişimi yok) · (c) KANIT 3'lü format — GEÇTİ (vault/ADR | repo 0 (stub dahil) |
web ⚠️) · (d) veri sınırı tek katmana ait (üretim arşivi K020'de; iş verisi K005; telemetri K012)
— GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K020 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: BOM · Suppliers · Assembly · Calibration ·
Production Test · QC · Serialisation · Field Service".

| Kalem | Ne yapar | Durum (araç/kod yok → DESIGN/PLANNED — H1) | Kanıt |
|---|---|---|---|
| BOM | malzeme listesi yönetimi (1.775 satır hedefi) | DESIGN — çelişki var (§4.3.1) | `.ai/CLAUDE.md §5 K20` · §5 H5 · stub (bom-classab.md) |
| Suppliers | tedarikçi listesi (Mouser/Digikey) | DESIGN | `.ai/CLAUDE.md §5 K20` |
| Assembly | montaj süreç/iş talimatları | DESIGN (süreç ⚠️ detay yok) | `00-kspace-anayasa.md §A.1 K020` (Assembly) |
| Calibration | ölçüm/kalibrasyon (sistem ayarı) | DESIGN (prosedür ⚠️) | `00-kspace-anayasa.md §A.1 K020` (Calibration) |
| Production Test | üretim-testi (AES17 hizası) | DESIGN (kural seti var: ADR-063) | ADR-063 (accepted/ ls) |
| QC | kalite kontrolü/kabul-red | DESIGN (kriterya ⚠️) | `00-kspace-anayasa.md §A.1 K020` (QC) |
| Serialisation | seri/rev numaralandırma · geri-izlenebilirlik | DESIGN | `00-kspace-anayasa.md §A.1 K020` (Serialisation) |
| Field Service | saha-servis/onarım döngüsü | DESIGN (süreç ⚠️) | `00-kspace-anayasa.md §A.1 K020` (Field Service) |

### §4.2 Anayasa §5 K-Matrix Satırı (K20) — 40 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K20 BOM & Üretim | 1,775 BOM satırı,
Mouser/Digikey, ~$682 sistem maliyeti | 40 bileşen | Üretim araçları dahil.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| 1.775 BOM satırı | sistem-BOM satır sayısı (envanter) | DESIGN (hedef) — H5 ile çelişki (§4.3.1) | `.ai/CLAUDE.md §5 K20` · §5 H5 (639) |
| Mouser/Digikey | tedarik kaynakları | DESIGN | `.ai/CLAUDE.md §5 K20` |
| ~$682 sistem maliyeti | sistem maliyet hedefi | DESIGN (hedef) — H5 ile çelişki (§4.3.1) | `.ai/CLAUDE.md §5 K20` · §5 H5 ($1.067/$133) |
| "Üretim araçları dahil" (kısıt) | kapsam: BOM/üretim araçları bu katmanda | BAĞLAYICI (anayasa kısıtı) | `.ai/CLAUDE.md §5 K20` kısıt sütunu |
| "40 bileşen" sayımı | §5 K20 bileşen sayısı (envanter — HEDEF) | HEDEF · alt-döküm ⚠️ | `.ai/CLAUDE.md §5 K20 satırı` · H10: hedef ≠ kanıt (repo 0) |

### §4.3 Alt-Sistem Bazlı Derinlik (10 kalem)

#### §4.3.1 BOM · 1.775 ↔ 639 (Çelişki Kaydı — Contradiction Gate)

| Boyut | İçerik |
|---|---|
| K20 satırı | **1.775 BOM satırı · ~$682 sistem maliyeti** (anayasa §5 K20) |
| H5 satırı | **639 bileşen · ~$1.067 (8 kanal) / ~$133 (kanal)** — kaynak: bom-classab.md (disk doğrulandı 2026-09-26) (anayasa §5 H5) |
| brain.md §5 | Electronics Registry: "bom-classab.md — Full 8-channel BOM: 639 components (Toplam sutunu), ~$1.067.44 (8 kanal) / ~$133.43 (kanal) — 2026-09-26" |
| Fiziksel durum | `architecture/k16-class-ab/bom-classab.md` **diskte YOK** (silinmiş); mevcut tek iz = stub `--/architecture/k16-class-ab/bom-classab.md` (2026-10-07) — açıkça "iki BOM rakamı farklı; hangisi geçerli Vault Steward onayı gerektirir (Contradiction Gate)" yazar |
| **Kayıt** | iki ölçüm AYRI kaydedildi; birleştirme/sahte-uzlaştırma YOK (H10/R9.4) · karar 👤 (§7.1 G1) |

#### §4.3.2 Tedarik · Mouser / Digikey

| Boyut | İçerik |
|---|---|
| Kural | tedarik kaynakları: Mouser · Digikey (§5 K20) — orijinal/izlenebilir parça |
| Durum | DESIGN (tedarikçi-hesap/emir süreci ⚠️ yok) |
| Edge case | stok-yokluğu (alternatif onayı ⚠️) · fiyat/değişkenlik · sahte-parça riski (counterfeit) |
| Güvenlik | özgünlük/lot-izlenebilirlik (§3/14) |
| Kanıt | `.ai/CLAUDE.md §5 K20` · ADR-090 (varyant-SKU tedarik etkisi — accepted/ ls) |

#### §4.3.3 Montaj · Assembly

| Boyut | İçerik |
|---|---|
| Kural | montaj süreci: kanal-bağımsız kartlar + konnektör + heatsink + fan + röle |
| Durum | DESIGN (iş-talimatı/akış ⚠️ yok) |
| Edge case | ESD koruması · sıralı montaj (kart → heatsink → kablo) · vida/termal-ped basıncı (K018 §6.4 #9) |
| Sınır | montaj kararı üretim içidir; tasarım K016-K019 |
| Kanıt | `00-kspace-anayasa.md §A.1 K020` (Assembly) · ADR-089 §1.1 (kanal-bağımsız PCB) |

#### §4.3.4 Kalibrasyon · Calibration

| Boyut | İçerik |
|---|---|
| Kural | sistem ölçüm-ayar döngüsü (bias/gain/DC offset ayarı) |
| Durum | DESIGN (prosedür/eşik ⚠️ yok) |
| Edge case | sıcak-başlangıç kalibrasyonu (K018 ile) · kalibrasyon-sıcaklığı · tekrar-tetikleme (saha) |
| Ölçüm | THD/gain/DC offset — K016 §6.4 hedefleri |
| Kanıt | `00-kspace-anayasa.md §A.1 K020` (Calibration) · `.ai/CLAUDE.md §5 K16` (THD hedefi) |

#### §4.3.5 Üretim Testi · Production Test (AES17)

| Boyut | İçerik |
|---|---|
| Kural | fabrika-testi hizası: **AES17 fabrika test hizası** (ADR-063 başlığı) |
| Durum | DESIGN (kural seti ADR-063'te; test-istasyonu/uygulama yok) |
| Edge case | test-süresi/otomasyon · ölçüm-belirsizliği (tolerans) · test-skip riski (QC zinciri) |
| Kapsam | K016 THD · K017 verim/koruma · K018 cutoff/termal · K019 empedans ölçümleri |
| Kanıt | ADR-063 (accepted/ ls) · `.ai/CLAUDE.md §5 K16-K19` |

#### §4.3.6 Kalite · QC (Kabul/Red)

| Boyut | İçerik |
|---|---|
| Kural | ürünün kabul/red kararı; PASS olmadan sevkiyat (§3/14) |
| Durum | DESIGN (kriterya eşiği ⚠️ yok) |
| Edge case | sınır-dışı ölçüm (borderline) · yeniden-test/rework döngüsü · örneklem planı (AQL ⚠️) |
| İlişki | IPC Class 3 muayene (K019 §6.4 #6) |
| Kanıt | `00-kspace-anayasa.md §A.1 K020` (QC) · `.ai/CLAUDE.md §5 H4` (IPC Class 3) |

#### §4.3.7 Geri-İzlenebilirlik · Serialisation (Seri + Rev)

| Boyut | İçerik |
|---|---|
| Kural | seri numarası + revizyon (rev) — hatalı-partiyi geri-çekmek için |
| Durum | DESIGN |
| Edge case | seri-üretim-aralığı · rev-atlaması (geriye-uyum) · etiket-okunabilirliği |
| Kullanım | saha-arızası → batch recall (§4.3.9) · K019 part/rev çıktısı |
| Kanıt | `00-kspace-anayasa.md §A.1 K020` (Serialisation) · ADR-063 (dokümantasyon standardı) |

#### §4.3.8 Saha · Field Service

| Boyut | İçerik |
|---|---|
| Kural | saha-servis/onarım döngüsü (arıza → teşhis → onarım → geri gönderim) |
| Durum | DESIGN (süreç/parça-stratejisi ⚠️ yok) |
| Edge case | garanti-süresi · yedek-parça stok · tekrar-arıza (bias drift — K016) |
| İlişki | telemetri olayları (K016-K018 → K012) teşhise girdi olabilir |
| Kanıt | `00-kspace-anayasa.md §A.1 K020` (Field Service) |

#### §4.3.9 Ürün Ailesi · Varyant / SKU (ADR-090)

| Boyut | İçerik |
|---|---|
| Kural | varyant seti: mono · 2 · 2+1 · 4 · 5 · 6 · 7 · 8 · 7+1 · 8+1 — SKU politikası ADR-090 |
| Durum | DESIGN (karar accepted) |
| Edge case | varyant-başına BOM/fark (maliyet kademesi) · stok bileşimi · firmware-konfigürasyon |
| Ölçüm | varyant-başına maliyet ($682 sistem ↔ $133/kanal H5 — ⚠️ çelişki bağlamı) |
| Kanıt | ADR-090-channel-variant-product-family (accepted/ ls) · `.ai/CLAUDE.md §5 K20/H5` |

#### §4.3.10 Maliyet & Üretim Araçları (Kısıt + Çelişki Bağlamı)

| Boyut | İçerik |
|---|---|
| Kural | kısıt: **"Üretim araçları dahil"** (§5 K20 kısıt sütunu) — araç-kapsamı bu katmandadır |
| Ölçümler | **~$682 sistem** (K20) · **~$1.067 (8 kanal) / ~$133 (kanal)** (H5) · **BOM <$430** (k16 sınıfı — ADR-089 §1.1 aktarımı) → üç ölçüm ayrı; uzlaştırma ⚠️ (§7.1 G1) |
| Durum | DESIGN (araçlar repo'da yok — find 0) |
| Edge case | maliyet-enflasyonu · araç-entegrasyonu (BOM ↔ CAD ↔ ERP ⚠️) |
| Kanıt | `.ai/CLAUDE.md §5 K20 + §5 H5` · ADR-089 §1.1 · repo find 0 |

### §4.4 Kapsam Dışı / Sınır Tanımı (K020'nin YAPMADIĞI)

| Aday konu | Neden K020 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| Kart geometrisi/stackup | tasarım katmanı | K019 PCB | `.ai/CLAUDE.md §5 K19` |
| Termal model/kesme eşiği | termal katman | K018 THERMAL | `.ai/CLAUDE.md §5 K18` |
| Güç korumaları/verim | güç katmanı | K017 POWER | `.ai/CLAUDE.md §5 K17` |
| Amplifikatör topolojisi/ölçütleri | yükseltme katmanı | K016 AMPLIFIER | `.ai/CLAUDE.md §5 K16` |
| Yazılım test otomasyonu (CI) | dağıtım katmanı | K013 CI/CD | `.ai/CLAUDE.md §5 K13` |
| Üretim sonrası sistem-testi/canlı-ölçüm | canlı sistem izleme | K012 OBSERVABILITY | `.ai/CLAUDE.md §5 K12` |

### §4.5 Durum Özeti & Risk Gerekçesi (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K20 hedefi | 40 bileşen (envanter) + 1.775 BOM satırı (hedef) |
| Repo kanıtı (find 2026-10-08) | üretim-araçları/kod → **0**; fiziksel tek iz = stub `--/architecture/k16-class-ab/bom-classab.md` (35 satır, çelişki notu) |
| Çelişki envanteri | **C1:** 1.775/$682 (K20) ↔ 639/$1.067/$133 (H5 + brain.md) ↔ BOM<$430 (ADR-089 aktarımı) → §4.3.1/§4.3.10 · G1 · **C2:** `brain.md` Electronics Registry `bom-classab.md` yolu diskte YOK (stub ile teyitli) → G2 |
| Durum dağılımı | DESIGN: 8 kalem (BOM · tedarik · montaj · kalibrasyon · üretim-test · QC · seri · saha) · BAĞLAYICI: 2 (kısıt "üretim araçları" · ADR-090 varyant) · IMPLEMENTED: 0 |
| Risk gerekçesi (low-medium) | doğrudan enerji/güvenlik-riski yok (yüksek risk K017); ancak ölçülebilir-maliyet/kanıt çelişkisi ve QC zinciri hassasiyeti nedeniyle "düşük-orta" |
| Web research | 0 URL bu dosyada → ⚠️ (R9 3'lü eksik; F1 EK B kapısı) |

### §4.6 K020 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K020 etkisi |
|---|---|---|
| ADR-061-electronics-architecture | Electronics Architecture (L6) — katman/kart/modül hiyerarşisi · bileşen seçim politikası | üretim-öncesi karar zinciri |
| ADR-063-hardware-design-standards | PCB kural seti · sinyal bütünlüğü & EMC · bileşen/kart kalitesi · dokümantasyon · **AES17 fabrika test hizası** | **ana karar** — üretim-test + dokümantasyon hizası |
| ADR-064-electronics-platform-architecture | Electronics Platform (L0-L6 · 5 cihaz sınıfı · cihaz↔servis eşleme) | cihaz-sınıfına göre üretim konfigürasyonu |
| ADR-089-classab-24v | Class AB Amplifikatör Sistemi (8×50W · hibrit MCU) — §1.1 "BOM <$430" + kanal-bağımsız PCB | maliyet/BOM ölçüm zinciri + lisans kısıtı (§1.3) |
| ADR-090-channel-variant-product-family | Kanal Varyant Ürün Ailesi (mono·2·2+1·4·5·6·7·8·7+1·8+1) + maliyet kademesi + SKU politikası | varyant/SKU/maliyet (§4.3.9) |
| ADR-096-kspace-5000-boundary-model | K-Space V2 · dizin deseni · çift uçak (§2.2) | PHYSICAL plane kuralı + format kaynağı |

### §4.7 K020 Arayüz Sözleşmeleri (komşularla sınır — çift uçak)

| Komşu | Arayüz | K020'nin verdiği | K020'nin beklediği | Kanıt |
|---|---|---|---|---|
| K019 PCB | üretim çıktısı (Gerber/BOM) | montaj + test + seri kaydı | üretim-dosyası + IPC Class 3 şartı | `.ai/CLAUDE.md §5 K19/H4` · ADR-063 |
| K016 AMPLIFIER | ölçüm/ölçüt | THD/gain/DC-offset ölçüm sonucu | kabul kriteri (hedef <0.005%) | `.ai/CLAUDE.md §5 K16` |
| K017 POWER | koruma/verim ölçümü | UVP/OVP/OCP/OTP eşik + %96 verim sonucu | eşik tanımı (⚠️ eksik — K017 G4) | `.ai/CLAUDE.md §5 K17` |
| K018 THERMAL | termal test | soak + 72°C cutoff doğrulaması | 41W/kanal şartı | `.ai/CLAUDE.md §5 K18` |
| K013 CI/CD | test otomasyonu hizası | üretim-test talimatı/otomasyonu | test-çalıştırma disiplini | `.ai/CLAUDE.md §5 K13` |
| K015/K009 (yazılım) | **olay/sürücü-API yalnız (senkron YOK)** | üretim/ölçüm olayları | komut kanalı (senkron bağ yasak) | ADR-096 §2.2 · R6.1 |
| Saha/kullanıcı | field service | onarım/kalibrasyon döngüsü | arıza geri-bildirimi (olay) | `00-kspace-anayasa.md §A.1 K020` |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K020 → K019 PCB | aşağı | üretim çıktısını (Gerber/BOM) tüketir | `00-kspace-anayasa.md §A.1 K020 "izinli=K000-K019"` |
| K020 → K016/K017/K018 | aşağı | ölçüm/ölçüt/koruma şartlarını tüketir (iç karara müdahale yok — §5.2) | `.ai/CLAUDE.md §5 K16/K17/K18` |
| K020 → K013 CI/CD | aşağı (hiza) | test-otomasyonu/raporlama disiplini | `.ai/CLAUDE.md §5 K13` |
| K020 → K000 OS | aşağı | süreç/araç runtime'ı | anayasa §A.1 K000 |
| K020 → bant-2+ (K021+) | **YOK** | bu dosya bant 1'in son katmanıdır; bant-2 erişimi izinli aralığın dışındadır (§5.2) | anayasa §A.0/A.1 (bant sınırı) · ADR-096 §2.2 |
| port/adapter | yan | EK A istisnası (R6.3) | `00-kspace-anayasa.md §A.1 K020` |

**Çift uçak notu (ADR-096 §2.2 · GÖREV 07):** K020 PHYSICAL PLANE'dedir. Yazılım katmanları ile
ilişki yalnız sürücü/API/olay üzerinden; **senkron doğrudan bağlantı yasaktır** (ihlal → R6.5).

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K020 → bant-2 (K021+) erişim / geri çağrı (H20) | klasik yön + bant sınırı | `rules.md R6.1` · `ADR-096 §2` |
| K015/K009/K010 ile SENKRON BAĞ | çift uçak ihlali | `ADR-096 §2.2` · `rules.md R6.1` |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali (R4.4d) | `rules.md R6.1` |
| K016-K019 iç tasarım kararlarına müdahale | katman münhasırlığı (K020 yalnız üretir/test eder/kaydeder) | `00-kspace-anayasa.md §A.1 K020` |
| Kanıtlanmamış BOM/maliyet sayısını "gerçek" sunmak | H10 (hedef ≠ kanıt) + R9 (3'lü kanıt) ihlali | `rules.md R9.4/H10` |
| AGPL/uyumsuz lisanslı CAD kaynağı kullanımı | ADR-089 §1.3 kısıtı | ADR-089 §1.3 (accepted/ ls) |
| QC atlanarak sevkiyat | üretim-güvenliği (§3/14) | `00-kspace-anayasa.md §A.1 K020` (QC) · ADR-063 |
| `SELECT *` / ORM / framework (yazılım tarafında) | ADR-001/002 mutlak yasakları | `rules.md R17` |

### §5.3 Boundary Matrisi

| Boundary | K020 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | üretim arşivi (BOM satırı · ölçüm · seri/rev · QC · tedarik emri · saha) — ürün/CI üzerinde; K005 DB'sinde değil | K005 (iş verisi) · K012 (canlı telemetri) |
| SECURITY_BOUNDARY | tedarik özgünlüğü · AES17 test güvenilirliği · seri/rev geri-izlenebilirlik · QC zinciri (atlanamaz) · lisans kısıtı | K019 (tasarım lisansı) · K013 (otomasyon) · K006 (erişim) |
| FAILURE_MODE | QC reddi/rework · kalibrasyon tekrarı · yanlış-parça recall (⚠️ politika) · saha döngüsü · BOM-eksikliği → üretememe | K016-K018 (ölçüm) · K012 (olay) |
| RUNTIME boundary | süreç/araç runtime'ı (kod yok — find 0) | K000 (runtime) · K013 (test) |
| MEASUREMENT boundary | üretim-test ölçümlerinin SAHİBİ (AES17 hizası) | K016-K019 (hedef ölçütler) · K013 (otomasyon) |
| Olay (event) yukarı serbest | üretim/ölçüm/saha olayları yukarı; senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay yayını, yukarı SERBEST)  ←  K020 üretim/ölçüm/saha olayları
      ↑                                                        │
      │ (okuma)                                      [senkron K015/K009'a YASAK]
      └────────── K008 SERVICES (Event Bus) ──────────┘
                        │
  K020 yalnız alttan beslenir: K019 çıktı · K016-K018 ölçüm şartları · K013 hiza  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K020 → K012 üretim/ölçüm olayı | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K020 → K015/K009 senkron çağrı | — | YASAK (çift uçak + H20) | `ADR-096 §2.2` · `rules.md R6.1` |
| K019 → K020 üretim çıktısı | aşağı (devir) | Gerber/BOM/ölçüm şartları | `.ai/CLAUDE.md §5 K19` |
| K016-K018 → K020 ölçüm şartı | aşağı | kabul kriterleri | `.ai/CLAUDE.md §5 K16-K18` |
| K020 → K013 test raporu | aşağı/yan (hiza) | üretim-test otomasyonu | `.ai/CLAUDE.md §5 K13` |

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K020 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K020 / bom · suppliers · assembly · test · qc · serial · service | SUBDOMAIN §3/5 |
| Module → Component | BOM modülü · tedarik modülü · montaj istasyonu · test istasyonu · seri/kayıt modülü | hedef (süreç seviyesi) |
| Service | (donanım-sonrası süreç — servis değil; araçlar kısıt ile bu katmanda) | çift uçak |
| Adapter | CAD/BOM-tool adaptörü · test-istasyonu adaptörü · ERP/saha adaptörü (⚠️) | port/adapter sıçraması |
| Döngü toleransı | sıfır (R6.6) | `rules.md R6.6` |

### §5.6 K020 Risk & Güvenlik Notları (görev notu + anayasa hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| **BOM/maliyet çelişkisi (1.775/$682 ↔ 639/$1.067 ↔ <$430)** | yanlış-maliyet/üretim-kararı | ayrı kayıt + Contradiction Gate + 👤 karar (§4.3.1 · G1) | `.ai/CLAUDE.md §5 K20/H5` · stub · ADR-089 §1.1 |
| Ölçümlerin kanıtsızlığı ("test edildi" iddiası) | sahte-kalite | hedef test tanımı + "yapıldı" DEĞİL yazımı (H10) | `rules.md H10/R16.3` |
| QC zincirinin atlanması | hatalı ürünün sahaya çıkması | PASS zorunluluğu (§3/14) + AES17 hizası | ADR-063 · `00-kspace-anayasa.md §A.1 K020` |
| Sahte/uyumsuz parça (tedarik) | güvenilirlik/güvenlik | Mouser/Digikey + lot-izlenebilirlik | `.ai/CLAUDE.md §5 K20` |
| Recall/saha döngüsü tanımsız | arızanın yayılması | seri/rev + politika kararı (⚠️ G4) | `00-kspace-anayasa.md §A.1 K020` |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| BOM oluşturma | tasarım-BOM + varyant (ADR-090) | 1.775 satır hedefi BOM + rev | çelişki §4.3.1 |
| Tedarik | BOM + Mouser/Digikey | parça emri + lot kaydı | özgünlük |
| Montaj | parça + kart + mekanik | üretilir ürün | §4.3.3 |
| Kalibrasyon | ürün + ölçüm | ayar-değerleri + kayıt | §4.3.4 |
| Üretim testi | ürün + AES17 paketi | ölçüm seti (THD/empedans/termal/verim) | §4.3.5 |
| QC | ölçüm seti + kriter | kabul/reddi | §4.3.6 |
| Seri | kabul edilen ürün | seri/rev kaydı | §4.3.7 |
| Saha | arıza/geri-bildirim | teşhis → onarım → geri gönderim | §4.3.8 |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| BOM hedefi | 1.775 satır · ~$682 (K20) · 639/$1.067/$133 (H5) · <$430 (ADR-089 aktarımı) — ⚠️ çelişki | `.ai/CLAUDE.md §5 K20/H5` · ADR-089 §1.1 |
| Tedarik | Mouser · Digikey | `.ai/CLAUDE.md §5 K20` |
| Kısıt | "Üretim araçları dahil" | `.ai/CLAUDE.md §5 K20 kısıt sütunu` |
| Test hizası | AES17 fabrika test (ADR-063) | ADR-063 (accepted/ ls) |
| Kalite sınıfı | IPC Class 3 (K019 ile ortak) | `.ai/CLAUDE.md §5 H4` |
| Araç/kod | repo'da YOK (find 2026-10-08) · tek iz = stub bom-classab.md | repo find · stub ls |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Üretim-test ölçüm kayıtları | test-istasyonu (AES17) | PLANNED (araç yok) |
| QC karar logları | QC süreci | PLANNED |
| Seri/rev kayıtları | serialisation | PLANNED |
| Tedarik emirleri/lot | tedarik süreci | PLANNED |
| Saha-servis kayıtları | field service | PLANNED |
| Canlı telemetri (saha) | K016-K018 olayları → K012 | PLANNED (K012 metrik) |

### §6.4 Test (Hedef Test Tanımları — "yapıldı" DEĞİL)

| # | Test | Ölçüt / Kabul | Durum |
|---|---|---|---|
| 1 | AES17 üretim-test paketi | ADR-063 hizası | TANIMLI, YAZILMADI |
| 2 | THD / DC-offset doğrulama | <0.005% (§5 K16) · röle >0.5V (§23 #7) | TANIMLI, YAZILMADI (K016 §6.4) |
| 3 | Empedans ölçümü | 90Ω USB · 50Ω I2S | TANIMLI, YAZILMADI (K019 §6.4) |
| 4 | Termal-soak + cutoff | 41W/kanal · 72°C | TANIMLI, YAZILMADI (K018 §6.4) |
| 5 | Güç koruma/verim | UVP/OVP/OCP/OTP eşik · %96 | TANIMLI, YAZILMADI (K017 §6.4) |
| 6 | Seri/rev geri-izlenebilirlik | kayıt ↔ ürün eşleşmesi | TANIMLI, YAZILMADI |
| 7 | BOM↔şema tutarlılığı | 1.775 ↔ 639 uzlaştırması (G1 kararı ile) | TANIMLI (karar bekliyor) |
| 8 | Varyant kapsamı | ADR-090 seti | TANIMLI, YAZILMADI |
| 9 | IPC Class 3 muayene | sınıf-3 kriterleri | TANIMLI (K019 §6.4 #6) |
| 10 | Tedarik lot-izlenebilirliği | lot ↔ seri eşleşmesi | TANIMLI, YAZILMADI |

### §6.5 Failure Mode Senaryoları (failure=üretim/süreç hatası)

| # | Senaryo | K020 davranışı | Etki | Kanıt |
|---|---|---|---|---|
| 1 | Ölçüm kriterin dışında | QC reddi → rework/devre dışı | fire/geri çevirim | `00-kspace-anayasa.md §A.1 K020` |
| 2 | Kalibrasyon başarısız | tekrar kalibrasyon | gecikme | §4.3.4 |
| 3 | Yanlış/sahte parça | seri/batch ile geri-çekme ⚠️ (politika tanımsız) | saha riski | §5.6 · G4 |
| 4 | Saha arızası | field service döngüsü (teşhis/onarım) | garanti maliyeti | §4.3.8 |
| 5 | BOM/rev eksik veya çelişkili | üretilemez/karar beklenir | üretim durur | §4.3.1 · G1 |
| 6 | Tedarik kesintisi | alternatif-onay zorunlu ⚠️ | gecikme | §4.3.2 · G5 |
| 7 | Test atlanması (QC bypass) | **yasak ihlali** → revert + CRITICAL log | güvenlik/kalite ihlali | §5.2 · R6.5 |
| 8 | Lisans-uyumsuz CAD kaynağı | kullanım yasak → tasarım-red | hukuki risk | ADR-089 §1.3 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K020 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K020 + §5 (K20/H5/K16-K19/K13) + brain.md + stub okundu + rules.md + ADR-096 + ADR-063/089/090 başlıkları okundu |
| KAPI 9 hallucination damgası | `⚠️` ayağlar §7.1'de (BOM/maliyet uzlaştırması · süreç eşikleri · recall/alternatif politikaları · araç-kodunun yokluğu · web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — üç maliyet ölçümü ayrı yazıldı; birleştirme YOK |
| R16.3 (durum etiketi) | Uygun — tüm kalemler DESIGN/BAĞLAYICI (repo 0) |
| H10 (hedef ≠ kanıt) | Uygun — 1.775/40 hedefleri ayrı; ölçümler "hedef test" olarak işaretli |
| R8 (çelişki) | 2 kayıt: BOM/maliyet (§4.3.1 · G1) · electronics/* ölü yolları (G2) → 👤 |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) |
| K3 | Çelişki kaydı | BOM/maliyet 3 ölçüm ayrı + 👤 | dolu (§4.3.1 · §4.5 · G1) |
| K4 | Veri sınırı | üretim arşivi K020; K005 değil | dolu (§3/13 · §5.3) |
| K5 | Çift uçak | PHYSICAL + senkron-yasak yazılı | dolu (§1/§5.1/§5.4) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G7, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt (+ G1 kararı) | bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K020 - MANUFACTURING «BUZUL»` (+ §A.0 anahtar satırı: PHYSICAL) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K20 · H5 · K16-K19 · K13 satırları) | dosya yolu (vault read 2026-10-08) | K-matrix + maliyet + ölçüm bağlantıları |
| 3 | `brain.md §5` (Electronics Registry bom-classab.md kaydı: 639/$1.067/$133 — 2026-09-26) | dosya yolu (vault read 2026-10-08) | ikinci maliyet ölçümü (kaynak zinciri) |
| 4 | `--/architecture/k16-class-ab/bom-classab.md` (stub, 35 satır — "kaynak dosya diskte YOK · iki rakam farklı · Contradiction Gate") | dosya yolu (okundu 2026-10-08) | çelişki kaydı + ölü-atıf teyidi |
| 5 | repo find: üretim-araçları/kod (`*.cpp/*.c/CMakeLists`) → 0 | repo grep/find (2026-10-08) | IMPLEMENTED iddiasının yokluğu |
| 6 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-061 · ADR-063 · ADR-064 · ADR-089 · ADR-090 · ADR-096 | ADR (ls teyitli) | karar atıfları (ADR-063 = test hizası) |
| 7 | `rules.md R2/R3/R4/R6/R8/R9/R10/R16.3` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` | dosya yolu (vault read) | format + yön |
| 8 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | BOM/maliyet uzlaştırması: 1.775/$682 ↔ 639/$1.067/$133 ↔ <$430 | §4.3.1 · §4.3.10 · EK C 10 | Vault Steward kararı (R8.2 Contradiction Gate — DUR/ask) |
| G2 | `brain.md` Electronics Registry `bom-classab.md` yolu diskte YOK (yalnız stub) | §4.5 · KANIT zinciri | kaynak BOM dosyasının geri gelmesi veya atıf güncellemesi |
| G3 | Montaj/kalibrasyon/QC süreç eşikleri yok | §4.3.3-6 | süreç şartnamesi + 👤 (uydurma YASAK) |
| G4 | Recall/geri-çekme politikası tanımsız | §4.3.7 · §6.5 #3 | politika kararı + test |
| G5 | Tedarik-kesintisi/alternatif-onay kuralı yok | §4.3.2 · §6.5 #6 | tedarik politikası (⚠️) |
| G6 | Üretim-araçları (kod/sistem) yok — yalnız kısıt metni | §6.2 · EK C 7 | araç kapsamı/planı (Kapı 10) |
| G7 | Web kanıtı (URL+tarih) — tedarik/üretim/maliyet iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G8 | BOM↔CAD↔ERP entegrasyonu tanımsız | §4.3.10 · §5.5 | entegrasyon kararı (⚠️) |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2) — **G1 (BOM/maliyet) ayrıca 👤
kararı gerektirir (Contradiction Gate).**

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» (senkron bağ YOK — sürücü/API/olay, çift uçak) · K016 AMPLIFIER «ZAR» ·
K017 POWER «KANTAR» · K018 THERMAL «MEZİT» · K019 PCB «ALEV» (üretim çıktısını veren).
Üst/sağ (H20 yasak yönü): bant-1 içinde K020'den sonraki katman YOK (bant 1'in son katmanıdır);
erişim yalnız bant-2 (K021+) yönünde ve o da izinli aralık dışındadır (§5.1).
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
| b1-K019-pcb.md | K019 PCB «ALEV» | band-1 setinin parçası |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | bu dosya (draft) |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
