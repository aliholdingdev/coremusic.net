---
node_id: "K020-discovery/dogrulama"
node_type: "dogrulama"
status: "draft"
parent_id: "K020-discovery"
children: []
depends_on:
  - "K020-discovery/discovery-master.md"
  - "K020-discovery/discovery-web-arastirma.md"
  - ".ai/architecture/index.md (§9.12.1 ölçüm kanonu)"
evidence:
  - "2026-10-17 disk doğrulaması: v2 ağacı satır 571–575/98/616 · ADR-097 §1 madde 6 (satır 33) · ADR-097 §2 K6 (satır 77–78) · K007 S-1 · D-1 · K008 C2"
  - "2026-10-17 geniş servis taraması (discovery-web-arastirma.md — 12 servis / 18 URL)"
  - "2026-10-17 disk ölçümü: log.md 118 satır · README 64 satır · registry §9.12.1/§9.13 · nova-search-engine envanteri"
validation: "partial"
open_questions:
  - "S-1 ID CONFLICT (K020-discovery ↔ K020-search) — v2 satır 98/616 + ADR-097 §2 K6; kapanış için üst karar gerekir"
  - "K008 C2 sahiplik çakışması (domain-master.md:188 ↔ K020-discovery keşif yüzeyi) — domain + architect"
  - "Medya-sağlayıcı auth gate ADR'si YOK (ADR-010 = CSRF stratejisi) — üretim entegrasyonu bloke; onay kullanıcı yetkisi"
group: "kanit"
---

# K020-discovery — Doğrulama (kanıt seti, kapılar ve ölçüm)

> **Rol:** Keşif disiplininin **kanıt dayanağını** (tarih + kaynak + satır), doğrulama
> kapılarını ve dosya ölçüm kanonunu tek yerde toplar. **Tahmin yok** — her iddia ya
> disk kanıtı ya da `⚠️ VERIFICATION REQUIRED` taşır (ZERO-HALLUCINATION, kök AGENTS.md §3).
>
> **Konum:** `.ai/architecture/K020-discovery/discovery-dogrulama.md`
> **Ölçüm kanonu:** `[System.IO.File]::ReadAllLines($path).Length` (registry §9.12.1, satır 761–764).

---

## 1. Doğrulama seviyeleri (bu disiplinde geçerli)

| Seviye | Ne demek | Kullanım |
|---|---|---|
| `verified` | Disk kanıtı var: gerçek dosya + satır + tarih | kesin iddialar |
| `partial` | Kısmi kanıt: kısmen doğrulanmış, eksik tur var | bu dosyanın `validation` durumu |
| `⚠️ VERIFICATION REQUIRED` | Doğrulanamayan iddia | ID CONFLICT · K008 C2 sahiplik · auth gate onayı |
| `UNKNOWN` | Bilinmeyen değer | örn. Deezer quota (aşağıdaki tablo) |

**Kural:** `partial` bir node, `verified` **gibi sunulamaz**; `⚠️ VERIFY REQUIRED`
bir madde asla **bulgu** sayılmaz, yalnız **açık soru** olarak taşınır.

---

## 2. Tur-1 kanıt seti (2026-10-17 disk doğrulaması — 2026-10-12 taslağının düzeltilmiş hâli)

> **Truth Mode düzeltme (2026-10-17):** Bu dosyanın önceki sürümü tur-1 kanıtı olarak
> `log.md:774–801` ve benzeri satır atıfları taşıyordu. **log.md diskte 118 satırdır** —
> 774+ satır atıfları **imkânsızdır ve uydurmadır**. Ayrıca `S-2…S-7` etiketleri,
> `ADR-097 §2.2/2.3/2.4/§6.1` (pipeline/enum/DUP) ve `adr-097-discovery-contract.md`
> dosya adı gerçek değildir. Tam döküm: `discovery-master.md` §9.3. Aşağıdaki tablo
> **yalnızca 2026-10-17'de diskten okunarak** doğrulanmış satırları taşır.

