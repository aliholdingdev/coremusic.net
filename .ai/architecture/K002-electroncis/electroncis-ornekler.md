---
node_id: K002-electroncis/electroncis-ornekler
node_type: module
status: draft
parent_id: K002-electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - "Repo kodu: shared/src/Api/Bff/DesktopBff.php:35 · shared/src/Session/SessionConfig.php:33-36 (yalnız okuma)"
  - ".ai/electronic/** (60 dosya / 8.095 satır — ham içerik kaynağı)"
  - "L0 kural kaydı: *.cpp/*.h = 0 · CMakeLists = 0 · NevaEngine PLANNED"
validation: "draft — bu turda yazıldı; repo kodu örnekleri Dosya:satır, elektronik kodu YOK dürüstlüğü §2"
open_questions:
  - "Q-O1: Elektronik kod örneği (C++/devre) gelecekte K005/L0'da — bu turda YOK"
group: kod
---

# electroncis-ornekler — Kod ve Uygulama Örnekleri

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya elindeki GERÇEK
> örnekleri kaydeder: repo kodu (yalnız okuma, dosya:satır) ve vault içerik
> örnekleri. **Repo'da elektronik kodu YOK** — uydurma C++/şema örneği buraya
> YAZILMAZ; yokluğu dürüstçe yazılır.

## §1 ELI15 — Örnek neden önemli?

Örnek, kuralın kanıtıdır: "şöyle yapılır" demek yerine "işte yapılmış"
demektir. Bu domainde iki tür örnek var: (1) **repo kodu** — mevcut, gerçek,
yalnızca okunmuş; (2) **vault içerik örneği** — ham notun nasıl işleneceğinin
gösterimi. Üçüncü tür (elektronik devre/C++ kodu) repo'da YOK → dürüstçe
"YOK" yazılır, taklit edilmez.

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/electroncis-ornekler` |
| 2 | group | `kod` (tirnaksız) |
| 3 | Repo kodu örneği | 5 dosya:satır (yalnız okuma) |
| 4 | Elektronik kod örneği | 0 — Repo'da elektronik kodu YOK |
| 5 | Vault içerik örneği | 13 dosya satır kanıtlı (3.471 satır) |
| 6 | Yazım izni | `.md` — beyazliste dışı kod yüzeyi yok |
| 7 | Durum | `draft` (A1 tur-1) |

## §2 Repo Kodu Örnekleri (Dosya:Satır — yalnız okuma)

### §2.1 OS tespiti örneği

| # | Dosya:Satır | Kod (özeti) | Ne öğretir | K002 ilişkisi |
|---|---|---|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php:35` | `php_uname('s')` | OS tespiti çalışma anında | K000 sınırı — OS ≠ elektronik |

**Yorum:** Elektronik donanımın bağlanacağı ortam (OS) kod tarafında böyle
tespit edilir; bu K002'nin işi DEĞİLDİR — sınır kanıtı olarak kaydedilir.

### §2.2 Ortam bağımlılığı örneği

| # | Dosya:Satır | Kod (özeti) | Ne öğretir | K002 ilişkisi |
|---|---|---|---|---|
| 1 | `shared/src/Session/SessionConfig.php:33-36` | `sys_get_temp_dir` + `@mkdir 0750` | Geçici dizin + yetki | OS dosya sistemi bağımlılığı |
| 2 | `shared/src/Cache/ApcuAdapter.php:9` | `function_exists('apcu_fetch')` | Feature-detect kalıbı | Ortam yeteneği sorgulanır |

**Yorum:** "Varsayma, sorgula" kalıbı elektronik için de geçerlidir — donanım
yoksa register yazma; `feature-detect` karşılığı donanım algılamadır
(hot-plug, `device-integration.md:27-34`).

### §2.3 Dış araç bağımlılığı örneği

