---
title: "TLS 1.3 & Sertifika Yönetimi"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# TLS 1.3 & Sertifika Yönetimi

## Genel Bakış

COREMUSIC TLS 1.3 katmanı, tüm ağ iletişiminin güvenliğini sağlayan temel bileşendir. Sertifika yönetimi, otomatik yenileme, OCSP stapling ve certificate pinning ile zero-trust güvenlik modeli uygular. 1-RTT handshake ve 0-RTT early data desteği ile düşük gecikmeli güvenli bağlantılar sağlar.

## Protokol Detayı

- **RFC**: 8446 (TLS 1.3), 6962 (Certificate Transparency)
- **Transport**: TCP (herhangi bir port)
- **Handshake**: 1-RTT (standart), 0-RTT (early data)
- **Şifreleme**: AES-256-GCM, ChaCha20-Poly1305
- **Key Exchange**: X25519, P-256, P-384
- **Digital Signature**: ECDSA-P256, Ed25519, RSA-PSS

## Teknik Detaylar

### TLS 1.3 Handshake Akışı

```
┌──────────┐                              ┌──────────┐
│  Client   │                              │  Server   │
└─────┬────┘                              └─────┬────┘
      │                                         │
      │ 1. ClientHello                           │
      │    - supported_versions: [TLS 1.3]      │
      │    - key_share: X25519 public key        │
      │    - signature_algorithms: [ECDSA, EdDSA]│
      │    - psk_key_exchange_modes (opsiyonel) │
      │────────────────────────────────────────>│
      │                                         │
      │ 2. ServerHello                           │
      │    - key_share: X25519 public key        │
      │    <EncryptedExtensions>                 │
      │    <Certificate>                         │
      │    <CertificateVerify>                   │
      │    <Finished>                            │
      │<────────────────────────────────────────│
      │                                         │
      │ 3. [Finished]                           │
      │                                         │
      │ ═══ Encrypted Application Data ═══      │
      │<══════════════════════════════════════>│
```

### 0-RTT Early Data

```
┌──────────┐                              ┌──────────┐
│  Client   │                              │  Server   │
└─────┬────┘                              └─────┬────┘
      │                                         │
      │ 1. ClientHello                           │
      │    + early_data_indication               │
      │    + psk_identity (saved session)        │
      │    + early data (encrypted with PSK)     │
      │────────────────────────────────────────>│
      │                                         │
      │ 2. ServerHello                           │
      │    + early_data_indication               │
      │    + Finished                            │
      │<────────────────────────────────────────│
      │                                         │
      │ 3. [Normal handshake completion]        │
      │                                         │
```

**0-RTT Uyarıları:**
- Replay attack risk: Idempotent operations only
- No forward secrecy for early data
- Max early data: 16384 bytes

### Sertifika Zinciri

```
┌─────────────────────────────────────────────┐
│            Certificate Chain                 │
│                                               │
│  ┌─────────────────────────────────────┐     │
│  │        Root CA (Offline)            │     │
│  │  Self-signed, 20+ year validity     │     │
│  │  SHA-384 + RSA-4096                 │     │
│  └──────────────┬──────────────────────┘     │
│                 │                             │
│  ┌──────────────┴──────────────────────┐     │
│  │      Intermediate CA               │     │
│  │  Signed by Root CA                  │     │
│  │  10 year validity                  │     │
│  └──────────────┬──────────────────────┘     │
│                 │                             │
│  ┌──────────────┴──────────────────────┐     │
│  │      End Entity Certificate         │     │
│  │  Signed by Intermediate CA          │     │
│  │  90 day validity (auto-renew)       │     │
│  │  ECDSA-P256 key                    │     │
│  │  SAN: music.coremusic.local        │     │
│  └─────────────────────────────────────┘     │
└─────────────────────────────────────────────┘
```

### Sertifika Oluşturma Akışı

```python
from cryptography import x509
from cryptography.hazmat.primitives import hashes, serialization
from cryptography.hazmat.primitives.asymmetric import ec
from cryptography.x509.oid import NameOID
import datetime

class CertificateManager:
    def generate_key_pair(self) -> ec.EllipticCurvePrivateKey:
        return ec.generate_private_key(ec.SECP256R1())

    def create_csr(self, private_key, common_name: str) -> x509.CertificateSigningRequest:
        subject = x509.Name([
            x509.NameAttribute(NameOID.COMMON_NAME, common_name),
            x509.NameAttribute(NameOID.ORGANIZATION_NAME, "COREMUSIC"),
        ])

        csr = (
            x509.CertificateSigningRequestBuilder()
            .subject_name(subject)
            .add_extension(
                x509.SubjectAlternativeName([
                    x509.DNSName("music.coremusic.local"),
                    x509.DNSName("*.coremusic.local"),
                    x509.IPAddress(IPv4Address("192.168.1.100")),
                ]),
                critical=False,
            )
            .sign(private_key, hashes.SHA256())
        )
        return csr

    def create_self_signed(self, private_key, validity_days: int = 365):
        subject = issuer = x509.Name([
            x509.NameAttribute(NameOID.COMMON_NAME, "COREMUSIC Root CA"),
        ])

        cert = (
            x509.CertificateBuilder()
            .subject_name(subject)
            .issuer_name(issuer)
            .public_key(private_key.public_key())
            .serial_number(x509.random_serial_number())
            .not_valid_before(datetime.datetime.utcnow())
            .not_valid_after(datetime.datetime.utcnow() + datetime.timedelta(days=validity_days))
            .add_extension(x509.BasicConstraints(ca=True, path_length=None), critical=True)
            .add_extension(
                x509.KeyUsage(
                    digital_signature=True, key_cert_sign=True, crl_sign=True,
                    content_commitment=False, key_encipherment=False,
                    data_encipherment=False, key_agreement=False,
                    encipher_only=False, decipher_only=False,
                ),
                critical=True,
            )
            .sign(private_key, hashes.SHA256())
        )
        return cert
```

