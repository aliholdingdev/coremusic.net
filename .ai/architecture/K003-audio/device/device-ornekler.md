---
title: "device — Örnekler"
type: architecture-examples
node_id: K003-audio/device
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: kod
---

# device — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `home.coremusic.net/` ve `.github/workflows/` içinde ses cihazı
envanteri/seçimi kodu bulunmaz (2026-10-12 envanter ölçümü). En yakın yüzey
`shared/src/AI/AIEngine.php` içindeki `ffmpeg` metadata çıkışıdır (satır
300–339) — bu **dosya** metadata'sıdır, **cihaz** metadata'sı değildir; device
düğümü kanıtı olarak **kullanılamaz**.

UI tarafındaki tek "device" çağrışımı donanım görünümüdür, ses cihazı seçimi
değildir: `home.coremusic.net/WidgetGridComponent.php` satır 35, 56 ve
`pages/components/widget-grid.php` satır 59–60 (widget grid / ekran kartı).

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | ALSA hw_params alanları | https://www.alsa-project.org/doc/api/group___p_c_m.html | `snd_pcm_hw_params` — bit hızı · örnekleme hızı · kanal sayısı · periodo · tampon · boşa çıkarma | Bir cihazın envanteri bu alanların **desteklediği** kümelerinden türetilir (cihaz seçiminin veri kaynağı) |
| 2 | ALSA `plug` dönüşümü | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | eksik kanal/format desteği yazılım katmanında çözülür | Seçim kararında "cihaz desteklemiyor → dönüştür" dalı zorunludur; kör seçim yapışma/bozulma üretir |
| 3 | PipeWire düğüm/port/link modeli | https://pipewire.org/page_objects_design | `graph.nodes` · `graph.ports` · `graph.links` | Cihaz bir **düğüm**tür; seçim = hangi düğümün portlarının bağlanacağı kararı |
| 4 | PipeWire driver.node | https://pipewire.org/page_latency | `sched.max = quantum` · `node.latency = buffer/quantum` | Cihazın zamanlama niteliği seçim kararına girer (yalnız ad/kip değil, quantum da) |
| 5 | Core Audio API kapsamı | https://developer.apple.com/documentation/coreaudio/core_audio_overview/core_audio_api_reference | sürücü · format · codec · mikrofon · hoparlör | Envanter alanlarının resmi referans listesi (Apple platformunda) |

### §2.1 Fetch durumu notu (şeffaflık)

Tablodaki 5 URL bu oturumda GET edilmiş ve içerik başlığı doğrulanmıştır —
bu yüzden `has-been-fetched 2026-10-12` taşırlar. `device-dogrulama.md`
#8–#9'daki WASAPI URL'leri ise bu turda fetch edilmediği için **bu dosyada
örnek satırı olarak kullanılmaz**; edilmemiş URL'ye "örnek" diye atıf
yapmak uydurma sayılır.

### §2.2 Örnek satırlarının iddia sınıfı

