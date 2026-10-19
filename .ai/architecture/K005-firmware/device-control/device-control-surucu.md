---
node_id: K005-firmware/device-control/device-control-surucu
node_type: module
status: draft
parent_id: K005-firmware/device-control
children: []
depends_on:
  - K005-firmware/device-control/device-control-anayasa
  - K005-firmware/device-control/device-control-kapsam
  - K005-firmware/boot/boot-surucu
  - K005-firmware/firmware-bilesenler
  - K005-firmware/firmware-dogrulama
evidence:
  - "@.ai/architecture/K005-firmware/boot/boot-surucu.md §5–§6, §9, §12–§13 — okundu 2026-10-14"
  - "@.ai/architecture/K005-firmware/device-control/device-control-kapsam.md §F.1–§F.4, §M, §A.4 — okundu 2026-10-14"
  - "@.ai/architecture/K005-firmware/device-control/device-control-dogrulama.md §2, §4 — okundu 2026-10-14"
  - "@.ai/architecture/K005-firmware/firmware-bilesenler.md §11 (C6/C7), §19.3 (d1–d8) — okundu 2026-10-14"
  - "@.ai/architecture/K005-firmware/firmware-kod-ornekleri.md §4 Örnek 11 — okundu 2026-10-14"
  - "@.ai/architecture/K005-firmware/firmware-evrensel-dil.md §5 (terim 10–13) — okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "A9/§14: komutların doğrudan mı yoksa kuyruk üzerinden mi geçtiği netleşmedi ⚠️"
  - "A12/§10: hata kodu setinin tanımı yapılmadı — kod adları uydurulamaz ⚠️"
  - "T12/§5: BOOT_READY (boot-surucu §2) ↔ BOOT_OK (device-control-kapsam §F.2) bayrak adı çelişkisi çözülmedi"
group: mimari
---

# device-control-surucu — Arayüz ve Sözleşme Kuralı

> **ELI15:** Bu dosya, device-control'ün **komşularıyla konuştuğu kapılara** işaret eder.
> Anayasa "neyin sahibi olduğunu" söyler; sürücü "nasıl konuşulduğunu" söyler: bayrak adları,
> durum makinesi, girdi→çıktı→garanti sözleşmeleri, hata arayüzü ve test kapısı. Kapılar
> **sözleşmedir** — uydurma bayrak adı yok, uydurma hata kodu yok; tanımı olmayan her şey
> açık soru olarak taşınır. Kod bu dosyada da **taslak imzalıdır** (bu depoda 0 .c/.cpp/.h).

---

## §1 Amaç · Rol · Sınır

**Amaç (1 cümle):** device-control-surucu, device-control grubunun **arayüz sözleşmelerini** tek
noktada tanımlar — bayrak/olay sözlüğü, durum makinesi, komşu modüllerle girdi→çıktı→garanti
paketleri, hata kodu arayüzü, gözlem ve test kapısı.

**Rol:** anayasa'nın (device-control-anayasa §1) **somutlaştırma katmanı**. Anayasa sahiplik ve
anayasa ilkelerini verir; bu dosya o ilkelerin **konuşma dili**ni verir. Çelişki anayasa lehine
çözülür (§14.1).

**Sınır — bu dosya ŞUNLARI YAPMAZ:**

1. Register adresi, bit maskesi, RAMIO kaydı **tanımlamaz/uydurmaz** — K001'e ait (§9, Q1).
2. Röle GPIO pin no, polarity, PCM3168A register değeri **yazmaz** — hardware-owned.
3. Bus sahipliğini **tekrar tanımlamaz** → tek tanım noktası `device-control-anayasa.md` §6
   (Örnek 11); burada yalnızca **kapı** tarif edilir.
4. Katalog projesi **kod üretmez** — §4, §10, §11'deki kod blokları "TASLAK" imzalıdır.
5. Boot'un iç işleyişine **karışmaz** → boot-surucu'nun kendi sözleşmesi (§5: yalnız devir paketi).

---

## §2 Bayrak ve Olay Sözlüğü (tek tanım noktası)

