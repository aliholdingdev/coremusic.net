---
title: "CoreMusic — Agent Tartışma Turu Şablonu (3 Tur / 20 Persona)"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
---

# Agent Tartışma Turu Şablonu (3 Tur / 20 Persona)

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]] · [[adr-nygard-template]] · [[../architecture/adlandirma-kurali]] · [[../WORKFLOW.md]]

> **Kullanım:** Mimari/katman/sınır/sayım kararı gerektiren her konuda **3 turlu, 20 participantlı** tartışma kaydını üretir. Çıktının kendisi bir **karar metni (ADR)** ile biter; ham tur kayıtları bu şablonda özetlenir. Tetikleme ve tur kuralları: [[../WORKFLOW.md]] §6.

---

## §1 Amaç

Tek agent kararının bağlayıcılığını ortak incelemeye açmak: **Tur 1** çeşitlilik (öneri), **Tur 2** dayanak (çapraz eleştiri), **Tur 3** uzlaşma (karar + gerekçe + redler). Kayıt şeffaflığı olmaksızın hiçbir tartışma sonucu vault'a giremez.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tartışma kaydı formatı (3 tur, 20 persona) | ADR metni kendisi (→ `adr-nygard-template`) |
| Uzlaşma/uzlaşmazlık protokolü (🔴 VERIFICATION + üst karar) | Uygulama, kod |
| Karar → ADR bağlantısı ve index kaydı | Persona profilleri tanımları (→ `.ai/.agents/`) |
| Red gerekçesi disiplini | Oylama istatistikleri (yok — uzlaşma esastır) |

## §2 Kapsam

