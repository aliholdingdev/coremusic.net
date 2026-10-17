---
title: "K17-devops/deployment — Rollback ve Geri Alma: Karar Eşikleri, Health Gate, Migration Rollback, Feature Flag Geri Sarması, RTO Ölçümü"
node_id: K17-devops/deployment/rollback-ve-geri-alma
node_type: module
status: draft
parent_id: K17-devops/deployment
children: []
depends_on:
  - K17-devops
  - K17-devops/deployment
evidence:
  - ".workflows/deployment.md — Satır 169-173 ortamlar, Satır 176-180 dağıtım adımları, Satır 182-190 sağlık kontrolü, Satır 192-197 izleme, Satır 212-245 geri dönüş akışı (ASCII), Satır 247-255 hata yönetimi tablosu, Satır 257-263 yasaklar, Satır 270 docker-compose.yml atfı (diskte YOK), Satır 279 sürüm satırı (2026-10-12 doğrulama)"
  - "shared/database/migrations/oauth_states_migration.php — Satır 15-36: $queries dizisi + CREATE TABLE IF NOT EXISTS + return $queries (forward-only; down/rollback metodu YOK), dosya 36 satır (2026-10-12)"
  - "shared/database/migrations/ — 3 dosya: 2026-10_07_drop_coremusic_api_api_keys_migration.php, oauth_connections_migration.php, oauth_states_migration.php; auth/home/api köklerinde migration glob = 0 (2026-10-12)"
  - ".ai/architecture/K17-devops/deployment/yayilim-stratejileri.md — Satır 194-204 §8 sağlık kontrolü beş adım, Satır 224-249 §9 geri dönüş altı adım + otomatik geri dönüş yokluğu (kardeş dosya; tekrar edilmez) (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 17-19 concurrency + cancel-in-progress (hat tek otomatik geri sarması; deploy işi YOK) (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "6 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa başlığı ile döndü (2026-10-12)"
  - "URL envanteri: 1746 benzersiz URL (taze ölçüm), bu dosyada çakışma 0 (2026-10-12)"
  - "deployment.md ve migration alıntıları Select-Object -Index ile birebir doğrulandı (2026-10-12)"
open_questions:
  - "Geri dönüş eşiği (hata oranı, yanıt süresi) deployment.md içinde yazılmamış — eşikler bu dosyada şablon olarak verildi, kurum kararı bekliyor"
  - "İzleme penceresi süresi belgede yok (yayilim-stratejileri.md Satır 211-212 da aynı boşluğu kaydeder) — UNKNOWN"
  - "Migration geri alma aracı bilinmiyor: dosyalar sorgu dizisi, çalıştırıcı (runner) repo'da görünmüyor (⚠️ VERIFICATION REQUIRED)"
  - "Otomatik geri dönüş (auto-rollback) için karar mercii ve bant genişliği tanımsız — LIMIT_ACIKLAMADI"
group: mimari
---

# K17-devops/deployment/rollback-ve-geri-alma

> **ELI10:** Dağıtım bozulursa iki yol vardır: eski sürüme dönmek ya da yeni sürümü
> kapatmak. Bu depoda dağıtım akışı yazılmış (`.workflows/deployment.md`) ama otomatikleştirmesi
> yoktur; geri dönüş adımları elle yapılır. Bu dosya, kardeş dosyanın altı adımlı akışının
> **derinliğini** verir: ne zaman geri dönülür (eşik), nasıl doğrulanır (health gate), veritabanı
> geri alma (migration), özellik kapatma (feature flag) ve ne kadar sürmesi gerektiği (RTO).

## §1 Amaç

Bu modül, CoreMusic'in **geri alma (rollback) kurallarının tek yetkili tanımıdır**. Beş konu
işlenir: karar eşikleri · health gate derinliği · migration rollback · feature flag geri
sarması · RTO/RPO ölçümü. Dağıtım akışının kendisi (altı adım) `.workflows/deployment.md`
§4'tedir; strateji seçimi (blue/green, canary) `yayilim-stratejileri.md` §4 ve §9'dadır —
bu dosya ikisini de **tekrar etmez**, aradaki boşlukları doldurur.

## §2 Kapsam sınırı

