---
node_id: K012-services
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - music-service
  - media-service
  - playback-service
  - audio-service
  - user-service
  - device-service
  - ai-service
  - integration-service
  - background-jobs
  - service-contracts
depends_on: [K006-yapay-zeka, K007-data, K008-domain, K010-identitiy, K013-integraion, K014-network, K015-media]
evidence:
  - "v2 §2.12 K012-services/ — çocuk listesi kaynağı (10 çocuk, birebir)"
  - ".ai/architecture/index.md §5/12 — services-master.md adı kaydı"
  - "Protobuf canonical model (portify benzeri) — 2026-10-12 beyanı (URL yok ⚠️)"
validation:
  - "Çocuk adları v2 §2.12 ile birebir karşılaştırıldı (10/10) — 2026-10-12"
  - "Web kanıtları services-dogrulama.md §3'e işlendi — 2026-10-12"
open_questions:
  - "ai-service K006-yapay-zeka'ya mı K016-AI'ye mi bağlanır? → ⚠️ VERIFICATION REQUIRED"
  - "playback-service (K012) ile K015/playback sahipliği → ⚠️ VERIFICATION REQUIRED"
  - "service-contracts evrensel Playlist/Track spec'i mi? → TASARIM İDDİASI"
  - "background-jobs ile K018-devops/dağıtım sınırı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K012-services — Arka Plan Servisleri

## 1. ELI15 — bu oda ne işe yarar?

v2 §2.12: *"Arka planda iş yapan küçük uygulamalar — 'çalan parça servisi',
'kullanıcı servisi'."*
Arayüz değil, **iş yapan** taraftır: çalmayı yönetir, kullanıcıyı tutar, cihazı
kaydeder, zamanlanmış işleri koşar. Uygulama (K011) bunları çağırır; veri K007'de,
iş kuralı K008'de, dış dünya K013'te durur.

## 2. Çocuk kataloğu (kademe-2 — v2 §2.12 ile birebir)

| # | Çocuk | ELI15 rol (tasarım iddiası) | Durum |
|---|---|---|---|
| 1 | music-service | Müzik katalog/arama işlerini yürüten servis | draft |
| 2 | media-service | Medya varlığı işlerini yürüten servis | draft |
| 3 | playback-service | "Ne çalıyor / ne çalacak" durumunu yöneten servis | draft |
| 4 | audio-service | Ses katmanına ait servis işleri (ayrıntı ⚠️) | draft |
| 5 | user-service | Kullanıcı profili ve tercihleri servisi | draft |
| 6 | device-service | Cihaz kaydı, durumu ve yetkisi servisi | draft |
| 7 | ai-service | Yapay zeka işlerini sunan servis (bağlı olduğu düğüm ⚠️) | draft |
| 8 | integration-service | Dış sistem çağrılarını barındıran servis (K013 kontratları) | draft |
| 9 | background-jobs | Zamanlanmış ve arka plan işleri | draft |
| 10 | service-contracts | Servisler arası sözleşmeler (mesaj/DTO biçimi ⚠️) | draft |

## 3. Bağımlılık özeti (tam metin: services-bagimliliklar.md)

| Yön | Düğüm | Ne için (TASARIM İDDİASI) |
|---|---|---|
| depends_on → | K006-yapay-zeka / K016-AI | ai-service'in arkası (hangisi ⚠️ §4) |
| depends_on → | K007-data | Kalıcı veri erişimi |
| depends_on → | K008-domain | İş kuralı ve domain modeli |
| depends_on → | K010-identitiy | Kullanıcı/cihaz kimliği doğrulama |
| depends_on → | K013-integraion | Dış sistem kontratları (integration-service) |
| depends_on → | K014-network | Servisler arası taşıma |
| depends_on → | K015-media | Kitaplık/metadata/akış verisi |
| ← used_by | K011-application | Uygulamanın servis çağrıları |
| ← used_by | K019-testing | Servis testleri (sahiplik ⚠️) |

## 4. Durum & açık sorular

- **Durum:** draft — kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor. Ayrıntı: services-bagimliliklar.md (§1–§5) · services-dogrulama.md (§1–§27) · services-riskler.md (§3) · services-ornekler.md (§3).
- Kaynak: v2 §2.12 · .ai/architecture/index.md §5/12 · ADR-097 §5
- Kısa liste:
  1. ai-service → K006 mı, K016 mı? → ⚠️ VERIFICATION REQUIRED (registry §6)
  2. playback-service ↔ K015/playback sahipliği → ⚠️ VERIFICATION REQUIRED
  3. service-contracts evrensel Playlist/Track spec'i mi? → TASARIM İDDİASI
  4. audio-service kapsamı (K003-audio ile sınır) → ⚠️ VERIFICATION REQUIRED

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `services-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `services-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `services-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `services-riskler.md` | Risk kaydı (R1–R8) ve azaltım planı | Kademe-1 |
| kod | `services-ornekler.md` | Kanıt kartları (S1) ve pseudocode örnekler | Kademe-1 |
| kanit | `services-web-arastirma.md` | Q1–Q9 web kanıtlı seçenek matrisleri (11 URL + 4 kardeş pointer; karar ADR bekler) | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
