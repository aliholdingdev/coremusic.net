---
title: "CoreMusic — MCP Integration"
type: architecture
category: mcp-integration
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — MCP Integration

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[CLAUDE.md]] · [[tool-calling]] · [[AGENTS.md]]

---

## 1. Amaç

Model Context Protocol (MCP) entegrasyonunu tanımlar. External tool ve service connectivity için standart protokol. MCP Server, Resources, Tools, Prompts ve security yapılandırmasını kapsar.

---

## 2. MCP Mimarisi

```
┌─────────────────────────────────────────────────────┐
│              MCP Host (CoreMusic AI Engine)           │
├─────────────────────────────────────────────────────┤
│  MCP Client ←→ MCP Server ←→ External Tools          │
├─────────────────────────────────────────────────────┤
│  Transport (stdio / SSE / Streamable HTTP)            │
├─────────────────────────────────────────────────────┤
│  Security Layer (Auth + Rate Limit + Audit)           │
└─────────────────────────────────────────────────────┘
```

---

## 3. MCP Bileşenleri

| Bileşen | Sorumluluk | Teknoloji |
|---------|------------|-----------|
| MCP Host | CoreMusic AI engine | PHP 8.4 |
| MCP Client | Tool invocation istemcisi | PHP 8.4 |
| MCP Server | Tool provider | PHP 8.4 |
| Transport | İletişim protokolü | stdio/SSE/HTTP |
| Security Layer | Auth + Rate limit + Audit | Middleware |

---

## 4. Supported Transports

| Transport | Kullanım | Durum | Port |
|-----------|----------|-------|------|
| stdio | Local processes | ✅ Destekli | — |
| SSE | Web streaming | ✅ Destekli | 81 |
| Streamable HTTP | REST API | ✅ Destekli | 81 |
| WebSocket | Gerçek zamanlı | ✅ Destekli | 9742 |

---

## 5. MCP Server Configuration

```json
{
  "mcp_server": {
    "name": "coremusic-mcp",
    "version": "1.0.0",
    "transport": "streamable-http",
    "port": 81,
    "host": "music.coremusic.net",
    "auth": {
      "type": "session",
      "session_cookie": "COREMUSIC_SESS",
      "csrf_token": "csrf_token"
    },
    "rate_limit": {
      "requests_per_minute": 60,
      "burst": 10
    }
  }
}
```

**Configuration Kuralları:**
- Transport: Streamable HTTP (varsayılan)
- Auth: Session-based (ADR-011)
- CSRF: `csrf_token` zorunlu (ADR-010)
- Rate limit: APCu tabanlı (ADR-013)

---

## 6. MCP Resources

| Resource URI | Tip | Kullanım | Erişim |
|--------------|-----|----------|--------|
| `file:///path` | File | Dosya okuma | Read |
| `db://table` | Database | DB sorgusu | Read/Write |
| `api://endpoint` | API | Dış API | Read |
| `service://name` | Service | İç servis | Read |
| `vault://path` | Vault | .ai/ vault | Read |
| `audio://track` | Audio | Ses dosyası | Read |
| `config://key` | Config | Sistem config | Read |

**Resource Erişim Kuralları:**
- Read: Tüm agent'lar
- Write: Sadece yetkili agent'lar
- Secret: `[REDACTED]` ile loglanır

---

## 7. MCP Tools

| Tool | Description | Parameters | Permissions |
|------|-------------|------------|-------------|
| `read_file` | Dosya oku | path, offset, limit | read:file |
| `write_file` | Dosya yaz | path, content | write:file |
| `search_files` | Dosya ara | path, pattern | read:file |
| `query_db` | DB sorgusu | database, query, params | read:db |
| `execute_db` | DB işlemi | database, query, params | write:db |
| `http_request` | HTTP çağrısı | method, url, body | read:api |
| `music_search` | Müzik ara | query, limit | read:music |
| `playlist_create` | Çalma listesi oluştur | name, tracks | write:music |
| `audio_analyze` | Ses analiz et | file_path | read:audio |
| `eq_adjust` | EQ ayarla | band, gain | write:audio |
| `device_control` | Cihaz kontrol et | device, command | write:hardware |
| `search_web` | Web'de ara | query, limit | read:web |
| `run_test` | Test çalıştır | test_file, coverage | read:test |
| `deploy` | Deploy et | service, version | write:deploy |

