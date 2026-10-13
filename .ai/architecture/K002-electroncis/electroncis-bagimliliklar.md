---
node_id: K002-electroncis/electroncis-bagimliliklar
node_type: module
status: draft
parent_id: K002-electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - ".ai/architecture/K002-electroncis/index.md §4 (dışa bağımlılık listesi)"
  - ".ai/electronic/** (60 dosya / 8.095 satır — 2026-10-13 disk ölçümü)"
  - "Repo kodu kanıtları: shared/src/Api/Bff/DesktopBff.php:35 · shared/src/Session/SessionConfig.php:33-36 (yalnız okuma)"
validation: "draft — bu turda yazıldı; iç bağımlılık matrisi §2, dış bağımlılık riskleri §4"
open_questions:
  - "Q-B1: K004-dsp ve K005-firmware node'ları diskte mevcut mu? (okunmadı — ⚠️ VERIFICATION REQUIRED)"
  - "Q-B2: `.ai/electronic/**` derinleştirmesi hangi ajan sırasıyla?"
group: mimari
---

# electroncis-bagimliliklar — Bağımlılık Defteri

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya K002'nin
> İÇ ve DIŞ bağımlılıklarını, bağımlılık yönünü ve kırılma riskini kayıt altına
> alır. Hiçbir bağımlılık "varsayım" olarak değil, disk kanıtıyla yazılır.

## §1 ELI15 — Bağımlılık ne demek?