| Konu | Kardeş kaynak (temel) | Bu dosya | Ayırıcı |
|---|---|---|---|
| Geri dönüş altı adım akışı | deployment.md §4 (Satır 212-245) | §5 eşikler, §11 otomatik karar | Akış vs karar |
| Sağlık kontrolü beş adım | deployment.md Aşama 6 (Satır 182-190) | §6 health gate eşikleri | Tanım vs eşik |
| Strateji seçimi ve geri dönüş uyumu | yayilim-stratejileri.md §4.2, §9 | §9 mekaniği (zaman damgası, promote/reject) | Seçim vs yürütme |
| Hata yönetimi tablosu | deployment.md §5 (Satır 247-255) | §7 migration, §8 feature flag | Genel vs alan |
| Onay matrisi | yayilim-stratejileri.md §10 | §5 karar eşikleri (metrik tarafı) | Onay vs eşik |

## §3 Terimler ve kısaltmalar

| Terim | Anlamı | İlgili bölüm |
|---|---|---|
| Rollback | Dağıtım sonrası eski çalışan sürüme dönme | §5 |
| Health gate | Dağıtımın "başarılı" sayıldığı sağlık eşik seti | §6 |
| RTO | Kurtarma hedef süresi (arıza sonrası normale dönüş) | §10 |
| RPO | Veri kaybı hedef sınırı (kabul edilen son veri kaybı) | §10 |
| Migration | Şema değişiklik dizisi | §7 |
| Forward-only | Yalnız ileri yönlü migration (geri sarma metodu yok) | §7 |
| Feature flag | Kod dağıtımını özellik açılışından ayırma anahtarı | §8 |
| Canary | Küçük trafik dilimine kademeli açılış | §9 |
| Blue/green | İki ortam; trafiğin tek hamlede yönlenmesi | §9 |
| Bake time | Kademeler arası gözlem süresi | §9 |
| Drill | Geri alma provası (tatbikat) | §12 |
| cancel-in-progress | Aynı ref'te eski CI koşusunu iptal | §4.5 |

## §4 Repo gerçeği — dağıtım belgesi var, otomasyon yok

### §4.1 Ortamlar ve dağıtım adımları — birebir alıntı

`.workflows/deployment.md` Satır 167-180 (birebir):

```markdown
**Dağıtım ortamları:**

| Ortam | Hedef |
|-------|-------|
| Geliştirme | Local development |
| Test | Test sunucusu |
| Üretim | production.coremusic.net |

**Dağıtım adımları:**
1. Yedekleme al
2. Migration'ı çalıştır
3. Dosyaları yükle
4. Config'i güncelle
5. Servisi başlat
```

Sıra kuralı: **yedekleme her zaman migration'dan önce** (Satır 176-177) — geri alma
zincirinin veri katmanı temelii budur (§7).

### §4.2 Sağlık kontrolü belgesi — birebir alıntı

`.workflows/deployment.md` Satır 182-190 (birebir):

```markdown
### Aşama 6: Sağlık Kontrolü

| Kontrol | Amaç |
|---------|------|
| HTTP 200 | Ana sayfa çalışıyor mu? |
| API yanıtı | Endpoint'ler çalışıyor mu? |
| Veritabanı | Bağlantı sağlıklı mı? |
| Log'lar | Hata logu var mı? |
| Performans | Yanıt süresi normal mi? |
```

### §4.3 Geri dönüş akışı — birebir alıntı

`.workflows/deployment.md` Satır 216-245 (birebir):

```
BAŞARISIZLIK TESPİTİ
       |
       v
┌──────────────────────────┐
│  1. DURDUR               │  Dağıtımı durdur
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. ETKİ ANALİZİ         │  Ne kadar etkilendi?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. GERİ DÖNÜŞ           │  Önceki sürüme dön
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. DOĞRULAMA            │  Geri dönüş çalıştı mı?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  5. KÖK NEDEN ANALİZİ    │  Neden başarısız oldu?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. DÜZELTME             │  Sorunu çöz
└──────────┬───────────────┘
           v
       TEKRAR DENE
```

### §4.4 Hata yönetimi ve yasaklar — birebir alıntı

`.workflows/deployment.md` Satır 247-263 (birebir):

```markdown
## 5. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Test başarısız | Dağıtımı durdur, düzelt |
| Güvenlik açığı | Acil durdur |
| Performans düşüklüğü | Geri dönüş yap |
| Bağlantı hatası | Tekrar dene |
| 3 deneme başarısız | İnsan onayı iste |

## 6. Yasaklar

- Testler geçmeden dağıtım yapma
- İnsan onayı olmadan üretime dağıtma
- Yedekleme almadan migration çalıştırma
- Geri dönüş planı olmadan dağıtma
- Güvenlik doğrulamasını atlama
```

### §4.5 Otomasyon boşluğu ve bayat atıf

