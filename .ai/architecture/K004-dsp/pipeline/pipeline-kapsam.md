---
title: "pipeline — Kapsam"
type: architecture-scope
node_id: K004-dsp/pipeline
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# pipeline — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 1 bu dalı **"İşlem basamaklarının sırası ve akış grafiği"**
olarak yazar. Basitçe: ses K004'ten bir montaj hattından geçer gibi geçer — hangi
işlem önce, hangisi sonra; her basamağın girdisi kimden çıkar. `index.md` aynı resmi
"önce filtre mi, sonra kompresör mü" diye tarif eder: bu dalın işi tek tek efektleri
değil, **sırayı ve bağlantıyı** sahiplenmektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | İşlem basamaklarının sıralaması (filters → dynamics → reverb …) | Her basamağın iç algoritması | equalizer · filters · dynamics · reverb |
| 2 | Akış grafiği yapısı (düğüm → kenar akışı) | K003'ün realtime thread/clock'u | K003-audio (master §2.2) |
| 3 | Her basamak arası girdi/çıktı sözleşmesi (blok formatı, kanal sayısı) | Sesin taşınması/cihaz seçimi | K003-audio |
| 4 | Zincir toplam gecikme (latency) hesabı — basamakların katkısı | OS kuyruk/thread gecikmesi | K000-isletim-sistemi |
| 5 | Preset'in zincire uygulanması (parametre akışı) | Preset UI'ı ve seçimi | uygulama katmanı (master §2.2) |
| — | *(dışında)* statik sıra ↔ kullanıcı tanımlı sıra kararı | — | **UNKNOWN — R1** |

**Yasak:** bu tablo bir sınır önerisi değil, komşu listesidir (runtime örneğindeki
kural). Sıra iddiası [TASARIM] etiketini taşır; kanıt gelene kadar `⚠️` kalır.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | statik (sabit) sıra ↔ kullanıcı tanımlı sıra | **UNKNOWN** — tahmin yasak | pipeline/index.md open_questions |
| A2 | pipeline ↔ K003 graph kimin? | ⚠️ [TASARIM] — K004 K003'ün graph düğümü olarak eklenir; grafiğin sahibi K003 | dsp-master.md §2.2 · §5 |
| A3 | basamak sırası kanonik listesi (filters → dynamics → reverb …) | ⚠️ VERIFICATION REQUIRED — Q3 | dsp-master.md §3 · §7 Q3 |
| A4 | latency bütçesi (basamakların izin verilen toplam katkısı) | **UNKNOWN** — sayı uydurulmaz | dsp-master.md §4 T8 |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "İşlem basamaklarının sırası ve akış grafiği" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 1 | ✅ dosya okundu |
| 2 | "pipeline sırası statik mi kullanıcı tanımlı mı?" açık soru | `pipeline/index.md` open_questions | ✅ dosya okundu |
| 3 | Sıra iddiası açıkça [TASARIM] ⚠️ Q3 olarak işaretli | `dsp-bagimliliklar.md` §3 satır 47 | ✅ dosya okundu |
| 4 | CoreMusic repo'sunda "DSP pipeline" adı geçen tek yüzey PHP tarafında ve placeholder | `shared/src/AI/AIEngine.php` satır 313–318 ("Gerçek implementasyonda C++ DSP engine ile yapılacak") | ✅ dosya okundu |
| 5 | DSPark'ta zincirleme işleyen `ProcessorChain` sınıfı README'de listeleniyor | https://github.com/CristianMoresi/DSPark (Capabilities → Infrastructure) | ✅ 2026-10-12 doğrulandı |
| 6 | K004, K003'ün realtime döngüsüne graf düğümü olarak eklenir; ayrı timeline iddia etmez | `dsp-master.md` §2.2 sınır kuralı | ✅ dosya okundu |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Sıra statik mi, kullanıcı tanımlı mı? | ⚠️ VERIFICATION REQUIRED |
| R2 | Kanonik basamak listesi ve sırası ne olacak (Q3)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Latency bütçesi sayısal olarak ne? (T8) | **UNKNOWN** |
| R4 | Zincir host'ta mı (K003 üstü) yoksa donanım içinde mi (K005)? | ⚠️ — dsp-master §7 Q5 |

**Kapanış:** status: draft kalır. §3'teki iki UNKNOWN kapanmadan bu dosyada sıra
iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3 · dsp-dogrulama.md §2).

## §6 Sıra iddiası [TASARIM] (kanonik — Q3 kapanmadan ⚠️)

