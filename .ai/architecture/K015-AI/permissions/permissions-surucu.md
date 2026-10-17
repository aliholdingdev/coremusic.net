---
node_id: permissions-surucu
node_type: module
status: active
parent_id: permissions
children: []
depends_on: [permissions-anayasa]
evidence:
  - ".ai/AGENTS.md §10 + §12 (escalation + lock yetkisi, okundu 2026-10-17)"
  - "root AGENTS.md §16.4-§16.5 + .ai/CLAUDE.md guardrail'ları"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# permissions-surucu - Ajan Yetki Sürümü

> **Kapsam:** Bir görev geldiğinde ajanın gerçekten çalıştığı yetki adımları — hedef dosya
> erişim kontrolü, beyazliste/salt-okunur kararı, yazım izni, kapı ve log. İhlal tespitinde
> DUR → rapor → escalation zinciri, lock kırma yetkisi, secret maskeleme ve L1→L2→L3→İnsan
> merdiveni. **Kural koyar değil, tarif eder:** bağlayıcı kurallar `Dosya: .ai/AGENTS.md`
> (§8 öncelik · §10 escalation · §12 context lock · §17-§18 uyarılar) ve `.ai/CLAUDE.md §7`
> (16 Hard Guardrails) içindedir. Bu dosya o kuralların **kullanım kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile diskten ölçülür — §19-§21).

## §1. ELI15 — Bu sürücü ne işe yarar

Her ajan yazmadan önce üç kapıdan geçer: (1) **bu dosyaya dokunabilir miyim?** — beyazliste
(`.ai/AGENTS.md §5` dosya tipi sahipliği + kök `AGENTS.md §16.4` dokunulmaz yüzeyler),
(2) **bu dosya kilitli mi?** — context lock (`§12`), (3) **bu satırda gizli veri var mı?** —
REDACTED maskeleme (`§17/3`). Kapıdan geçemeyen görev **DUR** olur; DUR, sessiz devam
demek değildir — rapor + escalation zinciri (`§10`) başlar. Sürücü bu kapıların **sırasını,
karar tablolarını ve ihlal davranışını** tarif eder.

> **eli10:** "Yazmadan önce izin al, kilitliyse bekle, gizliyi maskeyle, takılırsan yukarı
> sor" — dört cümlede tüm sürücü.
>
> **eli15:** Yetki bugün **elle** uygulanır (ajana boot kuralları + hook katmanı hatırlatır);
> merkezi bir ACL motoru **kodlanmamıştır** — `.claude/hooks/ai-risk-gate.cjs` kapısı
> (`Dosya: .claude/settings.json · Satır: 13-24`) çalışma anında tetiklenen tek otomatik
> katmandır. Bu yüzden sürucünün §4 akışı otomasyonu değil, **bugünün elle yetki kapısını**
> anlatır; otomasyon genişlemesi §13'te işaretlidir.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Görev başı yetki kontrol akışı A-G (§4-§6) | Dosya içeriği kuralları (o dosyanın işidir) |
| İhlal → DUR → rapor → escalation zinciri (§8-§9) | ADR metinleri (frozen — yalnız okunur) |
| Lock yetkisi + CRITICAL override (§10-§11) | `settings.json` hook kodunun kendisi (.cjs — yalnız okuma) |
| Secret maskeleme / REDACTED (§12-§13) | `.env` değerlerinin okunması (**hiçbir durumda**) |
| Escalation L1→L2→L3→İnsan + retry (§14-§16) | Commit atma yetkisi (§17: **orkestratöre ait**) |
| PowerShell doğrulama kalıpları + illüstratif bloklar (§18-§23) | Domain kodu değişikliği (bu dosya **yalnız .md üretir**) |

- **Kullananlar:** görev başına yazım yapan her ajan · MO (lock kırma + escalation sonu) ·
  Security Engineer (maskeleme denetimi) · Vault Steward (denetim).
- **Ön koşul:** boot zinciri okunmuş olmalı (`.ai/CLAUDE.md §16` — Guardrail #2 Vault First;
  `Dosya: .ai/CLAUDE.md · Satır: 521`).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; künyesiz iddia yazılmaz.

## §3. Yetki Kaynaklarının Hiyerarşisi (çelişkide kim kazanır)

