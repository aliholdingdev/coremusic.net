---
title: "driver — Örnekler"
type: architecture-examples
node_id: K003-audio/driver
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kod
---

# driver — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` ve `.github/workflows/` içinde sürücü katmanı
kodu bulunmaz (2026-10-12 kök `*.cpp`/`*.h` glob 0 isabet). Sürücüye giden
çağrı (ASIO/WASAPI/ALSA) üretecek tek satır bile yok; aranan: sürücü açma/kapama,
buffer yazma, hata kodu çevirme. Bulunan: yalnız AI/PHP katmanı.

İlgili tek gerçek repo kodu (yan sınır — driver DEĞİL, AI ajan eşlemesi):

```php
// shared/src/AI/AIOrchestrator.php : 32, 36, 45
// "embedded" / "dsp-fw" ajan alan eşlemeleri (audio-dsp) — ajan yönlendirme,
// sürücü çağrısı DEĞİLDİR; driver düğümü kanıtı olarak kullanılamaz.
// shared/src/AI/ToolCalling.php : 38 — "dsp" araç alanı eşlemesi (aynı uyarı).
```

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | WASAPI exclusive mode streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Özel kip (exclusive) akışlarının kurulumu ve davranışı | Windows'ta sürücüyle konuşma iki kiptir: exclusive (tam sahiplik) ↔ shared (paylaşım); driver bu seçimi tek yerde taşır |
| 2 | ALSA PCM hw_params / sw_params | https://www.alsa-project.org/doc/api/group___p_c_m.html | `hw_params` (format/rate/channels/buffer) → `sw_params` (avail_min/period_event) sırası | Sürücü yüzeyi **aşamalıdır**: önce format pazarlığı, sonra akış ayarı — tek çağrıda açılmaz |
| 3 | ALSA beş access modu | https://www.alsa-project.org/doc/api/group___p_c_m.html | MMAP_INTERLEAVED · MMAP_NONINTERLEAVED · MMAP_COMPLEX · RW_INTERLEAVED · RW_NONINTERLEAVED | Sürücü erişim kipi seçeneği çoktur; driver arayüzü bu seçenekleri üst katmana **açmalı** ya da tek birini zorlamalı |
| 4 | ALSA plug plugin | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | format/channels/rate otomatik dönüşümü (slave belirtilir) | Uyumsuzluğu sürücü katmanında **çözme** imkânı var — motor bunu istismar etmeden önce bilmeli |
| 5 | PipeWire driver node scheduling | https://pipewire.org/page_scheduling | driver node döngüyü başlatır; required/pending sayaçları | Zamanlamayı **sürücü tarafındaki driver node** sahiplenir; CoreMusic'te karşılığı realtime + driver sınırı |
| 6 | Steinberg ASIO SDK (açık kaynak) | https://www.steinberg.net/developers/asiosdk-open | SDK'nın açık kaynak ilanı | Windows düşük-gecikme kapısı adayı; lisans (GPLv3) riski master §8 Q1 ile açık |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **driver tasarımı öncesi** şablon olarak kullanılır:
1–2, sürücü arayüzünün **aşamalı** olduğunu kanıtlar (format pazarlığı → akış
ayarı → yaz/ok); 3–4, erişim kipi ve dönüşüm seçeneklerinin nerede
çözüleceğini (sürücü katmanı) gösterir; 5, zamanlama sahipliğinin driver node
tarafında olduğunu; 6, Windows düşük-gecikme kapısının adayını. Kesin kapı
seçimi (ASIO/WASAPI) R1 (`driver-kapsam.md` §5) kapanmadan yazılmaz; gecikme
değerleri uydurulmaz → UNKNOWN.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `driver-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `driver-dogrulama.md` | Kanıt/doğrulama tablosu (16 satır: ✅ 10 · ⚠️ 3 · UNKNOWN 3) |
| `driver/index.md` | Düğüm kaydı (ELI15 · açık soru: ASIO mu WASAPI mi) |
| `../audio-master.md` | Üst anayasa §3 satır 3 · §4 A1–A6 · §5 T1/T6/T7/T13/T14 · §8 Q1/Q2/Q4 |
| `../audio-bagimliliklar.md` | §2 — driver ↔ backend/diagrams sınır riskleri |
| `../backend/index.md` | backend alt-alanı (asio kataloğu) — driver'ın komşusu |

---

## §5 Eklenen şablonların kullanım sınıfı

> §1 ("— yok") ve §2'deki 6 doğrulanmış örnek aynen geçerlidir; bu bölüm
> onların yerine geçmez. Tüm ek şablonlar `driver-kapsam.md` §5'teki R1–R6
> kapılarıyla kilitlidir.

| # | Şablon | Dayandığı §2 örneği | Sınıf | Yasak |
|---|---|---|---|---|
| 1 | §6 yaşam döngüsü | satır 2 (hw_params→sw_params) | [TASARIM] öncesi aşama dizisi | kapı seçimi (R1) denmez |
| 2 | §7 akış şablonları | satır 1–4 (exclusive · hw/sw · access · plug) | kavramsal akış | IRP kodu yazılmaz |
| 3 | §8 sınır tabloları | §4 (master §3 satır 3) | sınır taslağı | Q6'ya cevap denmez |
| 4 | §9 hata modları | satır 6 (ASIO SDK) + §4 iç kanıtlar | aday kök neden listesi | kesin kök neden denmez |
| 5 | §10 kapı karşılaştırması | satır 1–6 | karşılaştırma | karar denmez (R1) |
| 6 | §11 repo kaydı | §1 ölçümü (2026-10-12) | kanıt tekrarı | yeni ölçüm iddiası yok |
| 7 | §12–§13 | §3–§4 | eşleme/atıf | kapı "geçti" denmez |

## §6 Sürücü yaşam döngüsü örnek akışları

### §6.1 Açılış akışı (ağaç)

```text
Motor "çıktıyı başlat" istedi
  └─ Kapı seçimi: HANGİ? (R1 AÇIK — karar yok)
       ├─ ASIO adayı: SDK yüzeyi (backend/asio kataloğu — komşu derin-dal)
       │    ├─ sürüm/lisans kapıları: R2 · R3 KAPALI
       │    └─ cihaz kaybı → fallback: `.ai/AGENTS.md` §17 #6 (tek kural)
       ├─ WASAPI adayı: exclusive/shared (§2 satır 1 — fetch edilmiş)
       │    └─ kip kararı: R1 KAPALI
       ├─ ALSA adayı (Linux): hw_params → sw_params (§2 satır 2)
       │    └─ uyuşmazlık → plug (§2 satır 4)
       └─ PipeWire adayı (Linux): node/link + driver node zamanlaması (§2 satır 5)
  Ağaç bir TERCİH SIRASI DEĞİLDİR — sıralama R1 kapanınca yazılacaktır.
