---
title: "K000 OS — Sorumluluk Derinliği (EK A · Kalem Envantersi)"
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

# K000 OS — Sorumluluk Derinliği

**Künye**

| Alan | Değer |
|---|---|
| Dosya | `sorumluluk.md` — katman derin dosyası 2/4 (rules R3.1) |
| Konu | EK A 8 sorumluluk maddesi · kalem envanterinin **tam derinliği** · kapsam dışı |
| Özet sahibi | `index.md` §1 / §4 — bu dosya derinlik katmanıdır |
| Durum | draft · maddeler PLANNED/DESIGN etiketli |
| Sahip | `win-sw` |

> **Türetme kuralı:** bu dosyadaki her satır ya anayasa alıntısıdır, ya
> `.ai/CLAUDE.md`/F1/ADR kaynaklıdır, ya da **[DESIGN]/[PLANNED]** etiketlidir.
> Etiketsiz iddia üretilmez (F1 §5.4 · H10).

---

## §1 Bu Dosyanın Kapsamı

| Var | Yok |
|---|---|
| EK A K000 Sorumluluk 8 maddenin madde-madde derinliği | bağımlılık matrisi → `bagimlilik-sinir.md` |
| 12 kalemlik bileşen envanteri (her kalem tek satır derin) | 20 alanlı kimlik kartı → `kimlik-karti.md` |
| `.ai/CLAUDE.md` §5 K0 satırının açılımı (8 alt kapsam) | kanıt zinciri → `kanit-kaynaklari.md` |
| Kapsam dışı (K000 ne YAPMAZ) + asıl sahip eşlemesi | 16 alan özet → `index.md` §2 (dokunulmaz) |
| Platform tier × yetenek matrisi · deployment modları | — |

---

## §2 Genel Bakış — Kök Katman Rolü

K000, CoreMusic K-space'inin **kök katmanıdır**: işletim sistemi runtime'ı,
çekirdek servisler, süreç/iş parçacığı yönetimi, dosya sistemi, bellek, ağ
yığını, cihaz soyutlaması ve konteyner runtime'ı bu katmanda tanımlanır
(anayasa §A.1 K000 kartı — satır 69-72).

| Özellik | Değer | Kaynak |
|---|---|---|
| K-ID | `K000` (değişmez) | anayasa A.0 · R2.3 |
| Uçak | SOFTWARE | anayasa A.0 (satır 45) |
| Alt katman | **YOK** — bandın tek alt katmanı olmayan üyesi (izinli = `—`) | anayasa satır 71 |
| Üst katmanlar | K001…K020 — yalnız **port/adapter** ile aşağı iner | anayasa satır 71 · F1 §8.2 |
| Sahip | `win-sw` (AGENTS agent registry §4 madde 11) | `.ai/AGENTS.md` |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) | anayasa §A |

**Tek kural cümlesi:** K000 aşağı inerken **hiçbir katmanı çağıramaz**; yalnız
port/adapter interface'i sunar (YARGI 1 — anayasa §5.1).

---

## §3 Kapsam Dışı — K000 Ne YAPMAZ (asıl sahibine devir)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | Donanım tasarımı, PCB, BOM, güç/termal | K001 (kesişim) + K016-K020 (fiziksel uçak) | F1 §8.5 · anayasa A.0 uçak sütunu |
| 2 | Sürücü protokolleri (ASIO/WASAPI/ALSA/I2S…) | K002 | anayasa §A.1 K002 kartı |
| 3 | Ses işleme / DSP / EQ zinciri | K003 | anayasa §A.1 K003 kartı |
| 4 | Model/öneri/kişiselleştirme | K004 | anayasa §A.1 K004 kartı |
| 5 | Kalıcı depolama, şema, cache, backup | K005 | anayasa §A.1 K005 kartı |
| 6 | Kimlik, yetki, kripto, denetim kararı | K006 | anayasa §A.1 K006 kartı · "asla bypass edilemez" |
| 7 | Log/metrik/trace **üretim kuralları** | K012 | anayasa §A.1 K012 kartı — K000 yalnız taşıyıcı |
| 8 | Ağ protokolleri (HTTP/WebSocket/WebRTC…) | K014 | anayasa §A.1 K014 kartı |
| 9 | Medya kitaplığı dosya **yüzeyi** | K015 | EK A Sorumluluk 4 sınırı |
| 10 | CI/CD · deploy · test depo yönetimi | K013 | `.ai/CLAUDE.md` §5 — test stratejisi çapraz-katman |

