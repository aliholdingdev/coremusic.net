---
title: "CoreMusic — Tool Calling"
type: architecture
category: tool-calling
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Tool Calling

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[mcp-integration]]

---

## 1. Amaç

AI'ın dış servisler ve araçlarla iletişimini tanımlar. Tool invocation, parameter passing, response handling, tool registration, execution, security ve composition süreçlerini kapsar.

---

## 2. Tool Calling Mimarisi

```
┌─────────────────────────────────────────────────────┐
│                 Tool Calling                          │
├─────────────────────────────────────────────────────┤
│  Intent Parser → Tool Router → Permission Check      │
├─────────────────────────────────────────────────────┤
│  Parameter Validator → Executor → Response Formatter  │
├─────────────────────────────────────────────────────┤
│  Tool Registry ←→ MCP Server ←→ External Services    │
└─────────────────────────────────────────────────────┘
```

---

## 3. Tool Types

| Kategori | Tool | Servis | Kullanım |
|----------|------|--------|----------|
| **File** | File Read | L0 Infrastructure | Dosya okuma |
| **File** | File Write | L0 Infrastructure | Dosya yazma |
| **File** | File Search | L0 Infrastructure | Dosya arama |
| **Database** | DB Query | Data Engineer | Veritabanı sorgusu |
| **Database** | DB Migration | Data Engineer | Schema değişikliği |
| **API** | HTTP Client | Network | Dış API çağrısı |
| **API** | WebSocket | Network | Gerçek zamanlı iletişim |
| **Audio** | Music Search | Media Service | Müzik arama |
| **Audio** | Playlist Manager | Media Service | Çalma listesi yönetimi |
| **Audio** | Audio Player | Audio Service | Ses oynatma |
| **Audio** | EQ Controller | Audio Service | EQ ayarlama |
| **Audio** | Download Manager | Download Service | İndirme yönetimi |
| **Hardware** | Device Control | Device Service | Cihaz kontrolü |
| **Hardware** | DSP Configure | Audio Service | DSP yapılandırması |
| **Security** | Auth Check | Security Service | Kimlik doğrulama |
| **Security** | Rate Limit Check | Security Service | Rate limit kontrolü |

---

## 4. Tool Registration

**Manifest-based registration:**

```json
{
  "tool_manifest": {
    "name": "music_search",
    "version": "1.0.0",
    "category": "audio",
    "service": "media-service",
    "endpoint": "/api/search",
    "method": "POST",
    "parameters": {
      "query": {"type": "string", "required": true},
      "limit": {"type": "integer", "default": 10}
    },
    "permissions": ["read:music"],
    "rate_limit": "60/60s",
    "timeout": "10s"
  }
}
```

**Registration Kuralları:**
- Tüm tool'lar manifest ile kayıt olur
- Versioning zorunlu (semver)
- Permission tanımlı olmalı
- Rate limit tanımlı olmalı
- Timeout tanımlı olmalı

---

## 5. Tool Invocation Flow

```
User Intent → Parse Intent → Select Tool → Permission Check → Validate Params → Execute → Format Response
     ↓              ↓            ↓              ↓                  ↓              ↓            ↓
  Natural Lang   JSON Schema   Router        RBAC check        Type Check     Service     Structured
```

**Adım Detayları:**
1. **Parse Intent:** Doğal dil → JSON schema
2. **Select Tool:** Tool registry'den doğru tool'u seç
3. **Permission Check:** RBAC ile yetki kontrolü
4. **Validate Params:** Input parametrelerini doğrula
5. **Execute:** Servisi çağır (sync/async)
6. **Format Response:** Structured output oluştur

---

## 6. Tool Execution Modes

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| **Sync** | Sonuç bekleyerek | Hızlı sorgular (<1s) |
| **Async** | Arka planda | Uzun işlemler (>1s) |
| **Streaming** | Kısmi sonuçlar | Gerçek zamanlı veri |
| **Batch** | Toplu işlem | Çoklu görev |

**Execution Parametreleri:**

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Timeout | 10s varsayılan | Maks bekleme |
| Max Retry | 3 | Yeniden deneme |
| Backoff | Exponential | 1s → 2s → 4s |
| Circuit Breaker | 5 hata | Devre kesici |

---

## 7. Error Handling

| Hata Tipi | Çözüm | Retry | ADR |
|-----------|-------|-------|-----|
| Invalid params | Validation error | 0 | — |
| Permission denied | Auth error | 0 | ADR-008 |
| Service unavailable | Fallback tool | 1 | — |
| Timeout | Retry with backoff | 3 | — |
| Rate limit | Queue + wait | 2 | ADR-013 |
| Circuit open | Skip + log | 0 | — |
| Network error | Offline fallback | 3 | — |
| Hallucination risk | VERIFICATION REQUIRED | 0 | ADR-005 |

---

## 8. Tool Security

| Kural | Açıklama | ADR |
|-------|----------|-----|
| Permission check | RBAC ile yetki kontrolü | ADR-008 |
| Parameter validation | Input sanitization | ADR-022 |
| Output sanitization | Çıktı temizleme | ADR-005 |
| Rate limiting | Tool abuse önleme | ADR-013 |
| Authorization | Tool-specific permissions | ADR-010 |
| Audit trail | Tüm invocations loglanır | ADR-004 |
| Input sanitization | SQL injection önleme | ADR-002 |
| No secrets in params | Secret tool'a yazılmaz | ADR-022 |

---

## 9. Tool Composition

**Chain tools for complex workflows:**

```
Workflow: "Yeni albüm indir ve kütüphaneye ekle"
  → Step 1: music_search (query: album name)
  → Step 2: download_manager (track: selected)
  → Step 3: db_query (table: coremusic_musics, action: insert)
  → Step 4: audio_player (action: play preview)
```

**Composition Patterns:**

| Pattern | Açıklama | Örnek |
|---------|----------|-------|
| **Sequential** | Sıralı ejecution | Search → Download → Store |
| **Parallel** | Eşzamanlı ejecution | Multi-source search |
| **Conditional** | Koşullu dal | If found → play, else → search |
| **Loop** | Tekrarlama | Batch download |
| **Fallback** | Alternatif | Primary → Fallback tool |

---

## 10. Tool Performance

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Invocation latency | <500ms | P95 |
| Success rate | >99% | Günlük |
| Error rate | <1% | Günlük |
| Rate limit hits | <5% | Günlük |
| Cache hit ratio | >80% | Saatlik |

---

## 11. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Tools | [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| § 4 Registration | [[mcp-integration]] | MCP protokolü |
| § 7 Error | [[ADR-008-bypass-auth-middleware]] | Auth bypass |
| § 8 Security | [[ADR-022-database-hardened-security]] | Şifreleme |
| § 9 Composition | [[ai-orchestrator]] | Görev dağıtımı |
| § 8 Security | [[ADR-013-rate-limiting-apcu]] | Rate limiting |

---

## 12. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 12 |
| SSOT Authority | Tool Calling |
| Last Updated | 2026-08-09 |
| Tool Categories | 6 (File, Database, API, Audio, Hardware, Security) |
| Tool Count | 16 |
| Execution Modes | 4 (Sync, Async, Streaming, Batch) |
| Composition Patterns | 5 |
| ADR Coverage | ADR-002/004/005/008/010/013/022 |
| Cross References | 6 çapraz referans |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