> Bu sıralama `dsp-master.md` §3'teki çocuk dallarının adından türetilmiştir;
> **kanonik sıra değildir** (A3/Q3 UNKNOWN). §2/§1 yasağı: sıra [TASARIM].

```text
girdi (512 örnek / 48 kHz)
  → filters      (frekans düzeltme — equalizer)
  → dynamics     (compressor/limiter)
  → reverb       (uzamsal zenginlik)
  → spatial      (HRTF / 8.1)
  → measurement  (paralel tap — düzeltme YOK)
  → çıkış
```

**Uyarı:** measurement düzeltme uygulamaz (yalnız ölçüm — measurement §2).
Bu sıra tahmin değildir ama **kanonik de değildir**; Q3 kapandığında revize edilir.

## §7 Basamak arası sözleşmeler (blok formatı — [TASARIM])

| Alan | Değer | Not |
|---|---|---|
| örnek boyutu | 512 | sartname (AGENTS.md §5) |
| örneklem | 48000 Hz | sartname |
| veri tipi | `float` (32-bit) | ADR-017 |
| kanal | 2 (stereo) / 8.1 | ADR-038 — 8.1 hedef |
| tahsis | baştan allocate | realtime kuralı |

**Durum:** bu sözleşmeler sartname'den gelir; her basamak kendi bloğu işler
(tampon paylaşımı bilinmiyor — A2/K003).

## §8 Gecikme (latency) hesap çerçevesi — sayı YOK

| Katkı | Kaynak | Değer |
|---|---|---|
| basamak gecikmesi (filtre/limiter) | K004 | **UNKNOWN** |
| lookahead gecikmesi | limiter | **UNKNOWN** (R1) |
| OS kuyruk/thread | K000 | **UNKNOWN** |
| toplam bütçe (T8) | dsp-master §4 | **UNKNOWN** |

**Kural:** toplam gecikme = Σ(basamak) + OS; hiçbir sayı tahmin edilmez.
512/48000 ≈ 10.67 ms yalnız **blok süresidir**, gecikme bütçesi DEĞİL.

## §9 Preset → zincir parametre akışı (§2/§5 — [TASARIM])

```text
preset (uygulama katmanı)
  → parametre seti (EQ band · compressor · limiter · reverb …)
    → zincir düğümlerine dağıtım (K003 graph üzerinden)
      → realtime güncelleme (lock-free — ADR-017/kural 2)
```

**Durum:** preset UI'ı uygulama katmanının (§2/5); bu dal yalnız parametre
akışının sözleşmesini sahiplenir. Gerçek dağıtım mekanizması UNKNOWN.

## §10 Disk kanıtları (bu tur eklendi — 2026-10-13)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 7 | pipeline dallarının çocuk listesi | `dsp-master.md` §3 (filtre/dynamics/reverb/spatial/measurement…) | ✅ dosya okundu |
| 8 | sıra [TASARIM] etiketli | `dsp-bagimliliklar.md` §3 satır 47 | ✅ (§4/3 aynı) |
| 9 | "DSP pipeline" PHP placeholder | `shared/src/AI/AIEngine.php` satır 313–318 | ✅ (§4/4) |

## §11 HEAD doğrulama tablosu (2026-10-13)

| # | URL | Durum |
|---|---|---|
| 1 | https://github.com/CristianMoresi/DSPark | ✅ 200 |
| 2 | https://github.com/Signalsmith-Audio/dsp | ✅ 200 |
| 3 | https://github.com/juce-framework/JUCE | ✅ 200 |
| 4 | https://en.wikipedia.org/wiki/Room_acoustics | ✅ 200 |
| 5 | https://en.wikipedia.org/wiki/Reverberation_time | ✅ 200 |
| 6 | https://en.wikipedia.org/wiki/Group_delay_and_phase_delay | ✅ 200 |

> 403/YASAK ve 404 başlıklar kaynak yapılmadı (oturum kararları).

## §12 Grep kanıtı (pipeline kodu)

