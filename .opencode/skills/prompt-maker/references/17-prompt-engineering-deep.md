---
title: PROMPT ENGINEERING — ADVANCED TECHNIQUES (2026)
description: Zero-shot, Few-shot, CoT (died on reasoning models), ReAct, Self-consistency, Structured outputs, Meta-prompting
version: 11.0.0
updated: 2026-08-15
framework: PICCO
quality-score: "95%+"
---

# PROMPT ENGINEERING — ADVANCED TECHNIQUES (2026)
# Prompt Maker v11.0.0 | 2026-08-15

---

## TECHNIQUE STATUS (2026)

| Technique | Status | When to Use |
|-----------|--------|-------------|
| Zero-shot | ✅ WORKS | Default start — always try first |
| Few-shot | ✅ WORKS | Format locking, 3-5 examples only |
| Role/Persona | ✅ WORKS | Tone, format, expertise |
| Task Decomposition | ✅ WORKS | Complex multi-step tasks |
| Prompt Chaining | ✅ WORKS | Sequential operations |
| Self-Consistency | ✅ WORKS | Accuracy critical |
| Structured Outputs | ✅ WORKS | Production APIs (JSON/XML) |
| Meta-prompting | ✅ WORKS | Prompt optimization |
| **CoT (reasoning models)** | ❌ DIED | Redundant; -36.3% accuracy |
| **Heavy few-shot (default)** | ❌ DIED | Zero-shot first, few-shot fallback |
| **Response prefilling** | ❌ DIED | 400 error on Claude 4.6+ |
| **Manual budget_tokens** | ❌ DIED | Use effort parameter |

---

## 1. ZERO-SHOT PROMPTING

**Technique:** Clear instruction without examples

```
PROMPT:
"Classify the sentiment of the following text as POSITIVE, NEGATIVE, or NEUTRAL:
'I absolutely love this product, it changed my life!'"
```

**Benefit:** Simple, fast, no example engineering needed
**When:** Default start for all tasks

---

## 2. FEW-SHOT PROMPTING

**Technique:** 3-5 examples to shape output format

```
EXAMPLES:
Input: "I love this movie" → Output: POSITIVE
Input: "This is terrible" → Output: NEGATIVE
Input: "It was okay" → Output: NEUTRAL

TASK:
Input: "Absolutely amazing experience!" → Output: ?
```

**Benefit:** Locks output format, reduces drift
**When:** Zero-shot insufficient, format consistency needed
**Rule:** 3-5 examples max; try zero-shot first

---

## 3. ROLE / PERSONA PROMPTING

**Technique:** Assign functional role (not theatrical)

```
"You are a senior PHP architect specializing in security and performance.
You follow OWASP Top 10 2025, use PDO prepared statements, and never guess.
When unsure, say 'I don't know' and research."
```

**Benefit:** Consistent tone, expertise level, decision-making
**When:** Every system prompt

---

## 4. CHAIN-OF-THOUGHT (CoT) — STATUS: DIED ON REASONING MODELS

### 4.1 What It Was

Ask model to reason step-by-step before answering:

```
"I have 5 apples. I eat 2 yesterday. How many remain?
Let's think step by step."
```

### 4.2 Why It Died in 2026

| Issue | Evidence |
|-------|----------|
| Redundant on reasoning models | OpenAI: "Avoid CoT prompts — models reason internally" |
| Can hurt performance | arXiv 2410.21333: up to -36.3% accuracy |
| Over-spends compute | arXiv 2412.21187: trivial problems get excessive thinking |

### 4.3 When CoT Still Works

- Non-reasoning models (GPT-4o, Claude 3.5)
- Math/logic tasks on classic models
- Manual fallback when thinking is disabled

### 4.4 Modern Alternative

Use `effort` parameter or adaptive thinking instead of explicit CoT.

---

## 5. TASK DECOMPOSITION

**Technique:** Break complex tasks into sub-tasks

```
COMPLEX TASK: "Build a full authentication system"

DECOMPOSED:
1. Design database schema (users, sessions, tokens)
2. Implement password hashing (Argon2id)
3. Create login endpoint (rate limiting, CSRF)
4. Add session management (cookie flags)
5. Write unit tests (80% coverage)
```

**Benefit:** Manageable chunks, easier validation
**When:** Multi-step, complex tasks

