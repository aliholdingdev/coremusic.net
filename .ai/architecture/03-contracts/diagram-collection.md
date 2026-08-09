---
title: "Complete Diagram Collection for CoreMusic ELECTRONICS"
type: architecture
category: diagram-collection
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Complete Diagram Collection for CoreMusic ELECTRONICS

**Zorunlu Bağlantılar:** [[brain.md]] · [[architecture/01-overview/architecture_master]] · [[architecture/06-audio/index]] · [[architecture/07-security/index]] · [[architecture/ai/index]] · [[electronic/index]]

---

## 1. Amaç

CoreMusic ELECTRONICS için tüm diyagramların Mermaid formatında toplandığı merkezi referanstır.

---

## 2. Sistem Mimarisi (L0-L6 Katman Yığını)

```mermaid
graph TB
    subgraph "L6 Electronics"
        E1[PCB Tasarımı]
        E2[Firmware]
        E3[Sürücüler]
    end
    subgraph "L5 Services"
        S1[Control Service]
        S2[Media Service]
        S3[Audio Service]
        S4[Device Service]
        S5[Network Audio]
        S6[AI Service]
        S7[Download Service]
    end
    subgraph "L4 Domain"
        D1[Auth Domain]
        D2[Music Domain]
        D3[Player Domain]
    end
    subgraph "L3 Presentation"
        P1[Vanilla JS]
        P2[ITCSS]
        P3[TrustedTypes]
    end
    subgraph "L2 Routing"
        R1[PageRouter]
        R2[Middleware]
        R3[Subdomain]
    end
    subgraph "L1 Security"
        SE1[Session]
        SE2[CSRF]
        SE3[CSP]
        SE4[Rate Limit]
    end
    subgraph "L0 Infrastructure"
        I1[MySQL 9 BCNF]
        I2[APCu]
        I3[Redis]
        I4[PDO]
    end

    E1 --> S1
    E2 --> S3
    E3 --> S4
    S1 --> D1
    S2 --> D2
    S3 --> D3
    D1 --> P1
    D2 --> P2
    D3 --> P3
    P1 --> R1
    P2 --> R2
    P3 --> R3
    R1 --> SE1
    R2 --> SE2
    R3 --> SE3
    SE1 --> I1
    SE2 --> I2
    SE3 --> I3
    SE4 --> I4

    style E1 fill:#9c27b0
    style E2 fill:#9c27b0
    style E3 fill:#9c27b0
    style S1 fill:#2196f3
    style S2 fill:#2196f3
    style S3 fill:#2196f3
    style S4 fill:#2196f3
    style S5 fill:#2196f3
    style S6 fill:#2196f3
    style S7 fill:#2196f3
    style P1 fill:#4caf50
    style P2 fill:#4caf50
    style P3 fill:#4caf50
    style R1 fill:#ff9800
    style R2 fill:#ff9800
    style R3 fill:#ff9800
    style SE1 fill:#f44336
    style SE2 fill:#f44336
    style SE3 fill:#f44336
    style SE4 fill:#f44336
    style I1 fill:#607d8b
    style I2 fill:#607d8b
    style I3 fill:#607d8b
    style I4 fill:#607d8b
```

---

## 3. Platform Mimarisi (Cihaz Ailesi Katmanları)

```mermaid
graph TB
    subgraph "Tier 1: Windows"
        W1[XP-11]
        W2[Server 2012 R2+]
    end
    subgraph "Tier 2: Linux"
        L1[Ubuntu]
        L2[Debian]
        L3[Fedora]
    end
    subgraph "Tier 3: macOS"
        M1[Monterey-Sonoma]
    end
    subgraph "Tier 4: Raspberry Pi"
        R1[ARM64]
        R2[Debian]
    end
    subgraph "Tier 5: ReactOS"
        X1[Experimental]
    end

    W1 --> L1
    W2 --> L2
    L1 --> M1
    L2 --> R1
    M1 --> X1

    style W1 fill:#2196f3
    style W2 fill:#2196f3
    style L1 fill:#4caf50
    style L2 fill:#4caf50
    style L3 fill:#4caf50
    style M1 fill:#9c27b0
    style R1 fill:#ff9800
    style R2 fill:#ff9800
    style X1 fill:#607d8b
```