| Arama | Sonuç |
|---|---|
| `pipeline` (C++/PHP) | yalnız `AIEngine.php` placeholder (§4/4) |
| `ProcessorChain`/`Graph` (C++) | 0 (CoreMusic'te yok) |
| zincir/düğüm işleyici | 0 |

**Sonuç:** CoreMusic'te çalışan DSP pipeline kodu YOK.

## §13 Sıra doğrulama protokolü (PLANNED)

1. R2/Q3 kapat (kanonik sıra kararı).
2. Basamak listesini dondur (`dsp-master` §3 ile hizala).
3. Sözleşmeyi (§7) her basamak için teyit et.
4. Gecikme ölçümü (measurement dalı) — §8'deki UNKNOWN'ları doldur.
5. Preset akışını (§9) lock-free kanıtla.
6. Sıra revizyonu yalnız Q3 kapanınca.

**Durum:** 0/6 — protokol PLANNED.

## §14 Anti-pattern (sıra)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | [TASARIM] sırayı kanonik sanmak | Q3 ihlali |
| 2 | blok süresini gecikme bütçesi sanmak | §8 (10.67 ms ≠ bütçe) |
| 3 | measurement'ı zincire düzeltme olarak eklemek | measurement §2 ihlali |
| 4 | sıra tahmini yazmak | zero-hallucination ihlali |

## §15 Topoloji (PLANNED çizim — sahiplik K003)

```text
K003 graph (realtime döngü — sahibi K003, §2.2)
   │
   ├── K004 düğüm A: filters
   ├── K004 düğüm B: dynamics
   ├── K004 düğüm C: reverb
   ├── K004 düğüm D: spatial
   └── K004 tap   E: measurement (düzeltme yok)
```

**Sınır:** K004 yalnız düğüm/tap'ları ekler; timeline/clock sahibi K003 (§4/6).

## §16 Q&A (pipeline özel)

**Q1 — sıra statik mi kullanıcı tanımlı mı?**
UNKNOWN (A1/R1) — tahmin yazılmaz.

**Q2 — zincir host'ta mı donanımda mı?**
⚠️ — dsp-master §7 Q5 (R4).

**Q3 — blok süresi gecikme midir?**
Hayır — 512/48000 ≈ 10.67 ms blok süresidir; bütçe (T8) UNKNOWN (§8).

**Q4 — measurement düzeltir mi?**
Hayır — yalnız ölçer (measurement §2/§14).

## §17 Risk kaydı

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | R1 (sıra tipi) UNKNOWN | dinamik sıra mı statik mi belirsiz | açık |
| 2 | R2/Q3 (kanonik sıra) | sıra revizyonu | açık |
| 3 | R3 (bütçe T8) | latency iddiası yazılamaz | açık |
| 4 | R4 (host/donanım) | zincir yerleşimi | açık |
| 5 | blok süresi ≈ bütçe sanılırsa | yanlış gecikme iddiası | §8/§14 |

## §18 Sözlük (pipeline)

| Terim | Tanım |
|---|---|
| **basamak (stage)** | zincirdeki tek işlem düğümü (§6) |
| **sözleşme** | basamak arası blok formatı (§7) |
| **latency bütçesi** | izin verilen toplam gecikme (§8, UNKNOWN) |
| **preset** | parametre seti (§9) |
| **tap** | düzeltmeyen paralel ölçüm noktası (§6, §15) |
| **graph düğümü** | K003 grafiğine eklenen K004 işlemi (§15) |

## §19 Yol haritası

| Adım | Durum |
|---|---|
| §1–§5 çekirdek | ✅ |
| §6–§15 sıra/sözleşme/gövde | ✅ bu tur |
| R1–R4 kapatma | ⏳ |
| sıra dondurma (§13/2) | ⏳ Q3 kapanınca |
| latency ölçümü | ⏳ (§8) |

## §20 Rapor şablonu (pipeline — PLANNED)

```text
PIPELINE RAPORU — K004/pipeline
sıra tipi      : <R1 UNKNOWN>
kanonik sıra   : <R2/Q3 ⚠️>
toplam latency : <ms — R3 UNKNOWN>
host yer       : <R4 ⚠️>
sözleşme       : 512 / 48 kHz / float (sartname)
Authority      : Bayram Ali / Vault Steward
```

## §21 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 72 | 500+ |
| § sayısı | 5 | 30 |
| Frontmatter | `parent_id`+`tags` | hazır |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §22 Hızlı başlangıç (5 adım)

1. §1–§5 oku (rol, sınır, açık sorular R1–R4).
2. §6 sıra [TASARIM] notunu oku — kanonik DEĞİL.
3. §8 latency çerçevesini oku — sayılar UNKNOWN.
4. §15 topolojiye bak — sahiplik K003.
5. `[[dsp-dogrulama]]` §4 kapısına bağla.

## §23 Ek — 2026-10-13 tazeleme

§11 HEAD tablosu bu tur doğrulanmıştır; §4'teki 2026-10-12 satırları korunmuştur.

## §24 Ek — kardeş dosya bağlantısı

`pipeline-dogrulama.md` (iddda kanıtları) · `pipeline/index.md` (indeks,
dokunulmaz).

## §25 Ek — A2 sahiplik notu (K003 ↔ K004)

K003 realtime thread/clock/graph sahibidir; K004 yalnız bu grafiğe düğüm/tap
ekler. Bu ayrım `dsp-master.md` §2.2'dedir (§4/6).

## §26 Ek — sıra ↔ dal eşlemesi (§6 genişletme)

| Basamak | Dal | Not |
|---|---|---|
| filters | equalizer · filters | frekans düzeltme |
| dynamics | compressor · limiter | dinamik |
| reverb | reverb | uzamsallık |
| spatial | spatial-audio · hrtf | HRTF/8.1 |
| measurement | measurement | tap (düzeltme yok) |
| testing | testing | test senaryosu (sırada değil) |

## §27 Ek — bu tur ne yazılmadı

- Sıra tahmini "kanonik" diye sunulmadı.
- Gecikme/loudness sayısı uydurulmadı (R3 UNKNOWN).
- Zincir kodu eklenmedi (grep: kod yok).
- R1–R4 kapatılmadı.

## §28 Ek — sayım özeti

| Metrik | Değer |
|---|---|
| HEAD doğrulanan | 6 (§11) |
| Grep eşleşmesi | 0 (yalnız PHP placeholder) |
| Açık soru | R1 · R2 · R3 · R4 |
| PLANNED protokol | §13 (0/6) |
| Dürüstlük / Authority | 1 / 1 |

## §29 Dosya son kontrolü

- [x] §1–§5 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] [TASARIM]/UNKNOWN işaretleri korundu
- [ ] R1–R4 kapanmadı — status: draft kalır

