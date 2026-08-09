# Vault Rules — CoreMusic

**Authority:** ADR-004, ADR-042, ADR-043
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Vault Purpose

Single Source of Truth (SSOT) for all AI assistants. Everything in `.ai/` is canonical.

## 2. Vault Components

| Component | Purpose |
|-----------|---------|
| CLAUDE.md | AI Constitution & Master Vault Mandate |
| AGENTS.md | Agent Registry & Coordination Protocol |
| WORKFLOW.md | Vault Workflows & Engineering Processes |
| index.md | Master Index (404 files, 50 ADRs) |
| keys.md | Keyword Map & Concept Router |
| brain.md | Engineering Brain (50 ADRs) |
| MEMORY.md | Memory System Index |
| log.md | Activity Log & Audit Trail |
| engine.md | Orchestration Engine |
| decisions/ | Architecture Decision Records (ADR-001 to ADR-050) |
| architecture/ | System documentation (L0-L3, services, security) |
| projects/ | Project documentation (Neva Engine, Download Service, etc.) |
| testing/ | Test strategies and coverage targets |
| electronic/ | Hardware and electronics documentation |
| personas/ | User personas |
| prompt-system/ | Prompt templates |
| ui-design/ | UI/UX design documentation |

## 3. File Size Limits

| File Type | Max Size | Action |
|-----------|----------|--------|
| Active log.md | 1000 lines | Archive to `archives/log-YYYY-MM.md` |
| Documentation files | 1000 lines | Split or archive |
| ADR files | 500 lines | Split or create supplementary ADR |
| Index files | 1500 lines | Archive older entries |

## 4. ADR Lifecycle

```
Draft → Review → Active → Frozen
```

- **Draft:** Editable by anyone
- **Review:** Under review
- **Active:** Can be updated (requires justification)
- **Frozen:** Cannot be modified (ADR-001 to ADR-037 frozen)

## 5. Vault Maintenance

### 5.1 Daily Tasks
- Check for broken wiki-links
- Verify ADR references
- Update cross-references if files changed

### 5.2 Weekly Tasks
- Archive old log entries
- Review and update personas
- Check for duplicate documentation

### 5.3 Monthly Tasks
- Full vault audit
- Update index.md with new files
- Review and consolidate documentation

## 6. Cross-Reference Rules

- Use `[[relative/path]]` for internal links
- Always verify link targets exist
- Update links when files are moved
- No dead links in documentation

## 7. Documentation Standards

- All docs must have frontmatter (title, category, date, status, version)
- Use consistent heading hierarchy
- Include cross-references to related ADRs
- Mark verification status of external information

## 8. Vault Automation

### 8.1 Scheduled Tasks
- `vault-integrity-check.ps1` — Hourly wiki-link validation
- `vault-update.ps1` — Daily cross-reference updates
- `vault-archive.ps1` — Monthly log rotation

### 8.2 CI/CD Integration
- Pre-commit: Wiki-link validation
- Post-merge: Cross-reference verification
- Scheduled: Full vault audit

## 9. Backup Strategy

| Layer | Method | Frequency |
|-------|--------|-----------|
| Primary | Git version control | Every commit |
| Secondary | Automated archive | Daily |
| Tertiary | Manual backup | Before major changes |

## 10. Governance Model

- **Vault Steward:** Bayram Ali
- **Authority:** Ultimate decision maker for vault changes
- **Process:** All vault changes must be logged in `log.md`
- **Audit:** Monthly review of vault health metrics

## 11. File Organization

```
.ai/
├── CLAUDE.md                    # AI Constitution
├── AGENTS.md                    # Agent Registry
├── WORKFLOW.md                  # Workflows
├── index.md                     # Master Index
├── keys.md                      # Keyword Map
├── brain.md                     # Engineering Brain
├── MEMORY.md                    # Memory System
├── log.md                       # Activity Log
├── engine.md                    # Orchestration Engine
├── decisions/
│   ├── accepted/                # Active ADRs
│   ├── draft/                   # Draft ADRs
│   └── rejected/                # Rejected ADRs
├── architecture/
│   ├── L0-infrastructure/       # Infrastructure layer
│   ├── L1-security/             # Security layer
│   ├── L2-routing/              # Routing layer
│   ├── L3-presentation/         # Presentation layer
│   └── services/                # Service documentation
├── projects/
│   ├── NevaEngine/              # Audio engine
│   ├── NevaPlayer/              # Media player
│   └── download-service/        # Download service
├── testing/
│   ├── strategies/              # Test strategies
│   └── coverage/                # Coverage reports
├── electronic/
│   ├── hardware/                # Hardware designs
│   └── software/                # Embedded software
├── personas/                    # User personas
├── prompt-system/               # Prompt templates
├── ui-design/                   # UI/UX documentation
├── archives/                    # Archived documents
└── sessions/                    # Session history
```

## 12. Naming Conventions

| Type | Convention | Example |
|------|-----------|---------|
| ADR | `ADR-NNN-description.md` | `ADR-044-dynamic-user-theme-engine.md` |
| Architecture | `layer/description.md` | `L0-infrastructure/database.md` |
| Project | `project/description.md` | `NevaEngine/audio-core.md` |
| Testing | `type/description.md` | `strategies/unit-testing.md` |

---

*Vault Rules v3.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
