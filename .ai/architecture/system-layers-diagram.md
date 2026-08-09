---
type: diagram
category: system-layers
title: "CoreMusic — System Architecture Diagrams (L0-L6)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — System Architecture Diagrams

**See also:** [[architecture/l0-infrastructure]] · [[architecture/l1-security]] · [[architecture/l2-routing]] · [[architecture/l3-presentation]] · [[architecture/l4-domain]] · [[architecture/l5-services]] · [[architecture/l6-electronics]]

---

## 1. L0-L6 Katman Diyagramı

```mermaid
graph TB
    subgraph "L6 — Electronics Layer"
        HW[Hardware Management]
        DRV[Driver Communication]
        FW[Firmware Management]
        DSP[DSP Control]
        AMP[Amplifier Control]
    end

    subgraph "L5 — Services Layer"
        AS[Application Services]
        UC[Use Cases]
        TX[Transaction Mgmt]
        EP[Event Publishing]
    end

    subgraph "L4 — Domain Layer"
        ENT[Entities]
        VO[Value Objects]
        AE[Aggregates]
        DE[Domain Events]
        DS[Domain Services]
    end

    subgraph "L3 — Presentation Layer"
        UI[User Interface]
        API[API Response]
        SPA[SPA Router]
        JS[Vanilla JS]
    end

    subgraph "L2 — Routing Layer"
        PR[PageRouter]
        MW[Middleware Pipeline]
        URL[URL Normalization]
    end

    subgraph "L1 — Security Layer"
        AUTH[Authentication]
        CSRF[CSRF Protection]
        CSP[CSP Nonce]
        RL[Rate Limiting]
        SEC[Security Headers]
    end

    subgraph "L0 — Infrastructure Layer"
        DB[(MySQL 9 BCNF)]
        CACHE[(APCu / Redis)]
        FS[File System]
        CRED[Credential Vault]
    end

    L6 --> L5
    L5 --> L4
    L4 --> L3
    L3 --> L2
    L2 --> L1
    L1 --> L0

    style L6 fill:#ff6b6b,color:#fff
    style L5 fill:#ffa94d,color:#fff
    style L4 fill:#ffd43b,color:#000
    style L3 fill:#69db7c,color:#000
    style L2 fill:#4dabf7,color:#fff
    style L1 fill:#9775fa,color:#fff
    style L0 fill:#868e96,color:#fff
```

---

## 2. Dependency Flow (Bağımlılık Akışı)

```mermaid
graph LR
    L6[L6 Electronics] -->|uses| L5[L5 Services]
    L5 -->|uses| L4[L4 Domain]
    L4 -->|defines| L3[L3 Presentation]
    L3 -->|calls| L2[L2 Routing]
    L2 -->|secured by| L1[L1 Security]
    L1 -->|stored in| L0[L0 Infrastructure]

    classDef allowed fill:#2ecc71,color:#fff
    classDef forbidden fill:#e74c3c,color:#fff

    class L6,L5,L4,L3,L2,L1,L0 allowed
```

---

## 3. Katman Bağımlılık Matrisi

```mermaid
graph TB
    subgraph "İzinli Bağımlılıklar"
        A1[L3 → L2] -->|✅| B1[Routing]
        A2[L2 → L1] -->|✅| B2[Security]
        A3[L1 → L0] -->|✅| B3[Infrastructure]
        A4[L6 → L5] -->|✅| B4[Services]
        A5[L5 → L4] -->|✅| B5[Domain]
    end

    subgraph "Yasak Bağımlılıklar"
        C1[L0 → L2/L3] -->|❌| D1[Layer Violation]
        C2[L1 → L3] -->|❌| D2[Layer Violation]
        C3[L3 → L0] -->|❌| D3[Layer Violation]
        C4[L6 → L4] -->|❌| D4[Layer Violation]
    end
```

---

## 4. Service Architecture

```mermaid
graph TB
    subgraph "API Gateway"
        GW[Gateway]
    end

    subgraph "Core Services"
        AUTH[Authentication]
        USER[User Service]
        MEDIA[Media Service]
        DEVICE[Device Service]
    end

    subgraph "Electronics Services"
        AUDIO[Audio Engine]
        DSP[DSP Service]
        FW[Firmware Service]
        DRV[Driver Service]
        AMP[Amplifier Service]
    end

    subgraph "Infrastructure"
        DB[(MySQL)]
        CACHE[(Redis)]
        MQ[Message Queue]
    end

    GW --> AUTH
    GW --> USER
    GW --> MEDIA
    GW --> DEVICE

    DEVICE --> AUDIO
    DEVICE --> DSP
    DEVICE --> FW
    DEVICE --> DRV
    DEVICE --> AMP

    AUTH --> DB
    USER --> DB
    MEDIA --> DB
    DEVICE --> DB

    AUTH --> CACHE
    DEVICE --> MQ
```

---

## 5. Device Ecosystem