---

## 4. Cihaz Mimarisi (Donanım/Yazılım Yığını)

```mermaid
graph TB
    subgraph "Application"
        A1[Web Panel]
        A2[Desktop App]
        A3[Mobile App]
    end
    subgraph "API Layer"
        AP1[REST API]
        AP2[WebSocket]
        AP3[gRPC]
    end
    subgraph "Service Layer"
        SV1[Control]
        SV2[Media]
        SV3[Audio]
    end
    subgraph "Driver Layer"
        DR1[ASIO]
        DR2[WASAPI]
        DR3[ALSA]
        DR4[CoreAudio]
    end
    subgraph "Hardware Layer"
        HW1[XMOS XU316]
        HW2[PCM3168A]
        HW3[AK4458]
    end

    A1 --> AP1
    A2 --> AP2
    A3 --> AP3
    AP1 --> SV1
    AP2 --> SV2
    AP3 --> SV3
    SV1 --> DR1
    SV2 --> DR2
    SV3 --> DR3
    DR1 --> HW1
    DR2 --> HW2
    DR3 --> HW3

    style A1 fill:#e1f5fe
    style A2 fill:#e1f5fe
    style A3 fill:#e1f5fe
    style HW1 fill:#f44336
    style HW2 fill:#f44336
    style HW3 fill:#f44336
```

---

## 5. Servis Mimarisi (Servis Topolojisi)

```mermaid
graph TB
    subgraph "Client"
        C1[Web Browser]
        C2[Desktop]
        C3[Mobile]
    end
    subgraph "Gateway"
        G1[API Gateway]
    end
    subgraph "Services"
        S1[Control :81]
        S2[Media :5000/6000]
        S3[Audio :9741/9742]
        S4[Device :BLE/WiFi]
        S5[Network :WebRTC]
        S6[AI :Internal]
        S7[Download :3001]
    end
    subgraph "Data"
        D1[MySQL 9]
        D2[Redis]
        D3[APCu]
    end

    C1 --> G1
    C2 --> G1
    C3 --> G1
    G1 --> S1
    G1 --> S2
    G1 --> S3
    G1 --> S4
    G1 --> S5
    G1 --> S6
    G1 --> S7
    S1 --> D1
    S2 --> D2
    S3 --> D3

    style C1 fill:#e1f5fe
    style G1 fill:#ff9800
    style S1 fill:#2196f3
    style S2 fill:#2196f3
    style S3 fill:#2196f3
    style S4 fill:#2196f3
    style S5 fill:#2196f3
    style S6 fill:#2196f3
    style S7 fill:#2196f3
```

---

## 6. Ses Pipeline'ı (Giriş→Decode→DSP→Mixer→Çıkış)

```mermaid
graph LR
    A[Giriş] --> B[Decode]
    B --> C[DSP]
    C --> D[Mixer]
    D --> E[Çıkış]

    subgraph "Giriş"
        A1[YouTube]
        A2[Deezer]
        A3[Local File]
    end
    subgraph "Decode"
        B1[FLAC]
        B2[MP3]
        B3[WAV]
    end
    subgraph "DSP"
        C1[EQ]
        C2[Compressor]
        C3[Limiter]
    end
    subgraph "Mixer"
        D1[Volume]
        D2[Balance]
        D3[Pan]
    end
    subgraph "Çıkış"
        E1[ASIO]
        E2[WASAPI]
        E3[Bluetooth]
    end

    A1 --> B1
    A2 --> B2
    A3 --> B3
    B1 --> C1
    B2 --> C2
    B3 --> C3
    C1 --> D1
    C2 --> D2
    C3 --> D3
    D1 --> E1
    D2 --> E2
    D3 --> E3

    style A1 fill:#e1f5fe
    style A2 fill:#e1f5fe
    style A3 fill:#e1f5fe
    style E1 fill:#c8e6c9
    style E2 fill:#c8e6c9
    style E3 fill:#c8e6c9
```

---

## 7. DSP Pipeline (Gain→Gate→HPF→LPF→EQ→Compressor→Limiter→Crossover→Output)

