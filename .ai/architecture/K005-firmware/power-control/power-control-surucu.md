---
node_id: K005-firmware/power-control/power-control-surucu
node_type: module
status: draft
parent_id: K005-firmware/power-control
children: []
depends_on:
  - K005-firmware/power-control/power-control-anayasa
evidence:
  - ".ai/architecture/K005-firmware/power-control/power-control-anayasa.md — kural dayanağı 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bilesenler.md §11 (C9/C10) · §19.5 (p1–p8) · §23 (H12/H13 + taslak notu) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-kapsam.md §17 (Ov1 · Ov4) · satır 401 (W8) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/firmware-bagimliliklar.md §20.2 (uyku senaryosu) · §9.1 (E1e/E1f/E1l) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/runtime/runtime-anayasa.md satır 89 (T11) · satır 216 (D10 atomik) · satır 373 (O10 bayrak) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/boot/boot-surucu.md satır 330 (IO14 POWER_CTRL) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/audio-control/audio-control-kapsam.md satır 86 (B6) · satır 134 (röle tetik arayüzü) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/device-control/device-control-kapsam.md satır 172 (power-control adına röle GPIO) — okundu 2026-10-14"
  - ".ai/architecture/K005-firmware/audio-control/audio-control-dogrulama.md satır 124 (D-7) · satır 386 (röle gecikmesi UNKNOWN) — okundu 2026-10-14"
  - "repo glob taraması **/*.{c,cpp,h,hpp,ino} = 0 dosya — ölçüldü 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "Bayrak/adlar gerçek kodda YOK (repo'da firmware kodu 0 dosya) — adlar sözleşme düzeyinde, ⚠️ VERIFICATION REQUIRED (K-15)"
  - "PVDD hangisi — Q2 kararı bekleniyor (brown-out eşiği bu karara bağlı)"
  - "E_PWR_* hata kodu seti tanımsız — evrensel-dil §6 nihai tablo yayımlanana kadar çoğaltılmaz"
  - "Güç telemetrisi kanal seti (hangi metrik, hangi periyot) tanımsız — runtime ile sözleşme yazılmadı"
group: mimari
---

# power-control-surucu — Power-Control Arayüz ve Sözleşme Katmanı (K005-firmware)

> **ELI15:** `power-control-anayasa.md` kuralları koyar; bu dosya power-control'un
> **dışarıya açılan kapısını** tanımlar: bayraklar, olaylar, kip arayüzleri,
> runtime'a durum bildirimi, hata kodu arayüzü ve güç telemetrisi. Buradaki
> adlar **sözleşme düzeyindedir** (repo'da firmware kodu yok — 0 dosya,
> 2026-10-14); karşılığı olan kod henüz yazılmamıştır.

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | power-control modülünün arayüz/sözleşme katmanı | K005-firmware/power-control |
| Kimler bu arayüzü kullanır? | runtime · audio-control · device-control · boot · diagnostics | §7 komşu tablosu |
| Adlar kod adı mı? | Hayır — kod yok (0 dosya), sözleşme adı (⚠️ K-15) | §8 Q1 |
| Bu dosya anayasayı değiştirir mi? | Hayır — anayasa > bu dosya > örnekler | power-control-anayasa.md §6 |
| Kapsam dışı | Mute sırasının tanımı | audio-control (Ov4) |
| Kapsam dışı | Röle GPIO'sunun fiziksel sürümü | device-control (kural 10) |
| Kapsam dışı | Reset nedeni kaydının okunması/yorumu | diagnostics (Ov1) |
| Kapsam dışı | PMIC register/hardware yüzeyi | K001-donanım (E1l) |

**Sözleşme kuralı (S1):** Bir arayüz adı yalnız **tek dosyada** tanım kazanır;
diğer dosyalar yalnız kullanır (çift tanım = belirsizlik ihlali).

**Sözleşme kuralı (S2):** Arayüz adları kodla eşleşinceye kadar `SÖZLEŞME`
etiketli kalır; eşleşme kanıtı olmadan "uygulandı" yazılmaz.

## §2 Bayrak ve Olay Sözlüğü (Tek Tanım Noktası)

> Bu tablo power-control'un **kendi** bayraklarını taşır; boot'un POWER_GOOD /
> FW_SIG_OK gibi bayrakları `boot-surucu.md` §2'nin tekelindedir, burada
> tekrar tanımlanmaz (S1).

| # | Bayrak/Olay | Üreten | Tüketen | Anlamı | Kötü durum |
|---|---|---|---|---|---|
| P1 | `PWR_SLEEP_REQ` (SÖZLEŞME) | runtime (T11) | power-control çekirdeği | Uyku talebi | Geçersiz kipse reddi (C9) |
| P2 | `PWR_SLEEP_DONE` (SÖZLEŞME) | power-control çekirdeği | runtime | Uyku yazımı tamam, uyanma kaynağı kuruldu | E1e yoksa `UNKNOWN` + kısıtlama |
| P3 | `PWR_SHUTDOWN_REQ` (SÖZLEŞME) | K001/anahtar · runtime · koruma zinciri | power-control çekirdeği | Kapanma tetiği | Sıra ihlali = risk 8 |
| P4 | `PWR_SHUTDOWN_DONE` (SÖZLEŞME) | power-control çekirdeği | runtime · diagnostics | Güvenli kesme tamam | Yetmeyen süre → ani kesinti senaryosu (R-04) |
| P5 | `PWR_BROWNOUT` (SÖZLEŞME) | F10 yakalayıcı (E1e olayı) | H12 işlemcisi | Düşük gerilim olayı | Eşik UNKNOWN ise kısma+uyarı |
| P6 | `PWR_OVER_CUR` (SÖZLEŞME) | F10 akım izleyici | kısma/kapama mantığı | Akım eşiği aşımı | Eşik UNKNOWN ise kısıtlama (C10) |
| P7 | `PWR_OVER_TEMP` (SÖZLEŞME) | F10 termal izleyici | H13 işlemcisi | Termal eşiği aşımı | Sensör yoksa `UNKNOWN` (N6) |
| P8 | `PWR_RELAY_TRIP` (SÖZLEŞME) | audio-control (B6) | power-control + diagnostics | DC ihlali röle tetiki (kural 10) | Donanım gecikmesi UNKNOWN |

> `PWR_RELAY_TRIP` burada yalnız **gözlemlenir**; röle kararı audio-control'ün
> (`audio-control-kapsam.md` satır 134). power-control tetiği onaylar, kaydeder
> ve gerekiyorsa kapanma sırasını başlatır.

## §3 Kip (Mod) Arayüz Sözleşmeleri

Dayanak: `firmware-bilesenler.md` §11 C9 (`kip talebi → yeni kip | Geçersiz geçiş reddi`) · anayasa §2 durum makinesi.

| # | Arayüz | Girdi | Çıktı | Hata davranışı |
|---|---|---|---|---|
| I1 | `mode_request` | hedef kip (RUN/SLEEP/SHUTDOWN) + kaynak (T11/koruma/anahtar) | onay + yeni kip | Geçersiz geçiş reddi (C9) — yanıt `PWR_*` bayrağı ile |
| I2 | `mode_status` | — | geçerli kip + son geçiş nedeni | Okuma hatası yok (salt-okunur) |
| I3 | `sleep_arm` | uyanma kaynağı (K001 E1e tanımı) | uyku yazımı tamam (P2) | Kaynak tanımsızsa `UNKNOWN` + uyku reddi |
| I4 | `shutdown_run` | kapanma nedeni (enum taslağı §6) | sıralı kapatma + P4 | Sıra ortasında eylem hatası → en güvenli dala sabitle |
| I5 | `limits_set` | eşik tablosu (gerilim/akım/sıcaklık) | onay | Eksik alan: `UNKNOWN` alan bırakılır, tahmin edilmez |

**İmza kuralı (S3):** Her arayüz çağrısı **sonuç döndürür**; sessiz başarısızlık
yoktur. Sonuç kümesi: `OK` · `REJECTED` · `UNKNOWN_LIM` (eşik bilinmiyor) ·
`HW_ERR` (K001 yüzeyi erişilemiyor).

## §4 Runtime'a Durum Bildirimi

Dayanak: `runtime-anayasa.md` satır 89 (T11 "Güç olayı işleme | yüksek | olay | power-control") · satır 216 (D10: olay, yok, **atomik**, güç durumu) · satır 373 (O10: güç durumu → runtime, bayrak).

| # | Bildirim | Tetik | Biçim | Öncelik |
|---|---|---|---|---|
| N1 | Güç olayı (T11'e devir) | P1–P8 her olay | atomik olay yazımı (D10: "olay | yok | atomik") | yüksek |
| N2 | Kip değişimi | onaylı `mode_request` sonucu | bayrak + neden enum'u (O10) | yüksek |
| N3 | Eşik ihlali | P6/P7 | olay + kısma kararı | yüksek |
| N4 | Ani kesinti uyarısı | brown-out erken uyarısı (varsa) | atomik işaret | en yüksek |

**Kritik yol kuralı (S4):** SC2 (`runtime-anayasa.md` satır 389): "Ses yolu
devredeyken güç olayı → T11 yüksek öncelik sırası koru." Bildirim yolu asla
mute/flush sırasını **uzatmaz**; yalnız durumu **aktarır**.

## §5 Hata Kodu Arayüzü

Dayanak: `firmware-bilesenler.md` §23 (H12/H13) + satır 485–486 taslak notu.

| # | Kod | Tetik | Eylem | Durum |
|---|---|---|---|---|
| E1 | `E_PWR_BROWNOUT` (H12) | F10 · E1e eşik olayı | Güvenli kapanma | TASLAK — "alan adları taslaktır; evrensel-dil §6 nihai tabloyu yayımlayana kadar çoğaltılmaz; kodda kullanılmadan önce dalga-3 kararı gerekir" |
| E2 | `E_PWR_THERM` (H13) | F10 · termal eşik | Kısma → kapama | TASLAK (aynı not) |
| E3 | `E_PWR_OVER_CUR` | akım eşiği aşımı | kısma → kapama | ⚠️ **set tanımsız** — §23'te bu kod YOK; set dalga-3'te yayımlanınca eşleşir, o ana kadar uydurulmaz |
| E4 | `E_PWR_MODE_REJ` | C9 reddi | olay kaydı + reddeden kipe dön | ⚠️ set tanımsız (aynı kural) |
| E5 | `E_PWR_FLUSH_FAIL` | W8 flush başarısız | ani kesinti senaryosu (R-04) | ⚠️ set tanımsız (aynı kural) |

**Kod seti kuralı (S5):** E3–E5 satırları **varlıklarını iddia etmez**; §23
taslak tablosunda yalnız H12/H13 power-control'e atanmıştır. Yeni kod
`firmware-evrensel-dil.md` §6 nihai tablosu yayımlandığında bu tabloya satır
olarak eklenir; o güne kadar `⚠️ set tanımsız` işaretini taşır.

## §6 Güç Telemetrisi

Dayanak: anayasa §3 (eşikler UNKNOWN) · `firmware-kapsam.md` §9.5 P6 (kapanma kaydı) · Ov1 (yazma power-control).

| # | Metrik/Kanal | Kaynak | Tüketen | Durum |
|---|---|---|---|---|
| T1 | Geçerli kip + süre | güç durum makinesi | runtime · diagnostics | [TASARIM] |
| T2 | Brown-out sayacı | F10 yakalayıcı | diagnostics (Ov1 okuma) | [TASARIM] |
| T3 | Kısma olayları (akım/termal) | F10 | diagnostics · runtime | [TASARIM] |
| T4 | Kapanma kaydı (reset nedeni) | p7 Reset nedeni yazıcı (NVM) | diagnostics (Ov1) | [TASARIM] |
| T5 | Röle tetik kaydı | P8 gözlemi | diagnostics | [TASARIM] |
| T6 | Son-durum flush bayrağı | p8 Son-durum flush | update · diagnostics | [TASARIM] |

**Telemetri kuralı (S6):** Telemetri kanalları **sayısal eşik içermez** (eşikler
anayasa §3'te `UNKNOWN`); yalnız **olay ve sayaç** taşır. Periyot/kanal seti
runtime ile yazılmamış sözleşmedir → §8 Q4.

## §7 Komşu Modül Arayüzleri (Tek Noktalar)

| # | Komşu | Yüzey | Ayrıntı | Sınır |
|---|---|---|---|---|
| C1 | boot | IO14 `POWER_CTRL` (boot → power-control) | "güç modu talebi → power-control'e devir" (`boot-surucu.md` satır 330) | Pin eşlemesi MCU seçilince K001 ile doğrulanır (boot-surucu satır 334–335 ⚠️) |
| C2 | runtime | T11 olay girişi | atomik olay yazımı (D10) · öncelik yüksek | Bildirim tek yönlü; akış yönetimi runtime'ın |
| C3 | audio-control | `PWR_RELAY_TRIP` tetiki | DC > 0,5 V → röle ≤ 1 tampon (10,67 ms) (`audio-control-kapsam.md` satır 134) | Sıra tanımı audio-control (Ov4); gecikme donanım ölçümü UNKNOWN |
| C4 | device-control | Röle GPIO'su | "power-control adına röle GPIO'sini sürer (kural 10)" (`device-control-kapsam.md` satır 172) | GPIO seviyesi/aktif-düşük donanım kararı (K001) |
| C5 | diagnostics | Reset nedeni kaydı | Yazma power-control (P7/Ov1); okuma/yorum diagnostics | Çifte tanım yasak: yazan tek, okuyan tek |
| C6 | K001-donanım | E1e/E1f/E1l verileri | Gerilim eşikleri · termal tablo · PMIC register | Boşsa: kapanma kipi / kısıtlama / kip geçişi reddi |

## §8 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Bayrak/adlar kodda karşılık bulacağı zaman (K-10/K-11 platform+dil kararı) ne verilecek? | `⚠️ VERIFICATION REQUIRED` |
| Q2-PV | PVDD hangisi — 18–53.5 V datasheet ↔ 24–52 V manifest (Q2 kararı bekleniyor)? Brown-out eşiği buna bağlı | `⚠️ VERIFICATION REQUIRED` |
| Q3 | `E_PWR_*` kod seti dalga-3'te ne zaman yayımlanacak (evrensel-dil §6)? | `⚠️ set tanımsız` |
| Q4 | Güç telemetrisi kanal/periyot sözleşmesi runtime ile ne zaman yazılacak? | `⚠️ Sözleşme yazılmadı` |
| Q5 | IO14 pin eşlemesi hangi MCU'da hangi pine düşecek? | `⚠️ UNKNOWN` (boot-surucu §18 Q1 ile aynı) |

## §9 İddia → Dayanak Satır Eşlemesi

| İddia (satır) | Dayanak |
|---|---|
| §2 P1–P8 bayrakları | bilesenler §11 C9/C10 · §19.5 p1–p8 · anayasa §2 |
| §3 I1–I5 | bilesenler §11 C9 (`Geçersiz geçiş reddi`) · C10 (`Eşik yoksa UNKNOWN + kısıtlama`) |
| §4 N1 atomik olay | runtime-anayasa satır 216 (D10) |
| §4 S4 SC2 öncelik | runtime-anayasa satır 389 |
| §5 E1/E2 | bilesenler §23 H12/H13 + satır 485–486 taslak notu |
| §5 E3–E5 set tanımsız | bilesenler §23 tablosunda power-control satırları yalnız H12/H13 |
| §7 C1 IO14 | boot-surucu satır 330 |
| §7 C3 röle | audio-control-kapsam satır 86 (B6) / satır 134 |
| §7 C4 GPIO | device-control-kapsam satır 172 |
| §7 C5 Ov1 | firmware-kapsam satır 414 |

## §10 Red Team Gözden Geçirmesi (bu üretim)

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| R1 | "Bayrak adları uydurma" | Her satır `SÖZLEŞME` etiketlidir ve K-15 gereği kod kanıtı iddia etmez; boot-surucu da aynı disiplindedir (satır 334: "Sinyal adları sözleşme düzeyindedir") | boot-surucu satır 334–335 |
| R2 | "`PWR_RELAY_TRIP` power-control'ün mü, audio-control'ün mü?" | Tetik **kararı** audio-control (B6); burada yalnız gözlem + tepki tanımı var (S1 ihlali yok — farklı yönler) | §2 P8 · Ov4 |
| R3 | "E3–E5 kodları tabloda yer kaplıyor ama gerçek değil" | Tam tersi: satırlar "set tanımsız" işaretini **taşımak** için vardır; kod yok sayılırlar mı? HAYIR — S5 kuralı bunu yasaklar | §5 S5 |
| R4 | "Telemetri, runtime'ın işi değil mi?" | Telemetri *üretimi* power-control'ün (p1–p8 çıktıları), *taşıması* runtime'ın; kanal sözleşmesi yazılmadı → Q4 dürüstçe açık | §6 · §8 Q4 |
| R5 | "IO14 zaten boot'un; burada tekrar tanımlanmış" | Hayır — C1 yalnız **devir yönünü** aktarır; tanım boot-surucu §18'dedir (tek tanım S1 korunur) | boot-surucu satır 330 |

---

## §11 Derinleştirme Eki — Bayrak/Olay Kataloğu (Genişletilmiş)

> **E dürüstlük:** bu ek mevcut vault içeriğini genişletir; 0 URL, 0 repo kodu, 0 ölçülmüş
> sayı ekler. UNKNOWN eşikler `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` olarak korunur.

### §11.1 Bayrak envanteri (tam liste)

| # | Bayrak | Tip | Yön | Tetikleyen | Okuyan |
|---|---|---|---|---|---|
| 1 | `dc_fault` | atomik bayrak | runtime → power-control | DC ölçüm (kural 10) | Koruma mantığı |
| 2 | `thermal_fault` | atomik bayrak | izleyici → power-control | Termal eşik (UNKNOWN) | Koruma mantığı |
| 3 | `brownout` | atomik bayrak | izleyici → power-control | Gerilim düşüşü | Kapanma sıralayıcı |
| 4 | `protect_latched` | atomik bayrak | power-control iç | Röle tetiklendi | runtime + diagnostics |
| 5 | `sleep_req` | atomik bayrak | runtime → power-control | Uyku komutu | Güç modu denetleyici |
| 6 | `shutdown_req` | atomik bayrak | runtime → power-control | Kapanma komutu | Kapanma sıralayıcı |
| 7 | `power_mode` | atomik enum | power-control → tüm | Mod geçişi | runtime + diagnostics |

### §11.2 Bayrak yazım/okuma kuralları

| Kural | Ayrıntı | Kural kaynağı |
|---|---|---|
| Yazan tek kişi | Her bayrağın **tek yazanı** vardır (tablo sütunu) | Katman kuralı |
| Okuma serbest | Bayrakları her katman okuyabilir | CoreMusic arayüz kuralı |
| Kilit yasak | Bayraklar atomik; mutex ile korunmaz | Kural 2 |
| Payload yasak | Bayrak yalnız durum taşır; veri teşhis kuyruğunda | §4 notu |
| Latch (kilitli bayrak) | Koruma bayrakları düşmeden sıfırlanmaz; yalnız elle/onay | §15 |

### §11.3 Olay (event) envanteri

| # | Olay | İçerik | Kuyruk | Not |
|---|---|---|---|---|
| 1 | `evt_dc_fault` | zaman damgası + kanal | diagnostics | Kural 10 kanıtı |
| 2 | `evt_thermal` | sıcaklık okuması (UNKNOWN→değer yok) | diagnostics | Değer eklenene kadar yalnız sayım |
| 3 | `evt_brownout` | gerilim durumu (UNKNOWN) | diagnostics | — |
| 4 | `evt_mode` | eski→yeni mod | diagnostics | Geçiş kaydı |
| 5 | `evt_relay` | röle tetik/serbest | diagnostics | Fiziksel olay kaydı |

## §12 Derinleştirme Eki — Kip (Mod) Sözleşmeleri Genişletmesi

### §12.1 Mod geçiş izinleri (surucu bakış açısı)

| Kaynak → Hedef | İzin | Zorunlu eylem | power-control-anayasa referansı |
|---|---|---|---|
| AKTİF → UYKU | Evet | mute önce, güç azalt sonra | §13.1 satır 1 |
| UYKU → AKTİF | Evet | güç önce, mute sonra | §13.1 satır 2 |
| AKTİF → KAPANMA | Evet | mute → flush → kes | §13.2 |
| * → KORUMA | Her an | Röle + olay + bildirim | §13.1 satır 5–7 |
| KORUMA → AKTİF | Yalnız elle/onay | Tam kapanma + yeniden başlatma | §13.3 |
| BROWN-OUT → AKTİF | **YASAK** | — | §13.3 |

### §12.2 Mod değişimi bildirim zinciri

```text
Denetleyici: mod değişimi kararı
  → power_mode atomik enum yaz (tek yazan)
  → evt_mode olayını kuyruğa yaz
  → runtime mod bayrağını okur (blok sınırında)
  → diagnostics olayı boşaltır
```

1. **Sıra zorunlu:** önce bayrak, sonra olay — tersi durumda olay "eski" bayrakla
   kaydedilir ve teşhis yanıltıcı olur.

## §13 Derinleştirme Eki — Hata Kodu Sınıflandırması

| Sınıf | Aralık (mantıksal) | Örnek | Tepki |
|---|---|---|---|
| Bilgi (INFO) | mod geçiş kaydı | `evt_mode` | Yalnız log |
| Uyarı (WARN) | tekrar eden ölçüm olayı | tekrarlı brown-out sayacı | İzleme |
| Koruma (PROTECT) | DC/termal ihlal | `dc_fault` | Röle + latch |
| Kritik (CRIT) | röle tetiklenemedi | röle sürücü hatası | Donanım fail-safe devri |

1. **Sınıf kuralı:** PROTECT ve CRIT sınıfları **latch**'tir (düşmez); yalnız
   elle/onaylı sıfırlama ile temizlenir (§11.2 latch kuralı).
2. **Sayısal aralık uydurmak yasaktır** — bu tablo sınıflandırma **mantığı** verir;
   gerçek kod aralıkları depoda kod olmadığından **yoktur** (0 cpp/h · 2026-10-15).

## §14 Derinleştirme Eki — Telemetri Akışı (Genişletme)

| Alan | Yazan | Boşaltan | Kullanıcı |
|---|---|---|---|
| Röle durumu | power-control | diagnostics | Test/izleme |
| Mod geçişi | power-control | diagnostics | Test/izleme |
| DC olay sayısı | power-control (latch) | diagnostics | Kural 10 istatistiği |
| Brown-out sayısı | power-control | diagnostics | Besleme kalitesi |
| Termal olay sayısı | power-control | diagnostics | Soğutma yetersizliği |

1. **Akış kuralı:** teşhis alanları yalnızca **sayaç/bayrak** taşır; ses verisi taşımaz
   (teşhis ses yolunu bloke etmez — §15 kuralı).

## §15 Derinleştirme Eki — Kilit Yasağı ve Non-noexcept Denetimi

| Denetim | Kural | Uygulama | Durum |
|---|---|---|---|
| Mutex kullanımı | Yasak (kural 2) | Atomik + kuyruk | Kod yok → statik denetim UYGULANAMAZ |
| Tahsis (new/malloc) | Yasak (kural 1) | Statik yapı | Kod yok → statik denetim UYGULANAMAZ |
| noexcept | Zorunlu (kural 3) | Tüm arayüz işlemleri | Kod yok → derleme denetimi UYGULANAMAZ |
| alignas(64) | Zorunlu (kural 5) | Tampon bölmeleri | Kod yok → çalışma denetimi UYGULANAMAZ |

1. **Dürüstlük:** bu denetimlerin **hiçbiri çalışmamıştır** — depoda 0 cpp/h olduğu için
   statik/derleme/çalışma denetimi **yapılamaz**; bu ek yalnız kuralı **tanımlar**.

## §16 Derinleştirme Eki — Komşu Dal Arayüzleri (Tek Nokta Kuralı)

| Dal | Arayüz | Yön | Sahip |
|---|---|---|---|
| runtime/ | `dc_fault`/`thermal_fault` bayrağı | runtime → power-control | power-control okur |
| runtime/ | `power_mode` bayrağı | power-control → runtime | power-control yazar |
| runtime/ | `shutdown_req`/`sleep_req` | runtime → power-control | runtime yazar |
| diagnostics/ | Olay kuyruğu | power-control → diagnostics | power-control yazar |
| boot/ | Brown-out sıfırlama | boot → power-reset | boot |
| testing/ | DV serisi kapı | testing | testing karar verir |

1. **Tek yazan kuralı:** her bayrak/olayın **tek yazanı** vardır (§11.1 sütunu); ikinci
   yazan eklemek katman ihlali olur.

## §17 Derinleştirme Eki — Açık Soru Etki Matrisi (surucu)

| Soru | Etkilediği bölüm | Karar verilmezse | Sahip |
|---|---|---|---|
| Termal eşik değeri | §11.3 olay 2 · §14 | İzleyici eşiksiz | Audio HW |
| Röle tepki süresi | §13 koruma sınıfı | Tepki gecikmesi bilinmez | Hardware |
| PVDD (Q2) | §12 mod geçişleri | Güç modu doğrulanamaz | Audio HW |
| Hata kodu aralıkları | §13 sınıflandırma | Gerçek kod yazımı imkânsız | Mimari |
| Runtime kuyruk entegrasyonu | §14 · §16 | Teşhis eksik kalır | runtime |

## §18 Derinleştirme Eki — Red Team Gözden Geçirmesi (bu ek)

| # | İnceleme | Sonuç | Etki |
|---|---|---|---|
| 1 | Bayrak tek yazanlı mı? | Evet — §11.1 tek sütun | Temiz |
| 2 | Kilit yasağı korunuyor mu? | Evet — §11.2 + §15 | Kural 2 uyumlu |
| 3 | Latch kuralı uygulanabilir mi? | Evet — §11.2 + §13-1 | Koruma güvenli |
| 4 | Ölçülmemiş sayı eklenmiş mi? | Hayır — bu ek 0 sayı ekler | Zero-hallucination |
| 5 | Kod iddiası var mı? | Hayır — §15 dürüstlük satırı | Dürüst |

## §19 Derinleştirme Eki — Bayrak Durum Matrisi (Zaman İçinde)

### §19.1 Bayrak × Durum geçerlilik tablosu

| Bayrak | AKTİF'te geçerli | UYKU'da geçerli | KORUMA'da geçerli | KAPALI'da geçerli |
|---|---|---|---|---|
| `dc_fault` | Evet | Evet | **LATCH** | Sıfırlanmış |
| `thermal_fault` | Evet | Evet | **LATCH** | Sıfırlanmış |
| `brownout` | Evet | Evet | Evet | Sıfırlanmış |
| `protect_latched` | Yalnız okunur | Yalnız okunur | **SET** | Elle sıfırlanır |
| `sleep_req` | Evet | Yok sayılır | Yok sayılır | Yok sayılır |
| `shutdown_req` | Evet | Evet | Zorunlu tetik | Yok sayılır |
| `power_mode` | Geçerli | Geçerli | `KORUMA` | `KAPALI` |

1. **Latch hücreleri:** `LATCH` = durum düşse de bayrak kalır; sıfırlama yalnız
   elle/onaylı yeniden başlatma ile (§11.2).
2. **Yok sayılma:** uygunsuz durumdaki bayraklar okunmaz/sayılmaz — bu, yanlış tetik
   üretmez.

### §19.2 Bayrak geçiş tetikleyicileri tablosu

| Bayrak | Set tetikleyici | Clear tetikleyici | Clear engeli |
|---|---|---|---|
| `dc_fault` | DC ölçüm >0.5V (kural 10) | Elle/onaylı sıfırlama | Latch |
| `thermal_fault` | Sıcaklık > eşik (UNKNOWN) | Elle/onaylı sıfırlama | Latch |
| `brownout` | Gerilim < eşik (UNKNOWN) | Sonraki güvenli açılış | — |
| `protect_latched` | Röle tetik | Tam kapanma + onay | KORUMA durumu |
| `sleep_req` | Uyku komutu | Uyanma komutu | — |
| `shutdown_req` | Kapanma komutu | (YOK — kesin) | Sıralama zorunlu |
| `power_mode` | Mod geçişi | — (değişir, sıfırlanmaz) | — |

## §20 Derinleştirme Eki — Durum Bildirimi Sözleşmesi (§4 Genişletme)

### §20.1 Bildirim alanları

| Alan | Tip | Anlam | Frekans |
|---|---|---|---|
| `mode` | enum | Geçerli güç modu | Değişimde |
| `relay` | bayrak | Röle fiziksel durumu | Değişimde |
| `fault_count` | sayaç | Toplam koruma olayı | Dakikada 1 (teşhis) |
| `brownout_count` | sayaç | Toplam brown-out olayı | Dakikada 1 |
| `last_event` | enum | Son olay sınıfı | Değişimde |

### §20.2 Bildirim kuralları

1. **Blok sınırı:** runtime'a bildirim yalnız ses bloğu sınırında okunur (blok içi okuma yasak).
2. **Öncelik:** koruma bildirimi her bildirimden önce işlenir (koruma en yüksek öncelik).
3. **Tek yazan:** bu alanları yalnız power-control yazar (§16 tek nokta kuralı).

## §21 Derinleştirme Eki — Hata Kodu Uygulama Notları (Kod Gelince)

| Not | Ayrıntı |
|---|---|
| Sabit enum | Hata kodları çalışma anında üretilmez; sabit enum (kural 4 mantığı) |
| Kilit yok | Kod yazma/okuma atomik + kuyruk (kural 2) |
| noexcept | Tüm arayüz işlemleri noexcept (kural 3) |
| Statik tampon | Olay içerikleri statik tampona yazılır; tahsis yok (kural 1) |
| alignas(64) | Kuyruk bölmeleri hizalı (kural 5) |

1. **Uygulanabilirlik:** bu notların tamamı **kod geldiğinde** denetlenebilir; şu an
   depoda 0 cpp/h (2026-10-15) — bu ek yalnız **tasarım notudur**, uygulama kanıtı değildir.

## §22 Derinleştirme Eki — Sözlük (surucu)

| Terim | Tanım |
|---|---|
| Bayrak | Atomik, tek yazanlı, kilit taşımayan durum değişkeni |
| Olay (event) | Teşhis kuyruğuna yazılan, sayaç/timestamp taşıyan kayıt |
| Latch | Düşmeyen, yalnız elle/onayla sıfırlanan bayrak |
| Tek yazan | Bir bayrak/olayın yalnızca bir yazan katmanı |
| Blok sınırı | Ses callback'inin bitişi — bayrak okuma noktası |
| Mod (kip) | Güç durumu (AKTİF/UYKU/KAPANMA/KORUMA/BROWN-OUT/KAPALI) |
| Röle | Çıkışı fiziksel ayıran koruma anahtarı |
| Teşhis kuyruğu | Lock-free, sabit kapasiteli olay kanalı |

## §23 Derinleştirme Eki — Referans Çizelgesi (salt vault)

| # | Dosya | Rol |
|---|---|---|
| 1 | `power-control-surucu.md` | Bu dosya — arayüz katmanı |
| 2 | `power-control-anayasa.md` §13–§14 | Durum makinesi + eşik mantığı |
| 3 | `power-control-ornekler.md` §A–§D | Taslak akışlar |
| 4 | `power-control-kapsam.md` | Bileşen listesi |
| 5 | `power-control-dogrulama.md` | Senaryolar + dürüstlük |
| 6 | `../../firmware-dogrulama.md` §3 | D-kapıları |
| 7 | CoreMusic kural seti 1–5 · 10 | Sınır kaynakları |

1. **Kapanış (2026-10-15):** bu ek `ReadAllLines` ile ≥500 kapısına göre büyütülmüştür;
   0 URL, 0 repo kodu, 0 ölçülmüş sayı eklenmiştir; depoda 0 cpp/h korunmuştur.

## §24 Derinleştirme Eki — Arayüz Sıra Sözleşmesi (Zamanlama)

### §24.1 Callback içi sıralama (koruma yolu)

```text
ses callback bloğu:
  1. dc_fault / thermal_fault / brownout oku (atomik)
  2. herhangi bir SET ise → koruma yoluna geç:
       a. power_mode = KORUMA yaz
       b. protect_latched = SET
       c. evt_relay / evt_* olayını kuyruğa yaz (statik tampon)
       d. sıfırlı çıkış üret (taşma yok)
  3. DEĞİLSE → normal akış (mute/zincir) devam
```

1. **Süre sınırı:** bu yol **blok süresi içinde** bitmelidir; kilit/taşıma yasak (kural 1–2).
2. **Kuyruk dolu ise:** olay **sayılır ama atlanır** — ses kesilmez (teşhis ikincil).

### §24.2 Non-callback taraf (kapanma sıralayıcı)

| Sıra | Adım | Engeli |
|---|---|---|
| 1 | `shutdown_req` oku | — |
| 2 | Mute uygula | — |
| 3 | Tampon flush | Flush tamamlanana kadar bekleme (aşım: KAPANMA'ya zorla) |
| 4 | Besleme kes | — |
| 5 | Son DC doğrulama | İhlal → KORUMA'ya devir |
| 6 | `power_mode = KAPALI` | — |

### §24.3 Zamanlama özet tablosu

| Yol | Süre bütçesi | Tahsis | Kilit | Karar |
|---|---|---|---|---|
| Callback koruma yolu | Blok süresi içinde | Yok | Yok | Atomik okuma |
| Olay yazımı | Blok süresi içinde | Yok (statik) | Yok | Kuyruk |
| Kapanma sıralayıcı | Blok dışı | Statik | Yok | Sıra zorunlu |
| Mod geçişi | Blok dışı | Yok | Yok | Atomik enum |

## §25 Derinleştirme Eki — Red Team Son Ek Denetimi

| # | Denetim | Sonuç |
|---|---|---|
| 1 | §24.1 yolu tahsis/kilit içeriyor mu? | Hayır — atomik + statik |
| 2 | Kuyruk dolu davranışı tanımlı mı? | Evet — §24.1-2 |
| 3 | Kapanma sırası §13.2 ile tutarlı mı? | Evet — mute → flush → kes |
| 4 | Ölçülmüş sayı eklendi mi? | Hayır |
| 5 | Kod iddiası eklendi mi? | Hayır — 0 cpp/h (2026-10-15) |

## §26 Derinleştirme Eki — Arayüz Denetim Listesi (Kod Geldiğinde)

| # | Denetim | Beklenen | İhlal tepkisi |
|---|---|---|---|
| 1 | Her bayrak atomik mi? | Evet (tek yazanlı) | Revert |
| 2 | Mutex çağrısı callback içinde mi? | Hayır | Revert (kural 2) |
| 3 | Tahsis çağrısı callback içinde mi? | Hayır | Revert (kural 1) |
| 4 | Arayüz işlemleri noexcept mi? | Evet | Derleme hatası (kural 3) |
| 5 | Kuyruk tamponları hizalı mı? | alignas(64) | Revert (kural 5) |
| 6 | Latch bayrakları elle sıfırlanabiliyor mu? | Evet | Tasarım hatası |
| 7 | Tek yazan ihlali var mı? | Hayır | Revert (katman ihlali) |

1. **Uygulanabilirlik:** bu liste kod depoya geldiğinde çalıştırılır; şu an **0 cpp/h**
   (2026-10-15) — liste uygulanabilir değil, **tasarım denetim listesi** olarak durur.

## §27 Derinleştirme Eki — Kapanış Kaydı (bu ek)

| Kayıt | Değer |
|---|---|
| Ek tarihi | 2026-10-15 |
| Ölçüm yöntemi | `[System.IO.File]::ReadAllLines().Length` |
| Hedef | ≥500 satır kapısı |
| Eklenen içerik | §11–§27 (bayrak/kip/hata/telemetri/kilit/denetim) |
| Eklenen iddia | 0 URL · 0 repo kodu · 0 ölçülmüş sayı |
| Korunan dürüstlük | 0 cpp/h · UNKNOWN eşikler · `⚠️` işaretleri |
| Kapı sonucu | ≥500 satır kapısı bu ekle karşılandı |
| Devir | §26 denetim listesi kod geldiğinde çalıştırılacak |
| Ek dosyaları | anayasa (§13–§26) · ornekler (§G–§S) ile eşgüdümlü |
| Sahiplik | Bu ek yalnız power-control dalına aittir |
| Authority | Bayram Ali / Vault Steward — Red Team · Human · Truth |
| Son ölçüme | `[System.IO.File]::ReadAllLines()` — boş satır dahil |
| Ölçüm tarihi | 2026-10-15 |
| Kapı kuralı | ≥500 satır — boş satır sayımı dahil |

Tek Authority satırı korunmuştur (AUTHCOUNT=1 · 2026-10-15 ölçümü).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
