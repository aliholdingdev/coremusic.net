---
title: "K000 OS — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K000-isletim-sistemi/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K000-isletim-sistemi
ssot: true
risk: medium
owner: win-sw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K000 OS — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | SOFTWARE (EK A §A.0) |
| Teatral epitet | «TEMEL TAŞI» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `win-sw` (Windows Software Engineer — AGENTS.md §4 madde 11) |
| Üretim yeri | staging (`b1-K000-isletim-sistemi.md`) → hedef `.ai/architecture/K000-isletim-sistemi/index.md` |

> **Durum etiketleri (F1 §5.4):** bu dosyadaki her mimari iddia ya `[CURRENT]`
> (repo kanıtlı), ya `[TARGET]` (onaylanmış hedef), ya `[PROPOSED]` (bu kartın
> kendisi), ya `[PLANNED]`/`[DESIGN]` (kanıtsız plan) ya da `[VERIFY REQUIRED]`
> etiketlidir. Etiketsiz iddia üretilmez; hedef ≠ kanıt ayrı yazılır (H10).

---

## Bölümler

> **Additive derinlik (R3.1 · ADR-096 k13-16):** `index.md` bölünmez/dokunulmaz;
> derinlik aşağıdaki 4 deep dosyadadır. Bu bölüm yalnızca gezinme tablosudur.

| Bölüm | Deep dosya | Kapsam |
|---|---|---|
| §2 · §3 · §6 | [[kimlik-karti]] | Özet satır 16 alan + EK C 20 alan + runtime/observability-test özeti |
| §1 · §4 | [[sorumluluk]] | EK A yükümlülük maddeleri, 12 kalem sorumluluk envanteri, kapsam dışı, tier matrisi |
| §5 · §6.6 (YARGI · port/adapter · ihlal) | [[bagimlilik-sinir]] | İzinli/yasak bağımlılık, DATA/SECURITY/FAILURE sınırı, port/adapter sözleşmesi, YARGI 1-6 |
| §7 · §6.7 (⚠️ defteri) | [[kanit-kaynaklari]] | 3'lü kanıt zinciri, denge kuralı, kanıtsız madde defteri, R14 araştırma kapısı |

---

#### §1 Genel Bakış

K000, CoreMusic K-space'inin **kök katmanıdır**: işletim sistemi runtime'ı,
çekirdek servisler, süreç/iş parçacığı yönetimi, dosya sistemi, bellek,
ağ yığını, cihaz soyutlaması ve konteyner runtime'ı bu katmanda tanımlanır
(EK A §A.1 K000 kartı). Katman, bandın tek **alt katmanı olmayan** üyesidir
(izinli = `—`, kök); üzerine çıkan her katman (K001…K020) yalnızca
port/adapter ile buraya iner. Kanonik K-ID `K000` asla değişmez (R2.3).

### 1.1 Kapsam Dışı (K000 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | Donanım tasarımı, PCB, BOM, güç/termal | K001 (kesişim) + K016-K020 (fiziksel uçak) | F1 §8.5 · EK A §A.0 uçak sütunu |
| 2 | Sürücü protokolleri (ASIO/WASAPI/ALSA/I2S…) | K002 | EK A §A.1 K002 kartı |
| 3 | Ses işleme / DSP / EQ zinciri | K003 | EK A §A.1 K003 kartı |
| 4 | Model/öneri/kişiselleştirme | K004 | EK A §A.1 K004 kartı |
| 5 | Kalıcı depolama, şema, cache, backup | K005 | EK A §A.1 K005 kartı |
| 6 | Kimlik, yetki, kripto, denetim kararı | K006 | EK A §A.1 K006 kartı · §5 K6 "asla bypass edilemez" |
| 7 | Log/metrik/trace üretim kuralları | K012 | EK A §A.1 K012 kartı (K000 yalnız taşıyıcı) |
| 8 | Ağ protokolleri (HTTP/WebSocket/WebRTC…) | K014 | EK A §A.1 K014 kartı |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K000 | OS | «TEMEL TAŞI» | PLATFORM | desktop · server · embedded · edge | OS Runtime · Kernel Services · Process/Thread · Filesystem · Memory · Networking · Device Abstraction · Container Runtime (EK A, 8 madde) | syscall/IO talebi · süreç yaşam döngüsü · cihaz enumerasyonu · konteyner başlangıç çağrısı | dosya · soket · iş parçacığı/zamanlayıcı · exit kodu · cihaz handle'ı | `—` (kök; alt katman yok) · port/adapter ile aşağı inilir | üst katmana doğrudan erişim (K001-K020) · geri çağrı (H20) · katmanlar arası doğrudan veri paylaşımı (H19) | çekirdek veri sınırı (EK A) — üst katmanlarla tablo/dosya paylaşımı YOK | security=ORTA (EK A) · OWASP A02/A08 arayüzü (F1 §9.1) · SECVIEW: firmware/asset imzası (F1 SEC15) | fail-over (EK A) · çekirdek çöküşünde yeniden başlatma; veri bütünlüğü üst katmanın sorumluluğu | süreç/cpu/bellek sayaçları · syscall hata sayımı · boot sağlık durumu (TASARIM — metrik adları [PLANNED]) | platform matrisi testi (Tier1-5) · süreç izolasyonu · dosya sistemi dayanıklılık (TASARIM [PLANNED]) | `.ai/architecture/00-kspace-anayasa.md` satır 69-72 · `.ai/CLAUDE.md` §5 K0 satırı (satır 121) · ADR-019/ADR-082 · ⚠️ repo kanıtı YOK (dosya yolu+satır bulunamadı) |

