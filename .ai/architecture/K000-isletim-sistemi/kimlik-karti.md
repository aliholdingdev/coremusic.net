---
title: "K000 OS — Kimlik Kartı (EK C · 20 Alan)"
type: deep
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K000-isletim-sistemi/index.md — derin dosya (ADR-096 karar 13-16 · rules R3.1)"
updated: 2026-10-08
tier: 3
domain: K000-isletim-sistemi
ssot: false
risk: medium
owner: win-sw
depends-on: [".ai/architecture/K000-isletim-sistemi/index.md", ".ai/architecture/00-kspace-anayasa.md"]
---

# K000 OS — Kimlik Kartı (EK C · 20 Alan)

**Künye**

| Alan | Değer |
|---|---|
| Dosya | `kimlik-karti.md` — katman derin dosyası 1/4 (rules R3.1) |
| Konu | EK C 20 alanlı tam kimlik kartı + alan bazlı derinlik |
| Özet sahibi | `index.md` §2 (16 alan özet satırı) — bu dosya **derinlik** katmanıdır |
| Durum | draft · kart `PROPOSED` (R16.2 — 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Sahip | `win-sw` (Windows Software Engineer) |

> **Bu dosya ne değildir:** K-ID/navigasyon/16 alan özet `index.md`'dedir ve
> **dokunulmaz** (ADR-096 karar 13-16). Buradaki içerik, `index.md` §2/§3/§6
> özetlerinin **EK C alan bazında derinleştirilmiş** hâlidir (G5 istisnası:
> aynı konu, farklı derinlik — çift kaynak değil, özet↔derinlik katmanıdır).
> Çelişkide `index.md` ve `00-kspace-anayasa.md` kazanır (R8.1).

---

## §1 Neden 20 Alan (R4.2/R4.3 · F1 M10)

K000, band-1 `EXISTING FOUNDATION` katmanıdır ve **kritik katman** sayılır:
tam EK C kartı **zorunludur** (R4.2). Özet satır (16 alan, `index.md` §2)
yönetim özeti iken; 20 alanlı tam kart, katmanın **sözleşmesidir** — her alan
bir kapıya bağlanır:

| Kapı | Hangi alanlar bağlar | Kural |
|---|---|---|
| Kalite kapısı (R4.4) | IZINLI · YASAK · KANIT · (a)-(d) | boş alan = kart eksik |
| Bağımlılık kapısı | IZINLI_BAGIMLILIK · YASAK_BAGIMLILIK | kesişim = ∅ |
| Yargı kapısı | DATA/SECURITY/FAILURE/OBSERVABILITY | §8.4 YARGI 1-6 |
| Research kapısı (R14) | KANIT `⚠️` ayağı | 2/3 → damga zorunlu |
| Onay kapısı (R10/R16.2) | kart-durumu · kanit-tarihi | PROPOSED → ACTIVE 👤 |

**20 alan = 18 EK C alanı + `kanit-tarihi` (R9.3) + `kart-durumu` (R16.2).**

---

## §2 Tam Kimlik Kartı — EK C (20 alan)

```yaml
K-ID:               K000                          # kanonik, asla değişmez (R2.3 · anayasa A.0)
KANONİK_AD:         OS                            # anayasa A.0 anahtar tablosu
TEATRAL_EPİTET:     «TEMEL TAŞI»                  # yalnız epitet; K-ID'yi ezmez (R2.3)
DOMAIN:             PLATFORM                      # runtime/kernel/fs sorumluluk kümesi
SUBDOMAIN:          os-runtime                    # EK A Sorumluluk 1-2 (runtime + kernel)
BOUNDED_CONTEXT:    platform-host                 # EK A Sorumluluk 3-7 (süreç/fs/bellek/ağ/cihaz)
RUNTIME:            desktop · server · embedded · edge
SORUMLULUK:         OS Runtime · Kernel Services · Process/Thread · Filesystem ·
                    Memory · Networking · Device Abstraction · Container Runtime
GIRDI:              syscall/IO talebi · süreç yaşam döngüsü komutları · cihaz
                    enumerasyonu · konteyner başlangiç çağrısı · üst katman
                    (K001+) port/adapter çağrıları
CIKTI:              dosya · soket/bağlantı · iş parçacığı ve zamanlayıcı ·
                    exit kodu · cihaz handle'ı · boot/health durumu
IZINLI_BAGIMLILIK:  []                            # kök — alt katman yok (anayasa: izinli=—)
YASAK_BAGIMLILIK:   [K001..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19]
DATA_BOUNDARY:      çekirdek veri sınırı — üst katmanlarla paylaşılan tablo/dosya YOK
                    (YARGI 2 · F1 §8.4)
SECURITY_BOUNDARY:  security=ORTA (anayasa) · OWASP A02:2025 Security Misconfiguration
                    + A08:2025 Integrity'e arayüz (F1 §9.1) · imzalı firmware/asset
                    (F1 SEC15) · devre dışı: K6 bypass (asla)
FAILURE_MODE:       fail-over (anayasa) — çekirdek/süreç arızasında yeniden başlatma;
                    fail-open YALNIZ ADR ile (F1 SEC14)
OBSERVABILITY:      süreç/cpu/bellek sayaçları · syscall hata sayımı · boot sağlık
                    durumu · cihaz bağ/bağlantı olayları [PLANNED — metrik adları
                    K012 OBSERVABILITY ile birlikte tanımlanacak]
TEST:               platform matrisi (Tier1-5) testi · süreç izolasyonu · dosya
                    sistemi dayanıklılık · konteyner başlatma [PLANNED]
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 69-72 |
                    .ai/CLAUDE.md §5 K0 satırı (satır 121) + §13 Platform Tiers |
                    ADR-019 · ADR-082 | web: EK B defteri bu kart için doğrudan
                    kaynak taşımıyor → ⚠️ VERIFICATION REQUIRED
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — özet satır + kanıt dolu, 👤 onaylı değil)
```

---

## §3 Alan Bazlı Derinlik (20/20 — değer · kaynak · durum)

| # | Alan | Değer (özet) | Birincil kaynak | Durum |
|---|---|---|---|---|
| 1 | K-ID | `K000` | `00-kspace-anayasa.md` satır 45 (A.0) | [CURRENT] — kimlik asla değişmez |
| 2 | KANONİK_AD | `OS` | anayasa A.0 kanonik ad sütunu | [CURRENT] |
| 3 | TEATRAL_EPİTET | «TEMEL TAŞI» | anayasa A.0 epitet sütunu | [CURRENT] — yalnız sıfat |
| 4 | DOMAIN | `PLATFORM` | EK A Sorumluluk kümesi (runtime/kernel/fs) | [PROPOSED] — karttan türetildi |
| 5 | SUBDOMAIN | `os-runtime` | anayasa satır 70 (Sorumluluk 1-2) | [PROPOSED] |
| 6 | BOUNDED_CONTEXT | `platform-host` | anayasa satır 70 (Sorumluluk 3-7) | [PROPOSED] |
| 7 | RUNTIME | desktop · server · embedded · edge | F1 EK C enum'u + §13/§14 | [TARGET] |
| 8 | SORUMLULUK | 8 madde (birebir) | anayasa satır 70 | [CURRENT] — alıntı |
| 9 | GIRDI | syscall/IO · süreç · cihaz · konteyner · port | Sorumluluk maddelerinden türetme | [DESIGN] |
| 10 | CIKTI | dosya · soket · thread · exit · handle · health | aynı türetme | [DESIGN] |
| 11 | IZINLI_BAGIMLILIK | `[]` (kök) | anayasa satır 71 "izinli=— (kök)" | [CURRENT] |
| 12 | YASAK_BAGIMLILIK | K001-K020 · H20 · H19 | anayasa satır 71 + F1 H19/H20 | [CURRENT] |
| 13 | DATA_BOUNDARY | çekirdek veri sınırı | anayasa satır 71 | [CURRENT] |
| 14 | SECURITY_BOUNDARY | ORTA · A02/A08 · SEC15 | anayasa satır 71 + F1 §9.1/§9.3 | [CURRENT] + [PROPOSED] eşleme |
| 15 | FAILURE_MODE | fail-over | anayasa satır 71 | [CURRENT] |
| 16 | OBSERVABILITY | süreç/cpu/bellek · syscall hata · boot health | **alan yok → tasarım** | [PLANNED] — metrik adları K012'de |
| 17 | TEST | platform matrisi · izolasyon · dayanıklılık | §13 tier listesi + §17 kapsam | [PLANNED] |
| 18 | KANIT | anayasa + ADR + `⚠️` | R9.5 3'lü format | repo ayağı **0/3** → damga |
| 19 | kanit-tarihi | 2026-10-08 | R9.3 zorunlu alan | güncel (STALE değil) |
| 20 | kart-durumu | PROPOSED | R16.2 — özet+kanıt dolu, 👤 onaylı değil | onay bekliyor |

**Alan 4-6 türetme notu:** `DOMAIN`/`SUBDOMAIN`/`BOUNDED_CONTEXT` alanları EK A'da
hazır gelmez; EK A Sorumluluk satırından **türetildi** (tasarım) — bu yüzden
[PROPOSED] etiketlidir ve kanıt gibi sunulmaz (H10 · F1 §5.4 zorunlu ayrım:
*çıkarım ≠ kanıt*).

---

## §4 Özet Satır (16) ↔ Tam Kart (20) Eşlemesi

| Özet satır alanı (index §2) | Tam kart karşılığı | Fark |
|---|---|---|
| K-ID · KANONİK_AD · TEATRAL_EPİTET | 1-3 | aynı |
| DOMAIN | 4-6 (DOMAIN + SUBDOMAIN + BC) | tam kart 2 alan daha açar |
| RUNTIME · SORUMLULUK · GİRDİ · ÇIKTI | 7-10 | aynı |
| İZİNLİ · YASAK | 11-12 | aynı |
| DATA/SECURITY/FAILURE BOUNDARY | 13-15 | aynı |
| OBSERVABILITY · TEST · KANIT | 16-18 | aynı |
| — (özet satırda yok) | 19 kanit-tarihi · 20 kart-durumu | yalnız tam kartta |

**Kontrat:** 16 alanın tamamı doludur (R4.1) — `index.md` §2 satırı bu
kontratın yönetim görünümüdür; 20 alanlı kart (bu dosya §2) ise imza
sözleşmesidir. İki görünüm aynı veriyi taşır, **iki farklı kaynak değildir**
(G5 özet↔derinlik).

---

## §5 Alan Gerekçeleri (R4.1 — bu kartta neden bu değer)

| # | Alan | Gerekçe (kaynak) |
|---|---|---|
| 1 | K-ID | anayasa A.0 anahtar tablosu — kimlik asla değişmez (R2.3) |
| 2 | KANONİK_AD | anayasa A.0 kanonik ad sütunu |
| 3 | TEATRAL_EPİTET | anayasa A.0 epitet sütunu — yalnız sıfat (R2.3) |
| 4 | DOMAIN | EK A Sorumluluk kapsamı (runtime/kernel/fs) — domain adı karttan türetildi |
| 5 | SUBDOMAIN | EK A Sorumluluk 1-2 (runtime + kernel) |
| 6 | BOUNDED_CONTEXT | EK A Sorumluluk 3-7 (süreç/fs/bellek/ağ/cihaz) |
| 7 | RUNTIME | F1 EK C enum'u + `.ai/CLAUDE.md` §13 tier / §14 deployment |
| 8 | SORUMLULUK | anayasa satır 70 — birebir alıntı (H4/In-Place) |
| 9 | GIRDI | Sorumluluk maddelerinden türetildi (tasarım) |
| 10 | CIKTI | aynı türetme; her çıktı bir üst katmana gider |
| 11 | IZINLI | anayasa satır 71 "izinli=— (kök)" |
| 12 | YASAK | anayasa satır 71 + F1 H19/H20 |
| 13 | DATA_BOUNDARY | anayasa satır 71 "data=çekirdek veri sınırı" |
| 14 | SECURITY_BOUNDARY | anayasa satır 71 "security=ORTA" + F1 §9.1 eşlemesi |
| 15 | FAILURE_MODE | anayasa satır 71 "failure=fail-over" |
| 16 | OBSERVABILITY | anayasa'da alan **yok** → tasarım; [PLANNED], K012'ye devredilir |
| 17 | TEST | `.ai/CLAUDE.md` §13 tier listesinden türetme; kapsam hedefi §17 |
| 18 | KANIT | R9.5 3'lü format — bu kartta repo ayağı eksik (`⚠️`) |
| 19 | kanit-tarihi | R9.3 zorunlu alan — üretim tarihi |
| 20 | kart-durumu | R16.2 — özet + kanıt dolu; 👤 onayı yok → PROPOSED |

---

## §6 GIRDI / CIKTI / RUNTIME Alan Açılımı

### 6.1 GIRDI (index §6.1 derinliği)

| Girdi | Kaynak katman | Biçim | Durum |
|---|---|---|---|
| Syscall/IO talebi | K001+ üst katmanlar | port/adapter çağrısı | DESIGN |
| Süreç yaşam döngüsü komutu | K013 (deploy) · K008 (servis) | çağrı | DESIGN |
| Cihaz enumerasyonu | K002 sürücü katmanı | port/adapter | DESIGN |
| Konteyner başlangıç çağrısı | K013 deploy | manifest (Docker) | PLANNED — repo'da Dockerfile yok |
| Boot parametresi | donanım/BIOS | yapılandırma | PLANNED |

**Kural:** hiçbir girdi doğrudan **veri tablosu** üzerinden gelmez; hepsi
arayüz/port çağrısıdır (YARGI 2 · H19).

### 6.2 CIKTI (index §6.2 derinliği)

| Çıktı | Tüketen katman | Sınır notu | Durum |
|---|---|---|---|
| Dosya tanıtıcısı / dosya içeriği | K005 (depolama) · K015 (medya) | tanıtıcı K000'in, içerik sahibi üst katman | DESIGN |
| Soket/bağlantı | K014 NETWORK | protokol detayı K014'te | DESIGN |
| İş parçacığı / zamanlayıcı | K003 (ses zamanlaması) | RT sınıfı isteği F1 §8.10 P06 | DESIGN |
| Exit kodu / durum | K012 OBSERVABILITY · K013 | kural üretimi K012'de | DESIGN |
| Cihaz handle'ı | K002 | donanım-yazılım kesişimi yalnız K002 | DESIGN |
| Health/boot durumu | K012 | alert eşlemesi K012'de | DESIGN |

### 6.3 RUNTIME

| Alan | Değer | Kaynak |
|---|---|---|
| Runtime sınıfları | desktop · server · embedded · edge | F1 EK C |
| Platform tier'ları | T1 Windows (XP-11, Server 2012 R2+) · T2 Linux (Ubuntu/Debian/Fedora) · T3 macOS (Monterey–Sonoma) · T4 RPi5 (ARM64) · T5 ReactOS (experimental) | `.ai/CLAUDE.md` §13 (satır 354-358) |
| Deployment modları | Home Media Center · Car Audio · Professional Studio · NAS Audio Server · DAC Control | `.ai/CLAUDE.md` §14 (mod × tier eşlemesi = [TARGET]) |
| Uçak | SOFTWARE (anayasa A.0) — PHYSICAL uçak K016-K020'dedir | anayasa satır 45 · F1 §8.5 |

---

## §7 OBSERVABILITY / TEST Alan Açılımı

| Alan | İçerik | Durum | Sahip |
|---|---|---|---|
| Log | çekirdek günlüğü — yalnız taşıyıcı | DESIGN | üretim kuralı **K012** (R7.1) |
| Metric | süreç/cpu/bellek sayaçları | PLANNED — metrik adı yok `⚠️` | K012 ile ortak tanımlama |
| Trace | syscall zinciri izleme | PLANNED `⚠️` | K012 |
| Health | boot/sağlık durumu | DESIGN | K012 |
| Alert | eşik/escort akışı | PLANNED | K012 |

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| Platform matrisi | Tier1-5 her tier'da temel başlatma | başlatma hatasız | PLANNED |
| Süreç izolasyonu | bir sürecin çöküşü diğerini etkilemez | izolasyon | PLANNED |
| Dosya sistemi dayanıklılık | yazma sırasında kesinti | veri bozulmaması | PLANNED |
| Konteyner başlatma | Docker lifecycle | health = healthy | PLANNED (repo 0 dosya) |
| Hedef kapsam | `.ai/CLAUDE.md` §17 — backend/frontend/audio/download testleri bu katmanın **üstünde** koşar | ≥80% | hedef (H10: hedef ≠ kanıt) |

---

## §8 Kart Kalite Kapıları (R4.4 · F1 EK C 4 kapı)

| Kapı | Soru | Sonuç (K000) |
|---|---|---|
| (a) | Her alan dolu mu? | GEÇTİ — 20/20 alan doldu (18 EK C + kanit-tarihi + kart-durumu) |
| (b) | IZINLI ∩ YASAK = ∅ mi? | GEÇTİ — IZINLI boş küme (kök) → kesişim ∅ |
| (c) | KANIT `⚠️` ise research kapısı | KISMİ — web ayağı `⚠️`; R14 research kapısında doldurulacak (bkz. `kanit-kaynaklari.md` §6) |
| (d) | Aynı veri sınırı iki katmandaysa? | GEÇTİ — çekirdek veri sınırı K000'e münhasır (YARGI 2) |

**Epитет kalite notu:** «TEMEL TAŞI» anayasa A.0'dan alınmıştır; başka kaynakta
epitet farklıysa **EK A kazanır** (R8.1). Epitet kimliği ezmez (R2.3).

---

## §9 Durum Etiketleri — Bu Kartta Uygulaması (F1 §5.4)

| Etiket | Bu kartta nereye düşer |
|---|---|
| [CURRENT] | K-ID · kanonik ad · epitet · 8 sorumluluk · sınır değerleri (anayasa satır 45/70-72 alıntısı) |
| [TARGET] | RUNTIME tier'ları · deployment modları · Docker 24+ (`.ai/CLAUDE.md` §12/§13/§14) |
| [PROPOSED] | DOMAIN/SUBDOMAIN/BC türetmeleri · GİRDİ/ÇIKTI · OBSERVABILITY/TEST · bu kartın kendisi |
| [PLANNED] | metrik adları · test kapsamı · konteyner runtime (repo kanıtı 0) |
| [DESIGN] | port/adapter yönlendirmeleri · girdi/çıkıtı eşlemeleri |
| [VERIFY REQUIRED] | web ayağı (EK B'de K000'e doğrudan kaynak yok) · repo kanıtı (`git ls-files` Dockerfile/C++ = 0) |

**Yasak dönüşümler (birbiri sessizce çevrilmez):** olgu ≠ öneri · çıkarım ≠ kanıt
· mevcut ≠ hedef · katman ≠ bileşen (F1 §5.4).

---

## §10 Kart Yaşam Döngüsü (R5 · R10 · R16.2)

```text
oluşum (R5.3: K-ID + bant + sahip + sınır + bağımlılık + kanıt)
  → PROPOSED  (özet satır + 20 alan dolu, kanıt 2/3 → ⚠️)
  → research kapısı R14 (web ayağı EK B'ye #38+ · repo kanıtı topla)
  → 👤 onay (Kapı 10 — durum geçişi R10: her seferinde insan onayı)
  → ACTIVE    (kanit-tarihi güncellenir; STALE kontrolü R11 ile periyodik)
  → deprecated (katman silinse bile ID asla yeniden kullanılmaz — R5.2)
```

- **Şu anki durum:** `PROPOSED` — özet satır ve 20 alan dolu, kanıt 2/3 (`⚠️`),
  👤 onayı **bekliyor**.
- **Kayıt formatı:** hibrit (ADR-096 §2.4) — özet satır (16) + tam kart (20).

---

## §11 EK C Boş Şablon ↔ K000 Karşılaştırması (F1 EK C)

| Şablon alanı | Şablon tipi/Notu | K000 karşılığı | Dolu mu |
|---|---|---|---|
| K-ID | K000..K5999 · asla değişmez | K000 | ✅ |
| KANONİK_AD | İNGİLİZCE KISA AD | OS | ✅ |
| TEATRAL_EPİTET | «…» · K-ID'yi ezmez | «TEMEL TAŞI» | ✅ |
| DOMAIN / SUBDOMAIN / BOUNDED_CONTEXT | alan adları | PLATFORM · os-runtime · platform-host | ✅ (türetme) |
| RUNTIME | web\|server\|desktop\|embedded\|audio\|edge | desktop · server · embedded · edge | ✅ |
| SORUMLULUK | 1-3 satır, tek odak | 8 madde (anayasa birebir) | ✅ |
| GIRDI / CIKTI | varlık listesi | §6.1/§6.2 tabloları | ✅ |
| IZINLI / YASAK | alt katmanlar / üst+veri+geriye | `[]` / K001-K020 · H20 · H19 | ✅ |
| DATA_BOUNDARY | paylaşılan tablo YOK | çekirdek veri sınırı | ✅ |
| SECURITY_BOUNDARY | OWASP madde eşlemesi | A02 · A08 · SEC15 | ✅ |
| FAILURE_MODE | fail-secure \| fail-open(ADR) \| fail-over | fail-over | ✅ |
| OBSERVABILITY | log/metric/trace | tasarımlık + [PLANNED] | ✅ (etiketli) |
| TEST | test tipi + kabul kriteri | §7 tabloları | ✅ (etiketli) |
| KANIT | 3'lü format | anayasa+ADR+`⚠️` | ✅ (2/3) |
| — | + kanit-tarihi (R9.3) + kart-durumu (R16.2) | 2026-10-08 · PROPOSED | ✅ |

---

## §12 Referanslar

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | `00-kspace-anayasa.md` satır 45 (A.0) · satır 69-72 (A.1 K000) | kimlik · sorumluluk · sınır (birincil) |
| 2 | `.ai/CLAUDE.md` §5 K0 (satır 121) · §5.1 (satır 182-186) · §12 (satır 343) · §13 (satır 354-358) · §17 | kapsam · katman yönü · Docker · tier · test ≥80% |
| 3 | F1 (`.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md`) EK C · §10.2 · §5.4 | 20 alan şablonu · 16 alan kontratı · durum etiketleri |
| 4 | `rules.md` R2/R4/R5/R9/R16 | isimlendirme · kayıt formatı · ID · kanıt · durum |
| 5 | `ADR-096-kspace-5000-boundary-model.md` §2.5 notu (karar 13-16) | çoklu-md yapısı · boyut bandı |
| 6 | `index.md` §2 · §3 · §6 | özet görünüm (özet↔derinlik) |

> **Wiki-linkler:** yalnız `[[architecture/00-kspace-anayasa]]` ·
> `[[architecture/rules]]` · `[[architecture/00-master-index]]` (index §8.2
> izinli listesi) + kendi katmanımın dosyaları. Kardeş K-ID'ye wiki-link
> **yasaktır** (hedef henüz yok — link-check ihlali).

---

**Authority:** `index.md` (SSOT) — bu dosya derinlik katmanı, `ssot: false`
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
