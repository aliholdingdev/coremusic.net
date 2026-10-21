# prompt-maker — P0–P3 Soru Bankası (question-bank)

> **Ne zaman okunur:** §6-3 (Sorular adımı) — soru üretimi ÖNCESİ.
> SKILL.md §5 bu dosyanın özüdür; kategori dalları, hazır cümleler ve iyi/kötü
> örnekler buradadır. Boot'ta okunmaz, yalnız ihtiyaç anında.
>
> **Skill:** prompt-maker · **Updated:** 2026-10-21

---

## §QB.1 Seviye özeti (SKILL.md §5.1 ile aynı — tekrar burada, hızlı bakış)

| Seviye | Tur bütçesi | Cevaplanmazsa |
|:------:|:-----------:|---------------|
| P0 Engelleyici | ≤5 | **ÜRETİM YOK — dur** |
| P1 Kalite belirleyici | ≤3 | Varsayım + bildirim |
| P2 Tercih | ≤2 | En mantıklı + "P2-öneri" etiketi |
| P3 Ertelenebilir | ≤2 | Defter + açık varsayım sayılır |

Toplam: **≤8 soru/tur · maks 3 tur · %95 eşiği.** Aşarsa dur + varsayım listesi.

## §QB.2 İyi soru / kötü soru

| | Kötü (generic — YASAK) | İyi (proje bağlamına göre) |
|---|------------------------|----------------------------|
| Stack | "Hangi framework kullanıyorsun?" | "CoreMusic PHP 8.4 hybrid SPA'da bu endpoint `app/routes/` altında mı olacak, ayrı subdomain mi?" |
| Kapsam | "Neleri kapsasın?" | "Kapsam yalnızca `moderation` modülü; `users` ve `billing` DIŞARIDA mı (ne DEĞİL)?" |
| Çıktı | "Ne format istersin?" | "Çıktı `references/api-contract-flow.md` şablonunda markdown + örnek curl olarak mı?" |
| Güvenlik | "Güvenlik önemli mi?" | "Bu uçta CSRF token zorunlu mu (state-changing ise) — hard mı soft mu?" |
| Teslimat | "Nereye kaydedeyim?" | "Kayıt `.ai/prompts/{date}-{slug}.md` + brain.md kararı — orkestratöre bildirim yeterli mi?" |

**Kalıp:** soru = [proje nesnesi] + [A/B seçeneği] + [hard/soft işareti].
Tek satır, numaralı, tek harfle cevaplanabilir.

## §QB.3 Zorunlu kategori dalları (her turda en az bir P0 bu altıdan beslenir)

### K1 — Girdi / Çıktı

- Girdi kaynağı ne? (A: vault dosyası · B: kullanıcı metni · C: web · D: API)
- Girdi formatı/sheması var mı? Yoksa şema sen mi tanımlarsın (JSON alan+tip)?
- Çıktı yüzeyi: dosya / rapor / kod / prompt — hangisi ve nereye?
- Boş/hatalı girdi davranışı: ne dönmeli (hata kodu, `UNKNOWN`, varsayılan)?

### K2 — Tech Stack

- Hedef platform/sürüm (PHP 8.4 mü, 8.3 mü; MySQL 8.0 mü?).
- Bağımlılık sınırı: yeni paket eklenebilir mi, yoksa stdlib mi (YAGNI)?
- İlgili vault dosyaları hangileri (`@` ile okunacak listeyi ver).

### K3 — Kapsam Sınırı (ne DEĞİL)

- Bu görevin DIŞINDA olanlar? (madde madde en az 1)
- Bitiş kriteri nedir — "bitti" hangi ölçütle kanıtlanır?
- İlişkili ama dokunulmayacak dosyalar (dokunulmaz yüzey listesi).

### K4 — Güvenlik / Yasak

- Hard rules onayı: `strict_types` · Vanilla JS (ADR-001) · OWASP Top 10:2025 ·
  ITCSS + `--cm-*` · PDO parametreli (ADR-002) · `SELECT *` yasak · ADR zorunlu.
- Secret/izin yüzeyi var mı (settings, `.env`, token)?
- Injection yüzeyi: kullanıcı verisi nereye giriyor (talimat vs veri ayrımı)?

### K5 — Kalite Eşiği

- Test/kanıt kapısı: ne çalıştırılınca "geçti" sayılacak (`php -l`, browser test, ölçüm)?
- Rubrik eşiği onayı: toplam ≥85/100 · Security ≥90/100 (SKILL §12.3) — kabul mü?
- Performans/erişilebilirlik zorunlu mu (WCAG 2.2 AA)?

### K6 — Teslimat / Kayıt

- Dosya adı/konumu (`.ai/prompts/{date}-{slug}.md` dışında bir yer mi?).
- Kayıt yetkisi: skill YAZMAZ — orkestratöre bildirim mi, doğrudan kayıt mı (onay)?
- Log satırı gerekiyor mu (`.ai/log.md` append — yine orkestratörde)?

## §QB.4 [UNKNOWN] yönetimi

1. Cevap yoksa bilinmeyen **`UNKNOWN`** yazılır — "muhtemelen/sanırım" YASAK (H001).
2. `UNKNOWN`, P0 ise üretim **durdurulur**; P1/P2 ise varsayım listesine girer
   ve çıktıda `⚠️ VERIFICATION REQUIRED` etiketi taşır.
3. Doğrulanabilir hale gelince (`sources.md` / disk kanıtı) `UNKNOWN` silinir.

## §QB.5 Hard / Soft ayrımı

| | Hard (pazarlık dışı) | Soft (müzakere) |
|---|---|---|
| Ne | Güvenlik, katman kuralları, dokunulmaz yüzey, ADR'ler, yasaklı kalıp | Ton, uzunluk, isimlendirme tercihi, araç seçimi |
| Cevap gelmezse | Varsayılamaz — **P0 soru tekrarlanır**, üretim durur | En mantıklı seçilir + "P2-öneri" etiketi |
| Değişiklik | Kullanıcı aksi açıkça söyleyene kadar kalıcı | Cevaba göre hemen uyarlanır |

## §QB.6 Tur kapanış kontrolü (soru turundan çıkarken)

```text
[ ] Her P0 cevaplandı mı? (biri açık → üretim YOK)
[ ] Çelişki doğrulandı mı? (eski cevap ile yeni cevap tutarsızsa yeniden sor)
[ ] Sorular proje bağlamına göre mi? (generic soru = 0 hedefi)
[ ] Toplam ≤8 soru · ≤3 tur aşıldı mı? (aşıldı → dur + varsayım listesi)
[ ] [UNKNOWN] kalemleri işaretli mi?
[ ] Hard/soft ayrımı yazıldı mı?
```