**Kontrat notu:** 16 alanın tamamı doludur (R4.1). `KANIT` alanı 3'lü format
zorunluluğunu (R9.5) karşılar: `dosya yolu+satır | URL+tarih | ⚠️` — bu kartta
repo ayağı `⚠️` ile, anayasa/ADR ayağı dosya yolu ile doludur; web ayağı için
bkz. §7.

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K000` | EK A §A.0 anahtar tablosu — kimlik asla değişmez (R2.3) |
| 2 | KANONİK_AD | `OS` | EK A §A.0 kanonik ad sütunu |
| 3 | TEATRAL_EPİTET | «TEMEL TAŞI» | EK A §A.0 epitet sütunu — yalnız sıfat (R2.3) |
| 4 | DOMAIN | `PLATFORM` | EK A Sorumluluk kapsamı (runtime/kernel/fs) — domain adı katman kartından türetildi |
| 5 | RUNTIME | desktop · server · embedded · edge | EK C enum'u (F1 §EK C) + §13 tier/§14 deployment modları |
| 6 | SORUMLULUK | 8 madde | EK A §A.1 K000 Sorumluluk satırı — birebir |
| 7 | GİRDİ | syscall/IO · süreç komutu · cihaz enum · konteyner çağrısı | Sorumluluk maddelerinden türetildi (tasarım) |
| 8 | ÇIKTI | dosya · soket · iş parçacığı · exit kodu · handle · health | Aynı türetme; her çıktı bir üst katmana gider |
| 9 | İZİNLİ_BAGIMLILIK | `—` (kök) | EK A satır 71 "izinli=— (kök)" |
| 10 | YASAK_BAGIMLILIK | K001-K020 doğrudan · H20 · H19 | EK A satır 71 "yasak=üst katmana doğrudan erişim" + F1 H19/H20 |
| 11 | DATA_BOUNDARY | çekirdek veri sınırı | EK A satır 71 "data=çekirdek veri sınırı" |
| 12 | SECURITY_BOUNDARY | ORTA · A02/A08 | EK A satır 71 "security=ORTA" + F1 §9.1 eşlemesi |
| 13 | FAILURE_MODE | fail-over | EK A satır 71 "failure=fail-over" |
| 14 | OBSERVABILITY | süreç/cpu/bellek · syscall hata · boot health | EK A'da alan yok → tasarım; [PLANNED] etiketli, K012'ye devredilir |
| 15 | TEST | platform matrisi · izolasyon · dayanıklılık | §13 tier listesinden türetildi; kapsam hedefi §17 |
| 16 | KANIT | anayasa+ADR+`⚠️` | R9.5 3'lü format — bu kartta repo ayağı eksik |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3)

Band-1 FOUNDATION kartı olduğu için tam kart **zorunludur** (R4.2 — kritik
katman; ayrıca bu görev kapsamında 7 katmanın tamamı için tam kart üretilir).

```yaml
K-ID:               K000                          # kanonik, asla değişmez (R2.3)
KANONİK_AD:         OS
TEATRAL_EPİTET:     «TEMEL TAŞI»                  # yalnız epitet; K-ID'yi ezmez
DOMAIN:             PLATFORM
SUBDOMAIN:          os-runtime                    # EK A Sorumluluk 1-2 (runtime + kernel)
BOUNDED_CONTEXT:    platform-host                 # EK A Sorumluluk 3-7 (süreç/fs/bellek/AĞ/cihaz)
RUNTIME:            desktop · server · embedded · edge
SORUMLULUK:         OS Runtime · Kernel Services · Process/Thread · Filesystem ·
                    Memory · Networking · Device Abstraction · Container Runtime
GIRDI:              syscall/IO talebi · süreç yaşam döngüsü komutları · cihaz
                    enumerasyonu · konteyner başlangıç çağrısı · üst katman
                    (K001+) port/adapter çağrıları