### OCSP Stapling

```
1. ClientHello:
   - status_request: ocsp
   - extension: certificate_status_request

2. Server:
   - Queries OCSP responder for certificate status
   - Caches OCSP response (typically 24 hours)
   - Includes cached response in CertificateStatus message

3. CertificateStatus:
   - cert_status: good / revoked / unknown
   - this_update: timestamp
   - next_update: timestamp
   - response: signed OCSP response
```

### Certificate Pinning

```python
class CertificatePinning:
    def __init__(self):
        self.pinned_hashes: dict[str, list[str]] = {}
        self.backup_pins: list[str] = []

    def add_pin(self, hostname: str, sha256_hash: str):
        if hostname not in self.pinned_hashes:
            self.pinned_hashes[hostname] = []
        self.pinned_hashes[hostname].append(sha256_hash)

    def verify(self, hostname: str, cert_chain: list[x509.Certificate]) -> bool:
        if hostname not in self.pinned_hashes:
            return True  # No pinning configured

        # Check each certificate in chain
        for cert in cert_chain:
            cert_hash = self._hash_certificate(cert)
            if cert_hash in self.pinned_hashes[hostname]:
                return True

        # Fallback to backup pins
        for cert in cert_chain:
            cert_hash = self._hash_certificate(cert)
            if cert_hash in self.backup_pins:
                return True

        return False

    def _hash_certificate(self, cert) -> str:
        digest = hashes.Hash(hashes.SHA256())
        digest.update(cert.public_bytes(serialization.Encoding.DER))
        return base64.b64encode(digest.finalize()).decode()
```

### Cipher Suites

| Cipher Suite | Key Exchange | Auth | Encryption | MAC |
|-------------|-------------|------|-----------|-----|
| TLS_AES_256_GCM_SHA384 | X25519 | ECDSA | AES-256-GCM | SHA-384 |
| TLS_CHACHA20_POLY1305_SHA256 | X25519 | ECDSA | ChaCha20 | Poly1305 |
| TLS_AES_128_GCM_SHA256 | X25519 | ECDSA | AES-128-GCM | SHA-256 |

### Session Ticket

```
1. Server generates session ticket after handshake:
   - Random 32-byte nonce
   - Encrypted with server's ticket key
   - Contains: session state, cipher suite, master secret

2. Client stores session ticket:
   - Sends in ClientHello for 0-RTT

3. Key Rotation:
   - Ticket key rotates every 24 hours
   - Old keys retained for 48 hours (grace period)
```

## Konfigürasyon

```yaml
tls:
  enabled: true
  version: "1.3"
  cipher_suites:
    - "TLS_AES_256_GCM_SHA384"
    - "TLS_CHACHA20_POLY1305_SHA256"
    - "TLS_AES_128_GCM_SHA256"
  key_exchange:
    - "X25519"
    - "P-256"
  certificate:
    auto_renew: true
    renew_before_days: 30
    key_type: "ECDSA"
    key_curve: "P-256"
    validity_days: 90
    ocsp_stapling: true
    ocsp_cache_ttl: 3600           # 1 saat
  pinning:
    enabled: true
    backup_pins:
      - "sha256/AAAA..."
    pin_expiry_days: 90
  session:
    tickets:
      enabled: true
      count: 3
      key_rotation_hours: 24
    resumption: true
    max_early_data: 16384
  client_auth:
    enabled: false
    require_any: false
  alerting:
    certificate_expiry_days: 30
    ocsp_revocation_alert: true
  logging:
    level: "info"
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | TCP socket I/O |
| K1 | Giren | CPU hızı (şifreleme) |
| K6 | Çıkan | Key management |
| K14-DNS | Çıkan | Sertifika validation |
| Tüm katmanlar | Çıkan | TLS entegrasyonu |

## Durum: Implementasyon

- [x] TLS 1.3 1-RTT handshake
- [x] 0-RTT early data
- [x] X25519 key exchange
- [x] ECDSA-P256 certificate
- [x] Auto certificate renewal
- [x] OCSP stapling
- [x] Certificate pinning
- [x] Session ticket management
- [x] Cipher suite hardening
- [x] Alert reporting
- [x] Chain validation
- [x] Revocation checking