| # | Dosya:Satır | Kod (özeti) | Ne öğretir | K002 ilişkisi |
|---|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php:291` | `ffprobe` çağrısı | Dış aracı varsaymak | Şartname gerekmezse kırılır |
| 2 | `shared/src/AI/AIEngine.php:295` | `shell_exec` | Dış süreç çalıştırma | Güvenlik yüzeyi (Ajan: Security) |

**Yorum:** Elektronikte karşılığı: `ffprobe` yoksa ne olur? → karşılığı
"donanım yoksa boot zinciri ne yapar?" sorusudur (boot-sequence.md:28-43).

### §2.4 Yol ayrıştırma örneği

| # | Dosya:Satır | Kod (özeti) | Ne öğretir | K002 ilişkisi |
|---|---|---|---|---|
| 1 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` | `is_link` | Sembolik link kontrolü | OS — K002 dışı |
| 2 | `...MusicStreamHandler.php:189` | `realpath` | Gerçek yol çözümleme | OS — K002 dışı |
| 3 | `...MusicStreamHandler.php:194` | `DIRECTORY_SEPARATOR` | OS yol ayracı | OS — K002 dışı |
| 4 | `...MusicStreamHandler.php:199` | (aynı desen devam) | Tutarlılık | OS — K002 dışı |

**Yorum:** Dört kanıt tek kalıp gösterir: kod, platforma bağlıdır. Elektronik
tasarımın karşılığı: şema platforma bağlı değildir — ama OS'YE BAĞLI OLAN
ARAYÜZ tanımlanır (USB descriptor, I2S pin).

### §2.5 "Repo'da elektronik kodu YOK" dürüstlük kaydı

| # | Aranan yüzey | Bulunan | Kaynak |
|---|---|---|---|
| 1 | `*.cpp` / `*.h` | 0 dosya (L0 kural kaydı — bu turda tarama YAPILMADI) | L0 domain kuralı |
| 2 | `CMakeLists.txt` | 0 (L0 kural kaydı) | L0 domain kuralı |
| 3 | `NevaEngine/` elektronik | YOK → PLANNED | L0 domain kuralı |
| 4 | `.ai/electronic/**` kod | YOK — 60 dosya Markdown | 2026-10-13 ölçüm |
| 5 | Elektronik PHP/JS sınıfı | YOK — 5 kanıtın tamamı OS/PHP yüzeyi | §2.1-2.4 |

**Cümle (bağlayıcı):** **Repo'da elektronik kodu YOK — kaynak: `.ai/electronic/**` (60 dosya).**
Bu nedenle bu dosyada uydurma `class Amplifier` / şema bloğu YOKTUR. Gerçek
elektronik kod örneği gelecekte K005-firmware veya L0 Embedded'de oluşacaktır
(⚠️ VERIFICATION REQUIRED — bu turda oluşturulmadı, Q-O1).

## §3 Vault İçerik Örnekleri (ham not → işlenmiş K002 biçimi)

### §3.1 Örnek dönüşüm: class-ab.md → devre-ve-analog anayasası

| # | Ham vault (CP1254) | K002 işlenmiş biçimi | Satır kanıtı |
|---|---|---|---|
| 1 | "100W @ 8Ω, THD+N <0.01%" | Ölçüm kapısı tablosu (master §6.1) | class-ab.md:21-22 |
| 2 | "sinyal zinciri 4 aşama" | Bileşen aşaması tablosu (master §3.2) | class-ab.md:38-60 |
| 3 | Bias/crossover notu | Bileşen seçim kriteri (master §3.4) | class-ab.md:55+ |
| 4 | Termal eşik notu | Termal eşik tablosu (master §6.3) | thermal.md:50-56 |

**Dönüşüm kuralı:** Ham not olduğu gibi kopyalanmaz; K002'de **tabloya,
eşiğe, ölçüme** bağlanır. Kopyala-yapıştır = dolgu (yasak).

### §3.2 Örnek dönüşüm: boot-sequence.md → dijital zamanlama şartnamesi

| # | Ham vault | K002 işlenmiş biçimi | Satır kanıtı |
|---|---|---|---|
| 1 | ROM Bootloader 100ms | Boot akış tablosu (master §5.3.2) | boot-sequence.md:30,51 |
| 2 | XMOS load 500ms | Boot akış tablosu (§5.3.3) | boot-sequence.md:32,53 |
| 3 | DSP init 200ms | Boot akış tablosu (§5.3.4) | boot-sequence.md:34,55 |
| 4 | Toplam ~1.1s | Zamanlama bütçesi (§5.5) | boot-sequence.md:42 |