- **Zorunlu olduğu durumlar:** yeni mimari ilke, katman sınırı/ok değişikliği, sayım kuralı, klasör birleşimi/taşıması, çelişen ADR kararı.
- **Katılmayan durumlar:** tek dosyalık satır editi, ADR'siz bug fix, `.workflows` imla düzeltmesi.
- **Çıktılar:** (1) bu şablonla doldurulmuş tartışma kaydı (ilgili kararın ADR'sine eklenebilir ya da `.ai/architecture/adr/` yanında ek dosya olabilir), (2) ADR-NNN (`adr-nygard-template`).

## §3 Mimari — Üç Tur İskeleti

### §3.0 Künye

```markdown
---
title: "Tartışma — <Konu> (3 tur / 20 persona)"
type: debate-record
category: architecture
version: 1.0.0
status: active
authority: "Kayıt — karar mercii: Tur 3 uzlaşması → ADR"
updated: {{DATE}}
---
# Tartışma — <Konu>
**Tarih:** {{DATE}} · **Moderatör:** <agent> · **Konu ID:** deb-YYYYMMDD-NN
**Çıktı hedefi:** ADR-NNN · **Kaynak sayısı:** <n> (dosya/satır/ADR)
```

### §3.1 Tur 1 — Öneri (20/20 zorunlu)

```markdown
## Tur 1 — Öneri
| # | Persona | Rol/çıkar | Öneri (1-3 cümle) | Gerekçe | Kaynak |
|---|---------|-----------|--------------------|---------|--------|
| 1 | persona-01 | <ör. security> | ... | ... | dosya §satır |
| 2 | persona-02 | ... | ... | ... | ... |
 …
| 20 | persona-20 | ... | ... | ... | ... |
```

**Tur 1 kuralları:**
1. 20 satır **eksiksiz** — katılmayan persona `çekirdek` (çekirdek) diye işaretlenir, satır silinmez.
2. Her önerinin **gerekçe + kaynağı** vardır; kaynaksız satır sayılmaz.
3. Uydurma dosya/satır/ADR adı yazılmaz — bulunamayan `⚠️ VERIFICATION REQUIRED`.
4. Öneriler birbirinin kopyası olamaz (kopya ise aynı persona altında birleşir, 20 kişi değil 20 **katılım** sayılır).

### §3.2 Tur 2 — Çapraz Eleştiri (herkes en az 1)

```markdown
## Tur 2 — Çapraz Eleştiri
| # | Eleştirmen | Hedef öneri | Eleştiri türü | Dayanak | Sonuçta öneri |
|---|-----------|-------------|----------------|---------|----------------|
| 1 | persona-03 | persona-07 önerisi | çelişki / risk / maliyet / kanıt eksikliği | matris §2.1 | revize / düşürüldü |
```

**Tur 2 kuralları:**
1. Her persona **başka bir** öneriyi eleştirir (kendi önerisi hariç); içeriksel, kişisel değil.
2. Eleştiri türü şunlardan biri: **çelişki** (vault/ADR ile), **risk**, **maliyet**, **kanıt eksikliği**, **alternatif** (daha iyisi).
3. Dayanak zorunlu: dosya/§satır/ADR — dayanağı olmayan eleştiri geçersiz sayılır.
4. Eleştiri → öneri sahibinin **revizyonu** veya **geri çekilmesi** ile kapanır (Tur 3'e açık madde bırakılmaz).

### §3.3 Tur 3 — Uzlaşma + ADR

```markdown
## Tur 3 — Uzlaşma
**Uzlaşılan karar (tek blok):**
- MADDE 1: ... (bağlayıcı)
- MADDE 2: ... (bağlayıcı)
**Reddedilen alternatifler:**
| Alternatif | REDDEDİLDİ — gerekçe |
|------------|------------------------|
**Uzlaşmazlık:** (varsa) 🔴 VERIFICATION REQUIRED + üst karar mercii
**Çıktı:** ADR-NNN oluşturuldu/güncellendi · index kaydı: ✓/✗
```

**Tur 3 kuralları:**
1. Karar **tek metin** hâlinde; ADR'nin `Decision` bloğuna birebir taşınır.
2. Her red alternatifi `REDDEDİLDİ — gerekçe` satırı ile (boş red yasak).
3. Uzlaşma yoksa karar **verilmez**: 🔴 `VERIFICATION REQUIRED` + üst karar (superstack/product-owner / kullanıcı) beklentisi yazılır.
4. `kaynak` alanına "3 turlu agent tartışması 20 persona" ibaresi ADR'de zorunlu.

## §4 Kurallar

1. **Uydurma yok:** hiçbir turda doğrulanamayan sayı/karar/kişi/kaynak üretilmez.
2. **Dürüst ayrım:** Tur 1/2 taslaktır, bağlayıcı değildir; yalnız Tur 3 bağlayıcıdır.
3. **20 persona sabittir:** azalan katılımda boş satır `çekirdek` ile korunur (20 satır silinmez).
4. **Kayıt bütünlüğü:** 3 tur eksizse kayıt `incomplete` sayılır, ADR'ye kaynak gösterilemez.
5. **REDACTED:** tartışma metnine sır/anahtar/`.env` değeri girmez.
6. **Frontmatter 7 alan** (§3.0).
7. **Dosya adı:** `deb-YYYYMMDD-<slug>.md` (yanında ADR-NNN varsa ADR `kaynak` alanında buna link verir).
8. **Kayıtlar append ruhundadır:** Tur 1/2 satırları sonradan **düzeltilemez/silinemez**; yalnız Tur 3'e giden "revize" sütunu eklenir.

## §5 Workflow

```text
KONU TETİKLE (WORKFLOW §6.1) → Kaydı aç (deb-YYYYMMDD-NN) → Künye
→ TUR 1: 20/20 öneri + gerekçe + kaynak
→ TUR 2: çapraz eleştiri + dayanak + revizyonlar
→ TUR 3: uzlaşma bloğu + red gerekçeleri
→ (uzlaşma yoksa 🔴 VERIFICATION REQUIRED + üst karar — DUR)
→ adr-nygard-template ile ADR-NNN yaz (Decision = Tur 3 bloğu)
→ index.md kaydı (ilgili seri) → log.md append → senkron (session-save + vault-post-update)
→ ilgili matris/adlandirma/README dosyalarına satır edit (varsa) — 0 silme
```

## §6 Doğrulama (checklist)

- [ ] 7 alanlı frontmatter · H1 = `Tartışma — <konu>`
- [ ] Tur 1: 20 satır (çekirdek dahil) — her satırda gerekçe + kaynak
- [ ] Tur 2: her persona en az 1 çapraz eleştiri; dayanaklı; tür etiketli
- [ ] Tur 3: uzlaşma bloğu tek metin · her red `REDDEDİLDİ — gerekçe` · uzlaşmazlık 🔴 işaretli
- [ ] Çıktı ADR'si: `kaynak` = "3 turlu agent tartışması 20 persona" · Decision = Tur 3
- [ ] Uydurma kişi/kaynak yok (20 persona `persona-NN` kod adlı; gerçek isim uydurulmaz)
- [ ] Tur 1/2 satırları silinmedi/düzeltilmedi (append ruhu)
- [ ] index.md kaydı · `log.md` append · senkron çalıştı
- [ ] Bağlantılı vault dosyalarına 0 dosya silme

## §7 Referanslar — Dolu Örnek (kısaltılmış gösterim)

```markdown
# Tartışma — Sayım Birimi ve Hedef (3 tur / 20 persona)
**Tarih:** 2026-09-24 · **Moderatör:** mo · **Konu ID:** deb-20260924-01
**Çıktı hedefi:** ADR-026 · **Kaynak sayısı:** 5 (gate, CLAUDE hedefi, sayım rehberi, matris, adlandirma)

## Tur 1 — Öneri (20/20 — 6 satır gösteriliyor)
| # | Persona | Öneri | Gerekçe | Kaynak |
|---|---------|-------|---------|--------|
| 1 | persona-01 | Birim = dosya | CI gate "script >= 5000" | sprint-3-poc-ci-gate.md |
| 2 | persona-07 | Birim = düğüm | Dosya ≠ içerik derinliği | katman-sayim-rehberi §2.1 |
| … | (persona-03…06, 08…20 çekirdek/diğer satırlar) | … | … | … |

## Tur 2 — Çapraz Eleştiri (3 satır gösteriliyor)
| Eleştirmen | Hedef | Tür | Dayanak | Sonuç |
|-----------|-------|-----|---------|-------|
| persona-12 | persona-01 | kanıt eksikliği | sayım rehberi §2 (332 dosya ≠ 5.000) | öneri düştü |
| persona-04 | persona-07 | risk (şişirme) | CLAUDE "5000+ dosya" baskısı | kanıt zorunluluğu eklendi |
| persona-15 | persona-02 | alternatif | gate rakamı sabit | 5000'de uzlaşıldı |

## Tur 3 — Uzlaşma
- MADDE 1: Sayım birimi DÜĞÜM'dür; dosya/bileşen değildir.
- MADDE 2: Kabul kriteri toplam düğüm ≥ 5000 (gate rakamı sabit).
- MADDE 3: 4. seviye yalnız 3 kanıt türüyle; kanıtsız düğüm sayılmaz.
| Reddedilen alternatif | Gerekçe |
|-----------------------|---------|
| A) Birim = dosya | Metrik kirlenir, kopya enflasyonu |
| B) Hedef 1000 | Gate reddedilmiş olur |
| C) Hedef 7500 | Üst tavan 6.200'ün üstü, şişirme |
**Uzlaşmazlık:** yok · **Çıktı:** ADR-026 ✓ · index: ✓
```

*(Örnek, mevcut ADR-026 tartışmasının gösterim amaçlı kısaltmasıdır; bağlayıcı kayıt ADR metnidir. 20 satırın tamamı gerçek kayıtta eksiksizdir.)*

---

**Registry notu:** Bu şablon `.ai/.templates/index.md`'ye kayıtlıdır; şablonsuz tartışma kaydı üretilmez (Guardrail #16).

*CoreMusic Agent Tartışma Turu Şablonu v1.0.0 — Authority: Bayram Ali / Vault Steward — 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
