---
title: "K17-devops/deployment — Yayılım Stratejileri, Ortamlar ve Geri Dönüş (Rollback)"
node_id: K17-devops/deployment/yayilim-stratejileri
node_type: module
status: draft
parent_id: K17-devops/deployment
children: []
depends_on:
  - K17-devops
  - K17-devops/deployment
  - K17-devops/pipeline
evidence:
  - ".workflows/deployment.md — ReadAllLines 281 satır, Aşama 5 Dağıtım satır 165, Üretim hedefi satır 173 (2026-10-12)"
  - ".workflows/deployment.md — Geri Dönüş (Rollback) satır 212, Yasaklar satır 257-262 (2026-10-12)"
  - ".workflows/deployment.md — Onay matrisi satır 158, 3 deneme başarısız satır 255 (2026-10-12)"
  - ".github/workflows/ci.yml — sert kapılar satır 26, 67, 111, 143; yumuşak kapılar satır 167, 183 (2026-10-12)"
  - ".workflows/deployment.md — sağlık kontrolü HTTP 200 satır 186, yasaklar satır 257 (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "626 mevcut .md dosyasında taban-URL çakışması 0 ölçüldü (2026-10-12)"
  - "Dağıtım işi (deploy job) .github/workflows/ altında tespit edilmedi (2026-10-12)"
open_questions:
  - "Dağıtım tetikleyicisi hangi mekanizmala tetikleniyor (manuel, cron, ayrı sistem) — UNKNOWN"
  - "Üretim ortamında otomatik geri dönüş (auto-rollback) eşiği tanımlı değil (⚠️ VERIFICATION REQUIRED)"
  - "Mavi/yeşil (blue/green) veya kanarya (canary) altyapısının varlığı doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# K17-devops/deployment/yayilim-stratejileri

> **ELI10:** Yayılım stratejisi, yeni sürümün eski sürümün yerine nasıl geçeceğidir: hepsi bir anda
> mı (big-bang), parça parça mı (canary), iki sistem yan yana mı (blue/green). Bu dosya hangi
> stratejinin ne zaman kullanılacağını, üretime geçmeden hangi kapıdan geçileceğini ve işler
> ters giderse hangi adımlarla geri dönüleceğini tek yerde tanımlar.

## §1 Amaç

Bu modül, CoreMusic'in **dağıtım kararlarının tek yetkili tanımıdır**. Dağıtım zinciri üç
halkadır ve halkalar atlanamaz: **kapı (pipeline gate) → strateji (strategy) → doğrulama ve geri
dönüş (verify & rollback)**. Repo'da `.workflows/deployment.md` 8 aşamalı dağıtım akışını
verir; bu dosya onu strateji düzeyinde tamamlar.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Sürüm nasıl yayılır? | Yayılım stratejisi (deployment strategy) | §4 seçim matrisi |
| 2 | Üretime geçmeden ne koşul? | Ön kapı (pre-deploy gate) | §7 — `ci.yml` sert kapıları |
| 3 | Hangi ortama çıkarılır? | Ortam hiyerarşisi | §6 — deployment.md satır 168-173 |
| 4 | Başarı nasıl ölçülür? | Sağlık kontrolü (health check) | §8 — deployment.md satır 182-190 |
| 5 | Ters giderse ne yapılır? | Geri dönüş (rollback) | §9 — deployment.md satır 212-245 |
| 6 | Kim onaylar? | Onay matrisi (approval matrix) | §10 — deployment.md satır 156-163 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Dağıtım tetikleyicisi | mekanizma | bu depoda workflow olarak yok | UNKNOWN |
| H2 | Geri dönüş planı zorunluluğu | kural | dağıtım öncesi zorunlu | deployment.md satır 262 |
| H3 | Sağlık kontrolü adımları | adet | 5 (HTTP, API, DB, log, performans) | deployment.md satır 184-190 |
| H4 | Onay matrisi seviyesi | adet | 4 (düşük, orta, yüksek, kritik) | deployment.md satır 158-162 |
| H5 | Otomatik yayılım (deploy job) | adet | `.github/workflows/` altında 0 | 2 dosya: ci.yml, secret-scan.yml |
| H6 | Üretim geri dönüş süresi hedefi (RTO) | dakika | değer tanımlı değil | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Yayılım stratejisi seçimi | ✅ big-bang, rolling, blue/green, canary | ❌ Hat kapıları → `pipeline/` |
| Ortam hiyerarşisi | ✅ geliştirme, test, üretim | ❌ Ortam değişkeni yönetimi → `secrets/` |
| Ön kapı | ✅ `ci.yml` sert kapılarının dağıtıma etkisi | ❌ Kapı içeriği ve testsuite → `pipeline/` |
| Sağlık kontrolü | ✅ 5 kontrol + başarı ölçütü | ❌ İzleme metrikleri → K16-observability |
| Geri dönüş | ✅ 6 adımlı akış + tetikleyici koşulları | ❌ Olay yönetimi → K017 alerting |
| Görüntü (image) dağıtımı | ✅ strateji düzeyinde atıf | ❌ Görüntü üretimi ve etiketleme → `containers/` |
| Veritabanı migration | ✅ yasak ve ön koşul olarak | ❌ Migration şeması → veri katmanı (dışı) |

