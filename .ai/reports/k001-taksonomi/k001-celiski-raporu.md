---
title: "K001 Taksonomi Çelişki Raporu — Gizleme Yok"
type: report
node_id: reports/k001-taksonomi/k001-celiski-raporu
status: active
evidence:
  - "anayasa §7 (hata modeli satır 152-153) — kapsam §7.3 T1/T2'nin atıf yaptığı satır artık BURASI, §10 değil"
  - "anayasa §10 (satır 191-200) — Q1/Q3 açık sorular"
  - "anayasa §12/C10 (satır 250)"
  - "grep: 'yalnız 3 bağlam' / 'ekran.*bağlamı.*AÇILMAZ' — 21 eşleşme (stale citation)"
  - "K000-isletim-sistemi/index.md depends_on: [K002-altyapi]"
validation: "Tüm çelişkiler disk kanıtlı · gizleme yok · zero-hallucination"
open_questions:
  - "Bu çelişkiler Vault Steward kararı olmadan çözülmez — raporlanır"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Taksonomi Çelişki Raporu

> **Kural:** Çelişkiler gizlenmez, saklanmaz. Her satır disk kanıtlıdır.

## §1 Çelişki Matrisi

| # | Çelişki | Kanıt | Etki | Çözüm |
|---|---|---|---|---|
| **B1** | anayasa §12/C10 "3 kademe-3 alt bağlam (audio/hdd/ssd)" — ADR-102 9 bağlam (display·power önceki oturum + cables·network·memory·cpu bu oturum) | anayasa satır 250 | Yapısal (C9-C10) — §12.1: ADR onayı ister | ADR-102 draft + Vault Steward onayı → **4 edit ile PROPOSED işaretlendi** (2026-10-13 ✓) |
| **B2** | kapsam §7.3 T1/T2: "yalnız 3 bağlam açılır" ve "ekran/kablo bağlamı AÇILMAZ" — dayanak "anayasa §10/5 — satır 152,153" artık §7 Hata Modeli satırıdır | anayasa satır 152-153 = §7 hata modeli, §10 = satır 191+ | **Stale citation** — yasak metni ÇÖMELMİŞ (line drift) | kapsam §7.3 düzeltmesi (4 edit #3) |
| **B3** | `architecture/index.md` §6: "K002 hiç yok (ID GAP)" — ama `K002-electroncis/` diskte (16 dosya, 2026-10-13) | K002-electroncis/index.md | K002 ID GAP kapanmış olabilir ama metin revize edilmedi | Vault Steward kararı — **bu oturumda çözülmez** |
| **B4** | anayasa §0 ELI15: "disk, ses kartı, **ekran, kablolar**" — ELI15 5 dalı ima eder, §12/C10 "3" der | anayasa satır 35 vs 250 | İç çelişki (aynı dosya) | ADR-102 + 4 edit ile ELI15 ile hizalanır |
| **B5** | ADR-017/038/062/063/064/089 frozen — değişmez | accepted/ dizini | Kısıt koruması | ✓ Korundu (diff=0 gate) |
| **B6** | `.ai/reports/` dizini önceden YOKTU — K001 raporları için konum gerekiyordu | glob "0 sonuç" | Rapor konumu belirsizdi | Bu oturumda `.ai/reports/k001-taksonomi/` açıldı |
| **B7** | K000'ın kendi `electroncis/` alt klasörü + K002-electroncis ayrı domain — OVERLAP | K000/index.md children | İki "electroncis" sahipliği belirsiz | Vault Steward kararı — raporlandı |
| **C1** | K000/index.md `depends_on: [K002-altyapi]` — diskte `K002-electroncis` (isim uyuşmazlığı) | K000/index.md frontmatter | Bağımlılık zinciri kırık olabilir | Vault Steward kararı |
| **C2** | K002-electroncis/index.md 5 çocuk bildirir, diskte 3 dizin var | K002/index.md vs glob | 2 eksik dal | Vault Steward kararı (rapor-5 §3.2) |
| **C3** | `donanım-devices/` diskte (5. çocuk) ama anayasa §1 + K001/index.md children "4" der | K001/index.md vs glob | Çocuk sayısı stale | Vault Steward onayı ile revize (PROPOSED) |
| **C4** | `display/index.md` "5. alt bağlam" der — 9 bağlam içinde 4. sıradadır (power display'den sonra açıldı) | display/index.md satır 32 | Sıra hatası | Raporlandı, düzeltilmez (dosya subagent'ın) |
| **C5** | anayasa §10/1: "K002 neden yok?" (K001→K003 atlama) — K002-electroncis var | anayasa satır 193 | Açık soru artık geçersiz olabilir | Vault Steward kararı |
| **C6** | "v2 §2.2" atıfları doğrulanamaz — v2 spec dosyası diskte YOK | glob "coremusic-katmanli-mimari-agaci-v2*" = 0 | Anayasa §1'in dayanağı eksik | ⚠️ VERIFICATION REQUIRED (raporlandı) |
| **C7** | `donanaım-type/gpu/` diskte açıldı (paralel Vault Steward oturumu, ADR-102 süreciyle EŞZAMANLI); ilk sayımda 3 dosya — `gpu-ornekler.md` eksikti (index children'ında listeli) | glob 2026-10-13 22:30 (3/4) → 22:55 (4/4) · gpu/index.md Q-GI1 | Dal sayısı 10; ADR-102 kapsamı 9'da kalır | **KISMEN KAPANDI (22:49):** `gpu-ornekler.md` paralel oturumca yazıldı (513 satır) — dosya eksiği kapandı. **Açık kalan:** Q-GI1 — ADR-102 gpu sayımı revizyonu (Vault Steward onayı REQUIRED) |

## §2 Stale Citation Detayı (B2 — en kritik)

`donanaım-type-kapsam.md` §7.3 T1 satırı (yaklaşık satır 135):

> `| T1 | Yalnız 3 bağlam açılır; ekran/kablo bağlamı AÇILMAZ | donanım-anayasa.md §10/5 — satır 152-153 |`

**Gerçek:** anayasa satır 152-153 artık §7 **Hata Modeli** tablosudur
("Kanıt eksikliği | status draft" · "Kapsam ihlali | ADR-062'ye aykırı").
§10 **Açık Sorular** tablosu satır 191-200'dedir ve Q3 (satır 195) der:

> `| 3 | donanaım-type altı 3 bağlam mı, fazlası mı? | genişleme | ⚠️ VERIFICATION REQUIRED |`

Yani: eski metinde bir **YASAK** vardı; anayasa büyüdükçe satır kaydı, "yasak"
satırı hata modeline dönüştü, §10'da ise **açık soru** olarak yeniden belirdi.
"Yasak" artık metinde YOK — yalnızca stale atıf onu hâlâ varmış gibi gösteriyor.
21 grep eşleşmesi bu stale atıfı tekrarlıyor.

## §3 Açıkh Sorular (anayasa §10 — cevaplanmamış)

| # | Soru | Bu oturum etkisi |
|---|---|---|
| Q1 | K002 neden yok? | K002-electroncis var — soru geçersizleşmiş olabilir (Vault Steward) |
| Q3 | 3 bağlam mı fazlası mı? | **ADR-102 ile cevaplandı: 8** (draft — onay bekliyor) |
| Q4 | Envanter: bilinen modeller gerçek mi? | Tüm envanter UNKNOWN (fiziksel ölçüm YOK) |
| Q5 | Ses donanımı envanteri K001 mi K003 mü? | Cevaplanmadı — audio sınırı etkilenmez |
| Q6 | firmware-sürüm uyuşmazlığı kimin işi? | Cevaplanmadı — K005 sınırı |
| Q9 | Band-2 genişlemesi ne zaman ADR'ye girer? | İlgisiz (K-Space) |

**Authority:** Bayram Ali / Vault Steward