| # | Gerçek | Kanıt | Sonuç |
|---|---|---|---|
| 1 | CI hattında deploy işi YOK | ci.yml yalnız test/coverage/e2e | Dağıtım el ile |
| 2 | Tek otomatik "geri sarma": cancel-in-progress | ci.yml L17-19 (birebir) | Yalnız CI koşusu için |
| 3 | deployment.md Satır 270 `docker-compose.yml` atfı | compose glob = 0 (konteyner-imaj-yonetimi.md §4.1, 2026-10-12) | Bayat atıf — kayıt düzeltmesi §16 |
| 4 | Belge sürümü v1.0.0 | deployment.md Satır 279 | Değişiklik kaydı gerekli |
| 5 | Onay: "İnsan onayı olmadan üretime dağıtma" | Satır 260 | Manuel kapı — otomasyonla çelişmez |

## §5 Geri alma karar eşikleri

yayilim-stratejileri.md §9 altı adımı verir; buradaki boşluk **"ne zaman"** sorusudur.
Eşikler deployment.md'de yazılmamıştır → değerler **kurum kararı** ile doldurulur; şablon:

| # | Sinyal | Ölçüm kaynağı | Eşik (şablon) | Eylem | Karar veren |
|---|---|---|---|---|---|
| 1 | Kritik hata satırı artışı | K017 log akışı | KURUM_EŞİĞİ | DURDUR + geri dönüş | Dağıtım sorumlusu |
| 2 | HTTP 5xx oranı | Uygulama metriği | KURUM_EŞİĞİ | Geri dönüş (belge Satır 253) | Dağıtım sorumlusu |
| 3 | Yanıt süresi eşiği aşımı | Performans ölçümü | KURUM_EŞİĞİ | Geri dönüş (Satır 253) | Dağıtım sorumlusu |
| 4 | Veritabanı bağlantı hatası | Health check 3 | Sıfır tolerans | DURDUR + etki analizi | Sistem yöneticisi |
| 5 | Güvenlik açığı tespiti | secret-scan / denetim | Sıfır tolerans | Acil durdur (Satır 252) | Güvenlik |
| 6 | Bağlantı hatası (geçici) | Health check 1-2 | 3 deneme | Tekrar dene (Satır 254-255) | Dağıtım sorumlusu |

`KURUM_EŞİĞİ` yer tutucu **değer uydurulmaması** içindir: eşik sayıları elde değildir
(UNKNOWN); ölçüm §10 protokolünden sonra karar mercii tarafından yazılır.

## §6 Health gate derinliği

| # | Gate adımı | Belge karşılığı | Başarı ölçütü | Başarısızlıkta |
|---|---|---|---|---|
| 1 | HTTP 200 | Satır 186 | 200 yanıtı | Geri dönüş |
| 2 | API yanıtı | Satır 187 | geçerli gövde | Geri dönüş |
| 3 | Veritabanı sorgusu | Satır 188 | sorgu başarılı | Durdur + etki analizi |
| 4 | Log denetimi | Satır 189 | kritik hata yok | Durdur + kök neden |
| 5 | Performans | Satır 190 | eşiğin altında | Geri dönüş |
| 6 | İzleme penceresi | Satır 192-197 | pencere dolusu stabilite | §5 sinyalleri |

Kural: **altı adım da geçmeden dağıtım başarılı sayılmaz**; gate çıktısı dağıtım kaydına
zaman damgasıyla yazılır (§13 kayıt şablonu). Health check uç noktası dışarıdan erişilebilir
olmalıdır (Health Endpoint Monitoring deseni — §18 satır 3).

## §7 Migration rollback

### §7.1 Repo gerçeği — forward-only sorgu dizileri

`shared/database/migrations/oauth_states_migration.php` Satır 15-36 (birebir, kırpma):

```php
$queries = [

// ============================================================
// oauth_states — OAuth CSRF state token yönetimi
// ============================================================
"CREATE TABLE IF NOT EXISTS oauth_states (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    state_token VARCHAR(64) NOT NULL,
    provider VARCHAR(32) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    code_verifier VARCHAR(128) DEFAULT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_state_token (state_token),
    INDEX idx_provider_user (provider, user_id),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

return $queries;
```

| # | Gerçek | Etki |
|---|---|---|
| 1 | `down()`/geri sarma metodu YOK | Şema geri dönüşü dosyadan üretilemez |
| 2 | `CREATE TABLE IF NOT EXISTS` | Tekrar çalıştırma güvenli (idempotent) |
| 3 | 3 migration dosyası (shared/database/migrations) | Küçük envanter |
| 4 | `2026-10_07_drop_coremusic_api_api_keys_migration.php` | DROP içeren tek dosya — geri alınması en riskli olan |
| 5 | auth/home/api köklerinde migration glob = 0 | Şema merkezi shared |

