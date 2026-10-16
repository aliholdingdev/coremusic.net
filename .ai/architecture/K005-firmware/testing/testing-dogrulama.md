---
node_id: K005-firmware/testing/testing-dogrulama
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/testing
evidence:
  - "K005-firmware/testing/index.md (open_questions · §GRUPLAR) — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §3 · §4 · §6 · §7 · §10 · §11 · §16 · §17 · §18 · §19 · §20 · §25 · §28 · §30 · §32 — okundu 2026-10-14"
  - "K005-firmware/firmware-dogrulama.md §3 (D1–D10 + geçiş kuralı) — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §20 (TS1–TS14) · §23 (H17) — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §20.5 (HIL 5 adım · eşik UNKNOWN) — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-dogrulama.md §A · §C · §D (DD-1 aktarımı) — kardeş şablon, okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md §20 (TI1–TI16 · kapı B1–B5) — okundu 2026-10-14"
  - ".github/workflows/ci.yml — G1 · G3–G8, tam dosya okundu 2026-10-14"
  - ".github/workflows/secret-scan.yml — G2, tam dosya okundu 2026-10-14"
  - "URL 200 doğrulama: https://google.github.io/googletest/ — erişildi 2026-10-14"
  - "URL 200 doğrulama: https://github.com/ThrowTheSwitch/Ceedling — erişildi 2026-10-14"
  - "URL 200 doğrulama: https://github.com/ThrowTheSwitch/Unity — erişildi 2026-10-14"
  - "URL 200 doğrulama: https://docs.zephyrproject.org/latest/develop/test/index.html — erişildi 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "test kapsamı hedefi tanımlı değil ⚠️ (index.md open_question — çözülmedi, DV-T5/Q0)"
  - "Hiçbir kapı bu üretimde ÇALIŞTIRILMADI — tüm Durum sütunu tasarım/PLANNED seviyesi"
  - "CI geçmişi okunmadı — 'CI yeşil' doğrulanmadı ⚠️"
  - "G9 firmware HIL workflow'u yok — DV-T6…DV-T14 için CI kapısı tanımsız ⚠️"
group: kanit
---

# Testing Alt-Modülü — Doğrulama (K005-firmware/testing/testing-dogrulama.md)

> Bu dosya, `K005-firmware/testing/` alt-modülünün **doğrulama**
> dokümanıdır. Eşikler sartname/boot köprülerinden ve kök dosyalardan
> alınmıştır; repo'da firmware kodu ve HIL tezgâhı bulunmadığından
> **hiçbir senaryo bu üretimde çalıştırılmamıştır**. Durum sütununda yalnız
> `Tasarım seviyesi` · `ölçüm PLANNED` · `yok` · `kısmi` etiketleri
> kullanılır — **"geçti" etiketi hiçbir satırda yoktur.**

## §A Doğrulama Senaryoları (DV-T serisi)