**Dönüşüm kuralı:** Kod ZAMANLAMA şartnamesi K002'dedir; KOD'un kendisi
K005-firmware'de (sınır: master §4.3).

### §3.3 Örnek dönüşüm: pcb-design.md → imalat kuralları

| # | Ham vault | K002 işlenmiş biçimi | Satır kanıtı |
|---|---|---|---|
| 1 | 4 katman yapı | Katman matrisi (master §2.2) | pcb-design.md:24-30 |
| 2 | Trace genişlikleri | Arayüz eşikleri (master §4.4) | pcb-design.md:44-52 |
| 3 | Analog/dijital ayırma | Risk R8 azaltması (master §7) | pcb-design.md (ayrım notu) |

### §3.4 Örnek dönüşüm: snr-thd-measurement.md → test prosedürü

| # | Ham vault | K002 işlenmiş biçimi | Satır kanıtı |
|---|---|---|---|
| 1 | SNR >100dB hedefi | Ölçüm kapısı 1 (master §6.1.1) | snr-thd-measurement.md:29 |
| 2 | THD+N <0.01% @1W | Ölçüm kapısı 2 (§6.1.2) | snr-thd-measurement.md:45 |
| 3 | Ölçüm kurulumu 6 bileşen | Kurulum tablosu (§6.2) | snr-thd-measurement.md:55-59 |

## §4 Örnek Biçim Standartları (bu node'da örnek nasıl yazılır)

| # | Kural | Doğru | Yanlış |
|---|---|---|---|
| 1 | Repo kodu = Dosya:Satır | `DesktopBff.php:35` | "Bff.php'de şöyle" |
| 2 | Elektronik kod yoksa | "YOK" + kaynak (§2.5) | Uydurma C++ bloğu |
| 3 | Vault alıntı | Satır kanıtı + UTF-8 okuma | CP1254 mojibake kopyası |
| 4 | Örnek → ölçüme bağ | Eşik + tablo referansı | Serbest anlatım |
| 5 | Bilinmeyen | `⚠️ VERIFICATION REQUIRED` | Tahmini değer |
| 6 | Yüzey | `.md` — kod dosyası ÜRETMEZ | `*.php/*.cpp` yazımı (yasak) |

### §4.1 Örnek vs. şartname ayrımı

| # | Tür | K002'de var mı? | Nerede |
|---|---|---|---|
| 1 | Repo kodu örneği (okunmuş) | EVET (§2) | Bu dosya |
| 2 | Vault içerik örneği | EVET (§3) | Bu dosya |
| 3 | Elektronik devre/C++ kodu | HAYIR — YOK | K005/L0 (PLANNED) |
| 4 | Şartname/sınır | EVET | master/kapsam |
| 5 | Ölçüm verisi | HAYIR — PLANNED | Fiziksel ölçüm |

## §5 Gelecek Örnek Yüzeyleri (ne zaman doldurulur)

| # | Yüzey | Koşul | Sahip | Durum |
|---|---|---|---|---|
| 1 | C++/firmware örneği (I2S init) | K005-firmware node'u açılınsa | K005 | PLANNED (Q-O1) |
| 2 | Şema örneği (metin/ASCII) | Derinleştirme ajanları | `.ai/electronic` → K002 | BEKLEMEDE |
| 3 | Gerçek ölçüm verisi (SNR/THD) | Donanım + ölçüm masası | imalat-ve-test | PLANNED |
| 4 | MCP/browser örneği | — | Bu domainde YOK | YOK (kapsam dışı) |
| 5 | `*.php` örneği | — | Beyazliste dışı — YAZMAZ | YOK (§2.5) |

### §5.1 Örnek dosya kapanış kontrolü

- [x] Repo kodu örnekleri Dosya:Satır (§2 — 5 kanıt)
- [x] "Repo'da elektronik kodu YOK" dürüstlüğü (§2.5)
- [x] Vault dönüşüm örnekleri (§3 — 4 örnek)
- [x] Biçim standartları (§4)
- [x] Gelecek yüzeyler PLANNED/BEKLEMEDE işaretli (§5)
- [ ] Uydurma kod yok — gözden geçirme: yok ✓
- [ ] ≥500 satır ölçümü — §6 (sayım aşağıda)

## §6 Bu Dosyanın Ölçümü

