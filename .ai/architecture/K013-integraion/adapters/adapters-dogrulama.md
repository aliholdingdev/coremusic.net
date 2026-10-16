---
node_id: K013-integraion/adapters/adapters-dogrulama
node_type: module
title: "Adaptör Doğrulama — Kontrat Testi, Sahte Sunucu, Kabul Testi, CI Kapısı ve Kanıt Kontrol Listesi"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/adapters
children: []
depends_on:
  - K013-integraion/adapters/adapters-sozlesme
  - K013-integraion/adapters
  - K013-integraion
evidence:
  - "https://docs.pact.io/ (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://martinfowler.com/articles/consumerDrivenContracts.html (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://wiremock.org/docs/ (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://spring.io/projects/spring-cloud-contract (2026-10-16 HEAD 200 · içerik çekildi — ARCHIVED)"
  - "https://docs.github.com/en/actions (2026-10-16 HEAD 200 · içerik okunmadı)"
  - "https://docs.specmatic.io/ (2026-10-16 HEAD 200 · içerik okunmadı)"
  - "Dosya: .github/workflows/ci.yml (2026-10-16 okundu — php-lint · php-test · composer-audit · js-test · js-coverage · js-e2e job'ları)"
  - "Dosya: .github/workflows/secret-scan.yml (2026-10-16 glob kanıtı)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 HEAD + içerik çekimi)"
  - "Repo CI iddiaları ci.yml içeriği okunarak yazılmıştır (2026-10-16)"
  - "Uydurma araç adı/sürümü yok; içerik okunmayan kaynak için içerik iddiası yazılmamıştır"
open_questions:
  - "Adaptör kontrat testi hangi test suite'ine bağlanacak (shared/tests mı, ayrı paket mi)? → UNKNOWN"
  - "Sahte sunucu kaydı (record/playback) dış sistemle sözleşme ihlali riskine karşı güncelliği nasıl koruyacak? → UNKNOWN"
  - "CI'da adaptör testi koşan bir job var mı? → ⚠️ VERIFICATION REQUIRED (ci.yml'de adaptör adlı job yok, 2026-10-16)"
  - "Dış sistem mock'u CI'da canlı uçlara dokunmadan mı koşacak? → UNKNOWN (network izin politikası)"
group: kod
---

# Adaptör Doğrulama — Katmanlar · Test · CI Kapısı

> **Bu dosya ne değildir:** Test *yazımı* rehberi veya araç kurulumu
> değildir. Bu dosya bir adaptörün **hangi katmanda, hangi testle, hangi
> kapıda** yakalanacağını tanımlar ve her kontrolün **kanıtını**
> (dosya/URL) zorunlu kılar.
>
> **Durum etiketi:** ŞARTNAME TASARIM İDDİASI · CI altyapısı IMPLEMENTED
> (`.github/workflows/ci.yml` diskte mevcut) · Adaptör testi ⚠️ PLANNED
> (adaptör sınıfı henüz yok — `adapters-sozlesme.md` §9 R1).

---

## §0 ELI15 — Bu dosya bir cümlede

Bir adaptör, **gerçek dış sisteme dokunmadan** önce üç kapıdan geçer:
(1) sözleşmenin kendisi kodda **eşleme tablosu** olarak var mı,
(2) dış sistem **sahtesi** karşısında davranış doğru mu (kontrat testi),
(3) dış sistem **gerçek** olduğunda da aynı mı (kabul testi) — ve bu
üçü **CI'da otomatik** koşmazsa adaptör, üretimde ilk sürprizde kırılır.

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | 2 |
| mimari | `adapters-anayasa.md` | Kimlik, kapsam-sınır, desen envanteri | 3 |
| mimari | `adapters-sozlesme.md` | Çeviri, tip, hata eşleme, versioning | 3 |
| kod | `adapters-dogrulama.md` (bu dosya) | Doğrulama katmanları ve CI kapısı | 3 |
| kod | `adapters-kod-ornekleri.md` | 12+ dilde port + adaptör + hata eşleme | 3 |
| kanit | `adapters-web-arastirma.md` | ≥12 benzersiz resmi URL + iddia defteri | 3 |

---

## §1 Doğrulama Katmanları Genel Bakış

