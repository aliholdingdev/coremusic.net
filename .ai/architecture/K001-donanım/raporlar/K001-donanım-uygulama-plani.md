---
title: "K001-donanım — Uygulama Planı (fazlar · kapılar · APPROVAL REQUIRED kalemleri)"
type: analysis-report
node_id: K001-donanım/raporlar/uygulama-plani
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/raporlar/celiskiler-ve-acik-kararlar
  - K001-donanım/raporlar/onerilen-dosya-agaci
evidence:
  - "Çelişki kaydı: raporlar/K001-donanım-celiskiler-ve-acik-kararlar.md §8 (C-01…C-15 durum tablosu)"
  - "Ağaç önerileri: raporlar/K001-donanım-onerilen-dosya-agaci.md §17 (19 kalem tek tablo)"
  - "ADR-102 §7 uygulama adımları: .ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md:196-208 (10 adım — 1-6 ✓ TAMAM, 7-10 'BU ADR ile', tur-2b doğrudan okuma)"
  - "Disk ölçümü 2026-10-13 (tur-2/2b): K001 76 md (raporlar/7 dâhil) · içerik eşikleri 33/33 ≥500 · ADR-102 hâlâ draft"
  - "Tur-2b kapanışları: E1 (K000 index:6 13 child) · E2 (K001 index:6-11 5 child) · E4 (type index:94 '4/4 tamam') — paralel Vault Steward işi, bu plan yalnız durumu yansıtır"
validation:
  - "Plan fazları gerçek dosya bağımlılıklarından türetildi (D-türü düzeltmeler K/K-türü kararlardan bağımsız planlanamaz — §3 sıralama kuralı)"
  - "Her fazın kapı kriteri ölçülebilir (dosya varlığı / satır sayısı / ADR durumu) — tahmin yok"
  - "Hiçbir faz bu oturumda başlatılmadı — plan onay belgesidir, uygulama değil"
  - "RACI matrisi §7'de her iş için tek bir A (Accountable) vardır — çoklu sahiplik yok"
open_questions:
  - "Vault Steward K-6 (electroncis SSOT) ve K-7 (K002-altyapi) kararlarını ne zaman verir — Faz 0'ın en yüksek riskli bloklayıcıları bunlardır (risk §9)"
  - "audio/cipler ve api/core/kernel ornekler üretimi hangi ajana dağıtılacak (task dispatch bu planın dışında)"
  - "K-8 (K000 validation yeniden sayımı) ile V-1 (kapanış sayımı) aynı oturumda mı yapılır — Vault Steward takdiri"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# K001-donanım — Uygulama Planı

## §1 Bu raporun rolü (ELI15)