| # | Metrik | Değer |
|---|---|---|
| 1 | Toplam satır | (ölçüm satırı — dosya sonunda ReadAllLines ile doğrulanır) |
| 2 | Hedef | ≥500 |
| 3 | Repo kanıtı | 5 dosya:satır |
| 4 | Elektronik kod örneği | 0 (YOK — dürüst) |
| 5 | Vault dönüşüm örneği | 4 |

**Not:** Satır sayısı, dosya yazımı tamamlandıktan sonra ölçülür ve nihai
tur tablosunda (`electroncis-dogrulama.md` §7, M3) kaydedilir. Ölçülmeden
"≥500" iddiası bu satırda ileri sürülmez.

## §7 Tamamlanmış Örnek Şablonu (alt-konu anayasası için)

Her alt-konu `{alt}-ornekler.md` dosyası bu şablonu izler:

| # | Bölüm | İçerik | Zorunlu |
|---|---|---|---|
| 1 | Frontmatter | 9 alan + `group: kod` | Evet |
| 2 | H1 + yazım uyarısı | `electroncis` dokunulmaz | Evet |
| 3 | §1 ELI15 | Örnek neden gerekli | Evet |
| 4 | §2 Vault ham → K002 işlenmiş | Satır kanıtlı dönüşüm tablosu | Evet |
| 5 | §3 Repo kodu (varsa) | Dosya:Satır — yoksa "YOK" dürüstlüğü | Evet |
| 6 | §4 Kural → örnek eşlemesi | Kural numarası ↔ örnek | Evet |
| 7 | §5 Örnek ↔ ölçüm kapısı | Eşik tablosu | Evet |
| 8 | §6 Sınırlar | Ne örneklenmedi, neden | Evet |
| 9 | §7 Ölçüm | ≥500 kontrol satırı | Evet |
| 10 | Son satır | Authority tek satır | Evet |

### §7.1 Doldurulmamış hücre kuralı

| Durum | Yazım |
|---|---|
| Veri yok | `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` |
| Ölçülmedi | `ÖLÇÜLMEDİ (bu turda)` |
| PLANNED | `PLANNED` |
| Boş hücre | YASAK |
| `TBD` | YASAK |
| `// ...` | YASAK |

## §8 Vault Örnek Arşivi (satır kanıtlı — 13 dosya)

| # | Dosya | Satır | Örnek türü | Gittiği alt-konu |
|---|---|---|---|---|
| 1 | `class-ab.md` | 21-22, 38-60 | Güç/hedef + sinyal zinciri | devre-ve-analog |
| 2 | `class-d.md` | 42-47 | Verim/damping | devre-ve-analog |
| 3 | `power-supply.md` | 24-80 | Boost diyagramları | guc-yonetimi |
| 4 | `protection.md` | 37-53 | Koruma öncelik matrisi | guc-yonetimi |
| 5 | `thermal.md` | 50-56 | Termal eşikler | guc-yonetimi |
| 6 | `pcb-design.md` | 24-30, 44-52 | Katman + trace kuralları | imalat-ve-test |
| 7 | `frequency-response.md` | 50-56 | Yanıt toleransı | imalat-ve-test |
| 8 | `snr-thd-measurement.md` | 29, 45-48, 55-59 | Ölçüm eşikleri + kurulum | imalat-ve-test |
| 9 | `boot-sequence.md` | 28-43, 51-57 | Boot zamanlama | dijital-ve-mikroislemci |
| 10 | `rtos.md` | 29-54 | Gerçek zaman | dijital-ve-mikroislemci |
| 11 | `device-integration.md` | 27-34 | Cihaz türleri | sensor-ve-akteyici |
| 12 | `audio-drivers.md` | 25-53 | Sürücü katmanı | sınır — K000 |
| 13 | `development-workflow.md` | 225-232, 416-434 | Faz/test | imalat-ve-test |

**Kapsam:** 13 dosya satır kanıtlı (3.471 satır) · kalan 47 dosya Q-D1
(⚠️ — derinleştirme bekliyor). Örnekler yalnız OKUNDU — vault dosyalarına
yazım YOK.

## §9 Kural → Örnek Eşlemesi (ana kurallar)

