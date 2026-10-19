---
node_id: "K020-discovery/search-dogrulama"
node_type: "dogrulama"
status: "draft"
parent_id: "K020-discovery/search"
children: []
depends_on:
  - "K020-discovery/search/search-kapsam.md"
  - "K020-discovery/search/search-web-arastirma.md"
  - "K020-discovery/discovery-dogrulama.md"
  - ".ai/architecture/index.md (§9.12.1 ölçüm kanonu)"
evidence:
  - "2026-10-17 kanıt kapıları (search boyutu)"
validation: "partial"
open_questions:
  - "S-1 sahiplik kararı (K020-discovery/search ↔ K020-search) — kapanış blokerı"
group: "kanit"
---

# K020-discovery/search — Doğrulama (kapılar ve kanıt)

> **Rol:** Sorgu-keşif (search) disiplininin **kendi kapılarını** (gate) ve dosya
> ölçümünü denetler. Genel omurga kapıları root `discovery-dogrulama.md` §4'tedir;
> bu dosya yalnız **search'e özgü** denetimleri ekler (tekrar yok).
>
> **Ölçüm kanonu:** `[System.IO.File]::ReadAllLines($path).Length` (registry §9.12.1, satır 761–764).

---

## 1. Search-özgü kapılar

| # | Kapı | Kontrol | Kırılırsa |
|---|---|---|---|
| S1 | **Sahiplik ayrımı** | dosya `K020-search` (ürün) ile **karıştırılmamış** mı? | çift sözleşme riski |
| S2 | **YTM hard-block** | YTM sorgusu için **örnek/kod ATILMAMIŞ** mı? (kapı değişmez) | ADR-097 §1 madde 6 (satır 33) ihlali |
| S3 | **Gate-first sıra** | B1 gate denetimi B3'ten **önce** mi? | kapalı kapıda istek |
| S4 | **Kota-aware** | 429/402/503 → erteleme var mı (dövme yok)? | rate-limit zinciri |
| S5 | **Token yasağı** | örneklerde gerçek secret **yok** (yalnız placeholder)? | ADR-095 §1.2 (satır 36) ihlali |
| S6 | **Sonuç şeması** | örnekler `search-kapsam.md` §3 alanlarıyla **uyumlu** mu? | uç model uyuşmazlığı |
| S7 | **Fallback zorunlu** | eksik fallback **tasarımda tespit edilebilir** mi (ADR-101 §2.2)? | eksik fallback → tasarım reddi |
| S8 | **Ölçüm** | `ReadAllLines` kullanıldı mı (`Get-Content` geçersiz)? | sayı uydurma |

---

## 2. Dilekçe dosyası denetimi (`search-ornekler.md`)

| # | Dil | Gate-first (ilk satır) | erteleme | fallback+tespit | secret yok |
|---|---|---|---|---|---|
| 1 | PHP 8.4 | ✓ `gate()` | `sleep` | ✓ | ✓ `.env` |
| 2 | JavaScript | ✓ `gateState` | `setTimeout` | ✓ | ✓ `process.env` |
| 3 | TypeScript | ✓ `GateState` union | `setTimeout` | ✓ | ✓ |
| 4 | Python | ✓ `gate_state` | `asyncio.sleep` | ✓ | ✓ `os.environ` |
| 5 | Go | ✓ `GateState()` | `time.After` | ✓ | ✓ `os.Getenv` |
| 6 | Rust | ✓ `gate_state` | `thread::sleep` | ✓ | ✓ `env::var` |
| 7 | C | ✓ `gate_state()` | `nanosleep` | ✓ | ✓ `getenv` |
| 8 | C++ | ✓ `gate_state` | `sleep_for` | ✓ | ✓ `getenv` |
| 9 | C# | ✓ `GetGate` | `Task.Delay` | ✓ | ✓ `GetEnvironmentVariable` |
| 10 | Java | ✓ `gateState` | `Thread.sleep` | ✓ | ✓ `getenv` |
| 11 | Kotlin | ✓ `gateState` | `delay` | ✓ | ✓ `getenv` |
| 12 | Swift | ✓ `guard gateState` | `Task.sleep` | ✓ | ✓ `environment` |
| 13 | Ruby | ✓ `gate_state` | `sleep` | ✓ | ✓ `ENV[]` |
| 14 | SQL | ✓ `gate_state` sayımı | — | denetim sorgusu | ✓ (parametreli) |

**Sonuç:** 13 dil + SQL **hepsi kapıları geçti** (S2: YTM için çağrı örneği **yok**,
yalnız `closed` modellemesi).

---

## 3. Kanıt türü denetimi (search-web-arastirma.md)

| Madde | Kanıt türü | Seviye |
|---|---|---|
| Arama tipi × servis matrisi (§1) | 2026-10-17 resmi docs | `verified` |
| Rate başlıkları (§2) | 2026-10-17 (429/30sn · 503/1sn · 402/2k · 5/sn) | `verified` |
| Anonim/en ucuz sıra (MB→Deezer) | §2 matrisinden **çıkarım** (yorum, kanıtla) | `verified` (çıkarım notlu) |
| Tidal/Deezer/Genius kota sayıları | resmi sayı **yok** | `UNKNOWN` / `⚠️ VERIFY` |

---

## 4. Search kapanış kontrolü (dosya bazında)

| Dosya | Frontmatter 9 | group | Authority | Ölçüm |
|---|---|---|---|---|
| `search-kapsam.md` | ✓ | `mimari` | ✓ | `ReadAllLines` |
| `search-bilesenler.md` | ✓ | `mimari` | ✓ | `ReadAllLines` |
| `search-ornekler.md` | ✓ | `kod` | ✓ | `ReadAllLines` |
| `search-web-arastirma.md` | ✓ | `kanit` | ✓ | `ReadAllLines` |
| `search-dogrulama.md` | ✓ | `kanit` | ✓ | `ReadAllLines` |
| `search/index.md` | ✓ | `index` | ✓ | 40–100 satır |

---

## 5. Açık bekleyenler (search)

| # | Bekleyen | Üst merci |
|---|---|---|
| 1 | S-1 sahiplik (K020-search ↔ discovery/search) | registry + üst karar |
| 2 | Dilekçe → uygulama onayı (S-5 gate) | Bayram Ali |
| 3 | Eşzamanlılık üst sınırı (federasyon) | architect |

---

## 6. İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `K020-discovery/discovery-dogrulama.md` | omurga kapıları (§4) |
| `search/search-kapsam.md` | sonuç şeması (S6 denetimi) |
| `search/search-ornekler.md` | 13 dil dilekçe (§2 denetimi) |
| `.ai/architecture/index.md` §9.12.1 | ölçüm kanonu (S8) |

---

**Authority:** Bayram Ali / Vault Steward