| # | İddia | Kanıt (dosya + satır) | Seviye |
|---|---|---|---|
| 1 | K020-discovery v2 mimari ağacında kayıtlı: `K020-discovery/{index.md, search/, agent/}` | `coremusic-katmanli-mimari-agaci-v2.md` satır 571–575 | `verified` |
| 2 | **ID CONFLICT:** `K020-discovery` ↔ `K020-search` iki ayrı node olarak v2'de çakışır | v2 satır 98/616 · ADR-097 §2 K6 (satır 77–78) | `verified` (çakışma gerçeği; **çözüm üst karar** — §6) |
| 3 | S-1 sahiplik iddiası: `search/` yüzeyi K007'ye ait sayılır | `K007-data/data-master.md:219` · `data-kapsam.md:148/126 (K-SCOPE-4)` · `data-bagimliliklar.md:132/207/439` · `K007-data/search/index.md:21` | `verified` (id dia var; **karşılıklı** — §6) |
| 4 | D-1 bağımlılık iddiası: `K020-recommendation → K020-discovery` | `K020-recommendation/recomenataion/index.md:12` · `recommendation-master.md:16/34/43` | `verified` (id dia var) |
| 5 | K008 C2 keşif sahipliği iddiası | `K008-domain/domain-master.md:188` | `verified` (çakışma → §6) |
| 6 | YouTube Music: resmi API **YOK** · Spotify PKCE · MusicKit token | ADR-097 §1 Bağlam madde 6 (satır 33) | `verified` (kapı **değişmez**) |
| 7 | Gerçek arama yüzeyi repo'da: `nova-search-engine` | `app-nodejs-projects/nova-search-engine/` — `packages/search/{engine,ranking,search-index,recommend}.ts` · `packages/common/signals.ts` (60 satır, dil/spam skoru) · `.ai/decisions/ADR-001-youtube-api-v3-yasak.md` · proje kökü: `.ai/projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular.md:4` | `verified` |
| 8 | Eski "68 dosya · §9.13 partial = K020-discovery dahil 4 node" iddiası | **GERÇEK DEĞİL** — registry §9.13 K000–K013'ü kapsar, K020 **dahil değil** | silindi |

**Tur-1 kapanış notu:** Bilgi tekrarı yasağı gereği bu 7 kayıt yeniden araştırılmaz;
yeni tur yalnız eksikleri (tur-2) tamamlar. Uydurma atıflar **hiçbir yerde**
kaynak olarak kullanılamaz.

---

## 3. Tur-2 kanıt seti (2026-10-17 · geniş servis taraması)

> Tam döküm: `discovery-web-arastirma.md`. Bu bölüm **kapıları** özetler.

| # | Kapı / bulgu | Kanıt (tarih) | Seviye | Keşif etkisi |
|---|---|---|---|---|
| 1 | Spotify PKCE · 429/Retry-After (30 sn rolling) | 2026-10-17 web taraması | `verified` (URL doğrulandı) | skor/kümeleme girdisi |
| 2 | Apple Music JWT (ES256+`kid`, `exp ≤ 15777000 sn`) · 429 | 2026-10-17 | `verified` | auth keşfi (gate: ADR YOK — §6) |
| 3 | YouTube Data API v3 `search.list` 100/gün · 10.000 unit · YT Music **resmi API yok** | 2026-10-17 | `verified` / KAPALI | kod yasağı (tur-1 kapısı **değişmez**) |
| 4 | Tidal OAuth 2.1 + **zorunlu PKCE** | 2026-10-17 | `verified` | auth keşfi |
| 5 | Deezer genel katalog araması (kota sayısı yayımlanmadı) | 2026-10-17 | `UNKNOWN` kota | `⚠️ VERIFY REQUIRED` |
| 6 | MusicBrainz 1 istek/sn/IP · User-Agent **zorunlu** · 503 | 2026-10-17 | `verified` | rate-limit keşfi |
| 7 | ListenBrainz kullanıcı token'ı · **katalog araması yok** | 2026-10-17 | `verified` | kapsam dışı |
| 8 | Genius OAuth2/client token · **ticari lisans kapısı** | 2026-10-17 | `verified` | kapı mantığı ADR-010 (CSRF state/PKCE) ile benzer |
| 9 | Discogs OAuth 1.0a + personal token · kademeli rate | 2026-10-17 | `verified` | auth keşfi |
| 10 | Amazon Music **kapalı beta** · LWA · `x-api-key` · ~1 sa token | 2026-10-17 | `verified` | erişim engeli |
| 11 | Last.fm `api_sig` (md5) · `sk` · 5/sn | 2026-10-17 | `verified` | imzalı çağrı keşfi |
| 12 | Musixmatch 2.000/gün (ücretsiz) · HTTP 402 · `X-RateLimit-Remaining` | 2026-10-17 | `verified` | kota kapısı |

**URL doğrulama (2026-10-17):** 12 servis belgesinin 18 URL'si fetch edildi;
**0 URL hatası** (ayrıntı: `discovery-web-arastirma.md` §6).

---

## 4. Doğrulama kapıları (her keşif dosyası YAZIM SONU kontrolü)

