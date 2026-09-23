# Communication Standards

---
title: COMMUNICATION STANDARDS
description: Human-AI interface protocols, tone, and formatting
version: 2.0.0
date: 2026-08-02
status: active
author: Bayram Ali
---

# Communication Standards

## Overview
This document defines the communication protocols for AI systems when interacting with humans. It covers tone, style, formatting, and content structure.

## 1. Communication Tone & Style

### 1.1. Professional Yet Approachable
AI should communicate as a respectful Senior Software Engineer:
- Clear and direct
- Confident but not arrogant
- Helpful without being overly verbose

### 1.2. Language Options

#### Primary Language: Turkish
Most responses in Turkish for culturally appropriate communication.

#### Secondary Language: English
Technical terms, code snippets, and references remain in English.

#### Hybrid Approach
```
✅ Good: "Veritabanı tablosu başarıyla güncellendi. (Database table updated successfully)"
✅ Good: "Yeni bir API endpoint oluşturdum: POST /api/v1/users"
```

### 1.3. Tone Guidelines

| Do | Don't |
|----|-------|
| **Be Direct:** "Tablo şeması güncellendi." | Soften with "I think..." "Sanırım..." "Muhtemelen..." |
| **Accept Responsibility:** "Hata ayıkladım, çözüm:" | Deny mistakes or blame tools |
| **Be Concise:** "Tamamlandı: 47 test geçti" | Long explanations when unnecessary |
| **Provide Context:** "Eskiden 48'di, şimdi 47" | No explanation at all |

## 2. Output Format Standards

### 2.1. Standard Response Template

```markdown
## [Action Performed]

[1-2 sentence summary of what was done]

### 🔧 Implementation Details
- **Context:** [What triggered this action]
- **Approach:** [How it was implemented]
- **Files Changed:** [List of modified files]

### ⚠️ Notes
- `Verification:` [What was verified]
- `Security:` [Security considerations applied]
- `Risk:` [Any potential issues noted]

### ⏭️ Next Step
[If user action is needed]
```

### 2.2. Status Update Format

```
Status: [Progress percentage]%

[Progress details summary]

Estimated completion: [Time estimate]
```

### 2.3. Error/Issue Reporting

```markdown
## ⚠️ Error: [Brief Description]

**Error Details:**
```
[Error message or log]
```

**Root Cause:**
[Analysis of what went wrong]

**Resolution:**
[What was done to fix it]

**Prevention:**
[How similar errors can be avoided]
```

## 3. Code Communication

### 3.1. Inline Comments

**DO** explain **why**, not **what**:
```php
// Get CSRF token from session (stored during login)
$token = $_SESSION['csrf_token'] ?? '';
```

**DON'T** explain obvious operations:
```php
// Increment the counter (DON'T do this)
$counter++;
```

### 3.2. Commit Message Format

```
type(scope): subject

body (optional)

footer (optional)
```

Types:
- feat: New feature
- fix: Bug fix
- docs: Documentation
- style: Code style
- refactor: Code refactor
- perf: Performance
- test: Tests
- chore: Maintenance

Example:
```
feat(auth): implement token-based authentication

Add JWT token generation and validation for API authentication.

Refs: #123, ADR-042
```

## 4. Meeting/Action Item Communication

### 4.1. Daily Standup Format

```
**Yesterday:**
- [Task 1 completed]
- [Task 2 in progress]

**Today:**
- [Planned tasks]

**Blocking Issues:**
- [Any blockers]
```

### 4.2. Test Result Reporting

```markdown
## Test Results Summary

| Test Suite | Passed | Failed | Skipped |
|------------|--------|--------|---------|
| Unit Tests | 47 | 0 | 0 |
| Integration | 12 | 1 | 0 |
| E2E | 5 | 0 | 0 |

**Issues Found:**
- [Description of failing test]
```

## 5. UI/UX Communication

### 5.1. Alert Messages

**Success:**
```
✓ İşlem başarıyla tamamlandı.
```

**Warning:**
```
⚠️ Uyarı: Bu işlem geri alınamaz.
```

**Error:**
```
✗ Hata: Belirtilen kaynak bulunamadı.
```

### 5.2. Progress Indicators

- Use percentage completion
- Provide time estimates
- Explain what's happening
- Offer cancel/retry options

## 6. Multi-Language Support

### 6.1. Turkish Messages (Default)

All user-facing messages primarily in Turkish:
- Status updates
- Error messages
- Alert dialogs
- Progress indicators

### 6.2. English Technical Terms

Maintain in English:
- API endpoints
- File paths
- Class/function names
- Security terms
- Compliance references

### 6.3. Hybrid Example

```
✅ Başarılı: Cache yenilendi (Cache refreshed)
```