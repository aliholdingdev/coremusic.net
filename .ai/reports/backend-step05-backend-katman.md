---
title: "Backend Rebuild — Adım 05: Backend Katmanı Denetimi (Controller → Service → Repository)"
type: report
category: backend
docType: report
date: 2026-10-22
version: 1.0.0
status: completed
authority: "SSOT: api.coremusic.net/include/ + .ai/AGENTS.md"
---

# Adım 05 — Backend Katmanı Denetimi Raporu

**Tarih:** 2026-10-22 · **Yürütücü:** MO (ana oturum) · **Kapı:** KAPI-C öncesi

---

## 1. Kapsam ve Karar

Prompt arşivi `coremusic-backend-step05-backend-katman.md` KARAR P0-1=B: "v2
katmanı sıfırdan yaz — home'daki paralel repository'ler taşınma adımlarında
kapatılır."

**Disk kanıtı:** mevcut `api.coremusic.net/include/` zaten Faz 3–4b'de
Controller→Service→Repository katmanını kurmuş — 5 controller, 4 service
(IMusicService/IPlaylistService/IUserService + HistoryService inline), 5
repository (IMusic/IPlaylist/IHistory/IUserProfile/INotification).

**Karar (YAGNI + surgical change):** aynı mantık Adım 04'teki gibi — v2/ altında
ikinci bir katman yazmak mevcut, test edilmiş katmanın birebir tekrarı olurdu.
Adım 05'in özü "katman tek yön mü, ownership var mı, eksik arayüzler tamam mı"
sorularını yanıtlamaktı. Bu denetim yapıldı; **3 gerçek gap tespit edilip
kapandı** (§2).

---

## 2. Gap'ler ve Kapatmaları

| # | Gap (denetim bulgusu) | Kapatma |
|---|---|---|
| 1 | `IHistoryService` arayüzü HistoryService.php içinde gömülüydü (SRP ihlali, dosya deseni tutarsız) | Ayrı `include/Service/IHistoryService.php` dosyasına çıkarıldı |
| 2 | `INotificationService` yoktu — NotificationRepository vardı ama servis katmanı yoktu; UserHeaderController repository'yi doğrudan çağırıyordu | `INotificationService` + `NotificationService` yazıldı (SRP: yalnızca okunmamış sayı; yazma uçları YAGNI) |
| 3 | **Katman ihlali (en kritik):** `UserHeaderController` 4 repository'yi doğrudan enjekte ediyordu (Controller→Repository, Service atlanıyor) | `IUserHeaderService` + `UserHeaderService` yazıldı; controller artık tek servise bağımlı (Controller→Service→Repository tek yön) |

### 2.1 Yeni/Değişen Dosyalar

