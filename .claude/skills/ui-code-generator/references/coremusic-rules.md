# CoreMusic Integration Rules

This document specifies mandatory CoreMusic-specific rules for UI/UX code generation when CoreMusic rule injection is enabled.

## When to Apply These Rules

Apply these rules **only if** the user explicitly confirms CoreMusic integration:
- "Use CoreMusic rules" ✅
- "Follow CoreMusic patterns" ✅
- "CoreMusic project" ✅
- No explicit CoreMusic mention = use defaults (no CoreMusic rules) ❌

---

## PHP Standards (CoreMusic Backend)

### Mandatory Rules

```php
// RULE 1: Strict Types (Every File)
declare(strict_types=1);

// RULE 2: PDO Only (No Raw SQL)
// ❌ WRONG
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✅ RIGHT
$stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();

// RULE 3: Constructor Injection (No Static)
class UserService {
    public function __construct(
        private IUserRepository $userRepository,
        private IAuthService $authService,
    ) {}
}

// RULE 4: Explicit Columns (No SELECT *)
// ❌ SELECT *
// ✅ SELECT id, username, email

// RULE 5: CSRF Token Key
// ✅ csrf_token (2026+ standard)
// ❌ _csrf_token (deprecated 2026-05-30)

// RULE 6: CSRF Token Validation
hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    or throw new SecurityException('CSRF validation failed');

// RULE 7: Password Hashing (Argon2id)
password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost'   => 4,
    'threads'     => 2,
]);

// RULE 8: Session Security
session_regenerate_id(true);  // After login
session_start();
session.cookie_httponly = 1;
session.cookie_secure = 1;
session.use_strict_mode = 1;
```

### Namespace Convention

```php
// Core: CoreMusic\System\*
CoreMusic\System\Middleware
CoreMusic\System\Cache
CoreMusic\System\Database
CoreMusic\System\PageRouter
CoreMusic\System\Security

// Contract interfaces
CoreMusic\System\Interface\Database\IDatabase
CoreMusic\System\Interface\Middleware\IMiddleware
CoreMusic\System\Interface\Auth\IAuthService
```

---

## JavaScript Standards (CoreMusic Frontend)

### Mandatory Rules

```javascript
// RULE 1: No var (const/let Only)
// ❌ var x = 10;
// ✅ const x = 10;
// ✅ let x = 10;

// RULE 2: AbortController for Every Fetch
const controller = new AbortController();
fetch(url, {
    method: 'POST',
    headers: { 'X-CSRF-Token': csrfToken },
    signal: controller.signal,
});
previousController?.abort();  // Cancel previous request

// RULE 3: Event Cleanup (No Memory Leaks)
mount() {
    this.clickHandler = (e) => this.#handleClick(e);
    document.addEventListener('click', this.clickHandler);
}
unmount() {
    document.removeEventListener('click', this.clickHandler);
}

// RULE 4: Async/Await (No Callbacks)
// ❌
fetch(url).then(r => r.json()).then(data => console.log(data));

// ✅
async function loadData() {
    const response = await fetch(url);
    const data = await response.json();
    return data;
}

// RULE 5: No Inline Event Handlers
// ❌ <button onclick="handleClick()">Click</button>
// ✅ document.addEventListener('click', ...)

// RULE 6: Router Entry Point (L3)
Router.navigate(url);  // Never call FetchWrapper directly from main.js

// RULE 7: DOM Patch Timing
#patchDOM(html);      // FIRST
#updateCsrf(token);   // SECOND (form inputs exist in new DOM)
```

### Persona Detection (localStorage)

```javascript
// Read user tier from localStorage
const userTier = localStorage.getItem('cm_user_tier') || 'HOME';
// Values: 'HOME' | 'PROFESSIONAL' | 'STUDIO' | 'EXPERT'

// Detect on login (set by server)
if (data.user.tier === 'PROFESSIONAL') {
    localStorage.setItem('cm_user_tier', 'PROFESSIONAL');
}

// Use in UI logic
if (userTier === 'STUDIO') {
    // Load studio-specific widgets, tools, etc.
}
```

---

## CSS Standards (CoreMusic Design)

### ITCSS Mandatory Structure

```
Css/
├── main.css                    (master import)
├── 01_Abstracts/
│   ├── _variables.css          (CSS custom properties: --cm-*)
│   └── _breakpoints.css
├── 02_Base/
│   ├── _reset.css
│   └── _typography.css
├── 03_Layout/
│   ├── _grid.css
│   ├── _header.css
│   └── _footer.css
├── 04_Components/
│   ├── _button.css
│   ├── _card.css
│   └── _form.css
├── 05_Pages/
│   ├── _home.css
│   └── _auth.css
├── 06_Utilities/
│   ├── _responsive.css
│   └── _accessibility.css
└── 07_Vendors/
    └── _external.css
```

