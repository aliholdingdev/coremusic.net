---
title: "cpu-kapsam — Genel Amaçlı İşlemci Sınıfı Kapsamı"
type: architecture
node_id: K001-donanım/donanaım-type/cpu/cpu-kapsam
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type/cpu
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Vault Steward 2026-10-13 dağıtık onayı (3. dalga) — klasör açımı YAPILDI; anayasa revizyonu YOK"
  - "donanım-anayasa.md §10 Açık Sorular/3 (satır 195) · §12/C10 (satır 250) · §12.1 (satır 258 — yapısal sınıf ADR-097 onayı ister)"
  - "donanaım-type-kapsam.md §2/A6 (satır 46) · §7/3-T1 (satır 135) — mevcut metinde kapsam DIŞINDA (revizyon: APPROVAL REQUIRED)"
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §4 satır 107 (cpu = 8. bağlam) — status: draft (kullanıcı onayı bekliyor)"
  - "shared/composer.json: php >=8.4 · ext-apcu · ext-json · ext-mbstring · ext-pdo (okundu 2026-10-13)"
  - "Repo ölçümü 2026-10-13: .php=319 · .js=98 · .css=280 · .c/.cpp/.h/.cs=0; cpuid·__rdtsc·AVX·NEON·SSE2 = 0 eşleşme"
  - "https://riscv.org/ (HTTP 200, erişim 2026-10-13, HEAD) · https://www.arm.com/architecture (HTTP 200, erişim 2026-10-13, HEAD) · https://www.php.net/ (HTTP 200, erişim 2026-10-13, HEAD)"
validation: "proposed — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel envanter ve ölçüm YOK (tüm S1 kapıları açık) · anayasa/kapsam metni revize EDİLMEDİ"
open_questions:
  - "Q-CK1: Çalışma makinesinin CPU envanteri (model · çekirdek · kuşak · önbellek · bellek kanalı) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-CK2: anayasa §10/3 + §12/C10 ve kapsam §2/A6 · §7/3-T1 metin revizyonu onayı alınmadı — APPROVAL REQUIRED"
  - "Q-CK3: PHP 8.4 JIT'in (opcache.jit) bu depoda etkin olup olmadığı — yapılandırma dosyası yok → UNKNOWN"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# cpu-kapsam — Genel Amaçlı İşlemci Kapsamı (K001 Donanım Türü Envanteri)

