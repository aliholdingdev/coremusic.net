# Yasaklı Kalıplar — Forbidden ↔ Correct (snippet'li)

> Kaynak: `.ai/CLAUDE.md` §21 Forbidden Patterns (11 satır) · §23 Critical Warnings ·
> §7 Guardrail #6/#9/#10 · ADR-002 (PDO), ADR-010 (token), ADR-012 (CSP/Trusted Types),
> ADR-022 (SELECT */, prepared statement).

## Anahtar Tablo

| # | ❌ Yasak | ✅ Doğru | Kaynak |
|---|----------|----------|--------|
| 1 | `_csrf_token` | `csrf_token` | Guardrail #6 (2026-05-30) |
| 2 | `localStorage` / `sessionStorage` auth için | Session-based auth (HTTPOnly cookie) | §21, ADR-011 |
| 3 | Hardcoded secret (kodda/logda) | `.env` / credential vault (ADR-015) | §21, §23-3 |
| 4 | `SELECT *` | Explicit column listesi | §21, ADR-022 |
| 5 | `eval()` / `Function()` | Safe alternatives (DOMParser, JSON.parse) | §21 |
| 6 | `innerHTML` | `DOMParser` + `TrustedTypes` | §21, ADR-012 |
| 7 | ORM (Eloquent, Doctrine) | Raw PDO (prepared statement tek kapı — ADR-002) | §21, Guardrail #9 |
| 8 | React / Vue / Angular | Vanilla JS + ITCSS | §21, Guardrail #10, ADR-001 |
| 9 | `var` | `const` / `let` | §21 |

## Doğru/Yanlış Örnekler

```php
// 1) CSRF token adı — Guardrail #6
// ❌ YASAK
if ($_POST['_csrf_token'] !== $_SESSION['_csrf_token']) { http_response_code(403); }
// ✅ DOĞRU (timing-safe, ADR-010: CsrfMiddleware hash_equals)
if (!hash_equals($_SESSION['csrf_token'], $request->header('x-csrf-token'))) {
    http_response_code(403); // csrf_invalid
}
```

```js
// 2) Auth saklama — §21
// ❌ YASAK
localStorage.setItem('authToken', jwt);
sessionStorage.setItem('user', JSON.stringify(u));
// ✅ DOĞRU — sunucu tarafı HTTPOnly cookie oturumu; JS token okumaz/yazmaz
fetch('/api/me', { credentials: 'include' });
```

```php
// 3) Hardcoded secret — §21 / ADR-015
// ❌ YASAK
$pdo = new PDO($dsn, 'root', 'S3cr3tP@ss');
// ✅ DOĞRU — .env → DI Config servisi
$pdo = new PDO($dsn, $config->get('DB_USER'), $config->get('DB_PASSWORD'));
```

```php
// 4) SELECT * — §21 / ADR-022
// ❌ YASAK
$pdo->query("SELECT * FROM users WHERE id = $id");
// ✅ DOĞRU — explicit columns + prepared statement (ATTR_EMULATE_PREPARES=false)
$stmt = $pdo->prepare('SELECT id, email, role FROM users WHERE id = :id');
$stmt->execute(['id' => $id]);
```

```js
// 5) eval / Function — §21
// ❌ YASAK
const obj = eval('(' + userInput + ')');
const fn = new Function('data', userCode);
// ✅ DOĞRU
const obj = JSON.parse(userInput);
```

```js
// 6) innerHTML — §21 / ADR-012 (Require-Trusted-Types-For 'script')
// ❌ YASAK
el.innerHTML = untrusted;
// ✅ DOĞRU
const doc = new DOMParser().parseFromString(untrusted, 'text/html');
el.replaceChildren(...doc.body.childNodes);
// + TrustedTypes default policy ile script/sink koruması
```

## Tarama (her güvenlik değişikliğinde — Truth Mode doğrulama adımı)

- `_csrf_token` / `localStorage`-auth / `SELECT *` / `eval(` / `Function(` / `innerHTML`
  grep'i değişen dosyalarda → ihlal varsa **revert + rapor** (H001).
- `_csrf_token` veya `_session` anahtarı: `.claude/skills/security-hardening/**` içinde de
  geçemez (bu dosyalar yalnızca doğru kalıbı öğretir).
- Sıra/kalıp ihlali + kanıtsız iddia → `⚠️ VERIFICATION REQUIRED` veya içerik silinir
  (Guardrail #3).