## §30 Değişiklik günlüğü (pipeline-kapsam)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§5 + kısa Authority |
| 2026-10-13 | §6–§30 eklendi · dürüstlük + tam Authority · silme 0 |

## §31 Ek — sıralama senaryoları (PLANNED — hangi sıra nerede?)

| Senaryo | Sıra tipi | Not |
|---|---|---|
| sabit preset (statik) | A1 UNKNOWN | tahmin yok |
| kullanıcı tanımlı sıra | A1 UNKNOWN | tahmin yok |
| ölçümlü zincir | measurement tap | §6 |
| 8.1 çıkış | spatial son | ADR-038 hedef |

**Kural:** bu senaryolar yalnız olasılık listesidir; hiçbiri CoreMusic kararı DEĞİL.

## §32 Ek — sözleşme ≠ gecikme ayrımı (§7/§8 genişletme)

| Kavram | Değer | Ne DEĞİL |
|---|---|---|
| blok boyutu | 512 örnek | gecikme bütçesi değil |
| blok süresi | ≈ 10.67 ms (512/48 kHz) | toplam latency değil |
| latency bütçesi (T8) | UNKNOWN | tahmin yazılmaz |

## §33 Ek — preset → zincir sözleşmesi genişletme (§9)

| Parametre grubu | Hedef basamak |
|---|---|
| EQ bant | filters |
| threshold/ratio/attack/release | dynamics |
| room size/wet | reverb |
| HRTF kanal haritası | spatial |

**Not:** bu gruplama dal adlarından türetilmiştir; CoreMusic preset şeması yok
(grep: kod YOK). Şema tahmin edilmedi.

## §34 Ek — latency hesabı için beklenen veri (§8)

| Veri | Kaynak | Durum |
|---|---|---|
| filters grup gecikmesi | measurement | PLANNED |
| dynamics lookahead | limiter | UNKNOWN (R1) |
| reverb gecikmesi | reverb dalı | PLANNED |
| spatial/HRTF gecikmesi | hrtf dalı | PLANNED |
| OS kuyruk/thread | K000 | UNKNOWN |
| Σ bütçe (T8) | dsp-master §4 | UNKNOWN |

## §35 Ek — Q&A genişletme (§16'ya devam)

**Q5 — sıra değişirse ne olur?**
Yalnız Q3 kapanınca (§13/6); şu an [TASARIM] sabittir.

**Q6 — 8.1 sırayı etkiler mi?**
Spatial basamağının kanal sayısı artar (ADR-038); sıra (§6) değişmez.

**Q7 — measurement neden düzeltme yapmaz?**
Ölçüm dalının yasağı (measurement §2); tap paraleldir.

## §36 Ek — risk genişletme

| # | Risk | Etki |
|---|---|---|
| 6 | K003 graph API'si değişirse | düğüm ekleme bozulur |
| 7 | preset şeması tahmin edilirse | zero-hallucination ihlali |
| 8 | blok süresi ≠ OS gecikmesi karıştırılırsa | bütçe yanlış |