---

## 8. MCP Prompts (Context Injection)

| Prompt Tipi | Kaynak | Kullanım |
|-------------|--------|----------|
| System | CLAUDE.md, AGENTS.md | Agent davranış tanımları |
| Context | ADR, architecture | Bağlam enjeksiyonu |
| Skill | .opencode/skills/ | Uzmanlık talimatları |
| Tool Result | Tool çağrısı sonuçları | Sonuç bağlamı |

**Context Injection Sırası:**
1. P0: CLAUDE.md, AGENTS.md, WORKFLOW.md
2. P1: index.md, keys.md, brain.md, MEMORY.md, log.md
3. P2: ADR files, Architecture docs
4. P3: Testing, UI-design, Personas

---

## 9. MCP Security

| Kural | Açıklama | ADR |
|-------|----------|-----|
| Authentication | Session-based auth | ADR-011 |
| Authorization | RBAC permission check | ADR-008 |
| Rate limiting | 60 req/60s | ADR-013 |
| CSRF protection | `csrf_token` doğrulama | ADR-010 |
| Input validation | Parameter sanitization | ADR-022 |
| Output sanitization | Çıktı temizleme | ADR-005 |
| Audit trail | Tüm calls loglanır | ADR-004 |
| No secrets | Secret resource'a yazılmaz | ADR-022 |
| Transport security | HTTPS zorunlu | ADR-022 |

---

## 10. MCP Client Configuration

```json
{
  "mcp_client": {
    "server_url": "https://music.coremusic.net/mcp",
    "transport": "streamable-http",
    "auth": {
      "type": "session",
      "cookie": "COREMUSIC_SESS={{session_id}}"
    },
    "timeout": 10000,
    "retry": {
      "max_attempts": 3,
      "backoff": "exponential"
    },
    "cache": {
      "enabled": true,
      "ttl": 300
    }
  }
}
```

---

## 11. Error Recovery

| Hata | Çözüm | Retry | ADR |
|------|-------|-------|-----|
| Connection refused | Retry with backoff | 3 | — |
| Timeout | Fallback to cached | 2 | — |
| Auth failure | Re-authenticate | 1 | ADR-011 |
| Rate limited | Queue + wait | 2 | ADR-013 |
| Tool not found | Log + skip | 0 | — |
| Invalid response | VERIFICATION REQUIRED | 0 | ADR-005 |
| Circuit open | Degrade mode | 0 | — |

---

## 12. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Mimarisi | [[tool-calling]] | Tool calling |
| § 5 Resources | [[knowledge-base]] | Bilgi bankası |
| § 6 Tools | [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| § 9 Security | [[ADR-022-database-hardened-security]] | Şifreleme |
| § 9 Security | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 9 Security | [[ADR-011-session-management]] | Session |
| § 9 Security | [[ADR-013-rate-limiting-apcu]] | Rate limit |
| § 9 Security | [[ADR-008-bypass-auth-middleware]] | Auth bypass |

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 13 |
| SSOT Authority | MCP Integration |
| Last Updated | 2026-08-09 |
| Transports | 4 (stdio, SSE, HTTP, WebSocket) |
| Resources | 7 (File, DB, API, Service, Vault, Audio, Config) |
| Tools | 14 |
| Prompt Types | 4 |
| Security Rules | 9 |
| ADR Coverage | ADR-004/005/008/010/011/013/022 |
| Cross References | 8 çapraz referans |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
