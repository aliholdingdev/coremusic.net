---
title: "Session Log Template"
type: template
category: session
version: 2.0.1
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-29
date: 2026-08-23
template_id: TPL-SESSION-LOG
risk: "Düşük — salt metin şablon; {PLACEHOLDER} kalırsa yanlış oturum kaydı riski"
reuse_note: "Oturum kapanışında kopyalanır; §3 alanları doldurulur (Guardrail #16)"
---

# Session Log Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

---

## 1. Amaç

CoreMusic agent oturum kaydını standartlaştırmaktır: oturum bilgisi (Session Info), özet, görevler, kararlar, sorunlar, değişen dosyalar, sonraki adımlar, vault güncellemeleri ve notlar alanlarını tek formatta toplar — böylece oturumlar arası devamlılık ve audit trail korunur.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Her oturum (session) kapanışında yazılan Markdown kayıt | Kod/shema değişikliklerinin kendisi |
| Görev, karar, sorun, dosya değişikliği tabloları | ADR metinleri (frozen — ADR-042) |
| Vault Updates kontrol listesi (log.md, MEMORY.md, ADR) | CI/CD pipeline kayıtları |

- **Dosya tipi:** Markdown oturum kaydı
- **Kullanan agent:** Tüm ajanlar (oturum kapanışında); vault-sync/vault güncellemesi Master Orchestrator koordinasyonunda (AGENTS.md §7.7, §14)
- **Guardrail:** #16 (Template Mandatory) — yeni oturum kaydı bu şablondan başlar

## 3. Mimari

Oturum kaydının tam alan yapısı. Not: bu şablonun placeholder'ları **tek süslü parantez** formatındadır (`2026-10-23-neva-engine-neva-player-prd-uretimi-iki-dosya-paralel-8000-s`, `{{...}}` vault formatından farklıdır) ve birebir korunmuştur.

### 3.1 Session Info

| Field | Value |
|-------|-------|
| Session ID | 2026-10-23-neva-engine-neva-player-prd-uretimi-iki-dosya-paralel-8000-s |
| Date | 2026-10-23 |
| Start Time | ⚠️ VERIFICATION REQUIRED (elle girilecek) |
| End Time | 21:21 |
| Duration | ⚠️ VERIFICATION REQUIRED (elle girilecek) |
| Status | completed |

### 3.2 Summary

Kullanici 'start both in parallel' + 'DEVAM ET' ile kapsami onayladi; iki PRD paralel-part stratejisiyle (eng-p01..p42, plr-p01..p42, temp dizin 84 dosya) uretilip G:Coremusic dizinine birlestirildi: ENGINE 8004 ham/6609 dolu/465314 bayt (22 ust baslik, 182 gereksinim/182 TC, FM-E01..E20, U1..U15, TD-01..TD-15, ADR taslaklari T-01..T-04, olcum scriptleri §21.P) · PLAYER 8006 ham/6624 dolu/458307 bayt (16 ust baslik, 215/215, CP-01..CP-08, T-P01..T-P05, E2E S-P01..S-P16, sozlesme karti §21.S). Disk kaniti tabanli duzeltme: neva_session.h 9 C ABI fonksiyonu + enum 0-4 (uydurma 8-fonksiyon/enum-adlari duzeltildi; denetim izi engine C-01..C-09, player P-C01..P-C04). Marker: ENGINE VR 196/oneri 179/UNKNOWN 59 · PLAYER VR 208/oneri 334/UNKNOWN 43. Uretim+olcum raporundan sonra onay sorusuna kullanici 'DEVAM ET' (2026-10-23) → K-6 ONAYLANDI olarak iki PRD'de guncellendi. Kapanis: PRD'ler vault'a kopyalandi (.ai/projects/NevaEngine + .ai/projects/NevaPlayer), session kaydi + MEMORY/TODO/root PRD.md §5.1 + AGENTS §9 + index + log guncellemeleri yapildi; temp parca dosyalari 84/84 temizlendi; hicbir kaynak kod dosyasi degistirilmedi; commit atilmadi.

### 3.3 Tasks

| # | Task | Status | Notes |
|---|------|--------|-------|
| 1 | Neva Engine + Neva Player PRD uretimi (iki dosya paralel, 8000+ satir) + K-6 onayi + vault-sync-post kapanisi | completed | agent: MO (ana oturum - subagent free-limit nedeniyle uretim inline) |
| 2 | — | — | (ikinci gorev verilmedi) |

### 3.4 Decisions

| Decision | Rationale |
|----------|-----------|
| K-6 = ONAYLANDI (2026-10-23, kullanici 'DEVAM ET') PRD'ler vault'a kopyalandi (G: arsiv disinda .ai/projects altinda ikinci kopya) K-1..K-5 faz kapilari AYRI kaldi (K-6 yalniz belge onayi) | Olcum raporu sonrasi onay sorusuna verilen yanit; metin dogrudan alintilandi, yorumlanmadi PRD icindeki SSS-PRD-4 akisi: K-6 onayi → Vault Steward kopyasi + indeks kaydi PRD §20.1 kapi tanimi - faz baslangiclarinda uygulanir |

### 3.5 Issues

| Issue | Resolution |
|-------|------------|
| K-6 onayi sonrasi eski 'onay bekliyor/BEKLIYOR' satirlari Root PRD.md indeksi 28 yuzey tablosu - Neva PRD'leri repo yuzeyi degil | Her iki PRD'de 1:1 satir editleriyle guncellendi; satir sayisi degismedi (8004/8006 olcumleri gecerli) §5.1 harici urun PRD arsivi bolumu eklendi; 28 satirli yuzey tablosuna dokunulmadi |

### 3.6 Files Changed

