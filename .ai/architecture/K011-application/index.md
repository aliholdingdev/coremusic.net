---
node_id: K011-application
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - usecase-akislari
  - command-query
  - event-akislari
  - dto-ve-mapper
  - odem-servisleri
  - zamanlamali-gorevler
  - web
  - desktop
  - electron
  - local-runtime
  - server-runtime
  - application-services
  - commands
  - queries
  - workflows
  - application-testing
depends_on: [K008-domain, K009-security, K012-services, K013-integraion, K010-network, K014-media]
evidence:
  - "v2 §2.11 K011-application/ — çocuk listesi kaynağı (16 çocuk birebir korundu)"
  - ".ai/architecture/index.md §5/11 · §8.1 — ad kaydı + iki-ekseni gruplama kuralı"
  - "shared/src/PageRouter/ 14 dosya / 1575 satır · Events/ 14 dosya + 2 Contracts · Cache/ 6 dosya / 176 satır — kademe-3 kanıtı (2026-10-12)"
validation:
  - "Çocuk adları v2 §2.11 ile birebir karşılaştırıldı (10/10) — 2026-10-12"
  - "Web kanıtları application-dogrulama.md §3'e işlendi — 2026-10-12"
  - "Kök 12 dosya satır aralıkları `Get-Content | .Count` ile ölçüldü — 2026-10-12"
  - "Kademe-2 6 çocuk v1+v2 şemasıyla üretildi (index + 5 içerik dosyası)"
## §GRUPLAR