| # | Kapı | Kontrol | Kırılırsa |
|---|---|---|---|
| 1 | Frontmatter | 9 alan + `group` (mimari/kod/kanit) + Authority footer | dosya geçersiz |
| 2 | Ölçüm | `ReadAllLines().Length` (registry §9.12.1) — `Get-Content \| Measure-Object -Line` **geçersiz** | sayı uydurma riski |
| 3 | Tahmin yasağı | tarih/URL/API **uydurulmadı**; doğrulanamayan `⚠️` | ZERO-HALLUCINATION ihlali (kök AGENTS.md §3) |
| 4 | Secret yasağı | token/anahtar `.md`'ye **yazılmadı** (yalnız `.env` anıldı) | ADR-095 §1.2 (satır 36) ihlali |
| 5 | Sıra yasağı | `APPROVAL → IMPLEMENTATION` ters çevrilmedi | kök AGENTS.md §1 ihlali |
| 6 | Kod sınırı | entegrasyon kodu **yok** (yalnız dilekçe örneği); YTM **kapalı** | ADR-097 §1 madde 6 (satır 33) ihlali |
| 7 | Sahiplik | S-1/K008 C2/ID CONFLICT çakışmaları `⚠️` olarak taşındı; **çözülmedi gibi sunulmadı** | kanıt sahteciliği |
| 8 | Dil | Türkçe prose, mojibake yok, `// ...` dolgu yok | kalite ihlali |

---

## 5. Dosya ölçümü (kanonik yöntem + örnek)

| Dosya kümesi | Yöntem | Değer (2026-10-17) |
|---|---|---|
| K020-discovery kök içerik dosyaları | `[System.IO.File]::ReadAllLines()` | 98–223 satır · **0 × ≥500** (bilinen boşluk — düzeltme kapsamı dışı, dürüst rapor) |
| `search/` + `agent/` içerik dosyaları | aynı kanon | `agent/` 503–1268 ✓ · `search/` 114–1464 (kök bazıları < 500 — bilinen boşluk) |
| Yeni K-node dosyaları | registry kapanış kaydı (§9.14) **yazılmadan** sayılmaz | ekleme: **orchestrator** |

**Yanlış yöntem (geçersiz):** `Get-Content $f | Measure-Object -Line` (registry
§9.12.1: bu yöntemle ölçülen sayı **geçersizdir**).

**PowerShell kanonu:**

```powershell
[System.IO.File]::ReadAllLines("C:\www\coremusic.net\.ai\architecture\K020-discovery\discovery-dogrulama.md").Length
```

---

## 6. Açık doğrulama bekleyenler (bu disiplin dışına HAVALE)

| # | Bekleyen | Kanıt | Neden çözülemedi | Üst merci |
|---|---|---|---|---|
| 1 | S-1 ID CONFLICT (`K020-discovery` ↔ `K020-search`) | v2 satır 98/616 · ADR-097 §2 K6 (satır 77–78) | keşif tek başına ID kararı **veremez** | registry SSOT + üst karar |
| 2 | K008 C2 sahiplik çakışması (K008 discovery ↔ K020-discovery) | `domain-master.md:188` | iki disiplin sınırı | domain + architect |
| 3 | Medya-sağlayıcı auth gate onay durumu | **ADR-010 = CSRF stratejisi**; medya-sağlayıcı auth gate ADR'si **diskte YOK** | gate metni henüz yazılmadı; onay **kullanıcı** yetkisi | Bayram Ali (gate onayı) |
| 4 | Deezer API kota sayısı | resmi sayı yayımlanmadı (2026-10-17 web) | `⚠️ VERIFY REQUIRED` | web |
| 5 | Yeni K020 dosyalarının registry kaydı | ✅ **KAPANDI 2026-10-17** — registry §9.14 eklendi (ölçüm §9.14.2; §9.1 satır 202 için geçersizlik notu eklendi) | — | orchestrator |

---

## 7. Doğrulama özeti ve İlgili dosyalar

### 7.1 Özbu dosyanın doğrulaması

- [x] Frontmatter 9 alan + `group: kanit` + Authority footer.
- [x] Tur-1 (7 gerçek kayıt — uydurma log.md/S-2…S-7 atıfları **silindi**) + tur-2 (12 servis / 18 URL) kanıt tarihiyle taşındı.
- [x] `⚠️ VERIFY REQUIRED` 5 madde açıkça üst merciye havale edildi (§6).
- [x] Ölçüm kanonu §9.12.1 (yalnız `ReadAllLines`).
- [ ] `validation: partial` — açık maddeler (§6) çözülmedi; içerik dosyalarının bir kısmı < 500 satır (bilinen boşluk).

### 7.2 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `K020-discovery/discovery-web-arastirma.md` | tur-2 tam döküm (bu dosyanın kaynağı) |
| `K020-discovery/discovery-master.md` | gate haritası (§6) + Truth Mode düzeltme kaydı (§9.3) |
| `.ai/architecture/index.md` §9.12.1 | ölçüm kanonu (satır 761–764) + K020-discovery kapanış kaydı §9.14 (orchestrator) |
| `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` | tur-1 §1 madde 6 (satır 33) + §2 K6 (satır 77–78) kaynağı |
| `K020-discovery/index.md` | route + çocuk listesi |

---

**Authority:** Bayram Ali / Vault Steward