```mermaid
graph TB
    subgraph "Cloud"
        CLOUD[CoreMusic Cloud]
    end

    subgraph "Home"
        HOME_A[Smart Amplifier]
        HOME_P[Network Player]
        HOME_R[Multi Room Controller]
    end

    subgraph "Car"
        CAR_D[DSP Amplifier]
        CAR_H[Android Head Unit]
    end

    subgraph "Studio"
        STUDIO_I[Audio Interface]
        STUDIO_D[DSP Processor]
    end

    subgraph "Embedded"
        EMB_R[Raspberry Pi]
        EMB_A[ARM Board]
    end

    CLOUD --> HOME_A
    CLOUD --> HOME_P
    CLOUD --> CAR_D
    CLOUD --> STUDIO_I
    CLOUD --> EMB_R

    HOME_A --> HOME_R
    CAR_D --> CAR_H
    STUDIO_I --> STUDIO_D
```

---

## 6. Audio Pipeline

```mermaid
graph LR
    IN[Audio Input] --> DEC[Decoder]
    DEC --> NOR[Normalize]
    NOR --> DSP[DSP]
    DSP --> MIX[Mixer]
    MIX --> EQ[Equalizer]
    EQ --> FX[Effects]
    FX --> LIM[Limiter]
    LIM --> ROUT[Output Routing]
    ROUT --> AMP[Amplifier]
    AMP --> SPK[Speaker]

    style DSP fill:#ff6b6b,color:#fff
    style EQ fill:#ffa94d,color:#fff
    style FX fill:#ffd43b,color:#000
```

---

## 7. DSP Pipeline

```mermaid
graph TB
    IN[Input Signal] --> IG[Input Gain]
    IG --> NG[Noise Gate]
    NG --> HPF[High Pass Filter]
    HPF --> LPF[Low Pass Filter]
    LPF --> PEQ[Parametric EQ]
    PEQ --> GEQ[Graphic EQ]
    GEQ --> COMP[Compressor]
    COMP --> LIM[Limiter]
    LIM --> LOUD[Loudness]
    LOUD --> XOVER[Crossover]
    XOVER --> DEL[Delay]
    DEL --> REV[Reverb]
    REV --> OG[Output Gain]
    OG --> OROUT[Output Routing]

    style PEQ fill:#4dabf7,color:#fff
    style GEQ fill:#4dabf7,color:#fff
    style COMP fill:#ff6b6b,color:#fff
    style LIM fill:#ff6b6b,color:#fff
    style XOVER fill:#ffd43b,color:#000
```

---

## 8. Driver Stack

```mermaid
graph TB
    subgraph "Application"
        APP[CoreMusic App]
    end

    subgraph "API Layer"
        API[CoreMusic API]
    end

    subgraph "Platform Abstraction"
        PAL[Platform Abstraction Layer]
    end

    subgraph "Driver Layer"
        ASIO[ASIO Driver]
        WASAPI[WASAPI Driver]
        WDM[WDM Driver]
        ALSA[ALSA Driver]
        CORE[CoreAudio Driver]
        VIRT[Virtual Audio Driver]
        HW[Hardware Driver]
    end

    subgraph "Kernel"
        KERN[Kernel Driver]
    end

    subgraph "Hardware"
        HW_DEV[Audio Device]
    end

    APP --> API
    API --> PAL
    PAL --> ASIO
    PAL --> WASAPI
    PAL --> WDM
    PAL --> ALSA
    PAL --> CORE
    PAL --> VIRT
    PAL --> HW

    ASIO --> KERN
    WASAPI --> KERN
    WDM --> KERN
    ALSA --> KERN
    CORE --> KERN
    HW --> KERN

    KERN --> HW_DEV
```

---

## 9. Firmware Update Flow

```mermaid
graph TB
    START[Start] --> DL[Download Firmware]
    DL --> SIGN{Imza Doğrula}
    SIGN -->|Geçti| CRC[CRC Kontrol]
    SIGN -->|Başarısız| REJECT[Reddet]
    CRC -->|Geçti| BACKUP[Backup Mevcut]
    CRC -->|Başarısız| REJECT
    BACKUP --> FLASH[Flash Yeni]
    FLASH --> REBOOT[Reboot]
    REBOOT --> VERIFY{Doğrula}
    VERIFY -->|Başarılı| DONE[BAŞARILI]
    VERIFY -->|Başarısız| ROLLBACK[Geri Yükle]
    ROLLBACK --> REBOOT2[Reboot]
    REBOOT2 --> DONE2[Başarısız Mod]

    style SIGN fill:#ff6b6b,color:#fff
    style VERIFY fill:#ff6b6b,color:#fff
    style DONE fill:#2ecc71,color:#fff
```

---

## 10. Authentication Flow

```mermaid
sequenceDiagram
    participant U as User
    participant FE as Frontend
    participant API as API Gateway
    participant AUTH as Auth Service
    participant DB as Database
    participant SES as Session

    U->>FE: Login (email + password)
    FE->>API: POST /auth/login
    API->>AUTH: Validate Credentials
    AUTH->>DB: Find User
    DB-->>AUTH: User Data
    AUTH->>AUTH: Verify Password (Argon2id)
    AUTH->>SES: Create Session
    SES-->>AUTH: Session Token
    AUTH-->>API: Success + Token
    API-->>FE: 200 OK + Set-Cookie
    FE-->>U: Dashboard
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