| # | Senaryo | Yöntem / giriş | Beklenen | Durum |
|---|---|---|---|---|
| DV-T1 | Piramit katman denetimi | strateji §2 sayımı | 6 katman (L1–L6) | Tasarım seviyesi (M1 ✅ sayım) |
| DV-T2 | CI kapı envanteri | workflow dosyaları okuma (2026-10-14) | 8 mevcut + 1 eksik (G9) | Tasarım seviyesi (M2 ✅ · M3 ⚠️) |
| DV-T3 | Soft kapı denetimi | `ci.yml` okuma | G6/G7 `continue-on-error: true` | Tasarım seviyesi (okundu — disk kanıtı) |
| DV-T4 | Senaryo ↔ risk bağları | strateji §14 tablosu | 10/10 bağlı | Tasarım seviyesi (M5 ✅) |
| DV-T5 | Coverage hedefi | strateji §11 M7 + index Q0 | hedef = **0 (uydurma yok)** | açık ⚠️ — Q0 çözülmedi |
| DV-T6 | Açılış süresi ölçümü | HIL kronometre (strateji §6 ölçüm 1) | eşik tanımlanacak | ölçüm PLANNED — eşik yok |
| DV-T7 | PCM3168A gerçek çıkış ölçümü | HIL — DAC çıkışı (§20 satır 1) | doğrulanmış çıkış | ölçüm PLANNED (donanım bekleniyor) |
| DV-T8 | Röle DC offset testi | HIL — DMM, >0.5V (§20 satır 2) | röle tetiklenir | ölçüm PLANNED |
| DV-T9 | ASIO Exclusive Lock çok-uygulama | HIL — 2 uygulama (§20 satır 3) | kilit çakışması yönetilir | ölçüm PLANNED (2 uygulama gerekir) |
| DV-T10 | 8.1 surround kanal haritası | HIL (§20 satır 4) | 9 kanal doğrulanır | ölçüm PLANNED |
| DV-T11 | S-BOOT-01 açılış senaryosu | tezgâh — enerji ver/kes | sıralı açılış, tekrarlanabilir | yok (senaryo PLANNED — `testing-ornekler.md` §A.2) |
| DV-T12 | S-UPD-01 geri alma senaryosu | tezgâh — bozuk doğrulama | eskiye dönüş, tuğla yok | yok (risk 3 🔴 — henüz koşulmadı) |
| DV-T13 | S-PWR-01 güç kesintisi | tezgâh — besleme kesme | veri bozulması 0 | yok |
| DV-T14 | S-DEV-02 komut yankısı | host simülasyon (stub t2) | hata kodu + onay yankısı | yok (t1/t2 dalga-3) |
| DV-T15 | D-kapıları D1–D10 | kök dosya (strateji §4) | tamamı ✅ → `verified` | kısmi (D9 · D10 ❌) |
| DV-T16 | Statik analiz — tahsis/mutex denetimi | cppcheck / Clang-Tidy (planlı) | 0 tahsis · 0 mutex | yok (firmware yüzeyinde araç tanımsız ⚠️) |
| DV-T17 | CI geçmişi doğrulaması | CI log okuma | işlerin gerçekten geçtiği | yok — log OKUNMADI ⚠️ |
| DV-T18 | Flaky politikası uygulaması | test geçmişi (§16) | flaky = 0 veya izleniyor | yok (test yok — politika taslak) |

> **Durum kuralı (V1):** `Tasarım seviyesi` = kural/kayıt diskte vardır ama
> test çalıştırılmamıştır · `ölçüm PLANNED` = donanım/araç bekleniyor ·
> `yok` = ön koşul (kod · tezgâh · log) mevcut değil. Hiçbir satır
> "geçti" demez (anayasa P10 · strateji §32 satır 3).

## §B D-Kapı ↔ Test Eşlemesi

| # | D-kapısı | Test karşılığı | Şu an (kök dosya, 2026-10-13/14) |
|---|---|---|---|
| D1 | spec birebirliği | karşılaştırma testi (elle) | ✅ 2026-10-12 |
| D2 | 12 kök MD mevcut | script assert (dizin sayımı) | ✅ 2026-10-13 |
| D3 | Kademe-2 çocuk şeması | script assert | 🔄 |
| D4 | Frontmatter anahtar/sıra | script assert | 🔄 |
| D5 | `## §GRUPLAR` varlığı | grep assert | ✅ 2026-10-13 |
| D6 | Web iddiaları 4 sütun tablo | grep assert (§E bu dosyanın tablosu) | ✅ 2026-10-13 |
| D7 | Kod örnekleri gerçek künye | dosya okuma assert | ✅ 2026-10-13 |
| D8 | Dolgu kelimesi yok | grep (0 eşleşme) | 🔄 |
| D9 | Açık ⚠️/UNKNOWN kalmadı | oturum kapanışı | ❌ açık |
| D10 | `validation:` → `verified` | D1–D9 ✅ | ❌ |

**Geçiş kuralı (V2):** D1–D10 tamamı ✅ olmadan `verified` kaydı düşülmez;
kısmi geçiş yoktur, tek ❌ kapıyı kapalı tutar (`firmware-dogrulama.md` §3).
D9'un ❌ olması bu dosyadaki açık ⚠️ satırlarıyla (Q0 dahil) tutarlıdır —
**çelişki değil, aynı gerçekliğin iki kaydı.**

## §C CI Gate Kaydı (gerçek workflow dosyaları — okundu 2026-10-14)