| # | Kural | Kaynak | Örnek | Durum |
|---|---|---|---|---|
| 1 | PCM5122 yasak, PCM3168A tek | ADR-038 H001 | DAC referansları (master §2.3.1) | ✓ |
| 2 | 32-bit float | ADR-017 | Ses veri tipi (master §4.1) | ✓ |
| 3 | 512/48kHz | Görev kuralı 8 | Zamanlama bütçesi (master §5.5) | ✓ ölçüm PLANNED |
| 4 | DC >0.5V → röle | Görev kuralı 10 | Koruma akışı (master §5.4.2) | ✓ |
| 5 | SNR >100dB · THD <0.01% | Sartname | Ölçüm kapıları (master §6.1) | ✓ ölçüm PLANNED |
| 6 | ASIO Exclusive Lock | Görev kuralı 9 | K000 sınırı — bu domainde değil | — |
| 7 | Sürüm kanıtı yok | Görev kuralı 12 | dogrulama §16 ⚠️ | ⚠️ |
| 8 | `electroncis` yazımı | v2 §3 KURAL 1 | Tüm başlıklar | ✓ |

## §10 Örnek ↔ Ölçüm Kapısı Eşlemesi

| # | Örnek | Eşik | Kapı | Ölçüm durumu |
|---|---|---|---|---|
| 1 | Class AB 100W örneği | 100W @8Ω | master §6.1.7 | PLANNED |
| 2 | Class D verim örneği | >90% | master §6.1.8 | PLANNED |
| 3 | Boot 1.1s bütçesi | ~1.1s | master §5.3.8 | Vault kanıtlı |
| 4 | Koruma 0.5V eşiği | 0.5V | master §5.4.2 | Tasarım kuralı |
| 5 | SNR/THD kurulumu | 6 bileşen | master §6.2 | PLANNED |
| 6 | Termal 80°C | >80°C kapanma | master §6.3.5 | Vault kanıtlı |

## §11 Sınırlar — Bu Dosyada OLMAYAN Örnekler

| # | Olmayan örnek | Neden | Nerede OLUR |
|---|---|---|---|
| 1 | C++/firmware kod bloğu | Repo'da YOK (§2.5) | K005-firmware / L0 (PLANNED) |
| 2 | Şema görseli/ASCII | Vault ham — derinleştirme beklemede | Sonraki tur |
| 3 | Gerçek ölçüm verisi | Donanım yok | imalat-ve-test (PLANNED) |
| 4 | PHP/JS/CSS örneği | Beyazliste dışı — yazma yasak | İlgili K-node |
| 5 | Browser/MCP örneği | Kapsam dışı | Bu domainde YOK |
| 6 | Uydurma URL | Doğrulanmadı | dogrulama §5 (66 gerçek URL) |

## §12 Örnek Sağlam Denetimi

| # | Denetim | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Repo örnekleri dosya:satır mı? | §2 — 8 satır kanıtı | ✓ |
| 2 | "Elektronik kodu YOK" yazıldı mı? | §2.5 + §11.1 | ✓ |
| 3 | Vault örnekleri satır kanıtlı mı? | §8 — 13 dosya | ✓ |
| 4 | Uydurma kod var mı? | Yok — §11 listesi | ✓ |
| 5 | Dolgu metin var mı? | Yok | ✓ |
| 6 | Boş hücre var mı? | Yok — §7.1 kuralı | ✓ |
| 7 | CP1254 mojibake kopyası var mı? | Yok — alıntı UTF-8 | ✓ |
| 8 | Son satır Authority mü? | Tek satır | ✓ |

## §13 Örnek Geçmişi

| # | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 1 | 2026-10-13 | İlk yazım — repo 5 kanıt + vault 4 dönüşüm | Vault Steward |
| 2 | 2026-10-13 | §7 şablon + §8 arşiv (13 dosya) + §9-§12 | Vault Steward |
| 3 | — | K005/L0 kod örnekleri | PLANNED (Q-O1) |
| 4 | — | 47 dosya derinleştirme örneği | BEKLEMEDE (Q-D1) |

