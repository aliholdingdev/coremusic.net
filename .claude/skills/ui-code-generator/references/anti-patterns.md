# Common Anti-Patterns in UI Design-to-Code

Avoid these mistakes when converting design to production code.

---

## Layout Anti-Patterns

### ❌ 1. Magic Numbers (CSS)

```css
/* WRONG */
.button {
    padding: 13px 27px;
    margin: 17px;
    width: 314px;
}

/* RIGHT */
.button {
    padding: var(--space-md) var(--space-lg);
    margin: var(--space-lg);
    width: 100%;  /* or use grid column span */
}
```

**Why:** Magic numbers are unmaintainable. If design tokens change, you must hunt through hundreds of files.

---

### ❌ 2. Fixed-Width Desktop Layout (No Responsive)

```css
/* WRONG */
.container {
    width: 1200px;  /* Fixed */
}

/* RIGHT */
.container {
    max-width: 1200px;
    width: 100%;
    padding: var(--space-md);
}

@media (min-width: 1280px) {
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
}
```

**Why:** Fixed width breaks on mobile, tablets, and ultra-wide screens.

---

### ❌ 3. Nested Media Queries

```css
/* WRONG */
.grid {
    @media (min-width: 768px) {
        @media (max-width: 1024px) {
            /* Very fragile */
        }
    }
}

/* RIGHT */
.grid { grid-template-columns: 1fr; }
@media (min-width: 768px) { .grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .grid { grid-template-columns: repeat(3, 1fr); } }
```

**Why:** Nested media queries are hard to override and maintain. Flat structure is clearer.

---

### ❌ 4. Using Floats for Layout

```css
/* WRONG */
.sidebar { float: left; width: 30%; }
.main { float: right; width: 70%; }

/* RIGHT */
.container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: var(--space-lg);
}
```

**Why:** Floats are legacy. CSS Grid is more flexible and responsive.

---

### ❌ 5. Overflow Hidden Instead of Scrollable

```css
/* WRONG */
.modal-body {
    max-height: 400px;
    overflow: hidden;  /* Content cut off */
}

/* RIGHT */
.modal-body {
    max-height: 400px;
    overflow-y: auto;  /* Scrollable */
}
```

**Why:** Hidden overflow truncates content. Users can't access it.

---

## Accessibility Anti-Patterns

### ❌ 6. Focus Indicator Removed

```css
/* WRONG */
button:focus { outline: none; }

/* RIGHT */
button:focus-visible {
    outline: 3px solid var(--color-primary);
    outline-offset: 2px;
}
```

**Why:** Users with keyboard navigation can't see where focus is.

---

### ❌ 7. Touch Target Too Small

```css
/* WRONG */
.button {
    padding: 4px 8px;
    height: 20px;  /* Too small */
}

/* RIGHT */
.button {
    padding: 12px 24px;
    min-height: 44px;  /* WCAG AA: 24px minimum, 44px recommended */
}
```

**Why:** Small touch targets cause frustration on mobile devices.

---

### ❌ 8. Color Only for Information

```html
<!-- WRONG -->
<div style="color: red">Error occurred</div>

<!-- RIGHT -->
<div style="color: red" role="alert">
    ⚠️ Error occurred
</div>
```

**Why:** Color-blind users can't tell what happened. Use text + icon.

---

### ❌ 9. No Semantic HTML

```html
<!-- WRONG -->
<div class="navigation">
    <div class="nav-link"><a href="/">Home</a></div>
</div>

<!-- RIGHT -->
<nav>
    <a href="/">Home</a>
</nav>
```

**Why:** Screen readers need semantic HTML to announce navigation.

---

### ❌ 10. Form Inputs Without Labels

```html
<!-- WRONG -->
<input type="email" placeholder="Enter email">

<!-- RIGHT -->
<label for="email">Email Address</label>
<input id="email" type="email" placeholder="name@example.com">
```

**Why:** Labels connect inputs to accessibility tree. Placeholder is not a label.

---

## CSS Anti-Patterns

### ❌ 11. !important Everywhere