**Sınır kararı:** bu depoda dağıtım işi (deploy job) bulunmadığı için stratejiler **hedef mimari**
olarak tanımlanır; mevcut durum "hiç dağıtım yok" değil, "dağıtım bu depoda otomatik değil"
şekindedir.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili satır |
|---|---|---|---|
| Big-bang | big-bang deployment | Tüm trafiğin tek anda yeni sürüme geçmesi | §4 |
| Rolling | rolling deployment | Örneklemin parça parça yenilenmesi | §4 |
| Blue/green | blue/green deployment | İki tam sürümün yan yana durması, tek anahtarla geçiş | §4 |
| Canary | canary deployment | Trafiğin küçük bir diliminin önce yeni sürüme verilmesi | §4 |
| Kapı | gate | Yayıtı engelleyen ön koşul | ci.yml satır 26, 67, 111, 143 |
| Sağlık kontrolü | health check | Yayından sonra hizmetin ayakta olduğu kanıtı | deployment.md satır 186 |
| Geri dönüş | rollback | Önceki sürüme dönme işlemi | deployment.md satır 212 |
| Onay | approval | Risk seviyesine göre insan/kimlik onayı | deployment.md satır 158 |
| Migration | schema migration | Şema değişikliği; yedekleme ön koşulu | deployment.md satır 261 |
| Sürüm etiketi | release tag | Dağıtılan kodun kimliği | deployment.md satır 205 |

## §4 Yayılım stratejisi seçimi

Strateji tek başına seçilmez; **risk seviyesi + ortam + geri dönüş maliyeti** birlikte belirler.
Bu depoda üretim hedefi `production.coremusic.net` olarak tanımlıdır (deployment.md satır 173).

| # | Strateji | Trafik geçişi | Geri dönüş hızı | Maliyet | Ne zaman kullanılır |
|---|---|---|---|---|---|
| 1 | Big-bang | %100 tek anda | yavaş (geri yükleme gerekir) | düşük | Geliştirme/Test ortamı |
| 2 | Rolling | %0 → %100 parça parça | orta (eski örnek kalır) | orta | Düşük riskli üretim sürümü |
| 3 | Blue/green | tek anahtarda %0 → %100 | çok hızlı (anahtar geri alınır) | yüksek (çift kaynak) | Kritik sürüm, migration yok |
| 4 | Canary | %5 → %25 → %100 | hızlı (dilim geri alınır) | orta | Performans riski olan sürüm |
| 5 | Sabit sürüm dondurma | trafik kilitli | uygulanamaz | düşük | Olay (incident) sırasında |

### §4.1 Seçim matrisi

| # | Koşul | Önerilen strateji | Gerekçe | Onay seviyesi |
|---|---|---|---|---|
| 1 | Ortam = geliştirme | Big-bang | Etki sınırı dar | Agent self-review (satır 159) |
| 2 | Ortam = test | Big-bang | Üretim trafiği yok | Agent self-review (satır 159) |
| 3 | Ortam = üretim + düşük risk | Rolling | Hız ve denge | Agent + peer review (satır 160) |
| 4 | Ortam = üretim + şema migration | Blue/green | Anlık geri dönüş mümkün | İnsan onayı (satır 161) |
| 5 | Ortam = üretim + performans riski | Canary | Erken uyarı imkânı | İnsan + güvenlik + mimari (satır 162) |
| 6 | Olay (incident) aktif | Sabit sürüm dondurma | Değişiklik riski yasak | İnsan onayı (satır 161) |

**Seçim kuralı:** strateji, dağıtım öncesi onay adımında (deployment.md satır 156) yazılır ve
dağıtım kaydına (satır 199-210) işlenir; sözlü karar geçerli değildir.

### §4.2 Strateji–geri dönüş uyumu