| Kademe | Kaynak | Ne verir | Kanıt |
|---|---|---|---|
| **Y1** | `.ai/CLAUDE.md §7` (16 Hard Guardrails) | Anayasal yasaklar + ihlal sonucu | `Dosya: .ai/CLAUDE.md · Satır: 516-535` |
| **Y2** | `.ai/AGENTS.md §5 §8 §10 §12 §17-§18` | Domain sahipliği, öncelik, escalation, lock, uyarılar | `Dosya: .ai/AGENTS.md · Satır: 95-109, 316-323, 371-399, 439-470, 195-225` |
| **Y3** | Kök `AGENTS.md §16.4-§16.5` | Dokunulmaz yüzeyler + subagent commit yasağı | `Dosya: kök AGENTS.md · §16 "Değiştirilemez Kullanıcı Kuralları"` |
| **Y4** | `.claude/AGENTS.md §3.3` | `.claude/` klasöründeki 8 yetki sınırı | `Dosya: .claude/AGENTS.md · §3.3` |
| **Y5** | `.claude/settings.json` hook katmanı | Çalışma anı otomatik kapı (Pre/PostToolUse) | `Dosya: .claude/settings.json · Satır: 13-38` |

**Çelişkide kademe kazanır:** Y1 > Y2 > Y3 > Y4 > Y5 (SSOT sırası `.ai/CLAUDE.md §2.1` ile
uyumludur: `CLAUDE > AGENTS > WORKFLOW > brain > index > templates`).
**Karar verilemiyorsa:** `⚠️ VERIFICATION REQUIRED` + DUR — **tahmin edilerek yazım yapılmaz**.

## §4. Görev Başı Yetki Kontrol Akışı A-G

```text
[A] Görev gelir → keyword/domain çıkar (§.ai/AGENTS.md §6 routing)
[B] Hedef dosya(lar) belirlenir → yolu yazılır (tahmin YOK)
[C] Beyazliste kontrolü: dosya tipi → sorumlu agent (§5 Domain Boundaries)
      → beyazlistedeyse [D] · değilse [E]
[D] Salt-okunur yüzey mi? (§16.4 dokunulmazlar + frozen ADR + .ai/.png)
      → salt-okunur ise: yalnız okuma/rapor · değilse [F]
[E] İHLAL TESPİTİ → §8 zinciri (DUR → rapor → escalation) — yazım BAŞLAMAZ
[F] Lock acquire (§10): kilitli ise kuyruk/öncelik · serbest ise yazım izni doğar
[G] Yazım → kapılar (§18-§21) → log.md (append-only) → kapanış
```

**Adım [C] kanıtı:** beyazlistenin kaynağı 12 satırlık dosya tipi tablosudur
(`Dosya: .ai/AGENTS.md · Satır: 95-109` — `*.php` → Backend, `*.js/.css` → UI, `*.sql` → Data,
`*.cpp/.h` → Embedded, `*.yml` → DevOps, `tests/**` → QA, `.env` → Security,
`log.md` → tümü append-only, `.ai/` vault → okuma serbest).
**Adım [D] kanıtı:** "Dokunulmaz yüzeyler: `.ai/.png/**` (salt-okunur SSOT) · frozen ADR'ler ·
`*.php` / `*.js` / `*.css` / `*.sql` kaynak kod (bu revizyon Markdown dışı **yazmaz**)"
(`Dosya: kök AGENTS.md · §16.4`; `.ai/AGENTS.md §13.9/4` aynı maddeyi taşır).

## §5. Karar Ağacı A — Dosya Tipi → Yetki

| # | Hedef | Sorumlu | Bu ajan yazabilir mi? | Karar |
|---|---|---|---|---|
| 1 | `*.php` / `*.js` / `*.css` / `*.sql` | İlgili domain agent'ı | ✅ (o domain ajanı) · başka domain → **RED** | `Dosya: .ai/AGENTS.md · Satır: 95-101` |
| 2 | `.ai/**` vault `.md` | MO (koordinasyon) | Okuma serbest; yazım görev manifestine bağlı | `Dosya: .ai/AGENTS.md · Satır: 108` |
| 3 | `.ai/.png/**` | — | **HİÇBİR ajan** — salt-okunur SSOT | `Dosya: kök AGENTS.md · §16.4` |
| 4 | Frozen ADR'ler | Vault Steward | **YOK** — yalnız okunur/referans verilir | `Dosya: .ai/AGENTS.md · §25.3/2` |
| 5 | `.claude/**` (settings/skill/agent/command) | Kullanıcı (onay) · MO (kayıt) | Onaysız değişiklik **YOK** | `Dosya: .claude/AGENTS.md · §3.3/2` |
| 6 | `log.md` | Tüm ajanlar | **Append-only** — geçmiş satıra dokunulmaz | `Dosya: .ai/AGENTS.md · Satır: 107` |
| 7 | `.env` / gizli anahtar | Security Engineer | Değer **hiç okunmaz/yazılmaz** — REDACTED | `Dosya: .ai/AGENTS.md · Satır: 106` + §17/3 |
| 8 | `.github/workflows/*.yml` | DevOps Engineer | Domain boundary korunur | `Dosya: .ai/AGENTS.md · Satır: 102` |

## §6. Karar Ağacı B — Eşzamanlılık → Lock Kararı