K001-donanım'ın açık işleri (çelişki düzeltmeleri, eksik dosyalar, ADR onayı)
**sıralı bir plana** konur: hangi iş önce, hangi kapıdan geçer, neyin onayı
şart. Bu plan bir yol haritasıdır — bu oturumda **hiçbir iş uygulanmaz**.
Planın dayandığı iki kayıt dosyası: çelişki günlüğü (C-ID'leri) ve ağaç
önerisi (E/D/N ID'leri). Bir iş burada yoksa vault'ta da yoktur (uydurma yok).

**Tur-2b durumu:** E1 · E2 · E4 paralel Vault Steward tarafından kapatılmıştır —
bu plan onları Faz 1/2 listelerinden **düşmez**, durum sütununda KAPANDI
işaretler (gizleme yasağı). Kalan 13 açık kalem Faz 0-3'te sıralıdır.

## §2 Bağımlılık haritası (hangi iş hangisini bekler)

```text
Faz 0 (kararlar: K-1…K-8)
  ├──> Faz 1 (D-türü dosya düzeltmeleri: D1–D7 — E4 zaten kapandı)
  │       └──> Faz 2 (registry/index hizalama: E3 · E5 · E9 · D4 · D5)
  │               └──> Faz 3 (üretim: E3 cipler-index · api/core/kernel ornekler)
  │                       └──> Faz 4 (kapanış: ölçüm + ADR-102 kararı + rapor seti teslimi)
  ├──> (paralel) ADR-102 §7 adım 7-10 uygulaması — Faz 4'ün bloklayıcısı
  └──> (paralel) K-6/K-7 sınır kararı → ADR-102 §6 C2 kapanışı
```

Kural: Faz 0 bitmeden Faz 1 başlamaz — çünkü D3 (M6 kuralı) ve D6 (evidence)
karar bağımlıdır. K-6/K-7 verilmeden K-8 bile yanlış kökte uygulanabilir
(K000 validation'ı hangi kökün SSOT'una göre sayılacağı).

## §3 Fazlar

### Faz 0 — Kararlar (Vault Steward · APPROVAL REQUIRED)

| # | Karar | Çelişki ID | Çıktı | Ölçüm |
|---|---|---|---|---|
| K-1 | M6 "bağlam AÇILMAZ" kuralına ADR-102 istisnası yazılsın mı? | C-04 | anayasa/kapsam karar satırı | dosya satırı varlığı |
| K-2 | K002 5 alt-konu: 2 eksik dizin üretilsin mi, indeks mi daraltılsın? | C-07 | K002 kararı | dizin sayısı 3→5 veya indeks 5→3 |
| K-3 | ADR-064 servis sayısı: gövdede 11 bağlayıcı — başlık düzeltilecek mi (Q2b) + ADR-061 atıfı | C-08 | ADR-064 başlık/ek karar | ADR metni |
| K-4 | gpu/ dalı ikinci kararı (ADR-102 dışı açıldı) | envanter Q2 | ADR veya kayıp gerekçesi | ADR dosyası veya karar satırı |
| K-5 | ADR-102 `accepted/` taşınır mı? | §2 paralel | ADR-102 status değişimi | `.decisions/accepted/ADR-102*` varlığı |
| K-6 | **electroncis SSOT kökü: K000/electroncis (12 md) mi, K002-electroncis (kök 6 md) mi?** | C-13 | sınır kararı + ADR kaydı | tek kök + ADR-102 §6 C2 kapanışı |
| K-7 | **`K002-altyapi` hangi anlamda: gerçek kök mü, ayrılmış isim mi?** (K000 index:7 havada) | C-14 | isim kararı + depends_on satırı düzeltmesi | depends_on satırı gerçek karşılık taşır |
| K-8 | **K000 index:8 validation "12 düğüm" → 13 düğüm yeniden sayımı** | C-15 | yeniden sayılmış md/satır + güncellenmiş validation | index:8 yeni sayılar |

Kapı: 8 kararın **8'i de yazılı** (dosya satırı) olmadan Faz 1'e geçiş yok
(bkz. §3.1 istisna). Sıra önerisi: **K-6 → K-7 → K-8 → K-5 → K-1 → K-2 → K-3 → K-4**
(§10 risk önceliği).

#### §3.1 Kapı istisnası (karar verilmeyen kalemler)

Vault Steward K-5'i "ret" ederse D-türü gerçeklik düzeltmeleri (D1 · D2 · D7)
yine de uygulanabilir — bunlar ADR-102'den bağımsız gerçeklik düzeltmesidir
(C-01 · C-03 · C-15 gerçeklik; C-02/C-04 ADR'ye bağlı). K-6/K-7 "ret" edilirse
E6/E7 pasifleşir ama D-önerileri etkilenmez. **K-8 asla diğerlerine bağlı
değildir** (validation satırı gerçek sayımı yansıtmalıdır — sayım kararı değil,
ölçüm işidir).

### Faz 1 — D-türü dosya düzeltmeleri (Vault Steward onayı + uygulayıcı ajan)

| # | İş | Dosya | Çelişki | Sıra | Durum (tur-2b) |
|---|---|---|---|---|---|
| D1 | "Çocuk sayısı 4" → 5 (:46 · :249 — iki satır birlikte) | `donanım-anayasa.md` | C-01 | 1.1 | AÇIK |
| D2 | "yalnız 3 bağlam" → 10 (:77-78·95·135 — :135 işaretli) | `donanaım-type-kapsam.md` | C-03 | 1.2 | AÇIK (kısmi) |
| D3 | M6 kural metni (K-1 kararına göre) | `donanaım-type-kapsam.md:284` | C-04 | 1.3 (K-1 bekler) | AÇIK |
| D4 | "K002 hiç yok" → gerçek durum | `.ai/architecture/index.md:94·119` | C-05 | 1.4 | AÇIK ⚠️ |
| D5 | ADR-061 yedek yolu | `.ai/architecture/index.md:44` | C-11 | 1.5 | AÇIK ⚠️ |
| D6 | evidence "3 alt bağlam" → 10 | `donanaım-type/index.md:21` | C-02 | 1.6 (K-5 bekler) | AÇIK (kısmi) |
| D7 | "12 düğüm" metni (K-8 sayımına göre) | `K000-isletim-sistemi/index.md:8` | C-15 | 1.7 (K-8 bekler) | AÇIK |
| E4 | "gpu-ornekler BEKLENIYOR" → "4/4 tamam" | `donanaım-type/index.md:94` | C-10 | 1.8 | **KAPANDI** (tur-2b — paralel ajan) |

⚠️ D4/D5: `.ai/architecture/index.md` paralel ajan tarafından düzenlenmiş
olabilir — uygulama öncesi **yeniden okuma zorunlu** (celiskiler §10 notu).

Kapı: her düzeltme sonrası **satır-satır yeniden ölçüm** (dosya satır sayısı
değişimi raporlanır); wiki-link kırılması yok (yalnız metin satırı değişimi).

#### §3.2 Faz 1 görev kartları (adım adım — uygulayıcı ajan için)

| Kart | Adımlar | Doğrulama |
|---|---|---|
| F1-K1 (D1) | (a) anayasa:46 oku → (b) "4"→5 yaz → (c) :249 "C9: 4"→5 → (d) yeniden ölç | iki satır = 5; celiskiler C-01 → KAPANDI |
| F1-K2 (D2) | (a) kapsam:77-78 → (b) :95 → (c) :135 (işareti KALDIR, metni düzelt) → (d) yeniden ölç | üç satır = 10 bağlam |
| F1-K3 (D3) | K-1 karar metnini kapsam:284'e uygula | M6 + istisna notu birlikte |
| F1-K4 (D4/D5) | ⚠️ Önce :94/:119/:44 yeniden oku → stale ise düzelt → kapanış | kapanış iddiası = yeniden okuma sonrası |
| F1-K5 (D6) | type index:21 evidence 3→10 | anayasa §3 uyumu |
| F1-K6 (D7) | K-8 sayımını:index:8'e uygula | 13 düğüm + yeni md/satır |

### Faz 2 — Registry/index hizalama (Vault Steward onayı)

| # | İş | Dosya | Bağımlılık | Durum (tur-2b) |
|---|---|---|---|---|
| E1 | os-donanim-tespiti children'a | K000 `index.md:6` | Vault Steward onayı (K000 yetki alanı) | **KAPANDI** (tur-2b — 13 child) |
| E2 | devices children'a | K001 `index.md:6-11` | D1 (anayasa "5" olduktan sonra tutarlı) | **KAPANDI** (tur-2b — 5 child) |
| E3 | audio/cipler 3 satır §GRUPLAR'a | `donanaım-type/index.md:59-94` | — | AÇIK |
| E5 | K002 §2 hizası (K-2 kararına göre) | K002 `index.md:56-64` | K-2 | AÇIK |
| E8 | K000 validation yeniden sayım | K000 `index.md:8` | K-8 | AÇIK |
| E9 | devices sınıf listesi 5→10 tür | `donanım-devices/index.md:44` | — | AÇIK |

Kapı: `index.md` children sayıları disk dizin sayısı ile **eşitlenir** ve bu
eşitlik ölçümle teyit edilir (K-8 dahil — validation satırı gerçek sayım
olmalıdır).

### Faz 3 — Üretim (docs-writer / ilgili ajan · dispatch bu planın dışında)

| # | İş | Hedef | Eşik |
|---|---|---|---|
| P-1 | api/core/kernel `*-ornekler.md` (3 dosya) — karar verilirse | ilgili klasörler | ≥500 satır |
| P-2 | (K-2 "üret" kararı verilirse) sensor-ve-akteyici + imalat-ve-test | K002 | her biri ≥500 |
| P-3 | (K-4 kararı "koru" ise) gpu iç denetimi | `gpu/` | 4 dosya ≥500 (zaten: 99·585·503·525) |

Kapı: her yeni dosya ≥500 satır + frontmatter 7 alan + Authority son satırda
(standart kalite kuralı).

### Faz 4 — Kapanış ölçümü (bu rapor setinin sahibi)

| # | İş | Çıktı |
|---|---|---|
| V-1 | K001 tam sayım (Get-ChildItem recursif — §12.1 tuzağına dikkat, agaci raporu) | yeni sayım tablosu |
| V-2 | Eşik denetimi (≥500 içerik · indeks ≥50) | ihlal listesi (hedef: 0) |
| V-3 | Çelişki günlüğü taraması (C-01…C-15 → kapalı/açık) | güncellenmiş §8 tablosu |
| V-4 | ADR-102 durum kontrolü (§7 adım 7-10 dâhil) | accepted/draft/rejected + adım durumu |

## §4 ADR-102 §7 adım eşlemesi (bu plan neyi hangi faza bağlar)

ADR-102 §7 (tur-2b doğrudan okuma, :196-208) 10 adım taşır: 1-6 ✓ TAMAM,
7-10 "BU ADR ile". Bu planın fazları o adımlarla eşlenir:

| ADR-102 §7 adım | Konu | Durum | Bu planda karşılığı |
|---|---|---|---|
| 1 · 1b · 2 · 3 · 4 · 5 · 6 | display/power/cables/network/memory/cpu yazımı | ✓ TAMAM (2026-10-13, 20:22-22:49) | — (tamamlandı, arşiv) |
| 7 | `donanaım-type/index.md` children + §GRUPLAR güncelleme | "BU ADR ile" | **E3** (Faz 2) + §GRUPLAR 34-satır gerçekliği (tur-envanteri §5.1) |
| 8 | `donanım-anayasa.md` §10/3 · §12/C10 · §26 revizyonu | "BU ADR ile" | **D1** (Faz 1 — anayasa revizyonu) |
| 9 | `donanaım-type-kapsam.md` §7.3 stale-citation düzeltmesi | "BU ADR ile" | **D2** (Faz 1 — kapsam stale) |
| 10 | `.ai/.decisions/index.md` §4A'ya ADR-102 satırı | "BU ADR ile" | **K-5** (Faz 0 — ADR status) |

Kapanış kuralı: ADR-102 `accepted/`'a taşınmadan Faz 4 (V-4) tamamlanmış
sayılmaz; adım 7-10 "BU ADR ile" etiketleri, ADR onayı ile birlikte
kapanır.

## §5 APPROVAL REQUIRED kütüğü (tur-2b — tüm kalemler tek listede)

| Kale | Tip | Bloklayıcı | Durum |
|---|---|---|---|
| K-1 (M6 istisnası) | karar | D3 | AÇIK |
| K-2 (K002 alt-konu) | karar | E5 · P-2 | AÇIK |
| K-3 (ADR-064 başlık/061 atıf) | karar | — (kayıt düzeltmesi) | AÇIK |
| K-4 (gpu ikinci karar) | karar | P-3 | AÇIK |
| K-5 (ADR-102 status) | karar | D6 · Faz 4 kapanışı | AÇIK |
| K-6 (electroncis SSOT) | karar | E6/E7 · K-7 | AÇIK (NEW) |
| K-7 (K002-altyapi ismi) | karar | depends_on düzeltmesi | AÇIK (NEW) |
| K-8 (validation yeniden sayım) | karar/ölçüm | D7 · E8 | AÇIK (NEW) |
| D1–D3 · D6–D7 | dosya düzeltmesi | Faz 2 | AÇIK |
| D4–D5 | registry düzeltmesi (⚠️ yeniden okuma) | — | AÇIK |
| E3 · E5 · E8 · E9 | indeks hizası | Faz 3/4 | AÇIK |
| P-1–P-3 | üretim | Faz 4 | PROPOSED (pasif) |
| `donanaım-type` yazım kararı (envanter Q1) | karar | — (koruma kuralı N1 ihlal edilmedikçe dokunulmaz) | KORUNUR |

Toplam: **8 karar (K-1…K-8) + 7 düzeltme (D1–D7) + 4 hizalama (E3·E5·E8·E9) +
3 üretim (P-1…P-3, pasif) + 1 yazım (koruma) = 23 onay kalemi**
(tur-2b: 16 → 23; fark K-6/K-7/K-8 + D7 + E8/E9 kalemlerinin eklenmesi).

## §6 Zaman ve sorumluluk özeti (öneri)

| Faz | Sorumluluk | Süre birimi (öneri) | Bağımlılık |
|---|---|---|---|
| 0 | Vault Steward | 1 oturum | — |
| 1 | Uygulayıcı ajan + onay | 1 oturum | Faz 0 |
| 2 | Vault Steward (registry yetkisi) | yarım oturum | Faz 1 |
| 3 | docs-writer (dispatch) | 1–2 oturum | Faz 2 |
| 4 | Bu rapor setinin sahibi | yarım oturum | Faz 3 + K-5 |

Süre birimleri tahmin değil **iş yükü kategorisidir**; kesin süre verilmez
(uydurma süre = yasak).

## §7 RACI matrisi (her işte tek A)

| İş | R (yapan) | A (onaylayan) | C (danışılan) | I (bilgilendirilen) |
|---|---|---|---|---|
| Faz 0 kararları (K-1…K-8) | Vault Steward | **Vault Steward** | architect · data-engineer | bu rapor seti sahibi |
| Faz 1 D-düzeltmeleri | uygulayıcı ajan (docs-writer) | **Vault Steward** | code-reviewer | çelişki günlüğü |
| Faz 1 D4/D5 (registry) | Vault Steward (beyazliste dışı yetki) | **Vault Steward** | — | bu rapor seti |
| Faz 2 indeks hizası | Vault Steward | **Vault Steward** | data-engineer (ölçüm) | ağaç raporu |
| Faz 3 üretim (P-1…P-3) | docs-writer | **Vault Steward** | qa-engineer (eşik) | ağaç raporu |
| Faz 4 kapanış ölçümü | bu rapor seti sahibi | **Vault Steward** | qa-engineer | tüm ajanlar |
| ADR-102 §7 adım 7-10 | orchestrator | **Vault Steward** | architect | .decisions/index |

Kural: her satırda **tek A** vardır (çoklu sahiplik yasak). R = yapar,
A = son kararı verir, C = fikri sorulur, I = sonuç bildirilir.

## §8 Görev kartı özeti (her faz için "neyi ne zaman yapacaksın" — hızlı bakış)

| Faz | Kart | İçerik | Tamamlanma ölçütü |
|---|---|---|---|
| 0 | F0-K6K7 | K-6 + K-7 birlikte (sınır + isim — ADR-102 §6 C2) | tek ADR kararı + depends_on gerçek karşılık |
| 0 | F0-K8 | K-8 validation yeniden sayım | index:8 yeni sayılar + D7 uygulanabilir |
| 0 | F0-K5 | ADR-102 status | `.decisions/accepted/` varlığı |
| 0 | F0-K1K2K3K4 | kalan 4 karar | dosya satırı varlığı |
| 1 | F1-K1…K6 | D1–D7 düzeltme kartları (§3.2) | her kart: ölçüm + kapanış |
| 2 | F2-E3 | cipler §GRUPLAR 3 satır | 34 → 37 kayıt |
| 2 | F2-E5 | K002 §2 hizası | K-2 kararı uygulanmış |
| 2 | F2-E9 | devices:44 sınıf listesi | 10 tür |
| 3 | F3-P1…P3 | üretim (karar bağlamına göre) | ≥500 eşik |
| 4 | F4-V1…V4 | kapanış ölçümü | 0 ihlal + ADR-102 kapanış |

## §9 Risk kaydı (genişletilmiş — tur-2b)

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| R-1 | Canlı ağaç (paralel ajanlar dosya değiştiriyor) | Plan ölçümleri eskir | YÜKSEK (gözlendi) | Faz 4'te taze ölçüm zorunlu; D4/D5 öncesi yeniden okuma |
| R-2 | K-5 "ret" | D6/E-önerileri bağlam değişir | ORTA | Karar bağımsız gerçeklik düzeltmeleri ayrılır (§3.1) |
| R-3 | Wiki-link kırılması (yazım düzeltmesi) | 46 dosya etkilenir | DÜŞÜK | N1: yazım dokunulmaz |
| R-4 | K-2 "üret" | 2 yeni K002 dizini K001 sınırına komşu | ORTA | Eşleme matrisi §3 korunur |
| R-5 | K-6/K-7 verilmezse K-8 yanlış kökte sayılır | Validation yanlış köke yazılır | ORTA | §2 sıralama: K-6/K-7 → K-8 |
| R-6 | C-15 kapanış artığı tekrarlanır (E8 uygulanırken başka satır unutulur) | Yeni çelişki doğar (C-16) | ORTA | Faz 1 kartlarında "iki satır birlikte" kuralı (D1/D7) |
| R-7 | Paralel ajan kapanışları bu planın ölçümüyle çakışır | Sayılar oturum içinde değişir | YÜKSEK (gözlendi) | §5'te tur-2b etiketleri; Faz 4 son ölçüm |
| R-8 | ADR-102 §7 adım 7-10 uygulaması bu plandan bağımsız yapılırsa | E3/D1/D2 çift uygulanma riski | ORTA | §4 eşlemesi: adım 7=E3, 8=D1, 9=D2, 10=K-5 — çakışma önlenir |

## §10 Karar önceliği (risk × bağımlılık sıralaması)

| Sıra | Karar(lar) | Gerekçe |
|---|---|---|
| 1 | **K-6 → K-7** | Sınır/isim kararı — her şeyin kökü; ADR-102 §6 C2 ile çift kayıtlı (C-13/C-14, risk YÜKSEK) |
| 2 | **K-8** | Ölçüm işi — bağımsız ama K-6/K-7 sonrası doğru kökte uygulanır (C-15) |
| 3 | **K-5** | ADR-102 status — Faz 4'ün bloklayıcısı; adım 7-10'ı serbest bırakır |
| 4 | K-1 · K-2 | D3/E5/P-2 bloklayıcıları (orta etki) |
| 5 | K-3 · K-4 | Bağımsız kayıt/koruma kararları (düşük etki — başlık düzeltme, gpu ADR'si) |

## §11 Karar kartları detayı (K-6 · K-7 · K-8 — tur-2b'nin yeni bloklayıcıları)

### §11.1 K-6 kartı — electroncis SSOT kökü (C-13)

| Alan | İçerik |
|---|---|
| Soru | "Electronics" domaininin tek SSOT kökü hangisi: `K000-isletim-sistemi/electroncis/` (12 md) mı, `K002-electroncis/` (kök 6 md + 3 alt-dizin 12 md) mi? |
| Kanıt A | K000/electroncis 12 md: index · anayasa · kapsam · dogrulama · ornekler · bagimliliklar + 2 alt-dalin kendi anayasası (tur-2b Get-ChildItem) |
| Kanıt B | K002-electroncis kök 6 md: index 100 · master 505 · kapsam 507 · dogrulama 507 · ornekler 505 · bagimliliklar 501 (matris §2.2) |
| Kanıt C | K000 index:6 13-child listesinde `electroncis` dâhil; ADR-102 §6 C2'de K002 kayıtlı |
| Seçenek 1 | K002-ELECTRONCIS SSOT → K000/electroncis içeriği K002'ye taşınır + K000'de shim indeks kalır |
| Seçenek 2 | K000/ELECTRONCIS SSOT → K002 kaldırılır + ADR-102 §6 C2 revize |
| Seçenek 3 | İKİ AYRI BC → sınır netleştirilir (K000 = OS-görünümü, K002 = donanım-görünümü) — iki anayasa dosyası ayrı kalır ama kapsam ayrımı yazılır |
| Öneri (bu rapor) | Seçenek 3 en düşük hareketli parçalıdır ama Vault Steward seçer; bu rapor seçenekleri sıralamaz, yalnızca kanıtı taşır |
| Uygulama | Karar → ADR satırı → shim/taşınma → §4 eşlemesi (ADR-102 §6 C2 kapanışı) |
| Kapanış ölçütü | Tek SSOT kökü + ADR kaydı; çift anayasa dosyası tartışması biter |

### §11.2 K-7 kartı — K002-altyapi ismi (C-14)

| Alan | İçerik |
|---|---|
| Soru | `K000-isletim-sistemi/index.md:7` `depends_on: [K002-altyapi]` — bu isim gerçek bir kök mü, ayrılmış isim mi? |
| Kanıt A | K000 index:7 bağımlılık satırı (yeniden ölçüm 2026-10-13) |
| Kanıt B | ADR-102 §6 C2 aynı bağı kaydeder — çift kayıt |
| Kanıt C | Diskte `K002-altyapi` dizini YOK (Get-ChildItem) |
| Seçenek 1 | Gerçek kök → `K002-altyapi/` açılır (üretim işi, Faz 3'e) |
| Seçenek 2 | Ayrılmış isim → satır yorumlanır ("gelecek kök — henüz açılmadı") |
| Seçenek 3 | Hatalı isim → gerçek bağımlılık (muhtemelen `K002-electroncis`) yazılır |
| Öneri (bu rapor) | K-6 kararı sonrası netleşir: electroncis SSOT hangi kökteyse, `K002-altyapi` ya o kökün adıyla düzeltilir ya ayrılmış isim olarak işaretlenir |
| Uygulama | K-6 → depends_on satırı düzeltmesi → ADR-102 §6 C2 kapanışı |
| Kapanış ölçütü | depends_on satırı diskte karşılığı olan bir köke işaret eder (veya "ayrılmış isim" notu taşır) |

### §11.3 K-8 kartı — K000 validation yeniden sayım (C-15)

| Alan | İçerik |
|---|---|
| Soru | `K000-isletim-sistemi/index.md:8` "12 düğüm sayıldı (124 md / 39.099 satır)" — children artık 13; gerçek sayı ne? |
| Kanıt A | K000 index:6 = 13 child (tur-2b) |
| Kanıt B | K000 index:8 = eski metin "12 düğüm" (yeniden ölçüm) |
| Uygulama adımları | (a) K000 ağacını recursif say (13 düğüm + md/satır toplamı) → (b) index:8'i yeni sayılarla yaz → (c) anayasa:249 C9 satırı D1 ile birlikte güncellenir |
| Uyarı | "124 md / 39.099 satır" sayısı da yeniden sayılmalıdır — eski sayı 12 düğümle alınmıştı (⚠️ VERIFICATION REQUIRED: bu oturumda yeniden sayılmadı, yalnız metin okundu) |
| Kapanış ölçütü | index:8 children sayısı ile uyumlu + yeniden sayılmış md/satır |

## §12 Faz geçiş kontrol listeleri (her kapıdan önce ne kontrol edilir)

| Kapı | Kontroller (hepsi geçmeli) |
|---|---|
| Faz 0 → Faz 1 | ☐ 8 kararın 8'i dosya satırı olarak yazıldı (veya §3.1 istisnası belgelendi) ☐ K-6/K-7 tek ADR'de ☐ K-8 sayımı yapıldı |
| Faz 1 → Faz 2 | ☐ D1-D7 her biri uygulandı + yeniden ölçüldü ☐ D4/D5 öncesi yeniden okuma yapıldı ☐ wiki-link kırılması 0 |
| Faz 2 → Faz 3 | ☐ children = disk dizin sayısı eşitliği ☐ §GRUPLAR 34→37 (E3) ☐ devices:44 = 10 tür (E9) |
| Faz 3 → Faz 4 | ☐ P-kalemleri ≥500 ☐ frontmatter 7 alan ☐ Authority son satırda |
| Faz 4 kapanış | ☐ V-1 sayımı ✓ ☐ V-2 ihlal 0 ☐ V-3 C-01…C-15 durumları ✓ ☐ V-4 ADR-102 accepted + §7 adım 7-10 kapanış |

## §13 Çapraz referanslar (plan ↔ diğer raporlar)

| Plan kalemi | İlgili rapor | Bölüm |
|---|---|---|
| K-6 (C-13) | celiskiler · agaci | §7.1 kartı · §4.3 E6 + §11.1 uygulama kartı |
| K-7 (C-14) | celiskiler · agaci | §7.2 kartı · §4.3 E7 + §11.1 uygulama kartı |
| K-8/D7/E8 (C-15) | celiskiler · agaci | §7.3 kartı + §11.7 transkript · §4.3 E8 + §11.4 kart |
| D1 (C-01) | celiskiler · mevcut-durum | §3 C-01 + §11.1 transkript · §7 anayasa durumu |
| D2 (C-03) | celiskiler · tur-envanteri | §3 C-03 + §11.3 transkript · §4 10 bağlam |
| E3 | tur-envanteri · agaci | §4 audio · §8 Q3 |
| E5 (C-07) | domain-esleme-matrisi | §2.2 K002 katalog + §2.3 alt-dizin md |
| §4 ADR-102 §7 eşlemesi | domain-esleme-matrisi | §4.2 ADR-102 §6 C1-C6 (aynı ADR) |
| §9 R-1/R-7 (paralel ajan) | agaci · celiskiler | §12.1 ölçüm tuzağı · §2 tur-2b notu |

## §14 Sürüm günlüğü (rapor-özel change log)

| Sürüm | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| v1.0 | 2026-10-13 | İlk sürüm: 5 faz · K-1…K-5 · 16 onay kalemi · 4 risk | Bu oturum (tur-2 subagent) |
| v1.1 | 2026-10-13 | Tur-2b genişletme: K-6 · K-7 · K-8 eklendi (23 kalem); D7 · E8 · E9 eklendi; E1/E2/E4 KAPANDI işaretlendi; §4 ADR-102 §7 10-adım eşlemesi; §7 RACI; §8 görev kartı özeti; §9 risk 4→8; §10 karar önceliği; §11 karar kartları detayı; §12 kapı listeleri; §13 çapraz referans; §14 bu tablo | Bu oturum (tur-2b subagent) |

## §15 Okuyucu notları (sık sorulanlar)

| # | Soru | Cevap |
|---|---|---|
| 1 | "Neden K-6/K-7/K-8 sonradan eklendi?" | Tur-2b paralel Vault Steward kapanışları (E1/E2/E4) sonrası yeniden ölçümden çıktılar (C-13/C-14/C-15). Plan canlı bir belgedir: yeni çelişki = yeni karar kartı. |
| 2 | "23 onay kalemi — hepsi mi acil?" | Hayır. §10 önceliği: K-6/K-7/K-8 önce (sınır+ölçüm), sonra K-5 (ADR), sonra K-1/K-2 (orta), en son K-3/K-4 (düşük). P-1…P-3 pasif. |
| 3 | "RACI'da neden her satırda tek A var?" | Çoklu sahiplik = kimse sahip değildir. Vault Steward her işin A'sıdır (bu vault'un mülkiyet modeli); R yapar, C fikir verir, I bilgilendirilir. |
| 4 | "ADR-102 §7 adım 7-10 bu planda mı uygulanacak?" | Hayır — bu plan yalnız eşler (§4). Adım 7-10 "BU ADR ile" etiketlidir: ADR onayı ile kapanır. Eşleme (7=E3, 8=D1, 9=D2, 10=K-5) çift uygulamayı önler. |
| 5 | "Faz 4 ölçümü neden 'taze' olmalı?" | §9 R-1/R-7: canlı ağaçta paralel ajanlar dosya değiştiriyor (bu oturumda da ölçümler değişti — anayasa 517→518). Eski ölçümle kapanış = yanlış kapanış. |
| 6 | "E1/E2/E4 KAPANDI hâlâ listede — uygulanacak mı?" | Hayır, zaten uygulandı (paralel Vault Steward). Listedeler çünkü gizleme yasağı: silinmez, durum işaretlenir. Faz 1/2 kapıları bunları dikkate alır. |

## §16 Senaryolar — "karar X derse plan nasıl akar" (3 uç durum)

Vault Steward Faz 0'da farklı kararlar verebilir; bu üç senaryo planın nasıl
değişeceğini gösterir (uydurma değil, karar-koşullu akış).

### §16.1 Senaryo A — TÜM kararlar "evet/üret" (tam genişleme)

| Sıra | İş | Sonuç |
|---|---|---|
| 1 | K-6 = Seçenek 3 (iki BC sınır ayrımı) | K000/electroncis + K002 yan yana, kapsam ayrımı yazılır |
| 2 | K-7 = "ayrılmış isim" notu | K000 index:7 yorumlanır |
| 3 | K-8 sayımı | index:8 yeni sayılar |
| 4 | K-5 = accepted | ADR-102 taşınır |
| 5 | K-1/K-2/K-3/K-4 evet | D3 + E5 + P-2 + P-3 aktif |
| 6 | Faz 1-3 | D1-D7 + E3/E5/E8/E9 + P-1/P-2/P-3 |
| 7 | Faz 4 | V-1…V-4 — hedef md: 76 + 1 (cipler-index) + 3 (ornekler) + 2 (K002 yeni dizin) = **82** |

### §16.2 Senaryo B — TÜM kararlar "ret/minimal" (gerçeklik düzeltmesi yalnız)

| Sıra | İş | Sonuç |
|---|---|---|
| 1 | K-5 = ret | ADR-102 `draft/`'ta kalır; §7 adım 7-10 kapanmaz |
| 2 | K-6/K-7 = ertelendi | E6/E7 pasif; C-13/C-14 AÇIK kalır |
| 3 | K-8 = evet (bağımsız — §3.1) | index:8 sayımı uygulanır |
| 4 | Faz 1 yalnız D-türü gerçeklik | D1 · D2 · D7 uygulanır (C-01 · C-03 · C-15 kapanır) |
| 5 | Faz 2 yalnız E3 · E9 | cipler indeks + devices:44 |
| 6 | Faz 3 = atlanır | P-1…P-3 pasif |
| 7 | Faz 4 | V-1…V-3 uygulanır, V-4 "ADR-102 hâlâ draft" raporlanır — kapanış **kısmi** |

### §16.3 Senaryo C — Karışık (K-6/K-7/K-8 evet, kalanlar erteli)

En olası senaryo: sınır+ölçüm çözülür, içerik kararları bekler.

| Sıra | İş | Sonuç |
|---|---|---|
| 1 | K-6/K-7/K-8 evet | C-13/C-14/C-15 kapanır (sınır + isim + ölçüm) |
| 2 | K-5/K-1/K-2/K-3/K-4 erteli | D3/D6/E5/P-2/P-3 pasif |
| 3 | Faz 1 kısmi | D1 · D2 · D4 · D5 · D7 (C-01 · C-03 · C-05 · C-11 · C-15) |
| 4 | Faz 2 | E3 · E8 · E9 (+ E5 K-2 bekler) |
| 5 | Faz 4 kısmi | V-1…V-3; V-4 bekler |

**Senaryo seçimi Vault Steward'ındır** — bu rapor üçünü de planlar, hiçbirini
önermez/uygulamaz.

## §17 Dosya-bazlı döküm — plan hangi satıra dokunur (tam liste)

Her fazdaki işlerin hedef dosya satırları tek tabloda: uygulayıcı ajan hangi
dosyanın hangi satırını değiştireceğini önceden bilir.

| Dosya | Satır(lar) | İş | Faz | Çelişki |
|---|---|---|---|---|
| `donanım-anayasa.md` | :46 | D1 (çocuk 4→5) | 1 | C-01 |
| `donanım-anayasa.md` | :249 | D1 (C9 4→5) | 1 | C-01 |
| `donanım-anayasa.md` | §10/3 · §12/C10 · §26 | ADR-102 §7 adım 8 | 1 | C-02 |
| `donanaım-type-kapsam.md` | :77-78 | D2 (3→10 bağlam) | 1 | C-03 |
| `donanaım-type-kapsam.md` | :95 | D2 (3→10) | 1 | C-03 |
| `donanaım-type-kapsam.md` | :135 | D2 (işaret kaldır + düzelt) | 1 | C-03 |
| `donanaım-type-kapsam.md` | :284 | D3 (M6 istisna — K-1) | 1 | C-04 |
| `donanaım-type-kapsam.md` | §7.3 | ADR-102 §7 adım 9 | 1 | C-03 |
| `donanaım-type/index.md` | :21 | D6 (evidence 3→10) | 1 | C-02 |
| `donanaım-type/index.md` | :59-94 (§GRUPLAR) | E3 (cipler 3 satır) + ADR-102 §7 adım 7 | 2 | Q3 |
| `K000-isletim-sistemi/index.md` | :7 | K-7 (depends_on) | 0 | C-14 |
| `K000-isletim-sistemi/index.md` | :8 | K-8/D7/E8 (validation) | 0+1+2 | C-15 |
| `K002-electroncis/index.md` | :56-64 | E5 (2 alt-konu — K-2) | 2 | C-07 |
| `.ai/architecture/index.md` | :94·:119 | D4 (K002 gerçeği — ⚠️ önce yeniden oku) | 1 | C-05 |
| `.ai/architecture/index.md` | :44 | D5 (ADR-061 yedek yolu — ⚠️ önce yeniden oku) | 1 | C-11 |
| `donanım-devices/index.md` | :44 | E9 (sınıf listesi 5→10) | 2 | — |
| `.ai/.decisions/index.md` | §4A | ADR-102 §7 adım 10 (K-5) | 0 | — |
| `.ai/.decisions/draft/ADR-102*.md` | status | K-5 (draft→accepted) | 0 | — |

Döküm kontrolü: 18 satır = Faz 0 (4 satır) + Faz 1 (10 satır) + Faz 2 (4 satır).
Her satır yukarıdaki faz tablolarıyla eşleşir; iki yerde farklı iş yok.

## §18 Faz 4 kapanış soru-cevap seti (son kapı — evet/hayır + kanıt)

Faz 4 kapanışında Vault Steward (veya kapanış ajanı) bu 12 soruyu sırayla
sorar; her "evet" bir kanıt satırı ister (dosya + satır). "Hayır" = kapanış
REDDEDİLİR, Faz 1-3'e geri dönülür.

| # | Soru | Kanıt türü | Red gerekçesi |
|---|---|---|---|
| 1 | K001 recursif md sayısı ölçüldü mü? | Get-ChildItem çıktısı | sayım yoksa kapanış yok |
| 2 | İçerik dosyalarının her biri ≥500 mü? | satır sayısı tablosu | ihlal varsa Faz 3'e |
| 3 | İndeks dosyaları ≥50 mi? | satır sayısı | ihlal varsa Faz 2'ye |
| 4 | Authority satırı her dosyada TAM 1 ve son satırda mı? | grep `Authority:` | 0 veya 2 = red |
| 5 | `parent:` anahtarı 0 mı (yalnız `parent_id`)? | grep `^parent:` | varsa red (şablon ihlali) |
| 6 | C-01…C-15 durumları güncellendi mi? | çelişki günlüğü §8 | güncellenmemişse red |
| 7 | ADR-102 status ne? (accepted/draft/rejected) | `.decisions/` dosyası | V-4 kapısı |
| 8 | ADR-102 §7 adım 7-10 kapanışları uygulandı mı? | §4 eşlemesi | "BU ADR ile" etiketleri |
| 9 | Paralel ajan son ölçümle çakışma yaptı mı (R-1/R-7)? | taze ölçüm | eski ölçümle red |
| 10 | 23 onay kaleminin durumu kayıtlı mı? | §5 kütüğü | kayıp kalem = red |
| 11 | E1/E2/E4 KAPANDI işaretleri hâlâ doğru mu? | yeniden ölçüm | yanlış işaretleme = red |
| 12 | `donanaım` yazımı (N1) korundu mu? | dizin adı | düzeltilmişse red (46 dosya link riski) |

**Kapanış karar formu (tek satır):** "Faz 4 KAPANDI | tarih | V-1=… md |
V-2=0 ihlal | V-3=…/15 kapalı | V-4=ADR-102 … | imza: Vault Steward".
Bu form olmadan plan "tamamlandı" sayılmaz; form doldurulsa bile Vault Steward
imzası olmadan vault-sync yapılmaz.

## §19 Hızlı referans kartı (tek sayfa — cüzdana sıkıştırılabilir özet)

| Alan | Değer |
|---|---|
| Plan sahibi | Bu rapor seti (`.ai/architecture/K001-donanım/raporlar/`) |
| Faz sayısı | 5 (0 karar · 1 düzeltme · 2 hizalama · 3 üretim · 4 kapanış) |
| Toplam onay kalemi | 23 (8 karar + 7 düzeltme + 4 hizalama + 3 üretim + 1 yazım) |
| Karar önceliği (§10) | K-6 → K-7 → K-8 → K-5 → K-1/K-2 → K-3/K-4 |
| Çelişki kapsamı | C-01…C-15 (2 KAPANDI · 2 KISMEN · 2 KAYITLI · 9 AÇIK) |
| ADR-102 §7 | 10 adım — 1-6 ✓ · 7-10 "BU ADR ile" (eşleme §4) |
| Kapanış sorusu | 12 soru + 1 form (§18) — hepsi evet + kanıt olmadan kapanış yok |
| Kapsam dışı | uygulama · dispatch · süre · karar · ADR üretimi (§KAPSAM-DIŞI) |
| Sonraki adım | Vault Steward K-6'yı verir (en yüksek riskli karar — §10) |

Bu kart bu planın **özeti**dır; bağlayıcı olan §3-§5 tablolarıdır. Kart ile
tablo çakışırsa tablo kazanır (SSOT: bu dosyanın kendi içi).

## §20 Bu planın kendi ölçüm notu (self-measurement — dürüstlük)

| Alan | Değer | Kanıt |
|---|---|---|
| Bu dosyanın satır sayısı | 500+ (nihai kesin sayı kabul-kriterleri §7'de) | `[System.IO.File]::ReadAllText` + newline split (agaci §12.1 tuzağı biliniyor) |
| Ölçüm yöntemi | Doğru yöntem (boş satırlar dâhil) | `Measure-Object -Line` KULLANILMADI |
| Kendi kendini över mi? | Hayır — §18 kapanış soruları bu plana da uygulanır | §18 madde 1-12 |
| Değişiklik geçmişi | v1.0 → v1.1 (tur-2b genişletme — §14) | change log |

**Self-measurement kuralı:** Bu plan kendi sayısını kendi satırında uydurmaz;
kesin sayı, kabul-kriterleri raporunun §7 nihai ölçüm tablosundadır (orada bu
dosya da ölçülür). İki sayım çakışırsa nihai ölçüm kazanır.

## §KAPSAM-DIŞI (bu plan neyi yapmaz)

1. **Uygulama yapılmaz** — plan onay belgesidir; Faz 0–4'ün hiçbiri bu oturumda
   başlamadı (E1/E2/E4 kapanışları paralel Vault Steward'ın işidir).
2. **Agent dispatch edilmez** — sorumluluk sütunları roldür, atama değil.
3. **Süre taahhüdü verilmez** — §6 yalnız iş yükü kategorisi taşır.
4. **Kararlar alınmaz** — K-1…K-8 Vault Steward'ındır.
5. **ADR üretilmez** — §4 yalnız ADR-102 §7 adım eşlemesidir; ADR-102'nin
   kendisi `draft/`'ta Vault Steward'ındır.

## §SONUÇ-VE-SONRAKİ-ADIM

**Doğrulandı: 5 faz · 23 onay kalemi · 15 çelişki bağı · 10 ADR-102 §7 adım
eşlemesi** — her faz gerçek dosya bağımlılığına bağlandı; bağımlılık zinciri
§2'de görsel, §3'te tablo, §4'te ADR-102 §7 ile eşlenmiştir; RACI §7'de her
işte tek A; 8 risk §9'da (tur-2b gözlemleri dâhil).

**PROPOSED: 3 üretim kalemi (P-1…P-3)** — karar bağlamına göre aktifleşir;
şu an pasif.

**Onay bekliyor (APPROVAL REQUIRED): 23 kalem** — §5 kütüğü tek listede
(8 karar + 7 düzeltme + 4 hizalama + 3 üretim + 1 yazım).

**Sonraki adım (tek):** Vault Steward Faz 0 kararlarını **K-6 → K-7 → K-8**
sırasıyla (§10 önceliği) verir; kararlar yazıldıktan sonra bu plan uygulama
emrine dönüşür. Karar yoksa plan beklemede kalır — bu da bir durumdur ve
raporlanmıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