Bir dosya "bağımlı"dır, çünkü onu anlamak için başka bir dosya da gerekir.
K002 şema = donanım envanteri (K001) olmadan eksik; boot şartnamesi = firmware
(K005) olmadan yarım; ölçüm kapısı = DSP zinciri (K004) olmadan doğrulanamaz.
Bu dosya bu hiyerarşiyi okur: hangisi hangisiz OLMAZ, hangisi yalnızca
"iyi olurdu" düzeyinde ilişkilidir.

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/electroncis-bagimliliklar` |
| 2 | İç bağımlılık | 6 modül + 5 alt-konu (§2) |
| 3 | Dış bağımlılık | 5 K-node + 2 vault yüzeyi (§3) |
| 4 | Zorunlu (kritik) | 3 (K001 · ADR-038 · sartname) |
| 5 | Durum | `draft` (A1 tur-1) |
| 6 | Açık | Q-B1, Q-B2 |
| 7 | Yazım | `electroncis` — dokunulmaz |

## §2 İç Bağımlılık Matrisi (K002 kendi içi)

### §2.1 Kök modül → modül

| # | Modül | Bağımlı olduğu | Gerekçe | Kritiklik |
|---|---|---|---|---|
| 1 | `index` | 5 alt-konu + 5 kök dosya | İndeks children listeleri | Yüksek |
| 2 | `electroncis-master` | `kapsam` (sınır) + `dogrulama` (kanıt) | Anayasa sınır ve kanıt olmadan yarım | Yüksek |
| 3 | `electroncis-kapsam` | `master` (kapsam özeti) + alt-konu anayasalar | Sınır çizgisi alt-konulardan okunur | Yüksek |
| 4 | `electroncis-bagimliliklar` | `master` §9 (kapsam dışı) | Bağımlılık ≠ kapsam; ikisi birbirini doğrular | Orta |
| 5 | `electroncis-dogrulama` | `master` + `kapsam` + `ornekler` | Doğrulanan iddialar bu dosyalarda | Yüksek |
| 6 | `electroncis-ornekler` | `master` §4 (arayüzler) | Örnekler arayüz şartından türetilir | Orta |

### §2.2 Kök → alt-konu

| # | Kök dosya | Alt-konuya bağımlı mı? | Yön |
|---|---|---|---|
| 1 | `index` | Evet — children: 5 alt-konu | index → alt-konu (yön: üst→alt) |
| 2 | `electroncis-master` | Evet — §2 katman matrisi alt-konu adlarını taşır | master → alt-konu |
| 3 | `electroncis-kapsam` | Evet — §B.1..B.5 alt-konu sınırı | kapsam → alt-konu |
| 4 | `electroncis-bagimliliklar` | Kısmen — §2.3 alt-konu bağımlılığı | bagimliliklar → alt-konu |
| 5 | `electroncis-dogrulama` | Evet — §6 alt-konu kanıt tabloları | dogrulama → alt-konu |
| 6 | `electroncis-ornekler` | Hayır — repo kodu bağımsız | — (tek yönlü referans) |

**Kural:** Bağımlılık yönü asla döngü olmaz — alt-konu dosyaları kök dosyalara
"bağımlı değildir", yalnızca referans verir. Döngü tespit edilirse §6'da
kayıt altına alınır.

### §2.3 Alt-konu → alt-konu (iç geçiş)

| # | Kaynak alt-konu | Hedef alt-konu | Bağımlılık | Kritiklik |
|---|---|---|---|---|
| 1 | devre-ve-analog | guc-yonetimi | Güç kaynağı şeması olmadan devre çalışmaz | Yüksek |
| 2 | dijital-ve-mikroislemci | devre-ve-analog | Saat devresi analog tasarım gerektirir | Yüksek |
| 3 | guc-yonetimi | imalat-ve-test | Güç katmanı üretim testine girer | Orta |
| 4 | sensor-ve-akteyici | devre-ve-analog | Sensör arayüzü op-amp/buffer gerektirir | Orta |
| 5 | imalat-ve-test | devre-ve-analog | DFT, şemadan test noktalarını alır | Orta |
| 6 | dijital-ve-mikroislemci | guc-yonetimi | MCU besleme + brown-out bağımlılığı | Orta |
| 7 | sensor-ve-akteyici | dijital-ve-mikroislemci | ADC/I2C dijital arayüz | Orta |
| 8 | imalat-ve-test | guc-yonetimi | Termal/EMC testi güç katmanını ölçer | Düşük |

### §2.4 Modül kritiklik özeti

| # | Modül | Kritiklik | Kırılırsa | Kurtarma |
|---|---|---|---|---|
| 1 | `index` | Yüksek | Domain erişilemez | children listesinden yeniden yaz |
| 2 | `electroncis-master` | Yüksek | 5 alt-konu kopuk kalır | §2.6 katman matrisi yeniden kurulur |
| 3 | `electroncis-kapsam` | Yüksek | Sınır belirsiz — komşu sızıntısı | §G tablosu yeniden yazılır |
| 4 | `electroncis-bagimliliklar` | Orta | Bağımlılık yönü kaybolur | §2-§3 tabloları yeterli |
| 5 | `electroncis-dogrulama` | Yüksek | Kanıtsız iddialar kalır | URL/envanter yeniden ölçülür |
| 6 | `electroncis-ornekler` | Orta | Repo kanıtı kaybolur | Dosya:satır tekrar okunur |

## §3 Dış Bağımlılık Matrisi (dışa)

### §3.1 K-node bağımlılıkları

| # | Hedef | İlişki | Yön | Kritiklik | Kanıt |
|---|---|---|---|---|---|
| 1 | `K001-donanım` | Fiziksel parça envanteri | K002 → K001 (K001'e bağımlı değil) | Yüksek | index §4.1 |
| 2 | `K004-dsp` | DSP algoritma zinciri | K002 → K004 sınırı | Orta | index §4.2 |
| 3 | `K005-firmware` | Gömülü kod yüzeyi | K002 → K005 sınırı | Orta | index §4.3 |
| 4 | `K000-isletim-sistemi` | OS çekirdeği (komşu) | Sınır — bağımlılık değil | Düşük | kapsam §C |
| 5 | `K000-isletim-sistemi/electroncis` | OS↔donanım kesişimi | Sınır — bağımlılık değil | Orta | kapsam §G |
| 6 | `.ai/architecture/index.md` | Registry (K002 GAP kaydı) | K002 → registry (yazım YASAK) | Yüksek | index evidence |

### §3.2 Vault yüzeyi bağımlılıkları

| # | Yüzey | İlişki | Kritiklik | Kanıt |
|---|---|---|---|---|
| 1 | `.ai/electronic/**` (60 dosya) | Ham içerik kaynağı | Yüksek | 2026-10-13 ölçüm: 8.095 satır |
| 2 | `.ai/electronic/amplifier/**` | Amfi/shema ham notları | Yüksek | class-ab.md 500 satır, class-d.md 418 |
| 3 | `.ai/electronic/hardware/**` | PCB/termal ham notları | Orta | pcb-design.md 90, thermal.md 82 |
| 4 | `.ai/electronic/firmware/**` | Boot/RTOS ham notları | Orta | boot-sequence.md 81, rtos.md 68 |
| 5 | `.ai/electronic/drivers/**` | Sürücü ham notları | Düşük (OS K000'de) | audio-drivers.md 652 |
| 6 | `.ai/decisions/` ADR-038 | DAC çip kısıtı | Kritik (bağlayıcı) | H001: yalnız PCM3168A |
| 7 | `.ai/decisions/` ADR-017 | 32-bit float | Kritik (bağlayıcı) | DSP donanım modu |
| 8 | `.ai/decisions/` ADR-019 | Per-OS player FROZEN | Kritik (dokunulmaz) | değişiklik ≥088 |

### §3.3 Sartname bağımlılıkları (ölçüm/performans)

| # | Sartname | Değer | Bağımlılık | Durum |
|---|---|---|---|---|
| 1 | Buffer | 512 örnek | Dikey tüm alt-konular | Sabit |
| 2 | Örnekleme | 48kHz | Dikey tüm alt-konular | Sabit |
| 3 | SNR | >100dB | imalat-ve-test ölçüm kapısı | Ölçüm PLANNED |
| 4 | THD+N | <0.01% @1kHz | imalat-ve-test ölçüm kapısı | Ölçüm PLANNED |
| 5 | Frekans yanıt | 20Hz-20kHz ±0.5dB | imalat-ve-test ölçüm kapısı | Ölçüm PLANNED |
| 6 | DC offset | >0.5V → röle | guc-yonetimi koruma | Tasarım kuralı (zorunlu) |
| 7 | Zamanlama | ≤25ms toplam | master §5.5 | Ölçüm PLANNED |

## §4 Dış Bağımlılık Riskleri

| # | Risk | Etkilenen | Olasılık | Azaltma |
|---|---|---|---|---|
| D1 | K004/K005 node'ları diskte YOK olabilir | §3.1.2-3 | Orta | Q-B1 — okunmadı, ⚠️ VERIFICATION REQUIRED |
| D2 | `.ai/electronic/**` CP1254 (non-UTF8) | §3.2 alıntılar | Yüksek (biliniyor) | Alıntı UTF-8 okuma ile; yeni dosyalar temiz UTF-8 |
| D3 | Envanter derinleştirme (3 ajan) gecikir | Tüm alt-konular | Orta | Q-B2 — bekleme |
| D4 | ADR-038 değişirse tüm DAC referansları | devre + dijital | Düşük (frozen) | Frozen — değişiklik ≥ yeni ADR |
| D5 | Sartname ölçümü (SNR/THD) tutmaz | imalat-ve-test | Orta | Ölçüm öncesi tasarım marjı |
| D6 | XMOS XU316 tedarik riski | dijital-ve-mikroislemci | Orta | Alternatif MCU araştırması (PLANNED) |
| D7 | K001 envanteri eksik | kapsam §B şema | Düşük | Komşu node okunur (bu turda okunmadı) |
| D8 | Registry (index.md) K002'yi görmezse | keşif/yönlendirme | Düşük | Orkestratöre bildirim — yazım yasak |
| D9 | Ham vault çelişkisi (iki dosya farklı derse) | master §6 | Orta | §G.2 yetki sırası: disk > ADR > işlenmiş |
| D10 | Görev envanter iddiası (10.323) tekrarlanır | doğrulama | Biliniyor | V6 — disk 8.095 yazılır |

## §5 Bağımlılık Yön Şeması (akış)

```text
.ai/architecture/index.md (registry — K002 GAP, yazım YASAK)
        │ (orkestratör yazar)
        ▼
K002-electroncis/index.md  ← children: 5 alt-konu
        │
        ├── electroncis-master      (anayasa — en az bağımlı)
        │        │
        │        ├── electroncis-kapsam      (sınır)
        │        ├── electroncis-bagimliliklar (bu dosya)
        │        ├── electroncis-dogrulama    (kanıt)
        │        └── electroncis-ornekler     (repo kodu)
        │
        └── 5 alt-konu (index + anayasa + ornekler + dogrulama)
                 │
                 ├── devre-ve-analog  ──┐
                 ├── dijital-ve-mikroislemci
                 ├── guc-yonetimi      ──┤ (iç geçiş: §2.3)
                 ├── sensor-ve-akteyici
                 └── imalat-ve-test  ───┘

DIŞ (sınır — okuma):
  K001-donanım · K004-dsp · K005-firmware · K000-isletim-sistemi[/electroncis]
  .ai/electronic/** (ham) · ADR-038/017/019 (frozen)
```

**Kural:** Ok yönü (↓) bağımlılıktır; ↑ yönünde bağımlılık YOKTUR. Alt-konu,
kök dosyayı "beklemez" — kök dosya alt-konu olmadan DA zayıf yaşar (yazım
sırası: kök index → kök içerik → alt-konu, görev kuralı ile aynı).

## §6 Bağımlılık Doğrulama (bu turda yapılan ölçüm)

| # | Doğrulama | Yöntem | Sonuç |
|---|---|---|---|
| 1 | index children 5 mi? | Dosya okuma (85→100 satır) | EVET — 5 alt-konu |
| 2 | `.ai/electronic/**` dosya sayısı | PowerShell envanter | 60 dosya / 8.095 satır |
| 3 | URL envanteri kesişimi | 2.110 envanter URL ile karşılaştırma | 0 kesişim |
| 4 | Repo kodu kanıtları | Dosya:satır okuma (yalnız okuma) | 5 kanıt kaydedildi (§7) |
| 5 | K004/K005 disk durumu | Okunmadı | ⚠️ VERIFICATION REQUIRED (Q-B1) |
| 6 | Registry değişmedi | Yazım yapılmadı | EVET — 0 giriş |
| 7 | Commit atılmadı | İşlem yapılmadı | EVET — 0 commit |

**Açık bağımlılık (Q-B1):** K004-dsp ve K005-firmware node'larının diskte
varlığı bu turda okunmadı; "var" ya da "yok" yazılmadı — ⚠️ VERIFICATION
REQUIRED. Okunduğunda §3.1 tablosu güncellenir.

## §7 Repo Kodu Kanıtları (yalnız okuma — K002 ile sınır)

K002 elektronik KODU üretmez (beyazliste `*.md`); ancak repo'nun OS/firmware
sınırındaki mevcut kodu, dış bağımlılıkların GERÇEK olduğunu kanıtlar.

| # | Dosya:Satır | Ne gösterir | Bağımlılık |
|---|---|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php:35` | `php_uname('s')` — OS tespiti | K000 sınırı (OS ≠ elektronik) |
| 2 | `shared/src/Session/SessionConfig.php:33-36` | `sys_get_temp_dir` + `@mkdir 0750` | OS dosya sistemi bağımlılığı |
| 3 | `shared/src/Cache/ApcuAdapter.php:9` | `function_exists('apcu_fetch')` | Ortam bağımlılık kontrolü |
| 4 | `shared/src/AI/AIEngine.php:291,295` | `ffprobe` + `shell_exec` | Dış araç bağımlılığı |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185,189,194,199` | `is_link`/`realpath`/`DIRECTORY_SEPARATOR` | Yol ayrıştırma (OS) |

**Tespit:** Beş kanıtın tamamı PHP/OS katmanındadır — **Repo'da elektronik
kodu YOK** (kaynak: `.ai/electronic/**` yalnız Markdown vault, 60 dosya).
K002'nin `.md` dışına yazıma izni bu nedenle yoktur; elektronik kod yüzeyi
gelecekte K005-firmware / L0 Embedded'de OLUR (⚠️ VERIFICATION REQUIRED —
bu turda oluşturulmadı).

## §8 Versiyon / Ortam Bağımlılıkları

| # | Bağımlılık | İddia | Disk kanıtı | Durum |
|---|---|---|---|---|
| 1 | JUCE 9 | Kural 12'de geçer | Sürüm kanıtı YOK | ⚠️ VERIFICATION REQUIRED |
| 2 | ASIO SDK 2.3.4 | Kural 12'de geçer | Sürüm kanıtı YOK | ⚠️ VERIFICATION REQUIRED |
| 3 | PHP 8.x | Kök AGENTS.md | composer.json okunmadı bu turda | ⚠️ VERIFICATION REQUIRED |
| 4 | XMOS XU316 | Vault + registry | PLANNED (spec var, donanım yok) | PLANNED |
| 5 | PCM3168A | ADR-038 H001 | ADR frozen — bağlayıcı | SABİT |
| 6 | 512/48kHz sartname | Görev kuralı 8 | Ölçüm PLANNED | SABİT + ölçüm beklemede |
| 7 | Windows (ASIO/WASAPI) | Kural 9-12 | OS hedefi | K000 alanı |
| 8 | MySQL / PHP katmanı | Kök AGENTS.md | K002 dışı (§K.3 kapsam) | Bu domainde değil |

**Not:** JUCE/ASIO sürüm iddiaları bu turda diskten doğrulanmadı →
`⚠️ VERIFICATION REQUIRED`. Sürüm numarası uydurulmadı (kural 12).

## §9 Bağımlılık Öncelik Sırası (ne önce gelir)

| # | Sıra | Bağımlılık | Gerekçe |
|---|---|---|---|
| 1 | 1 | ADR-038 / ADR-017 / ADR-019 | Frozen — her şeyin üstünde |
| 2 | 2 | Sartname (512/48kHz, SNR/THD eşikleri) | Ölçüm kapılarını belirler |
| 3 | 3 | `.ai/electronic/**` ham envanter | İçerik kaynağı (V6: 8.095 satır) |
| 4 | 4 | K001-donanım envanteri | Fiziksel gerçeklik |
| 5 | 5 | K005-firmware şartnamesi | Boot/I2S zamanlama sözleşmesi |
| 6 | 6 | K004-dsp zinciri | Ölçüm hattının son halkası |
| 7 | 7 | K000/electroncis sınırı | Yalnız kesişim — bağımlılık değil |
| 8 | 8 | Alt-konu iç geçişleri (§2.3) | Son aşamada birleştirilir |

**Sıra kuralı:** Erken aşamada bir bağımlılık değişirse yalnız sonraki satırlar
etkilenir; ADR (1. satır) değişirse TÜMÜ revize olur → bu yüzden frozen'dır.

## §10 Bağımlılık Kayıt Şekli (standart)

Her bağımlılık şu 7 alanla kaydedilir (bu dosyadaki tüm tablolar bu şemada):

| # | Alan | Zorunlu mu | Açıklama |
|---|---|---|---|
| 1 | Bağımlılık kimliği | Evet | K-node veya vault yüzeyi |
| 2 | Yön | Evet | Kim kime bağımlı (ok yönü) |
| 3 | Kritiklik | Evet | Kritik / Yüksek / Orta / Düşük |
| 4 | Kanıt | Evet | Dosya:satır veya disk ölçümü |
| 5 | Durum | Evet | Aktif / Sınır / PLANNED / ⚠️ |
| 6 | Kırılma etkisi | Hayır (ama yazılır) | Ne olur |
| 7 | Kurtarma | Hayır (ama yazılır) | Nasıl onarılır |

**Eksik alan:** Bilinmeyen alan `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` yazılır —
boş hücre bırakılmaz, uydurulmaz (zero-hallucination).

## §11 Kritik Bağımlılık Derinlemesine

### §11.1 ADR-038 (DAC çip kısıtı — Kritik)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | İzinli çip | PCM3168A (H001) | ADR-038 |
| 2 | Yasaklı çip | PCM5122 — ABSOLUTELY FORBIDDEN | Kural 6 |
| 3 | Etkilenen alt-konular | devre-ve-analog · dijital-ve-mikroislemci | §2.3 |
| 4 | Kırılırsa | Tüm DAC referansları + boot init + I2S map | frozen ADR |
| 5 | Değişiklik yolu | Yeni ADR ≥ mevcut numara — bu turda YOK | frozen |

### §11.2 Sartname (512 / 48kHz — Kritik)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | Buffer | 512 örnek | Kural 8 |
| 2 | Örnekleme | 48kHz | Kural 8 |
| 3 | Bütçe | ≤10.67ms işleme + toplam ≤25ms (master §5.5) | sartname |
| 4 | Etkilenen alt-konular | dijital (zamanlama) · imalat (ölçüm) · devre (filtre) | §2.3 |
| 5 | Ölçüm durumu | PLANNED — kural 8 "olcum PLANNED" | dürüstlük |

### §11.3 `.ai/electronic/**` (Yüksek — ham kaynak)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | Dosya sayısı | 60 | 2026-10-13 disk ölçümü |
| 2 | Satır sayısı | 8.095 | 2026-10-13 disk ölçümü |
| 3 | Görev iddiası | 10.323 | ÇÜRÜTÜLDÜ (V6) — disk kazanır |
| 4 | Kodlama | CP1254 (non-UTF8) | keşif notu |
| 5 | İşlem | Yalnız atıf — yazım YASAK | beyazliste |

## §12 Bağımlılık Risk Matrisi (özet — §4 + §8 birleşik)

| # | Risk | Kaynak | Etki | Öncelik |
|---|---|---|---|---|
| 1 | K004/K005 diskte yok | Q-B1 | Sınır boşta kalır | 1 |
| 2 | JUCE/ASIO sürüm kanıtı yok | §8.1-2 | Kural 12 ihlali olasılığı | 2 |
| 3 | Envanter iddiası (10.323) tekrarlanır | V6 | Doğrulama dosyası yanlış yazılır | 3 |
| 4 | CP1254 alıntı mojibake üretir | §3.2 notu | Dosya bozulması | 4 |
| 5 | 3 ajan derinleştirmesi gecikir | Q-B2 | Alt-konular yarım kalır | 5 |
| 6 | Ölçüm (SNR/THD) tutmaz | §3.3 | Test kapısı kapanır | 6 |
| 7 | XMOS tedarik riski | §4 D6 | dijital-ve-mikroislemci | 7 |
| 8 | Registry K002'yi görmez | §4 D8 | Yönlendirme kaybı | 8 |

## §13 Kapanış Kontrol Listesi (bağımlılık defteri)

- [x] İç bağımlılık matrisi (§2 — 6 modül + 8 alt-konu geçişi)
- [x] Dış bağımlılık matrisi (§3 — 6 K-node + 8 vault yüzeyi + 7 sartname)
- [x] Risk tablosu (§4 — 10 madde)
- [x] Yön şeması (§5 — ok yönü bağımlılık, döngü yok)
- [x] Doğrulama ölçümü (§6 — 7 madde, 2'si ⚠️)
- [x] Repo kodu kanıtları (§7 — 5 dosya:satır, "elektronik kodu YOK" dürüstlüğü)
- [x] Sürüm bağımlılıkları (§8 — JUCE/ASIO ⚠️ işaretli)
- [x] Öncelik sırası (§9) + şema (§10) + derinlik (§11)
- [ ] Q-B1: K004/K005 disk okuması — BEKLEMEDE
- [ ] Q-B2: Derinleştirme ajan sırası — BEKLEMEDE

## §14 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `electroncis-master.md` §3-§4 | Bileşen + arayüz bağımlılığı |
| 2 | `electroncis-kapsam.md` §G | Sınır (bağımlılık ≠ sınır) |
| 3 | `electroncis-dogrulama.md` §6 | Repo kanıtlarının doğrulanması |
| 4 | `index.md` §4 | Dışa bağımlılık listesi (özet) |
| 5 | 5 × `{alt}-dogrulama.md` | Alt-konu kanıt bağımlılıkları |
| 6 | `.ai/decisions/index.md` | ADR-038/017/019 (frozen — okuma) |

## §15 Alt-Konu → Dış Bağımlılık Haritası

Her alt-konunun dış dünyaya tek bir penceresi vardır; bu harita o pencereyi gösterir.

| # | Alt-konu | Birincil dış bağımlılık | İkincil | Kritik dış risk |
|---|---|---|---|---|
| 1 | devre-ve-analog | ADR-038 (DAC) | K001 (parça) | PCM5122 ihlali (kural 6) |
| 2 | dijital-ve-mikroislemci | K005-firmware (boot sözleşmesi) | ADR-017 (float) | Q-B1 — K005 disk durumu |
| 3 | guc-yonetimi | ADR-038 (DAC besleme) | K001 (güç parçaları) | DC 0.5V röle kuralı ihlali |
| 4 | sensor-ve-akteyici | K005 (I2C/SPI kodu) | K004 (kapalı çevrim) | Q-B1 + Q-K2-3 |
| 5 | imalat-ve-test | Sartname (SNR/THD) | K001 (üretim parçaları) | Ölçüm PLANNED — kapı kapalı |

### §15.1 Ters harita (dış bağımlılık → hangi alt-konular)

| # | Dış bağımlılık | Etkilenen alt-konular | Sayı |
|---|---|---|---|
| 1 | ADR-038 (PCM3168A) | devre · dijital · guc · imalat | 4 |
| 2 | ADR-017 (32-bit float) | dijital · imalat | 2 |
| 3 | Sartname (512/48kHz) | dijital · devre · imalat | 3 |
| 4 | `.ai/electronic/**` (60 dosya) | hepsi | 5 |
| 5 | K001-donanım | devre · guc · imalat | 3 |
| 6 | K005-firmware | dijital · sensor | 2 |
| 7 | K004-dsp | sensor · devre (ön zincir) | 2 |
| 8 | K000/electroncis (sınır) | sensor (hot-plug) · dijital (sürücü) | 2 |

**En kritik dış bağımlılık:** ADR-038 — 5 alt-konunun 4'ünü etkiler; frozen
olduğu için değişmez, dolayısıyla en güvenilir de odur.

## §16 Bağımlılık Kırılma Senaryoları

| # | Senaryo | Kırılan | Etki alanı | Kurtarma |
|---|---|---|---|---|
| 1 | `.ai/electronic/**` silinirse | §3.2 | Tüm ham kaynak | Git yedek + 3 ajan raporu (varsa) |
| 2 | ADR-038 revize edilirse | §11.1 | 4 alt-konu | Yeni ADR + tüm DAC referansları |
| 3 | K005-firmware yazılmazsa | §3.1.3 | boot/I2S sözleşmesi | Şartname K002'de kalır, kod beklemede |
| 4 | Sartname ölçümü tutmazsa | §3.3 | imalat-ve-test kapısı | Tasarım marjı + yeniden ölçüm |
| 5 | 3 ajan sonuç vermezse | §3.2 / Q-B2 | Alt-konu derinliği | Boş satırlar ⚠️ kalır — doldurulmaz |
| 6 | Registry K002'yi kayıt etmezse | §3.1.6 | Keşif/yönlendirme | Orkestratöre bildirim (yazım yasak) |
| 7 | CP1254 dosya UTF-8 sanılırsa | §3.2 notu | Alıntı mojibake | UTF-8 okuma (ReadAllLines) |
| 8 | Envanter 10.323 tekrarlanırsa | V6 | Doğrulama dosyası | 8.095 disk ölçümü yazılır |

**Ortak kural:** Kırılma senaryosunda dosya SİLİNMEZ, satır `⚠️ VERIFICATION
REQUIRED` veya `UNKNOWN` ile işaretlenir ve üst karara taşınır.

## §17 Bağımlılık Yoksayma Denetimi (ne bilerek yoksayıldı)

| # | Yoksayılan | Neden yoksayıldı | Kayıt |
|---|---|---|---|
| 1 | JUCE/ASIO gerçek sürümleri | Bu turda diske bakılmadı | §8.1-2 ⚠️ |
| 2 | K004/K005 dosya sayısı | Okunmadı | Q-B1 ⚠️ |
| 3 | K001 dosya sayısı | Komşu yazım yasak — okumak da gerekmedi | §3.1.1 (bilerek) |
| 4 | PHP sürümü (composer.json) | K002 dışı, okunmadı | §8.3 ⚠️ |
| 5 | Fiziksel ölçüm sonuçları | Donanım yok (PLANNED) | §3.3 |
| 6 | EMC standart numaraları | Doğrulanmadı | master §14.3 ⚠️ |
| 7 | K000/electroncis satır sayısı | Yazım yasak, ölçüm gerekmedi | §G.4 kapsam ⚠️ |
| 8 | `.ai/electronic` kodlama detayı (CP1254) | Keşifte tespit edildi — alıntı notu yazıldı | §3.2 |

**Yoksayma bütünlüğü:** 8 maddenin tamamı bilinçli ve kayıtlıdır; hiçbiri
"varmış gibi" yazılmadı. Yoksayma, uydurmaktan farklıdır — yoksayma ⚠️ taşır.

## §18 Bağımlılık Geçmişi

| # | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 1 | 2026-10-13 | Bağımlılık defteri ilk yazım (§1-§7) | Vault Steward |
| 2 | 2026-10-13 | §8-§14 versiyon/öncelik/derinlik | Vault Steward |
| 3 | 2026-10-13 | §15-§17 harita/kırılma/yoksayma | Vault Steward |
| 4 | 2026-10-13 | V6 envanter çürütmesi bağlandı (8.095) | Vault Steward |
| 5 | — | Q-B1, Q-B2 açık — üst karar/ajan bekliyor | BEKLEMEDE |

## §19 Bağımlılık Sorgu Kılavuzu (hangi soruya hangi tablo)

| # | Soru | Tablo |
|---|---|---|
| 1 | "Bu dosya neye bağımlı?" | §2.1 (kök) · §2.3 (alt-konu) |
| 2 | "K002 dışarıya ne borçlu?" | §3.1-§3.3 |
| 3 | "Bağımlılık kırılırsa ne olur?" | §4 · §16 |
| 4 | "Hangisi önce gelir?" | §9 öncelik sırası |
| 5 | "Sürüm/ortam bağımlılığı var mı?" | §8 |
| 6 | "En kritik dış bağımlılık hangisi?" | §15.1 (ADR-038 — 4 alt-konu) |
| 7 | "Bilerek ne atlandı?" | §17 yoksayma denetimi |
| 8 | "Doğrulama yapıldı mı?" | §6 + `electroncis-dogrulama.md` |

## §20 Sık Hatalar ve Doğru Davranış

| # | Hata | Sonuç | Doğru davranış |
|---|---|---|---|
| 1 | Bağımlılığı "varsayım" diye yazmak | Hallüsinasyon | Kanıt satırı zorunlu (§10.4) |
| 2 | Yön karıştırma (K002 → K001 mi, tersi mi?) | Ters hiyerarşi | §5 ok yönü: ↓ bağımlılık |
| 3 | Sınırı bağımlılık sanma (K000/electroncis) | Kapsam sızıntısı | §3.1.4-5 — sınır ≠ bağımlılık |
| 4 | Frozen ADR'yi es geçmek | Kural 6/11 ihlali | §11.1 — frozen en üstte |
| 5 | Bilinmeyen sürümü tahmin etmek | Hallüsinasyon | §8 ⚠️ — tahmin yok |
| 6 | Envantarı iddiaya uydurmak | V6 benzeri hata | Disk ölçümü yaz (8.095) |
| 7 | CP1254 dosyayı doğrudan kopyalamak | Mojibake | UTF-8 okuma, temiz yazma |
| 8 | Q-B1'i cevaplamış gibi göstermek | Yalan | ⚠️ + BEKLEMEDE kalır |

## §21 Bağımlılık Defteri Bütünlük Testi

| # | Test | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Her dış bağımlılığın kanıtı var mı? | §3.1-3.3 satır kanıtları | EVET (2'si ⚠️ işaretli) |
| 2 | Ok yönü döngü mü? | §5 şemasında ↑ bağımlılık yok | EVET — döngü yok |
| 3 | Kritik bağımlılıklar derinlendi mi? | §11.1-3 (ADR/sartname/envanter) | EVET |
| 4 | Yoksaymalar kayıtlı mı? | §17 = 8 madde, hepsi ⚠️ | EVET |
| 5 | Uydurma sürüm var mı? | §8 — tümü ⚠️/PLANNED/SABİT | EVET — yok |
| 6 | Repo kanıtları dosya:satır mı? | §7 — 5 satır | EVET |
| 7 | "Elektronik kodu YOK" dürüstlüğü | §7 son paragraf | EVET — yazıldı |
| 8 | Açık sorular açık mı? | Q-B1, Q-B2 BEKLEMEDE | EVET — uydurulmadı |

## §22 Dış Bağımlılık Envanteri — Dosya Bazlı Kanıt

### §22.1 Vault dosya bağımlılıkları (ham kaynak)

| # | Dosya | Satır | Etkilenen alt-konu | Kanıt türü |
|---|---|---|---|---|
| 1 | `class-ab.md` | 500 | devre · guc | Aktif (alıntı yapıldı) |
| 2 | `class-d.md` | 418 | devre · guc | Aktif |
| 3 | `power-supply.md` | 273 | guc | Aktif |
| 4 | `protection.md` | 455 | guc | Aktif |
| 5 | `pcb-design.md` | 90 | imalat | Aktif |
| 6 | `thermal.md` | 82 | guc · imalat | Aktif |
| 7 | `frequency-response.md` | 70 | devre · imalat | Aktif |
| 8 | `snr-thd-measurement.md` | 86 | imalat | Aktif |
| 9 | `boot-sequence.md` | 81 | dijital | Aktif |
| 10 | `rtos.md` | 68 | dijital | Aktif |
| 11 | `device-integration.md` | 73 | sensor | Aktif |
| 12 | `audio-drivers.md` | 652 | dijital (sınır K000) | Aktif — sınır notu |
| 13 | `development-workflow.md` | 623 | imalat | Aktif |
| 14 | Kalan 47 dosya | ⚠️ satır bazlı okunmadı | hepsi | ⚠️ VERIFICATION REQUIRED |

**Toplamlar:** 13 dosya satır bazlı kanıtlandı (toplam 3.471 satır) · 47 dosya
okunmadı (kalan) · envanter toplamı 60 dosya / 8.095 satır (disk ölçümü).
13 + 47 = 60 — sayımlar tutarlı, kayıp yok.

### §22.2 Repo kodu bağımlılıkları (yalnız okuma — bu tur)

| # | Yüzey | Bağımlılık türü | K002 ilişkisi |
|---|---|---|---|
| 1 | `shared/src/Api/Bff/` | OS tespiti (php_uname) | K000 sınırı — kanıt |
| 2 | `shared/src/Session/` | Dosya sistemi (temp dir) | OS bağımlılığı — kanıt |
| 3 | `shared/src/Cache/` | APCu (function_exists) | Ortam bağımlılığı — kanıt |
| 4 | `shared/src/AI/` | ffprobe shell_exec | Dış araç — kanıt |
| 5 | `home.coremusic.net/include/Stream/` | Yol ayrıştırma (OS) | OS bağımlılığı — kanıt |
| 6 | `*.cpp/*.h` (Neva Engine) | L0 domain kural kaydı: disk 0 dosya | L0 PLANNED — bu domainde değil |

**Sonuç:** Repo kod bağımlılıklarının 6/6'sı OS/PHP/dış araç düzeyindedir;
elektronik/devre kodu bağımlılığı **0** (`*.cpp/*.h = 0` L0 kural kaydı; bu turda
tarama yapılmadı → sayı L0 kaydına dayanır). `.ai/electronic/**` yalnız Markdown vault'tur (60 dosya).

## §23 Sıradaki Bağımlılık Adımları (bu tur sonrası)

| # | Adım | Sorumlu | Beklenen çıktı | Bağımlılığı |
|---|---|---|---|---|
| 1 | Q-B1: K004/K005 disk okuması | Orkestratör / explore ajan | Var/yok + dosya sayısı | §3.1.2-3 kapanır |
| 2 | Q-B2: `.ai/electronic/**` derinleştirme | 3 ajan (görev tanımı) | 60 dosya işlenmiş | §3.2 + Q-K2-4 |
| 3 | JUCE/ASIO sürüm kanıtı | L0 Embedded (kural 12) | Gerçek sürümler | §8.1-2 kapanır |
| 4 | Envanter 13/47 açılımı | Derinleştirme ajanları | 47 dosya satır kanıtı | §22.1.14 kapanır |
| 5 | Q-K2-1..3 sınır kararları | Üst karar (kullanıcı) | Karar + §I kaydı | Kapsam kapanır |

**Sıra kuralı:** Adım 1 ve 2 paralel yürütülebilir; adım 3 bağımsızdır;
adım 4 adım 2'ye, adım 5 üst karara bağlıdır. Hiçbir adım tamamlanmadan
ilgili tablo satırı "Aktif" yapılmaz — `⚠️` kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
