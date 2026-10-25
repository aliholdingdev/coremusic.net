---
type: plan
category: amplifier-architecture
title: "CoreMusic Electronics — Amfi Kodlama Paneli (Görev Kartları)"
date: 2026-10-25
updated: 2026-10-25
status: active
version: 1.0.0
authority: "Derived — SSOT: product-line-requirements.md v1.0.0 · ADR-111 (draft 2026-10-25 · next 112)"
governance: Red Team · Human Mode · Truth Mode
---

# Amfi Kodlama Paneli — Görev Kartları

**Zorunlu Bağlantılar:** [[product-line-requirements]] · [[index]] · [[class-ab]] · [[class-d]] · [[power-supply]] · [[protection]] · [[thermal]] · [[hardware/index]]

**Kullanım:** Bir görev satırındaki **Hazır Prompt**'u kopyalayıp sohbete gönder → agent o işi yapar. `Durum: BAŞLADI` satırları bu panel oluşturulurken paralel başlatılmıştır.
**Kapılar:** `onay` = kullanıcı Human Approval Gate · `mockup` = `.ai/ui-design/` görseli okunmadan UI kodu YASAK · `hesap` = girdi+sonuç yoksa iddia `⚠️ VERIFICATION REQUIRED`.
**Yasaklar (her kartta):** subagent `git commit` ATMAZ · firmware → `dsp-firmware-engineer` · PCB fab dosyası üretilmez · ADR-089 (8×50W studio/home) dokunulmaz.

---

## §1 Görev Kartları

| ID | Görev | Hazır Prompt (kopyala) | Hedef / Çıktı | Agent | Kapı | Durum |
|---|---|---|---|---|---|---|
| T-01 | ADR-111 taslağı | `T-01: product-line-requirements.md ve amplifier/index §4'e dayanarak ADR-111'i Nygard şablonuyla taslak yaz — konu: amfi ailesi + Power Governor + 18650 bypass; numara 111; debate PENDING` | `.ai/.decisions/draft/ADR-111-*.md` + `index.md` (draft 6→7, next 111→112) | `architect` → MO yazar | **onay** | ✅ HAZIR (2026-10-25) |
| T-02 | PRD/ürün belgesi güçlendirme | `T-02: PROJECTS §7.2.1-7.2.3 ve VISION §10.2 için SKU matrisi revizyon metnini üret (ADR-089'a dokunmadan, yeni SKU'lar ayrı satır)` | PROJECTS.md + VISION.md revizyon bloğu (metin) | `docs-writer` → MO yazar | onay | ✅ HAZIR (2026-10-25) |
| T-03 | Hoparlör profili şeması | `T-03: hoparlör profili + güç tavanı tablosunu BCNF'ye normalize et, coremusic_neva ailesi altında migration SQL yaz (Power Governor otomatik mod verisi)` | migration SQL (data-engineer yazar, `shared/database/migrations`) | `data-engineer` | — | ✅ HAZIR (2026-10-25) |
| T-04 | Power Governor API sözleşmesi | `T-04: Power Governor için Audio service (:9741/:9742) route/controller sözleşmesini yaz — watt tavanı set/get, empedans doğrulama, audit log, acil mute` | route + controller + test (PHP) | `backend-architect` | — | ✅ HAZIR (2026-10-25) |
| T-05 | Panel ekran spec'i | `T-05: pro.coremusic.net Power Governor panelinin ekran spec'ini Kalıp D ile yaz — önce ui-design mockup indeksi + screen-spec-template oku, kod YAZMA` | `.ai/ui-design/screens/` ekran spec (metin) | `ui-designer` | **mockup** | ✅ HAZIR (2026-10-25) |
| T-06 | 1000W↔100W güç bütçesi | `T-06: 1000W bas kabını için 100W bas kanalının güç bütçesini hesapla — hoparlör aktif/pasif, sürücü sayısı, PSU bütçesi girdi-sonuç formatında` | hesap raporu → ADR-111 §karar | `hardware-electronics` + MO | **hesap** | [x] KAPANDI 2026-10-25 — kullanıcı: kabin rating 1000 W, sürücü 100 W alt sürüş (çelişki yok) |
| T-07 | 8+1 topoloji kararı | `T-07: 8+1 (500W/kanal) topoloji kararını ver — amplifier/index §4 verim/ısı tablosu + ADR-089 karşılaştırması ile Class D mi Class AB mi, gerekçeli` | ADR-111 §Karar satırı | `architect` + `embedded-engineer` | **onay** | [ ] P0 |
| T-08 | `pro` yüzeyi PRD satırı | `T-08: PRD.md §5'e pro.coremusic.net satırını ekle (35. satır, Durum YOK) + pro.coremusic.net/PRD.md üret` | `PRD.md` (34→35) + yeni PRD | MO | onay | [ ] P1 |
| T-09 | Vault katalog/keyword | `T-09: .ai/index.md ve keys.md'ye product-line-requirements + coding-panel kayıtlarını ekle (katalog + Power Governor/18650/SKU anahtarları)` | index.md + keys.md | MO | — | [ ] P1 |
| T-10 | Kodlama görev paketi | `T-10: T-03/T-04/T-05 çıktılarını al ve Power Governor'ın PHP+JS uygulama görevlerini sırayla kodla (mockup gate'ten sonra)` | `shared/` + `assets/` + `pro/` kodu | `backend-architect` + `ui-designer` + `qa-engineer` | **mockup** | [ ] T-05 sonrası |