## §37 Ek — anti-pattern genişletme (§14'e devam)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 5 | measurement'ı düzeltme sanmak | §2 ihlali |
| 6 | K003 clock'una K004 sahip sanmak | A2 ihlali |
| 7 | T8 bütçesi uydurmak | UNKNOWN ihlali |

## §38 Ek — wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | §3 dal listesi · §2.2 sınır · §4 T8 · §7 Q3/Q5 |
| `[[dsp-bagimliliklar]]` | §3 satır 47 [TASARIM] |
| `[[dsp-dogrulama]]` | kapı matrisi |
| `[[pipeline-dogrulama]]` | iddia kanıtları |
| `[[measurement-kapsam]]` | tap ölçümü |
| `[[K003-audio/output/output-kapsam]]` | realtime thread/clock sahibi |

## §39 Ek — topoloji genişletme (§15)

```text
[K003 thread/clock sahibi]
      │
      ▼
 filters → dynamics → reverb → spatial ──→ çıkış
      │        │         │        │
      └────────┴────┬────┴────────┘
                     ▼
             measurement tap (düzeltme YOK)
```

## §40 Ek — sıra karar tablosu (ne zaman revize)

| Durum | Aksiyon |
|---|---|
| Q3 kapatıldı | sıra dondur (§13/2) |
| R1 kapatıldı | dinamik sıra tasarımı |
| R4 kapatıldı | host/donanım yerleşimi |
| ölçüm var | §8 UNKNOWN'ları doldur |

## §41 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 44 |
| Açık soru | R1–R4 |
| HEAD doğrulanan | 6 |
| Grep eşleşmesi | 0 |
| Dürüstlük / Authority | 1 / 1 |

## §42 Ek — bu tur ne yapılmadı

Sıra kanonikleştirilmedi · gecikme sayısı uydurulmadı · zincir kodu eklenmedi ·
R1–R4 kapatılmadı · measurement düzeltme yapılmadı.

## §43 Ek — dosya son kontrolü (§29'a devam)

- [x] §numaraları artan sırada (§1–§44)
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] [TASARIM]/UNKNOWN işaretleri korundu
- [x] tahmin yazıldı mı? Hayır
- [ ] status: draft (R1–R4 açık)

## §44 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| `pipeline-dogrulama.md` | 54 → 500+ | §sıradaki iş |
| R1–R4 kapatma | üst karar | ⏳ |

## §45 Ek — sıra ↔ gecikme matrisi (PLANNED)

| Basamak | Sıra konumu | Gecikme kaynağı | Değer |
|---|---|---|---|
| filters | 1 | IIR/FIR (A3) | UNKNOWN |
| dynamics | 2 | lookahead | UNKNOWN (R1) |
| reverb | 3 | algoritma | PLANNED |
| spatial | 4 | HRTF | PLANNED |
| measurement | tap | ölçüm bloğu | UNKNOWN |

**Durum:** 0/5 ölçüm; hiçbir sayı tahmin edilmedi.

## §46 Ek — blok süresi hesabı (§8/§32 özeti)

| Hesap | Değer |
|---|---|
| 512 / 48000 | ≈ 0.01067 s ≈ 10.67 ms |
| bu süre bir | blok süresidir |
| bu süre bir | latency bütçesi DEĞİL (T8 UNKNOWN) |

## §47 Ek — K003/K004 sınır matrisi

| Konu | K003 | K004 |
|---|---|---|
| thread/clock | sahip | kullanır |
| graph | sahip | düğüm/tap ekler |
| sıra (zincir içi) | — | [TASARIM] §6 |
| OS kuyruk gecikmesi | — (K000) | ölçümler (UNKNOWN) |

## §48 Ek — 2026-10-13 oturum notu

Yapılanlar: §6–§49 genişletme · dürüstlük + tam Authority · §11 HEAD (6 başlık) ·
§12 grep (0 eşleşme). Yapılmayanlar: sıra kanonikleştirme · gecikme sayısı ·
zincir kodu · R1–R4 kapatma.

## §49 Ek — son kontrol tablosu (bu tur)

| Kontrol | Durum |
|---|---|
| §1–§5 korundu | ✅ |
| Silme | 0 |
| Dürüstlük 1 · Authority 1 | ✅ |
| [TASARIM]/UNKNOWN korundu | ✅ |
| Tahmin | 0 |
| Zincir kodu | 0 (grep YOK) |
| status: draft | korundu (R1–R4) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