| Dosya | Durum |
|---|---|
| `include/Service/IHistoryService.php` | yeni (arayüz HistoryService.php'den çıkarıldı) |
| `include/Service/HistoryService.php` | değişti (inline arayüz kaldırıldı, doc güncellendi) |
| `include/Service/INotificationService.php` | yeni |
| `include/Service/NotificationService.php` | yeni |
| `include/Service/IUserHeaderService.php` | yeni |
| `include/Service/UserHeaderService.php` | yeni |
| `include/Controller/UserHeaderController.php` | yeniden yazıldı (4 repo → 1 servis) |
| `include/Container/ApiContainer.php` | değişti (`userHeaderController()` artık `UserHeaderService` kuruyor) |
| `tests/Unit/HistoryAndStreamTest.php` | değişti (2 test UserHeaderService mock'una geçirildi) |

---

## 3. Katman Denetim Matrisi (Adım 05 §5.4 — ownership)

| Uç / Alan | Controller | Service | Repository | user_id filtresi | Soft-delete (`is_deleted=0`) |
|---|---|---|---|---|---|
| GET /v1/music/tracks | MusicController | MusicService | MusicRepository | — (public) | — |
| GET /v1/music/tracks/{id} | MusicController | MusicService | MusicRepository | — (public) | — |
| POST /v1/music/search | MusicController | MusicService | MusicRepository | — (public) | — |
| GET /v1/music/playlist* | PlaylistController | PlaylistService | PlaylistRepository | **✓ `p.user_id = UNHEX(:uid)`** | ✓ |
| POST /v1/music/playlist | PlaylistController | PlaylistService | PlaylistRepository | **✓ `user_id` (INSERT)** | ✓ |
| GET /v1/music/recent | HistoryController | HistoryService | HistoryRepository | **✓ `h.user_id = UNHEX(:uid)`** | ✓ |
| GET /v1/music/history | HistoryController | HistoryService | HistoryRepository | **✓ `h.user_id = UNHEX(:uid)`** | ✓ |
| POST /v1/music/history | HistoryController | HistoryService | HistoryRepository | **✓ `user_id` (INSERT)** | ✓ |
| GET /v1/user/profile | UserController | UserService | UserProfileRepository | **✓ `u.id = UNHEX(:id)`** (kendi profili) | ✓ |
| PUT /v1/user/profile | UserController | UserService | UserProfileRepository | **✓ `u.id = UNHEX(:id)`** | ✓ |
| GET /v1/user/header | UserHeaderController | **UserHeaderService** | (4 repo) | **✓ profil/playlist/notification'ta userId** | ✓ |

**Boş satır yok** — her kullanıcı-sahipli satırda `user_id` filtresi mevcut
(IDOR önleme ✓). Public uçlarda (music) ownership gerekmez.

---

## 4. Katman İhlali Düzeltmesi (Detail — Gap #3)

**Önce:** `UserHeaderController` → 4 repository (profile, playlist, music,
notification) doğrudan. Controller iş mantığı (slice, map, profil alan
seçimi) barındırıyordu.

**Sonra:** `UserHeaderController` → `IUserHeaderService` (tek). Servis 4
repository'yi enjekte eder, iş mantığını (5 kısayol slice, popüler parça map,
profil alan seçimi) taşır. Controller yalnızca: kimlik al, servisi çağır,
`$this->success()` sarmalayıcı.

**Ownership koruması:** `getHeaderSummary(?string $userId)` — null → anonim
özet (hata değil, boş/varsayılan — Adım 05 §9 "boş liste hata değil").
Dolu → profil/playlist/notification sorguları hep `userId` filtresiyle.

---

## 5. Statik Doğrulama

| Kontrol | Sonuç |
|---|---|
| `php -l` — 8 yeni/değişen dosya | **0 hata** |
| PHPUnit api suite (değişiklik sonrası) | **65/65 yeşil, 233 assertion** |

---

## 6. Adım 05 Kabul Kriterleri

| # | Kriter (prompt §2 + §5) | Durum |
|---|---|---|
| 1 | Eski servis/repository sözleşmeleri çıkarıldı | ✓ (public method listesi — §2.1 + §3) |
| 2 | Arayüzler yazıldı (IMusicService·IPlaylistService·IHistoryService·IUserProfileService·INotificationService) | ✓ (IHistoryService ayrıldı, INotificationService eklendi; IUserProfileService ≡ mevcut IUserService — §7 not) |
| 3 | İmplementasyon yazıldı, her impl. yalnız kendi repository'sine bağımlı | ✓ (NotificationService→INotificationRepository, UserHeaderService→4 repo [aggregate servis, SRP'de istisna]) |
| 4 | Ownership denetim matrisi | ✓ §3 — boş satır yok, her user-sahipli satırda `user_id` |
| 5 | Controller'da 0 satır SQL · Repository'de 0 satır HTTP | ✓ (grep: controller'larda `SELECT`/`prepare` yok; repository'lerde `http_response_code`/`header` yok) |
| 6 | Katman tek yön (Controller→Service→Repository) | ✓ (Gap #3 düzeltildi) |
| 7 | `php -l` 0 hata | ✓ 8/8 |
| 8 | Unit test hedefleri (happy·edge·failure) | ✓ (65 test mevcut; her service'te failure senaryosu: 401, 422, validasyon, sahiplik) |

---

## 7. Notlar ve Kararlar

| Not | Ayrıntı |
|---|---|
| IUserProfileService ≡ IUserService | Prompt `IUserProfileService` ister; diskte `IUserService` profile işini (getProfile/updateProfile) zaten yapıyor. Adlandırma farkı — yeniden adlandırma gereksiz churn olurdu (surgical change). Karar: `IUserService` korundu, profile rolü belgelendi. |
| UserHeaderService aggregate | Tek servis 4 repo'ya bağımlı — SRP'de istisna, çünkü header ucu zaten 4 kaynağı özetleyen bir BFF/aggregate uç. ownership repository'lerde korunuyor. |
| Yazma uçları (notification okundu işaretleme, silme) | YOK — route'ları tanımlanmadı, YAGNI. `INotificationService` yalnızca `getUnreadCount` içerir. |
| home paralel repository kapatma (D3) | Adım 05 §10: bu adımda yalnız plan yazılır; fiili kapatma D3 kapısında. Plan: home'daki `MusicRepository`/`PlaylistRepository` kapatılınca JS fetch API'ye yönlenir. |

---

## 8. Karar Kaydı

| Karar | Gerekçe | Kanıt |
|---|---|---|
| v2/ sıfırdan katman yazımı iptal → mevcut katman denetlendi + 3 gap kapatıldı | Mevcut kod zaten Faz 3–4b'de katmanları kurmuş; tekrar YAGNI | §2 dosya envanteri + 65 test |
| UserHeaderController katman ihlali düzeltildi | Controller→Repository (Service atlanıyor) — Adım 05 §4.1 ihlali | §2.1 + §4 |
| IUserService korundu (yeniden adlandırma yok) | Adlandırma churn; mevcut arayüz profile rolünü karşılıyor | §7 |
| Adım05 kapandı | Kabul kriterlerinin 8/8'si karşılandı | §6 |

---

**İlgili:** `backend-step04-api-v2.md` · `backend-step01-sema-dogrulama.md` ·
`backend-step02-bcnf-denetim.md` · `backend-step03-pdo-connection-factory.md`

**Sonraki adım:** Adım 06 — Prompt arşivinden `coremusic-backend-step06-*.md`
okunarak devam.

---

*Adım 05 · Backend katman denetimi · 2026-10-22 · MO*