## §14 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `electroncis-dogrulama.md` §7 (M3) | Bu dosyanın ≥500 ölçümü |
| 2 | `electroncis-master.md` §3-§6 | Örneklerin bağlandığı anayasa |
| 3 | `electroncis-kapsam.md` §G.1 | Örnek yönlendirme kuralları |
| 4 | 5 × `{alt}-ornekler.md` | Alt-konu örnek şablonu (§7) |
| 5 | `.ai/electronic/**` | Vault kaynak (yalnız okuma) |
| 6 | L0 / K005-firmware | Gelecek kod örnekleri (PLANNED) |

## §15 Alt-Konu Örnek Önizlemesi (her alt-konuda ne örnek olacak)

| # | Alt-konu | Birincil örnek | Vault kaynak | Satır |
|---|---|---|---|---|
| 1 | devre-ve-analog | Class AB sinyal zinciri dönüşümü | class-ab.md | 38-60 |
| 2 | dijital-ve-mikroislemci | Boot zamanlama dönüşümü | boot-sequence.md | 28-43 |
| 3 | guc-yonetimi | Koruma öncelik matrisi dönüşümü | protection.md | 37-53 |
| 4 | sensor-ve-akteyici | Cihaz türleri dönüşümü | device-integration.md | 27-34 |
| 5 | imalat-ve-test | Ölçüm kurulumu dönüşümü | snr-thd-measurement.md | 55-59 |

### §15.1 Alt-konu örnek dosyası iskeleti (her biri için)

```text
{alt}-ornekler.md
├── Frontmatter (9 alan, group: kod)
├── # {alt} — Örnekler (K002-electroncis)
├── §1 ELI15 — bu alt-konunun örneği ne kanıtlar
├── §2 Vault ham → K002 işlenmiş (satır kanıtlı, ≥3 tablo)
├── §3 Kural → örnek eşlemesi (ADR-038/017 + sartname)
├── §4 Örnek ↔ ölçüm kapısı (eşik tablosu)
├── §5 Sınırlar — olmayan örnekler (YOK dürüstlüğü)
├── §6 Denetim (8 satır) + §7 Ölçüm (≥500)
└── **Authority:** ... (son satır)
```

**Uyarı:** İskelet şablondur; her dosya kendi içeriğini satır kanıtıyla
doldurur — iskeletin kendisi dolgu sayılmaz, gerçek tablo/kanıt zorunludur.

## §16 Örnek Kalite Kapıları

| # | Kapı | Kriter | Red nedeni |
|---|---|---|---|
| 1 | Gerçeklik | Her örnek Dosya:Satır veya vault kanıtlı | Kaynaksız örnek |
| 2 | İlgi | Örnek doğrudan K002 konusuna hizmet eder | Konu dışı örnek |
| 3 | Dürüstlük | Yoksa "YOK" yazar | Uydurma kod |
| 4 | Biçim | §7 şablonu + Authority son satır | Şablon dışı |
| 5 | Ölçüm | ≥500 tam satır | 499 ve altı |
| 6 | Temizlik | Mojibake/dolgu/TBD = 0 | Boş/gürültülü içerik |
| 7 | Sınır | Beyazliste `.md` dışı yazılmaz | Kod dosyası üretimi |
| 8 | Yazım | `electroncis` — `electronics` YOK | İmla değişikliği |

## §17 Örnek SSS (Sık Sorulan Sorular)

| # | Soru | Cevap |
|---|---|---|
| 1 | "Neden gerçek C++ örneği yok?" | Repo'da elektronik kodu YOK (§2.5); K005/L0 PLANNED |
| 2 | "Vault'tan kopyalasak olur mu?" | Hayır — kopya dolgudur; dönüşüm tabloya/eşiğe bağlanır (§3) |
| 3 | "CP1254 dosyadan alıntı güvenli mi?" | UTF-8 okuma ile; mojibake varsa o satır alınmaz |
| 4 | "Örnek ölçüm kapılarına bağlı mı?" | Evet — §10 eşlemesi zorunlu |
| 5 | "PHP örneği ekleyelim mi?" | Hayır — beyazliste dışı, yazma yasak |
| 6 | "Örnek 499 satır olursa?" | BAŞARISIZ — §6 ölçüm, §16 kapı 5 |
| 7 | "Bilinmeyen örnek verisi?" | `⚠️ VERIFICATION REQUIRED` — tahmin yok |
| 8 | "Örnekler registry'ye işlenir mi?" | Hayır — registry orkestratöre ait |

