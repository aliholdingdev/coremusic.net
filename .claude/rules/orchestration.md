# Orchestration Rules — CoreMusic

**Authority:** ADR-042
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Master Orchestrator (MO)

The MO coordinates all agent activities:
- Routes tasks to appropriate specialists
- Manages handovers between agents
- Enforces MSA limits (15 files/task)
- Maintains audit trail in `log.md`

## 2. Agent System

| Agent | Domain |
|-------|--------|
| Master Orchestrator | Task routing, coordination, handover |
| backend-architect | PHP, API, routing, middleware |
| ui-designer | CSS, ITCSS, frontend, responsive |
| security-engineer | OWASP, CSRF, CSP, encryption |
| data-engineer | MySQL, BCNF, migration, schema |
| embedded-engineer | C++20, JUCE, ASIO, audio DSP |
| qa-engineer | PHPUnit, Vitest, Playwright, E2E |
| devops-engineer | CI/CD, Docker, deploy, monitoring |

## 3. Routing Table

| Keywords | Agent |
|----------|-------|
| PHP, controller, repository, routing, middleware | backend-architect |
| CSS, UI, responsive, accessibility, ITCSS, BEM, frontend, design | ui-designer |
| CSRF, CSP, XSS, OWASP, auth, encryption, security, session | security-engineer |
| database, SQL, BCNF, migration, query, schema, MySQL, PDO | data-engineer |
| C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI | embedded-engineer |
| test, coverage, PHPUnit, Vitest, Playwright, E2E, unit test | qa-engineer |
| CI/CD, Docker, deploy, infrastructure, pipeline, monitoring | devops-engineer |
| composer, vendor, shared-infrastructure, dependency, junction | backend-architect (composer-sync) |
| vault, documentation, ADR, wiki-link, index, keys, brain | vault-updater |

## 4. Task Dispatch Flow

```
1. MO receives task
2. Analyze keywords → match agent
3. Read relevant vault docs (max 15 files)
4. Launch specialist agent with detailed context
5. Review agent output for completeness
6. If cross-domain, handover to next agent
```

## 5. Handover Protocol

```
[Source Agent] → [Handover] → [Target Agent]

Fields:
- Subject
- Source Agent
- Target Agent
- Priority (HIGH/MEDIUM/LOW)
- Affected Files
- Request
- Status
```

## 6. Agent Constraints

| Constraint | Rule |
|------------|------|
| Domain Boundary | Agent cannot modify files outside its domain |
| Zero Code Before Plan | No code without approved design |
| Audit Trail | Every interaction logged to `log.md` |
| 30s Health Check | Agent timeout 30s, max 3 retry |
| Escalation | L1→L2→L3 (Domain Lead → Tech Lead → Arch Lead) |

## 7. Mandatory Skills (ADR-042/C4)

Every agent must have these 5 skills active:

1. `/prompt-maker` — prompt generation
2. `/brainstorming` — idea exploration
3. `/vault-sync` — vault synchronization
4. `/hallucination-control` — verification
5. `Red Team · Truth Mode · Human Mode` — always-on protocol

## 8. Skill Loading

Skills are loaded from:
- `.opencode/skills/` — OpenCode skills
- `.claude/skills/` — Claude Code skills
- `.agents/skills/` — CoreMusic-specific skills

## 9. Parallel Execution

- Independent tasks can run in parallel
- Use barrier for synchronization
- Context lock for shared resources
- Queue for sequential dependencies

## 10. Error Recovery

- 30s timeout per agent
- Max 3 retry attempts
- Queue reset on failure
- Escalation to human on persistent failure

---

*Orchestration Rules v3.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
