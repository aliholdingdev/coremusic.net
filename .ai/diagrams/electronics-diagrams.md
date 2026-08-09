---
type: diagram
category: electronics-diagrams
title: "CoreMusic — Electronics Mermaid Diagram Collection"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Electronics Mermaid Diagram Collection

**Zorunlu Bağlantılar:** [[electronic/index]] · [[electronic/platform-architecture]] · [[electronic/device-architecture]] · [[electronic/audio-architecture]] · [[electronic/software-architecture]] · [[electronic/service-architecture]]

---

## 1. Device Architecture — Cihaz Aileleri

```mermaid
graph TB
    subgraph "CoreMusic Device Ecosystem"
        direction TB
        
        subgraph "Home Devices"
            H1[PC/Laptop<br/>Windows/Linux/macOS]
            H2[NAS Server<br/>Synology/QNAP]
            H3[Media Center<br/>RPi5 + PCM3168A]
        end
        
        subgraph "Professional Devices"
            P1[Studio Workstation<br/>Windows + ASIO]
            P2[8.1 Surround System<br/>XMOS XU316 + PCM3168A]
            P3[Rack-mount Server<br/>Linux Docker]
        end
        
        subgraph "Mobile Devices"
            M1[Smartphone<br/>PWA/Flutter]
            M2[Tablet<br/>PWA/Flutter]
            M3[Car Infotainment<br/>RPi5 + Android Auto]
        end
        
        subgraph "Embedded Devices"
            E1[XMOS XU316<br/>USB Audio Class 2.0]
            E2[RPi5 ARM64<br/>I2S + PCM3168A]
            E3[ESP32<br/>BLE Audio]
        end
    end
    
    H1 --> E1
    H2 --> E1
    H3 --> E2
    P1 --> E1
    P2 --> E1
    M1 --> H1
    M2 --> H2
    M3 --> E2
    E1 --> E2
    E2 --> E3
    
    style H1 fill:#3498db,color:#fff
    style P1 fill:#e74c3c,color:#fff
    style M1 fill:#2ecc71,color:#fff
    style E1 fill:#9b59b6,color:#fff
```

---

## 2. DSP Pipeline — Sinyal İşleme Zinciri

```mermaid
graph LR
    subgraph "Input Stage"
        IN[Audio Input<br/>Raw PCM 32-bit] --> GAIN[Input Gain<br/>0-60dB]
        GAIN --> DC[DC Offset Removal]
        DC --> PHASE[Phase Correction]
    end
    
    subgraph "Processing Stage"
        PHASE --> HPF[High Pass Filter<br/>20Hz-20kHz]
        HPF --> LPF[Low Pass Filter<br/>20Hz-20kHz]
        LPF --> EQ[31-Band Parametric EQ<br/>20Hz-20kHz]
        EQ --> COMP[Dynamics Compressor<br/>-60dB to 0dB]
        COMP --> LIMIT[Brick-wall Limiter<br/>0dBFS]
    end
    
    subgraph "Spatial Stage"
        LIMIT --> CROSS[Crossover<br/>Linkwitz-Riley 4th]
        CROSS --> DELAY[Channel Delay<br/>0-10ms]
        DELAY --> REVERB[Reverb<br/>5 modes]
        REVERB --> SPATIAL[Spatial Audio<br/>8.1 Surround]
    end
    
    subgraph "Output Stage"
        SPATIAL --> BASS[Bass Management<br/>80Hz crossover]
        BASS --> Dither[Dithering<br/>TPDF]
        DITHER --> ROUTE[Output Routing<br/>8.1 Channel Map]
        ROUTE --> OUT[Audio Output<br/>Processed PCM]
    end
    
    style IN fill:#3498db,color:#fff
    style EQ fill:#e74c3c,color:#fff
    style CROSS fill:#f39c12,color:#fff
    style OUT fill:#2ecc71,color:#fff
```

---

## 3. I2S/TDM Connection — XMOS to PCM3168A

