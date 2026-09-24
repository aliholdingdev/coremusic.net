---
title: "CoreMusic — Nygard ADR Şablonu (Context / Decision / Consequences)"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
---

# Nygard ADR Şablonu (ADR-NNN)

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]] · [[adr-template]] · [[adr-index]] · [[../architecture/adlandirma-kurali]] · [[agent-tartisma-turu-template]]

> **Kullanım:** Michael Nygard klasik ADR biçimi — **Status / Context / Decision / Consequences** çekirdeği. CoreMusic'te iki ADR serisi için de kullanılır: `.ai/.decisions/accepted/` (karar serisi, yeni numara ≥ ADR-090) ve `.ai/architecture/adr/` (mimari seri, kendi numara uzayı — son: ADR-026). **İki seri kasıtlı ayrıdır; birleştirme REDDEDİLDİ (ADR-026 §3.4).**

---

## §1 Amaç

Kararı **bağlam + sonuç** ile ikna eder; "ne yaptık" değil "o anda neden böyle karar verdik" sorusunu geleceğe bırakır. Nygard çekirdeği kısa kalır (bu şablon 100-250 satır bandındadır); CoreMusic zorunlu alanları (frontmatter 7 alan, kaynak, gerekçe/alternatif disiplini) çekirdeğe örtülür.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Yeni mimari/engineering karar metni | Mimari kılavuz/SSOT dosyaları (adlandirma, matris…) |
| Alternatif red gerekçeleri (zorunlu) | ADR indeksi güncelleme (→ `adr-creation.md` Adım 6) |
| Seri/numara seçimi (karar vs mimari) | Tartışma ham kaydı (→ `agent-tartisma-turu-template`) |
| Sonuçlar (olumlu/olumsuz/nötr) | Uygulama kodu |

## §2 Kapsam

- **Dosya adı:** karar serisi `adr-NNN-kisa-slug.md` (küçük harf) · mimari seri `ADR-NNN-kisa-slug.md` (büyük harf)
- **Konum seçimi:**

| Karar tipi | Konum | Numara |
|------------|-------|--------|
| Proje/engineering kararı (csrf, router, cache…) | `.ai/.decisions/accepted/` | `.decisions/index.md`'den; **≥ ADR-090** (frozen 001-037 immutable; active son 089, draft yok) |
| Katman/klasör/sınır/sayım kararı | `.ai/architecture/adr/` | kendi uzayda ardışık (son: ADR-026) |
| Karar verilemedi | `.ai/.decisions/draft/` | index'teki draft (yok — son draft ADR-089 2026-09-24'te accepted oldu) |

- **Karar kaynağı:** 3 tur / 20 persona tartışması ise `kaynak` alanına yazılır (ham kayıt: `agent-tartisma-turu-template`).

## §3 Mimari — Nygard Çekirdeği (silinemez sıralı 4 blok)

### §3.1 Frontmatter (7 zorunlu alan + ADR alanları)

```markdown
---
title: "ADR-NNN — <Karar Başlığı>"
type: adr
category: architecture | security | database | infrastructure | policy
version: 1.0.0
status: proposed | accepted | frozen | deprecated
authority: "Karar metninin kendisi (Guardrail #16 adr-nygard-template)"
updated: {{DATE}}
# --- ADR'ye özgü alanlar ---
id: "NNN"
date: "{{DATE}}"
deciders: "<kimler> — 3 turlu agent tartışması 20 persona | Principal Architect | ..."
tags: ["katman", "sayim", "..."]
kaynak: "3 turlu agent tartışması 20 persona"   # değilse: <karar mercii>
---
```

### §3.2 Nygard Blokları