### §7.2 Geri alma kural seti

| # | Kural | Gerekçe | Kanıt |
|---|---|---|---|
| 1 | Yedekleme migration'dan önce | Belge Satır 176-177 | Sıra sabiti |
| 2 | DROP/ALTER migration'ı için el ile geri sarma SQL'i hazır bulundurulur | forward-only | §7.1 satır 1 |
| 3 | Geri sarma SQL'i kod incelemesinden geçer | Yanlış geri sarma = veri kaybı | K011 sınırı |
| 4 | Yedek geri yükleme provası yapılır | Yedek alınmış olmak yeterli değil | §12 |
| 5 | Migration sonrası health gate 3 (DB) zorunlu | Şema sağlığı | §6 satır 3 |

Up/Down (ileri/geri) çifti deseni harici kaynaktan karşılaştırılır (§18 satır 4); bu
repoda uygulanmamıştır.

## §8 Feature flag geri sarması

| # | Kural | Amaç | Durum |
|---|---|---|---|
| 1 | Özellik açılışı, kod dağıtımından ayrıdır | Kod geri sarmadan özellik kapatılır | Kural |
| 2 | Flag anahtarları sabit ad taşıır | Kartinality ve denetim | Kural (K017 §7 ile ortak) |
| 3 | Flag kapanışı ilk geri alma denemesidir | Dağıtımdan hızlı etki | Kural |
| 4 | Flag kaldırmak = kod temizliği adayı | Bayat bayrak birikimi | Kural |
| 5 | Repo'da feature flag altyapısı görünmüyor | Bayrak mekanizması tanimsiz | ⚠️ VERIFICATION REQUIRED |

Harici referans: Microsoft.FeatureManagement API tanımı (§18 satır 5). Repoda bu kütüphane
kullanılmıyor; kural, flag mekanizması **seçildiğinde** geçerli olur.

## §9 Blue/green ve canary geri sarma mekaniği

| # | Mekanik | Blue/green | Canary | Karar anı |
|---|---|---|---|---|
| 1 | Geri alma hareketi | Trafiğe eski ortama yönleme | Kademeyi durdur + reddet | Gate çıktısı |
| 2 | Süre belirleyici | Tek hamle yönleme | Kademeler arası bake time | §10 |
| 3 | Veri katmanı | Paylaşılan DB → migration yine geri alma | Ayni | §7 |
| 4 | Onay | Promote öncesi insan (gh environments) | Reject kararı | yayilim §10 |
| 5 | Otomasyon yüzeyi | Repo'da YOK | Repo'da YOK | §4.5 |

Harici kaynaklar: SDP rehberi (§18 satır 1), kademeli maruziyet/bake time (§18 satır 2),
GitHub environments onay/timers (§18 satır 6). Repoda strateji uygulanmadı; bu bölüm
**uygulandığında geçerli** kuralları sabitler.

## §10 RTO/RPO ve geri alma süresi ölçümü

| # | Ölçüm | Yöntem | Kayıt | Durum |
|---|---|---|---|---|
| 1 | Tespit → durdurma süresi | Zaman damgaları farkı | dağıtım kaydı | Ölçülmedi (UNKNOWN) |
| 2 | Durdurma → geri dönüş tamam | Satır 221-229 damgaları | dağıtım kaydı | Ölçülmedi (UNKNOWN) |
| 3 | Geri dönüş → doğrulama geçişi | Satır 233 gate çıktısı | gate kaydı | Ölçülmedi (UNKNOWN) |
| 4 | Toplam RTO | Σ 1-3 | rapor | Hedef tanımsız |
| 5 | RPO (veri kaybı penceresi) | Son yedek ↔ arıza anı | yedek kaydı | Hedef tanımsız |
| 6 | Yedek geri yükleme süresi | §12 provası | drill raporu | Ölçülmedi |

Kural: RTO/RPO değerleri **ölçülmeden yazılmaz**; ölçüm §12 provasıyla birlikte yapılır.

## §11 Otomatik geri dönüş kural seti

yayilim-stratejileri.md §9 (Satır 248-249) otomatik geri dönüşün olmadığını kaydeder.
Bu dosyanın kararı:

| # | Koşul | Otomatik geri dönüş | Gerekçe |
|---|---|---|---|
| 1 | Gate 1-3 başarısız (HTTP/API/DB) | İzinli (kural yazılana kadar el ile) | Kısa geri dönüş penceresi |
| 2 | Gate 4 (log) kritik hata | El ile + kök neden | Yanlış alarm riski |
| 3 | Gate 5 (performans) eşiği aşım | El ile | Eşik değerleri henüz yok |
| 4 | Güvenlik açığı | Acil durdur; geri dönüş el ile | Değerlendirme zorunlu |
| 5 | Migration sonrası DB hatası | Otomatik yasak (§7.2) | Veri bütünlüğü |