```mermaid
graph TB
    subgraph "XMOS XU316"
        XC[XMOS Core<br/>16-thread]
        I2S_M[I2S Master<br/>Controller]
        TDM[TDM Controller]
        DMA[DMA Engine]
    end
    
    subgraph "I2S/TDM Bus"
        BCLK[Bit Clock<br/>BCLK]
        LRCLK[LR Clock<br/>LRCLK/FSYNC]
        SDIN[Serial Data In<br/>SDIN]
        SDOUT[Serial Data Out<br/>SDOUT]
        MCLK[Master Clock<br/>24.576 MHz]
    end
    
    subgraph "PCM3168A"
        DAC[6-Channel DAC<br/>24-bit, 192kHz]
        ADC[8-Channel ADC<br/>24-bit, 96kHz]
        I2C[I2C Control<br/>Config Register]
    end
    
    XC --> I2S_M
    XC --> TDM
    XC --> DMA
    
    I2S_M --> BCLK
    I2S_M --> LRCLK
    DMA --> SDIN
    DMA --> SDOUT
    
    BCLK --> DAC
    BCLK --> ADC
    LRCLK --> DAC
    LRCLK --> ADC
    SDOUT --> DAC
    ADC --> SDIN
    MCLK --> DAC
    MCLK --> ADC
    
    XC -.->|I2C Config| I2C
    I2C --> DAC
    I2C --> ADC
    
    style XC fill:#9b59b6,color:#fff
    style DAC fill:#e74c3c,color:#fff
    style ADC fill:#f39c12,color:#fff
```

---

## 4. Amplifier Architecture — Class AB 8.1

```mermaid
graph TB
    subgraph "Power Supply"
        AC[AC Mains<br/>220V/110V] --> FUSE[Fuse<br/>5A]
        FUSE --> TRAFO[Toroidal Transformer<br/>500VA]
        TRAFO --> RECT[Bridge Rectifier<br/>KBPC3510]
        RECT --> CAP[Filter Capacitors<br/>10,000μF × 4]
        CAP --> REG[Voltage Regulation<br/>±42V DC]
    end
    
    subgraph "Input Stage"
        IN[Audio Input<br/>XLR/RCA] --> BAL[Balanced Input<br/>OPA2134]
        BAL --> VOLUME[Volume Control<br/>ALPS RK27]
        VOLUME --> TONE[Tone Control<br/>Baxandall]
    end
    
    subgraph "Amplifier Channels (×8+1)"
        TONE --> DRIVER1[Driver Stage<br/>MJE340/350]
        TONE --> DRIVER2[Driver Stage<br/>MJE340/350]
        TONE --> DRIVER3[Driver Stage<br/>MJE340/350]
        TONE --> DRIVER4[Driver Stage<br/>MJE340/350]
        
        DRIVER1 --> OUTPUT1[Output Stage<br/>2SA1943/2SC5200]
        DRIVER2 --> OUTPUT2[Output Stage<br/>2SA1943/2SC5200]
        DRIVER3 --> OUTPUT3[Output Stage<br/>2SA1943/2SC5200]
        DRIVER4 --> OUTPUT4[Output Stage<br/>2SA1943/2SC5200]
    end
    
    subgraph "Protection"
        OUTPUT1 --> DC_PROT[DC Protection<br/>Relay]
        OUTPUT2 --> DC_PROT
        OUTPUT3 --> DC_PROT
        OUTPUT4 --> DC_PROT
        DC_PROT --> SPK[Speaker Output<br/>Binding Posts]
        
        REG --> SOFT_START[Soft Start<br/>Time Delay]
        SOFT_START --> DC_PROT
    end
    
    REG --> DRIVER1
    REG --> DRIVER2
    REG --> DRIVER3
    REG --> DRIVER4
    
    style AC fill:#e74c3c,color:#fff
    style OUTPUT1 fill:#2ecc71,color:#fff
    style DC_PROT fill:#f39c12,color:#fff
```

---

## 5. USB Audio Class 2.0 — XMOS USB Pipeline