| # | Strateji | Geri dönüş mekanizması | Süreç adımı | Dayanak |
|---|---|---|---|---|
| 1 | Big-bang | Eski sürüm dosyalarının geri yüklenmesi | 3. GERİ DÖNÜŞ | deployment.md satır 230 |
| 2 | Rolling | Yeni örneklerin durdurulması | 3. GERİ DÖNÜŞ | deployment.md satır 230 |
| 3 | Blue/green | Anahtarın eski sürüme çevrilmesi | 3. GERİ DÖNÜŞ | deployment.md satır 230 |
| 4 | Canary | Dilimin düşürülmesi | 3. GERİ DÖNÜŞ | deployment.md satır 230 |
| 5 | Dondurma | Yayıtım zaten durdu | 1. DURDUR | deployment.md satır 219 |

## §5 Strateji karşılaştırması — sayısal boyutlar

| # | Ölçü | Big-bang | Rolling | Blue/green | Canary |
|---|---|---|---|---|---|
| 1 | Kullanılan kaynak | 1x | 1x-2x | 2x | 1x-2x |
| 2 | Erişilebilirlik kesintisi | mümkün | nadir | yok (tasarım gereği) | yok |
| 3 | Gözlemlenebilirlik ihtiyacı | düşük | orta | orta | yüksek (K017 şart) |
| 4 | Geri dönüş karmaşıklığı | yüksek | orta | düşük | düşük-orta |
| 5 | Migration (şema) desteği | kısıtlı | kısıtlı | güçlü (çift şema) | kısıtlı |
| 6 | Otomasyon olgunluğu | düşük | orta | yüksek | yüksek |
| 7 | Bu depoda uygulanmış mı | Hayır | Hayır | Hayır | Hayır |

**Not (satır 7):** `.github/workflows/` altında yalnız `ci.yml` (203 satır) ve `secret-scan.yml`
(31 satır) vardır; dağıtım işi yoktur. Stratejiler hedef mimari olarak taşınır.

## §6 Ortam hiyerarşisi

Dağıtım ortamları `deployment.md` "Aşama 5: Dağıtım" bölümünde tanımlıdır.

| # | Ortam | Hedef | Kullanım amacı | Trafik | Dayanak satır |
|---|---|---|---|---|---|
| 1 | Geliştirme | Local development | Kod yazım doğrulaması | gerçek kullanıcı yok | 171 |
| 2 | Test | Test sunucusu | Entegrasyon ve E2E doğrulaması | gerçek kullanıcı yok | 172 |
| 3 | Üretim | production.coremusic.net | Gerçek hizmet | gerçek kullanıcı | 173 |

**Ortam kuralları:**

| # | Kural | Zorunlu mu | İhlal sonucu | Dayanak |
|---|---|---|---|---|
| 1 | Test ortamında doğrulanmadan üretime geçilmez | zorunlu | dağıtım durdurulur | Yasaklar (satır 259) |
| 2 | İnsan onayı olmadan üretime çıkılmaz | zorunlu | dağıtım durdurulur | Yasaklar (satır 260) |
| 3 | Yedekleme alınmadan migration çalışmaz | zorunlu | dağıtım durdurulur | Yasaklar (satır 261) |
| 4 | Geri dönüş planı olmadan dağıtılmaz | zorunlu | dağıtım durdurulur | Yasaklar (satır 262) |
| 5 | Güvenlik doğrulaması atlanamaz | zorunlu | dağıtım durdurulur | Yasaklar (satır 263) |
| 6 | Ortam değişkenleri dağıtım öncesi doğrulanır | zorunlu | dağıtım durdurulur | Aşama 2 (satır 143) |

## §7 Ön kapı (pre-deploy gate) — hattın dağıtıma etkisi

Dağıtım zincirinin ilk halkası mevcut CI hattıdır. **Sert kapılar** dağıtımın ön koşulu,
**yumuşak kapılar** yalnız bilgi üretir.

