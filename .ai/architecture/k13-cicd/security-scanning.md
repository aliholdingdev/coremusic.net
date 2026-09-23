---
title: "Security Scanning Pipeline"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Security Scanning Pipeline

## Genel Bakış

COREMUSIC security scanning pipeline'ı, Static Application Security Testing (SAST), Dynamic Application Security Testing (DAST), dependency scanning ve container image scanning olmak üzere dört katmanlı bir güvenlik tarama stratejisi uygular. OWASP Top 10, CVE database ve security best practices referencia alınır. Her pipeline step'inde security gate bulunur.

## Pipeline Akışı

```
Code Push → SAST Scan → Dependency Audit → Container Scan → DAST Scan → Security Gate → Deploy Approval
```

## Teknik Detaylar

### Security Scanning Tools

| Araç             | Kategori    | Kullanım Alanı                    |
|-----------------|-------------|-----------------------------------|
| Semgrep         | SAST        | Source code vulnerability scanning|
| OWASP ZAP       | DAST        | Runtime vulnerability testing     |
| Dependabot      | Dependency  | Package vulnerability alerts      |
| Trivy           | Container   | Image vulnerability scanning     |
| Cosign          | Signing     | Image integrity verification      |
| Gitleaks        | Secret      | Secret detection in code          |

### SAST Configuration

```yaml
# .semgrep.yml
rules:
  - id: php-sql-injection
    pattern: |
      $QUERY = "SELECT * FROM $TABLE WHERE $COL = " . $INPUT;
      ...
      $DB->query($QUERY);
    message: "Potential SQL injection detected"
    severity: ERROR
    languages: [php]

  - id: php-xss-vulnerability
    pattern: echo $USER_INPUT;
    message: "Potential XSS: user input echoed without escaping"
    severity: WARNING
    languages: [php]

  - id: php-hardcoded-secret
    patterns:
      - pattern: $SECRET = "...";
      - metavariable-regex:
          metavariable: $SECRET
          regex:.*(password|key|token|secret|api_key).*
    message: "Hardcoded secret detected"
    severity: ERROR
    languages: [php]

exclude:
  - "vendor/"
  - "node_modules/"
  - "tests/"
```

### Dependency Scanning

```yaml
# .github/dependabot.yml
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "daily"
    open-pull-requests-limit: 10
    labels:
      - "dependencies"
      - "security"
    reviewers:
      - "bayramali"

  - package-ecosystem: "npm"
    directory: "/"
    schedule:
      interval: "daily"
    open-pull-requests-limit: 5

  - package-ecosystem: "docker"
    directory: "/"
    schedule:
      interval: "weekly"

  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "weekly"
```

### Container Security Scanning

```yaml
# Trivy scan configuration
scan:
  image-ref: "ghcr.io/coremusic/coremusic:${{ github.sha }}"
  severity: "CRITICAL,HIGH"
  ignore-unfixed: true
  format: "table"
  output: "trivy-results.txt"

# Trivy ignore file
# .trivyignore
# CVE-2023-XXXXX - Accepted risk
CVE-2023-12345

# Vulnerability exceptions
# Will be fixed in next release
CVE-2023-67890
```

### DAST Configuration

```yaml
# OWASP ZAP scan policy
scanner:
  activeScan:
    enabled: true
    strength: HIGH
    threshold: MEDIUM
  passiveScan:
    enabled: true
  spider:
    maxDepth: 5
    maxDuration: 60

# Target configuration
target:
  url: "http://localhost:8000"
  context: "coremusic-context"
  authentication:
    method: "form"
    loginUrl: "/login"
    loginData:
      username: "test@example.com"
      password: "testpassword"
```

### Secret Detection

```yaml
# .gitleaks.toml
[allowlist]
  description = "Global allowlist"
  paths = [
    '''vendor/''',
    '''node_modules/''',
    '''*.test.js''',
    '''*.spec.ts'''
  ]

[[rules]]
  id = "generic-api-key"
  description = "Generic API Key"
  regex = '''(?i)(api[_-]?key|apikey)(\s*=\s*['"])([\w-]+)['"]'''
  tags = ["key", "API"]

[[rules]]
  id = "aws-access-key"
  description = "AWS Access Key"
  regex = '''AKIA[0-9A-Z]{16}'''
  tags = ["key", "AWS"]
```

### Security Gate Criteria

```yaml
# security-gate.yml
gates:
  sast:
    required: true
    maxCritical: 0
    maxHigh: 2
    blockOnFailure: true

  dependency:
    required: true
    maxCritical: 0
    maxHigh: 5
    autoFix: true

  container:
    required: true
    maxCritical: 0
    maxHigh: 0
    blockOnFailure: true

  dast:
    required: true
    maxCritical: 0
    maxHigh: 3
    schedule: "weekly"
```

### Image Signing

```yaml
# Cosign image signing
sign:
  key: "kms:///projects/coremusic/locations/global/keyRings/cicd/cryptoKeys/signing-key"
  annotations:
    org.opencontainers.image.source: "https://github.com/coremusic/coremusic"
    org.opencontainers.image.description: "COREMUSIC production image"

verify:
  key: "cosign.pub"
  annotations:
    - key: "org.opencontainers.image.source"
      value: "https://github.com/coremusic/coremusic"
```

### SBOM Generation

```yaml
# Syft SBOM configuration
sbom:
  format: "cyclonedx-json"
  output: "sbom.json"
  cataloger:
    enable-cataloging: true
    name: "coremusic-sbom"
```

## Konfigürasyon

### GitHub Actions Security Job

```yaml
security-scan:
  runs-on: ubuntu-latest
  steps:
    - uses: actions/checkout@v4

    - name: Run Semgrep SAST
      uses: returntocorp/semgrep-action@v1
      with:
        config: .semgrep.yml
        generateSarif: true

    - name: Upload SAST results
      uses: github/codeql-action/upload-sarif@v2
      if: always()
      with:
        sarif_file: semgrep.sarif

    - name: Run Trivy container scan
      uses: aquasecurity/trivy-action@master
      with:
        image-ref: ghcr.io/coremusic/coremusic:${{ github.sha }}
        format: 'sarif'
        output: 'trivy-results.sarif'
        severity: 'CRITICAL,HIGH'

    - name: Run Gitleaks
      uses: gitleaks/gitleaks-action@v2
      env:
        GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

    - name: Generate SBOM
      uses: anchore/sbom-action@v0
      with:
        image: ghcr.io/coremusic/coremusic:${{ github.sha }}
        format: cyclonedx
        output-file: sbom.json

    - name: Sign container image
      if: github.ref == 'refs/heads/main'
      uses: sigstore/cosign-installer@v3
    - run: |
        cosign sign --yes ghcr.io/coremusic/coremusic:${{ github.sha }}
```

## Bağımlılıklar

- `semgrep`: SAST scanning
- `trivy`: Container vulnerability scanning
- `gitleaks`: Secret detection
- `cosign`: Image signing
- `syft`: SBOM generation
- `dependabot`: Dependency updates
- `owasp/zap`: DAST testing

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| SAST Scanning        | Hazır       | 2026-09-20      |
| DAST Scanning        | Hazır       | 2026-09-20      |
| Dependency Scanning  | Hazır       | 2026-09-20      |
| Container Scanning   | Hazır       | 2026-09-20      |
| Secret Detection     | Hazır       | 2026-09-20      |
| Image Signing        | Hazır       | 2026-09-20      |
| SBOM Generation      | Hazır       | 2026-09-20      |