| # | Katman | Ne denetler | Ne yakalamaz | Ne yakalar | Kanıt |
|---|---|---|---|---|---|
| L1 | Eşleme tablosu denetimi | Her alanın bir eşlemesi var mı | Eşlemenin **doğru** olması | Eksik alan (M2 ihlali) | [D5] (kontrol listesi) |
| L2 | Tip dönüşüm testi | §3'teki T1–T12 her satır için davranış | Şekil-dışı anlam kaybı | Bozuk/düşük tip dönüşümü | [D5] |
| L3 | Kontrat testi | Mesaj, **paylaşılan anlayışa** uyuyor mu | Gerçekteki sağlayıcı davranışı | Sözleşme ihlali (sahte üzerinde) | [D1], [D4] |
| L4 | Sahte sunucu | İstek/yanıt şekli, hata/süre senaryoları | Sağlayıcının **gerçek** sürümü | Şekil + akış + hata yolları | [D3] |
| L5 | Kabul testi (canlı/contract verify) | Sağlayıcı da aynı sözleşmeyi tutuyor mu | Sağlayıcının gelecek sürümü | İki taraf uyumsuzluğu | [D2] |
| L6 | Dayanıklılık testi | Timeout/limit/kesinti altında davranış | İş kuralı doğruluğu | Kilitlenme, kaynak sızıntısı | [D6] |
| L7 | CI kapısı | Yukarıdakiler **otomatik** koşuyor mu | Doğru yazılmış ama yanlış yerde test | Testin hiç koşulmaması | [D7] + repo |

**Katman sırası:** L1 → L2 geçmeden L3 anlamsızdır (tablo yoksa
kontrat neyi ölçer?); L3/L4 geçmeden L5 canlıya bağlanmaz; L7 olmadan
hiçbiri **zorunlu değildir**.

> **Analogi (Pact dokümanından):** "Do you set your house on fire to test
> your smoke alarm? No, you test the contract it holds with your ears by
> using the testing button." ([D1]) — adaptörü canlı dış sisteme
> bağlayıp "test etmek" yangınla yangın alarmı testidir.

---

## §2 L3 — Kontrat Testi

### §2.1 Temel kavramlar

