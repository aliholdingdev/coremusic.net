---
description: Projenin .ai vault'unu referans şablona göre kur: dizin ağacı + boot/index stub'ları oluşturur, sonra prompt-maker akışına geç
---

Bu komut iki şeyi sırayla yapar. Henüz kod yazma, salt vault kurulumu + prompt ön işleme.

## [1] Vault oluştur — `vault` tool

`vault` çağır: `action=create-vault`.

Bu tek çağrı şunları üretir (var olan dosyalar KORUNUR, tekrar çağırma güvenlidir):

- **Dizin ağacı** (referans: `C:\www\coremusic.net\.ai`): `.agents` · `.decisions/{accepted,draft,rejected}` · `.personas` · `.rules` · `.templates` · `architecture` · `checklists` · `ecosystem` · `knowledge` · `prompts` · `reports` · `scripts` · `spec` · `ui-design/{flow,prompt,reference,screens,tokens}`
- **Boot + navigasyon stub'ları** (8 alanlı frontmatter, G4): `index.md` · `keys.md` · `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `ROLE.md` · `brain.md` · `engine.md` · `checklists/todos.md` · `spec/index.md` · `.rules/index.md` · `.decisions/{adr-index,index}.md` · `.templates/index.md` · `.agents/AGENTS.md` · `architecture/index.md` · `reports/index.md` · `knowledge/index.md`
- **Oturum durumu stub'ları**: `MEMORY.md` · `checklists/todos.md` · `log.md`

Çıktıyı kullanıcıya özetle: kaç dizin, kaç dosya oluşturuldu, kaçı zaten vardı.

## [2] Prompt Maker akışı — `promptmaker` tool

1. `promptmaker` çağır: `action=gather` (root md paketi).
2. `promptmaker` çağır: `action=vault` (vault durumu + index dosyaları; ilk kurulumdan sonra dolu olmalı).
3. **Tek mesajda 100 proje-özgü soru listele** (generic soru seti yasak) → cevapları bekle → `Netlik: NN%` yaz.
4. `promptmaker` çağır: `action=assemble` — onay beklemeden, cevaplar gelir gelmez.
5. Vault'a düşülecek notlar için `vault` tool: `action=read` / `action=write` / `action=create`.

## Yasaklar

- Hardcoded provider/model yazma.
- Oluşturulmayan dosyayı `vaultRefs`'e yazma (G5).
- İlk aşamada uygulama kodu üretme.