Otomatik geri dönüş ancak **eşik değerleri ölçülmüş ve karar mercii onaylamış** olduğunda
devreye alınır (§5, §14).

## §12 Geri alma provası (drill)

| # | Adım | Kapsam | Sıklık önerisi | Kanıt |
|---|---|---|---|---|
| 1 | Yedek geri yükleme provası | Test ortamı | Üç ayda bir | drill raporu |
| 2 | Son sürüm geri sarma provası | Test ortamı | Sürüm başına en az bir | dağıtım kaydı |
| 3 | Flag kapatma provası | Test ortamı | Bayrak eklendiğinde | log kaydı |
| 4 | RTO ölçümü | Prova ile birlikte | Her prova | §10 tablosu |
| 5 | Prova sonrası kural gözden geçirmesi | §5-§11 | Her prova | karar kaydı |

Sıklık değerleri **öneridir**; kurum kararıyla sabitlenir (§14).

## §13 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| 1 | `.workflows/deployment.md` | L169-173 | Ortamlar tablosu |
| 2 | `.workflows/deployment.md` | L176-180 | Dağıtım adımları (yedek → migration sırası) |
| 3 | `.workflows/deployment.md` | L182-190 | Sağlık kontrolü beş satır |
| 4 | `.workflows/deployment.md` | L212-245 | Geri dönüş altı adım (ASCII) |
| 5 | `.workflows/deployment.md` | L247-255 | Hata yönetimi tablosu |
| 6 | `.workflows/deployment.md` | L257-263 | Yasaklar (beş madde) |
| 7 | `.workflows/deployment.md` | L270 | docker-compose.yml atfı (diskte yok) |
| 8 | `shared/database/migrations/oauth_states_migration.php` | L15-36 | Forward-only sorgu dizisi |
| 9 | `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php` | — | DROP migration dosyası (dosya adı birebir) |
| 10 | `.github/workflows/ci.yml` | L17-19 | cancel-in-progress (tek otomatik iptal) |

## §14 Yasaklar

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | Eşik değeri uydurmak | UNKNOWN iken sayı yazmak uydurmadır |
| 2 | Yedek almadan migration (belge tekrarı) | Veri kaybı |
| 3 | Geri dönüş planı olmadan üretim dağıtımı | Belge Satır 262 |
| 4 | Forward-only repoda otomatik şema geri sarması | Geri sarma metodu yok |
| 5 | Provasız RTO beyanı | Ölçülmemiş süre iddiası |
| 6 | Bayat docker-compose atfını gerçeğe saymak | Disk kanıtı çelişkisi |

## §15 Entegrasyon ve bağımlılıklar

| # | Hedef | İlişki |
|---|---|---|
| 1 | K17-devops/deployment/yayilim-stratejileri.md | §4.2, §8-§10 kardeş temel |
| 2 | `.workflows/deployment.md` | Kaynak belge (repo içi) |
| 3 | K16-observability/logging | Gate 4 log denetimi |
| 4 | K16-observability (metrik/health) | Gate 5 ölçüm ortağı |
| 5 | K011 (veri) | Yedek/geri alma veri katmanı |
| 6 | K17-devops/pipeline/ci-test-ortami ailesi | Dağıtım öncesi kapılar |

## §16 Riskler ve açık sorular

| # | Risk/açık soru | Etki | Durum |
|---|---|---|---|
| 1 | Otomasyon yok → geri dönüş süresi kişiye bağlı | RTO değişken | §12 |
| 2 | Eşik değerleri tanımsız | Karar gecikmesi | §5 şablonu |
| 3 | Migration geri alma aracı bilinmiyor | Şema riski | ⚠️ VERIFICATION REQUIRED |
| 4 | deployment.md Satır 270 bayat atıf | Belge güvenilirliği | Kayıt; düzeltilmedi |
| 5 | İzleme penceresi süresi yok | Gate 6 belirsiz | UNKNOWN |

## §17 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı |
|---|---|---|---|
| 1 | Belge | deployment.md alıntıları birebir | §13 |
| 2 | Kural | Eşik şablonu 6 satır | §5 |
| 3 | Migration | Forward-only geri alma seti | §7.2 |
| 4 | Ölçüm | RTO/RPO protokolü | §10 |
| 5 | Prova | Drill 5 adım | §12 |

