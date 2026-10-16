---
node_id: K013-integraion/integration-testing/ittest-sozlesme
node_type: technology-deepdive
title: "Entegrasyon Test Sözleşmesi — Ortam Sözleşmesi, Stub Sözleşmesi, Canlı Test Onay Kapısı"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/integration-testing/ittest-anayasa
  - K013-integraion/integration-testing
  - K013-integraion/api-contracts/openapi
evidence:
  - "https://docs.pact.io/ (2026-10-16)"
  - "https://martinfowler.com/bliki/ContractTest.html (2026-10-16)"
  - "https://playwright.dev/docs/mock (2026-10-16)"
  - "auth.coremusic.net/phpunit.xml — Satır 10-12 (2026-10-16 disk)"
  - ".github/workflows/ci.yml — satır 77, 80 (2026-10-16 disk)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 fetch)"
  - "Sözleşme maddeleri, kaynak metinlerde geçmeyen yükümlülük içermiyor; geçmeyenler TASARIM ÖNERİSİ olarak etiketli"
  - "Uydurma ortam/anahtar/dosya adı yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN"
open_questions:
  - "Ortam sözleşmesini kim onaylar (QA mı DevOps mu) → ⚠️ VERIFICATION REQUIRED"
  - "Stub sözleşmesi hangi formatta (JSON mı kod mu) → UNKNOWN (ADR bekliyor)"
  - "Canlı test onay kapısının yetkisi (kaç kişilik) → UNKNOWN"
group: mimari
---

# Entegrasyon Test Sözleşmesi — Ortam · Stub · Canlı Onay Kapısı

> **Bu dosya ne değildir:** Kod veya K019 ortam *tarifi* değildir.
> Bu dosya entegrasyon testi **koşmadan önce** varılması gereken üç
> sözleşmeyi (ortam, stub, canlı onay) tanımlar; her madde kaynak metinle
> ya da açık "TASARIM ÖNERİSİ" etiketiyle taşınır.
>
> **Durum etiketi:** SÖZLEŞME taslağı (draft) · Uygulama ⚠️ PLANNED
> (repo'da ortam/stub sözleşmesi dosyası 2026-10-16'da tespit edilmedi).

---

## §0 ELI15 — Bu sözleşme bir cümlede

Entegrasyon testi, "hangi ortamda, neyin sahtesiyle ve kimin izniyle"
sorularının **testten önce** yazılı ve değişmez şekilde cevaplanmış hali
dir; böylece test sürpriz üretmez, canlıya izinsiz dokunulmaz.
(Kaynak: §Kaynak Tablosu [S1], [S2], [S3])

---

## §1 Sözleşmenin Üç Katmanı

| # | Sözleşme | Kime bağlar | Neyi sabitler | Kanıt |
|---|---|---|---|---|
| 1 | **Ortam sözleşmesi** | Testin koşacağı fiziksel/mantıksal ortam | URL/port/ürün/isolasyon — neyin canlı, neyin sahte olduğu | [S1], [S2] |
| 2 | **Stub (sahte) sözleşmesi** | Taklit + onu üreten kod | Taklit, gerçek servisin **aynı cevapları** vermek zorunda | [S3], [S2] |
| 3 | **Canlı test onay kapısı** | Üretime/gerçek servise dokunan her test | Kim, ne zaman, hangi onayla canlıya vurabilir | [S2] (kaynak: dikkat+konuşma kuralı) |

---

## §2 Ortam Sözleşmesi (Environment Contract)

### §2.1 Zorunlu alanlar (TASARIM ÖNERİSİ — kaynak metinlerde bu şema yok)

