---
node_id: K011-application
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - web
  - desktop
  - electron
  - local-runtime
  - server-runtime
  - application-services
  - commands
  - queries
  - workflows
  - application-testing
depends_on:
  - K009-security
  - K010-identitiy
  - K012-services
  - K013-integraion
  - K014-network
  - K015-media
evidence:
  - "v2 §2.11 K011-application/ — çocuk listesi kaynağı (10 çocuk, birebir)"
  - ".ai/architecture/index.md §5/11 — application-master.md adı kaydı"
  - "https://github.com/jellyfin/jellyfin — Jellyfin · .NET · 55732★ · GPL-2.0 (2026-10-12)"
validation:
  - "Çocuk adları v2 §2.11 ile birebir karşılaştırıldı (10/10) — 2026-10-12"
  - "Web kanıtları application-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "desktop ile electron sınırı (aynı kabuk mu, ayrı paket mi?) → ⚠️ VERIFICATION REQUIRED"
  - "server-runtime'ın barındırma yeri (nerede çalıştığı) → ⚠️ VERIFICATION REQUIRED"
  - "commands/queries ayrımı CQRS olarak mı okunacak? → TASARIM İDDİASI"
  - "application-testing ile K019-testing kapsam sınırı → ⚠️ VERIFICATION REQUIRED"
---

# K011-application — Uygulama Katmanı

## 1. ELI15 — bu oda ne işe yarar?

v2 §2.11: *"Uygulamanın kendisi — web sayfası, masaüstü penceresi, Electron çatısı."*
Kullanıcının gördüğü ve tıkladığı **her şey** bu odadır: arayüzü çizmek ve "çal, ara,
ekle" talebini alıp doğru servise iletmek. İş kuralı burada **yaşamaz** → K008-domain ·
arka plan → K012-services · dış dünya → K013-integraion · bağlantı → K014-network ·
medya → K015-media.

## 2. Çocuk kataloğu (kademe-2 — v2 §2.11 ile birebir)

> ELI15 sütunu **TASARIM İDDİASI**'dır (isimden türetildi, kanıt değildir).

| # | Çocuk | ELI15 rol (tasarım iddiası) | Durum |
|---|---|---|---|
| 1 | web | Tarayıcıda çalışan arayüz (sayfa, bileşen, stil) | draft |
| 2 | desktop | Yerel masaüstü penceresi ve kabuğu | draft |
| 3 | electron | Electron çatısıyla paketlenen masaüstü kabuğu | draft |
| 4 | local-runtime | Kullanıcı makinesinde çalışan çalışma zamanı | draft |
| 5 | server-runtime | Sunucu tarafında çalışan çalışma zamanı | draft |
| 6 | application-services | Uygulama düzeyinde orkestrasyon (iş akışı bağlayıcı) | draft |
| 7 | commands | "Yap" talepleri — yazma eylemleri | draft |
| 8 | queries | "Göster" talepleri — okuma eylemleri | draft |
| 9 | workflows | Çok adımlı iş akışları (adım sırası + durum) | draft |
| 10 | application-testing | Bu katmanın testleri (K019 ile sınır belirsiz) | draft |

## 3. Bağımlılık özeti (tam metin: application-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K009-security | Güvenlik politikası ve imzalama kuralları |
| depends_on → | K010-identitiy | Oturum, rol, cihaz kimliği doğrulaması |
| depends_on → | K012-services | Arka plan/servis çağrıları (çalan parça, kullanıcı) |
| depends_on → | K013-integraion | Dış API kontratları ve adaptörler |
| depends_on → | K014-network | Taşıma katmanı (HTTP/WebSocket/çevrimdışı) |
| depends_on → | K015-media | Oynatma ve medya verisi görünümü |
| ← used_by | K017-observability | Uygulama telemetrisi ve metrikleri |
| ← used_by | K019-testing | Uçtan uca / birim test hedefi |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor.
- Ayrıntı: application-bagimliliklar.md (§1–§5) · application-dogrulama.md (§1–§6).
- Kısa liste:
  1. desktop ↔ electron sınırı → ⚠️ VERIFICATION REQUIRED
  2. server-runtime barındırma yeri → ⚠️ VERIFICATION REQUIRED
  3. commands/queries = CQRS mi? → TASARIM İDDİASI
  4. application-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED

**Kaynak:** v2 §2.11 · .ai/architecture/index.md §5/11 · ADR-097 §5