| Satır | İddia sınıfı | Dayanak |
|---|---|---|
| 1–2 | `[TASARIM]` öncesi şablon (kullanım yasağı değil, veri kaynağı tanımı) | doğrulanmış URL |
| 3–4 | `[TASARIM]` öncesi graph/zamanlama bağlamı | doğrulanmış URL |
| 5 | Apple platform envanter listesi | doğrulanmış URL |
| Hepsi | Kesin uygulama iddiası DEĞİL | `UNKNOWN` korunur |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **device tasarım kararı öncesi** kullanılır: 1–2, envanter
alanlarını ve "desteklemiyorsa dönüştür" dalını tanımlarken şablon; 3–4, cihazın
graph içindeki yeri ve zamanlama niteliği; 5, Apple platformunda envanter
kaynaklarının listesidir. Kesin uygulama (envanter alanları, hot-swap sırası,
seçim kuralı) `device-kapsam.md` §5 R1–R6 kapanmadan yazılmaz; süre/alan/liste
değeri uydurulmaz → `UNKNOWN`.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `device-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular) |
| `device-dogrulama.md` | Kanıt/doğrulama tablosu (✅ · ⚠️ · UNKNOWN) |
| `device/index.md` | Düğüm kaydı (ELI15 · açık soru Q6) |
| `../audio-master.md` | Üst anayasa §3 satır 2 · §4 · §8 Q6 |
| `../audio-bagimliliklar.md` | §2 upstream/downstream · §4 komşu tablosu |
| `../driver/driver-kapsam.md` | D1 sınırının karşı tarafı |

## §5 Örnek şablonların kullanım sınıfı

> Bu bölüm §1–§4'ün **yerine geçmez**; §1'deki "— yok" ve §2'deki 5 doğrulanmış
> örnek aynen geçerlidir. Eklenen şablonlar yalnızca bu dosyanın §2.2'deki
> "kesin uygulama iddiası DEĞİL" sınıfına tabidir.

| # | Şablon | Dayandığı §2 örneği | Kullanım sınıfı | Kullanım yasağı |
|---|---|---|---|---|
| 1 | §6 keşif akışları | satır 1 (ALSA hw_params) · satır 3 (PipeWire node) | [TASARIM] öncesi iskelet | "CoreMusic bu adımları uygular" denmez |
| 2 | §7 hot-plug şablonları | satır 3 (graph link) · §4 ilgili dosyalar | [TASARIM] öncesi iskelet | fallback sırası karar sayılmaz (R4 açık) |
| 3 | §8 format eşlemeleri | satır 1 (bit/hız/kanal kümeleri) | [TASARIM] öncesi veri şeması | alan değerleri uydurulmaz (R2 açık) |
| 4 | §9 akış şablonları | satır 2 (plug dönüşümü) | [TASARIM] öncesi aşama dizisi | çağrı adı/API yazılmaz |
| 5 | §10 repo kaydı | §1 ölçümü (2026-10-12) | tekrar okuma (kanıt korunur) | yeni ölçüm iddiası eklenmez |
| 6 | §11 kontrol şablonları | §3 uygulama notu | kapı listesi | kapı "geçti" denmez |
| 7 | Hepsi | — | desteleyici | §1/§2 satırları değiştirilemez |

## §6 Cihaz profil keşfi örnek akışları

### §6.1 Açılış taraması (ilk enumerasyon)

```text
Uygulama açıldı
  └─ İlk tarama (E1 tetikleyicisi — §device-kapsam §6.3 T1)
       ├─ Bağlı cihaz listesi okundu (E2)
       │    ├─ Liste boş → ENVYOK durumu + kullanıcıya bildirim (arayüz katmanı)
       │    └─ Liste dolu → nitelikler okunur (E3)
       ├─ Nitelik kümesi uyuşmazlığı → dönüştürme yolları (§9.4)
       ├─ Tercih listesi ile kesişim (E5)
       │    ├─ Tercih var → E6 karar
       │    └─ Tercih yok → platform varsayılanı denenir (F8 adayı)
       └─ Karar sonrası durum: SECILI (akış henüz açık değil)
```

### §6.2 Olaya bağlı tarama (tak/çıkar)

```text
Cihaz takıldı/çıkarıldı (E1)
  ├─ Çıkarılan, seçili cihaz DEĞİL → envanter güncelle, karar korunur
  └─ Çıkarılan, seçili cihaz → device-kapsam §7 akışı:
       ├─ S6 CIHAZ_KAYBI
       ├─ S7 zincir değerlendirmesi (sıra AÇIK — R4)
       └─ S8/S9 geri düşme ya da duruş + diagnostics olayı
Takılan cihaz seçili değilse bile envanter yeniden okunur (E3) ve
kullanıcı tercihi yeniden değerlendirilir (E5) — otomatik geçiş YOKTUR
(geçiş kararı bu dosyada verilmez).
```

### §6.3 Keşif adım tablosu (örnek satır şeması)

| # | Adım | Girdi | Çıktı | Hata durumunda | Sahip |
|---|---|---|---|---|---|
| K1 | Enumerasyon çağrısı | — | cihaz kimliği listesi | liste boş → ENVYOK | device |
| K2 | Kimlik doğrulama | kimlik | geçerli/geçersiz | geçersiz → atla + rapor | device |
| K3 | Nitelik okuma | kimlik | format/kanal/kip kümesi | okunamadı → cihaz "bilinmiyor" işaretlenir | device |
| K4 | Yön sınıflandırma | nitelik | giriş/çıkış/çift | belirsiz → cihaz listede kalır, seçilemez | device |
| K5 | Tercih eşleşmesi | envanter + tercih listesi | uygun adaylar | tercih yok → F8 denemesi | device |
| K6 | Kıyas | adaylar | sıralı aday | hiç uygun yok → GERI_DUSME/RED | device |
| K7 | Karar | sıralı aday | seçilen cihaz | karar verilemiyorsa DURDU | device |
| K8 | Niyetin iletilmesi | seçim | routing/driver'a niyet | — | sınır (§4 D-komşuları) |
| K9 | Doğrulama | niyet | sağlık bildirimi | başarısız → diagnostics | diagnostics |
| K10 | Kayıt | tüm adımlar | envanter olay kaydı | — | diagnostics |
| K11 | Tazeleme | tetikleyici (§6.3 T-tablomuz device-kapsam §6.3) | yeni envanter | hata → yeniden dene | device |
| K12 | Kapatma | — | karar korunur, akış kapanır | — | device + driver |

### §6.4 Negatif örnekler (kanıt sayılmayan davranışlar)

| # | Yanlış örnek | Neden yanlış | Doğru sınıflandırma |
|---|---|---|---|
| N1 | "Envanter 12 alan taşır" | alan listesi kanıtsız | §5 R2 UNKNOWN |
| N2 | "Tarama 500 ms'de bir koşar" | sıklık değeri yok | §5 R6 UNKNOWN |
| N3 | "Takılan cihaz otomatik seçilir" | otomatik seçim kararı yok | şablon §6.2 bunu yapmaz |
| N4 | "Envanter UI'da listelenir" | gösterim kapsam dışı (§2 satır 8) | uygulama katmanı |
| N5 | "Cihaz kimliği endpoint ID'dir" | platform bağımlı, teyitsiz | ⚠️ VERIFICATION REQUIRED |
| N6 | "Seçim RT thread'de yapılır" | thread modeli yok | §5 R6 UNKNOWN |
| N7 | "WAVEFORMATEX alanları doğrulanmıştır" | MS sayfası fetch edilmedi | §9 ⚠️ (device-kapsam) |
| N8 | "Fallback zinciri ASIO→WASAPI→yok" | zincir sırası açık | §5 R4 ⚠️ |

## §7 Hot-plug ve cihaz değişimi örnek şablonları

### §7.1 Akış açıkken cihaz değişimi (şablon)

```text
Durum: AKTIF (akış açık, cihaz X)
  Kullanıcı cihaz değiştirdi / cihaz X düştü
    ├─ 1) Yazma güvenli biçimde durdurulur (niyet — sınır: realtime/driver)
    ├─ 2) Çıkış susturulur / DC-klik koruması (niyet — koruma kuralı)
    ├─ 3) Buffer davranışı seçilir: B1–B5 adaylarından biri (R3 AÇIK)
    ├─ 4) Cihaz kararı yeniden verilir (E5 → E6)
    ├─ 5) Yeni cihazla format pazarlığı (§8.4/Aşama P3)
    └─ 6) Akış yeniden başlatılır + olay diagnostics'e bildirilir
  Adım 3'te B-adayı seçilmediyse akış AÇILMAZ — kapı: R3 (KAPALI)