```css
/* WRONG */
.button { color: blue !important; }
.button.primary { color: red !important; }
.button.primary:hover { color: darkred !important; }

/* RIGHT */
.button { color: blue; }
.button--primary { color: red; }
.button--primary:hover { color: darkred; }
```

**Why:** !important breaks cascading and makes debugging impossible.

---

### ❌ 12. Inline Styles

```html
<!-- WRONG -->
<div style="color: red; font-size: 16px; margin: 10px;">Text</div>

<!-- RIGHT -->
<div class="text-error">Text</div>
```

**Why:** Inline styles can't be reused, can't use media queries, can't use variables.

---

### ❌ 13. BEM/Naming Confusion

```css
/* WRONG (mixed naming) */
.button { }
.button-primary { }
.buttonPrimary { }
.button__primary { }
.primary-button { }

/* RIGHT (consistent BEM) */
.button { }
.button--primary { }
.button__text { }
```

**Why:** Inconsistent naming makes debugging harder. Pick one convention and stick to it.

---

### ❌ 14. Hardcoded Colors (No Design Tokens)

```css
/* WRONG */
.button { color: #ff4fd8; }
.link { color: #9d4edd; }
.success { color: #4dff4d; }

/* RIGHT */
.button { color: var(--color-accent); }
.link { color: var(--color-primary); }
.success { color: var(--color-success); }
```

**Why:** If design changes, you update CSS variables once, not 50 places.

---

### ❌ 15. Negative Margins for Alignment

```css
/* WRONG */
.title { margin-top: -20px; }  /* Fragile */

/* RIGHT */
.header { display: flex; align-items: center; gap: var(--space-md); }
```

**Why:** Negative margins are hard to debug and context-dependent.

---

## JavaScript Anti-Patterns

### ❌ 16. No AbortController

```javascript
/* WRONG */
async function fetchUser(id) {
    const response = await fetch(`/api/users/${id}`);
    return response.json();
}
// If user navigates away, fetch still completes silently → memory leak

/* RIGHT */
class Router {
    async navigate(url) {
        this.controller?.abort();
        this.controller = new AbortController();
        const response = await fetch(url, { signal: this.controller.signal });
        return response.json();
    }
}
```

**Why:** Without AbortController, old requests complete and update stale state.

---

### ❌ 17. Memory Leak: Event Listeners Not Cleaned Up

```javascript
/* WRONG */
class Widget {
    init() {
        document.addEventListener('click', () => {
            this.handleClick();  // Leaked listener on unmount
        });
    }
}

/* RIGHT */
class Widget {
    init() {
        this.clickHandler = (e) => this.handleClick(e);
        document.addEventListener('click', this.clickHandler);
    }
    destroy() {
        document.removeEventListener('click', this.clickHandler);
    }
}
```

**Why:** Unmounted components with event listeners leak memory and fire stale handlers.

---

### ❌ 18. setTimeout Without Cleanup

```javascript
/* WRONG */
function setupTimer() {
    setInterval(() => {
        console.log('Tick');
    }, 1000);  // Never cleared → runs forever
}

/* RIGHT */
class Component {
    setup() {
        this.timerId = setInterval(() => {
            console.log('Tick');
        }, 1000);
    }
    destroy() {
        clearInterval(this.timerId);
    }
}
```

**Why:** Timers that don't clean up accumulate and waste CPU.

---

### ❌ 19. var Instead of const/let

```javascript
/* WRONG */
var count = 0;
for (var i = 0; i < 10; i++) {
    count += i;
}
console.log(i);  // 10 (leaked to scope)

/* RIGHT */
const count = 0;
for (let i = 0; i < 10; i++) {
    // i is block-scoped
}
console.log(i);  // ReferenceError (correct)
```

**Why:** var is function-scoped and hoisted. const/let are block-scoped and predictable.

---

### ❌ 20. Callback Hell

