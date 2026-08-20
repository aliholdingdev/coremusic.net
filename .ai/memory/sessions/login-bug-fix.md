# Session Summary — Login Bug + Redirect Loop Fix

## Goal
Fix login bug (first attempt fails, second succeeds) and `ERR_TOO_MANY_REDIRECTS` on `home.coremusic.net:81/home`.

## Status: IN PROGRESS

---

## Fixes Applied (8 files)

| # | File | Fix |
|---|---|---|
| 1 | `login.php:141` | `e.stopImmediatePropagation()` — dual form handler |
| 2 | `gender-select.js:36` | `e.stopImmediatePropagation()` — dual form handler |
| 3 | `register.php:195` | `e.stopImmediatePropagation()` — dual form handler |
| 4 | `SessionInitializer.php:104` | `return false` when `_session_created_at` null (was `return true`) |
| 5 | `auth.coremusic.net/index.php:66-68` | `@mkdir($savePath)` in `cm_session_start()` |
| 6 | `home.coremusic.net/index.php:81-83` | `@mkdir` in auth callback session init |
| 7 | `auth_callback.php:30-32` | `@mkdir` in session init |
| 8 | `HomeAuthBridge.php:144` | `CURLOPT_FOLLOWLOCATION => true` |

---

## Current Problem: ERR_TOO_MANY_REDIRECTS

Login succeeds on auth.coremusic.net → auth_key created → redirect to `home.coremusic.net/auth/callback?auth_key=xxx` → session created → redirect to `/home` → AuthGuard says "not authenticated" → redirect to auth → auth says "already authenticated" → redirect back to home → **LOOP**

### Root Cause (suspected)
Session data format mismatch between what `HomeAuthBridge::validateAndCreateSession()` writes and what `AuthGuard` reads.

### Next Steps
1. Read `shared/src/PageRouter/AuthGuard.php`
2. Read `shared/config/routes.php`
3. Read `shared/src/Middleware/AuthMiddleware.php`
4. Read `home.coremusic.net/include/Auth/HomeAuthBridge.php`
5. Trace session keys and fix mismatch

### Key Files
```
shared/src/PageRouter/AuthGuard.php
shared/src/Middleware/AuthMiddleware.php
shared/config/routes.php
home.coremusic.net/pages/auth_callback.php
home.coremusic.net/include/Auth/HomeAuthBridge.php
auth.coremusic.net/include/Service/SessionManager.php
```