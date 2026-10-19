---
node_id: K000-isletim-sistemi/os-kapsam.md
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on:
  - K001-donanım
  - K003-audio
  - K005-firmware
evidence:
  - "os-master.md §A–§D (okundu 2026-10-18) — ELI15, kimlik, 8 kavram, düğüm listesi"
  - "index.md §1–§4 (okundu 2026-10-18) — ne işe yarar · sınırlar · 13 düğüm envanteri (353 md / 152.346 satır, ölçüm 2026-10-15)"
  - ".ai/architecture/index.md §8.1 grup kuralı — okundu 2026-10-18"
  - "K000-isletim-sistemi/ disk ölçümü 2026-10-18 — kök 4 MD + 13 kademe-2 dizin"
validation:
  - "2026-10-18 — §2/§3 satırları os-master §C ve index §1 ile çapraz okundu"
  - "2026-10-18 — depends_on yalnız diskte var olan K adları (K001/K003/K005)"
  - "2026-10-18 — her satırda Etiket sütunu (§10 sözlüğü)"
open_questions:
  - "index.md §2 'iş mantığı → K001' kaydı K001-donanım ile çelişir (K011-application olmalı) (⚠️ VERIFICATION REQUIRED)"
  - "os-master FM 'depends_on: [K002-altyapi]' phatom referansıdır — ölü K adı (⚠️, yenisi yazılmaz)"
  - "Q4 hedef platform seti tek noktada tanımlı değil (Windows/Linux/macOS) (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# os-kapsam — K000 Kapsam Sınırı

> Dayanak: `os-master.md` §A–§D · `index.md` §1–§4. Bu dosya K000'in
> **içinde / dışında / komşu** ayrımının kök seviyedeki tek geniş dökümüdür;
> çelişkide bu dosya kazanır.

## §1 ELI15

> **K000, PHP kodunun "hayır diyemeyeceği üst otoritedir — dosyayı açan, sesi
> çalan, süreci başlatan işletim sisteminin kendisi.**

Bir restoran düşün: PHP uygulaması aşçıdır, işletim sistemi ise mutfağın
kendisi (ocak, buzdolabı, bulaşık makinesi). Aşçı mutfağı **kurmaz**, onu
**kullanır**. K000'in işi bu kullanım sözleşmesini yazmaktır: hangi iş için
hangi kapıyı (syscall) çalıyoruz, kapı kime ait, komşu katman (firmware,
donanım, DSP) ile sınır nerede. Üç soru kapsamı belirler: (1) bu iş bir işletim
sistemi hizmeti mi? (2) PHP'nin doğrudan erişemediği bir yüzey mi? (3) CoreMusic
bunun kararını mı verir, yoksa OS'e mi bırakır? 1+2 evet, 3 "OS'e bırakır" ise
bu dosyanın §2'sindedir.

## §2 Kapsam İÇİNDE (K000'e ait)

| # | Alan | Ne kapsar | Sahip kademe-2 | Etiket |
|---|---|---|---|---|
| I1 | Syscall sözleşmesi | kullanıcı↔kernel geçişi · libc sarmalayıcı · hata kodu | isletim-sistemi-api · kernel | [KANIT] |
| I2 | Süreç & zaman paylaşımı | fork/exec · scheduler · öncelik · zamanlayıcı | isletim-sistemi-kernel · plan | [KANIT] |
| I3 | Bellek yönetimi | virtual memory · mmap · temp dizin · adres alanı | isletim-sistemi-core | [KANIT] |
| I4 | Dosya sistemi & VFS | yol çözümleme · izin · symlink denetimi | isletim-sistemi-core · security | [KANIT] |
| I5 | Ses yığınına erişim | WASAPI · ALSA · Core Audio · AAudio (yalnız erişim katmanı) | isletim-sistemi-audio | [KANIT] |
| I6 | Platform bağımsızlık | PATH ayracı · çekirdek/repl · platform tespiti | isletim-sistemi-cross · turu | [KANIT] |
| I7 | Güvenlik modeli (OS) | izin · capability · seccomp · sandbox · kural uygulayıcı | isletim-sistemi-security | [KANIT] |
| I8 | Gözlemlenebilirlik (OS) | ftrace · ETW · perf · log yönlendirme | isletim-sistemi-observability | [KANIT] |
| I9 | OS test altyapısı | kselftest · KUnit · süreç izolasyonu · CI platformu | isletim-sistemi-testing | [KANIT] |
| I10 | Runtime/SAPI yüzeyi | PHP SAPI · Apache/nginx · .htaccess · web.config | isletim-sistemi-runtime | [KANIT] |
| I11 | Donanım tespiti | PnP · enumerasyon · sürücü bağlama (kullanıcı-kernel) | os-donanim-tespiti | [KANIT] |
| I12 | OS↔elektronik kesişimi | donanım algılama yazılımı yüzeyi (Q1 KAPANDI 2026-10-14) | electroncis (alt dal) | [TASARIM] |