> **Yazım notu:** `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak
> (v2 §3 KURAL 1; `donanaım-type/index.md` yazım notu). Bu dosya `cpu/`
> alt-konunun **kapsam** belgesidir: genel amaçlı işlemcinin K001 tür
> envanterindeki yeri, ISA/çekirdek/önbellek/denetleyici sınıfları, PHP 8.4
> çalıştırma bağlamı ve komşu düğümlerle sınır.

## §0 SINIR ÇELİŞKİSİ KAYDI (zorunlu — gizleme YASAK)

Bu klasör, vault'un mevcut anayasası ve üst kapsam metni ile **çelişir**. Çelişki
bu dosyada kardeş `gpu/gpu-kapsam.md` girişinde de tablo halinde kayıtlıdır;
**gizlenmez, yumuşatılmaz**. Çözüm yolu Vault Steward 2026-10-13 açık onayı ile
klasör açımıdır; metin revizyonu = **APPROVAL REQUIRED (henüz yapılmadı)**.

| # | Çelişkili kaynak | Metindeki ifade (olduğu gibi) | Bu klasörle ilişkisi |
|---|---|---|---|
| 1 | `donanım-anayasa.md` §10 Açık Sorular/3 (satır 195) | "donanaım-type altı 3 bağlam mı, fazlası mı? — genişleme — ⚠️ VERIFICATION REQUIRED" | `cpu/` bu soruyu *yanıtlar* ama anayasa metninde **hâlâ 3 yazar** |
| 2 | `donanım-anayasa.md` §12/C10 (satır 250) | "3 kademe-3 alt bağlam (audio/hdd/ssd) — EVET — envanterle" | Değişiklik yapısal sınıf (§12.1, satır 258) → **ADR onayı ister** — ADR-102 hâlâ `draft` |
| 3 | `donanaım-type-kapsam.md` §2/A6 (satır 46) | "Ekran · kablo · güç kaynağı türleri — **DIŞINDA** — anayasa §10/5 yasağı (ayrı bağlam AÇILMAZ)" | `cpu/` bu satırın dayandığı "yalnız 3 bağlam" sınırını aşar |
| 4 | `donanaım-type-kapsam.md` §7/3-T1 (satır 135) | "Altına yalnız 3 bağlam açılır (`hdd` · `audio` · `ssd`)" | Aynı ihlal |
| 5 | Yeni ihtiyaç (görev tanımı) | prompt §5 A sınıfı — işlem/hesaplama türü (x86 · ARM · RISC-V · cache · ISA · sanallaştırma · PHP execution) | Bu sınıfa karşılık gelen türler mevcut 3 bağlamda **hiçbiriyle tanımlı değil** |
| 6 | Onay | Vault Steward 2026-10-13 klasör açımı (3. dalga görev emri) | ✅ **YAPILDI** — `cpu/` klasörü açıldı |
| 7 | ADR-102 (draft) §4/8 | "`cpu/` — İşlemci: CPU/SoC, XMOS XU316 (ADR-038), gömülü işlemci çekirdekleri" | cpu **bu ADR'de geçiyor**; ancak ADR `draft` → onay **henüz verilmedi** |
| 8 | Anayasa/kapsam metin revizyonu | — | ❌ **YAPILMADI — APPROVAL REQUIRED** (bu dosyanın yetkisi dışında) |

**Durum etiketi (bağlayıcı):** Bu dosya seti `PROPOSED — APPROVAL REQUIRED`
etiketini taşır. `donanaım-type-kapsam.md` §6'daki **Q-T2** ("hdd/ssd dışı türler
nereye gidiyor?") bu dosyalarla **yanıtlanmış sayılır, kapatılmaz**; kapanış yalnız
anayasa §10/3 · §12/C10 ve üst kapsam §2/A6 · §7/3-T1 metinlerinin revizyonuyla olur.

| # | Kapanış adımı | Sahip | Durum |
|---|---|---|---|
| 1 | `cpu/` klasör açımı | Vault Steward (2026-10-13 açık onay) | ✅ YAPILDI |
| 2 | `donanım-anayasa.md` §10/3 + §12/C10 satır revizyonu | Vault Steward + ADR onayı | ❌ YAPILMADI — APPROVAL REQUIRED |
| 3 | `donanaım-type-kapsam.md` §2/A6 · §7/3-T1 revizyonu | Vault Steward | ❌ YAPILMADI — APPROVAL REQUIRED |
| 4 | ADR-102'nin `accepted/`'e geçmesi | Vault Steward (kullanıcı onayı) | ❌ `draft` — bekliyor |
| 5 | `donanaım-type/index.md` children + §GRUPLAR | orkestratör (toplu) | ⏳ BEKLEMEDE — bu turda dokunulmadı |
| 6 | Q-T2 kapanışı | Vault Steward | ❌ açık |

## §1 ELI15

İşlemci (CPU), bir bilgisayarın **her işi sırayla (ya da çok çekirdekte
paralel) yapan** ana çipidir: PHP kodunu yorumlayıp derler, dosyaları okur,
ağ paketlerini taşır, ses miksajı gibi sayısal işleri hesaplar. Bir işlemciyi
tanımlamak için dört soru yeterlidir: (1) **hangi komut takımıyla** çalışır
(x86/x64 · ARM · RISC-V), (2) **kaç çekirdeği** vardır ve hangi mimari kuşaktır,
(3) **önbellek hiyerarşisi** nasıldır (L1/L2/L3), (4) **bellek denetleyicisi**
kaç kanallı çalışır. Bu dosya bu dört soruyu sınıflandırır, sonra iki özel
soruyu yanıtlar: CoreMusic'in PHP 8.4 katmanı **hangi CPU özelliklerine güvenir**
(§11) ve ses işlemede CPU'nun rolü **DSP çipiyle neden karıştırılmaz** (§12).

> ⚠️ VERIFICATION REQUIRED: Bu alt-konu v2'de 3 bağlam (hdd · audio · ssd)
> arasında ad olarak geçmez; tanımı mevcut anayasa ile **çelişir** (§0).
> Fiziksel CPU envanteri bilinmiyor (Q-CK1 → UNKNOWN). Bu dosyada geçen
> **hiçbir sayısal performans değeri ölçüm değildir**; ölçüm `cpu-dogrulama.md`
> kapılarında tanımlıdır ve **0'dır**. Uydurma rakam, marka/model yazılmaz.

## §2 Kapsam — İÇİNDE NE var

| # | Kapsam İÇİ | Durum |
|---|---|---|
| A1 | CPU sınıfının K001 tür envanterindeki yeri (tür listesi) | ✅ mimari tanım (§4) |
| A2 | Komut takımı (ISA) aileleri: x86/x64 · ARM · RISC-V | ✅ kavramsal karşılaştırma (§5) |
| A3 | Çekirdek sayısı · fiziksel/mantıksal ayrım · mimari kuşaklar | ✅ §6 |
| A4 | Önbellek hiyerarşisi (L1/L2/L3 · satır · tutarlılık) | ✅ §7 — sayısal boyutlar ⚠️ |
| A5 | Bellek denetleyicisi (kanal · DDR sınıfı · erişim yolu) | ✅ §8 — sayısal boyutlar ⚠️ |
| A6 | Komut seti genişletmeleri (SIMD: SSE/AVX · NEON · V) | ✅ §9 — repo ölçümü ile |
| A7 | Sanallaştırma genişletmeleri (VT-x / AMD-V · EPT/NPT kavramı) | ✅ §10 — marka adları görev kapsamı |
| A8 | PHP 8.4 execution bağlamı (CoreMusic'in CPU'ya bağımlılığı) | ✅ §11 — composer.json disk kanıtı |
| A9 | Ses işlemede CPU rolü ve DSP ≠ CPU ayrımı | ✅ §12 — sınır |
| A10 | 26-alan tür kayıtları (6 kayıt) | ✅ §13 |
| A11 | Etiket disiplini · kapıların **tanımı** · riskler · terimler | ✅ §14–§20 |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Donanım DSP çipleri ve sinyal işleme (EQ · reverb · filtre) | K004-dsp | görev sınırı: K001 cpu = genel amaçlı işlem |
| B2 | Devre/PCB tasarımı, BOM, güç kaynağı hesabı | K002-electroncis | `donanım-anayasa.md` §2 (Fiziksel PCB/BOM → HAYIR) |
| B3 | Zamanlayıcı (scheduler), sayfa tablosu/TLB, önbellek politikası, sürücü | K000-isletim-sistemi | `donanım-anayasa.md` §2 (OS donanımı sürer) |
| B4 | İşlemcinin üzerine yazılan firmware (çekirdek başlatma, DMA) | K005-firmware | `donanım-anayasa.md` §2 |
| B5 | Ses motoru yazılımı (miks · yönlendirme · ASIO/WASAPI) | K003-audio | `donanaım-type-kapsam.md` §3/B5 |
| B6 | GPU'nun hesap/rendır çekirdeği ve compute API'leri | `../gpu/` | kardeş alt-bağlam (bu tur) |
| B7 | Panel · ekran arayüzü · dokunmatik sınıfı | `../display/` | kardeş alt-bağlam |
| B8 | RAM/EEPROM yongası sınıfı | `../memory/` (diğer ajan) | beyazliste — bu tur dokunulmadı |
| B9 | Ağ donanımı · kablo sınıfı | `../network/` · `../cables/` (diğer ajanlar) | beyazliste — bu tur dokunulmadı |
| B10 | CPU envanter kaydı (marka/model/seri no) | K001 envanter (Q4) + owner onayı | bu dosya karar üretmez |
| B11 | Depolama | `../hdd/` · `../ssd/` | kardeş alt-bağlam |

## §4 CPU Sınıfı — K001 Tür Envanteri

> Bu bölüm **tür** listesidir: "CoreMusic sistemlerinde hangi sınıftan işlemci
> bulunabilir" sorusunun cevabı. **Fiziksel olarak elde var mı** ayrı sorudur →
> Q-CK1 (UNKNOWN). Satır 7–8 yalnızca **vault'taki kayıtlı karar** durumudur.

| # | Tür | Görev | Vault'taki karşılığı | Etiket |
|---|---|---|---|---|
| 1 | x86/x64 masaüstü/sunucu işlemcisi | PHP-FPM uygulama katmanı çalıştırma | vault'ta envanter satırı YOK | UNKNOWN (envanter) · DOCUMENTED (sınıf tanımı) |
| 2 | ARM (AArch64) işlemci | Güç/verimlilik odaklı Linux barındırma | `shared/src/Device/DeviceDetector.php` ARM sınıfı tespiti | DOCUMENTED (kod) · UNKNOWN (envanter) |
| 3 | RISC-V işlemci | Açık ISA — aday sınıf | vault'ta envanter/plan YOK | PROPOSED (sınıf) · UNKNOWN (envanter) |
| 4 | Çok-çekirdekli + SMT işlemci | Paralel PHP worker · ses işi | — | UNKNOWN |
| 5 | Önbellek/TLB ve bellek denetleyicisi (bileşen bloğu) | Veri besleme | — | UNKNOWN |
| 6 | Sanallaştırma + SIMD genişletmeleri | Kapsayıcı/VM · vektörleştirme | ADR-006 (OPcache/JIT zorunlu — üretilmiş yapılandırma YOK) | DOCUMENTED (kural) · PLANNED |
| 7 | Gömülü ses işlemcisi (ör. ADR-038'teki XU316) | UAC2.0 ↔ I2S/TDM köprüsü | ADR-038 (accepted, 2026-09-26) | DOCUMENTED (ADR) · PLANNED (firmware, kod 0) |
| 8 | Barındırma/Ci barındırma işlemcisi (runners) | CI işleri | `.github/workflows/ci.yml` = `runs-on: ubuntu-latest` (7 satır) — mimari sabitlenmemiş | DOCUMENTED (workflow) · mimari UNKNOWN |
| 9 | Mobil/tables işlemci (SoC) | Uygulama barındırma | — | UNKNOWN |
| 10 | FPGA / mikrodenetleyici (MCU) | Özel hızlandırma | repo ölçümü: `fpga\|mcu\|cortex` = 0 | UNKNOWN — bu sınıf henüz tanımsız |

**Okuma kuralı:** "DOCUMENTED" = vault'ta okunan belge/ADR/workflow var;
"PLANNED" = karar/spec var, kod/prototip yok; "PROPOSED" = öneri, karar
verilmedi; "UNKNOWN" = veri hiç yok. **Belgede geçen ≠ fiziksel olarak üretilmiş.**

## §5 Komut Takımı (ISA) Aileleri — Kavramsal Karşılaştırma

> ⚠️ VERIFICATION REQUIRED: Bu tablo **kavramsal**tır; hiçbir satırda belirli
> bir modelin saat hızı, çekirdek sayısı veya önbellek boyutu yazmaz. Sayısal
> üretici değerleri yalnız "üretici spec sayfası + erişim tarihi" etiketiyle
> yazılabilir ve **bu dosyada yoktur**.

| # | Soru | x86/x64 | ARM (AArch64) | RISC-V |
|---|---|---|---|---|
| 1 | Komut uzunluğu | Değişken (CISC) | Sabit (RISC) | Sabit (RISC) |
| 2 | Ana kullanım alanı | Masaüstü/sunucu barındırma | Gömülü/mobil/sunucu barındırma | Gömülü/araştırma/açık ISA |
| 3 | 64-bit karşılığı | x86-64 sınıfı | AArch64 | RV64 sınıfı |
| 4 | Uçtan uca (little-endian) destek | Evet | Evet (AArch64 pratikte) | Evet (RV64 pratikte) |
| 5 | SIMD genişletmesi ailesi | SSE/AVX ailesi | NEON (ve SVE sınıfı) | V (vektör) uzantısı |
| 6 | Sanallaştırma genişletmesi | VT-x sınıfı | VHE benzeri uzantılar (detay ⚠️) | H uzantısı (kavramsal ⚠️) |
| 7 | Derleyici hedef etiketi | x86_64 | aarch64 / arm64 | riscv64 |
| 8 | CoreMusic envanterinde | UNKNOWN (Q-CK1) | UNKNOWN — yalnız cihaz sınıfı tespiti var | UNKNOWN |
| 9 | Repo'daki izi (2026-10-13) | `x86_64` = yalnız `.ai` belgelerinde; kaynak kodda ISA intrinsic'i **0** | `arm` = yalnız UA/yorum tespiti | 0 |
| 10 | Bu dosyadaki kanıt seviyesi | S4 (kavram) + S2 (ölçüm) | S4 + S1-rapor (kod ölçümü) | S3 (HEAD 200: riscv.org) |

**Sınır notu:** Hangi ISA'nın seçileceğine **bu dosya karar vermez** — karar
işletim/barındırma katmanının ve owner'ın işidir (`K001 envanter (Q4)`).

## §6 Çekirdek Sayımı ve Mimari Kuşaklar

| # | Kavram | Tanım | Bu konudaki durum |
|---|---|---|---|
| 1 | Fiziksel çekirdek | Paylaşımlı birimi olmayan bağımsız iş birimi | Envanter bilgisi UNKNOWN |
| 2 | Mantıksal çekirdek (SMT/HT) | Fiziksel çekirdeğin paylaştığı ikinci iş akışı | UNKNOWN — açık/kapalı durumu bilinmiyor |
| 3 | Mimari kuşak | Üreticinin aynı ISA içindeki nesil ayrımı (büyüklük/artış anlatımı) | Genel kavram — model kuşağı UNKNOWN |
| 4 | Çekirdek ↔ çekirdek çekirdeği (CCX/benzeri paylaşımlar) | Bellek önbelleği paylaşım topolojisi | UNKNOWN |
| 5 | Çekirdek sabitleme (affinity) | İşin hangi çekirdeğe bağlanacağı | K000 zamanlayıcısının işi — bu dosyada yalnız atıf |
| 6 | Boştaki çekirdek / güç durumu | Frekans/voltaj duraklaması | K000 + güç yönetimi — bu dosyanın dışı (§3/B3) |
| 7 | Çekirdek başına PHP-FPM worker | Uygulama ölçekleme kararı | K003/K007 (servis) — PLANNED, ölçüm yok ⚠️ |
| 8 | Ses işi için çekirdek izolasyonu | Gerçek zamanlı işin çekirdeğe bağlanması | K003 realtime + K000 — bu dosya yalnız sınır yazar |

**Dürüstlük notu:** Çekirdek sayısı, kuşak ve frekans **hiçbiri** bu repo'da
ölçülmedi. `cpu-dogrulama.md` §5'teki envanter tablosu boş kalır.

## §7 Önbellek (Cache) Hiyerarşisi

| # | Katman | Genel rol | Paylaşım | Ölçüm durumu |
|---|---|---|---|---|
| 1 | L1-I / L1-D | Komut ve veri için ayrı, en hızlı katman | çekirdek içi | boyut UNKNOWN |
| 2 | L2 | Orta katman, L1'in miss'ini karşılar | çekirdek (veya cluster) içi | boyut UNKNOWN |
| 3 | L3 (veya büyük önbellek) | Çok-çekirdekli paylaşımlı katman | çekirdek grubu / die | boyut UNKNOWN |
| 4 | Önbellek satırı | Bir transfer birimi (tipik olarak 64 bayt sınıfı — ⚠️ genel bilgi) | — | satır boyu UNKNOWN (ölçülmedi) |
| 5 | Önbellek tutarlılığı | Çekirdekler arası geçerlilik protokolü ailesi (MESI benzeri) | donanım | K000 yönetir — bu dosya yalnız atıf |
| 6 | Sahte paylaşım (false sharing) | Aynı satırda farklı veri yazan iki çekirdek | — | kod kanıtı YOK (repo'da yok) |
| 7 | Cache-friendly veri düzeni | Dizilerin satır hizalı kullanımı | — | K003/uygulama işi |
| 8 | Önbellek yönlendirme (partitioning) | İşletim sistemi/çekirdek kontrolü | — | K000 — bu dosyanın dışı |

> ⚠️ VERIFICATION REQUIRED: 4. satırdaki "64 bayt sınıfı" **genel teknik
> bilgidir**; CoreMusic'in kullandığı işlemcinin gerçek satır boyu
> **ölçülmedi/okunmadı** → UNKNOWN. Hiçbir L1/L2/L3 boyutu bu dosyada yazılmaz.

## §8 Bellek Denetleyicisi (Memory Controller)

| # | Konu | Kavramsal durum | CoreMusic durumu |
|---|---|---|---|
| 1 | Yerleşik denetleyici | Modern işlemcilerde denetleyici die üzerindedir | Genel bilgi; model bilgisi UNKNOWN |
| 2 | Kanal sayısı | Tek/çift/çok kanallı erişim | UNKNOWN (ölçülmedi) |
| 3 | Bellek sınıfı (DDR ailesi) | Nesil ve fiş tipi envantere bağlı | UNKNOWN — `../memory/` (diğer ajan) kapsamındadır |
| 4 | Zamanlama (gecikme) değerleri | Üretici spec'i ile belirlenir | ⚠️ VERIFICATION REQUIRED — ölçüm yok |
| 5 | Hata düzeltme (ECC) | Sunucu sınıfı sistemlerde bulunabilir | UNKNOWN |
| 6 | Band genişliği hesabı | kanal × bus × transfer başına bayt (aritmetik — S2) | Hesap yapılabilir ama kanal bilinmiyor → sonuç YOK |
| 7 | Bellek ↔ önbellek ilişkisi | Denetleyici L3 miss'ini karşılar | §7 ile ilişkili — kavramsal |
| 8 | RAM yongası sınıfı (SO-DIMM/DIMM/somut) | Fiziksel sınıf | `../memory/` — bu dosyada tekrar yazılmaz |

## §9 Komut Seti Genişletmeleri (SIMD dahil)

| # | Genişletme sınıfı | Ne işe yarar | Repo kanıtı (2026-10-13) |
|---|---|---|---|
| 1 | SSE ailesi (x86) | Vektör işleme | `SSE2` = 0 eşleşme (.php/.c/.cpp/.h/.hpp/.cs) |
| 2 | AVX ailesi (x86) | Geniş vektör işleme | `AVX` = 0 eşleşme (kaynak kodda) |
| 3 | NEON (ARM) | Vektör işleme | `NEON` = 0 eşleşme (kaynak kodda) |
| 4 | RISC-V V (vektör) | Ölçeklenebilir vektör | 0 — repo'da geçmiyor |
| 5 | CPUID benzeri sorgu | Çalışma anında genişletme tespiti | `cpuid` = 0 eşleşme |
| 6 | Yüksek çözünürlüklü sayaç (RDTSC benzeri) | Zaman ölçümü | `__rdtsc\|RDTSC` = 0 eşleşme |
| 7 | Derleyici hedef bayrağı (march benzeri) | Derlemede ISA hedefi | `.c/.cpp = 0 dosya` → bayrak yok |
| 8 | PHP genişletmesi (ISA'ye özel zorunlu uzantı) | — | `shared/composer.json` zorunlu `ext-*` listesinde **ISA'ye özel uzantı YOK** |

**Yorum (S2 — aritmetik değil, ölçüm okuması):** Repo kodu ISA'ye özel hiçbir
genişletmeye **bağlı değil**. Bu, uygulamanın herhangi bir ISA'da
çalışabileceği anlamına gelir; ancak **performans iddiası üretmez**.

## §10 Sanallaştırma Genişletmeleri

| # | Kavram | Açıklama | Bu konudaki durum |
|---|---|---|---|
| 1 | VT-x sınıfı genişletme (Intel) | Donanım destekli kapsayıcı/VM yürütme | Görev kapsamında adı geçen genişletme; sistemde var mı → UNKNOWN |
| 2 | AMD-V sınıfı genişletme (AMD) | Aynı işlevin AMD karşılığı | Aynı — UNKNOWN |
| 3 | Yönetilen (root) mod ayrımı | Kapsayıcının ayrıcalıklı çalıştığı özel mod | Kavramsal — ölçüm yok |
| 4 | Adres çevirme genişletmesi (EPT/NPT sınıfı) | Fiziksel↔sanal sayfa çevirisinin donanımda yapılması | Kavramsal ⚠️ |
| 5 | NEON'un sanallaştırma ile ilişkisi | **NEON bir SIMD genişletmesidir, sanallaştırma genişletmesi DEĞİL** | Ayrım bu satırda kayıtlıdır (görev metni §9/SIMD ayrı çalışır) |
| 6 | Kapsayıcı (container) | Donanım sanallaştırmasını kullanmadan izolasyon | K008/K018 servis katmanı — bu dosyanın dışı |
| 7 | CI koşucusunun sanallaştırma kullanımı | Bulut barındırma mimarisi | `runs-on: ubuntu-latest` — mimari sabitlenmedi (§4/8) |
| 8 | Çekirdek derleme/önyükleme | Host OS yetkisi | K000 — bu dosyanın dışı |

## §11 PHP 8.4 Execution Bağlamı (CoreMusic)

> Bu bölüm **disk kanıtlıdır**; marka/model veya performans iddiası içermez.

### §11.1 Okunan kanıtlar

| # | Kanıt | Dosya / değer | Etiket |
|---|---|---|---|
| 1 | PHP sürüm gereksinimi | `shared/composer.json` → `"php": ">=8.4"` | IMPLEMENTED (disk) |
| 2 | Zorunlu PHP uzantıları | `ext-apcu` · `ext-json` · `ext-mbstring` · `ext-pdo` | IMPLEMENTED (disk) |
| 3 | ISA'ye özel zorunlu uzantı | listede **YOK** | IMPLEMENTED (negatif kanıt) |
| 4 | OPcache/JIT kuralı | `.ai/.decisions/accepted/ADR-006-performance-targets.md` §"OPcache / JIT → **Zorunlu, üretimde açık**" | DOCUMENTED (ADR) |
| 5 | opcache çalışma anı kullanımı | `home.coremusic.net/index.php` → `DEBUG_MODE`'da `opcache_reset()` (CONTEXT.md kaydı + K000 runtime-ornekler aktarımı) | DOCUMENTED |
| 6 | Repo'da `opcache.jit` yapılandırması | Eşleşme YOK (`.ini/.yml/.conf/.json/.md` taraması) | UNKNOWN → Q-CK3 |
| 7 | Repo kodunda JIT/ISA kodu | `cpuid/AVX/NEON/SSE2` = 0 eşleşme | IMPLEMENTED (negatif kanıt) |

### §11.2 PHP katmanının CPU'ya bağımlılığı — soru/cevap

| # | Soru | Yanıt | Kanıt |
|---|---|---|---|
| 1 | PHP 8.4 hangi ISA'yı **zorunlu** tutuyor? | `composer.json` hiçbir ISA uzantısı **talep etmiyor**; gereksinim yalnız PHP sürümü ve 4 eklenti | §11.1/1-3 |
| 2 | JIT açık mı? | Vault kuralı "zorunlu, üretimde açık" (ADR-006); depoda bunu **kanıtlayan yapılandırma dosyası yok** | §11.1/4-6 → Q-CK3 |
| 3 | JIT hangi hedef mimariyi üretir? | ⚠️ VERIFICATION REQUIRED — php.net kılavuzunun içeriği bu oturumda **okunmadı** (yalnız HEAD 200) | §21/3 |
| 4 | CPU özellikleri ölçümü yapıldı mı? | **HAYIR — 0 ölçüm** | `cpu-dogrulama.md` §4 |
| 5 | Envanter (hangi CPU?) | **UNKNOWN** | Q-CK1 |
| 6 | Bu bölümün etiketi | DOCUMENTED (disk) + UNKNOWN (JIT durumu) | §14 |

**Sonuç cümlesi (bağlayıcı):** CoreMusic'in PHP katmanı, diske yazılan
kanıta göre **belirli bir SIMD/sanallaştırma genişletmesine bağlı değildir**;
bunun yerine OPcache/JIT kuralı vardır ve JIT'in gerçekten açık olduğunu
**kanıtlayan yapılandırma bulunmamaktadır** → `UNKNOWN`.

## §12 Ses İşlemede CPU Rolü (DSP ≠ CPU ayrımı)

| # | Soru | Sahip | Bu dosyanın tutumu |
|---|---|---|---|
| 1 | Ses **işi** hangi genel amaçlı işlemcide koşar? | K001 cpu (sınıf) + K003 (yazılım) | Sınıf tanımı bu dosyada; uygulama K003 |
| 2 | Donanım DSP çipleri (EQ · reverb · gürültü azaltma donanımı) | **K004-dsp** | Bu dosya DSP çipi **tanımlamaz** |
| 3 | Ses kartı köprüsü (USB ↔ I2S/TDM) | ADR-038 çekirdeği (gömülü işlemci) | §4/7 — ADR-038 **DEĞİŞMEZ**, yalnız aktarım |
| 4 | Gerçek zamanlı kısıtlar (tampon · gecikme · underrun) | K003 (`realtime/`, `output/`) | yalnız atıf |
| 5 | DSP donanım modu kararı | ADR-017 (accepted — varlık iddiası) | bu dosya içeriği okumaz |
| 6 | Çekirdek izolasyonu / zamanlayıcı | K000 | §3/B3 |
| 7 | Mikser/zaman alanı algoritması | K003-audio | §3/B5 |
| 8 | Devre/PCB tasarımı | K002-electroncis | §3/B2 |

**Ayrım cümlesi (bağlayıcı):** `cpu/` = **genel amaçlı işlem** (her işi
yapabilen çip sınıfı); `K004-dsp` = **özelleşmiş donanım DSP çipi**. İkisi
aynı satırda "işlemci" diye geçse de **farklı K düğümüdür**; bu dosya DSP
sınıfını içermez, yalnız sınırını yazar.

## §13 Tür Kayıtları (26 alan şeması — 6 kayıt)

> Her kayıt 26 alanla yazılır. Bilinmeyen alan `UNKNOWN` / `NOT PROVIDED` /
> `VERIFY REQUIRED` ile işaretlenir; marka/model **yalnız kanıtlanırsa** yazılır
> (bu turda **kanıtlanan fiziksel cihaz/model YOKTUR** — envanter UNKNOWN).
> Alan 21 (`existing_document`) yalnız vault disk dosyasını gösterir; alan 22
> (`evidence`) yalnız bu oturumda doğrulanmış kanıttır.

### §13.1 C-T01 — x86/x64 işlemci sınıfı

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | C-T01 |
| 2 | name_tr | x86/x64 genel amaçlı işlemci |
| 3 | name_en | x86/x64 general-purpose processor |
| 4 | category | cpu |
| 5 | subcategory | ISA ailesi — masaüstü/sunucu |
| 6 | description | Değişken uzunluklu komut setine sahip, yaygın barındırma platformunda çalışan genel amaçlı işlemci sınıfı |
| 7 | physical_component_or_device | fiziksel bileşen (işlemci çipi) — cihaz ayrı (envanter UNKNOWN) |
| 8 | primary_function | Uygulama kodunun (PHP 8.4 dâhil) yürütülmesi |
| 9 | interfaces | Anakart yuvası / bellek denetleyicisi yolu — türü UNKNOWN |
| 10 | protocols | Bellek/çevre yolu protokolleri ayrı eksendedir (§8) — bu kayıt ISA sınıfıdır |
| 11 | power_requirements | UNKNOWN (model datasheet'i yok) |
| 12 | operating_environment | UNKNOWN (çalışma ortamı kararı yok) |
| 13 | os_dependencies | EVET — işletim sistemi bu sınıfı zamanlar ve yapılandırır (K000) |
| 14 | driver_requirements | Çekirdek/CPU mikro kodu ve sürücüsü K000 kapsamındadır |
| 15 | firmware_dependency | BIOS/UEFI + mikro kod — varlığı ve sürümü UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET — K001 sınıfı envanter kaydı olarak sunar, ham register/ zamanlama yazmaz |
| 17 | audio_relevance | YOK-DOĞRUDAN (ses işi K003 tarafından bu sınıf üzerinde koşar — dolaylı) |
| 18 | coremusic_relevance | PHP 8.4 uygulama katmanının çalıştığı sınıf; §11 kanıtı |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · zamanlama/sürücü K000 |
| 21 | existing_document | `cpu/cpu-kapsam.md` (bu dosya) — ayrı model dosyası YOK |
| 22 | evidence | Repo ölçümü 2026-10-13 (§9) · `shared/composer.json` · Vault Steward 2026-10-13 onayı |
| 23 | status | PROPOSED (sınıf kaydı) · envanter UNKNOWN |
| 24 | confidence | LOW (envanter yok; yalnız sınıf tanımı) |
| 25 | open_questions | Q-CK1 (envanter) · model/kuşak/önbellek UNKNOWN |
| 26 | proposed_path | `cpu/cpu-kapsam.md` §13.1 · kapı `cpu-dogrulama.md` K-C1 |

### §13.2 C-T02 — ARM (AArch64) işlemci sınıfı

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | C-T02 |
| 2 | name_tr | ARM (AArch64) genel amaçlı işlemci |
| 3 | name_en | ARM (AArch64) general-purpose processor |
| 4 | category | cpu |
| 5 | subcategory | ISA ailesi — gömülü/mobil/barındırma |
| 6 | description | Sabit uzunluklu komut setine sahip, verimlilik odaklı genel amaçlı işlemci sınıfı |
| 7 | physical_component_or_device | fiziksel bileşen (işlemci/SoC) — envanter UNKNOWN |
| 8 | primary_function | Uygulama ve sistem kodunun yürütülmesi |
| 9 | interfaces | SoC yolları (bellek/çevre) — ayrıntı UNKNOWN |
| 10 | protocols | Bus/denetleyici protokolleri §8'de ayrı kayıt |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — Linux/Android sınıfı sistemler (K000) |
| 14 | driver_requirements | K000 (çekirdek + mikro kod sınıfı) |
| 15 | firmware_dependency | Önyükleme firmware'i K005/K000 sınırı — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | YOK-DOĞRUDAN (dolaylı: ses işi bu sınıf üzerinde koşabilir) |
| 18 | coremusic_relevance | Cihaz sınıfı tespiti kodu bu sınıfı tanır: `shared/src/Device/DeviceDetector.php` (ARM Linux satırı) |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · OS katmanı K000 |
| 21 | existing_document | `cpu/cpu-kapsam.md` (bu dosya) |
| 22 | evidence | `DeviceDetector.php` ARM sınıfı tespiti (okundu 2026-10-13) · arm.com/architecture HEAD 200 |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-CK1 |
| 26 | proposed_path | `cpu/cpu-kapsam.md` §13.2 |

### §13.3 C-T03 — RISC-V işlemci sınıfı

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | C-T03 |
| 2 | name_tr | RISC-V genel amaçlı işlemci |
| 3 | name_en | RISC-V general-purpose processor |
| 4 | category | cpu |
| 5 | subcategory | ISA ailesi — açık ISA |
| 6 | description | Açık ve modüler uzantılara sahip RISC komut takımı sınıfı |
| 7 | physical_component_or_device | fiziksel bileşen — envanter UNKNOWN |
| 8 | primary_function | Genel amaçlı yürütme |
| 9 | interfaces | Yonga içi/dışı yollar — UNKNOWN |
| 10 | protocols | §8 — ayrı kayıt |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET (destekleyen sistem varsa) — CoreMusic'te KULLANIM YOK |
| 14 | driver_requirements | K000 |
| 15 | firmware_dependency | UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | YOK |
| 18 | coremusic_relevance | Henüz yok — yalnız sınıf kaydı (PROPOSED) |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım |
| 21 | existing_document | `cpu/cpu-kapsam.md` (bu dosya) |
| 22 | evidence | https://riscv.org/ HEAD 200 (erişim 2026-10-13) — içerik okunmadı |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-CK1 · "RISC-V aday mı?" sorusu sahip bekliyor |
| 26 | proposed_path | `cpu/cpu-kapsam.md` §13.3 |

### §13.4 C-T04 — Çok-çekirdekli / SMT işlemci (çekirdek sayımı ve kuşak)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | C-T04 |
| 2 | name_tr | Çok-çekirdekli işlemci (fiziksel/mantıksal çekirdek ve kuşak) |
| 3 | name_en | Multi-core processor (core count and microarchitecture generation) |
| 4 | category | cpu |
| 5 | subcategory | yapı — çekirdek sayımı ve mimari kuşak |
| 6 | description | Birden fazla fiziksel çekirdek (ve varsa mantıksal eşlek) barındıran, aynı ISA içinde kuşaklarla ilerleyen işlemci yapı sınıfı |
| 7 | physical_component_or_device | fiziksel bileşen (tek çip, çok çekirdek) |
| 8 | primary_function | Paralel iş yürütme ve iş paylaşımlı çalışma |
| 9 | interfaces | Bellek denetleyicisi + çevre yolları (§8) |
| 10 | protocols | §8 — ayrı kayıt |
| 11 | power_requirements | UNKNOWN (TDP benzeri değer yok) |
| 12 | operating_environment | UNKNOWN (termal sınır ⚠️ ölçülmedi) |
| 13 | os_dependencies | EVET — çekirdek/izolasyon kararları K000'de |
| 14 | driver_requirements | K000 (çekirdek yönetimi) |
| 15 | firmware_dependency | Mikro kod/BIOS — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET |
| 17 | audio_relevance | DOLAYLI — ses işi çekirdek izolasyonuna bağlıdır (K003/K000) |
| 18 | coremusic_relevance | PHP worker ölçekleme ve CI iş paralelliği bu yapıya bağlı |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · zamanlama K000 |
| 21 | existing_document | `cpu/cpu-kapsam.md` §6 (bu dosya) |
| 22 | evidence | Repo ölçümü: çekirdek sayısı/kuşak iddiası **0** (uydurma yok) |
| 23 | status | PROPOSED · envanter UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-CK1 — çekirdek sayısı ve kuşak UNKNOWN |
| 26 | proposed_path | `cpu/cpu-kapsam.md` §13.4 |

### §13.5 C-T05 — Önbellek hiyerarşisi ve bellek denetleyicisi (bileşen bloğu)

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | C-T05 |
| 2 | name_tr | Önbellek hiyerarşisi ve bellek denetleyicisi |
| 3 | name_en | Cache hierarchy and memory controller |
| 4 | category | cpu |
| 5 | subcategory | bileşen bloğu — veri besleme yolu |
| 6 | description | Çekirdek içindeki L1/L2/L3 önbellek katmanları ile bellek erişimini sağlayan denetleyicinin oluşturduğu veri besleme yolu |
| 7 | physical_component_or_device | işlemci içi blok (ayrı çip DEĞİL) |
| 8 | primary_function | Çekirdeğe veri ve komut sağlama, bellek erişimini koordine etme |
| 9 | interfaces | Bellek yuvası/fiş yolu — türü UNKNOWN |
| 10 | protocols | Bellek protokolü (nesne/fiş sınıfı) — UNKNOWN |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | Kısmen — önbellek/TLB politikası K000'de |
| 14 | driver_requirements | HAYIR (donanım içi blok) · politika K000 |
| 15 | firmware_dependency | HAYIR — mikro kod etkisi ⚠️ VERIFY REQUIRED |
| 16 | hardware_abstraction_requirement | EVET — ölçüm/raporlama K000 üzerinden |
| 17 | audio_relevance | DOLAYLI — önbellek dostu veri düzeni ses işleme gecikmesini etkiler (iddia ölçülmedi ⚠️) |
| 18 | coremusic_relevance | Büyük dizi/bellek işlemleri için kavramsal zemin |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · politika K000 |
| 21 | existing_document | `cpu/cpu-kapsam.md` §7–§8 (bu dosya) |
| 22 | evidence | Ölçüm YOK — boyut/kanal değerleri uydurulmadı |
| 23 | status | PROPOSED · değerler UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-CK1 · L1/L2/L3 boyutları ve kanal sayısı UNKNOWN |
| 26 | proposed_path | `cpu/cpu-kapsam.md` §13.5 |

### §13.6 C-T06 — Sanallaştırma ve SIMD genişletmeleri

| # | Alan | Değer |
|---|---|---|
| 1 | type_id | C-T06 |
| 2 | name_tr | Sanallaştırma ve vektör (SIMD) komut genişletmeleri |
| 3 | name_en | Virtualization and SIMD instruction extensions |
| 4 | category | cpu |
| 5 | subcategory | komut seti genişletmesi |
| 6 | description | İşlemciye ek yetenek veren komut seti uzantıları: donanım destekli kapsayıcı/VM yürütmesi ve geniş vektör işlemesi |
| 7 | physical_component_or_device | donanım içi genişletme (ayrı bileşen değil) |
| 8 | primary_function | Kapsayıcı/VM yürütme hızlandırma · vektörleştirilmiş aritmetik |
| 9 | interfaces | Programlama modeli arayüzü (derleyici/OS) |
| 10 | protocols | Yok — komut düzeyinde genişletme |
| 11 | power_requirements | UNKNOWN |
| 12 | operating_environment | UNKNOWN |
| 13 | os_dependencies | EVET — genişletmeleri etkinleştirme K000'de |
| 14 | driver_requirements | OS çekirdeği + derleyici hedefi |
| 15 | firmware_dependency | BIOS/UEFI bayrağı olabilir — UNKNOWN |
| 16 | hardware_abstraction_requirement | EVET — envanter kaydı olarak |
| 17 | audio_relevance | DOLAYLI (vektörleştirme ses işlemede kullanılabilir) — kod YOK ⚠️ PLANNED |
| 18 | coremusic_relevance | Repo ölçümü: `cpuid/AVX/NEON/SSE2/Vulkan` = 0 → şu an **kullanılmıyor** |
| 19 | parent_domain | K001-donanım/donanaım-type |
| 20 | owner_domain | K001-donanım (tür) · etkinleştirme K000 · derleme K005 |
| 21 | existing_document | `cpu/cpu-kapsam.md` §9–§10 (bu dosya) |
| 22 | evidence | Repo grep 2026-10-13 (0 eşleşme) · görev metni VT-x/NEON kapsamı |
| 23 | status | PROPOSED · sistemde var mı UNKNOWN |
| 24 | confidence | LOW |
| 25 | open_questions | Q-CK1 · Q-CK3 (JIT) · VT-x/AMD-V varlığı UNKNOWN |
| 26 | proposed_path | `cpu/cpu-kapsam.md` §13.6 |

> §13.5 C-T05 kaydı 26 alanın tamamını taşır; alan 8 = `primary_function` (şema dışı alan yok).

## §14 Etiket Disiplini (belgede geçen ≠ üretilmiş)

| # | Etiket | Anlamı | Bu dosyalarda geçerli örnek |
|---|---|---|---|
| 1 | IMPLEMENTED | Kod/disk kanıtı vardır | `composer.json` php>=8.4 · dosya sayımı |
| 2 | DOCUMENTED | Belge/ADR/workflow okunmuştur; üretim yok | ADR-006 OPcache/JIT kuralı · ADR-038 |
| 3 | PLANNED | Karar/spec var, kod/prototip yok | JIT etkinliği · ses işi çekirdek izolasyonu |
| 4 | PROPOSED | Öneri/envanter kaydı; karar verilmedi | 6 tür kaydının tamamı · bu klasörün kendisi |
| 5 | UNKNOWN | Veri hiç yok | CPU envanteri (Q-CK1) · önbellek boyutları |
| 6 | VERIFY REQUIRED | Doğrulanamayan iddia | Anayasa revizyonu · JIT hedef mimarisi |

**Kapı kuralı:** Bir satırda tek etiket bulunur; karışık durumda **en düşük**
etiket yazılır. "Üretildi/sahte" iddiası yalnız IMPLEMENTED ile yazılır ve
**dosya yolu + sayı** ister (`cpu-dogrulama.md` §6).

## §15 K001 ↔ Komşu Düğüm Sınırı (çakışma yazma kuralı)

| # | Soru | Sahip | K001 cpu'un yazabileceği | K001 cpu'un YAZAMAYACAĞI |
|---|---|---|---|---|
| 1 | Hangi ISA/envanter? | K001 | tür + sınıf tanımı | marka/model (kanıt yoksa) |
| 2 | Zamanlayıcı/çekirdek izolasyonu? | K000 | yalnız atıf | politika/ölçüm |
| 3 | Önbellek/TLB politikası? | K000 | kavram (§7) | politika kararı |
| 4 | DSP algoritması / DSP çipi? | K004-dsp | sınır satırı (§12) | DSP sınıfı tanımı |
| 5 | Ses motoru gecikmesi? | K003 | donanım katkısı tanımı | "gecikme şudur" kararı |
| 6 | Firmware/çekirdek başlatma? | K005 | — | — |
| 7 | Devre/PCB/BOM? | K002-electroncis | atıf | tasarım |
| 8 | RAM yongası sınıfı? | `../memory/` | atıf | sınıf tanımı |
| 9 | GPU hesap çekirdeği? | `../gpu/` | sınır (§3/B6) | compute API tanımı |
| 10 | Panel/arayüz? | `../display/` | sınır | ekran sınıfı tanımı |
| 11 | CI mimarisi? | K018/K008 | workflow satırı (§4/8) | mimari kararı |
| 12 | PHP sürüm/uzantı gereksinimi? | uygulama katmanı | disk aktarımı (§11) | sürüm kararı |

## §16 Kabul Kapıları (tanım — uygulama `cpu-dogrulama.md`)

| # | Kapı | Hedef | Şu anki durum |
|---|---|---|---|
| 1 | K-C1 Envanter | CPU model/çekirdek/kuşak/önbellek listesi | ❌ UNKNOWN (Q-CK1) |
| 2 | K-C2 Ölçüm | Fiziksel ölçüm 0 → hiçbir performans iddiası yazılamaz | ❌ açık |
| 3 | K-C3 Repo kanıtı | ISA intrinsic = 0 · gömülü kaynak = 0 | ✅ kapandı (negatif kanıt) |
| 4 | K-C4 PHP bağlamı | composer.json + ADR-006 okundu | ✅ kapandı |
| 5 | K-C5 JIT durumu | Depoda JIT yapılandırması kanıtı | ❌ açık (Q-CK3) |
| 6 | K-C6 Web kaynakları | Kullanılan her URL HEAD 200 | ✅ kapandı (§21) |
| 7 | K-C7 Çelişki/onay | §0 tablosu + anayasa revizyonu | ❌ açık — APPROVAL REQUIRED |
| 8 | K-C8 Etiket | Her satırda tek etiket | ✅ bu dosyada uygulandı |

## §17 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Envanter yokken "hangi CPU" sorusuna cevap uydurmak | kritik | **aktif** | Q-CK1 kapatılmadan model yazılmaz |
| 2 | "Belgede var" → "üretildi" sanılması | yüksek | yüksek | §14 etiket + doğrulama §6 |
| 3 | Anayasa revizyonu yapılmadan kalıcı yapı sanılması | yüksek | orta | §0 — status: proposed |
| 4 | DSP ile CPU'nun karıştırılması | orta | orta | §12 ayrım tablosu |
| 5 | Sayısal önbellek/kanal değeri uydurma | kritik | orta | §7/§8'de değer yok |
| 6 | URL erişilemezken kaynak yazmak | orta | düşük | yalnız HEAD 200 (§21) |
| 7 | cpu/gpu sınırlarının kayması | orta | orta | §3/B6 · gpu-kapsam §3 |
| 8 | Ölçümsüz "hızlı/hafif" iddiası | yüksek | orta | K-C2 kapısı |

## §18 Uygulama Sırası (checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | CPU envanterini çıkar (model · çekirdek · kuşak · önbellek · kanal) | liste | K-C1 |
| 2 | 6 tür kaydını 26 alanla doldurmaya devam et | §13 | K-C8 |
| 3 | PHP/JIT yapılandırma kanıtını bul veya yokluğunu kaydet | kayıt | K-C5 |
| 4 | Anayasa + kapsam metin revizyonunu iste | onay talebi | K-C7 |
| 5 | Web kaynaklarını HEAD ile tekrar doğrula | URL tablosu | K-C6 |
| 6 | Ölçüm tezgâhı varsa ölç; yoksa kapıyı açık bırak | ölçüm raporu | K-C2 |
| 7 | Sınır tablosunu gpu/display/memory ile çapraz kontrol et | sınır | §15 |

## §19 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Belirli bir CPU modeli/saat hızı yazmak | Kanıtsız sayı | UNKNOWN + K-C1 |
| 2 | DSP çipini bu dosyaya koymak | Çifte sahiplik | K004 — §12 |
| 3 | "Belgede var" → "üretilmiş" | Sahte kanıt | §14 etiket disiplini |
| 4 | JIT'in açık olduğunu varsaymak | Kanıtsız iddia | Q-CK3 → UNKNOWN |
| 5 | Anayasa revizyonunu yapılı sanmak | Kapanmış kapı sanısı | §0 — APPROVAL REQUIRED |
| 6 | Erişilemeyen URL'yi kaynak yazmak | Kırık referans | yalnız HEAD 200 |
| 7 | Yer tutucu işaretler kullanmak | Boş tablo | `UNKNOWN` / `⚠️` |
| 8 | `parent` anahtarı ile frontmatter yazmak | Şablon ihlali | `parent_id` kullanılır |
| 9 | Yazımı düzeltmek ("donanım-type") | 489+ wiki-link kırılır | yazım korunur |

## §20 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | ISA (komut takımı) | İşlemcinin anladığı komutların tanımı |
| 2 | x86/x64 | Değişken uzunluklu komut takımının 64-bit sürdürücüsü sınıfı |
| 3 | AArch64 | ARM'ın 64-bit komut takımı |
| 4 | RISC-V | Açık ve modüler RISC komut takımı |
| 5 | CISC / RISC | Karmaşık/kısa komut takımı felsefeleri |
| 6 | Çekirdek (core) | Bağımsız iş birimi |
| 7 | SMT / hiper-iş parçacığı | Fiziksel çekirdekte ikinci iş akışı |
| 8 | Kuşak (nesil) | Aynı ISA içindeki mimari nesil ayrımı |
| 9 | L1/L2/L3 | Önbellek katmanları (hız/boyut sırası) |
| 10 | Önbellek satırı | Veri transfer birimi |
| 11 | Tutarsızlık protokolü | Çekirdekler arası önbellek geçerliliği |
| 12 | Bellek denetleyicisi | Bellek erişimini sağlayan donanım bloğu |
| 13 | Kanal | Denetleyicinin eşzamanlı erişim yolu |
| 14 | TLB | Sanal→fiziksel sayfa çevirme önbelleği (K000) |
| 15 | SIMD | Tek komutla çoklu veri işlemesi |
| 16 | SSE / AVX | x86 vektör genişletme ailesi |
| 17 | NEON | ARM vektör genişletmesi |
| 18 | VT-x / AMD-V | Donanım destekli sanallaştırma genişletmeleri (adlar görev kapsamındadır) |
| 19 | EPT/NPT | Adres çevirme hızlandırma sınıfı |
| 20 | JIT | Çalışma anında makine kodu üreten derleyici |
| 21 | OPcache | PHP'nin derlenmiş bytecode önbelleği |
| 22 | PHP-FPM | PHP için süreç yöneticisi |
| 23 | Uygulama katmanı | PHP ile yazılan servis kodu |
| 24 | Genel amaçlı işlem | Her işi yapabilen işlemci (bu dosyanın konusu) |
| 25 | DSP çipi | Özelleşmiş sinyal işleme donanımı (K004) |
| 26 | IMPLEMENTED / PLANNED / PROPOSED / UNKNOWN | §14 etiketleri |

## §21 Kaynakça (bu dosyanın kaynakları)

| # | Kaynak | Tarih | Sonuç |
|---|---|---|---|
| 1 | `shared/composer.json` (php >=8.4 · 4 zorunlu uzantı) | 2026-10-13 okundu | ✅ disk kanıtı |
| 2 | `.ai/.decisions/accepted/ADR-006-performance-targets.md` (OPcache/JIT zorunlu) | 2026-10-13 okundu | ✅ disk kanıtı |
| 3 | `https://www.php.net/` | 2026-10-13 HEAD | ✅ HTTP 200 — içerik okunmadı |
| 4 | `https://www.php.net/releases/8.4/en.php` | 2026-10-13 HEAD | ✅ HTTP 200 — içerik okunmadı |
| 5 | `https://www.arm.com/architecture` | 2026-10-13 HEAD | ✅ HTTP 200 — içerik okunmadı |
| 6 | `https://riscv.org/` · `https://www.riscv.org/` | 2026-10-13 HEAD | ✅ HTTP 200 / 200 — içerik okunmadı |
| 7 | `https://www.gnu.org/` | 2026-10-13 HEAD | ✅ HTTP 200 — içerik okunmadı |
| 8 | `https://www.intel.com/` | 2026-10-13 HEAD | ❌ **HTTP 403 → KULLANILMADI** |
| 9 | `https://www.xmos.com/` | 2026-10-13 HEAD | ❌ **HTTP 406 → KULLANILMADI** |
| 10 | `donanım-anayasa.md` §10/3 · §12/C10 · §12.1 | 2026-10-13 okundu | ✅ disk kanıtı |
| 11 | `donanaım-type-kapsam.md` §2/A6 · §7/3-T1 | 2026-10-13 okundu | ✅ disk kanıtı |
| 12 | `.ai/.decisions/draft/ADR-102-...-5-dal.md` §4 satır 107 | 2026-10-13 okundu | ✅ disk kanıtı — `draft` |
| 13 | `shared/src/AI/AIEngine.php:140` (XMOS XU316 yorumu) | 2026-10-13 okundu | ✅ disk kanıtı |
| 14 | Repo ölçümleri (§9) | 2026-10-13 | ✅ ölçüm |

