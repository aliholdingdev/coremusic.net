# Workflow & Dispatch — Orchestration Engine Notları

> **Kaynak:** `.opencode/skills/orchestration/SKILL.md` (v1.1, 2026-09-29) benzersiz içeriği
> bu dosyaya birleştirildi (2026-10-07, orchestration merge). Ana skill:
> [SKILL.md](../SKILL.md).

## 1. Kimlik & Tek Soru

Bu katman, görevin **kim tarafından** yapılacağını, **ne zaman durulacağını** ve
**nasıl üretileceğini** tanımlar. Tek soru: *"Bu görevi kim yapar, ne zaman dur,
nasıl üretirsin?"*

Agent routing tablosu, task-analysis pipeline ve validation pipeline → [SKILL.md](../SKILL.md) §3-§7.

## 2. Risk Sınıflandırması (5 Seviye)

| Seviye | Tanım | Onay | Örnek |
|--------|-------|------|-------|
| **L0 — AUTO** | Güvenli, geri dönüşü kolay | Yok | Dosya okuma, grep, search |
| **L1 — LOW** | Minimal etki | Self-review | README, ADR, typo fix |
| **L2 — MEDIUM** | Kısmi etki | Agent review | Yeni dosya, endpoint, component |
| **L3 — HIGH** | Geniş etki | **İnsan onayı** | Auth, DB schema, security, deploy |
| **L4 — CRITICAL** | Geri dönüşü olmayan | **Tam denetim** | Production data, encryption |

### R.A.I.L. Karar Matrisi

| Geri Dönüş | Belirsizlik | Etki | Gecikme | → Kontrol Noktası |
|------------|-------------|------|---------|-------------------|
| Kolay | Düşük | Düşük | Yüksek | Otomatik |
| Zor | Düşük | Yüksek | Düşük | **Approval** |
| Kolay | Yüksek | Orta | Orta | **Review** |
| Zor | Yüksek | Yüksek | Düşük | **Approval + Escalation** |

> Tam R.A.I.L. tanımı ve akış diyagramı: `human-mode` skill (global).

## 3. HITL Kontrol Noktaları (4 Nokta)

| # | Nokta | Ne Zaman | Fail-Safe |
|---|-------|----------|-----------|
| 1 | **Approval** | Geri dönüşü olmayan işlem öncesi | timeout → DENY |
| 2 | **Review** | Çıktı kalite kontrolü | timeout → auto-approve |
| 3 | **Escalation** | Karar verilemiyor | timeout → VERIFICATION REQUIRED |
| 4 | **Interrupt** | Aktif duraklama | timeout → continue + warning |

### Onay Formatı

```markdown
## HITL ONAY İSTEĞİ
**Agent:** [agent-id]
**Risk:** HIGH / CRITICAL
**İşlem:** [Ne yapılacak]
**Etki:** [Etkilenen dosyalar/servisler]
**Seçenekler:**
- [OK] ONAYLA — Uygula
- ✏️ REVİZE ET — [Değişiklik ile onayla]
- [X] REDDET — İptal
```

## 4. Escalation Matrisi

| Seviye | Tanım | Timeout | Fail-Safe |
|--------|-------|---------|-----------|
| L1 | Domain Lead | 30s | VERIFICATION REQUIRED |
| L2 | Tech Lead | 60s | ESCALATION REPORT |
| L3 | Arch Lead | 120s | FULL AUDIT |
| L4 | İnsan | 300s | ACTION DENIED |

## 5. Skill Oluşturma Protokolü

```
[1] GEREKSİNİM → ne yapacak, hangi domain
[2] ARAŞTIRMA → web'den doğrula (min 2 kaynak)
[3] YAPI → v3.0 format (claude-skill-v3): SKILL.md ≤~250 satır, derinlik references/ + examples/
[4] GÜVENLİK → OWASP, credential yok, hardcoded secret yok
[5] DOĞRULA → trigger'lar çalışıyor mu? orphan referans yok mu?
```

> ⚠️ Format otoritesi: `.claude/skills/skill-maker/` (v3.0 N1-N10). Kaynaktaki eski
> `title/type/version` yaml şablonu geçersizdir — yeni skill'lerde yalnız
> `name/description/license/metadata` kök anahtarı kullanılır.

### Oluşturmada Yasaklar

- SQL komut referansı yazma (MySQL docs'tan okunur)
- CSS/HTML kod örneği yazma (vault'ta)
- Agent-specific checklist yazma (her agent kendi domain'ini bilir)
- Anti-pattern katalogu yazma (H001-H039'da zaten var)
- 1000+ satır skill yazma (sert üst sınır 2000; hedef ≤250)

## 6. CoreMusic Hard Kurallar (Dispatch Sırasında Denetlenir)

| Kural | Kaynak |
|-------|--------|
| PHP 8.4 strict_types=1 | ADR |
| Vanilla JS (framework yasak) | ADR-001 |
| PDO only (ORM yasak) | ADR-002 |
| csrf_token (NOT _csrf_token) | ADR-010 |
| Port 81 = music.coremusic.net | ADR-042 |
| Middleware sırası değişmez | ADR-010/011/012 |
| SELECT * yasak | H021 |
| innerHTML yasak | XSS riski |
| var yasak (let/const) | ES6+ |
| Zero Code Before Plan | Guardrail #1 |

## 7. Quick Reference

| İhtiyaç | Aksiyon |
|---------|---------|
| Görev dağıt | SKILL.md §3 routing tablosu |
| Onay gerekli mi? | §2 R.A.I.L. matrisi (bu dosya) |
| Ne zaman escalation? | §4 escalation matrisi (bu dosya) |
| Yeni skill oluştur | §5 protokolü (bu dosya) |
| Yasaklar neler? | §6 hard kurallar (bu dosya) |
| SWARM akışı | SKILL.md "SWARM AKIŞ DİYAGRAMI" |

---
*CoreMusic reference v3.0 — agent-orchestrator/references/workflow-dispatch.md — Updated: 2026-10-07*