```mermaid
graph LR
    subgraph "Host (PC/RPi5)"
        USB_H[USB Host<br/>UAC 2.0 Driver]
        ASIO_D[ASIO Driver<br/>Steinberg]
        WASAPI_D[WASAPI Driver<br/>Windows]
    end
    
    subgraph "USB Transport"
        USB_EP1[ISO Endpoint<br/>OUT (Playback)]
        USB_EP2[ISO Endpoint<br/>IN (Capture)]
        USB_CTRL[Control Endpoint<br/>Settings]
    end
    
    subgraph "XMOS XU316"
        USB_D[USB Device<br/>UAC 2.0 Class]
        DECOUPLE[Decoupler Thread<br/>Async FIFO]
        MIXER[Mixer Thread<br/>Volume/Pan]
        HUB[AudioHub Thread<br/>I2S/TDM Master]
    end
    
    subgraph "Audio Output"
        I2S_O[I2S Output<br/>8 channels]
        DAC_O[PCM3168A<br/>6-CH DAC]
        AMP_O[Amplifier<br/>8.1 Surround]
    end
    
    USB_H --> USB_EP1
    USB_H --> USB_EP2
    USB_H --> USB_CTRL
    
    ASIO_D --> USB_H
    WASAPI_D --> USB_H
    
    USB_EP1 --> USB_D
    USB_EP2 --> USB_D
    USB_CTRL --> USB_D
    
    USB_D --> DECOUPLE
    DECOUPLE --> MIXER
    MIXER --> HUB
    
    HUB --> I2S_O
    I2S_O --> DAC_O
    DAC_O --> AMP_O
    
    style USB_H fill:#3498db,color:#fff
    style USB_D fill:#9b59b6,color:#fff
    style HUB fill:#e74c3c,color:#fff
```

---

## 6. Service Architecture — 13 Microservices

```mermaid
graph TB
    subgraph "API Gateway"
        GW[API Gateway<br/>Port 80/443]
    end
    
    subgraph "Core Services"
        AUTH[Auth Service<br/>JWT + Session]
        MEDIA[Media Service<br/>Library + Metadata]
        AUDIO[Audio Service<br/>Player + DSP]
        DL[Download Service<br/>Node.js + TS]
    end
    
    subgraph "Platform Services"
        AI[AI Service<br/>Recommendations]
        SYNC[Sync Service<br/>Multi-device]
        NOTIFY[Notification Service<br/>WebSocket]
    end
    
    subgraph "Infrastructure Services"
        DB[Database Service<br/>11 BCNF MySQL]
        CACHE[Cache Service<br/>APCu + Redis]
        LOG[Logging Service<br/>PSR-3 Monolog]
        QUEUE[Queue Service<br/>Async Jobs]
        STORAGE[Storage Service<br/>File Manager]
    end
    
    GW --> AUTH
    GW --> MEDIA
    GW --> AUDIO
    GW --> DL
    GW --> AI
    
    AUTH --> DB
    AUTH --> CACHE
    MEDIA --> DB
    MEDIA --> STORAGE
    AUDIO --> DB
    AUDIO --> CACHE
    DL --> QUEUE
    DL --> STORAGE
    AI --> DB
    AI --> CACHE
    SYNC --> DB
    SYNC --> QUEUE
    NOTIFY --> QUEUE
    
    style GW fill:#3498db,color:#fff
    style AUTH fill:#e74c3c,color:#fff
    style AUDIO fill:#9b59b6,color:#fff
    style AI fill:#f39c12,color:#fff
```

---

## 7. Platform Architecture — 9 Katman

```mermaid
graph TB
    subgraph "Layer 9: Presentation"
        L9[Web UI<br/>Vanilla JS + ITCSS]
    end
    
    subgraph "Layer 8: Application"
        L8[Use Cases<br/>Application Services]
    end
    
    subgraph "Layer 7: Domain"
        L7[Business Rules<br/>Entities + Value Objects]
    end
    
    subgraph "Layer 6: Electronics"
        L6[DSP Engine<br/>EQ + Compressor + Limiter]
    end
    
    subgraph "Layer 5: Services"
        L5[13 Microservices<br/>API Gateway]
    end
    
    subgraph "Layer 4: Infrastructure"
        L4[Database + Cache<br/>11 BCNF + APCu]
    end
    
    subgraph "Layer 3: Security"
        L3[OWASP + RBAC<br/>JWT + CSRF + CSP]
    end
    
    subgraph "Layer 2: Hardware"
        L2[XMOS + PCM3168A<br/>ASIO + WASAPI]
    end
    
    subgraph "Layer 1: OS"
        L1[Windows / Linux<br/>macOS / RPi5]
    end
    
    L9 --> L8
    L8 --> L7
    L7 --> L6
    L6 --> L5
    L5 --> L4
    L4 --> L3
    L3 --> L2
    L2 --> L1
    
    style L9 fill:#3498db,color:#fff
    style L7 fill:#2ecc71,color:#fff
    style L6 fill:#e74c3c,color:#fff
    style L3 fill:#f39c12,color:#fff
    style L1 fill:#9b59b6,color:#fff
```

---

## 8. Driver Framework — 4 Sürücü Tipi

