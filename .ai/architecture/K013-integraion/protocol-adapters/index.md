---
node_id: K013-integraion/protocol-adapters
node_type: subdomain
title: "Protocol Adapters — Kademe-2 Subdomain İndeksi"
type: architecture
status: draft
date: 2026-10-16
parent_id: K013-integraion
children:
  - proto-anayasa
  - proto-sozlesme
  - proto-dogrulama
  - proto-kod-ornekleri
  - proto-web-arastirma
depends_on:
  - K013-integraion
  - K013-integraion/adapters
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir; dizin adı 'integraion' yazım hatası değildir, değiştirilemez)"
  - "proto-web-arastirma.md §2.7 erişim günlüğü (15/15 URL · GET tam içerik · 2026-10-16)"
  - "proto-dogrulama.md §8 repo disk kanıtı (shared/src/OAuth/Provider/ · shared/src/Cache/ · RouteTable.php, 2026-10-16)"
  - "K013-integraion/api-contracts/openapi/index.md (kalıp — §GRUPLAR + kapı tablosu biçimi)"
  - "K013-integraion/adapters/adapters-anayasa.md §2 (kardeş sınırı: protokol ≠ sistem/ACL)"
validation:
  - "5 içerik dosyasının tamamı ≥350 satır (anayasa 374 · sozlesme 354 · dogrulama 360 · kod-ornekleri 743 · web-arastirma 380; index hariç)"
  - "Her içerik dosyasında §GRUPLAR (6 satır) + § Kaynak Tablosu + Authority footer mevcut"
  - "URL doğrulaması: 15 çekildi / 0 404 / 0 elenen (proto-web-arastirma.md §2.7 ve §5)"
  - "Doğrulanamayan her satır ⚠️ VERIFICATION REQUIRED / UNKNOWN / TASARIM İDDİASI ile işaretli"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Hangi protokol çiftleri production'da kullanılıyor → ⚠️ VERIFICATION REQUIRED (repo kanıtı yok)"
  - "API key yüzü için evrensel şartname var mı → ⚠️ VERIFICATION REQUIRED (bu turda çekilmedi)"
  - "gRPC status ↔ HTTP status resmi eşleme tablosu → ⚠️ VERIFICATION REQUIRED (çekilmedi)"
  - "El sıkışmanın bağlantı kurma tarafı (101) K014'te mi bu düğümde mi → ⚠️ VERIFICATION REQUIRED (sınır kararı)"
group: mimari
---


# protocol-adapters — K013-integraion (kademe-2 · subdomain)

**ELI15:** Farklı protokol ve şemaları birbirine çeviren köprüler: JSON↔XML,
HTTP↔gRPC, REST↔SOAP, SSE↔WebSocket, bearer↔API key — aynı anlamı iki farklı
konuşma biçimi arasında taşır.

**Sınır (ne DEĞİL):** İş kuralı K008-domain · ağ taşıması K014-network ·
üçüncü parti sistem/ACL dönüşümü kardeş `adapters` klasörü · dış API
sözleşmesi kardeş `api-contracts/openapi` (protokol ≠ sistem, protokol ≠ uç
nokta belgesi — `proto-anayasa.md` §2).

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve
etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar
TASARIM İDDİASI'dır (kanıt dosyalarında URL'lenmiştir).

**Durum:** draft · kademe-3 içerik bu turda YAZILDI (5 dosya) · kademe-4
(module'e iniş) ADR-097 K4 fazı (C) bekliyor.

**Durum etiketi:** ŞARTNAME IMPLEMENTED (15 URL çekildi) · Repo uygulaması
⚠️ VERIFICATION REQUIRED (protokol dönüşümü yapan sınıf diskte tespit
edilmedi — `proto-dogrulama.md` §8).


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi, çocuklar, kapılar (bu dosya) | 2 |
| mimari | `proto-anayasa.md` | Kimlik, sınır (adapters + K014), 5 çift envanteri, 10 protokol kimlik kartı | 3 |
| mimari | `proto-sozlesme.md` | Çift bazında tanım + sürüm + pazarlık + hata kodları | 3 |
| kod | `proto-dogrulama.md` | L1–L6 doğrulama katmanları, CI kapısı, red-team senaryoları, repo disk kanıtı | 3 |
| kod | `proto-kod-ornekleri.md` | 13 dilde protokol dönüşüm kalıbı (PHP…Elixir) | 3 |
| kanit | `proto-web-arastirma.md` | 15 benzersiz resmi URL + iddia/URL/tarih/durum defteri + erişim günlüğü | 3 |

**Gruplama kuralı:** registry §8.1 + ADR-097 §5.5 — bu klasörde `group`
değerleri `mimari | kod | kanit` (+ `index` bu dosya) ile sınırlıdır.

---

## §1 Hiyerarşi ve Bağımlılık Özeti

| Kademe | Düğüm | Ne katar |
|---|---|---|
| 0 | `K000–K5000 Architecture Registry` | Ağacın kökü |
| 1 | `K013-integraion` | Dış dünya tek kapı (v2 §2.13) |
| 2 | **`K013-integraion/protocol-adapters`** | **Protokol dönüşümü (bu klasör)** |
| 3 | `proto-*` (5 dosya) | mimari 2 · kod 2 · kanit 1 |
| 4 | module — | **Bu turda YOK** (ADR-097 K4 faz C bekliyor; uydurulmadı) |

**Kardeşler (kademe-2):** `api-contracts` (+ `openapi`) · `adapters` ·
`external-systems` · `event-contracts` · `synchronization` ·
`error-handling` · `integration-testing` — sayısal liste
`integraion-bagimliliklar.md` §1'e tabidir (burada çoğaltılmaz).

---

## §2 Dosya Okuma Sırası ve Açık Sorular

**Okuma sırası (öneri):** `anayasa` (hangi çift, sınır kim) →
`sozlesme` (ne konuşulur) → `dogrulama` (nasıl denetlenir) →
`kod-ornekleri` (dile nasıl dökülür) → `web-arastirma` (her satırın URL'si).

**Açık sorular (tam liste FM `open_questions`):** (1) kademe-3 kapsamı →
⚠️ (faz C) · (2) hangi çift production'da → ⚠️ · (3) API key evrensel
şartnamesi → ⚠️ · (4) gRPC↔HTTP status eşleme tablosu → ⚠️ ·
(5) 101 el sıkışması K014'te mi burada mı → ⚠️ (sınır kararı).

---

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Hangi protokol çiftleri envanterde → ⚠️ VERIFICATION REQUIRED (üretim kanıtı yok) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | API key yüzü evrensel şartnamesi → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 5 | gRPC status ↔ HTTP status resmi eşleme → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 6 | 101/Connection tarafının K014 sınırı → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 7 | Satır sayısı kapısı: 5 içerik dosyası ≥350 | ✅ 374/354/360/743/380 |
| 8 | URL kapısı: ≥12 benzersiz resmi kaynak | ✅ 15 (0 404 · 0 elenen) |
| 9 | Kalıp kapısı: her dosyada §GRUPLAR + §Kaynak + footer | ✅ 5/5 |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez (7–9 satırları **ölçüm**dür, iddia değildir).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · openapi kalıp index · **Tarih: 2026-10-16**
