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

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