| Gate | Dosya | Adım (okuma notu) | Sınıf | Durum |
|---|---|---|---|---|
| G1 | `.github/workflows/ci.yml` | `name: CI Pipeline` · `on: push · pull_request` | hard | dosyada mevcut |
| G2 | `.github/workflows/secret-scan.yml` | `gitleaks/gitleaks-action@v2` · push/pull_request tetikli | hard | dosyada mevcut |
| G3 | `ci.yml` | `composer stan` — PHPStan level=5 | hard | dosyada mevcut |
| G4 | `ci.yml` | php-test matrix — PHPUnit | hard | dosyada mevcut |
| G5 | `ci.yml` | js-test — Vitest | hard | dosyada mevcut |
| G6 | `ci.yml` | js-coverage — `continue-on-error: true` | **soft** | dosyada mevcut |
| G7 | `ci.yml` | js-e2e — `continue-on-error: true` | **soft** | dosyada mevcut |
| G8 | `ci.yml` | composer-audit | hard | dosyada mevcut |
| G9 | **dosya yok** | firmware/HIL işi tanımlanmamış | ⚠️ | **eksi̇k** — ad uydurulmadı |

**CI kaydı kuralı (V3):** Kanıt biçimi = **tam dosya okuması** (2026-10-14);
workflow dizininde **yalnız bu iki dosya** vardır. G9 satırı "yok" yazar —
varlığı iddia edilmez, yokluğu yazılır (strateji §3 · §18 SBOM notu ile
aynı dürüstlük: "üreten bir job yoktur"). Kapıların **çalışma geçmişi**
okunmamıştır (DV-T17).

## §D Ölçüm ve Eşik Kayıtları (DD-T)

| # | Parametre | Değer | Kaynak | Durum |
|---|---|---|---|---|
| DD-T1 | 1 tampon olay işleme bütçesi | 10,67 ms (512 / 48000) | 1 tampon — `device-control-dogrulama.md` DD-1 aktarımı | tanımlı — cihaz ölçümü yok |
| DD-T2 | Açılış süresi eşiği | `⚠️ UNKNOWN` | strateji §6 ölçüm 1 · §8 Q4 | ölçülmedi |
| DD-T3 | Watchdog periyodu | `⚠️ UNKNOWN` | `boot-surucu.md` §20 TI10 (periyot UNKNOWN) | ölçülmedi |
| DD-T4 | Coverage hedefi | **0 — atanmadı (uydurma yok)** | strateji §11 M7 · §17 | açık ⚠️ (Q0) |
| DD-T5 | HIL ölçüm eşiği | `⚠️ UNKNOWN` | `firmware-bagimliliklar.md` §20.5 adım 3 | tasarlandı, ölçülmedi |
| DD-T6 | Statik analiz eşiği | PHPStan level=5 (host yüzeyi) | strateji §6 ölçüm 6 | kapı tanımlı |
| DD-T7 | Kurtarma/ret eşikleri | %100 (TS5 · TS9 · TS11 · TS12 · TS13) · 0 (TS7 · TS8) | `firmware-bilesenler.md` §20 | tasarım — ölçülmedi |
| DD-T8 | UNKNOWN eşikler | `⚠️ UNKNOWN` (TS1 · TS3 · TS6 · TS10) | `firmware-bilesenler.md` §20 | uydurma rakam yazılmaz |
| DD-T9 | DSP callback bütçesi | 10,67 ms diliminin %50'si altında · underrun = 0 | strateji §19 (audio-control köprüsü) | ölçüm PLANNED |
| DD-T10 | Flaky kabulü | 2 tekrar → etiket; 3 gün → gerçek hata | strateji §16 | politika tanımlı — uygulanacak test yok |

## §E Web Araştırma Kaydı (İddia · URL · Tarih · Durum)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | GoogleTest, C++ birim testi için dokümante edilmiş framework'dür | https://google.github.io/googletest/ | 2026-10-14 | HTTP 200 |
| 2 | Ceedling, Unity tabanlı embedded test aracıdır (strateji araç eşlemesi adayı) | https://github.com/ThrowTheSwitch/Ceedling | 2026-10-14 | HTTP 200 |
| 3 | Unity, hafif C test framework'üdür (kardeş dosyalarda planlı araç) | https://github.com/ThrowTheSwitch/Unity | 2026-10-14 | HTTP 200 |
| 4 | Zephyr test altyapısı dokümantasyonu mevcuttur | https://docs.zephyrproject.org/latest/develop/test/index.html | 2026-10-14 | HTTP 200 |
| 5 | (Dışlanan) Zephyr eski test yolu | https://docs.zephyrproject.org/latest/test/index.html | 2026-10-14 | **HTTP 404 — KULLANILMADI** |