---

| T-11 | Aktif hoparlör SKU keşfi | `T-11: aktif hoparlör (amfi+DSP+koruma tek kutu, Power Governor yerleşik) feasibility notu yaz — mevcut SKU hattıyla parasütizyon/ortak parça analizi dahil` | future-roadmap §3 Y1 · `.ai/electronic/amplifier/future-roadmap.md` | `architect` + `audio-hw` | **onay** | [ ] |
| T-12 | Zone watt tavanı | `T-12: Power Governor'ı bölge (zone) bazlı watt tavanları için genişlet — NevaConnect multi-room sözleşmesiyle uyumlu API şeması yaz` | future-roadmap §3 Y2 · T-04 üzerine | `backend-architect` + `embedded-engineer` | — | [ ] |
| T-13 | 18650 SoH / şarj IQ | `T-13: 18650 pil sagligi (SoH) + sarj IQ telemetri spec'i yaz — kullanim deseninden bypass onerisi, pro panelde gosterim` | future-roadmap §3 Y4 | `embedded-engineer` + `ui-designer` | **mockup** | [ ] |
| T-14 | Üretim test hattı | `T-14: AES17/THD+N/SNR otomatik uretim test fixture spec + gate tablosu yaz (3 kosul, ihlal 0 — hardware-electronics §14)` | future-roadmap §3 Y5 · Y4/Y5 | `qa-engineer` + `audio-hw` | **onay** | [ ] |
| T-15 | Speaker fingerprint | `T-15: empedans + hoparlor parmak izi ile otomatik profil keşfi akışını yaz (Governor 2. nesil — ADC ölçümüyle profil teyidi)` | future-roadmap §3 Y3 · T-03 üzerine | `data-engineer` + `embedded-engineer` | — | [ ] |## §2 Bağımlılık Sırası (paralel değilse)

```text
T-01 (ADR-111) ──┬──> T-06 + T-07 kararları ADR'ye girer
T-02 (belge)    ──┴──> T-08 PRD hattı
T-03 (şema)  ─┐
T-04 (API)   ─┼──> T-10 (kod) ──> qa-engineer test/coverage
T-05 (spec)  ─┘        ↑
                       └── mockup gate: .ai/ui-design/ PNG okunmadan UI kodu YASAK
```

## §3 Kablosuz/Pil Cihazları Ortak Kartı

| ID | Görev | Hazır Prompt | Durum |
|---|---|---|---|
| T-PWR | 18650 balans şarj + bypass devresi | `T-PWR: 18650+adaptör bypass/şarj devresini tasarla — boot'ta otomatik şarj YOK, adaptörde ekranda bypass/şarj seçimi, MPN'li BOM + şarj akımı hesabı` | [ ] (MPN/ölçüm → `⚠️ VERIFICATION REQUIRED`) |

---

## §4 Değişiklik Günlüğü

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-25 | 1.0.0 | İlk panel — 11 görev kartı, 5'i paralel agent ile başlatıldı |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-25
**Mode:** Red Team · Human Mode · Truth Mode