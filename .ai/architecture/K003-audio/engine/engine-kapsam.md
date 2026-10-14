---
title: "engine — Kapsam"
type: architecture-scope
node_id: K003-audio/engine
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# engine — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 1 bu dalı **"Zinciri çalıştıran ana döngü ve yaşam
döngüsü"** olarak yazar. Basitçe: oynatmayı başlatan, durduran ve her tik'te
zinciri bir adım ilerleten motorun kalbi — diğer K003 çocukları bu düğümden
gelen komutla çalışır.

`engine/index.md` aynı anlamı ELI15 olarak verir: "Ses motorunun çekirdeği:
oynatma zincirini başlatan, durduran ve döndüren ana döngü." Bu kayıt iki
kaynaktan tek anlam çıkarır: **komut + ana döngü + yaşam döngüsü**.

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (master §3 dipnotu); içerik iddiası değildir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Oynatma zincirini başlatma/durdurma/devirme (yaşam döngüsü) | Buffer, kuyruk ve depolama altyapısı | backend (master §3 satır 4) |
| 2 | Ana döngünün adımlama sahipliği (kim zinciri bir sonraki tik'e taşır) | Döngünün uyması gereken lock-free/zaman kuralları | realtime (master §3 satır 9) |
| 3 | Zincirin uçtan uca yürütme akışı (kaynak → processing → çıkış) | DSP hesabının kendisi (EQ/reverb/crossover) | K004-dsp (master §2.2) |
| 4 | Motor komutlarının yorumu (oynat/duraklat/ileri-al) | Komutların UI'dan gelişi | uygulama katmanı (master §2.2) |
| 5 | Cihaz/sürücüye **komut** verme niyeti | Cihaz seçimi kararı ve sürücü çevirimi | device · driver (master §3 satır 2-3) |
| 6 | Zincir grafiğinin kurulması ve düğüm eklenmesi (AUGraph benzeri yaşam) | Grafiğin neden böyle kurulduğuna dair DSP kararı | routing · K004-dsp (master §3 satır 7) |
| 7 | *(dışında)* motor telemetrisinin toplanması ve sınıflandırılması | Log/ölçüm/hata sınıflandırma biçimi | diagnostics (master §3 satır 11) |
| 8 | *(dışında)* motorun doğrulanması (auval benzeri araç) | Kabul kriterleri ve test senaryoları | testing (master §3 satır 12) |

**Yasak:** satır 2'deki ayrım, Q5 kararı alınana kadar **sınır önerisi** değildir;
backend ile aynı döngü/kuyruk iki yerde tanımlanırsa `audio-bagimliliklar.md`
§2 U-05 riski (iki yerde aynı döngü/queue) gerçekleşir.

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| E1 | engine ↔ backend | yürütme sahipliği (döngü) ↔ altyapı (buffer/kuyruk) | ⚠️ master §8 Q5 — **VERIFICATION REQUIRED** |
| E2 | engine ↔ realtime | "ne çalıştırılır" ↔ "hangi kuralla çalıştırılır" | ⚠️ tahmin YASAK — ad-türetme |
| E3 | engine ↔ K004-dsp | zinciri yürütme ↔ sinyali şekillendirme | ✅ master §2.2 sınır kuralı (sinyal gider, kod girmez) |
| E4 | engine ↔ driver | komut niyeti ↔ sürücü konuşması | ⚠️ ad-türetme (master §3 satır 1 ve 3) |
| E5 | engine ↔ diagnostics | döngü olayının üretilmesi ↔ toplanması | ⚠️ üst karar bekliyor |
| E6 | engine ↔ synchronization | adımın zamanı ↔ clock/epoch uyumu | ⚠️ ad-türetme (master §3 satır 9-10) |
| E7 | engine ↔ device | "çalıştır" niyeti ↔ "hangi cihaz" kararı | ⚠️ Q6 ile bağlantılı (master §8 Q6) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "engine · Zinciri çalıştıran ana döngü ve yaşam döngüsü · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 62 | ✅ dosya okundu |
| 2 | ELI15: "ana döngü… diğer çocuklar buradan gelen komutla çalışır" | `engine/index.md` §1 | ✅ dosya okundu |
| 3 | Açık soru: "engine ile backend arasındaki sınır nedir? (K003 Q5)" | `audio-master.md` §8 Q5 · `engine/index.md` open_questions | ✅ dosya okundu |
| 4 | Q7: "CoreMusic'in kendi yaşam döngüsü durum kümesi nedir?" | `audio-master.md` §8 Q7 | ✅ dosya okundu |
| 5 | Sınır kuralı: "K003 içinden K004'e sinyal gönderilir, içine sokulmaz" | `audio-master.md` §2.2 | ✅ dosya okundu |
| 6 | K003 engine kodu (C/C++) bu turda repo'da YOK | kök `*.cpp`/`*.h` glob — 0 isabet (ölçüm 2026-10-12) | ✅ shell ölçümü |
| 7 | Hedef framework JUCE 9 (kurulu değil — hedef) | `.ai/AGENTS.md` §4 embedded satırı | ✅ iç kanıt [TASARIM] |
| 8 | Referans yaşam döngüsü: create → addNode → initialize → start | `audio-master.md` §4 A3 (fetch 2026-10-12) | ✅ kanıt-lı |
| 9 | `AudioUnitRender` sample-time monotonluğu (sonraki = önceki + frames) | `audio-master.md` §4 A4 (fetch 2026-10-12) | ✅ kanıt-lı |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | engine ↔ backend sınırı nedir? (K003 Q5 — pull model'de veri sahipliği) | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R2 | Ana döngü hangi thread/süreç önceliğinde yaşayacak? | **UNKNOWN** — kanıt yok | embedded-engineer |
| R3 | Yaşam döngüsü durum kümesi (state machine) nedir? (AAudio 5 durum şablon alınır mı?) | ⚠️ VERIFICATION REQUIRED (master Q7) | embedded-engineer |
| R4 | Zincir grafiği hangi API ile kurulacak (K004 düğümleri nasıl eklenir)? | ⚠️ VERIFICATION REQUIRED | embedded-engineer |
| R5 | Durum geçişleri asenkron mu, senkron mu (AAudio geçiş durumları)? | **UNKNOWN** — tasarım kararı yok | embedded-engineer |
| R6 | Zincirin uçtan uca yürütme akışında sıralama sahipliği kime ait? | ⚠️ VERIFICATION REQUIRED (R1 ile kilitli) | Vault Steward |

**Kapanış:** status: draft kalır. R1 kapanmadan bu dosyada "motor döngüsü
backend'den ayrıdır" iddiası yazılmaz (v2 §3 KURAL 3). R2/R5 için sayısal
değer (ms, öncelik) uydurulmaz → UNKNOWN.

---

## §6 Yaşam döngüsü şablonu (komut + ana döngü + zincir)

> Bu bölüm §1'deki üç kelimeyi (komut · ana döngü · yaşam döngüsü) **şablon
> tablolarına** açar; durum kümesi R3 ile, geçişlerin senkron/asenkron oluşu
> R5 ile açıktır — burada **kararlaştırılmaz**. Referans yaşam döngüsü
> doğrulanmış kaynaktır (§4 #8: create → addNode → initialize → start).

### §6.1 Yaşam döngüsü aşamaları (aday — kapı bağımsız)

| # | Aşama | Ne olur | Bu dalın payı | Komşu payı | Durum |
|---|---|---|---|---|---|
| L1 | oluştur (create) | motor nesnesi doğar | ✅ referans §4 #8 | — | ✅ kanıt-lı |
| L2 | düğüm ekle (addNode) | zincir grafiği kurulur | ✅ yürütme | K004/routing kararı (R4) | ⚠️ R4 |
| L3 | başlat (initialize) | yapılandırma kilitlenir | ✅ | backend buffer (R1) | ✅ adım + ⚠️ sahiplik |
| L4 | çalıştır (start) | ana döngü tik'leri başlar | ✅ | realtime kurallar (E2) | ⚠️ R2/R5 |
| L5 | tik adımı | zincir bir adım ilerler | ✅ adımlama sahipliği (§2 satır 2) | DSP hesap (K004) | şablon |
| L6 | duraklat | adımlama askıya alınır | ✅ (§2 satır 4) | — | şablon |
| L7 | ileri/geri sarma | konum değişir | ✅ | depolama backend | şablon |
| L8 | durdur (stop) | tik'ler biter | ✅ | — | şablon |
| L9 | yok et | nesne serbest | ✅ | — | şablon |
| L10 | hola dönüş | hatada L2/L3'e | ✅ | diagnostics olayı (E5) | ⚠️ R3 |

### §6.2 Durum şeması (aday — küme R3'e AÇIK)

> ⚠️ AAudio 5 durum şablonunun alınıp alınmadığı master Q7 (R3) ile açıktır;
> aşağıdaki küme **adaydır ve bağlayıcı değildir**.

| # | Durum | Tanım | Giriş | Çıkış | Etiket |
|---|---|---|---|---|---|
| S1 | OLUSTURULDU | L1 tamam | — | L2 | şablon |
| S2 | KURULUYOR | L2 sürüyor | L1 | L3 | şablon |
| S3 | HAZIR | L3 tamam, tik yok | L2 | L4 | şablon |
| S4 | CALISIYOR | L5 tik'leri aktif | L3 | L5/L6/L8 | şablon |
| S5 | DURAKLATILDI | L6 | L4 | L4/L8 | şablon |
| S6 | HATA | adım/aşama hatası | L5 | L10 | ⚠️ E5 |
| S7 | DURDU | L8 | S4/S5 | S9 | şablon |
| S8 | YENIDEN_KURULUYOR | L10 | S6 | S2/S3 | ⚠️ R3 |
| S9 | YOK_EDILDI | L9 | S7 | — | şablon |

### §6.3 Geçiş tablosu (aday — senkron/asenkron R5 ile AÇIK)

| # | Durum | Koşul | Hedef | Not |
|---|---|---|---|---|
| 1 | S1 | düğüm ekleme | S2 | R4 (grafik API) |
| 2 | S2 | kurulum tamam | S3 | — |
| 3 | S2 | kurulum hatası | S6 | diagnostics (E5) |
| 4 | S3 | start komutu | S4 | — |
| 5 | S4 | pause komutu | S5 | — |
| 6 | S5 | resume komutu | S4 | — |
| 7 | S4 | stop komutu | S7 | — |
| 8 | S4 | adım hatası | S6 | — |
| 9 | S6 | yeniden kurulum | S8 | — |
| 10 | S8 | başarı | S3 | — |
| 11 | S8 | başarısız | S6 | — |
| 12 | S7 | destroy | S9 | — |
| 13 | S5 | stop komutu | S7 | — |
| 14 | S6 | kullanıcı stop | S7 | — |

**R5 uyarısı:** bu geçişlerin **kim tarafından** (senkron komut mu, asenkron
olay mı) tetikleneceği UNKNOWN — tablo yalnız olası yönü gösterir.

## §7 Komut yorumu ve zincir yürütme akışları

### §7.1 Motor komutları (kaynak: §2 satır 4)

| # | Komut | Etki | Sınırdaş dal | Durum |
|---|---|---|---|---|
| 1 | oynat | L4/L5'e geçiş | uygulama katmanı (komut gelişi dışarıda) | şablon |
| 2 | dur | L8 | — | şablon |
| 3 | duraklat/devam | L6 ↔ L4 | — | şablon |
| 4 | ileri-al | L7 konum değişimi | backend depolama | şablon |
| 5 | geri-al | L7 | backend | şablon |
| 6 | zincire düğüm ekle | L2 (R4) | K004 · routing | ⚠️ R4 |
| 7 | cihazda çalıştır | niyet → device/driver | device · driver | ⚠️ E4/Q6 |
| 8 | kip/format değiştir | L3'e geri dönüş mü? | driver (R1) | ⚠️ R5 |

### §7.2 Uçtan uca yürütme akışı (§2 satır 3 — sıra sahipliği R6 ile AÇIK)

| # | Adım | Ne taşınır | DSP girdisi mi? | Sınır |
|---|---|---|---|---|
| 1 | kaynak okuma | ham akış | hayır | backend/input |
| 2 | giriş hazırlığı | çerçeve | — | engine |
| 3 | K004 işlemi | sinyal | ✅ evet | **sinyal gider, kod girmez** (§4 #5) |
| 4 | toplama/mix | kanal katkısı | hayır | mixing |
| 5 | çıkış yazımı | çerçeve | — | engine → driver |
| 6 | tik bitişi | monoton sayaç | — | §4 #9 (sample-time monoton) |

### §7.3 Tik adımı şablonu (L5)

| # | İç | Dış | Kural | Durum |
|---|---|---|---|---|
| 1 | adım sayacı ilerler | — | monoton (§4 #9) | ✅ kanıt-lı |
| 2 | komut kuyruğu okunur | uygulama komutu | kuyruk sahipliği R1 ⚠️ | R1 |
| 3 | zincir yürütülür | K004 sinyali | sınır §4 #5 | şablon |
| 4 | olay üretilir | diagnostics | toplama E5 ⚠️ | E5 |
| 5 | hata durur mu? | — | politika yok → UNKNOWN | ⚠️ |

## §8 Sınır tabloları (E1–E7 genişletmesi)

### §8.1 engine ↔ backend (Q5 girdisi — cevap değil)

| # | Soru | engine cevabı | backend cevabı | Etiket |
|---|---|---|---|---|
| 1 | döngüyü kim kurar? | adımlar (§2 satır 2) | altyapı mı? | ⚠️ R1 (Q5) |
| 2 | buffer'ı kim allocate eder? | hayır (kural: baştan allocate) | ⚠️ | R1 |
| 3 | kuyruk kimde? | komut kuyruğu okur | veri kuyruğu? | ⚠️ R1 |
| 4 | tik kimin zamanında? | adım atar | — | R2 ⚠️ |
| 5 | kim durdurur? | stop komutu | — | şablon |

### §8.2 engine ↔ realtime (E2 — kurallar dışarıda)

| # | Konu | engine | realtime | Kilit |
|---|---|---|---|---|
| 1 | lock-free kuralı | uyar | tanımlar | E2 |
| 2 | sıfır tahsis kuralı | uyar | tanımlar | E2 |
| 3 | öncelik | — | OS/plan | R2 UNKNOWN |
| 4 | zaman aşımı davranışı | — | kurallar | ⚠️ |
| 5 | callback noexcept | uyar | kural | E2 |

### §8.3 engine ↔ K004-dsp (✅ sınır kuralı kanıtlı)

| # | Konu | engine | K004 | Kural |
|---|---|---|---|---|
| 1 | sinyal verisi | taşır | işler | sinyal gider |
| 2 | DSP kodu | içermez | içerir | kod girmez (§4 #5) |
| 3 | düğüm parametresi | iletir | uygular | — |
| 4 | hesap hatası | bildirir | üretir | E5 |
| 5 | grafik topolojisi | kurar (R4) | düğüm tanımı | ⚠️ R4 |

## §9 Yaygın hata modları (failure modes)

| # | Hata modu | Belirti | Olası kök neden (aday) | İlk tepki | Sınıf (diagnostics) |
|---|---|---|---|---|---|
| 1 | start başarısız | ses yok | L3/L4 aşaması | kod sakla | H2/H3 |
| 2 | tik gelmiyor | akış durdu | thread/plan (R2 ⚠️) | bildir | H5 |
| 3 | tik düzensiz | click/drift | zamanlama | ölç | H6/H11 |
| 4 | komut yutuluyor | UI tepkisiz | kuyruk sahipliği (R1) | — | H7 |
| 5 | zincir koptu | ses kesik | düğüm/parametre | rapor | H6 |
| 6 | duraklat takıldı | resume çalışmıyor | durum kümesi (R3) | — | H3 |
| 7 | yeniden kurulum döngüsü | S8 ↔ S6 | hata kökü | rapor | H9 |
| 8 | çıkışta taşma | xrun | adım süresi | ölç | H5 |
| 9 | cihaz komutu reddi | ses çıkmıyor | driver/device | zincir | H1/H3 |
| 10 | bellek ihlali | çökme | sıfır-tahsis ihlali | — | H10 |
| 11 | DSP hatası yayılır | bozuk ses | K004 kökü | sınır (§8.3) | H4 |
| 12 | clock uyuşmazlığı | çift clock | synchronization | komşu | H11 |
| 13 | tek uygulama yarışı | exclusive çakışma | driver R1 | komşu | H3 |
| 14 | kapanışta patlama | click/DC | susturma yok | mixing/realtime | H12 |

### §9.1 Hata modu → açık soru eşlemesi

| # | Hata modu | Hangi R'ye girdi | Kapı |
|---|---|---|---|
| 1, 6 | start/duraklat | R3 (durum kümesi) | KAPALI |
| 2, 3, 8 | tik/ölçüm | R2 (thread/süreç) | KAPALI |
| 4 | komut yutulması | R1 (Q5 kuyruk) | KAPALI |
| 5, 7, 11 | zincir/döngü/DSP | R4/R6 | KAPALI |
| 9, 13 | cihaz/kip | E4/Q6 · driver R1 | KAPALI |
| 10, 14 | çökme/kapanış | realtime · mixing R6 | KAPALI |
| 12 | clock | synchronization komşu | — |

## §10 Uygulama kapıları (hepsi KAPALI)

| # | Kapı | Soru | Sahip | Durum |
|---|---|---|---|---|
| 1 | R1 | engine ↔ backend sınırı (Q5) | Vault Steward | KAPALI |
| 2 | R2 | thread/süreç önceliği | embedded-engineer | KAPALI (UNKNOWN) |
| 3 | R3 | durum kümesi (Q7) | embedded-engineer | KAPALI |
| 4 | R4 | grafik API'si | embedded-engineer | KAPALI |
| 5 | R5 | geçiş senkron/asenkron | embedded-engineer | KAPALI (UNKNOWN) |
| 6 | R6 | yürütme sırası sahibi | Vault Steward | KAPALI |
| 7 | Q7 | AAudio 5 durum alınır mı? | embedded-engineer | KAPALI (R3) |
| 8 | ölçüm | tik süresi/latans bütçesi | diagnostics | KAPALI (ölçüm yok) |
| 9 | kod | engine kodu oluşumu | — | KAPALI (glob 0, §4 #6) |

## §11 Kapsam dışı taşma denetimi

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §6.1 | aşama listesi bağlayıcı mı? | ✅ HAYIR — "aday" etiketi |
| 2 | §6.2 | durum kümesi kesin mi? | ✅ HAYIR — R3 AÇIK |
| 3 | §6.3 | geçiş tetikleyicisi yazıldı mı? | ✅ HAYIR — R5 UNKNOWN |
| 4 | §7.2 | sıra sahibi atandı mı? | ✅ HAYIR — R6 AÇIK |
| 5 | §7.3 satır 5 | hata politikası verildi mi? | ✅ HAYIR — UNKNOWN |
| 6 | §8.1 | Q5 cevaplandı mı? | ✅ HAYIR — girdi tablosu |
| 7 | §9 | kök neden kesin mi? | ✅ HAYIR — "aday" sütunu |
| 8 | §9 | sayısal eşik/periodyot? | ✅ HAYIR — yok |
| 9 | §10 | kapı "geçti" mi? | ✅ HAYIR — 9/9 KAPALI |
| 10 | genel | §1–§5 değişikliği | ✅ hayır |
| 11 | genel | yasaklı yer tutucu / derlenebilir kod | ✅ yok (yalnız ```text) |
| 12 | genel | DSP/latans sayısı uydurma | ✅ yok (UNKNOWN) |

## §12 Atıf tablosu

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §6.1 | §4 #8 (create→addNode→initialize→start) | ✅ fetch edilmiş URL |
| 2 | §6.2/§6.3 | §5 R3/R5 + master Q7 | açık soru |
| 3 | §7.1 | §2 satır 4 (komut yorumu) | iç kanıt |
| 4 | §7.2 | §2 satır 3 + §4 #5/#9 | iç kanıt + URL |
| 5 | §7.3 | §4 #9 (monoton sample-time) | ✅ fetch edilmiş URL |
| 6 | §8.1 | §5 R1 (Q5) | ⚠️ açık |
| 7 | §8.2 | §3 E2 + gerçek zamanlı kural seti | sınır + iç kanıt |
| 8 | §8.3 | §4 #5 (sinyal gider, kod girmez) | ✅ iç kanıt |
| 9 | §9 | §3 E'ler + diagnostics H-sınıfı şeması | komşu eşleme |
| 10 | §10 | §5 R1–R6 + §4 #6 | açık soru + ölçüm |
| 11 | §11 | §1–§5 | iç kanıt |
| 12 | §6.1 L5 | §1 "her tik'te zincir bir adım" | iç kanıt (adımlama sahipliği) |
| 13 | §13.1 | §2 satır 1 (buffer dışarıda) | iç kanıt — değerler UNKNOWN |
| 14 | §14.1 | §6 + §7.1 birleşimi | iç kanıt |
| 15 | §16.2 | ölçüm yokluğu (§4 #6 glob 0 + PLANNED) | ölçüm kapısı |
| 16 | §16.4 | §15 tekniğinin §16'ya devri | iç kanıt |
| 17 | §16.3 zinciri | ölçüm → R girdisi yönü (kapatma değil) | şablon |

## §13 Latans bütçesi ve tik zamanlaması (şablon — sayısal değer YOK)

> Sartname hedefi 512 örnek / 48 kHz'tir (ölçüm PLANNED). Bu bölüm bütçenin
> **parçalarını** listeler; hiçbir hücreye ms/periyot sayısı yazılmaz.

### §13.1 Bütçe kalemleri (kimden kiminle)

| # | Kalem | Başlangıç | Bitiş | Sahip | Değer |
|---|---|---|---|---|---|
| 1 | giriş tamponu | cihaz → engine | engine | backend/input | UNKNOWN |
| 2 | tik işleme | engine adımı | K004 çıkışı | engine + K004 | UNKNOWN |
| 3 | DSP kuyruğu (varsa) | K004 içi | K004 çıkışı | K004 | UNKNOWN |
| 4 | toplama | mix | çıkış | mixing | UNKNOWN |
| 5 | çıkış tamponu | engine → sürücü | sürücü | backend/driver | UNKNOWN |
| 6 | sürücü/OS | sürücü tamponu | cihaz | driver | UNKNOWN (R5 driver) |
| 7 | toplam | — | — | ⚠️ bütçe sahibi yok | UNKNOWN |

**Kural:** toplam satırına hiçbir yerde sayısal hedef yazılmaz — bütçe sahibi
(başka dosya/karar) belli olmadığı için **UNKNOWN** kalır; ölçüm kapıdadır.

### §13.2 Tik zamanlaması olayları (neden–sonuç, süre YOK)

| # | Olay | Olası sonuç (aday) | Ölçüm | Kapı |
|---|---|---|---|---|
| 1 | tik zamanında gelir | normal adım | — | — |
| 2 | tik geç gelir | gecikme/artış | var mı? | diagnostics R2 |
| 3 | tik erken gelir | — | — | R2 |
| 4 | iki tik birleşir | taşma riski | — | R2 |
| 5 | tik hiç gelmez | akış durdu (§9 satır 2) | — | R2 |
| 6 | komut tik'e rastlar | durum geçişi (§6.3) | — | R5 |

### §13.3 Bütçe denetim soruları (hepsi KAPALI)

| # | Soru | Durum | Sahip |
|---|---|---|---|
| 1 | hedef latans (ms) nedir? | KAPALI — sayı yok, sartname yalnız 512/48 | ölçüm |
| 2 | bütçe kalemlerine pay dağıtıldı mı? | KAPALI — pay YOK | ⚠️ üst karar |
| 3 | tik süresi ölçümü var mı? | KAPALI — ölçüm PLANNED | diagnostics |
| 4 | xrun eşiği nedir? | KAPALI — sayı uydurulmaz | diagnostics R2 |
| 5 | öncelik/RT politikası? | KAPALI — R2 UNKNOWN | embedded-engineer |

## §14 Komut → durum → komşu eşleme ve devir notları

### §14.1 Komut yerleşim haritası

| # | Komut | §6 durumu | Doğrudan komşuya gider mi? | Komşu |
|---|---|---|---|---|
| 1 | oynat | S4 (L4/L5) | hayır (motor içi) | — |
| 2 | dur | S7 (L8) | hayır | — |
| 3 | duraklat | S5 (L6) | hayır | — |
| 4 | ileri/geri | S4 içinde konum | evet (okuma) | backend |
| 5 | zincire ekle | S2 (L2) | evet (düğüm) | K004 · routing |
| 6 | cihazda çalıştır | — (niyet) | evet | device · driver |
| 7 | kip/format değiştir | S3'e dönüş mü? | evet | driver R1 |
| 8 | hata bildirimi | S6 (L10) | evet (olay) | diagnostics |

### §14.2 Komşu eşleme (dosya bazlı)

| # | Bu dosya konusu | Komşu dosya | Pay |
|---|---|---|---|
| 1 | Q5 sınırı (§8.1) | `backend/backend-kapsam.md` | birlikte karar |
| 2 | kurulum (§6.1 L1–L4) | `driver/driver-kapsam.md` §6 | komut zinciri |
| 3 | çıkış yazımı (§7.2 adım 5) | `output/output-kapsam.md` | komşu |
| 4 | kaynak okuma (§7.2 adım 1) | `input/input-kapsam.md` | komşu |
| 5 | toplama (§7.2 adım 4) | `mixing/mixing-kapsam.md` | komşu |
| 6 | tik olayı (§9) | `diagnostics/diagnostics-kapsam.md` | olay üretimi |
| 7 | monoton tik (§7.3) | `realtime/realtime-kapsam.md` | kurallar |
| 8 | clock (§9 satır 12) | `synchronization/` | komşu |
| 9 | Q6 niyeti (§7.1 satır 7) | `device/device-kapsam.md` | komşu |
| 10 | doğrulama (§10 kapı 8) | `testing/` | kabul kriteri |

### §14.3 Devir notları (sonraki tur)

| # | Devir maddesi | Bekleyen iş | Sahip |
|---|---|---|---|
| 1 | Q5 sınırı | iki dosya arasında karar | Vault Steward (R1) |
| 2 | Q7 durum kümesi | AAudio şablonu alınır mı? | embedded-engineer (R3) |
| 3 | grafik API'si | K004 düğüm ekleme arayüzü | embedded-engineer (R4) |
| 4 | thread/süreç | öncelik kararı — sayı yok | embedded-engineer (R2) |
| 5 | geçiş asenkronluğu | tasarım kararı | embedded-engineer (R5) |
| 6 | yürütme sırası | R1 ile kilitli | Vault Steward (R6) |
| 7 | latans bütçesi | kalem payları + ölçüm | ⚠️ üst karar + diagnostics |
| 8 | engine kodu | glob 0 (§4 #6) | — |

**Kural:** sekiz madde de bu dosyada kapatılmaz; kapanış ilgili R numarasında
olur ve Authority satırı değişmez.

## §15 Taşma denetimi eki (§13–§14)

| # | Eklenen bölüm | Denetim | Sonuç |
|---|---|---|---|
| 1 | §13.1 | bütçe kalemine ms/pay yazıldı mı? | ✅ HAYIR — 7 hücre UNKNOWN |
| 2 | §13.2 | süre/periyot sayısı yazıldı mı? | ✅ HAYIR |
| 3 | §13.3 | bütçe sorusu "geçti" mi? | ✅ HAYIR — 5/5 KAPALI |
| 4 | §14.1 | komut sahipliği değiştirildi mi? | ✅ HAYIR — §7.1 ile aynı |
| 5 | §14.2 | komşu dosyaya karar yazıldı mı? | ✅ HAYIR — yalnız eşleme |
| 6 | §14.3 | devir sahipleri uyduruldu mu? | ✅ HAYIR — §5 R sahipleri |
| 7 | §13–§14 | §1–§5 değişikliği | ✅ hayır |
| 8 | genel | yasaklı yer tutucu kalıbı | ✅ kullanılmadı |
| 9 | genel | derlenebilir kod iddiası | ✅ yok |
| 10 | genel | Authority tekliği | ✅ tek |
| 11 | §16.1 | "?" hücreleri kesin mi? | ✅ HAYIR — R3/R5 kapısına bakar |
| 12 | §16.2 | ölçüm kapısı "geçti" mi? | ✅ HAYIR — 8/8 AÇIK |
| 13 | §16.3 | ölçüm sonucu uyduruldu mu? | ✅ HAYIR — yalnız yön |
| 14 | §16 | süre/eşik yazıldı mı? | ✅ HAYIR — UNKNOWN |

## §16 Komut–durum–hata matrisi ve ölçüm kapıları

### §16.1 Komut × durum × hata üçlü matrisi (tam)

| # | Komut | Beklenen durum | Geçersiz durumda | Hata modu (§9) | Komşu |
|---|---|---|---|---|---|
| 1 | oynat | S3 → S4 | S4'te tekrar: yok say | 1 | — |
| 2 | oynat | S7 → ? | durum yok: ret mi? → R5 | 1 | — |
| 3 | oynat | S9 → ? | yok edilmiş: ret | 1 | — |
| 4 | dur | S4 → S7 | S7'de yok say | — | — |
| 5 | dur | S2 → ? | kurulumda ret → R5 | 1 | — |
| 6 | duraklat | S4 → S5 | S5'te yok say | 6 | — |
| 7 | duraklat | S3 → ? | henüz çalışmıyor | 6 | — |
| 8 | devam | S5 → S4 | S4'te yok say | 6 | — |
| 9 | devam | S4 → ? | değilse yok say | 6 | — |
| 10 | ileri-al | S4/S5 | depolama backend | 4 | backend |
| 11 | geri-al | S4/S5 | depolama backend | 4 | backend |
| 12 | zincire ekle | S1/S2/S3 | S4'te? → R5 | 5 | K004 · routing |
| 13 | cihazda çalıştır | S3/S4 | driver ret → zincir | 9 | device · driver |
| 14 | kip/format değiştir | S3'e dönüş | L3/L4 tekrarı | 9 | driver R1 |
| 15 | destroy | S7 → S9 | S4'te: önce durdur | 10 | — |
| 16 | (olay) hata | S4/S2 → S6 | — | 6, 7 | diagnostics |
| 17 | (olay) tik kaybı | S4 içinde | S6'ya? → politika yok | 2 | realtime |
| 18 | (olay) DSP hatası | S4 → S6 mı? | sınır §8.3 | 11 | K004 |

**Okuma kuralı:** "?" hücreleri R3/R5 kapılarına bakar; hiçbir hücrede
geçişin **kesin** olduğu iddia edilmez (durum kümesi adaydır).

## §16.2 Ölçüm kapıları (hepsi AÇIK — ölçüm yok)

| # | Kapı | Ne ölçülecek | Yöntem | Durum |
|---|---|---|---|---|
| 1 | tik süresi | L5 adım süresi | bilinmiyor | AÇIK (PLANNED) |
| 2 | tik düzensizliği | jitter | bilinmiyor | AÇIK |
| 3 | komut gecikmesi | komut → durum geçişi | bilinmiyor | AÇIK |
| 4 | toplam latans | §13.1 7 kalem | bütçe dağılımı yok | AÇIK |
| 5 | xrun/dropout sıklığı | §9 satır 8 | eşiği yok | AÇIK |
| 6 | S8 döngüsü sayısı | §9 satır 7 | eşik yok | AÇIK |
| 7 | CPU payı (motor) | tik içinde | yöntem yok | AÇIK |
| 8 | sıfır-tahsis uyumu | §9 satır 10 | denetim yok | AÇIK |

**Kural:** sekiz kapı da açık kalır; hiçbirine "geçti/ölçüldü" yazısı
konmaz. Ölçüm başladığında sonuç ilgili R'ye (R2/diagnostics) işlenir.

### §16.3 Kapı → ölçüm → R zinciri (dönüş tablosu)

| # | Kapı (§16.2) | Ölçüm sonucu nereye yazar? | R'ye etkisi | Kapı durumu |
|---|---|---|---|---|
| 1 | tik süresi | diagnostics ölçüm günlüğü | R2'ye girdi (sayı yine de UNKNOWN kalır) | AÇIK |
| 2 | tik düzensizliği | diagnostics | R2 + realtime komşu | AÇIK |
| 3 | komut gecikmesi | diagnostics | R5'e girdi (senkron/asenkron kanıtı) | AÇIK |
| 4 | toplam latans | — | bütçe sahibi yok → ⚠️ üst karar | AÇIK |
| 5 | xrun sıklığı | diagnostics | R2 + driver R5 (birim) | AÇIK |
| 6 | S8 döngüsü | diagnostics | R3'e girdi (durum kümesi gerçekliği) | AÇIK |
| 7 | CPU payı | diagnostics | R2'ye girdi | AÇIK |
| 8 | sıfır-tahsis | denetim raporu | E2 (realtime) | AÇIK |

**Döngü kuralı:** ölçüm → günlük → R girdisi → kapı kapanışı; hiçbiri bu
dosyada **başlatılmaz**, yalnız yönü gösterilir (ölçüm PLANNED).

### §16.4 Ek taşma denetimi (§16)

| # | Denetim | Sonuç |
|---|---|---|
| 1 | §16.1'de "?" hücreleri kesin yazıldı mı? | ✅ HAYIR — R3/R5'e bakar |
| 2 | §16.2'ye "geçti" yazıldı mı? | ✅ HAYIR — 8/8 AÇIK |
| 3 | §16.3'te ölçüm sonucu uyduruldu mu? | ✅ HAYIR — yalnız yön |
| 4 | §16'da sayısal süre/eşik var mı? | ✅ HAYIR — UNKNOWN |
| 5 | Authority tekliği | ✅ tek |

## §17 Atıf tablosu (§16)

| # | Bölüm | Dayanak | Sınıf |
|---|---|---|---|
| 1 | §13.1 | sartname 512/48 (ölçüm PLANNED) | hedef + ölçüm kapısı |
| 2 | §13.1 satır 7 | bütçe sahibi yok → UNKNOWN | bilinmeyen |
| 3 | §13.2 | §4 #9 (monoton sample-time) | ✅ fetch edilmiş URL |
| 4 | §13.3 | §5 R2 + diagnostics R2 | açık soru |
| 5 | §14.1 | §6 durum şeması + §7.1 | iç kanıt |
| 6 | §14.2 | §2 sınır satırları | iç kanıt |
| 7 | §14.3 | §5 R1—R6 | açık soru |
| 8 | §16 latans bütçesi | §8 ölçüm kapıları | ölçüm AÇIK |
| 9 | §17 matrisi | §6 durum şeması | iç kanıt |

---

## §18 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu dosyada AÇIK kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
