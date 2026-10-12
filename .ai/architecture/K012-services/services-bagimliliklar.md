---
node_id: K012-services
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.12 + ADR-097 §5.2"
---

# K012-services — services-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.12 çocuk adları ve (b) 2026-10-12 beyanları geçerlidir.
> Bu dosyada değişiklik = Vault Steward onayı + ADR-097 kaydı ister.

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 beyanı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K006-yapay-zeka / K016-AI | ai-service'in arkasındaki YZ yeteneği | yetenek çağrısı | **Hangisi belli değil** ⚠️ (registry §6) |
| 2 | K007-data | Kalıcı veri okuma/yazma | veri erişimi | Şema kararı K007'de kalmalı |
| 3 | K008-domain | İş kuralı ve model | kural tüketimi | Servis kuralı **üretirse** ihlal |
| 4 | K010-identitiy | Kullanıcı/cihaz kimliği | kimlik tüketimi | device-identity sahipliği belirsiz ⚠️ |
| 5 | K013-integraion | Dış sistem kontratları | kontrat | Servis protokolü elle konuşmamalı |
| 6 | K014-network | Servisler arası taşıma | taşıma | Taşıma biçimi UNKNOWN ⚠️ |
| 7 | K015-media | Kitaplık/metadata/akış verisi | veri okuma | İki gerçeklik riski (playback sahipliği) |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Uygulamanın servis çağrıları | TASARIM İDDİASI |
| 2 | K019-testing | Servis seviyesi testleri | TASARIM İDDİASI (sahiplik ⚠️) |
| 3 | Diğerleri | — | ⚠️ VERIFICATION REQUIRED (v2'de ters ok yok) |

## §3 Entegrasyon sınırları (K012 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Kalıcılık | servis ↔ veri | K007-data | ⚠️ UNKNOWN (repository/ORM biçimi) |
| 2 | Kural | servis ↔ domain | K008-domain | ⚠️ UNKNOWN (çağrı biçimi) |
| 3 | Kimlik | servis ↔ kimlik | K010-identitiy | ⚠️ UNKNOWN (token/oturum biçimi) |
| 4 | Dış dünya | integration-service ↔ adaptör | K013-integraion | ⚠️ UNKNOWN (kontrat biçimi) |
| 5 | Taşıma | servis ↔ ağ | K014-network | ⚠️ UNKNOWN (protokol seti) |
| 6 | Medya | servis ↔ kitaplık/akış | K015-media | ⚠️ UNKNOWN (görünüm sözleşmesi) |
| 7 | Arayüz | uygulama ↔ servis | K011-application | ⚠️ UNKNOWN (çağrı biçimi) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K012 yalnız bu yedi kapıdan çıkar; başka domain'e doğrudan dosya bağımlılığı **yasak**.
2. Dış dünya (K013) dışında kalan kapıların sahibi kontratı tanımlar; K012 dayatmaz.
3. Servisler birbirine doğrudan değil, service-contracts üzerinden konuşur → **iddia**.
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | ai-service → K006 / K016 belirsizliği | iki YZ domain'i (registry §6) | yanlış sahiplik | §7/1 — üst karar |
| 2 | playback-service ↔ K015/playback | iki "çalma" sahibi | iki gerçek durum | §7/2 — sınır ADR'si |
| 3 | audio-service ↔ K003-audio | servis mi motor mu belirsiz | sorumluluk kayması | §7/3 |
| 4 | device-service ↔ K010 device-identity | kimlik verisi iki yerde | güvenlik riski | §7/7 |
| 5 | service-contracts tekeli | tüm servisler buraya bağlı | sözleşme değişimi kırıcı | sürümleme ADR'si |
| 6 | K015'e çift bağımlılık (K012→K015, K015→K012 ihtimali) | döngü riski | test/dağıtım zorluğu | K015 dosyasında DAG denetimi |
| 7 | Kimlik (K010) yazımı "identitiy" | v2 kuralı imla hatalarını korur | yanlış arama | yazım DOKUNULMAZ (v2 §3) |

**Döngü denetimi:** §1'de K012'ye geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2'de geçen adlar imla düzeltmesi yapılmadan yazılır (K010-identitiy dahil).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