```markdown
# ADR-NNN — <Başlık>

**Durum:** {{durum}} · **Tarih:** {{DATE}}
**Konum:** <seri yolu> (ayrı seri — numara notu: <çakışma varsa slug farkı>)
**İlgili ADR'ler:** [[ADR-...]] · [[...]]

## 1. Status (Durum)
Önerildi → İncelendi → Kabul edildi → Donduruldu (frozen)
(deprecated için zorunlu: yerine geçen ADR + migration planı + onay)

## 2. Context (Bağlam)
Ne değişti, hangi güçler çarpışıyor? Hangi kısıtlar var (bütçe, süre,
teknik borç, gate)? Kaynak göster (dosya/satır/ADR). **Tahminler tahmin
diye işaretlenir; ölçüm ile karıştırılmaz.**

## 3. Decision (Karar)
Tek net cümle + zorunlu maddeler ("X YAPILIR", "Z YASAKTIR").
Belirsizlik varsa: ⚠️ VERIFICATION REQUIRED açıkça yazılır.

## 4. Consequences (Sonuçlar)
| Tür | Sonuç |
|-----|-------|
| Olumlu | ... |
| Olumsuz (kabullenilen) | ... |
| Nötr / geçiş | ... |
```

### §3.3 CoreMusic Zorunlu Ekleri (çekirdeğe bitişik)

| Bölüm | Neden zorunlu |
|-------|----------------|
| **Alternatifler** (her biri: öneri → gerekçe → **REDDEDİLDİ — neden**) | Karar sürecinin şeffaflığı (ADR-026 §5 örneği: A/B/C/D) |
| **İlgili Kararlar** | Wiki-link'ler; seri çakışması varsa slug notu |
| **Kabul Kriteri** (ölçülebilir, varsa sayısal eşik) | Doğrulanabilirlik (ör. "script >= 5000") |
| **Yasaklar** (karardan çıkan) | İhlal denetimi |

## §4 Kurallar

1. **Frozen ADR-001…037 değiştirilemez** — okunur, referans edilir.
2. **Numara yeniden kullanılmaz**; iki seride aynı numara farklı slug olabilir → her atıfta **slug + konum** yazılır.
3. **Birleştirme yasak** (ADR-026 §3.4): `.decisions` + `architecture/adr` tek seriye indirilemez; isteyen üst karara (superstack/product-owner) gider.
4. **Kanıtsız karar yok:** Context içindeki her sayı/iddia kaynağa bağlı; doğrulanamayan `⚠️ VERIFICATION REQUIRED`.
5. **Uydurma ADR yok:** yeni numara disk/index'ten doğrulanır.
6. **Frontmatter 7 alan** (§3.1) — eksik ise inceleme Adımı geçilmez.
7. **Dil/uzunluk:** Nygard çekirdeği kısa (Status+Context+Decision+Consequences ≈ 60-120 satır); ekler ayrıntıyı taşır.
8. **REDACTED:** kararda sır/anahtar geçemez.

## §5 Workflow

```text
1. Kararı belirle (tip + seri + numara)          → adr-creation.md Adım 1 (15 dk)
2. Araştırma + kaynak topla ( Context için )     → Adım 2 (30 dk); kanıtsız iddia yok
3. Gerekirse 3 tur / 20 persona tartışması       → agent-tartisma-turu-template
   (Tur1 öneri → Tur2 çapraz eleştiri → Tur3 uzlaşma+ADR)
4. Bu şablonu kopyala, çekirdeği doldur          → Adım 3 (1 saat)
5. İnceleme: cross-ref + hallüsinasyon + §6      → Adım 4 (30 dk)
6. Onay matrisi: mimari→Principal Architect ·    → Adım 5
   güvenlik→Security · veri→Data · API→Backend ·
   dağıtım→DevOps · donanım→Embedded · kritik→kullanıcı
7. Kaydet + index/keys/brain güncelle + log append + senkron → Adım 6
```

**Yaşam döngüsü:** `proposed → (review) → accepted → frozen`; `deprecated` = replacement ADR + migration + onay.

## §6 Doğrulama (checklist)

