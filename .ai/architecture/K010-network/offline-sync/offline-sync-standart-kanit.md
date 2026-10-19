---
node_id: K010-network/offline-sync/standart-kanit
node_type: module
status: draft
parent_id: K010-network/offline-sync
role: kanit
children: []
depends_on: []
evidence:
  - "MDN IndexedDB · Service Worker · Background Sync · Broadcast Channel · Web Storage (developer.mozilla.org, HTTP 200, 2026-10-15)"
  - "sqlite.org — yerel kuyruk depolama (HTTP 200, 2026-10-15)"
validation:
  - "Tüm URL'ler Invoke-WebRequest ile 2026-10-15'te HTTP 200 doğrulandı (başlık §1–§2)"
open_questions:
  - "Sahiplik: K014 mü K015 mü K012 mi → ⚠️ VERIFICATION REQUIRED (§7/2) — bu dosya yalnız teknik yol kanıtı taşır"
  - "Kuyruk girişimi/kuyruk sırası stratejisi (conflict/last-write) → ⚠️ UNKNOWN (faz C)"
group: kanit
---

# offline-sync — standart-kanit (kademe-2 · module · rol: kanit)

**ELI15:** "Bağlantı yokken çalış, gelince eşitle" iddiasının teknik yollarının URL
kanıt defteri. Her satır 2026-10-15'te HTTP 200 ile doğrulanmıştır (S1 kanıtı).

**Sınır (ne DEĞİL):** Kuyruğun **içeriği/sahibi** (§7/2 — K015/offline · K012) ·
dış eşitleme (K013/synchronization) · şifreleme (K009).

## §1 Tarayıcı Kolu — depolama + arka plan (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 1 | MDN IndexedDB API | Yapısal yerel depolama (kuyruk kayıtları) | https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API | ✅ 200 |
| 2 | MDN Service Worker API | Ağ bağımsız istek yakalama katmanı | https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API | ✅ 200 |
| 3 | MDN Background Synchronization API | Bağlantı gelince arka planda gönderim | https://developer.mozilla.org/en-US/docs/Web/API/Background_Synchronization_API | ✅ 200 |
| 4 | MDN Broadcast Channel API | Aynı cihazdaki sekmeler arası kuyruk duyurusu | https://developer.mozilla.org/en-US/docs/Web/API/Broadcast_Channel_API | ✅ 200 |
| 5 | MDN Web Storage API | Hafif (anahtar-değer) depolama — küçük bayraklar | https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API | ✅ 200 |

## §2 Yerel/Backend Kolu — kuyruk deposu (S1 — HTTP 200 · 2026-10-15)

| # | Kaynak | Konu | URL | Durum |
|---|---|---|---|---|
| 6 | SQLite | Gömülü transactional depo (native kuyruk — AGENTS §17/9 "Offline-First + SQLite queue" satırının teknik zemini) | https://sqlite.org/ | ✅ 200 |

## §3 Kanıt → Kapsam Eşlemesi (hangi iddiayı destekler)

| İddia | Destekleyen kanıt | Sınıf |
|---|---|---|
| Tarayıcıda offline kuyruk teknik olarak mümkündür | IndexedDB + Service Worker (§1/1–2) | S1 ✅ |
| Bağlantı gelince otomatik gönderim yolu standarttır | Background Synchronization (§1/3) | S1 ✅ |
| Native tarafta transactional kuyruk mümkündür | SQLite (§2/6) | S1 ✅ |
| Kuyruğun **içeriği/sahibi** (K014 vs K015 vs K012) | Seçim YOK — teknik yol ≠ sahiplik | ⚠️ §7/2 faz C |
| Çakışma stratejisi (last-write vs kuyruk sırası) | Seçim YOK | ⚠️ faz C |

## §4 Kanıt Sınıfı Özeti

| Sınıf | Sayı |
|---|---|
| S1 (URL + HTTP 200) | 6 |
| S3 (beyan, URL'siz) | 0 |
| UNKNOWN (karar bekleyen) | 2 (sahiplik §7/2 · çakışma stratejisi) |

**Kapı:** "offline-sync teknik olarak yapılabilir mi" sorusu S1 ile kapalıdır;
"kuyruk kime ait" (index §A/2 = master §7/2) açık kalır — teknik yol kanıtı,
sahiplik kanıtı değildir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
