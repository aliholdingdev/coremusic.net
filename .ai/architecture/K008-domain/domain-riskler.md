---
node_id: "K008-domain/riskler"
node_type: "root"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master.md"
evidence:
  - "2026-10-15 — shared/src grep: favorite|user_favorites = 0 · context_mapping = 0 · ->dispatch( = 1 (router, event değil)"
  - "2026-10-15 — k008-httpcheck: 75 kök URL, 73 HTTP 200, 2 kırık (§3-R5)"
  - "domain-master.md §6 (C1–C4) + §21 (R-1…R-5)"
validation: "2026-10-15 — 12 risk satırı; her satır Dosya/Satır veya grep kanıtlı"
open_questions:
  - "C3 (ADR-097 ↔ 'K-space kaldırıldı') orkestratör kararı bekliyor → AÇIK"
group: kanit
---

# K008-domain — Risk Defteri (Kök)

> **Authority: Bayram Ali / Vault Steward.** K008-domain genelinde risk kayıtları.
> Durumlar: `AÇIK` (önlem bekliyor) · `DÜZELTMEDE` (bu revizyonda ele alınıyor) ·
> `KAPANDI` (düzeltme uygulandı + audit ile doğrulandı, 2026-10-15) ·
> `KAYITLI` (bilinçli kabul). Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`.

## §1 RİSK TABLOSU (R1–R12)

| # | Risk | Kanıt (Dosya/Satır) | Etki | Azaltım | Durum |
|---|------|---------------------|------|---------|-------|
| R1 | Domain event'leri tanımlı ama **yayınlayan yok** | `shared/src/Events/EventDispatcher.php` satır 28 (dispatch) · `shared/src` geneli `->dispatch(` = 1 isabet (`PageRouterKernel.php` satır 118 — router, event değil; 2026-10-15 grep) | event-driven mimari kopuk (ADR-086 cansız) | yayınlayıcı + listener kaydı ve testi | `AÇIK` |
| R2 | `library` domain'inde **kod kanıtı yok** | repo geneli PHP/JS grep `favorite\|user_favorites` = 0 isabet (2026-10-15) · SQL: `.ai/.sql/mysql/coremusic_user.sql` satır 59 (`user_favorites` var) | davranış sınırı çizilemiyor | ADR-097 K4 + sahip servis yazımı | `AÇIK` |
| R3 | `context-mapping` kod karşılıksız | `shared/` grep `context_mapping` = 0 isabet (2026-10-15) | harita yalnız vault'ta, kodda doğrulanamaz | kademe-3 design + ADR onayı | `AÇIK` |
| R4 | C4 ihlali (önceden var) | `domain-web-arastirma.md` §2 (martinfowler BoundedContext) ↔ `music/music-dogrulama.md` satır 26 | URL tekliği bozuk | Faz D: music-dogrulama satır 26 kök pointer ile değiştirildi · audit: `k008-url-audit2.ps1` = 0 cross-file dup (2026-10-15) | `KAPANDI` |
| R5 | Kök web dosyasında kırık URL + internal dup | `domain-web-arastirma.md` §15 (Crossfading_(audio)→Crossfading, Mood_(music)→Music_and_emotion; ikisi de 200) · Navidrome subsonic URL §9-48 ↔ §12-72 | kanıt tekrarı/erişim hatası | §15 kaydı + §16 cetveli 2026-10-15 güncellendi · internal dup (#48↔#72) 2026-10-15 kaldırıldı (76 kayıt → 75 benzersiz URL, `k008-url-audit2.ps1` = 0 dup) | `KAYITLI` (yalnız kırık URL'ler; dup KAPANDI) |
| R6 | "domain" kelimesinin **iki anlamı** | `shared/config/domain.php` satır 7–15 (7 URL subdomain) ↔ `domain-master.md` §3.1 (10 Bounded Context) | ubiquitous language bulanıklığı | `domain-evrensel-dil.md` §1'de ayrım | `AÇIK` |
| R7 | master §4.1 matrisi disk gerçeğiyle çelişti | `domain-master.md` satır 134–145 ("yazıldı") ↔ 2026-10-15 glob (30/60 dosya) | plan güvenilirliği | Faz D: §4.1 satırları disk sayımıyla yeniden yazıldı · 72/72 dosya doğrulandı (2026-10-15 glob: 12 kök + 60 çocuk) | `KAPANDI` |
| R8 | Eskimiş event adları | `domain-bilesenler.md` §3.4/§3.6 · `domain-evrensel-dil.md` §6 (`MediaTranscodedEvent`/`PlaybackQueueUpdatedEvent`) ↔ `shared/src/Events/Domain/` 9 dosya (2026-10-15) | yanlış kanıt satırı | Faz D: adlar gerçek event'lerle değiştirildi (bilesenler §3.4/§3.6 + evrensel-dil §6 + master §3.1, 2026-10-15) · `grep MediaTranscodedEvent\|PlaybackQueueUpdatedEvent` = 0 | `KAPANDI` |
| R9 | ADR-097 ↔ "K-space kaldırıldı" (master §6-C3) | `shared/AGENTS.md` §5 notu (2026-10-12) ↔ `ADR-097 §1` | yetki belirsizliği — bu setin meşruiyeti | orkestratör kararı (onaysız kapanmaz) | `AÇIK` |
| R10 | Erişilemez web kaynakları | 2 URL 404 (§15) + 403 bot-block (iso.org · acm.org · hydrogenaud.io · isni.org — k008-httpcheck 2026-10-15) | ilgili iddialar web'den doğrulanamaz | `⚠️ VERIFICATION REQUIRED` ile kullanılır | `KAYITLI` |
| R11 | `recommendation` sahipliği açık (master §6-C2) | `domain-master.md` §4.3 satır 160–166 · `shared/src/AI/AIEngine.php` satır 30–32, 49 | sınır ADR'si yok | `domain-kararlar.md` §5 ADR bekliyor | `AÇIK` |
| R12 | Kanıt yoğunluğu dengesiz | 2026-10-15 glob (eski): music/media/user 4'er dosya, 7 çocuk yalnız `index.md` | parçalı okuma, eksik kanıt | bu turda **72/72 dosya** üretildi (12 kök + 10 çocuk × 6) · çocuk `index.md` §GRUPLAR 6'şar satır · kök `index.md` §1 + doğrulama satırı 2026-10-15 güncellendi | `KAPANDI` |

## §2 MASTER CONFLICT BAĞLANTISI (domain-master.md §6)

| Conflict | Risk karşılığı | Durum |
|---|---|---|
| C1 (`K008-domain` ↔ `K008-servisler` ID) | — | `⚠️ VERIFY REQUIRED` (dokunulmaz, onaysız ad değiştirme yok) |
| C2 (`discovery`/`recommendation` sahipliği) | R11 | `⚠️ VERIFY REQUIRED` |
| C3 (ADR-097 ↔ K-space kaldırıldı) | R9 | `⚠️ VERIFY REQUIRED` |
| C4 (root `depends_on: []` bilinçli boş) | — | kanıt toplanınca doldurulacak |

## §3 SAYIM

| Metrik | Değer |
|---|---|
| Risk satırı | 12 (R1–R12) |
| `AÇIK` | 6 (R1 · R2 · R3 · R6 · R11 + master bağlantılı açık conflict'ler) |
| `DÜZELTMEDE` | 3 (R4 · R7 · R8 — Faz D) |
| `KAYITLI` | 2 (R5 · R10) |
| `İşleniyor` | 1 (R12) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