### Design Tokens (Custom Properties)

```css
:root {
  /* CoreMusic Semantic Color Tokens */
  --cm-primary: #9d4edd;           /* Purple */
  --cm-accent: #ff4fd8;            /* Pink */
  --cm-bg: #0d0221;                /* Dark purple (login-bg) */
  --cm-text: #ffffff;              /* White */
  --cm-text-secondary: rgba(255, 255, 255, 0.7);
  --cm-border: rgba(255, 255, 255, 0.1);
  --cm-error: #ff4d4d;
  --cm-success: #4dff4d;

  /* Typography */
  --cm-font-family: "AvalonMedium", -apple-system, sans-serif;
  --cm-font-size-base: 16px;
  --cm-font-size-h1: 48px;
  --cm-font-size-h2: 36px;
  --cm-line-height-body: 1.5;
  --cm-line-height-heading: 1.2;

  /* Spacing (8px unit) */
  --cm-space-sm: 8px;
  --cm-space-md: 16px;
  --cm-space-lg: 24px;
  --cm-space-xl: 40px;

  /* Transitions */
  --cm-transition: 200ms ease-out;
}
```

### 7 Breakpoints (CoreMusic)

```css
/* Mobile: 320px */
@media (min-width: 320px) { /* Base styles */ }

/* Mobile Wide: 600px */
@media (min-width: 600px) { /* ... */ }

/* Tablet: 768px */
@media (min-width: 768px) { /* ... */ }

/* Tablet Wide: 1024px */
@media (min-width: 1024px) { /* ... */ }

/* Desktop: 1280px */
@media (min-width: 1280px) { /* ... */ }

/* Desktop Wide: 1920px */
@media (min-width: 1920px) { /* ... */ }

/* TV/4K: 3840px */
@media (min-width: 3840px) { /* ... */ }
```

### WCAG 2.2 AA Requirements

```css
/* Focus Outline (3px minimum) */
button:focus-visible {
    outline: 3px solid var(--cm-accent);
    outline-offset: 2px;
}

/* Touch Target (24px minimum — 44px recommended) */
.button {
    min-height: 44px;
    min-width: 44px;
    padding: 12px 24px;
}

/* Contrast (4.5:1 for normal text, 3:1 for large) */
body {
    color: var(--cm-text);        /* white on dark purple = 13.88:1 ✅ */
    background: var(--cm-bg);     /* #0d0221 */
}
```

---

## Architecture Rules (CoreMusic 4-Layer L0-L3)

### Layer Bindings

| Layer | L0 | L1 | L2 | L3 |
|-------|----|----|----|----|
| **L0 — Infrastructure** | ✅ | ✅ | ✅ | ❌ |
| **L1 — Security** | - | ✅ | ✅ | ❌ |
| **L2 — Router** | - | - | ✅ | ✅ |
| **L3 — Presentation** | - | - | - | ✅ |

**Strict Rule:** L3 → L2 → L1 → L0 (downward only)

### File Layer Mapping

```
L3 Presentation:
  - main.js (entry point)
  - pages/*.php (view layer)
  - AuthHandler.js, HomeHandler.js

L2 Router:
  - Router.js
  - PageRouter.php
  - RouteRegistry.php

L1 Security:
  - GuardPipeline.js
  - MiddlewarePipeline.php
  - AuthMiddleware.php, CsrfMiddleware.php

L0 Infrastructure:
  - FetchWrapper.js
  - CacheLayer.js
  - LifecycleManager.js
  - Logger.js
```

---

## API Standards (CoreMusic Routes)

### Route Security

```php
// Every route must be in RouteRegistry::$routes
'home' => [
    'path'          => '/home',
    'controller'    => 'HomeController',
    'method'        => 'GET',
    'requiresAuth'  => true,
    'requiredRole'  => 'user',      // RBAC
    'cacheable'     => true,
    'cacheType'     => 'user',      // TTL: 60s
],
```

### Middleware Order (IMMUTABLE)

```
1. SessionManager     ← Must read session FIRST for CSP nonce
2. BypassAuth        ← Allow dev bypass
3. RateLimiter       ← 60 req/60s per IP (APCu)
4. Auth              ← Check user authenticated
5. SecurityHeaders   ← CSP, HSTS, X-Frame-Options (uses session nonce)
6. CSRF              ← Validate csrf_token
```