```javascript
/* WRONG */
fetch(url1).then(r1 => r1.json()).then(data1 => {
    fetch(url2 + data1.id).then(r2 => r2.json()).then(data2 => {
        fetch(url3 + data2.id).then(r3 => r3.json()).then(data3 => {
            console.log(data3);
        });
    });
});

/* RIGHT */
async function loadData() {
    const data1 = await fetch(url1).then(r => r.json());
    const data2 = await fetch(url2 + data1.id).then(r => r.json());
    const data3 = await fetch(url3 + data2.id).then(r => r.json());
    return data3;
}
```

**Why:** async/await is cleaner and easier to debug than promise chains.

---

### ❌ 21. Mutating DOM Directly

```javascript
/* WRONG */
document.innerHTML = userProvidedHTML;  // XSS vulnerability

/* RIGHT */
const parser = new DOMParser();
const doc = parser.parseFromString(html, 'text/html');
const policy = trustedTypes.createPolicy('spa-router', {
    createHTML: (string) => string,
});
const sanitized = policy.createHTML(html);
document.innerHTML = sanitized;
```

**Why:** Direct innerHTML with user input allows XSS attacks.

---

### ❌ 22. Global Variables

```javascript
/* WRONG */
window.appState = { user: null, theme: 'dark' };

/* RIGHT */
class AppState {
    constructor() {
        this.user = null;
        this.theme = 'dark';
    }
}
const appState = new AppState();
```

**Why:** Global variables cause unpredictable state mutations and collisions.

---

## PHP Anti-Patterns

### ❌ 23. Raw SQL Concatenation

```php
/* WRONG */
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];
$result = $pdo->query($sql);

/* RIGHT */
$stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$result = $stmt->execute();
```

**Why:** Raw concatenation allows SQL injection attacks.

---

### ❌ 24. No Input Validation

```php
/* WRONG */
$email = $_POST['email'];
$user = User::create(['email' => $email]);

/* RIGHT */
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    throw new ValidationException('Invalid email');
}
$user = User::create(['email' => $email]);
```

**Why:** Unvalidated input leads to database corruption and security issues.

---

### ❌ 25. Not Encoding Output

```php
<!-- WRONG -->
<div><?= $_GET['name'] ?></div>

<!-- RIGHT -->
<div><?= htmlspecialchars($_GET['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
```

**Why:** Unencoded user input allows XSS attacks.

---

### ❌ 26. SELECT * (Performance + Security)

```php
/* WRONG */
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');

/* RIGHT */
$stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = :id');
```

**Why:** SELECT * fetches unnecessary columns (including passwords!), wastes bandwidth, and exposes secrets.

---

### ❌ 27. unserialize(user_input)

```php
/* WRONG */
$data = unserialize($_COOKIE['data']);

/* RIGHT */
$data = json_decode($_COOKIE['data'], true);
```

**Why:** unserialize() with user input can execute arbitrary PHP code (RCE).

---

### ❌ 28. MD5/SHA-1 for Passwords

```php
/* WRONG */
$hash = md5($password);
$hash = sha1($password);

/* RIGHT */
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost'   => 4,
    'threads'     => 2,
]);
```

**Why:** MD5/SHA-1 are fast to crack with GPU. Argon2id is slow and memory-hard.

---

### ❌ 29. No Exception Handling

```php
/* WRONG */
$pdo->query('SELECT * FROM users');  // No error handling

/* RIGHT */
try {
    $stmt = $pdo->prepare('SELECT id, name FROM users WHERE id = :id');
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    Log::error('Database error', ['exception' => $e]);
    throw new DatabaseException('Could not fetch user');
}
```

**Why:** Without error handling, database errors expose internal details to attackers.

---

### ❌ 30. Not Regenerating Session ID

```php
/* WRONG */
if (isset($_POST['login']) && authenticate($_POST['email'], $_POST['password'])) {
    $_SESSION['user_id'] = $user->id;
    // Session ID unchanged → session fixation risk
}

/* RIGHT */
if (isset($_POST['login']) && authenticate($_POST['email'], $_POST['password'])) {
    session_regenerate_id(true);  // true = delete old session file
    $_SESSION['user_id'] = $user->id;
}
```

**Why:** Without regeneration, attackers can hijack the session using a fixed ID.

