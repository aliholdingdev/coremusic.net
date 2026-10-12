---
node_id: K013-integraion
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.13 + ADR-097 §5.2"
---

# K013-integraion — integraion-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.13 çocuk adları ve (b) 2026-10-12 beyanları geçerlidir.
> **İsim notu:** yeni dosya ön eki düğüm adından gelir (**integraion**);
> master dosyası v2'deki adıyla **integration-master.md**'dir (imla dokunulmaz).

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED / VERIFY REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 beyanı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K007-data | Senkron sonucunu saklamak | veri erişimi | Şema K007'de kalmalı; sync yazımı yarış riski ⚠️ |
| 2 | K008-domain | Kontrat tiplerini almak | tip tüketimi | Dış şema ile domain tipi eşleşmeli ⚠️ |
| 3 | K009-security | Anahtar/secret/imza güvenliği | güvenlik | Politika K009'da; K013 secret saklamaz |
| 4 | K010-identitiy | OAuth/PKCE oturumu ve kimlik | kimlik | Yazım "identitiy" — dokunulmaz (v2 §3) |
| 5 | K014-network | HTTP vb. taşıma | taşıma | Rate limit/TLS ayrıntısı K014/K009'da |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Kullanıcının başlattığı dış çağrı akışı | TASARIM İDDİASI |
| 2 | K012-services | integration-service'in kontrat/adapter tüketimi | TASARIM İDDİASI |
| 3 | K015-media | Dış katalog/meta senkronu | TASARIM İDDİASI (sınır ⚠️) |
| 4 | Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

## §3 Entegrasyon sınırları (K013 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Dış dünya | adaptör → üçüncü taraf API | K013 | ⚠️ UNKNOWN (biçim, sürüm stratejisi) |
| 2 | Taşıma | HTTP vb. istek | K014-network | ⚠️ UNKNOWN (istemci kütüphanesi) |
| 3 | Kimlik | OAuth/PKCE token akışı | K010-identitiy | ⚠️ UNKNOWN (akış sahipliği §7/5) |
| 4 | Güvenlik | anahtar okuma/imza | K009-security | ⚠️ UNKNOWN (politika biçimi) |
| 5 | Kalıcılık | senkron sonucu yazma | K007-data | ⚠️ UNKNOWN (tablolar) |
| 6 | İç servis | kontrat tüketimi (K012) | K012-services | ⚠️ UNKNOWN (yön §7/3) |
| 7 | Medya | katalog senkronu (K015) | K015-media | ⚠️ UNKNOWN (sahiplik §7/2) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K013 dış dünyaya **tek** kapıdır; iç servisler dışarıya doğrudan çıkmaz (iddia).
2. Diğer domain'ler dışarıya yalnız K013 kontratı üzerinden çıkar (iddia).
3. Hata ve tekrar-dene politikası bu kapıda toplanır (error-handling) — iddia.
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | YouTube Music resmi public API yok iddiası | TOS riski, kanıtsız | hukuki/entegrasyon riski | §7/1 — ⚠️ VERIFY REQUIRED |
| 2 | synchronization iki domain'de (K013 + K015) | aynı ad, iki sahip | çakışan yazım | §7/2 — sınır ADR'si |
| 3 | Çağıran (K012) ↔ kontrat (K013) yönü | yön belirsiz | döngü riski | §7/3 — yön kilidi |
| 4 | External API sürüm değişimi | kontrat kırılır | kırılma yayılır | sürümleme stratejisi (§7/6) |
| 5 | Secret/anahtar sızıntısı | K009 dışında tutulursa | güvenlik olayı | K009 sahipliği zorunlu |
| 6 | PKCE/JWT iddiaları genel karar sanılır | beyan ≠ karar | yanlış tasarım | ADR (§7/4–5) |
| 7 | master "integration" vs yeni "integraion" | iki yazım aynı klasörde | yanlış arama | bilinçli (ADR-097 §5.4) |

**Döngü denetimi:** §1'de K013'e geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2 adları imla düzeltmesi yapılmadan yazılır (integraion · identitiy dahil).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