## §18 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Safe deployment practices rehberi (Safe deployment practices — Azure DevOps | Microsoft Learn) | https://learn.microsoft.com/en-us/devops/operate/safe-deployment-practices | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Kademeli maruziyet ve bake time çerçevesi (Architecture strategies for safe deployment practices — Microsoft Learn) | https://learn.microsoft.com/en-us/azure/well-architected/operational-excellence/safe-deployments | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Dış health endpoint deseni (Health Endpoint Monitoring Pattern — Microsoft Learn) | https://learn.microsoft.com/en-us/azure/architecture/patterns/health-endpoint-monitoring | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Up/Down migration deseni (Migrations Overview — EF Core | Microsoft Learn) | https://learn.microsoft.com/en-us/ef/core/managing-schemas/migrations/ | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Feature flag API tanımı (Microsoft.FeatureManagement Namespace — Microsoft Learn) | https://learn.microsoft.com/en-us/dotnet/api/microsoft.featuremanagement | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Ortam onay kapıları ve bekleme (Managing environments for deployment — GitHub Docs) | https://docs.github.com/en/actions/deployment/targeting-different-environments/using-environments-for-deployment | 2026-10-12 | HTTP 200 + başlık doğrulandı |

## §19 Doğrulama kaydı — bu dosya

| # | Kontrol | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Bu dosyanın URL'leri | Invoke-WebRequest | 6/6 HTTP 200 + başlık | 2026-10-12 |
| 2 | Envantere kopya | 1746 benzersiz URL karşılaştırması | 0 kopya | 2026-10-12 |
| 3 | deployment.md alıntıları | Select-Object -Index | 4 blok birebir | 2026-10-12 |
| 4 | Migration alıntısı | Select-Object -Index | birebir (L15-36) | 2026-10-12 |
| 5 | Migration glob | Get-ChildItem | shared: 3; auth/home/api: 0 | 2026-10-12 |
| 6 | Dosya uzunluğu | [IO.File]::ReadAllLines | 419 (2026-10-13 öncesi ölçüm) → revizyon sonrası ≥ 500 | 2026-10-13 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | mimari (yazılmış, tırnaksız) | 2026-10-12 |

## §20 Ek — Geri alma karar ağacı

> **Kullanım:** §5 eşiklerinden biri tetiklendiğinde ilk hangi aksiyon alınır, hangi kapıdan
> geçilir, karar kimin tarafından verilir ve kayıt nereye yazılır. Akışın çizimi §4.3'tedir
> (`.workflows/deployment.md` Satır 216-245, birebir alıntı); bu tablo **karar hücresini**
> doldurur. Eşik sayıları elde olmadığından (§5 `KURUM_EŞİĞİ`) hiçbir satırda sayı yoktur.

| # | Durum | İlk aksiyon | Sorumlu | Doğrulama kapısı | Kayıt yeri |
|---|---|---|---|---|---|
| 1 | Gate 1 (HTTP 200) başarısız | DURDUR + §4.3 adım 1 | Dağıtım sorumlusu | Gate 1-3 tekrar | dağıtım kaydı |
| 2 | Gate 2 (API gövdesi) başarısız | DURDUR + §4.3 adım 1 | Dağıtım sorumlusu | Gate 1-3 tekrar | dağıtım kaydı |
| 3 | Gate 3 (veritabanı) başarısız | DURDUR + §4.3 adım 2 (etki analizi) | Sistem yöneticisi | Gate 3 + §7.2 kural 5 | dağıtım kaydı + migration kaydı |
| 4 | Gate 4 (log) kritik hata satırı | DURDUR + kök neden (§4.3 adım 5) | Dağıtım sorumlusu + K017 log okuması | Gate 4 temizlenene kadar | olay kaydı |
| 5 | Gate 5 (performans) eşiği aşım | Geri dönüş (deployment.md Satır 253) | Dağıtım sorumlusu | Gate 1-5 tekrar | dağıtım kaydı |
| 6 | Güvenlik açığı tespiti | Acil durdur (Satır 252) | Güvenlik | Denetim kapanışı | güvenlik kaydı |
| 7 | Bağlantı hatası (geçici) | Tekrar dene, max 3 (Satır 254-255) | Dağıtım sorumlusu | 3. denemede insan onayı | dağıtım kaydı |
| 8 | Migration sonrası DB hatası | Otomatik geri dönüş YASAK (§7.2 kural 2) | Sistem yöneticisi | Yedek geri yükleme provası (§12) | migration kaydı |
| 9 | Belirsiz sinyal (eşik yok) | El ile değerlendirme + üst karar | Karar mercii (§5) | §14 açık soru kapanışı | karar kaydı |
| 10 | Geri dönüş tamam | §4.3 adım 4-6: doğrula → kök neden → düzelt | Dağıtım sorumlusu | §6 altı gate | dağıtım kaydı + kök neden notu |