---

## Design System Anti-Patterns

### ❌ 31. No Component State Matrix

```
/* WRONG */
Design only shows: Button (DEFAULT state)
No documentation of:
  - HOVER
  - ACTIVE
  - FOCUSED
  - DISABLED
  - LOADING
  - ERROR

/* RIGHT */
Every component has full state matrix:
| State | Background | Text | Border |
|-------|----------|------|--------|
| DEFAULT | #9d4edd | white | #9d4edd |
| HOVER | #8a3fac | white | #8a3fac |
| ACTIVE | #7a3b9d | white | #7a3b9d |
| FOCUSED | #9d4edd | white | #ff4fd8 (3px outline) |
| DISABLED | #666 | #ccc | #999 |
| LOADING | #9d4edd | white | spinning |
| ERROR | #ff4d4d | white | #ff4d4d |
```

**Why:** Without a state matrix, developers guess how components should look in different states.

---

### ❌ 32. Inconsistent Spacing

```
/* WRONG */
Padding: 10px, 12px, 16px, 18px, 20px, 24px (random)

/* RIGHT */
Spacing scale: 8px unit
  4px, 8px (1×), 16px (2×), 24px (3×), 40px (5×), 64px (8×)
  All spacing is multiple of 8
```

**Why:** Random spacing makes layout unpredictable. A scale ensures consistency.

---

### ❌ 33. No Dark Theme Support

```css
/* WRONG */
:root {
    --color-bg: white;
    --color-text: black;
}
/* Dark theme doesn't exist */

/* RIGHT */
:root {
    --color-bg: white;
    --color-text: black;
}
@media (prefers-color-scheme: dark) {
    :root {
        --color-bg: #0d0221;
        --color-text: white;
    }
}
```

**Why:** Dark mode is essential for modern UX. Many users prefer it for battery/eye comfort.

---

## Testing Anti-Patterns

### ❌ 34. No Viewport Testing

```
/* WRONG */
Test only on desktop (1920×1080)
No mobile, tablet, TV testing

/* RIGHT */
Test on all 7 breakpoints:
  320px (mobile), 600px (wide mobile), 768px (tablet), 1024px (wide tablet),
  1280px (desktop), 1920px (wide), 3840px (4K)
```

**Why:** 50%+ traffic is mobile. Not testing mobile means broken experience for half your users.

---

### ❌ 35. No Accessibility Testing

```
/* WRONG */
No keyboard navigation testing
No screen reader testing
No contrast testing

/* RIGHT */
Manual testing:
  - Tab through all interactive elements
  - Test with VoiceOver/NVDA
  - Check contrast with WebAIM contrast checker
Automated:
  - axe DevTools scan
  - Lighthouse audit
```

**Why:** Accessibility isn't a nice-to-have. It's a legal requirement (ADA, WCAG).

---

## Performance Anti-Patterns

### ❌ 36. No Asset Optimization

```
/* WRONG */
Images: 5MB PNG (not optimized)
CSS: All 7 layers bundled unminified
JS: All features in one 200KB file

/* RIGHT */
Images: WebP format, optimized, ~500KB
CSS: Minified, gzipped
JS: Code splitting, lazy loading, minified
```

**Why:** Unoptimized assets cause slow load times. Users abandon slow sites.

---

### ❌ 37. No Caching Strategy

```
/* WRONG */
Every page refresh fetches all assets from server
No cache headers

/* RIGHT */
Set Cache-Control headers:
  Static assets (CSS, fonts): 1 year (long-lived)
  HTML: 1 minute (revalidate)
  API responses: custom per endpoint
```

**Why:** Without caching, repeat visits are slow.

---

## Summary

**The 37 anti-patterns above cover:**
- ✅ 7 Layout patterns
- ✅ 5 Accessibility patterns
- ✅ 6 CSS patterns
- ✅ 7 JavaScript patterns
- ✅ 6 PHP patterns
- ✅ 3 Design System patterns
- ✅ 2 Testing patterns
- ✅ 2 Performance patterns

**Use this list as a checklist when reviewing generated code.**
