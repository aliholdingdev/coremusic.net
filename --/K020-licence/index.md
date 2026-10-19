---
node_id: K020-licence
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000–K5000 Architecture Registry"
  - K020-comilance
evidence:
  - "v2 §2.20 mini-ağaç (K020-licence → index.md · dependency-licenses.md · license-verification.md) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 563–568)"
  - ".ai/architecture/index.md §5 satır 82 (draft)"
  - "https://www.steinberg.net/developers/asiosdk-open — ASIO SDK GPLv3 açık kaynak (2026-10-12)"
  - "ADR-097 §1.6 web tur-1 — Koel 17.175★ MIT · Ampache 3.793★ AGPL-3.0"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir karşılaştırıldı (3 öğe)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "'licence' yazımı kaynağa sadık korunur; 'license' yapılmaz (v2 §3 KURAL 1)"
  - "ASIO SDK GPLv3'ün projeye etkisi HENÜZ DEĞERLENDİRİLMEDİ → VERIFY REQUIRED (ADR-097 §6/5)"
group: mimari
---


# K020-licence — Bağımlılık Lisans Kaydı

## ELI15 — Bu klasör ne işe yarar?

Projede kullanılan **üçüncü parti parçaların** hangi izinle geldiğini, yani
"bunu kopyalamak/dağıtmak serbest mi?" sorusunun cevabını tutar. Yanlış lisans
bilgisi yayınlama anında sorun çıkarır — bu yüzden her satırın **kaynağı** vardır.

## Çocuk kataloğu (v2 §2.20 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `dependency-licenses.md` | dosya | Lisans kayıt tablosu iskeleti + ASIO bulgusu + referans proje tablosu | draft |
| `license-verification.md` | dosya | Lisans doğrulama prosedürü ve tur kayıtları | draft |

## Durum

- **status:** `draft` — kayıt **1 açık satırla** başladı (ASIO SDK · GPLv3); envanter eksik.
- **children:** `[]` (v2 §2.20'de bu klasörün alt dizini yok).
- **Kapsam sınırı:** uyum **politikaları** `K020-comilance`dedir; burada yalnız **lisans** tutulur.

## Açık soru özeti

1. ASIO SDK GPLv3 → CoreMusic **kapatılıp dağıtılacaksa** etkisi ne? (`⚠️ VERIFY REQUIRED`)
2. Temel bağımlılıkların (PHP, MySQL, JUCE, Composer paketleri) lisans envanteri **yok**.
3. ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| kanit | `dependency-licenses.md` | Bağımlılık lisansı, kaynağı ve tarihi kaydı | Kademe-1 |
| kanit | `license-verification.md` | Lisans kaydı doğrulama yöntemi ve son kontrol tarihi | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
