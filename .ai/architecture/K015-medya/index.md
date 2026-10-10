---
type: architecture
category: layer
title: "K015 — Medya"
date: 2026-10-09
status: active
version: 2.0.1
authority: SSOT
---

# K015 — Medya

## §1 Kimlik
- Katman: K015 · Alan: **A4** (K12-K15).
- Kapsam: medya arşivi, ingest hattı ve teslim (`media.coremusic.net`).

## §2 Sorumluluk
1. Dizin ekseni + ULID kimliği (ADR-092): hedef şablonu `media/_inbox/{YYYY-AA-GG}/{slug}-{ULID}.{uzanti}`.
2. Ingest hattı: tarama → aday üretimi → `Ulid::uret()` → hedefe yazma → CSV audit.
3. Download servisi (ADR-026), dual-mode storage (ADR-027), anti-ban (ADR-028).
4. Medya veri şeması (`.ai/.sql/mysql/media_catalog.sql`).
5. Uzak teslimde timeout + sınırlı retry + fallback (plan §5.6).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K005 (medya DB) → K000.
- **Üst (çağıran):** K011 UX (oynatma/arşiv arayüzü) · K009 API (medya uçları) · K003 (oynatma kaynağı).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-092 | Medya Arşivi Dizin Ekseni ve ULID Kimliği |
| ADR-026 / ADR-027 | Download Service · Dual-Mode Storage |
| ADR-028 | Anti-Ban System |

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `media.coremusic.net/bin/ingest.php` (`use Media\Ulid;` L38, `Ulid::uret()` L211, hedef şablonu L254-256) · `media.coremusic.net/composer.json` · `.ai/.sql/mysql/media_catalog.sql`.

## §6 Risk / Not
- `Media\Ulid` sınıfının kaynağı/şartnamesi okunmadı → iç implementasyon **UNKNOWN**.
- Anti-ban/dual-mode (ADR-027/028) uygulama kanıtı bu görevde aranmadı → **UNKNOWN**.
## §7 Makro Katman Karşılığı
K015-medya → Makro **K3** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K3 içinde K015 = **medya iş mantığı**: arşiv dizin ekseni, ingest hattı ve teslim (`media.coremusic.net`) — iş kuralı/domain sınıfıdır (makro özet §2 K3-5).
- Makro komşular: alt = **K2** K005 veri (kendi §3 kaydı: "K005 (medya DB) → K000"; SSOT matris §2'de K015 satırı YOK — makro özet §6.2-10 ile uyumlu) · üst = K3 içi K008 (matris §2: "K008 servisler → K015 medya · port/adaptor" — izinli çağıran) · ayrıca K4'ten K009/K011 çağırır (§3).
- Özel durum: `K015→K003` (ses motoru) kenarı **yön çelişkilidir** ve matris §2'de kayıtlı DEĞİLDİR — istisna kaydı, ADR bekliyor (makro özet §6.2-10; [[../katmanli-mimari-k0-k4/k1-surucular-cihaz-yonetimi]] §3.1).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (üstten):** K008 servis → medya port'u çağrısı (matris §2, port/adaptor — ADR-086 wiring PLANNED); ingest hattı girdisi: tarama → aday üretimi (§2.2).
- **Çıktı (aşağıya/yana):** K005 medya DB yazımı (`.ai/.sql/mysql/media_catalog.sql` şeması) + dosya sistemi yazımı hedef şablonu + CSV audit (§2.2); üstte K009/K011'e medya uçları/oynatma verisi (§3).
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Kanıt satırı | Rol |
|---|---|---|---|
| Ingest hattı | `media.coremusic.net/bin/ingest.php` | `use Media\Ulid;` L38 · `Ulid::uret()` L211 · hedef şablonu L254-256 | Tarama → ULID → hedefe yazma → CSV audit |
| Composer zemini | `media.coremusic.net/composer.json` | §5 | `Media\` namespace autoload zemini |
| Medya şeması | `.ai/.sql/mysql/media_catalog.sql` | §4/§5 | Medya veri sözleşmesi |
| Hedef şablon kuralı | ADR-092 | `media/_inbox/{YYYY-AA-GG}/{slug}-{ULID}.{uzanti}` (§2.1) | Dizin ekseni + ULID kimliği |

- **İzinli/yasaklı çağrı özeti:** K015 → K005 veri erişimi (şema `media_catalog.sql`); K008 → K015 yalnız port/adaptor (ADR-086 wiring PLANNED — iç içe import YASAK, makro özet §2.4); `K015→K003` yönü norma çevrilemez, ADR bekliyor; DTO'nun PDO/HTTP bilmesi YASAK (K3 kuralı, makro özet §3).
- `Media\Ulid` sınıfının kaynağı/imzası okunmadı → iç implementasyon **UNKNOWN** (§6; makro özet §7-6 bu dosyada).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **`Media\Ulid` iç implementasyonu UNKNOWN** — ULID üretiminin doğruluğu/tekrarsızlığı kodda doğrulanmadı (§6; makro özet §5-FM5).
  2. **K015↔K003 yön çelişkisi** — medya ↔ ses motoru arasında dairesel çağrış riski; ADR bekliyor (makro özet §7-8).
  3. **Anti-ban/dual-mode (ADR-027/028) uygulama kanıtı aranmadı** (§6) → teslim/stor­age davranışı UNKNOWN.
  4. **Uzak teslim fallback'i denetimsiz** — timeout + sınırlı retry + fallback kuralı (§2.5, plan §5.6) kodda doğrulanmadı.
- **İzolasyon:** ingest hataları ingest.php içinde izol edilir (k3 makro §5; L211-L256 hattı); hedef şablonu disiplini (ADR-092) bozulursa arşiv dizin ekseni kirlenir — yazma münhasır/modül sahipliği kuralı (K008 §2.5) veri tutarlılığını korur.
- **Güvenlik sınırı (kim neyi doğrular):** auth/CSRF/RateLimit K015'te uygulanmaz — istek K2 kanonik sırasından geçer: OriginCheck+CSRF → RateLimit (APCu) → CSP nonce → Auth → PageRouter (makro özet [[../katmanli-mimari-k0-k4/k2-cekirdek-servisler-middleware]] §3.2). K015'in kendi sınırı: **dosya sistemi yazım disiplini** (hedef şablon, uzantı/slug doğrulama — ADR-092) + CSV audit kaydı (§2.2); dual-write yasağı K005/ADR-081 ile bağlantılıdır (k2 makro §6).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| `Media\Ulid` iç implementasyonu (L211 dışı davranış) | UNKNOWN (§6; makro özet §7-6/K015) |
| K015↔K003 yön çelişkisi | ⚠️ ADR bekliyor — matris §2'de kenar YOK (makro özet §6.2-10) |
| K015'in SSOT matris §2'de kendi bağımlılık satırı | YOK — yalnız K008→K015 satırında çağırıcı olarak geçer (matris §2; kendi §3'ü K005 derer) |
| Anti-ban (ADR-028) / dual-mode storage (ADR-027) uygulama kanıtı | UNKNOWN — aranmadı (§6) |
| Uzak teslim timeout+retry+fallback kod kanıtı | ⚠️ VERIFICATION REQUIRED (plan §5.6, §2.5) |