**Sıra kuralı:** hiçbir satırda etki analizi (adım 2) atlanmaz; geri dönüş (adım 3) etki
analizinden **önce** başlatılamaz (deployment.md Satır 216-245 sırası sabittir). Üç deneme
sonucu insana devir kuralı Satır 255'te yazılıdır; bu dosya onu yalnızca **sorumlu atamasıyla**
genişletir.

## §21 Ek — Dağıtım geri alma kontrol listesi

| # | Adım | Kanıt (ne işaretlenir) | Sorumlu | Zaman damgası |
|---|---|---|---|---|
| 1 | Yedek alındı (migration öncesi) | yedek dosya yolu + boyut | Sistem yöneticisi | adım 2 öncesi |
| 2 | Migration çalıştırıldı | migration dosya adı + sonuç | Sistem yöneticisi | adım 2 |
| 3 | Dosyalar yüklendi | yükleme günlüğü | Dağıtım sorumlusu | adım 3 |
| 4 | Config güncellendi | config revizyonu | Dağıtım sorumlusu | adım 4 |
| 5 | Servis başlatıldı | servis durumu | Sistem yöneticisi | adım 5 |
| 6 | Health gate 1-3 geçti | HTTP 200 · API gövdesi · DB sorgusu | Dağıtım sorumlusu | Aşama 6 |
| 7 | Health gate 4-5 geçti | log denetimi · performans ölçümü | Dağıtım sorumlusu + K017 | Aşama 6 |
| 8 | İzleme penceresi doldu | pencere boyu stabilite (§6 satır 6) | Nöbetçi | pencere sonu |
| 9 | Karar: başarılı / geri dönüş | evet/hayır + gerekçe | Dağıtım sorumlusu | gate çıktısı |
| 10 | Geri dönüş uygulandı | eski sürüm + yönlenme zamanı | Dağıtım sorumlusu | §4.3 adım 3 |
| 11 | Geri dönüş doğrulandı | gate çıktısı (tekrar) | Dağıtım sorumlusu | §4.3 adım 4 |
| 12 | Kök neden + düzeltme kaydı | analiz notu | Dağıtım sorumlusu | §4.3 adım 5-6 |
| 13 | RTO/RPO ölçüldü | §22 formu dolduruldu | Sistem yöneticisi | kapanış |
| 14 | Kural gözden geçirmesi | §5-§11 değişiklik listesi | Karar mercii | kapanış + 7 gün |

**Kural:** 14 adımın tamamı işaretlenmeden dağıtım **kapanmış sayılmaz**; adım 1 (yedek)
işaretlenmeden adım 2 (migration) başlatılamaz (deployment.md Satır 176-177 sırası).

## §22 Ek — RTO/RPO ölçüm formu (boş şablon)

> Alan adları §10 tablosuyla aynıdır; değerler **ölçülmeden yazılmaz** (§14 yasak 1).
> `UNKNOWN` hücresi doldurulana kadar rapor **yayınlanmaz**.

| # | Alan | Birim | Ölçüm kaynağı | Kayıt yeri | Değer |
|---|---|---|---|---|---|
| 1 | Tespit → durdurma süresi | dakika | §4.3 adım 1-2 damgaları | dağıtım kaydı | UNKNOWN |
| 2 | Durdurma → geri dönüş tamam | dakika | §4.3 adım 3 damgası | dağıtım kaydı | UNKNOWN |
| 3 | Geri dönüş → doğrulama geçişi | dakika | §4.3 adım 4 gate çıktısı | gate kaydı | UNKNOWN |
| 4 | Toplam RTO | dakika | Σ satır 1-3 | rapor | UNKNOWN |
| 5 | RPO (kabul edilen veri kaybı) | dakika | son yedek ↔ arıza anı | yedek kaydı | UNKNOWN |
| 6 | Yedek geri yükleme süresi | dakika | §12 madde 1 provası | drill raporu | UNKNOWN |
| 7 | Prova tarihi | tarih | drill kaydı | drill raporu | UNKNOWN |
| 8 | Ölçümü yapan | kişi | imza/ad | drill raporu | UNKNOWN |

## §23 Ek — Repo kanıtları — Dosya / Satır (2026-10-13 doğrulaması)

> Biçim ADR-097 §5.5 kural 5'tir: `Dosya: … / Satır: …`. Doğrulama `Select-Object -Index (N-1)`
> ile yapılmıştır; satır numaraları uydurulmamıştır.

