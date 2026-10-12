---
title: "prompt-maker Example — Ham Türkçe İstek → P0/P1/P2 → 8 Bölümü Yapılandırılmış Prompt"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Ham İstek → P0/P1/P2 Sorular → 8 Bölümü Yapılandırılmış Prompt

Bu örnek **tam çalışmış** bir akıştır: realist bir CoreMusic isteği (PHP 8.4, music paneli,
sosyal veritabanı) ham hâlden başlar, engelleyici sorularla netleştirilir ve § Zorunlu Akış B
şablonuna göre 8 bölümlü yapılandırılmış prompt'a dönüşür.

---

## 1. Girdi — kullanıcının ham isteği (düzeltilmeden)

```text
"kullanıcı: insanlar birbirinin çalma listesini görebilsin, beğensin kopyalayabilsin,
music panelinde falan görünür olsun işte"
```

**Kural (§ Zorunlu Akış A):** HEMEN üretme — önce **questions modu**; sorular **proje
bağlamına göre** (generic değil).

---

## 2. P0 / P1 / P2 Sorular (CoreMusic bağlamına göre)

| Seviye | Soru | Neden |
|--------|------|-------|
| **P0** (engelleyici) | Veri nerede? `coremusic_social` DB'si (yorum/ paylaşım var) mi, yoksa `coremusic_playlist` içine mi? | Şemayı belirler |
| **P0** (engelleyici) | Erişim modeli ne? Herkese açık liste mi, sadece takip edenler mi (RBAC: regular/premium/studio)? | Güvenlik + DB kuralı |
| **P0** (engelleyici) | Bu istek music panelinde (`music.coremusic.net`, port 81) mi — SPA üzerinden mi? SPA asla PDO/SQL göremez → API sözleşmesi (OpenAPI) önce | Mimari katman (K9/K10) |
| **P1** (kalite) | "Beğeni" tek yönlü like mı, takip/abo mu? Spam koruması (rate limit 60 req/60s) şart mı? | Kalite + güvenlik |
| **P1** (kalite) | Kopyalama: referans (FK) mi, anlık snapshot (kopya satır) mı? Orijinal silinince kopya ne olur? | BCNF + iş kuralı |
| **P1** (kalite) | Sıralama/trend: liste ana sayfasında "popüler listeler" vitrini var mı? (cache/APCu?) | Performans |
| **P2** (tercih) | Kopyalarken otomatik isim eki ("— kopya") mi, kullanıcıya sorulsun mu? | UX tercihi |
| **P2** (tercih) | Bildirim: listem beğenildiğinde bildirim (`coremusic_social.notifications`) gelsin mi? | Kapsam tercihi |

**Cevaplar (kullanıcı onayı):** P0-1 → `coremusic_social` altında yeni tablo; P0-2 → herkese
açık + gizli seçenek; P0-3 → OpenAPI sözleşmesi önce, SPA ApiClient üzerinden; P1-1 → like + rate
limit; P1-2 → snapshot kopya; P1-3 → evet, APCu ile; P2-1 → "— kopya" eki; P2-2 → bildirim yok (v1).

---

## 3. Çıktı — 8 Bölümü Yapılandırılmış Prompt (§ Zorunlu Akış B, sıra zorunlu)

### Bölüm 1 — /eli10 uzun paragraf

Sistem, çalma listelerini sosyal bir nesneye dönüştürmek istiyor: kullanıcı bir listeyi
görebilecek, beğenebilecek ve kendi kütüphanesine kopyalayabilecek. Bu üç eylem üç katmanı
harekete geçirir: `music.coremusic.net` SPA'sı yalnız ApiClient üzerinden konuşur (PDO/SQL
görmez), gateway'de OpenAPI sözleşmesi doğrulama yapar, yazma işleri Command → Use Case →
Repository zinciriyle MySQL'e iner. Beğeni ve kopyalama `coremusic_social` altındaki yeni
tablolarda yaşar; kopyalama anlık snapshot alır (orijinal liste sonradan değişse de kullanıcının
kopyası sabit kalır). Herkese açık/gizli erişim RBAC ile, istekler APCu rate limit (60 req/60s)
ile korunur. Trend listeler APCu cache ile okunur, yazmada cache invalidation yapılır.

### Bölüm 2 — İstenen şey nedir

Çalma listesi paylaşımı: görünürlük (public/private), beğeni (like), kopyalama (snapshot copy)
— music panelinde görünür, API-first, rate-limit korumalı.

### Bölüm 3 — Vault referansları (.ai/)

- `@.ai/CLAUDE.md` — §6A API-First, §6 middleware zinciri, §18 BCNF kuralları
- `@.ai/AGENTS.md` — agent sınırları / routing
- `@.ai/WORKFLOW.md` — faz kapıları (onay → implementasyon)
- `.ai/.decisions/accepted/` — ADR-002 (PDO, ORM yasak), ADR-010 (csrf_token), ADR-013 (rate limit)

### Bölüm 4 — Proje kod referansları

- `shared/` — Service/Repository katmanı (`coremusic/shared` composer paketi)
- Music paneli SPA router + ApiClient (`AbortController` zorunlu)
- Middleware sırası: OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → …

### Bölüm 5 — Kullanıcı kararları + sorular + cevaplar

| Soru (P seviyesi) | Cevap |
|-------------------|-------|
| P0 Veri nerede? | `coremusic_social` yeni tablo |
| P0 Erişim modeli? | public + private seçeneği, RBAC |
| P0 Nerde? | music paneli, OpenAPI önce, SPA → ApiClient |
| P1 Beğeni? | like + rate limit |
| P1 Kopyalama? | snapshot kopya |
| P1 Trend vitrin? | evet, APCu |
| P2 İsim eki? | "— kopya" |
| P2 Bildirim? | v1'de yok |

### Bölüm 6 — Orijinal prompt (değiştirilmeden)

```text
"kullanıcı: insanlar birbirinin çalma listesini görebilsin, beğensin kopyalayabilsin,
music panelinde falan görünür olsun işte"
```

### Bölüm 7 — Görevlere bölme (task breakdown)

1. **T1 — Şema:** `coremusic_social` BCNF tabloları: `playlist_share`, `playlist_like`,
   `playlist_copy` (migration → `coremusic_patch`)
2. **T2 — Sözleşme:** OpenAPI spec → DTO → Validation (API-First, kod ÖNCE değil)
3. **T3 — Write yolu:** Command → Use Case → Repository (raw PDO, prepared statement, SELECT * yasak)
4. **T4 — Read yolu:** Query → Read Model → APCu cache (+ invalidation)
5. **T5 — SPA:** ApiClient çağrıları + liste kartı bileşeni (mockup okumadan kod YASAK — Guardrail #11)
6. **T6 — Güvenlik:** CSRF (`csrf_token`), RBAC erişim kontrolü, rate limit doğrulaması
7. **T7 — Test:** ≥%80 coverage (PHPUnit 11), log.md girişi

### Bölüm 8 — Kapanış

```text
✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 7 | Cevap: 8
```

**Kapı (§ Zorunlu Akış C):** Bu çıktı **onaylanır → SONRA** session start. Onaysız session
start YASAK.