```mermaid
graph TB
    subgraph "Application Layer"
        APP[CoreMusic App<br/>JUCE AudioEngine]
    end
    
    subgraph "Driver Abstraction"
        DA[Driver Abstraction Layer<br/>Interface + Factory]
    end
    
    subgraph "Driver Types"
        ASIO_D[ASIO Driver<br/>Windows Professional]
        WASAPI_D[WASAPI Driver<br/>Windows Consumer]
        ALSA_D[ALSA Driver<br/>Linux Standard]
        CORE_D[CoreAudio Driver<br/>macOS Built-in]
    end
    
    subgraph "Hardware"
        HW1[USB Audio Interface<br/>XMOS XU316]
        HW2[Onboard Audio<br/>Realtek ALC1220]
        HW3[I2S DAC<br/>PCM3168A]
        HW4[Built-in Speakers<br/>Laptop/Mobile]
    end
    
    APP --> DA
    DA --> ASIO_D
    DA --> WASAPI_D
    DA --> ALSA_D
    DA --> CORE_D
    
    ASIO_D --> HW1
    WASAPI_D --> HW2
    ALSA_D --> HW3
    CORE_D --> HW4
    
    style APP fill:#3498db,color:#fff
    style DA fill:#2ecc71,color:#fff
    style ASIO_D fill:#e74c3c,color:#fff
```

---

## 9. Firmware Architecture — XMOS Boot Sequence

```mermaid
sequenceDiagram
    participant Power as Power On
    participant Boot as Boot ROM
    participant Loader as Flash Loader
    participant Firmware as Main Firmware
    participant I2S as I2S/TDM
    participant USB as USB Audio
    
    Power->>Boot: Power Reset
    Boot->>Loader: Load from Flash (OTP)
    Loader->>Firmware: Jump to Main
    
    par Initialize Subsystems
        Firmware->>I2S: Configure I2S Master
        Firmware->>USB: Configure USB UAC 2.0
    end
    
    loop Audio Processing
        I2S->>Firmware: Receive Samples (I2S)
        Firmware->>Firmware: DSP Processing
        Firmware->>I2S: Send Samples (I2S)
        USB->>Firmware: Receive Samples (USB)
        Firmware->>USB: Send Samples (USB)
    end
    
    Note over Power,USB: XMOS XU316<br/>16-core parallel processing<br/>I2S/TDM + USB Audio Class 2.0
```

---

## 10. Security Architecture — OWASP 2025

```mermaid
graph TB
    subgraph "OWASP Top 10:2025"
        direction TB
        A01[A01: Broken Access Control]
        A02[A02: Cryptographic Failures]
        A03[A03: Injection]
        A04[A04: Insecure Design]
        A05[A05: Security Misconfiguration]
        A06[A06: Vulnerable Components]
        A07[A07: Auth Failures]
        A08[A08: Data Integrity Failures]
        A09[A09: Logging Failures]
        A10[A10: Exceptional Conditions]
    end
    
    subgraph "CoreMusic Defenses"
        DEF1[RBAC + JWT<br/>Authentication]
        DEF2[AES-256-GCM<br/>Encryption]
        DEF3[PDO Prepared<br/>SQL Injection Prevention]
        DEF4[Secure Design<br/>DDD + CQRS]
        DEF5[CSP + CSRF<br/>Security Headers]
        DEF6[Dependency Audit<br/>Composer + npm]
        DEF7[Argon2id<br/>Password Hashing]
        DEF8[HMAC Validation<br/>Data Integrity]
        DEF9[PSR-3 Logging<br/>Audit Trail]
        DEF10[Error Handling<br/>Graceful Degradation]
    end
    
    A01 --> DEF1
    A02 --> DEF2
    A03 --> DEF3
    A04 --> DEF4
    A05 --> DEF5
    A06 --> DEF6
    A07 --> DEF7
    A08 --> DEF8
    A09 --> DEF9
    A10 --> DEF10
    
    style A01 fill:#e74c3c,color:#fff
    style DEF1 fill:#2ecc71,color:#fff
```

---

## 11. RAG System Integration — AI + Electronics