| # | Durum | Karar | Sonuç |
|---|---|---|---|
| 1 | Hedef dosya kilitli değil | Lock acquire → yaz | serbest devam (`Dosya: .ai/AGENTS.md · Satır: 455-460`) |
| 2 | Kilitli, aynı öncelik | Kuyruğa gir (max 30s bekleme) | sıra bekleme (`§12.2`) |
| 3 | Kilitli, ben öncelikli (HIGH) | Sıra talebi | öncelik sırası (`§12.1`) |
| 4 | Ben **CRITICAL**, kilit **LOW/MEDIUM** | **Priority override — kilidi kırar** | kırma + `log.md` (`§12.3`) |
| 5 | Deadlock (hiçbir taraf geri çekilmiyor) | MO en eski kilidi kırar | MO intervention (`§12.3`) |
| 6 | 30s timeout doldu | Lock serbest + escalation | L1 (`§12.2`, `§12.3`) |

## §7. Karar Ağacı C — Salt-Okunur Yüzey Kontrolü

| # | Yüzey | Okuma | Yazım | İhlal sonucu |
|---|---|---|---|---|
| 1 | `*.php` / `*.js` / `*.css` / `*.sql` (bu revizyon) | ✅ | ❌ (Markdown dışı yazım yasak) | geri alma + log (`Dosya: kök AGENTS.md · §16.4`) |
| 2 | `.ai/.png/**` | ✅ | ❌ (salt-okunur SSOT) | revert (`§16.4`) |
| 3 | Frozen ADR metni | ✅ | ❌ (kopyalanmaz/değiştirilmez) | revert (`Dosya: .claude/AGENTS.md · §3.3/8`) |
| 4 | `CLAUDE.md` (kök + `.claude/`) | ✅ | ❌ (dokunulmaz — yalnız vault-sync) | derhal geri al (`§3.3/3`) |
| 5 | `plans/` içeriği → `.ai/` kopyası | — | ❌ | SSOT kirlenmesi (`§3.3/5`) |
| 6 | Bu üretim turundaki tek hedef dosya (permissions-surucu.md) | ✅ | ✅ | kapsam dışı yazım = ihlal (kapı §21/10) |

## §8. Yetki İhlali Davranış Zinciri

```text
[1] İHLAL TESPİTİ — hook (ai-risk-gate) veya ajan kendi kontrolü (§4-C/D/E)
[2] DUR — yazım/komut DERHAL durur; kısmi işlem tamamlanmaz, geri alınır
[3] RAPOR — ne / hangi dosya / hangi kural ihlal edildi: log.md'ye CRITICAL girişi
      (append-only — geçmiş satıra dokunulmaz, Dosya: .ai/AGENTS.md · Satır: 107)
[4] ESCALATION — L1'e; çözülmezse §14 zinciri (L2 → L3 → İnsan)
[5] SONUÇ — düzelt + devam ET · revert + log · veya üst karar (§9 karar tablosu)
```

**Zincirin bağlayıcılığı:** "Domain boundary ihlali → Sistem durur, MO müdahale eder"
(`Dosya: .ai/AGENTS.md · Satır: 217`); "Mockup okunmadan kod tespit edilir → kod derhal
revert edilir → `log.md` CRITICAL → Vault Steward'a bildirim"
(`Dosya: .ai/CLAUDE.md · Satır: 552-556`).

## §9. İhlal Türü → Karar Tablosu