> **Tek tanım kuralı:** Bir bayrak/olay adı **yalnız burada** tam sözlüksel tanıma sahiptir;
> başka dosyada ilk kez görülen ad buraya **eklenir** (ya da açık soru olur). Kaynak adları
> korunur (`device-control-kapsam.md` §F.2'nin `BOOT_OK`'u ile `boot-surucu.md` §2'nin
> `BOOT_READY`'i **farklı adlardır — birleştirilemez**; bkz T12/§5).

| # | Ad (kaynak-tam hâli) | Kaynak | Yön | Anlamı (kaynakta ne yazıyor) |
|---|---|---|---|---|
| B1 | `BOOT_OK` | `device-control-kapsam.md` §F.2 | boot → device-control | boot başarılı bayrağı (kapsam'ın adı) |
| B2 | `BOOT_READY` | `boot-surucu.md` §2 (F10) | boot → device-control | boot tamamlandı bayrağı (boot'un adı) |
| B3 | `BOOT_ERR_INTEGRITY` | `device-control-kapsam.md` §F.2 | boot → device-control | bütünlük doğrulaması hatası |
| B4 | `BOOT_ERR_ROLLBACK` | `device-control-kapsam.md` §F.2 | boot → device-control | rollback tetiklendi |
| B5 | `BOOT_ERR_HW_INIT` | `device-control-kapsam.md` §F.2 | boot → device-control | hardware init hatası |
| B6 | `DEV_READY` | `device-control-kapsam.md` §F.2 | device-control → üst | device-control hazır |
| B7 | `BUS_NACK` | `firmware-evrensel-dil.md` §5 (terim 11) | bus → device-control | address-only NACK |
| B8 | `TLMT_EVENT` | `boot-surucu.md` §5 | device-control → runtime | telemetri olayı paketi |
| B9 | `DEV_FAULT_CODE` | türetilmiş (kapsam §M + anayasa §8) | device-control → üst | birleşik hata kodu (kod seti ⚠️ tanımsız, §10) |
| B10 | `DEV_RETRY` | **yok — adı geçmiyor (uydurulmadı)** | — | kapsam'ın "tekrar dene" davranışı §F.3'te **bayrak adı vermeden** tanımlı → bayrak adı yok |

**Türetme kuralı (taslak):**

```cpp
// TASLAK — 2026-10-14 · bu depoda derlenebilir kod yok · adlar §2 tablosundaki kaynak-tam adlardır
enum class DcBootFlag : uint8_t {          // device-control giriş bayrakları
    BOOT_OK, BOOT_ERR_INTEGRITY, BOOT_ERR_ROLLBACK, BOOT_ERR_HW_INIT   // B1, B3–B5 (kapsam §F.2)
};
// B2 (BOOT_READY) AYRI bayraktır — T12 çözülünceye kadar ikisi aynı sayılır mı? CEVAP YOK (§17 Q3)
```

---

## §3 Durum Makinesi

Kaynak: `device-control-kapsam.md` §F.1 — **tek tanım noktası orasıdır**; burada yalnız
**geçiş arayüzü** (hangi girdi hangi geçişi tetikler) tarif edilir.

```
UNINITIALIZED ──(init çağrısı)──> INITIALIZING ──(DEV_READY / B6)──> READY
       │                                │                               │
       │                                ├──(BOOT_ERR_* / B3–B5)──> FAILED
       │                                └──(NACK tekrarları, §F.3)─> FAILED
       └───────────────────────────────────────────────────────────────┘
FAILED ──(reset / yeniden init)──> UNINITIALIZED        [geçişin kendisi: kapsam §F.3]
```

| # | Geçiş | Girdi (bayrak/olay) | Çıktı (garanti) |
|---|---|---|---|
| S1 | UNINITIALIZED→INITIALIZING | init çağrısı (çağırıcı: **belirsiz ⚠️** — A9/§17) | hiçlik; pin'ler HIGH/aktif-dışarı (anayasa §11/10) |
| S2 | INITIALIZING→READY | `DEV_READY` (B6) | register RMW'ye hazır; runtime'a `TLMT_EVENT` |
| S3 | INITIALIZING→FAILED | `BOOT_ERR_*` (B3–B5) veya tekrarlı `BUS_NACK` | pin'ler fail-safe (anayasa §11); `DEV_FAULT_CODE` (B9) |
| S4 | READY→FAILED | runtime NACK / timeout (süre **⚠️ tanımsız**) | fail-safe + B9 |
| S5 | FAILED→UNINITIALIZED | reset | başa sarma; sayımlar **korunur** (§12) |

> **S1-çağırıcı belirsizliği = index.md açık sorusunun taşınması:** boot mu device-control'ü
> init eder, runtime mı? **A9/§17-Q6 ⚠️.**

---

## §4 Arayüz Sözleşmeleri (girdi → çıktı → garanti)

Şablon: `firmware-bilesenler.md` §11 C6/C7 (sahiplik + tek-yön ok) ve §19.3 (d1–d8 bileşen
kabuğu) — **kapsam bu şablonları bağlar** (`device-control-kapsam.md` §A.4). Aşağıdaki
sözleşmeler kaynak-tamdır; **uydurulan sözleşme yoktur**; kaynağı olmayan satır yoktur.

| # | Sözleşme | Girdi | Çıktı | Garanti (anayasa ile kilitli) |
|---|---|---|---|---|
| K1 | Boot devir (d1↔d2) | `BOOT_*` paketi (§5) | S1–S3 geçişleri | fail-safe pin (anayasa §11); lock-free (§7/ilk 1) |
| K2 | audio-control (d4) | I2C komutu (§6) | PCM3168A register yazımı | **yalnız device-control yazar** (kural 8); zero-alloc (§7/ilk 2) |
| K3 | power-control (d5) | GPIO isteği (§7) | röle çıkış komutu | kural 10 fail-safe; polarity **UNKNOWN** |
| K4 | runtime (d2) | tick / kuyruk | `TLMT_EVENT` | lock-free, tek-yön ok (C6/C7) |
| K5 | diagnostics (d6) | sayaç okuması (§12) | telemetri | sayaç yalnız ilgili modül yazar (C7) |
| K6 | K001 (d3) | register oku/yaz | yerel GPT kopyası | **sahiplik ⚠️ belirsiz** (§9/Q1) |

**Garanti metrikleri (değer kökeni zorunlu — anayasa §14):** ölçüm kapıları dogrulama
`device-control-dogrulama.md` §2'dedir (DD-1…DD-8); kapıları **açan sayı** yalnız oradan
okunur. Dogrulama'da olmayan metrik (ör. telemetri paket latency eşiği) = **UNKNOWN**;
uydurulan eşik bu dosyaya yazılmaz.

---

## §5 Boot Devir Arayüzü (handover)

Kaynak: `device-control-kapsam.md` §F.2 (event seti) + `boot-surucu.md` §5 ( paket sahipliği
notu: *"hangi paketin nereye ait olduğu §7.1'e kadar açık (boot vs device-control)"*).

**Devir paketi (taslak) — ne taşır:**

```cpp
// TASLAK — 2026-10-14 · depoda derlenebilir kod yok · alanlar yalnız kapsam §F.2 + boot-surucu §5'ten
struct Handover {                 // boot → device-control devri
    DcBootFlag  flag;             // §2 B1/B3–B5 (+B2 çelişkisi T12)
    // mevcut alan kapsam §F.2'de tek bayrak + "detay" yok → DETAY ALANLARI YOK (uydurma yasak)
};
```

**Sözleşme:**

1. **Yön:** boot → device-control (tek-yön ok, C6/C7).
2. **Sahiplik:** paketi **boot üretir**, device-control **yalnız okur** (boot-surucu §5 notu ile
   uyumlu); üretim yeri kesinse §17-Q1 ⚠️ (kapsam §F.2 "boot girişini device-control karşılar").
3. **Ad çelişkisi (T12):** boot tarafı `BOOT_READY` (boot-surucu §2/F10), kapsam `BOOT_OK`
   (§F.2) deriyor. **İkisi aynı olay mı, ayrı mı? → CEVAP YOK.** Çözülünceye kadar:
   - katalogda **iki ad da** korunur (§2 B1/B2);
   - kodda `// T12` imzası zorunlu; tek bayrağa indirgeme **yapılmaz**.
4. **UYDURMA YASAK:** devir paketine "sürüm no", "hash", "magic" eklemek — kaynağı olmadığı için
   yasak. Bütünlük doğrulaması boot'un kendi kapısıdır (`BOOT_ERR_INTEGRITY`, B3).

---

## §6 audio-control Arayüzü (I2C / PCM3168A)

Kaynak: `device-control-kapsam.md` §A.4 (PCM3168A I2C sahibi), §F.4; anayasa §18-③/⑥.

| Yön | Ne geçer | Kural |
|---|---|---|
| device-control → audio-control | I2C komutu (register **adı**: taslakta `MUTEB`, `DEMP`, `FMT` — `firmware-kod-ornekleri.md` §4 Örnek 11'deki adlar; **değer yok, değer uydurulamaz**) | normal I2C transferi **device-control üzerinden** geçer (kural 8) |
| audio-control → device-control | "komutu işledim / NACK aldım" bildirimi | NACK = `BUS_NACK` (B7) |

**RMW (read-modify-write) arayüzü — Örnek 11'in üçü:**

1. `register_read(adres, &eski_deger)` — **1× read**;
2. `yeni = (eski_deger & ~mask) | (deger & mask)` — yalnızca hedef bitler;
3. `register_write(adres, yeni)` — **1× write**.

**Garanti (anayasa §10 yasağı 6 + Örnek 11 dersi):** **DIĞER BİTLER KORUNUR**; "tüm bitler
sıfırlanmaz". Paralel yazan var mı → Q1'e bağlıdır (§9).

**Kod bağlantısı:** RMW zincirinin I2C RMW **zincir kilidi** ile birlikte ele alındığı yer
`firmware-kod-ornekleri.md` §4 → "Cihaz register'ı **değiştirmek** I2C RMW zincir kilidi ile
birlikte ele alınmalı". Kilit **paralel-rekabetçi kilit** (ADR-038) — §7 ilkesi 4 ile kilitli.

---

## §7 power-control Arayüzü (röle GPIO)

Kaynak: `firmware-bilesenler.md` §19.3-⑥ (power-control: röle GPIO'ları, ADR-026) + anayasa §11 (kural 10).

| Alan | Arayüz | Durum |
|---|---|---|
| Röle çıkış komutu | device-control → power-control GPIO isteği (tek-yön) | arayüz **var** (⑥ kaynağı) |
| Fail-safe | çıkışlar init'te **HIGH / aktif-dışarı** (↔ amplifier OFF) | **kaynaklı** (⑥: "çıkışlar init'te HIGH / aktif-dışarı"); kural 10 karşılığı anayasa §11'de |
| Polarity | — | **UNKNOWN ⚠️** (⑥: "polarity device-control §7.1'e kadar belirsiz") → §17-Q9 |
| Pin numarası / port | — | **YOK** (uydurma yasak; hardware-owned) |

**Sözleşme K3'ün garantisi:** röle isteği hangi durumda olursa olsun (S1–S5) **güç kaynağına
zarar veren sıraya düşmez** — sıranın kendisi `firmware-kararlar.md` §2/⑥'nın işidir
(bkz anayasa §19-K25); bu dosya **sıra numarası uydurmaz**.

---

## §8 runtime Arayüzü (ring-buffer / tick / olay)

Kaynak: `firmware-bilesenler.md` §19.3-d2 (runtime: event queue + ring-buffer) + boot-surucu §5
(`TLMT_EVENT`, B8) + anayasa §7 (lock-free).

| Girdi | Çıktı | Garanti |
|---|---|---|
| tick sayacı (d2'den) | periyodik örnekleme kapısı (kaynak: bilesenler §11-② Cihaz çipi periyodik okur) | **critical-section yok** (anayasa §7/ilk 1 — audio disabled path dahil) |
| `TLMT_EVENT` (B8) | telemetri kaydı (runtime ring-buffer'ı) | taşıma/taşma davranışı **runtime'ın** — device-control **sahip değil** |
| NACK/timeout sayımı | §12 sayaçları | kuyruk **tek-yön ok** (C7); ters ok yasak |

**Taslak (imzalı):**

```cpp
// TASLAK — 2026-10-14 · depoda derlenebilir kod yok · ring-buffer boyu §F.4'tedir (256 B), işlev adları UYDURMA
void dc_on_tick();        // çağırıcı: runtime (d2) — iddia değil, §19.3 d2'nin "tick/queue" rolünden türüme
```

---

## §9 K001 Arayüzü (register sahipliği — belirsiz kapı)

> **index.md açık sorusu harfiyen taşınır:** *"hangi register'lara kimin yazdığı belirsiz ⚠️"*.

Kapı şu **çerçevedir** (uydurma yok):

| İşlem | device-control'un rolü | Belirsizlik |
|---|---|---|
| GPT register oku | **yerel kopya** okur (⑥: "register'ları **yerel kopya** üzerinden okur") | okuma **kimden** geldiği → Q1 |
| GPT register yaz | yalnızca K005 **hardware register bloğu** (anayasa §6-③, firmware-kararlar §2/③) | **diğer K001 bloklarına kim yazar? → CEVAP YOK (Q1)** |
| Periyodik yazma | bilesenler §11-②: "Cihaz çipi **periyodik** olarak okur/yazar — critical-section yok" | "cihaz çipi" = RAMIO mı device-control mü? **⚠️ belirsiz** (§17-Q10) |

**Kural:** device-control, **kendi sahibi olmadığı** register'a yazar ise bu dosya bunu
**onaylamaz** — Q1 çözülene kadar her yazma `// Q1 ⚠️` imzalıdır.

---

## §10 Hata Kodu Arayüzü

Kaynak: `device-control-kapsam.md` §M (ErrDacInit — *yalnız taslak adıdır*), §F.2/F.3, anayasa §8.

**Gerçekleşen hata sınıfları (anayasa §8 ile aynı köken — tekrar değil, arayüz):**

| Arayüz | Gösterim | Kaynak |
|---|---|---|
| Init hatası | `BOOT_ERR_INTEGRITY` / `BOOT_ERR_ROLLBACK` / `BOOT_ERR_HW_INIT` (B3–B5) | kapsam §F.2 |
| Bus reddi | `BUS_NACK` (B7) — address-only NACK (terim 11) | firmware-evrensel-dil §5 |
| Runtime hata | `DEV_FAULT_CODE` (B9) | **kod seti ⚠️ TANIMSIZ** |

**⚠️ Hata kodu seti tanımsızdır (A12, §17-Q4):** kapsam §M'de geçen `ErrDacInit` **tek ve
taslak bir addır** ("taslak adlar"). Bu setin **sayısal kodları, adları, seviyeleri
TANIMLANMAMIŞTIR**. Dolayısıyla bu dosya:

- **kod listesi uydurmaz** (ör. `0xE1`, `E_DAC_NACK` gibi bir şey YAZILAMAZ — kaynak yok);
- yalnız **taşınan arayüzü** tanımlar: üst katmana çıkan `DEV_FAULT_CODE` bir **enum'dur**,
  elemanları **Q4 çözülünce** doldurulacak (`// Q4 ⚠️` imzalı).

**Taşıma arayüzü (taslak):**

```cpp
// TASLAK — 2026-10-14 · depoda derlenebilir kod yok · elemanlar YOK — Q4 tanımsız ⚠️
enum class DevFaultCode : uint8_t { /* ... Q4'e kadar boş — eleman uydurmak yasak ... */ };
```

**NACK/timeout arayüzü:** NACK → `BUS_NACK` (B7) sayılır (§12). **Timeout süresi/tanımı
=device-control-surucu §10-②'de "timeout süresi/tanımı" diye geçiyor, kaynağı da §10-③'te
"§10-②" görünüyor (kendi-içinde çapraz ref) → süre DEĞERİ hiçbir kaynakta YOK → UNKNOWN ⚠️**
(§17-Q5). Retry üst sınırı anayasa §17-Q6'ya bağlıdır (boot'un "max 2"si **kopyalanmaz**).

---

## §11 Bellek/Tampon Arayüzü (taslak §F.4 — paylaşımlı, tek sorumlu)

Kaynak: `device-control-kapsam.md` §F.4 — bu dosya yalnızca **erişim sözleşmesini** yazar
(sahiplik anayasa §7/ilk 5'e tabidir):

| Öğe | Boy (kaynak-tam) | Yazan | Okuyan | Arayüz kuralı |
|---|---|---|---|---|
| boot sonrası register kopyası | **6 × 24 B ≈ 144 B** | boot devri (§5) → sonra device-control | device-control | tek-yön ok (C7) |
| watchdog durum bayrağı | **32 B** | runtime (d2) | device-control | lock-free ok |
| event ring-buffer | **256 B** | device-control (B8 yazar) | runtime | taşma davranışı runtime'ın |
| retry sayaçları | **16 B** | device-control (yalnız kendi yazımı, C7) | diagnostics (§12) | **transient değil, sayaç** (anayasa §18-②) |

**Garanti:** stack'te dinamik alloc **yok** (anayasa §7/ilk 2); tüm tamponlar **baştan
allocate** (③ "tüm tamponlar baştan allocate"; A1 "döngüde alloc yok"); bu arayüzde
`new/malloc` **geçmez**.

---

## §12 Gözlem / Telemetri Arayüzü

| Sayacın adı (işlevsel) | Ne sayar | Kaynak | Sağlayıcı kapı |
|---|---|---|---|
| NACK sayacı | `BUS_NACK` olayları | §2 B7 + kapsam §F.3 | telemetry, A4 (⑦ "telemetry yalnız sayaç üretir") |
| retry sayacı | tekrar denemeler (üst sınır ⚠️ Q6) | kapsam §F.3 | runtime ring-buffer |
| init hata sayacı | B3–B5 olayları | kapsam §F.2 | DEV_FAULT_CODE üretimi (§10) |
| temp/undervolt sayacı | **device-control'da KAYNAK YOK → bu dosyada yok** | — | yalnız boot-surucu §7 (boot'un sayacı) |

**Tek-yön ok kuralı (C7):** sayaç üreticisi = sahibi modül; diagnostics (d6) **yalnız okur**,
**geriye komut/güncelleme göndermez** (③ "geriye yalnız telemetri, komut değil"; C7 "ters ok
yasak"). Olay paketi `TLMT_EVENT` (B8); içerik/alanları **tanımsız** (uydurma yasak) →
§17-Q7.

**Sayılar neden bu dosyada değer vermez:** dogrulama `device-control-dogrulama.md` §2-⑥:
"ölçülen değer(ler) ile eşik(ler)" — kapıları açan **sayı oradadır**; gözlem **arayüzü** burada
tanımlıdır (sayı ≠ arayüz).

---

## §13 Test Arayüzü (DV-1…DV-12 kapı eşlemesi)

Kaynak: `device-control-dogrulama.md` §2 (DV-1…DV-12, §8–§15) + §4 (DD-1…DD-8, "Sözleşme").
Test arayüzü = **hangi sözleşme hangi DV kapısına bağlanır**:

| DV kapısı (dogrulama §2) | Bu dosyadaki sözleşme |
|---|---|
| DV-1 (önyükleme başarısı: giriş seviyesi) | K1 + §5 (devir) |
| DV-2 (watchdog besleme) | §11 satır 2 (32 B flag, d2 kaynağı) |
| DV-3 (I2C sürücüsü) | K6 + §9 (Q1 imzası zorunlu) |
| DV-4 (kayıp saat) | K2 + §6 (MUTEB/DEMP/FMT — **değer yok**) |
| DV-5 (PCM3168A init) | K2 + §6 |
| DV-6 (DC gecikme ↔ AC gecikme) | K3 + §7 (polarity ⚠️ Q9) |
| DV-7 (çıkış sırası ↔ röle açma) | K3 + §7 (sıra firmware-kararlar §2/⑥'ya bağlı) |
| DV-8 (boot → runtime tutarlılık) | §5 + §8 |
| DV-9 (SPI overrun) | K6 (okuma yolu) |
| DV-10 (DC güç doğrulama) | K3 (kaynak: bu setin "DC güç" kapsamı) |
| DV-11 (48 kHz mod değiştirme) | §8 (tick/queue; **mod davranışı device-control'un değil — belirsizse ⚠️**) |
| DV-12 (çapraz test — boot sürüm B2'ye geri alma) | §5 B4 (`BOOT_ERR_ROLLBACK`) |

**Katalog gerçeği:** DV-11 **2/6 başarısız** (kaynak: doğrulama §2, "2/6 başarısız: … DV-11 …
soğuk başlatma modda tutarlı kalıyor, sorun sıcak değiştirme durumunda"), DV-10 **1/6 kısmi**.
**Test arayüzünün yükü:** DV-11'in sıcak-değiştirme koşulu bu arayüzde **tek bir bayrak =
B10 (`DEV_RETRY`) YOKLUĞU ile ilişkilidir** (kapsam §F.3 "tekrar dene" adı vermeden) →
**bağlantı KANITSIZDIR**, §17-Q11'de açık soru olarak durur (iddia kurulmadı).

**Sözleşme testi:** DD-1…DD-8 (sözleşme listesi, doğrulama §4) bu dosyanın §4/§14
sözleşmelerine karşılık gelir; **kapı adları dogrulama'dır, uydurulmaz**.

---

## §14 Sözleşme Değişikliği Kuralı (değişmez kapılar)

> `boot-surucu.md` §12-§13'ün device-control karşılığı — boot'un kural seti **kopyalanmaz**;
> buradaki kurallar **C6/C7 + anayasa 12 + kapsam §A.4**'ten türer.

1. **Tek tanım noktası:** bayrak/olay adları **yalnız §2**'de tanımlanır; yeni ad = önce §2'ye
   eklenir (ya da açık soru olur), **sonra** kullanılır.
2. **Ad birleştirmek yasak (T12):** `BOOT_READY`↔`BOOT_OK` gibi **farklı kaynaklardaki iki ad
   tek adılamaz** — kaynak adı korunur, çelişki açık soru (§17-Q3) olarak taşınır.
3. **Yön kuralı:** her ok **tek-yön** (C6); ters ok eklemek **sözleşme değişikliğidir** →
   (a) kaynak dosyada C7 ihlali mi? denetlenir, (b) onaysız eklenmez (C7: "onaysız eklenmez").
4. **Sahiplik değişikliği yasak:** bir tampon/register'ın sahibi değişirse anayasa §6/§7
   (tek registry) üzerinden — bu dosya **sahiplik değiştirmez**, yalnız yansıtır (§1).
5. **Garanti daraltmak yasak:** bir sözleşmeden garanti **çıkarılamaz** (ör. RMW "diğer bitler
   korunur" garantisi §6'dan çıkarılamaz — anayasa §10 yasağı 6).
6. **Kod eklendiğinde taslak imzası** (§1, §4 kuralı) ve **`.c/.cpp` ekleme yasağı**
   (`device-control-kapsam.md` §A.1) birlikte geçerli — imza + dosya yok.
7. **Ölçüm metriği eklerken:** sayı **yalnız** `device-control-dogrulama.md` §2'den gelir;
   yoksa `UNKNOWN` (anayasa §14 + bu dosya §4).
8. **Bu dosyanın kendisi de sözleşme** → değişikliği §17'deki açık soruya bağlıysa
   **önce soru kapanır, sonra §X revize edilir** (kapanış kontrolü §20/3).

**Çelişki çözüm sırası:** anayasa > bu dosya (§1.3) · kaynak adı > türetilmiş ad (§14.2) ·
kanıt > seviye (`.ai/CLAUDE.md` §6/③).

---

## §15 Bağımlılık Grafiği (bu dosyanın okuyup bağlandıkları)

```text
device-control-anayasa  ─(sahiplik/ilkeler)─►  device-control-surucu
        ▲                                             │
        │ (grup ortakları)                            ├─► boot-surucu §5 (devir paketi)
device-control-kapsam  ─(§F.1–F.4, §M, §A.4)─────────┤
        │                                             ├─► firmware-bilesenler §11/§19.3 (C6/C7, d1–d8)
        └─► device-control-dogrulama §2/§4 (DV/DD) ───┤
                                                      ├─► firmware-kod-ornekleri §4 Örnek 11 (RMW)
                                                      └─► firmware-evrensel-dil §5 (terim 10–13)
```

**Yön:** bu dosya **okur** (tek-yön, C7); bağımlılık listesi `depends_on` (FM) ile hizalıdır.
**Kaynakta olmayan ok yok** (fictional ok eklenmez): `.ai/CLAUDE.md`'ye bu dosyadan **ok yok** —
CLAUDE.md bu dosyayı **hiç anmaz** (④/⑤'deki profil-listesi kuralı boot'a ait, buna
kopyalanmadı; kopyalanması için kanıt gerekir → §17-Q12).

---

## §16 Terminoloji (evrensel-dil 10–13 — bu arayüzdeki karşılığı)

Kaynak: `firmware-evrensel-dil.md` §5, terim 10–13 (④'te "device-control-surucu'nun
terminoloji §4'te **sağlaması** gerekir" deniyor → bu §, o sözün karşılığıdır). Terim 1–9
zaten anayasa §16'da; **burada 10–13 tekrarlanmaz, yalnız arayüz karşılığı verilir**:

| Terim | Bu arayüzde hangi §'de geçer | Arayüz anlamı |
|---|---|---|
| 10 **bus donanım sahipliği** | §9 (K001 kapısı) | GPT register **yerel kopya** okunur; yazma sahipliği **Q1 ⚠️** |
| 11 **address-only NACK** | §2 B7 + §10 + §12 | `BUS_NACK` = yalnız adres, veri değil |
| 12 **retry / max-retry** | §10 (üst sınır) + §12 (retry sayacı) | **max değer YOK** (Q6 ⚠️) — boot'un "max 2"si kopyalanmadı |
| 13 **telemetri sayacı** | §12 | yalnız sayaç üretimi; geriye komut yok (⑦) |

**Kapsam farkı:** kapsam §5'te terim 1–9 tekrarı vardır (kapsam-özü) — bu dosya **terim 1–9'u
tekrar etmez** (filler yasağı); 1–9 için tek nokta anayasa §16'dır.

---

## §17 Açık Sorular

> **Kural:** buradaki sorular `device-control/index.md` `open_questions` alanıyla **çakışamaz** —
> index'e **eklenebilir**, index'ten **çıkarılamaz**. İvmeler (A8, A9, A12) doğrudan taşınır.

| # | Soru | Köken | Durum |
|---|---|---|---|
| Q1 | **hangi register'lara kimin yazdığı belirsiz** — GPT (K001) bloklarında device-control'un sahipliği ne? | `device-control/index.md` open_questions (harfiyen taşındı) + anayasa §6-①/§17 | **açık ⚠️** |
| Q2 | boot devir paketi tam olarak hangi alanları içerir (kapsam §F.2 "detay" demiyor, boot-surucu §5 paket sahipliğini sonraya atıyor)? | kapsam §F.2 + boot-surucu §5 | **açık** |
| Q3 | **BOOT_READY (boot-surucu §2) ↔ BOOT_OK (kapsam §F.2)** — iki ad aynı olay mı? | boot-surucu §2 F10 ↔ kapsam §F.2 | **açık (T12)** |
| Q4 | `DEV_FAULT_CODE` kod seti (elemanlar/seviyeler) — `ErrDacInit` tek taslak ad, gerisi yok | kapsam §M + anayasa §17-A12 | **açık ⚠️** |
| Q5 | timeout süresi/tanımı değer olarak YOK (§10-② kendi-içinde `§10-③`'e bakıyor, değer yok) | kapsam §10-②/③ (çapraz ref) | **açık ⚠️** |
| Q6 | retry üst sınırı device-control'da tanımlı mı (boot'un "max 2"si mi, yoksa başka mı)? | boot-surucu §11-② ↔ kapsam §F.3 (ad yok) | **açık** |
| Q7 | `TLMT_EVENT` paket alanları (içerik şeması) | boot-surucu §5 | **açık** |
| Q9 | röle GPIO **polarity** (⑥'da "§7.1'e kadar belirsiz" diyor — §7.1 dosyası **yok**) | firmware-bilesenler §19.3-⑥ | **açık ⚠️** |
| Q10 | "cihaz çipi periyodik okur/yazar" (②) — bu device-control mü, RAMIO mı? | firmware-bilesenler §11-② | **açık** |
| Q11 | DV-11 sıcak-değiştirme başarısızlığı ↔ `DEV_RETRY` bayrak-adı yokluğu ilişkisi **kanıtsız** (iddia YOK) | doğrulama §2 DV-11 ↔ kapsam §F.3 | **açık (iddia kurulmadı)** |
| Q12 | CLAUDE.md §④/⑤ profil-listesi bu dosyaya **uygulanır mı?** (④ bunu yalnız boot-surucu'ya bağlar; device-control'a kopya kanıt yok) | CLAUDE.md §④/⑤ ↔ kapsam §5 "④'te atıf boot-surucu'ya, burada değil" | **açık (açık-soru olarak taşındı)** |
| Q13 | init çağıran kim (S1 çağırıcısı)? | kapsam §F.1 + A9 (④ "boot ile senkron döngü"), §3-S1 | **açık ⚠️** |

> **Q8 bu dosyada YOK** (sayı atlaması değil, açık-soru bulunmaması): Q8'e karşılık gelen konu
> (bus sahipliği/Odin sözleşmesi) anayasa §17-Q3'te duruyor — burada **tekrarlanmadı**.

---

## §18 Red Team İtirazları ve Cevapları

**İtiraz 1 (gerçek −2):** "§13'te DV-11 başarısızlığını §14'ün bayrak-yokluğuna bağladın —
kanıt nerede?"
→ **Cevap:** bağ **kurulmadı**: §13'te "ilişkilidir" yerine *"bağlantı KANITSIZDIR"* yazıldı,
iddia **Q11'de açık soru** olarak durdu. İtiraz **kabul edilir** (kapsam §9.3'ün
"bağlantı-iddiası = kanıt + zorunlu red-team" kuralına uyuldu).

**İtiraz 2 (gerçek −2):** "`BOOT_READY`↔`BOOT_OK` çelişkisini çözmek yerine iki adı da
tutmak nedir? Kodda iki bayrak mı olacak?"
→ **Cevap:** kod iki bayrak **istemez**; bu dosya kod **yazmaz** (taslak §2/§5 yalnız enum
tanımı). Karar (ad birleştirme) **T12 sorusu kapanınca** verilir; şimdi birleştirmek, kaynak
adlardan birini **uydurma** yapar (§14.2). İtiraz **kabul edilir, fakat sonuç ertelenir** —
erteleme **kasıtlıdır** (zero-hallucination).

**İtiraz 3 (gerçek −2):** "§10-② kendi kendine `§10-③`'ü gösteriyor — self-reference, kod
gibi görünüyor."
→ **Cevap:** bu bir **kod çapraz-refidir, uydurma DEĞİL** — kaynağı kapsam §10-②'dir; ama
**değeri yok** (§10-③'te süre yok). İtiraz **yarı-kabul**: self-ref **açıkça işaretlendi**
(§10-② satırı: "kendi-içinde çapraz ref"), değer `UNKNOWN ⚠️` olarak Q5'e taşındı.

**İtiraz 4 (gerçek −3):** "§12'de 'temp/undervolt sayacı device-control'da KAYNAK YOK' dedin —
böyle bir şeyi yok diye yazmak iddia değil mi?"
→ **Cevap:** bu **negatif kanıt iddiasıdır** ve dayanağı dosyanın **kendisidir**: kapsam,
doğrulama, bilesenler'de device-control'a ait temp/undervolt sayacı **geçmiyor**; geçtiği yer
yalnız boot-surucu §7 (boot). İddia **dosya-içi tarama** ile sınırlı tutuldu; yeni kaynak
çıkarsa revize edilir. **İtiraz reddedilir** (dayanak var).

**İtiraz 5 (gerçek −2):** "§15'te `.ai/CLAUDE.md`'ye ok yok dedin — madem ok yok, neden
çiziyorsun?"
→ **Cevap:** ok **yokluğu** da bir arayüz gerçeğidir (neden: CLAUDE.md bu dosyayı anmıyor —
④/⑤ boot'a bağlı). Çizilen ok **gizleme** değil, **dürüstlük**: `depends_on` listesine
CLAUDE.md **eklenmedi** çünkü kaynakta bağ **yok**. İtiraz **reddedilir**.

---

## §19 İddia → Dayanak

| İddia (§) | Dayanak (dosya § — okundu 2026-10-14) |
|---|---|
| §2 B1–B9 bayrak adları | `device-control-kapsam.md` §F.2 (B1, B3–B6) · `boot-surucu.md` §2 (B2) + §5 (B8) · `firmware-evrensel-dil.md` §5 terim 11 (B7) |
| §2 B10 `DEV_RETRY` **yok** | `device-control-kapsam.md` §F.3 (ad vermeden tekrar) — bayrak adı **geçmiyor** (dosya-içi tarama) |
| §3 S1–S5 geçişleri | `device-control-kapsam.md` §F.1 (dört durum) + §F.3 (retry/reset) + anayasa §11 (fail-safe) |
| §4 K1–K6 | `firmware-bilesenler.md` §11 (C6/C7) + §19.3 (d1–d8) · `device-control-kapsam.md` §A.4 |
| §5 devir paketi + T12 | `device-control-kapsam.md` §F.2 · `boot-surucu.md` §5 (paket sahipliği notu) + §2 (F10 `BOOT_READY`) |
| §6 RMW + MUTEB/DEMP/FMT | `firmware-kod-ornekleri.md` §4 Örnek 11 · `device-control-kapsam.md` §A.4 + anayasa §10 yasağı 6 |
| §7 polarity ⚠️ + HIGH/aktif-dışarı | `firmware-bilesenler.md` §19.3-⑥ · anayasa §11 (kural 10) |
| §8 ring/tick + critical-section yok | `firmware-bilesenler.md` §19.3-d2 + §11-② · anayasa §7/ilk 1 |
| §9 yerel kopya + Q1 | `firmware-bilesenler.md` §19.3-⑥ ("yerel kopya üzerinden okur") · anayasa §6-③ · `device-control/index.md` (Q1) |
| §10 kod seti tanımsız | `device-control-kapsam.md` §M ("taslak adlar") + A12 (anayasa §17) — kod listesi **yok** |
| §11 144/32/256/16 B | `device-control-kapsam.md` §F.4 (§11-② bu dosyada yalnız **kod** verir) |
| §12 sayaç + tek-yön | `firmware-bilesenler.md` §11-⑦ + C7 · `boot-surucu.md` §5 (`TLMT_EVENT`) |
| §13 DV-1…DV-12 + DV-11 "2/6 başarısız" | `device-control-dogrulama.md` §2 + §4 (DD-1…DD-8) |
| §14 kural 1–8 | `firmware-bilesenler.md` C6/C7 + anayasa §6/§7/§10/§14 + `device-control-kapsam.md` §A.1 |
| §15 ok-yokluğu (CLAUDE.md) | `.ai/CLAUDE.md` §④/⑤ (boot-surucu'ya bağlı) ↔ `device-control-kapsam.md` §5 (④ atıf "burada değil") |
| §16 terim 10–13 | `firmware-evrensel-dil.md` §5 + `device-control-kapsam.md` §5 (④ sözü) |
| §17 Q1–Q13 | `device-control/index.md` (Q1 harfiyen) · kapsam §F/§M/§A.4 · boot-surucu §2/§5/§11 · doğrulama §2 · bilesenler §19.3-⑥/§11-② · CLAUDE.md §④/⑤ |
| §18 itirazlar | bu dosyanın §13 (DV-11 kanıtsızlık) + §14.2 + §10-②/③ (self-ref) + §12 (negatif kanıt) + §15 (ok-yokluğu) |

**Bu dosyada uydurulan değer/register/ölçüm yok.** Ölçüm kapısı değerleri = `device-control-dogrulama.md` §2.

---

## §20 Revizyon · Kapanış Kontrolü

| Tarih | Revizyon |
|---|---|
| 2026-10-14 | İlk yazım: bayrak sözlüğü (B1–B10), durum makinesi (S1–S5), sözleşmeler (K1–K6), boot devir (T12 çelişkisi), audio/power/runtime/K001 kapıları, hata kodu arayüzü (kod seti tanımsız ⚠️), gözlem, test kapısı (DV-1…12 eşlemesi), sözleşme-değişiklik kuralı (§14), terminoloji 10–13, 12 açık soru (Q1–Q13, Q8 yok), 5 red-team itirazı, iddia→dayanak. |

**Kapanış kontrolü (görev yasağıları):**

1. ✅ Yalnız `device-control-surucu.md` yazıldı — mevcut dosya **değiştirilmedi**
   (`device-control-anayasa.md` ve `index.md` **dokunulmadı**).
2. ✅ FM 10 anahtar, doğru sıra, **2 örnek `validation`**, `group: mimari`, `depends_on` ile
   §15 hizalı.
3. ✅ Son satır: `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode`
   (anayasa/boot-surucu ile aynı).
4. ✅ Boş `evidence`/`open_questions` hücresi yok; her iddia §19'da dayanaklı; uydurma
   register/ad/tanım **yok** (kod seti `⚠️ TANIMSIZ`, polarity `UNKNOWN`, timeout `UNKNOWN`).
5. ✅ index.md açık sorusu (Q1) harfiyen + açık (12 madde) — **hiçbiri çözülmedi**.
6. ✅ Çelişkiler gizlenmedi: **BOOT_READY↔BOOT_OK (T12/Q3)**, kod-seti tanımsız (Q4),
   timeout değeri yok (Q5), self-ref (§10-②), DV-11↔bayrak bağı kanıtsız (Q11).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
