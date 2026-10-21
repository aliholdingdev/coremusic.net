# Changelog — `security-hardening`

> Skill: `security-hardening` · Format: `claude-skill-v3` ·
> Tarih biçimi: YYYY-MM-DD ·
> Kaynak: skill-maker §N10 (eski sürüm silinmeden önce `references/changelog.md`
> oluşturulur).

---

## [4.0.0] — 2026-10-21 (bu sürüm — sıfırdan yeniden yazım, Wave 1/3)

**Yöntem:** skill-maker v3.0 spec (§5 N1-N10) — eski dosyalar (SKILL.md 92
satır + 4 sub-file, toplam 394 satır) silindi, tümü yeniden yazıldı.

### Added
- `references/sources.md` — Truth Mode kaynak listesi (5 harici kaynak, erişim
  2026-10-21); N4 zorunlu.
- `references/changelog.md` — bu dosya; N10 zorunlu.
- `references/owasp-mapping.md` — OWASP Top 10:2025 ↔ CoreMusic savunma
  eşlemesi, saldırı senaryoları, header matrisi, denetim checklist'i (derinlik).
- `examples/rate-limit-csp-walkthrough.md` — rate-limit + CSP tam döngü
  örneği (ADR-013 + ADR-012); N5 genişletme.
- SKILL.md §7.1: vault şablonu (`.ai/.templates/documentation/security-audit-
  template.md` §4.1) ile OWASP **2021 vs 2025** fark tablosu + çelişki uyarısı
  (şablon okunmadı/düzeltilmedi — authority: reference).
- SKILL.md §18: vault ADR künyesi (`.ai/.decisions/accepted/` dosya listesi —
  `glob` kanıtı 2026-10-21).
- SKILL.md §10.1: rate-limit pencere/kimlik tuzakları (fixed-window kayması,
  interval < window, client_id keşme, IP spoofing) · §12.1: kripto karar
  ağacı (blind index dâhil).

### Changed
- `SKILL.md`: 92 → **517 dolu satır / 645 ham satır** (ölçüm 2026-10-21;
  500-2000 aralığında ✓);
  18 bölümlü yapı (§1-§18 + footer); frontmatter yalnız
  `name/description/license/metadata`; `metadata.version: 4.0.0`,
  `previous-version: 3.0.0`.
- `description`: "Use when …" deseni + mevcut Türkçe tetikleyiciler korundu
  (`güvenlik değişikliği`, `CSRF ekle`, `CSP ayarı`, `auth kodu`).
- `references/middleware-security-order.md`: 61 → yeni yapı (§1-§6: pipeline
  tablosu + immutability + CSP nonce zinciri + bozulma matrisi + pipeline dışı
  bileşenler + adım adım audit).
- `references/forbidden-patterns.md`: 85 → §1 mutlak veto (eval/exec, token
  hash, hardcoded secret, fail-open, Argon2id/AES-GCM) + §2 yön-lu yasağlar
  + §3 mimari yerleşim + §4 veto akışı (rule 15/16).
- `references/crypto-standards.md`: 58 → §1 algoritma tablosu + §2 GCM
  IV/nonce (kayıt formatı) + §3 Argon2id parametre/rehash + §4 anahtar
  saklama (red-tablo) + §5 hybrid auth + §6 10 karşılaştırma çifti.
- `examples/csrf-change-walkthrough.md`: 98 → ADR-010 §1.1 (2026-10-14
  düzeltmesi: `_csrf_token` → `csrf_token`, read-only tampon idempotent)
  girdiyle tam döngü (gözlemle → pipeline → controller → doğrula → rapor).

### Security
- OWASP referansları **2021 → 2025** taşındı (resmi kaynak 2026-10-21'de
  doğrulandı; §sources.md).
- CSP: "W3C Rec" iddiası düzeltildi → **Level 3 = Working Draft (16 Sept
  2026), Rec = Level 2** (§sources.md #2).
- `password_hash` yanlış deseni (`PASSWORD_DEFAULT`) artık açıkça veto
  listesinde (PHP 8.4 default = bcrypt, ADR-022 §1 ihlali).

### Removed
- `CLAUDE.md` sidecar (§N8) — skill içinde bulunmaz.
- Tüm sub-file'lar ilk kayıtta silindi ve yeniden yazıldı (report bölümünde
  "deleted/new file listesi").

### Deviations (raporlanan sapmalar — skill-maker checklist ADIM 5)

- Minimum 6+2 dosya yerine **8 dosya** üretildi (`owasp-mapping.md` +
  `rate-limit-csp-walkthrough.md`) — her ikisi de SKILL.md §4/§5 tablolarında
  ilan edildiği için N4 orphan hatası yok.
- İlk taslakta §7 OWASP satırları A02/A04/A05 sırasıyla çelişiyordu;
  `sources.md`'deki doğrulanmış 2025 listesiyle **düzeltildi** (kayıt:
  bu bölüm).
- `forbidden-patterns.md:83`'teki örnek literal (`gizli-anahtar-123`) gizli-anahtar
  tarayıcısında isabet veriyordu → `<ANAHTAR>` placeholder ile değiştirildi
  (gerçek değer hiçbir dosyada yoktu).
- İlk changelog taslağında git'le kanıtlanmayan 2 satır (`meta-bilgi (44 satır)`,
  `1.0.0 = 1 sub-file/56 satır`) vardı → `git show c46f08a/cfba6b2` ile
  düzeltildi; 1.0.0/2.0.0 "İZ YOK (UNKNOWN)" olarak işaretlendi.
- Değişiklikler **commit edilmedi** (subagent git write yasak — orkestratörde).

---

## [3.0.0] — 2026-10-07 (git arşivi — `c46f08a` → `cfba6b2`)

- **5 dosya:** SKILL.md (96 satır @ `c46f08a` → **92 satır** @ `cfba6b2`) +
  `middleware-security-order.md` (61) + `forbidden-patterns.md` (85) +
  `crypto-standards.md` (58) + `csrf-change-walkthrough.md` (98) = **394 satır**
  (eski toplam; ölçüm bu oturumda `git show` ile alındı).
- Frontmatter: `version: 3.0.0` · `updated: 2026-10-07` · footer
  `*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*`
  (her iki commit'te aynı).
- `references/changelog.md` bu sürümde **yoktu** — N10 gereği 4.0.0'da eklendi.
- `previous-version: 3.0.0` alanı 4.0.0 frontmatter'ında bu sürüme bağlanır.

---

## [1.0.0] / [2.0.0] — İZ YOK (UNKNOWN)

- `git log --all -- .claude/skills/security-hardening/` = **yalnız 2 commit**
  (`c46f08a`, `cfba6b2`) ve ikisi de `version: 3.0.0`.
- 1.0.0/2.0.0 sürümü, `meta-bilgi` benzeri bir alt dosya veya bu dizinde
  başka bir tarihçe **diskte/git'te tespit edilmedi** → uydurulmadı, bu
  satırla bilinmeyen olarak işaretlendi (Zero-Hallucination §3).