| # | İhlal türü | Anında tepki | Karar | Kaynak künye |
|---|---|---|---|---|
| 1 | Domain boundary (yanlış ajan/domain) | DUR + revert | log ERROR, MO müdahale | `Dosya: .ai/AGENTS.md · Satır: 217, 222` |
| 2 | Mockup/şablon okunmadan kod (Guardrail #11/#16) | DUR + revert | CRITICAL log, Vault Steward | `Dosya: .ai/CLAUDE.md · Satır: 530, 578` |
| 3 | Dokunulmaz yüzeye yazım (§7 tablo 1-5) | Geri alma | dosya geri yüklenir | `Dosya: .ai/CLAUDE.md · Satır: 523, 533` |
| 4 | Secret/anahtar sızıntısı (maskeleme atlandı) | DUR | **anahtar iptali** + log ERROR | `Dosya: .claude/AGENTS.md · §3.3/1` |
| 5 | Hallüsinasyon (kanıtsız iddia) | Etiketle | `VERIFICATION REQUIRED`; içerik silinir | `Dosya: .ai/CLAUDE.md · Satır: 522` |
| 6 | Plan/onaysız kod (Guardrail #1/#14) | DUR | kod revert edilir | `Dosya: .ai/CLAUDE.md · Satır: 520, 533` |
| 7 | Vault'ta çelişki (Guardrail #12) | DUR | kullanıcıya sor, onay bekle | `Dosya: .ai/CLAUDE.md · Satır: 531` |
| 8 | Subagent `git commit` | Yetkisiz commit | orkestratöre devredilir | `Dosya: .claude/AGENTS.md · §3.3/6` |
| 9 | Üst üste 3 başarısız düzeltme | **DUR** | şüpheli varsayımı yaz, üst karar iste | kök `AGENTS.md §7` (devam yasak) |

## §10. Lock Yetkisi Akışı (CRITICAL Override)

| Adım | Ajan davranışı | Yetki sınırı |
|---|---|---|
| 1 | `Lock acquire` dene | max **30 sn** bekleme (`Dosya: .ai/AGENTS.md · Satır: 447, 457`) |
| 2 | Başarılı → düzenle | kilitleme süresi max 30s (`§12.1`) |
| 3 | Başarısız → kuyruk | öncelik sırası: CRITICAL > HIGH > MEDIUM > LOW (`§12.1`) |
| 4 | **CRITICAL** görev → başka ajanın kilidi varsa | **Priority override — kilidi kırma yetkisi var** (`Dosya: .ai/AGENTS.md · Satır: 467`) |
| 5 | Kırma sonrası | `log.md`'ye acquire/release kaydı **zorunlu** (`§12.1`) |
| 6 | Deadlock / kıramıyorsan | **MO intervention** — MO en eski kilidi kırar (`Dosya: .ai/AGENTS.md · Satır: 468`) |
| 7 | Timeout (30s) | lock serbest + escalation (`§12.3`) |

**Tek yetki sahibi:** kilidi yalnız **CRITICAL öncelikli görev** kırabilir; HIGH/MEDIUM/LOW
ajanlar kuyruk dışına **çıkamaz**. Kırma yetkisi kişisel değil **görev önceliğine** bağlıdır.

## §11. Yetki + Context Lock İlişkisi (eşzamanlı yazım)

| Senaryo | Yetki durumu | Karar |
|---|---|---|
| Ajan A kilitli, ajan B yazmak istiyor (eşzamanlı) | B'nin yetkisi geçerli ama kilit B'ye engel | kuyruk → sıra (edge case 1: `Dosya: .ai/AGENTS.md · Satır: 199`) |
| B CRITICAL, A LOW | B'nin öncelik yetkisi kilidi kırar | kırma + log (`§12.3`) |
| İkisi de CRITICAL | Deadlock riski | MO en eski kilidi kırar (`§12.3`) |
| Lock + redaction çakışması | İki kapı ayrıdır — kilit geçilse bile maskeleme **her yazım geçerli** | maskeleme her zaman kazanır (`§17/3`) |
| Lock, escalation'ı tetikler | 30s timeout → L1 | §14 zinciri devreye girer |

## §12. Secret Maskeleme Akışı — REDACTED Davranışı

| # | Durum | Ne maskelenir | Nasıl |
|---|---|---|---|
| 1 | Hassas anahtar-adı eşleşmesi | değer → `[REDACTED]` | `auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie` — case-insensitive (`Dosya: shared/src/Log/Redactor.php · Satır: 31`) |
| 2 | 64-char hex / base64 hash | tamamı `[REDACTED]` | lookaround deseni (`Dosya: .../Redactor.php · Satır: 49`) |
| 3 | E-posta | yerel kısım `***`, alan adı korunur | `***@domain` (`Dosya: .../Redactor.php · Satır: 56, 88`) |
| 4 | UUID (8-4-4-4-12) | **KORUNUR** — maskelenmez | tireli segment hash kuralına girmez (`Satır: 20`) |
| 5 | `settings.json` içindeki `EXA_API_KEY` | değer dosyada yok | `Dosya: .claude/AGENTS.md · §6/7` (REDACTED kapısı) |
| 6 | `.env` değeri | **hiçbir ajan okumaz/yazmaz** | Security sahipliği (`Dosya: .ai/AGENTS.md · Satır: 106`) |
| 7 | Geçmiş `.log` dosyaları | **dokunulmaz** — maskeleme yalnız YAZILACAK satırı etkiler | `Dosya: .../Redactor.php · Satır: 22` |

**Kural özü (vault):** "Sensitive data log'da → `[REDACTED]` ile maskeleme"
(`Dosya: .ai/AGENTS.md · Satır: 200`, edge case 3) — maskeleme **log'a özel değil, tüm
yazım yüzeyine** uygulanır; `.md` raporlarda da aynı davranış geçerlidir.

## §13. Repo Kanıtı — Redactor.php (tek maskeleme noktası)

> ✅ **Repo kanıtı** — `shared/src/Log/Redactor.php` içinden birebir alıntılar.

```php
public const MASK = '[REDACTED]';                                    // Satır 28
private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|
token|secret|api_key|session_id|session|authorization|cookie';      // Satır 31
public static function redact(string $line): string                  // Satır 65
{
    $redacted = preg_replace_callback(self::KEY_VALUE_PATTERN, ...); // Satır 72-76
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted); // Satır 83
    $withEmail = preg_replace_callback(self::EMAIL_PATTERN, ...);    // Satır 86-90
    return $withEmail ?? ($replaced ?? $redacted);                   // Satır 92
}
private static function maskValue(string $prefix, string $value): string // Satır 98
{
    if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
        return $prefix . $value[0] . self::MASK . $value[0];         // Satır 101
    }
    return $prefix . self::MASK;                                     // Satır 104
}
```

**Sürücü ile bağlantısı:** dosyanın kendi başlığı kuralı künyeler — "Kural: .ai/AGENTS.md
§17 Edge Case 3 … Tüm log yazma yolları (FileHandler, StructuredLogger, PageRouterKernel
fatal) satırı BURADAN geçirir; tek maskeleme noktası" (`Dosya: shared/src/Log/Redactor.php · Satır: 8-11`).

## §14. Escalation Akışı — L1 → L2 → L3 → İnsan

```text
L1 (Domain Lead) → L2 (Tech Lead) → L3 (Arch Lead) → İNSAN
```

| Kademe | Kim | Ne zaman devralır | Timeout |
|---|---|---|---|
| **L1** | İlgili domain lead'i (Data/Security/Embedded/QA/DevOps) | ajan kendi alanında çözüm üretemedi (dosya erişimi, BCNF, CSRF, coverage…) | 30s (`Dosya: .ai/AGENTS.md · Satır: 395`) |
| **L2** | Tech Lead | L1 timeout / L1'in çözemediği teknik çelişki | 60s (`Satır: 396`) |
| **L3** | Arch Lead | Mimari çelişki (ADR) veya güvenlik açığı | 120s (`Satır: 397`; senaryo satırları `387-388`) |
| **İnsan** | Bayram Ali | **Son çare** — sistem durması | anlık (`Satır: 389, 399`) |

## §15. Escalation Senaryo + Timeout Tablosu (§10.1/§10.2 taşıması)

| Senaryo | Başlangıç | Hedef | Timeout |
|---|---|---|---|
| Agent aynı dosyayı değiştiremiyor (lock/permission) | L1 | L2 | 30s |
| BCNF çelişkisi | L1 (Data) | L2 | 30s |
| CSRF/CSP uyumsuzluğu | L1 (Security) | L2 | 15s |
| ASIO cihaz kaybı | L1 (Embedded) | L2 | 30s |
| Test coverage %80 altı | L1 (QA) | L2 | 60s |
| Deployment başarısız | L1 (DevOps) | L2 | 30s |
| Mimari çelişki (ADR) | L2 | L3 | 60s |
| Güvenlik açığı | L2 | L3 | 15s |
| Sistem durması | L2 | İnsan | Anlık |

**Kaynak künye:** `Dosya: .ai/AGENTS.md · Satır: 377-389` (§10.1) · `Satır: 391-399`
(§10.2: L1 30s · L2 60s · L3 120s · max retry 3 her seviyede · insan son çare).

## §16. Retry / Override Kuralları

| # | Kural | Değer | Sonuç |
|---|---|---|---|
| 1 | Max retry | **3** (her seviyede) | 3 başarısız → escalation (`Dosya: .ai/AGENTS.md · Satır: 398`) |
| 2 | Timeout sonrası | retry → 3. deneme başarısız | **queue reset** (tüm kilitler sıfırlanır — `Satır: 469`) |
| 3 | CRITICAL öncelik retry'ı | max retry **1** (timeout 5s) | anlık yanıt beklenir (`Dosya: .ai/AGENTS.md · Satır: 320`) |
| 4 | LOW öncelik retry'ı | max retry 2 (timeout 60s) | düşük aciliyet (`Satır: 323`) |
| 5 | Health state `Failed (500)` | 3 retry başarısız | queue reset + escalation (`Satır: 421`) |
| 6 | Health state `Dead (503)` | yanıt yok | **derhal** escalation (`Satır: 422`) |
| 7 | Aynı hata 3 düzeltme turu | tur sayacı doldu | **DUR** — üst karar (kök `AGENTS.md §7`) |
| 8 | Override | yalnız CRITICAL lock kırar | MO intervention alternatifi (`§12.3`) |

## §17. Commit Yetkisi — Subagent Yasak

| # | Kural | Karar | Kaynak |
|---|---|---|---|
| 1 | `git commit` subagent tarafından ATILMAZ | **orkestratöre ait** | `Dosya: kök AGENTS.md · §16.5` + `.ai/AGENTS.md §13.9/5` |
| 2 | Commit kararı | ana oturum (orchestrator) verir | kök `AGENTS.md §7` |
| 3 | Yetkisiz commit tespiti | orkestratöre devredilir (§9/8) | `Dosya: .claude/AGENTS.md · §3.3/6` |
| 4 | Vault üretim turu kapanışı | commit'i orchestrator atar, subagent **bekler** | `Dosya: .ai/AGENTS.md · Satır: 161` |
| 5 | Üretim dosyası kapsamı | bu turda **tek dosya** yazılır; başka dosyaya yazım ihlaldir | §21 kapı 10 |

## §18. PowerShell — Beyazliste Yetki Taraması (gerçek çalıştırılabilir)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır.

```powershell
# A) Görev beyazlistesi: hedef dosya sorumlu mu? (§5 Domain Boundaries taşıması)
$map = @{ '*.php'='Backend'; '*.js'='UI'; '*.css'='UI'; '*.sql'='Data'
          '*.cpp'='Embedded'; '*.h'='Embedded'; '*.yml'='DevOps' }
$targets = @('shared/src/AI/KnowledgeBase.php', 'ui-design/01-mockup-index.md')
foreach ($t in $targets) {
    $ext = [IO.Path]::GetExtension($t)
    $owner = $map[$ext]; if (-not $owner) { $owner = 'vault(MO)' }
    "{0,-45} ext={1,-5} owner={2}" -f $t, $ext, $owner
}
```

```powershell
# B) Dokunulmaz yüzey ihlali taraması (git diff ile: değişen dosya yasaklı yüzeyde mi?)
$readonly = git status --porcelain |
    Where-Object { $_ -match '^( M|M |MM)' } |
    ForEach-Object { $_.Substring(3).Trim('"') } |
    Where-Object { $_ -match '^(\.ai/\.png/|.*\.(php|js|css|sql)$)' }
"ihlal aday => {0} (kapı: istisna yalnızca görev manifesti)" -f $readonly.Count
$readonly | ForEach-Object { "  YASAK YUZEY: {0}" -f $_ }
```

## §19. PowerShell — Salt-Okunur + Lock + REDACTED Kapıları

```powershell
# C) Salt-okunur yüzey bütünlüğü: .ai/.png/** değişmiş mi? (kapı: 0)
$png = git status --porcelain -- '.ai/.png/'
"png degisiklik => {0} (kapı: 0)" -f $png.Count

# D) REDACTED kapısı: üretilen .md içinde ham anahtar kalıpları var mı? (kapı: 0)
$p = '.ai\architecture\K015-AI\permissions\permissions-surucu.md'
$pat = '(?i)(api[_-]?key|password|secret)\s*[=:]\s*["'']?[A-Za-z0-9]{16,}'
"ham anahtar hit => {0} (kapı: 0)" -f ((Select-String -Path $p -Pattern $pat) | Measure-Object).Count

# E) Log append-only kapısı: log.md geçmiş satırları silinmemiş (yalnız ekleme)
git diff --numstat -- '.ai/log.md' | ForEach-Object { "log diff => {0}" -f $_ }
```

```powershell
# F) Bu dosyanın üretim kapısı (>=500 + FM 10 + Authority tek seferde)
$p = '.ai\architecture\K015-AI\permissions\permissions-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$fm = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $fm).Count -eq 10)
```

## §20. Repo Kanıtı — settings.json Hook Kapası (JSON alıntı)

> ✅ **Repo kanıtı** — `Dosya: .claude/settings.json · Satır: 13-38` (künyeli birebir alıntı).

```json
"PreToolUse": [
  {
    "matcher": "Edit|Write",
    "hooks": [
      {
        "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-risk-gate.cjs\"",
        "timeout": 15,
        "statusMessage": "Control Plane HIGH-RISK kapısı (T1/T2)"
      }
    ]
  }
],
"PostToolUse": [
  {
    "matcher": "Edit|Write",
    "hooks": [
      {
        "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-validate-post.cjs\"",
        "timeout": 60,
        "statusMessage": ".ai validator (fm/link/ssot/dep/tier/orphan)"
      }
    ]
  }
]
```

**Sürücü anlamı:** yazım anında **iki otomatik kapı** vardır — PreToolUse risk gate'i (T1/T2
yüksek risk) ve PostToolUse validator'ı (frontmatter/link/SSOT/dep/tier/orphan). Elle akışta
§4-C/E adımları bu kapıların **arızalanmış sayımını da** kapsar: hook engellemediyse ajan
kendi beyazliste kontrolüyle DUR olmak zorundadır.

## §21. İllüstratif — Python Permission Resolver

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
READONLY_PATTERNS = (r'\.ai/\.png/', r'\.env$', r'.*\.(php|js|css|sql)$')  # §7 tablosu ruhu

def resolve(target: str, agent: str, owner: str) -> str:
    if target == owner or agent == 'mo':
        return 'WRITE'
    if any(re.search(p, target) for p in READONLY_PATTERNS):
        return 'READONLY'          # oku+rapor; yazım DUR (§8 zinciri)
    return 'DENY'                  # ihlal → rapor → escalation (L1)
```

*İskelet kaynak: `Dosya: .ai/AGENTS.md · Satır: 95-109` (sahiplik) · `Dosya: kök AGENTS.md · §16.4` (dokunulmazlar).*

## §22. İllüstratif — Go Lock Override

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// Acquire: max 30s bekle; CRITICAL ise kilidi kır (§12.3 priority override).
func Acquire(f string, prio int) (bool, error) {
    if owner, ok := locks[f]; ok {
        if prio == CRITICAL && owner.Prio < CRITICAL {
            logBreak(f, owner)   // log.md acquire/release zorunlu (§12.1)
            locks[f] = Lock{Agent: self, Prio: prio}
            return true, nil
        }
        return waitQueue(f, 30*time.Second) // kuyruk → timeout → L1 escalation
    }
    locks[f] = Lock{Agent: self, Prio: prio}
    return true, nil
}
```

## §23. İllüstratif — TypeScript Escalation Gate

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
const TIMEOUT = { L1: 30, L2: 60, L3: 120, HUMAN: 0 } as const; // §10.2 tablosu
type Level = keyof typeof TIMEOUT;

export function escalate(level: Level, retries: number): Level | 'STOP' {
  if (retries >= 3) return level === 'L3' ? 'HUMAN' : next(level); // max 3 retry
  return level; // süre dolmadan bekle; süre doldu → next(level)
}
const next = (l: Level): Level => (l === 'L1' ? 'L2' : l === 'L2' ? 'L3' : 'HUMAN');
```

## §24. Token / Okuma Ekonomisi

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Boot'ta okunan dosya yeniden okunmaz | çift israf yok | §3 boot zinciri (Guardrail #2) |
| 2 | Yetki kararı **dosya bazında**, tam dosya okumadan | beyazliste tablosu ~15 satır | `Dosya: .ai/AGENTS.md · Satır: 95-109` |
| 3 | Toplu vault okuma yasak — yalnız ihtiyaç anında `@` | bütçe korunur | kök `AGENTS.md §14` |
| 4 | Hook kapıları dosya okumaz (matcher + betik) | gate maliyeti ~0 token | `Dosya: .claude/settings.json · Satır: 15, 28` |
| 5 | İhlalde DUR — sonsuz retry token yakmaz | max 3 + escalation | §16 tablosu |

## §25. Hata Modları

| # | Hata | Semptom | Tespit | Tepki |
|---|---|---|---|---|
| 1 | Beyazliste atlanmış | yanlış domain dosyasına yazım | §4-C adımı atlanmış | DUR + revert (§9/1) |
| 2 | Salt-okunur yüzeye yazım | `.png`/ADR/`.php` diff'i | §19 betik C | geri alma (§9/3) |
| 3 | Lock görmezden gelindi | eşzamanlı yazım çakışması | kilit kaydı yok | queue + §10 akışı |
| 4 | Maskeleme atlanmış | log/raporda ham secret | §19 betik D | **anahtar iptali** (§12/4) |
| 5 | Escalation zinciri kısaltılmış | L1'den doğrudan insan | seviye atlanmış | zinciri geri oyna (§14) |
| 6 | Retry sınırsız | aynı hata tekrarı | tur sayacı yok | DUR + üst karar (§16/7) |
| 7 | Hook engeli "geçti" sanıldı | gate timeout/arıza | statusMessage logu | elle kapı tekrarı (§20) |
| 8 | Tahmini yetki kararı | kanıtsız "yazabilirim" | künye yok | `⚠️ VERIFICATION REQUIRED` + DUR |

## §26. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | §19 betik F |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§19/F) |
| 3 | Authority son satır | birebir | §19 betik F |
| 4 | Kapsam dışı yazım | **0 dosya** | `git status --porcelain` sayacı |
| 5 | Salt-okunur bütünlüğü | `.ai/.png/**` değişiklik 0 | §19 betik C |
| 6 | REDACTED | ham anahtar kalıbı 0 | §19 betik D |
| 7 | Commit | bu turda **0 commit** | `git log` (subagent yasak — §17) |
| 8 | Placeholder | `{{` yok | `Select-String -Pattern '\{\{'` → 0 |
| 9 | Guardrail sayımı | `.ai/CLAUDE.md §7` = **16 madde** (#1-14, 16, 17) | `Dosya: .ai/CLAUDE.md · Satır: 518-535, 560` |
| 10 | İllüstratif etiket | Python/Go/TS bloklarında uyarı satırı var | gözden geçirme (§21-§23) |

## §27. Token Ekonomisi + Anti-Pattern Özet

| # | Yanlış | Doğru |
|---|---|---|
| 1 | "Hook geçtiyse yetki tamam" | elle beyazliste kontrolü de şart (§20) |
| 2 | Lock'u beklemeden yazmak | kuyruk/öncelik/CRITICAL kırma (§10) |
| 3 | Maskeyleme "log içindir" diye atlamak | maskeleme **tüm yazım yüzeyinde** (§12/7) |
| 4 | Insana erken sormak | L1→L2→L3 sırası zorunlu; insan son çare (§14) |
| 5 | 4. retry denemek | 3'te DUR + escalation (§16/1) |

## §28. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [permissions-anayasa]` — bu turda `permissions/` altında yalnız `index.md` diskteydi (glob 2026-10-17: 1 dosya). `permissions-anayasa` paralel üretiliyor mu? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | Ebeveyn `permissions/index.md` `children: []` ve "Kademe-3 bu fazda AÇILMADI" diyor (Satır: 6). Bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q3 | Hook gate'i (`ai-risk-gate.cjs`) T1/T2 risk sınıflandırma listesi .cjs içindedir — sürucuya (yalnız .md) taşınsın mı, yoksa .cjs tek kaynak mı kalsın? | `⚠️ VERIFICATION REQUIRED` |
| Q4 | Lock mekanizması bugün **kasıtlı/prosedürel** midir (ajlar arası uygulama), yoksa fiziksel kilit dosyası var mı? Disk kanıtı bulunamadı — `UNKNOWN`. | bilinmiyor — **uydurulmaz** |
| Q5 | Escalation seviye sahipleri (L1/L2/L3 kimdir — ajan adı mı, insan mı?) §10'da rol olarak yazılmış, kişi ataması yok. | MO/Vault Steward ataması |

## §29. Kaynaklar

| Kaynak | Yol / URL | Kullanım amacı | Ölçüm |
|---|---|---|---|
| Agent registry SSOT | `.ai/AGENTS.md` | §5 sahiplik · §8 öncelik · §10 escalation · §12 lock · §17-§18 uyarılar | 775 satır (okundu 2026-10-17) |
| AI anayasası | `.ai/CLAUDE.md §7` | 16 Hard Guardrails + ihlal sonucu (Satır: 516-535) | 987 satır |
| Kök kurallar | `AGENTS.md` (kök) §16.4-§16.5 | Dokunulmaz yüzeyler + commit yetkisi | 285 satır |
| `.claude` klasör kuralları | `.claude/AGENTS.md` §3.3 | 8 yetki sınırı | — |
| Ayar kanıtı | `.claude/settings.json` | Pre/PostToolUse hook kapıları (Satır: 13-38) | 64 satır |
| Maskeleme kodu | `shared/src/Log/Redactor.php` | REDACTED davranışı (Satır: 28-105) | 106 satır |
| Yapı referansı | `.../K015-AI/rag/rag-surucu.md` | H1 + § + karar ağacı + tablo iskeleti | 779 satır |
| Ebeveyn düğüm | `.../K015-AI/permissions/index.md` | kapsam sınırı + children durumu (Satır: 6, 29) | 50 satır |
| Web — OWASP | `https://owasp.org/projects/top-10-for-large-language-model-applications` · PDF: `https://owasp.github.io/www-project-top-10-for-large-language-model-applications/assets/PDF/OWASP-Top-10-for-LLMs-v2025.pdf` | LLM06:2025 **Excessive Agency** — yetki minimizasyonu ilkesi (bu sürucünün beyazliste/az-yetki gerekçesi) | doğrulandı 2026-10-17 (websearch) |
| Web — NIST | `https://csrc.nist.gov/pubs/sp/800/53/r5/upd1/final` | SP 800-53 Rev.5 **AC-6 Least Privilege** — escalation/onay kapısı prensibi | ⚠️ VERIFICATION REQUIRED (bu turda fetch edilmedi) |

## §30. Gate (özet)

§1-§30 tamam: ELI15 · kapsam · 5 kademe yetki hiyerarşisi · 7 adımlı A-G akışı · 3 karar
ağacı tablosu · 6 adımlı ihlal zinciri · 9 maddelik ihlal karar tablosu · lock yetkisi 7 adım ·
lock-yetki ilişkisi 5 satır · REDACTED 7 durum · repo kanıtı PHP (Redactor) + JSON (settings) ·
4 seviyeli escalation + 9 senaryo tablosu · 8 madde retry/override · commit yetkisi 5 kural ·
PowerShell 6 betik (A-F) · illüstratif 3 blok (Python/Go/TS) · 5 token kuralı · 8 hata modu ·
10 kapı · 5 anti-pattern · 5 açık soru · 11 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı yazım
**0** · `git commit` **0** · ham secret **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