| Kavram | Tanım | Kanıt |
|---|---|---|
| **Kontrat** | Uygulamalar arası mesajın **paylaşılan anlayışı** | [D1] |
| **Kontrat testi** | Her uygulamanın **yalnız başına** test edilerek mesajların bu anlayışa uymasını doğrulayan test | [D1] |
| **Consumer** | Veri **alan** taraf (CoreMusic'te: adaptör) | [D1] |
| **Provider** | Veri **sağlayan** taraf (dış sistem) | [D1] |
| Ne çözer | Pahalı ve kırılgan entegrasyon testi olmadan "birlikte çalışırlık" güvencesi | [D1] |
| Ne çözmez | Sağlayıcının **gerçek üretim** davranışı — o ayrı L5 | [D1] |

> **Terim notu (kaynak dili):** "In microservice architectures, the
> traditional terms client and server are not always appropriate … we
> stick to consumer and provider." ([D1]) — bu dosyada da consumer =
> adaptör, provider = dış sistem.

### §2.2 Adaptör için kontrat testi kalemleri

| # | Kalemin | Hangi katman |
|---|---|---|
| C1 | İstek şekli: yol, method, başlık, gövde anahtarları | L3 |
| C2 | Yanıt şekli: zorunlu alanlar, tipler | L3 |
| C3 | Hata şekli: durum kodu → iç sınıf eşlemesi (`sozlesme` §4.1) | L3 |
| C4 | Tip dönüşümü örnek değerleri (para, tarih, eksik↔null) | L2 + L3 |
| C5 | Bilinmeyen alan / bilinmeyen enum davranışı (M6/M7) | L2 |
| C6 | Sayfalama/limit davranışı (varsa) | L3 |
| C7 | Kimlik başlığı varlığı (**değeri değil**) | L3 |

> **C7 kuralı:** Kontrat testinde **gerçek secret kullanılmaz**; başlık
> `K009` sınırındadır (`.ai/AGENTS.md` §Security — secret `.md`/`.json`'a
> yazılmaz). Test çifti sahte değer kullanır.

---

## §3 L4 — Sahte (Mock) Sunucu

| # | Kullanım | Ne sağlar | Kanıt |
|---|---|---|---|
| M1 | Şekil doğrulama (request matching) | İstek, sahtenin beklediğiyle eşleşiyor mu | [D3] |
| M2 | Hata senaryosu (fault/latency injection) | 5xx/timeout/slow altında adaptör davranışı | [D3] |
| M3 | Record / Playback | Gerçek sistemden kaydedip çevrimdışı oynatma | [D3] |
| M4 | Henüz var olmayan API'yi simüle etme | Dış sistem yazılmadan önce adaptör geliştirme | [D3] |
| M5 | Flake'yı kesme ("isolate yourself from flakey 3rd parties") | Dış sistem kararsızlığı testi bozmasın | [D3] |

### §3.1 Sahte sunucunun SINIRI (ne yakalamaz)

| # | Yakalamaz | Neden |
|---|---|---|
| N1 | Sağlayıcının **gerçek** sürüm değişikliğini | Sahte, kendi kopyasını temsil eder |
| N2 | Sağlayıcının **gerçek** limit davranışını (429 eşiği) | Değer sahte yazılır |
| N3 | Kimlik doğrulamasının gerçek akışını | Token sahtedir (C7) |
| N4 | Ağ/protokol gerçekliğini (TLS, DNS) | Sahte yereldir |

→ Bu dört eksiklik **L5 (kabul testi)** tarafından kapatılır; sahte
**tek başına yeter değildir** (kaynak: [D1] "without having to deploy
the world first" gerekçesi + bu dosyanın §1 katman sırası).

---

## §4 L5 — Kabul Testi (Consumer-Driven Contract)

| # | Kavram | Tanım | Kanıt |
|---|---|---|---|
| A1 | Consumer-Driven Contract | Sağlayıcı, **tüketici yükümlülüklerini** görür | [D2] |
| A2 | Ne kazandırır | Sağlayıcı, evrimi tüketicilerin temel işlevine göre **yönlendirir** | [D2] |
| A3 | Sağlayıcı sözleşmesi (provider contract) | Sağlayıcının kendini **tek başına** doğruladığı sözleşme | [D2] |
| A4 | Tüketici sözleşmesi (consumer contract) | Tüketicinin **kendi** beklentisini kaydettiği sözleşme | [D2] |
| A5 | Sınır (liabilities) | Ek süreç yükü — makalede "Benefits / Liabilities" ayrı başlıktır | [D2] |
| A6 | Sürüm riski | Sağlayıcı değişince **önce** tüketici kırılma görünür hale gelir | [D2] |

### §4.1 CoreMusic adaptörü için akış (tasarım iddiası)

```text
1. adaptör-sozlesme.md'de eşleme tablosu (M2)
2. consumer contract dosyası: beklentiler (C1–C7)
3. sahte üzerinde kontrat testi (L3/L4) — CI'da koşar
4. sağlayıcı doğrulaması: dış sistemin gerçek uçlarıyla (periyodik/manuel)
5. sağlayıcı sözleşmesi tutmuyorsa → sozlesme.md güncellenir → 1'e dön
```

**Adım 4'ün sıklığı/otomasyonu → UNKNOWN** (dış sistem erişim politikası
bu turda tanımlı değil; canlı uç CI'da **varsayılan olarak koşmaz**).

### §4.2 Araç notları (içerik-kanıtlı)

| Araç | Kaynak durumu | Not | Kanıt |
|---|---|---|---|
| Pact | ✅ çekildi | "code-first tool for testing HTTP and message integrations using contract tests" | [D1] |
| WireMock | ✅ çekildi | API mock; request matching, fault/latency injection, record/playback | [D3] |
| Spring Cloud Contract | ✅ çekildi | Proje sayfası **ARCHIVED** — "no longer actively maintained"; bakım Stubborn.sh'ye devredilmiş | [D4] |
| Specmatic | ✅ 200 (içerik okunmadı) | Aday — içerik iddiası yazılmadı | [D8] |

> **Dürüstlük notu:** Spring Cloud Contract'ın **archived** olduğu
> sayfanın kendisinden okunmuştur; "neden kapandığı" **çekilmedi** →
> açıklama iddiası yazılmadı. CoreMusic'te bu araçların **hiçbiri
> seçilmemiştir** (karar yok → UNKNOWN).

---

## §5 L7 — CI Kapısı

### §5.1 Kapı aşamaları (tasarım)

| # | Aşama | Ne yapar | Geçemezse |
|---|---|---|---|
| G1 | Sözleşme doğrulama | Eşleme tablosu + şema biçimi | Pipeline durur (exit ≠ 0) |
| G2 | Birim + tip testleri | T1–T12 dönüşümleri, hata eşleme | Pipeline durur |
| G3 | Kontrat testi (sahte) | C1–C7 | Pipeline durur |
| G4 | Statik analiz | Tip/analiz denetimi | Pipeline durur |
| G5 | Kabul testi (canlı) | Periyodik/manuel — **otomatik kapı değil** | Uyarı raporu |
| G6 | Dayanıklılık testi | Timeout/kesinti senaryoları | Pipeline durur (seçili kümeler) |

**Kapı kuralı:** G1–G4 **sert** (kod merge edilemez), G5 **yumuşak**
(periyodik), G6 **sert ama dar** (yalnız dayanıklılık kümesi).

### §5.2 Mevcut CI — repo kanıtı (2026-10-16 `ci.yml` okundu)

| Job | Ne yapar | Sertlik |
|---|---|---|
| `php-lint` | Composer install ×3 + PHPStan (`composer stan`, level=5 — satır 60–64) | sert |
| `php-test` | PHPUnit — `shared` + `auth.coremusic.net --testsuite Unit` (matrix) | sert |
| `composer-audit` | 3 projede `composer audit` | sert |
| `js-test` | `npm run test:js` (Vitest) | sert |
| `js-coverage` | `npm run test:js:coverage` — **A7 kararı: `continue-on-error: true`** (soft, satır 167) | yumuşak |
| `js-e2e` | Playwright e2e — **A6 kararı: `continue-on-error: true`** (soft, satır 184) | yumuşak |

> **Negatif kanıt:** `ci.yml` içinde "adapter"/"contract" adlı bir job
> **yoktur** (2026-10-16 tam okuma) → adaptör kontrat testi **şu an
> CI'da koşmuyor**. Bu, mevcut durumun ölçümüdür; "koşmayacak" **iddiası
> değildir** (plan → UNKNOWN).
>
> **Yetki notu:** `.yml` değişikliği onay ister (`.github/AGENTS.md` §3.3
> sınır 4: "yml'e kullanıcı onayı olmadan job eklenmez/çıkarılmaz").
> Bu dosya **hiçbir workflow dosyasını değiştirmez.**

### §5.3 Gate → Kanıt zinciri

| Gate | Beklenen kanıt formatı | Nereye yazılır |
|---|---|---|
| G1 | Tablo + şema doğrulama raporu | Test raporu (CI log) |
| G2 | Test adı + assert | Test dosyası |
| G3 | Consumer contract dosyası + eşleşme raporu | `tests/` altı |
| G4 | Statik analiz çıktısı | CI log |
| G5 | Tarih + dış sürüm | Periyodik rapor |
| G6 | Senaryo + ölçüm (timeout, yanıt süresi) | CI log |

---

## §6 Kanıt-Temelli Kontrol Listesi

> **Kural:** "✓" yalnız **kanıt (dosya yolu / URL / test adı)** ile
> konur. Kanıtsız satır `⚠️` kalır — bu listenin **değil, kanıtın**
> notudur.

### §6.1 Sözleşme tarafı

| # | Kontrol | Kanıt | Durum |
|---|---|---|---|
| 1 | Eşleme tablosu mevcut | `adapters-sozlesme.md` §3 | ✓ (taslak) |
| 2 | Tip dönüşümü T1–T12 işaretli | `adapters-sozlesme.md` §3 | ✓ (taslak) |
| 3 | Hata eşleme tablosu | `adapters-sozlesme.md` §4.1 | ✓ (taslak) |
| 4 | Eksik↔null kararı | `adapters-sozlesme.md` M5 | ⚠️ karar yok |
| 5 | Bilinmeyen alan/enum kararı | M6/M7 | ⚠️ karar yok |

### §6.2 Test tarafı

| # | Kontrol | Kanıt | Durum |
|---|---|---|---|
| 6 | Adaptör port arayüzü var | `IOAuthProvider.php` / `CacheInterface.php` | ✓ (dizin) |
| 7 | Adaptör kontrat testi var | — | ⚠️ YOK (aranan: `shared/tests/` altında adaptör testi bu turda okunmadı) |
| 8 | Sahte sunucu senaryosu | — | ⚠️ YOK/UNKNOWN |
| 9 | Kabul testi koşumu | — | ⚠️ UNKNOWN |
| 10 | Mevcut test suite'leri | `ci.yml` `php-test` job'ı | ✓ (ci.yml satır 66–108) |
| 11 | JS testi | `js-test` job'ı (`npm run test:js`) | ✓ (satır 142–158) |

### §6.3 Güvenlik/zarafet tarafı

| # | Kontrol | Kanıt | Durum |
|---|---|---|---|
| 12 | Testte gerçek secret yok (C7) | kural: `sozlesme.md` §6 K2 | ✓ (kural yazılı) |
| 13 | Log'da token maskesi | `.ai/AGENTS.md` §17 edge 2 | ✓ (kural) |
| 14 | Secret taraması CI'da | `.github/workflows/secret-scan.yml` | ✓ (dosya var — içerik bu turda okunmadı → ⚠️ kapsam) |
| 15 | Timeout değeri K014'ten | — | ⚠️ UNKNOWN |

### §6.4 Süreç tarafı

| # | Kontrol | Kanıt | Durum |
|---|---|---|---|
| 16 | CI kapısı sert G1–G4 | `ci.yml` mevcut job'ları | ✓ (adaptör job'ı hariç — §5.2 negatif kanıt) |
| 17 | Yeni job onayı | `.github/AGENTS.md` §3.3/§4 kural 2 | ✓ (kural) |
| 18 | Dış sistem sürüm takibi | — | ⚠️ UNKNOWN |
| 19 | Periyodik canlı doğrulama (G5) | — | ⚠️ UNKNOWN |
| 20 | Kapı ölçümü depoda | — | ⚠️ YOK/PLANNED |

**Sayım (2026-10-16):** ✓ **11** · ⚠️ **9** (toplam 20).

---

## §7 Ölçüm ve Sınıf Dağılımı

| Sınıf | Anlamı | Adet (§6) |
|---|---|---|
| ✓ | Kanıt dosyada/URL'de mevcut | 11 |
| ⚠️ | Kanıt yok — eksik/planlanmamış | 9 |
| ❌ | İhlal tespit edildi | 0 |

> **❌ 0 önemli:** Bu dosya henüz **ihlal** değil, **eksiklik** raporluyor.
> İhlal, adaptör kodu yazıldıktan sonra anlamlıdır.

---

## §8 Hata Sınıfları — Doğrulama sırasında yakalananlar

| # | Hata sınıfı | Hangi katman yakalar | Çözüm yönü |
|---|---|---|---|
| H1 | Eşlemesiz alan | L1 | `sozlesme.md` §3'e satır ekle |
| H2 | Tip dönüşümü hatası | L2 | Dönüşüm fonksiyonu + test |
| H3 | Şekil ihlali (zorunlu alan eksik) | L3 | Sözleşme + sahte güncelle |
| H4 | İstek uyuşmazlığı (method/yol) | L4 | Adaptör istek üretimi |
| H5 | Sağlayıcı sözleşmesi kırıldı | L5 | Dış sistem değişikliği → sürüm kararı |
| H6 | Kilitlenme/timeout | L6 | Dayanıklılık katmanı (anayasa §4.4) |
| H7 | Test hiç koşmuyor | L7 | CI job'ı (onaylı) |

---

## §9 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Kontrat** | Paylaşılan mesaj anlayışı (dokümante) |
| **Consumer/Provider** | Alan / veren taraf — adaptör / dış sistem |
| **Sahte (mock/stub)** | Sağlayıcının yerine geçen test çifti |
| **Record/Playback** | Gerçekten kaydedip çevrimdışı oynatma |
| **Kabul testi** | Sağlayıcının sözleşmeyi tuttuğunu doğrulama |
| **Sert kapı** | Geçemezse merge engellenir |
| **Yumuşak kapı** | Görünür kırmızı ama engellemez (`continue-on-error`) |
| **Flake** | Dış bağımlılık kaynaklı kararsız test |

---

## §10 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| Kontrat testi = entegrasyon testi mi? | Hayır; kontrat **yalnız mesaj anlayışını** ölçer ([D1]) |
| Mock = gerçek sağlayıcı mı? | Hayır — §3.1 N1–N4 |
| Kabul testi CI'da her PR koşar mı? | Tasarım: **hayır** (G5 periyodik) — sıklık UNKNOWN |
| `js-coverage` sert kapı mı? | **Hayır** — `continue-on-error: true` (ci.yml satır 167) |
| Spring Cloud Contract güncel mi? | **Hayır** — ARCHIVED ([D4]) |
| Pact = HTTP + message | Evet — "testing HTTP and message integrations" ([D1]) |
| Mock sunucu canlı sistemin sürümünü yakalar | **Yakalamaz** — o L5 işidir |

---

## §11 Kapsam Dışı

1. `adapters-kod-ornekleri.md` — kod örnekleri
2. `adapters-sozlesme.md` — eşleme/hata tablolarının kendisi
3. `adapters-web-arastirma.md` — tam URL defteri
4. Workflow dosyası **değişikliği** (onay + `.github/AGENTS.md` kuralı)
5. Araç kurulumu/sürüm seçimi (Pact/WireMock/Specmatic) → **seçim yok → UNKNOWN**
6. Test yazım stili / kütüphane sürümü → `QA Engineer` domaini
7. `K013/integration-testing` kardeşinin kapsamı (bütünleşik test planı)

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| D1 | Pact — kontrat testi tanımı, consumer/provider, "deploy the world first" gerekçesi | https://docs.pact.io/ | 2026-10-16 | ✅ 200 · çekildi |
| D2 | Consumer-Driven Contracts (Ian Robinson) — provider/consumer contract, benefits/liabilities | https://martinfowler.com/articles/consumerDrivenContracts.html | 2026-10-16 | ✅ 200 · çekildi |
| D3 | WireMock — request matching, fault/latency injection, record/playback, "flakey 3rd parties" | https://wiremock.org/docs/ | 2026-10-16 | ✅ 200 · çekildi |
| D4 | Spring Cloud Contract — CDC desteği; proje **ARCHIVED** / bakım devri | https://spring.io/projects/spring-cloud-contract | 2026-10-16 | ✅ 200 · çekildi |
| D5 | Eşleme/tip kontrol listesi | — (bu dosyanın tasarımı) | 2026-10-16 | Tasarım — URL yok |
| D6 | Bulkhead (dayanıklılık izolasyonu) — L6 komşu desen | https://learn.microsoft.com/en-us/azure/architecture/patterns/bulkhead | 2026-10-16 | ✅ 200 · içerik okunmadı → içerik iddiası ⚠️ |
| D7 | GitHub Actions — CI çalıştırma altyapısı | https://docs.github.com/en/actions | 2026-10-16 | ✅ 200 · içerik okunmadı → içerik iddiası ⚠️ |
| D8 | Specmatic — kontrat test aracı adayı | https://docs.specmatic.io/ | 2026-10-16 | ✅ 200 · içerik okunmadı → içerik iddiası ⚠️ |

> **Kapsam beyanı:** Listelenmeyen URL dayanak alınmamıştır. §1, §5.1,
> §6'daki kapı/kontrol listeleri **tasarımdır**; dayanakları §6'daki
> kanıt sütunudur (repo dosyaları), URL **değildir**.

---

## §A Kapılar ve Açık Sorular (bu dosyanın)

| # | Kapı / Soru | İşaret | Kime gider |
|---|---|---|---|
| 1 | Adaptör kontrat testi hangi suite'e bağlanacak? | UNKNOWN | QA Engineer + ADR |
| 2 | CI'ya adaptör job'ı eklenecek mi (onaylı)? | UNKNOWN (onay kapısı) | DevOps (`.github/AGENTS.md` §4 kural 2) |
| 3 | Sahte sunucu stratejisi: record/playback mı, elle mi? | UNKNOWN | ADR |
| 4 | Canlı doğrulama sıklığı (G5) | UNKNOWN | ADR |
| 5 | Dış sistem mock'u CI'da ağa çıkar mı? | UNKNOWN | Ağ politikası (K014) |
| 6 | Hangi araç seçilecek (Pact/Specmatic/diğer)? | UNKNOWN | ADR |
| 7 | `secret-scan.yml` kapsamı adaptör test çiftlerini içeriyor mu | ⚠️ VERIFICATION REQUIRED | sonraki tur (dosya okunacak) |

> Bu tablo frontmatter `open_questions` alanını genişletir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · Pact · WireMock · Fowler/Robinson · GitHub Actions · **Tarih: 2026-10-16**