**URL kuralı (V4):** Yalnız 200 dönen ve iddia taşıyan 4 URL kayda girdi;
404 dönen yoldan **hiçbir iddia türetilmedi** (satır 5 dışlama kaydıdır).
Bu 4 URL K005 ağacında **tekildir** (aynı URL'nin başka bir K005 dosyasında
tekrarı yoktur). Araçların varlığı doğrulanmıştır; K005'te
**kullanıldıkları iddia edilmez** (strateji §2.1: firmware yüzeyinde
"tanımlı araç yok" — §A.2 notu korunur).

## §F Red Team Gözden Geçirmesi (bu üretim)

| # | İddia | Bulgu | Karar |
|---|---|---|---|
| 1 | "DV-T… senaryoları geçti" | hiçbiri çalıştırılmadı | reddedildi — Durum: Tasarım/PLANNED/yok |
| 2 | "CI yeşil" | CI geçmişi okunmadı | `⚠️` olarak taşındı (DV-T17) |
| 3 | "firmware testi doğrulandı" | depoda 0 firmware kodu | reddedildi |
| 4 | "coverage hedefi tutturuldu" | hedef tanımsız (Q0 · M7 = 0) | reddedildi — hedef yazılmadı |
| 5 | "G9 kapısı çalışıyor" | workflow dosyası yok | reddedildi — yokluk yazıldı |
| 6 | "Zephyr test sayfası 200" | 404 döndü | dışlandı — iddia üretilmedi (§E satır 5) |

## §G Kapanış Kontrolü (bu dosya)

| # | Kontrol | Beklenti | Durum |
|---|---|---|---|
| 1 | FM anahtar sayısı | 10 alan, doğru sıra | evet |
| 2 | Bölüm başı tablo | §A–§F her birinde ≥1 tablo | evet |
| 3 | "geçti" etiketi | 0 (yalnız Tasarım/PLANNED/yok/kısmi) | evet |
| 4 | Coverage açık sorusu | çözülmeden taşındı (DV-T5 · DD-T4 · Q0) | evet |
| 5 | G9 | "yok/eksi̇k" — ad uydurulmadı | evet |
| 6 | URL kaydı | 4×200 + 1×404 dışlama — 404'ten iddia yok | evet |
| 7 | Dosya/satır künyesi | 0 (firmware kodu yok) | evet |
| 8 | Çalıştırılan kapı | 0 | evet |
| 9 | Bölüm tekrarı / dolgu | 0 | evet |
| 10 | Son satır | Authority birebir tek satır | evet |


---

## §H DV-T Senaryo Ayrıntıları (DV-T1…DV-T10 · PLANNED — çalıştırılmadı)

### §H.1 DV-T1 — Kapı denetleyici (t2) karar bütünlüğü

| Alan | Değer |
|---|---|
| Amaç | Kapı değerlendirmesinin kriter-okuma hatasında PASS üretemediğini göstermek |
| Uyarıcı | kapı kriteri dosyası okunamaz / eksik satır |
| Beklenen | sonuç BELIRSIZ (asla PASS) · rapora BELIRSIZ satırı düşer |
| Kanıt | rapor satırı + kriter okuma logu (dosya+satır) |
| Durum | PLANNED — t2 bileşeni depoda YOK (bileşen şartnamedir) |

Adımlar: (1) kriter kaynağını gizle · (2) kapı denetimini çalıştır · (3) BELIRSIZ
sonucunu doğrula · (4) PASS üretilemediğini doğrula · (5) sonuç satırını deftere yaz.

### §H.2 DV-T2 — Kanıtsız sonuç (t3 bağımlılığı)

1. t3 kanıt yazım yolunu engelle (test düzeneği).
2. Senaryo çalıştır → sonuç üretilir.
3. Beklenen: sonuç **geçersiz sayılır** (testing-kapsam §G.2 t3); kapıya **girmez**.
4. Kanıt: BLOKE/INVALID satırı; kapı raporunda senaryonun yokluğu.

### §H.3 DV-T3 — Flaky işareti doğruluğu (t4)

- Uyarıcı: aynı senaryo 3 tekrar; sonuçlar PASS/FAIL/PASS.
- Beklenen: flaky işareti kapanır; kapı sonucu "geçti (flaky)" **ayrı** işaretlenir
  (normal PASS ile karıştırma yasak — testing-kapsam §O.1).
