---
title: "mixing — Kapsam"
type: architecture-scope
node_id: K003-audio/mixing
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# mixing — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 8 bu dalı **"Çoklu kaynağı tek akışta birleştirme"**
olarak yazar. Basitçe: birden fazla ses kaynağının (müzik + bildirim sesi + mikrofon)
tek bir akışa **toplanması** — mikser masasının başına benzer: her kanalın
kolu ayrı ayrı, çıkan ses tek. Kritik sınır: bu dal **toplar** (ve gerekirse
kazancı uygular); **şekillendirmez** (EQ/reverb/dinamik = K004) ve **yönlendirmez**
(hangi hedefe gideceği = routing).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Kaç kaynağın birleşeceği, kazanç
> kipleri ve taşma (clipping) davranışı kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Çoklu kaynağın tek akışta birleştirilmesi (toplama) | Kaynakların nereye gideceği (eşleme) | routing (master §3 satır 7) |
| 2 | Birleştirme sırasında kazanç (gain) uygulama niyeti | Efekt/şekillendirme (EQ, dinamik, reverb) | K004-dsp (master §2.2) |
| 3 | Taşma/klip (clipping) güvenli davranışının niyeti | Taşmanın ölçümü ve kabul kriteri | diagnostics · testing |
| 4 | Karışımın zincire tek veri olarak verilmesi (pull-model noktası) | Zinciri yürüten döngü | engine (master §3 satır 1) |
| 5 | *(dışında)* kaynak okunması (dosya/mikrofon/akış) | Kaynak sahipliği | input (master §3 satır 5) |
| 6 | *(dışında)* karıştırılacak kaynakların kanal eşlemesi | Yönlendirme kuralları | routing (master §3 satır 7) |
| 7 | *(dışında)* karışım buffer'ının altyapısı | Bellek/kuyruk | backend (master §3 satır 4) |
| 8 | *(dışında)* kendi kendine dinleme (monitor) gecikmesi | Ölçüm ve rapor | diagnostics (master §3 satır 11) |