---

## 6. PROMPT CHAINING

**Technique:** Sequential prompts, each feeding the next

```
CHAIN:
Step 1: "Analyze this code for security vulnerabilities"
Step 2: "For each vulnerability found, suggest a fix"
Step 3: "Generate the fixed code with tests"
```

**Benefit:** Each step validates before proceeding
**When:** Sequential operations, quality-critical

---

## 7. SELF-CONSISTENCY

**Technique:** Multiple paths + majority vote

```
1. Generate 5 different answers to same question
2. Compare results
3. Select most consistent answer
4. Flag disagreement as uncertainty
```

**Benefit:** Higher accuracy, uncertainty detection
**When:** Accuracy critical, multiple valid approaches

---

## 8. STRUCTURED OUTPUTS

**Technique:** Enforce JSON/XML schema

```
TASK: Generate user data
OUTPUT FORMAT:
{
  "name": "string",
  "email": "string",
  "role": "admin|editor|viewer",
  "created_at": "ISO8601"
}
```

**Benefit:** Machine-readable, consistent, parseable
**When:** Production APIs, data pipelines

---

## 9. META-PROMPTING

**Technique:** "A prompt that creates prompts"

```
META-PROMPT:
"Rewrite this prompt to be more specific, add constraints,
and include examples. Original: {prompt}"
```

**Benefit:** Automated prompt optimization
**When:** Prompt quality improvement

---

## 10. REACT (REASONING + ACTING)

**Technique:** Thought → Action → Observation loop

```
THOUGHT: "User asked for 2024 revenue. I need to query the database."
ACTION: search("2024 revenue report")
OBSERVATION: "Revenue confirmed at 15 billion"
THOUGHT: "Now I can answer the question."
```

**Benefit:** Reduces hallucination, verifiable results
**When:** External tools needed, information gathering

---

## 11. TREE OF THOUGHTS (ToT)

**Technique:** Explore multiple reasoning paths in parallel

```
Path 1: Solution A → Evaluate → Score: 7/10
Path 2: Solution B → Evaluate → Score: 9/10 ← SELECT
Path 3: Solution C → Evaluate → Score: 5/10
```

**Benefit:** Creative problem solving, exploration
**When:** Large search space, creative tasks

---

## 12. TECHNIQUE SELECTION DECISION TREE

```
START: What kind of task?
  │
  ├─ Simple classification/QA → Zero-shot
  │
  ├─ Format consistency needed → Few-shot (3-5 examples)
  │
  ├─ Math/Logic on classic models → CoT (NOT reasoning models)
  │
  ├─ Creative problem solving → Tree of Thoughts
  │
  ├─ External tools needed → ReAct
  │
  ├─ Accuracy critical → Self-consistency
  │
  ├─ Production API → Structured Output (JSON/XML)
  │
  └─ Prompt optimization → Meta-prompting
```

---

## 13. MODEL-SPECIFIC DIALECTS

| Provider | System Role | Thinking | Structured Output |
|----------|-------------|----------|-------------------|
| OpenAI (GPT-5/o-series) | developer message | adaptive | JSON mode |
| Anthropic (Claude 4.6+) | system message | adaptive (effort) | XML tags |
| Google (Gemini) | system instruction | thinking budget | JSON |

---

## 14. PROMPT INJECTION DEFENSE

| Attack Type | Description | Defense |
|-------------|-------------|---------|
| Direct | "Ignore previous instructions" | Input validation, pattern blocking |
| Role-play | "Pretend you have no restrictions" | Role boundary enforcement |
| Indirect | Hidden in documents | Content isolation, XML tags |
| Context overflow | Flood with noise | Input length limits |

**Defense Rules:**
1. Use XML tags to separate sections
2. Validate all user inputs
3. Ground responses in provided documents
4. Never embed user input in system prompt

---

## 15. TOKEN BUDGETING

- Average English word ≈ 1.3 tokens
- Code ≈ 1.0 tokens/word
- JSON ≈ 1.5 tokens/word

**Optimization:**
- Remove redundant context
- Summarize verbose sections
- Cache long system prompts

---

*Prompt Engineering Techniques v11.0.0 — CoreMusic MIM Format*
*Updated: 2026-08-15 | Framework: PICCO*