- Ölçüt: işaret + 3 tekrar satırı; tekrar sayısı sabitlenmemiştir (uydurma eşik yok).

### §H.4 DV-T4 — Coverage yorumu (t5 · eşik YOK)

1. Coverage raporu ver (boş / dolu iki varyant).
2. Beklenen: t5 **yorum üretir, eşik koymaz** (Q0 açık); boş raporda yorum = "rapor yok".
3. Kanıt: yorum satırı; sayısal hedef **olmaması** doğrulanır (uydurma hedef yasak).

### §H.5 DV-T5 — BLOKE ↔ FAIL ayrımı (kapı raporu)

- Uyarıcı: G9 senaryosu (ekipman/workflow yok).
- Beklenen: rapor **BLOKE** satırı; FAIL'a dönüşmez · kapının genel durumu BELIRSIZ.
- Kanıt: rapor satırı + G9 workflow yokluğu kaydı (firmware-test-stratejisi §3).

### §H.6 DV-T6 — INVALID ayrımı (fixture bozukluğu)

- Uyarıcı: fixture özet değeri beklemeyle uyuşmaz (fixture bozuk).
- Beklenen: senaryo INVALID (FAIL değil) · tekrar çalıştırma istenir · kapı etkilenmez.
- Kanıt: INVALID satırı + fixture envanteri (security-dogrulama §T.1 yöntemiyle).

### §H.7 DV-T7 — CI kapısı tekrar çalıştırma (G-kapı bağımsızlığı)

1. Aynı commit'te CI'i iki kez çalıştır (gerçek workflow: ci.yml — 2026-10-14 okuma).
2. Beklenen: kapı sonuçları **deterministik** (aynı commit → aynı kapı).
3. Bu depoda firmware kapıları **yok** → senaryo yalnız genel CI determinizmi için
   anlamlıdır; firmware varyantı BLOKE (kod yok).
4. Kanıt: iki çalıştırma logu + commit kimliği.

### §H.8 DV-T8 — Kapı kritik yolu bütünlüğü (t1→t3→t2)

- Uyarıcı: t1 sonucunu elle değiştirme denemesi (sahte PASS ekleme).
- Beklenen: kanıt satırı eşleşmediği için t2 **reddeder** (kanıtsız PASS yasak).
- Durum: tasarım denetimi — t2 bileşeni yok (PLANNED).

### §H.9 DV-T9 — Senaryo lifecycle ihlali (TASLAK kapıya girerse)

- Uyarıcı: TASLAK durumundaki senaryo rapora eklenir.
- Beklenen: t2 TASLAK'ı **saymaz** (testing-kapsam §N.3); raporda uyarı satırı.
- Kanıt: uyarı satırı + senaryo durumu.

### §H.10 DV-T10 — HIL senaryosu (L5 · F14 · BLOKE bekleyen)

1. Ekipman bağlanır (şartname testing-kapsam §I.2 — ekipman adı uydurulmaz).
2. Beklenen: F14 senaryosu çalışır · sonuç fiziksel ölçüm kanıtı taşır.
3. Şu anki durum: **BLOKE** — G9 workflow tanımsız; senaryo yalnızca tanımlıdır.
4. Kanıt: ekipman kaydı + ölçüm satırı (alanlar §K.1).

### §H.11 Senaryo → bileşen → katman matrisi

| Senaryo | t2 | t3 | t4 | t5 | F14 | Katman |
|---|---|---|---|---|---|---|
| DV-T1 | X | | | | | L2 |
| DV-T2 | | X | | | | L2 |
| DV-T3 | | | X | | | L3 |
| DV-T4 | | | | X | | L1+ |
| DV-T5 | X | | | | X | L5 (bloke) |
| DV-T6 | | X | | | | L2 |
| DV-T7 | X | | | | | L3 |
| DV-T8 | X | X | | | | L2 |
| DV-T9 | X | | | | | L2 |
| DV-T10 | | | | | X | L5 (bloke) |

---

## §I Ortam ve Düzeneği (EX1–EX4 · PLANNED)

| Ortam | İçerik | Senaryolar | Durum |
|---|---|---|---|
| EX1 sahte (mock) | t2/t3/t4/t5 sahte bileşenleri | DV-T1…DV-T4 · DV-T8/T9 | PLANNED — bileşenler yok |
| EX2 CI | gerçek workflow (ci.yml) | DV-T7 (genel determinizm) | Kısmi — firmware kapıları yok |
| EX3 firmware kütüphanesi | gerçek firmware kodu + test runner | DV-T1…DV-T6 firmware varyantı | **BLOKE** — kod yok (cpp/h=0) |
| EX4 HIL | F14 + ekipman | DV-T5 · DV-T10 | **BLOKE** — G9 yok |