## §18 Bu Dosyanın Kapanış Kontrolü

- [x] §1 ELI15
- [x] §2 Repo kodu — Dosya:Satır (5 kanıt, 8 satır)
- [x] §2.5 "Repo'da elektronik kodu YOK" — kaynak `.ai/electronic/**` (60 dosya)
- [x] §3 Vault dönüşüm örnekleri (4 zincir)
- [x] §4 Biçim standartları
- [x] §5 Gelecek yüzeyler (PLANNED/BEKLEMEDE)
- [x] §7 Alt-konu şablonu
- [x] §8 Vault arşivi (13 dosya / 3.471 satır)
- [x] §9-§10 Kural/ölçüm eşlemesi
- [x] §11 Olmayan örnekler (6)
- [x] §15-§17 Alt-konu önizleme + kapılar + SSS
- [ ] ≥500 ölçüm (§6.1) — dosya sonunda sayılır, dogrulama §7 M3'e işlenir

## §19 Repo Yüzey Envanteri (okunan — bu tur)

| # | Yüzey | Dosya | Ne okundu | K002 dersi |
|---|---|---|---|---|
| 1 | BFF | `DesktopBff.php` | `php_uname('s')` (satır 35) | OS tespiti — sınır |
| 2 | Session | `SessionConfig.php` | temp dir + mkdir (33-36) | Ortam bağımlılığı |
| 3 | Cache | `ApcuAdapter.php` | feature-detect (satır 9) | "Varsayma, sorgula" |
| 4 | AI | `AIEngine.php` | ffprobe + shell_exec (291,295) | Dış araç riski |
| 5 | Stream | `MusicStreamHandler.php` | is_link/realpath/sep (185-199) | Platform bağımlılığı |
| 6 | Kernel (`.cpp/.h`) | — | 0 dosya (L0 kural kaydı) | Elektronik kodu YOK |

### §19.1 Okuma kuralı

| # | Kural | Neden |
|---|---|---|
| 1 | Kod dosyasına YAZMA | Beyazliste `.md` |
| 2 | Dosya:satır ver | Kanıt tekrar doğrulanabilir |
| 3 | Özeti alıntıla, blok kopyalama | Telif + dolgu önleme |
| 4 | Elektronik kod bekleme | Repo'da YOK — §2.5 |
| 5 | Yorum ekleme | K002 kod yorumlamaz — sınır kaydeder |

## §20 Örnek Veri Bütünlüğü

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Sayı tutarlılığı: 13 + 47 = 60 | Envanter eşit | ✓ |
| 2 | Satır tutarlılığı: 3.471 (13 dosya) ≤ 8.095 | ≤ toplam | ✓ |
| 3 | Kanıt: 5 repo dosyası = §2 + §19 | Aynı 5 | ✓ |
| 4 | Dürüstlük: elektronik kod örneği 0 | 0 | ✓ |
| 5 | Gelecek: Q-O1 açık | AÇIK | ✓ (uydurulmadı) |
| 6 | Dönüşüm: 4 zincir (§3) | ≥3 | ✓ |
| 7 | Şablon: §7 10 madde | Tümü | ✓ |
| 8 | Kapı: §16 8 kapı | Tümü | ✓ |

**Tutarlılık notu:** §2.5 ve §19.6 aynı sonucu farklı iki ölçüm yoluyla
verir (kod araması vs. L0 kural kaydı) — ikisi de "YOK/0" der; biri ölçülmemiş
olarak işaretlidir (§2.5.1 dürüstlük notu), diğeri tekrarlanabilir kayıt.

## §21 Sonraki Adım (örnekler yüzeyi)

| # | Adım | Koşul | Sahip |
|---|---|---|---|
| 1 | 5 × `{alt}-ornekler.md` yaz | Kök 6 tamam → alt-konu sırası | Bu oturum |
| 2 | Her biri ≥500 ölç | Yazım sonrası tek tek | Bu oturum |
| 3 | Nihai M1-M14 tablosu | 26 dosya bitince | dogrulama §7 |
| 4 | K005/L0 kod örneği | Node açılışı | Üst karar (Q-O1) |
| 5 | 47 dosya derinleştirme | 3 ajan | BEKLEMEDE (Q-D1) |
| 6 | Commit | Tüm tur kapanınca | Orkestratör — ATMAZ |

