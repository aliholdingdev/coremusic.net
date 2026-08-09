# WEB RESEARCH PROTOCOL — LIVE DOCUMENTATION

---
title: WEB RESEARCH PROTOCOL — LIVE DOCUMENTATION
description: Mandatory web search triggers, source validation, research quality
version: 2.0.0
date: 2026-08-02
status: active
author: Bayram Ali
---

# Web Research Protocol (CORE SYSTEM)

## Overview
This document defines the web search and research requirements for AI systems. It covers:
- **Mandatory web search triggers**
- **Source validation** 
- **Search depth and quality**
- **Rate limiting**
- **Error handling**
- **Documentation requirements**

## 1. Research Priority (Mandatory Order)

### 1. Official Documentation
Primary sources that must be consulted before any other research:

| Type | Sources | Purpose |
|------|---------|---------|
| Language Specs | MDN, WHATWG, W3C, ECMA-262, PHP.net, Python docs | Language features, browser APIs |
| Standards | OWASP, NIST, ISO, RFC | Security, protocols, compliance |
| Vendor Docs | Microsoft, Google, AWS, Cloudflare | Platform-specific APIs |
| Framework Docs | React, Vue, Laravel, Express, Playwright, Vitest | Framework usage patterns |

### 2. Secondary Verification
| Type | Sources | Purpose |
|------|---------|---------|
| Community | StackOverflow (high votes), GitHub Issues, Reddit (verified) | Edge cases, workarounds |
| Academic | arXiv, ACM DL, IEEE Xplore | Theoretical foundations |
| Blogs | Dan Abramov, Jake Archibald, Ryan Dahl, trusted authors | Best practices, patterns |

## 2. Research Triggers (Mandatory Web Search)

| Trigger | Description | Minimum Sources |
|---------|-------------|-----------------|
| Browser API (Fetch, History, CSP) | Always | Official spec + 3 docs |
| Security claim (encryption, auth) | Always | OWASP + official + CVE |
| Language feature (async/await, generators) | If unsure | Official docs + 2 sources |
| Deprecated API (SHA1, DES, var keyword) | Always | Official deprecation notice |
| Cross-browser quirks | Always | Official browser docs |
| Performance claim | If numerical | Official release notes + 2 sources |
| Compliance requirement | Always | Official requirement + 2 sources |

## 3. Source Validation Rules

### 3.1. Accepted Sources (Priority Order)
1. **Official Specifications** - MDN, WHATWG, W3C, ECMA-262, RFCs
2. **Vendor Documentation** - Microsoft, Google, Apple, AWS official docs
3. **Standards Bodies** - OWASP, NIST, ISO, IETF
4. **Framework/Library Official** - Playwright, Vitest, PHPUnit, Laravel
5. **Verified Community** - StackOverflow (50+ votes), GitHub (maintainer responses)

### 3.2. Rejected Sources (Penalized)
- Personal blogs (unless official author)
- Medium articles (low credibility)
- Wikipedia (not primary technical source)
- Quora/Reddit (anecdotal)
- >5 year old content without version context

## 4. Search Quality Requirements

- **Cross-Verification**: Minimum 3 independent sources for critical claims
- **Recency Filter**: Sources older than Jan 2024 = -10 points, Jan 2022 = -15 points
- **Version Context**: Must specify version tested/verified against
- **Confidence Score**: All research must include confidence percentage

## 5. Documentation Format

Research findings must be documented as:
```markdown
## Research: [Topic]

**Sources:**
1. [Source 1 - URL + relevance]
2. [Source 2 - URL + relevance]
3. [Source 3 - URL + relevance]

**Confidence:** [XX%]

**Key Findings:**
- [Finding 1 with citation]
- [Finding 2 with citation]

**Implementation Notes:**
- [How to apply]
```