| File | Action |
|------|--------|
| .ai/SESSIONS/2026-10-23-neva-prd-uretimi.md<br>.ai/projects/NevaEngine/coremusic-neva-engine-prd.md<br>.ai/projects/NevaPlayer/coremusic-neva-player-prd.md<br>PRD.md<br>AGENTS.md<br>.ai/MEMORY.md<br>.ai/TODO.md<br>.ai/index.md<br>.ai/log.md | added<br>added<br>added<br>modified<br>modified<br>modified<br>modified<br>modified<br>modified |

### 3.7 Next Steps

- [ ] M0 faz acilisi: K-1 (architect) + K-5 (urun sahibi) kapilarindan sonra
- [ ] Player kritik aciklar sahiplendirilecek: T-P01 UI toolkit ADR, T-P02 mockup gate, T-P03 ADR-0105 dosya iadesi

### 3.8 Vault Updates

- [ ] log.md updated
- [ ] MEMORY.md updated
- [ ] ADR created/updated

### 3.9 Notes

Additional notes about the session.

---

## 4. Kurallar

Zorunlu / yasak kurallar:

- **Zorunlu:** §3.1-§3.9 alanlarının tamamı bulunur; boş bırakılan alan `—` ile "dolduruldu" gibi gösterilmez, gerçek içerik yazılır.
- **Zorunlu:** tüm `{...}` placeholder'ları (`2026-10-23-neva-engine-neva-player-prd-uretimi-iki-dosya-paralel-8000-s`, `2026-10-23`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `21:21`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `—`, `Neva Engine + Neva Player PRD uretimi (iki dosya paralel, 8000+ satir) + K-6 onayi + vault-sync-post kapanisi`, `—`, `(ikinci gorev verilmedi)`, `K-6 = ONAYLANDI (2026-10-23, kullanici 'DEVAM ET') PRD'ler vault'a kopyalandi (G: arsiv disinda .ai/projects altinda ikinci kopya) K-1..K-5 faz kapilari AYRI kaldi (K-6 yalniz belge onayi)`, `Olcum raporu sonrasi onay sorusuna verilen yanit; metin dogrudan alintilandi, yorumlanmadi PRD icindeki SSS-PRD-4 akisi: K-6 onayi → Vault Steward kopyasi + indeks kaydi PRD §20.1 kapi tanimi - faz baslangiclarinda uygulanir`, `K-6 onayi sonrasi eski 'onay bekliyor/BEKLIYOR' satirlari Root PRD.md indeksi 28 yuzey tablosu - Neva PRD'leri repo yuzeyi degil`, `Her iki PRD'de 1:1 satir editleriyle guncellendi; satir sayisi degismedi (8004/8006 olcumleri gecerli) §5.1 harici urun PRD arsivi bolumu eklendi; 28 satirli yuzey tablosuna dokunulmadi`, `.ai/SESSIONS/2026-10-23-neva-prd-uretimi.md<br>.ai/projects/NevaEngine/coremusic-neva-engine-prd.md<br>.ai/projects/NevaPlayer/coremusic-neva-player-prd.md<br>PRD.md<br>AGENTS.md<br>.ai/MEMORY.md<br>.ai/TODO.md<br>.ai/index.md<br>.ai/log.md`, `added<br>added<br>added<br>modified<br>modified<br>modified<br>modified<br>modified<br>modified`, `M0 faz acilisi: K-1 (architect) + K-5 (urun sahibi) kapilarindan sonra`, `Player kritik aciklar sahiplendirilecek: T-P01 UI toolkit ADR, T-P02 mockup gate, T-P03 ADR-0105 dosya iadesi`) doldurulmadan oturum kapatılamaz.
- **Zorunlu:** §3.8 Vault Updates — oturum sonunda `log.md`'ye giriş eklenir ve `MEMORY.md` session state güncellenir (AGENTS.md §7.7); ADR gerektiğinde oluşturulur/güncellenir.
- **Zorunlu:** `log.md`'ye yazım append-only'dir; geçmiş satıra dokunulmaz (AGENTS.md §25.3).
- **Zorunlu:** §3.6 Files Changed gerçek dosya yollarını ve action'ı (added/modified/deleted) içerir.
- **Yasak:** hassas veri (secret, token, credential) kayda yazılır — `log.md` içinde `[REDACTED]` ile maskele (AGENTS.md §17.3).
- **Uyarı:** doğrulanamayan durum/karar `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {PLACEHOLDER} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/session-log-template.md` (Guardrail #16).
2. **KOPYALA:** oturum kapanışında (vault-sync öncesi, AGENTS.md §14) yeni kayıt oluştur.
3. **{PLACEHOLDER} DOLDUR:** §3.1 Session Info (`2026-10-23-neva-engine-neva-player-prd-uretimi-iki-dosya-paralel-8000-s`, `2026-10-23`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `21:21`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `completed`); §3.2 Summary; §3.3 Tasks; §3.4 Decisions; §3.5 Issues; §3.6 Files Changed; §3.7 Next Steps.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm `{...}` placeholder'lar doldu + §3.8 Vault Updates işaretli.
5. **COMMIT:** kaydı commit et; `log.md` append-only giriş + `MEMORY.md` session state güncelle (AGENTS.md §7.7).

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {PLACEHOLDER}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §3.8 Vault Updates işaretli; log.md append-only; hassas veri `[REDACTED]`

**REFACTOR REPORT:** FILE: session-log-template.md · PURPOSE: Session Log Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../AGENTS.md]] — §7.7 tamamlanma (log.md + MEMORY.md), §14 vault-sync, §17.3 `[REDACTED]`, §25.3 append-only
- `.ai/log.md` · `.ai/MEMORY.md` — §3.8 Vault Updates hedefleri

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
