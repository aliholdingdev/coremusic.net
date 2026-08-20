# Project State — 2026-08-20

## Current Task: Fix Login Bug + Gender-Based Login Restriction
## Status: COMPLETED

## Changes Made:
1. `shared/src/PageRouter\ResponseEmitter.php` — session_write_close() before exit
2. `shared/src/Exception/AuthenticationException.php` — Turkish gender mismatch message
3. `auth.coremusic.net/include/Service/AuthService.php` — gender checks in login() and register()
4. `auth.coremusic.net/include/Controller/AuthController.php` — pass visitor gender to register()
5. `shared/src/Interfaces/Auth/IAuthService.php` — updated register() signature

## Architecture Notes:
- L2 Routing — PageRouterKernel handles all requests
- Session-based CSRF with `csrf_token` key
- Gender stored in session as `cm_gender` and cookie as `cm_gender`
- User DB gender: ENUM(male|female|neutral)

## Known Issues:
- Previous session: ERR_TOO_MANY_REDIRECTS on home.coremusic.net (login redirect loop)
- This session fixed login race condition + added gender restriction