| # | Kapı | İş | Sertlik | Dağıtıma etkisi | Dayanak |
|---|---|---|---|---|---|
| 1 | Statik analiz | php-lint (PHPStan level 5) | sert | Geçmeden dağıtım yok | ci.yml satır 26, 62 |
| 2 | Birim test (PHP) | php-test (matrix) | sert | Geçmeden dağıtım yok | ci.yml satır 67, 104 |
| 3 | Sürüm açığı | composer-audit | sert | Bilinen CVE varsa dağıtım yok | ci.yml satır 111 |
| 4 | Birim test (JS) | js-test | sert | Geçmeden dağıtım yok | ci.yml satır 143 |
| 5 | Kapsam ölçümü | js-coverage | yumuşak (satır 167) | Dağıtıma engel değil, rapor | ci.yml satır 161 |
| 6 | Uçtan uca test | js-e2e (Playwright) | yumuşak (satır 183) | Dağıtıma engel değil, rapor | ci.yml satır 179 |
| 7 | Sızıntı taraması | gitleaks | sert (ayrı workflow) | Bulunursa dağıtım yok | secret-scan.yml satır 29 |

**Kapı kararı:** `deployment.md` Aşama 3 "Test Doğrulaması" (satır 146) beş test türü ister
(birim, entegrasyon, E2E, güvenlik, performans). Repo'daki otomatik kapılar bu beşin
**üçünü** karşılar (birim ✅, güvenlik ✅ gitleaks/composer-audit, E2E ✅ ama yumuşak);
entegrasyon ve performans kapıları için otomatik iş tespit edilmedi → ⚠️ VERIFICATION REQUIRED.

| # | Eksik kapı | Beklenen iş | Mevcut durum | Öncelik |
|---|---|---|---|---|
| 1 | Entegrasyon testi | CI job (testsuite Api/Repository) | otomatik iş yok | yüksek |
| 2 | Performans testi | CI job veya ayrı akış | otomatik iş yok | orta |
| 3 | E2E sertleştirme | js-e2e `continue-on-error` kaldırma | yumuşak (satır 183) | yüksek (onay gerekli) |

## §8 Sağlık kontrolü (health check)

Dağıtım sonrası doğrulama beş adımdan oluşur; beşi de geçmeden dağıtım "başarılı" sayılmaz.

| # | Kontrol | Amaç | Başarı ölçütü | Başarısızlık eylemi |
|---|---|---|---|---|
| 1 | HTTP 200 | Ana sayfa ayakta mı | 200 yanıtı | Geri dönüş (3. adım) |
| 2 | API yanıtı | Endpoint'ler çalışıyor mu | geçerli gövde | Geri dönüş (3. adım) |
| 3 | Veritabanı | Bağlantı sağlıklı mı | sorgu başarılı | Durdur + etki analizi |
| 4 | Log'lar | Hata logu var mı | kritik hata yok | Durdur + kök neden |
| 5 | Performans | Yanıt süresi normal mi | eşiğin altında | Geri dönüş (§4.2) |

**Dayanak:** `deployment.md` satır 182-190 (Aşama 6: Sağlık Kontrolü), tablo satır 186
(`HTTP 200 | Ana sayfa çalışıyor mu?`).

**İzleme devri (Aşama 7, satır 192):** sağlık kontrolü geçtikten sonra izleme penceresi açılır;
dört gözlem tanımı vardır: hata oranı · performans metrikleri · kaynak kullanımı · kullanıcı
geri bildirimi. İzleme penceresinin süresi `deployment.md` içinde yazılmamıştır →
⚠️ VERIFICATION REQUIRED.

**İlk aksaklık davranışı (Aşama 5 hata yönetimi, satır 247-255):**

| Durum | Aksiyon | Sıra |
|---|---|---|
| Test başarısız | Dağıtımı durdur, düzelt | 1 |
| Güvenlik açığı | Acil durdur | 2 |
| Performans düşüklüğü | Geri dönüş yap | 3 |
| Bağlantı hatası | Tekrar dene | 4 |
| 3 deneme başarısız | İnsan onayı iste | 5 (satır 255) |

## §9 Geri dönüş (rollback)

Geri dönüş **altı adımlı, tekrarlanabilir** bir akıştır; akış başarıyla tamamlanmadan yeni
dağıtım başlatılamaz.

| # | Adım | Soru / İş | Adım çıktısı | Dayanak satır |
|---|---|---|---|---|
| 1 | DURDUR | Dağıtımı durdur | Durmuş dağıtım | 219 |
| 2 | ETKİ ANALİZİ | Ne kadar etkilendi? | Etki raporu | 223 |
| 3 | GERİ DÖNÜŞ | Önceki sürüme dön | Eski sürüm aktif | 230 |
| 4 | DOĞRULAMA | Geri dönüş çalıştı mı? | Doğrulama sonucu | 234 |
| 5 | KÖK NEDEN ANALİZİ | Neden başarısız oldu? | Kök neden kaydı | 238 |
| 6 | DÜZELTME | Sorunu çöz | Düzeltme + tekrar deneme | 242 |

