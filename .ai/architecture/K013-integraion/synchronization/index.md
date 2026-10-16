---
node_id: K013-integraion/synchronization
node_type: subdomain
status: draft
parent_id: K013-integraion
children:
  - sync-anayasa
  - sync-sozlesme
  - sync-dogrulama
  - sync-kod-ornekleri
  - sync-web-arastirma
depends_on: []
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
  - "sync-anayasa.md §Kaynak Tablosu [S1]–[S18] (2026-10-16 fetch, 18 URL)"
  - "sync-sozlesme.md §Kaynak Tablosu [R1]–[R11] (2026-10-16 fetch, 11 URL)"
  - "sync-dogrulama.md §Kaynak Tablosu [V1]–[V11] (2026-10-16 fetch, 11 URL)"
  - "sync-kod-ornekleri.md 13 dil örneği (dokunulmaz — 2026-10-16 sayımı 812 satır)"
  - "sync-web-arastirma.md §3 [W1]–[W18] (2026-10-16 fetch, 18 benzersiz resmi kaynak · §9 4 elenen aday · §11A–§12 tur-2 kayıt defteri)"
validation:
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
  - "Her içerik dosyası ≥350 satır (anayasa 505 · sozlesme 431 · dogrulama 443 · kod-ornekleri 812 · web-arastirma 380; index hariç)"
  - "Toplam 47 kaynak satırı / 18 benzersiz URL (web-arastirma §7 C-4 ölçümü; 2026-10-16)"
  - "Uydurma sürüm/arayüz/dosya adı yok; doğrulanamayan satır ⚠️ VERIFICATION REQUIRED / UNKNOWN işaretlidir"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Sahiplik: K013 mü K015 mi (aynı ad) → ⚠️ VERIFICATION REQUIRED (§7/2)"
  - "Checksum algoritması / reconciliation periyodu / senkron yönü CoreMusic seçimi → UNKNOWN (kaynaklar çekildi, karar yok — web-arastirma §10/4-5)"
group: mimari
---


# synchronization — K013-integraion (kademe-2 · subdomain)

**ELI15:** Dış sistemle verinin eşitlenmesi: ne zaman, hangi yön, çakışmada ne olur.

**Sınır (ne DEĞİL):** Kalıcı veri K007-data · medya katalog senkronu K015 ile sınır belirsiz.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 children ✅ 5 dosya (2026-10-16: anayasa · sozlesme · dogrulama · kod-ornekleri · web-arastirma) · kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `sync-anayasa.md` | Eksenler, sınır, sahiplik tartışması | Kademe-3 |
| mimari | `sync-sozlesme.md` | Token/hata/kavram sözleşmesi | Kademe-3 |
| kod | `sync-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| kod | `sync-kod-ornekleri.md` | 13 dilde artımlı senkron örneği | Kademe-3 |
| **kanit** | **`sync-web-arastirma.md`** | **≥12 benzersiz resmi kaynak** | Kademe-3 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Sahiplik: K013 mü K015 mi (aynı ad) → ⚠️ VERIFICATION REQUIRED (§7/2) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Checksum/reconciliation/senkron yonu CoreMusic secimi → UNKNOWN | FM open_questions (kaynaklar web-arastirma'da ✅) |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