CIKTI:              dosya · soket/bağlantı · iş parçacığı ve zamanlayıcı ·
                    exit kodu · cihaz handle'ı · boot/health durumu
IZINLI_BAGIMLILIK:  []                            # kök — alt katman yok (EK A: izinli=—)
YASAK_BAGIMLILIK:   [K001..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19]
DATA_BOUNDARY:      çekirdek veri sınırı — üst katmanlarla paylaşılan tablo/dosya YOK
                    (YARGI 2 · F1 §8.4)
SECURITY_BOUNDARY:  security=ORTA (EK A) · OWASP A02:2025 Security Misconfiguration
                    + A08:2025 Integrity'e arayüz (F1 §9.1) · imzalı firmware/asset
                    (F1 SEC15) · devre dışı: K6 bypass (asla)
FAILURE_MODE:       fail-over (EK A) — çekirdek/süreç arızasında yeniden başlatma;
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

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 alan doldu (18 EK C alanı + `kanit-tarihi` (R9.3) + `kart-durumu` (R16.2)) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI boş küme (kök); YASAK üst katman kümesi → kesişim ∅ |
| (c) KANIT `⚠️` ise research kapısı | KISMİ — web ayağı `⚠️`; R14 research kapısında doldurulacak |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — çekirdek veri sınırı K000'e münhasır |