- [ ] 7 alanlı frontmatter + `id`, `date`, `deciders`, `tags`, `kaynak`
- [ ] Nygard 4 bloğu sırasıyla: Status → Context → Decision → Consequences
- [ ] Alternatifler: her red `REDDEDİLDİ — gerekçe` satırlı (boş red yok)
- [ ] Context'teki her sayı/sağlam iddia kaynaklı; doğrulanamayan etiketli
- [ ] Decision maddeleri ölçülür/etestir (kabul kriteri yazıldı)
- [ ] Seri + numara doğru; slug çakışması varsa "ayrı seri" notu eklendi
- [ ] Frozen 001-037'ye dokunulmadı · iki seri birleştirilmedi
- [ ] Wiki-link'ler `[[relative/path]]`, hedefler diskte (kırık 0)
- [ ] index.md (ilgili seri) kaydedildi · `log.md` append · senkron çalıştı

## §7 Referanslar — Kısa Dolu Örnek (gösterim)

```markdown
---
title: "ADR-025 — K8.2 / K15 Sınırı (Servis Ucu vs Boru Hattı Sahibi)"
type: adr
category: architecture
version: 1.0.0
status: proposed
authority: "Karar metninin kendisi (Guardrail #16 adr-nygard-template)"
updated: 2026-09-24
id: "025"
date: "2026-09-24"
deciders: "3 turlu agent tartışması 20 persona"
tags: ["katman", "servis", "medya"]
kaynak: "3 turlu agent tartışması 20 persona"
---

# ADR-025 — K8.2 / K15 Sınırı

**Durum:** Önerildi · **Tarih:** 2026-09-24
**Konum:** .ai/architecture/adr/ADR-025-k8-2-k15-siniri.md (ayrı seri —
numara notu: .decisions kayıtlarındaki ADR-025-professional-eq-system ile
aynı numara, farklı slug)
**İlgili ADR'ler:** [[ADR-023-hibrit-derinlik]] · [[ADR-026-sayim-birimi-5000]]

## 1. Status
Önerildi — Tur 3 uzlaşması; kabul sonrası frozen'a taşınmaz (revizyon ADR ister).

## 2. Context
Medya işi iki sahipliği karıştırıyor: transcode/boruhattı (altyapı) ile
iş emri/kitaplık (ürün) aynı düğümde toplanırsa K8 ↔ K15 bağımlılığı
matriste tanımsız hale gelir. Kısıt: matriste K15 → K14 dışında ok yok.

## 3. Decision
- **K15**, FFmpeg/transcode/HLS-DASH boru hattının ve protokolün SAHİBİDİR.
- **K8.2** yalnız servis ucudur: job, akış URL'si, kitaplık CRUD.
- K8.2 → K15 **çağrı** oku kullanılır (bağımlılık satırı eklenmez).
- K8.2 asla FFmpeg komut satırını/parametrelerini kuramaz.

## 4. Consequences
| Tür | Sonuç |
|-----|-------|
| Olumlu | Matris sadeleşir; K15 → K14 tek hedefi korunur |
| Olumsuz | Servis ekibi boruhattı ayrıntısını dosyalarında göremez (doküman köprüsü gerekir) |
| Nötr | Her iki README'de birebir aynı sınır cümlesi yazılacak |

## Alternatifler
| Öneri | Gerekçe | Sonuç |
|-------|---------|-------|
| K8.2 sahip olsun | Boru hattı ürün sprintlerine kilitlenir | **REDDEDİLDİ** — K15 → K8 bağımlılığı doğar, matriste yok |
| Ortak üçüncü düğüm (K8.3/K15.9) | İki sahiplik de bulanıklaşır | **REDDEDİLDİ** — tanımsız ok, sayım şişmesi |

## Kabul Kriteri
Her iki README'de sınır cümlesi birebir aynı; matriste yeni ok yok (denetim 0 ihlal).
```

*(Örnek mevcut ADR-025'in gösterim amaçlı kısaltılmış Nygard uyarlamasıdır; bağlayıcı metin diskteki dosyadır.)*

---

**Registry notu:** Bu şablon `.ai/.templates/index.md`'ye kayıtlıdır; şablonsuz ADR üretilmez (Guardrail #16). İndeks/atıf navigasyonu: `adr-index`.

*CoreMusic Nygard ADR Şablonu v1.0.0 — Authority: Bayram Ali / Vault Steward — 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