**Uydurma kaynak yok:** 5 web URL'si bu oturumda HEAD ile 200 döndürülerek
doğrulanmıştır; 403 ve 406 döndüren 2 URL **kullanılmamıştır**; diğer satırlar
disk dosyası referansıdır. **Yeni envanter kesişimi: 0** (bu dosya envantere
yeni fiziksel parça iddiası eklemez).

## §22 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[cpu/index]]` | Bu konunun künyesi |
| 2 | `[[donanaım-type/index]]` | Üst künye (children/§GRUPLAR orkestratörde) |
| 3 | `[[donanaım-type-kapsam]]` | Üst kapsam — §2/A6 · §7/3-T1 (çelişki §0) |
| 4 | `donanım-anayasa.md` §10 · §12/C10 | Anayasa sınırı (çelişki §0) |
| 5 | `[[gpu-kapsam]]` | Kardeş sınıf (A-grafik) — sınır §3/B6 |
| 6 | `[[display-kapsam]]` | Kardeş sınıf (panel/arayüz) |
| 7 | `[[audio-kapsam]]` · `[[hdd-kapsam]]` · `[[ssd-kapsam]]` | İlk 3 bağlam |
| 8 | `cpu-dogrulama.md` | Kapılar K-C1…K-C8 |
| 9 | `cpu-ornekler.md` | Uygulama şablonları |
| 10 | ADR-006 · ADR-038 · ADR-102 (draft) | Karar kayıtları (varlık + okunan) |

## §23 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya yalnız okunan disk dosyaları
(`shared/composer.json`, ADR-006, ADR-102 draft, AIEngine.php:140, anayasa ve
üst kapsam satırları), kendi repo ölçümleri (2026-10-13) ve 5 adet HEAD-200 web
URL'si üzerine kuruludur; 403/406 döndüren URL'ler **kullanılmamıştır**.
**Hiçbir fiziksel ölçüm YAPILMAMIŞTIR** — CPU envanteri UNKNOWN'dur ve tüm
performans iddiaları `⚠️ VERIFICATION REQUIRED` işaretlidir. Belgede geçen hiçbir
değer "fiziksel olarak üretildi" diye sunulmamıştır; yer tutucu işaretler
kullanılmamıştır; bilinmeyenler `UNKNOWN` ile işaretlenmiştir. **Yeni fiziksel
envanter iddiası: 0.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
