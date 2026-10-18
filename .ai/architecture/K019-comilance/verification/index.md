---
node_id: K019-comilance/verification
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K019-comilance
  - controls
evidence:
  - "Web doğrulama tur-1 kaydı: .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §1.6 (2026-10-12, exa/websearch) — dosya diskte mevcut (doğrulandı 2026-10-17)"
  - ".ai/architecture/index.md §9 faz D — 'Web doğrulama turları → -dogrulama.md işlenmesi'"
  - "Tur-2 (2026-10-17): OWASP Top 10:2025 — top10.owasp.org/2025/en · ISO 27001:2022 Annex A — secureframe.com/hub/iso-27001/controls + drata.com/learn/iso-27001/controls-annex-a + glocertinternational.com · KVKK — kvkk.gov.tr/Icerik/5362 (Veri İhlali Bildirimi, 2019/10) + kvkk.gov.tr/Icerik/4192 (Kanun Kapsamındaki Hak ve Yükümlülükler)"
  - "Disk ölçümü 2026-10-17: K000–K013 arası 14 domain klasörü mevcut (Test-Path) · K020-adr/ K020-licence/ K020-comere/ YOK · adr/ VAR (3 md) · .ai/.decisions/accepted/ 81 ADR"
validation:
  - "2026-10-12 — tur-1 kayıtları ADR-097 §1.6'da mevcut (JUCE/Koel/Ampache/TPA3255/ASIO/YouTube Music/Spotify/MusicKit...)"
  - "2026-10-17 — tur-2 çalıştırıldı: OWASP liste + ISO Annex A yapısı + KVKK m.12/m.16/72s doğrulandı; 3 kırık referans tespit edildi (K020-adr/K020-licence/K020-comere diskte yok)"
  - "2026-10-17 — node_id diske hizalandı: K019-comilance (kullanıcı kararı: disk kanıtı esastır)"
open_questions:
  - "Tur-3 (KVKK saha denetimi: VERBİS kaydı, aydınlatma metni, saklama süresi) henüz çalıştırılmadı"
  - "ISO SoA denetimi (hangi A.5–A.8 kontrolü uygulanır) çalıştırılmadı"
  - "§16 hedeflerinin (%100/≥%80/≥%95) sayısal ölçümü hâlâ yok → VERIFY REQUIRED"
group: mimari
---


# K019-comilance/verification — Doğrulama Turu Kayıtları

**ELI15:** "Ne zaman denetledik, ne bulduk?" sorusunun cevabı. Kayıtlar
**eklenerek** tutulur; eski satır değiştirilmez (append-only).

## Tur kayıtları

| Tur | Tarih | Kapsam | Sonuç | Kaynak |
|---|---|---|---|---|
| tur-1 | 2026-10-12 | Web doğrulama: JUCE · Koel · Ampache · TPA3255 · Signalsmith · ASIO SDK · YouTube Music · Spotify · MusicKit · Protobuf | sonuçlar ADR-097 §1.6'ya işlendi; 2 satır `⚠️ VERIFY REQUIRED` (DSPark "90+" · ASIO lisans etkisi) | `@.ai/.decisions/draft/ADR-097-…md` §1.6 |
| tur-2 | 2026-10-17 | OWASP Top 10:2025 yeniden doğrulama + ISO 27001:2022 Annex A yapısı + KVKK (m.12/72s/m.16) + disk referans denetimi | OWASP A01–A10 **doğrulandı** (A03/A10 yeni, SSRF A01'e taşındı) · ISO **93 kontrol/4 tema** doğrulandı (SoA yok → uygulama denetlenmedi) · KVKK **72 saat** bildirimi doğrulandı (Kurul 2019/10) · **3 kırık referans** tespit: `K020-adr/` `K020-licence/` `K020-comere/` diskte YOK → `controls/index.md` + `compliance-master.md` dürüst notla düzeltildi | `top10.owasp.org/2025/en` · secureframe/drata/glocert · `kvkk.gov.tr` · Test-Path ölçümü |
| tur-3 | — | KVKK saha denetimi (VERBİS · aydınlatma metni · saklama/silme · ilgili kişi başvuruları) | **çalıştırılmadı** | `⚠️ VERIFY REQUIRED` |
| tur-4 | — | ISO SoA denetimi + §16 hedef sayısal ölçümü (coverage/CI pass/GitLeaks) | **çalıştırılmadı** | `⚠️ VERIFY REQUIRED` |

## Kayıt formatı (her tur için)

```text
| tur no | tarih | kapsam | yöntem (araç/kaynak) | sonuç | kanıt (dosya yolu / URL) | durum |
```

- **durum** değerleri: `geçti` · `kaldı` · `VERIFY REQUIRED`
- Kanıtsız sonuç **yazılmaz**; doğrulanamayan iddia `⚠️` ile işaretlenir
  (kök AGENTS.md §3 Zero-Hallucination).
- Tur-2 örneği: sonuç = tespit + düzeltme; **"geçti" DEĞİL** — çünkü denetlenen
  kontrolün kendisi değil, **kaynak/doğrulama altyapısı** doğrulandı.

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `[[../index]]` | Klasör künyesi + denetim döngüsü |
| `[[../compliance-master]]` | Denetim döngüsü adımları (§4) + kayıt formatı (§9) |
| `[[../controls/index]]` | Denetlenecek kontrol listesi (OWASP + ISO + K000–K013) |
| `[[../policies/index]]` | Politika dayanakları |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Doğrulama turu kayıt defteri (append-only) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
