---
title: "testing — Örnekler"
type: architecture-examples
node_id: K003-audio/testing
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# testing — Örnekler (K003)

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**Ses motoru testi — yok.** Ama **test altyapısı gerçektir** (PHP tarafı):

```text
shared/tests/ — 22 dosya, PHPUnit ^10.5/^11.0 (composer.json require-dev kanıtı;
                envanter: .ai/AGENTS.md §25.2 — dosya okundu 2026-10-12)
.github/workflows/ — ci.yml, secret-scan.yml (envanter ölçümü 2026-10-12)
```

Bu altyapı **K003 ses motorunu test etmez** (repo'da `*.cpp`/`*.h` glob 0
isabet, 2026-10-12). Aranan: render callback testi, xrun senaryosu, latency
eşik assertion'ı. Bulunan: yalnız PHP unit testleri — K003 testing dalının
**senaryo kanıtı olarak kullanılması yasaktır**.

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | `auval` bağımsız doğrulama aracı | `audio-master.md` §4 A6 (Apple AudioUnitDevelopmentFundamentals — bu oturumda vault'tan okundu) | plug-in API + temel işlevler: kanal konfigürasyonu · instantiate süresi · render yeteneği | Kabul testi **bağımsız araçla** ve **tanımlı işlev listesiyle** yapılır — K003 için şablon (R3) |
| 2 | Pull-model davranışının test edilmesi | https://developer.apple.com/documentation/coreaudio/audio_unit_programming_guide/audio_units_in_cocoa | çekirdek çıkış unit'ine iş isteği zinciri | Test hedefi: zincir **pull** olarak mı çağrılıyor (push değil) — senaryo #1 adayı |
| 3 | Monoton sampleTime doğrulaması | https://developer.apple.com/documentation/coreaudio/audiounit/1571111-audiounitrender | sonraki = önceki + inNumberFrames | Zamanlama testi **sayısal**: atlanan kare var mı, geriye sıçrama var mı — assertion'a uygun |
| 4 | QA coverage/flaky hedefi (vault) | `.ai/AGENTS.md` §16 (dosya okundu) | coverage ≥%80 · flaky %0 | Kabul kriteri **sayısal ve ölçülebilir** olmalı — ama K003 eşikleri henüz yok (R1) |
| 5 | PipeWire mismatch raporu (ölçüm→karar hattı) | https://pipewire.org/page_latency | beklenen vs gerçekleşen toplam latency | Örnek: ölçüm (diagnostics) → karşılaştırma → rapor (testing kararı) akışı |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **K003 testing tasarımı öncesi** şablon olarak
kullanılır: 1–3, bağımsız araç + davranış senaryosu + sayısal assertion
şablonları (auval, pull zinciri, monotonluk); 5, ölçüm→karar hattının
diagnostics bağlantısı. Eşikler R1, senaryolar R2, auval-benzeri araç R3
(`testing-kapsam.md` §5) kapanmadan yazılmaz → UNKNOWN; K004 testi ile
karıştırılmaz (R4).

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `testing-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `testing-dogrulama.md` | Kanıt/doğrulama tablosu (11 satır: ✅/has-been-fetched 8 · ⚠️ 1 · UNKNOWN 2) |
| `testing/index.md` | Düğüm kaydı (ELI15 · açık sorular) |
| `../audio-master.md` | Üst anayasa §3 satır 12 · §4 A6 · §7 D1–D7 · §8 |
| `../audio-bagimliliklar.md` | §7 — testing ↔ diagnostics tüm düğümler bağımlılığı |
| `../diagnostics/diagnostics-kapsam.md` | Kardeş çocuk — ölç ↔ karar ayrımı (TS1) |

## §5 `auval` doğrulama adımları — derin adım kaydı (master §4 A6)

> Kaynak: Apple `auval` (✅ doğrulanmış — master §4 A6). Tablo dokümanın
> doğruladığı **işlev listesidir**; K003 karşılıkları **gereksinim taslağıdır**.

| # | `auval` işlevi | Ne doğrular | K003 karşılığı (gereksinim — R3) |
|---|---|---|---|
| 1 | Plug-in API yükleme | Birim tanınabilir | Motor kaydı / plugin kaydı |
| 2 | Kanal konfigürasyonu | Kanal düzeni hatalı değil | 8.1/çoklu çıkış düzeni doğrulaması (§6.2 routing) |
| 3 | Instantiate süresi | Kurulum sürmez (zaman sınırı) | Engine kurulum süresi (ölçüm — UNKNOWN) |
| 4 | Render yeteneği | Veri üretir | Çıkış verisi üretimi (output §12) |
| 5 | Geçiş/temizleme (reset) | Birim sıfırlanabilir | K004 eki Process + Reset sözleşmesi (master §4 A5) |

**Örnek dersi:** bağımsız araç **tanımlı işlev listesine** bakar, kod
incelemez. K003 için bu liste **R3'te kararlaştırılacaktır**; bu tablo
adayları taşır, karar değildir.

## §6 Pull-model davranış testi — senaryo adayı (master §4 A1)

| # | Test girdisi | Beklenen gözlem | Assertion (kavramsal) |
|---|---|---|---|
| 1 | Zincir başlatılır, dışarıdan veri çekilir | Her düğüm **kendinden öncekine** sorar | Çağrı zinciri kaydı = beklenen sıra |
| 2 | Çıkış düğümüne "şimdi ver" çağrısı | Çıkış pasif değil, çağrılıyor | Çağrı sayacı artıyor |
| 3 | Push denemesi (zorla veri itme) | Sistem **kabul etmemeli** | Hata/durum kodu döner |

**Kural:** bu senaryo **kod kanıtı olmadan çalıştırılamaz** (repo'da C++ yok —
§1 satır 20). Senaryo **adayı** olarak kayıtlıdır; R2'de listeye girecektir.

## §7 Monoton sampleTime assertion şablonu (açıklama tabanlı — sayı yok)

| # | Assertion | Mantığı | Test tipi |
|---|---|---|---|
| 1 | Süreklilik: fark = inNumberFrames | Her render ardışık | L1 birim (döngü sahte veriyle) |
| 2 | Atlama: fark > inNumberFrames | Kare kaybı → underrun sayacı | L2/L3 |
| 3 | İhlal: fark ≤ 0 | Geriye sıçrama → kesin hata | L1 (sıkı — kesin ❌) |
| 4 | Geçiş sürekliliği | Rampa anında fark bozulmaz | L3 sistem (output §10.3) |

**Kural:** bu assertion'lar **sayısal ve kesindir** (PASS/FAIL uygundur);
ancak eşikler (kaç tick'te kaç atlama kabul) **R1'dir** → DOOR-OPEN
(testing-kapsam §8 satır 5).

## §8 Ölçüm → karar zinciri örneği (PipeWire mismatch örüntüsü)

| # | Aşama | PipeWire örneği (T16 ✅) | K003 şablonu |
|---|---|---|---|
| 1 | Beklenen | Linked latency toplamı | Bütçe (realtime §7 B5) |
| 2 | Gerçekleşen | Port latency okuma | diagnostics ölçümü |
| 3 | Fark | Mismatch | Fark > eşik? (eşik R1) |
| 4 | Rapor | Nesne işareti | diagnostics olay kaydı |
| 5 | Karar | — (PipeWire karar vermez) | testing: PASS/FAIL (§8) |

**Örnek dersi:** PipeWire **karar vermez, yalnız taşır**; K003'te de karar
testing dalındadır (TS1) — bu örüntü, diagnostics ↔ testing sınırının
**tek doğrulanmış şablonudur**.

## §9 Bağımsız doğrulama aracı gereksinim listesi (R3 — taslak)

§3 satır 3 ve R3 ile bağlantılı. auval'ın K003 karşılığı için **aday
işlev listesi**:

| # | İşlev (aday) | Neden gerekli | Durum |
|---|---|---|---|
| 1 | Motor kaydı tanınabilir | Plug-in API benzeri keşif | ⚠️ R3 |
| 2 | Kanal/topoloji doğrulama | 8.1/çoklu çıkış düzeni | ⚠️ R3 (routing R1) |
| 3 | Kurulum süresi ölçümü | Zaman sınırı (auval instantiate) | ⚠️ R3 (eşik R1) |
| 4 | Render yeteneği | Veri üretimi kanıtı | ⚠️ R3 |
| 5 | Reset/sıfırlama | K004 Process+Reset sözleşmesi | ⚠️ R3 (master §4 A5) |
| 6 | Lock-free denetimi | RT yasağı (realtime K2) | ⚠️ R3 (araç tipi yok) |

## §10 Uygulama kontrol listesi (K003 testing tasarımı öncesi — taslak)

| # | Kontrol | Neden | Kapanış kapısı |
|---|---|---|---|
| 1 | Eşikler (SNR/THD/latency) tanımlandı mı? | R1 (kapsam §7) | TS-G1 |
| 2 | Senaryo listesi yazıldı mı? | R2 (kapsam §9) | TS-G2 |
| 3 | auval-benzeri araç kararı verildi mi? | R3 (§9) | TS-G3 |
| 4 | K003↔K004 testing sınırı çizildi mi? | R4 | TS-G4 |
| 5 | CI kapsamı doğrulandı mı? | R5 (kapsam §11) | TS-G5 |
| 6 | Ölçüm sıklığı planlandı mı? | R6 | TS-G6 |

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