```

### §6.2 Aşama adım tablosu (kapı bağımsız)

| # | Adım | Girdi | Çıktı | Hata dalı | Durum |
|---|---|---|---|---|---|
| 1 | kapı seç | niyet | kapı adayı | aday yok → DURDU | R1 AÇIK |
| 2 | kapı aç | kapı | tutamaç | açılmadı → kodu sakla | şablon |
| 3 | enum | tutamaç | cihaz listesi | boş → device (§2 satır 4) | SINIR |
| 4 | cihaz seç | liste + device kararı | hedef | yok → reddet | SINIR (D2) |
| 5 | format pazarla | hedef nitelik | kabul/ret | ret → dönüşüm (§2 satır 4) | şablon |
| 6 | buffer pazarla | hedef (512/48 sartname) | kabul/ret | ret → alternatif | R4 AÇIK |
| 7 | kip seç | niyet | exclusive/shared | ret → shared (karar R1) | R1 AÇIK |
| 8 | başlat | hepsi | akış | → hata şablonu (§7.4) | şablon |
| 9 | gözet | akış | olay | hata → diagnostics | §3 D4 |
| 10 | kayıp | olay | çekme niyeti | zincir (device R4) | KAPALI |
| 11 | durdur | istek | onay | — | şablon |
| 12 | bırak | — | serbest | — | şablon |
| 13 | kapat | — | kapı kapandı | — | şablon |

### §6.3 Durum geçiş örnek tablosu

| # | Durum | Olay | Durum | Not |
|---|---|---|---|---|
| 1 | KAPALI | kapı seçimi | ACILIYOR | R1 |
| 2 | ACILIYOR | pazarlık tamam | HAZIR | — |
| 3 | ACILIYOR | ret | HATA | asıl kod saklanır |
| 4 | HAZIR | başlat | CALISIYOR | — |
| 5 | CALISIYOR | hata | HATA | diagnostics |
| 6 | CALISIYOR | cihaz kaybı | CIHAZ_KAYBI | — |
| 7 | CIHAZ_KAYBI | sonraki kapı | FALLBACK | sıra device R4 ⚠️ |
| 8 | CIHAZ_KAYBI | kapı yok | DURDU | — |
| 9 | FALLBACK | açıldı | ACILIYOR | — |
| 10 | HATA | yeniden dene | ACILIYOR | — |
| 11 | CALISIYOR | durdur | KAPATILIYOR | — |
| 12 | KAPATILIYOR | serbest | KAPALI | — |
| 13 | DURDU | manuel tekrar | ACILIYOR | — |
| 14 | HAZIR | kapıyı kapat | KAPALI | akışsız kapatma |

## §7 IRP/akış örnek şablonları

> ⚠️ **VERIFICATION REQUIRED:** IRP (I/O Request Packet) akışı Windows WDK
> dokümanına dayanır; bu oturumda fetch edilmemiştir. Aşağıdaki şablonlar
> **kavramsal**tır; major function kodu gibi ayrıntılar bilinçli olarak
> yazılmamıştır (uydurma kod = zero-hallucination ihlali).

### §7.1 Kavramsal istek akışı örnek tablosu

| # | İstek | Yön | Beklenen yanıt | Hata örneği | Sahip |
|---|---|---|---|---|---|
| 1 | kapı aç | → sürücü | tutamaç | cihaz meşgul | driver |
| 2 | bilgi iste | → sürücü | nitelik | desteklenmiyor | driver |
| 3 | yapılandır (format) | → sürücü | kabul | ret + kod | driver |
| 4 | yapılandır (buffer) | → sürücü | kabul | ret + kod | driver + backend (R4) |
| 5 | yapılandır (kip) | → sürücü | kabul | kip reddi | driver (R1) |
| 6 | başlat | → sürücü | akış | açılamadı | driver |
| 7 | veri | ↔ sürücü | tampon | xrun | realtime |
| 8 | durum | ← sürücü | olay | — | diagnostics |
| 9 | hata | ← sürücü | kod | bilinmeyen kod | diagnostics |
| 10 | durdur | → sürücü | onay | — | driver |
| 11 | bırak | → sürücü | onay | — | driver |
| 12 | kapat | → sürücü | onay | — | driver |

### §7.2 Doğrulanmış aşama örnekleri (§2'den)

| # | Örnek | Adım | Ne gösterir | Durum |
|---|---|---|---|---|
| 1 | exclusive-mode-streams | kip kurulumu | iki kip var, kip kararı zorunlu | ✅ fetch |
| 2 | ALSA hw_params | format/rate/channels/buffer | önce donanım pazarlığı | ✅ fetch |
| 3 | ALSA sw_params | avail_min/period_event | sonra yazılım ayarı | ✅ fetch |
| 4 | ALSA beş access modu | MMAP/RW × interleaved | erişim kipi seçeneği | ✅ fetch |
| 5 | ALSA plug | format/channels/rate dönüşümü | uyuşmazlık sürücüde | ✅ fetch |
| 6 | PipeWire scheduling | driver node + sayaçlar | zamanlama sürücüde | ✅ fetch |
| 7 | ASIO SDK | açık kaynak ilanı | kapı adayı + lisans riski | ✅ fetch (§4 #3) |

### §7.3 Yazma/okuma örnek şablonu

| # | An | Playback | Capture | Hata örneği |
|---|---|---|---|---|
| 1 | tampon isteği | sürücü ister | sürücü verir | — |
| 2 | doldurma | engine zinciri | input kaynağı | kaynak yok (H13) |
| 3 | teslim | yaz | oku | sürücü ret |
| 4 | tik bitişi | sonraki tik | sonraki tik | tik kayması |
| 5 | eksik | xrun | xrun | H5 |
| 6 | kapatma | sustur → bırak | bırak | click/DC (H12 komşu) |

### §7.4 Hata yanıt örnekleri

| # | Durum | Üst katmana giden | Ne saklanmaz | Sınıf |
|---|---|---|---|---|
| 1 | ret | asıl kod + üst çeviri | asıl kod | H10 |
| 2 | bulunamadı | durum + device'a bildirim | — | H2 |
| 3 | kip reddi | kip bilgisi | — | H3 |
| 4 | çalışma hatası | kod + düğüm | — | H10 |
| 5 | kayıp | güvenli çekme niyeti | — | H1 |
| 6 | zaman aşımı | süre | — | H14 |
| 7 | bilinmeyen | kod aynen | kod yutulmaz | "bilinmeyen" |
| 8 | tekrar | sayaç | — | tekrar sayacı |

## §8 Sürücü ↔ OS sınır örnek tabloları

### §8.1 OS↔sürücü örnek

| # | İş | OS | driver | Örnek hata modu |
|---|---|---|---|---|
| 1 | sürücü yükleme | ✅ | ❌ | sürücü yok → H2 |
| 2 | kimlik verme | ✅ | okur | kimlik değişti → H4 |
| 3 | API sunma | ✅ | çağırır | API değişimi → sürüm (§9 #15) |
| 4 | hata üretme | ✅ | yorumlar | bilinmeyen kod → §7.4 satır 7 |
| 5 | izin/örtük kaynak | ✅ | ❌ | ret → H2 |
| 6 | zamanlama politikası | ✅ | niyet | düşük öncelik → H5 |
| 7 | güç olayı | ✅ | bildirim alır | uyku → kayıp (H1) |
| 8 | sürüm bilgisi | ✅ | envanter (device) | uyumsuz → H4 |
| 9 | register/firmware | ❌ | ❌ | K005 sınırı |
| 10 | kernel kodu | ❌ | ❌ | K000 · K005 |

### §8.2 motor↔sürücü örnek

| # | İş | engine | driver | Kilit |
|---|---|---|---|---|
| 1 | başlat niyeti | ✅ | yürütür | — |
| 2 | periyot hedefi | 512/48 (sartname) | pazarlık | R4/Q5 |
| 3 | buffer sahipliği | ⚠️ soru | sınır | R4 |
| 4 | hata okuma | okur | üretir | §2 satır 3 |
| 5 | fallback politikası | ⚠️ | yürütür | R1 + device |
| 6 | kip niyeti | ister | uygular | R1 |
| 7 | dönüşüm | istemez | yapabilir | §2 satır 4 |
| 8 | ölçüm | ❌ | ❌ | diagnostics |
| 9 | log | ❌ | ❌ | diagnostics |
| 10 | test eşiği | ❌ | ❌ | testing |

### §8.3 device↔driver örnek ayrım (Q6 girdisi, cevap değil)

| # | Soru | driver | device | Durum |
|---|---|---|---|---|
| 1 | hangi cihaz? | hayır | evet | açık (Q6) |
| 2 | nasıl konuşulur? | evet | hayır | açık (Q6) |
| 3 | hangi kapı? | evet | hayır | R1 |
| 4 | envanter alanları? | sağlar | listeler | R2 |
| 5 | kayıp sonrası sıra? | yürütür | değerlendirir | device R4 |
| 6 | kip bilgisi? | uygular | envanterler | R1 |

## §9 Yaygın hata modu örnekleri (negatif)

| # | Senaryo | Belirti | Aday neden | İlk tepki | Kapı |
|---|---|---|---|---|---|
| 1 | kapı açılamıyor | akış yok | meşgul/kip/hat | kodu sakla | R1 |
| 2 | format ret | pazarlık yok | küme uyuşmaz | plug/dönüşüm | — |
| 3 | anlık kayıp | ses kesildi | donanım/sürücü | çek + zincir | device R4 |
| 4 | dropout seli | kesinti | periyot uyumsuz | ölç | R2 |
| 5 | xrun seli | taşma | RT/CPU | ölç | R2 |
| 6 | kilitlenme | ikinci uygulama yok | exclusive | kip kararı | R1 |
| 7 | sürücü çökmesi | OS olayı | sürücü hatası | rapor | K000 komşu |
| 8 | yanlış kanal | hoparlör hatası | maske | routing | routing |
| 9 | gecikme artışı | bütçe | buffer büyüme | ölç | R5/diag |
| 10 | clock drift | klik | çift clock | sync | sync komşu |
| 11 | fallback döngüsü | kapılar dönüyor | zincir yok | zincir kararı | device R4 |
| 12 | bilinmeyen kod | teşhis yok | yeni sürüm | "bilinmeyen" sınıfı | R3 |
| 13 | yavaş açılış | L2/L4 uzun | enum yavaş | ölç | device R6 |
| 14 | kapanış patlaması | click | susturma yok | kapatma şablonu | mixing R6 |
| 15 | sürüm değişimi | bozulma | envanter bayat | yeniden tarama | R6 |
| 16 | çift yarış | T7 çakışma | sahiplik yok | F17 kontrol | R1 |

### §9.1 Negatif kural örnekleri (yapılmayacaklar)

| # | Yanlış | Doğru |
|---|---|---|
| 1 | "driver yalnız ASIO kullanır" | kapı seçimi R1 AÇIK |
| 2 | "fallback ASIO→WASAPI→shared'dır" | `.ai/AGENTS.md` §17 #6 tek kural; sıra device R4 |
| 3 | "IRP_MJ_… kodu şudur" | ⚠️ teyit edilmedi, yazılmaz |
| 4 | "gecikme 5 ms'dir" | R5 AÇIK — birim bile yok |
| 5 | "buffer backend'indir" | R4 AÇIK |
| 6 | "repo'da sürücü kodu var" | glob 0 isabet (§11) |
| 7 | "ASIO SDK 2.3.4 teyitli" | §4 #10 ⚠️ |
| 8 | "GNU GPLv3 lisansı uygundur" | R2 AÇIK |

## §10 Platform kapı karşılaştırma örnekleri (R1 girdisi)

| # | Aday kapı | Güçlü yan (kaynaklı) | Zayıf yan (kaynaklı) | Kapı |
|---|---|---|---|---|
| 1 | ASIO | düşük gecikme adayı (§4 #3) | lisans R2 ⚠️ · sürüm R3 ⚠️ | R1 |
| 2 | WASAPI exclusive | özel kip (§2 satır 1) | kip çakışması H3 | R1 |
| 3 | WASAPI shared | paylaşım | gecikme üst sınırı — sayı yok | R1 |
| 4 | ALSA (Linux) | aşama + plug (§2 satır 2/4) | — | hedef (sartname OS) |
| 5 | PipeWire (Linux) | node/link + sürücü node zamanlaması | — | hedef |
| 6 | ikisi birden | kapsama genişliği | iki kod yolu, iki test | R1 |
| 7 | tek kapı | sadelik | platform kaybı | R1 |

**Karar bu tabloya yazılmaz** — R1 sahibi embedded-engineer (kapsam §5).

## §11 Repo kod yüzeyi — okuma kaydı

| # | Yüzey | Aranan | Bulunan | Sonuç |
|---|---|---|---|---|
| 1 | kök `*.cpp`/`*.h` | sürücü katmanı kodu | 0 isabet (2026-10-12) | kod yok |
| 2 | `shared/src/` | ASIO/WASAPI/ALSA çağrısı | PHP katmanı | kod yok |
| 3 | `shared/tests/` | sürücü testi | PHP testleri | yok |
| 4 | `.github/workflows/` | Windows/ASIO işi | workflow dosyaları | yok |
| 5 | `AIOrchestrator.php` : 32/36/45 | "embedded/dsp-fw" eşlemesi | ajan yönlendirme | sürücü çağrısı DEĞİL |
| 6 | `ToolCalling.php` : 38 | "dsp" araç alanı | AI aracı | sürücü çağrısı DEĞİL |
| 7 | `.ai/AGENTS.md` §17 #6 | fallback kuralı | tek satır | ✅ iç kanıt |
| 8 | `audio-master.md` §3 satır 64 | çocuk satırı | "driver · …" | ✅ iç kanıt |
| 9 | `driver/index.md` satır 15 | açık soru | "ASIO mu WASAPI mi?" | ✅ iç kanıt |
| 10 | `backend/asio/` glob | 8 dosyalık katalog | 8 dosya (§4 #8) | ✅ ölçüm |

## §12 Komşu eşleme ve kapı listesi

### §12.1 Komşu eşleme

| # | Şablon | Komşu dosya | Pay |
|---|---|---|---|
| 1 | §6.1 kapı seçimi | `backend/asio/asio-anayasa.md` | ASIO derin-dalı |
| 2 | §6.1 fallback | `device-ornekler.md` §7 | zincir iskeleti |
| 3 | §7.1/§7.4 | `diagnostics-ornekler.md` §8 | sınıf atama |
| 4 | §7.3 kapatma | `mixing-ornekler.md` | click/DC niyeti |
| 5 | §8.2 buffer | `backend/backend-kapsam.md` | R4/Q5 sınırı |
| 6 | §9 xrun/dropout | `input-ornekler.md` · `output-ornekler.md` | üreten taraf |
| 7 | §9 clock | `synchronization-ornekler.md` | komşu |
| 8 | §10 ALSA/PipeWire | `driver-kapsam.md` §10 | aynı karşılaştırma |
| 9 | §11 ölçüm | `driver-dogrulama.md` | kanıt günlüğü |
| 10 | IRP teyidi | sonraki tur (WDK/MS) | ⚠️ açık |

### §12.2 Kapı listesi (hepsi KAPALI)

| # | Kapı | Soru | Durum |
|---|---|---|---|
| 1 | R1 | ASIO mu WASAPI mi? | KAPALI |
| 2 | R2 | GPLv3 etkisi | KAPALI |
| 3 | R3 | SDK 2.3.4 uyumu | KAPALI |
| 4 | R4 | buffer sahipliği | KAPALI |
| 5 | R5 | gecikme rapor birimi | KAPALI |
| 6 | R6 | NAudio+ASIO thread | KAPALI |
| 7 | IRP | WDK dokümanı fetch | KAPALI |
| 8 | ölçüm | sürücü gecikmesi ölçümü | KAPALI |
| 9 | zincir | fallback sırası (device R4) | KAPALI |
| 10 | repo | sürücü kodu oluşumu | KAPALI (kod yok) |

## §13 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §5 | §2 + §3 | iç kanıt |
| 2 | §6 | `driver-kapsam.md` §6 + §2 satır 2 | şablon + URL |
| 3 | §7.1 | — | ⚠️ kavramsal (teyit kapıda) |
| 4 | §7.2 | §2 satır 1–6 + §4 #3 | ✅ fetch edilmiş URL |
| 5 | §7.3 | pull kanıtı (engine §2 satır 1–3) | ✅ URL |
| 6 | §7.4 | kapsam §7.4 | şablon |
| 7 | §8 | kapsam §8 + §4 (master §3 3) | iç kanıt |
| 8 | §9 | kapsam §9 + `.ai/AGENTS.md` §17 #6 | iç kanıt |
| 9 | §9.1 | §5 R1–R6 + §4 #10 | açık soru |
| 10 | §10 | §2 satır 1–6 | ✅ fetch edilmiş URL |
| 11 | §11 | §1 ölçümü | ölçüm |
| 12 | §12 | kapsam §5 + komşu dosyalar | iç kanıt |
| 13 | §6.1 fallback ağacı | `.ai/AGENTS.md` §17 #6 | ✅ iç kanıt |
| 14 | 512/48 hedefi | sartname (ölçüm PLANNED) | hedef, ölçüm değil |
| 15 | ADR-017 float | iç kural | ✅ iç kanıt |

---

## §14 Kip etkisi örnek tabloları (R1 girdisi — karar değil)

> Bu örnekler §10 adaylarının **kip boyutunu** gösterir; "hangi kapı/kip"
> kararı R1'dedir ve burada verilmez.

| # | Örnek senaryo | Exclusive altındaki beklenti | Shared altındaki beklenti | Kapı |
|---|---|---|---|---|
| 1 | tek uygulama, özel cihaz | doğrudan erişim | — | R1 |
| 2 | iki uygulama aynı cihaz | ikincisi açamaz (§9 satır 6) | ikisi paylaşır | R1 |
| 3 | uyuşmayan format | pazarlık doğrudan | sistem düzeneği devrede (§7.2 satır 4) | R1 |
| 4 | cihaz kaybı | kurtarma şablonu (§6.1 L9–L11) | aynı şablon | — |
| 5 | kapanış | sustur → bırak (§7.3 satır 6) | aynı | — |

### §14.1 Negatif kip örnekleri (yapılmayacaklar)

| # | Yapılmayacak | Neden | Kaynak |
|---|---|---|---|
| 1 | "exclusive her zaman tercih" yazmak | R1 kapısı açık | kapsam §5 R1 |
| 2 | shared gecikmesine sayı atamak | ölçüm yok → UNKNOWN | kapsam §5 R5 |
| 3 | kilidi kimin kıracağını yazmak | sahiplik R1'e bağlı | kapsam §14 satır 8 |
| 4 | kip seçiminin sahibini driver göstermek | kapı kararı R1 (embedded-engineer) | kapsam §5 |

## §15 Buffer/periyot bağlama örnekleri (R4/Q5 girdisi)

| # | Örnek adım | Gösterilen | Gösterilmeyen | Durum |
|---|---|---|---|---|
| 1 | hedef ilanı | 512/48 (sartname) | hedefin gerçekleştiği iddiası | ölçüm PLANNED |
| 2 | destek kümesi okuma | sürücü sunar | hangi değerler | teyit kapıda |
| 3 | kesişim | hedef ∩ destek | gerçek kesişim içeriği | şablon |
| 4 | uzlaşma | yol adayları (kapsam §15.2) | hangi yolun seçildiği | R4/Q5 AÇIK |
| 5 | geri bildirim | bağlanan değer engine'e döner | değer ne? | şablon |

### §15.1 Uzlaşma senaryosu negatif örnekleri

| # | Senaryo | Yasak yazımla | Doğru yazımla |
|---|---|---|---|
| 1 | hedef desteklenmiyor | "256'ya düşülür" | "uzlaşma R4/Q5'e açık" |
| 2 | 48 kHz uyuşmuyor | "dönüşüm yapılır" | "dönüşüm/retyolları aday; karar R4" |
| 3 | exclusive dar küme | "exclusive kullanılır" | "R1 + R4 açık" |
| 4 | ölçüm | "gecikme X ms" | "ölçüm yok → UNKNOWN" |

## §16 Kurtarma (reconnect) örnek akışları

### §16.1 Örnek: anlık cihaz kaybı (§9 satır 3)

```text
D6 CIHAZ_KAYBI
  → kod saklanır (§7.4 satır 5)
    → çekme niyeti üst katmana bildirilir (kapsam §2 satır 4)
      → zincir sırası device R4'e AÇIK
        → yeniden açma D8 (§6.3 satır 10)
          → başarı → D3/D4 · başarısız → D10 (manuel)