**Geri dönüş tetikleyicileri:**

| # | Tetikleyici | Kaynak | Eşik | Aksiyon |
|---|---|---|---|---|
| 1 | Sağlık kontrolü başarısız | §8 kontrol 1-5 | herhangi biri | 3. GERİ DÖNÜŞ |
| 2 | Hata oranı artışı | Aşama 7 izleme | eşik tanımlı değil → UNKNOWN | 3. GERİ DÖNÜŞ (insan kararı) |
| 3 | Performans düşüklüğü | Aşama 7 izleme | eşik tanımlı değil → UNKNOWN | 3. GERİ DÖNÜŞ (satır 253) |
| 4 | 3 ardışık başarısız deneme | Aşama 5 hata yönetimi | 3 (satır 255) | İnsan onayı iste |
| 5 | Güvenlik açığı | Aşama 2 doğrulama | tek bulgu | Acil durdur (satır 252) |

**Otomatik geri dönüş:** beş tetikleyiciden hiçbiri "otomatik" olarak yazılmamıştır; tümü
insan kararı ile ilerler. Otomatik geri dönüş eşiği bu depoda yok → ⚠️ VERIFICATION REQUIRED.

## §10 Onay matrisi (approval matrix)

| # | Risk seviyesi | Onay veren | Karar zamanı | Dayanak satır |
|---|---|---|---|---|
| 1 | Düşük | Agent self-review | dağıtım öncesi | 159 |
| 2 | Orta | Agent + peer review | dağıtım öncesi | 160 |
| 3 | Yüksek | İnsan onayı | dağıtım öncesi | 161 |
| 4 | Kritik | İnsan + güvenlik + mimari onay | dağıtım öncesi | 162 |

**Değişiklik onayı kapsamı (frontmatter `require_approval_for`, satır 54-59):** dağıtım hedefi ·
altyapı · CI/CD pipeline · üretim yapılandırması · geri dönüş prosedürü. Beşi de onaysız
değiştirilemez.

| # | Onaysız değişiklik | Sonuç | Denetim |
|---|---|---|---|
| 1 | Dağıtım hedefi değişimi | Deployment.md güncellenemez | Vault Steward |
| 2 | Altyapı değişimi | Onay gerekli | devops |
| 3 | Pipeline değişimi | Onay gerekli | devops |
| 4 | Üretim yapılandırması | Onay gerekli | güvenlik |
| 5 | Geri dönüş prosedürü | Onay gerekli | mimari |

## §11 Kod ve akış örnekleri — repo gerçek içeriği

### §11.1 Dağıtım adımları — .workflows/deployment.md satır 176-180

```text
1. Yedekleme al
2. Migration'ı çalıştır
3. Dosyaları yükle
4. Config'i güncelle
5. Servisi başlat
```

Beş adım sırasıyla zorunludur; 1 numaralı adım olmadan 2 numaralı adım yasaktır (satır 261).

### §11.2 Sağlık kontrolü tablosu — satır 184-190

```text
| HTTP 200  | Ana sayfa çalışıyor mu? |
| API yanıtı| Endpoint'ler çalışıyor mu? |
| Veritabanı| Bağlantı sağlıklı mı? |
| Log'lar   | Hata logu var mı? |
| Performans| Yanıt süresi normal mi? |
```

### §11.3 CI kapısı — .github/workflows/ci.yml satır 67-68

```yaml
    name: PHP Tests (${{ matrix.project }})
    needs: php-lint
```

Dağıtımın ön koşulu olan sert kapı zinciri burada başlar: `needs` bağımlılığı testi statik
kontrole bağlar; zincir koparsa hattın sonucu dağıtıma giremez.

### §11.4 Sızıntı kapısı — .github/workflows/secret-scan.yml satır 11-12

```yaml
permissions:
  contents: read
```

Tarama işi yazma yetkisi almaz; kapı yalnız okuma yapar ve sonucu dağıtım kararı için üretir
(satır 29: `gitleaks/gitleaks-action@v2`).