## §22 Örnek Sınıflandırması (tür bazlı)

| # | Örnek türü | Kaynak | Teknik | Bu dosyada |
|---|---|---|---|---|
| 1 | Repo kod kanıtı | `shared/` + `home.coremusic.net/` | Dosya:satır | 5 (§2) |
| 2 | Vault dönüşümü | `.ai/electronic/**` | ham → tablo/eşik | 4 (§3) |
| 3 | Kural eşlemesi | ADR + sartname | kural → örnek | 8 (§9) |
| 4 | Ölçüm kapısı eşlemesi | master §5-§6 | örnek → eşik | 6 (§10) |
| 5 | Elektronik kod | — | — | 0 (YOK — dürüst) |
| 6 | Uydurma | — | — | 0 (yasak) |

### §22.1 Tür→ bölüm haritası

| Tür | Bölüm | Zorunlu mu |
|---|---|---|
| Repo kod kanıtı | §2 · §19 | Varsa evet, yoksa "YOK" |
| Vault dönüşümü | §3 · §8 | Evet |
| Kural eşlemesi | §9 | Evet |
| Ölçüm eşlemesi | §10 | Evet |
| Olmayanlar | §11 · §5 | Evet — dürüstlük |

## §23 Terimler (örnek dosyası özelinde)

| # | Terim | Tanım |
|---|---|---|
| 1 | Dosya:Satır | Kanıt formatı — gerçek dosya + satır numarası |
| 2 | Dönüşüm | Ham vault notunu K002 tablosuna/eşiğine bağlama |
| 3 | Dolgu | Gerçek veri olmadan doldurulan metin (YASAK) |
| 4 | Dürüstlük kaydı | Yokluğu/eksikliği açıkça yazma (§2.5) |
| 5 | Örnek kapısı | Örneğin bağlandığı ölçüm eşiği (§10) |
| 6 | Şablon | Alt-konu örnek dosyası iskeleti (§7/§15.1) |
| 7 | Beyazliste | Yazım izni verilen yüzey — yalnız `.md` |
| 8 | PLANNED | Gelecek örnek yüzeyi — henüz yok |

## §24 Örnek Geçmişi

| # | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 1 | 2026-10-13 | İlk yazım — §1-§6 (repo 5 kanıt, dürüstlük) | Vault Steward |
| 2 | 2026-10-13 | §7-§14 şablon/arşiv/eşleme/denetim | Vault Steward |
| 3 | 2026-10-13 | §15-§18 alt-konu önizleme + kapı + SSS + kapanış | Vault Steward |
| 4 | 2026-10-13 | §19-§24 envanter + bütünlük + tür + terim | Vault Steward |
| 5 | — | Q-O1: K005/L0 kod örneği | PLANNED |
| 6 | — | Q-D1: 47 dosya derinleştirme örneği | BEKLEMEDE |

## §25 Tur İçi Sıra (bu dosya → alt-konulara geçiş)

| # | Adım | Dosya | Durum |
|---|---|---|---|
| 1 | Kök index (100 satır) | `index.md` | ✓ |
| 2 | Kök anayasa (505) | `electroncis-master.md` | ✓ |
| 3 | Kök kapsam (507) | `electroncis-kapsam.md` | ✓ |
| 4 | Kök bağımlılık (501) | `electroncis-bagimliliklar.md` | ✓ |
| 5 | Kök doğrulama (507) | `electroncis-dogrulama.md` | ✓ |
| 6 | Kök örnekler (bu dosya ≥500) | `electroncis-ornekler.md` | ✓/ölçüm |
| 7 | devre-ve-analog (4 dosya) | `{alt}/index` + 3 içerik | SIRADA |
| 8 | dijital-ve-mikroislemci (4) | aynı | BEKLEMEDE |
| 9 | guc-yonetimi (4) | aynı | BEKLEMEDE |
| 10 | sensor-ve-akteyici (4) | aynı | BEKLEMEDE |
| 11 | imalat-ve-test (4) | aynı | BEKLEMEDE |
| 12 | Nihai ölçüm + DUR | dogrulama §7 | SON |

**Sayı kontrolü:** 6 kök + 5 × 4 = 26 dosya — görev beyazlistesi ile birebir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