```mermaid
graph LR
    A[Input] --> B[Gain]
    B --> C[Gate]
    C --> D[HPF]
    D --> E[LPF]
    E --> F[EQ]
    F --> G[Compressor]
    G --> H[Limiter]
    H --> I[Crossover]
    I --> J[Output]

    style A fill:#e1f5fe
    style B fill:#fff3e0
    style C fill:#fff3e0
    style D fill:#fff3e0
    style E fill:#fff3e0
    style F fill:#fff3e0
    style G fill:#fff3e0
    style H fill:#fff3e0
    style I fill:#fff3e0
    style J fill:#c8e6c9
```

---

## 8. Sürücü Yığını (Uygulama→API→Sürücü→Kernel→Donanım)

```mermaid
graph TB
    A[Uygulama] --> B[API Katmanı]
    B --> C[Sürücü Katmanı]
    C --> D[Kernel]
    D --> E[Donanım]

    subgraph "Uygulama"
        A1[Audio Engine]
        A2[Player]
    end
    subgraph "API"
        B1[ASIO API]
        B2[WASAPI API]
        B3[ALSA API]
    end
    subgraph "Sürücü"
        C1[ASIO Sürücü]
        C2[WASAPI Sürücü]
        C3[ALSA Sürücü]
    end
    subgraph "Kernel"
        D1[Windows Kernel]
        D2[Linux Kernel]
    end
    subgraph "Donanım"
        E1[XMOS XU316]
        E2[PCM3168A]
    end

    A1 --> B1
    A2 --> B2
    B1 --> C1
    B2 --> C2
    B3 --> C3
    C1 --> D1
    C2 --> D1
    C3 --> D2
    D1 --> E1
    D2 --> E2

    style A1 fill:#e1f5fe
    style A2 fill:#e1f5fe
    style E1 fill:#f44336
    style E2 fill:#f44336
```

---

## 9. Ağ Topolojisi (Cloud→Gateway→Servisler→Cihazlar)

```mermaid
graph TB
    subgraph "Cloud"
        C1[CoreMusic Cloud]
    end
    subgraph "Gateway"
        G1[API Gateway]
        G2[Load Balancer]
    end
    subgraph "Services"
        S1[Control]
        S2[Media]
        S3[Audio]
        S4[Download]
    end
    subgraph "Devices"
        D1[Home PC]
        D2[Car RPi5]
        D3[Studio]
        D4[NAS]
    end

    C1 --> G1
    C1 --> G2
    G1 --> S1
    G1 --> S2
    G1 --> S3
    G1 --> S4
    S1 --> D1
    S2 --> D2
    S3 --> D3
    S4 --> D4

    style C1 fill:#9c27b0
    style G1 fill:#ff9800
    style G2 fill:#ff9800
    style D1 fill:#4caf50
    style D2 fill:#4caf50
    style D3 fill:#4caf50
    style D4 fill:#4caf50
```

---

## 10. Cihaz Topolojisi (Home/Car/Studio/Embedded Ekosistemi)

```mermaid
graph TB
    subgraph "Home"
        H1[PC]
        H2[TV]
        H3[Speakers]
    end
    subgraph "Car"
        C1[RPi5]
        C2[Head Unit]
        C3[Amp]
    end
    subgraph "Studio"
        S1[Workstation]
        S2[Monitors]
        S3[Console]
    end
    subgraph "Embedded"
        E1[DAC]
        E2[ADC]
        E3[DSP]
    end

    H1 --> C1
    H2 --> S1
    H3 --> E1
    C1 --> E2
    S1 --> E3

    style H1 fill:#4caf50
    style C1 fill:#2196f3
    style S1 fill:#9c27b0
    style E1 fill:#ff9800
```

---

## 11. AI Pipeline (Giriş→Bilgi→RAG→Çıktı)