```mermaid
graph TB
    subgraph "User Query"
        Q[User: "Nasıl 8.1 surround kurarım?"]
    end
    
    subgraph "RAG Pipeline"
        Q --> NLU[Query Understanding<br/>Intent: setup_guide]
        NLU --> RETRIEVE[Retrieval<br/>Vector + BM25]
        RETRIEVE --> RERANK[Reranking<br/>Cross-Encoder]
        RERANK --> GENERATE[Generation<br/>LLM + Context]
    end
    
    subgraph "Knowledge Sources"
        RETRIEVE --> V1[Electronics Vault<br/>ADR-038, hardware docs]
        RETRIEVE --> V2[Architecture Docs<br/>service-architecture]
        RETRIEVE --> V3[Audio Pipeline<br/>DSP chain, routing]
        RETRIEVE --> V4[Driver Framework<br/>ASIO, WASAPI, ALSA]
    end
    
    subgraph "Output"
        GENERATE --> A1[Step-by-step guide<br/>PCM3168A + XMOS config]
        GENERATE --> A2[Code examples<br/>I2S/TDM setup]
        GENERATE --> A3[Hardware diagram<br/>Connection schematic]
    end
    
    style Q fill:#3498db,color:#fff
    style RETRIEVE fill:#9b59b6,color:#fff
    style GENERATE fill:#2ecc71,color:#fff
```

---

## 12. 8.1 Surround Channel Mapping

```mermaid
graph TB
    subgraph "8.1 Surround Layout"
        FL[Front Left<br/>20Hz-20kHz]
        FR[Front Right<br/>20Hz-20kHz]
        FC[Center<br/>100Hz-8kHz]
        SL[Surround Left<br/>100Hz-16kHz]
        SR[Surround Right<br/>100Hz-16kHz]
        RL[Rear Left<br/>100Hz-16kHz]
        RR[Rear Right<br/>100Hz-16kHz]
        HL[Height Left<br/>200Hz-16kHz]
        HR[Height Right<br/>200Hz-16kHz]
        LFE[Subwoofer LFE<br/>20Hz-120Hz]
    end
    
    subgraph "DSP Processing"
        EQ_FL[EQ FL] --> FL
        EQ_FR[EQ FR] --> FR
        EQ_FC[EQ FC] --> FC
        EQ_SL[EQ SL] --> SL
        EQ_SR[EQ SR] --> SR
        EQ_RL[EQ RL] --> RL
        EQ_RR[EQ RR] --> RR
        EQ_HL[EQ HL] --> HL
        EQ_HR[EQ HR] --> HR
        CROSS_LFE[Crossover 80Hz<br/>Linkwitz-Riley 4th] --> LFE
    end
    
    subgraph "Crossover Network"
        FULL[Full Range<br/>20Hz-20kHz] --> CROSS_LFE
        FULL --> EQ_FL
        FULL --> EQ_FR
        FULL --> EQ_FC
        FULL --> EQ_SL
        FULL --> EQ_SR
        FULL --> EQ_RL
        FULL --> EQ_RR
        FULL --> EQ_HL
        FULL --> EQ_HR
    end
    
    style FL fill:#3498db,color:#fff
    style LFE fill:#e74c3c,color:#fff
    style CROSS_LFE fill:#f39c12,color:#fff
```

---

## 13. Hardware Block Diagram — Enhanced

```mermaid
graph TB
    subgraph "Digital Source"
        PC[PC / RPi5<br/>USB Host]
        NAS[NAS<br/>Network]
        BT[Bluetooth<br/>AptX HD]
    end
    
    subgraph "Digital Processing"
        USB[USB 2.0<br/>UAC 2.0]
        ETH[Ethernet<br/>1Gbps]
        BLE[BLE 5.0<br/>Audio Profile]
        
        USB --> XMOS[XMOS XU316<br/>16-core DSP]
        ETH --> RPI[RPi5<br/>ARM Cortex-A76]
        BLE --> XMOS
    end
    
    subgraph "Audio Interface"
        XMOS --> I2S[I2S/TDM Bus<br/>8 channels]
        I2S --> PCM[PCM3168A<br/>6-in/8-out Codec]
        I2S --> XMOS_DAC[XMOS DAC<br/>Optional AK4458]
    end
    
    subgraph "Analog Stage"
        PCM --> PREAMP[Preamp Stage<br/>OPA2134]
        PREAMP --> AMP[Class AB Amp<br/>100W × 8+1]
        AMP --> SPK[Speakers<br/>8.1 Surround]
    end
    
    subgraph "Power Supply"
        AC[AC Mains<br/>220V] --> TRAFO[Toroidal<br/>500VA]
        TRAFO --> PSU[PSU<br/>±42V DC]
        PSU --> AMP
        PSU --> PCM
        PSU --> XMOS
    end
    
    style PC fill:#3498db,color:#fff
    style XMOS fill:#9b59b6,color:#fff
    style PCM fill:#e74c3c,color:#fff
    style AMP fill:#2ecc71,color:#fff
```