```

### §16.2 Örnek: tekrarlayan hata (§9 satır 4/5)

| # | Adım | Gösterilen | Sayısal sınır | Durum |
|---|---|---|---|---|
| 1 | hata bildirimi | kod korunur | — | şablon |
| 2 | sayaç | tekrar sayısı tutulur | eşik YOK (uydurulmaz) | UNKNOWN |
| 3 | teşhis | diagnostics sınıfına gider | sınıf kodu yok | R3 KAPALI |
| 4 | eylem | ölç veya raporla | periyot yok | R2 KAPALI |
| 5 | zincir | sıradaki kapı | sıra device R4 ⚠️ | KAPALI |

### §16.3 Negatif kurtarma örnekleri

| # | Yapılmayacak | Neden |
|---|---|---|
| 1 | tekrar denme süresi yazmak | sayı uydurmak (UNKNOWN olmalı) |
| 2 | "3 deneme sonrası vazgeç" | eşik sayısı yok → UNKNOWN |
| 3 | fallback sırasını burada vermek | device R4 sahibi |
| 4 | kurtarma log'u bu dalda yazmak | kapsam §8.2 satır 10 — log diagnostics |

## §17 Taşma denetimi eki (§14–§16)

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §14 | kip kararı verildi mi? | ✅ HAYIR — yalnız beklenti karşılaştırması |
| 2 | §14.1 | "exclusive tercih" yazıldı mı? | ✅ HAYIR — negatif örnek olarak yasak |
| 3 | §15 | buffer sahibi atandı mı? | ✅ HAYIR — R4/Q5 açık |
| 4 | §15.1 | uzlaşma sonucu sayısal mı? | ✅ HAYIR — yasak sütunu |
| 5 | §16 | tekrar denme eşiği yazıldı mı? | ✅ HAYIR — UNKNOWN |
| 6 | §16.3 | log/fallback bu dalda mı? | ✅ HAYIR — sahiplerine devir |
| 7 | §14–§16 | §1–§4 değişikliği | ✅ hayır |
| 8 | genel | derlenebilir kod iddiası | ✅ yok (yalnız ```text akış) |
| 9 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |

## §18 Atıf tablosu (§14–§16)

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §14 | kapsam §14 + §10 adayları | iç kanıt |
| 2 | §14.1 | kapsam §5 R1 | açık soru |
| 3 | §15 | kapsam §15 + sartname 512/48 | şablon + hedef |
| 4 | §15.1 | kapsam §15.2 + R4/Q5 | açık soru |
| 5 | §16.1 | kapsam §16.1 + §9 satır 3 | şablon + iç kanıt |
| 6 | §16.2 | kapsam §9 satır 4/5 + §16.2 | şablon |
| 7 | §17 | kapsam §17 tekniği | iç kanıt |

### §18.1 Ek kapı sayımı (§14–§17 için)

| # | Kapı | Ek bölümde soruldu mu? | Durum |
|---|---|---|---|
| 1 | R1 (kapı/kip) | §14 yalnız beklenti gösterdi | KAPALI |
| 2 | R2 (GPLv3) | §14'e değinilmedi | KAPALI |
| 3 | R3 (SDK 2.3.4) | §14–§16'da yok | KAPALI |
| 4 | R4/Q5 (buffer sahipliği) | §15 aday gösterdi, karar yok | KAPALI |
| 5 | R5 (gecikme raporu) | §15.1 "UNKNOWN" bıraktı | KAPALI |
| 6 | R6 (NAudio thread) | §14–§16'da yok | KAPALI |
| 7 | tekrar denme eşiği | §16.2 "UNKNOWN" bıraktı | KAPALI |
| 8 | fallback sırası | §16.3 device R4'e devretti | KAPALI |
| 9 | log sahipliği | §16.3 diagnostics'e devretti | KAPALI |
| 10 | ölçüm (sürücü gecikmesi) | §15 "ölçüm yok" bıraktı | KAPALI |

**Toplam: 10/10 KAPALI** — ek bölümler §5'teki açık soruların sayısını
artırmamış, yalnız girdi tablolarını genişletmiştir (kapsam §17 ile aynı sonuç).

### §18.2 Devir notları (§14–§17'den sonraki tura)

| # | Devir maddesi | Bekleyen iş | Sahip |
|---|---|---|---|
| 1 | kip kararı (§14) | exclusive/shared + kapı seçimi | embedded-engineer (R1) |
| 2 | uzlaşma yolu (§15.1) | hedef desteklenmezse ne yapılır | Vault Steward (R4/Q5) |
| 3 | tekrar denme eşiği (§16.2) | sayısal sınır — elde ölçülmeden yazılmaz | realtime + diagnostics |
| 4 | IRP kod listesi (§7 teyidi) | WDK/MS doküman fetch | sonraki tur |
| 5 | sürücü gecikme ölçümü (§15 satır 7) | ölçüm metodu + ilk kayıt | diagnostics (R5) |

**Kural:** beş maddenin hiçbiri bu dosyada kapatılmaz; kapanışta ilgili
kapsam dosyasındaki R numarası güncellenir, Authority satırı değişmez.

---

## §19 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