**Yasak:** satır 1'deki ayrım v2 §2.3'te ayrı çocuklar olduğu için **bağlayıcıdır**
(KURAL 1); mixing ile routing tek dosyada birleştirilemez. Satır 2'deki sınır
master §2.2'ye dayanır: DSP matematiği K003'ün döngüsüne **kopyalanmaz**.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| M1 | mixing ↔ routing | toplama (bir akış) ↔ eşleme (kaynak→hedef) | ✅ master §3 satır 7/8 (ayrı çocuklar) |
| M2 | mixing ↔ K004-dsp | birleştirme/şekillendirme | ✅ master §2.2 sınır kuralı |
| M3 | mixing ↔ input | gelen kaynaklar ↔ kaynağın toplanması | ⚠️ ad-türetme (master §3 satır 5 ve 8) |
| M4 | mixing ↔ engine | karışımı üretme ↔ döngüyü adımlama | ⚠️ ad-türetme (master §3 satır 8 ve 1) |
| M5 | mixing ↔ realtime | toplamanın kuralları (kaçırma yok) ↔ zaman baskısı | ⚠️ ad-türetme (master §3 satır 8 ve 9) |
| M6 | mixing ↔ diagnostics | taşma anının üretilmesi ↔ tespiti/sınıflandırması | ⚠️ üst karar bekliyor |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "mixing · Çoklu kaynağı tek akışta birleştirme · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 69 | ✅ dosya okundu |
| 2 | Sınır kuralı: DSP parametresi veri olarak taşınır, DSP hesabı K003'ün döngüsüne kopyalanmaz | `audio-master.md` §2.2 | ✅ dosya okundu |
| 3 | Bir efekt birimi DSP çekirdeğini `Process` metodunda tanımlar; `Reset` birimi temizler | `audio-master.md` §4 A5 (Apple: TheAudioUnit) | ✅ dosya okundu |
| 4 | PipeWire graph = nodes + ports + links — karışım da bir node'un işidir | `audio-master.md` §5 T17 → docs.pipewire.org objects_design | ✅ dosya okundu (URL fetch) |
| 5 | Repo'da karıştırma (mix) kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 6 | AIEngine'de ISO 31-band EQ tablosu var (bant→frekans) — K004 equalizer kanıtı, mixing DEĞİL | `shared/src/AI/AIEngine.php` satır 300–339 (bandToFrequency) | ✅ dosya okundu |
| 7 | Kaç kaynağın birleşeceği ve kazanç kipi (lineer/akışkan) | — | **UNKNOWN** (tasarım yok) |
| 8 | Taşma (clipping) eşiği ve davranışı (yumuşak mı sert mi) | — | **UNKNOWN** (kriter yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | mixing ↔ routing sınırında kazanç (gain) kimde? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R2 | Maksimum eşzamanlı kaynak sayısı (mix bus kapasitesi)? | **UNKNOWN** — tasarım yok | embedded-engineer |
| R3 | Taşma anında davranış nedir (limit mi, sessizlik mi)? | ⚠️ VERIFICATION REQUIRED (K004 limiter ile komşu) | embedded-engineer |
| R4 | mixing kazancı K004 compressor/limiter ile ilişkili mi (ortak gain sahipliği)? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R5 | Karışım dönüşümü hangi formatta taşınır (float mı, bit derinliği)? | **UNKNOWN** — kanıt yok | backend |
| R6 | Click-free gain değişimi nasıl sağlanır? | ⚠️ VERIFICATION REQUIRED | realtime |

**Kapanış:** status: draft kalır. R1/R4 kapanmadan "gain mixing'tedir" ya da
"K004'tedir" iddiası yazılmaz; R2/R5 için sayı/format uydurulmaz → UNKNOWN.

---

## §6 Birleştirme (toplama) şablonu (§2 satır 1)

> Bu bölüm **toplama şeklini** şablonlar; eşzamanlı kaynak sayısı (R2),
> kazanç sahipliği (R1/R4) ve taşma davranışı (R3) **açıktır**.

### §6.1 Toplama aşamaları

| # | Aşama | Ne olur | Bu dalın payı | Komşu payı | Durum |
|---|---|---|---|---|---|
| T1 | kaynak listesi | hangi kaynaklar? | toplama niyeti | input + routing (M3) | ⚠️ R2 |
| T2 | kanal eşleşmesi | kaynak→bus | — | routing (§2 satır 6) | DIŞINDA |
| T3 | kazanç uygulama | kolda/üstte? | niyet (§2 satır 2) | R1/R4 | ⚠️ R1/R4 |
| T4 | toplama | hepsi tek akışa | ✅ çekirdek iş | — | şablon |
| T5 | taşma kontrolü | sınıra dayanma | niyet (§2 satır 3) | R3 + diagnostics | ⚠️ R3 |
| T6 | biçime dönüştürme | dönüşüm | — | R5 (format) | ⚠️ R5 |
| T7 | zincire verme | pull yanıtı | §2 satır 4 | engine (M4) | şablon |
| T8 | ölçüm/rapor | taşma sayısı | — | diagnostics | DIŞINDA |

### §6.2 Toplama matematiği şablonu (kavramsal — kod değil)

> ⚠️ Aşağıdaki sembolik şekil **kavramsal anlatımdır**, derlenebilir kod
> **iddiası değildir**; hangi kazanç kipinin (lineer/akışkan) kullanılacağı
> §4 #7 UNKNOWN'dur.

```text
toplam = (kaynak1 × kazanç1) + (kaynak2 × kazanç2) + … + (kaynakN × kazançN)
  → N: UNKNOWN (R2 — tasarım yok)
  → kazanç kipi: UNKNOWN (§4 #7)
  → taşma sonrası: UNKNOWN (R3 — limit mi sessizlik mi?)
  → dönüşüm: UNKNOWN (R5 — float mu?)
```

**Yasak:** bu bloğa sayısal N, kazanç değeri, eşiği veya format adı yazılmaz.

### §6.3 Taşma senaryoları (üçü de AÇIK)

| # | Senaryo | Olası yol (aday) | Kapı | Durum |
|---|---|---|---|---|
| 1 | toplam sınırı aşıyor | limit mi, sessizlik mi? | R3 | AÇIK |
| 2 | tek kol sınırdan taşar | kol mu, bus mu? | R1/R4 | AÇIK |
| 3 | tekrarlayan taşma | sayaç → diagnostics | M6 | AÇIK (üst karar) |

**Kural:** üç senaryoda da **davranış kararı yazılmaz**; hepsi kapıdadır.

## §7 Kazanç (gain) şablonları — R1/R4 girdisi

### §7.1 Kazanç yerleşim seçenekleri (aday)

| # | Seçenek | Nerede uygulanır | Avantaj/zayıf (aday) | Kapı |
|---|---|---|---|---|
| 1 | kol kazancı | toplamadan önce | kaynağa özel | R1 |
| 2 | bus kazancısı | toplama sonrası | tek kontrol | R1 |
| 3 | K004'te (limiter/compressor) | efekt aşaması | işbirliği mi? | R4 |
| 4 | karışık (kol + bus) | ikisi | karmaşık | R1 + R4 |

**Bu tablo R1'e CEVAP DEĞİLDİR** — yalnız seçenek kümesini gösterir.

### §7.2 Kazanç değişimi (click-free) — R6 girdisi

| # | Adım | Ne olur | Kurallar sahibi | Durum |
|---|---|---|---|---|
| 1 | değişim isteği | UI/komut | — | — |
| 2 | geçiş nasıl? | anlık mı, yumuşak mı? | realtime (R6) | ⚠️ R6 |
| 3 | kaç tikte? | süre/örnek sayısı | — | **UYDURULMAZ** (UNKNOWN) |
| 4 | uygulama | kazanç değeri güncellenir | R1 sahipliği | R1 |
| 5 | tıklama (click) ölçümü | — | diagnostics | ölçüm yok |

### §7.3 Kazanç sahipliği matrisi (R1 × R4)

| # | Soru | mixing cevabı | routing cevabı | K004 cevabı | Etiket |
|---|---|---|---|---|---|
| 1 | gain kimde? | aday | hayır (eşleme) | aday (limiter) | R1 + R4 |
| 2 | kim değiştirir? | niyet | hayır | niyet | R1/R4 |
| 3 | kim ölçer? | hayır | hayır | hayır | diagnostics |
| 4 | taşmada kim karar? | niyet (§2 satır 3) | hayır | limiter komşu | R3 + R4 |
| 5 | monitor gecikmesi | hayır | hayır | hayır | diagnostics (§2 satır 8) |

## §8 Sınır tabloları (M1–M6 genişletmesi)

### §8.1 mixing ↔ routing (✅ bağlayıcı sınır — §2 satır 1)

| # | Soru | mixing | routing | Etiket |
|---|---|---|---|---|
| 1 | hangi hedefe? | hayır | evet | M1 |
| 2 | hangi kaynaklar toplanır? | toplar | eşler | M1 |
| 3 | gain kimde? | aday | hayır | R1 |
| 4 | kanal maskesi? | hayır | evet | routing |
| 5 | tek akış kime? | üretir | — | §2 satır 4 |

### §8.2 mixing ↔ K004-dsp (✅ sınır kuralı kanıtlı — §4 #2)

| # | Konu | mixing | K004 | Kural |
|---|---|---|---|---|
| 1 | toplama matematiği | ✅ | — | — |
| 2 | EQ/reverb/dinamik | ❌ | ✅ | DSP kopyalanmaz |
| 3 | limiter | niyet | iş | R3 + R4 |
| 4 | parametre | — | tanımlar (`Process` — §4 #3) | — |
| 5 | `Reset` (temizleme) | — | ✅ (§4 #3) | — |

### §8.3 mixing ↔ engine/input/realtime

| # | Sınır | mixing payı | Komşu payı | Etiket |
|---|---|---|---|---|
| 1 | M4 (engine) | karışımı verir | döngü adımlar | ⚠️ ad-türetme |
| 2 | M3 (input) | toplanan kaynak | kaynağı okur | ⚠️ ad-türetme |
| 3 | M5 (realtime) | kurallara uyar | kural koyar | R6 + realtime |
| 4 | M6 (diagnostics) | taşma olayı üretir | sınıflandırır | ⚠️ üst karar |
| 5 | §2 satır 7 (backend) | — | karışım buffer'ı | DIŞINDA |

## §9 Yaygın hata modları (failure modes)

| # | Hata modu | Belirti | Olası kök neden (aday) | İlk tepki | Sınıf (diagnostics) |
|---|---|---|---|---|---|
| 1 | taşma (clip) | sert/distorsiyon | toplam eşiği | niyet (R3) | H5/H7 |
| 2 | bir kanal sessiz | kaynak eksik | gain 0 / eşleme | routing | H6 |
| 3 | gürültü/burkulma | ses bozuk | kazanç aşımı | R4 | H4 |
| 4 | click (kazançta) | tıklama | anlık değişim | R6 | H12 |
| 5 | gecikme artışı | monitor gecikmesi | — | ölç | H8 (ölçüm yok) |
| 6 | faz/kanal kayması | mono/sol-sağ | eşleme | routing | H4 |
| 7 | kaynak yarışı | kesik | M3 sahiplik | R2 (input) | H3 |
| 8 | taşma sessizliği | ani kesilme | R3 davranışı | karar | H7 |
| 9 | format uyuşmazlığı | bozuk çıkış | R5 format | — | H4 |
| 10 | bus kapasitesi aşımı | bazı kaynaklar | R2 (N UNKNOWN) | — | H6 |
| 11 | K004 limiter çakışması | iki kez limit | R4 ortak sahiplik | R4 | H7 |
| 12 | kapanışta patlama | click/DC | susturma yok | realtime + output | H12 |
| 13 | karışım zincire gitmiyor | sessizlik | pull noktası (M4) | engine | H2 |
| 14 | ölçüm yokluğu | "görünmez" taşma | M6 üst karar | — | — |

### §9.1 Hata modu → açık soru eşlemesi

| # | Hata modu | Hangi R'ye girdi | Kapı |
|---|---|---|---|
| 1, 8, 11 | taşma/sessizlik/limiter | R3 + R4 | KAPALI |
| 3, 4 | kazanç aşımı/click | R1 + R6 | KAPALI |
| 2, 6, 13 | sessizlik/faz/pull | routing · engine | — |
| 5 | gecikme | diagnostics (ölçüm yok) | KAPALI |
| 7, 10 | yarış/kapasite | R2 (input bağlantılı) | KAPALI |
| 9 | format | R5 | KAPALI |
| 12 | kapanış | realtime + mixing R6 | KAPALI |
| 14 | ölçüm yokluğu | M6 üst karar | — |

## §10 Uygulama kapıları (hepsi KAPALI)

| # | Kapı | Soru | Sahip | Durum |
|---|---|---|---|---|
| 1 | R1 | gain sahipliği (routing sınırı) | Vault Steward | KAPALI |
| 2 | R2 | eşzamanlı kaynak sayısı (N) | embedded-engineer | KAPALI (UNKNOWN) |
| 3 | R3 | taşma davranışı | embedded-engineer | KAPALI |
| 4 | R4 | K004 ortak gain sahipliği | Vault Steward | KAPALI |
| 5 | R5 | dönüşüm formatı | backend | KAPALI (UNKNOWN) |
| 6 | R6 | click-free gain değişimi | realtime | KAPALI |
| 7 | ölçüm | taşma/geri sayım ölçümü | diagnostics | KAPALI (ölçüm yok) |
| 8 | monitor | monitor gecikme ölçümü | diagnostics | KAPALI (§2 satır 8) |
| 9 | kod | mix kodu oluşumu | — | KAPALI (glob 0) |
| 10 | M6 | taşma olayının sahibi | üst karar | KAPALI |

## §11 Kapsam dışı taşma denetimi

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §6.1 | aşama kararları verildi mi? | ✅ HAYIR — T3/T5/T6 kapıda |
| 2 | §6.2 | N/kazanç/eşik/format yazıldı mı? | ✅ HAYIR — dört UNKNOWN |
| 3 | §6.3 | taşma davranışı seçildi mi? | ✅ HAYIR — 3/3 AÇIK |
| 4 | §7.1 | kazanç yerleşimi seçildi mi? | ✅ HAYIR — 4 seçenek R1 |
| 5 | §7.2 | geçiş süresi yazıldı mı? | ✅ HAYIR — UNKNOWN |
| 6 | §7.3 | R1/R4 cevaplandı mı? | ✅ HAYIR — matris "aday" |
| 7 | §8.1–8.3 | sınır değiştirildi mi? | ✅ HAYIR — mevcut M1–M6 |
| 8 | §9 | kök neden kesin mi? | ✅ HAYIR — "aday" |
| 9 | §9 | sayısal eşik yazıldı mı? | ✅ HAYIR |
| 10 | §10 | kapı "geçti" mi? | ✅ HAYIR — 10/10 KAPALI |
| 11 | genel | §1–§5 değişikliği | ✅ hayır |
| 12 | genel | yasaklı yer tutucu / derlenebilir kod | ✅ yok (yalnız ```text kavramsal) |
| 13 | genel | EQ/dinamik kodu eklenmedi mi? | ✅ HAYIR — K004 sınırı korundu |

## §12 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §6.1 | §2 satır 1–4 + §5 R1/R2/R3 | iç kanıt + açık |
| 2 | §6.2 | §4 #7 UNKNOWN ×4 | bilinmeyen (kavramsal) |
| 3 | §6.3 | §4 #8 + §5 R3 | UNKNOWN + açık |
| 4 | §7.1 | §5 R1/R4 | açık soru |
| 5 | §7.2 | §5 R6 + ölçüm yokluğu | açık soru |
| 6 | §7.3 | §5 R1/R4 + §2 satır 3 | açık soru |
| 7 | §8.1 | §2 satır 1 + M1 | ✅ bağlayıcı sınır |
| 8 | §8.2 | §4 #2/#3 + M2 | ✅ fetch edilmiş kanıt |
| 9 | §8.3 | §3 M3–M6 | ad-türetme |
| 10 | §9 | §2 satır 3/8 + diagnostics H-şeması | komşu eşleme |
| 11 | §10 | §5 R1–R6 + §4 #5 | açık soru + ölçüm |
| 12 | §11 | §1–§5 | iç kanıt |
| 13 | §4 #4 | PipeWire graph (fetch) | ✅ URL |

## §13 Karışım bus (yol/toplama hattı) şablonu

> Bus topolojisi (kaç bus, send/return var mı) **kanıt değildir**; bu bölüm
> olası hat **yönünü** şablonlar. N (R2) ve format (R5) UNKNOWN'dur.

### §13.1 Basit tek hat (aday)

```text
kaynak1 ─[gain1?]─┐
kaynak2 ─[gain2?]─┼─→ TOPLA (T4) → TAŞMA NİYETİ (T5) → DÖNÜŞÜM (T6, R5) → zincir
kaynakN ─[gainN?]─┘
     N: UNKNOWN (R2) · gain sahipliği: R1/R4 · taşma: R3
```

### §13.2 Send/return yolu (VARLIĞI KANITLANMAMIŞ — opsiyonel aday)

| # | Terim | Olası işlev | Kanıt | Durum |
|---|---|---|---|---|
| 1 | send | yan yola kısmi kopya | — | ⚠️ kanıt yok |
| 2 | return | dönüş toplama | — | ⚠️ kanıt yok |
| 3 | efekt yolu | K004 işlemi | sınır §8.2 | komşu |
| 4 | toplama | ana buse geri | — | ⚠️ |

**Kural:** bu dört satır **"vardır" demez**; topoloji kanıtı yoktur → UNKNOWN.
K003'te send/return bulunup bulunmadığı üst karar/karar turu işidir.

### §13.3 Bus karşılaştırma tablosu (aday)

| # | Seçenek | Tarif (aday) | Avantaj (aday) | Zayıf (aday) | Kapı |
|---|---|---|---|---|---|
| 1 | tek bus | her şey tek toplama | sadelik | az kontrol | R2/R1 |
| 2 | birden fazla bus | gruplar ayrı toplanır | grup gain | eşleme artar | R2 + routing |
| 3 | bus + send | efekt yolu | esneklik | kanıt yok | §13.2 |

**Hiçbir seçenek seçilmez** — seçim R2 + R1'e bağlıdır.

## §14 Taşma ölçümü ve rapor şablonları (M6 girdisi)

### §14.1 Taşma olayı yaşam şeması (aday)

```text
toplam (T4) sınırı aşıyor (§9 satır 1)
  → taşma niyeti (§2 satır 3 — bu dal üretir)
    → R3 kapısı: limit mi, sessizlik mi? — KARAR YOK
      → olay diagnostics'e gider mi? (M6 — üst karar bekliyor)
        → ölçüm yok → sayaç/eşik yazılmaz (UNKNOWN)
```

### §14.2 Ölçüm kalemleri (hepsi AÇIK)

| # | Kalem | Değer | Sahip | Durum |
|---|---|---|---|---|
| 1 | taşma sayısı/saniye | — | diagnostics | AÇIK (yöntem yok) |
| 2 | tepe (peak) seviyesi | — | diagnostics | AÇIK |
| 3 | klip uzunluğu | — | diagnostics | AÇIK (eşik yok) |
| 4 | monitor gecikmesi | — | diagnostics (§2 satır 8) | AÇIK |
| 5 | kazanç değişimi click'i | — | realtime + diagnostics | AÇIK (R6) |
| 6 | bus kapasitesi kullanımı | — | embedded-engineer (R2) | AÇIK (N UNKNOWN) |
| 7 | karışım formatı doğrulaması | — | backend (R5) | AÇIK (UNKNOWN) |
| 8 | mix kodu yokluğu | glob 0 (§4 #5) | — | AÇIK (kanıt ölçümü) |

**Kural:** sekiz kalem de **boştur**; hiçbirine değer/uydurma konmaz.

### §14.3 Rapor şeması (kavramsal — biçim yazılmaz)

| # | Alan | Değer | Not |
|---|---|---|---|
| 1 | olay türü | "taşma" (aday) | sınıf kodu: R3/M6 açık |
| 2 | timestamp | — | — |
| 3 | kaynak | — | eşleme routing |
| 4 | süre/derinlik | — | UNKNOWN |
| 5 | eylem | — | R3 kararı |

## §15 Ölçüm kapıları (hepsi AÇIK — ölçüm yok)

| # | Kapı | Ne ölçülecek | Durum | Sahip |
|---|---|---|---|---|
| 1 | taşma ölçümü | §14.2 satır 1–3 | AÇIK | diagnostics |
| 2 | monitor gecikmesi | §14.2 satır 4 | AÇIK | diagnostics |
| 3 | click ölçümü | §14.2 satır 5 | AÇIK | realtime + diagnostics |
| 4 | bus kapasitesi | §14.2 satır 6 | AÇIK | embedded-engineer |
| 5 | format doğrulama | §14.2 satır 7 | AÇIK | backend |
| 6 | N sınır stresi | R2 (UNKNOWN) | AÇIK | embedded-engineer |
| 7 | taşma davranışı doğrulaması | R3 | AÇIK | embedded-engineer |
| 8 | kod oluşumu | §4 #5 glob 0 | AÇIK | — |

## §16 Komşu eşleme ve devir notları

### §16.1 Komşu eşleme

| # | Bu dosya konusu | Komşu dosya | Pay |
|---|---|---|---|
| 1 | kazanç (§7) | `routing/` | R1 birlikte |
| 2 | limiter (§8.2) | `../dsp/` (K004) | R3/R4 |
| 3 | toplama → zincir (§6.1 T7) | `engine/` | M4 |
| 4 | kaynaklar (§6.1 T1) | `input/` | M3 · R2 bağlantılı |
| 5 | taşma raporu (§14) | `diagnostics/` | M6 |
| 6 | click/kapanış (§7.2/§9 satır 12) | `realtime/` · `output/` | R6 + komşu |
| 7 | karışım buffer'ı | `backend/` | §2 satır 7 DIŞINDA |
| 8 | eşleme | `routing/` | §2 satır 6 DIŞINDA |
| 9 | ölçüm kabul | `testing/` | — |
| 10 | biçime dönüştürme | `output/` · backend | R5 |

### §16.2 Devir notları (sonraki tur)

| # | Devir | Bekleyen iş | Sahip |
|---|---|---|---|
| 1 | R1 | gain sahipliği (routing sınırı) | Vault Steward |
| 2 | R2 | N (kaynak sayısı) | embedded-engineer |
| 3 | R3 | taşma davranışı | embedded-engineer |
| 4 | R4 | K004 ortak gain | Vault Steward |
| 5 | R5 | dönüşüm formatı | backend |
| 6 | R6 | click-free değişim | realtime |
| 7 | M6 | taşma olayı sahibi | üst karar |
| 8 | §13.2 | send/return topolojisi kanıtı | araştır (kanıt yok) |

**Kural:** sekiz madde de bu dosyada kapatılmaz; kapanış ilgili R/M'de olur,
Authority satırı değişmez.

## §17 Taşma denetimi eki (§13–§16)

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §13.1 | bus topolojisi kesin mi? | ✅ HAYIR — "aday" + "?" |
| 2 | §13.2 | send/return "var" mı denmiş? | ✅ HAYIR — "kanıt yok" |
| 3 | §13.3 | seçenek seçildi mi? | ✅ HAYIR — R2/R1 kapıda |
| 4 | §14.1 | taşma kararı verildi mi? | ✅ HAYIR — R3 |
| 5 | §14.2 | ölçüm değerine sayı kondu mu? | ✅ HAYIR — 8 hücre boş |
| 6 | §14.3 | rapor biçimi/eşik yazıldı mı? | ✅ HAYIR — UNKNOWN |
| 7 | §15 | kapı "geçti" mi? | ✅ HAYIR — 8/8 AÇIK |
| 8 | §16 | komşuya karar/uydurma sahip mi? | ✅ HAYIR — §5 R sahipleri |
| 9 | §13–§16 | §1–§5 değişikliği | ✅ hayır |
| 10 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 11 | genel | derlenebilir kod iddiası | ✅ yok |
| 12 | genel | Authority tekliği | ✅ tek |

## §18 Atıf tablosu (§13–§16)

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §13.1 | §6.1 + §5 R1/R2/R5 | şablon + açık |
| 2 | §13.2 | kanıt yokluğu + §8.2 | bilinmeyen |
| 3 | §13.3 | §5 R1/R2 + §2 satır 6 | açık soru |
| 4 | §14.1 | §2 satır 3 + §5 R3 + M6 | iç kanıt + açık |
| 5 | §14.2 | ölçüm yokluğu + §4 #5 | ölçüm kapısı |
| 6 | §14.3 | M6 üst karar | şablon |
| 7 | §15 | §14.2 + §5 | ölçüm kapısı |
| 8 | §16.1 | §2 sınır satırları + §3 M'ler | iç kanıt |
| 9 | §16.2 | §5 R sahipleri | açık soru |

### §18.1 Ek kapı sayımı (§13–§18)

| # | Kapı | Ek bölümde soruldu mu? | Durum |
|---|---|---|---|
| 1 | R1 (gain sahipliği) | §7.1/§7.3 "aday" | KAPALI |
| 2 | R2 (N sayısı) | §13.1 "?" + §13.3 | KAPALI (UNKNOWN) |
| 3 | R3 (taşma davranışı) | §6.3 3/3 AÇIK + §14.1 | KAPALI |
| 4 | R4 (K004 ortak gain) | §7.3 matris | KAPALI |
| 5 | R5 (dönüşüm formatı) | §6.2 + §14.2 satır 7 | KAPALI (UNKNOWN) |
| 6 | R6 (click-free) | §7.2 süre UNKNOWN | KAPALI |
| 7 | M6 (taşma olayı sahibi) | §14.1 üst karar | KAPALI |
| 8 | §13.2 send/return | "kanıt yok" | KAPALI |
| 9 | ölçüm 8 kapı | §15 8/8 AÇIK | KAPALI |
| 10 | kod (glob 0) | §4 #5 | KAPALI |
| 11 | §14.2 ölçüm kalemleri | 8 hücre boş | KAPALI |
| 12 | §14.3 rapor şeması | 5 alan boş | KAPALI |
| 13 | §13.1 "?" hücreleri | gain/N/taşma/dönüşüm | KAPALI |

**Sonuç:** ek bölümler açık soru sayısını **artırmadı** (6 → 6); bus/taşma
konuları mevcut R1–R6 + M6'ya bağlandı, yeni soru açılmadı. §18.1'de 13
madde de KAPALI; §18.2'deki toplam bu 13 maddeyi de kapsar.

### §18.2 Kapı sayım özeti (dosya geneli)

| # | Kaynak | Toplam | Durum |
|---|---|---|---|
| 1 | §10 uygulama kapıları | 10 | 10/10 KAPALI |
| 2 | §11 taşma denetimi | 13 satır | temiz |
| 3 | §15 ölçüm kapıları | 8 | 8/8 AÇIK |
| 4 | §17 taşma denetimi eki | 12 satır | temiz |
| 5 | §5 R1–R6 + M6 | 7 | 7/7 açık (devir §16.2) |

**Toplam:** 7 açık soru + 10 uygulama kapısı KAPALI + 8 ölçüm kapısı AÇIK —
hiçbiri bu oturumda kapatılmadı.

### §18.3 Negatif örnek: kapanmış gibi görünen kapı

| # | Sahte ifade | Neden sahte | Doğru hâli |
|---|---|---|---|
| 1 | "gain mixing'te" | R1 açık | "aday yerleşim (§7.1)" |
| 2 | "K004'te limiter" | R4 açık | "komşu niyeti (§8.2)" |
| 3 | "16 kaynak desteklenir" | R2 UNKNOWN | "N UNKNOWN" |
| 4 | "taşmada limit uygulanır" | R3 açık | "3 seçenek (§6.3)" |
| 5 | "çıkış float" | R5 UNKNOWN | "format UNKNOWN" |
| 6 | "gain yumuşak değişir" | R6 açık | "geçiş süresi UNKNOWN" |
| 7 | "taşma ölçümü yapıldı" | 8/8 AÇIK | "ölçüm yok (§14.2)" |
| 8 | "send/return var" | §13.2 kanıt yok | "kanıt yok" |
| 9 | "bus topolojisi seçildi" | §13.3 seçim yok | "3 seçenek kapıda" |
| 10 | "kod yazıldı" | glob 0 | "kod yok" |
| 11 | "monitor gecikmesi düşük" | ölçülmüş ms yok | "UNKNOWN (§2 satır 8)" |
| 12 | "taşma olayı tanımlandı" | M6 üst karar | "sahipsiz (§14.1)" |

**Kural:** on iki ifadenin hiçbiri bu dosyada yazılmamıştır; hepsi
"yazılmaz" sütunundadır. Authority bu dosyada tektir.

---

## §19 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