1. Öncelik: EX1 → EX2 → EX3 → EX4; EX3/EX4 blokajları dışarıdan çözülür (kod + DevOps).
2. EX3süz kapı sonuçları yalnız **altyapı** düzeyinde geçerlidir; firmware kapısı
   kapanmaz (gizleme yasak).
3. Her ortam için kayıt satırı §K.1 şemasında; alanlar dışında değer uydurulmaz.

---

## §J Ölçüm ve Sonuç Şeması (t1 çıktısı — boş kalıp)

### §J.1 Sonuç satırı alanları

| Alan | Açıklama |
|---|---|
| ts | zaman damgası (çalıştıran doldurur) |
| scen | DV-T kodu |
| env | EX kodu |
| verdict | PASS / FAIL / BLOKE / INVALID / BELIRSIZ |
| evidence | dosya + satır referansı |
| actor | çalıştırıcı (agent/kişi) |
| repeat | tekrar indeksi (flaky için — varsa) |

### §J.2 Ölçüm kuralları

1. Sayısal eşik **uydurulmaz** (Q0): eşik yalnız ölçüm sonrası ve ADR ile konur.
2. "Geçti" yalnız çalıştırılmış + kanıtlı sonuç için kullanılır.
3. Sonuç yoksa satır "ÇALIŞTIRILMADI" (dürüst boşluk; dolgu yasak).

---

## §K Enjeksiyon Senaryoları (altyapı sağlığı)

| # | Enjeksiyon | Hedef | Kabul |
|---|---|---|---|
| F1 | kriter dosyasını gizle | t2 → BELIRSIZ | PASS üretilemez |
| F2 | kanıt yolunu kapat | t3 → geçersiz | kapıya girmez |
| F3 | tekrar sonuçlarını karıştır | t4 → flaky işareti | normal PASS ile karıştırılmaz |
| F4 | raporu sil | t5 → "rapor yok" | yorum uydurulmaz |
| F5 | workflow'u kaldır | G9 → BLOKE | FAIL'a dönüşmez |
| F6 | fixture'ı boz | INVALID | FAIL'a dönüşmez |

1. Enjeksiyonlar EX1'de anlamlıdır (altyapı testi).
2. Her enjeksiyon bir DV-T ile çiftlenir (F1↔DV-T1, F2↔DV-T2, …).
3. Yeni enjeksiyon fikri → anayasa onayı; burada yalnız talep kaydedilir.

---

## §L Kapı Yetkisi ve Karar Tablosu

| Kapı | Sahip | Minimum kanıt | Durum |
|---|---|---|---|
| G-araştırma/lint | CI | ci.yml sonucu | Çalıştırılıyor (bu depoda) |
| G-bütünleşik (firmware) | QA | EX3 sonuçları | BLOKE (kod yok) |
| G-HIL (G9) | DevOps + QA | EX4 sonuçları | BLOKE (workflow yok) |
| G-coverage (G6) | QA | t5 yorumu + rapor | BELIRSIZ (eşik yok — Q0) |
| Kapı geneli (firmware) | Vault Steward | hepsi | **BELIRSIZ** (blokajlar) |

1. Agent "geçti" **bilemez** — yalnız kanıt üretir; karar sahibi tabloda.
2. Blokajlı kapı genel sonucu gizlenmez: BELIRSIZ raporda açık satır.

---

## §M Öncelik ve Sıra Planı

1. DV-T1 → DV-T2 → DV-T8 (kapı sağlığı — EX1).
2. DV-T3 → DV-T6 → DV-T9 (durum ayrım flaky/INVALID/TASLAK).
3. DV-T4 (coverage dürüstlüğü) → DV-T7 (CI determinizmi, EX2).
4. DV-T5 → DV-T10 (EX4 — blokaj çözülünce).
5. Genel firmware kapısı: EX3/EX4 açılmadan **değerlendirilmez** (belirsizlik gizlenmez).

---

## §N Red Team Notları (bu ek için)

1. **İtiraz:** "BLOKE'ı FAIL sayalım, temizlensin." **Karar:** hayır — nedeni gizlemek
   kapıya yalan yazmaktır (testing-kapsam §N.2).