**Toplam:** 12 İÇİNDE satırı · 13 kademe-2 düğüm (index §4 sayımı).

## §3 Kapsam DIŞI (K000'e ait DEĞİL)

| # | Alan | Neden dışarı | Giden düğüm | Etiket |
|---|---|---|---|---|
| O1 | PHP iş mantığı, route, controller | Uygulama katmanı OS'i **kullanır**, OS değildir | K011-application ⚠️ | [TASARIM] |
| O2 | Sunucu/altyapı kurulumu | "OS nasıl kurulur" ≠ "OS nasıl kullanılır" | K002-altyapi (ÖLÜ ref ⚠️) | [TASARIM] |
| O3 | Devre · PCB · güç kaynağı | Elektronik tasarımı ayrı disiplin | K002-electroncis | [TASARIM] |
| O4 | Fiziksel parça envanteri | Var olan ≠ hizmet veren | K001-donanım | [TASARIM] |
| O5 | Ses akışı taşıma/routing | Akış K003'ün; K000 yalnız **cihazı sunar** | K003-audio | [TASARIM] |
| O6 | EQ/reverb/kompresör algoritması | Algoritma OS katmanının konusu değil | K004-dsp | [TASARIM] |
| O7 | Cihaz içi firmware/boot | Gömülü hedef ayrı yaşam döngüsü | K005-firmware | [TASARIM] |
| O8 | Veritabanı şeması, BCNF, migration | Kalıcılık katmanı | K007-data | [TASARIM] |

## §4 Komşu sınır tablosu (ince çizgi nerede)

| Sınır | K000 tarafı | Komşu tarafı | Geçiş izni |
|---|---|---|---|
| K000 ↔ K001 | sürücü/OS soyutlama API'si | fiziksel parça · register · envanter | yalnız cihaz **talebi**, register DEĞİL |
| K000 ↔ K002-electroncis | donanım algılama/yükleme yazılımı | devre tasarımı · BOM · şema | yalnız "cihaz var mı" yanıtı |
| K000 ↔ K003 | ses cihazı handle/akış uçları | akış · miks · routing | cihaz erişimi, ses **verisi** DEĞİL |
| K000 ↔ K004 | CPU · thread · bellek · zaman kaynağı | EQ/reverb/DSP zinciri | kaynak, algoritma DEĞİL |
| K000 ↔ K005 | hedef platform kısıtı | bare-metal firmware | yalnız hedef seçiminde kesişim |
| K000 ↔ K009 | izin/capability modeli | uygulama seviyesi auth/CSRF | OS yetkisi ≠ uygulama yetkisi |

## §5 Yaşam döngüsüne göre kapsam

| Evre | Başlangıç | Bitiş | Sahip düğüm |
|---|---|---|---|
| Kurulum | OS yüklenmesi (KAPSI DIŞI → O2) | ilk boot | — (dışarıda) |
| Açılış | boot → servisler | PHP SAPI hazır | isletim-sistemi-runtime |
| Çalışma | istek kabulü | süreç kapanana dek | kernel · api · plan |
| Arıza | hata/timeout | log'a yazıldı | observability · security |
| Kapanma | shutdown tetiği | güç kesilmesi | runtime (O2 sınırında) |

## §6 Kapsam kararları

| # | Karar | Gerekçe |
|---|---|---|
| K-1 | K000 **kod üretmez**, OS sözleşmesini sabitler | index §1 "kod üretmez" |
| K-2 | Ses erişimi kapsamdadır, ses **verisi** değildir | os-master §C.8 + O5 |
| K-3 | Donanım tespiti kullanıcı-kernel sınırı olduğu için içeridedir | os-donanim-tespiti (2026-10-13) |
| K-4 | `electroncis` alt dalı Q1 ile kapatıldı (OS↔donanım yazılım yüzeyi) | os-master FM Q1 "KAPANDI 2026-10-14" |
| K-5 | `isletim-sistemi-plan` kapsamı Q3 ile BELİRSİZ | index §1 "[BİLİNMEYEN — Q3]" |
| K-6 | Ölüm/ölü referanslar silinmez, `⚠️` ile taşınır | brief §yasaklar |

## §7 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | §2 satırları os-master §C kavramlarıyla tutarlı | çapraz okuma | ✅ 2026-10-18 |
| C2 | §3 8 satırı komşu K'ları kapsıyor | sayım | ✅ 2026-10-18 |
| C3 | Her satırda etiket var | manuel | ✅ 2026-10-18 |
| C4 | depends_on ölü K adı içermiyor | brief §3 kara liste | ✅ 2026-10-18 |
| C5 | `isletim-sistemi-plan` kapsamı netleşti | — | ❌ açık (Q3) |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | `index.md §2` "iş mantığı → K001" kaydı doğru mu (K011 mi K001 mi)? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | `K002-altyapi` ölü referansı hangi K ile değiştirilecek (silen yok)? | `⚠️ VERIFICATION REQUIRED` |
| Q3 | `isletim-sistemi-plan` neyi kapsar (scheduling mi doküman mı)? | `⚠️ VERIFICATION REQUIRED` |
| Q4 | Hedef platform seti tek noktada tanımlı mı (Windows/Linux/macOS)? | `⚠️ VERIFICATION REQUIRED` |

