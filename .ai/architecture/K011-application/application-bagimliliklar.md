---
node_id: K011-application
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.11 + ADR-097 §5.2"
---

# K011-application — application-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.11 çocuk adları ve (b) 2026-10-12 web kanıtları geçerlidir.
> Bu dosyada değişiklik = Vault Steward onayı + ADR-097 kaydı ister.

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED | Cevabı bilinmiyor, tahmin yazıldı (yazılmadı) |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 web kanıtı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K009-security | Politika, imza, CSRF gibi kuralları uygulamak | kural tüketimi | Politika koda gömülürse ihlal → DUR |
| 2 | K010-identitiy | Oturum / rol / cihaz kimliğini doğrulamak | kimlik tüketimi | Oturum biçimi bilinmiyor ⚠️ |
| 3 | K012-services | Arka plan işini servise bırakmak (çalan parça, kullanıcı) | iş çağrısı | application-services sahipliği belirsiz ⚠️ |
| 4 | K013-integraion | Dış API kontratını kullanmak (Spotify/Apple Music akışları) | kontrat | Dış API K011'e mi K012'ye mi ait? ⚠️ |
| 5 | K014-network | Paket göndermek (HTTP/WebSocket) | taşıma | K011 protokol ayrıntısına inmemeli |
| 6 | K015-media | Çalan parçanın durumu / kitaplığı göstermek | veri okuma | UI medya modelini kopyalamamalı ⚠️ |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K017-observability | Uygulama telemetrisi/metrği toplamak | TASARIM İDDİASI |
| 2 | K019-testing | Uygulamayı sınamak (E2E / birim) | TASARIM İDDİASI |
| 3 | Diğerleri | — | ⚠️ VERIFICATION REQUIRED (v2'de ters ok tanımı yok) |

## §3 Entegrasyon sınırları (K011 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Servis çağrıları | uygulama servisleri ↔ servisler | K012 | ⚠️ UNKNOWN (REST/RPC/olay — kanıt yok) |
| 2 | Dış dünya | kontrat tüketimi | K013 | ⚠️ UNKNOWN (imza: PKCE/JJWT iddiaları §4) |
| 3 | Taşıma | istemci ağ çağrıları | K014 | ⚠️ UNKNOWN (protokol seti belirsiz) |
| 4 | Kimlik | oturum/rol sorgusu | K010 | ⚠️ UNKNOWN (oturum biçimi belirsiz) |
| 5 | Politika | kural okuması | K009 | ⚠️ UNKNOWN (politika biçimi belirsiz) |
| 6 | Medya | durum / kitaplık okuması | K015 | ⚠️ UNKNOWN (görünüm sözleşmesi belirsiz) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K011 yalnız bu altı kapıdan çıkar; başka domain'e **doğrudan dosya bağımlılığı yasak**.
2. Kapıların sahibi kendi kontratını tanımlar; K011 kendi sürümünü **dayatamaz**.
3. Hata biçimi kapı sahibine aittir; K011 onu yorumlar, sarmalamaz (⚠️ tasarım iddiası).
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | desktop ↔ electron tekrarı | ikisi de "masaüstü penceresi" tanımında | iki ayrı kabuk kodu | §7/6 açık soru |
| 2 | application-services ↔ K012-services | ikisi de "servis" adını taşıyor | sorumluluk dağılığı bulanık | §7/4 |
| 3 | application-testing ↔ K019-testing | iki test sahibi | test nerede koşar belirsiz | §7/5 |
| 4 | K015'e doğrudan arayüz bağımlılığı | UI medya modelini ezerse iki gerçeklik | veri tutarsızlığı | K015 ile sınır ADR'si |
| 5 | Çatı/framework bağımlılığı | kanıt yok, ad yok | teknoloji kararı görünmez | tur-2 kanıtı bekliyor |
| 6 | K013'e çift yönlü temas | akış K013'ü tetikler, hata K011'e döner | hata yayılımı | error-handling sahibi K013 |
| 7 | Kimlik (K010) yazımı "identitiy" | v2 kuralı imla hatalarını korur | yanlış arama/kanıt kaybı | yazım DOKUNULMAZ (v2 §3) |

**Döngü denetimi:** §1'de K011'e geri dönüş yok → bağımlılık grafiği **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2'de geçen adlar (K010-identitiy dahil) imla düzeltmesi yapılmadan yazılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