---

## Security Hardening (CoreMusic)

### OWASP Top 10:2025 Compliance

| Vulnerability | Rule | Implementation |
|---------------|------|-----------------|
| Injection | PDO prepared statements | ✅ Mandatory |
| CSRF | hash_equals token validation | ✅ Mandatory |
| XSS | Output encoding + CSP nonce | ✅ Mandatory |
| Broken Auth | session_regenerate_id | ✅ After login |
| Insecure Data | AES-256-GCM for stored data | ✅ credential_vault |
| XXE | XML parser disabled | ✅ By default (no XML parsing) |
| Access Control | RBAC + route-level auth | ✅ RouteRegistry |
| Crypto | No MD5/SHA-1, use Argon2id | ✅ Mandatory |
| Logging | Audit trail + traceId | ✅ Mandatory |
| Config | .env + secret management | ✅ Mandatory |

---

## Testing Requirements (CoreMusic)

### PHP Backend

```bash
# Middleware tests (7 files, 66 tests)
vendor/bin/phpunit test/Unit/Middleware/

# All tests
vendor/bin/phpunit

# Static analysis
vendor/bin/phpstan analyse
composer audit
```

### JavaScript Frontend

```bash
# All tests (unit + integration + router)
npx vitest run

# Watch mode
npx vitest

# Single test
npx vitest run assets.coremusic.net/js/test/Router.test.js
```

---

## Performance Targets (CoreMusic)

| Metric | Target |
|--------|--------|
| Lighthouse | 95+ |
| FCP (First Contentful Paint) | < 1.0s |
| LCP (Largest Contentful Paint) | < 1.5s |
| CLS (Cumulative Layout Shift) | < 0.05 |
| TTI (Time to Interactive) | < 2.5s |
| TBT (Total Blocking Time) | < 100ms |

---

## Accessibility Requirements (CoreMusic)

| SC | Requirement | Implementation |
|----|-------------|-----------------|
| 1.4.3 | Contrast ≥ 4.5:1 | ✅ Color token pairs |
| 1.4.10 | Reflow at 320px | ✅ Mobile-first CSS |
| 2.5.8 | Touch targets ≥ 24px | ✅ min-height, min-width |
| 3.2.4 | Consistent identification | ✅ Class naming, icon semantics |
| Keyboard | Tab order, focus visible | ✅ focus-visible outline 3px |
| Semantic HTML | h1, nav, main, form labels | ✅ Semantic tags |
| ARIA | aria-label, aria-describedby | ✅ Where HTML insufficient |

---

## Checklist (CoreMusic Integration)

```
[ ] declare(strict_types=1) in all PHP files
[ ] PDO prepared statements (no raw SQL)
[ ] Constructor DI (no static)
[ ] csrf_token key (not _csrf_token)
[ ] hash_equals() for CSRF validation
[ ] No var, const/let only in JS
[ ] AbortController for every fetch
[ ] Event listener cleanup on unmount
[ ] Router.navigate() (L3 → L2)
[ ] DOM patch → updateCsrf() order
[ ] ITCSS 7-layer structure
[ ] CSS custom properties (--cm-*)
[ ] 7 breakpoints (320px-3840px)
[ ] WCAG 2.2 AA compliance
[ ] Focus outline 3px
[ ] Touch targets 44px (min 24px)
[ ] Layer bindings correct (L3→L2→L1→L0)
[ ] Middleware order (Session→...→CSRF)
[ ] RateLimiter 60/60s
[ ] Password Argon2id
[ ] Session regenerate_id on login
[ ] Audit logging
[ ] Tests passing (unit + integration)
[ ] Lighthouse 95+
[ ] FCP < 1.0s, LCP < 1.5s
[ ] No SELECT *
[ ] No eval()
[ ] No inline event handlers
[ ] No unserialize(user_input)
[ ] No MD5/SHA-1 hashing
[ ] OWASP Top 10:2025 compliance
[ ] Persona detection (localStorage)
[ ] CSP nonce injection
[ ] No color-only information (icons + text for errors)
```

---

## References

- `.ai/architecture/l0-infrastructure.md` — L0 design details
- `.ai/architecture/l1-security.md` — L1 middleware
- `.ai/api/middleware-pipeline.md` — Middleware order & implementation
- `.ai/ui-design/index.md` — Design system
- `.ai/database/security-hardening.md` — DB rules
- `.ai/decisions/index.md` — All 36+ ADRs

---

**Last Updated:** 2026-06-11 | CoreMusic Ecosystem Phase 2