> İki-ekseni gruplama (registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**. `Kademe` ataması şeffaf rol kuralıdır (index/master/kapsam → Kademe-1; destek → Kademe-2) — diskte `kademe` alanı yok, uydurma yok.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Uygulama katmanı künyesi | Kademe-1 |
| mimari | `application-master.md` | K011 anayasası | Kademe-1 |
| mimari | `application-kapsam.md` | Kapsam sınırı | Kademe-1 |
| mimari | `application-bilesenler.md` | Bileşen envanteri | Kademe-2 |
| mimari | `application-bagimliliklar.md` | Bağımlılıklar | Kademe-2 |
| mimari | `application-kararlar.md` | Kararlar | Kademe-2 |
| kod | `application-kod-ornekleri.md` | Kod örnekleri | Kademe-2 |
| kanit | `application-riskler.md` | Riskler | Kademe-2 |
| kod | `application-test-stratejisi.md` | Test stratejisi | Kademe-2 |
| kanit | `application-web-arastirma.md` | Web araştırması | Kademe-2 |
| mimari | `application-evrensel-dil.md` | Evrensel dil | Kademe-2 |
| kanit | `application-dogrulama.md` | Doğrulama | Kademe-2 |


## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K011-application |
| **K Adı** | Uygulama Katmanı (Application) |
| **Kapsam** | Uygulamanın kendisi — web sayfası, masaüstü penceresi, Electron çatısı. Kullanıcının gördüğü ve tıkladığı her şey: arayüzü çizmek ve "çal, ara, ekle" talebini alıp doğru servise iletmek. İş kuralı burada yaşamaz → K008-domain · arka plan → K012-services · dış dünya → K013-integraion · bağlantı → K010-network · medya → K014-media. 16 kademe-2 çocuk (6 yeni v1+v2 + v2 eski 10 çocuk verify-required). |
| **Kritik Bağımlılıklar** | K008-domain · K009-security · K010-network · K012-services · K013-integraion (+K014-media) (kanıt: FM depends_on — K0100-identitiy ölü referansı 2026-10-18'de silindi ⚠️) |
| **Durum** | draft (16 kademe-2 çocuk · kademe-3: pagerrouter/ · event-bus/ · cache-layer/) |
| **Son Güncelleme** | 2026-10-13 (index 40-100 kapısı: 138 → ≤100 · §GRUPLAR'a de-duplike) |

## §1 — ELI15: bu oda ne işe yarar?

v2 §2.11: *"Uygulamanın kendisi — web sayfası, masaüstü penceresi, Electron çatısı."* Kullanıcının gördüğü ve tıkladığı **her şey** bu odadır: arayüzü çizmek ve "çal, ara, ekle" talebini alıp doğru servise iletmek. İş kuralı burada **yaşamaz** → K008-domain · arka plan → K012-services · dış dünya → K013-integraion → bağlantı → K010-network · medya → K014-media. K011 yalnız A2 bandında kodlar; politika K009'dan gelir.

## §2 — GRUPLAR kuralı

İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1): her dosyanın frontmatter'ında **9. alan `group`** (`mimari` · `kod` · `kanit`) + klasör içi dağılım aşağıdaki **§GRUPLAR** tablosunda. Bu dosyanın ayrı grup tablosu kaldırıldı — bilgi **§GRUPLAR**'da tektir (dedup, 2026-10-13).

## §3 — Çocuk kataloğu

**Kademe-2 (yeni, v1+v2 şeması):** usecase-akislari · command-query · event-akislari · dto-ve-mapper · odem-servisleri · zamanlamali-gorevler — her biri 6 MD (index 40–100 · kapsam · dogrulama · web-arastirma · ornekler · bilesenler). **Kademe-3:** `pagerrouter/` · `event-bus/` · `cache-layer/` — her dosya `{teknoloji}-{rol}.md`, ≥500 satır, yalnız kendi klasöründe. v2 eski 10 çocuk `verify-required`, adları değiştirilmedi/silinmedi.

## §4 — Bağımlılık özeti (tam metin: application-bagimliliklar.md)

| Yön | Düğüm | Ne için |
|---|---|---|
| depends_on → | K008-domain | Entity/VO sözleşmesi (link, kod taşımaz) |
| depends_on → | K009-security | Güvenlik politikası, CSRF/CSP kuralları |
| depends_on → | K012-services | Arka plan/servis çağrıları (adaptör) |
| depends_on → | K013-integraion | Dış API kontratları ve adaptörler |
| depends_on → | K010-network | Taşıma katmanı (HTTP/WebSocket) |
| depends_on → | K014-media | Oynatma ve medya verisi görünümü |
| ← used_by | K017-observability · K019-testing | Uygulama telemetrisi ve test hedefi |

## §5 — Durum & açık sorular

- **Durum:** draft — kök 12 dosya yazıldı (2026-10-12); kademe-2/3 üretimi sürüyor. Ayrıntı: application-master.md §11–§14 · application-dogrulama.md §12 · application-kararlar.md §1–§9.
- **Açık sorular:** 5 madde (desktop↔electron · server-runtime · CQRS · K019 sınırı · ödeme kodu yok) — hiçbiri bu dosyada kapatılmaz, tamamı FM `open_questions` alanında.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `application-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| mimari | `application-bilesenler.md` | Bileşen planı ve kademe ipuçları | Kademe-1 |
| kanit | `application-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `application-evrensel-dil.md` | domain terim sözlüğü (TR + EN) | Kademe-1 |
| mimari | `application-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-1 |
| mimari | `application-kararlar.md` | Mimari kararlar, alternatifler, ADR bağlantıları | Kademe-1 |
| kod | `application-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-1 |
| mimari | `application-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| kanit | `application-riskler.md` | Risk, etki, olasılık ve azaltım planı | Kademe-1 |
| kod | `application-test-stratejisi.md` | domain testleri: unit, property, contract | Kademe-1 |
| kanit | `application-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-1 |

## §BAĞLANTILAR

> **Kaynak & yöntem (2026-10-18 genişletme):** 22 K'nın tamamının `index.md` frontmatter
> `depends_on` alanları + tüm `.md` dosyalarında ad-geçen K taraması (UTF-8 ölçümü).
> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kendi belgeleri hedefe referans
> veriyor · `←` karşı tarafın belgeleri bu K'ya referans veriyor · `↔` ikisi de.
> Kanıtsız eşleşmede `⚠️` yazılır; **uydurma bağımlılık satırı yazılmaz** (zero-hallucination).

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ↔ | runtime, süreç ve zamanlama zemini | `K011 body` K000 referansı · `K000 isletim-sistemi-runtime-kapsam.md` L41 → K011 |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ← | donanım riskleri ve bağımlılıklar | `K001 donanım-riskler.md` L209 → K011 |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ↔ | cihaz donanım entegrasyonu | `K011 body` K002 referansı · `K002 dijital-ve-mikroislemci-anayasa.md` L432 → K011 |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | → | oynatma motoru | `K011 body` K003 referansı · K003 ters kanıt yok (yalnız stok satır, sayılmaz) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | DSP efekt kullanımı | `K011 body` K004 referansı · `K004 crossover-kapsam.md` L85 zayıf (sayılmaz) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | doğrudan kanıt yok — firmware boot etkisi olası | `K005` belgelerinde K011 geçmiyor · `K011` belgelerinde K005 geçmiyor (`⚠️ VERIFY REQUIRED`) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ↔ | AI ürün özellikleri | `K011 body` K006 referansı · `K006 ai-master.md` L107/L217 → K011 |
| K007-data | [[K007-data]] | ../K007-data/index.md | ↔ | repository erişimi | `K011 body` K007 referansı · `K007` dosyalarında K011 tam ad referansı |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | → | use-case ve domain | `K011 body` K008 referansı · `K008 domain/index.md` L59 çıplak zayıf (sayılmaz) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ↔ | auth ve authz | `K011 body` K009 referansı · `K009` dosyalarında K011 tam ad referansı |
| K010-network | [[K010-network]] | ../K010-network/index.md | ↔ | ağ çağrıları | `K011 body` K010 referansı · `K010 index.md` L81/L125 · `network-bagimliliklar.md` L41 → K011 |
| K011-application | — (kendi) | index.md | ○ | bu düğümün kendisi | — |
| K012-services | [[K012-services]] | ../K012-services/index.md | ↔ | servis çağrıları | `K011 body` K012 referansı · `K012` dosyalarında K011 tam ad referansı |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ↔ | entegrasyon adaptörleri | `K011 body` K013 referansı · `K013` dosyalarında K011 tam ad referansı |
| K014-media | [[K014-media]] | ../K014-media/index.md | ↔ | medya verisi görünümü | `K011 body` K014 referansı · `K014` dosyalarında K011 tam ad referansı |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ↔ | ajan altyapısı entegrasyonu | `K011 body` K015 referansı · `K015 depends_on [K011-application]` |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ↔ | uygulama telemetrisi | `K011 §4` eski ad `K017-observability` (⚠️ ad çelişkisi, §6.2-A) · `K016 correlation/index.md` L458 · `performance-profil-ve-olcum.md` L474 → K011 |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ← | CI/deploy zinciri | `K17 devops-kapsam.md` L123/L193 → K011 |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | → | test hedefi | `K011 §4` eski ad `K019-testing` (⚠️ ad çelişkisi, §6.2-A) · `K18 source-control/index.md` L67 git-log zayıf (sayılmaz) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ← | uyum kontrolleri | `K019 controls/index.md` L99 → K011 |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ← | keşif/arama entegrasyonu | `K020 agent-bilesenler.md` L50/L385 → K011 |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | doğrudan kanıt yok — öneri entegrasyonu olası | `K021` belgelerinde K011 geçmiyor (`⚠️ VERIFY REQUIRED`) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