2. **İtiraz:** "Coverage %X koyalım." **Karar:** hayır — Q0 açık; ölçülmüş eşik uydurmadır.
3. **İtiraz:** "Mock EX3'ün yerine geçer." **Karar:** hayır — EX3 firmware davranışını
   ölçer; mock yalnız altyapı (EX1).
4. **İtiraz:** "Flaky kapıyı geçebilir." **Karar:** yalnız "geçti (flaky)" ayrı
   işaretiyle; normal PASS yasak (DV-T3).
5. **İtiraz:** "Kanıt yazımı atlanabilir." **Karar:** hayır — t3 hatasında sonuç
   geçersizdir (DV-T2).

---

## §O Çapraz Bağlantılar (tek-yönlü — SSOT)

1. Kapı kriterleri: firmware-test-stratejisi.md §3/§17/§18 (bu dosya kopyalamaz).
2. Bileşen şartnamesi: testing-kapsam.md §B/§G.
3. Anayasa kapıları: testing-anayasa.md §3/§4.
4. Senaryo iskeletleri: testing-ornekler.md §A/§B.
5. Arayüz sözleşmeleri: testing-surucu.md §1–§6.
6. Firmware HIL planı: firmware-bagimliliklar.md §20.5 (5 adım — sahiplik DevOps).

---

## §P Kapanış Kontrolü (bu ek)

| Kontrol | Sonuç |
|---|---|
| Mevcut §A–§G silindi mi? | HAYIR (append-only) |
| Frontmatter | korundu (10 alan + group: kanit) |
| Dolgu/TBD | 0 |
| Çalıştırılmış iddiası | 0 (tümü PLANNED/BLOKE) |
| Uydurma eşik/ad/URL | 0 |
| Son satır Authority | birebir korunur |


---

## §Q Görev, Veri ve Kullanım Şemaları (tamamlama)

### §Q.1 Görev sırası ve bağımlılık (PLANNED)

| Adım | İş | Bağımlılık | Sahip |
|---|---|---|---|
| Q1 | t1–t5 bileşen şartnamesi dondurma | testing-kapsam §G onayı | QA |
| Q2 | EX1 sahte ortam kurulumu | Q1 | test-automator |
| Q3 | DV-T1…DV-T4, DV-T8/T9 çalıştırma | Q2 | QA |
| Q4 | EX2 CI determinizmi (DV-T7) | mevcut ci.yml | DevOps + QA |
| Q5 | Firmware kodu + EX3 | kod üretimi (dışarıdan) | kod sahibi |
| Q6 | G9 workflow + EX4 (DV-T5/T10) | DevOps onayı | DevOps |
| Q7 | Kapı değerlendirmesi + rapor | Q3–Q6 | Vault Steward |

### §Q.2 Fixture şartnamesi (uygulama yok — şartname)

| Kod | Fixture | Üretim | Not |
|---|---|---|---|
| FX-T1 | bozuk kapı kriteri kaynağı | test düzeneği (EX1) | DV-T1 |
| FX-T2 | kilitli kanıt yolu | test düzeneği | DV-T2 |
| FX-T3 | değişken tekrar sonuçları | test verisi | DV-T3 |
| FX-T4 | boş/dolu coverage raporu | rapor üretici | DV-T4 |
| FX-T5 | bozuk fixture özeti | mutasyon | DV-T6 |

1. Fixture envanteri her çalıştırmadan önce sayılır; eksik = senaryo BLOKE.
2. Değerler (özet/karma) uydurulmaz — çalıştırmada okunup §J.1'e yazılır.

### §Q.3 Kullanım kılavuzu (kim ne okur)

1. **QA**: §H (senaryolar) → §M (sıra) → §J (kayıt şeması).
2. **DevOps**: §I (EX2/EX4) + §Q.1 Q4/Q6.
3. **Vault Steward**: §L (kapı yetkisi) + §P (kapanış sayımı).
4. **Geliştirici**: §K (enjeksiyon) — altyapı sağlığı testleri.

### §Q.4 Sözlük (bu ek)

| Terim | Anlamı |
|---|---|
| EX1–EX4 | testing ortam kademeleri (§I) |
| FX-T* | testing fixture kodları (§Q.2) |
| F1–F6 | altyapı enjeksiyonları (§K) |
| G9 | firmware HIL workflow — TANIMLI DEĞİL |
| Q0 | coverage eşiği açık sorusu |
| BELIRSIZ/BLOKE/INVALID | kapı durumları — FAIL değildir (ayrım §H.5/H.6) |