## §12 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | .workflows/deployment.md | 128 | Aşama 1: Ön Kontroller (6 kontrol) |
| 2 | .workflows/deployment.md | 139 | Aşama 2: Güvenlik Doğrulaması |
| 3 | .workflows/deployment.md | 146 | Aşama 3: Test Doğrulaması (5 test türü) |
| 4 | .workflows/deployment.md | 156-162 | Aşama 4: Onay + 4 seviyeli matris |
| 5 | .workflows/deployment.md | 165 | Aşama 5: Dağıtım başlığı |
| 6 | .workflows/deployment.md | 171-173 | Ortamlar: geliştirme, test, üretim |
| 7 | .workflows/deployment.md | 176-180 | 5 dağıtım adımı (yedekleme → servis) |
| 8 | .workflows/deployment.md | 182-190 | Aşama 6: Sağlık Kontrolü (5 kontrol) |
| 9 | .workflows/deployment.md | 192 | Aşama 7: İzleme (4 gözlem) |
| 10 | .workflows/deployment.md | 199-210 | Aşama 8: Dağıtım logu şablonu |
| 11 | .workflows/deployment.md | 212 | §4 Geri Dönüş (Rollback) başlığı |
| 12 | .workflows/deployment.md | 219-242 | 6 adımlı geri dönüş akışı |
| 13 | .workflows/deployment.md | 247-255 | Hata yönetimi tablosu, 3 deneme kuralı |
| 14 | .workflows/deployment.md | 257-263 | 5 yasak |
| 15 | .workflows/deployment.md | 54-59 | Onay gerektiren değişiklikler (5 kalem) |
| 16 | .github/workflows/ci.yml | 26, 67, 111, 143 | Dört sert kapı (dağıtım ön koşulu) |
| 17 | .github/workflows/ci.yml | 161-167, 179-183 | İki yumuşak kapı (raporlayan) |
| 18 | .github/workflows/secret-scan.yml | 11-12, 29 | Yetki kısıtı + gitleaks kapısı |

**Dosya uzunlukları (2026-10-12, `[IO.File]::ReadAllLines`):** `deployment.md` 281 ·
`ci.yml` 203 · `secret-scan.yml` 31.

## §13 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K17-devops/pipeline | Sert kapı sonucu | Yayıta yalnız yeşil hattan çıkılır | CI hattı sonucu | mevcut (ci.yml) |
| 2 | K17-devops/containers | Dağıtılacak görüntü | Görüntü adı ve etiketi | image reference | ⚠️ VERIFICATION REQUIRED |
| 3 | K17-devops/secrets | Ortam değişkenleri | Üretim yapılandırması | secret okuma | parçası (Aşama 2) |
| 4 | K16-observability/alerting | Sağlık/izleme eşiği | Dağıtım sonrası uyarı | uyarı akışı | eşik yok → açık |
| 5 | K16-observability/logging | Dağıtım logu | Aşama 8 kaydı | log formatı | şablon mevcut (satır 202) |
| 6 | K18-testing/integration | Entegrasyon testi sonucu | Eksik kapı (§7) | otomatik iş yok | açık |
| 7 | K18-testing/performance | Performans testi sonucu | Eksik kapı (§7) | otomatik iş yok | açık |

**Sıra:** pipeline (kapı) → deployment (strateji) → observability (doğrulama/uyarı).
Ters akış yoktur; dağıtım, kapı sonucunu değiştiremez.

## §14 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Dağıtım işi yok (workflow) | Yayıtım manuel ve tekrarlanamaz | yüksek | Deploy job tanımı (onay gerekli) | UNKNOWN |
| 2 | Otomatik geri dönüş eşiği yok | Kırılma geç fark edilir | yüksek | §9 tetikleyici eşikleri | ⚠️ VERIFICATION REQUIRED |
| 3 | İzleme penceresi süresi yazılmamış | Pencere kapanış zamanı belirsiz | orta | Süre tanımı | ⚠️ VERIFICATION REQUIRED |
| 4 | Blue/green altyapısı doğrulanmadı | Strateji uygulanamayabilir | orta | Altyapı incelemesi | ⚠️ VERIFICATION REQUIRED |
| 5 | Entegrasyon/performance kapısı yok | §7 eksik kapı | yüksek | CI işi ekleme | açık |
| 6 | E2E yumuşak kapı | Uçtan uca regresyon yayına girebilir | yüksek | Sertleştirme kararı | açık |
| 7 | Migration geri dönüşü tanımlı değil | Şema geri dönüşü riskli | yüksek | Şema geri dönüş planı | ⚠️ VERIFICATION REQUIRED |
| 8 | RTO/RSA değerleri yok | Süre hedefi ölçülemez | orta | Hedef tanımı | UNKNOWN |