```

### §7.2 Değişim şablonu adım tablosu

| # | Adım | Girdi | Adım çıktısı | Kapı | Durum |
|---|---|---|---|---|---|
| 1 | Değişim nedeni sınıflandırma | olay | kullanıcı isteği / kayıp / ekleme | — | şablon |
| 2 | Eski cihazla vedalaşma | akış | durdurma niyeti | realtime/driver sınırı | şablon |
| 3 | Buffer davranışı seçimi | B1–B5 | seçilen aday | R3 | KAPALI |
| 4 | Envanter tazeleme | E1/E2 | yeni liste | R6 | KAPALI |
| 5 | Karar | E5/E6 | yeni cihaz | R5 (çoklu çıkış) | KAPALI |
| 6 | Format pazarlığı | nitelik kümesi | uygun format | §9.4 | şablon |
| 7 | Akış başlatma | hedef | akış | driver/routing | SINIR |
| 8 | Sağlık doğrulama | akış | olay | diagnostics | şablon |
| 9 | Geri alma | başarısızlık | eski cihaza dönüş? | R4 | KAPALI |

### §7.3 Hot-plug örnek durum geçiş tablosu

| # | Durum | Olay | Durum (sonuç) | Not |
|---|---|---|---|---|
| 1 | ENVYOK | cihaz takıldı | TARAMA | ilk olay |
| 2 | TARAMA | nitelik okundu | ENVANTER | — |
| 3 | ENVANTER | tercih eşleşti | SECILI | otomatik değil, karar ile |
| 4 | SECILI | akış açıldı | AKTIF | driver/routing |
| 5 | AKTIF | seçili cihaz düştü | CIHAZ_KAYBI | sürücü bildirimi |
| 6 | CIHAZ_KAYBI | zincir değerlendirildi | GERI_DUSME veya DURDU | sıra ⚠️ R4 |
| 7 | AKTIF | kullanıcı seçim değiştirdi | CIHAZ_KAYBI akışı | §7.1 |
| 8 | DURDU | cihaz geri geldi | ENVANTER | yeniden karar |
| 9 | GERI_DUSME | akış açıldı | AKTIF | yeni cihaz |
| 10 | SECILI | cihaz düştü (akış kapalı) | ENVANTER | daha ucuz yol |
| 11 | AKTIF | alakasız cihaz düştü | AKTIF | envanter güncellenir |
| 12 | ENVANTER | hepsi çıkarıldı | ENVYOK | — |

## §8 WAVEFORMATEX eşleme örnek şablonları

> ⚠️ **VERIFICATION REQUIRED:** bu şablonlar `device-kapsam.md` §9'daki gibi
> model hafızası ürünüdür; resmi Microsoft dokümanı bu oturumda fetch
> **edilmemiştir**. Şablonlar yalnız eşleme *düzenini* gösterir; alan adı/etiket
> değeri kullanılmadan önce teyit edilecektir.

### §8.1 Nitelik → envanter eşleme şablonu

| # | Nitelik kaynağı | Envanter alanına etki (aday) | Eşleme kuralı (şablon) | Etiket |
|---|---|---|---|---|
| 1 | örnek hızı kümesi | F5 | küme olarak saklanır, tek değer seçilmez | ⚠️ teyit |
| 2 | kanal sayısı aralığı | F4 | asgari–azami aralık | ⚠️ teyit |
| 3 | format/bit kümesi | F6 | her format ayrı satır | ⚠️ teyit |
| 4 | kip desteği | F7 | exclusive/shared bayrakları | ⚠️ teyit |
| 5 | cihaz adı | F2 | görünen metin | ⚠️ teyit |
| 6 | yön | F3 | giriş/çıkış/çift | ⚠️ teyit |
| 7 | sürücü bilgisi | F10 | sürücü kimliği+ürüm | ⚠️ teyit |
| 8 | sahiplik | F17 | başka uygulama kullanıyor mu | ⚠️ teyit |

### §8.2 Seçim → format kararı eşleme şablonu

| # | Seçim sonucu | Format kararı | Dönüştürme | Sahip |
|---|---|---|---|---|
| 1 | cihaz destekliyor | doğrudan istenen format | gerekmez | driver |
| 2 | cihaz kısmen destekliyor | en yakın desteklenen | sürücüde dönüşüm (ALSA plug kanıtı) | driver |
| 3 | hız uyuşmuyor | cihazın hızına geç | resampler (K004 sınırı) | driver/K004 |
| 4 | kanal uyuşmuyor | kanal maskesi eşle | routing eşlemesi | routing |
| 5 | bit uyuşmuyor | iç kipe dön (ADR-017 float) | dönüşüm | driver |
| 6 | desteklemiyor | cihaz reddedilir | — | device (E5) |
| 7 | kip uyuşmuyor | shared'a düş | kip düşürme | driver R1 ⚠️ |

### §8.3 8.1 hedef eşleme şablonu

| # | Slot | Hedef kanal (sartname) | Cihaz desteklemiyorsa | Sahip |
|---|---|---|---|---|
| 1 | Ön sol | 8.1 düzeninin bir parçası | maske kırpılır ya da cihaz reddedilir | routing · K001 |
| 2 | Ön sağ | " | " | routing · K001 |
| 3 | Merkez | " | " | routing · K001 |
| 4 | Subwoofer (LFE) | " | " | routing · K001 |
| 5 | Surround sol | " | " | routing · K001 |
| 6 | Surround sağ | " | " | routing · K001 |
| 7 | Arka sol | " | " | routing · K001 |
| 8 | Arka sağ | " | " | routing · K001 |
| 9 | Toplam | 8 kanal (8.1 — sartname) | — | HDEF |
| 10 | Sıra numarası | slot sırası ≠ fiziksel sıra | maske kararlaştırılır | ⚠️ eşleme kararı yok |

**Dürüstlük notu:** slot satırları bir *yerleşim taslağı*dır; hangi slotun hangi
OS kanalına denk geldiği **kararlaştırılmamıştır** (routing + K001 komşu kararı).

## §9 Cihaz başına akış şablonu örnekleri

### §9.1 Playback örnek adımları

| # | Adım | Koşul | Hata dalı |
|---|---|---|---|
| 1 | hedef seç | §6.3 K7 | yok → DURDU |
| 2 | format pazarla | §8.2 | uyuşmaz → dönüştür/red |
| 3 | 512/48 hedefini ilet | sartname (ölçüm PLANNED) | sürücü kabul etmez → alternatif |
| 4 | buffer bağla | backend sınırı | — |
| 5 | akış başlat | driver | açılamadı → zincir (R4 ⚠️) |
| 6 | gözle | dropout/olay | olay → diagnostics |
| 7 | kapat | — | sustur → kapat |

### §9.2 Capture örnek adımları

| # | Adım | Koşul | Hata dalı |
|---|---|---|---|
| 1 | kaynak cihazı seç | §6.3 K7 (giriş yönü) | giriş değil → RED |
| 2 | yön doğrula | F3 | belirsiz → seçilemez |
| 3 | format pazarla | §8.2 | uyuşmaz → dönüşüm |
| 4 | kanal/kip kararı | input sınırı (§4 I-ler) | açık → şablonda bırakılır |
| 5 | akış başlat | driver | açılamadı → bildirim |
| 6 | xrun niyeti gözle | taşma bildirimi | → diagnostics sınıflandırır |
| 7 | kapat | — | — |

### §9.3 Duplex örnek kontrol tablosu

| # | Kontrol | Evet | Hayır |
|---|---|---|---|
| 1 | giriş/çıkış aynı cihaz mı | ortak clock değerlendir | ayrı clock → synchronization |
| 2 | iki yön de 512/48 kabul ediyor mu | devam | dönüşüm kararı (§8.2) |
| 3 | toplam gecikme bütçesi var mı | devam | UNKNOWN — ölçüm yok |
| 4 | bir yön düşerse diğeri | davranış kararı | R4 ile kilitli |
| 5 | kapatma sırası | tasarım yok | UNKNOWN |
| 6 | kayıt başlatma | capture + playback eşzamanlı | — |

## §10 Repo kod yüzeyi — okuma kaydı (kanıt korunur)

| # | Yüzey | Ne arandı | Bulunan | Sonuç |
|---|---|---|---|---|
| 1 | `shared/src/` | cihaz envanteri/seçimi | AI/PHP katmanı | device kanıtı yok |
| 2 | `shared/tests/` | cihaz testi | PHP testleri | device kanıtı yok |
| 3 | `.github/workflows/` | cihaz/CI ses adımı | workflow dosyaları | device kanıtı yok |
| 4 | kök `*.cpp`/`*.h` | herhangi bir ses kodu | 0 isabet (2026-10-12) | C++ yüzeyi yok |
| 5 | `AIEngine.php` satır 300–339 | metadata çıkışı | ffmpeg metadata | dosya ≠ cihaz metadata'sı |
| 6 | `WidgetGridComponent.php` satır 35/56 | "device" çağrışımı | donanım görünümü | ses cihazı seçimi değil |
| 7 | `pages/components/widget-grid.php` satır 59–60 | "device" | widget grid kartı | ses cihazı seçimi değil |
| 8 | `.ai/AGENTS.md` §17 #6 | fallback kuralı | kural satırı | iç kanıt (tek kural) |
| 9 | `audio-master.md` §3 satır 63 | çocuk satırı | "device · Cihaz envanteri…" | iç kanıt |
| 10 | `device/index.md` §1 | ELI15 | tanım | iç kanıt |

**Not:** bu tablo §1'in ölçümünü (2026-10-12) **tekrarlar**, yeni ölçüm
iddiası **eklemez**; yeniden ölçüm yapılmadı.

## §11 Uygulama kontrol şablonları (kapı listesi — 0 geçiş)

| # | Şablon | Kontrol sorusu | Kapı durumu |
|---|---|---|---|
| 1 | §6 keşif | envanter şeması kanıtlandı mı? | KAPALI (R2) |
| 2 | §6.3 K-adımları | adım sırası mimari onaylandı mı? | KAPALI (Q6) |
| 3 | §7.1 | buffer davranışı seçildi mi? | KAPALI (R3) |
| 4 | §7.1 adım 6 | fallback sırası kararlaştı mı? | KAPALI (R4) |
| 5 | §7.3 | durum kümesi onaylandı mı? | KAPALI (R3/Q7) |
| 6 | §8.1 | MS format dokümanı fetch edildi mi? | KAPALI (⚠️) |
| 7 | §8.3 | 8.1 slot sırası kararlaştırıldı mı? | KAPALI (routing/K001) |
| 8 | §9.1 adım 3 | 512/48 ölçüldü mü? | KAPALI (ölçüm PLANNED) |
| 9 | §9.3 | duplex bütçesi ölçüldü mü? | KAPALI (ölçüm yok) |
| 10 | §10 | repo kodu oluştu mu? | KAPALI (kod yok) |

---

## §12 Komşu örnek dosyalarıyla eşleme (kim neyi alır?)

| # | Bu dosyadaki şablon | Komşu dosya | Komşunun payı | Çakışma riski |
|---|---|---|---|---|
| 1 | §6.1 açılış taraması | `driver-ornekler.md` | enumerasyonun sürücüden gelen tarafı | D1 sınırı açık |
| 2 | §6.1 nitelik okuma | `input-ornekler.md` · `output-ornekler.md` | okuma/yazma akışının kendi şablonu | Q6 açık |
| 3 | §6.2 olaya bağlı tarama | `diagnostics-ornekler.md` | olayın sınıflandırılması | §4 D7 |
| 4 | §6.3 K-adımları | `routing-ornekler.md` | K8 niyetinin bağlanması | D4 |
| 5 | §7.1 değişim şablonu | `realtime-ornekler.md` | durdurma/başlatma kuralları | komşu açık |
| 6 | §7.1 adım 2 (susturma/DC-klik) | `mixing-ornekler.md` | gain/koruma niyeti | mixing R6 |
| 7 | §7.1 adım 3 (B1–B5) | `backend-asio/asio-hot-swap-ve-device-loss.md` | ASIO özel hot-swap yüzeyi | ⚠️ komşu derin-dal |
| 8 | §7.2/§7.3 durum geçişleri | `engine-ornekler.md` | yaşam döngüsü durum kümesi (Q7) | iki dosyada ayrı soru |
| 9 | §8.1 nitelik eşlemesi | `driver-ornekler.md` (hw_params) | kümelerin okunması | doğrulanmış şablon |
| 10 | §8.2 dönüşüm satırları | `driver-ornekler.md` (plug) | sürücüde dönüşüm | doğrulanmış şablon |
| 11 | §8.3 8.1 slot şablonu | `routing-ornekler.md` + K001 | kanal maskesi kararı | karar yok |
| 12 | §9.1 playback | `output-ornekler.md` | çıkışa yazma | Q6 açık |
| 13 | §9.2 capture | `input-ornekler.md` | kaynaktan okuma | Q6 açık |
| 14 | §9.3 duplex | `synchronization-ornekler.md` | ortak clock | komşu açık |
| 15 | §10 repo kaydı | `diagnostics-ornekler.md` | ölçüm tekrarı | — |
| 16 | §11 kapı listesi | `device-dogrulama.md` | kapıların kanıt tablosu | — |
| 17 | Fallback iskeleti (§7.1 adım 6) | `.ai/AGENTS.md` §17 #6 | tek kural satırı | zincir sırası açık |
| 18 | exclusive/shared | `backend/asio/asio-wasapi-karsilastirma.md` | kip karşılaştırması derin-dalı | komşu derin-dal |
| 19 | envanter alanı adayları | `device-kapsam.md` §6.2 | aynı aday listenin kaynağı | tek kaynak |
| 20 | ölçüm beklentileri | `device-dogrulama.md` ölçüm günlüğü | gerçek satırlar append-only | ölçüm yok |

## §13 Hot-plug senaryo matrisi (senaryo → şablon → kapı)

| # | Senaryo | Tetikleyici | Uygulanan şablon | Sonuç (şablon) | Kapı |
|---|---|---|---|---|---|
| 1 | Açılışta hiç cihaz yok | T1 | §6.1 | ENVYOK + bildirim | — |
| 2 | Açılışta tek cihaz | T1 | §6.1 → §6.3 K7 | SECILI | R2/R5 |
| 3 | Açılışta üç cihaz, tercih var | T1 | §6.3 K5–K7 | tercih edilen seçilir | R5 |
| 4 | Açılışta üç cihaz, tercih yok | T1 | §6.3 K5 → F8 | varsayılan denemesi | R2/R5 |
| 5 | Çalarken tak-çıkar (alakasız) | T2 | §6.2 | AKTIF korunur | R6 |
| 6 | Çalarken seçili cihaz çıkar | T2 | §7.1 | CIHAZ_KAYBI → zincir | R3/R4 |
| 7 | Çalarken seçili cihaz düşer | sürücü hatası | §7.1 + §7.2 | zincir değerlendirilir | R4 |
| 8 | Zincirde sonraki kapı açılabilir | §7.1 adım 6 | GERI_DUSME → AKTIF | R4 |
| 9 | Zincir tükendi | §7.1 | DURDU + rapor | R4 |
| 10 | Çalarken kullanıcı seçim değiştirir | T3 | §7.1 (değişim) | yeni cihazla AKTIF | R3 |
| 11 | Kapalıyken cihaz değişir | T2/T3 | §7.2 adım 4–5 | SECILI | R5 |
| 12 | Düşen cihaz geri gelir | E1 | §7.3 satır 8 | ENVANTER → yeniden karar | — |
| 13 | İki uygulama aynı cihazı ister | T7 | §8.4 / §6.2 F17 | sahiplik çakışması | R1 (driver) ⚠️ |
| 14 | Cihaz 8.1 desteklemiyor | §6.3 K3 | §8.3 satır 10 → §8.2 satır 6 | cihaz reddedilir | eşleme kararı |
| 15 | Cihaz yalnız 44.1 kHz destekliyor | §6.3 K3 | §8.2 satır 2 | sürücüde dönüşüm | komşu (driver) |
| 16 | Exclusive açılamıyor | §8.4 | shared'a düşüş | kip kararı | R1 ⚠️ |
| 17 | Sürücü sürümü değişir | T6 | §6.3 K1–K3 yeniden | envanter tazelenir | R6 |
| 18 | Uygulama yeniden başlatılır | T1 | §6.1 | ilk tarama | — |
| 19 | Kip/endpoint değişimi (OS ayarı) | T6/T8 | §6.3 K11 | karar yeniden | R6 |
| 20 | Ölçüm oturumu (donanım bağlanır) | test | §9 + dogrulama günlüğü | ilk gerçek satır | ölçüm PLANNED |
| 21 | Fallback ölçümü | test | §7.1 iskeleti | R4'e girdi | R4 ⚠️ |
| 22 | Hot-plug ölçümü | test | §7.2 adım süreleri | R3/R6'ya girdi | R3/R6 |
| 23 | Envanter doğrulama | test | §6.3 K-adımları | R2'ye girdi | R2 |
| 24 | 8.1 kipi doğrulama | test | §8.3 | eşleme kararına girdi | routing/K001 |
| 25 | DC/klik doğrulama | test | §7.1 adım 2 | koruma kanıtı | ölçüm PLANNED |

## §14 Sık sorular (bu dosyanın cevaplamadıkları dâhil)

| # | Soru | Bu dosyadaki cevap | Dayanak |
|---|---|---|---|
| 1 | Cihaz seçimi bu dosyada mı? | Karar evet; uygulama hayır | §2 satır 2 |
| 2 | Envanter kaç alan taşır? | Bilinmiyor — 18 aday var, kanıt yok | §6.2 · §5 R2 |
| 3 | Hot-plug'ta buffer ne olur? | 5 aday var, karar yok | §7.3 · R3 |
| 4 | Fallback sırası nedir? | İskelet var, sıra yok | §7.4 · R4 |
| 5 | WAVEFORMATEX doğrulandı mı? | Hayır — MS sayfası fetch edilmedi | §8 ⚠️ |
| 6 | 8.1 slot sırası belli mi? | Hayır — yerleşim taslağı | §8.3 · routing/K001 |
| 7 | Tarama hangi thread'te? | Cevap yok | R6 |
| 8 | Repo'da cihaz kodu var mı? | Yok — 0 isabet (2026-10-12) | §1 · §10 |
| 9 | Örnekler "çalışır kod" mu? | Hayır — akış/tablo şablonları | §5 kullanım sınıfı |
| 10 | Ölçüm yapıldı mı? | Hayır — kapılar KAPALI | §11 |
| 11 | exclusive/shared hangisi? | Karar driver R1'de | §8.4 · driver-kapsam §5 |
| 12 | Dosya neyi değiştirdi? | Yalnız §5–§14 eklendi | §1–§4 aynen korundu |

---

## §15 Eklenen bölümlerin atıf tablosu (hangi bölüm hangi kanıta dayanır)

| # | Bölüm | Dayandığı orijinal kanıt | Kanıt sınıfı | Ek kanıt var mı? |
|---|---|---|---|---|
| 1 | §5 | §2 tablosu (5 URL) + §3 uygulama notu | doğrulanmış URL | hayır — aynı kanıt |
| 2 | §6.1 | `device-kapsam.md` §6.1 keşif aşamaları | şablon (tasarım öncesi) | hayır |
| 3 | §6.1 E2/E3 | `device-ornekler.md` §2 satır 1 (ALSA hw_params alanları) | doğrulanmış URL | evet — aynı dosyada |
| 4 | §6.1 E7/E8/E9 | `device-kapsam.md` §2 satır 2/6/8 (içinde/dışında) | iç kanıt | — |
| 5 | §6.2 | `device-kapsam.md` §5 R2 (UNKNOWN) | açık soru | hayır |
| 6 | §6.3 | `device-kapsam.md` §6.3 tetikleyiciler | şablon | hayır |
| 7 | §7 (tümü) | `device-kapsam.md` §7 durum şeması + §5 R3/R4 | açık soru + şablon | hayır |
| 8 | §7.1 (B1–B5) | `device-kapsam.md` §7.3 adaylar | aday (UNKNOWN) | hayır |
| 9 | §8.1 | ALSA hw_params kanıtı (§2 satır 1) | doğrulanmış URL | — |
| 10 | §8.2 | ALSA plug kanıtı (§2 satır 2) | doğrulanmış URL | — |
| 11 | §8.3 | PipeWire node modeli (§2 satır 3) | doğrulanmış URL | — |
| 12 | §9 (tümü) | — | model hafızası | ⚠️ VERIFICATION REQUIRED |
| 13 | §10 | §1 repo ölçümü + §4 dosya atıfları | ölçüm + iç kanıt | hayır — tekrar |
| 14 | §11 | `device-kapsam.md` §11 kapıları | kapı listesi | — |
| 15 | §12 | `device-kapsam.md` §3 D-tablosu + komşu dosyalar | iç kanıt | — |
| 16 | §13 | §6–§9 şablonlarının senaryolara uygulanması | şablon sentezi | hayır |
| 17 | §14 | §5 açık sorular R1–R6 | açık soru | hayır |
| 18 | §16 (aşağıda) | `diagnostics-kapsam.md` §5 R2 · koruma kuralı | komşu açık soru | hayır |

### §15.1 Bu dosyada geçen doğrulanmış URL envanteri

| # | URL | Hangi bölümde kullanıldı | Fetch durumu |
|---|---|---|---|
| 1 | alsa-project.org pcm (hw_params) | §2 satır 1 · §6.1 · §8.1 · §9.1 | ✅ fetch 2026-10-12 |
| 2 | alsa-project.org pcm_plugins (plug) | §2 satır 2 · §8.2 · §9.1 | ✅ fetch 2026-10-12 |
| 3 | pipewire.org page_objects_design | §2 satır 3 · §6.1 · §8.3 | ✅ fetch 2026-10-12 |
| 4 | pipewire.org page_latency | §2 satır 4 | ✅ fetch 2026-10-12 |
| 5 | developer.apple.com Core Audio API reference | §2 satır 5 | ✅ fetch 2026-10-12 |
| 6 | learn.microsoft.com WAVEFORMATEX | §8–§9 | ⚠️ fetch EDİLMEDİ — teyit kapıda |
| 7 | learn.microsoft.com exclusive-mode-streams | §8.4 (device-kapsam §9.4 aktarımı) | ⚠️ bu oturumda fetch edilmedi |

**Kural:** §15.1 satır 6–7'deki URL'ler bu dosyada "örnek" satırı olarak
**kanıt satırı** yapılmaz; yalnız şablon notu olarak geçer (§2.1 prensibi).

## §16 Ölçüm ve telemetri örnek şablonları (diagnostics'e devir)

> Bu şablonlar `diagnostics-kapsam.md` §5 R2 (metrik listesi/periyot) **açık**
> iken **boş iskelet** olarak yazılır: satırlar var, değer hücreleri yoktur.

### §16.1 Sayaç şablonu (ad alanları — değer yok)

| # | Sayaç adı (alan) | Ne saydığı | Değer | Sahip |
|---|---|---|---|---|
| 1 | envanter tarama sayısı | kaç kez tarama yapıldı | — (ölçülmedi) | device → diagnostics |
| 2 | cihaz ekleme olayı | takılan cihaz sayısı | — | device → diagnostics |
| 3 | cihaz çıkarma olayı | çıkarılan cihaz sayısı | — | device → diagnostics |
| 4 | cihaz kaybı olayı | düşen seçili cihaz | — | driver → diagnostics |
| 5 | fallback denemesi | zincirde denenen kapı | — | device → diagnostics |
| 6 | fallback başarısı | açılan kapı | — | device → diagnostics |
| 7 | karar süresi örneği | E5→E6 geçen süre | — | device → diagnostics |
| 8 | tarama süresi örneği | E2+E3 geçen süre | — | device → diagnostics |
| 9 | kip dağılımı | exclusive/shared kullanımları | — | driver → diagnostics |
| 10 | 8.1 kip olayı | hedef kipe geçiş | — | routing → diagnostics |
| 11 | dönüşüm tetiklenmesi | §8.2 satır 2/3/5 tetikleri | — | driver → diagnostics |
| 12 | sahiplik çakışması | §6.2 F17 çakışmaları | — | driver → diagnostics |

### §16.2 Olay kaydı alan şablonu (log satırı alanları)

| # | Alan | Zorunlu mu | Not |
|---|---|---|---|
| 1 | olay zamanı | evet | biçim kararı diagnostics'te (R1 açık) |
| 2 | olay tipi (cihaz ekleme/kayıp/karar/çakışma) | evet | sınıf şeması R3 (diagnostics) |
| 3 | cihaz kimliği | evet | F1 adayı — kimlik biçimi teyitsiz |
| 4 | önceki cihaz / sonraki cihaz | değişimde | — |
| 5 | karar sonucu | karar olayında | — |
| 6 | kapı/başarı durumu | fallback olayında | sıra R4 açık |
| 7 | süre değeri | süre olaylarında | ölçülmedi |
| 8 | hata kodu | hata olayunda | sınıf şeması açık |
| 9 | hassas alan maskesi | her zaman | `.ai/AGENTS.md` §17 #3 ([REDACTED]) |
| 10 | oturum/kaynak | her zaman | — |

### §16.3 Ölçüm senaryosu şablonları (hiçbiri çalıştırılmadı)

| # | Senaryo | Ne ölçülür | Hangi soruya girdi | Durum |
|---|---|---|---|---|
| 1 | tek cihaz tak-çıkar ×N | tepki süresi dağılımı | R6 | PLANNED |
| 2 | seçili cihaz çekme | kayıp→yeni kapı süresi | R4 | PLANNED |
| 3 | 3 cihaz + tercih listesi | karar doğruluğu | R5 | PLANNED |
| 4 | envanter ↔ gerçek karşılaştırma | alan doğruluğu | R2 | PLANNED |
| 5 | 44.1 kHz cihaz | dönüşüm tetiklenmesi | §8.2 | PLANNED |
| 6 | 8.1 desteklemeyen cihaz | reddetme davranışı | eşleme kararı | PLANNED |
| 7 | exclusive çakışması | sahiplik davranışı | driver R1 | PLANNED |
| 8 | sıcak değiştirme B1–B5 | klik/DC olayı | R3 | PLANNED |
| 9 | sürücü sürüm değişimi | envanter tazelenmesi | R6 | PLANNED |
| 10 | OS ayarıyla endpoint değişimi | karar yenileme | R6 | PLANNED |
| 11 | duplex giriş+çıkış | toplam gecikme bütçesi | R4 (bütçe) | PLANNED |
| 12 | çoklu çıkış | seçim kuralı davranışı | R5 | PLANNED |
| 13 | uygulama çakışması (iki host) | sahiplik çakışması | driver R1 | PLANNED |

---

## §17 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