| Alan | Ne sabitler | Zorunlu mu | Kaynak bağlantısı |
|---|---|---|---|
| `environment_id` | Testin adı (ör. `local`, `ci`, `staging-test`) | Evet | Öneri |
| `live_endpoints` | Canlıya **giden** uç nokta listesi (boş ise test hiç canlıya çıkmaz) | Evet | [S2] "üretime test yollama" kuralının ifadesi |
| `stub_endpoints` | Sahte uç nokta listesi (host:port) | Evet | [S2], [S3] |
| `data_policy` | Test verisi üretimi/sıfırlaması | Evet | ⚠️ Detay → K019 (sınır) |
| `network_access` | Ağ erişimi izni (CI'da dış ağ açık mı kapalı mı) | Evet | [S1] geniş test "ağ erişimi ister" |
| `approver` | Canlı uç nokta ekleyenin onay makamı | Evet | Öneri (bkz. §4) |

### §2.2 Temel kural — Tek Yön Derleme (kaynaklı)

| Kural | Metin | Kanıt |
|---|---|---|
| K1 | Test, canlı uç noktayı **varsayılan olarak göremez**; canlıya yalnızca `live_endpoints` listesindeki giriş açıkça varsa çıkar | [S2] türetilmiş kural (üretimde test yasağı) |
| K2 | Ortam sözleşmesi **repo'da** tutulur, kişilerde değil | Öneri |
| K3 | Ortam sözleşmesi değişirse stub sözleşmesi yeniden gözden geçirilir | Öneri |
| K4 | Ortam kimliği test adında/anında görünür olmalı | Öneri |

> **⚠️ Doğruluk:** K1-K4'ün *şeması* kaynaklarda birebir yoktur; kural
> olarak [S2]'deki "üretime otomatik testle vurmak yasak" ve [S1]'deki
> ortam/tanım gereksiniminden **türetilmiştir**. Şemanın kendisi
> TASARIM ÖNERİSİ etiketiyle taşınır.

### §2.3 CoreMusic ortam haritası (2026-10-16 disk durumu)

| Ortam | Durum | Kanıt |
|---|---|---|
| Yerel unit test ortamı (PHPUnit/Vitest) | ✅ Çalışıyor (tanımı var) | `phpunit.xml` ×3 · `ci.yml` |
| Entegrasyon test ortamı | ❌ **YOK** — `tests/Integration/` dizini diskte değil | `auth.coremusic.net/phpunit.xml` satır 10-12 [R1] |
| Sahte sunucu (player API mock'u) | ✅ Kapsam içi tek örnek | `assets.coremusic.net/tests/mocks/player-api.mock.js` [R2] |
| Test container'ı | ⚠️ UNKNOWN → **K019 sahibi** | `K019-testing/integration/...` [R3] |

---

### §2.4 Ortam Sözleşmesi Örneği (taslak biçim — gerçek dosya DEĞİL)

```yaml
# taslak — repoda böyle bir dosya 2026-10-16'da TESPİT EDİLMEDİ
environment_id: local-stub          # maddeler §2.1
stub_endpoints:                     # testin konuşacağı sahte uçlar
  - name: player-api-mock
    basis: assets.coremusic.net/tests/mocks/player-api.mock.js   # [R2] disk kanıtı
live_endpoints: []                  # boş = canlı temas yok (K1)
network_access:
  external: false                   # CI'da dış ağ kapalı varsayımı (öneri)
data_policy:
  reset: per-run                    # ⚠️ detay → K019 (sınır)
approver: UNKNOWN                   # onay makamı ADR bekliyor (§4)
```

> **UYARI:** Yukarıdaki blok **örnek taslaktır**; `approver: UNKNOWN` ve
> `data_policy` satırları kasıtlı olarak işaretli boşluktur. Repoda var olan
> tek gerçek dosya [R2]'dir; ortam sözleşmesi dosyasının kendisi **yoktur**.

### §2.5 Değişiklik Kuralları (TASARIM ÖNERİSİ)

| # | Değişiklik | Gereken |
|---|---|---|
| D1 | `stub_endpoints`'e uç ekleme | Sözleşme güncellemesi + stub'ın yazılması |
| D2 | `live_endpoints`'e uç ekleme | §4 onay kapısı (G1-G5) |
| D3 | `network_access` açma | DevOps + güvenlik gözden geçirmesi → ⚠️ yetki ADR |
| D4 | Ortam kimliği değişimi (`environment_id`) | Test adlarının/raporlarının eşzamanlı güncellenmesi |
| D5 | Sözleşme dosyasının silinmesi | YASAK — kapı fail-safe'i (G5) anlamsızlaşır |

## §3 Stub (Sahte) Sözleşmesi — "Taklit Sadık mı?"

### §3.1 Temel yükümlülük (kaynaklı)

Pact dokümanı contract test'i "çağrıların **test double'lardan gerçek
uygulamadan döndürülenle aynı sonucu** verdiğini" kontrollemek olarak
tanımlar; Fowler ContractTest bliki de "kendi testlerin taklitle koşmaya
devam et, ayrıca **periyodik contract testleri** çalıştır: bunlar taklitten
yapılan tüm çağrıların gerçek servisten döndürenle aynı olmasını sınar"
der. (Kaynak: [S3], [S2])

| Madde | Yükümlülük | Kanıt |
|---|---|---|
| S1 | Stub, gerçek servisin **dokümante edilmiş** davranışını yansıtır | [S3] |
| S2 | Stub davranışı **sözleşmeye** (belge/örneklere) dayanır, keyfî değil | [S3] "contract by example" |
| S3 | Gerçek servis değişirse contract test **tutarsızlığı** açar → stub güncellenir | [S2] |
| S4 | Contract test başarısızlığı normal test gibi build'i kırpmak zorunda değil; **tutarlılık görevi** açar ve sahiplerle konuşulur | [S2] |
| S5 | Contract test ritmi = dış servisin **değişim ritmi** (günde bir çoğu zaman yeterli) | [S2] |

### §3.2 Stub sözleşme biçimleri (seçenek — hangisi seçildi → UNKNOWN)

| Biçim | Artı | Eksi | Kanıt |
|---|---|---|---|
| Kod içinde tanımlı stub (test dosyası) | Basit, sürümle gider | Kod dışı paylaşımı zor | [S1] (WireMock örneği kod içidir) |
| HAR dosyasıyla kayıt/replay | Gerçek trafiği birebir oynatır | Kirlenebilir, gizli veri riski | [S3] (Playwright HAR akışı) |
| Ayrı depoda saklanan contract paketi | Ekipler arası paylaşım (CDC) | Yayınlama adımı gerekir | [S2], [S3] |

> **⚠️ CoreMusic kararı:** Üç biçimden hangisinin seçileceği **UNKNOWN** —
> `.ai/.decisions` içinde entegrasyon-test stub kararı bu turda okunmadı.

### §3.4 Stub Sözleşmesi Denetim Listesi (test kodlanmadan önce)

| # | Soru | "Evet" çıkana kadar davranış |
|---|---|---|
| 1 | Stub'ın yansıttığı uç nokta, ortam sözleşmesinde `stub_endpoints` içinde mi? | Dur |
| 2 | Cevap biçimi (durum kodu + gövde) belgede/örnekte var mı? | Dur |
| 3 | Hata yolları (4xx/5xx) de tanımlı mı? | Dur |
| 4 | Gerçek servis son doğrulamasından (contract test) geçti mi? | Dur — S3/S4 gereği |
| 5 | Stub'da gerçek secret/gerçek kişisel veri var mı? | Dur — sızıntı riski |
| 6 | Taklit "test geçsin" diye gevşetilmiş mi? | Dur — sözleşme bozulur |

> **Liste kanıtı:** 1-4 maddeleri [S1], [S2], [S3] metinlerinden
> türetilmiştir; 5-6'ncı maddeler TÜREV (güvenlik + sözleşme samimiyeti)
> olup ilgili güvenlik kuralı vault'ta ayrıca aranmalıdır → kesin hüküm
> için `⚠️ VERIFICATION REQUIRED`.

### §3.5 Stub içeriğinde yasaklar (TASARIM ÖNERİSİ — güvenlik türevi)

| Yasak | Neden |
|---|---|
| Stub içine gerçek secret/token/anahtar gömme | Taranan kodda sızıntı (bkz. `ittest-dogrulama.md` §Secret) |
| Stub içine gerçek kişisel veri | KVKK/veri sızıntısı riski → ⚠️ kesin hüküm için güvenlik kuralı |
| Stub'ı "testi geçsin diye" gerçek davranışı gevşeterek yazmak | Sözleşmeyi anlamsızlaştırır — S2-S4'e aykırı |

---

## §4 Canlı Test Onay Kapısı (Live-Test Approval Gate)

### §4.1 Neden kapı gerekli (kaynaklı gerekçe)

| Kaynak ifadesi | Anlamı | Kanıt |
|---|---|---|
| "Üretim sisteminize otomatik testle vurmak — logları kirletmenin en iyisi, en kötüsü servisi DoS'lamak" | Canlı test **tek başına bırakılamaz** | [S1] |
| "Kaçınılmazsa üretici/sahiplerle konuşun ve testlere fazlasıyla dikkat edin" | Canlıya çıkmak **onay + dikkat** ister | [S2] |
| Contract test başarısızlığı → "ileri sürme görevi + dış servis sahipleriyle konuşma" | Canlı davranış değişikliği **iletişim kapısı** doğurur | [S2] |

### §4.2 Kapı Kuralları (TASARIM ÖNERİSİ — kaynaklardaki dikkat dilinden türetme)

| # | Kural | Açıkhali |
|---|---|---|
| G1 | Canlı uç nokta içeren test, CI'da **otomatik** koşmaz | Manuel/onaylı tetik |
| G2 | Onay vermeden önce ortam sözleşmesindeki `live_endpoints` listesi gözden geçirilir | Zorunlu |
| G3 | Canlı test çalışması **sınırlandırılır** (yol sayısı, istek sayısı) | [S1] smoke test "sınırlı yol" |
| G4 | Sonuç, `log.md`'ye (append-only) kaydedilir | Vault kuralı |
| G5 | Onay makamı bilinmiyorsa test **çalıştırılmaz** | Fail-safe |

> **⚠️ DOĞRULUK:** G1-G5'in yetkili onay makamı (kişi/rol) **UNKNOWN** —
> bu bilgi ADR gerektirir. Kaynak metinler yalnızca "dikkat + konuşma"
> der; kimi kime soracağı yazılmamıştır → uydurulmadı.

### §4.3 Onay Kapısı Şeması (taslak — uygulanmadı)

```text
Tetik (manuel)
  → Ortam sözleşmesi yükle
      → live_endpoints boş mu?
         ├─ EVET → sahte uçlarla koş → sonuç log'a
         └─ HAYIR ↓
      → Onay makaması onayı var mı?
         ├─ HAYIR → DUR (fail-safe)
         └─ EVET ↓
      → Sınırlandırılmış canlı test
         → Sonuç: log.md (append-only) + rapor
```

### §4.4 Rol Matrisi — Kim Ne İster, Kim Onaylar (TASARIM ÖNERİSİ)

| Rol | İster | Onaylar | Kaynak bağ |
|---|---|---|---|
| QA Engineer | Canlı test çalıştırma talebi | ✗ (talep sahibi) | `.ai/AGENTS.md` §6 test → QA |
| DevOps Engineer | CI tetikleme/ortam | ✅ Ortam/tetik yüzeyi | `.ai/AGENTS.md` §6 CI/CD → DevOps |
| Security Engineer | — | ✅ Canlı uç/veri teması için vetosu olabilir | §6 güvenlik → Security |
| İnsan (Vault Steward) | — | ✅ Üretim temasında son onay | Kural: üretim deploy + secret onay ister |

> **⚠️ DOĞRULUK:** Tablo VAULT ROL TABLOSUNDAN türetilmiştir; fakat
> "canlı test onayı" için **özel ADR yoktur** → bağlayıcı yetki
> `UNKNOWN` olarak kalır (§4.2 G5 ihlal edilmez).

### §4.5 Kapı Senaryoları

| # | Senaryo | Kapı davranışı |
|---|---|---|
| 1 | Test yalnız sahte uç kullanır | Kapı açıktır (onay gerekmez), otomatik koşabilir |
| 2 | Tek bir canlı uç içerir | Onay zorunlu + sınırlandırma |
| 3 | Liste çok sayıda canlı uç içerir | DUR — önce kapsam daraltma |
| 4 | Onay makamı bilinmiyor | DUR (fail-safe) |
| 5 | Canlı test başarısız | log'a yaz + sahiplerle iletişim (S2) |

---

## §5 Sözleşmeler Arası Bağımlılık Zinciri

| Sıra | Sözleşme | Bozulursa ne olur |
|---|---|---|
| 1 | Ortam sözleşmesi | Stub yanlış uçta çalışır → test yanlış şeyi ölçer |
| 2 | Stub sözleşmesi | Taklit gerçekliği kaybeder → yeşil ama anlamsız test |
| 3 | Canlı onay kapısı | İzinsiz canlıya istek → log kirlenmesi / DoS riski |

> **Zincir kuralı (TASARIM ÖNERİSİ):** 1 → 2 → 3 sırasıyla tamamlanmadan
> entegrasyon testi **kodlanmaz**; bu sıra kaynakların neden-sonuç
> mantığıyla (önce ortam, sonra taklit doğruluğu, sonra canlı temas) uyumludur
> ([S1], [S2]).

### §5.1 Bozulma Etkisi Tablosu

| Bozulan madde | Hızlı belirti | Testin sonucu | Yanlış sonuç riski |
|---|---|---|---|
| Ortam sözleşmesi (yanlış uç) | Bağlantı hatası / 404 | Kırmızı | Düşük (başarısız olur) |
| Ortam sözleşmesi (canlı/sahte karışması) | Yeşil ama gerçek servise istek | **Yeşil** | **Yüksek** — kritik |
| Stub sözleşmesi (gerçeklik kaybı) | Yeşil, gerçek serviste kırmızı | **Yeşil** | **Yüksek** — kritik |
| Canlı onay kapısı yok | İzinsiz canlı istek | Değişir | Operasyonel risk |

> **İki kritik satır:** "Ortam/sahte karışması" ve "gerçeklik kaybı"
> **sessiz başarısızlık**tır — kapı tasarımı bu ikisini hedefler (§4 G1-G5,
> §3.4 denetim listesi). (Kaynak: [S2] taklit-gerçek uğultusu + [S1] üretim
> teması dikkati.)

---

## §6 Repo ile Yüzleşme — Sözleşme Boşlukları (2026-10-16)

| # | Boşluk | Kanıt | Durum |
|---|---|---|---|
| 1 | `tests/Integration/` yok → ortam sözleşmesi uygulanacak yer yok | `auth.coremusic.net/phpunit.xml` satır 10-12 | ⚠️ EKSİK |
| 2 | CI auth için yalnızca `Unit` koşuyor | `ci.yml` satır 80 | ✅ Kayıtlı (bilinçli) |
| 3 | Sahte sunucu var ama stub sözleşmesi belgesi yok | `player-api.mock.js` 184 satır; yanında `.md` yok | ⚠️ EKSİK |
| 4 | Canlı test onay kapısı yok | `.github/workflows/` altında canlı tetik yok → gözlem | ⚠️ EKSİK |
| 5 | Test container'ı kararlı mı | → K019 | ⚠️ SINIR |

---

## §7 Kapsam Dışı (bu dosyada işlenmez)

> **Sınır notu:** Aşağıdaki maddeler bu dosyada **tanımlanmaz**; bağlantı
> yalnızca yön gösterir. Ortam *tarifi* ve izolasyon münhasıran K019'a aittir
> — çelişkide K019 kazanır (bkz. `ittest-anayasa.md` §5).

1. Ortamın **nasıl kurulacağı** (container, DB izolasyonu) → **K019-testing**
2. `ittest-dogrulama.md` — doğrulama/CI kapısı ve rapor biçimi
3. `ittest-kod-ornekleri.md` — stub/mock kalıpları kodu
4. `ittest-web-arastirma.md` — tam URL kanıtı
5. OpenAPI belgesi kendisi → `api-contracts/openapi/`

---

## §8 Açık Sorular (özet — tam liste: `ittest-web-arastirma.md` §Açık Sorular)

| # | Soru | İşaret |
|---|---|---|
| Q1 | Ortam sözleşmesi kim onaylar? | ⚠️ VERIFICATION REQUIRED |
| Q1a | Sözleşme dosyasının adı ve konumu ne olacak? | UNKNOWN |
| Q2 | Stub sözleşmesi biçimi (kod/HAR/ayrı paket)? | UNKNOWN |
| Q3 | Canlı onay makamı ve yetki sayısı? | UNKNOWN (ADR) |
| Q4 | `live_endpoints` listesinde CoreMusic'te hangi alanlar? | UNKNOWN (üretim alanları vault'a yazılmaz) |
| Q5 | Onay kapı logu nereye (log.md hangi bölüm)? | ⚠️ VERIFICATION REQUIRED |

---

## §9 Okuma Haritası — 5 Dosyalık Seri

| # | Dosya | Ne verir |
|---|---|---|
| 1 | `ittest-anayasa.md` | Unit≠Integration≠Contract, piramit, live vs mock, K019 sınırı |
| 2 | **`ittest-sozlesme.md`** (bu dosya) | Ortam sözleşmesi, stub sözleşmesi, canlı onay kapısı |
| 3 | `ittest-dogrulama.md` | Doğrulama, mock tutarlılığı, eski kayıt, secret, CI |
| 4 | `ittest-kod-ornekleri.md` | 12 dilde kalıp |
| 5 | `ittest-web-arastirma.md` | URL kanıtları + reddedilenler |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | Üretimde test yasağı (log/DoS), ortam gereksinimi, WireMock ile yerel sahte uç, test konfigürasyonuyla uç değiştirme | https://martinfowler.com/articles/practical-test-pyramid.html | 2026-10-16 | ✅ çekildi |
| S2 | Contract test ritmi (günde 1), build'i kırma yerine tutarlılık görevi, sahiplerle konuşma, dikkatli canlı teması, taklit-vs-gerçek | https://martinfowler.com/bliki/ContractTest.html | 2026-10-16 | ✅ çekildi |
| S3 | Contract test = double çağrıları gerçek sonuçla aynı mı, "contract by example", statik şemadan farkı, consumer/provider | https://docs.pact.io/ | 2026-10-16 | ✅ çekildi |
| S4 | HAR ile kayıt/replay, route ile uç nokta engelleme | https://playwright.dev/docs/mock | 2026-10-16 | ✅ çekildi |
| R1 | Integration suite kaldırma notu | `auth.coremusic.net/phpunit.xml` satır 10-12 | 2026-10-16 | ✅ disk |
| R2 | Tek mock kaynağı | `assets.coremusic.net/tests/mocks/player-api.mock.js` (184 satır) | 2026-10-16 | ✅ disk |
| R3 | Ortam altyapısı sahibi sınırı | `.ai/architecture/K019-testing/integration/integration-test-ortami.md` | 2026-10-16 | ✅ disk |
| R4 | CI'da Unit-only koşum | `.github/workflows/ci.yml` satır 77, 80 | 2026-10-16 | ✅ disk |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretli satırlar için **dayanak
> yoktur**; öneri olarak dahi "kural" diye kullanılamaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · docs.pact.io · martinfowler.com · **Tarih: 2026-10-16**