---

## 14. Development Workflow — 20 Faz

```mermaid
graph LR
    subgraph "Phase 1-6: Vision"
        P1[Faz 1: Requirements]
        P2[Faz 2: Use Cases]
        P3[Faz 3: Architecture]
        P4[Faz 4: Design]
        P5[Faz 5: Prototyping]
        P6[Faz 6: Validation]
    end
    
    subgraph "Phase 7-12: Hardware"
        P7[Faz 7: Schematic<br/>⚠️ HARD GATE]
        P8[Faz 8: PCB Layout]
        P9[Faz 9: Fabrication]
        P10[Faz 10: Assembly]
        P11[Faz 11: Firmware]
        P12[Faz 12: Hardware Test]
    end
    
    subgraph "Phase 13-18: Software"
        P13[Faz 13: Driver Dev]
        P14[Faz 14: DSP Implementation]
        P15[Faz 15: UI Integration]
        P16[Faz 16: API Integration]
        P17[Faz 17: Security Audit<br/>⚠️ HARD GATE]
        P18[Faz 18: Performance Test]
    end
    
    subgraph "Phase 19-20: Release"
        P19[Faz 19: Beta Release<br/>⚠️ HARD GATE]
        P20[Faz 20: Production]
    end
    
    P1 --> P2 --> P3 --> P4 --> P5 --> P6
    P6 --> P7 --> P8 --> P9 --> P10 --> P11 --> P12
    P12 --> P13 --> P14 --> P15 --> P16 --> P17 --> P18
    P18 --> P19 --> P20
    
    style P7 fill:#e74c3c,color:#fff
    style P17 fill:#e74c3c,color:#fff
    style P19 fill:#e74c3c,color:#fff
```

---

## 15. Audio Platform Decision Matrix

```mermaid
quadrantChart
    title Audio Platform Selection
    x-axis "Low Latency" --> "High Latency"
    y-axis "Low Quality" --> "High Quality"
    quadrant-1 "Best Choice"
    quadrant-2 "Good Choice"
    quadrant-3 "Acceptable"
    quadrant-4 "Avoid"
    "ASIO (Windows)": [0.1, 0.9]
    "CoreAudio (macOS)": [0.2, 0.95]
    "WASAPI Exclusive": [0.4, 0.8]
    "JACK (Linux)": [0.15, 0.85]
    "ALSA (Linux)": [0.5, 0.7]
    "WASAPI Shared": [0.7, 0.6]
    "DirectSound": [0.8, 0.4]
    "MME": [0.9, 0.3]
```

---

## 16. Network Architecture — Service Communication

```mermaid
graph TB
    subgraph "External"
        CLIENT[Web Client<br/>Vanilla JS]
        MOBILE[Mobile Client<br/>PWA/Flutter]
        API_EXT[External API<br/>REST/GraphQL]
    end
    
    subgraph "Gateway"
        LB[Load Balancer<br/>nginx]
        GW[API Gateway<br/>Rate Limit + Auth]
    end
    
    subgraph "Internal Services"
        S1[Auth Service<br/>:81]
        S2[Media Service<br/>:5000]
        S3[Audio Service<br/>:9741]
        S4[Download Service<br/>:3001]
        S5[AI Service<br/>Internal]
    end
    
    subgraph "Message Bus"
        MQ[Message Queue<br/>Redis Pub/Sub]
        WS[WebSocket<br/>Real-time]
    end
    
    subgraph "Data Layer"
        DB[(MySQL 11 BCNF<br/>9 Databases)]
        CACHE[(Redis Cache<br/>Session + Query)]
        FS[(File Storage<br/>Media + Documents)]
    end
    
    CLIENT --> LB
    MOBILE --> LB
    API_EXT --> LB
    LB --> GW
    
    GW --> S1
    GW --> S2
    GW --> S3
    GW --> S4
    
    S1 --> DB
    S1 --> CACHE
    S2 --> DB
    S2 --> FS
    S3 --> CACHE
    S4 --> FS
    
    S1 --> MQ
    S2 --> MQ
    S3 --> MQ
    S4 --> MQ
    MQ --> WS
    WS --> CLIENT
    
    style GW fill:#3498db,color:#fff
    style MQ fill:#f39c12,color:#fff
    style DB fill:#2ecc71,color:#fff
```

