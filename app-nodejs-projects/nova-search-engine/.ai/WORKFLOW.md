# WORKFLOW.md — İş Akışı ve Oturum Sürekliliği

> Kaynak: `novasearch-engine-yapılandr-ai.md` talimatları + proje pratiği.

## 1. Oturum başlangıcı (her seferinde, sırasıyla)

1. `.ai/index.md` oku (vault haritası)
2. `.ai/MEMORY.md` oku (öğrenilenler + açık konular)
3. `.ai/log.md` son kayıtları oku (son ne yapıldı)
4. Görevle ilgili `.ai/decisions/` ve `docs/` dosyalarını oku
5. **Vault okunmadan plan/kod YOK**

## 2. Oturum kapanışı

1. `.ai/log.md`'ye append-only kayıt (ne yapıldı, ne öğrenildi)
2. Yeni karar → `.ai/decisions/ADR-NNN-*.md`
3. Kalıcı bilgi → `.ai/MEMORY.md`
4. Değişen doküman → `docs/` + README senkron

## 3. Çalışma kuralları

- **Plan önce:** onaysız büyük değişiklik yok
- **Çelişkide sor:** varsayım uydurma; kullanıcıya tek net soru sor
- **Test gate:** `tsc --noEmit` 0 hata + `bun test` yeşil + canlı smoke
- **Kanıt:** her iddia disk/çalışma kanıtıyla gelir
- **Prompt işleme:** ham promptlar `/prompt-maker` kalıbıyla normalize edilir,
  sonuç `.ai/log.md`'ye kaydedilir (`/vault-sync`)
- **Secret:** anahtar yalnız `.env`; `.md/.log/.json` yasak
- **Commit:** orkestratör atar; mesaj conventional + Türkçe özet

## 4. Değişiklik sonrası kural (deneyle öğrenildi)

Kod değişince **çalışan süreçleri yeniden başlat** — bayat süreç eski
kodla yanıltıcı hata üretir (bkz. log 14:32).

## 5. Kalite kapıları

| Kapı | Ölçüt |
|---|---|
| Tip | `tsc --noEmit` = 0 |
| Test | `bun test` = tümü yeşil |
| Gürültü | `[YOUTUBEJS]`/benzeri çıktı temiz (Log.setLevel ERROR) |
| Sızıntı | `git grep sk-or-` = boş |
| UI | tarayıcıda gerçek akış doğrulaması |