## §15 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Kapı | 4 sert kapı yeşil | CI hattı sonucu | her dağıtım | ci.yml satır 26, 67, 111, 143 |
| 2 | Onay | Risk seviyesine göre onay | Onay kaydı | her dağıtım | deployment.md satır 158-162 |
| 3 | Strateji | Seçim yazılı | Dağıtım kaydı | her dağıtım | §4.1 (kayıt zorunlu) |
| 4 | Sağlık | 5/5 kontrol | Aşama 6 tablosu | her dağıtım | deployment.md satır 184-190 |
| 5 | Geri dönüş | 6 adım tamamlandı | Olay kaydı | geri dönüşte | deployment.md satır 219-242 |
| 6 | Süre | Geri dönüş süresi | Zaman damgası | geri dönüşte | kayıt yok → UNKNOWN |

## §16 Strateji seçim şablonu (dağıtım öncesi)

| # | Şablon alanı | Zorunlu | Örnek | Denetim |
|---|---|---|---|---|
| 1 | Ortam | evet | üretim | §6 |
| 2 | Risk seviyesi | evet | yüksek | §10 matrisi |
| 3 | Strateji | evet | blue/green | §4.1 matrisi |
| 4 | Sert kapı sonucu | evet | 4/4 yeşil | §7 |
| 5 | Sağlık kontrolü planı | evet | §8 beş kontrol | §8 |
| 6 | Geri dönüş planı | evet | §9 akışı (6 adım) | Yasaklar satır 262 |
| 7 | Onay veren | evet | insan onayı | satır 161 |
| 8 | Dağıtım logu kaydı | evet | Aşama 8 şablonu | satır 199-210 |

**Yasak (özet):** planı eksik alanla dağıtım yapmak · onaysız üretime çıkmak · yedeksiz
migration çalıştırmak · geri dönüşsüz dağıtım · güvenlik doğrulamasını atlamak (deployment.md
satır 257-263).

## §17 Dağıtım öncesi ve sonrası kontrol listeleri

Kontrol listeleri deployment.md'in aşama tablolarından türetilmiştir; her satır dağıtım
kaydına "işaretlenmiş" olarak yazılır.

### §17.1 Öncesi (pre-flight) — 12 madde

| # | Kontrol | Kaynak aşama | Mükerrer edilebilir mi | Eksiğinde ne olur |
|---|---|---|---|---|
| 1 | Kod incelemesi tamam | Aşama 1 (satır 131) | hayır | dağıtım durdurulur |
| 2 | Testler başarılı | Aşama 1 (satır 132) | evet (CI tekrarlar) | dağıtım durdurulur |
| 3 | Güvenlik taraması yapıldı | Aşama 1 (satır 133) | evet (gitleaks) | dağıtım durdurulur |
| 4 | Dokümantasyon güncellendi | Aşama 1 (satır 134) | hayır | dağıtım durdurulur |
| 5 | Changelog yazıldı | Aşama 1 (satır 135) | hayır | dağıtım durdurulur |
| 6 | Migration dosyası hazır | Aşama 1 (satır 136) | hayır | dağıtım durdurulur |
| 7 | Güvenlik mühendisi onayı | Aşama 2 (satır 140) | hayır | dağıtım durdurulur |
| 8 | Secret'lar doğru | Aşama 2 (satır 142) | hayır | dağıtım durdurulur |
| 9 | Ortam değişkenleri doğru | Aşama 2 (satır 143) | hayır | dağıtım durdurulur |
| 10 | Beş test türü geçti | Aşama 3 (satır 146-154) | evet | dağıtım durdurulur |
| 11 | Risk seviyesine göre onay | Aşama 4 (satır 156-162) | hayır | dağıtım durdurulur |
| 12 | Geri dönüş planı yazıldı | §9 bu dosya | hayır | dağıtım durdurulur (satır 262) |

### §17.2 Sonrası (post-flight) — 8 madde

| # | Kontrol | Zamanlama | Kayıt yeri | Eksiğinde ne olur |
|---|---|---|---|---|
| 1 | Beş sağlık kontrolü | hemen | Aşama 6 kaydı | geri dönüş başlar (§9) |
| 2 | Hata oranı izlemi | izleme penceresi | Aşama 7 kaydı | pencere açılmaz |
| 3 | Performans metrikleri | izleme penceresi | Aşama 7 kaydı | pencere açılmaz |
| 4 | Kaynak kullanımı | izleme penceresi | Aşama 7 kaydı | pencere açılmaz |
| 5 | Kullanıcı geri bildirimi | izleme penceresi | Aşama 7 kaydı | pencere açılmaz |
| 6 | Dağıtım logu yazımı | dağıtım anında | Aşama 8 şablonu (satır 202) | kayıt boş kalır |
| 7 | Etki analizi (geri dönüşte) | geri dönüşte | 2. adım (satır 223) | kök neden eksik |
| 8 | Kök neden kaydı | geri dönüşte | 5. adım (satır 238) | tekrar riski |

