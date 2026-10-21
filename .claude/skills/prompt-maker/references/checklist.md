# prompt-maker — Kalite Kapısı (Quality Gate)

> **Kullanım:** Yapılandırılmış prompt / işlenmiş paket teslim edilmeden önce çalıştır.
> Her madde `PASS` / `BLOCK` / `N/A`. **Tek `BLOCK` = teslim YOK.**
> Çıktı biçimi: `GATE: PASS|BLOCK` + maddeler + kanıt (satır no / bölüm no).
> Kalıp: `skill-maker/references/checklist.md` (N1-N10) + bu skill'in N-özdenetimi (§12.5).

**Skill:** prompt-maker · **Format:** Claude Skill v3.0 (N1-N10) · **Updated:** 2026-10-21

---

## A. SKILL.md Biçim Kapıları (N1-N10 — teslimde bir kez)

| # | Madde | Kanıt |
|---|-------|-------|
| A1 (N1) | Frontmatter yalnız `name/description/license/metadata` | head |
| A2 (N2) | description "Use when…" · ≤1024 · tetikleyiciler ('prompt oluştur', 'prompt yaz', 'system prompt', 'steering yaz', 'hook yaz', 'kural seti oluştur', 'MASTER PROMPT', 'cursor rules', 'context engineering', 'PICCO', 'prompt template') | head |
| A3 (N3) | SKILL.md 500–2000 · dizin ≥1000 (`(Get-Content).Count`) | ölçüm |
| A4 (N4) | Her `references/` + `examples/` SKILL.md'den linkli — **orphan=0** | link taraması |
| A5 (N5) | `examples/full-cycle-raw-to-structured.md` tam döngü (ham → soru turu → yapılandırılmış prompt) | dosya |
| A6 (N6) | `name` = klasör 1:1 (`prompt-maker`) | head |
| A7 (N8) | `CLAUDE.md` sidecar YOK | dosya yok |
| A8 (N9) | Footer = `metadata.version` + `Updated: 2026-10-21` senkron | tail |
| A9 (N10) | Kök `changelog[]` yok · sürüm kaydı sürümle senkron | head |
| A10 | UTF-8 BOM yok · mojibake yok | bayt okuma |

## B — Akış Kapıları (SKILL.md §3 — sıra zorunlu)

```text
[ ] Ham prompt geldi → İLK işlem Questions Modu (soru sorma / başa atlama YASAK)
[ ] P0 → P1 → P2 → P3 sırası korundu; P0'sız üretim YOK
[ ] Tur bütçesi: ≤8 soru/tur · maks 3 tur · %95 eşiği (aşarsa DUR + varsayım listesi)
[ ] Çıktı = 8 bölüm (eli10 → görev dökümü) — sıra değiştirilmedi (§3.1)
[ ] Prompt Cevaplarla İşlendi satırı: dil + görev sayısı yazıldı
```

## C — Prompt İçerik Kapıları (23 bölüm mimarisi + PICCO)

```text
[ ] Talimat ↔ veri ayrımı açık (injection yüzeyi: kullanıcı verisi TALİMAT olarak yorumlanamaz)
[ ] PICCO elementleri yerli yerinde (Context / Input / Constraints / Personality / Output)
[ ] Çelişen kural varsa §4.2 öncelik sırasıyla çözüldü (çelişki gizlenmedi)
[ ] [UNKNOWN] kalemleri işaretli · doğrulanmayan iddia "VERIFICATION REQUIRED"
[ ] Zero-hallucination: var olmayan API/sürüm/MPN/URL/fiyat yazılmadı
[ ] Somut çıktı şeması var (alan adları, tipler, zorunlu alanlar)
[ ] Edge case / boş-hatalı girdi davranışı tanımlı
```

## D — Kalite Rubriği (§12.3 — ağırlıklı)

```text
[ ] Rubrik çalıştırıldı: 8 kategori × ağırlık → puanlar yazıldı
[ ] Toplam ≥85/100 (alt = BLOCK)
[ ] Security ≥90/100 (alt = BLOCK — ağırlık 20, zorunlu eşik)
[ ] Her düşük puanlı kategoriye gerekçe + düzeltme yazıldı
```

## E — Onay Kapıları (H0 + HITL)

```text
[ ] 23-bölüm mimarisi / rubrik / gate ilk kez uygulanıyorsa → H0 kullanıcı onayı alındı
[ ] Yıkıcı/görsel üretim öncesi açık onay var (ORTOK ÇEKİRDEK)
[ ] Vault'a YAZMA yok — kayıt yetkisi orkestratörde (skill yalnız bildirir)
```

## F — Yasaklar (bu skill'in kendisi)

```text
[ ] `git commit` ATILMADI (orkestratöre ait)
[ ] `.ai/` vault'a YAZMA YOK (salt okunur; hidden: true)
[ ] Secret/anahtar hiçbir `.md`/`.json`/`.log`'a yazılmadı
[ ] "soru sormadan üretim" YOK — P0 boşluğuna varsayım doldurulmadı
[ ] Generic/boş soru sorulmadı (her soru proje bağlamına göre — §QB.2 kalıbı)
```

---

## Çıktı şablonu

```
GATE: PASS|BLOCK
- A: 10/10 (N3 ölçüm: SKILL=515, toplam=…)
- B: PASS (5/5) · C: BLOCK — C.4: [UNKNOWN] işaretsiz, bölüm 6
- D: 87/100 · Security 92/100 → PASS
- E/F: PASS
Kanıt: bölüm no / dosya:satır
```