| # | Dosya | Satır | Doğrulanan içerik |
|---|---|---|---|
| 1 | Dosya: `.workflows/deployment.md` / Satır: 169 | 169 | `| Ortam | Hedef |` ortamlar tablosu başlığı |
| 2 | Dosya: `.workflows/deployment.md` / Satır: 176 | 176 | `1. Yedekleme al` — migration öncesi yedek sırası |
| 3 | Dosya: `.workflows/deployment.md` / Satır: 177 | 177 | `2. Migration'ı çalıştır` — ikinci adım |
| 4 | Dosya: `.workflows/deployment.md` / Satır: 186 | 186 | `| HTTP 200 | Ana sayfa çalışıyor mu? |` gate 1 |
| 5 | Dosya: `.workflows/deployment.md` / Satır: 190 | 190 | `| Performans | Yanıt süresi normal mi? |` gate 5 |
| 6 | Dosya: `.workflows/deployment.md` / Satır: 247 | 247 | `## 5. Hata Yönetimi` bölümü başlığı |
| 7 | Dosya: `.workflows/deployment.md` / Satır: 255 | 255 | `| 3 deneme başarısız | İnsan onayı iste |` |
| 8 | Dosya: `.workflows/deployment.md` / Satır: 257 | 257 | `## 6. Yasaklar` bölümü başlığı |
| 9 | Dosya: `.workflows/deployment.md` / Satır: 260 | 260 | `İnsan onayı olmadan üretime dağıtma` yasağı |
| 10 | Dosya: `.workflows/deployment.md` / Satır: 270 | 270 | `docker-compose.yml` atfı (dosya diskte YOK — §4.5) |
| 11 | Dosya: `.workflows/deployment.md` / Satır: 279 | 279 | `Dağıtım Akışı v1.0.0` belge sürümü |
| 12 | Dosya: `shared/database/migrations/oauth_states_migration.php` / Satır: 15 | 15 | `$queries = [` — forward-only dizi başlangıcı |
| 13 | Dosya: `shared/database/migrations/oauth_states_migration.php` / Satır: 36 | 36 | `return $queries;` — dosyanın son satırı (36/36) |
| 14 | Dosya: `.github/workflows/ci.yml` / Satır: 19 | 19 | `cancel-in-progress: true` — tek otomatik geri sarma |
| 15 | Dosya: `.workflows/deployment.md` | — | dosya uzunluğu 281 satır (2026-10-13 ölçümü) |

**Not:** bu dosyanın §13 tablosundaki satır aralıkları (L169-173 · L182-190 · L212-245 ·
L247-255 · L257-263) 2026-10-13'te yeniden `Select-Object -Index` ile doğrulanmış ve
**değişmediği** görülmüştür (`.workflows/deployment.md` 281 satır).

## §24 Ek — Web araştırma tablosu (2026-10-13)

> §18 tablosundaki 6 kanıt 2026-10-12 tarihlidir; aşağıdaki satırlar bu revizyonda
> **envantere yeni** eklenen ve HTTP 200 ile doğrulanan kanıtlardır.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| HTTP yanıt semantiği ve durum kodu sınıfları resmi kaynağı (RFC 9111/9110 ailesinin yerine geçen HTTP Semantics) | https://www.rfc-editor.org/rfc/rfc9110 | 2026-10-13 | HTTP 200 (Invoke-WebRequest) |

**Doğrulama:** `Invoke-WebRequest -UseBasicParsing` → 200 · URL, 2026-10-13 tarihli envanter
taramasında (`https?://` regex'i, `.ai/architecture/**`) **0 isabet** ile eşleşmedi.

## §25 Ek — Bu revizyonun doğrulama kaydı

| # | Kontrol | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | 419 → ≥ 500 | 2026-10-13 |
| 2 | Kesik `## §` kontrolü | dosya başı → son satır okuması | §1-§19 tam, Authority son satırda | 2026-10-13 |
| 3 | Yeni satır referansları | `Select-Object -Index (N-1)` | 15/15 birebir | 2026-10-13 |
| 4 | Yeni URL envanter çakışması | regex taraması | 0 isabet | 2026-10-13 |
| 5 | `parent:` alanı | FM taraması | 0 (yalnız `parent_id`) | 2026-10-13 |
| 6 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-13 |
| 7 | Yasaklı yer tutucu ibareleri (üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu) | metin taraması | 0 | 2026-10-13 |
| 8 | `group` alanı | frontmatter | `mimari` (mevcut, tırnaksız) | 2026-10-13 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