**Devir kuralı (R7.1):** her konunun **tek canonical owner'ı** vardır; K000
bu on maddede yalnız erişim/taşıyıcı rolündedir, kural üretmez.

---

## §4 EK A K000 Sorumluluk Maddeleri — Tam Derinlik (8/8)

*Kaynak: `00-kspace-anayasa.md` satır 70 (EK A §A.1 K000 Sorumluluk satırı) — birebir.*

| # | Kalem (EK A) | Ne yapar | Sınırı nerede biter | Durum | Kanıt |
|---|---|---|---|---|---|
| 1 | **OS Runtime** | Üst katmanların (K001+) çalışacağı kullanıcı-uzayı runtime'ını sağlar: süreç başlatma/bitiştirme, sinyal ve zamanlayıcı semantiği | uygulama/logic katmanı K010'da; K000 yalnız çalıştırma zemini | PLANNED | anayasa satır 70 · repo'da runtime dosya yolu **bulunamadı** → `⚠️` |
| 2 | **Kernel Services** | Dosya/ağ/süreç sistem çağrılarını üst katmana **port/adapter** ile sunar | protokol/şema üst katmanda (K009 API · K014 ağ) | PLANNED | anayasa satır 70 · `⚠️` |
| 3 | **Process/Thread** | İş parçacığı yaşam döngüsü, öncelik/sınıflandırma — ses thread'i ayrı sınıf (F1 §8.10 P06 → *Real-Time Work Queue*) | RT kuyruk **politikası** K003/K002'de; K000 yalnız OS sınıfı ataması | PLANNED (+P06 kısmi) | anayasa satır 70 · F1 §8.10 P06 (EK B #10, güven 96) |
| 4 | **Filesystem** | Dosya oluşturma/okuma/izin | medya kitaplığı dosya **yüzeyi** K015'e aittir; K000 yalnız erişim katmanıdır | PLANNED | anayasa satır 70 · EK A Sorumluluk 4 sınırı |
| 5 | **Memory** | Bellek ayırma/bırakma arayüzü | üst katmanlardaki `zero-allocation` kuralı bu arayüzün **üstünde** tanımlıdır | PLANNED | `.ai/CLAUDE.md` §19 C++ guardrails · anayasa satır 70 |
| 6 | **Networking** | Soket/çok-protokollü yığın erişimi | protokol detayı (HTTP/WS/WebRTC) K014'e aittir | PLANNED | anayasa satır 70 · EK A Sorumluluk 6 sınırı |
| 7 | **Device Abstraction** | Cihaz enumerasyonu/erişimi | **donanım-yazılım kesişimi yalnız K002 sürücü katmanındadır** (F1 §8.5) | PLANNED | F1 §8.5 "Kesişim: YALNIZ K2" · anayasa satır 70 |
| 8 | **Container Runtime** | Konteyner başlatma/sağlık — CoreMusic'te Docker hedefidir | deploy orkestrasyonu K013'te | PLANNED | `.ai/CLAUDE.md` §12 Containerization = Docker 24+ (satır 343) · **repo: `git ls-files` Dockerfile = 0 (2026-10-08)** → `⚠️` |

**Sayım:** 8/8 madde dolu (R4.1 ruhu) · 8 madde de PLANNED/partial — çünkü
K000'in repo kanıtı **0** (bkz. `kanit-kaynaklari.md` §4 ⚠️ defteri).

---

## §5 EK A K000 Bloğunun Birebir Alıntısı (kaynak metin)

```text
### K000 - OS «TEMEL TAŞI» Bant: K000-K020
- Sorumluluk: OS Runtime · Kernel Services · Process/Thread · Filesystem · Memory · Networking · Device Abstraction · Container Runtime
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=— (kök) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 69-72 (EK A §A.1).
Alıntı **değiştirilmemiştir** (In-Place · H4/R7). Bu katmandaki §2-§6
alanlarının tamamı bu üç satırdan türetilir; türetilen tasarım alanları
[DESIGN]/[PLANNED] etiketlidir.

---

## §6 `.ai/CLAUDE.md` §5 K0 Satırının Açılımı (satır 121)

§5 tablosunun K0 satırı: **Kapsam = "Win12, Lin10, Mac8, RPi5, ReactOS, Docker,
Cross-Platform API" · Bileşen = 50 · Hard Guardrail = "Alt seviye işletim sistemi
çekirdek servisleri"** (satır 121).

| Alt kapsam | Ne kapsar | Durum | Kanıt |
|---|---|---|---|
| Win12 | Windows 11 / Server 2012 R2+ hedef ailesi (Tier 1 — "XP-11") | TARGET | `.ai/CLAUDE.md` §13 satır 354 |
| Lin10 | Ubuntu / Debian / Fedora (Tier 2) | TARGET | §13 satır 355 |
| Mac8 | macOS Monterey–Sonoma (Tier 3) | TARGET | §13 satır 356 |
| RPi5 | Raspberry Pi 5 / ARM64 (Tier 4) | TARGET | §13 satır 357 |
| ReactOS | Deneysel tier (Tier 5) | TARGET · sınırlı | §13 satır 358 ("⚠️ Experimental") |
| Docker | Konteyner çalışma zamanı | PLANNED | §12 satır 343 (Docker 24+) · repo kanıtı yok |
| Cross-Platform API | Katmanlar arası platform soyutlama katmanı | DESIGN | §5 K0 hücresi; ayrıntı dokümanı **yok** → `⚠️` |
| "50 bileşen" | §5 sayım sütunu | **HEDEF** | H10: "50" hedef sayımıdır, kanıt değildir |

> **5 Katman (§5.1) ilişkisi:** L0 = bu katmandır. L1→L0 izinli,
> L0→L2/L3 **yasak** (`.ai/CLAUDE.md` satır 182-184) — ihlal: derhal revert +
> log CRITICAL (satır 186).

---

## §7 Bileşen / Kalem Envanteri — Tam Derinlik (12 kalem)

| # | Kalem | Ne yapar | Girdi → Çıktı | Durum | Kanıt |
|---|---|---|---|---|---|
| 1 | Boot & init | Sistem açılış sırası, servis registrasyonu | BIOS/boot parametresi → servis tablosu | PLANNED | `⚠️` (repo kanıtı yok) |
| 2 | Process table | Süreç kaydı, PID, durum makinesi | fork/exec → PID + durum | PLANNED | `⚠️` |
| 3 | Scheduler arayüzü | Öncelik/gerçek-zaman sınıfı isteği | öncelik isteği → OS zamanlayıcı sınıfı | DESIGN | F1 §8.10 P06 · EK B #10 (güven 96) |
| 4 | Memory manager arayüzü | ayırma/bırakma, koruma bölgeleri | boyut/koruma → blok handle | PLANNED | `⚠️` · `.ai/CLAUDE.md` §19 |
| 5 | VFS | Sanal dosya sistemi soyutlaması | yol/izin → dosya tanıtıcısı | PLANNED | `⚠️` |
| 6 | Socket layer | Bağlantı kurma/kapama, adres ailesi | adres → soket handle | PLANNED | `⚠️` — protokol K014'te |
| 7 | Device enumeration | Cihaz listesi çıkarma (USB/BT yüzeyi K002'ye devredilir) | bus taraması → cihaz envanteri | PLANNED | F1 §8.5 kesişim kuralı |
| 8 | Container runtime | Konteyner lifecycle (Docker) | manifest → konteyner health | PLANNED | §12 hedef (satır 343) · repo 0 dosya |
| 9 | Signal / timer | Zamanlayıcı ve sinyal teslimi | süre/sinyal → teslim olayı | PLANNED | `⚠️` |
| 10 | Cross-Platform API | Platform farklarını üst katmana gizleyen arayüz | platform API → tekleşik port | DESIGN | §5 K0 hücresi · `⚠️` |
| 11 | Health / reboot | Sağlık kontrolü ve yeniden başlatma (fail-over ucu) | sağlık sinyali → yeniden başlatma | DESIGN | EK A failure=fail-over · `⚠️` |
| 12 | Log sink (alt seviye) | Çekirdek günlüğü — **kural üretimi K012'ye ait**, K000 yalnız taşıyıcı | kayıt → K012 pipeline | DESIGN | R7.1 tek owner · EK A K012 |

**Sayım disiplini (H10):** burada **12 kalem** listelenmiştir; bu, §6'daki
"50 bileşen" hedefiyle **karıştırılmaz** — hedef ≠ kanıt ayrı raporlanır.

---

## §8 EK A Sınır Satırının Madde Madde Açılımı

| Sınır maddesi | EK A değeri | K000'deki karşılığı | Açıldığı dosya |
|---|---|---|---|
| `data` | çekirdek veri sınırı | süreç tablosu, dosya tanıtıcısı, soket durumu — üst katman tablolarıyla paylaşılmaz | `bagimlilik-sinir.md` §4 |
| `security` | ORTA | A02/A08 arayüzü; K006 bypass'ı yok; firmware imzası (SEC15) | `bagimlilik-sinir.md` §5 |
| `failure` | fail-over | süreç/çekirdek arızasında yeniden başlatma; fail-open yalnız ADR ile | `bagimlilik-sinir.md` §6 |
| `izinli` | `—` (kök) | alt katman yok; üst katmanlar port/adapter ile aşağı iner | `bagimlilik-sinir.md` §2 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K001-K020'ye doğrudan erişim + H20 + H19 | `bagimlilik-sinir.md` §3 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · `.ai/` vault | EK A'nın kendi kanıt satırı — web ayağı ayrı `⚠️` | `kanit-kaynaklari.md` §2 |

---

## §9 Okuma Köprüsü — Eski §5 K0-K2 Satırları ↔ Yeni K-ID

| Eski §5 satırı (`.ai/CLAUDE.md` §5) | Yeni EK A K-ID | Not |
|---|---|---|
| **K0** İşletim Sistemi (satır 121) | **K000** | bu katman; 50 bileşen = hedef (H10) |
| **K1** Donanım (satır 120) | **K001** | EK A'da uçağı SOFTWARE (F1 GÖREV 07 kesişim) |
| **K2** Sürücü (satır 119) | **K002** | uçağın SOFTWARE/PHYSICAL kesişimi (F1 §8.5) |
| §5.1 L0 → L2/L3 yasağı (satır 182) | K000 → K002/K003 yasağı | aynı hüküm, yeni K-ID ile |

> Eski K0-K20 numaralandırması **salt-okunur analiz girdisidir** (ADR-096 §2.9 ·
> R18); bu dosyada yalnız **okuma köprüsü** olarak taşınır, kimlik olarak
> kullanılmaz.

---

## §10 Platform Tier × K000 Yetenek Matrisi

| Tier | OS | K000 yeteneği (hedef) | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Windows (XP-11, Server 2012 R2+) | cihaz/çalışma zamanı (ASIO/WASAPI yolu; sürücü ayrı: K002) | TARGET | `.ai/CLAUDE.md` satır 354 |
| 2 | Linux (Ubuntu, Debian, Fedora) | ALSA/PipeWire yolu için çalışma zamanı | TARGET | satır 355 |
| 3 | macOS (Monterey–Sonoma) | CoreAudio yolu için çalışma zamanı | TARGET | satır 356 |
| 4 | Raspberry Pi (ARM64) | gömülü çalışma zamanı (I2S yüzeyi K002'de) | TARGET | satır 357 |
| 5 | ReactOS | sınırlı / deneysel | TARGET · sınırlı | satır 358 ("Experimental") |
| — | Docker (konteyner) | konteyner runtime | PLANNED | satır 343 · repo Dockerfile = 0 |

## §11 Deployment Modları (`.ai/CLAUDE.md` §14)

| Mod | K000 açısından notu | Durum |
|---|---|---|
| Home Media Center | desktop runtime sınıfı | [TARGET] |
| Car Audio | embedded runtime sınıfı | [TARGET] |
| Professional Studio | desktop + RT öncelik beklentisi (F1 §8.10) | [TARGET] |
| NAS Audio Server | server runtime sınıfı | [TARGET] |
| DAC Control | embedded + cihaz yüzeyi (erişim K002) | [TARGET] |

*Hangi modun hangi tier'da çalıştığı = [TARGET] (§14 eşlemesi); K000 için
kanıtlanmış çalışma kaydı **yok** → `⚠️`.*

---

## §12 Çelişki / Bilinmeyen Kaydı

| # | Konu | Durum |
|---|---|---|
| 1 | "50 bileşen" hedefi hiçbir repo sayımına bağlı değil | H10 — hedef ≠ kanıt |
| 2 | Cross-Platform API ayrıntı dokümanı yok | [DESIGN] + `⚠️` |
| 3 | 8 sorumluluk maddesinin hiçbiri repo'da dosya yoluyla kanıtlanamadı | PLANNED + `⚠️` → `kanit-kaynaklari.md` §5 research kapısı |
| 4 | §7 envanterindeki 12 kalem — adları `.ai/CLAUDE.md`'den geliyor, dosya karşılıkları (`device/`…`watchdog/`) diskte doğrulanmadı | PLANNED + `⚠️` |
| 5 | §10 tier matrisindeki "K000 yetenek" sütunu — ADR-096 band modelinden türetildi, K000 kartı tek tek yetenek saymıyor | [TARGET] + `⚠️` |
| 6 | §11 deployment modları (Docker ↔ Windows servis eşlemesi) — Docker satırları `.ai/CLAUDE.md` §14'te, Windows karşılığı tanımsız | [PROPOSED] + `⚠️` → ADR gerekli |
| 7 | APFS/Windows dışı platform sorumlulukları — anayasa L45'te "APFS hattı" var, sahibi atanmadı | PLANNED + `⚠️` (R7.1 tek owner ihlali riski) |

---

## §13 Referanslar

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | `00-kspace-anayasa.md` satır 45 · 69-72 | kimlik · 8 sorumluluk · sınır (birincil) |
| 2 | `.ai/CLAUDE.md` satır 121 (§5 K0) · 182-186 (§5.1) · 343 (§12) · 354-358 (§13) · §14 · §17 · §19 | kapsam · katman yönü · Docker · tier · deployment · test · C++ guardrails |
| 3 | F1 §8.5 (uçak ayrımı) · §8.10 P06 (RT kuyruk) · §10.2 (16 alan) | kesişim · scheduler · kontrat |
| 4 | `rules.md` R6/R7/R10 | bağımlılık yönü · tek owner · onay |
| 5 | `ADR-096-kspace-5000-boundary-model.md` §2.9 | eski numaralandırma salt-okunur |

---

**Authority:** `index.md` (SSOT) — bu dosya derinlik katmanı, `ssot: false`
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