```mermaid
graph LR
    A[Giriş] --> B[Bilgi Bankası]
    B --> C[RAG Pipeline]
    C --> D[Bellek Sistemi]
    D --> E[Prompt Motoru]
    E --> F[Araç Çağrısı]
    F --> G[MCP]
    G --> H[Çıktı]

    style A fill:#e1f5fe
    style B fill:#fff3e0
    style C fill:#fff3e0
    style D fill:#fff3e0
    style E fill:#fff3e0
    style F fill:#fff3e0
    style G fill:#fff3e0
    style H fill:#c8e6c9
```

---

## 12. Kimlik Doğrulama Akışı (Login→Session→JWT→Refresh→Logout)

```mermaid
sequenceDiagram
    participant U as User
    participant S as Server
    participant DB as Database
    participant JWT as JWT Service

    U->>S: Login Request
    S->>DB: Verify Credentials
    DB-->>S: User Data
    S->>JWT: Generate JWT
    JWT-->>S: JWT Token
    S->>U: Set Session + JWT

    loop Session Active
        U->>S: API Request
        S->>JWT: Validate JWT
        JWT-->>S: Valid
        S->>DB: Get Data
        DB-->>S: Response
        S-->>U: API Response
    end

    U->>S: Refresh Token
    S->>JWT: Validate Refresh
    JWT-->>S: New JWT
    S-->>U: New JWT

    U->>S: Logout
    S->>DB: Invalidate Session
    S-->>U: Logged Out
```

---

## 13. Firmware Güncelleme Akışı (Check→Download→Verify→Flash→Restart)

```mermaid
sequenceDiagram
    participant D as Device
    participant S as Server
    participant FW as Firmware

    D->>S: Check Update
    S-->>D: Update Available

    D->>S: Download Firmware
    S-->>D: Firmware Binary

    D->>FW: Verify Signature
    FW-->>D: Signature Valid

    D->>FW: Flash Firmware
    FW-->>D: Flash Complete

    D->>D: Restart
    D->>S: Update Complete
```

---

## 14. Boot Akışı (Power→Bootloader→Firmware→Driver→DSP→Ready)

```mermaid
graph LR
    A[Power On] --> B[Bootloader]
    B --> C[Firmware]
    C --> D[Driver]
    D --> E[DSP]
    E --> F[Ready]

    style A fill:#f44336
    style B fill:#ff9800
    style C fill:#ff9800
    style D fill:#2196f3
    style E fill:#2196f3
    style F fill:#4caf50
```

---

## 15. İstek Akışı (Client→Gateway→Auth→Service→DB→Response)

```mermaid
graph LR
    A[Client] --> B[Gateway]
    B --> C[Auth]
    C --> D[Service]
    D --> E[DB]
    E --> F[Response]

    style A fill:#e1f5fe
    style B fill:#ff9800
    style C fill:#f44336
    style D fill:#2196f3
    style E fill:#607d8b
    style F fill:#4caf50
```

---

## 16. Deploy Akışı (Build→Test→Stage→Deploy→Monitor)

```mermaid
graph LR
    A[Build] --> B[Test]
    B --> C[Stage]
    C --> D[Deploy]
    D --> E[Monitor]

    style A fill:#e1f5fe
    style B fill:#fff3e0
    style C fill:#fff3e0
    style D fill:#c8e6c9
    style E fill:#c8e6c9
```

---

## 17. Donanım Blok Diyagramı (CPU→DSP→DAC→Amp→Speaker)

```mermaid
graph LR
    A[CPU] --> B[DSP]
    B --> C[DAC]
    C --> D[Amplifikatör]
    D --> E[Hoparlör]

    style A fill:#2196f3
    style B fill:#2196f3
    style C fill:#ff9800
    style D fill:#f44336
    style E fill:#4caf50
```

---

## 18. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Sistem Mimarisi | [[architecture/01-overview/architecture_master]] | Mimari |
| § 5 Servis Mimarisi | [[architecture/06-audio/index]] | Ses servisleri |
| § 7 DSP Pipeline | [[electronic/dsp/index]] | DSP modülleri |
| § 11 AI Pipeline | [[architecture/ai/index]] | AI mimarisi |
| § 12 Auth Akışı | [[architecture/07-security/index]] | Güvenlik |

---

## 19. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 19 |
| Total Diagrams | 16 |
| Diagram Categories | 8 |
| ADR References | 0 |
| Cross References | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