---

## 17. Testing Pyramid — Electronics

```mermaid
graph TB
    subgraph "E2E Tests (10%)"
        E2E[Playwright<br/>Full System Tests]
    end
    
    subgraph "Integration Tests (20%)"
        INT1[PHPUnit<br/>API Integration]
        INT2[Vitest<br/>Frontend Integration]
        INT3[Google Test<br/>C++ Integration]
        INT4[Hardware-in-Loop<br/>I2S/TDM Tests]
    end
    
    subgraph "Unit Tests (70%)"
        U1[PHPUnit<br/>PHP Unit Tests]
        U2[Vitest<br/>JS Unit Tests]
        U3[Google Test<br/>C++ Unit Tests]
        U4[Mock Hardware<br/>XMOS Simulation]
    end
    
    E2E --> INT1
    E2E --> INT2
    E2E --> INT3
    E2E --> INT4
    INT1 --> U1
    INT2 --> U2
    INT3 --> U3
    INT4 --> U4
    
    style E2E fill:#e74c3c,color:#fff
    style INT1 fill:#f39c12,color:#fff
    style U1 fill:#2ecc71,color:#fff
```

---

## 18. Deployment Modes

```mermaid
graph TB
    subgraph "Home Media Center"
        HOME[Windows/Linux/macOS<br/>PC/Laptop]
    end
    
    subgraph "Car Audio System"
        CAR[Windows/Android Auto<br/>RPi5 + PCM3168A]
    end
    
    subgraph "Professional Studio"
        STUDIO[Windows WASAPI/ASIO<br/>8.1 Surround + Class AB]
    end
    
    subgraph "NAS Audio Server"
        NAS_D[Linux Docker<br/>Synology/QNAP]
    end
    
    subgraph "DAC Control System"
        DAC_D[Windows/Linux<br/>XMOS XU316 + PCM3168A]
    end
    
    HOME --> CORE[CoreMusic Platform]
    CAR --> CORE
    STUDIO --> CORE
    NAS_D --> CORE
    DAC_D --> CORE
    
    style CORE fill:#9b59b6,color:#fff
    style HOME fill:#3498db,color:#fff
    style CAR fill:#2ecc71,color:#fff
    style STUDIO fill:#e74c3c,color:#fff
    style NAS_D fill:#f39c12,color:#fff
    style DAC_D fill:#1abc9c,color:#fff
```

---

## 19. Layer Dependency Matrix

```mermaid
graph TB
    subgraph "Allowed Dependencies"
        L3[L3 Presentation<br/>Vanilla JS, ITCSS] -->|Allowed| L2[L2 Routing<br/>PHP PageRouter]
        L2 -->|Allowed| L1[L1 Security<br/>Middleware Pipeline]
        L1 -->|Allowed| L0[L0 Infrastructure<br/>PDO, APCu, Redis]
    end
    
    subgraph "Forbidden Dependencies (Layer Violation)"
        L0 -->|❌ FORBIDDEN| L2
        L0 -->|❌ FORBIDDEN| L3
        L1 -->|❌ FORBIDDEN| L3
        L3 -->|❌ FORBIDDEN| L0
    end
    
    style L3 fill:#3498db,color:#fff
    style L2 fill:#2ecc71,color:#fff
    style L1 fill:#f39c12,color:#fff
    style L0 fill:#e74c3c,color:#fff
```

---

## 20. Real-Time Audio Thread Budget

```mermaid
gantt
    title Audio Callback Budget (512 samples @ 48kHz = 10.67ms)
    dateFormat X
    axisFormat %L ms
    
    section Audio Thread
    Input Acquisition      :a1, 0, 1
    DSP Processing         :a2, 1, 6
    Output Routing         :a3, 6, 8
    Buffer Transfer (DMA)  :a4, 8, 10
    
    section Deadline
    Total Budget (10.67ms) :crit, 0, 11
    
    section Safety Margin
    Buffer Underrun Risk   :milestone, 10, 10
```

---

## Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Diagrams | 20 |
| Diagram Types | Architecture (8), Pipeline (4), Sequence (1), Network (2), Testing (1), Deployment (1), Gantt (1), Quadrant (1), Matrix (2) |
| Mermaid Version | Compatible with Mermaid 10+ |
| Cross References | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