**Epitet kalite notu:** «TEMEL TAŞI» epiteti EK A §A.0 anahtar tablosundan
alınmıştır; başka bir kaynakta epitet farklıysa EK A kazanır (R8.1 — anayasa
üstünlüğü). Epitet kimliği ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K000)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | OS Runtime | Üst katmanların (K001+) çalışacağı kullanıcı-uzayı runtime'ını sağlar; süreç başlatma/bitiştirme, sinyal ve zamanlayıcı semantiği | PLANNED | anayasa satır 70 (Sorumluluk 1); repo'da runtime'a ait dosya yolu **bulunamadı** → `⚠️` |
| 2 | Kernel Services | Dosya/ağ/süreç sistem çağrılarını üst katmana port/adapter ile sunar | PLANNED | anayasa satır 70; `⚠️` |
| 3 | Process/Thread | İş parçacığı yaşam döngüsü, öncelik/sınıflandırma (ses thread'i ayrı — F1 §8.10 P06, EK B #10) | PLANNED | anayasa satır 70; F1 §8.10 P06 (EK B #10) |
| 4 | Filesystem | Dosya oluşturma/okuma/izin; medya kitaplığı dosya yüzeyi K015'e aittir, K000 yalnız erişim katmanıdır | PLANNED | anayasa satır 70; sınır = EK A Sorumluluk 4 |
| 5 | Memory | Bellek ayırma/bırakma arayüzü; üst katmanlarda `zero-allocation` kuralı bu arayüzün üstünde tanımlıdır (CLAUDE §19 C++ guardrails) | PLANNED | `.ai/CLAUDE.md` §19; anayasa satır 70 |
| 6 | Networking | Soket/çok-protokollü yığın erişimi; protokol detayı K014 NETWORK'e aittir | PLANNED | anayasa satır 70; sınır = EK A Sorumluluk 6 |
| 7 | Device Abstraction | Cihaz enumerasyonu/erişimi; **donanım-yazılım kesişimi yalnız K002 sürücü katmanındadır** (F1 §8.5) | PLANNED | anayasa satır 70; F1 §8.5 "Kesişim: YALNIZ K2" |
| 8 | Container Runtime | Konteyner başlatma/sağlık; CoreMusic'te Docker hedefidir | PLANNED | `.ai/CLAUDE.md` §12 Containerization = Docker 24+ (hedef) · **repo'da Dockerfile/docker-compose `git ls-files` = 0 (2026-10-08 ölçümü)** → `⚠️` |

### 4.2 Anayasa §5 K0 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5)

§5 tablosunun K0 satırı: **Kapsam = "Win12, Lin10, Mac8, RPi5, ReactOS, Docker,
Cross-Platform API" · Bileşen = 50 · Hard Guardrail = "Alt seviye işletim sistemi
çekirdek servisleri"** (satır 121). Bu satırın açılımı:

| Alt kapsam | Ne kapsar | Durum | Kanıt |
|---|---|---|---|
| Win12 | Windows 11 / Server 2012 R2+ hedef ailesi (Tier 1 — "XP-11") | TARGET | `.ai/CLAUDE.md` §13 Tier 1 satırı |
| Lin10 | Ubuntu / Debian / Fedora (Tier 2) | TARGET | `.ai/CLAUDE.md` §13 Tier 2 satırı |
| Mac8 | macOS Monterey–Sonoma (Tier 3) | TARGET | `.ai/CLAUDE.md` §13 Tier 3 satırı |
| RPi5 | Raspberry Pi 5 / ARM64 (Tier 4) | TARGET | `.ai/CLAUDE.md` §13 Tier 4 satırı |
| ReactOS | Deneysel tier (Tier 5) | TARGET · sınırlı | `.ai/CLAUDE.md` §13 Tier 5 satırı ("⚠️ Experimental" — durum metni §13'te) |
| Docker | Konteyner çalışma zamanı | PLANNED | §12 Containerization = Docker 24+ · repo kanıtı yok (`git ls-files` Dockerfile = 0) |
| Cross-Platform API | Katmanlar arası platform soyutlama katmanı | DESIGN | `.ai/CLAUDE.md` §5 K0 hücresi; ayrıntı dokümanı **yok** → `⚠️` |
| "50 bileşen" | §5 sayım sütunu | **HEDEF** | H10: "50" hedef sayımıdır, kanıt değildir — herhangi bir repo sayımıyla eşleştirilmez |

> **5 Katman (§5.1) ilişkisi:** L0 = bu katmandır. L1→L0 izinli, L0→L2/L3
> **yasak** (`.ai/CLAUDE.md` §5.1 — Layer Violation → derhal revert + log CRITICAL).

### 4.3 Bileşen / kalem envanteri (derinlik — her kalem tek satır)

| Kalem | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Boot & init | Sistem açılış sırası, servis registrasyonu | PLANNED | `⚠️` (repo kanıtı yok) |
| Process table | Süreç kaydı, PID, durum makinesi | PLANNED | `⚠️` |
| Scheduler arayüzü | Öncelik/gerçek-zaman sınıfı isteği (ses için: F1 §8.10 P06 → Real-Time Work Queue, EK B #10) | DESIGN | F1 §8.10 P06 · EK B #10 (2026-10-08, güven 96) |
| Memory manager arayüzü | ayırma/bırakma, koruma bölgeleri | PLANNED | `⚠️` |
| VFS | Sanal dosya sistemi soyutlaması | PLANNED | `⚠️` |
| Socket layer | Bağlantı kurma/kapama, adres ailesi | PLANNED | `⚠️` |
| Device enumeration | Cihaz listesi çıkarma (USB/BT yüzeyi K002'ye devredilir) | PLANNED | F1 §8.5 kesişim kuralı |
| Container runtime | Konteyner lifecycle (Docker) | PLANNED | §12 hedef · repo 0 dosya |
| Signal / timer | Zamanlayıcı ve sinyal teslimi | PLANNED | `⚠️` |
| Cross-Platform API | Platform farklarını üst katmana gizleyen arayüz | DESIGN | §5 K0 hücresi · `⚠️` |
| Health / reboot | Sağlık kontrolü ve yeniden başlatma (fail-over ucu) | DESIGN | EK A failure=fail-over · `⚠️` |
| Log sink (alt seviye) | Çekirdek günlüğü — **kural üretimi K012'ye ait**, K000 yalnız taşıyıcı | DESIGN | R7.1 tek owner; EK A K012 |

**Sayım disiplini:** §4.3'te 12 kalem listelenmiştir; bu, §5'in "50 bileşen"
hedefiyle **karıştırılmaz** (H10 — hedef ≠ kanıt ayrı raporlanır).

### 4.4 EK A K000 bloğunun birebir alıntısı (kaynak metin)

```text
### K000 - OS «TEMEL TAŞI» Bant: K000-K020
- Sorumluluk: OS Runtime · Kernel Services · Process/Thread · Filesystem · Memory · Networking · Device Abstraction · Container Runtime
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=— (kök) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 69-72 (EK A §A.1).
Alıntı değiştirilmemiştir (In-Place · H4/R7). Bu kartın §2-§6 alanlarının
tamamı bu üç satırdan türetilir; türetilen tasarım alanları [DESIGN]/[PLANNED]
etiketlidir.

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı | süreç tablosu, dosya tanıtıcısı, soket durumu — üst katman tablolarıyla paylaşılmaz | §5.3 |
| `security` | ORTA | A02/A08 arayüzü; K006 bypass'ı yok; firmware imzası (SEC15) | §5.4 |
| `failure` | fail-over | süreç/çekirdek arızasında yeniden başlatma; fail-open yalnız ADR ile | §5.5 |
| `izinli` | `—` (kök) | alt katman yok; üst katmanlar port/adapter ile aşağı iner | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K001-K020'ye doğrudan erişim + H20 + H19 | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | EK A'nın kendi kanıt satırı — web ayağı bu kartta ayrı `⚠️` | §7 |

### 4.6 Eski §5 K0-K2 satırları ↔ EK A K-ID eşlemesi (okuma köprüsü)

| Eski §5 satırı (`.ai/CLAUDE.md` §5) | Yeni EK A K-ID | Not |
|---|---|---|
| **K0** İşletim Sistemi (satır 121) | **K000** | bu dosya; 50 bileşen = hedef (H10) |
| **K1** Donanım (satır 120) | **K001** | EK A'da uçağı SOFTWARE (GÖREV 07 kesişim) |
| **K2** Sürücü (satır 119) | **K002** | uçağın SOFTWARE/PHYSICAL kesişimi (F1 §8.5) |
| §5.1 L0 → L2/L3 yasağı (satır 182) | K000 → K002/K003 yasağı | aynı hüküm, yeni K-ID ile |

>Eski K0-K20 numaralandırması salt-okunur analiz girdisidir (ADR-096 §2.9 ·
R18); bu kartta yalnızca **okuma köprüsü** olarak taşınır, kimlik olarak
kullanılmaz.

---

### 4.7 Platform tier × K000 yetenek matrisi (§13 bağlamı)

| Tier | OS | K000 yeteneği (hedef) | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Windows (XP-11, Server 2012 R2+) | ASIO/WASAPI yolu için cihaz/çalışma zamanı (sürücü ayrı: K002) | TARGET | `.ai/CLAUDE.md` §13 Tier 1 |
| 2 | Linux (Ubuntu, Debian, Fedora) | ALSA/PipeWire yolu için çalışma zamanı | TARGET | §13 Tier 2 |
| 3 | macOS (Monterey–Sonoma) | CoreAudio yolu için çalışma zamanı | TARGET | §13 Tier 3 |
| 4 | Raspberry Pi (ARM64) | Gömülü çalışma zamanı (I2S yüzeyi K002'de) | TARGET | §13 Tier 4 |
| 5 | ReactOS | Sınırlı / deneysel | TARGET · sınırlı | §13 Tier 5 ("Experimental" notu) |
| — | Docker (konteyner) | Konteyner runtime | PLANNED | §12 hedef · repo Dockerfile = 0 |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = `—` · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| — | alt katman | **YOK** — K000 kök katmandır (EK A satır 71: "izinli=— (kök)") |
| Donanım/firmware (K001 üstü) | port/adapter | Yalnız port/adapter ile **aşağı inilir**; K000 doğrudan donanıma erişmez, erişim K002'de | 
| Üst katmanlar K001-K020 | gelen çağrı | Üst katmanlar K000'i port/adapter üzerinden çağırır (yön: yukarı → aşağı) |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K001-K020 katmanlarına **doğrudan erişim** | EK A satır 71 · R6.1 | Layer Violation → derhal revert + log CRITICAL (`.ai/CLAUDE.md` §5.1) |
| Aşağı katmandan yukarı **geri çağrı** (H20) | F1 §5.1 H20 · R6.1 | Yalnız port/adapter ile — bağımlılık inversiyonu ihlali |
| Komşu katmanla **doğrudan DB/veri paylaşımı** (H19) | F1 §5.1 H19 · YARGI 2 | data boundary ihlali |
| L0 → L2/L3 | `.ai/CLAUDE.md` §5.1 | Katman ihlali (açıkça yasaklanmış tek L0 satırı) |
| Web/UI katmanının doğrudan donanım katmanına erişimi | F1 §8.5 | K000 üzerinden değil, K002 üzerinden |
| Servisler arası senkron doğrudan çağrı | `.ai/CLAUDE.md` §5 K8 | Olay (event) sınırı ile — K000'e de uygulanır |

**Olay (event) yukarı serbest:** R6.2 gereği K000'den üst katmanlara **olay
yayını serbesttir**; senkron çağrı yukarı yasaktır. Örnek (TASARIM): cihaz
bağlanma/çıkma olayının K002/K001'e event ile bildirilmesi.

### 5.3 DATA_BOUNDARY (YARGI 2)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 71) |
| K000'in verisi | süreç tablosu, dosya tanıtıcıları, soket durumu, cihaz envanteri — **üst katman tablolarıyla paylaşılmaz** |
| Üst katmanın verisi | medya/kişi/çalma listesi tabloları K005'e aittir; K000 o tabloları **okumaz/yazmaz** |
| Paylaşılan tablo | YOK (aynı veri sınırı iki katmandaysa YARGI ihlali — R4.4d) |
| Kalıcılık | K000 kendi durumunu kalıcı DB'ye yazmaz; kalıcılık K005'tedir |

### 5.4 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | ORTA (EK A satır 71) — K006 YÜKSEK'tir, K000 onun altında kalmaz |
| OWASP eşlemesi | A02:2025 Security Misconfiguration (boot/konteyner yapılandırması) · A08:2025 Software/Data Integrity (imzalı asset/firmware — F1 §9.1) |
| İlgili kural | F1 SEC15: firmware/asset güncellemesi imzalı; doğrulanmadan çalıştırılmaz |
| Devre dışı bırakılamaz | K006 bypass'ı (`.ai/CLAUDE.md` §5 K6: "Asla bypass edilemez") — K000'de bypass mekanizması tanımlanamaz |
| Yetki | K000 yetki kararı üretmez; yetki K006'nın yetki alanıdır |

### 5.5 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Durum |
|---|---|---|
| Süreç çöküşü | Yeniden başlatma / devralma | DESIGN |
| Kernel/servis hatası | Sistem yeniden başlatma (fail-over) | DESIGN |
| Disk/bellek yetersizliği | Üst katmanlara hata dönüşü (exception değil, durum kodu) | DESIGN |
| fail-open senaryosu | **Yok** — fail-open yalnız belgelenmiş ve ADR'li senaryoda (F1 SEC14) | — |
| Veri bütünlüğü kaybı | K000'in sorumluluğu değil; kurtarma K005 (backup) sorumluluğundadır | sınır |

### 5.6 Observability / Test sınırı

K000 yalnız **taşıyıcıdır**: log/metrik üretimi kuralları K012
OBSERVABILITY'ye aittir (R7.1 tek canonical owner). Test stratejisi
çapraz-katmandır (K013 CI/CD); K000 kendi test kapısını tanımlar ama
depoyu yönetmez.

### 5.7 Port/Adapter deseni (F1 §8.2 — K000'e uygulaması)

| Port tipi | Yön | Örnek (tasarım) | Adapter | Durum |
|---|---|---|---|---|
| Inbound (gelen) | üst katman → K000 | süreç başlatma / dosya açma portu | üst katmanın kendi adapter'ı | DESIGN |
| Outbound (giden) | K000 → donanım/çekirdek | cihaz erişim portu (asıl erişim K002'de) | K002 sürücü adapter'ı | DESIGN |
| Kural | — | adapter değiştirilirse **K000'in sözleşme değişmez** (F1 §8.2) | — | bağlayıcı |
| Kural | — | iç halka dış halkanın varlığını bilemez (F1 §8.1 temiz mimari) | — | bağlayıcı |
| Sınır | — | K000'in port'u başka bir katmanın DB'sine bağlanamaz (H19) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti: K000 → K003 gibi doğrudan üst erişim (port/adapter'sız) | R6.1 · §5.2 |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 ("Layer Violation İhlali: derhal revert + log CRITICAL") |
| 3 | `log.md`'ye CRITICAL girişi (append-only) | anayasa §5.1 · J5 |
| 4 | 👤 bilgi (insan onayı olmadan düzeltme yapılmaz) | R6.5 |
| 5 | Döngü (circular) varsa: `dep-check` exit 1; en zayıf kenar ADR'a taşınır | R6.6 |
| 6 | Kardeş ilişki `refers-to` olarak düzeltilir (K-ID düz metin) | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi (GIRDI alanının açılımı)

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| Syscall/IO talebi | K001+ üst katmanlar (port/adapter) | arayüz çağrısı | DESIGN |
| Süreç yaşam döngüsü komutu | K013/K008 (servis başlatma) | çağrı | DESIGN |
| Cihaz enumerasyonu | K002 sürücü katmanı | port/adapter | DESIGN |
| Konteyner başlangıç çağrısı | K013 deploy (Docker) | manifest | PLANNED (Dockerfile repo'da yok) |
| Boot parametresi | donanım/BIOS | yapılandırma | PLANNED |

### 6.2 Çıktı (ÇIKTI alanının açılımı)

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| Dosya tanıtıcısı / dosya içeriği | K005 (depolama), K015 (medya) | DESIGN |
| Soket/bağlantı | K014 NETWORK | DESIGN |
| İş parçacığı / zamanlayıcı | K003 (ses zamanlaması) | DESIGN |
| Exit kodu / durum | K012 OBSERVABILITY, K013 | DESIGN |
| Cihaz handle'ı | K002 | DESIGN |
| Health/boot durumu | K012 | DESIGN |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | desktop · server · embedded · edge (EK C) |
| Platform tier'ları | Tier1 Windows (XP-11, Server 2012 R2+) · Tier2 Linux (Ubuntu/Debian/Fedora) · Tier3 macOS (Monterey–Sonoma) · Tier4 RPi5 (ARM64) · Tier5 ReactOS (experimental) — `.ai/CLAUDE.md` §13 |
| Deployment modları | Home Media Center · Car Audio · Professional Studio · NAS Audio Server · DAC Control — `.ai/CLAUDE.md` §14 (hangi modun hangi tier'da çalıştığı = [TARGET]) |
| Uçak | SOFTWARE (EK A §A.0) — PHYSICAL uçak K016-K020'dedir |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | çekirdek günlüğü — üretim kuralı K012'de | DESIGN (owner K012) |
| Metric | süreç/cpu/bellek sayaçları | PLANNED — metrik adları yok, `⚠️` |
| Trace | syscall zinciri izleme | PLANNED — `⚠️` |
| Health | boot/sağlık durumu | DESIGN |
| Alert | K012 üzerinden | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| Platform matrisi | Tier1-5 her tier'da temel başlatma | başlatma hatasız | PLANNED |
| Süreç izolasyonu | Bir sürecin çöküşünün diğerini etkilememesi | izolasyon sağlanması | PLANNED |
| Dosya sistemi dayanıklılık | Yazma sırasında kesinti | veri bozulmaması | PLANNED |
| Konteyner başlatma | Docker lifecycle | konteyner health = healthy | PLANNED (repo'da Dockerfile yok) |
| Hedef kapsam | `.ai/CLAUDE.md` §17: ≥80% minimum (backend/frontend/audio/download testleri bu katmanın üstünde koşar) | ≥80% | hedef |

### 6.6 F1 §8.4 YARGI'larının K000'e uygulaması

| Yargı | Kural (F1 §8.4) | K000 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | Her katman yalnız ALTINDAKİ katmanı tanır | K000'in altı yok → **hiçbir K katmanını çağıramaz**; yalnız port/adapter ile aşağı inilir | GEÇERLİ (EK A izinli=—) |
| YARGI 2 | Her katmanın DATA BOUNDARY'si vardır; tablo paylaşımı yasak | çekirdek veri sınırı (§5.3) | GEÇERLİ |
| YARGI 3 | Her katmanın SECURITY BOUNDARY'si vardır | security=ORTA + A02/A08 (§5.4) | GEÇERLİ |
| YARGI 4 | Her katmanın OBSERVABILITY alanı vardır | süreç/cpu/bellek · syscall hata · boot health [PLANNED] | TASARIM |
| YARGI 5 | Her katmanın FAILURE_MODE'u tanımlıdır | fail-over (§5.5) | GEÇERLİ |
| YARGI 6 | İletişim servis çağrısı yerine EVENT BOUNDARY ile tercih edilir | K000 → üst katmanlara olay yayını serbest (R6.2) | GEÇERLİ (kural) |

### 6.7 Bilinen açık maddeler ve riskler (K000'e özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | K000 için repo kanıtı yok (`git ls-files` Dockerfile/C++ = 0) | tüm maddeler PLANNED/DESIGN | R14 research + üretim kapısında kod kanıtı topla |
| 2 | "Cross-Platform API" §5'te tek hücre — ayrıntı dokümanı yok | tasarım boşluğu | K000 derinleşme dosyası gerekebilir (R3.1 koşulu) |
| 3 | EK B'de K000'e doğrudan numaralı kaynak yok | web ayağı `⚠️` | EK B'ye `#38+` olarak ekle (R14.3) |
| 4 | "50 bileşen" hedefi hiçbir sayım'a bağlı değil | hedef/kanıt karışması riski | H10: hedef ve kanıt ayrı raporlanır |

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 69-72 | K000 kartı: Sorumluluk 8 madde · Sınır (data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=— · yasak=üst katmana doğrudan erişim) · Kanıt satırı | GEÇERLİ (2026-10-08) |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 45 (§A.0) | K000 · OS · «TEMEL TAŞI» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K0 satırı (satır 121) | Win12, Lin10, Mac8, RPi5, ReactOS, Docker, Cross-Platform API · 50 bileşen · "Alt seviye işletim sistemi çekirdek servisleri" | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §5.1 (satır 172-186) · §13 (Tier 1-5) · §14 (5 deployment modu) · §12 (Containerization) | Katman yönü + platform tier'ları + deployment modları | GEÇERLİ |
| 5 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-019-per-os-neva-player.md` | Per-OS NEVA player kararı — K000/K002/K003 sınırı | GEÇERLİ |
| 6 | ADR (`ls` ile görüldü) | `ADR-082-dev-environment.md` | Geliştirme ortamı kararı — K000 runtime bağlamı | GEÇERLİ |
| 7 | ADR (`ls` ile görüldü) | `ADR-032-ipc-contract-versioning.md` | Süreçler arası sözleşme sürümleme — K000 Process/Thread + K008 sınırı | GEÇERLİ |
| 8 | Kural | `.ai/architecture/rules.md` R2/R3/R4/R6/R9 (satır 33-56, 64-71, 86-92) | İsimlendirme · iskelet/min-500 · 16+20 alan · bağımlılık yönü · 3'lü kanıt | GEÇERLİ |
| 9 | Karar | `.ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md` §2.2 (satır 117-133) | Uçak ayrımı + dizin deseni (`K000-isletim-sistemi/`) | GEÇERLİ |
| 10 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.5 (satır 658-665) | SOFTWARE/PHYSICAL uçak ayrımı · K000 SOFTWARE | GEÇERLİ |
| 11 | Repo (grep) | `.ai/` ve depo kökü taraması 2026-10-08: `git ls-files \| grep -c Dockerfile = 0` · `git ls-files '*.cpp' = 0` | K000 runtime/cont. runtime için **kod kanıtı YOK** | `⚠️` (kanıt yok → durum PLANNED) |
| 12 | Web (EK B) | F1 EK B defteri (`.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §EK B, satır 7409-7508) | K000'e doğrudan numaralı kaynak **ayırt edilemedi** (defter ağırlıklı sürücü/ses/AI/güvenlik konularında) | ⚠️ VERIFICATION REQUIRED |
| 13 | Web (EK B, dolaylı) | EK B #10 — `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` (er. 2026-10-08, güven 96) | Gerçek-zamanlı iş parçacığı sınıfı (Real-Time Work Queue) — §4.3 "Scheduler arayüzü" satırı | GEÇERLİ (dolaylı) |

**3'lü kanıt dengesi (R9.2):** repo ayağı **0/3** → bu kart için iddialar
`⚠️ VERIFICATION REQUIRED` veya `PLANNED/DESIGN` etiketlidir; ADR + anayasa
ayağı dolu. **R14 research kapısında** K000'e özgü web turu (OS/runtime
kaynakları) EK B'ye `#38+` numarasıyla eklenmelidir.

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı (bu kart için) | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | YOK | 0 | `⚠️` damgası zorunlu · durumlar PLANNED/DESIGN |
| ADR (`.ai/.decisions/accepted/` — `ls` ile görülen adlar) | VAR | 3 (ADR-019 · ADR-082 · ADR-032) | kimlik/bağlam kanıtı |
| Web (URL + tarih) | VAR (dolaylı) | 1 (EK B #10) · doğrudan K000 kaynağı yok | doğrudu iddialar `⚠️` |
| Anayasa/kural/karar (bağlayıcı) | VAR | 6 kayıt (§7 no. 1-5, 8-9) | kartın ana taşıyıcısı |
| **Toplam 3'lü denge** | 1+ ayağı dolu | 3'lüden 2'si kısmi | R9.2: **2/3 → `⚠️ VERIFICATION REQUIRED`** |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 zorunlu alan dolu |
| `STALE` kontrolü (R11) | Gerekmedi | — | tüm kanıtlar 2026-10-08 tarihli |

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK, R2/R3 link ihlali)

| Yön | K-ID | İlişki |
|---|---|---|
| Bu katman | K000 | kök (izinli = —) |
| Band-1 kardeşler (alt) | — | yok |
| Band-1 kardeşler (üst) | K001 · K002 · K003 · K004 · K005 · K006 · K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 · K016 · K017 · K018 · K019 · K020 | `refers-to` (doküman ilişkisi — R6.4), **`depends-on` DEĞİL** |
| Fiziksel uçak kardeşler | K016 · K017 · K018 · K019 · K020 | uçağın PHYSICAL tarafı (F1 §8.5) — K000 SOFTWARE'da kalır |
| Doğrudan üst veri sahibi | K005 | veri kalıcılığı K005'te (H19: K000 ile paylaşılan tablo yok) |
| Güvenlik üst otoritesi | K006 | K000'de bypass yok (§5.4) |
| Gözlem üst otoritesi | K012 | K000 yalnız log/metrik taşıyıcısı (§5.6) |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K000 kartı kaynağı)
- [[architecture/rules]] — R2/R3/R4/R6/R9
- [[architecture/00-master-index]] — giriş navigasyonu

> Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check
> ihlali). Dizin adı wiki-link hedefidir, değiştirilemez (R2.4).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K000 `index.md` (staging) | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K000-isletim-sistemi/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1 — her katman dizini `index.md` taşır) |
| `.ai/architecture/K000-isletim-sistemi/*.md` (derinleşme) | yalnız gerçek karmaşıklıkta (servis/adaptör/derin modül ayrımı varsa) | koşullu (R3.1 · F2 §42) |
| `b1-K000-isletim-sistemi.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K000-isletim-sistemi` — 3 haneli K-ID + tire + Türkçe ad (R2.2) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) — bu dosya tek katman anlatımıdır | bağlayıcı |
| min-500 | mimari üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K000-isletim-sistemi/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