## §9 13 kademe-2 düğümün kapsam dağılımı

| # | Düğüm | Kapsam içindeki payı | Kapsam dışı en yakın komşu | Etiket |
|---|---|---|---|---|
| 1 | electroncis | OS↔donanım yazılım kesitinin tamamı | devre tasarımı → K002 | Q1 KAPANDI |
| 2 | isletim-sistemi-api | kullanıcı-kernel sınırı, libc | uygulama API'si → K011 | [KANIT] |
| 3 | isletim-sistemi-audio | WASAPI/ALSA/CoreAudio erişimi | akış → K003 | [KANIT] |
| 4 | isletim-sistemi-core | bellek · süreç · dosya kavramları | DB → K007 | [KANIT] |
| 5 | isletim-sistemi-cross | platform bağımsızlık kuralları | — | [KANIT] |
| 6 | isletim-sistemi-kernel | süreç/syscall/iskelet | ring-0 sürücü kodu → K005 sınırı | [KANIT] |
| 7 | isletim-sistemi-observability | ftrace/ETW/perf, log hattı | uygulama logu → K016 | [KANIT] |
| 8 | isletim-sistemi-plan | scheduling/timer (kapsam BELİRSİZ) | — | Q3 ⚠️ |
| 9 | isletim-sistemi-runtime | SAPI + web sunucusu | sunucu kurulumu → O2 | [KANIT] |
| 10 | isletim-sistemi-security | izin/capability/sandbox | uygulama auth → K009 | [KANIT] |
| 11 | isletim-sistemi-testing | OS test altyapısı | uygulama testi → K18 | [KANIT] |
| 12 | isletim-sistemi-turu | 8 platform karşılaştırması | — | [KANIT] |
| 13 | os-donanim-tespiti | PnP/enumerasyon | fiziksel envanter → K001 | [KANIT] |

## §10 Etiket sözlüğü

| Etiket | Anlamı | Ne zaman kalkar |
|---|---|---|
| `[KANIT]` | disk dosyası veya 2026-10-18'de doğrulanmış resmi URL var | kalıcı |
| `[TASARIM]` | CoreMusic tercihi, OS zorunluluğu değil | kod yazılınca |
| `[BİLİNMEYEN]` | veri yok, uydurulmadı (Q1–Q4) | kanıt gelince |
| `⚠️ VERIFICATION REQUIRED` | üst karar/kanıt bekleyen satır | kanıt eklendiğinde |
| `ÖLÜ ref` | diskte karşılığı olmayan K adı — silinmez | üst kararla |

## §11 Kapsam değişiklik protokolü

| Adım | Aksiyon | Zorunlu kayıt |
|---|---|---|
| 1 | §1 üç soru testi çalıştırılır | cevap satırı |
| 2 | Hangi §9 düğümüne girdiği belirlenir | düğüm adı |
| 3 | `os-master.md` §D ile çapraz kontrol | çapraz satır |
| 4 | Bilinmeyen → §8'e `⚠️` satırı (silme yok) | soru no |
| 5 | Komşu etkisi → `os-bagimliliklar.md` | bağımlılık satırı |

## §12 Kapsam test senaryoları (içinde mi dışında mı?)

| # | Senaryo | Cevap | Dayanak |
|---|---|---|---|
| S1 | `file_get_contents()` için realpath denetimi yazmak | İÇİNDE | §2 I4 · os-master §C.3 |
| S2 | Şarkıyı EQ ile değiştirmek | DIŞARIDA | §3 O6 |
| S3 | WASAPI cihaz listesini çekmek | İÇİNDE | §2 I5 |
| S4 | DAC register'ını yazmak | DIŞARIDA | §3 O7 (→K005) |
| S5 | `proc_open` ile alt süreç başlatmak | İÇİNDE | §2 I2 |
| S6 | Web auth middleware'i | DIŞARIDA | §3 O1/K009 |
| S7 | ftrace ile kernel izleme | İÇİNDE | §2 I8 |
| S8 | CI'da `ubuntu-latest` platform seçimi | İÇİNDE | §2 I9 · ci.yml:1 |
| S9 | PCB güç kaynağı tasarımı | DIŞARIDA | §3 O3 |
| S10 | PHP route yönlendirmesi | DIŞARIDA | §3 O1 |

## §13 Özet matris

| Öğe | Sayı | Dayanak |
|---|---|---|
| İÇİNDE satırı | 12 (I1–I12) | §2 |
| DIŞARIDA satırı | 8 (O1–O8) | §3 |
| Komşu sınırı | 6 | §4 |
| Kademe-2 düğüm | 13 | index §4 (2026-10-15 sayımı) |
| Açık soru | 4 (Q1–Q4) | §8 |
| Test senaryosu | 10 | §12 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18
