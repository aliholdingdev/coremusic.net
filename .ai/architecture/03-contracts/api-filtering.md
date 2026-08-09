---
type: architecture
category: contracts
title: "API Filtering & Sorting Standard"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Filtering & Sorting Standard

## 1. Purpose

Defines standardized query parameter patterns for filtering, sorting, field selection, and search across all CoreMusic API endpoints.

---

## 2. Filter Patterns

### 2.1 Basic Filtering

| Pattern | Example | Description |
|---------|---------|-------------|
| Exact match | `?genre=pop` | Matches field value exactly |
| Multiple values | `?genre=pop,rock` | Matches any value (comma-separated) |
| Negation | `?genre!=pop` | Excludes matching values |
| Numeric range | `?duration_min=180&duration_max=300` | Min/max boundaries |
| Date range | `?created_after=2024-01-01&created_before=2024-12-31` | Date boundaries |
| Partial match | `?title=love` | LIKE operator (contains) |

### 2.2 Sorting

| Pattern | Example | Description |
|---------|---------|-------------|
| Single field | `?sort=created_at` | Sort ascending (default) |
| Direction | `?sort=created_at&order=desc` | Sort descending |
| Multiple fields | `?sort=artist,title` | Multi-level sort |
| Multi-direction | `?sort=artist:asc,title:desc` | Per-field direction |

### 2.3 Field Selection

| Pattern | Example | Description |
|---------|---------|-------------|
| Include fields | `?fields=id,title,artist` | Return only specified fields |
| Exclude fields | `?fields=-password,-secret` | Return all except specified |
| Nested fields | `?fields=id,title,artist.name` | Dot-notation for relations |

### 2.4 Search

| Pattern | Example | Description |
|---------|---------|-------------|
| Full-text search | `?q=beatles` | Global search across indexed fields |
| Field search | `?q_title=love` | Search within specific field |

---

## 3. Filter Operators

| Operator | Symbol | Example | SQL Equivalent |
|----------|--------|---------|----------------|
| Equals | `=` | `?genre=pop` | `= 'pop'` |
| Not equals | `!=` | `?genre!=pop` | `!= 'pop'` |
| Greater than | `_min` | `?duration_min=180` | `>= 180` |
| Less than | `_max` | `?duration_max=300` | `<= 300` |
| Greater or equal | `_gte` | `?year_gte=2024` | `>= 2024` |
| Less or equal | `_lte` | `?year_lte=2020` | `<= 2020` |
| In list | `,` | `?genre=pop,rock` | `IN ('pop','rock')` |
| Like | `~` | `?title~love` | `LIKE '%love%'` |
| Starts with | `^` | `?title^love` | `LIKE 'love%'` |
| Ends with | `$` | `?title$love` | `LIKE '%love'` |

---

## 4. Combined Filters

Filters combine with AND logic by default. OR logic uses bracket notation:

```
# AND: genre=pop AND year=2024
?genre=pop&year=2024

# OR: genre=pop OR genre=rock
?genre=pop|rock

# Complex: (genre=pop OR genre=rock) AND year_gte=2020
?genre=pop|rock&year_gte=2020
```

---

## 5. Filter Validation Rules

| Rule | Enforcement |
|------|-------------|
| Whitelist allowed fields | Reject unknown field names |
| Sanitize input | Strip special characters from string values |
| Type checking | Numeric fields reject non-numeric values |
| Date format | ISO 8601 (`YYYY-MM-DD`) required |
| UUID format | Standard UUID v4 required |
| Max array size | 50 values for IN operator |
| Max string length | 200 characters for LIKE searches |
| Reject SQL injection | Block `;`, `--`, `UNION`, `SELECT` patterns |

---

## 6. Response Format

```json
{
  "data": [...],
  "meta": {
    "total": 1250,
    "page": 1,
    "per_page": 20,
    "sort": "created_at",
    "order": "desc",
    "filters": {
      "genre": "pop",
      "year_gte": "2024"
    }
  }
}
```

---

## 7. Filter Examples

| Endpoint | Filter | Description |
|----------|--------|-------------|
| `GET /api/songs?genre=pop` | Genre filter | Pop songs only |
| `GET /api/songs?sort=created_at&order=desc` | Sort by newest | Latest songs first |
| `GET /api/songs?fields=id,title,artist` | Field selection | Minimal response |
| `GET /api/songs?q=beatles` | Full-text search | Search songs |
| `GET /api/songs?duration_min=180&duration_max=300` | Duration range | 3-5 minute songs |
| `GET /api/songs?created_after=2024-01-01` | Date filter | Songs from 2024+ |
| `GET /api/songs?genre=pop,rock&sort=title` | Combined | Pop/rock sorted by title |

---

## 8. Cross References

| Document | Relationship |
|----------|-------------|
| [[api-architecture-master]] | Parent API architecture |
| [[api-design-rules]] | API design conventions |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode