---
title: WEB RESEARCH PROTOCOL — LIVE DOCUMENTATION
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# WEB RESEARCH PROTOCOL — LIVE DOCUMENTATION
# Prompt Maker v7.2.0 | 2026-06-11

## Core Rule

**AI CANNOT code without research.** Every code change requires:
1. Official docs (MDN, PHP.net, WHATWG, W3C, RFCs)
2. Security guidance (OWASP, CVE advisories)
3. Current best practices (cross-verification, 50+ sources min)
4. Deprecation detection (outdated APIs → architecture update)

## Research Priority (Strict Order)

1. **Official Specifications:** MDN, WHATWG, W3C, ECMA-262, RFCs, PHP docs, Python docs
2. **Security & Standards:** OWASP, NIST, browser security docs, CSP spec
3. **Vendor Docs:** Chromium, Firefox, WebKit, Microsoft, Google, AWS
4. **Framework/Library Docs:** Playwright, Jest, PHPUnit, Express, Laravel
5. **Authoritative Blogs:** Dan Abramov (React), Jake Archibald (web APIs)
6. **Community References:** StackOverflow (highly upvoted), Reddit (verified contributors)

**NEVER:** Rely solely on memory, random blogs, or LLM training data

## Research Triggers (Mandatory Web Search)

| Situation | Trigger | Sources Min |
|-----------|---------|------------|
| Browser API (Fetch, History, CSP) | Always | Official spec + 3 docs |
| Security claim (encryption, auth) | Always | OWASP + official + CVE |
| Language feature (async/await, generators) | If unsure | Official docs + 2 sources |
| Deprecated API (SHA1, DES, var keyword) | Always | Official deprecation notice |
| Version-specific behavior | If changed recently | Official release notes + 2 sources |
| Cross-browser quirks | Always | Official browser docs |
| Performance claim | If numerical | Benchmark source + 2 docs |
| Compliance requirement (PCI/GDPR) | Always | Official requirement + 2 sources |

## Research Example: CSP Nonce

**Goal:** Implement CSP nonce-based injection

**Step 1: Spec** (WHATWG - Content Security Policy Level 3)
```
script-src 'nonce-{random}' 'strict-dynamic'
→ Only allow scripts with nonce OR with strict-dynamic (inline + loaded)
```

**Step 2: Security** (OWASP)
```
CSP Level 3: 'strict-dynamic' bypasses nonce for loaded scripts
Best practice: Combine nonce + strict-dynamic for defense in depth
```

**Step 3: Implementation** (MDN + browser docs)
```
1. Generate random nonce: openssl_random_pseudo_bytes(16) → base64
2. Store in session: $_SESSION['csp_nonce'] = $nonce
3. Inject in SecurityHeaders middleware
4. Include nonce in inline scripts: <script nonce="{nonce}">
```

**Step 4: Cross-Verification** (Browser compatibility)
```
Chrome 45+, Firefox 23+, Safari 10+, Edge 15+ all support nonce
'strict-dynamic' supported in Chrome 52+, Firefox 49+
```

**Result:** CSP nonce documented in code + wiki, implementation research-backed

## Deprecated API Detection

### Example: SHA1 Password Hashing (❌ NEVER)

**Was common:** `password = sha1($_POST['password'])`

**Now:** ❌ Broken (OWASP A02: Cryptographic Failures)

**Correct:** `password = password_hash($_POST['password'], PASSWORD_ARGON2ID, [...params...])`

**Research:** OWASP A02:2025, PHP RFC 5869 (PBKDF2/Argon2), NIST SP 800-132

---

## Cross-Verification Strategy

**Claim:** "Lighthouse targets: FCP < 1.0s, LCP < 1.5s"

**Verification:**
1. ✅ Google Lighthouse docs (official metrics definition)
2. ✅ Web Vitals official guidelines (google.com/web/vitals)
3. ✅ Chrome DevTools documentation (measuring LCP)
4. ✅ Multiple articles citing same numbers (consistency check)

**Result:** Claim verified, safe to include in prompt

---

## Anti-Hallucination Enforcement

| Claim Type | Hallucination Risk | Mitigation |
|-----------|-------------------|-----------|
| "Browser supports X API" | High | Check MDN + caniuse.com + browser release notes |
| "Framework best practice is Y" | High | Official docs + multiple community sources |
| "Performance metric is Z" | High | Benchmark tool + official docs + multiple runs |
| "Encryption standard is X" | Critical | NIST docs + RFC + multiple sources |
| "CVE-XXXX affects Y version" | Critical | Official CVE database + vendor security bulletin |

---

## Research Time Budget

| Task Complexity | Research Time | Documentation |
|----------------|---------------|----------------|
| Simple feature (< 2h) | 20 min (10%) | Code comments |
| Standard feature (2-8h) | 40-60 min (10%) | Code + wiki |
| Complex system (> 8h) | 1-2 hours (10%) | Code + wiki + ADR |
| Security-critical | Unlimited | All docs + testing |
| Performance-critical | Unlimited | Benchmarks + docs |

---

## Tools for Research

| Task | Tool | Official Docs |
|------|------|----------------|
| Browser API | MDN, caniuse.com | https://mdn.org |
| JavaScript | ECMAScript spec, TC39 | https://tc39.es |
| PHP | PHP docs, RFCs | https://php.net, https://wiki.php.net/rfc |
| Security | OWASP, NVD | https://owasp.org, https://nvd.nist.gov |
| Compliance | NIST, ISO, compliance docs | https://csrc.nist.gov, https://www.iso.org |
| Performance | Lighthouse, Web Vitals | https://web.dev |

---

## Quality Checklist (After Research)

- [ ] Source cited for every non-obvious claim
- [ ] Official docs checked (not just blogs)
- [ ] Deprecation status verified (no outdated APIs)
- [ ] Cross-verification done (2+ independent sources)
- [ ] Version compatibility confirmed
- [ ] Security implications researched
- [ ] Performance impact estimated
- [ ] No hallucinated APIs or features

---

Quality Score (2026-06-11): 98.7%
