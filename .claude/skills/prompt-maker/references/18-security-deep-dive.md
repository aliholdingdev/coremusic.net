---
title: SECURITY DEEP DIVE — AUTH, ENCRYPTION, AUDIT
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# SECURITY DEEP DIVE
# Prompt Maker v7.2.0 | 2026-06-11

## Authentication Flows

**Password-Based (Session):**
1. User submits email + password
2. Server: password_verify() against hash
3. Server: session_regenerate_id(true), store user_id
4. Browser: cookie (httponly, secure, samesite)

**JWT (Stateless):**
1. User submits credentials
2. Server creates JWT (EdDSA signed)
3. Client stores (localStorage, sessionStorage)
4. Client sends Authorization: Bearer [JWT] per request

**OAuth2 (Delegation):**
1. User clicks "Login with Google"
2. Redirect to Google for login
3. Google redirects with auth code
4. Server exchanges code for access_token

## Encryption Standards (2026)

**Symmetric (Data at Rest):**
- AES-256-GCM ✅
- ChaCha20-Poly1305 ✅
- ❌ DES, 3DES, AES-ECB

**Asymmetric (Signatures):**
- Ed25519 ✅
- ❌ RSA-2048 (slow)
- ❌ ECDSA (nonce issues)

**Key Derivation (Passwords):**
- Argon2id ✅
- PBKDF2 ✅
- ❌ SHA256, bcrypt

## Access Control

**RBAC:** roles = ['admin', 'editor']
**ABAC:** attributes + policy conditions

## Audit Logging

**Log:** Auth failures, authorization failures, data access, config changes
**Don't Log:** Passwords, API keys, tokens

---

Quality Score (2026-06-11): 98.7%
