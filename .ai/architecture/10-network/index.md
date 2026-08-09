---
type: architecture
category: network
title: "CoreMusic — Network Architecture Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Network Architecture

**See also:** [[index]] · [[brain.md]] · [[architecture/l5-services]] · [[architecture/l6-electronics]]

---

## 1. Amaç

Network Architecture, CoreMusic platformundaki tüm servisler arası iletişim protokollerini, ağ topolojisini ve veri akışını tanımlar.

---

## 2. Protokol Listesi

| Protokol | Dosya | Kullanım |
|----------|-------|----------|
| HTTP/HTTPS | [[http-https]] | REST API, web |
| WebSocket + MQTT | [[websocket-mqtt]] | Gerçek zamanlı, IoT |
| gRPC + IPC | [[grpc-ipc]] | Servisler arası, yüksek performans |
| Local Socket | [[local-socket]] | Yerel iletişim |

---

## 3. Protokol Seçim Matrisi

| Senaryo | Protokol | Gecikme | Güvenlik |
|---------|----------|---------|----------|
| Web API | HTTPS | 50-200ms | TLS |
| Gerçek zamanlı | WebSocket | 10-50ms | WSS |
| IoT cihazları | MQTT | 100-500ms | TLS |
| Servisler arası | gRPC | 1-10ms | mTLS |
| Yerel process | Unix Socket | <1ms | OS-level |
| Shared Memory | IPC | <0.1ms | Process-level |
| Dosya paylaşımı | SMB/NFS | 5-50ms | Auth |

---

## 4. Service Communication Matrix

| Kaynak → Hedef | Protokol | Port | Encryption |
|-----------------|----------|------|------------|
| Frontend → API Gateway | HTTPS | 443 | TLS 1.3 |
| API Gateway → Auth Service | gRPC | 9001 | mTLS |
| API Gateway → Media Service | gRPC | 9002 | mTLS |
| API Gateway → Device Service | gRPC | 9003 | mTLS |
| Device Service → Electronics | IPC | Unix Socket | OS-level |
| Web Panel → WebSocket | WSS | 8443 | TLS |
| IoT Device → MQTT | MQTTS | 8883 | TLS |
| Firmware → OTA Server | HTTPS | 443 | TLS |

---

## 5. Network Security

| Layer | Kullanım | ADR |
|-------|----------|-----|
| TLS 1.3 | Tüm HTTPS | [[ADR-022-database-hardened-security]] |
| mTLS | Servisler arası | [[ADR-052-hybrid-auth-architecture]] |
| API Key | Third-party erişim | [[ADR-020-api-public-security]] |
| Rate Limiting | Abuse önleme | [[ADR-013-rate-limiting-apcu]] |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-020-api-public-security]] | API güvenlik |
| [[ADR-022-database-hardened-security]] | Şifreleme |
| [[ADR-032-ipc-contract-versioning]] | IPC sözleşme versiyonlama |
| [[ADR-052-hybrid-auth-architecture]] | Hybrid auth |

---

## 7. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Network | [[architecture/l5-services]] | Servis katmanı |
| Network | [[architecture/l6-electronics]] | Electronics iletişim |
| Network | [[architecture/07-security/index]] | Güvenlik katmanı |
| Network | [[architecture/03-contracts/api-architecture-master]] | API mimarisi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
