---
node_id: K014-network
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.14 + ADR-097 §5.2"
---

# K014-network — network-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.14 çocuk/torun adları ve (b) 2026-10-12 beyanları geçerlidir.
> Bu dosyada değişiklik = Vault Steward onayı + ADR-097 kaydı ister.

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED / VERIFY REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 beyanı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K001-donanım | Ağ donanımı ve sürücüsüne erişim | donanım erişimi | Donanım yoksa bağlantı yok → fallback belirsiz ⚠️ |
| 2 | K009-security | TLS/şifreleme/anahtar kuralları | güvenlik | Şifreleme burada üretilirse güvenlik ihlali |
| 3 | K010-identitiy | Cihaz kimliği ile yetkilendirme | kimlik | device-identity sahipliği belirsiz ⚠️ |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Arayüzün ağ çağrıları | TASARIM İDDİASI |
| 2 | K012-services | Servisler arası taşıma | TASARIM İDDİASI |
| 3 | K013-integraion | Dış API taşıması | TASARIM İDDİASI |
| 4 | K015-media | Akış/indirme taşıması | TASARIM İDDİASI |
| 5 | Diğerleri (ör. K017-observability) | Ağ metrikleri | ⚠️ VERIFICATION REQUIRED |

## §3 Entegrasyon sınırları (K014 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Donanım | sürücü/OS erişimi | K001-donanım | ⚠️ UNKNOWN (erişim biçimi) |
| 2 | Güvenlik | TLS/anahtar kullanımı | K009-security | ⚠️ UNKNOWN (politika biçimi) |
| 3 | Kimlik | cihaz kimliği sorgusu | K010-identitiy | ⚠️ UNKNOWN (token/oturum) |
| 4 | Uygulama | ağ isteği alan taraf | K011-application | ⚠️ UNKNOWN (API biçimi) |
| 5 | Servisler | servisler arası çağrı | K012-services | ⚠️ UNKNOWN (protokol seti) |
| 6 | Dış dünya | dış API taşıması | K013-integraion | ⚠️ UNKNOWN (protokol seti) |
| 7 | Medya | akış/indirme | K015-media | ⚠️ UNKNOWN (akış biçimi) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K014 yalnız taşıma yapar; paketin **anlamını** okumaz (iş K012/K013'te).
2. Güvenlik katmanına dokunmaz: şifreleme/anahtar K009'dur (iddia).
3. Çevrimdışıyken davranışı tanımlar; içeriğini sahiplerine bırakır (§4/2).
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | offline-sync üçlüsü (K014 · K015/offline · K012) | aynı işi yapabilecek üç aday | çift kuyruk/çakışma | §7/2 — sınır ADR'si |
| 2 | handoff ↔ multi-room | ikisi de "cihazlar arası çalma" | tekrar | §7/1 |
| 3 | service-discovery kapsamı | cihaz keşfi mi servis kaydı mı belirsiz | yanlış sahiplik | §7/3 |
| 4 | Donanım bağımlılığı (K001) | donanım yoksa K014 işlevsiz | hizmet kesintisi | fallback stratejisi (§7/7) |
| 5 | Güvenlik katmanı sıçraması | TLS dışı şifreleme burada üretilirse | güvenlik olayı | K009 sahipliği zorunlu |
| 6 | WebRTC iddiasının kanıtsızlığı | URL yok, "standard" beyanı | yanlış zemin | §7/4 — ⚠️ VERIFY REQUIRED |
| 7 | network-testing ↔ K019-testing | iki test sahibi | test tekrarı | §7/6 |

**Döngü denetimi:** §1'de K014'e geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2 adları imla düzeltmesi yapılmadan yazılır (identitiy dahil).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
