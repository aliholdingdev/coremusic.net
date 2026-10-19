---
node_id: K013-integraion/adapters
node_type: subdomain
status: draft
parent_id: K013-integraion
children:
  - K013-integraion/adapters/adapters-anayasa
  - K013-integraion/adapters/adapters-sozlesme
  - K013-integraion/adapters/adapters-dogrulama
  - K013-integraion/adapters/adapters-kod-ornekleri
  - K013-integraion/adapters/adapters-web-arastirma
depends_on: []
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
  - "5 çocuk dosya üretildi (2026-10-16): anayasa 427 · sozlesme 376 · dogrulama 401 · kod-ornekleri 700 · web-arastirma 355 satır — hepsi ≥350"
  - "Repo kanıtı: shared/src/Cache/ (CacheInterface + 3 adapter) + shared/src/OAuth/Provider/ (IOAuthProvider + 10 provider) + js/src/lib/adapter-error.ts (2026-10-16)"
  - "Dış kanıt: 21 benzersiz URL (15 içerik çekildi · 6 ⚠️ HEAD-200) + 7 gerekçeli elenen aday — adapters-web-arastirma.md §7/§9"
validation:
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
  - "5 dosya satır sayısı ≥350 doğrulandı — 2026-10-16"
  - "§GRUPLAR 6 satır (index + 5 çocuk) · §A kapıları güncellendi — 2026-10-16"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Kaç adaptör, hangi sistemler → ⚠️ VERIFICATION REQUIRED (envanter §4)"
  - "shared/AGENTS.md '12 provider' der, dizin 10 somut provider gösterir (2026-10-16) → ⚠️ VERIFICATION REQUIRED"
  - "kod-ornekleri §3-§12 (11 dil) URL HEAD-200; API imzaları içerik çekilerek doğrulanmadı → ⚠️"
group: mimari
---


# adapters — K013-integraion (kademe-2 · subdomain)

**ELI15:** Her üçüncü taraf sistem için adaptör: iç modeli dış sistem diline çevirir.

**Sınır (ne DEĞİL):** Dış sistem envanteri external-systems/child · protokol çevrimi protocol-adapters/child.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `adapters-anayasa.md` | Sınır, desen envanteri (port/adapter · ACL · facade · circuit breaker), repo kanıtı | Kademe-2 |
| mimari | `adapters-sozlesme.md` | M1-M8 eşleme kuralları, T1-T12 tip tablosu, §4.1 hata eşleme, versioning | Kademe-2 |
| kod | `adapters-dogrulama.md` | L1-L7 test katmanları, contract test, CDC kabul, CI gate | Kademe-2 |
| kod | `adapters-kod-ornekleri.md` | 13 dilde port + adapter + hata eşleme (repo kanıtı §1-§2, şema §3-§12) | Kademe-2 |
| kanit | `adapters-web-arastirma.md` | 21 benzersiz resmi URL · İddia/URL/Tarih/Durum · §9 elenen 7 aday | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Kaç adaptör, hangi sistemler → ⚠️ VERIFICATION REQUIRED (envanter §4) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Provider sayısı çelişkisi (AGENTS.md 12 vs dizin 10) → ⚠️ | FM open_questions · 2026-10-16 |
| 5 | 11 dil şeması içerik-doğrulanmadı → ⚠️ | FM open_questions · kod-ornekleri §14 |
| 6 | 6 URL HEAD-200 / içerik okunmadı → ⚠️ | web-arastirma §7 · 2026-10-16 |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez. Kapı 4-6, çocuk dosyaların açık sorularını (2026-10-16) bu indekse taşır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
