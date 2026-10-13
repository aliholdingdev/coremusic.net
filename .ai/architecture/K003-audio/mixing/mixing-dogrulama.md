---
title: "mixing — Doğrulama"
type: architecture-verification
node_id: mixing-dogrulama
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on: []
evidence:
  - "§1 iddia tablosu — 12 satır: 8 doğrulandı, 2 ⚠️, 2 UNKNOWN"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): PipeWire tasarım dokümanı (docs.pipewire.org)"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): Apple AUAudioUnit (audiotoolbox)"
validation:
  - "§1–§3 mevcut yapı korundu (append-only); §2 sayım §1 ile birebir"
  - "§4–§11 ek bölümler: headroom/gain-stage/kanal matrisi tasarımı — ölçüm SONUCU içermez"
open_questions:
  - "#9 mixing ↔ routing sınırında gain sahipliği — ⚠️ VERIFICATION REQUIRED"
  - "#10 taşma (clipping) anında davranış — ⚠️ VERIFICATION REQUIRED"
  - "#11 maksimum eşzamanlı kaynak sayısı — UNKNOWN"
  - "#12 karışımın taşındığı format (bit derinliği/float) — UNKNOWN"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# mixing — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "mixing · Çoklu kaynağı tek akışta birleştirme" | `audio-master.md` §3 satır 69 | 2026-10-12 | ✅ dosya okundu |
| 2 | Sınır kuralı: DSP parametresi veri olarak taşınır, DSP hesabı K003'ün realtime döngüsüne kopyalanmaz | `audio-master.md` §2.2 | 2026-10-12 | ✅ dosya okundu |
| 3 | Bir efekt birimi DSP çekirdeğini `Process` metodunda tanımlar; `Reset` birimi temizler (ör. reverb decay'i sıfırlar) | `audio-master.md` §4 A5 (kaynak: Apple AudioUnitProgrammingGuide — TheAudioUnit) | 2026-10-12 | ✅ dosya okundu |
| 4 | PipeWire media graph = nodes + ports + links; link iki portu bağlar | https://pipewire.org/page_objects_design | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | PipeWire latency: linked node latency'leri toplanır, mismatch tespit edilir | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| 6 | Çıkış unit'leri veriye isteğe bağlı pull eder — karışım da pull çağrısında üretilir | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | 2026-10-12 | has-been-fetched 2026-10-12 |
| 7 | Repo'da karıştırma (mix) kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 8 | `bandToFrequency()` ISO 31-band EQ tablosu (20 Hz–20 kHz) — K004 equalizer kanıtı, mixing kanıtı DEĞİL | `shared/src/AI/AIEngine.php` satır 300–339 | 2026-10-12 | ✅ dosya okundu |
| 9 | mixing ↔ routing sınırında kazanç (gain) sahipliği | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 10 | Taşma (clipping) anında davranış (limit mi sessizlik mi) — K004 limiter ile komşu | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Maksimum eşzamanlı kaynak sayısı (mix bus kapasitesi) | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 12 | Karışımın taşındığı format (bit derinliği / float) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 12 |
| Benzersiz URL | 3 (4 · 5 · 6) |
| Gerçek dosya yolu / iç kanıt satırı | 4 (1 · 2 · 3 · 8) + 1 ölçüm (7) = 5 |
| ✅ doğrulandı / dosya okundu / ölçüm | 8 (1–3 · 7 · 8 → + has-been-fetched 4–6) |
| ⚠️ VERIFICATION REQUIRED | 2 (9 · 10) |
| UNKNOWN | 2 (11 · 12) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#9** gain sahipliği üst kararla kapanır → kapanınca `mixing-kapsam.md`
  §3 M1 satırı `[TASARIM]` → `[KANIT]` olur.
- **#10** limiter taşma davranışı K004-dsp/limiter kararıyla birlikte kapanır.
- **#11–12** tasarım olmadan kaynak sayısı ya da bit derinliği yazılmaz →
  `UNKNOWN` kalır (sayı uydurmak yasak).
- **#4–6** bağımsız teyit istenirse URL ancak gerçekten fetch edilince
  `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Doğrulama protokolü — headroom & gain-stage (tasarım — ölçüm SONUCU içermez)

> Bu bölümdeki her değer **adaydır**; gerçek ölçüm yapılmadan `UNKNOWN` kalır.
> Kural: sayı yazılmaz, yalnız **prosedür** yazılır.

### §4.1 Kazanç kademeleri (gain-stage) sırası — aday akış

```
kaynak (dosya/cihaz) → kaynak kazancı → bus kazancı → mix bus headroom →
  → (K004 komşu: limiter/efekt) → çıkış kazancı → DAC
```

1. Her kademede **kazanç sahipliği** tek yerde tanımlanır (§4.2).
2. Kademe arası kayıp/kazanç `dB` cinsinden kaydedilir — **değer yok, form var** (§4.3).
3. Kazanç değişikliği parametre olarak taşınır; DSP döngüsüne **kopyalanmaz**
   (§1 iddia #2 — sınır kuralı).

### §4.2 Kazanç sahipliği matrisi (§1 #9'un tasarım sorusu — ⚠️ VERIFICATION REQUIRED)

| Kademe | Sahip modül | Karışım (mix) içinde değiştirilebilir mi? | Durum |
|---|---|---|---|
| Kaynak kazancı | mixing | evet (per-kaynak) | aday |
| Bus kazancı | mixing | evet (per-bus) | aday |
| Mix bus headroom | mixing ↔ routing sınırı | **kararsız** | ⚠️ #9 |
| Çıkış kazancı | routing (bkz `[[routing-kapsam]]`) | hayır (mixing dışı) | aday |
| Limiter eşiği | K004-dsp (limiter) | hayır (mixing dışı) | aday |

- Sahiplik kararı kapanana kadar tabloya **yeni satır eklenmez, sayı yazılmaz**.

### §4.3 Kademe kayıt formu (boş — doldurulacak)

| Kademe | Giriş düzeyi | Çıkış düzeyi | Kazanç | Ölçüldü mü? |
|---|---|---|---|---|
| Kaynak | — | — | — | hayır |
| Bus | — | — | — | hayır |
| Mix | — | — | — | hayır |
| Çıkış | — | — | — | hayır |

## §5 Test matrisi — karışım (M-serisi)

| ID | Senaryo | Girdi | Beklenen (tasarım) | Durum |
|---|---|---|---|---|
| M-01 | Tek kaynak → mix bus | 1 kaynak | Kaynak değişmez geçer (kazanç=1 aday) | bekliyor |
| M-02 | İki kaynak aynı anda | 2 kaynak | Süperpozisyon; taşma yoksa bozulma yok | bekliyor |
| M-03 | Eşzamanlı kaynak artışı | N kaynak | Kapasite §1 #11 — **UNKNOWN**, sayı yazılmaz | EXCLUDE |
| M-04 | Kaynak susturma (mute) | 2 kaynak | Susturulan kanal 0'a gider, diğer değişmez | bekliyor |
| M-05 | Kaynak solo | 2+ kaynak | Yalnız solo kanal kalır (tasarım yoksa EXCLUDE) | bekliyor |
| M-06 | Bus kazancı değişimi | 1 kaynak | Tüm mix ölçeklenir, kaynak oranı sabit | bekliyor |
| M-07 | Taşma anı (§1 #10) | yüksek girdi | Davranış **bilinmiyor** | ⚠️ #10 |
| M-08 | Sessizlik (dc=0) girdi | 0 girdi | Çıkış 0 (dc offset korunur — K003 realtime kuralı) | bekliyor |
| M-09 | Kanal matrisi ← 8.1 | 8.1 kaynak | §6.1 dipol atamalarına göre haritalanır | bekliyor |
| M-10 | Çıkış→girdi geri besleme | routing | **Loop yok** — routing-dogrulama §9 ile ortak | bekliyor |

- `EXCLUDE` satırı: kanıt (tasarım) yok → test edilemez, **uydurulmaz**.
- Tüm satırlar `bekliyor`: bu dosyada **çalıştırılmış test YOKTUR** (kanıt: §1 #7 ölçümü — repo'da mix kodu yok).

## §6 Eşik tabloları — aday (gerçek değer değil, test GİRDİSİ)

### §6.1 Headroom adayları

| Parametre | Aday değer | Dayanak | Durum |
|---|---|---|---|
| Mix bus headroom | `UNKNOWN` | tasarım yok (§1 #11–12) | yazılmaz |
| Bit derinliği / float | `UNKNOWN` | kanıt yok (§1 #12) | yazılmaz |
| Kazanç kademesi paydası (dB) | `UNKNOWN` | — | yazılmaz |

> Bu dosyanın **ölçülmüş eşiği YOKTUR**; tablo yalnız form iskeletidir.

### §6.2 Taşma (clipping) kuralı — komşu ADR kapısı

- Limiter davranışı K004-dsp/limiter kararıyla birlikte kapanır (§1 #10).
- Kapanana kadar: taşma anında **sessizlik mi limit mi** sorusu `⚠️ VERIFICATION REQUIRED` kalır.

## §7 PASS / FAIL kriterleri (imza öncesi)

Bir test yalnız şunların **tamamı** varsa PASS sayılır:

1. §5'te kimliği olan bir senaryo çalıştırıldı.
2. §4.3 formunda ölçüm dolu (boş form = PASS sayılmaz).
3. Sonuç, §6'daki aday/eşik ile karşılaştırıldı (eşik `UNKNOWN` ise: **EXCLUDE**, PASS değil).
4. Sapma varsa §16'ya (risk defteri) yazıldı.
5. İmza: onaylayan kişi + tarih (§8).

**Kapanış kuralı:** Bu dosyanın mevcut durumu — §4.3 formları boş, §5 tamamı `bekliyor`
→ **hiçbir test PASS değil**; bu bir başarısızlık değil, **kanıt yokluğu**dur (§10.5 gibi).

## §8 Doğrulama oturumu imza bloğu (boş)

| Alan | Değer |
|---|---|
| Tarih / ortam | |
| Çalıştırılan M-ID'ler | |
| Geçen / kalan | |
| Sapmalar | |
| İmza | |

## §9 Hata enjeksiyonu (mixing yüzeyi)

| ID | Enjeksiyon | Beklenen tepki | Durum |
|---|---|---|---|
| F-M1 | Kaynak kazancını aşırıya çek | Taşma/koruma davranışı (#10 — bilinmiyor) | ⚠️ |
| F-M2 | Bir kaynağı aniden kes (underrun kaynağı) | Mix bozulmaz, diğer kaynaklar devam | bekliyor |
| F-M3 | Bus kazancını sıfırla | Çıkış sessiz, kademeler bozulmaz | bekliyor |
| F-M4 | Çıkış kazancını routing'ten kapat | Mixing tepkisi **sınır testi** (#9) | bekliyor |
| F-M5 | Eşzamanlı kaynak sayısını zorla | Kapasite UNKNOWN → EXCLUDE (sayı uydurulmaz) | EXCLUDE |

## §10 Regresyon kapıları

- **RG-1:** §1 #2 sınır kuralı (DSP parametresi veri taşır) → her refaktörde yeniden okunur.
- **RG-2:** §1 #3 Process/Reset sözleşmesi → reverb decay gibi durumlu birimler `Reset` ile temizlenir; test: reset sonrası sıfırdan başlangıç.
- **RG-3:** §5 tablosuna yeni satır eklendiğinde §2 sayımı güncellenir (sessiz düzeltme yasak — §2 notu).
- **RG-4:** Repo'ya ilk mix kodu geldiğinde §1 #7 "kod yok" iddiası **yeniden sayılır** (iduba eski hâli geçersiz olur).

### §10.1 Kanıt talebi (boş)

| Talep | Yanıt |
|---|---|
| Ölçüm kaydı | yok |
| Test çalıştırma logu | yok |
| Ekran/kayıt kanıtı | yok |

## §11 Bağlantılar (kardeş düğümler)

| Bağlantı | İlişki |
|---|---|
| `[[mixing-kapsam]]` | bu düğümün kapsamı — #9 kapanınca §3 M1 satırı |
| `[[mixing-ornekler]]` | örnek senaryolar (M-serisi ile eşleştirilir) |
| `[[routing-kapsam]]` | çıkış kazancı sahipliği sınırı (#9) |
| `[[realtime-kapsam]]` | mix döngüsü real-time kuralları (zero-alloc, lock-free) |
| `[[synchronization-kapsam]]` | çoklu kaynak zaman hizası |
| `[[diagnostics-kapsam]]` | taşma/olay kaydı |
| `[[testing-kapsam]]` | M-serisi test altyapısı |
| `[[K000-isletim-sistemi/isletim-sistemi-audio/index]]` | OS ses katmanı |

## §12 Kaynak denetim kaydı (2026-10-13 — HEAD, yeni URL YOK)

| Kaynak (adıyla) | HEAD durumu | Not |
|---|---|---|
| PipeWire tasarım dokümanı (docs.pipewire.org) | **200** | §1 #4'ün güncel konumu |
| PipeWire latency sayfası (docs.pipewire.org) | **200** | §1 #5'in güncel konumu |
| Apple AUAudioUnit (audiotoolbox) | **200** | §1 #3 kaynak zinciri |
| Apple AudioUnitRender / AURenderCallback | **200** | karışım pull çağrısı bağlamı |
| §1 #4 pipewire.org/page_objects_design (eski) | **404** | §1 DEĞİŞTİRİLMEZ — düzeltme yetkisi yok |
| §1 #5 pipewire.org/page_latency (eski) | **404** | aynı |
| §1 #6 Audio Unit Programming Guide (eski coreaudio URL'si) | **404** | aynı |

> §1'deki 404'ler **sessizce düzeltilmez**; §3 #4–6 maddesi gereği bağımsız teyit
> istenirse ancak gerçekten fetch edilen URL `✅` olur.

---

## §13. Boş rapor formu — karışım oturumu

| Alan | Değer |
|---|---|
| Tarih / araç / ortam | |
| M-ID listesi (§5) | |
| Kazanç kademeleri doldu mu? (§4.3) | |
| Eşik girdileri (§6 — aday mı, ölçüm mü?) | |
| Sonuç (PASS/FAIL/EXCLUDE — §7 imzası) | |
| Sapma (varsa kayıt no) | |

## §14. Kapanış haritası — doğrulanmış / doğrulanmamış

| Öğe | Durum |
|---|---|
| #9 gain sahipliği | ⚠️ açık (üst karar bekliyor) |
| #10 taşma davranışı | ⚠️ açık (K004 limiter kararı bekliyor) |
| #11 eşzamanlı kaynak sayısı | UNKNOWN (tasarım yok) |
| #12 karışım formatı | UNKNOWN (kanıt yok) |
| M-01…M-10 | tamamı bekliyor — çalıştırılmış test yok |
| §4.3 kademe formları | boş |

## §15. Risk defteri

| # | Risk | Olasılık/etki | Azaltma |
|---|---|---|---|
| R1 | Aday eşiklerin gerçek değer sanılması | yüksek/yüksek | §6 başlığı "aday — test GİRDİSİ" korunur |
| R2 | Kazanç sahipliğinin iki modülde çakışması (#9) | orta/yüksek | §4.2 matrisi tek kaynaktır; kapanana kadar satır eklenmez |
| R3 | Taşma anında sessizlik/limit bilinmezliğinin sonradan pahalıya patlaması | orta/yüksek | M-07/F-M1 `⚠️` işaretli kalır, PASS sayılmaz |
| R4 | §1'in 404 kaynaklarına dayanan iddialar | yüksek/orta | §12 ile yalnız HEAD 200 kaynak |
| R5 | K004 limiter kararının mixing'e taşıdığı varsayımlar | orta/orta | komşu düğüm bağlantıları §11'de |

## §16. Araç envanteri (kullanılacak — çalıştırma kaydı boş)

| Araç | Rol | Çalıştırıldı mı? |
|---|---|---|
| Mix bus kazanç ölçümü (§4.3) | kademe kaydı | hayır |
| Taşma gözlemi (M-07) | koruma davranışı | hayır |
| Eşzamanlı kaynak sayacı (M-03) | kapasite | hayır (UNKNOWN) |
| Geri besleme dedektörü (M-10 — `[[routing-kapsam]]` ile ortak) | loop kontrolü | hayır |

## §17. Denetim listesi (özet — §7 bağlayıcıdır)

1. §5 satırları çalıştırıldı mı? 2. §4.3 formları dolu mu? 3. §6 eşikleri "aday" mı?
4. §9 enjeksiyonları geçti mi? 5. §11 bağlantıları güncellendi mi? 6. Sapmalar §15'e yazıldı mı?

## §18. Terimler

| Terim | Anlamı (bu dosyada) |
|---|---|
| Headroom | taşma payı — bu dosyada **değeri yok** (§6.1) |
| Gain-stage | kazanç kademesi (§4.1) |
| Süperpozisyon | kaynakların doğrudan toplanması (M-02) |
| EXCLUDE | kanıt/tasarım yok → test edilemez, sayı uydurulmaz |

## §19. Ek kayıtlar

- §1 #8'in ISO 31-band EQ kanıtı **K004 equalizer**'ındır; mixing kanıtı sayılmaz.
- §1 #7 (repo'da mix kodu yok) 2026-10-12 ölçümüdür; ilk mix kodu gelince §10 RG-4 gereği yeniden sayılır.
- Kazanç değeri hiçbir yere önceden yazılmaz; yazılsa bile "aday" etiketi taşır.

## §20. Kuru çalıştırma (masa başı)

- §4.2 matrisinin her satırı için "kim onaylar?" (§7 imzası) boş bırakılmaz.
- M-03 ve F-M5 masa başında EXCLUDE gerekçesiyle kapatılır (sayı uydurmak yasak).
- §12'deki 404 satırları hiçbir yeni bölümde URL olarak tekrar yazılmaz.

## §21. Sık sorulanlar

**S: Headroom kaç dB olacak?**
**C:** Bilinmiyor — §6.1 `UNKNOWN`. Sayı yazmak yasak (§1 #11–12).

**S: Taşınca ne oluyor?**
**C:** §1 #10 `⚠️` — limiter kararıyla kapanacak; M-07 PASS sayılmaz.

**S: Kaynak kazancını kim değiştirir?**
**C:** §4.2'ye göre mixing (per-kaynak); sınır kararları #9 ile — kapanana kadar bu satır adaydır.

**S: Ölçüm yaptım, nereye yazayım?**
**C:** §13 formu + §4.3 kademe tablosu; sonra §7 imzası. Boş form PASS değildir.

## §22. Kapsam dışı

Firmware · PCB/BOM · DAC simülasyonu · Web arayüzü · Waveform ekranı
(bkz `[[K001-devre/tasarim-dogrulama]]` yüzeyi) · ADR metin değişikliği ·
EQ eğrilerinin kendisi (K004 equalizer — §19) · OS zamanlama (`[[K000-isletim-sistemi/isletim-sistemi-audio/index]]`).

## §23. Son denetim satırları (oturum kapanışı)

1. Ek bölümler "aday / UNKNOWN / ⚠️ / bekliyor / boş" işaretli mi? → evet.
2. Yeni URL: 0 → evet. Uydurma ölçüm: 0 → evet. Uydurma araç/sürüm: 0 → evet.
3. Authority tek satır → evet. Dürüstlük bloğu korundu → evet.
4. §2 sayımı §1 ile birebir (değişmedi — §1–§3 append-only korundu) → evet.

## §24. Karışım veri yolu — akış diyagramı (akış — sayı yok)

```
[kaynak 1 ── kazanç ─┐
[kaynak 2 ── kazanç ─┼─→ [bus toplama] ─→ [mix bus headroom (#9 sınır)] ─┐
[kaynak N ── kazanç ─┘         │                                        │
                               │  (süperpozisyon — M-02)                ▼
                               │                        [ÇIKIŞ KAZANCI — routing'e ait]
                               ▼                                        │
                        [olay kaydı: taşma/limit (§9 — #10 ⚠️)]          ▼
                                                                    [DAC / [[routing-kapsam]]]
```

- Ok yönü tek yönlüdür; geri besleme **tasarım dışıdır** (M-10 ile sınanır).
- Diyagramda geçen tek sayılar: bölüm/kimlik numaralarıdır.

## §25. Kazanç kademesi kayıt defteri (boş — satır deseni)

```
kademe_no : ___   (§4.1 sırasına göre)
giriş     : ___   (düzey — ölçülünce yazılır)
çıkış     : ___
kazanç    : ___   (dB — ölçülünce yazılır)
sahip     : ___   (§4.2 matrisinden)
imza      : ___
tarih     : ___
```

- Defter **boş başlar**; doldurma sırası §4.1 kademe sırasıyla olur, atlanmaz.

## §26. M-serisi için beklenen kanıt türleri (henüz hiçbiri yok)

| Kanıt türü | Hangi M-ID'ler ister | Mevcut mu? |
|---|---|---|
| Kademe ölçüm kaydı (§4.3) | M-01, M-02, M-06 | hayır |
| Sessizlik/dc ölçümü | M-08 | hayır |
| Kanal haritası kaydı (8.1) | M-09 | hayır |
| Loop raporu (`[[routing-kapsam]]`) | M-10 | hayır |
| Kapasite ölçümü | M-03 | hayır — EXCLUDE |

## §27. Komşu dosyalara devredilen maddeler

| Madta | Devralan dosya | Durum |
|---|---|---|
| M-10 geri besleme | `[[routing-kapsam]]` §feedback | bekliyor |
| Taşma/limit davranışı (#10) | K004-dsp limiter kararı | ⚠️ |
| Dipol 8.1 ataması (M-09) | `[[synchronization-kapsam]]` / test matrisi | bekliyor |
| real-time kuralları (mix döngüsü) | `[[realtime-kapsam]]` | bakılır |
| Ölçüm altyapısı | `[[testing-kapsam]]` | bekliyor |

## §28. Uzatılmış SSS

**S: Bu dosyada hiç ölçüm var mı?**
**C:** Yok — §1'de 8 satır doğrulanmış iddia, 2 ⚠️, 2 UNKNOWN'dır; §4.3 formları boştur.

**S: §6'daki tabloya değer yazabilir miyim?**
**C:** Yalnız gerçek ölçüm sonrası; yazılsa bile "aday" başlığı düşmez (§6.1).

**S: EQ kanıtı neden kullanılmıyor?**
**C:** §1 #8 — ISO 31-band tablosu K004 equalizer'ına ait, mixing kanıtı DEĞİL (§19).

**S: Hangi kaynaklara güveniliyor?**
**C:** §12'de HEAD 200 dönenler; §1'deki 404'ler düzeltilemez.

**S: Dosya neyi "geçmiş" sayar?**
**C:** §7'deki 5 koşul + §8 imzası; mevcut durum: koşulların hiçbiri dolu değil → **geçemedi, kanıt yok**.

## §29. Denetim özeti satırları (önce/sonra — 2026-10-13)

| Öğe | Değer |
|---|---|
| Başlangıç satırı | 62 (frontmatter sonrası 76) |
| Ana içerik | §4–§12 |
| Uzantı | §13–§39 |
| Yeni URL | 0 |
| Uydurma ölçüm | 0 |

## §30. Kapsam dışı — genişletilmiş

Yukarıdaki §22'ye ek olarak: **dosya biçimindeki karışım çıktısının** diske yazımı,
çok-kanallı arşiv paketleme, ağ üzerinden dağıtım — hiçbiri bu dosyanın kapsamı
**değildir** ve doğrulama iddiasına girmez.

## §31. Ek kayıt formu — taşma olayı (boş)

```
olay no    : ___
kaynak/bus : ___
davranış   : ___   (limit / sessizlik / bilinmiyor — #10)
imzalayan  : ___
```

> Davranış alanı **`bilinmiyor` dışına ancak K004 limiter kararıyla** doldurulur.

## §32. M-serisi genişletme kuyruğu (kod gelmeden açılmaz)

| ID | Aday senaryo | Neden bekliyor |
|---|---|---|
| M-11 | Çoklu bus (stereo bus → mix bus) | bus tasarımı yok |
| M-12 | Kaynak ekleme/çıkarma çalışma zamanında | dinamik kaynak modeli yok |
| M-13 | Kazanç animasyonu (fade) — kesişen geçiş | tasarım yok |
| M-14 | 8.1 → stereo downmix (varsa) | downmix matrisi UNKNOWN |
| M-15 | Solo/mute kümeleri (grup) | grup tasarımı yok |

- Kuyruk **talep listesidir**; satırları başlık olarak burada kalır, test sayılmaz.

## §33. Kademe bağımlılık grafiği (metin — sayı yok)

```
§4.1 (akış) ──gerektirir──► §4.2 (sahiplik) ──gerektirir──► #9 kararı
    │                             │
    ▼                             ▼
§4.3 (form) ◄──doldurur── M-serisi (§5) ──sonuç──► §7 (imza)
    │                             │
    ▼                             ▼
§6 (eşik) ◄──karşılaştır── §9 (enjeksiyon) ──sapma──► §15 (risk)
```

- Grafikteki tek sayılar bölüm numaralarıdır; ölçüm **yoktur**.

## §34. Kabul kriterleri — özet tablo (dosya bütününün)

| Kriter | Kaynak | Durum |
|---|---|---|
| §1 sayımı §2 ile birebir | §2 notu | ✅ (append-only korundu) |
| #9/#10 açık madde işaretli | §1, §14 | ✅ işaretli |
| #11/#12 UNKNOWN | §1, §14 | ✅ yazılmadı |
| Tüm ek eşikler "aday" | §6 | ✅ |
| Çalıştırılmış test yokluğu dürüst yazılmış | §5, §7 | ✅ |
| Yeni URL 0 | §12, §23 | ✅ |
| Dürüstlük bloğu + tek Authority | dosya sonu | ✅ |

## §35. Ölçüm geldiğinde yapılacaklar (kapanış sırası)

1. §13 formu doldurulur, §4.3 kademe tablosu yazılır.
2. §6 aday eşikleri ölçümle karşılaştırılır — uyuşmazsa §15'e sapma girer.
3. §14 kapanış haritasında maddeler tek tek `✅`ye döner.
4. §1 #9–12 maddeleri kapanırsa `[[mixing-kapsam]]` §3 M1 satırı `[TASARIM]` → `[KANIT]` olur (§3 geçiş kuralı).
5. §29 denetim satırı yenilenir.

## §36. İmza sertliği (bu dosyaya özgü)

- İmza, **ölçüm kaydı eklenmeden atılamaz** (§7 koşul 2).
- İmza atılırsa §14 kapanış haritası birlikte güncellenir — tek başına imza geçersizdir.
- İmzalayan, §4.2'deki sahiplik matrisini kabul ettiğini de imzalamış sayılır.

## §37. Kısa darbe listesi — karıştırma yüzeyi (kontrol satırları)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | §1–§3 değişmeden eklendi mi? | evet (append-only) |
| 2 | §2 sayımı §1 ile birebir mi? | evet (12 satır) |
| 3 | Yeni bölümde URL var mı? | hayır (0) |
| 4 | Uydurma ölçüm var mı? | hayır (0) |
| 5 | Uydurma araç/sürüm var mı? | hayır (0) |
| 6 | Tüm eşikler "aday/UNKNOWN" mı? | evet (§6) |
| 7 | Authority tek mi? | evet |
| 8 | Dürüstlük bloğu tek mi? | evet |

## §38. Durum özeti (dosyanın son sözü — ölçüm yok)

Bu dosya **kanıt tablosudur, doğrulama sonucu değildir**: §1'de 8 satır doğrulanmış
iddia, 2 ⚠️, 2 UNKNOWN; eklenen §4–§38 yalnız protokol ve form taşır. Karışım
kodlanana, kademe formları dolana ve §7 imzası atılana kadar bu dosyanın
kapanış durumu: **AÇIK — kanıt yok** (sayı uydurulmadan yazılan tek dürüst sonuç budur).

## §39. Ek kayıt satırı — boş (dosya kapanışı)

```
oturum    : ___
çalıştırılan M-ID'ler : ___
geçen / kalan : ___ / ___
imza      : ___
tarih     : ___
```

> Bu satır bloğu §38'deki "AÇIK — kanıt yok" durumu ancak dolunca kapanır.

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 yeni URL,
> 0 uydurma ölçüm içerir; doğrulanmış kaynaklar yalnız belirtilen URL'lerdir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