### §Q.5 Son cümle

Bu ek (§H–§Q) testing-dogrulama.md dosyasını 10 senaryo kartı, 4 ortam kademesi,
6 enjeksiyon, kapı yetki tablosu, görev/fixture/kullanım şemalarıyla derinleştirmiştir;
**hiçbir senaryo çalıştırılmamıştır** (PLANNED/BLOKE) ve mevcut §A–§G append-only
korunmuştur. Sonraki üretim yalnız §J.1 şemasına **gerçek sonuç satırı** eklenerek yapılır.


---

## §R Çalıştırma Günlüğü Şartnamesi (append-only günlük)

### §R.1 Günlük satırı ve oturum kuralı

| Alan | Açıklama |
|---|---|
| session_id | oturum kimliği (üreten atar) |
| scen | DV-T / S-* kodu |
| steps_done | tamamlanan adım sırası (adım no) |
| verdict | ara/kapanış durumu |
| evidence_refs | birden çok kanıt satırı (dosya+satır) |

1. Oturum yarım kalırsa satır "YARIM" olarak kalır — sonradan "tamamlandı"
   yapılmaz (yeniden çalıştırma yeni oturumdur).
2. Günlük de append-only'dir; düzeltme yalnız yeni satırla.

### §R.2 Oturum → kapı bağı

- Kapı değerlendirmesi yalnız **KAPANDI** oturumları kullanır.
- YARIM/BLOKE oturumlar raporda ayrı satır (kapı sonucunu etkilemez ama gizlenmez).

### §R.3 Sık karşılaşılan sorular (bu ek)

| Soru | Yanıt |
|---|---|
| BLOKE kapıyı düşürür mü? | Hayır — ayrı satır; genel kapı BELIRSIZ kalır (§H.5) |
| Mock ile L5 sayılır mı? | Hayır — L5 yalnız EX4 (§I) |
| Eşik şimdi konabilir mi? | Hayır — Q0 açık; ölçüm sonrası ADR (§J.2) |
| firmware kodu gelmeden kapı kapanır mı? | Hayır — EX3 blokajı (§I) |
| Kanıt defteri silinir mi? | Hayır — append-only (§O.1 testing-kapsam) |

### §R.4 Kapanış (bu ek)

Bu ek (§Q–§R) günlük/oturum şartnamesi, görev ve fixture şemaları, kullanım kılavuzu
ve sözlükle tamamlanmıştır; mevcut §A–§P append-only korunmuştur. Toplam ek
senaryo sayısı: 10 DV-T kartı (§H) + enjeksiyon F1–F6 (§K); hiçbiri çalıştırılmamıştır.


---

## §S Devralınan Açık Soruların Doğrulama Etkisi (son sayım)

| Açık soru | Doğrulamaya etkisi | Etkilenen senaryo |
|---|---|---|
| Q0 coverage eşiği | DV-T4 yalnız dürüst yorum üretir | DV-T4 |
| G9 workflow tanımsızlığı | DV-T5 · DV-T10 BLOKE | DV-T5 · DV-T10 |
| firmware kodu yok (cpp/h=0) | EX3 kapalı — tüm firmware varyantları BLOKE | §H tamamı |
| Q4 imza sahipliği (security) | S-SEC-01 kapısı kısmi kalır | §Q.1 Q5 |
| HIL ekipman listesi yok | ekipman adı uydurulmaz; EX4 beklemede | DV-T10 |

### §S.1 Sonraki üretim koşulu (bu ek)

1. Açık soru kapanırsa bu dosyaya **§T** olarak append edilir (mevcut tablo silinmez).
2. Gerçek sonuç satırları §J.1 şemasıyla gelir; uydurma sonuç yasak.
3. Kapı geneli BELIRSIZ'den çıkışı yalnız EX3 + EX4 açılışı verir (§L).


---

## §T Ek Kapanış Tablosu (bu ek için son sayım)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §H–§T |
| Silinen mevcut içerik | 0 |
| Dolgu/TBD | 0 |
| Çalıştırılmış iddiası | 0 |
| Uydurma eşik/ad/URL | 0 |
| Son satır Authority | birebir korunur |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode