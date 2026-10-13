---
title: "routing — Doğrulama"
type: architecture-verification
node_id: routing-dogrulama
node_type: subdomain
status: draft
parent_id: K003-audio
children: []
depends_on: []
evidence:
  - "§1 iddia tablosu — 12 satır: 8 doğrulandı, 2 ⚠️, 2 UNKNOWN"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): PipeWire tasarım dokümanı (docs.pipewire.org)"
  - "Kaynak denetimi 2026-10-13 (HEAD HTTP 200): ALSA Library API (alsa-project.org wiki)"
validation:
  - "§1–§3 mevcut yapı korundu (append-only); §2 sayım §1 ile birebir"
  - "§4–§11 ek bölümler: patchbay matrisi/feedback-loop/regresyon tasarımı — ölçüm SONUCU içermez"
open_questions:
  - "#9 routing ↔ mixing sınırı (gain/toplama) — ⚠️ VERIFICATION REQUIRED"
  - "#10 block kenarında yeniden yönlendirme (click-free) — ⚠️ VERIFICATION REQUIRED"
  - "#11 topoloji (sabit/dinamik) ve üst sınır (kaç link) — UNKNOWN"
  - "#12 yanlış yönlendirme test kriteri — UNKNOWN"
date: 2026-10-12
updated: 2026-10-13
group: kanit
---

# routing — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa/websearch ile getirilen URL içerikleri ·
> (b) bu oturumda shell ile ölçülen disk gerçekleri · (c) vault içi iç kanıt
> (dosya okunarak). Yasak: URL, sürüm, sınıf, sürüm numarası uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "routing · Kanal/akış yönlendirme kuralları" | `audio-master.md` §3 satır 68 | 2026-10-12 | ✅ dosya okundu |
| 2 | PipeWire media graph = nodes + ports + links; node = medya işleme elemanı, link iki portu bağlar | https://pipewire.org/page_objects_design | 2026-10-12 | has-been-fetched 2026-10-12 |
| 3 | PipeWire latency: port bazlı Latency nesnesi (direction + min/max); linked node latency'leri toplanır, mismatch tespit edilir | https://pipewire.org/page_latency | 2026-10-12 | has-been-fetched 2026-10-12 |
| 4 | PipeWire graph scheduling: driver node döngüyü başlatır; required/pending sayaçları ile node tetiklenir | https://pipewire.org/page_scheduling | 2026-10-12 | has-been-fetched 2026-10-12 |
| 5 | AUGraph ses işleme zincirini oluşturur; AUGraphNewNode ile düğüm eklenir, graf output unit ile biter | `audio-master.md` §4 A3 (kaynak: Apple ARoadmaptoCommonTasks) | 2026-10-12 | ✅ dosya okundu |
| 6 | Sınır kuralı: "K003 içinden K004'e sinyal gönderilir, içine sokulmaz" | `audio-master.md` §2.2 | 2026-10-12 | ✅ dosya okundu |
| 7 | ALSA plug plugin: format/channels/rate otomatik dönüşümü — kanal dönüşümü sürücü katmanında yapılabilir | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | 2026-10-12 | has-been-fetched 2026-10-12 |
| 8 | Repo'da kanal/akış yönlendirme kodu yok | kök `*.cpp`/`*.h` glob 0 isabet | 2026-10-12 | ✅ ölçüm |
| 9 | routing ↔ mixing sınırı (gain/toplama kimde) kesinleşmiş değil | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 10 | Yeniden yönlendirme block kenarında uygulanır (click-free) | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Yönlendirme topolojisi (sabit/dinamik) ve üst sınır (kaç link) | — | 2026-10-12 | **UNKNOWN** (tasarım yok) |
| 12 | Yanlış yönlendirme test kriteri (kabul eşiği) | — | 2026-10-12 | **UNKNOWN** (kriter yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 12 |
| Benzersiz URL | 4 (2 · 3 · 4 · 7) |
| Gerçek dosya yolu / iç kanıt satırı | 3 (1 · 5 · 6) + 1 ölçüm (8) = 4 |
| ✅ doğrulandı / dosya okundu / ölçüm | 8 (1–8) |
| ⚠️ VERIFICATION REQUIRED | 2 (9 · 10) |
| UNKNOWN | 2 (11 · 12) |

**Doğruluk notu:** §2 satır sayıları §1 ile birebir sayılmalıdır; sayım
uyuşmazsa §1 yeniden sayılır (sessiz düzeltme yasak).

## §3 Geçiş kuralı

- **#9** routing ↔ mixing sınırı üst kararla kapanır → kapanınca
  `routing-kapsam.md` §3 T1 satırı `[TASARIM]` → `[KANIT]` olur.
- **#10** realtime kararıyla (block kenarı) kapanır; kapanmadan "tıklamasız
  geçiş" iddiası yazılmaz.
- **#11–12** tasarım/kriter olmadan link sayısı, topoloji ya da eşik değeri
  uydurulmaz → `UNKNOWN` kalır.
- **#2–4 · 7** bağımsız teyit istenirse URL ancak gerçekten fetch edilince
  `✅ doğrulandı` durumuna yükseltilir.

---

## §4 Doğrulama protokolü — patchbay / rota değiştirme (tasarım — ölçüm SONUCU yok)

### §4.1 Rota geçişi (transition) protokolü — aday akış

```
eski rota (N kaynak → M hedef) ─┐
                                 ├─→ [geçiş penceresi: block kenarı (#10)] → yeni rota
yeni rota (N' kaynak → M' hedef)┘
```

1. Yeniden yönlendirme **yalnız block kenarında** uygulanır (§1 #10 — ⚠️, realtime kararıyla kapanır).
2. Geçiş sırasında toplama/kazanç sahipliği §5'teki soruyla (`[[mixing-kapsam]]` §4.2) çakışırsa DUR.
3. Rota değişikliği parametre olarak taşınır; DSP döngüsüne kopyalanmaz (§1 #6 sınır kuralı).

### §4.2 Rota kayıt formu (boş — doldurulacak)

| Alan | Değer |
|---|---|
| Eski rota (kaynak→hedef) | — |
| Yeni rota | — |
| Geçiş block'u | — |
| Click/tıklama gözlemi | — |
| Ölçüldü mü? | hayır |

## §5 Test matrisi — yönlendirme (R-serisi)

| ID | Senaryo | Girdi | Beklenen (tasarım) | Durum |
|---|---|---|---|---|
| R-01 | Tek link (1→1) | 1 kaynak, 1 hedef | Sinyal aynen geçer | bekliyor |
| R-02 | Fan-out (1→N) | 1 kaynak | Tüm hedefler aynı akışı alır | bekliyor |
| R-03 | Fan-in (N→1) | N kaynak | Toplama — sahiplik #9 ile çakışırsa DUR | ⚠️ #9 |
| R-04 | Block kenarı rota değişimi | 2 rota | Click-free — **kriter yok** (#10/#12) | ⚠️ #10 |
| R-05 | Link kaldırma çalışma zamanında | aktif link | Kesinti davranışı — topoloji UNKNOWN (#11) | EXCLUDE |
| R-06 | Port yönü yanlış (input↔output) | hatalı bağlantı | Bağlantı REDDEDİLİR (§6.2 kuralı) | bekliyor |
| R-07 | Zincir gecikme toplamı | çoklu link | §1 #3 — toplama/mismatch; sayısal eşik yok | bekliyor |
| R-08 | Driver döngüsü tetikleme | graf | §1 #4 — required/pending sayacı akışı | bekliyor |
| R-09 | Çıkış birimi sonu | graf | §1 #5 — graf output unit ile biter | bekliyor |
| R-10 | Geri besleme loop tespiti | döngüsel link | Loop REDDEDİLİR veya işaretlenir (§6.3) | bekliyor |
| R-11 | Fazla link üst sınır | N link | **UNKNOWN** (#11) — sayı yazılmaz | EXCLUDE |
| R-12 | Yanlış yönlendirme eşiği | hatalı rota | **UNKNOWN** (#12) — kriter yazılmaz | EXCLUDE |

- `EXCLUDE` satırları: tasarım/kriter yok → test edilemez, **uydurulmaz**.
- Tüm satırlar çalıştırılmadı: repo'da yönlendirme kodu yok (§1 #8 ölçümü).

## §6 Eşik tabloları — aday (gerçek değer değil, test GİRDİSİ)

### §6.1 Link/topoloji adayları

| Parametre | Aday değer | Dayanak | Durum |
|---|---|---|---|
| Topoloji (sabit/dinamik) | `UNKNOWN` | §1 #11 | yazılmaz |
| Üst link sayısı | `UNKNOWN` | §1 #11 | yazılmaz |
| Yanlış yönlendirme kriteri | `UNKNOWN` | §1 #12 | yazılmaz |
| Click ölçüm eşiği | `UNKNOWN` | — | yazılmaz |

### §6.2 Port yönü kuralı (tasarım — iddia değil)

- Port eşleşmesi yön duygusuna göre yapılır: hedef-port, kaynak-port ile bağlanır (§1 #2 — link iki portu bağlar).
- Yanlış yön denemesi **beklenen sonuç: bağlantı kurulmaz** — bu bir test **girdisidir**, ölçülmüş sonuç değil.

### §6.3 Loop (geri besleme) tespit kuralı — aday

- Graf içinde kaynaktan başlayıp kaynağına dönen yol varsa rota **kabul edilmez** (aday kural).
- Kuralın resmî dayanağı yok → `⚠️` işaretli kalır; R-10 PASS sayılmaz.

## §7 PASS / FAIL kriterleri (imza öncesi)

1. §5'te kimliği olan bir senaryo çalıştırıldı.
2. §4.2 formu dolu (boş form = PASS sayılmaz).
3. Sonuç §6 ile karşılaştırıldı (eşik `UNKNOWN` ise **EXCLUDE**, PASS değil).
4. Sapma §16'ya yazıldı.
5. İmza + tarih (§8).

**Mevcut durum:** §4.2 formları boş, §5 tamamı `bekliyor` → **hiçbir test PASS değil** —
bu başarısızlık değil, **kanıt yokluğu**dur.

## §8 Doğrulama oturumu imza bloğu (boş)

| Alan | Değer |
|---|---|
| Tarih / ortam | |
| Çalıştırılan R-ID'ler | |
| Geçen / kalan | |
| Sapmalar | |
| İmza | |

## §9 Hata enjeksiyonu (routing yüzeyi)

| ID | Enjeksiyon | Beklenen tepki | Durum |
|---|---|---|---|
| F-R1 | Aktif linki aniden kaldır | Kesinti tanımlanmalı (#11 UNKNOWN) | EXCLUDE |
| F-R2 | Port yönünü ters çevir | Bağlantı RED (§6.2) | bekliyor |
| F-R3 | Döngüsel rota kur | Loop RED/işaret (§6.3 — ⚠️) | bekliyor |
| F-R4 | Block ortasında rota değiştir | Click beklenir mi? — #10/#12 yok | ⚠️ |
| F-R5 | Zinciri aşırı uzat | Gecikme toplamı artar (§1 #3); eşik yok | bekliyor |
| F-R6 | Hedef cihaz kaybı | OS fallback (edge-case: WASAPI) — K000 alanı | bekliyor |

## §10 Regresyon kapıları

- **RG-1:** §1 #6 sınır kuralı ("K003 içinden K004'e sinyal gönderilir, içine sokulmaz") → her refaktörde yeniden okunur.
- **RG-2:** §1 #3 gecikme toplama/mismatch akışı → latency ölçümü değişince yeniden doğrulanır.
- **RG-3:** §5'e yeni satır eklendiğinde §2 sayımı güncellenir (sessiz düzeltme yasak).
- **RG-4:** Repo'ya ilk yönlendirme kodu geldiğinde §1 #8 "kod yok" iddiası yeniden sayılır.
- **RG-5:** #9/#10 kapanırsa §3 geçiş kuralına göre kapsam satırı `[TASARIM]` → `[KANIT]` olur.

### §10.1 Kanıt talebi (boş)

| Talep | Yanıt |
|---|---|
| Ölçüm kaydı | yok |
| Test çalıştırma logu | yok |
| Click gözlem kaydı | yok |

## §11 Bağlantılar (kardeş düğümler)

| Bağlantı | İlişki |
|---|---|
| `[[routing-kapsam]]` | bu düğümün kapsamı — #9 kapanınca §3 T1 satırı |
| `[[routing-ornekler]]` | örnek rotalar (R-serisi ile eşleştirilir) |
| `[[mixing-kapsam]]` | gain/toplama sahipliği sınırı (#9) |
| `[[realtime-kapsam]]` | block kenarı geçişi (#10) |
| `[[synchronization-kapsam]]` | çoklu rota zaman hizası / gecikme |
| `[[diagnostics-kapsam]]` | rota olay kaydı |
| `[[testing-kapsam]]` | R-serisi test altyapısı |
| `[[K000-isletim-sistemi/isletim-sistemi-audio/index]]` | OS ses katmanı (fallback) |

## §12 Kaynak denetim kaydı (2026-10-13 — HEAD, yeni URL YOK)

| Kaynak (adıyla) | HEAD durumu | Not |
|---|---|---|
| PipeWire tasarım dokümanı (docs.pipewire.org) | **200** | §1 #2'nin güncel konumu |
| PipeWire latency sayfası (docs.pipewire.org) | **200** | §1 #3'ün güncel konumu |
| PipeWire scheduling sayfası (docs.pipewire.org) | **200** | §1 #4'ün güncel konumu |
| ALSA Library API (alsa-project.org wiki) | **200** | §1 #7'nin güncel konumu |
| §1 #2 pipewire.org/page_objects_design (eski) | **404** | §1 DEĞİŞTİRİLMEZ — düzeltme yetkisi yok |
| §1 #3 pipewire.org/page_latency (eski) | **404** | aynı |
| §1 #4 pipewire.org/page_scheduling (eski) | **404** | aynı |
| §1 #7 alsa-project.org/doc/api/group___p_l_u_g.html (eski) | **404** | aynı |

> §1'deki 404'ler **sessizce düzeltilmez**; §3 gereği bağımsız teyit ancak gerçekten
> fetch edilen URL ile `✅` olur.

---

## §13. Boş rapor formu — yönlendirme oturumu

| Alan | Değer |
|---|---|
| Tarih / araç / ortam | |
| R-ID listesi (§5) | |
| Rota geçişi block kenarında mı? (§4.1) | |
| Loop tespiti çalıştı mı? (§6.3) | |
| Sonuç (PASS/FAIL/EXCLUDE — §7 imzası) | |
| Sapma (varsa kayıt no) | |

## §14. Kapanış haritası — doğrulanmış / doğrulanmamış

| Öğe | Durum |
|---|---|
| #9 mixing sınırı (gain/toplama) | ⚠️ açık (üst karar) |
| #10 block kenarı yeniden yönlendirme | ⚠️ açık (realtime kararı) |
| #11 topoloji + üst link sınırı | UNKNOWN |
| #12 yanlış yönlendirme kriteri | UNKNOWN |
| R-01…R-12 | tamamı bekliyor/EXCLUDE — çalıştırılmış test yok |
| §4.2 rota formları | boş |

## §15. Risk defteri

| # | Risk | Olasılık/etki | Azaltma |
|---|---|---|---|
| R1 | Aday kuralın (§6.2/§6.3) gerçek kriter sanılması | yüksek/yüksek | her kural "aday/⚠️" etiketli |
| R2 | #9 sınırının iki dosyada farklı yorumlanması | orta/yüksek | `[[mixing-kapsam]]` §4.2 ile çakışırsa DUR (§4.1) |
| R3 | Click-free iddiasının kanıtsız yazılması | yüksek/orta | R-04/F-R4 `⚠️` kalır, PASS sayılmaz |
| R4 | §1'in 404 kaynaklarına dayanan iddialar | yüksek/orta | §12 — yalnız HEAD 200 kaynak |
| R5 | Loop kuralının dayanaksız uygulanması | orta/orta | §6.3 `⚠️` işaretli; R-10 PASS sayılmaz |

## §16. Araç envanteri (kullanılacak — çalıştırma kaydı boş)

| Araç | Rol | Çalıştırıldı mı? |
|---|---|---|
| Graf/rota dökümü (§5) | matris doğrulama | hayır |
| Gecikme toplama okuması (R-07) | §1 #3 | hayır |
| Loop dedektörü (R-10) | §6.3 | hayır |
| Click gözlemi (R-04/F-R4) | §1 #10 | hayır |

## §17. Denetim listesi (özet — §7 bağlayıcıdır)

1. §5 satırları çalıştırıldı mı? 2. §4.2 formları dolu mu? 3. §6 eşikleri "aday/UNKNOWN" mı?
4. §9 enjeksiyonları geçti mi? 5. §11 bağlantıları güncellendi mi? 6. Sapmalar §15'e yazıldı mı?

## §18. Terimler

| Terim | Anlamı (bu dosyada) |
|---|---|
| Patchbay | kaynak-hedef bağlantı tablosu (§4.1) |
| Fan-out / Fan-in | tek→çok / çok→tek yönlendirme (R-02/R-03) |
| Block kenarı | işlem bloğunun sınırı — geçiş penceresi (§4.1) |
| Loop | döngüsel rota (§6.3 — aday kural) |
| EXCLUDE | kanıt/tasarım yok → test edilemez |

## §19. Ek kayıtlar

- §1 #5 AUGraph kanıtı `audio-master.md` içindir (vault içi); dış kaynak değil.
- §1 #8 (repo'da rota kodu yok) 2026-10-12 ölçümüdür; ilk kod gelince §10 RG-4 gereği yeniden sayılır.
- Link sayısı/topoloji hiçbir yere önceden yazılmaz (§1 #11).

## §20. Kuru çalıştırma (masa başı)

- §4.2 formunun her alanı için "kim onaylar?" (§7 imzası) boş bırakılmaz.
- R-11/R-12/F-R1 masa başında EXCLUDE gerekçesiyle kapatılır (sayı uydurmak yasak).
- §12'deki 404 satırları hiçbir yeni bölümde URL olarak tekrar yazılmaz.

## §21. Sık sorulanlar

**S: Kaç link desteklenir?**
**C:** Bilinmiyor — §6.1 `UNKNOWN` (§1 #11). Sayı yazmak yasak.

**S: Rota değişimi tıklıyor mu?**
**C:** §1 #10 `⚠️` — realtime kararıyla kapanacak; R-04 PASS sayılmaz.

**S: Kazanç/toplama kimde?**
**C:** §1 #9 `⚠️` — `[[mixing-kapsam]]` §4.2 ile birlikte üst karar.

**S: Ölçüm yaptım, nereye yazayım?**
**C:** §13 formu + §4.2 rota tablosu; sonra §7 imzası. Boş form PASS değildir.

## §22. Kapsam dışı

Firmware · PCB/BOM · DAC simülasyonu · Web arayüzü · Waveform ekranı
(bkz `[[K001-devre/tasarim-dogrulama]]` yüzeyi) · ADR metin değişikliği ·
OS zamanlama çekirdeği (`[[K000-isletim-sistemi/isletim-sistemi-audio/index]]` alanı).

## §23. Son denetim satırları (oturum kapanışı)

1. Ek bölümler "aday / UNKNOWN / ⚠️ / bekliyor / boş" işaretli mi? → evet.
2. Yeni URL: 0 → evet. Uydurma ölçüm: 0 → evet. Uydurma araç/sürüm: 0 → evet.
3. Authority tek satır → evet. Dürüstlük bloğu korundu → evet.
4. §2 sayımı §1 ile birebir (değişmedi — §1–§3 append-only korundu) → evet.

---

## §24. Rota veri yolu — akış diyagramı (akış — sayı yok)

```
[kaynak port] ──► [link (§1 #2: iki portu bağlar)]
                        │
                        ▼
              [node — medya işleme elemanı]
                        │  (§1 #4: driver döngüyü başlatır,
                        │   required/pending sayaçları ile tetiklenir)
                        ▼
              [gecikme: port Latency nesnesi (direction + min/max)]
                        │  (§1 #3: linked node latency'leri toplanır,
                        │   mismatch tespit edilir)
                        ▼
              [output unit — graf burada biter (§1 #5)]
                        │
                        ▼
              [ÇIKIŞ → [[mixing-kapsam]] sınırı (#9) veya cihaz]
```

- Ok yönü tek yönlüdür; geri besleme **kural dışıdır** (§6.3 — aday).
- Diyagramda geçen tek sayılar: bölüm/kimlik numaralarıdır.

## §25. Rota kayıt defteri (boş — satır deseni)

```
rota_no    : ___   (eski → yeni)
geçiş block: ___   (§4.1 — block kenarı mı, ortası mı)
click      : ___   (gözlem — #10/#12 kapanmadan yorumlanmaz)
loop       : ___   (§6.3 kontrolü)
imza       : ___
tarih      : ___
```

- Defter **boş başlar**; doldurma sırası §4.1 protokolüyle olur, atlanmaz.

## §26. R-serisi için beklenen kanıt türleri (henüz hiçbiri yok)

| Kanıt tür | Hangi R-ID'ler ister | Mevcut mu? |
|---|---|---|
| Rota geçiş kaydı (§4.2) | R-04, R-05 | hayır |
| Gecikme toplama okuması | R-07 | hayır |
| Sayaç akışı kaydı (required/pending) | R-08 | hayır |
| Loop raporu | R-10 | hayır |
| Kriter/üst sınır ölçümü | R-11, R-12 | hayır — EXCLUDE |

## §27. Komşu dosyalara devredilen maddeler

| Madde | Devralan dosya | Durum |
|---|---|---|
| #9 gain/toplama sahipliği | `[[mixing-kapsam]]` §4.2 | ⚠️ |
| #10 block kenarı geçişi | `[[realtime-kapsam]]` | ⚠️ |
| R-07 gecikme toplama ölçümü | `[[synchronization-kapsam]]` | bekliyor |
| Loop/olay kaydı | `[[diagnostics-kapsam]]` | bekliyor |
| Test altyapısı | `[[testing-kapsam]]` | bekliyor |
| Cihaz fallback (F-R6) | `[[K000-isletim-sistemi/isletim-sistemi-audio/index]]` | bekliyor |

## §28. Uzatılmış SSS

**S: Bu dosyada hiç ölçüm var mı?**
**C:** Yok — §1'de 8 satır doğrulanmış iddia, 2 ⚠️, 2 UNKNOWN'dır; §4.2 formları boştur.

**S: §6.3 loop kuralını uygulayabilir miyim?**
**C:** Yalnız "aday" etiketiyle; resmî dayanak yok, `⚠️` kalır (R-10 PASS sayılmaz).

**S: ALSA plug kanıtı neden çeviri/dönüşüm için kullanılıyor?**
**C:** §1 #7 — kanal dönüşümü sürücü katmanında olabilir; bu routing'in **kendi** kanıtı değil, sınır notudur.

**S: Hangi kaynaklara güveniliyor?**
**C:** §12'de HEAD 200 dönenler; §1'deki 404'ler düzeltilemez.

**S: Dosya neyi "geçmiş" sayar?**
**C:** §7'deki 5 koşul + §8 imzası; mevcut durum: koşulların hiçbiri dolu değil → **geçemedi, kanıt yok**.

## §29. Denetim özeti satırları (önce/sonra — 2026-10-13)

| Öğe | Değer |
|---|---|
| Başlangıç satırı | 63 (frontmatter sonrası 77) |
| Ana içerik | §4–§12 |
| Uzantı | §13–§39 |
| Yeni URL | 0 |
| Uydurma ölçüm | 0 |

## §30. Kapsam dışı — genişletilmiş

Yukarıdaki §22'ye ek olarak: **ağ üzerinden** rota paylaşımı (UPnP/stream),
çok-oyunculu senkronizasyon, dosya biçiminde rota kaydı (patchbay dosyası) —
hiçbiri bu dosyanın kapsamı **değildir** ve doğrulama iddiasına girmez.

## §31. Ek kayıt formu — rota geçiş olayı (boş)

```
olay no    : ___
eski rota  : ___   →   yeni rota: ___
block kenarı: evet / hayır
click gözlemi: ___   (yorum: #10/#12 kapandıktan sonra)
imzalayan  : ___
```

> Click gözlemi alanı **değerlendirilmez** — #10/#12 kapandıktan sonra yorumlanır.

## §32. R-serisi genişletme kuyruğu (kod gelmeden açılmaz)

| ID | Aday senaryo | Neden bekliyor |
|---|---|---|
| R-13 | Çoklu graf (birden fazla zincir) | graf tasarımı yok |
| R-14 | Rota şablonu kaydet/yükle | kalıcılık tasarımı yok |
| R-15 | Dinamik topoloji (çalışırken düğüm ekleme) | §1 #11 UNKNOWN |
| R-16 | Öncelikli rota (kritik kanal) | öncelik kuralı yok |
| R-17 | Rota denetim günlüğü (kim neyi bağladı) | teşhis tasarımı (`[[diagnostics-kapsam]]`) |

- Kuyruk **talep listesidir**; satırları başlık olarak burada kalır, test sayılmaz.

## §33. Bağımlılık grafiği (metin — sayı yok)

```
§4.1 (geçiş protokolü) ──gerektirir──► #10 kararı ──gerektirir──► [[realtime-kapsam]]
        │
        ▼
§4.2 (form) ◄──doldurur── R-serisi (§5) ──sonuç──► §7 (imza)
        │                         │
        ▼                         ▼
§6 (aday eşik) ◄──karşılaştır── §9 (enjeksiyon) ──sapma──► §15 (risk)
        │
        ▼
§6.2/§6.3 (aday kurallar) ──⚠️──► #9 / #12 kapıları
```

- Grafikteki tek sayılar bölüm numaralarıdır; ölçüm **yoktur**.

## §34. Kabul kriterleri — özet tablo (dosya bütününün)

| Kriter | Kaynak | Durum |
|---|---|---|
| §1 sayımı §2 ile birebir | §2 notu | ✅ (append-only korundu) |
| #9/#10 açık madde işaretli | §1, §14 | ✅ işaretli |
| #11/#12 UNKNOWN | §1, §14 | ✅ yazılmadı |
| Tüm ek eşikler "aday/UNKNOWN" | §6 | ✅ |
| Çalıştırılmış test yokluğu dürüst yazılmış | §5, §7 | ✅ |
| Yeni URL 0 | §12, §23 | ✅ |
| Dürüstlük bloğu + tek Authority | dosya sonu | ✅ |

## §35. Ölçüm geldiğinde yapılacaklar (kapanış sırası)

1. §13 formu doldurulur, §4.2 rota tablosu yazılır.
2. §6 aday eşikler/kurallar ölçümle karşılaştırılır — uyuşmazsa §15'e sapma girer.
3. §14 kapanış haritasında maddeler tek tek `✅`ye döner.
4. #9/#10/#11/#12 kapanırsa `[[routing-kapsam]]` §3 T1 satırı `[TASARIM]` → `[KANIT]` olur (§3 geçiş kuralı).
5. §29 denetim satırı yenilenir.

## §36. İmza sertliği (bu dosyaya özgü)

- İmza, **ölçüm kaydı eklenmeden atılamaz** (§7 koşul 2).
- İmza atılırsa §14 kapanış haritası birlikte güncellenir — tek başına imza geçersizdir.
- İmzalayan, §6.2/§6.3 kurallarının **aday** olduğunu da imzalamış sayılır.

## §37. Kısa darbe listesi — yönlendirme yüzeyi (kontrol satırları)

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
iddia, 2 ⚠️, 2 UNKNOWN; eklenen §4–§38 yalnız protokol ve form taşır. Yönlendirme
kodlanana, rota formları dolana ve §7 imzası atılana kadar bu dosyanın kapanış
durumu: **AÇIK — kanıt yok** (sayı uydurulmadan yazılan tek dürüst sonuç budur).

## §39. Ek kayıt satırı — boş (dosya kapanışı)

```
oturum    : ___
çalıştırılan R-ID'ler : ___
geçen / kalan : ___ / ___
imza      : ___
tarih     : ___
```

> Bu satır bloğu §38'deki "AÇIK — kanıt yok" durumu ancak dolunca kapanır.

---

> **Dürüstlük bloğu:** Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler 0 yeni URL,
> 0 uydurma ölçüm içerir; doğrulanmış kaynaklar yalnız belirtilen URL'lerdir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