**Pencere notu:** izleme penceresinin süresi deployment.md içinde yazılmamıştır; kontrol
listesi süreyi değil, penceresi içinde bakılacak şeyleri tanımlar (§14 madde 3).

## §18 Dağıtım kayıt şeması ve süreç uyumu

Aşama 8'deki dağıtım logu, dağıtımın tek kalıcı izidir. Şema deployment.md satır 202-210'dan
alınmıştır; alanlar değiştirilemez.

| # | Alan | Tip | Zorunlu | Örnek | Kaynak |
|---|---|---|---|---|---|
| 1 | Tarih ve saat | ISO benzeri | evet | 2026-10-12 14:30 | satır 202 |
| 2 | İşlem | metin | evet | Üretim dağıtımı | satır 204 |
| 3 | Agent | metin | evet | devops-engineer | satır 205 |
| 4 | Versiyon | sürüm | evet | X.Y.Z | satır 206 |
| 5 | Ortam | metin | evet | production | satır 207 |
| 6 | Sonuç | seçim | evet | Başarılı / Başarısız | satır 208 |
| 7 | Notlar | metin | hayır | Ek bilgiler | satır 209 |

### §18.1 Kayıt – strateji uyumu

| # | Kayıt alanına eklenecek | Zorunlu mu | Nereden gelir | Dayanak |
|---|---|---|---|---|
| 1 | Kullanılan strateji (§4) | evet | §4.1 seçim matrisi | bu dosya |
| 2 | Kapı sonucu (sert/yumuşak) | evet | CI hattı | §7 |
| 3 | Onay seviyesi | evet | §10 matrisi | deployment.md satır 158 |
| 4 | Sağlık kontrolü sonucu | evet | §8 | deployment.md satır 184 |
| 5 | Geri dönüş kaydı (varsa) | varsa | §9 | deployment.md satır 212 |

**Uyum kuralı:** kayıt alanları ile bu dosyanın §4-§10 kararları aynı adları kullanır; iki ayrı
sözlük oluşursa dağıtım geçmişi raporlanamaz.

### §18.2 Süreç kararı — dağıtım ne zaman durur

| # | Durum | Sonuç | Karar mercii | Dayanak |
|---|---|---|---|---|
| 1 | Sert kapı kırmızı | dağıtım yapılmaz | otomatik | §7 (ci.yml) |
| 2 | İnsan onayı yok | dağıtım yapılmaz | insan | satır 260 |
| 3 | Yedekleme yok | migration çalışmaz | otomatik + insan | satır 261 |
| 4 | Geri dönüş planı yok | dağıtım yapılmaz | mimari | satır 262 |
| 5 | Güvenlik doğrulaması eksik | dağıtım durur | güvenlik | satır 263 |
| 6 | Sağlık kontrolü başarısız | geri dönüş başlar | insan | §9 |

## §19 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| GitHub Actions dağıtım ortamları (environments), hedefleme ve koruma kurallarını tanımlar | https://docs.github.com/en/actions/deployment/targeting-different-environments | 2026-10-12 | HTTP 200 doğrulandı |
| GitHub Actions dağıtım (deployment) kavramı, tetikleyiciler ve akış yapısını belgeler | https://docs.github.com/en/actions/deployment/about-deployments | 2026-10-12 | HTTP 200 doğrulandı |
| Azure Pipelines environments, koruma kuralları ve izin modeli dağıtım ortamı için tanımlanır | https://learn.microsoft.com/en-us/azure/devops/pipelines/process/environments?view=azure-devops | 2026-10-12 | HTTP 200 doğrulandı |
| Docker `service create`, Swarm üzerinde yayılmış servis oluşturma komutunu tanımlar | https://docs.docker.com/reference/cli/docker/service/create/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1081 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz; 626 mevcut .md dosyasında taban-URL çakışması 0'dır.

## §20 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1081 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya (626 dosya) | 2026-10-12 |
| 4 | Repo kanıtları | numaralı döküm | 18 satır eşleşti | 2026-10-12 |
| 5 | Dağıtım işi yokluğu | workflow dizin taraması | 2 dosya: ci.yml, secret-scan.yml | 2026-10-12 |
| 6 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | metin taraması (yasaklı liste) | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | `mimari` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
