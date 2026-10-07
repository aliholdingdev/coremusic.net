# Skill Kullanım Kuralı (Skill Usage Rule)

> **Kaynak (SSOT):** kök `CLAUDE.md` §Skill Usage Mandate. Bu dosya yalnızca
> genişletilmiş örnektir; çelişki durumunda kök `CLAUDE.md` geçerlidir.

## Kural

1. Her görev başlangıcında **Skill Registry** (kök `CLAUDE.md` §Skill Registry) +
   harness available-skills listesi taranır, eşleşme **kontrol edilir**.
2. Görev bir skill'in `"Use when..."` tanımıyla eşleşiyorsa: **İLK işlem**
   `Skill` tool ile o skill'i yüklemektir. Eşleşme varken skill'siz işlem
   başlatmak **YASAKTIR**.
3. Birden fazla eşleşen varsa **en dar (domain-specific)** olan önce yüklenir.
4. **İstisna:** (a) kullanıcı açıkça "skill kullanma" derse; (b) görev skill'lerle
   ilgisizse (sohbet, aritmetik). Belirsizse 1 kısa soru sorulur.
5. Yetenek yoksa kopyala-yapıştır **YAPILMAZ**; `skill-maker` ile skill
   **ÜRETİLİR** (Guardrail #16 + Claude Skill v3.0 şeması).
6. Bu kural `.claude/settings.json` → `UserPromptSubmit` hook'u
   (`.claude/hooks/skill-mandate.cjs`) tarafından her prompt'ta hatırlatılır.

## Çatışma yorumu

Anti-overthink'teki "gereksiz skill" ibaresi = **gereksiz skill ÜRETME**
yasaklıdır; eşleşen skill'i **KULLANMAK** bu mandate ile çelişmez, aksine
zorunludur.

## Doğrulayıcı

```bash
node .claude/hooks/skill-mandate.cjs --check   # exit 0 = v3.0 format temiz
```